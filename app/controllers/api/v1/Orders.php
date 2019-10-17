<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;



class Orders extends REST_Controller {

	function __construct() {
		parent::__construct();
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		$this->load->api_model('orders_api');
		$this->lang->admin_load('engliah_khmer','english');
	}
	
	public function customerdiscount_post_20_09_2018(){
		
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		
		$bbq_order_type = 4;
		$dine_order_type = 1;
		$bill_type = 4;
		$table_id = $this->input->post('table_id');
		$split_id = $this->input->post('split_id');
		$bils = $this->input->post('bils');
		$customer_discount_id = $this->input->post('customer_discount_id');

		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		
		/*$this->form_validation->set_rules('bill_type', $this->lang->line("bill_type"), 'required');*/
		$this->form_validation->set_rules('table_id', $this->lang->line("table_id"), 'required');
		$this->form_validation->set_rules('split_id', $this->lang->line("split_id"), 'required');
		/*$this->form_validation->set_rules('bils', $this->lang->line("bils"), 'required');
		$this->form_validation->set_rules('warehouse_id', $this->lang->line("warehouse_id"), 'required');*/
		$this->form_validation->set_rules('user_id', $this->lang->line("user_id"), 'required');
		
		if ($this->form_validation->run() == true) {
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){	
				
				//$data['possettings'] = $this->orders_api->getPOSSettings();
				//$data['settings'] = $this->orders_api->getSettings();
				//$data['tax_rates'] = $this->site->getAllTaxRates();
				//$data['customer_discount'] = $this->site->GetAllcostomerDiscounts();
				//$data['bbq_discount'] = $this->site->GetAllBBQDiscounts();
				
				$customer_discount_value = $this->site->GetIDBycostomerDiscounts($customer_discount_id);
				//$data['cus_group_dis'] = $this->site->getGroupcustomer($customer_discount_id);
				$discount_value = $customer_discount_value ? $customer_discount_value : 0;
				$item_data = $this->orders_api->customergetBil($table_id, $split_id, $user_id);
				// print_r($item_data);die;
				foreach($item_data['items'] as $item_row){
					foreach($item_row as $item){
						$order_item[] = $item;
						$total_price[] = $item->unit_price;
					}
				}
				
				$total_price = array_sum($total_price);
				$data['discount_value'] = $this->site->calculate_Discount($discount_value, $total_price);
				
				//$data['order_item'] = $order_item;
				$result = array( 'status'=> true , 'message'=> lang('customer_discount_amount'),'message_khmer'=> html_entity_decode(lang('customer_discount_amount_khmer')), 'data' => $data);
			}else{
				$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
			}
		}else{
			$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));	
		}
		
		$this->response($result);
	}

	public function customerdiscount_post(){
		
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		
		$bbq_order_type = 4;
		$dine_order_type = 1;
		$bill_type = 4;
		$table_id = $this->input->post('table_id');
		$split_id = $this->input->post('split_id');
		$bils = $this->input->post('bils');
		$customer_discount_id = $this->input->post('customer_discount_id');

		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		$this->form_validation->set_rules('table_id', $this->lang->line("table_id"), 'required');
		$this->form_validation->set_rules('split_id', $this->lang->line("split_id"), 'required');		
		$this->form_validation->set_rules('user_id', $this->lang->line("user_id"), 'required');
		
		if ($this->form_validation->run() == true) {
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){	
				
				$customer_discount_value = $this->site->GetIDBycostomerDiscounts($customer_discount_id);				
				$discount_value = $customer_discount_value ? $customer_discount_value : 0;
				$item_data = $this->orders_api->customergetBil($table_id, $split_id, $user_id);
				// print_r($item_data);die;
				foreach($item_data['items'] as $item_row){
					foreach($item_row as $item){
						$discount = $this->site->discountMultiple($item->recipe_id);
						$order_item[] = $item;
						// $total_price[] = $item->unit_price;
						$price_total =  $item->unit_price;
						$finalAmt = $item->unit_price*$item->quantity;

						$dis = 0;
							if(!empty($discount)){                           
							    if($discount[2] == 'percentage_discount'){

							        $discount_value = $discount[1].'%';

							    }else{
								    $discount_value =$discount[1];
							    }							    
							    $dis = $this->site->calculateDiscount($discount_value, $price_total);
							    $finalAmt = $price_total - $dis;
							}

							/********* offer discount *****************/
								$TotalDiscount = $this->site->TotalDiscount();
						                $offer_dis = 0;
						                if(!empty($TotalDiscount) && $TotalDiscount[0] != 0){                                     
						                    if($TotalDiscount[3] == 'percentage_discount'){
						                        $totdiscount = $TotalDiscount[1].'%';
								    }else{
									$totdiscount =$TotalDiscount[1];
								    }
								    $offerdiscount = $this->site->calculateDiscount($totdiscount, $finalAmt);                                    
								    $offer_dis = $offerdiscount;
								    $finalAmt = $finalAmt - $offer_dis;  
						                }        
								/****************          ***************/

								// $recipe[$key]['id']  = $recipe_id;
								$recipe_id = $item->recipe_id;
								$subgroup_id =$item->subcategory_id;
								$recipes_discount_amt = $this->orders_api->recipe_customer_discount_calculation_api($recipe_id,$item->category_id,$subgroup_id,$finalAmt,$customer_discount_id);
								$amt +=$recipes_discount_amt;
								$data['discount_value'] = $amt;

					}
				}
				
				/*$total_price = array_sum($total_price);
				$data['discount_value'] = $this->site->calculate_Discount($discount_value, $total_price);*/				
				$result = array( 'status'=> true , 'message'=> lang('customer_discount_amount'),'message_khmer'=> html_entity_decode(lang('customer_discount_amount_khmer')), 'data' => $data);
			}else{
				$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
			}
		}else{
			$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));	
		}
		
		$this->response($result);
	}	

	public function bbqdiscount_post(){
		
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		
		$bbq_order_type = 4;
		$dine_order_type = 1;
		$bill_type = 4;
		$table_id = $this->input->post('table_id');
		$split_id = $this->input->post('split_id');
		$bbq_discount_id = $this->input->post('bbq_discount_id');

		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		
		/*$this->form_validation->set_rules('bill_type', $this->lang->line("bill_type"), 'required');*/
		$this->form_validation->set_rules('table_id', $this->lang->line("table_id"), 'required');
		$this->form_validation->set_rules('split_id', $this->lang->line("split_id"), 'required');
		/*$this->form_validation->set_rules('bils', $this->lang->line("bils"), 'required');
		$this->form_validation->set_rules('warehouse_id', $this->lang->line("warehouse_id"), 'required');*/
		$this->form_validation->set_rules('user_id', $this->lang->line("user_id"), 'required');
		
		if ($this->form_validation->run() == true) {
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){	
				
				//$data['possettings'] = $this->orders_api->getPOSSettings();
				//$data['settings'] = $this->orders_api->getSettings();
				//$data['tax_rates'] = $this->site->getAllTaxRates();
				//$data['customer_discount'] = $this->site->GetAllcostomerDiscounts();
				//$data['bbq_discount'] = $this->site->GetAllBBQDiscounts();
				
				$bbq_discount_value = $this->site->GetIDByBBQDiscounts($bbq_discount_id);
				//$data['cus_group_dis'] = $this->site->getGroupcustomer($customer_discount_id);
				$discount_value = $bbq_discount_value ? $bbq_discount_value : 0;
				$item_data = $this->orders_api->customergetBil($table_id, $split_id, $user_id);
				foreach($item_data['items'] as $item_row){
					foreach($item_row as $item){
						$order_item[] = $item;
						$total_price[] = $item->unit_price;
					}
				}
				
				$total_price = array_sum($total_price);
				$data['discount_value'] = $this->site->calculate_Discount($discount_value, $total_price);
				
				//$data['order_item'] = $order_item;
				$result = array( 'status'=> true , 'message'=> lang('bbq_discount_amount'),'message_khmer'=> html_entity_decode(lang('bbq_discount_amount_khmer')), 'data' => $data);
			}else{
				$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
			}
		}else{
			$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));	
		}
		
		$this->response($result);
	}
	
	
	public function billing_post(){
		
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		
		$waiter_id = $this->input->post('user_id');
		
		$bbq_order_type = 4;
		$dine_order_type = 1;
		$bill_type = 4;
		// $table_id = $this->input->post('table_id');
		$split_id = $this->input->post('split_id');
		$table_id = $this->orders_api->gettableidbysplitid($split_id);
		$GP = $this->site->getGroupPermissions($group_id);
		$bils = $this->input->post('bils');					
		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		/*$this->form_validation->set_rules('bill_type', $this->lang->line("bill_type"), 'required');*/
		//$this->form_validation->set_rules('table_id', $this->lang->line("table_id"), 'required');
		//$this->form_validation->set_rules('split_id', $this->lang->line("split_id"), 'required');
		/*$this->form_validation->set_rules('bils', $this->lang->line("bils"), 'required');
		$this->form_validation->set_rules('warehouse_id', $this->lang->line("warehouse_id"), 'required');*/
		$this->form_validation->set_rules('user_id', $this->lang->line("user_id"), 'required');
		
		if ($this->form_validation->run() == true) {
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){	
				
				$possettings[] = $this->orders_api->getPOSSettings();
				$settings[] = $this->orders_api->getSettings();
				
				$pos_settings = $this->orders_api->getServiceChargeSettings();		
				$posallsettings = $this->orders_api->getALLPosSettings();		
				$service_charge_option = $pos_settings->service_charge_option ? $pos_settings->service_charge_option :0;
				$ServiceCharge[] ='';
				if($pos_settings->service_charge_option != 0 && $pos_settings->default_service_charge !=0)
				{  
				    $ServiceCharge = $this->site->getServiceChargeByID($pos_settings->default_service_charge);
					if(!empty($ServiceCharge)){
						$ServiceChargearr[] = $ServiceCharge;
						$service_charge_option = $pos_settings->service_charge_option;
					}else{
						$ServiceChargearr[] = '';
						$service_charge_option = 0;
					}
				}

				//$data['tax_rates'] = $this->site->getAllTaxRates();
				// $customer_discount = $this->site->GetAllcostomerDiscounts();
				if($settings[0]->customer_discount == 'automanual'){
					$customer_discount = $this->site->GetAllcostomerDiscounts();
				}				

				if($settings[0]->bbq_discount == 'automanual'){
					$bbq_discount = $this->site->GetAllBBQDiscounts();
				}
				
				// $bbq_discount = $this->site->GetAllBBQDiscounts();

				// print_r($bbq_discount);die;
				$item_data = $this->orders_api->dinevaluegetBil($table_id, $split_id, $user_id);
				foreach($item_data['items'] as $item_row){
					foreach($item_row as $item){
						
						$item->discount_enable = 0;
						$item->item_discount = $this->site->CalculatesimpleDiscount($item);
						$addons = $this->site->getAddonByRecipeidAndOrderitemid($item->recipe_id, $item->id);
						$item->addon = $addons ? $addons :"0";
						if($item->variant == ''){
							$item->variant = "0";
						}
						
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
				
				if(!empty($order_item) && !empty($order_data)){
					$data_dine['order_item'] = $order_item;
					$data_dine['order_data'] = $order_data;
				}else{
					$data_dine = '0';
					
				}
				$order_bbq = $this->orders_api->BBQtablesplit($table_id, $split_id);
				/*echo "<pre>";
				print_r($order_bbq);die;*/
				if(!empty($order_bbq)){
				$current_days = date('l');
					
				if($order_bbq->bbq_menu_id !=1){
					$lobsterdiscount = $this->site->getBBQlobsterDAYS($current_days);
				}else{
					$buyxgetx = $this->site->getBBQbuyxgetxDAYS($current_days);	
				}				
					 $adult = 0; 
	                 $child = 0; 
	                 $kids = 0; 
	                 $adult_discoint ="0";	
	                 $child_discoint = "0";
	                 $kids_discoint = "0";			
					if($order_bbq->bbq_menu_id !=1){

						$adult = $this->site->CalculationBBQlobster($order_bbq->number_of_adult,$order_bbq->adult_price,$lobsterdiscount->discount_apply_type,$lobsterdiscount->discount_type,$lobsterdiscount->adult_discount_val);
                         $child = $this->site->CalculationBBQlobster($order_bbq->number_of_child,$order_bbq->child_price,$lobsterdiscount->discount_apply_type,$lobsterdiscount->discount_type,$lobsterdiscount->child_discount_val);
                        $kids = $this->site->CalculationBBQlobster($order_bbq->number_of_kids,$order_bbq->kids_price,$lobsterdiscount->discount_apply_type,$lobsterdiscount->discount_type,$lobsterdiscount->kids_discount_val);

                        if($lobsterdiscount->minimum_cover <= $order_bbq->number_of_adult && $order_bbq->number_of_adult != 0){
                            $adult_discoint = $this->site->CalculationBBQlobster($order_bbq->number_of_adult,$order_bbq->adult_price,$lobsterdiscount->discount_apply_type,$lobsterdiscount->discount_type,$lobsterdiscount->adult_discount_val);
                        }

                        $adult_subprice = ($order_bbq->adult_price * $order_bbq->number_of_adult)-$adult_discoint;

                         if($lobsterdiscount->minimum_cover <= $order_bbq->number_of_child && $order_bbq->number_of_child != 0){
                                 $child_discoint = $this->site->CalculationBBQlobster($order_bbq->number_of_child,$order_bbq->child_price,$lobsterdiscount->discount_apply_type,$lobsterdiscount->discount_type,$lobsterdiscount->child_discount_val);
                                }
                          $child_subprice = ($order_bbq->child_price * $order_bbq->number_of_child)-$child_discoint;
                        if($lobsterdiscount->minimum_cover <= $order_bbq->number_of_kids && $order_bbq->number_of_kids != 0){
                                $kids_discoint = $this->site->CalculationBBQlobster($order_bbq->number_of_kids,$order_bbq->kids_price,$lobsterdiscount->discount_apply_type,$lobsterdiscount->discount_type,$lobsterdiscount->kids_discount_val);
                                }                                
                        $kids_subprice = ($order_bbq->kids_price * $order_bbq->number_of_kids) - $kids_discoint;
                                   

					}else{
					
						$adult = $this->site->CalculationBBQbuyget($buyxgetx->adult_buy, $buyxgetx->adult_get, $order_bbq->number_of_adult);
						$child = $this->site->CalculationBBQbuyget($buyxgetx->child_buy, $buyxgetx->child_get, $order_bbq->number_of_child);
						$kids = $this->site->CalculationBBQbuyget($buyxgetx->kids_buy, $buyxgetx->kids_get, $order_bbq->number_of_kids);
					
						$adult_subprice = ($order_bbq->adult_price * $order_bbq->number_of_adult) - ($order_bbq->adult_price * $adult);

						$child_subprice = ($order_bbq->child_price * $order_bbq->number_of_child) - ($order_bbq->child_price * $child);

						$kids_subprice = ($order_bbq->kids_price * $order_bbq->number_of_kids) - ($order_bbq->kids_price * $kids);
					}

					/*11-09-2019 daywise discount */	
					$DaywiseDiscount = $this->site->getBBQDaywiseDiscountforpos($order_bbq->bbq_menu_id);  
					 $adult_daywise_discount =0;
                    if($DaywiseDiscount->adult_discount_val !=0){
                        $amount = $adult_subprice;
                        $adult_daywise_discount = $this->site->CalculationBBQDauwiseDiscount($amount,$DaywiseDiscount->discount_type, $DaywiseDiscount->adult_discount_val);
                    } 

                    $child_daywise_discount =0;
                    if($DaywiseDiscount->child_discount_val !=0){
                        $amount = $child_subprice;
                        $child_daywise_discount = $this->site->CalculationBBQDauwiseDiscount($amount,$DaywiseDiscount->discount_type, $DaywiseDiscount->child_discount_val);
                    }

					$kids_daywise_discount =0;
                    if($DaywiseDiscount->kids_discount_val !=0){
                        $amount = $kids_subprice;
                        $kids_daywise_discount = $this->site->CalculationBBQDauwiseDiscount($amount,$DaywiseDiscount->discount_type, $DaywiseDiscount->kids_discount_val);
                    } 
                    $adult_subprice =$adult_subprice-$adult_daywise_discount;
					$child_subprice = $child_subprice-$child_daywise_discount;
					$kids_subprice = $kids_subprice-$kids_daywise_discount;
				/*11-09-2019 daywise discount Sivan*/	
					$bbq_covers[] = array(
						'adult_price' => $order_bbq->adult_price,
						'number_of_adult' => $order_bbq->number_of_adult,
						'adult_days' => $current_days,
						'adult_price' => $order_bbq->adult_price,
						'adult_buyx' => $buyxgetx->adult_buy ? $buyxgetx->adult_buy : 0,
						'adult_get' => $buyxgetx->adult_get ? $buyxgetx->adult_get : 0,
						'adult_discount_cover' => $adult,
						'adult_subprice' => $adult_subprice,						
						'adult_daywise_discount' => $adult_daywise_discount,						
						'bbq_daywise_discount_edit_permission' => $GP->bbq_daywise_discount_edit_permission,						
					);										
					
					$bbq_covers[] = array(
						'child_price' => $order_bbq->child_price,
						'number_of_child' => $order_bbq->number_of_child,
						'child_days' => $current_days,
						'child_price' => $order_bbq->child_price,
						'child_buyx' => $buyxgetx->child_buy ? $buyxgetx->child_buy : 0,
						'child_get' => $buyxgetx->child_get ? $buyxgetx->child_get : 0,
						'child_discount_cover' => $child,
						'child_subprice' => $child_subprice,
						'child_daywise_discount' => $child_daywise_discount,	
						'bbq_daywise_discount_edit_permission' => $GP->bbq_daywise_discount_edit_permission,						
					);
					
					
					
					$bbq_covers[] = array(
						'kids_price' => $order_bbq->kids_price,
						'number_of_kids' => $order_bbq->number_of_kids,
						'kids_days' => $current_days,
						'kids_price' => $order_bbq->kids_price,
						'kids_buyx' => $buyxgetx->kids_buy ? $buyxgetx->kids_buy : 0,
						'kids_get' => $buyxgetx->kids_get ? $buyxgetx->kids_get : 0,
						'kids_discount_cover' => $kids,
						'kids_subprice' => $kids_subprice,
						'kids_daywise_discount' => $kids_daywise_discount,	
						'bbq_daywise_discount_edit_permission' => $GP->bbq_daywise_discount_edit_permission,
						
					);
				}
				
				$order_bbq->total_cover = $order_bbq->number_of_adult + $order_bbq->number_of_child + $order_bbq->number_of_kids;
				$order_bbq->total_amount =  ($adult_subprice) + ($child_subprice) + ($kids_subprice);
				
				$bbq_covers = $bbq_covers ? $bbq_covers : array();
				$order_bbq = $order_bbq ? $order_bbq : array();
				
				if(!empty($bbq_covers) && !empty($order_bbq)){
					$data_bbq['bbq_covers'] = $bbq_covers;
					$data_bbq['order_bbq'] = $order_bbq;
				}else{
					$data_bbq = '0';
					
				}
				
				$customer_id = $this->site->getCustomerDetails($waiter_id, $table_id, $split_id);
				$discount_select[] = $this->orders_api->getDiscountdata($customer_id, $waiter_id, $table_id, $split_id);

				
				$is_unique = $this->site->is_uniqueDiscountExist();
				if(!empty($is_unique)){
					$automatic = 1;
					
					$item_data = $this->orders_api->getBil($table_id, $split_id, $waiter_id);
					
					foreach($item_data['items'] as $item){
						$item->id = $item->recipe_id;
						$simple_discount[] = $this->site->CalculatesimpleDiscount($item);
							
					}
					$automatic_discount = array_sum($simple_discount) ? array_sum($simple_discount) : 0;
					$settings[0]->bbq_discount = 'none';
					$settings[0]->customer_discount = 'none';
					
				}else{
					//$automatic = 0;
					//$automatic_discount = 0;
					$automatic = 0;
					
					$item_data = $this->orders_api->getBil($table_id, $split_id, $waiter_id);
					
					foreach($item_data['items'] as $item){
						$item->id = $item->recipe_id;
						$simple_discount[] = $this->site->CalculatesimpleDiscount($item);
							
					}
					$automatic_discount = array_sum($simple_discount) ? array_sum($simple_discount) : 0;
					
				}
				// print_r($bbq_discount);die;
				
				$result = array( 'status'=> true , 'message'=> lang('biller_generator_in_data'),'message_khmer'=> html_entity_decode(lang('biller_generator_in_data_khmer')), 'possettings' => $possettings, 'discount_select' => $discount_select, 'settings' => $settings, 'customer_discount' => $customer_discount ? $customer_discount : 0, 'bbq_discount' => $bbq_discount ? $bbq_discount : [], 'data_dine' => $data_dine, 'data_bbq' => $data_bbq, 'automatic' => $automatic, 'automatic_discount' => $automatic_discount, 'is_unique' => $is_unique,'service_charge_option' => $service_charge_option,'service_charge_data' => $ServiceCharge,'item_comment_price_option' => $posallsettings->item_comment_price_option);
				
				
					
				
			}else{
				$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
			}
		}else{
			$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));	
		}
		
		$this->response($result);
	}
	
	
	public function billingadd_post(){		
	$api_key = $this->input->post('api-key');
	$devices_key = $this->input->post('devices_key');
	$user_id = $this->input->post('user_id');
	$group_id = $this->input->post('group_id');
	$warehouse_id = $this->input->post('warehouse_id');

	$bbq_order = $this->input->post('bbq_order');
	$dine_order = $this->input->post('dine_order');

	$this->input->post('customer_type_val');
	$this->input->post('customer_discount_val'); 
	$this->input->post('bbq_type_val'); 
	$this->input->post('bbq_discount_val');

	$bbq_order_type = 4;
	$dine_order_type = 1;
	/*$bill_type = $this->input->post('bbq_order');*/
	// $table_id = $this->input->post('table_id');
	$split_id = $this->input->post('split_id');
	$table_id = $this->orders_api->gettableidbysplitid($split_id);
 // var_dump($table_id);die;
	$bils = 1;
	$data['tax_rates'] = $this->site->getAllTaxRates();
	$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
	$this->form_validation->set_rules('user_id', $this->lang->line("user_id"), 'required');
	$this->form_validation->set_rules('warehouse_id', $this->lang->line("warehouse"), 'required');
	$this->form_validation->set_rules('split_id', $this->lang->line("split_id"), 'required');

	$split_status = $this->site->check_splitid_is_bill_generated($split_id);
	if($split_status == FALSE){

	if ($this->form_validation->run() == true) {

	$devices_check = $this->site->devicesCheck($api_key);
	if($devices_check == $devices_key){	

	/*bbq with dine*/
if($bbq_order == 1 && $dine_order == 1){
	if(!empty($dine_order_type)){ //dine
	$this->data['customer_discount'] = $this->site->GetAllcostomerDiscounts();
	$notification_array['customer_role'] = CUSTOMER;
	$notification_array['customer_msg'] = 'Waiter has been bil generator to customer';
	$notification_array['customer_type'] = 'Your bil  generator';

	$notification_array['from_role'] = $group_id;
	$notification_array['insert_array'] = array(
	'msg' => 'Waiter has been bil generator to '.$split_id,
	'type' => 'Bil generator ('.$split_id.')',
	'table_id' =>  $table_id,
	'role_id' => CASHIER,
	'user_id' => $user_id,	
	'warehouse_id' => $warehouse_id,
	'created_on' => date('Y-m-d H:m:s'),
	'is_read' => 0
	);
	$this->data['current_user'] = $this->orders_api->getUserByID($user_id);

	$item_data_dine = $this->orders_api->bildinegetBil($table_id, $split_id, $user_id);


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

	$this->data['order_item'] = $order_item_dine;
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
	'consolidated' => 1
	);

	$notification_array['customer_id'] = $order->customer_id;
	}

	$this->data['order_data'] = $order_data_dine;
	$postData = $this->input->post();
	$delivery_person =  0;

	}
	if(!empty($dine_order_type)){

	for($i=1; $i<=$bils; $i++){

	$tot_item =	$this->input->post('[split]['.$i.'][total_item]');
	$itemdis = $this->input->post('[split]['.$i.'][discount_amount]')/$tot_item;
	$billitem['bills_items'] = array();
	$bills_item['recipe_name'] = $this->input->post('split[][$i][recipe_name][$i]');	
	$splitData_dine = array();

	foreach ($postData['split'][$i]['recipe_name'] as $key => $split) {
	$discount = $this->site->discountMultiple($postData['split'][$i]['recipe_id'][$key]);
	/*item discount */
	/*$discount = $this->site->discountMultiple($postData['split'][$i]['recipe_id'][$key]);
	$subtotal =$postData['split'][$i]['subtotal'][$key]
	$discount_value = '';
	if(!empty($discount)){				                           
	if($discount[2] == 'percentage_discount'){
	$discount_value = $discount[1].'%';
	}else{
	$discount_value =$discount[1];
	}											
	$price_total = $subtotal;
	$item_disco = $this->site->calculateDiscount($discount_value, $price_total);											
	}else{
	$item_disco = 0;											
	}*/
	/*item discount */
	$item_disco = $postData['split'][$i]['item_discount'][$key];	
	$offer_dis = 0.0000;
	if($this->input->post('[split]['.$i.'][tot_dis_value]'))
	{
	$offer_dis = $this->site->calculate_Discount($this->input->post('[split]['.$i.'][tot_dis_value]'), ($postData['split'][$i]['subtotal'][$key]-$postData['split'][$i]['item_discount'][$key]),$this->input->post('[split]['.$i.'][item_dis]'));
	}									

	if($this->input->post('[split]['.$i.'][order_discount_input]'))
	{	
	$subtotal =$postData['split'][$i]['subtotal'][$key];
	$tot_dis1 = $this->input->post('[split]['.$i.'][tot_dis1]');

	$item_dis = $postData['split'][$i]['item_dis'][$key];

	// $item_discount = $postData['split'][$i]['item_discount'][$key];
	$item_discount = $item_disco;
	if($this->input->post('customer_type_val')=="automanual"){
	$recipe_id =  $postData['split'][$i]['recipe_id'][$key];
	$finalAmt = $subtotal - $item_discount -$offer_dis; 
	$customer_discount_status = 'applied';
	$discountid = $this->input->post('[split]['.$i.'][order_discount_input]');
	$recipeDetails = $this->orders_api->getrecipeByID($recipe_id);
	$group_id =$recipeDetails->category_id;

	$input_dis = $this->orders_api->recipe_customer_discount_calculation($this->input->post('customer_type_val'),$recipe_id,$group_id,$finalAmt,$discountid);

	}elseif($this->input->post('customer_type_val')=="manual"){

	$input_dis = $this->site->calculate_Discount($this->input->post('[split]['.$i.'][order_discount_input]'), (($postData['split'][$i]['subtotal'][$key]-$item_disco)-$offer_dis),$tot_dis1 ? $tot_dis1 : $item_dis );
	}elseif($this->input->post('customer_type_val')=="none"){

	$input_dis = $this->site->calculate_Discount($this->input->post('[split]['.$i.'][order_discount_input]'), (($postData['split'][$i]['subtotal'][$key]-$item_disco)-$offer_dis),$tot_dis1 ? $tot_dis1 : $item_dis );
	}
	}
	else{
	$input_dis = 0;
	}

	/*item service charge */
	$item_service_charge = 0;
	if(!empty($postData['split'][$i]['service_charge_id'][$key])){
		
	$item_service_charge = $this->site->calculateServiceCharge($postData['split'][$i]['service_charge_id'][$key], ($postData['split'][$i]['subtotal'][$key]-($offer_dis ? $offer_dis:0) -($input_dis ? $input_dis:0)-($postData['split'][$i]['item_discount'][$key])));	
	
	}
	/*item service charge */

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



	$splitData_dine[$i][] = array(
	'recipe_name' => $split,
	'recipe_variant' => $postData['split'][$i]['varaint'][$key] ? $postData['split'][$i]['varaint'][$key] :0,
	'recipe_variant_id' => $postData['split'][$i]['variant_id'][$key] ? $postData['split'][$i]['variant_id'][$key] : 0,
	'unit_price' => $postData['split'][$i]['unit_price'][$key],
	'net_unit_price' => $postData['split'][$i]['unit_price'][$key]*$postData['split'][$i]['quantity'][$key],
	'warehouse_id' => $warehouse_id,
	'recipe_type' => $postData['split'][$i]['recipe_type'][$key],
	'quantity' => $postData['split'][$i]['quantity'][$key],
	'recipe_id' => $postData['split'][$i]['recipe_id'][$key],
	'recipe_code' => $postData['split'][$i]['recipe_code'][$key],
	'discount' => $discount[0] ? $discount[0] : 0,
	'item_discount' => $item_disco,
	'off_discount' => $offer_dis ? $offer_dis:0,
	'input_discount' => $input_dis ? $input_dis:0,
	'tax_type' => $this->input->post('[split]['.$i.'][tax_type]'), 
	'addon_id' => $postData['split'][$i]['addon'][$key] ? $postData['split'][$i]['addon'][$key] : 0,
	'service_charge_id' =>$postData['split'][$i]['service_charge_id'][$key] ? $postData['split'][$i]['service_charge_id'][$key] : 0,
	'service_charge_amount' => $item_service_charge,
	'tax' => $itemtax,	
	'subtotal' => $sub_val,	
	'grand_total' => $sub_val ? $sub_val : 0,

	);
	}
	if($this->input->post('[split]['.$i.'][order_discount_input]')){
	$cus_discount_type = $this->input->post('customer_type_val');
	$cus_discount_val ='';
	if($this->input->post('customer_type_val')=="automanual"){
	$cus_discount_val = $this->input->post('[split]['.$i.'][order_discount_input]').'%';
	}else if($this->input->post('customer_type_val')=="manual"){
	$cus_discount_val = $this->input->post('[split]['.$i.'][order_discount_input]');
	}else if($this->input->post('customer_type_val')=="none"){
	$cus_discount_val = $this->input->post('[split]['.$i.'][order_discount_input]');
	}
	}else{
	$cus_discount_val ='';$cus_discount_type='';
	}

	$billData_dine[$i] = array(
	'reference_no' => $this->input->post('[split]['.$i.'][reference_no]'),
	'date' => $this->site->getTransactionDate(),
	'created_on' => date('Y-m-d H:i:s'),
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
	'grand_total' => $this->input->post('[split]['.$i.'][grand_total]') ? $this->input->post('[split]['.$i.'][grand_total]') : 0,
	'round_total' => $this->input->post('[split]['.$i.'][round_total]') != NULL ? $this->input->post('[split]['.$i.'][round_total]') : 0,
	'bill_type' => $bill_type != NULL ? $bill_type : 4,
	'order_type' => 1,
	'delivery_person_id' => $delivery_person,
	'order_discount_id' => $this->input->post('[split]['.$i.'][tot_dis_id]')? $this->input->post('[split]['.$i.'][tot_dis_id]') : 0,
	'warehouse_id' => $warehouse_id,
	'created_by' => $user_id,
	'customer_discount_id' => $discountid ? $discountid : 0,
	'discount_type'=>$cus_discount_type ? $cus_discount_type : 0,
	'discount_val'=>$cus_discount_val ? $cus_discount_val: 0,
	'service_charge_id' =>$this->input->post('service_charge_id') ? $this->input->post('service_charge_id') :0,
	'service_charge_amount' => $this->input->post('[split]['.$i.'][service_charge_amount]')? $this->input->post('[split]['.$i.'][service_charge_amount]') : 0,
	'consolidated' => 1
	);
	}
	$sales_total = array_column($billData_dine, 'grand_total');
	$sales_total = array_sum($sales_total);		

	/*echo "<pre>";
	print_r($splitData);				
	print_r($billData);	*/
	

	$dine_response = $this->orders_api->InsertBill($order_data_dine, $order_item_dine, $billData_dine,$splitData_dine, $sales_total, $delivery_person,$timelog_array, $notification_array,$order_item_id);
// var_dump($dine_response);die;
// die;

	}
/*BBQ ORDERS TO INSERT*/
	if(!empty($bbq_order_type)){
	$this->data['bbq_discount'] = $this->site->GetAllBBQDiscounts();
	$bbq_order_id = $this->orders_api->getBBQorderID($split_id);
	}							
	if(!empty($bbq_order_type)){
		for($i=0; $i<$bils; $i++){
			if($_POST['number_of_covers'][$i] != 0){
				$bbq_discount_amount[] = $_POST['bbq_discount_amount'][$i];
				$tax_amount[] = $_POST['tax_amount'][$i];
				$total_amount[] = $_POST['total_amount'][$i];
				$gtotal[] = $_POST['gtotal'][$i];

				$adult_price[] = $_POST['adult_price'][$i];
				$number_of_adult[] = $_POST['number_of_adult'][$i];
				$adult_subprice[] = $_POST['adult_subprice'][$i];

				$child_price[] = $_POST['child_price'][$i];
				$number_of_child[] = $_POST['number_of_child'][$i];
				$child_subprice[] = $_POST['child_subprice'][$i];

				$kids_price[] = $_POST['kids_price'][$i];
				$number_of_kids[] = $_POST['number_of_kids'][$i];
				$kids_subprice[] = $_POST['kids_subprice'][$i];

				$number_of_covers[] = $_POST['number_of_covers'][$i];

				/*$adult_discount_cover[] = $_POST['adult_discount_cover'][$i];
				$child_discount_cover[] = $_POST['child_discount_cover'][$i];
				$kids_discount_cover[] = $_POST['kids_discount_cover'][$i];*/

				/*$adult_daywise_discount[] = $_POST['adult_daywise_discount'][$i];
				$child_daywise_discount[] = $_POST['child_daywise_discount'][$i];
				$kids_daywise_discount[] = $_POST['kids_daywise_discount'][$i];*/
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

	$number_of_covers = array_sum($number_of_covers);

	$adult_discount_cover = array_sum($adult_discount_cover);
	$child_discount_cover = array_sum($child_discount_cover);
	$kids_discount_cover = array_sum($kids_discount_cover);

	$bbq_array = array(
	'number_of_adult' => $number_of_adult,
	'number_of_child' => $number_of_child,
	'number_of_kids' => $number_of_kids
	);

	$item_data_bbq = $this->orders_api->BBQgetBil($table_id, $split_id, $user_id);

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
	if($this->input->post('bbq_type_val') == 'automanual')
	{
	/*$bbq_discount_value = $this->site->GetIDByBBQDiscounts($this->input->post('bbq_discount'));
	$discount_value = $bbq_discount_value ? $bbq_discount_value : 0;
	$other_discount = $discount_value;*/
	$other_discount = $this->input->post('bbq_discount');
	}else{
	$other_discount = $this->input->post('bbq_discount');
	}
	$final_bil =  $final_bil - $TotalDiscount[1];
	$step_bil_2 = $step_bil_1 - $TotalDiscount[1];

	$other_discount_total = $this->site->calculateDiscount($other_discount, $step_bil_2);
	$total_discount =  $other_discount_total + array_sum($total_dis) + $offer_discount;
	$final_bil = $final_bil - $other_discount_total;
	$step_bil_3 = $step_bil_2 - $other_discount_total;

	$total_tax = $this->site->calculateOrderTax( $this->input->post('default_tax'), $final_bil);
	$final_bil = $final_bil;
	$step_bil_4 = $step_bil_3;
	foreach($item_data_bbq['order'] as $order){
	$order_data_bbq = array('sales_type_id' => $order->order_type,
	'sales_split_id' => $order->split_id,
	'sales_table_id' => $order->table_id,
	'date' => $this->site->getTransactionDate(),
	'created_on' => date('Y-m-d H:i:s'),
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
	}

	$sale = array(
	'bilgenerator_type' => 0,
	'sales_type_id' => 4,
	'sales_split_id' => $this->input->post('split_id'),
	'sales_table_id' => $table_id,
	'date' => $this->site->getTransactionDate(),
	'created_on' => date('Y-m-d H:i:s'),
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
	'total_items' => $this->input->post('number_of_covers'),
	'sale_status' => 'Process',
	'consolidated' => 1
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

	$itemtax = $this->site->calculateOrderTax($this->input->post('default_tax'), ($item->subtotal - $off_discount - $input_discount - $item_discount));


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
	'subtotal' => ($item->subtotal/$bil_value - $input_discount/$bil_value) + $itemtax/$bil_value,
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
	'user_id' => $user_id,	
	'warehouse_id' => $warehouse_id,
	'created_on' => date('Y-m-d H:m:s'),
	'is_read' => 0
	);

	$order_bbq = $this->orders_api->BBQtablesplit($table_id, $split_id);
	$current_days = date('l');
	if($order_bbq->bbq_menu_id !=1){
		$lobsterdiscount = $this->site->getBBQlobsterDAYS($current_days);
	}else{
		$buyxgetx = $this->site->getBBQbuyxgetxDAYS($current_days);	
	}				
	 $adult_cover_discount = 0; 
	 $child_cover_discount = 0; 
	 $kids_cover_discount = 0;          		
	if($order_bbq->bbq_menu_id !=1){
	   $adult_cover_discount = $this->site->CalculationBBQlobster($order_bbq->number_of_adult,$order_bbq->adult_price,$lobsterdiscount->discount_apply_type,$lobsterdiscount->discount_type,$lobsterdiscount->adult_discount_val);
	   $child_cover_discount = $this->site->CalculationBBQlobster($order_bbq->number_of_child,$order_bbq->child_price,$lobsterdiscount->discount_apply_type,$lobsterdiscount->discount_type,$lobsterdiscount->child_discount_val);
	   $kids_cover_discount = $this->site->CalculationBBQlobster($order_bbq->number_of_kids,$order_bbq->kids_price,$lobsterdiscount->discount_apply_type,$lobsterdiscount->discount_type,$lobsterdiscount->kids_discount_val);
	}else{
		$adult = $this->site->CalculationBBQbuyget($buyxgetx->adult_buy, $buyxgetx->adult_get, $order_bbq->number_of_adult);
		$adult_cover_discount = $adult * $order_bbq->adult_price;
		$child = $this->site->CalculationBBQbuyget($buyxgetx->child_buy, $buyxgetx->child_get, $order_bbq->number_of_child);
		$child_cover_discount = $child * $order_bbq->child_price;
		$kids = $this->site->CalculationBBQbuyget($buyxgetx->kids_buy, $buyxgetx->kids_get, $order_bbq->number_of_kids);
		$kids_cover_discount = $kids * $order_bbq->kids_price;
	}

	$adult_daywise_discount = $this->input->post('adult_daywise_discount') ? $this->input->post('adult_daywise_discount') :0;
	$child_daywise_discount = $this->input->post('child_daywise_discount') ? $this->input->post('child_daywise_discount') :0;
	$kids_daywise_discount = $this->input->post('kids_daywise_discount') ? $this->input->post('kids_daywise_discount') :0;
	$total_amount = $this->input->post('total_amount') ? $this->input->post('total_amount') :0;
	$total_cover_discount = $adult_cover_discount+$child_cover_discount+$kids_cover_discount;



	for($i=0; $i<$bils; $i++){
	$bilsdata_bbq[$i] = array(
	'bilgenerator_type' => 0,
	'date' => $this->site->getTransactionDate(),
    'created_on' => date('Y-m-d H:i:s'),
	'reference_no' => 'SALE'.date('YmdHis'),
	'customer_id' => $this->input->post('customer_id'),
	'customer' => $this->input->post('customer'),
	'biller_id' => $this->input->post('biller_id'),
	'biller' => $this->input->post('biller'),
	'warehouse_id' => $this->input->post('warehouse_id'),
	'created_by' => $this->input->post('user_id'), 
	'total' => $total_amount + $adult_daywise_discount + $child_daywise_discount + $kids_daywise_discount+$total_cover_discount, 
	'order_discount_id' => $this->input->post('bbq_discount'), 
	'customer_discount_id' => $this->input->post('bbq_discount_val') ? $this->input->post('bbq_discount_val') : 0,
	'total_discount' => $this->input->post('bbq_discount_amount'),
	'bbq_daywise_discount' => $adult_daywise_discount + $child_daywise_discount + $kids_daywise_discount,
	'bbq_cover_discount' =>  $total_cover_discount,
	'tax_id' => $this->input->post('ptax'),
	'total_tax' => $this->input->post('tax_amount'), 
	'tax_type' => $this->input->post('tax_type'),
	'grand_total' => $this->input->post('gtotal'),
	'total_items' => $this->input->post('number_of_covers'),
	'consolidated' => 1,
	'order_type' => 4
	);

	$adult_discount = $this->site->calculateDiscount($this->input->post('bbq_discount'), $this->input->post('adult_subprice'));
	$adult_disfinal = $this->input->post('adult_subprice') - $adult_discount;
	$adult_tax_id = $this->input->post('ptax');
	$adult_tax_type = $this->input->post('tax_type');
	$adult_tax = $this->site->calculateOrderTax( $this->input->post('ptax'), $adult_disfinal);
	$adult_subprice =  $this->input->post('adult_subprice') ? $this->input->post('adult_subprice') :0;

	$bil_items[$i][] = array(
	'type' => 'adult',
	'cover' => $this->input->post('number_of_adult'),
	'price' => $this->input->post('adult_price'),
	'days' => $this->input->post('adult_days'),
	'buyx' => $this->input->post('adult_buyx'),
	'getx' => $this->input->post('adult_getx'),
	'discount_cover' => $this->input->post('adult_discount_cover'),
	'daywise_discount' => $adult_daywise_discount,
	'discount' => $adult_discount,
	'tax_id' => $adult_tax_id,
	'tax_type' => $adult_tax_type,
	'tax' => $adult_tax,
	'subtotal' => $adult_subprice + $adult_daywise_discount,
	'created' => date('Y-m-d H:i:s'),
	);

	$child_discount = $this->site->calculateDiscount($this->input->post('bbq_discount'), $this->input->post('child_subprice'));
	$child_disfinal = $this->input->post('child_subprice') - $child_discount;
	$child_tax_id = $this->input->post('ptax');
	$child_tax_type = $this->input->post('tax_type');
	$child_tax = $this->site->calculateOrderTax( $this->input->post('ptax'), $child_disfinal);
	$child_subprice =  $this->input->post('child_subprice') ? $this->input->post('child_subprice') :0;

	$bil_items[$i][] = array(
	'type' => 'child',
	'cover' => $this->input->post('number_of_child'),
	'price' => $this->input->post('child_price'),
	'days' => $this->input->post('child_days'),
	'buyx' => $this->input->post('child_buyx'),
	'getx' => $this->input->post('child_getx'),
	'discount_cover' => $this->input->post('child_discount_cover'),
	'daywise_discount' => $child_daywise_discount,
	'discount' => $child_discount,
	'tax_id' => $child_tax_id,
	'tax_type' => $child_tax_type,
	'tax' => $child_tax,
	'subtotal' => $child_subprice + $child_daywise_discount,
	'created' => date('Y-m-d H:i:s'),
	);

	$kids_discount = $this->site->calculateDiscount($this->input->post('bbq_discount'), $this->input->post('kids_subprice'));
	$kids_disfinal = $this->input->post('kids_subprice') - $kids_discount;
	$kids_tax_id = $this->input->post('ptax');
	$kids_tax_type = $this->input->post('tax_type');
	$kids_tax = $this->site->calculateOrderTax( $this->input->post('ptax'), $kids_disfinal);
	$kids_subprice =  $this->input->post('kids_subprice') ? $this->input->post('kids_subprice') :0;

	$bil_items[$i][] = array(
	'type' => 'kids',
	'cover' => $this->input->post('number_of_kids'),
	'price' => $this->input->post('kids_price'),
	'days' => $this->input->post('kids_days'),
	'buyx' => $this->input->post('kids_buyx'),
	'getx' => $this->input->post('kids_getx'),
	'discount_cover' => $this->input->post('kids_discount_cover'),
	'daywise_discount' => $kids_daywise_discount,
	'discount' => $kids_discount,
	'tax_id' => $kids_tax_id,
	'tax_type' => $kids_tax_type,
	'tax' => $kids_tax,
	'subtotal' => $kids_subprice + $kids_daywise_discount,
	'created' => date('Y-m-d H:i:s'),
	);


	}

	$splits = $this->input->post('split_id');



	$bbq_response = $this->orders_api->BBQaddSale($notification_array, $timelog_array, $order_data_bbq, $splitData_bbq, $saleorder_item, $sale, $sale_items, $bilsdata_bbq, $bil_items, $bbq_order_id, $bbq_array, $splits);

	}

	$customer_type_val = $dine_order == 1 ? $this->input->post('customer_type_val') : '';
	$customer_discount_val =  $dine_order == 1 ?  $this->input->post('customer_discount_val') : '';
	$bbq_type_val = $bbq_order == 1 ? $this->input->post('bbq_type_val') : '';
	$bbq_discount_val = $bbq_order == 1 ? $this->input->post('bbq_discount_val') : '';
	$customer_id = $this->site->getOrderCustomerDATA($split_id);
	if(!empty($customer_discount_val) || !empty($bbq_discount_val)){
	$request_discount = array(
	'customer_id' => $customer_id,
	'waiter_id' => $user_id,
	'table_id' => $table_id,
	'split_id' => $split_id,
	'customer_type_val' => $customer_type_val,
	'customer_discount_val' => $customer_discount_val,
	'bbq_type_val' =>  $bbq_type_val,
	'bbq_discount_val' => $bbq_discount_val,
	'created_on' => date('Y-m-d H:i:s')
	);
	$this->orders_api->customerRequest($request_discount, $split_id);	
	}


	if($dine_response == TRUE && $bbq_response == TRUE){
	if($this->site->isSocketEnabled() && $table_id){
	$this->site->socket_refresh_bbqtables($table_id);	
	}
	$result = array( 'status'=> true , 'message'=> lang('biller_generator_in_data'),'message_khmer'=> html_entity_decode(lang('biller_generator_in_data_khmer')));
	}else{
	$result = array( 'status'=> false , 'message'=> lang('bill_generator_not_added'),'message_khmer'=> html_entity_decode(lang('bill_generator_not_added_khmer')));
	}

}elseif($bbq_order == 0 && $dine_order == 1){/*only dine in*/

	if(!empty($dine_order_type)){
	$this->data['customer_discount'] = $this->site->GetAllcostomerDiscounts();
	$notification_array['customer_role'] = CUSTOMER;
	$notification_array['customer_msg'] = 'Waiter has been bil generator to customer';
	$notification_array['customer_type'] = 'Your bil  generator';

	$notification_array['from_role'] = $group_id;
	$notification_array['insert_array'] = array(
	'msg' => 'Waiter has been bil generator to '.$split_id,
	'type' => 'Bil generator ('.$split_id.')',
	'table_id' =>  $table_id,
	'role_id' => CASHIER,
	'user_id' => $user_id,	
	'warehouse_id' => $warehouse_id,
	'created_on' => date('Y-m-d H:m:s'),
	'is_read' => 0
	);
	$this->data['current_user'] = $this->orders_api->getUserByID($user_id);

	$item_data_dine = $this->orders_api->bildinegetBil($table_id, $split_id, $user_id);

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

	$this->data['order_item'] = $order_item_dine;
	foreach($item_data_dine['order'] as $order){
	$order_data_dine = array('sales_type_id' => $order->order_type ? $order->order_type : 1,
	'sales_split_id' => $order->split_id,
	'sales_table_id' => $order->table_id,
	'created_on' => date('Y-m-d H:i:s'),				
	'date' => $this->site->getTransactionDate(),
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
	'consolidated' => 0
	);

	$notification_array['customer_id'] = $order->customer_id;
	}

	$this->data['order_data'] = $order_data_dine;
	$postData = $this->input->post();
	$delivery_person =  0;

	}

	if(!empty($dine_order_type)){

	for($i=1; $i<=$bils; $i++){

	$tot_item =	$this->input->post('[split]['.$i.'][total_item]');
	$itemdis = $this->input->post('[split]['.$i.'][discount_amount]')/$tot_item;
	$billitem['bills_items'] = array();
	$bills_item['recipe_name'] = $this->input->post('split[][$i][recipe_name][$i]');				
	$splitData_dine = array();

	foreach ($postData['split'][$i]['recipe_name'] as $key => $split) {
	$discount = $this->site->discountMultiple($postData['split'][$i]['recipe_id'][$key]);
	$offer_dis = 0.0000;
	if($this->input->post('[split]['.$i.'][tot_dis_value]'))
	{
	$offer_dis = $this->site->calculate_Discount($this->input->post('[split]['.$i.'][tot_dis_value]'), ($postData['split'][$i]['subtotal'][$key]-$postData['split'][$i]['item_discount'][$key]),$this->input->post('[split]['.$i.'][item_dis]'));
	}

	if($this->input->post('[split]['.$i.'][order_discount_input]'))
	{	

	$subtotal =$postData['split'][$i]['subtotal'][$key];
	$tot_dis1 = $this->input->post('[split]['.$i.'][tot_dis1]');
	$item_dis = $postData['split'][$i]['item_dis'][$key];
	$item_discount = $postData['split'][$i]['item_discount'][$key];
	if($this->input->post('customer_type_val')=="automanual"){
	$recipe_id =  $postData['split'][$i]['recipe_id'][$key];
	$finalAmt = $subtotal - $item_discount -$offer_dis; 
	$customer_discount_status = 'applied';
	$discountid = $this->input->post('[split]['.$i.'][order_discount_input]');
	$recipeDetails = $this->orders_api->getrecipeByID($recipe_id);
	$group_id =$recipeDetails->category_id;
	$input_dis = $this->orders_api->recipe_customer_discount_calculation($this->input->post('customer_type_val'), $recipe_id,$group_id,$finalAmt,$discountid);

	}else if($this->input->post('customer_type_val')=="manual"){
	$input_dis = $this->site->calculate_Discount($this->input->post('[split]['.$i.'][order_discount_input]'), (($postData['split'][$i]['subtotal'][$key]-$postData['split'][$i]['item_discount'][$key])-$offer_dis),$tot_dis1 ? $tot_dis1 : $item_dis );
	}else if($this->input->post('customer_type_val')=="none"){
	$input_dis = $this->site->calculate_Discount($this->input->post('[split]['.$i.'][order_discount_input]'), (($postData['split'][$i]['subtotal'][$key]-$postData['split'][$i]['item_discount'][$key])-$offer_dis),$tot_dis1 ? $tot_dis1 : $item_dis );
	}
	}
	else{
	$input_dis = 0;
	}
// var_dump($postData['split'][$i]['item_discount'][$key]);die;
	/*item service charge */
	$item_service_charge = 0;
	if(!empty($postData['split'][$i]['service_charge_id'][$key])){
		
	$item_service_charge = $this->site->calculateServiceCharge($postData['split'][$i]['service_charge_id'][$key], ($postData['split'][$i]['subtotal'][$key]-($offer_dis ? $offer_dis:0) -($input_dis ? $input_dis:0)-($postData['split'][$i]['item_discount'][$key])));	
	// var_dump($item_service_charge);
	}/*else{
echo "error";
	}*/
	/*item service charge */
 // die;


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

	$splitData_dine[$i][] = array(
	'recipe_name' => $split,
	'unit_price' => $postData['split'][$i]['unit_price'][$key],
	'net_unit_price' => $postData['split'][$i]['unit_price'][$key]*$postData['split'][$i]['quantity'][$key],
	'recipe_variant' => $postData['split'][$i]['varaint'][$key] ? $postData['split'][$i]['varaint'][$key] :0,
	'recipe_variant_id' => $postData['split'][$i]['variant_id'][$key] ? $postData['split'][$i]['variant_id'][$key] : 0,
	'warehouse_id' => $warehouse_id,
	'recipe_type' => $postData['split'][$i]['recipe_type'][$key],
	'quantity' => $postData['split'][$i]['quantity'][$key],
	'recipe_id' => $postData['split'][$i]['recipe_id'][$key],
	'recipe_code' => $postData['split'][$i]['recipe_code'][$key],
	'discount' => $discount[0] ? $discount[0] : 0,
	'item_discount' => $postData['split'][$i]['item_discount'][$key],
	'off_discount' => $offer_dis ? $offer_dis:0,
	'input_discount' => $input_dis ? $input_dis:0,
	'tax_type' => $this->input->post('[split]['.$i.'][tax_type]'), 
	'addon_id' => $postData['split'][$i]['addon'][$key] ? $postData['split'][$i]['addon'][$key] : 0,
	'service_charge_id' =>$postData['split'][$i]['service_charge_id'][$key] ? $postData['split'][$i]['service_charge_id'][$key] : 0,
	'service_charge_amount' => $item_service_charge,
	'tax' => $itemtax,	
	'subtotal' => $sub_val,
	);
	}
	if($this->input->post('[split]['.$i.'][order_discount_input]')){
	$cus_discount_type = $this->input->post('customer_type_val');
	$cus_discount_val ='';
	if($this->input->post('customer_type_val')=="automanual"){
	$cus_discount_val = $this->input->post('[split]['.$i.'][order_discount_input]').'%';
	}else if($this->input->post('customer_type_val')=="manual"){
	$cus_discount_val = $this->input->post('[split]['.$i.'][order_discount_input]');
	}
	}else{
	$cus_discount_val ='';$cus_discount_type='';
	}

	$billData_dine[$i] = array(
	'reference_no' => $this->input->post('[split]['.$i.'][reference_no]'),
	'date' => $this->site->getTransactionDate(),
	'created_on' => date('Y-m-d H:i:s'),
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
	'round_total' => $this->input->post('[split]['.$i.'][round_total]') != NULL ? $this->input->post('[split]['.$i.'][round_total]') : 0,
	'bill_type' => $bill_type != NULL ? $bill_type : 4,
	'delivery_person_id' => $delivery_person,
	'order_discount_id' => $this->input->post('[split]['.$i.'][tot_dis_id]')? $this->input->post('[split]['.$i.'][tot_dis_id]') : NULL,
	'warehouse_id' => $warehouse_id,
	'created_by' => $this->input->post('user_id'), 
	'discount_type'=>$cus_discount_type,
	'customer_discount_id'=>$discountid ? $discountid : 0,
	'discount_val'=>$cus_discount_val,
	'service_charge_id' =>$this->input->post('service_charge_id') ? $this->input->post('service_charge_id') :0,
	'service_charge_amount' => $this->input->post('[split]['.$i.'][service_charge_amount]')? $this->input->post('[split]['.$i.'][service_charge_amount]') : 0,
	'consolidated' => 0
	);

	}
	$sales_total = array_column($billData_dine, 'grand_total');
	$sales_total = array_sum($sales_total);
	$dine_response = $this->orders_api->InsertBill($order_data_dine, $order_item_dine, $billData_dine,$splitData_dine, $sales_total, $delivery_person,$timelog_array, $notification_array,$order_item_id);
	}
	$customer_type_val = $dine_order == 1 ? $this->input->post('customer_type_val') : '';
	$customer_discount_val =  $dine_order == 1 ?  $this->input->post('customer_discount_val') : '';
	$bbq_type_val = $bbq_order == 1 ? $this->input->post('bbq_type_val') : '';
	$bbq_discount_val = $bbq_order == 1 ? $this->input->post('bbq_discount_val') : '';
	$customer_id = $this->site->getOrderCustomerDATA($split_id);
	if(!empty($customer_discount_val) || !empty($bbq_discount_val)){
	$request_discount = array(
	'customer_id' => $customer_id,
	'waiter_id' => $user_id,
	'table_id' => $table_id,
	'split_id' => $split_id,
	'customer_type_val' => $customer_type_val,
	'customer_discount_val' => $customer_discount_val,
	'bbq_type_val' =>  $bbq_type_val,
	'bbq_discount_val' => $bbq_discount_val,
	'created_on' => date('Y-m-d H:i:s')
	);
	$this->orders_api->customerRequest($request_discount, $split_id);	
	}	
	if($dine_response == TRUE){
	if($this->site->isSocketEnabled() && $table_id){
	$this->site->socket_refresh_tables($table_id);	
	}
	$result = array( 'status'=> true , 'message'=> lang('biller_generator_in_data'),'message_khmer'=> html_entity_decode(lang('biller_generator_in_data_khmer')));
	}else{
	$result = array( 'status'=> false , 'message'=> lang('bill_generator_not_added'),'message_khmer'=> html_entity_decode(lang('bill_generator_not_added_khmer')));
	}
}elseif($bbq_order == 1 && $dine_order == 0){/*only bbq*/
	if(!empty($bbq_order_type)){
	$this->data['bbq_discount'] = $this->site->GetAllBBQDiscounts();
	$bbq_order_id = $this->orders_api->getBBQorderID($split_id);
	}							
	if(!empty($bbq_order_type)){
	for($i=0; $i<$bils; $i++){
	if($_POST['number_of_covers'][$i] != 0){
	$bbq_discount_amount[] = $_POST['bbq_discount_amount'][$i];
	$tax_amount[] = $_POST['tax_amount'][$i];
	$total_amount[] = $_POST['total_amount'][$i];
	$gtotal[] = $_POST['gtotal'][$i];

	$adult_price[] = $_POST['adult_price'][$i];
	$number_of_adult[] = $_POST['number_of_adult'][$i];
	$adult_subprice[] = $_POST['adult_subprice'][$i];

	$child_price[] = $_POST['child_price'][$i];
	$number_of_child[] = $_POST['number_of_child'][$i];
	$child_subprice[] = $_POST['child_subprice'][$i];

	$kids_price[] = $_POST['kids_price'][$i];
	$number_of_kids[] = $_POST['number_of_kids'][$i];
	$kids_subprice[] = $_POST['kids_subprice'][$i];

	$number_of_covers[] = $_POST['number_of_covers'][$i];

	$adult_discount_cover[] = $_POST['adult_discount_cover'][$i];
	$child_discount_cover[] = $_POST['child_discount_cover'][$i];
	$kids_discount_cover[] = $_POST['kids_discount_cover'][$i];
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

	$number_of_covers = array_sum($number_of_covers);

	$adult_discount_cover = array_sum($adult_discount_cover);
	$child_discount_cover = array_sum($child_discount_cover);
	$kids_discount_cover = array_sum($kids_discount_cover);
	$bbq_array = array(
	'number_of_adult' => $number_of_adult,
	'number_of_child' => $number_of_child,
	'number_of_kids' => $number_of_kids
	);

	$item_data_bbq = $this->orders_api->BBQgetBil($table_id, $split_id, $user_id);
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
	if($this->input->post('bbq_type_val') == 'automanual')
	{
	/*$bbq_discount_value = $this->site->GetIDByBBQDiscounts($this->input->post('bbq_discount'));
	$discount_value = $bbq_discount_value ? $bbq_discount_value : 0;
	$other_discount = $discount_value;*/
	$other_discount = $this->input->post('bbq_discount');
	}else{
	$other_discount = $this->input->post('bbq_discount');
	}
	$final_bil =  $final_bil - $TotalDiscount[1];
	$step_bil_2 = $step_bil_1 - $TotalDiscount[1];

	$other_discount_total = $this->site->calculateDiscount($other_discount, $step_bil_2);
	$total_discount =  $other_discount_total + array_sum($total_dis) + $offer_discount;
	$final_bil = $final_bil - $other_discount_total;
	$step_bil_3 = $step_bil_2 - $other_discount_total;

	$total_tax = $this->site->calculateOrderTax( $this->input->post('default_tax'), $final_bil);
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
	'consolidated' => 0
	);
	}

	$sale = array(
	'bilgenerator_type' => 0,
	'sales_type_id' => 4,
	'sales_split_id' => $this->input->post('split_id'),
	'sales_table_id' => $table_id,
	'created_on' => date('Y-m-d H:i:s'),
	'date' => $this->site->getTransactionDate(),
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
	'total_items' => $this->input->post('number_of_covers'),
	'consolidated' => 0,
	'sale_status' => 'Process'
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
	'consolidated' => 0
	);
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

	$itemtax = $this->site->calculateOrderTax($this->input->post('default_tax'), ($item->subtotal - $off_discount - $input_discount - $item_discount));


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
	'user_id' => $user_id,	
	'warehouse_id' => $warehouse_id,
	'created_on' => date('Y-m-d H:m:s'),
	'is_read' => 0
	);

	$order_bbq = $this->orders_api->BBQtablesplit($table_id, $split_id);
	$current_days = date('l');
	if($order_bbq->bbq_menu_id !=1){
		$lobsterdiscount = $this->site->getBBQlobsterDAYS($current_days);
	}else{
		$buyxgetx = $this->site->getBBQbuyxgetxDAYS($current_days);	
	}				
	 $adult_cover_discount = 0; 
	 $child_cover_discount = 0; 
	 $kids_cover_discount = 0;          		
	if($order_bbq->bbq_menu_id !=1){
	   $adult_cover_discount = $this->site->CalculationBBQlobster($order_bbq->number_of_adult,$order_bbq->adult_price,$lobsterdiscount->discount_apply_type,$lobsterdiscount->discount_type,$lobsterdiscount->adult_discount_val);
	   $child_cover_discount = $this->site->CalculationBBQlobster($order_bbq->number_of_child,$order_bbq->child_price,$lobsterdiscount->discount_apply_type,$lobsterdiscount->discount_type,$lobsterdiscount->child_discount_val);
	   $kids_cover_discount = $this->site->CalculationBBQlobster($order_bbq->number_of_kids,$order_bbq->kids_price,$lobsterdiscount->discount_apply_type,$lobsterdiscount->discount_type,$lobsterdiscount->kids_discount_val);
	}else{
		$adult = $this->site->CalculationBBQbuyget($buyxgetx->adult_buy, $buyxgetx->adult_get, $order_bbq->number_of_adult);
		$adult_cover_discount = $adult * $order_bbq->adult_price;
		$child = $this->site->CalculationBBQbuyget($buyxgetx->child_buy, $buyxgetx->child_get, $order_bbq->number_of_child);
		$child_cover_discount = $child * $order_bbq->child_price;
		$kids = $this->site->CalculationBBQbuyget($buyxgetx->kids_buy, $buyxgetx->kids_get, $order_bbq->number_of_kids);
		$kids_cover_discount = $kids * $order_bbq->kids_price;
	}

	$adult_daywise_discount = $this->input->post('adult_daywise_discount') ? $this->input->post('adult_daywise_discount') :0;
	$child_daywise_discount = $this->input->post('child_daywise_discount') ? $this->input->post('child_daywise_discount') :0;
	$kids_daywise_discount = $this->input->post('kids_daywise_discount') ? $this->input->post('kids_daywise_discount') :0;
	$total_amount = $this->input->post('total_amount') ? $this->input->post('total_amount') :0;
	$total_cover_discount = $adult_cover_discount+$child_cover_discount+$kids_cover_discount;

	for($i=0; $i<$bils; $i++){
	$bilsdata_bbq[$i] = array(
	'bilgenerator_type' => 0,
	'date' => $this->site->getTransactionDate(),
	'created_on' => date('Y-m-d H:i:s'),
	'reference_no' => 'SALE'.date('YmdHis'),
	'customer_id' => $this->input->post('customer_id'),
	'customer' => $this->input->post('customer'),
	'biller_id' => $this->input->post('biller_id'),
	'biller' => $this->input->post('biller'),
	'warehouse_id' => $this->input->post('warehouse_id'), 
	'created_by' => $this->input->post('user_id'), 
	'total' => $total_amount+$adult_daywise_discount + $child_daywise_discount + $kids_daywise_discount+$total_cover_discount, 
	'order_discount_id' => $this->input->post('bbq_discount'), 
	'customer_discount_id' => $this->input->post('bbq_discount_val') ? $this->input->post('bbq_discount_val') : 0,
	'total_discount' => $this->input->post('bbq_discount_amount'),
	'bbq_daywise_discount' => $adult_daywise_discount + $child_daywise_discount + $kids_daywise_discount,
	'bbq_cover_discount' =>  $total_cover_discount,
	'tax_id' => $this->input->post('ptax'),
	'total_tax' => $this->input->post('tax_amount'), 
	'tax_type' => $this->input->post('tax_type'),
	'grand_total' => $this->input->post('gtotal'),
	'total_items' => $this->input->post('number_of_covers'),
	'consolidated' => 0,
	'order_type' => 4
	);

	$adult_discount = $this->site->calculateDiscount($this->input->post('bbq_discount'), $this->input->post('adult_subprice'));
	$adult_disfinal = $this->input->post('adult_subprice') - $adult_discount;
	$adult_tax_id = $this->input->post('ptax');
	$adult_tax_type = $this->input->post('tax_type');
	$adult_tax = $this->site->calculateOrderTax( $this->input->post('ptax'), $adult_disfinal);
	$adult_subprice = $this->input->post('adult_subprice') ? $this->input->post('adult_subprice'):0;

	$bil_items[$i][] = array(
	'type' => 'adult',
	'cover' => $this->input->post('number_of_adult'),
	'price' => $this->input->post('adult_price'),
	'days' => $this->input->post('adult_days'),
	'buyx' => $this->input->post('adult_buyx'),
	'getx' => $this->input->post('adult_getx'),
	'discount_cover' => $this->input->post('adult_discount_cover'),
	'daywise_discount' => $adult_daywise_discount,
	'discount' => $adult_discount,
	'tax_id' => $adult_tax_id,
	'tax_type' => $adult_tax_type,
	'tax' => $adult_tax,
	'subtotal' => $adult_subprice + $adult_daywise_discount,
	'created' => date('Y-m-d H:i:s'),
	);

	$child_discount = $this->site->calculateDiscount($this->input->post('bbq_discount'), $this->input->post('child_subprice'));
	$child_disfinal = $this->input->post('child_subprice') - $child_discount;
	$child_tax_id = $this->input->post('ptax');
	$child_tax_type = $this->input->post('tax_type');
	$child_tax = $this->site->calculateOrderTax( $this->input->post('ptax'), $child_disfinal);
	$child_subprice = $this->input->post('child_subprice') ? $this->input->post('child_subprice'):0;

	$bil_items[$i][] = array(
	'type' => 'child',
	'cover' => $this->input->post('number_of_child'),
	'price' => $this->input->post('child_price'),
	'days' => $this->input->post('child_days'),
	'buyx' => $this->input->post('child_buyx'),
	'getx' => $this->input->post('child_getx'),
	'discount_cover' => $this->input->post('child_discount_cover'),
	'daywise_discount' => $child_daywise_discount,
	'discount' => $child_discount,
	'tax_id' => $child_tax_id,
	'tax_type' => $child_tax_type,
	'tax' => $child_tax,
	'subtotal' => $child_subprice + $child_daywise_discount,
	'created' => date('Y-m-d H:i:s'),
	);

	$kids_discount = $this->site->calculateDiscount($this->input->post('bbq_discount'), $this->input->post('kids_subprice'));
	$kids_disfinal = $this->input->post('kids_subprice') - $kids_discount;
	$kids_tax_id = $this->input->post('ptax');
	$kids_tax_type = $this->input->post('tax_type');
	$kids_tax = $this->site->calculateOrderTax( $this->input->post('ptax'), $kids_disfinal);
	$kids_subprice = $this->input->post('kids_subprice') ? $this->input->post('kids_subprice'):0;
	$bil_items[$i][] = array(
	'type' => 'kids',
	'cover' => $this->input->post('number_of_kids'),
	'price' => $this->input->post('kids_price'),
	'days' => $this->input->post('kids_days'),
	'buyx' => $this->input->post('kids_buyx'),
	'getx' => $this->input->post('kids_getx'),
	'discount_cover' => $this->input->post('kids_discount_cover'),
	'daywise_discount' => $kids_daywise_discount,
	'discount' => $kids_discount,
	'tax_id' => $kids_tax_id,
	'tax_type' => $kids_tax_type,
	'tax' => $kids_tax,
	'subtotal' => $kids_subprice+$kids_daywise_discount,
	'created' => date('Y-m-d H:i:s'),
	);
	}

	$splits = $this->input->post('split_id');
	$bbq_response = $this->orders_api->BBQaddSale($notification_array, $timelog_array, $order_data_bbq, $splitData_bbq, $saleorder_item, $sale, $sale_items, $bilsdata_bbq, $bil_items, $bbq_order_id, $bbq_array, $splits);

	}

	$customer_type_val = $dine_order == 1 ? $this->input->post('customer_type_val') : '';
	$customer_discount_val =  $dine_order == 1 ?  $this->input->post('customer_discount_val') : '';
	$bbq_type_val = $bbq_order == 1 ? $this->input->post('bbq_type_val') : '';
	$bbq_discount_val = $bbq_order == 1 ? $this->input->post('bbq_discount_val') : '';
	$customer_id = $this->site->getOrderCustomerDATA($split_id);
	if(!empty($customer_discount_val) || !empty($bbq_discount_val)){
	$request_discount = array(
	'customer_id' => $customer_id,
	'waiter_id' => $user_id,
	'table_id' => $table_id,
	'split_id' => $split_id,
	'customer_type_val' => $customer_type_val,
	'customer_discount_val' => $customer_discount_val,
	'bbq_type_val' =>  $bbq_type_val,
	'bbq_discount_val' => $bbq_discount_val,
	'created_on' => date('Y-m-d H:i:s')
	);
	$this->orders_api->customerRequest($request_discount, $split_id);	
	}

	if($bbq_response == TRUE){
	if($this->site->isSocketEnabled() && $table_id){
	$this->site->socket_refresh_bbqtables($table_id);	
	}
	$result = array( 'status'=> true , 'message'=> lang('biller_generator_in_data'),'message_khmer'=> html_entity_decode(lang('biller_generator_in_data_khmer')));
	}else{
	$result = array( 'status'=> false , 'message'=> lang('bill_generator_not_added'),'message_khmer'=> html_entity_decode(lang('bill_generator_not_added_khmer')));
	}

	}else{
	$result = array( 'status'=> false , 'message'=> lang('bill_generator_not_added'),'message_khmer'=> html_entity_decode(lang('bill_generator_not_added_khmer')));	
	}
	}else{
	$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
	}
	}else{

	$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));
	}
	}else{
	$result = array( 'status'=> false , 'message'=> lang('already_bil_generator_please_check_cashier_or_waiter'),'message_khmer'=> html_entity_decode(lang('already_bil_generator_please_check_cashier_or_waiter_khmer')));	
	}

	$this->response($result);
}
	
	public function consolidated_post(){
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		$settings = $this->site->get_setting();
		$GP = $this->site->getGroupPermissionsarray($group_id);
		$split = $this->post('split');
		$order_type[] = 1;
		$order_type[] = 4;
		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		$this->form_validation->set_rules('user_id', $this->lang->line("user_id"), 'required');
		$this->form_validation->set_rules('warehouse_id', $this->lang->line("warehouse"), 'required');
		$this->form_validation->set_rules('split', $this->lang->line("split"), 'required');

		
		
		if ($this->form_validation->run() == true) {
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){	
				$data = $this->orders_api->GetAllSplitconsolidated($split, $order_type, $user_id, $warehouse_id);				
				$billgenerate = true;
				if($GP[0]['pos-app_bil_generator'] == 0)
        		{
        			$billgenerate = false;
        	    }		
				foreach($data as $row){
					if($row->order_type == 4){
						$grand_total_cover = $row->grand_total_cover;
					}else{
						$grand_total[] = $row->grand_total;
					}
					if($row->ordered_by=='customer' && $row->customer_request==0 && $settings->order_request_stewardapp==1){
						$billgenerate = false;
					}
				}
				$grand_total[] = $grand_total_cover;
				$grand_total = array_sum($grand_total);
				
				$bbq_menu_id = 0;
				$q = $this->db->select('*')->where('reference_no', $split)->get('bbq', 1);
					if($q->num_rows() > 0){
						$bbq_menu_id = $q->row('bbq_menu_id');
					}

				if(!empty($data)){
					$result = array( 'status'=> true , 'message'=> lang('split_order_dine_in_data'),'message_khmer'=> html_entity_decode(lang('split_order_dine_in_data_khmer')), 'item_comment_price_option' => $posallsettings->item_comment_price_option,'grand_total' => $grand_total,'bill_generate' => $billgenerate,'bbq_menu_id' => $bbq_menu_id, 'data' => $data);
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
	
	public function consolidatedsplit_post(){
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
				
				$data = $this->orders_api->GetAllconsolidated($table_id, $user_id, $warehouse_id);
				
				if(!empty($data)){
					$result = array( 'status'=> true , 'message'=> lang('order_consolidated_data'),'message_khmer'=> html_entity_decode(lang('order_consolidated_data_khmer')), 'name' => $data[0]->name,  'data' => $data[0]->split_order);
				}else{
					$result = array( 'status'=> false , 'message'=> lang('order_consolidated_empty'),'message_khmer'=> html_entity_decode(lang('order_consolidated_empty_khmer')));
				}
			}else{
				
				$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
			}
		}else{
			$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));	
		}
		$this->response($result);
	}
	
	public function dinein_post(){		
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
				$data = $this->orders_api->GetAlldinein($table_id, $user_id, $warehouse_id);
				
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
	
	public function dineinsplit_post(){
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		
		$split = $this->post('split');
		$order_type = $this->post('order_type');
		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		$this->form_validation->set_rules('user_id', $this->lang->line("user_id"), 'required');
		$this->form_validation->set_rules('warehouse_id', $this->lang->line("warehouse"), 'required');
		$this->form_validation->set_rules('split', $this->lang->line("split"), 'required');
		$this->form_validation->set_rules('order_type', $this->lang->line("order_type"), 'required');
		if ($this->form_validation->run() == true) {
				$possettings = $this->orders_api->getServiceChargeSettings();		
				$service_charge_option = $possettings->service_charge_option ? $possettings->service_charge_option :0;
				$ServiceCharge[] ='';
				if($possettings->service_charge_option != 0 && $possettings->default_service_charge !=0)
				{  
				    $ServiceCharge = $this->site->getServiceChargeByID($possettings->default_service_charge);
					if(!empty($ServiceCharge)){
						$ServiceChargearr[] = $ServiceCharge;
						$service_charge_option = $possettings->service_charge_option;
					}else{
						$ServiceChargearr[] = '';
						$service_charge_option = 0;
					}
				}

			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){	
				$data = $this->orders_api->GetAllSplit($split, $order_type, $user_id, $warehouse_id);
				if(!empty($data)){
					$result = array( 'status'=> true , 'message'=> lang('split_order_dine_in_data'),'message_khmer'=> html_entity_decode(lang('split_order_dine_in_data_khmer')), 'service_charge_option' => $service_charge_option,'service_charge_data' => $ServiceCharge,'data' => $data);
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
	
	public function takeaway_post(){
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
				$data = $this->orders_api->GetAlltakeaway($user_id, $warehouse_id);
				if(!empty($data)){
					$result = array( 'status'=> true , 'message'=> lang('order_take_away_data'),'message_khmer'=> html_entity_decode(lang('order_take_away_data_khmer')), 'data' => $data);
				}else{
					$result = array( 'status'=> false , 'message'=> lang('order_take_away_empty'),'message_khmer'=> html_entity_decode(lang('order_take_away_empty_khmer')));
				}
			}else{
				$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
			}
		}else{
			$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));	
		}
		$this->response($result);
	}
	
	
	public function takeawaysplit_post(){
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		
		$split = $this->post('split');
		$order_type = $this->post('order_type');
		
		$this->form_validation->set_rules('user_id', $this->lang->line("user_id"), 'required');
		$this->form_validation->set_rules('warehouse_id', $this->lang->line("warehouse"), 'required');
		$this->form_validation->set_rules('split', $this->lang->line("split"), 'required');
		$this->form_validation->set_rules('order_type', $this->lang->line("order_type"), 'required');
		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		if ($this->form_validation->run() == true) {
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){	
				$data = $this->orders_api->GetAllSplit($split, $order_type, $user_id, $warehouse_id);
				if(!empty($data)){
					$result = array( 'status'=> true , 'message'=> lang('split_order_takeaway_in_data'),'message_khmer'=> html_entity_decode(lang('split_order_takeaway_in_data_khmer')), 'data' => $data);
				}else{
					$result = array( 'status'=> false , 'message'=> lang('split_order_takeaway_in_empty'),'message_khmer'=> html_entity_decode(lang('split_order_takeaway_in_empty_khmer')));
				}
			}else{
				$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
			}
		}else{
			$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));	
		}
		$this->response($result);
	}
	
	public function doordelivery_post(){
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
				$data = $this->orders_api->GetAlldoordelivery($user_id, $warehouse_id);
				if(!empty($data)){
					$result = array( 'status'=> true , 'message'=> lang('order_door_delivery_data'),'message_khmer'=> html_entity_decode(lang('order_door_delivery_data_khmer')), 'data' => $data);
				}else{
					$result = array( 'status'=> false , 'message'=> lang('order_door_delivery_empty'),'message_khmer'=> html_entity_decode(lang('order_door_delivery_empty_khmer')));
				}
			}else{
				$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
			}
		}else{
			$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));	
		}
		$this->response($result);
	}
	
	public function doordeliverysplit_post(){
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		
		$split = $this->post('split');
		$order_type = $this->post('order_type');
		
		$this->form_validation->set_rules('user_id', $this->lang->line("user_id"), 'required');
		$this->form_validation->set_rules('warehouse_id', $this->lang->line("warehouse"), 'required');
		$this->form_validation->set_rules('split', $this->lang->line("split"), 'required');
		$this->form_validation->set_rules('order_type', $this->lang->line("order_type"), 'required');
		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		if ($this->form_validation->run() == true) {
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){	
				$data = $this->orders_api->GetAllSplit($split, $order_type, $user_id, $warehouse_id);
				if(!empty($data)){
					$result = array( 'status'=> true , 'message'=> lang('split_order_door_delivery_in_data'),'message_khmer'=> html_entity_decode(lang('split_order_door_delivery_in_data_khmer')), 'data' => $data);
				}else{
					$result = array( 'status'=> false , 'message'=> lang('split_order_door_delivery_in_empty'),'message_khmer'=> html_entity_decode(lang('split_order_door_delivery_in_empty_khmer')));
				}
			}else{
				$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
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
		$split_id = $this->post('split_id');
		$remarks = $this->post('remarks');
		
		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		$this->form_validation->set_rules('item_id', $this->lang->line("item_id"), 'required');
		$this->form_validation->set_rules('remarks', $this->lang->line("remarks"), 'required');
		$this->form_validation->set_rules('split_id', $this->lang->line("split_id"), 'required');
		$this->form_validation->set_rules('user_id', $this->lang->line("user_id"), 'required');
		if ($this->form_validation->run() == true) {
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){
				
				
				 $notification_msg = 'The item has been cancel to waiter';
				 $type = 'Waiter Cancel';
				 
				 $item_data = $this->site->getOrderItem($item_id);
		 		 $customer_id = $this->site->getOrderItemCustomer($item_id);
		 
				 $notification_array['customer_role'] = CUSTOMER;
				 $notification_array['customer_msg'] =  'The '.$item_data->recipe_name.' has been cancel to waiter';
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
				
				 $checkrequestbill =$this->orders_api->checkorderRequestforbill($split_id);
				 // var_dump($checkrequestbill);die;
				if($checkrequestbill !=1) {
					 $data =$this->orders_api->CancelOrdersItem($split_id, $item_id, $remarks, $user_id, $notification_array);
					 if(!empty($data)){
						 $result = array( 'status'=> true , 'message'=> lang('item_has_been_cancel_success'),'message_khmer'=> html_entity_decode(lang('item_has_been_cancel_success_khmer')));
					}else{
						$result = array( 'status'=> false , 'message'=> lang('item_does_not_cancel'),'message_khmer'=> html_entity_decode(lang('item_does_not_cancel_khmer')));
					}
				}else{
						$result = array( 'status'=> false , 'message'=> lang('this_order_already_reqsuested_for_bill'),'message_khmer'=> html_entity_decode(lang('this_order_already_reqsuested_for_bill_khmer')));
					}
				
			}else{
				$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
			}
		}else{
			$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));
		}
		$this->response($result);
	}
	
	public function itemstatusorder_post(){
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		
		$item_id = $this->post('item_id');
		//$split_id = $this->post('split_id');
		$status = $this->post('status');
		
		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		
		//$this->form_validation->set_rules('remarks', $this->lang->line("remarks"), 'required');
		$this->form_validation->set_rules('user_id', $this->lang->line("user_id"), 'required');
		if ($this->form_validation->run() == true) {
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){
				
				
				if($status == 'Ready'){
					$current_status = 'Served';
				 }elseif($status == 'Served'){
					$current_status = 'Closed';         
				 }else{
					$current_status = 'Ready';
				 }
				 
				
				 $customer_id = $this->site->getOrderItemCustomer($item_id[0]);
		 
				 $notification_array['customer_role'] = CUSTOMER;
				 $notification_array['customer_msg'] =  'The item has been '.$current_status.' to waiter';
				 $notification_array['customer_type'] = 'Waiter '.$current_status.' Status';
				 $notification_array['customer_id'] = $customer_id;
		
				$notification_array['from_role'] = $group_id;
				$notification_array['insert_array'] = array(
					'type' => 'Waiter '.$current_status.' Status',
					'table_id' =>  0,
					'user_id' => $user_id,	
					'role_id' => 6,
					'warehouse_id' => $warehouse_id,
					'created_on' => date('Y-m-d H:m:s'),
					'is_read' => 0
				);
				
				$data = $this->orders_api->updateOrderstatus($status, $item_id, $current_status, $user_id, $notification_array);
				
				//$data = $this->orders_api->CancelOrdersItem($split_id, $item_id, $remarks, $user_id, $notification_array);
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
	
	
	
}
