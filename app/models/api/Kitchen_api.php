<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Kitchen_api extends CI_Model
{

    public function __construct() {
        parent::__construct();
    }

	public function GetAllkitchen(){
		$q = $this->db->get('restaurant_kitchens');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
	}

	public function getAllTablesWithKitchen($warehouse_id, $kitchen_type, $user_id){
		
		$current_date = date('Y-m-d');
		$default_image = site_url('assets/uploads/no_image.png');
		$default_url = site_url('assets/uploads/');
		
        $this->db->select("orders.id, orders.biller_id, restaurant_tables.name as tablename, orders.order_type, orders.order_status,orders.reference_no,users.username,orders.split_id,'order_items' ")
        ->join('kitchen_orders', 'orders.id = kitchen_orders.sale_id AND (kitchen_orders.chef_id = "'.$user_id.'" OR kitchen_orders.chef_id = 0) ')
		->join('order_items', 'order_items.sale_id = orders.id', 'inner')
        ->join('users', 'users.id = kitchen_orders.waiter_id')
        ->join('restaurant_tables', 'restaurant_tables.id = orders.table_id','left')
		->where('order_items.kitchen_type_id', $kitchen_type)
		//->join('sales', 'sales.sales_split_id = orders.split_id', 'inner')
		->where('orders.order_status', 'Open')
		->where('order_items.order_item_cancel_status', 0)
		->where('orders.order_cancel_status', 0)
		
		->where('DATE(date)', $current_date)
		->where_in('order_items.item_status', array('Inprocess','Preparing'))
		->where('orders.warehouse_id', $warehouse_id)
		->group_by('orders.id');
		$t = $this->db->get('orders');
		
      
        if ($t->num_rows() > 0) {

           foreach ($t->result() as $row) {
			   
			   if(!empty($row->tablename)){
				   $row->tablename = $row->tablename;
			   }else{
				   if($row->order_type == 2){
					   $row->tablename = 'Take Away';
				   }elseif($row->order_type == 3){
					   $row->tablename = 'Door Delivery';
				   }
			   }
				$row->kitchen_type = $kitchen_type;
                $data[] = $row;
            }

			
            return $data;
        }
        
        return FALSE;
    }
	
	
	public function getAllTablesWithKitchenItem($warehouse_id, $order_id, $order_type, $kitchen_type, $user_id){
		
		$current_date = date('Y-m-d');
		$default_image = site_url('assets/uploads/no_image.png');
		$default_url = site_url('assets/uploads/');
		
		 $this->db->select("order_items.sale_id,order_items.id, order_items.recipe_id, order_items.recipe_name,order_items.item_status as status,order_items.quantity, order_items.addon_id, order_items.buy_id, order_items.buy_quantity, order_items.get_item, order_items.get_quantity, order_items.total_get_quantity, recipe.image, order_items.variant, order_items.recipe_variant_id")
		   
                ->join('orders', 'order_items.sale_id = orders.id')
				->join('recipe', 'recipe.id = order_items.recipe_id', 'left')
                ->join('kitchen_orders', 'kitchen_orders.sale_id = orders.id')
				->where('order_items.sale_id', $order_id)
				->where('order_items.kitchen_type_id', $kitchen_type)
				->where('order_items.order_item_cancel_status', 0)
				->where('DATE(date)', $current_date)
				->where('orders.warehouse_id', $warehouse_id)
				->where_in('order_items.item_status', array('Inprocess','Preparing'));
				 $s = $this->db->get('order_items');
				 
				 if ($s->num_rows() > 0) {
					  foreach ($s->result() as $sow) {
						  	$sow->type = $order_type ? $order_type : 0;
							$sow->order_id = $order_id;
							
							if($sow->variant == ''){
								$sow->variant =0;
							}
							if(!empty($sow->image)){
								$sow->image = $default_url.$sow->image;
							}else{
								$sow->image = $default_image;
							}
							$addons = $this->site->getAddonByRecipeidAndOrderitemid($sow->recipe_id, $sow->id);
				           $sow->addon = $addons ? $addons : 0;

							/*if($sow->addon_id == 'null'){
								$sow->addon = array();
							}else{								
								$addons = $this->site->getAddonByRecipe($sow->recipe_id, $sow->addon_id);
								$sow->addon = $addons;
							}*/
							$data[] = $sow;       
					  }
					  return $data;
				 }
				 
		 	 return FALSE;
		
    }
	
	public function CancelOrdersItem($notification_array, $remarks, $item_id, $user_id){
		
		$q = $this->db->select('sale_id')->where('id', $item_id)->get('order_items');
		if ($q->num_rows() > 0) {
            $sale_id =  $q->row('sale_id');
        }
		
		$k = $this->db->select('waiter_id, chef_id')->where('sale_id', $sale_id)->get('kitchen_orders');
		if ($k->num_rows() > 0) {
            $waiter_id =  $k->row('waiter_id');
			$chef_id =  $k->row('chef_id');
        }
	
		 $notification_array['insert_array']['role_id'] = 7;
		 $notification_array['insert_array']['to_user_id'] = $chef_id;
		
		
		
		$this->site->create_notification($notification_array);
		
        $order_item_array = array(
            'order_item_cancel_id' => $user_id,
            'order_item_cancel_note' => $remarks,
            'order_item_cancel_status' => 1,
			'item_status' => 'Cancel',
        );
        
		$this->db->where('id', $item_id);
		if ($this->db->update('order_items',  $order_item_array)) {
			return true;
		}
		return false;

    }
	
	public function updateKitchenstatus($notification_array, $status, $order_id, $order_item_id, $current_status, $user_id){
	
	
	$ka = $this->db->select('reference_no, table_id')->where('id', $order_id)->get('orders');
		
	if ($ka->num_rows() > 0) {
		$order_number =  $ka->row('reference_no');
		$table_id =  $ka->row('table_id');
	}
	
	$k = $this->db->select('waiter_id')->where('sale_id', $order_id)->get('kitchen_orders');
	if ($k->num_rows() > 0) {
		$waiter_id =  $k->row('waiter_id');
	}
	
	$notification_array['insert_array']['msg'] = 'The order ['.$order_number.'] item has been '.$current_status.'.';
	$notification_array['insert_array']['to_user_id'] = $waiter_id;
	$notification_array['insert_array']['table_id'] = $table_id;
	
	$this->site->create_notification($notification_array);
	

	//$order_item_id = explode(',', $order_item_id);

	$q = $this->db->where('kitchen_orders.sale_id', $order_id);
	$a = $this->db->update('kitchen_orders', array('chef_id' => $user_id, 'status' => 'Booked'));
	
	$kitchen_array = array(
		'item_status' => $current_status,
	);
	
	if(!empty($order_item_id)){
		foreach($order_item_id as $item_id){
			$this->db->where('id', $item_id);
			$this->db->update('order_items',  $kitchen_array);
		}
		return true;
	}
	return false;
}

	public function getTableOrderCount($order_id)
    {
          
        $this->db->select('orders.id')
         ->join('order_items', 'orders.id=order_items.sale_id');
        $order_count = $this->db->get_where('orders', array('orders.id' => $order_id,'order_items.order_item_cancel_status' => 0));

	
      $this->db->select('orders.id')
         ->join('order_items', 'orders.id=order_items.sale_id');
        $order_closed_count = $this->db->get_where('orders', array('orders.id' => $order_id,'order_items.item_status' => 'Closed','order_items.order_item_cancel_status' => 0));
		
		  
		$order_array = array(
            'order_status' => "Closed",
        );       
        if ($order_count->num_rows()  == $order_closed_count->num_rows()) {
			
			$this->db->where('id', $order_id);
			$o = $this->db->update('orders', $order_array);
					
            return TRUE;
			
        }
        else{
            return FALSE;
        }
        
		
        return FALSE; 
    }

}
