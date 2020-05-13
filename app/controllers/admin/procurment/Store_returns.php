<?php defined('BASEPATH') or exit('No direct script access allowed');

class Store_returns extends MY_Controller{
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
        $this->lang->admin_load('procurment/store_returns', $this->Settings->user_language);
        $this->load->library('form_validation');
        $this->load->admin_model('procurment/store_returns_model');
        $this->digital_upload_path = 'files/';
        $this->digital_file_types = 'zip|psd|ai|rar|pdf|doc|docx|xls|xlsx|ppt|pptx|gif|jpg|jpeg|png|tif|txt';
        $this->allowed_file_size = '1024';
        $this->data['logo'] = true;
		
		$this->Muser_id = $this->session->userdata('user_id');
		$this->Maccess_id = 8;

    }

    public function index($warehouse_id = null){
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

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('store_returns')));
        $meta = array('page_title' => lang('store_returns'), 'bc' => $bc);
        $this->page_construct('procurment/store_returns/index', $meta, $this->data);

    }

    public function getStore_returns($warehouse_id = null){
		$view_link = '<a href="'.admin_url('procurment/store_returns/view/$1').'" data-toggle="modal" data-target="#myModal"><i class="fa fa-edit"></i>'.lang('view_store_return').'</a>';
        $edit_link = anchor('admin/procurment/store_returns/edit/$1', '<i class="fa fa-edit"></i> ' . lang('edit_store_return'));
        $delete_link = "<a href='#' class='po' title='<b>" . $this->lang->line("delete_quotation") . "</b>' data-content=\"<p>"
        . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('procurment/store_returns/delete/$1') . "'>"
        . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i> "
        . lang('delete_store_returns') . "</a>";
       
		$action = '<div class="text-center"><div class="btn-group text-left">'
        . '<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">'
        . lang('actions') . ' <span class="caret"></span></button>
        <ul class="dropdown-menu pull-right" role="menu">
            <li>' . $edit_link . '</li>
			 <li>' . $view_link . '</li>
            <li>' . $delete_link . '</li>
        </ul>
		</div></div>';
        $this->load->library('datatables');
         $this->datatables
	    ->select("pro_store_returns.id, pro_store_returns.date, pro_store_returns.reference_no, pro_store_returns.req_reference_no as ref, f.name as from_name, t.name as to_name, pro_store_returns.total_no_qty as return_qty, pro_store_returns.status")
            ->from('pro_store_returns')
	    
	    ->join('warehouses f', 'f.id = pro_store_returns.from_store', 'left')
	    ->join('warehouses t', 't.id = pro_store_returns.to_store', 'left')
		->where('pro_store_returns.store_id',$this->store_id);		
		$this->datatables->group_by('pro_store_returns.id');
        $this->datatables->add_column("Actions", $action, "pro_store_returns.id");
		echo      $this->datatables->generate();
    }

    
 
    public function view($store_return_id = null) {
        if ($this->input->get('id')) {
         $store_return_id = $this->input->get('id');
        }
         $this->data['store_rec']=$po= $this->store_returns_model->getStore_return_ByID($store_return_id);
		 $inv_items = $this->store_returns_model->getStore_return_Items($store_return_id);
         krsort($inv_items);
            $c = rand(100000, 9999999);
			
			 foreach ($inv_items as $item) {
                $row = $this->siteprocurment->getRecipeByID($item->product_id);
                if (!$row) {
                    $row = json_decode('{}');
                    $row->tax_method = 0;
                } else {
                    unset($row->details, $row->cost, $row->supplier1price, $row->supplier2price, $row->supplier3price, $row->supplier4price, $row->supplier5price);
                }
                $row->id = $item->product_id;
                $row->code = $item->product_code;
                $row->name = $item->product_name;
                $row->type = $item->product_type;
                $ri = $this->Settings->item_addition ? $row->id : $c;
                $pr[$ri] = array('id' => $c, 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'row' => $item);
                $c++;
            }
			 $this->data['store_rec_items'] = $pr;
             $this->data['id'] = $id;
	         $this->data['fromstore'] = $this->siteprocurment->getWarehouseByID($po->from_store);
		     $this->data['to_store'] = $this->siteprocurment->getWarehouseByID($po->to_store);
             $this->data['inv'] = $po;
			 $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('procurment/store_returns'), 'page' => lang('store_returns')), array('link' => '#', 'page' => lang('view')));
			 $meta = array('page_title' => lang('view_store_return_details'), 'bc' => $bc);
			 $this->load->view($this->theme.'procurment/store_returns/view', $this->data);

    }
    
    public function add(){
          $this->form_validation->set_message('is_natural_no_zero', $this->lang->line("no_zero_required"));
		  $this->form_validation->set_rules('from_store_id', $this->lang->line("from_store"), 'required');
		  $this->form_validation->set_rules('to_store_id', $this->lang->line("to_store"), 'required');
		  $this->form_validation->set_rules('request_type', $this->lang->line("request_type"), 'required');
        if ($this->form_validation->run() == true) {
            $reference = 'STORERETURN'.date('YmdHis');            
			$date = date('Y-m-d H:i:s');
            $warehouse_id = $this->input->post('warehouse');
			$from_store_id = $this->input->post('from_store_id');
			$to_store_id = $this->input->post('to_store_id');
            $supplier_id = $this->input->post('supplier');
			$biller_id = $this->input->post('biller');
            $status = $this->input->post('status');
            
			
            $biller_details = $this->siteprocurment->getCompanyByID($biller_id);
            $biller = $biller_details->company != '-' ? $biller_details->company : $biller_details->name;
            if ($supplier_id) {
                $supplier_details = $this->siteprocurment->getCompanyByID($supplier_id);
                $supplier = $supplier_details->company != '-' ? $supplier_details->company : $supplier_details->name;
            } else {
                $supplier = NULL;
            }
            $note = $this->sma->clear_tags($this->input->post('note'));

            $total = 0;
            $product_tax = 0;
            $product_discount = 0;
            $gst_data = [];
            $total_cgst = $total_sgst = $total_igst = 0;
            $i = isset($_POST['product_code']) ? sizeof($_POST['product_code']) : 0;
            for ($r = 0; $r < $i; $r++) {
                $item_id = $_POST['product_id'][$r];
                $item_type = $_POST['product_type'][$r];
                $item_code = $_POST['product_code'][$r];
                $item_name = $_POST['product_name'][$r];
				$item_batch_no = $_POST['batch_no'][$r];
				$item_available_qty = $_POST['available_qty'][$r];
                //$item_option = isset($_POST['product_option'][$r]) && $_POST['product_option'][$r] != 'false' ? $_POST['product_option'][$r] : null;
                //$real_unit_price = $this->sma->formatDecimal($_POST['real_unit_price'][$r]);
               // $unit_price = $this->sma->formatDecimal($_POST['unit_price'][$r]);
               $item_unit_quantity = $_POST['quantity'][$r];
               // $item_tax_rate = isset($_POST['product_tax'][$r]) ? $_POST['product_tax'][$r] : null;
               // $item_discount = isset($_POST['product_discount'][$r]) ? $_POST['product_discount'][$r] : null;
                $item_unit = $_POST['product_unit'][$r];
                $item_quantity = $_POST['product_base_quantity'][$r];

               if (!empty($item_code)) {
                    $product_details = $item_type != 'manual' ? $this->store_returns_model->getProductByCode($item_code) : null;
                    // $unit_price = $real_unit_price;
                   // $pr_discount = $this->siteprocurment->calculateDiscount($item_discount, $unit_price);
                  //  $unit_price = $this->sma->formatDecimal($unit_price - $pr_discount);
                   // $item_net_price = $unit_price;
                  //  $pr_item_discount = $this->sma->formatDecimal($pr_discount * $item_unit_quantity);
                  //  $product_discount += $pr_item_discount;
                  //  $pr_item_tax = $item_tax = 0;
                  //  $tax = "";

                   

                    $products[] = array(
                        'product_id' => $item_id,
						'batch_no' => $item_batch_no,
						'available_qty' => $item_available_qty,
                        'product_code' => $item_code,
                        'product_name' => $item_name,
                        'product_type' => $item_type,
                        'quantity' => $item_quantity,
						'unit_quantity' => $item_quantity,
                        'product_unit_id' => $item_unit,
                       // 'product_unit_code' => $unit->code,
                        'warehouse_id' => $warehouse_id,
                    );

                }
            }
            if (empty($products)) {
                $this->form_validation->set_rules('product', lang("order_items"), 'required');
            } else {
                $products;
            }

            if($this->siteprocurment->GETaccessModules('')){
				$approved_by = $this->session->userdata('user_id');
			}
			if($status == 'process'){
				$un = $this->siteprocurment->getUsersnotificationWithoutSales();
				foreach($un as $un_row)
				$notification = array(
					'user_id' => $un_row->user_id,
					'group_id' => $un_row->group_id,
					'title' => 'Purchases Request',
					'message' => 'The new purchase request has been created. REF No:'.$reference.', Date:'.$date,
					'created_by' => $this->session->userdata('user_id'),
					'created_on' => date('Y-m-d H:i:s'),
				);	
				$this->siteprocurment->insertNotification($notification);
			}
            $data = array(
				'reference_no' => $reference,
				'date' => $date,
				'request_type' => $this->input->post('request_type'),
                
				'from_store_id' => $from_store_id,
				'to_store_id' => $to_store_id,
                'warehouse_id' => $warehouse_id,
                'note' => $note,
                'status' => $status,
                'created_by' => $this->session->userdata('user_id'),
				'approved_by' => $approved_by ? $approved_by : 0,
				'approved_on' => $date,
				'is_create' => date('Y-m-d H:i:s'),
                'hash' => hash('sha256', microtime() . mt_rand()),
            );

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

            //$this->sma->print_arrays($data, $products);die;
        }

        if ($this->form_validation->run() == true && $this->store_returns_model->addStore_returns($data, $products)) {
            $this->session->set_userdata('remove_quls', 1);
            $this->session->set_flashdata('message', $this->lang->line("store_returns_added"));
            admin_redirect('procurment/store_returns');
        } else {
			$this->data['stores'] = $this->siteprocurment->getAllStores();
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['billers'] = ($this->Owner || $this->Admin || !$this->session->userdata('biller_id')) ? $this->siteprocurment->getAllCompanies('biller') : null;
            //$this->data['currencies'] = $this->siteprocurment->getAllCurrencies();
            $this->data['warehouses'] = ($this->Owner || $this->Admin || !$this->session->userdata('warehouse_id')) ? $this->siteprocurment->getAllWarehouses() : null;
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('procurment/store_returns'), 'page' => lang('store_returns')), array('link' => '#', 'page' => lang('add_store_returns')));
            $meta = array('page_title' => lang('add_store_returns'), 'bc' => $bc);
            $this->page_construct('procurment/store_returns/add', $meta, $this->data);
        }
    }

        public function edit($id = null){
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
          $inv = $this->store_returns_model->getStore_returnsByID($id);
           if ($inv->status == 'approved' || $inv->status == 'completed') {
	      $this->session->set_flashdata('error', lang("Do not allowed edit option"));
	      admin_redirect("procurment/store_returns");
	     } 
			$this->form_validation->set_rules('date', $this->lang->line("date"), 'required');
			$this->session->unset_userdata('csrf_token');
			if ($this->form_validation->run() == true) {
            $date = date('Y-m-d H:i:s');           
            $i = count($_POST['product_id']);
			$products = array();
			for($r = 0; $r < $i; $r++){
			$total_t_qty = 0;$total_r_qty=0;
		    $products[$r] = array(
			    'store_return_itemid'=>$_POST["store_return_itemid"][$r],
			    'product_id' => $_POST["product_id"][$r],
			    'product_code' => $_POST['product_code'][$r],
			    'product_type' => $_POST['product_type'][$r],
			    'product_name' => $_POST['product_name'][$r],			    
			    'request_qty' => $_POST['request_qty'][$r],
			    'store_id' =>$this->store_id,
				'variant_id'=>$_POST['variant_id'][$r]
			    );
		    foreach($_POST['batch'][$this->store_id.$_POST["product_id"][$r]] as $k => $row){
				$products[$r]['batches'][] = array(
		        'id'=>$row['itemid'],
				'store_return_item_id'=>$row['storereturnitemid'],	
				'store_return_id'=>$row['storereturnid'],					
			    'return_qty' => $row['return_qty'],
			    'received_qty' => $row['received_qty'],
			    'batch' => $row['batch_no'],
			    'vendor_id' => $row['vendor_id'],
			    'expiry' => $row['expiry'],
			    'cost_price' => $row['cost_price'],
			    'selling_price' => $row['selling_price'],
			    'landing_cost' => $row['landing_cost'],
			    'unit_price' => $row['selling_price'],
			    'net_unit_price' => $row['selling_price']*$row['request_qty'],
			    'tax' => $row['tax'],
			    'tax_method' => $row['tax_method'],
			    'gross_amount' => $row['gross'],
			    'tax_amount' => $row['tax_amount'],
			    'net_amount' => $row['product_grand_total'],
			    'store_id' =>$this->store_id,
				'invoice_id' => $row['invoice_id'],
				'category_id'=>  $_POST['category_id'][$r],
				'subcategory_id'=> $_POST['subcategory_id'][$r],  
				'brand_id'=> $_POST['brand_id'][$r], 
				'variant_id'=>$_POST['variant_id'][$r],
			    'return_type'=>$row['r_type'],
			);
			$total_t_qty +=$row['return_qty'];
			$total_r_qty +=$row['received_qty'];
		    }
		    $products[$r]['return_qty'] = $total_t_qty;
		    $products[$r]['received_qty'] = $total_r_qty;
			}
            if (empty($products)) {
                $this->form_validation->set_rules('product', lang("order_items"), 'required');
            } else {
                
                krsort($items);
            }
            
            $data = array(
		       'total_no_items'=>$this->input->post('total_no_items'),
		       'total_no_qty'=>$this->input->post('total_no_qty'),
		       'status' =>$this->input->post('status'),
			   'reference_no'=>$inv->reference_no
            );
			 if($this->input->post('status')=="approved"){
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
		/* 	print_r($products);
			die; */
        }

        if ($this->form_validation->run() == true && $this->store_returns_model->updateStoreReturns($id, $data, $products)) {
            $this->session->set_userdata('remove_pols', 1);
            $this->session->set_flashdata('message', $this->lang->line("pro_store_receivers_added"));
            admin_redirect('procurment/store_returns');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['sr'] = $inv;
         
            $inv_items = $this->store_returns_model->getStoreReturnsItems($id);   
            krsort($inv_items);
            $c = rand(100000, 9999999);
            foreach ($inv_items as $item) {
              $row                 = $this->siteprocurment->getItemByID($item->product_id);
			  $row->tax_method     = $item->tax_method;
			  $row->variant_id     = $item->variant_id;
			  $row->category_id    = $row->category_id;
			  $row->subcategory_id = $row->subcategory_id;
			  $row->brand_id       = $row->brand;
			  $batches             = $this->store_returns_model->getReturnStockData($item->id);
			  $row->request_qty    = $item->request_qty;
			  $row->received_qty   = $item->received_qty;
			  $row->batches = $batches;
			  $unique_item_id = $this->store_id.$item->product_id.$item->batch;
			  $ri = $row->id;
			  $options = array();
			  $pr[$unique_item_id] = array('unique_id'=>$unique_item_id,'id' => $row->id,'store_receiveItemid'=>$item->id, 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")",
				'row' => $row,  'options' => $options);
            }

            $this->data['inv_items']       = json_encode($pr);
            $this->data['id']              = $id;
            $this->data['suppliers']       = $this->siteprocurment->getAllCompanies('supplier');
			$this->data['warehouses']      = $this->siteprocurment->getAllWarehouses();
			$this->data['all_stores']      = $this->siteprocurment->getAllWarehouses_Storeslist();
			$this->data['store_req']       = $this->siteprocurment->getAll_respectiveSTOREREQUESTNUMBER();
			$this->data['stores']          = $this->siteprocurment->getAllWarehouses_Stores();
            $this->load->helper('string');
            $value = random_string('alnum', 20);
            $this->session->set_userdata('user_csrf', $value);
            $this->session->set_userdata('remove_pols', 1);
            $this->data['csrf'] = $this->session->userdata('user_csrf');
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('procurment/store_returns'), 'page' => lang('store_returns')), array('link' => '#', 'page' => lang('edit_store_returns')));
            $meta = array('page_title' => lang('edit_store_returns'), 'bc' => $bc);
        
            $this->page_construct('procurment/store_returns/edit', $meta, $this->data);
        }
    }

    public function delete($id = null){
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
		  $inv = $this->store_returns_model->getStore_returnsByID($id);
           if ($inv->status == 'approved' || $inv->status == 'completed') {
	      $this->session->set_flashdata('error', lang("Do not allowed edit option"));
	      admin_redirect("procurment/store_returns");
	     } 
        if ($this->store_returns_model->deleteStore_returns($id)) {
            if ($this->input->is_ajax_store_returns()) {
                $this->sma->send_json(array('error' => 0, 'msg' => lang("store_returns_deleted")));
            }
            $this->session->set_flashdata('message', lang('store_returns_deleted'));
            admin_redirect('procurment/welcome');
        }
    }

    public function suggestions(){
        $term = $this->input->get('term', true);
        $warehouse_id = $this->input->get('warehouse_id', true);
        $store_id = $this->input->get('store_id', true);
        if (strlen($term) < 1 || !$term) {
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . admin_url('procurment/welcome') . "'; }, 10);</script>");
        }
        $analyzed = $this->sma->analyze_term($term);
        $sr = $analyzed['term'];
        $option_id = $analyzed['option_id'];
        $warehouse = $this->siteprocurment->getWarehouseByID($warehouse_id);
        $rows = $this->store_returns_model->getProductNames($sr, $warehouse_id, $store_id);
        if ($rows) {
            $c = str_replace(".", "", microtime(true));
            $r = 0;
            foreach ($rows as $row) {
                unset($row->cost, $row->details, $row->product_details, $row->image, $row->barcode_symbology, $row->cf1, $row->cf2, $row->cf3, $row->cf4, $row->cf5, $row->cf6, $row->supplier1price, $row->supplier2price, $row->cfsupplier3price, $row->supplier4price, $row->supplier5price, $row->supplier1, $row->supplier2, $row->supplier3, $row->supplier4, $row->supplier5, $row->supplier1_part_no, $row->supplier2_part_no, $row->supplier3_part_no, $row->supplier4_part_no, $row->supplier5_part_no);
                $option = false;
                $row->quantity = 0;
                $row->item_tax_method = $row->tax_method;
                $row->qty = 1;
                $row->discount = '0';
                $options = $this->store_returns_model->getProductOptions($row->id, $warehouse_id);
                if ($options) {
                    $opt = $option_id && $r == 0 ? $this->store_returns_model->getProductOptionByID($option_id) : $options[0];
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
				$row->batch_no = $row->purchase_batch_no;
				$row->available_qty = $row->available_quantity;
                $row->base_unit_price = $row->price;
                $row->unit = $row->sale_unit ? $row->sale_unit : $row->unit;
                $combo_items = false;
                if ($row->type == 'combo') {
                    $combo_items = $this->store_returns_model->getProductComboItems($row->id, $warehouse_id);
                }
                $units = $this->siteprocurment->getUnitsByBUID($row->base_unit);
                $tax_rate = $this->siteprocurment->getTaxRateByID($row->tax_rate);

                $pr[] = array('id' => ($c + $r), 'item_id' => $row->stock_id, 'label' => $row->name . " (" . $row->purchase_batch_no . ")", 'category' => $row->category_id,
                    'row' => $row, 'combo_items' => $combo_items, 'tax_rate' => $tax_rate, 'units' => $units, 'options' => $options);
				
                $r++;
            }
            $this->sma->send_json($pr);
        } else {
            $this->sma->send_json(array(array('id' => 0, 'label' => lang('no_match_found'), 'value' => $term)));
        }
    }

    public function store_returns_actions()
    {
        if (!$this->Owner && !$this->GP['bulk_actions']) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER["HTTP_REFERER"]);
        }

        $this->form_validation->set_rules('form_action', lang("form_action"), 'required');

        if ($this->form_validation->run() == true) {

            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {

                    //$this->sma->checkPermissions('delete');
                    foreach ($_POST['val'] as $id) {
                        $this->store_returns_model->deleteStore_returns($id);
                    }
                    $this->session->set_flashdata('message', $this->lang->line("store_returns_deleted"));
                    redirect($_SERVER["HTTP_REFERER"]);

                } elseif ($this->input->post('form_action') == 'combine') {

                    $html = $this->combine_pdf($_POST['val']);

                } elseif ($this->input->post('form_action') == 'export_excel') {

                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle(lang('store_returns'));
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('date'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('reference_no'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('biller'));
                    $this->excel->getActiveSheet()->SetCellValue('D1', lang('customer'));
                    $this->excel->getActiveSheet()->SetCellValue('E1', lang('total'));
                    $this->excel->getActiveSheet()->SetCellValue('F1', lang('status'));

                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $qu = $this->store_returns_model->getStore_returnsByID($id);
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $this->sma->hrld($qu->date));
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $qu->reference_no);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $qu->biller);
                        $this->excel->getActiveSheet()->SetCellValue('D' . $row, $qu->customer);
                        $this->excel->getActiveSheet()->SetCellValue('E' . $row, $qu->total);
                        $this->excel->getActiveSheet()->SetCellValue('F' . $row, $qu->status);
                        $row++;
                    }

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'quotations_' . date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);
                }
            } else {
                $this->session->set_flashdata('error', $this->lang->line("no_store_returns_selected"));
                redirect($_SERVER["HTTP_REFERER"]);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }

   
}
