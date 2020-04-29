<?php defined('BASEPATH') or exit('No direct script access allowed');

class Store_indent_receive extends MY_Controller{
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
        $this->load->admin_model('procurment/store_indent_receive_model');
        $this->digital_upload_path = 'assets/uploads/procurment/store_indent_receive/';
        $this->digital_file_types = 'zip|psd|ai|rar|pdf|doc|docx|xls|xlsx|ppt|pptx|gif|jpg|jpeg|png|tif|txt';
        $this->allowed_file_size = '1024';
	if (!file_exists($this->digital_upload_path)) {
		mkdir($this->digital_upload_path, 0777, true);
	}
        $this->data['logo'] = true;
		
		$this->Muser_id = $this->session->userdata('user_id');
		$this->Maccess_id = 8;
		$this->ModuleTheme = 'procurment/store_indent_receive/';
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

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('store_indent_receive')));
        $meta = array('page_title' => lang('store_indent_receive'), 'bc' => $bc);
        $this->page_construct('procurment/store_indent_receive/index', $meta, $this->data);

    }

    public function getStore_indentReceive($warehouse_id = null){
        //$this->sma->checkPermissions('index');

        if ((!$this->Owner || !$this->Admin) && !$warehouse_id) {
            $user = $this->siteprocurment->getUser();
            $warehouse_id = $user->warehouse_id;
        }
	    $view_link = '<a href="'.admin_url('procurment/store_indent_receive/view/$1').'" data-toggle="modal" data-target="#myModal" data-backdrop="static" data-keyboard="false"><i class="fa fa-edit"></i>'.lang('view_store_indent_receive').'</a>';
       
        $detail_link = anchor('admin/procurment/store_request/view/$1/', '<i class="fa fa-file-text-o"></i> ' . lang('store_request_details'));
        $email_link = anchor('admin/procurment/store_request/email/$1', '<i class="fa fa-envelope"></i> ' . lang('email_store_request'), 'data-toggle="modal" data-target="#myModal"');
		
		
		$edit_link = anchor('admin/procurment/store_request/edit/$1', '<i class="fa fa-edit"></i> ' . lang('edit_store_request'));
		
       // $convert_link = anchor('admin/procurment/sales/add/$1', '<i class="fa fa-heart"></i> ' . lang('create_sale'));
       // $pc_link = anchor('admin/procurment/purchases/add/$1', '<i class="fa fa-star"></i> ' . lang('create_purchase'));
        $pdf_link = anchor('admin/procurment/store_request/pdf/$1', '<i class="fa fa-file-pdf-o"></i> ' . lang('download_pdf'));
        $delete_link = "<a href='#' class='po' title='<b>" . $this->lang->line("delete_store_request") . "</b>' data-content=\"<p>"
        . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('procurment/store_request/delete/$1') . "'>"
        . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i> "
        . lang('delete_store_request') . "</a>";
		
       /* $action = '<div class="text-center"><div class="btn-group text-left">'
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
                       
			<li>' . $view_link . '</li>
                        
                    </ul>
                </div></div>';
		//$action ='';
        //$action = '<div class="text-center">' . $detail_link . ' ' . $edit_link . ' ' . $email_link . ' ' . $delete_link . '</div>';

        $this->load->library('datatables');
      
            $this->datatables
                ->select("pro_store_indent_receive.id, pro_store_indent_receive.date, pro_store_indent_receive.reference_no, f.name as from_name, t.name as to_name, pro_store_indent_receive.status, pro_store_indent_receive.attachment as attachment")
                ->from('pro_store_indent_receive')
				->join('warehouses f', 'f.id = pro_store_indent_receive.from_store_id', 'left')
				->join('warehouses t', 't.id = pro_store_indent_receive.to_store_id', 'left');
        
        
	$this->datatables->edit_column('attachment', '$1__$2', $this->digital_upload_path.', attachment');
        $this->datatables->add_column("Actions", $action, "pro_store_indent_receive.id,pro_store_indent_receive.status");
        echo $this->datatables->generate();
    }

    public function view($stock_request_id = null)
    {
	$this->sma->checkPermissions(false,true);
	$id = $stock_request_id;
	$this->data['store_req'] = $this->store_indent_receive_model->getindentReceive_requestByID($id);
	
            $inv_items = $this->store_indent_receive_model->getAllIndentRecevie_requestItems($id);
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

            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('procurment/store_indent_receive'), 'page' => lang('view_indent_receive_request')), array('link' => '#', 'page' => lang('view_indent_receive_request')));
            $meta = array('page_title' => lang('view_indent_receive_request'), 'bc' => $bc);
            $this->load->view($this->theme . $this->ModuleTheme.'view',  $this->data);

    }

}