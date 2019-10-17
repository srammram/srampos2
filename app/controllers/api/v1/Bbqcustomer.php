<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;



class Bbqcustomer extends REST_Controller {

	function __construct() {
		parent::__construct();
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		$this->load->api_model('bbqcustomer_api');
		$this->lang->admin_load('engliah_khmer','english');
		$this->load->library('socketemitter');
		$this->load->library('firebase');
		$this->load->library('push');
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
				$data = $this->bbqcustomer_api->checkbilStatus($user_id, $warehouse_id,$table_id);
				
				if(!empty($data)){
					$result = array( 'status'=> true , 'message'=> lang('success'),'message_khmer'=> html_entity_decode(lang('success_khmer')));
				}else{
					$result = array( 'status'=> false , 'message'=> lang('bill_generator_disabled'),'message_khmer'=> html_entity_decode(lang('bill_generator_disabled_khmer')));
				}
			}else{
				$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
			}
		}else{
			$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));	
		}
		$this->response($result);
	}
	
	public function bbqinsert_post(){
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		$table_id = $this->input->post('table_id');
		$bbq_menu_id = $this->input->post('bbq_menu_id');  	
		$phone_number = $this->input->post('phone');
		$name = $this->input->post('name');
		$stewardID = $this->site->getSteward($table_id);
		$bbq = $this->site->CreateBBQSplitID($stewardID);
		
		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		$this->form_validation->set_rules('user_id', $this->lang->line("user_id"), 'required');
		$this->form_validation->set_rules('warehouse_id', $this->lang->line("warehouse"), 'required');
		$this->form_validation->set_rules('table_id', $this->lang->line("table"), 'required');
		if ($this->form_validation->run() == true) {
			
			$bbq_array = array(
				'reference_no' => $bbq,
				'warehouse_id' => $this->input->post('warehouse_id'),
				'bbq_menu_id' => $this->input->post('bbq_menu_id'),
				'table_id' => $this->input->post('table_id'),
				'name' => $name ? $name : '',
				'phone' => $phone_number ? $phone_number : '',
				'number_of_adult' => $this->input->post('number_of_adult'),
				'number_of_child' => $this->input->post('number_of_child'),
				'number_of_kids' => $this->input->post('number_of_kids'),
				'bbq_set_id' => 1,
				'adult_price' => $this->input->post('adult_price'),
				'child_price' => $this->input->post('child_price'),
				'kids_price' => $this->input->post('kids_price'),
				'status' => 'waiting',
				'payment_status' => '',
				'created_by' => $this->input->post('user_id'),
				'customer_id' => $this->input->post('user_id'),
				'created_on' => date('Y-m-d H:i:s')
					
			);
		
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){		 
				$insert = $this->bbqcustomer_api->insertBBQ($bbq_array);
				if(!empty($insert)){
					if($this->site->isSocketEnabled()){$this->site->socket_refresh_bbqtables($table_id);}
					$result = array( 'status'=> true , 'message'=> lang('bbq_covers_added_success'),'message_khmer'=> html_entity_decode(lang('bbq_covers_added_success_khmer')), 'data' => $insert);
				}else{
					$result = array( 'status'=> false , 'message'=> lang('bbq_covers_not_added'),'message_khmer'=> html_entity_decode(lang('bbq_covers_not_added_khmer')));
				}
			}else{
				$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
			}
		}else{
			$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));	
		}
		$this->response($result);
	}
	
	public function bbqcover_post(){
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		$table_id = $this->input->post('table_id');
		
		$bbq_code = $this->post('bbq_code');
		
		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		$this->form_validation->set_rules('bbq_code', $this->lang->line("bbq_code"), 'required');
		$this->form_validation->set_rules('warehouse_id', $this->lang->line("warehouse"), 'required');
		if ($this->form_validation->run() == true) {
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){		 
				$data = $this->bbqcustomer_api->getBBQdataCode($bbq_code);
				if(!empty($data)){
					$result = array( 'status'=> true , 'message'=> lang('bbq_covers'),'message_khmer'=> html_entity_decode(lang('bbq_covers_khmer')), 'data' => $data);
				}else{
					$result = array( 'status'=> false , 'message'=> lang('bbq_covers_empty'),'message_khmer'=> html_entity_decode(lang('bbq_covers_empty_khmer')));
				}
			}else{
				$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
			}
		}else{
			$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));	
		}
		$this->response($result);
	}
	
	public function bbqupdate_post(){
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		$table_id = $this->input->post('table_id');
		
		$bbq_code = $this->post('bbq_code');
		
		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		$this->form_validation->set_rules('bbq_code', $this->lang->line("bbq_code"), 'required');
		$this->form_validation->set_rules('warehouse_id', $this->lang->line("warehouse"), 'required');
		if ($this->form_validation->run() == true) {
			
			$bbq_array = array(
				'number_of_adult' => $this->input->post('number_of_adult') ? $this->input->post('number_of_adult') : 1,
				'number_of_child' => $this->input->post('number_of_child') ? $this->input->post('number_of_child') : 0,
				'number_of_kids' => $this->input->post('number_of_kids') ?  $this->input->post('number_of_kids') : 0,
			);
		
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){		 
				$update = $this->bbqcustomer_api->updateBBQ($bbq_array, $bbq_code);
				if(!empty($update)){
					$result = array( 'status'=> true , 'message'=> lang('bbq_covers_update_success'),'message_khmer'=> html_entity_decode(lang('bbq_covers_update_success_khmer')));
				}else{
					$result = array( 'status'=> false , 'message'=> lang('bbq_covers_not_update'),'message_khmer'=> html_entity_decode(lang('bbq_covers_not_update_khmer')));
				}
			}else{
				$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
			}
		}else{
			$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));	
		}
		$this->response($result);
	}
	
	public function bbqorders_post(){
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
				$data = $this->bbqcustomer_api->GetAllorders($user_id, $warehouse_id,$table_id);
				
				if(!empty($data)){
					$result = array( 'status'=> true , 'message'=> lang('customer_bbq_order_data'),'message_khmer'=> html_entity_decode(lang('customer_bbq_order_data_khmer')), 'table_id' => $data[0]->table_id, 'table_name' => $data[0]->table_name, 'waiter_id' => $data[0]->waiter_id, 'chef_id' => $data[0]->chef_id, 'area_name' => $data[0]->area_name, 'session_started' => $data[0]->session_started, 'timezone' => $this->Settings->timezone_gmt, 'split_id' => $data[0]->split_id,  'data' => $data[0]->item);
				}else{
					$result = array( 'status'=> false , 'message'=> lang('customer_bbq_order_in_empty'),'message_khmer'=> html_entity_decode(lang('customer_bbq_order_in_empty_khmer')));
				}
			}else{
				$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
			}
		}else{
			$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));	
		}
		$this->response($result);
	}
	
	public function bbqdiscount_get(){
		$api_key = $this->input->get('api-key');
		$devices_key = $this->input->get('devices_key');
		$user_id = $this->input->get('user_id');
		$group_id = $this->input->get('group_id');
		$warehouse_id = $this->input->get('warehouse_id');
		
		$devices_check = $this->site->devicesCheck($api_key);
		if($devices_check == $devices_key){
			$data = $this->bbqcustomer_api->GetAllBBQdiscount();
			if(!empty($data)){
				$result = array( 'status'=> true , 'message'=> lang('bbq_discount_datas'),'message_khmer'=> html_entity_decode(lang('bbq_discount_datas_khmer')), 'data' => $data);
			}else{
				$result = array( 'status'=> false , 'message'=> lang('bbq_discount_empty'),'message_khmer'=> html_entity_decode(lang('bbq_discount_empty_khmer')));
			}
		}else{
			$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
		}
		$this->response($result);
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
		
		$bbq_discount_id = $this->input->post('bbq_discount_id');
		
		$waiter_id = $this->site->getWaiter($split_id);
		
		$bbq_discount = $this->bbqcustomer_api->GetBBQDiscount($bbq_discount_id);
		
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
					
					$order_id = $this->bbqcustomer_api->getBBQorderID($split_id, $order_type);
					
					$bbq_data = $this->bbqcustomer_api->getBBQData($split_id);
					$order_bbq = $this->bbqcustomer_api->BBQtablesplit($table_id, $split_id);
					
					$number_of_covers = ($bbq_data->number_of_adult + $bbq_data->number_of_child + $bbq_data->number_of_kids);
					$total_amount = ($bbq_data->number_of_adult * $bbq_data->adult_price) + ($bbq_data->number_of_child * $bbq_data->child_price) + ($bbq_data->number_of_kids * $bbq_data->kids_price);
					
					//$bbq_discount = '10%';
					$discount_total = $this->site->calculateDiscount($bbq_discount, $total_amount);
					$final_total =  $total_amount - $discount_total;
					
					$total_tax = $this->site->calculateOrderTax( $this->Settings->default_tax_rate, $final_total);
					$final_total = $final_total + $total_tax;
					$gtotal = $final_total;
					$sale = array(
						'bilgenerator_type' => 0,
						'sales_type_id' => 4,
						'sales_split_id' => $split_id,
						'sales_table_id' => $table_id,
						'date' => date('Y-m-d H:i:s'),
						'reference_no' => 'SALE'.date('YmdHis'),
						'customer_id' => $order_bbq->customer_id,
						'customer' => $order_bbq->customer,
						'biller_id' => $order_bbq->biller_id,
						'biller' => $order_bbq->biller,
						'warehouse_id' => $order_bbq->warehouse_id, 
						'total' => $total_amount, 
						'order_discount_id' => $bbq_discount, 
						'total_discount' => $discount_total,
						'order_tax_id' => $this->Settings->default_tax_rate,
						'total_tax' => $total_tax, 
						'grand_total' => $gtotal,
						'total_cover' => $number_of_covers
					);
					
					$grand_total = $gtotal;
					
						$sale_items[] = array(
							'type' => 'adult',
							'cover' => $bbq_data->number_of_adult,
							'price' => $bbq_data->adult_price,
							'subtotal' => $bbq_data->adult_price * $bbq_data->number_of_adult,
							'created' => date('Y-m-d H:i:s'),
						);
						
						$sale_items[] = array(
							'type' => 'child',
							'cover' => $bbq_data->number_of_child,
							'price' => $bbq_data->child_price,
							'subtotal' => $bbq_data->child_price * $bbq_data->number_of_child,
							'created' => date('Y-m-d H:i:s'),
						);
						
						$sale_items[] = array(
							'type' => 'kids',
							'cover' => $bbq_data->number_of_kids,
							'price' => $bbq_data->kids_price,
							'subtotal' => $bbq_data->kids_price * $bbq_data->number_of_kids,
							'created' => date('Y-m-d H:i:s'),
						);
					
					for($i=0; $i<$this->input->post('bils'); $i++){
						$bilsdata[$i] = array(
							'bilgenerator_type' => 0,
							'date' => date('Y-m-d H:i:s'),
							'reference_no' => 'SALE'.date('YmdHis'),
							'customer_id' => $order_bbq->customer_id,
							'customer' => $order_bbq->customer,
							'biller_id' => $order_bbq->biller_id,
							'biller' => $order_bbq->biller,
							'warehouse_id' => $order_bbq->warehouse_id, 
							'total' => $total_amount / $bils, 
							'order_discount_id' => $bbq_discount, 
							'total_discount' => $discount_total / $bils,
							'tax_id' => $this->Settings->default_tax_rate,
							'total_tax' => $total_tax / $bils, 
							'tax_type' => 0,
							'grand_total' => $gtotal / $bils,
							'total_cover' => $number_of_covers / $bils
						);
						
						$bil_items[$i][] = array(
							'type' => 'adult',
							'cover' => $bbq_data->number_of_adult,
							'price' => $bbq_data->adult_price / $bils,
							'subtotal' => ($bbq_data->adult_price * $bbq_data->number_of_adult) / $bils,
							'created' => date('Y-m-d H:i:s'),
						);
						
						$bil_items[$i][] = array(
							'type' => 'child',
							'cover' => $bbq_data->number_of_child,
							'price' => $bbq_data->child_price / $bils,
							'subtotal' => ($bbq_data->child_price * $bbq_data->number_of_child) / $bils,
							'created' => date('Y-m-d H:i:s'),
						);
						
						$bil_items[$i][] = array(
							'type' => 'kids',
							'cover' => $bbq_data->number_of_kids,
							'price' => $bbq_data->kids_price / $bils,
							'subtotal' => ($bbq_data->kids_price * $bbq_data->number_of_kids) / $bils,
							'created' => date('Y-m-d H:i:s'),
						);
					}
					
				
					$response_data = $this->bbqcustomer_api->BBQaddSale($sale, $sale_items, $bilsdata, $bil_items, $order_id, $split_id, $grand_total);
					if($response_data){
						$result = array( 'status'=> true, 'message'=> lang('bils_generator_success'),'message_khmer'=> html_entity_decode(lang('bils_generator_success_khmer')), 'data' => $response_data);	
					}else{
						$result = array( 'status'=> false , 'message'=> lang('bils_generator_not_success'),'message_khmer'=> html_entity_decode(lang('bils_generator_not_success_khmer')));		
					}
			}else{
				$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
			}
			
		}else{
			$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));	
		}
		
		$this->response($result);
		
	}
	//public function bbqcoversconfirmationrequest_post(){
	//	$api_key = $this->input->post('api-key');
	//	$devices_key = $this->input->post('devices_key');
	//	$user_id = $this->input->post('user_id');
	//	$group_id = $this->input->post('group_id');
	//	$warehouse_id = $this->input->post('warehouse_id');
	//	$table_id = $this->input->post('table_id');
	//	$bbq = $this->input->post('bbq_code');
	//	
	//	$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
	//	$this->form_validation->set_rules('bbq_code', $this->lang->line("bbq_code"), 'required');
	//	$this->form_validation->set_rules('warehouse_id', $this->lang->line("warehouse"), 'required');
	//	$this->form_validation->set_rules('user_id', $this->lang->line("user"), 'required');
	//	$this->form_validation->set_rules('table_id', $this->lang->line("table"), 'required');
	//	$this->form_validation->set_rules('group_id', $this->lang->line("group"), 'required');
	//	
	//	if ($this->form_validation->run() == true) {
	//		$devices_check = $this->site->devicesCheck($api_key);
	//		if($devices_check == $devices_key){
	//			$table_name = $this->site->getTablename($table_id);
	//			$notification_message = $table_name.' - Customer has sent BBQ Covers.';
	//			$notification_title = 'BBQ Covers validation request - '.$bbq;
	//			$notification_array['from_role'] = $group_id;
	//			$notification_array['insert_array'] = array(
	//				'msg' => $notification_message,
	//				'type' => $notification_title,
	//				//'reference'=>$bbq,
	//				'table_id' => $table_id,
	//				'user_id' => $user_id,
	//				'to_user_id' => $user_id,	
	//				'role_id' => $group_id,
	//				'warehouse_id' => $warehouse_id,
	//				'created_on' => date('Y-m-d H:i:s'),
	//				'is_read' => 0
	//			);
	//			//$this->site->create_notification($notification_array);
	//			
	//			$device_token = $this->site->deviceGET($user_id);	
	//			foreach($device_token as $token){
	//				$title = $notification_title;
	//				$message = $notification_message;
	//				$push_data = $this->push->setPush($title,$message);
	//				if($push_data == true){
	//					$json_data = '';
	//					$response_data = '';
	//					$json_data = $this->push->getPush();
	//					$regId_data = $token;
	//					$response_data = $this->firebase->send($regId_data, $json_data);
	//					//var_dump($response_data);
	//				}
	//			}
	//		
	//			$notification['title'] = $notification_title;
	//			$notification['msg'] = $notification_message;
	//			$notification['bbqcode'] = 'oiooioioio';
	//			$event = 'bbq_cover_confirmed';
	//			$edata = $notification;
	//			$this->socketemitter->setEmit($event, $_POST);				
	//			$result = array( 'status'=> true , 'message'=> lang('bbq_covers_notification_has_been_sent'),'message_khmer'=> html_entity_decode(lang('bbq_covers_notification_has_been_sent')));
	//		       
	//			
	//		}else{
	//			$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
	//		}
	//	}else{
	//		$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));	
	//	}
	//	$this->response($result);
	//	
	//	//echo json_encode($result);
	//	//sleep(10);
	//	//$this->site->setTimeout('is_bbqCoversValidated',$bbq,1);
	//}
	public function confirmbbqcovers_post(){
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		$table_id = $this->input->post('table_id');
		$bbq = $this->input->post('bbq_code');
		
		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		$this->form_validation->set_rules('bbq_code', $this->lang->line("bbq_code"), 'required');
		$this->form_validation->set_rules('warehouse_id', $this->lang->line("warehouse"), 'required');
		$this->form_validation->set_rules('user_id', $this->lang->line("user"), 'required');
		$this->form_validation->set_rules('table_id', $this->lang->line("table"), 'required');
		$this->form_validation->set_rules('group_id', $this->lang->line("group"), 'required');
		
		if ($this->form_validation->run() == true) {
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){
				
				
				$bbq_array = array(
					'number_of_adult' => $this->input->post('number_of_adult') ? $this->input->post('number_of_adult') : 1,
					'number_of_child' => $this->input->post('number_of_child') ? $this->input->post('number_of_child') : 0,
					'number_of_kids' => $this->input->post('number_of_kids') ?  $this->input->post('number_of_kids') : 0,
				);
				$bbq_array['status'] = 'open';
				$bbq_array['confirmed_by'] = $user_id;
				$bbq_array['order_request'] = 1;
				$update = $this->bbqcustomer_api->updateBBQ($bbq_array, $bbq);
				if(!empty($update)){
					$device_data = $this->bbqcustomer_api->getCustomerSocketData($table_id);
					if($this->site->isSocketEnabled()){
						foreach($device_data as $k =>$device){
							$notification['title'] = lang('bbq_covers_confirmed');
							$notification['msg'] = lang('bbq_covers_confirmed');
							$notification['user_id'] = $user_id;
							$notification['bbqcode'] = $bbq;
							$notification['socket_id'] = $device->socket_id;
							$event = 'bbq_cover_confirmed';
							$edata = $notification;
							$this->socketemitter->setEmit($event, $edata);
						}
					}
					$update_notifi['split_id']=$bbq;
					$update_notifi['tag']='bbq-cover-validation';
					$this->site->update_notification_status($update_notifi);
					$result = array( 'status'=> true , 'message'=> lang('bbq_covers_confirmed'),'message_khmer'=> html_entity_decode(lang('bbq_covers_confirmed')));
				}else{
					$result = array( 'status'=> false , 'message'=> lang('bbq_covers_not_confirmed'),'message_khmer'=> html_entity_decode(lang('bbq_covers_confirmed')));
				}
			}else{
				$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
			}
		}else{
			$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));	
		}
		$this->response($result);
	}
	public function bbqaddmorecovers_post(){
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		$table_id = $this->input->post('table_id');
		
		$bbq_code = $this->post('bbq_code');
		
		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		$this->form_validation->set_rules('bbq_code', $this->lang->line("bbq_code"), 'required');
		$this->form_validation->set_rules('warehouse_id', $this->lang->line("warehouse"), 'required');
		if ($this->form_validation->run() == true) {
			
			$bbq_array = array(
				'number_of_adult' => $this->input->post('number_of_adult') ? $this->input->post('number_of_adult') : 1,
				'number_of_child' => $this->input->post('number_of_child') ? $this->input->post('number_of_child') : 0,
				'number_of_kids' => $this->input->post('number_of_kids') ?  $this->input->post('number_of_kids') : 0,
			);
		
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){
				
				$data = $this->bbqcustomer_api->getBBQdataCode($bbq_code);
				$lowCntError = false;
				if($data->number_of_adult<$bbq_array['number_of_adult']){
					$lowCntError = true;
				}
				if($data->number_of_child<$bbq_array['number_of_child']){
					$lowCntError = true;
				}
				if($data->number_of_kids<$bbq_array['number_of_kids']){
					$lowCntError = true;
				}
				if(!$lowCntError){				
					$update = $this->bbqcustomer_api->updateBBQCoversCount($bbq_array, $bbq_code);
					if(!empty($update)){
						$result = array( 'status'=> true , 'message'=> lang('bbq_covers_update_success'),'message_khmer'=> html_entity_decode(lang('bbq_covers_update_success_khmer')));
					}else{
						$result = array( 'status'=> false , 'message'=> lang('bbq_covers_not_update'),'message_khmer'=> html_entity_decode(lang('bbq_covers_not_update_khmer')));
					}	
				}else{
					$result = array( 'status'=> false , 'message'=> lang('covers_count_should_not_be_lower_than_existing_count'),'message_khmer'=> html_entity_decode(lang('covers_count_should_not_be_lower_than_existing_count')));
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
