<?php defined('BASEPATH') or exit('No direct script access allowed');

class Grn extends MY_Controller{
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
        $this->lang->admin_load('procurment/grn', $this->Settings->user_language);
        $this->load->library('form_validation');
        $this->load->admin_model('procurment/grn_model');
        $this->digital_upload_path = 'assets/uploads/procurment/Grn/';
        $this->digital_file_types = 'zip|psd|ai|rar|pdf|doc|docx|xls|xlsx|ppt|pptx|gif|jpg|jpeg|png|tif|txt';
        $this->allowed_file_size = '1024';
	    if (!file_exists($this->digital_upload_path)) {
		   mkdir($this->digital_upload_path, 0777, true);
	    }
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

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('goods_received_note')));
        $meta = array('page_title' => lang('goods_received_note'), 'bc' => $bc);
        $this->page_construct('procurment/grn/index', $meta, $this->data);

    }

    public function getGrn($warehouse_id = null){
        if ((!$this->Owner || !$this->Admin) && !$warehouse_id) {
            $user = $this->siteprocurment->getUser();
            $warehouse_id = $user->warehouse_id;
        }
	    $view_link = '<a href="'.admin_url('procurment/grn/view/$1').'" data-toggle="modal" data-target="#myModal"><i class="fa fa-edit"></i>'.lang('view_GRN').'</a>';
        $detail_link = anchor('admin/procurment/grn/view/$1/', '<i class="fa fa-file-text-o"></i> ' . lang('GRN_details'));
		$edit_link = anchor('admin/procurment/grn/edit/$1', '<i class="fa fa-edit"></i> ' . lang('edit_GRN'));
        $delete_link = "<a href='#' class='po' title='<b>" . $this->lang->line("delete_GRN") . "</b>' data-content=\"<p>"
        . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('procurment/grn/delete/$1') . "'>"
        . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i> "
        . lang('delete_GRN') . "</a>";
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
                ->select("pro_grn.id, pro_grn.date, pro_grn.reference_no,  pro_grn.invoice_date,pro_grn.invoice_referenceno, pro_grn.status")
                ->from('pro_grn')
                ->where('pro_grn.store_id', $warehouse_id);
        } else {
            $this->datatables
                ->select("pro_grn.id, pro_grn.date, pro_grn.reference_no,pro_grn.invoice_date,pro_grn.invoice_referenceno,pro_grn.status")
                ->from('pro_grn')
				 ->where('pro_grn.store_id',$this->store_id);
        }
      
	    $this->datatables->edit_column('attachment', '$1__$2', $this->digital_upload_path.', attachment');
        $this->datatables->add_column("Actions", $action, "pro_grn.id,pro_grn.status");
        echo $this->datatables->generate();
    }

    public function modal_view($store_request_id = null){
        if ($this->input->get('id')) {
            $store_request_id = $this->input->get('id');
        }
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $inv = $this->grn_model->getStore_requestByID($store_request_id);
        if (!$this->session->userdata('view_right')) {
            $this->sma->view_rights($inv->created_by, true);
        }
        $this->data['rows'] = $this->grn_model->getAllStore_requestItems($store_request_id);
        $this->data['customer'] = $this->siteprocurment->getCompanyByID($inv->customer_id);
        $this->data['biller'] = $this->siteprocurment->getCompanyByID($inv->biller_id);
        $this->data['created_by'] = $this->siteprocurment->getUser($inv->created_by);
        $this->data['updated_by'] = $inv->updated_by ? $this->siteprocurment->getUser($inv->updated_by) : null;
        $this->data['warehouse'] = $this->siteprocurment->getWarehouseByID($inv->warehouse_id);
        $this->data['inv'] = $inv;
        $this->load->view($this->theme . 'grn/modal_view', $this->data);
    }

    public function view($store_request_id = null){
	        $this->sma->checkPermissions();
	        $id = $store_request_id;
	        $this->data['store_req'] = $this->grn_model->getStore_requestByID($id);
            $inv_items = $this->grn_model->getAllStore_requestItems($id);
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
                $row->quantity = 0;
                $pis = $this->siteprocurment->getPurchasedItems($item->product_id, $item->warehouse_id, $item->option_id);
                if ($pis) {
                    foreach ($pis as $pi) {
                        $row->quantity += $pi->quantity_balance;
                    }
                }
                $row->id = $item->product_id;
                $row->code = $item->product_code;
                $row->name = $item->product_name;
                $row->type = $item->product_type;
                $row->base_quantity = $item->quantity;
                $row->base_unit = $row->unit ? $row->unit : $item->product_unit_id;
                $row->base_unit_price = $row->price ? $row->price : $item->unit_price;
                $row->unit = $item->product_unit_id;
                $row->qty = $item->quantity;
                $row->discount = $item->discount ? $item->discount : '0';
                $row->price = $this->sma->formatDecimal($item->net_unit_price + $this->sma->formatDecimal($item->item_discount / $item->quantity));
                $row->unit_price = $row->tax_method ? $item->unit_price + $this->sma->formatDecimal($item->item_discount / $item->quantity) + $this->sma->formatDecimal($item->item_tax / $item->quantity) : $item->unit_price + ($item->item_discount / $item->quantity);
                $row->real_unit_price = $item->real_unit_price;
                $row->tax_rate = $item->tax_rate_id;
                $row->option = $item->option_id;
                $options = $this->grn_model->getProductOptions($row->id, $item->warehouse_id);
                if ($options) {
                    $option_quantity = 0;
                    foreach ($options as $option) {
                        $pis = $this->siteprocurment->getPurchasedItems($row->id, $item->warehouse_id, $item->option_id);
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
                $combo_items = false;
                if ($row->type == 'combo') {
                    $combo_items = $this->grn_model->getProductComboItems($row->id, $item->warehouse_id);
                    foreach ($combo_items as $combo_item) {
                        $combo_item->quantity = $combo_item->qty * $item->quantity;
                    }
                }
                $units = $this->siteprocurment->getUnitsByBUID($row->base_unit);
                $tax_rate = $this->siteprocurment->getTaxRateByID($row->tax_rate);
                $ri = $this->Settings->item_addition ? $row->id : $c;

                $pr[$ri] = array('id' => $c, 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'row' => $item, 'combo_items' => $combo_items, 'tax_rate' => $tax_rate, 'units' => $units, 'options' => $options);
                $c++;
            }
            $this->data['store_req_items'] = $pr;
            $this->data['id'] = $id;
	        $this->data['stores'] = $this->siteprocurment->getAllStores();
            $this->data['billers'] = ($this->Owner || $this->Admin || !$this->session->userdata('biller_id')) ? $this->siteprocurment->getAllCompanies('biller') : null;
            $this->data['tax_rates'] = $this->siteprocurment->getAllTaxRates();
            $this->data['warehouses'] = ($this->Owner || $this->Admin || !$this->session->userdata('warehouse_id')) ? $this->siteprocurment->getAllWarehouses() : null;
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('procurment/store_request'), 'page' => lang('store_request')), array('link' => '#', 'page' => lang('view_store_request')));
            $meta = array('page_title' => lang('view_store_request'), 'bc' => $bc);
            $this->load->view($this->theme . 'procurment/grn/view',  $this->data);

    }

   

 
    public function add(){
            $this->sma->checkPermissions();
		    $this->form_validation->set_rules('pi_number', $this->lang->line("pi_number"), 'required');
			
         if ($this->form_validation->run() == true) {
			$n = $this->siteprocurment->lastidGrn();
			$reference = 'GRN'.str_pad($n + 1, 5, 0, STR_PAD_LEFT);
			$date = date('Y-m-d H:i:s');
			$supplier_id = $this->input->post('supplier');
			$status = $this->input->post('status');
          if ($supplier_id) {
                $supplier_details = $this->siteprocurment->getCompanyByID($supplier_id);
                $supplier = $supplier_details->company != '-' ? $supplier_details->company : $supplier_details->name;
           } else {
                $supplier = NULL;
           }
            $note = $this->sma->clear_tags($this->input->post('note'));
			$pi=$this->grn_model->getpi($_POST['pi_number']);
            $i = isset($_POST['product_code']) ? sizeof($_POST['product_code']) : 0;
            for ($r = 0; $r < $i; $r++) {
				$unit = $this->site->getUnitByID($_POST['product_unit'][$r]);
               if (!empty($_POST['product_code'][$r])) {
                    $products[] = array(
					     'store_id'     =>$_POST['store_id'][$r],
					    'product_id'      => $_POST['product_id'][$r],
					   'variant_id'       => $_POST['product_option'][$r],
                        'product_code'    => $_POST['product_code'][$r],
                        'product_name'    => $_POST['product_name'][$r],
						'batch_no'        => ($_POST['batch'][$r])?$_POST['batch'][$r]:'',
                        'expiry'          => ($_POST['expiry'][$r])?$_POST['expiry'][$r]:'',
                        'quantity'        => ($_POST['quantity'][$r])?$_POST['quantity'][$r]:0,
						'pi_qty'          => ($_POST['pi_qty'][$r])?$_POST['pi_qty'][$r]:0,
						'landing_cost'	  => ($_POST['landing_cost'][$r])?$_POST['landing_cost'][$r]:0,
						'selling_price'   => ($_POST['selling_price'][$r])?$_POST['selling_price'][$r]:0,
					//	'margin'          => $_POST['store_id'][$r],
						//'net_amt'         => $_POST['store_id'][$r],
						
						'product_unit_id' => $_POST['product_unit'][$r],
						'unit_quantity'   => $_POST['product_base_quantity'][$r],
						'product_unit_code'=>$unit->code,
						'expiry_type'      =>$_POST['expiry_type'][$r],
						'category_id'      =>$_POST['category_id'][$r],
						'category_name'    =>$_POST['category_name'][$r],
						'subcategory_id'   =>$_POST['subcategory_id'][$r],
						'subcategory_name' =>$_POST['subcategory_name'][$r],
						'brand_id'         =>$_POST['brand_id'][$r],
						'brand_name'       =>$_POST['brand_name'][$r],
						'cm_id '           =>$_POST['cm_id'][$r]
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
            $data = array(
				'reference_no' => $reference,
				'date' => $date,
				'store_id' =>$this->store_id,
				'invoice_id'=>$pi->id,
				'invoice_date'=>$pi->date,
				'invoice_referenceno'=>$pi->reference_no,
				'invoice_amt'=>$pi->invoice_amt,
                'note' => $note,
                'status' => $status,
                'created_by' => $this->session->userdata('user_id'),
				'approved_by' => $approved_by ? $approved_by : 0,
				'approved_on' => $date,
				'created_on' => date('Y-m-d H:i:s'),
				'no_of_items' => $this->input->post('titems'),
				'no_of_qty' => $this->input->post('total_items'),
				'created_by' => $this->session->userdata('user_id'),
				'created_on' => date('Y-m-d H:i:s'),
				'processed_by' => $this->session->userdata('user_id'),
				'processed_on' => date('Y-m-d H:i:s'),
				'delivery_address'=>$this->input->post('delivery_address'),
				'supplier_id' =>$supplier_id,
				'supplier'   =>$supplier,
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
			   if($status=="approved"){
		     $data['approved_by'] = $this->session->userdata('user_id');
             $data['approved_on'] = date('Y-m-d H:i:s');
	    }
        }

        if ($this->form_validation->run() == true && $this->grn_model->addGrn($data, $products)) {
            $this->session->set_userdata('remove_quls', 1);
            $this->session->set_flashdata('message', $this->lang->line("grn_added"));
            admin_redirect('procurment/grn');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['billers'] = ($this->Owner || $this->Admin || !$this->session->userdata('biller_id')) ? $this->siteprocurment->getAllCompanies('biller') : null;
            $this->data['warehouses'] = ($this->Owner || $this->Admin || !$this->session->userdata('warehouse_id')) ? $this->siteprocurment->getAllWarehouses() : null;
			$this->data['invoicelist'] =$this->grn_model->getPurchase_invoicelist();
			$this->data['suppliers'] = $this->siteprocurment->getAllCompanies('supplier');
			$this->data['currencies'] = $this->siteprocurment->getAllCurrencies();
			$this->data['tax_rates'] = $this->siteprocurment->getAllTaxRates();
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('procurment/grn'), 'page' => lang('GRN')), array('link' => '#', 'page' => lang('add_GRN')));
            $meta = array('page_title' => lang('add_GRN'), 'bc' => $bc);
            $this->page_construct('procurment/grn/add', $meta, $this->data);
        }
    }

    public function edit($id = null){
        $this->sma->checkPermissions();
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $inv = $this->grn_model->getGRNById($id);
		if ($inv->status == 'approved' || $inv->status == 'completed') {
			$this->session->set_flashdata('error', lang("Do not allowed edit option"));
			admin_redirect("procurment/grn");
		}	
        $this->form_validation->set_message('is_natural_no_zero', $this->lang->line("no_zero_required"));
		$this->form_validation->set_rules('request_type', $this->lang->line("request_type"), 'required');
        $this->form_validation->set_rules('warehouse', $this->lang->line("warehouse"), 'required');
        if ($this->form_validation->run() == true) {
            $warehouse_id = $this->input->post('warehouse');
            $biller_id = $this->input->post('biller');
			$from_store_id = $this->input->post('from_store_id');
			$to_store_id = $this->input->post('to_store_id');
            $supplier_id = $this->input->post('supplier');
            $status = $this->input->post('status');
            $shipping = $this->input->post('shipping') ? $this->input->post('shipping') : 0;
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
               $unit = $this->site->getUnitByID($_POST['product_unit'][$r]);
		        $store_id = $from_store_id;
                $item_id = $_POST['product_id'][$r];
                $item_type = $_POST['product_type'][$r];
                $item_code = $_POST['product_code'][$r];
                $item_name = $_POST['product_name'][$r];
                $item_unit_quantity = $_POST['quantity'][$r];
                $item_unit = $_POST['product_unit'][$r];
				$quantity = $_POST['quantity'][$r];
                $unit_quantity = $_POST['product_base_quantity'][$r];
		        $category_id = $_POST['category_id'][$r];
                $category_name = $_POST['category_name'][$r];
		        $subcategory_id = $_POST['subcategory_id'][$r];
                $subcategory_name = $_POST['subcategory_name'][$r];
		        $brand_id = $_POST['brand_id'][$r];
				$brand_id = $_POST['brand_id'][$r];
                $brand_name = $_POST['brand_name'][$r];
				$option_id = $_POST['product_option'][$r];
				$product_option=$this->grn_model->getrecipeOptionByID($option_id);
               if (!empty($item_code)) {
                    $product_details = $item_type != 'manual' ? $this->grn_model->getProductByCode($item_code) : null;
                    $products[] = array(
                        'product_id'      => $item_id,
                        'product_code'    => $item_code,
                        'product_name'    => $item_name,
                        'product_type'    => $item_type,
                        'quantity'        => $quantity,
						'unit_quantity'   => $unit_quantity,
                        'product_unit_id' => $item_unit,
						'product_unit_code'=>$unit->name,
						'option_id'=>$option_id,
						'option_name'=>$product_option->name
                    );

                }
            }
            if (empty($products)) {
                $this->form_validation->set_rules('product', lang("order_items"), 'required');
            } else {
                $products;
            }
            $data = array(
				'request_type' => $this->input->post('request_type'),
                'warehouse_id' => $warehouse_id,
                'note' => $note,
                'status' => $status,
                'updated_by' => $this->session->userdata('user_id'),
				'total_no_items' =>$this->input->post('titems'),
				'total_no_qty'=>$this->input->post('total_items')
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
	          	@unlink($this->digital_upload_path.$inv->attachment);
            }
        }
        if ($this->form_validation->run() == true && $this->grn_model->updateStore_request($id, $data, $products)) {
            $this->session->set_userdata('remove_quls', 1);
            $this->session->set_flashdata('message', $this->lang->line("store_request_added"));
            admin_redirect('procurment/grn');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['inv'] = $this->grn_model->getGRNById($id);
            $grn_items = $this->grn_model->getGRNIitemById($id);
            krsort($grn_items);
            $c = rand(100000, 9999999);
            foreach ($grn_items as $item) {
                $row = $this->siteprocurment->getRecipeByID($item->product_id);
                if (!$row) {
                    $row = json_decode('{}');
                    $row->tax_method = 0;
                } else {
                    unset($row->details, $row->cost, $row->supplier1price, $row->supplier2price, $row->supplier3price, $row->supplier4price, $row->supplier5price);
                }
                $row->quantity = 0;
                $pis = $this->siteprocurment->getPurchasedItems($item->product_id, $item->warehouse_id, $item->option_id);
                if ($pis) {
                    foreach ($pis as $pi) {
                        $row->quantity += $pi->quantity_balance;
                    }
                }
                $row->id            = $item->product_id;
                $row->code          = $item->product_code;
                $row->name          = $item->product_name;
                $row->type          = $item->product_type;
                $row->base_quantity = $item->quantity;
                $row->base_unit     = $row->unit ? $row->unit : $item->product_unit_id;
                $row->base_unit_price = $row->price ? $row->price : $item->unit_price;
                $row->unit = $item->product_unit_id;
                $row->qty = $item->quantity;
                $row->discount = $row->discount ? $row->discount : '0';
                $row->real_unit_price = $row->real_unit_price;
                $row->tax_rate = $row->tax_rate_id;
                $row->option = $row->option_id;
		        $row->category_id = $row->category_id;
                $row->category_name = $row->category_name;
		        $row->subcategory_id = $row->subcategory_id;
                $row->subcategory_name = $row->subcategory_name;
		        $row->brand_id = $row->brand_id;
                $row->brand_name = $row->brand_name;
		        $row->purchase_cost = $row->cost_price;
                $row->cost = $row->selling_price;
				$row->option_id = ($item->option_id)?$item->option_id:0;
                $options = $this->grn_model->getProductOptions($row->id, $item->warehouse_id);
                if ($options) {
                    $option_quantity = 0;
                    foreach ($options as $option) {
                        $pis = $this->siteprocurment->getPurchasedItems($row->id, $item->warehouse_id, $item->option_id);
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

                $combo_items = false;
                if ($row->type == 'combo') {
                    $combo_items = $this->grn_model->getProductComboItems($row->id, $item->warehouse_id);
                    foreach ($combo_items as $combo_item) {
                        $combo_item->quantity = $combo_item->qty * $item->quantity;
                    }
                }
                $units = $this->siteprocurment->getUnitsByBUID($row->base_unit);
                $tax_rate = $this->siteprocurment->getTaxRateByID($row->tax_rate);
                $ri = $row->id ? $row->id : $c;
		        $item_key = $ri.'_'.$row->category_id.'_'.$row->subcategory_id.'_'.$row->brand_id.'_'.$row->option_id;
                $pr[$item_key] = array('id' => $c, 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'row' => $row, 'combo_items' => $combo_items, 'tax_rate' => $tax_rate, 'units' => $units, 'options' => $options);
				
                $c++;
            }
            $this->data['grn_items'] = json_encode($pr);
            $this->data['id'] = $id;
            $this->data['tax_rates'] = $this->siteprocurment->getAllTaxRates();
            $this->data['warehouses'] = ($this->Owner || $this->Admin || !$this->session->userdata('warehouse_id')) ? $this->siteprocurment->getAllWarehouses() : null;
			$this->data['invoicelist'] =$this->grn_model->getPurchase_invoicelist();
			$this->data['suppliers'] = $this->siteprocurment->getAllCompanies('supplier');
			$this->data['currencies'] = $this->siteprocurment->getAllCurrencies();
			
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('procurment/grn'), 'page' => lang('GRN')), array('link' => '#', 'page' => lang('edit_GRN')));
            $meta = array('page_title' => lang('edit_grn'), 'bc' => $bc);
            $this->page_construct('procurment/grn/edit', $meta, $this->data);
        }
    }

    public function delete($id = null){
        if ($this->input->get('id')) {
             $id = $this->input->get('id')?$this->input->get('id'):$id;
        }
		$inv = $this->grn_model->getStore_requestByID($id);
		if ($inv->status == 'approved' || $inv->status == 'completed') {
			 $this->sma->send_json(array('error' => 1, 'msg' => lang("Do not allowed edit option")));
		}	
        if ($this->grn_model->deleteStore_request($id)) {
                $this->sma->send_json(array('error' => 0, 'msg' => lang("store_request_deleted")));
        }else{
			$this->sma->send_json(array('error' => 1, 'msg' => lang("store_request Unable to Delete")));
		}
    }

  public function purchase_invoice_list(){
	     $poref =  $this->input->get('poref');
	     $inv= $this->grn_model->getPurchase_invoicesByID($poref);
		 $inv->address=strip_tags($inv->address);
		 $data['purchase_invoices'] =$inv;
	     $inv_items = $this->grn_model->getAllPurchase_invoiceItems($poref);
	     $c=1;
	    foreach ($inv_items as $item) {
            $row                        = $this->siteprocurment->getItemByID($item->product_id);
            $row->name                  = $item->product_name;
            $row->id                    = $item->product_id;
            $row->code                  = $item->product_code;
            $row->pi_qty                = $item->quantity;
            $row->qty                   = $item->quantity;
			$row->base_quantity         = $item->quantity;
            $row->quantity_balance      = $item->quantity;
            $row->tax_rate              = $item->item_tax_method;
            $tax                        = $this->siteprocurment->getTaxRateByID($item->item_tax_method);
            $row->tax_rate_val          = $tax->rate;
            $row->item_selling_price    = $item->selling_price;
            $row->category_id           = $item->category_id;
            $row->category_name         = $item->category_name;
            $row->subcategory_id        = $item->subcategory_id;
            $row->subcategory_name      = $item->subcategory_name;
            $row->brand_id              = $item->brand_id;
            $row->variant_id            = $item->variant_id;
			$row->option_id             = $item->variant_id;
            $row->brand_name            = $item->brand_name;
            
            $row->unit_name             = $item->product_unit_code;
            $row->base_unit             = $row->unit ? $row->unit : $item->product_unit_id;
			$row->unit                  = $row->purchase_unit ? $row->purchase_unit : $row->unit;
			$row->store_id  		    = $item->store_id;
			$row->cost_price			= $item->cost;
			$row->selling_price			= $item->selling_price;
			$row->landing_cost			= $item->landing_cost;
			$row->tax_rate				= $item->tax_rate;
			$row->invoice_id			= $item->invoice_id;
			$row->batch					= $item->batch;
			$row->expiry				= $item->expiry;
			$row->expiry_type			= $item->expiry_type;
			$row->invoice_date			= $item->invoice_date;
			$row->tax_rate_id			= $item->tax_rate_id;
            $options                    = $this->grn_model->getProductOptions($row->id);
            $units                      = $this->siteprocurment->getUnitsByBUID($row->base_unit);
            $ri                         = $this->Settings->item_addition ? $row->id : $row->id;
		    $item_key = $ri.'_'.$item->store_id.'_'.$item->category_id.'_'.$item->subcategory_id.'_'.$item->brand_id.'_'.$item->variant_id;
            $pr[$item_key] = array('id' => $c,'store_id'=>$item->store_id,'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")",
                    'row' => $row, 'tax_rate_id' => $item->item_tax_method,'tax_rate_val' => $item->tax_rate,'tax_rate' => $item->tax, 'units' => $units, 'options' => $options);
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


}
