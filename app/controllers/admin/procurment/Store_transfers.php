<?php defined('BASEPATH') or exit('No direct script access allowed');
class Store_transfers extends MY_Controller{
    public function __construct(){
        parent::__construct();
        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }
        if ($this->Customer) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER["HTTP_REFERER"]);
        }
        $this->lang->admin_load('procurment/store_transfers', $this->Settings->user_language);
        $this->load->library('form_validation');
        $this->load->admin_model('procurment/store_transfers_model');
        $this->digital_upload_path = 'files/';
        $this->upload_path = 'assets/uploads/';
        $this->thumbs_path = 'assets/uploads/thumbs/';
        $this->image_types = 'gif|jpg|jpeg|png|tif';
        $this->digital_file_types = 'zip|psd|ai|rar|pdf|doc|docx|xls|xlsx|ppt|pptx|gif|jpg|jpeg|png|tif|txt';
        $this->allowed_file_size = '1024';
        $this->data['logo'] = true;
		$this->Muser_id = $this->session->userdata('user_id');
		$this->Maccess_id = 8;
    }
	
	public function store_transfers_list(){
		$poref =  $this->input->get('poref');
		$data['store_transfers'] = $this->store_transfers_model->getRequestByID($poref);
		$store_req_items = $this->store_transfers_model->getAllRequestItems($poref);
		$c = rand(100000, 9999999);
		foreach ($store_req_items as $item) {
			$row = $this->siteprocurment->getItemByID($item->product_id);
	     	$row->request_qty=$item->quantity;
			$option = false;
                $row->item_tax_method = $row->tax_method;
                $options = $this->store_transfers_model->getProductOptions($row->id);
                if ($options) {
                    $opt = $option_id && $r == 0 ? $this->store_transfers_model->getProductOptionByID($option_id) : current($options);
                    if (!$option_id || $r > 0) {
                        $option_id = $opt->id;
                    }
                } else {
                    $opt = json_decode('{}');
                    $opt->cost = 0;
                    $option_id = FALSE;
                }
                $row->option = $option_id;
                $row->supplier_part_no = '';
                if ($opt->cost != 0) {
                    $row->cost = $opt->cost;
                }
                $row->cost             = $supplier_id ? $this->getSupplierCost($supplier_id, $row) : $row->cost;
                $row->real_unit_cost   = $row->cost;
                $row->base_quantity    = 1;
                $row->base_unit        = $row->unit;
                $row->base_unit_cost   = $row->cost;
                $row->unit             = $row->purchase_unit ? $row->purchase_unit : $row->unit;
                $row->new_entry        = 1;
                $row->expiry           = '';
                $row->variant_id       =$item->option_id;
                $row->quantity_balance = '';
                $row->discount = '0';
				$unique_item_id = $this->store_id.$row->id.$row->variant_id.$row->batch.$row->category_id.$row->subcategory_id.$row->brand_id.$row->invoice_id;
                unset($row->details, $row->product_details, $row->price, $row->file, $row->supplier1price, $row->supplier2price, $row->supplier3price, $row->supplier4price, $row->supplier5price, $row->supplier1_part_no, $row->supplier2_part_no, $row->supplier3_part_no, $row->supplier4_part_no, $row->supplier5_part_no);
                $units        = $this->siteprocurment->getUnitsByBUID($row->base_unit);
                $tax_rate     = $this->siteprocurment->getTaxRateByID($row->tax_rate);
				$batches      =  $this->store_transfers_model->loadbatches($row->id);
				$row->batches = $batches;
                $pr[$row->id] = array('id' => $unique_item_id, 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")",
                    'row' => $row, 'unique_id'=>$unique_item_id,'tax_rate' => $tax_rate, 'units' => $units, 'options' => $options);
			$c++;
		}
		$data['store_transfersitem'] = $pr;
		if(!empty($data)){
			$response['status'] = 'success';
			$response['value'] = $data;
		}else{
			$response['status'] = 'error';
			$response['value'] = '';
		}
		echo json_encode($response);
		exit;
	}

	public function supplier(){
		$supplier_id =  $this->input->get('supplier_id');
		$data = $this->store_transfers_model->getSupplierdetails($supplier_id);
		if(!empty($data)){
			$response['supplier_name'] = $data->name;
			$response['supplier_code'] = $data->ref_id;
			$response['supplier_vatno'] = $data->vat_no;
			$response['supplier_address'] = $data->address.' '.$data->city.' '.$data->state.' '.$data->country;
			$response['supplier_email'] = $data->email;
			$response['supplier_phno'] = $data->phone;
		}else{
			$response['supplier_name'] = '';
			$response['supplier_code'] = '';
			$response['supplier_vatno'] = '';
			$response['supplier_address'] = '';
			$response['supplier_email'] = '';
			$response['supplier_phno'] = '';
		}
		echo json_encode($response);
		exit;
	}
    /* ------------------------------------------------------------------------- */

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

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('store_transfers')));
        $meta = array('page_title' => lang('store_transfers'), 'bc' => $bc);
        $this->page_construct('procurment/store_transfers/index', $meta, $this->data);

    }

    public function getStore_transfers($warehouse_id = null){ 
		$view_link = '<a href="'.admin_url('procurment/store_transfers/view/$1').'" data-toggle="modal" data-target="#myModal"><i class="fa fa-edit"></i>'.lang('view_store_transfer').'</a>';
        $edit_link = anchor('admin/procurment/store_transfers/edit/$1', '<i class="fa fa-edit"></i> ' . lang('edit_store_transfer'));
        $delete_link = "<a href='#' class='po' title='<b>" . $this->lang->line("delete_store_transfer") . "</b>' data-content=\"<p>"
        . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('procurment/store_transfers/delete/$1') . "'>"
        . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i> "
        . lang('delete_store_transfer') . "</a>";
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
        if ($warehouse_id) {        
		 $this->datatables
			 ->select("pro_store_transfers.id, pro_store_transfers.processed_on, pro_store_transfers.reference_no, f.reference_no, wf.name as from_name, wt.name as to_name, SUM(sti.transfer_qty) as transfer_quantity, pro_store_transfers.status")
                ->from('pro_store_transfers')
				->join('pro_store_indent_receive f', 'f.id = pro_store_transfers.store_indent_id', 'left')
				->join('pro_store_transfer_items sti', 'sti.store_transfer_id = pro_store_transfers.id', 'left')
				->join('warehouses wf', 'wf.id = f.from_store_id', 'left')
				->join('warehouses wt', 'wt.id = f.to_store_id', 'left')
				 ->where('pro_store_transfers.warehouse_id', $warehouse_id)
				  ->where('pro_store_transfers.total_no_items !=',0);
        } else {
           $this->datatables
			 ->select("pro_store_transfers.id, pro_store_transfers.created_on, pro_store_transfers.reference_no, f.reference_no as store_req_no, wf.name as from_name, wt.name as to_name, SUM(sti.transfer_qty) as transfer_quantity, pro_store_transfers.status")
                ->from('pro_store_transfers')
					->join('pro_store_indent_receive f', 'f.id = pro_store_transfers.store_indent_id', 'left')
				->join('pro_store_transfer_items sti', 'sti.store_transfer_id = pro_store_transfers.id', 'left')
				->join('warehouses wf', 'wf.id = pro_store_transfers.from_store', 'left')
				->join('warehouses wt', 'wt.id = pro_store_transfers.to_store', 'left')
				->where('pro_store_transfers.total_no_items !=',0);
                
        }
		$this->datatables->group_by('pro_store_transfers.id');
        $this->datatables->add_column("Actions", $action, "pro_store_transfers.id");
        echo $this->datatables->generate();
    }

    /* ----------------------------------------------------------------------------- */

  

    public function view($store_transfers_id = null){
        if ($this->input->get('id')) {
             $store_transfers_id = $this->input->get('id');
        }
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['store_tranf_details']=$po = $this->store_transfers_model->getStore_transfersByID($store_transfers_id);
		$store_transfer_items = $this->store_transfers_model->getAllStore_transfersItems($store_transfers_id);
        if (!$this->session->userdata('view_right')) {
            $this->sma->view_rights($po->created_by);
        }
		   krsort($store_transfer_items);
            $c = rand(100000, 9999999);
			 foreach ($store_transfer_items as $item) {
                $row = $this->siteprocurment->getRecipeByID($item->product_id);
                $row->id = $item->product_id;
                $row->code = $item->product_code;
                $row->name = $item->product_name;
                $row->type = $item->product_type;
                $ri = $this->Settings->item_addition ? $row->id : $c;
                $pr[$ri] = array('id' => $c, 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'row' => $item);
                $c++;
            }
        $this->data['store_tranf_items'] = $pr;
	    $this->data['fromstore'] = $this->siteprocurment->getWarehouseByID($po->from_store);
		$this->data['to_store'] = $this->siteprocurment->getWarehouseByID($po->to_store);
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('procurment/store_transfers'), 'page' => lang('store_transfers')), array('link' => '#', 'page' => lang('view')));
        $meta = array('page_title' => lang('view_store_transfers_details'), 'bc' => $bc);
        $this->load->view($this->theme . 'procurment/store_transfers/view',  $this->data);

    }


    /* -------------------------------------------------------------------------------------------------------------------------------- */

    public function add($store_transfers_id = null){
        $this->form_validation->set_rules('from_store_id', $this->lang->line("from_store_id"), 'required');
        $this->session->unset_userdata('csrf_token');
         if ($this->form_validation->run() == true) {          
         //   p($_POST,1);
	     $n = $this->siteprocurment->lastidStoreTransafer();
		 $n=($n !=0)?$n+1:$this->store_id .'1';
	     $reference = 'ST'.str_pad($n + 1, 8, 0, STR_PAD_LEFT);
	     $date = date('Y-m-d H:i:s');
		 $i = count($_POST['product_id']);
	     $products = array();
	     for($r = 0; $r < $i; $r++){
		 $total_t_qty = 0;
		    $products[$r] = array(
			    'product_id' => $_POST["product_id"][$r],
			    'product_code' => $_POST['product_code'][$r],
			    'product_type' => $_POST['product_type'][$r],
			    'product_name' => $_POST['product_name'][$r],			    
			    'request_qty' => $_POST['request_qty'][$r],
			    'store_id' =>$this->store_id,
				'variant_id'=>$_POST['variant_id'][$r]
			    );
		    
		    foreach($_POST['batch'][$_POST["product_id"][$r]] as $k => $row){
			if($row['transfer_qty']!=0){
				$unit = $this->site->getUnitByID($row['product_unit']);
				$product_unit_code=$unit->code;
		     	$products[$r]['batches'][] = array(
			    'available_qty'    => $row['available_qty'],
				'variant_id'       => $_POST['variant_id'][$r],
			    'transfer_qty'     => $row['transfer_qty'],
			    'pending_qty'      => $row['pending_qty'],
			    'batch'            => $row['batch_no'],
			    'vendor_id'        => $row['vendor_id'],
			    'expiry'           => $row['expiry'],
			    'cost_price'       => $row['cost_price'],
			    'selling_price'    => $row['selling_price'],
			    'landing_cost'     => $row['landing_cost'],
			    'unit_price'       => $row['selling_price'],
			    'net_unit_price'   => $row['selling_price']*$row['request_qty'],
			    //'tax' => $row['tax'],
			   // 'tax_method' => $row['tax_method'],
			    'gross_amount'     => $row['gross'],
			    'tax_amount'       => $row['tax_amount'],
			    'net_amount'       => $row['grand_total'],
			    'store_id'         => $this->store_id,
			    'stock_id'         => $row['stock_id'],
				'product_unit_id'  => $row['product_unit'],
				'unit_quantity'    => $row['base_quantity'],
				'transfer_unit_qty'=> $row['base_quantity'],
				'product_unit_code' =>$unit->code,
				'category_id'      => $row['catgory_id'],
				'subcategory_id'   => $row['subcatgory_id'],
				'brand_id'         => !empty($row['brand_id'])?$row['brand_id']:0,
				'invoice_id'       => !empty($row['invoice_id'])?$row['invoice_id']:0,
				
			);
			}
			$total_t_qty +=$row['transfer_qty'];
		    }
		    $products[$r]['transfer_qty'] = $total_t_qty;
	     }
            if (empty($products)) {
                $this->form_validation->set_rules('product', lang("order_items"), 'required');
            } else {
                krsort($products);
            }
            $data = array(
			  'reference_no' =>$reference,
			  'store_id' =>$this->store_id,
			  'intend_request_id' =>($this->input->post('intend_request_id'))?$this->input->post('intend_request_id'):0,
			  'intend_request_date' =>($this->input->post('intend_request_date'))?$this->input->post('intend_request_date'):0,
			  'from_store' => $this->input->post('from_store_id'),
			  'to_store' =>$this->input->post('to_store_id'),
			  'total_no_items'=>$this->input->post('total_no_items'),
			  'total_no_qty'=>$this->input->post('total_no_qty'),
			  'status' =>$this->input->post('status'),
			  'remarks'=>$this->input->post('remarks')
            );
			if($data['intend_request_id']!=''){
				$store_indent = $this->store_transfers_model->getStoreindentData($data['intend_request_id']);
				$data['store_indent_id'] = $store_indent->store_indent_id;
				$data['store_indent_date'] = $store_indent->store_indent_date;
			}	
			
			if($data['status']=='process'){
				$data['processed_by'] = $this->session->userdata('user_id');
				$data['processed_on']=date('Y-m-d H:i:s');
			}else{
				$data['approved_by'] = $this->session->userdata('user_id');
				$data['approved_on']=date('Y-m-d H:i:s');
			}
           
            if ($_FILES['document']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path']     = $this->digital_upload_path;
                $config['allowed_types']   = $this->digital_file_types;
                $config['max_size']        = $this->allowed_file_size;
                $config['overwrite']       = false;
                $config['encrypt_name']    = true;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('document')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER["HTTP_REFERER"]);
                }
                $photo = $this->upload->file_name;
                $data['attachment'] = $photo;
            }
			
			 $store_transfers_array = array();	
			 if($this->input->post('requestnumber') != ''){
				if(in_array(1, $check_quantity)){
					$rstatus = 'partial_complete';
				}else{
					$rstatus = 'completed';
				}
				    $store_transfers_array = array('status' => $rstatus);
			 }
	
        }
        if ($this->form_validation->run() == true && $this->store_transfers_model->addStore_transfers($data, $products, $store_transfers_array, $this->input->post('requestnumber'))) {
            
            $this->session->set_userdata('remove_pols', 1);
            $this->session->set_flashdata('message', $this->lang->line("store_transfers_added"));
            admin_redirect('procurment/store_transfers');
        } else {
			
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['store_transfers_id'] = $store_transfers_id;
            $this->data['suppliers'] = $this->siteprocurment->getAllCompanies('supplier');
            $this->data['categories'] = $this->siteprocurment->getAllCategories();
            $this->data['tax_rates'] = $this->siteprocurment->getAllTaxRates();
            $this->data['warehouses'] = $this->siteprocurment->getAllWarehouses();
			$this->data['all_stores'] = $this->siteprocurment->getAllWarehouses_Storeslist();
			$this->data['store_req'] = $this->siteprocurment->getAll_respectiveSTOREREQUESTNUMBER();
			$this->data['stores'] = $this->siteprocurment->getAllWarehouses_Stores();
            $this->load->helper('string');
            $value = random_string('alnum', 20);
            $this->session->set_userdata('user_csrf', $value);
            $this->data['csrf'] = $this->session->userdata('user_csrf');
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('procurment/store_transfers'), 'page' => lang('store_transfers')), array('link' => '#', 'page' => lang('add_store_transfers')));
            $meta = array('page_title' => lang('add_store_transfers'), 'bc' => $bc);
            $this->page_construct('procurment/store_transfers/add', $meta, $this->data);
        }
    }


 public function edit($id = null){
		$this->sma->checkPermissions();
        $stransfer = $this->store_transfers_model->getStore_transfersByID($id);
        if ($stransfer->status == 'approved' || $stransfer->status == 'completed') {
	    $this->session->set_flashdata('error', lang("Do not allowed edit option"));
	    admin_redirect("procurment/store_transfers");
		}
        $this->form_validation->set_rules('status', $this->lang->line("status"), 'required');
        $this->session->unset_userdata('csrf_token');
        if ($this->form_validation->run() == true) { 
	    $date = date('Y-m-d H:i:s');           
        $i = count($_POST['product_id']);
	    $products = array();
	    for($r = 0; $r < $i; $r++){
		$total_t_qty = 0;
		    $products[$r] = array(
			    'product_id' => $_POST["product_id"][$r],
			    'product_code' => $_POST['product_code'][$r],
			    'product_type' => $_POST['product_type'][$r],
			    'product_name' => $_POST['product_name'][$r],			    
			    'request_qty' => $_POST['request_qty'][$r],
			    'store_id' =>$this->store_id,
				'variant_id'=>$_POST['variant_id'][$r]
			    );
		    foreach($_POST['batch'][$_POST["product_id"][$r]] as $k => $row){
			if($row['transfer_qty']!=0){
			$unit = $this->site->getUnitByID($row['product_unit']);
				$product_unit_code=$unit->code;
		     	$products[$r]['batches'][] = array(   
			    'available_qty'    => $row['available_qty'],
				'variant_id'       => $_POST['variant_id'][$r],
			    'transfer_qty'     => $row['transfer_qty'],
			    'pending_qty'      => $row['pending_qty'],
			    'batch'            => $row['batch_no'],
			    'vendor_id'        => $row['vendor_id'],
			    'expiry'           => $row['expiry'],
			    'cost_price'       => $row['cost_price'],
			    'selling_price'    => $row['selling_price'],
			    'landing_cost'     => $row['landing_cost'],
			    'unit_price'       => $row['selling_price'],
			    'net_unit_price'   => $row['selling_price']*$row['request_qty'],
			    //'tax' => $row['tax'],
			   // 'tax_method' => $row['tax_method'],
			    'gross_amount'     => $row['gross'],
			    'tax_amount'       => $row['tax_amount'],
			    'net_amount'       => $row['grand_total'],
			    'store_id'         => $this->store_id,
			    'stock_id'         => $row['stock_id'],
				'product_unit_id'  => $row['product_unit'],
				'unit_quantity'    => $row['base_quantity'],
				'transfer_unit_qty'=> $row['base_quantity'],
				'product_unit_code' =>$unit->code,
				'category_id'      => $row['catgory_id'],
				'subcategory_id'   => $row['subcatgory_id'],
				'brand_id'         => !empty($row['brand_id'])?$row['brand_id']:0,
				'invoice_id'       => !empty($row['invoice_id'])?$row['invoice_id']:0,
				
			);
			}
			$total_t_qty +=$row['transfer_qty'];
		    }
		    $products[$r]['transfer_qty'] = $total_t_qty;
	     }   
            if (empty($products)) {
                $this->form_validation->set_rules('product', lang("transfer_items"), 'required');
            } else {
                krsort($products);
            }
			$data = array(
				'intend_request_id' =>($this->input->post('intend_request_id'))?$this->input->post('intend_request_id'):0,
				'intend_request_date' =>($this->input->post('intend_request_date'))?$this->input->post('intend_request_date'):0,
				'to_store' =>$this->input->post('to_store_id'),
				'total_no_items'=>$this->input->post('total_no_items'),
				'total_no_qty'=>$this->input->post('total_no_qty'),
				'status' =>$this->input->post('status'),
				);
			if($data['intend_request_id']!=''){
				$store_indent = $this->store_transfers_model->getStoreindentData($data['intend_request_id']);
				$data['store_indent_id'] = $store_indent->store_indent_id;
				$data['store_indent_date'] = $store_indent->store_indent_date;
			}
			if($data['status']=='process'){
				$data['processed_by'] = $this->session->userdata('user_id');
				$data['processed_on']=date('Y-m-d H:i:s');
			}else{
				$data['approved_by'] = $this->session->userdata('user_id');
				$data['approved_on']=date('Y-m-d H:i:s');
			}
			$data['remarks']=$this->input->post('remarks');
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
	         $store_transfers_array = array();	
	       if($this->input->post('requestnumber') != ''){
		    if(in_array(1, $check_quantity)){
			    $rstatus = 'partial_complete';
		    }else{
			    $rstatus = 'completed';
		    }
		    $store_transfers_array = array(
			    'status' => $rstatus,
		    );
		    
	    }
        }
        if ($this->form_validation->run() == true && $this->store_transfers_model->updateStore_transfers($id,$data, $products)) {
            $this->session->set_userdata('remove_pols', 1);
            $this->session->set_flashdata('message', $this->lang->line("store_transfers_updated"));
            admin_redirect('procurment/store_transfers');
        } else {
			$store_transfer_items = $this->store_transfers_model->getAllStore_transfersItems($id);
			$pr = array();
			foreach ($store_transfer_items as $item) {
			$row 					  = $this->siteprocurment->getItemByID($item->product_id);
			$row->available_qty       = $item->available_qty;
			
			$row->qty                 = $item->wastage_qty;
			$row->transfer_qty        = $item->wastage_qty;
			$row->pending_qty         = $item->pending_qty;
			$row->batch_no            = $item->batch;
			$row->expiry              = $item->expiry;
			$row->cost_price          = $item->cost_price;
			$row->base_unit           = $row->unit;
            $row->unit                = $row->purchase_unit ? $row->purchase_unit : $row->unit;
			$row->price               = $item->selling_price;
			$row->tax                 = $item->tax;
			$row->tax_method          = $item->tax_method;
			$row->variant_id          = $item->variant_id;
			$row->brand_id            = $item->brand_id;
			$row->category_id         = $item->category_id;
			$row->subcategory_id      = $item->subcategory_id;
			$p_ids                    = array($item->product_id);$s_ids =  array($this->store_id); 
			$batches                  = $this->store_transfers_model->getbatchStockData($item->id);
			$row->batches             = $batches;
			
			$units = $this->siteprocurment->getUnitsByBUID($row->base_unit);
			$unique_item_id = $this->store_id.$item->product_id.$item->batch;
			$ri = $row->id;
			$options = array();
			$pr[$ri] = array('unique_id'=>$unique_item_id,'id' => $row->id, 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")",
				'row' => $row,  'options' => $options,'units'=>$units);
			}
			$this->data['store_transfersitem'] = json_encode($pr);
            $this->data['error']               = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['store_transfers_id']  = $id;
            $this->data['suppliers']           = $this->siteprocurment->getAllCompanies('supplier');
            $this->data['categories']          = $this->siteprocurment->getAllCategories();
            $this->data['tax_rates']           = $this->siteprocurment->getAllTaxRates();
            $this->data['warehouses']          = $this->siteprocurment->getAllWarehouses();
			$this->data['store_transfer']      = $stransfer;
			$this->data['store_req']           = $this->store_transfers_model->getStockReference($id);
			if(!$this->data['store_req']){
			$this->data['store_req']           = $this->siteprocurment->getAll_respectiveSTOREREQUESTNUMBER();
			}
			$this->data['stores'] = $this->siteprocurment->getAllWarehouses_Stores();
            $this->load->helper('string');
            $value = random_string('alnum', 20);
            $this->session->set_userdata('user_csrf', $value);
            $this->data['csrf'] = $this->session->userdata('user_csrf');
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('procurment/store_transfers'), 'page' => lang('store_transfers')), array('link' => '#', 'page' => lang('edit_store_transfers')));
            $meta = array('page_title' => lang('edit_store_transfers'), 'bc' => $bc);
            $this->page_construct('procurment/store_transfers/edit', $meta, $this->data);
        }
    }

    /* ----------------------------------------------------------------------------------------------------------- */

  


   public function delete($id = null){
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
		 $stransfer = $this->store_transfers_model->getStore_transfersByID($id);
        if ($stransfer->status == 'approved' || $stransfer->status == 'completed') {
	    $this->session->set_flashdata('error', lang("Do not allowed edit option"));
	    admin_redirect("procurment/store_transfers");
		}
        if ($this->store_transfers_model->deleteStore_transfers($id)) {
            if ($this->input->is_ajax_request()) {
                $this->sma->send_json(array('error' => 0, 'msg' => lang("store_transfers_deleted")));
            }
            $this->session->set_flashdata('message', lang('store_transfers_deleted'));
            admin_redirect('procurment/welcome');
        }
    }
    
    public function suggestions(){
        $term = $this->input->get('term', true);
        $supplier_id = $this->input->get('supplier_id', true);
        if (strlen($term) < 1 || !$term) {
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . admin_url('procurment/welcome') . "'; }, 10);</script>");
        }
        $analyzed       = $this->sma->analyze_term($term);
        $sr             = $analyzed['term'];
        $option_id      = $analyzed['option_id'];
        $rows           = $this->siteprocurment->getProductNames($sr);
        if ($rows) {
            $c = str_replace(".", "", microtime(true));
            $r = 0;
            foreach ($rows as $row) {
                $option = false;
                $row->item_tax_method = $row->tax_method;
                $options = $this->store_transfers_model->getProductOptions($row->id);
                if ($options) {
                    $opt = $option_id && $r == 0 ? $this->store_transfers_model->getProductOptionByID($option_id) : current($options);
                    if (!$option_id || $r > 0) {
                        $option_id = $opt->id;
                    }
                } else {
                    $opt = json_decode('{}');
                    $opt->cost = 0;
                    $option_id = FALSE;
                }
                $row->option = $option_id;
                $row->supplier_part_no = '';
                if ($opt->cost != 0) {
                    $row->cost = $opt->cost;
                }
                $row->cost             = $supplier_id ? $this->getSupplierCost($supplier_id, $row) : $row->cost;
                $row->real_unit_cost   = $row->cost;
                $row->base_quantity    = 1;
                $row->base_unit        = $row->unit;
                $row->base_unit_cost   = $row->cost;
                $row->unit             = $row->purchase_unit ? $row->purchase_unit : $row->unit;
                $row->new_entry        = 1;
                $row->expiry           = '';
                $row->qty              = 1;
                $row->quantity_balance = '';
                $row->discount = '0';
				if($row->category_name != ''){
					$cat = ' CAT - ' .$row->category_name;
				}else{
					$cat = '';
				}
				if($row->subcategory_name != ''){
					$sub_cat = ' | SUBCAT - ' .$row->subcategory_name;
				}else{
					$sub_cat = '';
				}
				if($row->brand_name != ''){
					$brand = ' | BRAND - ' .$row->brand_name;
				}else{
					$brand = '';
				}
				$unique_item_id = $this->store_id.$row->id.$row->variant_id.$row->batch.$row->category_id.$row->subcategory_id.$row->brand_id.$row->invoice_id;
                unset($row->details, $row->product_details, $row->price, $row->file, $row->supplier1price, $row->supplier2price, $row->supplier3price, $row->supplier4price, $row->supplier5price, $row->supplier1_part_no, $row->supplier2_part_no, $row->supplier3_part_no, $row->supplier4_part_no, $row->supplier5_part_no);
                $units        = $this->siteprocurment->getUnitsByBUID($row->base_unit);
                $tax_rate     = $this->siteprocurment->getTaxRateByID($row->tax_rate);
				$batches      =  $this->store_transfers_model->loadbatches($row->id);
				$row->batches = $batches;
                $pr[] = array('id' => $unique_item_id, 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")" . $cat.$sub_cat.$brand,
                    'row' => $row, 'unique_id'=>$unique_item_id,'tax_rate' => $tax_rate, 'units' => $units, 'options' => $options);
                $r++;
            }
            $this->sma->send_json($pr);
        } else {
            $this->sma->send_json(array(array('id' => 0, 'label' => lang('no_match_found'), 'value' => $term)));
        }
    }

    /* -------------------------------------------------------------------------------- */

    public function store_transfers_actions(){
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
                        $this->store_transfers_model->deleteStore_transfers($id);
                    }
                    $this->session->set_flashdata('message', $this->lang->line("store_transfers_deleted"));
                    redirect($_SERVER["HTTP_REFERER"]);
                } elseif ($this->input->post('form_action') == 'combine') {
                    $html = $this->combine_pdf($_POST['val']);
                } elseif ($this->input->post('form_action') == 'export_excel') {
                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle(lang('store_transfers'));
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('date'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('reference_no'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('supplier'));
                    $this->excel->getActiveSheet()->SetCellValue('F1', lang('status'));
                    $this->excel->getActiveSheet()->SetCellValue('E1', lang('grand_total'));

                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $store_transfers = $this->store_transfers_model->getStore_transfersByID($id);
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $this->sma->hrld($store_transfers->date));
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $store_transfers->reference_no);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $store_transfers->supplier);
                        $this->excel->getActiveSheet()->SetCellValue('D' . $row, $store_transfers->status);
                        $this->excel->getActiveSheet()->SetCellValue('E' . $row, $this->sma->formatMoney($store_transfers->grand_total));
                        $row++;
                    }

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'store_transfers_' . date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);
                }
            } else {
                $this->session->set_flashdata('error', $this->lang->line("no_store_transfers_selected"));
                redirect($_SERVER["HTTP_REFERER"]);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }

   
}
