<?php defined('BASEPATH') or exit('No direct script access allowed');

class Production extends MY_Controller{
    public function __construct(){
        parent::__construct();
        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }
        if ($this->Supplier) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER["HTTP_REFERER"]);
        }
        $this->lang->admin_load('procurment/request', $this->Settings->user_language);
        $this->load->library('form_validation');
        $this->load->admin_model('procurment/production_model');
        $this->digital_upload_path = 'assets/uploads/procurment/production/';
        $this->digital_file_types = 'zip|psd|ai|rar|pdf|doc|docx|xls|xlsx|ppt|pptx|gif|jpg|jpeg|png|tif|txt';
        $this->allowed_file_size = '1024';
	    if (!file_exists($this->digital_upload_path)) {
		mkdir($this->digital_upload_path, 0777, true);
	    }
        $this->data['logo'] = true;
	}

    public function index($warehouse_id = null){
		$this->sma->checkPermissions();
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        if ($this->Owner || $this->Admin || !$this->session->userdata('warehouse_id')) {
            $this->data['warehouses'] = $this->siteprocurment->getAllWarehouses();
            $this->data['warehouse_id'] = $warehouse_id;
            $this->data['warehouse'] = $warehouse_id ? $this->siteprocurment->getWarehouseByID($warehouse_id) : null;
        } else {
            $this->data['warehouses'] = null;
            $this->data['warehouse_id'] = $this->session->userdata('warehouse_id');
            $this->data['warehouse'] = $this->session->userdata('warehouse_id') ? $this->siteprocurment->getWarehouseByID($this->session->userdata('warehouse_id')) : null;
        }
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('production')));
        $meta = array('page_title' => lang('production'), 'bc' => $bc);
        $this->page_construct('procurment/production/index', $meta, $this->data);
    }
    public function getProduction($warehouse_id = null){
        $detail_link = anchor('admin/procurment/production/view/$1/', '<i class="fa fa-file-text-o"></i> ' . lang('quotation_request_details'));
        $email_link = anchor('admin/procurment/production/email/$1', '<i class="fa fa-envelope"></i> ' . lang('email_request'), 'data-toggle="modal" data-target="#myModal"');
		$view_link = '<a href="'.admin_url('procurment/production/view/$1').'" data-toggle="modal" data-target="#myModal"><i class="fa fa-edit"></i>'.lang('view_production_list').'</a>';
		$edit_link = anchor('admin/procurment/production/edit/$1', '<i class="fa fa-edit"></i> ' . lang('edit_production'));
		
       // $convert_link = anchor('admin/procurment/sales/add/$1', '<i class="fa fa-heart"></i> ' . lang('create_sale'));
       // $pc_link = anchor('admin/procurment/purchases/add/$1', '<i class="fa fa-star"></i> ' . lang('create_purchase'));
        $pdf_link = anchor('admin/procurment/production/pdf/$1', '<i class="fa fa-file-pdf-o"></i> ' . lang('download_pdf'));
		$delete_link = "<a href='#' class='po' title='<b>" . $this->lang->line("delete_request") . "</b>' data-content=\"<p>"
        . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('procurment/production/delete/$1') . "'>"
        . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i> "
        . lang('delete_production') . "</a>";
       /* $action = '<div class="text-center"><div class="btn-group text-left">'
        . '<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">'
        . lang('actions') . ' <span class="caret"></span></button>
                    <ul class="dropdown-menu pull-right" role="menu">
                        <li>' . $detail_link . '</li>
                        <li>' . $edit_link . '</li>
                        <li>' . $convert_link . '</li>
                        <li>' . $pc_link . '</li>
                        <li>' . $pdf_link . '</li>
                        <li>' . $email_link . '</li>
                        <li>' . $delete_link . '</li>
                    </ul>
                </div></div>';*/
		 $action = '<div class="text-center"><div class="btn-group text-left">'
        . '<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">'
        . lang('actions') . ' <span class="caret"></span></button>
                    <ul class="dropdown-menu pull-right" role="menu">
                        <li>' . $edit_link . '</li>
			<li>' . $view_link . '</li>   
            <li>' . $delete_link . '</li>                      
                    </ul>
                </div></div>';
        //$action = '<div class="text-center">' . $detail_link . ' ' . $edit_link . ' ' . $email_link . ' ' . $delete_link . '</div>';
        $this->load->library('datatables');
        if ($warehouse_id) {
            $this->datatables
                ->select("pro_production.id as id, pro_production.date, pro_production.reference_no, pro_production.status, pro_production.attachment as attachment")
                ->from('pro_production')
                ->where('pro_production.warehouse_id', $warehouse_id);
        } else {
            $this->datatables
                ->select("pro_production.id as id, pro_production.date, pro_production.reference_no, pro_production.status, pro_production.attachment as attachment")
                ->from('pro_production');
        }
       
	    $this->datatables->edit_column('attachment', '$1__$2', $this->digital_upload_path.', attachment');
        $this->datatables->add_column("Actions", $action, "id,status");
        echo $this->datatables->generate();
    }

    public function add() {
        $this->sma->checkPermissions();
		// echo "<pre>";
		//print_r($this->input->post());die;		
        //$this->form_validation->set_message('is_natural_no_zero', $this->lang->line("no_zero_required"));
        $this->form_validation->set_rules('reference_no', $this->lang->line("reference_no"), 'required');
        // $this->form_validation->set_rules('supplier', $this->lang->line("supplier"), 'required');		
        if ($this->form_validation->run() == true) {     
			$n = $this->siteprocurment->lastidProduction();
			$reference = 'PR'.str_pad($n + 1, 5, 0, STR_PAD_LEFT);            
            $date = date('Y-m-d H:i:s');
            $warehouse_id = $this->input->post('warehouse');           
            $status = $this->input->post('status');        
            $note = $this->sma->clear_tags($this->input->post('note'));
            $data = array(
				'date' => $date,
                'reference_no' => $reference,
				'warehouse_id' => $warehouse_id,
                'store_id'=>$this->session->userdata('warehouse_id'),
                'note' => $note,
                'status' => $status,
                'created_by' => $this->session->userdata('user_id'),
                'processed_by' => $this->session->userdata('user_id'),
				'processed_on' => date('Y-m-d H:i:s'),
            );
    	    if($status=="approved"){
    			$data['approved_by'] = $this->session->userdata('user_id');
                $data['approved_on'] = date('Y-m-d H:i:s');
    	    }
            if ($_FILES['document']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path'] = $this->digital_upload_path;
                $config['allowed_types'] = $this->digital_file_types;
                $config['max_size'] = $this->allowed_file_size;
                $config['overwrite'] = false;
                $config['encrypt_name'] = true;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('document')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER["HTTP_REFERER"]);
                }
                $photo = $this->upload->file_name;
                $data['attachment'] = $photo;
            }
            
            $i = isset($_POST['product_code']) ? sizeof($_POST['product_code']) : 0;
            for ($r = 0; $r < $i; $r++) {
				$store_id = $_POST['store_id'][$r];
                $item_id = $_POST['product_id'][$r];
                $variant_id = $_POST['variant_id'][$r] ? $_POST['variant_id'][$r] : 0;
                $item_type = $_POST['product_type'][$r];
                $item_code = $_POST['product_code'][$r];
                $item_name = $_POST['product_name'][$r];		                
                $item_quantity = $_POST['quantity'][$r];         
				$category_id = $_POST['category_id'][$r];
                $category_name = $_POST['category_name'][$r];
				$subcategory_id = $_POST['subcategory_id'][$r];
                $subcategory_name = $_POST['subcategory_name'][$r];
                $brand_id = $_POST['brand_id'][$r];
				$cm_id = $_POST['cm_id'][$r];
                $brand_name = $_POST['brand_name'][$r];
				$uom = $_POST['uom'][$r];
				$base_quantity=$_POST['product_base_quantity'][$r];
               if (!empty($item_code)) {                    
                    $products[] = array(
                        'store_id' => $store_id,
                        'product_id' => $item_id,
						'variant_id' => $variant_id,
                        'product_code' => $item_code,
                        'product_name' => $item_name,						
                        'product_type' => $item_type,
                        'quantity' => $item_quantity,
						'category_id' => $category_id,
                        'category_name' => $category_name,
						'subcategory_id' => $subcategory_id,
                        'subcategory_name' => $subcategory_name,
                        'brand_id' => $brand_id,
						'cm_id' => $cm_id,
                        'brand_name' => $brand_name,
						'uom'=>$uom,
						'base_quantity'=>$base_quantity
                    );
                }
            }           
            if (empty($products)) {
                $this->form_validation->set_rules('product', lang("order_items"), 'required');
            } else {
                $products;
            }					
			
        }
		//echo '<pre>';print_r($products);exit;	
        if ($this->form_validation->run() == true && $this->production_model->addProduction($data, $products)) {
            $this->session->set_userdata('remove_quls', 1);
            $this->session->set_flashdata('message', $this->lang->line("production_added"));
            admin_redirect('procurment/production');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));                     
            $this->data['warehouses'] = ($this->Owner || $this->Admin || !$this->session->userdata('warehouse_id')) ? $this->siteprocurment->getAllWarehouses() : null;
            $bc = array(array('link' => base_url(), 'page' => lang('home')),array('link' => admin_url('procurment/production'), 'page' => lang('production')), array('link' => admin_url('procurment/production'), 'page' => lang('add production')));
            $meta = array('page_title' => lang('production'), 'bc' => $bc);
            $this->page_construct('procurment/production/add', $meta, $this->data);
        }
    }
     public function suggestions(){
        $term = $this->input->get('term', true);
        $warehouse_id = $this->input->get('warehouse_id', true);
        $supplier_id = $this->input->get('supplier_id', true);
        if (strlen($term) < 1 || !$term) {
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . admin_url('procurment/welcome') . "'; }, 10);</script>");
        }
        $analyzed = $this->sma->analyze_term($term);
        $sr = $analyzed['term'];
        $option_id = $analyzed['option_id'];
        $warehouse = $this->siteprocurment->getWarehouseByID($warehouse_id);
       // $customer = $this->siteprocurment->getCompanyByID($customer_id);
       // $customer_group = $this->siteprocurment->getCustomerGroupByID($customer->customer_group_id);
        $rows = $this->production_model->getProductNamesNew($sr);	
        if ($rows) {
            $c = str_replace(".", "", microtime(true));
            $r = 0;
            foreach ($rows as $row) {
                unset($row->details, $row->product_details, $row->image, $row->barcode_symbology, $row->cf1, $row->cf2, $row->cf3, $row->cf4, $row->cf5, $row->cf6, $row->supplier1price, $row->supplier2price, $row->cfsupplier3price, $row->supplier4price, $row->supplier5price, $row->supplier1, $row->supplier2, $row->supplier3, $row->supplier4, $row->supplier5, $row->supplier1_part_no, $row->supplier2_part_no, $row->supplier3_part_no, $row->supplier4_part_no, $row->supplier5_part_no);
                $option = false;
                $row->quantity = 0;
                $row->item_tax_method = $row->tax_method;
                $row->qty = 1;
                $row->discount = '0';
                $options = $this->production_model->getProductOptions($row->id, $warehouse_id);		
                if ($options) {
                    $opt = $option_id && $r == 0 ? $this->production_model->getProductOptionByID($option_id) : $options[0];
                    if (!$option_id || $r > 0) {
                        $option_id = $opt->id;
                    }
                } else {
                    $opt = json_decode('{}');
                    $opt->price = 0;
                    $option_id = FALSE;
                }
                $row->option = $option_id;
                $pis = $this->siteprocurment->getPurchasedItems($row->id, $warehouse_id, $row->option);
				/* echo '<pre>';
				print_r($pis);
				die; */
                if ($pis) {
                    foreach ($pis as $pi) {
                        $row->quantity += $pi->quantity_balance;
                    }
                }
                if ($options) {
                    $option_quantity = 0;
                    foreach ($options as $option) {
                        $pis = $this->siteprocurment->getPurchasedItems($row->id, $warehouse_id, $row->option);
                        if ($pis) {
                            foreach ($pis as $pi) {
                                $option_quantity += $pi->quantity_balance;
                            }
                        }
                        if ($option->quantity > $option_quantity) {
                            $option->quantity = $option_quantity;
                        }
                    }
                }
                if ($row->promotion) {
                    $row->price = $row->promo_price;
                } elseif ($customer->price_group_id) {
                    if ($pr_group_price = $this->siteprocurment->getProductGroupPrice($row->id, $customer->price_group_id)) {
                        $row->price = $pr_group_price->price;
                    }
                } elseif ($warehouse->price_group_id) {
                    if ($pr_group_price = $this->siteprocurment->getProductGroupPrice($row->id, $warehouse->price_group_id)) {
                        $row->price = $pr_group_price->price;
                    }
                }
                $row->price = $row->price + (($row->price * $customer_group->percent) / 100);
                $row->real_unit_price = $row->price;
                $row->base_quantity = 1;
                $row->base_unit = $row->unit;
                $row->base_unit_price = $row->price;
                $row->unit = $row->sale_unit ? $row->sale_unit : $row->unit;
				$row->product_uom='';
                $combo_items = false;
                if ($row->type == 'combo') {
                    $combo_items = $this->production_model->getProductComboItems($row->id, $warehouse_id);
                }
                $units = $this->siteprocurment->getUnitsByBUID($row->base_unit);
                $tax_rate = $this->siteprocurment->getTaxRateByID($row->tax_rate);
                $variant = '';
                $variant_id = '';
                if($row->variant_name !=''){
                    $variant = ' ['.$row->variant_name.']';  
                    $variant_id =$row->variant_id;
                }
				$label = $row->name.$variant . " (" . $row->code . ") CAT - ".$row->category_name." | SUBCAT - ".$row->subcategory_name." | BRAND - ".$row->brand_name;
                $pr[] = array('id' => ($c + $r), 'item_id' => $row->id.$variant_id, 'label' => $label, 'category' => $row->category_id,
                    'row' => $row, 'combo_items' => $combo_items, 'tax_rate' => $tax_rate, 'units' => $units, 'options' => $options);
                $r++;
            }
            $this->sma->send_json($pr);
        } else {
            $this->sma->send_json(array(array('id' => 0, 'label' => lang('no_match_found'), 'value' => $term)));
        }
    }
    public function edit($id) {
      $this->sma->checkPermissions();
	  $production = $this->production_model->getProductionByID($id);
	  if ($production->status == 'approved' || $production->status == 'completed') {
		$this->session->set_flashdata('error', lang("Do not allowed edit option"));
		admin_redirect("procurment/production");
	   }	 
        //$this->form_validation->set_message('is_natural_no_zero', $this->lang->line("no_zero_required"));
        $this->form_validation->set_rules('reference_no', $this->lang->line("reference_no"), 'required');
        // $this->form_validation->set_rules('supplier', $this->lang->line("supplier"), 'required');
        if ($this->form_validation->run() == true) {
                    //echo "<pre>";
//print_r($this->input->post());die;
	    $n = $this->siteprocurment->lastidProduction();
	    
            $date = date('Y-m-d H:i:s');
            $warehouse_id = $this->input->post('warehouse');           
            $status = $this->input->post('status');        
            $note = $this->sma->clear_tags($this->input->post('note'));
            $data = array(
				'date' => $date,
               // 'reference_no' => $reference,
				'warehouse_id' => $warehouse_id,
                'store_id'=>$this->session->userdata('warehouse_id'),
                'note' => $note,
                'status' => $status,
                'created_by' => $this->session->userdata('user_id'),
                'processed_by' => $this->session->userdata('user_id'),
	        	'processed_on' => date('Y-m-d H:i:s'),
            );
	         if($status=="approved"){
		        $data['approved_by'] = $this->session->userdata('user_id');
                $data['approved_on'] = date('Y-m-d H:i:s');
	         }
            if ($_FILES['document']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path'] = $this->digital_upload_path;
                $config['allowed_types'] = $this->digital_file_types;
                $config['max_size'] = $this->allowed_file_size;
                $config['overwrite'] = false;
                $config['encrypt_name'] = true;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('document')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER["HTTP_REFERER"]);
                }
                $photo = $this->upload->file_name;
                $data['attachment'] = $photo;
            }
            $i = isset($_POST['product_code']) ? sizeof($_POST['product_code']) : 0;
            for ($r = 0; $r < $i; $r++) {
                $store_id = $_POST['store_id'][$r];
                $item_id = $_POST['product_id'][$r];
                $item_type = $_POST['product_type'][$r];
                $item_code = $_POST['product_code'][$r];
                $item_name = $_POST['product_name'][$r];		                
                $item_quantity = $_POST['quantity'][$r];         
                $category_id = $_POST['category_id'][$r];
                $category_name = $_POST['category_name'][$r];
                $subcategory_id = $_POST['subcategory_id'][$r];
                $subcategory_name = $_POST['subcategory_name'][$r];
                $brand_id = $_POST['brand_id'][$r];
                $cm_id = $_POST['cm_id'][$r];
                $brand_name = $_POST['brand_name'][$r];
                $variant_id = $_POST['variant_id'][$r] ? $_POST['variant_id'][$r] : 0;
				$uom = $_POST['uom'][$r];
				$base_quantity=$_POST['product_base_quantity'][$r];
               if (!empty($item_code)) {
                    
                    $products[$r] = array(
                    'store_id' => $store_id,
                    'product_id' => $item_id,
                    'product_code' => $item_code,
                    'product_name' => $item_name,						
                    'product_type' => $item_type,
                    'quantity' => $item_quantity,
                    'category_id' => $category_id,
                    'category_name' => $category_name,
                    'subcategory_id' => $subcategory_id,
                    'subcategory_name' => $subcategory_name,
                    'brand_id' => $brand_id,
                    'brand_name' => $brand_name,
                    'cm_id' => $cm_id,
                    'variant_id' => $variant_id,
				     'uom'=>$uom,
					 'base_quantity'=>$base_quantity
                    );

                }
            }
           
            if (empty($products)) {
                $this->form_validation->set_rules('product', lang("order_items"), 'required');
            } else {
                $products;
            }
        }
        //echo "<pre>";
        //print_r($products);
        //print_r($data);die;
	
	//echo '<pre>';print_r($products);exit;
	
        if ($this->form_validation->run() == true && $this->production_model->updateProduction($id,$data, $products)) {
            $this->session->set_userdata('remove_quls', 1);
            $this->session->set_flashdata('message', $this->lang->line("production_updated"));
            admin_redirect('procurment/production');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            //// *************** production items ****************///
	        $this->data['production'] = $production;
            $inv_items = $this->production_model->getAllProductionItemsWithDetails($id);
            $c = rand(100000, 9999999);
            foreach ($inv_items as $item) {
                $row = $this->siteprocurment->getItemByID($item->product_id);
		        //echo '<pre>';print_R($row);exit;
                $row = $this->siteprocurment->getItemByID($item->product_id);
                $row->name = $item->product_name;
                $row->id = $item->product_id;
                $row->code = $item->product_code;
                $row->qty = $item->quantity;
                $row->quantity_balance = $item->quantity;
				$row->product_uom = $item->uom;
				$row->base_quantity = $item->base_quantity;
                //$row->real_unit_cost = $item->gross;
                $row->category_id = $item->category_id;
                $row->category_name = $item->category_name;
                $row->subcategory_id = $item->subcategory_id;
                $row->subcategory_name = $item->subcategory_name;
                $row->brand_id = $item->brand_id;
                $row->brand_name = $item->brand_name;
                $row->variant_name = $item->variant_name ? $item->variant_name:"";
                $row->variant_id = $item->variant_id ? $item->variant_id :0;
                $row->cm_id = $item->cm_id ? $item->cm_id :0;
                $options = $this->production_model->getProductOptions($row->id);
                $units = $this->siteprocurment->getUnitsByBUID($row->purchase_unit);
                $ri = $this->Settings->item_addition ? $row->id : $row->id;
                $pr[$ri.'_'.$item->store_id] = array('id' => $c,'store_id'=>$item->store_id,'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")",
                    'row' => $row,  'units' => $units, 'options' => $options);
                $c++;
            }
			
			
            $this->data['inv_items'] = json_encode($pr);
	    
	    //// *********************-------***********************//
            $this->data['warehouses'] = ($this->Owner || $this->Admin || !$this->session->userdata('warehouse_id')) ? $this->siteprocurment->getAllWarehouses() : null;
            $bc = array(array('link' => base_url(), 'page' => lang('home')),array('link' => admin_url('procurment/production'), 'page' => lang('production')), array('link' => admin_url('procurment/production'), 'page' => lang('edit production')));
            $meta = array('page_title' => lang('production'), 'bc' => $bc);
            $this->page_construct('procurment/production/edit', $meta, $this->data);
        }
    }
    public function view($id) {
            $this->sma->checkPermissions();
	        $production = $this->production_model->getProductionByID($id);
        //// *************** production items ****************///
	    
	        $this->data['production'] = $production;
            $production_items = $this->production_model->getAllProductionItemsWithDetails($id);
            //krsort($production_items);
            $c = rand(100000, 9999999);
            foreach ($production_items as $item) {
            $row = $this->siteprocurment->getItemByID($item->product_id);
			$store = $this->siteprocurment->getWarehouseByID($item->store_id);
			$item->store_name = $store->name;
            $options = $this->production_model->getProductOptions($row->id);
            $units = $this->siteprocurment->getUnitsByBUID($row->purchase_unit);
            $ri = $this->Settings->item_addition ? $row->id : $row->id;
            $pr[$ri.'_'.$item->store_id] = array('id' => $c,'store_id'=>$item->store_id,'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")",
                    'row' => $item,  'units' => $units, 'options' => $options);
                $c++;
            }
            $this->data['pro_items'] = $pr;
	    //// *********************-------***********************//
            $this->data['warehouses'] = ($this->Owner || $this->Admin || !$this->session->userdata('warehouse_id')) ? $this->siteprocurment->getAllWarehouses() : null;
            $bc = array(array('link' => base_url(), 'page' => lang('home')),array('link' => admin_url('procurment/production'), 'page' => lang('production')), array('link' => admin_url('procurment/production'), 'page' => lang('view production')));
            $meta = array('page_title' => lang('production'), 'bc' => $bc);
	        $this->load->view($this->theme . 'procurment/production/view', $this->data);
    }

   
    function delete($id = NULL){        
        $this->sma->checkPermissions(NULL, TRUE);
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $delete_check = $this->production_model->checkproductionsapproved($id);
        if($delete_check == FALSE){
        if ($this->production_model->deleteproductions($id)) {
            if($this->input->is_ajax_request()) {
                $this->sma->send_json(array('error' => 0, 'msg' => lang("production_item_deleted")));
            }
            $this->session->set_flashdata('message', lang('production_item_deleted'));
            admin_redirect('welcome');
        }
        }else{
            $this->sma->send_json(array('error' => 1, 'msg' => lang("can_not_delete_the_approved_production_items")));  
        }
		
 
    }  
