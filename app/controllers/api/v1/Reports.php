<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;



class Reports extends REST_Controller {
	function __construct() {
		parent::__construct();
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		$this->load->api_model('reports_api');
		$this->load->api_model('login_api');
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
				$login_response = $this->reports_api->login($username,$password);
				/*echo "string";die;*/
				/*var_dump($login_response);die;*/
				if($login_response == TRUE){
					/*$data = $this->customer_api->GetuserByID($phone_number);*/
					$user = $this->reports_api->getUser($login_response);
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
			$data = $this->reports_api->checkDevices($api_key);
			if(!empty($data->devices_key)){
				//if($data->devices_key == $devices_key){	
				if($data->devices_key == $devices_key && $data->api_type == $api_type){
					$result = array( 'status'=> true , 'message'=> 'Devices Key does is matched!');	
				}else{
					$result = array( 'status'=> false , 'message'=> 'Devices Key does not matche. please check your devices key');	
				}
			}else{
				$this->reports_api->updateDevices($api_key, $devices_key, $devices_type, $api_type);
				$result = array( 'status'=> true , 'message'=> 'New Devices key has been Insert');	
			}
		}else{
			$result = array( 'status'=> false , 'message'=> 'Please Enter All Details');	
		}
		$this->response($result);
		
	}		
	public function apitype_post(){
		$api_key = $this->input->get('api-key');
		$data = $this->reports_api->GetAllapitype();
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
public function settlementreports_post(){
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		
		$start_date = $this->input->post('start_date');
		$end_date = $this->input->post('end_date');
		$where = array();
	$where['table_whitelisted'] = ($this->input->post('table_whitelisted'))?$this->input->post('table_whitelisted'):0;
		/*$page = $this->input->post('page');*/

		
		/*$this->form_validation->set_rules('start_date', $this->lang->line("start_date"), 'required');
		$this->form_validation->set_rules('end_date', $this->lang->line("end_date"), 'required');*/

		/*if ($this->form_validation->run() == true) {*/
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){
				$data = $this->reports_api->PosSettlementReport($start_date,$end_date,$warehouse_id,$where);
				if($data == TRUE){
					$result = array( 'status'=> true , 'message'=> 'Sucess', 'data' => $data);
				}else{
					$result = array( 'status'=> false , 'message'=> 'Data is empty');
				}
			}else{
				$result = array( 'status'=> false , 'message'=> 'Devices Key does not matche. please check your devices key');	
			}
		/*}else{
			$result = array( 'status'=> false , 'message'=> 'Please Enter All Fields');
		}*/
		$this->response($result);
	}
public function kotdetailsreports_post(){
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		
		$start = $this->input->post('start_date');
		$end = $this->input->post('end_date');
		$kot = $this->input->post('kot');
		$page = $this->input->post('page');
		$table_whitelisted = ($this->input->post('table_whitelisted'))?$this->input->post('table_whitelisted'):0;
		/*$where['table_whitelisted'] = ($this->input->post('table_whitelisted'))?$this->input->post('table_whitelisted'):0;*/
		
		/*$this->form_validation->set_rules('start_date', $this->lang->line("start_date"), 'required');
		$this->form_validation->set_rules('end_date', $this->lang->line("end_date"), 'required');*/

		$this->form_validation->set_rules('kot', $this->lang->line("kot"), 'required');

		if ($this->form_validation->run() == true) {
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){

				if($kot == 'kot_cancel') 
             {                
                $data = $this->reports_api->getKotCancelReport($start,$end,$warehouse_id,$page,$table_whitelisted);
             }
             elseif ($kot == 'kot_pending')
             {
              $data = $this->reports_api->getKotPendingReport($start,$end,$warehouse_id,$page,$table_whitelisted);  
             }
             else{                
                $data = $this->reports_api->getKotDetailsReport($start,$end,$warehouse_id,$page,$table_whitelisted);
             }

				if($data == TRUE){
					$result = array( 'status'=> true , 'message'=> 'Sucess', 'data' => $data);
				}
				else{
					$result = array( 'status'=> false , 'message'=> 'Data is empty');
				}
			}else{
				$result = array( 'status'=> false , 'message'=> 'Devices Key does not matche. please check your devices key');	
			}
		}else{
			$result = array( 'status'=> false , 'message'=> 'Please Enter All Fields');
		}
		$this->response($result);
	}	
public function user_reports_post(){
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		
		$start = $this->input->post('start_date');
		$end = $this->input->post('end_date');
		$user = $this->input->post('user');
		$page = $this->input->post('page');
		$where = array();
		$where['table_whitelisted'] = ($this->input->post('table_whitelisted'))?$this->input->post('table_whitelisted'):0;
		
		$this->form_validation->set_rules('start_date', $this->lang->line("start_date"), 'required');
		$this->form_validation->set_rules('end_date', $this->lang->line("end_date"), 'required');
		$this->form_validation->set_rules('user', $this->lang->line("user"), 'required');

		if ($this->form_validation->run() == true) {
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){
              
                $data = $this->reports_api->getCashierReport($start,$end,$user,$page,$where);

				if($data == TRUE){
					$result = array( 'status'=> true , 'message'=> 'Sucess', 'data' => $data);
				}
				else{
					$result = array( 'status'=> false , 'message'=> 'Data is empty');
				}
			}else{
				$result = array( 'status'=> false , 'message'=> 'Devices Key does not matche. please check your devices key');	
			}
		}else{
			$result = array( 'status'=> false , 'message'=> 'Please Enter All Fields');
		}
		$this->response($result);
	}
	public function bill_no_post($start = NULL, $end = NULL, $warehouse_id=NULL){    
        $start = $this->input->post('start');
        $end = $this->input->post('end');
        $warehouse_id = $this->input->post('warehouse_id');

        $data= '';
        if ($start != '' && $end != '') {
            $data = $this->reports_api->getBill_no($start,$end,$warehouse_id);
            if ($data != false) {
            	$result = array( 'status'=> true , 'message'=> 'Sucess', 'data' => $data);

                 // $bill_no = $data;
             }
             else{
             	$result = array( 'status'=> false , 'message'=> 'Data is empty');
               /* $bill_no = 'empty';*/
             }
        }
        else{
        	$result = array( 'status'=> false , 'message'=> 'Please Enter All Fields');

            /*$bill_no = 'error';*/
        }
        $this->response($result);
        // $this->sma->send_json(array('bill_no' => $bill_no));
   }
