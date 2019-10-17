<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;



class Biller extends REST_Controller {

	function __construct() {
		parent::__construct();
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		$this->load->api_model('biller_api');
		$this->load->library('firebase');
		$this->load->library('push');
		$this->lang->admin_load('engliah_khmer','english');
		$this->possettings = $this->biller_api->getPOSSettings();
	}
	
	public function bilgenerator_post()
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

		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		$this->form_validation->set_rules('order_type', $this->lang->line("order_type"), 'required');
		$this->form_validation->set_rules('bill_type', $this->lang->line("bill_type"), 'required');
		$this->form_validation->set_rules('table_id', $this->lang->line("table_id"), 'required');
		$this->form_validation->set_rules('split_id', $this->lang->line("split_id"), 'required');
		$this->form_validation->set_rules('bils', $this->lang->line("bils"), 'required');
		$this->form_validation->set_rules('warehouse_id', $this->lang->line("warehouse_id"), 'required');
		$this->form_validation->set_rules('user_id', $this->lang->line("user_id"), 'required');
		
		if ($this->form_validation->run() == true) {
			
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){	
				
				$data['order_type'] = $order_type;
				$data['bill_type'] = $bill_type;
				$data['bils'] = $bils;
				$data['table_id'] = $table_id;
				$data['split_id'] = $split_id;
				$data['tax_rates'] = $this->site->getAllTaxRates();
				$data['delivery_person'] = $this->site->getDeliveryPersonall($warehouse_id);
				$data['customer_discount'] = $this->site->GetAllcostomerDiscounts();
				$data['possettings'] = $this->biller_api->getPOSSettings();
				$data['settings'] = $this->biller_api->getSettings();
				
				$data['current_user'] = $this->biller_api->getUserByID($user_id);
				
				
				if(!empty($table_id)){
					$item_data = $this->biller_api->getBil($table_id, $split_id, $user_id);
				}else{
					$item_data = $this->biller_api->getBil($table_id, $split_id, $user_id);
				}	
				
				foreach($item_data['items'] as $item_row){
					foreach($item_row as $item){
						$order_item_id[] = $item->id;
					}
				}	
		
					
				foreach($item_data['items'] as $item_row){
					foreach($item_row as $item){
						$order_item[] = $item;
					}
				}
		
				foreach($item_data['items'] as $orderitems){
					foreach($orderitems as $items){
					$timelog_array[] = array(
					'status' => 'Closed',
					'created_on' => date('Y-m-d H:m:s'),
					'item_id' => $items->id,
					'user_id' => $user_id,	
					'warehouse_id' => $warehouse_id);
				  }
				}	
				
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
					
					
				}
					
