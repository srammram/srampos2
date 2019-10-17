<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;



class Kitchen extends REST_Controller {

	function __construct() {
		parent::__construct();
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		$this->load->api_model('kitchen_api');
	}
	
	public function index_post()
	{
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		
		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		if ($this->form_validation->run() == true) {
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){			
				$data = $this->kitchen_api->GetAllkitchen();
				if(!empty($data)){
					$result = array( 'status'=> true , 'message'=> 'Kitchen type list in data', 'data' => $data);
				}else{
					$result = array( 'status'=> false , 'message'=> 'Kitchen type list in Empty');
				}
			}else{
				$result = array( 'status'=> false , 'message'=> 'Devices Key does not matche. please check your devices key');	
			}
		}else{
			$result = array( 'status'=> false , 'message'=> 'Please Enter All Fields');	
		}
		$this->response($result);
		
	}
	
	public function orders_post()
	{
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		
		$kitchen_type = $this->post('kitchen_type');
		$this->form_validation->set_rules('warehouse_id', $this->lang->line("warehouse"), 'required');
		$this->form_validation->set_rules('kitchen_type', $this->lang->line("kitchen_type"), 'required');
		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		$this->form_validation->set_rules('user_id', $this->lang->line("user_id"), 'required');
		if ($this->form_validation->run() == true) {	
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){	
				$data = $this->kitchen_api->getAllTablesWithKitchen($warehouse_id, $kitchen_type, $user_id);
				if(!empty($data)){
					$result = array( 'status'=> true , 'message'=> 'Kitchen order in data', 'data' => $data);
				}else{
					$result = array( 'status'=> false , 'message'=> 'Kitchen order in Empty');
				}
			}else{
				$result = array( 'status'=> false , 'message'=> 'Devices Key does not matche. please check your devices key');	
			}
		}else{
			$result = array( 'status'=> false , 'message'=> 'Please Enter All Fields');	
		}
		$this->response($result);
		
	}
	
	public function items_post()
	{
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		
		$order_id = $this->post('order_id');
		$kitchen_type = $this->post('kitchen_type');
		$order_type = $this->post('order_type');
		$this->form_validation->set_rules('warehouse_id', $this->lang->line("warehouse"), 'required');
		$this->form_validation->set_rules('order_id', $this->lang->line("order_id"), 'required');
		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		$this->form_validation->set_rules('user_id', $this->lang->line("user_id"), 'required');
		if ($this->form_validation->run() == true) {	
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){	
				$data = $this->kitchen_api->getAllTablesWithKitchenItem($warehouse_id, $order_id, $order_type, $kitchen_type, $user_id);
				if(!empty($data)){
					$result = array( 'status'=> true , 'message'=> 'Kitchen order in data', 'data' => $data);
				}else{
					$result = array( 'status'=> false , 'message'=> 'Kitchen order in Empty');
				}
			}else{
				$result = array( 'status'=> false , 'message'=> 'Devices Key does not matche. please check your devices key');	
			}
		}else{
			$result = array( 'status'=> false , 'message'=> 'Please Enter All Fields');	
		}
		$this->response($result);
		
	}
		
	public function itemcancelorder_post(){
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		
		$item_id = $this->input->post('item_id');
		$remarks = $this->input->post('remarks');
		
		$this->form_validation->set_rules('item_id', $this->lang->line("item_id"), 'required');
		$this->form_validation->set_rules('remarks', $this->lang->line("remarks"), 'required');
		$this->form_validation->set_rules('user_id', $this->lang->line("user_id"), 'required');
		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		
		
		 $type = 'Chef Cancel';
		 $notification_msg = 'The item has been cancel to chef';
		
		 $item_data = $this->site->getOrderItem($item_id);
		 $customer_id = $this->site->getOrderItemCustomer($item_id);
 
		 $notification_array['customer_role'] = CUSTOMER;
		 $notification_array['customer_msg'] =  'The '.$item_data->recipe_name.' has been cancel to chef';
		 $notification_array['customer_type'] = $type;
		 $notification_array['customer_id'] = $customer_id;
		
		$notification_array['from_role'] = $group_id;
		$notification_array['insert_array'] = array(
			'msg' => $notification_msg,
			'type' => $type,
			'table_id' =>  0,
			'user_id' => $user_id,	
			'warehouse_id' => $warehouse_id,
			'created_on' => date('Y-m-d H:m:s'),
			'is_read' => 0
		);   
		
		if ($this->form_validation->run() == true) {
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){	
				$data = $this->kitchen_api->CancelOrdersItem($notification_array, $remarks, $item_id, $user_id);
				if(!empty($data)){
					$result = array( 'status'=> true , 'message'=> 'Item has been cancel success');
				}else{
					$result = array( 'status'=> false , 'message'=> 'Item does not cancel');
				}
			}else{
				$result = array( 'status'=> false , 'message'=> 'Devices Key does not matche. please check your devices key');	
			}
		}else{
			$result = array( 'status'=> false , 'message'=> 'Please Enter All Fields');
		}
		$this->response($result);
	}
	
	public function kitchenstatus_post()
    {   
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		
         $status = $this->input->post('status');
         $order_item_id = $this->input->post('order_item_id'); 
		 $order_id = $this->input->post('order_id');
		 $order_type = $this->input->post('order_type');
		
		
		
		 $this->form_validation->set_rules('status', $this->lang->line("status"), 'required');
		 $this->form_validation->set_rules('order_id', $this->lang->line("order_id"), 'required');
		 $this->form_validation->set_rules('order_type', $this->lang->line("order_type"), 'required');
		 $this->form_validation->set_rules('user_id', $this->lang->line("user_id"), 'required');
		 $this->form_validation->set_rules('warehouse_id', $this->lang->line("warehouse_id"), 'required');       
		 $this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
         if($status == 'Inprocess'){
            $current_status = 'Preparing';
         }elseif($status == 'Preparing' && ($order_type == 1 || $order_type == 4)){
            $current_status = 'Ready';
         }elseif($status == 'Preparing' && ($order_type == 2 || $order_type == 3)){
			 $current_status = 'Closed';
		}else{
            $current_status = 'Inprocess';
         }
         
		 
		
		 $customer_id = $this->site->getOrderCustomer($order_id);
 
		 $notification_array['customer_role'] = CUSTOMER;
		 $notification_array['customer_msg'] =  'The item has been '.$current_status.' to chef';
		 $notification_array['customer_type'] = 'Chef '.$current_status.' Status';
		 $notification_array['customer_id'] = $customer_id;
		 	
		$notification_array['from_role'] = $group_id;
		$notification_array['insert_array'] = array(
			'type' => 'Chef '.$current_status.' Status',
			'table_id' =>  0,
			'user_id' => $user_id,	
			'role_id' => 7,
			'warehouse_id' => $warehouse_id,
			'created_on' => date('Y-m-d H:m:s'),
			'is_read' => 0
		);
		

		 
		 if ($this->form_validation->run() == true) {
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){		 
				$data = $this->kitchen_api->updateKitchenstatus($notification_array, $status, $order_id, $order_item_id, $current_status, $user_id);
				
				if(!empty($data)){
					$result = array( 'status'=> true , 'message'=> 'Item has been status success');
				}else{
					$result = array( 'status'=> false , 'message'=> 'Item  does not status');
				}
			}else{
				$result = array( 'status'=> false , 'message'=> 'Devices Key does not matche. please check your devices key');	
			}
		}else{
			$result = array( 'status'=> false , 'message'=> 'Please Enter All Fields');
		}
		$this->response($result);
        
    }
	
	
		
}
