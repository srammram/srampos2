<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;



class Apisetting extends REST_Controller {

	function __construct() {
		parent::__construct();
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		$this->load->api_model('apisetting_api');
		$this->lang->admin_load('engliah_khmer','english');
	}
	
	public function buildapi_post(){
		$api_key = $this->input->post('api-key');
		$type = $this->input->post('type');
		
		$data = $this->apisetting_api->GetBuild($type);
		if(!empty($data)){
			$result = array( 'status'=> true , 'message'=> lang('build_files'),'message_khmer'=> html_entity_decode(lang('build_files_khmer')), 'data' => $data);
		}else{
			$result = array( 'status'=> false , 'message'=> lang('build_files_empty'),'message_khmer'=> html_entity_decode(lang('build_files_empty_khmer')));
		}
		
		$this->response($result);
	}
	
	public function index_post()
	{
		
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$devices_type = $this->input->post('devices_type');
		$api_type = 2;
		
		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		$this->form_validation->set_rules('devices_type', $this->lang->line("devices_type"), 'required');
		
		if ($this->form_validation->run() == true) {
			$data = $this->apisetting_api->checkDevices($api_key);
			$settings = $this->site->get_setting();
			$resdata['socket_port'] = $settings->socket_port;
			$resdata['socket_host'] = $settings->socket_host;
			$resdata['socket_enable'] = $settings->socket_enable;
			if(!empty($data->devices_key)){
				
				if($data->devices_key == $devices_key && $data->api_type == $api_type){
					$result = array( 'status'=> true , 'message'=> lang('devices_key_does_is_matched'),'message_khmer'=> html_entity_decode(lang('devices_key_does_is_matched_khmer')),'data'=>$resdata);	
				}else{
					$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
				}
			}else{
				$this->apisetting_api->updateDevices($api_key, $devices_key, $devices_type, $api_type);
				$result = array( 'status'=> true , 'message'=> lang('new_devices_key_has_been_insert'),'message_khmer'=> html_entity_decode(lang('new_devices_key_has_been_insert_khmer')),'data'=>$resdata);	
			}
		}else{
			$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));	
		}
		$this->response($result);
		
	}
	
	public function apitype_get(){
		$api_key = $this->input->get('api-key');
		$data = $this->apisetting_api->GetAllapitype();
		if(!empty($data)){
			$result = array( 'status'=> true , 'message'=> lang('api_type_data'),'message_khmer'=> html_entity_decode(lang('api_type_data_khmer')), 'data' => $data);
		}else{
			$result = array( 'status'=> false , 'message'=> lang('api_type_list_empty'),'message_khmer'=> html_entity_decode(lang('api_type_list_empty_khmer')));
		}
		
		$this->response($result);
	}
	
	public function refreshtoken_post(){
			
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$device_token = $this->input->post('device_token');
		$device_type = 'Android or IOS';
		
		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		if ($this->form_validation->run() == true) {
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){
				$data_array = array(
					'device_token' => $device_token,
					'created' => date('Y-m-d H:i:s')
				);
				$data = $this->apisetting_api->refreshDevices($devices_key, $data_array);
				if($data == TRUE){
					$result = array( 'status'=> true , 'message'=> lang('devices_token_updated'),'message_khmer'=> html_entity_decode(lang('devices_token_updated_khmer')));	
				}else{
					$result = array( 'status'=> false , 'message'=> lang('devices_token_does_not_updated'),'message_khmer'=> html_entity_decode(lang('devices_token_does_not_updated_khmer')));	
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
	
	

