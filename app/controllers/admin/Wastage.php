<?php defined('BASEPATH') or exit('No direct script access allowed');

class Wastage extends MY_Controller{
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
        $this->lang->admin_load('wastage', $this->Settings->user_language);
        $this->load->library('form_validation');
        $this->load->admin_model('wastage_model');
        $this->digital_upload_path = 'assets/uploads/wastage/';
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

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('wastage_management')));
        $meta = array('page_title' => lang('wastage_management'), 'bc' => $bc);
        $this->page_construct('wastage/index', $meta, $this->data);

    }

    public function getWastage($warehouse_id = null){
        if ((!$this->Owner || !$this->Admin) && !$warehouse_id) {
            $user = $this->siteprocurment->getUser();
            $warehouse_id = $user->warehouse_id;
        }
	    $view_link = '<a href="'.admin_url('wastage/view/$1').'" data-toggle="modal" data-target="#myModal"><i class="fa fa-edit"></i>'.lang('view_wastage_details').'</a>';
        $detail_link = anchor('admin/wastage/view/$1/', '<i class="fa fa-file-text-o"></i> ' . lang('view_wastage_details'));
		$edit_link = anchor('admin/wastage/edit/$1', '<i class="fa fa-edit"></i> ' . lang('edit_wastage'));
        $delete_link = "<a href='#' class='po' title='<b>" . $this->lang->line("delete_wastage") . "</b>' data-content=\"<p>"
        . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('procurment/grn/delete/$1') . "'>"
        . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i> "
        . lang('delete_wastage') . "</a>";
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
                ->select("wastage.id, wastage.date, wastage.reference_no,  wastage.type,wastage.note, wastage.status")
                ->from('wastage')
                ->where('wastage.store_id', $warehouse_id);
        } else {
            $this->datatables
                ->select("wastage.id, wastage.date, wastage.reference_no,wastage.type,wastage.note,wastage.status")
                ->from('wastage')
				 ->where('wastage.store_id',$this->store_id);
        }
      
	    $this->datatables->edit_column('attachment', '$1__$2', $this->digital_upload_path.', attachment');
        $this->datatables->add_column("Actions", $action, "wastage.id,wastage.status");
        echo $this->datatables->generate();
    }


    public function view($id = null){
	        $this->sma->checkPermissions();
	        $this->data['wastage']       = $this->wastage_model->getWastageId($id);
            $this->data['wastage_items'] = $this->wastage_model->getWastageItemById($id);
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('wastage'), 'page' => lang('wastage')), array('link' => '#', 'page' => lang('view_wastage_details')));
            $meta = array('page_title' => lang('view_wastage_details'), 'bc' => $bc);
            $this->load->view($this->theme . 'wastage/view',  $this->data);
    }

 
    public function add(){
            $this->sma->checkPermissions();
		    $this->form_validation->set_rules('type', $this->lang->line("type"), 'required');
         if ($this->form_validation->run() == true) {
			$n = $this->siteprocurment->lastWastageId();
			$n=($n !=0)?$n+1:$this->store_id .'1';
			$reference = 'WTN'.str_pad($n, 8, 0, STR_PAD_LEFT);
			$date = date('Y-m-d H:i:s');
		    $status = $this->input->post('status');
            $note = $this->sma->clear_tags($this->input->post('note'));
         $products = array();
			$total_t_qty=0;
			$total_t_items=0;
			$i = isset($_POST['product_code']) ? sizeof($_POST['product_code']) : 0; 
	     $products = array();
	     for($r = 0; $r < $i; $r++){
			foreach($_POST['batch'][$_POST["product_id"][$r]] as $k => $row){
			if($row['wastage_qty']!=0){
				$unit = $this->site->getUnitByID($row['product_unit']);
				$product_unit_code=$unit->code;
		     	$products[$r]['batches'][] = array(
			    'available_qty'    => $row['available_qty'],
				'variant_id'       => $_POST['variant_id'][$r],
			    'wastage_qty'     => $row['wastage_qty'],
			    'product_id' => $_POST["product_id"][$r],
			    'product_code' => $_POST['product_code'][$r],
			    'product_type' => $_POST['product_type'][$r],
			    'product_name' => $_POST['product_name'][$r],			    
			    
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
			   // 'tax_amount'       => $row['tax_amount'],
			    'net_amount'       => $row['grand_total'],
			    'store_id'         => $this->store_id,
			    'stock_id'         => $row['stock_id'],
				'product_unit_id'  => $row['product_unit'],
				'unit_quantity'    => $row['base_quantity'],
				'wastage_unit_qty'=> $row['base_quantity'],
				'product_unit_code' =>$unit->code,
				'category_id'      => $_POST['catgory_id'][$r],
				'subcategory_id'   => $_POST['subcatgory_id'][$r],
				'brand_id'         => !empty($_POST['brand_id'][$r])?$_POST['brand_id'][$r]:0,
				
				
			);
			}
			$total_t_qty +=$row['wastage_qty'];
			$total_t_items++;
		    }
			
		 }
			
            if (empty($products)) {
                $this->form_validation->set_rules('product', lang("order_items"), 'required');
            } else {
                $products;
            }
          
            $data = array(
				'reference_no' => $reference,
				'date' => $date,
				'store_id' =>$this->store_id,
                'note' => $note,
                'status' => $status,
                'created_by' => $this->session->userdata('user_id'),
				'approved_by' => $approved_by ? $approved_by : 0,
				'approved_on' => $date,
				'type' => $this->input->post('type'),
				'created_on' => date('Y-m-d H:i:s'),
				'no_of_items' => $this->input->post('total_no_items'),
				'no_of_qty' => $this->input->post('total_no_qty'),
				'created_by' => $this->session->userdata('user_id'),
				'created_on' => date('Y-m-d H:i:s'),
				'processed_by' => $this->session->userdata('user_id'),
				'processed_on' => date('Y-m-d H:i:s'),
				
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

        if ($this->form_validation->run() == true && $this->wastage_model->addWastage($data, $products)) {
            $this->session->set_userdata('remove_quls', 1);
            $this->session->set_flashdata('message', $this->lang->line("wastage_added"));
            admin_redirect('wastage');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['billers'] = ($this->Owner || $this->Admin || !$this->session->userdata('biller_id')) ? $this->siteprocurment->getAllCompanies('biller') : null;
            $this->data['warehouses'] = ($this->Owner || $this->Admin || !$this->session->userdata('warehouse_id')) ? $this->siteprocurment->getAllWarehouses() : null;
			
			$this->data['suppliers'] = $this->siteprocurment->getAllCompanies('supplier');
			$this->data['currencies'] = $this->siteprocurment->getAllCurrencies();
			$this->data['tax_rates'] = $this->siteprocurment->getAllTaxRates();
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('wastage'), 'page' => lang('wastage')), array('link' => '#', 'page' => lang('add_wastage')));
            $meta = array('page_title' => lang('add_wastage'), 'bc' => $bc);
            $this->page_construct('wastage/add', $meta, $this->data);
        }
    }

    public function edit($id = null){
        $this->sma->checkPermissions();
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $inv = $this->wastage_model->getWastageId($id);
		if ($inv->status == 'approved' || $inv->status == 'completed') {
			$this->session->set_flashdata('error', lang("Do not allowed edit option"));
			admin_redirect("wastage");
		}	
        $this->form_validation->set_rules('type', $this->lang->line("type"), 'required');
        if ($this->form_validation->run() == true) {
            $note = $this->sma->clear_tags($this->input->post('note'));
			$date = date('Y-m-d H:i:s');
			$status = $this->input->post('status');
		    $total_t_items=0;
		    $i = isset($_POST['product_code']) ? sizeof($_POST['product_code']) : 0; 
	        $products = array();
	     for($r = 0; $r < $i; $r++){
			foreach($_POST['batch'][$_POST["product_id"][$r]] as $k => $row){
			if($row['wastage_qty']!=0){
				$unit = $this->site->getUnitByID($row['product_unit']);
				$product_unit_code=$unit->code;
		     	$products[$r]['batches'][] = array(
			    'available_qty'    => $row['available_qty'],
				'variant_id'       => $_POST['variant_id'][$r],
			    'wastage_qty'     => $row['wastage_qty'],
			    'product_id' => $_POST["product_id"][$r],
			    'product_code' => $_POST['product_code'][$r],
			    'product_type' => $_POST['product_type'][$r],
			    'product_name' => $_POST['product_name'][$r],
			    'batch'            => $row['batch_no'],
			    'vendor_id'        => $row['vendor_id'],
			    'expiry'           => $row['expiry'],
			    'cost_price'       => $row['cost_price'],
			    'selling_price'    => $row['selling_price'],
			    'landing_cost'     => $row['landing_cost'],
			    'unit_price'       => $row['selling_price'],
			    'net_unit_price'   => $row['selling_price']*$row['request_qty'],
			    'gross_amount'     => $row['gross'],
			    'net_amount'       => $row['grand_total'],
			    'store_id'         => $this->store_id,
			    'stock_id'         => $row['stock_id'],
				'product_unit_id'  => $row['product_unit'],
				'unit_quantity'    => $row['base_quantity'],
				'wastage_unit_qty'=> $row['base_quantity'],
				'product_unit_code' =>$unit->code,
				'category_id'      => $_POST['catgory_id'][$r],
				'subcategory_id'   => $_POST['subcatgory_id'][$r],
				'brand_id'         => !empty($_POST['brand_id'][$r])?$_POST['brand_id'][$r]:0,
			);
			}
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
        if ($this->form_validation->run() == true && $this->wastage_model->updateWastage($id, $data, $products)) {
            $this->session->set_userdata('remove_quls', 1);
            $this->session->set_flashdata('message', $this->lang->line("wastage_updated"));
            admin_redirect('wastage');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['inv']   = $this->wastage_model->getWastageId($id);
            $wastage_items       = $this->wastage_model->getWastageItemById($id);
            krsort($wastage_items);
            $c = rand(100000, 9999999);
            foreach ($wastage_items as $item) {
                $row = $this->siteprocurment->getRecipeByID($item->product_id);
				$row->name                  = $item->product_name;
				$row->id                    = $item->product_id;
                $row->code                  = $item->product_code;
				$row->pi_qty                = $item->pi_qty;
				$row->qty                   = $item->quantity;
				$row->base_quantity         = $item->quantity;
				$row->quantity_balance      = $item->pi_qty- $item->quantity;
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
				$options                    = $this->wastage_model->getProductOptions($row->id);
				$units                      = $this->siteprocurment->getUnitsByBUID($row->base_unit);
				$batches                    = $this->wastage_model->getbatchStockData($id,$row->id);
				$row->batches               = $batches;
				$ri                         = $this->Settings->item_addition ? $row->id : $row->id;
				$item_key = $ri.'_'.$item->store_id.'_'.$item->category_id.'_'.$item->subcategory_id.'_'.$item->brand_id.'_'.$item->variant_id;
                $pr[$ri] = array('id' => $c,'store_id'=>$item->store_id,'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")",
                'row' => $row, 'tax_rate_id' => $item->item_tax_method,'tax_rate_val' => $item->tax_rate,'tax_rate' => $item->tax, 'units' => $units, 'options' => $options);
                $c++;
            }
            $this->data['wastage_items'] = json_encode($pr);
            $this->data['id'] = $id;
            $this->data['tax_rates'] = $this->siteprocurment->getAllTaxRates();
            $this->data['warehouses'] = ($this->Owner || $this->Admin || !$this->session->userdata('warehouse_id')) ? $this->siteprocurment->getAllWarehouses() : null;
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('wastage'), 'page' => lang('wastage')), array('link' => '#', 'page' => lang('edit_wastage')));
            $meta = array('page_title' => lang('edit_wastage'), 'bc' => $bc);
            $this->page_construct('wastage/edit', $meta, $this->data);
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
public function suggestions(){
        $term = $this->input->get('term', true);
        $supplier_id = $this->input->get('supplier_id', true);
        if (strlen($term) < 1 || !$term) {
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . admin_url('procurment/welcome') . "'; }, 10);</script>");
        }
        $analyzed       = $this->sma->analyze_term($term);
        $sr             = $analyzed['term'];
        $option_id      = $analyzed['option_id'];
        $rows           = $this->wastage_model->getProductNames($sr);
        if ($rows) {
            $c = str_replace(".", "", microtime(true));
            $r = 0;
            foreach ($rows as $row) {
                $option = false;
                $row->item_tax_method = $row->tax_method;
                $options = $this->wastage_model->getProductOptions($row->id);
                if ($options) {
                    $opt = $option_id && $r == 0 ? $this->wastage_model->getProductOptionByID($option_id) : current($options);
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
				$unique_item_id = $this->store_id.$row->id.$row->variant_id.$row->batch.$row->category_id.$row->subcategory_id.$row->brand_id.$row->invoice_id;
                unset($row->details, $row->product_details, $row->price, $row->file, $row->supplier1price, $row->supplier2price, $row->supplier3price, $row->supplier4price, $row->supplier5price, $row->supplier1_part_no, $row->supplier2_part_no, $row->supplier3_part_no, $row->supplier4_part_no, $row->supplier5_part_no);
                $units        = $this->siteprocurment->getUnitsByBUID($row->base_unit);
                $tax_rate     = $this->siteprocurment->getTaxRateByID($row->tax_rate);
				$batches      =  $this->wastage_model->loadbatches($row->id,$row->variant_id,$row->category_id,$row->subcategory_id,$row->brand_id);
				$row->batches = $batches;
				$label = $row->name . " (" . $row->code . ") CAT - ".$row->category_name." | SUBCAT - ".$row->subcategory_name." | BRAND - ".$row->brand_name;
                $pr[] = array('id' => $unique_item_id, 'item_id' => $row->id, 'label' => $label . " (" . $row->code . ")",
                    'row' => $row, 'unique_id'=>$unique_item_id,'tax_rate' => $tax_rate, 'units' => $units, 'options' => $options);
                $r++;
            }
            $this->sma->send_json($pr);
        } else {
            $this->sma->send_json(array(array('id' => 0, 'label' => lang('no_match_found'), 'value' => $term)));
        }
    }

}
