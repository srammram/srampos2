<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Biller_api extends CI_Model
{

    public function __construct() {
        parent::__construct();
    }
	
	public function getSettings(){
		$this->db->select('*');
		$q = $this->db->get('settings');
		if ($q->num_rows() == 1) {
			$data = $q->row();
			
			return $data;
		}
		
		return FALSE;
	}
	
	public function splitWaiterid($split_id){
		$q = $this->db->select('*')->where('split_id', $split_id)->get('orders');
		if ($q->num_rows() == 1) {
			
			return  $q->row('created_by');
			
		}
		return FALSE;
	}
	
	public function deviceGET($user_id){
		$this->db->select('users.id, device_detail.device_token');
		$this->db->join('device_detail', 'device_detail.user_id = users.id', 'left');
		$this->db->where('users.id', $user_id);
		$q = $this->db->get('users');
		if ($q->num_rows() == 1) {
			
			return  $q->row('device_token');
			
		}
		return FALSE;
	}
	public function deviceDetails($user_id){
		$this->db->select('users.id, device_detail.device_token,device_detail.socket_id');
		$this->db->join('device_detail', 'device_detail.user_id = users.id', 'left');
		$this->db->where('users.id', $user_id);
		$q = $this->db->get('users');
		if ($q->num_rows() == 1) {
			
			return  $q->row();
			
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
	
	public function getPOSSettings(){
		$this->db->select('default_tax, tax_type,taxation_report_settings,discount_popup_screen_in_bill_print');
		$q = $this->db->get('pos_settings');
		if ($q->num_rows() == 1) {
			$data = $q->row();
			return $data;
		}
		return FALSE;
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
	
	public function getrecipeByID($id)
    {

        $q = $this->db->get_where('recipe', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return FALSE;
    }
	
	function recipe_customer_discount_calculation($itemid,$groupid,$finalAmt,$discountid, $customer){
	//echo $itemid.'-'.$groupid.'-'.$finalAmt.'-'.$discountid;
	if($customer=="customer"){
	    $discount  = $this->getCategory_cusDiscount($groupid,$discountid);
	    if($discount){
		return $discountAmt = $finalAmt*($discount/100);
		
	    }
	}else if($customer=="manual"){//manual
	    $discount_value = $discountid;
	    return $discountAmt = $this->site->calculateDiscount($discount_value, $finalAmt);
	}
	return 0;
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
	
	  public function InsertBillorder($order_data = array(), $order_item = array(), $billData = array(), $splitData = array(), $sales_total = NULL, $delivery_person = NULL,$timelog_array = NULL, $notification_array = array(),$order_item_id =array())
    {		
    	
    	$sales_array = array(
		            'grand_total' => $sales_total,
					'delivery_person_id' => $delivery_person
		        );
		
		$this->site->create_notification($notification_array);
    	foreach ($timelog_array as $time) {
              	$res = $this->db->insert('time_log', $time);
        }      	
    	
		
		
        if ($this->db->insert('sales', $order_data)) {
            $sale_id = $this->db->insert_id();
            
            $this->db->update('sales', $sales_array, array('id' => $sale_id));

              foreach ($billData as $key =>  $bills) {
              	$bills['sales_id'] = $sale_id;
		$bills['table_whitelisted'] = $this->isTableWhitelisted($order_data['sales_table_id']);
              	$this->db->insert('bils', $bills);
					$bill_id = $this->db->insert_id();
					//$bill_number = sprintf("%'.05d", $bill_id);
		   $bill_number = $this->site->generate_bill_number($bills['table_whitelisted']);
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
           
            if ($order_data['sale_status'] == 'completed') {
            }
                     
            return true;
        }
        return false;
    } 
	
	public function InsertBill($order_data = array(), $order_item = array(), $billData = array(), $splitData = array(), $sales_total = NULL, $delivery_person = NULL, $timelog_array = NULL, $notification_array )
    {		
    	
		$this->site->create_notification($notification_array);
		
    	$sales_array = array(
		            'grand_total' => $sales_total,
					'delivery_person_id' => $delivery_person
		        );

		if(!empty($timelog_array)){
				foreach ($timelog_array as $time) {
						$res = $this->db->insert('time_log', $time);
				}      	
		}
    	
		$this->db->update('orders', array('spli_id' => $order_data->sales_table_id), array('order_status' => 'Closed'));
		
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

           
                     
            return true;
        }
        return false;
    } 
	
	
	public function getAllSalesWithbiller($order_type = NULL, $warehouse_id){
		
		$current_date = date('Y-m-d');
		$this->db->select("sales.*, ,restaurant_tables.name as tablename,restaurant_areas.name as areaname ");
		$this->db->join("restaurant_tables", "restaurant_tables.id = sales.sales_table_id",'left');
		$this->db->join("restaurant_areas", "restaurant_areas.id = restaurant_tables.area_id",'left');
		if(!empty($order_type)){
			$this->db->where('sales.sales_type_id', $order_type);	
		}
		$this->db->where('sales.warehouse_id', $warehouse_id);
		$this->db->where('sales.sale_status', 'Process');
		$this->db->where('sales.cancel_status', 0);
		$this->db->where('DATE(date)', $current_date);

		$s = $this->db->get('sales');

		if ($s->num_rows() > 0) {

            foreach ($s->result() as $row) {
				if($row->tablename == NULL){
					$row->tablename = '0';
				}
				if($row->areaname == NULL){
					$row->areaname = '0';
				}
				$data[] = $row;	
			}
			
			return $data;
		}else{
			return $data = array();
		}

		return FALSE;
	}
	
	public function CancelSale($cancel_remarks, $sales_id, $user_id, $notification_array){

    	$q = $this->db->select('sales_split_id, sales_table_id')->where('id', $sales_id)->get('sales');
		if ($q->num_rows() > 0) {
            $split_id =  $q->row('sales_split_id');
			$table_id =  $q->row('sales_table_id');
        }
        /*echo $split_id;*/
		
		$k = $this->db->select('id ')->where('split_id', $split_id)->get('orders');
		if ($k->num_rows() > 0) {
            $id =  $k->row('id');
        }
		
		$sd = $this->db->select('waiter_id, chef_id')->where('sale_id', $id)->get('kitchen_orders');
		if ($sd->num_rows() > 0) {
            $waiter_id =  $sd->row('waiter_id');
			$chef_id =  $sd->row('chef_id');
        }
		
		$notification_array['insert_array']['msg'] = 'Cashier has benn cancel this bil ('.$split_id.')';
		$notification_array['insert_array']['table_id'] = $table_id;
		$notification_array['insert_array']['type'] = 'Cashier cancel bils';
		 $notification_array['insert_array']['role_id'] = 7;
		 $notification_array['insert_array']['to_user_id'] = $waiter_id;
		
		
		
		$this->site->create_notification($notification_array);
        /*echo $id;die;*/
        $sale_aray = array(
            'canceled_user_id' => $user_id,
            'cancel_remarks' => $cancel_remarks,
            'cancel_status' => 1,
        );
        $order_item_array = array(
            'order_item_cancel_id' => $user_id,
            'order_item_cancel_note' => $cancel_remarks,
            'order_item_cancel_status' => 1,
        );

        $order_array = array(
            'order_cancel_id' => $user_id,
            'order_cancel_note' => $cancel_remarks,
            'order_cancel_status' => 1,
            'payment_status' => 'Cancelled',
        );
 	
         $bill_array = array(
            'updated_by' => $user_id,
            'updated_at' => date('Y-m-d H:i:s'),
            'payment_status' => 'Cancelled',
            'bil_status' => 'Cancelled',
        );

		$this->db->update('bils', $bill_array, array('sales_id' => $sales_id));
	    $this->db->update('orders', $order_array, array('id' => $id));

		$this->db->update('order_items', $order_item_array, array('sale_id' => $id));
			
		$this->db->where('id', $sales_id);
		if ($this->db->update('sales',  $sale_aray)) {
			return true;
		}
		return false;

    }
	
	public function getBilvalue($bil_id){
		$this->db->select("bils.*");
		$this->db->where('bils.id', $bil_id);
		$b = $this->db->get('bils');
		if ($b->num_rows() > 0) {
			
			return $b->row();	
		}
		return FALSE;
	}
	
	
	
	
	 public function insertPayment($update_bill = array(), $bill_id =NULL, $payment = array(), $multi_currency = array(), $salesid = NULL, $sales_bill = array(), $order_split_id = NULL, $notification_array,$total,$customer_id,$loyalty_used_points,$taxation,$customer_changed)
    {      
		//print_r($payment);die;
		$q = $this->db->select('sales_split_id, sales_table_id')->where('id', $salesid)->get('sales');
		if ($q->num_rows() > 0) {
            $split_id =  $q->row('sales_split_id');
			$table_id =  $q->row('sales_table_id');			
        }
        $bill_number = $this->site->Payment_dine_bill_number($taxation,$bill_id);
		$bill_no = $bill_number;
        /*if($taxation == 1){
			$bill_number = $this->site->generate_bill_number($taxation);
			$this->db->update('bils', array('bill_number' => $bill_number), array('id' => $bill_id));
			$bill_no = $bill_number;
		}
		else{
				$bilno = $this->db->select('bill_number')->where('id', $bill_id)->get('bils');		
				$bill_no = $bilno->row('bill_number');
		}*/
       		

		$this->db->update('bils', array('bill_number' => $bill_number), array('id' => $bill_id));
		$k = $this->db->select('id ')->where('split_id', $split_id)->get('orders');
		if ($k->num_rows() > 0) {
            $id =  $k->row('id');
        }
		
		$k = $this->db->select('waiter_id, chef_id')->where('sale_id', $id)->get('kitchen_orders');
		if ($k->num_rows() > 0) {
            $waiter_id =  $k->row('waiter_id');
			$chef_id =  $k->row('chef_id');
        }
		
		$notification_array['insert_array']['msg'] = 'Cashier has benn payment this bil ('.$split_id.')';
		$notification_array['insert_array']['table_id'] = $table_id;
		$notification_array['insert_array']['type'] = 'Cashier payment bils';
		 $notification_array['insert_array']['role_id'] = 7;
		 $notification_array['insert_array']['to_user_id'] = $waiter_id;
		
		
		
		$this->site->create_notification($notification_array);
		
    	if ($this->db->update('bils', $update_bill, array('id' => $bill_id))){
			$this->db->update('sales', $sales_bill, array('id' => $salesid));
    			$order_count = $this->db->get_where('bils', array('sales_id' => $salesid));
				$order_count =$order_count->num_rows();
				
				 $order_closed_count = $this->db->get_where('bils', array('bils.sales_id' => $salesid,'bils.payment_status' => 'Completed'));
				 $order_closed_count =$order_closed_count->num_rows();

    		foreach ($payment as $item) {
    		$this->db->insert('payments', $item);
    		}
			foreach ($multi_currency as $currency) {
    			$this->db->insert('sale_currency', $currency);
    		}
    		 
	    		$sales_array = array(
		            'sale_status' => "Closed",
		            'payment_status' => "Paid",
		        );

		        $tables_array = array(
		            'session_end' => date('Y-m-d H:m:s'),
		        );   


			    if ($order_count  == $order_closed_count) {
			    	
			         $this->db->update('sales', $sales_array, array('id' => $salesid));
			         $this->db->update('orders', $sales_array, array('split_id' =>  $order_split_id));

			        $res =  $this->db->update('restaurant_table_sessions', $tables_array, array('split_id' =>  $order_split_id));
			       
		        }

		        
		        if($customer_changed == 1){
		        	$this->site->UpdateCustomerFromLoyalty($customer_id,$bill_id,$salesid,$order_split_id);	       
		        }
		         $this->site->LoyaltyinserAndUpdate($bill_id,$total,$customer_id,$loyalty_used_points);	

    	 return true;
    	}    	
    	return false;
    }  
	
	public function BBQinsertPayment($update_bill = array(), $bill_id =NULL, $payment = array(), $multi_currency = array(), $salesid = NULL, $sales_bill = array(), $order_split_id = NULL, $notification_array, $total,$customer_id,$loyalty_used_points,$customer_changed, $taxation)
    {      
	
		$q = $this->db->select('sales_split_id, sales_table_id')->where('id', $salesid)->get('sales');
		if ($q->num_rows() > 0) {
            $split_id =  $q->row('sales_split_id');
			$table_id =  $q->row('sales_table_id');
        }
        $bill_number = $this->site->Payment_dine_bill_number($taxation,$bill_id);
		$bill_no = $bill_number;
		
        /*if($taxation == 1){
			$bill_number = $this->site->generate_bill_number($taxation);
			$this->db->update('bils', array('bill_number' => $bill_number), array('id' => $bill_id));
			$bill_no = $bill_number;
		}
		else{
				$bilno = $this->db->select('bill_number')->where('id', $bill_id)->get('bils');		
				$bill_no = $bilno->row('bill_number');
		}*/

		$k = $this->db->select('id ')->where('split_id', $split_id)->get('orders');
		if ($k->num_rows() > 0) {
            $id =  $k->row('id');
        }
		
		$k = $this->db->select('waiter_id, chef_id')->where('sale_id', $id)->get('kitchen_orders');
		if ($k->num_rows() > 0) {
            $waiter_id =  $k->row('waiter_id');
			$chef_id =  $k->row('chef_id');
        }
		
		$notification_array['insert_array']['msg'] = 'Cashier has benn payment this bil ('.$split_id.')';
		$notification_array['insert_array']['table_id'] = $table_id;
		$notification_array['insert_array']['type'] = 'Cashier payment bils';
		 $notification_array['insert_array']['role_id'] = 7;
		 $notification_array['insert_array']['to_user_id'] = $waiter_id;
		
		
		
		$this->site->create_notification($notification_array);
		
    	if ($this->db->update('bils', $update_bill, array('id' => $bill_id))){
			$this->db->update('sales', $sales_bill, array('id' => $salesid));
    			$order_count = $this->db->get_where('bils', array('sales_id' => $salesid));
				$order_count =$order_count->num_rows();
				
				 $order_closed_count = $this->db->get_where('bils', array('bils.sales_id' => $salesid,'bils.payment_status' => 'Completed'));
				 $order_closed_count =$order_closed_count->num_rows();

    		foreach ($payment as $item) {
    		$this->db->insert('payments', $item);
    		}
			foreach ($multi_currency as $currency) {
    			$this->db->insert('sale_currency', $currency);
    		}
    		 
	    		$sales_array = array(
		            'sale_status' => "Closed",
		            'payment_status' => "Paid",
		        );

				$bbq_array = array(
					'status' => "Closed",
					'payment_status' => "Paid",
				);	
				

		        $tables_array = array(
		            'session_end' => date('Y-m-d H:m:s'),
		        );   


			    if ($order_count  == $order_closed_count) {
			    	
			         $this->db->update('sales', $sales_array, array('id' => $salesid));
			         $this->db->update('orders', $sales_array, array('split_id' =>  $order_split_id));
					$this->db->update('bbq', $bbq_array, array('reference_no' => $order_split_id, 'table_id' => $table_id));
			        $res =  $this->db->update('restaurant_table_sessions', $tables_array, array('split_id' =>  $order_split_id));
			       
		        }
		        
		        if($customer_changed == 1){
		        	$this->site->UpdateCustomerFromLoyalty($customer_id,$bill_id,$salesid,$order_split_id);	       
		        }
		         $this->site->LoyaltyinserAndUpdate($bill_id,$total,$customer_id,$loyalty_used_points);

    	 return true;
    	}    	
    	return false;
    }  
	
	
	public function CONinsertPayment($update_bill = array(), $billid_val =NULL, $consolidatedpayment = array(), $multi_currency = array(), $salesid_val = NULL, $sales_bill = array(), $order_split_id = NULL, $notification_array, $total,$customer_id,$loyalty_used_points,$customer_changed, $taxation)
    {      
	
		foreach($salesid_val as $salesid){
			$q = $this->db->select('sales_split_id, sales_table_id,GROUP_CONCAT(id SEPARATOR ",") as sales_id')->where('id', $salesid)->get('sales');
			if ($q->num_rows() > 0) {
				$split_id =  $q->row('sales_split_id');
				$table_id =  $q->row('sales_table_id');
				$sales_id =  $q->row('sales_id');
			}
			
			if($taxation == 1){
    		$bill_number = $this->site->BBQgenerate_bill_number($taxation);
				$billno = array(
				'bill_number' => $bill_number,				
				);							
				 $id2 =   explode(',',$sales_id);
					$this->db->where_in('sales_id', $id2);
					$this->db->update('bils',  $billno);				
			}	

			$k = $this->db->select('id ')->where('split_id', $split_id)->get('orders');
			if ($k->num_rows() > 0) {
				$id =  $k->row('id');
			}
			
			$k = $this->db->select('waiter_id, chef_id')->where('sale_id', $id)->get('kitchen_orders');
			if ($k->num_rows() > 0) {
				$waiter_id =  $k->row('waiter_id');
				$chef_id =  $k->row('chef_id');
			}
			
			$notification_array['insert_array']['msg'] = 'Cashier has benn payment this bil ('.$split_id.')';
			$notification_array['insert_array']['table_id'] = $table_id;
			$notification_array['insert_array']['type'] = 'Cashier payment bils';
			 $notification_array['insert_array']['role_id'] = 7;
			 $notification_array['insert_array']['to_user_id'] = $waiter_id;
			
			$this->site->create_notification($notification_array);
		}
		
		
		foreach ($consolidatedpayment as $item) {
			unset($item['exchange_enable']);
			$this->db->insert('payments', $item);
		}
		
		foreach ($multi_currency as $currency) {
			$this->db->insert('sale_currency', $currency);
		}
		
		
    	if(!empty($salesid_val)){
			
			for($i=1; $i<=count($salesid_val); $i++){
				
				$this->db->where('id', $billid_val[$i-1]);
				$this->db->update('bils', $update_bill[$i]);
				
				$this->db->where('id', $salesid_val[$i-1]);
				$this->db->update('sales', $sales_bill[$i]);
				
				$order_count = $this->db->get_where('bils', array('sales_id' => $salesid));
				$order_count =$order_count->num_rows();
				$order_closed_count = $this->db->get_where('bils', array('bils.sales_id' => $salesid,'bils.payment_status' => 'Completed'));
				$order_closed_count =$order_closed_count->num_rows();
				
				$sales_array = array(
					'sale_status' => "Closed",
					'payment_status' => "Paid",
				);
				
				$bbq_array = array(
					'status' => "Closed",
					'payment_status' => "Paid",
				);
		
				$tables_array = array(
					'session_end' => date('Y-m-d H:m:s'),
				); 
				
					$this->db->update('sales', $sales_array, array('id' => $salesid_val[$i-1]));
					$this->db->update('orders', $sales_array, array('split_id' =>  $order_split_id));
					$this->db->update('bbq', $bbq_array, array('reference_no' => $order_split_id));
					$res =  $this->db->update('restaurant_table_sessions', $tables_array, array('split_id' =>  $order_split_id));

					if($customer_changed == 1){
		        	 $this->site->UpdateCustomerFromLoyalty($customer_id,$billid_val[$i-1],$$salesid_val[$i-1],$order_split_id);	       
		           }
		         	$this->site->LoyaltyinserAndUpdate($billid_val[$i-1],$total,$customer_id,$loyalty_used_points);
				
			}
			return TRUE;
		} 	
    	return false;
    }
	

	public function getBillingsplitdata($bil_id, $consolidated_id){
		$bil_id = explode(',', $bil_id);
		$this->db->select('sales.sales_split_id, bils.sales_id')->where_in('bils.id', $bil_id);
		$this->db->join('sales', 'sales.id = bils.sales_id');
		$q = $this->db->get('bils'); 
		if ($q->num_rows() > 0) {
			foreach ($q->result() as $row) {
				$data[] = $row;
			}
			return $data;
		}
		return false;
	}
	
	public function getSplitBils($split_id){
		$this->db->select('sales.*, bils.id as bil_id');
		$this->db->join('bils', 'bils.sales_id = sales.id');
		$this->db->where('sales.sales_split_id', $split_id);
		$q = $this->db->get('sales');
		if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
		return FALSE;
	}
	
	public function CONgetAllBilling($sales_id = NULL){
		
		$billQuery = "SELECT  GROUP_CONCAT(id) as ids FROM " . $this->db->dbprefix('sales') . "  WHERE sales_split_id = '".$sales_id."' ";
		$q = $this->db->query($billQuery);
		
		$ids = $q->row('ids');
		
		
		 $bill = "SELECT  GROUP_CONCAT(id) as id,  GROUP_CONCAT(bill_number) as bill_number, SUM(total_tax) as total_tax, SUM(grand_total) as grand_total  FROM " . $this->db->dbprefix('bils') . "  WHERE sales_id IN  (".$ids.") ";
		
		$b = $this->db->query($bill);		
		
	
		if ($b->num_rows() > 0) {
			foreach ($b->result() as $row) {
				
				$ss = "SELECT sales_split_id, sales_type_id, sales_table_id FROM " . $this->db->dbprefix('sales') . " WHERE sales_split_id = '".$sales_id."' ";
				$s = $this->db->query($ss);	
				if ($s->num_rows() > 0) {
					foreach ($s->result() as $sow) {
						$sales_order = $sow;
						$check_order[] = $sow->sales_type_id;
						$cr = "SELECT * FROM  " . $this->db->dbprefix('customer_request_discount') . " WHERE split_id = '".$sow->sales_split_id."' ";
						$c = $this->db->query($cr);	
						if ($c->num_rows() > 0) {
							foreach ($c->result() as $cow) {
								if(empty($cow->customer_type_val)){
									$cow->customer_type_val = "0";	
								}
								if(empty($cow->customer_discount_val)){
									$cow->customer_discount_val = "0";	
								}
								if(empty($cow->bbq_type_val)){
									$cow->bbq_type_val = "0";	
								}
								if(empty($cow->bbq_discount_val)){
									$cow->bbq_discount_val = "0";	
								}
								$customer_request = $cow;
							}
						}
					}
					$row->sales_order = $sales_order;
					$row->check_order = $check_order;
					$row->customer_request = $customer_request;
				}
				
				$data[] = $row;	
			}
			return $data;
		}
		return FALSE;
	}
	
	public function getAllBilling($sales_id = NULL){
		
		$this->db->select("bils.id, bils.bill_number,CAST(" . $this->db->dbprefix('bils') . ".total_tax AS DECIMAL(10,2)) as total_tax,CAST(" . $this->db->dbprefix('bils') . ".grand_total AS DECIMAL(10,2)) as grand_total");
		
		$this->db->where('bils.sales_id', $sales_id);
		$this->db->where('bils.bil_status', NULL);
		$b = $this->db->get('bils');
		if ($b->num_rows() > 0) {
			foreach ($b->result() as $row) {
				
				$ss = "SELECT sales_split_id, sales_type_id, sales_table_id FROM " . $this->db->dbprefix('sales') . " WHERE id = ".$sales_id." ";
				$s = $this->db->query($ss);	
				if ($s->num_rows() > 0) {
					foreach ($s->result() as $sow) {
						$sales_order = $sow;
						$check_order[] = $sow->sales_type_id;
						
						$cr = "SELECT * FROM  " . $this->db->dbprefix('customer_request_discount') . " WHERE split_id = '".$sow->sales_split_id."' ";
						$c = $this->db->query($cr);	
						if ($c->num_rows() > 0) {
							foreach ($c->result() as $cow) {
								if(empty($cow->customer_type_val)){
									$cow->customer_type_val = "0";	
								}
								if(empty($cow->customer_discount_val)){
									$cow->customer_discount_val = "0";	
								}
								if(empty($cow->bbq_type_val)){
									$cow->bbq_type_val = "0";	
								}
								if(empty($cow->bbq_discount_val)){
									$cow->bbq_discount_val = "0";	
								}
								$customer_request = $cow;
							}
						}
					}
					$row->sales_order = $sales_order;
					$row->check_order = $check_order;
					$row->customer_request = $customer_request;
				}
				
				
				$data[] = $row;	
			}
			return $data;
		}
		return FALSE;
	}
	
	public function CONgetAllBillingitem($bil_id){
		
		// CAST(" . $this->db->dbprefix('bils') . ".total_tax AS DECIMAL(10,2)) as total_tax,CAST(" . $this->db->dbprefix('bils') . ".SUM(grand_total) AS DECIMAL(10,2)) as grand_total

		 $bill = "SELECT  GROUP_CONCAT(id) as id, GROUP_CONCAT(sales_id) as sales_id,  GROUP_CONCAT(bill_number) as bill_number,CAST((SUM(grand_total)) AS DECIMAL(10,2)) as grand_total  FROM " . $this->db->dbprefix('bils') . "  WHERE id IN  (".$bil_id.") ";
		
		$b = $this->db->query($bill);		
		
	
		if ($b->num_rows() > 0) {
			foreach ($b->result() as $row) {
				
				$bill_item = "SELECT  count(*) AS total_item  FROM " . $this->db->dbprefix('bil_items') . "  WHERE bil_id IN  (".$bil_id.") ";
				$c = $this->db->query($bill_item);	
				$row->total_item = $c->row('total_item');
				$row->rate = "0.000244";
				
				
				$ss = "SELECT sales_type_id FROM " . $this->db->dbprefix('sales') . " WHERE id IN (".$row->sales_id.") ";
				$s = $this->db->query($ss);	
				if ($s->num_rows() > 0) {
					foreach ($s->result() as $sow) {
						$check_order[] = $sow->sales_type_id;
					}
					$row->check_order = $check_order;
				}
				$data[] = $row;	
			}
			return $data;
		}
		return FALSE;
	}
	
	public function getAllBillingitem($bil_id = NULL){
		
		$default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
		$myQuery = "SELECT B.id,B.bill_number, B.sales_id,CAST((B.total_tax) AS DECIMAL(10,2)) as total_tax, count(*) AS total_item, C.rate,CAST((B.total-B.total_discount-B.bbq_daywise_discount+CASE WHEN (B.tax_type= 1) THEN B.total_tax ELSE 0 END) AS DECIMAL(10,2)) as grand_total
                    FROM " . $this->db->dbprefix('bils') . " B
                    JOIN " . $this->db->dbprefix('bil_items') . " BI ON BI.bil_id = B.id
                    JOIN " . $this->db->dbprefix('currencies') . " C ON C.code = '".$default_currency_data->code."' 
                        WHERE  B.id =".$bil_id."  AND B.bil_status IS NULL
                         GROUP BY B.id ";  
// echo $myQuery;die;
		$b = $this->db->query($myQuery);
		/*$this->db->select("bils.id, bils.bill_number, bils.sales_id,bils.total_tax, bils.grand_total as grand_total1,SUM(total-total_discount+CASE WHEN (' . $this->db->dbprefix('bils') . '.tax_type= 1) THEN total_tax ELSE 0 END) as grand_total, count(*) AS total_item, currencies.rate");
		$this->db->join('bil_items', 'bil_items.bil_id = bils.id', 'left');
		$this->db->join('currencies', 'currencies.code = "KHR" ');
		$this->db->where('bils.id', $bil_id);
		$this->db->where('bils.bil_status', NULL);
		$this->db->group_by('bils.id');*/
		// $b = $this->db->get('bils');
		// print_r($this->db->last_query());die;
		if ($b->num_rows() > 0) {
			foreach ($b->result() as $row) {
				$ss = "SELECT sales_type_id FROM " . $this->db->dbprefix('sales') . " WHERE id = ".$row->sales_id." ";
				$s = $this->db->query($ss);	
				if ($s->num_rows() > 0) {
					foreach ($s->result() as $sow) {
						$check_order[] = $sow->sales_type_id;
					}
					$row->check_order = $check_order;
				}
				$data[] = $row;	
			}
			return $data;
		}
		return FALSE;
	}
	
	
	public function DINEgetAllSalesWithbiller($warehouse_id){
		$current_date = date('Y-m-d');
		$this->db->select("sales.*, ,restaurant_tables.name as tablename,restaurant_areas.name as areaname,'bils'");
		$this->db->join("restaurant_tables", "restaurant_tables.id = sales.sales_table_id",'left');
		$this->db->join("restaurant_areas", "restaurant_areas.id = restaurant_tables.area_id",'left');
		$this->db->where('sales.sales_type_id', 1);	
		$this->db->where('sales.warehouse_id', $warehouse_id);
		$this->db->where('sales.sale_status', 'Process');
		$this->db->where('sales.cancel_status', 0);
		$this->db->where('sales.consolidated', 0);
		$this->db->where('DATE(date)', $current_date);
		$this->db->order_by('sales.id', 'DESC');
		$b = $this->db->get('sales');

		if ($b->num_rows() > 0) {
			foreach ($b->result() as $row) {
				$data[] = $row;	
			}
			return $data;
		}
		return FALSE;
	}
	
	public function BBQgetAllSalesWithbiller($warehouse_id){
		$current_date = date('Y-m-d');
		$this->db->select("sales.*, ,restaurant_tables.name as tablename,restaurant_areas.name as areaname,'bils'");
		$this->db->join("restaurant_tables", "restaurant_tables.id = sales.sales_table_id",'left');
		$this->db->join("restaurant_areas", "restaurant_areas.id = restaurant_tables.area_id",'left');
		$this->db->where('sales.sales_type_id', 4);	
		$this->db->where('sales.warehouse_id', $warehouse_id);
		$this->db->where('sales.sale_status', 'Process');		
		$this->db->where('sales.cancel_status', 0);
		$this->db->where('sales.consolidated', 0);
		$this->db->where('DATE(date)', $current_date);
		$this->db->group_by('sales.sales_split_id');
		$this->db->order_by('sales.id', 'DESC');
		$b = $this->db->get('sales');

		if ($b->num_rows() > 0) {
			foreach ($b->result() as $row) {
				$data[] = $row;	
			}
			return $data;
		}
		return FALSE;
	}
	
	public function CONgetAllSalesWithbiller($warehouse_id){
		
		$current_date = date('Y-m-d');
		$this->db->select("sales.*, ,restaurant_tables.name as tablename,restaurant_areas.name as areaname,'bils'");
		$this->db->join("restaurant_tables", "restaurant_tables.id = sales.sales_table_id",'left');
		$this->db->join("restaurant_areas", "restaurant_areas.id = restaurant_tables.area_id",'left');
		$this->db->where('sales.warehouse_id', $warehouse_id);
		$this->db->where('sales.sale_status', 'Process');
		$this->db->where('sales.payment_status', NULL);
		$this->db->where('sales.cancel_status', 0);
		$this->db->where('sales.consolidated', 1);
		
		
		$this->db->where('DATE(date)', $current_date);
		$this->db->group_by('sales.sales_split_id');
		$this->db->order_by('sales.id', 'DESC');
		$b = $this->db->get('sales');
		
		if ($b->num_rows() > 0) {
			foreach ($b->result() as $row) {
				$data[] = $row;	
			}
			return $data;
		}
		return FALSE;
	}
	
	public function getBilID($order_split_id){
		$billQuery = "SELECT  GROUP_CONCAT(id) as ids FROM " . $this->db->dbprefix('sales') . " 
 		 WHERE sales_split_id = '".$order_split_id."' ";
        $q = $this->db->query($billQuery);        
        if ($q->num_rows() > 0) {
			$b = $this->db->select('bils.id, bils.sales_id, bils.grand_total, sales.sales_type_id, sales.sales_split_id')->join('sales', 'sales.id = bils.sales_id')->where_in('bils.sales_id', explode(',', $q->row('ids')))->get('bils');
			if ($b->num_rows() > 0) {
				foreach (($b->result()) as $row) {
					$data[] = $row;
				}
			}
			return $data;	
		}
		return FALSE;
	}
	
	function getDineinCustomerDiscount($billid){
				
		$this->db
		->select('P.id bil_id, P.tax_type, P.tax_id, P.total, P.total_discount, P.grand_total, S.sales_split_id, CRD.id as customer_request_id, CRD.customer_discount_val, GD.discount_val, D.*')
		->from('bils P')
		->join('sales S', 'S.id = P.sales_id', 'left')
		->join('customer_request_discount CRD', 'CRD.split_id = S.sales_split_id', 'left')
		->join('diccounts_for_customer D','D.id=CRD.customer_discount_val','left')
		->join('group_discount GD', 'GD.cus_discount_id = D.id', 'left')
		->where('P.id',$billid)->group_by('D.id');
		
		$q = $this->db->get();
		
		if ($q->num_rows() > 0) {
			$result = $q->row();   
			 
            return $result;
		}
		return FALSE;
	}
	
	public function getDINEINBils($billid){
		$b = $this->db->select('*')->where('id', $billid)->get('bils');
		 if ($b->num_rows() > 0) {
            return $b->row();
        }
		return FALSE;
	}
	
	public function getDINEINBilitem($billid){
		$q = $this->db->select('*')->where('bil_id', $billid)->get('bil_items');
		if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
	}
	
	function getDINEINCUSDisIDBy($dis_id){
		$this->db->select('D.*, GD.discount_val')->from('diccounts_for_customer D')
		->join('group_discount GD', 'GD.cus_discount_id = D.id', 'left')
		->where('D.id',$dis_id)->group_by('D.id');
		
		$q = $this->db->get();
		
		if ($q->num_rows() > 0) {
			$result = $q->row();   
			 
            return $result;
		}
		return FALSE;
		
	}
	function DINEINupdate_bil($bils_update, $billid, $item_updates, $bilitem_ids, $request_array, $customer_request_id){
		
		if($this->db->update('bils', $bils_update, array('id' => $billid))){
			$this->db->update('customer_request_discount', $request_array, array('id' => $customer_request_id));
			$i=0;
			
			foreach($bilitem_ids as $ids){
				
				$this->db->update('bil_items', $item_updates[$i], array('id' => $ids['id']));
				$i++;	
			}
			
			return TRUE;	
		}
		return FALSE;
	}
	
	function getBBQDiscount($billid){
				
		$this->db
		->select('P.id bil_id, P.tax_type, P.tax_id, P.total, P.total_discount, P.grand_total, S.sales_split_id, CRD.id as customer_request_id, CRD.bbq_discount_val, D.*')
		->from('bils P')
		->join('sales S', 'S.id = P.sales_id', 'left')
		->join('customer_request_discount CRD', 'CRD.split_id = S.sales_split_id', 'left')
		->join('diccounts_for_bbq D','D.id=CRD.bbq_discount_val','left')
		
		->where('P.id',$billid)->group_by('D.id');
		
		$q = $this->db->get();
		
		if ($q->num_rows() > 0) {
			$result = $q->row();   
			 
            return $result;
		}
		return FALSE;
	}
	
	public function getBBQBils($billid){
		$b = $this->db->select('*')->where('id', $billid)->get('bils');
		 if ($b->num_rows() > 0) {
            return $b->row();
        }
		return FALSE;
	}
	
	public function getBBQBilitem($billid){
		$q = $this->db->select('*')->where('bil_id', $billid)->get('bil_items');
		if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
	}
	
	public function getBBQBilcover($billid){
		$q = $this->db->select('*')->where('bil_id', $billid)->get('bbq_bil_items');
		if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
	}
	
	function getBBQCUSDisIDBy($dis_id){
		$this->db->select('D.*')->from('diccounts_for_bbq D')
		->where('D.id',$dis_id)->group_by('D.id');
		
		$q = $this->db->get();
		
		if ($q->num_rows() > 0) {
			$result = $q->row();   
			 
            return $result;
		}
		return FALSE;
		
	}
	
	function BBQupdate_bil($bils_update, $billid, $item_updates, $bilitem_ids, $request_array, $customer_request_id){
		
		if($this->db->update('bils', $bils_update, array('id' => $billid))){
			$this->db->update('customer_request_discount', $request_array, array('id' => $customer_request_id));
			$i=0;
			
			foreach($bilitem_ids as $ids){
				
				$this->db->update('bil_items', $item_updates[$i], array('id' => $ids['id']));
				$i++;	
			}
			
			return TRUE;	
		}
		return FALSE;
	}
	
	
	public function getsalesID($order_split_id){
		$billQuery = "SELECT  GROUP_CONCAT(id) as ids, GROUP_CONCAT(sales_type_id) as sales_type_id FROM " . $this->db->dbprefix('sales') . " 
 		 WHERE sales_split_id = '".$order_split_id."' ";
        $q = $this->db->query($billQuery);        
        if ($q->num_rows() > 0) {
				
				$ids = explode(',', $q->row('ids'));
				$sales_type_id = explode(',', $q->row('sales_type_id'));
				foreach($ids as $key => $val){
					$data[] = array('id' => $ids[$key], 'sales_type_id' => $sales_type_id[$key]);
				}
				//$data[] = $d;
				
				
			return $data;	
		}
		return FALSE;
	}
    public function getBBQTableID($bill_id)
    {
        $table_name = "SELECT T.id

                    FROM ".$this->db->dbprefix('bils')." AS P
                    JOIN ". $this->db->dbprefix('sales') ." AS S ON S.id = P.sales_id
                    JOIN ". $this->db->dbprefix('orders') ." AS O ON O.split_id = S.sales_split_id
                    JOIN ". $this->db->dbprefix('sales_type') ." AS TY ON TY.id = O.order_type
                           
                    LEFT JOIN ". $this->db->dbprefix('restaurant_tables') ." AS T ON T.id = O.table_id
                    WHERE P.id='".$bill_id."' ";
            
        $q = $this->db->query($table_name);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data = $row->id;
            }
            return $data;
        }
        return FALSE;
    }
    public function getTableID($bill_id)
    {
        $table_name = "SELECT T.id

                    FROM ".$this->db->dbprefix('bils')." AS P
                    JOIN ". $this->db->dbprefix('sales') ." AS S ON S.id = P.sales_id
                    JOIN ". $this->db->dbprefix('orders') ." AS O ON O.split_id = S.sales_split_id
                    JOIN ". $this->db->dbprefix('sales_type') ." AS TY ON TY.id = O.order_type
                           
                    LEFT JOIN ". $this->db->dbprefix('restaurant_tables') ." AS T ON T.id = O.table_id
                    WHERE P.id='".$bill_id."' ";
            
        $q = $this->db->query($table_name);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data = $row->id;
            }
            return $data;
        }
        return FALSE;
    }
	
	public function getPaymentmethod(){
		$q = $this->db->select('*')->where('payment_type !=', 'loyalty')->get('payment_methods');
		if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
		return false;	
	}
/*loyalty 26-12-2018*/	
public function getcustomerbysaleid($sales_id){
    	
    	$this->db->select('customer_id');		
		$this->db->where('id', $sales_id);
		$q = $this->db->get('sales');
		if ($q->num_rows() == 1) {			
			return  $q->row('customer_id');			
		}		
		return FALSE;
	}

    function getLoyaltyCustomerByCardNo($phone_or_loyalty_card, $limit = 1){

    $this->db->select("C.id, (CASE WHEN C.company = '-' THEN C.name ELSE CONCAT(C.company, ' (', C.name, ')') END) as text", FALSE);
        $this->db->from('companies C');
    $this->db->join('loyalty_points LP','LP.customer_id=C.id','left');    
    $this->db->where(" (LP.loyalty_card_no LIKE '%" . $term . "%' OR  C.phone LIKE '%" . $term . "%') ");
    $this->db->where('LP.total_points >',0);
   // $this->db->where('LP.loyalty_card_no', $term) ;  
    $this->db->where('C.group_name', 'customer');    
    $this->db->limit($limit);
    $q = $this->db->get(); 
      // print_r($this->db->last_query());die;
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return '';
    }
    public function getLoyaltypointsBycustomer($phone_or_loyalty_card){
    		$current_date = date('Y-m-d');
    		$this->db->select('LP.id,LP.total_points,S.eligibity_point,LP.loyalty_card_no,LP.expiry_date,LP.customer_id')
		    ->from('loyalty_points LP')
		    ->join('loyalty_settings S', 'S.id = LP.loyalty_id') 			            
		    ->join('companies C', 'C.id = LP.customer_id') 
		    ->where(" (LP.loyalty_card_no LIKE '%" . $phone_or_loyalty_card . "%' OR  C.phone LIKE '%" . $phone_or_loyalty_card . "%') ")
		    ->where('"'.$current_date.'" BETWEEN DATE(S.from_date) and DATE(S.end_date)')            
		    ->where('S.status',1);    
		    // $this->db->where('LP.customer_id', $customer_id);
		    $r = $this->db->get();				    
		     // print_r($this->db->last_query());die;
		    if ($r->num_rows() > 0) {		    	 				 
                return $r->row();
            }
        return FALSE;		
	}
	
    public function LoyaltyRedemtiondetails($phone_or_loyalty_card){	

	   	$this->db->select('LR.id AS redemption_id,LR.points AS redempoint,LR.amount')
            ->from('loyalty_redemption LR')
            ->join('loyalty_settings S', 'S.id = LR.loyalty_id','left')             
            ->join('loyalty_points LP', 'S.id = LP.loyalty_id','left')  
            ->join('companies C', 'C.id = LP.customer_id')            
            ->where(" (LP.loyalty_card_no LIKE '%" . $phone_or_loyalty_card . "%' OR  C.phone LIKE '%" . $phone_or_loyalty_card . "%') ")
            //->where('LP.customer_id',$customer_id)              
            ->where('S.status',1);    
            $this->db->order_by('LR.id', 'ASC');
            $this->db->limit(1);
            $redem = $this->db->get();                 
             if ($redem->num_rows() > 0) {                      	
				$loyalty_id =  $redem->row('loyalty_id');
				$redempoint =  $redem->row('redempoint');
				$amount =  $redem->row('amount');
				$data = array(
						'redempoint' => $redempoint,						
						'amount' => $amount,
						//'customer_id' => $customer_id,
						);				
				  	 return $data;
			}				
			return 0;			
    }
    	public function LoyaltyRedemtion($customer_id, $points,$loyalty_card){	

	   	$this->db->select('LR.id AS redemption_id,LR.points AS redempoint,LR.amount')
            ->from('loyalty_redemption LR')
            ->join('loyalty_settings S', 'S.id = LR.loyalty_id')             
            ->where('LR.points >=',$points)              
            ->where('S.status',1);    
            $this->db->order_by('LR.id', 'ASC');
            $this->db->limit(1);
            $redem = $this->db->get();      
            // print_r($this->db->last_query());die;
             if ($redem->num_rows() > 0) { 
                     	
				$loyalty_id =  $redem->row('loyalty_id');
				$redempoint =  $redem->row('redempoint');
				$amount =  $redem->row('amount');
				$count = $points /$redempoint;
				$total_redemamount  = intval($count) * $amount;
				$data = array(
						// 'redempoint' => intval($count) * $redempoint,
						'total_redemamount' => $total_redemamount,							
						'redemption' => $redempoint,
						'amount' => $amount,
						//'customer_id' => $customer_id,
						);				
				  	 return $data;
			}				
			return 0;			
    }    

/*loyalty 26-12-2018*/

}

