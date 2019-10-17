<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Bbqcustomer_api extends CI_Model
{

    public function __construct() {
        parent::__construct();
    }
	
	public function getBBQData($split_id){
		$this->db->select('*');
		$this->db->where('reference_no', $split_id);
		$q = $this->db->get('bbq');
		if ($q->num_rows() == 1) {
			$data = $q->row();
			return $data;	
		}
		return FALSE;
		
	}
	
	public function checkbilStatus($user_id, $warehouse_id,$table_id){
		$current_date = date('Y-m-d');
		
		$this->db->select('*')->get('pos_settings');
		$q = $this->db->get('bbq');
		if ($q->num_rows() == 1) {
			$billgenerator = $q->row('default_billgenerator');
		}else{
			$billgenerator = 0;	
		}
		if($billgenerator == 1){
			return TRUE;
		}elseif($billgenerator == 0){
			
			$myQuery = "SELECT O.id
			  FROM " . $this->db->dbprefix('orders') . " AS O
			  JOIN " . $this->db->dbprefix('order_items') . " OI ON OI.sale_id = O.id
			WHERE O.table_id ='".$table_id."' AND O.cutomer_id ='".$user_id."' AND O.order_type = 4  AND ((OI.item_status = 'Inprocess') OR(OI.item_status = 'Preparing') OR (OI.item_status = 'Ready')) AND OI.order_item_cancel_status = 0";
			
			$q = $this->db->query($myQuery);
	
			if ($q->num_rows() == 0) {
				return TRUE;
			}
		}
		return FALSE;
	}
	
	public function GetAllBBQdiscount(){
		
		$q = $this->db->get('diccounts_for_bbq');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
	}
	
	public function GetBBQDiscount($bbq_discount_id){
		$this->db->select('*');
		$this->db->where('id', $bbq_discount_id);
		$q = $this->db->get('diccounts_for_bbq');
		if ($q->num_rows() == 1) {
			if($q->row() == 'percentage'){
				$data = $q->row('discount').'%';
			}else{
				$data = $q->row('discount');
			}
			
			return $data;	
		}
		return FALSE;
	}
	
	public function BBQtablesplit($table_id, $split_id){
		$current_date = date('Y-m-d');
		$this->db->select('bbq.*, orders.customer, orders.biller_id, orders.biller ');
		$this->db->join('orders', 'orders.table_id = bbq.table_id AND orders.split_id = bbq.reference_no AND orders.order_type = 4  ');
		$this->db->where('bbq.table_id', $table_id);
		$this->db->where('bbq.reference_no', $split_id);
		$this->db->group_by('bbq.reference_no');
		$q = $this->db->get('bbq');
		if ($q->num_rows() > 0) {
            $data =  $q->row();
			
			return $data;
        }
		return FALSE;	
	}
	
	public function insertBBQ($bbq_array){
				
		if($this->db->insert('bbq', $bbq_array)){
			
			$bbq_id = $this->db->insert_id();
			$this->db->where('bbq.id', $bbq_id);
			$q = $this->db->get('bbq');
			$data = $q->row();
			
			return $data;	
		}
		return FALSE;
	}
	
	public function getBBQdataCode($bbq_code){
		$this->db->select('bbq.*, restaurant_tables.name as table_name');
		$this->db->join('restaurant_tables', 'restaurant_tables.id = bbq.table_id');
		//$this->db->join('orders', 'orders.split_id = bbq.reference_no');
		$this->db->where('bbq.reference_no', $bbq_code);
		$q = $this->db->get('bbq');
		// print_r($this->db->last_query());die;
        if ($q->num_rows() == 1) {
			
            return $q->row();
        }
		return FALSE;
	}
	
	public function updateBBQ($data, $reference_no){
		$this->db->where('reference_no', $reference_no);
        if ($this->db->update('bbq', $data)) {
            return true;
        }
        return false;
	}
	
	public function GetAllorders($user_id, $warehouse_id,$table_id){
		$default_image = site_url('assets/uploads/no_image.png');
		$default_url = site_url('assets/uploads/');
		$current_date = date('Y-m-d');
		
		$this->db->select("restaurant_tables.id AS table_id, restaurant_table_sessions.session_started, orders.split_id, restaurant_tables.name AS table_name, restaurant_tables.max_seats, restaurant_tables.warehouse_id, restaurant_areas.name AS area_name, kitchen_orders.waiter_id, kitchen_orders.chef_id", FALSE)
		->join("restaurant_table_orders", "restaurant_table_orders.table_id = restaurant_tables.id")
		->join("kitchen_orders", "kitchen_orders.sale_id = restaurant_table_orders.order_id")
		->join('orders', 'orders.id = restaurant_table_orders.order_id  AND orders.order_cancel_status = 0 AND  orders.order_status = "Open" ')
		->join("restaurant_areas", "restaurant_areas.id = restaurant_tables.area_id")
		//->join("sales", "sales.sales_split_id != restaurant_table_sessions.split_id", 'left')
		->join("restaurant_table_sessions", "restaurant_table_sessions.customer_id = ".$user_id." ");
		
		$this->db->where('orders.order_status', 'Open');
		$this->db->where('DATE(date)', $current_date);
		$this->db->where("orders.order_type", 4);
		$this->db->where('orders.payment_status', NULL);
		$this->db->where('orders.table_id', $table_id);
		$this->db->where('restaurant_tables.warehouse_id', $warehouse_id);
		$this->db->group_by("restaurant_table_orders.table_id");
		
		$t = $this->db->get('restaurant_tables');		

		if ($t->num_rows() > 0) {
		
			foreach ($t->result() as $row){
				$data[] = $row;
				$i = $this->db->query("SELECT a.reference_no, a.order_type, order_items.*, recipe.preparation_time, CASE WHEN recipe.khmer_name !='' THEN  recipe.khmer_name ELSE recipe.name END AS khmer_name, CASE WHEN recipe.image !='' THEN CONCAT('".$default_url."', recipe.image) ELSE '$default_image' END AS recipe_image FROM " . $this->db->dbprefix('orders') . "  AS a
				LEFT JOIN " . $this->db->dbprefix('order_items') . "  AS order_items ON order_items.sale_id = a.id 
				LEFT JOIN " . $this->db->dbprefix('recipe') . " AS recipe ON recipe.id = order_items.recipe_id 
				WHERE a.customer_id = '".$user_id."' AND a.warehouse_id = ".$warehouse_id." AND  DATE(date) = '".$current_date."' AND order_status = 'Open' AND a.table_id= '".$table_id."' AND a.order_type = 4 ");
				
				if($i->num_rows() > 0){
									
					foreach($i->result() as $item){
						
						$item->timezone = $this->Settings->timezone_gmt;
						//$item->time_limit = date('H:i:s', mktime($item->preparation_time));
						$item->time_limit = gmdate('H:i:s', $item->preparation_time);
						$row->item[] = $item;
					}
					
				}
				
			}
			return $data;
		}
	}
	
	public function BBQaddSale($sale, $sale_items, $bilsdata, $bil_items, $order_id, $split_id, $grand_total){
		
		
		if($this->db->insert('bbq_sales', $sale)){
			$sale_id = $this->db->insert_id();
            
			$this->db->where('split_id', $split_id);
    		$this->db->update('orders', array('order_status' => 'Closed'));
			
			foreach($bilsdata as $key => $bilsrow){
				$bilsrow['sales_id'] = $sale_id;
				$bilsrow['table_whitelisted'] = $this->isTableWhitelisted($sale['sales_table_id']);
				
				$this->db->insert('bbq_bils', $bilsrow);
				$bill_id = $this->db->insert_id();
				$bill_number = $this->site->BBQgenerate_bill_number($bilsrow['table_whitelisted']);
				$this->db->update('bbq_bils', array('bill_number' => $bill_number), array('id' => $bill_id));
				
				foreach ($bil_items[$key]  as $bitems) {
					$bitems['bil_id'] = $bill_id;
					$this->db->insert('bbq_bil_items', $bitems);
				}
			}
			
			foreach ($sale_items as $sitem) {
				$sitem['sale_id'] = $sale_id;
                $this->db->insert('bbq_sale_items', $sitem);
                
            }
			$kitchen_array = array(
			  'item_status' => 'Closed',	    
			  'time_end' => date('Y-m-d H:i:s'),	    
			);
			$this->db->where_in('sale_id', $order_id);
			$this->db->update('order_items',  $kitchen_array);
			
			$bbqstatus_array = array(
			  'payment_status' => 'Open',	
			  'status' => 'Close',	    
			);
			
			$this->db->where_in('reference_no', $split_id);
			$this->db->update('bbq', $bbqstatus_array);
			
			$data['grand_total'] = $grand_total;
			return $data;
			
		}
		return FALSE;
	}
	
	function isTableWhitelisted($tableid){
		$q = $this->db->get_where("restaurant_tables",array('id'=>$tableid,'whitelisted'=>1));
		if ($q->num_rows() > 0) {
			return 1;
		}
		return 0;
    }
	
	public function getBBQorderID($split_id, $order_type){
		$this->db->where('split_id', $split_id);
		$this->db->where('order_type', $order_type);
		$q = $this->db->get('orders');
		if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row->id;
            }
            return $data;
        }
        return FALSE;
	}
    function getCustomerSocketData($tableid){
	$this->db->where('table_id', $tableid);
	//$this->db->limit(1);
	//$this->db->order_by('desc');
	$q = $this->db->get('table_device_detail');
		if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
    }
    }
	
	
}
