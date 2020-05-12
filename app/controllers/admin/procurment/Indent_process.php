<?php defined('BASEPATH') or exit('No direct script access allowed');
class Indent_process extends MY_Controller{
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
        $this->lang->admin_load('procurement', $this->Settings->user_language);
        $this->load->library('form_validation');
        $this->load->admin_model('procurment/indent_process_model');
        $this->digital_upload_path = 'assets/uploads/procurment/indent_process/';
        $this->digital_file_types = 'zip|psd|ai|rar|pdf|doc|docx|xls|xlsx|ppt|pptx|gif|jpg|jpeg|png|tif|txt';
        $this->allowed_file_size = '1024';
	if (!file_exists($this->digital_upload_path)) {
		mkdir($this->digital_upload_path, 0777, true);
	}
        $this->data['logo'] = true;
		
		$this->Muser_id = $this->session->userdata('user_id');
		$this->Maccess_id = 8;
	    $this->ModuleTheme = 'procurment/indent_process/';
	    $this->module = 'indent_process';
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
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('store_indent_request')));
        $meta = array('page_title' => lang('store_indent_request'), 'bc' => $bc);
        $this->page_construct($this->ModuleTheme.'index', $meta, $this->data);

    }

    public function getStock_request($warehouse_id = null){
        if ((!$this->Owner || !$this->Admin) && !$warehouse_id) {
            $user = $this->siteprocurment->getUser();
            $warehouse_id = $user->warehouse_id;
        }
		$view_link = '<a href="'.admin_url('procurment/indent_process/view/$1').'" data-toggle="modal" data-target="#myModal" data-backdrop="static" data-keyboard="false"><i class="fa fa-edit"></i>'.lang('view_indent_process').'</a>';
       
        $detail_link = anchor('admin/procurment/store_request/view/$1/', '<i class="fa fa-file-text-o"></i> ' . lang('store_request_details'));
        $email_link = anchor('admin/procurment/store_request/email/$1', '<i class="fa fa-envelope"></i> ' . lang('email_store_request'), 'data-toggle="modal" data-target="#myModal"');
		
		
		//$edit_link = anchor('admin/procurment/indent_process/edit/$1', '<i class="fa fa-edit"></i> ' . lang('edit_indent_process'));
		
       // $convert_link = anchor('admin/procurment/sales/add/$1', '<i class="fa fa-heart"></i> ' . lang('create_sale'));
       // $pc_link = anchor('admin/procurment/purchases/add/$1', '<i class="fa fa-star"></i> ' . lang('create_purchase'));
        $pdf_link = anchor('admin/procurment/store_request/pdf/$1', '<i class="fa fa-file-pdf-o"></i> ' . lang('download_pdf'));
        $delete_link = "<a href='#' class='po' title='<b>" . $this->lang->line("delete_indent_process") . "</b>' data-content=\"<p>"
        . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('procurment/indent_process/delete/$1') . "'>"
        . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i> "
        . lang('delete_indent_process') . "</a>";
		
		$action = '<div class="text-center"><div class="btn-group text-left">'
        . '<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">'
        . lang('actions') . ' <span class="caret"></span></button>
                    <ul class="dropdown-menu pull-right" role="menu">
                        
			<li>' . $view_link . '</li>
                        <li>' . $delete_link . '</li>
                    </ul>
                </div></div>';
        $this->load->library('datatables');
        if ($warehouse_id) {
            $this->datatables
                ->select("pro_stock_request.id, pro_stock_request.date, pro_stock_request.reference_no,   pro_stock_request.status, pro_stock_request.attachment")
				
                ->from('pro_stock_request')
                ->where('pro_stock_request.warehouse_id', $warehouse_id);
        } else {
            $this->datatables
                ->select("pro_stock_request.id, pro_stock_request.date, pro_stock_request.reference_no, f.name as from_name, t.name as to_name, pro_stock_request.status, pro_stock_request.attachment as attachment")
                ->from('pro_stock_request')
				->join('warehouses f', 'f.id = pro_stock_request.from_store_id', 'left')
				->join('warehouses t', 't.id = pro_stock_request.to_store_id', 'left');
        }
        
		$this->datatables->edit_column('attachment', '$1__$2', $this->digital_upload_path.', attachment');
        $this->datatables->add_column("Actions", $action, "pro_stock_request.id,pro_stock_request.status");
        echo $this->datatables->generate();
    }

    public function modal_view($store_request_id = null){
        if ($this->input->get('id')) {
            $store_request_id = $this->input->get('id');
        }
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $inv = $this->store_request_model->getStore_requestByID($store_request_id);
        if (!$this->session->userdata('view_right')) {
            $this->sma->view_rights($inv->created_by, true);
        }
        $this->data['rows'] = $this->store_request_model->getAllStore_requestItems($store_request_id);
        $this->data['customer'] = $this->siteprocurment->getCompanyByID($inv->customer_id);
        $this->data['biller'] = $this->siteprocurment->getCompanyByID($inv->biller_id);
        $this->data['created_by'] = $this->siteprocurment->getUser($inv->created_by);
        $this->data['updated_by'] = $inv->updated_by ? $this->siteprocurment->getUser($inv->updated_by) : null;
        $this->data['warehouse'] = $this->siteprocurment->getWarehouseByID($inv->warehouse_id);
        $this->data['inv'] = $inv;
        $this->load->view($this->theme .$this->ModuleTheme.'/modal_view', $this->data);
    }

    public function view($stock_request_id = null){
			$this->sma->checkPermissions();
			$id = $stock_request_id;
			$this->data['store_req'] = $this->indent_process_model->getStock_requestByID($id);
            $inv_items = $this->indent_process_model->getAllStock_requestItems($id);
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
                $row->qty = $item->quantity;
                $row->discount = $item->discount ? $item->discount : '0';
                $row->price = $this->sma->formatDecimal($item->net_unit_price + $this->sma->formatDecimal($item->item_discount / $item->quantity));
                $row->unit_price = $row->tax_method ? $item->unit_price + $this->sma->formatDecimal($item->item_discount / $item->quantity) + $this->sma->formatDecimal($item->item_tax / $item->quantity) : $item->unit_price + ($item->item_discount / $item->quantity);
                $row->real_unit_price = $item->real_unit_price;
                $row->tax_rate = $item->tax_rate_id;
                $row->option = $item->option_id;
                $units = $this->siteprocurment->getUnitsByBUID($row->base_unit);
                $tax_rate = $this->siteprocurment->getTaxRateByID($row->tax_rate);
                $ri = $this->Settings->item_addition ? $row->id : $c;

                $pr[$ri] = array('id' => $c, 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'row' => $item, 'tax_rate' => $tax_rate, 'units' => $units);
                $c++;
            }
            $this->data['store_req_items'] = $pr;
            $this->data['id'] = $id;
			$this->data['stores'] = $this->siteprocurment->getAllStores();
			$this->data['all_stores'] = $this->siteprocurment->getAllWarehouses_Stores();
            //$this->data['currencies'] = $this->siteprocurment->getAllCurrencies();
            $this->data['billers'] = ($this->Owner || $this->Admin || !$this->session->userdata('biller_id')) ? $this->siteprocurment->getAllCompanies('biller') : null;
            $this->data['tax_rates'] = $this->siteprocurment->getAllTaxRates();
            $this->data['warehouses'] = ($this->Owner || $this->Admin || !$this->session->userdata('warehouse_id')) ? $this->siteprocurment->getAllWarehouses() : null;

            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('procurment/indent_process'), 'page' => lang('stock_request')), array('link' => '#', 'page' => lang('view_stock_request')));
            $meta = array('page_title' => lang('view_stock_request'), 'bc' => $bc);
            $this->load->view($this->theme . $this->ModuleTheme.'view',  $this->data);

    }


    public function add(){
        $this->sma->checkPermissions();
		$this->form_validation->set_rules('from_store_id', $this->lang->line("from_store"), 'required');
		$this->form_validation->set_rules('indent_id', $this->lang->line("indent_no"), 'required');
	    $this->session->unset_userdata('csrf_token');
        if ($this->form_validation->run() == true) { 
	    $n = $this->siteprocurment->lastidStoreStockRequest();
        $reference = 'SR'.str_pad($n + 1, 5, 0, STR_PAD_LEFT);
	    $date = date('Y-m-d H:i:s');
	    $indent_reference = $this->input->post('indent_id');
	    $process_store_ids = $this->input->post('processing_from');
	    $i = count($_POST['product_id']);
	    $products = array();
	    for($r = 0; $r < $i; $r++){
		    $products[] = array(
			    'product_id' => $_POST["product_id"][$r],
			    'product_code' => $_POST['product_code'][$r],
			    'product_type' => $_POST['product_type'][$r],
			    'product_name' => $_POST['product_name'][$r],			    
			    
			    'store_id' =>$this->store_id,
				'request_qty'=>$_POST['qty'][$r]
		    );
	    }
            
            if (empty($products)) {
                $this->form_validation->set_rules('product', lang("order_items"), 'required');
            } else {
                krsort($products);
            }
	    $stocks = $this->input->post('stock');
	    $store_req_products = array();
	    foreach($process_store_ids as $k => $store){
		$store_req_products[$k]['products'] = array();
		$total_no_qty = 0;
		foreach($products as $p => $product){
		    $p_id = $product['product_id'];
		    $qty = $stocks[$this->store_id.$p_id][$store]['t_stock'];
		    $product['quantity'] = ($qty!='')?$qty:0;
		    $products[$p]['quantity'] = $product['quantity'];
		    if($qty!=0){
			$store_req_products[$k]['products'][]=$product;
			$total_no_qty +=$product['quantity'];
		    }
		}
		if(!empty($store_req_products[$k]['products'])){
		    $store_req_products[$k]['data'] = array(
			  'date'=>date('Y-m-d H:i:s'),
			  'store_id' =>$this->store_id,
			  'store_indent_id'=>$this->input->post('indent_id'),
			  'store_indent_date'=>$this->input->post('indent_date'),
			  'from_store_id' => $this->input->post('from_store_id'),
			  'to_store_id' =>$store,
			  'total_no_items'=>count($store_req_products[$k]['products']),
			  'total_no_qty'=>$total_no_qty,
			  'status' =>'approved',
			  'note' =>$this->input->post('note'),
			  'approved_by' => $this->session->userdata('user_id'),
			  'approved_on'=>date('Y-m-d H:i:s'),
		    );
		}
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

        if ($this->form_validation->run() == true && $this->indent_process_model->addStock_request($store_req_products,$indent_reference)) {
            $this->session->set_userdata('remove_quls', 1);
            $this->session->set_flashdata('message', $this->lang->line("stock_request_added"));
            admin_redirect('procurment/indent_process');
        } else {
            $this->data['stores'] = $this->siteprocurment->getAllStores();
			$this->data['all_stores'] = $this->siteprocurment->getAllWarehouses_Stores();
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['billers'] = ($this->Owner || $this->Admin || !$this->session->userdata('biller_id')) ? $this->siteprocurment->getAllCompanies('biller') : null;
            $this->data['warehouses'] = ($this->Owner || $this->Admin || !$this->session->userdata('warehouse_id')) ? $this->siteprocurment->getAllWarehouses() : null;
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('procurment/indent_process'), 'page' => lang('indent_process')), array('link' => '#', 'page' => lang('indent_processing')));
            $meta = array('page_title' => lang('indent_processing'), 'bc' => $bc);
	        $this->data['warehouse'] = $this->siteprocurment->getWarehouse();
         //   $this->page_construct($this->ModuleTheme.'add', $meta, $this->data);
			 $this->page_construct('procurment/indent_process/add', $meta, $this->data);
        }
    }

    public function edit($id = null){
        $this->sma->checkPermissions();

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $inv = $this->store_request_model->getStore_requestByID($id);
	if ($inv->status == 'approved' || $inv->status == 'completed') {
		$this->session->set_flashdata('error', lang("Do not allowed edit option"));
		admin_redirect("procurment/indent_process");
	}	
		
        $this->form_validation->set_rules('from_store_id', $this->lang->line("from_store"), 'required');
        $this->form_validation->set_rules('to_store_id', $this->lang->line("to_store"), 'required');
        $this->form_validation->set_rules('note', $this->lang->line("note"), 'xss_clean');

        if ($this->form_validation->run() == true) {
           
            $i = count($_POST['product_id']);
	    $products = array();
	    for($r = 0; $r < $i; $r++){
		    $products[] = array(
			    'product_id' => $_POST["product_id"][$r],
			    'product_code' => $_POST['product_code'][$r],
			    'product_type' => $_POST['product_type'][$r],
			    'product_name' => $_POST['product_name'][$r],			    
			    'quantity' => $_POST['qty'][$r],
			    'store_id' =>$this->store_id,
		    );
	    }
            
            if (empty($products)) {
                $this->form_validation->set_rules('product', lang("order_items"), 'required');
            } else {
                krsort($products);
            }
	    
            $data = array(
			  'date'=>date('Y-m-d H:i:s'),			  
			  'store_id' =>$this->store_id,
			  
			  'from_store_id' => $this->input->post('from_store_id'),
			  'to_store_id' =>$this->input->post('to_store_id'),
			  'total_no_items'=>$this->input->post('total_no_items'),
			  'total_no_qty'=>$this->input->post('total_no_qty'),
			  'status' =>$this->input->post('status'),
			  'note' =>$this->input->post('note'),
			  
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
		@unlink($this->digital_upload_path.$inv->attachment);
            }

            //$this->sma->print_arrays($data, $products);die;
        }
        /*echo "<pre>";
print_r($this->input->post());die;*/
        if ($this->form_validation->run() == true && $this->store_request_model->updateStore_request($id, $data, $products)) {
            $this->session->set_userdata('remove_quls', 1);
            $this->session->set_flashdata('message', $this->lang->line("store_request_added"));
            admin_redirect('procurment/store_request');
        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['inv'] = $this->store_request_model->getStore_requestByID($id);
            $inv_items = $this->store_request_model->getAllStore_requestItems($id);

            krsort($inv_items);
            $c = rand(100000, 9999999);
            foreach ($inv_items as $item) {
                $row = $this->siteprocurment->getItemByID($item->product_id);
                $units = $this->site->getUnitsByBUID($row->base_unit);
		$tax_rate = $this->site->getTaxRateByID($row->tax_rate);		
		$unique_item_id = $this->store_id.$item->id;
		$item->id = $item->product_id;
		$item->name = $item->product_name;
		$item->code = $item->product_code;
		$item->type = $item->product_type;
		$item->qty = $item->quantity;
		$pr[$unique_item_id] = array('id' => $item->id, 'label' => $item->name . " (" . $item->code . ")",'row' => $item, 'unique_id'=>$unique_item_id,'store_id'=>$this->store_id);
                
                $c++;
            }
	    //p($pr,1);
            $this->data['inv_items'] = json_encode($pr);
            $this->data['id'] = $id;
	    $this->data['stores'] = $this->siteprocurment->getAllStores();
	    $this->data['all_stores'] = $this->siteprocurment->getAllWarehouses_Stores();
            //$this->data['currencies'] = $this->siteprocurment->getAllCurrencies();
            $this->data['billers'] = ($this->Owner || $this->Admin || !$this->session->userdata('biller_id')) ? $this->siteprocurment->getAllCompanies('biller') : null;
            $this->data['tax_rates'] = $this->siteprocurment->getAllTaxRates();
            $this->data['warehouses'] = ($this->Owner || $this->Admin || !$this->session->userdata('warehouse_id')) ? $this->siteprocurment->getAllWarehouses() : null;

            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('procurment/store_request'), 'page' => lang('store_request')), array('link' => '#', 'page' => lang('edit_store_request')));
            $meta = array('page_title' => lang('edit_store_request'), 'bc' => $bc);
	    $this->data['warehouse'] = $this->siteprocurment->getWarehouse($inv->to_store_id);
            $this->page_construct($this->ModuleTheme.'edit', $meta, $this->data);
        }
    }

    public function delete($id = null)
    {
        $this->sma->checkPermissions(NULL, true);

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        if ($this->store_request_model->deleteStore_request($id)) {
            if ($this->input->is_ajax_store_request()) {
                $this->sma->send_json(array('error' => 0, 'msg' => lang("store_request_deleted")));
            }
            $this->session->set_flashdata('message', lang('store_request_deleted'));
            admin_redirect('procurment/welcome');
        }
    }

    public function suggestions()
    {
        $term = $this->input->get('term', true);
        $warehouse_id = $this->input->get('warehouse_id', true);
        $supplier_id = $this->input->get('supplier_id', true);

        if (strlen($term) < 1 || !$term) {
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . admin_url('procurment/welcome') . "'; }, 10);</script>");
        }

        $analyzed = $this->sma->analyze_term($term);
        $sr = $analyzed['term'];
        $option_id = $analyzed['option_id'];
       
        $rows = $this->store_request_model->getProductNames($sr);
        if ($rows) {
            $c = str_replace(".", "", microtime(true));
            $r = 0;
           
               foreach ($rows as $product) {                
		$units = $this->site->getUnitsByBUID($product->base_unit);
		$tax_rate = $this->site->getTaxRateByID($product->tax_rate);		
		$unique_item_id = $this->store_id.$product->id;
		
		$pr[] = array('id' => $product->id, 'label' => $product->name . " (" . $product->code . ")",'row' => $product, 'tax_rate' => $tax_rate, 'units' => $units,'unique_id'=>$unique_item_id,'store_id'=>$this->store_id);

                $r++;
            }
            
            $this->sma->send_json($pr);
        } else {
            $this->sma->send_json(array(array('id' => 0, 'label' => lang('no_match_found'), 'value' => $term)));
        }
    }

    
    function getStoreIndentRequests(){
	$store_id = $this->input->get('store_id');
	$data = $this->indent_process_model->getStoreIndentRequests($store_id);
	$this->sma->send_json($data);
    }
    function getIndentRequestsData(){
	$store_id = $this->input->get('store_id');
	$indent_id =  $this->input->get('indent_id');
	$data = $this->indent_process_model->getIndentRequestsData($store_id,$indent_id);
	foreach ($data->items as $item) {
        $row = $this->siteprocurment->getItemByID($item->product_id);
		$unique_item_id = $this->store_id.$item->product_id;
		$item->id = $item->product_id;
		$item->name = $item->product_name;
		$item->code = $item->product_code;
		$item->type = $item->product_type;
		$item->qty = $item->quantity;		
		$pr[$unique_item_id] = array('id' => $item->id, 'label' => $item->name . " (" . $item->code . ")",'row' => $item, 'unique_id'=>$unique_item_id,'store_id'=>$this->store_id);
                $c++;
            }
	$data->req_items = $pr;
	$this->sma->send_json($data);
    }
    function LoadStock(){
		
	$store_ids = $this->input->post('processing_from');
	$i = count($_POST['product_id']);
	$products = array();
	for($r = 0; $r < $i; $r++){
	    $products[] = array(
		    'product_id' => $_POST["product_id"][$r],
		    'quantity' => $_POST['qty'][$r],
	    );
	    $products_id[] = $_POST["product_id"][$r];
	}
	$data = $this->indent_process_model->LoadStock($products_id,$store_ids);	
	$this->sma->send_json($data);
    }

}
