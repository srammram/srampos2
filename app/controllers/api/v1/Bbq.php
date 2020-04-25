<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;



class Bbq extends REST_Controller {

	function __construct() {
		parent::__construct();
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		$this->load->api_model('bbq_api');
		$this->load->api_model('login_api');
		$this->lang->admin_load('engliah_khmer','english');
		$this->pos_settings = $this->site->get_posSetting();
	}


	public function category_get(){
		$api_key = $this->input->get('api-key');
		$devices_key = $this->input->get('devices_key');
		$user_id = $this->input->get('user_id');
		$group_id = $this->input->get('group_id');
		$warehouse_id = $this->input->get('warehouse_id');
		$split_id = $this->input->get('split_id');
		$sales_type = 4;
		$sales_type  = $this->bbq_api->getBBQLobsterSaletype($split_id);
		
		$sales_type = 1;
		if(!empty($sales_type)){
			$sales_type =$sales_type;
		}		
		$devices_check = $this->site->devicesCheck($api_key);
		if (!empty($sales_type)) {	
			if($devices_check == $devices_key){
				if($this->pos_settings->sales_item_in_pos == 1){
					$data = $this->bbq_api->GetAllmaincategory($sales_type);
				}else{
					$data = $this->bbq_api->GetAllmaincategory_withdays($sales_type);
				}
				if(!empty($data)){
					$result = array( 'status'=> true , 'message'=> lang('recipe_category'),'message_khmer'=> html_entity_decode(lang('recipe_category_khmer')), 'data' => $data);
				}else{
					$result = array( 'status'=> false , 'message'=> lang('recipe_category_empty'),'message_khmer'=> html_entity_decode(lang('recipe_category_empty_khmer')));
				}
			}else{
				$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
			}
		}else{
				$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));	
			}	
		$this->response($result);
	}
		
	public function subcategory_post(){
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		$category_id = $this->post('category_id');
		$split_id = $this->input->post('split_id');		

		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		$this->form_validation->set_rules('category_id', $this->lang->line("category"), 'required');
		$this->form_validation->set_rules('split_id', $this->lang->line("split_id"), 'required');
		if ($this->form_validation->run() == true) {

		 	$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){

				$sales_type = 4;
				$sales_type  = $this->bbq_api->getBBQLobsterSaletype($split_id);				
				if(!empty($sales_type)){
					$sales_type =$sales_type;
				}
				if($this->pos_settings->sales_item_in_pos == 1){
					$data = $this->bbq_api->GetAllsubcategory($category_id,$sales_type);
				}else{
					$data = $this->bbq_api->GetAllsubcategory_withdays($category_id,$sales_type);
				}
				if(!empty($data)){
					$result = array( 'status'=> true , 'message'=> lang('recipe_subcategory'),'message_khmer'=> html_entity_decode(lang('recipe_subcategory_khmer')), 'data' => $data);
				}else{
					$result = array( 'status'=> false , 'message'=> lang('recipe_subcategory_empty'),'message_khmer'=> html_entity_decode(lang('recipe_subcategory_empty_khmer')));
				}
			}else{
				$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
			}
		}else{
			$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));	
		}
		$this->response($result);
	}
	
	
	

	
	public function bbqreturnorders_post(){
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		
		$table_id = $this->post('table_id');
		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		$this->form_validation->set_rules('user_id', $this->lang->line("user_id"), 'required');
		$this->form_validation->set_rules('warehouse_id', $this->lang->line("warehouse"), 'required');
		if ($this->form_validation->run() == true) {
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){	
				$data = $this->bbq_api->Getreturnorders($table_id, $user_id, $warehouse_id);
				
				if(!empty($data)){
					$result = array( 'status'=> true , 'message'=> lang('order_bbq'),'message_khmer'=> html_entity_decode(lang('order_bbq_khmer')));
				}else{
					$result = array( 'status'=> false , 'message'=> lang('order_bbq_in_empty'),'message_khmer'=> html_entity_decode(lang('order_bbq_in_empty_khmer')));
				}
			}else{
				$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
			}
		}else{
			$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));	
		}
		$this->response($result);
	}
	
	public function bbqreturnupdate_post(){
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		
		$table_id = $this->input->post('table_id');
		
		
		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		$this->form_validation->set_rules('user_id', $this->lang->line("user_id"), 'required');
		$this->form_validation->set_rules('warehouse_id', $this->lang->line("warehouse"), 'required');
		if ($this->form_validation->run() == true) {
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){	
				
				$return_array = array(
					'sale_id' => $this->input->post('sale_id'),
					'order_id' => $this->input->post('order'),
					'split_id' => $this->input->post('split_id'),
					'order_type' => $this->input->post('order_type'),
					'created_at' => date('Y-m-d H:i:s'),
				);
				
				for($i=0; $i< count($this->input->post('item_id')); $i++){
					$returnitem_array[$i] = array(
						
						'order_id' => $_POST['order_id'][$i],
						'item_id' => $_POST['item_id'][$i],
						'recipe_id' =>$_POST['recipe_id'][$i],
						'recipe_code' => $_POST['recipe_code'][$i],
						'recipe_name' => $_POST['recipe_name'][$i],
						'recipe_type' => $_POST['recipe_type'][$i],
						'total_piece' => $_POST['total_piece'][$i],
						'return_piece' => $_POST['return_piece'][$i],
						'created_at' => date('Y-m-d H:i:s'),
					);
				}
				
				$data = $this->bbq_api->returnUpdate($return_array, $returnitem_array, $table_id, $user_id, $warehouse_id);
				
				if(!empty($data)){
					$result = array( 'status'=> true , 'message'=> lang('order_return_success'),'message_khmer'=> html_entity_decode(lang('order_return_success_khmer')));
				}else{
					$result = array( 'status'=> false , 'message'=> lang('order_return_not_success'),'message_khmer'=> html_entity_decode(lang('order_return_not_success_khmer')));
				}
			}else{
				$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
			}
		}else{
			$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));	
		}
		$this->response($result);
	}
	
	/*BBQ Bils*/
	
	public function bbqbilgenerator_post()
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
				
			$current_days = date('l');
		
			$data['order_type'] = $order_type;
			$data['bill_type'] = $bill_type;
			$data['bils'] = $bils;
			$data['table_id'] = $table_id;
			$data['split_id'] = $split_id;
			$data['tax_rates'] = $this->site->getAllTaxRates();
			$data['bbq_discount'] = $this->site->GetAllBBQDiscounts();
			$data['bbq_buyxgetx'] = $this->site->getBBQbuyxgetxDAYS($current_days);	
			$data['order_id'] = $this->bbq_api->getBBQorderID($split_id, $order_type);
			$data['order_bbq'] = $this->bbq_api->BBQtablesplit($table_id, $split_id);	
			$data['possettings'] = $this->bbq_api->getPOSSettings();
			$data['settings'] = $this->bbq_api->getSettings();
				
				$result = array( 'status'=> true , 'message'=> lang('biller_generator_in_data'),'message_khmer'=> html_entity_decode(lang('biller_generator_in_data_khmer')), 'data' => $data);
			}else{
				$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
			}
			
		}else{
			$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));	
		}
		
		$this->response($result);
		
	}
	
	public function bbqbiladd_post()
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
				$data['bbq_discount'] = $this->site->GetAllBBQDiscounts();
				$data['order_id'] = $this->bbq_api->getBBQorderID($split_id, $order_type);
				$data['covers'] = $this->bbq_api->BBQtablesplit($table_id, $split_id);	
				if($bill_type == 1){
					
					$bbq_discount = $this->input->post('bbq_discount');
					$ptax = $this->input->post('ptax');
					
					$sale = array(
						'bilgenerator_type' => 0,
						'sales_type_id' => 4,
						'sales_split_id' => $this->input->post('splits'),
						'sales_table_id' => $this->input->post('table'),
						'date' => date('Y-m-d H:i:s'),
						'reference_no' => 'SALE'.date('YmdHis'),
						'customer_id' => $this->input->post('customer_id'),
						'customer' => $this->input->post('customer'),
						'biller_id' => $this->input->post('biller_id'),
						'biller' => $this->input->post('biller'),
						'warehouse_id' => $this->input->post('warehouse_id'), 
						'total' => $this->input->post('total_amount'), 
						'order_discount_id' => $this->input->post('bbq_discount'), 
						'total_discount' => $this->input->post('bbq_discount_amount'),
						'order_tax_id' => $this->input->post('ptax'),
						'total_tax' => $this->input->post('tax_amount'), 
						'grand_total' => $this->input->post('gtotal'),
						'total_cover' => $this->input->post('number_of_covers')
					);
					
						$sale_items[] = array(
							'type' => 'adult',
							'cover' => $this->input->post('number_of_adult'),
							'price' => $this->input->post('adult_price'),
							'days' => $this->input->post('adult_days'),
							'buyx' => $this->input->post('adult_buyx'),
							'getx' => $this->input->post('adult_getx'),
							'discount_cover' => $this->input->post('adult_discount_cover'),
							'subtotal' => $this->input->post('adult_subprice'),
							'created' => date('Y-m-d H:i:s'),
						);
						
						$sale_items[] = array(
							'type' => 'child',
							'cover' => $this->input->post('number_of_child'),
							'price' => $this->input->post('child_price'),
							'days' => $this->input->post('child_days'),
							'buyx' => $this->input->post('child_buyx'),
							'getx' => $this->input->post('child_getx'),
							'discount_cover' => $this->input->post('child_discount_cover'),
							'subtotal' => $this->input->post('child_subprice'),
							'created' => date('Y-m-d H:i:s'),
						);
						
						$sale_items[] = array(
							'type' => 'kids',
							'cover' => $this->input->post('number_of_kids'),
							'price' => $this->input->post('kids_price'),
							'days' => $this->input->post('kids_days'),
							'buyx' => $this->input->post('kids_buyx'),
							'getx' => $this->input->post('kids_getx'),
							'discount_cover' => $this->input->post('kids_discount_cover'),
							'subtotal' => $this->input->post('kids_subprice'),
							'created' => date('Y-m-d H:i:s'),
						);
					
					for($i=0; $i<$this->input->post('bils'); $i++){
						$bilsdata[$i] = array(
							'bilgenerator_type' => 0,
							'date' => date('Y-m-d H:i:s'),
							'reference_no' => 'SALE'.date('YmdHis'),
							'customer_id' => $this->input->post('customer_id'),
							'customer' => $this->input->post('customer'),
							'biller_id' => $this->input->post('biller_id'),
							'biller' => $this->input->post('biller'),
							'warehouse_id' => $this->input->post('warehouse_id'), 
							'total' => $this->input->post('total_amount'), 
							'order_discount_id' => $this->input->post('bbq_discount'), 
							'total_discount' => $this->input->post('bbq_discount_amount'),
							'tax_id' => $this->input->post('ptax'),
							'total_tax' => $this->input->post('tax_amount'), 
							'tax_type' => $this->input->post('tax_type'),
							'grand_total' => $this->input->post('gtotal'),
							'total_cover' => $this->input->post('number_of_covers')
						);
						
						$bil_items[$i][] = array(
							'type' => 'adult',
							'cover' => $this->input->post('number_of_adult'),
							'price' => $this->input->post('adult_price'),
							'days' => $this->input->post('adult_days'),
							'buyx' => $this->input->post('adult_buyx'),
							'getx' => $this->input->post('adult_getx'),
							'discount_cover' => $this->input->post('adult_discount_cover'),
							'subtotal' => $this->input->post('adult_subprice'),
							'created' => date('Y-m-d H:i:s'),
						);
						
						$bil_items[$i][] = array(
							'type' => 'child',
							'cover' => $this->input->post('number_of_child'),
							'price' => $this->input->post('child_price'),
							'days' => $this->input->post('child_days'),
							'buyx' => $this->input->post('child_buyx'),
							'getx' => $this->input->post('child_getx'),
							'discount_cover' => $this->input->post('child_discount_cover'),
							'subtotal' => $this->input->post('child_subprice'),
							'created' => date('Y-m-d H:i:s'),
						);
						
						$bil_items[$i][] = array(
							'type' => 'kids',
							'cover' => $this->input->post('number_of_kids'),
							'price' => $this->input->post('kids_price'),
							'days' => $this->input->post('kids_days'),
							'buyx' => $this->input->post('kids_buyx'),
							'getx' => $this->input->post('kids_getx'),
							'discount_cover' => $this->input->post('kids_discount_cover'),
							'subtotal' => $this->input->post('kids_subprice'),
							'created' => date('Y-m-d H:i:s'),
						);
					}
					
				
					
					$response = $this->bbq_api->BBQaddSale($sale, $sale_items, $bilsdata, $bil_items, $order_id, $split_id, $this->input->post('gtotal'));
					if(!empty($response)){
						$result = array( 'status'=> true , 'message'=> lang('single_bill_generator_success'),'message_khmer'=> html_entity_decode(lang('single_bill_generator_success_khmer')));
					}else{
						$result = array( 'status'=> false , 'message'=> lang('single_bill_generator_not_success'),'message_khmer'=> html_entity_decode(lang('single_bill_generator_not_success_khmer')));	
					}					
				}else{
					$result = array( 'status'=> false , 'message'=> lang('single_bill_generator_not_success'),'message_khmer'=> html_entity_decode(lang('single_bill_generator_not_success_khmer')));	
				}							
			}else{
				$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
			}
			
		}else{
			$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));	
		}
		
		$this->response($result);
		
	}
	
	/*BBQ Bils*/
	
	public function salebil_post()
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
							
				$data = $this->bbq_api->getBBQAllSalesWithbiller($order_type, $warehouse_id);							
				$result = array( 'status'=> true , 'message'=> lang('biller_list_in_data'),'message_khmer'=> html_entity_decode(lang('biller_list_in_data_khmer')), 'data' => $data);
			}else{
				$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
			}
			
		}else{
			$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));	
		}
		
		$this->response($result);
		
	}
	
	public function bils_post()
	{
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		
		$sales_id = $this->input->post('sales_id');

		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		$this->form_validation->set_rules('order_type', $this->lang->line("order_type"), 'required');
		$this->form_validation->set_rules('warehouse_id', $this->lang->line("warehouse_id"), 'required');
		$this->form_validation->set_rules('user_id', $this->lang->line("user_id"), 'required');
		
		if ($this->form_validation->run() == true) {
			
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){	
							
				$data = $this->bbq_api->getBBQSaleIDWithBils($sales_id, $warehouse_id);							
				$result = array( 'status'=> true , 'message'=> lang('biller_list_in_data'),'message_khmer'=> html_entity_decode(lang('biller_list_in_data_khmer')), 'data' => $data);
			}else{
				$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
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
		
		
		$bill_id = $this->input->post('bill_id');
		$total_pay = $this->input->post('total_pay'); 
		$total = $this->input->post('total'); 
		$balance = $total_pay - $total;
		$default_currency = $this->input->post('default_currency');
		$order_split_id = $this->input->post('order_split_id'); 
		
		$this->form_validation->set_rules('amount', $this->lang->line("amount"), 'required');
		$this->form_validation->set_rules('total_pay', $this->lang->line("total_pay"), 'required');
		$this->form_validation->set_rules('total', $this->lang->line("total"), 'required');
		$this->form_validation->set_rules('bill_id', $this->lang->line("bill_id"), 'required');
		$this->form_validation->set_rules('warehouse_id', $this->lang->line("warehouse_id"), 'required');
		$this->form_validation->set_rules('user_id', $this->lang->line("user_id"), 'required');
		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		
		if ($this->form_validation->run() == true) {
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){	
				
				$default_currency_data = $this->site->getCurrencyByID($default_currency);
				
				$result = $this->bbq_api->getBilvalue($bill_id);	
				
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
					'created_by' 			=> $user_id,
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
				
				
				$notification_array['from_role'] = $this->session->userdata('group_id');
				$notification_array['insert_array'] = array(			
					'user_id' => $this->session->userdata('user_id'),	
					'warehouse_id' => $this->session->userdata('warehouse_id'),
					'created_on' => date('Y-m-d H:m:s'),
					'is_read' => 0
				);
				
				$payment[] = array(
                'date'         => date('Y-m-d H:i:s'),
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
			
			
				$response = $this->bbq_api->BBQPayment($update_bill, $bill_id, $payment, $multi_currency, $salesid, $sales_bill, $order_split_id, $updateCreditLimit);
				//$response = $this->biller_api->BBQPayment($update_bill,$billid,$payment,$multi_currency,$salesid,$sales_bill, $order_split_id, $notification_array);
				if($response){
					
					
					$result = array( 'status'=> true , 'message'=> lang('payment_has_been_success_please_check_cashier'),'message_khmer'=> html_entity_decode(lang('payment_has_been_success_please_check_cashier_khmer')));
					
				}else{
					$result = array( 'status'=> true , 'message'=> lang('payment_has_been_not_success'),'message_khmer'=> html_entity_decode(lang('payment_has_been_not_success_khmer')));
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
			$data = $this->bbq_api->GetAllBBQdiscount();
			if(!empty($data)){
				$result = array( 'status'=> true ,  'message'=> lang('bbq_discount_datas'),'message_khmer'=> html_entity_decode(lang('bbq_discount_datas_khmer')), 'data' => $data);
			}else{
				$result = array( 'status'=> false , 'message'=> lang('bbq_discount_empty'),'message_khmer'=> html_entity_decode(lang('bbq_discount_empty_khmer')));
			}
		}else{
			$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
		}
		$this->response($result);
	}
	
	public function bbqorders_post(){
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		
		$table_id = $this->post('table_id');
		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		$this->form_validation->set_rules('user_id', $this->lang->line("user_id"), 'required');
		$this->form_validation->set_rules('warehouse_id', $this->lang->line("warehouse"), 'required');
		if ($this->form_validation->run() == true) {
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){	
				$data = $this->bbq_api->GetAlldinein($table_id, $user_id, $warehouse_id);
				
				if(!empty($data)){
					$result = array( 'status'=> true , 'message'=> lang('order_dine_in_data'),'message_khmer'=> html_entity_decode(lang('order_dine_in_data_khmer')), 'name' => $data[0]->name,  'data' => $data[0]->split_order);
				}else{
					$result = array( 'status'=> false , 'message'=> lang('order_dine_in_empty'),'message_khmer'=> html_entity_decode(lang('order_dine_in_empty_khmer')));
				}
			}else{
				$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
			}
		}else{
			$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));	
		}
		$this->response($result);
	}
	
	public function bbqordersplit_post(){
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		
		$split = $this->post('split');
		$order_type = 4;
		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		$this->form_validation->set_rules('user_id', $this->lang->line("user_id"), 'required');
		$this->form_validation->set_rules('warehouse_id', $this->lang->line("warehouse"), 'required');
		$this->form_validation->set_rules('split', $this->lang->line("split"), 'required');
		$this->form_validation->set_rules('order_type', $this->lang->line("order_type"), 'required');
		if ($this->form_validation->run() == true) {
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){	
				$data = $this->bbq_api->GetAllSplit($split, $order_type, $user_id, $warehouse_id);
				if(!empty($data)){
					$result = array( 'status'=> true , 'message'=> lang('split_order_dine_in_data'),'message_khmer'=> html_entity_decode(lang('split_order_dine_in_data_khmer')), 'data' => $data);
				}else{
					$result = array( 'status'=> false , 'message'=> lang('split_order_dine_in_empty'),'message_khmer'=> html_entity_decode(lang('split_order_dine_in_empty_khmer')));
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
		
		$bbq_code = $this->post('bbq_code');
		
		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		$this->form_validation->set_rules('bbq_code', $this->lang->line("bbq_code"), 'required');
		$this->form_validation->set_rules('warehouse_id', $this->lang->line("warehouse"), 'required');
		if ($this->form_validation->run() == true) {
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){		 
				$data = $this->bbq_api->getBBQdataCode($bbq_code);
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
				$update = $this->bbq_api->updateBBQ($bbq_array, $bbq_code);
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
		
	public function recipe_post(){
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		$split_id = $this->input->post('split_id');		
		$bbq_set_id = $this->post('bbq_set_id');
		
		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		$this->form_validation->set_rules('bbq_set_id', $this->lang->line("bbq_set_id"), 'required');
		$this->form_validation->set_rules('warehouse_id', $this->lang->line("warehouse"), 'required');
		if ($this->form_validation->run() == true) {
				$sales_type = 0;
				$sales_type  = $this->bbq_api->getBBQLobsterSaletype($split_id);
				if(!empty($sales_type)){
					$sales_type =$sales_type;
				}

			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){	
				if($this->pos_settings->sales_item_in_pos == 1){ 	 
					$data = $this->bbq_api->GetAllrecipe($bbq_set_id, $warehouse_id,$sales_type);
				}else{
					$data = $this->bbq_api->GetAllrecipe_withdays($bbq_set_id, $warehouse_id,$sales_type);
				}
				if(!empty($data)){
					$result = array( 'status'=> true , 'message'=> lang('recipe'),'message_khmer'=> html_entity_decode(lang('recipe_khmer')), 'data' => $data);
				}else{
					$result = array( 'status'=> false , 'message'=> lang('recipe_empty'),'message_khmer'=> html_entity_decode(lang('recipe_empty_khmer')));
				}
			}else{
				$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
			}
		}else{
			$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));	
		}
		$this->response($result);
	}
	
	public function bbqrecipe_post(){
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		$subcategory_id = $this->post('subcategory_id');
		$split_id = $this->input->post('split_id');		
		
		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		$this->form_validation->set_rules('subcategory_id', $this->lang->line("subcategory_id"), 'required');
		$this->form_validation->set_rules('warehouse_id', $this->lang->line("warehouse"), 'required');
		if ($this->form_validation->run() == true) {
				$sales_type = 0;
				$sales_type  = $this->bbq_api->getBBQLobsterSaletype($split_id);
				if(!empty($sales_type)){
					$sales_type =$sales_type;
				}
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){	
				if($this->pos_settings->sales_item_in_pos == 1){ 				
					$data = $this->bbq_api->GetAllbbqrecipe($subcategory_id, $warehouse_id,$sales_type);
				}else{
					$data = $this->bbq_api->GetAllbbqrecipe_withdays($subcategory_id, $warehouse_id,$sales_type);
				}
				if(!empty($data)){
					$result = array( 'status'=> true , 'message'=> lang('recipe'),'message_khmer'=> html_entity_decode(lang('recipe_khmer')), 'data' => $data);
				}else{
					$result = array( 'status'=> false , 'message'=> lang('recipe_empty'),'message_khmer'=> html_entity_decode(lang('recipe_empty_khmer')));
				}
			}else{
				$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
			}
		}else{
			$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));	
		}
		$this->response($result);
	}

	public function recipedetails_post(){
		
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		
		$recipe_id = $this->post('recipe_id');
		
		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		$this->form_validation->set_rules('recipe_id', $this->lang->line("recipe_id"), 'required');
		$this->form_validation->set_rules('warehouse_id', $this->lang->line("warehouse"), 'required');
		if ($this->form_validation->run() == true) {
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){		 
				$data = $this->bbq_api->GetRecipedetails($recipe_id);
				if(!empty($data)){
					$result = array( 'status'=> true , 'message'=> lang('recipe_details'),'message_khmer'=> html_entity_decode(lang('recipe_details_khmer')), 'code' => $data->code, 'recipe_details' => $data->recipe_details, 'type' => $data->type, 'name' => $data->name, 'khmer_name' => $data->khmer_name, 'price' => $data->price, 'category_name' => $data->category_name, 'subcategory_name' => $data->subcategory_name, 'image' => $data->image, 'thumbnail' => $data->thumbnail, 'kitchens_id' =>  $data->kitchens_id, 'category_id' => $data->category_id, 
					'subcategory_id' => $data->subcategory_id, 'data' => $data->addon_list);
				}else{
					$result = array( 'status'=> false , 'message'=> lang('recipe_details_empty'),'message_khmer'=> html_entity_decode(lang('recipe_details_empty_khmer')));
				}
			}else{
				$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
			}
		}else{
			$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));	
		}
		$this->response($result);
	}
	
	public function tablecategory_post(){
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');		
		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		$this->form_validation->set_rules('warehouse_id', $this->lang->line("warehouse"), 'required');
		if ($this->form_validation->run() == true) {
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){			
				$data = $this->bbq_api->GetAlltablecategory($warehouse_id);
				if(!empty($data)){
					$result = array( 'status'=> true , 'message'=> lang('table_category_datas'),'message_khmer'=> html_entity_decode(lang('table_category_datas_khmer')), 'data' => $data);
				}else{
					$result = array( 'status'=> false , 'message'=> lang('table_category_empty'),'message_khmer'=> html_entity_decode(lang('table_category_empty_khmer')));
				}
			}else{
				$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
			}
		}else{
			$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));	
		}
		$this->response($result);
	}
	
	public function tables_post(){
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		
		$area_id = $this->post('area_id');
		
		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		$this->form_validation->set_rules('area_id', $this->lang->line("area"), 'required');
		$this->form_validation->set_rules('warehouse_id', $this->lang->line("warehouse"), 'required');
		if ($this->form_validation->run() == true) {
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){		
				//$data = $this->bbq_api->GetAlltables($area_id, $warehouse_id, $user_id);
				$data = $this->bbq_api->BBQGetAlltables($area_id, $warehouse_id, $user_id);
				
				if(!empty($data)){
					
					$result = array( 'status'=> true , 'message'=> lang('bbq_tables_datas'),'message_khmer'=> html_entity_decode(lang('bbq_tables_datas_khmer')), 'data' => $data);
				}else{
					$result = array( 'status'=> false , 'message'=> lang('bbq_tables_empty'),'message_khmer'=> html_entity_decode(lang('bbq_tables_empty_khmer')));
				}
			}else{
				$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
			}
		}else{
			$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));	
		}
		$this->response($result);
	}
	
	
	public function currencies_get(){
		$api_key = $this->input->get('api-key');
		$devices_key = $this->input->get('devices_key');
		$user_id = $this->input->get('user_id');
		$group_id = $this->input->get('group_id');
		$warehouse_id = $this->input->get('warehouse_id');
		
		$devices_check = $this->site->devicesCheck($api_key);
		if($devices_check == $devices_key){
			$data = $this->bbq_api->GetAllcurrencies();
			if(!empty($data)){
				$result = array( 'status'=> true , 'message'=> lang('currencies'),'message_khmer'=> html_entity_decode(lang('currencies_khmer')), 'data' => $data);
			}else{
				$result = array( 'status'=> false , 'message'=> lang('currencies_empty'),'message_khmer'=> html_entity_decode(lang('currencies_empty_khmer')));
			}
		}else{
			$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
		}
		$this->response($result);
	}
	
	public function taxs_get(){
		$api_key = $this->input->get('api-key');
		$devices_key = $this->input->get('devices_key');
		$user_id = $this->input->get('user_id');
		$group_id = $this->input->get('group_id');
		$warehouse_id = $this->input->get('warehouse_id');
		
		$devices_check = $this->site->devicesCheck($api_key);
		if($devices_check == $devices_key){
			$data = $this->bbq_api->GetAlltaxs();
			if(!empty($data)){
				$result = array( 'status'=> true , 'message'=> lang('taxs'),'message_khmer'=> html_entity_decode(lang('taxs_khmer')), 'data' => $data);
			}else{
				$result = array( 'status'=> false , 'message'=> lang('taxs_empty'),'message_khmer'=> html_entity_decode(lang('taxs_empty_khmer')));
			}
		}else{
			$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
		}
		$this->response($result);
	}
	
	public function warehouses_get(){
		$api_key = $this->input->get('api-key');
		$devices_key = $this->input->get('devices_key');
		$user_id = $this->input->get('user_id');
		$group_id = $this->input->get('group_id');
		$warehouse_id = $this->input->get('warehouse_id');
		
		$devices_check = $this->site->devicesCheck($api_key);
		if($devices_check == $devices_key){
			$data = $this->bbq_api->GetAllwarehouses();
			if(!empty($data)){
				$result = array( 'status'=> true , 'message'=> lang('warehouses'),'message_khmer'=> html_entity_decode(lang('warehouses_khmer')), 'data' => $data);
			}else{
				$result = array( 'status'=> false , 'message'=> lang('warehouses_empty'),'message_khmer'=> html_entity_decode(lang('warehouses_empty_khmer')));
			}
		}else{
			$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
		}
		$this->response($result);
	}
	
	public function groups_get(){
		$api_key = $this->input->get('api-key');
		$devices_key = $this->input->get('devices_key');
		$user_id = $this->input->get('user_id');
		$group_id = $this->input->get('group_id');
		$warehouse_id = $this->input->get('warehouse_id');
		
		$devices_check = $this->site->devicesCheck($api_key);
		if($devices_check == $devices_key){
			$data = $this->bbq_api->GetAllgroups();
			if(!empty($data)){
				$result = array( 'status'=> true , 'message'=> lang('groups'),'message_khmer'=> html_entity_decode(lang('groups_khmer')), 'data' => $data);
			}else{
				$result = array( 'status'=> false , 'message'=> lang('groups_empty'),'message_khmer'=> html_entity_decode(lang('groups_empty_khmer')));
			}
		}else{
			$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
		}
		$this->response($result);
	}
	
	public function customer_groups_get(){
		$api_key = $this->input->get('api-key');
		$devices_key = $this->input->get('devices_key');
		$user_id = $this->input->get('user_id');
		$group_id = $this->input->get('group_id');
		$warehouse_id = $this->input->get('warehouse_id');
		
		$devices_check = $this->site->devicesCheck($api_key);
		if($devices_check == $devices_key){
			$data = $this->bbq_api->GetAllcustomer_groups();
			if(!empty($data)){
				$result = array( 'status'=> true , 'message'=> lang('customer_groups'),'message_khmer'=> html_entity_decode(lang('customer_groups_khmer')), 'data' => $data);
			}else{
				$result = array( 'status'=> false , 'message'=> lang('customer_groups_empty'),'message_khmer'=> html_entity_decode(lang('customer_groups_empty_khmer')));
			}
		}else{
			$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
		}
		$this->response($result);
	}
	
	
	
	public function customers_get(){
		$api_key = $this->input->get('api-key');
		$devices_key = $this->input->get('devices_key');
		$user_id = $this->input->get('user_id');
		$group_id = $this->input->get('group_id');
		$warehouse_id = $this->input->get('warehouse_id');
		
		$devices_check = $this->site->devicesCheck($api_key);
		if($devices_check == $devices_key){
			$data = $this->bbq_api->GetAllcustomers();
			$possetting = $this->bbq_api->getPOSSettingsALL();
			if(!empty($data)){
				$result = array( 'status'=> true , 'message'=> lang('customer'),'message_khmer'=> html_entity_decode(lang('customer_khmer')), 'data' => $data, 'default_customer' => $possetting->default_customer);
			}else{
				$result = array( 'status'=> false , 'message'=> lang('customer_empty'),'message_khmer'=> html_entity_decode(lang('customer_empty_khmer')));
			}
		}else{
			$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
		}
		$this->response($result);
	}
	
	public function bbqinsert_post(){
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		$bbq_menu_id = $this->input->post('bbq_menu_id');  	
		$bbq = $this->site->CreateBBQSplitID($this->input->post('user_id'));
		
		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		$this->form_validation->set_rules('bbq_menu_id', $this->lang->line("bbq_menu_id"), 'required');
		
		if ($this->form_validation->run() == true) {	
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){
				
				$array_bbq = array(
					'reference_no' => $bbq,
					'warehouse_id' => $this->input->post('warehouse_id'),
					'bbq_menu_id' => $this->input->post('bbq_menu_id'),
					'table_id' => $this->input->post('table_id'),
					'name' => $this->input->post('name') ? $this->input->post('name') : NULL,
					'phone' => $this->input->post('phone') ? $this->input->post('phone') : NULL,
					'email' => $this->input->post('email_address') ? $this->input->post('email_address') : '',
					'number_of_adult' => $this->input->post('number_of_adult'),
					'number_of_child' => $this->input->post('number_of_child'),
					'number_of_kids' => $this->input->post('number_of_kids'),
					'bbq_set_id' => 1,
					'adult_price' => $this->input->post('adult_price'),
					'child_price' => $this->input->post('child_price'),
					'kids_price' => $this->input->post('kids_price'),
					'status' => 'Open',
					'payment_status' => '',
					'created_by' => $this->input->post('user_id'),
					'created_on' => date('Y-m-d H:i:s'),
					'confirmed_by' => $this->input->post('user_id')
				);
				
				$array_customer = array(
					'ref_id' => 'CUS-'.date('YmdHis'),
					//'company' => $this->input->post('company') ? $this->input->post('company') : NULL,
					'name' => $this->input->post('name') ? $this->input->post('name') : 'New Customer',
					'email' => $this->input->post('email_address') ? $this->input->post('email_address') : '',
					'phone' => $this->input->post('phone') ? $this->input->post('phone') : NULL,
					//'address' => $this->input->post('address') ? $this->input->post('address') : NULL,
					//'city' => $this->input->post('city') ? $this->input->post('city') : NULL,
					//'state' => $this->input->post('state') ? $this->input->post('state') : NULL,
					//'postal_code' => $this->input->post('postal_code') ? $this->input->post('postal_code') : NULL,
					//'country' => $this->input->post('country') ? $this->input->post('country') : NULL,
					'group_id' => 3,
					'group_name' => 'customer'
				);
				
				$customer_id = $this->input->post('customer_id') ? $this->input->post('customer_id') : NULL;
				
				$data = $this->bbq_api->addBBQ($array_bbq, $array_customer, $customer_id);
				//$data->order_type = 4;
				if(!empty($data)){
					$table_id = $this->input->post('table_id');
					if($this->site->isSocketEnabled()){
						$this->site->socket_refresh_bbqtables($table_id);	
					}						
					$result = array( 'status'=> true , 'message'=> lang('new_bbq_added'),'message_khmer'=> html_entity_decode(lang('new_bbq_added_khmer')), 'data' => $data);
				}else{
					$result = array( 'status'=> false , 'message'=> lang('new_bbq_not_added'),'message_khmer'=> html_entity_decode(lang('new_bbq_not_added_khmer')));
				}
			}else{
				$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
			}
		}else{
			$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));	
		}
		$this->response($result);	
	}
	
	public function suppliers_get(){
		$api_key = $this->input->get('api-key');
		$devices_key = $this->input->get('devices_key');
		$user_id = $this->input->get('user_id');
		$group_id = $this->input->get('group_id');
		$warehouse_id = $this->input->get('warehouse_id');
		
		$devices_check = $this->site->devicesCheck($api_key);
		if($devices_check == $devices_key){
			$data = $this->bbq_api->GetAllsuppliers();
			if(!empty($data)){
				$result = array( 'status'=> true , 'message'=> lang('supplier'),'message_khmer'=> html_entity_decode(lang('supplier_khmer')), 'data' => $data);
			}else{
				$result = array( 'status'=> false , 'message'=> lang('supplier_empty'),'message_khmer'=> html_entity_decode(lang('supplier_empty_khmer')));
			}
		}else{
			$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
		}
		$this->response($result);
	}
	
	public function deliveryusers_post(){
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		
		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		$this->form_validation->set_rules('warehouse_id', $this->lang->line("warehouse_id"), 'required');
		if ($this->form_validation->run() == true) {	
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){
				$data = $this->bbq_api->GetAlldeliveryusers($warehouse_id);
				if(!empty($data)){
					$result = array( 'status'=> true , 'message'=> lang('delivery_users'),'message_khmer'=> html_entity_decode(lang('delivery_users_khmer')), 'data' => $data);
				}else{
					$result = array( 'status'=> false , 'message'=> lang('delivery_users_empty'),'message_khmer'=> html_entity_decode(lang('delivery_users_empty_khmer')));
				}
			}else{
				$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
			}
		}else{
			$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));	
		}
		$this->response($result);
	}
	
	public function customeradd_post(){
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		
		
		$this->form_validation->set_rules('customer_group', $this->lang->line("customer_group"), 'required');
		$this->form_validation->set_rules('name', $this->lang->line("name"), 'required');
		$this->form_validation->set_rules('phone', $this->lang->line("phone"), 'required');
		$this->form_validation->set_rules('address', $this->lang->line("address"), 'required');
		$this->form_validation->set_rules('city', $this->lang->line("city"), 'required');
		$this->form_validation->set_rules('email', lang("email_address"), 'is_unique[companies.email]');
		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		if ($this->form_validation->run() == true) {	
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){
				$cg = $this->site->getCustomerGroupByID($this->input->post('customer_group'));
				$customer_array = array(
					'name' => $this->input->post('name'),
					'short_name' => $this->input->post('short_name'),
					'email' => $this->input->post('email'),
					'ref_id' => 'CUS-'.date('YmdHis'),
					'group_id' => '3',
					'group_name' => 'customer',
					'customer_group_id' => $this->input->post('customer_group'),
					'customer_group_name' => $cg->name,
					'address' => $this->input->post('address'),
					'city' => $this->input->post('city'),
					'state' => $this->input->post('state'),
					'postal_code' => $this->input->post('postal_code'),
					'country' => $this->input->post('country'),
					'phone' => $this->input->post('phone'),
				);
				
				$data = $this->bbq_api->Insertcustomer($customer_array);
				if(!empty($data)){
					$result = array( 'status'=> true , 'message'=> lang('new_customer_added'),'message_khmer'=> html_entity_decode(lang('new_customer_added_khmer')), 'data' => $data);
				}else{
					$result = array( 'status'=> false , 'message'=> lang('new_customer_not_added'),'message_khmer'=> html_entity_decode(lang('new_customer_not_added_khmer')));
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
				$data = $this->bbq_api->Getnotification($group_id, $user_id, $warehouse_id);
				
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
	
	public function nitificationclear_post(){
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
				$data = $this->bbq_api->notification_clear($notification_id);	
				
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
	public function customerdiscounts_get(){
		$api_key = $this->input->get('api-key');
		$devices_key = $this->input->get('devices_key');
		$user_id = $this->input->get('user_id');
		$group_id = $this->input->get('group_id');
		$warehouse_id = $this->input->get('warehouse_id');
		
		$devices_check = $this->site->devicesCheck($api_key);
		if($devices_check == $devices_key){
			$data = $this->bbq_api->GetAllcostomerDiscounts();
			if(!empty($data)){
				$result = array( 'status'=> true , 'message'=> lang('customer_discounts'),'message_khmer'=> html_entity_decode(lang('customer_discounts_khmer')), 'data' => $data);
			}else{
				$result = array( 'status'=> false , 'message'=> lang('customer_discounts_empty'),'message_khmer'=> html_entity_decode(lang('customer_discounts_empty_khmer')));
			}
		}else{
			$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
		}
		$this->response($result);
	}

	public function bbqitemreturn_post(){
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		
		$devices_check = $this->site->devicesCheck($api_key);
		if($devices_check == $devices_key){
			//$data = $this->bbq_api->getBBQAllBillingDatasreturn($warehouse_id);
			$data = $this->bbq_api->getBBQReturn($warehouse_id);
			
			if(!empty($data)){
				$result = array( 'status'=> true , 'message'=> lang('return_items'),'message_khmer'=> html_entity_decode(lang('return_items_khmer')), 'data' => $data);
			}else{
				$result = array( 'status'=> false , 'message'=> lang('return_items_empty'),'message_khmer'=> html_entity_decode(lang('return_items_empty_khmer')));
			}
		}else{
			$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
		}
		$this->response($result);
	}

	public function itemreturnBBQCode_post(){
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		$split_id = $this->input->post('split_id');

		$devices_check =$this->site->devicesCheck($api_key);
		if($devices_check == $devices_key){
			$data = $this->bbq_api->BBQsalesordersGET($split_id);
			
			if(!empty($data)){
				$result = array( 'status'=> true , 'message'=> lang('return_items'),'message_khmer'=> html_entity_decode(lang('return_items_khmer')), 'data' => $data);
			}else{
				$result = array( 'status'=> false , 'message'=> lang('return_items_empty'),'message_khmer'=> html_entity_decode(lang('return_items_empty_khmer')));
			}
		}else{
			$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
		}
		$this->response($result);
	}

	public function salereturnUpdate_post(){
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		
		$devices_check =$this->site->devicesCheck($api_key);
		if($devices_check == $devices_key){

		    $item_count = isset($_POST['recipe_code']) ? sizeof($_POST['recipe_code']) : 0; 
			for ($i = 0; $i < $item_count; $i++){
				$returnitem_array[$i] = array(					
					'order_id' => $_POST['order_id'][$i],
					'item_id' => $_POST['item_id'][$i],
					'recipe_id' =>$_POST['recipe_id'][$i],
					'recipe_code' => $_POST['recipe_code'][$i],
					'recipe_name' => $_POST['recipe_name'][$i],
					'recipe_type' => $_POST['recipe_type'][$i],
					'total_piece' => $_POST['total_piece'][$i],
					'return_piece' => $_POST['return_piece'][$i],
					'return_uom' => $_POST['return_uom'][$i] ? $_POST['return_uom'][$i] :0,
					'created_at' => date('Y-m-d H:i:s'),
				);	

				$return_array = array(				
				
				'order_id' => $_POST['order_id'][$i],
				'split_id' => $_POST['split_id'][$i],
				'order_type' => $_POST['order_type'][$i],
				'created_at' => date('Y-m-d H:i:s'),
				'confirmed_by' => $user_id,
			);
			
			$piece = $_POST['piece'][$i];
			$return_uom = $_POST['return_uom'][$i];
			$stock_piece = $return_uom * $piece;
			
			$query = 'update srampos_pro_stock_master set stock_in = stock_in + '.$return_uom.', stock_in_piece = stock_in_piece + '.$stock_piece.', stock_out = stock_out - '.$return_uom.', stock_out_piece = stock_out_piece - '.$stock_piece.' where product_id='.$_POST['recipe_id'][$i];
			$this->db->query($query);

			}
			$response = $this->bbq_api->salereturnUpdate($return_array, $returnitem_array);
			if($response == TRUE){
				$update_notifi['split_id']=$return_array['split_id'];
				$update_notifi['tag']='bbq-return';
				$this->site->update_notification_status($update_notifi);
				$bbqData = $this->bbq_api->getBBQdataCode($return_array['split_id']);
				if($this->site->isSocketEnabled()){$this->site->socket_refresh_bbqtables($bbqData->table_id);}
				$result = array( 'status'=> true , 'message'=> lang('return_items_updated_sucessfully'),'message_khmer'=> html_entity_decode(lang('return_items_updated_sucessfully_khmer')));
			}else{
				$result = array( 'status'=> false , 'message'=> lang('return_items_not_updated'),'message_khmer'=> html_entity_decode(lang('return_items_not_updated_khmer')));
			}
		}else{
			$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
		}
		$this->response($result);
	}			
	
	public function bbq_menu_get(){

		$api_key = $this->input->get('api-key');
		$devices_key = $this->input->get('devices_key');
		$warehouse_id = $this->input->get('warehouse_id');
		$user_id = $this->input->get('user_id');
		$devices_check = $this->site->devicesCheck($api_key);
		if($devices_check == $devices_key){
			$data = $this->bbq_api->GetAllBBQMenus();
			if(!empty($data)){
				$result = array( 'status'=> true , 'message'=> lang('bbq_menu'),'message_khmer'=> html_entity_decode(lang('bbq_menu_khmer')), 'data' => $data);
			}else{
				$result = array( 'status'=> false , 'message'=> lang('bbq_menu_empty'),'message_khmer'=> html_entity_decode(lang('bbq_menu_empty_khmer')));
			}
		}else{
			$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
		}
		$this->response($result);
	}
	public function lobster_discount_get(){

		$api_key = $this->input->get('api-key');
		$devices_key = $this->input->get('devices_key');
		$warehouse_id = $this->input->get('warehouse_id');
		$user_id = $this->input->get('user_id');
		$devices_check = $this->site->devicesCheck($api_key);
		$current_days = date('l');
		if($devices_check == $devices_key){
			$data = $this->site->getBBQlobsterDAYS($current_days);
			if(!empty($data)){
				$result = array( 'status'=> true , 'message'=> lang('lobster_discount'),'message_khmer'=> html_entity_decode(lang('lobster_discount_category_khmer')), 'data' => $data);
			}else{
				$result = array( 'status'=> false , 'message'=> lang('lobster_discount_empty'),'message_khmer'=> html_entity_decode(lang('lobster_discount_empty_khmer')));
			}
		}else{
			$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
		}
		$this->response($result);
	}	

	/*public function billgeneratornew_post()
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
		
		$bbq_discount = $this->bbq_api->GetBBQDiscount($bbq_discount_id);
		
		
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
					
					$order_id = $this->bbq_api->getBBQorderID($split_id, $order_type);
					
					
					$bbq_data = $this->bbq_api->getBBQData($split_id);
					
					$order_bbq = $this->bbq_api->BBQtablesplitone($table_id, $split_id);
					
					
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
					
				
					$response_data = $this->bbq_api->BBQaddSale_new($sale, $sale_items, $bilsdata, $bil_items, $order_id, $split_id, $grand_total);
					
					if($response_data){
						$result = array( 'status'=> true, 'message'=> 'Bils Generator Success', 'data' => $response_data);	
					}else{
						$result = array( 'status'=> false , 'message'=> 'Bils Generator Not Success');		
					}
			}else{
				$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
			}
			
		}else{
			$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));	
		}
		
		$this->response($result);
		
	}*/

	
}
