<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;



class Customersetting extends REST_Controller {

	function __construct() {
		parent::__construct();
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		$this->load->api_model('customersetting_api');
		$this->lang->admin_load('engliah_khmer','english');
	}
	
	public function index_post()
	{
		
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$devices_type = $this->input->post('devices_type');
		
		$warehouse_id = $this->input->post('warehouse_id');
		$table_id = $this->input->post('table_id');
		$waiter_id = $this->site->getSteward($table_id);//$this->input->post('waiter_id');
		$api_type = 3;
		
		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		$this->form_validation->set_rules('devices_type', $this->lang->line("devices_type"), 'required');
		
		if ($this->form_validation->run() == true) {
			$data = $this->customersetting_api->checkDevices($api_key);
			$all = $this->customersetting_api->fetchData($warehouse_id, $table_id, $waiter_id);

			if(!empty($data->devices_key)){	
				
							
				if($data->devices_key == $devices_key && $data->api_type == $api_type){	
							
					$result = array( 'status'=> true , 'message'=> lang('devices_key_does_is_matched'),'message_khmer'=> html_entity_decode(lang('devices_key_does_is_matched_khmer')), 'data' => $all);	
				}else{
					$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
				}
			}else{
				$this->customersetting_api->updateDevices($api_key, $devices_key, $devices_type, $api_type);
				$result = array( 'status'=> true , 'message'=> lang('new_devices_key_has_been_insert'),'message_khmer'=> html_entity_decode(lang('new_devices_key_has_been_insert_khmer')), 'data' => $all);	
			}
		}else{
			$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));	
		}
		$this->response($result);
		
	}
	
	public function buildapi_post(){
		$api_key = $this->input->post('api-key');
		$type = $this->input->post('type');
		
		$data = $this->customersetting_api->GetBuild($type);
		if(!empty($data)){
			$result = array( 'status'=> true , 'message'=> lang('build_files'),'message_khmer'=> html_entity_decode(lang('build_files_khmer')), 'data' => $data);
		}else{
			$result = array( 'status'=> false , 'message'=> lang('build_files_empty'),'message_khmer'=> html_entity_decode(lang('build_files_empty_khmer')));
		}
		
		$this->response($result);
	}
	
	public function waiterdetails_post(){
		$api_key = $this->input->post('api-key');
		$waiter_id = $this->input->post('waiter_id');
		
		$data = $this->customersetting_api->GetwaiterDetails($waiter_id);
		if(!empty($data)){
			$result = array( 'status'=> true , 'message'=> lang('waiter_details_data'),'message_khmer'=> html_entity_decode(lang('waiter_details_data_khmer')), 'data' => $data);
		}else{
			$result = array( 'status'=> false , 'message'=> lang('waiter_details_empty'),'message_khmer'=> html_entity_decode(lang('waiter_details_empty_khmer')));
		}
		
		$this->response($result);
	}
	
	public function alltablecategory_get(){
		
		$api_key = $this->input->get('api-key');
		$warehouse_id = $this->input->get('warehouse_id');

		$data = $this->customersetting_api->Alltablecategory($warehouse_id);
		if(!empty($data)){
			$result = array( 'status'=> true , 'message'=> lang('tables_area_data'),'message_khmer'=> html_entity_decode(lang('tables_area_data_khmer')), 'data' => $data);
		}else{
			$result = array( 'status'=> false , 'message'=> lang('tables_area_empty'),'message_khmer'=> html_entity_decode(lang('tables_area_empty_khmer')));
		}
		
		$this->response($result);
		
		
	}
	
	public function table_get(){

		$api_key = $this->input->get('api-key');
		$warehouse_id = $this->input->get('warehouse_id');
		$area_id = $this->input->get('area_id'); 
		$bbq_type = $this->input->get('bbq_type'); 
		
		$data = $this->customersetting_api->GetAlltables($warehouse_id, $area_id, $bbq_type);
		if(!empty($data)){
			$result = array( 'status'=> true , 'message'=> lang('tables_list_data'),'message_khmer'=> html_entity_decode(lang('tables_list_data_khmer')), 'data' => $data);
		}else{
			$result = array( 'status'=> false , 'message'=> lang('tables_list_empty'),'message_khmer'=> html_entity_decode(lang('tables_list_empty_khmer')));
		}
		
		$this->response($result);
	}
	
	public function apitype_get(){
		$api_key = $this->input->get('api-key');
		$data = $this->customersetting_api->GetAllapitype();
		if(!empty($data)){
			$result = array( 'status'=> true , 'message'=> lang('api_type_data'),'message_khmer'=> html_entity_decode(lang('api_type_data_khmer')), 'data' => $data);
		}else{
			$result = array( 'status'=> false , 'message'=> lang('api_type_list_empty'),'message_khmer'=> html_entity_decode(lang('api_type_list_empty_khmer')));
		}
		
		$this->response($result);
	}
	
	public function warehouse_get(){
		$api_key = $this->input->get('api-key');
		
		$data = $this->customersetting_api->GetAllwarehouse();
		if(!empty($data)){
			$result = array( 'status'=> true , 'message'=> lang('warehouse_list_data'),'message_khmer'=> html_entity_decode(lang('warehouse_list_data_khmer')), 'data' => $data);
		}else{
			$result = array( 'status'=> false , 'message'=> lang('warehouse_list_empty'),'message_khmer'=> html_entity_decode(lang('warehouse_list_empty_khmer')));
		}
		
		$this->response($result);
	}
	
	public function waiter_get(){
		$api_key = $this->input->get('api-key');
		$warehouse_id = $this->input->get('warehouse_id');
		
		$data = $this->customersetting_api->GetAllwaiter($warehouse_id);
		if(!empty($data)){
			$result = array( 'status'=> true , 'message'=> lang('waiter_list_data'),'message_khmer'=> html_entity_decode(lang('waiter_list_data_khmer')), 'data' => $data);
		}else{
			$result = array( 'status'=> false , 'message'=> lang('waiter_list_empty'),'message_khmer'=> html_entity_decode(lang('waiter_list_empty_khmer')));
		}
		
		$this->response($result);
	}
	
	public function checkuser_post(){
		$api_key = $this->input->post('api-key');
		$user_number = $this->input->post('user_number');		
		$this->form_validation->set_rules('user_number', $this->lang->line("user_number"), 'required');		
		if ($this->form_validation->run() == true) {
			
			if($user_number == '1234'){
				$result = array( 'status'=> true , 'message'=> lang('admin_user_code_matched'),'message_khmer'=> html_entity_decode(lang('admin_user_code_matched_khmer')));	
			}else{
				$result = array( 'status'=> false , 'message'=> lang('admin_user_code_does_not_matched'),'message_khmer'=> html_entity_decode(lang('admin_user_code_does_not_matched_khmer')));	
			}
			
		}else{
			$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));	
		}
		$this->response($result);
	}
	public function settings_post(){
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$table_id = $this->input->post('table_id');
		$user_id = $this->input->post('user_id');
		$warehouse_id = $this->input->post('warehouse_id');
		
		
		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		$this->form_validation->set_rules('user_id', $this->lang->line("user_id"), 'required');
		$this->form_validation->set_rules('table_id', $this->lang->line("table_id"), 'required');
		$this->form_validation->set_rules('warehouse_id', $this->lang->line("warehouse"), 'required');
		if ($this->form_validation->run() == true) {
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){
				$data = $this->customersetting_api->addSettings($table_id, $user_id);
				if($data){
					$result = array( 'status'=> true , 'message'=> lang('settings_updated'),'message_khmer'=> html_entity_decode(lang('settings_updated')));
				}else{
					$result = array( 'status'=> false , 'message'=> lang('settings_not_updated'),'message_khmer'=> html_entity_decode(lang('settings_not_updated')));
				}
			}else{
				$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
			}
		}else{
			$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));	
		}
		
		$this->response($result);
	}
	
}
