<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Wastage_model extends CI_Model{
    public function __construct(){
        parent::__construct();
    }
 function addWastage($data,$items){
            $this->db->insert('wastage', $data);
			$wastage_id = $this->db->insert_id();
	        $unique_id = $this->site->generateUniqueTableID($wastage_id);
			if ($wastage_id) {
				$this->site->updateUniqueTableId($wastage_id,$unique_id,'wastage');
			}
			foreach ($items as $item) {
				if($item['batches'] !=0){
				   $batches = $item['batches'];unset($item['batches']);
				   foreach ($batches as $k => $batch) {
					   $batch['wastage_id'] = $unique_id;
				   $this->db->insert('wastage_items', $batch);
						$item_d_insert_id = $this->db->insert_id();
						$id_unique_id = $this->site->generateUniqueTableID($item_d_insert_id);
						if ($item_d_insert_id) {
							$this->site->updateUniqueTableId($item_d_insert_id,$id_unique_id,'wastage_items');
								if($data['status']=="approved"){
								    $this->TransferStockOut($batch['wastage_unit_qty'],$batch['stock_id']);  
							}
						}
				   }
				}
				
			}
   return true;

 }
 function updateWastage($id,$data,$products){
	   if ($this->db->update('wastage', $data, array('id' => $id)) && $this->db->delete('wastage_items', array('wastage_id' => $id))) {
	 $wastage_id=$id;
	 foreach ($products as $item) {
				if($item['batches'] !=0){
				   $batches = $item['batches'];unset($item['batches']);
				   foreach ($batches as $k => $batch) {
					   $batch['wastage_id'] = $wastage_id;
				   $this->db->insert('wastage_items', $batch);
						$item_d_insert_id = $this->db->insert_id();
						$id_unique_id = $this->site->generateUniqueTableID($item_d_insert_id);
						if ($item_d_insert_id) {
							$this->site->updateUniqueTableId($item_d_insert_id,$id_unique_id,'wastage_items');
								if($data['status']=="approved"){
								    $this->TransferStockOut($batch['wastage_unit_qty'],$batch['stock_id']);  
							}
						}
				   }
				}
				
			}
	 
	   } 
	   
 }

 public function getProductOptions($product_id)
    {
        $q = $this->db->get_where('product_variants', array('product_id' => $product_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    } 
 public function getProductOptionByID($id)
    {
        $q = $this->db->get_where('product_variants', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	 public function loadbatches($productid,$variantid,$categoryid,$subCategoryid,$brandid){
		$type = array('standard','raw');
		$this->db->select('pro_stock_master.*,r.id');
		$this->db->from('recipe r');
		$this->db->join('tax_rates t','r.purchase_tax=t.id','left');
		$this->db->join('pro_stock_master','pro_stock_master.product_id=r.id AND pro_stock_master.store_id='.$this->store_id);
		$this->db->where('r.id',$productid);
		if(!empty($variantid)){
			$this->db->where('pro_stock_master.variant_id',$variantid);
		}
		if(!empty($categoryid)){
			$this->db->where('pro_stock_master.category_id',$categoryid);
		}
		if(!empty($subCategoryid)){
			$this->db->where('pro_stock_master.subcategory_id',$subCategoryid);
		}
		if(!empty($brandid)){
			$this->db->where('pro_stock_master.brand_id',$brandid);
		}
		$this->db->where('pro_stock_master.stock_in >',0);
		$this->db->where_in('r.type',$type);
        $q = $this->db->get();
		
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $k=> $row) {	
			if($row->batch==null){$row->batch="No Batch";}
		      $row->available_stock = $row->stock_in;			
              $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	function TransferStockOut($qty,$stockid){
		$store_id = $this->store_id;
		$id=$stockid;	
		$query = 'update '.$this->db->dbprefix('pro_stock_master').'
			set stock_in = stock_in - '.$qty.' 
			where unique_id="'.$id.'"';
	    $this->db->query($query);
		
		return $id;
    }
	function  getWastageId($id){
		$q=$this->db->get_where("wastage",array("id"=>$id));
	            if($q->num_rows()>0){
                         return $q->row();
				}
			return false;				
	}
	
	function getWastageItemById($wastageid){
		$this->db->select('wastage_items.*,b.name as brand_name,rc.name as category_name,rsc.name as subcategory_name,rv.name as variant');
		$this->db->join('recipe_categories as rc','rc.id=wastage_items.category_id','left');
		$this->db->join('recipe_categories rsc','rsc.id=wastage_items.subcategory_id','left');	
		$this->db->join('brands b','b.id=wastage_items.brand_id','left');
		$this->db->join('recipe_variants rv','rv.id=wastage_items.variant_id','left');
		$this->db->where("wastage_id",$wastageid);
		$q=$this->db->get('wastage_items');
		if($q->num_rows()>0){
			foreach($q->result() as $row){
				$data[]=$row;
				
			}
			return $data;
		}
		return false;
		
	}
		function getbatchStockData($wastage_id,$product_id){
		$this->db->select('wi.product_id as id,wi.wastage_qty,wi.tax_method,wi.tax,pro_stock_master.stock_in ,pro_stock_master.unique_id as stock_id,pro_stock_master.supplier_id,pro_stock_master.invoice_id,pro_stock_master.selling_price,pro_stock_master.batch,pro_stock_master.expiry,pro_stock_master.cost_price,pro_stock_master.landing_cost,DATE(wi.expiry) as expiry_date,wi.brand_id as brand_id,wi.variant_id as variant_id,wi.stock_id as unique_id');
		$this->db->from('wastage_items as wi');
		
		$this->db->join('warehouses_recipe wr', 'wr.recipe_id=wi.product_id and warehouse_id='.$this->store_id,'left');
		$this->db->join('pro_stock_master','pro_stock_master.unique_id=wi.stock_id AND pro_stock_master.store_id='.$this->store_id);
		$this->db->where('wi.wastage_id',$wastage_id);
		$this->db->where('wi.product_id',$product_id);
		$q = $this->db->get();
		
		if($q->num_rows()>0){
			  foreach (($q->result()) as $k=> $row) {	
			if($row->batch==null){$row->batch="No Batch";}
		      $row->available_stock = $row->stock_in;			
              $data[] = $row;
	    }
	    return $data;
	}
	return false;
     
    }
}
