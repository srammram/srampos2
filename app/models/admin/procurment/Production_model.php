<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Production_model extends CI_Model{
    public function __construct()
    {
        parent::__construct();
    }

   public function addProduction($data = array(), $items = array()){
		$this->db->insert('pro_production', $data);
		$production_id = $this->db->insert_id();   
			if($production_id){    		
				foreach ($items  as $item) {
					$item['production_id'] = $production_id;
					$this->db->insert('pro_production_items', $item);
					if($data['status']=='approved'){
						 $cate['category_id']    = $item['category_id'];
						 $cate['subcategory_id'] = $item['subcategory_id'];
						 $cate['brand_id']       = $item['brand_id'];
				     	 $cate['store_id']       = $item['store_id'];
				 		 $cate['product_id']     = $item['product_id'];
						 $cate['vendor_id']      = 0;
						 $cate['invoice_id']     = 0;
						 $cate['pieces_selling_price'] = 0;
						 $cate['selling_price']      = 0;
						 // Ingredient stock out
						$cate['purchase_cost']= $this->productionStockOut($item['product_id'],$item['variant_id'],$item['quantity'],$item['base_quantity'],$item['uom']);
						
						// ProductionItem Stock In
						$this->updateStockMaster_new($item['product_id'],$item['variant_id'],$item['base_quantity'],$cate); // $category_mappingID
					}				
				}
			} 
		return true;
    }
	
	public function updateproduction_new($id, $data, $items = array()){
            if ($this->db->update('pro_production', $data, array('id' => $id)) && $this->db->delete('pro_production_items', array('production_id' => $id))) {
			foreach ($items  as $item) {
					 $item['production_id'] = $id;
					$this->db->insert('pro_production_items', $item);// print_r($this->db->error());die;
					if($data['status']=='approved'){
						 $cate['category_id']    = $item['category_id'];
						 $cate['subcategory_id'] = $item['subcategory_id'];
						 $cate['brand_id']       = $item['brand_id'];
						 $cate['store_id']       = $item['store_id'];
						 $cate['product_id']     = $item['product_id'];
						 $cate['vendor_id']      = 0;
						 $cate['invoice_id']           = 0;
						 $cate['pieces_selling_price'] = 0;
						 $cate['selling_price']        = 0;
						 // Ingredient stock out
						 $cate['purchase_cost']= $this->productionStockOut($item['product_id'],$item['variant_id'],$item['quantity'],$item['base_quantity'],$item['uom']);
						// ProductionItem Stock In
						$this->updateStockMaster_new($item['product_id'],$item['variant_id'],$item['base_quantity'],$cate); 
					}				
				}
            return true;
        }
        return false;
    }
	
	
	
	function productionStockOut($productid,$variantid,$quantity,$base_quantity,$item_uom){
		$this->db->select("*");
		$this->db->where("recipe_id",$productid);
		if(empty($variantid)){
			$this->db->where("variant_id",$variantid);
		}
		$rp=$this->db->get("ingrediend_head");
		if($rp->num_rows()>0){
			$rp_details=$rp->row();
			$rp_uom=$this->siteprocurment->getUnitByID($rp_details->uom);
			$rp_items=$this->db->get_where("recipe_products",array("ingrediends_hd_id"=>$rp_details->id));
			$cost=0;
			if($rp_items->num_rows()>0){
				foreach($rp_items->result() as $row){
							 $base_quantity=$row->quantity*$quantity;
							 $r_cost          = $this->productionItemStockout($row->product_id,$row->variant_id,$row->category_id,$row->sub_category_id,$row->brand_id,$base_quantity); 
							 $cost +=$r_cost;
				}	
                return $cost;				
			}
		}
		return false;
	}
	function productionItemStockout($product_id,$variant_id,$category_id,$subcategory_id,$brand_id,$base_quantity){
		$batches=$this->getBatchwisestock($product_id,$variant_id,$category_id,$subcategory_id,$brand_id);
		if(!empty($batches)){
			$total_row=count($batches);
			$row=1;
		foreach($batches as $batch){
			if($total_row==$row){
				$query = 'update srampos_pro_stock_master set stock_in = stock_in - '.$base_quantity.',  stock_out = stock_out + '.$base_quantity.' where store_id='.$this->store_id.' and id='.$batch->id;
                    $this->db->query($query); 
					$stock=$this->db->get_where("pro_stock_master",array("id"=>$batch->id))->row();
					 $recipe_cost=$stock->cost_price;
					break;
			}else{
				$balance_quantity =$base_quantity-$batch->stock_in;
				if($balance_quantity>0){
				$query = 'update srampos_pro_stock_master set stock_in = stock_in - '.$batch->stock_in.',  stock_out = stock_out + '.$batch->stock_in.'  ,stock_status="closed" where store_id='.$this->store_id.' and id='.$batch->id;
                $this->db->query($query); 
				$base_quantity =$base_quantity-$batch->stock_in;
				$stock=$this->db->get_where("pro_stock_master",array("id"=>$batch->id))->row();
				}else{
					$query = 'update srampos_pro_stock_master set stock_in = stock_in - '.$base_quantity.',  stock_out = stock_out + '.$base_quantity.' where store_id='.$this->store_id.' and id='.$batch->id;
                    $this->db->query($query); 
					$stock=$this->db->get_where("pro_stock_master",array("id"=>$batch->id))->row();
					 $recipe_cost=$stock->cost_price;
					break;
				}
			}
			$row++;
		}
		}else{
			$rawstock =$this->getrawstock_empty($product_id,$variant_id,$category_id,$subcategory_id,$brand_id); 
			 foreach($rawstock as $row){
				 $query = 'update srampos_pro_stock_master set stock_in=stock_in - '.$base_quantity.', stock_out = stock_out + '.$base_quantity.'  where id='.$row->id;
                    $this->db->query($query); 
                    $stock_id = $row->id;
					$stock=$this->db->get_where("pro_stock_master",array("id"=>$row->id))->row();
					 $recipe_cost=$stock->cost_price;
					break;
			 }
		}
		return $recipe_cost;
	}
    public function getProductOptions($product_id){
        $q = $this->db->get_where('recipe_variants', array('recipe_id' => $product_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    public function getProductNames($term,$limit = 10){
	$type = array('production');//,'standard'
	$this->db->select('r.*,t.rate as purchase_tax_rate,b.name as brand_name,rc.name as category_name,rsc.name as subcategory_name,cm.category_id,cm.subcategory_id,cm.brand_id,cm.purchase_cost,cm.selling_price as cost');
	$this->db->from('recipe r');
	$this->db->join('category_mapping as cm','cm.product_id=r.id','left');
	$this->db->join('recipe_categories as rc','rc.id=cm.category_id','left');
	$this->db->join('recipe_categories rsc','rsc.id=cm.subcategory_id','left');	
	$this->db->join('brands b','b.id=r.brand','left');
	$this->db->join('tax_rates t','r.purchase_tax=t.id','left');
    $this->db->where("(r.name LIKE '%" . $term . "%' OR r.code LIKE '%" . $term . "%' OR  concat(r.name, ' (', r.code, ')') LIKE '%" . $term . "%')");
        $this->db->where_in('r.type',$type);
		$this->db->limit($limit);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
	// Production Suggestions
	public function getProductNamesNew($term,$limit = 10){		
		$type = array('production','semi_finished', 'quick_service'); //,'standard'
		$this->db->select('r.*,t.rate as purchase_tax_rate,b.name as brand_name,rc.name as category_name,rsc.name as subcategory_name,cm.category_id,cm.subcategory_id,cm.brand_id,cm.purchase_cost,cm.id as cm_id,cm.selling_price as cost,IFNULL(rvv.price,0) as variant_price,IFNULL(rv.name,"") as variant_name,IFNULL(rv.id,0) as variant_id');
		$this->db->from('recipe r');
		$this->db->join('recipe_products rp', 'rp.recipe_id=r.id');
		$this->db->join('recipe_variants_values rvv', 'rp.recipe_id=rvv.recipe_id', 'left');
		$this->db->join('recipe_variants rv', 'rp.variant_id=rv.id', 'left');
		$this->db->join('category_mapping as cm','cm.product_id=r.id','left');
		$this->db->join('recipe_categories as rc','rc.id=cm.category_id','left');
		$this->db->join('recipe_categories rsc','rsc.id=cm.subcategory_id','left');	
		$this->db->join('brands b','b.id=cm.brand_id','left');
		$this->db->join('tax_rates t','r.purchase_tax=t.id','left');
		if($this->Settings->item_search ==1){
		$this->db->where("(r.name LIKE '%" . $term . "%' OR r.code LIKE '%" . $term . "%' OR  concat(r.name, ' (', r.code, ')') LIKE '%" . $term . "%')");
		}else{
		 $this->db->where("(r.name LIKE '" . $term . "%' OR r.code LIKE '" . $term . "%' OR  concat(r.name, ' (', r.code, ')') LIKE '" . $term . "%')");
		}
		$this->db->where_in('r.type',$type);
		$this->db->group_by('r.id,rv.id');
		$this->db->limit($limit);		
		$q = $this->db->get();	
		//print_r($this->db->last_query());die;
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }			
            return $data;			
        }
        return FALSE;
    }
    function getProductionByID($id){
	 $q = $this->db->get_where('pro_production', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
function getAllProductionItemsWithDetails($p_id){
   // $type = array('production','semi_finished', 'quick_service'); //,'standard'
	$this->db->select('p.*,t.rate as purchase_tax_rate,b.name as brand_name,rc.name as category_name,rsc.name as subcategory_name,cm.category_id,cm.subcategory_id,cm.brand_id,cm.purchase_cost,cm.id as cm_id,cm.selling_price as cost,IFNULL(rvv.price,0) as variant_price,IFNULL(rv.name,"") as variant_name,IFNULL(rv.id,0) as variant_id');
	$this->db->from('pro_production_items p');
	$this->db->join('recipe as r', 'r.id=p.product_id', 'left');
	$this->db->join('recipe_products rp', 'rp.recipe_id=r.id');
	$this->db->join('recipe_variants_values rvv', 'rp.recipe_id=rvv.recipe_id', 'left');
    $this->db->join('recipe_variants rv', 'rp.variant_id=rv.id', 'left');
	$this->db->join('category_mapping as cm','cm.product_id=r.id','left');
	$this->db->join('recipe_categories as rc','rc.id=cm.category_id','left');
	$this->db->join('recipe_categories rsc','rsc.id=cm.subcategory_id','left');	
	$this->db->join('brands b','b.id=cm.brand_id','left');
	$this->db->join('tax_rates t','r.purchase_tax=t.id','left');
    //$this->db->where_in('r.type',$type);
    $this->db->where(array('p.production_id' => $p_id));
    $this->db->order_by('p.id', 'asc');
    $q = $this->db->get();	
    	//print_r($this->db->last_query());die;
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
   } 
    function getAllProductionItemsWithDetails123($p_id){
	    $this->db->select('p.*');
        $this->db->from('pro_production_items as p');
	    $this->db->join('recipe as r', 'r.id=p.product_id', 'left');
        $this->db->order_by('id', 'asc');
        $this->db->where(array('production_id' => $p_id));
	    $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }
   public function updateProduction($id, $data, $items = array()){
     if ($this->db->update('pro_production', $data, array('id' => $id)) && $this->db->delete('pro_production_items', array('production_id' => $id))) {
            foreach ($items as $item) {
                $item['production_id'] = $id;
                $this->db->insert('pro_production_items', $item);
				if($data['status']=='approved'){
						$cate['category_id'] = $item['category_id'];
						$cate['subcategory_id'] = $item['subcategory_id'];
						$cate['brand_id'] = $item['brand_id'];
						$this->siteprocurment->production_salestock_out($item['product_id'],$item['base_quantity'],$item['variant_id']);
						$this->siteprocurment->product_stockIn($item['product_id'],$item['base_quantity'],$cate);
						$this->updateStockMaster($item['product_id'],$item['base_quantity'],$cate); // ,$category_mappingID);
		       }
            }
            return true;
        }
        return false;
    }
    function updateStockMaster($pro_id,$variant_id,$qty,$cate=null){	
		$store_id = $this->session->userdata('warehouse_id');
		$date =date('Y-m-d h:m:s');
			$q = $this->db->get_where('srampos_pro_stock_master', array('product_id' => $pro_id,'variant_id' => $variant_id));
			 if ($q->num_rows() > 0) {
			 	$id = $q->row('id');
				  	$query ='update srampos_pro_stock_master set stock_in = stock_in + '.$qty.' where store_id='.$store_id.' AND product_id='.$pro_id.' AND variant_id='.$variant_id.''; //  AND batch=IS NULL
					$this->db->query($query);
			      }else{
					$query ='insert into srampos_pro_stock_master(store_id, product_id,variant_id, cm_id, category_id, subcategory_id, brand_id, stock_in, stock_out)values('.$store_id.','.$pro_id.','.$variant_id.', 0, '.$cate['category_id'].', '.$cate['subcategory_id'].', '.$cate['brand_id'].', '.$qty.', 0)';
					$this->db->query($query);
					$id = $this->db->insert_id();
			  } 	
		return true;
    }
	
  function updateStockMaster_new($pro_id,$variant_id,$qty,$cate=null){	
					$store_id             = $this->session->userdata('warehouse_id');
					$unique_id            = $store_id.$pro_id.$variant_id.$cate['category_id'].$cate['subcategory_id'].$cate['brand_id'].strtotime("now");
					$date                 = date('Y-m-d h:m:s');
					$batch                = strtotime("now");
					$cate['batch_no']     = $batch;
					$cate['unique_id']    = $unique_id;
					$expiry=$this->site->getExpiryDate($pro_id);
					$this->db->insert("category_mapping",$cate);
					$cm_id=$this->db->insert_id();
		         	$UniqueID             = $this->site->generateUniqueTableID($cm_id);
			        $this->site->updateUniqueTableId($cm_id,$UniqueID,'category_mapping');
					$query ='insert into srampos_pro_stock_master(store_id, product_id,variant_id, cm_id, category_id, subcategory_id, brand_id, stock_in, stock_out,unique_id,batch,cost_price,expiry_date,invoice_date,stock_status)values('.$store_id.','.$pro_id.','.$variant_id.', '.$cm_id.', '.$cate['category_id'].', '.$cate['subcategory_id'].', '.$cate['brand_id'].', '.$qty.', 0,'.$unique_id.','.$batch.','.$cate['purchase_cost'].','."'".$expiry."'".','."'".$date."'".',"available")';
					$this->db->query($query);
					$insertID                  = $this->db->insert_id();
					$UniqueID                  = $this->site->generateUniqueTableID($insertID);
					$this->site->updateUniqueTableId($insertID,$UniqueID,'pro_stock_master');

		return true;
    }

    function production_salestock_out($product_id,$stock_out_qty){
		$item = $this->getrecipeByID($product_id);
		if($item->type=="production"){
		   $q = $this->get_recipe_products($product_id);		   
			if($q->num_rows()>0){
				foreach($q->result() as $k => $row){
					 $cate['category_id']    = $row->category_id;
				     $cate['subcategory_id'] = $row->subcategory_id;
				     $cate['brand_id']       = $row->brand_id;
				     $cate['cm_id']          = $row->cm_id;
					 $mapped_item_qty     =  $stock_out_qty * $row->quantity;
					 $updated_stock       = $this->productionupdateStockMaster($row->product_id,$mapped_item_qty,$cm_id,$cate);
				}
			}
		}
		
    }
    function productionupdateStockMaster($product_id,$stock_out,$cm_id,$cate){
		$piece = $this->db->get_where('recipe', array('id' =>$product_id))->row('piece');
		$store_id = $this->data['pos_store'];
		$q = $this->db->get_where('srampos_pro_stock_master', array('product_id' => $product_id));
		if ($q->num_rows() > 0) {
			$id = $q->row('id');							
			$query = 'update srampos_pro_stock_master set  stock_out = stock_out + '.$stock_out.', stock_out_piece = stock_out_piece + '.$stock_out * $piece.' where id='.$id;
			$this->db->query($query);	
			//print_r($this->db->last_query());
			}else{						
			$query ='insert into srampos_pro_stock_master(store_id, product_id, cm_id, category_id, subcategory_id, brand_id, stock_out)values('.$store_id.','.$product_id.', 0, '.$cate['category_id'].', '.$cate['subcategory_id'].', '.$cate['brand_id'].', stock_out + '.$stock_out.')';
			$this->db->query($query);
		}  
			return $order_item;
    }
	public function checkproductionsapproved($id){
		$q = $this->db->get_where('pro_production', array('id' => $id,'status' => 'process'), 1);
        if ($q->num_rows() > 0) {
            return FALSE;
        }
        return TRUE;
	}
   	
    public function deleteproductions($id){
        if ($this->db->delete('pro_production_items', array('production_id' => $id))) {
            $this->db->delete('pro_production', array('id' => $id));            
            return true;
        }
        return FALSE;
    }
	public function getBatchwisestock($product_id,$variant_id,$category_id,$subcategory_id,$brand_id){
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
		if($variant_id !='0'){
            $this->db->where('variant_id',$variant_id);
        }
		$this->db->where('stock_in>0');
	//	$this->db->where('store_id',$this->store_id);
        $this->db->where_not_in('stock_status','closed');
        $this->db->group_by('id');
        $this->db->order_by('id', 'asc');
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return array();
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
	function unitToBaseUnit($qty,$operator,$operation_value) {
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
}