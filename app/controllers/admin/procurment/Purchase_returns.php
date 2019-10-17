<?php defined('BASEPATH') or exit('No direct script access allowed');

class Purchase_returns extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }
        if ($this->Customer) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER["HTTP_REFERER"]);
        }
        $this->lang->admin_load('procurment/purchase_returns', $this->Settings->user_language);
        $this->load->library('form_validation');
        $this->load->admin_model('procurment/purchase_returns_model');
        $this->digital_upload_path = 'assets/uploads/procurment/purchase_returns/';
	if (!file_exists($this->digital_upload_path)) {
		mkdir($this->digital_upload_path, 0777, true);
	}
        $this->upload_path = 'assets/uploads/';
        $this->thumbs_path = 'assets/uploads/thumbs/';
        $this->image_types = 'gif|jpg|jpeg|png|tif';
        $this->digital_file_types = 'zip|psd|ai|rar|pdf|doc|docx|xls|xlsx|ppt|pptx|gif|jpg|jpeg|png|tif|txt';
        $this->allowed_file_size = '1024';
        $this->data['logo'] = true;
		$this->Muser_id = $this->session->userdata('user_id');
		$this->Maccess_id = 8;
    }

    public function index($warehouse_id = null)
    {
         
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

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('purchase_returns')));
        $meta = array('page_title' => lang('purchase_returns'), 'bc' => $bc);
        $this->page_construct('procurment/purchase_returns/index', $meta, $this->data);

    }

    public function getPurchase_returns($warehouse_id = null)
    { 

               
        $detail_link = anchor('admin/procurment/purchase_returns/view/$1', '<i class="fa fa-file-text-o"></i> ' . lang('purchase_returns_details'));
        $payments_link = anchor('admin/procurment/purchase_returns/payments/$1', '<i class="fa fa-money"></i> ' . lang('view_payments'), 'data-toggle="modal" data-target="#myModal"');
        $add_payment_link = anchor('admin/procurment/purchase_returns/add_payment/$1', '<i class="fa fa-money"></i> ' . lang('add_payment'), 'data-toggle="modal" data-target="#myModal"');
        $email_link = anchor('admin/procurment/purchase_returns/email/$1', '<i class="fa fa-envelope"></i> ' . lang('email'), 'data-toggle="modal" data-target="#myModal"');
        $edit_link = anchor('admin/procurment/purchase_returns/edit/$1', '<i class="fa fa-edit"></i> ' . lang('edit'));
        $view_link = '<a href="'.admin_url('procurment/purchase_returns/view/$1').'" data-toggle="modal" data-target="#myModal"><i class="fa fa-edit"></i>'.lang('view_purchase_return').'</a>';
	$pdf_link = anchor('admin/procurment/purchase_returns/pdf/$1', '<i class="fa fa-file-pdf-o"></i> ' . lang('download_pdf'));
        $print_barcode = anchor('admin/procurment/products/print_barcodes/?purchase=$1', '<i class="fa fa-print"></i> ' . lang('print_barcodes'));
        $return_link = anchor('admin/procurment/purchase_returns/return_purchase/$1', '<i class="fa fa-angle-double-left"></i> ' . lang('return_purchase'));
        $delete_link = "<a href='#' class='po' title='<b>" . $this->lang->line("delete_quotation") . "</b>' data-content=\"<p>"
        . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('procurment/purchase_returns/delete/$1') . "'>"
        . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i> "
        . lang('delete') . "</a>";
        /*$action = '<div class="text-center"><div class="btn-group text-left">'
        . '<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">'
        . lang('actions') . ' <span class="caret"></span></button>
        <ul class="dropdown-menu pull-right" role="menu">
            <li>' . $detail_link . '</li>            
            <li>' . $edit_link . '</li>
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
// echo "string";exit;
        $this->load->library('datatables');
        if ($warehouse_id) {        
            
            $this->datatables
                ->select("id, DATE_FORMAT(date, '%Y-%m-%d %T') as date, reference_no, invoice_no, supplier, total, total_discount, total_tax, grand_total, status,attachment")
                ->from('pro_purchase_returns')
                ->where('warehouse_id', $warehouse_id);
        } else {
            // echo "sdsd";exit;
            $this->datatables
                 ->select("'sno',".$this->db->dbprefix('pro_purchase_returns') . ".id as id, DATE_FORMAT(".$this->db->dbprefix('pro_purchase_returns') . ".date, '%Y-%m-%d %T') as date, ".$this->db->dbprefix('pro_purchase_returns') . ".reference_no, ".$this->db->dbprefix('pro_purchase_invoices') . ".reference_no as invoice_no, ".$this->db->dbprefix('pro_purchase_returns') . ".supplier, ".$this->db->dbprefix('pro_purchase_returns') . ".total, ".$this->db->dbprefix('pro_purchase_returns') . ".total_discount, ".$this->db->dbprefix('pro_purchase_returns') . ".total_tax, ".$this->db->dbprefix('pro_purchase_returns') . ".grand_total, ".$this->db->dbprefix('pro_purchase_returns') . ".status as status,".$this->db->dbprefix('pro_purchase_returns') . ".attachment as attachment")
                ->from('pro_purchase_returns')
		->join('pro_purchase_invoices','pro_purchase_invoices.id=pro_purchase_returns.invoice_id','left');
                
        }
        // $this->datatables->where('status !=', 'returned');
        /*if (!$this->Customer && !$this->Supplier && !$this->Owner && !$this->Admin && !$this->session->userdata('view_right')) {
            $this->datatables->where('created_by', $this->session->userdata('user_id'));
        } elseif ($this->Supplier) {
            $this->datatables->where('customer_id', $this->session->userdata('user_id'));
        }*/
	$this->datatables->edit_column('attachment', '$1__$2', $this->digital_upload_path.', attachment');
        $this->datatables->add_column("Actions", $action, "id");

        echo $this->datatables->generate();
    }

    /* ----------------------------------------------------------------------------- */

     public function view($id = null)
    {
		
        $this->sma->checkPermissions();
	$store_id = $this->data['default_store'];
  
		 
        
	$this->data['orders'] =  $this->purchase_returns_model->getPurchase_returnsByID($id);
	$this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
	$this->data['suppliers'] = $this->siteprocurment->getAllCompanies('supplier');
	$this->data['categories'] = $this->siteprocurment->getAllCategories();
	$this->data['currencies'] = $this->siteprocurment->getAllCurrencies();
	$this->data['tax_rates'] = $this->siteprocurment->getAllTaxRates();
	$this->data['warehouses'] = $this->siteprocurment->getAllWarehouses();
	$this->data['purchaseorder'] = array();
	//$this->data['requestnumber'] = $this->siteprocurment->getAllQUOTESNUMBERedit();
	$this->data['order_items'] =   $this->purchase_returns_model->getAllPurchase_returnsItems($id); 
    //echo '<pre>';print_R($this->data['order_items']);exit;
      $c=1;
      foreach ($this->data['order_items'] as $item) {
		    
	  $row = $this->siteprocurment->getItemByID($item->product_id);
	  
		$row->name = $item->product_name;
		$row->id = $item->product_id;
		$row->code = $item->product_code;
		$row->received_quantity = $item->received_quantity;
                $row->qty = $item->quantity;
		$row->quantity_balance = $item->quantity;
		$row->batch_no = $item->batch_no;
		$row->expiry = $item->expiry;
		$row->expiry_type = $row->type_expiry;
		$row->unit_cost = $item->cost;
		$row->real_unit_cost = $item->cost;
                //$row->real_unit_cost = $item->gross;
		$row->item_discount_percent = $item->item_disc ? $item->item_disc : '0';
		$row->item_discount_amt = $item->item_disc_amt ? $item->item_disc_amt : '0';
		$row->item_dis_type = $item->item_dis_type;
		$row->item_bill_discount = $item->item_bill_disc_amt ? $item->item_bill_disc_amt : '0';
		$row->tax_rate = $item->tax_rate_id;
		$tax = $this->siteprocurment->getTaxRateByID($item->tax_rate_id);
		$row->tax_rate_val = $tax->rate;
                $row->item_selling_price =$item->selling_price;
		
		
		$row->base_unit = $row->unit ? $row->unit : $item->product_unit_id;
      
	  $options = array();

	  $units = $this->siteprocurment->getUnitsByBUID($row->base_unit);
	  $ri = $this->Settings->item_addition ? $row->id : $row->id;

	  $pr[$ri.'_'.$item->store_id] = array('id' => $c,'store_id'=>$item->store_id,'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")",
	      'row' => $item, 'tax_rate_val' => $row->tax_rate_val,'tax_rate' => $row->tax_rate, 'units' => $units, 'options' => $options);
	  $c++;
      }
	    //echo json_encode($pr);exit;
        $this->data['po_order_items'] = $pr;
         //echo '<pre>';print_R($this->data['po_order_items']);exit;
       
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('procurment/purchase_invoice'), 'page' => lang('purchase_invoice')), array('link' => '#', 'page' => lang('view')));
        $meta = array('page_title' => lang('view_purchase_invoice_details'), 'bc' => $bc);
	$this->load->view($this->theme . 'procurment/purchase_returns/view', $this->data);

    }

    /* ----------------------------------------------------------------------------- */

       public function add($purchase_invoices_id = null)
    {
        $this->sma->checkPermissions();
        $this->form_validation->set_rules('supplier', $this->lang->line("supplier"), 'required');
	
	
	$store_id = $this->data['default_store'];
        $this->session->unset_userdata('csrf_token');
        if ($this->form_validation->run() == true) {  
	    $n = $this->siteprocurment->lastidPurchaseReturn();
	    $reference = 'PR'.str_pad($n + 1, 5, 0, STR_PAD_LEFT);
	    //echo '<pre>';print_R($_POST);exit;
            $warehouse_id = $this->input->post('warehouse');
          
            $status = $this->input->post('status');                      
            $supplier_details = $this->siteprocurment->getCompanyByID($this->input->post('supplier'));
            $supplier = $supplier_details->company != '-'  ? $supplier_details->company : $supplier_details->name;
            $dateFormat = explode('-',$this->input->post('invoice_date'));
			$inv_date = $dateFormat[2].'-'.$dateFormat[1].'-'.$dateFormat[0];
            $data = array(
				'reference_no' => $reference,
                'invoice_id' => $this->input->post('invoice_id'),
				'date' => date('Y-m-d H:i:s'),
                'supplier_id' => $this->input->post('supplier'),
				'supplier' => $supplier,
               
                'warehouse_id' => $warehouse_id,		
				'invoice_date' =>  $inv_date,
                'note' => $this->sma->clear_tags($this->input->post('note')),
                'tax_method' => $this->input->post('tax_method'),
				'shipping' => $this->input->post('shipping_charge'),
				'bill_disc' => $this->input->post('bill_disc'),		
				'round_off' => $this->input->post('round_off'),
                
				'supplier_address' => $this->input->post('supplier_address'),
                'status' => $this->input->post('status'),
                'currency' => $this->input->post('currency'),
                'no_of_items' => $this->input->post('total_no_items'),
				'no_of_qty' => $this->input->post('total_no_qty'),
                'total' => $this->input->post('final_gross_amt'),
                'item_discount' => $this->input->post('item_disc'),
                'bill_disc_val' => $this->input->post('bill_disc_val'),               
				'sub_total' => $this->input->post('sub_total'),   
                'total_tax' => $this->input->post('tax'),
                'grand_total' => $this->input->post('net_amt'),                
                'created_by' => $this->session->userdata('user_id'),
				'created_on' => date('Y-m-d H:i:s'),
				'processed_by' => $this->session->userdata('user_id'),
				'processed_on' => date('Y-m-d H:i:s'),
				'total_discount' => $this->input->post('item_disc')+$this->input->post('bill_disc_val'),
            );
	    
	    if($status=="approved"){
		$data['approved_by'] = $this->session->userdata('user_id');
                $data['approved_on'] = date('Y-m-d H:i:s');
	    }
	    $items =  array();
	    if(isset($_POST['product'])){
		$p_count = count($_POST['product']);
		for($i=0;$i<$p_count;$i++){
		    $items[$i]['store_id'] = $this->input->post('store_id['.$i.']');
		    $items[$i]['product_id'] = $this->input->post('product_id['.$i.']');
		    $items[$i]['product_code'] = $this->input->post('product['.$i.']');
		    $items[$i]['product_name'] = $this->input->post('product_name['.$i.']');
		    $items[$i]['received_quantity'] = $this->input->post('received_quantity['.$i.']');
		    $items[$i]['quantity'] = $this->input->post('quantity['.$i.']');
		    $items[$i]['batch_no'] = $this->input->post('batch_no['.$i.']');
		    $items[$i]['expiry'] = $this->input->post('expiry['.$i.']');
		    $items[$i]['expiry_type'] = $this->input->post('expiry_type['.$i.']');
		    $items[$i]['cost'] = $this->input->post('unit_cost['.$i.']');
		    $items[$i]['gross'] = $this->input->post('unit_gross['.$i.']');
		    
		    $items[$i]['item_disc'] = $this->input->post('item_dis['.$i.']');
		    $items[$i]['item_dis_type'] = @$this->input->post('item_dis_type['.$i.']');
		    $items[$i]['item_disc_amt'] = $this->input->post('item_disc_amt['.$i.']');
		    
		    //$items[0]['item_bill_disc'] = $this->input->post('item_bill_disc['.$i.']');
		    $items[$i]['item_bill_disc_amt'] = $this->input->post('item_bill_disc_amt['.$i.']');		    
		    $items[$i]['total'] = $this->input->post('total['.$i.']');
		    $t_rate = $this->siteprocurment->getTaxRateByID($this->input->post('tax2['.$i.']'));
		    $items[$i]['tax_rate_id'] = $this->input->post('tax2['.$i.']');
		    $items[$i]['tax_rate'] = $t_rate->rate;
		    $items[$i]['tax'] = $this->input->post('item_tax['.$i.']');
		    $items[$i]['landing_cost'] = $this->input->post('landing_cost['.$i.']');
		    $items[$i]['selling_price'] = $this->input->post('selling_price['.$i.']');
		    //$items[$i]['margin'] = $this->input->post('margin['.$i.']');
		    $items[$i]['net_amt'] = $this->input->post('net_cost['.$i.']');
		    $items[$i]['category_id'] = $_POST['category_id'][$i];
		    $items[$i]['category_name'] = $_POST['category_name'][$i];
		    $items[$i]['subcategory_id'] = $_POST['subcategory_id'][$i];
		    $items[$i]['subcategory_name'] = $_POST['subcategory_name'][$i];
		    $items[$i]['brand_id'] = $_POST['brand_id'][$i];
		    $items[$i]['brand_name'] = $_POST['brand_name'][$i];
		    
		    
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
	    $pi_array = array();
	    if($this->input->post('invoice_id') != ''){
				$pi_array = array(
					'status' => 'completed',
				);
			}
		// echo '<pre>';print_R($data);print_R($items);print_R($pi_array);exit;
        }
		 
        if ($this->form_validation->run() == true && $this->purchase_returns_model->addPurchase_returns($data,$items,$pi_array)) {
            $this->session->set_userdata('remove_pols', 1);
            $this->session->set_flashdata('message', $this->lang->line("purchase_return_added"));
            admin_redirect('procurment/purchase_returns');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
	    
            $this->data['suppliers'] = $this->siteprocurment->getAllCompanies('supplier');
            $this->data['categories'] = $this->siteprocurment->getAllCategories();
            $this->data['currencies'] = $this->siteprocurment->getAllCurrencies();
            $this->data['tax_rates'] = $this->siteprocurment->getAllTaxRates();
            $this->data['warehouses'] = $this->siteprocurment->getAllWarehouses();
	    
            $this->data['purchaseinvoice'] = $this->siteprocurment->getAllInvoiceNumbers();
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('procurment/purchase_returns'), 'page' => lang('purchase_returns')), array('link' => '#', 'page' => lang('add_purchase_return')));
            $meta = array('page_title' => lang('add_purchase_return'), 'bc' => $bc);
            $this->page_construct('procurment/purchase_returns/add', $meta, $this->data);
        }
    }
    public function purchase_invoice_list(){
	    $poref =  $this->input->get('poref');
	    
	    $data['purchase_invoices'] = $this->purchase_returns_model->getPurchase_invoicesByID($poref);
	    $inv_items = $this->purchase_returns_model->getAllPurchase_invoicesItems_storeID($poref);
	    $c=1;
	   // echo '<pre>';print_R($inv_items);exit;
	    foreach ($inv_items as $item) {                          
        $row = $this->siteprocurment->getItemByID($item->product_id);
        $row->name = $item->product_name;
        $row->id = $item->product_id;
        $row->code = $item->product_code;
        $row->r_qty = $item->quantity;
        $row->qty = $item->quantity;
        $row->quantity_balance = $item->quantity;
        $row->batch_no = $item->batch_no;
        $row->expiry = $row->value_expiry;
        $row->expiry_type = $row->type_expiry;
        $row->unit_cost = $item->cost;
        $row->real_unit_cost = $item->cost;
        //$row->real_unit_cost = $item->gross;
        $row->item_discount_percent = $item->item_disc ? $item->item_disc : '0';
        $row->item_discount_amt = $item->item_disc_amt ? $item->item_disc_amt : '0';
        $row->item_dis_type = $item->item_dis_type;
        $row->item_bill_discount = $item->item_bill_disc_amt ? $item->item_bill_disc_amt : '0';
        $row->tax_rate = $item->item_tax_method;
        $tax = $this->siteprocurment->getTaxRateByID($item->item_tax_method);
        $row->tax_rate_val = $tax->rate;
        $row->item_selling_price =$item->selling_price;
        $row->category_name = $item->category_name;
        $row->subcategory_id = $item->subcategory_id;
        $row->subcategory_name = $item->subcategory_name;        
        $row->category_id = $item->category_id;
        $row->category_name = $item->category_name;
        $row->brand_id = $item->brand_id;
        $row->brand_name = $item->brand_name;
        $row->cost = $item->selling_price;
        $row->base_unit = $row->unit ? $row->unit : $item->product_unit_id;
        $options = $this->purchase_returns_model->getProductOptions($row->id);
        $units = $this->siteprocurment->getUnitsByBUID($row->base_unit);
        $ri = $this->Settings->item_addition ? $row->id : $row->id;
        $item_key = $ri.'_'.$item->store_id.'_'.$item->category_id.'_'.$item->subcategory_id.'_'.$item->brand_id;
        $pr[$item_key] = array('id' => $c,'store_id'=>$item->store_id,'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")",
        'row' => $row, 'tax_rate_id' => $item->item_tax_method,'tax_rate_val' => $item->tax_rate,'tax_rate' => $item->item_tax, 'units' => $units, 'options' => $options);
        $c++;
        }
		
		$data['purchase_invoicesitem'] = $pr;
		
		
		
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

	
    /* ------------------------------------------------------------------------------------- */
     public function edit($id = null)
    {
        $this->sma->checkPermissions();
        $this->form_validation->set_rules('supplier', $this->lang->line("supplier"), 'required');
	
	$store_id = $this->data['default_store'];
        $this->session->unset_userdata('csrf_token');
	$this->data['inv'] = $this->purchase_returns_model->getPurchase_returnsByID($id);
	if ($this->data['inv']->status == 'approved' || $this->data['inv']->status == 'completed') {
		$this->session->set_flashdata('error', lang("Do not allowed edit option"));
		admin_redirect("procurment/purchase_returns");
	}
        if ($this->form_validation->run() == true) {  

	    		
            $warehouse_id = $this->input->post('warehouse');
          
            $status = $this->input->post('status');                      
           
	   $dateFormat = explode('-',$this->input->post('invoice_date'));
	   $inv_date = $dateFormat[2].'-'.$dateFormat[1].'-'.$dateFormat[0];
           
            $data = array(
		'reference_no' => $this->input->post('reference_no'),
                'invoice_id' => $this->input->post('invoice_id'),
		'date' => date('Y-m-d H:i:s'),
                'supplier_id' => $this->input->post('supplier'),
                //'supplier' => $supplier,
                
                'warehouse_id' => $warehouse_id,		
		'invoice_date' =>  $inv_date,
                'note' => $this->sma->clear_tags($this->input->post('note')),
                'tax_method' => $this->input->post('tax_method'),
		'shipping' => $this->input->post('shipping_charge'),
		'bill_disc' => $this->input->post('bill_disc'),		
		'round_off' => $this->input->post('round_off'),
                
		'supplier_address' => $this->input->post('supplier_address'),
                'status' => $this->input->post('status'),
                'currency' => $this->input->post('currency'),
                'no_of_items' => $this->input->post('total_no_items'),
		'no_of_qty' => $this->input->post('total_no_qty'),
                'total' => $this->input->post('final_gross_amt'),
                'item_discount' => $this->input->post('item_disc'),
                'bill_disc_val' => $this->input->post('bill_disc_val'),               
		'sub_total' => $this->input->post('sub_total'),   
                'total_tax' => $this->input->post('tax'),
                'grand_total' => $this->input->post('net_amt'),                
                'updated_by' => $this->session->userdata('user_id'),
		'updated_on' => date('Y-m-d H:i:s'),
		'total_discount' => $this->input->post('item_disc')+$this->input->post('bill_disc_val'),
            );
	    if($status=="approved"){
		$data['approved_by'] = $this->session->userdata('user_id');
                $data['approved_on'] = date('Y-m-d H:i:s');
	    }
            $items =  array();
	    if(isset($_POST['product'])){
		$p_count = count($_POST['product']);
		for($i=0;$i<$p_count;$i++){
		    //$items[$i]['invoice_reference_no'] = $this->input->post('reference_no');
		    $items[$i]['store_id'] = $this->input->post('store_id['.$i.']');
		    $items[$i]['product_id'] = $this->input->post('product_id['.$i.']');
		    $items[$i]['product_code'] = $this->input->post('product['.$i.']');
		    $items[$i]['product_name'] = $this->input->post('product_name['.$i.']');
		    $items[$i]['last_updated_quantity'] = $this->input->post('quantity_balance['.$i.']');
		    $items[$i]['received_quantity'] = $this->input->post('received_quantity['.$i.']');
		    $items[$i]['quantity'] = $this->input->post('quantity['.$i.']');
		    $items[$i]['batch_no'] = $this->input->post('batch_no['.$i.']');
		    $items[$i]['expiry'] = $this->input->post('expiry['.$i.']');		    
		    $items[$i]['expiry_type'] = $this->input->post('expiry_type['.$i.']');
		    $items[$i]['cost'] = $this->input->post('unit_cost['.$i.']');
		    $items[$i]['gross'] = $this->input->post('unit_gross['.$i.']');
		    
		    $items[$i]['item_disc'] = $this->input->post('item_dis['.$i.']');
		    $items[$i]['item_dis_type'] = @$this->input->post('item_dis_type['.$i.']');
		    $items[$i]['item_disc_amt'] = $this->input->post('item_disc_amt['.$i.']');
		    
		    //$items[0]['item_bill_disc'] = $this->input->post('item_bill_disc['.$i.']');
		    $items[$i]['item_bill_disc_amt'] = $this->input->post('item_bill_disc_amt['.$i.']');		    
		    $items[$i]['total'] = $this->input->post('total['.$i.']');		    
		    $t_rate = $this->siteprocurment->getTaxRateByID($this->input->post('tax2['.$i.']'));
		    $items[$i]['tax_rate_id'] = $this->input->post('tax2['.$i.']');
		    $items[$i]['tax_rate'] = $t_rate->rate;
		    $items[$i]['tax'] = $this->input->post('item_tax['.$i.']');
		    $items[$i]['landing_cost'] = $this->input->post('landing_cost['.$i.']');
		    $items[$i]['selling_price'] = $this->input->post('selling_price['.$i.']');
		    //$items[$i]['margin'] = $this->input->post('margin['.$i.']');
		    $items[$i]['net_amt'] = $this->input->post('net_cost['.$i.']');
		    $items[$i]['category_id'] = $_POST['category_id'][$i];
		    $items[$i]['category_name'] = $_POST['category_name'][$i];
		    $items[$i]['subcategory_id'] = $_POST['subcategory_id'][$i];
		    $items[$i]['subcategory_name'] = $_POST['subcategory_name'][$i];
		    $items[$i]['brand_id'] = $_POST['brand_id'][$i];
		    $items[$i]['brand_name'] = $_POST['brand_name'][$i];
		    
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
		@unlink($this->digital_upload_path.$inv->attachment);
            }
	     $pi_array = array();
	    if($this->input->post('invoice_id') != ''){
				$pi_array = array(
					'status' => 'completed',
				);
			}
		//print_R($po_array);exit;
        }
		 
        if ($this->form_validation->run() == true && $this->purchase_returns_model->updatePurchase_returns($id,$data,$items,$pi_array)) {
            
            $this->session->set_userdata('remove_pols', 1);
            $this->session->set_flashdata('message', $this->lang->line("purchase_return_updated"));
            admin_redirect('procurment/purchase_returns');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['suppliers'] = $this->siteprocurment->getAllCompanies('supplier');
            $this->data['categories'] = $this->siteprocurment->getAllCategories();
            $this->data['currencies'] = $this->siteprocurment->getAllCurrencies();
            $this->data['tax_rates'] = $this->siteprocurment->getAllTaxRates();
            $this->data['warehouses'] = $this->siteprocurment->getAllWarehouses();
	    $this->data['purchaseinvoice'] = $this->siteprocurment->getAllInvoiceNumbers_edit($this->data['inv']->invoice_id);
            $this->data['inv_items'] = $this->purchase_returns_model->getAllPurchase_returnsItems($id);
	  
	    $c=1;
	    foreach ($this->data['inv_items'] as $item) {
                          
                $row = $this->siteprocurment->getItemByID($item->product_id);
		$row->name = $item->product_name;
		$row->id = $item->product_id;
		$row->code = $item->product_code;
                $row->r_qty = $item->received_quantity;
		$row->qty = $item->quantity;
		$row->quantity_balance = $item->quantity;
		$row->batch_no = $item->batch_no;
		$row->expiry = $item->expiry;
		$row->expiry_type = $row->type_expiry;
		$row->unit_cost = $item->cost;
		$row->real_unit_cost = $item->cost;
                //$row->real_unit_cost = $item->gross;
		$row->item_discount_percent = $item->item_disc ? $item->item_disc : '0';
		$row->item_discount_amt = $item->item_disc_amt ? $item->item_disc_amt : '0';
		$row->item_dis_type = $item->item_dis_type;
		$row->item_bill_discount = $item->item_bill_disc_amt ? $item->item_bill_disc_amt : '0';
		$row->tax_rate = $item->tax_rate_id;
		$tax = $this->siteprocurment->getTaxRateByID($item->tax_rate_id);
		$row->tax_rate_val = $tax->rate;
                $row->item_selling_price =$item->selling_price;
		
		
		$row->base_unit = $row->unit ? $row->unit : $item->product_unit_id;
		$row->category_id = $item->category_id;
                $row->category_name = $item->category_name;
		$row->subcategory_id = $item->subcategory_id;
                $row->subcategory_name = $item->subcategory_name;
		$row->brand_id = $item->brand_id;
                $row->brand_name = $item->brand_name;
//                $row->base_unit_cost = $row->cost ? $row->cost : $item->unit_price;
//                $row->unit = $item->product_unit_id;
//                $row->oqty = $item->quantity;                
//                
//                
//                
               $options = $this->purchase_returns_model->getProductOptions($row->id);
//		
//		$row->mfg = $item->item_mfg;
//		$row->days = $item->item_days;
//                $row->option = $item->option_id;
//                
//                $row->cost = $this->sma->formatDecimal($item->net_unit_price + ($item->item_discount / $item->quantity));
//               
//                
//		$row->tax_method = $item->item_tax_method ? $item->item_tax_method : 0;
//                unset($row->details, $row->product_details, $row->price, $row->file, $row->product_group_id);
                $units = $this->siteprocurment->getUnitsByBUID($row->base_unit);
//                $tax_rate = $this->siteprocurment->getTaxRateByID($row->tax_rate);
                $ri = $this->Settings->item_addition ? $row->id : $row->id;
		$item_key = $ri.'_'.$item->store_id.'_'.$item->category_id.'_'.$item->subcategory_id.'_'.$item->brand_id;
                $pr[$item_key] = array('id' => $c,'store_id'=>$item->store_id,'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")",
                    'row' => $row, 'tax_rate_val' => $item->tax_rate,'tax_rate_id' => $item->tax_rate_id,'tax_rate' => $item->tax_rate, 'units' => $units, 'options' => $options);
                $c++;
		//echo json_encode($pr);exit;
            }

            $this->data['json_inv_items'] = json_encode($pr);
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('procurment/purchase_returns'), 'page' => lang('purchase_returns')), array('link' => '#', 'page' => lang('edit_purchase_return')));
            $meta = array('page_title' => lang('edit_purchase_return'), 'bc' => $bc);
            $this->page_construct('procurment/purchase_returns/edit', $meta, $this->data);
        }
    }
    /* ----------------------------------------------------------------------------------------------------------- */

    public function purchase_invoices_by_csv()
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
                    admin_redirect("procurment/purchase_invoices/purchase_invoices_by_csv");
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

                        if ($product_details = $this->purchase_returns_model->getProductByCode($csv_pr['code'])) {

                            if ($csv_pr['variant']) {
                                $item_option = $this->purchase_returns_model->getProductVariantByName($csv_pr['variant'], $product_details->id);
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
                            $tax_details = ((isset($item_tax_rate) && !empty($item_tax_rate)) ? $this->purchase_returns_model->getTaxRateByName($item_tax_rate) : $this->siteprocurment->getTaxRateByID($product_details->tax_rate));
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

        if ($this->form_validation->run() == true && $this->purchase_returns_model->addPurchase($data, $products)) {

            $this->session->set_flashdata('message', $this->lang->line("purchase_invoices_added"));
            admin_redirect("procurment/purchase_invoices");
        } else {

            $data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['warehouses'] = $this->siteprocurment->getAllWarehouses();
            $this->data['tax_rates'] = $this->siteprocurment->getAllTaxRates();
            $this->data['ponumber'] = ''; // $this->siteprocurment->getReference('po');

            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('procurment/purchase_invoices'), 'page' => lang('purchase_invoices')), array('link' => '#', 'page' => lang('add_purchase_invoices_by_csv')));
            $meta = array('page_title' => lang('add_purchase_invoices_by_csv'), 'bc' => $bc);
            $this->page_construct('procurment/purchase_invoices/purchase_invoices__orderby_csv', $meta, $this->data);

        }
    }

    /* --------------------------------------------------------------------------- */

   public function delete($id = null)
    {
        //$this->sma->checkPermissions(null, true);

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        if ($this->purchase_returns_model->deletePurchase_invoices($id)) {

            if ($this->input->is_ajax_request()) {
                $this->sma->send_json(array('error' => 0, 'msg' => lang("purchase_invoices_deleted")));
            }
            $this->session->set_flashdata('message', lang('purchase_invoices_deleted'));
            admin_redirect('procurment/welcome');
        }
    }
    
    /* --------------------------------------------------------------------------- */

    public function suggestions()
    {
        $term = $this->input->get('term', true);
        $supplier_id = $this->input->get('supplier_id', true);

        if (strlen($term) < 1 || !$term) {
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . admin_url('procurment/welcome') . "'; }, 10);</script>");
        }

        $analyzed = $this->sma->analyze_term($term);
        $sr = $analyzed['term'];
        $option_id = $analyzed['option_id'];

        $rows = $this->siteprocurment->getProductNames($sr);
		
        if ($rows) {
            $c = str_replace(".", "", microtime(true));
            $r = 0;
            foreach ($rows as $row) {
                $option = false;
                $row->item_tax_method = $row->tax_method;
                $options = $this->purchase_returns_model->getProductOptions($row->id);
                if ($options) {
                    $opt = $option_id && $r == 0 ? $this->purchase_returns_model->getProductOptionByID($option_id) : current($options);
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
                $row->unit_cost = $row->purchase_cost;
                $row->real_unit_cost = $row->cost;
                $row->base_quantity = 1;
                $row->base_unit = $row->unit;
                $row->base_unit_cost = $row->cost;
                $row->unit = $row->purchase_unit ? $row->purchase_unit : $row->unit;
                $row->new_entry = 1;
                $row->expiry = '';
                $row->qty = 1;
                $row->quantity_balance = '';
                $row->item_discount_percent = '0';
                $row->item_discount_amt = '0';
                $row->item_bill_discount = '0';
                $row->item_tax_rate = '0';
                $row->item_selling_price = '0';
                unset($row->details, $row->product_details, $row->price, $row->file, $row->supplier1price, $row->supplier2price, $row->supplier3price, $row->supplier4price, $row->supplier5price, $row->supplier1_part_no, $row->supplier2_part_no, $row->supplier3_part_no, $row->supplier4_part_no, $row->supplier5_part_no);

                $units = $this->siteprocurment->getUnitsByBUID($row->base_unit);
                $tax_rate = $this->siteprocurment->getTaxRateByID($row->tax_rate);
		$label = $row->name . " (" . $row->code . ") CAT - ".$row->category_name." | SUBCAT - ".$row->subcategory_name." | BRAND - ".$row->brand_name;
                $pr[] = array('id' => ($c + $r), 'item_id' => $row->id, 'label' => $label,
                    'row' => $row, 'tax_rate' => $tax_rate, 'units' => $units, 'options' => $options);
                $r++;
            }
            $this->sma->send_json($pr);
        } else {
            $this->sma->send_json(array(array('id' => 0, 'label' => lang('no_match_found'), 'value' => $term)));
        }
    }

    /* -------------------------------------------------------------------------------- */
    function isUniqueInvoice($invoice_no,$value){
	$value = explode('.',$value);
	$supplier_id = $value[0];
	$edit_id = @$value[1];
        if($this->purchase_returns_model->isInvoiceExist($invoice_no,$supplier_id,$edit_id)){
	    $this->form_validation->set_message('isUniqueInvoice', lang('invoice_no_already_exist_for_this_supplier'));
            return false;
        }
        return true;
    }
    function ust(){
	$d = $this->siteprocurment->saleStockIn(2,0,174);
	echo '<pre>';print_R($d);
    }
}
