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


    public function view($id = null){
	        $this->sma->checkPermissions();
	        $this->data['grn']       = $this->grn_model->getGRNById($id);
            $this->data['grn_items'] = $this->grn_model->getGRNIitemById($id);
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('procurment/GRN'), 'page' => lang('GRN')), array('link' => '#', 'page' => lang('view_GRN')));
            $meta = array('page_title' => lang('view_GRN'), 'bc' => $bc);
            $this->load->view($this->theme . 'procurment/grn/view',  $this->data);
    }

 
    public function add(){
            $this->sma->checkPermissions();
		    $this->form_validation->set_rules('pi_number', $this->lang->line("pi_number"), 'required');
         if ($this->form_validation->run() == true) {
			$n = $this->siteprocurment->lastidGrn();
			$n=($n !=0)?$n+1:$this->store_id .'1';
			$reference = 'GRN'.str_pad($n, 8, 0, STR_PAD_LEFT);
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
						'product_base_cost'=>$_POST['product_base_cost'][$r],
						'product_base_price'=>$_POST['product_base_price'][$r],
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
						'cm_id'           =>$_POST['cm_id'][$r],
						'tax_rate_id '     =>$_POST['tax_rate_id'][$r],
						'tax_rate'        =>$_POST['tax_rate'][$r],
						'cost_price'      =>$_POST['cost_price'][$r],
						'pi_uniqueId'      =>$_POST['unique_id'][$r]
						
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
        $this->form_validation->set_rules('pi_number', $this->lang->line("pi_number"), 'required');
        if ($this->form_validation->run() == true) {
            $note = $this->sma->clear_tags($this->input->post('note'));
			$date = date('Y-m-d H:i:s');
			$status = $this->input->post('status');
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
						'batch_no'        => ($_POST['batch'][$r] && $_POST['batch'][$r] !=null)?$_POST['batch'][$r]:'',
                        'expiry'          => ($_POST['expiry'][$r])?$_POST['expiry'][$r]:'',
                        'quantity'        => ($_POST['quantity'][$r])?$_POST['quantity'][$r]:0,
						'pi_qty'          => ($_POST['pi_qty'][$r])?$_POST['pi_qty'][$r]:0,
						'landing_cost'	  => ($_POST['landing_cost'][$r])?$_POST['landing_cost'][$r]:0,
						'selling_price'   => ($_POST['selling_price'][$r])?$_POST['selling_price'][$r]:0,
					//	'margin'          => $_POST['store_id'][$r],
						//'net_amt'         => $_POST['store_id'][$r],
						'product_base_cost'=>$_POST['product_base_cost'][$r],
						'product_base_price'=>$_POST['product_base_price'][$r],
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
						'cm_id'           =>$_POST['cm_id'][$r],
						'tax_rate_id'     =>$_POST['tax_rate_id'][$r],
						'tax_rate'        =>$_POST['tax_rate'][$r],
						'cost_price'      =>$_POST['cost_price'][$r],
						'pi_uniqueId'      =>$_POST['unique_id'][$r]
                    );
                }
            }
            if (empty($products)) {
                $this->form_validation->set_rules('product', lang("order_items"), 'required');
            } else {
                $products;
            }
			  $data = array(
                'note' => $note,
                'status' => $status,
				'no_of_items' => $this->input->post('titems'),
				'no_of_qty' => $this->input->post('total_items'),
				'updated_by' => $this->session->userdata('user_id'),
				'updated_on'=>$date
				);
				if($status=="approved"){
		        $data['approved_by'] = $this->session->userdata('user_id');
                $data['approved_on'] = $date;
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
			
        }
        if ($this->form_validation->run() == true && $this->grn_model->updateGrn($id, $data, $products)) {
            $this->session->set_userdata('remove_quls', 1);
            $this->session->set_flashdata('message', $this->lang->line("grn_updated"));
            admin_redirect('procurment/grn');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['inv'] = $this->grn_model->getGRNById($id);
            $grn_items = $this->grn_model->getGRNIitemById($id);
            krsort($grn_items);
            $c = rand(100000, 9999999);
            foreach ($grn_items as $item) {
                $row = $this->siteprocurment->getRecipeByID($item->product_id);
				$row->name                  = $item->product_name;
				$row->id                    = $item->product_id;
                $row->code                  = $item->product_code;
				$row->pi_qty                = $item->pi_qty;
				$row->qty                   = $item->quantity;
				$row->base_quantity         = $item->quantity;
				$row->quantity_balance      =$item->pi_qty- $item->quantity;
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
				$row->cost_price			= $item->cost_price;
				$row->selling_price			= $item->selling_price;
				$row->landing_cost			= $item->landing_cost;
				$row->tax_rate				= $item->tax_rate;
				$row->invoice_id			= $item->invoice_id;
				$row->batch					= $item->batch;
				$row->expiry				= $item->expiry;
				$row->expiry_type			= $item->expiry_type;
				$row->invoice_date			= $item->invoice_date;
				$row->tax_rate_id			= $item->tax_rate_id;
				$row->uniqueid			    = $item->pi_uniqueId;
				$options                    = $this->grn_model->getProductOptions($row->id);
				$units                      = $this->siteprocurment->getUnitsByBUID($row->base_unit);
				$ri                         = $this->Settings->item_addition ? $row->id : $row->id;
				$item_key = $ri.'_'.$item->store_id.'_'.$item->category_id.'_'.$item->subcategory_id.'_'.$item->brand_id.'_'.$item->variant_id;
                $pr[$item_key] = array('id' => $c,'store_id'=>$item->store_id,'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")",
                    'row' => $row, 'tax_rate_id' => $item->item_tax_method,'tax_rate_val' => $item->tax_rate,'tax_rate' => $item->tax, 'units' => $units, 'options' => $options);
				
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
		$grn = $this->grn_model->getGRNById($id);
		if ($grn->status == 'approved' || $grn->status == 'completed') {
			 $this->sma->send_json(array('error' => 1, 'msg' => lang("Do not allowed edit option")));
		}	
        if ($this->grn_model->deleteGrn($id)) {
                $this->sma->send_json(array('error' => 0, 'msg' => lang("GRN_deleted")));
        }else{
			$this->sma->send_json(array('error' => 1, 'msg' => lang("GRN Unable to Delete")));
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
		    $row->uniqueid			    = $item->pi_uniqueId;
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
