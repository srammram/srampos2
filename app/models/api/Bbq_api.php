<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Bbq_api extends CI_Model
{

    public function __construct() {
        parent::__construct();
        $mydate=getdate(date("U"));
        $this->today = "$mydate[weekday]";
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
	
	public function returnUpdate_new($return_array, $returnitem_array, $table_id, $user_id, $warehouse_id){
		if($this->db->insert('sale_return', $return_array)){
			$sale_return_id = $this->db->insert_id();
			foreach($returnitem_array as $item){
				
				$item['sale_return_id'] = $sale_return_id;
				$this->db->insert('sale_return_item', $item);
			}
			
			return true;
		}
		return false;
	}
	
	public function Getreturnorders_new(){
		if($this->db->insert('sale_return', $return_array)){
			$sale_return_id = $this->db->insert_id();
			foreach($returnitem_array as $item){
				
				$item['sale_return_id'] = $sale_return_id;
				$this->db->insert('sale_return_item', $item);
			}
			
			return true;
		}
		return false;
	}
	
	public function GetAlldinein($table_id, $user_id, $warehouse_id){
		
		$current_date = date('Y-m-d');

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
		$this->db->where("orders.order_type", 4);
		$this->db->where('orders.payment_status', NULL);
		$this->db->where('restaurant_tables.warehouse_id', $warehouse_id);
		$this->db->group_by("restaurant_table_orders.table_id");
		
		$t = $this->db->get('restaurant_tables');		

		if ($t->num_rows() > 0) {
		
			foreach ($t->result() as $row) {
				
				$this->db->select("restaurant_table_sessions.split_id, orders.customer_id, restaurant_table_sessions.table_id ", FALSE)
				->join('orders', 'orders.split_id = restaurant_table_sessions.split_id AND orders.order_cancel_status = 0');
				$this->db->where('restaurant_table_sessions.table_id', $row->id);
				$this->db->where("orders.order_type", 4);
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

		$i = $this->db->query("SELECT a.reference_no, a.order_type, order_items.*, CASE WHEN recipe.image !='' THEN CONCAT('".$default_url."', recipe.image) ELSE '$default_image' END AS recipe_image FROM " . $this->db->dbprefix('orders') . "  AS a
		LEFT JOIN " . $this->db->dbprefix('order_items') . "  AS order_items ON order_items.sale_id = a.id 
		LEFT JOIN " . $this->db->dbprefix('recipe') . " AS recipe ON recipe.id = order_items.recipe_id 
		WHERE a.split_id = '".$split."' AND a.order_type = ".$order_type." AND  DATE(date) = '".$current_date."' ");
		
		if($i->num_rows() > 0){
							
			foreach($i->result() as $item){
				$data[] = $item;
			}
			return $data;
		}
		
		return FALSE;	
	}
	
	public function getBBQAllSalesWithbiller($order_type, $warehouse_id){
		$current_date = date('Y-m-d');
		$this->db->select("bbq_sales.*, ,restaurant_tables.name as tablename,restaurant_areas.name as areaname,'bils'");
		$this->db->join("restaurant_tables", "restaurant_tables.id = bbq_sales.sales_table_id",'left');
		$this->db->join("restaurant_areas", "restaurant_areas.id = restaurant_tables.area_id",'left');
		if(!empty($order_type)){
			$this->db->where('bbq_sales.sales_type_id', $order_type);	
		}
		
		$this->db->where('bbq_sales.warehouse_id', $warehouse_id);
		$this->db->where('bbq_sales.sale_status', 'Process');
		$this->db->where('bbq_sales.cancel_status', 0);
		$this->db->where('DATE(date)', $current_date);

		$s = $this->db->get('bbq_sales');
		
		if ($s->num_rows() > 0) {
		
            foreach ($s->result() as $row) {
				$data[] = $row;	
			}
			return $data;
		}

		return FALSE;
	}
	
	public function getBBQSaleIDWithBils($sales_id, $warehouse_id){
		
		$this->db->select("bbq_bils.*,companies.credit_limit,companies.name customer_name,companies.customer_type,companies.id company_id,companies.credit_limit");
		$this->db->join("companies", "companies.id = bbq_bils.customer_id",'left');
		$this->db->where('bbq_bils.sales_id', $sales_id);
		$q = $this->db->get('bbq_bils');
		if ($q->num_rows() > 0) {
		
            foreach ($q->result() as $row) {
				$data[] = $row;	
			}
			return $data;
		}
		return FALSE;
	}
	
	public function getBilvalue($bil_id){
		$this->db->select("bbq_bils.*");
		$this->db->where('bbq_bils.id', $bil_id);
		$b = $this->db->get('bbq_bils');
		if ($b->num_rows() > 0) {
			
			return $b->row();	
		}
		return FALSE;
	}
	
	public function BBQPayment($update_bill = array(), $bill_id =NULL, $payment = array(), $multi_currency = array(), $salesid = NULL, $sales_bill = array(), $order_split_id = NULL, $updateCreditLimit)
    {      
	
		$q = $this->db->select('sales_split_id, sales_table_id')->where('id', $salesid)->get('bbq_sales');

		$bilno = $this->db->select('reference_no')->where('id', $bill_id)->get('bils');
		
		$bill_no = $bilno->row('reference_no');

		if ($q->num_rows() > 0) {
            $split_id =  $q->row('sales_split_id');
			$table_id =  $q->row('sales_table_id');
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

		
    	if ($this->db->update('bbq_bils', $update_bill, array('id' => $bill_id))){
			$this->db->update('bbq_sales', $sales_bill, array('id' => $salesid));
    			$order_count = $this->db->get_where('bbq_bils', array('sales_id' => $salesid));
				$order_count =$order_count->num_rows();
				 $order_closed_count = $this->db->get_where('bbq_bils', array('bbq_bils.sales_id' => $salesid,'bbq_bils.payment_status' => 'Completed'));
				 $order_closed_count =$order_closed_count->num_rows();

    		foreach ($payment as $item) {
		    $item['customer_payment_type'] = $updateCreditLimit['customer_type'];
		    $this->db->insert('bbq_payments', $item);
			
		    $pid = $this->db->insert_id();
			
		    if($pid && $item['paid_by']=='credit'){/*
			$creditedAmt = $item['pos_paid'];
			$d_q = $this->db->get_where('deposits', array('company_id' => $updateCreditLimit['company_id'],'credit_balance!='=>0))->result_array();
			$amountpayable = $item['pos_paid'];
			foreach($d_q as $dep => $depositRow){			    
			    if($amountpayable<=$depositRow['credit_balance']){
				$payableamt = $amountpayable;
				$this->db->set('credit_balance', 'credit_balance-'.$payableamt,false);
				$this->db->set('credit_used', 'credit_used+'.$payableamt,false);
				$this->db->where('id',$depositRow['id']);
				$this->db->update('deposits');//echo 'exit';exit;
				$amountpayable =0;
				break;
			    }else{
				$payableamt = $depositRow['credit_balance'];
				$this->db->set('credit_balance', 'credit_balance-'.$payableamt,false);
				$this->db->set('credit_used', 'credit_used+'.$payableamt,false);
				$this->db->where('id',$depositRow['id']);
				$this->db->update('deposits');
				$amountpayable = $amountpayable-$payableamt;
				
			    }
			}
			if($updateCreditLimit['customer_type']=="postpaid") {
			    if($amountpayable>0){
				$date = date('Y-m-d H:i:s');
				$deposit_data = array(
				    'date' => $date,
				    'credit_amount' => $amountpayable,
				    'credit_used' => $amountpayable,
				    'paid_by' => 'postpaid',
				    'company_id' => $updateCreditLimit['company_id'],
				    'created_by' => $this->session->userdata('user_id'),
				    'added_on' => date('Y-m-d H:i:s'),
				);
				if ($this->db->insert('deposits', $deposit_data)) {
				    $this->db->set('credit_limit', 'credit_limit+'.$deposit_data['credit_amount'],false);
					$this->db->where('id',$deposit_data['company_id']);
					$this->db->update('companies');
				}
			    }
			    $com = $this->db->get_where('companies', array('id' => $updateCreditLimit['company_id']))->row_array();
			    $postpaid_bill['company_id'] = $updateCreditLimit['company_id'];
			    $postpaid_bill['credit_amount'] = $creditedAmt;
			    $postpaid_bill['amount_payable'] = $creditedAmt;
			    $postpaid_bill['bill_id'] = $bill_id;
			    $postpaid_bill['created_on'] = date('Y-m-d H:i:s');
			    $postpaid_bill['due_date'] = date('Y-m-d H:i:s',strtotime('+'.$com['credit_days'].' days', strtotime(date('Y-m-d H:i:s'))));		 $postpaid_bill['status'] = 9;
			    $this->db->insert('companies_postpaid_bills', $postpaid_bill);
			    $this->db->insert_id();
			}
			$this->db->set('credit_limit', 'credit_limit-'.$creditedAmt,false);
			$this->db->where('id',$updateCreditLimit['company_id']);
			$this->db->update('companies');//echo 'exit';exit;
       
		    */}
    		
    		}
			foreach ($multi_currency as $currency) {
    			$this->db->insert('bbq_sale_currency', $currency);
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
					$this->db->update('bbq_sales', $sales_array, array('id' => $salesid));
					$this->db->update('orders', $sales_array, array('split_id' =>  $order_split_id));
					$this->db->update('bbq', $bbq_array, array('reference_no' => $split_id, 'table_id' => $table_id));
					$res =  $this->db->update('restaurant_table_sessions', $tables_array, array('split_id' =>  $order_split_id));
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
	
	public function BBQaddSale_new($sale, $sale_items, $bilsdata, $bil_items, $order_id, $split_id, $grand_total){
		
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
	
	public function BBQaddSale($sale, $sale_items, $bilsdata, $bil_items, $order_id, $split_id, $grand_total){
		
		
		if($this->db->insert('bbq_sales', $sale)){
			$sale_id = $this->db->insert_id();
            $this->db->update('bbq_sales', $sales_array, array('id' => $sale_id));
			
			foreach($bilsdata as $key => $bilsrow){
				echo $key;
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
			
			return TRUE;
			
		}
		return FALSE;
		
	}
	
	public function BBQaddSaleManul($sale, $sale_items, $bilsdata, $bil_items, $order_id, $bbq_array, $splits){
		
		if($this->db->insert('bbq_sales', $sale)){
			$sale_id = $this->db->insert_id();
            $this->db->update('bbq_sales', $sales_array, array('id' => $sale_id));
			
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
			
			if(!empty($bbq_array)){
				$this->db->where_in('reference_no', $bbq_array);
				$this->db->update('bbq',  $bbq_array);
			}
			return TRUE;
			
		}
		return FALSE;
	}
	
	
	
	public function BBQtablesplitone($table_id, $split_id){
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
	
	
	public function GetuserByID($user_number){
		
		$query = $this->db->select('*')
            ->where('user_number', $user_number)
            ->limit(1)
            ->get('users');
			
		if ($query->num_rows() === 1) {
            $user = $query->row();
			$ldata = array('user_id' => $user->id, 'ip_address' => $user->ip_address, 'login' => $user->id);
			$ldata['group_id'] = $user->group_id;
			$this->db->insert('user_logins', $ldata);
			$data = $user;
			return $data;
        }
		return FALSE;
	}
	
	public function GetRecipedetails($recipe_id){
		$default_image = site_url('assets/uploads/no_image.png');
		$default_url = site_url('assets/uploads/');
		$default_thumb_url = site_url('assets/uploads/thumbs/');
		
		$q = $this->db->query("SELECT r.id, r.code, r.recipe_details, r.type,  r.name, CASE WHEN r.khmer_name !='' THEN  r.khmer_name ELSE r.name END AS khmer_name, r.price, r.slug, r.category_id, c.name AS category_name, r.subcategory_id, s.name AS subcategory_name, r.kitchens_id, CASE WHEN r.image !='' THEN CONCAT('".$default_url."', r.image) ELSE '$default_image' END AS image, CASE WHEN r.image !='' THEN CONCAT('".$default_thumb_url."', r.image) ELSE '$default_image' END AS thumbnail FROM " . $this->db->dbprefix('recipe') . " AS r
		LEFT JOIN ".$this->db->dbprefix('recipe_categories')." AS c ON c.id = r.category_id
		LEFT JOIN ".$this->db->dbprefix('recipe_categories')." AS s ON s.id = r.subcategory_id
		WHERE r.id = ".$recipe_id." ");
		
		
		 if ($q->num_rows() == 1) {
			
			
			
			$data = $q->row();
			
			$this->db->select('recipe_addon.*, recipe.name, recipe.price');
			$this->db->join('recipe', 'recipe.id = recipe_addon.recipe_id');
			$this->db->where('recipe_addon.recipe_id', $recipe_id);
			
			$addon_query = $this->db->get('recipe_addon');
			
			if($addon_query->num_rows() > 0){
				foreach ($addon_query->result() as $addon_row) {
					$add[$recipe_id][] = $addon_row;
				}
				$data->addon_list = $add[$recipe_id];
			}else{
				$data->addon_list = array();	
			}
		
			
            return $data;
        }
	
		
		
		return FALSE;
	}
	
	public function getPrinterByID($id) {
        $q = $this->db->get_where('printers', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
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
		
		$r =$this->db->select('*')->where('role_id', $group_id)->where('to_user_id', 0)->where('warehouse_id', $warehouse_id)->where('is_read', 0)->where('DATE(created_on)', $current_date)->get('notiy');
		if ($r->num_rows() > 0) {
			foreach($r->result() as $row){
				$group[] = $row;
			}
		}
		
		if(!empty($user) && empty($group)){
			$data['notification_list'] = $user;
		}elseif(empty($user) && !empty($group)){
			$data['notification_list'] = $group;
		}elseif(!empty($user) && !empty($group)){
			$data['notification_list'] = array_merge($user, $group);
		}

		$data['notification_count'] = count($data['notification_list']);
		
		if(!empty($data['notification_list'])){
			
			return $data;
		}else{
			return false;
		}
				
		
	}
	public function GetAllmaincategory_withdays($order_type){

		$default_image = site_url('assets/uploads/no_image.png');
		$default_url = site_url('assets/uploads/');
		$default_thumb_url = site_url('assets/uploads/thumbs/');
		$today =$this->today; 		

		$order_by ='ORDER BY RC.name';		
		if($this->getPOSSettings->categories_list_by ==0) {
			$order_by ='ORDER BY RC.id';
		}	

		$query = $this->db->query("SELECT RC.id, RC.name, RC.khmer_name, CASE WHEN RC.image !='' THEN CONCAT('".$default_url."', RC.image) ELSE '$default_image' END AS image, CASE WHEN RC.image !='' THEN CONCAT('".$default_thumb_url."', RC.image) ELSE '$default_image' END AS thumbnail  FROM " . $this->db->dbprefix('recipe_categories') . "  AS RC
			JOIN " . $this->db->dbprefix('sale_items_mapping_details') . " AS IMDT ON IMDT.recipe_group_id = RC.id 
			JOIN " . $this->db->dbprefix('sale_items_mapping_head') . " AS IMHD ON IMHD.id = IMDT.sales_map_hd_id 
			WHERE RC.parent_id is NULL or RC.parent_id = 0 AND IMHD.days= '".$today."' AND  IMHD.sale_type= ".$order_type." AND RC.status =1 GROUP BY RC.id ".$order_by." ");		
			
        	if ($query->num_rows() > 0) {
			$all = array('id' => "0", 'name' => 'ALL', 'khmer_name' => 'ទាំងអស់','image' => $default_image,'thumbnail' => $default_image);
			$data[] = $all;
           foreach ($query->result() as $row) {
			   if(!empty($row->khmer_name)){
				   $row->khmer_name = $row->khmer_name;
			   }else{
				   $row->khmer_name = $row->name;
			   }
                $data[] = $row;
            }

            return $data;
        }
        return FALSE;
	}

	public function GetAllmaincategory($order_type){

		$default_image = site_url('assets/uploads/no_image.png');
		$default_url = site_url('assets/uploads/');
		$default_thumb_url = site_url('assets/uploads/thumbs/');

		$order_by ='ORDER BY RC.name';
		if($this->pos_settings->categories_list_by ==0) {
			$order_by ='ORDER BY RC.id';
		}

		$query = $this->db->query("SELECT RC.id, RC.name, RC.khmer_name, CASE WHEN RC.image !='' THEN CONCAT('".$default_url."', RC.image) ELSE '$default_image' END AS image, CASE WHEN RC.image !='' THEN CONCAT('".$default_thumb_url."', RC.image) ELSE '$default_image' END AS thumbnail  FROM " . $this->db->dbprefix('recipe_categories') . "  AS RC
			WHERE RC.parent_id is NULL or RC.parent_id = 0 AND RC.status=1 GROUP BY RC.id ".$order_by." ");		
        	if ($query->num_rows() > 0) {
			$all = array('id' => "0", 'name' => 'ALL', 'khmer_name' => 'ទាំងអស់','image' => $default_image,'thumbnail' => $default_image);
			$data[] = $all;
           foreach ($query->result() as $row) {
			   if(!empty($row->khmer_name)){
				   $row->khmer_name = $row->khmer_name;
			   }else{
				   $row->khmer_name = $row->name;
			   }
                $data[] = $row;
            }

            return $data;
        }
        return FALSE;
	}

/*	public function GetAllmaincategory(){
		
		$this->db->select('id, name, khmer_name');
		 $this->db->where('parent_id', NULL)->or_where('parent_id', 0)->order_by('id');
        $query = $this->db->get("recipe_categories");
        if ($query->num_rows() > 0) {
			$all = array('id' => "0", 'name' => 'ALL', 'khmer_name' => 'ទាំងអស់');
			$data[] = $all;
           foreach ($query->result() as $row) {
			   if(!empty($row->khmer_name)){
				   $row->khmer_name = $row->khmer_name;
			   }else{
				   $row->khmer_name = $row->name;
			   }
                $data[] = $row;
            }
			
			
            return $data;
        }
        return FALSE;
	}*/
	public function GetAllsubcategory_withdays($category_id,$order_type){
		$default_image = site_url('assets/uploads/no_image.png');
		$default_url = site_url('assets/uploads/');
		$default_thumb_url = site_url('assets/uploads/thumbs/');
		$today =$this->today; 

		if($category_id == 0){
			$where = " WHERE parent_id != ".$category_id."";
			
		}else{
			$where = " WHERE parent_id = ".$category_id."";
		}
		$query = $this->db->query("SELECT RC.id, RC.name, RC.khmer_name, CASE WHEN RC.image !='' THEN CONCAT('".$default_url."', RC.image) ELSE '$default_image' END AS image, CASE WHEN RC.image !='' THEN CONCAT('".$default_thumb_url."', RC.image) ELSE '$default_image' END AS thumbnail  FROM 
			" . $this->db->dbprefix('recipe_categories') . " AS RC
			JOIN " . $this->db->dbprefix('sale_items_mapping_details') . " AS IMDT ON IMDT.recipe_subgroup_id = RC.id 
			JOIN " . $this->db->dbprefix('sale_items_mapping_head') . " AS IMHD ON IMHD.id = IMDT.sales_map_hd_id 
			  ".$where."  AND IMHD.days= '".$today."' AND  IMHD.sale_type= ".$order_type." AND RC.status=1 ");
        if ($query->num_rows() > 0) {
           foreach ($query->result() as $row) {
			   if(!empty($row->khmer_name)){
				   $row->khmer_name = $row->khmer_name;
			   }else{
				   $row->khmer_name = $row->name;
			   }
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
	}

	public function GetAllsubcategory($category_id,$order_type){
		$default_image = site_url('assets/uploads/no_image.png');
		$default_url = site_url('assets/uploads/');
		$default_thumb_url = site_url('assets/uploads/thumbs/');
		$today =$this->today; 

		if($category_id == 0){
			$where = " WHERE parent_id != ".$category_id." AND status=1";
			
		}else{
			$where = " WHERE parent_id = ".$category_id." AND status=1";
		}
		$query = $this->db->query("SELECT RC.id, RC.name, RC.khmer_name, CASE WHEN RC.image !='' THEN CONCAT('".$default_url."', RC.image) ELSE '$default_image' END AS image, CASE WHEN RC.image !='' THEN CONCAT('".$default_thumb_url."', RC.image) ELSE '$default_image' END AS thumbnail  FROM 
			" . $this->db->dbprefix('recipe_categories') . " AS RC
			".$where." ");
		// print_r($this->db->last_query());die;
        if ($query->num_rows() > 0) {
           foreach ($query->result() as $row) {
			   if(!empty($row->khmer_name)){
				   $row->khmer_name = $row->khmer_name;
			   }else{
				   $row->khmer_name = $row->name;
			   }
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
	}	
	/*public function GetAllsubcategory($category_id){
		$default_image = site_url('assets/uploads/no_image.png');
		$default_url = site_url('assets/uploads/');
		$default_thumb_url = site_url('assets/uploads/thumbs/');
		
		if($category_id == 0){
			$where = " WHERE parent_id != ".$category_id."";
			
		}else{
			$where = " WHERE parent_id = ".$category_id."";
		}
		$query = $this->db->query("SELECT id, name, khmer_name, CASE WHEN image !='' THEN CONCAT('".$default_url."', image) ELSE '$default_image' END AS image, CASE WHEN image !='' THEN CONCAT('".$default_thumb_url."', image) ELSE '$default_image' END AS thumbnail  FROM " . $this->db->dbprefix('recipe_categories') . " ".$where."  ");

        if ($query->num_rows() > 0) {
           foreach ($query->result() as $row) {
	    //// variants ///
			$this->db->select('recipe_variants.id as variant_id,recipe_variants.name,recipe_variants_values.price');
			$this->db->join('recipe_variants', 'recipe_variants.id=recipe_variants_values.attr_id');
			$this->db->where('recipe_variants_values.recipe_id',$row->id);
			$variant_query = $this->db->get('recipe_variants_values');
			$row->variants = array();
			if($variant_query->num_rows()>0){
			    $row->variants = $variant_query->result();
			}
			   if(!empty($row->khmer_name)){
				   $row->khmer_name = $row->khmer_name;
			   }else{
				   $row->khmer_name = $row->name;
			   }
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
	}*/
	
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
	
	public function getSettings(){
		$this->db->select('customer_discount, default_currency, bbq_enable, bbq_adult_price, bbq_child_price, bbq_kids_price');
		$q = $this->db->get('settings');
		if ($q->num_rows() == 1) {
			$data = $q->row();
			
			return $data;
		}
		
		return FALSE;
	}
	
	public function getPOSSettings(){
		$this->db->select('default_tax, tax_type');
		$q = $this->db->get('pos_settings');
		if ($q->num_rows() == 1) {
			$data = $q->row();
			return $data;
		}
		return FALSE;
	}
	
	public function BBQGetAlltables($area_id, $warehouse_id, $user_id){
	    $current_date = date('Y-m-d');
		$this->db->select('r.id, r.name, r.area_id, r.warehouse_id,b.created_by,b.confirmed_by,(number_of_adult+number_of_child+number_of_kids) as bbqcovers,b.status as bbq_status,b.reference_no as bbq_code,r.sale_type as type');
		$this->db->from('restaurant_tables r');
		$this->db->join('bbq b','r.id=b.table_id AND (b.status="open" OR b.status="waiting") AND b.created_on="'.$current_date.'"','left');
		$this->db->where(array('r.area_id' => $area_id, 'r.warehouse_id' => $warehouse_id ,'r.sale_type' => 'bbq'));
		$this->db->group_by('r.id');
		//echo $this->db->get_compiled_select();
		$query = $this->db->get();		
		if ($query->num_rows() > 0) {
			 foreach ($query->result() as $row) {
			    $bbq_status =false;
			    if($row->bbq_status=="waiting"){
			      $bbq_status = 'waiting';
			    }
			    if(strtolower($row->bbq_status)=="open"){
			      $bbq_status = ($user_id==$row->confirmed_by)?'ongoing':'ongoingothers';
			    }
			    $table_status = ($bbq_status)?$bbq_status:$this->site->orderBBQTablecheckapi($row->id, $user_id);
               	 $row->status = $table_status;
		 
		 $row->bbqcovers = ($row->bbqcovers==null)?"0":$row->bbqcovers;
		 $row->bbq_code = ($row->bbq_code==null)?"0":$row->bbq_code;
				 $data[] = $row;
			 }
			  return $data;
		}
		return FALSE;
	}
	
	public function getPOSSettingsALL(){
		$this->db->select('*');
		$q = $this->db->get('pos_settings');
		if ($q->num_rows() == 1) {
			$data = $q->row();
			return $data;
		}
		return FALSE;
	}
	
	
	public function getBBQdataCode($bbq_code){
		$this->db->select('bbq.*, restaurant_tables.name as table_name');
		$this->db->join('restaurant_tables', 'restaurant_tables.id = bbq.table_id');
		$this->db->where('bbq.reference_no', $bbq_code);
		$q = $this->db->get('bbq');
        if ($q->num_rows() == 1) {
			
            return $q->row();
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
	
	public function updateBBQ($data, $reference_no){
		$this->db->where('reference_no', $reference_no);
        if ($this->db->update('bbq', $data)) {
            return true;
        }
        return false;
	}

	public function GetAllrecipe_withdays($bbq_set_id, $warehouse_id,$order_type){
		
		$this->db->where('bbq_categories.id', $bbq_set_id);
		$q = $this->db->get('bbq_categories');
        if ($q->num_rows() > 0) {
            $bbq_items =  $q->row('items');
        }
		
		$default_image = site_url('assets/uploads/no_image.png');
		$default_url = site_url('assets/uploads/');
		$default_thumb_url = site_url('assets/uploads/thumbs/');
		$today =$this->today; 
		$where_in = ("'standard'".','."'production'".','."'quick_service'".','."'combo'");
		$query = $this->db->query("SELECT r.id, r.code, r.type,  r.name, r.khmer_name, r.price, r.currency_type, r.slug, r.category_id, r.subcategory_id, r.kitchens_id, CASE WHEN r.image !='' THEN CONCAT('".$default_url."', r.image) ELSE '$default_image' END AS image, CASE WHEN r.image !='' THEN CONCAT('".$default_thumb_url."', r.image) ELSE '$default_image' END AS thumbnail, w.warehouse_id, 'addon_list'  FROM " . $this->db->dbprefix('recipe') . " AS r 
			LEFT JOIN " . $this->db->dbprefix('warehouses_recipe') . " AS w ON w.recipe_id = r.id 
			JOIN " . $this->db->dbprefix('recipe_categories') . " AS RC ON RC.id = r.category_id 
			JOIN " . $this->db->dbprefix('sale_items_mapping_details') . " AS IMDT ON IMDT.recipe_subgroup_id = r.subcategory_id
			JOIN " . $this->db->dbprefix('sale_items_mapping_head') . " AS IMHD ON IMHD.id = IMDT.sales_map_hd_id 
			 WHERE r.recipe_standard != 2 AND r.subcategory_id = ".$subcategory_id." AND r.active = 1 AND w.warehouse_id = ".$warehouse_id."  AND IMHD.days= '".$today."' AND  IMHD.sale_type= ".$order_type." AND FIND_IN_SET(r.id,IMDT.recipe_id) !=0 AND r.type in (".$where_in.") order by RC.id asc");

        if ($query->num_rows() > 0) {
           foreach ($query->result() as $row) {
			   
			    if(!empty($row->khmer_name)){
				   $row->khmer_name = $row->khmer_name;
			   }else{
				   $row->khmer_name = $row->name;
			   }
			   
			  $row->comment_active = 0;
			   
			   $this->db->select('recipe_addon.*, recipe.name AS addon, recipe.price');
			   $this->db->join('recipe', 'recipe.id = recipe_addon.addon_id');
			   $this->db->where('recipe_addon.recipe_id', $row->id);			   
			   $this->db->group_by('recipe_addon.recipe_id');			   
			    $addon_query = $this->db->get('recipe_addon');
			    
				if($addon_query->num_rows() > 0){
					foreach ($addon_query->result() as $addon_row) {
						$add[$row->id][] = $addon_row;
					}
					$row->addon_list = $add[$row->id];
				}else{
					$row->addon_list = array();	
				}
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
	}

	
	public function GetAllrecipe($bbq_set_id, $warehouse_id,$order_type){
		
		$this->db->where('bbq_categories.id', $bbq_set_id);
		$q = $this->db->get('bbq_categories');
        if ($q->num_rows() > 0) {
            $bbq_items =  $q->row('items');
        }
		$default_image = site_url('assets/uploads/no_image.png');
		$default_url = site_url('assets/uploads/');
		$default_thumb_url = site_url('assets/uploads/thumbs/');
		
		$where_in = ("'standard'".','."'production'".','."'quick_service'".','."'combo'");
		$query = $this->db->query("SELECT r.id, r.code, r.type,  r.name, r.khmer_name, r.price, r.currency_type, r.slug, r.category_id, r.subcategory_id, r.kitchens_id, CASE WHEN r.image !='' THEN CONCAT('".$default_url."', r.image) ELSE '$default_image' END AS image, CASE WHEN r.image !='' THEN CONCAT('".$default_thumb_url."', r.image) ELSE '$default_image' END AS thumbnail, w.warehouse_id, 'addon_list'  FROM " . $this->db->dbprefix('recipe') . " AS r 
			LEFT JOIN " . $this->db->dbprefix('warehouses_recipe') . " AS w ON w.recipe_id = r.id 
			JOIN " . $this->db->dbprefix('recipe_categories') . " AS RC ON RC.id = r.category_id 			
			 WHERE r.recipe_standard != 2 AND r.subcategory_id = ".$subcategory_id." AND r.active = 1 AND w.warehouse_id = ".$warehouse_id." AND r.type in (".$where_in.")order by RC.id asc");

        if ($query->num_rows() > 0) {
           foreach ($query->result() as $row) {
			   
			    if(!empty($row->khmer_name)){
				   $row->khmer_name = $row->khmer_name;
			   }else{
				   $row->khmer_name = $row->name;
			   }
			   
			  $row->comment_active = 0;
			   
			   $this->db->select('recipe_addon.*, recipe.name AS addon, recipe.price');
			   $this->db->join('recipe', 'recipe.id = recipe_addon.addon_id');
			   $this->db->where('recipe_addon.recipe_id', $row->id);			   
			   $this->db->group_by('recipe_addon.recipe_id');			   
			    $addon_query = $this->db->get('recipe_addon');
			    
				if($addon_query->num_rows() > 0){
					foreach ($addon_query->result() as $addon_row) {
						$add[$row->id][] = $addon_row;
					}
					$row->addon_list = $add[$row->id];
				}else{
					$row->addon_list = array();	
				}
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
	}

	public function GetAllbbqrecipe($subcategory_id, $warehouse_id,$order_type){

		$default_image = site_url('assets/uploads/no_image.png');
		$default_url = site_url('assets/uploads/');
		$default_thumb_url = site_url('assets/uploads/thumbs/');
		$today =$this->today; 
		$where_in = ("'standard'".','."'production'".','."'quick_service'".','."'combo'");
		$query = $this->db->query("SELECT r.id, r.code, r.type, r.active, r.name, r.khmer_name, r.price, r.currency_type, r.slug, r.category_id, r.subcategory_id, r.kitchens_id, CASE WHEN r.image !='' THEN CONCAT('".$default_url."', r.image) ELSE '$default_image' END AS image, CASE WHEN r.image !='' THEN CONCAT('".$default_thumb_url."', r.image) ELSE '$default_image' END AS thumbnail, w.warehouse_id, 'addon_list',r.active  FROM " . $this->db->dbprefix('recipe') . " AS r 
			LEFT JOIN " . $this->db->dbprefix('warehouses_recipe') . " AS w ON w.recipe_id = r.id 
			JOIN " . $this->db->dbprefix('recipe_categories') . " AS RC ON RC.id = r.category_id 
			
			 WHERE r.recipe_standard != 2 AND r.subcategory_id = ".$subcategory_id." AND r.active in (1,2) AND w.warehouse_id = ".//$warehouse_id."  AND r.type in (".$where_in.") order by RC.id asc");
			 $warehouse_id."  AND r.type in (".$where_in.") order by r.name asc");


        if ($query->num_rows() > 0) {
           foreach ($query->result() as $row) {
			   
			    if(!empty($row->khmer_name)){
				   $row->khmer_name = $row->khmer_name;
			   }else{
				   $row->khmer_name = $row->name;
			   }
			   
			  $row->comment_active = 0;
			    ($row->active ==2)?$row->non_transaction=1:$row->non_transaction=0;
			   /*$this->db->select("recipe_addon_details.*, recipe.name AS addon, recipe.price")
			      	->join('recipe_addon_details', 'recipe_addon_details.addon_head_id = recipe_addon.id')
			      	->join('recipe', 'recipe.id = recipe_addon_details.addon_item_id','left')
			      	->where('recipe_addon.recipe_id', $row->id);      
			   $this->db->group_by('recipe_addon.recipe_id');			   
			    $addon_query = $this->db->get('recipe_addon');
			    
				if($addon_query->num_rows() > 0){
					foreach ($addon_query->result() as $addon_row) {
						$add[$row->id][] = $addon_row;
					}
					$row->addon = $add[$row->id];
				}else{
					$row->addon= array();	
				}*/
				$row->addon =0;
				$row->recipe_variant_id =0;
				$row->variants =0;
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
	}	
	
	/*public function GetAllbbqrecipe_withdays($subcategory_id, $warehouse_id,$order_type){
		$default_image = site_url('assets/uploads/no_image.png');
		$default_url = site_url('assets/uploads/');
		$default_thumb_url = site_url('assets/uploads/thumbs/');
		$today =$this->today; 
		$where_in = ("'standard'".','."'production'".','."'quick_service'".','."'combo'");
		$query = $this->db->query("SELECT r.id, r.code, r.type,  r.name, r.khmer_name, r.price, r.currency_type, r.slug, r.category_id, r.subcategory_id, r.kitchens_id, CASE WHEN r.image !='' THEN CONCAT('".$default_url."', r.image) ELSE '$default_image' END AS image, CASE WHEN r.image !='' THEN CONCAT('".$default_thumb_url."', r.image) ELSE '$default_image' END AS thumbnail, w.warehouse_id, 'addon_list'  FROM " . $this->db->dbprefix('recipe') . " AS r 
			LEFT JOIN " . $this->db->dbprefix('warehouses_recipe') . " AS w ON w.recipe_id = r.id 
			JOIN " . $this->db->dbprefix('recipe_categories') . " AS RC ON RC.id = r.category_id 
			 WHERE r.recipe_standard != 2 AND r.subcategory_id = ".$subcategory_id." AND r.active = 1 AND w.warehouse_id = ".$warehouse_id." AND r.type in (".$where_in.") order by RC.id asc");
        if ($query->num_rows() > 0) {
           foreach ($query->result() as $row) {			   
			    if(!empty($row->khmer_name)){
				   $row->khmer_name = $row->khmer_name;
			   }else{
				   $row->khmer_name = $row->name;
			   }			   
			  $row->comment_active = 0;
				$row->addon =0;
				$row->recipe_variant_id =0;
				$row->variants =0;
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
	}*/	

	public function GetAllbbqrecipe_withdays($subcategory_id, $warehouse_id,$order_type){
		$default_image = site_url('assets/uploads/no_image.png');
		$default_url = site_url('assets/uploads/');
		$default_thumb_url = site_url('assets/uploads/thumbs/');
		$today =$this->today; 
		$where_in = ("'standard'".','."'production'".','."'quick_service'".','."'combo'");
		$query = $this->db->query("SELECT r.id, r.code, r.type, r.active, r.name, r.khmer_name, r.price, r.currency_type, r.slug, r.category_id, r.subcategory_id, r.kitchens_id, CASE WHEN r.image !='' THEN CONCAT('".$default_url."', r.image) ELSE '$default_image' END AS image, CASE WHEN r.image !='' THEN CONCAT('".$default_thumb_url."', r.image) ELSE '$default_image' END AS thumbnail, w.warehouse_id  FROM " . $this->db->dbprefix('recipe') . " AS r 
			LEFT JOIN " . $this->db->dbprefix('warehouses_recipe') . " AS w ON w.recipe_id = r.id 
			JOIN " . $this->db->dbprefix('recipe_categories') . " AS RC ON RC.id = r.category_id 
			JOIN " . $this->db->dbprefix('sale_items_mapping_details') . " AS IMDT ON IMDT.recipe_subgroup_id = r.subcategory_id
			JOIN " . $this->db->dbprefix('sale_items_mapping_head') . " AS IMHD ON IMHD.id = IMDT.sales_map_hd_id 
			 WHERE r.recipe_standard != 1 AND r.subcategory_id = ".$subcategory_id." AND r.active in (1,2) AND w.warehouse_id = ".$warehouse_id."  AND IMHD.days= '".$today."' AND  IMHD.sale_type= ".$order_type." AND FIND_IN_SET(r.id,IMDT.recipe_id) !=0 AND r.type in (".$where_in.") order by r.name asc");		
        if ($query->num_rows() > 0) {
           foreach ($query->result() as $row) {			   
			    if(!empty($row->khmer_name)){
				   $row->khmer_name = $row->khmer_name;
			   }else{
				   $row->khmer_name = $row->name;
			   }			   
			  $row->comment_active = 0;
			    ($row->active ==2)?$row->non_transaction=1:$row->non_transaction=0;
				$row->addon =0;
				$row->recipe_variant_id =0;
				$row->variants =0;
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
	}
	public function GetAlltablecategory($warehouse_id, $bbq_id =NULL){
		/*if($bbq_id == 1){
			$bbq = 'bbq';
		}else{
			$bbq = 'suki';
		}*/
		$this->db->select('restaurant_areas.id, restaurant_areas.name');
		$this->db->join('restaurant_tables', 'restaurant_tables.warehouse_id = '.$warehouse_id.' AND restaurant_tables.area_id = restaurant_areas.id ');
		// $this->db->where('restaurant_areas.type', $bbq);
		$this->db->where("restaurant_tables.sale_type", 'bbq');
		$this->db->having('COUNT(srampos_restaurant_tables.sale_type) >= 1'); 
		$this->db->group_by('restaurant_areas.id');
		$query = $this->db->get('restaurant_areas');
		if ($query->num_rows() > 0) {
			 foreach ($query->result() as $row) {
				 $data[] = $row;
			 }
			  return $data;
		}
		 return FALSE;
	}
	
	public function GetAlltables($area_id, $warehouse_id, $user_id){
		$this->db->select('id, name, area_id, warehouse_id');
		$query = $this->db->get_where('restaurant_tables', array('area_id' => $area_id, 'warehouse_id' => $warehouse_id));
		if ($query->num_rows() > 0) {
			 foreach ($query->result() as $row) {
				 $table_status = $this->site->orderTablecheckapi($row->id, $user_id);
               	 $row->status = $table_status;
				 $data[] = $row;
			 }
			  return $data;
		}
		return FALSE;
	}
	
	public function addBBQ($array_bbq, $array_customer, $customer_id){

			
		if($this->db->insert('bbq', $array_bbq)){
								
			$bbq_id = $this->db->insert_id();
			
			if(!empty($customer_id)){
				$this->db->where('id', $bbq_id);
				$this->db->update('bbq', array('customer_id' => $customer_id));
			}else{
				$this->db->insert('companies', $array_customer);
				$cus_id = $this->db->insert_id();
				$this->db->where('id', $bbq_id);
				$this->db->update('bbq', array('customer_id' => $cus_id));
			}
			$this->db->where('bbq.id', $bbq_id);
			$q = $this->db->get('bbq');
			$data = $q->row();
			$data->order_type = 4;
			return $data;
		}
		return FALSE;
	}
	
	public function GetAllcurrencies() {
        $q = $this->db->get('currencies');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
	public function GetAlltaxs() {
        $q = $this->db->get('tax_rates');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
	public function GetAllwarehouses() {
        $q = $this->db->get('warehouses');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
	public function GetAllgroups() {
		
		$q = $this->db->get('groups');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
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
	
	public function GetAllcustomer_groups() {
        $q = $this->db->get('customer_groups');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
	public function GetAllsuppliers() {
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
	
	public function GetAllcustomers() {
		$this->db->where('group_name', 'customer');
        $q = $this->db->get("companies");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
	public function GetAlldeliveryusers($warehouse_id){
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
	
	public function Insertcustomer($data = array())
    {
        if ($this->db->insert('companies', $data)) {
            $cid = $this->db->insert_id();
            return $cid;
        }
        return false;
    }
	
    public function GetAllcostomerDiscounts() {
        $q = $this->db->get('diccounts_for_customer');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
	public function getBBQReturn($warehouse_id){
		$current_date = date('Y-m-d');
		$ignore = $this->db->select('*')->where('DATE(created_at)', $current_date)->get('sale_return');
		// print_r($this->db->last_query());die;
		if ($ignore->num_rows() > 0) {

            foreach ($ignore->result() as $ignorerow) {
				$sale_return[] = $ignorerow->split_id;	
			}
		}
		
		$this->db->select('orders.*, restaurant_tables.name as tablename,restaurant_areas.name as areaname');
		$this->db->join("restaurant_tables", "restaurant_tables.id = orders.table_id",'left');
		$this->db->join("restaurant_areas", "restaurant_areas.id = restaurant_tables.area_id",'left');
		$this->db->where_not_in('orders.split_id', $sale_return);	
		$this->db->where('orders.warehouse_id', $warehouse_id);
		$this->db->where('orders.order_type', 4);
		$this->db->where('DATE(date)', $current_date);
		$this->db->group_by('orders.split_id');
		$s = $this->db->get('orders');
// print_r($this->db->last_query());die;
		if ($s->num_rows() > 0) {

            foreach ($s->result() as $row) {
				if($row->sale_id == NULL){
					$row->sale_id = '0';	
				}else{
					$row->sale_id = $row->sale_id;	
				}
				$row->sales_split_id = $row->split_id;
				
				
				$q = $this->db->select('sales.*')
				->join('bils','bils.sales_id=sales.id')
				->join('payments','payments.bill_id=bils.id')
				->where('sales.sales_split_id', $row->split_id)
				->get('sales');	
				
				if ($q->num_rows() > 0) {
					$data[] = $row;	
				}
				
			}
			return $data;
		}
		
		return FALSE;	
	}
	
	public function getBBQAllBillingDatasreturn($warehouse_id){
		
		$current_date = date('Y-m-d');
		
		$ignore = $this->db->select('*')->where('DATE(created_at)', $current_date)->get('sale_return');
		if ($ignore->num_rows() > 0) {

            foreach ($ignore->result() as $ignorerow) {
				$sale_return[] = $ignorerow->split_id;	
			}
		}
		
		$this->db->select("bbq_sales.*, ,restaurant_tables.name as tablename,restaurant_areas.name as areaname");
		$this->db->join("restaurant_tables", "restaurant_tables.id = bbq_sales.sales_table_id",'left');
		$this->db->join("restaurant_areas", "restaurant_areas.id = restaurant_tables.area_id",'left');
		
		$this->db->where_not_in('bbq_sales.sales_split_id', $sale_return);		    
		$this->db->where('bbq_sales.warehouse_id', $warehouse_id);
		$this->db->where('bbq_sales.sale_status', 'Closed');
		$this->db->where('bbq_sales.cancel_status', 0);
		$this->db->where('DATE(date)', $current_date);
		$this->db->group_by('bbq_sales.sales_split_id');
		
		$s = $this->db->get('bbq_sales');
		// echo $this->db->last_query();die;
		if ($s->num_rows() > 0) {

            foreach ($s->result() as $row) {
				$data[] = $row;	
			}
			return $data;
		}
		return FALSE;
	}
	public function BBQsalesordersGET($split_id){
		$this->db->select('orders.id AS order_id,  orders.split_id, orders.order_type, order_items.id AS item_id, order_items.recipe_id, order_items.recipe_code, order_items.recipe_name, order_items.recipe_type, order_items.quantity, recipe.piece');
		$this->db->join('order_items', 'order_items.sale_id = orders.id');
		$this->db->join('recipe', 'recipe.id = order_items.recipe_id');
		
		$this->db->where('orders.split_id', $split_id);
		$this->db->where('orders.order_type', 4);
		$q = $this->db->get('orders');
		if ($q->num_rows() > 0) {
			
            foreach ($q->result() as $row) {
                $data[] = $row;
            }
            return $data;
        }
		return false;
	}	

	public function salereturnUpdate($return_array, $returnitem_array){
		
		if($this->db->insert('sale_return', $return_array)){
			$sale_return_id = $this->db->insert_id();
			foreach($returnitem_array as $item){
				
				$item['sale_return_id'] = $sale_return_id;
				$this->db->insert('sale_return_item', $item);
			}		
			return true;
		}		
		return false;
	}	

	public function GetAllBBQMenus(){		
		$mydate=getdate(date("U"));
        $day = "$mydate[weekday]";
		$default_image = site_url('assets/uploads/no_image.png');
		$default_url = site_url('assets/uploads/');
		$default_thumb_url = site_url('assets/uploads/thumbs/');
		$this->db->select("bbq_menu_day_wise_price.bbq_menu_id, bbq_menu.name,bbq_menu.khmer_name, bbq_menu_day_wise_price.adult_price,bbq_menu_day_wise_price.child_price,bbq_menu_day_wise_price.kids_price,CASE WHEN " . $this->db->dbprefix('bbq_menu') . ".image !='' THEN CONCAT('".$default_url."', image) ELSE '$default_image' END AS image");	
		$this->db->join('bbq_menu_day_wise_price', 'bbq_menu_day_wise_price.bbq_menu_id = bbq_menu.bbq_menu_id');	
		$this->db->where('bbq_menu_day_wise_price.day', $day);	
        $query = $this->db->get("bbq_menu");
        // print_r($this->db->last_query());die;
        if ($query->num_rows() > 0) {			
           foreach ($query->result() as $row) {			   
                $data[] = $row;
            }			
            return $data;
        }
        return FALSE;
	}

 public function getBBQLobsterSaletype($split){
		
		$this->db->select('BM.sale_type')
		    ->from('bbq BBQ')
		    ->join('bbq_menu BM', 'BM.bbq_menu_id = BBQ.bbq_menu_id') 	
		    ->where('BBQ.reference_no', $split)   
		    ->where('BM.status',1);  	

		    $bbq = $this->db->get();				    
		    if ($bbq->num_rows() > 0) {		    				 
                return $bbq->row('sale_type');
            }

        return FALSE;	
	}


}
