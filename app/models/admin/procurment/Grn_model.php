<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Grn_model extends CI_Model{
    public function __construct(){
        parent::__construct();
    }
  public function getPurchase_invoicesByID($id){
      $this->db->select('pro_purchase_invoices.*,warehouses.name,warehouses.address,warehouses.id as deliver_tostore')
	  ->from('pro_purchase_invoices')
	  ->join('pro_purchase_invoice_items','pro_purchase_invoice_items.invoice_id=pro_purchase_invoices.id','left')
	  ->join('warehouses','warehouses.id=pro_purchase_invoice_items.store_id','left')
	 ->where('pro_purchase_invoice_items.store_id',$this->store_id)
	 ->where('pro_purchase_invoices.id',$id)
	 ->group_by('pro_purchase_invoices.id');
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	public function getAllPurchase_invoiceItems($id){
		$this->db->select("pro_purchase_invoice_items.*");
		$this->db->where("invoice_id",$id);
		$this->db->where("store_id",$this->store_id);
		$this->db->where("is_complete",0);
		$q=$this->db->get("pro_purchase_invoice_items");
		
		if($q->num_rows()>0){
			foreach($q->result() as $row){
				$data[]=$row;
			}
			return $data;
		}
		return false;
	}
	
	public function getPurchase_invoicelist(){
     $this->db->select('pro_purchase_invoices.id,reference_no')
	 ->from('pro_purchase_invoices')
	 ->join('pro_purchase_invoice_items','pro_purchase_invoice_items.invoice_id=pro_purchase_invoices.id','left')
	 ->where('pro_purchase_invoices.status', 'approved')
	 ->where('pro_purchase_invoice_items.store_id',$this->store_id)
	 ->group_by('pro_purchase_invoices.id');
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
           foreach($q->result() as $row){
			   $data[]=$row;
		   }
		   return $data;
        }
        return FALSE;
    }
	 public function getProductOptions($product_id, $warehouse_id){
        $this->db->select('product_variants.id as id, product_variants.name as name, product_variants.price as price, product_variants.quantity as total_quantity, warehouses_products_variants.quantity as quantity')
        ->join('warehouses_products_variants', 'warehouses_products_variants.option_id=product_variants.id', 'left')
        ->where('product_variants.product_id', $product_id)
            ->where('warehouses_products_variants.warehouse_id', $warehouse_id)
            ->where('warehouses_products_variants.quantity >', 0)
            ->group_by('product_variants.id');
        $q = $this->db->get('product_variants');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
   function getpi($id){
		$this->db->select("*");
		$this->db->where("id",$id);
		$q=$this->db->get("pro_purchase_invoices");
		if($q->num_rows()>0){
		   return $q->row();
		}
		return false;
	}
	
   public function addGrn($data = array(), $items = array()){
            if ($this->db->insert('pro_grn', $data)) {
            $grn_id = $this->db->insert_id();
            if ($grn_id) {            
                $unique_id = $this->site->generateUniqueTableID($grn_id);
            if ($grn_id) {
                $this->site->updateUniqueTableId($grn_id,$unique_id,'pro_grn');
            }
			$bal_count=0;
            foreach ($items as $item) {
                $item['grn_id'] = $unique_id;
				$product_base_cost=$item['product_base_cost'];
				$product_base_price=$item['product_base_price'];
				unset($item['product_base_cost'],$item['product_base_price']);
                $this->db->insert('pro_grn_items', $item);
				$i_grn = $this->db->insert_id();
                $i_unique_id = $this->site->generateUniqueTableID($i_grn);
                if ($i_grn) {
                    $this->site->updateUniqueTableId($i_grn,$i_unique_id,'pro_grn_items');
                }
			 	if($item['quantity']>=$item['pi_qty']){
					$this->db->where("pi_uniqueId",$item['pi_uniqueId']);
					$this->db->update("pro_purchase_invoice_items",array("is_complete"=>1));
				}  
				if($data['status']=="approved"){
				    $warehouse_id                  = $this->siteprocurment->default_warehouse_id();
					$stock_update['store_id']      = $item['store_id'];
                    $stock_update['product_id']    = $item['product_id'];
					$stock_update['variant_id']    = $item['variant_id'];
					$stock_update['category_id']   = $item['category_id'];
					$stock_update['subcategory_id'] = $item['subcategory_id'];
					$stock_update['brand_id']      = $item['brand_id'];
					$stock_update['stock_in']      = $item['unit_quantity'];
					$stock_update['stock_out']     = 0;
					$stock_update['cost_price']    = $product_base_cost;
					$stock_update['selling_price'] = $product_base_price;
					$stock_update['landing_cost']  = $item['landing_cost'];
					$stock_update['tax_rate']      = $item['tax_rate'];
					$stock_update['invoice_id']    = $data['invoice_id'];
					$stock_update['batch']         = $item['batch_no'];
					$stock_update['expiry']        = $item['expiry'];
					$stock_update['expiry_type']   = $item['expiry_type'];
					$stock_update['invoice_date']  = $data['invoice_date'];
					$stock_update['supplier_id']   = $data['supplier_id'];
					$stock_update['parent_unique_id']   = $item['parent_stock_unique_id'];
					if($item['expiry_type']=='days'){
					$stock_update['expiry_date'] = date('Y-m-d', strtotime("+".$data['expiry']." day"));
					}else if($item['expiry_type']=='months'){
					$stock_update['expiry_date'] = date('Y-m-d', strtotime("+".$data['expiry']." months"));
					}else if($item['expiry_type']=='year'){
					$stock_update['expiry_date'] = $data['expiry'];
					}
					$stock_update['unique_id'] = $item['pi_uniqueId'];
					$category_mappingID=$this->siteprocurment->item_cost_update_new($stock_update);
					$stock_update['cm_id']     = $category_mappingID ? $category_mappingID :0;
					if(!empty($stock_id) && !empty($stock_update['parent_unique_id']) &&$data['stock_type']==1)	{
							$stock_id=$this->stock_master_update($stock_update);
							$this->negative_stock_balancing($stock_update['parent_unique_id'],$stock_id);
					}elseif($data['stock_type']==1){
						$this->negative_stock_adjustment($stock_update);
					}else{
						$stock_id=$this->stock_master_update($stock_update);
					}
					
	            }
            }
		    $bal_count +=($item['quantity']>=$item['pi_qty'])?0:1;
		    if($bal_count<=0){
				$this->db->where("id",$data['invoice_id']);
				$this->db->update("pro_purchase_invoices",array("status"=>"completed"));
			}  
			if($this->isStore && $data['status']=="approved"){	
			    $this->sync_center->sync_grn($unique_id);
			}
            return true;
        }
        return false;
    }
	}
	function negative_stock_adjustment($stock_data){
		$batches=$this->getNegativeBatches($stock_data['product_id'],$stock_data['variant_id'],$stock_data['category_id'],$stock_data['subcategory_id'],$stock_data['brand_id']);
		$total_row=count($batches);
		$row=0;
		foreach($batches as $batch){
				if($total_row==$row){
					$a_stock=($batch->stock_in>=0)$batch->stock_in:abs($batch->stock_in);
					$balance_quantity=$stock_data['stock_in']-$a_stock;
					$status=($balance_quantity>0)?"available":"closed";
				    $query = 'update srampos_pro_stock_master set stock_in = '.$balance_quantity.',stock_status='.$status.' where store_id='.$this->store_id.' and id='.$batch->id;
                    $this->db->query($query); 
					break;
			}else{
				$a_stock=($batch->stock_in>=0)$batch->stock_in:abs($batch->stock_in);
				$balance_quantity=$stock_data['stock_in']-$a_stock;
				if($balance_quantity>0){
				$query = 'update srampos_pro_stock_master set stock_in =  '.$a_stock.' stock_status="closed" where store_id='.$this->store_id.' and id='.$batch->id;
                $this->db->query($query); 
				$base_quantity =$base_quantity-$a_stock;
				}else{
					$query = 'update srampos_pro_stock_master set stock_in = stock_in + '.$base_quantity.'  where store_id='.$this->store_id.' and id='.$batch->id;
                    $this->db->query($query); 
					break;
				}
			}
			$row++;
			
		}
		return  true;
	}
	public function getNegativeBatches($product_id,$variant_id,$category_id,$subcategory_id,$brand_id){
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
		$this->db->where('store_id',$this->store_id);
		$this->db->where('store_in<0');
		$this->db->limit(1);
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
	function negative_stock_balancing($old_stockId,$new_stockId){
		$oq=$this->db->get_where("pro_stock_master",array("unique_id"=>$old_stockId));
		$nq=$this->db->get_where("pro_stock_master",array("unique_id"=>$new_stockId));
		if($oq->num_row()>0){
			$old_stock=$oq->row();
			$new_stock=$nq->row();
			if($new_stock->stock_in>$old_stock->stock_in){
				$o_update="update pro_stock_master set stock_in=0 where unique_id".$old_stockId;
				$this->db->query($o_update);
				$n_update="update pro_stock_master set stock_in=stock_in".$old_stock->stock_in." where unique_id".$new_stockId;
				$this->db->query($n_update);
				return true;
			}else{
				$o_update="update pro_stock_master set stock_in=0 where unique_id".$new_stockId;
				$this->db->query($o_update);
				$n_update="update pro_stock_master set stock_in=stock_in".$new_stock->stock_in." where unique_id".$old_stockId;
				$this->db->query($n_update);
				return true;
			}
			return true;
		}
		return false;
		
	}
	
	function getGRNById($id){
		$this->db->select("pro_grn.*");
		$this->db->where("id",$id);
		$q=$this->db->get("pro_grn");
		if($q->num_rows()>0){
			return $q->row();
		}
		return false;
	}
	function getGRNIitemById($id){
		$this->db->select("pro_grn_items.*");
		$this->db->where("grn_id",$id);
		$q=$this->db->get("pro_grn_items");
		if($q->num_rows()>0){
			foreach($q->result() as $row){
				$data[]=$row;
			}
			return $data;
		}
		return false;
	}
	
	public function updateGrn($id, $data, $items = array()){  
     if ($this->db->update('pro_grn', $data, array('id' => $id)) && $this->db->delete('pro_grn_items', array('grn_id' => $id))) {
			$bal_count=0;
            foreach ($items as $item) {
                $item['grn_id'] = $id;
				$product_base_cost=$item['product_base_cost'];
				$product_base_price=$item['product_base_price'];
				unset($item['product_base_cost'],$item['product_base_price']);
                $this->db->insert('pro_grn_items', $item);
				$i_grn = $this->db->insert_id();
                $i_unique_id = $this->site->generateUniqueTableID($i_grn);
                if ($i_grn) {
                    $this->site->updateUniqueTableId($i_grn,$i_unique_id,'pro_grn_items');
                }
				if($item['quantity']>=$item['pi_qty']){
					$this->db->where("pi_uniqueId",$item['pi_uniqueId']);
					$this->db->update("pro_purchase_invoice_items",array("is_complete"=>1));
				}else{
					$this->db->where("pi_uniqueId",$item['pi_uniqueId']);
					$this->db->update("pro_purchase_invoice_items",array("is_complete"=>0));
				}
					if($data['status']=="approved"){
	   	            $warehouse_id = $this->siteprocurment->default_warehouse_id();
					$stock_update['store_id']    = $item['store_id'];
                    $stock_update['product_id']  = $item['product_id'];
					$stock_update['variant_id']  = $item['variant_id'];
					$stock_update['category_id'] = $item['category_id'];
					$stock_update['subcategory_id'] = $item['subcategory_id'];
					$stock_update['brand_id']    = $item['brand_id'];
					$stock_update['stock_in']    = $item['unit_quantity'];
					$stock_update['stock_out']   = 0;
					$stock_update['cost_price']    = $product_base_cost;
					$stock_update['selling_price'] = $product_base_price;
					$stock_update['landing_cost']  = $item['landing_cost'];
					$stock_update['tax_rate']      = $item['tax_rate'];
					$stock_update['invoice_id']    = $data['invoice_id'];
					$stock_update['batch']         = $item['batch_no'];
					$stock_update['expiry']        = $item['expiry'];
					$stock_update['expiry_type']   = $item['expiry_type'];
					$stock_update['invoice_date']  = $data['invoice_date'];
					if($item['expiry_type']=='days'){
					$stock_update['expiry_date'] = date('Y-m-d', strtotime("+".$data['expiry']." day"));
					}else if($item['expiry_type']=='months'){
					$stock_update['expiry_date'] = date('Y-m-d', strtotime("+".$data['expiry']." months"));
					}else if($item['expiry_type']=='year'){
					$stock_update['expiry_date'] = $data['expiry'];
					}
					$stock_update['unique_id'] = $item['pi_uniqueId'];
					$category_mappingID=$this->siteprocurment->item_cost_update_new($stock_update);
					$stock_update['cm_id']     = $category_mappingID ? $category_mappingID :0;
					/* $this->stock_master_update($stock_update);
					if(!empty($stock_id) && !empty($stock_update['parent_unique_id']))	{
							$this->negative_stock_balancing($stock_update['parent_unique_id'],$stock_id);
					} */
					if(!empty($stock_id) && !empty($stock_update['parent_unique_id']) && $data['stock_type']==1)	{
							$stock_id=$this->stock_master_update($stock_update);
							$this->negative_stock_balancing($stock_update['parent_unique_id'],$stock_id);
					}elseif($data['stock_type']==1){
						$this->negative_stock_adjustment($stock_update);
					}else{
						$stock_id=$this->stock_master_update($stock_update);
					}
			}	
            }   
			$bal_count +=($item['quantity']>=$item['pi_qty'])?0:1;
			if($bal_count<=0){
				$this->db->where("id",$data['invoice_id']);
				$this->db->update("pro_purchase_invoices",array("status"=>"completed"));
			}else{
				$this->db->where("id",$data['invoice_id']);
				$this->db->update("pro_purchase_invoices",array("status"=>"approved"));
			}				
				if($this->isStore && $data['status']=="approved"){	
			    $this->sync_center->sync_purchase_invoice($id);
			}
            return true;
        }        
        return false;
    }
	
	
    public function getProductNames($term, $warehouse_id, $limit = 10){
			$this->db->select('recipe.*, warehouses_recipe.quantity')
            ->join('warehouses_recipe', 'warehouses_recipe.recipe_id=recipe.id', 'left')
            ->group_by('recipe.id');
            $this->db->where("(name LIKE '%" . $term . "%' OR code LIKE '%" . $term . "%' OR  concat(name, ' (', code, ')') LIKE '%" . $term . "%')");
			$this->db->limit($limit);
			$q = $this->db->get('recipe');        
			if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
			}
    }

    public function getProductByCode($code){
        $q = $this->db->get_where('recipe', array('code' => $code), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getWHProduct($id){
        $this->db->select('recipe.id, code, name, warehouses_recipe.quantity, cost, tax_rate')
            ->join('warehouses_recipe', 'warehouses_recipe.product_id=recipe.id', 'left')
            ->group_by('recipe.id');
        $q = $this->db->get_where('recipe', array('warehouses_recipe.product_id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }


   public function deleteGrn($id){
        if ($this->db->delete('pro_grn_items', array('grn_id' => $id)) && $this->db->delete('pro_grn', array('id' => $id))) {
            return true;
        }
        return FALSE;
    }

    public function getItemByID($id){
        $q = $this->db->get_where('pro_store_request_items', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getProductByName($name){
        $q = $this->db->get_where('products', array('name' => $name), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getWarehouseProductQuantity($warehouse_id, $product_id){
        $q = $this->db->get_where('warehouses_products', array('warehouse_id' => $warehouse_id, 'product_id' => $product_id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getProductComboItems($pid, $warehouse_id){
        $this->db->select('products.id as id, combo_items.item_code as code, combo_items.quantity as qty, products.name as name, products.type as type, warehouses_products.quantity as quantity')
            ->join('products', 'products.code=combo_items.item_code', 'left')
            ->join('warehouses_products', 'warehouses_products.product_id=products.id', 'left')
            ->where('warehouses_products.warehouse_id', $warehouse_id)
            ->group_by('combo_items.id');
        $q = $this->db->get_where('combo_items', array('combo_items.product_id' => $pid));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
     

    public function getProductOptionByID($id){
        $q = $this->db->get_where('product_variants', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	
	
	 public function getrecipeOptionByID($id){
        $q = $this->db->get_where('recipe_variants', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	 function stock_master_update($stock_update){
        $date         =date('Y-m-d h:m:s');
		$store_id 	  = $stock_update['store_id'];
        $product_id   = $stock_update['product_id'];
		$variant_id   = $stock_update['variant_id'];
		$category_id  = $stock_update['category_id'];
		$subcategory_id = $stock_update['subcategory_id'];
		$brand_id     = $stock_update['brand_id'];
		$cm_id        = $stock_update['cm_id']; 
		$invoice_id   = $stock_update['invoice_id'];
		$batch        = $stock_update['batch'];
		$expiry       = $stock_update['expiry'];
        $inv_date     = $stock_update['invoice_date'];
		$qty          = $stock_update['stock_in'];
		$this->db->select();
		$this->db->from('pro_stock_master');
		$this->db->where("unique_id",$stock_update['unique_id']);
		$q = $this->db->get();
		if($q->num_rows()>0){
			$id = $q->row('id');
			$this->db->where('id',$id);
			$this->db->update('pro_stock_master',$stock_update);
		}else{
			$this->db->insert('pro_stock_master',$stock_update);
			$insertID                  = $this->db->insert_id();
			$UniqueID                  = $this->site->generateUniqueTableID($insertID);
			$this->site->updateUniqueTableId($insertID,$UniqueID,'pro_stock_master');
			$stock_id = $this->db->insert_id();
		}
		if($this->isStore){
			$this->sync_center->sync_stock_auto($stock_update['unique_id']);
		}
		return $stock_id;
    }
    

}
