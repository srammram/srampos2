<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Customer_api extends CI_Model
{

    public function __construct() {
        parent::__construct();
    }
	
	
	public function orderRequestcheck($customer_id, $table_id){
		$current_date = date('Y-m-d');	
		$this->db->select('customer_request');
		$this->db->where('customer_request', 1);
		$this->db->where('customer_id', $customer_id);
		$this->db->where('table_id', $table_id);
		$this->db->where('payment_status', NULL);
		$this->db->where('DATE(date)', $current_date);
		$q = $this->db->get('orders');
		if ($q->num_rows() > 0) {
          return TRUE;		   
		}
		
		return FALSE;
	}
	
	public function orderRequestchecktable($table_id){
		$current_date = date('Y-m-d');		
		$this->db->select('*');
		$this->db->where('table_id', $table_id);
		$this->db->where('payment_status', NULL);
		$this->db->where('DATE(date)', $current_date);
		$q = $this->db->get('orders');
		if ($q->num_rows() > 0) {
          return TRUE;		   
		}
		
		return FALSE;
	}
	
	public function orderRequestchecktablecustomer($table_id){
		$current_date = date('Y-m-d');		
		$this->db->select('*');
		$this->db->where('table_id', $table_id);
		$this->db->where('payment_status', NULL);
		$this->db->where('DATE(date)', $current_date);
		$q = $this->db->get('orders');		
		if ($q->num_rows() > 0) {
          return $q->row();		   
		}
		
		return FALSE;
	}
	public function customerbbqcoverentered($table_id){
		$current_date = date('Y-m-d');		
		$this->db->select('*');
		$this->db->where('table_id', $table_id);		
		 $this->db->where_in('status', array('waiting','Open'));
		$this->db->where('DATE(created_on)', $current_date);
		$q = $this->db->get('bbq');			
		if ($q->num_rows() > 0) {
          return $q->row();		   
		}
		
		return FALSE;
	}	
	
	public function getSettings(){
		$this->db->select('*');
		$q = $this->db->get('settings');
		if ($q->num_rows() == 1) {
			
			$data = $q->row();
			if($data->customer_discount == 'customer'){
				$data->customer_discount = 'automanual';
			}elseif($data->customer_discount == 'manual'){
				$data->customer_discount = 'manual';
			}elseif($data->customer_discount == 'none'){
				$data->customer_discount = 'manual';
			}
			
			if($data->bbq_discount == 'bbq'){
				$data->bbq_discount = 'automanual';
			}elseif($data->bbq_discount == 'manual'){
				$data->bbq_discount = 'manual';
			}elseif($data->bbq_discount == 'none'){
				$data->bbq_discount = 'manual';
			}
			
			return $data;
		}
		
		return FALSE;
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
		$this->db->join('device_detail', 'device_detail.user_id = users.id', 'left');
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
		
public function Checknightaudit($branch_id) {
	
		$q = $this->db->select('*')->get('settings');
		if ($q->num_rows() > 0) {
			$night_audit_rights = $q->row('night_audit_rights');
		}
		else{
			$night_audit_rights = 0;
		}
		
		$date_format = 'Y-m-d';
		$yesterday = strtotime('-1 day');
		$previous_date = date($date_format, $yesterday);
		$check_row = $this->db->get('nightaudit');

		$installed_date = $this->Settings->installed_date;
		$install = strtotime($installed_date);        
		$install_date = date('Y-m-d', $install);
		$today_date = date('Y-m-d');
		
		$q = $this->db->select('*')->get('settings');
		$night_audit_rights = $q->row('night_audit_rights');
		if($night_audit_rights == 1){
			if($install_date < $today_date){
				
				if($night_audit_rights != 0){
					if($check_row->num_rows() > 0){
						$todaytransactionDay = $this->site->getTransactionDate_nightaudit();
						$previousTransactionDay = $this->site->getLastDayTransactionDate();
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
							
					}
				}else{
					return TRUE;
				}
				
				
			}
			else{
				
				return TRUE;
			}
		}else{
			return TRUE;
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
			WHERE O.table_id ='".$table_id."' AND O.cutomer_id ='".$user_id."' AND O.order_type = 1  AND ((OI.item_status = 'Inprocess') OR(OI.item_status = 'Preparing') OR (OI.item_status = 'Ready')) AND OI.order_item_cancel_status = 0";
			
			$q = $this->db->query($myQuery);
	
			if ($q->num_rows() == 0) {
				return TRUE;
			}
		}
		return FALSE;
	}
	
	public function GetuserByID($phone_number, $table_id=false){
		
		
		$query = $this->db->select('id, group_id, ref_id, group_name, customer_group_id, customer_group_name, name, phone, email')
            ->where('phone', $phone_number)
			->where('group_id', 3)
            ->limit(1)
            ->get('companies');
			
		if ($query->num_rows() === 1) {
			
			$default_waiter = $this->input->post('waiter_id') ? $this->input->post('waiter_id') : 2;
			$default_tables = $this->input->post('table_id') ? $this->input->post('table_id') : 1;
			$default_type = $this->input->post('order_type') ? $this->input->post('order_type') : 1;
		
			
            $user = $query->row();
			
			$u = $this->db->select('id AS waiter_id, user_number AS waiter_number, group_id AS waiter_group_id, warehouse_id, biller_id ')->where('id', $default_waiter)->get('users');
			
			if ($u->num_rows() > 0) {
				$user->waiter_id = $u->row('waiter_id');
				$user->waiter_number = $u->row('waiter_number');
				$user->waiter_group_id = $u->row('waiter_group_id');
				$user->warehouse_id = $u->row('warehouse_id');
				$user->biller_id = $u->row('biller_id');
			}
			$s = $this->db->select('*')->get('settings');
			if ($s->num_rows() > 0) {
				
				$default_currency_data = $this->site->getCurrencyByID($s->row('default_currency'));
				$user->base_currency_id = $default_currency_data->id;
				$user->base_currency_code = $default_currency_data->code;
				$user->base_currency_rate = $default_currency_data->rate;
				
				$user->bbq_discount = $s->row('bbq_discount');
				$user->bbq_enable = $s->row('bbq_enable');
				$user->bbq_adult_price = $s->row('bbq_adult_price');
				$user->bbq_child_price = $s->row('bbq_child_price');
				$user->bbq_kids_price = $s->row('bbq_kids_price');
				
			}
			
			$current_date = date('Y-m-d');	
			
			$bbq_check = $this->db->select('*')->where('table_id', $table_id)->where('customer_id', $user->id)->where('created_on', $current_date)->where_in('status', array('waiting','Open'))->get('bbq', 1);
			 // print_r($this->db->last_query());die;
			if ($bbq_check->num_rows() > 0) {
				$user->bbq_reference_no = $bbq_check->row('reference_no');
				$user->bbq_menu_id = $bbq_check->row('bbq_menu_id');
			}else{
				$user->bbq_reference_no = 'empty';	
				$user->bbq_menu_id = 'empty';	
			}
			
			$ldata = array('user_id' => $user->id, 'login' => $user->id);
			$ldata['group_id'] = $user->group_id;
			$this->db->insert('user_logins', $ldata);
			
			$data = $user;
			
			return $data;
        }
		return FALSE;
	}
	
	public function Checknumber($phone_number){	    
	$q = $this->db->get_where('companies', array('phone' => $phone_number, 'group_id' => 3), 1);
		
        if ($q->num_rows() == 1) {
			
            return TRUE;
        }
		return FALSE;
	}
	
	public function getcustomerusingphone($phone_number){
	    $this->db->select('id,name')->where('phone', $phone_number)->where('group_id',3);	
	$q = $this->db->get('companies');		
        if ($q->num_rows() == 1) {
			
            return $q->result();
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
/*	public function OrderCloseStatus($customer_id){
		$current_date = date('Y-m-d');
		$this->db->where('orders.customer_id', $customer_id);
		$this->db->where('orders.order_status', 'Open');
		$this->db->where('DATE(date)', $current_date);
		$q = $this->db->get('orders');
        if ($q->num_rows() > 0) {
            return FALSE;
        }
		return TRUE;
	}*/
/*21-09-2018 sivan*/
	public function OrderCloseStatus($customer_id,$table_id){
		$current_date = date('Y-m-d');
		$this->db->where('orders.customer_id', $customer_id);
		$this->db->where('orders.payment_status', NULL);
		$this->db->where('DATE(date)', $current_date);
		$this->db->where('orders.table_id', $table_id);
		$this->db->order_by('orders.id', 'DESC');
		$this->db->limit(1);
		$q = $this->db->get('orders');		
        if ($q->num_rows() > 0) {
            return FALSE;
        }
		return TRUE;
	}

	
	public function OrderRequestorBilgeneratorStatus_29_09_2018($user_id, $table_id, $split_id){
		$current_date = date('Y-m-d');
		
		/////////////// check it its bbq and validated ot not ////////////
		$this->db->where('bbq.customer_id', $customer_id);		
		$this->db->where('bbq.created_on', $current_date);			
		$this->db->where('bbq.table_id', $table_id);			
		$this->db->where_not_in('bbq.status', 'Closed');	//other status open /waiting				
		$bqq = $this->db->get('bbq');		
		if(!empty($bbq)){
		    if($bbq->confirmed_by==0){
			$status = 'not_validated';
		    }else{
			$status = 'validated';
		    }
		}else{
		    $status = 'no_bbq';
		}
		
		if($status=='no_bbq' || $status=="validated"){
		
		    $this->db->where('orders.customer_id', $user_id);
		    $this->db->where('orders.table_id', $table_id);
		    if(!empty($split_id)){
			    $this->db->where('orders.split_id', $split_id);
		    }
		    $this->db->where('DATE(date)', $current_date);
		    $this->db->order_by('orders.id', 'DESC');
		    $this->db->limit(1);
		    $q = $this->db->get('orders');
		    if ($q->num_rows() > 0) {
			    if($q->row('customer_request') == 1){
				    return 1;// true
			    }else{
				    $this->db->where('sales.sales_table_id', $q->row('table_id'));
				    $this->db->where('sales.sales_split_id', $q->row('split_id'));
				    $this->db->where('sales.customer_id', $q->row('customer_id'));
				    $this->db->where('DATE(date)', $current_date);
				    $this->db->order_by('sales.id', 'DESC');
				    $this->db->limit(1);
				    $s = $this->db->get('sales');
				    if($s->num_rows() > 0) {
					    return 1;// true
				    }else{
					return 2; //order placed
					//if($status=="validated")
					   // return 2; ///false
					//else
					    //return 3;// false : validated n order placed
				    }
			    }
		
		    }	else{
			if($status=="validated")
			    return 3; ///true :cover only validated, no order placed
			else
			    return 1;// true : no order placed
		    }
		   
		}else{
		    return 4;//bbq not validated
		}
	}

	public function OrderRequestorBilgeneratorStatus($user_id, $table_id, $split_id){

		if($split_id != 0 ){
		    $split_id =$split_id;
		    $split =$split_id;
		}else{
		    $split_id = 0;
		}
		if($split_id != 0  && substr($split, 0, 3) === 'BBQ'){

			$current_date = date('Y-m-d');
            $this->db->where('bbq.customer_id', $user_id);                
            $this->db->where('bbq.created_on', $current_date);                        
            $this->db->where('bbq.table_id', $table_id);   
            $this->db->where_not_in('bbq.status', 'Closed');                                
            // $this->db->order_by('bbq.id', 'DESC');  
            // $this->db->limit(1);                                    
            $bq = $this->db->get('bbq');  
            // echo count($bq);
            // print_r($this->db->last_query());
            // print_r($bq->num_rows());//die;
            if ($bq->num_rows() != 0) {
            	// echo "string";die;
            	$current_date = date('Y-m-d');
	            $this->db->where('bbq.customer_id', $user_id);                
	            $this->db->where('bbq.created_on', $current_date);                        
	            $this->db->where('bbq.table_id', $table_id);                        
	            $this->db->where_not_in('bbq.confirmed_by', 0);                        	            
	            $this->db->order_by('bbq.id', 'DESC');  
	            $this->db->limit(1);                                    
               $bqq1 = $this->db->get('bbq'); 
               // print_r($this->db->last_query());die;
               if ($bqq1->num_rows() > 0) {
               			return 1;
               }else{
               	return 3;//cover NOT validated
               }				
			}
			else{	
			// echo "sivam";			die;
				return 2;
			}
		}else{	
		$current_date = date('Y-m-d');
		$this->db->where('orders.customer_id', $user_id);
		$this->db->where('orders.table_id', $table_id);
		if(!empty($split_id)){
			$this->db->where('orders.split_id', $split_id);
		}
		$this->db->where('DATE(date)', $current_date);
		$this->db->order_by('orders.id', 'DESC');
		$this->db->limit(1);
		$q = $this->db->get('orders');
		// print_r($this->db->last_query());die;
		if ($q->num_rows() > 0) {
			if($q->row('customer_request') == 1){
				return 1;
			}else{
				$this->db->where('sales.sales_table_id', $q->row('table_id'));
				$this->db->where('sales.sales_split_id', $q->row('split_id'));
				$this->db->where('sales.customer_id', $q->row('customer_id'));
				$this->db->where('DATE(date)', $current_date);
				$this->db->order_by('sales.id', 'DESC');
				$this->db->limit(1);
				$s = $this->db->get('sales');				
				if($s->num_rows() > 0) {
					return 1;
				}else{
					return 2;
				}
			}            
        }        
		return 1;
	  }	
	}	
	
public function customercheckbbqvalidate($user_id, $table_id){
    $current_date = date('Y-m-d');
            $this->db->where('bbq.customer_id', $user_id);                
            $this->db->where('bbq.created_on', $current_date);                        
            $this->db->where('bbq.table_id', $table_id);   
            $this->db->where_not_in('bbq.status', 'Closed');    
            $bq = $this->db->get('bbq');  
            
            if ($bq->num_rows() != 0) {            	
            	$current_date = date('Y-m-d');
	            $this->db->where('bbq.customer_id', $user_id);                
	            $this->db->where('bbq.created_on', $current_date);                        
	            $this->db->where('bbq.table_id', $table_id);                        
	            $this->db->where_not_in('bbq.confirmed_by', 0);                        	            
	            $this->db->order_by('bbq.id', 'DESC');  
	            $this->db->limit(1);                                    
               $bqq1 = $this->db->get('bbq'); 
               // print_r($this->db->last_query());die;
               if ($bqq1->num_rows() > 0) {
               			return 1;
               }else{
               	return 3;//cover NOT validated
               }				
			}
			else{				
				return 2;
			}
}

	public function InsertCustomer($phone_number, $name){
		$cg = $this->site->getCustomerGroupByID($this->Settings->customer_group);
		$insert_array = array(
			'group_id' => '3',
			'name' => $name,
			'short_name' => $name,
			'group_name' => 'customer',
			'ref_id' => 'CUS-'.date('YmdHis'), 
			'customer_group_id' => $this->Settings->customer_group,
			'customer_group_name' => $cg->name,
			'phone' => $phone_number,
		);
		$data = $this->db->insert('companies', $insert_array);	
		if(!empty($data)){
			return TRUE;
		}
		return FALSE;
	}
	
	public function GetAllcustomfeedback(){
		$this->db->select('*');
		$query = $this->db->get('customfeedback');
        if ($query->num_rows() > 0) {
           foreach ($query->result() as $row) {
			     $answers = $this->db->select('*')->where('question_id', $row->id)->get('customfeedback_answer');
				 
				 if ($answers->num_rows() > 0) {
					 foreach ($answers->result() as $ans) {
						 $row->answer[$row->id][] = $ans;
					 }
				 }else{
					$row->answer = 'NULL'; 
				 }
			     $data[] = $row;
            }
            return $data;
        }
        return FALSE;
	}
	
	
	public function GetAllSplitconsolidated($user_id, $order_type, $warehouse_id,$table_id){
		
		
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
		//$this->db->where_in("orders.order_type", $order_type);
		$this->db->where('orders.payment_status', NULL);
		$this->db->where('customer_request', 0);
		$this->db->where('orders.table_id', $table_id);
		$this->db->where('restaurant_tables.warehouse_id', $warehouse_id);
		$this->db->group_by("restaurant_table_orders.table_id");
		
		$t = $this->db->get('restaurant_tables');		

		if ($t->num_rows() > 0) {
		
			foreach ($t->result() as $row){
				$data[] = $row;
				$i = $this->db->query("SELECT a.split_id, a.reference_no, a.order_type, order_items.*, recipe.preparation_time, CASE WHEN recipe.khmer_name !='' THEN  recipe.khmer_name ELSE recipe.name END AS khmer_name, CASE WHEN recipe.image !='' THEN CONCAT('".$default_url."', recipe.image) ELSE '$default_image' END AS recipe_image,
						      
				CASE 
WHEN  recipe_feedback_mapping.id IS NULL Then 0 ELSE 1  END as feedback

FROM " . $this->db->dbprefix('orders') . "  AS a
				LEFT JOIN " . $this->db->dbprefix('order_items') . "  AS order_items ON order_items.sale_id = a.id 
				LEFT JOIN " . $this->db->dbprefix('recipe') . " AS recipe ON recipe.id = order_items.recipe_id  
				LEFT JOIN " . $this->db->dbprefix('recipe_feedback_mapping'). " AS recipe_feedback_mapping ON recipe_feedback_mapping.recipe_id=order_items.recipe_id
				WHERE a.customer_id = '".$user_id."' AND a.warehouse_id = ".$warehouse_id." AND  DATE(date) = '".$current_date."' AND  order_items.item_status != 'Closed' AND  order_status = 'Open' AND a.table_id= '".$table_id."' ");
				
				if($i->num_rows() > 0){
					
					$k=1;	
									
					foreach($i->result() as $item){
						unset($item->igst,$item->sgst,$item->gst,$item->cgst,$item->addon_id,$item->addon_qty,$item->option_id);	
						$addons = $this->site->getAddonByRecipeidAndOrderitemid($item->recipe_id, $item->id);
				       $item->addon = $addons ? $addons : 0;
						$check_order[] = $item->order_type;
						$item->timezone = $this->Settings->timezone_gmt;
						//$item->time_limit = date('H:i:s', mktime($item->preparation_time));
						$item->time_limit = gmdate('H:i:s', $item->preparation_time);
						
						
						if($item->order_type == 4){
							$item->highlight_color = 'red';
							$item->highlight_color_id = '1';
							$q = $this->db->select('*')->where('reference_no', $item->split_id)->get('bbq', 1);
							if($q->num_rows() > 0){
								$item->grand_total_cover = ($q->row('number_of_adult') * $q->row('adult_price')) +  ($q->row('number_of_child') * $q->row('child_price')) + ($q->row('number_of_kids') * $q->row('kids_price')); 
								$item->grand_total = 0;
							}else{
								$item->grand_total_cover = 0;
								$item->grand_total = 0;
							}
						}else{
							$item->highlight_color = 'blue';
							$item->highlight_color_id = '2';
							$item->grand_total = $item->subtotal;
						}
						//$item->grand_total = $grand;
						$row->item[] = $item;
						$k++;
						
					}
					
				}
				
				$row->check_order = $check_order;
			}
			return $data;
		}
		
		/*$i = $this->db->query("SELECT a.reference_no, a.order_type, order_items.*, CASE WHEN recipe.image !='' THEN CONCAT('".$default_url."', recipe.image) ELSE '$default_image' END AS recipe_image FROM " . $this->db->dbprefix('orders') . "  AS a
		LEFT JOIN " . $this->db->dbprefix('order_items') . "  AS order_items ON order_items.sale_id = a.id 
		LEFT JOIN " . $this->db->dbprefix('recipe') . " AS recipe ON recipe.id = order_items.recipe_id 
		WHERE a.split_id = '".$split."' AND  DATE(date) = '".$current_date."' ");
		
		if($i->num_rows() > 0){
							
			foreach($i->result() as $item){
				$data[] = $item;
			}
			return $data;
		}*/
		
		return FALSE;	
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
		$this->db->where("orders.order_type", 1);
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
				WHERE a.customer_id = '".$user_id."' AND a.warehouse_id = ".$warehouse_id." AND  DATE(date) = '".$current_date."' AND order_status = 'Open' AND a.table_id= '".$table_id."' ");
				
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
	
	public function notification_clear($notification_id){
		
		if(!empty($notification_id)){	
			
			$this->db->where_in('id', $notification_id);
			$this->db->update('notiy', array('is_read' => 1));			
			
			return true;
		}
		return false;
	}
	
	public function Getnotification($group_id, $user_id, $warehouse_id){
		
		$current_date = date('Y-m-d');
		
		$u = $this->db->select('*')->where('to_user_id', $user_id)->where('is_read', 0)->where('warehouse_id', $warehouse_id)->where('DATE(created_on)', $current_date)->get('notiy');
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
		}*/
		
		if(!empty($user)){
			$data['notification_list'] = $user;
		}
		/*if(!empty($user) && empty($group)){
			$data['notification_list'] = $user;
		}elseif(empty($user) && !empty($group)){
			$data['notification_list'] = $group;
		}elseif(!empty($user) && !empty($group)){
			$data['notification_list'] = array_merge($user, $group);
		}*/

		$data['notification_count'] = count($data['notification_list']);
		
		if(!empty($data['notification_list'])){
			
			return $data;
		}else{
			return false;
		}
				
		
	}
	
	public function Insertfeedback($insert_array){
		
				
		$data = $this->db->insert_batch('feedback', $insert_array);
		
		if(!empty($data)){
			return TRUE;
		}
		return FALSE;
	}
	
	public function Insertextrafeedback($insert_array, $testimonial_array){
		
		$this->db->insert('testimonial', $testimonial_array);
		
		$data = $this->db->insert_batch('extrafeedback', $insert_array);
		
		if(!empty($data)){
			
			return TRUE;
		}
		return FALSE;
	}
	
	public function CancelOrdersItem($split_id, $item_id, $remarks, $user_id, $notification_array){
		
		$q = $this->db->select('sale_id')->where('id', $item_id)->get('order_items');
		if ($q->num_rows() > 0) {
            $sale_id =  $q->row('sale_id');
        }
		
		$k = $this->db->select('waiter_id, chef_id')->where('sale_id', $sale_id)->get('kitchen_orders');
		if ($k->num_rows() > 0) {
            $waiter_id =  $k->row('waiter_id');
			$chef_id =  $k->row('chef_id');
        }
		
		
		 $notification_array['insert_array']['role_id'] = 6;
		 $notification_array['insert_array']['to_user_id'] = $waiter_id;
		
		
		
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
			
			if($order->num_rows() == 0){
				$this->db->where('orders.split_id', $split_id);
				$orderupdate = $this->db->update('orders', array( 'order_cancel_id' => $user_id, 'order_cancel_note' => 'All item order cancel', 'order_cancel_status' => 1));
			}			
			return true;
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
				
				
				$this->db->select("order_items.recipe_id, recipe.category_id, order_items.recipe_code, order_items.recipe_name, order_items.recipe_type, order_items.option_id, order_items.net_unit_price, order_items.unit_price, order_items.quantity, order_items.warehouse_id, order_items.item_tax, order_items.tax_rate_id, order_items.tax, order_items.discount, order_items.item_discount, order_items.subtotal, order_items.serial_no, order_items.real_unit_price, order_items.sale_item_id, order_items.recipe_unit_id, order_items.recipe_unit_code, order_items.unit_quantity, order_items.comment, order_items.gst, order_items.cgst, order_items.sgst, order_items.igst", FALSE);
				$this->db->join('recipe', 'recipe.id = order_items.recipe_id');
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
	
	public function requestBill($timelog_array, $notification_array, $grand_total, $split_id, $table_id, $request_discount){
		
		//$this->site->create_notification($notification_array);
		if(!empty($timelog_array)){
			foreach ($timelog_array as $time) {
					$res = $this->db->insert('time_log', $time);
			}      	
		}
		$q = $this->db->insert('customer_request_discount', $request_discount);
		if(!empty($split_id) && !empty( $table_id)){
			$this->db->where('split_id', $split_id);
			$this->db->where('table_id', $table_id);
    		$this->db->update('orders', array('customer_request' => 1));
			
			
			if(!empty($request_discount['customer_discount_val'])){
				$this->db->select('diccounts_for_customer.*');
				
				$this->db->where('diccounts_for_customer.id', $request_discount['customer_discount_val']);
				$c =  $this->db->get('diccounts_for_customer', 1);
				$c_discount = $c->row('name');
				
			}
			
			if($request_discount['bbq_discount_val']){
				$b = $this->db->select('*')->where('id', $request_discount['bbq_discount_val'])->get('diccounts_for_bbq');
				$b_discount = $b->row('name');
				
			}
			
			$data['gtotal'] = $grand_total;
			$data['c_discount'] = $c_discount;
			$data['b_discount'] = $b_discount;
			
			return $data;
		}
		return FALSE;	
	}
	
	public function requestwithoutBill($split_id, $table_id, $request_discount,  $overtotal){
		
		$q = $this->db->insert('customer_request_discount', $request_discount);
		if(!empty($split_id) && !empty( $table_id)){
			$this->db->where('split_id', $split_id);
			$this->db->where('table_id', $table_id);
    		$this->db->update('orders', array('customer_request' => 1));
			if(!empty($request_discount['customer_discount_val'])){
				$this->db->select('diccounts_for_customer.*');
				
				$this->db->where('diccounts_for_customer.id', $request_discount['customer_discount_val']);
				$c =  $this->db->get('diccounts_for_customer', 1);
				$c_discount = $c->row('name');
				
			}
			
			if($request_discount['bbq_discount_val']){
				$b = $this->db->select('*')->where('id', $request_discount['bbq_discount_val'])->get('diccounts_for_bbq');
				$b_discount = $b->row('name');
				
			}
			
			$data['gtotal'] = array_sum($overtotal);
			$data['c_discount'] = $c_discount;
			$data['b_discount'] = $b_discount;
			
			return $data;
			
		}
		return FALSE;	
	}
	
	
	public function InsertBill($order_data = array(), $order_item = array(), $billData = array(), $splitData = array(), $sales_total = NULL, $delivery_person = NULL, $timelog_array = NULL, $notification_array,  $grand_total)
    {		
    	
		$this->site->create_notification($notification_array);
		
    	$sales_array = array(
		            'grand_total' => $sales_total,
					'delivery_person_id' => $delivery_person,
					'bilgenerator_type' => 1,
		        );

		if(!empty($timelog_array)){
			foreach ($timelog_array as $time) {
					$res = $this->db->insert('time_log', $time);
			}      	
		}
		
		
		$this->db->where('split_id', $order_data['sales_split_id']);
    	$this->db->update('orders', array('order_status' => 'Closed'));
		
		
        if ($this->db->insert('sales', $order_data)) {
            $sale_id = $this->db->insert_id();
            
            $this->db->update('sales', $sales_array, array('id' => $sale_id));

              foreach ($billData as $key =>  $bills) {
              	$bills['sales_id'] = $sale_id;
              	$this->db->insert('bils', $bills);
					$bill_id = $this->db->insert_id();
					$bill_number = sprintf("%'.05d\n", $bill_id);
              	   $this->db->update('bils', array('bill_number' => $bill_number,'bill_sequence_number' => $bill_id), array('id' => $bill_id));
				  $this->site->latest_bill($bill_number);
				foreach ($splitData[$key]  as $bill_items) {
					$bill_items['bil_id'] = $bill_id;
					$this->db->insert('bil_items', $bill_items);
				}
              }

            foreach ($order_item as $item) {
                $this->db->insert('sale_items', $item);
                $sale_item_id = $this->db->insert_id();
                $this->db->update('sale_items', array('sale_id' =>  $sale_id), array('id' => $sale_item_id));

            }

           
                 $data['grand_total'] = $grand_total;
            return $data;
        }
        return false;
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
				$bill_number = $this->site->BBQgenerate_bill_number($bilsrow['table_whitelisted']);
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
	
	function isTableWhitelisted($tableid){
		$q = $this->db->get_where("restaurant_tables",array('id'=>$tableid,'whitelisted'=>1));
		if ($q->num_rows() > 0) {
			return 1;
		}
		return 0;
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
	
	public function getrecipeByID($id)
    {

        $q = $this->db->get_where('recipe', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return FALSE;
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
	
	 public function InsertBillDine($order_data_dine = array(), $order_item_dine = array(), $billData_dine = array(), $splitData_dine = array(), $sales_total = NULL, $delivery_person = NULL,$timelog_array = NULL, $notification_array = array(),$order_item_id =array())
    {		
		
    	
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
					$bill_id = $this->db->insert_id();
					//$bill_number = sprintf("%'.05d", $bill_id);
		   $bill_number = $this->site->generate_bill_number($bills['table_whitelisted']);
              	   $this->db->update('bils', array('bill_number' => $bill_number,'bill_sequence_number' => $bill_id), array('id' => $bill_id));
				$this->site->latest_bill($bill_number);
				foreach ($splitData_dine[$key]  as $bill_items) {
					$bill_items['bil_id'] = $bill_id;
					$this->db->insert('bil_items', $bill_items);
					
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
                     
            return true;
        }
        return false;
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
				
				
			  $this->db->select("order_items.id,order_items.recipe_id, recipe.category_id, recipe.subcategory_id, order_items.recipe_code, order_items.recipe_name, order_items.recipe_type, order_items.option_id, order_items.net_unit_price, order_items.unit_price, order_items.quantity, order_items.warehouse_id, order_items.item_tax, order_items.tax_rate_id, order_items.tax, order_items.discount, order_items.item_discount, order_items.subtotal, order_items.serial_no, order_items.real_unit_price, order_items.sale_item_id, order_items.recipe_unit_id, order_items.recipe_unit_code, order_items.unit_quantity, order_items.comment, order_items.gst, order_items.cgst, order_items.sgst, order_items.igst", FALSE);
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

	public function getBBQCoverSettings(){
		$this->db->select(' bbq_covers_limit,bbq_adult_price, bbq_child_price, bbq_kids_price');
		$q = $this->db->get('settings');		 
		if ($q->num_rows() > 0) {	 			
			$data = $q->row();				
			return $data;
		}		
		return FALSE;
	}
	public function ifBBqReturned($table_id){
	    $current_date = date('Y-m-d');	
	    $this->db->select();
	    $this->db->from('bbq');
	    $this->db->join('sale_return','sale_return.split_id=bbq.reference_no');
	    $this->db->where('table_id',$table_id);
	    $this->db->where('status','closed');
	    $this->db->where('payment_status','paid');
	    $this->db->where('DATE(created_on)', $current_date);
	    //echo $this->db->get_compiled_select();
	    $q = $this->db->get();
	    if ($q->num_rows() > 0) {	 			
			$data = $q->row();				
			return $data;
	    }		
	    return FALSE;
	}
	public function CheckLastBBQ_data($table_id,$phone){
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
		$order_request = strtolower($data->order_request);		
		 if($status=="open" || $status=="waiting"){
		    return ($data->phone==$phone)?true:array('status'=>4,'message'=>lang('table_orders_is_not_closed'),'message_khmer'=> html_entity_decode(lang('table_orders_is_not_closed')));
		}else if($status=="closed" && strtolower($data->payment_status)=="paid"){
		    $salereturn = $this->db->get_where('sale_return',array('split_id'=>$data->reference_no));
		    return ($salereturn->num_rows() > 0)?true:array('status'=>3,'message'=>lang('bbq_return_not_yet_completed'),'message_khmer'=> html_entity_decode(lang('bbq_return_not_yet_completed')));
		}else{
		    return true;
		}
	    }		
	    return true;//allow login
	}
	/*	 24-07-2018
	public function CheckBBQorder_request($table_id,$phone){
	    $current_date = date('Y-m-d');	
	    $this->db->select();
	    $this->db->from('bbq');
	    $this->db->where('table_id',$table_id);
	    $this->db->where_not_in('status', 'Closed');
	    $this->db->where('DATE(created_on)', $current_date);	    
	    $this->db->limit(1);
	    $this->db->order_by('id','DESC');
	    $q = $this->db->get();
	    if ($q->num_rows() > 0) {		
			$data = $q->row();		
			// print_r($data->phone);die;
			$order_request = strtolower($data->order_request);		
				if($order_request==1 && $data->phone==$phone &&  $data->confirmed_by !=1){
					return true;
			    }
		    }		
	    return false;//allow login
	}*/
	/*24-07-2019 new */
		public function CheckBBQorder_request($table_id,$phone){
	    $current_date = date('Y-m-d');	
	    $this->db->select();
	    $this->db->from('bbq');
	    $this->db->where('table_id',$table_id);
	    $this->db->where_not_in('status', 'Closed');
	    $this->db->where('DATE(created_on)', $current_date);	    
	    $this->db->limit(1);
	    $this->db->order_by('id','DESC');
	    $q = $this->db->get();	    
	    if ($q->num_rows() > 0) {		
			$data = $q->row();					
			$order_request = strtolower($data->order_request);		
				if($order_request!=0 && $data->phone==$phone &&  $data->confirmed_by !=0){
					return false; //allow login
			    }else{
			    	return true;//not allow login(because cover vaidation is not completed.)
			    }
		    }else{
				return false; //allow login
		    }		
	    
	}
	public function CheckBBQorder_request_new($table_id,$phone){
	    $current_date = date('Y-m-d');	
	    $this->db->select();
	    $this->db->from('bbq');
	    $this->db->where('table_id',$table_id);
	    $this->db->where('DATE(created_on)', $current_date);	    
	    $this->db->limit(1);
	    $this->db->order_by('id','DESC');
	    $q = $this->db->get();
	    if ($q->num_rows() > 0) {		
			$data = $q->row();		
			// print_r($data->phone);die;
			$order_request = strtolower($data->order_request);		
				if($order_request==1 && $data->phone==$phone &&  $data->confirmed_by !=1){
					return true;
			    }
		    }		
	    return false;//allow login
	}


	public function CheckTableForBBQorAlacarte($table_id){
		$this->db->select('sale_type');		
		$this->db->where('id', $table_id);
		$q = $this->db->get('restaurant_tables');
		$bbq_enable = 0;
		if ($q->num_rows() > 0) {
		      $sale_type = $q->row('sale_type');	
				if($sale_type == 'bbq')	
				{
					$bbq_enable = 1;
				}	
			}
			return $bbq_enable;
		return FALSE;
	}

	//function getCurrentBBQData($table_id){
	//    $current_date = date('Y-m-d');	
	//    $this->db->select();
	//    $this->db->from('bbq');
	//    //$this->db->join('sale_return','sale_return.split_id=bbq.reference_no');
	//    $this->db->where('table_id',$table_id);
	//    $this->db->where('status','closed');
	//    $this->db->where('payment_status','paid');
	//    $this->db->where('DATE(created_on)', $current_date);
	//    //echo $this->db->get_compiled_select();
	//    $q = $this->db->get();
	//    if ($q->num_rows() > 0) {	 			
	//		$data = $q->row();				
	//		return $data;
	//    }		
	//    return FALSE;
	//}
	public function GetFeedback_itemsList($user_id,$warehouse_id,$table_id){
		
		
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
		//$this->db->where_in("orders.order_type", $order_type);
		$this->db->where('orders.payment_status', NULL);
		$this->db->where('customer_request', 0);
		$this->db->where('orders.table_id', $table_id);
		$this->db->where('restaurant_tables.warehouse_id', $warehouse_id);
		$this->db->group_by("restaurant_table_orders.table_id");
		
		$t = $this->db->get('restaurant_tables');		

		if ($t->num_rows() > 0) {
		
			foreach ($t->result() as $row){
				$data[] = $row;
				$i = $this->db->query("SELECT a.split_id, a.reference_no, a.order_type, order_items.*, recipe.preparation_time, CASE WHEN recipe.khmer_name !='' THEN  recipe.khmer_name ELSE recipe.name END AS khmer_name, CASE WHEN recipe.image !='' THEN CONCAT('".$default_url."', recipe.image) ELSE '$default_image' END AS recipe_image,
						      
				CASE 
WHEN  recipe_feedback_mapping.id IS NULL Then 0 ELSE 1  END as feedback

FROM " . $this->db->dbprefix('orders') . "  AS a
				LEFT JOIN " . $this->db->dbprefix('order_items') . "  AS order_items ON order_items.sale_id = a.id 
				LEFT JOIN " . $this->db->dbprefix('recipe') . " AS recipe ON recipe.id = order_items.recipe_id  
				LEFT JOIN " . $this->db->dbprefix('recipe_feedback_mapping'). " AS recipe_feedback_mapping ON recipe_feedback_mapping.recipe_id=order_items.recipe_id
				WHERE a.customer_id = '".$user_id."' AND a.warehouse_id = ".$warehouse_id." AND  DATE(date) = '".$current_date."' AND  order_items.item_status != 'Closed' AND  order_status = 'Open' AND a.table_id= '".$table_id."' ");
				
				if($i->num_rows() > 0){
					
					$k=1;	
									
					foreach($i->result() as $item){
						$check_order[] = $item->order_type;
						$item->timezone = $this->Settings->timezone_gmt;
						//$item->time_limit = date('H:i:s', mktime($item->preparation_time));
						$item->time_limit = gmdate('H:i:s', $item->preparation_time);
						
						
						if($item->order_type == 4){
							$item->highlight_color = 'red';
							$item->highlight_color_id = '1';
							$q = $this->db->select('*')->where('reference_no', $item->split_id)->get('bbq', 1);
							if($q->num_rows() > 0){
								$item->grand_total_cover = ($q->row('number_of_adult') * $q->row('adult_price')) +  ($q->row('number_of_child') * $q->row('child_price')) + ($q->row('number_of_kids') * $q->row('kids_price')); 
								$item->grand_total = 0;
							}else{
								$item->grand_total_cover = 0;
								$item->grand_total = 0;
							}
						}else{
							$item->highlight_color = 'blue';
							$item->highlight_color_id = '2';
							$item->grand_total = $item->subtotal;
						}
						//$item->grand_total = $grand;
						$row->item[] = $item;
						$k++;
						
					}
					
				}
				
				$row->check_order = $check_order;
			}
			return $data;
		}
		
		
		return FALSE;	
	}
}
