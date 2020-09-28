<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Site extends CI_Model{
    public function __construct() {
        parent::__construct();
	$this->load->library('ion_auth');
    }
    public function get_total_qty_alerts() {
        /*$this->db->where('quantity < alert_quantity', NULL, FALSE)->where('track_quantity', 1);
        return $this->db->count_all_results('products');*/
        $this->db->select('srampos_recipe.minimum_quantity');
		$this->db->join('pro_stock_master st','st.product_id = recipe.id');						
		$this->db->group_by('srampos_recipe.id HAVING sum(st.stock_in-st.stock_out) <= srampos_recipe.minimum_quantity',FALSE);	
		return $this->db->count_all_results('recipe');
    }
    public function get_expiring_qty_alerts() {
			$date = date('Y-m-d', strtotime('+3 months'));
			$this->db->select(" sum(st.stock_in-st.stock_out) as alert_num, ");
			$this->db->from('recipe');
			$this->db->join('pro_stock_master st', 'recipe.id=st.product_id', 'left');
			$this->db->join('warehouses', 'warehouses.id=st.store_id', 'left');           
			$this->db->where('expiry_date !=', NULL)->where('expiry_date !=', '0000-00-00');
			$this->db->where('expiry <', $date);
			$q = $this->db->get();			
			if ($q->num_rows() > 0) {
			$res = $q->row();
			return (INT) $res->alert_num;
			}
			return FALSE;
      /*  $this->db->select('SUM(quantity_balance) as alert_num')
        ->where('expiry !=', NULL)->where('expiry !=', '0000-00-00')
        ->where('expiry <', $date);
        $q = $this->db->get('purchase_items');
        if ($q->num_rows() > 0) {
            $res = $q->row();
            return (INT) $res->alert_num;
        }
        return FALSE;*/
    }


 /*function send_Demo(){
	$this->load->library('socketemitter');
	$push_notify['title'] = 'BBQ Covers validation request - BBQ20190928124538014';
	$push_notify['msg'] = 'TABLE 18 - Customer has sent BBQ Covers.';
	$push_notify['type'] = 'bbq_cover_validation';
	$push_notify['socket_id'] = '3FmgtQxxIblH-d-4AAAL';
	$push_notify['bbq_code'] = 'BBQ20190928135244014';
	$push_notify['notify_id'] = 50;
	$push_notify['table_id'] = 10;
	$push_notify['stop_count'] = 1;
	$event = 'bbq_cover_validation_stop';
	$edata = $push_notify;
	$this->socketemitter->setEmit($event, $edata);
    }
	
	
 function send_Demo1(){
	$this->load->library('socketemitter');
	$push_notify['title'] = 'BBQ Covers validation request - BBQ20190928124538014';
	$push_notify['msg'] = 'TABLE 18 - Customer has sent BBQ Covers.';
	$push_notify['type'] = 'bbq_cover_validation';
	$push_notify['socket_id'] = '3FmgtQxxIblH-d-4AAAL';
	$push_notify['bbq_code'] = 'BBQ20190928135244014';
	$push_notify['notify_id'] = 50;
	$push_notify['table_id'] = 10;
	$push_notify['stop_count'] = 0;
	$event = 'bbq_cover_validation';
	$edata = $push_notify;
	$this->socketemitter->setEmit($event, $edata);
    }*/
	/*BBQ*/
	public function getBBQbuyxgetxDAYS($days){
		$this->db->where('days', $days);
		$q = $this->db->get('bbq_buyx_getx');
        if ($q->num_rows() == 1) {
            return $q->row();
        }
		return FALSE;
	}
	public function getBBQlobsterDAYS($days){
		$this->db->where('days', $days);
		$q = $this->db->get('bbq_lobster_discount');				
        if ($q->num_rows() == 1) {
            return $q->row();
        }
		return FALSE;
	}
	public function getBBQDAYwiseDiscount($days){
		$this->db->where('days', $days);
		$q = $this->db->get('bbq_daywise_discount');				
        if ($q->num_rows() == 1) {
            return $q->row();
        }
		return FALSE;
	}	

	public function getBBQDaywiseDiscountforpos($bbq_menu_id){	
		$mydate=getdate(date("U"));
        $day = "$mydate[weekday]";
		$this->db->select("bbqdd.*");		
		$this->db->join('bbq_daywise_discount bbqdd', 'bbqdd.bbq_daywise_discount_hd_id = bbq_daywise_discount_hd.id');
		$this->db->where('bbq_daywise_discount_hd.bbq_menu_id', $bbq_menu_id);					
		$this->db->where('bbqdd.days', $day);					
		$this->db->where('bbqdd.status', 1);					
		$q = $this->db->get('bbq_daywise_discount_hd');		
		// print_r($this->db->last_query());die;	
		if($q->num_rows()>0){
		    return $q->row();
		}
	}
	public function getBBQDaywiseDiscountbyidandday($bbq_menu_id,$day){			
		$this->db->select("bbqdd.*");		
		$this->db->join('bbq_daywise_discount bbqdd', 'bbqdd.bbq_daywise_discount_hd_id = bbq_daywise_discount_hd.id');
		$this->db->where('bbq_daywise_discount_hd.id', $bbq_menu_id);					
		$this->db->where('bbqdd.days', $day);					
		$q = $this->db->get('bbq_daywise_discount_hd');		
		// print_r($this->db->last_query());die;	
		if($q->num_rows()>0){
		    return $q->row();
		}
	}

	public function getCustomerDetails($waiter_id, $table_id, $split_id){
		$this->db->select('*');
		$this->db->where('table_id', $table_id);
		$this->db->where('split_id', $split_id);
		$this->db->where('created_by', $waiter_id);
		$this->db->group_by('orders.split_id');
		$q = $this->db->get('orders');
		if ($q->num_rows() == 1) {
            return $q->row('customer_id');
        }
		return FALSE;
	}
	public function CalculationBBQbuyget($buy, $get, $total_number){
		
		$paid = 0;
		if(!empty($buy) && !empty($get)){
			
			$quotient = (int)($total_number / $buy);
			$paid = ($get * $quotient);
			
			return $paid;
		}
		return $paid;
	}
	public function CalculationBBQlobster($number_of_adult, $price, $discount_apply_type, $discount_type, $discount_val){		
			if($discount_apply_type == 'EVEN')
			{
				$remainder = $number_of_adult % 2;
				if($remainder != 0)
				{
					$discountcovers = $number_of_adult-$remainder;
				}else{
					$discountcovers = $number_of_adult;
				}
			}else{				
				$remainder = $number_of_adult % 2;
				if($number_of_adult != 0) :
					if($remainder == 0)
					{  
						$discountcovers = $number_of_adult-1;
					}else{
						$discountcovers = $number_of_adult;
					}
				endif;					 
			}
			$disamt = 0;
			if($discount_type =='percentage'){			
			      $amount = $discountcovers * $price;
			      $disamt = $this->sma->formatDecimal(((($this->sma->formatDecimal($amount)) * (Float) ($discount_val)) / 100));
			}else{				
				$disamt = $this->sma->formatDecimal($discountcovers *$discount_val);	
			}
			 return $disamt;
			
	}

	public function CalculationBBQDauwiseDiscount($amount,$discount_type, $discount_val){					
			$disamt = 0;
			if($discount_type =='percentage'){						      
			      $disamt = $this->sma->formatDecimal(((($this->sma->formatDecimal($amount)) * (Float) ($discount_val)) / 100));
			}else{				
				$disamt = $this->sma->formatDecimal($discount_val);	
			}
			 return $disamt;
			
	}

	public function getBBQdataCode($reference_no){
		$this->db->select('bbq.*, restaurant_tables.name as table_name');
		$this->db->join('restaurant_tables', 'restaurant_tables.id = bbq.table_id');
		$this->db->where('bbq.reference_no', $reference_no);
		$q = $this->db->get('bbq');
        if ($q->num_rows() == 1) {
			
            return $q->row();
        }
		return FALSE;
	}
	public function splitBBQCheckSalestable($split_id){
		$q = $this->db->get_where('sales', array('sales_split_id' => $split_id), 1);
        if ($q->num_rows() == 1) {
            return TRUE;
        }
        return FALSE;
	}
	public function BBQcheckTable($val){
		$current_date = date('Y-m-d');
		$current_date = $this->getTransactionDate();
		$this->db->where('table_id', $val);
		$this->db->where('payment_status', '');
		$this->db->where('cancel_status', 0);
		//$this->db->where('created_on', $current_date);
		$q = $this->db->get('bbq');
		if ($q->num_rows() > 0) {
            return $q->row();
        }
		return FALSE;	
	}
	public function ordertypeTables($table_id){
		
		$current_date = date('Y-m-d');
		$this->db->where('table_id', $table_id);
		$this->db->where('order_status', 'Open');
		$this->db->where('DATE(date)', $current_date);
		$this->db->group_by('table_id');
		$this->db->order_by('id', 'DESC');
		$q = $this->db->get('orders');
		if ($q->num_rows() > 0) {
			
            return $q->row('order_type');
        }
		return FALSE;	
	}
	public function BBQcheckorders($table_id, $split_id, $customer_id){
		
		$this->db->where('bbq.reference_no', $split_id);
		$this->db->where('bbq.table_id', $table_id);
		$this->db->where('bbq.customer_id', $customer_id);
		$this->db->where('bbq.status', 'Open');
		$this->db->where('bbq.cancel_status', 0);
		$q = $this->db->get('bbq');
        if ($q->num_rows() == 1) {
			
            return TRUE;
        }
		return FALSE;
	}
	public function orderBBQTablecheck($val){
		$current_date = date('Y-m-d');
		$main = "SELECT KO.waiter_id
					FROM " . $this->db->dbprefix('restaurant_table_orders') . " AS RTO
					JOIN " . $this->db->dbprefix('kitchen_orders') . " KO ON KO.sale_id = RTO.order_id
					JOIN " . $this->db->dbprefix('orders') . " O ON O.id = RTO.order_id AND O.order_cancel_status = 0
					WHERE RTO.table_id='".$val."' AND O.payment_status is null GROUP BY RTO.table_id";
//DATE(O.date) ='".$current_date."'  AND 
		$q = $this->db->query($main);
		if ($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				if($this->session->userdata('user_id') == $row->waiter_id){
					$splits = "SELECT O.split_id
					FROM " . $this->db->dbprefix('orders') . " AS O
					JOIN " . $this->db->dbprefix('restaurant_table_orders') . " T ON T.order_id = O.id
					WHERE T.table_id ='".$val."'  ORDER BY O.id DESC ";

					$s = $this->db->query($splits);
					
			        if ($s->num_rows() > 0) {
			            $spt = $s->row();
			            $split = $spt->split_id;
			        }

					$myQuery = "SELECT (CASE
					        WHEN ((OI.item_status = 'Inprocess') OR (OI.item_status = 'Preparing') OR (OI.item_status = 'Cancel')) THEN 'In_Kitchen'
					        WHEN (OI.item_status = 'Ready') THEN 'READY'
					        WHEN ((OI.item_status = 'Closed') OR (OI.item_status = 'Served') OR (B.payment_status ='null'))  THEN 'PENDING'
					        WHEN (OI.item_status = 'Served') THEN 'SERVED'					       
					        ELSE 'Available'
					        END) AS table_status
						FROM " . $this->db->dbprefix('orders') . " AS O
						JOIN " . $this->db->dbprefix('order_items') . " OI ON OI.sale_id = O.id
						JOIN " . $this->db->dbprefix('restaurant_table_orders') . " T ON T.order_id = O.id
						LEFT JOIN " . $this->db->dbprefix('bbq_bils') . " B ON B.sales_id = O.id
						WHERE O.split_id ='".$split."' AND T.table_id =".$val." ORDER BY O.id DESC limit 1";
						
						$q = $this->db->query($myQuery);
				        if ($q->num_rows() > 0) {
				            $res = $q->row();
				            return $res->table_status;
				        }

				}else{
					$result = 'Ongoingothers';
				}
            }
			
			return $result;
		}else{
			return $result = 'Available';
		}
		return FALSE;	
	}
	
	public function getbbqCategoryByID($id) {
        $q = $this->db->get_where('bbq_categories', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	
	public function getBBQcurrentDiscount(){
	
		return false;	
	}
	public function GetAllBBQDiscounts() {
    	$this->db->where('status', 1);
        $q = $this->db->get('diccounts_for_bbq');
        // print_r($this->db->last_query());die;
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
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
	
	
	

	public function CheckCustomerDiscountAppliedBySplitID($split_id){
 		$current_date = date('Y-m-d');
		$this->db->select('*');
		/*$this->db->where('customer_id', $customer_id);
		$this->db->where('waiter_id', $waiter_id);
		$this->db->where('table_id', $table_id);*/
		$this->db->where('split_id', $split_id);
		$this->db->where('customer_type_val', 'customer');
		$this->db->where('DATE(created_on)', $current_date);
		$q = $this->db->get('customer_request_discount');
		// $data = $q->num_rows();die;
		// print_r($this->db->last_query());die;
		// if ($q->num_rows() == 1) {			
			/*if($q->row('customer_type_val') == 'automanual' || $q->row('customer_type_val') == 'customer'){
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
		$data['bbq'] = "0";*/
		if ($q->num_rows() > 0) {
            return $q->row('customer_discount_val');
        }
        return FALSE;

		// return $data;	
	}


	public function BBQsplitCountcheck($split_id){
		$current_date = date('Y-m-d');
		$q = $this->db->select('bbq.number_of_adult, bbq.number_of_child, bbq.number_of_kids')->where('bbq.reference_no', $split_id)->where('bbq.created_on', $current_date)->get('bbq');
		if ($q->num_rows() > 0) {
				$number_of_adult = $q->row('number_of_adult');
				$number_of_child = $q->row('number_of_child');
				$number_of_kids = $q->row('number_of_kids');
				
			return $data = $number_of_adult + $number_of_child + $number_of_kids;
		}
		return $data = 0;
	}
	

	
	public function dineinbbqbothCheck($split_id){
		$this->db->select('GROUP_CONCAT(order_type) AS order_type');
		$this->db->where('split_id', $split_id);
		$q= $this->db->get('orders');
		if($q->num_rows() > 0){
			$data = array_unique(explode(',', $q->row('order_type')));
			if($data[0] == 4 && $data[1] == 1){
				return TRUE;	
			}
			
		}
		return FALSE;
	}
	
	
	public function create_notification_bk($notification_array = array()){
	    $this->load->library('socketemitter');
		
		if(!empty($notification_array)){	
		
			$all = $this->db->insert('notiy', $notification_array['insert_array']);	
			$notifyid = $this->db->insert_id();
			if(isset($notification_array['from_role']) && $notification_array['from_role'] != SALE){	
				
				if($notification_array['from_role'] == WAITER){
					$role_form = 'Waiter';
				}elseif($notification_array['from_role'] == KITCHEN){
					$role_form = 'Kitchen';
				}elseif($notification_array['from_role'] == CASHIER){
					$role_form = 'Cashier';					
				}
				
				
				if($notification_array['insert_array']['role_id'] == WAITER){
					$role_to = 'Waiter';
				}elseif($notification_array['insert_array']['role_id'] == KITCHEN){
					$role_to = 'Kitchen';
				}elseif($notification_array['insert_array']['role_id'] == CASHIER){
					$role_to = 'Cashier';					
				}
				
				$notification = array(
					'msg' => $role_form.' to  '.$role_to,
					'type' => $notification_array['insert_array']['type'],
					'user_id' => $notification_array['insert_array']['user_id'],	
					'table_id' => $notification_array['insert_array']['table_id'],	
					'role_id' => SALE,
					'warehouse_id' => $notification_array['insert_array']['warehouse_id'],
					'created_on' => date('Y-m-d H:m:s'),
					'is_read' => 0
				);	
				$s = $this->db->insert('notiy', $notification);	
				
					
			}
			
			
			/*if($notification_array['customer_role'] == CUSTOMER){
				$notification_customer = array(
					'msg' => $notification_array['customer_msg'],
					'type' => $notification_array['customer_type'],
					'user_id' => $notification_array['customer_id'],	
					'table_id' => $notification_array['insert_array']['table_id'],	
					'role_id' => CUSTOMER,
					'warehouse_id' => $notification_array['insert_array']['warehouse_id'],
					'created_on' => date('Y-m-d H:m:s'),
					'is_read' => 0
				);	
								
				$c = $this->db->insert('notiy', $notification_customer);	
			}*/
			$notification_title = $notification_array['insert_array']['type'];
			$notification_message = $notification_array['insert_array']['msg'];
			if($this->isSocketEnabled()){
			    $emit_notification['title'] = $notification_title;
			    $emit_notification['msg'] = $notification_message;
			    $time1 = microtime(true);
			    // echo "step_one:".$time1;
			    $this->socketemitter->setEmit('notification', $emit_notification);
			    $time2 = microtime(true);
			    // echo "step_two:".$time2;
			}
			return $notifyid;
		}
		return false;
	}
	public function create_notification($notification_array = array()){
		
		$ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, site_url('archival_data/create_notifications'));
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        $notification_array = json_encode($notification_array);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $notification_array);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        $result = curl_exec($ch);
        curl_close($ch);
	}
	public function notification_clear($notification_id){
		
		if(!empty($notification_id)){	
			
			$this->db->where_in('id', explode(',',$notification_id));
			$this->db->update('notiy', array('is_read' => 1));			
			
			return true;
		}
		return false;
	}
	
	public function request_count($group_id, $user_id, $warehouse_id){
		$current_date = date('Y-m-d');
		$data = array();
		//$req = $this->db->select('*')->where('warehouse_id', $warehouse_id)->where('DATE(date)', $current_date)->where('bilgenerator_type', 1)->where('customer_discount_status', 'pending')->get('bils');
		$req = $this->db->select('*')->where('warehouse_id', $warehouse_id)->where('DATE(date)', $current_date)->where('payment_status', NULL)->get('bils');
		if ($req->num_rows() > 0) {
			foreach($req->result() as $row){
				$reqbil[] = $row;
			}
		}
		$data['list'] = $reqbil;
		if(!empty($data['list'])){
			$data['req_length'] = count($data['list']);
			return $data;
		}else{
			return false;
		}
	}
	
	public function notification_count($group_id, $user_id, $warehouse_id){
		$current_date = date('Y-m-d');
		$data = array();
		
		$u = $this->db->select('*')->where('to_user_id', $user_id)->where('warehouse_id', $warehouse_id)->where('is_read', 0)->where('DATE(created_on)', $current_date)->get('notiy');
		if ($u->num_rows() > 0) {
			foreach($u->result() as $uow){
				$user[] = $uow;
			}
		}
		
		/*$r =$this->db->select('*')->where('role_id', $group_id)->where('to_user_id', 0)->where('warehouse_id', $warehouse_id)->where('is_read', 0)->where('DATE(created_on)', $current_date)->get('notiy');
		if ($r->num_rows() > 0) {
			foreach($r->result() as $row){
				$group[] = $row;
			}
		}
		if(!empty($user) && empty($group)){
			$data['list'] = $user;
		}elseif(empty($user) && !empty($group)){
			$data['list'] = $group;
		}elseif(!empty($user) && !empty($group)){
			$data['list'] = array_merge($user, $group);
		}*/

		if(!empty($user)){
			$data['list'] = $user;
		}
		
		if(!empty($data['list'])){
			$data['count'] = count($data['list']);
			return $data;
		}else{
			return false;
		}
				
		
	}
	
	
	
    public function get_shop_sale_alerts() {
        $this->db->join('deliveries', 'deliveries.sale_id=sales.id', 'left')
        ->where('sales.shop', 1)->where('sales.sale_status', 'completed')->where('sales.payment_status', 'paid')
        ->group_start()->where('deliveries.status !=', 'delivered')->or_where('deliveries.status IS NULL', NULL)->group_end();
        return $this->db->count_all_results('sales');
    }

    public function get_shop_payment_alerts() {
        $this->db->where('shop', 1)->where('attachment !=', NULL)->where('payment_status !=', 'paid');
        return $this->db->count_all_results('sales');
    }

    public function get_setting() {
        $q = $this->db->get('settings');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	public function get_posSetting()
    {	
        $q = $this->db->get('pos_settings');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }	
	public function getWaiter($split_id){
		
		$q = $this->db->get_where('orders', array('split_id' => $split_id), 1);
        if ($q->num_rows() == 1) {
			
            return $q->row('created_by');
        }
		return FALSE;
	}
	
	public function getOrderCustomerDATA($split_id){
		
		$q = $this->db->get_where('orders', array('split_id' => $split_id), 1);
        if ($q->num_rows() == 1) {
			
            return $q->row('customer_id');
        }
		return FALSE;
	}
	
	public function devicesCheck($api_key){
		$q = $this->db->get_where('api_keys', array('key' => $api_key), 1);		
        if ($q->num_rows() == 1) {
			
            return $q->row('devices_key');
        }
		return FALSE;
	}
	
	public function splitCheckSalestable($split_id){
		$q = $this->db->get_where('sales', array('sales_split_id' => $split_id), 1);
        if ($q->num_rows() == 1) {
            return TRUE;
        }
        return FALSE;
	}
	
	public function splitCountcheck($split_id){
		$current_date = date('Y-m-d');
		$current_date = $this->getTransactionDate();
		$q = $this->db->select('orders.id AS order_id, order_items.id AS item_id')->join('order_items', 'order_items.sale_id = orders.id AND order_items.order_item_cancel_status = 0')->where('orders.split_id', $split_id)->where('orders.order_cancel_status', 0)->where('DATE(date)', $current_date)->get('orders');
		if ($q->num_rows() > 0) {
				$i=0;
				foreach (($q->result()) as $row) {
					$i++;
				}
			return $data = $i;
		}
		return $data = 0;
	}
	
	
	
	public function FinalamountRound($amount){
		
		$checkamount = $amount % 100;
		$extraamount = (100 - $amount % 100 ?: 100);
		if($checkamount < 50){
			 $grand_amount = $amount - $checkamount;
		}else{
			$grand_amount = $amount + $extraamount;
		}
		
		return $grand_amount;

	}
	
	public function checkTableStatus($table_id){
		$current_date = date('Y-m-d');
		$current_date = $this->getTransactionDate();
		$items['a'] = $this->db->select('COUNT(id) AS count_null', false)
		->from('orders')
		->where('orders.table_id', $table_id)
		//->where('DATE(date)', $current_date)
		->get()->result();
		$items['b'] = $this->db->select('COUNT(id) AS count_not_null', false)
		->from('orders')
		->where('orders.table_id', $table_id)
		//->where('DATE(date)', $current_date)
		->where('orders.payment_status', 'Paid')
		->get()->result();
		$items = array_merge($items['a'], $items['b']);
		if($items[0]->count_null == $items[1]->count_not_null){
			return TRUE;
		}
        return FALSE;
	}
	
	public function checkBuyget($recipe_id){
		$current_date = date('Y-m-d');
		$current_time = date('H:i:s');
		$current_day=date("l");
		$check_get_x = 'buy_x_get_x';
		$check_get_y = 'buy_x_get_y';
		$buy_query_x = "SELECT buy_get.id, buy_get.buy_method, buy_get.buy_quantity, buy_get.get_quantity, buy_get_items.get_item, recipe.name AS free_recipe,buy_variant_id , get_variant_id,rv.name variant_Name  FROM ".$this->db->dbprefix('buy_get')." AS buy_get
		JOIN  ".$this->db->dbprefix('buy_get_items')." AS buy_get_items ON buy_get_items.buy_get_id = buy_get.id 
		JOIN ".$this->db->dbprefix('recipe')." AS recipe ON recipe.id = buy_get_items.get_item
		left join ".$this->db->dbprefix('recipe_variants')." AS rv ON rv.id = buy_get_items.get_variant_id
		WHERE buy_get.buy_method = '".$check_get_x."' AND buy_get_items.buy_item = ".$recipe_id."  AND '".$current_date."' BETWEEN buy_get.start_date AND buy_get.end_date AND '".$current_time."' BETWEEN buy_get.start_time AND buy_get.end_time and  FIND_IN_SET('".$current_day."' ,week_days) ORDER BY buy_get.id DESC LIMIT 1";

		
		$buy_query_y = "SELECT buy_get.id, buy_get.buy_method, buy_get.buy_quantity, buy_get.get_quantity, buy_get_items.get_item, recipe.name AS free_recipe,buy_variant_id , get_variant_id,rv.name variant_Name  FROM ".$this->db->dbprefix('buy_get')." AS buy_get
		JOIN  ".$this->db->dbprefix('buy_get_items')." AS buy_get_items ON buy_get_items.buy_get_id = buy_get.id 
		JOIN ".$this->db->dbprefix('recipe')." AS recipe ON recipe.id = buy_get_items.get_item
		left join ".$this->db->dbprefix('recipe_variants')." AS rv ON rv.id = buy_get_items.get_variant_id
		WHERE buy_get.buy_method = '".$check_get_y."' AND buy_get_items.buy_item = ".$recipe_id."  AND '".$current_date."' BETWEEN buy_get.start_date AND buy_get.end_date AND '".$current_time."' BETWEEN buy_get.start_time AND buy_get.end_time and  FIND_IN_SET('".$current_day."' ,week_days) ORDER BY buy_get.id DESC LIMIT 1";
		$x = $this->db->query($buy_query_x);
		$y = $this->db->query($buy_query_y);
		if ($x->num_rows() > 0) {
			return $x->row();
		}elseif($y->num_rows() > 0){
			return $y->row();
		}
		 return FALSE;
	}
	public function checkBuyget_variant($recipe_id,$variant_id){
		$current_date = date('Y-m-d');
		$current_time = date('H:i:s');
		$check_get_x = 'buy_x_get_x';
		$check_get_y = 'buy_x_get_y';
		$buy_query_x = "SELECT buy_get.id, buy_get.buy_method, buy_get.buy_quantity, buy_get.get_quantity, buy_get_items.get_item, recipe.name AS free_recipe,buy_variant_id , get_variant_id,rv.name variant_Name  FROM ".$this->db->dbprefix('buy_get')." AS buy_get
		JOIN  ".$this->db->dbprefix('buy_get_items')." AS buy_get_items ON buy_get_items.buy_get_id = buy_get.id 
		JOIN ".$this->db->dbprefix('recipe')." AS recipe ON recipe.id = buy_get_items.get_item
		left join ".$this->db->dbprefix('recipe_variants')." AS rv ON rv.id = buy_get_items.get_variant_id
		WHERE buy_get.buy_method = '".$check_get_x."' AND buy_get_items.buy_item = ".$recipe_id."And  buy_get_items.buy_variant_id = ".$variant_id."  AND '".$current_date."' BETWEEN buy_get.start_date AND buy_get.end_date AND '".$current_time."' BETWEEN buy_get.start_time AND buy_get.end_time ORDER BY buy_get.id DESC LIMIT 1";
		
		$buy_query_y = "SELECT buy_get.id, buy_get.buy_method, buy_get.buy_quantity, buy_get.get_quantity, buy_get_items.get_item, recipe.name AS free_recipe,buy_variant_id , get_variant_id,rv.name variant_Name  FROM ".$this->db->dbprefix('buy_get')." AS buy_get
		JOIN  ".$this->db->dbprefix('buy_get_items')." AS buy_get_items ON buy_get_items.buy_get_id = buy_get.id 
		JOIN ".$this->db->dbprefix('recipe')." AS recipe ON recipe.id = buy_get_items.get_item
		left join ".$this->db->dbprefix('recipe_variants')." AS rv ON rv.id = buy_get_items.get_variant_id
		WHERE buy_get.buy_method = '".$check_get_y."' AND buy_get_items.buy_item = ".$recipe_id." and  buy_get_items.buy_variant_id = ".$variant_id."  AND '".$current_date."' BETWEEN buy_get.start_date AND buy_get.end_date AND '".$current_time."' BETWEEN buy_get.start_time AND buy_get.end_time ORDER BY buy_get.id DESC LIMIT 1";
		$x = $this->db->query($buy_query_x);
		
		$y = $this->db->query($buy_query_y);
		if ($x->num_rows() > 0) {
			return $x->row();
		}elseif($y->num_rows() > 0){
			return $y->row();
		}
		 return FALSE;
	}
	public function allOrdersCancelStatus($order_id){
		$q = $this->db->get_where('order_items', array('sale_id' => $order_id, 'order_item_cancel_status' => 0));
        if ($q->num_rows() == 0) {
            return TRUE;
        }
        return FALSE;
	}
	
	public function orderTablecheck($val){
		
		$current_date = date('Y-m-d');
		$current_date = $this->getTransactionDate();
		/*$this->db->select('kitchen_orders.waiter_id ')
		->join('kitchen_orders', 'kitchen_orders.sale_id = restaurant_table_orders.order_id')
		->join('orders', 'orders.id = restaurant_table_orders.order_id AND orders.order_cancel_status = 0')
		->where('restaurant_table_orders.table_id', $val)
		->where('orders.payment_status', NULL)
		->where('DATE(date)', $current_date)
		//->where('orders', 'orders.date = '.date('y-m-d').'')
		->group_by('restaurant_table_orders.table_id');		
		$q = $this->db->get('restaurant_table_orders');*/
		$where = '';
		if($this->Settings->night_audit_rights==0){
		    $where = 'DATE(O.date) ="'.$current_date.'"  AND ';

		}
		$main = "SELECT KO.waiter_id
					FROM " . $this->db->dbprefix('restaurant_table_orders') . " AS RTO
					JOIN " . $this->db->dbprefix('kitchen_orders') . " KO ON KO.sale_id = RTO.order_id
					JOIN " . $this->db->dbprefix('orders') . " O ON O.id = RTO.order_id AND O.order_cancel_status = 0
					WHERE ".$where."RTO.table_id='".$val."' AND O.payment_status is null  GROUP BY RTO.table_id";
					
//DATE(O.date) ='".$current_date."'  AND 
		$q = $this->db->query($main);
		
		if ($q->num_rows() > 0) {

			foreach (($q->result()) as $row) {
              
			 
				$p = $this->db->select('*')->where('group_id', $group_id)->get('permissions');
			  if ($p->num_rows() > 0) { 
			  	if($p->row('pos-view_allusers_orders') == 0){
					if($user_id == $row->waiter_id){
						$other = 1;
					}else{
						$other = 0;
					}
				}else{
					$other = 1; 
				}
			  }else{
				 $other = 1; 
			  }
			  
			  
			  if($other == 1){

					$splits = "SELECT O.split_id
					FROM " . $this->db->dbprefix('orders') . " AS O
					JOIN " . $this->db->dbprefix('restaurant_table_orders') . " T ON T.order_id = O.id
					WHERE T.table_id ='".$val."'  ORDER BY O.id DESC ";

					$s = $this->db->query($splits);
					
			        if ($s->num_rows() > 0) {
			            $spt = $s->row();
			            $split = $spt->split_id;
			        }

					$myQuery = "SELECT (CASE
					        WHEN ((OI.item_status = 'Inprocess') OR (OI.item_status = 'Preparing') OR (OI.item_status = 'Cancel')) THEN 'In_Kitchen'
					        WHEN (OI.item_status = 'Ready') THEN 'READY'
					        WHEN ((OI.item_status = 'Closed') OR (OI.item_status = 'Served'))  THEN 'PENDING'
					        WHEN (OI.item_status = 'Served') THEN 'SERVED'					       
					        ELSE 'Available'
					        END) AS table_status
						FROM " . $this->db->dbprefix('orders') . " AS O
						JOIN " . $this->db->dbprefix('order_items') . " OI ON OI.sale_id = O.id
						JOIN " . $this->db->dbprefix('restaurant_table_orders') . " T ON T.order_id = O.id
						LEFT JOIN " . $this->db->dbprefix('bils') . " B ON B.sales_id = O.id
						WHERE O.split_id ='".$split."' AND T.table_id ='".$val."' ORDER BY O.id DESC limit 1";
						
						$q = $this->db->query($myQuery);
					
				        if ($q->num_rows() > 0) {
				            $res = $q->row();
				            return $res->table_status;
				        }

					/*$result = 'Ongoing';*/
				}else{
					$result = 'Ongoingothers';
				}
            }
			
			return $result;
		}else{
			return $result = 'Available';
		}
		return FALSE;	
	}
	
	
	
	public function orderBBQTablecheckapi($val, $user_id){
		
		$current_date = date('Y-m-d');
		$current_date = $this->getTransactionDate();
		$main = "SELECT KO.waiter_id
					FROM " . $this->db->dbprefix('restaurant_table_orders') . " AS RTO
					JOIN " . $this->db->dbprefix('kitchen_orders') . " KO ON KO.sale_id = RTO.order_id
					JOIN " . $this->db->dbprefix('orders') . " O ON O.id = RTO.order_id AND O.order_cancel_status = 0
					WHERE RTO.table_id='".$val."' AND O.payment_status is null GROUP BY RTO.table_id";
//DATE(O.date) ='".$current_date."'  AND 
		$q = $this->db->query($main);
		
		if ($q->num_rows() > 0) {

			foreach (($q->result()) as $row) {
              
			 
				$p = $this->db->select('*')->where('group_id', $group_id)->get('permissions');
			  if ($p->num_rows() > 0) { 
			  	if($p->row('pos-view_allusers_orders') == 0){
					if($user_id == $row->waiter_id){
						$other = 1;
					}else{
						$other = 0;
					}
				}else{
					$other = 1; 
				}
			  }else{
				 $other = 1; 
			  }
			  
			  
			  if($other == 1){

					$splits = "SELECT O.split_id
					FROM " . $this->db->dbprefix('orders') . " AS O
					JOIN " . $this->db->dbprefix('restaurant_table_orders') . " T ON T.order_id = O.id
					WHERE T.table_id ='".$val."'  ORDER BY O.id DESC ";

					$s = $this->db->query($splits);
					
			        if ($s->num_rows() > 0) {
			            $spt = $s->row();
			            $split = $spt->split_id;
			        }

					$myQuery = "SELECT (CASE
					        WHEN ((OI.item_status = 'Inprocess') OR (OI.item_status = 'Preparing') OR (OI.item_status = 'Cancel')) THEN 'In_Kitchen'
					        WHEN (OI.item_status = 'Ready') THEN 'READY'
					        WHEN ((OI.item_status = 'Closed') OR (OI.item_status = 'Served') OR (B.payment_status ='null'))  THEN 'PENDING'
					        WHEN (OI.item_status = 'Served') THEN 'SERVED'					       
					        ELSE 'Available'
					        END) AS table_status
						FROM " . $this->db->dbprefix('orders') . " AS O
						JOIN " . $this->db->dbprefix('order_items') . " OI ON OI.sale_id = O.id
						JOIN " . $this->db->dbprefix('restaurant_table_orders') . " T ON T.order_id = O.id
						LEFT JOIN " . $this->db->dbprefix('bils') . " B ON B.sales_id = O.id
						WHERE O.split_id ='".$split."' AND T.table_id =".$val." ORDER BY O.id DESC limit 1";
						
						$q = $this->db->query($myQuery);
				        if ($q->num_rows() > 0) {
				            $res = $q->row();
				            return $res->table_status;
				        }

				}else{
					$result = 'Ongoingothers';
				}
            }
			
			return $result;
		}else{
			return $result = 'Available';
		}
		return FALSE;	
	}
	
	public function orderTablecheckapi($val, $user_id){
		
		$current_date = date('Y-m-d');
		$current_date = $this->getTransactionDate();
		/*$this->db->select('kitchen_orders.waiter_id ')
		->join('kitchen_orders', 'kitchen_orders.sale_id = restaurant_table_orders.order_id')
		->join('orders', 'orders.id = restaurant_table_orders.order_id AND orders.order_cancel_status = 0')
		->where('restaurant_table_orders.table_id', $val)
		->where('orders.payment_status', NULL)
		->where('DATE(date)', $current_date)
		//->where('orders', 'orders.date = '.date('y-m-d').'')
		->group_by('restaurant_table_orders.table_id');
		
		$q = $this->db->get('restaurant_table_orders');*/

		 $main = "SELECT KO.waiter_id
			FROM " . $this->db->dbprefix('restaurant_table_orders') . " AS RTO
			JOIN " . $this->db->dbprefix('kitchen_orders') . " KO ON KO.sale_id = RTO.order_id
			JOIN " . $this->db->dbprefix('orders') . " O ON O.id = RTO.order_id AND O.order_cancel_status = 0
			WHERE DATE(O.date)='".$current_date."' AND RTO.table_id='".$val."' AND O.payment_status is null GROUP BY RTO.table_id";

		$q = $this->db->query($main);
		
		
		if ($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
              
			 
				$p = $this->db->select('*')->where('group_id', $group_id)->get('permissions');
			  if ($p->num_rows() > 0) { 
			  	if($p->row('pos-view_allusers_orders') == 0){
					if($user_id == $row->waiter_id){
						$other = 1;
					}else{
						$other = 0;
					}
				}else{
					$other = 1; 
				}
			  }else{
				 $other = 1; 
			  }
			  
			  
			  if($other == 1){
					
					$splits = "SELECT O.split_id
					FROM " . $this->db->dbprefix('orders') . " AS O
					JOIN " . $this->db->dbprefix('restaurant_table_orders') . " T ON T.order_id = O.id
					WHERE T.table_id ='".$val."'  ORDER BY O.id DESC ";
					
					/*$splits= "SELECT O.split_id
					FROM " . $this->db->dbprefix('orders') . " AS O
					JOIN " . $this->db->dbprefix('restaurant_table_orders') . " T ON T.order_id = O.id
					WHERE T.table_id ='".$val."' ";*/

					$s = $this->db->query($splits);
					
			        if ($s->num_rows() > 0) {
			            $spt = $s->row();
			            $split = $spt->split_id;
			        }
					
				/*	$myQuery = "SELECT (CASE
					        WHEN ((OI.item_status = 'Inprocess') OR (OI.item_status = 'Preparing') OR (OI.item_status = 'Cancel')) THEN 'In_Kitchen'
					        WHEN (OI.item_status = 'Ready') THEN 'READY'
					        WHEN ((OI.item_status = 'Closed') OR (OI.item_status = 'Served')) AND ((B.sales_id = O.id) OR (B.payment_status ='null')) THEN 'PENDING'
					        WHEN (OI.item_status = 'Served') THEN 'SERVED'					       
					        ELSE 'Available'
					        END) AS table_status
						FROM " . $this->db->dbprefix('orders') . " AS O
						JOIN " . $this->db->dbprefix('order_items') . " OI ON OI.sale_id = O.id
						JOIN " . $this->db->dbprefix('restaurant_table_orders') . " T ON T.order_id = O.id
						LEFT JOIN " . $this->db->dbprefix('bils') . " B ON B.sales_id = O.id
						WHERE O.split_id ='".$split."' AND T.table_id ='".$val."' ORDER BY O.id DESC limit 1";
						
						$q = $this->db->query($myQuery);*/


							$myQuery = "SELECT (CASE
					        WHEN ((OI.item_status = 'Inprocess') OR (OI.item_status = 'Preparing') OR (OI.item_status = 'Cancel')) THEN 'In_Kitchen'
					        WHEN (OI.item_status = 'Ready') THEN 'READY'
					        WHEN ((OI.item_status = 'Closed') OR (OI.item_status = 'Served'))  THEN 'PENDING'
					        WHEN (OI.item_status = 'Served') THEN 'SERVED'					       
					        ELSE 'Available'
					        END) AS table_status
						FROM " . $this->db->dbprefix('orders') . " AS O
						JOIN " . $this->db->dbprefix('order_items') . " OI ON OI.sale_id = O.id
						JOIN " . $this->db->dbprefix('restaurant_table_orders') . " T ON T.order_id = O.id
						LEFT JOIN " . $this->db->dbprefix('bils') . " B ON B.sales_id = O.id
						WHERE O.split_id ='".$split."' AND T.table_id ='".$val."' ORDER BY O.id DESC limit 1";
						
						$q = $this->db->query($myQuery);

					
				        if ($q->num_rows() > 0) {
				            $res = $q->row();
				            $result =  $res->table_status;
				        }
						
					//$result = 'Ongoing';
				}else{
					$result = 'Ongoingothers';
				}
            }
			
			return $result;
		}else{
			return $result = 'Available';
		}
		return FALSE;	
	}
	
	public function getTableCancelstatus($item_id){
		
		$q = $this->db->get_where('order_items', array('id' => $item_id, 'order_item_cancel_status' => 1), 1);
        if ($q->num_rows() != 0) {
            return TRUE;
        }
        return FALSE;
	}
	
	public function getOrderItem($item_id){
		
		$q = $this->db->get_where('order_items', array('id' => $item_id), 1);
        if ($q->num_rows() == 1 ) {
            return $q->row();
        }
        return FALSE;
	}
	
	public function getOrderItemCustomer($item_id){
		$this->db->select('orders.customer_id');
		$this->db->join('orders', 'orders.id = order_items.sale_id');
		$q = $this->db->get_where('order_items', array('order_items.id' => $item_id), 1);
        if ($q->num_rows() == 1 ) {
            return $q->row('customer_id');
        }
        return FALSE;
	}
	public function getOrderCustomer($order_id){
		$this->db->select('orders.customer_id');
		$q = $this->db->get_where('orders', array('orders.id' => $order_id), 1);
        if ($q->num_rows() == 1 ) {
            return $q->row('customer_id');
        }
        return FALSE;
	}
	
	
	public function splitClose($split){
		 $this->db->select('orders.id, order_items.item_status')
         ->join('order_items', 'orders.id=order_items.sale_id');
        $split_count = $this->db->get_where('orders', array('orders.split_id' => $split, 'order_items.item_status !='  => 'Closed', 'order_items.order_item_cancel_status' => 0 ));
		if($split_count->num_rows() == 0){
			return TRUE;
		}
		return FALSE;	
	}

    public function getDateFormat($id) {
        $q = $this->db->get_where('date_format', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	
	public function getAllPONUMBER(){
		$q = $this->db->get_where('purchase_order', array('status' => ''));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
	}
	
	public function getAllPONUMBERedit(){
		$q = $this->db->get('purchase_order');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
	}
public function getAllQUATATIONNUMBER(){
		$q = $this->db->get_where('quotes', array('status' => NULL));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
	}

	public function getAllQUATATIONNUMBERedit(){
		$q = $this->db->get('quotes');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
	}

public function getAllMaterial_RequestNo(){
		$q = $this->db->get_where('material_request', array('status' => NULL));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
	}		
    public function getAllCompanies($group_name) {
        $q = $this->db->get_where('companies', array('group_name' => $group_name));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getCompanyByID($id) {
        $q = $this->db->get_where('companies', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getCustomerGroupByID($id) {
        $q = $this->db->get_where('customer_groups', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	
	public function getPreviousDayNightAudit($branch_id) {
		$date_format = 'Y-m-d';
		$yesterday = strtotime('-1 day');
		$previous_date = date($date_format, $yesterday);
		$check_row = $this->db->get('nightaudit');

		$installed_date = $this->Settings->installed_date;
		$install = strtotime($installed_date);        
		$install_date = date('Y-m-d', $install);
		$today_date = date('Y-m-d');
		
		if($install_date < $today_date){
			
			if($check_row->num_rows() > 0){
				$todaytransactionDay = $this->getTransactionDate_nightaudit();
				$previousTransactionDay = $this->getLastDayTransactionDate();
				if (!$todaytransactionDay || $todaytransactionDay==date('Y-m-d')){
				    $this->db->where('nightaudit_date', $previous_date);
				    $this->db->where('warehouse_id', $branch_id);
				    $q = $this->db->get('nightaudit');
				    if ($q->num_rows() > 0) {
					    
					     return TRUE;
				    }
				    else{
					    return FALSE;
				    }
				}else{
				    return true;
				}
			}
			else{	
				return FALSE;
					/*$this->db->where('DATE(date)', $previous_date);
					$this->db->where('warehouse_id', $branch_id);
					$p = $this->db->get('bils');
					if($p->num_rows() > 0){
						return FALSE;
				    }	
				    else{
				    	return TRUE;
				    }*/
			}
			
		}
		else{
			
			return TRUE;
		}
        return FALSE;
    }

	public function getDeliveryPersonall($warehouse_id){
		$this->db->select("users.id, users.first_name, users.last_name, users.email, groups.description");
		$this->db->join('groups', 'groups.id = users.group_id');
		$this->db->where('users.warehouse_id', $warehouse_id);
		$this->db->where('users.active', 1);
		$this->db->order_by('users.group_id', 'DESC');
		 $q = $this->db->get('users');
		if ($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
                $data[] = $row;
            }
			
			
            return $data;
		}
		return FALSE;
	}

    public function getUser($id = NULL) {
        if (!$id) {
            $id = $this->session->userdata('user_id');
        }
        $q = $this->db->get_where('users', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getProductByID($id) {
        $q = $this->db->get_where('products', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	
	 public function getrecipeByID($id) {
        $q = $this->db->get_where('recipe', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	
	public function getrecipeKhmer($id){
		$q = $this->db->get_where('recipe', array('id' => $id), 1);
		// print_r($this->db->last_query());die;
        if ($q->num_rows() > 0) {
            return $q->row('khmer_name');
        }
        return FALSE;
	}

	public function getrecipevariantKhmer($id){
		$q = $this->db->get_where('recipe_variants', array('id' => $id), 1);		
        if ($q->num_rows() > 0) {
            return $q->row('native_name');
        }
        return FALSE;
	}
public function getRecipeVariantById($id){
		$q = $this->db->get_where('recipe_variants', array('id' => $id), 1);		
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
	}

	public function getrecipeKhmerimage($id){
		$q = $this->db->get_where('recipe', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row('khmer_image');
        }
        return FALSE;
	}
	public function getvariantlocalnameimage($id){
		$q = $this->db->get_where('recipe_variants', array('id' => $id), 1);		
        if ($q->num_rows() > 0) {
            return $q->row('variant_localname_image');
        }
        return FALSE;
	}

   public function getitemvariantlocalnameimage($recipe_id,$vari_id){
		$q = $this->db->get_where('recipe_variants_values', array('recipe_id' => $recipe_id,'attr_id' => $vari_id), 1);		
		// print_r($this->db->last_query());die;
        if ($q->num_rows() > 0) {
            return $q->row('image');
        }
        return FALSE;
	}


	
	 public function getAllGroups($pos_user = false) {
		 if($pos_user){
			 $this->db->where_not_in('id', array(1,2,3,4,9));
			 
		 } 
		 $q = $this->db->get('groups');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getAllCurrencies() {
        $q = $this->db->get('currencies');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getCurrencyByCode($code) {
        $q = $this->db->get_where('currencies', array('code' => $code), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	
	public function defaultCurrencyData($id) {
        $q = $this->db->get_where('currencies', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    
 public function getExchangeCurrency($id) {
 	$this->db->select('symbol');
 	$this->db->where_not_in('id', array($id));
        $q = $this->db->get('currencies');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data = $row->symbol;
            }
            return $data;
        }
        return FALSE;
    }
    
    public function getExchangeRatey($id) {
 	$this->db->select('rate');
 	$this->db->where_not_in('id', array($id));
        $q = $this->db->get('currencies');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data = $row->rate;
            }
            return $data;
        }
        return FALSE;
    }
    public function getExchangeRatecode($id) {
 	$this->db->select('code');
 	$this->db->where_not_in('id', array($id));
        $q = $this->db->get('currencies');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data = $row->code;
            }
            return $data;
        }
        return FALSE;
    }    	
	public function getCurrencyByID($id) {
        $q = $this->db->get_where('currencies', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	
	public function CancelSalescheckData($sale_id){
		 $q = $this->db->get_where('sales', array('id' => $sale_id, 'payment_status' => NULL), 1);
        if ($q->num_rows() > 0) {
            return TRUE;
        }
        return FALSE;
	}
public function getaddonitemid($id){
	 $q = $this->db->get_where('recipe_addon_details', array('id' => $id), 1);	 
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;

}	
	public function getAddonByRecipeidAndOrderitemid($recipe_id, $order_item_id) {	
		$this->db->select('addon_sale_items.id,addon_sale_items.price,addon_sale_items.qty,addon_sale_items.subtotal,recipe.name AS addon_name,recipe.khmer_name AS native_name,recipe.id AS recipe_id,addon_sale_items.image_path');
		$this->db->join('order_items', 'order_items.id = addon_sale_items.order_item_id');
		$this->db->join('recipe', 'recipe.id = addon_sale_items.sale_item_id');		
		$this->db->where('order_items.id', $order_item_id);
        $q = $this->db->get('addon_sale_items');         
        // print_r($this->db->last_query());die;
		//Print_r($order_item_id); die;
		
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }	

    public function getAddonByRecipeidAndOrderitemid_kot($recipe_id, $order_item_id) {	
    	
		$this->db->select('addon_sale_items.id,addon_sale_items.price,addon_sale_items.qty,addon_sale_items.subtotal,recipe.name AS addon_name,recipe.khmer_name AS native_name,recipe.id AS recipe_id,addon_sale_items.image_path');
		$this->db->join('order_items', 'order_items.id = addon_sale_items.order_item_id');
		$this->db->join('recipe', 'recipe.id = addon_sale_items.sale_item_id');		
		$this->db->where('order_items.id', $order_item_id);
		$this->db->where('order_items.recipe_id', $recipe_id);
        $q = $this->db->get('addon_sale_items');         
        // print_r($this->db->last_query());die;
        $kotimagefolder = date('Ymd');	
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
            	if($this->pos_settings->kot_print_lang_option == 1 ){
            		$image_path = (!empty($row->image_path) && file_exists('assets/language/'.$kotimagefolder.'/'.$row->image_path))? (base_url().'assets/language/'.$kotimagefolder.'/'.$row->image_path) : '';	
            	}else{
            		$khmer_image = $this->site->getrecipeKhmerimage($row->recipe_id);
	            	$image_path = (!empty($khmer_image) && file_exists('assets/language/'.$khmer_image))? (base_url().'assets/language/'.$khmer_image) : '';
            	}   
            	$newline = false;
            	$row->addon_name_qty = $this->wraprecipe_name_qty('[+]'.$row->addon_name,$row->qty,$newline);            	
            	$row->addon_image = $image_path;
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getunwanted_ingredientfor_kot($recipe_id, $order_item_id,$unwanted_ingredients) {

		$this->db->select('recipe.name AS removed_ingredients,recipe.khmer_name AS native_name,recipe.id AS recipe_id');
		$this->db->join('recipe', 'order_items.recipe_id = order_items.recipe_id');		
		$this->db->where('order_items.id', $order_item_id);
		$this->db->where('order_items.recipe_id', $recipe_id);
		$this->db->where_in('recipe.id', explode(',',$unwanted_ingredients));
		$this->db->where('order_items.unwanted_ingredients !=', '');
        $q = $this->db->get('order_items');     
        // print_r($this->db->last_query());die;            
        $kotimagefolder = date('Ymd');	
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
            	$khmer_image = $this->site->getrecipeKhmerimage($row->recipe_id);
            	$image_path = (!empty($khmer_image) && file_exists('assets/language/'.$khmer_image))? (base_url().'assets/language/'.$khmer_image) : '';

            	/*$image_path = (!empty($row->image_path) && file_exists('assets/language/'.$kotimagefolder.'/'.$row->image_path))? (base_url().'assets/language/'.$kotimagefolder.'/'.$row->image_path) : '';*/
            	$row->unwanted_ingredients_image = $image_path;

                $data[] = $row;
            }
            // print_r($this->db->error());die;
            return $data;
        }
        return FALSE;
    }    	

	public function getAddonByRecipeidAndBillitemid($recipe_id, $bill_item_id) {	
		$this->db->select('addon_bill_items.*, recipe.name AS addon_name,');
		$this->db->join('bil_items', 'bil_items.id = addon_bill_items.bill_item_id');
		$this->db->join('recipe', 'recipe.id = addon_bill_items.sale_item_id');		
		$this->db->where('bil_items.id', $bill_item_id);
        $q = $this->db->get('addon_bill_items');          
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }	
	public function getAddonByRecipeidAndBillitemid_archival($recipe_id, $bill_item_id) {	
		$this->db->select('addon_bill_items_archival.*, recipe.name AS addon_name,');
		$this->db->join('bil_items_archival', 'bil_items_archival.id = addon_bill_items_archival.bill_item_id');
		$this->db->join('recipe', 'recipe.id = addon_bill_items_archival.sale_item_id');		
		$this->db->where('bil_items_archival.id', $bill_item_id);
        $q = $this->db->get('addon_bill_items_archival');          
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }	
	/*public function getAddonByid($recipe_id, $order_item_id) {	
		
				
				$billQuery = "SELECT  GROUP_CONCAT(id) as ids FROM " . $this->db->dbprefix('sales') . "  WHERE sales_split_id = '".$row->sales_split_id."' ";
        		$q = $this->db->query($billQuery);
				
				if ($q->num_rows() > 0) {
					
					$this->db->select("bils.*,companies.credit_limit,companies.name customer_name,companies.customer_type,companies.id company_id,companies.credit_limit");
					$this->db->join("companies", "companies.id = bils.customer_id","left");
					$this->db->where('bils.consolidated', 1);
					$this->db->where_in('bils.sales_id', explode(',',$q->row('ids')));
					$b = $this->db->get('bils');
					
					if ($b->num_rows() > 0) {
						foreach ($b->result() as $bil_row) {
							
							$bils[$row->id][] = $bil_row;
						}
						$row->bils = $bils[$row->id];
						$data[] = $row;	
					}
				}

		$this->db->select('addon_sale_items.*, recipe.name AS addon_name,');
		$this->db->join('order_items', 'order_items.id = addon_sale_items.order_item_id');
		$this->db->join('recipe', 'recipe.id = addon_sale_items.sale_item_id');		
		$this->db->where('order_items.id', $order_item_id);
        $q = $this->db->get('addon_sale_items');        
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }	
*/
	public function getAddonByRecipe($recipe_id, $recipe_addon = array()) {		    

		$addons =   explode(',',$recipe_addon);		
		$this->db->select('recipe_addon.*, recipe.name AS addon_name');
		$this->db->join('recipe_addon_details', 'recipe_addon_details.addon_head_id = recipe_addon.id');
		$this->db->join('recipe', 'recipe.id = recipe_addon_details.addon_item_id');
		$this->db->where('recipe_addon.recipe_id', $recipe_id);
		$this->db->where_in('recipe_addon.id', $addons);
        $q = $this->db->get('recipe_addon');        
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
	public function getAllResKitchen() {
		
        $q = $this->db->get('restaurant_kitchens');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
public function getAllDefalutKitchen() {
		
		$default_kitchen = 0;

		$get_default_kitchen = "SELECT RK.id
 		    FROM ".$this->db->dbprefix('restaurant_kitchens')." AS RK
   			where RK.is_default = 1 ";   

		   $k = $this->db->query($get_default_kitchen);  
           
			if ($k->num_rows() > 0) {				
				$result = $k->row();
				$default_kitchen = $result->id;
				  return $default_kitchen;
			}
		return 0;      
    }	
    
	public function getResKitchenByID($id) {
        $q = $this->db->get_where('restaurant_kitchens', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getAllTaxRates() {
        $q = $this->db->get('tax_rates');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getTaxRateByID($id) {
        $q = $this->db->get_where('tax_rates', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }


    public function getAllSericeCharges() {
        $q = $this->db->get('service_charge');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }


    public function getServiceChargeByID($id) {
        $q = $this->db->get_where('service_charge', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }


    public function getAllWarehouses() {
        $q = $this->db->get('warehouses');////,array('type'=>0))
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getThisStore(){
	$q = $this->db->get_where('warehouses',array('this_store'=>1));
	
	if($q->num_rows()>0){
	    return $q->row();
	}
	return false;
    }

    public function getAllStores() {
        $q = $this->db->get_where('warehouses',array('type'=>1));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getWarehouseByID($id) {
        $q = $this->db->get_where('warehouses', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	public function getWarehouseOrderByID($id) {
        $q = $this->db->get_where('warehouses', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	
	
	public function getAllSalestype() {
        $q = $this->db->get('sales_type');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getSalestypeByID($id) {
        $q = $this->db->get_where('sales_type', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	
	public function getAllAreas() {
        $q = $this->db->get('restaurant_areas');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getAreasByID($id) {
        $q = $this->db->get_where('restaurant_areas', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	 public function getAreasByIDWithArea($id) {
		$this->db->select("restaurant_tables.*,restaurant_areas.name as floor");
		$this->db->join("restaurant_areas","restaurant_areas.id=area_id","left");
		$this->db->where("restaurant_tables.id",$id);
		$q=$this->db->get("restaurant_tables");
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	
	public function getAllKitchens() {
        $q = $this->db->get('restaurant_kitchens');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
	public function getAllRecipes() {
        $q = $this->db->get('recipe');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getKitchensByID($id) {
        $q = $this->db->get_where('restaurant_kitchens', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	
	public function getAllTables() {
        /*$q = $this->db->get('restaurant_tables');*/
        $this->db->select('*');		
		$this->db->order_by('name', 'asc');
		$q = $this->db->get('restaurant_tables');

        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }            
            return $data;
        }
        return FALSE;
    }

    public function getTablesByID($id) {
        $q = $this->db->get_where('restaurant_tables', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	
    public function getAllCategories() {
        $this->db->where('parent_id', NULL)->or_where('parent_id', 0)->order_by('name');
        $q = $this->db->get("categories");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
	
	
	public function getAllrecipeCategories() {

		if($this->pos_settings->categories_list_by ==0) {
			$this->db->where('type',0);
			$this->db->where('status',1)
			->where('(parent_id="null" or parent_id=0)')->order_by('id');
		}else{
			$this->db->where('type',0);
			$this->db->where('status',1)
			->where('(parent_id="null" or parent_id=0)')->order_by('name');
		}
        $q = $this->db->get("recipe_categories");        
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    	public function getAllrecipe_subCategories() {

		if($this->pos_settings->categories_list_by ==0) {
			$this->db->where('type',0)
			->where('parent_id !=0')->order_by('id');
		}else{
			$this->db->where('type',0)
			->where('parent_id !=0')->order_by('name');
		}
        $q = $this->db->get("recipe_categories");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
/*For Item Stock Report Purpose*/
	public function getAllrecipeCategories_for_report() {

		if($this->pos_settings->categories_list_by ==0) {
			//$this->db->where('type',0);
			$this->db->where('status',1);
			$this->db->where('(parent_id="null" or parent_id=0)')->order_by('id');
		}else{
			//$this->db->where('type',0);
			$this->db->where('status',1);
			$this->db->where('(parent_id="null" or parent_id=0)')->order_by('name');
		}
        $q = $this->db->get("recipe_categories");        
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    	public function getAllrecipe_subCategories_for_report() {

		if($this->pos_settings->categories_list_by ==0) {
			//$this->db->where('type',0)
			$this->db->where('parent_id !=0')->order_by('id');
		}else{
			//$this->db->where('type',0)
			$this->db->where('parent_id !=0')->order_by('name');
		}
        $q = $this->db->get("recipe_categories");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
/*For Item Stock Report Purpose*/
    public function getAllrecipeMappedCategories() {
		$this->db->select('recipe_categories.*');
		if($this->pos_settings->categories_list_by ==0) {
		$this->db->where('type',0)
		->where('(parent_id="null" or parent_id=0)')->order_by('id');
		}else{
		$this->db->where('type',0)
		->where('(parent_id="null" or parent_id=0)')->order_by('name');
		}
		$this->db->join('recipe_mapping_for_modify_bills','recipe_mapping_for_modify_bills.category_id=recipe_categories.id');
		$this->db->group_by('recipe_categories.id');
		$q = $this->db->get("recipe_categories");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
	  
            return $data;
        }
        return FALSE;
    }
    
	public function getAllrecipeCategories_withdays($sale_type) {

	/*SELECT RC.* FROM `srampos_recipe_categories` RC
	JOIN srampos_sale_items_mapping_details SID ON RC.id =SID. recipe_group_id
	JOIN srampos_sale_items_mapping_head H ON H.id =SID. sales_map_hd_id
	WHERE RC.type =0 AND (RC.parent_id = "null" or RC.parent_id =0) ORDER BY RC.id AND H.sale_type=1 AND H.days='Tuesday'*/

		$mydate=getdate(date("U"));
        $today = "$mydate[weekday]";
        $this->db->select('recipe_categories.*');
        $this->db->join('sale_items_mapping_details','recipe_categories.id=sale_items_mapping_details.recipe_group_id');
        $this->db->join('sale_items_mapping_head', 'sale_items_mapping_head.id=sale_items_mapping_details.sales_map_hd_id');
        $this->db->where('recipe_categories.type', 0);  
        $this->db->where('recipe_categories.status', 1);        
        $this->db->where('recipe_categories.parent_id', null);
        $this->db->or_where('recipe_categories.parent_id', 0);
        $this->db->where('sale_items_mapping_head.sale_type', $sale_type);
        $this->db->where('sale_items_mapping_head.days', $today);
        $this->db->group_by('recipe_categories.id');
        if($this->pos_settings->categories_list_by ==0) {
        	$this->db->order_by('recipe_categories.id');
        }else{
        	$this->db->order_by('recipe_categories.name');
        }
        $q = $this->db->get("recipe_categories"); 
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getrecipeSubCategories_withdays($category_id=NULL,$order_type) {
    	/*SELECT `srampos_recipe_categories`.* FROM `srampos_recipe_categories` JOIN `srampos_sale_items_mapping_details` ON `srampos_recipe_categories`.`id`=`srampos_sale_items_mapping_details`.`recipe_subgroup_id` JOIN `srampos_sale_items_mapping_head` ON `srampos_sale_items_mapping_head`.`id`=`srampos_sale_items_mapping_details`.`sales_map_hd_id` WHERE `srampos_recipe_categories`.`type` =0 AND  `srampos_recipe_categories`.`parent_id` =1 AND `srampos_sale_items_mapping_head`.`sale_type` = '1' AND `srampos_sale_items_mapping_head`.`days` = 'Tuesday'*/

    	$mydate=getdate(date("U"));
        $today = "$mydate[weekday]";
        $this->db->select('recipe_categories.*');
        $this->db->join('sale_items_mapping_details','recipe_categories.id=sale_items_mapping_details.recipe_subgroup_id');
        $this->db->join('sale_items_mapping_head', 'sale_items_mapping_head.id=sale_items_mapping_details.sales_map_hd_id');
        $this->db->where('recipe_categories.type', 0);  
        $this->db->where('recipe_categories.status', 1);    
        if($category_id){
        	$this->db->where('recipe_categories.parent_id', $category_id);    
        }        
        $this->db->where('sale_items_mapping_head.sale_type', $order_type);
        $this->db->where('sale_items_mapping_head.days', $today);
        $this->db->group_by('recipe_categories.id');
        $q = $this->db->get("recipe_categories"); 
// print_r($this->db->last_query());die;  
        /*$this->db->where('parent_id', $parent_id)->order_by('name');
        $q = $this->db->get("recipe_categories");*/
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }


	public function Getsalesrecipecategoriesmapping($days){
		$this->db->where('days', $days);
		$q = $this->db->get('sales_recipecategories_mapping');
        if ($q->num_rows() == 1) {
            return $q->row();
        }
		return FALSE;
	}
  public function checksaleitemsmapped_byid($id){

		$q = $this->db->get_where('sale_items_mapping_details', array('sales_map_hd_id' => $id), 1);		
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
	}

	public function checksaleitemsmapped($id,$sub_category){

	$this->db->select('recipe_id');
	$this->db->where('sales_map_hd_id',$id);
	$this->db->where('recipe_subgroup_id',$sub_category);	
	$q = $this->db->get('sale_items_mapping_details');	
	if($q->num_rows()>0){	    
	    return $q->row('recipe_id');
	}
	return false;
	}

	public function saleitemsmappeddaysbysaletype($sale_type){
		$this->db->select('days');
		$this->db->where('sale_type', $sale_type);
        $q = $this->db->get("sale_items_mapping_head");
        
        if ($q->num_rows() > 0) {            
                $data = $q->result();            
            return $data;
        }
        return FALSE;
	}

	public function getBBQmenuListCount(){
		$this->db->select('COUNT(bbq_menu_id) AS mqnu_count');
		$this->db->where('status', 1);
        $q = $this->db->get("bbq_menu");          
        if ($q->num_rows() > 0) {            
                $data = $q->row('mqnu_count');            
            return $data;
        }
        return FALSE;
	}	
public function getbbqmenucoverprice(){	
		$mydate=getdate(date("U"));
        $day = "$mydate[weekday]";
		$this->db->select("bbq_menu_day_wise_price.*");		
		$this->db->join('bbq_menu_day_wise_price', 'bbq_menu_day_wise_price.bbq_menu_id = bbq_menu.bbq_menu_id');
		$this->db->where('bbq_menu_day_wise_price.day', $day);					
		$q = $this->db->get('bbq_menu');			
		if($q->num_rows()>0){
		    return $q->row();
		}
}		

public function getbbqmenucoverprice_old(){
	$this->db->select('*');	
	$q = $this->db->get('bbq_menu');
	if($q->num_rows()>0){
	    return $q->row();
	}
    }

public function getBBQmenuList(){
		$this->db->select('*');
		$this->db->where('status', 1);
        $q = $this->db->get("bbq_menu");     
         if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    
	}	


    public function getSubCategories($parent_id) {
        $this->db->where('parent_id', $parent_id)->order_by('name');
        $q = $this->db->get("categories");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	public function getrecipeSubCategories($parent_id) {
        $this->db->where('parent_id', $parent_id)->where('status', 1)->order_by('name');
        $q = $this->db->get("recipe_categories");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    public function getrecipeMappedSubCategories($parent_id) {
       
	$this->db->select('recipe_categories.*');
		if($this->pos_settings->categories_list_by ==0) {
			$this->db->where('parent_id', $parent_id)->order_by('id');
		}else{
			$this->db->where('parent_id', $parent_id)->order_by('name');
		}
	$this->db->join('recipe_mapping_for_modify_bills','recipe_mapping_for_modify_bills.subcategory_id=recipe_categories.id');
	$this->db->group_by('recipe_categories.id');
        $q = $this->db->get("recipe_categories");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
	    
            return $data;
        }
        return FALSE;
    }
    function getAllrecipeSubCategories(){
	$categories = $this->getAllrecipeCategories();
	$subcategories = array();
	foreach($categories as $k => $category){
	    $categories[$k]->subcategories = $this->getrecipeSubCategories($category->id);
	}
	return $categories;
        return FALSE;
    }
function getAllrecipeMappedSubCategories(){
	$categories = $this->getAllrecipeMappedCategories();
	$subcategories = array();
	foreach($categories as $k => $category){
	    $categories[$k]->subcategories = $this->getrecipeMappedSubCategories($category->id);
	}
	return $categories;
        return FALSE;
    }
    public function getCategoryByID($id) {
        $q = $this->db->get_where('categories', array('id' => $id), 1);
		echo $this->db->last_query();
		die;
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	public function getrecipeCategoryByID($id) {
        $q = $this->db->get_where('recipe_categories', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getGiftCardByID($id) {
        $q = $this->db->get_where('gift_cards', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getGiftCardByNO($no) {
        $q = $this->db->get_where('gift_cards', array('card_no' => $no), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function updateInvoiceStatus() {
        $date = date('Y-m-d');
        $q = $this->db->get_where('invoices', array('status' => 'unpaid'));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                if ($row->due_date < $date) {
                    $this->db->update('invoices', array('status' => 'due'), array('id' => $row->id));
                }
            }
            $this->db->update('settings', array('update' => $date), array('setting_id' => '1'));
            return true;
        }
    }

    public function modal_js() {
        return '<script type="text/javascript">' . file_get_contents($this->data['assets'] . 'js/modal.js') . '</script>';
    }

    public function getReference($field) {
        $q = $this->db->get_where('order_ref', array('ref_id' => '1'), 1);
        if ($q->num_rows() > 0) {
            $ref = $q->row();
            switch ($field) {
                case 'so':
                    $prefix = $this->Settings->sales_prefix;
                    break;
                case 'pos':
                    $prefix = isset($this->Settings->sales_prefix) ? $this->Settings->sales_prefix . '/POS' : '';
                    break;
                case 'qu':
                    $prefix = $this->Settings->quote_prefix;
                    break;
                case 'po':
                    $prefix = $this->Settings->purchase_prefix;
                    break;
                case 'to':
                    $prefix = $this->Settings->transfer_prefix;
                    break;
                case 'do':
                    $prefix = $this->Settings->delivery_prefix;
                    break;
                case 'pay':
                    $prefix = $this->Settings->payment_prefix;
                    break;
                case 'ppay':
                    $prefix = $this->Settings->ppayment_prefix;
                    break;
                case 'ex':
                    $prefix = $this->Settings->expense_prefix;
                    break;
                case 're':
                    $prefix = $this->Settings->return_prefix;
                    break;
                case 'rep':
                    $prefix = $this->Settings->returnp_prefix;
                    break;
                case 'qa':
                    $prefix = $this->Settings->returnp_prefix;
                    break;
                default:
                    $prefix = '';
            }

            $ref_no = (!empty($prefix)) ? $prefix . '/' : '';

            if ($this->Settings->reference_format == 1) {
                $ref_no .= date('Y') . "/" . sprintf("%04s", $ref->{$field});
            } elseif ($this->Settings->reference_format == 2) {
                $ref_no .= date('Y') . "/" . date('m') . "/" . sprintf("%04s", $ref->{$field});
            } elseif ($this->Settings->reference_format == 3) {
                $ref_no .= sprintf("%04s", $ref->{$field});
            } else {
                $ref_no .= $this->getRandomReference();
            }

            return $ref_no;
        }
        return FALSE;
    }

    public function getRandomReference($len = 12) {
        $result = '';
        for ($i = 0; $i < $len; $i++) {
            $result .= mt_rand(0, 9);
        }

        if ($this->getSaleByReference($result)) {
            $this->getRandomReference();
        }

        return $result;
    }

    public function getSaleByReference($ref) {
        $this->db->like('reference_no', $ref, 'before');
        $q = $this->db->get('sales', 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function updateReference($field) {
        $q = $this->db->get_where('order_ref', array('ref_id' => '1'), 1);
        if ($q->num_rows() > 0) {
            $ref = $q->row();
            $this->db->update('order_ref', array($field => $ref->{$field} + 1), array('ref_id' => '1'));
            return TRUE;
        }
        return FALSE;
    }

    public function checkPermissions() {
	if($this->Settings->module_permission==2){
	    $q = $this->db->get_where('user_permissions', array('user_id' => $this->session->userdata('user_id')), 1);
	}else {
	    $q = $this->db->get_where('permissions', array('group_id' => $this->session->userdata('group_id')), 1);
	}
        
        if ($q->num_rows() > 0) {
            return $q->result_array();
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
    public function getGroupPermissions($id)
    {
        $q = $this->db->get_where('permissions', array('group_id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
   public function getGroupPermissionsarray($id)
    {
        $q = $this->db->get_where('permissions', array('group_id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->result_array();
        }
        return FALSE;
    }    
   public function getGroupPermissionsAlluseraccess($id)
    {
        $q = $this->db->get_where('permissions', array('group_id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row('pos-view_allusers_orders');
        }
        return FALSE;
    }

    public function getNotifications() {
        $date = date('Y-m-d H:i:s', time());
        $this->db->where("from_date <=", $date);
        $this->db->where("till_date >=", $date);
        if (!$this->Owner) {
            if ($this->Supplier) {
                $this->db->where('scope', 4);
            } elseif ($this->Customer) {
                $this->db->where('scope', 1)->or_where('scope', 3);
            } elseif (!$this->Customer && !$this->Supplier) {
                $this->db->where('scope', 2)->or_where('scope', 3);
            }
        }
        $q = $this->db->get("notifications");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }
	
	

    public function getUpcomingEvents() {
        $dt = date('Y-m-d');
        $this->db->where('start >=', $dt)->order_by('start')->limit(5);
        if ($this->Settings->restrict_calendar) {
            $this->db->where('user_id', $this->session->userdata('user_id'));
        }

        $q = $this->db->get('calendar');

        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getUserGroup($user_id = false) {
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        $group_id = $this->getUserGroupID($user_id);
        $q = $this->db->get_where('groups', array('id' => $group_id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getUserGroupID($user_id = false) {
        $user = $this->getUser($user_id);
        return $user->group_id;
    }

    public function getWarehouseProductsVariants($option_id, $warehouse_id = NULL) {
        if ($warehouse_id) {
            $this->db->where('warehouse_id', $warehouse_id);
        }
        $q = $this->db->get_where('warehouses_products_variants', array('option_id' => $option_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getPurchasedItem($clause) {
        $orderby = ($this->Settings->accounting_method == 1) ? 'asc' : 'desc';
        $this->db->order_by('date', $orderby);
        $this->db->order_by('purchase_id', $orderby);
        if (!isset($clause['option_id']) || empty($clause['option_id'])) {
            $this->db->group_start()->where('option_id', NULL)->or_where('option_id', 0)->group_end();
        }
        $q = $this->db->get_where('purchase_items', $clause);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function setPurchaseItem($clause, $qty) {
        if ($product = $this->getProductByID($clause['product_id'])) {
            if ($pi = $this->getPurchasedItem($clause)) {
                $quantity_balance = $pi->quantity_balance+$qty;
                return $this->db->update('purchase_items', array('quantity_balance' => $quantity_balance), array('id' => $pi->id));
            } else {
                $clause['product_code'] = $product->code;
                $clause['product_name'] = $product->name;
                $clause['purchase_id'] = $clause['transfer_id'] = $clause['item_tax'] = NULL;
                $clause['quantity'] = $clause['unit_quantity'] = $clause['net_unit_cost'] = $clause['subtotal'] = 0;
                $clause['status'] = 'received';
                $clause['date'] = date('Y-m-d');
                $clause['quantity_balance'] = $qty;
                $clause['option_id'] = !empty($clause['option_id']) && is_numeric($clause['option_id']) ? $clause['option_id'] : NULL;
                return $this->db->insert('purchase_items', $clause);
            }
        }
        return FALSE;
    }

    public function syncVariantQty($variant_id, $warehouse_id, $product_id = NULL) {
        $balance_qty = $this->getBalanceVariantQuantity($variant_id);
        $wh_balance_qty = $this->getBalanceVariantQuantity($variant_id, $warehouse_id);
        if ($this->db->update('product_variants', array('quantity' => $balance_qty), array('id' => $variant_id))) {
            if ($this->getWarehouseProductsVariants($variant_id, $warehouse_id)) {
                $this->db->update('warehouses_products_variants', array('quantity' => $wh_balance_qty), array('option_id' => $variant_id, 'warehouse_id' => $warehouse_id));
            } else {
                if($wh_balance_qty) {
                    $this->db->insert('warehouses_products_variants', array('quantity' => $wh_balance_qty, 'option_id' => $variant_id, 'warehouse_id' => $warehouse_id, 'product_id' => $product_id));
                }
            }
            return TRUE;
        }
        return FALSE;
    }

    public function getWarehouseProducts($product_id, $warehouse_id = NULL) {
        if ($warehouse_id) {
            $this->db->where('warehouse_id', $warehouse_id);
        }
        $q = $this->db->get_where('warehouses_products', array('product_id' => $product_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
	public function getWarehouserecipe($recipe_id, $warehouse_id = NULL) {
        if ($warehouse_id) {
            $this->db->where('warehouse_id', $warehouse_id);
        }
        $q = $this->db->get_where('warehouses_recipe', array('recipe_id' => $recipe_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function syncProductQty($product_id, $warehouse_id) {
        $balance_qty = $this->getBalanceQuantity($product_id);
        $wh_balance_qty = $this->getBalanceQuantity($product_id, $warehouse_id);
        if ($this->db->update('products', array('quantity' => $balance_qty), array('id' => $product_id))) {
            if ($this->getWarehouseProducts($product_id, $warehouse_id)) {
                $this->db->update('warehouses_products', array('quantity' => $wh_balance_qty), array('product_id' => $product_id, 'warehouse_id' => $warehouse_id));
            } else {
                if( ! $wh_balance_qty) { $wh_balance_qty = 0; }
                $product = $this->site->getProductByID($product_id);
                $this->db->insert('warehouses_products', array('quantity' => $wh_balance_qty, 'product_id' => $product_id, 'warehouse_id' => $warehouse_id, 'avg_cost' => $product->cost));
            }
            return TRUE;
        }
        return FALSE;
    }
	
	public function syncrecipetQty($recipe_id, $warehouse_id) {
        $balance_qty = $this->getBalanceQuantity($recipe_id);
        $wh_balance_qty = $this->getBalanceQuantity($recipe_id, $warehouse_id);
        if ($this->db->update('recipe', array('quantity' => $balance_qty), array('id' => $recipe_id))) {
            if ($this->getWarehouserecipe($product_id, $warehouse_id)) {
                $this->db->update('warehouses_recipe', array('quantity' => $wh_balance_qty), array('recipe_id' => $recipe_id, 'warehouse_id' => $warehouse_id));
            } else {
                if( ! $wh_balance_qty) { $wh_balance_qty = 0; }
                $product = $this->site->getrecipeByID($product_id);
                $this->db->insert('warehouses_recipe', array('quantity' => $wh_balance_qty, 'recipe_id' => $product_id, 'warehouse_id' => $warehouse_id, 'avg_cost' => $product->cost));
            }
            return TRUE;
        }
        return FALSE;
    }

    public function getSaleByID($id) {
        $q = $this->db->get_where('sales', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getSalePayments($sale_id) {
        $q = $this->db->get_where('payments', array('sale_id' => $sale_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function syncSalePayments($id) {
        $sale = $this->getSaleByID($id);
        if ($payments = $this->getSalePayments($id)) {
            $paid = 0;
            $grand_total = $sale->grand_total+$sale->rounding;
            foreach ($payments as $payment) {
                $paid += $payment->amount;
            }

            $payment_status = $paid == 0 ? 'pending' : $sale->payment_status;
            if ($this->sma->formatDecimal($grand_total) == $this->sma->formatDecimal($paid)) {
                $payment_status = 'paid';
            } elseif ($sale->due_date <= date('Y-m-d') && !$sale->sale_id) {
                $payment_status = 'due';
            } elseif ($paid != 0) {
                $payment_status = 'partial';
            }

            if ($this->db->update('sales', array('paid' => $paid, 'payment_status' => $payment_status), array('id' => $id))) {
                return true;
            }
        } else {
            $payment_status = ($sale->due_date <= date('Y-m-d')) ? 'due' : 'pending';
            if ($this->db->update('sales', array('paid' => 0, 'payment_status' => $payment_status), array('id' => $id))) {
                return true;
            }
        }

        return FALSE;
    }

    public function getPurchaseByID($id) {
        $q = $this->db->get_where('purchases', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getPurchasePayments($purchase_id) {
        $q = $this->db->get_where('payments', array('purchase_id' => $purchase_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function syncPurchasePayments($id) {
        $purchase = $this->getPurchaseByID($id);
        $paid = 0;
        if ($payments = $this->getPurchasePayments($id)) {
            foreach ($payments as $payment) {
                $paid += $payment->amount;
            }
        }

        $payment_status = $paid <= 0 ? 'pending' : $purchase->payment_status;
        if ($this->sma->formatDecimal($purchase->grand_total) > $this->sma->formatDecimal($paid) && $paid > 0) {
            $payment_status = 'partial';
        } elseif ($this->sma->formatDecimal($purchase->grand_total) <= $this->sma->formatDecimal($paid)) {
            $payment_status = 'paid';
        }

        if ($this->db->update('purchases', array('paid' => $paid, 'payment_status' => $payment_status), array('id' => $id))) {
            return true;
        }

        return FALSE;
    }

    private function getBalanceQuantity($product_id, $warehouse_id = NULL) {
        $this->db->select('SUM(COALESCE(quantity_balance, 0)) as stock', False);
        $this->db->where('product_id', $product_id)->where('quantity_balance !=', 0);
        if ($warehouse_id) {
            $this->db->where('warehouse_id', $warehouse_id);
        }
        $this->db->group_start()->where('status', 'received')->or_where('status', 'partial')->group_end();
        $q = $this->db->get('purchase_items');
        if ($q->num_rows() > 0) {
            $data = $q->row();
            return $data->stock;
        }
        return 0;
    }

    private function getBalanceVariantQuantity($variant_id, $warehouse_id = NULL) {
        $this->db->select('SUM(COALESCE(quantity_balance, 0)) as stock', False);
        $this->db->where('option_id', $variant_id)->where('quantity_balance !=', 0);
        if ($warehouse_id) {
            $this->db->where('warehouse_id', $warehouse_id);
        }
        $this->db->group_start()->where('status', 'received')->or_where('status', 'partial')->group_end();
        $q = $this->db->get('purchase_items');
        if ($q->num_rows() > 0) {
            $data = $q->row();
            return $data->stock;
        }
        return 0;
    }

    public function calculateAVCost($recipe_id, $warehouse_id, $net_unit_price, $unit_price, $quantity, $recipe_name, $option_id, $item_quantity) {
        $recipe = $this->getrecipeByID($recipe_id);
        $real_item_qty = $quantity;
        $wp_details = $this->getWarehouserecipeone($warehouse_id, $recipe_id);
        $con = $wp_details ? $wp_details->avg_cost : $recipe->cost;
        $tax_rate = $this->getTaxRateByID($recipe->tax_rate);
        $ctax = $this->calculateTax($recipe, $tax_rate, $con);
        if ($recipe->tax_method) {
            $avg_net_unit_cost = $con;
            $avg_unit_cost = ($con + $ctax['amount']);
        } else {
            $avg_unit_cost = $con;
            $avg_net_unit_cost = ($con - $ctax['amount']);
        }

        if ($pis = $this->getPurchasedItems($recipe_id, $warehouse_id, $option_id)) {
            $cost_row = array();
            $quantity = $item_quantity;
            $balance_qty = $quantity;
            foreach ($pis as $pi) {
                if (!empty($pi) && $pi->quantity > 0 && $balance_qty <= $quantity && $quantity > 0) {
                    if ($pi->quantity_balance >= $quantity && $quantity > 0) {
                        $balance_qty = $pi->quantity_balance - $quantity;
                        $cost_row = array('date' => date('Y-m-d'), 'recipe_id' => $recipe_id, 'sale_item_id' => 'sale_items.id', 'purchase_item_id' => $pi->id, 'quantity' => $quantity, 'purchase_net_unit_cost' => $avg_net_unit_cost, 'purchase_unit_cost' => $avg_unit_cost, 'sale_net_unit_price' => $net_unit_price, 'sale_unit_price' => $unit_price, 'quantity_balance' => $balance_qty, 'inventory' => 1, 'option_id' => $option_id);
                        $quantity = 0;
                    } elseif ($quantity > 0) {
                        $quantity = $quantity - $pi->quantity_balance;
                        $balance_qty = $quantity;
                        $cost_row = array('date' => date('Y-m-d'), 'recipe_id' => $recipe_id, 'sale_item_id' => 'sale_items.id', 'purchase_item_id' => $pi->id, 'quantity' => $pi->quantity_balance, 'purchase_net_unit_cost' => $avg_net_unit_cost, 'purchase_unit_cost' => $avg_unit_cost, 'sale_net_unit_price' => $net_unit_price, 'sale_unit_price' => $unit_price, 'quantity_balance' => 0, 'inventory' => 1, 'option_id' => $option_id);
                    }
                }
                if (empty($cost_row)) {
                    break;
                }
                $cost[] = $cost_row;
                if ($quantity == 0) {
                    break;
                }
            }
        }
        if ($quantity > 0 && !$this->Settings->overselling) {
            $this->session->set_flashdata('error', sprintf(lang("quantity_out_of_stock_for_%s"), ($pi->recipe_name ? $pi->recipe_name : $recipe_name)));
            redirect($_SERVER["HTTP_REFERER"]);
        } elseif ($quantity > 0) {
            $cost[] = array('date' => date('Y-m-d'), 'recipe_id' => $recipe_id, 'sale_item_id' => 'sale_items.id', 'purchase_item_id' => NULL, 'quantity' => $real_item_qty, 'purchase_net_unit_cost' => $avg_net_unit_cost, 'purchase_unit_cost' => $avg_unit_cost, 'sale_net_unit_price' => $net_unit_price, 'sale_unit_price' => $unit_price, 'quantity_balance' => NULL, 'overselling' => 1, 'inventory' => 1);
            $cost[] = array('pi_overselling' => 1, 'recipe_id' => $recipe_id, 'quantity_balance' => (0 - $quantity), 'warehouse_id' => $warehouse_id, 'option_id' => $option_id);
        }
        return $cost;
    }

    public function calculateCost($product_id, $warehouse_id, $net_unit_price, $unit_price, $quantity, $product_name, $option_id, $item_quantity) {
        $pis = $this->getPurchasedItems($product_id, $warehouse_id, $option_id);
        $real_item_qty = $quantity;
        $quantity = $item_quantity;
        $balance_qty = $quantity;
        foreach ($pis as $pi) {
            $cost_row = NULL;
            if (!empty($pi) && $balance_qty <= $quantity && $quantity > 0) {
                $purchase_unit_cost = $pi->unit_cost ? $pi->unit_cost : ($pi->net_unit_cost + ($pi->item_tax / $pi->quantity));
                if ($pi->quantity_balance >= $quantity && $quantity > 0) {
                    $balance_qty = $pi->quantity_balance - $quantity;
                    $cost_row = array('date' => date('Y-m-d'), 'product_id' => $product_id, 'sale_item_id' => 'sale_items.id', 'purchase_item_id' => $pi->id, 'quantity' => $quantity, 'purchase_net_unit_cost' => $pi->net_unit_cost, 'purchase_unit_cost' => $purchase_unit_cost, 'sale_net_unit_price' => $net_unit_price, 'sale_unit_price' => $unit_price, 'quantity_balance' => $balance_qty, 'inventory' => 1, 'option_id' => $option_id);
                    $quantity = 0;
                } elseif ($quantity > 0) {
                    $quantity = $quantity - $pi->quantity_balance;
                    $balance_qty = $quantity;
                    $cost_row = array('date' => date('Y-m-d'), 'product_id' => $product_id, 'sale_item_id' => 'sale_items.id', 'purchase_item_id' => $pi->id, 'quantity' => $pi->quantity_balance, 'purchase_net_unit_cost' => $pi->net_unit_cost, 'purchase_unit_cost' => $purchase_unit_cost, 'sale_net_unit_price' => $net_unit_price, 'sale_unit_price' => $unit_price, 'quantity_balance' => 0, 'inventory' => 1, 'option_id' => $option_id);
                }
            }
            $cost[] = $cost_row;
            if ($quantity == 0) {
                break;
            }
        }
        if ($quantity > 0) {
            $this->session->set_flashdata('error', sprintf(lang("quantity_out_of_stock_for_%s"), (isset($pi->product_name) ? $pi->product_name : $product_name)));
            redirect($_SERVER["HTTP_REFERER"]);
        }
        return $cost;
    }

    public function getPurchasedItems($product_id, $warehouse_id, $option_id = NULL) {
        $orderby = ($this->Settings->accounting_method == 1) ? 'asc' : 'desc';
        $this->db->select('id, quantity, quantity_balance, net_unit_cost, unit_cost, item_tax');
        $this->db->where('product_id', $product_id)->where('warehouse_id', $warehouse_id)->where('quantity_balance !=', 0);
        if (!isset($option_id) || empty($option_id)) {
            $this->db->group_start()->where('option_id', NULL)->or_where('option_id', 0)->group_end();
        } else {
            $this->db->where('option_id', $option_id);
        }
        $this->db->group_start()->where('status', 'received')->or_where('status', 'partial')->group_end();
        $this->db->group_by('id');
        $this->db->order_by('date', $orderby);
        $this->db->order_by('purchase_id', $orderby);
        $q = $this->db->get('purchase_items');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getProductComboItems($pid, $warehouse_id = NULL) {
        $this->db->select('products.id as id, combo_items.item_code as code, combo_items.quantity as qty, products.name as name, products.type as type, combo_items.unit_price as unit_price, warehouses_products.quantity as quantity')
            ->join('products', 'products.code=combo_items.item_code', 'left')
            ->join('warehouses_products', 'warehouses_products.product_id=products.id', 'left')
            ->group_by('combo_items.id');
        if($warehouse_id) {
            $this->db->where('warehouses_products.warehouse_id', $warehouse_id);
        }
        $q = $this->db->get_where('combo_items', array('combo_items.product_id' => $pid));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
        return FALSE;
    }
	
	 public function getrecipeComboItems($pid, $warehouse_id = NULL) {
        $this->db->select('recipe.id as id, recipe_combo_items.combo_item_id as code, recipe.name as name, recipe.type as type, recipe_combo_items.unit_price as unit_price, warehouses_recipe.quantity as quantity')
            ->join('recipe', 'recipe.code=recipe_combo_items.item_code', 'left')
            ->join('warehouses_recipe', 'warehouses_recipe.recipe_id=recipe.id', 'left')
            ->group_by('recipe_combo_items.id');
        if($warehouse_id) {
            $this->db->where('warehouses_products.warehouse_id', $warehouse_id);
        }
        $q = $this->db->get_where('recipe_combo_items', array('recipe_combo_items.recipe_id' => $pid));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
        return FALSE;
    }

    public function item_costing($item, $pi = NULL) {
        $item_quantity = $pi ? $item['aquantity'] : $item['quantity'];
        if (!isset($item['option_id']) || empty($item['option_id']) || $item['option_id'] == 'null') {
            $item['option_id'] = NULL;
        }

        if ($this->Settings->accounting_method != 2 && !$this->Settings->overselling) {

            if ($this->getProductByID($item['product_id'])) {
                if ($item['product_type'] == 'standard') {
                    $unit = $this->getUnitByID($item['product_unit_id']);
                    $item['net_unit_price'] = $this->convertToBase($unit, $item['net_unit_price']);
                    $item['unit_price'] = $this->convertToBase($unit, $item['unit_price']);
                    $cost = $this->calculateCost($item['product_id'], $item['warehouse_id'], $item['net_unit_price'], $item['unit_price'], $item['quantity'], $item['product_name'], $item['option_id'], $item_quantity);
                } elseif ($item['product_type'] == 'combo') {
                    $combo_items = $this->getProductComboItems($item['product_id'], $item['warehouse_id']);
                    foreach ($combo_items as $combo_item) {
                        $pr = $this->getProductByCode($combo_item->code);
                        if ($pr->tax_rate) {
                            $pr_tax = $this->getTaxRateByID($pr->tax_rate);
                            if ($pr->tax_method) {
                                $item_tax = $this->sma->formatDecimal((($combo_item->unit_price) * $pr_tax->rate) / (100 + $pr_tax->rate));
                                $net_unit_price = $combo_item->unit_price - $item_tax;
                                $unit_price = $combo_item->unit_price;
                            } else {
                                $item_tax = $this->sma->formatDecimal((($combo_item->unit_price) * $pr_tax->rate) / 100);
                                $net_unit_price = $combo_item->unit_price;
                                $unit_price = $combo_item->unit_price + $item_tax;
                            }
                        } else {
                            $net_unit_price = $combo_item->unit_price;
                            $unit_price = $combo_item->unit_price;
                        }
                        if ($pr->type == 'standard') {
                            $cost[] = $this->calculateCost($pr->id, $item['warehouse_id'], $net_unit_price, $unit_price, ($combo_item->qty * $item['quantity']), $pr->name, NULL, $item_quantity);
                        } else {
                            $cost[] = array(array('date' => date('Y-m-d'), 'product_id' => $pr->id, 'sale_item_id' => 'sale_items.id', 'purchase_item_id' => NULL, 'quantity' => ($combo_item->qty * $item['quantity']), 'purchase_net_unit_cost' => 0, 'purchase_unit_cost' => 0, 'sale_net_unit_price' => $combo_item->unit_price, 'sale_unit_price' => $combo_item->unit_price, 'quantity_balance' => NULL, 'inventory' => NULL));
                        }
                    }
                } else {
                    $cost = array(array('date' => date('Y-m-d'), 'product_id' => $item['product_id'], 'sale_item_id' => 'sale_items.id', 'purchase_item_id' => NULL, 'quantity' => $item['quantity'], 'purchase_net_unit_cost' => 0, 'purchase_unit_cost' => 0, 'sale_net_unit_price' => $item['net_unit_price'], 'sale_unit_price' => $item['unit_price'], 'quantity_balance' => NULL, 'inventory' => NULL));
                }
            } elseif ($item['product_type'] == 'manual') {
                $cost = array(array('date' => date('Y-m-d'), 'product_id' => $item['product_id'], 'sale_item_id' => 'sale_items.id', 'purchase_item_id' => NULL, 'quantity' => $item['quantity'], 'purchase_net_unit_cost' => 0, 'purchase_unit_cost' => 0, 'sale_net_unit_price' => $item['net_unit_price'], 'sale_unit_price' => $item['unit_price'], 'quantity_balance' => NULL, 'inventory' => NULL));
            }

        } else {

            if ($this->getrecipeByID($item['recipe_id'])) {
                if ($item['recipe_type'] == 'standard') {
                    $cost = $this->calculateAVCost($item['recipe_id'], $item['warehouse_id'], $item['net_unit_price'], $item['unit_price'], $item['quantity'], $item['recipe_name'], $item['option_id'], $item_quantity);
                } elseif ($item['recipe_type'] == 'combo') {
                    $combo_items = $this->getrecipeComboItems($item['recipe_id'], $item['warehouse_id']);
                    foreach ($combo_items as $combo_item) {
                        $pr = $this->getrecipeByCode($combo_item->code);
                        if ($pr->tax_rate) {
                            $pr_tax = $this->getTaxRateByID($pr->tax_rate);
                            if ($pr->tax_method) {
                                $item_tax = $this->sma->formatDecimal((($combo_item->unit_price) * $pr_tax->rate) / (100 + $pr_tax->rate));
                                $net_unit_price = $combo_item->unit_price - $item_tax;
                                $unit_price = $combo_item->unit_price;
                            } else {
                                $item_tax = $this->sma->formatDecimal((($combo_item->unit_price) * $pr_tax->rate) / 100);
                                $net_unit_price = $combo_item->unit_price;
                                $unit_price = $combo_item->unit_price + $item_tax;
                            }
                        } else {
                            $net_unit_price = $combo_item->unit_price;
                            $unit_price = $combo_item->unit_price;
                        }
                        $cost[] = $this->calculateAVCost($combo_item->id, $item['warehouse_id'], $net_unit_price, $unit_price, ($combo_item->qty * $item['quantity']), $item['recipe_name'], $item['option_id'], $item_quantity);
                    }
                } else {
                    $cost = array(array('date' => date('Y-m-d'), 'recipe_id' => $item['recipe_id'], 'sale_item_id' => 'sale_items.id', 'purchase_item_id' => NULL, 'quantity' => $item['quantity'], 'purchase_net_unit_cost' => 0, 'purchase_unit_cost' => 0, 'sale_net_unit_price' => $item['net_unit_price'], 'sale_unit_price' => $item['unit_price'], 'quantity_balance' => NULL, 'inventory' => NULL));
                }
            } elseif ($item['recipe_type'] == 'manual') {
                $cost = array(array('date' => date('Y-m-d'), 'recipe_id' => $item['recipe_id'], 'sale_item_id' => 'sale_items.id', 'purchase_item_id' => NULL, 'quantity' => $item['quantity'], 'purchase_net_unit_cost' => 0, 'purchase_unit_cost' => 0, 'sale_net_unit_price' => $item['net_unit_price'], 'sale_unit_price' => $item['unit_price'], 'quantity_balance' => NULL, 'inventory' => NULL));
            }

        }
        return $cost;
    }

    public function costing($items) {
        $citems = array();
		
		
        foreach ($items as $item) {
            $option = (isset($item['option_id']) && !empty($item['option_id']) && $item['option_id'] != 'null' && $item['option_id'] != 'false') ? $item['option_id'] : '';
			
			
            $pr = $this->getrecipeByID($item['recipe_id']);
			
            $item['option_id'] = $option;
			
			
            if ($pr && $pr->type == 'standard') {
				
				
                if (isset($citems['p' . $item['recipe_id'] . 'o' . $item['option_id']])) {
                    $citems['p' . $item['recipe_id'] . 'o' . $item['option_id']]['aquantity'] += $item['quantity'];
                } else {
                    $citems['p' . $item['recipe_id'] . 'o' . $item['option_id']] = $item;
                    $citems['p' . $item['recipe_id'] . 'o' . $item['option_id']]['aquantity'] = $item['quantity'];
                }
            } elseif ($pr && $pr->type == 'combo') {
                $wh = $this->Settings->overselling ? NULL : $item['warehouse_id'];
                $combo_items = $this->getrecipeComboItems($item['recipe_id'], $wh);
				
				
			
                foreach ($combo_items as $combo_item) {
					
                    if ($combo_item->type == 'standard') {
                        if (isset($citems['p' . $combo_item->id . 'o' . $item['option_id']])) {
                            $citems['p' . $combo_item->id . 'o' . $item['option_id']]['aquantity'] += ($combo_item->qty*$item['quantity']);
							
							
                        } else {
                            $cpr = $this->getrecipeByID($combo_item->id);
							
							
							
                            if ($cpr->tax_rate) {
                                $cpr_tax = $this->getTaxRateByID($cpr->tax_rate);
                                if ($cpr->tax_method) {
                                    $item_tax = $this->sma->formatDecimal((($combo_item->unit_price) * $cpr_tax->rate) / (100 + $cpr_tax->rate));
                                    $net_unit_price = $combo_item->unit_price - $item_tax;
                                    $unit_price = $combo_item->unit_price;
                                } else {
                                    $item_tax = $this->sma->formatDecimal((($combo_item->unit_price) * $cpr_tax->rate) / 100);
                                    $net_unit_price = $combo_item->unit_price;
                                    $unit_price = $combo_item->unit_price + $item_tax;
                                }
                            } else {
                                $net_unit_price = $combo_item->unit_price;
                                $unit_price = $combo_item->unit_price;
                            }
                            $cproduct = array('recipe_id' => $combo_item->id, 'recipe_name' => $cpr->name, 'recipe_type' => $combo_item->type, 'quantity' => ($combo_item->qty*$item['quantity']), 'net_unit_price' => $net_unit_price, 'unit_price' => $unit_price, 'warehouse_id' => $item['warehouse_id'], 'item_tax' => $item_tax, 'tax_rate_id' => $cpr->tax_rate, 'tax' => ($cpr_tax->type == 1 ? $cpr_tax->rate.'%' : $cpr_tax->rate), 'option_id' => NULL, 'recipe_unit_id' => $cpr->unit);
                            $citems['p' . $combo_item->id . 'o' . $item['option_id']] = $cproduct;
                            $citems['p' . $combo_item->id . 'o' . $item['option_id']]['aquantity'] = ($combo_item->qty*$item['quantity']);
                        }
                    }
                }
            }
			
			
			
        }
		
         //$this->sma->print_arrays($combo_items, $citems);
		 
        $cost = array();
        foreach ($citems as $item) {
            $item['aquantity'] = $citems['p' . $item['recipe_id'] . 'o' . $item['option_id']]['aquantity'];
            $cost[] = $this->item_costing($item, TRUE);
        }
        return $cost;
    }

    public function syncQuantity($sale_id = NULL, $purchase_id = NULL, $oitems = NULL, $recipe_id = NULL) {
        if ($sale_id) {

            $sale_items = $this->getAllSaleItems($sale_id);
            foreach ($sale_items as $item) {
                if ($item->recipe_type == 'standard') {
                    $this->syncProductQty($item->product_id, $item->warehouse_id);
                    if (isset($item->option_id) && !empty($item->option_id)) {
                        $this->syncVariantQty($item->option_id, $item->warehouse_id, $item->recipe_id);
                    }
                } elseif ($item->recipe_type == 'combo') {
                    $wh = $this->Settings->overselling ? NULL : $item->warehouse_id;
                    $combo_items = $this->getrecipeComboItems($item->recipe_id, $wh);
                    foreach ($combo_items as $combo_item) {
                        if($combo_item->type == 'standard') {
                            $this->syncProductQty($combo_item->id, $item->warehouse_id);
                        }
                    }
                }
            }

        } elseif ($purchase_id) {

            $purchase_items = $this->getAllPurchaseItems($purchase_id);
            foreach ($purchase_items as $item) {
                $this->syncProductQty($item->product_id, $item->warehouse_id);
                if (isset($item->option_id) && !empty($item->option_id)) {
                    $this->syncVariantQty($item->option_id, $item->warehouse_id, $item->product_id);
                }
            }

        } elseif ($oitems) {

            foreach ($oitems as $item) {
                if (isset($item->product_type)) {
                    if ($item->product_type == 'standard') {
                        $this->syncProductQty($item->product_id, $item->warehouse_id);
                        if (isset($item->option_id) && !empty($item->option_id)) {
                            $this->syncVariantQty($item->option_id, $item->warehouse_id, $item->product_id);
                        }
                    } elseif ($item->product_type == 'combo') {
                        $combo_items = $this->getProductComboItems($item->product_id, $item->warehouse_id);
                        foreach ($combo_items as $combo_item) {
                            if($combo_item->type == 'standard') {
                                $this->syncProductQty($combo_item->id, $item->warehouse_id);
                            }
                        }
                    }
                } else {
                    $this->syncProductQty($item->product_id, $item->warehouse_id);
                    if (isset($item->option_id) && !empty($item->option_id)) {
                        $this->syncVariantQty($item->option_id, $item->warehouse_id, $item->product_id);
                    }
                }
            }

        } elseif ($product_id) {
            $warehouses = $this->getAllWarehouses();
            foreach ($warehouses as $warehouse) {
                $this->syncProductQty($product_id, $warehouse->id);
                if ($product_variants = $this->getProductVariants($product_id)) {
                    foreach ($product_variants as $pv) {
                        $this->syncVariantQty($pv->id, $warehouse->id, $product_id);
                    }
                }
            }
        }
    }

    public function getProductVariants($product_id) {
        $q = $this->db->get_where('product_variants', array('product_id' => $product_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getAllSaleItems($sale_id) {
        $q = $this->db->get_where('sale_items', array('sale_id' => $sale_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getAllPurchaseItems($purchase_id) {
        $q = $this->db->get_where('purchase_items', array('purchase_id' => $purchase_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
     public function getAllPurchasesOrderItems($purchase_order_id) {
        $q = $this->db->get_where('purchase_order_items', array('purchase_order_id' => $purchase_order_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    

    public function getAllQuotationItems($quotes_id) {

        $q = $this->db->get_where('quote_items', array('quote_id' => $quotes_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function syncPurchaseItems($data = array()) {
        if (!empty($data)) {
            foreach ($data as $items) {
                foreach ($items as $item) {
                    if (isset($item['pi_overselling'])) {
                        unset($item['pi_overselling']);
                        $option_id = (isset($item['option_id']) && !empty($item['option_id'])) ? $item['option_id'] : NULL;
                        $clause = array('purchase_id' => NULL, 'transfer_id' => NULL, 'product_id' => $item['product_id'], 'warehouse_id' => $item['warehouse_id'], 'option_id' => $option_id);
                        if ($pi = $this->getPurchasedItem($clause)) {
                            $quantity_balance = $pi->quantity_balance + $item['quantity_balance'];
                            $this->db->update('purchase_items', array('quantity_balance' => $quantity_balance), array('id' => $pi->id));
                        } else {
                            $clause['quantity'] = 0;
                            $clause['item_tax'] = 0;
                            $clause['quantity_balance'] = $item['quantity_balance'];
                            $clause['status'] = 'received';
                            $clause['option_id'] = !empty($clause['option_id']) && is_numeric($clause['option_id']) ? $clause['option_id'] : NULL;
                            $this->db->insert('purchase_items', $clause);
                        }
                    } else {
                        if ($item['inventory']) {
                            $this->db->update('purchase_items', array('quantity_balance' => $item['quantity_balance']), array('id' => $item['purchase_item_id']));
                        }
                    }
                }
            }
            return TRUE;
        }
        return FALSE;
    }

    public function getProductByCode($code) {
        $q = $this->db->get_where('products', array('code' => $code), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	
	public function getrecipeByCode($code) {
        $q = $this->db->get_where('recipe', array('code' => $code), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function check_customer_deposit($customer_id, $amount) {
        $customer = $this->getCompanyByID($customer_id);
        return $customer->deposit_amount >= $amount;
    }

    public function getWarehouseProduct($warehouse_id, $product_id) {
        $q = $this->db->get_where('warehouses_products', array('product_id' => $product_id, 'warehouse_id' => $warehouse_id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	
	public function getWarehouserecipeone($warehouse_id, $recipe_id) {
        $q = $this->db->get_where('warehouses_recipe', array('recipe_id' => $recipe_id, 'warehouse_id' => $warehouse_id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	
    public function getAllBaseUnits() {
        $q = $this->db->get_where("units", array('base_unit' => NULL));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
	 public function getAllUnits()
    {
        $q = $this->db->get('units');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	

    public function getUnitsByBUID($base_unit) {
        $this->db->where('id', $base_unit)->or_where('base_unit', $base_unit)
        ->group_by('id')->order_by('id asc');
        $q = $this->db->get("units");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getUnitByID($id) {
        $q = $this->db->get_where("units", array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	 public function getUnitByname($name) {
		$this->db->select("*");
		$this->db->or_where("code",$name);
		$this->db->or_where("name",$name);
		$q=$this->db->get("units");
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

	public function GetIDBycostomerDiscounts($customer_discount_id){
		$this->db->select('diccounts_for_customer.id, group_discount.discount_val');
		$this->db->join('group_discount', 'group_discount.cus_discount_id = diccounts_for_customer.id ' );
		$this->db->where('diccounts_for_customer.status', 1);
		$this->db->where('diccounts_for_customer.id', $customer_discount_id);
		$this->db->group_by('diccounts_for_customer.id');
        $q = $this->db->get('diccounts_for_customer');
		 if ($q->num_rows() > 0) {
            return $q->row('discount_val').'%';
        }
		return FALSE;
	}
	
	public function GetIDByBBQDiscounts($bbq_discount_id){
		$this->db->select('diccounts_for_bbq.*');
		$this->db->where('diccounts_for_bbq.status', 1);
		$this->db->where('diccounts_for_bbq.id', $bbq_discount_id);
		$this->db->group_by('diccounts_for_bbq.id');
        $q = $this->db->get('diccounts_for_bbq');
		 if ($q->num_rows() > 0) {
            return $q->row('discount').'%';
        }
		return FALSE;
	}
	
    public function GetAllcostomerDiscounts() {
    	$this->db->where('status', 1);
        $q = $this->db->get('diccounts_for_customer');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

   /*public function GetAllcostomerDiscounts(){
        $u_dis = $this->is_uniqueDiscountExist();
	if(!empty($u_dis)){
	    return FALSE;
	}
  	$date = date('Y-m-d');
        $current_time = date('H:i:s');
        $mydate=getdate(date("U"));
        $today = "$mydate[weekday]";
    	
    	$myQuery = "SELECT *
        FROM " . $this->db->dbprefix('diccounts_for_customer') . "         
            WHERE   FIND_IN_SET('".$today."' ,week_days)  AND status =1";            
        $q = $this->db->query($myQuery);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }*/

public function getRecipeGroupId($recipe_id) {

	   $this->db->select('category_id');
    	$this->db->where('id', $recipe_id);
        $q = $this->db->get('recipe');

        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data = $row->category_id;
            }            
            return $data;
        }
        return FALSE;
    }

public function getCalculateCustomerDiscount($recipe_id) {

    	$this->db->where('status', 1);
        $q = $this->db->get('diccounts_for_customer');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getCustomerDiscount($id) {
        $q = $this->db->get_where("diccounts_for_customer", array('id' => $id,'status' => 1));
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getPriceGroupByID($id) {
        $q = $this->db->get_where('price_groups', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getRecipeGroupPrice($product_id, $group_id) {
        $q = $this->db->get_where('recipe_prices', array('price_group_id' => $group_id, 'recipe_id' => $product_id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getAllBrands() {
        $q = $this->db->get("brands");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
	public function getAllSuppliers() {
		$this->db->where('group_name', 'supplier');
        $q = $this->db->get("companies");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
	 public function getCompanyOrderByID($id) {
        $q = $this->db->get_where('companies', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getBrandByID($id) {
        $q = $this->db->get_where('brands', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	
	public function getKitchenByID($id) {
        $q = $this->db->get_where('restaurant_kitchens', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	public function getAreaByID($id) {
        $q = $this->db->get_where('restaurant_areas', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	public function getTableByID($id) {
        $q = $this->db->get_where('restaurant_tables', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	public function getDiscountByID($id) {
        $q = $this->db->get_where('discount', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	
	

    public function convertToBase($unit, $value) {
        switch($unit->operator) {
            case '*':
                return $value / $unit->operation_value;
                break;
            case '/':
                return $value * $unit->operation_value;
                break;
            case '+':
                return $value - $unit->operation_value;
                break;
            case '-':
                return $value + $unit->operation_value;
                break;
            default:
                return $value;
        }
    }

    function calculateTax($recipe_details = NULL, $tax_details, $custom_value = NULL, $c_on = NULL) {
        $value = $custom_value ? $custom_value : (($c_on == 'cost') ? $recipe_details->cost : $recipe_details->price);
        $tax_amount = 0; $tax = 0;
        if ($tax_details && $tax_details->type == 1 && $tax_details->rate != 0) {
			
            if ($recipe_details && $recipe_details->tax_method == 1) {
                $tax_amount = $this->sma->formatDecimal((($value) * $tax_details->rate) / 100);
                $tax = $this->sma->formatDecimal($tax_details->rate, 0) . "%";
            } else {
                $tax_amount = $this->sma->formatDecimal((($value) * $tax_details->rate) / (100 + $tax_details->rate));
                $tax = $this->sma->formatDecimal($tax_details->rate, 0) . "%";
            }
        } elseif ($tax_details && $tax_details->type == 2) {
            $tax_amount = $this->sma->formatDecimal($tax_details->rate);
            $tax = $this->sma->formatDecimal($tax_details->rate, 0);
        }
		if($tax_details) {
			return array('id' => $tax_details->id, 'tax' => $tax, 'amount' => $tax_amount);
		} else {
				return FALSE;
		}
    }

    function discountMultiple($id = NULL){
        //$id =1;
        $date = date('Y-m-d');
        $current_time = date('H:i:s');
        $mydate=getdate(date("U"));
        $today = "$mydate[weekday]";
        $q = $this->db->get_where('recipe', array('id' => $id));
        if ($q->num_rows() > 0) {
            $row = $q->row();
	    
		
		$uniqueDaysDis = $this->is_uniqueDiscountExist();
		if(!empty($uniqueDaysDis)){
		    if(@$uniqueDaysDis->type=="discount_simple"){
		    
		
		//echo '<pre>';print_R($uniqueDaysDis));exit;
		
		if(!empty($row->id)){
		  $uniqueQuery = "SELECT max(CASE 
			  WHEN DI.item_type = 'in_list'  
			    AND DI.item_method = 'item_product'             			    
			    AND  FIND_IN_SET('".$row->id."' ,DI.item_type_id) 
			  THEN CONCAT(D.id,',',D.discount, ',', D.discount_type) 
			  ELSE null END) AS DateDiscount
		  FROM " . $this->db->dbprefix('discount_item_list') . " DIL 
		  JOIN " . $this->db->dbprefix('discount_items') . " DI ON DI.id = DIL.discount_item_id 
		  JOIN " . $this->db->dbprefix('discounts') . " D ON D.id = DI.discount_id 
		  WHERE D.id=".$uniqueDaysDis->discount_id." AND DIL.item_id = ".$row->id." AND D.type='discount_simple' AND D.discount_status=1";
		$uniqueDaysproduct = $this->db->query($uniqueQuery);
		$only_discount = $uniqueDaysproduct->row();
		
		//echo '<pre>';print_R($only_discount);echo 66;exit;
		if(empty($only_discount->DateDiscount)){ //echo 5;
		     $uniqueQuery = "SELECT max(CASE 
                      WHEN DI.item_type = 'not_in_list'  
                        AND DI.item_method = 'item_product'             
                        
			AND !FIND_IN_SET('".$row->id."',DI.item_type_id)
                      THEN CONCAT(D.id,',',D.discount, ',', D.discount_type) 
                      ELSE null END) AS DateDiscount
              FROM " . $this->db->dbprefix('discount_item_list') . " DIL 
              JOIN " . $this->db->dbprefix('discount_items') . " DI ON DI.id = DIL.discount_item_id 
              JOIN " . $this->db->dbprefix('discounts') . " D ON D.id = DI.discount_id 
              
              WHERE D.id=".$uniqueDaysDis->discount_id." AND D.type='discount_simple' AND D.discount_status=1";
		    $uniqueDaysproduct = $this->db->query($uniqueQuery); 
		    $only_discount = $uniqueDaysproduct->row();//echo '<pre>';print_R($only_discount);
		    if(empty($only_discount->DateDiscount)){ 
			$category_inlist = "SELECT max(CASE 
				WHEN DI.item_type = 'in_list'  
				  AND DI.item_method = 'item_category'             
				 
				THEN CONCAT(D.id,',',D.discount, ',', D.discount_type) 
				ELSE null END) AS DateDiscount
			    FROM " . $this->db->dbprefix('discount_item_list') . " DIL 
			     JOIN " . $this->db->dbprefix('discount_items') . " DI ON DI.id = DIL.discount_item_id 
			     JOIN " . $this->db->dbprefix('discounts') . " D ON D.id = DI.discount_id 
			    
			     WHERE D.id=".$uniqueDaysDis->discount_id." AND DIL.item_id = ".$row->category_id."  AND D.type='discount_simple' AND D.discount_status=1 ";
			   /*echo $category_inlist;die;  */
			    $category = $this->db->query($category_inlist);
			    $only_discount = $category->row();
			    if(empty($only_discount->DateDiscount)){
				$category_notlist = "SELECT max(CASE 
					  WHEN DI.item_type = 'not_in_list'  
					    AND DI.item_method = 'item_category'             
					   
					    AND !FIND_IN_SET('".$row->id."',DI.item_type_id)
					  THEN CONCAT(D.id,',',D.discount, ',', D.discount_type) 
					  ELSE null END) AS DateDiscount
				  FROM " . $this->db->dbprefix('discount_item_list') . " DIL 
				  JOIN " . $this->db->dbprefix('discount_items') . " DI ON DI.id = DIL.discount_item_id 
				  JOIN " . $this->db->dbprefix('discounts') . " D ON D.id = DI.discount_id 
				 
				  WHERE D.id=".$uniqueDaysDis->discount_id." AND DIL.item_id != ".$row->category_id." AND D.type='discount_simple' AND D.discount_status=1 ";
		    
				  $cate_not = $this->db->query($category_notlist);
				  $only_discount = $cate_not->row();
			    }
		    }
		}
	    }
	    //echo '<pre>';print_r($only_discount);exit;
	    $only_discount = explode(',',$only_discount->DateDiscount);
		
	    
	    $only_discount['unique_discount'] = true;
	    return $only_discount;
	    }else if(@$uniqueDaysDis->type=="discount_on_total"){
		$only_discount['DateDiscount'] = array();
		$only_discount['only_offer_dis'] = true;
		$only_discount['unique_discount'] = true;
		//echo '<pre>';print_R($only_discount);exit;
		return $only_discount;
	    }
	   } else{
            if(!empty($row->id)){

                $product_inlist = "SELECT max(CASE 
                      WHEN DI.item_type = 'in_list'  
                        AND DI.item_method = 'item_product'             
                        AND DC.condition_method ='condition_date' 
                        AND ( DATE(NOW()) >= DATE(DC.from_date) and DATE(NOW()) <= DATE(DC.to_date))  
                      THEN CONCAT(D.id,',',D.discount, ',', D.discount_type) 
                      ELSE null END) AS DateDiscount,                      
                      max(CASE
                      WHEN DI.item_type = 'in_list' 
                      AND DI.item_method = 'item_product' 
                      AND DC.condition_method ='condition_time' 
                      AND CAST('".$current_time."' AS time) BETWEEN DC.from_time AND DC.to_time 
                    THEN CONCAT(D.id,',',D.discount, ',', D.discount_type) 
                   ELSE null END)AS TimeDiscount, 
                    max(CASE 
                      WHEN DI.item_type = 'in_list'  
                        AND DI.item_method = 'item_product' 
                        AND DC.condition_method ='condition_days' 
                       AND  FIND_IN_SET('".$today."' ,days) 
                      THEN CONCAT(D.id,',',D.discount, ',', D.discount_type) 
                      ELSE null END) AS DaysDiscount
              FROM " . $this->db->dbprefix('discount_item_list') . " DIL 
              JOIN " . $this->db->dbprefix('discount_items') . " DI ON DI.id = DIL.discount_item_id 
              JOIN " . $this->db->dbprefix('discounts') . " D ON D.id = DI.discount_id 
              JOIN " . $this->db->dbprefix('discount_conditions') . " DC ON D.id = DC.discount_id 
              WHERE DIL.item_id = ".$row->id." AND D.type='discount_simple' AND D.discount_status=1 AND D.unique_discount=0";
/*echo $product_inlist;
echo "<br>";
echo "<br>";*/
            $product = $this->db->query($product_inlist);

            $product_notlist = "SELECT max(CASE 
                      WHEN DI.item_type = 'not_in_list'  
                        AND DI.item_method = 'item_product'             
                        AND DC.condition_method ='condition_date' 
                        AND ( DATE(NOW()) >= DATE(DC.from_date) and DATE(NOW()) <= DATE(DC.to_date))  
                      THEN CONCAT(D.id,',',D.discount, ',', D.discount_type) 
                      ELSE null END) AS DateDiscount,                      
                      max(CASE
                      WHEN DI.item_type = 'not_in_list' 
                      AND DI.item_method = 'item_product' 
                      AND DC.condition_method ='condition_time' 
                      AND CAST('".$current_time."' AS time) BETWEEN DC.from_time AND DC.to_time 
                    THEN CONCAT(D.id,',',D.discount, ',', D.discount_type) 
                   ELSE null END)AS TimeDiscount, 
                    max(CASE 
                      WHEN DI.item_type = 'not_in_list'  
                        AND DI.item_method = 'item_product' 
                        AND DC.condition_method ='condition_days' 
                       AND  FIND_IN_SET('".$today."' ,days) 
                      THEN CONCAT(D.id,',',D.discount, ',', D.discount_type) 
                      ELSE null END) AS DaysDiscount
              FROM " . $this->db->dbprefix('discount_item_list') . " DIL 
              JOIN " . $this->db->dbprefix('discount_items') . " DI ON DI.id = DIL.discount_item_id 
              JOIN " . $this->db->dbprefix('discounts') . " D ON D.id = DI.discount_id 
              JOIN " . $this->db->dbprefix('discount_conditions') . " DC ON D.id = DC.discount_id 
              WHERE DIL.item_id != ".$row->id." AND D.type='discount_simple' AND D.discount_status=1  AND D.unique_discount=0";

              $product_not = $this->db->query($product_notlist);
         
                if ($product->row('DateDiscount') != NULL ||  $product->row('TimeDiscount') != NULL ||  $product->row('DaysDiscount') != NULL) {

                    if($product->row('DateDiscount') != NULL)
                    {
                        $discount_recipe = explode(',', $product->row('DateDiscount'));

                    }elseif($product->row('TimeDiscount') != NULL){
                        $discount_recipe = explode(',', $product->row('TimeDiscount'));
                    }elseif($product->row('DaysDiscount') != NULL){
                        $discount_recipe = explode(',', $product->row('DaysDiscount'));
                    }   else{
                       $discount_recipe = '';
                    } 
                }
                else if($product_not->row('DateDiscount') != NULL ||  $product_not->row('TimeDiscount') != NULL ||  $product_not->row('DaysDiscount') != NULL){
                    if($product_not->row('DateDiscount') != NULL)
                    {
                        $discount_recipe = explode(',', $product_not->row('DateDiscount'));
                    }elseif($product_not->row('TimeDiscount') != NULL){
                        $discount_recipe = explode(',', $product_not->row('TimeDiscount'));
                    }elseif($product_not->row('DaysDiscount') != NULL){
                        $discount_recipe = explode(',', $product_not->row('DaysDiscount'));
                    }
                    else{
                       $discount_recipe = '';
                    }
                }
            }
            
            if(!empty($row->category_id)){
                $category_inlist = "SELECT max(CASE 
                      WHEN DI.item_type = 'in_list'  
                        AND DI.item_method = 'item_category'             
                        AND DC.condition_method ='condition_date' 
                        AND ( DATE(NOW()) >= DATE(DC.from_date) and DATE(NOW()) <= DATE(DC.to_date))  
                      THEN CONCAT(D.id,',',D.discount, ',', D.discount_type) 
                      ELSE null END) AS DateDiscount,                      
                      max(CASE
                      WHEN DI.item_type = 'in_list' 
                      AND DI.item_method = 'item_category' 
                      AND DC.condition_method ='condition_time' 
                      AND CAST('".$current_time."' AS time) BETWEEN DC.from_time AND DC.to_time 
                    THEN CONCAT(D.id,',',D.discount, ',', D.discount_type) 
                   ELSE null END)AS TimeDiscount, 
                    max(CASE 
                      WHEN DI.item_type = 'in_list'  
                        AND DI.item_method = 'item_category' 
                        AND DC.condition_method ='condition_days' 
                       AND  FIND_IN_SET('".$today."' ,days) 
                      THEN CONCAT(D.id,',',D.discount, ',', D.discount_type) 
                      ELSE null END) AS DaysDiscount
             FROM " . $this->db->dbprefix('discount_item_list') . " DIL 
              JOIN " . $this->db->dbprefix('discount_items') . " DI ON DI.id = DIL.discount_item_id 
              JOIN " . $this->db->dbprefix('discounts') . " D ON D.id = DI.discount_id 
              JOIN " . $this->db->dbprefix('discount_conditions') . " DC ON D.id = DC.discount_id 
              WHERE DIL.item_id = ".$row->category_id."  AND D.type='discount_simple' AND D.discount_status=1  AND D.unique_discount=0";
            /*echo $category_inlist;die;  */
            $category = $this->db->query($category_inlist);

            $category_notlist = "SELECT max(CASE 
                      WHEN DI.item_type = 'not_in_list'  
                        AND DI.item_method = 'item_category'             
                        AND DC.condition_method ='condition_date' 
                        AND ( DATE(NOW()) >= DATE(DC.from_date) and DATE(NOW()) <= DATE(DC.to_date))  
                      THEN CONCAT(D.id,',',D.discount, ',', D.discount_type) 
                      ELSE null END) AS DateDiscount,                      
                      max(CASE
                      WHEN DI.item_type = 'not_in_list' 
                      AND DI.item_method = 'item_category' 
                      AND DC.condition_method ='condition_time' 
                      AND CAST('".$current_time."' AS time) BETWEEN DC.from_time AND DC.to_time 
                    THEN CONCAT(D.id,',',D.discount, ',', D.discount_type) 
                   ELSE null END)AS TimeDiscount, 
                    max(CASE 
                      WHEN DI.item_type = 'not_in_list'  
                        AND DI.item_method = 'item_category' 
                        AND DC.condition_method ='condition_days' 
                       AND  FIND_IN_SET('".$today."' ,days) 
                      THEN CONCAT(D.id,',',D.discount, ',', D.discount_type) 
                      ELSE null END) AS DaysDiscount
              FROM " . $this->db->dbprefix('discount_item_list') . " DIL 
              JOIN " . $this->db->dbprefix('discount_items') . " DI ON DI.id = DIL.discount_item_id 
              JOIN " . $this->db->dbprefix('discounts') . " D ON D.id = DI.discount_id 
              JOIN " . $this->db->dbprefix('discount_conditions') . " DC ON D.id = DC.discount_id  
              WHERE DIL.item_id != ".$row->category_id." AND D.type='discount_simple' AND D.discount_status=1  AND D.unique_discount=0";

              $cate_not = $this->db->query($category_notlist);

              if ($category->row('DateDiscount') != NULL ||  $category->row('TimeDiscount') != NULL ||  $category->row('DaysDiscount') != NULL) {
                    if($category->row('DateDiscount') != NULL)
                    {

                        $discount_category = explode(',', $category->row('DateDiscount'));
                    }elseif($category->row('TimeDiscount') != NULL){
                        $discount_category = explode(',', $category->row('TimeDiscount'));
                    }elseif($category->row('DaysDiscount') != NULL){
                        
                        $discount_category = explode(',', $category->row('DaysDiscount'));
                    }   
                }
                else if($cate_not->row('DateDiscount') != NULL ||  $cate_not->row('TimeDiscount') != NULL ||  $cate_not->row('DaysDiscount') != NULL){
                    if($cate_not->row('DateDiscount') != NULL)
                    {
                        $discount_category = explode(',', $cate_not->row('DateDiscount'));
                    }elseif($cate_not->row('TimeDiscount') != NULL){
                        $discount_category = explode(',', $cate_not->row('TimeDiscount'));
                    }elseif($cate_not->row('DaysDiscount') != NULL){
                        $discount_category = explode(',', $cate_not->row('DaysDiscount'));
                    }
                    else{
                        $discount_category = '';
                    }
                }
            }
	    // if(!empty($discount_recipe)){
	    	if(!empty($discount_recipe) || !empty($discount_category)){
		if($discount_recipe[0] != 0){
		    return $value = $discount_recipe;
		}else{  
		    return $value = $discount_category;
		}
	    }else{
		return FALSE;
	    }
           }
	   return FALSE;
        }
        return FALSE;
    
    }
    function TotalDiscount(){
        $date = date('Y-m-d');
        $current_time = date('H:i:s');
        $mydate=getdate(date("U"));
        $today = "$mydate[weekday]";

    $TotalDiscount = "SELECT max(CASE 
                   WHEN DC.condition_method ='condition_date' 
                        AND ( DATE(NOW()) >= DATE(DC.from_date) and DATE(NOW()) <= DATE(DC.to_date))  
                      THEN CONCAT(D.id,',',D.discount, ',',D.amount,',', D.discount_type) 
                      ELSE null END) AS DateDiscount,                      
                      max(CASE
                      WHEN DC.condition_method ='condition_time' 
                      AND CAST('".$current_time."' AS time) BETWEEN DC.from_time AND DC.to_time 
                    THEN CONCAT(D.id,',',D.discount, ',',D.amount,',', D.discount_type) 
                   ELSE null END)AS TimeDiscount, 
                    max(CASE 
                      WHEN DC.condition_method ='condition_days' 
                       AND  FIND_IN_SET('".$today."' ,days) 
                      THEN CONCAT(D.id,',',D.discount, ',',D.amount,',', D.discount_type)
                      ELSE null END) AS DaysDiscount
              FROM " . $this->db->dbprefix('discounts') . " D 
              JOIN " . $this->db->dbprefix('discount_conditions') . " DC ON D.id = DC.discount_id 
              WHERE  D.type = 'discount_on_total' AND D.discount_status=1 ";

            $TotalDiscount = $this->db->query($TotalDiscount);
	        $Total_Discount = '';	   
                if ($TotalDiscount->row('DateDiscount') != NULL ||  $TotalDiscount->row('TimeDiscount') != NULL ||  $TotalDiscount->row('DaysDiscount') != NULL) {

                    if($TotalDiscount->row('DateDiscount') != NULL)
                    {

                        $Total_Discount = explode(',', $TotalDiscount->row('DateDiscount'));

                    }elseif($TotalDiscount->row('TimeDiscount') != NULL){

                        $Total_Discount = explode(',', $TotalDiscount->row('TimeDiscount'));
                    }elseif($TotalDiscount->row('DaysDiscount') != NULL){                    	
                        $Total_Discount = explode(',', $TotalDiscount->row('DaysDiscount'));
                    }else{
                       $Total_Discount = '';
                    } 
                }
                
                return $value = $Total_Discount;
        }
      
    function HappyHourdiscount_X_X($id = NULL){
        
        $date = date('Y-m-d');
        $current_time = date('H:i:s');
        $mydate=getdate(date("U"));
        $today = "$mydate[weekday]";

        $q = $this->db->get_where('recipe', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            $row = $q->row();
            if(!empty($row->id)){

                $product_inlist = "SELECT max(CASE 
                      WHEN DI.item_type = 'in_list'  
                        AND DI.item_method = 'item_product'             
                        AND DC.condition_method ='condition_date' 
                        AND ( DATE(NOW()) >= DATE(DC.from_date) and DATE(NOW()) <= DATE(DC.to_date))  
                      THEN D.id 
                      ELSE null END) AS DateDiscount,                      
                      max(CASE
                      WHEN DI.item_type = 'in_list' 
                      AND DI.item_method = 'item_product' 
                      AND DC.condition_method ='condition_time' 
                      AND CAST('".$current_time."' AS time) BETWEEN DC.from_time AND DC.to_time 
                    THEN D.id 
                   ELSE null END)AS TimeDiscount, 
                    max(CASE 
                      WHEN DI.item_type = 'in_list'  
                        AND DI.item_method = 'item_product' 
                        AND DC.condition_method ='condition_days' 
                       AND  FIND_IN_SET('".$today."' ,days) 
                      THEN D.id 
                      ELSE null END) AS DaysDiscount
              FROM " . $this->db->dbprefix('discount_item_list') . " DIL 
              JOIN " . $this->db->dbprefix('discount_items') . " DI ON DI.id = DIL.discount_item_id 
              JOIN " . $this->db->dbprefix('discounts') . " D ON D.id = DI.discount_id 
              JOIN " . $this->db->dbprefix('discount_conditions') . " DC ON D.id = DC.discount_id 
              WHERE DIL.item_id = ".$row->id." AND D.type='discount_buy_x_get_x' ";

            $product = $this->db->query($product_inlist);

            $product_notlist = "SELECT max(CASE 
                      WHEN DI.item_type = 'not_in_list'  
                        AND DI.item_method = 'item_product'             
                        AND DC.condition_method ='condition_date' 
                        AND ( DATE(NOW()) >= DATE(DC.from_date) and DATE(NOW()) <= DATE(DC.to_date))  
                      THEN D.id 
                      ELSE null END) AS DateDiscount,                      
                      max(CASE
                      WHEN DI.item_type = 'not_in_list' 
                      AND DI.item_method = 'item_product' 
                      AND DC.condition_method ='condition_time' 
                      AND CAST('".$current_time."' AS time) BETWEEN DC.from_time AND DC.to_time 
                    THEN D.id 
                   ELSE null END)AS TimeDiscount, 
                    max(CASE 
                      WHEN DI.item_type = 'not_in_list'  
                        AND DI.item_method = 'item_product' 
                        AND DC.condition_method ='condition_days' 
                       AND  FIND_IN_SET('".$today."' ,days) 
                      THEN D.id 
                      ELSE null END) AS DaysDiscount
              FROM " . $this->db->dbprefix('discount_item_list') . " DIL 
              JOIN " . $this->db->dbprefix('discount_items') . " DI ON DI.id = DIL.discount_item_id 
              JOIN " . $this->db->dbprefix('discounts') . " D ON D.id = DI.discount_id 
              JOIN " . $this->db->dbprefix('discount_conditions') . " DC ON D.id = DC.discount_id 
              WHERE DIL.item_id != ".$row->id." AND D.type='discount_buy_x_get_x' ";

              $product_not = $this->db->query($product_notlist);
         
                if ($product->row('DateDiscount') != NULL ||  $product->row('TimeDiscount') != NULL ||  $product->row('DaysDiscount') != NULL) {

                    if($product->row('DateDiscount') != NULL)
                    {
                        $discount_recipe = $product->row('DateDiscount');
                    }elseif($product->row('TimeDiscount') != NULL){
                        $discount_recipe = $product->row('TimeDiscount');
                    }elseif($product->row('DaysDiscount') != NULL){
                        $discount_recipe = $product->row('DaysDiscount');
                    }   else{
                       $discount_recipe = '';
                    } 
                }
                else if($product_not->row('DateDiscount') != NULL ||  $product_not->row('TimeDiscount') != NULL ||  $product_not->row('DaysDiscount') != NULL){
                    if($product_not->row('DateDiscount') != NULL)
                    {
                        $discount_recipe = $product_not->row('DateDiscount');
                    }elseif($product_not->row('TimeDiscount') != NULL){
                        $discount_recipe = $product_not->row('TimeDiscount');
                    }elseif($product_not->row('DaysDiscount') != NULL){
                        $discount_recipe = $product_not->row('DaysDiscount');
                    }
                    else{
                       $discount_recipe = '';
                    }
                }
            }
            
            if(!empty($row->category_id)){

                $category_inlist = "SELECT max(CASE 
                      WHEN DI.item_type = 'in_list'  
                        AND DI.item_method = 'item_category'             
                        AND DC.condition_method ='condition_date' 
                        AND ( DATE(NOW()) >= DATE(DC.from_date) and DATE(NOW()) <= DATE(DC.to_date))  
                      THEN D.id 
                      ELSE null END) AS DateDiscount,                      
                      max(CASE
                      WHEN DI.item_type = 'in_list' 
                      AND DI.item_method = 'item_category' 
                      AND DC.condition_method ='condition_time' 
                      AND CAST('".$current_time."' AS time) BETWEEN DC.from_time AND DC.to_time 
                    THEN D.id 
                   ELSE null END)AS TimeDiscount, 
                    max(CASE 
                      WHEN DI.item_type = 'in_list'  
                        AND DI.item_method = 'item_category' 
                        AND DC.condition_method ='condition_days' 
                       AND  FIND_IN_SET('".$today."' ,days) 
                      THEN D.id  
                      ELSE null END) AS DaysDiscount
              FROM " . $this->db->dbprefix('discount_item_list') . " DIL 
              JOIN " . $this->db->dbprefix('discount_items') . " DI ON DI.id = DIL.discount_item_id 
              JOIN " . $this->db->dbprefix('discounts') . " D ON D.id = DI.discount_id 
              JOIN " . $this->db->dbprefix('discount_conditions') . " DC ON D.id = DC.discount_id 
              WHERE DIL.item_id = ".$row->category_id."  AND D.type='discount_buy_x_get_x' ";

            $category = $this->db->query($category_inlist);

            $category_notlist = "SELECT max(CASE 
                      WHEN DI.item_type = 'not_in_list'  
                        AND DI.item_method = 'item_category'             
                        AND DC.condition_method ='condition_date' 
                        AND ( DATE(NOW()) >= DATE(DC.from_date) and DATE(NOW()) <= DATE(DC.to_date))  
                      THEN D.id 
                      ELSE null END) AS DateDiscount,                      
                      max(CASE
                      WHEN DI.item_type = 'not_in_list' 
                      AND DI.item_method = 'item_category' 
                      AND DC.condition_method ='condition_time' 
                      AND CAST('".$current_time."' AS time) BETWEEN DC.from_time AND DC.to_time 
                    THEN D.id 
                   ELSE null END)AS TimeDiscount, 
                    max(CASE 
                      WHEN DI.item_type = 'not_in_list'  
                        AND DI.item_method = 'item_category' 
                        AND DC.condition_method ='condition_days' 
                       AND  FIND_IN_SET('".$today."' ,days) 
                      THEN D.id 
                      ELSE null END) AS DaysDiscount
              FROM " . $this->db->dbprefix('discount_item_list') . " DIL 
              JOIN " . $this->db->dbprefix('discount_items') . " DI ON DI.id = DIL.discount_item_id 
              JOIN " . $this->db->dbprefix('discounts') . " D ON D.id = DI.discount_id 
              JOIN " . $this->db->dbprefix('discount_conditions') . " DC ON D.id = DC.discount_id 
              WHERE DIL.item_id != ".$row->category_id." AND D.type='discount_buy_x_get_x' ";

              $cate_not = $this->db->query($category_notlist);

              if ($category->row('DateDiscount') != NULL ||  $category->row('TimeDiscount') != NULL ||  $category->row('DaysDiscount') != NULL) {
                    if($category->row('DateDiscount') != NULL)
                    {
                        $discount_category = $category->row('DateDiscount');
                    }elseif($category->row('TimeDiscount') != NULL){
                        $discount_category = $category->row('TimeDiscount');
                    }elseif($category->row('DaysDiscount') != NULL){
                        $discount_category = $category->row('DaysDiscount');
                    }   
                }
                else if($cate_not->row('DateDiscount') != NULL ||  $cate_not->row('TimeDiscount') != NULL ||  $cate_not->row('DaysDiscount') != NULL){
                    if($cate_not->row('DateDiscount') != NULL)
                    {
                        $discount_category = $cate_not->row('DateDiscount');
                    }elseif($cate_not->row('TimeDiscount') != NULL){
                        $discount_category = $cate_not->row('TimeDiscount');
                    }elseif($cate_not->row('DaysDiscount') != NULL){
                        $discount_category = $cate_not->row('DaysDiscount');
                    }
                    else{
                        $discount_category = '';
                    }
                }
            }

            if($discount_recipe[0] != 0){                
                return $value = $discount_recipe;
            }else{                                    
                return $value = $discount_category;
            }
        }
        return FALSE;    
    }        
    function HappyHourdiscount_X_Y($id = NULL){

        $date = date('Y-m-d');
        $current_time = date('H:i:s');
        $mydate=getdate(date("U"));
        $today = "$mydate[weekday]";

        $q = $this->db->get_where('recipe', array('id' => $id), 1);
        if ($q->num_rows() > 0) {

            $row = $q->row();
            if(!empty($row->id)){

                $product_inlist = "SELECT max(CASE 
                      WHEN DI.item_type = 'in_list'  
                        AND DI.item_method = 'item_product'             
                        AND DC.condition_method ='condition_date' 
                        AND ( DATE(NOW()) >= DATE(DC.from_date) and DATE(NOW()) <= DATE(DC.to_date))  
                      THEN CONCAT(D.id,',',D.discount, ',',D.buy_quantity,',', D.get_quantity,',', DI.item_get_id) 
                      ELSE null  END) AS DateDiscount,                      
                      max(CASE
                      WHEN DI.item_type = 'in_list' 
                      AND DI.item_method = 'item_product' 
                      AND DC.condition_method ='condition_time' 
                      AND CAST('".$current_time."' AS time) BETWEEN DC.from_time AND DC.to_time 
                    THEN CONCAT(D.id,',',D.discount, ',',D.buy_quantity,',', D.get_quantity,',', DI.item_get_id) 
                   ELSE null END)AS TimeDiscount, 
                    max(CASE 
                      WHEN DI.item_type = 'in_list'  
                        AND DI.item_method = 'item_product' 
                        AND DC.condition_method ='condition_days' 
                       AND  FIND_IN_SET('".$today."' ,days) 
                      THEN CONCAT(D.id,',',D.discount, ',',D.buy_quantity,',', D.get_quantity,',', DI.item_get_id) 
                      ELSE null  END) AS DaysDiscount
              FROM " . $this->db->dbprefix('discount_item_list') . " DIL 
              JOIN " . $this->db->dbprefix('discount_items') . " DI ON DI.id = DIL.discount_item_id 
              JOIN " . $this->db->dbprefix('discounts') . " D ON D.id = DI.discount_id 
              JOIN " . $this->db->dbprefix('discount_conditions') . " DC ON D.id = DC.discount_id  
              WHERE DIL.item_id = ".$row->id." AND D.type='discount_buy_x_get_y' ";

            $product = $this->db->query($product_inlist);

            $product_notlist = "SELECT max(CASE 
                      WHEN DI.item_type = 'not_in_list'  
                        AND DI.item_method = 'item_product'             
                        AND DC.condition_method ='condition_date' 
                        AND ( DATE(NOW()) >= DATE(DC.from_date) and DATE(NOW()) <= DATE(DC.to_date)) 
                      THEN CONCAT(D.id,',',D.discount, ',',D.buy_quantity,',', D.get_quantity,',', DI.item_get_id)  
                      ELSE null  END) AS DateDiscount,                      
                      max(CASE
                      WHEN DI.item_type = 'not_in_list' 
                      AND DI.item_method = 'item_product' 
                      AND DC.condition_method ='condition_time' 
                      AND CAST('".$current_time."' AS time) BETWEEN DC.from_time AND DC.to_time 
                    THEN CONCAT(D.id,',',D.discount, ',',D.buy_quantity,',', D.get_quantity,',', DI.item_get_id)  
                   ELSE null  END)AS TimeDiscount, 
                    max(CASE 
                      WHEN DI.item_type = 'not_in_list'  
                        AND DI.item_method = 'item_product' 
                        AND DC.condition_method ='condition_days' 
                       AND  FIND_IN_SET('".$today."' ,days) 
                      THEN CONCAT(D.id,',',D.discount, ',',D.buy_quantity,',', D.get_quantity,',', DI.item_get_id)  
                      ELSE null  END) AS DaysDiscount
              FROM " . $this->db->dbprefix('discount_item_list') . " DIL 
              JOIN " . $this->db->dbprefix('discount_items') . " DI ON DI.id = DIL.discount_item_id 
              JOIN " . $this->db->dbprefix('discounts') . " D ON D.id = DI.discount_id 
              JOIN " . $this->db->dbprefix('discount_conditions') . " DC ON D.id = DC.discount_id  
              WHERE DIL.item_id != ".$row->id." AND D.type='discount_buy_x_get_y' ";

              $product_not = $this->db->query($product_notlist);
         
                if ($product->row('DateDiscount') != NULL ||  $product->row('TimeDiscount') != NULL ||  $product->row('DaysDiscount') != NULL) {
                    if($product->row('DateDiscount') != NULL)
                    {
                        $discount_recipe = explode(',', $product->row('DateDiscount'));
                    }elseif($product->row('TimeDiscount') != NULL){
                        $discount_recipe = explode(',', $product->row('TimeDiscount'));
                    }elseif($product->row('DaysDiscount') != NULL){
                        $discount_recipe = explode(',', $product->row('DaysDiscount'));
                    }   else{
                       $discount_recipe = '';
                    } 
                }
                else if($product_not->row('DateDiscount') != NULL ||  $product_not->row('TimeDiscount') != NULL ||  $product_not->row('DaysDiscount') != NULL){
                    if($product_not->row('DateDiscount') != NULL)
                    {
                        $discount_recipe = explode(',', $product_not->row('DateDiscount'));
                    }elseif($product_not->row('TimeDiscount') != NULL){
                        $discount_recipe = explode(',', $product_not->row('TimeDiscount'));
                    }elseif($product_not->row('DaysDiscount') != NULL){
                        $discount_recipe = explode(',', $product_not->row('DaysDiscount'));
                    }
                    else{
                       $discount_recipe = '';
                    }
                }
            }
            
            if(!empty($row->category_id)){

                $category_inlist = "SELECT max(CASE 
                      WHEN DI.item_type = 'in_list'  
                        AND DI.item_method = 'item_category'             
                        AND DC.condition_method ='condition_date' 
                        AND ( DATE(NOW()) >= DATE(DC.from_date) and DATE(NOW()) <= DATE(DC.to_date))
                      THEN CONCAT(D.id,',',D.discount, ',',D.buy_quantity,',', D.get_quantity,',', DI.item_get_id)  
                      ELSE null  END) AS DateDiscount,                      
                      max(CASE
                      WHEN DI.item_type = 'in_list' 
                      AND DI.item_method = 'item_category' 
                      AND DC.condition_method ='condition_time' 
                      AND CAST('".$current_time."' AS time) BETWEEN DC.from_time AND DC.to_time 
                    THEN CONCAT(D.id,',',D.discount, ',',D.buy_quantity,',', D.get_quantity,',', DI.item_get_id)  
                   ELSE D.id  END)AS TimeDiscount, 
                    max(CASE 
                      WHEN DI.item_type = 'in_list'  
                        AND DI.item_method = 'item_category' 
                        AND DC.condition_method ='condition_days' 
                       AND  FIND_IN_SET('".$today."' ,days) 
                      THEN CONCAT(D.id,',',D.discount, ',',D.buy_quantity,',', D.get_quantity,',', DI.item_get_id)  
                      ELSE null  END) AS DaysDiscount
              FROM " . $this->db->dbprefix('discount_item_list') . " DIL 
              JOIN " . $this->db->dbprefix('discount_items') . " DI ON DI.id = DIL.discount_item_id 
              JOIN " . $this->db->dbprefix('discounts') . " D ON D.id = DI.discount_id 
              JOIN " . $this->db->dbprefix('discount_conditions') . " DC ON D.id = DC.discount_id  
              WHERE DIL.item_id = ".$row->category_id."  AND D.type='discount_buy_x_get_y' ";

            $category = $this->db->query($category_inlist);

            $category_notlist = "SELECT max(CASE 
                      WHEN DI.item_type = 'not_in_list'  
                        AND DI.item_method = 'item_category'             
                        AND DC.condition_method ='condition_date' 
                        AND ( DATE(NOW()) >= DATE(DC.from_date) and DATE(NOW()) <= DATE(DC.to_date))  
                      THEN CONCAT(D.id,',',D.discount, ',',D.buy_quantity,',', D.get_quantity,',', DI.item_get_id)  
                      ELSE null END) AS DateDiscount,                      
                      max(CASE
                      WHEN DI.item_type = 'not_in_list' 
                      AND DI.item_method = 'item_category' 
                      AND DC.condition_method ='condition_time' 
                      AND CAST('".$current_time."' AS time) BETWEEN DC.from_time AND DC.to_time 
                    THEN CONCAT(D.id,',',D.discount, ',',D.buy_quantity,',', D.get_quantity,',', DI.item_get_id)  
                   ELSE null END)AS TimeDiscount, 
                    max(CASE 
                      WHEN DI.item_type = 'not_in_list'  
                        AND DI.item_method = 'item_category' 
                        AND DC.condition_method ='condition_days' 
                       AND  FIND_IN_SET('".$today."' ,days) 
                      THEN CONCAT(D.id,',',D.discount, ',',D.buy_quantity,',', D.get_quantity,',', DI.item_get_id)  
                      ELSE null END) AS DaysDiscount
              FROM " . $this->db->dbprefix('discount_item_list') . " DIL 
              JOIN " . $this->db->dbprefix('discount_items') . " DI ON DI.id = DIL.discount_item_id 
              JOIN " . $this->db->dbprefix('discounts') . " D ON D.id = DI.discount_id 
              JOIN " . $this->db->dbprefix('discount_conditions') . " DC ON D.id = DC.discount_id  
              WHERE DIL.item_id != ".$row->category_id." AND D.type='discount_buy_x_get_y' ";

              $cate_not = $this->db->query($category_notlist);

              if ($category->row('DateDiscount') != NULL ||  $category->row('TimeDiscount') != NULL &&  $category->row('DaysDiscount') != NULL) {
                    if($category->row('DateDiscount') != NULL)
                    {
                        $discount_category = explode(',', $category->row('DateDiscount'));
                    }elseif($category->row('TimeDiscount') != NULL){
                        $discount_category = explode(',', $category->row('TimeDiscount'));
                    }elseif($category->row('DaysDiscount') != NULL){
                        $discount_category = explode(',', $category->row('DaysDiscount'));
                    }   
                }
                else if($cate_not->row('DateDiscount') != NULL || $cate_not->row('TimeDiscount') != NULL ||  $cate_not->row('DaysDiscount') != NULL){
                    if($cate_not->row('DateDiscount') != NULL)
                    {
                        $discount_category = explode(',', $cate_not->row('DateDiscount'));
                    }elseif($cate_not->row('TimeDiscount') != NULL){
                        $discount_category = explode(',', $cate_not->row('TimeDiscount'));
                    }elseif($cate_not->row('DaysDiscount') != NULL){
                        $discount_category = explode(',', $cate_not->row('DaysDiscount'));
                    }
                    else{
                        $discount_category = '';
                    }
                }
            }

            if($discount_recipe[0] != 0){                
                return $value = $discount_recipe;
            }else{                                    
                return $value = $discount_category;
            }
        }
        return FALSE;    
    }      
    public function getAddressByID($id) {
        return $this->db->get_where('addresses', ['id' => $id], 1)->row();
    }

    public function checkSlug($slug, $type = NULL) {
        if (!$type) {
            return $this->db->get_where('products', ['slug' => $slug], 1)->row();
        } elseif ($type == 'category') {
            return $this->db->get_where('categories', ['slug' => $slug], 1)->row();
        } elseif ($type == 'brand') {
            return $this->db->get_where('brands', ['slug' => $slug], 1)->row();
        }
        return FALSE;
    }

    public function calculateDiscount($discount = NULL, $amount) {
        if ($discount && $this->Settings->product_discount) {
            $dpos = strpos($discount, '%');
            if ($dpos !== false) {
                $pds = explode("%", $discount);
                return $this->sma->formatDecimal(((($this->sma->formatDecimal($amount)) * (Float) ($pds[0])) / 100), 4);
            } else {
                return $this->sma->formatDecimal($discount, 4);
            }
        }
        return 0;
    }

    public function calculate_Discount($discount = NULL, $amount = NULL, $total = NULL) {
     
        if ($discount && $this->Settings->product_discount) {
            $dpos = strpos($discount, '%');
            if ($dpos !== false) {

                $pds = explode("%", $discount);
                return $this->sma->formatDecimal(((($this->sma->formatDecimal($amount)) * (Float) ($pds[0])) / 100));
            } else {
                  $per =  ($discount /$total)*100;
                return $this->sma->formatDecimal(((($this->sma->formatDecimal($amount)) * (Float) ($per)) / 100));
            }
        }
        return 0;
    }    

    public function calculateOrderTax($order_tax_id = NULL, $amount) {
        if ($this->Settings->tax2 != 0 && $order_tax_id) {
            if ($order_tax_details = $this->site->getTaxRateByID($order_tax_id)) {
                if ($order_tax_details->type == 1) {
                    return $this->sma->formatDecimal((($amount * $order_tax_details->rate) / 100));
                } else {
                    return $this->sma->formatDecimal($order_tax_details->rate);
                }
            }
        }
        return 0;
    }

   public function calculateServiceCharge($service_charge_id = NULL, $amount) {        
            if ($service_charge_details = $this->getServiceChargeByID($service_charge_id)) {                
                    return $this->sma->formatDecimal((($amount * $service_charge_details->rate) / 100));               
            }        
        return 0;
    }
    
    public function getDiscounts($code) {

        $q = $this->db->get_where('recipe', array('code' => $code), 1);

        $this->db->select("discounts.id, discounts.name, discounts.buy_quantity, discounts.get_quantity, discounts.amount, discounts.discount_type, discounts.type, discounts.discount, discount_items.item_method, discount_items.item_type, discount_items.item_get_id, discount_item_list.item_id, discount_item_list.discount_item_id");
                $this->db->join("discount_items", "discount_items.id = discount_item_list.discount_item_id");
                $this->db->join("discounts", "discounts.id = discount_items.discount_id");
                $this->db->where("discount_items.item_method", "item_category");
                $this->db->where("discount_item_list.item_id", $row->category_id);
                $c = $this->db->get("recipe");

        if ($q->num_rows() > 0) {
            $ref = $q->row();
            
            /*switch ($field) {
                case 'so':
                    $prefix = $this->Settings->sales_prefix;
                    break;
                case 'pos':
                    $prefix = isset($this->Settings->sales_prefix) ? $this->Settings->sales_prefix . '/POS' : '';
                    break;
                case 'qu':
                    $prefix = $this->Settings->quote_prefix;
                    break;
                case 'po':
                    $prefix = $this->Settings->purchase_prefix;
                    break;
                case 'to':
                    $prefix = $this->Settings->transfer_prefix;
                    break;
                case 'do':
                    $prefix = $this->Settings->delivery_prefix;
                    break;
                case 'pay':
                    $prefix = $this->Settings->payment_prefix;
                    break;
                case 'ppay':
                    $prefix = $this->Settings->ppayment_prefix;
                    break;
                case 'ex':
                    $prefix = $this->Settings->expense_prefix;
                    break;
                case 're':
                    $prefix = $this->Settings->return_prefix;
                    break;
                case 'rep':
                    $prefix = $this->Settings->returnp_prefix;
                    break;
                case 'qa':
                    $prefix = $this->Settings->returnp_prefix;
                    break;
                default:
                    $prefix = '';
            }

            $ref_no = (!empty($prefix)) ? $prefix . '/' : '';

            if ($this->Settings->reference_format == 1) {
                $ref_no .= date('Y') . "/" . sprintf("%04s", $ref->{$field});
            } elseif ($this->Settings->reference_format == 2) {
                $ref_no .= date('Y') . "/" . date('m') . "/" . sprintf("%04s", $ref->{$field});
            } elseif ($this->Settings->reference_format == 3) {
                $ref_no .= sprintf("%04s", $ref->{$field});
            } else {
                $ref_no .= $this->getRandomReference();
            }*/

            return $code;
        }
        return FALSE;
    }
   

 /*public function getDayCategorySale($start,$id,$billid,$warehouse_id) {
    	
        $where ='';
            if($warehouse_id != 0)
            {
                $where = "AND P.warehouse_id =".$warehouse_id."";
            }
        
        $myquery ="SELECT P.bill_number,P.total,P.total_tax,P.total_discount,P.tax_type
            FROM " . $this->db->dbprefix('bils') . " P
            JOIN " . $this->db->dbprefix('bil_items') . " BI ON BI.bil_id = P.id
            JOIN " . $this->db->dbprefix('recipe') . " R ON R.id = BI.recipe_id
            JOIN " . $this->db->dbprefix('recipe_categories') . " RC ON RC.id = R.category_id
            WHERE P.id= ".$billid." AND DATE(P.date) = '".$start."' AND RC.id =".$id." AND 
            P.payment_status ='Completed'  ".$where." group by R.category_id" ;
           
        $q = $this->db->query($myquery);
       
        if ($q->num_rows() > 0) {
            $res = $q->row();
            if($res->tax_type == 0)
            {	
            	$value = ($res->total)-($res->total_tax);
            }
            else
            {
				$value =($res->total)-($res->total_discount);
            }
            return $value;
        }
        return 0;
    }*/

    public function getDayCategorySale($start,$id,$billid,$warehouse_id) {
        $where ='';
            if($warehouse_id != 0)
            {
                $where = "AND P.warehouse_id =".$warehouse_id."";
            }
        
        $myquery ="SELECT bill_number,SUM(BI.subtotal-BI.item_discount-BI.off_discount-BI.input_discount+BI.service_charge_amount+CASE WHEN (BI.tax_type = 1) THEN BI.tax ELSE 0 END) as amt
            FROM " . $this->db->dbprefix('bils') . " P
            JOIN " . $this->db->dbprefix('bil_items') . " BI ON BI.bil_id = P.id
            JOIN " . $this->db->dbprefix('recipe') . " R ON R.id = BI.recipe_id
            JOIN " . $this->db->dbprefix('recipe_categories') . " RC ON RC.id = R.category_id
            WHERE DATE(P.date) = '".$start."' AND RC.id =".$id." AND P.id =".$billid." AND
            P.payment_status ='Completed'  ".$where." group by R.category_id" ;                   
        $q = $this->db->query($myquery);
       
        if ($q->num_rows() > 0) {
            $res = $q->row();
            return $res->amt;
        }
        return 0;
	
	
    }
    public function getMonthlyCategorySale($start,$id,$warehouse_id,$bill_id) {
    	
        $where ='';
            if($warehouse_id != 0)
            {
                $where = "AND P.warehouse_id =".$warehouse_id."";
            }
        
        $myquery ="SELECT P.bill_number,P.total,P.total_tax,P.total_discount,P.tax_type,SUM(BI.subtotal-BI.item_discount-BI.off_discount-BI.input_discount+BI.service_charge_amount+CASE WHEN (BI.tax_type = 1) THEN BI.tax ELSE 0 END) as amt
            FROM " . $this->db->dbprefix('bils') . " P
            JOIN " . $this->db->dbprefix('bil_items') . " BI ON BI.bil_id = P.id
            JOIN " . $this->db->dbprefix('recipe') . " R ON R.id = BI.recipe_id
            JOIN " . $this->db->dbprefix('recipe_categories') . " RC ON RC.id = R.category_id
            WHERE P.id= ".$bill_id." AND DATE_FORMAT( P.date,  '%Y-%m' ) =  '".$start."' AND RC.id =".$id." AND 
            P.payment_status ='Completed'  ".$where." group by R.category_id" ;
           
        $q = $this->db->query($myquery);
       
        if ($q->num_rows() > 0) {
            $res = $q->row();
            return $this->sma->formatMoney($res->amt);
            /*if($res->tax_type == 0)
            {	
            	$value = ($res->total)-($res->total_tax);
            }
            else
            {
				$value =($res->total)-($res->total_discount);
            }*/
            
            // return $this->sma->formatMoney($value);
        }
        return 0;
	
	
    }    
public function check_splitid_is_bill_generated($split_id){

    	$myQuery = "SELECT id
			FROM ".$this->db->dbprefix('sales')." 
			WHERE sales_split_id= '".$split_id."' ";
        $q = $this->db->query($myQuery);
        if ($q->num_rows() > 0) {
            $res = $q->row();
            return TRUE;
        }
        return FALSE;
	}
	
    function my_is_unique($value,$field,$table){
	$q = $this->db->get_where($table,array($field=>$value));
	if($q->num_rows()>0){
	    return false;
	}
	return true;
    }
public function getTable_Ordered_time_interval($table_id){
  

	    $current_date = date('Y-m-d');
		$current_date = $this->getTransactionDate();    	
		$myQuery = "SELECT O.created_on
		  FROM " . $this->db->dbprefix('orders') . " AS O          
		WHERE O.table_id ='".$table_id."'AND O.payment_status is null AND O.order_cancel_status =0 AND DATE(date)='".$current_date."'";
		// echo $myQuery;		
		$q = $this->db->query($myQuery);
        if ($q->num_rows() > 0) {
            $res = $q->row('created_on');
            return $res;
        }
        else{
          return FALSE;
        }
	}

public function getOrderStatus($split_id){

		$myQuery = "SELECT O.id
		  FROM " . $this->db->dbprefix('orders') . " AS O
          JOIN " . $this->db->dbprefix('order_items') . " OI ON OI.sale_id = O.id
		WHERE O.split_id ='".$split_id."' AND ((OI.item_status = 'Inprocess') OR(OI.item_status = 'Preparing') OR (OI.item_status = 'Ready')) AND OI.order_item_cancel_status = 0";
		
		$q = $this->db->query($myQuery);

        if ($q->num_rows() == 0) {
            return TRUE;
        }
        else{
          return FALSE;
        }
	}   

    public function getDiscountsAmt($id) {

        $myquery ="SELECT SUM(BI.item_discount+off_discount+input_discount) AS discount_total
            FROM " . $this->db->dbprefix('bils') . " P
            JOIN " . $this->db->dbprefix('bil_items') . " BI ON BI.bil_id = P.id
            WHERE P.id =".$id." ";
            
        $q = $this->db->query($myquery);

        if ($q->num_rows() > 0) {
            $res = $q->row();
            return $res->discount_total;
        }
        return 0;
    }
	
    function generate_bill_number($tableWhitelisted){ 
	$billNumReset = $this->Settings->billnumber_reset;
	$today = time();//strtotime('2018-05-01');
	// echo $billNumReset;exit;
	switch($billNumReset){
	    case 1://daily
		$start_time = date('Y-m-d 00:00:01');
		$end_time = date('Y-m-d 23:59:59');
		$billnumber = $this->getbillNumber($tableWhitelisted,$start_time,$end_time,'daily');
		break;
	    case 2://weekly
		$start_date = date('Y-m-d', strtotime('monday this week', $today));
		$end_date = date('Y-m-d', strtotime('sunday this week', $today));
		$billnumber = $this->getbillNumber($tableWhitelisted,$start_date,$end_date);
		break;
	    case 3://monthly
		$start_date = date('Y-m-01', $today);
		$end_date = date('Y-m-t', $today);
		$billnumber = $this->getbillNumber($tableWhitelisted,$start_date,$end_date);
		break;
	    case 4://yearly
		$financial_yr_from = explode('/',$this->Settings->financial_yr_from);
		$financial_yr_to = explode('/',$this->Settings->financial_yr_to);
		$start_date = date('Y-'.$financial_yr_from[1].'-'.$financial_yr_from[0], $today);
		$end_date = date('Y-'.$financial_yr_to[1].'-'.$financial_yr_to[0],strtotime('+1 years'));
		
		if($financial_yr_from[1]<$financial_yr_to[1]){
		    $end_date = date('Y-'.$financial_yr_to[1].'-'.$financial_yr_to[0],$today);
		
		}
		//$end_date = date('Y-'.$financial_yr_to[1].'-'.$financial_yr_to[0],strtotime('+1 years'));
		$billnumber = $this->getbillNumber($tableWhitelisted,$start_date,$end_date,'yearly',1);
		break;
	    default://none
		$billnumber = $this->getbillNumber($tableWhitelisted);
		break;
	    
	}
	return $billnumber;
    }
    function getbillNumber($tableWhitelisted,$start=null,$end=null,$case=null,$yrcnt=null){ 
	$this->db->select();
	if($case == "daily" && $start && $end){
	    $this->db->where(array('date>='=>$start,'date<'=>$end)); 
	}else if($case == "yearly" && $start && $end){
	    $this->db->where(array('DATE(date)>='=>$start,'DATE(date)<='=>$end));
	}else if($case != "daily" && $start && $end){
	    $this->db->where(array('DATE(date)>='=>$start,'DATE(date)<'=>$end));
	}
	      $where = '';
          if($case == "daily" && $start && $end){
            $where = " AND DATE(date) >= '".$start."' AND DATE(date) >= '".$end."'"; 
          }else if($case == "yearly" && $start && $end){
            $where = "AND DATE(date)>= '".$start."'  AND DATE(date)<= '".$end."'"; 
          }else if($case != "daily" && $start && $end){
            $where = "AND ATE(date)>='".$start."' AND DATE(date)<'".$end."'"; 
          }
          if($tableWhitelisted){
          $where .= "AND table_whitelisted = 1";
          }else{
            $where .= "AND table_whitelisted = 0";
          }
          $myQuery = "SELECT *
                   FROM " . $this->db->dbprefix('bils') . "        
                  WHERE bill_number !='' AND (payment_status='Completed' OR payment_status='Cancelled')  ".$where."  order by updated_at desc limit 1";
                  // echo $myQuery;die;
          $q = $this->db->query($myQuery);

	if(!$tableWhitelisted){
	    if ($q->num_rows() > 0) {
		$result = $q->row();
		if($result->bill_number[0]==0) {
		    $bill_no = sprintf("%'.0".strlen($result->bill_number)."d",intval($result->bill_number)+1);
		}else {$bill_no = intval($result->bill_number)+1;}
	    
		return $bill_no;
	    }else if($case == "yearly" && $start && $end && $yrcnt==1){
		
		$financial_yr_from = explode('/',$this->Settings->financial_yr_from);
		$financial_yr_to = explode('/',$this->Settings->financial_yr_to);
		$s = explode('-',$start);
		$s_year = $s[0] -1;
		$e = explode('-',$end);
		$e_year = $e[0] -1;
		$start_date = $s_year.'-'.$financial_yr_from[1].'-'.$financial_yr_from[0];
		$end_date = $e_year.'-'.$financial_yr_to[1].'-'.$financial_yr_to[0];
		$bill_no = $this->getbillNumber($tableWhitelisted,$start_date,$end_date,'yearly');
		return $bill_no;
	    }
	    else{
		$bill_no = ($this->Settings->bill_number_start_from!='')?$this->Settings->bill_number_start_from:sprintf("%'.05d", 1);
		return $bill_no;
	    }
	}else{
	    $billPrefix = 'tw-';
	    if ($q->num_rows() > 0) {
		$result = $q->row();
		$prevbillno = str_replace($billPrefix,'',$result->bill_number);
		if($prevbillno[0]==0) {
		    $bill_no = sprintf("%'.0".strlen($prevbillno)."d",intval($prevbillno)+1);
		}else {
		    $bill_no = intval($prevbillno)+1;
		    }
		return $billPrefix.$bill_no;
	    }
	    else{
		$bill_no = ($this->Settings->bill_number_start_from!='')?$this->Settings->bill_number_start_from:sprintf("%'.05d", 1);
		return $billPrefix.$bill_no;
	    }
	}
	
    }

    function Payment_dine_bill_number($tableWhitelisted,$bill_id){ 
	$billNumReset = $this->Settings->billnumber_reset;
	$today = time();
	switch($billNumReset){
	    case 1://daily
		$start_time = date('Y-m-d 00:00:01');
		$end_time = date('Y-m-d 23:59:59');
		$billnumber = $this->getPaymentdineinbillNumber($tableWhitelisted,$bill_id,$start_time,$end_time,'daily');
		break;
	    case 2://weekly
		$start_date = date('Y-m-d', strtotime('monday this week', $today));
		$end_date = date('Y-m-d', strtotime('sunday this week', $today));
		$billnumber = $this->getPaymentdineinbillNumber($tableWhitelisted,$bill_id,$start_date,$end_date);
		break;
	    case 3://monthly
		$start_date = date('Y-m-01', $today);
		$end_date = date('Y-m-t', $today);
		$billnumber = $this->getPaymentdineinbillNumber($tableWhitelisted,$bill_id,$start_date,$end_date);
		break;
	    case 4://yearly
		$financial_yr_from = explode('/',$this->Settings->financial_yr_from);
		$financial_yr_to = explode('/',$this->Settings->financial_yr_to);
		$start_date = date('Y-'.$financial_yr_from[1].'-'.$financial_yr_from[0], $today);
		$end_date = date('Y-'.$financial_yr_to[1].'-'.$financial_yr_to[0],strtotime('+1 years'));
		if($financial_yr_from[1]<$financial_yr_to[1]){
		    $end_date = date('Y-'.$financial_yr_to[1].'-'.$financial_yr_to[0],$today);		
		}
		$billnumber = $this->getPaymentdineinbillNumber($tableWhitelisted,$bill_id,$start_date,$end_date,'yearly');
		break;
	    default://none
		$billnumber = $this->getPaymentdineinbillNumber($tableWhitelisted,$bill_id);
		break;
	    
	}
	return $billnumber;
    }
    function getPaymentdineinbillNumber($tableWhitelisted,$bill_id,$start=null,$end=null,$case=null){ 	
	  $where = '';
		  if($case == "daily" && $start && $end){
		  	$where = " AND DATE(date) >= '".$start."' AND DATE(date) >= '".$end."'"; 
		  }else if($case == "yearly" && $start && $end){
		  	$where = "AND DATE(date)>= '".$start."'  AND DATE(date)<= '".$end."'"; 
		  }else if($case != "daily" && $start && $end){
		  	$where = "AND ATE(date)>='".$start."' AND DATE(date)<'".$end."'"; 
		  }
		  if($tableWhitelisted){
		  $where .= "AND table_whitelisted = 1";
		  }else{
		  	$where .= "AND table_whitelisted = 0";
		  }
	      $myQuery = "SELECT * FROM ".$this->db->dbprefix('bils')." WHERE bill_number !='' AND payment_status='Completed'  ".$where."  order by updated_at desc limit 1";
	      
	      /*$myQuery = "SELECT * FROM ".$this->db->dbprefix('bils')." WHERE bill_number !='' AND(payment_status='Completed' OR payment_status='Cancelled')  ".$where."  order by updated_at desc limit 1";*/
                  // echo $myQuery;die;
          $q = $this->db->query($myQuery);
	if(!$tableWhitelisted){
	    if ($q->num_rows() > 0) {
		$result = $q->row();
		if($result->bill_number[0]==0) {
		    $bill_no = sprintf("%'.0".strlen($result->bill_number)."d",intval($result->bill_number)+1);
		}else {$bill_no = intval($result->bill_number)+1;}
		return $bill_no;
	    }
	    else{
		$bill_no = ($this->Settings->bill_number_start_from!='')?$this->Settings->bill_number_start_from:sprintf("%'.05d", 1);
		return $bill_no;
	    }
	}else{
	    $billPrefix = $this->pos_settings->taxation_bill_prefix;
	    if ($q->num_rows() > 0) {
		$result = $q->row();
		$prevbillno = str_replace($billPrefix,'',$result->bill_number);
		if($prevbillno[0]==0) {
		    $bill_no = sprintf("%'.0".strlen($prevbillno)."d",intval($prevbillno)+1);
		}else {
		    $bill_no = intval($prevbillno)+1;
		    }
		return $billPrefix.$bill_no;
	    }
	    else{
		$bill_no = ($this->pos_settings->taxation_bill_start_from!='')?$this->pos_settings->taxation_bill_start_from:sprintf("%'.05d", 1);
		return $billPrefix.$bill_no;
	    }
	}
	
    }

	function BBQgenerate_bill_number($tableWhitelisted){ 
	$billNumReset = $this->Settings->billnumber_reset;
	$today = time();//strtotime('2018-05-01');
	switch($billNumReset){
	    case 1://daily
		$start_time = date('Y-m-d 00:00:01');
		$end_time = date('Y-m-d 23:59:59');
		$billnumber = $this->getbillNumber($tableWhitelisted,$start_time,$end_time,'daily');
		break;
	    case 2://weekly
		$start_date = date('Y-m-d', strtotime('monday this week', $today));
		$end_date = date('Y-m-d', strtotime('sunday this week', $today));
		$billnumber = $this->getbillNumber($tableWhitelisted,$start_date,$end_date);
		break;
	    case 3://monthly
		$start_date = date('Y-m-01', $today);
		$end_date = date('Y-m-t', $today);
		$billnumber = $this->getbillNumber($tableWhitelisted,$start_date,$end_date);
		break;
	    case 4://yearly
		$financial_yr_from = explode('/',$this->Settings->financial_yr_from);
		$financial_yr_to = explode('/',$this->Settings->financial_yr_to);
		$start_date = date('Y-'.$financial_yr_from[1].'-'.$financial_yr_from[0], $today);
		$end_date = date('Y-'.$financial_yr_to[1].'-'.$financial_yr_to[0],strtotime('+1 years'));
		$billnumber = $this->getbillNumber($tableWhitelisted,$start_date,$end_date,'yearly');
		break;
	    default://none
		$billnumber = $this->getbillNumber($tableWhitelisted);
		break;
	    
	}
	return $billnumber;
    }

    function BBQgetbillNumber($tableWhitelisted,$start=null,$end=null,$case=null){ 
	$this->db->select();
	if($case == "daily" && $start && $end){
	    $this->db->where(array('date>='=>$start,'date<'=>$end)); 
	}else if($case != "daily" && $start && $end){
	    $this->db->where(array('DATE(date)>='=>$start,'DATE(date)<'=>$end));
	}
	
	$this->db->where('bill_number!=','');
	if($tableWhitelisted){ 
	    $this->db->where('table_whitelisted',1);
	}else{
	    $this->db->where('table_whitelisted',0);
	}
	$this->db->order_by('id','desc');
	$this->db->limit(1);
	$q = $this->db->get('bils');

	$where = '';
		  if($case == "daily" && $start && $end){
		  	$where = " AND DATE(date) >= '".$start."' AND DATE(date) >= '".$end."'"; 
		  }else if($case == "yearly" && $start && $end){
		  	$where = "AND DATE(date)>= '".$start."'  AND DATE(date)<= '".$end."'"; 
		  }else if($case != "daily" && $start && $end){
		  	$where = "AND ATE(date)>='".$start."' AND DATE(date)<'".$end."'"; 
		  }
		  if($tableWhitelisted){
		  $where .= "AND table_whitelisted = 1";
		  }else{
		  	$where .= "AND table_whitelisted = 0";
		  }
	      $myQuery = "SELECT * FROM ".$this->db->dbprefix('bils')." WHERE bill_number !='' AND(payment_status='Completed' OR payment_status='Cancelled')  ".$where."  order by updated_at desc limit 1";

	if(!$tableWhitelisted){
	    if ($q->num_rows() > 0) {
		$result = $q->row();
		if($result->bill_number[0]==0) {
		    $bill_no = sprintf("%'.0".strlen($result->bill_number)."d",intval($result->bill_number)+1);
		}else {$bill_no = intval($result->bill_number)+1;}
		return $bill_no;
	    }
	    else{
		$bill_no = ($this->Settings->bill_number_start_from!='')?$this->Settings->bill_number_start_from:sprintf("%'.05d", 1);
		return $bill_no;
	    }
	}else{
	    $billPrefix = 'tw-';
	    if ($q->num_rows() > 0) {
		$result = $q->row();
		$prevbillno = str_replace($billPrefix,'',$result->bill_number);
		if($prevbillno[0]==0) {
		    $bill_no = sprintf("%'.0".strlen($prevbillno)."d",intval($prevbillno)+1);
		}else {
		    $bill_no = intval($prevbillno)+1;
		    }
		return $billPrefix.$bill_no;
	    }
	    else{
		$bill_no = ($this->Settings->bill_number_start_from!='')?$this->Settings->bill_number_start_from:sprintf("%'.05d", 1);
		return $billPrefix.$bill_no;
	    }
	}
	
    }
	
	public function CheckConsolidate($splits){

    	$myQuery = "SELECT P.bill_number
			FROM ".$this->db->dbprefix('bils')." AS P		
			JOIN ". $this->db->dbprefix('sales') ." AS S ON S.id = P.sales_id   	
			Where S.sales_split_id ='".$splits."' ";			
        $q = $this->db->query($myQuery);
        if ($q->num_rows() > 0) {
        	$res = $q->row();
            return $res->bill_number;           
        }
        return FALSE;       
	}

	/*BBQ END*/

    
    /**** one login at a time ****/
    function isloggeddIn($user){
        
        $q = $this->db
        ->select()
        ->from('user_logins')
        //->where("username ='$user' or email = '$user' ")
	->where("login_type='A' AND (username ='$user' or email = '$user' )")
        ->order_by('id','DESC')
        ->get();
        $data = $q->row_array();
        if($q->num_rows() > 0){
            if($data['status']=="logged_out"){
                return false;
            }else if(time()>strtotime($data['expiry'])){
               return false;
            }/*else if(time()>strtotime($data['last_activity'])+120){
               return false;
            }*/else{
               return true;
            }
        }
       return false;
    }
    function updateLoginStatus($data){
        $session_id = $this->session->userdata('session_id');
        $this->db->where('session_id',$session_id);
        $this->db->update('user_logins',$data);
    }
    function isActiveUser(){
	if($this->router->fetch_method()=="logout"){return true;}
	$session_id = $this->session->userdata('session_id');
	$login_user = $this->session->userdata('username');
        $login_email = $this->session->userdata('email');
        $q = $this->db
        ->select()
        ->from('user_logins')
        ->where("login_type='A' AND (username ='$login_user' or email = '$login_email' )")
	
        ->order_by('id','DESC')
        ->get();
	
	//print_R($q->row());
        if($q->num_rows()>0){
            $row = $q->row();//print_r($row);
	    //echo $session_id.'=='.$row->session_id;exit;
            if($session_id!=$row->session_id) {
		
		/*$data['status'] = "inactive";	*/	
		$this->updateLoginStatus($data);
		$this->session->set_flashdata(lang('someone has logged in'));
		$this->ion_auth->logout();
		admin_redirect('login');
	    }
        }
    }
    /**** one login at a time - End****/
    public function getAllPrinters() {
        $q = $this->db->get('printers');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    public function getAvilAbleTables(){

    	$current_date = date('Y-m-d');
		$current_date = $this->getTransactionDate();
    	$myQuery = "SELECT T.id,T.name
        FROM " . $this->db->dbprefix('restaurant_tables') . " T
        
            WHERE T.id NOT IN (SELECT table_id from srampos_orders WHERE payment_status IS NULL AND order_cancel_status = 0 AND DATE(date) ='".$current_date."')
             GROUP BY T.id";//
            
        $q = $this->db->query($myQuery);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	 public function getAvilAbleTables_dineIn(){

    	$current_date = date('Y-m-d');
		$current_date = $this->getTransactionDate();
    	$myQuery = "SELECT T.id,T.name
        FROM " . $this->db->dbprefix('restaurant_tables') . " T
        
            WHERE T.id NOT IN (SELECT table_id from srampos_orders WHERE payment_status IS NULL AND order_cancel_status = 0 AND DATE(date) ='".$current_date."') and T.sale_type='alacarte'
             GROUP BY T.id";//
            
        $q = $this->db->query($myQuery);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    public function getsplitsformerge($current_split){
    	$gp = $this->site->checkPermissions();    	
    	$current_date = date('Y-m-d');
    	$current_date = $this->getTransactionDate();
    	$user_id = $this->session->userdata('user_id');
    	 $where ='';
        if($gp[0]['pos-view_allusers_orders'] == 0){
            $where = "AND O.created_by =".$user_id."";
        }
    	$myQuery = "SELECT C.name,CONCAT(T.name, '[', O.split_id,']') AS name,split_id
        FROM " . $this->db->dbprefix('orders') . " O 
        JOIN " . $this->db->dbprefix('restaurant_tables') . " T ON T.id = O.table_id
        JOIN " . $this->db->dbprefix('companies') . " C ON C.id = O.customer_id
        WHERE  O.split_id !='".$current_split."' AND DATE(O.date) ='".$current_date."'  AND O.payment_status IS NULL AND O.order_cancel_status = 0  AND
        O.split_id NOT IN (SELECT S.sales_split_id from " . $this->db->dbprefix('sales') . "   AS S WHERE DATE(S.date) ='".$current_date."')".$where." AND O.order_type !=4
        GROUP BY O.split_id"; 
       $q = $this->db->query($myQuery);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }    
        
    function getAllPaymentMethods(){
	$q = $this->db->get_where('payment_methods', array('status' => 1));
        if ($q->num_rows() > 0) {
            return $q->result();
        }
        return FALSE;
    }
    function getAvilAbleCustomers(){
	$q = $this->db->get_where('companies', array('group_name' => 'customer'));//print_R($this->db->error());
	if ($q->num_rows() > 0) {
           
                $data = $q->result();
            
            return $data;
        }
        return FALSE;
    }

    public function getCustomerDiscountval($id){

    	$myQuery = "SELECT CD.name
			FROM ".$this->db->dbprefix('diccounts_for_customer')." AS CD			
			Where CD.id =".$id." ";			
        $q = $this->db->query($myQuery);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data = $row->name;
            }
            return $data;
        }
        return FALSE;
       
	}
    function is_uniqueDiscountExist($checkformulti=false){
	$date = date('Y-m-d');
        $current_time = date('H:i:s');
        $mydate=getdate(date("U"));
        $today = "$mydate[weekday]";
	$uniqueQuery = "SELECT *,D.id as discount_id,DC.from_date,DC.to_date,DC2.from_time,DC2.to_time,DC1.days from " . $this->db->dbprefix('discounts') . " D
		left JOIN " . $this->db->dbprefix('discount_conditions') . " DC ON D.id = DC.discount_id AND DC.condition_method ='condition_date'
		left JOIN srampos_discount_conditions DC2 ON D.id = DC2.discount_id AND DC2.condition_method ='condition_time'
		
		left JOIN " . $this->db->dbprefix('discount_conditions') . " DC1 ON D.id = DC1.discount_id AND DC1.condition_method ='condition_days'
		WHERE
		
		((DC.from_date IS NOT NULL AND DATE('".$date."') >= DATE(DC.from_date) and DATE('".$date."') <= DATE(DC.to_date) AND DC2.from_time IS NOT NULL AND CAST('".$current_time."' AS time) BETWEEN DC2.from_time AND DC2.to_time AND DC1.days IS NOT NULL AND FIND_IN_SET('".$today."' ,DC1.days) )
		OR  
		( DC.from_date IS NOT NULL AND DATE('".$date."') >= DATE(DC.from_date) and DATE('".$date."') <= DATE(DC.to_date) AND DC2.from_time IS NULL  AND DC1.days IS NOT NULL AND FIND_IN_SET('".$today."' ,DC1.days) )
		
		
		OR
		    ( DC.from_date IS NOT NULL AND DATE('".$date."') >= DATE(DC.from_date) and DATE('".$date."') <= DATE(DC.to_date) AND  DC2.from_time IS NOT NULL AND CAST('".$current_time."' AS time) BETWEEN DC2.from_time AND DC2.to_time AND DC1.days IS NULL )
		OR
		    ( DC.from_date IS NULL AND DC2.from_time IS NOT NULL AND CAST('".$current_time."' AS time) BETWEEN DC2.from_time AND DC2.to_time AND DC1.days IS NOT NULL AND FIND_IN_SET('".$today."' ,DC1.days))
		
		OR  
		( DC.from_date IS NOT NULL AND DATE('".$date."') >= DATE(DC.from_date) and DATE('".$date."') <= DATE(DC.to_date) AND DC2.from_time IS NULL  AND DC1.days IS NULL )
		OR
		    ( DC.from_date IS NULL AND  DC2.from_time IS NOT NULL AND CAST('".$current_time."' AS time) BETWEEN DC2.from_time AND DC2.to_time AND  DC1.days IS NULL )
		OR
		    ( DC.from_date IS NULL AND DC2.from_time IS NULL  AND DC1.days IS NOT NULL AND FIND_IN_SET('".$today."' ,DC1.days)   ))
		    
		   
	        AND D.discount_status=1 AND D.unique_discount=1 order by D.id ";//DESC LIMIT 1";
		//( DATE(NOW()) >= DATE(DC.from_date) and DATE(NOW()) <= DATE(DC.to_date))
		//	    AND  FIND_IN_SET('".$today."' ,DC1.days) AND D.unique_discount=1 order by D.id DESC LIMIT 1
		//";
		$uniqueDaysDis = $this->db->query($uniqueQuery);
		//echo '<pre>';print_R($uniqueDaysDis->result());exit;
		//echo $uniqueDaysDis->num_rows();
		if(!$checkformulti && $uniqueDaysDis->num_rows()>0){
		    if($uniqueDaysDis->num_rows()==1){
			return $uniqueDaysDis->row();
		    }else if($uniqueDaysDis->num_rows()>1){
			foreach($uniqueDaysDis->result() as $k => $row){
			    if(date('Y-m-d',strtotime($row->apply_for_today)) == date('Y-m-d')){
				return $row;
			    }
			}			
		    }
		   
		} else if($checkformulti && $uniqueDaysDis->num_rows()>1){
		    $hasdisToday = false;
			foreach($uniqueDaysDis->result() as $k => $row){
			    if(date('Y-m-d',strtotime($row->apply_for_today)) == date('Y-m-d')){
				$hasdisToday = true;
			    }
			}
			if(!$hasdisToday){
				
			    return $uniqueDaysDis->result();
			}
		    }
		return array();
    }
	
    function set_unique_discount($id){
	$data['apply_for_today'] = date('Y-m-d');
	$this->db->where('id',$id);
	$this->db->update('discounts',$data);
	
    }
    
    public function getAllrecipeCategories_items($sales_type =NULL) {
        $this->db->where('type',0)
	->where('(parent_id="null" or parent_id=0)')->order_by('id');
        $q = $this->db->get("recipe_categories");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $k => $row) {
		$data[$k] = $row;
                $data[$k]->sub_category = $this->getrecipeSubCategories($row->id);
		foreach($data[$k]->sub_category as $kk => $row1){
		     $data[$k]->sub_category[$kk]->recipes = $this->getrecipeBySubCategories($row1->id,$sales_type);
		}
            }
	    //print_R($data);exit;
            return $data;
        }
        return FALSE;
    }
    function getrecipeBySubCategories($sub_id,$sales_type=NULL){
    	if($sales_type[0]->sale_type == "bbq"){
    		$this->db->where('recipe_standard !=',1);
    	}elseif($sales_type[0]->sale_type == "alacarte"){
    		$this->db->where('recipe_standard !=',2);
    	}
	$this->db->where('subcategory_id', $sub_id)->order_by('name');
        $q = $this->db->get("recipe");
        // print_r($this->db->last_query());die;
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    
	public function Check_birthday_discount_isavail($customer_id){

		$current_date =date("Y-m");
		$pos_settings = $this->get_posSetting();
		
		if($pos_settings->birthday_enable != 0  && $pos_settings->birthday_discount != 0){
			
			$customer_birthday = "SELECT C.id, C.birthday FROM ".$this->db->dbprefix('companies')." AS C where DATE_FORMAT(C.birthday, '%m') = DATE_FORMAT(NOW(), '%m') AND C.id=".$customer_id."";				
			$c = $this->db->query($customer_birthday); 
			if ($c->num_rows() > 0) {
				$check_discount_aflied = "SELECT B.id FROM ".$this->db->dbprefix('birthday')." AS B Where DATE_FORMAT(B.issue_date, '%Y-%m') = DATE_FORMAT(NOW(), '%Y-%m') AND B.customer_id=".$customer_id."";
				
				
				$d = $this->db->query($check_discount_aflied);
				if ($d->num_rows() >= 0) {
					if($d->row('id') == ''){
	            		return true;
					}else{
						return FALSE;
					}
	            }
				return FALSE;
			}
			return FALSE;
		}
		return FALSE;
	}
	public function Check_bbq_birthday_discount_isavail($customer_id){

		$current_date =date("Y-m");
		$pos_settings = $this->get_posSetting();
		
		if($this->pos_settings->birthday_enable_bbq != 0  && $this->pos_settings->birthday_discount_for_bbq != 0){
			
			$customer_birthday = "SELECT C.id, C.birthday FROM ".$this->db->dbprefix('companies')." AS C where DATE_FORMAT(C.birthday, '%m') = DATE_FORMAT(NOW(), '%m') AND C.id=".$customer_id."";				
			$c = $this->db->query($customer_birthday); 
			if ($c->num_rows() > 0) {
				$check_discount_aflied = "SELECT B.id FROM ".$this->db->dbprefix('birthday')." AS B Where DATE_FORMAT(B.issue_date, '%Y-%m') = DATE_FORMAT(NOW(), '%Y-%m') AND B.customer_id=".$customer_id."";
				
				
				$d = $this->db->query($check_discount_aflied);
				if ($d->num_rows() >= 0) {
					if($d->row('id') == ''){
	            		return true;
					}else{
						return FALSE;
					}
	            }
				return FALSE;
			}
			return FALSE;
		}
		return FALSE;
	}	    
    function CalculatesimpleDiscount($itemdata){
		
	
	$dis = 0;
	//foreach($itemdata as $k => $row) {
	      $id = $itemdata->recipe_id;
		 $row['net_unit_price'];
	      $subtotal = $itemdata->subtotal;
	    $discount = $this->discountMultiple($id);
	
	    if(!empty($discount)){
		
                           
		if($discount[2] == 'percentage_discount'){

		    $discount_value = $discount[1].'%';

		}else{
		    $discount_value =$discount[1];
		}
		$dis += $this->site->calculateDiscount($discount_value, $subtotal);
		
		return $dis;
	    }
		
	//}
	return $dis;
    }
    function CalculateDiscount_onTotal($total,$existing_dis){
	$Total_Discount = $this->TotalDiscount();		
		
		if($Total_Discount[0] != 0)
                    { 
                         
                         if($Total_Discount[3] == 'percentage_discount'){

                                $totdiscount = $Total_Discount[1].'%';

                            }else{
                                $totdiscount =$Total_Discount[1];
                            }
                            
                        $totdiscount1 = $this->calculateDiscount($totdiscount, $value);
			$sub_total =array_sum($total) - array_sum($existing_dis);
			if($Total_Discount[2]  <= $sub_total){
			    return $Total_Discount[2];
			}else{
			    return 0;
			}
		    }
                return 0;
    }
    
    function setTimeout($fn,$reference,$count){
	$Settings = $this->site->get_setting();
	$timeout = $Settings->notification_time_interval;
	// sleep for $timeout milliseconds.
	sleep($timeout);

	$this->$fn($reference,$count);
    }
	
	function setTimeoutStart($fn,$reference,$count){
	$Settings = $this->site->get_setting();
	$timeout = $Settings->notification_start_interval;
	// sleep for $timeout milliseconds.
	sleep($timeout);

	$this->$fn($reference,$count);
    }
	
    function is_bbqCoversValidated($reference,$count){
	$Settings = $this->site->get_setting();
	$interval = $Settings->notification_time_interval;
	$no_of_times = $Settings->bbq_notify_no_of_times;
	$this->load->library('socketemitter');
	$this->load->library('push');
	$now = date('Y-m-d H:i:s');
	$today = date('Y-m-d');
	$this->db->select('*');
	$this->db->where('split_id',$reference);
	$this->db->where('stop',0);
	$this->db->where('tag','bbq-cover-validation');
	$this->db->from('notiy')
	->where('DATE(created_on)', $today);
	//if($count==1){
	//    $this->db->where('is_read', 0);	    
	//}else{
	//    $this->db->having('SUM(is_read) = 0');
	//}
	//$this->db->where('TIMESTAMPDIFF(SECOND,created_on, "'.$now.'")>'.$interval);
	
	//->where('TIMESTAMPDIFF(SECOND,created_on, "'.$now.'")>30');
	$this->db->group_by('split_id,table_id');
	$this->db->order_by('id','ASC');
	$this->db->limit(1);
	//echo $this->db->get_compiled_select();
	$q = $this->db->get();//echo '<pre>';print_R($q->result());exit;
	$bbqWaiting = $this->db->get_where('bbq',array('reference_no'=>$reference,'status'=>'waiting'));
	if($bbqWaiting->num_rows()>0 && $q->num_rows()>0){
	    $data = $q->result();
	    
	    foreach($data as $k => $row){
		$touser = $row->to_user_id;
		$tableid = $row->table_id;
		if($count<$no_of_times){
		    $this->db->select('*');
		    $this->db->where('id',$touser);
		    $this->db->from('users');
		    $u = $this->db->get();
		    $users = $u->result();
		}
		else if($count==$no_of_times || $count==$no_of_times+1){
		    $t = $this->db->get_where('restaurant_tables',array('id'=>$tableid))->row();
		    $areaID = $t->area_id;
		    $q = $this->db->get_where('restaurant_tables',array('area_id'=>$areaID))->result();
		    $AreaUsers = array();
		    foreach($q as $k => $urow){
			array_push($AreaUsers,$urow->steward_id);
		    }
		    $AreaUsers = array_unique($AreaUsers);
		    $this->db->select('*');
		    $this->db->where_in('id', $AreaUsers);
		    $this->db->where('id !=', $touser);
		    $this->db->from('users');
		    $u = $this->db->get();
		    $users = $u->result();
		}else{
		    $this->db->select('*');
		    $this->db->where_in('group_id', array(5,7,8,10));
		    $this->db->where('id !=', $touser);
		    $this->db->from('users');
		    $u = $this->db->get();
		    $users = $u->result();
		}
		$table_id = $row->table_id;
		$table_name = $this->getTablename($table_id);
		foreach($users as $k1 => $user){
			
			$group_id = $user->group_id;
			$warehouse_id = $row->warehouse_id;
			$notification_message = $table_name.' - Customer has sent BBQ Covers.';
			$notification_title = 'BBQ Covers validation request - '.$reference;
			//$notification_array['from_role'] = $group_id;
			$user_id = $user->id;
			
			$notification_array['insert_array'] = array(
			    'msg' => $notification_message,
			    'type' => $notification_title,
			    'table_id' => $table_id,
			    'user_id' => $row->user_id,
			    'to_user_id' => $user_id,	
			    'role_id' => $group_id,
			    'warehouse_id' => $warehouse_id,
			    'created_on' => date('Y-m-d H:m:s'),
			    'is_read' => 0,
			    'respective_steward'=>$row->respective_steward,
                            'split_id'=>$reference,
                            'tag'=>'bbq-cover-validation',
			    //'reference'=>$row->reference,
			);
			$notifyID = $this->add_notification($notification_array);
			
			$device_token = $this->site->deviceDetails($user_id);
			foreach($device_token as $k =>$device){
			    $title = $notification_title;
			    $message = $notification_message;
			    $push_data = $this->push->setPush($title,$message);
			    if($this->isSocketEnabled() && $push_data == true && isset($device->socket_id) && $device->socket_id!=''){
				    $json_data = '';
				    $response_data = '';
				    $json_data = $this->push->getPush();
				    $regId_data = $device->device_token;
				    $socketid = $device->socket_id;
				    //$response_data = $this->firebase->send($regId_data, $json_data);
				    //var_dump($response_data);
				    
				    $bbq_code = $reference;
				    $table_id = $table_id;
				    $this->site->send_BBQpushNotification($title,$message,$socketid,$bbq_code,$table_id,$notifyID,'bbq_cover_validation');
		    
		    
			    }
		    }
		
		}
	    }
	    //$notification['title'] = $notification_title;
	    //$notification['msg'] = $notification_message;
	    //$event = 'notification';
	    //$edata = $notification;
	    //$this->socketemitter->setEmit($event, $edata);
	    //if($count<3){
		$count++;
		$this->setTimeout('is_bbqCoversValidated',$reference,$count);
	   // }	    
	    
	}
    }
    public function deviceGET($user_id){
		$this->db->select('users.id, device_detail.device_token');
		$this->db->join('device_detail', 'device_detail.user_id = users.id', 'left');
		$this->db->where('users.id', $user_id);
		$q = $this->db->get('users');
		if ($q->num_rows() > 0) {
							
			foreach($q->result() as $row){
				$data[] = $row->device_token;
			}
			return $data;
			
		}
		return FALSE;
	}
	public function deviceDetails($user_id){
		$this->db->select('users.id, device_detail.device_token,socket_id');
		$this->db->join('device_detail', 'device_detail.user_id = users.id');
		$this->db->where('users.id', $user_id);
		$this->db->group_by('device_detail.user_id,device_detail.devices_key');
		$q = $this->db->get('users');
		
		if ($q->num_rows() > 0) {
							
			foreach($q->result() as $row){
				$data[] = $row;
			}
			return $data;
			
		}
		return FALSE;
	}
    public function getTablename($table_id){
		$this->db->select('*')->where('id', $table_id);
		$q = $this->db->get('restaurant_tables');
        if ($q->num_rows() > 0) {
            return $q->row('name');
        }
		return TRUE;
	}
    function send_pushNotification($title,$msg,$socketid,$type="general"){
	$this->load->library('socketemitter');
	$push_notify['title'] = $title;
	$push_notify['msg'] = $msg;
	$push_notify['type'] = $type;
	$push_notify['socket_id'] = $socketid;
	$event = 'push_notification';
	$edata = $push_notify;
	$this->socketemitter->setEmit($event, $edata);
    }
    function send_BBQpushNotification($title,$msg,$socketid,$bbq_code,$tableid,$notifyid,$type="general"){
	$this->load->library('socketemitter');
	$push_notify['title'] = $title;
	$push_notify['msg'] = $msg;
	$push_notify['type'] = $type;
	$push_notify['socket_id'] = $socketid;
	$push_notify['bbq_code'] = $bbq_code;
	$push_notify['notify_id'] = $notifyid;
	$push_notify['table_id'] = $tableid;
	$event = 'bbq_push_notification';
	$edata = $push_notify;
	$this->socketemitter->setEmit($event, $edata);
    }
    function send_billRequestpushNotification($title,$msg,$socketid,$splitid,$tableid,$notifyid,$request_type,$type){
	$this->load->library('socketemitter');

	$push_notify['title'] = $title;
	$push_notify['msg'] = $msg;
	$push_notify['socket_id'] = $socketid;
	$push_notify['split_id'] = $splitid;
	$push_notify['notify_id'] = $notifyid;
	$push_notify['table_id'] = $tableid;
	$push_notify['request_type'] = $request_type;// 1 - to steward // 2- to cashier
	$push_notify['sale_type'] = $type;// bbq or alacarte
	$event = 'billRequest_push_notification';
	$edata = $push_notify;
	$this->socketemitter->setEmit($event, $edata);
    }
    function is_unique_category($pid,$value,$id){
	$this->db->select('*')
	->where(array('name'=>$value,'parent_id'=>$pid,'type'=>1));
	if($id){
	    $this->db->where(array('id !='=>$id));
	}
	$q = $this->db->get('recipe_categories');
        if ($q->num_rows() > 0) {
            return true;
        }
	return false;
    }
    function is_unique_recipeCategories($pid,$value,$id){
	$this->db->select('*')
	->where(array('name'=>$value,'parent_id'=>$pid,'type'=>0));
	if($id){
	    $this->db->where(array('id !='=>$id));
	}
	$q = $this->db->get('recipe_categories');
        if ($q->num_rows() > 0) {
            return true;
        }
	return false;
    }
    
    function start_server(){
	$settings = $this->get_setting();
	$host = str_replace('http://','',$settings->socket_host);
	$connection = @fsockopen($host, $settings->socket_port);
	//if(!is_resource($connection)){
	//    exec('START '.FCPATH.'startserver.bat');
	//    return true;
	//}else{
	//    return true;
	//}
	return false;
    }
    function getAllStewards(){
        $this->db->select()
	    ->from('users')
	    ->where_in('group_id',array(5,7))
	    ->where('active',1);
	$q = $this->db->get();
        if ($q->num_rows() > 0) {
            return $q->result();
        }
        return FALSE;
    }
    public function getSteward($tableid){
		$q = $this->db->get_where('restaurant_tables',array('id'=>$tableid));
		if ($q->num_rows() > 0) {
			$data = $q->row();
			return $data->steward_id;
		}
        return FALSE;
    }
    function isTableProcessing($table_id){
	$current_date = date('Y-m-d');	
	    $this->db->select();
	    $this->db->from('bbq');
	    $this->db->where('table_id',$table_id);
	    $this->db->where('DATE(created_on)', $current_date);
	    //echo $this->db->get_compiled_select();
	    $this->db->limit(1);
	    $this->db->order_by('id','DESC');//echo $this->db->get_compiled_select();
	    $q = $this->db->get();
	    if ($q->num_rows() > 0) {
		
		$data = $q->row();
		$status = strtolower($data->status);
		if($status=="open" || $status=="waiting"){
		    return true;
		}else if($status=="closed" && $data->payment_status=="paid"){
		    $salereturn = $this->db->get_where('sale_return',array('split_id'=>$data->reference_no));
		    return ($salereturn->num_rows() > 0)?true:false;
		}else{
		    return false;
		}
	    }else{
		$current_date = date('Y-m-d');
		$myQuery = "SELECT *
		FROM " . $this->db->dbprefix('orders') . " WHERE table_id ='".$table_id."'  AND DATE(date) ='".$current_date."'  AND payment_status IS NULL AND order_cancel_status = 0";
		    
		$q = $this->db->query($myQuery);
		if ($q->num_rows() > 0) {
		    
		    return true;
		}else{
		    return false;
		}
		
	    }
	    return false;//allow login
    }
    function getBBQSteward($bbqcode){
	$q = $this->db->get_where('bbq',array('reference_no'=>$bbqcode))->row();
	return $q->confirmed_by;
    }
    
    function send_BBQReturnpushNotification($title,$msg,$socketid,$bbq_code,$tableid,$notifyid,$type="general"){
	$this->load->library('socketemitter');
	$push_notify['title'] = $title;
	$push_notify['msg'] = $msg;
	//$push_notify['type'] = $type;
	$push_notify['socket_id'] = $socketid;
	$push_notify['bbq_code'] = $bbq_code;
	$push_notify['notify_id'] = $notifyid;
	$push_notify['table_id'] = $tableid;
	$event = 'bbq_return_push_notification';
	$edata = $push_notify;
	$this->socketemitter->setEmit($event, $edata);
    }
    function is_bbqReturnCompleted($reference,$count){
	$Settings = $this->site->get_setting();
	$interval = $Settings->notification_time_interval;
	$no_of_times = $Settings->bbq_notify_no_of_times;
	$this->load->library('socketemitter');
	$this->load->library('push');
	$now = date('Y-m-d H:i:s');
	$today = date('Y-m-d');
	$this->db->select('*');
	$this->db->where('split_id',$reference);
	$this->db->where('stop', 0);
	$this->db->where('tag','bbq-return');
	$this->db->from('notiy')
	->where('DATE(created_on)', $today);
	//if($count==1){
	//    $this->db->where('is_read', 0);	    
	//}else{
	//    $this->db->having('SUM(is_read) = 0');
	//}
	//$this->db->where('TIMESTAMPDIFF(SECOND,created_on, "'.$now.'")>'.$interval);
	
	//->where('TIMESTAMPDIFF(SECOND,created_on, "'.$now.'")>30');
	$this->db->group_by('split_id,table_id');
	$this->db->order_by('id','ASC');
	$this->db->limit(1);
	//echo $this->db->get_compiled_select();
	$q = $this->db->get();//echo '<pre>';print_R($q->result());exit;
	$salereturn = $this->db->get_where('sale_return',array('split_id'=>$reference));
	if($salereturn->num_rows()==0 && $q->num_rows()>0){
	    $data = $q->result();
	    
	    foreach($data as $k => $row){
		$touser = $row->to_user_id;
		$tableid = $row->table_id;
		if($count<$no_of_times){
		    $this->db->select('*');
		    $this->db->where('id',$touser);
		    $this->db->from('users');
		    $u = $this->db->get();
		    $users = $u->result();
		}
		else if($count==$no_of_times || $count==$no_of_times+1){
		    $t = $this->db->get_where('restaurant_tables',array('id'=>$tableid))->row();
		    $areaID = $t->area_id;
		    $q = $this->db->get_where('restaurant_tables',array('area_id'=>$areaID))->result();
		    $AreaUsers = array();
		    foreach($q as $k => $urow){
			array_push($AreaUsers,$urow->steward_id);
		    }
		    $AreaUsers = array_unique($AreaUsers);
		    $this->db->select('*');
		    $this->db->where_in('id', $AreaUsers);
		    $this->db->where('id !=', $touser);
		    $this->db->from('users');
		    $u = $this->db->get();
		    $users = $u->result();
		}else{
		    $this->db->select('*');
		    $this->db->where_in('group_id', array(5,7,8,10));
		    $this->db->where('id !=', $touser);
		    $this->db->from('users');
		    $u = $this->db->get();
		    $users = $u->result();
		}
		$table_id = $row->table_id;
		$table_name = $this->getTablename($table_id);
		foreach($users as $k1 => $user){
			
			$group_id = $user->group_id;
			$warehouse_id = $row->warehouse_id;
			$notification_message = $table_name.' - Customer has requested BBQ Return.';
			$notification_title = 'BBQ Return Request - '.$reference;
			//$notification_array['from_role'] = $group_id;
			$user_id = $user->id;
			
			$notification_array['insert_array'] = array(
			    'msg' => $notification_message,
			    'type' => $notification_title,
			    'table_id' => $table_id,
			    'user_id' => $row->user_id,
			    'to_user_id' => $user_id,	
			    'role_id' => $group_id,
			    'warehouse_id' => $warehouse_id,
			    'created_on' => date('Y-m-d H:m:s'),
			    'is_read' => 0,
			    'respective_steward'=>$row->respective_steward,
                            'split_id'=>$reference,
                            'tag'=>'bbq-return',
			    //'reference'=>$row->reference,
			);
			$notifyID = $this->add_notification($notification_array);
			
			$device_token = $this->site->deviceDetails($user_id);
			foreach($device_token as $k =>$device){
			    $title = $notification_title;
			    $message = $notification_message;
			    $push_data = $this->push->setPush($title,$message);
			    if($this->isSocketEnabled() && $push_data == true && isset($device->socket_id) && $device->socket_id!=''){
				    $json_data = '';
				    $response_data = '';
				    $json_data = $this->push->getPush();
				    $regId_data = $device->device_token;
				    $socketid = $device->socket_id;
				    //$response_data = $this->firebase->send($regId_data, $json_data);
				    //var_dump($response_data);
				    
				    $bbq_code = $reference;
				    $table_id = $table_id;
				    $this->site->send_BBQReturnpushNotification($title,$message,$socketid,$bbq_code,$table_id,$notifyID,'bbq_return');
		    
		    
			    }
		    }
		
		}
	    }
	    //$notification['title'] = $notification_title;
	    //$notification['msg'] = $notification_message;
	    //$event = 'notification';
	    //$edata = $notification;
	    //$this->socketemitter->setEmit($event, $edata);
	    //if($count<3){
		$count++;
		$this->setTimeout('is_bbqReturnCompleted',$reference,$count);
	   // }	    
	    
	}
    }
    function socket_refresh_tables($tableid){
	$this->load->library('socketemitter');
	$refreshTable['tableid'] = $tableid;	
	$event = 'update_table';
	$edata = $refreshTable;
	$this->socketemitter->setEmit($event, $edata);
    }
    function socket_refresh_bbqtables($tableid){
	$this->load->library('socketemitter');
	$refreshTable['tableid'] = $tableid;	
	$event = 'update_bbqtable';
	$edata = $refreshTable;
	$this->socketemitter->setEmit($event, $edata);
    }


	public function create_or_get_manual_recipe_details($recipe_name,$unit_price){
		/*echo "<pre>";
		print_r($this->settings);die;*/		
		$category = 'OPEN SALE ITEM';

	    $check_category = "SELECT RC.id,RC.name
 		    FROM ".$this->db->dbprefix('recipe_categories')." AS RC
   			where RC.name='".$category."'";   	

		$c = $this->db->query($check_category);

		$default_kitchen = 0;

		$get_default_kitchen = "SELECT RK.id
 		    FROM ".$this->db->dbprefix('restaurant_kitchens')." AS RK
   			where RK.is_default = 1 ";   

		   $k = $this->db->query($get_default_kitchen);  
           
			if ($k->num_rows() > 0) {				
				$result = $k->row();
				$default_kitchen = $result->id;
			}

	if ($c->num_rows() > 0) {		

		 $category_id = "SELECT RC.id,RC.name
 		    FROM ".$this->db->dbprefix('recipe_categories')." AS RC
   			where RC.name='".$category."' AND RC.parent_id = 0";  
		$c_id = $this->db->query($category_id);
           
           $catid =$c_id->row();
           $category_id = $catid->id;

            $sub_category_id = "SELECT RC.id,RC.name
 		    FROM ".$this->db->dbprefix('recipe_categories')." AS RC
   			where RC.name='".$category."' AND RC.parent_id = ".$category_id.""; 
   			   			
		   $sub_id = $this->db->query($sub_category_id);
           
           $subcatid =$sub_id->row();
           $subcategory = $subcatid->id;
           
           $data = array(
				'code' => 99999,
				'khmer_name' => $recipe_name ? $recipe_name : 0,
				'khmer_image' => str_replace(' ', '-',$recipe_name).'.png',                
                'name' => $recipe_name,
				'currency_type' => $this->settings->default_currency ? $this->settings->default_currency :0,
				'kitchens_id' => $default_kitchen ? $default_kitchen : 0,
                'type' => 'manual',
				'stock_quantity' => 0,				
                'category_id' => $category_id ? $category_id : 0,
                'subcategory_id' => $subcategory ? $subcategory : 0,                
				'cost' => $unit_price ? $unit_price : 0,
				'price' => $unit_price ? $unit_price : 0,                
				'active' => 1,
                'hide' =>  0,
		        'preparation_time' =>600,
            );
           /*echo "<pre>";
           print_r($data);die;*/
            if ($this->db->insert('recipe', $data)) {
            	
             $recipe_id = $this->db->insert_id();

               $warehouse = array(
				'recipe_id' => $recipe_id ? $recipe_id : 0,
				'warehouse_id' => $this->session->userdata('warehouse_id') ? $this->session->userdata('warehouse_id') : 0,
               );
               
               $this->db->insert('warehouses_recipe', $warehouse);   
                return $recipe_id;
               /*var_dump($recipe_id);die;*/
            }
            else{            	
            	  return 0;
            }                      
        }
        else{     

            $insert_category = array(
            'code' => 99999,
            'name' => $category,
            'parent_id' => 0,
            'kitchens_id' => $default_kitchen ? $default_kitchen :0,                        
        );
           $responce = $this->db->insert('recipe_categories', $insert_category);
           $cat_id = $this->db->insert_id();

           $this->db->insert('recipe_categories', $insert_category);

           $sub_cat_id = $this->db->insert_id();

           $this->db->update('recipe_categories', array('parent_id' => $cat_id), array('id' => $sub_cat_id));

            $data = array(
				'code' => 99999,
				'khmer_name' => $recipe_name ? $recipe_name : 0,
				'khmer_image' => str_replace(' ', '-',$recipe_name).'.png',                
                'name' => $recipe_name,
				'currency_type' => $this->settings->default_currency ? $this->settings->default_currency :0,
				'kitchens_id' => $default_kitchen ? $default_kitchen : 0,
                'type' => 'manual',
				'stock_quantity' => 0,				
                'category_id' => $cat_id ? $cat_id : 0,
                'subcategory_id' => $sub_cat_id ? $sub_cat_id : 0,                
				'cost' => $unit_price ? $unit_price : 0,
				'price' => $unit_price ? $unit_price : 0,                
				'active' => 1,
                'hide' =>  0,
		        'preparation_time' =>600,
            );
              

            if ($this->db->insert('recipe', $data)) {
             $recipe_id = $this->db->insert_id();
             $warehouse = array(
				'recipe_id' => $recipe_id ? $recipe_id : 0,
				'warehouse_id' => $this->session->userdata('warehouse_id') ? $this->session->userdata('warehouse_id') : 0,
            );
                 $this->db->insert('warehouses_recipe', $warehouse);             
                  return $recipe_id;
            }
            else{
            	  return 0;
            }
        }         
    }
    function create_nightauditDate(){
	$data['currentdate'] = date('Y-m-d H:i:s');
	
	$q = $this->db->get_where('transaction_date',array('date(currentdate)'=>date('Y-m-d')));
	if($q->num_rows()==0){
	    $this->db->insert('transaction_date',$data);
	    return $this->db->insert_id();
	}
	
    }
    function check_nightauditDate(){//$transactionDate today or yesterday
	$curdate= date('Y-m-d');
	$user_number = $this->session->userdata('user_number');
	$q = $this->db->get_where('transaction_date',array('date(currentdate)'=>$curdate,'transaction_date !='=>'0000-00-00 00:00:00'));
	
	if($q->num_rows()>0){
	    $transactionDate = $q->row('transaction_date');
	    if($this->isNightaudit_done($transactionDate)){
		$return = $curdate;
		$this->update_nightauditDate('today');
	    }else{
		$return = $q->row('transaction_date');
	    }
	    echo json_encode(array('status'=>true,'date'=>$return,'user'=>$user_number));
	}else{
	    $date  = date('Y-m-d');
	    $transactionDate = date('Y-m-d', strtotime($date .' -1 day'));
	    if($this->isNightaudit_done($transactionDate)){
		$return = $curdate;
		$this->update_nightauditDate('today');
		echo json_encode(array('status'=>true,'date'=>$return,'user'=>$user_number));
	    }else{
		$lastTrandate = $this->getLastTransactionDate();
		$lastTrandate = date('d-m-Y',strtotime($lastTrandate));
		if($lastTrandate!=date('d-m-Y')){
		    echo json_encode(array('status'=>false,'date'=>$lastTrandate,'user'=>$user_number));
		}else{
		    $this->update_nightauditDate('today');
		    echo json_encode(array('status'=>true,'date'=>$lastTrandate,'user'=>$user_number));
		}
	    }
	    
	}
	exit;
	
    }
    function isNightaudit_done($date){
	$date = date('Y-m-d',strtotime($date));
	$q = $this->db->get_where('nightaudit',array('date(nightaudit_date)'=>$date));	
	if($q->num_rows()>0){
	    return true;
	}
	return false;
    }
    function update_nightauditDate($transactionDay='today'){//$transactionDate today or lastday
	
	$currentdate = date('Y-m-d');
	if($transactionDay=="today"){
	    $data['transaction_date'] = date('Y-m-d').' 00:00:00';
	}else{
	    $data['transaction_date'] = $this->getLastTransactionDate();//date('Y-m-d',strtotime("-1 days")).' 00:00:00';
	}
	$data['approved_by'] = $this->session->userdata('user_id');
	
	$q = $this->db->get_where('transaction_date',array('date(currentdate)'=>$currentdate));
	if($q->num_rows()==0){
	    $this->create_nightauditDate();
	}
	$this->db->where('date(currentdate)',$currentdate);
	$this->db->order_by('id','desc');
	$this->db->limit(1);
	$this->db->update('transaction_date',$data);
	
	return $data['transaction_date'];
    }
    function getTransactionDate_nightaudit(){
	$curdate = date('Y-m-d');
	$q = $this->db->get_where('transaction_date',array('date(currentdate)'=>$curdate,'transaction_date !='=>'0000-00-00 00:00:00'));
	if($q->num_rows()>0){	    
	    return date('Y-m-d', strtotime($q->row('transaction_date')));
	}
	return false;
	
    }
    function getTransactionDate(){
	$curdate = date('Y-m-d');
	//$q = $this->db->get_where('transaction_date',array('date(currentdate)'=>$curdate,'transaction_date !='=>'0000-00-00 00:00:00'));
	$q = $this->db->get_where('transaction_date',array('date(currentdate)'=>$curdate));
	
	if($q->num_rows()>0){
	    if($q->row('transaction_date') == '0000-00-00 00:00:00'){
		$data['transaction_date'] = date('Y-m-d');
		$this->db->where('id',$q->row('id'));
		$this->db->update('transaction_date',$data);
		return $data['transaction_date'];
	    }else{
		return date('Y-m-d', strtotime($q->row('transaction_date')));
	    }
	    
	}else{
	    $data['currentdate'] = date('Y-m-d');
	    $data['transaction_date'] = date('Y-m-d');
	    $this->db->insert('transaction_date',$data);
	    return $data['transaction_date'];
	}
	return false;
	
    }
    function getLastTransactionDate(){
	$this->db->select();
	$this->db->from('transaction_date');
	$this->db->where(array('transaction_date !='=>'0000-00-00 00:00:00'));
	$this->db->order_by('id','desc');
	$this->db->limit(1);
	$q = $this->db->get();
	//print_R($q->row());
	if($q->num_rows()>0){	    
	    return date('Y-m-d', strtotime($q->row('transaction_date')));
	}
	return date('Y-m-d');
	
    }
    function getLastDayTransactionDate(){
	$curdate = date('Y-m-d');
	$this->db->select();
	$this->db->from('transaction_date');
	$this->db->where(array('date(currentdate) <'=>$curdate,'transaction_date !='=>'0000-00-00 00:00:00'));
	$this->db->order_by('id','desc');
	$this->db->limit(1);
	$q = $this->db->get();
	//print_R($q->row());
	if($q->num_rows()>0){	    
	    return date('Y-m-d', strtotime($q->row('transaction_date')));
	}
	return false;
	
    }
    function isSocketEnabled(){
	$data = $this->get_setting();
	return @$data->socket_enable;
    }
    function update_dbbackup_date($data){
	
	$this->db->update('ftp_backup',$data);
    }
    function update_filesbackup_date($data){
	
	$this->db->update('ftp_backup',$data);
    }
    function update_ftpbackup($data){
	
	$this->db->update('ftp_backup',$data);
    }
    function getAutoback_details(){
	$q = $this->db->get('ftp_backup');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

	public function CheckLoyaltyPoints($customer_id){
		
		$l = $this->db->select('total_points,loyalty_id')->where('customer_id', $customer_id)->where('status', 1)->get('loyalty_points');
		
		if ($l->num_rows() == 1) {

	   		$total_points =  $l->row('total_points');
			$loyalty_id =  $l->row('loyalty_id');
			$e = $this->db->select('eligibity_point')->where('id', $loyalty_id)->where('status', 1)->get('loyalty_settings');
				if ($e->num_rows() == 1) {	
				  $eligibity_point =  $e->row('eligibity_point');
				  if($total_points >= $eligibity_point){
				  	$data = array(
							'total_points' => $total_points,
							'loyalty_id' => $loyalty_id,
							'eligibity_point' => $eligibity_point,							
						);
				  	 return $data;
				   }
				    return FALSE;
		        }		   	
        }
		return FALSE;	
	}	
	public function getLoyaltyRedemption($loyalty_id){  
		$this->db->select('LR.id,LR.points,LR.amount')
		    ->from('loyalty_redemption LR')
		    ->join('loyalty_settings S', 'S.id = LR.loyalty_id') 			            
		    ->where('S.id',$loyalty_id)			            
		    ->where('S.status',1);    
		    $this->db->order_by('S.id', 'ASC');		    
		    $r = $this->db->get();
		    if ($r->num_rows() > 0) {
                return $r->result();
            }
            return FALSE;
	// print_r($this->db->error());die;
    }
    public function getLoyaltyCardByNO($no) {
        $q = $this->db->get_where('loyalty_cards', array('card_no' => $no), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    public function getLoyaltyCardByID($id) {
        $q = $this->db->get_where('loyalty_cards', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }    
    public function UpdateCustomerFromLoyalty($customer_id,$bill_id,$salesid,$split_id) {

		/*srampos_sales ==customer_id,customer
		srampos_restaurant_table_sessions ==customer_id
		srampos_orders ==customer_id,customer
		srampos_bils ==customer_id,customer*/

        if ($bill_id) {            
            $this->db->select('id,name')
            ->from('companies')                         
             ->where('group_name','customer')
             ->where('id',$customer_id);
            $this->db->limit(1);
            $c = $this->db->get();             			

	            if ($c->num_rows() > 0) { 	           		

					$customer_id =  $c->row('id');
					$customer_name =  $c->row('per_amounts');	

				$customer_array = array(
		            'customer_id' => $customer_id,
		            'customer' => $customer_name,
		        );

				$this->db->update('sales', $customer_array, array('id' => $salesid));
				$this->db->update('bils', $customer_array, array('id' => $bill_id));
		        
		        $this->db->update('orders', $customer_array, array('split_id' =>  $split_id));
		        $this->db->update('restaurant_table_sessions', array('customer_id' => $customer_id), array('split_id' => $split_id));
			}
			
			//}	      
        }
        
        return FALSE;
    }
    public function LoyaltyinserAndUpdate($bill_id,$total,$customer_id,$loyalty_used_points) {
    	$cus = $this->db->select('allow_loyalty')->where('id', $customer_id)->get('companies')->row();
        if ($cus->allow_loyalty && $bill_id) {
            $cur_date = date('Y-m-d');
            $this->db->select('S.id AS loyalty_id,A.start_amount,A.end_amount,A.per_amounts,A.per_points')
            ->from('loyalty_accumalation A')
            ->join('loyalty_settings S', 'S.id = A.loyalty_id') 
            // ->where('"'.$cur_date.'" BETWEEN DATE(S.from_date) and DATE(S.end_date)')            
            // ->where('A.start_amount <=',$total)
            // ->where('A.end_amount >=',$total)    
            ->where('S.status',1);    
            $this->db->order_by('A.id', 'ASC');
            $this->db->limit(1);
            $l = $this->db->get(); 
            
			if($customer_id != $this->pos_settings->default_customer){

	            if ($l->num_rows() > 0) { 	           		

					$loyalty_id =  $l->row('loyalty_id');
					$per_amounts =  $l->row('per_amounts');
					$per_points =  $l->row('per_points');
					$start_amount =  $l->row('start_amount');
					$end_amount =  $l->row('end_amount');
				if($start_amount <= $total && $end_amount >= $total) {

					$count = $total /$per_amounts;
					$total_points  = intval($count) * $per_points;
	            
		            $loyalty_insert = array(
						    'loyalty_id' => $loyalty_id,
						    'bill_id' => $bill_id,
						    'customer_id' => $customer_id,
						    'total_points' => $total_points,				    
						    'created_on' => date('Y-m-d H:i:s'),
						    'status' => 1,
						);
		            $loyalty_points_add = array(
						    'bill_id' => $bill_id,						    
						    'loyalty_id' => $loyalty_id,						    
						    'accumulation_points' => $total_points,
						    'identify' => 1,						    
						);

		             $c = $this->db->select('customer_id,total_points')->where('customer_id', $customer_id)->get('loyalty_points');
		             if ($c->num_rows() > 0) {	             	
	    					$customer =  $c->row('customer_id');
	    					$points =  $c->row('total_points');
	    					$totalpoints = $points + $total_points;
						$this->db->set('total_points', $totalpoints,false);
						$this->db->where('customer_id',$customer);
						$this->db->update('loyalty_points');	

						$this->db->insert('loyalty_points_details', $loyalty_points_add);				
		             }else{	             	
		             	$this->db->insert('loyalty_points_details', $loyalty_points_add);				
		             	$this->db->insert('loyalty_points', $loyalty_insert);	             	
		             }	
		        }else{ 
		        	 $p = $this->db->select('customer_id,total_points')->where('customer_id', $customer_id)->get('loyalty_points');
		             if ($p->num_rows() == 0) {	  
		        	 $loyalty = array(
						    'loyalty_id' => $loyalty_id,
						    'bill_id' => $bill_id,
						    'customer_id' => $customer_id,
						    'total_points' => 0,				    
						    'created_on' => date('Y-m-d H:i:s'),
						    'status' => 1,
						);
		        	 $this->db->insert('loyalty_points', $loyalty);	
		            }	   
		        }     
				if(!empty($loyalty_used_points) && $loyalty_used_points != 0){
					 $redempoints = $this->db->select('customer_id,total_points,loyalty_id')->where('customer_id', $customer_id)->get('loyalty_points');
		             if ($redempoints->num_rows() > 0) {	             	
	    					$customer =  $redempoints->row('customer_id');
	    					$points =  $redempoints->row('total_points');
	    					$loyaltyid =  $redempoints->row('loyalty_id');

	    					/*$loyalty_points_reduce = array(
						    'bill_id' => $bill_id,						    
						    'loyalty_id' => $loyaltyid,						    
						    'points' => $loyalty_used_points,
						    'identify' => 2,						    
							);*/

	    				$totalpoints = $points - $loyalty_used_points;
						$this->db->set('total_points', $totalpoints,false);
						$this->db->where('customer_id',$customer);
						$this->db->update('loyalty_points');

						$this->db->set('redemption_points', $loyalty_used_points,false);
						$this->db->where('bill_id',$bill_id);
						$this->db->update('loyalty_points_details');

						// $this->db->insert('loyalty_points_details', $loyalty_points_reduce);				
		             }
				}
			}
			}	      
        }
        return FALSE;
    }
    public function getCheckLoyaltyAvailable($customer_id){
    		$this->db->select('LP.total_points,LP.expiry_date')
		    ->from('loyalty_points LP')
		    ->join('loyalty_settings S', 'S.id = LP.loyalty_id') 			            
		    ->join('companies C', 'C.id = LP.customer_id') 
		    ->where('LP.total_points >',0)
		    ->where('LP.loyalty_card_no !=', '')   
		    ->where('LP.customer_id', $customer_id)   
		    ->where('S.status',1);  		    		 		    		   
		    $r = $this->db->get();				    
		    if ($r->num_rows() > 0) {		    				 
                return 1;
            }
        return 0;		
	}    
     function add_notification($notification_array){
	$data = $notification_array['insert_array'];
	
	$q = $this->db->get_where('notiy',array('to_user_id'=>$data['to_user_id'],'split_id'=>$data['split_id'],'tag'=>$data['tag']));
	
	if($q->num_rows()>0){
	    
	    $id = $q->row('id');
	    $this->db->set('count','count+1', FALSE);
	    $this->db->where('id',$id);
	    $this->db->update('notiy');
	    return $id;
	}else{
	   $this->db->insert('notiy', $notification_array['insert_array']);
	   return $this->db->insert_id(); 
	}
	
    }
    function BillRequestNotification($reference,$count){
	$Settings = $this->site->get_setting();
	$interval = $Settings->notification_time_interval;
	$no_of_times = $Settings->bill_request_notify_no_of_times;
	$this->load->library('socketemitter');
	$this->load->library('push');
	$now = date('Y-m-d H:i:s');
	$today = date('Y-m-d');
	$this->db->select('*');
	$this->db->where('stop', 0);
	$this->db->where('split_id',$reference);
	$this->db->from('notiy')
	->where('DATE(created_on)', $today);
	//if($count==1){
	//    $this->db->where('is_read', 0);	    
	//}else{
	//    $this->db->having('SUM(is_read) = 0');
	//}
	//$this->db->where('TIMESTAMPDIFF(SECOND,created_on, "'.$now.'")>'.$interval);
	
	//->where('TIMESTAMPDIFF(SECOND,created_on, "'.$now.'")>30');
	$this->db->group_by('split_id,table_id');
	$this->db->order_by('id','ASC');
	$this->db->limit(1);
	//echo $this->db->get_compiled_select();
	$q = $this->db->get();//echo '<pre>';print_R($this->db->last_query());
	$this->db->select()
        ->from('bils')
        ->join('sales','sales.id=bils.sales_id and sales.sales_split_id="'.$reference.'"');
        $billgenerated = $this->db->get();
	$orders = $this->db->get_where('orders',array('split_id'=>$reference))->row();
        
	if($billgenerated->num_rows()==0 && $q->num_rows()>0){
	    $data = $q->result();
	    foreach($data as $k => $row){
		$touser = $row->to_user_id;
		$tableid = $row->table_id;
		$tt = $this->db->get_where('restaurant_tables',array('id'=>$tableid))->row();
		$type   = $tt->sale_type;
		if($count<$no_of_times){
		    $this->db->select('*');
		    $this->db->where('id',$touser);
		    $this->db->from('users');
		    $u = $this->db->get();
		    $users = $u->result();
		}
		else if($count==$no_of_times || $count==$no_of_times+1){
		    $t = $this->db->get_where('restaurant_tables',array('id'=>$tableid))->row();
		    $areaID = $t->area_id;		    
		    $q = $this->db->get_where('restaurant_tables',array('area_id'=>$areaID))->result();
		    $AreaUsers = array();
		    foreach($q as $k => $urow){
			array_push($AreaUsers,$urow->steward_id);
		    }
		    $AreaUsers = array_unique($AreaUsers);
		    $this->db->select('*');
		    $this->db->where_in('id', $AreaUsers);
		    $this->db->where('id !=', $touser);
		    $this->db->from('users');
		    $u = $this->db->get();
		    $users = $u->result();
		}else{
		    $this->db->select('*');
		    $this->db->where_in('group_id', array(5,7,8,10));
		    $this->db->where('id !=', $touser);
		    $this->db->from('users');
		    $u = $this->db->get();
		    $users = $u->result();
		}
		$table_id = $row->table_id;
		$table_name = $this->getTablename($table_id);
		//file_put_contents('notify_values3.txt',json_encode($users),FILE_APPEND);
		foreach($users as $k1 => $user){
			
			$group_id = $user->group_id;
			$warehouse_id = $row->warehouse_id;
                        $notification_title = 'Bill Request';
			$notification_message = 'Customer has requested for bill  '.$reference.' from '.$table_name;
			//$notification_array['from_role'] = $group_id;
			$user_id = $user->id;
			
			$notification_array['insert_array'] = array(
			    'msg' => $notification_message,
			    'type' => $notification_title,
			    'table_id' => $table_id,
			    'user_id' => $row->user_id,
			    'to_user_id' => $user_id,	
			    'role_id' => $group_id,
			    'warehouse_id' => $warehouse_id,
			    'created_on' => date('Y-m-d H:m:s'),
			    'is_read' => 0,
			    'respective_steward'=>$row->respective_steward,
                            'split_id'=>$reference,
                            'tag'=>'bill-request',
			);
			//file_put_contents('notify_values5.txt',json_encode($notification_array),FILE_APPEND);
			$notifyID = $this->add_notification($notification_array);

			
			$device_token = $this->site->deviceDetails($user_id);
			foreach($device_token as $k =>$device){
			    $title = $notification_title;
			    $message = $notification_message;
			    $push_data = $this->push->setPush($title,$message);
			    if($this->isSocketEnabled() && $push_data == true && isset($device->socket_id) && $device->socket_id!=''){
				    $json_data = '';
				    $response_data = '';
				    $json_data = $this->push->getPush();
				    $regId_data = $device->device_token;
				    $socketid = $device->socket_id;
				    //$response_data = $this->firebase->send($regId_data, $json_data);
				    //var_dump($response_data);
				    $request_type = 1;
				    $table_id = $table_id;
				    $this->site->send_billRequestpushNotification($title,$message,$socketid,$reference,$table_id,$notifyID,$request_type,$type);
		    
		    
			    }
		    }
		
		}
	    }
            $count++;
            $this->setTimeout('BillRequestNotification',$reference,$count);

	}
    }
    function PaymentRequestNotification($reference,$count){
	$Settings = $this->site->get_setting();	
	$interval = $Settings->notification_time_interval;
	$no_of_times = $Settings->bill_request_notify_no_of_times;
	$this->load->library('socketemitter');
	$this->load->library('push');
	$now = date('Y-m-d H:i:s');
	$today = date('Y-m-d');
	$this->db->select('*');
	$this->db->where('split_id',$reference);
	$this->db->from('notiy')
	->where('DATE(created_on)', $today);
	//if($count==1){
	//    $this->db->where('is_read', 0);	    
	//}else{
	//    $this->db->having('SUM(is_read) = 0');
	//}
	//$this->db->where('TIMESTAMPDIFF(SECOND,created_on, "'.$now.'")>'.$interval);
	
	//->where('TIMESTAMPDIFF(SECOND,created_on, "'.$now.'")>30');
	$this->db->group_by('split_id,table_id');
	$this->db->order_by('id','ASC');
	$this->db->limit(1);
	//echo $this->db->get_compiled_select();
	$q = $this->db->get();//echo '<pre>';print_R($q->result());exit;
	$this->db->select()
        ->from('bils')
        ->join('sales','sales.id=bils.sales_id and sales.sales_split_id="'.$reference.'"')
        ->join('payments','payments.bill_id=bils.id');
        $billpayment = $this->db->get();
	$orders = $this->db->get_where('orders',array('split_id'=>$reference))->row();
        
	if($billpayment->num_rows()==0 && $q->num_rows()>0){
	    $data = $q->result();
	    foreach($data as $k => $row){
		$touser = $row->to_user_id;
		$tableid = $row->table_id;
		if($count<$no_of_times){
		    $this->db->select('*');
		    $this->db->where('id',$touser);
		    $this->db->from('users');
		    $u = $this->db->get();
		    $users = $u->result();
		}
		else{
                    $this->db->where('DATE(time)',date('Y-m-d'));
                    $this->db->where('group_id',8);
                    $this->db->where('user_id !=', $touser);
                    $cashiers = $this->db->get('user_logins')->result();
                    $cashier_ids = array();
                    if(!empty($cashiers)){
                        foreach($cashiers as $k => $cashier){
                            array_push($cashier_ids,$cashier->user_id);
                        }
			if(!empty($cashier_ids)){
			  $this->db->select('*');
			    $this->db->where_in('user_id', $cashier_ids);
			    $this->db->where('id !=', $touser);
			    $this->db->from('users');
			    $u = $this->db->get();
			    $users = $u->result();  
			}                        
                    }
                    
		}
		$table_id = $row->table_id;
		$table_name = $this->getTablename($table_id);
		//file_put_contents('notify_values3.txt',json_encode($users),FILE_APPEND);
		foreach($users as $k1 => $user){
			
			$group_id = $user->group_id;
			$warehouse_id = $row->warehouse_id;
                        $notification_title = 'Bill Request';
			$notification_message = 'Customer has requested for bill  '.$reference.' from '.$table_name;
			//$notification_array['from_role'] = $group_id;
			$user_id = $user->id;
			
			$notification_array['insert_array'] = array(
			    'msg' => $notification_message,
			    'type' => $notification_title,
			    'table_id' => $table_id,
			    'user_id' => $row->user_id,
			    'to_user_id' => $user_id,	
			    'role_id' => $group_id,
			    'warehouse_id' => $warehouse_id,
			    'created_on' => date('Y-m-d H:m:s'),
			    'is_read' => 0,
			    'respective_steward'=>$row->respective_steward,
                            'split_id'=>$reference,
                            'tag'=>'bill-request',
			);
			//file_put_contents('notify_values5.txt',json_encode($notification_array),FILE_APPEND);
			$notifyID = $this->add_notification($notification_array);

			
			$device_token = $this->site->deviceDetails($user_id);
			foreach($device_token as $k =>$device){
			    $title = $notification_title;
			    $message = $notification_message;
			    $push_data = $this->push->setPush($title,$message);
			    if($this->isSocketEnabled() && $push_data == true && isset($device->socket_id) && $device->socket_id!=''){
				    $json_data = '';
				    $response_data = '';
				    $json_data = $this->push->getPush();
				    $regId_data = $device->device_token;
				    $socketid = $device->socket_id;
				    //$response_data = $this->firebase->send($regId_data, $json_data);
				    //var_dump($response_data);
				    $request_type = 2;
				    $table_id = $table_id;
				    $this->site->send_billRequestpushNotification($title,$message,$socketid,$reference,$table_id,$notifyID,$request_type);
		    
		    
			    }
		    }
		
		}
	    }
            $count++;
            $this->setTimeout('PaymentRequestNotification',$reference,$count);

	}
    }

    function update_notification_status($data){
	$data['status'] = 1;
	$this->db->where(array('split_id'=>$data['split_id'],'tag'=>$data['tag']));
	$this->db->update('notiy',$data);
    }
    function get_product_version_history(){
	$q = $this->db->get('version_update_history');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    function get_productVersion_date($version){
	$q = $this->db->get_where('version_update_history',array('version'=>$version));
        if ($q->num_rows() > 0) {
            return $q->row('time');
        }
        return FALSE;
    }
    function add_version_history($version){
	$data['time'] = date('Y-m-d H:i:s');
	$data['version'] = $version;
	$this->db->insert('version_update_history', $data);	
	return $this->db->insert_id();
    }
    function product_update_ftp(){
	$q = $this->db->get('product_upgrade');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    function update_version($version){
	$data['version'] = $version;
	$this->db->update('settings',$data);
    }
    
	

 //////////////////////////////////// sale stock in / stock out - Start ////////////////////////////
    function getCategoryMappingID($product_id,$category_id,$subcategory_id,$brand_id){
		/* echo $product_id;
		echo $category_id;
		echo $subcategory_id;
		echo $brand_id; */
	$q = $this->db->get_where('category_mapping', array('product_id'=>$product_id,'category_id' => $category_id,'subcategory_id' => $subcategory_id,'brand_id' => $brand_id));
      /*  echo $this->db->last_query();
		die; */
        return @$q->row('id');
		
		
        
    }
    function salestock_out($product_id,$stock_out,$order_item_id,$prod_qty,$cate){
		 $item = $this->getrecipeByID($product_id);
		 //print_r($item);
		$category_id =  $cate['category_id'];
		$subcategory_id = $cate['subcategory_id'];
		$brand_id = $cate['brand_id'];
		 $cm_id = $this->getCategoryMappingID($product_id,$category_id,$subcategory_id,$brand_id);
		
		
		
		if($item->type=="standard" || $item->type=="raw" || $item->type=="production"){
			$updated_stock = $this->updateStockMaster($product_id,$stock_out,$cm_id,$cate);
			$order_item_stock_out[$order_item_id][$item->id]['mapped_items'][$product_id] = $updated_stock[$product_id];
			
		}else if($item->type=="quick_service"){
		   $q = $this->get_recipe_products($product_id);
			if($q->num_rows()>0){
				foreach($q->result() as $k => $row){
					$category_id =  $row->category_id;
					$subcategory_id =  $row->subcategory_id;
					$brand_id = $row->brand_id;
					$cm_id = $row->cm_id;
					if($row->type=='semi_finished'){
						$mapped_item_qty = $prod_qty * $row->quantity;
						$updated_stock = $this->update_stockout_semifinished($row->product_id,$mapped_item_qty,$cm_id);
						$order_item_stock_out[$order_item_id][$item->id]['mapped_items'][$row->product_id] = $updated_stock[$row->product_id];
					}else{
						$mapped_item_qty = $prod_qty * $row->quantity;
						$updated_stock = $this->updateStockMaster($row->product_id,$mapped_item_qty,$cm_id,$cate);
						$order_item_stock_out[$order_item_id][$item->id]['mapped_items'][$row->product_id] = $updated_stock[$row->product_id];
					}
				}
			}
		}
		$order_item_data['stock_out_data'] =  serialize($order_item_stock_out);	
		$this->db->where('id',$order_item_id);
		$this->db->update('order_items',$order_item_data);
    }
    function production_salestock_out($product_id,$stock_out_qty){
		
		$item = $this->getrecipeByID($product_id);
	
		if($item->type=="production"){
		   $q = $this->get_recipe_products($product_id);
		   
			if($q->num_rows()>0){
				foreach($q->result() as $k => $row){
					$category_id =  $row->category_id;
					$subcategory_id =  $row->subcategory_id;
					$brand_id = $row->brand_id;
					$cm_id = $row->cm_id;
					if($row->type=='semi_finished'){
						$mapped_item_qty = $stock_out_qty * $row->quantity;
						$updated_stock = $this->update_stockout_semifinished($row->product_id,$mapped_item_qty,$cm_id);
					}else{
						$mapped_item_qty = $stock_out_qty * $row->quantity;
						$updated_stock = $this->updateStockMaster($row->product_id,$mapped_item_qty,$cm_id,$cate);
					}
				}
			}
		}
		
    }
    function update_stockout_semifinished($product_id,$prod_qty,$cm_id){
		$s = $this->get_recipe_products($product_id);
		$semi_stock_out = $this->updateStockMaster($product_id,$prod_qty,$cm_id,$cate);
		$order_item_stock_out[$product_id] = $semi_stock_out[$product_id];
			if($s->num_rows()>0){
				foreach($s->result() as $kk => $s_row){
					$category_id =  $s_row->category_id;
					$subcategory_id =  $s_row->subcategory_id;
					$brand_id = $s_row->brand_id;
					$cm_id = $s_row->cm_id;
						if($row->type=='semi_finished'){
							$updated_stock = $this->update_stockout_semifinished($s_row->product_id,$prod_qty,$cm_id);
							$order_item_stock_out[$product_id]['mapped_items'][$s_row->product_id] = $updated_stock[$s_row->product_id];
						}else{
						   
							$mapped_item_qty = $prod_qty * $s_row->quantity;
							$updated_stock  = $this->updateStockMaster($s_row->product_id,$mapped_item_qty,$cm_id,$cate);
							$order_item_stock_out[$product_id]['mapped_items'][$s_row->product_id] = $updated_stock[$s_row->product_id];
						}		
				}
			}
		return $order_item_stock_out;
    }
    function get_recipe_products($product_id){
		$this->db->select('recipe_products.product_id,recipe_products.quantity,recipe.type,cm.id as cm_id,cm.category_id,cm.subcategory_id,cm.brand_id');
		$this->db->from('recipe_products');
		$this->db->join('recipe','recipe.id=recipe_products.product_id');
		$this->db->join('category_mapping as cm','cm.id=recipe_products.cm_id','left');
		$this->db->where('recipe_id',$product_id);
		$q = $this->db->get();
		/* echo  $this->db->last_query();
		die; */
		return $q;
    }
    function updateStockMaster($product_id,$stock_out,$cm_id,$cate){
		/* echo $product_id;
		echo "<br />";
		echo $cm_id;
		echo "<br />";  */
		
		/* $category_id = $cate['category_id'];
		$subcategory_id = $cate['subcategory_id'];
		$brand_id = $cate['brand_id']; */
		
		//print_r($cate);

		$this->product_stockOut($product_id,$stock_out,$cate);
		$store_id = $this->data['pos_store'];
		
		$this->db->select();
		$this->db->from('pro_stock_master');
		$this->db->where(array('product_id'=>$product_id,'store_id'=>$store_id,'cm_id'=>$cm_id));
		$this->db->order_by('id');
		$q = $this->db->get(); 
		
		if($q->num_rows()>0){
		 	$stock_out_qty = $stock_out;
			$cnt = count($q->result());
			$order_item = array();
			foreach($q->result() as $k => $row){
				$piece = $this->db->get_where('recipe', array('id' =>$product_id))->row('piece');
				if($stock_out_qty>0){
					if($k+1==$cnt || $row->stock_in!=0){
					 	$available_qty = $row->stock_in;
						if($available_qty<$stock_out_qty){
							$update_qty = $available_qty;
							if($k+1==$cnt){
								$update_qty = abs($available_qty-$stock_out_qty);
								if($available_qty<0){
									$update_qty = $stock_out_qty;
									
								}				
								$stock_out_qty = $available_qty-$stock_out_qty;
							}else{
								$stock_out_qty = $stock_out_qty - $available_qty;
							}
						}else{
							$update_qty = $stock_out_qty;
							$stock_out_qty = 0;
						}
						$stock_piece = $update_qty * $piece;
					//	$update_qty * 5 
						
						//$qu = $this->db->get_where('srampos_pro_stock_master', array('product_id' => $product_id));
						//if ($qu->num_rows() > 0) {
						$query = 'update srampos_pro_stock_master set stock_in = stock_in - '.$update_qty.', stock_in_piece = stock_in_piece - '.$stock_piece.', stock_out = stock_out + '.$update_qty.', stock_out_piece = stock_out_piece + '.$stock_piece.' where id='.$row->id;
						$this->db->query($query);
						/* }else{
							$query ='insert into srampos_pro_stock_master(store_id, product_id, cm_id, category_id, subcategory_id, brand_id, stock_in, stock_out)values('.$store_id.','.$product_id.', 0, '.$cate['category_id'].', '.$cate['subcategory_id'].', '.$cate['brand_id'].', stock_in + '.$update_qty.', stock_in + '.$update_qty.')';
							 $this->db->query($query);
						}  */
						
						/* echo  $this->db->last_query();
						echo 'test1';
						die; */
						
						//$this->product_stockOut($product_id,$update_qty);
						
						$order_item[$product_id]['stock'][$k]['s_id'] = $row->id;
						$order_item[$product_id]['stock'][$k]['qty'] = $update_qty;
					}

				}
			}
			
			return $order_item;
		}
    }

    function product_stockOut($product_id,$stockout,$cate){
	$this->db->set('stock','stock - '.$stockout,false);
	$this->db->where(array('product_id'=>$product_id,'category_id'=>$cate['category_id'],'subcategory_id'=>$cate['subcategory_id'],'brand_id'=>$cate['brand_id']));
	$this->db->update('category_mapping');
    }
    function product_stockIn($product_id,$stockin,$cate){
	$this->db->set('stock','stock + '.$stockin,false);
	$this->db->where(array('product_id'=>$product_id,'category_id'=>$cate['category_id'],'subcategory_id'=>$cate['subcategory_id'],'brand_id'=>$cate['brand_id']));
	$this->db->update('category_mapping');
    }
   function saleStockIn_new($recipe_id,$cancelqty,$order_item_id){  //02-10-2019
	$stock_out_data = $this->getOrder_item_ID($order_item_id);
	$stock_out_data = unserialize($stock_out_data);	
	// $stock_out_data = @$stock_out_data[$order_item_id][$recipe_id];	
	// echo '<pre>';print_R($stock_out_data['stock']);//die;
        foreach($stock_out_data as $row){  
        /*echo '<pre>';print_R($row['s_id']);      	
        echo '<pre>';print_R($row['qty']); */     	
	    // foreach($pro as $p_id => $row){		
		if(($row['s_id'] && $row['s_id'] !='' )){
		/*echo "<pre>";
		print_r($row['s_id']);
		print_r($row['qty']);die;*/
		    $this->item_stockIN_new($row['s_id'],$cancelqty);
		}
		if(isset($row['mapped_items']) && !empty($row['mapped_items'])){
		    $this->mapped_item_stockIN($row['mapped_items']);
		}
	    // }
        }        
    }
  function item_stockIN_new($stock_id,$qty){
  	/*echo "<pre>";
  	print_r($stock_id);
  	print_r($qty);
  	die;*/
	// foreach($stock as $k => $row){
	    $query = 'update srampos_pro_stock_master
			set stock_out = stock_out - '.$qty.'
			where id='.$stock_id;
			// echo $query;die;
	    $this->db->query($query);
	    $q = $this->db->get_where('pro_stock_master',array('id'=>$stock_id))->row();
	    // print_r($this->db->last_query());die;
/*	    $cate['category_id'] = $q->category_id;
	    $cate['subcategory_id'] = $q->subcategory_id;
	    $cate['brand_id'] = $q->brand_id;
	    $this->product_stockIn($q->product_id,$row['qty'],$cate);*/
	// }
    }

    function saleStockIn($recipe_id,$cancelqty,$order_item_id){ 
	$stock_out_data = $this->getOrder_item_ID($order_item_id);
	$stock_out_data = unserialize($stock_out_data);
	$stock_out_data = @$stock_out_data[$order_item_id][$recipe_id];
	//echo '<pre>';print_R($stock_out_data);
        foreach($stock_out_data as $k => $pro){
	    foreach($pro as $p_id => $row){		
		if(isset($row['stock']) && !empty($row['stock'])){
		    $this->item_stockIN($row['stock']);
		}
		if(isset($row['mapped_items']) && !empty($row['mapped_items'])){
		    $this->mapped_item_stockIN($row['mapped_items']);
		}
	    }
        }  
    }

  

    function mapped_item_stockIN($mapped_items){
	foreach($mapped_items as $p_id => $row){
	    if(isset($row['stock']) && !empty($row['stock'])){
		$this->item_stockIN($row['stock']);
	    }
	    if(isset($row['mapped_items']) && !empty($row['mapped_items'])){
		$this->mapped_item_stockIN($row['mapped_items']);
	    }
	}
    }
    function item_stockIN($stock){
	foreach($stock as $k => $row){
	    $query = 'update srampos_pro_stock_master
			set stock_in = stock_in + '.$row['qty'].' ,
			    stock_out = stock_out - '.$row['qty'].'
			where id='.$row['s_id'];
	    $this->db->query($query);
	    $q = $this->db->get_where('pro_stock_master',array('id'=>$row['s_id']))->row();
	    $cate['category_id'] = $q->category_id;
	    $cate['subcategory_id'] = $q->subcategory_id;
	    $cate['brand_id'] = $q->brand_id;
	    $this->product_stockIn($q->product_id,$row['qty'],$cate);
	}
    }
    function getOrder_item_ID($order_item_id){
	$this->db->select('stock_out_data');
	$this->db->from('order_items');
	$this->db->where('id',$order_item_id);
	$q = $this->db->get();
	// print_r($q->row('stock_out_data'));die;
	return $q->row('stock_out_data');
    }
    //////////////////////////////////// sale stock in / stock out - END////////////////////////////
	
	function getEligibitypoints(){
		$current_date = date('Y-m-d');
		$this->db->select('eligibity_point');
		$this->db->from('loyalty_settings');
		$this->db->where('end_date >= ', $current_date);
		$this->db->limit(1);
		$q = $this->db->get();
		if ($q->num_rows() > 0) {
            return $q->row('eligibity_point');
        }
		return 0;
	}
	
	function checkLoyaltycustomer($customer_id){
		$current_date = date('Y-m-d');
		$this->db->select('*');
		$this->db->from('loyalty_points');
		$this->db->where('customer_id', $customer_id);
		$this->db->where('loyalty_card_id', 0);
		$this->db->limit(1);
		$q = $this->db->get();
		if ($q->num_rows() > 0) {
            return true;
        }
		return false;
	}
    
    public function getAllWarehouses_Stores() {
        $q = $this->db->get('warehouses');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    function getAssignedStores($userid){
	$this->db->select('ua.store_id');
	$this->db->from('user_store_access ua');
	$this->db->join('warehouses w','w.id=ua.store_id');
	$this->db->where(array('user_id'=>$userid));
	$q = $this->db->get();
	$storeids = array();
	if($q->num_rows()>0){
	    foreach($q->result() as $k => $row){
		array_push($storeids,$row->store_id);
	    }
	}
	return $storeids;
    }
    function getUserAssignedStores($userid){
	$this->db->select('w.*');
	$this->db->from('user_store_access ua');
	$this->db->join('warehouses w','w.id=ua.store_id');
	$this->db->where(array('user_id'=>$userid));
	$q = $this->db->get();
	$storeids = array();
	if($q->num_rows()>0){	 //echo '<pre>';print_R($q->result());exit;  
	   return $q->result();
	}
	return false;
    }
    function getStoreName($id){
	$q = $this->db->get_where('warehouses',array('id'=>$id));
        if ($q->num_rows() > 0) {           
            return $q->row('name');
        }
        return FALSE;
    }
    
    function stockout_stockMaster_ID($stockMasterId,$stockout){
	$query = 'update '.$this->db->dbprefix('pro_stock_master').'
		set stock_out = stock_out + '.$stockout.'
		where id='.$stockMasterId;
	    $this->db->query($query);
    }
    
    function set_cur_transaction_date(){
	$data['currentdate'] = date('Y-m-d H:i:s');
	$data['transaction_date'] =   date('Y-m-d');
	$q = $this->db->get_where('transaction_date',array('date(currentdate)'=>date('Y-m-d')));
	if($q->num_rows()==0){
	    $this->db->insert('transaction_date',$data);
	}else if($q->row('transaction_date')=='0000-00-00 00:00:00'){
	    $udata['transaction_date'] = $data['transaction_date'];
	    $this->db->where('id',$q->row('id'));
	    $this->db->update('transaction_date',$udata);
	}
    }
    function update_cur_transaction_date($date){
	$data['currentdate'] = date('Y-m-d H:i:s');
	$data['transaction_date'] =   $date;
	$q = $this->db->get_where('transaction_date',array('date(currentdate)'=>date('Y-m-d')));
	if($q->num_rows()==0){
	    $this->db->insert('transaction_date',$data);
	}else{
	    $udata['transaction_date'] = $data['transaction_date'];
	    $this->db->where('id',$q->row('id'));
	    $this->db->update('transaction_date',$udata);
	}
    }
    function addStockMaster($data){
		$q=$this->db->get_where("pro_stock_master",array("unique_id"=>$data["unique_id"]));
		if($q->num_rows()>0){
			$this->db->where("unique_id",$data["unique_id"]);
			$this->db->update("pro_stock_master",$data);
			echo $this->db->last_query();
		}else{
			$this->db->insert('pro_stock_master',$data);
		    $stock_id = $this->db->insert_id();
			$UniqueID = $this->site->generateUniqueTableID($stock_id);
            $this->site->updateUniqueTableId($stock_id,$UniqueID,'pro_stock_master');
				echo $this->db->last_query();
		}
		return true;
    }
	
	function updateStockMaster_product($data){
		$q=$this->db->get_where("pro_stock_master",array("unique_id"=>$data["unique_id"]));
		if($q->num_rows()>0){
			$this->db->where("unique_id",$data["unique_id"]);
			$this->db->update("pro_stock_master",$data);
		}else{
			$this->db->insert('pro_stock_master',$data);
		    $stock_id = $this->db->insert_id();
			$UniqueID = $this->site->generateUniqueTableID($stock_id);
            $this->site->updateUniqueTableId($stock_id,$UniqueID,'pro_stock_master');
		}
		return true;
    }
    
   public function Check_item_Discount_customer($itemid,$dis_id){
   	  	$recipedata = $this->getrecipeByID($itemid);
		$today = date('Y-m-d');
		$curtime  = date('H:i').':00';
		$subcategory_id =$recipedata->subcategory_id; 
		$subcategory_id = (int)$subcategory_id;
		// var_dump($int);
		$q = $this->db
		    ->select('GD.discount_val,GD.discount_type,GD.recipe_id,GD.type')
		    ->from('diccounts_for_customer D')
		    ->join('group_discount GD','GD.cus_discount_id=D.id and GD.recipe_group_id='.$recipedata->category_id)		   
		    ->where('GD.recipe_subgroup_id',$subcategory_id)		    
		    ->where('D.id', $dis_id)	    
		    ->get();
		    /*echo "<pre>";
		    print_r($this->db->last_query());die;*/
		if($q->num_rows()>0) {

		    $res = $q->result();
		    foreach($res as $k => $row){ 
				$recipe_id_days = unserialize($row->recipe_id);						
				if(isset($recipe_id_days[$itemid]) && $row->type=="included") {		    
				    $today = strtolower(date('D'));
				    $days = unserialize($recipe_id_days[$itemid]['days']);		   
				    if(isset($days[$today])){
					   return true;
				    }		    
				    return false;
				}else if(!isset($recipe_id_days[$itemid]) && $row->type=="excluded"){		    
				    return true;
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
    
    function getKitchenID_recipe($recipeID){
	$q = $this->db->get_where('recipe',array('id'=>$recipeID))->row();
	//file_put_contents('jj.txt',json_encode());
	return $q->kitchens_id;
    }
    function getUserStore_singleStore($user_pass_code){
	
	$this->db->select('w.*');
	$this->db->from('user_store_access ua');
	$this->db->join('users u','u.id=ua.user_id');
	$this->db->join('warehouses w','w.id=ua.store_id');
	$this->db->where(array('u.user_number'=>$user_pass_code));
	$q = $this->db->get();
	$storeids = array();
	if($q->num_rows()>0){	 //echo '<pre>';print_R($q->result());exit;  
	   return $q->row('id');
	}
	return false;
    }
    function isTransactionDateSet(){
	$curdate = date('Y-m-d');
	$q = $this->db->get_where('transaction_date',array('date(currentdate)'=>$curdate,'transaction_date !='=>'0000-00-00 00:00:00'));
	if($q->num_rows()>0){	    
	    return true;
	}
	return false;
	
    }
    function CreateSplitID($stewardId){
	if(strlen($stewardId)==1){
	    $stewardId = '00'.$stewardId;
	}else if(strlen($stewardId)==2){
	    $stewardId = '0'.$stewardId;
	}
	$now = date('YmdHis');
	return 'SPILT'.$now.$stewardId;
    }
    function CreateBBQSplitID($stewardId){
	if(strlen($stewardId)==1){
	    $stewardId = '00'.$stewardId;
	}else if(strlen($stewardId)==2){
	    $stewardId = '0'.$stewardId;
	}
	$now = date('YmdHis');
	return 'BBQ'.$now.$stewardId;
    }
    function getPrinters($ids){
	$this->db->select();
	$this->db->from('printers');
	$this->db->where_in('id',$ids);
	$q = $this->db->get();
	if($q->num_rows()>0){
	    return $q->result();
	}
	return false;
    }
    public function amount_to_percentage($value = NULL, $amount = NULL) {
            
            if ($value !='' && $amount !='') {
            	$percentage = ( $value / $amount ) * 100;
            	return $percentage;
            }
        
        return 0;
    }
    function getPurchaseCategories(){
    $this->db->where('type',1)
	->where('(parent_id="null" or parent_id=0)')
	->order_by('id');
        $q = $this->db->get("recipe_categories");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $k => $row) {
		        $data[$k] = $row;
                $data[$k]->sub_category = $this->getrecipeSubCategories($row->id);
            }
            return $data;
        }
        return FALSE;
    }
    function getCategory_mapping($pid){
	$this->db->where('product_id', $pid);
    $q = $this->db->get("category_mapping");
	$category_ids = array();
	$subcategory_ids = array();
	$brand_ids = array();
	$purchase_cost =  array();
	$selling_price =  array();
	$stock = array();
	if($q->num_rows()>0){
	   foreach($q->result() as $k => $row){
	    array_push($category_ids,$row->category_id);
	    array_push($subcategory_ids,$row->subcategory_id);
	    //array_push($brand_ids,$row->brand_id);
	    $brand_ids[$row->category_id.'_'.$row->subcategory_id.'_'.$row->brand_id] = $row->brand_id;
	    $purchase_cost[$row->category_id.'_'.$row->subcategory_id.'_'.$row->brand_id] = $row->purchase_cost;
	    $selling_price[$row->category_id.'_'.$row->subcategory_id.'_'.$row->brand_id] = $row->selling_price;
	    $stock[$row->category_id.'_'.$row->subcategory_id.'_'.$row->brand_id] = $row->stock;
	   }
	   return array('category'=>$category_ids,'subcategory'=>$subcategory_ids,'brand'=>$brand_ids,'purchase_cost'=>$purchase_cost,'selling_price'=>$selling_price,'stock'=>$stock);
	}
	return false;
    }
    function getSaleCategory_mapping($pid){
	$this->db->where('product_id', $pid);
    $q = $this->db->get("category_mapping");
	if($q->num_rows()>0){
	    return $q->row();
	}
	return false;
    }
    function isRoughTenderDone($bill_id){
	$this->db->select('*,SUM(amount) as amount,SUM(amount) as pos_paid');
	$this->db->where('bill_id',$bill_id);
	$this->db->group_by('paid_by');
	$q = $this->db->get('rough_tender_payments');
	if($q->num_rows()>0){
	    return $q->result();
	}
	return false;
    }
    function isRoughTenderDone_saleID($sale_id){//for bbq with dine in
	$s = $this->db->get_where('bils',array('sales_id'=>$sale_id));
	if($s->num_rows()>0){
	    $bill_no = $s->row('bill_number');
	    $b = $this->db->get_where('bils',array('bill_number'=>$bill_no));
	    if($b->num_rows()>0){
		$billids = array();
		foreach($b->result() as $k => $bill){
		    array_push($billids,$bill->id);
		}
		if(!empty($billids)){
		    $this->db->select('*,SUM(amount) as amount,SUM(amount) as pos_paid');
		    $this->db->where_in('bill_id',$billids);
		    $this->db->group_by('paid_by');
		    $q = $this->db->get('rough_tender_payments');
		    if($q->num_rows()>0){
			//echo '<pre>';print_R($q->result());
			return $q->result();
		    }
		}   	    
	    }
	}
	return false;
    }
    function getBillItemDiscount($dis_id){
	$q = $this->db->get_where('discounts',array('id'=>$dis_id))->row();
	return ($q->discount_type=='percentage_discount')?$q->discount.'%':$q->discount;
    }
    function getRandomActiveCashier(){
	$this->db->where('DATE(time)',date('Y-m-d'));
	$this->db->where('group_id',8);
	$this->db->order_by('id', 'RANDOM');
	//echo $this->db->get_compiled_select();
	$q = $this->db->get('user_logins');
	if($q->num_rows()>0){
	    return $q->row('user_id');
	}
	return false;
    }
    public function getrecipeItem_Name($id){
         $this->db->select('id, name')
         ->where('id',$id);
         $q = $this->db->get("recipe");
         if ($q->num_rows() > 0) {
           return $q->row('name');
        }
        return FALSE;
    }
    function getFirstPrint(){
	$this->db->limit(1);
	 $q = $this->db->get('printers');
        if ($q->num_rows() > 0) {            
            return $q->row();
        }
        return FALSE;
    }
    public function getCashierInfo($bill_id)
    {   
        $Cashier = "SELECT U.username AS sales_associate1,PU.username AS cashier1,CONCAT(PU.first_name, ' ', PU.last_name) AS cashier,CONCAT(U.first_name, ' ', U.last_name) AS sales_associate
        FROM " . $this->db->dbprefix('bils') . " B         
		LEFT JOIN " . $this->db->dbprefix('payments') . " PM ON  PM.bill_id = B.id                 
        JOIN " . $this->db->dbprefix('users') . " U ON  B.created_by = U.id        
        LEFT JOIN " . $this->db->dbprefix('users') . " PU ON  PM.created_by = PU.id        
        WHERE B.id = '".$bill_id."' GROUP BY B.id";           
        $q = $this->db->query($Cashier);        
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data = $row;
            }
            return $data;
        }
    }
    public function getTableNumber($bill_id){
        $table_name = "SELECT T.name AS table_name,TY.name AS order_type
                    FROM ".$this->db->dbprefix('bils')." AS P
                    JOIN ". $this->db->dbprefix('sales') ." AS S ON S.id = P.sales_id
                    JOIN ". $this->db->dbprefix('orders') ." AS O ON O.split_id = S.sales_split_id
                    JOIN ". $this->db->dbprefix('sales_type') ." AS TY ON TY.id = O.order_type
                    LEFT JOIN ". $this->db->dbprefix('restaurant_tables') ." AS T ON T.id = O.table_id
                    WHERE P.id='".$bill_id."' ";
        $q = $this->db->query($table_name);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data = $row;
            }
            return $data;
        }
        return FALSE;
    }
    function get_Bill_receipt_data($bill_id){
	$this->db->select();
	$this->db->from('bils');
	$this->db->where('id',$bill_id);
	$b = $this->db->get();
	$bill_data = array();
	$bill_ids = array();
	if ($b->num_rows() > 0) {
	    $bill_data = $b->row();
	    $biller = $this->getCompanyByID($bill_data->biller_id);
	    $bill_data->customer = $this->getCompanyByID($bill_data->customer_id);
	    $bill_data->biller = $biller;
	    $bill_data->inv_footer = $biller->invoice_footer;
	    $bill_data->cashier = $this->getCashierInfo($bill_data->id);
	    $bill_data->tableno = $this->getTableNumber($bill_data->id);
	    //echo '<pre>';print_R($bill_data->tableno);
	    $bill_data->tax_rates = $this->getTaxRateByID($bill_data->tax_id);
	    $bill_data->tax_rate = $bill_data->tax_rates->rate;
	    array_push($bill_ids,$bill_data->id);
	    
	    if(in_array($bill_data->order_type,array(0,1,2,3))){
		$this->db->select();
		$this->db->from('bil_items');
		$this->db->where('bil_id',$bill_id);
		$bi = $this->db->get();
		if ($bi->num_rows() > 0) {
		    $bill_data->bill_items = $bi->result();
		}
	    }
	    
	    
	    if($bill_data->order_type==1 || $bill_data->order_type==4){
		$this->db->select();
		$this->db->from('bils');
		$this->db->where('bill_number',$bill_data->bill_number);
		$this->db->where('order_type',4);
		$bbq = $this->db->get();
		if ($bbq->num_rows() > 0) {
		    $bill_data->bbq = $bbq->row();
		    array_push($bill_ids,$bill_data->bbq->id);
		    if(!empty($bill_data->bbq)){
			$this->db->select();
			$this->db->from('bbq_bil_items');
			$this->db->where('bil_id',$bill_data->bbq->id);
			$bbq_covers = $this->db->get();
			$bill_data->bbq->bbq_covers = $bbq_covers->result();
		    }
		}
	    }
	    ///// payemnt ///
	    $this->db->select();
	    $this->db->from('payments');
	    $this->db->where_in('bill_id',$bill_ids);
	    $p = $this->db->get();
	    if ($p->num_rows() > 0) {
		$bill_data->payments = $p->result();
	    }
	}
	return $bill_data;
    }
    function latest_bill($bill_number){
	$data['bill_number'] = $bill_number;
	$data['date'] = date('Y-m-d H:i:s');
	$q = $this->db->get('latest_bill');
	if ($q->num_rows() == 0) {		
	     $this->db->insert('latest_bill',$data); 
	}else{
	    $this->db->update('latest_bill', array('bill_number' => $bill_number,'date' => date('Y-m-d H:i:s')));
	}
	
    }
	function latest_bill_new($bill_number,$type){
		if($type ==0){
	       $data['bill_number'] = $bill_number;
		}else{
		   $data['dont_print_billnumber'] = $bill_number;
		}
	$data['date'] = date('Y-m-d H:i:s');
	$q = $this->db->get('latest_bill');
	$row=$q->row();
	if ($q->num_rows() == 0) {		
	     $this->db->insert('latest_bill',$data); 
	}else{
		$this->db->where("id",$row->id);
	    $this->db->update('latest_bill',$data);
	}
	
    }
    function send_to_bill_print($billID,$type='invoice'){	
	$bill_data['bill_id']=$billID;
	$bill_data['type']=$type;
	if($type=='reprint'){
	    $this->update_billprint_count($billID);
	}	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, site_url('billing_recipt/send_to_bill_print'));
	curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
	
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode($bill_data));
	curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_VERBOSE, true);
	$result = curl_exec($ch);
	
    curl_close($ch);
  }
  function update_billprint_count($bill_id){
    $this->db->set('print_count','print_count + 1',false);
    $this->db->where('id',$bill_id);
    $this->db->update('bils');
  }
  function getCustomerDiscount_billID($billid){
		$this->db
		->select('P.id bil_id,P.tax_type,P.tax_id,P.total,P.customer_discount_id,P.customer_discount_status,P.total_discount,P.total_tax,P.grand_total,D.*')
		->from('bils P')
		->join('diccounts_for_customer D','D.id=P.customer_discount_id','left')
		->where('P.id',$billid);	
		    $q = $this->db->get();	
		    if ($q->num_rows() > 0) {
		    $result = $q->row();    
		        return $result;
		    }
		    return FALSE;
    }
     public function update_bill_withcustomer_discount($billid,$dis_id,$dis_val){		
    	$update_bil['customer_discount_status'] = 'applied';
	    $update_bil['customer_discount_id'] = $dis_id;
	    $update_bil['discount_val'] = $dis_val;	
	    $myQuery = "SELECT BI.id,BI.recipe_id,sum(BI.subtotal-BI.item_discount-BI.off_discount) AS amount,BI.tax_type,R.category_id,R.subcategory_id,B.tax_id
        FROM " . $this->db->dbprefix('bil_items') . " BI
        JOIN " . $this->db->dbprefix('recipe') . " R ON R.id = BI.recipe_id
        JOIN " . $this->db->dbprefix('bils') . " B ON B.id = BI.bil_id
            WHERE BI.bil_id =".$billid." GROUP BY BI.id";            
        $q = $this->db->query($myQuery);
	    if ($q->num_rows() > 0) {		
		foreach (($q->result_array()) as $row) {			
			$inputDiscount = $this->recipe_customer_discount_calculation($row['recipe_id'],$row['category_id'],$row['subcategory_id'],$row['amount'],$dis_id);	
		    $afterDis_total = $row['amount'] - $inputDiscount;
		    $tax = ($row['tax_type']==0)?$this->inclusive_tax_and_amt($afterDis_total,$row['tax_id']):$this->exclusive_tax_and_amt($afterDis_total,$row['tax_id']);
		    $updateItem['tax'] = $tax['tax'];
		    $updateItem['input_discount'] = $inputDiscount;
		    $cus_dis_val = str_replace('Discount ','',$dis_val);
		    $updateItem['customer_discount_val'] = trim($cus_dis_val,'%').'%';
		    $row['id'];		    
		    $this->db->where('id', $row['id']);
		    $this->db->update('bil_items', $updateItem);	
		}
		$BillQuery = "SELECT B.tax_id,BI.bil_id,SUM(BI.item_discount+BI.off_discount+BI.input_discount) AS total_discount,SUM(BI.subtotal) AS subtotal,BI.tax_type
        FROM " . $this->db->dbprefix('bil_items') . " BI
    	JOIN " . $this->db->dbprefix('bils') . " B ON B.id = BI.bil_id
            WHERE BI.bil_id =".$billid." GROUP BY BI.bil_id";
        $b = $this->db->query($BillQuery);
		if ($b->num_rows() > 0) {
			foreach (($b->result()) as $row) {
				$totalAmt_afterDiscount = $row->subtotal - $row->total_discount;
			$getTax = $this->getTaxRateByID($row->tax_id);
			if($row->tax_type==0){
			    $grandTotal = $totalAmt_afterDiscount/(($getTax->rate/100)+1);
				    $totalTax = $totalAmt_afterDiscount-($totalAmt_afterDiscount/(($getTax->rate/100)+1));
				    $amountPayable = $grandTotal+$totalTax;
				    
				}else{
				    $totalTax = $totalAmt_afterDiscount*($getTax->rate/100);
				    $grandTotal = $totalAmt_afterDiscount+$totalTax;
				    $amountPayable = $grandTotal;
				}
				$update_bil['grand_total'] = $this->sma->formatDecimal($grandTotal);
				$update_bil['total_tax'] = $this->sma->formatDecimal($totalTax);
				$update_bil['total_discount'] = $row->total_discount;
				$update_bil['round_total'] =  $this->sma->formatDecimal($grandTotal);
				$this->db->where('id', $row->bil_id);
				
		        $this->db->update('bils', $update_bil);		        

		       return $this->sma->formatDecimal($amountPayable);;
		    }		
		}	
	    }	
        return false;
    }
    function recipe_customer_discount_calculation($itemid,$groupid,$subgroup_id,$finalAmt,$discountid){
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
	    return $discountAmt = $this->calculateDiscount($discount_value, $finalAmt);
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
    function inclusive_tax_and_amt($total,$taxID){
	$getTax = $this->site->getTaxRateByID($taxID);
	$return['g_total'] = $total/(($getTax->rate/100)+1);
	$return['tax'] = $total-($total/(($getTax->rate/100)+1));
	return $return;
    }
    function auto_modify_bills($bill_data){	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, site_url('auto_modifybills/modify'));
	curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode($bill_data));
	curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_VERBOSE, true);
	$result = curl_exec($ch);
    curl_close($ch);
   }

   function get_tax_splits($tax_id){
		$this->db->select('*');
	    $this->db->where('tax_id',$tax_id);		    
	    $q = $this->db->get('tax_splits');
		    if($q->num_rows()>0){			
			   foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
		 }
   }

    function updateTableStatus($table_id,$status,$user_id = NULL){
    if($status !=0){
	     $time=date('Y-m-d H:i:s');
        }else{
	    $time='0000-00-00 00:00:00';
        }
    	$this->db->update('restaurant_tables', array('current_order_status' => $status,'current_order_user' =>$user_id,'last_order_placed_time'=>$time), array('id' => $table_id));
        return true;
    }
        function update_TableStatus_after_payment($table_id,$status,$salesid = NULL,$split_id){
    	$current_date = $this->site->getTransactionDate();
    	$this->db->select('id');		
		$this->db->where('payment_status', NULL);	
		$this->db->where('date', $current_date);	
		$this->db->where('table_id', $table_id);	
        $q = $this->db->get('orders');
		$this->db->select('id');		
		$this->db->where('sales.payment_status', NULL);	
		$this->db->where('sales.sales_split_id', $split_id);		
        $s = $this->db->get('sales'); 
        $this->db->select('id');		
		$this->db->where('bils.payment_status', NULL);	
		$this->db->where('bils.sales_id', $salesid);		
        $b = $this->db->get('bils'); 
		if($status !=0){
		$time=date('Y-m-d H:i:s');
			}else{
	    $time='0000-00-00 00:00:00';
			}
        if ($q->num_rows() <= 0 && $b->num_rows() <= 0 && $s->num_rows() <= 0 ) {
    	$this->db->update('restaurant_tables', array('current_order_status' => $status,'current_order_user' => $status,'last_order_placed_time'=>$time), array('id' => $table_id));
        	return true;
    	}else if($s->num_rows() <= 0){
        return false;
    	}else{
    		$this->db->update('restaurant_tables', array('current_order_status' => 2,'current_order_user' => $status), array('id' => $table_id));
    	} return false;
    } 


    function update_TableStatus_after_payment_old($table_id,$status,$salesid = NULL){
    	$this->db->select('id');		
		$this->db->where('bils.payment_status', NULL);	
		$this->db->where('bils.sales_id', $salesid);		
        $q = $this->db->get('bils');        
        if ($q->num_rows() <= 0) {
    	$this->db->update('restaurant_tables', array('current_order_status' => $status,'current_order_user' => $status), array('id' => $table_id));
        	return true;
    	} return false;
    }  

     function updatepayment_TableStatus($table_id,$status){
    	$this->db->update('restaurant_tables', array('current_order_status' => $status,'current_order_user' => $status), array('id' => $table_id));
        return true;
    }    


	/*Order List separated from group function*/
	public function GetALlOrdersTableList($table_id = NULL){
		$current_date = date('Y-m-d');
		$current_date = $this->site->getTransactionDate();
		$this->db->select("restaurant_tables.id, restaurant_tables.name, restaurant_tables.max_seats, restaurant_tables.warehouse_id, orders.waiter_id, 'split_order',users.first_name  ,  users.   last_name ", FALSE)		
		->join('orders', 'orders.table_id = restaurant_tables.id  AND orders.order_cancel_status = 0')
		->join("restaurant_areas", "restaurant_areas.id = restaurant_tables.area_id")
		->join('users', 'users.id = orders.created_by ');
		if(!empty($table_id)){
		$this->db->where('restaurant_tables.id', $table_id);
		}		
		if($this->GP['pos-view_allusers_orders']==0){
		    $this->db->where('orders.waiter_id',$this->session->userdata('user_id'));
		}
		$this->db->where('DATE(date)', $current_date);
		$this->db->where("orders.order_type", 1);
		$this->db->where('orders.payment_status', NULL);
		$this->db->where('restaurant_tables.warehouse_id', $this->session->userdata('warehouse_id'));
		$this->db->group_by("restaurant_tables.id");		
		$t = $this->db->get('restaurant_tables');	
		if ($t->num_rows() > 0) {
	        foreach ($t->result() as $row) {
	        	$data[] = $row;
	        }
	        return $data;
	    }
	    return FALSE;
	}
	
	public function AllOrdersTableList_($table_id = NULL,$steward_id = NULL){
		$current_date = date('Y-m-d');
		$current_date = $this->site->getTransactionDate();
		$this->db->select("restaurant_tables.id, restaurant_tables.name, restaurant_tables.max_seats, restaurant_tables.warehouse_id, orders.waiter_id, 'split_order',users.first_name  ,  users.   last_name ", FALSE)		
		->join('orders', 'orders.table_id = restaurant_tables.id  AND orders.order_cancel_status = 0')
		->join("restaurant_areas", "restaurant_areas.id = restaurant_tables.area_id")
		->join('users', 'users.id = orders.created_by ');
		if(!empty($table_id)){
		$this->db->where('restaurant_tables.id', $table_id);
		}		
		
		if(!empty($steward_id)){
		$this->db->where('orders.waiter_id',$steward_id);
		}	
		$this->db->where('DATE(date)', $current_date);
		$this->db->where("orders.order_type", 1);
		$this->db->where('orders.payment_status', NULL);
		$this->db->where('restaurant_tables.warehouse_id', $this->session->userdata('warehouse_id'));
		$this->db->group_by("restaurant_tables.id");		
		$t = $this->db->get('restaurant_tables');	
		if ($t->num_rows() > 0) {
	        foreach ($t->result() as $row) {
	        	$data[] = $row;
	        }
	        return $data;
	    }
	    return FALSE;
	}
	public function GetALlSplitsFromOrders($table_id){
		$current_date = date('Y-m-d');
		$current_date = $this->site->getTransactionDate();		
		$this->db->select("orders.id,restaurant_table_sessions.split_id, companies.name,orders.customer_id, restaurant_table_sessions.table_id, restaurant_table_sessions.session_started,orders.biller, 'order' ", FALSE)
		->join('orders', 'orders.split_id = restaurant_table_sessions.split_id AND orders.order_cancel_status = 0')
		->join("companies", "companies.id = orders.customer_id",'left');
		$this->db->where('orders.table_id', $table_id);
		$this->db->where("orders.order_type", 1);
		$this->db->where('orders.payment_status', NULL);
		$this->db->where('DATE(date)', $current_date);
		$this->db->group_by('restaurant_table_sessions.split_id');
		$t = $this->db->get('restaurant_table_sessions');	
		
		if ($t->num_rows() > 0) {
	        foreach ($t->result() as $row) {
	        	$data[] = $row;
	        }
	        return $data;
	    }
	    return FALSE;
	}

	public function GetALlSplitsOrders($split_id,$table_id){
		$current_date = date('Y-m-d');
		$current_date = $this->site->getTransactionDate();		
		$this->db->select("orders.id, orders.customer_id,orders.order_type, orders.seats_id, orders.order_status, orders.reference_no, orders.date, orders.split_id, orders.table_id, 'items' AS item");
		// ->join('kitchen_orders', 'kitchen_orders.sale_id = orders.id', 'left');
		$this->db->where('orders.split_id', $split_id);
		$this->db->where('orders.table_id', $table_id);
		$this->db->where("orders.order_type", 1);
		$this->db->where('orders.order_cancel_status', 0);
		$this->db->where('DATE(date)', $current_date);
		$t = $this->db->get('orders');		
		if ($t->num_rows() > 0) {
	        foreach ($t->result() as $row) {
	        	$data[] = $row;
	        }
	        return $data;
	    }
	    return FALSE;
	}

	public function GetALlSplitsOrderItems($order_id){
		$current_date = date('Y-m-d');
		$current_date = $this->site->getTransactionDate();		
		$this->db->select("order_items.*, recipe.image, recipe.khmer_name")
        ->join('recipe', 'recipe.id = order_items.recipe_id');
        $t = $this->db->get_where('order_items', array('sale_id' => $order_id));	
		if ($t->num_rows() > 0) {
	        foreach ($t->result() as $row) {
	        	$data[] = $row;
	        }
	        return $data;
	    }
	    return FALSE;
	}



 function updateStockMaster_new($product_id,$variant_id,$stock_out,$cate,$order_item_id=null){
        $stock_out = $stock_out;       	
        $rawstock =$this->getrawstock($product_id,$variant_id,$cate['category_id'],$cate['subcategory_id'],$cate['brand_id']); 
        $stock_overflow =0;
        if(!empty($rawstock)){
        	$order_item = array();
            foreach($rawstock as $row){     
                if($stock_overflow == 0)     {
                    $tobedetect = $stock_out; 
                }else{
                    $tobedetect =$stock_overflow; 
                }
                 $stock = $row->stock_in - $row->stock_out;
                if ($stock > $tobedetect){  
                    $stock_overflow = $stock-$tobedetect;  
                    $stock_qty_taken = $tobedetect-$stock;   
                    if($stock_overflow >= 0){
                       $query = 'update srampos_pro_stock_master set stock_in=stock_in - '.$tobedetect.', stock_out = stock_out + '.$tobedetect.' where id='.$row->id;
                        $this->db->query($query); 

                        $stock_id = $row->id;
	                    $date =date('Y-m-d h:m:s');

	                    $ledger_query ='insert into srampos_pro_stock_ledger(stock_id,store_id, product_id,variant_id, cm_id, category_id, subcategory_id, brand_id, transaction_identify,transaction_type,transaction_qty,date)values('.$stock_id.','.$store_id.','.$product_id.','.$variant_id.', '.$cate['cm_id'].', '.$cate['category_id'].', '.$cate['subcategory_id'].', '.$cate['brand_id'].', "Sales","O",'.$tobedetect.',"'.$date.'")';                       
	                    $this->db->query($ledger_query); 
                         $order_item['stock']['s_id'] = $row->id;
					     $order_item['stock']['qty'] = $tobedetect;
                        
                    }  
                      if($stock_qty_taken <= 0){
                    	break;
                    }

                }else{
                	
                	$stock = $row->stock_in - $row->stock_out;
                	$stock_overflow = $tobedetect -$stock;
                    $out = $stock - $tobedetect;                    
                    $closed='';
                    if($out <= 0){
                        $closed=', stock_status =  "closed"';
                    }
                   
                    $query = 'update srampos_pro_stock_master set stock_in=stock_in - '.$stock.', stock_out = stock_out + '.$stock.'  '.$closed.'  where id='.$row->id;
                    $this->db->query($query); 
                    $stock_id = $row->id;
                    $date =date('Y-m-d h:m:s');

                   /*  $ledger_query ='insert into srampos_pro_stock_ledger(stock_id,store_id, product_id,variant_id, cm_id, category_id, subcategory_id, brand_id, transaction_identify,transaction_type,transaction_qty,date)values('.$stock_id.','.$store_id.','.$product_id.','.$variant_id.', '.$cate['cm_id'].', '.$cate['category_id'].', '.$cate['subcategory_id'].', '.$cate['brand_id'].', "Sales","O",'.$stock.',"'.$date.'")';                       
                    $this->db->query($ledger_query);  */

                    $order_item['stock']['s_id'] = $row->id;
					$order_item['stock']['qty'] = $tobedetect;
                    if($stock_overflow <= 0){
                    	break;
                    }
                    // 
                }
            }
			if(!empty($order_item_id)){
            $order_item_data['stock_out_data'] =  serialize($order_item);	
			$this->db->where('id',$order_item_id);
			$this->db->update('order_items',$order_item_data);
			}
            // return $order_item;
        }else{
			$rawstock =$this->getrawstock_empty($product_id,$variant_id,$cate['category_id'],$cate['subcategory_id'],$cate['brand_id']); 
		
			 foreach($rawstock as $row){
				 $query = 'update srampos_pro_stock_master set stock_in=stock_in - '.$stock_out.', stock_out = stock_out + '.$stock_out.'  '.$closed.'  where id='.$row->id;
                    $this->db->query($query); 
                    $stock_id = $row->id;
					break;
			 }
			
		}
    }

public function getrawstock($product_id,$variant_id,$category_id,$subcategory_id,$brand_id){
       $this->db->select('pro_stock_master.*');
        $this->db->from('pro_stock_master');
        if($category_id !=''){
            $this->db->where('category_id',$category_id);
        }
        if($subcategory_id !=''){
            $this->db->where('subcategory_id',$subcategory_id);
        }
        if($brand_id !=''){
            $this->db->where('brand_id',$brand_id);
        }
		if($variant_id !='' && $variant_id !=0){
        $this->db->where('variant_id',$variant_id);   
		}		
        $this->db->where('product_id',$product_id);
        $this->db->where_not_in('stock_status','closed');
		$this->db->where('store_id',$this->store_id);
		$this->db->where('stock_in>0');
        $this->db->group_by('id');
        $this->db->order_by('id', 'asc');
		
        $q = $this->db->get(); 
      //   print_r($this->db->last_query());die; 
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return array();

     /*   if ($q->num_rows() > 0) {
            return $q->result_array();
        }
        return FALSE;*/
}
public function getrawstock_empty($product_id,$variant_id,$category_id,$subcategory_id,$brand_id){
       $this->db->select('pro_stock_master.*');
        $this->db->from('pro_stock_master');
        if($category_id !=''){
            $this->db->where('category_id',$category_id);
        }
        if($subcategory_id !=''){
            $this->db->where('subcategory_id',$subcategory_id);
        }
        if($brand_id !=''){
            $this->db->where('brand_id',$brand_id);
        }
		if($variant_id !='' && $variant_id !=0){
        $this->db->where('variant_id',$variant_id);   
		}	
        //$this->db->where('variant_id',$variant_id);        
        $this->db->where('product_id',$product_id);
      //  $this->db->where_not_in('stock_status','closed');
		$this->db->where('store_id',$this->store_id);
		$this->db->limit(1);
        $this->db->group_by('id');
        $this->db->order_by('id', 'asc');
        $q = $this->db->get(); 
        // print_r($this->db->last_query());die; 
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return array();

     /*   if ($q->num_rows() > 0) {
            return $q->result_array();
        }
        return FALSE;*/
}
    function updateStockMaster_new_hide31082019($product_id,$stock_out,$cm_id,$cate){
    	$piece = $this->db->get_where('recipe', array('id' =>$product_id))->row('piece');
		$store_id = $this->data['pos_store'];
		$q = $this->db->get_where('srampos_pro_stock_master', array('product_id' => $product_id));
		if ($q->num_rows() > 0) {
			$id = $q->row('id');							
			$query = 'update srampos_pro_stock_master set  stock_out = stock_out + '.$stock_out.', stock_out_piece = stock_out_piece + '.$stock_out * $piece.' where id='.$id;
			$this->db->query($query);	
			}else{						
			$query ='insert into srampos_pro_stock_master(store_id, product_id, cm_id, category_id, subcategory_id, brand_id, stock_out)values('.$store_id.','.$product_id.', 0, '.$cate['category_id'].', '.$cate['subcategory_id'].', '.$cate['brand_id'].', stock_out + '.$stock_out.')';
			$this->db->query($query);
		}  			
			return $order_item;
    }

	public function unit_of_measurement($product_id,$select_unit = null, $purchase = false, $empty_opt = false)
    {
        $opts = '';
        if ($empty_opt) {
            $opts .= '<option value="">'.lang('select').'</option>';
        }
		    $recipe=$this->site->getrecipeByID($product_id);
            $Unit  = $this->site->getUnitByID($recipe->unit);
            $opts .= '<option value="'.$Unit->id.'"'.($select_unit && $select_unit == $Unit->id ? ' selected="selected"' : '').'>'.lang($Unit->name).'</option>';
        
        return $opts;
    }


    function wraprecipe_name_qty($r_name,$r_qty,$newline){
    if($this->pos_settings->kot_font_size == 0)	{
    	$wrapped = wordwrap($r_name,37,"\n");
    }elseif($this->pos_settings->kot_font_size == 1){
    	$wrapped = wordwrap($r_name,50,"\n");
    }else{
		$wrapped = wordwrap($r_name,25,"\n");    	
    }
	
	$lines = explode("\n", $wrapped);
	$wrap_cnt = count($lines)-1; 
	if($this->pos_settings->kot_font_size == 0)	{
		$lines[$wrap_cnt] = sprintf('%-37.37s %8.0f',$lines[$wrap_cnt], $r_qty); 
	}elseif($this->pos_settings->kot_font_size == 1){ 
		$lines[$wrap_cnt] = sprintf('%-20.20s %2.0f', $lines[$wrap_cnt], $r_qty); 
	}else{
		$lines[$wrap_cnt] = sprintf('%-19.19s %2.0f',$lines[$wrap_cnt], $r_qty); 
	}
	$items = implode("\n",$lines);
	if($newline) {
	 $items = $items."\n";
	}
	return $items;
    }
public function checkTableActiveStatus($table_id){
		$current_date = date('Y-m-d');
		$current_date = $this->getTransactionDate();
		$items['a'] = $this->db->select('COUNT(id) AS count_null', false)
		->from('orders')
		->where('orders.table_id', $table_id)
		//->where('DATE(date)', $current_date)
		->get()->result();
		$items['b'] = $this->db->select('COUNT(id) AS count_not_null', false)
		->from('orders')
		->where('orders.table_id', $table_id)
		//->where('DATE(date)', $current_date)
		->where('orders.payment_status', 'Paid')
		->get()->result();
		$items = array_merge($items['a'], $items['b']);
		if($items[0]->count_null == $items[1]->count_not_null){
			return 1;
		}
        return 0;
	}
	function checkTableCancelStatus($table_id){
		$q=$this->db->select('id', false)
		->from('orders')
		->where('orders.table_id', $table_id)
		->where('orders.payment_status', 'Cancelled')
		->get();
		//echo $this->db->last_query();
		 if ($q->num_rows() > 0) {
			 return 1;
		 }else{
			 return 0;
		 }
	}
	function checkactiveTablestatus($table_id){
		$q=$this->db->select('id', false)
		->from('restaurant_tables`')
		->where_not_in('current_order_status',0)
		->where('id', $table_id)
		->get();
	//	echo $this->db->last_query();
		 if ($q->num_rows() > 0) {
			 return 1;
		 }else{
			 return 0;
		 }
	}
/*sivan 22-07-2019*/
	/*Order List separated from group function*/

//    function addpermissioncolum($data){//echo '<pre>';print_r($data);
//	foreach($data as $k => $col){ //echo $k;
//	  
//		$query = 'ALTER TABLE `srampos_permissions` ADD `'.$k.'` TINYINT NOT NULL';
//		$this->db->query($query);
//	    
//	}
//    }
function update_TableStatus_after_payment_for_consolidate($table_id,$status,$salesid = NULL,$split_id){
    	$current_date = $this->site->getTransactionDate();
    	$this->db->select('id,created_on');		
		$this->db->where('payment_status', NULL);	
		$this->db->where('date', $current_date);	
		$this->db->where('table_id', $table_id);	
		$this->db->order_by("created_on","ASC");
		$this->db->limit(1);
		// $this->db->where('bils.sales_id', $salesid);		
        $q = $this->db->get('orders');
			$orders=$q->row();
/*        echo "<pre>";
        print_r($this->db->last_query());
*/

		$this->db->select('id');		
		$this->db->where('sales.payment_status', NULL);	
		//$this->db->where('sales.sales_split_id', $split_id);	
		$this->db->where('sales.sales_table_id', $table_id);		
        $s = $this->db->get('sales'); 
		// print_r($this->db->last_query());die;
        $this->db->select('id');		
		$this->db->where('bils.payment_status', NULL);	
		$this->db->where('bils.sales_id', $salesid);		
        $b = $this->db->get('bils'); 

        // if ($q->num_rows() <= 0) {
	    $last_order_time=!empty($orders->created_on)? $orders->created_on:NULL;
        if ($s->num_rows() > 0 ) {
    	$this->db->update('restaurant_tables', array('current_order_status' => 4,'current_order_user' => $status), array('id' => $table_id));
        	return true;
    	}elseif($b->num_rows()>0){
			$this->db->update('restaurant_tables', array('current_order_status' => 4), array('id' => $table_id));
		}elseif($q->num_rows() > 0 ){
			$this->db->update('restaurant_tables', array('current_order_status' => 1,'last_order_placed_time'=>$last_order_time), array('id' => $table_id));
		}else{
    		$this->db->update('restaurant_tables', array('current_order_status' => 0), array('id' => $table_id));
    	} return false;
    } 
     function updatepayment_TableStatus_consolidate($table_id,$status){
    	$this->db->update('restaurant_tables', array('current_order_status' => $status,'current_order_user' => $status), array('id' => $table_id));
        return true;
    }   

 public function getTablearea($table_id){
		$this->db->select('*')->where('id', $table_id);
		$q = $this->db->get('restaurant_tables');
        if ($q->num_rows() > 0) {
            return $q->row('area_id');
        }
		return TRUE;
	}
	function get_archival_tables(){
		$this->db->select("*");
		$this->db->where("active",1);
		$q=$this->db->get("archival_tables");
		if($q->num_rows()>0){
			foreach($q->result() as $row){
				$data[]=$row;
			}
			return $data;
		}
		return false;
	}
		function get_achival(){
      $q = $this->db->get_where('last_archival_process',array('id'=>1));
      if($q->num_rows()>0){
		return $q->row();
      }
      return false;
    }
	 function start_achival_time(){
	$data['starttime'] = date('Y-m-d H:i:s');
	$data['status'] = 'Processing';
	if(!empty($this->get_achival())){
	    $this->db->where(array('id'=>1));
	    $this->db->update('last_archival_process',$data);
	}else{
	   $this->db->insert('last_archival_process',$data);
	}
    }
    function end_achival_time(){
	$data['endtime'] = date('Y-m-d H:i:s');
	$data['status'] = 'completed';
	if(!empty($this->get_achival())){
	    $this->db->where(array('id'=>1));
	    $this->db->update('last_archival_process',$data);
	}else{
		$this->db->insert('last_archival_process',$data);
	}
    }
	function  get_new_archival_data($fromtable,$totable){
		$where='id not in (SELECT '.$totable.'.id FROM '.$totable.')';
		$this->db->select("*");
		$this->db->where($where);
		$q=$this->db->get($fromtable);
		if($q->num_rows()>0){
			foreach($q->result() as $row){
				$data[]=$row;
			}
			return $data;
		}
		return false;
	
	}
	function  insert_archival_data($data,$totable){
		if($this->db->insert_batch($totable, $data)){
		return true;
		}else{
			return false;
		}
	}
	function check_archival_table($tablename){
		$tablename=$this->db->dbprefix($tablename);
		$this->db->select("*");
		$this->db->where("archival_from_table",$tablename);
		$this->db->where("active",1);
		$q=$this->db->get("archival_tables");
		if($q->num_rows()>0){
			return true;
		}
		return false;
		
	}
	function select_record($tablename,$where){
		$this->db->where($where);
		$q=$this->db->get($tablename);
		if($q->num_rows()>0){
			foreach($q->result() as $row){
				$data[]=$row->id;
			}
			return $data; 
		}
		return false;
	}
	function record_delete($tablename,$fields,$id){
		$this->db->where_in($fields,$id);
		$this->db->delete($tablename);
		if($this->db->affected_rows()>0){
			return $this->db->affected_rows();
		}else{
			return false;
		}
	}
	public function getRecipeDetails($recipe_id) {
	   $this->db->select('*');
    	$this->db->where('id', $recipe_id);
        $q = $this->db->get('recipe');
        if ($q->num_rows() > 0) {
           return $q->row();
        }
        return FALSE;
    }
	public function Get_SplitsFromOrders($table_id,$split_id){
		$current_date = date('Y-m-d');
		$current_date = $this->site->getTransactionDate();		
		$this->db->select("orders.id,restaurant_table_sessions.split_id, companies.name,orders.customer_id, restaurant_table_sessions.table_id, restaurant_table_sessions.session_started, 'order' ", FALSE)
		->join('orders', 'orders.split_id = restaurant_table_sessions.split_id AND orders.order_cancel_status = 0')
		->join("companies", "companies.id = orders.customer_id",'left');
		$this->db->where('orders.table_id', $table_id);
		$this->db->where("orders.order_type", 1);
		$this->db->where('orders.payment_status', NULL);
		$this->db->where('DATE(date)', $current_date);
		$this->db->group_by('restaurant_table_sessions.split_id');
		$t = $this->db->get('restaurant_table_sessions');			
		if ($t->num_rows() > 0) {
	        foreach ($t->result() as $row) {
	        	$data[] = $row;
	        }
	        return $data;
	    }
	    return FALSE;
	}
	
	function getCounter($ip){
		$this->db->select('id, till_name');
		$this->db->where('system_ip', $ip);
		$q = $this->db->get('tills');
		if ($q->num_rows() > 0) {	        
	        return $q->row();
	    }
	    return FALSE;
	}
	
	function exitShift($till_id){
		$this->db->select('*');
		$this->db->from('shifts');
		$this->db->where('till_id',$till_id);
		$this->db->where('settled',0);
		$this->db->where('continued_shift',0);
		$this->db->order_by('id','desc');
		$q = $this->db->get();
		if($q->num_rows()>0){
			return $q->row();
		}
		return FALSE;	
	}
	
	function dontcontinueShift($till_id){
		$this->db->select('*');
		$this->db->from('shifts');
		$this->db->where('till_id',$till_id);
		$this->db->where('settled',0);
		$this->db->where('continued_shift',2);
		$this->db->order_by('id','desc');
		$q = $this->db->get();
		if($q->num_rows()>0){
			return $q->row();
		}
		return FALSE;	
	}
	
	function continueShift($till_id){
		$this->db->select('*');
		$this->db->from('shifts');
		$this->db->where('till_id',$till_id);
		$this->db->where('settled',0);
		$this->db->where('continued_shift',1);
		$this->db->order_by('id','desc');
		$q = $this->db->get();
		if($q->num_rows()>0){
			return $q->row();
		}
		return FALSE;	
	}
	
	function getpendingshift($warehouse_id, $till_id,$user_id){
	 	$this->db->select('s.*, c.first_name as created_name, u.first_name as assigned_name, t.till_name as till_name');
	    $this->db->from('shifts s');
		$this->db->join('tills t', 't.id = s.till_id');
		$this->db->join('users c', 'c.id = s.created_by', 'left');
		$this->db->join('users u', 'u.id = s.user_id', 'left');
	    $this->db->where('s.till_id',$till_id);
	    //if($user_id){
			//$this->db->where('user_id',$user_id);
		//}
	    $this->db->where('s.settled',0);
	    $this->db->where('s.warehouse_id',$warehouse_id);
	    $this->db->order_by('s.id','desc');
	    $q = $this->db->get();
		//print_r($this->db->last_query());
	    if($q->num_rows()>0){
			$end_time =  $q->row('shift_end_time');
			if($end_time=='0000-00-00 00:00:00'){
				$data = $q->row();
				$res = $this->getShiftBills($data->id, $till_id, $warehouse_id);
				
				if(!empty($res)){
					$data->no_of_bills = $res['no_of_bills'];
					$data->no_of_items = $res['no_of_items'];
					$data->bill_total = $res['bill_total'];
				}else{
					$data->no_of_bills = 0;
					$data->no_of_items = 0;
					$data->bill_total = 0;
				}
				
				
				$data->shift_name = $this->getShiftName($data->shiftmaster_id);
				return $data;
			}
	    }
    }
	
	function getShiftBills($shift_id, $till_id, $warehouse_id){
		$res = array();
		$s = $this->db->select('s.id, s.grand_total')->from('sales s')->where('s.shift_id', $shift_id)->where('s.till_id', $till_id)->get();
		if ($s->num_rows() > 0) {
			foreach (($s->result()) as $sow) {
				$sale_amount[] = $sow->grand_total;
			}
		}
		$res['bill_total'] = array_sum($sale_amount);
		
		$b = $this->db->select('s.id, b.id as bill_id, bi.id as bill_item_id')->from('sales s')->join('bils b', 'b.sales_id = s.id', 'left')->join('bil_items bi', 'bi.bil_id = b.id', 'left')->where('s.shift_id', $shift_id)->where('s.till_id', $till_id)->get();
		if ($s->num_rows() > 0) {
			foreach (($b->result()) as $bow) {
				$bill_id[] = $bow->bill_id;
				$bill_item_id[] = $bow->bill_item_id;
			}
		}
		
		$res['no_of_bills'] = count(array_unique($bill_id));
		$res['no_of_items'] = count($bill_item_id);
		if(!empty($res)){
			return $res;	
		}
		
		return false;	
	}
	
	function getShiftName($shift_id){
		$this->db->where('id',$shift_id);
		$q = $this->db->get('shiftmaster');
		return $q->row('name');
    }
	
	
	function getShiftmaster(){
		$now = date('H:i:s');
		$now_date = strtotime(date('Y-m-d H:i:s'));
		$hr = date('H');
		$this->db->where('status', 1);
		$q = $this->db->get('shiftmaster');
		
		if($q->num_rows()>0){
			foreach($q->result() as $k => $row){
			
				if(strtotime($row->from_time)<strtotime($row->to_time)){
					if(strtotime($row->from_time)<=strtotime($now) && strtotime($now)<=strtotime($row->to_time)){
						return $row;
					}
				}else{
				  
					$from_hr = date('H',strtotime($row->from_time));
					if($from_hr>$hr){
						$from_datetime = strtotime(date('Y-m-d ',strtotime($date .' -1 day')).$row->from_time);
						$to_datetime = strtotime(date('Y-m-d ').$row->to_time);
						if($now_date>=$from_datetime && $now_date<=$to_datetime ){
							return $row;
						}
					}else if($from_hr<=$hr){
						$from_datetime = strtotime(date('Y-m-d ').$row->from_time);
						$to_datetime = strtotime(date('Y-m-d ',strtotime($date .' +1 day')).$row->to_time);
						if($now_date>=$from_datetime && $now_date<=$to_datetime ){
							return $row;
						}
					}
				}
			}
		}
	    return FALSE;
	}
	
	/*function getShiftmaster(){
		$now = date('H:i:s');
		$now_date = strtotime(date('Y-m-d H:i:s'));
		$hr = date('H');
		$this->db->select('*');
		$this->db->where('status', 1);
		$this->db->where("TIME(from_time) >=", $now);
		$this->db->or_where("TIME(to_time) <=", $now);
	
		$q = $this->db->get('shiftmaster', 1);
		if($q->num_rows()>0){
			
	    	return $q->row();	
	    }
		return FALSE;	
	}*/
	
	
	function getTils(){
		//$this->db->where('status',1);
		$q = $this->db->get('tills');
		return $q->result();
    }
	
	function getShiftUsers(){
		$this->db->where('active',1);
		$q = $this->db->get('users');
		return $q->result();
    }
	function get_till($till_id){
		$this->db->select("*");
		$this->db->where("id",$till_id);
		$q=$this->db->get("tills");
		if($q->num_rows()>0){
			return $q->row();
		}
		return false;
	}
	public function getUser_details($id = NULL) {
        if (!$id) {
            $id = $this->session->userdata('user_id');
        }
        
		$this->db->select("users.*,warehouses.name warehouses");
		$this->db->join("warehouses","warehouses.id=users.warehouse_id","left");
		$this->db->where("users.id",$id);
		$q=$this->db->get("users");
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	
	function lastCounter($till_id){
		$this->db->select('s.CUR_USD, s.CUR_KHR, date_format(s.shift_start_time,"%d/%m/%Y") as shift_start, ss.cash_USD_received, ss.cash_KHR_received');
		$this->db->from('shifts s');
		$this->db->join('shifts_settlement ss', 'ss.shift_id = s.id', 'left');
		$this->db->where('s.till_id', $till_id);
		$this->db->where('s.settled', 1);
		$this->db->order_by('s.id', 'desc');
		$q = $this->db->get();
		if($q->num_rows()>0){
			return $q->row();
		}
		return false;
	}
	function avl_order($tableid){
		$this->db->select("split_id,COUNT(srampos_order_items.id)count,customer_id");
		$this->db->join("order_items","order_items.sale_id=orders.id");
		$this->db->where("table_id",$tableid);
		$this->db->where('payment_status', NULL);	
		$this->db->group_by('orders.split_id');	
		$q=$this->db->get("orders");
		if($q->num_rows()>1){
			return $q->result();
		}else{
			return $q->row();
		}
		return false;
	}
	/* function avl_bill($tableid){
		$this->db->select("split_id");
		$this->db->where("table_id",$tableid);
		$this->db->where('payment_status', 'Bill GENERATED');	
		$q=$this->db->get("orders");
		if($q->num_rows()>1){
			return $q->result();
		}else{
			return $q->row();
		}
		return false;
	} */
	function avl_bill($tableid){
		$this->db->select("sales_split_id");
		$this->db->join("bils","bils.sales_id=sales.id","left");
		$this->db->where("sales_table_id",$tableid);
		$this->db->where('bils.payment_status', NULL);	
		$q=$this->db->get("sales");
		if($q->num_rows()>1){
			return $q->result();
		}else{
			return $q->row();
		}
		return false;
	}
	
	function get_order_details($split_id){
	$this->db->select("split_id,reference_no,customer,name");
	$this->db->join("restaurant_tables","restaurant_tables.id=orders.table_id","left");
	$this->db->where("split_id",$split_id);
	$q=$this->db->get("orders");
	if($q->num_rows()>0){
		return  $q->row();
	}
	return false;
	}
	function get_sales_details($split_id){
	$this->db->select("sales_split_id,reference_no,customer,name");
	$this->db->join("restaurant_tables","restaurant_tables.id=sales.sales_table_id","left");
	$this->db->where("sales_split_id",$split_id);
	$q=$this->db->get("sales");
	if($q->num_rows()>0){
		return  $q->row();
	}
	return false;
	}
		public function getAllTakeawayorder(){
		$current_date = date('Y-m-d');
		$current_date = $this->site->getTransactionDate();
		$this->db->select("orders.split_id, orders.customer_id,orders.customer,orders.created_on,  'order'");
		$this->db->where("orders.order_type", 2);
		$this->db->where("orders.payment_status", NULL);
		$this->db->where('orders.order_cancel_status', 0);
		$this->db->where('DATE(date)', $current_date);
		$this->db->where('orders.warehouse_id', $this->session->userdata('warehouse_id'));
		$this->db->group_by('orders.split_id');
		$t = $this->db->get("orders");
		if ($t->num_rows() > 0) {
			foreach($t->result() as $row){
				$this->db->select("id ");
				$checkbils = $this->db->get_where('sales', array('sales_split_id' => $row->split_id));
				if ($checkbils->num_rows() == 0) {
					 $this->db->select("orders.id, orders.customer_id,orders.customer,orders.created_on, kitchen_orders.id AS kitchen, kitchen_orders.status,orders.order_type, orders.seats_id, orders.order_status, orders.reference_no, orders.date, orders.split_id, orders.table_id, 'items' AS item")
					 
					->join('kitchen_orders', 'kitchen_orders.sale_id = orders.id', 'left')
					->where('orders.split_id', $row->split_id)
					->where('DATE(date)', $current_date);
					
					$o = $this->db->get('orders');
					$split[$row->id][] = $row;
					if ($o->num_rows() > 0) {
						
						foreach($o->result() as $oow){
							
							$this->db->select("order_items.*, recipe.image, recipe.khmer_name")
							->join('recipe', 'recipe.id = order_items.recipe_id');
							$i = $this->db->get_where('order_items', array('sale_id' => $oow->id));
							
							if($i->num_rows() > 0){
								
								foreach($i->result() as $item){
									$item_list[$oow->id][] = $item;
								}
								
							}
							
							$oow->item = $item_list[$oow->id];
							
							$order[$row->split_id][] = $oow;
						}
					}
					
				}
					$row->order = $order[$row->split_id];					
					
				
				$data[] = $row;
			}
			
			return $data;	
		}
		return FALSE;
	}
		public function getAllDoordeliveryorder(){
		$current_date = date('Y-m-d');
		$current_date = $this->site->getTransactionDate();
		$this->db->select("orders.split_id , orders.customer_id, orders.customer,orders.created_on,  'order'");
		$this->db->where("orders.order_type", 3);
		$this->db->where("orders.payment_status", NULL);
		$this->db->where('orders.order_cancel_status', 0);
		$this->db->where('DATE(date)', $current_date);
		$this->db->where('orders.warehouse_id', $this->session->userdata('warehouse_id'));
		$this->db->group_by('orders.split_id');
		$t = $this->db->get("orders");
		if ($t->num_rows() > 0) {
			foreach($t->result() as $row){
				$this->db->select("id ");
				$checkbils = $this->db->get_where('sales', array('sales_split_id' => $row->split_id));
				if ($checkbils->num_rows() == 0) {
						
					 $this->db->select("orders.id, orders.customer_id,,orders.created_on,orders.customer, kitchen_orders.id AS kitchen, kitchen_orders.status,orders.order_type, orders.seats_id, orders.order_status, orders.reference_no, orders.date, orders.split_id, orders.table_id, 'items' AS item")
					->join('kitchen_orders', 'kitchen_orders.sale_id = orders.id', 'left');
					
					$o = $this->db->get_where('orders', array('orders.split_id' => $row->split_id));
				
					$split[$row->id][] = $row;
					if ($o->num_rows() > 0) {
						foreach($o->result() as $oow){
							$this->db->select("order_items.*, recipe.image, recipe.khmer_name")
							->join('recipe', 'recipe.id = order_items.recipe_id');
							$i = $this->db->get_where('order_items', array('sale_id' => $oow->id));
							if($i->num_rows() > 0){
								foreach($i->result() as $item){
									$item_list[$oow->id][] = $item;
								}
							}
							$oow->item = $item_list[$oow->id];
							$order[$row->split_id][] = $oow;
						}
					}
				}
					$row->order = $order[$row->split_id];					
				$data[] = $row;
			}
			return $data;	
		}
		return FALSE;
	}
	     function update_TableStatus_after_cancel_split($split_id,$status =NULL){

    	$current_date = $this->site->getTransactionDate();
		$table=$this->db->get_where("orders",array("split_id"=>$split_id))->row();
		$table_id=$table->table_id;
    	$this->db->select('id,created_on');		
		$this->db->where('payment_status', NULL);	
		$this->db->where('date', $current_date);	
		$this->db->where('table_id', $table->table_id);	
		$this->db->order_by("id", "asc");
		$this->db->limit("1");
        $q = $this->db->get('orders');
		
		$this->db->select('id');		
		$this->db->where('sales.payment_status', NULL);	
		$this->db->where('sales.sales_split_id', $split_id);		
        $s = $this->db->get('sales'); 

        $this->db->select('id');		
		$this->db->where('bils.payment_status', NULL);	
		$this->db->where('bils.sales_id', $salesid);		
        $b = $this->db->get('bils'); 
// print_r($this->db->last_query());die;
        // if ($q->num_rows() <= 0) {
			$order_row=$q->row();
   if(!empty($order_row)){
	   $time=$order_row->created_on;
   }else{
	    $time='0000-00-00 00:00:00';
   }
        if ($q->num_rows() <= 0 && $b->num_rows() <= 0 && $s->num_rows() <= 0 ) {
    	$this->db->update('restaurant_tables', array('current_order_status' => $status,'current_order_user' => $status,'last_order_placed_time'=>$time), array('id' => $table_id));
        	return true;
    	}else if($s->num_rows() <= 0){
			$this->db->update('restaurant_tables', array('current_order_status' =>$status,'current_order_user' => $status), array('id' => $table_id));
        return false;
    	}else{
    		$this->db->update('restaurant_tables', array('current_order_status' => 2,'current_order_user' => $status), array('id' => $table_id));
    	} return false;
    } 
	
	function  get_lastbill($type){
		if($type ==0){
			$this->db->select("bill_number as bill_number");
			$this->db->limit(1);
			$this->db->order_by('id',"DESC");
			$q=$this->db->get("latest_bill");
			return $q->row();
		}else{
		    $this->db->select("dont_print_billnumber as bill_number");
			$this->db->limit(1);
			$this->db->order_by('id',"DESC");
			$q=$this->db->get("latest_bill");
			return $q->row();
		}
		return false;
	}
	 function checkbill_exist($table_whitelisted){
		$this->db->select("*");
		$this->db->where("bill_number !=","");
		$this->db->where("table_whitelisted",$table_whitelisted);
		$this->db->where("payment_status","Completed"); 
		$q=$this->db->get("bils");
		if($q->num_rows()>0){
			return $q->result();
		}
		return false;
	} 
   function get_recipeCodeById($recipe_id){
	   $this->db->select("code");
	   $this->db->where("id",$recipe_id);
	   $q=$this->db->get("recipe");
	   if($q->num_rows()>0){
		   return $q->row('code');
	   }
	   return false;
   }
   function checkbill_count($reference_no){
		$this->db->select("sales_type_id , sales_split_id  ,        sales_table_id");
		$this->db->join("bils","bils.sales_id=sales.id","left");
		$this->db->where("bils.reference_no",$reference_no);
		$this->db->where("bils.payment_status",NULL); 
		$q=$this->db->get("sales");
		if($q->num_rows()>0){
			return $q->result();
		}
		return false;
	} 
	function filter_number($str){
		preg_match_all('!\d+!', $str, $matches);
		return $matches[0][0];
		
	}
	function getWallets(){
		$this->db->select("*");
		$this->db->where("active",1);
		$q=$this->db->get("wallet_master");
		if($q->num_rows()>0){
			foreach($q->result() as $row){
			$data[] =$row;
		}
		return $data;
		}
		return false;
	}
	function getWalletsById($id){
		$this->db->select("*");
		$this->db->where("id",$id);
		$q=$this->db->get("wallet_master");
		if($q->num_rows()>0){
			return $q->row();
		}
		return false;
	}
	function get_comboItems($recipeid){
		$this->db->select("*");
		$this->db->where("recipe_id",$recipeid);
		$q=$this->db->get("recipe_combo_items");
		if($q->num_rows()>0){
			foreach($q->result() as $row){
				$data[]=$row;
			}
		}
		return $data;
	}
  function	get_ncKotMasters(){
		$this->db->select("*");
		$this->db->where("active",1);
		$q=$this->db->get("nc_kot_type_master");
		if($q->num_rows()>0){
			foreach($q->result() as $row){
				$data[]=$row;
			}
			return $data;
		}
		return false;
	}
	 function	get_ncKotMastersByid($id){
		$this->db->select("*");
		$this->db->where("active",1);
		$this->db->where("id",$id);
		$q=$this->db->get("nc_kot_type_master");
		if($q->num_rows()>0){
			return $q->row();
		}
		return false;
	}
	function getUsers(){
		$this->db->select("id,first_name as name");
		$this->db->where('active',1);
		$q = $this->db->get('users');
		if($q->num_rows()>0){
		    foreach($q->result() as $row){
				$data[]=$row;
			}
			return $data;
		}
		return false;
    }
	function baseToUnitQty($qty,$operator,$operation_value) {
    switch($operator) {
        case '*':
            return ($qty/$operation_value);
            break;
        case '/':
            return ($qty*$operation_value);
            break;
        case '+':
            return ($qty-$operation_value);
            break;
        case '-':
            return ($qty+$operation_value);
            break;
        default:
            return $qty;
    }
}
function unitToBaseQty($qty,$operator,$operation_value) {
    switch($operator) {
        case '*':
            return ($qty*$operation_value);
            break;
        case '/':
            return ($qty/$operation_value);
            break;
        case '+':
            return ($qty+$operation_value);
            break;
        case '-':
            return ($qty-$operation_value);
            break;
        default:
            return $qty;
    }
}

function basePriceToUnitPrice($price,$operator,$operation_value){
		switch($operator) {
        case '*':
            return ($price*$operation_value);
            break;
        case '/':
            return ($price/$operation_value);
            break;
        case '+':
            return ($price+$operation_value);
            break;
        case '-':
            return ($price-$operation_value);
            break;
        default:
            return $price;
	  }
	}
	function generateUniqueTableID($db_insertid,$store_id=false){
	$storeid = ($store_id)?$store_id:$this->store_id;
	return $storeid.$db_insertid;
    }
    function updateUniqueTableId($db_insertid,$unique_ID,$table_name){
	$this->db->set('id',$unique_ID);
	$this->db->where('s_no',$db_insertid);
	$this->db->update($table_name);
  //  echo $this->db->last_query();
	
    }
	 function start_sync($sync_now=false){
	$sync_time = $this->site->get_syncTime();
	if($sync_time){
	    $end_time = $sync_time->end_time;
	    $now = date('Y-m-d H:i:s');
	    $seconds = strtotime($now) - strtotime($end_time);
	}
	
	if($this->centerdb_connected &&($sync_now || !$sync_time || ($seconds>60 && $sync_time->status=="completed"))){		    
	    $ch=curl_init();
	    curl_setopt($ch,CURLOPT_URL,site_url('sync/start_sync'));
	    curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,2);
	    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	    $buffer = curl_exec($ch);
	    curl_close($ch);
	}
    }
	   function getSyncEnabledTables(){
        $q = $this->db->get_where('sync_settings',array('enable_sync'=>1));
        if($q->num_rows()>0){
            $table_names = array();
            foreach($q->result() as $k => $row){
                array_push($table_names,$row->type);
            }
            return $table_names;
        }
    }
	 function get_syncTime(){
      $q = $this->db->get_where('last_sync',array('id'=>1));
      if($q->num_rows()>0){
	return $q->row();
      }
      return false;
    }
    function insert_lastsync($data){
	$this->db->insert('last_sync',$data);
    }
    function update_sync_startTime(){
	$time['start_time'] = date('Y-m-d H:i:s');
	$time['status'] = 'ongoing';
	if($this->get_syncTime()){
	    $this->db->where(array('id'=>1));
	    $this->db->update('last_sync',$time);
	}else{
	    $this->insert_lastsync($time);
	}
     
    }
    function update_sync_endTime(){
	$time['end_time'] = date('Y-m-d H:i:s');
	$time['status'] = 'completed';
	if($this->get_syncTime()){
	    $this->db->where(array('id'=>1));
	    $this->db->update('last_sync',$time);
	}else{
	    $this->insert_lastsync($time);
	}
    }
	function getExpiryDate($recipeId){
		$recipe=$this->getrecipeByID($recipeId);
		
		if($recipe->expiry_date_required==1){
			switch($recipe->type_expiry){
				case 'days':
				  $date=date('Y-m-d', strtotime("+".$recipe->value_expiry." days"));
				break;
				case 'months':
				  $date=date('Y-m-d', strtotime("+".$recipe->value_expiry." months"));
				break;
				case 'year':
				  $date=date('Y-m-d', strtotime("+".$recipe->value_expiry." years"));
				break;
				
			}
			return $date;
		}else{
			return 'null';
		}
		
		
	}
}