<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Sendtokitchen_api extends CI_Model
{

    public function __construct() {
        parent::__construct();
    }	
	
	public function addKitchen($order_data = array(), $item_data = array(), $kitchen = array(), $notification_array = array())
	{
		//$notification_array['tag'] = 'send-to-kitchen';
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
				
if($this->pos_settings->kot_enable_disable == 1){				
	if($this->pos_settings->consolidated_kot_print_option == 0){					
		if($this->pos_settings->consolidated_kot_print != 0){
				$table_id = $kit['consolid_orders_details']->table_id;
				$consolid_kot_print_details = $this->db->select("printers.*")
				->join('restaurant_areas', 'restaurant_areas.printer_id = printers.id','left')
				->join('restaurant_tables', 'restaurant_tables.area_id = restaurant_areas.id','left')							
				->where('printers.id', $this->pos_settings->consolidated_kot_print)
				->get('printers');

				if ($consolid_kot_print_details->num_rows() > 0) {
				  $kit['consolid_kot_print_details'][] =  $consolid_kot_print_details->row();
			     }else{
			     	$kit['consolid_kot_print_details'] =array();
			     }
					$consolid_kit_item =array();
					
					foreach($item_data as $key => $kit_item){
							
							$addons = $this->site->getAddonByRecipe($kit_item['recipe_id'], $kit_item['addon_id']);

							$get_item =  $this->site->getrecipeByID($kit_item['get_item']);
								
							$consolid_kit_item[$key]['recipe_id'] = $kit_item['recipe_id'];
							
							
							if($this->Settings->user_language == 'khmer' || true){
								$khmer_name = $this->site->getrecipeKhmer($kit_item['recipe_id']);	
								if($kit_item['recipe_variant_id']!= 0){
								$khmer_image = $this->site->getitemvariantlocalnameimage($kit_item['recipe_id'],$kit_item['recipe_variant_id']);								
								}else{
									$khmer_image = $this->site->getrecipeKhmerimage($kit_item['recipe_id']);
								}
								
								if(!empty($khmer_name)){
									$consolid_kit_item[$key]['recipe_name'] = $khmer_name;
									
								}else{
									$consolid_kit_item[$key]['recipe_name'] = $kit_item['recipe_name'];
								}
							}else{
								$consolid_kit_item[$key]['recipe_name'] = $kit_item['recipe_name'];
							}
							
							$consolid_kit_item[$key]['en_recipe_name'] = $kit_item['recipe_name'];
							$consolid_kit_item[$key]['comment'] = $kit_item['comment'];								

							$consolid_kit_item[$key]['khmer_recipe_image'] =(!empty($khmer_image) && file_exists('assets/language/'.$khmer_image))? (base_url().'assets/language/'.$khmer_image) : '';

							/*if($kit_item['recipe_variant_id']!= 0){
							$consolid_kit_item[$key]['en_variant_name'] = $kit_item['variant'];
							$variant_localname_image = $this->site->getitemvariantlocalnameimage($kit_item['recipe_id'],$kit_item['recipe_variant_id']);
							$consolid_kit_item[$key]['khmer_recipe_image'] = (!empty($variant_localname_image) && file_exists('assets/language/'.$variant_localname_image))? (base_url().'assets/language/'.$variant_localname_image) : '';
							}else{									
								$consolid_kit_item[$key]['khmer_recipe_image'] = (!empty($khmer_image) && file_exists('assets/language/'.$khmer_image))? (base_url().'assets/language/'.$khmer_image) : '';
							} */
							// $consolid_kit_item[$key]['khmer_recipe_image'] = (!empty($khmer_image) && file_exists('assets/language/'.$khmer_image)) ? (base_url().'assets/language/'.$khmer_image) : '';							
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
/*Consolidate kot for table area wise */	

}else{
/*Consolidate kot only one single priter */	
		$consolidate_kitchen_details = $this->db->select('restaurant_kitchens.id,restaurant_kitchens.kitchen_consolid_printer_id,  restaurant_kitchens.name')->get('restaurant_kitchens');				
		if ($consolidate_kitchen_details->num_rows() > 0) {					
			foreach (($consolidate_kitchen_details->result()) as $consolidate_kitchen_row) {						
				$printers_details = $this->db->select('*')->where('id', $consolidate_kitchen_row->kitchen_consolid_printer_id)->get('printers');
				if ($printers_details->num_rows() > 0) {
					$consolidate_kitchen_row->printers_details =  $printers_details->row();
				}
				foreach($items as $key => $kit_item){
					if($consolidate_kitchen_row->id == $kit_item['kitchen_type_id']){
						$addons = $this->site->getAddonByRecipe($kit_item['recipe_id'], $kit_item['addon_id']);
						$get_item =  $this->site->getrecipeByID($kit_item['get_item']);									
						$consolidate_kitchen_row->kit_o[$key]['recipe_id'] = $kit_item['recipe_id'];								
						if($this->Settings->user_language == 'khmer' || true){
							$khmer_name = $this->site->getrecipeKhmer($kit_item['recipe_id']);	

								if($kit_item['recipe_variant_id']!= 0){
									$khmer_image = $this->site->getitemvariantlocalnameimage($kit_item['recipe_id'],$kit_item['recipe_variant_id']);								
								}else{
										$khmer_image = $this->site->getrecipeKhmerimage($kit_item['recipe_id']);
								}
							
							if(!empty($khmer_name)){
								$consolidate_kitchen_row->kit_o[$key]['recipe_name'] = $khmer_name;
							}else{
								$consolidate_kitchen_row->kit_o[$key]['recipe_name'] = $kit_item['recipe_name'];
							}
						}else{
							$consolidate_kitchen_row->kit_o[$key]['recipe_name'] = $kit_item['recipe_name'];
						}
						$consolidate_kitchen_row->kit_o[$key]['en_recipe_name'] = $kit_item['recipe_name'];
						$consolidate_kitchen_row->kit_o[$key]['comment'] = $kit_item['comment'];
						$consolidate_kitchen_row->kit_o[$key]['en_variant_name'] = $kit_item['variant'] ? $kit_item['variant'] :'';
						$consolidate_kitchen_row->kit_o[$key]['khmer_recipe_image'] = (!empty($khmer_image) && file_exists('assets/language/'.$khmer_image))? (base_url().'assets/language/'.$khmer_image) : '';

						// $consolidate_kitchen_row->kit_o[$key]['khmer_recipe_image'] = (!empty($khmer_image) && file_exists('assets/language/'.$khmer_image))? (base_url().'assets/language/'.$khmer_image) : '';	
						$consolidate_kitchen_row->kit_o[$key]['quantity'] = $kit_item['quantity'];
						$consolidate_kitchen_row->kit_o[$key]['get_item_name'] = $get_item->name;
						$consolidate_kitchen_row->kit_o[$key]['total_get_quantity'] = $get_item->total_get_quantity;
						foreach($addons as $addons_row){
		                	$addon_name = '<small class="text-danger">'.$addons_row->addon_name.' , '.'</small>';
		                }									
						$consolidate_kitchen_row->kit_o[$key]['addons'] = $addon_name;								
					}
				}
				$consolidate_kitchens_kot['kitchens'][] = $consolidate_kitchen_row;
			}					
		}else{
			$consolidate_kitchens_kot[] = '';	
		}
}				
/*Consolidate kot only one single priter */					
				$kitchen_details = $this->db->select('restaurant_kitchens.id,restaurant_kitchens.printer_id, restaurant_kitchens.name')->get('restaurant_kitchens');
				
				if ($kitchen_details->num_rows() > 0) {
					foreach (($kitchen_details->result()) as $kitchen_row) {
						
						$printers_details = $this->db->select('*')->where('id', $kitchen_row->printer_id)->get('printers');
						
						if ($printers_details->num_rows() > 0) {
							$kitchen_row->printers_details =  $printers_details->row();
							
						}
						
						foreach($item_data as $key => $kit_item){
							if($kitchen_row->id == $kit_item['kitchen_type_id']){
								
								// $addons = $this->site->getAddonByRecipe($kit_item['recipe_id'], $kit_item['addon_id']);
								$get_item =  $this->site->getrecipeByID($kit_item['get_item']);
									
								$kitchen_row->kit_o[$key]['recipe_id'] = $kit_item['recipe_id'];
								//$kitchen_row->kit_o[$key]['recipe_name'] = $kit_item['recipe_name'];
								$kitchen_row->kit_o[$key]['quantity'] = $kit_item['quantity'];
								
								$kitchen_row->kit_o[$key]['get_item_name'] = $get_item->name;
								$kitchen_row->kit_o[$key]['total_get_quantity'] = $get_item->total_get_quantity;
								
								if($this->Settings->user_language == 'khmer' || true){
									$khmer_name = $this->site->getrecipeKhmer($kit_item['recipe_id']);	

									if($kit_item['recipe_variant_id']!= 0){
										$khmer_image = $this->site->getitemvariantlocalnameimage($kit_item['recipe_id'],$kit_item['recipe_variant_id']);										
									}else{
										$khmer_image = $this->site->getrecipeKhmerimage($kit_item['recipe_id']);
									}
									if(!empty($khmer_name)){
										$kitchen_row->kit_o[$key]['recipe_name'] = $khmer_name;
										
									}else{
										$kitchen_row->kit_o[$key]['recipe_name'] = $kit_item['recipe_name'];
									}
								}else{
									$kitchen_row->kit_o[$key]['recipe_name'] = $kit_item['recipe_name'];
								}
								
								$kitchen_row->kit_o[$key]['en_recipe_name'] = $kit_item['recipe_name'];
								$kitchen_row->kit_o[$key]['comment'] = $kit_item['comment'];
								$kitchen_row->kit_o[$key]['en_variant_name'] = $kit_item['variant'] ? $kit_item['variant'] :'';

								$kitchen_row->kit_o[$key]['khmer_recipe_image'] = (!empty($khmer_image) && file_exists('assets/language/'.$khmer_image))? (base_url().'assets/language/'.$khmer_image) : '';

								// $kitchen_row->kit_o[$key]['khmer_recipe_image'] = (!empty($khmer_image) && file_exists('assets/language/'.$khmer_image)) ? (base_url().'assets/language/'.$khmer_image) : '';
								
								
								/*foreach($addons as $addons_row){
                                   		$addon_name = '<small class="text-danger">'.$addons_row->addon_name.' , '.'</small>';
                                	}
									
								$kitchen_row->kit_o[$key]['addons'] = $addon_name;*/
								
								//$kitchen_row->kit_o[$kitchen_row->id][] = $kit_item['quantity'];
							}
						}
						$kit['kitchens'][] = $kitchen_row;
					}
					
				}else{
					$kit[] = '';	
				}
}
				$msg = array();
				$msg[] = 'Your order has been success';
				$kitchen['sale_id'] = $sale_id;
				
				$this->db->insert('restaurant_table_orders', array('order_id' => $sale_id, 'table_id' => $order_data['table_id']));
				$this->db->insert('restaurant_table_sessions', array('order_id' => $sale_id, 'table_id' => $order_data['table_id'], 'split_id' => $order_data['split_id'], 'customer_id' => $order_data['customer_id'], 'session_started' => date('Y-m-d H:i:s')));
				
				if($this->db->insert('kitchen_orders', $kitchen)){
					$kitchen_id = $this->db->insert_id();
					
					foreach ($item_data as $item) {
						// print_r($item['addon']);
						$addonid = $item['addon_id'] ? $item['addon_id'] :0; 
						$item['sale_id'] = $sale_id;
						$item['kitchen_id'] = $kitchen_id;
						$this->db->insert('order_items', $item);
						$order_item_id = $this->db->insert_id();
						// print_r($this->db->error());die;
						$cm = $this->db->get_where('category_mapping',array('product_id'=>$item['recipe_id'],'status'=>1))->row();
						$cate['category_id'] = $cm->category_id;
						$cate['subcategory_id'] = $cm->subcategory_id;
						$cate['brand_id'] = $cm->brand_id;
						// $this->site->salestock_out($item['recipe_id'],$item['quantity'],$order_item_id,$item['quantity'],$cate);
						/*if($this->Settings->procurment == 1){
						    if($item['recipe_type'] =='standard'){
								$this->site->updateStockMaster_new($item['recipe_id'],$item['quantity'],$item['quantity'],$cate);
							}
						}*/
						
						/*if($this->Settings->procurment == 1){
							if($item['recipe_type'] =='standard' || $item['recipe_type'] =='production'){
								$this->site->updateStockMaster_new($item['recipe_id'],$item['quantity'],$cate);
							}elseif($item['recipe_type'] =='quick_service'){
								$this->siteprocurment->production_salestock_out($item['recipe_id'],$item['quantity'],$kit_item['recipe_variant_id']);
							}	
						}*/

						if($this->Settings->procurment == 1){
							if($item['recipe_type'] =='standard' || $item['recipe_type'] =='production'){
								$this->site->updateStockMaster_new($item['recipe_id'],$item['quantity'],$cate,$order_item_id);
							}elseif($item['recipe_type'] =='quick_service'){
								$this->siteprocurment->production_salestock_out($item['recipe_id'],$item['quantity'],$kit_item['recipe_variant_id']);
							}	
						}

						/*addon array*/
						$recipe_addon_item =[];
						$someArray = json_decode($addonid, true);
					 	if($someArray !='') :				
							foreach ($someArray as  $split) {

	                            $AddonDetails = $this->site->getaddonitemid($split['addon_id']);
								$recipeDetails = $this->site->getrecipeByID($AddonDetails->addon_item_id);

								$recipe_addon_item[] = array(
									'split_id'      => $order_data['split_id'],
									'order_id'      => $sale_id,
									'sale_item_id'      => $recipeDetails->id ? $recipeDetails->id : 0,
									'order_item_id'      => $order_item_id,
									'addon_id'      => $split['addon_id'] ? $split['addon_id'] : 0,
									'price'      => $recipeDetails->cost ? $recipeDetails->cost : 0,
									'qty'      => $split['addon_qty'],
									'subtotal'      => ($split['addon_qty'] * $recipeDetails->cost),
									);
							}	
							
							foreach ($recipe_addon_item as $recipe_addon) {
								$this->db->insert('addon_sale_items', $recipe_addon);
							}							
						endif;	
						/*addon array*/
					}
					$msg[] = 'Order sent to kitchen process. wait few mintues';
					
				}
				// print_r($this->db->error());die;
				$this->site->updateTableStatus($order_data['table_id'],1,$order_data['created_by']);
				return $kit;
		}
		 // print_r($this->db->error());die;
		return false;
	}
	
	public function BBQaddKitchen($order_data = array(), $item_data = array(), $kitchen = array(), $notification_array = array())
	{
		//$notification_array['tag'] = 'send-to-kitchen';
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
/*Consolidate kot only one single priter */	
if($this->pos_settings->kot_enable_disable == 1){
		if($this->pos_settings->consolidated_kot_print_option == 0){				
			if($this->pos_settings->consolidated_kot_print != 0){
				$table_id = $kit['consolid_orders_details']->table_id;
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
					
					foreach($item_data as $key => $kit_item){
							
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
							$consolid_kit_item[$key]['comment'] = $kit_item['comment'];
							if($kit_item['recipe_variant_id']!= 0){
							$consolid_kit_item[$key]['en_variant_name'] = $kit_item['variant'] ? $kit_item['variant'] :'' ;
							$variant_localname_image = $this->site->getitemvariantlocalnameimage($kit_item['recipe_id'],$kit_item['recipe_variant_id']);
							$consolid_kit_item[$key]['khmer_recipe_image'] = (!empty($variant_localname_image) && file_exists('assets/language/'.$variant_localname_image))? (base_url().'assets/language/'.$variant_localname_image) : '';
							}else{									
								$consolid_kit_item[$key]['khmer_recipe_image'] = (!empty($khmer_image) && file_exists('assets/language/'.$khmer_image))? (base_url().'assets/language/'.$khmer_image) : '';
							}
							// $consolid_kit_item[$key]['khmer_recipe_image'] = (!empty($khmer_image) && file_exists('assets/language/'.$khmer_image)) ? (base_url().'assets/language/'.$khmer_image) : '';							
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
		/*Consolidate kot for table area wise */	
}else{

	/*Consolidate kot only one single priter */	
		$consolidate_kitchen_details = $this->db->select('restaurant_kitchens.id,restaurant_kitchens.kitchen_consolid_printer_id,  restaurant_kitchens.name')->get('restaurant_kitchens');				
		if ($consolidate_kitchen_details->num_rows() > 0) {					
			foreach (($consolidate_kitchen_details->result()) as $consolidate_kitchen_row) {						
				$printers_details = $this->db->select('*')->where('id', $consolidate_kitchen_row->kitchen_consolid_printer_id)->get('printers');
				if ($printers_details->num_rows() > 0) {
					$consolidate_kitchen_row->printers_details =  $printers_details->row();
				}
				foreach($items as $key => $kit_item){
					if($consolidate_kitchen_row->id == $kit_item['kitchen_type_id']){
						$addons = $this->site->getAddonByRecipe($kit_item['recipe_id'], $kit_item['addon_id']);
						$get_item =  $this->site->getrecipeByID($kit_item['get_item']);									
						$consolidate_kitchen_row->kit_o[$key]['recipe_id'] = $kit_item['recipe_id'];								
						if($this->Settings->user_language == 'khmer' || true){
							$khmer_name = $this->site->getrecipeKhmer($kit_item['recipe_id']);	
							$khmer_image = $this->site->getrecipeKhmerimage($kit_item['recipe_id']);
							
							if(!empty($khmer_name)){
								$consolidate_kitchen_row->kit_o[$key]['recipe_name'] = $khmer_name;
							}else{
								$consolidate_kitchen_row->kit_o[$key]['recipe_name'] = $kit_item['recipe_name'];
							}
						}else{
							$consolidate_kitchen_row->kit_o[$key]['recipe_name'] = $kit_item['recipe_name'];
						}
						$consolidate_kitchen_row->kit_o[$key]['en_recipe_name'] = $kit_item['recipe_name'];
						$consolidate_kitchen_row->kit_o[$key]['comment'] = $kit_item['comment'];
						if($kit_item['recipe_variant_id']!= 0){
							$consolidate_kitchen_row->kit_o[$key]['en_variant_name'] = $kit_item['variant'];
							$variant_localname_image = $this->site->getitemvariantlocalnameimage($kit_item['recipe_id'],$kit_item['recipe_variant_id']);
							$consolidate_kitchen_row->kit_o[$key]['khmer_recipe_image'] = (!empty($variant_localname_image) && file_exists('assets/language/'.$variant_localname_image))? (base_url().'assets/language/'.$variant_localname_image) : '';
						}else{									
							$consolidate_kitchen_row->kit_o[$key]['khmer_recipe_image'] = (!empty($khmer_image) && file_exists('assets/language/'.$khmer_image))? (base_url().'assets/language/'.$khmer_image) : '';
						} 
						// $consolidate_kitchen_row->kit_o[$key]['khmer_recipe_image'] = (!empty($khmer_image) && file_exists('assets/language/'.$khmer_image))? (base_url().'assets/language/'.$khmer_image) : '';	
						$consolidate_kitchen_row->kit_o[$key]['quantity'] = $kit_item['quantity'];
						$consolidate_kitchen_row->kit_o[$key]['get_item_name'] = $get_item->name;
						$consolidate_kitchen_row->kit_o[$key]['total_get_quantity'] = $get_item->total_get_quantity;
						foreach($addons as $addons_row){
		                	$addon_name = '<small class="text-danger">'.$addons_row->addon_name.' , '.'</small>';
		                }									
						$consolidate_kitchen_row->kit_o[$key]['addons'] = $addon_name;								
					}
				}
				$consolidate_kitchens_kot['kitchens'][] = $consolidate_kitchen_row;
			}					
		}else{
			$consolidate_kitchens_kot[] = '';	
		}
}				
/*Consolidate kot only one single priter */				
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
								$kitchen_row->kit_o[$key]['comment'] = $kit_item['comment'];
								if($kit_item['recipe_variant_id']!= 0){
									$kitchen_row->kit_o[$key]['en_variant_name'] = $kit_item['variant'];
									$variant_localname_image = $this->site->getitemvariantlocalnameimage($kit_item['recipe_id'],$kit_item['recipe_variant_id']);
									$kitchen_row->kit_o[$key]['khmer_recipe_image'] = (!empty($variant_localname_image) && file_exists('assets/language/'.$variant_localname_image))? (base_url().'assets/language/'.$variant_localname_image) : '';
								}else{									
									$kitchen_row->kit_o[$key]['khmer_recipe_image'] = (!empty($khmer_image) && file_exists('assets/language/'.$khmer_image))? (base_url().'assets/language/'.$khmer_image) : '';
								}
								// $kitchen_row->kit_o[$key]['khmer_recipe_image'] = (!empty($khmer_image) && file_exists('assets/language/'.$khmer_image)) ? (base_url().'assets/language/'.$khmer_image) : '';
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
}				
				$msg = array();
				$msg[] = 'Your order has been success';
				$kitchen['sale_id'] = $sale_id;
				
				$this->db->insert('restaurant_table_orders', array('order_id' => $sale_id, 'table_id' => $order_data['table_id']));
				$this->db->insert('restaurant_table_sessions', array('order_id' => $sale_id, 'table_id' => $order_data['table_id'], 'split_id' => $order_data['split_id'], 'customer_id' => $order_data['customer_id'], 'session_started' => date('Y-m-d H:i:s')));
				
				if($this->db->insert('kitchen_orders', $kitchen)){
					$kitchen_id = $this->db->insert_id();
					
					foreach ($item_data as $item) {
						$item['sale_id'] = $sale_id;
						$item['kitchen_id'] = $kitchen_id;
						$this->db->insert('order_items', $item);
						$order_item_id = $this->db->insert_id();
						$cm = $this->db->get_where('category_mapping',array('product_id'=>$item['recipe_id'],'status'=>1))->row();
						$cate['category_id'] = $cm->category_id;
						$cate['subcategory_id'] = $cm->subcategory_id;
						$cate['brand_id'] = $cm->brand_id;	

						if($this->Settings->procurment == 1){
							if($item['recipe_type'] =='standard' || $item['recipe_type'] =='production'){
								$this->site->updateStockMaster_new($item['recipe_id'],$item['quantity'],$cate);
							}elseif($item['recipe_type'] =='quick_service'){
								$this->siteprocurment->production_salestock_out($item['recipe_id'],$item['quantity'],$kit_item['recipe_variant_id']);
							}	
						}

						/* if($item['recipe_type'] =='standard'){
							$this->site->updateStockMaster_new($item['recipe_id'],$item['quantity'],$item['quantity'],$cate);
						}*/
						// $this->site->salestock_out($item['recipe_id'],$item['quantity'],$order_item_id,$item['quantity'],$cate);
					}
					$msg[] = 'Order sent to kitchen process. wait few mintues';
					
				}
				return $kit;
		}
		return false;
	}
	
	public function getSplitId($customer_id, $warehouse_id,$table_id){
		$current_date = date('Y-m-d');
		$this->db->select('*');		
		$this->db->where('orders.customer_id', $customer_id);
		$this->db->where('orders.order_status', 'Open');
		$this->db->where('orders.sale_status', null);
		$this->db->where('orders.warehouse_id', $warehouse_id);
		$this->db->where('orders.table_id', $table_id);
		$this->db->where('DATE(date)', $current_date);		
		$q = $this->db->get('orders');
		if ($q->num_rows() > 0) {
			$this->db->where('sales.sales_split_id', $q->row('split_id'));
			$s = $this->db->get('sales');
			if($s->num_rows() > 0){
				return false;
			}else{
				return $q->row('split_id');
			}
			
		}
		return false;
		
	}
	
    function getSetting()
    {
        $q = $this->db->get('pos_settings');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    public function isBBQCoverConfirmed($reference_no){
	    
	    $this->db->select('bbq.*');
	    $this->db->where('bbq.confirmed_by !=','');
	    $this->db->where('reference_no', $reference_no);
	    $q = $this->db->get('bbq');
	    
	    if ($q->num_rows()>0) {
		return $q->row();
	    }
	    return false;
	}

	public function update_bbq_order_request($bbqcode){		
		if(!empty($bbqcode)){	
			$this->db->where_in('reference_no', $bbqcode);
			$this->db->update('bbq', array('order_request' => 1));		
			return true;
		}
		return false;
	}
    function getVariantData($vid,$rid){
	$this->db->select('v.*,r.*');
	$this->db->from('recipe_variants_values r');
	$this->db->join('recipe_variants v','v.id=r.attr_id');
	$this->db->where(array('r.recipe_id'=>$rid,'v.id'=>$vid));
	//echo $this->db->get_compiled_select();
	$q = $this->db->get();
	if($q->num_rows()>0){
	    return $q->row();
	}
	return false;
	
    }	
}
