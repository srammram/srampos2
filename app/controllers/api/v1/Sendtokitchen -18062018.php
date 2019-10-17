<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;



class Sendtokitchen extends REST_Controller {

	function __construct() {
		parent::__construct();
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		$this->load->api_model('sendtokitchen_api');
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
		$devices_key = $this->input->post('devices_key');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$warehouse_id = $this->input->post('warehouse_id');
		$table_id = $this->post('table_id');
		
		$customer_id = $this->post('customer_id');
		$biller_id = $this->post('biller_id');
		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		$this->form_validation->set_rules('user_id', $this->lang->line("user_id"), 'required');
		$this->form_validation->set_rules('customer_id', $this->lang->line("customer_id"), 'required');
		$this->form_validation->set_rules('biller_id', $this->lang->line("biller_id"), 'required');
		$this->form_validation->set_rules('table_id', $this->lang->line("table_id"), 'required');
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
				
				$split_id = ($ordersplitid) ? ($ordersplitid) : 'SPILT'.date('YmdHis');
				
				$order_data = array('date'  => $date,
					'reference_no'      => $reference,
					'table_id'			=> !empty($this->post('table_id'))  ? $this->post('table_id')  : 0 ,
					'seats_id'			=> !empty($this->post('table_id')) ? $this->post('seats_id') : 0,
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
					'hash'              => hash('sha256', microtime() . mt_rand()),
					);
			   
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
						'addon_id' => $_POST['addon_id'][$i],
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
					'is_read' => 0
				);
				
				//$this->sma->print_arrays($order_data, $item_data, $kitchen);
				//die;
				$data = $this->sendtokitchen_api->addKitchen($order_data, $item_data, $kitchen, $notification_array);
				if(!empty($data)){
					$this->remotePrintingKOT($data);
					
			if($this->pos_settings->consolidated_kot_print != 0){	
				/**/

				$kotconsoildprint = $data['consoild_orders_details'];

		

					if(!empty($kotconsoildprint['consolid_kot_print_details'])){
						
		
						foreach($kotconsoildprint['consolid_kot_print_details'] as $order_data){	
							
				
							if(!empty($kotconsoildprint['consolid_kot_print_details']) && !empty($kotconsoildprint['consolid_kitchens'])){

								$this->remotePrintingCONSOLIDKOT($sale['consolid_kitchen_data']);
							}
						}
					}					
			}
			
					$result = array( 'status'=> true , 'message'=> 'Order has been added success', 'kitchen_detail' => $data);
				}else{
					$result = array( 'status'=> false , 'message'=> 'Order does not added');
				}
			}else{
				$result = array( 'status'=> false , 'message'=> 'Devices Key does not matche. please check your devices key');	
			}
		}else{
			$result = array( 'status'=> false , 'message'=> 'Please Enter All Fields');	
		}
		
		$this->response($result);
		
	}
	
	public function remotePrintingKOT($kitchen_data=array()){
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
					$print_info .= "\n-----------------------------------------------\n";
					$print_items = "";
					if(!empty($order_data->kit_o) && !empty($order_data->printers_details)){
						$i =1;
						$list = array();
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
							
							$list[] = array(
								'sno' => $i,
								'en_recipe_name' => $item_data['en_recipe_name'],
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
public function remotePrintingCONSOLIDKOT($kitchen_data=array()){

		if(!empty($kitchen_data)){
			$ordered_by = 'N/A';
			$user = $this->site->getUser($kitchen_data['consolid_orders_details']->created_by); 
			if($user){ 
			$ordered_by = $user->first_name.' '.$user->last_name; 
			}
			$biller = $this->site->getCompanyOrderByID($kitchen_data['consolid_orders_details']->biller_id);
			$print_header = "";
			if($kitchen_data['consolid_orders_details']->order_type == 1){
				$store_name = "Table : #".$kitchen_data['consolid_orders_details']->table_name;
			} elseif($kitchen_data['consolid_orders_details']->order_type == 2){
				$store_name = "Takeaway : #".$kitchen_data['consolid_orders_details']->reference_no;
			} else{
				$store_name = "Delivery : #".$kitchen_data['consolid_orders_details']->reference_no;
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
			$print_info_common .= $kitchen_data['consolid_orders_details']->date;
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
							
							$list[] = array(
								'sno' => $i,
								'en_recipe_name' => $item_data['en_recipe_name'],
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
							'logo'=> base_url().'assets/uploads/logos/'.$biller->logo,
							'text' => $receipt,
							'cash_drawer' => ''
						)
						);
						
						if(!empty($this->ws->checkConnection())){//echo '<pre>';print_R($data);
						$result = $this->ws->send(json_encode($data));
						$this->ws->close();
						}
						/*echo "<pre>";
						print_r($data);die;*/

					}
				}
				/*echo "<pre>";
						print_r($data);die;*/
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

	
}
