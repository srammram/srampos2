<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Pos_model extends CI_Model{
    public function __construct(){
        parent::__construct();
    }
	
	function checkStockavaQTY($recipe_id, $recipe_type){
		$current_date = date('Y-m-d');
		if($recipe_type == 'standard'){
			$this->db->select('preparation.id as id, preparation_items.stock_quantity, preparation_items.sale_quantity')
			->join('preparation_items', 'preparation_items.recipe_id = '.$recipe_id.'', 'left');
			$this->db->where('preparation_date', $current_date);
			$q =  $this->db->get('preparation');
			
			if ($q->num_rows() > 0) {
				$stock_ava_quantity = $q->row('stock_quantity') - $q->row('sale_quantity');
				if($stock_ava_quantity > 0){
					return $stock_ava_quantity;
				}else{
					return FALSE;	
				}
			}else{
				return FALSE;	
			}
		}elseif($recipe_type == 'production'){
			return FALSE;
		}
		return FALSE;
	}
    function getSetting(){
       $q = $this->db->get('pos_settings');
        if ($q->num_rows() > 0) {
            return $q->row();
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
	public function splitWaiterid($split_id){
		$q = $this->db->select('*')->where('split_id', $split_id)->get('orders');
		if ($q->num_rows() == 1) {
			
			return  $q->row('created_by');
			
		}
		return FALSE;
	}
     function getSettings()
    {
        $q = $this->db->get('settings');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function updateSetting($data)
    {
        $this->db->where('pos_id', '1');
        if ($this->db->update('pos_settings', $data)) {
            return true;
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
	
	/*BBQ*/
	
	public function getDiscountdata($customer_id, $waiter_id, $table_id, $split_id){
 		$current_date = date('Y-m-d');
		$this->db->select('*');
		$this->db->where('customer_id', $customer_id);
		$this->db->where('waiter_id', $waiter_id);
		$this->db->where('table_id', $table_id);
		$this->db->where('split_id', $split_id);
		$this->db->where('DATE(created_on)', $current_date);
		$q = $this->db->get('customer_request_discount');
		$data ='0';
		if ($q->num_rows() == 1) {
			//$data = $q->row();
			if($q->row('customer_type_val') == 'automanual' || $q->row('customer_type_val') == 'customer'){
				$data['dine'] = $q->row('customer_discount_val');
			}else{
				$data['dine'] = "0";
			}
			if($q->row('bbq_type_val') == 'automanual' || $q->row('bbq_type_val') == 'customer'){
				$data['bbq'] = $q->row('bbq_discount_val');
			}else{
				$data['bbq'] = "0";
			}
			return $data;
		}
		
		return $data;	
	}
	
	public function cancelBBQ($bbqcode){
		$update = array(
		        'status'=>'Closed',
			'cancel_status' => 1,
			'cancel_msg' => 'BBQ Covers Cancel',
			'created_by' => $this->session->userdata('user_id')
		);
		$this->db->where('reference_no', $bbqcode);
		$q = $this->db->update('bbq', $update);
		if($q){
			return true;	
		}
		return false;
	}
	public function BBQsalesordersGET($split_id){
		$this->db->select('orders.id AS order_id, orders.split_id, orders.order_type, order_items.id AS item_id, order_items.recipe_id, order_items.recipe_code, order_items.recipe_name, order_items.recipe_type, order_items.quantity, recipe.piece');
		$this->db->join('order_items', 'order_items.sale_id = orders.id');
		$this->db->join('recipe', 'recipe.id = order_items.recipe_id');
		//$this->db->join('sales', 'sales.sales_split_id = orders.split_id');
		$this->db->where('orders.split_id', $split_id);
		$q = $this->db->get('orders');
		if ($q->num_rows() > 0) {
			
            foreach ($q->result() as $row) {
                $data[] = $row;
            }
            return $data;
        }
		return false;
	}
	public function getCustomerBYID($customer_id) {
        $this->db->select('*')
        ->where('id', $customer_id)->where('group_id', 3);
        $q = $this->db->get("companies");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data['company'] = $row->company;
				$data['name'] = $row->name;
				$data['email_address'] = $row->email_address;
				$data['phone'] = $row->phone;
				$data['address'] = $row->address;
				$data['city'] = $row->city;
				$data['state'] = $row->state;
				$data['postal_code'] = $row->postal_code;
				$data['country'] = $row->country;
				$data['customer_id'] = $row->id;
            }
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
 public function getBBQLobsterSaletype($split){
		
		$this->db->select('BM.sale_type')
		    ->from('bbq BBQ')
		    ->join('bbq_menu BM', 'BM.bbq_menu_id = BBQ.bbq_menu_id') 	
		    ->where('BBQ.reference_no', $split)   
		    ->where('BM.status',1);  		    		 		    		   
		    $bbq = $this->db->get();	
		    // print_r($this->db->last_query());die;			    
		    if ($bbq->num_rows() > 0) {		    				 
                return $bbq->row('sale_type');
            }
        return FALSE;	
	}

	
	public function bbqrecipe_count($category_id, $warehouse_id, $subcategory_id = NULL, $brand_id = NULL)
    {
		/*$this->db->where('bbq_categories.id', $bbq_set_id);
		$q = $this->db->get('bbq_categories');
        if ($q->num_rows() > 0) {
            $bbq_items =  $q->row('items');
        }*/
		
		/*$this->db->select('recipe.*');		
		//$this->db->where_in('recipe.id', explode(',', $bbq_items));
		$this->db->join('recipe', "recipe.id = warehouses_recipe.recipe_id AND recipe.active = 1");
		
		$this->db->where('warehouses_recipe.warehouse_id', $warehouse_id);

		$this->db->where('recipe.recipe_standard !=', 1);
		$this->db->where('recipe.active', 1);		
		if ($category_id) {
            $this->db->where('recipe.category_id', $category_id);
        }
        if ($subcategory_id) {
            $this->db->where('recipe.subcategory_id', $subcategory_id);
        }

		$this->db->order_by("recipe.name", "asc");
		$query = $this->db->get("recipe");
*/
		$this->db->select('recipe.*');
		$this->db->join('recipe', "recipe.id = warehouses_recipe.recipe_id AND recipe.active = 1");
		$this->db->where('warehouses_recipe.warehouse_id', $warehouse_id);
		$this->db->where('recipe.recipe_standard !=', 1);
		$this->db->where('recipe.active', 1);	
		if ($brand_id) {
            $this->db->where('recipe.brand', $brand_id);
        } elseif ($category_id) {
            $this->db->where('recipe.category_id', $category_id);
        }
        if ($subcategory_id) {
            $this->db->where('recipe.subcategory_id', $subcategory_id);
        }
		$this->db->order_by("recipe.name", "asc");
		$query = $this->db->get("warehouses_recipe");
		// print_r($this->db->last_query());die;
        if ($query->num_rows() > 0) {
			
            foreach ($query->result() as $row) {
                $data[] = $row;
            }
			
            return count($data);
        }
        return false;
    }

    // public function bbqfetch_recipe($bbq_set_id, $warehouse_id, $limit, $start)
    public function bbqfetch_recipe($category_id, $warehouse_id, $limit, $start, $subcategory_id = NULL, $brand_id = NULL)    {
		/*$this->db->where('bbq_categories.id', $bbq_set_id);
		$q = $this->db->get('bbq_categories');
        if ($q->num_rows() > 0) {
            $bbq_items =  $q->row('items');
        }*/
		$where_in = array('standard','production','quick_service','combo');
		$this->db->select('recipe.*');	
		$this->db->join('recipe', "recipe.id = warehouses_recipe.recipe_id AND recipe.active = 1");
		$this->db->where('recipe.recipe_standard !=', 1);	
		$this->db->where('warehouses_recipe.warehouse_id', $warehouse_id);
		//$this->db->where_in('recipe.id', explode(',', $bbq_items));
		$this->db->where('recipe.active', 1);
		$this->db->where_in('recipe.type',$where_in);
		if ($category_id) {
            $this->db->where('recipe.category_id', $category_id);
        }
        if ($subcategory_id) {
            $this->db->where('recipe.subcategory_id', $subcategory_id);
        }

		$this->db->order_by("recipe.name", "asc");
		$this->db->limit($limit, $start);
		$query = $this->db->get("warehouses_recipe");
		  // print_r($this->db->last_query());die;
				
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }
	
public function bbqfetch_recipe_withdays($category_id=null, $warehouse_id, $limit, $start, $subcategory_id = NULL, $brand_id = NULL,$sale_type=NULL){    	
    	$mydate=getdate(date("U"));
        $today = "$mydate[weekday]";
		$where_in = array('standard','production','quick_service','combo');
		$this->db->select('recipe.*');
		$this->db->join('recipe', "recipe.id = warehouses_recipe.recipe_id AND recipe.active = 1");	
		$this->db->join('sale_items_mapping_details', "sale_items_mapping_details.recipe_group_id = recipe.category_id");		
		$this->db->join('sale_items_mapping_head', 'sale_items_mapping_head.id=sale_items_mapping_details.sales_map_hd_id');
		$this->db->where('sale_items_mapping_head.sale_type', $sale_type);
        $this->db->where('sale_items_mapping_head.days', $today);
          $this->db->where("FIND_IN_SET(srampos_recipe.id,srampos_sale_items_mapping_details.recipe_id) !=", 0);
		$this->db->where('warehouses_recipe.warehouse_id', $warehouse_id);
		$this->db->where_in('recipe.type',$where_in);
		if ($brand_id) {
            $this->db->where('recipe.brand', $brand_id);
        } elseif ($category_id) {
            $this->db->where('recipe.category_id', $category_id);
        }
        if ($subcategory_id) {
            $this->db->where('recipe.subcategory_id', $subcategory_id);
        }
		$this->db->limit($limit, $start);
		$this->db->group_by("recipe.id");
		$this->db->order_by("recipe.name", "asc");
		$query = $this->db->get("warehouses_recipe");   
		// print_r($this->db->last_query());die;    
        if ($query->num_rows() > 0) {
			
            foreach ($query->result() as $row) {
                $data[] = $row;
            }
			
            return $data;
        }
        return false;
    }
	public function getAllbbqCategories(){
		$current_day = date('l');
		
		$this->db->select('bbq_day_discount.discount, bbq_day_discount.discount_type, bbq_categories.id as bbq_id, bbq_categories.name, bbq_categories.adult_price, bbq_categories.child_price');
		$this->db->join('bbq_categories', 'bbq_categories.id = bbq_day_discount.bbq_category_id');
		$this->db->where('bbq_day_discount.active_day', $current_day);
		$q = $this->db->get('bbq_day_discount');
		
		if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $data[] = $row;
            }
			
            return $data;
        }
        return FALSE;
		
	}
	
	public function availableTables(){
		$current_date = date('Y-m-d');
    	$myQuery = "SELECT T.id,T.name
        FROM " . $this->db->dbprefix('restaurant_tables') . " T
        
            WHERE T.id NOT IN (SELECT table_id from srampos_orders WHERE DATE(date) ='".$current_date."'  AND payment_status IS NULL AND order_cancel_status = 0) AND T.id NOT IN (SELECT table_id from srampos_bbq_tables WHERE DATE(created_on) ='".$current_date."'  AND payment_status IS NULL)
             GROUP BY T.id";
            
        $q = $this->db->query($myQuery);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
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
			
			return $q->row();
		}
		return FALSE;
	}
	
	public function getBBQTablelist($warehouse_id){
		
		
		$this->db->select("restaurant_areas.name AS areas_name, restaurant_tables.area_id, 'tables' ");
		$this->db->join("restaurant_areas", "restaurant_areas.id = restaurant_tables.area_id");
		$this->db->where("restaurant_tables.sale_type", 'bbq');
		$this->db->where("restaurant_tables.warehouse_id", $warehouse_id);
		//$this->db->where("restaurant_areas.id", $table_area);
		$this->db->group_by("restaurant_tables.area_id");
		$this->db->having('COUNT(srampos_restaurant_tables.sale_type) >= 1'); 
		$q = $this->db->get("restaurant_tables");
		
		if ($q->num_rows() > 0) {
			 foreach ($q->result() as $row){
				 $this->db->select("restaurant_tables.id AS table_id, restaurant_tables.name AS table_name, restaurant_tables.max_seats AS table_seat");
				 //$this->db->join('bbq_tables', 'bbq_tables.table_id != restaurant_tables.id');
				 //$this->db->where_not_in('restaurant_tables.id', $bbqtable);
				 $this->db->where("restaurant_tables.area_id", $row->area_id);
				 $this->db->where("restaurant_tables.sale_type", 'bbq');
				 $this->db->where("restaurant_tables.warehouse_id", $warehouse_id);
				 $this->db->group_by('restaurant_tables.id');
				 $t = $this->db->get("restaurant_tables");
				 
				 if ($t->num_rows() > 0) {
					 foreach ($t->result() as $tow){
						 $table[$row->area_id][] = $tow;
					 }
					 $row->tables = $table[$row->area_id];
				 }
				 
				 $data[] = $row;
			 }
			
			 return $data;
		 }
		return FALSE;
	}
	public function getBBQTablelist_byID($id,$warehouse_id){
		
		
		$this->db->select("restaurant_areas.name AS areas_name, restaurant_tables.area_id, 'tables' ");
		$this->db->join("restaurant_areas", "restaurant_areas.id = restaurant_tables.area_id AND restaurant_tables.sale_type = 'bbq'");
		$this->db->where("restaurant_tables.warehouse_id", $warehouse_id);
		//$this->db->where("restaurant_areas.id", $table_area);
		$this->db->group_by("restaurant_tables.area_id");
		$q = $this->db->get("restaurant_tables");
		
		if ($q->num_rows() > 0) {
			 foreach ($q->result() as $row){
				 $this->db->select("restaurant_tables.id AS table_id, restaurant_tables.name AS table_name, restaurant_tables.max_seats AS table_seat");
				 //$this->db->join('bbq_tables', 'bbq_tables.table_id != restaurant_tables.id');
				 //$this->db->where_not_in('restaurant_tables.id', $bbqtable);
				 $this->db->where("restaurant_tables.area_id", $row->area_id);
				 $this->db->where("restaurant_tables.warehouse_id", $warehouse_id);
				 $this->db->where("restaurant_tables.id", $id);
				 $this->db->group_by('restaurant_tables.id');
				 $t = $this->db->get("restaurant_tables");
				 
				 if ($t->num_rows() > 0) {
					 foreach ($t->result() as $tow){
						 $table[$row->area_id][] = $tow;
					 }
					 $row->tables = $table[$row->area_id];
				 }
				 
				 $data[] = $row;
			 }
			
			 return $data;
		 }
		return FALSE;
	}
	public function getBBQByCode($split_id){
		
		$this->db->select("bbq.*, orders.customer, orders.biller_id, orders.biller ");
		$this->db->join('orders', 'orders.split_id = bbq.reference_no');
		$this->db->where('bbq.reference_no', $split_id);
		$this->db->group_by('bbq.reference_no');
		$q = $this->db->get('bbq');
		if ($q->num_rows() > 0) {
			$data = $q->row();
			
			return $data;
		}
		return FALSE;
	}
	
	public function getBBQAllSalesWithbiller($sales_type_id){
		
		
		/*$current_date = date('Y-m-d');
		$this->db->select("sales.*, ,restaurant_tables.name as tablename,restaurant_areas.name as areaname,'bils'");
		$this->db->join("restaurant_tables", "restaurant_tables.id = sales.sales_table_id",'left');
		$this->db->join("restaurant_areas", "restaurant_areas.id = restaurant_tables.area_id",'left');
		if(!empty($sales_type_id)){
			$this->db->where('sales.sales_type_id', $sales_type_id);	
		}
		
		$this->db->where('sales.warehouse_id', $this->session->userdata('warehouse_id'));
		$this->db->where('sales.sale_status', 'Process');
		$this->db->or_where('sales.sale_status', NULL);
		$this->db->where('sales.cancel_status', 0);
		$this->db->where('sales.consolidated', 0);
		
		$this->db->where('DATE(date)', $current_date);

		$s = $this->db->get('sales');*/
		$current_date = date('Y-m-d');
		$current_date = $this->site->getTransactionDate();
		$this->db->select("sales.*, ,restaurant_tables.name as tablename,restaurant_areas.name as areaname, customer_request_discount.id as customer_request_id, 'bils'");
		$this->db->join("restaurant_tables", "restaurant_tables.id = sales.sales_table_id",'left');
		$this->db->join("restaurant_areas", "restaurant_areas.id = restaurant_tables.area_id",'left');
		$this->db->join("customer_request_discount", "customer_request_discount.split_id = sales.sales_split_id AND customer_request_discount.table_id = sales.sales_table_id", 'left');
		if(!empty($sales_type_id)){
			$this->db->where('sales.sales_type_id', $sales_type_id);	
		}
		
		$this->db->where('sales.warehouse_id', $this->session->userdata('warehouse_id'));
		$this->db->where('sales.sale_status', 'Process');
		$this->db->where('sales.cancel_status', 0);
		$this->db->where('sales.consolidated', 0);
		//$this->db->where('DATE(date)', $current_date);

		$s = $this->db->get('sales');

		// print_r($this->db->last_query());die;
		if ($s->num_rows() > 0) {
		
            foreach ($s->result() as $row) {
				
				$this->db->select("bils.*,companies.credit_limit,companies.name customer_name,companies.customer_type,companies.id company_id,companies.credit_limit");
				$this->db->join("companies", "companies.id = bils.customer_id",'left');
				//$this->db->join("companies_paymentmode_details cp", "cp.company_id=companies.id",'left');
				//$this->db->join("deposits d", "d.company_id=companies.id and (companies.customer_type='postpaid' AND cp.status='active') or (companies.customer_type='prepaid' AND cp.status='paid')",'left');
				
				$this->db->where('bils.sales_id', $row->id);
				$this->db->where('bils.consolidated', 0);
				$b = $this->db->get('bils');
				// AND cp.due_date > CURRENT_TIMESTAMP
				if ($b->num_rows() > 0) {
					foreach ($b->result() as $bil_row) {
						
						$bils[$row->id][] = $bil_row;
					}
					$row->bils = $bils[$row->id];
					$data[] = $row;	
				}
				
			}
			
			return $data;
		}

		return FALSE;
	}
	
	public function getCONBBQAllSalesWithbiller($sales_type_id){
		
		/*$current_date = date('Y-m-d');
		$this->db->select("sales.*, ,restaurant_tables.name as tablename,restaurant_areas.name as areaname,'bils'");
		$this->db->join("restaurant_tables", "restaurant_tables.id = sales.sales_table_id",'left');
		$this->db->join("restaurant_areas", "restaurant_areas.id = restaurant_tables.area_id",'left');
		
		
		$this->db->where('sales.warehouse_id', $this->session->userdata('warehouse_id'));
		$this->db->where('sales.sale_status', 'Process');
		//$this->db->where('sales.payment_status', NULL);
		$this->db->where('sales.cancel_status', 0);
		$this->db->where('sales.consolidated', 1);
		
		$this->db->where('DATE(date)', $current_date);
		$this->db->group_by('sales.sales_split_id');
		$s = $this->db->get('sales');*/


		$current_date = date('Y-m-d');
		$current_date = $this->site->getTransactionDate();
		$this->db->select("sales.*, ,restaurant_tables.name as tablename,restaurant_areas.name as areaname, customer_request_discount.id as customer_request_id, 'bils'");
		$this->db->join("restaurant_tables", "restaurant_tables.id = sales.sales_table_id",'left');
		$this->db->join("restaurant_areas", "restaurant_areas.id = restaurant_tables.area_id",'left');
		$this->db->join("customer_request_discount", "customer_request_discount.split_id = sales.sales_split_id AND customer_request_discount.table_id = sales.sales_table_id", 'left');
		if(!empty($sales_type_id)){
			$this->db->where('sales.sales_type_id', $sales_type_id);	
		}
		
		$this->db->where('sales.warehouse_id', $this->session->userdata('warehouse_id'));
		$this->db->where('sales.sale_status', 'Process');
		$this->db->where('sales.cancel_status', 0);
		$this->db->where('sales.consolidated', 1);
		//$this->db->where('DATE(date)', $current_date);

		$s = $this->db->get('sales');

		// print_r($this->db->last_query());die;
		if ($s->num_rows() > 0) {
			
			
            foreach ($s->result() as $row) {
				
				$billQuery = "SELECT  GROUP_CONCAT(id) as ids FROM " . $this->db->dbprefix('sales') . "  WHERE sales_split_id = '".$row->sales_split_id."' ";
        		$q = $this->db->query($billQuery);
				
				if ($q->num_rows() > 0) {
					
					$this->db->select("bils.*,companies.credit_limit,companies.name customer_name,companies.customer_type,companies.id company_id,companies.credit_limit");
					$this->db->join("companies", "companies.id = bils.customer_id","left");
					$this->db->where('bils.consolidated', 1);
					$this->db->where_in('bils.sales_id', explode(',',$q->row('ids')));
					$b = $this->db->get('bils');
					// AND cp.due_date > CURRENT_TIMESTAMP
					if ($b->num_rows() > 0) {
						foreach ($b->result() as $bil_row) {
							
							$bils[$row->id][] = $bil_row;
						}
						$row->bils = $bils[$row->id];
						$data[] = $row;	
					}
				}
				
			}
			
			return $data;
		}

		return FALSE;
	}
	
	public function getBBQAllBillingDatas($date){
		
		$current_date = date('Y-m-d');
				
		$this->db->select("sales.*, ,restaurant_tables.name as tablename,restaurant_areas.name as areaname");
		$this->db->join("restaurant_tables", "restaurant_tables.id = sales.sales_table_id",'left');
		$this->db->join("restaurant_areas", "restaurant_areas.id = restaurant_tables.area_id",'left');
		
		    
		$this->db->where('sales.warehouse_id', $this->session->userdata('warehouse_id'));
		$this->db->where('sales.sale_status', 'Closed');
		$this->db->where('sales.cancel_status', 0);
		$this->db->where('sales.consolidated', 0);
		
		if($date==''){$date = date('Y-m-d');}
		$this->db->where('DATE(date)', $date);
		
		
		$this->db->where('sales.sales_type_id', 4);
		$this->db->group_by('sales.sales_split_id');
		$this->db->order_by("sales.id", "desc");
		
		$s = $this->db->get('sales');
		if ($s->num_rows() > 0) {

            foreach ($s->result() as $row) {
				
				$this->db->select("bils.*");
				$this->db->where('bils.sales_id', $row->id);
				/*$this->db->order_by("bils.sales_id", "desc");*/
				if(isset($_GET['bill_no']) && $_GET['bill_no']!=''){
				$this->db->where("(bils.bill_number like '%" . $_GET['bill_no'] . "%')");
				}
				if(isset($_GET['type']) && $_GET['type']!='all'){
				    $this->db->where('bils.table_whitelisted', $_GET['type']);
				}
				$b = $this->db->get('bils');
				

				if ($b->num_rows() > 0) {
					foreach ($b->result() as $bil_row) {
						
						$bils[$row->id][] = $bil_row;
					}
					$row->bils = $bils[$row->id];
					$data[] = $row;	
				}
			}
			return $data;
		}

		return FALSE;
	}	
	
	public function getBBQReturn(){
		$current_date = date('Y-m-d');
		$current_date = $this->site->getTransactionDate();
		$ignore = $this->db->select('*')->where('DATE(created_at)', $current_date)->get('sale_return');
		if ($ignore->num_rows() > 0) {

            foreach ($ignore->result() as $ignorerow) {
				$sale_return[] = $ignorerow->split_id;	
			}
		}
		
		$this->db->select('orders.*, restaurant_tables.name as tablename,restaurant_areas.name as areaname');
		$this->db->join("restaurant_tables", "restaurant_tables.id = orders.table_id",'left');
		$this->db->join("restaurant_areas", "restaurant_areas.id = restaurant_tables.area_id",'left');
		$this->db->where_not_in('orders.split_id', $sale_return);	
		$this->db->where('orders.warehouse_id', $this->session->userdata('warehouse_id'));
		$this->db->where('orders.order_type', 4);
		$this->db->where('DATE(date)', $current_date);
		$this->db->group_by('orders.split_id');
		$s = $this->db->get('orders');
		if ($s->num_rows() > 0) {

            foreach ($s->result() as $row) {
				
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
	
	public function getBBQAllBillingDatasreturn(){
		
		$current_date = date('Y-m-d');
		
		$ignore = $this->db->select('*')->where('DATE(created_at)', $current_date)->get('sale_return');
		if ($ignore->num_rows() > 0) {

            foreach ($ignore->result() as $ignorerow) {
				$sale_return[] = $ignorerow->split_id;	
			}
		}
		
		$this->db->select("bbq_sales.*, restaurant_tables.name as tablename,restaurant_areas.name as areaname");
		$this->db->join("restaurant_tables", "restaurant_tables.id = bbq_sales.sales_table_id",'left');
		$this->db->join("restaurant_areas", "restaurant_areas.id = restaurant_tables.area_id",'left');
		
		$this->db->where_not_in('bbq_sales.sales_split_id', $sale_return);		    
		$this->db->where('bbq_sales.warehouse_id', $this->session->userdata('warehouse_id'));
		$this->db->where('bbq_sales.sale_status', 'Closed');
		$this->db->where('bbq_sales.cancel_status', 0);
		$this->db->where('DATE(date)', $current_date);
		$this->db->group_by('bbq_sales.sales_split_id');
		
		$s = $this->db->get('bbq_sales');
		if ($s->num_rows() > 0) {

            foreach ($s->result() as $row) {
				$data[] = $row;	
			}
			return $data;
		}

		return FALSE;
	}	
	
	
	public function getAllBBQTablesorder($table_id = NULL){
		$current_date = date('Y-m-d');
		$current_date = $this->site->getTransactionDate();
		$this->db->select("restaurant_tables.id, restaurant_tables.name, restaurant_tables.max_seats, restaurant_tables.warehouse_id, restaurant_areas.name AS area_name, kitchen_orders.waiter_id, 'split_order' ", FALSE)
		->join("restaurant_table_orders", "restaurant_table_orders.table_id = restaurant_tables.id")
		//->join("kitchen_orders", "kitchen_orders.waiter_id = ".$this->session->userdata('user_id')." AND  kitchen_orders.sale_id = restaurant_table_orders.order_id")
		->join("kitchen_orders", "kitchen_orders.sale_id = restaurant_table_orders.order_id")
		->join('orders', 'orders.id = restaurant_table_orders.order_id  AND orders.order_cancel_status = 0')
		->join("restaurant_areas", "restaurant_areas.id = restaurant_tables.area_id");
		if(!empty($table_id)){
		$this->db->where('restaurant_tables.id', $table_id);
		}
		
		if($this->GP['pos-view_allusers_orders']==0){
		    $this->db->where('kitchen_orders.waiter_id',$this->session->userdata('user_id'));
		}
		//$this->db->where('DATE(date)', $current_date);
		$this->db->where("orders.order_type", 4);
		$this->db->where('orders.payment_status', NULL);
		$this->db->where('restaurant_tables.warehouse_id', $this->session->userdata('warehouse_id'));
		$this->db->group_by("restaurant_table_orders.table_id");
		
		$t = $this->db->get('restaurant_tables');		
		
		
		if ($t->num_rows() > 0) {

            foreach ($t->result() as $row) {
				
				
		$this->db->select("orders.id,restaurant_table_sessions.split_id, companies.name,orders.customer_id, restaurant_table_sessions.table_id, restaurant_table_sessions.session_started, 'order' ", FALSE)
				->join('orders', 'orders.split_id = restaurant_table_sessions.split_id AND orders.order_cancel_status = 0')
				->join("companies", "companies.id = orders.customer_id",'left');
				$this->db->where('restaurant_table_sessions.table_id', $row->id);
				$this->db->where("orders.order_type", 4);
				$this->db->where('orders.payment_status', NULL);
				//$this->db->where('DATE(date)', $current_date);
				$this->db->group_by('restaurant_table_sessions.split_id');
				$s = $this->db->get('restaurant_table_sessions');
				if ($s->num_rows() > 0) {
						
												
					foreach ($s->result() as $sow) {
						
						//$this->db->select("id ");
						//$checkbils = $this->db->get_where('sales', array('sales_split_id' => $sow->split_id));
						//if ($checkbils->num_rows() == 0) {
								
						 $this->db->select("orders.id, orders.customer_id, kitchen_orders.id AS kitchen, kitchen_orders.status,orders.order_type, orders.seats_id, orders.order_status, orders.reference_no, orders.date, orders.split_id, orders.table_id, 'items' AS item")
						->join('kitchen_orders', 'kitchen_orders.sale_id = orders.id', 'left');
						$this->db->where('orders.split_id', $sow->split_id);
						$this->db->where('orders.table_id', $sow->table_id);
						$this->db->where("orders.order_type", 4);
						$this->db->where('orders.order_cancel_status', 0);
						//$this->db->where('DATE(date)', $current_date);
						$o = $this->db->get('orders');
						
						$split[$row->id][] = $sow;
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
								
								$order[$sow->split_id][] = $oow;
							}
						}
						$sow->order = $order[$sow->split_id];	
						
						//}
						
						
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
	
	public function getBBQInvoiceByID($id)
    {
    	$this->db->select("bils.*,tax_rates.name as tax_name, tax_rates.rate as tax_rate")
	    ->join('tax_rates', 'tax_rates.id = bils.tax_id','left')
	    ->where('bils.id', $id);
		$q = $this->db->get('bils');
        
        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return FALSE;
    }
	
	public function getBBQInvoicePayments($bill_id)
    {
        $q = $this->db->get_where("payments", array('bill_id' => $bill_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
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
	
	public function BBQaddSale($notification_array, $timelog_array, $order_data, $splitData, $saleorder_item, $sale, $sale_items, $bilsdata, $bil_items, $order_id, $bbq_array, $splits, $request_discount, $bbq_in_discount,$birthday){	
	
	if(empty($bbq_in_discount)){
			foreach ($request_discount as $request) {
				$check = $this->db->select('*')->where('split_id', $splits)->get('customer_request_discount');
				if($check->num_rows() > 0){
					$this->db->where('split_id', $splits);
					$q = $this->db->update('customer_request_discount', $request);
				}else{					
					$q = $this->db->insert('customer_request_discount', $request);
				}
			}
		}
		
		$this->site->create_notification($notification_array);
		if(!empty($timelog_array)){
			foreach ($timelog_array as $time) {
					$res = $this->db->insert('time_log', $time);
			}      	
		}				
		if($this->db->insert('sales', $sale)){
			$sale_id = $this->db->insert_id();			
            $this->db->update('sales', $sales_array, array('id' => $sale_id));
			if(!empty($birthday)){ 	$this->db->insert('birthday', $birthday); }
			foreach($bilsdata as $key => $bilsrow){				
				$bilsrow['sales_id'] = $sale_id;
				$bilsrow['table_whitelisted'] = $this->isTableWhitelisted($sale['sales_table_id']);				
				$this->db->insert('bils', $bilsrow);
				$bill_id = $this->db->insert_id();
				$this->db->update('bils', array('bill_sequence_number' => $bill_id), array('id' => $bill_id));
				/*$bill_no = $this->site->CheckConsolidate($splits);		
				
				if($bill_no){					
					$bill_number = $bill_no;
				}else{					
					$bill_number = $this->site->BBQgenerate_bill_number($bilsrow['table_whitelisted']);
					$this->site->latest_bill($bill_number);
				}*/						
				foreach ($bil_items[$key]  as $bitems) {
					$bitems['bil_id'] = $bill_id;
					$this->db->insert('bbq_bil_items', $bitems);
				}
				foreach ($splitData[$key]  as $bill_items) {
					$bill_items['bil_id'] = $bill_id;
					$this->db->insert('bil_items', $bill_items);
				}
			}			
			/*foreach ($sale_items as $sitem) {
				$sitem['sale_id'] = $sale_id;
                $this->db->insert('bbq_sale_items', $sitem);                
            }*/
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
			$this->db->where_in('sale_id', $order_id);
			$this->db->update('order_items',  $kitchen_array);		
							
		if(!empty($bbq_array)){
				$this->db->where('reference_no', $splits);
				$this->db->update('bbq',  $bbq_array);
			}
			// print_r($this->db->error());die;
			return TRUE;			
		}
		// print_r($this->db->error());die;
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
	
	public function getBilID($order_split_id){
		$billQuery = "SELECT  GROUP_CONCAT(id) as ids FROM " . $this->db->dbprefix('sales') . " 
 		 WHERE sales_split_id = '".$order_split_id."' ";
        $q = $this->db->query($billQuery);        
        if ($q->num_rows() > 0) {
			$b = $this->db->select('bils.id, bils.sales_id, bils.grand_total, sales.sales_type_id, sales.sales_split_id,sales.reference_no')->join('sales', 'sales.id = bils.sales_id')->where_in('bils.sales_id', explode(',', $q->row('ids')))->get('bils');
			if ($b->num_rows() > 0) {
				foreach (($b->result()) as $row) {
					$data[] = $row;
				}
			}
			return $data;	
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
	
    public function BBQPayment($update_bill = array(), $bill_id =NULL, $payment = array(), $multi_currency = array(), $salesid = NULL, $sales_bill = array(), $order_split_id = NULL, $notification_array, $updateCreditLimit,$total,$customer_id,$loyalty_used_points,$taxation)
    {   
		$q = $this->db->select('sales_split_id, sales_table_id')->where('sales_split_id', $order_split_id)->get('sales');

		if($this->pos_settings->bill_series_settings == 1){
			$bill_number = $this->site->Payment_dine_bill_number($taxation,$bill_id);		
			$bill_no = $bill_number;
			$this->db->update('bils', array('bill_number' => $bill_number), array('id' => $bill_id));
		}else{
			$bill_number = $this->site->generate_bill_number($taxation,$bill_id);	
			$this->db->update('bils', array('bill_number' => $bill_number), array('id' => $bill_id));
		}
		$bill_no = $bill_number;

		if ($q->num_rows() > 0) {
            $split_id =  $q->row('sales_split_id');
			$table_id =  $q->row('sales_table_id');
        }
		
		$n = $this->db->select('id ')->where('split_id', $order_split_id)->get('orders');
		if ($n->num_rows() > 0) {
            $id =  $n->row('id');
        }
		
		$k = $this->db->select('waiter_id, chef_id')->where('sale_id', $id)->get('kitchen_orders');
		if ($k->num_rows() > 0) {
            $waiter_id =  $k->row('waiter_id');
			$chef_id =  $k->row('chef_id');
        }
		
		
		$notification_array['insert_array']['msg'] = 'The cashier check payment status has been done. please check bbq return process -  '.$order_split_id;
		$notification_array['insert_array']['table_id'] = $table_id;
		$notification_array['insert_array']['type'] = 'Cashier payment bils';
		 $notification_array['insert_array']['role_id'] = WAITER;
		 $notification_array['insert_array']['to_user_id'] = $waiter_id;
		
		
		
		$this->site->create_notification($notification_array);
		
    	if ($this->db->update('bils', $update_bill, array('id' => $bill_id))){
			$this->db->update('sales', $sales_bill, array('id' => $salesid));
    			$order_count = $this->db->get_where('bils', array('sales_id' => $salesid));
				$order_count =$order_count->num_rows();
				 $order_closed_count = $this->db->get_where('bils', array('bils.sales_id' => $salesid,'bils.payment_status' => 'Completed'));
				 $order_closed_count =$order_closed_count->num_rows();

    		foreach ($payment as $item) {
		    $item['customer_payment_type'] = $updateCreditLimit['customer_type'];
		    $this->db->insert('payments', $item);
			
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
					$this->db->update('bbq', $bbq_array, array('reference_no' => $split_id, 'table_id' => $table_id));					
					$res =  $this->db->update('restaurant_table_sessions', $tables_array, array('split_id' =>  $order_split_id));
					 // print_r($this->db->error());die;
		        }
		        $this->site->LoyaltyinserAndUpdate($bill_id,$total,$customer_id,$loyalty_used_points);	       
		    
    	 return true;
    	}    	
    	return false;
    } 
	
	public function BBQCONPayment($update_bill = array(), $billid_val =NULL, $consolidatedpayment = array(), $multi_currency = array(), $salesid_val = NULL, $sales_bill = array(), $order_split_id = NULL, $notification_array, $updateCreditLimit,$total,$customer_id,$loyalty_used_points,$bill_id,$taxation)
    {      
    	/*echo "<pre>";
    	print_r($billid_val[0]);die;*/
		$q = $this->db->select('sales_split_id, sales_table_id,GROUP_CONCAT(id SEPARATOR ",") as sales_id')->where('sales_split_id', $order_split_id)->get('sales');

		if ($q->num_rows() > 0) {
            $split_id =  $q->row('sales_split_id');
			$table_id =  $q->row('sales_table_id');
			$sales_id =  $q->row('sales_id');
        }	
		
		/*if($taxation == 1){
    		$bill_number = $this->site->BBQgenerate_bill_number($taxation);
			$billno = array(
			'bill_number' => $bill_number,				
			);							
			 $id2 =   explode(',',$sales_id);
				$this->db->where_in('sales_id', $id2);
				$this->db->update('bils',  $billno);				
			}*/					

		/*$bill_number = $this->site->Payment_dine_bill_number($taxation,$billid_val[0]);		
		$billno = array(
			'bill_number' => $bill_number,				
			);
		$id2 =   explode(',',$sales_id);
		$this->db->where_in('sales_id', $id2);
		$this->db->update('bils',  $billno);*/
		
// print_r($this->db->last_query());die;

	   if($this->pos_settings->bill_series_settings == 1){
			$bill_number = $this->site->Payment_dine_bill_number($taxation,$billid_val[0]);					
			$bill_no = array(
			'bill_number' => $bill_number,				
			);
			$id2 =   explode(',',$sales_id);
			$this->db->where_in('sales_id', $id2);
			$this->db->update('bils',  $bill_no);			
		}else{
		    	$bill_number = $this->site->generate_bill_number($taxation,$billid_val[0]);	
				$bill_no = array(
				'bill_number' => $bill_number,				
				);
				$id2 =   explode(',',$sales_id);
				$this->db->where_in('sales_id', $id2);
				$this->db->update('bils',  $bill_no);

		}
		$bill_no = $bill_number;

		$n = $this->db->select('id ')->where('split_id', $order_split_id)->get('orders');
		if ($n->num_rows() > 0) {
            $id =  $n->row('id');
        }
		
		$k = $this->db->select('waiter_id, chef_id')->where('sale_id', $id)->get('kitchen_orders');
		if ($k->num_rows() > 0) {
            $waiter_id =  $k->row('waiter_id');
			$chef_id =  $k->row('chef_id');
        }
		
		
		$notification_array['insert_array']['msg'] = 'The cashier check payment status has been done. please check bbq return process -  '.$order_split_id;
		$notification_array['insert_array']['table_id'] = $table_id;
		$notification_array['insert_array']['type'] = 'Cashier payment bils';
		 $notification_array['insert_array']['role_id'] = WAITER;
		 $notification_array['insert_array']['to_user_id'] = $waiter_id;		
		
		$this->site->create_notification($notification_array);
		
		foreach ($consolidatedpayment as $item) {
			unset($item['exchange_enable']);
			$this->db->insert('payments', $item);
		}
		
		foreach ($multi_currency as $currency) {
			$this->db->insert('sale_currency', $currency);
		}
		
	
		
		if(!empty($salesid_val)){

			for($i=0; $i<count($salesid_val); $i++){							

				$this->db->where('id', $billid_val[$i]);
				$this->db->update('bils', $update_bill[$billid_val[$i]]);				
				$this->db->where('id', $salesid_val[$i]);
				$this->db->update('sales', $sales_bill[$salesid_val[$i]]);
				
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
				
				//if ($order_count  == $order_closed_count) {
					$this->db->update('sales', $sales_array, array('id' => $salesid));
					$this->db->update('orders', $sales_array, array('split_id' =>  $order_split_id));
					$this->db->update('bbq', $bbq_array, array('reference_no' => $order_split_id));
					$res =  $this->db->update('restaurant_table_sessions', $tables_array, array('split_id' =>  $order_split_id));
				//}				
			}		

		
			$this->site->LoyaltyinserAndUpdate($bill_id,$total,$customer_id,$loyalty_used_points);	       
			return TRUE;
		}
		
		//die;
    	return false;
    } 
	
	public function getBBQAllBillitems($id =NULL)
    {
    	 $Billitems = "SELECT BI.* 
                    FROM ".$this->db->dbprefix('bbq_bil_items')." AS BI
                    WHERE BI.bil_id='".$id."' ";
            
        $q = $this->db->query($Billitems);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
     public function getBBQBillDiscountCovers($id =NULL)
    {

    	 $Billitems = "SELECT IFNULL(sum(discount_cover),0) AS discountcovers
                    FROM ".$this->db->dbprefix('bbq_bil_items')." AS BI
                    WHERE BI.bil_id='".$id."' ";
            
        $q = $this->db->query($Billitems);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data = $row->discountcovers;
            }
            return $data;
        }
        return 0;
    }
	
	public function getBBQTableNumber($bill_id)
    {
        $table_name = "SELECT T.name AS table_name,TY.name AS order_type

                    FROM ".$this->db->dbprefix('bils')." AS P
                    JOIN ". $this->db->dbprefix('sales') ." AS S ON S.id = P.sales_id
                    JOIN ". $this->db->dbprefix('orders') ." AS O ON O.split_id = S.sales_split_id
                    JOIN ". $this->db->dbprefix('sales_type') ." AS TY ON TY.id = O.order_type
                           
                    LEFT JOIN ". $this->db->dbprefix('restaurant_tables') ." AS T ON T.id = O.table_id
                    WHERE P.id='".$bill_id."' ";
            // echo $table_name;die;
        $q = $this->db->query($table_name);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data = $row;
            }
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
	/*BBQ END*/
    public function recipe_count($category_id, $warehouse_id, $subcategory_id = NULL, $brand_id = NULL)
    {
		
		$this->db->select('recipe.*');
		$this->db->join('recipe', "recipe.id = warehouses_recipe.recipe_id AND recipe.active = 1");
		
		$this->db->where('warehouses_recipe.warehouse_id', $warehouse_id);
		if ($brand_id) {
            $this->db->where('recipe.brand', $brand_id);
        } elseif ($category_id) {
            $this->db->where('recipe.category_id', $category_id);
        }
        if ($subcategory_id) {
            $this->db->where('recipe.subcategory_id', $subcategory_id);
        }
		$this->db->order_by("recipe.name", "asc");
		$query = $this->db->get("warehouses_recipe");
        if ($query->num_rows() > 0) {
			
            foreach ($query->result() as $row) {
                $data[] = $row;
            }
			
            return count($data);
        }
        return false;
		
    }
    public function recipe_count_withdays($category_id, $warehouse_id, $subcategory_id = NULL, $brand_id = NULL,$sale_type=NULL)
    {	    	 

		$mydate=getdate(date("U"));
        $today = "$mydate[weekday]";
		$this->db->select('recipe.*');
		$this->db->join('recipe', "recipe.id = warehouses_recipe.recipe_id AND recipe.active = 1");
		$this->db->join('sale_items_mapping_details', "sale_items_mapping_details.recipe_group_id = recipe.category_id");
		// $this->db->join('sale_items_mapping_details', "sale_items_mapping_details.recipe_subgroup_id = recipe.subcategory_id");
		$this->db->join('sale_items_mapping_head', 'sale_items_mapping_head.id=sale_items_mapping_details.sales_map_hd_id');
		$this->db->where('sale_items_mapping_head.sale_type', $sale_type);
        $this->db->where('sale_items_mapping_head.days', $today);
          $this->db->where("FIND_IN_SET(srampos_recipe.id,srampos_sale_items_mapping_details.recipe_id) !=", 0);
		$this->db->where('warehouses_recipe.warehouse_id', $warehouse_id);
		if ($brand_id) {
            $this->db->where('recipe.brand', $brand_id);
        } elseif ($category_id) {
            $this->db->where('recipe.category_id', $category_id);
        }
        if ($subcategory_id) {
            $this->db->where('recipe.subcategory_id', $subcategory_id);
        }
		$this->db->order_by("recipe.name", "asc");
		$query = $this->db->get("warehouses_recipe");
		// print_r($this->db->last_query());die;
        if ($query->num_rows() > 0) {
			
            foreach ($query->result() as $row) {
                $data[] = $row;
            }
			
            return count($data);
        }
        return false;
		
    }

    public function fetch_recipe_withdays($category_id=null, $warehouse_id, $limit, $start, $subcategory_id = NULL, $brand_id = NULL,$sale_type=NULL)
    {    	
    	$mydate=getdate(date("U"));
        $today = "$mydate[weekday]";
		$where_in = array('standard','production','quick_service','combo');
		$this->db->select('recipe.*');
		$this->db->join('recipe', "recipe.id = warehouses_recipe.recipe_id AND recipe.active = 1");	
		$this->db->join('sale_items_mapping_details', "sale_items_mapping_details.recipe_group_id = recipe.category_id");		
		$this->db->join('sale_items_mapping_head', 'sale_items_mapping_head.id=sale_items_mapping_details.sales_map_hd_id');
		$this->db->where('sale_items_mapping_head.sale_type', $sale_type);
        $this->db->where('sale_items_mapping_head.days', $today);
          $this->db->where("FIND_IN_SET(srampos_recipe.id,srampos_sale_items_mapping_details.recipe_id) !=", 0);
		$this->db->where('warehouses_recipe.warehouse_id', $warehouse_id);
		$this->db->where_in('recipe.type',$where_in);
		if ($brand_id) {
            $this->db->where('recipe.brand', $brand_id);
        } elseif ($category_id) {
            $this->db->where('recipe.category_id', $category_id);
        }
        if ($subcategory_id) {
            $this->db->where('recipe.subcategory_id', $subcategory_id);
        }
		$this->db->limit($limit, $start);
		$this->db->group_by("recipe.id");
		$this->db->order_by("recipe.name", "asc");
		$query = $this->db->get("warehouses_recipe");       
        if ($query->num_rows() > 0) {
			
            foreach ($query->result() as $row) {
                $data[] = $row;
            }
			
            return $data;
        }
        return false;
    }

    public function fetch_recipe($category_id, $warehouse_id, $limit, $start, $subcategory_id = NULL, $brand_id = NULL)
    {

    	/*$where ='';
        if ($brand_id){
            $where = "AND r.brand =".$brand_id."";
        }
        if($category_id){
             $where .= " AND r.category_id = ".$category_id." ";
         }
         if($subcategory_id){
             $where .= " AND r.subcategory_id = ".$subcategory_id." ";
         }
        $where_in = ('"standard","production","quick_service","combo"'); 
		$table_name = "SELECT r.* FROM ".$this->db->dbprefix('warehouses_recipe')." wr 
		JOIN ". $this->db->dbprefix('recipe') ." AS r ON r.id = wr.recipe_id AND r.active = 1		
		WHERE r.recipe_standard != 2 AND wr.warehouse_id = ".$warehouse_id." AND r.type IN(".$where_in.") ".$where."  ORDER BY  LEFT(r.name,1), CAST(MID(r.name,2) AS UNSIGNED),CAST(r.name AS UNSIGNED), LPAD(REPLACE(r.name,' ',''),10,'0'),r.name ASC LIMIT 50";
		*/
		/*CAST(name AS UNSIGNED),LEFT(name,10), CAST(MID(name,2) AS UNSIGNED), LPAD(REPLACE(name,' ',''),10,'0'),name ASC*/
		
		$where_in = array('standard','production','quick_service','combo');
		$this->db->select('recipe.*');
		$this->db->join('recipe', "recipe.id = warehouses_recipe.recipe_id ");
		$this->db->where('recipe.recipe_standard !=', 2);	
		$this->db->where('warehouses_recipe.warehouse_id', $warehouse_id);
		$this->db->where_in('recipe.type',$where_in);
		if ($brand_id) {
            $this->db->where('recipe.brand', $brand_id);
        } elseif ($category_id) {
            $this->db->where('recipe.category_id', $category_id);
        }
        if ($subcategory_id) {
            $this->db->where('recipe.subcategory_id', $subcategory_id);
        }
		$this->db->where_in('recipe.active', array(1,2));	
		$this->db->limit($limit, $start);
		$this->db->order_by("recipe.name", "asc");
		$query = $this->db->get("warehouses_recipe");
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function registerData($user_id){
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        $q = $this->db->get_where('pos_register', array('user_id' => $user_id, 'status' => 'open'), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function openRegister($data){
        if ($this->db->insert('pos_register', $data)) {
            return true;
        }
        return FALSE;
    }

    public function getOpenRegisters(){
        $this->db->select("date, user_id, cash_in_hand, CONCAT(" . $this->db->dbprefix('users') . ".first_name, ' ', " . $this->db->dbprefix('users') . ".last_name, ' - ', " . $this->db->dbprefix('users') . ".email) as user", FALSE)
            ->join('users', 'users.id=pos_register.user_id', 'left');
        $q = $this->db->get_where('pos_register', array('status' => 'open'));
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	public function getTablelist($warehouse_id,$area_id =null){
		$this->db->select("restaurant_areas.name AS areas_name, restaurant_tables.area_id,last_order_placed_time, 'tables' ");
		$this->db->join("restaurant_areas", "restaurant_areas.id = restaurant_tables.area_id");
		$this->db->where("restaurant_tables.sale_type", 'alacarte');
		$this->db->where("restaurant_tables.warehouse_id", $warehouse_id);
		if(!empty($area_id)){
		$this->db->where_in("restaurant_tables.area_id",explode(',',$area_id));
		}
		$this->db->group_by("restaurant_tables.area_id");
		$this->db->having('COUNT(srampos_restaurant_tables.sale_type) >= 1');
		$q = $this->db->get("restaurant_tables");
		if ($q->num_rows() > 0) {
			 foreach ($q->result() as $row){
                $table_name = "SELECT T.id AS table_id,T.name AS table_name,T.max_seats AS table_seat,T.current_order_user,T.current_order_status,T.area_id,T.last_order_placed_time
                    FROM ".$this->db->dbprefix('restaurant_tables')." AS T                                               
                    WHERE T.warehouse_id='".$warehouse_id."' AND T.area_id =".$row->area_id." AND T.sale_type='alacarte' ORDER BY  LEFT(name,1), CAST(MID(name,2) AS UNSIGNED),CAST(name AS UNSIGNED), LPAD(REPLACE(name,' ',''),10,'0'),name ASC LIMIT 0,35  ";
                    $t = $this->db->query($table_name);
				 if ($t->num_rows() > 0) {
					 foreach ($t->result() as $tow){
						 $table[$row->area_id][] = $tow;
					 }
					 $row->tables = $table[$row->area_id];
				 }
				 $data[] = $row;
			 }
			 return $data;
		 }
		return FALSE;
	}
		
	/*public function getTablelist($warehouse_id){
		$this->db->select("restaurant_areas.name AS areas_name, restaurant_tables.area_id, 'tables' ");
		$this->db->join("restaurant_areas", "restaurant_areas.id = restaurant_tables.area_id");
		$this->db->where("restaurant_tables.sale_type", 'alacarte');
		$this->db->where("restaurant_tables.warehouse_id", $warehouse_id);
		$this->db->group_by("restaurant_tables.area_id");
		$this->db->having('COUNT(srampos_restaurant_tables.sale_type) >= 1'); 
		$q = $this->db->get("restaurant_tables");		
		if ($q->num_rows() > 0) {
			 foreach ($q->result() as $row){
				 $this->db->select("restaurant_tables.id AS table_id, restaurant_tables.name AS table_name, restaurant_tables.max_seats AS table_seat");
				 $this->db->where("restaurant_tables.area_id", $row->area_id);
				 $this->db->where("restaurant_tables.sale_type", 'alacarte');
				 $this->db->where("restaurant_tables.warehouse_id", $warehouse_id);
				 $this->db->order_by("name", 'asc');
				 $t = $this->db->get("restaurant_tables");				 
				 if ($t->num_rows() > 0) {
					 foreach ($t->result() as $tow){
						 $table[$row->area_id][] = $tow;
					 }
					 $row->tables = $table[$row->area_id];
				 }
				 
				 $data[] = $row;
			 }
			
			 return $data;
		 }
		return FALSE;
	}*/
	public function getTable_byID($id,$warehouse_id){
		$this->db->select("restaurant_areas.name AS areas_name, restaurant_tables.area_id, 'tables' ");
		$this->db->join("restaurant_areas", "restaurant_areas.id = restaurant_tables.area_id AND restaurant_areas.type = 'suki' ");
		$this->db->where("restaurant_tables.warehouse_id", $warehouse_id);
		//$this->db->where("restaurant_tables.id", $id);
		$this->db->group_by("restaurant_tables.area_id");
		$q = $this->db->get("restaurant_tables");
		
		if ($q->num_rows() > 0) {
			 foreach ($q->result() as $row){
				 $this->db->select("restaurant_tables.id AS table_id, restaurant_tables.name AS table_name, restaurant_tables.max_seats AS table_seat");
				 $this->db->where("restaurant_tables.area_id", $row->area_id);
				 $this->db->where("restaurant_tables.warehouse_id", $warehouse_id);
				 $this->db->where("restaurant_tables.id", $id);
				 $t = $this->db->get("restaurant_tables");
				 
				 if ($t->num_rows() > 0) {
					 foreach ($t->result() as $tow){
						 $table[$row->area_id][] = $tow;
					 }
					 $row->tables = $table[$row->area_id];
				 }
				 
				 $data[] = $row;
			 }
			
			 return $data;
		 }
		return FALSE;
	}

public function checkTimeoutNotify($id){
				
		$notify = "SELECT N.id FROM ".$this->db->dbprefix('notiy')." AS N
		WHERE N.order_item_id = '".$id."'";

		$x = $this->db->query($notify);
		
		if ($x->num_rows() > 0) {
			return $x->row();
		}
		 return FALSE;
	}

  public function getbbqmenucoverpricebyid($menu_id){
  		$mydate=getdate(date("U"));
        $day = "$mydate[weekday]";
		$this->db->select("bbq_menu_day_wise_price.*");		
		$this->db->join('bbq_menu_day_wise_price', 'bbq_menu_day_wise_price.bbq_menu_id = bbq_menu.bbq_menu_id');
		$this->db->where('bbq_menu_day_wise_price.day', $day);
		$this->db->where('bbq_menu_day_wise_price.bbq_menu_id',$menu_id);					
		$q = $this->db->get('bbq_menu');			
		if($q->num_rows()>0){
		    return $q->row();
		}
		/*$this->db->select('*');
		$this->db->where('bbq_menu_id',$menu_id);
		$q = $this->db->get('bbq_menu');
		if($q->num_rows()>0){
		    return $q->row();
		}*/
    }

  public function getbbqmenucoverpricebyid_old($menu_id){
	$this->db->select('*');
	$this->db->where('bbq_menu_id',$menu_id);
	$q = $this->db->get('bbq_menu');
	if($q->num_rows()>0){
	    return $q->row();
	}
    }

	public function checkTables($table_id = NULL, $order_type = NULL){
		
		$current_date = date('Y-m-d');
		$this->db->select("orders.id,orders.split_id")
		->where('orders.table_id', $table_id)
		->where('orders.order_cancel_status', 0)
		->where('orders.payment_status', NULL)
		->where('orders.order_type', $order_type)
		->where('DATE(date)', $current_date);
		$q = $this->db->get('orders');		
		
		if ($q->num_rows() > 0) {
		    $data['status'] = 'success';
		    if($this->site->splitCheckSalestable($q->row('split_id')) == TRUE){
			$data['status'] = 'bill-generated';
		    }
		
		    return $data;
			  
		 }	
		
		return FALSE;
	}
	
	public function getAllTablesWithCustomerRequest($warehouse_id){
		
		$current_date = date('Y-m-d');
		$this->db->select("sales.*, ,restaurant_tables.name as tablename,restaurant_areas.name as areaname");
		$this->db->join("restaurant_tables", "restaurant_tables.id = sales.sales_table_id",'left');
		$this->db->join("restaurant_areas", "restaurant_areas.id = restaurant_tables.area_id",'left');
		$this->db->where('sales.warehouse_id', $warehouse_id);
		$this->db->where('sales.sale_status', 'Process');
		$this->db->where('sales.cancel_status', 0);
		$this->db->where('sales.bilgenerator_type', 1);
		$this->db->where('DATE(date)', $current_date);

		$q = $this->db->get('sales');
				
		
		if ($q->num_rows() > 0) {

            foreach ($q->result() as $row) {
				
				$this->db->select("bils.*, diccounts_for_customer.name AS customer_discount_name, diccounts_for_customer.discount_type AS customer_discount_type, diccounts_for_customer.value AS customer_value");
				$this->db->join("diccounts_for_customer", "diccounts_for_customer.id = bils.customer_discount_id");
				$this->db->where('bils.sales_id', $row->id);
				$b = $this->db->get('bils');
				

				if ($b->num_rows() > 0) {
					foreach ($b->result() as $bil_row) {
						
						$bils[$row->id][] = $bil_row;
					}
					$row->bils = $bils[$row->id];
					$data[] = $row;	
				}
				
			}
			
			return $data;
		}
		return FALSE;
	}
	
	public function getAllSalesWithbiller($sales_type_id = NULL,$table_id=null,$steward=null){
		
		$current_date = date('Y-m-d');
		$current_date = $this->site->getTransactionDate();
		$this->db->select("sales.*, ,restaurant_tables.name as tablename,restaurant_areas.name as areaname, customer_request_discount.id as customer_request_id, 'bils'");
		$this->db->join("restaurant_tables", "restaurant_tables.id = sales.sales_table_id",'left');
		$this->db->join("restaurant_areas", "restaurant_areas.id = restaurant_tables.area_id",'left');
		$this->db->join("bils", "bils.sales_id = sales.id",'left');
		$this->db->join("customer_request_discount", "customer_request_discount.split_id = sales.sales_split_id AND customer_request_discount.table_id = sales.sales_table_id", 'left');
		if(!empty($sales_type_id)){
			$this->db->where('sales.sales_type_id', $sales_type_id);	
		}
		
		$this->db->where('sales.warehouse_id', $this->session->userdata('warehouse_id'));
		$this->db->where('sales.sale_status', 'Process');
		$this->db->where('sales.cancel_status', 0);
		$this->db->where('sales.consolidated', 0);
		if($table_id!=null){
		    $this->db->where('sales.sales_table_id', $table_id);
		}
		if($steward!=null){
		    $this->db->where('sales.biller_id', $steward);
		}
		// $this->db->where('srampos_bils.date', $date);
		$this->db->group_by("sales.id");
		$this->db->order_by("bils.bill_number", "desc");

		//$this->db->where('DATE(date)', $current_date);

		$s = $this->db->get('sales');
// print_r($this->db->last_query());die;
		if ($s->num_rows() > 0) {

            foreach ($s->result() as $row) {
				$this->db->select("bils.*,companies.credit_limit,companies.name customer_name,companies.allow_loyalty,companies.customer_type,companies.id company_id,companies.credit_limit");
				$this->db->join("companies", "companies.id = bils.customer_id",'left');
				$this->db->where('bils.sales_id', $row->id);
				$this->db->where('bils.consolidated', 0);
				$this->db->where('bils.payment_status', NULL);
				$b = $this->db->get('bils');
				// AND cp.due_date > CURRENT_TIMESTAMP
				if ($b->num_rows() > 0) {
					foreach ($b->result() as $bil_row) {
						
						$bils[$row->id][] = $bil_row;
					}
					$row->bils = $bils[$row->id];
					$data[] = $row;	
				}
				
			}
			// echo "<pre>";
		
			
			return $data;
		}

		return FALSE;
	}
	
	public function getCONBBQAllSalesWithbillerreprint($sales_type_id = NULL,$date){
		
		$current_date = date('Y-m-d');
		$current_date = $this->site->getTransactionDate();
		$this->db->select("sales.*, ,restaurant_tables.name as tablename,restaurant_areas.name as areaname,'bils'");
		$this->db->join("restaurant_tables", "restaurant_tables.id = sales.sales_table_id",'left');
		$this->db->join("restaurant_areas", "restaurant_areas.id = restaurant_tables.area_id",'left');
		
		
		$this->db->where('sales.warehouse_id', $this->session->userdata('warehouse_id'));
		$this->db->where('sales.sale_status', 'Closed');
		$this->db->where('sales.cancel_status', 0);
		$this->db->where('sales.consolidated', 1);
		if($date==''){$date = date('Y-m-d');}
		$this->db->where('DATE(date)', $date);
		$this->db->group_by('sales.sales_split_id');
		$this->db->order_by("sales.id", "desc");
		$s = $this->db->get('sales');

		if ($s->num_rows() > 0) {

            foreach ($s->result() as $row) {
				
				$this->db->select("bils.*,companies.credit_limit,companies.name customer_name,companies.customer_type,companies.id company_id,companies.credit_limit,companies.allow_loyalty");
				$this->db->join("companies", "companies.id = bils.customer_id",'left');
				//$this->db->join("companies_paymentmode_details cp", "cp.company_id=companies.id",'left');
				//$this->db->join("deposits d", "d.company_id=companies.id and (companies.customer_type='postpaid' AND cp.status='active') or (companies.customer_type='prepaid' AND cp.status='paid')",'left');
				
				$this->db->where('bils.sales_id', $row->id);
				$this->db->where('bils.consolidated', 1);
				if(isset($_GET['bill_no']) && $_GET['bill_no']!=''){
				$this->db->where("(bils.bill_number like '%" . $_GET['bill_no'] . "%')");
				}
				if(isset($_GET['type']) && $_GET['type']!='all'){
				    $this->db->where('bils.table_whitelisted', $_GET['type']);
				}
				$b = $this->db->get('bils');
				// AND cp.due_date > CURRENT_TIMESTAMP
				if ($b->num_rows() > 0) {
					foreach ($b->result() as $bil_row) {
						
						$bils[$row->id][] = $bil_row;
					}
					$row->bils = $bils[$row->id];
					$data[] = $row;	
				}
				
			}
			
			return $data;
		}

		return FALSE;
	}
	
	public function getCONAllSalesWithbiller($sales_type_id = NULL){
		
		$current_date = date('Y-m-d');
		$this->db->select("sales.*, ,restaurant_tables.name as tablename,restaurant_areas.name as areaname,'bils'");
		$this->db->join("restaurant_tables", "restaurant_tables.id = sales.sales_table_id",'left');
		$this->db->join("restaurant_areas", "restaurant_areas.id = restaurant_tables.area_id",'left');
		
		
		$this->db->where('sales.warehouse_id', $this->session->userdata('warehouse_id'));
		$this->db->where('sales.sale_status', 'Process');
		$this->db->where('sales.cancel_status', 0);
		$this->db->where('sales.consolidated', 1);
		$this->db->where('DATE(date)', $current_date);

		$s = $this->db->get('sales');

		if ($s->num_rows() > 0) {

            foreach ($s->result() as $row) {
				
				$this->db->select("bils.*,companies.credit_limit,companies.name customer_name,companies.customer_type,companies.id company_id,companies.credit_limit,companies.allow_loyalty");
				$this->db->join("companies", "companies.id = bils.customer_id",'left');
				//$this->db->join("companies_paymentmode_details cp", "cp.company_id=companies.id",'left');
				//$this->db->join("deposits d", "d.company_id=companies.id and (companies.customer_type='postpaid' AND cp.status='active') or (companies.customer_type='prepaid' AND cp.status='paid')",'left');
				
				$this->db->where('bils.sales_id', $row->id);
				$this->db->where('bils.consolidated', 1);
				
				$b = $this->db->get('bils');
				// AND cp.due_date > CURRENT_TIMESTAMP
				if ($b->num_rows() > 0) {
					foreach ($b->result() as $bil_row) {
						
						$bils[$row->id][] = $bil_row;
					}
					$row->bils = $bils[$row->id];
					$data[] = $row;	
				}
				
			}
			return $data;
		}

		return FALSE;
	}
	
	public function getAllBillingDatas(){
		
		$current_date = date('Y-m-d');
		$current_date = $this->site->getTransactionDate();
		$this->db->select("sales.*, ,restaurant_tables.name as tablename,restaurant_areas.name as areaname,'bils'");
		$this->db->join("restaurant_tables", "restaurant_tables.id = sales.sales_table_id",'left');
		$this->db->join("restaurant_areas", "restaurant_areas.id = restaurant_tables.area_id",'left');
		/*if(!empty($sales_type_id)){
			$this->db->where('sales.sales_type_id', $sales_type_id);	
		}*/
		$this->db->where('sales.warehouse_id', $this->session->userdata('warehouse_id'));
		$this->db->where('sales.sale_status', 'Closed');
		$this->db->where('sales.cancel_status', 0);
		$this->db->where('sales.consolidated', 0);
		$this->db->where('DATE(date)', $current_date);
		$this->db->order_by("sales.id", "desc");

		$s = $this->db->get('sales');

		if ($s->num_rows() > 0) {

            foreach ($s->result() as $row) {
				
				$this->db->select("bils.*");
				$this->db->where('bils.sales_id', $row->id);
				$b = $this->db->get('bils');
				

				if ($b->num_rows() > 0) {
					foreach ($b->result() as $bil_row) {
						
						$bils[$row->id][] = $bil_row;
					}
					$row->bils = $bils[$row->id];
					$data[] = $row;	
				}
				
			}
			
			return $data;
		}

		return FALSE;
	}	

	
public function getAllBillingforReprint($date){
		$current_date = date('Y-m-d');
		$this->db->select("sales.*, ,restaurant_tables.name as tablename,restaurant_areas.name as areaname,'bils'");
		$this->db->join("restaurant_tables", "restaurant_tables.id = sales.sales_table_id",'left');
		$this->db->join("restaurant_areas", "restaurant_areas.id = restaurant_tables.area_id",'left');		
		$this->db->join("bils", "bils.sales_id = sales.id");		
		$this->db->where('sales.warehouse_id', $this->session->userdata('warehouse_id'));
		$this->db->where('sales.sale_status', 'Closed');
		$this->db->where('sales.cancel_status', 0);
		$this->db->where('sales.consolidated', 0);
		if($date==''){$date = date('Y-m-d');}
		$this->db->where('bils.date', $date);
		if(isset($_POST['bill_no']) && $_POST['bill_no']!=''){
		   // $this->db->where("(bils.bill_number like '%" . $_POST['bill_no'] . "%')");
			$this->db->where("bils.bill_number like",$_POST['bill_no']);
		}
		if(isset($_POST['type']) && $_POST['type']!='all'){
		    $this->db->where('bils.table_whitelisted', $_POST['type']);
		}
		$this->db->where_in('sales_type_id',array(1,2,3));
		$this->db->group_by("sales.id");
		$this->db->order_by("bils.bill_number", "desc");
		$s = $this->db->get('sales');
		if ($s->num_rows() > 0) {
            foreach ($s->result() as $row) {
				
				$this->db->select("bils.*");
				// $this->db->join("sales", "bils.sales_id = sales.id");		
				$this->db->where('bils.sales_id', $row->id);
				// $this->db->order_by("bils.bill_number", "desc");
				
				$b = $this->db->get('bils');
				// print_r($this->db->last_query());die;

				if ($b->num_rows() > 0) {
					foreach ($b->result() as $bil_row) {
						
						$bils[$row->id][] = $bil_row;
					}
					$row->bils = $bils[$row->id];
					$data[] = $row;	
				}
				
			}
			
			return $data;
		}

		return FALSE;
	}	
public function getAllBillingforReprint_archival($date){
		$current_date = date('Y-m-d');
		$this->db->select("sales_archival.*, ,restaurant_tables.name as tablename,restaurant_areas.name as areaname,'bils'");
		$this->db->join("restaurant_tables", "restaurant_tables.id = sales_archival.sales_table_id",'left');
		$this->db->join("restaurant_areas", "restaurant_areas.id = restaurant_tables.area_id",'left');		
		$this->db->join("bils_archival", "bils_archival.sales_id = sales_archival.id");		
		$this->db->where('sales_archival.warehouse_id', $this->session->userdata('warehouse_id'));
		$this->db->where('sales_archival.sale_status', 'Closed');
		$this->db->where('sales_archival.cancel_status', 0);
		$this->db->where('sales_archival.consolidated', 0);
		if($date==''){$date = date('Y-m-d');}
		$this->db->where('bils_archival.date', $date);
		if(isset($_POST['bill_no']) && $_POST['bill_no']!=''){
		   // $this->db->where("(bils_archival.bill_number like '%" . $_POST['bill_no'] . "%')");
			$this->db->where("bils_archival.bill_number like",$_POST['bill_no']);
		}
		if(isset($_POST['type']) && $_POST['type']!='all'){
		    $this->db->where('bils_archival.table_whitelisted', $_POST['type']);
		}
		$this->db->where_in('sales_type_id',array(1,2,3));
		$this->db->group_by("sales_archival.id");
		$this->db->order_by("bils_archival.bill_number", "desc");
		$s = $this->db->get('sales_archival');
		if ($s->num_rows() > 0) {
            foreach ($s->result() as $row) {
				$this->db->select("bils_archival.*");
				// $this->db->join("sales", "bils.sales_id = sales.id");		
				$this->db->where('bils_archival.sales_id', $row->id);
				// $this->db->order_by("bils_archival.bill_number", "desc");
				$b = $this->db->get('bils_archival');
				// print_r($this->db->last_query());die;

				if ($b->num_rows() > 0) {
					foreach ($b->result() as $bil_row) {
						
						$bils[$row->id][] = $bil_row;
					}
					$row->bils = $bils[$row->id];
					$data[] = $row;	
				}
				
			}
			
			return $data;
		}

		return FALSE;
	}	

	public function getAllDoordeliveryorder(){
		$current_date = date('Y-m-d');
		$current_date = $this->site->getTransactionDate();
		$this->db->select("orders.split_id , orders.customer_id, orders.customer,  'order'");
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
						
					 $this->db->select("orders.id, orders.customer_id, kitchen_orders.id AS kitchen, kitchen_orders.status,orders.order_type, orders.seats_id, orders.order_status, orders.reference_no, orders.date, orders.split_id, orders.table_id, 'items' AS item")
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
	public function getAllTakeawayorder(){
		$current_date = date('Y-m-d');
		$current_date = $this->site->getTransactionDate();
		$this->db->select("orders.split_id, orders.customer_id,  'order'");
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
								
					 $this->db->select("orders.id, orders.customer_id, kitchen_orders.id AS kitchen, kitchen_orders.status,orders.order_type, orders.seats_id, orders.order_status, orders.reference_no, orders.date, orders.split_id, orders.table_id, 'items' AS item")
					 
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
		public function getAllTablesorder_new($table_id = NULL){
		$current_date = date('Y-m-d');
		$current_date = $this->site->getTransactionDate();
		$this->db->select("restaurant_tables.id, restaurant_tables.name, restaurant_tables.max_seats, restaurant_tables.warehouse_id, restaurant_areas.name AS area_name, orders.waiter_id, 'split_order' ", FALSE)
		// ->join("restaurant_table_orders", "restaurant_table_orders.table_id = restaurant_tables.id")
		//->join("kitchen_orders", "kitchen_orders.waiter_id = ".$this->session->userdata('user_id')." AND  kitchen_orders.sale_id = restaurant_table_orders.order_id")
		// ->join("kitchen_orders", "kitchen_orders.sale_id = restaurant_table_orders.order_id")
		->join('orders', 'orders.table_id = restaurant_tables.id  AND orders.order_cancel_status = 0')
		->join("restaurant_areas", "restaurant_areas.id = restaurant_tables.area_id");
		if(!empty($table_id)){
		$this->db->where('restaurant_tables.id', $table_id);
		}
		
		if($this->GP['pos-view_allusers_orders']==0){
		    $this->db->where('orders.waiter_id',$this->session->userdata('user_id'));
		}
		$this->db->where('DATE(date)', $current_date);
		$this->db->where("orders.order_type", 1);
		$this->db->where('orders.payment_status', NULL);
		
		//$this->db->where('orders.order_status !=', 'Closed');
		$this->db->where('restaurant_tables.warehouse_id', $this->session->userdata('warehouse_id'));
		$this->db->group_by("restaurant_tables.id");
		
		$t = $this->db->get('restaurant_tables');		
		// print_r($this->db->error());die;
		if ($t->num_rows() > 0) {

            foreach ($t->result() as $row) {
				
				
		$this->db->select("orders.id,restaurant_table_sessions.split_id, companies.name,orders.customer_id, restaurant_table_sessions.table_id, restaurant_table_sessions.session_started, 'order' ", FALSE)
				->join('orders', 'orders.split_id = restaurant_table_sessions.split_id AND orders.order_cancel_status = 0')
				->join("companies", "companies.id = orders.customer_id",'left');
				$this->db->where('restaurant_table_sessions.table_id', $row->id);
				$this->db->where("orders.order_type", 1);
				$this->db->where('orders.payment_status', NULL);
				$this->db->where('DATE(date)', $current_date);
				$this->db->group_by('restaurant_table_sessions.split_id');
				$s = $this->db->get('restaurant_table_sessions');
				if ($s->num_rows() > 0) {
						
												
					foreach ($s->result() as $sow) {
						
						//$this->db->select("id ");
						//$checkbils = $this->db->get_where('sales', array('sales_split_id' => $sow->split_id));
						//if ($checkbils->num_rows() == 0) {
								
						 $this->db->select("orders.id, orders.customer_id, orders.order_type, orders.seats_id, orders.order_status, orders.reference_no, orders.date, orders.split_id, orders.table_id, 'items' AS item");
						// ->join('kitchen_orders', 'kitchen_orders.sale_id = orders.id', 'left');
						$this->db->where('orders.split_id', $sow->split_id);
						$this->db->where('orders.table_id', $sow->table_id);
						$this->db->where("orders.order_type", 1);
						$this->db->where('orders.order_cancel_status', 0);
						$this->db->where('DATE(date)', $current_date);
						$o = $this->db->get('orders');
						
						$split[$row->id][] = $sow;
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
								
								$order[$sow->split_id][] = $oow;
							}
						}
						$sow->order = $order[$sow->split_id];	
						
						//}
						
						
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

	public function getAllTablesorder($table_id = NULL){
		$current_date = date('Y-m-d');
		$current_date = $this->site->getTransactionDate();
		$this->db->select("restaurant_tables.id, restaurant_tables.name, restaurant_tables.max_seats, restaurant_tables.warehouse_id, restaurant_areas.name AS area_name, kitchen_orders.waiter_id, 'split_order' ", FALSE)
		->join("restaurant_table_orders", "restaurant_table_orders.table_id = restaurant_tables.id")
		//->join("kitchen_orders", "kitchen_orders.waiter_id = ".$this->session->userdata('user_id')." AND  kitchen_orders.sale_id = restaurant_table_orders.order_id")
		->join("kitchen_orders", "kitchen_orders.sale_id = restaurant_table_orders.order_id")
		->join('orders', 'orders.id = restaurant_table_orders.order_id  AND orders.order_cancel_status = 0')
		->join("restaurant_areas", "restaurant_areas.id = restaurant_tables.area_id");
		if(!empty($table_id)){
		$this->db->where('restaurant_tables.id', $table_id);
		}
		
		if($this->GP['pos-view_allusers_orders']==0){
		    $this->db->where('kitchen_orders.waiter_id',$this->session->userdata('user_id'));
		}
		$this->db->where('DATE(date)', $current_date);
		$this->db->where("orders.order_type", 1);
		$this->db->where('orders.payment_status', NULL);
		
		//$this->db->where('orders.order_status !=', 'Closed');
		$this->db->where('restaurant_tables.warehouse_id', $this->session->userdata('warehouse_id'));
		$this->db->group_by("restaurant_table_orders.table_id");
		
		$t = $this->db->get('restaurant_tables');		
		
		if ($t->num_rows() > 0) {

            foreach ($t->result() as $row) {
				
				
		$this->db->select("orders.id,restaurant_table_sessions.split_id, companies.name,orders.customer_id, restaurant_table_sessions.table_id, restaurant_table_sessions.session_started, 'order' ", FALSE)
				->join('orders', 'orders.split_id = restaurant_table_sessions.split_id AND orders.order_cancel_status = 0')
				->join("companies", "companies.id = orders.customer_id",'left');
				$this->db->where('restaurant_table_sessions.table_id', $row->id);
				$this->db->where("orders.order_type", 1);
				$this->db->where('orders.payment_status', NULL);
				$this->db->where('DATE(date)', $current_date);
				$this->db->group_by('restaurant_table_sessions.split_id');
				$s = $this->db->get('restaurant_table_sessions');
				if ($s->num_rows() > 0) {
						
												
					foreach ($s->result() as $sow) {
						
						//$this->db->select("id ");
						//$checkbils = $this->db->get_where('sales', array('sales_split_id' => $sow->split_id));
						//if ($checkbils->num_rows() == 0) {
								
						 $this->db->select("orders.id, orders.customer_id, kitchen_orders.id AS kitchen, kitchen_orders.status,orders.order_type, orders.seats_id, orders.order_status, orders.reference_no, orders.date, orders.split_id, orders.table_id, 'items' AS item")
						->join('kitchen_orders', 'kitchen_orders.sale_id = orders.id', 'left');
						$this->db->where('orders.split_id', $sow->split_id);
						$this->db->where('orders.table_id', $sow->table_id);
						$this->db->where("orders.order_type", 1);
						$this->db->where('orders.order_cancel_status', 0);
						$this->db->where('DATE(date)', $current_date);
						$o = $this->db->get('orders');
						
						$split[$row->id][] = $sow;
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
								
								$order[$sow->split_id][] = $oow;
							}
						}
						$sow->order = $order[$sow->split_id];	
						
						//}
						
						
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
	
	public function getOrderitemlist($order_id, $table_id, $split_id, $user_id){
		$this->db->select("orders.reference_no, kitchen_orders.id AS kitchen, kitchen_orders.waiter_id AS waiter, order_items.id AS item_id, order_items.recipe_id, order_items.recipe_name, order_items.recipe_type, order_items.unit_price, order_items.quantity, order_items.subtotal ")
		->join('kitchen_orders', 'kitchen_orders.sale_id = orders.id AND kitchen_orders.waiter_id = '.$user_id.'', 'left')
		->join('order_items', 'order_items.sale_id = orders.id AND order_items.kitchen_id = kitchen_orders.id', 'left');
		
		$q = $this->db->get_where('orders', array('orders.reference_no' => $order_id, 'orders.table_id' => $table_id, 'orders.split_id' => $split_id));
		if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $data[] = $row;
            }
			
            return $data;
        }
        return FALSE;
	}
	
	
	public function getBil_Special($table_id, $split_id, $user_id){
		
		
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
				
				
			  $this->db->select("order_items.id,order_items.recipe_id, order_items.recipe_code, order_items.recipe_name, order_items.recipe_type, order_items.option_id, order_items.net_unit_price, order_items.unit_price, order_items.quantity, order_items.warehouse_id, order_items.item_tax, order_items.tax_rate_id, order_items.tax, order_items.discount, order_items.item_discount, order_items.subtotal, order_items.serial_no, order_items.real_unit_price, order_items.sale_item_id, order_items.recipe_unit_id, order_items.recipe_unit_code, order_items.unit_quantity, order_items.comment, order_items.gst, order_items.cgst, order_items.sgst, order_items.igst,order_items.recipe_variant_id,order_items.variant", FALSE);
				$i = $this->db->get_where('order_items', array('order_items.sale_id' => $row->order, 'order_items.kitchen_id' => $row->kitchen,  'order_items.order_item_cancel_status	' => 0));
				
			
				if ($i->num_rows() > 0) {
					foreach (($i->result()) as $iow) {
						$item[$row->order][] = $iow;
					}
					$row->order_item = $item[$row->order];
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
	public function getBil($table_id, $split_id, $user_id){
		$this->db->select('GROUP_CONCAT(' . $this->db->dbprefix('orders') . '.id SEPARATOR ",") as "order",GROUP_CONCAT(' . $this->db->dbprefix('kitchen_orders') . '.id SEPARATOR ",") as kitchen, "order_item" AS order_item');
		$this->db->join('kitchen_orders', 'kitchen_orders.sale_id = orders.id');
		if(!empty($table_id)){
			$this->db->where('orders.table_id', $table_id);
		}
		$this->db->where('orders.split_id', $split_id);
		$this->db->where('orders.order_cancel_status', 0);		
		$q = $this->db->get('orders');
		// print_r($this->db->last_query());die;
		if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {

            	$order =  $q->row('order');
				$order =   explode(',',$order);

				$kitchen =  $q->row('kitchen');
				$kitchen =   explode(',',$kitchen);
		

			  $this->db->select("order_items.id,order_items.recipe_id, order_items.recipe_code, order_items.recipe_name, order_items.recipe_type, order_items.option_id, order_items.net_unit_price, order_items.unit_price,SUM(srampos_order_items.quantity) quantity , order_items.warehouse_id, order_items.item_tax, order_items.tax_rate_id, order_items.tax, order_items.discount, order_items.item_discount, SUM(srampos_order_items.subtotal) subtotal, order_items.serial_no, order_items.real_unit_price, order_items.sale_item_id, order_items.recipe_unit_id, order_items.recipe_unit_code, order_items.unit_quantity, order_items.comment, order_items.gst, order_items.cgst, order_items.sgst, order_items.igst,order_items.recipe_variant_id,order_items.variant,order_items.manual_item_discount,order_items.manual_item_discount_val,order_items.addon_id,order_items.addon_qty,order_items.item_status", FALSE);
			  $this->db->order_by('order_items.recipe_name', 'ASC');
			  
				$this->db->where_in('order_items.sale_id', $order);
				$this->db->where_in('order_items.kitchen_id', $kitchen);
				$this->db->where('order_items.order_item_cancel_status', 0);
				$this->db->group_by('CASE WHEN srampos_order_items.manual_item_discount >= 0.00 THEN ' . $this->db->dbprefix('order_items') . '.recipe_id END,srampos_order_items.recipe_variant_id',FALSE);
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
			
			$this->db->select("orders.*");
			if(!empty($table_id)){
				$this->db->where('orders.table_id', $table_id);
			}
			$this->db->where('orders.split_id', $split_id);
			$this->db->where('orders.order_cancel_status', 0);			
			$this->db->group_by('orders.split_id');			
			$o = $this->db->get('orders');
			foreach (($o->result()) as $result) {
				$data['order'][] = $result;
				
			}
            return $data;
        }
        return FALSE;
	}

	public function getBil_19_03_2019($table_id, $split_id, $user_id){
		
		
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
				
				
			  $this->db->select("order_items.id,order_items.recipe_id, order_items.recipe_code, order_items.recipe_name, order_items.recipe_type, order_items.option_id, order_items.net_unit_price, order_items.unit_price, order_items.quantity, order_items.warehouse_id, order_items.item_tax, order_items.tax_rate_id, order_items.tax, order_items.discount, order_items.item_discount, order_items.subtotal, order_items.serial_no, order_items.real_unit_price, order_items.sale_item_id, order_items.recipe_unit_id, order_items.recipe_unit_code, order_items.unit_quantity, order_items.comment, order_items.gst, order_items.cgst, order_items.sgst, order_items.igst,order_items.recipe_variant_id,order_items.variant,order_items.manual_item_discount,order_items.manual_item_discount_val,order_items.item_status", FALSE);
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
	
	public function dinegetBil($table_id, $split_id, $user_id){
		
		
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
				
				
			  $this->db->select("order_items.id,order_items.recipe_id, order_items.recipe_code, order_items.recipe_name, order_items.recipe_type, order_items.option_id, order_items.net_unit_price, order_items.unit_price, order_items.quantity, order_items.warehouse_id, order_items.item_tax, order_items.tax_rate_id, order_items.tax, order_items.discount, order_items.item_discount, order_items.subtotal, order_items.serial_no, order_items.real_unit_price, order_items.sale_item_id, order_items.recipe_unit_id, order_items.recipe_unit_code, order_items.unit_quantity, order_items.comment, order_items.gst, order_items.cgst, order_items.sgst, order_items.igst,order_items.recipe_variant_id,order_items.variant", FALSE);
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
	
	
	public function updateNewSales($order_data, $item_data){
		
		if($this->db->insert('sales', $order_data)){
			
			$sale_id = $this->db->insert_id();
			foreach ($item_data as $item) {
                $item->sale_id = $sale_id;
                $this->db->insert('sale_items', $item);
			}
			return TRUE;
		}
		return FALSE;
	}
	
	public function getSplititemlist($table_id, $split_id, $user_id){
		$this->db->select("orders.reference_no, kitchen_orders.id AS kitchen, kitchen_orders.waiter_id AS waiter, order_items.id AS item_id, order_items.recipe_id, order_items.recipe_name, order_items.recipe_type, order_items.unit_price, order_items.quantity, order_items.subtotal ")
		->join('kitchen_orders', 'kitchen_orders.sale_id = orders.id AND kitchen_orders.waiter_id = '.$user_id.'', 'left')
		->join('order_items', 'order_items.sale_id = orders.id AND order_items.kitchen_id = kitchen_orders.id', 'left');
		
		$q = $this->db->get_where('orders', array('orders.table_id' => $table_id, 'orders.split_id' => $split_id));
		$this->db->select();
		if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $data[] = $row;
            }
			
            return $data;
        }
        return FALSE;
	}
	
	public function getBillCashierPrintdata($sale_id){

     $this->db->select("sale_items.recipe_name,sale_items.quantity,sale_items.subtotal,sale_items.unit_price") 
         
             ->join('sale_items', 'sales.id = sale_items.sale_id');

        $q = $this->db->get_where('sales', array('sales.id' => $sale_id));

        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $data[] = $row;
            }
            
            return $data;
        }
       
        return FALSE;
    }
	
   public function getAllTablesbiller(){

        $this->db->select("sales.reference_no,sales.id");    
             /*->join('sale_items', 'sale_items.sale_id = sales.id');*/
        $q = $this->db->get('sales');

        /*$this->db->select("orders.reference_no, orders.split_id,orders.total,orders.total_tax, orders.shipping,orders.grand_total,orders.total_items");        
        $q = $this->db->get_where('orders', array('orders.split_id' => $split_id));*/
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $data[] = $row;
            }
            
            return $data;
        }
       
        return FALSE;

        
        /*$this->db->select("restaurant_tables.id, restaurant_tables.name, restaurant_tables.max_seats, 'split_order' ", FALSE);
        $t = $this->db->get('restaurant_tables');       
        
        if ($t->num_rows() > 0) {

            foreach ($t->result() as $row) {
                $this->db->select("restaurant_table_sessions.split_id, restaurant_table_sessions.table_id, 'order' ", FALSE)
                ->group_by('restaurant_table_sessions.split_id');
                $s = $this->db->get_where('restaurant_table_sessions', array('restaurant_table_sessions.table_id' => $row->id));
                if ($s->num_rows() > 0) {
                        
                        foreach ($s->result() as $sow) {
                                
                                 $this->db->select("orders.id, kitchen_orders.status,orders.order_type, orders.seats_id, orders.order_status, orders.reference_no, orders.date, orders.split_id, orders.table_id")
                                ->join('kitchen_orders', 'kitchen_orders.sale_id = orders.id', 'left');
                                
                                $o = $this->db->get_where('orders', array('orders.split_id' => $sow->split_id, 'orders.table_id' => $sow->table_id));
                                $split[$row->id][] = $sow;
                                if ($o->num_rows() > 0) {
                                    
                                    foreach($o->result() as $oow){
                                        $order[$sow->split_id][] = $oow;
                                    }
                                }
                                $sow->order = $order[$sow->split_id];                   
                        }
                    
                    
                    $row->split_order = $split[$row->id];
                }else{
                    $row->split_order = array();
                }
                
                $data[] = $row;

            }
            
            return $data;
        }
        
        return FALSE;*/
    } 
	public function getBilleritemlist($order_id, $table_id, $split_id, $user_id){
		
        $this->db->select("orders.reference_no, kitchen_orders.id AS kitchen, kitchen_orders.waiter_id AS waiter, order_items.id AS item_id, order_items.recipe_id, order_items.recipe_name, order_items.recipe_type, order_items.unit_price, order_items.quantity, order_items.subtotal ")
        ->join('kitchen_orders', 'kitchen_orders.sale_id = orders.id AND kitchen_orders.waiter_id = '.$user_id.'', 'left')
        ->join('order_items', 'order_items.sale_id = orders.id AND order_items.kitchen_id = kitchen_orders.id', 'left');
        
        $q = $this->db->get_where('orders', array('orders.reference_no' => $order_id, 'orders.table_id' => $table_id, 'orders.split_id' => $split_id));
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $data[] = $row;
            }
            
            return $data;
        }
        return FALSE;
    }

    
     public function getSalesData($sale_id){

        $this->db->select("sales.*");     

        $q = $this->db->get_where('sales', array('sales.id' => $sale_id));

        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $data[] = $row;
            }
            
            return $data;
        }
       
        return FALSE;
    }
	
    public function closeRegister($rid, $user_id, $data)
    {
        if (!$rid) {
            $rid = $this->session->userdata('register_id');
        }
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        if ($data['transfer_opened_bills'] == -1) {
            $this->db->delete('suspended_bills', array('created_by' => $user_id));
        } elseif ($data['transfer_opened_bills'] != 0) {
            $this->db->update('suspended_bills', array('created_by' => $data['transfer_opened_bills']), array('created_by' => $user_id));
        }
        if ($this->db->update('pos_register', $data, array('id' => $rid, 'user_id' => $user_id))) {
            return true;
        }
        return FALSE;
    }

    public function getUsers()
    {
        $q = $this->db->get_where('users', array('company_id' => NULL));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
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

    public function getrecipeByCode($code)
    {
        $this->db->like('code', $code, 'both')->order_by("code");
        $q = $this->db->get('recipe');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    public function getWHrecipe($code, $warehouse_id){
        $this->db->select('recipe.*, warehouses_recipe.quantity, categories.id as category_id, categories.name as category_name')
            ->join('warehouses_recipe', 'warehouses_recipe.recipe_id=recipe.id', 'left')
            ->join('categories', 'categories.id=recipe.category_id', 'left')
            ->group_by('recipe.id');
        $q = $this->db->get_where("recipe", array('recipe.code' => $code));
        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return FALSE;
    }
    public function getWHrecipebyid($code, $warehouse_id){
        $this->db->select('recipe.*, warehouses_recipe.quantity, categories.id as category_id, categories.name as category_name')
            ->join('warehouses_recipe', 'warehouses_recipe.recipe_id=recipe.id', 'left')
            ->join('categories', 'categories.id=recipe.category_id', 'left')
            ->group_by('recipe.id');
        $q = $this->db->get_where("recipe", array('recipe.id' => $code));
        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return FALSE;
    }    

    public function getrecipeOptions($recipe_id, $warehouse_id, $all = NULL){
        $wpv = "( SELECT option_id, warehouse_id, quantity from {$this->db->dbprefix('warehouses_recipe_variants')} WHERE recipe_id = {$recipe_id}) FWPV";
        $this->db->select('recipe_variants.id as id, recipe_variants.name as name, recipe_variants.price as price, recipe_variants.quantity as total_quantity, FWPV.quantity as quantity', FALSE)
            ->join($wpv, 'FWPV.option_id=recipe_variants.id', 'left')
            //->join('warehouses', 'warehouses.id=recipe_variants.warehouse_id', 'left')
            ->where('recipe_variants.recipe_id', $recipe_id)
            ->group_by('recipe_variants.id');

        if (! $this->Settings->overselling && ! $all) {
            $this->db->where('FWPV.warehouse_id', $warehouse_id);
            $this->db->where('FWPV.quantity >', 0);
        }
        $q = $this->db->get('recipe_variants');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
	/*public function getrecipeAddons($recipe_id, $all = NULL)
    {
      	$this->db->select("recipe_addon.*, recipe.name AS addon, recipe.price")->join('recipe', 'recipe.id = recipe_addon.addon_id')->where('recipe_addon.recipe_id', $recipe_id);
        $q = $this->db->get('recipe_addon');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }*/
	public function getrecipeAddons($recipe_id){
      	$this->db->select("recipe_addon_details.*, recipe.name AS addon, recipe.khmer_name AS addon_native_name, recipe.price")
      	->join('recipe_addon_details', 'recipe_addon_details.addon_head_id = recipe_addon.id')
      	->join('recipe', 'recipe.id = recipe_addon_details.addon_item_id','left')
      	->where('recipe_addon.recipe_id', $recipe_id);      	
        $q = $this->db->get('recipe_addon');   
//print_r($this->db->last_query());die; 		
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	public function getrecipeVariantAddons($variant, $recipe_id)
    {
      	$this->db->select("recipe_addon_details.*, recipe.name AS addon, recipe.price,recipe.khmer_name AS addon_native_name,recipe_addon.recipe_id as recipe_id,recipe_addon.variant_id")
      	->join('recipe_addon_details', 'recipe_addon_details.addon_head_id = recipe_addon.id')
      	->join('recipe', 'recipe.id = recipe_addon_details.addon_item_id','left')
      	->where('recipe_addon.recipe_id', $recipe_id)
      	->where('recipe_addon.variant_id', $variant);      	
        $q = $this->db->get('recipe_addon');
        // print_r($this->db->last_query());die;             
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }	

	public function getrecipeVariantCustomizable($variant, $recipe_id)
    {
      	$this->db->select("r.id,r.name AS customize_name, IFNULL(r.price,0) price,r.khmer_name AS customize_native_name,recipe_products.quantity,units.name as unit_name")      	
      	->join('recipe r', 'r.id = recipe_products.product_id','left')
      	->join('recipe_variants', 'recipe_variants.id = recipe_products.product_id','left')
      	->join('units', 'units.id = recipe_products.unit_id')
      	->where('recipe_products.recipe_id', $recipe_id)
      	->where('recipe_products.variant_id', $variant)    	
      	->where('recipe_products.item_customizable', 1);   
        $q = $this->db->get('recipe_products');                         
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	public function getrecipeCustomizable($recipe_id)
    {
      	$this->db->select("r.id,r.name AS customize_name, IFNULL(r.price,0) price,r.khmer_name AS customize_native_name,recipe_products.quantity,units.name as unit_name")      	
      	->join('recipe r', 'r.id = recipe_products.product_id','left')   
      	->join('units', 'units.id = recipe_products.unit_id')   	
      	->where('recipe_products.recipe_id', $recipe_id)      	   	
      	->where('recipe_products.item_customizable', 1);   
        $q = $this->db->get('recipe_products');                 
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }    
    public function getrecipeComboItems($pid, $warehouse_id)
    {
		
        $this->db->select('recipe.id as id, recipe_combo_items.item_code as code, recipe_combo_items.quantity as qty, recipe.name as name, recipe.type as type')
            ->join('recipe', 'recipe.code=recipe_combo_items.item_code', 'left')
            ->join('warehouses_recipe', 'warehouses_recipe.recipe_id=recipe.id', 'left')
            ->where('warehouses_recipe.warehouse_id', $warehouse_id)
            ->group_by('recipe_combo_items.id');
			
        $q = $this->db->get_where('recipe_combo_items', array('recipe_combo_items.recipe_id' => $pid));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
			
            return $data;
        }
        return FALSE;
    }

    public function updateOptionQuantity($option_id, $quantity)
    {
        if ($option = $this->getrecipeOptionByID($option_id)) {
            $nq = $option->quantity - $quantity;
            if ($this->db->update('recipe_variants', array('quantity' => $nq), array('id' => $option_id))) {
                return TRUE;
            }
        }
        return FALSE;
    }

    public function addOptionQuantity($option_id, $quantity)
    {
        if ($option = $this->getrecipeOptionByID($option_id)) {
            $nq = $option->quantity + $quantity;
            if ($this->db->update('recipe_variants', array('quantity' => $nq), array('id' => $option_id))) {
                return TRUE;
            }
        }
        return FALSE;
    }

    public function getrecipeOptionByID($id)
    {
        $q = $this->db->get_where('recipe_variants', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getrecipeWarehouseOptionQty($option_id, $warehouse_id)
    {
        $q = $this->db->get_where('warehouses_recipe_variants', array('option_id' => $option_id, 'warehouse_id' => $warehouse_id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function updaterecipeOptionQuantity($option_id, $warehouse_id, $quantity, $recipe_id)
    {
        if ($option = $this->getrecipeWarehouseOptionQty($option_id, $warehouse_id)) {
            $nq = $option->quantity - $quantity;
            if ($this->db->update('warehouses_recipe_variants', array('quantity' => $nq), array('option_id' => $option_id, 'warehouse_id' => $warehouse_id))) {
                $this->site->syncVariantQty($option_id, $warehouse_id);
                return TRUE;
            }
        } else {
            $nq = 0 - $quantity;
            if ($this->db->insert('warehouses_recipe_variants', array('option_id' => $option_id, 'recipe_id' => $recipe_id, 'warehouse_id' => $warehouse_id, 'quantity' => $nq))) {
                $this->site->syncVariantQty($option_id, $warehouse_id);
                return TRUE;
            }
        }
        return FALSE;
    }

    public function addSale($data = array(), $items = array(), $payments = array(), $sid = NULL)
    {
        $cost = $this->site->costing($items);
        // $this->sma->print_arrays($cost);

        if ($this->db->insert('sales', $data)) {
            $sale_id = $this->db->insert_id();

            foreach ($items as $item) {

                $item['sale_id'] = $sale_id;
                $this->db->insert('sale_items', $item);
                $sale_item_id = $this->db->insert_id();
                if ($data['sale_status'] == 'completed' && $this->site->getrecipeByID($item['recipe_id'])) {

                    $item_costs = $this->site->item_costing($item);
                    foreach ($item_costs as $item_cost) {
                        if (isset($item_cost['date']) || isset($item_cost['pi_overselling'])) {
                            $item_cost['sale_item_id'] = $sale_item_id;
                            $item_cost['sale_id'] = $sale_id;
                            $item_cost['date'] = date('Y-m-d', strtotime($data['date']));
                            if(! isset($item_cost['pi_overselling'])) {
                                $this->db->insert('costing', $item_cost);
                            }
                        } else {
                            foreach ($item_cost as $ic) {
                                $ic['sale_item_id'] = $sale_item_id;
                                $ic['sale_id'] = $sale_id;
                                $ic['date'] = date('Y-m-d', strtotime($data['date']));
                                if(! isset($ic['pi_overselling'])) {
                                    $this->db->insert('costing', $ic);
                                }
                            }
                        }
                    }
                }
            }

            if ($data['sale_status'] == 'completed') {
                $this->site->syncPurchaseItems($cost);
            }

            $msg = array();
            if (!empty($payments)) {
                $paid = 0;
                foreach ($payments as $payment) {
                    if (!empty($payment) && isset($payment['amount']) && $payment['amount'] != 0) {
                        $payment['sale_id'] = $sale_id;
                        $payment['reference_no'] = $this->site->getReference('pay');
                        if ($payment['paid_by'] == 'ppp') {
                            $card_info = array("number" => $payment['cc_no'], "exp_month" => $payment['cc_month'], "exp_year" => $payment['cc_year'], "cvc" => $payment['cc_cvv2'], 'type' => $payment['cc_type']);
                            $result = $this->paypal($payment['amount'], $card_info);
                            if (!isset($result['error'])) {
                                $payment['transaction_id'] = $result['transaction_id'];
                                $payment['date'] =  $this->site->getTransactionDate();
				$payment['paid_on'] = date('Y-m-d H:i:s');//$this->sma->fld($result['created_at']);
                                $payment['amount'] = $result['amount'];
                                $payment['currency'] = $result['currency'];
                                unset($payment['cc_cvv2']);
                                $this->db->insert('payments', $payment);
                                $this->site->updateReference('pay');
                                $paid += $payment['amount'];
                            } else {
                                $msg[] = lang('payment_failed');
                                if (!empty($result['message'])) {
                                    foreach ($result['message'] as $m) {
                                        $msg[] = '<p class="text-danger">' . $m['L_ERRORCODE'] . ': ' . $m['L_LONGMESSAGE'] . '</p>';
                                    }
                                } else {
                                    $msg[] = lang('paypal_empty_error');
                                }
                            }
                        } elseif ($payment['paid_by'] == 'stripe') {
                            $card_info = array("number" => $payment['cc_no'], "exp_month" => $payment['cc_month'], "exp_year" => $payment['cc_year'], "cvc" => $payment['cc_cvv2'], 'type' => $payment['cc_type']);
                            $result = $this->stripe($payment['amount'], $card_info);
                            if (!isset($result['error'])) {
                                $payment['transaction_id'] = $result['transaction_id'];
                                $payment['date'] = $this->sma->fld($result['created_at']);
                                $payment['amount'] = $result['amount'];
                                $payment['currency'] = $result['currency'];
                                unset($payment['cc_cvv2']);
                                $this->db->insert('payments', $payment);
                                $this->site->updateReference('pay');
                                $paid += $payment['amount'];
                            } else {
                                $msg[] = lang('payment_failed');
                                $msg[] = '<p class="text-danger">' . $result['code'] . ': ' . $result['message'] . '</p>';
                            }
                        } elseif ($payment['paid_by'] == 'authorize') {
                            $authorize_arr = array("x_card_num" => $payment['cc_no'], "x_exp_date" => ($payment['cc_month'].'/'.$payment['cc_year']), "x_card_code" => $payment['cc_cvv2'], 'x_amount' => $payment['amount'], 'x_invoice_num' => $sale_id, 'x_description' => 'Sale Ref '.$data['reference_no'].' and Payment Ref '.$payment['reference_no']);
                            list($first_name, $last_name) = explode(' ', $payment['cc_holder'], 2);
                            $authorize_arr['x_first_name'] = $first_name;
                            $authorize_arr['x_last_name'] = $last_name;
                            $result = $this->authorize($authorize_arr);
                            if (!isset($result['error'])) {
                                $payment['transaction_id'] = $result['transaction_id'];
                                $payment['approval_code'] = $result['approval_code'];
                                $payment['date'] =  $this->site->getTransactionDate();
				$payment['paid_on'] = date('Y-m-d H:i:s');//$this->sma->fld($result['created_at']);
                                unset($payment['cc_cvv2']);
                                $this->db->insert('payments', $payment);
                                $this->site->updateReference('pay');
                                $paid += $payment['amount'];
                            } else {
                                $msg[] = lang('payment_failed');
                                $msg[] = '<p class="text-danger">' . $result['msg'] . '</p>';
                            }
                        } else {
                            if ($payment['paid_by'] == 'gift_card') {
                                $this->db->update('gift_cards', array('balance' => $payment['gc_balance']), array('card_no' => $payment['cc_no']));
                                unset($payment['gc_balance']);
                            } elseif ($payment['paid_by'] == 'deposit') {
                                $customer = $this->site->getCompanyByID($data['customer_id']);
                                $this->db->update('companies', array('deposit_amount' => ($customer->deposit_amount-$payment['amount'])), array('id' => $customer->id));
                            }
                            unset($payment['cc_cvv2']);
			    $payment['date'] =$this->site->getTransactionDate();
			    $payment['paid_on'] = date('Y-m-d H:i:s');
                            $this->db->insert('payments', $payment);
                            $this->site->updateReference('pay');
                            $paid += $payment['amount'];
                        }
                    }
                }
                $this->site->syncSalePayments($sale_id);
            }

            $this->site->syncQuantity($sale_id);
            if ($sid) {
                $this->deleteBill($sid);
            }
            $this->sma->update_award_points($data['grand_total'], $data['customer_id'], $data['created_by']);
            $this->site->updateReference('pos');
            return array('sale_id' => $sale_id, 'message' => $msg);

        }

        return false;
    }
	
	public function addKitchen_Special($data = array(), $items = array(), $kitchen = array(), $warehouse_id, $user_id)
	{		

		if ($this->db->insert('orders', $data)){
				$sale_id = $this->db->insert_id();
				
				
				$msg = array();
				$msg[] = 'Your order has been success';
				$kitchen['sale_id'] = $sale_id;
				
				$this->db->insert('restaurant_table_orders', array('order_id' => $sale_id, 'table_id' => $data['table_id']));
				$this->db->insert('restaurant_table_sessions', array('table_id' => $data['table_id'],'order_id' => $sale_id, 'split_id' => $data['split_id'], 'customer_id' => $data['customer_id'], 'session_started' => date('Y-m-d H:i:s')));
				
				if($this->db->insert('kitchen_orders', $kitchen)){
					$kitchen_id = $this->db->insert_id();
					
					foreach ($items as $item) {
						$item['sale_id'] = $sale_id;
						$item['kitchen_id'] = $kitchen_id;
						$this->db->insert('order_items', $item);
						$order_item_id = $this->db->insert_id();
						$cm = $this->db->get_where('category_mapping',array('product_id'=>$item['recipe_id'],'status'=>1))->row();
						$cate['category_id'] = $cm->category_id;
						$cate['subcategory_id'] = $cm->subcategory_id;
						$cate['brand_id'] = $cm->brand_id;
						$this->site->salestock_out($item['recipe_id'],$item['quantity'],$order_item_id,$item['quantity'],$cate);
					}
					$msg[] = 'Order sent to kitchen process. wait few mintues';
				}
				
				return array('sale_id' => $sale_id, 'order_type' => $data['order_type'], 'bill_type' => 1, 'table' => $data['table_id'], 'splits' => $data['split_id'], 'bils' => 1, 'message' => $msg);
		}
		return false;
	}
	
	public function SpecialSaleandBils($sale_data, $sale_item, $bil_data, $bil_item){
		//print_r($sale_data);
		if ($this->db->insert('sales', $sale_data)){
			
			$sale_id = $this->db->insert_id();
			foreach($sale_item as $sitem){
				$sitem->sale_id = $sale_id;
				$this->db->insert('sale_items', $sitem);
			}
			
			if(!empty($sale_id)){
				
				$bil_data['sales_id'] = $sale_id;
				$this->db->insert('bils', $bil_data);
				
				$bil_id = $this->db->insert_id();
				foreach($bil_item as $bitem){
					
					unset($bitem->gst);
					unset($bitem->cgst);
					unset($bitem->sgst);
					unset($bitem->igst);
					unset($bitem->variant);
					unset($bitem->sale_id);
				
					$bitem->bil_id = $bil_id;
					$this->db->insert('bil_items', $bitem);
					
				}
				$bill_number = $this->site->Payment_dine_bill_number(0,$bil_id);
				$bill_no = $bill_number;
				$this->db->update('bils', array('bill_number' => $bill_number,'bill_sequence_number' => $bil_id), array('id' => $bil_id));	
			}
			
			$this->db->insert('sale_currency', array('sale_id' => $sale_id, 'bil_id' => $bil_id, 'special_order' => 1));
			$this->db->insert('payments', array('sale_id' => $sale_id, 'bil_id' => $bil_id, 'special_order' => 1));
			
			return true;	
		}
		return false;
		
	}
	
	public function addKitchen($data = array(), $items = array(), $kitchen = array(), $notification_array = array(), $warehouse_id, $user_id)
	{
/*echo "<pre>";
print_r($items);die;*/
		if ($this->db->insert('orders', $data)){
				$sale_id = $this->db->insert_id();
				$this->site->create_notification($notification_array);
				$msg = array();
				$msg[] = 'Your order has been success';
				$kitchen['sale_id'] = $sale_id;
				$this->db->insert('restaurant_table_orders', array('order_id' => $sale_id, 'table_id' => $data['table_id']));
				$this->db->insert('restaurant_table_sessions', array('table_id' => $data['table_id'],'order_id' => $sale_id, 'split_id' => $data['split_id'], 'customer_id' => $data['customer_id'], 'session_started' => date('Y-m-d H:i:s')));
				$print_items =[]; 				
				if($this->db->insert('kitchen_orders', $kitchen)){
					$kitchen_id = $this->db->insert_id();					
					foreach ($items as $item) {		

						$item['sale_id'] = $sale_id;
						$item['kitchen_id'] = $kitchen_id;						
						$this->db->insert('order_items', $item);						
						$order_item_id = $this->db->insert_id();						
						$item['order_item_id'] = $order_item_id;

						if($this->pos_settings->kot_print_lang_option ==1){  //if kot only for local language
							$item['image_path'] = str_replace(' ', '-',$order_item_id).'.png';
							$this->db->update('order_items', array('image_path' => str_replace(' ', '-',$order_item_id).'.png'), array('id' => $order_item_id));
							$yesdrdayfolder = date('Ymd',strtotime("-1 days"));
							if (file_exists('assets/language/'.$yesdrdayfolder)) {							
								$path   = 'assets/language/'.$yesdrdayfolder; 	
								delete_files($path, true , false, 1);												
							}					
							if($item['recipe_name_img'] != ''){
								$kotimagefolder = date('Ymd');						
								if (!file_exists('assets/language/'.$kotimagefolder)) {
								    mkdir('assets/language/'.$kotimagefolder, 0777, true);
								}
								$filename = 'assets/language/'.$kotimagefolder.'/'.str_replace(' ', '-',$order_item_id).'.png';
		            			$this->base64ToImage($item['recipe_name_img'],$filename);
		            			$this->db->update('order_items', array('recipe_name_img' => ''), array('id' => $order_item_id));
	            		   }
	            		} 

					if($item['addon_qty'] !='null' || $item['addon_qty'] !=''){
							$recipe_addon_item =[];
							$addonid = explode(',',$item['addon_id']); 
							$addonqty = explode(',', $item['addon_qty']);	
							if($item['addon_name_img'] != ''){
								$addon_name_img = explode('sivan', $item['addon_name_img']);						
							}else{
								$addon_name_img ='';
							}
							
							foreach ($addonid as $key => $addon) {	
								
							if($addon !='null' && $addonqty[$key] != 0) :
								$AddonDetails = $this->site->getaddonitemid($addon);								
								$recipeDetails = $this->pos_model->getrecipeByID($AddonDetails->addon_item_id);
								$recipe_addon_item[] = array(
									'split_id'      => $data['split_id'],
									'order_id'      => $sale_id,
									'sale_item_id'      => $recipeDetails->id,
									'order_item_id'      => $order_item_id,
									'addon_id'      => $addon,
									'price'      => $recipeDetails->price ? $recipeDetails->price : 0,
									'qty'      => $addonqty[$key],
									'addon_name_img' => $addon_name_img[$key] ? $addon_name_img[$key] :'',
									'subtotal'      => ($addonqty[$key] * $recipeDetails->price),
								);
							endif;	
							}											
							foreach ($recipe_addon_item as $recipe_addon) {
								$this->db->insert('addon_sale_items', $recipe_addon);
								$addon_sale_items_id  = $this->db->insert_id();
								if($kit_item['addon_id'] !='null'){	
									if($this->pos_settings->kot_print_lang_option ==1){  //if kot only for local language
										$this->db->update('addon_sale_items', array('image_path' => str_replace(' ', '-',$order_item_id.$addon_sale_items_id).'.png'), array('id' => $addon_sale_items_id));

										$filename = 'assets/language/'.$kotimagefolder.'/'.str_replace(' ', '-',$order_item_id.$addon_sale_items_id).'.png';								
				            			$this->base64ToImage($recipe_addon['addon_name_img'],$filename);
				            			$this->db->update('addon_sale_items', array('addon_name_img' => ''), array('id' => $addon_sale_items_id));
				            		}	
		            			}
							}
					}
					/*addon array*/	
						$cm = $this->db->get_where('category_mapping',array('product_id'=>$item['recipe_id'],'status'=>1))->row();
						$cate['category_id'] = $cm->id;
						$cate['category_id'] = $cm->category_id;
						$cate['subcategory_id'] = $cm->subcategory_id;
						$cate['brand_id'] = $cm->brand_id;
						// $this->site->salestock_out($item['recipe_id'],$item['quantity'],$order_item_id,$item['quantity'],$cate);
						/*echo "<pre>";
						print_r($cate);die;*/
						if($this->Settings->procurment == 1){
							if($item['recipe_type'] =='standard' || $item['recipe_type'] =='production'){
								//var_dump($item['recipe_variant_id']);die;
								$this->site->updateStockMaster_new($item['recipe_id'],$item['recipe_variant_id'],$item['quantity'],$cate,$order_item_id);
							}elseif($item['recipe_type'] =='quick_service'){
								$this->siteprocurment->production_salestock_out($item['recipe_id'],$item['recipe_variant_id'],$item['quantity'],$kit_item['recipe_variant_id']);
							}	
						}
						$print_items[] = $item;
					}
					$msg[] = 'Order sent to kitchen process. wait few mintues';					
				}	//die;

				$this->site->updateTableStatus($data['table_id'],1,$user_id);				 
				$this->db->select('orders.*, restaurant_tables.name AS table_name');
				$this->db->join('restaurant_tables', 'restaurant_tables.id = orders.table_id', 'left');		
			   $this->db->where('orders.id', $sale_id);
				$t = $this->db->get('orders');
				$kit = array();
				$consolid_kit = array();
				if ($t->num_rows() > 0) {
					$orders_details =  $t->row();
					$kit['orders_details'] = $orders_details;
					$consolid_kit['orders_details'] = $orders_details;
					$consolidate_kitchens_kot['orders_details'] = $orders_details;
				}	
/*Consolidate kot for table area wise */ 
if($this->pos_settings->kot_enable_disable == 1){
	if($this->pos_settings->consolidated_kot_print_option == 0){
			if($this->pos_settings->consolidated_kot_print != 0){
				$table_id = $consolid_kit['orders_details']->table_id;
				$consolid_kot_print_details = $this->db->select("printers.*")
				->join('restaurant_areas', 'restaurant_areas.printer_id = printers.id','left')
				->join('restaurant_tables', 'restaurant_tables.area_id = restaurant_areas.id','left')							
				->where('restaurant_tables.id', $table_id)
				->get('printers');
				if ($consolid_kot_print_details->num_rows() > 0) {
				  $consolid_kit['consolid_kot_print_details'][] =  $consolid_kot_print_details->row();
				 }else{
				 	$consolid_kit['consolid_kot_print_details'] =array();
				 }

				$consolid_kit_item =array();
				foreach($print_items as $key => $kit_item){
					// $printer_order_item_id = $this->site->get_printer_order_item_id($sale_id,$kit_item['recipe_id']);
					if($kit_item['kitchen_type_id'] != 0) :
						/*var_dump($kit_item['recipe_id']);*/
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
								$consolid_kit_item[$key]['recipe_name'] = $kit_item['recipe_name'];;
								
							}else{
								$consolid_kit_item[$key]['recipe_name'] = $kit_item['recipe_name'];
							}
						}else{
								$consolid_kit_item[$key]['recipe_name'] = $kit_item['recipe_name'];
						}
						
						$consolid_kit_item[$key]['en_recipe_name'] = $kit_item['recipe_name'];
						$consolid_kit_item[$key]['comment'] = $kit_item['comment'];
						$consolid_kit_item[$key]['en_variant_name'] = $kit_item['variant'] ? $kit_item['variant'] :'';

						if($this->pos_settings->kot_print_lang_option ==1){
							$kotimagefolder = date('Ymd');	
							$consolid_kit_item[$key]['khmer_recipe_image'] = (!empty($kit_item['image_path']) && file_exists('assets/language/'.$kotimagefolder.'/'.$kit_item['image_path']))? (base_url().'assets/language/'.$kotimagefolder.'/'.$kit_item['image_path']) : '';
						}else{
							$consolid_kit_item[$key]['khmer_recipe_image'] =(!empty($khmer_image) && file_exists('assets/language/'.$khmer_image))? (base_url().'assets/language/'.$khmer_image) : '';
						}

						/*if($kit_item['recipe_variant_id']!= 0){
							$consolid_kit_item[$key]['en_variant_name'] = $kit_item['variant'];
							$variant_localname_image = $this->site->getitemvariantlocalnameimage($kit_item['recipe_id'],$kit_item['recipe_variant_id']);
							$consolid_kit_item[$key]['khmer_recipe_image'] = (!empty($variant_localname_image) && file_exists('assets/language/'.$variant_localname_image))? (base_url().'assets/language/'.$variant_localname_image) : '';
						}else{									
							$consolid_kit_item[$key]['khmer_recipe_image'] = (!empty($khmer_image) && file_exists('assets/language/'.$khmer_image))? (base_url().'assets/language/'.$khmer_image) : '';
						} 	*/											
						$consolid_kit_item[$key]['quantity'] = $kit_item['quantity'];
						$consolid_kit_item[$key]['get_item_name'] = $get_item->name;
						$consolid_kit_item[$key]['total_get_quantity'] = $get_item->total_get_quantity;
						$addondetails ='';
						if($kit_item['addon_id'] !='null'){					
								$addondetails = $this->site->getAddonByRecipeidAndOrderitemid_kot($kit_item['recipe_id'],$kit_item['order_item_id']);
							}		
										
						$consolid_kit_item[$key]['addons'] = $addondetails;
				   endif;		
				}						
				$consolid_kit['consolid_kitchens'] = $consolid_kit_item;
		    }else{
			$consolid_kit['consolid_kitchens'] = array();
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

							if($this->pos_settings->kot_print_lang_option ==1){
								$kotimagefolder = date('Ymd');	
								$consolidate_kitchen_row->kit_o[$key]['khmer_recipe_image'] = (!empty($kit_item['image_path']) && file_exists('assets/language/'.$kotimagefolder.'/'.$kit_item['image_path']))? (base_url().'assets/language/'.$kotimagefolder.'/'.$kit_item['image_path']) : '';
							}else{
								$consolidate_kitchen_row->kit_o[$key]['khmer_recipe_image'] =(!empty($khmer_image) && file_exists('assets/language/'.$khmer_image))? (base_url().'assets/language/'.$khmer_image) : '';
							}

							/*if($kit_item['recipe_variant_id']!= 0){
								$consolidate_kitchen_row->kit_o[$key]['en_variant_name'] = $kit_item['variant'];
								$variant_localname_image = $this->site->getitemvariantlocalnameimage($kit_item['recipe_id'],$kit_item['recipe_variant_id']);
								$consolidate_kitchen_row->kit_o[$key]['khmer_recipe_image'] = (!empty($variant_localname_image) && file_exists('assets/language/'.$variant_localname_image))? (base_url().'assets/language/'.$variant_localname_image) : '';
							}else{									
								$consolidate_kitchen_row->kit_o[$key]['khmer_recipe_image'] = (!empty($khmer_image) && file_exists('assets/language/'.$khmer_image))? (base_url().'assets/language/'.$khmer_image) : '';
							} */
							// $consolidate_kitchen_row->kit_o[$key]['khmer_recipe_image'] = (!empty($khmer_image) && file_exists('assets/language/'.$khmer_image))? (base_url().'assets/language/'.$khmer_image) : '';	
							$consolidate_kitchen_row->kit_o[$key]['quantity'] = $kit_item['quantity'];
							$consolidate_kitchen_row->kit_o[$key]['get_item_name'] = $get_item->name;
							$consolidate_kitchen_row->kit_o[$key]['total_get_quantity'] = $get_item->total_get_quantity;	
							$addondetails ='';
							if($kit_item['addon_id'] !='null'){	
								$addondetails = $this->site->getAddonByRecipeidAndOrderitemid_kot($kit_item['recipe_id'],$kit_item['order_item_id']);
							}
							$consolidate_kitchen_row->kit_o[$key]['addons'] = $addondetails;								
						}
					}
					$consolidate_kitchens_kot['kitchens'][] = $consolidate_kitchen_row;
				}					
			}else{
				$consolidate_kitchens_kot[] = '';	
			}
	}
				
/*Consolidate kot only one single priter */	
/*items kot with mapped kichens its common for all clients*/
				$kitchen_details = $this->db->select('restaurant_kitchens.id,restaurant_kitchens.printer_id,  restaurant_kitchens.name')->get('restaurant_kitchens');
				if ($kitchen_details->num_rows() > 0) {					
					foreach (($kitchen_details->result()) as $kitchen_row) {
						
						$printers_details = $this->db->select('*')->where('id', $kitchen_row->printer_id)->get('printers');	
						if ($printers_details->num_rows() > 0) {
							$kitchen_row->printers_details =  $printers_details->row();
						}
						
						foreach($print_items as $key => $kit_item){
							if($kitchen_row->id == $kit_item['kitchen_type_id']){
								
								// $addons = $this->site->getAddonByRecipe($kit_item['recipe_id'], $kit_item['addon_id']);
								$get_item =  $this->site->getrecipeByID($kit_item['get_item']);									
								$kitchen_row->kit_o[$key]['recipe_id'] = $kit_item['recipe_id'];								
								if($this->Settings->user_language == 'khmer' || true){
									$khmer_name = $this->site->getrecipeKhmer($kit_item['recipe_id']);	
									if($kit_item['recipe_variant_id']!= 0){
										$khmer_image = $this->site->getitemvariantlocalnameimage($kit_item['recipe_id'],$kit_item['recipe_variant_id']);
									}else{
										$khmer_image = $this->site->getrecipeKhmerimage($kit_item['recipe_id']);
									}

									if(!empty($khmer_name)){
										$kitchen_row->kit_o[$key]['recipe_name'] = $kit_item['recipe_name'];;
									}else{
										$kitchen_row->kit_o[$key]['recipe_name'] = $kit_item['recipe_name'];
									}
								}else{
									$kitchen_row->kit_o[$key]['recipe_name'] = $kit_item['recipe_name'];
								}
								$kitchen_row->kit_o[$key]['en_variant_name'] = $kit_item['variant'] ? $kit_item['variant'] :'';								
								// $kitchen_row->kit_o[$key]['khmer_name'] = iconv(mb_detect_encoding($khmer_name, mb_detect_order(), true), "UTF-8", $khmer_name);
								
								/*if($kit_item['recipe_variant_id']!= 0){
									$kitchen_row->kit_o[$key]['en_variant_name'] = $kit_item['variant'];
									$variant_localname_image = $this->site->getitemvariantlocalnameimage($kit_item['recipe_id'],$kit_item['recipe_variant_id']);
									$kitchen_row->kit_o[$key]['khmer_recipe_image'] = (!empty($variant_localname_image) && file_exists('assets/language/'.$variant_localname_image))? (base_url().'assets/language/'.$variant_localname_image) : '';
								}else{									
									$kitchen_row->kit_o[$key]['khmer_recipe_image'] = (!empty($khmer_image) && file_exists('assets/language/'.$khmer_image))? (base_url().'assets/language/'.$khmer_image) : '';
								} */
								/*echo "<pre>";
								print_r($kit_item);die;*/
								if($this->pos_settings->kot_print_lang_option ==1){
									$kotimagefolder = date('Ymd');	
									$kitchen_row->kit_o[$key]['khmer_recipe_image'] = (!empty($kit_item['image_path']) && file_exists('assets/language/'.$kotimagefolder.'/'.$kit_item['image_path']))? (base_url().'assets/language/'.$kotimagefolder.'/'.$kit_item['image_path']) : '';
								}else{
									$kitchen_row->kit_o[$key]['khmer_recipe_image'] =(!empty($khmer_image) && file_exists('assets/language/'.$khmer_image))? (base_url().'assets/language/'.$khmer_image) : '';
								}

								$kitchen_row->kit_o[$key]['en_recipe_name'] = $kit_item['recipe_name'];
								$kitchen_row->kit_o[$key]['en_variant_name'] = $kit_item['variant'];
								$kitchen_row->kit_o[$key]['comment'] = $kit_item['comment'];
								$kitchen_row->kit_o[$key]['quantity'] = $kit_item['quantity'];
								$kitchen_row->kit_o[$key]['get_item_name'] = $get_item->name;
								$kitchen_row->kit_o[$key]['total_get_quantity'] = $get_item->total_get_quantity;
								$addondetails ='';
								if($kit_item['addon_id'] !='null'){					
									$addondetails = $this->site->getAddonByRecipeidAndOrderitemid_kot($kit_item['recipe_id'],$kit_item['order_item_id']);
								}        								
								$kitchen_row->kit_o[$key]['addons'] = $addondetails;

								$unwanted_ingredientsdetails ='';
								if($kit_item['unwanted_ingredients'] !=''){					
									$unwanted_ingredientsdetails = $this->site->getunwanted_ingredientfor_kot($kit_item['recipe_id'],$kit_item['order_item_id'],$kit_item['unwanted_ingredients']);
								}                                
								$kitchen_row->kit_o[$key]['unwanted_ingredients'] = $unwanted_ingredientsdetails;
							}
						}
						$kit['kitchens'][] = $kitchen_row;
					}
					
				}else{
					$kit[] = '';	
				}
/*items kot with mapped kichens its common for all clients*/
/*kitchen consolidate start */
			$kitchen_details = $this->db->select('restaurant_kitchens.id,restaurant_kitchens.kitchen_consolid_printer_id,  restaurant_kitchens.name')->where('kitchen_consolid_printer_id !=', 0)->get('restaurant_kitchens');

				if ($kitchen_details->num_rows() > 0) {					
					foreach (($kitchen_details->result()) as $kitchen_row) {
						
						$printers_details = $this->db->select('*')->where('id', $kitchen_row->kitchen_consolid_printer_id)->get('printers');
						
						if ($printers_details->num_rows() > 0) {
							$kitchen_row->printers_details =  $printers_details->row();
						}						
						foreach($print_items as $key => $kit_item){
							if($kitchen_row->id == $kit_item['kitchen_type_id']){
								
								$addons = $this->site->getAddonByRecipe($kit_item['recipe_id'], $kit_item['addon_id']);
								$get_item =  $this->site->getrecipeByID($kit_item['get_item']);
									
								$kitchen_row->kit_o[$key]['recipe_id'] = $kit_item['recipe_id'];
								
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

								$kitchen_row->kit_o[$key]['en_variant_name'] = $kit_item['variant'] ? $kit_item['variant'] :'';

								$kitchen_row->kit_o[$key]['en_recipe_name'] = $kit_item['recipe_name'];
								// $kitchen_row->kit_o[$key]['khmer_name'] = iconv(mb_detect_encoding($khmer_name, mb_detect_order(), true), "UTF-8", $khmer_name);

								$kitchen_row->kit_o[$key]['comment'] = $kit_item['comment'];
								/*if($kit_item['recipe_variant_id']!= 0){
									$kitchen_row->kit_o[$key]['en_variant_name'] = $kit_item['variant'];
									$variant_localname_image = $this->site->getitemvariantlocalnameimage($kit_item['recipe_id'],$kit_item['recipe_variant_id']);
									$kitchen_row->kit_o[$key]['khmer_recipe_image'] = (!empty($variant_localname_image) && file_exists('assets/language/'.$variant_localname_image))? (base_url().'assets/language/'.$variant_localname_image) : '';
								}else{									
									$kitchen_row->kit_o[$key]['khmer_recipe_image'] = (!empty($khmer_image) && file_exists('assets/language/'.$khmer_image))? (base_url().'assets/language/'.$khmer_image) : '';
								} 	*/
								if($this->pos_settings->kot_print_lang_option ==1){
									$kotimagefolder = date('Ymd');	
									$kitchen_row->kit_o[$key]['khmer_recipe_image'] = (!empty($kit_item['image_path']) && file_exists('assets/language/'.$kotimagefolder.'/'.$kit_item['image_path']))? (base_url().'assets/language/'.$kotimagefolder.'/'.$kit_item['image_path']) : '';
								}else{
									$kitchen_row->kit_o[$key]['khmer_recipe_image'] =(!empty($khmer_image) && file_exists('assets/language/'.$khmer_image))? (base_url().'assets/language/'.$khmer_image) : '';
								}

								$kitchen_row->kit_o[$key]['quantity'] = $kit_item['quantity'];
								$kitchen_row->kit_o[$key]['get_item_name'] = $get_item->name;
								$kitchen_row->kit_o[$key]['total_get_quantity'] = $get_item->total_get_quantity;
								if($kit_item['addon_id'] !='null'){					
									$addondetails = $this->site->getAddonByRecipeidAndOrderitemid_kot($kit_item['recipe_id'],$kit_item['order_item_id']);
								}								
								$kitchen_row->kit_o[$key]['addons'] = $addondetails ? $addondetails :'';
								//$kitchen_row->kit_o[$kitchen_row->id][] = $kit_item['quantity'];
							}
						}
						$consolidate_kitchens_kot['kitchens'][] = $kitchen_row;
					}
				}else{
					$consolidate_kitchens_kot[] = '';	
				}
}
/*
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
								$consolidate_kitchen_row->kit_o[$key]['khmer_recipe_image'] = (!empty($khmer_image) && file_exists('assets/language/'.$khmer_image))? (base_url().'assets/language/'.$khmer_image) : '';								
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
				}*/
				/*kitchen consolidate*/		
				/*echo "<pre>";
				print_r($kit);*/
				// die;
				// print_r($this->db->error());die;				
				return array('sale_id' => $sale_id, 'message' => $msg, 'kitchen_data' => $kit, 'consolid_kitchen_data' => $consolid_kit, 'consolidate_kitchens_kot' => $consolidate_kitchens_kot);
		}		
		// print_r($this->db->error());die;
		return false;
	}

function base64ToImage($imageData,$filename){
    $data = 'data:image/png;base64,AAAFBfj42Pj4';
    list($type, $imageData) = explode(';', $imageData);
    list(,$extension) = explode('/',$type);
    list(,$imageData)      = explode(',', $imageData);
    //$fileName = uniqid().'.'.$extension;
    $imageData = base64_decode($imageData);
    file_put_contents($filename, $imageData);
}

    public function getrecipeoneByCode($code)
    {
        $q = $this->db->get_where('recipe', array('code' => $code), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getrecipeByName($name)
    {
        $q = $this->db->get_where('recipe', array('name' => $name), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return FALSE;
    }

    public function getAllBillerCompanies()
    {
        $q = $this->db->get_where('companies', array('group_name' => 'biller'));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
    }

    public function getAllCustomerCompanies()
    {
        $q = $this->db->get_where('companies', array('group_name' => 'customer'));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
    }

    public function getCompanyByID($id)
    {
        $q = $this->db->get_where('companies', array('id' => $id), 1);        
        if ($q->num_rows() > 0) {        	
            return $q->row();
        }
        return FALSE;
    }

    public function getAllrecipe()
    {
        $q = $this->db->query('SELECT * FROM recipe ORDER BY id');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
    }

    public function getrecipeByID($id)
    {

        $q = $this->db->get_where('recipe', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return FALSE;
    }

    public function getAllTaxRates()
    {
        $q = $this->db->get('tax_rates');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }
    public function getAllSericeCharges()
    {
        $q = $this->db->get('service_charge');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }    

    public function getTaxRateByID($id)
    {

        $q = $this->db->get_where('tax_rates', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return FALSE;
    }

    public function updaterecipeQuantity($recipe_id, $warehouse_id, $quantity)
    {

        if ($this->addQuantity($recipe_id, $warehouse_id, $quantity)) {
            return true;
        }

        return false;
    }

    public function addQuantity($recipe_id, $warehouse_id, $quantity)
    {
        if ($warehouse_quantity = $this->getrecipeQuantity($recipe_id, $warehouse_id)) {
            $new_quantity = $warehouse_quantity['quantity'] - $quantity;
            if ($this->updateQuantity($recipe_id, $warehouse_id, $new_quantity)) {
                $this->site->syncrecipeQty($recipe_id, $warehouse_id);
                return TRUE;
            }
        } else {
            if ($this->insertQuantity($recipe_id, $warehouse_id, -$quantity)) {
                $this->site->syncrecipeQty($recipe_id, $warehouse_id);
                return TRUE;
            }
        }
        return FALSE;
    }

    public function insertQuantity($recipe_id, $warehouse_id, $quantity)
    {
        if ($this->db->insert('warehouses_recipe', array('recipe_id' => $recipe_id, 'warehouse_id' => $warehouse_id, 'quantity' => $quantity))) {
            return true;
        }
        return false;
    }

    public function updateQuantity($recipe_id, $warehouse_id, $quantity)
    {
        if ($this->db->update('warehouses_recipe', array('quantity' => $quantity), array('recipe_id' => $recipe_id, 'warehouse_id' => $warehouse_id))) {
            return true;
        }
        return false;
    }

    public function getrecipeQuantity($recipe_id, $warehouse)
    {
        $q = $this->db->get_where('warehouses_recipe', array('recipe_id' => $recipe_id, 'warehouse_id' => $warehouse), 1);
        if ($q->num_rows() > 0) {
            return $q->row_array(); //$q->row();
        }
        return FALSE;
    }

    public function getItemByID($id)
    {
        $q = $this->db->get_where('sale_items', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getAllSales()
    {
        $q = $this->db->get('sales');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function sales_count()
    {
        return $this->db->count_all("sales");
    }

    public function fetch_sales($limit, $start)
    {
        $this->db->limit($limit, $start);
        $this->db->order_by("id", "desc");
        $query = $this->db->get("sales");

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getAllInvoiceItems($sale_id)
    {
        if ($this->pos_settings->item_order == 0) {
            $this->db->select('bil_items.*, tax_rates.code as tax_code, tax_rates.name as tax_name, tax_rates.rate as tax_rate, recipe_variants.name as variant, recipe.details as details, recipe.khmer_name, recipe.hsn_code as hsn_code')
            ->join('recipe', 'recipe.id=bil_items.recipe_id', 'left')
            ->join('tax_rates', 'tax_rates.id=bil_items.tax_rate_id', 'left')
            ->join('recipe_variants', 'recipe_variants.id=bil_items.option_id', 'left')
            ->group_by('bil_items.id')
            ->order_by('id', 'asc');
        } elseif ($this->pos_settings->item_order == 1) {
            $this->db->select('bil_items.*, tax_rates.code as tax_code, tax_rates.name as tax_name, tax_rates.rate as tax_rate, recipe_variants.name as variant, categories.id as category_id, categories.name as category_name, recipe.details as details, recipe.khmer_name, recipe.hsn_code as hsn_code')
            ->join('tax_rates', 'tax_rates.id=bil_items.tax_rate_id', 'left')
            ->join('recipe_variants', 'recipe_variants.id=bil_items.option_id', 'left')
            ->join('recipe', 'recipe.id=bil_items.recipe_id', 'left')
            ->join('categories', 'categories.id=recipe.category_id', 'left')
            ->group_by('bil_items.id')
            ->order_by('categories.id', 'asc');
        }

        $q = $this->db->get_where('bil_items', array('bil_id' => $sale_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
	 public function getAllInvoiceItems_archival($sale_id){
        if ($this->pos_settings->item_order == 0) {
            $this->db->select('bil_items_archival.*, tax_rates.code as tax_code, tax_rates.name as tax_name, tax_rates.rate as tax_rate, recipe_variants.name as variant, recipe.details as details, recipe.khmer_name, recipe.hsn_code as hsn_code')
            ->join('recipe', 'recipe.id=bil_items_archival.recipe_id', 'left')
            ->join('tax_rates', 'tax_rates.id=bil_items_archival.tax_rate_id', 'left')
            ->join('recipe_variants', 'recipe_variants.id=bil_items_archival.option_id', 'left')
            ->group_by('bil_items_archival.id')
            ->order_by('id', 'asc');
        } elseif ($this->pos_settings->item_order == 1) {
            $this->db->select('bil_items_archival.*, tax_rates.code as tax_code, tax_rates.name as tax_name, tax_rates.rate as tax_rate, recipe_variants.name as variant, categories.id as category_id, categories.name as category_name, recipe.details as details, recipe.khmer_name, recipe.hsn_code as hsn_code')
            ->join('tax_rates', 'tax_rates.id=bil_items_archival.tax_rate_id', 'left')
            ->join('recipe_variants', 'recipe_variants.id=bil_items_archival.option_id', 'left')
            ->join('recipe', 'recipe.id=bil_items_archival.recipe_id', 'left')
            ->join('categories', 'categories.id=recipe.category_id', 'left')
            ->group_by('bil_items_archival.id')
            ->order_by('categories.id', 'asc');
        }
        $q = $this->db->get_where('bil_items_archival', array('bil_id' => $sale_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getSuspendedSaleItems($id)
    {
        $q = $this->db->get_where('suspended_items', array('suspend_id' => $id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    public function getSaleItems($id)
    {
        $q = $this->db->get_where('sale_items', array('sale_id' => $id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    public function getSuspendedSales($user_id = NULL)
    {
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        $q = $this->db->get_where('suspended_bills', array('created_by' => $user_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
    }


    public function getOpenBillByID($id)
    {

        $q = $this->db->get_where('suspended_bills', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return FALSE;
    }

    public function getInvoiceByID($id)
    {
    	$this->db->select("bils.*,tax_rates.name as tax_name, tax_rates.rate as tax_rate")
	    ->join('tax_rates', 'tax_rates.id = bils.tax_id','left')
	    ->where('bils.id', $id);
		$q = $this->db->get('bils');
        
        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return FALSE;
    }
	 public function getInvoiceByID_archival($id){
    	$this->db->select("bils_archival.*,tax_rates.name as tax_name, tax_rates.rate as tax_rate")
	    ->join('tax_rates', 'tax_rates.id = bils_archival.tax_id','left')
	    ->where('bils_archival.id', $id);
		$q = $this->db->get('bils_archival');
        
        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return FALSE;
    }

    public function getDineFromConsolidatebill($bill_no)
    {
    	$this->db->select("bils.id")	    
	    ->where('bils.order_type', 1)
	    ->where('bils.bill_number', $bill_no);
		$q = $this->db->get('bils');
        
        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return FALSE;
    }

    public function getBBQFromConsolidatebill($bill_no)
    {
    	$this->db->select("bils.id")	    
	    ->where('bils.order_type', 4)
	    ->where('bils.bill_number', $bill_no);
		$q = $this->db->get('bils');
        
        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return FALSE;
    }
    

    public function bills_count()
    {
        if (!$this->Owner && !$this->Admin) {
            $this->db->where('created_by', $this->session->userdata('user_id'));
        }
        return $this->db->count_all_results("suspended_bills");
    }

    public function fetch_bills($limit, $start)
    {
        if (!$this->Owner && !$this->Admin) {
            $this->db->where('created_by', $this->session->userdata('user_id'));
        }
        $this->db->limit($limit, $start);
        $this->db->order_by("id", "asc");
        $query = $this->db->get("suspended_bills");

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getTodaySales()
    {
        $sdate = date('Y-m-d 00:00:00');
        $edate = date('Y-m-d 23:59:59');
        $this->db->select('SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid', FALSE)
            ->join('sales', 'sales.id=payments.sale_id', 'left')
            ->where('type', 'received')->where('sales.date >=', $sdate)->where('payments.date <=', $edate);

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getCosting()
    {
        $date = date('Y-m-d');
        $this->db->select('SUM( COALESCE( purchase_unit_cost, 0 ) * quantity ) AS cost, SUM( COALESCE( sale_unit_price, 0 ) * quantity ) AS sales, SUM( COALESCE( purchase_net_unit_cost, 0 ) * quantity ) AS net_cost, SUM( COALESCE( sale_net_unit_price, 0 ) * quantity ) AS net_sales', FALSE)
            ->where('date', $date);

        $q = $this->db->get('costing');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getTodayCCSales()
    {
        $date = date('Y-m-d 00:00:00');
        $this->db->select('COUNT(' . $this->db->dbprefix('payments') . '.id) as total_cc_slips, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid', FALSE)
            ->join('sales', 'sales.id=payments.sale_id', 'left')
            ->where('type', 'received')->where('payments.date >', $date)->where('paid_by', 'CC');

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getTodayCashSales()
    {
        $date = date('Y-m-d 00:00:00');
        $this->db->select('SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid', FALSE)
            ->join('sales', 'sales.id=payments.sale_id', 'left')
            ->where('type', 'received')->where('payments.date >', $date)->where('paid_by', 'cash');

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getTodayRefunds()
    {
        $date = date('Y-m-d 00:00:00');
        $this->db->select('SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS returned', FALSE)
            ->join('sales', 'sales.id=payments.return_id', 'left')
            ->where('type', 'returned')->where('payments.date >', $date);

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getTodayExpenses()
    {
        $date = date('Y-m-d 00:00:00');
        $this->db->select('SUM( COALESCE( amount, 0 ) ) AS total', FALSE)
            ->where('date >', $date);

        $q = $this->db->get('expenses');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getTodayCashRefunds()
    {
        $date = date('Y-m-d 00:00:00');
        $this->db->select('SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS returned', FALSE)
            ->join('sales', 'sales.id=payments.return_id', 'left')
            ->where('type', 'returned')->where('payments.date >', $date)->where('paid_by', 'cash');

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getTodayChSales()
    {
        $date = date('Y-m-d 00:00:00');
        $this->db->select('COUNT(' . $this->db->dbprefix('payments') . '.id) as total_cheques, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid', FALSE)
            ->join('sales', 'sales.id=payments.sale_id', 'left')
            ->where('type', 'received')->where('payments.date >', $date)->where('paid_by', 'Cheque');

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getTodayPPPSales()
    {
        $date = date('Y-m-d 00:00:00');
        $this->db->select('COUNT(' . $this->db->dbprefix('payments') . '.id) as total_cheques, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid', FALSE)
            ->join('sales', 'sales.id=payments.sale_id', 'left')
            ->where('type', 'received')->where('payments.date >', $date)->where('paid_by', 'ppp');

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getTodayStripeSales()
    {
        $date = date('Y-m-d 00:00:00');
        $this->db->select('COUNT(' . $this->db->dbprefix('payments') . '.id) as total_cheques, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid', FALSE)
            ->join('sales', 'sales.id=payments.sale_id', 'left')
            ->where('type', 'received')->where('payments.date >', $date)->where('paid_by', 'stripe');

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getTodayAuthorizeSales()
    {
        $date = date('Y-m-d 00:00:00');
        $this->db->select('COUNT(' . $this->db->dbprefix('payments') . '.id) as total_cheques, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid', FALSE)
            ->join('sales', 'sales.id=payments.sale_id', 'left')
            ->where('type', 'received')->where('payments.date >', $date)->where('paid_by', 'authorize');

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getRegisterSales($date, $user_id = NULL)
    {
        if (!$date) {
            $date = $this->session->userdata('register_open_time');
        }
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        $this->db->select('SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid', FALSE)
            ->join('sales', 'sales.id=payments.sale_id', 'left')
            ->where('type', 'received')->where('payments.date >', $date);
        $this->db->where('payments.created_by', $user_id);

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }


    public function getRegisterCCSales($date, $user_id = NULL)
    {
        if (!$date) {
            $date = $this->session->userdata('register_open_time');
        }

        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }

        $this->db->select('COUNT(' . $this->db->dbprefix('payments') . '.id) as total_cc_slips, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid', FALSE)
            ->join('bils', 'bils.id=payments.bill_id', 'left')
            ->where('type', 'received')->where('payments.date >', $date)->where('payments.paid_by', 'CC');
        $this->db->where('payments.created_by', $user_id);

        $q = $this->db->get('payments');
        // print_r($this->db->error());die;
        if ($q->num_rows() > 0) {

            return $q->row();

        }
        return false;
    }

    public function getRegisterCashSales($date, $user_id = NULL)
    {
        if (!$date) {
            $date = $this->session->userdata('register_open_time');
        }
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        $this->db->select('SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid', FALSE)
            ->join('bils', 'bils.id=payments.bill_id', 'left')
            ->where('type', 'received')->where('payments.date >', $date)->where('payments.paid_by', 'cash');
        $this->db->where('payments.created_by', $user_id);

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getRegisterRefunds($date, $user_id = NULL)
    {
        if (!$date) {
            $date = $this->session->userdata('register_open_time');
        }
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        $this->db->select('SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS returned', FALSE)
            ->join('bils', 'bils.id=payments.bill_id', 'left')
            ->where('type', 'returned')->where('payments.date >', $date);
        $this->db->where('payments.created_by', $user_id);

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getRegisterCashRefunds($date, $user_id = NULL)
    {
        if (!$date) {
            $date = $this->session->userdata('register_open_time');
        }
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        $this->db->select('SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS returned', FALSE)
            ->join('bils', 'bils.id=payments.bill_id', 'left')
            ->where('type', 'returned')->where('payments.date >', $date)->where('payments.paid_by', 'cash');
        $this->db->where('payments.created_by', $user_id);

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getRegisterExpenses($date, $user_id = NULL)
    {
        if (!$date) {
            $date = $this->session->userdata('register_open_time');
        }
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        $this->db->select('SUM( COALESCE( amount, 0 ) ) AS total', FALSE)
            ->where('date >', $date);
        $this->db->where('created_by', $user_id);

        $q = $this->db->get('expenses');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getRegisterChSales($date, $user_id = NULL)
    {
        if (!$date) {
            $date = $this->session->userdata('register_open_time');
        }
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        $this->db->select('COUNT(' . $this->db->dbprefix('payments') . '.id) as total_cheques, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid', FALSE)
            ->join('bils', 'bils.id=payments.bill_id', 'left')
            ->where('type', 'received')->where('payments.date >', $date)->where('payments.paid_by', 'Cheque');
        $this->db->where('payments.created_by', $user_id);

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getRegisterGCSales($date, $user_id = NULL)
    {
        if (!$date) {
            $date = $this->session->userdata('register_open_time');
        }
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        $this->db->select('COUNT(' . $this->db->dbprefix('payments') . '.id) as total_cheques, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid', FALSE)
            ->join('bils', 'bils.id=payments.bill_id', 'left')
            ->where('type', 'received')->where('payments.date >', $date)->where('payments.paid_by', 'gift_card');
        $this->db->where('payments.created_by', $user_id);

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getRegisterPPPSales($date, $user_id = NULL)
    {
        if (!$date) {
            $date = $this->session->userdata('register_open_time');
        }
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        $this->db->select('COUNT(' . $this->db->dbprefix('payments') . '.id) as total_cheques, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid', FALSE)
            ->join('bils', 'bils.id=payments.bill_id', 'left')
            ->where('type', 'received')->where('payments.date >', $date)->where('payments.paid_by', 'ppp');
        $this->db->where('payments.created_by', $user_id);

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getRegisterStripeSales($date, $user_id = NULL)
    {
        if (!$date) {
            $date = $this->session->userdata('register_open_time');
        }
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        $this->db->select('COUNT(' . $this->db->dbprefix('payments') . '.id) as total_cheques, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid', FALSE)
            ->join('bils', 'bils.id=payments.bill_id', 'left')
            ->where('type', 'received')->where('payments.date >', $date)->where('payments.paid_by', 'stripe');
        $this->db->where('payments.created_by', $user_id);

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getRegisterAuthorizeSales($date, $user_id = NULL)
    {
        if (!$date) {
            $date = $this->session->userdata('register_open_time');
        }
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        $this->db->select('COUNT(' . $this->db->dbprefix('payments') . '.id) as total_cheques, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid', FALSE)
            ->join('bils', 'bils.id=payments.bill_id', 'left')
            ->where('type', 'received')->where('payments.date >', $date)->where('payments.paid_by', 'authorize');
        $this->db->where('payments.created_by', $user_id);

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function suspendSale($data = array(), $items = array(), $did = NULL)
    {
        $sData = array(
            'count' => $data['total_items'],
            'biller_id' => $data['biller_id'],
            'customer_id' => $data['customer_id'],
            'warehouse_id' => $data['warehouse_id'],
            'customer' => $data['customer'],
            'date' => $data['date'],
            'suspend_note' => $data['suspend_note'],
            'total' => $data['grand_total'],
            'order_tax_id' => $data['order_tax_id'],
            'order_discount_id' => $data['order_discount_id'],
            'created_by' => $this->session->userdata('user_id')
        );

        if ($did) {

            if ($this->db->update('suspended_bills', $sData, array('id' => $did)) && $this->db->delete('suspended_items', array('suspend_id' => $did))) {
                $addOn = array('suspend_id' => $did);
                end($addOn);
                foreach ($items as &$var) {
                    $var = array_merge($addOn, $var);
                }
                if ($this->db->insert_batch('suspended_items', $items)) {
                    return TRUE;
                }
            }

        } else {

            if ($this->db->insert('suspended_bills', $sData)) {
                $suspend_id = $this->db->insert_id();
                $addOn = array('suspend_id' => $suspend_id);
                end($addOn);
                foreach ($items as &$var) {
                    $var = array_merge($addOn, $var);
                }
                if ($this->db->insert_batch('suspended_items', $items)) {
                    return TRUE;
                }
            }

        }
        return FALSE;
    }

    public function deleteBill($id)
    {

        if ($this->db->delete('suspended_items', array('suspend_id' => $id)) && $this->db->delete('suspended_bills', array('id' => $id))) {
            return true;
        }

        return FALSE;
    }

    public function getInvoicePayments($bill_id)
    {
        $q = $this->db->get_where("payments", array('bill_id' => $bill_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }

        return FALSE;
    }
	public function getInvoicePayments_archival($bill_id){
        $q = $this->db->get_where("payments_archival", array('bill_id' => $bill_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    function getInvoiceRoughTenderPayments($bill_id){
	$q = $this->db->get_where("rough_tender_payments", array('bill_id' => $bill_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }

        return FALSE;
    }
    function stripe($amount = 0, $card_info = array(), $desc = '')
    {
        $this->load->admin_model('stripe_payments');
        //$card_info = array( "number" => "4242424242424242", "exp_month" => 1, "exp_year" => 2016, "cvc" => "314" );
        //$amount = $amount ? $amount*100 : 3000;
        unset($card_info['type']);
        $amount = $amount * 100;
        if ($amount && !empty($card_info)) {
            $token_info = $this->stripe_payments->create_card_token($card_info);
            if (!isset($token_info['error'])) {
                $token = $token_info->id;
                $data = $this->stripe_payments->insert($token, $desc, $amount, $this->default_currency->code);
                if (!isset($data['error'])) {
                    $result = array('transaction_id' => $data->id,
                        'created_at' => date($this->dateFormats['php_ldate'], $data->created),
                        'amount' => ($data->amount / 100),
                        'currency' => strtoupper($data->currency)
                    );
                    return $result;
                } else {
                    return $data;
                }
            } else {
                return $token_info;
            }
        }
        return false;
    }

    function paypal($amount = NULL, $card_info = array(), $desc = '')
    {
        $this->load->admin_model('paypal_payments');
        //$card_info = array( "number" => "5522340006063638", "exp_month" => 2, "exp_year" => 2016, "cvc" => "456", 'type' => 'MasterCard' );
        //$amount = $amount ? $amount : 30.00;
        if ($amount && !empty($card_info)) {
            $data = $this->paypal_payments->Do_direct_payment($amount, $this->default_currency->code, $card_info, $desc);
            if (!isset($data['error'])) {
                $result = array('transaction_id' => $data['TRANSACTIONID'],
                    'created_at' => date($this->dateFormats['php_ldate'], strtotime($data['TIMESTAMP'])),
                    'amount' => $data['AMT'],
                    'currency' => strtoupper($data['CURRENCYCODE'])
                );
                return $result;
            } else {
                return $data;
            }
        }
        return false;
    }

    public function authorize($authorize_data)
    {
        $this->load->library('authorize_net');
        // $authorize_data = array( 'x_card_num' => '4111111111111111', 'x_exp_date' => '12/20', 'x_card_code' => '123', 'x_amount' => '25', 'x_invoice_num' => '15454', 'x_description' => 'References');
        $this->authorize_net->setData($authorize_data);

        if( $this->authorize_net->authorizeAndCapture() ) {
            $result = array(
                'transaction_id' => $this->authorize_net->getTransactionId(),
                'approval_code' => $this->authorize_net->getApprovalCode(),
                'created_at' => date($this->dateFormats['php_ldate']),
            );
            return $result;
        } else {
            return array('error' => 1, 'msg' => $this->authorize_net->getError());
        }
    }

    public function addPayment($payment = array(), $customer_id = null)
    {
        if (isset($payment['sale_id']) && isset($payment['paid_by']) && isset($payment['amount'])) {
            $payment['pos_paid'] = $payment['amount'];
            $inv = $this->getInvoiceByID($payment['sale_id']);
            $paid = $inv->paid + $payment['amount'];
            if ($payment['paid_by'] == 'ppp') {
                $card_info = array("number" => $payment['cc_no'], "exp_month" => $payment['cc_month'], "exp_year" => $payment['cc_year'], "cvc" => $payment['cc_cvv2'], 'type' => $payment['cc_type']);
                $result = $this->paypal($payment['amount'], $card_info);
                if (!isset($result['error'])) {
                    $payment['transaction_id'] = $result['transaction_id'];
                    $payment['date'] = $this->site->getTransactionDate();
		    $payment['paid_on'] = date('Y-m-d H:i:s');//$this->sma->fld($result['created_at']);
                    $payment['amount'] = $result['amount'];
                    $payment['currency'] = $result['currency'];
                    unset($payment['cc_cvv2']);
                    $this->db->insert('payments', $payment);
                    $paid += $payment['amount'];
                } else {
                    $msg[] = lang('payment_failed');
                    if (!empty($result['message'])) {
                        foreach ($result['message'] as $m) {
                            $msg[] = '<p class="text-danger">' . $m['L_ERRORCODE'] . ': ' . $m['L_LONGMESSAGE'] . '</p>';
                        }
                    } else {
                        $msg[] = lang('paypal_empty_error');
                    }
                }
            } elseif ($payment['paid_by'] == 'stripe') {
                $card_info = array("number" => $payment['cc_no'], "exp_month" => $payment['cc_month'], "exp_year" => $payment['cc_year'], "cvc" => $payment['cc_cvv2'], 'type' => $payment['cc_type']);
                $result = $this->stripe($payment['amount'], $card_info);
                if (!isset($result['error'])) {
                    $payment['transaction_id'] = $result['transaction_id'];
                    $payment['date'] = $this->site->getTransactionDate();
		    $payment['paid_on'] = date('Y-m-d H:i:s');//$this->sma->fld($result['created_at']);
                    $payment['amount'] = $result['amount'];
                    $payment['currency'] = $result['currency'];
                    unset($payment['cc_cvv2']);
                    $this->db->insert('payments', $payment);
                    $paid += $payment['amount'];
                } else {
                    $msg[] = lang('payment_failed');
                    $msg[] = '<p class="text-danger">' . $result['code'] . ': ' . $result['message'] . '</p>';
                }

            } elseif ($payment['paid_by'] == 'authorize') {
                $authorize_arr = array("x_card_num" => $payment['cc_no'], "x_exp_date" => ($payment['cc_month'].'/'.$payment['cc_year']), "x_card_code" => $payment['cc_cvv2'], 'x_amount' => $payment['amount'], 'x_invoice_num' => $inv->id, 'x_description' => 'Sale Ref '.$inv->reference_no.' and Payment Ref '.$payment['reference_no']);
                list($first_name, $last_name) = explode(' ', $payment['cc_holder'], 2);
                $authorize_arr['x_first_name'] = $first_name;
                $authorize_arr['x_last_name'] = $last_name;
                $result = $this->authorize($authorize_arr);
                if (!isset($result['error'])) {
                    $payment['transaction_id'] = $result['transaction_id'];
                    $payment['approval_code'] = $result['approval_code'];
                    $payment['date'] = $this->site->getTransactionDate();
		    $payment['paid_on'] = date('Y-m-d H:i:s');//$this->sma->fld($result['created_at']);
                    unset($payment['cc_cvv2']);
                    $this->db->insert('payments', $payment);
                    $paid += $payment['amount'];
                } else {
                    $msg[] = lang('payment_failed');
                    $msg[] = '<p class="text-danger">' . $result['msg'] . '</p>';
                }

            } else {
                if ($payment['paid_by'] == 'gift_card') {
                    $gc = $this->site->getGiftCardByNO($payment['cc_no']);
                    $this->db->update('gift_cards', array('balance' => ($gc->balance - $payment['amount'])), array('card_no' => $payment['cc_no']));
                } elseif ($customer_id && $payment['paid_by'] == 'deposit') {
                    $customer = $this->site->getCompanyByID($customer_id);
                    $this->db->update('companies', array('deposit_amount' => ($customer->deposit_amount-$payment['amount'])), array('id' => $customer_id));
                }
                unset($payment['cc_cvv2']);
		$payment['date'] =$this->site->getTransactionDate();
		$payment['paid_on'] = date('Y-m-d H:i:s');
                $this->db->insert('payments', $payment);
                $paid += $payment['amount'];
            }
            if (!isset($msg)) {
                if ($this->site->getReference('pay') == $data['reference_no']) {
                    $this->site->updateReference('pay');
                }
                $this->site->syncSalePayments($payment['sale_id']);
                return array('status' => 1, 'msg' => '');
            }
            return array('status' => 0, 'msg' => $msg);

        }
        return false;
    }

    public function addPrinter($data = array()) {
        if($this->db->insert('printers', $data)) {
            return $this->db->insert_id();
        }
        return false;
    }

    public function updatePrinter($id, $data = array()) {
        if($this->db->update('printers', $data, array('id' => $id))) {
            return true;
        }
        return false;
    }

    public function deletePrinter($id) {
        if($this->db->delete('printers', array('id' => $id))) {
            return true;
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
    
    public function getAllPrinters() {
        $q = $this->db->get('printers');
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    
    public function getAllTablesWithKitchen($kitchen_type){
		
		$current_date = date('Y-m-d');
		$current_date = $this->site->getTransactionDate();
        $this->db->select("orders.id, orders.biller_id, restaurant_tables.name as tablename, restaurant_tables.name table_name,orders.order_type, orders.order_status,orders.reference_no,users.username,orders.split_id,'order_items' ")
        ->join('kitchen_orders', 'orders.id = kitchen_orders.sale_id AND (kitchen_orders.chef_id = "'.$this->session->userdata('user_id').'" OR kitchen_orders.chef_id = 0) ')
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
		->where('orders.warehouse_id', $this->session->userdata('warehouse_id'))
		->group_by('orders.id');
		$t = $this->db->get('orders');

        if ($t->num_rows() > 0) {

           foreach ($t->result() as $row) {

           $this->db->select("order_items.sale_id,order_items.id, order_items.recipe_id, order_items.recipe_name,order_items.item_status as status,order_items.quantity, order_items.addon_id, order_items.buy_id, order_items.buy_quantity, order_items.get_item, order_items.get_quantity, order_items.total_get_quantity, recipe.image, recipe.khmer_name, order_items.time_started,recipe.preparation_time")
                ->join('orders', 'order_items.sale_id = orders.id')
				->join('recipe', 'recipe.id = order_items.recipe_id', 'left')
                ->join('kitchen_orders', 'kitchen_orders.sale_id = orders.id')
				->where('order_items.sale_id', $row->id)
				//->where('order_items.order_item_cancel_status', 0)
				->where('order_items.kitchen_type_id', $kitchen_type)
				->where('DATE(date)', $current_date)
				->where('orders.warehouse_id', $this->session->userdata('warehouse_id'));
				//->where_in('order_items.item_status', array('Inprocess','Preparing'))
				//->or_where('order_items.item_status', 'Preparing');
				
				//->group_by('order_items.sale_id');
				
                $s = $this->db->get('order_items');
              
                if ($s->num_rows() > 0) {

                        foreach ($s->result() as $sow) {

                             $split[$row->id][] = $sow;                                             
                        }  

                    $row->order_items = $split[$row->id];
                }else{
                    $row->order_items = array();
                }
                
                $data[] = $row;
            }

			
            return $data;
        }
        
        return FALSE;
    }
	
	 public function getorderKitchenprint($kitchen_type, $order_id = NULL){
		
		$current_date = date('Y-m-d');
		
        $this->db->select("orders.id, orders.biller_id, restaurant_tables.name as tablename, restaurant_tables.name table_name,orders.order_type, orders.order_status,orders.reference_no,users.username,orders.split_id,'order_items' ")
        ->join('kitchen_orders', 'orders.id = kitchen_orders.sale_id AND (kitchen_orders.chef_id = "'.$this->session->userdata('user_id').'" OR kitchen_orders.chef_id = 0) ')
		->join('order_items', 'order_items.sale_id = orders.id', 'inner')
        ->join('users', 'users.id = kitchen_orders.waiter_id')
        ->join('restaurant_tables', 'restaurant_tables.id = orders.table_id','left')
		->where('order_items.kitchen_type_id', $kitchen_type)
		//->join('sales', 'sales.sales_split_id = orders.split_id', 'inner')
		->where('orders.order_status', 'Open')
		->where('order_items.order_item_cancel_status', 0)
		->where('orders.order_cancel_status', 0)
		
		->where('DATE(date)', $current_date)
		->where('orders.id', $order_id)
		->where_in('order_items.item_status', array('Inprocess','Preparing'))
		->where('orders.warehouse_id', $this->session->userdata('warehouse_id'))
		->group_by('orders.id');
		$t = $this->db->get('orders');

        if ($t->num_rows() > 0) {

           foreach ($t->result() as $row) {

           $this->db->select("order_items.sale_id,order_items.id, order_items.recipe_id, order_items.recipe_name,order_items.item_status as status,order_items.quantity, order_items.addon_id, order_items.buy_id, order_items.buy_quantity, order_items.get_item, order_items.get_quantity, order_items.total_get_quantity, recipe.image, recipe.khmer_name, order_items.time_started,recipe.preparation_time")
                ->join('orders', 'order_items.sale_id = orders.id')
				->join('recipe', 'recipe.id = order_items.recipe_id', 'left')
                ->join('kitchen_orders', 'kitchen_orders.sale_id = orders.id')
				->where('order_items.sale_id', $row->id)
				//->where('order_items.order_item_cancel_status', 0)
				->where('order_items.kitchen_type_id', $kitchen_type)
				->where('DATE(date)', $current_date)
				->where('orders.warehouse_id', $this->session->userdata('warehouse_id'));
				//->where_in('order_items.item_status', array('Inprocess','Preparing'))
				//->or_where('order_items.item_status', 'Preparing');
				
				//->group_by('order_items.sale_id');
				
                $s = $this->db->get('order_items');
              
                if ($s->num_rows() > 0) {

                        foreach ($s->result() as $sow) {

                             $split[$row->id][] = $sow;                                             
                        }  

                    $row->order_items = $split[$row->id];
                }else{
                    $row->order_items = array();
                }
                
                $data[] = $row;
            }

			
            return $data;
        }
        
        return FALSE;
    }
	
    public function updateKitchenstatus($notification_array, $status, $order_id, $order_item_id, $current_status, $user_id,$timelog_array){

		$q = $this->db->select('reference_no, table_id, customer_id')->where('id', $order_id)->get('orders');
		
		if ($q->num_rows() > 0) {
            $order_number =  $q->row('reference_no');
			$table_id =  $q->row('table_id');
			$customer_id =  $q->row('customer_id');
        }
		
		$k = $this->db->select('waiter_id')->where('sale_id', $order_id)->get('kitchen_orders');
		if ($k->num_rows() > 0) {
            $waiter_id =  $k->row('waiter_id');
        }
		
		$notification_array['insert_array']['msg'] = 'The order ['.$order_number.'] item has been '.$current_status.'.';
		$notification_array['insert_array']['to_user_id'] = $waiter_id;
		$notification_array['insert_array']['table_id'] = $table_id;
		
		$notification_array['customer_msg'] = 'The order ['.$order_number.'] item has been '.$current_status.'.';
		$notification_array['customer_id'] = $customer_id;
		
		$this->site->create_notification($notification_array);

		$order_item_id = explode(',', $order_item_id);

		$q = $this->db->where('kitchen_orders.sale_id', $order_id);
		$q = $this->db->update('kitchen_orders', array('chef_id' => $user_id, 'status' => 'Booked'));
		
        $kitchen_array = array(
            'item_status' => $current_status	    
        );
	if($current_status=="Ready"):
	    $kitchen_array['time_end'] = date('Y-m-d H:i:s');	
	endif;
		foreach($order_item_id as $item_id){
			
				$timelog_array['item_id'] = $item_id;
				$this->db->insert('time_log',  $timelog_array);
			}


		if(!empty($order_item_id)){
			foreach($order_item_id as $item_id){
				$this->db->where('order_item_cancel_status', 0);
				$this->db->where('id', $item_id);
				$this->db->update('order_items',  $kitchen_array);
			}
			return true;
		}
        return false;
    }


   /* public function getTodayCCSales()
    {
        $date = date('Y-m-d 00:00:00');
        $this->db->select('COUNT(' . $this->db->dbprefix('payments') . '.id) as total_cc_slips, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid', FALSE)
            ->join('sales', 'sales.id=payments.sale_id', 'left')
            ->where('type', 'received')->where('payments.date >', $date)->where('paid_by', 'CC');

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }*/


   /*public function updateKitchenstatus($status, $order_item_id, $current_status, $user_id){
        $kitchen_array = array(
            'item_status' => $current_status,
        );        
       $this->db->where('id', $order_item_id);
        if ($this->db->update('order_items',  $kitchen_array)) {
            return true;
        }
        return false;
    }*/

    public function getTableSplitCount($split_id)
    {
          
        $this->db->select('orders.id')
         ->join('order_items', 'orders.id=order_items.sale_id');
        $split_count = $this->db->get_where('orders', array('orders.split_id' => $split_id,'order_items.order_item_cancel_status' => 0));

	
      $this->db->select('orders.id')
         ->join('order_items', 'orders.id=order_items.sale_id');
        $split_closed_count = $this->db->get_where('orders', array('orders.split_id' => $split_id,'order_items.item_status' => 'Closed','order_items.order_item_cancel_status' => 0));
		
		  
		$order_array = array(
            'order_status' => "Closed",
        );       
        if ($split_count->num_rows()  == $split_closed_count->num_rows()) {
			
			$this->db->where('split_id', $split_id);
			$o = $this->db->update('orders', $order_array);
					
            return TRUE;
			
        }
        else{
            return FALSE;
        }
        
		
        return FALSE; 
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

    public function updateOrderstatus($status, $order_item_id, $current_status, $user_id, $notification_array,$timelog_array){
		
	
		$order_item_id = explode(',', $order_item_id);
		
	
		$this->db->select('orders.reference_no, orders.customer_id, orders.table_id, order_items.sale_id')
         ->join('orders', 'orders.id = order_items.sale_id', 'left');
        $q = $this->db->get_where('order_items', array('order_items.id' => $order_item_id[0]));
		
		
		
		if ($q->num_rows() > 0) {
            $order_number =  $q->row('reference_no');
			$table_id =  $q->row('table_id');
			$sale_id =  $q->row('sale_id');
			$customer_id = $q->row('customer_id');
        }
		
		$k = $this->db->select('chef_id')->where('sale_id', $sale_id)->get('kitchen_orders');
		if ($k->num_rows() > 0) {
            $chef_id =  $k->row('chef_id');
        }
		
		$notification_array['customer_msg'] = 'The order ['.$order_number.'] item has been '.$current_status.'.';
		$notification_array['customer_id'] = $customer_id;
		
		$notification_array['insert_array']['msg'] = 'The order ['.$order_number.'] item has been '.$current_status.'.';
		$notification_array['insert_array']['to_user_id'] = $chef_id;
		$notification_array['insert_array']['table_id'] = $table_id;
		
		$this->site->create_notification($notification_array);
		
        $order_item_array = array(
            'item_status' => $current_status,
        );

        $order_itemendtime = array(
        	//'time_end' => date('Y-m-d H:m:s'),
        );
		
		if($current_status = 'Served')
		{
		//foreach($order_item_id as $item_id){
		//		$this->db->where('id', $item_id);
		//		$this->db->update('order_items',  $order_itemendtime);
		//	}
		}	
		foreach($order_item_id as $item_id){
				$timelog_array['item_id'] = $item_id;
				$this->db->insert('time_log',  $timelog_array);
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

public function ALLCancelOrdersItem($cancel_remarks, $split_table_id, $user_id, $notification_array){
		$k = $this->db->select('id,GROUP_CONCAT(id SEPARATOR ",") as ordersid ')->where('split_id', $split_table_id)->get('orders');
		if ($k->num_rows() > 0) {
            $id =  $k->row('ordersid');
        }

        $bb = $this->db->select('split_id')->where('reference_no', $split_table_id)->get('bbq');
		if ($k->num_rows() > 0) {
		        $bbqupdate = array(
					'cancel_status' => 1,
					'cancel_msg' => 'BBQ Covers Cancel',
					'status' => 'Cancelled',
					'payment_status' => 'Cancelled',
					'cancel_by' => $this->session->userdata('user_id')
				);
				$this->db->where('reference_no', $split_table_id);
				$this->db->update('bbq', $bbqupdate);
		}
		
		$k = $this->db->select('waiter_id, chef_id')->where('sale_id', $id)->get('kitchen_orders');
		if ($k->num_rows() > 0) {
            $waiter_id =  $k->row('waiter_id');
			$chef_id =  $k->row('chef_id');
        }
		
		/*$notification_array['insert_array']['msg'] = 'Cashier has benn cancel this bil ('.$split_id.')';
		$notification_array['insert_array']['table_id'] = $table_id;
		$notification_array['insert_array']['type'] = 'Cashier cancel bils';
		$notification_array['insert_array']['role_id'] = WAITER;
		$notification_array['insert_array']['to_user_id'] = $waiter_id;*/
		
		
		
		//$this->site->create_notification($notification_array);
        /*echo $id;die;*/
       
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
 	
         

		//$this->db->update('bils', $bill_array, array('sales_id' => $sale_id));
		/*$this->db->where_in('split_id', $split_id);
		$this->db->update('orders',  $order_array);*/
	    
		
		$id2 =   explode(',',$id);
		$this->db->where_in('sale_id', $id2);
		$this->db->update('order_items',  $order_item_array);

		/*table status changed if only one orderd placed*/
	      /*   $this->db->select("table_id", FALSE);		
			$this->db->where('split_id', $split_table_id);						
			$table = $this->db->get('orders'); 	
		        if ($table->num_rows() > 0) {
		            foreach (($table->result()) as $row) {
		            	$this->db->update('restaurant_tables', array('current_order_status' => 0,'current_order_user' => 0), array('id' => $row->table_id));
		            }
		        }	 */
				$this->site->update_TableStatus_after_cancel_split($split_table_id,0);
	   /*table status changed if only one orderd placed*/
	   
			
		$this->db->where('split_id', $split_table_id);
		if ($this->db->update('orders',  $order_array)) {
			return true;
		}
		
		return false;

    }    
    public function CancelOrdersItem($notification_array, $cancel_remarks, $order_item_id, $user_id, $split_id,$timelog_array,$cancelQty,$cancel_type){
    	
		$q = $this->db->select('*')->where('id', $order_item_id)->get('order_items');
		if ($q->num_rows() > 0) {
            $sale_id =  $q->row('sale_id');
	        $recipe_id = $q->row('recipe_id');
	        $recipe_type = $q->row('recipe_type');
        }

        $addonTot=0;
		$this->db->select('SUM(DISTINCT subtotal) as subtotal');
		$this->db->where('order_item_id',$order_item_id);
		$addon = $this->db->get('addon_sale_items');			
		if ($addon->num_rows() > 0) {
			$addonTot = $addon->row('subtotal');
		}
		$k = $this->db->select('waiter_id, chef_id')->where('sale_id', $sale_id)->get('kitchen_orders');
		if ($k->num_rows() > 0) {
            $waiter_id =  $k->row('waiter_id');
			$chef_id =  $k->row('chef_id');
        }
		if(!empty($split_id)){
			 $notification_array['insert_array']['role_id'] = KITCHEN;
			 $notification_array['insert_array']['to_user_id'] = $waiter_id;
		 }else{
			 $notification_array['insert_array']['role_id'] = WAITER;
			 $notification_array['insert_array']['to_user_id'] = $chef_id;
		 }			
        $order_item_array = array(
            'order_item_cancel_id' => $user_id,
            'order_item_cancel_note' => $cancel_remarks,
            'order_item_cancel_status' => 1,
        );
        $unit_qty =$q->row('unit_price')*($q->row('quantity')-$cancelQty);

	if($cancelQty!='all' && $cancelQty!=NULL){ //cancel item
	    $insertNew = $q->row();
	    $insertNew->quantity = $cancelQty;
	    $insertNew->item_status = 'Cancel';
		$insertNew->order_item_cancel_status = 1;
	    unset($insertNew->id);
	    $this->db->insert('order_items',  $insertNew);
	    $order_item_array = array(
		'quantity' => $q->row('quantity')-$cancelQty,
		'subtotal' => ($unit_qty+$addonTot),
		// 'subtotal' => $q->row('unit_price')*($q->row('quantity')-$cancelQty),
		);
	}else{
	    $cancelQty = $q->row('quantity');
	    $order_item_array = array(
		'item_status' => 'Cancel',
		'order_item_cancel_status' => 1
		);
	}
        $order_item_array['item_cancel_type']=$cancel_type;
        $this->db->insert('time_log',  $timelog_array);
		$this->db->where('id', $order_item_id);
		if ($this->db->update('order_items',  $order_item_array)) {
			$order = $this->db->select('order_items.order_item_cancel_status')
			->join('order_items', 'order_items.sale_id = orders.id')
			->where('orders.split_id', $split_id)
			->where('order_items.order_item_cancel_status', 0)
			->get('orders');
			
			
			
				if($cancel_type     =="out_of_stock" || $cancel_type  == "kitchen_cancel" ){
					if($recipe_type =='standard' || $recipe_type =='production' ){ 
					    $this->site->saleStockIn_new($recipe_id,$cancelQty,$order_item_id);
					}else if($recipe_type =='standard' || $recipe_type =='production' ){ 
						$this->site->saleStockIn_new($recipe_id,$cancelQty,$order_item_id);
					}
				}elseif($cancel_type=="reusable" && $recipe_type=="quick_service"){
	                 $this->siteprocurment->production_salestock_in($recipe_id,$cancelQty,$q->recipe_variant_id);
				}elseif($cancel_type=="spoiled"){
					$this->wastageItem($q->recipe_id,$q->recipe_variant_id,$cancelQty,$order_item_id);
				}
				
				
				
				
				
				
			if($order->num_rows() == 0){
				$this->db->where('orders.split_id', $split_id);
				$orderupdate = $this->db->update('orders', array('order_cancel_id' => $user_id, 'order_cancel_note' => 'All item order cancel', 'order_cancel_status' => 1,'payment_status'=>'Cancelled'));
				/*table status changed if only one orderd placed*/
			        $this->db->select("table_id", FALSE);		
					$this->db->where('split_id', $split_id);						
					$table = $this->db->get('orders'); 	
				        if ($table->num_rows() > 0) {
				            foreach (($table->result()) as $row) {
				            	$this->db->update('restaurant_tables', array('current_order_status' => 0,'current_order_user' => 0), array('id' => $row->table_id));
				            }
				        }	
			   /*table status changed if only one orderd placed*/
			}

			return $this->cancel_kot($split_id,$order_item_id,$cancelQty);
		}
		return false;
    }


    public function CancelSale($cancel_remarks, $sale_id, $user_id, $notification_array){
    	$q = $this->db->select('sales_split_id, sales_table_id')->where('id', $sale_id)->get('sales');
		if ($q->num_rows() > 0) {
            $split_id =  $q->row('sales_split_id');
			$table_id =  $q->row('sales_table_id');
        }
        /*echo $split_id;*/
		
		
		$k = $this->db->select('id,GROUP_CONCAT(id SEPARATOR ",") as ordersid ')->where('split_id', $split_id)->get('orders');
		if ($k->num_rows() > 0) {
            $id =  $k->row('ordersid');
        }

        $bb = $this->db->select('split_id')->where('reference_no', $split_id)->get('bbq');
		if ($k->num_rows() > 0) {
		        $bbqupdate = array(
					'cancel_status' => 1,
					'cancel_msg' => 'BBQ Covers Cancel',
					'status' => 'Cancelled',
					'payment_status' => 'Cancelled',
					'cancel_by' => $this->session->userdata('user_id')
				);
				$this->db->where('reference_no', $split_id);
				$this->db->update('bbq', $bbqupdate);
		}
		// print_r($this->db->error());die;
		$k = $this->db->select('waiter_id, chef_id')->where('sale_id', $id)->get('kitchen_orders');
		if ($k->num_rows() > 0) {
            $waiter_id =  $k->row('waiter_id');
			$chef_id =  $k->row('chef_id');
        }
		
		$notification_array['insert_array']['msg'] = 'Cashier has benn cancel this bil ('.$split_id.')';
		$notification_array['insert_array']['table_id'] = $table_id;
		$notification_array['insert_array']['type'] = 'Cashier cancel bils';
		$notification_array['insert_array']['role_id'] = WAITER;
		$notification_array['insert_array']['to_user_id'] = $waiter_id;
		
		
		
		$this->site->create_notification($notification_array);
        /*echo $id;die;*/
        $sale_aray = array(
            'canceled_user_id' => $user_id,
            'cancel_remarks' => $cancel_remarks,
            'cancel_status' => 1,
            'payment_status' => 'Cancelled',
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

		$this->db->update('bils', $bill_array, array('sales_id' => $sale_id));
		$this->db->where_in('split_id', $split_id);
		$this->db->update('orders',  $order_array);
	    /*$this->db->update('orders', $order_array, array('id' => $id));*/

		// $this->db->update('order_items', $order_item_array, array('sale_id' => $id));
		
		$id2 =   explode(',',$id);
		$this->db->where_in('sale_id', $id2);
		$this->db->update('order_items',  $order_item_array);

			
		$this->db->where('id', $sale_id);
		if ($this->db->update('sales',  $sale_aray)) {
			// print_r($this->db->last_query());die;
			/*if(!empty($id)){

			}*/
			
			if($table_id != 0){
				$this->site->updatepayment_TableStatus($table_id,0);
			}
				
			return true;
		}
		return false;

    }

    public function BBQCancelSale($cancel_remarks, $split_id, $user_id, $notification_array){
    	$q = $this->db->select('sales_split_id, sales_table_id,id')->where('sales_split_id', $split_id)->get('sales');
		if ($q->num_rows() > 0) {
            $split_id =  $q->row('sales_split_id');
			$table_id =  $q->row('sales_table_id');
			$sale_id =  $q->row('id');
        }
        /*echo $split_id;*/
		
		// $k = $this->db->select('id,split_id')->where('split_id', $split_id)->get('orders');
		$k = $this->db->select('id,GROUP_CONCAT(id SEPARATOR ",") as ordersid ')->where('split_id', $split_id)->get('orders');
		if ($k->num_rows() > 0) {
            // $id =  $k->row('id');
            $id =  $k->row('ordersid');
        }

        $bb = $this->db->select('split_id')->where('reference_no', $split_id)->get('bbq');
		if ($k->num_rows() > 0) {
		        $bbqupdate = array(
					'cancel_status' => 1,
					'cancel_msg' => 'BBQ Covers Cancel',
					'status' => 'Cancelled',
					'payment_status' => 'Cancelled',
					'cancel_by' => $this->session->userdata('user_id')
				);
				$this->db->where('reference_no', $split_id);
				$this->db->update('bbq', $bbqupdate);
		}
		// print_r($this->db->error());die;
		// $k = $this->db->select('waiter_id, chef_id')->where('sale_id', $id)->get('kitchen_orders');
		$k = $this->db->select('waiter_id, chef_id')->where_in('sale_id', $id)->get('kitchen_orders');
		if ($k->num_rows() > 0) {
            $waiter_id =  $k->row('waiter_id');
			$chef_id =  $k->row('chef_id');
        }
		
		$notification_array['insert_array']['msg'] = 'Cashier has benn cancel this bil ('.$split_id.')';
		$notification_array['insert_array']['table_id'] = $table_id;
		$notification_array['insert_array']['type'] = 'Cashier cancel bils';
		$notification_array['insert_array']['role_id'] = WAITER;
		$notification_array['insert_array']['to_user_id'] = $waiter_id;
		
		$this->site->create_notification($notification_array);
        /*echo $id;die;*/
        $sale_aray = array(
            'canceled_user_id' => $user_id,
            'cancel_remarks' => $cancel_remarks,
            'cancel_status' => 1,
            'payment_status' => 'Cancelled',
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

		$this->db->update('bils', $bill_array, array('sales_id' => $sale_id));
		$this->db->where_in('split_id', $split_id);
		$this->db->update('orders',  $order_array);
		$id2 =   explode(',',$id);
		$this->db->where_in('sale_id', $id2);
		$this->db->update('order_items',  $order_item_array);

		$this->db->where('sales_split_id', $split_id);
		if ($this->db->update('sales',  $sale_aray)) {	
		// print_r($this->db->error())		;die;
			return true;
		}
		// print_r($this->db->error())		;die;
		return false;


    }    

	 public function CancelWaiterOrders($order_id, $user_id){
        $order_array = array(
            'order_cancel_id' => $user_id,
            'order_cancel_note' => 'Waiter cancel',
            'order_cancel_status' => 1,
        );
        
		$this->db->where('id', $order_id);
		if ($this->db->update('orders',  $order_array)) {
			return true;
		}
		return false;

    }
	
    public function Saleitemqtyadjustment($order_item_id, $user_id, $split_id){
		$q = $this->db->select('*')->where('id', $order_item_id)->get('order_items');
		if ($q->num_rows() > 0) {
            $sale_id =  $q->row('sale_id');
	        $recipe_id = $q->row('recipe_id');
        }
        $addon_qty='';
        $addonTot=0;
			$this->db->select('id,subtotal,qty,price');
			$this->db->where('order_item_id',$order_item_id);
			$addon = $this->db->get('addon_sale_items');			
			if ($addon->num_rows() > 0) {
				foreach($addon->result() as $addons_items){
					$subtotal= ($addons_items->price*($addons_items->qty-1));
					$query = "update ".$this->db->dbprefix('addon_sale_items')." set qty = qty-1,subtotal = ".$subtotal." where id=".$addons_items->id." ";
				    $this->db->query($query);
					$addonTot +=$subtotal;
					$addon_qty .= !empty($addon_qty)?','.($addons_items->qty+1):$addons_items->qty+1;
				}
				
			}

		$k = $this->db->select('waiter_id, chef_id')->where('sale_id', $sale_id)->get('kitchen_orders');
		if ($k->num_rows() > 0) {
            $waiter_id =  $k->row('waiter_id');
			$chef_id =  $k->row('chef_id');
        }
		
	if($q->row('quantity') > 1){ //cancel item
	    $insertNew = $q->row();
		$ritem=$q->row();
	    $insertNew->quantity = 1;
	    $insertNew->item_status = 'Cancel';
		$insertNew->order_item_cancel_status = 1;
	    unset($insertNew->id);
	    $this->db->insert('order_items',  $insertNew);

	    $unit_qty =$q->row('unit_price')*($q->row('quantity')-1);

	    $order_item_array = array(
		'quantity' => $q->row('quantity')-1,
		'subtotal' => ($unit_qty+$addonTot),
		'addon_qty'=>$addon_qty
		// 'subtotal' => $q->row('unit_price')*($q->row('quantity')-1),
		);
		if($this->Settings->procurment == 1){
			if($ritem->recipe_type=='standard' || $ritem->recipe_type =='production'){
			$this->site->updateStockMaster_new($ritem->recipe_id,$ritem->recipe_variant_id,$ritem->quantity+1,$cate,$ritem->id);
			}else if($ritem->recipe_type =="quick_service"){
			$this->siteprocurment->production_salestock_in($ritem->recipe_id,$ritem->quantity-1,$ritem->recipe_variant_id);
			}	
		}
	}else{
	    $cancelQty = $q->row('quantity');
		$ritem=$q->row();
	    $order_item_array = array(
		'item_status' => 'Cancel',
		'order_item_cancel_status' => 1
		);
		if($this->Settings->procurment == 1){
			if($ritem->recipe_type=='standard' || $ritem->recipe_type =='production'){
			$this->site->updateStockMaster_new($ritem->recipe_id,$ritem->recipe_variant_id,$ritem->quantity+1,$cate,$ritem->id);
			}else if($ritem->recipe_type =="quick_service"){
			$this->siteprocurment->production_salestock_in($ritem->recipe_id,$ritem->quantity-1,$ritem->recipe_variant_id);
			}	
		}
	}

		$this->db->where('id', $order_item_id);
		if ($this->db->update('order_items',  $order_item_array)) {
			$order = $this->db->select('order_items.order_item_cancel_status')
			->join('order_items', 'order_items.sale_id = orders.id')
			->where('orders.split_id', $split_id)
			->where('order_items.order_item_cancel_status', 0)
			->get('orders');
			/*if($cancel_type=="out_of_stock" || $cancel_type=="kitchen_cancel"){
			    $this->site->saleStockIn($recipe_id,$cancelQty,$order_item_id);
			}*/
			if($order->num_rows() == 0){
				$this->db->where('orders.split_id', $split_id);
				$orderupdate = $this->db->update('orders', array( 'order_cancel_id' => $user_id, 'order_cancel_note' => 'All item order cancel', 'order_cancel_status' => 1));
			}	
			return true;
		}
		return false;
    }

    public function SaleItemQtyIncrease($order_item_id, $user_id, $split_id){
		    $q = $this->db->select('*')->where('id', $order_item_id)->get('order_items');
            $addonTot=0;
			$addon_qty='';
			$this->db->select('id,subtotal,qty,price');
			$this->db->where('order_item_id',$order_item_id);
			$addon = $this->db->get('addon_sale_items');			
			if ($addon->num_rows() > 0) {
				foreach($addon->result() as $addons_items){
					$subtotal= ($addons_items->price*($addons_items->qty+1));
					$query = "update ".$this->db->dbprefix('addon_sale_items')." set qty = qty+1,subtotal = ".$subtotal." where id=".$addons_items->id." ";
				    $this->db->query($query);
					$addonTot +=$subtotal;
					$addon_qty .= !empty($addon_qty)?','.($addons_items->qty+1):$addons_items->qty+1;
				}
			}
		if ($q->num_rows() > 0) {
			$ritem=$q->row();
            $sale_id =  $q->row('sale_id');
	    	$recipe_id = $q->row('recipe_id');
			$cm = $this->db->get_where('category_mapping',array('product_id'=>$ritem->recipe_id,'status'=>1))->row();
			$cate['category_id'] = $cm->id;
			$cate['category_id'] = $cm->category_id;
			$cate['subcategory_id'] = $cm->subcategory_id;
			$cate['brand_id'] = $cm->brand_id;
			if($this->Settings->procurment == 1){
			if($ritem->recipe_type=='standard' || $ritem->recipe_type =='production'){
			$this->site->updateStockMaster_new($ritem->recipe_id,$ritem->recipe_variant_id,1,$cate,$ritem->id);
			}else if($ritem->recipe_type =="quick_service"){
			$this->production_salestock_out($ritem->recipe_id,1,$ritem->recipe_variant_id);
			}	
		}
        }
        $unit_qty =$q->row('unit_price')*($q->row('quantity')+1);
	    $order_item_array = array(
		'quantity' => $q->row('quantity')+1,
		'subtotal' => ($unit_qty+$addonTot),
		'addon_qty'=>$addon_qty
		);	
		$this->db->where('id', $order_item_id);
		if ($this->db->update('order_items',  $order_item_array)) {			
			return true;
		}
		return false;
    }
    
    public function InsertBill($order_data = array(), $order_item = array(), $billData = array(), $splitData = array(), $sales_total = NULL, $delivery_person = NULL,$timelog_array = NULL, $notification_array = array(),$order_item_id =array(), $split_id, $request_discount, $dine_in_discount,$birthday){		
		if(empty($dine_in_discount)){
			foreach ($request_discount as $request) {
				$check = $this->db->select('*')->where('split_id', $split_id)->get('customer_request_discount');
				if($check->num_rows() > 0){
					$this->db->where('split_id', $split_id);
					$q = $this->db->update('customer_request_discount', $request);
				}else{
					
					$q = $this->db->insert('customer_request_discount', $request);
				}				
			}
		}
				$sales_array = array(
		            'grand_total' => $sales_total,
					'delivery_person_id' => $delivery_person
		        );
			if($this->Settings->socket_enable !=0){
				$this->site->create_notification($notification_array);
			}
				foreach ($timelog_array as $time) {
              	$res = $this->db->insert('time_log', $time);
                 }     	   	
        if ($this->db->insert('sales', $order_data)) {
            $sale_id = $this->db->insert_id();
	    /*********** seats count **************/
		    $splitID = $order_data['sales_split_id'];
		    $this->db->where(array('split_id'=>$splitID));
		    $this->db->where('seats_id !=',0);
		    $order_seats_query = $this->db->get('orders');
		    $bill_seats = 0;
		    if($order_seats_query->num_rows()>0){
		       $bill_seats = $order_seats_query->row('seats_id');
		    }
	    /*********** seats count **************/
        	if(!empty($birthday)){ 	$this->db->insert('birthday', $birthday); }
            $this->db->update('sales', $sales_array, array('id' => $sale_id));
	   
            foreach ($billData as $key =>  $bills) {
             	$bills['sales_id'] = $sale_id;
				$bills['table_whitelisted'] = $this->isTableWhitelisted($order_data['sales_table_id']);
				$bills['seats'] = $bill_seats;
              	$this->db->insert('bils', $bills);
				$bill_id = $this->db->insert_id();
				//$bill_number = sprintf("%'.05d", $bill_id);
				//$bill_number = $this->site->generate_bill_number(0,$bill_id);		
		        // $bill_number = $this->site->generate_bill_number($bills['table_whitelisted']);
              	//$this->db->update('bils', array('bill_number' => $bill_number,'bill_sequence_number' => $bill_id), array('id' => $bill_id));
		/// update latest bill////
		// $this->site->latest_bill($bill_number);
				$this->db->update('bils', array('bill_sequence_number' => $bill_id), array('id' => $bill_id));
				
				foreach ($splitData[$key]  as $bill_items) {
					$bill_items['bil_id'] = $bill_id;
					$bill_items['item_type'] = ($bill_items['net_unit_price'])?"Paid":"Complimentary";
					$this->db->insert('bil_items', $bill_items);//echo '<pre>';print_R($bill_items);print_R($this->db->error());exit;
					/*addon array*/
							$bill_item_id = $this->db->insert_id();
							$recipe_addon_item =[];
							$addonid = explode(',',$bill_items['addon_id'] ); 
							$addonqty = explode(',', $bill_items['addon_qty']);						
							foreach ($addonid as $key => $addon) {	
							if($addon !='null') :
								$AddonDetails = $this->site->getaddonitemid($addon);
								$recipeDetails = $this->pos_model->getrecipeByID($AddonDetails->addon_item_id);
								$recipe_addon_item[] = array(									
									'bill_id'      => $bill_id,
									'bill_item_id'      => $bill_item_id,
									'sale_item_id'      => $recipeDetails->id,
									'addon_id'      => $addon,
									'price'      => $recipeDetails->price ? $recipeDetails->price : 0,
									'qty'      => $addonqty[$key],
									'subtotal'      => ($addonqty[$key] * $recipeDetails->price),
								);
							endif;	
							}								
							foreach ($recipe_addon_item as $recipe_addon) {
								$this->db->insert('addon_bill_items', $recipe_addon);
							}
										
					/*addon array*/	
				}
            }

            foreach ($order_item as $item) {
            	/*echo "<pre>";
print_r($item);die;*/
                $this->db->insert('sale_items', $item);
                // print_r($this->db->last_query());die;
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
           $this->db->where('split_id',$split_id);
		   $this->db->update('orders',array("sale_status"=>'Bill Generated',"payment_status"=>'Bill Generated'));
            if ($order_data['sale_status'] == 'completed') {
            }
               //   print_r($this->db->error());die;
             $this->site->updateTableStatus($order_data['sales_table_id'],4,$this->session->userdata('user_id')); 
            return true;
        }//print_r($this->db->error());die;
        return false;
    } 
	
    public function Payment($update_bill = array(), $bill_id =NULL, $payment = array(), $multi_currency = array(), $salesid = NULL, $sales_bill = array(), $order_split_id = NULL, $notification_array,$updateCreditLimit,$total,$customer_id,$loyalty_used_points,$taxation,$customer_changed)
    {    
    	
		$q = $this->db->select('sales_split_id, sales_table_id')->where('id', $salesid)->get('sales');		
		if($this->pos_settings->bill_series_settings == 1){
			$bill_number = $this->site->Payment_dine_bill_number($taxation,$bill_id);		
			$bill_no = $bill_number;
			$this->db->update('bils', array('bill_number' => $bill_number), array('id' => $bill_id));
		}else{
			$bill_number = $this->site->generate_bill_number($taxation,$bill_id);	
			$this->db->update('bils', array('bill_number' => $bill_number), array('id' => $bill_id));
		}
		$bill_no = $bill_number;		
		// var_dump($bill_no);die;
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
		$notification_array['insert_array']['msg'] = 'Cashier has been payment this bil ('.$bill_no.'';
		$notification_array['insert_array']['table_id'] = $table_id;
		$notification_array['insert_array']['type'] = 'Cashier payment bils';
		$notification_array['insert_array']['role_id'] = WAITER;
		$notification_array['insert_array']['to_user_id'] = $waiter_id;	
		$this->site->create_notification($notification_array);
		
    	if ($this->db->update('bils', $update_bill, array('id' => $bill_id))){
			$this->db->update('sales', $sales_bill, array('id' => $salesid));
    			$order_count = $this->db->get_where('bils', array('sales_id' => $salesid));
				$order_count =$order_count->num_rows();
				
				 $order_closed_count = $this->db->get_where('bils', array('bils.sales_id' => $salesid,'bils.payment_status' => 'Completed'));
				 $order_closed_count =$order_closed_count->num_rows();

    		foreach ($payment as $item) {
		    $item['customer_payment_type'] = $updateCreditLimit['customer_type'];
		    $this->db->insert('payments', $item);
		    $pid = $this->db->insert_id();
		    
		    if($pid && $item['paid_by']=='credit'){
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
		    }
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
		        	$this->site->UpdateCustomerFromLoyalty($customer_id,$bill_id,$salesid,$split_id);	       
		        }

		/*Loyalty inser and Update*/	
		 $this->site->LoyaltyinserAndUpdate($bill_id,$total,$customer_id,$loyalty_used_points);	  
		 $this->site->update_TableStatus_after_payment($table_id,0,$salesid,$split_id);
		 // print_r($this->db->error());die;
    	 return true;
    	}    	
    	// print_r($this->db->error());die;
    	return false;
    }   

     public function getAllBillitems($id =NULL)
    {
    	 /* $Billitems = "SELECT BI.id,BI.recipe_name,BI.recipe_id,BI.input_discount,BI.unit_price  AS net_unit_price,BI.manual_item_discount,BI.manual_item_discount_per_val,SUM(BI.quantity) AS quantity,SUM(BI.subtotal) AS subtotal,R.khmer_name,BI.discount,BI.recipe_variant,B.customer_discount_id,BI.item_discount,BI.off_discount,BI.input_discount,BI.birthday_discount,BI.customer_discount_val,BI.comment,BI.comment_price,BI.recipe_variant_id
                    FROM ".$this->db->dbprefix('bil_items')." AS BI
                    JOIN ". $this->db->dbprefix('recipe') ." AS R ON R.id = BI.recipe_id
                    JOIN ". $this->db->dbprefix('bils') ." AS B ON B.id = BI.bil_id
                   WHERE BI.bil_id='".$id."' GROUP BY BI.recipe_name,BI.recipe_variant_id"; */
				   $Billitems = "SELECT BI.id,BI.recipe_name,BI.recipe_id,BI.input_discount,BI.unit_price  ,BI.  net_unit_price,BI.manual_item_discount,BI.manual_item_discount_per_val,BI.quantity AS quantity,BI.subtotal AS subtotal,R.khmer_name,BI.discount,BI.recipe_variant,B.customer_discount_id,BI.item_discount,BI.off_discount,BI.input_discount,BI.birthday_discount,BI.customer_discount_val,BI.comment,BI.comment_price,BI.recipe_variant_id
                    FROM ".$this->db->dbprefix('bil_items')." AS BI
                    JOIN ". $this->db->dbprefix('recipe') ." AS R ON R.id = BI.recipe_id
                    JOIN ". $this->db->dbprefix('bils') ." AS B ON B.id = BI.bil_id
                   WHERE BI.bil_id='".$id."' ";
            /*echo $Billitems;die;*/
        $q = $this->db->query($Billitems);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
            	$addondetails='';
            	$addondetails = $this->site->getAddonByRecipeidAndBillitemid($row->recipe_id,$row->id);
            	if($addondetails){
            		$addondetails = $addondetails;
            	}
            	$star ='';	     	    	   
                if (!empty($row->customer_discount_id)) {  
                  /*if($this->settings->customer_discount == 'customer'){                    */
                        $check = $this->site->Check_item_Discount_customer($row->recipe_id,$row->customer_discount_id);  
                 /* }
                  else{                    
                       $check = $this->site->Check_item_Discount_manual($row->recipe_id,$row->customer_discount_id);  
                  }  */                         
                  if($check){
                    $star ='';                        
                  }else{
                    $star ='*';
                  }                
	            } 
	            $row->star = $star;
	            $row->addondetails = $addondetails;

                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	    public function getAllBillitems_archival($id =NULL)
    {
    	 /* $Billitems = "SELECT BI.id,BI.recipe_name,BI.recipe_id,BI.input_discount,BI.unit_price  AS net_unit_price,BI.manual_item_discount,BI.manual_item_discount_per_val,SUM(BI.quantity) AS quantity,SUM(BI.subtotal) AS subtotal,R.khmer_name,BI.discount,BI.recipe_variant,B.customer_discount_id,BI.item_discount,BI.off_discount,BI.input_discount,BI.birthday_discount,BI.customer_discount_val,BI.comment,BI.comment_price,BI.recipe_variant_id
                    FROM ".$this->db->dbprefix('bil_items')." AS BI
                    JOIN ". $this->db->dbprefix('recipe') ." AS R ON R.id = BI.recipe_id
                    JOIN ". $this->db->dbprefix('bils') ." AS B ON B.id = BI.bil_id
                   WHERE BI.bil_id='".$id."' GROUP BY BI.recipe_name,BI.recipe_variant_id"; */
				   $Billitems = "SELECT BI.id,BI.recipe_name,BI.recipe_id,BI.input_discount,BI.unit_price  AS net_unit_price,BI.manual_item_discount,BI.manual_item_discount_per_val,BI.quantity AS quantity,BI.subtotal AS subtotal,R.khmer_name,BI.discount,BI.recipe_variant,B.customer_discount_id,BI.item_discount,BI.off_discount,BI.input_discount,BI.birthday_discount,BI.customer_discount_val,BI.comment,BI.comment_price,BI.recipe_variant_id
                    FROM ".$this->db->dbprefix('bil_items_archival')." AS BI
                    JOIN ". $this->db->dbprefix('recipe') ." AS R ON R.id = BI.recipe_id
                    JOIN ". $this->db->dbprefix('bils_archival') ." AS B ON B.id = BI.bil_id
                   WHERE BI.bil_id='".$id."' ";
            //echo $Billitems;die;
        $q = $this->db->query($Billitems);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
            	$addondetails='';
            	$addondetails = $this->site->getAddonByRecipeidAndBillitemid_archival($row->recipe_id,$row->id);
            	if($addondetails){
            		$addondetails = $addondetails;
            	}
            	$star ='';	     	    	   
                if (!empty($row->customer_discount_id)) {  
                  /*if($this->settings->customer_discount == 'customer'){                    */
                        $check = $this->site->Check_item_Discount_customer($row->recipe_id,$row->customer_discount_id);  
                 /* }
                  else{                    
                       $check = $this->site->Check_item_Discount_manual($row->recipe_id,$row->customer_discount_id);  
                  }  */                         
                  if($check){
                    $star ='';                        
                  }else{
                    $star ='*';
                  }                
	            } 
	            $row->star = $star;
	            $row->addondetails = $addondetails;

                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    public function getBillDiscountNames($id =NULL){
    	 $Billitems = "SELECT D.name
                    FROM ".$this->db->dbprefix('bil_items')." AS BI
                    JOIN ". $this->db->dbprefix('bils') ." AS B ON B.id = BI.bil_id
                    LEFT JOIN ". $this->db->dbprefix('discounts') ." AS D ON D.id = BI.discount
                    WHERE BI.bil_id='".$id."' GROUP BY B.id ";       
                                 
        $q = $this->db->query($Billitems);
        $dis ="";
        $predefine ='';
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $predefine = $row->name;
                if($row->name != ''){
                 $dis = $dis.$row->name;
                } 
            }            
        }
		
    	$customerdis = "SELECT C.name
                    FROM ".$this->db->dbprefix('bils')." AS B
                    LEFT JOIN ". $this->db->dbprefix('diccounts_for_customer') ." AS C ON C.id = B.customer_discount_id                    
                    WHERE B.id='".$id."'"; 
                    
        $c = $this->db->query($customerdis);
        $customer ='';
        if ($c->num_rows() > 0) {
            foreach (($c->result()) as $row) {
                $customer = $row->name;
                if(	$dis !='' && $row->name != ''){
                $dis = $dis.','.$row->name;
               }
               else{
               	$dis = $dis.$row->name;
               }
            }            
        }

        $total_dis = "SELECT T.name
                    FROM ".$this->db->dbprefix('bils')." AS B
                    LEFT JOIN ". $this->db->dbprefix('discounts') ." AS T ON T.id = B.order_discount_id                    
                    WHERE B.id='".$id."'";                                      
        $t = $this->db->query($total_dis);
        $total ='';        
        if ($t->num_rows() > 0) {
            foreach (($t->result()) as $row) {
                $total = $row->name;
                if($row->name !=NULL && $dis !=''){
                  $dis = $dis.','.$row->name;
                }
                else{
                $dis = $dis.$row->name;	
                }
            }            
        }

        $checkmannual = "SELECT B.discount_type,B.discount_val
                    FROM ".$this->db->dbprefix('bils')." AS B                    
                    WHERE B.id='".$id."'";    	
        $m = $this->db->query($checkmannual);
		if ($m->num_rows() > 0) {
            foreach (($m->result()) as $row) {
                if($row->discount_type == 'manual' && $dis !='' ){
                	$dis = $dis.','.'Discount('.$row->discount_val.')';
                } else{
                  $dis = $dis;	
                }
            }
        }    
        if($dis != ''){        	
        	return $dis ;
        }
		
        return '';
    } 
	
	 public function getBillDiscountNames_archival($id =NULL){
    	 $Billitems = "SELECT D.name
                    FROM ".$this->db->dbprefix('bil_items_archival')." AS BI
                    JOIN ". $this->db->dbprefix('bils_archival') ." AS B ON B.id = BI.bil_id
                    LEFT JOIN ". $this->db->dbprefix('discounts') ." AS D ON D.id = BI.discount
                    WHERE BI.bil_id='".$id."' GROUP BY B.id ";       
                                 
        $q = $this->db->query($Billitems);
        $dis ="";
        $predefine ='';
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $predefine = $row->name;
                if($row->name != ''){
                 $dis = $dis.$row->name;
                } 
            }            
        }
		
    	$customerdis = "SELECT C.name
                    FROM ".$this->db->dbprefix('bils_archival')." AS B
                    LEFT JOIN ". $this->db->dbprefix('diccounts_for_customer') ." AS C ON C.id = B.customer_discount_id                    
                    WHERE B.id='".$id."'"; 
                    
        $c = $this->db->query($customerdis);
        $customer ='';
        if ($c->num_rows() > 0) {
            foreach (($c->result()) as $row) {
                $customer = $row->name;
                if(	$dis !='' && $row->name != ''){
                $dis = $dis.','.$row->name;
               }
               else{
               	$dis = $dis.$row->name;
               }
            }            
        }

        $total_dis = "SELECT T.name
                    FROM ".$this->db->dbprefix('bils_archival')." AS B
                    LEFT JOIN ". $this->db->dbprefix('discounts') ." AS T ON T.id = B.order_discount_id                    
                    WHERE B.id='".$id."'";                                      
        $t = $this->db->query($total_dis);
        $total ='';        
        if ($t->num_rows() > 0) {
            foreach (($t->result()) as $row) {
                $total = $row->name;
                if($row->name !=NULL && $dis !=''){
                  $dis = $dis.','.$row->name;
                }
                else{
                $dis = $dis.$row->name;	
                }
            }            
        }

        $checkmannual = "SELECT B.discount_type,B.discount_val
                    FROM ".$this->db->dbprefix('bils_archival')." AS B                    
                    WHERE B.id='".$id."'";    	
        $m = $this->db->query($checkmannual);
		if ($m->num_rows() > 0) {
            foreach (($m->result()) as $row) {
                if($row->discount_type == 'manual' && $dis !='' ){
                	$dis = $dis.','.'Discount('.$row->discount_val.')';
                } else{
                  $dis = $dis;	
                }
            }
        }    
        if($dis != ''){        	
        	return $dis ;
        }
		
        return '';
    } 
   public function getBillDiscountNamesbysplitname($split_id =NULL)
    {
    	 $Billitems = "SELECT D.name
                    FROM ".$this->db->dbprefix('bil_items')." AS BI
                    JOIN ". $this->db->dbprefix('bils') ." AS B ON B.id = BI.bil_id
                    JOIN ". $this->db->dbprefix('sales') ." AS S ON S.id = B.sales_id
                    JOIN ". $this->db->dbprefix('orders') ." AS O ON O.split_id = S.sales_split_id
                    LEFT JOIN ". $this->db->dbprefix('discounts') ." AS D ON D.id = BI.discount
                    WHERE S.sales_split_id='".$split_id."' GROUP BY B.id ";       
                                 
        $q = $this->db->query($Billitems);
        $dis ="";
        $predefine ='';
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $predefine = $row->name;
                if($row->name != ''){
                 $dis = $dis.$row->name;
                } 
            }            
        }
		
    	$customerdis = "SELECT C.name
                    FROM ".$this->db->dbprefix('bils')." AS B
                      JOIN ". $this->db->dbprefix('sales') ." AS S ON S.id = B.sales_id
                      JOIN ". $this->db->dbprefix('orders') ." AS O ON O.split_id = S.sales_split_id
                      LEFT JOIN ". $this->db->dbprefix('diccounts_for_customer') ." AS C ON C.id = B.customer_discount_id                    
                    WHERE S.sales_split_id='".$split_id."' "; 
                    
        $c = $this->db->query($customerdis);
        $customer ='';
        if ($c->num_rows() > 0) {
            foreach (($c->result()) as $row) {
                $customer = $row->name;
                if(	$dis !='' && $row->name != ''){
                $dis = $dis.','.$row->name;
               }
               else{
               	$dis = $dis.$row->name;
               }
            }            
        }

        $total_dis = "SELECT T.name
                    FROM ".$this->db->dbprefix('bils')." AS B
                    JOIN ". $this->db->dbprefix('sales') ." AS S ON S.id = B.sales_id
                    JOIN ". $this->db->dbprefix('orders') ." AS O ON O.split_id = S.sales_split_id
                    LEFT JOIN ". $this->db->dbprefix('discounts') ." AS T ON T.id = B.order_discount_id                    
                    WHERE S.sales_split_id='".$split_id."' ";                                      
        $t = $this->db->query($total_dis);
        $total ='';        
        if ($t->num_rows() > 0) {
            foreach (($t->result()) as $row) {
                $total = $row->name;
                if($row->name !=NULL && $dis !=''){
                  $dis = $dis.','.$row->name;
                }
                else{
                $dis = $dis.$row->name;	
                }
            }            
        }

        $checkmannual = "SELECT B.discount_type,B.discount_val
                    FROM ".$this->db->dbprefix('bils')." AS B      
                    JOIN ". $this->db->dbprefix('sales') ." AS S ON S.id = B.sales_id
                    JOIN ". $this->db->dbprefix('orders') ." AS O ON O.split_id = S.sales_split_id              
                    WHERE S.sales_split_id='".$split_id."'";    	
        $m = $this->db->query($checkmannual);
		if ($m->num_rows() > 0) {
            foreach (($m->result()) as $row) {
                if($row->discount_type == 'manual' && $dis !='' ){
                	$dis = $dis.','.'Discount('.$row->discount_val.')';
                } else{
                  $dis = $dis;	
                }
            }
        }    
        if($dis != ''){        	
        	return $dis ;
        }
		
        return '';
    } 
    public function BBQgetBillDiscountNames($id =NULL)
    {
    	/* $Billitems = "SELECT D.name
                    FROM ".$this->db->dbprefix('bil_items')." AS BI
                    JOIN ". $this->db->dbprefix('bils') ." AS B ON B.id = BI.bil_id
                    LEFT JOIN ". $this->db->dbprefix('discounts') ." AS D ON D.id = BI.discount
                    WHERE BI.bil_id='".$id."' GROUP BY B.id ";       
                                
        $q = $this->db->query($Billitems);*/
        $dis ="";
        $predefine ='';
        /*if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $predefine = $row->name;
                if($row->name != ''){
                 $dis = $dis.$row->name;
                } 
            }            
        }*/
    	$customerdis = "SELECT C.name
                    FROM ".$this->db->dbprefix('bils')." AS B
                    LEFT JOIN ". $this->db->dbprefix('diccounts_for_bbq') ." AS C ON C.id = B.customer_discount_id                    
                    WHERE B.id='".$id."'"; 
                    
        $c = $this->db->query($customerdis);
        $customer ='';
        if ($c->num_rows() > 0) {
            foreach (($c->result()) as $row) {
                $customer = $row->name;
                if(	$dis !='' && $row->name != ''){
                $dis = $dis.','.$row->name;
               }
               else{
               	$dis = $dis.$row->name;
               }
            }            
        }

        /*$total_dis = "SELECT T.name
                    FROM ".$this->db->dbprefix('bils')." AS B
                    LEFT JOIN ". $this->db->dbprefix('discounts') ." AS T ON T.id = B.order_discount_id                    
                    WHERE B.id='".$id."'";                                   
        $t = $this->db->query($total_dis);*/   
        $total ='';        
       /* if ($t->num_rows() > 0) {
            foreach (($t->result()) as $row) {
                $total = $row->name;
                if($row->name !=NULL && $dis !=''){
                  $dis = $dis.','.$row->name;
                }
                else{
                $dis = $dis.$row->name;	
                }
            }            
        }   */     
        if($dis != ''){        	
        	return $dis ;
        }        
        return '';
    }    
	/*public function getAllBillitems($id =NULL)
      {
		$this->db->select('bil_items.*, recipe.khmer_name')
		->join('recipe', 'recipe.id = bil_items.recipe_id')
		->where('bil_items.bil_id', $id);
		$q = $this->db->get('bil_items');*/
       // $q = $this->db->get_where('bil_items', array('bil_id' => $id));

        /*if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }*/
    public function get_BillData($id) {
    $this->db->select("bils.*,tax_rates.name as tax_name, tax_rates.rate as tax_rate,restaurant_tables.name table_name,orders.order_type")
    ->join('tax_rates', 'tax_rates.id = bils.tax_id','left')
    ->join('sales', 'sales.id = bils.sales_id')
    ->join('orders', 'orders.split_id = sales.sales_split_id')
    ->join('restaurant_tables', 'restaurant_tables.id = orders.table_id','left')
    ->where('bils.id', $id);
	$q = $this->db->get('bils');

       /*$q = $this->db->get_where('bils', array('id' => $id));*/
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }   

    public function getBillData($sale_id){

        $this->db->select("sales.*");     

        $q = $this->db->get_where('sales', array('sales.id' => $sale_id));

        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $data[] = $row;
            }
            
            return $data;
        }
       
        return FALSE;
    }
	
public function getItemSaleReports($start,$end,$report_view_access,$report_show,$group,$subgroup){
		if($group !=""){
			$categorys = "AND R.category_id ='".$group."'";
		} else {
			$categorys="";
		}
		if($subgroup !=""){
			$subcategory = "AND R.subcategory_id ='".$subgroup."'";
		} else {
			$subcategory="";
		}
		$where = '';
         if($report_view_access != 1)
         {
             $where .= " AND B.table_whitelisted = ".$report_show." ";
         }
        $category = "SELECT RC.id AS cate_id,RC.name as category, 'split_order' 
        FROM " . $this->db->dbprefix('recipe_categories') . " RC
        JOIN " . $this->db->dbprefix('recipe') . " R ON  R.category_id = RC.id
        JOIN " . $this->db->dbprefix('bil_items') . " BI ON BI.recipe_id = R.id
        JOIN " . $this->db->dbprefix('bils') . " B ON B.id = BI.bil_id        
        WHERE DATE(B.date) BETWEEN '".$start."' AND '".$end."' AND
         B.payment_status ='Completed' ".$categorys." ".$subcategory." ".$where." GROUP BY RC.id";   
         //var_dump($category);exit;            
        $t = $this->db->query($category);   
        if ($t->num_rows() > 0) {
     /*$this->db->select("recipe_categories.id AS cate_id,recipe_categories.name as category,SUM(" . $this->db->dbprefix('bils') . ".grand_total) AS grand_total,SUM(" . $this->db->dbprefix('bils') . ".round_total) AS round_total, 'split_order'")
        ->join('recipe', 'recipe.category_id = recipe_categories.id')
        ->join('bil_items', 'bil_items.recipe_id = recipe.id')
        ->join('bils', 'bils.id = bil_items.bil_id')
        ->where('recipe_categories.parent_id', NULL)
        ->or_where('recipe_categories.parent_id',0);
                
        $this->db->group_by('recipe_categories.id');        
        $t = $this->db->get('recipe_categories');      
        
        if ($t->num_rows() > 0) {*/
            
            foreach ($t->result() as $row) {
                    $this->db->select("recipe_categories.id AS sub_id,recipe_categories.name AS sub_category,bils.total_tax, 'order'")
                    ->join('recipe', 'recipe.subcategory_id = recipe_categories.id')
                    ->join('bil_items', 'bil_items.recipe_id = recipe.id')
                    ->join('bils', 'bils.id = bil_items.bil_id')
                    ->where('recipe.category_id', $row->cate_id);
                    if($report_view_access != 1){
	                     $this->db->where('bils.table_whitelisted', $report_show);                      
	                 }
                    $this->db->group_by('recipe.subcategory_id');
                    $s = $this->db->get('recipe_categories');
                      if ($s->num_rows() > 0) {
                        foreach ($s->result() as $sow) {
                        	if($group !=""){
								$categorys = "AND R.category_id ='".$group."'";
									} else {
										$categorys="";
										}
							if($subgroup !=""){
							$subcategory = "AND R.subcategory_id ='".$subgroup."'";
							} else {
								$subcategory="";
							}

								$myQuery = "SELECT R.name,SUM(BI.item_discount) AS item_discount,SUM(BI.off_discount) AS off_discount,SUM(BI.input_discount) AS input_discount,SUM(BI.manual_item_discount) AS manual_item_discount,SUM(CASE WHEN (BI.tax_type= 1) THEN BI.tax ELSE 0 END) as tax,SUM(BI.service_charge_amount) AS service_charge_amount,SUM(BI.quantity) AS quantity,SUM(BI.subtotal) AS subtotal,CASE WHEN RV.name  is NOT NULL THEN RV.name ELSE 'No Variant' END AS variant
								FROM " . $this->db->dbprefix('bil_items') . " BI
								JOIN " . $this->db->dbprefix('recipe') . " R ON R.id = BI.recipe_id
								JOIN " . $this->db->dbprefix('bils') . " B ON B.id = BI.bil_id
								LEFT JOIN " . $this->db->dbprefix('recipe_variants') . " RV ON RV.id = BI.recipe_variant_id
								WHERE DATE(B.date) BETWEEN '".$start."' AND '".$end."' AND
								R.subcategory_id =".$sow->sub_id."  AND  B.payment_status ='Completed'   ".$categorys." ".$subcategory."
								GROUP BY R.id,BI.recipe_variant_id,item_type ";
								$o = $this->db->query($myQuery);

                                $split[$row->cate_id][] = $sow;
                                if ($o->num_rows() > 0) {                                    
                                    foreach($o->result() as $oow){
                                        $order[$sow->sub_id][] = $oow;
                                    }
                                }
                                $sow->order = $order[$sow->sub_id];  
///add_ons
								$myQuery1 = "SELECT CONCAT(R.name,'(Add-ons)') name,0 AS item_discount,0 AS off_discount,0 AS input_discount,0 AS manual_item_discount,
											0 AS tax,0 AS service_charge_amount,SUM(ai.qty) AS quantity,SUM(ai.subtotal) AS subtotal,
											CASE WHEN RV.name IS NOT NULL THEN RV.name ELSE 'No Variant' END AS variant
								FROM " . $this->db->dbprefix('addon_bill_items') . " ai 
								left join  ".$this->db->dbprefix('bil_items')." BI ON ai.bill_item_id=BI.id
								JOIN " . $this->db->dbprefix('recipe') . " R ON R.id = ai.sale_item_id
								JOIN " . $this->db->dbprefix('bils') . " B ON B.id = BI.bil_id
								LEFT JOIN " . $this->db->dbprefix('recipe_variants') . " RV ON RV.id = BI.recipe_variant_id
								WHERE DATE(B.date) BETWEEN '".$start."' AND '".$end."' AND
								  B.payment_status ='Completed'   ".$categorys." 
								GROUP BY R.id,BI.recipe_variant_id ";
								$o1 = $this->db->query($myQuery1);
									if ($o1->num_rows() > 0) {                                    
                                    foreach($o1->result() as $oow1){
                                        $order[$sow->sub_id][] = $oow1;
                                    }
                                }
                                $sow->order = $order[$sow->sub_id];  
								
                        }                    
                    $row->split_order = $split[$row->cate_id];
                }else{
                    $row->split_order = array();
                }                
                $data[] = $row;

            }            
            return $data;
        }        
        return FALSE;   
    }
	
	
	
public function getItemSaleReports_archival($start,$end,$report_view_access,$report_show,$group,$subgroup){
		if($group !=""){
			$categorys = "AND R.category_id ='".$group."'";
		} else {
			$categorys="";
		}
		if($subgroup !=""){
			$subcategory = "AND R.subcategory_id ='".$subgroup."'";
		} else {
			$subcategory="";
		}
		$where = '';
         if($report_view_access != 1)
         {
             $where .= " AND B.table_whitelisted = ".$report_show." ";
         }
        $category = "SELECT RC.id AS cate_id,RC.name as category, 'split_order' 
        FROM " . $this->db->dbprefix('recipe_categories') . " RC
        JOIN " . $this->db->dbprefix('recipe') . " R ON  R.category_id = RC.id
        JOIN " . $this->db->dbprefix('bil_items_archival') . " BI ON BI.recipe_id = R.id
        JOIN " . $this->db->dbprefix('bils_archival') . " B ON B.id = BI.bil_id        
        WHERE DATE(B.date) BETWEEN '".$start."' AND '".$end."' AND
         B.payment_status ='Completed' ".$categorys." ".$subcategory." ".$where." GROUP BY RC.id";   
         //var_dump($category);exit;            
        $t = $this->db->query($category);   
        if ($t->num_rows() > 0) {
     /*$this->db->select("recipe_categories.id AS cate_id,recipe_categories.name as category,SUM(" . $this->db->dbprefix('bils') . ".grand_total) AS grand_total,SUM(" . $this->db->dbprefix('bils') . ".round_total) AS round_total, 'split_order'")
        ->join('recipe', 'recipe.category_id = recipe_categories.id')
        ->join('bil_items', 'bil_items.recipe_id = recipe.id')
        ->join('bils', 'bils.id = bil_items.bil_id')
        ->where('recipe_categories.parent_id', NULL)
        ->or_where('recipe_categories.parent_id',0);
                
        $this->db->group_by('recipe_categories.id');        
        $t = $this->db->get('recipe_categories');      
        
        if ($t->num_rows() > 0) {*/
            
            foreach ($t->result() as $row) {
                    $this->db->select("recipe_categories.id AS sub_id,recipe_categories.name AS sub_category,bils_archival.total_tax, 'order'")
                    ->join('recipe', 'recipe.subcategory_id = recipe_categories.id')
                    ->join('bil_items_archival', 'bil_items_archival.recipe_id = recipe.id')
                    ->join('bils_archival', 'bils_archival.id = bil_items_archival.bil_id')
                    ->where('recipe.category_id', $row->cate_id);
                    if($report_view_access != 1){
	                     $this->db->where('bils_archival.table_whitelisted', $report_show);                      
	                 }
                    $this->db->group_by('recipe.subcategory_id');
                    $s = $this->db->get('recipe_categories');
					
                      if ($s->num_rows() > 0) {
                        foreach ($s->result() as $sow) {
                        	if($group !=""){
								$categorys = "AND R.category_id ='".$group."'";
									} else {
										$categorys="";
										}
							if($subgroup !=""){
							$subcategory = "AND R.subcategory_id ='".$subgroup."'";
							} else {
								$subcategory="";
							}

								$myQuery = "SELECT R.name,SUM(BI.item_discount) AS item_discount,SUM(BI.off_discount) AS off_discount,SUM(BI.input_discount) AS input_discount,SUM(BI.manual_item_discount) AS manual_item_discount,SUM(CASE WHEN (BI.tax_type= 1) THEN BI.tax ELSE 0 END) as tax,SUM(BI.service_charge_amount) AS service_charge_amount,SUM(BI.quantity) AS quantity,SUM(BI.subtotal) AS subtotal,CASE WHEN RV.name  is NOT NULL THEN RV.name ELSE 'No Variant' END AS variant
								FROM " . $this->db->dbprefix('bil_items_archival') . " BI
								JOIN " . $this->db->dbprefix('recipe') . " R ON R.id = BI.recipe_id
								JOIN " . $this->db->dbprefix('bils_archival') . " B ON B.id = BI.bil_id
								LEFT JOIN " . $this->db->dbprefix('recipe_variants') . " RV ON RV.id = BI.recipe_variant_id
								WHERE DATE(B.date) BETWEEN '".$start."' AND '".$end."' AND
								R.subcategory_id =".$sow->sub_id."  AND  B.payment_status ='Completed'   ".$categorys." ".$subcategory."
								GROUP BY R.id,BI.recipe_variant_id,item_type ";
								$o = $this->db->query($myQuery);

                                $split[$row->cate_id][] = $sow;
                                if ($o->num_rows() > 0) {                                    
                                    foreach($o->result() as $oow){
                                        $order[$sow->sub_id][] = $oow;
                                    }
                                }
                                $sow->order = $order[$sow->sub_id];  
///add_ons


								/* $myQuery1 = "SELECT CONCAT(R.name,'(Add-ons)') name,0 AS item_discount,0 AS off_discount,0 AS input_discount,0 AS manual_item_discount,
											0 AS tax,0 AS service_charge_amount,SUM(ai.qty) AS quantity,SUM(ai.subtotal) AS subtotal,
											CASE WHEN RV.name IS NOT NULL THEN RV.name ELSE 'No Variant' END AS variant
								FROM " . $this->db->dbprefix('addon_bill_items_archival') . " ai 
								left join  ".$this->db->dbprefix('bil_items_archival')." BI ON ai.bill_item_id=BI.id
								JOIN " . $this->db->dbprefix('recipe') . " R ON R.id = ai.sale_item_id
								JOIN " . $this->db->dbprefix('bils_archival') . " B ON B.id = BI.bil_id
								LEFT JOIN " . $this->db->dbprefix('recipe_variants') . " RV ON RV.id = BI.recipe_variant_id
								WHERE DATE(B.date) BETWEEN '".$start."' AND '".$end."' AND
								  B.payment_status ='Completed'   ".$categorys." 
								GROUP BY R.id,BI.recipe_variant_id ";
								
								$o1 = $this->db->query($myQuery1);
									if ($o1->num_rows() > 0) {                                    
                                    foreach($o1->result() as $oow1){
                                        $order[$sow->sub_id][] = $oow1;
                                    }
                                }
                                $sow->order = $order[$sow->sub_id];   */
								
                        }                    
                    $row->split_order = $split[$row->cate_id];
                }else{
                    $row->split_order = array();
                }                
                $data[] = $row;

            }            
            return $data;
        }        
        return FALSE;   
    }

	public function getdaysummary($start,$end,$report_view_access,$report_show)
    {	
        $this->db->select('SUM(COALESCE(total, 0)) as total,SUM(COALESCE(grand_total, 0)) as total_amount1, SUM(COALESCE(total_tax, 0)) as total_tax,SUM(COALESCE(service_charge_amount, 0)) as service_charge_amount, SUM(COALESCE(total_discount+bbq_cover_discount+bbq_daywise_discount, 0)) as total_discount,SUM(total-total_discount+CASE WHEN (tax_type= 1) THEN total_tax ELSE 0 END) as total_amount, COUNT(' . $this->db->dbprefix('bils') . '.id) as totalbill,SUM(total-total_discount-bbq_cover_discount-bbq_daywise_discount+service_charge_amount +CASE WHEN (tax_type= 1) THEN total_tax ELSE 0 END)as net_amt,SUM(total) as gross_amt,SUM(total-CASE WHEN (tax_type= 0) THEN total_tax ELSE 0 END) as netamt', FALSE)
			->where('payment_status', 'Completed')
			
			->where('DATE(date) >=', $start)
			->where('DATE(date) <=', $end);
			if($report_view_access != 1)
		     {
		         $this->db->where('table_whitelisted', $report_show);                      
		     }

			 /*->where('S.status',1);    		 
		    if($customer_id){
		    	$this->db->where('LP.customer_id', $customer_id);
		    }	*/

        $q = $this->db->get('bils');
		// print_r($this->db->last_query());die;
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
public function getdaysummary_archival($start,$end,$report_view_access,$report_show)
    {	
        $this->db->select('SUM(COALESCE(total, 0)) as total,SUM(COALESCE(grand_total, 0)) as total_amount1, SUM(COALESCE(total_tax, 0)) as total_tax,SUM(COALESCE(service_charge_amount, 0)) as service_charge_amount, SUM(COALESCE(total_discount+bbq_cover_discount+bbq_daywise_discount, 0)) as total_discount,SUM(total-total_discount+CASE WHEN (tax_type= 1) THEN total_tax ELSE 0 END) as total_amount, COUNT(' . $this->db->dbprefix('bils_archival') . '.id) as totalbill,SUM(total-total_discount-bbq_cover_discount-bbq_daywise_discount+service_charge_amount +CASE WHEN (tax_type= 1) THEN total_tax ELSE 0 END)as net_amt,SUM(total) as gross_amt,SUM(total-CASE WHEN (tax_type= 0) THEN total_tax ELSE 0 END) as netamt', FALSE)
			->where('payment_status', 'Completed')
			
			->where('DATE(date) >=', $start)
			->where('DATE(date) <=', $end);
			if($report_view_access != 1)
		     {
		         $this->db->where('table_whitelisted', $report_show);                      
		     }

			 /*->where('S.status',1);    		 
		    if($customer_id){
		    	$this->db->where('LP.customer_id', $customer_id);
		    }	*/

        $q = $this->db->get('bils_archival');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }


	public function getCollection($start,$end,$report_view_access,$report_show)
    {
    	if($start == ''){
    		$start = date('d-m-Y');
    	}
    	if($end == ''){
    		$end = date('d-m-Y');
    	}
    	
    	$where = '';
    	$where1 = '';
         if($report_view_access != 1)
         {
             $where .= " AND table_whitelisted = ".$report_show." ";
             $where1 .= " AND P.table_whitelisted = ".$report_show." ";
         }

		$default_currency = $this->Settings->default_currency;

		$billQuery = "SELECT  GROUP_CONCAT(id) as id FROM " . $this->db->dbprefix('bils') . " 
 		 WHERE payment_status ='Completed' AND DATE(date) BETWEEN '".$start."' AND '".$end."' ".$where."";
 		 
        $q = $this->db->query($billQuery);
        
        if ($q->num_rows() > 0) {
        	$bill_ids = $q->row()->id;
        	

        if($bill_ids){	
 		$myQuery = "SELECT  SUM(CASE WHEN ((PM.paid_by = 'cash') AND (SC.currency_id = ".$default_currency.")) THEN SC.amount - PM.pos_balance ELSE 0 END) AS Cash,SUM(CASE WHEN ((PM.paid_by = 'cash') AND (SC.currency_id != ".$default_currency.")) THEN amount_exchange*currency_rate ELSE 0 END) as For_Exto_usd,SUM(CASE WHEN ((PM.paid_by = 'cash') AND (SC.currency_id != ".$default_currency.")) THEN SC.amount ELSE 0 END) as For_Ex,SUM(DISTINCT CASE WHEN PM.paid_by = 'CC' THEN PM.amount ELSE 0 END) AS Credit_Card,SUM( DISTINCT P.balance) AS return_balance FROM " . $this->db->dbprefix('sale_currency') . " SC
 		JOIN " . $this->db->dbprefix('bils') . " P ON P.id = SC.bil_id
 		JOIN " . $this->db->dbprefix('payments') . " PM ON PM.bill_id = P.id
 		 WHERE P.payment_status ='Completed'AND SC.bil_id IN (".$bill_ids.") ".$where1."";
		// echo $myQuery;die;
        $p = $this->db->query($myQuery);

         if ($p->num_rows() > 0) {
            return $p->row();
        }
    }
    return FALSE;
}
   return FALSE;     
    }    


	public function getTendertypes($start,$end,$report_view_access,$report_show)
    {
    	if($start == ''){
    		$start = date('d-m-Y');
    	}
    	if($end == ''){
    		$end = date('d-m-Y');
    	}
    	
    	$where = '';
    	$where1 = '';
         if($report_view_access != 1)
         {
             $where .= " AND table_whitelisted = ".$report_show." ";
             $where1 .= " AND P.table_whitelisted = ".$report_show." ";
         }

		$tenderType = "SELECT PA.paid_by tender_type,SUM(PA.amount-PA.pos_balance) tender_type_total
		FROM " . $this->db->dbprefix('bils') . "  P
		LEFT JOIN srampos_payments  PA
		ON P.id = PA.bill_id
		WHERE DATE(P.date) BETWEEN '".$start."' AND '".$end."' AND
		P.payment_status ='Completed'  ".$where."
		GROUP BY PA.paid_by HAVING SUM(PA.amount-PA.pos_balance) > 0";
		$tenderType_res = $this->db->query($tenderType);

		$default_currency = $this->Settings->default_currency;

		$exchange = "SELECT SUM(CASE WHEN ((PM.paid_by = 'cash') AND (SC.currency_id != ".$default_currency.")) THEN amount_exchange*currency_rate ELSE 0 END) as usd,SUM(CASE WHEN ((PM.paid_by = 'cash') AND (SC.currency_id != ".$default_currency.")) THEN SC.amount ELSE 0 END) as For_Ex FROM " . $this->db->dbprefix('sale_currency') . " SC
		JOIN " . $this->db->dbprefix('bils') . " P ON P.id = SC.bil_id
		JOIN " . $this->db->dbprefix('payments') . " PM ON PM.bill_id = P.id
		WHERE P.payment_status ='Completed' ".$where." AND DATE(P.date) BETWEEN '".$start."' AND '".$end."' ";

		$exchange = $this->db->query($exchange);

		$data['Exchange'] = $exchange->result();
		$data['Tender_Type'] = $tenderType_res->result();

	  return $data;
      return FALSE;


    } 
	
	public function getTendertypes_archival($start,$end,$report_view_access,$report_show)
    {
    	if($start == ''){
    		$start = date('d-m-Y');
    	}
    	if($end == ''){
    		$end = date('d-m-Y');
    	}
    	
    	$where = '';
    	$where1 = '';
         if($report_view_access != 1)
         {
             $where .= " AND table_whitelisted = ".$report_show." ";
             $where1 .= " AND P.table_whitelisted = ".$report_show." ";
         }

		$tenderType = "SELECT PA.paid_by tender_type,SUM(PA.amount-PA.pos_balance) tender_type_total
		FROM " . $this->db->dbprefix('bils_archival') . "  P
		LEFT JOIN srampos_payments_archival  PA
		ON P.id = PA.bill_id
		WHERE DATE(P.date) BETWEEN '".$start."' AND '".$end."' AND
		P.payment_status ='Completed'  ".$where."
		GROUP BY PA.paid_by HAVING SUM(PA.amount-PA.pos_balance) > 0";
		$tenderType_res = $this->db->query($tenderType);

		$default_currency = $this->Settings->default_currency;

		$exchange = "SELECT SUM(CASE WHEN ((PM.paid_by = 'cash') AND (SC.currency_id != ".$default_currency.")) THEN amount_exchange*currency_rate ELSE 0 END) as usd,SUM(CASE WHEN ((PM.paid_by = 'cash') AND (SC.currency_id != ".$default_currency.")) THEN SC.amount ELSE 0 END) as For_Ex FROM " . $this->db->dbprefix('sale_currency') . " SC
		JOIN " . $this->db->dbprefix('bils_archival') . " P ON P.id = SC.bil_id
		JOIN " . $this->db->dbprefix('payments_archival') . " PM ON PM.bill_id = P.id
		WHERE P.payment_status ='Completed' ".$where." AND DATE(P.date) BETWEEN '".$start."' AND '".$end."' ";

		$exchange = $this->db->query($exchange);

		$data['Exchange'] = $exchange->result();
		$data['Tender_Type'] = $tenderType_res->result();

	  return $data;
      return FALSE;


    } 
	
	
	
	
	
public function getRoundamount($start,$end)
    {
        $round = "SELECT SUM(P.grand_total - round_total) AS round
        FROM " . $this->db->dbprefix('bils') . " AS P
            WHERE DATE(P.date) BETWEEN '".$start."' AND '".$end."' AND
             P.payment_status ='Completed' ";
            
        $q = $this->db->query($round);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    } 
	public function getRoundamount_archival($start,$end)
    {
        $round = "SELECT SUM(P.grand_total - round_total) AS round
        FROM " . $this->db->dbprefix('bils_archival') . " AS P
            WHERE DATE(P.date) BETWEEN '".$start."' AND '".$end."' AND
             P.payment_status ='Completed' ";
            
        $q = $this->db->query($round);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    } 
public function getItemSaleReports_new($start,$end){

	$this->db->select("recipe_categories.id AS cate_id,recipe_categories.name as category, 'split_order'")
		->join('recipe', 'recipe.category_id = recipe_categories.id')
		->join('bil_items', 'bil_items.recipe_id = recipe.id')
		->join('bils', 'bils.id = bil_items.bil_id')
		->where('recipe_categories.parent_id', NULL)
		->or_where('recipe_categories.parent_id',0);
		$this->db->group_by('recipe_categories.id');
		$t = $this->db->get('recipe_categories');    
        
        if ($t->num_rows() > 0) {
        	/*echo "<pre>";
        	print_r($t->result());*/
            foreach ($t->result() as $row) {
					$this->db->select("recipe_categories.id AS sub_id,recipe_categories.name AS sub_category , 'order'")
					->join('recipe', 'recipe.subcategory_id = recipe_categories.id')
					->join('bil_items', 'bil_items.recipe_id = recipe.id')
					->join('bils', 'bils.id = bil_items.bil_id')
					->where('recipe.category_id', $row->cate_id);
					$this->db->group_by('recipe.subcategory_id');
					$s = $this->db->get('recipe_categories');
                if ($s->num_rows() > 0) {
                        
                        foreach ($s->result() as $sow) {
                                
									$this->db->select('recipe.name,SUM(COALESCE(subtotal, 0)) as total,SUM(' . $this->db->dbprefix('bil_items') . '.quantity) as quantity')
									->join('recipe', 'recipe.id = bil_items.recipe_id')
									->join('bils', 'bils.id = bil_items.bil_id')
									->where('DATE(date) >=', $start)
									->where('DATE(date) <=', $end)
									->where('recipe.subcategory_id', $sow->sub_id);
									$this->db->group_by('recipe.id');
									$o = $this->db->get('bil_items');                               
                                $split[$row->cate_id][] = $sow;
                                if ($o->num_rows() > 0) {
                                    
                                    foreach($o->result() as $oow){
                                        $order[$sow->sub_id][] = $oow;
                                    }
                                }
                                $sow->order = $order[$sow->sub_id];                   
                        }
                    
                    
                    $row->split_order = $split[$row->cate_id];
                }else{
                    $row->split_order = array();
                }
                
                $data[] = $row;

            }
            
            return $data;
        }
        
        return FALSE;
	}	
	
	
  public function getCashierReport($start,$end,$report_view_access,$report_show)
    {

    	$where ='';
    	  if($report_view_access != 1 ){
            $where .= "AND P.table_whitelisted = ".$report_show."";
        }

        $myQuery = "SELECT U.id, U.username,SUM(P.total-P.total_discount-P.bbq_cover_discount-P.bbq_daywise_discount+P.service_charge_amount+CASE WHEN (P.tax_type= 1) THEN total_tax ELSE 0 END) AS grand_total
        FROM srampos_bils  P
            LEFT JOIN srampos_users  U
            ON P.created_by = U.id
            WHERE DATE(P.date) BETWEEN '".$start."' AND '".$end."' AND
             P.payment_status ='Completed' ".$where."
            GROUP BY U.username";
            
        $q = $this->db->query($myQuery);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }





  public function getCashierReport_archival($start,$end,$report_view_access,$report_show){
    	$where ='';
    	  if($report_view_access != 1 ){
            $where .= "AND P.table_whitelisted = ".$report_show."";
        }
        $myQuery = "SELECT U.id, U.username,SUM(P.total-P.total_discount-P.bbq_cover_discount-P.bbq_daywise_discount+P.service_charge_amount+CASE WHEN (P.tax_type= 1) THEN total_tax ELSE 0 END) AS grand_total
        FROM srampos_bils_archival  P
            LEFT JOIN srampos_users  U
            ON P.created_by = U.id
            WHERE DATE(P.date) BETWEEN '".$start."' AND '".$end."' AND
             P.payment_status ='Completed' ".$where."
            GROUP BY U.username";
        $q = $this->db->query($myQuery);
		/* echo $this->db->last_query();
		die; */
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

public function getOrderitemDetsils($id){

	$myQuery = "SELECT OI.recipe_name,O.reference_no,O.created_by,T.name,OI.id,T.id AS table_id
        FROM " . $this->db->dbprefix('order_items') . " AS OI
           JOIN " . $this->db->dbprefix('orders') . "  AS O
            ON O.id = OI.sale_id
            JOIN " . $this->db->dbprefix('restaurant_tables') . " AS T
            ON T.id = O.table_id
            WHERE OI.order_item_cancel_status = 0 AND OI.id=".$id."";
            /*echo $myQuery;die;*/
        $q = $this->db->query($myQuery);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;

	}
	
	public function getSplitBils($split_id){
		$this->db->select('sales.*, bils.id as bil_id,bils.unique_discount');
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
	
	public function getDINEINBils($billid){
		$b = $this->db->select('*')->where('id', $billid)->get('bils');
		 if ($b->num_rows() > 0) {
            return $b->row();
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
	
	
	function getDineinCustomerDiscount($billid){
				
		$this->db
		->select('P.id bil_id, P.tax_type, P.tax_id, P.total, P.total_discount, P.grand_total, S.sales_split_id, P.customer_discount_id, D.*')
		->from('bils P')
		->join('sales S', 'S.id = P.sales_id', 'left')
		// ->join('customer_request_discount CRD', 'CRD.split_id = S.sales_split_id', 'left')
		->join('diccounts_for_customer D','D.id=P.customer_discount_id','left')
		->join('group_discount GD', 'GD.cus_discount_id = D.id', 'left')
		->where('P.id',$billid)->group_by('D.id')
		->where('P.unique_discount',0)->group_by('D.id');
		
		$q = $this->db->get();
		
		if ($q->num_rows() > 0) {
			$result = $q->row();   
			 
            return $result;
		}
		return FALSE;
	}

    function getAllCustomerDiscount()
    {
	
	$q = $this->db->get_where('diccounts_for_customer',array('status'=>1));
        if ($q->num_rows() > 0) {
	    $result = $q->result();    
            return $result;
        }
        return FALSE;
    }

	
	function getBBQDiscount($billid){
				
		$this->db
		->select('P.id bil_id, P.tax_type, P.tax_id, P.total, P.total_discount, P.grand_total, S.sales_split_id,P.customer_discount_id, D.*')
		->from('bils P')
		->join('sales S', 'S.id = P.sales_id', 'left')
		// ->join('customer_request_discount CRD', 'CRD.split_id = S.sales_split_id', 'left')
		->join('diccounts_for_bbq D','D.id=P.customer_discount_id','left')
		
		->where('P.id',$billid)->group_by('D.id');
		
		$q = $this->db->get();
		
		if ($q->num_rows() > 0) {
			$result = $q->row();   
			 
            return $result;
		}
		return FALSE;
	}
	
    function getCustomerDiscount($billid)
    {
		$this->db
		->select('P.id bil_id,P.tax_type,P.tax_id,P.total,P.customer_discount_id,P.customer_discount_status,P.total_discount,P.total_tax,P.service_charge_amount,P.grand_total,D.*')
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
	
	
	
    function updateCustomerDiscount($billid,$dis_id){
	if($dis_id!=0) $update_bil['customer_discount_id'] = $dis_id;
	if($dis_id!=0) $update_bil['customer_discount_status'] = 'pending';
	if($dis_id==0) $update_bil['customer_discount_status'] = 'no_discount';
	$this->db->where('id', $billid);
        $this->db->update('bils', $update_bil);
    }
	

	function getAllBBQDiscount()
    {
	
	$q = $this->db->get('diccounts_for_bbq');
        if ($q->num_rows() > 0) {
	    $result = $q->result();    
            return $result;
        }
        return FALSE;
    }
	
    public function getBillItemsRecipeID($billid){
    	$myQuery = "SELECT BI.id,BI.recipe_id,sum(BI.subtotal-BI.item_discount-BI.off_discount) AS amount,BI.tax_type,R.category_id
        FROM " . $this->db->dbprefix('bil_items') . " BI
        JOIN " . $this->db->dbprefix('recipe') . " R ON R.id = BI.recipe_id
            WHERE BI.bil_id =".$billid." GROUP BY BI.id";
            /*echo $myQuery;die;*/
        $q = $this->db->query($myQuery);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;

    }
   public function update_bill_withcustomer_discount($billid,$dis_id,$dis_val){		
	$update_bil['customer_discount_status'] = 'applied';
	$update_bil['customer_discount_id'] = $dis_id;
	$update_bil['discount_val'] = $dis_val;	
	    $myQuery = "SELECT BI.id,BI.recipe_id,sum(BI.subtotal-BI.item_discount-BI.off_discount-BI.manual_item_discount) AS amount,BI.tax_type,R.category_id,R.subcategory_id,B.tax_id
        FROM " . $this->db->dbprefix('bil_items') . " BI
        JOIN " . $this->db->dbprefix('recipe') . " R ON R.id = BI.recipe_id
        JOIN " . $this->db->dbprefix('bils') . " B ON B.id = BI.bil_id
            WHERE BI.bil_id =".$billid." GROUP BY BI.id";            
        $q = $this->db->query($myQuery);
	    if ($q->num_rows() > 0) {		
		foreach (($q->result_array()) as $row) {			
			$inputDiscount = $this->pos_model->recipe_customer_discount_calculation($row['recipe_id'],$row['category_id'],$row['subcategory_id'],$row['amount'],$dis_id);	
		    $afterDis_total = $row['amount'] - $inputDiscount;
		    $tax = ($row['tax_type']==0)?$this->inclusive_tax_and_amt($afterDis_total,$row['tax_id']):$this->exclusive_tax_and_amt($afterDis_total,$row['tax_id']);
		    $servicecharge = 0;
		    if($this->pos_settings->default_service_charge !=0 && $this->pos_settings->service_charge_option !=0){
		    $servicecharge = $this->site->calculateServiceCharge($this->pos_settings->default_service_charge,$afterDis_total);
			}
		    $updateItem['tax'] = $tax['tax'];
		    $updateItem['service_charge_amount'] = $servicecharge;
		    $updateItem['input_discount'] = $inputDiscount;
		    $cus_dis_val = str_replace('Discount ','',$dis_val);
		    $updateItem['customer_discount_val'] = trim($cus_dis_val,'%').'%';
		    $row['id'];		    
		    $this->db->where('id', $row['id']);
		    $this->db->update('bil_items', $updateItem);	
		}
// print_r($updateItem);die;
		$BillQuery = "SELECT B.tax_id,BI.bil_id,SUM(BI.item_discount+BI.off_discount+BI.input_discount+BI.manual_item_discount) AS total_discount,SUM(BI.subtotal) AS subtotal,BI.tax_type
        FROM " . $this->db->dbprefix('bil_items') . " BI
	JOIN " . $this->db->dbprefix('bils') . " B ON B.id = BI.bil_id
            WHERE BI.bil_id =".$billid." GROUP BY BI.bil_id";
        $b = $this->db->query($BillQuery);

		if ($b->num_rows() > 0) {
			foreach (($b->result()) as $row) {
				$totalAmt_afterDiscount = $row->subtotal - $row->total_discount;
			$getTax = $this->site->getTaxRateByID($row->tax_id);

			$servicecharge = 0;
			    if($this->pos_settings->default_service_charge !=0 && $this->pos_settings->service_charge_option !=0){
			    $servicecharge = $this->site->calculateServiceCharge($this->pos_settings->default_service_charge,$totalAmt_afterDiscount);
				}

			if($row->tax_type==0){
			    $grandTotal = $totalAmt_afterDiscount/(($getTax->rate/100)+1);
				    $totalTax = $totalAmt_afterDiscount-($totalAmt_afterDiscount/(($getTax->rate/100)+1));
				    $amountPayable = $grandTotal+$totalTax;
				    
				}else{
				    $totalTax = $totalAmt_afterDiscount*($getTax->rate/100);
				    $grandTotal = $totalAmt_afterDiscount+$totalTax;
				    $amountPayable = $grandTotal;
				}
				

				$update_bil['grand_total'] = $this->sma->formatDecimal($grandTotal+$servicecharge);
				$update_bil['service_charge_amount'] = $this->sma->formatDecimal($servicecharge);
				$update_bil['total_tax'] = $this->sma->formatDecimal($totalTax);
				$update_bil['total_discount'] = $row->total_discount;
				$update_bil['round_total'] =  $this->sma->formatDecimal($grandTotal+$servicecharge);
				$this->db->where('id', $row->bil_id);
				
		        $this->db->update('bils', $update_bil);		        

		       return $this->sma->formatDecimal($amountPayable);;
		    }		
		}	
	    }	
        return false;
    }

   public function update_bill_withcustomer_discount_28_09_2018($billid,$dis_id){	
	/*echo "sivan"; die;*/
	$update_bil['customer_discount_status'] = 'applied';
	// $this->db->where('id', $billid);
        // if ($this->db->update('bils', $update_bil)) {
	    $myQuery = "SELECT BI.id,BI.recipe_id,sum(BI.subtotal-BI.item_discount-BI.off_discount) AS amount,BI.tax_type,R.category_id
        FROM " . $this->db->dbprefix('bil_items') . " BI
        JOIN " . $this->db->dbprefix('recipe') . " R ON R.id = BI.recipe_id
            WHERE BI.bil_id =".$billid." GROUP BY BI.id";
            
        $q = $this->db->query($myQuery);
        /*
if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data = $row;
            }
            return $data;
        }*/

	    if ($q->num_rows() > 0) {
		/*$result = $q->result_array();*/
		 //print_r($q->result_array());
		foreach (($q->result_array()) as $row) {
		
			$inputDiscount = $this->get_customer_discount_bygroup($row['category_id'],$row['amount'],$dis_id);
		    /*$inputDiscount = $this->site->calculate_Discount($discount, (($row['amount']),(($row['subtotal']-$row['item_discount'])-$row['off_discount']));*/
		    $afterDis_total = $row['amount'] - $inputDiscount;
		    $tax = ($row['tax_type']==0)?$this->inclusive_tax_and_amt($afterDis_total,$row['tax_id']):$this->exclusive_tax_and_amt($afterDis_total,$row['tax_id']);
		    $updateItem['tax'] = $tax['tax'];
		    $updateItem['input_discount'] = $inputDiscount;
		    	    
		    $row['id'];		    
		    $this->db->where('id', $row['id']);
		    $this->db->update('bil_items', $updateItem);		    
		    //echo $this->db->last_query();exit;
		}

		$BillQuery = "SELECT B.tax_id,BI.bil_id,SUM(BI.item_discount+BI.off_discount+BI.input_discount) AS total_discount,SUM(BI.subtotal) AS subtotal,BI.tax_type
        FROM " . $this->db->dbprefix('bil_items') . " BI
	JOIN " . $this->db->dbprefix('bils') . " B ON B.id = BI.bil_id
            WHERE BI.bil_id =".$billid." GROUP BY BI.bil_id";
        $b = $this->db->query($BillQuery);

		if ($b->num_rows() > 0) {
			foreach (($b->result()) as $row) {
				$totalAmt_afterDiscount = $row->subtotal - $row->total_discount;
			$getTax = $this->site->getTaxRateByID($row->tax_id);
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
				$update_bil['customer_discount_id'] =  $dis_id;
				$this->db->where('id', $row->bil_id);
				
		        $this->db->update('bils', $update_bil);

		       return $this->sma->formatDecimal($amountPayable);;
		    }		

		}
	//die;
	    }
	// }
        return false;
    }

 public  function get_customer_discount_bygroup($groupid,$finalAmt,$discountid){
    	
	$discount  = $this->getCategory_Group_Customer_Discount($groupid,$discountid);
	if($discount){
	    return $discountAmt = $finalAmt*($discount/100);
	    
	}
	return 0;
    }
  public  function getCategory_Group_Customer_Discount($groupid,$discountid){
	$q = $this->db
	    ->select('GD.discount_val')
	    ->from('diccounts_for_customer D')
	    ->join('group_discount GD','GD.cus_discount_id=D.id and GD.recipe_group_id='.$groupid)
	    ->where('D.id',$discountid)
	    ->get();
	$res = $q->row();
	return ($q->num_rows()>0)?$res->discount_val:false;
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
	
	function BBQupdate_bil($bils_update, $billid, $item_updates, $bilitem_ids, $request_array, $customer_request_id){
		
		if($this->db->update('bils', $bils_update, array('id' => $billid))){
			// print_r($this->db->last_query());die;
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
	
    function update_bil($billid,$update_bil,$discount){
	
	$dpos = strpos($discount, '%');
	if ($dpos !== false) {
	    $pds = explode("%", $discount);
	    $disType = '%';
	    $disVal = $pds[0];
	}else{
	    $disVal = $discount;
	    $disType = 'F';//fixed
	}
	
	
	$update_bil['customer_discount_status'] = 'applied';
	$this->db->where('id', $billid);
        if ($this->db->update('bils', $update_bil)) {
	    $this->db
		->select('BI.id,B.tax_id,B.total bill_total,BI.*')
		->from('bil_items BI')
		->join('bils B','B.id=BI.bil_id')
		->where('B.id',$billid);
	    $q = $this->db->get();
	   
	    if ($q->num_rows() > 0) {
		$result = $q->result_array();
		 //print_r($q->result_array());
		foreach($result as $k => $row){
		    $inputDiscount = $this->site->calculate_Discount($discount, (($row['subtotal']-$row['item_discount'])-$row['off_discount']),(($row['subtotal']-$row['item_discount'])-$row['off_discount']));
		    $afterDis_total = $row['subtotal'] - $inputDiscount;
		    $tax = ($row['tax_type']==0)?$this->inclusive_tax_and_amt($afterDis_total,$row['tax_id']):$this->exclusive_tax_and_amt($afterDis_total,$row['tax_id']);
		    $updateItem['tax'] = $tax['tax'];
		    $updateItem['input_discount'] = $inputDiscount;
		    $row['id'];
		    
		    $this->db->where('id', $row['id']);
		    $this->db->update('bil_items', $updateItem);
		    
		    //echo $this->db->last_query();exit;
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
    public function exclusive_tax_and_amt($total,$taxID){
	$getTax = $this->site->getTaxRateByID($taxID);
	$return['tax'] = $total*($getTax->rate/100);
	$return['g_total'] = $total+$return['tax'];
	return $return;
    }

public function getTableNumber($bill_id){
        $table_name = "SELECT T.name AS table_name,TY.name AS order_type,T.max_seats as seats,ra.name as floor
                    FROM ".$this->db->dbprefix('bils')." AS P
                    JOIN ". $this->db->dbprefix('sales') ." AS S ON S.id = P.sales_id
                    JOIN ". $this->db->dbprefix('orders') ." AS O ON O.split_id = S.sales_split_id
                    JOIN ". $this->db->dbprefix('sales_type') ." AS TY ON TY.id = O.order_type
                    LEFT JOIN ". $this->db->dbprefix('restaurant_tables') ." AS T ON T.id = O.table_id
					 LEFT JOIN ". $this->db->dbprefix('restaurant_areas') ." AS ra ON ra.id = T.area_id
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
	
	public function getTableNumber_archival($bill_id){
        $table_name = "SELECT T.name AS table_name,TY.name AS order_type,T.max_seats as seats,ra.name as floor
         FROM ".$this->db->dbprefix('bils_archival')." AS P
         JOIN ". $this->db->dbprefix('sales_archival') ." AS S ON S.id = P.sales_id
         JOIN ". $this->db->dbprefix('orders_archival') ." AS O ON O.split_id = S.sales_split_id
         JOIN ". $this->db->dbprefix('sales_type') ." AS TY ON TY.id = O.order_type
         LEFT JOIN ". $this->db->dbprefix('restaurant_tables') ." AS T ON T.id = O.table_id
		 LEFT JOIN ". $this->db->dbprefix('restaurant_areas') ." AS ra ON ra.id = T.area_id
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

    function recipe_customer_discount_calculation_bk($itemid,$groupid,$finalAmt,$discountid){
	//echo $itemid.'-'.$groupid.'-'.$finalAmt.'-'.$discountid;
	if($this->Settings->customer_discount=="customer"){
	    $discount  = $this->getCategory_cusDiscount($groupid,$discountid);
	    if($discount){
		return $discountAmt = $finalAmt*($discount/100);
		
	    }
	}else if($this->Settings->customer_discount=="manual"){//manual
	    $discount_value = $discountid;
	    return $discountAmt = $this->site->calculateDiscount($discount_value, $finalAmt);
	}
	return 0;
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

public function change_table($change_split_id, $changed_table_id){

        $order_array = array(
            'table_id' => $changed_table_id,
        );
        $old_table_id = $this->db->get_where('orders',array('split_id'=>$change_split_id))->row('table_id');
        $this->site->updateTableStatus($old_table_id,0,$this->session->userdata('user_id'));
        $this->site->updateTableStatus($changed_table_id,1,$this->session->userdata('user_id'));
        $this->db->where('split_id', $change_split_id);
        if ($this->db->update('orders',  $order_array)) {
			$orders_id = "SELECT O.id 
			FROM ".$this->db->dbprefix('orders')." AS O
			WHERE O.split_id='".$change_split_id."' ";

			$q = $this->db->query($orders_id);			
	        if ($q->num_rows() > 0) {
	            foreach (($q->result()) as $row) {
	            	$this->db->update('restaurant_table_orders', array('table_id' => $changed_table_id), array('order_id' => $row->id));
	            	$this->db->update('restaurant_table_sessions', array('table_id' => $changed_table_id), array('order_id' => $row->id));	                
	            }
	            return TRUE;
	        }		
			return true;
		}
		return false;
    }
    public function merger_multiple_to_single_split($merge_splits, $current_split,$merge_table_id ){


        $order_array = array(
            'table_id' => $merge_table_id,
            'split_id' => $current_split,
        );


		$stack = $merge_splits;
		array_push($stack, $current_split);

		$this->db->select("SUM(srampos_order_items.quantity) as quantity,order_items.recipe_id,order_items.unit_price", FALSE);
		$this->db->join('orders', 'orders.id = order_items.sale_id');
		$this->db->where_in('split_id', $stack);		
		$this->db->where('order_items.order_item_cancel_status', 0);		
		$this->db->group_by('CASE WHEN srampos_order_items.manual_item_discount >= 0.00 THEN ' . $this->db->dbprefix('order_items') . '.recipe_id END HAVING COUNT(*) > 1',FALSE);
		$ms = $this->db->get('order_items'); 
 

	  if ($ms->num_rows() > 0) {  	
            foreach (($ms->result()) as $row) {

					$this->db->select("order_items.id", FALSE);
					$this->db->join('orders', 'orders.id = order_items.sale_id');
					$this->db->where('orders.split_id', $current_split);	
					$this->db->where('order_items.recipe_id', $row->recipe_id);				
					$cs = $this->db->get('order_items');
            		//$this->db->update('order_items', array('quantity' => $row->quantity,'subtotal' => $row->unit_price*($row->quantity)), array('id' => $cs->row('id')));

            		$this->db->select("order_items.id", FALSE);
					$this->db->join('orders', 'orders.id = order_items.sale_id');
					$this->db->where_in('split_id', $merge_splits);		
					$this->db->where('recipe_id', $row->recipe_id);		
					//$this->db->group_by('order_items.id,CASE WHEN srampos_order_items.manual_item_discount != 0.00 THEN ' . $this->db->dbprefix('order_items') . '.recipe_id END',FALSE);		
					$cancelssplit = $this->db->get('order_items');
//print_r($this->db->last_query());die;
					//$this->db->update('order_items', array('order_item_cancel_id' => $this->session->userdata('user_id'),'order_item_cancel_note' => 'Cancelled','item_status' => 'Cancel','order_item_cancel_status' => 1), array('id' => $cancelssplit->row('id')));
            }
        }        
        
/*table status changed if only one orderd placed*/
     
 $this->db->select("table_id", FALSE);		
		$this->db->where_in('split_id', $merge_splits);	
		$this->db->group_by('table_id HAVING COUNT(*) >= 1',FALSE);		
		$table = $this->db->get('orders'); 	
		
	        if ($table->num_rows() > 0) {
	            foreach (($table->result()) as $row) {
	            	$this->db->update('restaurant_tables', array('current_order_status' => 0,'current_order_user' => 0), array('id' => $row->table_id));
	            
	            }
	        }		
/*table status changed if only one orderd placed*/

        $this->db->where_in('split_id', $merge_splits);
        if ($this->db->update('orders',  $order_array)) {
			$orders_id = "SELECT O.id 
			FROM ".$this->db->dbprefix('orders')." AS O
			WHERE O.split_id='".$current_split."' ";

			$q = $this->db->query($orders_id);			
	        if ($q->num_rows() > 0) {
	            foreach (($q->result()) as $row) {
	            	$this->db->update('restaurant_table_orders', array('table_id' => $merge_table_id), array('order_id' => $row->id));
	            	$this->db->update('restaurant_table_sessions', array('table_id' => $merge_table_id), array('order_id' => $row->id));	                
	            }
	            return TRUE;
	        }		
			return true;
		}
		return false;
    }

public function merger_multiple_to_single_split_old($merge_splits, $current_split,$merge_table_id ){


        $order_array = array(
            'table_id' => $merge_table_id,
            'split_id' => $current_split,
        );


		$stack = $merge_splits;
		array_push($stack, $current_split);

		$this->db->select("SUM(srampos_order_items.quantity) quantity,order_items.recipe_id,order_items.unit_price", FALSE);
		$this->db->join('orders', 'orders.id = order_items.sale_id');
		$this->db->where_in('split_id', $stack);		
		$this->db->where('order_items.order_item_cancel_status', 0);		
		$this->db->group_by('CASE WHEN srampos_order_items.manual_item_discount >= 0.00 THEN ' . $this->db->dbprefix('order_items') . '.recipe_id END HAVING COUNT(*) > 1',FALSE);
		$ms = $this->db->get('order_items'); 
// print_r($this->db->last_query());die;

	  if ($ms->num_rows() > 0) {  	
            foreach (($ms->result()) as $row) {

					$this->db->select("order_items.id", FALSE);
					$this->db->join('orders', 'orders.id = order_items.sale_id');
					$this->db->where('orders.split_id', $current_split);	
					$this->db->where('order_items.recipe_id', $row->recipe_id);				
					$cs = $this->db->get('order_items');
            		$this->db->update('order_items', array('quantity' => $row->quantity,'subtotal' => $row->unit_price*($row->quantity)), array('id' => $cs->row('id')));

            		$this->db->select("order_items.id", FALSE);
					$this->db->join('orders', 'orders.id = order_items.sale_id');
					$this->db->where_in('split_id', $merge_splits);		
					$this->db->where('recipe_id', $row->recipe_id);		
					$this->db->group_by('CASE WHEN srampos_order_items.manual_item_discount >= 0.00 THEN ' . $this->db->dbprefix('order_items') . '.recipe_id END',FALSE);		
					$cancelssplit = $this->db->get('order_items');

					$this->db->update('order_items', array('order_item_cancel_id' => $this->session->userdata('user_id'),'order_item_cancel_note' => 'Cancelled','item_status' => 'Cancel','order_item_cancel_status' => 1), array('id' => $cancelssplit->row('id')));
            }
        }        
        
/*table status changed if only one orderd placed*/
        $this->db->select("table_id", FALSE);		
		$this->db->where_in('split_id', $merge_splits);	
		$this->db->group_by('table_id HAVING COUNT(table_id) <= 1',FALSE);		
		$table = $this->db->get('orders'); 	
	        if ($table->num_rows() > 0) {
	            foreach (($table->result()) as $row) {
	            	$this->db->update('restaurant_tables', array('current_order_status' => 0,'current_order_user' => 0), array('id' => $row->table_id));
	            }
	        }	
/*table status changed if only one orderd placed*/

        $this->db->where_in('split_id', $merge_splits);
        if ($this->db->update('orders',  $order_array)) {
			$orders_id = "SELECT O.id 
			FROM ".$this->db->dbprefix('orders')." AS O
			WHERE O.split_id='".$current_split."' ";

			$q = $this->db->query($orders_id);			
	        if ($q->num_rows() > 0) {
	            foreach (($q->result()) as $row) {
	            	$this->db->update('restaurant_table_orders', array('table_id' => $merge_table_id), array('order_id' => $row->id));
	            	$this->db->update('restaurant_table_sessions', array('table_id' => $merge_table_id), array('order_id' => $row->id));	                
	            }
	            return TRUE;
	        }		
			return true;
		}
		return false;
    }

   public function merger_multiple_to_single_split_20_03_2019($merge_splits, $current_split,$merge_table_id ){

        $order_array = array(
            'table_id' => $merge_table_id,
            'split_id' => $current_split,
        );
        
        $this->db->where_in('split_id', $merge_splits);
        if ($this->db->update('orders',  $order_array)) {
			$orders_id = "SELECT O.id 
			FROM ".$this->db->dbprefix('orders')." AS O
			WHERE O.split_id='".$current_split."' ";

			$q = $this->db->query($orders_id);			
	        if ($q->num_rows() > 0) {
	            foreach (($q->result()) as $row) {
	            	$this->db->update('restaurant_table_orders', array('table_id' => $merge_table_id), array('order_id' => $row->id));
	            	$this->db->update('restaurant_table_sessions', array('table_id' => $merge_table_id), array('order_id' => $row->id));	                
	            }
	            return TRUE;
	        }		
			return true;
		}
		return false;
    }
function change_customer($change_split_id, $changed_customer_id){
	$customer_details=$this->db->get_where("companies",array("id"=>$changed_customer_id))->row();
    $order_array = array(
            'customer_id' => $changed_customer_id,
			'customer'=>$customer_details->name
        );
    $this->db->where('split_id',$change_split_id);
    $this->db->update('orders',  $order_array);
    return TRUE;
}
    function isTableWhitelisted($tableid){
	$q = $this->db->get_where("restaurant_tables",array('id'=>$tableid,'whitelisted'=>1));
	if ($q->num_rows() > 0) {
	    return 1;
	}
	return 0;
    }
    public function getSettlementReport($start,$end, $report_view_access,$report_show)
    {

    	$where ='';

    	  if($report_view_access != 1 ){
            $where .= "AND P.table_whitelisted = ".$report_show."";
        }

        $saletype = "SELECT st.name sale_type,SUM(P.round_total) sale_type_total1,SUM(P.total-P.total_discount-P.bbq_cover_discount-P.bbq_daywise_discount+P.service_charge_amount+CASE WHEN (P.tax_type= 1) THEN P.total_tax ELSE 0 END) AS sale_type_total
        FROM " . $this->db->dbprefix('bils') . "  P
            LEFT JOIN srampos_sales  s
            ON s.id = P.sales_id
	    LEFT JOIN srampos_sales_type  st
            ON st.id = s.sales_type_id
            WHERE DATE(P.date) BETWEEN '".$start."' AND '".$end."' AND
            P.payment_status ='Completed' ".$where."
            GROUP BY s.sales_type_id";
            
        $saletype_res = $this->db->query($saletype);
	
	$tenderType = "SELECT PA.paid_by tender_type,SUM(PA.amount-PA.pos_balance) tender_type_total
        FROM " . $this->db->dbprefix('bils') . "  P
            LEFT JOIN srampos_payments  PA
            ON P.id = PA.bill_id
            WHERE DATE(P.date) BETWEEN '".$start."' AND '".$end."' AND
            P.payment_status ='Completed'  ".$where."
            GROUP BY PA.paid_by HAVING SUM(PA.amount-PA.pos_balance) > 0";

        $tenderType_res = $this->db->query($tenderType);
		$default_currency = $this->Settings->default_currency;

        $exchange = "SELECT SUM(CASE WHEN ((PM.paid_by = 'cash') AND (SC.currency_id != ".$default_currency.")) THEN amount_exchange*currency_rate ELSE 0 END) as usd,SUM(CASE WHEN ((PM.paid_by = 'cash') AND (SC.currency_id != ".$default_currency.")) THEN SC.amount ELSE 0 END) as For_Ex FROM " . $this->db->dbprefix('sale_currency') . " SC
 		JOIN " . $this->db->dbprefix('bils') . " P ON P.id = SC.bil_id
 		JOIN " . $this->db->dbprefix('payments') . " PM ON PM.bill_id = P.id
 		 WHERE P.payment_status ='Completed' ".$where." AND DATE(P.date) BETWEEN '".$start."' AND '".$end."' ";

 	$exchange = $this->db->query($exchange);
	// print_r($this->db->error());die;
	$query = "SELECT count(P.id) total_transaction,SUM(P.round_total) gross_total1,SUM(P.total-P.total_discount-P.bbq_cover_discount-P.bbq_daywise_discount+P.service_charge_amount+CASE WHEN (P.tax_type= 1) THEN P.total_tax ELSE 0 END) AS gross_total,SUM(P.round_total-P.total_discount) net_total1,SUM(P.total-P.total_discount-P.bbq_cover_discount-P.bbq_daywise_discount+CASE WHEN (P.tax_type= 1) THEN P.total_tax ELSE 0 END)+P.service_charge_amount  AS net_total
        FROM " . $this->db->dbprefix('bils') . "  P
            WHERE DATE(P.date) BETWEEN '".$start."' AND '".$end."' AND
            P.payment_status ='Completed' ".$where." ";

	$details = $this->db->query($query);



    $open_cash = "SELECT RP.id,SUM(RP.cash_in_hand) AS opensale
        FROM " . $this->db->dbprefix('open_register_payments') . " AS RP
         JOIN  " . $this->db->dbprefix('pos_open_register') . " AS PR ON PR.id = RP.register_id
         JOIN  " . $this->db->dbprefix('users') . " AS U ON U.id = PR.user_id
         JOIN  " . $this->db->dbprefix('users') . " AS UU ON UU.id = PR.created_by
            WHERE DATE(RP.date) BETWEEN '".$start."' AND '".$end."'  ";  

    $open_cash = $this->db->query($open_cash);

	

   $buffet_sale = "SELECT count(P.id) as total_transaction,st.name sale_type,SUM(P.total-P.total_discount+CASE WHEN (P.tax_type= 1) THEN P.total_tax ELSE 0 END)  AS sale_type_total
    FROM " . $this->db->dbprefix('bbq_bils') . " AS P
     JOIN  " . $this->db->dbprefix('bbq_sales') . " AS s ON s.id = P.sales_id
     JOIN  " . $this->db->dbprefix('sales_type') . " AS st ON st.id = s.sales_type_id        
        WHERE DATE(P.date) BETWEEN '".$start."' AND '".$end."' AND P.payment_status ='Completed' ".$where." GROUP BY s.sales_type_id ";
// echo $buffet_sale;die;
    $buffet_sale = $this->db->query($buffet_sale);   


    $buffet_saletype = "SELECT st.name sale_type,SUM(P.total-P.total_discount+CASE WHEN (P.tax_type= 1) THEN P.total_tax ELSE 0 END) AS sale_type_total
        FROM " . $this->db->dbprefix('bbq_bils') . "  P
            LEFT JOIN " . $this->db->dbprefix('bbq_sales') . " AS  s
            ON s.id = P.sales_id
	    LEFT JOIN " . $this->db->dbprefix('sales_type') . " AS  st
            ON st.id = s.sales_type_id
            WHERE DATE(P.date) BETWEEN '".$start."' AND '".$end."' AND
            P.payment_status ='Completed' ".$where." 
            GROUP BY s.sales_type_id";
            
     $buffet_saletype = $this->db->query($buffet_saletype);

     $buffet_tender_type = "SELECT PA.paid_by tender_type,SUM(PA.amount-PA.pos_balance) tender_type_total
        FROM " . $this->db->dbprefix('bbq_bils') . "  P
            LEFT JOIN " . $this->db->dbprefix('bbq_payments') . "  PA
            ON P.id = PA.bill_id
            WHERE DATE(P.date) BETWEEN '".$start."' AND '".$end."' AND
            P.payment_status ='Completed' 
            GROUP BY PA.paid_by";  

      $buffet_tender_type = $this->db->query($buffet_tender_type);

      $buffet_exchange = "SELECT SUM(CASE WHEN ((PM.paid_by = 'cash') AND (SC.currency_id != ".$default_currency.")) THEN amount_exchange*currency_rate ELSE 0 END) as usd,SUM(CASE WHEN ((PM.paid_by = 'cash') AND (SC.currency_id != ".$default_currency.")) THEN SC.amount ELSE 0 END) as For_Ex FROM " . $this->db->dbprefix('sale_currency') . " SC
 		JOIN " . $this->db->dbprefix('bbq_bils') . " P ON P.id = SC.bil_id
 		JOIN " . $this->db->dbprefix('bbq_payments') . " PM ON PM.bill_id = P.id
 		 WHERE P.payment_status ='Completed'AND DATE(P.date) BETWEEN '".$start."' AND '".$end."' ";

     	$buffet_exchange = $this->db->query($buffet_exchange);
	  
	  $data['payments'] = $details->result(); 

	 $dine_tender = $tenderType_res->result(); 
	 $buffet_tender = $buffet_tender_type->result();


	 $tender_meged = array_merge($dine_tender, $buffet_tender);

	$tender_totals = array();
	foreach ($tender_meged as $row) {
	  $tender_type = $row->tender_type;
	  $tender_type_total = $row->tender_type_total;
	  $tender_totals[$tender_type] += $tender_type_total;
	}

	$tendertype = array();
	foreach ($tender_totals as $tender_type => $tender_type_total) {
	  $tendertype[] = array('tender_type' => $tender_type, 'tender_type_total' => $tender_type_total);
	}  


	$data['tender_type'] = $tendertype;
	/*$data['tender_type'] = $tenderType_res->result();*/
	$data['sale_type'] = $saletype_res->result(); 
	$data['exchange_amt'] = $exchange->result();
	$data['open_sale'] = $open_cash->result();

	$data['buffet_sale'] = $buffet_sale->result();
	$data['buffet_saletype'] = $buffet_saletype->result();
	$data['buffet_exchange'] = $buffet_exchange->result();

	  return $data;
      return FALSE;
    }   
	
	    public function getSettlementReport_archival($start,$end, $report_view_access,$report_show)
    {

    	$where ='';

    	  if($report_view_access != 1 ){
            $where .= "AND P.table_whitelisted = ".$report_show."";
        }

        $saletype = "SELECT st.name sale_type,SUM(P.round_total) sale_type_total1,SUM(P.total-P.total_discount-P.bbq_cover_discount-P.bbq_daywise_discount+P.service_charge_amount+CASE WHEN (P.tax_type= 1) THEN P.total_tax ELSE 0 END) AS sale_type_total
        FROM " . $this->db->dbprefix('bils_archival') . "  P
            LEFT JOIN srampos_sales_archival  s
            ON s.id = P.sales_id
	    LEFT JOIN srampos_sales_type  st
            ON st.id = s.sales_type_id
            WHERE DATE(P.date) BETWEEN '".$start."' AND '".$end."' AND
            P.payment_status ='Completed' ".$where."
            GROUP BY s.sales_type_id";
            
        $saletype_res = $this->db->query($saletype);
	
	$tenderType = "SELECT PA.paid_by tender_type,SUM(PA.amount-PA.pos_balance) tender_type_total
        FROM " . $this->db->dbprefix('bils_archival') . "  P
            LEFT JOIN srampos_payments_archival  PA
            ON P.id = PA.bill_id
            WHERE DATE(P.date) BETWEEN '".$start."' AND '".$end."' AND
            P.payment_status ='Completed'  ".$where."
            GROUP BY PA.paid_by HAVING SUM(PA.amount-PA.pos_balance) > 0";

        $tenderType_res = $this->db->query($tenderType);
		$default_currency = $this->Settings->default_currency;

        $exchange = "SELECT SUM(CASE WHEN ((PM.paid_by = 'cash') AND (SC.currency_id != ".$default_currency.")) THEN amount_exchange*currency_rate ELSE 0 END) as usd,SUM(CASE WHEN ((PM.paid_by = 'cash') AND (SC.currency_id != ".$default_currency.")) THEN SC.amount ELSE 0 END) as For_Ex FROM " . $this->db->dbprefix('sale_currency_archival') . " SC
 		JOIN " . $this->db->dbprefix('bils_archival') . " P ON P.id = SC.bil_id
 		JOIN " . $this->db->dbprefix('payments_archival') . " PM ON PM.bill_id = P.id
 		 WHERE P.payment_status ='Completed' ".$where." AND DATE(P.date) BETWEEN '".$start."' AND '".$end."' ";

 	$exchange = $this->db->query($exchange);
	// print_r($this->db->error());die;
	$query = "SELECT count(P.id) total_transaction,SUM(P.round_total) gross_total1,SUM(P.total-P.total_discount-P.bbq_cover_discount-P.bbq_daywise_discount+P.service_charge_amount+CASE WHEN (P.tax_type= 1) THEN P.total_tax ELSE 0 END) AS gross_total,SUM(P.round_total-P.total_discount) net_total1,SUM(P.total-P.total_discount-P.bbq_cover_discount-P.bbq_daywise_discount+CASE WHEN (P.tax_type= 1) THEN P.total_tax ELSE 0 END)+P.service_charge_amount  AS net_total
        FROM " . $this->db->dbprefix('bils_archival') . "  P
            WHERE DATE(P.date) BETWEEN '".$start."' AND '".$end."' AND
            P.payment_status ='Completed' ".$where." ";

	$details = $this->db->query($query);


    $open_cash = "SELECT RP.id,SUM(RP.cash_in_hand) AS opensale
        FROM " . $this->db->dbprefix('open_register_payments') . " AS RP
         JOIN  " . $this->db->dbprefix('pos_open_register') . " AS PR ON PR.id = RP.register_id
         JOIN  " . $this->db->dbprefix('users') . " AS U ON U.id = PR.user_id
         JOIN  " . $this->db->dbprefix('users') . " AS UU ON UU.id = PR.created_by
            WHERE DATE(RP.date) BETWEEN '".$start."' AND '".$end."'  ";  

    $open_cash = $this->db->query($open_cash);

	

   $buffet_sale = "SELECT count(P.id) as total_transaction,st.name sale_type,SUM(P.total-P.total_discount+CASE WHEN (P.tax_type= 1) THEN P.total_tax ELSE 0 END)  AS sale_type_total
    FROM " . $this->db->dbprefix('bbq_bils') . " AS P
     JOIN  " . $this->db->dbprefix('bbq_sales') . " AS s ON s.id = P.sales_id
     JOIN  " . $this->db->dbprefix('sales_type') . " AS st ON st.id = s.sales_type_id        
        WHERE DATE(P.date) BETWEEN '".$start."' AND '".$end."' AND P.payment_status ='Completed' ".$where." GROUP BY s.sales_type_id ";
// echo $buffet_sale;die;
    $buffet_sale = $this->db->query($buffet_sale);   


    $buffet_saletype = "SELECT st.name sale_type,SUM(P.total-P.total_discount+CASE WHEN (P.tax_type= 1) THEN P.total_tax ELSE 0 END) AS sale_type_total
        FROM " . $this->db->dbprefix('bbq_bils') . "  P
            LEFT JOIN " . $this->db->dbprefix('bbq_sales') . " AS  s
            ON s.id = P.sales_id
	    LEFT JOIN " . $this->db->dbprefix('sales_type') . " AS  st
            ON st.id = s.sales_type_id
            WHERE DATE(P.date) BETWEEN '".$start."' AND '".$end."' AND
            P.payment_status ='Completed' ".$where." 
            GROUP BY s.sales_type_id";
            
     $buffet_saletype = $this->db->query($buffet_saletype);

     $buffet_tender_type = "SELECT PA.paid_by tender_type,SUM(PA.amount-PA.pos_balance) tender_type_total
        FROM " . $this->db->dbprefix('bbq_bils') . "  P
            LEFT JOIN " . $this->db->dbprefix('bbq_payments') . "  PA
            ON P.id = PA.bill_id
            WHERE DATE(P.date) BETWEEN '".$start."' AND '".$end."' AND
            P.payment_status ='Completed' 
            GROUP BY PA.paid_by";  

      $buffet_tender_type = $this->db->query($buffet_tender_type);

      $buffet_exchange = "SELECT SUM(CASE WHEN ((PM.paid_by = 'cash') AND (SC.currency_id != ".$default_currency.")) THEN amount_exchange*currency_rate ELSE 0 END) as usd,SUM(CASE WHEN ((PM.paid_by = 'cash') AND (SC.currency_id != ".$default_currency.")) THEN SC.amount ELSE 0 END) as For_Ex FROM " . $this->db->dbprefix('sale_currency') . " SC
 		JOIN " . $this->db->dbprefix('bbq_bils') . " P ON P.id = SC.bil_id
 		JOIN " . $this->db->dbprefix('bbq_payments') . " PM ON PM.bill_id = P.id
 		 WHERE P.payment_status ='Completed'AND DATE(P.date) BETWEEN '".$start."' AND '".$end."' ";

     	$buffet_exchange = $this->db->query($buffet_exchange);
	  
	  $data['payments'] = $details->result(); 

	 $dine_tender = $tenderType_res->result(); 
	 $buffet_tender = $buffet_tender_type->result();


	 $tender_meged = array_merge($dine_tender, $buffet_tender);

	$tender_totals = array();
	foreach ($tender_meged as $row) {
	  $tender_type = $row->tender_type;
	  $tender_type_total = $row->tender_type_total;
	  $tender_totals[$tender_type] += $tender_type_total;
	}

	$tendertype = array();
	foreach ($tender_totals as $tender_type => $tender_type_total) {
	  $tendertype[] = array('tender_type' => $tender_type, 'tender_type_total' => $tender_type_total);
	}  


	$data['tender_type'] = $tendertype;
	/*$data['tender_type'] = $tenderType_res->result();*/
	$data['sale_type'] = $saletype_res->result(); 
	$data['exchange_amt'] = $exchange->result();
	$data['open_sale'] = $open_cash->result();

	$data['buffet_sale'] = $buffet_sale->result();
	$data['buffet_saletype'] = $buffet_saletype->result();
	$data['buffet_exchange'] = $buffet_exchange->result();

	  return $data;
      return FALSE;
    }   

    public function getShiftWiseReport_new($start,$end,$shift_id,$report_view_access,$report_show)
    {  
        $where ='';
        if($report_view_access != 1)
         {
             $where .= " AND B.table_whitelisted = ".$report_show." ";
         } 

         $WHERE1 ='';
            if($shift_id != 0 ){
                $WHERE1 .= "WHERE id =".$shift."";
            }
            
            $category = "SELECT name,start_time,end_time,id as cate_id, 'split_order' 
            FROM " . $this->db->dbprefix('shift_time') . " ".$WHERE1."";       
            $t = $this->db->query($category);
        if ($t->num_rows() > 0) {
            $i = 0;

            foreach ($t->result() as $row) { 
            	$i++;
					$user = "SELECT U.first_name as username,U.id as sub_id, 'order'
					FROM " . $this->db->dbprefix('bils') . "  B
					JOIN " . $this->db->dbprefix('users') . " U ON U.id = B.created_by
					WHERE  DATE(B.date) BETWEEN '".$start."' AND '".$end."' AND DATE_FORMAT(B.created_on,'%H:%i:%s') BETWEEN '".$row->start_time."' AND '".$row->end_time."' AND
					B.payment_status ='Completed'". $where."  GROUP BY U.id ";                 
                $s = $this->db->query($user);                 
                 // echo $user;die;
            if ($s->num_rows() > 0) {
                foreach ($s->result() as $sow) {

                    $where = '';
                    if(!$this->Owner && !$this->Admin){
                        $where .= " AND B.table_whitelisted =0";
                    }
				
				$myQuery = "SELECT  U.first_name AS username,SUM(CASE WHEN ((PM.paid_by = 'cash') AND (SC.currency_id = 2)) THEN SC.amount - PM.pos_balance ELSE 0 END) AS Cash,SUM(CASE WHEN ((PM.paid_by = 'cash') AND (SC.currency_id != 2)) THEN amount_exchange*currency_rate ELSE 0 END) as For_Exto_usd,SUM(CASE WHEN ((PM.paid_by = 'cash') AND (SC.currency_id != 2)) THEN SC.amount ELSE 0 END) as For_Ex,SUM(DISTINCT CASE WHEN PM.paid_by = 'CC' THEN PM.amount ELSE 0 END) AS Credit_Card,SUM(DISTINCT CASE WHEN PM.paid_by = 'credit' THEN PM.amount ELSE 0 END) AS credit,SUM( DISTINCT P.balance) AS return_balance FROM " . $this->db->dbprefix('sale_currency') . " SC JOIN " . $this->db->dbprefix('bils') . " P ON P.id = SC.bil_id JOIN " . $this->db->dbprefix('payments') . " PM ON PM.bill_id = P.id
				JOIN " . $this->db->dbprefix('users') . " U ON U.id =P.created_by WHERE DATE(P.date) BETWEEN '".$start."' AND '".$end."'  AND P.payment_status='Completed' AND DATE_FORMAT(P.created_on,'%H:%i:%s') BETWEEN '".$row->start_time."' AND '".$row->end_time."' AND SC.bil_id IN (SELECT id FROM srampos_bils  WHERE P.payment_status ='Completed') GROUP BY U.id";



                     // echo $myQuery;die;
                    $o = $this->db->query($myQuery);
                                                            
                        $split[$row->cate_id][] = $sow;
                        if ($o->num_rows() > 0) {                                    
                            foreach($o->result() as $oow){
                                $order[$sow->sub_id][] = $oow;
                            }
                        }
                        $sow->order = $order[$sow->sub_id];   
                                       
                }     
               // $row->cate_id  =   $sow->order;             
                        $row->split_order = $split[$row->cate_id];
        }else{
            $row->split_order = array();
        }                
              $data[] = $row;
        }
        //echo "<pre>";
        //print_r($data);die;
           
            return $data;
        }        
        return FALSE;   
       


    }  
public function getShiftWiseReport($start,$end,$shift_id,$report_view_access,$report_show)
    {  
    	$defalut_currency = $this->Settings->default_currency;
         $where ='';
        if($report_view_access != 1)
         {
             $where .= " AND B.table_whitelisted = ".$report_show." ";
         } 

         $WHERE1 ='';
            if($shift_id != 0 ){
                $WHERE1 .= "WHERE id =".$shift_id."";
            }else{
            	$WHERE1 .= "WHERE status=1";
            }
            
            $category = "SELECT name,start_time,end_time,id as cate_id, 'split_order' 
            FROM " . $this->db->dbprefix('shift_time') . " ".$WHERE1."";                
            $u = $this->db->query($category);
            
        if ($u->num_rows() > 0) {
            foreach (($u->result()) as $uow) {

            /*$myQuery = "SELECT U.first_name AS username,SUM(DISTINCT CASE WHEN ((PM.paid_by = 'cash') AND (SC.currency_id = ".$defalut_currency.")) THEN PM.amount ELSE 0 END) AS Cash,SUM(DISTINCT CASE WHEN ((PM.paid_by = 'cash') AND (SC.currency_id != ".$defalut_currency.")) THEN amount_exchange*currency_rate ELSE 0 END) as For_Ex,SUM(DISTINCT CASE WHEN PM.paid_by = 'CC' THEN PM.amount ELSE 0 END) AS Credit_Card,SUM(DISTINCT CASE WHEN PM.paid_by = 'credit' THEN PM.amount ELSE 0 END) AS credit,SUM(DISTINCT P.paid) AS Bill_amt,SUM(DISTINCT P.balance) AS return_balance,SUM(CASE WHEN ((PM.paid_by = 'cash') AND (SC.currency_id != ".$defalut_currency.")) THEN SC.amount ELSE 0 END) as ForEx
                    FROM " . $this->db->dbprefix('bils') . " P
                    JOIN " . $this->db->dbprefix('users') . " U ON P.created_by = U.id
                    JOIN " . $this->db->dbprefix('payments') . " PM ON PM.bill_id = P.id
                    JOIN " . $this->db->dbprefix('sale_currency') . " SC ON SC.bil_id = P.id
                        WHERE DATE(P.date) BETWEEN '".$start."' AND '".$end."' AND
                         P.payment_status ='Completed' 
                        ".$where." GROUP BY U.id "; */

			/*	$myQuery = "SELECT t.id,t.username,t.Cash,t.For_Exto_usd,t.Credit_Card,t.credit,(t.Cash+t.For_Exto_usd+t.Credit_Card+t.credit) as Bill_amt from (SELECT ".$uow->cate_id." as id, U.first_name AS username,SUM(CASE WHEN ((PM.paid_by = 'cash') AND (SC.currency_id = 2)) THEN SC.amount - PM.pos_balance ELSE 0 END) AS Cash,SUM(CASE WHEN ((PM.paid_by = 'cash') AND (SC.currency_id != 2)) THEN amount_exchange*currency_rate ELSE 0 END) as For_Exto_usd,SUM(CASE WHEN ((PM.paid_by = 'cash') AND (SC.currency_id != 2)) THEN SC.amount ELSE 0 END) as For_Ex,SUM(DISTINCT CASE WHEN PM.paid_by = 'CC' THEN PM.amount ELSE 0 END) AS Credit_Card,SUM(DISTINCT CASE WHEN PM.paid_by = 'credit' THEN PM.amount ELSE 0 END) AS credit,SUM( DISTINCT P.balance) AS return_balance,SUM(DISTINCT P.total-P.total_discount+CASE WHEN (P.tax_type= 1) THEN P.total_tax ELSE 0 END) AS Bill_amt FROM " . $this->db->dbprefix('sale_currency') . " SC JOIN " . $this->db->dbprefix('bils') . " P ON P.id = SC.bil_id JOIN " . $this->db->dbprefix('payments') . " PM ON PM.bill_id = P.id
				JOIN " . $this->db->dbprefix('users') . " U ON U.id =P.created_by WHERE DATE(P.date) BETWEEN '".$start."' AND '".$end."'  AND P.payment_status='Completed' AND DATE_FORMAT(P.created_on,'%H:%i:%s') BETWEEN '".$uow->start_time."' AND '".$uow->end_time."' AND SC.bil_id IN (SELECT id FROM srampos_bils  WHERE P.payment_status ='Completed' AND DATE(P.date) BETWEEN '".$start."' AND '".$end."' ) GROUP BY U.id) t";*/
  // echo $myQuery;die;
					

				$myQuery = "SELECT t.id,t.username,t.Cash,t.For_Exto_usd,t.Credit_Card,t.credit,(t.Cash+t.For_Exto_usd+t.Credit_Card+t.credit) as Bill_amt from (SELECT ".$uow->cate_id." as id, U.first_name AS username,SUM(CASE WHEN ((PM.paid_by = 'cash') AND (SC.currency_id = 2)) THEN SC.amount - PM.pos_balance ELSE 0 END) AS Cash,SUM(CASE WHEN ((PM.paid_by = 'cash') AND (SC.currency_id != 2)) THEN amount_exchange*currency_rate ELSE 0 END) as For_Exto_usd,SUM(CASE WHEN ((PM.paid_by = 'cash') AND (SC.currency_id != 2)) THEN SC.amount ELSE 0 END) as For_Ex,SUM(DISTINCT CASE WHEN PM.paid_by = 'CC' THEN PM.amount ELSE 0 END) AS Credit_Card,SUM(DISTINCT CASE WHEN PM.paid_by = 'credit' THEN PM.amount ELSE 0 END) AS credit,SUM( DISTINCT P.balance) AS return_balance,SUM(DISTINCT P.total-P.total_discount+CASE WHEN (P.tax_type= 1) THEN P.total_tax ELSE 0 END) AS Bill_amt FROM " . $this->db->dbprefix('sale_currency') . " SC JOIN " . $this->db->dbprefix('bils') . " P ON P.id = SC.bil_id JOIN " . $this->db->dbprefix('payments') . " PM ON PM.bill_id = P.id
				JOIN " . $this->db->dbprefix('users') . " U ON U.id =P.created_by WHERE DATE(P.date) BETWEEN '".$start."' AND '".$end."'  AND P.payment_status='Completed' AND DATE_FORMAT(P.created_on,'%H:%i:%s') BETWEEN '".$uow->start_time."' AND '".$uow->end_time."' AND SC.bil_id IN (SELECT id FROM srampos_bils  WHERE P.payment_status ='Completed') GROUP BY U.id) t";
  //echo $myQuery;die;
					
					

                    $q = $this->db->query($myQuery);
                    if ($q->num_rows() > 0) {
                        foreach (($q->result()) as $row) {
                            $user[$uow->cate_id][] = $row;
                        }
                        $uow->user = $user[$uow->cate_id];
                        $data[] = $uow;
                    }
                }//die;
                    //echo "<pre>";
                    //print_r($data);die;
                return $data;
            // return array('data'=>$data);
        }
        return FALSE;
    }  

public function getShiftWiseReport_archival($start,$end,$shift_id,$report_view_access,$report_show)
    {  
    	$defalut_currency = $this->Settings->default_currency;
         $where ='';
        if($report_view_access != 1)
         {
             $where .= " AND B.table_whitelisted = ".$report_show." ";
         } 

         $WHERE1 ='';
            if($shift_id != 0 ){
                $WHERE1 .= "WHERE id =".$shift_id."";
            }else{
            	$WHERE1 .= "WHERE status=1";
            }
            
            $category = "SELECT name,start_time,end_time,id as cate_id, 'split_order' 
            FROM " . $this->db->dbprefix('shift_time') . " ".$WHERE1."";                
            $u = $this->db->query($category);
            
        if ($u->num_rows() > 0) {
            foreach (($u->result()) as $uow) {

            /*$myQuery = "SELECT U.first_name AS username,SUM(DISTINCT CASE WHEN ((PM.paid_by = 'cash') AND (SC.currency_id = ".$defalut_currency.")) THEN PM.amount ELSE 0 END) AS Cash,SUM(DISTINCT CASE WHEN ((PM.paid_by = 'cash') AND (SC.currency_id != ".$defalut_currency.")) THEN amount_exchange*currency_rate ELSE 0 END) as For_Ex,SUM(DISTINCT CASE WHEN PM.paid_by = 'CC' THEN PM.amount ELSE 0 END) AS Credit_Card,SUM(DISTINCT CASE WHEN PM.paid_by = 'credit' THEN PM.amount ELSE 0 END) AS credit,SUM(DISTINCT P.paid) AS Bill_amt,SUM(DISTINCT P.balance) AS return_balance,SUM(CASE WHEN ((PM.paid_by = 'cash') AND (SC.currency_id != ".$defalut_currency.")) THEN SC.amount ELSE 0 END) as ForEx
                    FROM " . $this->db->dbprefix('bils') . " P
                    JOIN " . $this->db->dbprefix('users') . " U ON P.created_by = U.id
                    JOIN " . $this->db->dbprefix('payments') . " PM ON PM.bill_id = P.id
                    JOIN " . $this->db->dbprefix('sale_currency') . " SC ON SC.bil_id = P.id
                        WHERE DATE(P.date) BETWEEN '".$start."' AND '".$end."' AND
                         P.payment_status ='Completed' 
                        ".$where." GROUP BY U.id "; */

			/*	$myQuery = "SELECT t.id,t.username,t.Cash,t.For_Exto_usd,t.Credit_Card,t.credit,(t.Cash+t.For_Exto_usd+t.Credit_Card+t.credit) as Bill_amt from (SELECT ".$uow->cate_id." as id, U.first_name AS username,SUM(CASE WHEN ((PM.paid_by = 'cash') AND (SC.currency_id = 2)) THEN SC.amount - PM.pos_balance ELSE 0 END) AS Cash,SUM(CASE WHEN ((PM.paid_by = 'cash') AND (SC.currency_id != 2)) THEN amount_exchange*currency_rate ELSE 0 END) as For_Exto_usd,SUM(CASE WHEN ((PM.paid_by = 'cash') AND (SC.currency_id != 2)) THEN SC.amount ELSE 0 END) as For_Ex,SUM(DISTINCT CASE WHEN PM.paid_by = 'CC' THEN PM.amount ELSE 0 END) AS Credit_Card,SUM(DISTINCT CASE WHEN PM.paid_by = 'credit' THEN PM.amount ELSE 0 END) AS credit,SUM( DISTINCT P.balance) AS return_balance,SUM(DISTINCT P.total-P.total_discount+CASE WHEN (P.tax_type= 1) THEN P.total_tax ELSE 0 END) AS Bill_amt FROM " . $this->db->dbprefix('sale_currency') . " SC JOIN " . $this->db->dbprefix('bils') . " P ON P.id = SC.bil_id JOIN " . $this->db->dbprefix('payments') . " PM ON PM.bill_id = P.id
				JOIN " . $this->db->dbprefix('users') . " U ON U.id =P.created_by WHERE DATE(P.date) BETWEEN '".$start."' AND '".$end."'  AND P.payment_status='Completed' AND DATE_FORMAT(P.created_on,'%H:%i:%s') BETWEEN '".$uow->start_time."' AND '".$uow->end_time."' AND SC.bil_id IN (SELECT id FROM srampos_bils  WHERE P.payment_status ='Completed' AND DATE(P.date) BETWEEN '".$start."' AND '".$end."' ) GROUP BY U.id) t";*/
  // echo $myQuery;die;
					

			/* command on 12-02-2020 cuz slowness
			$myQuery = "SELECT t.id,t.username,t.Cash,t.For_Exto_usd,t.Credit_Card,t.credit,(t.Cash+t.For_Exto_usd+t.Credit_Card+t.credit) as Bill_amt from (SELECT ".$uow->cate_id." as id, U.first_name AS username,SUM(CASE WHEN ((PM.paid_by = 'cash') AND (SC.currency_id = 2)) THEN SC.amount - PM.pos_balance ELSE 0 END) AS Cash,SUM(CASE WHEN ((PM.paid_by = 'cash') AND (SC.currency_id != 2)) THEN amount_exchange*currency_rate ELSE 0 END) as For_Exto_usd,SUM(CASE WHEN ((PM.paid_by = 'cash') AND (SC.currency_id != 2)) THEN SC.amount ELSE 0 END) as For_Ex,SUM(DISTINCT CASE WHEN PM.paid_by = 'CC' THEN PM.amount ELSE 0 END) AS Credit_Card,SUM(DISTINCT CASE WHEN PM.paid_by = 'credit' THEN PM.amount ELSE 0 END) AS credit,SUM( DISTINCT P.balance) AS return_balance,SUM(DISTINCT P.total-P.total_discount+CASE WHEN (P.tax_type= 1) THEN P.total_tax ELSE 0 END) AS Bill_amt FROM " . $this->db->dbprefix('sale_currency_archival') . " SC JOIN " . $this->db->dbprefix('bils_archival') . " P ON P.id = SC.bil_id JOIN " . $this->db->dbprefix('payments_archival') . " PM ON PM.bill_id = P.id
				JOIN " . $this->db->dbprefix('users') . " U ON U.id =P.created_by WHERE DATE(P.date) BETWEEN '".$start."' AND '".$end."'  AND P.payment_status='Completed' AND DATE_FORMAT(P.created_on,'%H:%i:%s') BETWEEN '".$uow->start_time."' AND '".$uow->end_time."' AND SC.bil_id IN (SELECT id FROM srampos_bils_archival  WHERE P.payment_status ='Completed') GROUP BY U.id) t"; */
$myQuery = "SELECT t.id,t.username,t.Cash,t.For_Exto_usd,t.Credit_Card,t.credit,(t.Cash+t.For_Exto_usd+t.Credit_Card+t.credit) as Bill_amt from (SELECT ".$uow->cate_id." as id, U.first_name AS username,SUM(CASE WHEN ((PM.paid_by = 'cash') AND (SC.currency_id = 2)) THEN SC.amount - PM.pos_balance ELSE 0 END) AS Cash,SUM(CASE WHEN ((PM.paid_by = 'cash') AND (SC.currency_id != 2)) THEN amount_exchange*currency_rate ELSE 0 END) as For_Exto_usd,SUM(CASE WHEN ((PM.paid_by = 'cash') AND (SC.currency_id != 2)) THEN SC.amount ELSE 0 END) as For_Ex,SUM(DISTINCT CASE WHEN PM.paid_by = 'CC' THEN PM.amount ELSE 0 END) AS Credit_Card,SUM(DISTINCT CASE WHEN PM.paid_by = 'credit' THEN PM.amount ELSE 0 END) AS credit,SUM( DISTINCT P.balance) AS return_balance,SUM(DISTINCT P.total-P.total_discount+CASE WHEN (P.tax_type= 1) THEN P.total_tax ELSE 0 END) AS Bill_amt FROM " . $this->db->dbprefix('sale_currency_archival') . " SC JOIN " . $this->db->dbprefix('bils_archival') . " P ON P.id = SC.bil_id JOIN " . $this->db->dbprefix('payments_archival') . " PM ON PM.bill_id = P.id
				JOIN " . $this->db->dbprefix('users') . " U ON U.id =P.created_by WHERE DATE(P.date) BETWEEN '".$start."' AND '".$end."'  AND P.payment_status='Completed' AND DATE_FORMAT(P.created_on,'%H:%i:%s') BETWEEN '".$uow->start_time."' AND '".$uow->end_time."'  GROUP BY U.id) t";
                    $q = $this->db->query($myQuery);
					//echo $myQuery;die;
                    if ($q->num_rows() > 0) {
                        foreach (($q->result()) as $row) {
                            $user[$uow->cate_id][] = $row;
                        }
                        $uow->user = $user[$uow->cate_id];
                        $data[] = $uow;
                    }
                }//die;
                    //echo "<pre>";
                    //print_r($data);die;
                return $data;
            // return array('data'=>$data);
        }
        return FALSE;
    }
public function getShiftWiseReport_23012019($start,$end,$shift_id,$report_view_access,$report_show)
    {  
        $where ='';
        /*if($warehouse_id != 0)
        {
            $where = "AND P.warehouse_id =".$warehouse_id."";
        }*/
        if($report_view_access != 1)
         {
             $where .= " AND B.table_whitelisted = ".$report_show." ";
         } 

         $WHERE1 ='';
            if($shift_id != 0 ){
                $WHERE1 .= "WHERE id =".$shift."";
            }
            
            $category = "SELECT name,start_time,end_time,id as cate_id, 'split_order' 
            FROM " . $this->db->dbprefix('shift_time') . " ".$WHERE1."";       
            $t = $this->db->query($category);
        if ($t->num_rows() > 0) {
            
            foreach ($t->result() as $row) { 
					$user = "SELECT U.first_name as username,U.id as sub_id, 'order'
					FROM " . $this->db->dbprefix('bils') . "  B
					JOIN " . $this->db->dbprefix('users') . " U ON U.id = B.created_by
					WHERE  DATE(B.date) BETWEEN '".$start."' AND '".$end."' AND DATE_FORMAT(B.created_on,'%H:%i:%s') BETWEEN '".$row->start_time."' AND '".$row->end_time."' AND
					B.payment_status ='Completed'". $where."  GROUP BY U.id ";                 
                $s = $this->db->query($user);                 
                 // echo $user;die;
            if ($s->num_rows() > 0) {
                foreach ($s->result() as $sow) {

                    $where = '';
                    if(!$this->Owner && !$this->Admin){
                        $where .= " AND B.table_whitelisted =0";
                    }
				/*$myQuery = "SELECT SUM(B.total-B.total_discount+CASE WHEN (B.tax_type= 1) THEN B.total_tax ELSE 0 END) AS grand_total
				FROM " . $this->db->dbprefix('bils') . " B
				WHERE DATE(B.date) BETWEEN '".$start."' AND '".$end."' AND DATE_FORMAT(B.created_on,'%H:%i:%s') BETWEEN '".$row->start_time."' AND '".$row->end_time."'  AND
                         B.payment_status ='Completed'  
                        ".$where." " ;*/

                        	$myQuery = "SELECT PA.paid_by tender_type,SUM(B.total-B.total_discount+CASE WHEN (B.tax_type= 1) THEN B.total_tax ELSE 0 END) AS grand_total
					        FROM " . $this->db->dbprefix('bils') . "  B
					            LEFT JOIN " . $this->db->dbprefix('payments') . "   PA
					            ON B.id = PA.bill_id
					            WHERE DATE(B.date) BETWEEN '".$start."' AND '".$end."' AND DATE_FORMAT(B.created_on,'%H:%i:%s') BETWEEN '".$row->start_time."' AND '".$row->end_time."' AND B.created_by =".$sow->sub_id."  AND
					            B.payment_status ='Completed'  ".$where."
					            GROUP BY PA.paid_by";

                     // echo $myQuery;die;
                    $o = $this->db->query($myQuery);
                                                            
                        $split[$row->cate_id][] = $sow;
                        if ($o->num_rows() > 0) {                                    
                            foreach($o->result() as $oow){
                                $order[$sow->sub_id][] = $oow;
                            }
                        }
                        $sow->order = $order[$sow->sub_id];                   
                }                    
                        $row->split_order = $split[$row->cate_id];
        }else{
            $row->split_order = array();
        }                
              $data[] = $row;
        }
            //echo $total->num_rows();
        /*echo "<pre>";
            print_R($data);exit;*/
            // return array('data'=>$data);
            return $data;
        }        
        return FALSE;   
       

/*SELECT P.created_on,SUM(CASE WHEN ((PM.paid_by = 'cash') AND (SC.currency_id = 2)) THEN SC.amount - PM.pos_balance ELSE 0 END) AS Cash,SUM(CASE WHEN ((PM.paid_by = 'cash') AND (SC.currency_id != 2)) THEN amount_exchange*currency_rate ELSE 0 END) as For_Exto_usd,SUM(CASE WHEN ((PM.paid_by = 'cash') AND (SC.currency_id != 2)) THEN SC.amount ELSE 0 END) as For_Ex,SUM(DISTINCT CASE WHEN PM.paid_by = 'CC' THEN PM.amount ELSE 0 END) AS Credit_Card,SUM( DISTINCT P.balance) AS return_balance FROM srampos_sale_currency SC JOIN srampos_bils P ON P.id = SC.bil_id JOIN srampos_payments PM ON PM.bill_id = P.id WHERE DATE(P.date) BETWEEN '2019-01-01' AND '2019-02-01' AND P.payment_status='Completed' AND DATE_FORMAT(P.created_on,'%H:%i:%s') BETWEEN '23:00:00' AND '23:59:00' AND SC.bil_id IN (SELECT id FROM srampos_bils  WHERE P.payment_status ='Completed') 
*/
    }    
    public function getshifttime()
    {
    	$this->db->where('status', 1);
        $q = $this->db->get('shift_time');
        // print_r($this->db->last_query());die;
         if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

  /*  public function getSettlementReport($start,$end)
    {
        $saletype = "SELECT st.name sale_type,SUM(P.round_total) sale_type_total1,SUM(P.total-P.total_discount+CASE WHEN (P.tax_type= 1) THEN P.total_tax ELSE 0 END) as sale_type_total
        FROM srampos_bils  P
            LEFT JOIN srampos_sales  s
            ON s.id = P.sales_id
	    LEFT JOIN srampos_sales_type  st
            ON st.id = s.sales_type_id
            WHERE DATE(P.date) BETWEEN '".$start."' AND '".$end."' AND
            P.payment_status ='Completed' 
            GROUP BY s.sales_type_id";
            
        $saletype_res = $this->db->query($saletype);//echo $myQuery;exit;
	
	$tenderType = "SELECT PA.paid_by tender_type,PA.cc_type,SUM(PA.amount-PA.pos_balance) tender_type_total
        FROM srampos_bils  P
            LEFT JOIN srampos_payments  PA
            ON P.id = PA.bill_id
            WHERE DATE(P.date) BETWEEN '".$start."' AND '".$end."' AND
            P.payment_status ='Completed' 
            GROUP BY PA.paid_by";
     // echo $tenderType;    die;
        $tenderType_res = $this->db->query($tenderType);//echo $myQuery;exit;

        $default_currency = $this->Settings->default_currency;

        $exchange = "SELECT SUM(CASE WHEN ((PM.paid_by = 'cash') AND (SC.currency_id != ".$default_currency.")) THEN amount_exchange*currency_rate ELSE 0 END) as usd,SUM(CASE WHEN ((PM.paid_by = 'cash') AND (SC.currency_id != ".$default_currency.")) THEN SC.amount ELSE 0 END) as For_Ex FROM " . $this->db->dbprefix('sale_currency') . " SC
 		JOIN " . $this->db->dbprefix('bils') . " P ON P.id = SC.bil_id
 		JOIN " . $this->db->dbprefix('payments') . " PM ON PM.bill_id = P.id
 		 WHERE P.payment_status ='Completed' ".$where." AND DATE(P.date) BETWEEN '".$start."' AND '".$end."' ";

 	$exchange = $this->db->query($exchange);

	
	$query = "SELECT count(P.id) total_transaction,SUM(P.round_total) gross_total,SUM(P.total-P.total_discount+P.total_tax) net_total2,SUM(P.total-P.total_discount+CASE WHEN (P.tax_type= 1) THEN total_tax ELSE 0 END) AS net_total
        FROM srampos_bils  P
            WHERE DATE(P.date) BETWEEN '".$start."' AND '".$end."' AND
            P.payment_status ='Completed' ";
            // echo $query;
	$details = $this->db->query($query);//print_R($this->db->error());exit;
	$data['payments'] = $details->result(); 
	$data['tender_type'] = $tenderType_res->result();
	$data['sale_type'] = $saletype_res->result();
	$data['exchange_amt'] = $exchange->result();
        //if ($q->num_rows() > 0) { print_R($q->result());exit;
        //    foreach (($q->result()) as $row) {
        //        $data[] = $row;
        //    }
        //    return $data;
        //}
	    //print_R($data);exit;
	return $data;
        return FALSE;
    }*/
public function check_reportview_access($pass_code){

    $myQuery = "SELECT (CASE WHEN (S.taxation_all  =".$pass_code.")  THEN 1 WHEN (S.taxation_include  =".$pass_code.") THEN 2 WHEN ((S.taxation_exclude =".$pass_code.") )  THEN 3                           
         ELSE 0 END) AS report_view
        FROM " . $this->db->dbprefix('pos_settings') . " AS S ";         
        $q = $this->db->query($myQuery);
        if ($q->num_rows() > 0) {

            $res = $q->row();
            return $res->report_view;
        }  
    }	
    /*variant start*/

    function isVarientExist($rid){
	$this->db->select('v.*,r.*,v.id variant_id,r.price as variant_price');
	$this->db->from('recipe_variants_values r');
	$this->db->join('recipe_variants v','v.id=r.attr_id');
	$this->db->where(array('r.recipe_id'=>$rid));
	$q = $this->db->get();	
	if($q->num_rows()>0){
	    return $q->result();
	}
	return false;
	
    }
    function getrecipeVarient($rid,$variant_id){
	$this->db->select('v.*,r.*,v.id variant_id');
	$this->db->from('recipe_variants_values r');
	$this->db->join('recipe_variants v','v.id=r.attr_id');
	$this->db->where('r.recipe_id',$rid);
	$this->db->where('v.id',$variant_id);
	$q = $this->db->get();	
	if($q->num_rows()>0){
	    return $q->row();
	}
	return false;
	
    }

    function getVariantData($vid,$rid){
	$this->db->select('v.*,r.*,v.id variant_id');
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
    /*variant end */

    /*loyalty start*/
    public function getLoyaltycustomer(){
    		$this->db->select('C.name,C.phone,C.id AS customer_id,LP.id,LP.total_points,S.eligibity_point,LP.loyalty_card_no,LP.expiry_date')
		    ->from('loyalty_points LP')
		    ->join('loyalty_settings S', 'S.id = LP.loyalty_id') 			            
		    ->join('companies C', 'C.id = LP.customer_id') 
		    ->where('S.status',1);    		 
		    if($customer_id){
		    	$this->db->where('LP.customer_id', $customer_id);
		    }			   
		    $r = $this->db->get();		    
		    if ($r->num_rows() > 0) {
		    	 foreach (($r->result()) as $row) {
	            	$data[] =   $row;
				 }				 
                return $data;
            }
        return FALSE;		
	}
    public function getLoyaltypointsBycustomer($customer_id){
    		$current_date = date('Y-m-d');
    		$this->db->select('LP.id,LP.total_points,S.eligibity_point,LP.loyalty_card_no,LP.expiry_date,LP.customer_id')
		    ->from('loyalty_points LP')
		    ->join('loyalty_settings S', 'S.id = LP.loyalty_id') 			            
		    ->join('companies C', 'C.id = LP.customer_id') 
		    ->where('"'.$current_date.'" BETWEEN DATE(S.from_date) and DATE(S.end_date)')            
		    ->where('S.status',1);    
		    $this->db->where('LP.customer_id', $customer_id);
		    $r = $this->db->get();				    
		     // print_r($this->db->last_query());die;
		    if ($r->num_rows() > 0) {		    	 				 
                return $r->row();
            }
        return FALSE;		
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
	public function getLoyaltyCardNo(){
		
		$current_date = date('Y-m-d');
    	$myQuery = "SELECT LC.id,LC.card_no
        FROM " . $this->db->dbprefix('loyalty_cards') . " LC
              JOIN " . $this->db->dbprefix('loyalty_settings') . " LS ON LS.id = LC.loyalty_id        
            WHERE LC.id NOT IN (SELECT loyalty_card_id FROM " . $this->db->dbprefix('loyalty_points') . ") ";            
        $q = $this->db->query($myQuery);
        
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
	}

	public function LoyaltyCardIssuetoCustomer($customer_id, $cus_loyalty,$loyalty_card){
		$this->db->where('customer_id', $customer_id);
		$q = $this->db->update('loyalty_points', $cus_loyalty);		
		if($q){
			$this->db->where('id', $loyalty_card);
			$this->db->update('loyalty_cards', array('status' => 2));			 
			 return TRUE;
		}		
		return FALSE;	
	}

 /*$this->db->select('S.id AS loyalty_id,A.start_amount,A.end_amount,A.per_amounts,A.per_points')
            ->from('loyalty_accumalation A')
            ->join('loyalty_settings S', 'S.id = A.loyalty_id') 
            ->where('A.start_amount <=',$total)
            ->where('A.end_amount >=',$total)    
            ->where('S.status',1);    
            $this->db->order_by('A.id', 'ASC');
            $this->db->limit(1);
            $l = $this->db->get();  

            if ($l->num_rows() > 0) {            	
				$loyalty_id =  $l->row('loyalty_id');
				$per_amounts =  $l->row('per_amounts');
				$per_points =  $l->row('per_points');
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

	             $c = $this->db->select('customer_id,total_points')->where('customer_id', $customer_id)->get('loyalty_points');
	             if ($c->num_rows() > 0) {	             	
    					$customer =  $c->row('customer_id');
    					$points =  $c->row('total_points');
    					$totalpoints = $points + $total_points;
					$this->db->set('total_points', $totalpoints,false);
					$this->db->where('customer_id',$customer);
					$this->db->update('loyalty_points');					
	             }else{	             	
	             	$this->db->insert('loyalty_points', $loyalty_insert);	             	
	             }	
			}	*/
	public function LoyaltyRedemtion($customer_id, $points,$bal_amount){	

	   	$this->db->select('LR.id AS redemption_id,LR.points AS redempoint,LR.amount')
            ->from('loyalty_redemption LR')
            ->join('loyalty_settings S', 'S.id = LR.loyalty_id')             
            ->where('LR.points <=',$points)              
            ->where('S.status',1);    
            $this->db->order_by('LR.id', 'ASC');
            $this->db->limit(1);
            $redem = $this->db->get();             
             if ($redem->num_rows() > 0) { 
                     	
				$loyalty_id =  $redem->row('loyalty_id');
				$redempoint =  $redem->row('redempoint');
				$amount =  $redem->row('amount');
				$count = $points /$redempoint;
				$total_redemamount  = intval($count) * $amount;
				$data = array(
						'redempoint' => intval($count) * $redempoint,
						'total_redemamount' => $total_redemamount,							
						'redemption' => $redempoint,
						'amount' => $amount,
						'customer_id' => $customer_id,
						);				
				  	 return $data;
			}				
			return 0;			
    }       

    public function LoyaltyRedemtiondetails($customer_id){	

	   	$this->db->select('LR.id AS redemption_id,LR.points AS redempoint,LR.amount')
            ->from('loyalty_redemption LR')
            ->join('loyalty_settings S', 'S.id = LR.loyalty_id','left')             
            ->join('loyalty_points LP', 'S.id = LP.loyalty_id','left')             
            ->where('LP.customer_id',$customer_id)              
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
						'customer_id' => $customer_id,
						);				
				  	 return $data;
			}				
			return 0;			
    }

function getLoyaltyCustomerByCardNo($term, $limit = 1){

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
    /*loyalty end */
    
    
    public function getAllorders_splitID($splitid){
		$current_date = date('Y-m-d');
		$current_date = $this->site->getTransactionDate();
		$this->db->select("orders.split_id, orders.customer_id,  'order'");
		
		$this->db->where("orders.payment_status", NULL);
		$this->db->where('orders.order_cancel_status', 0);
		$this->db->where('DATE(date)', $current_date);
		$this->db->where('orders.warehouse_id', $this->session->userdata('warehouse_id'));
		if(!empty($splitid)){
		$this->db->where('orders.split_id', $splitid);
		}
		$this->db->group_by('orders.split_id');
		$t = $this->db->get("orders");
		if ($t->num_rows() > 0) {
			foreach($t->result() as $row){
						
				$this->db->select("id ");
				$checkbils = $this->db->get_where('sales', array('sales_split_id' => $row->split_id));
				if ($checkbils->num_rows() == 0) {
								
					 $this->db->select("orders.id, orders.customer_id, kitchen_orders.id AS kitchen, kitchen_orders.status,orders.order_type, orders.seats_id, orders.order_status, orders.reference_no, orders.date, orders.split_id, orders.table_id, 'items' AS item")
					 
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
	
	

    function kot_print_copy($split_id,$kitchen_id){
	$this->db->select('orders.*, restaurant_tables.name AS table_name');
				$this->db->join('restaurant_tables', 'restaurant_tables.id = orders.table_id', 'left');		
			   $this->db->where('orders.split_id', $split_id);
				$t = $this->db->get('orders');
				$kit = array();
				$consolid_kit = array();
                 $items = array();
				if ($t->num_rows() > 0) {
					$orders_details =  $t->row();
					$orders_details->is_print_copy = true;
					$kit['orders_details'] = $orders_details;
					$consolid_kit['orders_details'] = $orders_details;
                        $orderIDs =array();
                        foreach($t->result() as $k => $row){
                            $orderIDs[$k] = $row->id;
                        }
                        $this->db->where('order_item_cancel_status',0);
                        $this->db->where_in('sale_id',$orderIDs);
                        $q = $this->db->get('order_items');
                        $orderitems = $q->result_array();
				}
				foreach($orderitems as $item){
					 $recipe=$this->site->getrecipeByID($item['recipe_id']);
					 if($recipe->type =="combo"){
							$combo_items=$this->get_comboItemslist($recipe->id,$recipe->name);
							$items=array_merge($items,$combo_items);
						}
					$items[]=$item;
				}
			
/*Consolidate kot only one single priter */	
if($this->pos_settings->consolidated_kot_print_option == 0){					
if($this->pos_settings->consolidated_kot_print != 0){
				$table_id = $consolid_kit['orders_details']->table_id;
				$consolid_kot_print_details = $this->db->select("printers.*")
				->join('restaurant_areas', 'restaurant_areas.printer_id = printers.id','left')
				->join('restaurant_tables', 'restaurant_tables.area_id = restaurant_areas.id','left')							
				->where('restaurant_tables.id', $table_id)
				->get('printers');
				if ($consolid_kot_print_details->num_rows() > 0) {
				  $consolid_kit['consolid_kot_print_details'][] =  $consolid_kot_print_details->row();
			     }else{
			     	$consolid_kit['consolid_kot_print_details'] =array();
			     }
					$consolid_kit_item =array();
					foreach($items as $key => $kit_item){							
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
							$consolid_kit_item->kit_o[$key]['en_variant_name'] = $kit_item['variant'] ? $kit_item['variant'] :'';

							/*$kotimagefolder = date('Ymd');	
							$consolid_kit_item[$key]['khmer_recipe_image'] = (!empty($kit_item['image_path']) && file_exists('assets/language/'.$kotimagefolder.'/'.$kit_item['image_path']))? (base_url().'assets/language/'.$kotimagefolder.'/'.$kit_item['image_path']) : '';*/

							if($this->pos_settings->kot_print_lang_option ==1){
								$kotimagefolder = date('Ymd');	
								$consolid_kit_item[$key]['khmer_recipe_image'] = (!empty($kit_item['image_path']) && file_exists('assets/language/'.$kotimagefolder.'/'.$kit_item['image_path']))? (base_url().'assets/language/'.$kotimagefolder.'/'.$kit_item['image_path']) : '';
							}else{
								$consolid_kit_item[$key]['khmer_recipe_image'] =(!empty($khmer_image) && file_exists('assets/language/'.$khmer_image))? (base_url().'assets/language/'.$khmer_image) : '';
							}
                            $code=$this->site->get_recipeCodeById($kit_item['recipe_id']);
							$consolid_kit_item[$key]['code'] = substr($code,0, 4);
							$consolid_kit_item[$key]['en_recipe_name'] = $kit_item['recipe_name'];
							$consolid_kit_item[$key]['comment'] = $kit_item['comment'];
							$consolid_kit_item[$key]['quantity'] = $kit_item['quantity'];
							$consolid_kit_item[$key]['get_item_name'] = $get_item->name;
							$consolid_kit_item[$key]['total_get_quantity'] = $get_item->total_get_quantity;
							$addondetails ='';
							$addondetails = $this->site->getAddonByRecipeidAndOrderitemid_kot($kit_item['recipe_id'],$kit_item['id']);
							$consolid_kit_item[$key]['addons'] = $addondetails;
					}						
						$consolid_kit['consolid_kitchens'] = $consolid_kit_item;
				    }
				else
				{
					$consolid_kit['consolid_kitchens'] = array();
				}
/*Consolidate kot only one single priter */	
}else{
/*Consolidate kot for table area wise */	
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
						
                       if($this->pos_settings->kot_print_lang_option ==1){
							$kotimagefolder = date('Ymd');	
							$consolidate_kitchen_row->kit_o[$key]['khmer_recipe_image'] = (!empty($kit_item['image_path']) && file_exists('assets/language/'.$kotimagefolder.'/'.$kit_item['image_path']))? (base_url().'assets/language/'.$kotimagefolder.'/'.$kit_item['image_path']) : '';
						}else{
							$consolidate_kitchen_row->kit_o[$key]['khmer_recipe_image'] =(!empty($khmer_image) && file_exists('assets/language/'.$khmer_image))? (base_url().'assets/language/'.$khmer_image) : '';
						}

						// $consolidate_kitchen_row->kit_o[$key]['khmer_recipe_image'] = (!empty($khmer_image) && file_exists('assets/language/'.$khmer_image))? (base_url().'assets/language/'.$khmer_image) : '';	
						$consolidate_kitchen_row->kit_o[$key]['quantity'] = $kit_item['quantity'];
						$consolidate_kitchen_row->kit_o[$key]['get_item_name'] = $get_item->name;
						$consolidate_kitchen_row->kit_o[$key]['total_get_quantity'] = $get_item->total_get_quantity;
						$addondetails ='';
						if($kit_item['addon_id'] !='null'){
							$addondetails = $this->site->getAddonByRecipeidAndOrderitemid_kot($kit_item['recipe_id'],$kit_item['id']);
						}
						$consolidate_kitchen_row->kit_o[$key]['addons'] = $addondetails;								
					}
				}
				$consolid_kit['kitchens'][] = $consolidate_kitchen_row;
			}					
		}else{
			$consolid_kit[] = '';	
		}
}				
/*Consolidate kot for table area wise */					
/**/				
				$kitchen_details = $this->db->select('restaurant_kitchens.id,restaurant_kitchens.printer_id,  restaurant_kitchens.name')->get('restaurant_kitchens');
				
				if ($kitchen_details->num_rows() > 0) {					
					foreach (($kitchen_details->result()) as $kitchen_row) {
						$printers_details = $this->db->select('*')->where('id', $kitchen_row->printer_id)->get('printers');
						if ($printers_details->num_rows() > 0) {
							$kitchen_row->printers_details =  $printers_details->row();
						}
						foreach($items as $key => $kit_item){
							if($kitchen_row->id == $kit_item['kitchen_type_id']){
								
								$addons = $this->site->getAddonByRecipe($kit_item['recipe_id'], $kit_item['addon_id']);
								$get_item =  $this->site->getrecipeByID($kit_item['get_item']);
									
								$kitchen_row->kit_o[$key]['recipe_id'] = $kit_item['recipe_id'];
								
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

								$kitchen_row->kit_o[$key]['en_variant_name'] = $kit_item['variant'] ? $kit_item['variant'] :'';

								$kotimagefolder = date('Ymd');	
						     	$kitchen_row->kit_o[$key]['khmer_recipe_image'] = (!empty($kit_item['image_path']) && file_exists('assets/language/'.$kotimagefolder.'/'.$kit_item['image_path']))? (base_url().'assets/language/'.$kotimagefolder.'/'.$kit_item['image_path']) : '';

							  if($this->pos_settings->kot_print_lang_option ==1){
									/* $kotimagefolder = date('Ymd');	
									$kitchen_row->kit_o[$key]['khmer_recipe_image'] = (!empty($kit_item['image_path']) && file_exists('assets/language/'.$kotimagefolder.'/'.$kit_item['image_path']))? (base_url().'assets/language/'.$kotimagefolder.'/'.$kit_item['image_path']) : ''; */
									//comment on 03-02-2020
									$kitchen_row->kit_o[$key]['khmer_recipe_image'] =(!empty($khmer_image) && file_exists('assets/language/'.$khmer_image))? (base_url().'assets/language/'.$khmer_image) : '';
								}else{
									
									$kitchen_row->kit_o[$key]['khmer_recipe_image'] =(!empty($khmer_image) && file_exists('assets/language/'.$khmer_image))? (base_url().'assets/language/'.$khmer_image) : '';
								}
								$code=$this->site->get_recipeCodeById($kit_item['recipe_id']);
						        $kitchen_row->kit_o[$key]['code'] =substr($code,0, 4);
								$kitchen_row->kit_o[$key]['en_recipe_name'] = $kit_item['recipe_name'];
								$kitchen_row->kit_o[$key]['comment'] = $kit_item['comment'];
								$kitchen_row->kit_o[$key]['quantity'] = $kit_item['quantity'];								
								$kitchen_row->kit_o[$key]['get_item_name'] = $get_item->name;
								$kitchen_row->kit_o[$key]['total_get_quantity'] = $get_item->total_get_quantity;
								$addondetails ='';
								if($kit_item['addon_id'] !='null'){	
									$addondetails = $this->site->getAddonByRecipeidAndOrderitemid_kot($kit_item['recipe_id'],$kit_item['id']);
								}
								$kitchen_row->kit_o[$key]['addons'] = $addondetails;

								$unwanted_ingredientsdetails ='';
								if($kit_item['unwanted_ingredients'] !=0){					
									$unwanted_ingredientsdetails = $this->site->getunwanted_ingredientfor_kot($kit_item['recipe_id'],$kit_item['id'],$kit_item['unwanted_ingredients']);
								}                                
								$kitchen_row->kit_o[$key]['unwanted_ingredients'] = $unwanted_ingredientsdetails;

								//$kitchen_row->kit_o[$kitchen_row->id][] = $kit_item['quantity'];
							}
						}
						$kit['kitchens'][] = $kitchen_row;
					}
					
				}else{
					$kit[] = '';	
				}			
			/*	echo "<pre>";	
				print_r($kit);die;*/
				return array('kitchen_data' => $kit, 'consolid_kitchen_data' => $consolid_kit);
    }

    function kitchen_kot_print_copy($order_id,$orderItemIDs,$kitchen_id){
	$this->db->select('orders.*, restaurant_tables.name AS table_name');
				$this->db->join('restaurant_tables', 'restaurant_tables.id = orders.table_id', 'left');		
			   $this->db->where('orders.id', $order_id);
				$t = $this->db->get('orders');
				$kit = array();
				$consolid_kit = array();
                $items = array();
				if ($t->num_rows() > 0) {
					$orders_details =  $t->row();
					$orders_details->is_print_copy = true;
					$kit['orders_details'] = $orders_details;
					$consolid_kit['orders_details'] = $orders_details;
                                        
					$this->db->where('order_item_cancel_status',0);
					$this->db->where('sale_id',$order_id);
					$this->db->where_in('order_items.id',$orderItemIDs);
					$this->db->join('restaurant_kitchens','restaurant_kitchens.id=order_items.kitchen_type_id');
					$q = $this->db->get('order_items');
					$items = $q->result_array();
				}			
				
if($this->pos_settings->consolidated_kot_print_option == 0){/*Consolidate kot for table area wise */ 
      if($this->pos_settings->consolidated_kot_print != 0){
				$table_id = $consolid_kit['orders_details']->table_id;
				$consolid_kot_print_details = $this->db->select("printers.*")
				->join('restaurant_areas', 'restaurant_areas.printer_id = printers.id','left')
				->join('restaurant_tables', 'restaurant_tables.area_id = restaurant_areas.id','left')							
				->where('restaurant_tables.id', $table_id)
				->where('printers.id', $this->pos_settings->consolidated_kot_print)
				->get('printers');

				if ($consolid_kot_print_details->num_rows() > 0) {
				  $consolid_kit['consolid_kot_print_details'][] =  $consolid_kot_print_details->row();
			     }else{
			     	$consolid_kit['consolid_kot_print_details'] =array();
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
							$consolid_kit_item[$key]['en_variant_name'] = $kit_item['variant'] ? $kit_item['variant'] :'';

						   /*$kotimagefolder = date('Ymd');	
							$consolid_kit_item[$key]['khmer_recipe_image'] = (!empty($kit_item['image_path']) && file_exists('assets/language/'.$kotimagefolder.'/'.$kit_item['image_path']))? (base_url().'assets/language/'.$kotimagefolder.'/'.$kit_item['image_path']) : '';*/

							if($this->pos_settings->kot_print_lang_option ==1){
							/* 	$kotimagefolder = date('Ymd');	
								$consolid_kit_item[$key]['khmer_recipe_image'] = (!empty($kit_item['image_path']) && file_exists('assets/language/'.$kotimagefolder.'/'.$kit_item['image_path']))? (base_url().'assets/language/'.$kotimagefolder.'/'.$kit_item['image_path']) : ''; */
								//commands on 03-02-220
							}else{
								$consolid_kit_item[$key]['khmer_recipe_image'] =(!empty($khmer_image) && file_exists('assets/language/'.$khmer_image))? (base_url().'assets/language/'.$khmer_image) : '';
							}
							
							$consolid_kit_item[$key]['en_recipe_name'] = $kit_item['recipe_name'];
							$consolid_kit_item[$key]['comment'] = $kit_item['comment'];												
							$consolid_kit_item[$key]['quantity'] = $kit_item['quantity'];
							$consolid_kit_item[$key]['get_item_name'] = $get_item->name;
							$consolid_kit_item[$key]['total_get_quantity'] = $get_item->total_get_quantity;
							$addondetails ='';
							if($kit_item['addon_id'] !='null'){	
								$addondetails = $this->site->getAddonByRecipeidAndOrderitemid_kot($kit_item['recipe_id'],$kit_item['id']);
							}
							$consolid_kit_item[$key]['addons'] = $addondetails;
					}						
						$consolid_kit['consolid_kitchens'] = $consolid_kit_item;
				    }
				else
				{
					$consolid_kit['consolid_kitchens'] = array();
				}	
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

							if($this->pos_settings->kot_print_lang_option ==1){
								$kotimagefolder = date('Ymd');	
								$consolidate_kitchen_row->kit_o[$key]['khmer_recipe_image'] = (!empty($kit_item['image_path']) && file_exists('assets/language/'.$kotimagefolder.'/'.$kit_item['image_path']))? (base_url().'assets/language/'.$kotimagefolder.'/'.$kit_item['image_path']) : '';
							}else{
								$consolidate_kitchen_row->kit_o[$key]['khmer_recipe_image'] =(!empty($khmer_image) && file_exists('assets/language/'.$khmer_image))? (base_url().'assets/language/'.$khmer_image) : '';
							}							
							$consolidate_kitchen_row->kit_o[$key]['quantity'] = $kit_item['quantity'];
							$consolidate_kitchen_row->kit_o[$key]['get_item_name'] = $get_item->name;
							$consolidate_kitchen_row->kit_o[$key]['total_get_quantity'] = $get_item->total_get_quantity;	
							$addondetails ='';
							if($kit_item['addon_id'] !='null'){	
								$addondetails = $this->site->getAddonByRecipeidAndOrderitemid_kot($kit_item['recipe_id'],$kit_item['order_item_id']);
							}
							$consolidate_kitchen_row->kit_o[$key]['addons'] = $addondetails;								
						}
					}
					$consolid_kit['kitchens'][] = $consolidate_kitchen_row;
				}					
			}else{
				$consolid_kit[] = '';	
			}

}				
/**/				
				$kitchen_details = $this->db->select('restaurant_kitchens.id,restaurant_kitchens.printer_id,  restaurant_kitchens.name')->where('id',$kitchen_id)->get('restaurant_kitchens');
				
				if ($kitchen_details->num_rows() > 0) {					
					foreach (($kitchen_details->result()) as $kitchen_row) {						
						$printers_details = $this->db->select('*')->where('id', $kitchen_row->printer_id)->get('printers');					
						if ($printers_details->num_rows() > 0) {
							$kitchen_row->printers_details =  $printers_details->row();							
						}						
						foreach($items as $key => $kit_item){
							if($kitchen_row->id == $kit_item['kitchen_type_id']){
								
								$addons = $this->site->getAddonByRecipe($kit_item['recipe_id'], $kit_item['addon_id']);
								$get_item =  $this->site->getrecipeByID($kit_item['get_item']);
									
								$kitchen_row->kit_o[$key]['recipe_id'] = $kit_item['recipe_id'];
								
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
								$kitchen_row->kit_o[$key]['en_variant_name'] = $kit_item['variant'] ? $kit_item['variant'] :'';
								/*$kotimagefolder = date('Ymd');	
								$kitchen_row->kit_o[$key]['khmer_recipe_image'] = (!empty($kit_item['image_path']) && file_exists('assets/language/'.$kotimagefolder.'/'.$kit_item['image_path']))? (base_url().'assets/language/'.$kotimagefolder.'/'.$kit_item['image_path']) : '';*/
								if($this->pos_settings->kot_print_lang_option ==1){
									$kotimagefolder = date('Ymd');	
									$kitchen_row->kit_o[$key]['en_recipe_name'] = (!empty($kit_item['image_path']) && file_exists('assets/language/'.$kotimagefolder.'/'.$kit_item['image_path']))? (base_url().'assets/language/'.$kotimagefolder.'/'.$kit_item['image_path']) : '';
								}else{
									$kitchen_row->kit_o[$key]['en_recipe_name'] =(!empty($khmer_image) && file_exists('assets/language/'.$khmer_image))? (base_url().'assets/language/'.$khmer_image) : '';
								}                           	

								$kitchen_row->kit_o[$key]['en_recipe_name'] = $kit_item['recipe_name'];
								$kitchen_row->kit_o[$key]['comment'] = $kit_item['comment'];
								// $kitchen_row->kit_o[$key]['khmer_recipe_image'] = (!empty($khmer_image) && file_exists('assets/language/'.$khmer_image)) ? (base_url().'assets/language/'.$khmer_image) : '';							
								$kitchen_row->kit_o[$key]['quantity'] = $kit_item['quantity'];
								$kitchen_row->kit_o[$key]['get_item_name'] = $get_item->name;
								$kitchen_row->kit_o[$key]['total_get_quantity'] = $get_item->total_get_quantity;
								$addondetails ='';
								if($kit_item['addon_id'] !='null'){	
									$addondetails = $this->site->getAddonByRecipeidAndOrderitemid_kot($kit_item['recipe_id'],$kit_item['id']);
								}
								$kitchen_row->kit_o[$key]['addons'] = $addondetails;								
								//$kitchen_row->kit_o[$kitchen_row->id][] = $kit_item['quantity'];
							}
						}
						$kit['kitchens'][] = $kitchen_row;
					}
					
				}else{
					$kit[] = '';	
				}
				return array('kitchen_data' => $kit, 'consolid_kitchen_data' => $consolid_kit);
    }
    function getOtherPrinters($id=0){
	$this->db->select();
	$this->db->from('printers');
	$this->db->where('id !=',$id);
	$q = $this->db->get();
	if($q->num_rows()>0){
	    return $q->result();
	}
	return false;
    }
    function addRoughTender($billid,$payment,$multi_currency,$updateCreditLimit,$BBQCon=false){
        foreach ($payment as $item) {
		    $item['customer_payment_type'] = $updateCreditLimit['customer_type'];
		    if($BBQCon){
			unset($item['exchange_enable']);
		    }
		    $this->db->insert('rough_tender_payments', $item);//print_R($this->db->error());
		    $pid = $this->db->insert_id();
        }
        foreach ($multi_currency as $currency) {
                $this->db->insert('rough_tender_sale_currency', $currency); 
        }
	return true;
    }
    function getNewBillCalculation($billID,$update=false){
	$this->db->select('SUM(net_unit_price) as total,count(*) as no_of_items');
	$this->db->where('bil_id',$billID);
	$q = $this->db->get('bil_items');
	if($q->num_rows()>0){
	    $res = $q->row();
	    $bill['total'] = $res->total;
	    $bill['total_discount'] = 0;
	    $tax = $res->total*(10/100);
	    $bill['total_tax'] = $tax;
	    $g_total = $res->total+$tax;
	    $bill['grand_total'] = $g_total;
	    $bill['round_total'] =$g_total;
	    $bill['total_pay'] = $g_total;
	    $bill['balance'] = 0;
	    $bill['total_items'] = $res->no_of_items;
	    $bill['paid'] = $g_total;
	    if($update){
		$this->db->where('id',$billID);
	        $this->db->update('bils',$bill);
	    }
	   
	   return $bill;
	}
    }
    function getBillItems($billID){
	$this->db->select('*');
	$this->db->where('bil_id',$billID);
	$q = $this->db->get('bil_items');
	if($q->num_rows()>0){
	    return $q->result();
	}
    }
    function delete_bill_item($id){
	$this->db->where('id',$id);
	$this->db->delete('bil_items');
    }
    public function getBillReports($start,$end,$bill_no,$warehouse_id,$table_whitelisted,$limit,$offset)
    {  
        $where ='';
        if($warehouse_id != 0)
        {
            $where .= "AND P.warehouse_id =".$warehouse_id."";
        }
	$tw = '';
        if($table_whitelisted){
	    if($table_whitelisted!='all'){
		 $tw .= " AND P.table_whitelisted = ".$table_whitelisted." ";
	    }
	}else{
		 $tw .= " AND P.table_whitelisted = 0 ";
        }
	
        if($bill_no)
        {
            $where .= "AND P.id =".$bill_no."";
        }
       
        //P.id,SUM(DISTINCT P.total-P.total_discount+CASE WHEN (P.tax_type= 1) THEN P.total_tax ELSE 0 END) as grand_total,P.round_total 
        $bill = "SELECT P.action,P.id,case when (P.table_whitelisted=0) then 'print' else 'dont print' end as print_type,P.bill_number,T.name as table_name,P.date,P.reference_no,P.total,P.total_discount,P.total_tax,P.grand_total,P.total_pay,P.balance,P.total_items
            FROM ". $this->db->dbprefix('bils') ." AS P
	    JOIN ". $this->db->dbprefix('sales') ." AS S ON S.id = P.sales_id
	    JOIN ". $this->db->dbprefix('orders') ." AS O ON O.split_id = S.sales_split_id
	    LEFT JOIN ". $this->db->dbprefix('restaurant_tables') ." AS T ON T.id = O.table_id
            JOIN ". $this->db->dbprefix('payments') ." AS PM ON PM.bill_id = P.id
            WHERE DATE(P.date) BETWEEN '".$start."' AND '".$end."' AND 
            P.payment_status ='Completed' ".$where." ".$tw."  
            GROUP BY P.id ORDER BY P.id ASC";
            
            $limit_q = " limit $offset,$limit";
            $t = $this->db->query($bill);
            if($limit!=0) $bill .=$limit_q;
            $b = $this->db->query($bill);
	    
	    
	    
	    // print //dont print - total
	    
	    $printquery = "SELECT SUM(P.grand_total) as grand_total
            FROM ". $this->db->dbprefix('bils') ." AS P
	    JOIN ". $this->db->dbprefix('sales') ." AS S ON S.id = P.sales_id
	    JOIN ". $this->db->dbprefix('orders') ." AS O ON O.split_id = S.sales_split_id
	    LEFT JOIN ". $this->db->dbprefix('restaurant_tables') ." AS T ON T.id = O.table_id
            JOIN ". $this->db->dbprefix('payments') ." AS PM ON PM.bill_id = P.id
            WHERE DATE(P.date) BETWEEN '".$start."' AND '".$end."' AND
            P.payment_status ='Completed' ".$where." AND P.table_whitelisted =0  ";
	    
	    $dontprintquery = "SELECT SUM(P.grand_total) as grand_total
            FROM ". $this->db->dbprefix('bils') ." AS P
	    JOIN ". $this->db->dbprefix('sales') ." AS S ON S.id = P.sales_id
	    JOIN ". $this->db->dbprefix('orders') ." AS O ON O.split_id = S.sales_split_id
	    LEFT JOIN ". $this->db->dbprefix('restaurant_tables') ." AS T ON T.id = O.table_id
            JOIN ". $this->db->dbprefix('payments') ." AS PM ON PM.bill_id = P.id
            WHERE DATE(P.date) BETWEEN '".$start."' AND '".$end."' AND
            P.payment_status ='Completed' ".$where." AND P.table_whitelisted =1  ";
            
	    $p = $this->db->query($printquery);
	    $p_total = $p->row('grand_total');
	    $dp = $this->db->query($dontprintquery);
	    $dp_total = $dp->row('grand_total');
        if ($b->num_rows() > 0) {
            $data = $b->result();
            return array('data'=>$data,'total'=>$t->num_rows(),'print_total'=>$this->sma->formatMoney($p_total),'dontprint_total'=>$this->sma->formatMoney($dp_total));
        }
        return FALSE;
    }
    
     function deleteDontPrintBill($bill_nos){
	
        foreach($bill_nos as $bill_no){
           
           
            $sale_id = false;
            $sales_split_id = false;
            $orders_id = false;
            $bbq_cover_id = false;
            $this->db->select()
            ->from('bils')
            ->where('bill_number',$bill_no);
            $bil_query= $this->db->get();
	    //print_R($bil_query->row());exit;
            if($bil_query->num_rows()>0){
		foreach($bil_query->result() as $k => $bill){
		    
		
		    $billid = $bill->id;
		    $sale_id = $bill->sales_id;
		    $sale_query = $this->db->get_where('sales',array('id'=>$sale_id),1);
		    if($sale_query->num_rows()>0){
			$sales_split_id = $sale_query->row('sales_split_id');
			$orders_query = $this->db->get_where('orders',array('split_id'=>$sales_split_id),1);
			if($orders_query->num_rows()>0){
			    $orders_id = $orders_query->row('id');
			    if($orders_query->row('order_type')==4){
				$bbq_query = $this->db->get_where('bbq',array('reference_no'=>$orders_query->row('split_id')),1);
				if($bbq_query->num_rows()>0){
				    $bbq_cover_id = $bbq_query->row('id');
				}
			    }
			}
		    }
		    
		    
		    if($billid){
			/////////////// using bill id from bils tables//////////////
			$bill->action = 2;
			$this->db->insert('archive_bils',$bill);
			
    		$this->db->where('id',$billid);
            $this->db->delete('bils');
			
			$bill_items = $this->db->get_where('bil_items',array('bil_id'=>$billid))->result();
			$this->db->insert_batch('archive_bil_items',$bill_items);
			$this->db->where('bil_id',$billid);
			$this->db->delete('bil_items');
			
			$payment_data = $this->db->get_where('payments',array('bill_id'=>$billid))->result();
			$this->db->insert_batch('archive_payments',$payment_data);
			$this->db->where('bill_id',$billid);
			$this->db->delete('payments');
			
			///rough tender
			$r_payment_data = $this->db->get_where('rough_tender_payments',array('bill_id'=>$billid))->result();
			$this->db->insert_batch('archive_rough_tender_payments',$r_payment_data);
			
			$r_sale_currency_data = $this->db->get_where('rough_tender_sale_currency',array('bil_id'=>$billid))->result();
			$this->db->insert_batch('archive_rough_tender_sale_currency',$r_sale_currency_data);
			
    		    $this->db->where('bill_id',$billid);
                $this->db->delete('rough_tender_payments');
    		    $this->db->where('bil_id',$billid);
                $this->db->delete('rough_tender_sale_currency');
			
			///rough tender - end
			if($sale_id){
			/////////////// using sale id from bils table //////////////
			
			    $sales_data = $this->db->get_where('sales',array('id'=>$sale_id))->result();
			    $this->db->insert_batch('archive_sales',$sales_data);
			
			    $sale_items_data = $this->db->get_where('sale_items',array('sale_id'=>$sale_id))->result();
			    $this->db->insert_batch('archive_sale_items',$sale_items_data);
			    
			    $sale_cur_data = $this->db->get_where('sale_currency',array('sale_id'=>$sale_id))->result();
			    $this->db->insert_batch('archive_sale_currency',$sale_cur_data);
			
			
			    $this->db->where('id',$sale_id);
			    $this->db->delete('sales');
			    
			    $this->db->where('sale_id',$sale_id);
			    $this->db->delete('sale_items');
			    
			    $this->db->where('sale_id',$sale_id);
			    $this->db->delete('sale_currency');
			    if($sales_split_id){
			    /////////////// using sale split id from sales table //////////////
			    
			    $orders_data = $this->db->get_where('orders',array('split_id'=>$sales_split_id))->result();
			    $this->db->insert_batch('archive_orders',$orders_data);
			    
			    $rts_data = $this->db->get_where('restaurant_table_sessions',array('split_id'=>$sales_split_id))->result();
			    $this->db->insert_batch('archive_restaurant_table_sessions',$rts_data);
			    
			    
				$this->db->where('split_id',$sales_split_id);
				$this->db->delete('orders');
				
				$this->db->where('split_id',$sales_split_id);
				$this->db->delete('restaurant_table_sessions');
				$order_ids = array();
				foreach($orders_data as $k => $o_data){
				    $order_id = $o_data->id;
				    array_push($order_ids,$order_id);
				}
				if(!empty($order_ids)){
				/////////////// using sale split id from sales table //////////////
				$this->db->where_in('sale_id',$order_ids);
				$kitchen_data = $this->db->get('kitchen_orders')->result();
				$this->db->insert_batch('archive_kitchen_orders',$kitchen_data);
				
				$this->db->where_in('sale_id',$order_ids);
				$order_items_data = $this->db->get('order_items')->result();
				$this->db->insert_batch('archive_order_items',$order_items_data);
				
				$this->db->where_in('order_id',$order_ids);
				$rto_data = $this->db->get('restaurant_table_orders')->result();
				$this->db->insert_batch('archive_restaurant_table_orders',$rto_data);
			    
			    
				    $this->db->where_in('sale_id',$order_ids);
				    $this->db->delete('kitchen_orders');
				    
				    $this->db->where_in('sale_id',$order_ids);
				    $this->db->delete('order_items');
				    
				    $this->db->where_in('order_id',$order_ids);
				    $this->db->delete('restaurant_table_orders');
				    
				    
				}
				if($bbq_cover_id){
					$bbq_data = $this->db->get_where('bbq',array('id'=>$bbq_cover_id))->result();
					$this->db->insert_batch('archive_bbq',$bbq_data);
					$bbq_items_data = $this->db->get_where('bbq_bil_items',array('bil_id'=>$billid))->result();
					$this->db->insert_batch('archive_bbq_bil_items',$bbq_items_data);
				
				
					$this->db->where('id',$bbq_cover_id);
					$this->db->delete('bbq');
					$this->db->where('bil_id',$billid);
					$this->db->delete('bbq_bil_items');
				}
			    }
			    
			}
		    }
		}
            }
        }
    }
    function change_toPrintBill($bill_no){
	$data['table_whitelisted'] = 0;
	$this->db->where(array('bill_number'=>$bill_no));
	$this->db->update('bils',$data);
	$this->db->select()
            ->from('bils')
            ->where('bill_number',$bill_no);
            $bil_query= $this->db->get();
	    if($bil_query->num_rows()>0){
		foreach($bil_query->result() as $k => $row){
		    $sales_id  = $row->sales_id;
		    $this->db->where('id',$sales_id);
		    $this->db->update('sales',$data);
		    
		    $sale_data = $this->db->get_where('sales',array('id'=>$sales_id))->row();
		    $this->db->where('split_id',$sale_data->sales_split_id);
		    $this->db->update('orders',$data);
		}
	    }
    }
    function get_dontprintBillData($bill_no){
	$this->db->select()
            ->from('bils')
            ->where('bill_number',$bill_no);
            $bill_query= $this->db->get();
	    if($bill_query->num_rows()>0){
		$bill = $bill_query->row();//echo '<pre>';print_R($bill);exit;
		$sales_id = $bill->sales_id;
		$bill_id = $bill->id;
		$bill->bill_items = $this->db->get_where('bil_items',array('bil_id'=>$bill_id))->result();
		///echo '<pre>';print_R($bill);
		return $bill;
	    }
	    return false;
    }
    function getOrderItemsDetails($order_itemid){
	$q = $this->db->get_where('order_items',array('id'=>$order_itemid))->row_array();
	return $q;
    }
    function edit_bill($bill_id,$sale_id,$bill_data,$sale_data,$bill_items,$order_items,$payment,$multi_currency){
	
	//echo '<pre>';print_R($order_items);exit;
	//*** bill //
	$ori_bill_data = $this->db->get_where('bils',array('id'=>$bill_id))->row();
	$ori_bill_data->action = 1;
	
	$this->db->insert('archive_bils',$ori_bill_data);
	
	$bill_data['action'] = 1;
	$this->db->where('id',$bill_id);
	$this->db->update('bils',$bill_data);
	
	//*** sales  //	
	$ori_sale_data = $this->db->get_where('sales',array('id'=>$sale_id))->row();	
	$this->db->insert('archive_sales',$ori_sale_data);
	
	$this->db->where('id',$sale_id);
	$this->db->update('sales',$sale_data);
	
	//*** bill_items  //
	$ori_bill_items_data = $this->db->get_where('bil_items',array('bil_id'=>$bill_id))->result();
	$this->db->insert_batch('archive_bil_items',$ori_bill_items_data);
	
	$this->db->where('bil_id',$bill_id);
	$this->db->delete('bil_items');	
	$this->db->insert_batch('bil_items',$bill_items);
	
	//*** order_items_items  //
	$split_id = $ori_sale_data->sales_split_id;
	$all_orders = $this->db->get_where('orders',array('split_id'=>$split_id))->result();
	$all_order_ids  = array();
	foreach($all_orders as $ao => $all_o){
	    array_push($all_order_ids,$all_o->id);
	}
	$orderIDS = array();
	$this->db->where_in('order_items',$all_order_ids);
	$archive_order_items = $this->db->get('order_items')->result();
	$this->db->insert('archive_order_items',$archive_order_items);
	foreach($order_items as $k => $row){
	   foreach($row as $kk =>$u_data){
	    if($kk=='update'){
		
		$this->db->where('id',$u_data['id']);
		$this->db->update('order_items',$u_data);
		array_push($orderIDS,$archive_order_items->sale_id);
	    }else{
		array_push($orderIDS,$u_data->sale_id);
		$this->db->insert('order_items',$u_data);
	    }
	   }
	}
	$del_order_ids = array_diff($all_order_ids,$orderIDS);
	if(!empty($all_order_ids)){
	    $this->db->where_in('id',$all_order_ids);
	    $archive_orders = $this->db->get('orders')->result();
	    $this->db->insert_batch('archive_orders',$archive_orders);
	    
	    $this->db->where_in('sale_id',$all_order_ids);
	    $archive_kit_orders = $this->db->get('kitchen_orders')->result();
	    $this->db->insert_batch('archive_kitchen_orders',$archive_kit_orders);
	    
	    $this->db->where_in('order_id',$all_order_ids);
	    $archive_rt_orders = $this->db->get('restaurant_table_orders')->result();
	    $this->db->insert_batch('archive_restaurant_table_orders',$archive_rt_orders);
	    
	    $this->db->where_in('order_id',$all_order_ids);
	    $archive_rts_orders = $this->db->get('restaurant_table_sessions')->result();
	    $this->db->insert_batch('archive_restaurant_table_sessions',$archive_rts_orders);
	    
	    // delete order ids
	    $this->db->where_in('id',$del_order_ids);
	    $this->db->delete('orders');
	    $this->db->where_in('sale_id',$del_order_ids);
	    $this->db->delete('kitchen_orders');
	    $this->db->where_in('order_id',$del_order_ids);
	    $this->db->delete('restaurant_table_orders');
	    $this->db->where_in('order_id',$del_order_ids);
	    $this->db->delete('restaurant_table_sessions');
	}
	
	//payment
	
	$archive_payment = $this->db->get_where('payments',array('bill_id'=>$bill_id))->result();
	
	$this->db->insert_batch('archive_payments',$archive_payment);
	foreach($payment as $p => $p_data){
	    if($p_data['amount']!=0){
		$pid = $p_data['payment_id'];
		unset($p_data['payment_id']);
		$this->db->where('id',$pid);
		$this->db->update('payments',$p_data);
		
	    }else{
		$this->db->where('id',$pid);
		$this->db->delete('payments');
	    }
	}
	//salecurrecy
	$archive_sale_currency = $this->db->get_where('sale_currency',array('bil_id'=>$bill_id))->result();
	$this->db->insert_batch('archive_sale_currency',$archive_sale_currency);
	$this->db->where('bil_id',$bill_id);
	$this->db->delete('sale_currency');
	foreach($multi_currency as $m => $mc_data){	    
	    $this->db->insert('sale_currency',$mc_data);
	}
	
	
    }
    function getOfferdiscount($id){
	$q = $this->db->get_where('discounts',array('id'=>$id))->row();
	return ($q->discount_type=='percentage_discount')?$q->discount.'%':$q->discount;
    }
    function getBillTax($id){
	$q = $this->db->get_where('tax_rates',array('id'=>$id))->row();
	return $q;
    }
    function get_BillPaymentData($bill_id){
	$q = $this->db->get_where('payments',array('bill_id'=>$bill_id))->result();
	return $q;
    }
    public function getArchived_BillReports($start,$end,$bill_no,$warehouse_id,$table_whitelisted,$limit,$offset)
    {  
        $where ='';
        if($warehouse_id != 0)
        {
            $where .= "AND P.warehouse_id =".$warehouse_id."";
        }
	$tw = '';
        if($table_whitelisted){
	    if($table_whitelisted!='all'){
		 $tw .= " AND P.table_whitelisted = ".$table_whitelisted." ";
	    }
	}else{
		 $tw .= " AND P.table_whitelisted = 0 ";
        }
	
        if($bill_no)
        {
            $where .= "AND P.id =".$bill_no."";
        }
       
        //P.id,SUM(DISTINCT P.total-P.total_discount+CASE WHEN (P.tax_type= 1) THEN P.total_tax ELSE 0 END) as grand_total,P.round_total 
        $bill = "SELECT P.action,P.id,P.bill_number,P.date,P.grand_total
            FROM ". $this->db->dbprefix('archive_bils') ." AS P
	   
            WHERE DATE(P.date) BETWEEN '".$start."' AND '".$end."'  ".$where."  
            GROUP BY P.id ORDER BY P.id ASC";
            
            $limit_q = " limit $offset,$limit";
            $t = $this->db->query($bill);
            if($limit!=0) $bill .=$limit_q;
            $b = $this->db->query($bill);
	    
	    
	   
            
	    
        if ($b->num_rows() > 0) {
            $data = $b->result();
            return array('data'=>$data,'total'=>$t->num_rows());
        }
        return FALSE;
    }
    
    function restore_deleted_bill($bill_id){
	
	/// get data
	$bill = $this->db->get_where('archive_bils',array('id'=>$bill_id))->row();
	$bill_items = $this->db->get_where('archive_bil_items',array('bil_id'=>$bill_id))->result();
	$payment = $this->db->get_where('archive_payments',array('bill_id'=>$bill_id))->result();
	$rt_payment = $this->db->get_where('archive_rough_tender_payments',array('bill_id'=>$bill_id))->result();
	
	$sale_id = $bill->sales_id;
	$sale = $this->db->get_where('archive_sales',array('id'=>$sale_id))->row();
	$sale_items = $this->db->get_where('archive_sale_items',array('sale_id'=>$bill_id))->result();
	$sale_currency = $this->db->get_where('archive_sale_currency',array('sale_id'=>$bill_id))->result();
	
	$split_id = $sale->sales_split_id;
	$orders = $this->db->get_where('archive_orders',array('split_id'=>$split_id))->result();
	$r_table_sessions = $this->db->get_where('archive_restaurant_table_sessions',array('split_id'=>$split_id))->result();
	
	$order_items  = array();
	$kitchen_orders  = array();
	$r_table_orders = array();
	$order_ids = array();
	foreach($orders as $k => $order){
	    $order_id = $order->id;
	    array_push($order_ids,$order_id);
	}
	$this->db->where_in('sale_id',$order_ids);
	$order_items = $this->db->get('archive_order_items')->result();
	
	$this->db->where_in('sale_id',$order_ids);
	$kitchen_orders = $this->db->get('archive_kitchen_orders')->result();
	
	$this->db->where_in('order_id',$order_ids);
	$r_table_orders = $this->db->get('archive_restaurant_table_orders')->result();
	
	if($bill->order_type=4){
	    $bbq_query = $this->db->get_where('bbq',array('reference_no'=>$orders[0]->split_id),1);
	    if($bbq_query->num_rows()>0){
		$bbq_cover_id = $bbq_query->row('id');		
		$bbq = $this->db->get_where('archive_bbq',array('id'=>$bbq_cover_id))->row();
		$bbq_bill_items = $this->db->get_where('archive_bbq_bil_items',array('bil_id'=>$bill_id))->result();
		
		
	    }
	}
	
	/// delete data
	$this->db->delete('archive_bils',array('id'=>$bill_id));
	$this->db->delete('archive_bil_items',array('bil_id'=>$bill_id));
	$this->db->delete('archive_payments',array('bill_id'=>$bill_id));
	$this->db->delete('archive_rough_tender_payments',array('bill_id'=>$bill_id));
	
	$this->db->delete('archive_sales',array('id'=>$sale_id));
	$this->db->delete('archive_sale_items',array('sale_id'=>$bill_id));
	$this->db->delete('archive_sale_currency',array('sale_id'=>$bill_id));	
	
	$this->db->delete('archive_orders',array('split_id'=>$split_id));
	$this->db->delete('archive_restaurant_table_sessions',array('split_id'=>$split_id));
	
	
	$this->db->where_in('sale_id',$order_ids);
	$this->db->delete('archive_order_items');
	
	$this->db->where_in('sale_id',$order_ids);
	$this->db->delete('archive_kitchen_orders');
	
	$this->db->where_in('order_id',$order_ids);
	$this->db->delete('archive_restaurant_table_orders');
	if($bbq_query->num_rows()>0){
	    $bbq_cover_id = $bbq_query->row('id');		
	    $this->db->delete('archive_bbq',array('id'=>$bbq_cover_id));
	    $this->db->delete('archive_bbq_bil_items',array('bil_id'=>$bill_id));
	}
	
	// Restore data
	$this->db->insert('bils',$bill);
	$this->db->insert_batch('bil_items',$bill_items);
	$this->db->insert_batch('payments',$payment);
	$this->db->insert_batch('rough_tender_payments',$rt_payment);
	
	$this->db->insert('sales',$sale);
	$this->db->insert_batch('sale_items',$sale_items);
	$this->db->insert_batch('sale_currency',$sale_currency);	
	
	$this->db->insert_batch('orders',$orders);
	$this->db->insert_batch('restaurant_table_sessions',$r_table_sessions);
	
	$this->db->insert_batch('order_items',$order_items);
	$this->db->insert_batch('kitchen_orders',$kitchen_orders);		
	$this->db->insert_batch('restaurant_table_orders',$r_table_orders);
	if($bbq_query->num_rows()>0){
	    $bbq_cover_id = $bbq_query->row('id');		
	    $this->db->insert('bbq',$bbq);
	    $this->db->insert_batch('bbq_bil_items',$bbq_bill_items);
	}
	
    }

    public function getItemSubCategories($parent_id) {
        $this->db->select('id as id, name as text')
        ->where('parent_id', $parent_id)->order_by('name');
        $q = $this->db->get("recipe_categories");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    public function Payment_for_consolidate($update_bill = array(), $bill_id =NULL, $payment = array(), $multi_currency = array(), $salesid = NULL, $sales_bill = array(), $order_split_id = NULL, $notification_array,$updateCreditLimit,$total,$customer_id,$loyalty_used_points,$taxation,$customer_changed)
    {    
    	
		$q = $this->db->select('sales_split_id, sales_table_id')->where('id', $salesid)->get('sales');
		///bill number generation block
		$exixts_bills=$this->site->checkbill_exist($taxation);
        if(!empty($exixts_bills)){
		if($this->pos_settings->bill_series_settings == 1){
			$bill_number = $this->site->Payment_dine_bill_number($taxation,$bill_id);		
			$bill_no = $bill_number;
			$this->db->update('bils', array('bill_number' => $bill_number), array('id' => $bill_id));
		}else{
			$bill_number = $this->site->generate_bill_number($taxation,$bill_id);	
			$this->db->update('bils', array('bill_number' => $bill_number), array('id' => $bill_id));
		}
		   $bill_no = $bill_number;		
		   $this->site->latest_bill_new($bill_no,$taxation);
      }else{
		  $last_bill=$this->site->get_lastbill($taxation);
		  if($taxation ==0){
			$bill_no=  $last_bill->bill_number+1;
		  }else{
			  $last_bill=$this->site->filter_number($last_bill->bill_number);
			  $bill_no=!empty($last_bill)?'TW-00'.($last_bill+1):'TW-001';
		  }
		     $update_bill['bill_number']=$bill_no;
		   $this->site->latest_bill_new($bill_no,$taxation);
	  }
	  
	  
	  
	  /// bill number generation block end
		// var_dump($bill_no);die;
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
		$notification_array['insert_array']['msg'] = 'Cashier has been payment this bil ('.$bill_no.'';
		$notification_array['insert_array']['table_id'] = $table_id;
		$notification_array['insert_array']['type'] = 'Cashier payment bils';
		$notification_array['insert_array']['role_id'] = WAITER;
		$notification_array['insert_array']['to_user_id'] = $waiter_id;	
		$this->site->create_notification($notification_array);
		
    	if ($this->db->update('bils', $update_bill, array('id' => $bill_id))){
			$this->db->update('sales', $sales_bill, array('id' => $salesid));
    			$order_count = $this->db->get_where('bils', array('sales_id' => $salesid));
				$order_count =$order_count->num_rows();
				
				 $order_closed_count = $this->db->get_where('bils', array('bils.sales_id' => $salesid,'bils.payment_status' => 'Completed'));
				 $order_closed_count =$order_closed_count->num_rows();

    		foreach ($payment as $item) {
		    $item['customer_payment_type'] = $updateCreditLimit['customer_type'];
		    $this->db->insert('payments', $item);
		    $pid = $this->db->insert_id();
		    
		    if($pid && $item['paid_by']=='credit'){
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
		    }
    		
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
		        	$this->site->UpdateCustomerFromLoyalty($customer_id,$bill_id,$salesid,$split_id);	       
		        }

		/*Loyalty inser and Update*/	
		 $this->site->LoyaltyinserAndUpdate($bill_id,$total,$customer_id,$loyalty_used_points);	  
		 $this->site->update_TableStatus_after_payment_for_consolidate($table_id,0,$salesid,$split_id);
		 // print_r($this->db->error());die;
    	 return true;
    	}    	
    	// print_r($this->db->error());die;
    	return false;
    }   
     public function merger_multiple_to_single_split_consolidate($merge_splits, $current_split,$merge_table_id ){
        $order_array = array(
            'table_id' => $merge_table_id,
            'split_id' => $current_split,
        );
		$this->db->select("table_id");
		$this->db->where_in('split_id', $merge_splits);
		$q = $this->db->get('orders'); 
		if($q->num_rows()>0){
			$merge_tables_from=$q->result();
		}
		$stack = $merge_splits;
		array_push($stack, $current_split);
		//unknow block start past code move here///
		$this->db->select("SUM(srampos_order_items.quantity) as quantity,order_items.recipe_id,order_items.unit_price", FALSE);
		$this->db->join('orders', 'orders.id = order_items.sale_id');
		$this->db->where_in('split_id', $stack);		
		$this->db->where('order_items.order_item_cancel_status', 0);		
		$this->db->group_by('CASE WHEN srampos_order_items.manual_item_discount >= 0.00 THEN ' . $this->db->dbprefix('order_items') . '.recipe_id END HAVING COUNT(*) > 1',FALSE);
		$ms = $this->db->get('order_items'); 
	  if ($ms->num_rows() > 0) {  	
            foreach (($ms->result()) as $row) {
					$this->db->select("order_items.id", FALSE);
					$this->db->join('orders', 'orders.id = order_items.sale_id');
					$this->db->where('orders.split_id', $current_split);	
					$this->db->where('order_items.recipe_id', $row->recipe_id);				
					$cs = $this->db->get('order_items');
            		//$this->db->update('order_items', array('quantity' => $row->quantity,'subtotal' => $row->unit_price*($row->quantity)), array('id' => $cs->row('id')));

            		$this->db->select("order_items.id", FALSE);
					$this->db->join('orders', 'orders.id = order_items.sale_id');
					$this->db->where_in('split_id', $merge_splits);		
					$this->db->where('recipe_id', $row->recipe_id);		
					//$this->db->group_by('order_items.id,CASE WHEN srampos_order_items.manual_item_discount != 0.00 THEN ' . $this->db->dbprefix('order_items') . '.recipe_id END',FALSE);		
					$cancelssplit = $this->db->get('order_items');
//print_r($this->db->last_query());die;
					//$this->db->update('order_items', array('order_item_cancel_id' => $this->session->userdata('user_id'),'order_item_cancel_note' => 'Cancelled','item_status' => 'Cancel','order_item_cancel_status' => 1), array('id' => $cancelssplit->row('id')));
            }
        }        
        ////unknow block end   ///
/*table status changed if only one orderd placed*/
     
 $this->db->select("table_id", FALSE);		
		$this->db->where_in('split_id', $merge_splits);	
		$this->db->where_not_in('table_id', $merge_table_id);	
		$this->db->group_by('table_id HAVING COUNT(*) >= 1',FALSE);		
		$table = $this->db->get('orders'); 	
	        if ($table->num_rows() > 0) {
	            foreach (($table->result()) as $row) {
	            	$this->db->update('restaurant_tables', array('current_order_status' => 1,'current_order_user' => 0), array('id' => $row->table_id));
	            
	            }
	        }		
/*table status changed if only one orderd placed*/

        $this->db->where_in('split_id', $merge_splits);
        if ($this->db->update('orders',  $order_array)) {
			if(!empty($merge_tables_from)){
			$this->merge_table_status_update($merge_tables_from);
			}
			$orders_id = "SELECT O.id 
			FROM ".$this->db->dbprefix('orders')." AS O
			WHERE O.split_id='".$current_split."' ";
			$q = $this->db->query($orders_id);			
	        if ($q->num_rows() > 0) {
	            foreach (($q->result()) as $row) {
	            	$this->db->update('restaurant_table_orders', array('table_id' => $merge_table_id), array('order_id' => $row->id));
	            	$this->db->update('restaurant_table_sessions', array('table_id' => $merge_table_id), array('order_id' => $row->id));	                
	            }
	            return TRUE;
	        }		
			return true;
		}
		return false;
    }
  
  
	function merge_table_status_update($merge_tables_from){
			if(!empty($merge_tables_from)){
				foreach($merge_tables_from as $table){
				   $this->db->select("table_id")	;
				   $this->db->where("table_id",$table->table_id);
				   //$this->db->where_not_in("payment_status",array('Cancelled','Paid'));
				   $this->db->where("payment_status IS NULL");
				   $q=$this->db->get("orders");
				    if(empty($q->result())){
					   $this->db->where("id",$table->table_id);
					   $this->db->update("restaurant_tables",array("current_order_status"=>0));
				   } 
				}
			}	
	}
	
	
public function ALLCancelOrdersItem_consolidate($cancel_remarks, $split_table_id, $user_id, $notification_array){

    
		$k = $this->db->select('id,GROUP_CONCAT(id SEPARATOR ",") as ordersid ')->where('split_id', $split_table_id)->get('orders');
		if ($k->num_rows() > 0) {
            $id =  $k->row('ordersid');
        }

        $bb = $this->db->select('split_id')->where('reference_no', $split_table_id)->get('bbq');
		if ($k->num_rows() > 0) {
		        $bbqupdate = array(
					'cancel_status' => 1,
					'cancel_msg' => 'BBQ Covers Cancel',
					'status' => 'Cancelled',
					'payment_status' => 'Cancelled',
					'cancel_by' => $this->session->userdata('user_id')
				);
				$this->db->where('reference_no', $split_table_id);
				$this->db->update('bbq', $bbqupdate);
		}
		
		$k = $this->db->select('waiter_id, chef_id')->where('sale_id', $id)->get('kitchen_orders');
		if ($k->num_rows() > 0) {
            $waiter_id =  $k->row('waiter_id');
			$chef_id =  $k->row('chef_id');
        }
		
		/*$notification_array['insert_array']['msg'] = 'Cashier has benn cancel this bil ('.$split_id.')';
		$notification_array['insert_array']['table_id'] = $table_id;
		$notification_array['insert_array']['type'] = 'Cashier cancel bils';
		$notification_array['insert_array']['role_id'] = WAITER;
		$notification_array['insert_array']['to_user_id'] = $waiter_id;*/
		
		
		
		//$this->site->create_notification($notification_array);
        /*echo $id;die;*/
       
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
 	
		//$this->db->update('bils', $bill_array, array('sales_id' => $sale_id));
		/*$this->db->where_in('split_id', $split_id);
		$this->db->update('orders',  $order_array);*/
	    
		$id2 =   explode(',',$id);
		$this->db->where_in('sale_id', $id2);
		$this->db->update('order_items',  $order_item_array);
         $order_data=$this->db->get_where("orders",array("split_id"=>$split_table_id))->row();
		
		 $table_id=$order_data->table_id;
		/*table status changed if only one orderd placed*/
	       
	   /*table status changed if only one orderd placed*/
	   
			
		$this->db->where('split_id', $split_table_id);
		if ($this->db->update('orders',  $order_array)) {
		//	print_r($this->db->last_query());die;
		 $this->db->select("table_id", FALSE);		
			$this->db->where('table_id', $table_id);	
             $order_status=array('Cancelled','Paid');
			//$this->db->where_not_in('payment_status', $order_status);						
			$this->db->where('payment_status is null');
			$table = $this->db->get('orders'); 	
			
		        if ($table->num_rows() > 0) {
		            
		        }	else{
					$this->db->update('restaurant_tables', array('current_order_status' => 0,'current_order_user' => 0), array('id' => $table_id));
				}
			return true;
		}
		//print_r($this->db->last_query());die;
		return false;

    }    
	public function change_table_consolidate($change_split_id, $changed_table_id){

        $order_array = array(
            'table_id' => $changed_table_id,
        );
        $orders = $this->db->get_where('orders',array('split_id'=>$change_split_id))->row();
		$old_table_id=$orders->table_id;
       // $this->site->updateTableStatus($old_table_id,0,$this->session->userdata('user_id'));
	   $this->db->where("order_id",$orders->id);
	   $this->db->update("restaurant_table_sessions",array("table_id"=>$changed_table_id));
        $this->site->updateTableStatus($changed_table_id,1,$this->session->userdata('user_id'));
        $this->db->where('split_id', $change_split_id);
        if ($this->db->update('orders',$order_array)) {
			$this->db->where("order_id",$orders->id);
			$this->db->update("restaurant_table_orders",$order_array);
			/* $orders_id = "SELECT O.id 
			FROM ".$this->db->dbprefix('orders')." AS O
			WHERE O.split_id='".$change_split_id."' ";

			$q = $this->db->query($orders_id);	 */	
			$this->db->select("table_id", FALSE);		
			$this->db->where('table_id', $old_table_id);	
             $order_status=array('Cancelled','Paid');
			//$this->db->where_not_in('payment_status', $order_status);						
			$this->db->where('payment_status is null');
			$table = $this->db->get('orders'); 	
	        if ($table->num_rows() > 0) {
		        }	else{
					$this->db->update('restaurant_tables', array('current_order_status' => 0,'current_order_user' => 0), array('id' => $old_table_id));
				}
	            return TRUE;
		}
		return false;
    }
	public function CancelSale_consolidate($cancel_remarks, $sale_id, $user_id, $notification_array){

    	$q = $this->db->select('sales_split_id, sales_table_id')->where('id', $sale_id)->get('sales');
		if ($q->num_rows() > 0) {
            $split_id =  $q->row('sales_split_id');
			$table_id =  $q->row('sales_table_id');
        }
        /*echo $split_id;*/
		
		
		$k = $this->db->select('id,GROUP_CONCAT(id SEPARATOR ",") as ordersid ')->where('split_id', $split_id)->get('orders');
		if ($k->num_rows() > 0) {
            $id =  $k->row('ordersid');
        }

        $bb = $this->db->select('split_id')->where('reference_no', $split_id)->get('bbq');
		if ($k->num_rows() > 0) {
		        $bbqupdate = array(
					'cancel_status' => 1,
					'cancel_msg' => 'BBQ Covers Cancel',
					'status' => 'Cancelled',
					'payment_status' => 'Cancelled',
					'cancel_by' => $this->session->userdata('user_id')
				);
				$this->db->where('reference_no', $split_id);
				$this->db->update('bbq', $bbqupdate);
		}
		// print_r($this->db->error());die;
		$k = $this->db->select('waiter_id, chef_id')->where('sale_id', $id)->get('kitchen_orders');
		if ($k->num_rows() > 0) {
            $waiter_id =  $k->row('waiter_id');
			$chef_id =  $k->row('chef_id');
        }
		
		$notification_array['insert_array']['msg'] = 'Cashier has benn cancel this bil ('.$split_id.')';
		$notification_array['insert_array']['table_id'] = $table_id;
		$notification_array['insert_array']['type'] = 'Cashier cancel bils';
		 $notification_array['insert_array']['role_id'] = WAITER;
		 $notification_array['insert_array']['to_user_id'] = $waiter_id;
		
		
		
		$this->site->create_notification($notification_array);
        /*echo $id;die;*/
        $sale_aray = array(
            'canceled_user_id' => $user_id,
            'cancel_remarks' => $cancel_remarks,
            'cancel_status' => 1,
            'payment_status' => 'Cancelled',
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

		$this->db->update('bils', $bill_array, array('sales_id' => $sale_id));
		$this->db->where_in('split_id', $split_id);
		$this->db->update('orders',  $order_array);
	    /*$this->db->update('orders', $order_array, array('id' => $id));*/

		// $this->db->update('order_items', $order_item_array, array('sale_id' => $id));
		
		$id2 =   explode(',',$id);
		$this->db->where_in('sale_id', $id2);
		$this->db->update('order_items',  $order_item_array);

			
		$this->db->where('id', $sale_id);
		if ($this->db->update('sales',  $sale_aray)) {
			// print_r($this->db->last_query());die;
			/*if(!empty($id)){

			}*/
			
		/* 	if($table_id != 0){
				$this->site->updatepayment_TableStatus($table_id,0);
			} */
				
			$this->db->select("table_id", FALSE);		
			$this->db->where('table_id', $table_id);	
             $order_status=array('Cancelled','Paid');
			//$this->db->where_not_in('payment_status', $order_status);						
			$this->db->where('payment_status is null');
			$table = $this->db->get('orders'); 	
	        if ($table->num_rows() > 0) {
		        }	else{
					$this->db->update('restaurant_tables', array('current_order_status' => 0,'current_order_user' => 0), array('id' => $table_id));
				}
	            return TRUE;
		}
		return false;

    }
	public function getBill_all($table_id, $split_id, $user_id,$bill_type){
		$this->db->select('GROUP_CONCAT(' . $this->db->dbprefix('orders') . '.id SEPARATOR ",") as "order",GROUP_CONCAT(' . $this->db->dbprefix('kitchen_orders') . '.id SEPARATOR ",") as kitchen, "order_item" AS order_item');
		$this->db->join('kitchen_orders', 'kitchen_orders.sale_id = orders.id');
		if(!empty($table_id)){
			$this->db->where('orders.table_id', $table_id);
		}
		$this->db->where('orders.split_id', $split_id);
		$this->db->where('orders.order_cancel_status', 0);		
		$q = $this->db->get('orders');
		// print_r($this->db->last_query());die;
		if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {

            	$order =  $q->row('order');
				$order =   explode(',',$order);

				$kitchen =  $q->row('kitchen');
				$kitchen =   explode(',',$kitchen);
		
				if($bill_type !=3){
			  $this->db->select("order_items.id,order_items.recipe_id, order_items.recipe_code, order_items.recipe_name, order_items.recipe_type, order_items.option_id, order_items.net_unit_price, order_items.unit_price,SUM(srampos_order_items.quantity) quantity , order_items.warehouse_id, order_items.item_tax, order_items.tax_rate_id, order_items.tax, order_items.discount, order_items.item_discount, SUM(srampos_order_items.subtotal) subtotal, order_items.serial_no, order_items.real_unit_price, order_items.sale_item_id, order_items.recipe_unit_id, order_items.recipe_unit_code, order_items.unit_quantity, order_items.comment, order_items.gst, order_items.cgst, order_items.sgst, order_items.igst,order_items.recipe_variant_id,order_items.variant,order_items.manual_item_discount,order_items.manual_item_discount_val,order_items.addon_id,order_items.addon_qty,order_items.item_status", FALSE);
					}else{
		  $this->db->select("order_items.id,order_items.recipe_id, order_items.recipe_code, order_items.recipe_name, order_items.recipe_type, order_items.option_id, order_items.net_unit_price, order_items.unit_price,srampos_order_items.quantity quantity , order_items.warehouse_id, order_items.item_tax, order_items.tax_rate_id, order_items.tax, order_items.discount, order_items.item_discount, srampos_order_items.subtotal subtotal, order_items.serial_no, order_items.real_unit_price, order_items.sale_item_id, order_items.recipe_unit_id, order_items.recipe_unit_code, order_items.unit_quantity, order_items.comment, order_items.gst, order_items.cgst, order_items.sgst, order_items.igst,order_items.recipe_variant_id,order_items.variant,order_items.manual_item_discount,order_items.manual_item_discount_val,order_items.addon_id,order_items.addon_qty,order_items.item_status", FALSE);
						}
			  $this->db->order_by('order_items.recipe_name', 'ASC');
			  
				$this->db->where_in('order_items.sale_id', $order);
				$this->db->where_in('order_items.kitchen_id', $kitchen);
				$this->db->where('order_items.order_item_cancel_status', 0);
				if($bill_type !=3){
				$this->db->group_by('CASE WHEN srampos_order_items.manual_item_discount >= 0.00 THEN ' . $this->db->dbprefix('order_items') . '.recipe_id END,srampos_order_items.recipe_variant_id',FALSE);
				$this->db->group_by('srampos_order_items.parent_order_item_id');
				}
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
			
			$this->db->select("orders.*");
			if(!empty($table_id)){
				$this->db->where('orders.table_id', $table_id);
			}
			$this->db->where('orders.split_id', $split_id);
			$this->db->where('orders.order_cancel_status', 0);			
			$this->db->group_by('orders.split_id');			
			$o = $this->db->get('orders');
			foreach (($o->result()) as $result) {
				$data['order'][] = $result;
				
			}
            return $data;
        }
        return FALSE;
	}
 public function InsertBill_all($order_data = array(), $order_item = array(), $billData = array(), $splitData = array(), $sales_total = NULL, $delivery_person = NULL,$timelog_array = NULL, $notification_array = array(),$order_item_id =array(), $split_id, $request_discount, $dine_in_discount,$birthday){	
        $total_bills=!empty(count($billData))?count($billData):1;
		if(empty($dine_in_discount)){
			foreach ($request_discount as $request) {
				$check = $this->db->select('*')->where('split_id', $split_id)->get('customer_request_discount');
				if($check->num_rows() > 0){
					$this->db->where('split_id', $split_id);
					$q = $this->db->update('customer_request_discount', $request);
				}else{
					$q = $this->db->insert('customer_request_discount', $request);
				}				
			}
		}
    	$sales_array = array(
		            'grand_total' => $sales_total,
					'delivery_person_id' => $delivery_person
		        );
			if($this->Settings->socket_enable !=0){
				$this->site->create_notification($notification_array);
			}
    	     foreach ($timelog_array as $time) {
              	$res = $this->db->insert('time_log', $time);
              }
        if ($this->db->insert('sales', $order_data)) {
            $sale_id = $this->db->insert_id();
	    /*********** seats count **************/
		    $splitID = $order_data['sales_split_id'];
		    $this->db->where(array('split_id'=>$splitID));
		    $this->db->where('seats_id !=',0);
		    $order_seats_query = $this->db->get('orders');
		    $bill_seats = 0;
		    if($order_seats_query->num_rows()>0){
		       $bill_seats = $order_seats_query->row('seats_id');
		    }
	    /*********** seats count **************/
        	if(!empty($birthday)){ 	$this->db->insert('birthday', $birthday); }
            $this->db->update('sales', $sales_array, array('id' => $sale_id));
            foreach ($billData as $key =>  $bills) {
             	$bills['sales_id'] = $sale_id;
				$bills['table_whitelisted'] = $this->isTableWhitelisted($order_data['sales_table_id']);
				$bills['seats'] = $bill_seats;
              	$this->db->insert('bils', $bills);
				$bill_id = $this->db->insert_id();
				$this->db->update('bils', array('bill_sequence_number' => $bill_id), array('id' => $bill_id));
				foreach ($splitData[$key]  as $bill_items) {
					$bill_items['bil_id'] = $bill_id;
					$bill_items['item_type'] = ($bill_items['net_unit_price'])?"Paid":"Complimentary";
					$this->db->insert('bil_items', $bill_items);
							$bill_item_id = $this->db->insert_id();
							$recipe_addon_item =[];
							$addonid = explode(',',$bill_items['addon_id'] ); 
							$addonqty = explode(',', $bill_items['addon_qty']);						
							foreach ($addonid as $key => $addon) {
							if($addon !='null') :
								$AddonDetails = $this->site->getaddonitemid($addon);
								$recipeDetails = $this->pos_model->getrecipeByID($AddonDetails->addon_item_id);
								$recipe_addon_item[] = array(									
									'bill_id'      => $bill_id,
									'bill_item_id'       => $bill_item_id,
									'sale_item_id'       => $recipeDetails->id,
									'addon_id'           => $addon,
									'price'              => $recipeDetails->price ? $recipeDetails->price/$total_bills : 0,
									'qty'                => $addonqty[$key]/$total_bills,
									'subtotal'           => ($addonqty[$key]/$total_bills * $recipeDetails->price/$total_bills),
								);
							endif;	
							}								
							foreach ($recipe_addon_item as $recipe_addon) {
								$this->db->insert('addon_bill_items', $recipe_addon);
							}
										
					/*addon array*/	
				}
            }

            foreach ($order_item as $item) {
                $this->db->insert('sale_items', $item);
                // print_r($this->db->last_query());die;
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
           $this->db->where('split_id',$split_id);
		   $this->db->update('orders',array("sale_status"=>'Bill Generated',"payment_status"=>'Bill Generated'));
            if ($order_data['sale_status'] == 'completed') {
            }
               //   print_r($this->db->error());die;
             $this->site->updateTableStatus($order_data['sales_table_id'],4,$this->session->userdata('user_id')); 
			 
			
            return true;
        }//print_r($this->db->error());die;
        return false;
    } 
	public function addKitchen_all($data = array(), $items = array(), $kitchen = array(), $notification_array = array(), $warehouse_id, $user_id){
		if ($this->db->insert('orders', $data)){
				$sale_id = $this->db->insert_id();
				$this->site->create_notification($notification_array);
				$msg = array();
				$msg[] = 'Your order has been success';
				$kitchen['sale_id'] = $sale_id;
				$this->db->insert('restaurant_table_orders', array('order_id' => $sale_id, 'table_id' => $data['table_id']));
				$this->db->insert('restaurant_table_sessions', array('table_id' => $data['table_id'],'order_id' => $sale_id, 'split_id' => $data['split_id'], 'customer_id' => $data['customer_id'], 'session_started' => date('Y-m-d H:i:s')));
				$print_items =[]; 				
				if($this->db->insert('kitchen_orders', $kitchen)){
					$kitchen_id = $this->db->insert_id();					
					foreach ($items as $item) {		
						$item['sale_id'] = $sale_id;
						$item['kitchen_id'] = $kitchen_id;						
						$this->db->insert('order_items', $item);						
						$order_item_id = $this->db->insert_id();						
						$item['order_item_id'] = $order_item_id;
						if($this->pos_settings->kot_print_lang_option ==1){  //if kot only for local language
							$item['image_path'] = str_replace(' ', '-',$order_item_id).'.png';
							$this->db->update('order_items', array('image_path' => str_replace(' ', '-',$order_item_id).'.png'), array('id' => $order_item_id));
							$yesdrdayfolder = date('Ymd',strtotime("-1 days"));
							if (file_exists('assets/language/'.$yesdrdayfolder)) {							
								$path   = 'assets/language/'.$yesdrdayfolder; 	
								delete_files($path, true , false, 1);												
							}					
							if($item['recipe_name_img'] != ''){
								$kotimagefolder = date('Ymd');						
								if (!file_exists('assets/language/'.$kotimagefolder)) {
								    mkdir('assets/language/'.$kotimagefolder, 0777, true);
								}
								$filename = 'assets/language/'.$kotimagefolder.'/'.str_replace(' ', '-',$order_item_id).'.png';
		            			$this->base64ToImage($item['recipe_name_img'],$filename);
		            			$this->db->update('order_items', array('recipe_name_img' => ''), array('id' => $order_item_id));
	            		   }
	            		} 

					if($item['addon_qty'] !='null' || $item['addon_qty'] !=''){
							$recipe_addon_item =[];
							$addonid = explode(',',$item['addon_id']); 
							$addonqty = explode(',', $item['addon_qty']);	
							if($item['addon_name_img'] != ''){
								$addon_name_img = explode('sivan', $item['addon_name_img']);						
							}else{
								$addon_name_img ='';
							}
							foreach ($addonid as $key => $addon) {	
							if($addon !='null' && $addonqty[$key] != 0) :
								$AddonDetails = $this->site->getaddonitemid($addon);								
								$recipeDetails = $this->pos_model->getrecipeByID($AddonDetails->addon_item_id);
								$recipe_addon_item[] = array(
									'split_id'           => $data['split_id'],
									'order_id'           => $sale_id,
									'sale_item_id'       => $recipeDetails->id,
									'order_item_id'      => $order_item_id,
									'addon_id'      => $addon,
									'price'      => $recipeDetails->price ? $recipeDetails->price : 0,
									'qty'      => $addonqty[$key],
									'addon_name_img' => $addon_name_img[$key] ? $addon_name_img[$key] :'',
									'subtotal'      => ($addonqty[$key] * $recipeDetails->price),
								);
							endif;	
							}											
							foreach ($recipe_addon_item as $recipe_addon) {
								$this->db->insert('addon_sale_items', $recipe_addon);
								$addon_sale_items_id  = $this->db->insert_id();
								if($kit_item['addon_id'] !='null'){	
									if($this->pos_settings->kot_print_lang_option ==1){  //if kot only for local language
										$this->db->update('addon_sale_items', array('image_path' => str_replace(' ', '-',$order_item_id.$addon_sale_items_id).'.png'), array('id' => $addon_sale_items_id));

										$filename = 'assets/language/'.$kotimagefolder.'/'.str_replace(' ', '-',$order_item_id.$addon_sale_items_id).'.png';								
				            			$this->base64ToImage($recipe_addon['addon_name_img'],$filename);
				            			$this->db->update('addon_sale_items', array('addon_name_img' => ''), array('id' => $addon_sale_items_id));
				            		}	
		            			}
							}
					}
					/*addon array*/	
						$cm = $this->db->get_where('category_mapping',array('product_id'=>$item['recipe_id'],'status'=>1))->row();
						$cate['category_id'] = $cm->id;
						$cate['category_id'] = $cm->category_id;
						$cate['subcategory_id'] = $cm->subcategory_id;
						$cate['brand_id'] = $cm->brand_id;
						// $this->site->salestock_out($item['recipe_id'],$item['quantity'],$order_item_id,$item['quantity'],$cate);
						/*echo "<pre>";
						print_r($cate);die;*/
						if($this->Settings->procurment == 1){
							if($item['recipe_type'] =='standard' || $item['recipe_type'] =='production'){
								//var_dump($item['recipe_variant_id']);die;
								$this->site->updateStockMaster_new($item['recipe_id'],$item['recipe_variant_id'],$item['quantity'],$cate,$order_item_id);
							}elseif($item['recipe_type'] =='quick_service'){
								//echo '@@';
								$this->production_salestock_out($item['recipe_id'],$item['quantity'],$item['recipe_variant_id']);
							}	
						}
						if($item['recipe_type'] =="combo"){
							$combo_items=$this->get_comboItems($item['recipe_id'],$item['recipe_name']);
							$print_items=array_merge($print_items,$combo_items);
						}
						$print_items[] = $item;
					}
					$msg[] = 'Order sent to kitchen process. wait few mintues';					
				}	//die;

				$this->site->updateTableStatus($data['table_id'],1,$user_id);				 
				$this->db->select('orders.*, restaurant_tables.name AS table_name');
				$this->db->join('restaurant_tables', 'restaurant_tables.id = orders.table_id', 'left');		
			    $this->db->where('orders.id', $sale_id);
				$t = $this->db->get('orders');
				$kit = array();
				$consolid_kit = array();
				if ($t->num_rows() > 0) {
					$orders_details =  $t->row();
					$kit['orders_details'] = $orders_details;
					$consolid_kit['orders_details'] = $orders_details;
					$consolidate_kitchens_kot['orders_details'] = $orders_details;
				}	

/*Consolidate kot for table area wise */ 
if($this->pos_settings->kot_enable_disable == 1){
	if($this->pos_settings->consolidated_kot_print_option == 0){
			if($this->pos_settings->consolidated_kot_print != 0){
				$table_id = $consolid_kit['orders_details']->table_id;
				$consolid_kot_print_details = $this->db->select("printers.*")
				->join('restaurant_areas', 'restaurant_areas.printer_id = printers.id','left')
				->join('restaurant_tables', 'restaurant_tables.area_id = restaurant_areas.id','left')							
				->where('restaurant_tables.id', $table_id)
				->get('printers');
				if ($consolid_kot_print_details->num_rows() > 0) {
				  $consolid_kit['consolid_kot_print_details'][] =  $consolid_kot_print_details->row();
				 }else{
				 	$consolid_kit['consolid_kot_print_details'] =array();
				 }
				$consolid_kit_item =array();
				foreach($print_items as $key => $kit_item){
					// $printer_order_item_id = $this->site->get_printer_order_item_id($sale_id,$kit_item['recipe_id']);
					if($kit_item['kitchen_type_id'] != 0) :
						/*var_dump($kit_item['recipe_id']);*/
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
								$consolid_kit_item[$key]['recipe_name'] = $kit_item['recipe_name'];;
								
							}else{
								$consolid_kit_item[$key]['recipe_name'] = $kit_item['recipe_name'];
							}
						}else{
								$consolid_kit_item[$key]['recipe_name'] = $kit_item['recipe_name'];
						}
						
						$consolid_kit_item[$key]['en_recipe_name'] = $kit_item['recipe_name'];
						$consolid_kit_item[$key]['comment'] = $kit_item['comment'];
						$consolid_kit_item[$key]['en_variant_name'] = $kit_item['variant'] ? $kit_item['variant'] :'';

						if($this->pos_settings->kot_print_lang_option ==1){
							$kotimagefolder = date('Ymd');	
							$consolid_kit_item[$key]['khmer_recipe_image'] = (!empty($kit_item['image_path']) && file_exists('assets/language/'.$kotimagefolder.'/'.$kit_item['image_path']))? (base_url().'assets/language/'.$kotimagefolder.'/'.$kit_item['image_path']) : '';
						}else{
							$consolid_kit_item[$key]['khmer_recipe_image'] =(!empty($khmer_image) && file_exists('assets/language/'.$khmer_image))? (base_url().'assets/language/'.$khmer_image) : '';
						}

						/*if($kit_item['recipe_variant_id']!= 0){
							$consolid_kit_item[$key]['en_variant_name'] = $kit_item['variant'];
							$variant_localname_image = $this->site->getitemvariantlocalnameimage($kit_item['recipe_id'],$kit_item['recipe_variant_id']);
							$consolid_kit_item[$key]['khmer_recipe_image'] = (!empty($variant_localname_image) && file_exists('assets/language/'.$variant_localname_image))? (base_url().'assets/language/'.$variant_localname_image) : '';
						}else{									
							$consolid_kit_item[$key]['khmer_recipe_image'] = (!empty($khmer_image) && file_exists('assets/language/'.$khmer_image))? (base_url().'assets/language/'.$khmer_image) : '';
						} 	*/											
						$consolid_kit_item[$key]['quantity'] = $kit_item['quantity'];
						$consolid_kit_item[$key]['get_item_name'] = $get_item->name;
						$consolid_kit_item[$key]['total_get_quantity'] = $get_item->total_get_quantity;
						$addondetails ='';
						if($kit_item['addon_id'] !='null'){					
								$addondetails = $this->site->getAddonByRecipeidAndOrderitemid_kot($kit_item['recipe_id'],$kit_item['order_item_id']);
							}		
										
						$consolid_kit_item[$key]['addons'] = $addondetails;
				   endif;		
				}						
				$consolid_kit['consolid_kitchens'] = $consolid_kit_item;
		    }else{
			$consolid_kit['consolid_kitchens'] = array();
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

							if($this->pos_settings->kot_print_lang_option ==1){
								$kotimagefolder = date('Ymd');	
								$consolidate_kitchen_row->kit_o[$key]['khmer_recipe_image'] = (!empty($kit_item['image_path']) && file_exists('assets/language/'.$kotimagefolder.'/'.$kit_item['image_path']))? (base_url().'assets/language/'.$kotimagefolder.'/'.$kit_item['image_path']) : '';
							}else{
								$consolidate_kitchen_row->kit_o[$key]['khmer_recipe_image'] =(!empty($khmer_image) && file_exists('assets/language/'.$khmer_image))? (base_url().'assets/language/'.$khmer_image) : '';
							}

							/*if($kit_item['recipe_variant_id']!= 0){
								$consolidate_kitchen_row->kit_o[$key]['en_variant_name'] = $kit_item['variant'];
								$variant_localname_image = $this->site->getitemvariantlocalnameimage($kit_item['recipe_id'],$kit_item['recipe_variant_id']);
								$consolidate_kitchen_row->kit_o[$key]['khmer_recipe_image'] = (!empty($variant_localname_image) && file_exists('assets/language/'.$variant_localname_image))? (base_url().'assets/language/'.$variant_localname_image) : '';
							}else{									
								$consolidate_kitchen_row->kit_o[$key]['khmer_recipe_image'] = (!empty($khmer_image) && file_exists('assets/language/'.$khmer_image))? (base_url().'assets/language/'.$khmer_image) : '';
							} */
							// $consolidate_kitchen_row->kit_o[$key]['khmer_recipe_image'] = (!empty($khmer_image) && file_exists('assets/language/'.$khmer_image))? (base_url().'assets/language/'.$khmer_image) : '';	
							$consolidate_kitchen_row->kit_o[$key]['quantity'] = $kit_item['quantity'];
							$consolidate_kitchen_row->kit_o[$key]['get_item_name'] = $get_item->name;
							$consolidate_kitchen_row->kit_o[$key]['total_get_quantity'] = $get_item->total_get_quantity;	
							$addondetails ='';
							if($kit_item['addon_id'] !='null'){	
								$addondetails = $this->site->getAddonByRecipeidAndOrderitemid_kot($kit_item['recipe_id'],$kit_item['order_item_id']);
							}
							$consolidate_kitchen_row->kit_o[$key]['addons'] = $addondetails;								
						}
					}
					$consolidate_kitchens_kot['kitchens'][] = $consolidate_kitchen_row;
				}					
			}else{
				$consolidate_kitchens_kot[] = '';	
			}
	}
				
/*Consolidate kot only one single priter */	
/*items kot with mapped kichens its common for all clients*/
				$kitchen_details = $this->db->select('restaurant_kitchens.id,restaurant_kitchens.printer_id,  restaurant_kitchens.name')->get('restaurant_kitchens');
				if ($kitchen_details->num_rows() > 0) {					
					foreach (($kitchen_details->result()) as $kitchen_row) {
						
						$printers_details = $this->db->select('*')->where('id', $kitchen_row->printer_id)->get('printers');	
						if ($printers_details->num_rows() > 0) {
							$kitchen_row->printers_details =  $printers_details->row();
						}
					
						foreach($print_items as $key => $kit_item){
						//	print_r($kit_item['kitchen_type_id']."||".$kitchen_row->id);
					
							if($kitchen_row->id == $kit_item['kitchen_type_id']){
								
								// $addons = $this->site->getAddonByRecipe($kit_item['recipe_id'], $kit_item['addon_id']);
								$get_item =  $this->site->getrecipeByID($kit_item['get_item']);									
								$kitchen_row->kit_o[$key]['recipe_id'] = $kit_item['recipe_id'];								
								if($this->Settings->user_language == 'khmer' || true){
									$khmer_name = $this->site->getrecipeKhmer($kit_item['recipe_id']);	
									if($kit_item['recipe_variant_id']!= 0){
										$khmer_image = $this->site->getitemvariantlocalnameimage($kit_item['recipe_id'],$kit_item['recipe_variant_id']);
									}else{
										$khmer_image = $this->site->getrecipeKhmerimage($kit_item['recipe_id']);
									}

									if(!empty($khmer_name)){
										$kitchen_row->kit_o[$key]['recipe_name'] = $kit_item['recipe_name'];;
									}else{
										$kitchen_row->kit_o[$key]['recipe_name'] = $kit_item['recipe_name'];
									}
								}else{
									$kitchen_row->kit_o[$key]['recipe_name'] = $kit_item['recipe_name'];
								}
								$kitchen_row->kit_o[$key]['en_variant_name'] = $kit_item['variant'] ? $kit_item['variant'] :'';								
								// $kitchen_row->kit_o[$key]['khmer_name'] = iconv(mb_detect_encoding($khmer_name, mb_detect_order(), true), "UTF-8", $khmer_name);
								
								/*if($kit_item['recipe_variant_id']!= 0){
									$kitchen_row->kit_o[$key]['en_variant_name'] = $kit_item['variant'];
									$variant_localname_image = $this->site->getitemvariantlocalnameimage($kit_item['recipe_id'],$kit_item['recipe_variant_id']);
									$kitchen_row->kit_o[$key]['khmer_recipe_image'] = (!empty($variant_localname_image) && file_exists('assets/language/'.$variant_localname_image))? (base_url().'assets/language/'.$variant_localname_image) : '';
								}else{									
									$kitchen_row->kit_o[$key]['khmer_recipe_image'] = (!empty($khmer_image) && file_exists('assets/language/'.$khmer_image))? (base_url().'assets/language/'.$khmer_image) : '';
								} */
								/*echo "<pre>";
								print_r($kit_item);die;*/
								if($this->pos_settings->kot_print_lang_option ==1){
									$kotimagefolder = date('Ymd');	
									$kitchen_row->kit_o[$key]['khmer_recipe_image'] = (!empty($kit_item['image_path']) && file_exists('assets/language/'.$kotimagefolder.'/'.$kit_item['image_path']))? (base_url().'assets/language/'.$kotimagefolder.'/'.$kit_item['image_path']) : '';
								}else{
									$kitchen_row->kit_o[$key]['khmer_recipe_image'] =(!empty($khmer_image) && file_exists('assets/language/'.$khmer_image))? (base_url().'assets/language/'.$khmer_image) : '';
								}

								$kitchen_row->kit_o[$key]['en_recipe_name'] = $kit_item['recipe_name'];
								$kitchen_row->kit_o[$key]['en_variant_name'] = $kit_item['variant'];
								$kitchen_row->kit_o[$key]['comment'] = $kit_item['comment'];
								$kitchen_row->kit_o[$key]['quantity'] = $kit_item['quantity'];
								$kitchen_row->kit_o[$key]['get_item_name'] = $get_item->name;
								$kitchen_row->kit_o[$key]['total_get_quantity'] = $get_item->total_get_quantity;
								$addondetails ='';
								if($kit_item['addon_id'] !='null'){					
									$addondetails = $this->site->getAddonByRecipeidAndOrderitemid_kot($kit_item['recipe_id'],$kit_item['order_item_id']);
								}        								
								$kitchen_row->kit_o[$key]['addons'] = $addondetails;

								$unwanted_ingredientsdetails ='';
								if($kit_item['unwanted_ingredients'] !=''){					
									$unwanted_ingredientsdetails = $this->site->getunwanted_ingredientfor_kot($kit_item['recipe_id'],$kit_item['order_item_id'],$kit_item['unwanted_ingredients']);
								}                                
								$kitchen_row->kit_o[$key]['unwanted_ingredients'] = $unwanted_ingredientsdetails;
							}
						}
						$kit['kitchens'][] = $kitchen_row;
					}
					
				}else{
					$kit[] = '';	
				}
/*items kot with mapped kichens its common for all clients*/
/*kitchen consolidate start */
			$kitchen_details = $this->db->select('restaurant_kitchens.id,restaurant_kitchens.kitchen_consolid_printer_id,  restaurant_kitchens.name')->where('kitchen_consolid_printer_id !=', 0)->get('restaurant_kitchens');
				if ($kitchen_details->num_rows() > 0) {					
					foreach (($kitchen_details->result()) as $kitchen_row) {
						$printers_details = $this->db->select('*')->where('id', $kitchen_row->kitchen_consolid_printer_id)->get('printers');
						if ($printers_details->num_rows() > 0) {
							$kitchen_row->printers_details =  $printers_details->row();
						}						
						 foreach($print_items as $key => $kit_item){
							if($kitchen_row->id == $kit_item['kitchen_type_id']){
								$addons = $this->site->getAddonByRecipe($kit_item['recipe_id'], $kit_item['addon_id']);
								$get_item =  $this->site->getrecipeByID($kit_item['get_item']);
								$kitchen_row->kit_o[$key]['recipe_id'] = $kit_item['recipe_id'];
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

								$kitchen_row->kit_o[$key]['en_variant_name'] = $kit_item['variant'] ? $kit_item['variant'] :'';
								$kitchen_row->kit_o[$key]['en_recipe_name'] = $kit_item['recipe_name'];
								// $kitchen_row->kit_o[$key]['khmer_name'] = iconv(mb_detect_encoding($khmer_name, mb_detect_order(), true), "UTF-8", $khmer_name);
								$kitchen_row->kit_o[$key]['comment'] = $kit_item['comment'];
								/*if($kit_item['recipe_variant_id']!= 0){
									$kitchen_row->kit_o[$key]['en_variant_name'] = $kit_item['variant'];
									$variant_localname_image = $this->site->getitemvariantlocalnameimage($kit_item['recipe_id'],$kit_item['recipe_variant_id']);
									$kitchen_row->kit_o[$key]['khmer_recipe_image'] = (!empty($variant_localname_image) && file_exists('assets/language/'.$variant_localname_image))? (base_url().'assets/language/'.$variant_localname_image) : '';
								}else{									
									$kitchen_row->kit_o[$key]['khmer_recipe_image'] = (!empty($khmer_image) && file_exists('assets/language/'.$khmer_image))? (base_url().'assets/language/'.$khmer_image) : '';
								} 	*/
								if($this->pos_settings->kot_print_lang_option ==1){
									$kotimagefolder = date('Ymd');	
									$kitchen_row->kit_o[$key]['khmer_recipe_image'] = (!empty($kit_item['image_path']) && file_exists('assets/language/'.$kotimagefolder.'/'.$kit_item['image_path']))? (base_url().'assets/language/'.$kotimagefolder.'/'.$kit_item['image_path']) : '';
								}else{
									$kitchen_row->kit_o[$key]['khmer_recipe_image'] =(!empty($khmer_image) && file_exists('assets/language/'.$khmer_image))? (base_url().'assets/language/'.$khmer_image) : '';
								}

								$kitchen_row->kit_o[$key]['quantity'] = $kit_item['quantity'];
								$kitchen_row->kit_o[$key]['get_item_name'] = $get_item->name;
								$kitchen_row->kit_o[$key]['total_get_quantity'] = $get_item->total_get_quantity;
								if($kit_item['addon_id'] !='null'){					
									$addondetails = $this->site->getAddonByRecipeidAndOrderitemid_kot($kit_item['recipe_id'],$kit_item['order_item_id']);
								}								
								$kitchen_row->kit_o[$key]['addons'] = $addondetails ? $addondetails :'';
								//$kitchen_row->kit_o[$kitchen_row->id][] = $kit_item['quantity'];
							}
						}
						$consolidate_kitchens_kot['kitchens'][] = $kitchen_row;
					}
				}else{
					$consolidate_kitchens_kot[] = '';	
				}
}
/*
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
								$consolidate_kitchen_row->kit_o[$key]['khmer_recipe_image'] = (!empty($khmer_image) && file_exists('assets/language/'.$khmer_image))? (base_url().'assets/language/'.$khmer_image) : '';								
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
				}*/
				/*kitchen consolidate*/		
				/*echo "<pre>";
				print_r($kit);*/
				// die;
				// print_r($this->db->error());die;				
				return array('sale_id' => $sale_id, 'message' => $msg, 'kitchen_data' => $kit, 'consolid_kitchen_data' => $consolid_kit, 'consolidate_kitchens_kot' => $consolidate_kitchens_kot);
		}		
		// print_r($this->db->error());die;
		return false;
	}
	
	   function production_salestock_out($product_id,$stock_out_qty,$variant_id){
		// ingredient stock
        $item = $this->getrecipedeatilsByID($product_id);  
        //if($item->type=="production" || $item->type=="quick_service" || $item->type=="semi_finished"){
           $q = $this->get_recipe_products($product_id,$variant_id); 
            if($q->num_rows()>0){
                foreach($q->result() as $k => $row){
                    $cate['category_id'] = $row->category_id;
                    $cate['subcategory_id'] = $row->subcategory_id;
                    $cate['brand_id'] = $row->brand_id;
                    $cate['cm_id'] = $row->cm_id;
					$ingredint_stock_out=$stock_out_qty *$this->site->unitToBaseQty($row->quantity,$row->operator,$row->operation_value);
                    $updated_stock = $this->productionupdateStockMaster($row->product_id,$variant_id,$ingredint_stock_out,$cate);
                }
            }
		
        //}   // die;    
		return true;
    }

	
	
	
	
    function productionupdateStockMaster($product_id,$variant_id,$base_quantity,$cate){   
        $store_id = $this->data['pos_store'];
        $batches =$this->getrawstock($product_id,$variant_id,$cate['category_id'],$cate['subcategory_id'],$cate['brand_id']); 
       foreach($batches as $batch){
			if($batch->stock_in>0){
				$balance_quantity =$base_quantity-$batch->stock_in;
				if($balance_quantity>0){
				$query = 'update srampos_pro_stock_master set stock_in = stock_in - '.$batch->stock_in.',  stock_out = stock_out + '.$batch->stock_in.'  ,stock_status="closed" where store_id='.$this->store_id.' and id='.$batch->id;
                $this->db->query($query); 
				$base_quantity =$base_quantity-$batch->stock_in;
				}else{
					$query = 'update srampos_pro_stock_master set stock_in = stock_in - '.$base_quantity.',  stock_out = stock_out + '.$base_quantity.' where store_id='.$this->store_id.' and id='.$batch->id;
                    $this->db->query($query); 
					break;
				}
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
        $this->db->where('product_id',$product_id);
		$this->db->where('store_id',$this->store_id);
		//if($variant_id !='0'){
            //$this->db->where('variant_id',$variant_id);
        //}
        //$this->db->where('variant_id',$variant_id);
        $this->db->where_not_in('stock_status','closed');
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
	
	
	
	
	
	public function getrecipedeatilsByID($id) {
        $q = $this->db->get_where('recipe', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	 function get_recipe_products($product_id,$variant_id){
        $this->db->select('recipe_products.product_id,recipe_products.quantity,recipe.type,cm.id as cm_id,cm.category_id,cm.subcategory_id,cm.brand_id,IFNULL(u.operator,"") as operator,IFNULL(u.operation_value,"1") as operation_value,u.name');
        $this->db->from('recipe_products');
        $this->db->join('recipe','recipe.id=recipe_products.product_id');
        $this->db->join('category_mapping as cm','cm.id=recipe_products.cm_id','left');
        $this->db->join('units as u','u.id=recipe_products.unit_id','left');
        $this->db->where('recipe_id',$product_id);
        if($variant_id != 0){
            $this->db->where('recipe_products.variant_id',$variant_id);
        }
        $q = $this->db->get();            
        return $q;
    }        

	
	
	function get_table_order_count($table_id,$split_id=null){
		$current_date = $this->site->getTransactionDate();
    	$this->db->select('split_id,customer_id,customer');		
		$this->db->where('payment_status', NULL);	
		$this->db->where('date', $current_date);	
		$this->db->where('table_id', $table_id);
		if(!empty($split_id)){
		$this->db->where('split_id', $split_id);
		}
		$this->db->group_by("split_id");
        $q = $this->db->get('orders');
		if($q->num_rows()>1){
			return $q->result();
		}else{
			return $q->row();
		}
		
	}
	public function getAllSalesWithbiller_based_split($sales_type_id = NULL,$table_id=null,$split_id=null){
		$current_date = date('Y-m-d');
		$current_date = $this->site->getTransactionDate();
		$this->db->select("sales.*, ,restaurant_tables.name as tablename,restaurant_areas.name as areaname, customer_request_discount.id as customer_request_id, 'bils'");
		$this->db->join("restaurant_tables", "restaurant_tables.id = sales.sales_table_id",'left');
		$this->db->join("restaurant_areas", "restaurant_areas.id = restaurant_tables.area_id",'left');
		$this->db->join("bils", "bils.sales_id = sales.id",'left');
		$this->db->join("customer_request_discount", "customer_request_discount.split_id = sales.sales_split_id AND customer_request_discount.table_id = sales.sales_table_id", 'left');
		if(!empty($sales_type_id)){
			$this->db->where('sales.sales_type_id', $sales_type_id);	
		}
		$this->db->where('sales.warehouse_id', $this->session->userdata('warehouse_id'));
		$this->db->where('sales.sale_status', 'Process');
		$this->db->where('sales.cancel_status', 0);
		$this->db->where('sales.consolidated', 0);
		if($table_id!=null){
		    $this->db->where('sales.sales_table_id', $table_id);
		}
		 $this->db->where('sales.sales_split_id', $split_id);
		// $this->db->where('srampos_bils.date', $date);
		$this->db->group_by("sales.id");
		$this->db->order_by("bils.bill_number", "desc");
		$s = $this->db->get('sales');
		if ($s->num_rows() > 0) {
            foreach ($s->result() as $row) {
				$this->db->select("bils.*,companies.credit_limit,companies.name customer_name,companies.allow_loyalty,companies.customer_type,companies.id company_id,companies.credit_limit");
				$this->db->join("companies", "companies.id = bils.customer_id",'left');
				$this->db->where('bils.sales_id', $row->id);
				$this->db->where('bils.consolidated', 0);
				$b = $this->db->get('bils');
				// AND cp.due_date > CURRENT_TIMESTAMP
				if ($b->num_rows() > 0) {
					foreach ($b->result() as $bil_row) {
						$bils[$row->id][] = $bil_row;
					}
					$row->bils = $bils[$row->id];
					$data[] = $row;	
				}
			}
			return $data;
		}

		return FALSE;
	}
	function ordered_table(){
		$this->db->select(" id , name ");
		$this->db->where("current_order_status",1);
		$q=$this->db->get("restaurant_tables");
		if($q->num_rows()>0){
			foreach($q->result() as $row){
				$data[]=$row;
			}
			return $data;
		}
		return false;
	}
	function billing_table(){
		$this->db->select(" id , name ");
		$this->db->where("current_order_status",4);
		$q=$this->db->get("restaurant_tables");
		if($q->num_rows()>0){
			foreach($q->result() as $row){
				$data[]=$row;
			}
			return $data;
		}
		return false;
	}
	 function ordered_steward(){
		$this->db->select("users.id,username,first_name");
		$this->db->join("users","users.id=orders.created_by","left");
		$this->db->group_by("created_by");
		$q=$this->db->get("orders");
		if($q->num_rows()>0){
			foreach($q->result() as $row){
				$data[]=$row;
			}
			return $data;
		}
		return false;
	} 
	function get_sales_details($shift_id,$till_id,$store_id){
		$this->db->select("SUM(round_total) total,SUM(balance) balance");
	//	$this->db->join("sale_currency","sale_currency.bil_id=bils.id","left");
		//$this->db->join("currencies","currencies.id=sale_currency.currency_id","left");
		$this->db->where("payment_status",'Completed');
		$this->db->where("shift_id",$shift_id);
		$this->db->where("till_id",$till_id);
		$this->db->where("store_id",$store_id);
		$this->db->where("date(date)",date("Y-m-d"));
		$q=$this->db->get("bils");
		if($q->num_rows()>0){
		return $q->row();
		}
		return false;
		
	}
	function get_sales_details_byUserWise($shift_id,$till_id,$store_id,$user_id){
		$this->db->select("SUM(round_total) total,SUM(balance) balance");
	//	$this->db->join("sale_currency","sale_currency.bil_id=bils.id","left");
		//$this->db->join("currencies","currencies.id=sale_currency.currency_id","left");
		$this->db->where("payment_status",'Completed');
		$this->db->where("shift_id",$shift_id);
		$this->db->where("till_id",$till_id);
		$this->db->where("store_id",$store_id);
		$this->db->where("created_by",$user_id);
		$this->db->where("date(date)",date("Y-m-d"));
		$q=$this->db->get("bils");
		if($q->num_rows()>0){
		return $q->row();
		}
		return false;
		
	}
	function get_sales_details_khr($shift_id,$till_id,$store_id){
		$this->db->select(" CODE,rate,symbol,SUM(srampos_sale_currency.amount) paid,currency_rate,SUM(round_total) total,SUM(balance) balance");
		$this->db->join("sale_currency","sale_currency.bil_id=bils.id","left");
		$this->db->join("currencies","currencies.id=sale_currency.currency_id","left");
		$this->db->where("payment_status",'Completed');
		$this->db->where("shift_id",$shift_id);
		$this->db->where("till_id",$till_id);
		$this->db->where("currencies.id",1);
		$this->db->where("store_id",$store_id);
		$this->db->where("date(date)",date("Y-m-d"));
		$this->db->group_by("code");
		$q=$this->db->get("bils");
		if($q->num_rows()>0){
		return $q->row();
		}
		return false;
		
	}
	
	function get_sales_details_usd($shift_id,$till_id,$store_id){
		$this->db->select(" CODE,rate,symbol,SUM(srampos_sale_currency.amount) paid,currency_rate,SUM(round_total) total,SUM(balance) balance");
		$this->db->join("sale_currency","sale_currency.bil_id=bils.id","left");
		$this->db->join("currencies","currencies.id=sale_currency.currency_id","left");
		$this->db->where("payment_status",'Completed');
		$this->db->where("shift_id",$shift_id);
		$this->db->where("till_id",$till_id);
		$this->db->where("store_id",$store_id);
		$this->db->where("currencies.id",2);
		$this->db->where("date(date)",date("Y-m-d"));
		$this->db->group_by("code");
		$q=$this->db->get("bils");
		if($q->num_rows()>0){
		return $q->row();
		}
		return false;
		
	}
	function get_openning_cash(){
		$shift_id=!empty($this->ShiftID)?$this->ShiftID:0;
		$date=("Y-m-d");
		$this->db->select("CUR_USD,CUR_KHR"); 
		$this->db->where(array("shiftmaster_id"=> $shift_id,"till_id"=>$this->till_id,"settled"=>0,"date(created_on)"=>$date)) ;
		$q=$this->db->get("shifts");
		if($q->num_rows()>0){
			return $q->row();
		}
		return false;
	}
	public function getAllrecipeCategories($limit,$start) {
		if($this->pos_settings->categories_list_by ==0) {
			$this->db->where('type',0);
			$this->db->where('status',1)
			->where('(parent_id="null" or parent_id=0)')->order_by('id');
		}else{
			$this->db->where('type',0);
			$this->db->where('status',1)
			->where('(parent_id="null" or parent_id=0)')->order_by('name');
		}
		$this->db->limit($limit, $start);
        $q = $this->db->get("recipe_categories");        
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	function get_count_category(){
		$q=$this->db->get_where("recipe_categories",array("status"=>1));
		if($q->num_rows()>0){
			foreach($q->result() as $row){
		          $data[]=$row;		
			}
			return count($data);
		}
		return false;
	}
	public function getrecipeSubCategories_count($parent_id) {
        $this->db->where('parent_id', $parent_id)->where('status', 1)->order_by('name');
        $q = $this->db->get("recipe_categories");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return count($data);
        }
        return FALSE;
    }
	public function getrecipeSubCategories($parent_id,$limit,$start) {
		$this->db->select("*");
		$this->db->where('type',0);
	    $this->db->where('status',1);
		$this->db->where('parent_id',$parent_id);
		$this->db->limit($limit, $start);
        $q = $this->db->get("recipe_categories");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	function   get_area_tables($area_id,$warehouse_id,$limit,$start){
		$this->db->select("*");
		$this->db->where('area_id',$area_id);
	    $this->db->where('warehouse_id',$warehouse_id);
	//	$this->db->where('current_order_status',$parent_id);
		$this->db->limit($limit, $start);
        $q = $this->db->get("restaurant_tables");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
		
	}
	function   get_area_tables_count($area_id,$warehouse_id){
		$this->db->select("*");
		$this->db->where('area_id',$area_id);
	    $this->db->where('warehouse_id',$warehouse_id);
	//	$this->db->where('current_order_status',$parent_id);
		$this->db->limit($limit, $start);
        $q = $this->db->get("restaurant_tables");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return count($data);
        }
        return FALSE;
	}
	function active_area_name($area_id){
	$this->db->select("GROUP_CONCAT(name SEPARATOR ', ') area");
	$this->db->where_in("id",explode(",",$area_id));
	$q=$this->db->get("restaurant_areas");
	if($q->num_rows()>0){
	return $q->row();	
	}
	}
	function getCustomerSuggestions($term, $limit = 10){
    $this->db->select("C.id, (CASE WHEN C.company = '-' THEN C.name ELSE CONCAT(C.company, ' (', C.name, ')') END) as text", FALSE);
    $this->db->from('companies C');
    $this->db->join('loyalty_points LP','LP.customer_id=C.id','left');    
    $this->db->where(" (C.id LIKE '%" . $term . "%' OR C.name LIKE '%" . $term . "%' OR C.company LIKE '%" . $term . "%' OR C.email LIKE '%" . $term . "%' OR C.phone LIKE '%" . $term . "%') ");
    $this->db->where('C.group_name', 'customer');    
    $this->db->or_where('LP.total_points >',0);
    $this->db->or_where('LP.loyalty_card_no !=', '') ;  
    $this->db->limit($limit);
    $q = $this->db->get();     
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return '';
    }
	function getMember_discount($customerid){
		$this->db->select("memberdisountcard_issued.*,card_no");
		$this->db->join("memberdiscountcards","memberdiscountcards.id=memberdisountcard_issued.card_id","left");
		$this->db->where("customer_id",$customerid);
		$q=$this->db->get("memberdisountcard_issued");
		if($q->num_rows()>0){
			return $q->row();
		}
		return false;
	}
	function get_order_customer($split_id){
		$this->db->select("customer_id");
		$this->db->where('orders.split_id', $split_id);
		$this->db->where('orders.order_cancel_status', 0);		
		$q = $this->db->get('orders');
		if($q->num_rows()>0){
		  	return $q->row();
		}
		return false;
	}
	function check_bill_exists($date){
		$this->db->select("id");
		$this->db->where("date(date)",$date);
		$q=$this->db->get("bils");
		if($q->num_rows()>0){
			return $q->result();
		}
		return false;
	}
	function cancel_kot($splitid,$order_item_id,$cancelQty){
		       $this->db->select('orders.*, restaurant_tables.name AS table_name');
				$this->db->join('restaurant_tables', 'restaurant_tables.id = orders.table_id', 'left');		
			    $this->db->where('orders.split_id', $splitid);
				$t = $this->db->get('orders');
				$kit = array();
				$consolid_kit = array();
				$print_items=[];
				if ($t->num_rows() > 0) {
					$orders_details =  $t->row();
					$kit['orders_details'] = $orders_details;
					$consolid_kit['orders_details'] = $orders_details;
					$consolidate_kitchens_kot['orders_details'] = $orders_details;
					$items=$this->db->get_where("order_items",array("sale_id"=>$orders_details->id,"id"=>$order_item_id));
					if($items->num_rows() > 0){
						foreach($items->result() as $item){
				     	$print_items[]=$item;
						}
					}
				}	
			
/*Consolidate kot for table area wise */ 
if($this->pos_settings->kot_enable_disable == 1){
	if($this->pos_settings->consolidated_kot_print_option == 0){
			if($this->pos_settings->consolidated_kot_print != 0){
				$table_id = $consolid_kit['orders_details']->table_id;
				$consolid_kot_print_details = $this->db->select("printers.*")
				->join('restaurant_areas', 'restaurant_areas.printer_id = printers.id','left')
				->join('restaurant_tables', 'restaurant_tables.area_id = restaurant_areas.id','left')							
				->where('restaurant_tables.id', $table_id)
				->get('printers');
				if ($consolid_kot_print_details->num_rows() > 0) {
				  $consolid_kit['consolid_kot_print_details'][] =  $consolid_kot_print_details->row();
				 }else{
				 	$consolid_kit['consolid_kot_print_details'] =array();
				 }

				$consolid_kit_item =array();
				foreach($print_items as $key => $kit_item){
					// $printer_order_item_id = $this->site->get_printer_order_item_id($sale_id,$kit_item['recipe_id']);
					if($kit_item->kitchen_type_id != 0) :
						/*var_dump($kit_item['recipe_id']);*/
						$addons = $this->site->getAddonByRecipe($kit_item->recipe_id, $kit_item->addon_id);
						$get_item =  $this->site->getrecipeByID($kit_item->get_item);								
						$consolid_kit_item[$key]['recipe_id'] = $kit_item->recipe_id;							
						if($this->Settings->user_language == 'khmer' || true){
							$khmer_name = $this->site->getrecipeKhmer($kit_item->recipe_id);	

							if($kit_item->recipe_variant_id != 0){
								$khmer_image = $this->site->getitemvariantlocalnameimage($kit_item->recipe_id,$kit_item->recipe_variant_id);								
							}else{
								$khmer_image = $this->site->getrecipeKhmerimage($kit_item->recipe_id);
							}
							
							if(!empty($khmer_name)){
								$consolid_kit_item[$key]['recipe_name'] = $kit_item->recipe_name;;
								
							}else{
								$consolid_kit_item[$key]['recipe_name'] = $kit_item->recipe_name;
							}
						}else{
								$consolid_kit_item[$key]['recipe_name'] = $kit_item->recipe_name;
						}
						
						$consolid_kit_item[$key]['en_recipe_name'] = $kit_item->recipe_name;
						$consolid_kit_item[$key]['comment'] = $kit_item->comment;
						$consolid_kit_item[$key]['en_variant_name'] = $kit_item->variant ? $kit_item->variant :'';

						if($this->pos_settings->kot_print_lang_option ==1){
							$kotimagefolder = date('Ymd');	
							$consolid_kit_item[$key]['khmer_recipe_image'] = (!empty($kit_item->image_path) && file_exists('assets/language/'.$kotimagefolder.'/'.$kit_item->image_path))? (base_url().'assets/language/'.$kotimagefolder.'/'.$kit_item->image_path) : '';
						}else{
							$consolid_kit_item[$key]['khmer_recipe_image'] =(!empty($khmer_image) && file_exists('assets/language/'.$khmer_image))? (base_url().'assets/language/'.$khmer_image) : '';
						}

						/*if($kit_item['recipe_variant_id']!= 0){
							$consolid_kit_item[$key]['en_variant_name'] = $kit_item['variant'];
							$variant_localname_image = $this->site->getitemvariantlocalnameimage($kit_item['recipe_id'],$kit_item['recipe_variant_id']);
							$consolid_kit_item[$key]['khmer_recipe_image'] = (!empty($variant_localname_image) && file_exists('assets/language/'.$variant_localname_image))? (base_url().'assets/language/'.$variant_localname_image) : '';
						}else{									
							$consolid_kit_item[$key]['khmer_recipe_image'] = (!empty($khmer_image) && file_exists('assets/language/'.$khmer_image))? (base_url().'assets/language/'.$khmer_image) : '';
						} 	*/											
						$consolid_kit_item[$key]['quantity'] = $cancelQty;
						$consolid_kit_item[$key]['get_item_name'] = $get_item->name;
						$consolid_kit_item[$key]['total_get_quantity'] = $get_item->total_get_quantity;
						$addondetails ='';
						if($kit_item->addon_id !='null'){					
								$addondetails = $this->site->getAddonByRecipeidAndOrderitemid_kot($kit_item->recipe_id,$kit_item->order_item_id);
							}		
										
						$consolid_kit_item[$key]['addons'] = $addondetails;
				   endif;		
				}						
				$consolid_kit['consolid_kitchens'] = $consolid_kit_item;
		    }else{
			$consolid_kit['consolid_kitchens'] = array();
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
					foreach($print_items as $key => $kit_item){
						if($consolidate_kitchen_row->id == $kit_item->kitchen_type_id){
							$addons = $this->site->getAddonByRecipe($kit_item->recipe_id, $kit_item->addon_id);
							$get_item =  $this->site->getrecipeByID($kit_item->get_item);									
							$consolidate_kitchen_row->kit_o[$key]['recipe_id'] = $kit_item->recipe_id;								
							if($this->Settings->user_language == 'khmer' || true){
									$khmer_name = $this->site->getrecipeKhmer($kit_item->recipe_id);	
									if($kit_item->recipe_variant_id!= 0){
									   $khmer_image = $this->site->getitemvariantlocalnameimage($kit_item->recipe_id,$kit_item->recipe_variant_id);								   
								   }else{
								   			$khmer_image = $this->site->getrecipeKhmerimage($kit_item->recipe_id);
								   }
								if(!empty($khmer_name)){
									$consolidate_kitchen_row->kit_o[$key]['recipe_name'] = $khmer_name;
								}else{
									$consolidate_kitchen_row->kit_o[$key]['recipe_name'] = $kit_item->recipe_name;
								}
							}else{
								$consolidate_kitchen_row->kit_o[$key]['recipe_name'] = $kit_item->recipe_name;
							}
							$consolidate_kitchen_row->kit_o[$key]['en_recipe_name'] = $kit_item->recipe_name;
							$consolidate_kitchen_row->kit_o[$key]['comment'] = $kit_item->comment;

							$consolidate_kitchen_row->kit_o[$key]['en_variant_name'] = $kit_item->variant ? $kit_item->variant :'';

							if($this->pos_settings->kot_print_lang_option ==1){
								$kotimagefolder = date('Ymd');	
								$consolidate_kitchen_row->kit_o[$key]['khmer_recipe_image'] = (!empty($kit_item->image_path) && file_exists('assets/language/'.$kotimagefolder.'/'.$kit_item->image_path))? (base_url().'assets/language/'.$kotimagefolder.'/'.$kit_item->image_path) : '';
							}else{
								$consolidate_kitchen_row->kit_o[$key]['khmer_recipe_image'] =(!empty($khmer_image) && file_exists('assets/language/'.$khmer_image))? (base_url().'assets/language/'.$khmer_image) : '';
							}

							/*if($kit_item['recipe_variant_id']!= 0){
								$consolidate_kitchen_row->kit_o[$key]['en_variant_name'] = $kit_item['variant'];
								$variant_localname_image = $this->site->getitemvariantlocalnameimage($kit_item['recipe_id'],$kit_item['recipe_variant_id']);
								$consolidate_kitchen_row->kit_o[$key]['khmer_recipe_image'] = (!empty($variant_localname_image) && file_exists('assets/language/'.$variant_localname_image))? (base_url().'assets/language/'.$variant_localname_image) : '';
							}else{									
								$consolidate_kitchen_row->kit_o[$key]['khmer_recipe_image'] = (!empty($khmer_image) && file_exists('assets/language/'.$khmer_image))? (base_url().'assets/language/'.$khmer_image) : '';
							} */
							// $consolidate_kitchen_row->kit_o[$key]['khmer_recipe_image'] = (!empty($khmer_image) && file_exists('assets/language/'.$khmer_image))? (base_url().'assets/language/'.$khmer_image) : '';	
							$consolidate_kitchen_row->kit_o[$key]['quantity'] = $cancelQty;
							$consolidate_kitchen_row->kit_o[$key]['get_item_name'] = $get_item->name;
							$consolidate_kitchen_row->kit_o[$key]['total_get_quantity'] = $get_item->total_get_quantity;	
							$addondetails ='';
							if($kit_item->addon_id !='null'){	
								$addondetails = $this->site->getAddonByRecipeidAndOrderitemid_kot($kit_item->recipe_id,$kit_item->order_item_id);
							}
							$consolidate_kitchen_row->kit_o[$key]['addons'] = $addondetails;								
						}
					}
					$consolidate_kitchens_kot['kitchens'][] = $consolidate_kitchen_row;
				}					
			}else{
				$consolidate_kitchens_kot[] = '';	
			}
	}
				
/*Consolidate kot only one single priter */	
/*items kot with mapped kichens its common for all clients*/
				$kitchen_details = $this->db->select('restaurant_kitchens.id,restaurant_kitchens.printer_id,  restaurant_kitchens.name')->get('restaurant_kitchens');
				if ($kitchen_details->num_rows() > 0) {					
					foreach (($kitchen_details->result()) as $kitchen_row) {
						$printers_details = $this->db->select('*')->where('id', $kitchen_row->printer_id)->get('printers');	
						if ($printers_details->num_rows() > 0) {
							$kitchen_row->printers_details =  $printers_details->row();
						}
						foreach($print_items as $key => $kit_item){
							if($kitchen_row->id == $kit_item->kitchen_type_id){
								// $addons = $this->site->getAddonByRecipe($kit_item['recipe_id'], $kit_item['addon_id']);
							    $get_item =  $this->site->getrecipeByID($kit_item->get_item);								
								$kitchen_row->kit_o[$key]['recipe_id'] = $kit_item->recipe_id;								
								if($this->Settings->user_language == 'khmer' || true){
									$khmer_name = $this->site->getrecipeKhmer($kit_item->recipe_id);	
									if($kit_item->recipe_variant_id!= 0){
										$khmer_image = $this->site->getitemvariantlocalnameimage($kit_item->recipe_id,$kit_item->recipe_variant_id);
									}else{
										$khmer_image = $this->site->getrecipeKhmerimage($kit_item->recipe_id);
									}

									if(!empty($khmer_name)){
										$kitchen_row->kit_o[$key]['recipe_name'] = $kit_item->recipe_name;
									}else{
										$kitchen_row->kit_o[$key]['recipe_name'] = $kit_item->recipe_name;
									}
								}else{
									$kitchen_row->kit_o[$key]['recipe_name'] = $kit_item->recipe_name;
								}
								$kitchen_row->kit_o[$key]['en_variant_name'] = $kit_item->variant ? $kit_item->variant:'';								
								// $kitchen_row->kit_o[$key]['khmer_name'] = iconv(mb_detect_encoding($khmer_name, mb_detect_order(), true), "UTF-8", $khmer_name);
								
								/*if($kit_item['recipe_variant_id']!= 0){
									$kitchen_row->kit_o[$key]['en_variant_name'] = $kit_item['variant'];
									$variant_localname_image = $this->site->getitemvariantlocalnameimage($kit_item['recipe_id'],$kit_item['recipe_variant_id']);
									$kitchen_row->kit_o[$key]['khmer_recipe_image'] = (!empty($variant_localname_image) && file_exists('assets/language/'.$variant_localname_image))? (base_url().'assets/language/'.$variant_localname_image) : '';
								}else{									
									$kitchen_row->kit_o[$key]['khmer_recipe_image'] = (!empty($khmer_image) && file_exists('assets/language/'.$khmer_image))? (base_url().'assets/language/'.$khmer_image) : '';
								} */
								/*echo "<pre>";
								print_r($kit_item);die;*/
								if($this->pos_settings->kot_print_lang_option ==1){
									$kotimagefolder = date('Ymd');	
									$kitchen_row->kit_o[$key]['khmer_recipe_image'] = (!empty($kit_item->image_path) && file_exists('assets/language/'.$kotimagefolder.'/'.$kit_item->image_path))? (base_url().'assets/language/'.$kotimagefolder.'/'.$kit_item->image_path) : '';
								}else{
									$kitchen_row->kit_o[$key]['khmer_recipe_image'] =(!empty($khmer_image) && file_exists('assets/language/'.$khmer_image))? (base_url().'assets/language/'.$khmer_image) : '';
								}

								$kitchen_row->kit_o[$key]['en_recipe_name'] = $kit_item->recipe_name;
								$kitchen_row->kit_o[$key]['en_variant_name'] = $kit_item->variant;
								$kitchen_row->kit_o[$key]['comment'] = $kit_item->comment;
								$kitchen_row->kit_o[$key]['quantity'] = $cancelQty;
								$kitchen_row->kit_o[$key]['get_item_name'] = $get_item->name;
								$kitchen_row->kit_o[$key]['total_get_quantity'] = $get_item->total_get_quantity;
								$addondetails ='';
								if($kit_item->addon_id !='null'){					
									$addondetails = $this->site->getAddonByRecipeidAndOrderitemid_kot($kit_item->recipe_id,$kit_item->order_item_id);
								}        								
								$kitchen_row->kit_o[$key]['addons'] = $addondetails;

								$unwanted_ingredientsdetails ='';
								if($kit_item->unwanted_ingredients !=''){					
									$unwanted_ingredientsdetails = $this->site->getunwanted_ingredientfor_kot($kit_item->recipe_id,$kit_item->order_item_id,$kit_item->unwanted_ingredients);
								}                                
								$kitchen_row->kit_o[$key]['unwanted_ingredients'] = $unwanted_ingredientsdetails;
							}
						}
						$kit['kitchens'][] = $kitchen_row;
					}
					
				}else{
					$kit[] = '';	
				}
/*items kot with mapped kichens its common for all clients*/
/*kitchen consolidate start */
			$kitchen_details = $this->db->select('restaurant_kitchens.id,restaurant_kitchens.kitchen_consolid_printer_id,  restaurant_kitchens.name')->where('kitchen_consolid_printer_id !=', 0)->get('restaurant_kitchens');

				if ($kitchen_details->num_rows() > 0) {					
					foreach (($kitchen_details->result()) as $kitchen_row) {
						
						$printers_details = $this->db->select('*')->where('id', $kitchen_row->kitchen_consolid_printer_id)->get('printers');
						
						if ($printers_details->num_rows() > 0) {
							$kitchen_row->printers_details =  $printers_details->row();
						}	
						foreach($print_items as $key => $kit_item){
							if($kitchen_row->id == $kit_item->kitchen_type_id){
								$addons = $this->site->getAddonByRecipe($kit_item->recipe_id, $kit_item->addon_id);
								$get_item =  $this->site->getrecipeByID($kit_item->get_item);
								$kitchen_row->kit_o[$key]['recipe_id'] = $kit_item->recipe_id;
								if($this->Settings->user_language == 'khmer' || true){
									$khmer_name = $this->site->getrecipeKhmer($kit_item->recipe_id);	
									if($kit_item->recipe_variant_id!= 0){
										$khmer_image = $this->site->getitemvariantlocalnameimage($kit_item->recipe_id,$kit_item->recipe_variant_id);
									}else{
										$khmer_image = $this->site->getrecipeKhmerimage($kit_item->recipe_id);
									}
									if(!empty($khmer_name)){
										$kitchen_row->kit_o[$key]['recipe_name'] = $khmer_name;
									}else{
										$kitchen_row->kit_o[$key]['recipe_name'] = $kit_item->recipe_name;
									}
								}else{
									$kitchen_row->kit_o[$key]['recipe_name'] = $kit_item->recipe_name;
								}
								$kitchen_row->kit_o[$key]['en_variant_name'] = $kit_item->variant ? $kit_item->variant :'';
								$kitchen_row->kit_o[$key]['en_recipe_name'] = $kit_item->recipe_name;
								// $kitchen_row->kit_o[$key]['khmer_name'] = iconv(mb_detect_encoding($khmer_name, mb_detect_order(), true), "UTF-8", $khmer_name);

								$kitchen_row->kit_o[$key]['comment'] = $kit_item->comment;
								/*if($kit_item['recipe_variant_id']!= 0){
									$kitchen_row->kit_o[$key]['en_variant_name'] = $kit_item['variant'];
									$variant_localname_image = $this->site->getitemvariantlocalnameimage($kit_item['recipe_id'],$kit_item['recipe_variant_id']);
									$kitchen_row->kit_o[$key]['khmer_recipe_image'] = (!empty($variant_localname_image) && file_exists('assets/language/'.$variant_localname_image))? (base_url().'assets/language/'.$variant_localname_image) : '';
								}else{									
									$kitchen_row->kit_o[$key]['khmer_recipe_image'] = (!empty($khmer_image) && file_exists('assets/language/'.$khmer_image))? (base_url().'assets/language/'.$khmer_image) : '';
								} 	*/
								if($this->pos_settings->kot_print_lang_option ==1){
									$kotimagefolder = date('Ymd');	
									$kitchen_row->kit_o[$key]['khmer_recipe_image'] = (!empty($kit_item->image_path) && file_exists('assets/language/'.$kotimagefolder.'/'.$kit_item->image_path))? (base_url().'assets/language/'.$kotimagefolder.'/'.$kit_item->image_path) : '';
								}else{
									$kitchen_row->kit_o[$key]['khmer_recipe_image'] =(!empty($khmer_image) && file_exists('assets/language/'.$khmer_image))? (base_url().'assets/language/'.$khmer_image) : '';
								}

								$kitchen_row->kit_o[$key]['quantity'] = $cancelQty;
								$kitchen_row->kit_o[$key]['get_item_name'] = $get_item->name;
								$kitchen_row->kit_o[$key]['total_get_quantity'] = $get_item->total_get_quantity;
								if($kit_item->addon_id !='null'){					
									$addondetails = $this->site->getAddonByRecipeidAndOrderitemid_kot($kit_item->recipe_id,$kit_item->order_item_id);
								}								
								$kitchen_row->kit_o[$key]['addons'] = $addondetails ? $addondetails :'';
								//$kitchen_row->kit_o[$kitchen_row->id][] = $kit_item['quantity'];
							}
						}
						$consolidate_kitchens_kot['kitchens'][] = $kitchen_row;
					}
				}else{
					$consolidate_kitchens_kot[] = '';	
				}
       }
		return array( 'kitchen_data' => $kit, 'consolid_kitchen_data' => $consolid_kit, 'consolidate_kitchens_kot' => $consolidate_kitchens_kot); 
	}
	public function rePayment($update_bill = array(), $bill_id =NULL, $payment = array(), $multi_currency = array(), $salesid = NULL, $sales_bill = array(), $order_split_id = NULL, $notification_array,$updateCreditLimit,$total,$customer_id,$loyalty_used_points,$taxation,$customer_changed){  
		$q = $this->db->select('sales_split_id, sales_table_id')->where('id', $salesid)->get('sales');	
		$b = $this->db->select('id, order_type')->where('id', $bill_id)->get('bils');
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
		
       /*************************     ******************/		
		$this->db->select('sale_id , bill_id , GROUP_CONCAT(paid_by,"") act_tender,GROUP_CONCAT(amount,"") act_amount,created_by');
	    $this->db->where("bill_id",$bill_id);
		$p=$this->db->get("payments");
		if($p->num_rows()>0){
			$op=$p->row();
			$old_salesid=$op->sale_id;
			$old_billid=$op->bill_id;
			$actul_tender=$op->act_tender;
			$actul_amount=$op->act_amount;
			$old_user=$op->created_by;
		}
		/*********************   ***************************/
		$this->db->where("bill_id",$bill_id);
		$this->db->delete("payments");
		$this->db->where("bil_id",$bill_id);
		$this->db->delete("sale_currency");
		$resettle_tender='';
		$resettle_amount='';
		$created_by=$this->session->userdata('user_id');
    	if ($this->db->update('bils', $update_bill, array('id' => $bill_id))){
			$this->db->update('sales', $sales_bill, array('id' => $salesid));
    		foreach ($payment as $item) {
				$resettle_tender .= empty($resettle_tender)? $item['paid_by']:','.$item['paid_by'];
				$resettle_amount .= empty($resettle_amount)? $item['amount']:','.$item['amount'];
		        $item['customer_payment_type'] = $updateCreditLimit['customer_type'];
		        $this->db->insert('payments', $item);
		        $pid = $this->db->insert_id();
		    if($pid && $item['paid_by']=='credit'){
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
			    $postpaid_bill['due_date'] = date('Y-m-d H:i:s',strtotime('+'.$com['credit_days'].' days', strtotime(date('Y-m-d H:i:s'))));		
				$postpaid_bill['status'] = 9;
			    $this->db->insert('companies_postpaid_bills', $postpaid_bill);
			    $this->db->insert_id();
			}
			$this->db->set('credit_limit', 'credit_limit-'.$creditedAmt,false);
			$this->db->where('id',$updateCreditLimit['company_id']);
			$this->db->update('companies');//echo 'exit';exit;       
		    }
    		}
			   foreach ($multi_currency as $currency) {
    			  $this->db->insert('sale_currency', $currency);
    		   }
	    		$sales_array = array(
		            'sale_status' => "Closed",
		            'payment_status' => "Paid",
		        );
				$resettlement_log=array("bill_id"=>$old_billid,
				"sales_id"=>$old_salesid,
				"resettlement_date"=>date("Y-m-d"),
				"actual_tender"=>$actul_tender,
				"actual_amount"=>$actul_amount,
				"resettlement_tender"=>$resettle_tender,
				"resettlement_amount"=>$resettle_amount,
				"settled_user"=>$old_user,
				"resettle_user"=>$created_by,
				);
				$this->db->insert("resettlement_log",$resettlement_log);
		        if($customer_changed == 1){
		        	$this->site->UpdateCustomerFromLoyalty($customer_id,$bill_id,$salesid,$split_id);	       
		        }
		          $this->site->LoyaltyinserAndUpdate($bill_id,$total,$customer_id,$loyalty_used_points);	  
		      
    	 return $b->row();
    	}    	
    	return false;
    }   
	function get_comboItems($recipeId,$recipe_name){
		$combos=$this->site->get_comboItems($recipeId);
		if(!empty($combos)){
		foreach($combos as $row){
		   $recipe=$this->site->getrecipeByID($row->item_id);
		   $items['recipe_id']=$recipe->id;
		   $items['kitchen_type_id']=$recipe->kitchens_id;
		   $items['recipe_variant_id']='';
		   $items['recipe_name']='['.$recipe_name.']'.' '.$recipe->name;
		   $items['khmer_recipe_image']=$recipe->khmer_image;
		   $items['quantity']=$row->quantity;
		               $cm = $this->db->get_where('category_mapping',array('product_id'=>$recipe->id,'status'=>1))->row();
						$cate['category_id'] = $cm->id;
						$cate['category_id'] = $cm->category_id;
						$cate['subcategory_id'] = $cm->subcategory_id;
						$cate['brand_id'] = $cm->brand_id;
						if($this->Settings->procurment == 1){
							if($recipe->type =='standard' || $recipe->type =='production'){
								$this->site->updateStockMaster_new($items['recipe_id'],$items['recipe_variant_id'],$items['quantity'],$cate);
							}elseif($recipe->type =='quick_service'){
								$this->siteprocurment->production_salestock_out($items['recipe_id'],$items['quantity'],$items['recipe_variant_id']);
							}	
						}
		   $data[]=$items;
		}
		return $data;
	}
	return false;
	}
	
 function get_comboItemslist($recipeId,$recipe_name){
		$combos=$this->site->get_comboItems($recipeId);
		if(!empty($combos)){
		foreach($combos as $row){
		   $recipe=$this->site->getrecipeByID($row->item_id);
		   $items['recipe_id']=$recipe->id;
		   $items['kitchen_type_id']=$recipe->kitchens_id;
		   $items['recipe_variant_id']='';
		   $items['recipe_name']='['.$recipe_name.']'.' '.$recipe->name;
		   $items['khmer_recipe_image']=$recipe->khmer_image;
		   $items['quantity']=$row->quantity;
		   $data[]=$items;
		}
		return $data;
	}
	return false;
	}
	function  get_nkm_details($type,$id){
		switch($type){
			case 1:  
			$company=$this->getCompanyByID($id);
			$name=$company->name;
			$type="Customer";
			break;
			case 2: 
			$company=$this->getCompanyByID($id);
			$name=$company->name;
			$type="Company";
			break;
			case 3:
			$user=$this->site->getUserByID($id);
			$name=$user->first_name;
			$type="User";
			break;

		}
		return array("name"=>$name,"type"=>$type);
	}
	 function production_salestock_in($product_id,$stock_out_qty,$variant_id){
		// ingredient stock
       
        //if($item->type=="production" || $item->type=="quick_service" || $item->type=="semi_finished"){
           $q = $this->get_recipe_products($product_id,$variant_id); 
            if($q->num_rows()>0){
                foreach($q->result() as $k => $row){
                    $cate['category_id'] = $row->category_id;
                    $cate['subcategory_id'] = $row->subcategory_id;
                    $cate['brand_id'] = $row->brand_id;
                    $cate['cm_id'] = $row->cm_id;
					$ingredint_stock_out=$stock_out_qty *$this->site->unitToBaseQty($row->quantity,$row->operator,$row->operation_value);
                    $updated_stock = $this->cancelStockMaster_in($row->product_id,$variant_id,$ingredint_stock_out,$cate);
                }
            }
		
        //}   // die;    
		return true;
    }

	
    function cancelStockMaster_in($product_id,$variant_id,$base_quantity,$cate){   
        $store_id = $this->data['pos_store'];
        $batches =$this->getrawstock($product_id,$variant_id,$cate['category_id'],$cate['subcategory_id'],$cate['brand_id']); 
       foreach($batches as $batch){
			if($batch->stock_in>0){
				$query = 'update srampos_pro_stock_master set stock_in = stock_in + '.$base_quantity.',  stock_out = stock_out - '.$base_quantity.'  ,stock_status="closed" where store_id='.$this->store_id.' and id='.$batch->id;
                $this->db->query($query);
				break;
			}
		}
          return true;  
    }
	function wastageItem($recipeId,$variantId,$cancelQty,$OrderItemId){
		$recipe=$this->site->getrecipeByID($recipeId);
		switch($recipe->type){
			case "standard": 
			$this->wastageOrderItem($recipeId,$variantId,$cancelQty,$OrderItemId);
			break;
			case "production":
			$this->wastageOrderItem($recipeId,$variantId,$cancelQty,$OrderItemId);
			break;
			
			case "quick_service":
			$this->wastageQuickServiceItem($recipeId,$variantId,$cancelQty,$OrderItemId);
			
			break;
		}
		
	}
	
	
	
	function wastageOrderItem($recipeId,$variantId,$cancelQty,$OrderItemId){
		$recipe=$this->site->getrecipeByID($recipeId);
		$orderItem=$this->db->get_where("order_items",array("id"=>$OrderItemId))->row();
		$n = $this->siteprocurment->lastWastageId();
		$n=($n !=0)?$n+1:$this->store_id .'1';
		$reference = 'WTN'.str_pad($n, 8, 0, STR_PAD_LEFT);
		$date = date('Y-m-d H:i:s');
		$data=array("date"=>$date,
		"reference_no"=>$reference,
		"store_id"=>$this->store_id,
		"type"=>"Spoiled",
		"note"=>"Pos Order Item Cancel",
		"status"=>"approved",
		"created_by"=>$this->session->userdata('user_id'),
		"no_of_qty"=>$cancelQty,
		"no_of_items"=>1,
		"approved_by"=>$this->session->userdata('user_id'),
		"approved_on"=>$date,
		"created_on"=>$date		);
		$this->db->insert('wastage', $data);
			$wastage_id = $this->db->insert_id();
	        $unique_id = $this->site->generateUniqueTableID($wastage_id);
			if ($wastage_id) {
				$this->site->updateUniqueTableId($wastage_id,$unique_id,'wastage');
			}
			$item=array("store_id"=>$this->store_id,
			"wastage_id"=>$unique_id,
			"variant_id"=>$orderItem->recipe_variant_id,
			"net_unit_price"=>$orderItem->real_unit_price,
			"unit_price"=>$orderItem->subtotal,
			"wastage_unit_qty"=>$orderItem->unit_quantity,
			"product_unit_id"=>$orderItem->recipe_unit_id,
			"product_unit_code"=>$orderItem->recipe_unit_code,
			"cost_price"=>$recipe->cost,
			"gross_amount"=>$recipe->cost*$cancelQty,
			"net_amount"=>$recipe->cost*$cancelQty,
			"unit_quantity"=>$orderItem->unit_quantity,
			"category_id"=>$recipe->category_id,
			"subcategory_id"=>$recipe->subcategory_id,
			"brand_id"=>$recipe->brand,
			"product_code"=>$orderItem->recipe_code,
			"product_id"=>$orderItem->recipe_id,
			"product_name"=>$orderItem->recipe_name,
			"product_type"=>$orderItem->recipe_type,
			
			)
			$this->db->insert("wastage_items",$item);
		return true;
	}
	function  wastageQuickServiceItem($recipeId,$variantId,$cancelQty,$OrderItemId){
		$recipe=$this->site->getrecipeByID($recipeId);
		$orderItem=$this->db->get_where("order_items",array("id"=>$OrderItemId))->row();
		$n = $this->siteprocurment->lastWastageId();
		$n=($n !=0)?$n+1:$this->store_id .'1';
		$reference = 'WTN'.str_pad($n, 8, 0, STR_PAD_LEFT);
		$date = date('Y-m-d H:i:s');
		$data=array("date"=>$date,
		"reference_no"=>$reference,
		"store_id"=>$this->store_id,
		"type"=>"Spoiled",
		"note"=>"Pos Order Item Cancel",
		"status"=>"approved",
		"created_by"=>$this->session->userdata('user_id'),
		"no_of_qty"=>$cancelQty,
		"no_of_items"=>1,
		"approved_by"=>$this->session->userdata('user_id'),
		"approved_on"=>$date,
		"created_on"=>$date		);
		
		$item=array();
		
		  $q = $this->get_recipe_products($product_id,$variant_id); 
            if($q->num_rows()>0){
                foreach($q->result() as $k => $row){
					$quantity=$cancelQty *$this->site->unitToBaseQty($row->quantity,$row->operator,$row->operation_value);
					$item[]=array("store_id"=>$this->store_id,
					"wastage_id"=>$unique_id,
					"variant_id"=>$orderItem->recipe_variant_id,
					"net_unit_price"=>$orderItem->real_unit_price,
					"unit_price"=>$orderItem->subtotal,
					"wastage_unit_qty"=>$quantity,
					"product_unit_id"=>$orderItem->recipe_unit_id,
					"product_unit_code"=>$orderItem->recipe_unit_code,
					"cost_price"=>$recipe->cost,
					"gross_amount"=>$recipe->cost*$cancelQty,
					"net_amount"=>$recipe->cost*$cancelQty,
					"unit_quantity"=>$quantity,
					"category_id"=>$recipe->category_id,
					"subcategory_id"=>$recipe->subcategory_id,
					"brand_id"=>$recipe->brand,
					"product_code"=>$orderItem->recipe_code,
					"product_id"=>$orderItem->recipe_id,
					"product_name"=>$orderItem->recipe_name,
					"product_type"=>$orderItem->recipe_type,
			)
					
					
					
					
					
					
					
					
					
					
					
					
					
                  
                }
            }
		
	}
}
