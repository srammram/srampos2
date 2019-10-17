<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Production_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function addProduction($data = array(), $items = array())
    {
		$this->db->insert('pro_production', $data);
		$production_id = $this->db->insert_id();     
			if($production_id){    		
				foreach ($items  as $item) {
					$item['production_id'] = $production_id;
					$this->db->insert('pro_production_items', $item); //print_r($this->db->error());
					//echo $data['status'];
					//die;
					if($data['status']=='approved'){
						 $cate['category_id'] = $item['category_id'];
						 $cate['subcategory_id'] = $item['subcategory_id'];
						 $cate['brand_id'] = $item['brand_id'];
						 // $category_mappingID = $this->siteprocurment->getCategoryMappingID($item['product_id'],$item['category_id'],$item['subcategory_id'],$item['brand_id']);
						 
						$this->siteprocurment->production_salestock_out($item['product_id'],$item['quantity'],$item['variant_id']);
						// $this->siteprocurment->product_stockIn($item['product_id'],$item['quantity'],$cate);
						$this->updateStockMaster($item['product_id'],$item['quantity'],$cate); // $category_mappingID
					}				
				}
			} 
			// die;
		return true;
    }
    public function getProductOptions($product_id)
    {
        $q = $this->db->get_where('recipe_variants', array('recipe_id' => $product_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    public function getProductNames($term,$limit = 10)
    {
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
	public function getProductNamesNew($term,$limit = 10)
    {		
	/*$type = array('production','semi-finished', 'quick_service'); //,'standard'
	$this->db->select('r.*,t.rate as purchase_tax_rate,b.name as brand_name,rc.name as category_name,rsc.name as subcategory_name,cm.category_id,cm.subcategory_id,cm.brand_id,cm.purchase_cost,cm.selling_price as cost,IFNULL(rvv.price,0) as variant_price,IFNULL(rv.name,"") as variant_name,IFNULL(rv.id,0) as variant_id');
	$this->db->from('recipe r');
	$this->db->join('recipe_variants_values rvv', 'r.id=rvv.recipe_id', 'left');
    $this->db->join('recipe_variants rv', 'rv.id=rvv.attr_id', 'left');
	$this->db->join('category_mapping as cm','cm.product_id=r.id','left');
	$this->db->join('recipe_categories as rc','rc.id=cm.category_id','left');
	$this->db->join('recipe_categories rsc','rsc.id=cm.subcategory_id','left');	
	$this->db->join('brands b','b.id=cm.brand_id','left');
	$this->db->join('tax_rates t','r.purchase_tax=t.id','left');
    $this->db->where("(r.name LIKE '%" . $term . "%' OR r.code LIKE '%" . $term . "%' OR  concat(r.name, ' (', r.code, ')') LIKE '%" . $term . "%')");
    $this->db->where_in('r.type',$type);
	$this->db->limit($limit);		
	$q = $this->db->get();	*/		

/*SELECT rp.id,r.id,r.name,IFNULL(rvv.price, 0) as variant_price, IFNULL(rv.name, "") as variant_name, IFNULL(rv.id, 0) as variant_id
FROM `srampos_recipe` `r`
JOIN `srampos_recipe_products` `rp` ON `rp`.`recipe_id`=`r`.`id`
LEFT JOIN `srampos_recipe_variants_values` `rvv` ON `rp`.`recipe_id`=`rvv`.`recipe_id`
LEFT JOIN `srampos_recipe_variants` `rv` ON `rp`.`variant_id`=`rv`.`id`
LEFT JOIN `srampos_category_mapping` as `cm` ON `cm`.`product_id`=`r`.`id`
LEFT JOIN `srampos_recipe_categories` as `rc` ON `rc`.`id`=`cm`.`category_id`
LEFT JOIN `srampos_recipe_categories` `rsc` ON `rsc`.`id`=`cm`.`subcategory_id`
LEFT JOIN `srampos_brands` `b` ON `b`.`id`=`cm`.`brand_id`
LEFT JOIN `srampos_tax_rates` `t` ON `r`.`purchase_tax`=`t`.`id`*/

	$type = array('production','semi-finished', 'quick_service'); //,'standard'
	$this->db->select('r.*,t.rate as purchase_tax_rate,b.name as brand_name,rc.name as category_name,rsc.name as subcategory_name,cm.category_id,cm.subcategory_id,cm.brand_id,cm.purchase_cost,cm.selling_price as cost,IFNULL(rvv.price,0) as variant_price,IFNULL(rv.name,"") as variant_name,IFNULL(rv.id,0) as variant_id');
	$this->db->from('recipe r');
	$this->db->join('recipe_products rp', 'rp.recipe_id=r.id');
	$this->db->join('recipe_variants_values rvv', 'rp.recipe_id=rvv.recipe_id', 'left');
    $this->db->join('recipe_variants rv', 'rp.variant_id=rv.id', 'left');
	$this->db->join('category_mapping as cm','cm.product_id=r.id','left');
	$this->db->join('recipe_categories as rc','rc.id=cm.category_id','left');
	$this->db->join('recipe_categories rsc','rsc.id=cm.subcategory_id','left');	
	$this->db->join('brands b','b.id=cm.brand_id','left');
	$this->db->join('tax_rates t','r.purchase_tax=t.id','left');
    $this->db->where("(r.name LIKE '%" . $term . "%' OR r.code LIKE '%" . $term . "%' OR  concat(r.name, ' (', r.code, ')') LIKE '%" . $term . "%')");
    $this->db->where_in('r.type',$type);
    $this->db->group_by('r.id,rv.id');
	$this->db->limit($limit);		
	$q = $this->db->get();	
	// print_r($this->db->last_query());die;
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
	$this->db->select('p.*');
        $this->db->from('pro_production_items as p');
	$this->db->join('recipe as r', 'r.id=p.product_id', 'left');
        $this->db->order_by('id', 'asc');
        $this->db->where(array('production_id' => $p_id));
	//echo $this->db->get_compiled_select();
	$q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }
    public function updateProduction($id, $data, $items = array())
    {
	
	
        if ($this->db->update('pro_production', $data, array('id' => $id)) && $this->db->delete('pro_production_items', array('production_id' => $id))) {
            foreach ($items as $item) {
                $item['production_id'] = $id;
                $this->db->insert('pro_production_items', $item);
		if($data['status']=='approved'){
		    $cate['category_id'] = $item['category_id'];
		    $cate['subcategory_id'] = $item['subcategory_id'];
		    $cate['brand_id'] = $item['brand_id'];
		    $category_mappingID = $this->siteprocurment->getCategoryMappingID($item['product_id'],$item['category_id'],$item['subcategory_id'],$item['brand_id']);
		    
		    $this->site->production_salestock_out($item['product_id'],$item['quantity']);
		    $this->siteprocurment->product_stockIn($item['product_id'],$item['quantity'],$cate);
		    $this->updateStockMaster($item['product_id'],$item['quantity'],$cate); // ,$category_mappingID);
		}
            }
            return true;
        }
        return false;
    }
    function updateStockMaster($pro_id,$qty,$cate){	

		$store_id = $this->session->userdata('warehouse_id');
		
		$piece = $this->db->get_where('recipe', array('id' =>$pro_id))->row('piece');
		$stock_piece = $qty * $piece;
		// echo "string";die;
			$q = $this->db->get_where('srampos_pro_stock_master', array('product_id' => $pro_id));
			 if ($q->num_rows() > 0) {
				  	$query ='update srampos_pro_stock_master set stock_in = stock_in + '.$qty.', stock_in_piece = stock_in_piece + '.$stock_piece.' where store_id='.$store_id.' AND product_id='.$pro_id.''; //  AND batch=IS NULL
			   }else{ 
			   	// echo "string";die;
					$query ='insert into srampos_pro_stock_master(store_id, product_id, cm_id, category_id, subcategory_id, brand_id, stock_in, stock_out)values('.$store_id.','.$pro_id.', 0, '.$cate['category_id'].', '.$cate['subcategory_id'].', '.$cate['brand_id'].', '.$qty.', 0)';
			  } 	
		//echo $this->db->last_query();
		$this->db->query($query);
		// echo $this->db->last_query();die;
		
    }

    function production_salestock_out($product_id,$stock_out_qty){
		
		$item = $this->getrecipeByID($product_id);
	
		if($item->type=="production"){
		   $q = $this->get_recipe_products($product_id);		   
			if($q->num_rows()>0){
				foreach($q->result() as $k => $row){
					 $cate['category_id'] = $row->category_id;
				    $cate['subcategory_id'] = $row->subcategory_id;
				    $cate['brand_id'] = $row->brand_id;
				    $cate['cm_id'] = $row->cm_id;
						$mapped_item_qty = $stock_out_qty * $row->quantity;
						$updated_stock = $this->productionupdateStockMaster($row->product_id,$mapped_item_qty,$cm_id,$cate);
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
			}else{						
			$query ='insert into srampos_pro_stock_master(store_id, product_id, cm_id, category_id, subcategory_id, brand_id, stock_out)values('.$store_id.','.$product_id.', 0, '.$cate['category_id'].', '.$cate['subcategory_id'].', '.$cate['brand_id'].', stock_out + '.$stock_out.')';
			$this->db->query($query);
		}  
			return $order_item;
    }

   
}