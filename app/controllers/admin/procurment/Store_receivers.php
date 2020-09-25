<?php defined('BASEPATH') or exit('No direct script access allowed');
class Store_receivers extends MY_Controller{
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
        $this->lang->admin_load('procurment/store_receivers', $this->Settings->user_language);
        $this->load->library('form_validation');
        $this->load->admin_model('procurment/store_receivers_model');
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
	
	public function store_receivers_list(){
		$poref =  $this->input->get('poref');
		$data['store_receivers'] = $this->store_receivers_model->getRequestByID($poref);
		$inv_items = $this->store_receivers_model->getAllRequestItems($poref);
		 krsort($inv_items);
		$c = rand(100000, 9999999);
		foreach ($inv_items as $item) {
			$row = $this->siteprocurment->getProductByID($item->product_id);
			$row->expiry = (($item->expiry && $item->expiry != '0000-00-00') ? $this->sma->hrsd($item->expiry) : '');
			$row->mfg = (($item->mfg && $item->mfg != '0000-00-00') ? $this->sma->hrsd($item->mfg) : '');
			$row->batch_no = $item->batch_no ? $item->batch_no : '';
			$row->current_quantity = $item->available_quantity;
			$row->pending_quantity = $item->pending_quantity;
			$row->transfer_quantity = $item->transfer_quantity;
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
			$options = $this->store_receivers_model->getProductOptions($row->id);
			$row->option = $item->option_id;
            $row->real_unit_cost = $item->real_unit_price;
            $row->cost = $this->sma->formatDecimal($item->net_unit_price + ($item->item_discount / $item->quantity));
				
            $row->tax_rate = $item->tax_rate_id;
		    $row->tax_method = $item->item_tax_method;
            unset($row->details, $row->product_details, $row->price, $row->file, $row->product_group_id);
            $units = $this->siteprocurment->getUnitsByBUID($row->base_unit);
            $tax_rate = $this->siteprocurment->getTaxRateByID($row->tax_rate);
			$ri = $this->Settings->item_addition ? $row->id : $row->id;
			$pr[$ri] = array('id' => $c, 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")",
				'row' => $row, 'tax_rate' => $row->tax_rate, 'units' => $units, 'options' => $options);
			$c++;
		}
		
		$data['store_receiversitem'] = $pr;
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
		$data = $this->store_receivers_model->getSupplierdetails($supplier_id);
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

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('store_receivers')));
        $meta = array('page_title' => lang('store_receivers'), 'bc' => $bc);
        $this->page_construct('procurment/store_receivers/index', $meta, $this->data);

    }

    
	
	public function getStore_receivers($warehouse_id = null){ 
		$view_link = '<a href="'.admin_url('procurment/store_receivers/view/$1').'" data-toggle="modal" data-target="#myModal"><i class="fa fa-edit"></i>'.lang('view_store_receiver').'</a>';
        $detail_link = anchor('admin/procurment/store_receivers/view/$1', '<i class="fa fa-file-text-o"></i> ' . lang('store_receivers_details'));
        $edit_link = anchor('admin/procurment/store_receivers/edit/$1', '<i class="fa fa-edit"></i> ' . lang('edit_store_receiver'));
        $delete_link = "<a href='#' class='po' title='<b>" . $this->lang->line("delete_quotation") . "</b>' data-content=\"<p>"
        . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('procurment/store_receivers/delete/$1') . "'>"
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
	    ->select("pro_store_receivers.id, pro_store_receivers.date, pro_store_receivers.reference_no, pro_store_request.reference_no as ref, f.name as from_name, t.name as to_name, pro_store_receivers.total_no_qty as transfer_quantity, pro_store_receivers.status")
            ->from('pro_store_receivers')
	    ->join('pro_store_request', 'pro_store_request.id = pro_store_receivers.store_indent_id', 'left')
	    ->join('warehouses f', 'f.id = pro_store_receivers.from_store', 'left')
	    ->join('warehouses t', 't.id = pro_store_receivers.to_store', 'left')
		->where('pro_store_receivers.to_store',$this->store_id);		
		$this->datatables->group_by('pro_store_receivers.id');
        $this->datatables->add_column("Actions", $action, "pro_store_receivers.id");
        echo $this->datatables->generate();
		
    }

    /* ----------------------------------------------------------------------------- */

   
   
    public function view($store_receivers_id = null){
	     $id = $store_receivers_id;
	      $this->data['store_rec'] =$po= $this->store_receivers_model->getStore_receiverByID($id);
            $inv_items = $this->store_receivers_model->getAllStore_receiverItemsbyid($id);
            /*echo "<pre>";
             print_r($inv_items);die;*/
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
             $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('procurment/store_receivers'), 'page' => lang('store_receivers')), array('link' => '#', 'page' => lang('view')));
             $meta = array('page_title' => lang('view_store_receivers_details'), 'bc' => $bc);
	         $this->load->view($this->theme . 'procurment/store_receivers/view',  $this->data);

    }
    
    /* ------------------------------------------------------------------------------------- */

    public function edit($id = null){
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $inv = $this->store_receivers_model->getStore_receiversByID($id);
        if ($inv->status == 'approved' || $inv->status == 'completed') {
	       $this->session->set_flashdata('error', lang("Do not allowed edit option"));
	       admin_redirect("procurment/store_receivers");
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
			    'store_receive_itemid'=>$_POST["store_receive_itemid"][$r],
			    'product_id' => $_POST["product_id"][$r],
			    'product_code' => $_POST['product_code'][$r],
			    'product_type' => $_POST['product_type'][$r],
			    'product_name' => $_POST['product_name'][$r],			    
			    'request_qty' => $_POST['request_qty'][$r],
			    'store_id' =>$this->store_id,
			    );
		    foreach($_POST['batch'][$this->store_id.$_POST["product_id"][$r]] as $k => $row){
			$products[$r]['batches'][] = array(
		        'id'=>$row['itemid'],
				'store_receiver_item_id'=>$row['storereceiveritemid'],	
				'store_receiver_id'=>$row['storereceiverid'],					
			    'transfer_qty' => $row['transfer_qty'],
			    'received_qty' => $row['received_qty'],
				 'received_unit_qty' => $row['base_received_qty'],
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
				'variant_id'=>$_POST['variant_id'][$r]
			);
			$total_t_qty +=$row['transfer_qty'];
			$total_r_qty +=$row['received_qty'];
		    }
		    $products[$r]['transfer_qty'] = $total_t_qty;
		    $products[$r]['received_qty'] = $total_r_qty;
			}
            if (empty($products)) {
                $this->form_validation->set_rules('product', lang("order_items"), 'required');
            } else {
                
                krsort($items);
            }
            /*update common_store_receivers*/
            $data = array(
               'intend_request_id' =>($this->input->post('intend_request_id'))?$this->input->post('intend_request_id'):0,
		       'intend_request_date' =>($this->input->post('intend_request_date'))?$this->input->post('intend_request_date'):0,
		       'total_no_items'=>$this->input->post('total_no_items'),
		       'total_no_qty'=>$this->input->post('total_no_qty'),
		       'status' =>$this->input->post('status'),
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
        }

        if ($this->form_validation->run() == true && $this->store_receivers_model->updateStore_receivers($id, $data, $products)) {
            $this->session->set_userdata('remove_pols', 1);
            $this->session->set_flashdata('message', $this->lang->line("pro_store_receivers_added"));
            admin_redirect('procurment/store_receivers');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['inv'] = $inv;
            $inv_items = $this->store_receivers_model->getAllStore_receiversItems($id);   
            krsort($inv_items);
            $c = rand(100000, 9999999);
            foreach ($inv_items as $item) {
              $row                 = $this->siteprocurment->getItemByID($item->product_id);
			  $row->quantity       =  $item->quantity;
			  $row->batch_no       = $item->batch;
			  $row->expiry         = $item->expiry;
			  $row->cost_price     = $item->cost_price;
			  $row->price          = $item->selling_price;
			  $row->tax            = $item->tax;
			  $row->tax_method     = $item->tax_method;
			  $row->variant_id     = $item->variant_id;
			  $row->category_id    = $row->category_id;
			  $row->subcategory_id = $row->subcategory_id;
			  $row->brand_id       = $row->brand;
			  $row->base_unit      = $row->unit;
              $row->unit           = $row->purchase_unit ? $row->purchase_unit : $row->unit;
			  $batches             = $this->store_receivers_model->getTransferredStockData($item->id);
			  $row->request_qty    = $item->request_qty;
			  $row->received_qty   = $item->received_qty;
			  $row->batches        = $batches;
			  $unique_item_id      = $this->store_id.$item->product_id.$item->batch;
			  $ri                  = $row->id;
			  $units = $this->siteprocurment->getUnitsByBUID($row->base_unit);
			  $options = array();
			  $pr[$unique_item_id] = array('unique_id'=>$unique_item_id,'id' => $row->id,'store_receiveItemid'=>$item->id, 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")",
				'row' => $row,  'options' => $options,'units'=>$units);
            }
            $this->data['inv_items'] 	   = json_encode($pr);
            $this->data['id'] 			   = $id;
            $this->data['suppliers'] 	   = $this->siteprocurment->getAllCompanies('supplier');
            $this->data['store_receivers'] = $this->store_receivers_model->getStore_receiversByID($id);
			$this->data['warehouses'] 	   = $this->siteprocurment->getAllWarehouses();
			$this->data['all_stores']      = $this->siteprocurment->getAllWarehouses_Storeslist();
			$this->data['store_req']       = $this->siteprocurment->getAll_respectiveSTOREREQUESTNUMBER();
			$this->data['stores']          = $this->siteprocurment->getAllWarehouses_Stores();
            $this->load->helper('string');
            $value = random_string('alnum', 20);
            $this->session->set_userdata('user_csrf', $value);
            $this->session->set_userdata('remove_pols', 1);
            $this->data['csrf'] = $this->session->userdata('user_csrf');
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('procurment/store_receivers'), 'page' => lang('store_receivers')), array('link' => '#', 'page' => lang('edit_store_receivers')));
            $meta = array('page_title' => lang('edit_store_receivers'), 'bc' => $bc);
            $this->page_construct('procurment/store_receivers/edit', $meta, $this->data);
        }
    }

    /* ----------------------------------------------------------------------------------------------------------- */

  
	public function delete($id = null){
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
		$inv = $this->store_receivers_model->getStore_receiversByID($id);
        if ($inv->status == 'approved' || $inv->status == 'completed') {
	    $this->session->set_flashdata('error', lang("Do not allowed edit option"));
	    admin_redirect("procurment/store_receivers");
		}
        if ($this->store_receivers_model->deleteStore_receivers($id)) {
            if ($this->input->is_ajax_request()) {
                $this->sma->send_json(array('error' => 0, 'msg' => lang("pro_store_receivers_deleted")));
            }
            $this->session->set_flashdata('message', lang('store_receivers_deleted'));
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
        $rows = $this->store_receivers_model->getProductNames($sr);
        if ($rows) {
            $c = str_replace(".", "", microtime(true));
            $r = 0;
            foreach ($rows as $row) {
                $option = false;
                $row->item_tax_method = $row->tax_method;
                $options = $this->store_receivers_model->getProductOptions($row->id);
                if ($options) {
                    $opt = $option_id && $r == 0 ? $this->store_receivers_model->getProductOptionByID($option_id) : current($options);
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

    public function store_receivers_actions()
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
                        $this->store_receivers_model->deleteStore_receivers($id);
                    }
                    $this->session->set_flashdata('message', $this->lang->line("store_receivers_deleted"));
                    redirect($_SERVER["HTTP_REFERER"]);

                } elseif ($this->input->post('form_action') == 'combine') {

                    $html = $this->combine_pdf($_POST['val']);

                } elseif ($this->input->post('form_action') == 'export_excel') {

                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle(lang('store_receivers'));
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('date'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('reference_no'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('supplier'));
                    $this->excel->getActiveSheet()->SetCellValue('F1', lang('status'));
                    $this->excel->getActiveSheet()->SetCellValue('E1', lang('grand_total'));

                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $store_receivers = $this->store_receivers_model->getStore_receiversByID($id);
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $this->sma->hrld($store_receivers->date));
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $store_receivers->reference_no);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $store_receivers->supplier);
                        $this->excel->getActiveSheet()->SetCellValue('D' . $row, $store_receivers->status);
                        $this->excel->getActiveSheet()->SetCellValue('E' . $row, $this->sma->formatMoney($store_receivers->grand_total));
                        $row++;
                    }

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'store_receivers_' . date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);
                }
            } else {
                $this->session->set_flashdata('error', $this->lang->line("no_store_receivers_selected"));
                redirect($_SERVER["HTTP_REFERER"]);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }

  
    
   
}
