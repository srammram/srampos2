<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;



class Login extends REST_Controller {

	function __construct() {
		parent::__construct();
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		$this->load->api_model('login_api');
		$this->load->library('firebase');
		$this->load->library('push');		
		$this->lang->admin_load('engliah_khmer','english');
	}
	
	public function index_post()
	{				
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		
		$user_number = $this->input->post('user_number');
		
		$this->form_validation->set_rules('user_number', $this->lang->line("user_number"), 'required');
		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');		
		if ($this->form_validation->run() == true) {
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){
				$data = $this->login_api->GetuserByID($user_number);
				$exchange_rate = $this->site->getExchangeRatey($data->base_currency_id);
				if($exchange_rate)
				{
					$data->base_currency_rate =$exchange_rate;
				}
				$settings = $this->site->get_setting();
				$timeout = $settings->notification_time_interval;
				if(!empty($data)){
					$check_nightaudit = $this->login_api->Checknightaudit($data->warehouse_id);
					/*$device_token = $this->login_api->deviceGET($user_number);
					
					if(!empty($device_token)){
						$title = 'Hi';
						$message = 'Welcome';
						$push_data = $this->push->setPush($title,$message);
						//print_r($push_data);
						if($push_data == true){
							$json = '';
							$response = '';
							$json = $this->push->getPush();
							$regId = $device_token;
							$response_data = $this->firebase->send($regId, $json);
						}
					}*/
					if($this->site->isSocketEnabled()){
						$socket_status ="1";
					}else{
						$socket_status ="0";
					}
					$settings = $this->site->get_setting();
					$socket_port = $settings->socket_port;
					$socket_host = $settings->socket_host;
					$socket_enable = $settings->socket_enable;
					if(!$check_nightaudit){
						$result = array( 'status'=> false , 'message'=> lang('night_audit_not_complete'),'message_khmer'=> html_entity_decode(lang('night_audit_not_complete_khmer')));	
					}else{
						$result = array( 'status'=> true , 'message'=> lang('sucessfully_logged_in'),'message_khmer'=> html_entity_decode(lang('sucessfully_logged_in_khmer')), 'data' => $data,'notification_timeout'=>$timeout,'socket_status'=>$socket_status,'socket_port'=>$socket_port,'socket_host'=>$socket_host);				
					}	
				}else{
					$result = array( 'status'=> false , 'message'=> lang('your_passcode_invaild'),'message_khmer'=> html_entity_decode(lang('your_passcode_invaild_khmer')));	
				}
			}else{
				$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
			}
			
		}else{
			$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));	
		}
		
		$this->response($result);
		
	}
	
	public function usertoken_post(){
			
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$device_token = $this->input->post('device_token');
		$device_type = 'Android or IOS';
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		
		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		if ($this->form_validation->run() == true) {
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){
				$data_array1 = array(
					'device_token' => $device_token,
					'user_id' => $user_id,
					'group_id' => $group_id,
					'device_type' => $device_type,
					'created' => date('Y-m-d H:i:s')
				);
				$data_array2 = array(
					'device_token' => $device_token,
					'user_id' => $user_id,
					'group_id' => $group_id,
					'device_type' => $device_type,
					'devices_key' => $devices_key,
					'created' => date('Y-m-d H:i:s')
				);
				
				$data = $this->login_api->userDevices($devices_key, $data_array1, $data_array2);
				if($data == TRUE){
					$result = array( 'status'=> true ,  'message'=> lang('devices_token_updated'), 'message_khmer'=> html_entity_decode(lang('devices_token_updated_khmer')));	
				}else{
					$result = array( 'status'=> false , 'message'=> lang('devices_token_does_not_updated'), 'message_khmer'=> html_entity_decode(lang('devices_token_does_not_updated_khmer')));	
				}
			}else{
				$result = array( 'status'=> false ,  'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
			}
		}else{
			$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));
		}
		$this->response($result);
	}
	
	public function userlogouttoken_post(){
			
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$device_token = '';
		$device_type = '';
		$user_id = 0;
		$group_id = 0;
		
		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		if ($this->form_validation->run() == true) {
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){
				$data_array = array(
					'device_token' => $device_token,
					'user_id' => $user_id,
					'group_id' => $group_id,
					'device_type' => $device_type,
					'created' => date('Y-m-d H:i:s')
				);
				
				
				$data = $this->login_api->userlogoutDevices($devices_key, $data_array);
				if($data == TRUE){
					$result = array( 'status'=> true , 'message'=> lang('devices_token_log​​_out_updated'),'message_khmer'=> html_entity_decode(lang('devices_token_log​​_out_updated_khmer')));	
				}else{
					$result = array( 'status'=> false , 'message'=> lang('devices_token_does_not_logout_updated'),'message_khmer'=> html_entity_decode(lang('devices_token_does_not_logout_updated_khmer')));	
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
