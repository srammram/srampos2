<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;



class Sendtokitchen extends REST_Controller {

	function __construct() {
		parent::__construct();
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		$this->load->api_model('sendtokitchen_api');
		$this->lang->admin_load('engliah_khmer','english');
		$params = array(
			'host' => PRINTER_HOST,
			'port' => PRINTER_PORT,
			'path' => ''
		);
		$this->load->library('ws',$params);
	}
	
	public function index_post()
	{
		$api_key = $this->input->post('api-key');
		$this->pos_settings = $this->sendtokitchen_api->getSetting();
		$devices_key = $this->input->post('devices_key');
		$table_id = $this->post('table_id');
		$user_id = ($this->input->post('user_id'))?$this->input->post('user_id'):$this->site->getSteward($table_id);
		$group_id = ($this->input->post('group_id'))?$this->input->post('group_id'):$this->site->getUserGroupID($user_id);
		$warehouse_id = $this->input->post('warehouse_id');
		
		
		$customer_id = $this->post('customer_id');
		$biller_id = $this->post('biller_id');
		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		//$this->form_validation->set_rules('user_id', $this->lang->line("user_id"), 'required');
		$this->form_validation->set_rules('customer_id', $this->lang->line("customer_id"), 'required');
		$this->form_validation->set_rules('biller_id', $this->lang->line("biller_id"), 'required');
		if($this->post('order_type') == 1 || $this->post('order_type') == 4)
		{
			$this->form_validation->set_rules('table_id', $this->lang->line("table_id"), 'required');
		}
		
		if ($this->form_validation->run() == true) {
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){	
				$date = date('Y-m-d H:i:s');
				$total_items = $this->post('total_items');
				
				$total = $this->post('total');
				
				$customer_details = $this->site->getCompanyByID($customer_id);
				$customer = $customer_details->name;
				$biller_details = $this->site->getCompanyByID($biller_id);
				$biller = $biller_details->name;
				$reference = 'ORDER'.date('YmdHis');
				$ordersplitid = $this->sendtokitchen_api->getSplitId($customer_id, $warehouse_id,$table_id);
				
				$split_id = $ordersplitid ? $ordersplitid : $this->site->CreateSplitID($user_id);
				
				$order_data = array('date'  => $this->site->getTransactionDate(),
					'created_on' => $date,
					'reference_no'      => $reference,
					'table_id'			=> !empty($this->post('table_id'))  ? $this->post('table_id')  : 0 ,
					'seats_id'			=> !empty($this->post('seats_id')) ? $this->post('seats_id') : 0,
					'split_id'			=> $split_id,
					'order_type' 		=> $this->post('order_type'),
					'order_status' 		=> 'Open',
					'customer_id'       => $customer_id,
					'customer'          => $customer,
					'biller_id'         => $biller_id,
					'biller'            => $biller,
					'warehouse_id'      => $warehouse_id,
					'total'             => $total,
					'grand_total'       => $this->sma->formatDecimal($total),
					'total_items'       => $total_items,
					'pos'               => 1,
					'created_by'        => $user_id,
					'order_from'        => 'app',
					'hash'              => hash('sha256', microtime() . mt_rand()),
					'waiter_id'         => $user_id,
					);
			   if(isset($_POST['user_id'])){
				$order_data['ordered_by']  = 'steward';
			   }else{
				$order_data['ordered_by']  = 'customer';
			   }
			   $item_count = isset($_POST['recipe_code']) ? sizeof($_POST['recipe_code']) : 0;
			   
			   /*echo "<pre>";
				print_r($this->input->post());*/
				for ($i = 0; $i < $item_count; $i++){

					$variantname = $this->sendtokitchen_api->getVariantData($_POST['variant_id'][$i],$_POST['recipe_id'][$i]);

					$item_data[] = array(				
						'item_status' 	 => 'Inprocess',
						'kitchen_type_id' => $_POST['kitchen_type'][$i],
						'recipe_id' => $_POST['recipe_id'][$i],
						'recipe_variant_id' => $_POST['variant_id'][$i] ? $_POST['variant_id'][$i] :0,						
						'variant' => $variantname->name ? $variantname->name:0,	
						'recipe_code' => $_POST['recipe_code'][$i],
						'recipe_name' => $_POST['recipe_name'][$i],
						'recipe_type' => $_POST['recipe_type'][$i],
						'warehouse_id' => $_POST['warehouse'][$i],
						//'addon_id' => $_POST['addon_id'][$i],
						'unit_price' => $this->sma->formatDecimal($_POST['unit_price'][$i]),
						'net_unit_price' => $_POST['unit_price'][$i],
						'quantity' => $_POST['quantity'][$i],
						'subtotal' => $this->sma->formatDecimal($_POST['subtotal'][$i]),
						'real_unit_price' => $_POST['subtotal'][$i],
						'comment' => $_POST['comment'][$i],
						'addon_id' => $_POST['addon'][$i] ? $_POST['addon'][$i] : 0,
						'time_started' => date('Y-m-d H:i:s'),
						'created_on' => date('Y-m-d H:i:s'),
					);
					
				}
				$kitchen = array(
					'waiter_id' => $user_id,
					'status' => 'Inprocess' 
				);
				
				if($group_id == 5){
					$role = ' (Sale) ';
				}elseif($group_id == 7){
					$role = ' (Waiter) ';
				}
				if($this->input->post('order_type') == 1){ 
					$notification_message = $role.'  has been create new dine in order. it will be process sent to kitchen'; 
				}elseif($this->input->post('order_type') == 2){ 
					$notification_message = $role.'  has been create new takeaway order. it will be process sent to kitchen'; 
				}elseif($this->input->post('order_type') == 3){ 
					$notification_message = $role.' has been create new door delivery order. it will be process sent to kitchen'; 
				}
				
				$notification_array['from_role'] = $group_id;
				$notification_array['insert_array'] = array(
					'msg' => $notification_message,
					'type' => 'Send to kitchen',
					'table_id' => !empty($this->input->post('table_id'))  ? $this->input->post('table_id')  : 0 ,
					'user_id' => $user_id,
					'to_user_id' => $user_id,
					'role_id' => 6,
					'warehouse_id' => $warehouse_id,
					'created_on' => date('Y-m-d H:m:s'),
					'is_read' => 0,
					'respective_steward'=>0,
					'split_id'=>$split_id,
					'tag'=>'send-to-kitchen',
					'status'=>1
				);
				/*echo "<pre>";
				print_r($item_data);die;*/
				//$this->sma->print_arrays($order_data, $item_data, $kitchen);
				//die;
				$data = $this->sendtokitchen_api->addKitchen($order_data, $item_data, $kitchen, $notification_array);
				if(!empty($data)){
					
				$current_date = date('Y-m-d');	
					$bbq_check = $this->db->select('*')->where('reference_no', $split_id)->where('created_on', $current_date)->where_in('status', array('waiting','Open'))->get('bbq', 1);				 
					if ($bbq_check->num_rows() > 0) {					
						$data['orders_details']->bbq_menu_id = $bbq_check->row('bbq_menu_id');
					}else{					
						$data['orders_details']->bbq_menu_id = 'empty';	
					}

				
				$kot_print_data['kot_print_option'] = $this->pos_settings->kot_print_option;
				$kot_print_data['con_kot_print_option'] = $this->pos_settings->consolidated_kot_print;
				$kot_print_data['kot_area_print'] = $data;
				$kot_print_data['kot_con_print'] =$data;
				if($this->pos_settings->kot_enable_disable == 1){	
					$this->send_to_kot_print($kot_print_data);
				}				
				//if($this->pos_settings->kot_print_option == 1){	
				//	$this->remotePrintingKOT_single($data);
				//}else{
				//	$this->remotePrintingKOT($data);
				//}

				
					
					//if($this->pos_settings->consolidated_kot_print != 0){
						//$this->kot_consolidated_curl($data);
							//if(!empty($data['consolid_kot_print_details'])){
							//
							//	foreach($data['consolid_kot_print_details'] as $order_data){	
							//
							//		if(!empty($data['consolid_kot_print_details']) && !empty($data['consolid_kitchens'])){		
							//			$this->remotePrintingCONSOLIDKOT($data);
							//		}
							//	}
							//}					
					//}
					$tableid = !empty($this->input->post('table_id'))  ? $this->input->post('table_id')  : 0 ;
					if($this->site->isSocketEnabled() && $tableid){
					$this->site->socket_refresh_tables($tableid);	
					}
					$result = array( 'status'=> true , 'message'=> lang('order_has_been_added_success'),'message_khmer'=> html_entity_decode(lang('order_has_been_added_success_khmer')), 'kitchen_detail' => $data);
				}else{
					$result = array( 'status'=> false ,  'message'=> lang('order_does_not_added'),'message_khmer'=> html_entity_decode(lang('order_does_not_added_khmer')));
				}
			}else{
				$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
			}
		}else{
			$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));	
		}
		
		$this->response($result);
		
	}
	
	public function bbq_post()
	{
		$api_key = $this->input->post('api-key');
		$this->pos_settings = $this->sendtokitchen_api->getSetting();
		$devices_key = $this->input->post('devices_key');
		$table_id = $this->post('table_id');
		$user_id = ($this->input->post('user_id'))?$this->input->post('user_id'):$this->site->getSteward($table_id);
		$group_id = ($this->input->post('group_id'))?$this->input->post('group_id'):$this->site->getUserGroupID($user_id);
		$warehouse_id = $this->input->post('warehouse_id');
		
		$customer_id = $this->post('customer_id');
		$biller_id = $this->post('biller_id');
		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		//$this->form_validation->set_rules('user_id', $this->lang->line("user_id"), 'required');
		$this->form_validation->set_rules('customer_id', $this->lang->line("customer_id"), 'required');
		$this->form_validation->set_rules('biller_id', $this->lang->line("biller_id"), 'required');
		$this->form_validation->set_rules('table_id', $this->lang->line("table_id"), 'required');
		if ($this->form_validation->run() == true) {
			$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){
				$bbqcode = $this->input->post('split_id');
				$BBQdata = $this->sendtokitchen_api->isBBQCoverConfirmed($bbqcode);
				if($BBQdata){
				$user_id = $BBQdata->confirmed_by;
				$date = date('Y-m-d H:i:s');
				$total_items = $this->post('total_items');
				
				$total = $this->post('total');
				
				$customer_details = $this->site->getCompanyByID($customer_id);
				$customer = $customer_details->name;
				$biller_details = $this->site->getCompanyByID($biller_id);
				$biller = $biller_details->name;
				$reference = 'ORDER'.date('YmdHis');
				$ordersplitid = $this->sendtokitchen_api->getSplitId($customer_id, $warehouse_id,$table_id);
				
				$split_id = ($ordersplitid) ? ($ordersplitid) : $this->input->post('split_id');
				
				$order_data = array('date'  => $this->site->getTransactionDate(),
					'created_on' => $date,
					'reference_no'      => $reference,
					'table_id'			=> !empty($this->post('table_id'))  ? $this->post('table_id')  : 0 ,
					'seats_id'			=> 0,
					'split_id'			=> $split_id,
					'order_type' 		=> $this->post('order_type'),
					'order_status' 		=> 'Open',
					'customer_id'       => $customer_id,
					'customer'          => $customer,
					'biller_id'         => $biller_id,
					'biller'            => $biller,
					'warehouse_id'      => $warehouse_id,
					'total'             => $total,
					'grand_total'       => $this->sma->formatDecimal($total),
					'total_items'       => $total_items,
					'pos'               => 1,
					'order_from'        => 'app',
					'created_by'        => $user_id,
					'hash'              => hash('sha256', microtime() . mt_rand()),
					);
			   if(isset($_POST['user_id'])){
				$order_data['ordered_by']  = 'steward';
			   }else{
				$order_data['ordered_by']  = 'customer';
			   }
			   $item_count = isset($_POST['recipe_code']) ? sizeof($_POST['recipe_code']) : 0;
			   
				
				for ($i = 0; $i < $item_count; $i++){
					
					$item_data[] = array(				
						'item_status' 	 => 'Inprocess',
						'kitchen_type_id' => $_POST['kitchen_type'][$i],
						'recipe_id' => $_POST['recipe_id'][$i],
						'recipe_code' => $_POST['recipe_code'][$i],
						'recipe_name' => $_POST['recipe_name'][$i],
						'recipe_type' => $_POST['recipe_type'][$i],
						'warehouse_id' => $_POST['warehouse'][$i],
						'addon_id' => $_POST['addon_id'][$i] ? $_POST['addon_id'][$i] :0,
						'unit_price' => $this->sma->formatDecimal($_POST['unit_price'][$i]),
						'net_unit_price' => $_POST['unit_price'][$i],
						'quantity' => $_POST['quantity'][$i],
						'subtotal' => $this->sma->formatDecimal($_POST['subtotal'][$i]),
						'real_unit_price' => $_POST['subtotal'][$i],
						'comment' => $_POST['comment'][$i],
						'time_started' => date('Y-m-d H:i:s'),
						'created_on' => date('Y-m-d H:i:s'),
					);
					
				}
				$kitchen = array(
					'waiter_id' => $user_id,
					'status' => 'Inprocess' 
				);
				
				if($group_id == 5){
					$role = ' (Sale) ';
				}elseif($group_id == 7){
					$role = ' (Waiter) ';
				}
				if($this->input->post('order_type') == 1){ 
					$notification_message = $role.'  has been create new dine in order. it will be process sent to kitchen'; 
				}elseif($this->input->post('order_type') == 2){ 
					$notification_message = $role.'  has been create new takeaway order. it will be process sent to kitchen'; 
				}elseif($this->input->post('order_type') == 3){ 
					$notification_message = $role.' has been create new door delivery order. it will be process sent to kitchen'; 
				}elseif($this->input->post('order_type') == 4){ 
					$notification_message = $role.' has been create new BBQ order. it will be process sent to kitchen'; 
				}
				
				$notification_array['from_role'] = $group_id;
				$notification_array['insert_array'] = array(
					'msg' => $notification_message,
					'type' => 'Send to kitchen',
					'table_id' => !empty($this->input->post('table_id'))  ? $this->input->post('table_id')  : 0 ,
					'user_id' => $user_id,	
					'role_id' => 6,
					'warehouse_id' => $warehouse_id,
					'created_on' => date('Y-m-d H:m:s'),
					'is_read' => 0,
					'respective_steward'=>0,
					'split_id'=>$split_id,
					'tag'=>'send-to-kitchen',
					'status'=>1
				);
				
				//$this->sma->print_arrays($order_data, $item_data, $kitchen);
				//die;
				$data = $this->sendtokitchen_api->BBQaddKitchen($order_data, $item_data, $kitchen, $notification_array);
				if(!empty($data)){
					
					$current_date = date('Y-m-d');	
					$bbq_check = $this->db->select('*')->where('reference_no', $split_id)->where('created_on', $current_date)->where_in('status', array('waiting','Open'))->get('bbq', 1);				 
					if ($bbq_check->num_rows() > 0) {					
						$data['orders_details']->bbq_menu_id = $bbq_check->row('bbq_menu_id');
					}else{					
						$data['orders_details']->bbq_menu_id = 'empty';	
					}

					$kot_print_data['kot_print_option'] = $this->pos_settings->kot_print_option;
					$kot_print_data['con_kot_print_option'] = $this->pos_settings->consolidated_kot_print;
					$kot_print_data['kot_area_print'] = $data;
					$kot_print_data['kot_con_print'] =$data;
				if($this->pos_settings->kot_enable_disable == 1){	
					$this->send_to_kot_print($kot_print_data);
				}

					// $this->send_to_kot_print($kot_print_data);
				//if($this->pos_settings->kot_print_option == 1){	
				//	$this->remotePrintingKOT_single($data);					
				//}else{
				//	$this->remotePrintingKOT($data);					
				//}				
					
					//if($this->pos_settings->consolidated_kot_print != 0){	

						//$this->kot_consolidated_curl($data);
							//if(!empty($data['consolid_kot_print_details'])){
							//
							//	foreach($data['consolid_kot_print_details'] as $order_data){	
							//
							//		if(!empty($data['consolid_kot_print_details']) && !empty($data['consolid_kitchens'])){		
							//			$this->remotePrintingCONSOLIDKOT($data);
							//		}
							//	}
							//}					
					//}
					$tableid = !empty($this->input->post('table_id'))  ? $this->input->post('table_id')  : 0 ;
					if($this->site->isSocketEnabled() && $tableid){
					$this->site->socket_refresh_bbqtables($tableid);	
					}					
					$result = array( 'status'=> true , 'message'=> lang('order_has_been_added_success'),'message_khmer'=> html_entity_decode(lang('order_has_been_added_success_khmer')), 'kitchen_detail' => $data);
				}else{
					$result = array( 'status'=> false , 'message'=> lang('order_does_not_added'),'message_khmer'=> html_entity_decode(lang('order_does_not_added_khmer')));
				}
			}else{
				$this->sendtokitchen_api->update_bbq_order_request($bbqcode);
				$result = array( 'status'=> false , 'message'=> lang('cover_not_validated'),'message_khmer'=> html_entity_decode(lang('cover_not_validated_khmer')));
			}
			}else{
				$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
			}
		}else{
			$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));	
		}
		
		$this->response($result);
		
	}
	
	public function remotePrintingKOT_bk($kitchen_data=array()){
		if(!empty($kitchen_data)){
			$ordered_by = 'N/A';
			$user = $this->site->getUser($kitchen_data['orders_details']->created_by); 
			if($user){ 
			$ordered_by = $user->first_name.' '.$user->last_name; 
			}
			$biller = $this->site->getCompanyOrderByID($kitchen_data['orders_details']->biller_id);
			$print_header = "";
			if($kitchen_data['orders_details']->order_type == 1){
				$store_name = "Table : #".$kitchen_data['orders_details']->table_name;
			} elseif($kitchen_data['orders_details']->order_type == 2){
				$store_name = "Takeaway : #".$kitchen_data['orders_details']->reference_no;
			} else{
				$store_name = "Delivery : #".$kitchen_data['orders_details']->reference_no;
			}
			//$print_header .= $biller->company;
			//$print_header .= ', ';
			//$print_header .= $biller->address;
			if($this->Settings->time_format == 12){
			$date = new DateTime($kitchen_data['orders_details']->created_on);
			$created_on = $date->format('Y-m-d h:iA');
			}else{
				$created_on =  $kitchen_data['orders_details']->created_on;
			}

			$print_header .= "\n";
			$print_header .= "KOT ORDER";
			$print_header .= "\n\n";
			$print_info_common = "";
			$print_info_common .= 'Order Number';
			$print_info_common .= ' : ';
			$print_info_common .= $kitchen_data['orders_details']->reference_no;
			$print_info_common .= "\n";
			$print_info_common .= 'Date';
			$print_info_common .= ' : ';
			$print_info_common .= $created_on;
			$print_info_common .= "\n";
			$print_info_common .= 'Order Person';
			$print_info_common .= ' : ';
			$print_info_common .= $ordered_by;
			$print_info_common .= "\n";
			
			
			
			if(!empty($kitchen_data['kitchens'])){
				foreach($kitchen_data['kitchens'] as $order_data){
					$print_info = ''; 
					$print_info .= $print_info_common;
					$print_info .= 'Kitchen Type';
					$print_info .= ' : ';
					$print_info .= $order_data->name;
					$print_info .= "\n-----------------------------------------------\n";
					
					if(!empty($order_data->kit_o) && !empty($order_data->printers_details)){
						$i =1;
						
						$orderItemCnt = count($order_data->kit_o);
						foreach($order_data->kit_o as $item_data){
							$print_items = "";
							$list = array();
							$print_items .= '';
							$print_items .= $i;
							$print_items .= ' ';
							if(!empty($item_data['khmer_recipe_image'])){
								$print_items .= $item_data['khmer_recipe_image'];
							}else{
								$print_items .= $item_data['recipe_name'];
							}
							$print_items .= "";
							$print_items .= '   X ';
							$print_items .= $item_data['quantity'];
							$print_items .= "\n";
							$newline = false;
							if($orderItemCnt!=$i){
							    $newline ="\n";
							}
							$list[] = array(
								'sno' => $i,
								'en_recipe_name' => $this->wraprecipe_name_qty($item_data['en_recipe_name'],$item_data['quantity'],$newline),
								'quantity' => $item_data['quantity'],
								'comment' => $item_data['comment'],
								'khmer_image' => $item_data['khmer_recipe_image']
							);
							$i++;
							//Remote printing KOT
						$receipt = array(
							'store_name' => $store_name,
							'header' => $print_header,
							'info' => $print_info,
							'items' => $print_items,
							'itemlists' => $list
						);
						$data = array(
						'type'=>'print-receipt',
						'data'=>array(
							'printer' => $order_data->printers_details,
							// 'logo'=> base_url().'assets/uploads/logos/'.$biller->logo,
							'text' => $receipt,
							'cash_drawer' => ''
						)
						);
						/*echo "<pre>";
						print_r($data);*/
						if($this->pos_settings->kot_print_logo){
						    $data['data']['logo'] = base_url().'assets/uploads/logos/'.$biller->logo;
						}
						if(!empty($this->ws->checkConnection())){
							$result = $this->ws->send(json_encode($data));						
							$this->ws->close();						
						}

						}
						
					}
				}
			}//die;
		}
	}
	public function remotePrintingKOT_single_bk($kitchen_data=array()){
		if(!empty($kitchen_data)){
			$ordered_by = 'N/A';
			$user = $this->site->getUser($kitchen_data['orders_details']->created_by); 
			if($user){ 
			$ordered_by = $user->first_name.' '.$user->last_name; 
			}
			$biller = $this->site->getCompanyOrderByID($kitchen_data['orders_details']->biller_id);
			$print_header = "";
			if($kitchen_data['orders_details']->order_type == 1 || $kitchen_data['orders_details']->order_type == 4){
				$store_name = "Table : #".$kitchen_data['orders_details']->table_name;
			} elseif($kitchen_data['orders_details']->order_type == 2){
				$store_name = "Takeaway : #".$kitchen_data['orders_details']->reference_no;
			} else{
				$store_name = "Delivery : #".$kitchen_data['orders_details']->reference_no;
			}
			//$print_header .= $biller->company;
			//$print_header .= ', ';
			//$print_header .= $biller->address;
			$print_header .= "\n";
			$print_header .= "KOT ORDER";
			$print_header .= "\n\n";
			$print_info_common = "";
			$print_info_common .= lang('order_number');
			$print_info_common .= ' : ';
			$print_info_common .= $kitchen_data['orders_details']->reference_no;
			$print_info_common .= "\n";
			$print_info_common .= lang('date');
			$print_info_common .= ' : ';
			$print_info_common .= $kitchen_data['orders_details']->created_on;
			$print_info_common .= "\n";
			$print_info_common .= lang('order_person');
			$print_info_common .= ' : ';
			$print_info_common .= $ordered_by;
			$print_info_common .= "\n";
			
			
			
			if(!empty($kitchen_data['kitchens'])){
				foreach($kitchen_data['kitchens'] as $order_data){
					$print_info = ''; 
					$print_info .= $print_info_common;
					$print_info .= lang('kitchen_type');
					$print_info .= ' : ';
					$print_info .= $order_data->name;
					$print_info .= "\n-----------------------------------------------\n";
					$print_items = "";
					if(!empty($order_data->kit_o) && !empty($order_data->printers_details)){
						$i =1;
						$list = array();
						$orderItemCnt = count($order_data->kit_o);
						foreach($order_data->kit_o as $item_data){
							
							
							$print_items .= '';
							$print_items .= $i;
							$print_items .= ' ';
							if(!empty($item_data['khmer_recipe_image'])){
								$print_items .= $item_data['khmer_recipe_image'];
							}else{
								$print_items .= $item_data['recipe_name'];
							}
							$print_items .= "";
							$print_items .= '   X ';
							$print_items .= $item_data['quantity'];
							$print_items .= "\n";
							$newline = false;
							if($orderItemCnt!=$i){
							    $newline ="\n";
							}
							$list[] = array(
								'sno' => $i,
								'en_recipe_name' => $this->wraprecipe_name_qty($item_data['en_recipe_name'],$item_data['quantity'],$newline),
								'quantity' => $item_data['quantity'],
								'khmer_image' => $item_data['khmer_recipe_image']
							);
							$i++;
						}
						//Remote printing KOT
						$receipt = array(
							'store_name' => $store_name,
							'header' => $print_header,
							'info' => $print_info,
							'items' => $print_items,
							'itemlists' => $list
						);
						$data = array(
						'type'=>'print-receipt',
						'data'=>array(
							'printer' => $order_data->printers_details,
							//'logo'=> base_url().'assets/uploads/logos/'.$biller->logo,
							'text' => $receipt,
							'cash_drawer' => ''
						)
						);
						if($this->pos_settings->kot_print_logo){
						    $data['data']['logo'] = base_url().'assets/uploads/logos/'.$biller->logo;
						}
						if(!empty($this->ws->checkConnection())){//echo '<pre>';print_R($data);
						$result = $this->ws->send(json_encode($data));
						$this->ws->close();
						}
					}
				}
			}//die;
		}
	}
	public function remotePrintingCONSOLIDKOT($kitchen_data=array()){

		if(!empty($kitchen_data)){
			$ordered_by = 'N/A';
			$user = $this->site->getUser($kitchen_data['consolid_orders_details']->created_by); 
			if($user){ 
			$ordered_by = $user->first_name.' '.$user->last_name; 
			}
			$biller = $this->site->getCompanyOrderByID($kitchen_data['consolid_orders_details']->biller_id);
			$print_header = "";
			if($kitchen_data['consolid_orders_details']->order_type == 1 || $kitchen_data['consolid_orders_details']->order_type == 4){
				$store_name = "Table : #".$kitchen_data['consolid_orders_details']->table_name;
			} elseif($kitchen_data['consolid_orders_details']->order_type == 2){
				$store_name = "Takeaway : #".$kitchen_data['consolid_orders_details']->reference_no;
			} else{
				$store_name = "Delivery : #".$kitchen_data['consolid_orders_details']->reference_no;
			}
			if($this->Settings->time_format == 12){
			$date = new DateTime($kitchen_data['orders_details']->created_on);
			$created_on = $date->format('Y-m-d h:iA');
			}else{
				$created_on =  $kitchen_data['orders_details']->created_on;
			}

			$print_header .= "\n";
			$print_header .= "KOT ORDER";
			$print_header .= "\n\n";
			$print_info_common = "";
			$print_info_common .= 'Order Number';
			$print_info_common .= ' : ';
			$print_info_common .= $kitchen_data['consolid_orders_details']->reference_no;
			$print_info_common .= "\n";
			$print_info_common .= 'Date';
			$print_info_common .= ' : ';
			$print_info_common .= $created_on;
			$print_info_common .= "\n";
			$print_info_common .= 'Order Person';
			$print_info_common .= ' : ';
			$print_info_common .= $ordered_by;
			$print_info_common .= "\n";
			
			
			
			if(!empty($kitchen_data['consolid_kot_print_details'])){

				foreach($kitchen_data['consolid_kot_print_details'] as $order_data){
	
					$print_info = ''; 
					$print_info .= $print_info_common;
					
					$print_info .= "\n-----------------------------------------------\n";
					$print_items = "";
					if(!empty($kitchen_data['consolid_kot_print_details']) && !empty($kitchen_data['consolid_kitchens'])){
						$i =1;
						$list = array();
						$orderItemCnt = count($kitchen_data['consolid_kitchens']);
						foreach($kitchen_data['consolid_kitchens'] as $item_data){
							
							
							$print_items .= '';
							$print_items .= $i;
							$print_items .= ' ';
							if(!empty($item_data['khmer_recipe_image'])){
								$print_items .= $item_data['khmer_recipe_image'];
							}else{
								$print_items .= $item_data['recipe_name'];
							}
							$print_items .= "";
							$print_items .= '   X ';
							$print_items .= $item_data['quantity'];
							$print_items .= "\n";
							$newline = false;
							if($orderItemCnt!=$i){
							    $newline ="\n";
							}
							$list[] = array(
								'sno' => $i,
								'en_recipe_name' => $this->wraprecipe_name_qty($item_data['en_recipe_name'],$item_data['quantity'],$newline),
								'quantity' => $item_data['quantity'],
								'khmer_image' => $item_data['khmer_recipe_image']
							);
							$i++;
						}
						//Remote printing KOT
						$receipt = array(
							'store_name' => $store_name,
							'header' => $print_header,
							'info' => $print_info,
							'items' => $print_items,
							'itemlists' => $list
						);
						$data = array(
						'type'=>'print-receipt',
						'data'=>array(
							'printer' => $order_data,
							//'logo'=> base_url().'assets/uploads/logos/'.$biller->logo,
							'text' => $receipt,
							'cash_drawer' => ''
						)
						);
						if($this->pos_settings->kot_print_logo){
						    $data['data']['logo'] = base_url().'assets/uploads/logos/'.$biller->logo;
						}
						if(!empty($this->ws->checkConnection())){//echo '<pre>';print_R($data);
						$result = $this->ws->send(json_encode($data));
						$this->ws->close();
						}
					}
				}
				
			}//die;
		}
	}	
	public function remotePrintingKOT_new($kitchen_data=array()){
		if(!empty($kitchen_data)){//echo '<pre>';print_R($kitchen_data);die;
			$ordered_by = 'N/A';
			$user = $this->site->getUser($kitchen_data['orders_details']->created_by); 
			if($user){ 
			$ordered_by = $user->first_name.' '.$user->last_name; 
			}
			$biller = $this->site->getCompanyOrderByID($kitchen_data['orders_details']->biller_id);
			$print_header = "";
			$store_name = "Table : #".$kitchen_data['orders_details']->table_name;
			//$print_header .= $biller->company;
			//$print_header .= ', ';
			//$print_header .= $biller->address;
			$print_header .= "\n";
			$print_header .= "KOT ORDER";
			$print_header .= "\n\n";
			$print_info_common = "";
			$print_info_common .= lang('order_number');
			$print_info_common .= ' : ';
			$print_info_common .= $kitchen_data['orders_details']->reference_no;
			$print_info_common .= "\n";
			$print_info_common .= lang('date');
			$print_info_common .= ' : ';
			$print_info_common .= $kitchen_data['orders_details']->date;
			$print_info_common .= "\n";
			$print_info_common .= lang('order_person');
			$print_info_common .= ' : ';
			$print_info_common .= $ordered_by;
			$print_info_common .= "\n";
			
			
			
			if(!empty($kitchen_data['kitchens'])){
				foreach($kitchen_data['kitchens'] as $order_data){
					$print_info = ''; 
					$print_info .= $print_info_common;
					$print_info .= lang('kitchen_type');
					$print_info .= ' : ';
					$print_info .= $order_data->name;
					$print_info .= "\n-----------------------------------------\n\n\n";
					$print_items = "";
					if(!empty($order_data->kit_o) && !empty($order_data->printers_details)){
						$i =1;
						foreach($order_data->kit_o as $item_data){
							
							$print_items .= '#';
							$print_items .= $i;
							$print_items .= ' ';
							$print_items .= $item_data['recipe_name'];
							$print_items .= "";
							$print_items .= '   X ';
							$print_items .= $item_data['quantity'];
							$print_items .= "\n";
							$i++;
						}
						//Remote printing KOT
						$receipt = array(
							'store_name' => $store_name,
							'header' => $print_header,
							'info' => $print_info,
							'items' => $print_items
						);
						$data = array(
						'type'=>'print-receipt',
						'data'=>array(
							'printer' => $order_data->printers_details,
							'logo'=> base_url().'assets/uploads/logos/'.$biller->logo,
							'text' => $receipt,
							'cash_drawer' => ''
						)
						);
						if(!empty($this->ws->checkConnection())){//echo '<pre>';print_R($data);
						$result = $this->ws->send(json_encode($data));
						$this->ws->close();
						}
					}
				}
			}//die;
		}
	}
	
	public function remotePrintingKOT_single($kitchen_data=array()){
		    
		    $ch = curl_init();
		    curl_setopt($ch, CURLOPT_URL, site_url('kot_print/single_item'));
		    curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
		    $kitchendata = json_encode( array('k_data'=>$kitchen_data) );
		    curl_setopt($ch, CURLOPT_POST, 1);
		    curl_setopt( $ch, CURLOPT_POSTFIELDS, $kitchendata );
		    curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		    curl_setopt($ch, CURLOPT_VERBOSE, true);
		    $result = curl_exec($ch);
		    curl_close($ch);
	}
	public function remotePrintingKOT($kitchen_data=array()){
	    
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, site_url('kot_print/all_items'));
		curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
		$kitchendata = json_encode( array('k_data'=>$kitchen_data) );
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $kitchendata );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_VERBOSE, true);
		$result = curl_exec($ch);
		curl_close($ch);
	}
	function kot_consolidated_curl($kotconsoildprint){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, site_url('kot_print/kot_consolidated'));
		curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
		$kitchendata = json_encode($kotconsoildprint);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $kitchendata );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_VERBOSE, true);
		$result = curl_exec($ch);
		curl_close($ch);
	      }
	function send_to_kot_print($kot_print_data){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, site_url('kot_print/send_to_kot_print'));
		curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
		$kot_print_data = json_encode($kot_print_data);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $kot_print_data );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_VERBOSE, true);
		$result = curl_exec($ch);
		curl_close($ch);
	      }

	
}
