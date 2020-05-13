<?php defined('BASEPATH') or exit('No direct script access allowed');

class Store_return_receivers extends MY_Controller{
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
        $this->lang->admin_load('procurment/store_return_receivers', $this->Settings->user_language);
        $this->load->library('form_validation');
        $this->load->admin_model('procurment/store_return_receivers_model');
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
	
	public function store_return_receivers_list(){
		$poref =  $this->input->get('poref');
		$data['store_return_receivers'] = $this->store_return_receivers_model->getRequestByID($poref);
		$inv_items = $this->store_return_receivers_model->getAllRequestItems($poref);
		$c = rand(100000, 9999999);
		foreach ($inv_items as $item) {
			$row = $this->siteprocurment->getProductByID($item->product_id);
			$row->return_id = $item->id;
			$row->expiry = (($item->expiry && $item->expiry != '0000-00-00') ? $this->sma->hrsd($item->expiry) : '');
			$row->mfg = (($item->mfg && $item->mfg != '0000-00-00') ? $this->sma->hrsd($item->mfg) : '');
			$row->batch_no = $item->batch_no;
			$row->available_qty = $item->available_qty;
						
			$row->base_quantity = $item->quantity;
			$row->base_unit = $row->unit ? $row->unit : $item->product_unit_id;
			$row->base_unit_cost = $row->cost ? $row->cost : $item->unit_cost;
			$row->unit = $item->product_unit_id;
			$row->qty = $item->unit_quantity;
			$row->oqty = $item->quantity;
			$row->supplier_part_no = $item->supplier_part_no;
			$row->received = $item->quantity_received ? $item->quantity_received : $item->quantity;
			$row->quantity_balance = $item->quantity_balance + ($item->quantity-$row->received);
			$row->discount = $item->discount ? $item->discount : '0';
			$options = $this->store_return_receivers_model->getProductOptions($row->id);
			$row->option = $item->option_id;
                $row->real_unit_cost = $item->real_unit_price;
                $row->cost = $this->sma->formatDecimal($item->net_unit_price + ($item->item_discount / $item->quantity));
                $row->tax_rate = $item->tax_rate_id;
				 $row->tax_method = $item->item_tax_method;
                unset($row->details, $row->product_details, $row->price, $row->file, $row->product_group_id);
                $units = $this->siteprocurment->getUnitsByBUID($row->base_unit);
                $tax_rate = $this->siteprocurment->getTaxRateByID($row->tax_rate);
				$ri = $this->Settings->item_addition ? $row->return_id : $row->return_id;
				$pr[] = array('id' => $c, 'item_id' => $row->return_id, 'label' => $row->name . " (" . $row->code . ")",
				'row' => $row, 'tax_rate' => $row->tax_rate, 'units' => $units, 'options' => $options);
			$c++;
		}
		$data['store_return_receiversitem'] = $pr;
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
		$data = $this->store_return_receivers_model->getSupplierdetails($supplier_id);
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

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('store_return_receivers')));
        $meta = array('page_title' => lang('store_return_receivers'), 'bc' => $bc);
        $this->page_construct('procurment/store_return_receivers/index', $meta, $this->data);

    }

    
	
	public function getStore_return_receivers($warehouse_id = null){ 
		$view_link = '<a href="'.admin_url('procurment/store_return_receivers/view/$1').'" data-toggle="modal" data-target="#myModal"><i class="fa fa-edit"></i>'.lang('view_store_return_receiver').'</a>';
        $edit_link = anchor('admin/procurment/store_return_receivers/edit/$1', '<i class="fa fa-edit"></i> ' . lang('edit_store_return_receiver'));
        $delete_link = "<a href='#' class='po' title='<b>" . $this->lang->line("delete_quotation") . "</b>' data-content=\"<p>"
        . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('procurment/store_return_receivers/delete/$1') . "'>"
        . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i> "
        . lang('delete_store_receivers') . "</a>";
       
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
	    ->select("pro_store_return_receivers.id, pro_store_return_receivers.date, pro_store_return_receivers.reference_no, pro_store_return_receivers.req_reference_no as ref, f.name as from_name, t.name as to_name, pro_store_return_receivers.total_no_qty as return_qty, pro_store_return_receivers.status")
            ->from('pro_store_return_receivers')
	    
	    ->join('warehouses f', 'f.id = pro_store_return_receivers.from_store', 'left')
	    ->join('warehouses t', 't.id = pro_store_return_receivers.to_store', 'left')
		->where('pro_store_return_receivers.store_id',$this->store_id);		
		$this->datatables->group_by('pro_store_return_receivers.id');
        $this->datatables->add_column("Actions", $action, "pro_store_return_receivers.id");
		echo      $this->datatables->generate();
	
    }

    /* ----------------------------------------------------------------------------- */

    public function view($store_return_receivers_id = null) {
        if ($this->input->get('id')) {
         $store_return_receivers_id = $this->input->get('id');
        }
         $this->data['store_rec']=$po= $this->store_return_receivers_model->getStore_return_receiversByID($store_return_receivers_id);
        $inv_items = $this->store_return_receivers_model->getStore_return_Items($store_return_receivers_id);
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
			
           $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('procurment/store_return_receivers'), 'page' => lang('store_return_receivers')), array('link' => '#', 'page' => lang('view')));
           $meta = array('page_title' => lang('view_store_return_receivers_details'), 'bc' => $bc);
         $this->load->view($this->theme.'procurment/store_return_receivers/view', $this->data);

    }
   public function  getstore_receiver_no(){
	   	$store_id = $this->input->get('store_id');
	    $data = $this->store_return_receivers_model->get_receiver_list($store_id);
     	$this->sma->send_json($data);
   }
   function get_store_receivers_data(){
		$store_id = $this->input->get('store_id');
		$store_receivers_id =  $this->input->get('store_receivers_id');
		$store_receivers = $this->store_return_receivers_model->get_store_receivers_by_id($store_receivers_id);
		$inv_items = $this->store_return_receivers_model->getAllStore_receiversItems($store_receivers_id);   
            krsort($inv_items);
            $c = rand(100000, 9999999);
            foreach ($inv_items as $item) {
              $row             = $this->siteprocurment->getItemByID($item->product_id);
			  $row->tax_method     = $row->tax_method;
			  $row->variant_id     = $item->variant_id;
			  $row->category_id    = $row->category_id;
			  $row->subcategory_id = $row->subcategory_id;
			  $row->brand_id       = $row->brand;
			  $batches             = $this->store_return_receivers_model->getReceiversStockData($item->id);
			  $row->request_qty    = $item->request_qty;
			  $row->received_qty   = $item->received_qty;
			  $row->batches = $batches;
			 
			  $unique_item_id = $this->store_id.$item->product_id.$item->batch;
			  $ri = $row->id;
			  $options = array();
			  $pr[$unique_item_id] = array('unique_id'=>$unique_item_id,'id' => $row->id,'store_receiveItemid'=>$item->id, 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")",
				'row' => $row,  'options' => $options);
			
            }
			$data->req_items = $pr;
			$data->date = $store_receivers->date;
			$this->sma->send_json($data);
    }
	
	
	
 public function add($store_return_receivers_id = null){
        $this->form_validation->set_rules('from_store_id', $this->lang->line("from_store_id"), 'required');
		$this->form_validation->set_rules('receiver_id', $this->lang->line("receiver_number"), 'required');
        $this->session->unset_userdata('csrf_token');
        if ($this->form_validation->run() == true) { 
		    $store_receivers = $this->store_return_receivers_model->get_store_receivers_by_id($_POST["receiver_id"]);
			
		    $date = date('Y-m-d H:i:s');          
			$n = $this->siteprocurment->lastidpro_store_return_receivers();
			$n=($n !=0)?$n+1:$this->store_id .'1';
			$reference = 'SRRE'.str_pad($n , 8, 0, STR_PAD_LEFT);			
            $i = count($_POST['product_id']);
			$products = array();
			for($r = 0; $r < $i; $r++){
			$total_t_qty = 0;$total_r_qty=0;
				$products[$r] = array(
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
			    'received_qty' => $row['received_qty'],
			    'return_qty' => $row['return_qty'],
			    'batch'          => $row['batch_no'],
			    'vendor_id'      => $row['vendor_id'],
			    'expiry'         => $row['expiry'],
			    'cost_price'     => $row['cost_price'],
			    'selling_price'  => $row['selling_price'],
			    'landing_cost'   => $row['landing_cost'],
			    'unit_price'     => $row['selling_price'],
			    'net_unit_price' => $row['selling_price']*$row['return_qty'],
			    'tax'            => $row['tax'],
			    'tax_method'     => $row['tax_method'],
			    'gross_amount'   => $row['gross'],
			    'tax_amount'     => $row['tax_amount'],
			    'net_amount'     => $row['product_grand_total'],
			    'store_id'       => $this->store_id,
				 'invoice_id'    => $row['invoice_id'],
				'category_id'    => $_POST['category_id'][$r],
				'subcategory_id' => $_POST['subcategory_id'][$r],  
				'brand_id'       => $_POST['brand_id'][$r], 
				'variant_id'     => $_POST['variant_id'][$r],
				'stock_id'       => $row['stock_id'],
				'return_unit_qty'=> $row['return_qty'],
				'return_type'    => $row['r_type'],   
			);
			$total_t_qty +=$row['received_qty'];
			$total_r_qty +=$row['return_qty'];
		    }
		    $products[$r]['received_qty'] = $total_t_qty;
		    $products[$r]['return_qty'] = $total_r_qty;
			}
            if (empty($products)) {
                $this->form_validation->set_rules('product', lang("order_items"), 'required');
            } else {
                
                krsort($products);
            }
            /*update common_store_return*/
			   
            $data = array(
			   'date'=>$date,
			   'reference_no'=>$reference,
			   'from_store'=>$this->store_id,
			   'to_store'=>$this->input->post('from_store_id'),
			   'store_id'=>$this->store_id,
			   'store_indent_id'=>($store_receivers->store_indent_id)?$store_receivers->store_indent_id:0,
			   'store_indent_date'=>($store_receivers->store_indent_date)?$store_receivers->store_indent_date:0,
			   'req_reference_no'=>($store_receivers->req_reference_no)?$store_receivers->req_reference_no:0,
			   'store_receiver_id'=>($store_receivers->id)?$store_receivers->id:0,
			   'store_receiver_date'=>($store_receivers->date)?$store_receivers->date:0,
			   'store_receiver_refno'=>($store_receivers->reference_no)?$store_receivers->reference_no:0,
               'intend_request_id' =>($store_receivers->intend_request_id)?$store_receivers->intend_request_id:0,
		       'intend_request_date' =>($store_receivers->intend_request_date)?$store_receivers->intend_request_date:0,
		       'total_no_items'=>$this->input->post('total_no_items'),
		       'total_no_qty'=>$this->input->post('total_no_qty'),
		       'status' =>$this->input->post('status'),
            );
			 if($data['status']=='process'){
				$data['processed_by'] = $this->session->userdata('user_id');
				$data['processed_on']=date('Y-m-d H:i:s');
	    }else{
				$data['approved_by'] = $this->session->userdata('user_id');
				$data['approved_on']=date('Y-m-d H:i:s');
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
		/* 	  print_r($data);
			print_r('<pre>');
			print_r($products);
			die; */  
        }
        if ($this->form_validation->run() == true && $this->store_return_receivers_model->addStore_return_receivers($data, $products)) {
            $this->session->set_userdata('remove_pols', 1);
            $this->session->set_flashdata('message', $this->lang->line("store_return_receivers_added"));
            admin_redirect('procurment/store_return_receivers');
        } else {
			
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['suppliers']  = $this->siteprocurment->getAllCompanies('supplier');
            $this->data['categories'] = $this->siteprocurment->getAllCategories();
            $this->data['tax_rates']  = $this->siteprocurment->getAllTaxRates();
            $this->data['warehouses'] = $this->siteprocurment->getAllWarehouses();
		    $this->data['stores']     = $this->siteprocurment->getAllWarehouses_Storeslist();
            $this->load->helper('string');
            $value = random_string('alnum', 20);
            $this->session->set_userdata('user_csrf', $value);
            $this->data['csrf'] = $this->session->userdata('user_csrf');
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('procurment/store_return_receivers'), 'page' => lang('store_return_receivers')), array('link' => '#', 'page' => lang('add_store_return_receivers')));
            $meta = array('page_title' => lang('add_store_return_receivers'), 'bc' => $bc);
            $this->page_construct('procurment/store_return_receivers/add', $meta, $this->data);
        }
    }



    public function edit($id = null){
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $srr = $this->store_return_receivers_model->getStore_return_receiversByID($id);
         if ( $srr->status == 'completed' ||$srr->status == 'approved') {
			$this->session->set_flashdata('error', lang("Do not allowed edit option"));
			admin_redirect("procurment/store_return_receivers");
		} 
		$this->form_validation->set_rules('receiver_id', $this->lang->line("receiver_number"), 'required');
        $this->session->unset_userdata('csrf_token');
        if ($this->form_validation->run() == true) {
            $i = count($_POST['product_id']);
			$products = array();
			for($r = 0; $r < $i; $r++){
			$total_t_qty = 0;$total_r_qty=0;
				$products[$r] = array(
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
			    'received_qty' => $row['received_qty'],
			    'return_qty' => $row['return_qty'],
			    'batch'          => $row['batch_no'],
			    'vendor_id'      => $row['vendor_id'],
			    'expiry'         => $row['expiry'],
			    'cost_price'     => $row['cost_price'],
			    'selling_price'  => $row['selling_price'],
			    'landing_cost'   => $row['landing_cost'],
			    'unit_price'     => $row['selling_price'],
			    'net_unit_price' => $row['selling_price']*$row['return_qty'],
			    'tax'            => $row['tax'],
			    'tax_method'     => $row['tax_method'],
			    'gross_amount'   => $row['gross'],
			    'tax_amount'     => $row['tax_amount'],
			    'net_amount'     => $row['product_grand_total'],
			    'store_id'       => $this->store_id,
				 'invoice_id'    => $row['invoice_id'],
				'category_id'    => $_POST['category_id'][$r],
				'subcategory_id' => $_POST['subcategory_id'][$r],  
				'brand_id'       => $_POST['brand_id'][$r], 
				'variant_id'     => $_POST['variant_id'][$r],
				'stock_id'       => $row['stock_id'],
				'return_unit_qty'=> $row['return_qty'],
				'return_type'    => $row['r_type'],   
			);
			$total_t_qty +=$row['received_qty'];
			$total_r_qty +=$row['return_qty'];
		    }
		    $products[$r]['received_qty'] = $total_t_qty;
		    $products[$r]['return_qty'] = $total_r_qty;
			}
            if (empty($products)) {
                $this->form_validation->set_rules('product', lang("order_items"), 'required');
            } else {
                
                krsort($products);
            }
            /*update common_store_return*/
			   
            $data = array(
		       'total_no_items'=>$this->input->post('total_no_items'),
		       'total_no_qty'=>$this->input->post('total_no_qty'),
		       'status' =>$this->input->post('status'),
            );
			 if($data['status']=='process'){
				$data['processed_by'] = $this->session->userdata('user_id');
				$data['processed_on']=date('Y-m-d H:i:s');
	         }else{
				$data['approved_by'] = $this->session->userdata('user_id');
				$data['approved_on']=date('Y-m-d H:i:s');
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
			/*   print_r($data);
			print_r('<pre>');
			print_r($products);
			die;  */
           
        }

        if ($this->form_validation->run() == true && $this->store_return_receivers_model->updateStore_return_receivers($id, $data, $products)) {             
            $this->session->set_userdata('remove_pols', 1);
            $this->session->set_flashdata('message', $this->lang->line("store_return_receivers_added"));
            admin_redirect('procurment/store_return_receivers');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['srr'] = $srr;
            $store_transfer_items = $this->store_return_receivers_model->getstore_return_receiver_Items($id);
			$pr = array();
			foreach ($store_transfer_items as $item) {
			$row = $this->siteprocurment->getItemByID($item->product_id);
			//$row->available_qty       = $this->siteprocurment->getAvailableQty($item->product_id);
			$row->request_qty         = $item->request_qty;
			$row->qty                 = $item->transfer_qty;
			$row->transfer_qty        = $item->transfer_qty;
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
			$p_ids                    = array($item->product_id);$s_ids =  array($this->store_id); 
			$batches                  =  $this->store_return_receivers_model->getstore_return_receiver_StockData($item->id);
			$row->batches             = $batches;
			$units = $this->siteprocurment->getUnitsByBUID($row->base_unit);
			$unique_item_id = $this->store_id.$item->product_id.$item->batch;
			$ri = $row->id;
			$options = array();
			$pr[$ri] = array('unique_id'=>$unique_item_id,'id' => $row->id, 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")",
				'row' => $row,  'options' => $options,'units'=>$units);
            }

            $this->data['srr_items'] = json_encode($pr);
            $this->data['id'] = $id;
            $this->data['suppliers']  = $this->siteprocurment->getAllCompanies('supplier');
            $this->data['categories'] = $this->siteprocurment->getAllCategories();
            $this->data['tax_rates']  = $this->siteprocurment->getAllTaxRates();
            $this->data['warehouses'] = $this->siteprocurment->getAllWarehouses();
		    $this->data['stores']     = $this->siteprocurment->getAllWarehouses_Storeslist();
            $this->load->helper('string');
            $value = random_string('alnum', 20);
            $this->session->set_userdata('user_csrf', $value);
            $this->session->set_userdata('remove_pols', 1);
            $this->data['csrf'] = $this->session->userdata('user_csrf');
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('procurment/store_return_receivers'), 'page' => lang('store_return_receivers')), array('link' => '#', 'page' => lang('edit_store_return_receivers')));
            $meta = array('page_title' => lang('edit_store_return_receivers'), 'bc' => $bc);
          
            $this->page_construct('procurment/store_return_receivers/edit', $meta, $this->data);
        }
    }

    /* -------------------------------------------------------------------------------------------------------------------------------- */

    public function add_bk($store_return_receivers_id = null){
        $this->form_validation->set_rules('warehouse', $this->lang->line("warehouse"), 'required|is_natural_no_zero');
		$this->form_validation->set_rules('requestnumber', $this->lang->line("requestnumber"), 'required');
        $this->session->unset_userdata('csrf_token');
        if ($this->form_validation->run() == true) { 
			$reference = 'STORERTNREC'.date('YmdHis');            
			$date = date('Y-m-d H:i:s');
            $warehouse_id = $this->input->post('warehouse');
            $supplier_id = $this->input->post('supplier');
            $status = $this->input->post('status');
            $shipping = $this->input->post('shipping') ? $this->input->post('shipping') : 0;
            $supplier_details = $this->siteprocurment->getCompanyByID($supplier_id);
            $supplier = $supplier_details->company != '-'  ? $supplier_details->company : $supplier_details->name;
            $note = $this->sma->clear_tags($this->input->post('note'));
            $total = 0;
            $product_tax = 0;
            $product_discount = 0;
            $i = sizeof($_POST['product']);
            $gst_data = [];
            $total_cgst = $total_sgst = $total_igst = 0;
            for ($r = 0; $r < $i; $r++) {
                $item_code = $_POST['product'][$r];
                $item_net_cost = $this->sma->formatDecimal($_POST['net_cost'][$r]);
                $unit_cost = $this->sma->formatDecimal($_POST['unit_cost'][$r]);
				$unit_cost_new = $this->sma->formatDecimal($_POST['unit_cost'][$r]);
                $real_unit_cost = $this->sma->formatDecimal($_POST['real_unit_cost'][$r]);
                $item_unit_quantity = $_POST['quantity'][$r];
				
				 $item_available_quantity = $_POST['available_qty'][$r];
				 $item_transfer_quantity = $_POST['transfer_qty'][$r];
				 $item_pending_quantity = $_POST['pending_qty'][$r];
				
				if($item_pending_quantity == 0){
					$check_quantity[] = 0;
				}else{
					$check_quantity[] = 1;
				}

                $item_option = isset($_POST['product_option'][$r]) && !empty($_POST['product_option'][$r]) && $_POST['product_option'][$r] != 'false' ? $_POST['product_option'][$r] : null;               
                $item_tax_rate = isset($_POST['product_tax'][$r]) ? $_POST['product_tax'][$r] : null;
                $item_discount = isset($_POST['product_discount'][$r]) ? $_POST['product_discount'][$r] : null;
				$item_tax_method = isset($_POST['tax1'][$r]) ? $_POST['tax1'][$r] : null;
                // $item_expiry = (isset($_POST['expiry'][$r]) && !empty($_POST['expiry'][$r])) ? $this->sma->fsd($_POST['expiry'][$r]) : null;
                $supplier_part_no = (isset($_POST['part_no'][$r]) && !empty($_POST['part_no'][$r])) ? $_POST['part_no'][$r] : null;
                $item_unit = $_POST['product_unit'][$r];
                $item_quantity = $_POST['quantity'][$r];
				$item_batch_no = $_POST['batch_no'][$r];
				$item_available_qty = $_POST['available_qty'][$r];
                if (isset($item_code) && isset($item_quantity)) {
                    $product_details = $this->store_return_receivers_model->getProductByCode($item_code);
					if($this->input->post('request_type') == 'new'){
						$from_store = $this->store_return_receivers_model->getStoreMasterProductID($product_details->id, $this->input->post('to_store_id'));
						$to_current_qty = $this->store_return_receivers_model->getCurrentQuantityID($product_details->id, $this->input->post('from_store_id'));
						$from_current_qty = $this->store_return_receivers_model->getCurrentQuantityID($product_details->id, $this->input->post('to_store_id'));
						$transfer_quantity[$product_details->id] = $item_transfer_quantity;
						$sum = 0;
						foreach($from_store as $from_store_row){
						if($transfer_quantity[$product_details->id] > 0 ){
							$transfer_quantity[$product_details->id] = $from_store_row->quantity - $transfer_quantity[$product_details->id];
							
							if($transfer_quantity[$product_details->id] < 0 ){
								$from_qty = $from_store_row->quantity;
								$to_qty = $from_store_row->quantity;
								$sum+= $to_qty;
								if($to_current_qty > 0){
									$tcurrent_quantity = $to_current_qty + $to_qty;
								}else{
									$tcurrent_quantity = $sum;
								}
								
								$stock[] = array('transacton_type' => 'IN', 'product_id' => $from_store_row->product_id, 'current_quantity' => $tcurrent_quantity, 'store_id' => $this->input->post('from_store_id'), 'type' => 'transfer', 'quantity' => $to_qty, 'purchase_invoice_id' => $from_store_row->purchase_invoice_id, 'purchase_batch_no' => $from_store_row->purchase_batch_no, 'created_on' => date('Y-m-d H:i:s'), 'created_by' =>  $this->session->userdata('user_id'));
								
								if($from_current_qty > 0){
									$fcurrent_quantity = $from_current_qty - $from_qty;
								}else{
									$fcurrent_quantity = 0;
								}
								
								$stock[] = array('transacton_type' => 'OUT', 'product_id' => $from_store_row->product_id,  'current_quantity' => $fcurrent_quantity, 'store_id' => $this->input->post('to_store_id'), 'type' => 'transfer', 'quantity' => $from_qty, 'purchase_invoice_id' => $from_store_row->purchase_invoice_id, 'purchase_batch_no' => $from_store_row->purchase_batch_no, 'created_on' => date('Y-m-d H:i:s'), 'created_by' =>  $this->session->userdata('user_id'));
								
								$stockupdate[$from_store_row->id] =  array('status' => 1);
								
								$transfer_quantity[$product_details->id] = abs($transfer_quantity[$product_details->id]);
							}else{
																
								$from_qty = $from_store_row->quantity - $transfer_quantity[$product_details->id];
								$to_qty = $from_store_row->quantity - $transfer_quantity[$product_details->id];
								$sum+= $to_qty;
								if($to_current_qty > 0){
									$tcurrent_quantity = $to_current_qty + $to_qty;
								}else{
									$tcurrent_quantity = $sum;
								}
								
								$stock[] = array('transacton_type' => 'IN', 'product_id' => $from_store_row->product_id, 'current_quantity' => $tcurrent_quantity, 'store_id' => $this->input->post('from_store_id'), 'type' => 'transfer', 'quantity' => $to_qty, 'purchase_invoice_id' => $from_store_row->purchase_invoice_id, 'purchase_batch_no' => $from_store_row->purchase_batch_no, 'created_on' => date('Y-m-d H:i:s'), 'created_by' =>  $this->session->userdata('user_id'));
								
								if($from_current_qty > 0){
									$fcurrent_quantity = $from_current_qty - $from_qty;
								}else{
									$fcurrent_quantity = 0;
								}
								
								$stock[] = array('transacton_type' => 'OUT', 'product_id' => $from_store_row->product_id, 'current_quantity' => $fcurrent_quantity, 'store_id' => $this->input->post('to_store_id'), 'type' => 'transfer', 'quantity' => $from_qty, 'purchase_invoice_id' => $from_store_row->purchase_invoice_id, 'purchase_batch_no' => $from_store_row->purchase_batch_no, 'created_on' => date('Y-m-d H:i:s'), 'created_by' =>  $this->session->userdata('user_id'));
								
								$stockupdate[$from_store_row->id] =  array('status' => 1);
								
								break; return;	
							}
							
						}
						
					}
					}elseif($this->input->post('request_type') == 'return'){
						
						$from_store = $this->store_return_receivers_model->getStoreMasterProductID($product_details->id, $this->input->post('from_store_id'));
						$to_current_qty = $this->store_return_receivers_model->getCurrentQuantityID($product_details->id, $this->input->post('to_store_id'));
						$from_current_qty = $this->store_return_receivers_model->getCurrentQuantityID($product_details->id, $this->input->post('from_store_id'));
						
						$transfer_quantity[$product_details->id] = $item_transfer_quantity;
						
						$sum = 0;
						foreach($from_store as $from_store_row){
							
							
							if($transfer_quantity[$product_details->id] > 0 ){
								
								$transfer_quantity[$product_details->id] = $from_store_row->quantity - $transfer_quantity[$product_details->id];
								
								if($transfer_quantity[$product_details->id] < 0 ){
									$from_qty = $from_store_row->quantity;
									$to_qty = $from_store_row->quantity;
									$sum+= $to_qty;
									if($to_current_qty > 0){
										$tcurrent_quantity = $to_current_qty + $to_qty;
									}else{
										$tcurrent_quantity = $sum;
									}
									
									$stock[] = array('transacton_type' => 'IN', 'product_id' => $from_store_row->product_id, 'current_quantity' => $tcurrent_quantity, 'store_id' => $this->input->post('to_store_id'), 'type' => 'return', 'quantity' => $to_qty, 'purchase_invoice_id' => $from_store_row->purchase_invoice_id, 'purchase_batch_no' => $from_store_row->purchase_batch_no, 'created_on' => date('Y-m-d H:i:s'), 'created_by' =>  $this->session->userdata('user_id'));
									
									if($from_current_qty > 0){
										$fcurrent_quantity = $from_current_qty - $from_qty;
									}else{
										$fcurrent_quantity = 0;
									}
									
									$stock[] = array('transacton_type' => 'OUT', 'product_id' => $from_store_row->product_id,  'current_quantity' => $fcurrent_quantity, 'store_id' => $this->input->post('from_store_id'), 'type' => 'return', 'quantity' => $from_qty, 'purchase_invoice_id' => $from_store_row->purchase_invoice_id, 'purchase_batch_no' => $from_store_row->purchase_batch_no, 'created_on' => date('Y-m-d H:i:s'), 'created_by' =>  $this->session->userdata('user_id'));
									
									$stockupdate[$from_store_row->id] =  array('status' => 1);
									
									$transfer_quantity[$product_details->id] = abs($transfer_quantity[$product_details->id]);
								}else{
																	
									$from_qty = $from_store_row->quantity - $transfer_quantity[$product_details->id];
									$to_qty = $from_store_row->quantity - $transfer_quantity[$product_details->id];
									$sum+= $to_qty;
									if($to_current_qty > 0){
										$tcurrent_quantity = $to_current_qty + $to_qty;
									}else{
										$tcurrent_quantity = $sum;
									}
									
									$stock[] = array('transacton_type' => 'IN', 'product_id' => $from_store_row->product_id, 'current_quantity' => $tcurrent_quantity, 'store_id' => $this->input->post('to_store_id'), 'type' => 'return', 'quantity' => $to_qty, 'purchase_invoice_id' => $from_store_row->purchase_invoice_id, 'purchase_batch_no' => $from_store_row->purchase_batch_no, 'created_on' => date('Y-m-d H:i:s'), 'created_by' =>  $this->session->userdata('user_id'));
									
									if($from_current_qty > 0){
										$fcurrent_quantity = $from_current_qty - $from_qty;
									}else{
										$fcurrent_quantity = 0;
									}
									
									$stock[] = array('transacton_type' => 'OUT', 'product_id' => $from_store_row->product_id, 'current_quantity' => $fcurrent_quantity, 'store_id' => $this->input->post('from_store_id'), 'type' => 'return', 'quantity' => $from_qty, 'purchase_invoice_id' => $from_store_row->purchase_invoice_id, 'purchase_batch_no' => $from_store_row->purchase_batch_no, 'created_on' => date('Y-m-d H:i:s'), 'created_by' =>  $this->session->userdata('user_id'));
									
									$stockupdate[$from_store_row->id] =  array('status' => 1);
									
									break; return;	
								}
								
							}
							
						}
					}
					
					
                    // if ($item_expiry) {
                    //     $today = date('Y-m-d');
                    //     if ($item_expiry <= $today) {
                    //         $this->session->set_flashdata('error', lang('product_expiry_date_issue') . ' (' . $product_details->name . ')');
                    //         redirect($_SERVER["HTTP_REFERER"]);
                    //     }
                    // }
                    // $unit_cost = $real_unit_cost;
                    $pr_discount = $this->siteprocurment->calculateDiscount($item_discount, $unit_cost);
                    $unit_cost = $this->sma->formatDecimal($unit_cost - $pr_discount);
					
                    $item_net_cost = $unit_cost;
                    $pr_item_discount = $this->sma->formatDecimal($pr_discount * $item_unit_quantity);
                    $product_discount += $pr_item_discount;
                    $pr_item_tax = $item_tax = 0;
                    $tax = "";

                    if (isset($item_tax_rate) && $item_tax_rate != 0) {

                        $tax_details = $this->siteprocurment->getTaxRateByID($item_tax_rate);
                        $ctax = $this->siteprocurment->calculateTax($product_details, $tax_details, $unit_cost);
                        $item_tax = $ctax['amount'];
                        $tax = $ctax['tax'];
                        if ($product_details->tax_method != 1) {
                            $item_net_cost = $unit_cost - $item_tax;
                        }
                        $pr_item_tax = $this->sma->formatDecimal($item_tax * $item_unit_quantity, 4);
                        if ($this->Settings->indian_gst && $gst_data = $this->gst->calculteIndianGST($pr_item_tax, ($this->Settings->state == $supplier_details->state), $tax_details)) {
                            $total_cgst += $gst_data['cgst'];
                            $total_sgst += $gst_data['sgst'];
                            $total_igst += $gst_data['igst'];
                        }
                    }

                    $product_tax += $pr_item_tax;
                    $subtotal = (($item_net_cost * $item_unit_quantity) + $pr_item_tax);
                    $unit = $this->siteprocurment->getUnitByID($item_unit);
/*common_store_return_receiver_items*/
                    $product = array(
                        'product_id' => $product_details->id,
						'batch_no' => $item_batch_no,
						'available_qty' => $item_available_qty,
                        'product_code' => $item_code,
                        'unit_price' => $unit_cost_new,
                        'product_name' => $product_details->name,
                        'option_id' => null,
                         'net_unit_price' => $item_net_cost,
                        'real_unit_price' => $unit_cost_new,
                        'quantity' => $item_quantity,
                        'product_unit_id' => $item_unit,
                        'product_unit_code' => $unit->code,
                        'unit_quantity' => $item_unit_quantity,                       
                        'warehouse_id' => $warehouse_id,
                        'item_tax' => $pr_item_tax,                        
                        'tax' => $tax,
						'tax_rate_id' => $item_tax_rate,
                        'discount' => $item_discount,
                        'item_discount' => $pr_item_discount,
                        'subtotal' => $this->sma->formatDecimal($subtotal),                        
                         //'real_unit_price' => $real_unit_cost,                      
                        // 'status' => $status,                        
                    );

                    $products[] = $product;
                    // echo "<pre>";
                    // print_r($this->input->post());
                    // echo "</pre>";
                    // echo "<pre>";
                    // print_r($products);exit;
                    // echo "</pre>";
                    $total += $this->sma->formatDecimal(($item_net_cost * $item_unit_quantity), 4);
                }
            }
			
            if (empty($products)) {
                $this->form_validation->set_rules('product', lang("order_items"), 'required');
            } else {
                krsort($products);
            }

            $order_discount = $this->siteprocurment->calculateDiscount($this->input->post('discount'), ($total + $product_tax));
            $total_discount = $this->sma->formatDecimal(($order_discount + $product_discount), 4);
            $order_tax = $this->siteprocurment->calculateOrderTax($this->input->post('order_tax'), ($total + $product_tax - $total_discount));
            $total_tax = $this->sma->formatDecimal(($product_tax + $order_tax), 4);
            $grand_total = $this->sma->formatDecimal(($total + $total_tax + $this->sma->formatDecimal($shipping) - $order_discount), 4);
			
			$join_ref_no = $this->store_return_receivers_model->getReqBYID($this->input->post('requestnumber'));
            
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
            $data = array('date' => $date,
			    'reference_no' => $reference,
				'request_type' => $this->input->post('request_type'),
                //'supplier_id' => $supplier_id,
               // 'supplier' => $supplier,
			    'from_store_id' => $this->input->post('from_store_id'),
				'to_store_id' => $this->input->post('to_store_id'),
                'warehouse_id' => $warehouse_id,
				//'invoice_no' => $this->input->post('invoice_no'),
				//'store_return_receivers_details' => $this->input->post('store_return_receivers_details') ? $this->input->post('store_return_receivers_details') : '',
				//'store_return_receivers_no' => $this->input->post('store_return_receivers_no'),
				//'store_return_receivers_date' => $this->input->post('store_return_receivers_date'),
				//'store_return_receivers_expected_date' => $this->input->post('store_return_receivers_expected_date'),
                'note' => $note,
                'total' => $total,
                'product_discount' => $product_discount,
                'order_discount_id' => $this->input->post('discount'),
                'order_discount' => $order_discount,
                'total_discount' => $total_discount,
                'product_tax' => $product_tax,
                'order_tax_id' => $this->input->post('order_tax'),
                'order_tax' => $order_tax,
                'total_tax' => $total_tax,
                'shipping' => $this->sma->formatDecimal($shipping),
                'grand_total' => $grand_total,
                'status' => $status ? $status : '',
                'created_by' => $this->session->userdata('user_id'),
				'approved_by' => $approved_by ? $approved_by : 0,
				'approved_on' => $date,
				'requestnumber' => $this->input->post('requestnumber'),
				'requestdate' => $join_ref_no->date,
				'req_reference_no' => $join_ref_no->reference_no
				
               //  'payment_term' => $payment_term ? $payment_term : 0 ,
                // 'due_date' => $due_date,
            );
            if ($this->Settings->indian_gst) {
                $data['cgst'] = $total_cgst;
                $data['sgst'] = $total_sgst;
                $data['igst'] = $total_igst;
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
			
			if($this->input->post('requestnumber') != ''){
				
				
				$store_return_receivers_array = array(
					'status' => 'completed',
				);
				
			}
			/*echo $this->input->post('request_type');
			echo '<pre>';
			print_r($stock);
			print_r($stockupdate);
			die;*/
           //$this->sma->print_arrays($data, $products, $store_return_receivers_array, $this->input->post('requestnumber'), $rstatus);
		   //die;
        }
		
		
		 
        if ($this->form_validation->run() == true && $this->store_return_receivers_model->addStore_return_receivers($data, $products, $store_return_receivers_array, $stock, $stockupdate, $this->input->post('requestnumber'))) {
            
            $this->session->set_userdata('remove_pols', 1);
            $this->session->set_flashdata('message', $this->lang->line("store_return_receivers_added"));
            admin_redirect('procurment/store_return_receivers');
        } else {
			
			
            if ($store_return_receivers_id) {
                $this->data['store_return_receivers'] = $this->store_return_receivers_model->getStore_return_receiversByID($store_return_receivers_id);
                $supplier_id = $this->data['store_return_receivers']->supplier_id;
                $items = $this->store_return_receivers_model->getAllStore_return_receiversItems($store_return_receivers_id);
                krsort($items);
                $c = rand(100000, 9999999);
                foreach ($items as $item) {
                    $row = $this->siteprocurment->getProductByID($item->product_id);
                    if ($row->type == 'combo') {
                        $combo_items = $this->siteprocurment->getProductComboItems($row->id, $item->warehouse_id);
                        foreach ($combo_items as $citem) {
                            $crow = $this->siteprocurment->getProductByID($citem->id);
                            if (!$crow) {
                                $crow = json_decode('{}');
                                $crow->qty = $item->quantity;
                            } else {
                                unset($crow->details, $crow->product_details, $crow->price);
                                $crow->qty = $citem->qty*$item->quantity;
                            }
                            $crow->base_quantity = $item->quantity;
                            $crow->base_unit = $crow->unit ? $crow->unit : $item->product_unit_id;
                            $crow->base_unit_cost = $crow->cost ? $crow->cost : $item->unit_cost;
                            $crow->unit = $item->product_unit_id;
                            $crow->discount = $item->discount ? $item->discount : '0';
                            $supplier_cost = $supplier_id ? $this->getSupplierCost($supplier_id, $crow) : $crow->cost;
                            $crow->cost = $supplier_cost ? $supplier_cost : 0;
                            $crow->tax_rate = $item->tax_rate_id;
                            $crow->real_unit_cost = $crow->cost ? $crow->cost : 0;
                            $crow->expiry = '';
                            $options = $this->store_return_receivers_model->getProductOptions($crow->id);
                            $units = $this->siteprocurment->getUnitsByBUID($row->base_unit);
                            $tax_rate = $this->siteprocurment->getTaxRateByID($crow->tax_rate);
                            $ri = $this->Settings->item_addition ? $crow->id : $c;

                            $pr[$ri] = array('id' => $c, 'item_id' => $crow->id, 'label' => $crow->name . " (" . $crow->code . ")", 'row' => $crow, 'tax_rate' => $tax_rate, 'units' => $units, 'options' => $options);
                            $c++;
                        }
                    } elseif ($row->type == 'standard') {
                        if (!$row) {
                            $row = json_decode('{}');
                            $row->quantity = 0;
                        } else {
                            unset($row->details, $row->product_details);
                        }

                        $row->id = $item->product_id;
                        $row->code = $item->product_code;
                        $row->name = $item->product_name;
                        $row->base_quantity = $item->quantity;
                        $row->base_unit = $row->unit ? $row->unit : $item->product_unit_id;
                        $row->base_unit_cost = $row->cost ? $row->cost : $item->unit_cost;
                        $row->unit = $item->product_unit_id;
                        $row->qty = $item->unit_quantity;
                        $row->option = $item->option_id;
                        $row->discount = $item->discount ? $item->discount : '0';
                        $supplier_cost = $supplier_id ? $this->getSupplierCost($supplier_id, $row) : $row->cost;
                        $row->cost = $supplier_cost ? $supplier_cost : 0;
                        $row->tax_rate = $item->tax_rate_id;
						$row->tax_method = $item->item_tax_method ? $item->item_tax_method : 0;
                        $row->expiry = '';
                        $row->real_unit_cost = $row->cost ? $row->cost : 0;
                        $options = $this->store_return_receivers_model->getProductOptions($row->id);

                        $units = $this->siteprocurment->getUnitsByBUID($row->base_unit);
                        $tax_rate = $this->siteprocurment->getTaxRateByID($row->tax_rate);
                        $ri = $this->Settings->item_addition ? $row->id : $row->id;

                        $pr[$ri] = array('id' => $c, 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")",
                            'row' => $row, 'tax_rate' => $row->tax_rate, 'units' => $units, 'options' => $options);
                        $c++;
                    }
                }
                $this->data['store_return_receivers_items'] = json_encode($pr);
            }

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['store_return_receivers_id'] = $store_return_receivers_id;
           $this->data['suppliers'] = $this->siteprocurment->getAllCompanies('supplier');
            $this->data['categories'] = $this->siteprocurment->getAllCategories();
           $this->data['tax_rates'] = $this->siteprocurment->getAllTaxRates();
            $this->data['warehouses'] = $this->siteprocurment->getAllWarehouses();
			$this->data['ref_requestnumber'] = $_GET['ref'];
			 $this->data['requestnumber'] = $this->siteprocurment->getAllSTORE_RETURN_NO();
			$this->data['stores'] = $this->siteprocurment->getAllStores();
           $this->data['ponumber'] = ''; //$this->siteprocurment->getReference('po');
            $this->load->helper('string');
            $value = random_string('alnum', 20);
            $this->session->set_userdata('user_csrf', $value);
            $this->data['csrf'] = $this->session->userdata('user_csrf');
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('procurment/store_return_receivers'), 'page' => lang('store_return_receivers')), array('link' => '#', 'page' => lang('add_store_return_receivers')));
            $meta = array('page_title' => lang('add_store_return_receivers'), 'bc' => $bc);
            $this->page_construct('procurment/store_return_receivers/add', $meta, $this->data);
        }
    }




    /* ------------------------------------------------------------------------------------- */

    public function edit_bk($id = null)
    {
        ////$this->sma->checkPermissions();

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
		
		
        $inv = $this->store_return_receivers_model->getStore_return_receiversByID($id);
        if ( $inv->status == 'completed') {
			$this->session->set_flashdata('error', lang("Do not allowed edit option"));
			admin_redirect("procurment/store_return_receivers");
		}
      
       $this->form_validation->set_rules('warehouse', $this->lang->line("warehouse"), 'required|is_natural_no_zero');
       
        $this->session->unset_userdata('csrf_token');
        if ($this->form_validation->run() == true) {

            $reference = $this->input->post('reference_no');
            if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld(trim($this->input->post('date')));
            } else {
                $date = $inv->date;
            }
            $warehouse_id = $this->input->post('warehouse');           
            $supplier_id = $this->input->post('supplier');
            $status = $this->input->post('status');
            $shipping = $this->input->post('shipping') ? $this->input->post('shipping') : 0;
            $supplier_details = $this->siteprocurment->getCompanyByID($supplier_id);
            $supplier = $supplier_details->company != '-'  ? $supplier_details->company : $supplier_details->name;
            $note = $this->sma->clear_tags($this->input->post('note'));
             $payment_term = $this->input->post('payment_term');
             $due_date = $payment_term ? date('Y-m-d', strtotime('+' . ' days', strtotime($date))) : null;

            $total = 0;
            $product_tax = 0;
            $product_discount = 0;
            $partial = false;
            $i = sizeof($_POST['product']);
            $gst_data = [];
            $total_cgst = $total_sgst = $total_igst = 0;
			
			$from_current_qty = 0;
			$to_current_qty = 0;
						
            for ($r = 0; $r < $i; $r++) {
                $item_code = $_POST['product'][$r];
                $item_net_cost = $this->sma->formatDecimal($_POST['net_cost'][$r]);
                $unit_cost = $this->sma->formatDecimal($_POST['unit_cost'][$r]);
				$unit_cost_new = $this->sma->formatDecimal($_POST['unit_cost'][$r]);
                $real_unit_cost = $this->sma->formatDecimal($_POST['real_unit_cost'][$r]);
                $item_unit_quantity = $_POST['quantity'][$r];
				
				$item_available_quantity = $_POST['available_qty'][$r];
				 $item_transfer_quantity = $_POST['transfer_qty'][$r];
				 $item_pending_quantity = $_POST['pending_qty'][$r];
				 
                // $quantity_received = $_POST['received_base_quantity'][$r];
                $item_option = isset($_POST['product_option'][$r]) && $_POST['product_option'][$r] != 'false' ? $_POST['product_option'][$r] : null;
                $item_tax_rate = isset($_POST['product_tax'][$r]) ? $_POST['product_tax'][$r] : null;
				$item_tax_method = isset($_POST['tax1'][$r]) ? $_POST['tax1'][$r] : 0;
                $item_discount = isset($_POST['product_discount'][$r]) ? $_POST['product_discount'][$r] : null;
				
                $item_unit = $_POST['product_unit'][$r];
                $item_quantity = $_POST['product_base_quantity'][$r];
				
				$item_batch_no = $_POST['batch_no'][$r];
				$item_available_qty = $_POST['available_qty'][$r];
                if (isset($item_code) && isset($item_quantity)) {
                    $product_details = $this->store_return_receivers_model->getProductByCode($item_code);
					
					if($inv->request_type == 'return'){
						
						$store = $this->store_return_receivers_model->getBatchProductID($product_details->id, $item_batch_no, $inv->from_store_id);
						
						if($from_current_qty == 0){
							$from_current_qty = $this->store_return_receivers_model->getCurrentQuantityID($product_details->id, $inv->from_store_id);
						}
						if($to_current_qty == 0){
							$to_current_qty = $this->store_return_receivers_model->getCurrentQuantityID($product_details->id, $inv->to_store_id);
						}
						if(!empty($inv->from_store_id)){
							
							
							if($from_current_qty > 0){
								
								$from_current_qty = $from_current_qty - $item_quantity;
							}else{
								$from_current_qty = $item_quantity;
							}
							
							$stockupdate[$inv->from_store_id] =  array('status' => 1);
							
							$stock[] = array('transacton_type' => 'OUT', 'product_id' => $product_details->id, 'current_quantity' => $from_current_qty, 'store_id' => $inv->to_store_id, 'type' => 'transfer', 'return_status' => 0,  'quantity' => $item_quantity, 'purchase_invoice_id' => $store->purchase_invoice_id, 'purchase_batch_no' => $store->purchase_batch_no, 'created_on' => date('Y-m-d H:i:s'), 'created_by' =>  $this->session->userdata('user_id'));
							
							
						}
						if($inv->to_store_id){
							if($to_current_qty > 0){
								$to_current_qty = $to_current_qty + $item_quantity;
							}else{
								$to_current_qty = $item_quantity;
							}
							
							$stock[] = array('transacton_type' => 'IN', 'product_id' => $product_details->id, 'current_quantity' => $to_current_qty, 'store_id' => $inv->to_store_id, 'type' => 'transfer', 'return_status' => 1, 'quantity' => $item_quantity, 'purchase_invoice_id' => $store->purchase_invoice_id, 'purchase_batch_no' => $store->purchase_batch_no, 'created_on' => date('Y-m-d H:i:s'), 'created_by' =>  $this->session->userdata('user_id'));
									
						}
						
					}
                    // $unit_cost = $real_unit_cost;
                    $pr_discount = $this->siteprocurment->calculateDiscount($item_discount, $unit_cost);
                    $unit_cost = $this->sma->formatDecimal($unit_cost - $pr_discount);
                    $item_net_cost = $unit_cost;
                    $pr_item_discount = $this->sma->formatDecimal($pr_discount * $item_unit_quantity);
                    $product_discount += $pr_item_discount;
                    $pr_item_tax = 0;
                    $item_tax = 0;
                    $tax = "";

                    if (isset($item_tax_rate) && $item_tax_rate != 0) {

                        $tax_details = $this->siteprocurment->getTaxRateByID($item_tax_rate);
                        $ctax = $this->siteprocurment->calculateTax($product_details, $tax_details, $unit_cost);
                        $item_tax = $ctax['amount'];
                        $tax = $ctax['tax'];
                        if ($product_details->tax_method != 1) {
                            $item_net_cost = $unit_cost - $item_tax;
                        }
                        $pr_item_tax = $this->sma->formatDecimal($item_tax * $item_unit_quantity, 4);
                        if ($this->Settings->indian_gst && $gst_data = $this->gst->calculteIndianGST($pr_item_tax, ($this->Settings->state == $supplier_details->state), $tax_details)) {
                            $total_cgst += $gst_data['cgst'];
                            $total_sgst += $gst_data['sgst'];
                            $total_igst += $gst_data['igst'];
                        }
                    }

                    $product_tax += $pr_item_tax;
                    $subtotal = (($item_net_cost * $item_unit_quantity) + $pr_item_tax);
                    $unit = $this->siteprocurment->getUnitByID($item_unit);
                    /*update common_store_return_receiver_items*/
                     $item = array(
                        'product_id' => $product_details->id,
						'batch_no' => $item_batch_no,
						'available_qty' => $item_available_qty,
                        'product_code' => $item_code,
                        'unit_price' => $unit_cost_new,
                        'product_name' => $product_details->name,
                        'option_id' => null,
                        'net_unit_price' => $item_net_cost,
                        'real_unit_price' => $unit_cost_new,
                        'quantity' => $item_quantity,
                        'product_unit_id' => $item_unit,
                        'product_unit_code' => $unit->code,
                        'unit_quantity' => $item_unit_quantity,                       
                        'warehouse_id' => $warehouse_id,
                        'item_tax' => $pr_item_tax,                        
                        'tax' => $tax,
						'tax_rate_id' => $item_tax_rate,
                        'discount' => $item_discount,
                        'item_discount' => $pr_item_discount,
                        'subtotal' => $this->sma->formatDecimal($subtotal),                          
                    );
                    $items[] = $item;
                    $total += $item_net_cost * $item_unit_quantity;
                }
            }

			$from_current_qty = $from_current_qty;
			$to_current_qty = $to_current_qty;
			
            if (empty($items)) {
                $this->form_validation->set_rules('product', lang("order_items"), 'required');
            } else {
                
                krsort($items);
            }

            $order_discount = $this->siteprocurment->calculateDiscount($this->input->post('discount'), ($total + $product_tax));
            $total_discount = $this->sma->formatDecimal(($order_discount + $product_discount), 4);
            $order_tax = $this->siteprocurment->calculateOrderTax($this->input->post('order_tax'), ($total + $product_tax - $total_discount));
            $total_tax = $this->sma->formatDecimal(($product_tax + $order_tax), 4);
            $grand_total = $this->sma->formatDecimal(($total + $total_tax + $this->sma->formatDecimal($shipping) - $order_discount), 4);
            /*update common_store_return_receivers*/
            $data = array(
                'warehouse_id' => $warehouse_id,
                'note' => $note,
                'total' => $total,
                'product_discount' => $product_discount,
                'order_discount_id' => $this->input->post('discount'),
                'order_discount' => $order_discount,
                'total_discount' => $total_discount,
                'product_tax' => $product_tax,
                'order_tax_id' => $this->input->post('order_tax'),
                'order_tax' => $order_tax,
                'total_tax' => $total_tax,
                'shipping' => $this->sma->formatDecimal($shipping),
                'grand_total' => $grand_total,
                'status' => $status ? $status : '',
                'updated_by' => $this->session->userdata('user_id'),
            );
           
            if ($this->Settings->indian_gst) {
                $data['cgst'] = $total_cgst;
                $data['sgst'] = $total_sgst;
                $data['igst'] = $total_igst;
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
        }

        if ($this->form_validation->run() == true && $this->store_return_receivers_model->updateStore_return_receivers($id, $data, $items, $stock, $stockupdate)) {             
            $this->session->set_userdata('remove_pols', 1);
            $this->session->set_flashdata('message', $this->lang->line("store_return_receivers_added"));
            admin_redirect('procurment/store_return_receivers');
        } else {
            
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['inv'] = $inv;
            if ($this->Settings->disable_editing) {
                if ($this->data['inv']->date <= date('Y-m-d', strtotime('-'.$this->Settings->disable_editing.' days'))) {
                    $this->session->set_flashdata('error', sprintf(lang("store_return_receivers_x_edited_older_than_x_days"), $this->Settings->disable_editing));
                    redirect($_SERVER["HTTP_REFERER"]);
                }
            }
            $inv_items = $this->store_return_receivers_model->getAllStore_return_receiversItems($id);   

             krsort($inv_items);
            $c = rand(100000, 9999999);
            foreach ($inv_items as $item) {
                 
				 $row->return_id = $item->id;
                $row = $this->siteprocurment->getProductByID($item->product_id);                   
                $row->base_quantity = $item->quantity;
                $row->base_unit = $row->unit ? $row->unit : $item->product_unit_id;
                $row->base_unit_cost = $row->cost ? $row->cost : $item->unit_price;
                $row->unit = $item->product_unit_id;
                $row->qty = $item->unit_quantity;
                $row->oqty = $item->quantity;
				
				//$current_quantity = $this->store_return_receivers_model->getAvailableQTY($item->product_id, $inv->to_store_id);
				
				//$pending_quantity = $this->store_return_receivers_model->checkPendingQTYEdit($item->product_id, $item->quantity, $inv->id);
			
				//$row->current_quantity = $current_quantity;
				//$row->pending_quantity = $pending_quantity;
				
				//$row->transfer_quantity = $item->transfer_quantity;
				
				$row->batch_no = $item->batch_no;
				$row->available_qty = $item->available_qty;
                // $row->supplier_part_no = $item->supplier_part_no;
                // $row->received = $item->quantity_received ? $item->quantity_received : $item->quantity;
                // $row->quantity_balance = $item->quantity_balance + ($item->quantity-$row->received);
                $row->discount = $item->discount ? $item->discount : '0';
                $options = $this->store_return_receivers_model->getProductOptions($row->id);

                $row->option = $item->option_id;
                $row->real_unit_cost = $item->real_unit_price;
                $row->cost = $this->sma->formatDecimal($item->net_unit_price + ($item->item_discount / $item->quantity));
                $row->tax_rate = $item->tax_rate_id;
				$row->tax_method = $item->item_tax_method ? $item->item_tax_method : 0;
                unset($row->details, $row->product_details, $row->price, $row->file, $row->product_group_id);
                $units = $this->siteprocurment->getUnitsByBUID($row->base_unit);
                $tax_rate = $this->siteprocurment->getTaxRateByID($row->tax_rate);
                $ri = $this->Settings->item_addition ? $row->return_id : $row->return_id;

                $pr[] = array('id' => $c, 'item_id' => $row->return_id, 'label' => $row->name . " (" . $row->code . ")",
                    'row' => $row, 'tax_rate' => $row->tax_rate, 'units' => $units, 'options' => $options);
                $c++;
            }

            $this->data['inv_items'] = json_encode($pr);
            $this->data['id'] = $id;
            $this->data['suppliers'] = $this->siteprocurment->getAllCompanies('supplier');
            $this->data['store_return_receivers'] = $this->store_return_receivers_model->getStore_return_receiversByID($id);
            $this->data['categories'] = $this->siteprocurment->getAllCategories();
            $this->data['tax_rates'] = $this->siteprocurment->getAllTaxRates();
            $this->data['warehouses'] = $this->siteprocurment->getAllWarehouses();
			$this->data['ref_requestnumber'] = $_GET['ref'];
			 $this->data['requestnumber'] = $this->siteprocurment->getAllSTORE_RETURN_NOedit();
			 $this->data['stores'] = $this->siteprocurment->getAllStores();
            $this->load->helper('string');
            $value = random_string('alnum', 20);
            $this->session->set_userdata('user_csrf', $value);
            $this->session->set_userdata('remove_pols', 1);
            $this->data['csrf'] = $this->session->userdata('user_csrf');
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('procurment/store_return_receivers'), 'page' => lang('store_return_receivers')), array('link' => '#', 'page' => lang('edit_store_return_receivers')));
            $meta = array('page_title' => lang('edit_store_return_receivers'), 'bc' => $bc);
          /*  echo "<pre>";
            print_r($this->data);exit;
            echo "</pre>";*/
            $this->page_construct('procurment/store_return_receivers/edit', $meta, $this->data);
        }
    }

    /* ----------------------------------------------------------------------------------------------------------- */

    public function store_return_receivers_by_csv()
    {
        //$this->sma->checkPermissions('csv');
        $this->load->helper('security');
        $this->form_validation->set_message('is_natural_no_zero', $this->lang->line("no_zero_required"));
        $this->form_validation->set_rules('warehouse', $this->lang->line("warehouse"), 'required|is_natural_no_zero');
        $this->form_validation->set_rules('supplier', $this->lang->line("supplier"), 'required');
        $this->form_validation->set_rules('userfile', $this->lang->line("upload_file"), 'xss_clean');

        if ($this->form_validation->run() == true) {
            $quantity = "quantity";
            $product = "product";
            $unit_cost = "unit_cost";
            $tax_rate = "tax_rate";
            $reference = $this->input->post('reference_no') ? $this->input->post('reference_no') : $this->siteprocurment->getReference('po');
            if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld(trim($this->input->post('date')));
            } else {
                $date = null;
            }
            $warehouse_id = $this->input->post('warehouse');
            $supplier_id = $this->input->post('supplier');
            $status = $this->input->post('status');
            $shipping = $this->input->post('shipping') ? $this->input->post('shipping') : 0;
            $supplier_details = $this->siteprocurment->getCompanyByID($supplier_id);
            $supplier = $supplier_details->company != '-'  ? $supplier_details->company : $supplier_details->name;
            $note = $this->sma->clear_tags($this->input->post('note'));

            $total = 0;
            $product_tax = 0;
            $product_discount = 0;
            $gst_data = [];
            $total_cgst = $total_sgst = $total_igst = 0;

            if (isset($_FILES["userfile"])) {

                $this->load->library('upload');

                $config['upload_path'] = $this->digital_upload_path;
                $config['allowed_types'] = 'csv';
                $config['max_size'] = $this->allowed_file_size;
                $config['overwrite'] = true;

                $this->upload->initialize($config);

                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    admin_redirect("procurment/store_return_receivers/store_return_receivers_by_csv");
                }

                $csv = $this->upload->file_name;

                $arrResult = array();
                $handle = fopen($this->digital_upload_path . $csv, "r");
                if ($handle) {
                    while (($row = fgetcsv($handle, 1000, ",")) !== false) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                $titles = array_shift($arrResult);

                $keys = array('code', 'net_unit_cost', 'quantity', 'variant', 'item_tax_rate', 'discount', 'expiry');
                $final = array();
                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }
                $rw = 2;
                foreach ($final as $csv_pr) {

                    if (isset($csv_pr['code']) && isset($csv_pr['net_unit_cost']) && isset($csv_pr['quantity'])) {

                        if ($product_details = $this->store_return_receivers_model->getProductByCode($csv_pr['code'])) {

                            if ($csv_pr['variant']) {
                                $item_option = $this->store_return_receivers_model->getProductVariantByName($csv_pr['variant'], $product_details->id);
                                if (!$item_option) {
                                    $this->session->set_flashdata('error', lang("pr_not_found") . " ( " . $product_details->name . " - " . $csv_pr['variant'] . " ). " . lang("line_no") . " " . $rw);
                                    redirect($_SERVER["HTTP_REFERER"]);
                                }
                            } else {
                                $item_option = json_decode('{}');
                                $item_option->id = null;
                            }

                            $item_code = $csv_pr['code'];
                            $item_net_cost = $this->sma->formatDecimal($csv_pr['net_unit_cost']);
                            $item_quantity = $csv_pr['quantity'];
                            $quantity_balance = $csv_pr['quantity'];
                            $item_tax_rate = $csv_pr['item_tax_rate'];
                            $item_discount = $csv_pr['discount'];
                            $item_expiry = isset($csv_pr['expiry']) ? $this->sma->fsd($csv_pr['expiry']) : null;

                            $pr_discount = $this->siteprocurment->calculateDiscount($item_discount, $item_net_cost);
                            $pr_item_discount = $this->sma->formatDecimal(($pr_discount * $item_quantity), 4);
                            $product_discount += $pr_item_discount;

                            $tax = "";
                            $pr_item_tax = 0;
                            $unit_cost = $item_net_cost - $pr_discount;
                            $gst_data = [];
                            $tax_details = ((isset($item_tax_rate) && !empty($item_tax_rate)) ? $this->store_return_receivers_model->getTaxRateByName($item_tax_rate) : $this->siteprocurment->getTaxRateByID($product_details->tax_rate));
                            if ($tax_details) {
                                $ctax = $this->siteprocurment->calculateTax($product_details, $tax_details, $unit_cost);
                                $item_tax = $ctax['amount'];
                                $tax = $ctax['tax'];
                                if ($product_details->tax_method != 1) {
                                    $item_net_cost = $unit_cost - $item_tax;
                                }
                                $pr_item_tax = $this->sma->formatDecimal($item_tax * $item_quantity, 4);
                                if ($this->Settings->indian_gst && $gst_data = $this->gst->calculteIndianGST($pr_item_tax, ($this->Settings->state == $supplier_details->state), $tax_details)) {
                                    $total_cgst += $gst_data['cgst'];
                                    $total_sgst += $gst_data['sgst'];
                                    $total_igst += $gst_data['igst'];
                                }
                            }

                            $product_tax += $pr_item_tax;
                            $subtotal = $this->sma->formatDecimal(((($item_net_cost * $item_quantity) + $pr_item_tax) - $pr_item_discount), 4);
                            $unit = $this->siteprocurment->getUnitByID($product_details->unit);
                            $product = array(
                                'product_id' => $product_details->id,
                                'product_code' => $item_code,
                                'product_name' => $product_details->name,
                                'option_id' => $item_option->id,
                                'net_unit_cost' => $item_net_cost,
                                'quantity' => $item_quantity,
                                'product_unit_id' => $product_details->unit,
                                'product_unit_code' => $unit->code,
                                'unit_quantity' => $item_quantity,
                                'quantity_balance' => $quantity_balance,
                                'warehouse_id' => $warehouse_id,
                                'item_tax' => $pr_item_tax,
                                'tax_rate_id' => $tax_details ? $tax_details->id : null,
                                'tax' => $tax,
                                'discount' => $item_discount,
                                'item_discount' => $pr_item_discount,
                                'expiry' => $item_expiry,
                                'subtotal' => $subtotal,
                                'date' => date('Y-m-d', strtotime($date)),
                                'status' => $status,
                                'unit_cost' => $this->sma->formatDecimal(($item_net_cost + $item_tax), 4),
                                'real_unit_cost' => $this->sma->formatDecimal(($item_net_cost + $item_tax + $pr_discount), 4),
                            );

                            $products[] = ($product+$gst_data);
                            $total += $this->sma->formatDecimal(($item_net_cost * $item_quantity), 4);

                        } else {
                            $this->session->set_flashdata('error', $this->lang->line("pr_not_found") . " ( " . $csv_pr['code'] . " ). " . $this->lang->line("line_no") . " " . $rw);
                            redirect($_SERVER["HTTP_REFERER"]);
                        }
                        $rw++;
                    }

                }
            }

            $order_discount = $this->siteprocurment->calculateDiscount($this->input->post('discount'), ($total + $product_tax));
            $total_discount = $this->sma->formatDecimal(($order_discount + $product_discount), 4);
            $order_tax = $this->siteprocurment->calculateOrderTax($this->input->post('order_tax'), ($total + $product_tax - $total_discount));
            $total_tax = $this->sma->formatDecimal(($product_tax + $order_tax), 4);
            $grand_total = $this->sma->formatDecimal(($total + $total_tax + $this->sma->formatDecimal($shipping) - $total_discount), 4);
            $data = array('reference_no' => $reference,
                'date' => $date,
                'supplier_id' => $supplier_id,
                'supplier' => $supplier,
                'warehouse_id' => $warehouse_id,
                'note' => $note,
                'total' => $total,
                'product_discount' => $product_discount,
                'order_discount_id' => $this->input->post('discount'),
                'order_discount' => $order_discount,
                'total_discount' => $total_discount,
                'product_tax' => $product_tax,
                'order_tax_id' => $this->input->post('order_tax'),
                'order_tax' => $order_tax,
                'total_tax' => $total_tax,
                'shipping' => $this->sma->formatDecimal($shipping),
                'grand_total' => $grand_total,
                'status' => $status,
                'created_by' => $this->session->userdata('username'),
            );
            if ($this->Settings->indian_gst) {
                $data['cgst'] = $total_cgst;
                $data['sgst'] = $total_sgst;
                $data['igst'] = $total_igst;
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

            // $this->sma->print_arrays($data, $products);
        }

        if ($this->form_validation->run() == true && $this->store_return_receivers_model->addPurchase($data, $products)) {

            $this->session->set_flashdata('message', $this->lang->line("store_return_receivers_added"));
            admin_redirect("procurment/store_return_receivers");
        } else {

            $data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['warehouses'] = $this->siteprocurment->getAllWarehouses();
            $this->data['tax_rates'] = $this->siteprocurment->getAllTaxRates();
            $this->data['ponumber'] = ''; // $this->siteprocurment->getReference('po');

            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('procurment/store_return_receivers'), 'page' => lang('store_return_receivers')), array('link' => '#', 'page' => lang('add_store_return_receivers_by_csv')));
            $meta = array('page_title' => lang('add_store_return_receivers_by_csv'), 'bc' => $bc);
            $this->page_construct('procurment/store_return_receivers/store_return_receivers__orderby_csv', $meta, $this->data);

        }
    }

    /* --------------------------------------------------------------------------- */

   public function delete($id = null){
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
		 $srr = $this->store_return_receivers_model->getStore_return_receiversByID($id);
         if ( $srr->status == 'completed' ||$srr->status == 'approved') {
			$this->session->set_flashdata('error', lang("Do not allowed edit option"));
			admin_redirect("procurment/store_return_receivers");
		} 
        if ($this->store_return_receivers_model->deleteStore_return_receivers($id)) {
            if ($this->input->is_ajax_request()) {
                $this->sma->send_json(array('error' => 0, 'msg' => lang("store_return_receivers_deleted")));
            }
            $this->session->set_flashdata('message', lang('store_return_receivers_deleted'));
            admin_redirect('procurment/welcome');
        }
    }
    
    /* --------------------------------------------------------------------------- */

    public function suggestions(){
        $term = $this->input->get('term', true);
        $supplier_id = $this->input->get('supplier_id', true);
        if (strlen($term) < 1 || !$term) {
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . admin_url('procurment/welcome') . "'; }, 10);</script>");
        }

        $analyzed = $this->sma->analyze_term($term);
        $sr = $analyzed['term'];
        $option_id = $analyzed['option_id'];
        $rows = $this->store_return_receivers_model->getProductNames($sr);
        if ($rows) {
            $c = str_replace(".", "", microtime(true));
            $r = 0;
            foreach ($rows as $row) {
                $option = false;
                $row->item_tax_method = $row->tax_method;
                $options = $this->store_return_receivers_model->getProductOptions($row->id);
                if ($options) {
                    $opt = $option_id && $r == 0 ? $this->store_return_receivers_model->getProductOptionByID($option_id) : current($options);
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
                $row->cost = $supplier_id ? $this->getSupplierCost($supplier_id, $row) : $row->cost;
                $row->real_unit_cost = $row->cost;
                $row->base_quantity = 1;
                $row->base_unit = $row->unit;
                $row->base_unit_cost = $row->cost;
                $row->unit = $row->purchase_unit ? $row->purchase_unit : $row->unit;
                $row->new_entry = 1;
                $row->expiry = '';
                $row->qty = 1;
                $row->quantity_balance = '';
                $row->discount = '0';
                unset($row->details, $row->product_details, $row->price, $row->file, $row->supplier1price, $row->supplier2price, $row->supplier3price, $row->supplier4price, $row->supplier5price, $row->supplier1_part_no, $row->supplier2_part_no, $row->supplier3_part_no, $row->supplier4_part_no, $row->supplier5_part_no);
                $units = $this->siteprocurment->getUnitsByBUID($row->base_unit);
                $tax_rate = $this->siteprocurment->getTaxRateByID($row->tax_rate);
                $pr[] = array('id' => ($c + $r), 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")",
                    'row' => $row, 'tax_rate' => $tax_rate, 'units' => $units, 'options' => $options);
                $r++;
            }
            $this->sma->send_json($pr);
        } else {
            $this->sma->send_json(array(array('id' => 0, 'label' => lang('no_match_found'), 'value' => $term)));
        }
    }

    /* -------------------------------------------------------------------------------- */

    public function store_return_receivers_actions(){
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
                        $this->store_return_receivers_model->deleteStore_return_receivers($id);
                    }
                    $this->session->set_flashdata('message', $this->lang->line("store_return_receivers_deleted"));
                    redirect($_SERVER["HTTP_REFERER"]);

                } elseif ($this->input->post('form_action') == 'combine') {
                    $html = $this->combine_pdf($_POST['val']);
                } elseif ($this->input->post('form_action') == 'export_excel') {
                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle(lang('store_return_receivers'));
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('date'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('reference_no'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('supplier'));
                    $this->excel->getActiveSheet()->SetCellValue('F1', lang('status'));
                    $this->excel->getActiveSheet()->SetCellValue('E1', lang('grand_total'));

                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $store_return_receivers = $this->store_return_receivers_model->getStore_return_receiversByID($id);
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $this->sma->hrld($store_return_receivers->date));
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $store_return_receivers->reference_no);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $store_return_receivers->supplier);
                        $this->excel->getActiveSheet()->SetCellValue('D' . $row, $store_return_receivers->status);
                        $this->excel->getActiveSheet()->SetCellValue('E' . $row, $this->sma->formatMoney($store_return_receivers->grand_total));
                        $row++;
                    }

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'store_return_receivers_' . date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);
                }
            } else {
                $this->session->set_flashdata('error', $this->lang->line("no_store_return_receivers_selected"));
                redirect($_SERVER["HTTP_REFERER"]);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }

    /* -------------------------------------------------------------------------------- */

  

  

    public function view_return($id = null){
        //$this->sma->checkPermissions('return_store_return_receivers');

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $inv = $this->store_return_receivers_model->getReturnByID($id);
        if (!$this->session->userdata('view_right')) {
            $this->sma->view_rights($inv->created_by);
        }
        $this->data['barcode'] = "<img src='" . admin_url('procurment/products/gen_barcode/' . $inv->reference_no) . "' alt='" . $inv->reference_no . "' class='pull-left' />";
        $this->data['supplier'] = $this->siteprocurment->getCompanyByID($inv->supplier_id);
        $this->data['payments'] = $this->store_return_receivers_model->getPaymentsForPurchase($id);
        $this->data['user'] = $this->siteprocurment->getUser($inv->created_by);
        $this->data['warehouse'] = $this->siteprocurment->getWarehouseByID($inv->warehouse_id);
        $this->data['inv'] = $inv;
        $this->data['rows'] = $this->store_return_receivers_model->getAllReturnItems($id);
        $this->data['purchase'] = $this->store_return_receivers_model->getStore_return_receiversByID($inv->purchase_id);
        $this->load->view($this->theme.'store_return_receivers/view_return', $this->data);
    }

    public function return_purchase($id = null){
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $purchase = $this->store_return_receivers_model->getStore_return_receiversByID($id);
        if ($purchase->return_id) {
            $this->session->set_flashdata('error', lang("purchase_already_returned"));
            redirect($_SERVER["HTTP_REFERER"]);
        }
        $this->form_validation->set_rules('return_surcharge', lang("return_surcharge"), 'required');
        if ($this->form_validation->run() == true) {
            $reference = $this->input->post('reference_no') ? $this->input->post('reference_no') : $this->siteprocurment->getReference('rep');
            if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld(trim($this->input->post('date')));
            } else {
                $date = date('Y-m-d H:i:s');
            }
            $return_surcharge = $this->input->post('return_surcharge') ? $this->input->post('return_surcharge') : 0;
            $note = $this->sma->clear_tags($this->input->post('note'));
            $supplier_details = $this->siteprocurment->getCompanyByID($purchase->supplier_id);
            $total = 0;
            $product_tax = 0;
            $product_discount = 0;
            $gst_data = [];
            $total_cgst = $total_sgst = $total_igst = 0;
            $i = isset($_POST['product']) ? sizeof($_POST['product']) : 0;
            for ($r = 0; $r < $i; $r++) {
                $item_id = $_POST['product_id'][$r];
                $item_code = $_POST['product'][$r];
                $purchase_item_id = $_POST['purchase_item_id'][$r];
                $item_option = isset($_POST['product_option'][$r]) && !empty($_POST['product_option'][$r]) && $_POST['product_option'][$r] != 'false' ? $_POST['product_option'][$r] : null;
                $real_unit_cost = $this->sma->formatDecimal($_POST['real_unit_cost'][$r]);
                $unit_cost = $this->sma->formatDecimal($_POST['unit_cost'][$r]);
                $item_unit_quantity = (0-$_POST['quantity'][$r]);
                $item_expiry = isset($_POST['expiry'][$r]) ? $_POST['expiry'][$r] : '';
                $item_tax_rate = isset($_POST['product_tax'][$r]) ? $_POST['product_tax'][$r] : null;
                $item_discount = isset($_POST['product_discount'][$r]) ? $_POST['product_discount'][$r] : null;
                $item_unit = $_POST['product_unit'][$r];
                $item_quantity = (0-$_POST['product_base_quantity'][$r]);

                if (isset($item_code) && isset($real_unit_cost) && isset($unit_cost) && isset($item_quantity)) {
                    $product_details = $this->store_return_receivers_model->getProductByCode($item_code);

                    $item_type = $product_details->type;
                    $item_name = $product_details->name;
                    $pr_discount = $this->siteprocurment->calculateDiscount($item_discount, $unit_cost);
                    $unit_cost = $this->sma->formatDecimal($unit_cost - $pr_discount);
                    $pr_item_discount = $this->sma->formatDecimal(($pr_discount * $item_unit_quantity), 4);
                    $product_discount += $pr_item_discount;
                    $item_net_cost = $unit_cost;
                    $pr_item_tax = $item_tax = 0;
                    $tax = "";

                    if (isset($item_tax_rate) && $item_tax_rate != 0) {

                        $tax_details = $this->siteprocurment->getTaxRateByID($item_tax_rate);
                        $ctax = $this->siteprocurment->calculateTax($product_details, $tax_details, $unit_cost);
                        $item_tax = $ctax['amount'];
                        $tax = $ctax['tax'];
                        if ($product_details->tax_method != 1) {
                            $item_net_cost = $unit_cost - $item_tax;
                        }
                        $pr_item_tax = $this->sma->formatDecimal($item_tax * $item_unit_quantity, 4);
                        if ($this->Settings->indian_gst && $gst_data = $this->gst->calculteIndianGST($pr_item_tax, ($this->Settings->state == $supplier_details->state), $tax_details)) {
                            $total_cgst += $gst_data['cgst'];
                            $total_sgst += $gst_data['sgst'];
                            $total_igst += $gst_data['igst'];
                        }
                    }

                    $product_tax += $pr_item_tax;
                    $subtotal = $this->sma->formatDecimal((($item_net_cost * $item_unit_quantity) + $pr_item_tax), 4);
                    $unit = $this->siteprocurment->getUnitByID($item_unit);

                    $product = array(
                        'product_id' => $item_id,
                        'product_code' => $item_code,
                        'product_name' => $item_name,
                        'option_id' => $item_option,
                        'net_unit_cost' => $item_net_cost,
                        'unit_cost' => $this->sma->formatDecimal($item_net_cost + $item_tax),
                        'quantity' => $item_quantity,
                        'product_unit_id' => $item_unit,
                        'product_unit_code' => $unit->code,
                        'unit_quantity' => $item_unit_quantity,
                        'quantity_balance' => $item_quantity,
                        'warehouse_id' => $purchase->warehouse_id,
                        'item_tax' => $pr_item_tax,
                        'tax_rate_id' => $item_tax_rate,
                        'tax' => $tax,
                        'discount' => $item_discount,
                        'item_discount' => $pr_item_discount,
                        'subtotal' => $this->sma->formatDecimal($subtotal),
                        'real_unit_cost' => $real_unit_cost,
                        'purchase_item_id' => $purchase_item_id,
                        'status' => 'received',
                    );

                    $products[] = ($product+$gst_data);
                    $total += $this->sma->formatDecimal(($item_net_cost * $item_unit_quantity), 4);
                }
            }
            if (empty($products)) {
                $this->form_validation->set_rules('product', lang("order_items"), 'required');
            } else {
                krsort($products);
            }

            $order_discount = $this->siteprocurment->calculateDiscount($this->input->post('discount') ? $this->input->post('order_discount') : null, ($total + $product_tax));
            $total_discount = $this->sma->formatDecimal(($order_discount + $product_discount), 4);
            $order_tax = $this->siteprocurment->calculateOrderTax($this->input->post('order_tax'), ($total + $product_tax - $total_discount));
            $total_tax = $this->sma->formatDecimal(($product_tax + $order_tax), 4);
            $grand_total = $this->sma->formatDecimal(($total + $total_tax + $this->sma->formatDecimal($return_surcharge) - $order_discount), 4);
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
            $data = array('date' => $date,
                'purchase_id' => $id,
                'reference_no' => $purchase->reference_no,
                'supplier_id' => $purchase->supplier_id,
                'supplier' => $purchase->supplier,
                'warehouse_id' => $purchase->warehouse_id,
                'note' => $note,
                'total' => $total,
                'product_discount' => $product_discount,
                'order_discount_id' => ($this->input->post('discount') ? $this->input->post('order_discount') : null),
                'order_discount' => $order_discount,
                'total_discount' => $total_discount,
                'product_tax' => $product_tax,
                'order_tax_id' => $this->input->post('order_tax'),
                'order_tax' => $order_tax,
                'total_tax' => $total_tax,
                'surcharge' => $this->sma->formatDecimal($return_surcharge),
                'grand_total' => $grand_total,
                'created_by' => $this->session->userdata('user_id'),
                'return_purchase_ref' => $reference,
                'status' => 'returned',
                'payment_status' => $purchase->payment_status == 'paid' ? 'due' : 'process',
            );
            if ($this->Settings->indian_gst) {
                $data['cgst'] = $total_cgst;
                $data['sgst'] = $total_sgst;
                $data['igst'] = $total_igst;
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

            // $this->sma->print_arrays($data, $products);
        }

        if ($this->form_validation->run() == true && $this->store_return_receivers_model->addPurchase($data, $products)) {
            $this->session->set_flashdata('message', lang("return_purchase_added"));
            admin_redirect("procurment/store_return_receivers");
        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['inv'] = $purchase;
            if ($this->data['inv']->status != 'received' && $this->data['inv']->status != 'partial') {
                $this->session->set_flashdata('error', lang("purchase_status_x_received"));
                redirect($_SERVER["HTTP_REFERER"]);
            }
            if ($this->Settings->disable_editing) {
                if ($this->data['inv']->date <= date('Y-m-d', strtotime('-'.$this->Settings->disable_editing.' days'))) {
                    $this->session->set_flashdata('error', sprintf(lang("purchase_x_edited_older_than_x_days"), $this->Settings->disable_editing));
                    redirect($_SERVER["HTTP_REFERER"]);
                }
            }
            $inv_items = $this->store_return_receivers_model->getAllPurchaseItems($id);
             krsort($inv_items);
            $c = rand(100000, 9999999);
            foreach ($inv_items as $item) {
                $row = $this->siteprocurment->getProductByID($item->product_id);
                $row->expiry = (($item->expiry && $item->expiry != '0000-00-00') ? $this->sma->hrsd($item->expiry) : '');
                $row->base_quantity = $item->quantity;
                $row->base_unit = $row->unit ? $row->unit : $item->product_unit_id;
                $row->base_unit_cost = $row->cost ? $row->cost : $item->unit_cost;
                $row->unit = $item->product_unit_id;
                $row->qty = $item->unit_quantity;
                $row->oqty = $item->unit_quantity;
                $row->purchase_item_id = $item->id;
                $row->supplier_part_no = $item->supplier_part_no;
                $row->received = $item->quantity_received ? $item->quantity_received : $item->quantity;
                $row->quantity_balance = $item->quantity_balance + ($item->quantity-$row->received);
                $row->discount = $item->discount ? $item->discount : '0';
                $options = $this->store_return_receivers_model->getProductOptions($row->id);
                $row->option = !empty($item->option_id) ? $item->option_id : '';
                $row->real_unit_cost = $item->real_unit_cost;
                $row->cost = $this->sma->formatDecimal($item->net_unit_cost + ($item->item_discount / $item->quantity));
                $row->tax_rate = $item->tax_rate_id;
                unset($row->details, $row->product_details, $row->price, $row->file, $row->product_group_id);
                $units = $this->siteprocurment->getUnitsByBUID($row->base_unit);
                $tax_rate = $this->siteprocurment->getTaxRateByID($row->tax_rate);
                $ri = $this->Settings->item_addition ? $row->id : $c;

                $pr[$ri] = array('id' => $c, 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'row' => $row, 'units' => $units, 'tax_rate' => $tax_rate, 'options' => $options);

                $c++;
            }

            $this->data['inv_items'] = json_encode($pr);
            $this->data['id'] = $id;
            $this->data['reference'] = '';
            $this->data['tax_rates'] = $this->siteprocurment->getAllTaxRates();
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('procurment/store_return_receivers'), 'page' => lang('store_return_receivers')), array('link' => '#', 'page' => lang('return_purchase')));
            $meta = array('page_title' => lang('return_purchase'), 'bc' => $bc);
            $this->page_construct('procurment/store_return_receivers/return_purchase', $meta, $this->data);
        }
    }

    public function getSupplierCost($supplier_id, $product)
    {
        switch ($supplier_id) {
            case $product->supplier1:
                $cost =  $product->supplier1price > 0 ? $product->supplier1price : $product->cost;
                break;
            case $product->supplier2:
                $cost =  $product->supplier2price > 0 ? $product->supplier2price : $product->cost;
                break;
            case $product->supplier3:
                $cost =  $product->supplier3price > 0 ? $product->supplier3price : $product->cost;
                break;
            case $product->supplier4:
                $cost =  $product->supplier4price > 0 ? $product->supplier4price : $product->cost;
                break;
            case $product->supplier5:
                $cost =  $product->supplier5price > 0 ? $product->supplier5price : $product->cost;
                break;
            default:
                $cost = $product->cost;
        }
        return $cost;
    }

    public function update_status($id)
    {

        $this->form_validation->set_rules('status', lang("status"), 'required');

        if ($this->form_validation->run() == true) {
            $status = $this->input->post('status');
            $note = $this->sma->clear_tags($this->input->post('note'));
        } elseif ($this->input->post('update')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'sales');
        }

        if ($this->form_validation->run() == true && $this->store_return_receivers_model->updateStatus($id, $status, $note)) {
            $this->session->set_flashdata('message', lang('status_updated'));
            admin_redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'sales');
        } else {

            $this->data['inv'] = $this->store_return_receivers_model->getStore_return_receiversByID($id);
            $this->data['returned'] = FALSE;
            if ($this->data['inv']->status == 'returned' || $this->data['inv']->return_id) {
                $this->data['returned'] = TRUE;
            }
            $this->data['modal_js'] = $this->siteprocurment->modal_js();
            $this->load->view($this->theme.'store_return_receivers/update_status', $this->data);

        }
    }

}
