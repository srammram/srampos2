<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;



class Pos extends REST_Controller {

	function __construct() {
		parent::__construct();
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		$this->load->api_model('pos_api');
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
		$order_type = $this->input->get('order_type');		
		
		if (!empty($order_type)) {				
		$devices_check = $this->site->devicesCheck($api_key);
		if($devices_check == $devices_key){
			if($this->pos_settings->sales_item_in_pos == 1){
				$data = $this->pos_api->GetAllmaincategory($order_type);
		    }else{
		    	$data = $this->pos_api->GetAllmaincategory_withdays($order_type);
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
		$order_type = $this->input->post('order_type');
		
		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		$this->form_validation->set_rules('category_id', $this->lang->line("category"), 'required');
		$this->form_validation->set_rules('order_type', $this->lang->line("order_type"), 'required');
		if ($this->form_validation->run() == true) {	
		 	$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){
				if($this->pos_settings->sales_item_in_pos == 1){					
						$data = $this->pos_api->GetAllsubcategory($category_id,$order_type);
				}else{
					$data = $this->pos_api->GetAllsubcategory_withdays($category_id,$order_type);
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
	
	public function recipe_post(){
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		
		$subcategory_id = $this->post('subcategory_id');
		$order_type = $this->post('order_type');
		$setting = $this->pos_api->getSettingsALL();
		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		$this->form_validation->set_rules('subcategory_id', $this->lang->line("subcategory"), 'required');
		$this->form_validation->set_rules('warehouse_id', $this->lang->line("warehouse"), 'required');
		$this->form_validation->set_rules('order_type', $this->lang->line("order_type"), 'required');
		if ($this->form_validation->run() == true) {
			$devices_check = $this->site->devicesCheck($api_key);
			
			if($devices_check == $devices_key){						
				if($this->pos_settings->sales_item_in_pos == 1){ 
					$data = $this->pos_api->GetAllrecipe($subcategory_id, $warehouse_id,$order_type);
				}else{
					$data = $this->pos_api->GetAllrecipe_withdays($subcategory_id, $warehouse_id,$order_type);
				}
				if(!empty($data)){
					$result = array( 'status'=> true , 'message'=> lang('recipe'),'message_khmer'=> html_entity_decode(lang('recipe_khmer')), 'item_run_time_discount_option' => $setting->manual_item_discount,'data' => $data);
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
				$data = $this->pos_api->GetRecipedetails($recipe_id);
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

/*07-05-2019 for get variant details from recipe id */
	public function get_variants_from_recipe_post(){
		
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$recipe_id = $this->post('recipe_id');				
		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		$this->form_validation->set_rules('recipe_id', $this->lang->line("recipe_id"), 'required');		
		if ($this->form_validation->run() == true) {
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){	
				$data = $this->pos_api->GetVaraintDetails($recipe_id);
				if(!empty($data)){
					$result = array( 'status'=> true , 'message'=> lang('variant_details'),'message_khmer'=> html_entity_decode(lang('variant_details_khmer')), 'data' => $data);
				}else{
					$result = array( 'status'=> false , 'message'=> lang('variant_details_empty'),'message_khmer'=> html_entity_decode(lang('variant_details_empty_khmer')));
				}
			}else{
				$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
			}
		}else{
			$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));	
		}
		$this->response($result);
	}
/*07-05-2019 for get variant details from recipe id */

/*21-04-2019 addon for recipe or variant*/
	public function get_addon_from_recipe_or_varaint_post(){
		
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');

		$recipe_id = $this->post('recipe_id');
		$variant_id = $this->post('variant_id');
		
		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		$this->form_validation->set_rules('recipe_id', $this->lang->line("recipe_id"), 'required');		
		if ($this->form_validation->run() == true) {
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){	
			    if($variant_id != 0){
					$data = $this->pos_api->getrecipeVariantAddons($variant_id,$recipe_id);
			    }else{
					$data = $this->pos_api->getrecipeAddons($recipe_id);
			    }	 
				if(!empty($data)){
					$result = array( 'status'=> true , 'message'=> lang('addon_details'),'message_khmer'=> html_entity_decode(lang('addon_details_khmer')), 'data' => $data);
				}else{
					$result = array( 'status'=> false , 'message'=> lang('addon_details_empty'),'message_khmer'=> html_entity_decode(lang('addon_details_empty_khmer')));
				}
			}else{
				$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
			}
		}else{
			$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));	
		}
		$this->response($result);
	}

/*21-04-2019 addon for recipe or variant*/	

/*09-09-2019 Customzec Item for recipe or variant*/
	public function get_customized_from_recipe_or_varaint_post(){
		
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');

		$recipe_id = $this->post('recipe_id');
		$variant_id = $this->post('variant_id');
		
		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		$this->form_validation->set_rules('recipe_id', $this->lang->line("recipe_id"), 'required');		
		if ($this->form_validation->run() == true) {
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){	
			    if($variant_id != 0){
					$data = $this->pos_api->getrecipeVariantCustomizable($variant_id,$recipe_id);
			    }else{
					$data = $this->pos_api->getrecipeCustomizable($recipe_id);
			    }	 
				if(!empty($data)){
					$result = array( 'status'=> true , 'message'=> lang('item_customized_details'),'message_khmer'=> html_entity_decode(lang('item_customized_details_khmer')), 'data' => $data);
				}else{
					$result = array( 'status'=> false , 'message'=> lang('item_customized_details_empty'),'message_khmer'=> html_entity_decode(lang('item_customized_details_empty_khmer')));
				}
			}else{
				$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
			}
		}else{
			$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));	
		}
		$this->response($result);
	}

/*09-09-2019 Customzec Item for recipe or variant*/

	public function alltablecategory_post(){
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
				$data = $this->pos_api->Alltablecategory($warehouse_id);
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
				$data = $this->pos_api->GetAlltablecategory($warehouse_id);
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
	
	public function alltables_post(){
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		
		$area_id = $this->post('area_id');
		//$type_id = $this->post('type_id');
		
		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		$this->form_validation->set_rules('area_id', $this->lang->line("area"), 'required');
		$this->form_validation->set_rules('warehouse_id', $this->lang->line("warehouse"), 'required');
		if ($this->form_validation->run() == true) {
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){	
				
				/*if($type_id == 'suki'){*/
					$data = $this->pos_api->DineGetAlltables($area_id, $warehouse_id, $user_id);
					if(!empty($data)){
						
						$result = array( 'status'=> true , 'message'=> lang('tables_datas'),'message_khmer'=> html_entity_decode(lang('tables_datas_khmer')), 'data' => $data);
					}/*else{
						$result = array( 'status'=> false , 'message'=> lang('tables_empty'),'message_khmer'=> html_entity_decode(lang('dine_tables_empty_khmer')));
					}
				}elseif($type_id == 'bbq'){
				
					$data = $this->pos_api->BBQGetAlltables($area_id, $warehouse_id, $user_id);
					if(!empty($data)){
						
						$result = array( 'status'=> true , 'message'=> lang('bbq_tables_datas'),'message_khmer'=> html_entity_decode(lang('bbq_tables_datas_khmer')), 'data' => $data);
					}else{
						$result = array( 'status'=> false , 'message'=> lang('bbq_tables_empty'),'message_khmer'=> html_entity_decode(lang('bbq_tables_empty_khmer')));
					}
				
				}*/else{
					$result = array( 'status'=> false , 'message'=> lang('tables_empty'),'message_khmer'=> html_entity_decode(lang('tables_empty_khmer')));		
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
				//$data = $this->pos_api->GetAlltables($area_id, $warehouse_id, $user_id);
				$data = $this->pos_api->DineGetAlltables($area_id, $warehouse_id, $user_id);
				
				if(!empty($data)){
					
					$result = array( 'status'=> true , 'message'=> lang('tables_datas'),'message_khmer'=> html_entity_decode(lang('tables_datas_khmer')), 'data' => $data);
				}else{
					$result = array( 'status'=> false , 'message'=> lang('tables_empty'),'message_khmer'=> html_entity_decode(lang('tables_empty_khmer')));
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
			$data = $this->pos_api->GetAllcurrencies();
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
			$data = $this->pos_api->GetAlltaxs();
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
			$data = $this->pos_api->GetAllwarehouses();
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
			$data = $this->pos_api->GetAllgroups();
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
			$data = $this->pos_api->GetAllcustomer_groups();
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
		$possetting = $this->pos_api->getPOSSettingsALL();
		if($devices_check == $devices_key){
			$data = $this->pos_api->GetAllcustomers();
			if(!empty($data)){
				$result = array( 'status'=> true , 'message'=> lang('customer'),'message_khmer'=> html_entity_decode(lang('customer_khmer')), 'data' => $data,  'default_customer' => $possetting->default_customer);
			}else{
				$result = array( 'status'=> false , 'message'=> lang('customer_empty'),'message_khmer'=> html_entity_decode(lang('customer_empty_khmer')));
			}
		}else{
			$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
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
			$data = $this->pos_api->GetAllsuppliers();
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
				$data = $this->pos_api->GetAlldeliveryusers($warehouse_id);
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
		
		
		
		$this->form_validation->set_rules('name', $this->lang->line("name"), 'required');
		$this->form_validation->set_rules('phone', $this->lang->line("phone"), 'required|is_unique[companies.email]');
		//$this->form_validation->set_rules('email', lang("email_address"), 'required|is_unique[companies.email]');
		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		if ($this->form_validation->run() == true) {	
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){
				//$cg = $this->site->getCustomerGroupByID($this->input->post('customer_group'));
				$customer_array = array(
					'name' => $this->input->post('name') ? $this->input->post('name') : '',
					'email' => $this->input->post('email') ? $this->input->post('email') : '',
					'ref_id' => 'CUS-'.date('YmdHis'),
					'group_id' => '3',
					'group_name' => 'customer',
					'phone' => $this->input->post('phone') ? $this->input->post('phone') : '',
				);
				
				$data = $this->pos_api->Insertcustomer($customer_array);
				if(!empty($data)){
					$result = array( 'status'=> true , 'message'=> lang('new_customer_added'),'message_khmer'=> html_entity_decode(lang('new_customer_added_khmer')), 'data' => $data);
				}else{
					$result = array( 'status'=> false , 'message'=> lang('new_customer_not_added_users'),'message_khmer'=> html_entity_decode(lang('new_customer_not_added_khmer')));
				}
			}else{
				$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
			}
		}else{
			$result = array( 'status'=> false , 'message'=> 'Already existing phone number.');	
		}
		$this->response($result);
	}
	
	public function notificationcount_post(){
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		
		
		
		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		if ($this->form_validation->run() == true) {	
		 	$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){
				$data = $this->pos_api->Getnotification($group_id, $user_id, $warehouse_id);
				
				if(!empty($data)){
					$result = array( 'status'=> true , 'message'=> lang('notification_count'),'message_khmer'=> html_entity_decode(lang('notification_count_khmer')), 'count' => $data['notification_count']);
				}else{
					$result = array( 'status'=> false , 'message'=> lang('notification_count_empty'),'message_khmer'=> html_entity_decode(lang('notification_count_empty_khmer')));
				}
			}else{
				$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
			}
		}else{
			$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));	
		}
		$this->response($result);
	}
	
	public function notificationtags_post(){
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		
		$tags = $this->input->post('tag');
		
		
		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		if ($this->form_validation->run() == true) {	
		 	$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){

				//echo 'test';die;
				$data = $this->pos_api->GetnotificationTags($group_id, $user_id, $warehouse_id,$tags);
				
				if(!empty($data)){
					$result = array( 'status'=> true , 'message'=> lang('afication_list'),'message_khmer'=> html_entity_decode(lang('notification_list_khmer')), 
						'tags' => $tags, 'data' => $data);
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

	public function notification_post(){
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		$status = $this->input->post('status');
		
		
		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		if ($this->form_validation->run() == true) {	
		 	$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){
				$data = $this->pos_api->Getnotification($group_id, $user_id, $warehouse_id,$status);
				
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
				//$data = $this->pos_api->notification_clear($notification_id);	
				$result = array( 'status'=> true , 'message'=> lang('notification_clear_success'),'message_khmer'=> html_entity_decode(lang('notification_clear_success_khmer')));
				/*if(!empty($data)){
					$result = array( 'status'=> true , 'message'=> lang('notification_clear_success'),'message_khmer'=> html_entity_decode(lang('notification_clear_success_khmer')));
				}else{
					$result = array( 'status'=> false , 'message'=> lang('notification_clear_not_success'),'message_khmer'=> html_entity_decode(lang('notification_clear_not_success_khmer')));
				}*/
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
			$data = $this->pos_api->GetAllcostomerDiscounts();
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
	function reprint_get(){
		$api_key = $this->input->get('api-key');
		$devices_key = $this->input->get('devices_key');
		$user_id = $this->input->get('user_id');
		$group_id = $this->input->get('group_id');
		$warehouse_id = $this->input->get('warehouse_id');
		
		$devices_check = $this->site->devicesCheck($api_key);
		if($devices_check == $devices_key){
			$start = $this->input->get('date');
			$type = $this->input->get('type');
			$bill_no = $this->input->get('bill_no'); 
			if($start){
				$start = $start;
			}
			else{$start =  date('Y-m-d');}
		
			$data = $this->pos_api->getAllBillingforReprint($start,$type,$bill_no,$warehouse_id);
			if(!empty($data)){
				$result = array( 'status'=> true , 'message'=> lang('reprint_bills'),'message_khmer'=> html_entity_decode(lang('reprint_bills')), 'data' => $data);
			}else{
				$result = array( 'status'=> false , 'message'=> lang('no_bills'),'message_khmer'=> html_entity_decode(lang('no_bills')));
			}
		}else{
			$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
		}
		$this->response($result);
	}
	function reprint_bill_get(){
		$api_key = $this->input->get('api-key');
		$devices_key = $this->input->get('devices_key');
		$user_id = $this->input->get('user_id');
		$group_id = $this->input->get('group_id');
		$warehouse_id = $this->input->get('warehouse_id');
		
		$devices_check = $this->site->devicesCheck($api_key);
		if($devices_check == $devices_key){
			$bill_id = $this->input->get('bill_id'); 
			
			$this->site->send_to_bill_print($bill_id,'reprint');
		
			$data = 'printed';
			if($data){
				$result = array( 'status'=> true , 'message'=> lang('bill_sent_to_printer'),'message_khmer'=> html_entity_decode(lang('bill_sent_to_printer')));
			}else{
				$result = array( 'status'=> false , 'message'=> lang('error'),'message_khmer'=> html_entity_decode(lang('error')));
			}
		}else{
			$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
		}
		$this->response($result);
	}
	function bill_preview_get(){
		$api_key = $this->input->get('api-key');
		$devices_key = $this->input->get('devices_key');
		$user_id = $this->input->get('user_id');
		$group_id = $this->input->get('group_id');
		$warehouse_id = $this->input->get('warehouse_id');
		
		$devices_check = $this->site->devicesCheck($api_key);
		if($devices_check == $devices_key){
			$bill_id = $this->input->get('bill_id'); 
			
			$data = $this->pos_api->getBillDetails($bill_id,$warehouse_id);
		
			if($data){
				$result = array( 'status'=> true , 'message'=> lang('bill_details'),'message_khmer'=> html_entity_decode(lang('bill_details')),'data'=>$data);
			}else{
				$result = array( 'status'=> false , 'message'=> lang('error'),'message_khmer'=> html_entity_decode(lang('error')));
			}
		}else{
			$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
		}
		$this->response($result);
	}
	function bill_print_get(){
		$api_key = $this->input->get('api-key');
		$devices_key = $this->input->get('devices_key');
		$user_id = $this->input->get('user_id');
		$group_id = $this->input->get('group_id');
		$warehouse_id = $this->input->get('warehouse_id');
		
		$devices_check = $this->site->devicesCheck($api_key);
		if($devices_check == $devices_key){
			$bill_id = $this->input->get('bill_id'); 
			
			$this->site->send_to_bill_print($bill_id);
		
			$data = 'printed';
			if($data){
				$result = array( 'status'=> true , 'message'=> lang('bill_sent_to_printer'),'message_khmer'=> html_entity_decode(lang('bill_sent_to_printer')));
			}else{
				$result = array( 'status'=> false , 'message'=> lang('error'),'message_khmer'=> html_entity_decode(lang('error')));
			}
		}else{
			$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
		}
		$this->response($result);
	}
	function update_discount_post(){
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		
		$devices_check = $this->site->devicesCheck($api_key);
		if($devices_check == $devices_key){
			$billid = $this->input->post('bill_id');
			$discount_id = $this->input->post('discount_id');
			
			$cusdis_val = $this->site->getCustomerDiscountval($discount_id);

			$dis_val = $cusdis_val.'%';
			$data = true;
			// $result = $this->pos_model->getCustomerDiscount($billid);die;
			if($result = $this->site->getCustomerDiscount_billID($billid)){
				if($discount_id != $result->customer_discount_id){
				$return =  $this->site->update_bill_withcustomer_discount($billid,$discount_id,$dis_val);
			   }
			   
			}  
			
			if($data){
				$result = array( 'status'=> true , 'message'=> lang('discount_applied'),'message_khmer'=> html_entity_decode(lang('discount_applied')));
			}else{
				$result = array( 'status'=> false , 'message'=> lang('error'),'message_khmer'=> html_entity_decode(lang('error')));
			}
		}else{
			$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
		}
		$this->response($result);
	}
	
}
