<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;



class Customer extends REST_Controller {

	function __construct() {
		parent::__construct();
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		$this->load->api_model('customer_api');
		$this->load->library('firebase');
		$this->load->library('push');
		$this->load->library('upload');
		$this->lang->admin_load('engliah_khmer','english');
		
	}
	
	
	
	public function bilsatatus(){
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		$table_id = $this->input->post('table_id');
		
		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		$this->form_validation->set_rules('user_id', $this->lang->line("user_id"), 'required');
		$this->form_validation->set_rules('warehouse_id', $this->lang->line("warehouse"), 'required');
		$this->form_validation->set_rules('table_id', $this->lang->line("table"), 'required');
		if ($this->form_validation->run() == true) {
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){	
				$data = $this->customer_api->checkbilStatus($user_id, $warehouse_id,$table_id);
				
				if(!empty($data)){
					$result = array( 'status'=> true ,  'message'=> lang('success'),'message_khmer'=> html_entity_decode(lang('success_khmer')));
				}else{
					$result = array( 'status'=> false , 'message'=> lang('bil_generator_disabled'),'message_khmer'=> html_entity_decode(lang('bil_generator_disabled_khmer')));
				}
			}else{
				$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
			}
		}else{
			$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));	
		}
		$this->response($result);
	}
	
	public function index_post()
	{
		
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		
		$phone_number = $this->input->post('phone_number');
		$name = $this->input->post('name');
		$warehouse_id = $this->input->post('warehouse_id');
		$table_id = $this->input->post('table_id');
		
		$this->form_validation->set_rules('phone_number', $this->lang->line("phone_number"), 'required');
		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		if ($this->form_validation->run() == true) {
			
			$devices_check = $this->site->devicesCheck($api_key);
				$settings = $this->site->get_setting();
				$socket_port = $settings->socket_port;
				$socket_host = $settings->socket_host;
				$socket_status = $settings->socket_enable;
				$bbq_enable = $this->customer_api->CheckTableForBBQorAlacarte($table_id);
				// $bbq_enable = $settings->bbq_enable;
			if($devices_check == $devices_key){
				$check_phone = $this->customer_api->Checknumber($phone_number);
				$customer_data = $this->customer_api->getcustomerusingphone($phone_number);
				//print_r($customer_data[0]->id);die;
				if($check_phone == TRUE){
					$data = $this->customer_api->GetuserByID($phone_number, $table_id);
					
					$check_nightaudit = $this->customer_api->Checknightaudit($warehouse_id);
					if(!$check_nightaudit){
						$result = array( 'status'=> 3 , 'message'=> lang('night_audit_not_complete'),'message_khmer'=> html_entity_decode(lang('night_audit_not_complete_khmer')));	
					}else{
						$covervalidate = $this->customer_api->customercheckbbqvalidate($customer_data[0]->id, $table_id);
						$request = $this->customer_api->orderRequestcheck($data->id, $table_id);
						$request = $this->customer_api->orderRequestcheck($data->id, $table_id);	
						if($request == FALSE){
							$covervalidate = 2;
					   }else{
							$covervalidate = $this->customer_api->customercheckbbqvalidate($customer_data[0]->id, $table_id);
						}
												
					    if($covervalidate != 3){		
						if($request == FALSE){
							$request_customer = $this->customer_api->orderRequestchecktablecustomer($table_id);
							if(!empty($request_customer)){
								
								if($data->id == $request_customer->customer_id){
									$result = array( 'status'=> 1 , 'message'=> lang('sucessfully_logged_in'),'message_khmer'=> html_entity_decode(lang('sucessfully_logged_in_khmer')),'socket_status'=>$socket_status,'socket_port'=>$socket_port,'socket_host'=>$socket_host, 'bbq_enable'=>$bbq_enable, 'data' => $data);
								}else{
									$result = array( 'status'=> 3 , 'message'=> lang('table_has_been_already_order_processing_another_customer_please_check_with_cashier_or_waiter'),'message_khmer'=> html_entity_decode(lang('table_has_been_already_order_processing_another_customer_please_check_with_cashier_or_waiter_khmer')));
								}
							}else{
								$result = array( 'status'=> 1 , 'message'=> lang('sucessfully_logged_in'),'message_khmer'=> html_entity_decode(lang('sucessfully_logged_in_khmer')),'socket_status'=>$socket_status,'socket_port'=>$socket_port,'socket_host'=>$socket_host, 'bbq_enable'=>$bbq_enable, 'data' => $data);	
							}
								
						}else{
							$result = array( 'status'=> 3 , 'message'=> lang('your_previous_bill_payment_is_pending_please_check_with_cashier_or_waiter'),'message_khmer'=> html_entity_decode(lang('your_previous_bill_payment_is_pending_please_check_with_cashier_or_waiter_khmer')));
						}
					   }else{
						$result = array( 'status'=> 4 , 'message'=> lang('bbq_cover_not_validated'),'message_khmer'=> html_entity_decode(lang('bbq_cover_not_validated_khmer')));
					   }	
					}
				}else{
					$insert = $this->customer_api->InsertCustomer($phone_number, $name);
					$data = $this->customer_api->GetuserByID($phone_number, $table_id);
					if(!empty($data)){
						$check_nightaudit = $this->customer_api->Checknightaudit($warehouse_id);
						if(!$check_nightaudit){
							$result = array( 'status'=> 3 , 'message'=> lang('night_audit_not_complete'),'message_khmer'=> html_entity_decode(lang('night_audit_not_complete_khmer')));	
						}else{
							$request = $this->customer_api->orderRequestchecktable($table_id);
							if($request == FALSE){
								$result = array( 'status'=> 1 , 'message'=> lang('sucessfully_logged_in'),'message_khmer'=> html_entity_decode(lang('sucessfully_logged_in_khmer')),'socket_status'=>$socket_status,'socket_port'=>$socket_port,'socket_host'=>$socket_host, 'bbq_enable'=>$bbq_enable, 'data' => $data);
							}else{
								$result = array( 'status'=> 3 , 'message'=> lang('table_has_been_already_order_processing_another_customer_please_check_with_cashier_or_waiter'),'message_khmer'=> html_entity_decode(lang('table_has_been_already_order_processing_another_customer_please_check_with_cashier_or_waiter_khmer')));
							}							
							
						}
					}else{
						$result = array( 'status'=> 3 , 'message'=> lang('data_not_insert'),'message_khmer'=> html_entity_decode(lang('data_not_insert_khmer')));
					}
				}				
			}else{
				$result = array( 'status'=> 3 , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
			}
			
		}else{
			$result = array( 'status'=> 3 , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));	
		}		
		$this->response($result);
		
	}
	
	
	public function newcustomer_post()
	{
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$phone_number = $this->input->post('phone_number');
		$name = $this->input->post('name');
		
		$this->form_validation->set_rules('phone_number', $this->lang->line("phone_number"), 'required');
		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		if ($this->form_validation->run() == true) {
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){
					$insert = $this->customer_api->InsertCustomer($phone_number, $name);
					$data = $this->customer_api->GetuserByID($phone_number);
					if($insert){
						$result = array( 'status'=> true , 'message'=> lang('your_customer_details_has_been_insert'),'message_khmer'=> html_entity_decode(lang('your_customer_details_has_been_insert_khmer')), 'data' => $data);
					}else{
						$result = array( 'status'=> false , 'message'=> lang('customer_details_not_insert'),'message_khmer'=> html_entity_decode(lang('customer_details_not_insert_khmer')));
					}
			}else{
				$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
			}
			
		}else{
			$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));	
		}		
		$this->response($result);
		
	}
	
	public function login_post()
	{
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$phone_number = $this->input->post('phone_number');
		$table_id = $this->input->post('table_id');
		$isbbqEnabled = $this->input->post('bbq_enabled');
		$this->form_validation->set_rules('phone_number', $this->lang->line("phone_number"), 'required');
		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		if ($this->form_validation->run() == true) {
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){
				$settings = $this->site->get_setting();
				$socket_port = $settings->socket_port;
				$socket_host = $settings->socket_host;
				$socket_status = $settings->socket_enable;
				$bbq_enable = $this->customer_api->CheckTableForBBQorAlacarte($table_id);
				// $bbq_enable = $settings->bbq_enable;
				$check_phone = $this->customer_api->Checknumber($phone_number);
				$LastBBQ_data = $this->customer_api->CheckLastBBQ_data($table_id,$phone_number);
				
				if($check_phone == TRUE){
					$BBQorder_request = $this->customer_api->CheckBBQorder_request($table_id,$phone_number);
					 // var_dump($BBQorder_request);die;
						if($BBQorder_request){
							

							$result = array( 'status'=> 4 , 'message'=> lang('bbq_cover_not_validated'),'message_khmer'=> html_entity_decode(lang('bbq_cover_not_validated_khmer')),'socket_status'=>$socket_status,'socket_port'=>$socket_port,'socket_host'=>$socket_host);
						   
						}else{
							$data = $this->customer_api->GetuserByID($phone_number,$table_id);							
							$request_customer = $this->customer_api->orderRequestchecktablecustomer($table_id);
							$customerbbqcoverentered = $this->customer_api->customerbbqcoverentered($table_id);
							/*var_dump($customerbbqcoverentered);
							var_dump($data->id);die;*/
							if($data->id == $customerbbqcoverentered->customer_id){

									if(($isbbqEnabled && is_array($LastBBQ_data))){
										$result = $LastBBQ_data;
									}else{
										$data = $this->customer_api->GetuserByID($phone_number,$table_id);							
								         $result = array( 'status'=> 1 , 'message'=> lang('sucessfully_logged_in'),'message_khmer'=> html_entity_decode(lang('sucessfully_logged_in_khmer')), 'data' => $data,'socket_status'=>$socket_status,'socket_port'=>$socket_port,'socket_host'=>$socket_host,'bbq_enable'=>$bbq_enable);
									}
							 }else{
							 	 if($customerbbqcoverentered == FALSE){
							 	 $data = $this->customer_api->GetuserByID($phone_number,$table_id);							
								         $result = array( 'status'=> 1 , 'message'=> lang('sucessfully_logged_in'),'message_khmer'=> html_entity_decode(lang('sucessfully_logged_in_khmer')), 'data' => $data,'socket_status'=>$socket_status,'socket_port'=>$socket_port,'socket_host'=>$socket_host,'bbq_enable'=>$bbq_enable);	
								     }else{
								    	$result = array( 'status'=> 3 , 'message'=> lang('table_has_been_already_order_processing_another_customer_please_check_with_cashier_or_waiter'),'message_khmer'=> html_entity_decode(lang('table_has_been_already_order_processing_another_customer_please_check_with_cashier_or_waiter_khmer')),'socket_status'=>$socket_status,'socket_port'=>$socket_port,'socket_host'=>$socket_host,'bbq_enable'=>$bbq_enable);
								    }
						    }
						}
				}else{
						
						$result = array( 'status'=> 2 , 'message'=> lang('your_phone_number_not_exits'),'message_khmer'=> html_entity_decode(lang('your_phone_number_not_exits_khmer')),'socket_status'=>$socket_status,'socket_port'=>$socket_port,'socket_host'=>$socket_host,'bbq_enable'=>$bbq_enable);
					}
				/*if(!$isbbqEnabled || ($isbbqEnabled && $LastBBQ_data && !is_array($LastBBQ_data))){ 

					if($check_phone == TRUE){
						$BBQorder_request = $this->customer_api->CheckBBQorder_request($table_id,$phone_number);
						if($BBQorder_request){
							$result = array( 'status'=> 4 , 'message'=> lang('bbq_cover_not_validated'),'message_khmer'=> html_entity_decode(lang('bbq_cover_not_validated_khmer')));
						}else{
							$data = $this->customer_api->GetuserByID($phone_number,$table_id);							
						    $result = array( 'status'=> 1 , 'message'=> lang('sucessfully_logged_in'),'message_khmer'=> html_entity_decode(lang('sucessfully_logged_in_khmer')), 'data' => $data);
						}
					}else{
						
						$result = array( 'status'=> 2 , 'message'=> lang('your_phone_number_not_exits'),'message_khmer'=> html_entity_decode(lang('your_phone_number_not_exits_khmer')));
					}
				}else{
						$result = $LastBBQ_data;
				}*/
			}else{
				$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
			}
			
		}else{
			$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));	
		}		
		$this->response($result);
		
	}
	
	public function orders_post(){
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		$table_id = $this->input->post('table_id');
		
		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		$this->form_validation->set_rules('user_id', $this->lang->line("user_id"), 'required');
		$this->form_validation->set_rules('warehouse_id', $this->lang->line("warehouse"), 'required');
		$this->form_validation->set_rules('table_id', $this->lang->line("table"), 'required');
		if ($this->form_validation->run() == true) {
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){	
				$data = $this->customer_api->GetAllorders($user_id, $warehouse_id,$table_id);
				
				if(!empty($data)){
					$result = array( 'status'=> true , 'message'=> lang('customer_order_data'),'message_khmer'=> html_entity_decode(lang('customer_order_data_khmer')), 'table_id' => $data[0]->table_id, 'table_name' => $data[0]->table_name, 'waiter_id' => $data[0]->waiter_id, 'chef_id' => $data[0]->chef_id, 'area_name' => $data[0]->area_name, 'session_started' => $data[0]->session_started, 'timezone' => $this->Settings->timezone_gmt, 'split_id' => $data[0]->split_id,  'data' => $data[0]->item);
				}else{
					$result = array( 'status'=> false , 'message'=> lang('customer_order_in_empty'),'message_khmer'=> html_entity_decode(lang('customer_order_in_empty_khmer')));
				}
			}else{
				$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
			}
		}else{
			$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));	
		}
		$this->response($result);
	}
	
	public function consolidatedorders_post(){
		
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		$table_id = $this->input->post('table_id');
		$order_type[] = 1;
		$order_type[] = 4;
		
		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		$this->form_validation->set_rules('user_id', $this->lang->line("user_id"), 'required');
		$this->form_validation->set_rules('warehouse_id', $this->lang->line("warehouse"), 'required');
		$this->form_validation->set_rules('table_id', $this->lang->line("table"), 'required');
		if ($this->form_validation->run() == true) {
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){	
				$data = $this->customer_api->GetAllSplitconsolidated($user_id, $order_type, $warehouse_id,$table_id);
				
				$check = array_unique($data[0]->check_order);
				foreach($check as $check_order){
					if($check_order == 1){
						$order_dine = 1;
					}elseif($check_order == 4){
						$order_bbq = 1;
					}
				}
				
				foreach($data[0]->item as $row){
					if($row->order_type == 4){
						$grand_total_cover = $row->grand_total_cover;
					}else{
						$grand_total[] = $row->grand_total;
					}
				}
				$grand_total[] = $grand_total_cover;
				$grand_total = array_sum($grand_total);
				
				$settings = $this->customer_api->getSettings();
				if(!empty($data)){
					$result = array( 'status'=> true , 'message'=> 'Customer Order  data', 'table_id' => $data[0]->table_id, 'table_name' => $data[0]->table_name, 'waiter_id' => $data[0]->waiter_id, 'chef_id' => $data[0]->chef_id, 'area_name' => $data[0]->area_name, 'session_started' => $data[0]->session_started, 'timezone' => $this->Settings->timezone_gmt, 'split_id' => $data[0]->split_id, 'order_dine' => $order_dine ? $order_dine : 0, 'order_bbq' => $order_bbq ? $order_bbq : 0,  'data' => $data[0]->item, 'grand_total' => $grand_total);
				}else{
					$result = array( 'status'=> false , 'message'=> lang('customer_order_in_empty'),'message_khmer'=> html_entity_decode(lang('customer_order_in_empty_khmer')));
				}
			}else{
				$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
			}
		}else{
			$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));	
		}
		
		$this->response($result);
		
		
	}
	
	public function notification_post(){
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		
		
		
		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		if ($this->form_validation->run() == true) {	
		 	$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){
				$data = $this->customer_api->Getnotification($group_id, $user_id, $warehouse_id);
				
				if(!empty($data)){
					$result = array( 'status'=> true , 'message'=> lang('notification_list'),'message_khmer'=> html_entity_decode(lang('notification_list_khmer')), 'count' => $data['notification_count'], 'data' => $data['notification_list']);
				}else{
					$result = array( 'status'=> false , 'message'=> lang('notification_list_empty'),'message_khmer'=> html_entity_decode(lang('notification_list_empty_khmer')));
				}
			}else{
				$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
			}
		}else{
			$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));	
		}
		$this->response($result);
	}
	
	public function notificationclear_post(){
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		
		$notification_id = $this->input->post('notification_id');
		
		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		if ($this->form_validation->run() == true) {	
		 	$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){
				$data = $this->customer_api->notification_clear($notification_id);	
				
				if(!empty($data)){
					$result = array( 'status'=> true , 'message'=> lang('notification_clear_success'),'message_khmer'=> html_entity_decode(lang('notification_clear_success_khmer')));
				}else{
					$result = array( 'status'=> false , 'message'=> lang('notification_clear_not_success'),'message_khmer'=> html_entity_decode(lang('notification_clear_not_success_khmer')));
				}
			}else{
				$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
			}
		}else{
			$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));	
		}
		$this->response($result);
	}
	
	public function popdiscount_post(){
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
				
		$order_dine = $this->input->post('order_dine');
		$order_bbq = $this->input->post('order_bbq');
		$split_id = $this->input->post('split_id');
		$table_id = $this->input->post('table_id');
		
		$waiter_id = $this->site->getWaiter($split_id);
		$customer_discount =0;
		$this->form_validation->set_rules('warehouse_id', $this->lang->line("warehouse_id"), 'required');
		$this->form_validation->set_rules('user_id', $this->lang->line("user_id"), 'required');
		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		if ($this->form_validation->run() == true) {
			
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){	
			
				$settings = $this->customer_api->getSettings();
				
				if($settings->discount_request_customer_app != 0){
					if($settings->customer_discount == 'automanual'){
						$customer_discount = $this->site->GetAllcostomerDiscounts();
					}
				}
				
				
				//$customer_discount = $this->site->GetAllcostomerDiscounts();
				if($settings->bbq_discount == 'automanual'){
					$bbq_discount = $this->site->GetAllBBQDiscounts();
				}
				
				$is_unique = $this->site->is_uniqueDiscountExist();
				if(!empty($is_unique)){
					$automatic = 1;
					$item_data = $this->customer_api->getBil($table_id, $split_id, $waiter_id);
					
					foreach($item_data['items'] as $item){
						$item->id = $item->recipe_id;
						$simple_discount[] = $this->site->CalculatesimpleDiscount($item);
							
					}
					$automatic_discount = array_sum($simple_discount);
					
				}else{
					
					$automatic = 0;
					$automatic_discount = 0;
				}
				
				
				$result = array( 'status'=> true , 'message'=> lang('biller_request_has_been_success_please_check_cashier'),'message_khmer'=> html_entity_decode(lang('biller_request_has_been_success_please_check_cashier_khmer')), 'order_dine' => $order_dine ? $order_dine : 0, 'order_bbq' => $order_bbq ? $order_bbq : 0, 'settings' => $settings ? $settings : 0, 'customer_discount' => $customer_discount ? $customer_discount : 0, 'bbq_discount' => $bbq_discount ? $bbq_discount : 0, 'automatic' => $automatic, 'automatic_discount' => $automatic_discount, 'is_unique' => $is_unique);
				
			
			}else{
				$result = array( 'status'=> false , 'message'=> lang('bil_generator_not_success'),'message_khmer'=> html_entity_decode(lang('bil_generator_not_success_khmer')));	
			}
			
		}else{
			$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));	
		}
		
		$this->response($result);
	}
	
	
	
	public function billrequest_post()
	{
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		
		$order_type = $this->input->post('order_type');
		$bill_type = $this->input->post('bill_type');
		$table_id = $this->input->post('table_id');
		$split_id = $this->input->post('split_id');
		$bils = $this->input->post('bils');
		
		$order_dine = $this->input->post('order_dine');
		$order_bbq = $this->input->post('order_bbq');
		
		$table_name = $this->customer_api->getTablename($table_id);
		
		$customer_type_val = $order_dine == 1 ? $this->input->post('customer_type_val') : '';
		$customer_discount_val =  $order_dine == 1 ?  $this->input->post('customer_discount_val') : '';
		
		$bbq_type_val = $order_bbq == 1 ? $this->input->post('bbq_type_val') : '';
		$bbq_discount_val = $order_bbq == 1 ? $this->input->post('bbq_discount_val') : '';
		
		
		$waiter_id = $this->site->getWaiter($split_id);
		

		$this->form_validation->set_rules('order_type', $this->lang->line("order_type"), 'required');
		$this->form_validation->set_rules('bill_type', $this->lang->line("bill_type"), 'required');
		$this->form_validation->set_rules('table_id', $this->lang->line("table_id"), 'required');
		$this->form_validation->set_rules('split_id', $this->lang->line("split_id"), 'required');
		$this->form_validation->set_rules('bils', $this->lang->line("bils"), 'required');
		$this->form_validation->set_rules('warehouse_id', $this->lang->line("warehouse_id"), 'required');
		$this->form_validation->set_rules('user_id', $this->lang->line("user_id"), 'required');
		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		if ($this->form_validation->run() == true) {
			$setting = $this->customer_api->getSettings();
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){	
				
				if($setting->order_request_stewardapp == 0){
					
					$split_status = $this->site->check_splitid_is_bill_generated($split_id);
					if($split_status == FALSE){
						
						
						$data = $this->requestwithoutsteward($user_id, $group_id, $warehouse_id, $order_type, $bill_type, $table_id, $split_id, $bils, $order_dine, $order_bbq, $table_name, $customer_type_val, $customer_discount_val, $bbq_type_val, $bbq_discount_val, $waiter_id);
						
						if($order_dine == 1 && $order_bbq == 1 && ($data['c_discount'] || $data['b_discount'])){
							
							$discount_message = 'Dear Customer, Your are eglible for '.$data['c_discount'].' and '.$data['b_discount'].'. Subjected to approval by Management.';
							
						}elseif($order_dine == 1 && $order_bbq == 0 && ($data['c_discount'] || $data['b_discount'])){
							$discount_message = 'Dear Customer, Your are eglible for '.$data['c_discount'].'. Subjected to approval by Management.';
						}elseif($order_dine == 0 && $order_bbq == 1 && ($data['c_discount'] || $data['b_discount'])){
							$discount_message = 'Dear Customer, Your are eglible for '.$data['b_discount'].'. Subjected to approval by Management';
						}else{
							$discount_message = 'Biller Request has been success.';
						}
						if($this->site->isSocketEnabled()){
							if($order_bbq==1){
								$this->site->socket_refresh_bbqtables($table_id);	
							}else{
								$this->site->socket_refresh_tables($table_id);	
							}
						}
						
						$result = array( 'status'=> true , 'message'=> lang('biller_request_has_been_success_please_check_cashier'),'message_khmer'=> html_entity_decode(lang('biller_request_has_been_success_please_check_cashier_khmer')), 'data' => $data, 'discount_message' => $discount_message, 'bill_status' => 0);
					}else{
						
						$result = array( 'status'=> false , 'message'=> lang('already_bil_generator_please_check_cashier_or_waiter'),'message_khmer'=> html_entity_decode(lang('already_bil_generator_please_check_cashier_or_waiter_khmer')), 'discount_message' => 'empty', 'bill_status' => 1);
					}
					
				}else{
					
					$data = $this->requestwithsteward($user_id, $group_id, $warehouse_id, $order_type, $bill_type, $table_id, $split_id, $bils, $order_dine, $order_bbq, $table_name, $customer_type_val, $customer_discount_val, $bbq_type_val, $bbq_discount_val, $waiter_id);
					
					if($order_dine == 1 && $order_bbq == 1 && ($data['c_discount'] || $data['b_discount'])){
							
							$discount_message = 'Dear Customer, Your are eglible for '.$data['c_discount'].' and '.$data['b_discount'].'. Subjected to approval by Management.';
							
						}elseif($order_dine == 1 && $order_bbq == 0 && ($data['c_discount'] || $data['b_discount'])){
							$discount_message = 'Dear Customer, Your are eglible for '.$data['c_discount'].'. Subjected to approval by Management.';
						}elseif($order_dine == 0 && $order_bbq == 1 && ($data['c_discount'] || $data['b_discount'])){
							$discount_message = 'Dear Customer, Your are eglible for '.$data['b_discount'].'. Subjected to approval by Management';
						}else{
							$discount_message = 'Biller Request has been success.';
						}
						
					$result = array( 'status'=> true , 'message'=> lang('biller_request_has_been_success_please_check_cashier'),'message_khmer'=> html_entity_decode(lang('biller_request_has_been_success_please_check_cashier_khmer')), 'data' => $data, 'discount_message' =>$discount_message, 'bill_status' => 0);
				}
				
					
				
			}else{
				$result = array( 'status'=> false , 'message'=> lang('bil_generator_not_success'),'message_khmer'=> html_entity_decode(lang('bil_generator_not_success_khmer')), 'discount_message' => 'discount empty', 'bill_status' => 0);	
			}
			
		}else{
			$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')), 'discount_message' => 'discount empty', 'bill_status' => 0);	
		}
		
		$this->response($result);
		
	}
	
	function customerdiscount($waiter_id, $table_id, $split_id, $customer_discount_id){
		
		$customer_discount_value = $this->site->GetIDBycostomerDiscounts($customer_discount_id);
		$discount_value = $customer_discount_value ? $customer_discount_value : 0;
		$item_data = $this->customer_api->customergetBil($table_id, $split_id, $user_id);
		foreach($item_data['items'] as $item_row){
			foreach($item_row as $item){
				$order_item[] = $item;
				$total_price[] = $item->unit_price;
			}
		}
		
		$total_price = array_sum($total_price);
		$discount_value = $this->site->calculate_Discount($discount_value, $total_price);
		if($discount_value){
			return $discount_value;
		}
		return false;		
		
	}
	
	function bbqdiscount($bbq_discount_id){
		
		 $bbq_discount_value = $this->site->GetIDByBBQDiscounts($bbq_discount_id);
		if($bbq_discount_value){
			return $bbq_discount_value;
		}
		return false;		
			
	}
	
	function requestwithsteward($user_id, $group_id, $warehouse_id, $order_type, $bill_type, $table_id, $split_id, $bils, $order_dine, $order_bbq, $table_name, $customer_type_val, $customer_discount_val, $bbq_type_val, $bbq_discount_val, $waiter_id){

		$request_discount = array(
			'customer_id' => $user_id,
			'waiter_id' => $waiter_id,
			'table_id' => $table_id,
			'split_id' => $split_id,
			'customer_type_val' => $customer_type_val ? $customer_type_val : '',
			'customer_discount_val' => $customer_discount_val ? $customer_discount_val : '',
			'bbq_type_val' => $bbq_type_val ? $bbq_type_val : '',
			'bbq_discount_val' => $bbq_discount_val ? $bbq_discount_val : '',
			'created_on' => date('Y-m-d H:i:s')
		);
		
		$item_data = $this->customer_api->getBil($table_id, $split_id, $waiter_id);
		
		foreach($item_data['items'] as $item){
			$order_item[] = $item;
			$bil_total[] = $item->subtotal;
			
			$discount = $this->site->discountMultiple($item->recipe_id);
				
			if(!empty($discount)){
												   
				if($discount[2] == 'percentage_discount'){
				  $discount_value = $discount[1].'%';
				}else{
					$discount_value =$discount[1];
				}
				 $item_discount1 = $this->site->calculateDiscount($discount_value, $item->subtotal);
				 $total_dis[] = $item_discount1;
			}else{
				 $item_discount1 = 0;
				 $total_dis[] = 0;
			}
				
				
		}
		
		$TotalDiscount = $this->site->TotalDiscount();
		if(!empty($TotalDiscount)){
			$offer_discount =  $TotalDiscount[1];
			$offer_discount_id =  $TotalDiscount[0];
		}else{
			$offer_discount = 0;
			$offer_discount_id =  0;
		}
		$final_bil = array_sum($bil_total) - array_sum($total_dis);
		$step_bil_1  = array_sum($bil_total) - array_sum($total_dis);
				
		$other_discount = '0%';
		$final_bil =  $final_bil - $TotalDiscount[1];
		$step_bil_2 = $step_bil_1 - $TotalDiscount[1];
		
		$other_discount_total = $this->site->calculateDiscount($other_discount, $step_bil_2);
		$total_discount =  $other_discount_total + array_sum($total_dis) + $offer_discount;
		$final_bil = $final_bil - $other_discount_total;
		$step_bil_3 = $step_bil_2 - $other_discount_total;
		
		
		$total_tax = $this->site->calculateOrderTax( $this->Settings->default_tax, $final_bil);
		//$final_bil = $final_bil + $total_tax;
		$final_bil = $final_bil;
		//$step_bil_4 = $step_bil_3 + $total_tax;
		$step_bil_4 = $step_bil_3;
		
		foreach($item_data['order'] as $order){
			$order_data = array('sales_type_id' => $order->order_type,
				'sales_split_id' => $order->split_id,
				'sales_table_id' => $order->table_id,
				'date' => date('Y-m-d H:i:s'),
				'reference_no' => 'SALES-'.date('YmdHis'),
				'customer_id' => $order->customer_id,
				'customer' => $order->customer,
				'biller_id' => $order->biller_id,
				'biller' => $order->biller,
				'warehouse_id' => $order->warehouse_id,
				'note' => $order->note,
				'staff_note' => $order->staff_note,
				'sale_status' => 'Process',
				'hash'      => hash('sha256', microtime() . mt_rand()),
			);
			$customer_id = $this->site->getOrderCustomer($order->id);
		}
		
		$delivery_person = $this->input->post('delivery_person_id') ? $this->input->post('delivery_person_id') : 0;
		
		$bil_value = $this->input->post('bils');
		
		for($i=1; $i<=$this->input->post('bils'); $i++){
			
			$total = array_sum($bil_total);
			$bil_total_count = count($item_data['items']);
			
			foreach($item_data['order'] as $order){
				$billData[$i] = array(
					'date' => date('Y-m-d H:i:s'),
					'customer_id' => $order->customer_id,
					'customer' => $order->customer,
					'biller_id' => $order->biller_id,
					'biller' => $order->biller,
					'reference_no' => 'SALES-'.date('YmdHis'),
					'total_items' => $bil_total_count,
					'total' => $total/$bil_value,
					'total_tax' => $total_tax/$bil_value,
					'tax_id' => $this->Settings->default_tax,
					'total_discount' => $total_discount/$bil_value,
					'grand_total' => $final_bil/$bil_value,
					'round_total' => $final_bil/$bil_value,
					'order_discount_id' => $offer_discount_id,
					'customer_discount_id' =>$customer_discount_id,
					'customer_discount_status' => 'pending',
					'bilgenerator_type'	=> 1,
					'bill_type' => $bill_type,
					'delivery_person_id' => $delivery_person,
					'warehouse_id' => $warehouse_id,
				);
			}
			foreach($item_data['items'] as $item){
				
				$discount = $this->site->discountMultiple($item->recipe_id);
				
				if(!empty($discount)){
													   
					if($discount[2] == 'percentage_discount'){
					  $discount_value = $discount[1].'%';
					}else{
						$discount_value =$discount[1];
					}
					 $item_discount = $this->site->calculateDiscount($discount_value, $item->subtotal);
				}else{
					 $item_discount = 0;
				}
				
				$off_discount = $this->site->calculate_Discount($offer_discount, ($item->subtotal - $item_discount), $step_bil_1);
				$input_discount = $this->site->calculate_Discount($other_discount_total, ($item->subtotal - $item_discount - $off_discount), $step_bil_2);
				
				$itemtax = $this->site->calculateOrderTax($this->input->post('tax_id'), ($item->subtotal - $off_discount - $input_discount - $item_discount));
		
				
				$splitData[$i][] = array(
					'recipe_name' => $item->recipe_name,
					'unit_price' => $item->unit_price/$bil_value,
					'net_unit_price' => $item->net_unit_price/$bil_value,
					'warehouse_id' => $warehouse_id,
					'recipe_type' => $item->recipe_type,
					'quantity' => $item->quantity,
					'recipe_id' => $item->recipe_id,
					'recipe_code' => $item->recipe_code,
					'discount' => $discount[0],						
					'item_discount' => $item_discount/$bil_value,
					'off_discount' => $off_discount ? $off_discount/$bil_value : 0,
					'input_discount' => $input_discount ? $input_discount/$i : 0,
					'tax' => $itemtax ? $itemtax/$bil_value : 0,	
					'subtotal' => $item->subtotal/$bil_value,
				);
			}
				
		}
		
		$sales_total = array_column($billData, 'grand_total');
		$sales_total = array_sum($sales_total);
		
		$grand_total = $sales_total;
		$notification_array['from_role'] = $group_id;
		
		 $notification_array['customer_role'] = WAITER;
		 $notification_array['customer_msg'] = 'Customer has been bil generator to '.$split_id;
		 $notification_array['customer_type'] = 'Bil generator ('.$split_id.')';
		 $notification_array['customer_id'] = $waiter_id;
		
		$notification_array['insert_array'] = array(
			'msg' => 'Customer has requested for bill  '.$split_id.' from '.$table_name,
			'type' => 'Bill Request',
			'table_id' =>  $table_id,
			'role_id' => 8,
			'user_id' => $waiter_id,
			'to_user_id' => $waiter_id,
			'warehouse_id' => $warehouse_id,
			'created_on' => date('Y-m-d H:m:s'),
			'is_read' => 0,
			'respective_steward'=>$waiter_id,
			'split_id'=>$split_id,
			'tag'=>'bill-request',
		);
		
		$device_token = $this->customer_api->deviceGET($waiter_id);
		$deviceDetails = $this->customer_api->deviceDetails($waiter_id);
		
		
		$data = $this->customer_api->requestBill($timelog_array, $notification_array, $grand_total, $split_id, $table_id, $request_discount);

		 $t = $this->db->get_where('restaurant_tables',array('id'=>$table_id))->row();
		 $type   = $t->sale_type;

		$notifyID = $this->site->create_notification($notification_array);
		$request_type = 1;
		foreach($deviceDetails as $k => $device){
			$title = 'Bill Request';
			$message = 'Customer has requested for bill  '.$split_id.' from '.$table_name;
			$push_data = $this->push->setPush($title,$message);
			if($this->site->isSocketEnabled() && $push_data == true && isset($device->socket_id) && $device->socket_id!=''){
				$json_data = '';
				$response_data = '';
				$json_data = $this->push->getPush();
				$regId_data = $device->device_token;
				$socket_id = $device->socket_id;
				
				//$response_data = $this->firebase->send($regId_data, $json_data);
				$this->site->send_billRequestpushNotification($title,$message,$socket_id,$split_id,$table_id,$notifyID,$request_type,$type);
				//file_put_contents('notify_values66.txt',$title.$message,FILE_APPEND);
			}
		}
		if($this->site->isSocketEnabled()){//file_put_contents('notify_values33.txt',$notifyID,FILE_APPEND);
			    $emit_notification['split_id'] = $split_id;
			    $emit_notification['request_type'] = $request_type;
			    $this->socketemitter->setEmit('bill_request', $emit_notification);
		}
		if($data){
			return $data;	
		}
		return false;
	}
	
	function requestwithoutsteward($user_id, $group_id, $warehouse_id, $order_type, $bill_type, $table_id, $split_id, $bils, $order_dine, $order_bbq, $table_name, $customer_type_val, $customer_discount_val, $bbq_type_val, $bbq_discount_val, $waiter_id){
		
		
		$bbq_order_type = 4;
		$dine_order_type = 1;
		
		$possettings = $this->customer_api->getPOSSettings();
		$settings = $this->customer_api->getSettings();
		$customer_discount = $this->site->GetAllcostomerDiscounts();
		$tax_rates = $this->site->getAllTaxRates();		
		
		
		
				if($order_bbq == 1){
					
								if($order_dine == 1 && $order_bbq == 1){
									$consolidated = 1;
								}else{
									$consolidated = 0;
								}
					if(!empty($bbq_order_type)){
							$bbq_discount = $this->site->GetAllBBQDiscounts();
							$bbq_order_id = $this->customer_api->getBBQorderID($split_id);
							$current_days = date('l');
							$buyxgetx = $this->site->getBBQbuyxgetxDAYS($current_days);	
							$order_bbq = $this->customer_api->BBQtablesplit($table_id, $split_id);	
							
							if(!empty($order_bbq)){
							
								$adult = $this->site->CalculationBBQbuyget($buyxgetx->adult_buy, $buyxgetx->adult_get, $order_bbq->number_of_adult);
								$child = $this->site->CalculationBBQbuyget($buyxgetx->child_buy, $buyxgetx->child_get, $order_bbq->number_of_child);
								$kids = $this->site->CalculationBBQbuyget($buyxgetx->kids_buy, $buyxgetx->kids_get, $order_bbq->number_of_kids);
							
								$adult_subprice = ($order_bbq->adult_price * $order_bbq->number_of_adult) - ($order_bbq->adult_price * $adult);
								
								$bbq_covers[] = array(
									'adult_price' => $order_bbq->adult_price,
									'number_of_adult' => $order_bbq->number_of_adult,
									'adult_days' => $current_days,
									'adult_price' => $order_bbq->adult_price,
									'adult_buyx' => $buyxgetx->adult_buy ? $buyxgetx->adult_buy : 0,
									'adult_getx' => $buyxgetx->adult_get ? $buyxgetx->adult_get : 0,
									'adult_discount_cover' => $adult,
									'adult_subprice' => $adult_subprice
									
								);
								
								$child_subprice = ($order_bbq->child_price * $order_bbq->number_of_child) - ($order_bbq->child_price * $child);
								
								$bbq_covers[] = array(
									'child_price' => $order_bbq->child_price,
									'number_of_child' => $order_bbq->number_of_child,
									'child_days' => $current_days,
									'child_price' => $order_bbq->child_price,
									'child_buyx' => $buyxgetx->child_buy ? $buyxgetx->child_buy : 0,
									'child_getx' => $buyxgetx->child_get ? $buyxgetx->child_get : 0,
									'child_discount_cover' => $child,
									'child_subprice' => $child_subprice
									
								);
								
								$kids_subprice = ($order_bbq->kids_price * $order_bbq->number_of_kids) - ($order_bbq->kids_price * $kids);
								
								$bbq_covers[] = array(
									'kids_price' => $order_bbq->kids_price,
									'number_of_kids' => $order_bbq->number_of_kids,
									'kids_days' => $current_days,
									'kids_price' => $order_bbq->kids_price,
									'kids_buyx' => $buyxgetx->kids_buy ? $buyxgetx->kids_buy : 0,
									'kids_getx' => $buyxgetx->kids_get ? $buyxgetx->kids_get : 0,
									'kids_discount_cover' => $kids,
									'kids_subprice' => $kids_subprice
									
								);
							}
							
							$order_bbq->total_cover = $order_bbq->number_of_adult + $order_bbq->number_of_child + $order_bbq->number_of_kids;
							$order_bbq->total_amount =  ($adult_subprice) + ($child_subprice) + ($kids_subprice);
							
							
						}							
					if(!empty($bbq_order_type)){
							
							for($i=0; $i<$bils; $i++){
								
								$number_of_covers = $order_bbq->total_cover;
								
								if($number_of_covers != 0){
									
									 $bbq_discounts = $this->bbqdiscount($bbq_discount_val);
									
									$bbq_discountamount = $this->site->calculate_Discount($bbq_discounts, $order_bbq->total_amount);
									
									$final_bil1 = $order_bbq->total_amount - $bbq_discountamount;
									$bbq_discount_amount[] = $bbq_discountamount;
									
									
									$total_tax = $this->site->calculateOrderTax( $possettings->default_tax, $final_bil1);
									$final_bil1 = $final_bil1 + $total_tax;
									
									
									$tax_amount[] = $total_tax;
									$total_amount[] = $order_bbq->total_amount;
									$gtotal[] = $final_bil1;
									
									$adult_price[] = $order_bbq->adult_price;
									$number_of_adult[] = $order_bbq->number_of_adult;
									$adult_subprice[] = $order_bbq->adult_price * $order_bbq->number_of_adult;
									
									$child_price[] = $order_bbq->child_price;
									$number_of_child[] = $order_bbq->number_of_child;
									$child_subprice[] = $order_bbq->child_price * $order_bbq->number_of_child;
									
									$kids_price[] = $order_bbq->kids_price;
									$number_of_kids[] = $order_bbq->number_of_kids;
									$kids_subprice[] = $order_bbq->kids_price * $order_bbq->number_of_kids;
									
									$number_covers[] = $number_of_covers;
									
									$adult_discount_cover[] = $order_bbq->number_of_adult;
									$child_discount_cover[] = $order_bbq->number_of_child;
									$kids_discount_cover[] = $order_bbq->number_of_kids;
									
								}
							}
							
							
							$bbq_discount_amount = array_sum($bbq_discount_amount);
							$tax_amount = array_sum($tax_amount);
							$total_amount = array_sum($total_amount);
							$gtotal = array_sum($gtotal);
							
							$adult_price = array_sum($adult_price);
							$number_of_adult = array_sum($number_of_adult);
							$adult_subprice = array_sum($adult_subprice);
							
							$child_price = array_sum($child_price);
							$number_of_child = array_sum($number_of_child);
							$child_subprice = array_sum($child_subprice);
							
							$kids_price = array_sum($kids_price);
							$number_of_kids = array_sum($number_of_kids);
							$kids_subprice = array_sum($kids_subprice);
							
							$number_of_covers = array_sum($number_covers);
							
							$adult_discount_cover = array_sum($adult_discount_cover);
							$child_discount_cover = array_sum($child_discount_cover);
							$kids_discount_cover = array_sum($kids_discount_cover);
							
							$bbq_array = array(
								'number_of_adult' => $number_of_adult,
								'number_of_child' => $number_of_child,
								'number_of_kids' => $number_of_kids
							);
							
							$item_data_bbq = $this->customer_api->BBQgetBil($table_id, $split_id, $waiter_id);
							
						
							foreach($item_data_bbq['items'] as $row_order){
								foreach($row_order as $item){
								
								$saleorder_item[] = $item;
								$bil_total[] = $item->subtotal;
								
								$discount = $this->site->discountMultiple($item->recipe_id);
									
								if(!empty($discount)){
																	   
									if($discount[2] == 'percentage_discount'){
									  $discount_value = $discount[1].'%';
									}else{
										$discount_value =$discount[1];
									}
									 $item_discount1 = $this->site->calculateDiscount($discount_value, $item->subtotal);
									 $total_dis[] = $item_discount1;
								}else{
									 $item_discount1 = 0;
									 $total_dis[] = 0;
								}
							}
								
							}
							$TotalDiscount = $this->site->TotalDiscount();
							if(!empty($TotalDiscount)){
								$offer_discount =  $TotalDiscount[1];
								$offer_discount_id =  $TotalDiscount[0];
							}else{
								$offer_discount = 0;
								$offer_discount_id =  0;
							}
							$final_bil = array_sum($bil_total) - array_sum($total_dis);
							$step_bil_1  = array_sum($bil_total) - array_sum($total_dis);
							if($bbq_type_val == 'automanual')
							{
								
								$bbq_discounts = $this->bbqdiscount($bbq_discount_val);
								$other_discount = $bbq_discounts;
							}else{
								$bbq_discounts = $this->bbqdiscount($bbq_discount_val);
								$other_discount = $bbq_discounts;
							}
							$final_bil =  $final_bil - $TotalDiscount[1];
							$step_bil_2 = $step_bil_1 - $TotalDiscount[1];
							
							$other_discount_total = $this->site->calculateDiscount($other_discount, $step_bil_2);
							$total_discount =  $other_discount_total + array_sum($total_dis) + $offer_discount;
							$final_bil = $final_bil - $other_discount_total;
							$step_bil_3 = $step_bil_2 - $other_discount_total;
							
							$total_tax = $this->site->calculateOrderTax( $possettings->default_tax, $final_bil);
							$final_bil = $final_bil;
							$step_bil_4 = $step_bil_3;
							
							foreach($item_data_bbq['order'] as $order){
								$order_data_bbq = array('sales_type_id' => $order->order_type,
									'sales_split_id' => $order->split_id,
									'sales_table_id' => $order->table_id,
									'date' => date('Y-m-d H:i:s'),
									'reference_no' => 'SALES-'.date('YmdHis'),
									'customer_id' => $order->customer_id,
									'customer' => $order->customer,
									'biller_id' => $order->biller_id,
									'biller' => $order->biller,
									'warehouse_id' => $order->warehouse_id,
									'note' => $order->note ? $order->note : '',
									'staff_note' => $order->staff_note ? $order->staff_note : '',
									'sale_status' => 'Process',
									'hash'      => hash('sha256', microtime() . mt_rand()),
									'consolidated' => 1
								);
								
								$sale = array(
									'bilgenerator_type' => 0,
									'sales_type_id' => 4,
									'sales_split_id' => $split_id,
									'sales_table_id' => $table_id,
									'date' => date('Y-m-d H:i:s'),
									'reference_no' => 'SALE'.date('YmdHis'),
									'customer_id' => $order->customer_id,
									'customer' => $order->customer,
									'biller_id' => $order->biller_id,
									'biller' => $order->biller,
									'warehouse_id' => $warehouse_id, 
									'total' => $total_amount, 
									'order_discount_id' => $bbq_discount_val, 
									'total_discount' => $bbq_discount_amount,
									'order_tax_id' => $possettings->default_tax,
									'total_tax' => $tax_amount, 
									'grand_total' => $gtotal,
									'total_items' => $number_of_covers,
									'sale_status' => 'Process',
									'consolidated' => $consolidated
								);
								
								$bilsdata_bbq[0] = array(
									'bilgenerator_type' => 0,
									'date' => date('Y-m-d H:i:s'),
									'reference_no' => 'SALE'.date('YmdHis'),
									'customer_id' => $order->customer_id,
									'customer' => $order->customer,
									'biller_id' => $order->biller_id,
									'biller' => $order->biller,
									'warehouse_id' => $warehouse_id, 
									'total' => $total_amount, 
									'order_discount_id' => $bbq_discount_val, 
									'total_discount' => $bbq_discount_amount,
									'tax_id' => $possettings->default_tax,
									'total_tax' => $tax_amount, 
									'tax_type' => $possettings->tax_type,
									'grand_total' => $gtotal,
									'total_items' => $number_of_covers,
									'consolidated' => $consolidated
								);
							
								
							}
							
						
								$sale_items[] = array(
									'type' => 'adult',
									'cover' => $bbq_covers[0]['number_of_adult'],
									'price' => $bbq_covers[0]['adult_price'],
									'days' => $bbq_covers[0]['adult_days'],
									'buyx' => $bbq_covers[0]['adult_buyx'],
									'getx' => $bbq_covers[0]['adult_getx'],
									'discount_cover' => $bbq_covers[0]['adult_discount_cover'],
									'subtotal' => $bbq_covers[0]['adult_subprice'],
									'created' => date('Y-m-d H:i:s'),
								);
								
								$sale_items[] = array(
									'type' => 'child',
									'cover' => $bbq_covers[1]['number_of_child'],
									'price' => $bbq_covers[1]['child_price'],
									'days' => $bbq_covers[1]['child_days'],
									'buyx' => $bbq_covers[1]['child_buyx'],
									'getx' => $bbq_covers[1]['child_getx'],
									'discount_cover' => $bbq_covers[1]['child_discount_cover'],
									'subtotal' => $bbq_covers[1]['child_subprice'],
									'created' => date('Y-m-d H:i:s'),
								);
								
								$sale_items[] = array(
									'type' => 'kids',
									'cover' => $bbq_covers[2]['number_of_kids'],
									'price' => $bbq_covers[2]['kids_price'],
									'days' => $bbq_covers[2]['kids_days'],
									'buyx' => $bbq_covers[2]['kids_buyx'],
									'getx' => $bbq_covers[2]['kids_getx'],
									'discount_cover' => $bbq_covers[2]['kids_discount_cover'],
									'subtotal' => $bbq_covers[2]['kids_subprice'],
									'created' => date('Y-m-d H:i:s'),
								);
							
							
							$bil_value = $bils;
						
						for($i=0; $i<$bils; $i++){
							
							$total = array_sum($bil_total);
							$bil_total_count = count($item_data['items']);
							
							foreach($item_data_bbq['order'] as $order){
								$Data_bbq[$i] = array(
									'date' => date('Y-m-d H:i:s'),
									'customer_id' => $order->customer_id,
									'customer' => $order->customer,
									'biller_id' => $order->biller_id,
									'biller' => $order->biller,
									'reference_no' => 'SALES-'.date('YmdHis'),
									'total_items' => $bil_total_count,
									'total' => $total/$bil_value,
									'total_tax' => $total_tax/$bil_value,
									'tax_id' => $this->input->post('default_tax'),
									'total_discount' => $total_discount/$bil_value,
									'grand_total' => $final_bil/$bil_value,
									'round_total' => $final_bil/$bil_value != NULL ?  $final_bil/$bil_value : 0,
									'order_discount_id' => $offer_discount_id,
									'bill_type' => $bill_type != NULL ? $bill_type : 4,
									'delivery_person_id' => $delivery_person,
									'warehouse_id' => $warehouse_id,
									'consolidated' => 1
								);
								
								$customer_id = $order->customer_id;
								$customer = $order->customer;
								$biller_id = $order->biller_id;
								$biller = $order->biller;
								
								
								
							}
							
							
							
							foreach($item_data_bbq['items'][$i] as $item){
								
								$discount = $this->site->discountMultiple($item->recipe_id);
								
								if(!empty($discount)){
																	   
									if($discount[2] == '1'){
									  $discount_value = $discount[1].'%';
									}else{
										$discount_value =$discount[1];
									}
									 $item_discount = $this->site->calculateDiscount($discount_value, $item->subtotal);
								}else{
									 $item_discount = 0;
								}
								
								$off_discount = $this->site->calculate_Discount($offer_discount, ($item->subtotal - $item_discount), $step_bil_1);
								$input_discount = $this->site->calculate_Discount($other_discount_total, ($item->subtotal - $item_discount - $off_discount), $step_bil_2);
								
								$itemtax = $this->site->calculateOrderTax($possettings->default_tax, ($item->subtotal - $off_discount - $input_discount - $item_discount));
				
							
								$splitData_bbq[$i][] = array(
									'recipe_name' => $item->recipe_name,
									'unit_price' => $item->unit_price/$bil_value,
									'net_unit_price' => $item->net_unit_price/$bil_value,
									'warehouse_id' => $warehouse_id,
									'recipe_type' => $item->recipe_type,
									'quantity' => $item->quantity,
									'recipe_id' => $item->recipe_id,
									'recipe_code' => $item->recipe_code,
									'discount' => $discount[0],						
									'item_discount' => $item_discount/$bil_value,
									'off_discount' => $off_discount ? $off_discount/$bil_value : 0,
									'input_discount' => $input_discount ? $input_discount/$bil_value : 0,
									'tax' => $itemtax ? $itemtax/$bil_value : 0,	
									'subtotal' => ($item->subtotal/$bil_value - $input_discount/$bil_value) + $itemtax/$bil_value  ,
								);
								
								$j++;
							}
								
						}
						
						
						$sales_total = array_column($billData_bbq, 'grand_total');
						$sales_total = array_sum($sales_total);
						
						$notification_array['from_role'] = $group_id;
						$notification_array['insert_array'] = array(
							'msg' => 'Waiter has been bil generator to '.$split_id,
							'type' => 'Bil generator ('.$split_id.')',
							'table_id' =>  $table_id,
							'role_id' => 8,
							'user_id' => $waiter_id,	
							'warehouse_id' => $warehouse_id,
							'created_on' => date('Y-m-d H:m:s'),
							'is_read' => 0
						);
						
							for($i=0; $i<$bils; $i++){
								
								
								
							$bbq_discounts = $this->bbqdiscount($bbq_discount_val);
							$adult_discount = $this->site->calculateDiscount($bbq_discounts, $bbq_covers[0]['adult_subprice']);
							$adult_disfinal = $bbq_covers[0]['adult_subprice'] - $adult_discount;
							$adult_tax_id = $possettings->default_tax;
							$adult_tax_type = $possettings->tax_type;
							$adult_tax = $this->site->calculateOrderTax( $possettings->default_tax, $adult_disfinal);
							
							$bil_items[$i][] = array(
								'type' => 'adult',
								'cover' => $bbq_covers[0]['number_of_adult'],
								'price' => $bbq_covers[0]['adult_price'],
								'days' => $bbq_covers[0]['adult_days'],
								'buyx' => $bbq_covers[0]['adult_buyx'],
								'getx' => $bbq_covers[0]['adult_getx'],
								'discount_cover' => $bbq_covers[0]['adult_discount_cover'],
								'discount' => $adult_discount,
								'tax_id' => $adult_tax_id,
								'tax_type' => $adult_tax_type,
								'tax' => $adult_tax,
								'subtotal' => $bbq_covers[0]['adult_subprice'],
								'created' => date('Y-m-d H:i:s'),
							);
							
							$child_discount = $this->site->calculateDiscount($bbq_discounts, $bbq_covers[1]['child_subprice']);
							$child_disfinal = $bbq_covers[1]['child_subprice'] - $child_discount;
							$child_tax_id = $possettings->default_tax;
							$child_tax_type = $possettings->tax_type;
							$child_tax = $this->site->calculateOrderTax( $possettings->default_tax, $child_disfinal);
							
							$bil_items[$i][] = array(
								'type' => 'child',
								'cover' => $bbq_covers[1]['number_of_child'],
								'price' => $bbq_covers[1]['child_price'],
								'days' => $bbq_covers[1]['child_days'],
								'buyx' => $bbq_covers[1]['child_buyx'],
								'getx' => $bbq_covers[1]['child_getx'],
								'discount_cover' => $bbq_covers[1]['child_discount_cover'],
								'discount' => $child_discount,
								'tax_id' => $child_tax_id,
								'tax_type' => $child_tax_type,
								'tax' => $child_tax,
								'subtotal' => $bbq_covers[1]['child_subprice'],
								'created' => date('Y-m-d H:i:s'),
							);
							
							$kids_discount = $this->site->calculateDiscount($bbq_discounts, $bbq_covers[2]['kids_subprice']);
							$kids_disfinal = $bbq_covers[2]['kids_subprice'] - $kids_discount;
							$kids_tax_id = $possettings->default_tax;
							$kids_tax_type = $possettings->tax_type;
							$kids_tax = $this->site->calculateOrderTax($possettings->default_tax, $kids_disfinal);
							
							$bil_items[$i][] = array(
								'type' => 'kids',
								'cover' => $bbq_covers[2]['number_of_kids'],
								'price' => $bbq_covers[2]['kids_price'],
								'days' => $bbq_covers[2]['kids_days'],
								'buyx' => $bbq_covers[2]['kids_buyx'],
								'getx' => $bbq_covers[2]['kids_getx'],
								'discount_cover' => $bbq_covers[2]['kids_discount_cover'],
								'discount' => $kids_discount,
								'tax_id' => $kids_tax_id,
								'tax_type' => $kids_tax_type,
								'tax' => $kids_tax,
								'subtotal' => $bbq_covers[2]['kids_subprice'],
								'created' => date('Y-m-d H:i:s'),
							);
							
								
							}
							
							$splits = $split_id;
							
							$bbq_response = $this->customer_api->BBQaddSale($notification_array, $timelog_array, $order_data_bbq, $splitData_bbq, $saleorder_item, $sale, $sale_items, $bilsdata_bbq, $bil_items, $bbq_order_id, $bbq_array, $splits);
							$overtotal[] = $gtotal;
						}
				}
				
				if($order_dine == 1){
					if($order_dine == 1 && $order_bbq == 1){
						$consolidated = 1;
					}else{
						$consolidated = 0;
					}
					if(!empty($dine_order_type)){
						
						$item_data = $this->customer_api->dinevaluegetBil($table_id, $split_id, $waiter_id);
						foreach($item_data['items'] as $item_row){
							foreach($item_row as $item){
								
								$item->discount_enable = 0;
								$order_item[] = $item;
								$total_price[] = $item->subtotal;
							}
						}
						
						$total_items = count($order_item);
						$total_price = array_sum($total_price);
						foreach($item_data['order'] as $order){
							$order_data = array('sales_type_id' => $order->order_type,
								'sales_split_id' => $order->split_id,
								'sales_table_id' => $order->table_id,
								'date' => date('Y-m-d H:i:s'),
								'reference_no' => 'SALES-'.date('YmdHis'),
								'customer_id' => $order->customer_id,
								'customer' => $order->customer,
								'biller_id' => $order->biller_id,
								'biller' => $order->biller,
								'warehouse_id' => $order->warehouse_id,
								'note' => $order->note,
								'staff_note' => $order->staff_note,
								'sale_status' => 'Process',
								'hash'      => hash('sha256', microtime() . mt_rand()),
								'total_items' => $total_items,
								'total_price' => $total_price
							);
						}
						
						$order_item = $order_item ? $order_item : array();
						$order_data = $order_data ? $order_data : array();
						
						
								
							$this->data['customer_discount'] = $this->site->GetAllcostomerDiscounts();
							$notification_array['customer_role'] = CUSTOMER;
							$notification_array['customer_msg'] = 'Waiter has been bil generator to customer';
							$notification_array['customer_type'] = 'Your bil  generator';
								
							$notification_array['from_role'] = $group_id;
							$notification_array['insert_array'] = array(
								'msg' => 'Waiter has been bil generator to '.$split_id,
								'type' => 'Bil generator ('.$split_id.')',
								'table_id' =>  $table_id,
								'role_id' => 8,
								'user_id' => $waiter_id,	
								'warehouse_id' => $warehouse_id,
								'created_on' => date('Y-m-d H:m:s'),
								'is_read' => 0
							);
						
							$item_data_dine = $this->customer_api->bildinegetBil($table_id, $split_id, $waiter_id);
								
							
							foreach($item_data_dine['items'] as $item_row){
								foreach($item_row as $item){
									$order_item_id[] = $item->id;
								}
							}	
					
								
							foreach($item_data_dine['items'] as $item_row){
								foreach($item_row as $item){
									$order_item_dine[] = $item;
								}
							}
					
							foreach($item_data_dine['items'] as $orderitems){
								foreach($orderitems as $items){
								$timelog_array[] = array(
								'status' => 'Closed',
								'created_on' => date('Y-m-d H:m:s'),
								'item_id' => $items->id,
								'user_id' => $user_id,	
								'warehouse_id' => $warehouse_id,);
							  }
							}	
								
							$order_item = $order_item_dine;
							foreach($item_data_dine['order'] as $order){
								$order_data_dine = array('sales_type_id' => $order->order_type ? $order->order_type : 1,
									'sales_split_id' => $order->split_id,
									'sales_table_id' => $order->table_id,
									'date' => date('Y-m-d H:i:s'),
									'reference_no' => 'SALES-'.date('YmdHis'),
									'customer_id' => $order->customer_id,
									'customer' => $order->customer,
									'biller_id' => $order->biller_id,
									'biller' => $order->biller,
									'warehouse_id' => $order->warehouse_id,
									'note' => $order->note != NULL ? $order->note : '',
									'staff_note' => $order->staff_note != NULL ? $order->staff_note : '',
									'sale_status' => 'Process',
									'hash'      => hash('sha256', microtime() . mt_rand()),
									'consolidated' => $consolidated
								);
								
								$notification_array['customer_id'] = $order->customer_id;
							}
							
							
					
							$order_data = $order_data_dine;
							
							$delivery_person =  0;
							
							}
							
							if(!empty($dine_order_type)){
										
										for($i=0; $i<$bils; $i++){
										
										
										
										$order_data->total_price;
											
										$check_discount_amount_old = '0.0000';
										//order_discount_input
										$check_order_discount_input = $customer_discount_val;
										
										if(!empty($check_discount_amount_old) || !empty($check_order_discount_input)){
											$check_discount = 'YES';
										}else{
											$check_discount = '';
										}
											
										$tot_item =	$order_data[0]->total_items;
										$tot_total = $item_data['order'][$i]->total;
										$itemdis = 0.0000;
										
														
										$billitem['bills_items'] = array();
										
										//$bills_item['recipe_name'] = $this->input->post('split[][$i][recipe_name][$i]');					
										$splitData_dine = array();
										
										
										
										
										$cus_dis = $this->customerdiscount($waiter_id, $table_id, $split_id, $customer_discount_val);
										
										$total_discount = $itemdis + $cus_dis;
										
										$final_bil2 = $tot_total - $total_discount;
										$total_tax = $this->site->calculateOrderTax( $possettings->default_tax, $final_bil2);
										$final_bil2 = $final_bil2 + $total_tax;
										
										
										$tax_amount = $total_tax;
									
										$grand_total = $final_bil2;
										
										foreach($item_data['items'][0] as $items){
											
											
											$offer_dis = 0.0000;
											
											
											$discount = $this->site->discountMultiple($items->recipe_id);
											if(!empty($discount)){
															   
												if($discount[2] == 'percentage_discount'){
												  $discount_value = $discount[1].'%';
												}else{
													$discount_value =$discount[1];
												}
												 $item_discount = $this->site->calculateDiscount($discount_value, $itembil->subtotal);
											}else{
												 $item_discount = 0;
											}
											
											if($customer_discount_val)
											{	
												$subtotal = $items->subtotal;
												//$tot_dis1 = $this->input->post('[split]['.$i.'][tot_dis1]');
				
												
				
												$item_discount = $item_discount;
												
												if($customer_type_val == "automanual"){
													$recipe_id = $items->recipe_id;
													$finalAmt = $subtotal - $item_discount -$offer_dis; 
													$customer_discount_status = 'applied';
													$discountid = $customer_discount_val;
													$recipeDetails = $this->customer_api->getrecipeByID($recipe_id);
													$group_id =$recipeDetails->category_id;
													
													$input_dis = $this->customer_api->recipe_customer_discount_calculation($customer_discount_val,$recipe_id,$group_id,$finalAmt,$discountid);
													
												}elseif($customer_type_val == "manual" ){
												   
												 $input_dis = $this->site->calculate_Discount($customer_discount_val, (($item->subtotal -  $item_discount) - $offer_dis),$tot_dis1 ? $tot_dis1 : $item_dis );
												}elseif($customer_type_val == "none"){
												   
												 $input_dis = $this->site->calculate_Discount($customer_discount_val, (($item->subtotal-$item_discount)-$offer_dis),$tot_dis1 ? $tot_dis1 : $item_dis );
												}
											 
											}
											else{
												
												$input_dis = 0;
											}
											
											if($possettings->default_tax)
											{
											$tax_type = $possettings->tax_type;
				
											  if($tax_type != 0){
				
											   $itemtax = $this->site->calculateOrderTax($possettings->default_tax, ($item->subtotal -($offer_dis ? $offer_dis:0) -($input_dis ? $input_dis:0)-($item_discount)));
											   
				
											   $sub_val =$item->subtotal;
				
											  }
											  else
											  {
												$default_tax = $this->site->calculateOrderTax($possettings->default_tax, ($item->subtotal-($offer_dis ? $offer_dis:0) -($input_dis ? $input_dis:0)-($item_discount)));
				
												$final_val = ($item->subtotal-($offer_dis ? $offer_dis:0) -($input_dis ? $input_dis:0)-($item_discount));
				
												$subval = $final_val/(($default_tax/$final_val)+1);
				
												$getTax = $this->site->getTaxRateByID($possettings->default_tax);
				
												$itemtax = ($subval) * ($getTax->rate / 100);
				
												$sub_val = $item->subtotal;	
											  } 
											}else{
												$sub_val = $item->subtotal;
											}
											
										$splitData_dine[$i][] = array(
											'recipe_name' =>$items->recipe_name,
											'unit_price' => $items->unit_price,
											'net_unit_price' => $items->unit_price * $items->quantity,
											'warehouse_id' => $warehouse_id,
											'recipe_type' => $items->recipe_type,
											'quantity' => $items->quantity,
											'recipe_id' => $items->recipe_id,
											'recipe_code' => $items->recipe_code,
											'discount' => 0,
											
											'item_discount' => $item_discount,
											'off_discount' => $offer_dis ? $offer_dis:0,
											'input_discount' => $input_dis ? $input_dis:0,
											'tax_type' => $possettings->tax_type, 
											'tax' => $itemtax,	
											'subtotal' => $sub_val,
			
										);
										
										}
										
										
										$billData_dine[$i] = array(
											'reference_no' => $item_data['order'][$i]->reference_no,
											'date' => date('Y-m-d H:i:s'),
											'customer_id' => $item_data['order'][$i]->customer_id,
											'customer' => $item_data['order'][$i]->customer,
											'biller' => $item_data['order'][$i]->biller,
											'biller_id' => $item_data['order'][$i]->biller_id,
											'total_items' => $item_data['order'][$i]->total_items,
											'total' => $item_data['order'][$i]->total,
											'total_tax' => $total_tax,
											'tax_type' => $possettings->tax_type, 
											'tax_id' => $possettings->default_tax,
											'total_discount' => $total_discount ? $total_discount : 0,
											'grand_total' => $grand_total,
											'round_total' => 0,
											'bill_type' => $bill_type != NULL ? $bill_type : 4,
											'delivery_person_id' => 0,
											'order_discount_id' => $customer_discount_val,
											'warehouse_id' => $item_data['order'][$i]->warehouse_id,
											'discount_type'=>$customer_type_val,
											'discount_val'=>$customer_discount_val,
											'consolidated' => $consolidated
											
										);
		
										
										
										
										
										}
										
										$sales_total = array_column($billData_dine, 'grand_total');
										$sales_total = array_sum($sales_total);
										
									
									
										 $dine_response = $this->customer_api->InsertBillDine($order_data_dine, $order_item_dine, $billData_dine,$splitData_dine, $sales_total, $delivery_person,$timelog_array, $notification_array,$order_item_id);
										 
										 
								$overtotal[] = $grand_total;
									
								}
				}
				
				
				$request_discount = array(
					'customer_id' => $user_id,
					'waiter_id' => $waiter_id,
					'table_id' => $table_id,
					'split_id' => $split_id,
					'customer_type_val' => $customer_type_val ? $customer_type_val : '',
					'customer_discount_val' => $customer_discount_val ? $customer_discount_val : '',
					'bbq_type_val' => $bbq_type_val ? $bbq_type_val : '',
					'bbq_discount_val' => $bbq_discount_val ? $bbq_discount_val : '',
					'created_on' => date('Y-m-d H:i:s')
				);
				
				$data = $this->customer_api->requestwithoutBill($split_id, $table_id, $request_discount, $overtotal);
			//////////////////////// notification /////////////////////
			$notification_array['from_role'] = $group_id;
		
			$notification_array['customer_role'] = CUSTOMER;
			$notification_array['customer_msg'] = 'Customer has been bil generator to '.$split_id;
			$notification_array['customer_type'] = 'Bil generator ('.$split_id.')';
			$notification_array['customer_id'] = $cashier_id;
		        $cashier_id = $this->site->getRandomActiveCashier();
			$notification_array['insert_array'] = array(
				'msg' => 'Customer has requested for bill  '.$split_id.' from '.$table_name,
				'type' => 'Bill Request',
				'table_id' =>  $table_id,
				'role_id' => 8,
				'user_id' => $cashier_id,
				'to_user_id' => $cashier_id,
				'warehouse_id' => $warehouse_id,
				'created_on' => date('Y-m-d H:m:s'),
				'is_read' => 0,
				'respective_steward'=>$cashier_id,
				'split_id'=>$split_id,
				'tag'=>'bill-request',
			);
			$deviceDetails = $this->customer_api->deviceDetails($cashier_id);
			$notifyID = $this->site->create_notification($notification_array);
			$request_type = 2;
			if($deviceDetails && !empty($deviceDetails)){
				foreach($deviceDetails as $k => $device){
					$title = 'Bill Request';
					$message = 'Customer has requested for bill  '.$split_id.' from '.$table_name;
					$push_data = $this->push->setPush($title,$message);
					if($this->site->isSocketEnabled() && $push_data == true && isset($device->socket_id) && $device->socket_id!=''){
						$json_data = '';
						$response_data = '';
						$json_data = $this->push->getPush();
						$regId_data = $device->device_token;
						$socket_id = $device->socket_id;
						
						//$response_data = $this->firebase->send($regId_data, $json_data);
						$this->site->send_billRequestpushNotification($title,$message,$socket_id,$split_id,$table_id,$notifyID,$request_type);
						//file_put_contents('notify_values66.txt',$title.$message,FILE_APPEND);
					}
				}
			}
			if($this->site->isSocketEnabled()){//file_put_contents('notify_values33.txt',$notifyID,FILE_APPEND);
				    $emit_notification['split_id'] = $split_id;
				    $emit_notification['request_type'] = $request_type;
				    $this->socketemitter->setEmit('payment_request', $emit_notification);
			}
			
			
			
			///////////////////////// notification - end ////////////////
			
		
			 if($dine_response || $bbq_response){
				 return $data;
			 }
		
		 return false;
	}
	
	public function billgenerator_post()
	{
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		
		$order_type = $this->input->post('order_type');
		$bill_type = $this->input->post('bill_type');
		$table_id = $this->input->post('table_id');
		$split_id = $this->input->post('split_id');
		$bils = $this->input->post('bils');
		$customer_discount_id = $this->input->post('customer_discount_id');
		
		$waiter_id = $this->site->getWaiter($split_id);
		

		$this->form_validation->set_rules('order_type', $this->lang->line("order_type"), 'required');
		$this->form_validation->set_rules('bill_type', $this->lang->line("bill_type"), 'required');
		$this->form_validation->set_rules('table_id', $this->lang->line("table_id"), 'required');
		$this->form_validation->set_rules('split_id', $this->lang->line("split_id"), 'required');
		$this->form_validation->set_rules('bils', $this->lang->line("bils"), 'required');
		$this->form_validation->set_rules('warehouse_id', $this->lang->line("warehouse_id"), 'required');
		$this->form_validation->set_rules('user_id', $this->lang->line("user_id"), 'required');
		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		if ($this->form_validation->run() == true) {
			
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){	
			
		
				$item_data = $this->customer_api->getBil($table_id, $split_id, $waiter_id);
				
				foreach($item_data['items'] as $item){
					$order_item[] = $item;
					$bil_total[] = $item->subtotal;
					
					$discount = $this->site->discountMultiple($item->recipe_id);
						
					if(!empty($discount)){
														   
						if($discount[2] == 'percentage_discount'){
						  $discount_value = $discount[1].'%';
						}else{
							$discount_value =$discount[1];
						}
						 $item_discount1 = $this->site->calculateDiscount($discount_value, $item->subtotal);
						 $total_dis[] = $item_discount1;
					}else{
						 $item_discount1 = 0;
						 $total_dis[] = 0;
					}
						
						
				}
				//var_dump($total_dis);
				$TotalDiscount = $this->site->TotalDiscount();
				if(!empty($TotalDiscount)){
					$offer_discount =  $TotalDiscount[1];
					$offer_discount_id =  $TotalDiscount[0];
				}else{
					$offer_discount = 0;
					$offer_discount_id =  0;
				}
				$final_bil = array_sum($bil_total) - array_sum($total_dis);
				$step_bil_1  = array_sum($bil_total) - array_sum($total_dis);
			/*	if($this->input->post('other_discount_type') == '1'){
					$other_discount = $this->input->post('other_discount').'%';
				}else{
					$other_discount = $this->input->post('other_discount');
				}*/
				$other_discount = '0%';
				$final_bil =  $final_bil - $TotalDiscount[1];
				$step_bil_2 = $step_bil_1 - $TotalDiscount[1];
				
				$other_discount_total = $this->site->calculateDiscount($other_discount, $step_bil_2);
				$total_discount =  $other_discount_total + array_sum($total_dis) + $offer_discount;
				$final_bil = $final_bil - $other_discount_total;
				$step_bil_3 = $step_bil_2 - $other_discount_total;
				
				
				$total_tax = $this->site->calculateOrderTax( $this->Settings->default_tax, $final_bil);
				//$final_bil = $final_bil + $total_tax;
				$final_bil = $final_bil;
				//$step_bil_4 = $step_bil_3 + $total_tax;
				$step_bil_4 = $step_bil_3;
				
				foreach($item_data['order'] as $order){
					$order_data = array('sales_type_id' => $order->order_type,
						'sales_split_id' => $order->split_id,
						'sales_table_id' => $order->table_id,
						'date' => date('Y-m-d H:i:s'),
						'reference_no' => 'SALES-'.date('YmdHis'),
						'customer_id' => $order->customer_id,
						'customer' => $order->customer,
						'biller_id' => $order->biller_id,
						'biller' => $order->biller,
						'warehouse_id' => $order->warehouse_id,
						'note' => $order->note,
						'staff_note' => $order->staff_note,
						'sale_status' => 'Process',
						'hash'      => hash('sha256', microtime() . mt_rand()),
					);
					$customer_id = $this->site->getOrderCustomer($order->id);
				}
				
				$delivery_person = $this->input->post('delivery_person_id') ? $this->input->post('delivery_person_id') : 0;
				
				$bil_value = $this->input->post('bils');
				
				for($i=1; $i<=$this->input->post('bils'); $i++){
					
					$total = array_sum($bil_total);
					$bil_total_count = count($item_data['items']);
					
					foreach($item_data['order'] as $order){
						$billData[$i] = array(
							'date' => date('Y-m-d H:i:s'),
							'customer_id' => $order->customer_id,
							'customer' => $order->customer,
							'biller_id' => $order->biller_id,
							'biller' => $order->biller,
							'reference_no' => 'SALES-'.date('YmdHis'),
							'total_items' => $bil_total_count,
							'total' => $total/$bil_value,
							'total_tax' => $total_tax/$bil_value,
							'tax_id' => $this->Settings->default_tax,
							'total_discount' => $total_discount/$bil_value,
							'grand_total' => $final_bil/$bil_value,
							'round_total' => $final_bil/$bil_value,
							'order_discount_id' => $offer_discount_id,
							'customer_discount_id' =>$customer_discount_id,
							'customer_discount_status' => 'pending',
							'bilgenerator_type'	=> 1,
							'bill_type' => $bill_type,
							'delivery_person_id' => $delivery_person,
							'warehouse_id' => $warehouse_id,
						);
					}
					foreach($item_data['items'] as $item){
						
						$discount = $this->site->discountMultiple($item->recipe_id);
						
						if(!empty($discount)){
															   
							if($discount[2] == 'percentage_discount'){
							  $discount_value = $discount[1].'%';
							}else{
								$discount_value =$discount[1];
							}
							 $item_discount = $this->site->calculateDiscount($discount_value, $item->subtotal);
						}else{
							 $item_discount = 0;
						}
						
						$off_discount = $this->site->calculate_Discount($offer_discount, ($item->subtotal - $item_discount), $step_bil_1);
						$input_discount = $this->site->calculate_Discount($other_discount_total, ($item->subtotal - $item_discount - $off_discount), $step_bil_2);
						
						$itemtax = $this->site->calculateOrderTax($this->input->post('tax_id'), ($item->subtotal - $off_discount - $input_discount - $item_discount));
	
						
						$splitData[$i][] = array(
							'recipe_name' => $item->recipe_name,
							'unit_price' => $item->unit_price/$bil_value,
							'net_unit_price' => $item->net_unit_price/$bil_value,
							'warehouse_id' => $warehouse_id,
							'recipe_type' => $item->recipe_type,
							'quantity' => $item->quantity,
							'recipe_id' => $item->recipe_id,
							'recipe_code' => $item->recipe_code,
							'discount' => $discount[0],						
							'item_discount' => $item_discount/$bil_value,
							'off_discount' => $off_discount ? $off_discount/$bil_value : 0,
							'input_discount' => $input_discount ? $input_discount/$i : 0,
							'tax' => $itemtax ? $itemtax/$bil_value : 0,	
							'subtotal' => $item->subtotal/$bil_value,
						);
					}
						
				}
				
				
				$sales_total = array_column($billData, 'grand_total');
				$sales_total = array_sum($sales_total);
				
				$grand_total = $sales_total;
				$notification_array['from_role'] = $group_id;
				
				 $notification_array['customer_role'] = CUSTOMER;
				 $notification_array['customer_msg'] = 'Customer has been bil generator to '.$split_id;
				 $notification_array['customer_type'] = 'Bil generator ('.$split_id.')';
				 $notification_array['customer_id'] = $customer_id;
				
				$notification_array['insert_array'] = array(
					'msg' => 'Customer has been bil generator to  '.$split_id,
					'type' => 'Bil generator ('.$split_id.')',
					'table_id' =>  $table_id,
					'role_id' => 8,
					'user_id' => $waiter_id,	
					'warehouse_id' => $warehouse_id,
					'created_on' => date('Y-m-d H:m:s'),
					'is_read' => 0
				);
							
				$data = $this->customer_api->InsertBill($order_data, $order_item, $billData,$splitData, $sales_total, $delivery_person, $timelog_array, $notification_array, $grand_total);
							
				$result = array( 'status'=> true , 'message'=> lang('biller_generator_has_been_success_please_check_cashier'),'message_khmer'=> html_entity_decode(lang('biller_generator_has_been_success_please_check_cashier_khmer')), 'data' => $data);
			}else{
				$result = array( 'status'=> false , 'message'=> lang('bil_generator_not_success'),'message_khmer'=> html_entity_decode(lang('bil_generator_not_success_khmer')));	
			}
			
		}else{
			$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));	
		}
		
		$this->response($result);
		
	}
	
	public function customfeedbacklist_post(){
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		
		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		$this->form_validation->set_rules('user_id', $this->lang->line("user_id"), 'required');
		$this->form_validation->set_rules('warehouse_id', $this->lang->line("warehouse"), 'required');
		if ($this->form_validation->run() == true) {
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){	
				$data = $this->customer_api->GetAllcustomfeedback();
				
				if(!empty($data)){
					$result = array( 'status'=> true , 'message'=> lang('custom_feedback_list'),'message_khmer'=> html_entity_decode(lang('custom_feedback_list_khmer')), 'data' => $data);
				}else{
					$result = array( 'status'=> false , 'message'=> lang('custom_feedback_list_empty'),'message_khmer'=> html_entity_decode(lang('custom_feedback_list_empty_khmer')));
				}
			}else{
				$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
			}
		}else{
			$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));	
		}
		$this->response($result);
		
	}
	
	public function feedback_post(){
		
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		$item_id = $this->input->post('item_id');
		$status = $this->input->post('status');
		$message = $this->input->post('message');
		$table_id = $this->input->post('table_id');
		$split_id = $this->input->post('split_id');
		
		$this->form_validation->set_rules('warehouse_id', $this->lang->line("warehouse_id"), 'required');
		$this->form_validation->set_rules('user_id', $this->lang->line("user_id"), 'required');
		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		
		if ($this->form_validation->run() == true) {
			for($i=0; $i<count($item_id); $i++){
				$insert_array[] = array(
					'customer_id' => $user_id,
					'table_id' => $table_id,
					'warehouse_id' => $warehouse_id,
					'item_id' => $item_id[$i] ? $item_id[$i] : 0,
					'status' => $status[$i] ? $status[$i] : '',
					'message' => $message[$i] ? $message[$i] : '',
					'split_id' => $split_id,
					'create_on' => date('Y-m-d H:i:s')
				);
			}
			
			
			$data = $this->customer_api->Insertfeedback($insert_array);
			if($data){
				$result = array( 'status'=> true , 'message'=> lang('feedback_added'),'message_khmer'=> html_entity_decode(lang('feedback_added_khmer')));	
			}else{
				$result = array( 'status'=> false , 'message'=> lang('feedback_not_added'),'message_khmer'=> html_entity_decode(lang('feedback_not_added_khmer')));	
			}
		}else{
			$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));	
		}
		
		$this->response($result);
	}
	
	
	public function extrafeedback_post(){
		
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		$question_id = $this->input->post('question_id');
		$answer = $this->input->post('answer');
		$table_id = $this->input->post('table_id');
		$split_id = $this->input->post('split_id');
		
		$this->form_validation->set_rules('warehouse_id', $this->lang->line("warehouse_id"), 'required');
		$this->form_validation->set_rules('user_id', $this->lang->line("user_id"), 'required');
		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		
		if ($this->form_validation->run() == true) {
			for($i=0; $i<count($question_id); $i++){
				$insert_array[] = array(
					'customer_id' => $user_id,
					'table_id' => $table_id,
					'warehouse_id' => $warehouse_id,
					'question_id' => $question_id[$i],
					'answer' => $answer[$i],
					'split_id' => $split_id,
					'created_on' => date('Y-m-d H:i:s')
				);
			}
			
			$data = $this->customer_api->Insertextrafeedback($insert_array);
			
			if($data){
				$result = array( 'status'=> true , 'message'=> lang('feedback_added'),'message_khmer'=> html_entity_decode(lang('feedback_added_khmer')));	
			}else{
				$result = array( 'status'=> false , 'message'=> lang('feedback_not_added'),'message_khmer'=> html_entity_decode(lang('feedback_not_added_khmer')));	
			}
		}else{
			$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));	
		}
		
		$this->response($result);
	}
	
	public function extrafeedbacknew_post(){
		$this->load->library('Emoji');
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		$question_id = $this->input->post('question_id');
		$answer = $this->input->post('answer');
		$table_id = $this->input->post('table_id');
		$split_id = $this->input->post('split_id');
		
		$comment = $this->input->post('comment');
		$photo = $_FILES['photo']['name'];
		$audio = $_FILES['audio']['name'];
		
		//$this->form_validation->set_rules('warehouse_id', $this->lang->line("warehouse_id"), 'required');
		//$this->form_validation->set_rules('user_id', $this->lang->line("user_id"), 'required');
		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		
		
		if ($this->form_validation->run() == true) {
			for($i=0; $i<count($question_id); $i++){
				$insert_array[] = array(
					'customer_id' => $user_id,
					'table_id' => $table_id,
					'warehouse_id' => $warehouse_id,
					'question_id' => $question_id[$i],
					'answer' => $answer[$i],
					'split_id' => $split_id,
					'created_on' => date('Y-m-d H:i:s')
				);
			}
			
			$testimonial_array = array(
				'customer_id' => $user_id,
				'comment' => $this->emoji->Encode($comment),
				'split_id' => $split_id,
				'status' => 1,
				'create_on' => date('Y-m-d H:i:s')
			);
			
			
			$this->upload_path = 'assets/uploads/';
			//$this->thumbs_path = 'assets/uploads/thumbs/';
			$this->image_types = 'gif|jpg|jpeg|png|pdf|csv|xlsx|mp3|aac|ogg|wma|m4a|flac|wac|mp4|avi|mpg|mov|wmv|mkv|m4v|webm|flv|3gp';
			$this->audio_types = 'mp4|3gp|flv|mp3|mpg';
			$this->allowed_file_size = '100000';
			
			if ($_FILES['photo']['size'] > 0) {
                
                $config['upload_path'] = $this->upload_path . 'testimonial/';
                $config['allowed_types'] = $this->image_types;
                $config['max_size'] = $this->allowed_file_size;
                $config['encrypt_name'] = TRUE;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('photo')) {
                   $result = array( 'status'=> false , 'message'=> lang('audio_not_upload'),'message_khmer'=> html_entity_decode(lang('audio_not_upload_khmer')));	
                    
                }
				
                 $photo1 = $this->upload->file_name;
				$testimonial_array['photo'] = $photo1;
                
            }
			
			
			
			//$_FILES['audio']['type'] = 'audio/mpeg';
			
			if ($_FILES['audio']['size'] > 0) {
				
               
				
                $config['upload_path'] = $this->upload_path . 'testimonial/';
                $config['allowed_types'] = $this->audio_types;
                $config['max_size'] = $this->allowed_file_size;
                $config['encrypt_name'] = TRUE;
				
                $this->upload->initialize($config);
				
                if (!$this->upload->do_upload('audio')) {
                   $result = array( 'status'=> false , 'message'=> lang('audio_not_upload'),'message_khmer'=> html_entity_decode(lang('audio_not_upload_khmer')));	
                    
                }
				
                $audio2 = $this->upload->file_name;
				$testimonial_array['audio'] = $audio2;
				
                
            }
			
	
			$data = $this->customer_api->Insertextrafeedback($insert_array, $testimonial_array);
			
			
			if($data){
				$result = array( 'status'=> true , 'message'=> lang('feedback_added'),'message_khmer'=> html_entity_decode(lang('feedback_added_khmer')));	
			}else{
				$result = array( 'status'=> false , 'message'=> lang('feedback_not_added'),'message_khmer'=> html_entity_decode(lang('feedback_not_added_khmer')));	
			}
		}else{
			$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));	
		}
		
		$this->response($result);
	}
	
	public function itemcancelorder_post(){
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		
		$item_id = $this->post('item_id');
		$split_id = $this->input->post('split_id');
		$remarks = $this->post('remarks');
		
		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		$this->form_validation->set_rules('item_id', $this->lang->line("item_id"), 'required');
		$this->form_validation->set_rules('remarks', $this->lang->line("remarks"), 'required');
		$this->form_validation->set_rules('user_id', $this->lang->line("user_id"), 'required');
		if ($this->form_validation->run() == true) {
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){
				
				
				 $notification_msg = 'The item has been cancel to customer';
				 $type = 'Customer Cancel';
				 
				 $item_data = $this->site->getOrderItem($item_id);
		 		 $waiter_id = $this->site->getWaiter($split_id);
		 
				 $notification_array['customer_role'] = CUSTOMER;
				 $notification_array['customer_msg'] =  'The '.$item_data->recipe_name.' has been cancel to customer';
				 $notification_array['customer_type'] = $type;
				 $notification_array['customer_id'] = $user_id;
				
				$notification_array['from_role'] = 7;
				$notification_array['insert_array'] = array(
					'msg' => $notification_msg,
					'type' => $type,
					'table_id' =>  0,
					'user_id' => $waiter_id,	
					'warehouse_id' => $warehouse_id,
					'created_on' => date('Y-m-d H:m:s'),
					'is_read' => 0
				);
				
				$data = $this->customer_api->CancelOrdersItem($split_id, $item_id, $remarks, $user_id, $notification_array);
				if(!empty($data)){
					$result = array( 'status'=> true , 'message'=> lang('item_has_been_cancel_success'),'message_khmer'=> html_entity_decode(lang('item_has_been_cancel_success_khmer')));
				}else{
					$result = array( 'status'=> false , 'message'=> lang('item_does_not_cancel'),'message_khmer'=> html_entity_decode(lang('item_does_not_cancel_khmer')));
				}
			}else{
				$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
			}
		}else{
			$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));
		}
		$this->response($result);
	}
	
	public function customerrequestorbilgenerator_post_29_09_2018(){
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		$table_id = $this->input->post('table_id');
		$split_id = $this->input->post('split_id');
		
		$this->form_validation->set_rules('user_id', $this->lang->line("user_id"), 'required');
		if ($this->form_validation->run() == true) {
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){
				$data = $this->customer_api->OrderRequestorBilgeneratorStatus($user_id, $table_id, $split_id);
				if($data == 1){
					$result = array( 'status'=> 1 , 'message'=> lang('customer_has_been_bil_request'),'message_khmer'=> html_entity_decode(lang('customer_has_been_bil_request_khmer')));
				}else if($data == 2){
					$result = array( 'status'=> 2 , 'message'=> lang('customer_is_not_bil_request'),'message_khmer'=> html_entity_decode(lang('customer_is_not_bil_request_khmer')));
				}else if($data == 3){
					$result = array( 'status'=> 3 , 'message'=> lang('bbq_cover_not_validated'),'message_khmer'=> html_entity_decode(lang('bbq_cover_validated_order_placed_khmer')));
				}
			}else{
				$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
			}
		}else{
			$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));
		}
		$this->response($result);
	}

	public function customerrequestorbilgenerator_post(){
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		$table_id = $this->input->post('table_id');
		$split_id = $this->input->post('split_id');
		$bbq_enable = $this->input->post('bbq_enable');
		
		$this->form_validation->set_rules('user_id', $this->lang->line("user_id"), 'required');
		if ($this->form_validation->run() == true) {
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){
				$data = $this->customer_api->OrderRequestorBilgeneratorStatus($user_id, $table_id, $split_id);				
				if($data == 1){
					$result = array( 'status'=> 1 , 'message'=> lang('customer_has_been_bil_request'),'message_khmer'=> html_entity_decode(lang('customer_has_been_bil_request_khmer')));
				}else if($data == 3){
					$result = array( 'status'=> 4 , 'message'=> lang('bbq_cover_not_validated'),'message_khmer'=> html_entity_decode(lang('bbq_cover_not_validated_khmer')));
				}else{
					$result = array( 'status'=> 2 , 'message'=> lang('customer_is_not_bil_request'),'message_khmer'=> html_entity_decode(lang('customer_is_not_bil_request_khmer')));
				}

			}else{
				$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
			}
		}else{
			$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));
		}
		$this->response($result);
	}

	public function logoutstatus_post(){
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		$table_id = $this->input->post('table_id');
		
		$this->form_validation->set_rules('user_id', $this->lang->line("user_id"), 'required');
		$this->form_validation->set_rules('table_id', $this->lang->line("table_id"), 'required');
		if ($this->form_validation->run() == true) {
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){
				
				$data = $this->customer_api->OrderCloseStatus($user_id,$table_id);
				if($data == TRUE){
					$result = array( 'status'=> true ,  'message'=> lang('all_orders_closed'),'message_khmer'=> html_entity_decode(lang('all_orders_closed_khmer')));
				}else{
					$result = array( 'status'=> false , 'message'=> lang('all_orders_not_closed'),'message_khmer'=> html_entity_decode(lang('all_orders_not_closed_khmer')));
				}
			}else{
				$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
			}
		}else{
			$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));
		}
		$this->response($result);
	}

	public function bbqcoverdetails_post(){
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');

		/*if ($this->form_validation->run() == true) {*/
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){				
				$data = $this->customer_api->getBBQCoverSettings();
				if(!empty($data)){
					$result = array( 'status'=> true , 'message'=> lang('bbq_covers'),'message_khmer'=> html_entity_decode(lang('bbq_covers_khmer')), 'data' => $data);
				}else{
					$result = array( 'status'=> false , 'message'=> lang('bbq_covers_empty'),'message_khmer'=> html_entity_decode(lang('bbq_covers_empty_khmer')));
				}
			}else{

				$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
			}	
	   /* }else{
			$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));
		}		*/	
		$this->response($result);
	}
	public function feedbacknew_post(){
		$this->load->library('Emoji');
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		$question_id = $this->input->post('question_id');
		$type = $this->input->post('type');
		$f_comment = $this->input->post('f_comment');
		$answer = $this->input->post('answer');
		$table_id = $this->input->post('table_id');
		$split_id = $this->input->post('split_id');
		
		$comment = $this->input->post('comment');
		$photo = $_FILES['photo']['name'];
		$audio = $_FILES['audio']['name'];
		//file_put_contents('feedback_post.txt',json_encode($_POST),FILE_APPEND);
		//$this->form_validation->set_rules('warehouse_id', $this->lang->line("warehouse_id"), 'required');
		//$this->form_validation->set_rules('user_id', $this->lang->line("user_id"), 'required');
		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		
		
		if ($this->form_validation->run() == true) {
			$ques_start = 0;
			$feedbacktype = false;
			if($question_id[0]=="Products"){
				$Allitems_feeback['status'] = $answer[0];
				$Allitems_feeback['message'] = $f_comment[0];
				$ques_start = 1;
				$feedbacktype = $type[0];
			}
			for($i=$ques_start; $i<count($question_id); $i++){
				$insert_array[] = array(
					'customer_id' => $user_id,
					'table_id' => $table_id,
					'warehouse_id' => $warehouse_id,
					'question_id' => $question_id[$i],
					'type' => $type[$i],
					'comment' => $f_comment[$i],
					'answer' => $answer[$i],
					'split_id' => $split_id,
					'created_on' => date('Y-m-d H:i:s')
				);
			}
			
			$testimonial_array = array(
				'customer_id' => $user_id,
				'comment' => $this->emoji->Encode($comment),
				'split_id' => $split_id,
				'status' => 1,
				'create_on' => date('Y-m-d H:i:s')
			);
			
			
			$this->upload_path = 'assets/uploads/';
			//$this->thumbs_path = 'assets/uploads/thumbs/';
			$this->image_types = 'gif|jpg|jpeg|png|pdf|csv|xlsx|mp3|aac|ogg|wma|m4a|flac|wac|mp4|avi|mpg|mov|wmv|mkv|m4v|webm|flv|3gp';
			$this->audio_types = 'mp4|3gp|flv|mp3|mpg';
			$this->allowed_file_size = '100000';
			
			if ($_FILES['photo']['size'] > 0) {
                
				$config['upload_path'] = $this->upload_path . 'testimonial/';
				$config['allowed_types'] = $this->image_types;
				$config['max_size'] = $this->allowed_file_size;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('photo')) {
				   $result = array( 'status'=> false , 'message'=> lang('audio_not_upload'),'message_khmer'=> html_entity_decode(lang('audio_not_upload_khmer')));	
				    
				}
						
				$photo1 = $this->upload->file_name;
				$testimonial_array['photo'] = $photo1;
			}	
			
			
			
			//$_FILES['audio']['type'] = 'audio/mpeg';
			
			if ($_FILES['audio']['size'] > 0) {								
				$config['upload_path'] = $this->upload_path . 'testimonial/';
				$config['allowed_types'] = $this->audio_types;
				$config['max_size'] = $this->allowed_file_size;
				$config['encrypt_name'] = TRUE;
						
				$this->upload->initialize($config);
						
				if (!$this->upload->do_upload('audio')) {
				   $result = array( 'status'=> false , 'message'=> lang('audio_not_upload'),'message_khmer'=> html_entity_decode(lang('audio_not_upload_khmer')));	
				    
				}
						
				$audio2 = $this->upload->file_name;
						$testimonial_array['audio'] = $audio2;
						
			}
			$item_feedback_array = array();
			/*********** Item Feedback - START**************/
			
			if($feedbacktype=='Each Items'){
			    /*********** Item Each Feedback **************/	
			    $item_id = $this->input->post('item_id');
			    $status = $this->input->post('status');
			    $message = $this->input->post('message');
			    
			    for($i=0; $i<count($item_id); $i++){
				    $item_feedback_array[] = array(
					    'customer_id' => $user_id,
					    'table_id' => $table_id,
					    'warehouse_id' => $warehouse_id,
					    'item_id' => $item_id[$i] ? $item_id[$i] : 0,
					    'status' => $status[$i] ? $status[$i] : '',
					    'message' => $message[$i] ? $message[$i] : '',
					    'split_id' => $split_id,
					    'create_on' => date('Y-m-d H:i:s')
				    );
			    }
			}else if($feedbacktype=='All Items'){
			    /*********** Item All common Feedback **************/
			    $allOrderItems = $this->customer_api->GetFeedback_itemsList($user_id,$warehouse_id,$table_id);
			    //$Allitems_feeback = $this->input->post('products');
			    foreach($allOrderItems as $k => $item){
				$item_feedback_array[] = array(
					    'customer_id' => $user_id,
					    'table_id' => $table_id,
					    'warehouse_id' => $warehouse_id,
					    'item_id' => $item->recipe_id?$item->recipe_id: 0,
					    'status' => $Allitems_feeback['status'],
					    'message' => (isset($Allitems_feeback['message']))?$Allitems_feeback['message']:'',
					    'split_id' => $split_id,
					    'create_on' => date('Y-m-d H:i:s')
				    );
			    }   
			}
			$this->customer_api->Insertfeedback($item_feedback_array);
			/*********** Item Feedback  - End **************/
			$testimonial_array['feedback_type'] = $feedbacktype;
			$data = $this->customer_api->Insertextrafeedback($insert_array, $testimonial_array);
			if($data){
				$result = array( 'status'=> true , 'message'=> lang('feedback_added'),'message_khmer'=> html_entity_decode(lang('feedback_added_khmer')));	
			}else{
				$result = array( 'status'=> false , 'message'=> lang('feedback_not_added'),'message_khmer'=> html_entity_decode(lang('feedback_not_added_khmer')));	
			}
		}else{
			$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));	
		}		
		$this->response($result);
	}
	
}