public function bill_details_post(){
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		
		$start = $this->input->post('start');
        $end = $this->input->post('end');
        $bill_no = $this->input->post('bill_no');
        $page = $this->input->post('page');
	$where = array();
	$where['table_whitelisted'] = ($this->input->post('table_whitelisted'))?$this->input->post('table_whitelisted'):0;	
		$this->form_validation->set_rules('start', $this->lang->line("start"), 'required');
		$this->form_validation->set_rules('end', $this->lang->line("end"), 'required');
		

		if ($this->form_validation->run() == true) {
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){
              
                $data = $this->reports_api->getBillDetailsReport($start,$end,$bill_no,$warehouse_id,$page,$where);

				if($data == TRUE){
					$result = array( 'status'=> true , 'message'=> 'Sucess', 'data' => $data);
				}
				else{
					$result = array( 'status'=> false , 'message'=> 'Data is empty');
				}
			}else{
				$result = array( 'status'=> false , 'message'=> 'Devices Key does not matche. please check your devices key');	
			}
		}else{
			$result = array( 'status'=> false , 'message'=> 'Please Enter All Fields');
		}
		$this->response($result);
	}
public function cover_analysis_post(){

		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		
		$start = $this->input->post('start');
        $end = $this->input->post('end');
        $page = $this->input->post('page');
        		
	/*	$this->form_validation->set_rules('start', $this->lang->line("start"), 'required');
		$this->form_validation->set_rules('end', $this->lang->line("end"), 'required');*/

		/*if ($this->form_validation->run() == true) {*/
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){
              
                $data = $this->reports_api->getCoverAnalysisReport($start,$end,$warehouse_id,$page);

				if($data == TRUE){
					$result = array( 'status'=> true , 'message'=> 'Sucess', 'data' => $data);
				}
				else{
					$result = array( 'status'=> false , 'message'=> 'Data is empty');
				}
			}else{
				$result = array( 'status'=> false , 'message'=> 'Devices Key does not matche. please check your devices key');	
			}
	/*	}else{
			$result = array( 'status'=> false , 'message'=> 'Please Enter All Fields');
		}*/
		$this->response($result);
	}	
public function tax_reports_post(){

		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		
		$start = $this->input->post('start');
        $end = $this->input->post('end');
        $page = $this->input->post('page');
        		$where['table_whitelisted'] = ($this->input->post('table_whitelisted'))?$this->input->post('table_whitelisted'):0;
	/*	$this->form_validation->set_rules('start', $this->lang->line("start"), 'required');
		$this->form_validation->set_rules('end', $this->lang->line("end"), 'required');*/

		/*if ($this->form_validation->run() == true) {*/
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){              
                $data = $this->reports_api->getTaxReport($start,$end,$warehouse_id,$page,$where);
				if($data == TRUE){
				   $result = array( 'status'=> true , 'message'=> 'Sucess', 'data' => $data);
				}
				else{
					$result = array( 'status'=> false , 'message'=> 'Data is empty');
				}
			}else{
				$result = array( 'status'=> false , 'message'=> 'Devices Key does not matche. please check your devices key');	
			}
		/*}else{
			$result = array( 'status'=> false , 'message'=> 'Please Enter All Fields');
		}*/
		$this->response($result);
	}	
public function discount_reports_post(){

		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		
		$start = $this->input->post('start');
        $end = $this->input->post('end');
        $dis_type = $this->input->post('dis_type');
        $page = $this->input->post('page');
        		
/*		$this->form_validation->set_rules('start', $this->lang->line("start"), 'required');
		$this->form_validation->set_rules('end', $this->lang->line("end"), 'required');*/

		$this->form_validation->set_rules('dis_type', $this->lang->line("dis_type"), 'required');

		if ($this->form_validation->run() == true) {
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){
              
             if($dis_type == 'dis_details') 
             {                
                $data = $this->reports_api->getDiscountDetailsReport($start, $end, $warehouse_id,$page);
             }
             else
             {
                $data = $this->reports_api->getDiscountsummaryReport($start, $end, $warehouse_id,$page);
             }
				if($data == TRUE){
					$result = array( 'status'=> true , 'message'=> 'Sucess', 'data' => $data);
				}
				else{
					$result = array( 'status'=> false , 'message'=> 'Data is empty');
				}
			}else{
				$result = array( 'status'=> false , 'message'=> 'Devices Key does not matche. please check your devices key');	
			}
		}else{
			$result = array( 'status'=> false , 'message'=> 'Please Enter All Fields');
		}
		$this->response($result);
	}
public function item_reports_post(){ //recipe reports

		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		
		$start = $this->input->post('start');
        $end = $this->input->post('end');
        $page = $this->input->post('page');
$where['table_whitelisted'] = ($this->input->post('table_whitelisted'))?$this->input->post('table_whitelisted'):0;
		
		/*$this->form_validation->set_rules('start', $this->lang->line("start"), 'required');
		$this->form_validation->set_rules('end', $this->lang->line("end"), 'required');*/

		/*if ($this->form_validation->run() == true) {*/
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){
              
              $data = $this->reports_api->getItemSaleReports($start, $end, $warehouse_id,$page,$where);
             
				if($data == TRUE){
					$result = array( 'status'=> true , 'message'=> 'Sucess', 'data' => $data);
				}
				else{
					$result = array( 'status'=> false , 'message'=> 'Data is empty');
				}
			}else
			{
				$result = array( 'status'=> false , 'message'=> 'Devices Key does not matche. please check your devices key');	
			}
		/*}else{
			$result = array( 'status'=> false , 'message'=> 'Please Enter All Fields');
		}*/
		$this->response($result);
	}
