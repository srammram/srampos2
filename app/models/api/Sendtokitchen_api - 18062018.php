<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Sendtokitchen_api extends CI_Model
{

    public function __construct() {
        parent::__construct();
    }	
	
	public function addKitchen($order_data = array(), $item_data = array(), $kitchen = array(), $notification_array = array())
	{
		$this->site->create_notification($notification_array);
		
		if ($this->db->insert('orders', $order_data)){
				$sale_id = $this->db->insert_id();
				
				$this->db->select('orders.*, restaurant_tables.name AS table_name');
				$this->db->join('restaurant_tables', 'restaurant_tables.id = orders.table_id', 'left');
				$this->db->where('orders.id', $sale_id);
				$t = $this->db->get('orders');
				$kit = array();
				if ($t->num_rows() > 0) {
					$orders_details =  $t->row();
					$kit['orders_details'] = $orders_details;
					$kit['consolid_orders_details'] = $orders_details;
				}
				
			if($this->pos_settings->consolidated_kot_print != 0){

				$table_id = $kit['consoild_orders_details']->table_id;

				$consolid_kot_print_details = $this->db->select("printers.*")
				->join('restaurant_areas', 'restaurant_areas.printer_id = printers.id','left')
				->join('restaurant_tables', 'restaurant_tables.area_id = restaurant_areas.id','left')							
				->where('restaurant_tables.id', $table_id)
				->get('printers');

				if ($consolid_kot_print_details->num_rows() > 0) {
				  $kit['consolid_kot_print_details'][] =  $consolid_kot_print_details->row();
			     }else{
			     	$kit['consolid_kot_print_details'] =array();
			     }
					$consolid_kit_item =array();
					foreach($items as $key => $kit_item){
							/*var_dump($kit_item['recipe_id']);*/
							$addons = $this->site->getAddonByRecipe($kit_item['recipe_id'], $kit_item['addon_id']);

							$get_item =  $this->site->getrecipeByID($kit_item['get_item']);
								
							$consolid_kit_item[$key]['recipe_id'] = $kit_item['recipe_id'];
							
							if($this->Settings->user_language == 'khmer' || true){
								$khmer_name = $this->site->getrecipeKhmer($kit_item['recipe_id']);	
								$khmer_image = $this->site->getrecipeKhmerimage($kit_item['recipe_id']);
								
								if(!empty($khmer_name)){
									$consolid_kit_item[$key]['recipe_name'] = $khmer_name;
									
								}else{
									$consolid_kit_item[$key]['recipe_name'] = $kit_item['recipe_name'];
								}
							}else{
								$consolid_kit_item[$key]['recipe_name'] = $kit_item['recipe_name'];
							}

							$consolid_kit_item[$key]['en_recipe_name'] = $kit_item['recipe_name'];
							$consolid_kit_item[$key]['khmer_recipe_image'] = !empty($khmer_image) ? (base_url().'assets/language/'.$khmer_image) : '';
														
							$consolid_kit_item[$key]['quantity'] = $kit_item['quantity'];
							
							$consolid_kit_item[$key]['get_item_name'] = $get_item->name;
							$consolid_kit_item[$key]['total_get_quantity'] = $get_item->total_get_quantity;
							
							foreach($addons as $addons_row){
                               		$addon_name = '<small class="text-danger">'.$addons_row->addon_name.' , '.'</small>';
                            	}
								
							$consolid_kit_item[$key]['addons'] = $addon_name;
					}						
						$kit['consolid_kitchens'] = $consolid_kit_item;
				    }
				else
				{
					$kit['consolid_kitchens'] = array();
				}	
				$kitchen_details = $this->db->select('restaurant_kitchens.id, restaurant_kitchens.name')->get('restaurant_kitchens');
				
				if ($kitchen_details->num_rows() > 0) {
					foreach (($kitchen_details->result()) as $kitchen_row) {
						
						$printers_details = $this->db->select('*')->where('id', $kitchen_row->id)->get('printers');
						
						if ($printers_details->num_rows() > 0) {
							$kitchen_row->printers_details =  $printers_details->row();
							
						}
						
						foreach($item_data as $key => $kit_item){
							if($kitchen_row->id == $kit_item['kitchen_type_id']){
								
								$addons = $this->site->getAddonByRecipe($kit_item['recipe_id'], $kit_item['addon_id']);
								$get_item =  $this->site->getrecipeByID($kit_item['get_item']);
									
								$kitchen_row->kit_o[$key]['recipe_id'] = $kit_item['recipe_id'];
								//$kitchen_row->kit_o[$key]['recipe_name'] = $kit_item['recipe_name'];
								$kitchen_row->kit_o[$key]['quantity'] = $kit_item['quantity'];
								
								$kitchen_row->kit_o[$key]['get_item_name'] = $get_item->name;
								$kitchen_row->kit_o[$key]['total_get_quantity'] = $get_item->total_get_quantity;
								
								if($this->Settings->user_language == 'khmer' || true){
									$khmer_name = $this->site->getrecipeKhmer($kit_item['recipe_id']);	
									$khmer_image = $this->site->getrecipeKhmerimage($kit_item['recipe_id']);
									
									if(!empty($khmer_name)){
										$kitchen_row->kit_o[$key]['recipe_name'] = $khmer_name;
										
									}else{
										$kitchen_row->kit_o[$key]['recipe_name'] = $kit_item['recipe_name'];
									}
								}else{
									$kitchen_row->kit_o[$key]['recipe_name'] = $kit_item['recipe_name'];
								}
								
								$kitchen_row->kit_o[$key]['en_recipe_name'] = $kit_item['recipe_name'];
								$kitchen_row->kit_o[$key]['khmer_recipe_image'] = !empty($khmer_image) ? (base_url().'assets/language/'.$khmer_image) : '';
								
								
								foreach($addons as $addons_row){
                                   		$addon_name = '<small class="text-danger">'.$addons_row->addon_name.' , '.'</small>';
                                	}
									
								$kitchen_row->kit_o[$key]['addons'] = $addon_name;
								
								//$kitchen_row->kit_o[$kitchen_row->id][] = $kit_item['quantity'];
							}
						}
						$kit['kitchens'][] = $kitchen_row;
					}
					
				}else{
					$kit[] = '';	
				}
				
				$msg = array();
				$msg[] = 'Your order has been success';
				$kitchen['sale_id'] = $sale_id;
				
				$this->db->insert('restaurant_table_orders', array('order_id' => $sale_id, 'table_id' => $order_data['table_id']));
				$this->db->insert('restaurant_table_sessions', array('table_id' => $order_data['table_id'], 'split_id' => $order_data['split_id'], 'customer_id' => $order_data['customer_id'], 'session_started' => date('Y-m-d H:i:s')));
				
				if($this->db->insert('kitchen_orders', $kitchen)){
					$kitchen_id = $this->db->insert_id();
					
					foreach ($item_data as $item) {
						$item['sale_id'] = $sale_id;
						$item['kitchen_id'] = $kitchen_id;
						$this->db->insert('order_items', $item);
					}
					$msg[] = 'Order sent to kitchen process. wait few mintues';
					
				}
				return $kit;
		}
		return false;
	}
	
	public function getSplitId($customer_id, $warehouse_id,$table_id){
		$current_date = date('Y-m-d');
		$this->db->where('orders.customer_id', $customer_id);
		$this->db->where('orders.order_status', 'Open');
		$this->db->where('orders.warehouse_id', $warehouse_id);
		$this->db->where('orders.table_id', $table_id);
		$this->db->where('DATE(date)', $current_date);
		$q = $this->db->get('orders');
		if ($q->num_rows() > 0) {
			return $q->row(split_id);
		}
		return false;
		
	}
}