				$data['order_item'] = $order_item;
				$data['order_data'] = $order_data;			
				$update_notifi['split_id']=$split_id;
				$update_notifi['tag']='bill-request';
				$this->site->update_notification_status($update_notifi);	
				$result = array( 'status'=> true , 'message'=> lang('biller_generator_in_data'),'message_khmer'=> html_entity_decode(lang('biller_generator_in_data_khmer')), 'data' => $data);
			}else{
				$result = array( 'status'=> false ,  'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
			}
			
		}else{
			$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));	
		}
		
		$this->response($result);
		
	}
	
	public function biladd_post(){
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
		$customer_discount = $this->input->post('customer_discount');

		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		$this->form_validation->set_rules('order_type', $this->lang->line("order_type"), 'required');
		$this->form_validation->set_rules('bill_type', $this->lang->line("bill_type"), 'required');
		$this->form_validation->set_rules('table_id', $this->lang->line("table_id"), 'required');
		$this->form_validation->set_rules('split_id', $this->lang->line("split_id"), 'required');
		$this->form_validation->set_rules('bils', $this->lang->line("bils"), 'required');
		$this->form_validation->set_rules('warehouse_id', $this->lang->line("warehouse_id"), 'required');
		$this->form_validation->set_rules('user_id', $this->lang->line("user_id"), 'required');
		
		if ($this->form_validation->run() == true) {
			
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){	
				
				$data['order_type'] = $order_type;
				$data['bill_type'] = $bill_type;
				$data['bils'] = $bils;
				$data['table_id'] = $table_id;
				$data['split_id'] = $split_id;
				$data['tax_rates'] = $this->site->getAllTaxRates();
				$data['delivery_person'] = $this->site->getDeliveryPersonall($warehouse_id);
				$data['customer_discount'] = $this->site->GetAllcostomerDiscounts();
				$data['possettings'] = $this->biller_api->getPOSSettings();
				$data['settings'] = $this->biller_api->getSettings();
				
				$data['current_user'] = $this->biller_api->getUserByID($user_id);
				
				
				if(!empty($table_id)){
					$item_data = $this->biller_api->getBil($table_id, $split_id, $user_id);
				}else{
					$item_data = $this->biller_api->getBil($table_id, $split_id, $user_id);
				}	
				
				foreach($item_data['items'] as $item_row){
					foreach($item_row as $item){
						$order_item_id[] = $item->id;
					}
				}	
		
					
				foreach($item_data['items'] as $item_row){
					foreach($item_row as $item){
						$order_item[] = $item;
					}
				}
		
				foreach($item_data['items'] as $orderitems){
					foreach($orderitems as $items){
					$timelog_array[] = array(
					'status' => 'Closed',
					'created_on' => date('Y-m-d H:m:s'),
					'item_id' => $items->id,
					'user_id' => $user_id,	
					'warehouse_id' => $warehouse_id);
				  }
				}	
				
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
					$notification_array['customer_id'] = $order->customer_id;
					
				}
					
				$data['order_item'] = $order_item;
				$data['order_data'] = $order_data;	
				
				$notification_array['customer_role'] = CUSTOMER;
				$notification_array['customer_msg'] = 'Customer has been bil generator to customer';
				$notification_array['customer_type'] = 'Your bil  generator';
					
				$notification_array['from_role'] = $group_id;
				$notification_array['insert_array'] = array(
					'msg' => 'customer has been bil generator to '.$split_id,
					'type' => 'Bil generator ('.$split_id.')',
					'table_id' =>  $table_id,
					'role_id' => CASHIER,
					'user_id' => $user_id,	
					'warehouse_id' => $warehouse_id,
					'created_on' => date('Y-m-d H:m:s'),
					'is_read' => 0
				);	
				$postData = $this->input->post();
				$delivery_person = $this->input->post('delivery_person_id') ? $this->input->post('delivery_person_id') : 0;	
					
				
						for($i=1; $i<=$this->input->post('bils'); $i++){
							
						$check_discount_amount_old = $this->input->post('split['.$i.'][itemdiscounts]');
						$check_order_discount_input = $this->input->post('split['.$i.'][order_discount_input]');
						
						if(!empty($check_discount_amount_old) || !empty($check_order_discount_input)){
							$check_discount = 'YES';
						}else{
							$check_discount = '';
						}
							
						$tot_item =	$this->input->post('[split]['.$i.'][total_item]');
						$itemdis = $this->input->post('[split]['.$i.'][discount_amount]')/$tot_item;
						
										
						$billitem['bills_items'] = array();
						$bills_item['recipe_name'] = $this->input->post('split[][$i][recipe_name][$i]');					
						$splitData = array();
						foreach ($postData['split'][$i]['recipe_name'] as $key => $split) {

							$offer_dis = 0.0000;
							if($this->input->post('[split]['.$i.'][tot_dis_value]'))
							{
								$offer_dis = $this->site->calculate_Discount($this->input->post('[split]['.$i.'][tot_dis_value]'), ($postData['split'][$i]['subtotal'][$key]-$postData['split'][$i]['item_discount'][$key]),$this->input->post('[split]['.$i.'][item_dis]'));
							}
							/*314500*/
							
							if($this->input->post('[split]['.$i.'][order_discount_input]'))
							{	
								$subtotal =$postData['split'][$i]['subtotal'][$key];
								$tot_dis1 = $this->input->post('[split]['.$i.'][tot_dis1]');

								$item_dis = $postData['split'][$i]['item_dis'][$key];

								$item_discount = $postData['split'][$i]['item_discount'][$key];
if($customer_discount == "customer"){
    $recipe_id =  $postData['split'][$i]['recipe_id'][$key];
								/*echo $recipe_id;die;*/
								//echo $subtotal.'-'.$item_dis.'-'.$offer_dis;
								$finalAmt = $subtotal - $item_discount -$offer_dis; 
								$customer_discount_status = 'applied';
								$discountid = $this->input->post('[split]['.$i.'][order_discount_input]');
								$recipeDetails = $this->biller_api->getrecipeByID($recipe_id);
								$group_id =$recipeDetails->category_id;
								$input_dis = $this->biller_api->recipe_customer_discount_calculation($recipe_id,$group_id,$finalAmt,$discountid,$customer_discount);
}else if($customer_discount == "manual"){
							   
							 $input_dis = $this->site->calculate_Discount($this->input->post('[split]['.$i.'][order_discount_input]'), (($postData['split'][$i]['subtotal'][$key]-$postData['split'][$i]['item_discount'][$key])-$offer_dis),$tot_dis1 ? $tot_dis1 : $item_dis );
}
							 
							// $input_dis = $this->input->post('[split]['.$i.'][item_input_dis]['.$key.']');
							}
							else{
								
								$input_dis = 0;
							}
							if($this->input->post('[split]['.$i.'][ptax]'))
							{
							$tax_type = $this->input->post('[split]['.$i.'][tax_type]');

							  if($tax_type != 0){

							   $itemtax = $this->site->calculateOrderTax($this->input->post('[split]['.$i.'][ptax]'), ($postData['split'][$i]['subtotal'][$key]-($offer_dis ? $offer_dis:0) -($input_dis ? $input_dis:0)-($postData['split'][$i]['item_discount'][$key])));

							   $sub_val =$postData['split'][$i]['subtotal'][$key];

							  }
							  else
							  {
							  	$default_tax = $this->site->calculateOrderTax($this->input->post('[split]['.$i.'][ptax]'), ($postData['split'][$i]['subtotal'][$key]-($offer_dis ? $offer_dis:0) -($input_dis ? $input_dis:0)-($postData['split'][$i]['item_discount'][$key])));

							  	$final_val = ($postData['split'][$i]['subtotal'][$key]-($offer_dis ? $offer_dis:0) -($input_dis ? $input_dis:0)-($postData['split'][$i]['item_discount'][$key]));

							  	$subval = $final_val/(($default_tax/$final_val)+1);

							  	$getTax = $this->site->getTaxRateByID($this->input->post('[split]['.$i.'][ptax]'));

							  	$itemtax = ($subval) * ($getTax->rate / 100);

							  	$sub_val =$postData['split'][$i]['subtotal'][$key];	
							  } 
							}else{
								$sub_val =$postData['split'][$i]['subtotal'][$key];
							}
							

							$splitData[$i][] = array(
								'recipe_name' => $split,
								'unit_price' => $postData['split'][$i]['unit_price'][$key],
								'net_unit_price' => $postData['split'][$i]['unit_price'][$key]*$postData['split'][$i]['quantity'][$key],
								'warehouse_id' => $warehouse_id,
								'recipe_type' => $postData['split'][$i]['recipe_type'][$key],
								'quantity' => $postData['split'][$i]['quantity'][$key],
								'recipe_id' => $postData['split'][$i]['recipe_id'][$key],
								'recipe_code' => $postData['split'][$i]['recipe_code'][$key],
								'discount' => $postData['split'][$i]['item_discount_id'][$key],
								
								'item_discount' => $postData['split'][$i]['item_discount'][$key],
								'off_discount' => $offer_dis ? $offer_dis:0,
								'input_discount' => $input_dis ? $input_dis:0,
								'tax_type' => $this->input->post('[split]['.$i.'][tax_type]'), 
								'tax' => $itemtax,	
								'subtotal' => $sub_val,

								/*'subtotal' => $postData['split'][$i]['subtotal'][$key]-(($input_dis ? $input_dis:0)-($offer_dis ? $offer_dis:0)-($postData['split'][$i]['item_discount'][$key]+$itemtax)),*/
							);
						}
						if($this->input->post('[split]['.$i.'][order_discount_input]')){
						    $cus_discount_type = $customer_discount;
						    $cus_discount_val ='';
						    if($customer_discount=="customer"){
							$cus_discount_val = $this->input->post('[split]['.$i.'][order_discount_input]').'%';
						    }else if($customer_discount=="manual"){
							$cus_discount_val = $this->input->post('[split]['.$i.'][order_discount_input]');
						    }
						}else{
						    $cus_discount_val ='';$cus_discount_type='';
						}

						$billData[$i] = array(
									'reference_no' => $this->input->post('[split]['.$i.'][reference_no]'),
									'created_on' => date('Y-m-d H:i:s'),									
									'date' => $this->site->getTransactionDate(),
									'customer_id' => $this->input->post('[split]['.$i.'][customer_id]'),
									'customer' => $this->input->post('[split]['.$i.'][customer]'),
									'biller' => $this->input->post('[split]['.$i.'][biller]'),
									'biller_id' => $this->input->post('[split]['.$i.'][biller_id]'),
									'total_items' => $this->input->post('[split]['.$i.'][total_item]'),
									'total' => $this->input->post('[split]['.$i.'][total_price]'),
									'total_tax' => $this->input->post('[split]['.$i.'][tax_amount]'),
									'tax_type' => $this->input->post('[split]['.$i.'][tax_type]'), 
									'tax_id' => $this->input->post('[split]['.$i.'][ptax]'),
									'total_discount' => (($this->input->post('[split]['.$i.'][itemdiscounts]'))+($this->input->post('[split]['.$i.'][offer_dis]'))+($this->input->post('[split]['.$i.'][discount_amount]'))+($this->input->post('[split]['.$i.'][off_discount]')? $this->input->post('[split]['.$i.'][off_discount]') : 0)),
									'grand_total' => $this->input->post('[split]['.$i.'][grand_total]'),
									'round_total' => $this->input->post('[split]['.$i.'][round_total]'),
									'bill_type' => $bill_type,
									'delivery_person_id' => $delivery_person,
									'order_discount_id' => $this->input->post('[split]['.$i.'][tot_dis_id]')? $this->input->post('[split]['.$i.'][tot_dis_id]') : NULL,
									'warehouse_id' => $warehouse_id,
									'discount_type'=>$cus_discount_type,
									'discount_val'=>$cus_discount_val,
									
								);
						
						
						
						}
//die;	
						$sales_total = array_column($billData, 'grand_total');
						$sales_total = array_sum($sales_total);
						
											
						 $response = $this->biller_api->InsertBillorder($order_data, $order_item, $billData,$splitData, $sales_total, $delivery_person,$timelog_array, $notification_array,$order_item_id);
						
						if($response == 1)
						{	
							$result = array( 'status'=> true , 'message'=> lang('biller_generator_in_data'),'message_khmer'=> html_entity_decode(lang('biller_generator_in_data_khmer')), 'data' => $data);					
						}else{
							$result = array( 'status'=> false , 'message'=> lang('biller_is_not_generator'),'message_khmer'=> html_entity_decode(lang('biller_is_not_generator_khmer')));	
						}
							
				
			}else{
				$result = array( 'status'=> false ,  'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
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
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		
		$order_type = $this->input->post('order_type');
		$bill_type = $this->input->post('bill_type');
		$table_id = $this->input->post('table_id');
		$split_id = $this->input->post('split_id');
		$bils = $this->input->post('bils');

		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		$this->form_validation->set_rules('order_type', $this->lang->line("order_type"), 'required');
		$this->form_validation->set_rules('bill_type', $this->lang->line("bill_type"), 'required');
		$this->form_validation->set_rules('table_id', $this->lang->line("table_id"), 'required');
		$this->form_validation->set_rules('split_id', $this->lang->line("split_id"), 'required');
		$this->form_validation->set_rules('bils', $this->lang->line("bils"), 'required');
		$this->form_validation->set_rules('warehouse_id', $this->lang->line("warehouse_id"), 'required');
		$this->form_validation->set_rules('user_id', $this->lang->line("user_id"), 'required');
		
		if ($this->form_validation->run() == true) {
			
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){	
				$data = $this->biller_api->getBil($table_id, $split_id, $user_id);
				$data['order_type'] = $order_type;
				$data['bill_type'] = $bill_type;
				$data['bils'] = $bils;
				$data['table_id'] = $table_id;
				$data['split_id'] = $split_id;
				$data['tax_rates'] = $this->site->getAllTaxRates();
				$data['delivery_person'] = $this->site->getDeliveryPersonall($warehouse_id);
				$data['possettings'] = $this->biller_api->getPOSSettings();
				$data['settings'] = $this->biller_api->getSettings();
	
							
				$result = array( 'status'=> true , 'message'=> lang('biller_generator_in_data'),'message_khmer'=> html_entity_decode(lang('biller_generator_in_data_khmer')), 'data' => $data);
			}else{
				$result = array( 'status'=> false ,  'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
			}
			
		}else{
			$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));	
		}
		
		$this->response($result);
		
	}
	
	public function list_post()
	{
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		
		$order_type = $this->input->post('order_type');

		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		$this->form_validation->set_rules('order_type', $this->lang->line("order_type"), 'required');
		$this->form_validation->set_rules('warehouse_id', $this->lang->line("warehouse_id"), 'required');
		$this->form_validation->set_rules('user_id', $this->lang->line("user_id"), 'required');
		
		if ($this->form_validation->run() == true) {
			
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){	
							
				$data = $this->biller_api->getAllSalesWithbiller($order_type, $warehouse_id);							
				$result = array( 'status'=> true , 'message'=> lang('biller_list_in_data'),'message_khmer'=> html_entity_decode(lang('biller_list_in_data_khmer')), 'data' => $data);
			}else{
				$result = array( 'status'=> false ,  'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
			}
			
		}else{
			$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));	
		}
		
		$this->response($result);
		
	}
	
	public function consolidatedlist_post()
	{
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');

		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		$this->form_validation->set_rules('warehouse_id', $this->lang->line("warehouse_id"), 'required');
		$this->form_validation->set_rules('user_id', $this->lang->line("user_id"), 'required');
		
		if ($this->form_validation->run() == true) {
			
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){	
							
				$dinein = $this->biller_api->DINEgetAllSalesWithbiller($warehouse_id);	
				$bbq = $this->biller_api->BBQgetAllSalesWithbiller($warehouse_id);	
				$consolidated = $this->biller_api->CONgetAllSalesWithbiller($warehouse_id);	
				$data = array();
				if(!empty($dinein)){
					$data  = array_merge($dinein, $data);
					
				}
				if(!empty($bbq)){
					$data  = array_merge($bbq, $data);
				}
				if(!empty($consolidated)){
					$data  = array_merge($consolidated, $data);
				}
				$result = array( 'status'=> true , 'message'=> lang('biller_list_in_data'),'message_khmer'=> html_entity_decode(lang('biller_list_in_data_khmer')), 'data' => $data);
			}else{
				$result = array( 'status'=> false ,  'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
			}
			
		}else{
			$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));	
		}
		
		$this->response($result);
		
	}
	
	public function consolidatedbildata_post()
	{
			
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		
		$consolidated_id = $this->input->post('consolidated_id');
		$sales_id = $this->input->post('sales_id');
		$taxation = $this->possettings->taxation_report_settings;
		
		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		$this->form_validation->set_rules('sales_id', $this->lang->line("sales_id"), 'required');
		$this->form_validation->set_rules('warehouse_id', $this->lang->line("warehouse_id"), 'required');
		$this->form_validation->set_rules('user_id', $this->lang->line("user_id"), 'required');
		
		if ($this->form_validation->run() == true) {
			
			$devices_check = $this->site->devicesCheck($api_key);

			$customer_id = $this->biller_api->getcustomerbysaleid($sales_id);				
			$loyalty_available = $this->site->getCheckLoyaltyAvailable($customer_id);

			if($devices_check == $devices_key){	
				
				if($consolidated_id == 1){
					$data = $this->biller_api->CONgetAllBilling($sales_id);	
				}else{
					$data = $this->biller_api->getAllBilling($sales_id);	
				}
				
				$check = array_unique($data[0]->check_order);
				foreach($check as $check_order){
					if($check_order == 1){
						$dine_bil = 1;
					}elseif($check_order == 4){
						$bbq_bil = 1;
					}
				}
				$settings = $this->site->get_setting();
				$customer_discount = $this->site->GetAllcostomerDiscounts();
				$bbq_discount = $this->site->GetAllBBQDiscounts();
				if(!empty($data[0]->customer_request)){
					$customer_request = $data[0]->customer_request;
				}else{
					$customer_request = array();
				}
				
				$is_unique = $this->site->is_uniqueDiscountExist();
				if(!empty($is_unique)){
					
					/*if(!empty($data[0]->sales_order)){
						
						$table_id = $data[0]->sales_order['sales_table_id'];
						$split_id = $data[0]->sales_order['sales_split_id']; 
						$waiter_id = 1;
					}*/
					$automatic = 1;
					//$item_data = $this->customer_api->getBil($table_id, $split_id, $waiter_id);
					
					foreach($item_data['items'] as $item){
						$item->id = $item->recipe_id;
						$simple_discount[] = $this->site->CalculatesimpleDiscount($item);
							
					}
					$automatic_discount = 0;
					
				}else{
					$automatic = 0;
					$automatic_discount = 0;
				}
				
				$credit = 0; 
				 $loyalty_available = "".$loyalty_available."";
				 $credit = "".$credit."";;
				if(!empty($data)){
					$result = array( 'status'=> true , 'message'=> lang('biller_list_in_data'),'message_khmer'=> html_entity_decode(lang('biller_list_in_data_khmer')), 'data' => $data, 'discount_popup'=>$this->possettings->discount_popup_screen_in_bill_print,'customer_request' => $customer_request, 'dine_bil' => $dine_bil ? $dine_bil : 0, 'bbq_bil' => $bbq_bil ? $bbq_bil : 0, 'automatic' => $automatic, 'automatic_discount' => $automatic_discount, 'is_unique' => $is_unique, 'taxation' => $taxation, 'loyalty_available' => $loyalty_available, 'credit' => $credit);
					
					if($bbq_discount && $settings->bbq_discount!="none"){
						$result['bbq_discount'] = $bbq_discount;
					}
					if($customer_discount && $settings->customer_discount!="none"){
						$result['customer_discount'] = $customer_discount;
					}
				}else{
					$result = array( 'status'=> false , 'message'=> lang('biller_list_is_empty'),'message_khmer'=> html_entity_decode(lang('biller_list_is_empty_khmer')));
				}
				
			}else{
				$result = array( 'status'=> false ,  'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
			}
			
		}else{
			$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));	
		}
		
		$this->response($result);
		
	}
	
	public function consolidatedcashierdiscount_post(){
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		
		
		$bil_id = $this->input->post('bil_id');
		$consolidated_id = $this->input->post('consolidated_id');
		
		$customer_discount_id = $this->input->post('customer_discount_id');
		$bbq_discount_id = $this->input->post('bbq_discount_id');
		$dine_bil = $this->input->post('dine_bil');
		$bbq_bil = $this->input->post('bbq_bil');

		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		$this->form_validation->set_rules('bil_id', $this->lang->line("bil_id"), 'required');
		$this->form_validation->set_rules('warehouse_id', $this->lang->line("warehouse_id"), 'required');
		$this->form_validation->set_rules('user_id', $this->lang->line("user_id"), 'required');
		
		$setting = $this->biller_api->getSettings();
		
		if ($this->form_validation->run() == true) {
			
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){	
				
				$split_id = $this->biller_api->getBillingsplitdata($bil_id, $consolidated_id);
				
				$split_data = $this->biller_api->getSplitBils($split_id[0]->sales_split_id);
				
				foreach($split_data as $row){
					
					if($row->sales_type_id == 1){
						
						$cus_result = $this->biller_api->getDineinCustomerDiscount($row->bil_id);
						$cus_bils = $this->biller_api->getDINEINBils($row->bil_id);
						$cus_bils_item = $this->biller_api->getDINEINBilitem($row->bil_id);
						$cus_discount_data = $this->biller_api->getDINEINCUSDisIDBy($customer_discount_id);
		 
							 $cus_customer_request_id = $cus_result->customer_request_id;
							 $cus_request_array = array(
								'customer_discount_val' => $cus_discount_data->id
							 );
						  
							  $cus_final_total = $cus_bils->total;
						   $cus_discount_data->discount_val;
							if(!empty($cus_discount_data->discount_val)){
								$cus_discount_data->discount_val = $cus_discount_data->discount_val.'%';
							}else{
								$cus_discount_data->discount_val = $cus_discount_data->discount_val;
							}
						  
						  $cus_total_discount = $this->site->calculateDiscount($cus_discount_data->discount_val, $cus_final_total);
						  $cus_total_discount_total = ($cus_final_total - $cus_total_discount);
						  $cus_total_tax = $this->site->calculateOrderTax($cus_bils->tax_id, $cus_total_discount_total);
						  
						  if($setting->tax_type == 1){
						 	 $cus_grand_total = $cus_total_discount_total + $cus_total_tax;
						  }else{
							  $cus_grand_total = $cus_total_discount_total; 
						  }
						  $cus_round_total = $cus_grand_total;
						  $cus_customer_discount_id = $cus_discount_data->id;
						  $cus_bils_update = array(
							'total' => $cus_bils->total,
							'total_discount' => $cus_total_discount,
							'customer_discount_id' => $cus_customer_discount_id,
							'tax_id' => $cus_bils->tax_id,
							'total_tax' => $cus_total_tax,
							'grand_total' => $cus_grand_total,
							'round_total' => $cus_round_total
							
						  );
						 
						  foreach($cus_bils_item as $cus_item){
							  $cus_net = ($cus_item->unit_price * $cus_item->quantity);
							  
							   $cus_input_discount = $this->site->calculateDiscount($cus_discount_data->discount_val, $cus_net);
							   $cus_net = $cus_net - $cus_input_discount;
							   $cus_tax = $this->site->calculateOrderTax($cus_bils->tax_id, $cus_net);
						  
							  $cus_item_updates[] = array(
								'input_discount' => $cus_input_discount,
								'tax' => $cus_tax,
								'bil_id' => $cus_item->bil_id,
								'id' => $cus_item->id
							  );
							  $cus_bilitem_ids[] = array(
									'id' => $cus_item->id
							  );
						  }
						  
						 
							$cus_return =  $this->biller_api->DINEINupdate_bil($cus_bils_update, $row->bil_id, $cus_item_updates, $cus_bilitem_ids, $cus_request_array, $cus_customer_request_id);
							
							
							$amount[] = $this->sma->formatDecimal($cus_grand_total);
							
						
	  
					}elseif($row->sales_type_id == 4){
						
						$bbq_result = $this->biller_api->getBBQDiscount($row->bil_id);
					  $bbq_bils = $this->biller_api->getBBQBils($row->bil_id);
					  $bbq_bils_item = $this->biller_api->getBBQBilitem($row->bil_id);
					  $bbq_bils_cover = $this->biller_api->getBBQBilcover($row->bil_id);
					  $bbq_discount_data = $this->biller_api->getBBQCUSDisIDBy($bbq_discount_id);
					  
					
					
					$bbq_customer_request_id = $bbq_result->customer_request_id;
					$bbq_request_array = array(
					'bbq_discount_val' => $bbq_discount_data->id
					);
					
					$bbq_final_total = $bbq_bils->total;
					
					if(!empty($bbq_discount_data->discount)){
					$bbq_discount_data->discount = $bbq_discount_data->discount.'%';
					}else{
					$bbq_discount_data->discount = $bbq_discount_data->discount;
					}
					
					$bbq_total_discount = $this->site->calculateDiscount($bbq_discount_data->discount, $bbq_final_total);
					$bbq_total_discount_total = ($bbq_final_total - $bbq_total_discount);
					$bbq_total_tax = $this->site->calculateOrderTax($bbq_bils->tax_id, $bbq_total_discount_total);
					
					if($setting->tax_type == 1){
						$bbq_grand_total = $bbq_total_discount_total + $bbq_total_tax;
					}else{
						$bbq_grand_total = $bbq_total_discount_total;
					}
					
					$bbq_round_total = $bbq_grand_total;
					$bbq_customer_discount_id = $bbq_discount_data->id;
					$bbq_bils_update = array(
					'total' => $bbq_bils->total,
					'total_discount' => $bbq_total_discount,
					'customer_discount_id' => $bbq_customer_discount_id,
					'tax_id' => $bbq_bils->tax_id,
					'total_tax' => $bbq_total_tax,
					'grand_total' => $bbq_grand_total,
					'round_total' => $bbq_round_total
					
					);
					
					foreach($bbq_bils_item as $bbq_item){
					$bbq_net = ($bbq_item->unit_price * $bbq_item->quantity);
					
					$bbq_input_discount = $this->site->calculateDiscount($bbq_discount_data->discount_val, $bbq_net);
					$bbq_net = $bbq_net - $bbq_input_discount;
					$bbq_tax = $this->site->calculateOrderTax($bbq_bils->tax_id, $bbq_net);
					
					$bbq_item_updates[] = array(
					'input_discount' => $bbq_input_discount,
					'tax' => $bbq_tax,
					'bil_id' => $bbq_item->bil_id,
					'id' => $bbq_item->id
					);
					$bbq_bilitem_ids[] = array(
					'id' => $bbq_item->id
					);
					}
					
					
					
					$bbq_return =  $this->biller_api->BBQupdate_bil($bbq_bils_update, $row->bil_id, $bbq_item_updates, $bbq_bilitem_ids, $bbq_request_array, $bbq_customer_request_id);
					$amount[] = $this->sma->formatDecimal($bbq_grand_total);
					
					
					}
					
				}
				
				
				if(!empty($amount)){
				
					
					if($consolidated_id == 1){
						$data = $this->biller_api->CONgetAllBillingitem($bil_id);	
					}else{
						$data = $this->biller_api->getAllBillingitem($bil_id);	
					}
					
					$check = array_unique($data[0]->check_order);
					foreach($check as $check_order){
						if($check_order == 1){
							$dine_bil = 1;
						}elseif($check_order == 4){
							$bbq_bil = 1;
						}
					}
					
					if(!empty($data)){
						$result = array( 'status'=> true , 'message'=> lang('biller_list_item_in_data'),'message_khmer'=> html_entity_decode(lang('biller_list_item_in_data_khmer')), 'data' => $data, 'dine_bil' => $dine_bil ? $dine_bil : 0, 'bbq_bil' => $bbq_bil ? $bbq_bil : 0);
					}else{
						$result = array( 'status'=> false , 'message'=> lang('biller_list_item_is_empty'),'message_khmer'=> html_entity_decode(lang('biller_list_item_is_empty_khmer')));
					}
					
				}else{
					$result = array( 'status'=> false , 'message'=> lang('discount_not_updated'),'message_khmer'=> html_entity_decode(lang('discount_not_updated_khmer')));	
				}
				
				
			}else{
				$result = array( 'status'=> false ,  'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
			}
			
		}else{
			$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));	
		}
		
		$this->response($result);
		
	}
	
	
	
	public function bildata_post()
	{
			
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		
		$sales_id = $this->input->post('sales_id');

		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		$this->form_validation->set_rules('sales_id', $this->lang->line("sales_id"), 'required');
		$this->form_validation->set_rules('warehouse_id', $this->lang->line("warehouse_id"), 'required');
		$this->form_validation->set_rules('user_id', $this->lang->line("user_id"), 'required');
		
		if ($this->form_validation->run() == true) {
			$taxation = $this->possettings->taxation_report_settings;
			$devices_check = $this->site->devicesCheck($api_key);
			$customer_id = $this->biller_api->getcustomerbysaleid($sales_id);				
			$loyalty_available = $this->site->getCheckLoyaltyAvailable($customer_id);

			if($devices_check == $devices_key){	
					
				$data = $this->biller_api->getAllBilling($sales_id);	
				if(!empty($data)){
					$result = array( 'status'=> true , 'message'=> lang('biller_list_in_data'),'message_khmer'=> html_entity_decode(lang('biller_list_in_data_khmer')), 'data' => $data, 'taxation' => $taxation, 'loyalty_available' => $loyalty_available, 'credit' => 0);
				}else{
					$result = array( 'status'=> false , 'message'=> lang('biller_list_is_empty'),'message_khmer'=> html_entity_decode(lang('biller_list_is_empty_khmer')));
				}
				
			}else{
				$result = array( 'status'=> false ,  'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
			}
			
		}else{
			$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));	
		}
		
		$this->response($result);
		
	}
	
	public function consolidatedbilitem_post()
	{
			/*echo "<pre>";
			print_r($this->input->post());die;*/
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		
		
		$bil_id = $this->input->post('bil_id');
		$consolidated_id = $this->input->post('consolidated_id');

		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		$this->form_validation->set_rules('bil_id', $this->lang->line("bil_id"), 'required');
		$this->form_validation->set_rules('warehouse_id', $this->lang->line("warehouse_id"), 'required');
		$this->form_validation->set_rules('user_id', $this->lang->line("user_id"), 'required');
		
		if ($this->form_validation->run() == true) {
			
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){	
				
				if($consolidated_id == 1){
					$data = $this->biller_api->CONgetAllBillingitem($bil_id);	
				}else{
					$data = $this->biller_api->getAllBillingitem($bil_id);	
				}
				
				$check = array_unique($data[0]->check_order);
				foreach($check as $check_order){
					if($check_order == 1){
						$dine_bil = 1;
					}elseif($check_order == 4){
						$bbq_bil = 1;
					}
				}
				
				if(!empty($data)){
					$result = array( 'status'=> true , 'message'=> lang('biller_list_item_in_data'),'message_khmer'=> html_entity_decode(lang('biller_list_item_in_data_khmer')), 'data' => $data, 'dine_bil' => $dine_bil ? $dine_bil : 0, 'bbq_bil' => $bbq_bil ? $bbq_bil : 0);
				}else{
					$result = array( 'status'=> false , 'message'=> lang('biller_list_item_is_empty'),'message_khmer'=> html_entity_decode(lang('biller_list_item_is_empty_khmer')));
				}
				
			}else{
				$result = array( 'status'=> false ,  'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
			}
			
		}else{
			$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));	
		}
		
		$this->response($result);
		
	}
	
	public function bilitem_post()
	{
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		
		$bil_id = $this->input->post('bil_id');

		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		$this->form_validation->set_rules('bil_id', $this->lang->line("bil_id"), 'required');
		$this->form_validation->set_rules('warehouse_id', $this->lang->line("warehouse_id"), 'required');
		$this->form_validation->set_rules('user_id', $this->lang->line("user_id"), 'required');
		
		if ($this->form_validation->run() == true) {
			
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){	
					
				$data = $this->biller_api->getAllBillingitem($bil_id);	
				if(!empty($data)){
					$result = array( 'status'=> true , 'message'=> lang('biller_item_in_data'),'message_khmer'=> html_entity_decode(lang('biller_item_in_data_khmer')), 'data' => $data);
				}else{
					$result = array( 'status'=> true , 'message'=> lang('biller_item_is_empty'),'message_khmer'=> html_entity_decode(lang('biller_item_is_empty_khmer')));
				}
				
			}else{
				$result = array( 'status'=> false ,  'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
			}
			
		}else{
			$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));	
		}
		
		$this->response($result);
		
	}
	
	public function bilcancel_post()
	{
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		
		$sales_id = $this->input->post('sales_id');
		$cancel_remarks = $this->input->post('cancel_remarks');
		
		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		$this->form_validation->set_rules('sales_id', $this->lang->line("sales_id"), 'required');
		$this->form_validation->set_rules('warehouse_id', $this->lang->line("warehouse_id"), 'required');
		$this->form_validation->set_rules('user_id', $this->lang->line("user_id"), 'required');
		
		if ($this->form_validation->run() == true) {
			
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){	
							
				$notification_array['from_role'] = $group_id;
				$notification_array['insert_array'] = array(			
					'user_id' => $user_id,	
					'warehouse_id' => $warehouse_id,
					'created_on' => date('Y-m-d H:m:s'),
					'is_read' => 0
				);
					
				$data  = $this->biller_api->CancelSale($cancel_remarks, $sales_id, $user_id, $notification_array);
				
				if($data == TRUE){
					$result = array( 'status'=> true , 'message'=> lang('biller_cancel_has_been_success'),'message_khmer'=> html_entity_decode(lang('biller_cancel_has_been_success_khmer')));
				}else{
					$result = array( 'status'=> false , 'message'=> lang('biller_cancel_is_not_success'),'message_khmer'=> html_entity_decode(lang('biller_cancel_is_not_success_khmer')));
				}
				
			}else{
				$result = array( 'status'=> false ,  'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
			}
			
		}else{
			$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));	
		}
		
		$this->response($result);
		
	}
	
	public function add_post()
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

		/*$this->form_validation->set_rules('order_type', $this->lang->line("order_type"), 'required');
		$this->form_validation->set_rules('bill_type', $this->lang->line("bill_type"), 'required');
		$this->form_validation->set_rules('table_id', $this->lang->line("table_id"), 'required');
		$this->form_validation->set_rules('split_id', $this->lang->line("split_id"), 'required');
		$this->form_validation->set_rules('bils', $this->lang->line("bils"), 'required');
		$this->form_validation->set_rules('warehouse_id', $this->lang->line("warehouse_id"), 'required');
		$this->form_validation->set_rules('user_id', $this->lang->line("user_id"), 'required');*/
		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		if ($this->form_validation->run() == true) {
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){	
				
				$item_data = $this->biller_api->getBil($table_id, $split_id, $user_id);
				$k=0;
				foreach($item_data['items'][$k] as $item){
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
						
					$k++;
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
				if($this->input->post('other_discount_type') == '1'){
					$other_discount = $this->input->post('other_discount').'%';
				}else{
					$other_discount = $this->input->post('other_discount');
				}
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
				}
				
				$delivery_person = $this->input->post('delivery_person_id') ? $this->input->post('delivery_person_id') : 0;
				
				$bil_value = $this->input->post('bils');
				
				for($i=0; $i<$this->input->post('bils'); $i++){
					
					$total = array_sum($bil_total[$i]);
					$bil_total_count = count($item_data['items'][0][$i]);
					
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
							'bill_type' => $bill_type,
							'delivery_person_id' => $delivery_person,
							'warehouse_id' => $warehouse_id,
						);
					}
					
					$j=0;
					
					
					foreach($item_data['items'][0] as $itembil){
						
						$discount = $this->site->discountMultiple($itembil->recipe_id);
						
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
						
						$off_discount = $this->site->calculate_Discount($offer_discount, ($itembil->subtotal - $item_discount), $step_bil_1);
						$input_discount = $this->site->calculate_Discount($other_discount_total, ($itembil->subtotal - $item_discount - $off_discount), $step_bil_2);
						
						$itemtax = $this->site->calculateOrderTax($this->input->post('tax_id'), ($itembil->subtotal - $off_discount - $input_discount - $item_discount));
	
						
						$splitData[$i][] = array(
							'recipe_name' => $itembil->recipe_name,
							'unit_price' => $itembil->unit_price/$bil_value,
							'net_unit_price' => $itembil->net_unit_price/$bil_value,
							'warehouse_id' => $warehouse_id,
							'recipe_type' => $itembil->recipe_type,
							'quantity' => $itembil->quantity,
							'recipe_id' => $itembil->recipe_id,
							'recipe_code' => $itembil->recipe_code,
							'discount' => $discount[0],						
							'item_discount' => $item_discount/$bil_value,
							'off_discount' => $off_discount ? $off_discount/$bil_value : 0,
							'input_discount' => $input_discount ? $input_discount/$i : 0,
							'tax' => $itemtax ? $itemtax/$bil_value : 0,	
							'subtotal' => $itembil->subtotal/$bil_value,
						);
						$j++;
					}
						
				}
				
				
				$sales_total = array_column($billData, 'grand_total');
				$sales_total = array_sum($sales_total);
				
				$notification_array['from_role'] = $group_id;
				$notification_array['insert_array'] = array(
					'msg' => 'Waiter has been bil generator to '.$split_id,
					'type' => 'Bil generator ('.$split_id.')',
					'table_id' =>  $table_id,
					'role_id' => 8,
					'user_id' => $user_id,	
					'warehouse_id' => $warehouse_id,
					'created_on' => date('Y-m-d H:m:s'),
					'is_read' => 0
				);
				
			/*	echo '<pre>';
				print_r($order_data);
				print_r($order_item);
				print_r($billData);
				print_r($splitData);
				print_r($sales_total);
			
				die;*/
						
				$data = $this->biller_api->InsertBill($order_data, $order_item, $billData,$splitData, $sales_total, $delivery_person, $timelog_array, $notification_array);
							
				$result = array( 'status'=> true , 'message'=> lang('biller_generator_has_been_success_please_check_cashier'),'message_khmer'=> html_entity_decode(lang('biller_generator_has_been_success_please_check_cashier_khmer')));
			}else{
				$result = array( 'status'=> false ,  'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
			}
			
		}else{
			$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));	
		}
		
		$this->response($result);
		
	}
	
	
	public function payment_post()
	{
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		
		
		$bil_id = $this->input->post('bil_id');
		$total_pay = $this->input->post('total_pay'); 
		$total = $this->input->post('total'); 
		$balance = $total_pay - $total;
		$default_currency = $this->input->post('default_currency');
		
		
		$this->form_validation->set_rules('amount', $this->lang->line("amount"), 'required');
		$this->form_validation->set_rules('total_pay', $this->lang->line("total_pay"), 'required');
		$this->form_validation->set_rules('total', $this->lang->line("total"), 'required');
		$this->form_validation->set_rules('bil_id', $this->lang->line("bil_id"), 'required');
		$this->form_validation->set_rules('warehouse_id', $this->lang->line("warehouse_id"), 'required');
		$this->form_validation->set_rules('user_id', $this->lang->line("user_id"), 'required');
		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		
		if ($this->form_validation->run() == true) {
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){	
				
				$default_currency_data = $this->site->getCurrencyByID($default_currency);
				
				$result = $this->biller_api->getBilvalue($bil_id);	
				
				$amount_USD = $this->input->post('amount') ? $this->input->post('amount') : 0;
				$amount_KHR = $this->input->post('amount_khr') ? $this->input->post('amount_khr') : 0;
				
				foreach($currency as $currency_row){
					if($currency_row->code == 'KHR'){
						$multi_currency[] = array(
						
							'sale_id' => $result->sales_id ? $result->sales_id : 0,
							'bil_id' => $result->id ? $result->id : 0,
							'currency_id' => $currency_row->id,
							'currency_rate' => $currency_row->rate,
							'amount' => $amount_KHR,
						);
							
						
					}elseif($currency_row->code == 'USD'){
						$multi_currency[] = array(
						
							'sale_id' => $result->sales_id ? $result->sales_id : 0,
							'bil_id' => $result->id ? $result->id : 0,
							'currency_id' => $currency_row->id,
							'currency_rate' => $currency_row->rate,
							'amount' => $amount_USD,
						);
					}
				}
				
				$billid  = $result->id ? $result->id : 0;
				$salesid = $result->sales_id ? $result->sales_id : 0;
				$taxation = $this->input->post('taxation') ? $this->input->post('taxation') :0;

				$update_bill = array(
					'updated_at'            => date('Y-m-d H:i:s'),
					'created_by' 			=> $user_id,
					'total_pay'				=> $total_pay,
					'balance' 				=> $balance,
					'paid'                  => $total,
					'payment_status'        => 'Completed',
					'default_currency_code' => $default_currency_data->code,
					'default_currency_rate' => $default_currency_data->rate,
					'table_whitelisted'     => $taxation,
                );

                $sales_bill = array(
					'grand_total'           => $total,				
					'paid'                  => $total,
					'payment_status'		=>'Paid',
					'default_currency_code' => $default_currency_data->code,
					'default_currency_rate' => $default_currency_data->rate,
                );
				
				
				$notification_array['from_role'] = $this->session->userdata('group_id');
				$notification_array['insert_array'] = array(			
					'user_id' => $this->session->userdata('user_id'),	
					'warehouse_id' => $this->session->userdata('warehouse_id'),
					'created_on' => date('Y-m-d H:m:s'),
					'is_read' => 0
				);
				
				$payment[] = array(
				'date' => $this->site->getTransactionDate(),
                'sale_id'      => $result->sales_id ? $result->sales_id : 0,
                'bill_id'      => $result->id ? $result->id : 0,
                //'reference_no' => $this->input->post('reference_no'),
                'amount'       => $this->input->post('amount') ? $this->input->post('amount') : '',
				'amount_exchange'   => $this->input->post('amount_khr') ? $this->input->post('amount_khr') : '',
                'pos_paid'     => $this->input->post('amount') ? $this->input->post('amount') : '',
                'pos_balance'  => round($balance, 3),
                 'paid_by'     => $this->input->post('paid_by') ? $this->input->post('paid_by') : '',
                 'cheque_no'   => $this->input->post('cheque_no') ? $this->input->post('cheque_no') : '',
                 'cc_no'       => $this->input->post('cc_no') ? $this->input->post('cc_no') : '',
                 'cc_holder'   => $this->input->post('cc_holder') ? $this->input->post('cc_holder') : '',
                 'cc_month'    => $this->input->post('cc_month') ? $this->input->post('cc_month') : '',
                 'cc_year'     => $this->input->post('cc_year') ? $this->input->post('cc_year') : '',
                 'cc_type'     => $this->input->post('cc_type') ? $this->input->post('cc_type') : '',
                 // 'cc_cvv2'   => $this->input->post('cc_cvv2'),
                 'sale_note'   => $this->input->post('payment_note') ? $this->input->post('payment_note') : '',
                 'staff_note'   => $this->input->post('payment_note') ? $this->input->post('payment_note') : '',
                 'payment_note' => $this->input->post('payment_note') ? $this->input->post('payment_note') : '',
                 'created_by'   => $user_id,
                 'type'         => 'received',
            );
					
				$response = $this->biller_api->insertPayment($update_bill,$billid,$payment,$multi_currency,$salesid,$sales_bill, $order_split_id, $notification_array,$taxation);
				if($response){
					
					
					$result = array( 'status'=> true , 'message'=> lang('payment_has_been_success_please_check_cashier'),'message_khmer'=> html_entity_decode(lang('payment_has_been_success_please_check_cashier_khmer')));
					
				}else{
					$result = array( 'status'=> true , 'message'=> lang('payment_has_been_not_success'),'message_khmer'=> html_entity_decode(lang('payment_has_been_not_success_khmer')));
				}
				
			}else{
				$result = array( 'status'=> false ,  'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
			}
			
		}else{
			$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));	
		}
		
		$this->response($result);
		
	}
	
	
	public function consolidatedpayment_post()
	{
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		
		
		$bil_id = $this->input->post('bil_id');
		$total_pay = $this->input->post('total_pay'); 
		$order_split_id = $this->input->post('split_id');
		$total = $this->input->post('total'); 
		$balance = $total_pay - $total;
		$default_currency = $this->input->post('default_currency');
		
		$dine_bil = $this->input->post('dine_bil');
		$bbq_bil = $this->input->post('bbq_bil');
		
		$this->form_validation->set_rules('amount', $this->lang->line("amount"), 'required');
		$this->form_validation->set_rules('total_pay', $this->lang->line("total_pay"), 'required');
		$this->form_validation->set_rules('total', $this->lang->line("total"), 'required');
		$this->form_validation->set_rules('bil_id', $this->lang->line("bil_id"), 'required');
		$this->form_validation->set_rules('warehouse_id', $this->lang->line("warehouse_id"), 'required');
		$this->form_validation->set_rules('user_id', $this->lang->line("user_id"), 'required');
		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		$currency = $this->site->getAllCurrencies();
		
		if ($this->form_validation->run() == true) {
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){	
				
				
				if($dine_bil == 1 && $bbq_bil == 1){
					
					$default_currency_data = $this->site->getCurrencyByID($default_currency);
					//$result = $this->biller_api->getBilvalue($bil_id);	
					$amount_USD = $this->input->post('amount') ? $this->input->post('amount') : 0;
					$amount_KHR = $this->input->post('amount_khr') ? $this->input->post('amount_khr') : 0;
					
					$billid  = $this->biller_api->getBilID($order_split_id);
					$salesid = $this->biller_api->getsalesID($order_split_id);
					
					foreach($billid as $billid_row){
						$billid_val[] = $billid_row->id;
						$salesid_val[] = $billid_row->sales_id;
					}
					
					
					$p = isset($_POST['paid_by']) ? sizeof($_POST['paid_by']) : 0;
					
					
					$notification_array['from_role'] = $group_id;
					$notification_array['insert_array'] = array(			
						'user_id' => $user_id,	
						'warehouse_id' => $warehouse_id,
						'created_on' => date('Y-m-d H:m:s'),
						'is_read' => 0
					);
					
					
					
					if($amount_USD){
						$payment[] = array(							
							'date' => $this->site->getTransactionDate(),
							//'sale_id'      => $result->sales_id ? $result->sales_id : 0,
							//'bill_id'      => $result->id ? $result->id : 0,
							//'reference_no' => $this->input->post('reference_no'),
							'amount'       => $this->input->post('amount') ? $this->input->post('amount') : 0,
							'amount_exchange'   => 0,
							'exchange_enable' =>  0,
							'pos_paid'     => $this->input->post('amount') ? $this->input->post('amount') : '',
							'pos_balance'  => round($balance, 3),
							 'paid_by'     => $this->input->post('paid_by') ? $this->input->post('paid_by') : '',
							 'cheque_no'   => $this->input->post('cheque_no') ? $this->input->post('cheque_no') : '',
							 'cc_no'       => $this->input->post('cc_no') ? $this->input->post('cc_no') : '',
							 'cc_holder'   => $this->input->post('cc_holder') ? $this->input->post('cc_holder') : '',
							 'cc_month'    => $this->input->post('cc_month') ? $this->input->post('cc_month') : '',
							 'cc_year'     => $this->input->post('cc_year') ? $this->input->post('cc_year') : '',
							 'cc_type'     => $this->input->post('cc_type') ? $this->input->post('cc_type') : '',
							 // 'cc_cvv2'   => $this->input->post('cc_cvv2'),
							 'sale_note'   => $this->input->post('payment_note') ? $this->input->post('payment_note') : '',
							 'staff_note'   => $this->input->post('payment_note') ? $this->input->post('payment_note') : '',
							 'payment_note' => $this->input->post('payment_note') ? $this->input->post('payment_note') : '',
							 'created_by'   => $user_id,
							 'type'         => 'received',
						);
					}
					if(!empty($amount_KHR)){
						$amount_ex = $amount_KHR * 0.000244;
						$payment[] = array(							
							'date' => $this->site->getTransactionDate(),
							'amount'       => $amount_ex ? $amount_ex:0,
							'amount_exchange'   => $amount_KHR ? $amount_KHR:0,
							'exchange_enable' => 1,
							'pos_paid'     => $this->input->post('amount') ? $this->input->post('amount') : '',
							'pos_balance'  => round($balance, 3),
							 'paid_by'     => $this->input->post('paid_by') ? $this->input->post('paid_by') : '',
							 'cheque_no'   => $this->input->post('cheque_no') ? $this->input->post('cheque_no') : '',
							 'cc_no'       => $this->input->post('cc_no') ? $this->input->post('cc_no') : '',
							 'cc_holder'   => $this->input->post('cc_holder') ? $this->input->post('cc_holder') : '',
							 'cc_month'    => $this->input->post('cc_month') ? $this->input->post('cc_month') : '',
							 'cc_year'     => $this->input->post('cc_year') ? $this->input->post('cc_year') : '',
							 'cc_type'     => $this->input->post('cc_type') ? $this->input->post('cc_type') : '',
							 // 'cc_cvv2'   => $this->input->post('cc_cvv2'),
							 'sale_note'   => $this->input->post('payment_note') ? $this->input->post('payment_note') : '',
							 'staff_note'   => $this->input->post('payment_note') ? $this->input->post('payment_note') : '',
							 'payment_note' => $this->input->post('payment_note') ? $this->input->post('payment_note') : '',
							 'created_by'   => $user_id,
							 'type'         => 'received',
						);
					}
					
				
					$alacat = 0;
					foreach($payment as $key =>  $pay){
					if($alacat <= 0){
						if($billid[0]->grand_total <= $pay['amount']){
							$bil_id = $billid[1]->id;
							$sale_id = $billid[1]->sales_id;
							
							$alacat -= $this->sma->formatDecimal($billid[0]->grand_total)  - $this->sma->formatDecimal($pay['amount']);
						}elseif($billid[0]->grand_total > $pay['amount']){
							$alacat += $this->sma->formatDecimal($billid[0]->grand_total)  - $this->sma->formatDecimal($pay['amount']);
							$bil_id = $billid[1]->id;
							$sale_id = $billid[1]->sales_id;
						}
						
						
						$consolidatedpayment[] = array(
							'date' => $pay['date'],
							'amount' => $this->sma->formatDecimal($billid[0]->grand_total), 
							'amount_exchange' => 0,
							'bill_id' => $billid[0]->id,
							 'sale_id' => $billid[0]->sales_id,
							 'exchange_enable' => $pay['exchange_enable'],
							 'paid_by'     => $pay['paid_by'],
							 'cheque_no'   => $pay['cheque_no'],
							 'cc_no'       => $pay['cc_no'],
							 'cc_holder'   => $pay['cc_holer'],
							 'cc_month'    => $pay['cc_month'],
							 'cc_year'     => $pay['cc_year'],
							 'cc_type'     => $pay['cc_type'],
							  'sale_note'   => $pay['sale_note'] != NULL ? $pay['sale_note'] : '',
							 'staff_note'   => $pay['staffnote'] != NULL ? $pay['staffnote'] : '',
							 'payment_note' => $pay['payment_note'] != NULL ? $pay['payment_note'] : '',
							 'created_by'   => $user_id,
							 'type'         => 'received',
						);
						if($pay['exchange_enable'] == 1){
							
							$amount_exchange = $alacat / 0.000244;
							$alacat = 0;
						}else{
							$amount_exchange = 0;
							$alacat = $this->sma->formatDecimal($alacat);
						}
						
						$consolidatedpayment[] = array(
							'date' => $pay['date'],
							'amount' => $this->sma->formatDecimal($alacat),
							'amount_exchange' => $amount_exchange,
							'bill_id' => $bil_id,
							 'sale_id' => $sale_id,
							'exchange_enable' => $pay['exchange_enable'],
							 'paid_by'     => $pay['paid_by'],
							 'cheque_no'   => $pay['cheque_no'],
							 'cc_no'       => $pay['cc_no'],
							 'cc_holder'   => $pay['cc_holer'],
							 'cc_month'    => $pay['cc_month'],
							 'cc_year'     => $pay['cc_year'],
							 'cc_type'     => $pay['cc_type'],
							 'sale_note'   => $pay['sale_note'] != NULL ? $pay['sale_note'] : '',
							 'staff_note'   => $pay['staffnote'] != NULL ? $pay['staffnote'] : '',
							 'payment_note' => $pay['payment_note'] != NULL ? $pay['payment_note'] : '',
							 'created_by'   => $user_id,
							 'type'         => 'received',
						);
						
					}else{
						if($pay['exchange_enable'] == 1){
							$amount_exchange = $pay['amount'] / 0.000244;
							$pay_amount = 0;
						}else{
							$amount_exchange = 0;	
							$pay_amount = $this->sma->formatDecimal($pay['amount']);
						}
						$consolidatedpayment[] = array(
							'date' => $pay['date'],
							'amount' => $pay_amount,
							'amount_exchange' => $amount_exchange,
							'bill_id' => $billid[1]->id,
							 'sale_id' => $billid[1]->sales_id,
							'exchange_enable' => $pay['exchange_enable'],
							 'paid_by'     => $pay['paid_by'],
							 'cheque_no'   => $pay['cheque_no'],
							 'cc_no'       => $pay['cc_no'],
							 'cc_holder'   => $pay['cc_holer'],
							 'cc_month'    => $pay['cc_month'],
							 'cc_year'     => $pay['cc_year'],
							 'cc_type'     => $pay['cc_type'],
							 'sale_note'   => $pay['sale_note'] != NULL ? $pay['sale_note'] : '',
							 'staff_note'   => $pay['staffnote'] != NULL ? $pay['staffnote'] : '',
							 'payment_note' => $pay['payment_note'] != NULL ? $pay['payment_note'] : '',
							 'created_by'   => $user_id,
							 'type'         => 'received',
						);
					}
				}
				
					foreach($consolidatedpayment as $key => $consolidated){
					
					
					
					foreach($currency as $currency_row){
						
						if($currency_row->code == 'USD'){
							
							if($consolidatedpayment[$key]['amount_exchange'] == 0){
								$amount_val = $consolidatedpayment[$key]['amount'];
								$multi_currency[$key] = array(
									'sale_id' => $consolidatedpayment[$key]['sale_id'],
									'bil_id' => $consolidatedpayment[$key]['bill_id'],
									'currency_id' => $currency_row->id,
									'currency_rate' => $currency_row->rate,
									'amount' => $amount_val,
								);
							}else{
								$amount_val = $consolidatedpayment[$key]['amount_exchange'];
							}
							
							
						}else{
							
							if($consolidatedpayment[$key]['amount_exchange'] == 0){
								$amount_val = $consolidatedpayment[$key]['amount'];
									
							}else{
								$amount_val = $consolidatedpayment[$key]['amount_exchange'];
								$multi_currency[$key] = array(
									'sale_id' =>  $consolidatedpayment[$key]['sale_id'],
									'bil_id' => $consolidatedpayment[$key]['bill_id'],
									'currency_id' => $currency_row->id,
									'currency_rate' => $currency_row->rate,
									'amount' => $amount_val,
								);
							}
							
							
						}
					}
				}
				$taxation = $this->input->post('taxation') ? $this->input->post('taxation') :0;
					$update_bill[$billid[0]->id] = array(
						'updated_at'            => date('Y-m-d H:i:s'),
						'paid_by' 		    	=> $user_id,
						'total_pay'				=> $billid[0]->grand_total,
						'balance' 				=> 0.00,
						'paid'                  => $billid[0]->grand_total,
						'payment_status'        => 'Completed',
						'default_currency_code' => $default_currency_data->code,
						'default_currency_rate' => $default_currency_data->rate,
						'table_whitelisted'     => $taxation,
					);
					$update_bill[$billid[1]->id] = array(
						'updated_at'            => date('Y-m-d H:i:s'),
						'paid_by' 			    => $user_id,
						'total_pay'				=> $total_pay - $billid[0]->grand_total,
						'balance' 				=> $balance,
						'paid'                  => $paid - $billid[0]->grand_total,
						'payment_status'        => 'Completed',
						'default_currency_code' => $default_currency_data->code,
						'default_currency_rate' => $default_currency_data->rate,
						'table_whitelisted'     => $taxation,
					);
					$sales_bill[$billid[0]->sales_id] = array(
						'grand_total'           => $billid[0]->grand_total,				
						'paid'                  => $billid[0]->grand_total,
						'payment_status'		=>'Paid',
						'default_currency_code' => $default_currency_data->code,
						'default_currency_rate' => $default_currency_data->rate,
					);
					
					 $sales_bill[$billid[1]->sales_id] = array(
						'grand_total'           => $billid[1]->grand_total,				
						'paid'                  => $billid[1]->grand_total,
						'payment_status'		=>'Paid',
						'default_currency_code' => $default_currency_data->code,
						'default_currency_rate' => $default_currency_data->rate,
					);
					
					$waiter_id = $this->biller_api->splitWaiterid($order_split_id);
					//$device_token = $this->biller_api->deviceGET($waiter_id);					
					$deviceDetails = $this->biller_api->deviceDetails($waiter_id);
					$device_token = @$deviceDetails->device_token;	
					$title = 'BBQ Return ('.$order_split_id.')';
					$message = 'The cashier check payment status has been done. please check bbq return process -  '.$order_split_id;
					$push_data = $this->push->setPush($title,$message);
					if($this->site->isSocketEnabled() && $push_data == true && isset($deviceDetails->socket_id) && $deviceDetails->socket_id!=''){
						$json_data = '';
						$response_data = '';
						$json_data = $this->push->getPush();
						$regId_data = $device_token;
						//$response_data = $this->firebase->send($regId_data, $json_data);
						$socket_id = $deviceDetails->socket_id;
						$this->site->send_pushNotification($title,$message,$socket_id);
						
					}
					
					
						
					$response = $this->biller_api->CONinsertPayment($update_bill, $billid_val, $consolidatedpayment, $multi_currency, $salesid_val, $sales_bill, $order_split_id, $notification_array,$taxation);
					
				}elseif($dine_bil == 1 && $bbq_bil == 0){
					
					$default_currency_data = $this->site->getCurrencyByID($default_currency);
					$result = $this->biller_api->getBilvalue($bil_id);	
					$amount_USD = $this->input->post('amount') ? $this->input->post('amount') : 0;
					$amount_KHR = $this->input->post('amount_khr') ? $this->input->post('amount_khr') : 0;
					
					foreach($currency as $currency_row){
						if($currency_row->code == 'KHR'){
							$multi_currency[] = array(
							
								'sale_id' => $result->sales_id ? $result->sales_id : 0,
								'bil_id' => $result->id ? $result->id : 0,
								'currency_id' => $currency_row->id,
								'currency_rate' => $currency_row->rate,
								'amount' => $amount_KHR,
							);
								
							
						}elseif($currency_row->code == 'USD'){
							$multi_currency[] = array(
							
								'sale_id' => $result->sales_id ? $result->sales_id : 0,
								'bil_id' => $result->id ? $result->id : 0,
								'currency_id' => $currency_row->id,
								'currency_rate' => $currency_row->rate,
								'amount' => $amount_USD,
							);
						}
					}
					
					$billid  = $result->id ? $result->id : 0;
					$salesid = $result->sales_id ? $result->sales_id : 0;
					
					$update_bill = array(
						'updated_at'            => date('Y-m-d H:i:s'),
						'paid_by' 			    => $user_id,
						'total_pay'				=> $total_pay,
						'balance' 				=> $balance,
						'paid'                  => $total,
						'payment_status'        => 'Completed',
						'default_currency_code' => $default_currency_data->code,
						'default_currency_rate' => $default_currency_data->rate,
					);
	
					$sales_bill = array(
						'grand_total'           => $total,				
						'paid'                  => $total,
						'payment_status'		=>'Paid',
						'default_currency_code' => $default_currency_data->code,
						'default_currency_rate' => $default_currency_data->rate,
					);
					
					
					$notification_array['from_role'] = $group_id;
					$notification_array['insert_array'] = array(			
						'user_id' => $user_id,	
						'warehouse_id' => $warehouse_id,
						'created_on' => date('Y-m-d H:m:s'),
						'is_read' => 0
					);
					
					$payment[] = array(
					'paid_on' => date('Y-m-d H:i:s'),
					'date' => $this->site->getTransactionDate(),
					'sale_id'      => $result->sales_id ? $result->sales_id : 0,
					'bill_id'      => $result->id ? $result->id : 0,
					//'reference_no' => $this->input->post('reference_no'),
					'amount'       => $this->input->post('amount') ? $this->input->post('amount') : '',
					'amount_exchange'   => $this->input->post('amount_khr') ? $this->input->post('amount_khr') : '',
					'pos_paid'     => $this->input->post('amount') ? $this->input->post('amount') : '',
					'pos_balance'  => round($balance, 3),
					 'paid_by'     => $this->input->post('paid_by') ? $this->input->post('paid_by') : '',
					 'cheque_no'   => $this->input->post('cheque_no') ? $this->input->post('cheque_no') : '',
					 'cc_no'       => $this->input->post('cc_no') ? $this->input->post('cc_no') : '',
					 'cc_holder'   => $this->input->post('cc_holder') ? $this->input->post('cc_holder') : '',
					 'cc_month'    => $this->input->post('cc_month') ? $this->input->post('cc_month') : '',
					 'cc_year'     => $this->input->post('cc_year') ? $this->input->post('cc_year') : '',
					 'cc_type'     => $this->input->post('cc_type') ? $this->input->post('cc_type') : '',
					 // 'cc_cvv2'   => $this->input->post('cc_cvv2'),
					 'sale_note'   => $this->input->post('payment_note') ? $this->input->post('payment_note') : '',
					 'staff_note'   => $this->input->post('payment_note') ? $this->input->post('payment_note') : '',
					 'payment_note' => $this->input->post('payment_note') ? $this->input->post('payment_note') : '',
					 'created_by'   => $user_id,
					 'type'         => 'received',
				);
						
					$response = $this->biller_api->insertPayment($update_bill,$billid,$payment,$multi_currency,$salesid,$sales_bill, $order_split_id, $notification_array);
				}elseif($dine_bil == 0 && $bbq_bil == 1){
					
					$default_currency_data = $this->site->getCurrencyByID($default_currency);
					$result = $this->biller_api->getBilvalue($bil_id);	
					$amount_USD = $this->input->post('amount') ? $this->input->post('amount') : 0;
					$amount_KHR = $this->input->post('amount_khr') ? $this->input->post('amount_khr') : 0;
					
					foreach($currency as $currency_row){
						if($currency_row->code == 'KHR'){
							$multi_currency[] = array(
							
								'sale_id' => $result->sales_id ? $result->sales_id : 0,
								'bil_id' => $result->id ? $result->id : 0,
								'currency_id' => $currency_row->id,
								'currency_rate' => $currency_row->rate,
								'amount' => $amount_KHR,
							);
								
							
						}elseif($currency_row->code == 'USD'){
							$multi_currency[] = array(
							
								'sale_id' => $result->sales_id ? $result->sales_id : 0,
								'bil_id' => $result->id ? $result->id : 0,
								'currency_id' => $currency_row->id,
								'currency_rate' => $currency_row->rate,
								'amount' => $amount_USD,
							);
						}
					}
					
					
					$notification_array['from_role'] = $group_id;
					$notification_array['insert_array'] = array(			
						'user_id' => $user_id,	
						'warehouse_id' => $warehouse_id,
						'created_on' => date('Y-m-d H:m:s'),
						'is_read' => 0
					);
					
					
					$billid  = $result->id ? $result->id : 0;
					$salesid = $result->sales_id ? $result->sales_id : 0;
					$taxation = $this->input->post('taxation') ? $this->input->post('taxation') :0;
					
					$update_bill = array(
						'updated_at'            => date('Y-m-d H:i:s'),
						'paid_by' 			    => $user_id,
						'total_pay'		  		=> $total_pay,
						'balance' 				=> $balance,
						'paid'                  => $total,
						'payment_status'        => 'Completed',
						'default_currency_code' => $default_currency_data->code,
						'default_currency_rate' => $default_currency_data->rate,
						'table_whitelisted'     => $taxation,
					);
	
					$sales_bill = array(
						'grand_total'           => $total,				
						'paid'                  => $total,
						'payment_status'		=>'Paid',
						'default_currency_code' => $default_currency_data->code,
						'default_currency_rate' => $default_currency_data->rate,
					);
					
					
					$notification_array['from_role'] = $this->session->userdata('group_id');
					$notification_array['insert_array'] = array(			
						'user_id' => $this->session->userdata('user_id'),	
						'warehouse_id' => $this->session->userdata('warehouse_id'),
						'created_on' => date('Y-m-d H:m:s'),
						'is_read' => 0
					);
					
					$payment[] = array(					
					'date' => $this->site->getTransactionDate(),
					'sale_id'      => $result->sales_id ? $result->sales_id : 0,
					'bill_id'      => $result->id ? $result->id : 0,
					//'reference_no' => $this->input->post('reference_no'),
					'amount'       => $this->input->post('amount') ? $this->input->post('amount') : '',
					'amount_exchange'   => $this->input->post('amount_khr') ? $this->input->post('amount_khr') : '',
					'pos_paid'     => $this->input->post('amount') ? $this->input->post('amount') : '',
					'pos_balance'  => round($balance, 3),
					 'paid_by'     => $this->input->post('paid_by') ? $this->input->post('paid_by') : '',
					 'cheque_no'   => $this->input->post('cheque_no') ? $this->input->post('cheque_no') : '',
					 'cc_no'       => $this->input->post('cc_no') ? $this->input->post('cc_no') : '',
					 'cc_holder'   => $this->input->post('cc_holder') ? $this->input->post('cc_holder') : '',
					 'cc_month'    => $this->input->post('cc_month') ? $this->input->post('cc_month') : '',
					 'cc_year'     => $this->input->post('cc_year') ? $this->input->post('cc_year') : '',
					 'cc_type'     => $this->input->post('cc_type') ? $this->input->post('cc_type') : '',
					 // 'cc_cvv2'   => $this->input->post('cc_cvv2'),
					 'sale_note'   => $this->input->post('payment_note') ? $this->input->post('payment_note') : '',
					 'staff_note'   => $this->input->post('payment_note') ? $this->input->post('payment_note') : '',
					 'payment_note' => $this->input->post('payment_note') ? $this->input->post('payment_note') : '',
					 'created_by'   => $user_id,
					 'type'         => 'received',
				);
					
					$waiter_id = $this->biller_api->splitWaiterid($order_split_id);
					//$device_token = $this->biller_api->deviceGET($waiter_id);					
					$deviceDetails = $this->biller_api->deviceDetails($waiter_id);
					$device_token = @$deviceDetails->device_token;		
					$title = 'BBQ Return ('.$order_split_id.')';
					$message = 'The cashier check payment status has been done. please check bbq return process -  '.$order_split_id;
					$push_data = $this->push->setPush($title,$message);
					if($this->site->isSocketEnabled() && $push_data == true && isset($deviceDetails->socket_id) && $deviceDetails->socket_id!=''){
						$json_data = '';
						$response_data = '';
						$json_data = $this->push->getPush();
						$regId_data = $device_token;
						//$response_data = $this->firebase->send($regId_data, $json_data);
						$socket_id = $deviceDetails->socket_id;
						$this->site->send_pushNotification($title,$message,$socket_id);
					}
						
					$response = $this->biller_api->BBQinsertPayment($update_bill,$billid,$payment,$multi_currency,$salesid,$sales_bill, $order_split_id, $notification_array,$taxation);
					if($response){
						$tableid = $this->biller_api->getBBQTableID($billid);
						$stewardid = $this->site->getBBQSteward($order_split_id);
						$stewardGroupid = $this->site->getUserGroupID($stewardid);
						if($this->site->isSocketEnabled()){
							$socketEmit['user_id'] = $stewardid;
							$socketEmit['group_id'] = $stewardGroupid;
							$socketEmit['table_id'] = $tableid;
							$socketEmit['warehouse_id'] = $warehouse_id;
							$socketEmit['bbq_code'] = $order_split_id;
							$event = 'bbq_return_request';
							$edata = $socketEmit;
							$this->socketemitter->setEmit($event, $edata);
						}
					}
				}
				
				
				if($response){
					$update_notifi['split_id']=$order_split_id;
					$update_notifi['tag']='bill-request';
					$this->site->update_notification_status($update_notifi);
					$table_id = $this->biller_api->getTableID($bil_id);
					if($this->site->isSocketEnabled() && $bbq_bil==1 && $table_id){
						$this->site->socket_refresh_bbqtables($table_id);	
					}else if($this->site->isSocketEnabled() && $bbq_bil==0 && $table_id){
						$this->site->socket_refresh_tables($table_id);
					}
					$result = array( 'status'=> true , 'message'=> lang('payment_has_been_success_please_check_cashier'),'message_khmer'=> html_entity_decode(lang('payment_has_been_success_please_check_cashier_khmer')));
					
				}else{
					$result = array( 'status'=> false , 'message'=> lang('payment_has_been_not_success'),'message_khmer'=> html_entity_decode(lang('payment_has_been_not_success_khmer')));
				}
				
			}else{
				$result = array( 'status'=> false ,  'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
			}
			
		}else{
			$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));	
		}
		
		$this->response($result);
		
	}
	
	/*Ananthan Changes - New requirement Payment Screen*/
	public function paymentmethod_post(){
		$api_key = $this->input->get('api-key');
		$devices_key = $this->input->get('devices_key');
		$user_id = $this->input->get('user_id');
		$group_id = $this->input->get('group_id');
		$warehouse_id = $this->input->get('warehouse_id');
		
		$devices_check = $this->site->devicesCheck($api_key);
		if($devices_check == $devices_key){
			$data = $this->biller_api->getPaymentmethod();
			if(!empty($data)){
				$result = array( 'status'=> true , 'message'=> lang('payment_method'),'message_khmer'=> html_entity_decode(lang('payment_method_khmer')), 'data' => $data);
			}else{
				$result = array( 'status'=> false , 'message'=> lang('payment_method_empty'),'message_khmer'=> html_entity_decode(lang('payment_method_empty_khmer')));
			}
		}else{
			$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
		}
		$this->response($result);
		
	}
	
	public function consolidatedpayment_new_post()
	{
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		
		
		$bil_id = $this->input->post('bil_id');
		$total_pay = $this->input->post('total_pay'); 
		$order_split_id = $this->input->post('split_id');
		$total = $this->input->post('total'); 
		$balance = $total_pay - $total;
		$default_currency = $this->input->post('default_currency');
		
		$dine_bil = $this->input->post('dine_bil');
		$bbq_bil = $this->input->post('bbq_bil');
		$bbq_bil = $this->input->post('bbq_bil');
		
		//amount_usd_cash, amount_usd_CC, amount_usd_credit, amount_khr_cash, cc_no_CC, cc_month_CC, cc_year_CC, payment_type[]
		
		$this->form_validation->set_rules('total_pay', $this->lang->line("total_pay"), 'required');
		$this->form_validation->set_rules('total', $this->lang->line("total"), 'required');
		$this->form_validation->set_rules('bil_id', $this->lang->line("bil_id"), 'required');
		$this->form_validation->set_rules('warehouse_id', $this->lang->line("warehouse_id"), 'required');
		$this->form_validation->set_rules('user_id', $this->lang->line("user_id"), 'required');
		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		$currency = $this->site->getAllCurrencies();
		
		$customer_changed=0;
		$customer_id =0;
		$loyalty_customer = $this->input->post('loyalty_customer'); 	
		$loyalty_used_points = $this->input->post('loyalty_used_points') ? $this->input->post('loyalty_used_points') : 0; 
 	    if($loyalty_customer){
					$customer_changed=1;
					$customer_id = $loyalty_customer;
				}		

		
		if ($this->form_validation->run() == true) {
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){	
				
				
				if($dine_bil == 1 && $bbq_bil == 1){
					
					$default_currency_data = $this->site->getCurrencyByID($default_currency);
					//$result = $this->biller_api->getBilvalue($bil_id);	
					
					$payment_type = $this->input->post('payment_type');
					
					foreach($payment_type as $payment_row){
						
						$amount_USD = $this->input->post('amount_usd_'.$payment_row) ? $this->input->post('amount_usd_'.$payment_row) : 0;
						if($payment == 'cash'){
							$amount_KHR = $this->input->post('amount_khr_'.$payment_row) ? $this->input->post('amount_khr_'.$payment_row) : 0;
						}else{
							$amount_KHR = 0;
						}
						
						if($amount_USD){
							$payment[] = array(
								//'date'         => date('Y-m-d H:i:s'),
								
								'date' => $this->site->getTransactionDate(),
								'amount'       => $this->input->post('amount_usd_'.$payment_row) ? $this->input->post('amount_'.$payment_row) : 0,
								'amount_exchange'   => 0,
								'exchange_enable' =>  0,
								'pos_paid'     => $this->input->post('amount_'.$payment_row) ? $this->input->post('amount_'.$payment_row) : '',
								'pos_balance'  => round($balance, 3),
								 'paid_by'     => $payment_row,
								 'cheque_no'   => $this->input->post('cheque_no_'.$payment_row) ? $this->input->post('cheque_no_'.$payment_row) : '',
								 'cc_no'       => $this->input->post('cc_no_'.$payment_row) ? $this->input->post('cc_no_'.$payment_row) : '',
								 'cc_holder'   => $this->input->post('cc_holder_'.$payment_row) ? $this->input->post('cc_holder_'.$payment_row) : '',
								 'cc_month'    => $this->input->post('cc_month_'.$payment_row) ? $this->input->post('cc_month_'.$payment_row) : '',
								 'cc_year'     => $this->input->post('cc_year_'.$payment_row) ? $this->input->post('cc_year_'.$payment_row) : '',
								 'cc_type'     => $this->input->post('cc_type_'.$payment_row) ? $this->input->post('cc_type_'.$payment_row) : '',
								 'sale_note'   => $this->input->post('payment_note') ? $this->input->post('payment_note') : '',
								 'staff_note'   => $this->input->post('payment_note') ? $this->input->post('payment_note') : '',
								 'payment_note' => $this->input->post('payment_note') ? $this->input->post('payment_note') : '',
								 'created_by'   => $user_id,
								 'type'         => 'received',
							);
						}
						if(!empty($amount_KHR)){
							$amount_ex = $amount_KHR * 0.000244;
							$payment[] = array(
								//'date'         => date('Y-m-d H:i:s'),
								
				                'date' => $this->site->getTransactionDate(),
								'amount'       => $amount_ex ? $amount_ex:0,
								'amount_exchange'   => $amount_KHR ? $amount_KHR:0,
								'exchange_enable' => 1,
								'pos_paid'     => $this->input->post('amount_'.$payment_row) ? $this->input->post('amount_'.$payment_row) : '',
								'pos_balance'  => round($balance, 3),
								 'paid_by'     => $payment_row,
								 'cheque_no'   => $this->input->post('cheque_no_'.$payment_row) ? $this->input->post('cheque_no_'.$payment_row) : '',
								 'cc_no'       => $this->input->post('cc_no_'.$payment_row) ? $this->input->post('cc_no_'.$payment_row) : '',
								 'cc_holder'   => $this->input->post('cc_holder_'.$payment_row) ? $this->input->post('cc_holder_'.$payment_row) : '',
								 'cc_month'    => $this->input->post('cc_month_'.$payment_row) ? $this->input->post('cc_month_'.$payment_row) : '',
								 'cc_year'     => $this->input->post('cc_year_'.$payment_row) ? $this->input->post('cc_year_'.$payment_row) : '',
								 'cc_type'     => $this->input->post('cc_type_'.$payment_row) ? $this->input->post('cc_type_'.$payment_row) : '',
								 // 'cc_cvv2'   => $this->input->post('cc_cvv2'),
								 'sale_note'   => $this->input->post('payment_note') ? $this->input->post('payment_note') : '',
								 'staff_note'   => $this->input->post('payment_note') ? $this->input->post('payment_note') : '',
								 'payment_note' => $this->input->post('payment_note') ? $this->input->post('payment_note') : '',
								 'created_by'   => $user_id,
								 'type'         => 'received',
							);
						}
						
					}
					
					
					$billid  = $this->biller_api->getBilID($order_split_id);
					$salesid = $this->biller_api->getsalesID($order_split_id);
					
					foreach($billid as $billid_row){
						$billid_val[] = $billid_row->id;
						$salesid_val[] = $billid_row->sales_id;
					}
					
					
					$p = isset($_POST['paid_by']) ? sizeof($_POST['paid_by']) : 0;
					
					
					$notification_array['from_role'] = $group_id;
					$notification_array['insert_array'] = array(			
						'user_id' => $user_id,	
						'warehouse_id' => $warehouse_id,
						'created_on' => date('Y-m-d H:m:s'),
						'is_read' => 0
					);
					
					
					
					
					
				
					$alacat = 0;
					foreach($payment as $key =>  $pay){
					if($alacat <= 0){
						if($billid[0]->grand_total <= $pay['amount']){
							$bil_id = $billid[1]->id;
							$sale_id = $billid[1]->sales_id;
							
							$alacat -= $this->sma->formatDecimal($billid[0]->grand_total)  - $this->sma->formatDecimal($pay['amount']);
						}elseif($billid[0]->grand_total > $pay['amount']){
							$alacat += $this->sma->formatDecimal($billid[0]->grand_total)  - $this->sma->formatDecimal($pay['amount']);
							$bil_id = $billid[1]->id;
							$sale_id = $billid[1]->sales_id;
						}
						
						
						$consolidatedpayment[] = array(
							'date' => $pay['date'],
							'amount' => $this->sma->formatDecimal($billid[0]->grand_total), 
							'amount_exchange' => 0,
							'bill_id' => $billid[0]->id,
							 'sale_id' => $billid[0]->sales_id,
							 'exchange_enable' => $pay['exchange_enable'],
							 'paid_by'     => $pay['paid_by'],
							 'cheque_no'   => $pay['cheque_no'],
							 'cc_no'       => $pay['cc_no'],
							 'cc_holder'   => $pay['cc_holer'],
							 'cc_month'    => $pay['cc_month'],
							 'cc_year'     => $pay['cc_year'],
							 'cc_type'     => $pay['cc_type'],
							  'sale_note'   => $pay['sale_note'] != NULL ? $pay['sale_note'] : '',
							 'staff_note'   => $pay['staffnote'] != NULL ? $pay['staffnote'] : '',
							 'payment_note' => $pay['payment_note'] != NULL ? $pay['payment_note'] : '',
							 'created_by'   => $user_id,
							 'type'         => 'received',
						);
						if($pay['exchange_enable'] == 1){
							
							$amount_exchange = $alacat / 0.000244;
							$alacat = 0;
						}else{
							$amount_exchange = 0;
							$alacat = $this->sma->formatDecimal($alacat);
						}
						
						$consolidatedpayment[] = array(
							'date' => $pay['date'],
							'amount' => $this->sma->formatDecimal($alacat),
							'amount_exchange' => $amount_exchange,
							'bill_id' => $bil_id,
							 'sale_id' => $sale_id,
							'exchange_enable' => $pay['exchange_enable'],
							 'paid_by'     => $pay['paid_by'],
							 'cheque_no'   => $pay['cheque_no'],
							 'cc_no'       => $pay['cc_no'],
							 'cc_holder'   => $pay['cc_holer'],
							 'cc_month'    => $pay['cc_month'],
							 'cc_year'     => $pay['cc_year'],
							 'cc_type'     => $pay['cc_type'],
							 'sale_note'   => $pay['sale_note'] != NULL ? $pay['sale_note'] : '',
							 'staff_note'   => $pay['staffnote'] != NULL ? $pay['staffnote'] : '',
							 'payment_note' => $pay['payment_note'] != NULL ? $pay['payment_note'] : '',
							 'created_by'   => $user_id,
							 'type'         => 'received',
						);
						
					}else{
						if($pay['exchange_enable'] == 1){
							$amount_exchange = $pay['amount'] / 0.000244;
							$pay_amount = 0;
						}else{
							$amount_exchange = 0;	
							$pay_amount = $this->sma->formatDecimal($pay['amount']);
						}
						$consolidatedpayment[] = array(
							'date' => $pay['date'],
							'amount' => $pay_amount,
							'amount_exchange' => $amount_exchange,
							'bill_id' => $billid[1]->id,
							 'sale_id' => $billid[1]->sales_id,
							'exchange_enable' => $pay['exchange_enable'],
							 'paid_by'     => $pay['paid_by'],
							 'cheque_no'   => $pay['cheque_no'],
							 'cc_no'       => $pay['cc_no'],
							 'cc_holder'   => $pay['cc_holer'],
							 'cc_month'    => $pay['cc_month'],
							 'cc_year'     => $pay['cc_year'],
							 'cc_type'     => $pay['cc_type'],
							 'sale_note'   => $pay['sale_note'] != NULL ? $pay['sale_note'] : '',
							 'staff_note'   => $pay['staffnote'] != NULL ? $pay['staffnote'] : '',
							 'payment_note' => $pay['payment_note'] != NULL ? $pay['payment_note'] : '',
							 'created_by'   => $user_id,
							 'type'         => 'received',
						);
					}
				}
				
					foreach($consolidatedpayment as $key => $consolidated){
					
					
					
					foreach($currency as $currency_row){
						
						if($currency_row->code == 'USD'){
							
							if($consolidatedpayment[$key]['amount_exchange'] == 0){
								$amount_val = $consolidatedpayment[$key]['amount'];
								$multi_currency[$key] = array(
									'sale_id' => $consolidatedpayment[$key]['sale_id'],
									'bil_id' => $consolidatedpayment[$key]['bill_id'],
									'currency_id' => $currency_row->id,
									'currency_rate' => $currency_row->rate,
									'amount' => $amount_val,
								);
							}else{
								$amount_val = $consolidatedpayment[$key]['amount_exchange'];
							}
							
							
						}else{
							
							if($consolidatedpayment[$key]['amount_exchange'] == 0){
								$amount_val = $consolidatedpayment[$key]['amount'];
									
							}else{
								$amount_val = $consolidatedpayment[$key]['amount_exchange'];
								$multi_currency[$key] = array(
									'sale_id' =>  $consolidatedpayment[$key]['sale_id'],
									'bil_id' => $consolidatedpayment[$key]['bill_id'],
									'currency_id' => $currency_row->id,
									'currency_rate' => $currency_row->rate,
									'amount' => $amount_val,
								);
							}
							
							
						}
					}
				}
				$taxation = $this->input->post('taxation') ? $this->input->post('taxation') :0;
					$update_bill[$billid[0]->id] = array(
						'updated_at'            => date('Y-m-d H:i:s'),
						'paid_by' 		    	=> $user_id,
						'total_pay'				=> $billid[0]->grand_total,
						'balance' 				=> 0.00,
						'paid'                  => $billid[0]->grand_total,
						'payment_status'        => 'Completed',
						'default_currency_code' => $default_currency_data->code,
						'default_currency_rate' => $default_currency_data->rate,
						'table_whitelisted'     => $taxation,
					);
					$update_bill[$billid[1]->id] = array(
						'updated_at'            => date('Y-m-d H:i:s'),
						'paid_by' 			    => $user_id,
						'total_pay'				=> $total_pay - $billid[0]->grand_total,
						'balance' 				=> $balance,
						'paid'                  => $paid - $billid[0]->grand_total,
						'payment_status'        => 'Completed',
						'default_currency_code' => $default_currency_data->code,
						'default_currency_rate' => $default_currency_data->rate,
						'table_whitelisted'     => $taxation,
					);
					$sales_bill[$billid[0]->sales_id] = array(
						'grand_total'           => $billid[0]->grand_total,				
						'paid'                  => $billid[0]->grand_total,
						'payment_status'		=>'Paid',
						'default_currency_code' => $default_currency_data->code,
						'default_currency_rate' => $default_currency_data->rate,
					);
					
					 $sales_bill[$billid[1]->sales_id] = array(
						'grand_total'           => $billid[1]->grand_total,				
						'paid'                  => $billid[1]->grand_total,
						'payment_status'		=>'Paid',
						'default_currency_code' => $default_currency_data->code,
						'default_currency_rate' => $default_currency_data->rate,
					);
					
					$waiter_id = $this->biller_api->splitWaiterid($order_split_id);
					//$device_token = $this->biller_api->deviceGET($waiter_id);					
					$deviceDetails = $this->biller_api->deviceDetails($waiter_id);
					$device_token = @$deviceDetails->device_token;	
					$title = 'BBQ Return ('.$order_split_id.')';
					$message = 'The cashier check payment status has been done. please check bbq return process -  '.$order_split_id;
					$push_data = $this->push->setPush($title,$message);
					if($this->site->isSocketEnabled() && $push_data == true && isset($deviceDetails->socket_id) && $deviceDetails->socket_id!=''){
						$json_data = '';
						$response_data = '';
						$json_data = $this->push->getPush();
						$regId_data = $device_token;
						//$response_data = $this->firebase->send($regId_data, $json_data);
						$socket_id = $deviceDetails->socket_id;
						$this->site->send_pushNotification($title,$message,$socket_id);
						
					}
					
					
						
					$response = $this->biller_api->CONinsertPayment($update_bill, $billid_val, $consolidatedpayment, $multi_currency, $salesid_val, $sales_bill, $order_split_id, $notification_array, $total,$customer_id,$loyalty_used_points,$customer_changed, $taxation);
					
				}
				elseif($dine_bil == 1 && $bbq_bil == 0){
					
					
					$default_currency_data = $this->site->getCurrencyByID($default_currency);
					$result = $this->biller_api->getBilvalue($bil_id);
					
					$payment_type = $this->input->post('payment_type');
					
					
					foreach($payment_type as $payment){
						
						$amount_USD = $this->input->post('amount_usd_'.$payment) ? $this->input->post('amount_usd_'.$payment) : 0;
						if($payment == 'cash'){
							$amount_KHR = $this->input->post('amount_khr_'.$payment) ? $this->input->post('amount_khr_'.$payment) : 0;
						}else{
							$amount_KHR = 0;
						}
						foreach($currency as $currency_row){
							if($currency_row->code == 'KHR'){
								$multi_currency[] = array(
								
									'sale_id' => $result->sales_id ? $result->sales_id : 0,
									'bil_id' => $result->id ? $result->id : 0,
									'currency_id' => $currency_row->id,
									'currency_rate' => $currency_row->rate,
									'amount' => $amount_KHR,
								);
									
								
							}elseif($currency_row->code == 'USD'){
								$multi_currency[] = array(
								
									'sale_id' => $result->sales_id ? $result->sales_id : 0,
									'bil_id' => $result->id ? $result->id : 0,
									'currency_id' => $currency_row->id,
									'currency_rate' => $currency_row->rate,
									'amount' => $amount_USD,
								);
							}
						}
					}
					
					$billid  = $result->id ? $result->id : 0;
					$salesid = $result->sales_id ? $result->sales_id : 0;
					$taxation = $this->input->post('taxation') ? $this->input->post('taxation') :0;
					$update_bill = array(
						'updated_at'            => date('Y-m-d H:i:s'),
						'paid_by' 			    => $user_id,
						'total_pay'				=> $total_pay,
						'balance' 				=> $balance,
						'paid'                  => $total,
						'payment_status'        => 'Completed',
						'default_currency_code' => $default_currency_data->code,
						'default_currency_rate' => $default_currency_data->rate,
						'table_whitelisted' 	=> $taxation
					);
	
					$sales_bill = array(
						'grand_total'           => $total,				
						'paid'                  => $total,
						'payment_status'		=>'Paid',
						'default_currency_code' => $default_currency_data->code,
						'default_currency_rate' => $default_currency_data->rate,
					);
					
					
					$notification_array['from_role'] = $group_id;
					$notification_array['insert_array'] = array(			
						'user_id' => $user_id,	
						'warehouse_id' => $warehouse_id,
						'created_on' => date('Y-m-d H:m:s'),
						'is_read' => 0
					);
					
					
					foreach($payment_type as $payment){
						
						$payment_data[] = array(
							//'date'         => date('Y-m-d H:i:s'),
							
				'date' => $this->site->getTransactionDate(),
							'sale_id'      => $result->sales_id ? $result->sales_id : 0,
							'bill_id'      => $result->id ? $result->id : 0,
							//'reference_no' => $this->input->post('reference_no'),
							'amount'       => $this->input->post('amount_usd_'.$payment) ? $this->input->post('amount_usd_'.$payment) : '',
							'amount_exchange'   => $this->input->post('amount_khr_'.$payment) ? $this->input->post('amount_khr_'.$payment) : '',
							'pos_paid'     => $this->input->post('amount_usd_'.$payment) ? $this->input->post('amount_usd_'.$payment) : '',
							'pos_balance'  => round($balance, 3),
							 'paid_by'     => $payment,
							 'cheque_no'   => $this->input->post('cheque_no_'.$payment) ? $this->input->post('cheque_no_'.$payment) : '',
							 'cc_no'       => $this->input->post('cc_no_'.$payment) ? $this->input->post('cc_no_'.$payment) : '',
							 'cc_holder'   => $this->input->post('cc_holder_'.$payment) ? $this->input->post('cc_holder_'.$payment) : '',
							 'cc_month'    => $this->input->post('cc_month_'.$payment) ? $this->input->post('cc_month_'.$payment) : '',
							 'cc_year'     => $this->input->post('cc_year_'.$payment) ? $this->input->post('cc_year_'.$payment) : '',
							 'cc_type'     => $this->input->post('cc_type_'.$payment) ? $this->input->post('cc_type_'.$payment) : '',
							 // 'cc_cvv2'   => $this->input->post('cc_cvv2'),
							 'sale_note'   => $this->input->post('payment_note') ? $this->input->post('payment_note') : '',
							 'staff_note'   => $this->input->post('payment_note') ? $this->input->post('payment_note') : '',
							 'payment_note' => $this->input->post('payment_note') ? $this->input->post('payment_note') : '',
							 'created_by'   => $user_id,
							 'type'         => 'received',
						);
					}
					
					
					
					$response = $this->biller_api->insertPayment($update_bill,$billid,$payment_data,$multi_currency,$salesid,$sales_bill, $order_split_id, $notification_array, $total,$customer_id,$loyalty_used_points,$taxation,$customer_changed);
					
				}
				elseif($dine_bil == 0 && $bbq_bil == 1){
					
					$default_currency_data = $this->site->getCurrencyByID($default_currency);
					$result = $this->biller_api->getBilvalue($bil_id);	
					
					$payment_type = $this->input->post('payment_type');
					
					
					foreach($payment_type as $payment){
						
						$amount_USD = $this->input->post('amount_usd_'.$payment) ? $this->input->post('amount_usd_'.$payment) : 0;
						if($payment == 'cash'){
							$amount_KHR = $this->input->post('amount_khr_'.$payment) ? $this->input->post('amount_khr_'.$payment) : 0;
						}else{
							$amount_KHR = 0;
						}
						foreach($currency as $currency_row){
							if($currency_row->code == 'KHR'){
								$multi_currency[] = array(
								
									'sale_id' => $result->sales_id ? $result->sales_id : 0,
									'bil_id' => $result->id ? $result->id : 0,
									'currency_id' => $currency_row->id,
									'currency_rate' => $currency_row->rate,
									'amount' => $amount_KHR,
								);
									
								
							}elseif($currency_row->code == 'USD'){
								$multi_currency[] = array(
								
									'sale_id' => $result->sales_id ? $result->sales_id : 0,
									'bil_id' => $result->id ? $result->id : 0,
									'currency_id' => $currency_row->id,
									'currency_rate' => $currency_row->rate,
									'amount' => $amount_USD,
								);
							}
						}
					}
					
					
					$notification_array['from_role'] = $group_id;
					$notification_array['insert_array'] = array(			
						'user_id' => $user_id,	
						'warehouse_id' => $warehouse_id,
						'created_on' => date('Y-m-d H:m:s'),
						'is_read' => 0
					);
					
					
					$billid  = $result->id ? $result->id : 0;
					$salesid = $result->sales_id ? $result->sales_id : 0;
					$taxation = $this->input->post('taxation') ? $this->input->post('taxation') :0;
					
					$update_bill = array(
						'updated_at'            => date('Y-m-d H:i:s'),
						'paid_by' 			    => $user_id,
						'total_pay'		  		=> $total_pay,
						'balance' 				=> $balance,
						'paid'                  => $total,
						'payment_status'        => 'Completed',
						'default_currency_code' => $default_currency_data->code,
						'default_currency_rate' => $default_currency_data->rate,
						'table_whitelisted'     => $taxation,
					);
	
					$sales_bill = array(
						'grand_total'           => $total,				
						'paid'                  => $total,
						'payment_status'		=>'Paid',
						'default_currency_code' => $default_currency_data->code,
						'default_currency_rate' => $default_currency_data->rate,
					);
					
					
					$notification_array['from_role'] = $this->session->userdata('group_id');
					$notification_array['insert_array'] = array(			
						'user_id' => $this->session->userdata('user_id'),	
						'warehouse_id' => $this->session->userdata('warehouse_id'),
						'created_on' => date('Y-m-d H:m:s'),
						'is_read' => 0
					);
					
					foreach($payment_type as $payment){
						
						$payment_data[] = array(
							//'date'         => date('Y-m-d H:i:s'),
							
				'date' => $this->site->getTransactionDate(),
							'sale_id'      => $result->sales_id ? $result->sales_id : 0,
							'bill_id'      => $result->id ? $result->id : 0,
							//'reference_no' => $this->input->post('reference_no'),
							'amount'       => $this->input->post('amount_usd_'.$payment) ? $this->input->post('amount_usd_'.$payment) : '',
							'amount_exchange'   => $this->input->post('amount_khr_'.$payment) ? $this->input->post('amount_khr_'.$payment) : '',
							'pos_paid'     => $this->input->post('amount_usd_'.$payment) ? $this->input->post('amount_usd_'.$payment) : '',
							'pos_balance'  => round($balance, 3),
							 'paid_by'     => $payment,
							 'cheque_no'   => $this->input->post('cheque_no_'.$payment) ? $this->input->post('cheque_no_'.$payment) : '',
							 'cc_no'       => $this->input->post('cc_no_'.$payment) ? $this->input->post('cc_no_'.$payment) : '',
							 'cc_holder'   => $this->input->post('cc_holder_'.$payment) ? $this->input->post('cc_holder_'.$payment) : '',
							 'cc_month'    => $this->input->post('cc_month_'.$payment) ? $this->input->post('cc_month_'.$payment) : '',
							 'cc_year'     => $this->input->post('cc_year_'.$payment) ? $this->input->post('cc_year_'.$payment) : '',
							 'cc_type'     => $this->input->post('cc_type_'.$payment) ? $this->input->post('cc_type_'.$payment) : '',
							 // 'cc_cvv2'   => $this->input->post('cc_cvv2'),
							 'sale_note'   => $this->input->post('payment_note') ? $this->input->post('payment_note') : '',
							 'staff_note'   => $this->input->post('payment_note') ? $this->input->post('payment_note') : '',
							 'payment_note' => $this->input->post('payment_note') ? $this->input->post('payment_note') : '',
							 'created_by'   => $user_id,
							 'type'         => 'received',
						);
					}
					
					$waiter_id = $this->biller_api->splitWaiterid($order_split_id);
					//$device_token = $this->biller_api->deviceGET($waiter_id);					
					$deviceDetails = $this->biller_api->deviceDetails($waiter_id);
					$device_token = @$deviceDetails->device_token;		
					$title = 'BBQ Return ('.$order_split_id.')';
					$message = 'The cashier check payment status has been done. please check bbq return process -  '.$order_split_id;
					$push_data = $this->push->setPush($title,$message);
					if($this->site->isSocketEnabled() && $push_data == true && isset($deviceDetails->socket_id) && $deviceDetails->socket_id!=''){
						$json_data = '';
						$response_data = '';
						$json_data = $this->push->getPush();
						$regId_data = $device_token;
						//$response_data = $this->firebase->send($regId_data, $json_data);
						$socket_id = $deviceDetails->socket_id;
						$this->site->send_pushNotification($title,$message,$socket_id);
					}
						
					$response = $this->biller_api->BBQinsertPayment($update_bill,$billid,$payment_data,$multi_currency,$salesid,$sales_bill, $order_split_id, $notification_array,  $total,$customer_id,$loyalty_used_points,$customer_changed, $taxation);
					if($response){
						$tableid = $this->biller_api->getBBQTableID($billid);
						$stewardid = $this->site->getBBQSteward($order_split_id);
						$stewardGroupid = $this->site->getUserGroupID($stewardid);
						if($this->site->isSocketEnabled()){
							$socketEmit['user_id'] = $stewardid;
							$socketEmit['group_id'] = $stewardGroupid;
							$socketEmit['table_id'] = $tableid;
							$socketEmit['warehouse_id'] = $warehouse_id;
							$socketEmit['bbq_code'] = $order_split_id;
							$event = 'bbq_return_request';
							$edata = $socketEmit;
							$this->socketemitter->setEmit($event, $edata);
						}
					}
				}
				
				
				if($response){
					$this->site->send_to_bill_print($bil_id);
					$table_id = $this->biller_api->getTableID($bil_id);
					if($this->site->isSocketEnabled() && $bbq_bil==1 && $table_id){
						$this->site->socket_refresh_bbqtables($table_id);	
					}else if($this->site->isSocketEnabled() && $bbq_bil==0 && $table_id){
						$this->site->socket_refresh_tables($table_id);
					}
					$result = array( 'status'=> true , 'message'=> lang('payment_has_been_success_please_check_cashier'),'message_khmer'=> html_entity_decode(lang('payment_has_been_success_please_check_cashier_khmer')));
					
				}else{
					$result = array( 'status'=> false , 'message'=> lang('payment_has_been_not_success'),'message_khmer'=> html_entity_decode(lang('payment_has_been_not_success_khmer')));
				}
				
			}else{
				$result = array( 'status'=> false ,  'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
			}
			
		}else{
			$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));	
		}
		
		$this->response($result);
		
	}
