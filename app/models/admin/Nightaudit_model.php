<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Nightaudit_model extends CI_Model{
    public function __construct(){
        parent::__construct();
    }
	public function getDataviewSales($dates = NULL, $warehouses_id = NULL){
		if(!empty($dates)){
			$current_date = $dates;
		}else{
			$current_date = date('Y-m-d');
		}
		if(!empty($warehouses_id)){
			$warehouses = $warehouses_id;
		}else{
			$warehouses = 1;
		}
		$this->db->select("grand_total, sale_status");
		$this->db->where('DATE(date)', $current_date);
		$this->db->where('warehouse_id', $warehouses);
		$q = $this->db->get('sales');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }	
	}
	
	public function checkbeforedate($dates = NULL, $warehouses_id = NULL){
		if(!empty($dates)){
			$current_date = $dates;
		}else{
			$current_date = date('Y-m-d');
		}
		
		if(!empty($warehouses_id)){
			$warehouses = $warehouses_id;
		}else{
			$warehouses = 1;
		}
		$this->db->where('DATE(nightaudit_date)', $current_date);
		$this->db->where('warehouse_id', $warehouses);
		$q = $this->db->get('nightaudit');
        if ($q->num_rows() > 0) {
			return $data = 'yes';
		}
		return $data = 'no';
	}

	public function checkNightaudit($dates = NULL, $warehouses_id = NULL){
		if(!empty($dates)){
			$current_date = $dates;
		}else{
			$current_date = date('Y-m-d');
		}
		
		if(!empty($warehouses_id)){
			$warehouses = $warehouses_id;
		}else{
			$warehouses = 1;
		}
		$this->db->where('DATE(nightaudit_date)', $current_date);
		$this->db->where('warehouse_id', $warehouses);
		$q = $this->db->get('nightaudit');
        if ($q->num_rows() > 0) {
			return $data = 'yes';
		}
		return $data = 'no';
	}
	
	function addNightaudit($data = array()){
		if ($this->db->insert('nightaudit', $data)){
			$insert_id= $this->db->insert_id();
			if($data['stock_audit']==1){
				$this->stockAduitProcess($data['nightaudit_date'],$insert_id);
			}
			return true;	
		}
        return false;
	}
    function Check_Not_Closed_Nightaudit(){
    	$Max_Date  = "SELECT DISTINCT  max(nightaudit_date)  AS lastdate 
		FROM " . $this->db->dbprefix('nightaudit') . " ";
		$MaxDate = $this->db->query($Max_Date);	
		 if ($MaxDate->num_rows() > 0) {		 	 
            foreach (($MaxDate->result()) as $row) {
                $lastdate = $row->lastdate;   
            }            
        }
        if(isset($lastdate)){
			$Miss_dates = "SELECT * FROM
				(
				SELECT DATE_ADD('".$lastdate."', INTERVAL t4+t16+t64+t256+t1024 DAY) missingDates 
				FROM 
				(SELECT 0 t4    UNION ALL SELECT 1   UNION ALL SELECT 2   UNION ALL SELECT 3  ) t4,
				(SELECT 0 t16   UNION ALL SELECT 4   UNION ALL SELECT 8   UNION ALL SELECT 12 ) t16,   
				(SELECT 0 t64   UNION ALL SELECT 16  UNION ALL SELECT 32  UNION ALL SELECT 48 ) t64,      
				(SELECT 0 t256  UNION ALL SELECT 64  UNION ALL SELECT 128 UNION ALL SELECT 192) t256,     
				(SELECT 0 t1024 UNION ALL SELECT 256 UNION ALL SELECT 512 UNION ALL SELECT 768) t1024     
				) b 
				WHERE
				missingDates NOT IN (SELECT DATE_FORMAT(nightaudit_date,'%Y-%m-%d')
				FROM " . $this->db->dbprefix('nightaudit') . "  GROUP BY nightaudit_date)
				AND
				missingDates <= DATE(NOW())";
				
			    $missdate = $this->db->query($Miss_dates);
			    if ($missdate->num_rows() > 0) {
			        foreach (($missdate->result()) as $row) {			        	
			            $misdate[] = $row->missingDates;
			        }
			        return $misdate;
			    }
			    return FALSE;
        }else{
	        	$date_format = 'Y-m-d';
				$yesterday = strtotime('-1 day');
				$previous_date = date($date_format, $yesterday);
			
				$lastdate = $previous_date;
				$Miss_dates = "SELECT * FROM
				(
				SELECT DATE_ADD('".$lastdate."', INTERVAL t4+t16+t64+t256+t1024 DAY) missingDates 
				FROM 
				(SELECT 0 t4    UNION ALL SELECT 1   UNION ALL SELECT 2   UNION ALL SELECT 3  ) t4,
				(SELECT 0 t16   UNION ALL SELECT 4   UNION ALL SELECT 8   UNION ALL SELECT 12 ) t16,   
				(SELECT 0 t64   UNION ALL SELECT 16  UNION ALL SELECT 32  UNION ALL SELECT 48 ) t64,      
				(SELECT 0 t256  UNION ALL SELECT 64  UNION ALL SELECT 128 UNION ALL SELECT 192) t256,     
				(SELECT 0 t1024 UNION ALL SELECT 256 UNION ALL SELECT 512 UNION ALL SELECT 768) t1024     
				) b 
				WHERE
				missingDates NOT IN (SELECT DATE_FORMAT(nightaudit_date,'%Y-%m-%d')
				FROM
				" . $this->db->dbprefix('nightaudit') . " GROUP BY nightaudit_date)
				AND
				missingDates <= DATE(NOW())";
				
			    $missdate = $this->db->query($Miss_dates);
			    if ($missdate->num_rows() > 0) {
			        foreach (($missdate->result()) as $row) {			        	
			            $misdate[] = $row->missingDates;
			        }
			        return $misdate;
			    }
			    return FALSE;
        }

    }
    function Last_Nightaudit(){

    	$Max_Date  = "SELECT DISTINCT  max(nightaudit_date)  AS lastdate 
		FROM " . $this->db->dbprefix('nightaudit') . " ";

		$MaxDate = $this->db->query($Max_Date);	

		 if ($MaxDate->num_rows() > 0) {		 	 
            foreach (($MaxDate->result()) as $row) {
                $lastdate = $row->lastdate;   
            }   
            return $lastdate;         
        }
        return FALSE;        
    }    
    public function getUserGroupid($user_id)
    {

        $this->db->select('group_id')
            ->where('id', $user_id);
        $q = $this->db->get('users');
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
	
	public function getNegativeStock(){
		$query=('SELECT '.$this->db->dbprefix('pro_stock_master') .'.*, `r`.`name` AS `productname`, `b`.`name` AS `brand_name`, `rc`.`name` AS `category_name`, `rsc`.`name` AS `subcategory_name`, `rv`.`name` AS `variant`, `u`.`name` AS `p_uom`, `b`.`name` AS `brand`,
		CASE 
			WHEN `u`.`operator` ="*" THEN '.$this->db->dbprefix('pro_stock_master').'.`stock_in`/`u`.`operation_value`
			WHEN `u`.`operator` ="/" THEN '.$this->db->dbprefix('pro_stock_master').'.`stock_in`*`u`.`operation_value`
			WHEN `u`.`operator` ="+" THEN '.$this->db->dbprefix('pro_stock_master').'.`stock_in`-`u`.`operation_value`
			WHEN `u`.`operator` ="-" THEN '.$this->db->dbprefix('pro_stock_master').'.`stock_in`+`u`.`operation_value`
			ELSE '.$this->db->dbprefix('pro_stock_master').'.`stock_in` END AS `stock` 
			FROM '.$this->db->dbprefix('pro_stock_master').'
			LEFT JOIN '.$this->db->dbprefix('recipe_categories').' `rc` ON `rc`.`id`='.$this->db->dbprefix('pro_stock_master').'.`category_id`
			LEFT JOIN '.$this->db->dbprefix('recipe_categories').'`rsc` ON `rsc`.`id`='.$this->db->dbprefix('pro_stock_master').'.`subcategory_id` 
			LEFT JOIN '.$this->db->dbprefix('brands').'  `b` ON `b`.`id`='.$this->db->dbprefix('pro_stock_master').'.`brand_id`
			LEFT JOIN '.$this->db->dbprefix('recipe_variants').' `rv` ON `rv`.`id`='.$this->db->dbprefix('pro_stock_master').'.`variant_id` LEFT JOIN `srampos_recipe` `r` ON `r`.`id`='.$this->db->dbprefix('pro_stock_master').'.`product_id` 
			LEFT JOIN '.$this->db->dbprefix('units').'  `u` ON `u`.`id`=`r`.`purchase_unit` WHERE `store_id` = '.$this->store_id
			.' AND '."(".'`expiry_date` <= '.'"'.date("Y-m-d").'"'.' or `expiry_date` IS NULL '.")".'and `stock_in` <0  and parent_unique_id is null');
		$q=$this->db->query($query);
	
		if($q->num_rows()>0){
		      foreach($q->result() as $row)	{
				  $data[]=$row;
			  }
			return $data;
			
		}
		return  false;
	}
   public function carryForward($stockId){
	   $carry_forward_days=($this->Settings->negative_stock_carry_forward)?$this->Settings->negative_stock_carry_forward:1;
	   $carry_fordward_date=date('Y-m-d', strtotime($dates . ' '.$carry_forward_days.' day'));
	   $this->db->where("unique_id",$stockId);
	   if($this->db->update("pro_stock_master",array("expiry_date"=>$carry_fordward_date))){
		   return true;
	   }else{
		   return false;
	   }
	   
   }
  public function getStockDetails($stockid){
	  $q=$this->db->get_where("pro_stock_master",array("unique_id"=>$stockid));
	  if($q->num_rows()>0){
		  return $q->row();
	  }
	  return false;
  }
    public function generateInvoice($data,$item,$stockid){
	
		if ($this->db->insert('pro_purchase_invoices', $data)) {
            $id = $this->db->insert_id();
			$UniqueID                  = $this->site->generateUniqueTableID($id);
			$this->site->updateUniqueTableId($id,$UniqueID,'pro_purchase_invoices');
                /*** insert invoice items **/
				$item['invoice_id'] = $UniqueID;
				$cp = str_replace('.','_',$item['cost']);
                $this->db->insert('pro_purchase_invoice_items', $item);
                $this->db->where("unique_id",$stockid);
				$this->db->update("pro_stock_master ",array("parent_unique_id"=>0));
            return true;
        }
        return false;
	}

	public function generateStockRequest($data,$item,$stockId){
		
	if ($this->db->insert('pro_store_request', $data)) {
            $store_request_id = $this->db->insert_id();
        if ($store_request_id) {            
            $unique_id = $this->site->generateUniqueTableID($store_request_id);
            if ($store_request_id) {
                $this->site->updateUniqueTableId($store_request_id,$unique_id,'pro_store_request');
            }
                $item['store_request_id'] = $unique_id;
                $this->db->insert('pro_store_request_items', $item);
				
				$i_request_id = $this->db->insert_id();
                $i_unique_id = $this->site->generateUniqueTableID($i_request_id);
                if ($i_request_id) {
                    $this->site->updateUniqueTableId($i_request_id,$i_unique_id,'pro_store_request_items');
                }
				 $this->db->where("unique_id",$stockId);
				$this->db->update("pro_stock_master ",array("parent_unique_id"=>0));
			
            return true;
        }
        return false;
    }
	}
	function ongoingOrder(){
		$q=$this->db->get_where("restaurant_tables",array("current_order_status !=0"));
		if($q->num_rows()>0){
			return $q->result();
		}
		return false;
	}
	function inProcessOrder(){
		$q=$this->db->get_where("orders",array("payment_status IS NULL"));
		if($q->num_rows()>0){
			return $q->result();
		}
		return false;
	}
	
	function inProcessBill(){
		$q=$this->db->get_where("bils",array("payment_status IS NULL"));
		if($q->num_rows()>0){
			return $q->result();
		}
		return false;
		
	}
	function stockAduitProcess($nightAudit_date,$nightAuditId){
		$q=$this->db->select("*");
		$this->db->where_in("type",array("standard","production","addon","semi_finished","raw","service"));
		$q=$this->db->get("recipe");
		if($q->num_rows()>0){
			foreach($q->result() as $row){
				$categoryMapping=$this->getRecipeMapping($row->id);
				  foreach($categoryMapping as $recipe){
						$stock    = $this->getStock($recipe->product_id,$recipe->variant_id,$recipe->category_id,$recipe->subcategory_id,$recipe->brand_id); 
						if($recipe->type !="production"){
							$purchase  =$this->getPurchase($recipe->product_id,$recipe->variant_id,$recipe->category_id,$recipe->subcategory_id,$recipe->brand_id,$nightAudit_date);
						}else{
							$production=$this->getproduction($recipe->product_id,$recipe->variant_id,$recipe->category_id,$recipe->subcategory_id,$recipe->brand_id,$nightAudit_date);
							$purchase=$production;
						}
						
						$transfer =$this->getTransfer($recipe->product_id,$recipe->variant_id,$recipe->category_id,$recipe->subcategory_id,$recipe->brand_id,$nightAudit_date);
						$receiver =$this->getReceiver($recipe->product_id,$recipe->variant_id,$recipe->category_id,$recipe->subcategory_id,$recipe->brand_id,$nightAudit_date);
						$wastage  =$this->getWastage($recipe->product_id,$recipe->variant_id,$recipe->category_id,$recipe->subcategory_id,$recipe->brand_id,$nightAudit_date);
						$sale=$this->getsales($recipe->product_id,$recipe->variant_id,$recipe->category_id,$recipe->subcategory_id,$recipe->brand_id,$nightAudit_date);
						$stock_quantity      =($stock->stock)?$stock->stock:0;
						$purchase_quantity   =($purchase->quantity)?$purchase->quantity:0;
						$transfer_quantity   =($transfer->quantity)?$transfer->quantity:0;
						$receiver_quantity   =($receiver->quantity)?$receiver->quantity:0;
						$wastage_quantity    =($wastage->quantity)?$wastage->quantity:0;
						$sale_quantity       =($sale->quantity)?$sale->quantity:0;
						$opening_stock       =($stock_quantity+$wastage_quantity+$transfer_quantity+$sale_quantity)-($purchase_quantity+$receiver_quantity);
						$closing_stock       =$stock_quantity;
						$item=array("date"=>date("Y-m-d H:i:s"),
						"store_id"=>$this->store_id,
						"night_audit_date"=>$nightAudit_date,
						"nightaudit_id"=>$nightAuditId,
						"product_id"=>$recipe->product_id,
						"variant_id"=>$recipe->variant_id,
						"category_id"=>$recipe->category_id,
						"sub_category_id"=>$recipe->subcategory_id,
						"brand_id"=>$recipe->brand_id,
						"opening_stock"=>$opening_stock,
						"stock_uom"=>$row->unit,
						"sales_stock"=>$sale_stock,
						"purchase_stock"=>$purchase_quantity,
						"store_transfer_stock"=>$transfer_quantity,
						"store_receiver_stock"=>$receiver_quantity,
						"wastage_stock"=>$wastage_quantity,
						"closing_stock"=>$closing_stock,
						"created_on"=>date("Y-m-d H:i:s"),
						"created_by"=>$this->session->userdata('user_id'),
						"brand_name"=>$recipe->brand_name,
						"category_name"=>$recipe->category_name,
						"subcategory_name"=>$recipe->subcategory_name,
						"variant_name"=>$recipe->variant);
						$this->db->insert("stock_audit",$item);
						
				        $insertID= $this->db->insert_id();
						$UniqueID                  = $this->site->generateUniqueTableID($insertID);
						$this->site->updateUniqueTableId($insertID,$UniqueID,'stock_audit');
			   }
			   $this->db->where("id",$nightAuditId);
			   $this->db->update("nightaudit",array("stock_audit_status"=>"Completed"));
			   
			   //api_log clear
			      $old_date=date('Y-m-d', strtotime('+30 day', strtotime($nightAudit_date)));
			      $this->db->where("date(".$this->db->dbprefix('api_logs')."created_on) <",$old_date);
			      $this->db->delete("api_logs");
			   //orderitem ingredient clear
			      $this->db->where("date(".$this->db->dbprefix('pos_orderitem_ingredient')."created_on) <",$old_date);
			      $this->db->delete("pos_orderitem_ingredient");
			   
			   
			}
			return true;
		}
		return false;
	}
	function  getRecipeMapping($recipe_id){
		$this->db->select("category_mapping.*,b.name as brand_name,rc.name as category_name,rsc.name as subcategory_name,rv.name as variant,r.type");
		$this->db->join('recipe_categories as rc','rc.id=category_mapping.category_id','left');
		$this->db->join('recipe_categories rsc','rsc.id=category_mapping.subcategory_id','left');	
		$this->db->join('brands b','b.id=category_mapping.brand_id','left');
		$this->db->join('recipe r','r.id=category_mapping.product_id','left');
		$this->db->join('recipe_variants rv','rv.id=category_mapping.variant_id','left');
		$this->db->where("product_id",$recipe_id);
		$this->db->where("store_id",$this->store_id);
		$this->db->group_by(array("product_id", "variant_id","category_id", "subcategory_id","brand_id"));
		$q=$this->db->get("category_mapping ");
		
		if($q->num_rows()>0){
			foreach($q->result() as $row){
				$data[]=$row;
			}
			return $data;
		}
		return false;
	}
	
	function getStock($recipe_id,$variant_id,$catgory_id,$subcategory_id,$brand_id){
		$this->db->select("sum(stock_in) as stock");
		$this->db->where(array("store_id"=>$this->store_id,
		"product_id"=>$recipe_id,
		"variant_id"=>$variant_id,
		"category_id"=>$catgory_id,
		"subcategory_id"=>$subcategory_id,
		"brand_id"=>$brand_id));
		$q=$this->db->get("pro_stock_master");
		if($q->num_rows()>0){
			return $q->row();
		}
		return false;
	}
	function getPurchase($recipe_id,$variant_id,$catgory_id,$subcategory_id,$brand_id,$nightaudit_date){
		$this->db->select("sum(".$this->db->dbprefix('pro_grn_items').".unit_quantity ) as quantity");
		$this->db->join("pro_grn G","G.id=pro_grn_items.grn_id");
		$this->db->where('date(date)', $nightaudit_date);
		$this->db->where('G.status !="process"');
		$this->db->where(array("pro_grn_items.store_id"=>$this->store_id,
		"product_id"=>$recipe_id,
		"variant_id"=>$variant_id,
		"category_id"=>$catgory_id,
		"subcategory_id"=>$subcategory_id,
		"brand_id"=>$brand_id));
		$q=$this->db->get("pro_grn_items");
		if($q->num_rows()>0){
			return $q->row();
		}
		return false;
	}
	function getproduction($recipe_id,$variant_id,$catgory_id,$subcategory_id,$brand_id,$nightaudit_date){
		$this->db->select("sum(".$this->db->dbprefix('pro_production_items').".base_quantity ) as quantity");
		$this->db->join("pro_production G","G.id=pro_production_items.production_id");
		$this->db->where('date(date)', $nightaudit_date);
		$this->db->where('G.status !="process"');
		$this->db->where(array("pro_production_items.store_id"=>$this->store_id,
		"product_id"=>$recipe_id,
		"variant_id"=>$variant_id,
		"category_id"=>$catgory_id,
		"subcategory_id"=>$subcategory_id,
		"brand_id"=>$brand_id));
		$q=$this->db->get("pro_production_items");
	
		if($q->num_rows()>0){
			return $q->row();
		}
		return false;
	}
	function getTransfer($recipe_id,$variant_id,$catgory_id,$subcategory_id,$brand_id,$nightaudit_date){
		$this->db->select("sum(".$this->db->dbprefix('pro_store_transfer_item_details').".transfer_unit_qty ) as quantity");
		$this->db->join("pro_store_transfers ","pro_store_transfers.id=pro_store_transfer_item_details.store_transfer_id");
		$this->db->join("pro_store_transfer_items ","pro_store_transfer_items.id=pro_store_transfer_item_details.store_transfer_item_id");
		$this->db->where('date(created_on) ', $nightaudit_date);
		$this->db->where($this->db->dbprefix('pro_store_transfer_item_details').'.variant_id ', $variant_id);
		$this->db->where('status !="process"');
		$this->db->where($this->db->dbprefix('pro_store_transfer_item_details').".store_id",$this->store_id);
		$this->db->where(array(
		"product_id"=>$recipe_id,
		"category_id"=>$catgory_id,
		"subcategory_id"=>$subcategory_id,
		"brand_id"=>$brand_id));
		$q=$this->db->get("pro_store_transfer_item_details");
		if($q->num_rows()>0){
			return $q->row();
		}
		return false;
	}
	function getReceiver($recipe_id,$variant_id,$catgory_id,$subcategory_id,$brand_id,$nightaudit_date){
		$this->db->select("sum(".$this->db->dbprefix('pro_store_receiver_item_details').".received_unit_qty ) as quantity");
		$this->db->join("pro_store_receivers R","R.id=pro_store_receiver_item_details.store_receiver_id");
		$this->db->join("pro_store_receiver_items ","pro_store_receiver_items.id=pro_store_receiver_item_details.store_receiver_item_id");
		$this->db->where('date(date) ', $nightaudit_date);
		$this->db->where($this->db->dbprefix('pro_store_receiver_item_details').'.variant_id ', $variant_id);
		$this->db->where('R.status !="process"');
		$this->db->where($this->db->dbprefix('pro_store_receiver_item_details').".store_id",$this->store_id);
		$this->db->where(array(
		"product_id"=>$recipe_id,
		"category_id"=>$catgory_id,
		"subcategory_id"=>$subcategory_id,
		"brand_id"=>$brand_id));
		$q=$this->db->get("pro_store_receiver_item_details ");
		if($q->num_rows()>0){
			return $q->row();
		}
		return false;
	}
	
	function getWastage($recipe_id,$variant_id,$catgory_id,$subcategory_id,$brand_id,$nightaudit_date){
		$this->db->select("sum(".$this->db->dbprefix('wastage_items').".wastage_unit_qty ) as quantity");
		$this->db->join("wastage W","W.id=wastage_items.wastage_id","left");
		$this->db->where('date(date) ', $nightaudit_date);
		$this->db->where('W.status !="process"');
	    $this->db->where("W.store_id",$this->store_id);
		$this->db->where(array(
		"product_id"=>$recipe_id,
		"variant_id"=>$variant_id,
		"category_id"=>$catgory_id,
		"subcategory_id"=>$subcategory_id,
		"brand_id"=>$brand_id));
		$q=$this->db->get("wastage_items");
		if($q->num_rows()>0){
			return $q->row();
		}
		return false;
	}
	
	function getsales($recipe_id,$variant_id,$catgory_id,$subcategory_id,$brand_id,$nightaudit_date){
		$this->db->select("sum(".$this->db->dbprefix('pos_orderitem_ingredient').".unit_quantity ) as quantity");
		$this->db->where('date(created_on) ', $nightaudit_date);
	    $this->db->where("store_id",$this->store_id);
		$this->db->where(array(
		"recipe_id"=>$recipe_id,
		"variant_id"=>$variant_id,
		"category_id"=>$catgory_id,
		"subcategory_id"=>$subcategory_id,
		"brand_id"=>$brand_id));
		$q=$this->db->get("pos_orderitem_ingredient");/* 
		echo $this->db->last_query();
		die; */
		if($q->num_rows()>0){
			return $q->row();
		}
		return false;
	}
}