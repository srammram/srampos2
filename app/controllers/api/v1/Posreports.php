<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;



class Posreports extends REST_Controller {
	var $Owner;
	var $Admin;
	var $theme;
	var $dateFormats;
	var $data;
	var $v;
	var $m;
	var $GP;
	function __construct() {
		parent::__construct();
		$this->load->helper(array('form', 'url'));
		$this->load->library(array('sma','form_validation'));
		$this->load->api_model('posreports_api');
		$this->settings = $this->posreports_api->getSettings();
		
		if($sma_language = $this->input->cookie('sma_language', TRUE)) {
			$this->config->set_item('language', $sma_language);
			$this->lang->admin_load('sma', $sma_language);
			$this->Settings->user_language = $sma_language;
		} else {
		    $this->config->set_item('language', $this->Settings->language);
		    $this->lang->admin_load('sma', $this->Settings->language);
		    $this->Settings->user_language = $this->Settings->language;
		}
		$this->lang->admin_load('reports', $this->Settings->user_language);
		$this->m = strtolower($this->router->fetch_class());
		$this->v = strtolower($this->router->fetch_method());
		$this->theme=$this->Settings->theme.'/admin/views/';
		$this->loggedIn = $this->sma->logged_in();
		if($sd = $this->site->getDateFormat($this->settings->dateformat)) {
			$this->dateFormats = array(
			    'js_sdate' => $sd->js,
			    'php_sdate' => $sd->php,
			    'mysq_sdate' => $sd->sql,
			    'js_ldate' => $sd->js . ' hh:ii',
			    'php_ldate' => $sd->php . ' H:i',
			    'mysql_ldate' => $sd->sql . ' %H:%i'
			    );
		} else {
		    $this->dateFormats = array(
			'js_sdate' => 'mm-dd-yyyy',
			'php_sdate' => 'm-d-Y',
			'mysq_sdate' => '%m-%d-%Y',
			'js_ldate' => 'mm-dd-yyyy hh:ii:ss',
			'php_ldate' => 'm-d-Y H:i:s',
			'mysql_ldate' => '%m-%d-%Y %T'
			);
		}
		$this->data['pb'] = array(
		'cash' => lang('cash'),
		'CC' => lang('CC'),
		'Cheque' => lang('Cheque'),
		'paypal_pro' => lang('paypal_pro'),
		'stripe' => lang('stripe'),
		'gift_card' => lang('gift_card'),
		'deposit' => lang('deposit'),
		'authorize' => lang('authorize'),
		);
		$this->pos_settings = $this->posreports_api->getPOSSetting(); 
		if($this->loggedIn) {
			$this->Owner = $this->sma->in_group('owner') ? TRUE : NULL;
			$this->data['Owner'] = $this->Owner;
			$this->Customer = $this->sma->in_group('customer') ? TRUE : NULL;
			$this->data['Customer'] = $this->Customer;
			$this->Supplier = $this->sma->in_group('supplier') ? TRUE : NULL;
			$this->data['Supplier'] = $this->Supplier;
			$this->Admin = $this->sma->in_group('admin') ? TRUE : NULL;
			$this->data['Admin'] = $this->Admin;
			if(!$this->Owner && !$this->Admin) {
				//$gp = $this->site->checkPermissions();
				//$this->GP = $gp[0];
				//$this->data['GP'] = $gp[0];
				$this->data['GP'] = NULL;
			} else {
			    $this->data['GP'] = NULL;
			}
			$this->session->set_userdata('start_date', $this->input->post('start'));
			$this->session->set_userdata('end_date', $this->input->post('end'));
		}
	}
	public function testlogin_get()
	{	$meta =  array();
		$api_key = $this->input->post('api-key');
		$this->data['api_key'] = $api_key;
		$this->page_construct('posreports/testlogin', $meta, $this->data);
	}
	public function login_post()
	{
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$username = $this->input->post('username');
		$password = $this->input->post('password');
		
		$this->form_validation->set_rules('username', $this->lang->line("username"), 'required');
		$this->form_validation->set_rules('password', $this->lang->line("password"), 'required');
		/*$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');*/
		if ($this->form_validation->run() == true) {
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){
				$login_response = $this->posreports_api->login($username,$password);
				/*echo "string";die;*/
				/*var_dump($login_response);die;*/
				if($login_response == TRUE){
					/*$data = $this->customer_api->GetuserByID($phone_number);*/
					$user = $this->site->getUser($login_response);
					$tablewhitelisted = 0;
					if($user->group_id==1 || $user->group_id==2) $tablewhitelisted = 1;
					$result = array( 'status'=> true ,'tablewhitelisted'=>$tablewhitelisted,'message'=> 'Sucessfully Logged In');
				
					//$result = array( 'status'=> true , 'message'=> 'Sucessfully Logged In');
				}else{
					
					$result = array( 'status'=> false , 'message'=> 'User Name OR Password is Wrong!');
				}				
			}else{
				$result = array( 'status'=> false , 'message'=> 'Devices Key does not matche. please check your devices key');	
			}
			
		}else{
			$result = array( 'status'=> false , 'message'=> 'Please Enter All Details');	
		}		
		$this->response($result);
		
	}
	public function settings_post()
	{
		
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$devices_type = $this->input->post('devices_type');
		$api_type = 1;
		
		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		$this->form_validation->set_rules('devices_type', $this->lang->line("devices_type"), 'required');
		
		if ($this->form_validation->run() == true) {
			$data = $this->posreports_api->checkDevices($api_key);
			if(!empty($data->devices_key)){
				//if($data->devices_key == $devices_key){	
				if($data->devices_key == $devices_key && $data->api_type == $api_type){
					$result = array( 'status'=> true , 'message'=> 'Devices Key does is matched!');	
				}else{
					$result = array( 'status'=> false , 'message'=> 'Devices Key does not matche. please check your devices key');	
				}
			}else{
				$this->posreports_api->updateDevices($api_key, $devices_key, $devices_type, $api_type);
				$result = array( 'status'=> true , 'message'=> 'New Devices key has been Insert');	
			}
		}else{
			$result = array( 'status'=> false , 'message'=> 'Please Enter All Details');	
		}
		$this->response($result);
		
	}		
	public function apitype_post(){
		$api_key = $this->input->get('api-key');
		$data = $this->posreports_api->GetAllapitype();
		if(!empty($data)){
			$result = array( 'status'=> true , 'message'=> 'Api Type data', 'data' => $data);
		}else{
			$result = array( 'status'=> false , 'message'=> 'Api Type List Empty');
		}
		
		$this->response($result);
	}	
	public function settings_login_post(){
		$api_key = $this->input->post('api-key');
		$user_number = $this->input->post('user_number');		
		$this->form_validation->set_rules('user_number', $this->lang->line("user_number"), 'required');		
		if ($this->form_validation->run() == true) {
			
			if($user_number == '1234'){
				$result = array( 'status'=> true , 'message'=> 'Admin user code matched!');	
			}else{
				$result = array( 'status'=> false , 'message'=> 'Admin user code does not matched.');	
			}
			
		}else{
			$result = array( 'status'=> false , 'message'=> 'Please Enter All Details');	
		}
		$this->response($result);
	}
	public function reportslist_get()
	{	$meta =  array();
		$api_key = $this->input->get('api-key');
		$this->data['api_key'] = $api_key;
		//echo $this->Owner;echo '<pre>';print_r($this->session->all_userdata());exit;
		$reports = array();		
		$this->page_construct('posreports/reportslist', $meta, $this->data);
	}
	
	public function pos_settlement_get(){
		//$this->sma->checkPermissions();
		$api_key = $this->input->get('api-key');		
		$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
		$this->data['users'] = $this->posreports_api->getStaff();        
		$this->data['warehouses'] = $this->site->getAllWarehouses();
		$this->data['billers'] = $this->site->getAllCompanies('biller');
		$bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('pos_settlement_report')));
		$meta = array('page_title' => lang('pos_settlement_report'), 'bc' => $bc);
		
		$this->settings = $this->posreports_api->getSettings();
		$this->data['default_currency'] = $this->settings->default_currency;
		$this->data['api_key'] = $api_key;
		$this->page_construct('posreports/pos_settlement', $meta, $this->data);
	}
      public function get_settlementreports_post($start_date = NULL, $end_date = NULL, $warehouse_id = NULL, $defalut_currency = NULL){
        $api_key = $this->input->post('api_key');
	$start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        $warehouse_id = $this->input->post('warehouse_id');
        $limit = $this->input->post('pagelimit');  
		$printlist = $this->input->post('printlist') ? $this->input->post('printlist') : 0;          
        $offsetSegment = 5;
        $offset = $this->uri->segment($offsetSegment,0);
        $defalut_currency = $this->input->post('defalut_currency');
	$this->Owner = true;$this->Admin = true;
        $this->session->set_userdata('start_date', $this->input->post('start_date'));
        $this->session->set_userdata('end_date', $this->input->post('end_date'));
   
        $data= '';
	//$start_date = '2018-11-13';
	//$end_date = '2018-11-19';
        if ($end_date != '' && $end_date != '') {
            $data = $this->posreports_api->getPosSettlementReport($start_date,$end_date,$warehouse_id,$defalut_currency,$limit,$offset,$printlist);
            
            if (!empty($data['data'])){
                 
                 $settlements = $data['data'];
             }
             else{
                
                $settlements = 'empty';
             }
        }
        else{
            $settlements = 'error';
        }
        $total = $data['total'];
        $pagination = $this->pagination('api/v1/posreports/get_settlementreports',$limit,$offsetSegment,$total);
        $this->sma->send_json(array('settlements' => $settlements,'pagination'=>$pagination));
   }
   function bbq_reports_get()
    {
        //$this->sma->checkPermissions();
        $api_key = $this->input->get('api-key');
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users'] = $this->posreports_api->getStaff();        
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['billers'] = $this->site->getAllCompanies('biller');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('bbq_reports')));
        $meta = array('page_title' => lang('bbq_reports'), 'bc' => $bc);
        $this->data['api_key'] = $api_key;
        $this->page_construct('posreports/bbq_reports', $meta, $this->data);
    }

   public function get_bbqrports_post(){

        $api_key = $this->input->post('api_key');       
        $start = $this->input->post('start_date');
        $end = $this->input->post('end_date');
        $warehouse_id = $this->input->post('warehouse_id');
        $summary_items = $this->input->post('summary_items');
        $limit = $this->input->post('pagelimit');    
		$printlist = $this->input->post('printlist') ? $this->input->post('printlist') : 0;    
        $offsetSegment = 5;
        $offset = $this->uri->segment($offsetSegment,0);

        $this->session->set_userdata('start_date', $this->input->post('start_date'));
        $this->session->set_userdata('end_date', $this->input->post('end_date'));

        $data= '';
        if ($start != '' && $end != '') {
            if($summary_items == 'bbq_summary'){
                 $data = $this->posreports_api->getBBQDetailsReport($start,$end,$warehouse_id,$summary_items,$limit,$offset,$printlist);

            }elseif($summary_items == 'bbq_bills'){
                $data = $this->posreports_api->getBBQBillDetailsReport($start,$end,$warehouse_id,$summary_items,$limit,$offset,$printlist);                
            }else{
                $data = $this->posreports_api->getBBQitemsDetailsReport($start,$end,$warehouse_id,$summary_items,$limit,$offset,$printlist);
            }
            $round_tot = $this->posreports_api->getRoundamount($start,$end,$warehouse_id);            
             if (!empty($data['data'])) {                 
                 $bbqrports = $data['data'];
             }
             else{                
                $bbqrports = 'empty';
             }
             if ($round_tot != false) {                 
                 $round = $round_tot;
             }
             else{                
                $round = 'empty';
             }
        }
        else{
            $bbqrports = 'error';
        }
        $total = $data['total'];
        $pagination = $this->pagination('api/v1/posreports/get_bbqrports',$limit,$offsetSegment,$total);
        $this->sma->send_json(array('bbqrports' => $bbqrports,'round' => $round,'pagination'=>$pagination));
   }
   function bbq_notification_reports_get()
    {
	//$this->sma->checkPermissions();
        $api_key = $this->input->get('api-key');
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users'] = $this->posreports_api->getStaff();        
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['billers'] = $this->site->getAllCompanies('biller');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('bbq_cover_validtion_request_notification_report')));
        $meta = array('page_title' => lang('bbq_cover_validtion_request_notification_report'), 'bc' => $bc);
        $this->data['api_key'] = $api_key;
        $this->page_construct('posreports/bbq_notification', $meta, $this->data);
    }

   public function get_bbqnotificationrports_post(){

        $api_key = $this->input->post('api_key');      
        $start = $this->input->post('start_date');
        $end = $this->input->post('end_date');
        $warehouse_id = $this->input->post('warehouse_id');
        $limit = $this->input->post('pagelimit');    
        $offsetSegment = 5;
        $offset = $this->uri->segment($offsetSegment,0);
        $this->Owner = true;$this->Admin = true;
        $this->session->set_userdata('start_date', $this->input->post('start_date'));
        $this->session->set_userdata('end_date', $this->input->post('end_date'));

        $data= '';
        if ($start != '' && $end != '') {
            
                 $data = $this->posreports_api->get_bbqnotificationrports($start,$end,$warehouse_id,$limit,$offset);

                  
             if (!empty($data['data'])) {                 
                 $bbqNotifyreports = $data['data'];
             }
             else{                
                $bbqNotifyreports = 'empty';
             }
             
        }
        else{
            $bbqNotifyreports = 'error';
        }
        $total = $data['total'];
        $pagination = $this->pagination('api/v1/posreports/get_bbqnotificationrports',$limit,$offsetSegment,$total);
        $this->sma->send_json(array('bbqrports' => $bbqNotifyreports,'pagination'=>$pagination));
   }
   function recipe_get()
    {	
	//$this->sma->checkPermissions();
        $api_key = $this->input->get('api-key');
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users'] = $this->posreports_api->getStaff();
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['billers'] = $this->site->getAllCompanies('biller');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('item_sale_report')));
        $meta = array('page_title' => lang('item_sale_report'), 'bc' => $bc);
        $this->data['api_key'] = $api_key;
        $this->page_construct('posreports/recipe', $meta, $this->data);
    }

   public function get_itemreports_post($start_date = NULL, $end_date = NULL, $warehouse_id = NULL){

        $api_key = $this->input->post('api_key');      
        $start = $this->input->post('start_date');
        $end = $this->input->post('end_date');
        $warehouse_id = $this->input->post('warehouse_id');
        $limit = $this->input->post('pagelimit');    +
		$printlist = $this->input->post('printlist') ? $this->input->post('printlist') : 0;    
        $offsetSegment = 5;
        $offset = $this->uri->segment($offsetSegment,0);
        $this->Owner = true;$this->Admin = true;
        $this->session->set_userdata('start_date', $this->input->post('start_date'));
        $this->session->set_userdata('end_date', $this->input->post('end_date'));

        $data= '';
        if ($start != '' && $end != '') {
            $data = $this->posreports_api->getItemSaleReports($start,$end,$warehouse_id,$limit,$offset,$printlist);
            $round_tot = $this->posreports_api->getRoundamount($start,$end,$warehouse_id);
            
             if (!empty($data['data'])) {                 
                 $itemreports = $data['data'];
             }
             else{                
                $itemreports = 'empty';
             }
             if ($round_tot != false) {                 
                 $round = $round_tot;
             }
             else{                
                $round = 'empty';
             }
        }
        else{
            $itemreports = 'error';
        }
        $total = $data['total'];
        $pagination = $this->pagination('api/v1/posreports/get_itemreports',$limit,$offsetSegment,$total);
        $this->sma->send_json(array('itemreports' => $itemreports,'round' => $round,'pagination'=>$pagination));
   } 
   function kot_details_get()
    {
	//$this->sma->checkPermissions();
        $api_key = $this->input->get('api-key');
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users'] = $this->posreports_api->getStaff();
        /*$this->data['users'] = $this->posreports_api->getPosSettlementReport();*/
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['billers'] = $this->site->getAllCompanies('biller');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('kot_details_report')));
        $meta = array('page_title' => lang('kot_details_report'), 'bc' => $bc);
        $this->data['api_key'] = $api_key;
        $this->page_construct('posreports/kot_details', $meta, $this->data);
    }  
 public function get_kotdetailsreports_post($start_date = NULL, $end_date = NULL, $kot = NULL, $warehouse_id = NULL){
        $api_key = $this->input->post('api_key');
        $start = $this->input->post('start_date');
        $end = $this->input->post('end_date');
        $kot = $this->input->post('kot');
        $warehouse_id = $this->input->post('warehouse_id');
        $limit = $this->input->post('pagelimit');        
        $offsetSegment = 5;
        $offset = $this->uri->segment($offsetSegment,0);
        $this->Owner = true;$this->Admin = true;
        $this->session->set_userdata('start_date', $this->input->post('start_date'));
        $this->session->set_userdata('end_date', $this->input->post('end_date'));

        $data= '';

        if ($start != '' && $end != '' && $kot != '') {
             if($kot == 'kot_cancel') 
             {                
                $data = $this->posreports_api->getKotCancelReport($start,$end,$warehouse_id,$limit,$offset);
             }
             elseif ($kot == 'kot_pending')
             {
              $data = $this->posreports_api->getKotPendingReport($start,$end,$warehouse_id,$limit,$offset);  
             }
             else{                
                $data = $this->posreports_api->getKotDetailsReport($start,$end,$warehouse_id,$limit,$offset);
             }
             
             if (!empty($data['data'])){
                 
                 $kotdetails = $data['data'];
             }
             else{
                
                $kotdetails = 'empty';
             }
        }
        else{
            $kotdetails = 'error';
        }
        $total = $data['total'];
        $pagination = $this->pagination('api/v1/posreports/get_kotdetailsreports',$limit,$offsetSegment,$total);
        $this->sma->send_json(array('kotdetails' => $kotdetails,'pagination'=>$pagination));
   }
   function user_reports_get()
    {
	//$this->sma->checkPermissions();
        $api_key = $this->input->get('api-key');
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users'] = $this->posreports_api->getStaff();        
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['billers'] = $this->site->getAllCompanies('biller');
	$this->data['groups'] = $this->site->getAllGroups();  
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('user_report')));
        $meta = array('page_title' => lang('user_report'), 'bc' => $bc);
        $this->data['api_key'] = $api_key;
        $this->page_construct('posreports/user_reports', $meta, $this->data);
    }    
 public function get_user_reports_post($start = NULL, $end = NULL, $user = NULL){
        $api_key = $this->input->post('api_key');;
        $start = $this->input->post('start');
        $end = $this->input->post('end');
        $user = $this->input->post('user');
		$group = $this->input->post('group');
        $limit = $this->input->post('pagelimit');        
        $offsetSegment = 5;
        $offset = $this->uri->segment($offsetSegment,0);
        $this->Owner = true;$this->Admin = true;
        $this->session->set_userdata('start_date', $this->input->post('start'));
        $this->session->set_userdata('end_date', $this->input->post('end'));

        $data= '';
        if ($start != '' && $end != '') {
            $data = $this->posreports_api->getCashierReport($start,$end,$user,$limit,$offset,$group);
            if (!empty($data['data'])){
                 
                 $user_report = $data['data'];
             }
             else{
                
                $user_report = 'empty';
             }
        }
        else{
            $user_report = 'error';
        }
        $total = $data['total'];
        $pagination = $this->pagination('api/v1/posreports/get_user_reports',$limit,$offsetSegment,$total);
        $this->sma->send_json(array('user_report' => $user_report,'pagination'=>$pagination));
   }
   function home_delivery_get()
    {
	//$this->sma->checkPermissions();
        $api_key = $this->input->get('api-key');
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users'] = $this->posreports_api->getStaff();        
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['customers'] = $this->site->getAllCompanies('customer');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('home_delivery_report')));
        $meta = array('page_title' => lang('home_delivery_report'), 'bc' => $bc);
        $this->data['api_key'] = $api_key;
        $this->page_construct('posreports/home_delivery', $meta, $this->data);
    }
   public function get_homedelivery_reports_post($start = NULL, $end = NULL, $warehouse_id = NULL, $customer = NULL){
        $api_key = $this->input->post('api_key');
        $start = $this->input->post('start');
        $end = $this->input->post('end');
        $warehouse_id = $this->input->post('warehouse_id');
        $customer = $this->input->post('customer');
        $limit = $this->input->post('pagelimit');        
        $offsetSegment = 5;
        $offset = $this->uri->segment($offsetSegment,0);
        $this->Owner = true;$this->Admin = true;
        $this->session->set_userdata('start_date', $this->input->post('start'));
        $this->session->set_userdata('end_date', $this->input->post('end'));

        $data= '';
        if ($start != '' && $end != '') {
            $data = $this->posreports_api->getHomedeliveryReport($start,$end,$warehouse_id,$customer,$limit,$offset);
            if (!empty($data['data'])){
                 
                 $home_delivery = $data['data'];
             }
             else{
                
                $home_delivery = 'empty';
             }
        }
        else{
            $home_delivery = 'error';
        }
        $total = $data['total'];
        $pagination = $this->pagination('api/v1/posreports/get_homedelivery_reports',$limit,$offsetSegment,$total);
        $this->sma->send_json(array('home_delivery' => $home_delivery,'pagination'=>$pagination));
   }
   function take_away_get()
    {
	//$this->sma->checkPermissions();
        $api_key = $this->input->get('api-key');
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users'] = $this->posreports_api->getStaff();        
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['billers'] = $this->site->getAllCompanies('biller');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('take_away')));
        $meta = array('page_title' => lang('take_away'), 'bc' => $bc);
        $this->data['api_key'] = $api_key;
        $this->page_construct('posreports/takeaway', $meta, $this->data);
    }
   public function get_take_away_reports_post($start = NULL, $end = NULL, $warehouse_id = NULL){
        $api_key = $this->input->post('api_key');
        $start = $this->input->post('start');
        $end = $this->input->post('end');
        $warehouse_id = $this->input->post('warehouse_id');
        $limit = $this->input->post('pagelimit');        
        $offsetSegment = 5;
        $offset = $this->uri->segment($offsetSegment,0);

        $this->session->set_userdata('start_date', $this->input->post('start'));
        $this->session->set_userdata('end_date', $this->input->post('end'));

        $data= '';
        if ($start != '' && $end != '') {
            $data = $this->posreports_api->getTakeAwayReport($start,$end,$warehouse_id,$limit,$offset);
            if (!empty($data['data'])){
                 
                 $take_away = $data['data'];
             }
             else{
                
                $take_away = 'empty';
             }
        }
        else{
            $take_away = 'error';
        }
        $total = $data['total'];
        $pagination = $this->pagination('api/v1/posreports/get_take_away_reports',$limit,$offsetSegment,$total);
        $this->sma->send_json(array('take_away' => $take_away,'pagination'=>$pagination));
   }  
    function daywise_get()
    {
	//$this->sma->checkPermissions();
        $api_key = $this->input->get('api-key');
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users'] = $this->posreports_api->getStaff();        
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['billers'] = $this->site->getAllCompanies('biller');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('day_wise')));
        $meta = array('page_title' => lang('day_wise'), 'bc' => $bc);
        $this->data['api_key'] = $api_key;
        $this->page_construct('posreports/day_wise', $meta, $this->data);
    } 
    public function get_DaySummaryreports_post($start = NULL, $warehouse_id = NULL){
        $api_key = $this->input->post('api_key');
        $start = $this->input->post('start_date');
        $limit = $this->input->post('pagelimit');        
        $warehouse_id = $this->input->post('warehouse_id');
		$printlist = $this->input->post('printlist') ? $this->input->post('printlist') : 0;    
        $offsetSegment = 5;
        $offset = $this->uri->segment($offsetSegment,0);
        $data= '';
        $this->Owner = true;$this->Admin = true;
        if ($start != '') {
            $data = $this->posreports_api->getDaysummaryReport($start, $warehouse_id,$limit,$offset,$printlist);
            if (!empty($data['data'])) {
                 
                 $daysummary = $data['data'];
             }
             else{
                
                $daysummary = 'empty';
             }
        }
        else{
            $daysummary = 'error';
        }
        $total = $data['total'];
        $pagination = $this->pagination('api/v1/posreports/get_DaySummaryreports',$limit,$offsetSegment,$total);
        //echo $daysummary;
        $this->sma->send_json(array('daysummary' => $daysummary,'pagination'=>$pagination));
   }   
   function days_reports_get()
    {
	//$this->sma->checkPermissions();
        $api_key = $this->input->get('api-key');
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users'] = $this->posreports_api->getStaff();        
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['billers'] = $this->site->getAllCompanies('biller');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('day_wise_sale_report')));
        $meta = array('page_title' => lang('day_wise_sale_report'), 'bc' => $bc);
        $this->settings = $this->posreports_api->getSettings();
        $this->data['default_currency'] = $this->settings->default_currency;
        $this->data['api_key'] = $api_key;
        $this->page_construct('posreports/days_reports', $meta, $this->data);
    }
      public function get_daysreports_post($start_date = NULL, $end_date = NULL, $warehouse_id = NULL, $defalut_currency = NULL){
        $api_key = $this->input->post('api_key');
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        $warehouse_id = $this->input->post('warehouse_id');
        $day = $this->input->post('day');
        /*echo $day;die;*/
        $limit = $this->input->post('pagelimit');        
        $offsetSegment = 5;
        $offset = $this->uri->segment($offsetSegment,0);
        $defalut_currency = $this->input->post('defalut_currency');
        $data= '';
        $this->Owner = true;$this->Admin = true;
        if ($end_date != '' && $end_date != '') {
            $data = $this->posreports_api->getDaysreport($start_date,$end_date,$warehouse_id,$day,$defalut_currency,$limit,$offset);
            /*echo "<pre>";
            print_r($data);die;*/
            if (!empty($data['data'])){
                 
                 $settlements = $data['data'];
             }
             else{
                
                $settlements = 'empty';
             }
        }
        else{
            $settlements = 'error';
        }
        $total = $data['total'];
        $pagination = $this->pagination('api/v1/posreports/get_daysreports',$limit,$offsetSegment,$total);
        $this->sma->send_json(array('settlements' => $settlements,'pagination'=>$pagination));
   }
   function bill_details_get()
    {
	//$this->sma->checkPermissions();
        $api_key = $this->input->get('api-key');
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users'] = $this->posreports_api->getStaff();        
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['billers'] = $this->site->getAllCompanies('biller');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('bill_details_report')));
        $meta = array('page_title' => lang('bill_details_report'), 'bc' => $bc);
        $this->data['api_key'] = $api_key;
        $this->page_construct('posreports/bill_details', $meta, $this->data);
    }    
 public function get_bill_details_reports_post($start = NULL, $end = NULL, $bill_no = NULL, $warehouse_id = NULL){
        $api_key = $this->input->post('api_key');
        $start = $this->input->post('start');
        $end = $this->input->post('end');
        $bill_no = $this->input->post('bill_no');
        $warehouse_id = $this->input->post('warehouse_id');
        $limit = $this->input->post('pagelimit');        
        $offsetSegment = 5;
        $offset = $this->uri->segment($offsetSegment,0);
        $this->Owner = true;$this->Admin = true;
        $this->session->set_userdata('start_date', $this->input->post('start'));
        $this->session->set_userdata('end_date', $this->input->post('end'));
		$printlist = $this->input->post('printlist') ? $this->input->post('printlist') : 0;    
        $data= '';
        $table_whitelisted = $this->input->post('table_whitelisted');
        if ($start != '' && $end != '') {
            $data = $this->posreports_api->getBillDetailsReport($start,$end,$bill_no,$warehouse_id,$limit,$offset,$printlist);
            if (!empty($data['data'])){
                 $bill = $data['data'];
             }
             else{
                $bill = 'empty';
             }
        }
        else{
            $bill = 'error';
        }
        $total = $data['total'];
        $pagination = $this->pagination('api/v1/posreports/get_bill_details_reports',$limit,$offsetSegment,$total);
        $this->sma->send_json(array('bill_details' => $bill,'pagination'=>$pagination));
        
   }
   function postpaid_bills_get()
    {
	//$this->sma->checkPermissions();
        $api_key = $this->input->get('api-key');
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users'] = $this->posreports_api->getStaff();        
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['billers'] = $this->site->getAllCompanies('biller');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('postpaid_bills')));
        $meta = array('page_title' => lang('postpaid_bills'), 'bc' => $bc);
        $this->data['api_key'] = $api_key;
        $this->page_construct('posreports/postpaid_bills', $meta, $this->data);
    }
  public function postpaid_bills_report_post($start_date = NULL, $end_date = NULL, $warehouse_id = NULL){
        
        $start = $this->input->post('start_date');
        $end = $this->input->post('end_date');
        $limit = $this->input->post('pagelimit');        
        $warehouse_id = $this->input->post('warehouse_id');
        $dayrange = $this->input->post('day_range');
        $customer_id = $this->input->post('customer_id');
        $offsetSegment = 5;
        $offset = $this->uri->segment($offsetSegment,0);
        $data= '';
        //if ($start != '' && $end != '') {
            $data = $this->posreports_api->postpaid_bills_report($warehouse_id,$limit,$offset,$customer_id);
           
             if (!empty($data['data'])) {                 
                 $postpaid_bills = $data['data'];
             }
             else{                
                $postpaid_bills = 'empty';
             }
            
        //}
        //else{
        //    $postpaid_bills = 'error';
        //}
        $total = $data['total'];
        $customer_details = $data['customer_details'];
        $pagination = $this->pagination('api/v1/posreports/postpaid_bills_report',$limit,$offsetSegment,$total);
        //echo $daysummary;
        $this->sma->send_json(array('postpaid_bills' => $postpaid_bills,'customer_details'=>$customer_details,'pagination'=>$pagination));
   }
   function monthly_reports_get()
    {
	//$this->sma->checkPermissions();
        $api_key = $this->input->get('api-key');
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users'] = $this->posreports_api->getStaff();        
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['billers'] = $this->site->getAllCompanies('biller');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('category_wise_monthly_sales_report')));
        $meta = array('page_title' => lang('category_wise_monthly_sales_report'), 'bc' => $bc);
        $this->data['api_key'] = $api_key;
        $this->page_construct('posreports/monthly_reports', $meta, $this->data);
    }  

 public function get_monthly_reports_post($start= NULL,$end= NULL,$warehouse_id= NULL){
   
        $start = $this->input->post('start');
      /*  if ($start != '') {            
           $start = $start; 
        }
        else{
            $start = date('Y-m-d');
        }*/
        //$end = $this->input->post('end');
        $warehouse_id = $this->input->post('warehouse_id');
        $limit = $this->input->post('pagelimit');     
        $offsetSegment = 5;
        $offset = $this->uri->segment($offsetSegment,0);
        $data= '';
        if ($start != '') {
            $data = $this->posreports_api->getMonthlyReport($start,$warehouse_id,$limit,$offset);
         
        if (!empty($data['data'])) {
                 
                 $MonthlyReports = $data['data'];
             }
             else{
                
                $MonthlyReports = 'empty';
             }
        }
        else{
            $MonthlyReports = 'error';
        }
   // echo $MonthlyReports;
        $total = $data['total'];
        $pagination = $this->pagination('api/v1/posreports/get_monthly_reports',$limit,$offsetSegment,$total);
        $this->sma->send_json(array('monthly_reports' => $MonthlyReports,'pagination'=>$pagination));
        /*$this->sma->send_json(array('monthly_reports' => $month));*/
   }
    function hourly_wise_get()
    {
	//$this->sma->checkPermissions();
        $api_key = $this->input->get('api-key');
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users'] = $this->posreports_api->getStaff();        
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['billers'] = $this->site->getAllCompanies('biller');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('hourly_wise')));
        $meta = array('page_title' => lang('hourly_wise'), 'bc' => $bc);
        $this->data['api_key'] = $api_key;
        $this->page_construct('posreports/hourly_wise', $meta, $this->data);
    }
  public function get_HourlySummaryreports_post($start_date = NULL, $end_date = NULL, $warehouse_id = NULL, $time_range = NULL){
        
        $start = $this->input->post('start_date');
        $end = $this->input->post('end_date');
        $limit = $this->input->post('pagelimit');        
        $warehouse_id = $this->input->post('warehouse_id');
        $offsetSegment = 5;
        $offset = $this->uri->segment($offsetSegment,0);
        $time_range = $this->input->post('time_range');
		$printlist = $this->input->post('printlist') ? $this->input->post('printlist') : 0;    
        $this->session->set_userdata('start_date', $this->input->post('start_date'));
        $this->session->set_userdata('end_date', $this->input->post('end_date'));

        $data= '';
        if ($start != '' && $end != '') {
            $data = $this->posreports_api->getHourlysummaryReport($start,$end,$warehouse_id,$time_range,$limit,$offset,$printlist);
           
             if (!empty($data['data'])) {                 
                 $hourlysummary = $data['data'];
             }
             else{                
                $hourlysummary = 'empty';
             }
            
        }
        else{
            $hourlysummary = 'error';
        }
        $total = $data['total'];
        $pagination = $this->pagination('api/v1/posreports/get_HourlySummaryreports',$limit,$offsetSegment,$total);
        //echo $daysummary;
        $this->sma->send_json(array('hourlysummary' => $hourlysummary,'pagination'=>$pagination));
   }
    function discount_summary_get()
    {
	//$this->sma->checkPermissions();
        $api_key = $this->input->get('api-key');
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users'] = $this->posreports_api->getStaff();        
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['billers'] = $this->site->getAllCompanies('biller');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('discount_summary')));
        $meta = array('page_title' => lang('discount_summary'), 'bc' => $bc);
        $this->data['api_key'] = $api_key;
        $this->page_construct('posreports/discount_summary', $meta, $this->data);
    }  
   public function get_DiscountSummary_post($start = NULL, $end = NULL, $dis_type = NULL, $warehouse_id = NULL){
        
        $start = $this->input->post('start_date');
        $end = $this->input->post('end_date');
        $dis_type = $this->input->post('dis_type');
        $warehouse_id = $this->input->post('warehouse_id');
        $limit = $this->input->post('pagelimit');  
		$printlist = $this->input->post('printlist') ? $this->input->post('printlist') : 0;          
        $offsetSegment = 5;
        $offset = $this->uri->segment($offsetSegment,0);

        $this->session->set_userdata('start_date', $this->input->post('start_date'));
        $this->session->set_userdata('end_date', $this->input->post('end_date'));

        $data= '';
        if ($start != '' && $end != '' && $dis_type != '') {
            if($dis_type == 'dis_details') 
             {                
                $data = $this->posreports_api->getDiscountDetailsReport($start, $end, $warehouse_id,$limit,$offset, $printlist);
             }
             else
             {
                $data = $this->posreports_api->getDiscountsummaryReport($start, $end, $warehouse_id,$limit,$offset, $printlist);
             }
            if (!empty($data['data']))
             {
                 $discount = $data['data'];
             }
             else
             {  
                $discount = 'empty';
             }
        }
        else
        {
            $discount = 'error';
        }
        $total = $data['total'];
        $pagination = $this->pagination('api/v1/posreports/get_DiscountSummary',$limit,$offsetSegment,$total);
        $this->sma->send_json(array('discount' => $discount,'pagination'=>$pagination));
   }
   function void_bills_get()
    {
	//$this->sma->checkPermissions();
        $api_key = $this->input->get('api-key');
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users'] = $this->posreports_api->getStaff();        
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['billers'] = $this->site->getAllCompanies('biller');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('void_bill')));
        $meta = array('page_title' => lang('void_bill'), 'bc' => $bc);
        $this->data['api_key'] = $api_key;
        $this->page_construct('posreports/void_bills', $meta, $this->data);
    }  
  public function get_voidbills_reports_post($start = NULL, $end = NULL, $warehouse_id = NULL){
        $start = $this->input->post('start_date');
        $end = $this->input->post('end_date');
        $warehouse_id = $this->input->post('warehouse_id');
        $limit = $this->input->post('pagelimit');        
        $offsetSegment = 5;
        $offset = $this->uri->segment($offsetSegment,0);
		$printlist = $this->input->post('printlist') ? $this->input->post('printlist') : 0;    
        $this->session->set_userdata('start_date', $this->input->post('start_date'));
        $this->session->set_userdata('end_date', $this->input->post('end_date'));

        $data= '';
        if ($start != '' && $end != '') {
            $data = $this->posreports_api->getVoidBillsReport($start,$end,$warehouse_id,$limit,$offset,$printlist);  
            if (!empty($data['data'])){
                 
                 $voidbills = $data['data'];
             }
             else{
                
                $voidbills = 'empty';
             }
        }
        else{
            $voidbills = 'error';
        }
        $total = $data['total'];
        $pagination = $this->pagination('api/v1/posreports/get_popular_reports',$limit,$offsetSegment,$total);
        $this->sma->send_json(array('void_bills' => $voidbills,'pagination'=>$pagination));
   }
   function tax_reports_get()
    {
	//$this->sma->checkPermissions();
        $api_key = $this->input->get('api-key');
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users'] = $this->posreports_api->getStaff();        
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['billers'] = $this->site->getAllCompanies('biller');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('tax_reports')));
        $meta = array('page_title' => lang('tax_reports'), 'bc' => $bc);
        $this->data['api_key'] = $api_key;
        $this->page_construct('posreports/tax_reports', $meta, $this->data);
    }   
  public function get_tax_reports_post($start = NULL, $end = NULL, $warehouse_id = NULL){
        $start = $this->input->post('start_date');
        $end = $this->input->post('end_date');
        $warehouse_id = $this->input->post('warehouse_id');
        $limit = $this->input->post('pagelimit');        
        $offsetSegment = 5;
        $offset = $this->uri->segment($offsetSegment,0);
		$printlist = $this->input->post('printlist') ? $this->input->post('printlist') : 0;    
        $this->session->set_userdata('start_date', $this->input->post('start_date'));
        $this->session->set_userdata('end_date', $this->input->post('end_date'));

        $data= '';
        if ($start != '' && $end != '') {
            $data = $this->posreports_api->getTaxReport($start,$end,$warehouse_id,$limit,$offset,$printlist);  
            if (!empty($data['data'])) {
                 
                 $taxrep = $data['data'];
             }
             else{
                
                $taxrep = 'empty';
             }
        }
        else{
            $taxrep = 'error';
        }
        $total = $data['total'];
        $pagination = $this->pagination('api/v1/posreports/get_tax_reports',$limit,$offsetSegment,$total);
        $this->sma->send_json(array('tax' => $taxrep,'pagination'=>$pagination));
   }
   function popular_analysis_get()
    {
	//$this->sma->checkPermissions();
        $api_key = $this->input->get('api-key');
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users'] = $this->posreports_api->getStaff();        
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['billers'] = $this->site->getAllCompanies('biller');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('popular_analysis_reports')));
        $meta = array('page_title' => lang('popular_analysis_reports'), 'bc' => $bc);
        $this->data['api_key'] = $api_key;
        $this->page_construct('posreports/popular_analysis', $meta, $this->data);
    }    
    public function get_popular_reports_post($start = NULL, $end = NULL, $popular = NULL, $warehouse_id = NULL){
        $start = $this->input->post('start_date');
        $end = $this->input->post('end_date');
        $popular = $this->input->post('popular');
        $warehouse_id = $this->input->post('warehouse_id');
        $limit = $this->input->post('pagelimit');        
        $offsetSegment = 5;
        $offset = $this->uri->segment($offsetSegment,0);
		$printlist = $this->input->post('printlist') ? $this->input->post('printlist') : 0;    
        $this->session->set_userdata('start_date', $this->input->post('start_date'));
        $this->session->set_userdata('end_date', $this->input->post('end_date'));

        $data= '';
        if ($start != '' && $end != '') {
           if($popular == 'popular') 
             {                
                $data = $this->posreports_api->getPopularReports($start,$end,$warehouse_id,$limit,$offset,$printlist);
                $round_tot = $this->posreports_api->getRoundamount($start,$end,$warehouse_id);
             }
             else
             {
              $data = $this->posreports_api->getNonPopularReports($start,$end,$warehouse_id,$limit,$offset,$printlist); 
              $round_tot = $this->posreports_api->getRoundamount($start,$end,$warehouse_id); 
             }
           if ($round_tot != false) {
                 $round = $round_tot;
             }
             else{
                $round = 'empty';
             }
            if (!empty($data['data'])){
                 $popular = $data['data'];
             }
             else{
                $popular = 'empty';
             }
        }
        else{
            $popular = 'error';
        }
        $total = $data['total'];
        $pagination = $this->pagination('api/v1/posreports/get_popular_reports',$limit,$offsetSegment,$total);
        $this->sma->send_json(array('popular_non_popular' => $popular,'round' => $round,'pagination'=>$pagination));
   }     
    function cover_analysis_get()
    {
	//$this->sma->checkPermissions();
        $api_key = $this->input->get('api-key');
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users'] = $this->posreports_api->getStaff();        
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['billers'] = $this->site->getAllCompanies('biller');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('cover_analysis')));
        $meta = array('page_title' => lang('cover_analysis'), 'bc' => $bc);
        $this->data['api_key'] = $api_key;
        $this->page_construct('posreports/cover_analysis', $meta, $this->data);
    }    
 public function get_cover_analysis_post($start = NULL, $end = NULL, $warehouse_id = NULL){
        $start = $this->input->post('start');
        $end = $this->input->post('end');
        $warehouse_id = $this->input->post('warehouse_id');
        $limit = $this->input->post('pagelimit');        
        $offsetSegment = 5;
        $offset = $this->uri->segment($offsetSegment,0);
		$printlist = $this->input->post('printlist') ? $this->input->post('printlist') : 0;    
        $this->session->set_userdata('start_date', $this->input->post('start'));
        $this->session->set_userdata('end_date', $this->input->post('end'));

        $data= '';
        if ($start != '' && $end != '') {
            $data = $this->posreports_api->getCoverAnalysisReport($start,$end,$warehouse_id,$limit,$offset,$printlist);
            if (!empty($data['data'])){
                 
                 $coveranalysis = $data['data'];
             }
             else{
                
                $coveranalysis = 'empty';
             }
        }
        else{
            $coveranalysis = 'error';
        }
        $total = $data['total'];
        $pagination = $this->pagination('api/v1/posreports/get_cover_analysis',$limit,$offsetSegment,$total);
        $this->sma->send_json(array('cover_analysis' => $coveranalysis,'pagination'=>$pagination));
   }
   
   function order_timing_get()
    {
	//$this->sma->checkPermissions();
	if(!$this->Settings->recipe_time_management){ $this->session->set_flashdata('error','Access_denied');redirect();}
        $api_key = $this->input->get('api-key');
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users'] = $this->posreports_api->getStaff();
        /*$this->data['users'] = $this->posreports_api->getPosSettlementReport();*/
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['billers'] = $this->site->getAllCompanies('biller');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('order_time_report')));
        $meta = array('page_title' => lang('order_time_report'), 'bc' => $bc);
        $this->data['api_key'] = $api_key;
        $this->page_construct('posreports/order_timing', $meta, $this->data);
    }  
 public function get_ordertiming_details_post($start_date = NULL, $end_date = NULL, $warehouse_id = NULL){

        $start = $this->input->post('start_date');
        $end = $this->input->post('end_date');
        
        $warehouse_id = $this->input->post('warehouse_id');
        $limit = $this->input->post('pagelimit');        
        $offsetSegment = 5;
        $offset = $this->uri->segment($offsetSegment,0);
			$printlist = $this->input->post('printlist') ? $this->input->post('printlist') : 0;    
        $this->session->set_userdata('start_date', $this->input->post('start_date'));
        $this->session->set_userdata('end_date', $this->input->post('end_date'));
        
        $data= '';

        if ($start != '' && $end != '') {
             $data = $this->posreports_api->getOrderTimeReport($start,$end,$warehouse_id,$limit,$offset,$printlist);
             
             if (!empty($data['data'])) {
                 
                 $ordertime = $data['data'];
             }
             else{
                
                $ordertime = 'empty';
             }
        }
        else{
            $ordertime = 'error';
        }
        $total = $data['total'];
        $pagination = $this->pagination('api/v1/posreports/get_ordertiming_details',$limit,$offsetSegment,$total);
        $this->sma->send_json(array('ordertime' => $ordertime,'pagination'=>$pagination));
   }
   function products_get()
    {
	//$this->sma->checkPermissions();
        $api_key = $this->input->get('api-key');
        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['categories'] = $this->site->getAllCategories();

        $this->data['brands'] = $this->site->getAllBrands();
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        
        if ($this->input->post('start_date')) {
            $dt = "From " . $this->input->post('start_date') . " to " . $this->input->post('end_date');
        } else {
            $dt = "Till " . $this->input->post('end_date');
        }
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('sale_items_report')));
        $meta = array('page_title' => lang('products_report'), 'bc' => $bc);
        $this->data['api_key'] = $api_key;
        $this->page_construct('posreports/products', $meta, $this->data);
    }
    function getProductsReport_post($pdf = NULL, $xls = NULL)
    {
        
        $product = $this->input->get('product') ? $this->input->get('product') : NULL;
        //$category = $this->input->get('category') ? $this->input->get('category') : NULL;
        //$brand = $this->input->get('brand') ? $this->input->get('brand') : NULL;
        //$subcategory = $this->input->get('subcategory') ? $this->input->get('subcategory') : NULL;
        $warehouse_id = $this->input->get('warehouse') ? $this->input->get('warehouse') : NULL;
        $start_date = $this->input->get('start_date') ? $this->input->get('start_date') : NULL;
        $end_date = $this->input->get('end_date') ? $this->input->get('end_date') : NULL;
        $purchased = "
                (
                    SELECT
                        recipe_id,product_id,SU.name,SP.price,SRP.quantity,SRU.name unit_name,
         
                        SUM(CASE
                         WHEN (SU.name='Kg' AND SRU.name='Gram') THEN (SP.price/SRU.operation_value)*SRP.quantity
                         WHEN (SU.name='Kg' AND SRU.name='Kg') THEN SP.price*SRP.quantity
                         
                         WHEN (SU.name='Litre' AND SRU.name='Millilitre') THEN (SP.price/SRU.operation_value)*SRP.quantity
                         WHEN (SU.name='Litre' AND SRU.name='Litre') THEN SP.price*SRP.quantity
                         
                         WHEN (SU.name='Package' AND SRU.name='Pieces') THEN (SP.price/SRU.operation_value)*SRP.quantity
                         WHEN (SU.name='Package' AND SRU.name='Package') THEN SP.price*SRP.quantity
                 
                        ELSE 0 END) purchased
                
                    FROM ".$this->db->dbprefix('recipe_products')." SRP         
                    JOIN ".$this->db->dbprefix('products')." SP on SRP.product_id=SP.id 
                    JOIN ".$this->db->dbprefix('units')." SU on SU.id=SP.unit 
                    JOIN ".$this->db->dbprefix('units')." SRU on SRU.id=SRP.unit_id
                    group by SRP.recipe_id
                    order by product_id
                ) P";
        $sold = "
            (
                SELECT recipe_id,SUM(quantity) as quantity,SUM(SBI.unit_price*quantity) as sold FROM ".$this->db->dbprefix('bil_items')." SBI
                            join ".$this->db->dbprefix('bils')." SB on SBI.bil_id=SB.id
                            where SB.payment_status='completed'";
            if ($start_date) {
                $sold .=' AND DATE_FORMAT(SB.date, "%Y-%m-%d") >="'.$start_date.'"';
            }
            if ($end_date) {
             $sold .=' AND DATE_FORMAT(SB.date, "%Y-%m-%d") <="'.$end_date.'"';
            }
            if($warehouse_id != 0){
                $sold .=' AND SBI.warehouse_id='.$warehouse_id;    
            }
            if(!$this->Owner && !$this->Admin){
                $sold .= " AND SB.table_whitelisted =0";
            }
         $sold .= " group by SBI.recipe_id
            ) SLSold";
            //echo $sold;exit;
        if ($pdf || $xls) {
            $this->db
                ->select($this->db->dbprefix('recipe').".code,
                ".$this->db->dbprefix('recipe').".name,
                SUM(P.purchased*SLSold.quantity) as purchased,
                SLSold.sold,
                SUM(SLSold.sold-(P.purchased*SLSold.quantity)) as profitloss,
                SLSold.Quantity
                    ")
                ->from($this->db->dbprefix('recipe'))
                ->join($purchased, $this->db->dbprefix('recipe').".id=P.recipe_id")
                ->join($sold, $this->db->dbprefix('recipe').".id=SLSold.recipe_id")
                ->group_by($this->db->dbprefix('recipe').".id");
            $q = $this->db->get();
            //echo "<pre>";
            //print_r($q->result());die;
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = NULL;
            }

            if (!empty($data)) {

                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle(lang('products_report'));
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('recipe_code'));
                $this->excel->getActiveSheet()->SetCellValue('B1', lang('recipe_name'));
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('purchased'));
                $this->excel->getActiveSheet()->SetCellValue('D1', lang('sold'));
                $this->excel->getActiveSheet()->SetCellValue('E1', lang('profit_loss'));

                $row = 2;
                $sQty = 0;
                $pQty = 0;
                $sAmt = 0;
                $pAmt = 0;
                $bQty = 0;
                $bAmt = 0;
                $pl = 0;
                /*echo "<pre>";
                print_r($data);die;*/
                foreach ($data as $data_row) {
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $data_row->code);
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->name);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->purchased);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->sold);
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, $data_row->profitloss);
                    $pQty += $data_row->purchased;
                    $sQty += $data_row->sold;
                    $pl += $data_row->profitloss;
                    $row++;
                }
                $this->excel->getActiveSheet()->getStyle("C" . $row . ":I" . $row)->getBorders()
                    ->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
                $this->excel->getActiveSheet()->SetCellValue('C' . $row, $pQty);
                $this->excel->getActiveSheet()->SetCellValue('D' . $row, $sQty);
                $this->excel->getActiveSheet()->SetCellValue('E' . $row, $pl);

                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(35);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(35);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->excel->getActiveSheet()->getStyle('C2:G' . $row)->getAlignment()->setWrapText(true);
                $filename = 'products_report';
                $this->load->helper('excel');
                create_excel($this->excel, $filename);

            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER["HTTP_REFERER"]);

        } else {

            $this->load->library('datatables');
            $this->datatables
                    ->select("'sno',".
                        $this->db->dbprefix('recipe').".code,
                ".$this->db->dbprefix('recipe').".name,
                SUM(P.purchased*SLSold.quantity) as purchased,
                SLSold.sold,
                SUM(SLSold.sold-(P.purchased*SLSold.quantity)) as profitloss,
                SLSold.Quantity
                    ")
                    ->from($this->db->dbprefix('recipe'))
                    ->join($purchased, $this->db->dbprefix('recipe').".id=P.recipe_id")
                    ->join($sold, $this->db->dbprefix('recipe').".id=SLSold.recipe_id")
                    ->group_by($this->db->dbprefix('recipe').".id");
                    //print_R($this->datatables);exit;
            if ($product) {
                $this->datatables->where($this->db->dbprefix('recipe') . ".id", $product);
            }
            
            echo $this->datatables->generate();
//print_R($this->db);
        }

    }
     function categories_get()
    {
	//$this->sma->checkPermissions();
        $api_key = $this->input->get('api-key');
        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['categories'] = $this->site->getAllCategories();
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        if ($this->input->post('start_date')) {
            $dt = "From " . $this->input->post('start_date') . " to " . $this->input->post('end_date');
        } else {
            $dt = "Till " . $this->input->post('end_date');
        }
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('categories_report')));
        $meta = array('page_title' => lang('categories_report'), 'bc' => $bc);
        $this->data['api_key'] = $api_key;
        $this->page_construct('posreports/categories', $meta, $this->data);
    }

    function getCategoriesReport_post($pdf = NULL, $xls = NULL)
    {
        
        $warehouse = $this->input->get('warehouse') ? $this->input->get('warehouse') : NULL;
        $category = $this->input->get('category') ? $this->input->get('category') : NULL;
        $start_date = $this->input->get('start_date') ? $this->input->get('start_date') : NULL;
        $end_date = $this->input->get('end_date') ? $this->input->get('end_date') : NULL;

        $pp = "( SELECT pp.category_id as category, SUM( pi.quantity ) purchasedQty, SUM( pi.subtotal ) totalPurchase from {$this->db->dbprefix('products')} pp
                left JOIN " . $this->db->dbprefix('purchase_items') . " pi ON pp.id = pi.product_id
                left join " . $this->db->dbprefix('purchases') . " p ON p.id = pi.purchase_id ";

        $sp = "( SELECT sp.category_id as category, SUM( si.quantity ) soldQty, SUM( si.subtotal ) totalSale12,SUM(DISTINCT s.total-s.total_discount+CASE WHEN (s.tax_type = 1) THEN s.total_tax ELSE 0 END) as totalSale
         from {$this->db->dbprefix('recipe')} sp
                left JOIN " . $this->db->dbprefix('bil_items') . " si ON sp.id = si.recipe_id
                left join " . $this->db->dbprefix('bils') . " s ON s.id = si.bil_id ";
            
        if ($start_date || $warehouse) {
            $pp .= " WHERE ";
            $sp .= " WHERE ";
            if ($start_date) {
                $start_date = $this->sma->fld($start_date);
                $end_date = $end_date ? $this->sma->fld($end_date) : date('Y-m-d');
                $pp .= " p.date >= '{$start_date}' AND p.date < '{$end_date}' ";
                $sp .= " s.date >= '{$start_date}' AND s.date < '{$end_date}' ";
                if ($warehouse) {
                    $pp .= " AND ";
                    $sp .= " AND ";
                }
            }
            if ($warehouse) {
                $pp .= " pi.warehouse_id = '{$warehouse}' ";
                $sp .= " si.warehouse_id = '{$warehouse}' ";
            }
        }
	if(!$this->Owner && !$this->Admin)
            {
                $sp .= "AND s.table_whitelisted = 0";                 
            }
        $pp .= " GROUP BY pp.category_id ) PCosts";
        $sp .= " GROUP BY sp.category_id ) PSales";

        if ($pdf || $xls) {

            $this->db
                ->select($this->db->dbprefix('recipe_categories') . ".code, " . $this->db->dbprefix('recipe_categories') . ".name,
                    SUM( COALESCE( PCosts.purchasedQty, 0 ) ) as PurchasedQty,
                    SUM( COALESCE( PSales.soldQty, 0 ) ) as SoldQty,
                    SUM( COALESCE( PCosts.totalPurchase, 0 ) ) as TotalPurchase,
                    SUM( COALESCE( PSales.totalSale, 0 ) ) as TotalSales,
                    (SUM( COALESCE( PSales.totalSale, 0 ) )- SUM( COALESCE( PCosts.totalPurchase, 0 ) ) ) as Profit", FALSE)
                ->from('recipe_categories')
                ->join($sp, 'recipe_categories.id = PSales.category', 'left')
                ->join($pp, 'recipe_categories.id = PCosts.category', 'left')
                ->group_by('recipe_categories.id, recipe_categories.code, recipe_categories.name')
                ->order_by('recipe_categories.code', 'asc');

            if ($category) {
                $this->db->where($this->db->dbprefix('recipe_categories') . ".id", $category);
            }
            $this->db->where($this->db->dbprefix('recipe_categories') . ".parent_id", 0);
            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = NULL;
            }

            if (!empty($data)) {

                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle(lang('categories_report'));
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('category_code'));
                $this->excel->getActiveSheet()->SetCellValue('B1', lang('category_name'));
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('purchased'));
                $this->excel->getActiveSheet()->SetCellValue('D1', lang('sold'));
                $this->excel->getActiveSheet()->SetCellValue('E1', lang('purchased_amount'));
                $this->excel->getActiveSheet()->SetCellValue('F1', lang('sold_amount'));
                $this->excel->getActiveSheet()->SetCellValue('G1', lang('profit_loss'));

                $row = 2;
                $sQty = 0;
                $pQty = 0;
                $sAmt = 0;
                $pAmt = 0;
                $pl = 0;
                foreach ($data as $data_row) {
                    $profit = $data_row->TotalSales - $data_row->TotalPurchase;
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $data_row->code);
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->name);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->PurchasedQty);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->SoldQty);
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, $data_row->TotalPurchase);
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, $data_row->TotalSales);
                    $this->excel->getActiveSheet()->SetCellValue('G' . $row, $profit);
                    $pQty += $data_row->PurchasedQty;
                    $sQty += $data_row->SoldQty;
                    $pAmt += $data_row->TotalPurchase;
                    $sAmt += $data_row->TotalSales;
                    $pl += $profit;
                    $row++;
                }
                $this->excel->getActiveSheet()->getStyle("C" . $row . ":G" . $row)->getBorders()
                    ->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
                $this->excel->getActiveSheet()->SetCellValue('C' . $row, $pQty);
                $this->excel->getActiveSheet()->SetCellValue('D' . $row, $sQty);
                $this->excel->getActiveSheet()->SetCellValue('E' . $row, $pAmt);
                $this->excel->getActiveSheet()->SetCellValue('F' . $row, $sAmt);
                $this->excel->getActiveSheet()->SetCellValue('G' . $row, $pl);

                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(35);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(35);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(25);
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->excel->getActiveSheet()->getStyle('C2:G' . $row)->getAlignment()->setWrapText(true);
                $filename = 'categories_report';
                $this->load->helper('excel');
                create_excel($this->excel, $filename);

            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER["HTTP_REFERER"]);

        } else {


            $this->load->library('datatables');
            $this->datatables
                ->select($this->db->dbprefix('recipe_categories') . ".id as cid,'sno', " .$this->db->dbprefix('recipe_categories') . ".code, " . $this->db->dbprefix('recipe_categories') . ".name,
                    SUM( COALESCE( PCosts.purchasedQty, 0 ) ) as PurchasedQty,
                    SUM( COALESCE( PSales.soldQty, 0 ) ) as SoldQty,
                    SUM( COALESCE( PCosts.totalPurchase, 0 ) ) as TotalPurchase,
                    SUM( COALESCE( PSales.totalSale, 0 ) ) as TotalSales,
                    (SUM( COALESCE( PSales.totalSale, 0 ) )- SUM( COALESCE( PCosts.totalPurchase, 0 ) ) ) as Profit", FALSE)
                ->from('recipe_categories')
                ->join($sp, 'recipe_categories.id = PSales.category', 'left')
                ->join($pp, 'recipe_categories.id = PCosts.category', 'left');

            if ($category) {
                $this->datatables->where('recipe_categories.id', $category);
            }
            $this->db->where($this->db->dbprefix('recipe_categories') . ".parent_id", 0);
            $this->datatables->group_by('recipe_categories.id, recipe_categories.code, recipe_categories.name, PSales.SoldQty, PSales.totalSale, PCosts.purchasedQty, PCosts.totalPurchase');
            $this->datatables->unset_column('cid');
            echo $this->datatables->generate();

        }

    }

    function brands_get()
    {
	//$this->sma->checkPermissions();
        $api_key = $this->input->get('api-key');
        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['brands'] = $this->site->getAllBrands();
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        if ($this->input->post('start_date')) {
            $dt = "From " . $this->input->post('start_date') . " to " . $this->input->post('end_date');
        } else {
            $dt = "Till " . $this->input->post('end_date');
        }
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('brands_report')));
        $meta = array('page_title' => lang('brands_report'), 'bc' => $bc);
        $this->data['api_key'] = $api_key;
        $this->page_construct('posreports/brands', $meta, $this->data);
    }

    function getBrandsReport_post($pdf = NULL, $xls = NULL)
    {
        
        $warehouse = $this->input->get('warehouse') ? $this->input->get('warehouse') : NULL;
        $brand = $this->input->get('brand') ? $this->input->get('brand') : NULL;
        $start_date = $this->input->get('start_date') ? $this->input->get('start_date') : NULL;
        $end_date = $this->input->get('end_date') ? $this->input->get('end_date') : NULL;

        $pp = "( SELECT pp.brand as brand, SUM( pi.quantity ) purchasedQty, SUM( pi.subtotal ) totalPurchase from {$this->db->dbprefix('products')} pp
                left JOIN " . $this->db->dbprefix('purchase_items') . " pi ON pp.id = pi.product_id
                left join " . $this->db->dbprefix('purchases') . " p ON p.id = pi.purchase_id ";

        $sp = "( SELECT sp.brand as brand, SUM( si.quantity ) soldQty,SUM(s.total-s.total_discount+CASE WHEN (s.tax_type= 1) THEN s.total_tax ELSE 0 END) as totalSale, SUM( si.subtotal ) totalSale1 from {$this->db->dbprefix('products')} sp
                left JOIN " . $this->db->dbprefix('bil_items') . " si ON sp.id = si.recipe_id
                left join " . $this->db->dbprefix('bils') . " s ON s.id = si.bil_id ";
                /*echo $sp;die;*/
        if ($start_date || $warehouse) {
            $pp .= " WHERE ";
            $sp .= " WHERE ";
            if ($start_date) {
                $start_date = $this->sma->fld($start_date);
                $end_date = $end_date ? $this->sma->fld($end_date) : date('Y-m-d');
                $pp .= " p.date >= '{$start_date}' AND p.date < '{$end_date}' ";
                $sp .= " s.date >= '{$start_date}' AND s.date < '{$end_date}' ";
                if ($warehouse) {
                    $pp .= " AND ";
                    $sp .= " AND ";
                }
            }
            if ($warehouse) {
                $pp .= " pi.warehouse_id = '{$warehouse}' ";
                $sp .= " si.warehouse_id = '{$warehouse}' ";
            }
        }
        
        $pp .= " GROUP BY pp.brand ) PCosts";
        $sp .= " GROUP BY sp.brand ) PSales";

        if ($pdf || $xls) {

            $this->db
                ->select($this->db->dbprefix('brands') . ".name,
                    SUM( COALESCE( PCosts.purchasedQty, 0 ) ) as PurchasedQty,
                    SUM( COALESCE( PSales.soldQty, 0 ) ) as SoldQty,
                    SUM( COALESCE( PCosts.totalPurchase, 0 ) ) as TotalPurchase,
                    SUM( COALESCE( PSales.totalSale, 0 ) ) as TotalSales,
                    (SUM( COALESCE( PSales.totalSale, 0 ) )- SUM( COALESCE( PCosts.totalPurchase, 0 ) ) ) as Profit", FALSE)
                ->from('brands')
                ->join($sp, 'brands.id = PSales.brand', 'left')
                ->join($pp, 'brands.id = PCosts.brand', 'left')
                ->group_by('brands.id, brands.name')
                ->order_by('brands.code', 'asc');

            if ($brand) {
                $this->db->where($this->db->dbprefix('brands') . ".id", $brand);
            }

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = NULL;
            }

            if (!empty($data)) {

                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle(lang('brands_report'));
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('brands'));
                $this->excel->getActiveSheet()->SetCellValue('B1', lang('purchased'));
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('sold'));
                $this->excel->getActiveSheet()->SetCellValue('D1', lang('purchased_amount'));
                $this->excel->getActiveSheet()->SetCellValue('E1', lang('sold_amount'));
                $this->excel->getActiveSheet()->SetCellValue('F1', lang('profit_loss'));

                $row = 2; $sQty = 0; $pQty = 0; $sAmt = 0; $pAmt = 0; $pl = 0;
                foreach ($data as $data_row) {
                    $profit = $data_row->TotalSales - $data_row->TotalPurchase;
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $data_row->name);
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->PurchasedQty);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->SoldQty);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->TotalPurchase);
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, $data_row->TotalSales);
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, $profit);
                    $pQty += $data_row->PurchasedQty;
                    $sQty += $data_row->SoldQty;
                    $pAmt += $data_row->TotalPurchase;
                    $sAmt += $data_row->TotalSales;
                    $pl += $profit;
                    $row++;
                }
                $this->excel->getActiveSheet()->getStyle("B" . $row . ":F" . $row)->getBorders()
                    ->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
                $this->excel->getActiveSheet()->SetCellValue('B' . $row, $pQty);
                $this->excel->getActiveSheet()->SetCellValue('C' . $row, $sQty);
                $this->excel->getActiveSheet()->SetCellValue('D' . $row, $pAmt);
                $this->excel->getActiveSheet()->SetCellValue('E' . $row, $sAmt);
                $this->excel->getActiveSheet()->SetCellValue('F' . $row, $pl);

                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(35);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->excel->getActiveSheet()->getStyle('C2:G' . $row)->getAlignment()->setWrapText(true);
                $filename = 'brands_report';
                $this->load->helper('excel');
                create_excel($this->excel, $filename);

            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER["HTTP_REFERER"]);

        } else {


            $this->load->library('datatables');
            $this->datatables
                ->select($this->db->dbprefix('brands') . ".id as id,'sno', " . $this->db->dbprefix('brands') . ".name,
                    SUM( COALESCE( PCosts.purchasedQty, 0 ) ) as PurchasedQty,
                    SUM( COALESCE( PSales.soldQty, 0 ) ) as SoldQty,
                    SUM( COALESCE( PCosts.totalPurchase, 0 ) ) as TotalPurchase,
                    SUM( COALESCE( PSales.totalSale, 0 ) ) as TotalSales,
                    (SUM( COALESCE( PSales.totalSale, 0 ) )- SUM( COALESCE( PCosts.totalPurchase, 0 ) ) ) as Profit", FALSE)
                ->from('brands')
                ->join($sp, 'brands.id = PSales.brand', 'left')
                ->join($pp, 'brands.id = PCosts.brand', 'left');

            if ($brand) {
                $this->datatables->where('brands.id', $brand);
            }
            $this->datatables->group_by('brands.id, brands.name, PSales.SoldQty, PSales.totalSale, PCosts.purchasedQty, PCosts.totalPurchase');
            $this->datatables->unset_column('id');
            echo $this->datatables->generate();

        }

    }
    function daily_sales_get($warehouse_id = NULL, $year = NULL, $month = NULL, $pdf = NULL, $user_id = NULL)
    {
	//$this->sma->checkPermissions();
        $api_key = $this->input->get('api-key');$this->data['api_key'] = $api_key;

        if (!$this->Owner && !$this->Admin && $this->session->userdata('warehouse_id')) {
            $warehouse_id = $this->session->userdata('warehouse_id');
        }
        if (!$year) {
            $year = date('Y');
        }
        if (!$month) {
            $month = date('m');
        }
        if (!$this->Owner && !$this->Admin && !$this->session->userdata('view_right')) {
            $user_id = $this->session->userdata('user_id');
        }

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $config = array(
            'show_next_prev' => TRUE,
            'next_prev_url' => site_url('api/v1/posreports/daily_sales/'.($warehouse_id ? $warehouse_id : 0)),
            'month_type' => 'long',
            'day_type' => 'long'
        );

        $config['template'] = '{table_open}<div class="table-responsive"><table border="0" cellpadding="0" cellspacing="0" class="table table-bordered dfTable">{/table_open}
        {heading_row_start}<tr>{/heading_row_start}
        {heading_previous_cell}<th><a href="{previous_url}">&lt;&lt;</a></th>{/heading_previous_cell}
        {heading_title_cell}<th colspan="{colspan}" id="month_year">{heading}</th>{/heading_title_cell}
        {heading_next_cell}<th><a href="{next_url}">&gt;&gt;</a></th>{/heading_next_cell}
        {heading_row_end}</tr>{/heading_row_end}
        {week_row_start}<tr>{/week_row_start}
        {week_day_cell}<td class="cl_wday">{week_day}</td>{/week_day_cell}
        {week_row_end}</tr>{/week_row_end}
        {cal_row_start}<tr class="days">{/cal_row_start}
        {cal_cell_start}<td class="day">{/cal_cell_start}
        {cal_cell_content}
        <div class="day_num">{day}</div>
        <div class="content">{content}</div>
        {/cal_cell_content}
        {cal_cell_content_today}
        <div class="day_num highlight">{day}</div>
        <div class="content">{content}</div>
        {/cal_cell_content_today}
        {cal_cell_no_content}<div class="day_num">{day}</div>{/cal_cell_no_content}
        {cal_cell_no_content_today}<div class="day_num highlight">{day}</div>{/cal_cell_no_content_today}
        {cal_cell_blank}&nbsp;{/cal_cell_blank}
        {cal_cell_end}</td>{/cal_cell_end}
        {cal_row_end}</tr>{/cal_row_end}
        {table_close}</table></div>{/table_close}';


        $this->load->library('calendar', $config);

        $sales = $user_id ? $this->posreports_api->getStaffDailySales($user_id, $year, $month, $warehouse_id) : $this->posreports_api->getDailySales($year, $month, $warehouse_id);
        
        if (!empty($sales)) {

            foreach ($sales as $sale) {
                $daily_sale[$sale->date] = "<table class='table table-bordered table-hover table-striped table-condensed data' style='margin:0;'><tr><td>" . lang("total") . "</td><td>" . $this->sma->formatMoney($sale->total) . "</td></tr><tr><td>" . lang("discount") . "</td><td>" . $this->sma->formatMoney($sale->discount) . "</td></tr><tr><td>" . lang("tax") . "</td><td>" . $this->sma->formatMoney($sale->tax) . "</td></tr><tr><td>" . lang("grand_total") . "</td><td>" . $this->sma->formatMoney($sale->grand_total) . "</td></tr></table>";
            }
        } else {
            $daily_sale = array();
        }

        $this->data['calender'] = $this->calendar->generate($year, $month, $daily_sale);
        $this->data['year'] = $year;
        $this->data['month'] = $month;
        if ($pdf) {
            $html = $this->load->view($this->theme . 'posreports/daily', $this->data, true);
            $name = lang("daily_sales") . "_" . $year . "_" . $month . ".pdf";
            $html = str_replace('<p class="introtext">' . lang("reports_calendar_text") . '</p>', '', $html);
            $this->sma->generate_pdf($html, $name, null, null, null, null, null, 'L');
        }
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['warehouse_id'] = $warehouse_id;
        $this->data['sel_warehouse'] = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : NULL;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('daily_sales_report')));
        $meta = array('page_title' => lang('daily_sales_report'), 'bc' => $bc);
        $this->page_construct('posreports/daily', $meta, $this->data);

    }


    function monthly_sales_get($warehouse_id = NULL, $year = NULL, $pdf = NULL, $user_id = NULL)
    {
	//$this->sma->checkPermissions();
        $api_key = $this->input->get('api-key');$this->data['api_key'] = $api_key;
		
		$printlist = $this->input->get('printlist') ? $this->input->get('printlist') : 0;    
        if (!$this->Owner && !$this->Admin && $this->session->userdata('warehouse_id')) {
            $warehouse_id = $this->session->userdata('warehouse_id');
        }
        if (!$year) {
            $year = date('Y');
        }
        if (!$this->Owner && !$this->Admin && !$this->session->userdata('view_right')) {
            $user_id = $this->session->userdata('user_id');
        }
        $this->load->language('calendar');
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['year'] = $year;
        $this->data['sales'] = $user_id ? $this->posreports_api->getStaffMonthlySales($user_id, $year, $warehouse_id) : $this->posreports_api->getMonthlySales($year, $warehouse_id,$printlist);
        if ($pdf) {
            $html = $this->load->view($this->theme . 'posreports/monthly', $this->data, true);
            $name = lang("monthly_sales") . "_" . $year . ".pdf";
            $html = str_replace('<p class="introtext">' . lang("reports_calendar_text") . '</p>', '', $html);
            $this->sma->generate_pdf($html, $name, null, null, null, null, null, 'L');
        }
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['warehouse_id'] = $warehouse_id;
        $this->data['sel_warehouse'] = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : NULL;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('monthly_sales_report')));
        $meta = array('page_title' => lang('monthly_sales_report'), 'bc' => $bc);
        $this->page_construct('posreports/monthly', $meta, $this->data);

    }

    function sales_get()
    {
	//$this->sma->checkPermissions();
        $api_key = $this->input->get('api-key');$this->data['api_key'] = $api_key;
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users'] = $this->posreports_api->getStaff();
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['billers'] = $this->site->getAllCompanies('biller');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('sales_report')));
        $meta = array('page_title' => lang('sales_report'), 'bc' => $bc);
        $this->page_construct('posreports/sales', $meta, $this->data);
    }

    function getSalesReport_post($pdf = NULL, $xls = NULL)
    {
        
        $product = $this->input->get('product') ? $this->input->get('product') : NULL;
        $user = $this->input->get('user') ? $this->input->get('user') : NULL;
        $customer = $this->input->get('customer') ? $this->input->get('customer') : NULL;
        $biller = $this->input->get('biller') ? $this->input->get('biller') : NULL;
        $warehouse = $this->input->get('warehouse') ? $this->input->get('warehouse') : NULL;
        $reference_no = $this->input->get('reference_no') ? $this->input->get('reference_no') : NULL;
        $start_date = $this->input->get('start_date') ? $this->input->get('start_date') : NULL;
        $end_date = $this->input->get('end_date') ? $this->input->get('end_date') : NULL;
        $serial = $this->input->get('serial') ? $this->input->get('serial') : NULL;

        if ($start_date) {
            $start_date = $this->sma->fld($start_date);
            $end_date = $this->sma->fld($end_date);
        }
        if (!$this->Owner && !$this->Admin && !$this->session->userdata('view_right')) {
            $user = $this->session->userdata('user_id');
        }

        if ($pdf || $xls) {

            $this->db
                ->select("date, reference_no, biller, customer, GROUP_CONCAT(CONCAT(" . $this->db->dbprefix('bil_items') . ".recipe_name, ' (', " . $this->db->dbprefix('bil_items') . ".quantity, ')') SEPARATOR '\n') as iname, grand_total, paid, payment_status", FALSE)
                ->from('bils')
                ->join('bil_items', 'bil_items.bil_id=bils.id', 'left')
                /*->join('warehouses', 'warehouses.id=bils.warehouse_id', 'left')*/
                ->group_by('bils.id')
                ->order_by('bils.date desc');

            if ($user) {
                $this->db->where('bils.created_by', $user);
            }
            $this->db->where('bils.payment_status', 'Completed');
            if ($product) {
                $this->db->where('bil_items.recipe_id', $product);
            }
            /*if ($serial) {
                $this->db->like('bil_items.serial_no', $serial);
            }*/
            if ($biller) {
                $this->db->where('bils.biller_id', $biller);
            }
            if ($customer) {
                $this->db->where('bils.customer_id', $customer);
            }
           /* if ($warehouse) {
                $this->db->where('bils.warehouse_id', $warehouse);
            }*/
            if ($reference_no) {
                $this->db->like('bils.reference_no', $reference_no, 'both');
            }
            if ($start_date) {
                $this->db->where($this->db->dbprefix('bils').'.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }
           if(!$this->Owner && !$this->Admin){
                $this->db->where('bils.table_whitelisted', 0); 
            }
            $q = $this->db->get();
            
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {

                    $data[] = $row;
                }
            } else {
                $data = NULL;
            }

            if (!empty($data)) {
                $h_color = $this->Settings->excel_header_color;
                $f_color = $this->Settings->excel_footer_color;
                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle(lang('sales_report'));
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('date'));
                $this->excel->getActiveSheet()->getStyle('A1:I1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB($h_color);
                $this->excel->getActiveSheet()->SetCellValue('B1', lang('reference_no'));
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('biller'));
                $this->excel->getActiveSheet()->SetCellValue('D1', lang('customer'));
                $this->excel->getActiveSheet()->SetCellValue('E1', lang('product_qty'));
                $this->excel->getActiveSheet()->SetCellValue('F1', lang('grand_total'));
                $this->excel->getActiveSheet()->SetCellValue('G1', lang('paid'));
                $this->excel->getActiveSheet()->SetCellValue('H1', lang('balance'));
                $this->excel->getActiveSheet()->SetCellValue('I1', lang('payment_status'));
                
                $row = 2;
                $total = 0;
                $paid = 0;
                $balance = 0;
                foreach ($data as $data_row) {
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $this->sma->hrld($data_row->date));
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->reference_no);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->biller);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->customer);
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, $data_row->iname);
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, $data_row->grand_total);
                    $this->excel->getActiveSheet()->SetCellValue('G' . $row, $data_row->paid);
                    $this->excel->getActiveSheet()->SetCellValue('H' . $row, ($data_row->grand_total - $data_row->paid));
                    $this->excel->getActiveSheet()->SetCellValue('I' . $row, lang($data_row->payment_status));
                    $total += $data_row->grand_total;
                    $paid += $data_row->paid;
                    $balance += ($data_row->grand_total - $data_row->paid);
                    $row++;
                }
                $this->excel->getActiveSheet()->getStyle("F" . $row . ":H" . $row)->getBorders()
                    ->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
                $this->excel->getActiveSheet()->SetCellValue('F' . $row, $total);
                $this->excel->getActiveSheet()->SetCellValue('G' . $row, $paid);
                $this->excel->getActiveSheet()->SetCellValue('H' . $row, $balance);

                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->excel->getActiveSheet()->getStyle('E2:E' . $row)->getAlignment()->setWrapText(true);
                $filename = 'sales_report';
                $excelLastRow = $this->excel->setActiveSheetIndex(0)->getHighestRow();
                $this->excel->getActiveSheet()->getStyle('A'.$excelLastRow.':I'.$excelLastRow)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB($f_color);
                $this->load->helper('excel');
                create_excel($this->excel, $filename);

            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER["HTTP_REFERER"]);

        } else {
            $si = "( SELECT bil_id as bil_id, recipe_id as product_id from {$this->db->dbprefix('bil_items')} ";
            if ($product || $serial) { $si .= " WHERE "; }
            if ($product) {
                $si .= " {$this->db->dbprefix('bil_items')}.recipe_id = {$product} ";
            }
            if ($product && $serial) { $si .= " AND "; }
           /* if ($serial) {
                $si .= " {$this->db->dbprefix('bil_items')}.serial_no LIKe '%{$serial}%' ";
            }*/
            $si .= " GROUP BY {$this->db->dbprefix('bil_items')}.bil_id ) FSI";
            $this->load->library('datatables');
            $this->datatables
                ->select("'sno',DATE_FORMAT(date, '%d-%m-%Y') as date,{$this->db->dbprefix('warehouses')}.name as branch, reference_no, biller, customer, grand_total, paid, (grand_total-paid) as balance, payment_status, {$this->db->dbprefix('bils')}.id as id", FALSE)
                ->from('bils')
                ->join($si, 'FSI.bil_id=bils.id', 'left')
                ->join('warehouses', 'warehouses.id=bils.warehouse_id', 'left');
                // ->group_by('sales.id');

            if ($user) {
                $this->datatables->where('bils.created_by', $user);
            }
            $this->db->where('bils.payment_status', 'Completed');
            if ($product) {
                $this->datatables->where('FSI.product_id', $product);
            }
            /*if ($serial) {
                $this->datatables->like('FSI.serial_no', $serial);
            }*/
            if ($biller) {
                $this->datatables->where('bils.biller_id', $biller);
            }
            if ($customer) {
                $this->datatables->where('bils.customer_id', $customer);
            }
            /*if ($warehouse) {
                $this->datatables->where('bils.warehouse_id', $warehouse);
            }*/
            if ($reference_no) {
                $this->datatables->like('bils.reference_no', $reference_no, 'both');
            }
            if ($start_date) {
                $this->datatables->where($this->db->dbprefix('bils').'.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }
            if(!$this->Owner && !$this->Admin){
                $this->db->where('bils.table_whitelisted', 0); 
                
            }
/*echo "string";die;*/
            echo $this->datatables->generate();

        }

    }
    
    function daily_purchases_get($warehouse_id = NULL, $year = NULL, $month = NULL, $pdf = NULL, $user_id = NULL)
    {
        //$this->sma->checkPermissions();
	$api_key = $this->input->get('api-key');$this->data['api_key'] = $api_key;
        if (!$this->Owner && !$this->Admin && $this->session->userdata('warehouse_id')) {
            $warehouse_id = $this->session->userdata('warehouse_id');
        }
        if (!$year) {
            $year = date('Y');
        }
        if (!$month) {
            $month = date('m');
        }
        if (!$this->Owner && !$this->Admin && !$this->session->userdata('view_right')) {
            $user_id = $this->session->userdata('user_id');
        }
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $config = array(
            'show_next_prev' => TRUE,
            'next_prev_url' => admin_url('posreports/daily_purchases/'.($warehouse_id ? $warehouse_id : 0)),
            'month_type' => 'long',
            'day_type' => 'long'
        );

        $config['template'] = '{table_open}<div class="table-responsive"><table border="0" cellpadding="0" cellspacing="0" class="table table-bordered dfTable">{/table_open}
        {heading_row_start}<tr>{/heading_row_start}
        {heading_previous_cell}<th><a href="{previous_url}">&lt;&lt;</a></th>{/heading_previous_cell}
        {heading_title_cell}<th colspan="{colspan}" id="month_year">{heading}</th>{/heading_title_cell}
        {heading_next_cell}<th><a href="{next_url}">&gt;&gt;</a></th>{/heading_next_cell}
        {heading_row_end}</tr>{/heading_row_end}
        {week_row_start}<tr>{/week_row_start}
        {week_day_cell}<td class="cl_wday">{week_day}</td>{/week_day_cell}
        {week_row_end}</tr>{/week_row_end}
        {cal_row_start}<tr class="days">{/cal_row_start}
        {cal_cell_start}<td class="day">{/cal_cell_start}
        {cal_cell_content}
        <div class="day_num">{day}</div>
        <div class="content">{content}</div>
        {/cal_cell_content}
        {cal_cell_content_today}
        <div class="day_num highlight">{day}</div>
        <div class="content">{content}</div>
        {/cal_cell_content_today}
        {cal_cell_no_content}<div class="day_num">{day}</div>{/cal_cell_no_content}
        {cal_cell_no_content_today}<div class="day_num highlight">{day}</div>{/cal_cell_no_content_today}
        {cal_cell_blank}&nbsp;{/cal_cell_blank}
        {cal_cell_end}</td>{/cal_cell_end}
        {cal_row_end}</tr>{/cal_row_end}
        {table_close}</table></div>{/table_close}';

        $this->load->library('calendar', $config);
        $purchases = $user_id ? $this->posreports_api->getStaffDailyPurchases($user_id, $year, $month, $warehouse_id) : $this->posreports_api->getDailyPurchases($year, $month, $warehouse_id);

        if (!empty($purchases)) {
            foreach ($purchases as $purchase) {
                $daily_purchase[$purchase->date] = "<table class='table table-bordered table-hover table-striped table-condensed data' style='margin:0;'><tr><td>" . lang("discount") . "</td><td>" . $this->sma->formatMoney($purchase->discount) . "</td></tr><tr><td>" . lang("shipping") . "</td><td>" . $this->sma->formatMoney($purchase->shipping) . "</td></tr><tr><td>" . lang("product_tax") . "</td><td>" . $this->sma->formatMoney($purchase->tax1) . "</td></tr><tr><td>" . lang("order_tax") . "</td><td>" . $this->sma->formatMoney($purchase->tax2) . "</td></tr><tr><td>" . lang("total") . "</td><td>" . $this->sma->formatMoney($purchase->total) . "</td></tr></table>";
            }
        } else {
            $daily_purchase = array();
        }

        $this->data['calender'] = $this->calendar->generate($year, $month, $daily_purchase);
        $this->data['year'] = $year;
        $this->data['month'] = $month;
        if ($pdf) {
            $html = $this->load->view($this->theme . 'posreports/daily', $this->data, true);
            $name = lang("daily_purchases") . "_" . $year . "_" . $month . ".pdf";
            $html = str_replace('<p class="introtext">' . lang("reports_calendar_text") . '</p>', '', $html);
            $this->sma->generate_pdf($html, $name, null, null, null, null, null, 'L');
        }
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['warehouse_id'] = $warehouse_id;
        $this->data['sel_warehouse'] = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : NULL;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('daily_purchases_report')));
        $meta = array('page_title' => lang('daily_purchases_report'), 'bc' => $bc);
        $this->page_construct('posreports/daily_purchases', $meta, $this->data);

    }

    function profit_get($date = NULL, $warehouse_id = NULL, $re = NULL)
    {
        if ( ! $this->Owner) {
            $this->session->set_flashdata('error', lang('access_denied'));
            $this->sma->md();
        }
        if ( ! $date) { $date = date('Y-m-d'); }
        $this->data['costing'] = $this->posreports_api->getCosting($date, $warehouse_id);
        $this->data['discount'] = $this->posreports_api->getOrderDiscount($date, $warehouse_id);
        $this->data['expenses'] = $this->posreports_api->getExpenses($date, $warehouse_id);
        $this->data['returns'] = $this->posreports_api->getReturns($date, $warehouse_id);
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['swh'] = $warehouse_id;
        $this->data['date'] = $date;
        if ($re) {
            echo $this->load->view($this->theme . 'posreports/profit', $this->data, TRUE);
            exit();
        }
        $this->load->view($this->theme . 'posreports/profit', $this->data);
    }
    function monthly_purchases_get($warehouse_id = NULL, $year = NULL, $pdf = NULL, $user_id = NULL)
    {
        //$this->sma->checkPermissions();
	$api_key = $this->input->get('api-key');$this->data['api_key'] = $api_key;
        if (!$this->Owner && !$this->Admin && $this->session->userdata('warehouse_id')) {
            $warehouse_id = $this->session->userdata('warehouse_id');
        }
        if (!$year) {
            $year = date('Y');
        }
        if (!$this->Owner && !$this->Admin && !$this->session->userdata('view_right')) {
            $user_id = $this->session->userdata('user_id');
        }
        $this->load->language('calendar');
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['year'] = $year;
        $this->data['purchases'] = $user_id ? $this->posreports_api->getStaffMonthlyPurchases($user_id, $year, $warehouse_id) : $this->posreports_api->getMonthlyPurchases($year, $warehouse_id);
        if ($pdf) {
            $html = $this->load->view($this->theme . 'posreports/monthly', $this->data, true);
            $name = lang("monthly_purchases") . "_" . $year . ".pdf";
            $html = str_replace('<p class="introtext">' . lang("reports_calendar_text") . '</p>', '', $html);
            $this->sma->generate_pdf($html, $name, null, null, null, null, null, 'L');
        }
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['warehouse_id'] = $warehouse_id;
        $this->data['sel_warehouse'] = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : NULL;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('monthly_purchases_report')));
        $meta = array('page_title' => lang('monthly_purchases_report'), 'bc' => $bc);
        $this->page_construct('posreports/monthly_purchases', $meta, $this->data);

    }
    function monthly_profit_get($year, $month, $warehouse_id = NULL, $re = NULL)
    {
        if ( ! $this->Owner) {
            $this->session->set_flashdata('error', lang('access_denied'));
            $this->sma->md();
        }

        $this->data['costing'] = $this->posreports_api->getCosting(NULL, $warehouse_id, $year, $month);
        $this->data['discount'] = $this->posreports_api->getOrderDiscount(NULL, $warehouse_id, $year, $month);
        $this->data['expenses'] = $this->posreports_api->getExpenses(NULL, $warehouse_id, $year, $month);
        $this->data['returns'] = $this->posreports_api->getReturns(NULL, $warehouse_id, $year, $month);
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['swh'] = $warehouse_id;
        $this->data['year'] = $year;
        $this->data['month'] = $month;
        $this->data['date'] = date('F Y', strtotime($year.'-'.$month.'-'.'01'));
        if ($re) {
            echo $this->load->view($this->theme . 'posreports/monthly_profit', $this->data, TRUE);
            exit();
        }
        $this->load->view($this->theme . 'posreports/monthly_profit', $this->data);
    }

    
    function purchases_get()
    {
        //$this->sma->checkPermissions('purchases');
	$api_key = $this->input->get('api-key');$this->data['api_key'] = $api_key;
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users'] = $this->posreports_api->getStaff();
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('purchases_report')));
        $meta = array('page_title' => lang('purchases_report'), 'bc' => $bc);
        $this->page_construct('posreports/purchases', $meta, $this->data);
    }

    function getPurchasesReport_post($pdf = NULL, $xls = NULL)
    {
        //$this->sma->checkPermissions('purchases', TRUE);

        $product = $this->input->get('product') ? $this->input->get('product') : NULL;
        $user = $this->input->get('user') ? $this->input->get('user') : NULL;
        $supplier = $this->input->get('supplier') ? $this->input->get('supplier') : NULL;
        $warehouse = $this->input->get('warehouse') ? $this->input->get('warehouse') : NULL;
        $reference_no = $this->input->get('reference_no') ? $this->input->get('reference_no') : NULL;
        $start_date = $this->input->get('start_date') ? $this->input->get('start_date') : NULL;
        $end_date = $this->input->get('end_date') ? $this->input->get('end_date') : NULL;

        if ($start_date) {
            $start_date = $this->sma->fld($start_date);
            $end_date = $this->sma->fld($end_date);
        }
        if (!$this->Owner && !$this->Admin && !$this->session->userdata('view_right')) {
            $user = $this->session->userdata('user_id');
        }

        if ($pdf || $xls) {

            $this->db
                ->select("" . $this->db->dbprefix('purchases') . ".date, reference_no, " . $this->db->dbprefix('warehouses') . ".name as wname, supplier, GROUP_CONCAT(CONCAT(" . $this->db->dbprefix('purchase_items') . ".product_name, ' (', " . $this->db->dbprefix('purchase_items') . ".quantity, ')') SEPARATOR '\n') as iname, grand_total, paid, " . $this->db->dbprefix('purchases') . ".status", FALSE)
                ->from('purchases')
                ->join('purchase_items', 'purchase_items.purchase_id=purchases.id', 'left')
                ->join('warehouses', 'warehouses.id=purchases.warehouse_id', 'left')
                ->group_by('purchases.id')
                ->order_by('purchases.date desc');

            if ($user) {
                $this->db->where('purchases.created_by', $user);
            }
            if ($product) {
                $this->db->where('purchase_items.product_id', $product);
            }
            if ($supplier) {
                $this->db->where('purchases.supplier_id', $supplier);
            }
            if ($warehouse) {
                $this->db->where('purchases.warehouse_id', $warehouse);
            }
            if ($reference_no) {
                $this->db->like('purchases.reference_no', $reference_no, 'both');
            }
            if ($start_date) {
                $this->db->where($this->db->dbprefix('purchases').'.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = NULL;
            }

            if (!empty($data)) {

                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle(lang('purchase_report'));
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('date'));
                $this->excel->getActiveSheet()->SetCellValue('B1', lang('reference_no'));
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('warehouse'));
                $this->excel->getActiveSheet()->SetCellValue('D1', lang('supplier'));
                $this->excel->getActiveSheet()->SetCellValue('E1', lang('product_qty'));
                $this->excel->getActiveSheet()->SetCellValue('F1', lang('grand_total'));
                $this->excel->getActiveSheet()->SetCellValue('G1', lang('paid'));
                $this->excel->getActiveSheet()->SetCellValue('H1', lang('balance'));
                $this->excel->getActiveSheet()->SetCellValue('I1', lang('status'));

                $row = 2;
                $total = 0;
                $paid = 0;
                $balance = 0;
                foreach ($data as $data_row) {
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $this->sma->hrld($data_row->date));
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->reference_no);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->wname);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->supplier);
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, $data_row->iname);
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, $data_row->grand_total);
                    $this->excel->getActiveSheet()->SetCellValue('G' . $row, $data_row->paid);
                    $this->excel->getActiveSheet()->SetCellValue('H' . $row, ($data_row->grand_total - $data_row->paid));
                    $this->excel->getActiveSheet()->SetCellValue('I' . $row, $data_row->status);
                    $total += $data_row->grand_total;
                    $paid += $data_row->paid;
                    $balance += ($data_row->grand_total - $data_row->paid);
                    $row++;
                }
                $this->excel->getActiveSheet()->getStyle("F" . $row . ":H" . $row)->getBorders()
                    ->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
                $this->excel->getActiveSheet()->SetCellValue('F' . $row, $total);
                $this->excel->getActiveSheet()->SetCellValue('G' . $row, $paid);
                $this->excel->getActiveSheet()->SetCellValue('H' . $row, $balance);

                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->excel->getActiveSheet()->getStyle('E2:E' . $row)->getAlignment()->setWrapText(true);
                $filename = 'purchase_report';
                $this->load->helper('excel');
                create_excel($this->excel, $filename);

            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER["HTTP_REFERER"]);

        } else {

            $pi = "( SELECT purchase_id, product_id, (GROUP_CONCAT(CONCAT({$this->db->dbprefix('purchase_items')}.product_name, '__', {$this->db->dbprefix('purchase_items')}.quantity) SEPARATOR '___')) as item_nane from {$this->db->dbprefix('purchase_items')} ";
            if ($product) {
                $pi .= " WHERE {$this->db->dbprefix('purchase_items')}.product_id = {$product} ";
            }
            $pi .= " GROUP BY {$this->db->dbprefix('purchase_items')}.purchase_id ) FPI";

            $this->load->library('datatables');
            $this->datatables
                ->select("'sno',DATE_FORMAT({$this->db->dbprefix('purchases')}.date, '%Y-%m-%d %T') as date, reference_no, {$this->db->dbprefix('warehouses')}.name as wname, supplier, (FPI.item_nane) as iname, grand_total, paid, (grand_total-paid) as balance, {$this->db->dbprefix('purchases')}.status, {$this->db->dbprefix('purchases')}.id as id", FALSE)
                ->from('purchases')
                ->join($pi, 'FPI.purchase_id=purchases.id', 'left')
                ->join('warehouses', 'warehouses.id=purchases.warehouse_id', 'left');
                // ->group_by('purchases.id');

            if ($user) {
                $this->datatables->where('purchases.created_by', $user);
            }
            if ($product) {
                $this->datatables->where('FPI.product_id', $product, FALSE);
            }
            if ($supplier) {
                $this->datatables->where('purchases.supplier_id', $supplier);
            }
            if ($warehouse) {
                $this->datatables->where('purchases.warehouse_id', $warehouse);
            }
            if ($reference_no) {
                $this->datatables->like('purchases.reference_no', $reference_no, 'both');
            }
            if ($start_date) {
                $this->datatables->where($this->db->dbprefix('purchases').'.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            echo $this->datatables->generate();

        }

    }
    function stock_audit_get()
    {
        //$this->sma->checkPermissions();
        $api_key = $this->input->get('api-key');$this->data['api_key'] = $api_key;
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users'] = $this->posreports_api->getStaff();        
        $this->data['Products'] = $this->posreports_api->getProducts();

        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['billers'] = $this->site->getAllCompanies('biller');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('stock_audit_rep')));
        $meta = array('page_title' => lang('stock_audit_rep'), 'bc' => $bc);
        
        $this->page_construct('posreports/stock_audit', $meta, $this->data);
    }   
    public function get_StockAuditreports_post($start = NULL, $product_id = NULL,$warehouse_id = NULL){
     //$this->sma->checkPermissions('stock_audit',TRUE);
        $start = $this->input->post('start_date');
        $product_id = $this->input->post('product_id');
        $warehouse_id = $this->input->post('warehouse_id');
        $limit = $this->input->post('pagelimit');        
        $offsetSegment = 5;
        $offset = $this->uri->segment($offsetSegment,0);
        /*echo "<pre>";
        print_r($this->input->post());die;*/
        $data= '';
        if ($start != '') {
            $data = $this->posreports_api->getStockVariance($start, $product_id, $warehouse_id,$limit,$offset);
            if (!empty($data['data'])){             
                 $stockaudit = $data['data'];
             }
             else{
                 $stockaudit = 'empty';
             }
        }
        else{
            $stockaudit = 'error';
        }
        $total = $data['total'];
        $pagination = $this->pagination('api/v1/posreports/get_StockAuditreports',$limit,$offsetSegment,$total);
        $this->sma->send_json(array('stock_audit' => $stockaudit,'pagination'=>$pagination));
   }
   
   function quantity_alerts_get($warehouse_id = NULL)
    {
        //$this->sma->checkPermissions();
        $api_key = $this->input->get('api-key');$this->data['api_key'] = $api_key;
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

        if ($this->Owner || $this->Admin || !$this->session->userdata('warehouse_id')) {
            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $this->data['warehouse_id'] = $warehouse_id;
            $this->data['warehouse'] = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : NULL;
        } else {
            $user = $this->site->getUser();
            $this->data['warehouses'] = NULL;
            $this->data['warehouse_id'] = $user->warehouse_id;
            $this->data['warehouse'] = $user->warehouse_id ? $this->site->getWarehouseByID($user->warehouse_id) : NULL;
        }

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('product_quantity_alerts')));
        $meta = array('page_title' => lang('product_quantity_alerts'), 'bc' => $bc);
        $this->page_construct('posreports/quantity_alerts', $meta, $this->data);
    }

    function getQuantityAlerts_post($warehouse_id = NULL, $pdf = NULL, $xls = NULL)
    {
        //$this->sma->checkPermissions('quantity_alerts', TRUE);
        if (!$this->Owner && !$warehouse_id) {
            $user = $this->site->getUser();
            $warehouse_id = $user->warehouse_id;
        }

        if ($pdf || $xls) {

            if ($warehouse_id) {
                $this->db
                    ->select('products.image as image, products.code, products.name, warehouses_products.quantity, alert_quantity')
                    ->from('products')->join('warehouses_products', 'warehouses_products.product_id=products.id', 'left')
                    ->where('alert_quantity > warehouses_products.quantity', NULL)
                    ->where('warehouse_id', $warehouse_id)
                    ->where('track_quantity', 1)
                    ->order_by('products.code desc');
            } else {
                $this->db
                    ->select('image, code, name, quantity, alert_quantity')
                    ->from('products')
                    ->where('alert_quantity > quantity', NULL)
                    ->where('track_quantity', 1)
                    ->order_by('code desc');
            }

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = NULL;
            }

            if (!empty($data)) {

                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle(lang('product_quantity_alerts'));
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('product_code'));
                $this->excel->getActiveSheet()->SetCellValue('B1', lang('product_name'));
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('quantity'));
                $this->excel->getActiveSheet()->SetCellValue('D1', lang('alert_quantity'));

                $row = 2;
                foreach ($data as $data_row) {
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $data_row->code);
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->name);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->quantity);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->alert_quantity);
                    $row++;
                }

                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(35);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(35);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $filename = 'product_quantity_alerts';
                $this->load->helper('excel');
                create_excel($this->excel, $filename);

            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER["HTTP_REFERER"]);

        } else {

            $this->load->library('datatables');
            if ($warehouse_id) {
                $this->datatables
                    ->select('"sno",image, code, name, wp.quantity, alert_quantity')
                    ->from('products')
                    ->join("( SELECT * from {$this->db->dbprefix('warehouses_products')} WHERE warehouse_id = {$warehouse_id}) wp", 'products.id=wp.product_id', 'left')
                    ->where('alert_quantity > wp.quantity', NULL)
                    ->or_where('wp.quantity', NULL)
                    ->where('track_quantity', 1)
                    ->group_by('products.id');
            } else {
                $this->datatables
                    ->select('"sno",image, code, name, quantity, alert_quantity')
                    ->from('products')
                    ->where('alert_quantity > quantity', NULL)
                    ->where('track_quantity', 1);
            }

            echo $this->datatables->generate();

        }

    }
    function expiry_alerts_get($warehouse_id = NULL)
    {
        //$this->sma->checkPermissions();
        $api_key = $this->input->get('api-key');$this->data['api_key'] = $api_key;
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

        if ($this->Owner || $this->Admin || !$this->session->userdata('warehouse_id')) {
            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $this->data['warehouse_id'] = $warehouse_id;
            $this->data['warehouse'] = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : NULL;
        } else {
            $user = $this->site->getUser();
            $this->data['warehouses'] = NULL;
            $this->data['warehouse_id'] = $user->warehouse_id;
            $this->data['warehouse'] = $user->warehouse_id ? $this->site->getWarehouseByID($user->warehouse_id) : NULL;
        }

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('product_expiry_alerts')));
        $meta = array('page_title' => lang('product_expiry_alerts'), 'bc' => $bc);
        $this->page_construct('posreports/expiry_alerts', $meta, $this->data);
    }

    function getExpiryAlerts_post($warehouse_id = NULL)
    {
        
	//$this->sma->checkPermissions('expiry_alerts', TRUE);
        $date = date('Y-m-d', strtotime('+3 months'));

        if (!$this->Owner && !$warehouse_id) {
            $user = $this->site->getUser();
            $warehouse_id = $user->warehouse_id;
        }

        $this->load->library('datatables');
        if ($warehouse_id) {
            $this->datatables
                ->select("'sno',image, product_code, product_name, quantity_balance, warehouses.name, expiry")
                ->from('purchase_items')
                ->join('products', 'products.id=purchase_items.product_id', 'left')
                ->join('warehouses', 'warehouses.id=purchase_items.warehouse_id', 'left')
                ->where('warehouse_id', $warehouse_id)
                ->where('expiry !=', NULL)->where('expiry !=', '0000-00-00')
                ->where('expiry <', $date);
        } else {
            $this->datatables
                ->select("'sno',image, product_code, product_name, quantity_balance, warehouses.name, expiry")
                ->from('purchase_items')
                ->join('products', 'products.id=purchase_items.product_id', 'left')
                ->join('warehouses', 'warehouses.id=purchase_items.warehouse_id', 'left')
                ->where('expiry !=', NULL)->where('expiry !=', '0000-00-00')
                ->where('expiry <', $date);
        }
        echo $this->datatables->generate();
    }
    function payments_get()
    {
        //$this->sma->checkPermissions('payments');
        $api_key = $this->input->get('api-key');$this->data['api_key'] = $api_key;
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users'] = $this->posreports_api->getStaff();
        $this->data['billers'] = $this->site->getAllCompanies('biller');
        $this->data['pos_settings'] = $this->posreports_api->getPOSSetting('biller');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('payments_report')));
        $meta = array('page_title' => lang('payments_report'), 'bc' => $bc);
        $this->page_construct('posreports/payments', $meta, $this->data);
    }

    function getPaymentsReport_post($pdf = NULL, $xls = NULL)
    {
        //$this->sma->checkPermissions('payments', TRUE);

        $user = $this->input->get('user') ? $this->input->get('user') : NULL;
        $supplier = $this->input->get('supplier') ? $this->input->get('supplier') : NULL;
        $customer = $this->input->get('customer') ? $this->input->get('customer') : NULL;
        $biller = $this->input->get('biller') ? $this->input->get('biller') : NULL;
        $payment_ref = $this->input->get('payment_ref') ? $this->input->get('payment_ref') : NULL;
        $paid_by = $this->input->get('paid_by') ? $this->input->get('paid_by') : NULL;
        $sale_ref = $this->input->get('sale_ref') ? $this->input->get('sale_ref') : NULL;
        $purchase_ref = $this->input->get('purchase_ref') ? $this->input->get('purchase_ref') : NULL;
        $card = $this->input->get('card') ? $this->input->get('card') : NULL;
        $cheque = $this->input->get('cheque') ? $this->input->get('cheque') : NULL;
        $transaction_id = $this->input->get('tid') ? $this->input->get('tid') : NULL;
        $start_date = $this->input->get('start_date') ? $this->input->get('start_date') : NULL;
        $end_date = $this->input->get('end_date') ? $this->input->get('end_date') : NULL;
        $defalut_currency = $this->Settings->default_currency;
        if ($start_date) {
            $start_date = $this->sma->fsd($start_date);
            $end_date = $this->sma->fsd($end_date);
        }
        if (!$this->Owner && !$this->Admin && !$this->session->userdata('view_right')) {
            $user = $this->session->userdata('user_id');
        }
        if ($pdf || $xls) {


 /*->select("DATE_FORMAT({$this->db->dbprefix('payments')}.date, '%Y-%m-%d %T') as date,  " . $this->db->dbprefix('bils') . ".reference_no as sale_ref, " . $this->db->dbprefix('payments') . ".paid_by as paid_by ,( COALESCE(sum(amount), 0) + COALESCE(sum(amount_usd*4000), 0) - COALESCE(sum(pos_balance), 0)) as amount, type, {$this->db->dbprefix('payments')}.id as id")
                ->from('bils')
                ->join('payments', 'payments.bill_id = bils.id')
               
                ->group_by('payments.id');*/

/*GROUP_CONCAT(CONCAT(" . $this->db->dbprefix('payments') . ".paid_by, ' (', " . $this->db->dbprefix('purchase_items') . ".quantity, ')') SEPARATOR '\n') as paid_by,*/


/*(GROUP_CONCAT(CONCAT({$this->db->dbprefix('payments')}.paid_by, ) SEPARATOR ',')) as paid_by*/

            $this->db
                //->select("DATE_FORMAT({$this->db->dbprefix('payments')}.date, '%Y-%m-%d %T') as date, " . $this->db->dbprefix('bils') . ".reference_no as sale_ref, GROUP_CONCAT(CONCAT({$this->db->dbprefix('payments')}.paid_by, ) SEPARATOR ',') as paid_by,( COALESCE(sum(amount), 0) + COALESCE(sum(amount_usd*4000), 0) - COALESCE(sum(pos_balance), 0)) as amount, type")
                ->select("DATE_FORMAT({$this->db->dbprefix('payments')}.date, '%Y-%m-%d %T') as date, " . $this->db->dbprefix('bils') . ".reference_no as sale_ref, CONCAT('cash - ',SUM(DISTINCT CASE  WHEN ((srampos_payments.paid_by = 'cash') AND (srampos_sale_currency.currency_id=2) AND (srampos_sale_currency.amount!='')) THEN srampos_payments.amount 
WHEN ((srampos_payments.paid_by = 'cash' AND srampos_sale_currency.currency_id=1 AND srampos_sale_currency.amount!='')) THEN (srampos_sale_currency.amount*srampos_sale_currency.currency_rate) ELSE 0 END),' | CC - ',SUM(DISTINCT CASE
WHEN " . $this->db->dbprefix('payments') . ".paid_by = 'CC'  THEN {$this->db->dbprefix('payments')}.amount

ELSE 0 END),' | credit - ',SUM(DISTINCT CASE
WHEN " . $this->db->dbprefix('payments') . ".paid_by = 'credit'  THEN {$this->db->dbprefix('payments')}.amount

ELSE 0 END)) paid_by,{$this->db->dbprefix('bils')}.paid  as amount,type")
                ->from('bils')
                ->join('payments', 'payments.bill_id = bils.id','left')
               ->join('sale_currency', 'sale_currency.bil_id = bils.id')
                ->group_by('payments.id')
                ->order_by('payments.date desc');

            if ($user) {
                $this->db->where('payments.created_by', $user);
            }
            if ($card) {
                $this->db->like('payments.cc_no', $card, 'both');
            }
            if ($cheque) {
                $this->db->where('payments.cheque_no', $cheque);
            }
            if ($transaction_id) {
                $this->db->where('payments.transaction_id', $transaction_id);
            }
            if ($customer) {
                $this->db->where('sales.customer_id', $customer);
            }
           /* if ($supplier) {
                $this->db->where('purchases.supplier_id', $supplier);
            }*/
            if ($biller) {
                $this->db->where('sales.biller_id', $biller);
            }
            if ($customer) {
                $this->db->where('sales.customer_id', $customer);
            }
            if ($payment_ref) {
                $this->db->like('payments.reference_no', $payment_ref, 'both');
            }
            if ($paid_by) {
                $this->db->where('payments.paid_by', $paid_by);
            }
            if ($sale_ref) {
                $this->db->like('sales.reference_no', $sale_ref, 'both');
            }
            /*if ($purchase_ref) {
                $this->db->like('purchases.reference_no', $purchase_ref, 'both');
            }*/
            if ($start_date) {
                $this->db->where($this->db->dbprefix('payments').'.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }
            if(!$this->Owner && !$this->Admin){
                $this->db->where('bils.table_whitelisted', 0); 
            }

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = NULL;
            }

            if (!empty($data)) {

                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle(lang('payments_report'));
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('date'));
               /* $this->excel->getActiveSheet()->SetCellValue('B1', lang('payment_reference'));*/
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('sale_reference'));
               /* $this->excel->getActiveSheet()->SetCellValue('D1', lang('purchase_reference'));*/
                $this->excel->getActiveSheet()->SetCellValue('E1', lang('paid_by'));
                $this->excel->getActiveSheet()->SetCellValue('F1', lang('amount'));
                $this->excel->getActiveSheet()->SetCellValue('G1', lang('type'));

                $row = 2;
                $total = 0;
                foreach ($data as $data_row) {
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $this->sma->hrld($data_row->date));
                   /* $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->payment_ref);*/
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->sale_ref);
                   /* $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->purchase_ref);*/
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, lang($data_row->paid_by));
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, $data_row->amount);
                    $this->excel->getActiveSheet()->SetCellValue('G' . $row, $data_row->type);
                    if ($data_row->type == 'returned' || $data_row->type == 'sent') {
                        $total -= $data_row->amount;
                    } else {
                        $total += $data_row->amount;
                    }
                    $row++;
                }
                $this->excel->getActiveSheet()->getStyle("F" . $row)->getBorders()
                    ->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
                $this->excel->getActiveSheet()->SetCellValue('F' . $row, $total);

                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $filename = 'payments_report';
                $this->load->helper('excel');
                create_excel($this->excel, $filename);

            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER["HTTP_REFERER"]);

        } else {

/*            GROUP_CONCAT(CONCAT(" . $this->db->dbprefix('payments') . ".paid_by, ' (', " . $this->db->dbprefix('payments') . ".quantity, ')') SEPARATOR '\n') as iname,*/

/*GROUP_CONCAT(CONCAT(" . $this->db->dbprefix('payments') . ".paid_by, ' (', " . $this->db->dbprefix('payments') . ".amount, ')') SEPARATOR '\n') as paid_by,*/


            $this->load->library('datatables');
            //$this->datatables
                //->select("DATE_FORMAT({$this->db->dbprefix('payments')}.date, '%Y-%m-%d %T') as date,  " . $this->db->dbprefix('bils') . ".reference_no as sale_ref, GROUP_CONCAT(CONCAT(" . $this->db->dbprefix('payments') . ".paid_by, ' (', " . $this->db->dbprefix('payments') . ".amount, ')') SEPARATOR '\n') as paid_by,( COALESCE(sum(amount), 0) + COALESCE(sum(amount_usd*4000), 0) - COALESCE(sum(pos_balance), 0)) as amount, type, {$this->db->dbprefix('payments')}.id as id")
                //->from('bils')
                //->join('payments', 'payments.bill_id = bils.id')
                //
                //->group_by('bils.id');
                
                //GROUP_CONCAT(CONCAT(" . $this->db->dbprefix('payments') . ".paid_by, ' (', " . $this->db->dbprefix('payments') . ".amount, ')') SEPARATOR '\n') as paid_by,
                
                //SUM(DISTINCT CASE WHEN ((" . $this->db->dbprefix('payments') . ".paid_by = 'cash') AND ({$this->db->dbprefix('sale_currency')}.currency_id != ".$defalut_currency.")) THEN amount_exchange*currency_rate ELSE {$this->db->dbprefix('bils')}.paid END) as For_Ex
                
               $this->datatables
                ->select("'sno',".$this->db->dbprefix('warehouses') . ".name as branch, DATE_FORMAT({$this->db->dbprefix('payments')}.date, '%Y-%m-%d %T') as date, bill_number, " . $this->db->dbprefix('bils') . ".reference_no as sale_ref, CONCAT('cash - ',SUM(DISTINCT CASE  WHEN ((srampos_payments.paid_by = 'cash') AND (srampos_sale_currency.currency_id=2) AND (srampos_sale_currency.amount!='')) THEN srampos_payments.amount 
WHEN ((srampos_payments.paid_by = 'cash' AND srampos_sale_currency.currency_id=1 AND srampos_sale_currency.amount!='')) THEN (srampos_sale_currency.amount*srampos_sale_currency.currency_rate) ELSE 0 END),' | CC - ',SUM(DISTINCT CASE
WHEN " . $this->db->dbprefix('payments') . ".paid_by = 'CC'  THEN {$this->db->dbprefix('payments')}.amount

ELSE 0 END),'| credit - ',SUM(DISTINCT CASE  WHEN ((srampos_payments.paid_by = 'credit') AND (srampos_sale_currency.currency_id=2) AND (srampos_sale_currency.amount!='')) THEN srampos_payments.amount 
WHEN ((srampos_payments.paid_by = 'credit' AND srampos_sale_currency.currency_id=1 AND srampos_sale_currency.amount!='')) THEN (srampos_sale_currency.amount*srampos_sale_currency.currency_rate) ELSE 0 END)) paid_by,{$this->db->dbprefix('bils')}.paid  as For_Ex,{$this->db->dbprefix('bils')}.balance,{$this->db->dbprefix('payments')}.type as type, {$this->db->dbprefix('payments')}.id as id,{$this->db->dbprefix('payments')}.bill_id")
                ->from('bils')
                ->join('payments', 'payments.bill_id = bils.id')
                ->join('sale_currency', 'sale_currency.bil_id = bils.id')
                ->join('warehouses', 'warehouses.id = bils.warehouse_id')
                //->order_by('bils.id','ASC')
                ->group_by('bils.id');


            if ($user) {
                $this->datatables->where('payments.created_by', $user);
            }
            if ($card) {
                $this->datatables->like('payments.cc_no', $card, 'both');
            }
            if ($cheque) {
                $this->datatables->where('payments.cheque_no', $cheque);
            }
            if ($transaction_id) {
                $this->datatables->where('payments.transaction_id', $transaction_id);
            }
            if ($customer) {
                $this->datatables->where('bils.customer_id', $customer);
            }
            if ($supplier) {
                $this->datatables->where('purchases.supplier_id', $supplier);
            }
            if ($biller) {
                $this->datatables->where('bils.biller_id', $biller);
            }
            if ($customer) {
                $this->datatables->where('bils.customer_id', $customer);
            }
           /* if ($payment_ref) {
                $this->datatables->like('payments.reference_no', $payment_ref, 'both');
            }*/
            if ($paid_by) {
                $this->datatables->where('payments.paid_by', $paid_by);
            }
            if ($sale_ref) {
                $this->datatables->like('bils.reference_no', $sale_ref, 'both');
            }
          /*  if ($purchase_ref) {
                $this->datatables->like('purchases.reference_no', $purchase_ref, 'both');
            }*/
            if ($start_date) {
                $this->datatables->where($this->db->dbprefix('payments').'.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }
            if(!$this->Owner && !$this->Admin){
                $this->db->where('bils.table_whitelisted', 0); 
            }
            //print_r($this->datatables);exit;
            /*echo "<pre>";
print_r($this->datatables->generate());die;  */
/*print_r($this->db->error());die;*/
            echo $this->datatables->generate();

        }

    }
    function profit_loss_get($start_date = NULL, $end_date = NULL)
    {
        //$this->sma->checkPermissions('profit_loss');
        $api_key = $this->input->get('api-key');$this->data['api_key'] = $api_key;
        if (!$start_date) {
            $start = $this->db->escape(date('Y-m') . '-1');
            $start_date = date('Y-m') . '-1';
        } else {
            $start = $this->db->escape(urldecode($start_date));
        }
        if (!$end_date) {
            $end = $this->db->escape(date('Y-m-d H:i'));
            $end_date = date('Y-m-d H:i');
        } else {
            $end = $this->db->escape(urldecode($end_date));
        }
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

        $this->data['total_purchases'] = $this->posreports_api->getTotalPurchases($start, $end);
        $this->data['total_sales'] = $this->posreports_api->getTotalSales($start, $end);
        $this->data['total_expenses'] = $this->posreports_api->getTotalExpenses($start, $end);
        $this->data['total_paid'] = $this->posreports_api->getTotalPaidAmount($start, $end);
        $this->data['total_received'] = $this->posreports_api->getTotalReceivedAmount($start, $end);
        $this->data['total_received_cash'] = $this->posreports_api->getTotalReceivedCashAmount($start, $end);
        $this->data['total_received_cc'] = $this->posreports_api->getTotalReceivedCCAmount($start, $end);
        $this->data['total_received_cheque'] = $this->posreports_api->getTotalReceivedChequeAmount($start, $end);
        $this->data['total_received_ppp'] = $this->posreports_api->getTotalReceivedPPPAmount($start, $end);
        $this->data['total_received_stripe'] = $this->posreports_api->getTotalReceivedStripeAmount($start, $end);
        $this->data['total_returned'] = $this->posreports_api->getTotalReturnedAmount($start, $end);
        $this->data['start'] = urldecode($start_date);
        $this->data['end'] = urldecode($end_date);

        $warehouses = $this->site->getAllWarehouses();
        foreach ($warehouses as $warehouse) {
            $total_purchases = $this->posreports_api->getTotalPurchases($start, $end, $warehouse->id);
            $total_sales = $this->posreports_api->getTotalSales($start, $end, $warehouse->id);
            $total_expenses = $this->posreports_api->getTotalExpenses($start, $end, $warehouse->id);
            $warehouses_report[] = array(
                'warehouse' => $warehouse,
                'total_purchases' => $total_purchases,
                'total_sales' => $total_sales,
                'total_expenses' => $total_expenses,
                );
        }
        $this->data['warehouses_report'] = $warehouses_report;

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('profit_loss')));
        $meta = array('page_title' => lang('profit_loss'), 'bc' => $bc);
        $this->page_construct('posreports/profit_loss', $meta, $this->data);
    }
    function customers_get()
    {
        //$this->sma->checkPermissions('customers');
        $api_key = $this->input->get('api-key');$this->data['api_key'] = $api_key;
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('customers_report')));
        $meta = array('page_title' => lang('customers_report'), 'bc' => $bc);
        $this->page_construct('posreports/customers', $meta, $this->data);
    }

    function getCustomers_post($pdf = NULL, $xls = NULL)
    {
        //$this->sma->checkPermissions('customers', TRUE);

        if ($pdf || $xls) {

            $this->db
                ->select($this->db->dbprefix('companies') . ".id as id, company, name, phone, email, count(" . $this->db->dbprefix('bils') . ".id) as total, COALESCE(sum(grand_total), 0) as total_amount, COALESCE(sum(paid), 0) as paid, ( COALESCE(sum(grand_total), 0) - COALESCE(sum(paid), 0)) as balance", FALSE)
                ->from("companies")
                ->join('bils', 'bils.customer_id=companies.id')
                ->where('companies.group_name', 'customer')
                ->where('bils.payment_status', 'Completed')
                ->order_by('companies.company asc')
                ->group_by('companies.id');

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = NULL;
            }

            if (!empty($data)) {

                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle(lang('customers_report'));
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('company'));
                $this->excel->getActiveSheet()->SetCellValue('B1', lang('name'));
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('phone'));
                $this->excel->getActiveSheet()->SetCellValue('D1', lang('email'));
                $this->excel->getActiveSheet()->SetCellValue('E1', lang('total_sales'));
                $this->excel->getActiveSheet()->SetCellValue('F1', lang('total_amount'));
                $this->excel->getActiveSheet()->SetCellValue('G1', lang('paid'));
                $this->excel->getActiveSheet()->SetCellValue('H1', lang('balance'));

                $row = 2;
                foreach ($data as $data_row) {
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $data_row->company);
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->name);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->phone);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->email);
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, $data_row->total);
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, $this->sma->formatMoney($data_row->total_amount));
                    $this->excel->getActiveSheet()->SetCellValue('G' . $row, $this->sma->formatMoney($data_row->paid));
                    $this->excel->getActiveSheet()->SetCellValue('H' . $row, $this->sma->formatMoney($data_row->balance));
                    $row++;
                }

                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $filename = 'customers_report';
                $this->load->helper('excel');
                create_excel($this->excel, $filename);

            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER["HTTP_REFERER"]);

        } else {

            $s = "( SELECT customer_id,payment_status, count(" . $this->db->dbprefix('bils') . ".id) as total, COALESCE(sum(grand_total), 0) as total_amount, COALESCE(sum(paid), 0) as paid, ( COALESCE(sum(grand_total), 0) - COALESCE(sum(paid), 0)) as balance from {$this->db->dbprefix('bils')} GROUP BY {$this->db->dbprefix('bils')}.customer_id ) FS";

            $this->load->library('datatables');
            $this->datatables
                ->select($this->db->dbprefix('companies') . ".id as id,'sno', company, name, phone, email, FS.total, FS.total_amount, FS.paid, FS.balance", FALSE)
                ->from("companies")
                ->join($s, 'FS.customer_id=companies.id')
                ->where('companies.group_name', 'customer')
                ->where('FS.payment_status', 'Completed')
                ->group_by('companies.id')
                ->add_column("Actions", "<div class='text-center'><a class=\"tip\" title='" . lang("view_report") . "' href='" . site_url('api/v1/posreports/customer_report/$1?api-key='.$_POST['api-key']) . "'><span class='label label-primary'>" . lang("view_report") . "</span></a></div>", "id")
                ->unset_column('id');
            echo $this->datatables->generate();

        }

    }
    function customer_report_get($user_id = NULL)
    {
        //$this->sma->checkPermissions('customers', TRUE);
	$api_key = $this->input->get('api-key');$this->data['api_key'] = $api_key;
        if (!$user_id) {
            $this->session->set_flashdata('error', lang("no_customer_selected"));
            redirect('api/v1/posreports/customers?api-key='.$_GET['api-key']);
        }

        $this->data['sales'] = $this->posreports_api->getSalesTotals($user_id);
        $this->data['total_sales'] = $this->posreports_api->getCustomerSales($user_id);
        $this->data['total_quotes'] = $this->posreports_api->getCustomerQuotes($user_id);
        $this->data['total_returns'] = $this->posreports_api->getCustomerReturns($user_id);
        $this->data['users'] = $this->posreports_api->getStaff();
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['billers'] = $this->site->getAllCompanies('biller');

        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');

        $this->data['user_id'] = $user_id;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('customers_report')));
        $meta = array('page_title' => lang('customers_report'), 'bc' => $bc);
        $this->page_construct('posreports/customer_report', $meta, $this->data);

    }
     function getQuotesReport_post($pdf = NULL, $xls = NULL)
    {

        if ($this->input->get('product')) {
            $product = $this->input->get('product');
        } else {
            $product = NULL;
        }
        if ($this->input->get('user')) {
            $user = $this->input->get('user');
        } else {
            $user = NULL;
        }
        if ($this->input->get('customer')) {
            $customer = $this->input->get('customer');
        } else {
            $customer = NULL;
        }
        if ($this->input->get('biller')) {
            $biller = $this->input->get('biller');
        } else {
            $biller = NULL;
        }
        if ($this->input->get('warehouse')) {
            $warehouse = $this->input->get('warehouse');
        } else {
            $warehouse = NULL;
        }
        if ($this->input->get('reference_no')) {
            $reference_no = $this->input->get('reference_no');
        } else {
            $reference_no = NULL;
        }
        if ($this->input->get('start_date')) {
            $start_date = $this->input->get('start_date');
        } else {
            $start_date = NULL;
        }
        if ($this->input->get('end_date')) {
            $end_date = $this->input->get('end_date');
        } else {
            $end_date = NULL;
        }
        if ($start_date) {
            $start_date = $this->sma->fld($start_date);
            $end_date = $this->sma->fld($end_date);
        }
        if ($pdf || $xls) {

            $this->db
                ->select("date, reference_no, biller, customer, GROUP_CONCAT(CONCAT(" . $this->db->dbprefix('quote_items') . ".product_name, ' (', " . $this->db->dbprefix('quote_items') . ".quantity, ')') SEPARATOR '<br>') as iname, grand_total, status", FALSE)
                ->from('quotes')
                ->join('quote_items', 'quote_items.quote_id=quotes.id', 'left')
                ->join('warehouses', 'warehouses.id=quotes.warehouse_id', 'left')
                ->group_by('quotes.id');

            if ($user) {
                $this->db->where('quotes.created_by', $user);
            }
            if ($product) {
                $this->db->where('quote_items.product_id', $product);
            }
            if ($biller) {
                $this->db->where('quotes.biller_id', $biller);
            }
            if ($customer) {
                $this->db->where('quotes.customer_id', $customer);
            }
            if ($warehouse) {
                $this->db->where('quotes.warehouse_id', $warehouse);
            }
            if ($reference_no) {
                $this->db->like('quotes.reference_no', $reference_no, 'both');
            }
            if ($start_date) {
                $this->db->where($this->db->dbprefix('quotes').'.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = NULL;
            }

            if (!empty($data)) {

                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle(lang('quotes_report'));
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('date'));
                $this->excel->getActiveSheet()->SetCellValue('B1', lang('reference_no'));
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('biller'));
                $this->excel->getActiveSheet()->SetCellValue('D1', lang('customer'));
                $this->excel->getActiveSheet()->SetCellValue('E1', lang('product_qty'));
                $this->excel->getActiveSheet()->SetCellValue('F1', lang('grand_total'));
                $this->excel->getActiveSheet()->SetCellValue('G1', lang('status'));

                $row = 2;
                foreach ($data as $data_row) {
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $this->sma->hrld($data_row->date));
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->reference_no);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->biller);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->customer);
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, $data_row->iname);
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, $data_row->grand_total);
                    $this->excel->getActiveSheet()->SetCellValue('G' . $row, $data_row->status);
                    $row++;
                }

                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->excel->getActiveSheet()->getStyle('E2:E' . $row)->getAlignment()->setWrapText(true);
                $filename = 'quotes_report';
                $this->load->helper('excel');
                create_excel($this->excel, $filename);

            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER["HTTP_REFERER"]);

        } else {

            $qi = "( SELECT quote_id, product_id, GROUP_CONCAT(CONCAT({$this->db->dbprefix('quote_items')}.product_name, '__', {$this->db->dbprefix('quote_items')}.quantity) SEPARATOR '___') as item_nane from {$this->db->dbprefix('quote_items')} ";
            if ($product) {
                $qi .= " WHERE {$this->db->dbprefix('quote_items')}.product_id = {$product} ";
            }
            $qi .= " GROUP BY {$this->db->dbprefix('quote_items')}.quote_id ) FQI";
            $this->load->library('datatables');
            $this->datatables
                ->select("'sno',date, reference_no, biller, customer, FQI.item_nane as iname, grand_total, status, {$this->db->dbprefix('quotes')}.id as id", FALSE)
                ->from('quotes')
                ->join($qi, 'FQI.quote_id=quotes.id', 'left')
                ->join('warehouses', 'warehouses.id=quotes.warehouse_id', 'left')
                ->group_by('quotes.id');

            if ($user) {
                $this->datatables->where('quotes.created_by', $user);
            }
            if ($product) {
                $this->datatables->where('FQI.product_id', $product, FALSE);
            }
            if ($biller) {
                $this->datatables->where('quotes.biller_id', $biller);
            }
            if ($customer) {
                $this->datatables->where('quotes.customer_id', $customer);
            }
            if ($warehouse) {
                $this->datatables->where('quotes.warehouse_id', $warehouse);
            }
            if ($reference_no) {
                $this->datatables->like('quotes.reference_no', $reference_no, 'both');
            }
            if ($start_date) {
                $this->datatables->where($this->db->dbprefix('quotes').'.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            echo $this->datatables->generate();

        }

    }

    
    function get_deposits_post($company_id = NULL)
    {
        //$this->sma->checkPermissions('customers', TRUE);
        $this->load->library('datatables');
        $this->datatables
            ->select("'sno',date, credit_amount, paid_by, CONCAT({$this->db->dbprefix('users')}.first_name, ' ', {$this->db->dbprefix('users')}.last_name) as created_by, note", false)
            ->from("deposits")
            ->join('users', 'users.id=deposits.created_by', 'left')
            ->where($this->db->dbprefix('deposits').'.company_id', $company_id);
            
        echo $this->datatables->generate();
    }
    function suppliers_get()
    {
        //$this->sma->checkPermissions('suppliers');
        $api_key = $this->input->get('api-key');$this->data['api_key'] = $api_key;
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('suppliers_report')));
        $meta = array('page_title' => lang('suppliers_report'), 'bc' => $bc);
        $this->page_construct('posreports/suppliers', $meta, $this->data);
    }

    function getSuppliers_post($pdf = NULL, $xls = NULL)
    {
        //$this->sma->checkPermissions('suppliers', TRUE);

        if ($pdf || $xls) {

            $this->db
                ->select($this->db->dbprefix('companies') . ".id as id, company, name, phone, email, count({$this->db->dbprefix('purchases')}.id) as total, COALESCE(sum(grand_total), 0) as total_amount, COALESCE(sum(paid), 0) as paid, ( COALESCE(sum(grand_total), 0) - COALESCE(sum(paid), 0)) as balance", FALSE)
                ->from("companies")
                ->join('purchases', 'purchases.supplier_id=companies.id')
                ->where('companies.group_name', 'supplier')
                ->order_by('companies.company asc')
                ->group_by('companies.id');

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = NULL;
            }

            if (!empty($data)) {

                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle(lang('suppliers_report'));
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('company'));
                $this->excel->getActiveSheet()->SetCellValue('B1', lang('name'));
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('phone'));
                $this->excel->getActiveSheet()->SetCellValue('D1', lang('email'));
                $this->excel->getActiveSheet()->SetCellValue('E1', lang('total_purchases'));
                $this->excel->getActiveSheet()->SetCellValue('F1', lang('total_amount'));
                $this->excel->getActiveSheet()->SetCellValue('G1', lang('paid'));
                $this->excel->getActiveSheet()->SetCellValue('H1', lang('balance'));

                $row = 2;
                foreach ($data as $data_row) {
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $data_row->company);
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->name);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->phone);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->email);
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, $data_row->total);
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, $data_row->total_amount);
                    $this->excel->getActiveSheet()->SetCellValue('G' . $row, $data_row->paid);
                    $this->excel->getActiveSheet()->SetCellValue('H' . $row, $data_row->balance);
                    $row++;
                }

                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $filename = 'suppliers_report';
                $this->load->helper('excel');
                create_excel($this->excel, $filename);

            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER["HTTP_REFERER"]);

        } else {

            $p = "( SELECT supplier_id, count(" . $this->db->dbprefix('purchases') . ".id) as total, COALESCE(sum(grand_total), 0) as total_amount, COALESCE(sum(paid), 0) as paid, ( COALESCE(sum(grand_total), 0) - COALESCE(sum(paid), 0)) as balance from {$this->db->dbprefix('purchases')} GROUP BY {$this->db->dbprefix('purchases')}.supplier_id ) FP";

            $this->load->library('datatables');
            $this->datatables
                ->select($this->db->dbprefix('companies') . ".id as id, 'sno',company, name, phone, email, FP.total, FP.total_amount, FP.paid, FP.balance", FALSE)
                ->from("companies")
                ->join($p, 'FP.supplier_id=companies.id')
                ->where('companies.group_name', 'supplier')
                ->group_by('companies.id')
                ->add_column("Actions", "<div class='text-center'><a class=\"tip\" title='" . lang("view_report") . "' href='" . admin_url('reports/supplier_report/$1') . "'><span class='label label-primary'>" . lang("view_report") . "</span></a></div>", "id")
                ->unset_column('id');
            echo $this->datatables->generate();

        }

    }
    function users_get()
    {
        //$this->sma->checkPermissions();
        $api_key = $this->input->get('api-key');
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('staff_report')));
        $meta = array('page_title' => lang('staff_report'), 'bc' => $bc);
        $this->data['api_key'] = $api_key;
        $this->page_construct('posreports/users', $meta, $this->data);
    }

    function getUsers_post()
    {
        //$this->sma->checkPermissions('users',TRUE);
        $this->load->library('datatables');
        $this->datatables
            ->select("'sno',".$this->db->dbprefix('warehouses').".name as branch,".$this->db->dbprefix('users').".id as id, first_name, last_name, ".$this->db->dbprefix('users').".email, company, ".$this->db->dbprefix('groups').".name, active")
            ->from("users")
            ->join('groups', 'users.group_id=groups.id', 'left')
            ->join('warehouses', 'users.warehouse_id=warehouses.id', 'left')
            ->group_by('users.id')
            ->where('company_id', NULL);
        if (!$this->Owner) {
            $this->datatables->where('group_id !=', 1);
        }
        $this->datatables
            ->edit_column('active', '$1__$2', 'active, id')
            ->add_column("Actions", "<div class='text-center'><a class=\"tip\" title='" . lang("view_report") . "' href='" . site_url('api/v1/posreports/staff_report/$1?api-key='.$_POST['api-key']) . "'><span class='label label-primary'>" . lang("view_report") . "</span></a></div>", "id")
            ->unset_column('id');
        echo $this->datatables->generate();
    }
    function staff_report_get($user_id = NULL, $year = NULL, $month = NULL, $pdf = NULL, $cal = 0)
    {

        if (!$user_id) {
            $this->session->set_flashdata('error', lang("no_user_selected"));
            redirect('api/v1/posreports/users?api-key='.$_GET['api-key']);
        }
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $this->data['purchases'] = $this->posreports_api->getStaffPurchases($user_id);
        $this->data['sales'] = $this->posreports_api->getStaffSales($user_id);        
        $this->data['billers'] = $this->site->getAllCompanies('biller');
        $this->data['warehouses'] = $this->site->getAllWarehouses();

        if (!$year) {
            $year = date('Y');
        }
        if (!$month || $month == '#monthly-con') {
            $month = date('m');
        }
        if ($pdf) {
            if ($cal) {
                $this->monthly_sales($year, $pdf, $user_id);
            } else {
                $this->daily_sales($year, $month, $pdf, $user_id);
            }
        }
        $config = array(
            'show_next_prev' => TRUE,
            'next_prev_url' => site_url('api/v1/posreports/staff_report/'.$user_id.'?api-key='.$_GET['api-key']),
            'month_type' => 'long',
            'day_type' => 'long'
        );

        $config['template'] = '{table_open}<div class="table-responsive"><table border="0" cellpadding="0" cellspacing="0" class="table table-bordered dfTable reports-table">{/table_open}
        {heading_row_start}<tr>{/heading_row_start}
        {heading_previous_cell}<th class="text-center"><a href="{previous_url}">&lt;&lt;</a></th>{/heading_previous_cell}
        {heading_title_cell}<th class="text-center" colspan="{colspan}" id="month_year">{heading}</th>{/heading_title_cell}
        {heading_next_cell}<th class="text-center"><a href="{next_url}">&gt;&gt;</a></th>{/heading_next_cell}
        {heading_row_end}</tr>{/heading_row_end}
        {week_row_start}<tr>{/week_row_start}
        {week_day_cell}<td class="cl_wday">{week_day}</td>{/week_day_cell}
        {week_row_end}</tr>{/week_row_end}
        {cal_row_start}<tr class="days">{/cal_row_start}
        {cal_cell_start}<td class="day">{/cal_cell_start}
        {cal_cell_content}
        <div class="day_num">{day}</div>
        <div class="content">{content}</div>
        {/cal_cell_content}
        {cal_cell_content_today}
        <div class="day_num highlight">{day}</div>
        <div class="content">{content}</div>
        {/cal_cell_content_today}
        {cal_cell_no_content}<div class="day_num">{day}</div>{/cal_cell_no_content}
        {cal_cell_no_content_today}<div class="day_num highlight">{day}</div>{/cal_cell_no_content_today}
        {cal_cell_blank}&nbsp;{/cal_cell_blank}
        {cal_cell_end}</td>{/cal_cell_end}
        {cal_row_end}</tr>{/cal_row_end}
        {table_close}</table></div>{/table_close}';

        $this->load->library('calendar', $config);
        $sales = $this->posreports_api->getStaffDailySales($user_id, $year, $month);

        if (!empty($sales)) {
            foreach ($sales as $sale) {
                $daily_sale[$sale->date] = "<table class='table table-bordered table-hover table-striped table-condensed data' style='margin:0;'><tr><td>" . lang("total") . "</td><td>" . $this->sma->formatMoney($sale->total) . "</td></tr><tr><td>" . lang("discount") . "</td><td>" . $this->sma->formatMoney($sale->discount) . "</td></tr><tr><td>" . lang("tax") . "</td><td>" . $this->sma->formatMoney($sale->tax) . "</td></tr><tr><td>" . lang("total") . "</td><td>" . $this->sma->formatMoney($sale->total-$sale->discount+$sale->tax) . "</td></tr></table>";
            }
        } else {
            $daily_sale = array();
        }
        $this->data['calender'] = $this->calendar->generate($year, $month, $daily_sale);
        if ($this->input->get('pdf')) {

        }
		$printlist = $this->input->get('printlist') ? $this->input->get('printlist') : 0;    
		
        $this->data['year'] = $year;
        $this->data['month'] = $month;
        $this->data['msales'] = $this->posreports_api->getStaffMonthlySales($user_id, $year,$printlist);
        $this->data['user_id'] = $user_id;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('staff_report')));
        $meta = array('page_title' => lang('staff_report'), 'bc' => $bc);
        $this->page_construct('posreports/staff_report', $meta, $this->data);

    }
    function getUserLogins_post($id = NULL, $pdf = NULL, $xls = NULL)
    {
        if ($this->input->get('start_date')) {
            $login_start_date = $this->input->get('start_date');
        } else {
            $login_start_date = NULL;
        }
        if ($this->input->get('end_date')) {
            $login_end_date = $this->input->get('end_date');
        } else {
            $login_end_date = NULL;
        }
        if ($login_start_date) {
            $login_start_date = $this->sma->fld($login_start_date);
            $login_end_date = $login_end_date ? $this->sma->fld($login_end_date) : date('Y-m-d H:i:s');
        }
        if ($pdf || $xls) {

            $this->db
                ->select("login, ip_address, time")
                ->from("user_logins")
                ->where('user_id', $id)
                ->order_by('time desc');
            if ($login_start_date) {
                $this->db->where("time BETWEEN '{$login_start_date}' and '{$login_end_date}'", NULL, FALSE);
            }

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = NULL;
            }

            if (!empty($data)) {

                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle(lang('staff_login_report'));
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('email'));
                $this->excel->getActiveSheet()->SetCellValue('B1', lang('ip_address'));
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('time'));

                $row = 2;
                foreach ($data as $data_row) {
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $data_row->login);
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->ip_address);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $this->sma->hrld($data_row->time));
                    $row++;
                }

                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(35);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(35);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(35);
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->excel->getActiveSheet()->getStyle('C2:C' . $row)->getAlignment()->setWrapText(true);
                $filename = 'staff_login_report';
                $this->load->helper('excel');
                create_excel($this->excel, $filename);

            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER["HTTP_REFERER"]);

        } else {

            $this->load->library('datatables');
            $this->datatables
                ->select("login, ip_address, DATE_FORMAT(time, '%Y-%m-%d %T') as time")
                ->from("user_logins")
                ->where('user_id', $id);
            if ($login_start_date) {
                $this->datatables->where("time BETWEEN '{$login_start_date}' and '{$login_end_date}'", NULL, FALSE);
            }
            echo $this->datatables->generate();

        }

    }
    function feedback_get()
    {
        //$this->sma->checkPermissions('feedback_details');
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $api_key = $this->input->get('api-key');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('feedback_details')), array('link' => '#', 'page' => lang('feedback_details')));
        $meta = array('page_title' => lang('feedback_details'), 'bc' => $bc);
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['api_key'] = $api_key;
        $this->page_construct('posreports/feedback', $meta, $this->data);
    }



   public function get_feedback_post(){
        //$this->sma->checkPermissions('recipe',TRUE);
        $start = $this->input->post('start');
        $end = $this->input->post('end');
        $warehouse_id = $this->input->post('warehouse_id');
        $limit = $this->input->post('pagelimit');    
        $offsetSegment = 5;
        $offset = $this->uri->segment($offsetSegment,0);
        $data= '';
        $total =0;
        if ($start != '' && $end != '') {
            $data = $this->posreports_api->getFeedBackReports($start,$end,$warehouse_id,$limit,$offset);
            $total = @$data['total'];
             if ($data != false) {             
                 $data = $data['data'];
             }
             else{                
                $data = 'empty';
             }            
        }
        else{
            $data = 'error';
        }
        //$total = $data['total'];
        $pagination = $this->pagination('api/v1/posreports/get_feedback',$limit,$offsetSegment,$total);
        $this->sma->send_json(array('feedback' => $data,'pagination'=>$pagination));

        // $this->sma->send_json(array('feedback' => $data));
   }
   function warehouse_stock_get($warehouse = NULL)
    {
        //$this->sma->checkPermissions();
	$api_key = $this->input->get('api-key');
        $data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        if ($this->input->get('warehouse')) {
            $warehouse = $this->input->get('warehouse');
        }
        $this->data['stock'] = $warehouse ? $this->posreports_api->getWarehouseStockValue($warehouse) : $this->posreports_api->getStockValue();
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['warehouse_id'] = $warehouse;
        $this->data['warehouse'] = $warehouse ? $this->site->getWarehouseByID($warehouse) : NULL;
        $this->data['totals'] = $this->posreports_api->getWarehouseTotals($warehouse);
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('reports')));
        $meta = array('page_title' => lang('reports'), 'bc' => $bc);
	$this->data['api_key'] = $api_key;
        $this->page_construct('posreports/warehouse_stock', $meta, $this->data);

    }
    public function best_sellers_get($warehouse_id = NULL)
    {
        //$this->sma->checkPermissions();

        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $y1 = date('Y', strtotime('-1 month'));
        $m1 = date('m', strtotime('-1 month'));  
        
        $m1sdate = $y1.'-'.$m1.'-01 00:00:00';
        $m1edate = $y1.'-'.$m1.'-'. days_in_month($m1, $y1) . ' 23:59:59';
        $this->data['m1'] = date('M Y', strtotime($y1.'-'.$m1));
        $this->data['m1bs'] = $this->posreports_api->getBestSeller($m1sdate, $m1edate, $warehouse_id);        


        $y2 = date('Y', strtotime('-2 months'));
        $m2 = date('m', strtotime('-2 months'));
        $m2sdate = $y2.'-'.$m2.'-01 00:00:00';
        $m2edate = $y2.'-'.$m2.'-'. days_in_month($m2, $y2) . ' 23:59:59';
        $this->data['m2'] = date('M Y', strtotime($y2.'-'.$m2));
        $this->data['m2bs'] = $this->posreports_api->getBestSeller($m2sdate, $m2edate, $warehouse_id);      

        $y3 = date('Y', strtotime('-3 months'));
        $m3 = date('m', strtotime('-3 months'));
        $m3sdate = $y3.'-'.$m3.'-01 23:59:59';
        $this->data['m3'] = date('M Y', strtotime($y3.'-'.$m3)).' - '.$this->data['m1'];
        $this->data['m3bs'] = $this->posreports_api->getBestSeller($m3sdate, $m1edate, $warehouse_id);        

        $y4 = date('Y', strtotime('-12 months'));
        $m4 = date('m', strtotime('-12 months'));
        $m4sdate = $y4.'-'.$m4.'-01 23:59:59';
        $this->data['m4'] = date('M Y', strtotime($y4.'-'.$m4)).' - '.$this->data['m1'];
        $this->data['m4bs'] = $this->posreports_api->getBestSeller($m4sdate, $m1edate, $warehouse_id);
        // $this->sma->print_arrays($this->data['m1bs'], $this->data['m2bs'], $this->data['m3bs'], $this->data['m4bs']);
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['warehouse'] = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : NULL;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('best_sellers')));
        $meta = array('page_title' => lang('best_sellers'), 'bc' => $bc);
	$api_key = $this->input->get('api-key');
	$this->data['api_key'] = $api_key;
        $this->page_construct('posreports/best_sellers', $meta, $this->data);

    }
	function overview_chart_get()
    {
	
        //$this->sma->checkPermissions();
        $data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['monthly_sales'] = $this->posreports_api->getChartData();

        $this->data['stock'] = $this->posreports_api->getStockValue();
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('reports')));
        $meta = array('page_title' => lang('reports'), 'bc' => $bc);
        $this->page_construct('posreports/index', $meta, $this->data);

    }
    
    function page_construct($page, $meta = array(), $data = array()) {
     $this->load->model('site');
        $meta['message'] = isset($data['message']) ? $data['message'] : $this->session->flashdata('message');
        $meta['error'] = isset($data['error']) ? $data['error'] : $this->session->flashdata('error');
        $meta['warning'] = isset($data['warning']) ? $data['warning'] : $this->session->flashdata('warning');
       
        $meta['Settings'] = $this->site->get_setting();
	$meta['Settings']->user_rtl = $meta['Settings']->rtl;
            
        $meta['assets'] = base_url() . 'themes/default/admin/assets/';
	$meta['isMobileApp'] = true;
	$meta['ip_address'] = $this->input->ip_address();
	
        $meta['dateFormats'] = $this->dateFormats;
	$data['Settings'] = $meta['Settings'];
        $this->theme = $meta['Settings']->theme.'/admin/views/';
        $this->load->view($this->theme . 'app_header', $meta);
        $this->load->view($this->theme . $page, $data);
        $this->load->view($this->theme . 'app_footer');
    }
    function pagination($url,$per,$segment,$total){
        $config['base_url'] = site_url($url);
        $config['per_page'] = $per;
        $config['uri_segment'] = $segment;
        $config['total_rows'] = $total;
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['prev_link'] = 'Previous';
        $config['next_link'] = 'Next';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
       //$config['num_links'] = 3;
        $config['first_link']  = FALSE;
        $config['last_link']   = FALSE;
        $limit = $config['per_page'];
        $offset = $this->uri->segment($config['uri_segment'],0);
        $offset = ($offset>1)?(($offset-1) * $limit):0;
        
        $this->pagination->initialize($config);
        return $this->pagination->create_links();
   }
   
   ////////////////////// summary report ////////////////////
   public function cover_analysis_summary_get(){
	$start = date('Y-m-01');
        $end   = date('Y-m-d');
        $warehouse_id = $this->input->post('warehouse_id');

       
	$data = $this->posreports_api->getCoverAnalysis_summary($start,$end,$warehouse_id);
	if (!empty($data['data'])){
	     
	     $result = $data['data'];
	 }
	 else{
	    
	    $result = 'empty';
	 }
      
        $this->sma->send_json(array('summary' => $result));
   }
   public function tax_summary_get(){
        $start = date('Y-m-01');
        $end   =date('Y-m-d');
        $warehouse_id = $this->input->post('warehouse_id');
		$printlist = $this->input->post('printlist') ? $this->input->post('printlist') : 0;    
       
	$data = $this->posreports_api->getTaxReport_summary($start,$end,$warehouse_id,$printlist);
	if (!empty($data['data'])){
	     
	     $result = $data['data'];
	 }
	 else{
	    
	    $result = 'empty';
	 }
      
        $this->sma->send_json(array('summary' => $result));
   }
   public function voidbills_summary_get(){
        $start = date('Y-m-01');        
        $end   = date('Y-m-d');
        $warehouse_id = $this->input->post('warehouse_id');
		$printlist = $this->input->post('printlist') ? $this->input->post('printlist') : 0;    
       
	$data = $this->posreports_api->getVoidBillsReport_summary($start,$end,$warehouse_id,$printlist);
	if (!empty($data['data'])){
	     
	     $result = $data['data'];
	 }
	 else{
	    
	    $result = 'empty';
	 }
      
        $this->sma->send_json(array('summary' => $result));
   }
   public function discounts_summary_get(){
        $start = date('Y-m-01');
        $end   = date('Y-m-d');
        $warehouse_id = $this->input->post('warehouse_id');
		$printlist = $this->input->post('printlist') ? $this->input->post('printlist') : 0;    
       
	$data = $this->posreports_api->getDiscountsummaryReport_summary($start,$end,$warehouse_id,$printlist);
	if (!empty($data['data'])){
	     
	     $result = $data['data'];
	 }
	 else{
	    
	    $result = 'empty';
	 }
      
        $this->sma->send_json(array('summary' => $result));
   }
   public function bbq_summary_get(){
        $start = date('Y-m-01');
        $end   = date('Y-m-d');
        $warehouse_id = $this->input->post('warehouse_id');
		$printlist = $this->input->post('printlist') ? $this->input->post('printlist') : 0;    
       
	$data = $this->posreports_api->getBBQDetailsReport_summary($start,$end,$warehouse_id,$printlist);
	if (!empty($data['data'])){
	     
	     $result = $data['data'];
	 }
	 else{
	    
	    $result = 'empty';
	 }
      
        $this->sma->send_json(array('summary' => $result));
   }
   public function pos_settlement_summary_get(){
        $start = date('Y-m-01');
        $end   = date('Y-m-d');
        $warehouse_id = $this->input->post('warehouse_id');
		$printlist = $this->input->post('printlist') ? $this->input->post('printlist') : 0;    
       
	$data = $this->posreports_api->getPosSettlementReport_summary($start,$end,$warehouse_id,$printlist);
	if (!empty($data['data'])){
	     
	     $result = $data['data'];
	 }
	 else{
	    
	    $result = 'empty';
	 }
      
        $this->sma->send_json(array('summary' => $result));
   }
   public function daywise_summary_get(){
        $today= date('Y-m-d');
        $warehouse_id = $this->input->post('warehouse_id');
	$day = strtolower(date('l'));
       
	$data = $this->posreports_api->getDaysummaryReport_summary($today,$warehouse_id);
	if (!empty($data['data'])){
	     
	     $result = $data['data'];
	 }
	 else{
	    
	    $result = 'empty';
	 }
      
        $this->sma->send_json($result);
   }
   public function monthly_reports_summary_get(){
        $curmonth= date('Y-m');
        $warehouse_id = $this->input->post('warehouse_id');
	$day = strtolower(date('l'));
       $printlist = $this->input->post('printlist') ? $this->input->post('printlist') : 0;    
	$data = $this->posreports_api->getMonthlyReport_summary($curmonth,$warehouse_id,$printlist);
	if (!empty($data['data'])){
	     
	     $result = $data['data'];
	 }
	 else{
	    
	    $result = 'empty';
	 }
        $this->sma->send_json($result);
   }
   public function days_reports_summary_get(){
        $start = date('Y-m-01');
        $end   = date('Y-m-d');
        $warehouse_id = $this->input->post('warehouse_id');
	$day = strtolower(date('l'));
       
	$data = $this->posreports_api->getDaysreport_summary($start,$end,$warehouse_id,$day);
	if (!empty($data['data'])){
	     
	     $result = $data['data'];
	 }
	 else{
	    
	    $result = 'empty';
	 }
      
        $this->sma->send_json(array('summary' => $result));
   }
   
   
   ////////////////////////// procurment reports ///////////////////////////
   function pro_store_request_get()
    {
        //$this->sma->checkPermissions('feedback_details');
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $api_key = $this->input->get('api-key');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('pro_store_request_details')), array('link' => '#', 'page' => lang('pro_store_request_details')));
        $meta = array('page_title' => lang('pro_store_request_details'), 'bc' => $bc);
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['api_key'] = $api_key;
        $this->page_construct('posreports/pro_store_request', $meta, $this->data);
    }



   public function get_pro_store_request_post(){
        //$this->sma->checkPermissions('recipe',TRUE);
        $start = $this->input->post('start');
        $end = $this->input->post('end');
        $warehouse_id = $this->input->post('warehouse_id');
        $limit =2;// $this->input->post('pagelimit');    
        $offsetSegment = 5;
        $offset = $this->uri->segment($offsetSegment,0);
        $data= '';
        $total =0;
        if ($start != '' && $end != '') {
            $data = $this->posreports_api->getStoreRequest_Report($start,$end,$warehouse_id,$limit,$offset);
            $total = @$data['total'];
             if ($data != false) {             
                 $data = $data['data'];
             }
             else{                
                $data = 'empty';
             }            
        }
        else{
            $data = 'error';
        }
        //$total = $data['total'];
        $pagination = $this->pagination('api/v1/posreports/get_pro_store_request',$limit,$offsetSegment,$total);
        $this->sma->send_json(array('report' => $data,'pagination'=>$pagination));

   }
   
   function pro_quotes_request_get()
    {
        //$this->sma->checkPermissions('feedback_details');
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $api_key = $this->input->get('api-key');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('pro_quotes_request_details')), array('link' => '#', 'page' => lang('pro_store_request_details')));
        $meta = array('page_title' => lang('pro_quotes_request_details'), 'bc' => $bc);
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['api_key'] = $api_key;
        $this->page_construct('posreports/pro_quotes_request', $meta, $this->data);
    }



   public function get_pro_quotes_request_post(){
        //$this->sma->checkPermissions('recipe',TRUE);
        $start = $this->input->post('start');
        $end = $this->input->post('end');
        $warehouse_id = $this->input->post('warehouse_id');
        $limit =2;// $this->input->post('pagelimit');    
        $offsetSegment = 5;
        $offset = $this->uri->segment($offsetSegment,0);
        $data= '';
        $total =0;
        if ($start != '' && $end != '') {
            $data = $this->posreports_api->getQuotesRequest_Report($start,$end,$warehouse_id,$limit,$offset);
            $total = @$data['total'];
             if ($data != false) {             
                 $data = $data['data'];
             }
             else{                
                $data = 'empty';
             }            
        }
        else{
            $data = 'error';
        }
        //$total = $data['total'];
        $pagination = $this->pagination('api/v1/posreports/get_pro_quotes_request',$limit,$offsetSegment,$total);
        $this->sma->send_json(array('report' => $data,'pagination'=>$pagination));

   }
   function pro_quotation_get()
    {
        //$this->sma->checkPermissions('feedback_details');
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $api_key = $this->input->get('api-key');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('pro_quotations')), array('link' => '#', 'page' => lang('pro_store_request_details')));
        $meta = array('page_title' => lang('pro_quotations'), 'bc' => $bc);
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['api_key'] = $api_key;
        $this->page_construct('posreports/pro_quotation', $meta, $this->data);
    }



   public function get_pro_quotation_post(){
        //$this->sma->checkPermissions('recipe',TRUE);
        $start = $this->input->post('start');
        $end = $this->input->post('end');
        $warehouse_id = $this->input->post('warehouse_id');
        $limit = $this->input->post('pagelimit');    
        $offsetSegment = 5;
        $offset = $this->uri->segment($offsetSegment,0);
        $data= '';
        $total =0;
        if ($start != '' && $end != '') {
            $data = $this->posreports_api->getQuotation_Report($start,$end,$warehouse_id,$limit,$offset);
            $total = @$data['total'];
             if ($data != false) {             
                 $data = $data['data'];
             }
             else{                
                $data = 'empty';
             }            
        }
        else{
            $data = 'error';
        }
        //$total = $data['total'];
        $pagination = $this->pagination('api/v1/posreports/get_pro_quotation',$limit,$offsetSegment,$total);
        $this->sma->send_json(array('report' => $data,'pagination'=>$pagination));

   }
   function pro_purchase_order_get()
    {
        //$this->sma->checkPermissions('feedback_details');
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $api_key = $this->input->get('api-key');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('pro_purchase_order')), array('link' => '#', 'page' => lang('pro_purchase_order_details')));
        $meta = array('page_title' => lang('pro_purchase_order'), 'bc' => $bc);
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['api_key'] = $api_key;
        $this->page_construct('posreports/pro_purchase_order', $meta, $this->data);
    }



   public function get_pro_purchase_order_post(){
        //$this->sma->checkPermissions('recipe',TRUE);
        $start = $this->input->post('start');
        $end = $this->input->post('end');
        $warehouse_id = $this->input->post('warehouse_id');
        $limit = $this->input->post('pagelimit');    
        $offsetSegment = 5;
        $offset = $this->uri->segment($offsetSegment,0);
        $data= '';
        $total =0;
	$this->session->set_userdata('start_date', $this->input->post('start'));
        $this->session->set_userdata('end_date', $this->input->post('end'));
        if ($start != '' && $end != '') {
            $data = $this->posreports_api->getPurchaseOrder_Report($start,$end,$warehouse_id,$limit,$offset);
            $total = @$data['total'];
             if ($data != false) {             
                 $data = $data['data'];
             }
             else{                
                $data = 'empty';
             }            
        }
        else{
            $data = 'error';
        }
        //$total = $data['total'];
        $pagination = $this->pagination('api/v1/posreports/get_pro_purchase_order',$limit,$offsetSegment,$total);
        $this->sma->send_json(array('report' => $data,'pagination'=>$pagination));

   }
   function pro_purchase_invoice_get()
    {
        //$this->sma->checkPermissions('feedback_details');
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $api_key = $this->input->get('api-key');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('pro_purchase_invoice')), array('link' => '#', 'page' => lang('pro_purchase_invoice_details')));
        $meta = array('page_title' => lang('pro_purchase_invoice'), 'bc' => $bc);
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['api_key'] = $api_key;
        $this->page_construct('posreports/pro_purchase_invoice', $meta, $this->data);
    }



   public function get_pro_purchase_invoice_post(){
        //$this->sma->checkPermissions('recipe',TRUE);
        $start = $this->input->post('start');
        $end = $this->input->post('end');
        $warehouse_id = $this->input->post('warehouse_id');
        $limit = $this->input->post('pagelimit');    
        $offsetSegment = 5;
        $offset = $this->uri->segment($offsetSegment,0);
        $data= '';
        $total =0;
	$this->session->set_userdata('start_date', $this->input->post('start'));
        $this->session->set_userdata('end_date', $this->input->post('end'));
        if ($start != '' && $end != '') {
            $data = $this->posreports_api->getPurchaseInvoice_Report($start,$end,$warehouse_id,$limit,$offset);
            $total = @$data['total'];
             if ($data != false) {             
                 $data = $data['data'];
             }
             else{                
                $data = 'empty';
             }            
        }
        else{
            $data = 'error';
        }
        //$total = $data['total'];
        $pagination = $this->pagination('api/v1/posreports/get_pro_invoice_order',$limit,$offsetSegment,$total);
        $this->sma->send_json(array('report' => $data,'pagination'=>$pagination));

   }
   function pro_purchase_order_summary_get()
    {
        //$this->sma->checkPermissions('feedback_details');
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $api_key = $this->input->get('api-key');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('pro_purchase_order_summary')), array('link' => '#', 'page' => lang('pro_purchase_order_summary')));
        $meta = array('page_title' => lang('pro_purchase_order_summary'), 'bc' => $bc);
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['api_key'] = $api_key;
        $this->page_construct('posreports/pro_purchase_order_summary', $meta, $this->data);
    }



   public function get_pro_purchase_order_summary_post(){
        //$this->sma->checkPermissions('recipe',TRUE);
        $start = $this->input->post('start');
        $end = $this->input->post('end');
        $warehouse_id = $this->input->post('warehouse_id');
        $limit = $this->input->post('pagelimit');    
        $offsetSegment = 5;
        $offset = $this->uri->segment($offsetSegment,0);
        $data= '';
        $total =0;
        if ($start != '' && $end != '') {
            $data = $this->posreports_api->getPurchaseOrderSummary_Report($start,$end,$warehouse_id,$limit,$offset);
            $total = @$data['total'];
             if ($data != false) {             
                 $data = $data['data'];
             }
             else{                
                $data = 'empty';
             }            
        }
        else{
            $data = 'error';
        }
        //$total = $data['total'];
        $pagination = $this->pagination('api/v1/posreports/get_pro_purchase_order_summary',$limit,$offsetSegment,$total);
        $this->sma->send_json(array('report' => $data,'pagination'=>$pagination));

   }
   function pro_purchase_invoice_summary_get()
    {
        //$this->sma->checkPermissions('feedback_details');
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $api_key = $this->input->get('api-key');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('pro_purchase_invoie_summary')), array('link' => '#', 'page' => lang('pro_purchase_invoie_summary')));
        $meta = array('page_title' => lang('pro_purchase_invoie_summary'), 'bc' => $bc);
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['api_key'] = $api_key;
        $this->page_construct('posreports/pro_purchase_invoice_summary', $meta, $this->data);
    }



   public function get_pro_purchase_invoice_summary_post(){
        //$this->sma->checkPermissions('recipe',TRUE);
        $start = $this->input->post('start');
        $end = $this->input->post('end');
        $warehouse_id = $this->input->post('warehouse_id');
        $limit = $this->input->post('pagelimit');    
        $offsetSegment = 5;
        $offset = $this->uri->segment($offsetSegment,0);
        $data= '';
        $total =0;
        if ($start != '' && $end != '') {
            $data = $this->posreports_api->getPurchaseInvoiceSummary_Report($start,$end,$warehouse_id,$limit,$offset);
            $total = @$data['total'];
             if ($data != false) {             
                 $data = $data['data'];
             }
             else{                
                $data = 'empty';
             }            
        }
        else{
            $data = 'error';
        }
        //$total = $data['total'];
        $pagination = $this->pagination('api/v1/posreports/get_pro_purchase_invoice_summary',$limit,$offsetSegment,$total);
        $this->sma->send_json(array('report' => $data,'pagination'=>$pagination));

   }
   
   function shifttime_reports_get()
    {
	//$this->sma->checkPermissions();
        $api_key = $this->input->get('api-key');
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users'] = $this->posreports_api->getStaff();        
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['billers'] = $this->site->getAllCompanies('biller');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('shift_time_report')));
        $meta = array('page_title' => lang('shift_time_report'), 'bc' => $bc);
        $this->data['api_key'] = $api_key;
        $this->page_construct('posreports/shift_time_report', $meta, $this->data);
    }  

 public function get_shifttime_reports_post($start= NULL,$end= NULL,$warehouse_id= NULL){
   
        $end = $this->input->post('end');
        $shift = $this->input->post('shift') ? $this->input->post('shift') : 0;
        $defalut_currency = $this->input->post('defalut_currency');
        $warehouse_id = $this->input->post('warehouse_id');
        $limit = $this->input->post('pagelimit');  
		$printlist = $this->input->post('printlist') ? $this->input->post('printlist') : 0;       
        $offsetSegment = 5;
        $offset = $this->uri->segment($offsetSegment,0);
        $data= '';
         $this->session->set_userdata('start_date', $this->input->post('start'));
        $this->session->set_userdata('end_date', $this->input->post('end'));
        if ($start != '') {
            $data = $this->posreports_api->getShiftReport($start,$end,$user,$limit,$offset,$shift,$defalut_currency,$printlist);
         
        if (!empty($data['data'])) {
                 
                 $Reports = $data['data'];
             }
             else{
                
                $Reports = 'empty';
             }
        }
        else{
            $Reports = 'error';
        }
   // echo $MonthlyReports;
        $total = $data['total'];
        $pagination = $this->pagination('api/v1/posreports/get_shifttime_reports',$limit,$offsetSegment,$total);
        $this->sma->send_json(array('reports' => $Reports,'pagination'=>$pagination));
        /*$this->sma->send_json(array('monthly_reports' => $month));*/
   }
    
}
