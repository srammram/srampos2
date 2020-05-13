<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Store_receivers_model extends CI_Model{
    public function __construct()
    {
        parent::__construct();
    }
    public function getProductNames($term, $limit = 10)
    {
        $this->db->where("(name LIKE '%" . $term . "%' OR code LIKE '%" . $term . "%' OR  concat(name, ' (', code, ')') LIKE '%" . $term . "%')");
        $this->db->limit($limit);
        $q = $this->db->get('products');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
	public function getReqBYID($id)
    {
        $q = $this->db->get_where('pro_store_transfers', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	
	public function getStore_receiverByID($id)
    {
        $q = $this->db->get_where('pro_store_receivers', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
		public function getAllStore_receiverItemsbyid($store_receiver_id){
        $this->db->select('pro_store_receiver_items.*')
            ->group_by('pro_store_receiver_items.id')
            ->order_by('id', 'asc');
        $q = $this->db->get_where('pro_store_receiver_items', array('store_receiver_id' => $store_receiver_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	public function checkPendingQTY($product_id, $quantity, $requestnumber){
		
		$q = $this->db->select('id')->where('requestnumber', $requestnumber)->order_by('id', 'DESC')->limit(1)->get('pro_store_receivers');
		if ($q->num_rows() > 0) {
			$s = $this->db->select('pending_quantity')->where('product_id', $product_id)->where('store_receiver_id', $q->row('id'))->get('pro_store_receiver_items');
            if($s->num_rows() > 0) {
				return $s->row('pending_quantity');	
			}else{
				return $quantity;	
			}
        }else{
			return $quantity;	
		}
		return 0;	
	}
	
	public function checkPendingQTYEdit($product_id, $quantity, $id){
		
		$q = $this->db->select('id')->where('id', $id)->order_by('id', 'DESC')->limit(1)->get('pro_store_receivers');
		if ($q->num_rows() > 0) {
			$s = $this->db->select('pending_quantity')->where('product_id', $product_id)->where('store_receiver_id', $q->row('id'))->get('pro_store_receiver_items');
            if($s->num_rows() > 0) {
				return $s->row('pending_quantity');	
			}else{
				return $quantity;	
			}
        }else{
			return $quantity;	
		}
		return 0;	
	}
	
	public function getAllStore_receiverItems($store_receiver_id)
    {
		
        $this->db->select('pro_store_receiver_items.*, tax_rates.code as tax_code, tax_rates.name as tax_name, tax_rates.rate as tax_rate, products.unit, products.details as details, product_variants.name as variant, products.hsn_code as hsn_code')
            ->join('products', 'products.id=pro_store_receiver_items.product_id', 'left')
            ->join('product_variants', 'product_variants.id=pro_store_receiver_items.option_id', 'left')
            ->join('tax_rates', 'tax_rates.id=pro_store_receiver_items.tax_rate_id', 'left')
            ->group_by('pro_store_receiver_items.id')
            ->order_by('id', 'asc');
        $q = $this->db->get_where('pro_store_receiver_items', array('store_receiver_id' => $store_receiver_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
    public function getAllProducts()
    {
        $q = $this->db->get('products');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getProductByID($id)
    {
        $q = $this->db->get_where('products', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	
	public function getSupplierdetails($supplier_id){
		$q = $this->db->get_where('companies', array('id' => $supplier_id, 'group_id' => 4), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
	}
    public function getProductsByCode($code)
    {
        $this->db->select('*')->from('products')->like('code', $code, 'both');
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    public function getProductByCode($code)
    {
        $q = $this->db->get_where('products', array('code' => $code), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getProductByName($name)
    {
        $q = $this->db->get_where('products', array('name' => $name), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getAllStore_receivers()
    {
        $q = $this->db->get('pro_store_receivers');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }

	public function getAvailableQTY($product_id, $store_id){
		
		$q = $this->db->select('current_quantity')->where('product_id', $product_id)->where('store_id', $store_id)->order_by('id', 'DESC')->limit(1)->get('pro_stock_master');
		if ($q->num_rows() > 0) {
			return $q->row('current_quantity');	
		}
		return 0;
	}
	
	public function getAllRequestItems($store_receivers_id)
    {
        $this->db->select('pro_store_transfer_items.*, tax_rates.code as tax_code, tax_rates.name as tax_name, tax_rates.rate as tax_rate, products.unit, products.details as details, product_variants.name as variant, products.hsn_code as hsn_code')
            ->join('products', 'products.id=pro_store_transfer_items.product_id', 'left')
            ->join('product_variants', 'product_variants.id=pro_store_transfer_items.option_id', 'left')
            ->join('tax_rates', 'tax_rates.id=pro_store_transfer_items.tax_rate_id', 'left')
            ->group_by('pro_store_transfer_items.id')
            ->order_by('id', 'asc');
        $q = $this->db->get_where('pro_store_transfer_items', array('store_transfer_id' => $store_receivers_id));
		
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
    public function getAllStore_receiversItems($store_receivers_id)
    {
        $this->db->select('pro_store_receiver_items.*')
            ->join('recipe', 'recipe.id=pro_store_receiver_items.product_id', 'left')
            ->group_by('pro_store_receiver_items.id')
            ->order_by('id', 'asc');
        $q = $this->db->get_where('pro_store_receiver_items', array('store_receiver_id' => $store_receivers_id));
		
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
 public function getTransferredStockData($item_receiver_id)
    {
        $this->db->select('i.id as itemid,i.store_receiver_id as stri ,i.store_receiver_item_id as strii,pi.product_id as id,i.selling_price as price,i.batch as batch_no,i.expiry,i.cost_price,i.transfer_qty,i.tax,i.tax_method,i.received_qty,i.vendor_id,i.landing_cost,i.invoice_id');
        $this->db->from('pro_store_receiver_item_details as i');
        $this->db->join('pro_store_receiver_items as pi', 'pi.id=i.store_receiver_item_id', 'left');
        $this->db->where('i.store_receiver_item_id', $item_receiver_id);
        //$this->db->group_by('pi.id');
        //echo $this->db->get_compiled_select();
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            return $q->result();
        }
        return false;

    }
    public function getItemByID($id)
    {
        $q = $this->db->get_where('pro_store_receiver_items', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getTaxRateByName($name)
    {
        $q = $this->db->get_where('tax_rates', array('name' => $name), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getStore_receiversByID($id)
    {
        $q = $this->db->get_where('pro_store_receivers', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	
	 public function getRequestByID($id)
    {
        $q = $this->db->get_where('pro_store_transfers', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
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

    public function getProductWarehouseOptionQty($option_id, $warehouse_id)
    {
        $q = $this->db->get_where('warehouses_products_variants', array('option_id' => $option_id, 'warehouse_id' => $warehouse_id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function addProductOptionQuantity($option_id, $warehouse_id, $quantity, $product_id)
    {
        if ($option = $this->getProductWarehouseOptionQty($option_id, $warehouse_id)) {
            $nq = $option->quantity + $quantity;
            if ($this->db->update('warehouses_products_variants', array('quantity' => $nq), array('option_id' => $option_id, 'warehouse_id' => $warehouse_id))) {
                return TRUE;
            }
        } else {
            if ($this->db->insert('warehouses_products_variants', array('option_id' => $option_id, 'product_id' => $product_id, 'warehouse_id' => $warehouse_id, 'quantity' => $quantity))) {
                return TRUE;
            }
        }
        return FALSE;
    }

    public function resetProductOptionQuantity($option_id, $warehouse_id, $quantity, $product_id)
    {
        if ($option = $this->getProductWarehouseOptionQty($option_id, $warehouse_id)) {
            $nq = $option->quantity - $quantity;
            if ($this->db->update('warehouses_products_variants', array('quantity' => $nq), array('option_id' => $option_id, 'warehouse_id' => $warehouse_id))) {
                return TRUE;
            }
        } else {
            $nq = 0 - $quantity;
            if ($this->db->insert('warehouses_products_variants', array('option_id' => $option_id, 'product_id' => $product_id, 'warehouse_id' => $warehouse_id, 'quantity' => $nq))) {
                return TRUE;
            }
        }
        return FALSE;
    }

    public function getOverSoldCosting($product_id)
    {
        $q = $this->db->get_where('costing', array('overselling' => 1));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
	public function getStoreMasterProductID($product_id, $store_id){
		$q = $this->db->get_where('pro_stock_master', array('product_id' => $product_id, 'store_id' => $store_id, 'status' => 0));
		
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
	}
	
	public function getCurrentQuantityID($product_id, $store_id){
		$q = $this->db->select('current_quantity')->where('product_id', $product_id)->where('store_id', $store_id)->order_by('id', 'DESC')->limit(1)->get('pro_stock_master');
		if ($q->num_rows() > 0) {
			return $q->row('current_quantity');	
		}
		return 0;
	}
	
   

    public function updateStore_receivers($id, $data, $items = array())
    {
        if ($this->db->update('pro_store_receivers', $data, array('id' => $id))) {
            
            foreach ($items as $item) {
                $batches = $item['batches'];unset($item['batches']);
                $store_receive_itemid = $item['store_receive_itemid'];
                unset($item['store_receive_itemid']);
                $this->db->where(array("id" => $store_receive_itemid, "store_receiver_id" => $id));
                $this->db->update('pro_store_receiver_items', $item);
                if ($batches) {
                    foreach ($batches as $k => $batch) {
                        
                        $store_receiver_item_details_id = $batch['id'];
                        unset($batch['id']);
                        $batch['store_receiver_item_id'] = $batch['store_receiver_item_id'];
                        $batch['store_receiver_id'] = $id;
                        $this->db->where(array("id" => $store_receiver_item_details_id, "store_receiver_item_id" => $batch['store_receiver_item_id'], "store_receiver_id" => $batch['store_receiver_id']));
                        $this->db->update('pro_store_receiver_item_details', $batch);
                        if ($data['status'] == "approved") {    
						
                            $stock_update['store_id']       = $batch['store_id'];
							$stock_update['product_id']     = $item['product_id'];
							$stock_update['variant_id']     = $batch['variant_id'];
							$stock_update['category_id']    = $batch['category_id'];
							$stock_update['subcategory_id'] = $batch['subcategory_id'];
							$stock_update['brand_id']       = $batch['brand_id'];
							$stock_update['stock_in']       = $batch['received_qty'];
							$stock_update['stock_in_piece'] = 0;
							$stock_update['stock_out']      = 0;
							$stock_update['stock_out_piece']= 0;
							$stock_update['cost_price']     = $batch['cost_price'];
							$stock_update['selling_price']  = $batch['selling_price'];
							$stock_update['landing_cost']   = $batch['landing_cost'];
							$stock_update['tax_rate']       = $batch['tax_amount'];
							$stock_update['invoice_id']     = $batch['invoice_id'];
							$stock_update['batch']          = $batch['batch'];
							$stock_update['expiry']         = $batch['expiry'];
							$stock_update['supplier_id']    = $batch['vendor_id'];
							$stock_update['invoice_date'] ="";
							$cp = str_replace('.','_',$batch['cost_price']);
							if($item['expiry_type']=='days'){
							  $stock_update['expiry_date'] = date('Y-m-d', strtotime("+".$data['expiry']." day"));
							}else if($item['expiry_type']=='months'){
									$stock_update['expiry_date'] = date('Y-m-d', strtotime("+".$data['expiry']." months"));
							}else if($item['expiry_type']=='year'){
								$stock_update['expiry_date'] = $data['expiry'];
							}
							$stock_update['unique_id']      =$item['store_id'].$item['product_id'].$batch['variant_id'].$batch['batch'].$batch['category_id'].$batch['subcategory_id'].$batch['brand_id'].$cp.$batch['vendor_id'].$batch['invoice_id'];

			                $category_mappingID=$this->siteprocurment->item_cost_update_new($stock_update);
					        $stock_update['cm_id']     = $category_mappingID ? $category_mappingID :0;
					        $this->stock_master_update($stock_update);
							
                        }
                    }
                }
            }
			
			
            return true;
        }
        return false;
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
			$return_id = $this->db->insert_id();
		}
		if($this->isStore){
			$this->sync_center->sync_stock_auto($stock_update['unique_id']);
		}
    }

    public function deleteStore_receivers($id)
    {
        $purchase = $this->getStore_receiversByID($id);
        $purchase_items = $this->siteprocurment->getAllStore_receiversItems($id);
        if ($this->db->delete('pro_store_receiver_items', array('store_receivers_id' => $id)) && $this->db->delete('pro_store_receivers', array('id' => $id))) {
            // $this->db->delete('payments', array('store_receiver_id' => $id));
            // if ($purchase->status == 'received' || $purchase->status == 'partial') {
            //     foreach ($purchase_items as $oitem) {
            //         $this->updateAVCO(array('product_id' => $oitem->product_id, 'warehouse_id' => $oitem->warehouse_id, 'quantity' => (0-$oitem->quantity), 'cost' => $oitem->real_unit_cost));
            //         $received = $oitem->quantity_received ? $oitem->quantity_received : $oitem->quantity;
            //         if ($oitem->quantity_balance < $received) {
            //             $clause = array('store_receiver_id' => NULL, 'transfer_id' => NULL, 'product_id' => $oitem->product_id, 'warehouse_id' => $oitem->warehouse_id, 'option_id' => $oitem->option_id);
            //             $this->siteprocurment->setPurchaseItem($clause, ($oitem->quantity_balance - $received));
            //         }
            //     }
            // }
            $this->siteprocurment->syncQuantity(NULL, NULL, $purchase_items);
            return true;
        }
        return FALSE;
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

    public function getProductVariantByName($name, $product_id)
    {
        $q = $this->db->get_where('product_variants', array('name' => $name, 'product_id' => $product_id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

  

}
