<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Store_return_receivers_model extends CI_Model{
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
        $q = $this->db->get_where('pro_store_returns', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	
	public function getStore_return_receiverByID($id)
    {
        $q = $this->db->get_where('pro_store_return_receivers', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	
	public function checkPendingQTY($product_id, $quantity, $requestnumber){
		
		$q = $this->db->select('id')->where('requestnumber', $requestnumber)->order_by('id', 'DESC')->limit(1)->get('pro_store_return_receivers');
		if ($q->num_rows() > 0) {
			$s = $this->db->select('pending_quantity')->where('product_id', $product_id)->where('store_return_receiver_id', $q->row('id'))->get('pro_store_return_receiver_items');
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
		
		$q = $this->db->select('id')->where('id', $id)->order_by('id', 'DESC')->limit(1)->get('pro_store_return_receivers');
		if ($q->num_rows() > 0) {
			$s = $this->db->select('pending_quantity')->where('product_id', $product_id)->where('store_return_receiver_id', $q->row('id'))->get('pro_store_return_receiver_items');
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
	
	public function getAllStore_return_receiverItems($store_return_receiver_id){
        $this->db->select('pro_store_return_receiver_items.*, tax_rates.code as tax_code, tax_rates.name as tax_name, tax_rates.rate as tax_rate, products.unit, products.details as details, product_variants.name as variant, products.hsn_code as hsn_code')
            ->join('products', 'products.id=pro_store_return_receiver_items.product_id', 'left')
            ->join('product_variants', 'product_variants.id=pro_store_return_receiver_items.option_id', 'left')
            ->join('tax_rates', 'tax_rates.id=pro_store_return_receiver_items.tax_rate_id', 'left')
            ->group_by('pro_store_return_receiver_items.id')
            ->order_by('id', 'asc');
        $q = $this->db->get_where('pro_store_return_receiver_items', array('store_return_receiver_id' => $store_return_receiver_id));
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

    public function getAllStore_return_receivers()
    {
        $q = $this->db->get('pro_store_return_receivers');
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
	
	public function getAllRequestItems($store_return_receivers_id)
    {
        $this->db->select('pro_store_return_items.*, tax_rates.code as tax_code, tax_rates.name as tax_name, tax_rates.rate as tax_rate, products.unit, products.details as details, product_variants.name as variant, products.hsn_code as hsn_code')
            ->join('products', 'products.id=pro_store_return_items.product_id', 'left')
            ->join('product_variants', 'product_variants.id=pro_store_return_items.option_id', 'left')
            ->join('tax_rates', 'tax_rates.id=pro_store_return_items.tax_rate_id', 'left')
            ->group_by('pro_store_return_items.id')
            ->order_by('id', 'asc');
        $q = $this->db->get_where('pro_store_return_items', array('store_return_id' => $store_return_receivers_id));
		
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
    public function getAllStore_return_receiversItems($store_return_receivers_id)
    {
        $this->db->select('pro_store_return_receiver_items.*, tax_rates.code as tax_code, tax_rates.name as tax_name, tax_rates.rate as tax_rate, products.unit, products.details as details, product_variants.name as variant, products.hsn_code as hsn_code')
            ->join('products', 'products.id=pro_store_return_receiver_items.product_id', 'left')
            ->join('product_variants', 'product_variants.id=pro_store_return_receiver_items.option_id', 'left')
            ->join('tax_rates', 'tax_rates.id=pro_store_return_receiver_items.tax_rate_id', 'left')
            ->group_by('pro_store_return_receiver_items.id')
            ->order_by('id', 'asc');
        $q = $this->db->get_where('pro_store_return_receiver_items', array('store_return_receiver_id' => $store_return_receivers_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	public function getStore_return_receiversItems($store_receiver_id){
        $this->db->select('pro_store_return_receiver_items.*')
            ->group_by('pro_store_return_receiver_items.id')
            ->order_by('id', 'asc');
        $q = $this->db->get_where('pro_store_return_receiver_items', array('store_return_receiver_id' => $store_receiver_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
public function getstore_return_receiver_Items($store_return_receiver_id){
        $this->db->select('pro_store_return_receiver_items.*,pid.batch,pid.cost_price,pid.landing_cost,pid.selling_price,pid.tax,pid.tax_method,pid.expiry,pid.stock_id ,pid.category_id,pid.subcategory_id,pid.brand_id,pid.variant_id')
	    ->from('pro_store_return_receiver_items')      
		->join('pro_store_return_receiver_item_details pid','pid.store_return_receiver_item_id=pro_store_return_receiver_items.id','left')
        ->where('pro_store_return_receiver_items.store_return_receiver_id' ,$store_return_receiver_id)
	    ->group_by('pro_store_return_receiver_items.id')
        ->order_by('id', 'asc');
        $q = $this->db->get();
		
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	public function getstore_return_receiver_StockData($item_return_receiver_id){
        $this->db->select('i.id as itemid,i.store_return_receiver_id as stri ,i.store_return_receiver_item_id as strii,pi.product_id as id,i.selling_price as price,i.batch as batch_no,i.expiry,i.cost_price,i.return_qty,i.tax,i.tax_method,i.received_qty,i.vendor_id,i.landing_cost,i.invoice_id,i.stock_id');
        $this->db->from('pro_store_return_receiver_item_details as i');
        $this->db->join('pro_store_return_receiver_items as pi', 'pi.id=i.store_return_receiver_item_id', 'left');
        $this->db->where('i.store_return_receiver_item_id', $item_return_receiver_id);
        //$this->db->group_by('pi.id');
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            return $q->result();
        }
        return false;

    }
    public function getItemByID($id)
    {
        $q = $this->db->get_where('pro_store_return_receiver_items', array('id' => $id), 1);
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

    public function getStore_return_receiversByID($id)
    {
        $q = $this->db->get_where('pro_store_return_receivers', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	
	 public function getRequestByID($id)
    {
        $q = $this->db->get_where('pro_store_returns', array('id' => $id), 1);
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
	
	public function getBatchProductID($product_id, $batch_no, $store_id){
		$this->db->where('product_id', $product_id);
		$this->db->where('purchase_batch_no', $batch_no);
		$this->db->where('store_id', $store_id);
		$q = $this->db->get('pro_stock_master');
		if ($q->num_rows() > 0) {
			return $q->row();	
		}
		return false;	
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
	
    public function addStore_return_receivers($data, $items){
			$this->db->insert('pro_store_return_receivers', $data);
			$store_transfers_id = $this->db->insert_id();
	        $unique_id = $this->site->generateUniqueTableID($store_transfers_id);
			if ($store_transfers_id) {
				$this->site->updateUniqueTableId($store_transfers_id,$unique_id,'pro_store_return_receivers');
			}
			foreach ($items as $item) {
				if($item['batches'] !=0){
				   $batches = $item['batches'];unset($item['batches']);
				   $item['store_return_receiver_id'] = $unique_id;
				   $this->db->insert('pro_store_return_receiver_items', $item);  
				   $item_insert_id = $this->db->insert_id();
				   $i_unique_id = $this->site->generateUniqueTableID($item_insert_id);
				   if ($item_insert_id) {
				     $this->site->updateUniqueTableId($item_insert_id,$i_unique_id,'pro_store_return_receiver_items');
				   }
					foreach ($batches as $k => $batch) {
						$stock_id = $batch['stock_id'];
						$batch['store_return_receiver_item_id'] = $i_unique_id;
						$batch['store_return_receiver_id'] = $unique_id;
						$invoice_id = $batch['invoice_id'];
						$this->db->insert('pro_store_return_receiver_item_details', $batch);
						$item_d_insert_id = $this->db->insert_id();
						$id_unique_id = $this->site->generateUniqueTableID($item_d_insert_id);
						if ($item_d_insert_id) {
							$this->site->updateUniqueTableId($item_d_insert_id,$id_unique_id,'pro_store_return_receiver_item_details');
							if($data['status']=="approved"){
								$cp = str_replace('.','_',$batch['cost_price']);
								$cunique_id=$this->store_id.$item['product_id'].$batch['variant_id'].$batch['batch'].$batch['category_id'].$batch['subcategory_id'].$batch['brand_id'].$cp.$batch['vendor_id'].$batch['invoice_id'];	
								$this->TransferStockOut($batch['return_unit_qty'],$cunique_id);  
			        }
		          }
			   }
			}
	    }
	         if($data['status']=="approved"){
		        $this->sync_store_return($unique_id);
	         }
            return true;
        }
	
     

    public function updateStore_return_receivers($id, $data, $items = array()){
		
        if ($this->db->update('pro_store_return_receivers', $data, array('id' => $id)) && $this->db->delete('pro_store_return_receiver_items', array('store_return_receiver_id' => $id))) {
			 $this->db->delete('pro_store_return_receiver_item_details', array('store_return_receiver_id' => $id));
            $store_return_receivers_id = $id;
			
			foreach ($items as $item) {
				if($item['batches'] !=0){
				   $batches = $item['batches'];unset($item['batches']);
				   $item['store_return_receiver_id'] = $store_return_receivers_id;
				   $this->db->insert('pro_store_return_receiver_items', $item);  
				   $item_insert_id = $this->db->insert_id();
				   $i_unique_id = $this->site->generateUniqueTableID($item_insert_id);
				   if ($item_insert_id) {
				     $this->site->updateUniqueTableId($item_insert_id,$i_unique_id,'pro_store_return_receiver_items');
				   }
					foreach ($batches as $k => $batch) {
						$stock_id = $batch['stock_id'];
						$batch['store_return_receiver_item_id'] = $i_unique_id;
						$batch['store_return_receiver_id'] = $store_return_receivers_id;
						$invoice_id = $batch['invoice_id'];
						$this->db->insert('pro_store_return_receiver_item_details', $batch);
						$item_d_insert_id = $this->db->insert_id();
						$id_unique_id = $this->site->generateUniqueTableID($item_d_insert_id);
						if ($item_d_insert_id) {
							$this->site->updateUniqueTableId($item_d_insert_id,$id_unique_id,'pro_store_return_receiver_item_details');
							if($data['status']=="approved"){
								$cp = str_replace('.','_',$batch['cost_price']);
								$cunique_id=$this->store_id.$item['product_id'].$batch['variant_id'].$batch['batch'].$batch['category_id'].$batch['subcategory_id'].$batch['brand_id'].$cp.$batch['vendor_id'].$batch['invoice_id'];	
								$this->TransferStockOut($batch['return_unit_qty'],$cunique_id);  
			        }
		          }
			   }
			}
	    }
			
			 if($data['status']=="approved"){
		        $this->sync_store_return($store_return_receivers_id);
	         }
			 
		
            return true;
        }
        return false;
    }

   
    public function deleteStore_return_receivers($id){
        if ($this->db->delete('pro_store_return_receiver_items', array('store_return_receivers_id' => $id)) && $this->db->delete('pro_store_return_receivers', array('id' => $id))) {
         $this->db->delete('pro_store_return_receiver_item_details', array('store_return_receivers_id' => $id));
        //    $this->siteprocurment->syncQuantity(NULL, NULL, $purchase_items);
            return true;
        }
        return FALSE;
    }

    public function getWarehouseProductQuantity($warehouse_id, $product_id)
    {
        $q = $this->db->get_where('warehouses_products', array('warehouse_id' => $warehouse_id, 'product_id' => $product_id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
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

  
   

    public function getReturnByID($id)
    {
        $q = $this->db->get_where('return_store_return_receivers', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getAllReturnItems($return_id)
    {
        $this->db->select('return_purchase_items.*, products.details as details, product_variants.name as variant, products.hsn_code as hsn_code')
            ->join('products', 'products.id=return_purchase_items.product_id', 'left')
            ->join('product_variants', 'product_variants.id=return_purchase_items.option_id', 'left')
            ->group_by('return_purchase_items.id')
            ->order_by('id', 'asc');
        $q = $this->db->get_where('return_purchase_items', array('return_id' => $return_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    
 
   


  function get_receiver_list($store_id){
        $this->db->select('id,reference_no');
        $this->db->from('pro_store_receivers');
        $this->db->where('status','approved');
        $this->db->where('from_store',$store_id);
        $q = $this->db->get();
        if($q->num_rows()>0){
            return $q->result();
        }
        return false;
    }
	function get_store_receivers_by_id($store_receiver_id){
        $this->db->select('*');
        $this->db->from('pro_store_receivers');
        $this->db->where('status','approved');
        $this->db->where('id',$store_receiver_id);
        $q = $this->db->get();
        if($q->num_rows()>0){
            return $q->row();
        }
        return false;
    }
	 public function getAllStore_receiversItems($store_receivers_id){
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
	 public function getReceiversStockData($item_receiver_id){
        $this->db->select('i.id as itemid,i.store_receiver_id as stri ,i.store_receiver_item_id as strii,pi.product_id as id,i.selling_price as price,i.batch as batch_no,i.expiry,i.cost_price,i.transfer_qty,i.tax,i.tax_method,i.received_qty,i.vendor_id,i.landing_cost,i.invoice_id,i.stock_id');
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
	 function sync_store_return($return_id){
		$q = $this->db->get_where('srampos_pro_store_return_receivers',array('id'=>$return_id));
		$insertData = $q->row_array();
		$q = $this->db->get_where('srampos_pro_store_return_receiver_items',array('store_return_receiver_id'=>$return_id));
		$items_data = $q->result_array();
		unset($insertData['attachment']);
		$table_name = 'srampos_pro_store_returns';
		$table_items = 'srampos_pro_store_return_items';
		$table_item_details = 'srampos_pro_store_return_item_details';
		$insert_data = $insertData;
		$insert_data['store_id'] = $insertData['to_store'];
		$insert_data['approved_by'] = '';
		$insert_data['approved_on'] = '0000-00-00 00:00:00';
		$insert_data['processed_by'] = '';
		$insert_data['processed_on'] = '0000-00-00 00:00:00';
		$insert_data['status'] = 'new stock in';
		$insert_data['date'] = date('Y-m-d H:i:s');
        $n = $this->siteprocurment->lastidpro_store_return();
		$n=($n !=0)?$n+1:$this->store_id .'1';
		$reference = 'SRR'.str_pad($n , 8, 0, STR_PAD_LEFT);	
        $insert_data['reference_no'] =  $reference;
        $this->db->insert($table_name,$insert_data);
        $insert_id =$this->db->insert_id();
        $unique_id = $this->site->generateUniqueTableID($insert_id,$insert_data['store_id']);
		if ($insert_id) {
			$this->db->set('id',$unique_id);
            $this->db->where('s_no',$insert_id);
            $this->db->update($table_name);
		}
        foreach($items_data as $k => $item){
			$item['store_id'] = $insertData['to_store'];
			$item['store_return_id'] = $unique_id;
			unset($item['store_return_receiver_id'],$item['s_no']);
            $this->db->insert($table_items,$item);
            $i_insert_id =$this->db->insert_id();
            $i_unique_id = $this->site->generateUniqueTableID($i_insert_id,$insert_data['store_id']);
            if ($i_insert_id) {
                $this->db->set('id',$i_unique_id);
                $this->db->where('s_no',$i_insert_id);
                $this->db->update($table_items);
				$i_details = $this->get_store_return_receiver_ItemDetails($item['id']);
			foreach($i_details as $kk => $item_d){
				$item_d['store_id'] = $insertData['to_store'];
				$item_d['store_return_id'] = $unique_id;
				$item_d['store_return_item_id'] = $i_unique_id;
				unset($item_d['store_return_receiver_id']);unset($item_d['store_return_receiver_item_id'],$item_d['s_no']);
				$this->db->insert($table_item_details,$item_d);
				$id_insert_id =$this->db->insert_id();//p($this->db->error());exit;
				$id_unique_id = $this->site->generateUniqueTableID($id_insert_id,$insert_data['store_id']);
				if ($id_insert_id) {
				$this->db->set('id',$id_unique_id);
				$this->db->where('s_no',$id_insert_id);
				$this->db->update($table_item_details);
				}
				}
		
            }
        }
		if($this->isStore){	
			$this->sync_center->sync_store_returns($unique_id);
	    }
        
    }
	 function get_store_return_receiver_ItemDetails($id){
			$q = $this->db->get_where('srampos_pro_store_return_receiver_item_details',array('store_return_receiver_item_id'=>$id));
			return $q->result_array();
    }
	function TransferStockOut($qty,$stockid){
		$store_id = $this->store_id;
		$id=$stockid;	
		$query = 'update '.$this->db->dbprefix('pro_stock_master').'
			set stock_in = stock_in - '.$qty.' ,
			    stock_out = stock_out + '.$qty.'
			where unique_id="'.$id.'"';
	    $this->db->query($query);
		
		return $id;
    }
}
