<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Orders_api extends CI_Model
{

    public function __construct() {
        parent::__construct();
    }
	
	public function getDiscountdata($customer_id, $waiter_id, $table_id, $split_id){
 		$current_date = date('Y-m-d');
		$this->db->select('*');
		$this->db->where('customer_id', $customer_id);
		$this->db->where('waiter_id', $waiter_id);
		$this->db->where('table_id', $table_id);
		$this->db->where('split_id', $split_id);
		$this->db->where('DATE(created_on)', $current_date);
		$q = $this->db->get('customer_request_discount');
		// $data = $q->num_rows();die;
		// print_r($this->db->last_query());die;
		if ($q->num_rows() == 1) {
			
			if($q->row('customer_type_val') == 'automanual' || $q->row('customer_type_val') == 'customer'){
				$data['dine'] = $q->row('customer_discount_val');
				if($data['dine'] != ''){
					$data['dine'] = $data['dine'];
				}
				else{
					$data['dine'] = "0";
				}
				// var_dump($data['dine']);die;

			}else{
				$data['dine'] = "0";
			}
			if($q->row('bbq_type_val') == 'automanual' || $q->row('bbq_type_val') == 'customer'){
				$data['bbq'] = $q->row('bbq_discount_val');
				if($data['bbq'] != ''){
					$data['bbq'] = $data['bbq'];
				}
				else{
					$data['bbq'] = "0";
				}
			}else{
				$data['bbq'] = "0";
			}
			return $data;
		}
		$data['dine'] = "0";
		$data['bbq'] = "0";
		return $data;	
	}
	public function getPOSSettings(){
		$this->db->select('pos_settings.default_tax, pos_settings.tax_type, tax_rates.name, tax_rates.rate');
		$this->db->join('tax_rates', 'tax_rates.id = pos_settings.default_tax');
		$q = $this->db->get('pos_settings');
		if ($q->num_rows() == 1) {
			$data = $q->row();
			return $data;
		}
		return FALSE;
	}

	public function getServiceChargeSettings(){
		$this->db->select('pos_settings.default_service_charge, pos_settings.service_charge_option, service_charge.name, service_charge.rate');
		$this->db->join('service_charge', 'service_charge.id = pos_settings.default_service_charge');
		$q = $this->db->get('pos_settings');
		if ($q->num_rows() == 1) {
			$data = $q->row();
			return $data;
		}
		return FALSE;
	}
	public function getALLPosSettings(){
		$this->db->select('pos_settings.item_comment_price_option');
		$q = $this->db->get('pos_settings');
		if ($q->num_rows() == 1) {
			$data = $q->row();
			return $data;
		}
		return FALSE;
	}	
	
	public function BBQtablesplit($table_id, $split_id){
		$current_date = date('Y-m-d');
		$current_date = $this->site->getTransactionDate();
		$this->db->select('bbq.*, orders.customer, orders.biller_id, orders.biller ');
		$this->db->join('orders', 'orders.table_id = bbq.table_id AND orders.split_id = bbq.reference_no AND orders.order_type = 4  ');
		if(!empty($table_id)){
			$this->db->where('bbq.table_id', $table_id);
		}		
		$this->db->where('bbq.reference_no', $split_id);
		$this->db->group_by('bbq.reference_no');
		$q = $this->db->get('bbq');			
		if ($q->num_rows() > 0) {
            $data =  $q->row();
			
			return $data;
        }
		return FALSE;	
	}
	public function getSettings(){
		$this->db->select('customer_discount, bbq_discount, default_currency, bbq_enable, bbq_adult_price, bbq_child_price, bbq_kids_price');
		$q = $this->db->get('settings');
		if ($q->num_rows() == 1) {
			
			$data = $q->row();
			if($data->customer_discount == 'customer'){
				$data->customer_discount = 'automanual';
			}
			if($data->bbq_discount == 'bbq'){
				$data->bbq_discount = 'automanual';
			}
			
			return $data;
		}
		
		return FALSE;
	}
	
	public function dinevaluegetBil($table_id, $split_id, $user_id){
		
		
		$this->db->select("orders.id AS order, kitchen_orders.id AS kitchen, 'order_item' AS order_item")
		->join('kitchen_orders', 'kitchen_orders.sale_id = orders.id');
		if(!empty($table_id)){
			$this->db->where('orders.table_id', $table_id);
		}
		$this->db->where('orders.split_id', $split_id);
		$this->db->where('orders.order_type', 1);
		$this->db->where('orders.order_cancel_status', 0);
		//$this->db->where('orders.order_status', 'Closed');
		$q = $this->db->get('orders');		
		if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
				
				
			  $this->db->select("order_items.id,order_items.recipe_id, order_items.recipe_code, order_items.recipe_name, order_items.recipe_type, order_items.option_id, order_items.net_unit_price, order_items.unit_price, order_items.quantity, order_items.warehouse_id, order_items.item_tax, order_items.tax_rate_id, order_items.tax, order_items.discount, order_items.item_discount, order_items.subtotal, order_items.serial_no, order_items.real_unit_price, order_items.sale_item_id, order_items.recipe_unit_id, order_items.recipe_unit_code, order_items.unit_quantity, order_items.comment, order_items.gst, order_items.cgst, order_items.sgst, order_items.igst, order_items.variant, order_items.recipe_variant_id", FALSE);
				$this->db->order_by('order_items.recipe_name', 'ASC');
				$i = $this->db->get_where('order_items', array('order_items.sale_id' => $row->order, 'order_items.kitchen_id' => $row->kitchen,  'order_items.order_item_cancel_status	' => 0));
				
			
				if ($i->num_rows() > 0) {
					foreach (($i->result()) as $iow) {
						$item[$row->order][] = $iow;
					}
					$row->order_item = $item[$row->order];
				}else{
					$row->order_item = array();	
				}
				
                $data['items'][] = $row->order_item;
            }
			
			$this->db->select("orders.*");
			if(!empty($table_id)){
				$this->db->where('orders.table_id', $table_id);
			}
			$this->db->where('orders.split_id', $split_id);
			$this->db->where('orders.order_type', 1);
			$this->db->where('orders.order_cancel_status', 0);
			//$this->db->where('orders.order_status', 'Closed');
			$this->db->group_by('orders.split_id');			
			$o = $this->db->get('orders');
			foreach (($o->result()) as $result) {
				$data['order'][] = $result;
				
			}
            return $data;
        }
        return FALSE;
	}

	public function GetAlldinein($table_id, $user_id, $warehouse_id){
		
		$current_date = date('Y-m-d');
		$current_date = $this->site->getTransactionDate();
		$user_group = $this->site->getUserByID($user_id);
		$gp = $this->site->getGroupPermissions($user_group->group_id);

		$this->db->select("restaurant_tables.id, restaurant_tables.name, restaurant_tables.max_seats, restaurant_tables.warehouse_id, restaurant_areas.name AS area_name, kitchen_orders.waiter_id, 'split_order' ", FALSE)
		->join("restaurant_table_orders", "restaurant_table_orders.table_id = restaurant_tables.id")
		// ->join("kitchen_orders", "kitchen_orders.waiter_id = ".$user_id." AND  kitchen_orders.sale_id = restaurant_table_orders.order_id")
		->join("kitchen_orders", "kitchen_orders.sale_id = restaurant_table_orders.order_id")
		->join('orders', 'orders.id = restaurant_table_orders.order_id  AND orders.order_cancel_status = 0')
		->join("restaurant_areas", "restaurant_areas.id = restaurant_tables.area_id");
		if(!empty($table_id)){
		$this->db->where('restaurant_tables.id', $table_id);
		}
		if($gp->{'pos-view_allusers_orders'} == 0){
		    $this->db->where('kitchen_orders.waiter_id',$user_id);
		}
		$this->db->where('DATE(date)', $current_date);
		$this->db->where("orders.order_type", 1);
		$this->db->where('orders.payment_status', NULL);
		$this->db->where('restaurant_tables.warehouse_id', $warehouse_id);
		$this->db->group_by("restaurant_table_orders.table_id");
		
		$t = $this->db->get('restaurant_tables');		

		if ($t->num_rows() > 0) {
		
			foreach ($t->result() as $row) {
				
				$this->db->select("restaurant_table_sessions.split_id, orders.customer_id, restaurant_table_sessions.table_id ", FALSE)
				->join('orders', 'orders.split_id = restaurant_table_sessions.split_id AND orders.order_cancel_status = 0');
				$this->db->where('restaurant_table_sessions.table_id', $row->id);
				$this->db->where("orders.order_type", 1);
				$this->db->where('orders.payment_status', NULL);
				$this->db->where('DATE(date)', $current_date);
				$this->db->group_by('restaurant_table_sessions.split_id');
				$s = $this->db->get('restaurant_table_sessions');
				
				if ($s->num_rows() > 0) {
					foreach ($s->result() as $sow) {
						$this->db->select("id ");
						$checkbils = $this->db->get_where('sales', array('sales_split_id' => $row->split_id));
						if ($checkbils->num_rows() == 0) {
							$split[$row->id][] = $sow;
						}
				}
					
					$row->split_order = $split[$row->id];
				}else{
					$row->split_order = array();
				}
				
				$data[] = $row;
		
			}
			
			return $data;
		}

		
		return FALSE;
	}
	
	public function GetAllSplit($split, $order_type, $user_id, $warehouse_id){
		$default_image = site_url('assets/uploads/no_image.png');
		$default_url = site_url('assets/uploads/');
		$current_date = date('Y-m-d');
		$current_date = $this->site->getTransactionDate();
		$i = $this->db->query("SELECT a.reference_no, a.order_type, order_items.*, CASE WHEN recipe.image !='' THEN CONCAT('".$default_url."', recipe.image) ELSE '$default_image' END AS recipe_image FROM " . $this->db->dbprefix('orders') . "  AS a
		LEFT JOIN " . $this->db->dbprefix('order_items') . "  AS order_items ON order_items.sale_id = a.id 
		LEFT JOIN " . $this->db->dbprefix('recipe') . " AS recipe ON recipe.id = order_items.recipe_id 
		WHERE a.split_id = '".$split."' AND a.order_type = ".$order_type." AND  DATE(date) = '".$current_date."' ");
		// print_r($this->db->last_query());die;
		if($i->num_rows() > 0){
							
			foreach($i->result() as $item){
				unset($item->igst,$item->sgst,$item->gst,$item->cgst,$item->addon_id,$item->addon_qty);	
				$addons = $this->site->getAddonByRecipeidAndOrderitemid($item->recipe_id, $item->id);
				$item->addon = $addons;
				if($item->order_type == 4){
					$item->highlight_color = 'red';
					$item->highlight_color_id = '1';
				}else{
					$item->highlight_color = 'blue';
					$item->highlight_color_id = '2';
					$grand[] = $item->subtotal;
				}
				$data[] = $item;
			}
			return $data;
		}
		
		return FALSE;	
	}
	
	public function GetAllconsolidated($table_id, $user_id, $warehouse_id){
		
		$current_date = date('Y-m-d');
		//$current_date = $this->site->getTransactionDate();
		
		$user_group = $this->site->getUserByID($user_id);
		$gp = $this->site->getGroupPermissions($user_group->group_id);
	
		$this->db->select("restaurant_tables.id, restaurant_tables.name, restaurant_tables.max_seats, restaurant_tables.warehouse_id, restaurant_areas.name AS area_name, kitchen_orders.waiter_id, 'split_order' ", FALSE)
		->join("restaurant_table_orders", "restaurant_table_orders.table_id = restaurant_tables.id")
		// ->join("kitchen_orders", "kitchen_orders.waiter_id = ".$user_id." AND  kitchen_orders.sale_id = restaurant_table_orders.order_id")
		->join("kitchen_orders", "kitchen_orders.sale_id = restaurant_table_orders.order_id")
		->join('orders', 'orders.id = restaurant_table_orders.order_id  AND orders.order_cancel_status = 0')
		->join("restaurant_areas", "restaurant_areas.id = restaurant_tables.area_id");
		if(!empty($table_id)){
		$this->db->where('restaurant_tables.id', $table_id);
		}
		if($gp->{'pos-view_allusers_orders'} == 0){
		    // $this->db->where('kitchen_orders.waiter_id',$user_id);
		}
		$this->db->where('DATE(date)', $current_date);
		//$this->db->where("orders.order_type", 1);
		$this->db->where('orders.payment_status', NULL);
		$this->db->where('restaurant_tables.warehouse_id', $warehouse_id);
		$this->db->group_by("restaurant_table_orders.table_id");
		
		$t = $this->db->get('restaurant_tables');		
// print_r($this->db->last_query());die;
		if ($t->num_rows() > 0) {
		
			foreach ($t->result() as $row) {
				
				$this->db->select("restaurant_table_sessions.split_id, orders.customer_id, restaurant_table_sessions.table_id ", FALSE)
				->join('orders', 'orders.split_id = restaurant_table_sessions.split_id AND orders.order_cancel_status = 0');
				$this->db->where('restaurant_table_sessions.table_id', $row->id);
				//$this->db->where("orders.order_type", 1);
				$this->db->where('orders.payment_status', NULL);
				$this->db->where('DATE(date)', $current_date);
				$this->db->group_by('restaurant_table_sessions.split_id');
				$s = $this->db->get('restaurant_table_sessions');
				// print_r($this->db->last_query());die;
				if ($s->num_rows() > 0) {
					foreach ($s->result() as $sow) {
						$this->db->select("id ");
						$checkbils = $this->db->get_where('sales', array('sales_split_id' => $row->split_id));
						if ($checkbils->num_rows() == 0) {
							$split[$row->id][] = $sow;
						}
				}
					
					$row->split_order = $split[$row->id];
				}else{
					$row->split_order = array();
				}
				
				$data[] = $row;
		
			}
			$data['grand_total'] = array_sum($grand);
			return $data;
		}

		
		return FALSE;
	}
	
	public function GetAllSplitconsolidated($split, $order_type, $user_id, $warehouse_id){
		
		$default_image = site_url('assets/uploads/no_image.png');
		$default_url = site_url('assets/uploads/');
		$current_date = date('Y-m-d');
		//$current_date = $this->site->getTransactionDate();
		$i = $this->db->query("SELECT a.ordered_by,a.reference_no, a.order_type, order_items.*, CASE WHEN recipe.image !='' THEN CONCAT('".$default_url."', recipe.image) ELSE '$default_image' END AS recipe_image,a.customer_request FROM " . $this->db->dbprefix('orders') . "  AS a
		JOIN " . $this->db->dbprefix('order_items') . "  AS order_items ON order_items.sale_id = a.id  AND order_items.item_status != 'Closed'
		LEFT JOIN " . $this->db->dbprefix('recipe') . " AS recipe ON recipe.id = order_items.recipe_id 
		WHERE a.split_id = '".$split."'  AND  DATE(date) = '".$current_date."' order by order_items.recipe_name ASC");
		// print_r($this->db->last_query());die;
		if($i->num_rows() > 0){
			$k=1;			
			foreach($i->result() as $item){
				unset($item->igst,$item->sgst,$item->gst,$item->cgst,$item->addon_id,$item->addon_qty);	
				$addons = $this->site->getAddonByRecipeidAndOrderitemid($item->recipe_id, $item->id);
				$item->addon = $addons ? $addons :"0";
				if($item->variant == ''){
					$item->variant ="0";
				}
				if($item->order_type == 4){
					$item->highlight_color = 'red';
					$item->highlight_color_id = '1';
					$q = $this->db->select('*')->where('reference_no', $split)->get('bbq', 1);
					if($q->num_rows() > 0){
						$item->grand_total = 0;
						$item->grand_total_cover = ($q->row('number_of_adult') * $q->row('adult_price')) +  ($q->row('number_of_child') * $q->row('child_price')) + ($q->row('number_of_kids') * $q->row('kids_price')); 
					}else{
						$item->grand_total_cover = 0;
						$item->grand_total = 0;
 					}
				}else{
					$item->highlight_color = 'blue';
					$item->highlight_color_id = '2';
					$item->grand_total = $item->subtotal;
				}
				//$item->grand_total = array_sum($grand);
				$data[] = $item;
				$k++;
			}
			
			return $data;
		}
		
		return FALSE;	
	}
	
	function customerRequest($request_discount, $split_id){
		$check = $this->db->select('*')->where('split_id', $split_id)->get('customer_request_discount');
		if($check->num_rows() > 0){
			$this->db->where('split_id', $split_id);
			$q = $this->db->update('customer_request_discount', $request_discount);
		}else{
			
			$q = $this->db->insert('customer_request_discount', $request_discount);
		}
		if($q){
			return true;	
		}
		return false;
	}
	
	
	public function GetAlldoordelivery($user_id, $warehouse_id){
		$default_image = site_url('assets/uploads/no_image.png');
		$default_url = site_url('assets/uploads/');
		$current_date = date('Y-m-d');
		$current_date = $this->site->getTransactionDate();
		$user_group = $this->site->getUserByID($user_id);
		$gp = $this->site->getGroupPermissions($user_group->group_id);

		$this->db->select("orders.split_id , orders.customer_id");
		/* sivan 
		$this->db->join("kitchen_orders", "kitchen_orders.sale_id = restaurant_table_orders.order_id");*/
		$this->db->join("kitchen_orders", "kitchen_orders.sale_id = orders.id");
		$this->db->where("orders.order_type", 3);
		$this->db->where("orders.payment_status", NULL);
		$this->db->where('orders.order_cancel_status', 0);
		$this->db->where('DATE(date)', $current_date);
		$this->db->where('orders.warehouse_id', $warehouse_id);
		if($gp->{'pos-view_allusers_orders'} == 0){
		    $this->db->where('kitchen_orders.waiter_id',$user_id);
		}
		$this->db->group_by('orders.split_id');
		$t = $this->db->get("orders");
		if ($t->num_rows() > 0) {
			foreach($t->result() as $row){
				
				$this->db->select("id ");
				$checkbils = $this->db->get_where('sales', array('sales_split_id' => $row->split_id));
				if ($checkbils->num_rows() == 0) {
						
					$data[] = $row;
					
					
				}
								
			}
			return $data;	
		}
		return FALSE;
	}
	
	
	public function GetAlltakeaway($user_id, $warehouse_id){
		$default_image = site_url('assets/uploads/no_image.png');
		$default_url = site_url('assets/uploads/');
		$current_date = date('Y-m-d');
		$current_date = $this->site->getTransactionDate();
		$user_group = $this->site->getUserByID($user_id);
		$gp = $this->site->getGroupPermissions($user_group->group_id);
		
		$this->db->select("orders.split_id, orders.customer_id");
		$this->db->join("kitchen_orders", "kitchen_orders.sale_id = orders.id");
		$this->db->where("orders.order_type", 2);
		$this->db->where("orders.payment_status", NULL);
		$this->db->where('orders.order_cancel_status', 0);
		$this->db->where('DATE(date)', $current_date);
		$this->db->where('orders.warehouse_id', $warehouse_id);
		if($gp->{'pos-view_allusers_orders'} == 0){
		    $this->db->where('kitchen_orders.waiter_id',$user_id);
		}
		$this->db->group_by('orders.split_id');
		$t = $this->db->get("orders");
		if ($t->num_rows() > 0) {
			foreach($t->result() as $row){
						
				$this->db->select("id ");
				$checkbils = $this->db->get_where('sales', array('sales_split_id' => $row->split_id));
				if ($checkbils->num_rows() == 0) {
					$data[] = $row;
				}
			}
			
			return $data;	
		}
		return FALSE;
	}
//check order is requested for bill from customer side 
	function checkorderRequestforbill($split_id){
		$check = $this->db->select('customer_request')->where('split_id', $split_id)->get('orders');
		$customer_request = $check->row('customer_request');
		return $customer_request;
	}
	
	public function CancelOrdersItem($split_id, $item_id, $remarks, $user_id, $notification_array){
		
		$q = $this->db->select('sale_id')->where('id', $item_id)->get('order_items');
		if ($q->num_rows() > 0) {
            $sale_id =  $q->row('sale_id');
$recipe_id = $q->row('recipe_id');
        }
		
		$k = $this->db->select('waiter_id, chef_id')->where('sale_id', $sale_id)->get('kitchen_orders');
		if ($k->num_rows() > 0) {
            $waiter_id =  $k->row('waiter_id');
			$chef_id =  $k->row('chef_id');
        }
		
		if(!empty($split_id)){
			 $notification_array['insert_array']['role_id'] = 6;
			 $notification_array['insert_array']['to_user_id'] = $waiter_id;
		 }else{
			 $notification_array['insert_array']['role_id'] = 7;
			 $notification_array['insert_array']['to_user_id'] = $chef_id;
		 }
		
		
		$this->site->create_notification($notification_array);
		
		$order_item_array = array(
            'order_item_cancel_id' => $user_id,
            'order_item_cancel_note' => $remarks,
            'order_item_cancel_status' => 1,
			'item_status' => 'Cancel',
        );
        
		$this->db->where('id', $item_id);
		if ($this->db->update('order_items',  $order_item_array)) {
			$order = $this->db->select('order_items.order_item_cancel_status')
			->join('order_items', 'order_items.sale_id = orders.id')
			->where('orders.split_id', $split_id)
			->where('order_items.order_item_cancel_status', 0)
			->get('orders');
			//if($cancel_type=="out_of_stock" || $cancel_type=="kitchen_cancel"){
			//    $this->site->saleStockIn($recipe_id,$cancelQty,$order_item_id);
			//}
			if($order->num_rows() == 0){
				$this->db->where('orders.split_id', $split_id);
				$orderupdate = $this->db->update('orders', array( 'order_cancel_id' => $user_id, 'order_cancel_note' => 'All item order cancel', 'order_cancel_status' => 1));
			}			
			return true;
		}
		return false;
	}
	
	public function updateOrderstatus($status, $order_item_id, $current_status, $user_id, $notification_array){
	
		//$order_item_id = explode(',', $order_item_id);
		
		$this->db->select('orders.reference_no, orders.table_id, order_items.sale_id')
         ->join('orders', 'orders.id = order_items.sale_id', 'left');
        $q = $this->db->get_where('order_items', array('order_items.id' => $order_item_id[0]));
		
		if ($q->num_rows() > 0) {
            $order_number =  $q->row('reference_no');
			$table_id =  $q->row('table_id');
			$sale_id =  $q->row('sale_id');
        }
		
		$k = $this->db->select('chef_id')->where('sale_id', $sale_id)->get('kitchen_orders');
		if ($k->num_rows() > 0) {
            $chef_id =  $k->row('chef_id');
        }
		
		$notification_array['insert_array']['msg'] = 'The order ['.$order_number.'] item has been '.$current_status.'.';
		$notification_array['insert_array']['to_user_id'] = $chef_id;
		$notification_array['insert_array']['table_id'] = $table_id;
		
		$this->site->create_notification($notification_array);
		
        $order_item_array = array(
            'item_status' => $current_status,
        );

        $order_itemendtime = array(
        	'time_end' => date('Y-m-d H:m:s'),
        );
		
		if($current_status = 'Served')
		{
		foreach($order_item_id as $item_id){
				$this->db->where('id', $item_id);
				$this->db->update('order_items',  $order_itemendtime);
			}
		}	
		
		if(!empty($order_item_id)){
			foreach($order_item_id as $item_id){
				$this->db->where('id', $item_id);
				$this->db->update('order_items',  $order_item_array);
			}
			return true;
		}


        return false;
    }
	
	function isTableWhitelisted($tableid){
		$q = $this->db->get_where("restaurant_tables",array('id'=>$tableid,'whitelisted'=>1));
		if ($q->num_rows() > 0) {
			return 1;
		}
		return 0;
    }
	
	 public function InsertBill($order_data_dine = array(), $order_item_dine = array(), $billData_dine = array(), $splitData_dine = array(), $sales_total = NULL, $delivery_person = NULL,$timelog_array = NULL, $notification_array = array(),$order_item_id =array())
    {		
		
    	/*echo "<pre>";
    	print_r($splitData_dine);die;*/
    	$sales_array = array(
		            'grand_total' => $sales_total,
					'delivery_person_id' => $delivery_person
		        );
		
		//$this->site->create_notification($notification_array);
    	foreach ($timelog_array as $time) {
              	$res = $this->db->insert('time_log', $time);
        }      	
    	
		
		
        if ($this->db->insert('sales', $order_data_dine)) {
			
            $sale_id = $this->db->insert_id();
            
            $this->db->update('sales', $sales_array, array('id' => $sale_id));

              foreach ($billData_dine as $key =>  $bills) {				
				$bills['sales_id'] = $sale_id;			

				$bills['table_whitelisted'] = $this->isTableWhitelisted($order_data['sales_table_id']);
				$this->db->insert('bils', $bills);
				// print_r($this->db->error());die;
				$bill_id = $this->db->insert_id();
				//$bill_number = sprintf("%'.05d", $bill_id);
				$bill_number = $this->site->generate_bill_number(0);
              	$this->db->update('bils', array('bill_number' => $bill_number,'bill_sequence_number' => $bill_id), array('id' => $bill_id));
				$this->site->latest_bill($bill_number); 

				foreach ($splitData_dine[$key]  as $bill_items) {
					$bill_items['bil_id'] = $bill_id;
					$this->db->insert('bil_items', $bill_items);
					// echo "string";
					  // print_r($this->db->error());
						$bill_item_id = $this->db->insert_id();

					/*addon array*/
					    $addonid = $bill_items['addon_id'] ? $bill_items['addon_id'] :0; 
						$recipe_addon_item =[];
						$someArray = json_decode($addonid, true);
						if($someArray !='') :				
							foreach ($someArray as  $split) {

	                            $AddonDetails = $this->site->getaddonitemid($split['id']);
								$recipeDetails = $this->site->getrecipeByID($AddonDetails->addon_item_id);

								$recipe_addon_item[] = array(									
									'bill_id'      => $bill_id,
									'sale_item_id'      => $recipeDetails->id ? $recipeDetails->id :0,
									'bill_item_id'      => $bill_item_id,
									'addon_id'      => $split['id'] ? $split['id'] : 0,
									'price'      => $recipeDetails->cost ? $recipeDetails->cost : 0,
									'qty'      => $split['qty'],
									'subtotal'      => ($split['qty'] * $recipeDetails->cost),
									);
							}	
							/*echo "<pre>";	
							print_r($recipe_addon_item);die;*/
							foreach ($recipe_addon_item as $recipe_addon) {
								$this->db->insert('addon_bill_items', $recipe_addon);
							}	
							//print_r($this->db->error());	
												
						endif;

					 	/*if($addonArray !='') :				
							foreach ($addonArray as  $split) {

								$AddonDetails = $this->site->getaddonitemid($split['id']);
								$recipeDetails = $this->pos_model->getrecipeByID($AddonDetails->addon_item_id);
								$recipe_addon_item[] = array(									
									'bill_id'      => $bill_id,
									'bill_item_id'      => $bill_item_id,
									'sale_item_id'      => $recipeDetails->id,
									'addon_id'      => $split['id'],
									'price'      => $recipeDetails->cost ? $recipeDetails->cost : 0,
									'qty'      => $addonqty[$key],
									'subtotal'      => ($addonqty[$key] * $recipeDetails->cost),
								);
							}
							
							foreach ($recipe_addon_item as $recipe_addon) {
								$this->db->insert('addon_bill_items', $recipe_addon);
							}
						endif;	*/
						/*addon array*/
				}
              }

            foreach ($order_item_dine as $item) {
                $this->db->insert('sale_items', $item);
                $sale_item_id = $this->db->insert_id();
                $this->db->update('sale_items', array('sale_id' =>  $sale_id), array('id' => $sale_item_id));

            }
			$kitchen_array = array(
                      'item_status' => 'Closed',	    
                      'time_end' => date('Y-m-d H:i:s'),	    
			        );
				
			if(!empty($order_item_id)){
						foreach($order_item_id as $item_id){
							$this->db->where('order_item_cancel_status', 0);
							$this->db->where('order_item_cancel_status', 'Inprocess');
							$this->db->where('id', $item_id);
							$this->db->update('order_items',  $kitchen_array);
						}
						
					}
           
            if ($order_data_dine['sale_status'] == 'completed') {
            }
                 // print_r($this->db->error());die;   
            return true;
        }//print_r($this->db->error());die;
        return false;
    } 
	
	public function BBQaddSale($notification_array, $timelog_array, $order_data_bbq, $splitData_bbq, $saleorder_item, $sale, $sale_items, $bilsdata_bbq, $bil_items, $bbq_order_id, $bbq_array, $splits){
	
	
		$this->site->create_notification($notification_array);
		if(!empty($timelog_array)){
			foreach ($timelog_array as $time) {
					$res = $this->db->insert('time_log', $time);
			}      	
		}
		
		
		if($this->db->insert('sales', $sale)){
			$sale_id = $this->db->insert_id();
			
            $this->db->update('sales', $sales_array, array('id' => $sale_id));
			
			foreach($bilsdata_bbq as $key => $bilsrow){
				
				$bilsrow['sales_id'] = $sale_id;
				
				$bilsrow['table_whitelisted'] = $this->isTableWhitelisted($sale['sales_table_id']);
				
				$this->db->insert('bils', $bilsrow);
				$bill_id = $this->db->insert_id();
				$bill_no = $this->site->CheckConsolidate($splits);			
				
				if($bill_no){					
					$bill_number = $bill_no;
				}else{					
					$bill_number = $this->site->BBQgenerate_bill_number($bilsrow['table_whitelisted']);
				}
				//$bill_number = $this->site->BBQgenerate_bill_number($bilsrow['table_whitelisted']);
				$this->db->update('bils', array('bill_number' => $bill_number,'bill_sequence_number' => $bill_id), array('id' => $bill_id));
				$this->site->latest_bill($bill_number);
				foreach ($bil_items[$key]  as $bitems) {
					$bitems['bil_id'] = $bill_id;
					$this->db->insert('bbq_bil_items', $bitems);
				}
				foreach ($splitData_bbq[$key]  as $bill_items) {
					$bill_items['bil_id'] = $bill_id;
					$this->db->insert('bil_items', $bill_items);
				}
			}
			
			foreach ($sale_items as $sitem) {
				$sitem['sale_id'] = $sale_id;
                $this->db->insert('bbq_sale_items', $sitem);
                
            }
			$k=0;
			foreach ($saleorder_item as $items) {
				
				//foreach($items as $row){
					
					$this->db->insert('sale_items', $items);
					$sale_item_id = $this->db->insert_id();
					$this->db->update('sale_items', array('sale_id' =>  $sale_id), array('id' => $sale_item_id));
				//}
				
            }
			
			$kitchen_array = array(
			  'item_status' => 'Closed',	    
			  'time_end' => date('Y-m-d H:i:s'),	    
			);
			
							
			$this->db->where_in('sale_id', $bbq_order_id);
			$this->db->update('order_items',  $kitchen_array);
			
			
				
		if(!empty($bbq_array)){
				$this->db->where('reference_no', $splits);
				$this->db->update('bbq',  $bbq_array);
			}
			
			return TRUE;
			
		}
		
		return FALSE;
	}
	
	public function bildinegetBil($table_id, $split_id, $user_id){
		
		
		$this->db->select("orders.id AS order, kitchen_orders.id AS kitchen, 'order_item' AS order_item")
		->join('kitchen_orders', 'kitchen_orders.sale_id = orders.id');
		if(!empty($table_id)){
			$this->db->where('orders.table_id', $table_id);
		}
		$this->db->where('orders.split_id', $split_id);
		$this->db->where('orders.order_type', 1);
		$this->db->where('orders.order_cancel_status', 0);
		//$this->db->where('orders.order_status', 'Closed');
		
		$q = $this->db->get('orders');
		
		if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
				
				
			  $this->db->select("order_items.id,order_items.recipe_id, order_items.recipe_code, order_items.recipe_name, order_items.recipe_type, order_items.option_id, order_items.net_unit_price, order_items.unit_price, order_items.quantity, order_items.warehouse_id, order_items.item_tax, order_items.tax_rate_id, order_items.tax, order_items.discount, order_items.item_discount, order_items.subtotal, order_items.serial_no, order_items.real_unit_price, order_items.sale_item_id, order_items.recipe_unit_id, order_items.recipe_unit_code, order_items.unit_quantity, order_items.comment, order_items.gst, order_items.cgst, order_items.sgst, order_items.igst", FALSE);
				$i = $this->db->get_where('order_items', array('order_items.sale_id' => $row->order, 'order_items.kitchen_id' => $row->kitchen,  'order_items.order_item_cancel_status	' => 0));
				
			
				if ($i->num_rows() > 0) {
					foreach (($i->result()) as $iow) {
						$item[$row->order][] = $iow;
					}
					$row->order_item = $item[$row->order];
				}else{
					$row->order_item = array();	
				}
				
                $data['items'][] = $row->order_item;
            }
			
			$this->db->select("orders.*");
			if(!empty($table_id)){
				$this->db->where('orders.table_id', $table_id);
			}
			$this->db->where('orders.order_type', 1);
			$this->db->where('orders.split_id', $split_id);
			$this->db->where('orders.order_cancel_status', 0);
			//$this->db->where('orders.order_status', 'Closed');
			$this->db->group_by('orders.split_id');			
			$o = $this->db->get('orders');
			foreach (($o->result()) as $result) {
				$data['order'][] = $result;
				
			}
            return $data;
        }
        return FALSE;
	}
	
	public function BBQgetBil($table_id, $split_id, $user_id){
		
		
		$this->db->select("orders.id AS order, kitchen_orders.id AS kitchen, 'order_item' AS order_item")
		->join('kitchen_orders', 'kitchen_orders.sale_id = orders.id');
		if(!empty($table_id)){
			$this->db->where('orders.table_id', $table_id);
		}
		$this->db->where('orders.split_id', $split_id);
		$this->db->where('orders.order_type', 4);
		$this->db->where('orders.order_cancel_status', 0);
		//$this->db->where('orders.order_status', 'Closed');
		
		$q = $this->db->get('orders');
		
		if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
				
				
			  $this->db->select("order_items.id,order_items.recipe_id, order_items.recipe_code, order_items.recipe_name, order_items.recipe_type, order_items.option_id, order_items.net_unit_price, order_items.unit_price, order_items.quantity, order_items.warehouse_id, order_items.item_tax, order_items.tax_rate_id, order_items.tax, order_items.discount, order_items.item_discount, order_items.subtotal, order_items.serial_no, order_items.real_unit_price, order_items.sale_item_id, order_items.recipe_unit_id, order_items.recipe_unit_code, order_items.unit_quantity, order_items.comment, order_items.gst, order_items.cgst, order_items.sgst, order_items.igst", FALSE);
				$i = $this->db->get_where('order_items', array('order_items.sale_id' => $row->order, 'order_items.kitchen_id' => $row->kitchen,  'order_items.order_item_cancel_status	' => 0));
				
			
				if ($i->num_rows() > 0) {
					foreach (($i->result()) as $iow) {
						$item[$row->order][] = $iow;
					}
					$row->order_item = $item[$row->order];
				}else{
					$row->order_item = array();	
				}
				
                $data['items'][] = $row->order_item;
            }
			
			$this->db->select("orders.*");
			if(!empty($table_id)){
				$this->db->where('orders.table_id', $table_id);
			}
			$this->db->where('orders.order_type', 4);
			$this->db->where('orders.split_id', $split_id);
			$this->db->where('orders.order_cancel_status', 0);
			//$this->db->where('orders.order_status', 'Closed');
			$this->db->group_by('orders.split_id');			
			$o = $this->db->get('orders');
			foreach (($o->result()) as $result) {
				$data['order'][] = $result;
				
			}
            return $data;
        }
        return FALSE;
	}

	public function getUserByID($id)
    {
        $q = $this->db->get_where('users', array('id' => $id, 'active' => 1), 1);        
        if ($q->num_rows() > 0) {        	
            return $q->row();
        }
        return FALSE;
    }
	
	public function getBBQorderID($split_id){
		$this->db->where('split_id', $split_id);
		$q = $this->db->get('orders');
		if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row->id;
            }
            return $data;
        }
        return FALSE;
	}
	
	function getCategory_cusDiscount($groupid,$discountid){
	$q = $this->db
	    ->select('GD.discount_val')
	    ->from('diccounts_for_customer D')
	    ->join('group_discount GD','GD.cus_discount_id=D.id and GD.recipe_group_id='.$groupid)
	    ->where('D.id',$discountid)
	    ->get();
	$res = $q->row();
	return ($q->num_rows()>0)?$res->discount_val:false;
    }
	
	function recipe_customer_discount_calculation($customer_type_val, $itemid,$groupid,$finalAmt,$discountid){
	//echo $itemid.'-'.$groupid.'-'.$finalAmt.'-'.$discountid;
	if($customer_type_val=="automanual"){
	    $discount  = $this->getCategory_cusDiscount($groupid,$discountid);
	    if($discount){
		return $discountAmt = $finalAmt*($discount/100);
		
	    }
	}else if($customer_type_val=="manual"){//manual
	    $discount_value = $discountid;
	    return $discountAmt = $this->site->calculateDiscount($discount_value, $finalAmt);
	}
	return 0;
    }
	
	public function getrecipeByID($id)
    {

        $q = $this->db->get_where('recipe', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return FALSE;
    }
	
	public function customergetBil($table_id, $split_id, $user_id){
		$this->db->select("orders.id AS order, kitchen_orders.id AS kitchen, 'order_item' AS order_item")
		->join('kitchen_orders', 'kitchen_orders.sale_id = orders.id');
		if(!empty($table_id)){
			$this->db->where('orders.table_id', $table_id);
		}
		$this->db->where('orders.split_id', $split_id);
		$this->db->where('orders.order_type', 1);
		$this->db->where('orders.order_cancel_status', 0);
		//$this->db->where('orders.order_status', 'Closed');
		
		$q = $this->db->get('orders');
		
		if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
				
				
			  $this->db->select("order_items.id,order_items.recipe_id, recipe.category_id, recipe.subcategory_id, order_items.recipe_code, order_items.recipe_name, order_items.recipe_type, order_items.option_id, order_items.net_unit_price, order_items.unit_price, order_items.quantity, order_items.warehouse_id, order_items.item_tax, order_items.tax_rate_id, order_items.tax, order_items.discount, order_items.item_discount, order_items.subtotal, order_items.serial_no, order_items.real_unit_price, order_items.sale_item_id, order_items.recipe_unit_id, order_items.recipe_unit_code, order_items.unit_quantity, order_items.comment, order_items.gst, order_items.cgst, order_items.sgst, order_items.igst,recipe.cost", FALSE);
			   	$this->db->join('recipe', 'recipe.id = order_items.recipe_id');
			  	$this->db->where('order_items.sale_id', $row->order);
				$this->db->where('order_items.kitchen_id', $row->kitchen);
				$this->db->where('order_items.order_item_cancel_status', 0);
				$i = $this->db->get('order_items');
				
			
				if ($i->num_rows() > 0) {
					foreach (($i->result()) as $iow) {
						$item[$row->order][] = $iow;
					}
					$row->order_item = $item[$row->order];
				}else{
					$row->order_item = array();	
				}
				
                $data['items'][] = $row->order_item;
            }
			
			
            return $data;
        }
        return FALSE;
		
	}
	
	/*20-09-2018 because not incorporated based on the back end setting */
    function recipe_customer_discount_calculation_api($itemid,$groupid,$subgroup_id,$finalAmt,$discountid){
	//echo $itemid.'-'.$groupid.'-'.$finalAmt.'-'.$discountid;
	if($this->Settings->customer_discount=="customer"){ 
	    //$discount  = $this->getCategory_GroupDiscount($groupid,$discountid);
	    //echo $groupid.'-'.$subgroup_id.'-'.$itemid.'-'.$discountid;
	    $discount = $this->getCategory_GroupDiscount($groupid,$subgroup_id,$itemid,$discountid);
	    if(isset($discount['discount_val']) && $discount['discount_val']!=''){
		$dis_val = $discount['discount_val'];
		if($discount['discount_type']=="percentage"){
		    return $discountAmt = $finalAmt*($dis_val/100);
		}else if($discount['discount_type']=="amount"){
		    if($dis_val<$finalAmt){ return $dis_val;}else{return $finalAmt;}
		}
		
		
	    }
	}else if($this->Settings->customer_discount=="manual"){//manual
	    $discount_value = $discountid;
	    return $discountAmt = $this->site->calculateDiscount($discount_value, $finalAmt);
	}
	return 0;
    }
    function getCategory_GroupDiscount($groupid,$subgroup_id,$itemid,$discountid){
	$today = date('Y-m-d');
	$curtime  = date('H:i').':00';
	$q = $this->db
	    ->select('GD.discount_val,GD.discount_type,GD.recipe_id,GD.type')
	    ->from('diccounts_for_customer D')
	    ->join('group_discount GD','GD.cus_discount_id=D.id and GD.recipe_group_id='.$groupid)
	    ->where('D.id',$discountid)
	    ->where('D.status',1)
	    ->where('GD.status',1)
	    ->where('GD.recipe_subgroup_id',$subgroup_id)
	    ->where('DATE(D.from_date) <=', $today)
	    ->where('DATE(D.to_date) >=', $today)
	    
	    ->where('TIME(D.from_time) <=', $curtime)
	    ->where('TIME(D.to_time) >=', $curtime)
	    //->where('GD.type','included')
	    ->get();
	    // print_r($this->db->last_query());die;
	if($q->num_rows()>0) {
	    $res = $q->result();
	    foreach($res as $k => $row){ 
		$recipe_id_days = unserialize($row->recipe_id);
		$return['discount_val'] = $row->discount_val;
		$return['discount_type'] = $row->discount_type;
		if(isset($recipe_id_days[$itemid]) && $row->type=="included") {
		    
		    $today = strtolower(date('D'));
		    $days = unserialize($recipe_id_days[$itemid]['days']);
		   
		    if(isset($days[$today])){
			
			return $return;
		    }		    
		    return false;
		}else if(!isset($recipe_id_days[$itemid]) && $row->type=="excluded"){
		    
		    return $return;
		}else if(isset($recipe_id_days[$itemid]) && $row->type=="excluded"){
		   
		    return false;
		}		
		else{
		    return false;
		}
	    }
	}
	return false;
    }

	public function getBil($table_id, $split_id, $user_id){
		
		
		$this->db->select("orders.id AS order, kitchen_orders.id AS kitchen, 'order_item' AS order_item")
		->join('kitchen_orders', 'kitchen_orders.sale_id = orders.id');
		if(!empty($table_id)){
			$this->db->where('orders.table_id', $table_id);
		}
		$this->db->where('orders.split_id', $split_id);
		$this->db->where('orders.order_cancel_status', 0);
		//$this->db->where('orders.order_status', 'Closed');
		
		$q = $this->db->get('orders');
		
		if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
				
				
				$this->db->select("order_items.recipe_id, order_items.recipe_code, order_items.recipe_name, order_items.recipe_type, order_items.option_id, order_items.net_unit_price, order_items.unit_price, order_items.quantity, order_items.warehouse_id, order_items.item_tax, order_items.tax_rate_id, order_items.tax, order_items.discount, order_items.item_discount, order_items.subtotal, order_items.serial_no, order_items.real_unit_price, order_items.sale_item_id, order_items.recipe_unit_id, order_items.recipe_unit_code, order_items.unit_quantity, order_items.comment, order_items.gst, order_items.cgst, order_items.sgst, order_items.igst", FALSE);
				$i = $this->db->get_where('order_items', array('order_items.sale_id' => $row->order, 'order_items.kitchen_id' => $row->kitchen,  'order_items.order_item_cancel_status	' => 0));
				
			
				if ($i->num_rows() > 0) {
					foreach (($i->result()) as $iow) {
						$item[] = $iow;
					}
					$row->order_item = $item;
				}else{
					$row->order_item = array();	
				}
				
                $data['items'] = $row->order_item;
            }
			
			$this->db->select("orders.*");
			if(!empty($table_id)){
				$this->db->where('orders.table_id', $table_id);
			}
			$this->db->where('orders.split_id', $split_id);
			$this->db->where('orders.order_cancel_status', 0);
			//$this->db->where('orders.order_status', 'Closed');
			$this->db->group_by('orders.split_id');			
			$o = $this->db->get('orders');
			foreach (($o->result()) as $result) {
				$data['order'][] = $result;
				
			}
			
			
            return $data;
        }
        return FALSE;
	}
    
    public function gettableidbysplitid($split_id){
    	$this->db->select('table_id');
    	$this->db->where('split_id',$split_id);
    	$q =$this->db->get('orders');
    	if($q->num_rows() > 0){
    		$data =$q->row('table_id');
    		return $data;
    	}return 0;
    }
}