public function popular_and_nonpopular_analysis_post(){ //popular and non popular analysis reports

		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		
		$start = $this->input->post('start');
        $end = $this->input->post('end');
        $type = $this->input->post('type');
        $page = $this->input->post('page');
	$where=array();
        $where['table_whitelisted'] = ($this->input->post('table_whitelisted'))?$this->input->post('table_whitelisted'):0;	
		/*$this->form_validation->set_rules('start', $this->lang->line("start"), 'required');
		$this->form_validation->set_rules('end', $this->lang->line("end"), 'required');*/
		$this->form_validation->set_rules('type', $this->lang->line("type"), 'required');

		if ($this->form_validation->run() == true) {
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){
              
              if($type == 'popular') 
             {                
                $data = $this->reports_api->getPopularReports($start,$end,$warehouse_id,$page,$where);
             }
             else
             {
              $data = $this->reports_api->getNonPopularReports($start,$end,$warehouse_id,$page,$where); 
             }
             
				if($data == TRUE){
					$result = array( 'status'=> true , 'message'=> 'Sucess', 'data' => $data);
				}
				else{
					$result = array( 'status'=> false , 'message'=> 'Data is empty');
				}
			}else
			{
				$result = array( 'status'=> false , 'message'=> 'Devices Key does not matche. please check your devices key');	
			}
		}else{
			$result = array( 'status'=> false , 'message'=> 'Please Enter All Fields');
		}
		$this->response($result);
	}
	public function payments_post(){
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		
		
		$where = array();
		$where['start_date'] = $this->input->post('start_date');
		$where['end_date'] = $this->input->post('end_date');
		$where['user'] = $this->input->post('user');
		$where['card'] = $this->input->post('card');
		$where['cheque'] = $this->input->post('cheque');
		$where['supplier'] = $this->input->post('supplier');
		$where['customer'] = $this->input->post('customer');
		$where['transaction_id'] = $this->input->post('tid');
		$where['paid_by'] = $this->input->post('paid_by');
		$where['biller'] = $this->input->post('biller');
		$where['sale_ref'] = $this->input->post('sale_ref');
		$page = $this->input->post('page');
		$where['table_whitelisted'] = ($this->input->post('table_whitelisted'))?$this->input->post('table_whitelisted'):0;
		
		/*$this->form_validation->set_rules('start_date', $this->lang->line("start_date"), 'required');
		$this->form_validation->set_rules('end_date', $this->lang->line("end_date"), 'required');*/

		/*if ($this->form_validation->run() == true) {*/
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){
				$data = $this->reports_api->payments_report($warehouse_id,$where,$page);
				if($data == TRUE){
					$result = array( 'status'=> true , 'message'=> 'Success', 'data' => $data);
				}else{
					$result = array( 'status'=> false , 'message'=> 'Data is empty');
				}
			}else{
				$result = array( 'status'=> false , 'message'=> 'Devices Key does not matche. please check your devices key');	
			}
		/*}else{
			$result = array( 'status'=> false , 'message'=> 'Please Enter All Fields');
		}*/
		$this->response($result);
	}
	
	public function sales_post(){
		
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		
		
		$where = array();
		$where['product'] = $this->input->post('product');
		$where['user'] = $this->input->post('user');
		$where['customer'] = $this->input->post('customer');
		$where['biller'] = $this->input->post('biller');
		$where['reference_no'] = $this->input->post('reference_no');
		$where['start_date'] = $this->input->post('start_date');
		$where['end_date'] = $this->input->post('end_date');
		$where['serial'] = $this->input->post('serial');
		$page = $this->input->post('page');
		$where['table_whitelisted'] = ($this->input->post('table_whitelisted'))?$this->input->post('table_whitelisted'):0;
		/*$this->form_validation->set_rules('start_date', $this->lang->line("start_date"), 'required');*/
		//$this->form_validation->set_rules('end_date', $this->lang->line("end_date"), 'required');

		/*if ($this->form_validation->run() == true) {*/
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){
				$data = $this->reports_api->sales_report($warehouse_id,$where,$page);
				if($data == TRUE){
					$result = array( 'status'=> true , 'message'=> 'Success', 'data' => $data);
				}else{
					$result = array( 'status'=> false , 'message'=> 'Data is empty');
				}
			}else{
				$result = array( 'status'=> false , 'message'=> 'Devices Key does not matche. please check your devices key');	
			}
		/*}else{
			$result = array( 'status'=> false , 'message'=> 'Please Enter All Fields');
		}*/
		$this->response($result);
	}
	/*public function recipes_post(){
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		$page = $this->input->post('page');
		
		$where = array();
		$where['product'] = $this->input->post('product');
		$where['start_date'] = $this->input->post('start_date');
		$where['end_date'] = $this->input->post('end_date');
		$where['table_whitelisted'] = ($this->input->post('table_whitelisted'))?$this->input->post('table_whitelisted'):0;
		$this->form_validation->set_rules('start_date', $this->lang->line("start_date"), 'required');
		//$this->form_validation->set_rules('end_date', $this->lang->line("end_date"), 'required');

		if ($this->form_validation->run() == true) {
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){
				$data = $this->reports_api->recipes_report($warehouse_id,$where,$page);
				if($data == TRUE){
					$result = array( 'status'=> true , 'message'=> 'Success', 'data' => $data);
				}else{
					$result = array( 'status'=> false , 'message'=> 'Data is empty');
				}
			}else{
				$result = array( 'status'=> false , 'message'=> 'Devices Key does not matche. please check your devices key');	
			}
		}else{
			$result = array( 'status'=> false , 'message'=> 'Please Enter All Fields');
		}
		$this->response($result);
	}*/
	public function daywise_post(){
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		$page = $this->input->post('page');
		$category_id = $this->input->post('category_id');
		
		$where = array();
		$where['start_date'] = $this->input->post('start_date');
		$where['table_whitelisted'] = ($this->input->post('table_whitelisted'))?$this->input->post('table_whitelisted'):0;
		/*$this->form_validation->set_rules('start_date', $this->lang->line("start_date"), 'required');*/
		
		/*if ($this->form_validation->run() == true) {*/
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){
				if($category_id){
					$data = $this->reports_api->daysales_BillDetails($warehouse_id,$where,$page,$category_id);
				}
				else{
				$data = $this->reports_api->daysales_report($warehouse_id,$where,$page);
			    }
				if($data['data'] == TRUE){
					$result = array( 'status'=> true , 'message'=> 'Success', 'data' => $data['data']);
				}else{
					$result = array( 'status'=> false , 'message'=> 'Data is empty');
				}
			}else{
				$result = array( 'status'=> false , 'message'=> 'Devices Key does not matche. please check your devices key');	
			}
		/*}else{
			$result = array( 'status'=> false , 'message'=> 'Please Enter All Fields');
		}*/
		$this->response($result);
	}
	public function monthly_reports_post(){
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		$page = $this->input->post('page');
		$category_id = $this->input->post('category_id');
		
		$where = array();
		$where['start_date'] = $this->input->post('start_date');
		$where['end_date'] = $this->input->post('end_date');
		$where['table_whitelisted'] = ($this->input->post('table_whitelisted'))?$this->input->post('table_whitelisted'):0;
		
		/*$this->form_validation->set_rules('start_date', $this->lang->line("start_date"), 'required');
		$this->form_validation->set_rules('end_date', $this->lang->line("end_date"), 'required');*/
		/*if ($this->form_validation->run() == true) {*/
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){
				if($category_id){
					$data = $this->reports_api->monthlysales_BillDetails($warehouse_id,$where,$page,$category_id);
				}
				else{
				   $data = $this->reports_api->monthlysales_report($warehouse_id,$where,$page);
			   }
				if($data['data'] == TRUE){
					$result = array( 'status'=> true , 'message'=> 'Success', 'data' => $data['data']);
				}else{
					$result = array( 'status'=> false , 'message'=> 'Data is empty');
				}
			}else{
				$result = array( 'status'=> false , 'message'=> 'Devices Key does not matche. please check your devices key');	
			}
		/*}else{
			$result = array( 'status'=> false , 'message'=> 'Please Enter All Fields');
		}*/
		$this->response($result);
	}
	public function hourlysales_post(){
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		$page = $this->input->post('page');
		
		$where = array();
		$where['start_date'] = $this->input->post('start_date');
		$where['end_date'] = $this->input->post('end_date');
		$where['start_time'] = $this->input->post('start_time');
		$where['end_time'] = $this->input->post('end_time');
		/*$where['time_range'] = $this->input->post('time_range');*/
		$where['table_whitelisted'] = ($this->input->post('table_whitelisted'))?$this->input->post('table_whitelisted'):0;
		$this->form_validation->set_rules('start_date', $this->lang->line("start_date"), 'required');
		$this->form_validation->set_rules('end_date', $this->lang->line("end_date"), 'required');
		$this->form_validation->set_rules('start_time', $this->lang->line("start_time"), 'required');
		$this->form_validation->set_rules('end_time', $this->lang->line("end_time"), 'required');
		/*$this->form_validation->set_rules('time_range', $this->lang->line("time_range"), 'required');*/
		if ($this->form_validation->run() == true) {
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){
				$data = $this->reports_api->hourlysales_report($warehouse_id,$where,$page);
				if($data == TRUE){
					$result = array( 'status'=> true , 'message'=> 'Success', 'data' => $data);
				}else{
					$result = array( 'status'=> false , 'message'=> 'Data is empty');
				}
			}else{
				$result = array( 'status'=> false , 'message'=> 'Devices Key does not matche. please check your devices key');	
			}
		}else{
			$result = array( 'status'=> false , 'message'=> 'Please Enter All Fields');
		}
		$this->response($result);
	}
	public function categories_post(){
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		$page = $this->input->post('page');
		
		$where = array();
		$where['start_date'] = $this->input->post('start_date');
		$where['end_date'] = $this->input->post('end_date');
		$where['category'] = $this->input->post('category');
		/*$this->form_validation->set_rules('start_date', $this->lang->line("start_date"), 'required');
		$this->form_validation->set_rules('end_date', $this->lang->line("end_date"), 'required');*/
		//$this->form_validation->set_rules('category', $this->lang->line("category"), 'required');
		/*if ($this->form_validation->run() == true) {*/
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){
				$data = $this->reports_api->categories_report($warehouse_id,$where,$page);
				if($data == TRUE){
					$result = array( 'status'=> true , 'message'=> 'Success', 'data' => $data);
				}else{
					$result = array( 'status'=> false , 'message'=> 'Data is empty');
				}
			}else{
				$result = array( 'status'=> false , 'message'=> 'Devices Key does not matche. please check your devices key');	
			}
		/*}else{
			$result = array( 'status'=> false , 'message'=> 'Please Enter All Fields');
		}*/
		$this->response($result);
	}
	public function brands_post(){
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		$page = $this->input->post('page');
		
		$where = array();
		$where['start_date'] = $this->input->post('start_date');
		$where['end_date'] = $this->input->post('end_date');
		$where['brand'] = $this->input->post('brand');
		/*$this->form_validation->set_rules('start_date', $this->lang->line("start_date"), 'required');
		$this->form_validation->set_rules('end_date', $this->lang->line("end_date"), 'required');*/
		//$this->form_validation->set_rules('category', $this->lang->line("category"), 'required');
		/*if ($this->form_validation->run() == true) {*/
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){
				$data = $this->reports_api->brands_report($warehouse_id,$where,$page);
				if($data == TRUE){
					$result = array( 'status'=> true , 'message'=> 'Success', 'data' => $data);
				}else{
					$result = array( 'status'=> false , 'message'=> 'Data is empty');
				}
			}else{
				$result = array( 'status'=> false , 'message'=> 'Devices Key does not matche. please check your devices key');	
			}
		/*}else{
			$result = array( 'status'=> false , 'message'=> 'Please Enter All Fields');
		}*/
		$this->response($result);
	}
	public function quantity_alerts_post(){
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		$page = $this->input->post('page');

		$where = array();
		
		//if ($this->form_validation->run() == true) {
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){
				$data = $this->reports_api->quantity_alerts_report($warehouse_id,$where,$page);
				if($data == TRUE){
					$result = array( 'status'=> true , 'message'=> 'Success', 'data' => $data);
				}else{
					$result = array( 'status'=> false , 'message'=> 'Data is empty');
				}
			}else{
				$result = array( 'status'=> false , 'message'=> 'Devices Key does not matche. please check your devices key');	
			}
		//}else{
		//	$result = array( 'status'=> false , 'message'=> 'Please Enter All Fields');
		//}
		$this->response($result);
	}
	public function customers_post(){
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		$page = $this->input->post('page');
		$customer_id = $this->input->post('customer_id');
		
		$where = array();
		$where['start_date'] = $this->input->post('start_date');
		$where['end_date'] = $this->input->post('end_date');
		$where['customer_id'] = $this->input->post('customer_id');
		$where['table_whitelisted'] = $this->input->post('table_whitelisted');
		
		//if ($this->form_validation->run() == true) {
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){

				if($customer_id){
					$data = $this->reports_api->getSalesReport($warehouse_id,$where,$page);
					
				}else{					
				    $data = $this->reports_api->customers_report($warehouse_id,$where,$page);
			    }

				if($data == TRUE){
					$result = array( 'status'=> true , 'message'=> 'Success', 'data' => $data);
				}else{
					$result = array( 'status'=> false , 'message'=> 'Data is empty');
				}
			}else{
				$result = array( 'status'=> false , 'message'=> 'Devices Key does not matche. please check your devices key');	
			}
		//}else{
		//	$result = array( 'status'=> false , 'message'=> 'Please Enter All Fields');
		//}
		$this->response($result);
	}
	public function suppliers_post(){
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		$page = $this->input->post('page');
		$supplier_id = $this->input->post('supplier_id');

		$where = array();
		$where['start_date'] = $this->input->post('start_date');
		$where['end_date'] = $this->input->post('end_date');
		$where['supplier_id'] = $this->input->post('supplier_id');
		$where['table_whitelisted'] = $this->input->post('table_whitelisted');
		
		
		//if ($this->form_validation->run() == true) {
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){
				if($supplier_id){

					$data = $this->reports_api->getPurchasesReport($warehouse_id,$where,$page);
				}else{
				    $data = $this->reports_api->suppliers_report($page);
			    } 
				if($data == TRUE){
					$result = array( 'status'=> true , 'message'=> 'Success', 'data' => $data);
				}else{
					$result = array( 'status'=> false , 'message'=> 'Data is empty');
				}
			}else{
				$result = array( 'status'=> false , 'message'=> 'Devices Key does not matche. please check your devices key');	
			}
		//}else{
		//	$result = array( 'status'=> false , 'message'=> 'Please Enter All Fields');
		//}
		$this->response($result);
	}
	public function purchases_post(){
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		$page = $this->input->post('page');
		
		$where = array();
		$where['start_date'] = $this->input->post('start_date');
		$where['end_date'] = $this->input->post('end_date');
		$where['product'] = $this->input->post('product');
		$where['supplier'] = $this->input->post('supplier');
		$where['user'] = $this->input->post('user');
		$where['reference_no'] = $this->input->post('reference_no');
		
		//if ($this->form_validation->run() == true) {
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){
				$data = $this->reports_api->purchases_report($warehouse_id,$where,$page);
				if($data == TRUE){
					$result = array( 'status'=> true , 'message'=> 'Success', 'data' => $data);
				}else{
					$result = array( 'status'=> false , 'message'=> 'Data is empty');
				}
			}else{
				$result = array( 'status'=> false , 'message'=> 'Devices Key does not matche. please check your devices key');	
			}
		//}else{
		//	$result = array( 'status'=> false , 'message'=> 'Please Enter All Fields');
		//}
		$this->response($result);
	}
	public function takeaway_post(){
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		$page = $this->input->post('page');
		
		$where = array();
		$where['start_date'] = $this->input->post('start_date');
		$where['end_date'] = $this->input->post('end_date');
		/*$this->form_validation->set_rules('start_date', $this->lang->line("start_date"), 'required');
		$this->form_validation->set_rules('end_date', $this->lang->line("end_date"), 'required');*/
		//$this->form_validation->set_rules('category', $this->lang->line("category"), 'required');
		/*if ($this->form_validation->run() == true) {*/
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){
				$data = $this->reports_api->takeaway_report($warehouse_id,$where,$page);
				if($data == TRUE){
					$result = array( 'status'=> true , 'message'=> 'Success', 'data' => $data);
				}else{
					$result = array( 'status'=> false , 'message'=> 'Data is empty');
				}
			}else{
				$result = array( 'status'=> false , 'message'=> 'Devices Key does not matche. please check your devices key');	
			}
		/*}else{
			$result = array( 'status'=> false , 'message'=> 'Please Enter All Fields');
		}*/
		$this->response($result);
	}
	public function homedelivery_post(){
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		$page = $this->input->post('page');
		$customer_id = $this->input->post('customer_id');
				
		$where = array();
		$where['start_date'] = $this->input->post('start_date');
		$where['end_date'] = $this->input->post('end_date');
		
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){
				$data = $this->reports_api->homedelivery_report($warehouse_id,$customer_id,$where,$page);
				if($data == TRUE){
					$result = array( 'status'=> true , 'message'=> 'Success', 'data' => $data);
				}else{
					$result = array( 'status'=> false , 'message'=> 'Data is empty');
				}
			}else{
				$result = array( 'status'=> false , 'message'=> 'Devices Key does not matche. please check your devices key');	
			}
		
		$this->response($result);
	}
	public function ordertiming_post(){
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		$page = $this->input->post('page');
		
		$where = array();
		$where['start_date'] = $this->input->post('start_date');
		$where['end_date'] = $this->input->post('end_date');
		$where['table_whitelisted'] = ($this->input->post('table_whitelisted'))?$this->input->post('table_whitelisted'):0;
	/*	$this->form_validation->set_rules('start_date', $this->lang->line("start_date"), 'required');
		$this->form_validation->set_rules('end_date', $this->lang->line("end_date"), 'required');*/
		//$this->form_validation->set_rules('category', $this->lang->line("category"), 'required');
		if($this->Settings->recipe_time_management) { 
			/*if ($this->form_validation->run() == true) {*/
				$devices_check = $this->site->devicesCheck($api_key);
				if($devices_check == $devices_key){
					$data = $this->reports_api->ordertiming_report($warehouse_id,$where,$page);
					if($data == TRUE){
						$result = array( 'status'=> true , 'message'=> 'Success', 'data' => $data);
					}else{
						$result = array( 'status'=> false , 'message'=> 'Data is empty');
					}
				}else{
					$result = array( 'status'=> false , 'message'=> 'Devices Key does not matche. please check your devices key');	
				}
			/*}else{
				$result = array( 'status'=> false , 'message'=> 'Please Enter All Fields');
			}*/
		}else{
			$result = array( 'status'=> false , 'message'=> 'Access Denied');
		}
		$this->response($result);
	}
	public function feedback_post(){
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		$page = $this->input->post('page');
		
		$where = array();
		
		//if ($this->form_validation->run() == true) {
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){
				$data = $this->reports_api->feedback_report($warehouse_id,$where,$page);
				if($data == TRUE){
					$result = array( 'status'=> true , 'message'=> 'Success', 'data' => $data);
				}else{
					$result = array( 'status'=> false , 'message'=> 'Data is empty');
				}
			}else{
				$result = array( 'status'=> false , 'message'=> 'Devices Key does not matche. please check your devices key');	
			}
		//}else{
			//$result = array( 'status'=> false , 'message'=> 'Please Enter All Fields');
		//}
		$this->response($result);
	}
	public function warehouse_post(){
		$api_key = $this->input->get('api-key');
		$page = $this->input->post('page');
		
		$data = $this->reports_api->GetAllwarehouse($page);
		if(!empty($data)){
			$result = array( 'status'=> true , 'message'=> 'Success', 'data' => $data);
		}else{
			$result = array( 'status'=> false , 'message'=> 'Warehouse Data is Empty');
		}
		
		$this->response($result);
	}
	public function users_post(){
		$api_key = $this->input->get('api-key');
		$page = $this->input->post('page');
		
		$data = $this->reports_api->GetAllusers($page);
		if(!empty($data)){
			$result = array( 'status'=> true , 'message'=> 'Success', 'data' => $data);
		}else{
			$result = array( 'status'=> false , 'message'=> 'Warehouse Data is Empty');
		}
		
		$this->response($result);
	}	

	public function brand_list_post(){
		$api_key = $this->input->get('api-key');
		$page = $this->input->post('page');
		
		$data = $this->reports_api->GetAllBrand($page);
		if(!empty($data)){
			$result = array( 'status'=> true , 'message'=> 'Success', 'data' => $data);
		}else{
			$result = array( 'status'=> false , 'message'=> 'Warehouse Data is Empty');
		}
		
		$this->response($result);
	}
	public function categories_list_post(){
		$api_key = $this->input->get('api-key');
		$page = $this->input->post('page');
		
		$data = $this->reports_api->GetAllCategories($page);
		if(!empty($data)){
			$result = array( 'status'=> true , 'message'=> 'Success', 'data' => $data);
		}else{
			$result = array( 'status'=> false , 'message'=> 'Categories Data is Empty');
		}
		
		$this->response($result);
	}		
	public function staff_post(){
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		$page = $this->input->post('page');
		$type = $this->input->post('type');
		$year = $this->input->post('year');
		$month = $this->input->post('month');
		if (!$year) {
            $year = date('Y');
        }
        if (!$month) {
            $month = date('m');
        }
		
		$where = array();
		//$this->form_validation->set_rules('category', $this->lang->line("category"), 'required');
		 
			//if ($this->form_validation->run() == true) {
				$devices_check = $this->site->devicesCheck($api_key);
				if($devices_check == $devices_key){
					if($type == 'daily_sales'){
						$data = $this->reports_api->getStaffDailySales($user_id, $year, $month, $warehouse_id);
					}elseif ($type == 'monthly_sales') {
						$data = $this->reports_api->getStaffMonthlySales($user_id, $year, $warehouse_id);
					}
					elseif ($type == 'staff_sales_report') {
						$data = $this->reports_api->getStaffSalesReport($warehouse_id,$user_id,$page);
					}
					elseif ($type == 'staff_purchase_report') {
						$data = $this->reports_api->getStaffPurchasedReport($warehouse_id,$user_id,$page);
					}
					elseif ($type == 'staff_payment_report') {
						$data = $this->reports_api->getStaffPaymentReport($warehouse_id,$user_id,$page);
					}
					elseif ($type == 'staff_login_report') {
						$data = $this->reports_api->getStaffLoginReport($warehouse_id,$user_id,$page);
					}
					else{
					  $data = $this->reports_api->staff_report($where,$page);
				   }
					if($data == TRUE){
						$result = array( 'status'=> true , 'message'=> 'Success', 'data' => $data);
					}else{
						$result = array( 'status'=> false , 'message'=> 'Data is empty');
					}
				}else{
					$result = array( 'status'=> false , 'message'=> 'Devices Key does not matche. please check your devices key');	
				}
			//}else{
			//	$result = array( 'status'=> false , 'message'=> 'Please Enter All Fields');
			//}
		
		$this->response($result);
	}
	public function adjustments_post(){
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		$page = $this->input->post('page');
		
		$where = array();
		$where['start_date'] = $this->input->post('start_date');
		$where['end_date'] = $this->input->post('end_date');
		$where['user'] = $this->input->post('user');
		$where['reference_no'] = $this->input->post('reference_no');
		$where['serial'] = $this->input->post('serial');
		$where['product'] = $this->input->post('product');
		$this->form_validation->set_rules('start_date', $this->lang->line("start_date"), 'required');
		$this->form_validation->set_rules('end_date', $this->lang->line("end_date"), 'required');
		//$this->form_validation->set_rules('category', $this->lang->line("category"), 'required');
		
		if ($this->form_validation->run() == true) {
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){
				$data = $this->reports_api->adjustments_report($warehouse_id,$where,$page);
				if($data == TRUE){
					$result = array( 'status'=> true , 'message'=> 'Success', 'data' => $data);
				}else{
					$result = array( 'status'=> false , 'message'=> 'Data is empty');
				}
			}else{
				$result = array( 'status'=> false , 'message'=> 'Devices Key does not matche. please check your devices key');	
			}
		}else{
			$result = array( 'status'=> false , 'message'=> 'Please Enter All Fields');
		}
		
		$this->response($result);
	}
	public function recipes_post(){
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		$page = $this->input->post('page');
		
		$where = array();
		$where['start_date'] = $this->input->post('start_date');
		$where['end_date'] = $this->input->post('end_date');
		$where['product'] = $this->input->post('product');
		$where['table_whitelisted'] = $this->input->post('table_whitelisted');
	
		
		/*if ($this->form_validation->run() == true) {*/
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){
				$data = $this->reports_api->products_report($warehouse_id,$where,$page);
				if($data == TRUE){
					$result = array( 'status'=> true , 'message'=> 'Success', 'data' => $data);
				}else{
					$result = array( 'status'=> false , 'message'=> 'Data is empty');
				}
			}else{
				$result = array( 'status'=> false , 'message'=> 'Devices Key does not matche. please check your devices key');	
			}
		/*}else{
			$result = array( 'status'=> false , 'message'=> 'Please Enter All Fields');
		}*/
		
		$this->response($result);
	}
	public function void_bills_post(){
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		$page = $this->input->post('page');
		
		$where = array();
		$where['start_date'] = $this->input->post('start_date');
		$where['end_date'] = $this->input->post('end_date');
		
/*		$this->form_validation->set_rules('start_date', $this->lang->line("start_date"), 'required');
		$this->form_validation->set_rules('end_date', $this->lang->line("end_date"), 'required');*/
		//$this->form_validation->set_rules('category', $this->lang->line("category"), 'required');
		
		/*if ($this->form_validation->run() == true) {*/
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){
				$data = $this->reports_api->void_bills_report($warehouse_id,$where,$page);
				if($data == TRUE){
					$result = array( 'status'=> true , 'message'=> 'Success', 'data' => $data);
				}else{
					$result = array( 'status'=> false , 'message'=> 'Data is empty');
				}
			}else{
				$result = array( 'status'=> false , 'message'=> 'Devices Key does not matche. please check your devices key');	
			}
		/*}else{
			$result = array( 'status'=> false , 'message'=> 'Please Enter All Fields');
		}*/
		
		$this->response($result);
	}
	public function products_expiry_post(){
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		$page = $this->input->post('page');
		
		$where = array();
		
		//if ($this->form_validation->run() == true) {
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){
				$data = $this->reports_api->products_expiry_report($warehouse_id,$where,$page);
				if($data == TRUE){
					$result = array( 'status'=> true , 'message'=> 'Success', 'data' => $data);
				}else{
					$result = array( 'status'=> false , 'message'=> 'Data is empty');
				}
			}else{
				$result = array( 'status'=> false , 'message'=> 'Devices Key does not matche. please check your devices key');	
			}
		//}else{
		//	$result = array( 'status'=> false , 'message'=> 'Please Enter All Fields');
		//}
		
		$this->response($result);
	}
	public function best_sellers_post(){
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		$page = $this->input->post('page');
		
		$where = array();
		
		//if ($this->form_validation->run() == true) {
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){
				$data = $this->reports_api->best_sellers_report($warehouse_id,$where,$page);
				if($data == TRUE){
					$result = array( 'status'=> true , 'message'=> 'Success', 'data' => $data);
				}else{
					$result = array( 'status'=> false , 'message'=> 'Data is empty');
				}
			}else{
				$result = array( 'status'=> false , 'message'=> 'Devices Key does not matche. please check your devices key');	
			}
		//}else{
		//	$result = array( 'status'=> false , 'message'=> 'Please Enter All Fields');
		//}
		
		$this->response($result);
	}
	public function stock_audit_post(){
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		$page = $this->input->post('page');
		$product_id = $this->input->post('product_id');
		$date = $this->input->post('date');
		
		$this->form_validation->set_rules('date', $this->lang->line("date"), 'required');
		
		if ($this->form_validation->run() == true) {
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){
				$data = $this->reports_api->getStockVariance($date,$product_id,$warehouse_id,$page);
				if($data == TRUE){
					$result = array( 'status'=> true , 'message'=> 'Success', 'data' => $data);
				}else{
					$result = array( 'status'=> false , 'message'=> 'Data is empty');
				}
			}else{
				$result = array( 'status'=> false , 'message'=> 'Devices Key does not matche. please check your devices key');	
			}
		}else{
			$result = array( 'status'=> false , 'message'=> 'Please Enter All Fields');
		}
		
		$this->response($result);
	}
	public function warehouse_stock_post(){
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		$page = $this->input->post('page');
		
		$where = array();
		
		$devices_check = $this->site->devicesCheck($api_key);
		if($devices_check == $devices_key){
			$data = $this->reports_api->warehouse_stock_report($warehouse_id,$where,$page);
			if($data == TRUE){
				$result = array( 'status'=> true , 'message'=> 'Success', 'data' => $data);
			}else{
				$result = array( 'status'=> false , 'message'=> 'Data is empty');
			}
		}else{
			$result = array( 'status'=> false , 'message'=> 'Devices Key does not matche. please check your devices key');	
		}
			
		
		$this->response($result);
	}
	public function monthly_sales_post(){
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		
		$where = array();
		$year = $this->input->post('year');
		if($year == ""){
			$year =date("Y");
		}
		
		$where['table_whitelisted'] = ($this->input->post('table_whitelisted'))?$this->input->post('table_whitelisted'):0;
		$devices_check = $this->site->devicesCheck($api_key);
		/*$this->form_validation->set_rules('year', $this->lang->line("year"), 'required');
		$this->form_validation->set_rules('month', $this->lang->line("month"), 'required');*/
		/*if ($this->form_validation->run() == true) {*/
			if($devices_check == $devices_key){
				$data = $this->reports_api->getMonthlySales($year,$warehouse_id);
				if($data == TRUE){
					$result = array( 'status'=> true , 'message'=> 'Success', 'data' => $data);
				}else{
					$result = array( 'status'=> false , 'message'=> 'Data is empty');
				}
			}else{
				$result = array( 'status'=> false , 'message'=> 'Devices Key does not matche. please check your devices key');	
			}
		/*}else{
			$result = array( 'status'=> false , 'message'=> 'Please Enter All Fields');
		}*/
			
		
		$this->response($result);
	}
	public function daily_sales_post(){
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		$year = $this->input->post('year');
		$month = $this->input->post('month');
		
		$where = array();
		if (!$year) {
            $year = date('Y');
        }
        if (!$month) {
            $month = date('m');
        }

		$where['table_whitelisted'] = ($this->input->post('table_whitelisted'))?$this->input->post('table_whitelisted'):0;
	    	$devices_check = $this->site->devicesCheck($api_key);
		
			if($devices_check == $devices_key){
				$data = $this->reports_api->getDailySales($year, $month, $warehouse_id);
				if($data == TRUE){
					$result = array( 'status'=> true , 'message'=> 'Success', 'data' => $data);
				}else{
					$result = array( 'status'=> false , 'message'=> 'Data is empty');
				}
			}else{
				$result = array( 'status'=> false , 'message'=> 'Devices Key does not matche. please check your devices key');	
			}
		
		$this->response($result);
	}
	
	public function daily_purchases_post(){
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		$year = $this->input->post('year');
		$month = $this->input->post('month');
		
		 if (!$year) {
            $year = date('Y');
        }
        if (!$month) {
            $month = date('m');
        }
				
		$devices_check = $this->site->devicesCheck($api_key);
		if($devices_check == $devices_key){
			$data = $this->reports_api->getDailyPurchases($year, $month, $warehouse_id);
			if($data == TRUE){
				$result = array( 'status'=> true , 'message'=> 'Success', 'data' => $data);
			}else{
				$result = array( 'status'=> false , 'message'=> 'Data is empty');
			}
		}else{
			$result = array( 'status'=> false , 'message'=> 'Devices Key does not matche. please check your devices key');	
			}

	  $this->response($result);
	}
	public function monthly_purchases_post(){
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');

		$year = $this->input->post('year');
		if($year == ""){
			$year =date("Y");
		}
			
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){
				$data = $this->reports_api->getMonthlyPurchases($year,$warehouse_id);
				if($data == TRUE){
					$result = array( 'status'=> true , 'message'=> 'Success', 'data' => $data);
				}else{
					$result = array( 'status'=> false , 'message'=> 'Data is empty');
				}
			}else{
				$result = array( 'status'=> false , 'message'=> 'Devices Key does not matche. please check your devices key');	
			}
		
		
		$this->response($result);
	}
	public function getAllproductsDetails_post(){
		$api_key = $this->input->get('api-key');
		$page = $this->input->post('page');
		
		$data = $this->reports_api->getProducts();
		if(!empty($data)){
			$result = array( 'status'=> true , 'message'=> 'Success', 'data' => $data);
		}else{
			$result = array( 'status'=> false , 'message'=> 'Warehouse Data is Empty');
		}		
		$this->response($result);
	}	
    function profit_loss_post()
    {        
        $api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');

		$start_date = $this->input->post('start_date');
		$end_date = $this->input->post('end_date');

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

        $devices_check = $this->site->devicesCheck($api_key);
		if($devices_check == $devices_key){

        $this->data['total_purchases'][] = $this->reports_api->getTotalPurchases($start, $end);

        $total_purchases = $this->reports_api->getTotalPurchases($start, $end);
        /*echo "<pre>";
        print_r($this->data['total_purchases']);die;*/
        $this->data['total_sales'][] = $this->reports_api->getTotalSales($start, $end);

        $total_sales = $this->reports_api->getTotalSales($start, $end);

        $this->data['total_expenses'][] = $this->reports_api->getTotalExpenses($start, $end);

        $total_expenses = $this->reports_api->getTotalExpenses($start, $end);
        $this->data['payment_send'][] = $this->reports_api->getTotalPaidAmount($start, $end);
        $payment_send = $this->reports_api->getTotalPaidAmount($start, $end);
        $total_received = $this->reports_api->getTotalReceivedAmount($start, $end);
        $total_received_cash = $this->reports_api->getTotalReceivedCashAmount($start, $end);
        $total_received_cc= $this->reports_api->getTotalReceivedCCAmount($start, $end);
        $total_received_cheque = $this->reports_api->getTotalReceivedChequeAmount($start, $end);
        $total_received_ppp = $this->reports_api->getTotalReceivedPPPAmount($start, $end);
        $total_received_stripe = $this->reports_api->getTotalReceivedStripeAmount($start, $end);
        $total_returned = $this->reports_api->getTotalReturnedAmount($start, $end);
       /* $this->data['start'] = urldecode($start_date);
        $this->data['end'] = urldecode($end_date);*/
        $payments[] = array(
        	    'total' => $total_received->total_amount,
        	    'received' => $total_received->total_amount,
                'returned' => $total_returned->total_amount,
                'send' => $payment_send->total_amount,
                'expenses' => $total_expenses->total_amount,
                );
        $this->data['payments'] = $payments;

         $profit_loss1[] = array(
        	    'amount' => $total_sales->total_amount - $total_purchases->total_amount,
        	    'sales' => $total_sales->total_amount,
                'purchases' =>  $total_purchases->total_amount,
                );
        $this->data['profit_loss1'] = $profit_loss1;

         $profit_loss2[] = array(
        	    'amount' => $total_sales->total_amount - $total_purchases->total_amount,
        	    'sales' => $total_sales->total_amount,
        	    'sales_tax' => $total_sales->tax,
                'purchases' => $total_purchases->total_amount,
                );
        $this->data['profit_loss2'] = $profit_loss2;

        $profit_loss3[] = array(
        	    'amount' => $total_sales->total_amount - $total_purchases->total_amount,
        	    'sales' => $total_sales->total_amount,
        	    'sales_tax' => $total_sales->tax,
                'purchases' => $total_purchases->total_amount,
                'purchases_tax' => $total_purchases->tax,
                );
        $this->data['profit_loss3'] = $profit_loss3;

        $payments_recived[] = array(
        	    'received_amt' => $total_received->total_amount,
        	    'received_total' => $total_received->total,
                'cash' => $total_received_cash->total_amount,
                'credit_card' => $total_received_cc->total_amount,
                'cheque' => $total_received_cheque->total_amount,
                'paypal' => $total_received_ppp->total_amount,
                'stripe' => $total_received_stripe->total_amount,
                );
        $this->data['payments_recived'] = $payments_recived;
        $warehouses = $this->site->getAllWarehouses();
        foreach ($warehouses as $warehouse) {
            $total_purchases = $this->reports_api->getTotalPurchases($start, $end, $warehouse->id);
            $total_sales = $this->reports_api->getTotalSales($start, $end, $warehouse->id);
            $total_expenses = $this->reports_api->getTotalExpenses($start, $end, $warehouse->id);
            $warehouses_report[] = array(
                'warehouse_name' => $warehouse->name,
                'warehouse_code' => $warehouse->code,
                'sales_total' => $total_sales->total_amount,
                'sales_tax' => $total_sales->tax,
                'purchases_total' => $total_purchases->total_amount,
                'purchases_tax' => $total_purchases->tax,
                'total_expenses' => $total_expenses->total_amount,
                );
        }
          $this->data['warehouses_report'] = $warehouses_report;
          $res = array($this->data);
/*echo "<pre>";
print_r($res);die;*/
          if(!empty($this->data)){
          	
			$result = array( 'status'=> true , 'message'=> 'Success', 'data' => $res);
		    }else{
			$result = array( 'status'=> false , 'message'=> 'Data is Empty');
		    }

        }else{
			$result = array( 'status'=> false , 'message'=> 'Devices Key does not matche. please check your devices key');	
			}
				
		$this->response($result);
    }	
	public function biller_post(){

		$api_key = $this->input->get('api-key');		
		$data = $this->site->getAllCompanies('biller');
		if(!empty($data)){
			$result = array( 'status'=> true , 'message'=> 'Success', 'data' => $data);
		}else{
			$result = array( 'status'=> false , 'message'=> 'Warehouse Data is Empty');
		}		
		$this->response($result);
	}
	public function customer_post(){

		$api_key = $this->input->get('api-key');		
		$data = $this->site->getAllCompanies('customer');
		if(!empty($data)){
			$result = array( 'status'=> true , 'message'=> 'Success', 'data' => $data);
		}else{
			$result = array( 'status'=> false , 'message'=> 'Warehouse Data is Empty');
		}		
		$this->response($result);
	}
	public function home_delivery_customer_post(){

		$api_key = $this->input->get('api-key');		
		$data = $this->reports_api->HomedeliveryCostomer();
		if(!empty($data)){
			$result = array( 'status'=> true , 'message'=> 'Success', 'data' => $data);
		}else{
			$result = array( 'status'=> false , 'message'=> 'Customer Data is Empty');
		}		
		$this->response($result);
	}		    
}