/*loyalty 26-12-2018*/	

	public function loyalty_points_post(){

		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');				
		$phone_or_loyalty_card = $this->input->post('phone_or_loyalty_card');
		$devices_check = $this->site->devicesCheck($api_key);
		$this->form_validation->set_rules('phone_or_loyalty_card', $this->lang->line("phone_or_loyalty_card"), 'required');		
		if ($this->form_validation->run() == true) {
			if($devices_check == $devices_key){
				// $data = $this->biller_api->getPaymentmethod();
				$loyaltypoints = $this->biller_api->getLoyaltypointsBycustomer($phone_or_loyalty_card);
				$redemption = $this->biller_api->LoyaltyRedemtiondetails($phone_or_loyalty_card);
				if((!empty($redemption)) && (!empty($loyaltypoints))){
					$result = array( 'status'=> true , 'message'=> lang('loyalty_points_details'),'message_khmer'=> html_entity_decode(lang('loyalty_points_details_khmer')), 'loyaltypoints' => $loyaltypoints, 'redemption' => $redemption);
				}else{
					$result = array( 'status'=> false , 'message'=> lang('loyalty_points_details_empty'),'message_khmer'=> html_entity_decode(lang('loyalty_points_details_empty_khmer')));
				}
			}else{
				$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
			}
		}else{
			$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));	
		}
		$this->response($result);
	}
	
	public function loyalty_customer_post(){

		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		$phone_or_loyalty_card = $this->input->post('phone_or_loyalty_card');
		
		$devices_check = $this->site->devicesCheck($api_key);
		if($devices_check == $devices_key){
			// $data = $this->biller_api->getPaymentmethod();
			$data = $this->biller_api->getLoyaltyCustomerByCardNo($phone_or_loyalty_card);
			if(!empty($data)){
				$result = array( 'status'=> true , 'message'=> lang('loyalty_card_details'),'message_khmer'=> html_entity_decode(lang('loyalty_card_details_khmer')), 'data' => $data);
			}else{
				$result = array( 'status'=> false , 'message'=> lang('loyalty_card_details_empty'),'message_khmer'=> html_entity_decode(lang('loyalty_card_details_empty_khmer')));
			}
		}else{
			$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
		}
		$this->response($result);
	}
	public function loyalty_redemtion_post(){

		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$customer_id = $this->input->post('customer_id');
		$loyalty_card = $this->input->post('loyalty_card');
		$points = $this->input->post('points');		
		
		$devices_check = $this->site->devicesCheck($api_key);
		$this->form_validation->set_rules('points', $this->lang->line("points"), 'required');		
		$this->form_validation->set_rules('loyalty_card', $this->lang->line("loyalty_card"), 'required');		
		// $this->form_validation->set_rules('customer_id', $this->lang->line("customer_id"), 'required');		
		if ($this->form_validation->run() == true) {
		if($devices_check == $devices_key){
			// $data = $this->biller_api->getPaymentmethod();
			$data = $this->biller_api->LoyaltyRedemtion($customer_id, $points,$loyalty_card);
			if(!empty($data)){
				$result = array( 'status'=> true , 'message'=> lang('loyalty_card_details'),'message_khmer'=> html_entity_decode(lang('loyalty_card_details_khmer')), 'data' => $data);
			}else{
				$result = array( 'status'=> false , 'message'=> lang('Loyalty_points_entered_does_not_match_allocated_points'),'message_khmer'=> html_entity_decode(lang('Loyalty_points_entered_does_not_match_allocated_points_empty_khmer')));
			}
		}else{
			$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
		}
	}else{
			$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));	
		}
		$this->response($result);
	}
/*loyalty 26-12-2018*/
}
