<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Store_transfers_model extends CI_Model{
    public function __construct()
    {
        parent::__construct();
    }
    public function getProductNames($term, $limit = 10){
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
	
	public function getReqBYID($id){
        $q = $this->db->get_where('pro_store_request', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	
	public function getStore_transferByID($id){
        $q = $this->db->get_where('pro_store_transfers', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	
	public function checkPendingQTY($product_id, $quantity, $requestnumber){
		$q = $this->db->select('id')->where('requestnumber', $requestnumber)->order_by('id', 'DESC')->limit(1)->get('pro_store_transfers');
		if ($q->num_rows() > 0) {
			$s = $this->db->select('pending_quantity')->where('product_id', $product_id)->where('store_transfer_id', $q->row('id'))->get('pro_store_transfer_items');
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
		$q = $this->db->select('id')->where('id', $id)->order_by('id', 'DESC')->limit(1)->get('pro_store_transfers');
		if ($q->num_rows() > 0) {
			$s = $this->db->select('pending_quantity')->where('product_id', $product_id)->where('store_transfer_id', $q->row('id'))->get('pro_store_transfer_items');
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
	
	public function getAllStore_transferItems($store_transfer_id){
        $this->db->select('pro_store_transfer_items.*, tax_rates.code as tax_code, tax_rates.name as tax_name, tax_rates.rate as tax_rate, products.unit, products.details as details, product_variants.name as variant, products.hsn_code as hsn_code')
            ->join('products', 'products.id=pro_store_transfer_items.product_id', 'left')
            ->join('product_variants', 'product_variants.id=pro_store_transfer_items.option_id', 'left')
            ->join('tax_rates', 'tax_rates.id=pro_store_transfer_items.tax_rate_id', 'left')
            ->group_by('pro_store_transfer_items.id')
            ->order_by('id', 'asc');
        $q = $this->db->get_where('pro_store_transfer_items', array('store_transfer_id' => $store_transfer_id));
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

    public function getAllStore_transfers()
    {
        $q = $this->db->get('pro_store_transfers');
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
	
	public function getAllRequestItems($store_transfers_id){
         $this->db->select('pro_store_request_items.*')
	    ->from('pro_store_request_items')
            ->join('recipe', 'recipe.id=pro_store_request_items.product_id', 'left')
            ->join('recipe_variants', 'recipe_variants.id=pro_store_request_items.option_id', 'left')
            ->where('store_request_id' ,$store_transfers_id)
	    ->group_by('pro_store_request_items.id')
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
   
	
    public function getAllStore_transfersItems($store_transfers_id){
        $this->db->select('pro_store_transfer_items.*,pid.batch,pid.cost_price,pid.landing_cost,pid.selling_price,pid.tax,pid.tax_method,pid.expiry,pid.pending_qty,pid.stock_id ,pid.category_id,pid.subcategory_id,pid.brand_id,pid.variant_id')
	    ->from('pro_store_transfer_items')      
		->join('pro_store_transfer_item_details pid','pid.store_transfer_item_id=pro_store_transfer_items.id','left')
        ->where('pro_store_transfer_items.store_transfer_id' ,$store_transfers_id)
	    ->group_by('pro_store_transfer_items.id')
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

    public function getItemByID($id){
        $q = $this->db->get_where('pro_store_transfer_items', array('id' => $id), 1);
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

    public function getStore_transfersByID($id)
    {
        $q = $this->db->get_where('pro_store_transfers', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	
	 public function getRequestByID($id)
    {
        $q = $this->db->get_where('pro_store_request', array('id' => $id), 1);
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

    public function getOverSoldCosting($product_id){
        $q = $this->db->get_where('costing', array('overselling' => 1));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

   public function addStore_transfers($data, $items, $store_transfers, $order_id){
			$this->db->insert('pro_store_transfers', $data);
			$store_transfers_id = $this->db->insert_id();
	        $unique_id = $this->site->generateUniqueTableID($store_transfers_id);
			if ($store_transfers_id) {
				$this->site->updateUniqueTableId($store_transfers_id,$unique_id,'pro_store_transfers');
			}
			if ($unique_id) {
				if($data['intend_request_id']!=''){
					$u_data['is_processed'] =1;
					$u_data['status'] ="Completed";
					$this->db->where('id',$data['intend_request_id']);
					$this->db->update('pro_store_indent_receive',$u_data);
			}
			$store_transfers['is_processed'] = 1;
			$this->db->update('pro_stock_request', $store_transfers, array('id' => $order_id));
			foreach ($items as $item) {
				if($item['batches'] !=0){
				   $batches = $item['batches'];unset($item['batches']);
				   $item['store_transfer_id'] = $unique_id;
				   $this->db->insert('pro_store_transfer_items', $item);
				   $item_insert_id = $this->db->insert_id();
				   $i_unique_id = $this->site->generateUniqueTableID($item_insert_id);
				   if ($item_insert_id) {
				     $this->site->updateUniqueTableId($item_insert_id,$i_unique_id,'pro_store_transfer_items');
				   }
					foreach ($batches as $k => $batch) {
						$stock_id = $batch['stock_id'];
						$batch['store_transfer_item_id'] = $i_unique_id;
						$batch['store_transfer_id'] = $unique_id;
						$invoice_id = $batch['invoice_id'];
						$this->db->insert('pro_store_transfer_item_details', $batch);
						$item_d_insert_id = $this->db->insert_id();
						$id_unique_id = $this->site->generateUniqueTableID($item_d_insert_id);
						if ($item_d_insert_id) {
							$this->site->updateUniqueTableId($item_d_insert_id,$id_unique_id,'pro_store_transfer_item_details');
							if($data['status']=="approved"){
									$cp = str_replace('.','_',$batch['cost_price']);
									$cunique_id=$data['to_store'].$item['product_id'].$batch['variant_id'].$batch['batch'].$batch['category_id'].$batch['subcategory_id'].$batch['brand_id'].$cp.$batch['vendor_id'].$batch['invoice_id'];	
									$cat_mapp_data['unique_id']      =$cunique_id;
									$cat_mapp_data['store_id']       =$data['to_store'];
									$cat_mapp_data['product_id']     =$item['product_id'];
									$cat_mapp_data['variant_id']     =$batch['variant_id'];
									$cat_mapp_data['category_id']    =$batch['category_id'];
									$cat_mapp_data['subcategory_id'] =$batch['subcategory_id'];
									$cat_mapp_data['brand_id']       =$batch['brand_id'];
									$cat_mapp_data['batch_no']       =$batch['batch'];
									$cat_mapp_data['purchase_cost']  =$batch['cost_price'];
									$cat_mapp_data['vendor_id']      =$batch['vendor_id'];
									$cat_mapp_data['invoice_id']     =$batch['invoice_id'];
									$cat_mapp_data['selling_price']  =$batch['selling_price'];
									$cat_mapp_data['pieces_selling_price']=0.00;
									$cat_mapp_data['status']			  =1;
									$this->category_mapping_update($cat_mapp_data);
									$transfer_qty						  =($batch['transfer_unit_qty'])?$batch['transfer_unit_qty']:$batch['transfer_qty'];
								    $this->TransferStockOut($transfer_qty,$batch['stock_id']);  
			        }
		          }
			   }
			}
	    }
	        if($data['status']=="approved"){
		        $this->sync_store_receivers($unique_id);
	       }
            return true;
        }
        return false;
    }
 
  
   public function updateStore_transfers($id, $data, $items = array()) {
        if ($this->db->update('pro_store_transfers', $data, array('id' => $id)) && $this->db->delete('pro_store_transfer_items', array('store_transfer_id' => $id))) {
            $store_transfers_id = $id;
	    $this->db->delete('pro_store_transfer_item_details', array('store_transfer_id' => $id));
	    if($data['intend_request_id']!=''){
		$u_data['is_processed'] =1;
		$u_data['status'] ="Completed";
		$this->db->where('id',$data['intend_request_id']);
		$this->db->update('pro_store_indent_receive',$u_data);
	    }
		$tostore=$data['to_store'];
		unset($data['to_store']);
            foreach ($items as $item) {
				if($item['batches'] !=0){
		        $batches = $item['batches'];unset($item['batches']);
                $item['store_transfer_id'] = $id;
                $this->db->insert('pro_store_transfer_items', $item);
	        	$item_insert_id = $this->db->insert_id();
	        	$i_unique_id = $this->site->generateUniqueTableID($item_insert_id);
		   if ($item_insert_id) {
				$this->site->updateUniqueTableId($item_insert_id,$i_unique_id,'pro_store_transfer_items');
				foreach ($batches as $k => $batch) {
				$stock_id = $batch['stock_id'];//unset($batch['stock_id']);
				$batch['store_transfer_item_id'] = $i_unique_id;
				$batch['store_transfer_id'] = $id;
				$invoice_id = $batch['invoice_id'];
				$this->db->insert('pro_store_transfer_item_details', $batch);
				$item_d_insert_id = $this->db->insert_id();
				$id_unique_id = $this->site->generateUniqueTableID($item_d_insert_id);
		    if ($item_d_insert_id) {
				$this->site->updateUniqueTableId($item_d_insert_id,$id_unique_id,'pro_store_transfer_item_details');
				if($data['status']=="approved"){
									$cp = str_replace('.','_',$batch['cost_price']);
									$cunique_id=$data['to_store'].$item['product_id'].$batch['variant_id'].$batch['batch'].$batch['category_id'].$batch['subcategory_id'].$batch['brand_id'].$cp.$batch['vendor_id'].$batch['invoice_id'];	
									$cat_mapp_data['unique_id']      =$cunique_id;
									$cat_mapp_data['store_id']       =$data['to_store'];
									$cat_mapp_data['product_id']     =$item['product_id'];
									$cat_mapp_data['variant_id']     =$batch['variant_id'];
									$cat_mapp_data['category_id']    =$batch['category_id'];
									$cat_mapp_data['subcategory_id'] =$batch['subcategory_id'];
									$cat_mapp_data['brand_id']       =$batch['brand_id'];
									$cat_mapp_data['batch_no']       =$batch['batch'];
									$cat_mapp_data['purchase_cost']  =$batch['cost_price'];
									$cat_mapp_data['vendor_id']      =$batch['vendor_id'];
									$cat_mapp_data['invoice_id']     =$batch['invoice_id'];
									$cat_mapp_data['selling_price']  =$batch['selling_price'];
									$cat_mapp_data['pieces_selling_price']=0.00;
									$cat_mapp_data['status']=1;
									$this->category_mapping_update($cat_mapp_data);
									$transfer_qty=($batch['transfer_unit_qty'])?$batch['transfer_unit_qty']:$batch['transfer_qty'];
								    $this->TransferStockOut($transfer_qty,$batch['stock_id']);  
					}
		        }
		     }
		  }
		}
       }
	    if($data['status']=="approved"){
		    $this->sync_store_receivers($id);
	    }
           return true;
        }

        return false;
    }
  
   function category_mapping_update($cat_mapp_data){
	  $q=$this->db->get_where("category_mapping",array("unique_id"=>$cat_mapp_data['unique_id']));
	  if($q->num_rows()>0){
		  $q=$q->row();
		  $this->db->where("unique_id",$cat_mapp_data['unique_id']);
		  $this->db->update("category_mapping",$cat_mapp_data);
		     return $q->id;
	  }else{
			$this->db->insert("category_mapping",$cat_mapp_data);
			$insertID                  = $this->db->insert_id();
			$UniqueID                  = $this->site->generateUniqueTableID($insertID);
			$this->site->updateUniqueTableId($insertID,$UniqueID,'category_mapping');
			return $UniqueID;
	  }
	  
	  
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
	 function sync_store_receivers($transfer_id){
		$q 			= $this->db->get_where('pro_store_transfers',array('id'=>$transfer_id));
		$insertData = $q->row_array();
		$q 			= $this->db->get_where('pro_store_transfer_items',array('store_transfer_id'=>$transfer_id));
		$items_data = $q->result_array();
		unset($insertData['attachment'],$insertData['s_no']);
		$table_name = 'pro_store_receivers';
		$table_items = 'pro_store_receiver_items';
		$table_item_details = 'pro_store_receiver_item_details';
		$insert_data = $insertData;
		$insert_data['store_id'] = $insertData['to_store'];
		$insert_data['approved_by'] = '';
		$insert_data['approved_on'] = '0000-00-00 00:00:00';
		$insert_data['processed_by'] = '';
		$insert_data['processed_on'] = '0000-00-00 00:00:00';
		$insert_data['status'] = 'new stock in';
		$insert_data['date'] = date('Y-m-d H:i:s');
        $n = $this->lastidStoreReceiver();
	    $reference = 'SRE'.str_pad($n + 1, 5, 0, STR_PAD_LEFT);
        $insert_data['reference_no'] =  str_replace('ST','SRE',$insert_data['reference_no']);
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
	    $item['store_receiver_id'] = $unique_id;
	        unset($item['store_transfer_id'],$item['available_qty'],$item['pending_qty'],$item['store_transfer_id'],$item['s_no']);
            $this->db->insert($table_items,$item);
            $i_insert_id =$this->db->insert_id();
            $i_unique_id = $this->site->generateUniqueTableID($i_insert_id,$insert_data['store_id']);
            if ($i_insert_id) {
                $this->db->set('id',$i_unique_id);
                $this->db->where('s_no',$i_insert_id);
                $this->db->update($table_items);
		$i_details = $this->getStoreTItemDetails($item['id']);
		foreach($i_details as $kk => $item_d){
		    $item_d['store_id'] = $insertData['to_store'];
		    $item_d['store_receiver_id'] = $unique_id;
		    $item_d['store_receiver_item_id'] = $i_unique_id;
		    $item_d['transfer_qty'] = $item_d['transfer_qty'];
		    unset($item_d['store_transfer_id']);unset($item_d['store_transfer_item_id']);
		    unset($item_d['request_qty'],$item_d['available_qty'],$item_d['pending_qty'],$item_d['s_no']);
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
    }
	    function lastidStoreReceiver(){
			$this->db->order_by('id' , 'DESC');
			$q = $this->db->get('pro_store_receivers');
			if ($q->num_rows() > 0) {
				return $q->row('id');
			}
			return 0;
    }
	 function getStoreTItemDetails($id){
			$q = $this->db->get_where('pro_store_transfer_item_details',array('store_transfer_item_id'=>$id));
			return $q->result_array();
    }
  
  
   function getStockRequestsData($store_id,$request_id){
        $this->db->select('id,reference_no,date');
        $this->db->from('pro_stock_request');
        $this->db->where('is_completed',0);
        $this->db->where('is_processed',0);
        $this->db->where('from_store_id',$store_id);
        $this->db->where('id',$request_id);
	//echo $this->db->get_compiled_select();
        $q = $this->db->get();$data= array();
        if($q->num_rows()>0){
            $data = $q->row();
            $this->db->from('pro_stock_request_items');
            $this->db->where('stock_request_id',$request_id);
            $p = $this->db->get();
            $data->items = $p->result();
            return $data;
        }
        return false;
    }
  
  
  
    public function updateStatus($id, $status, $note){
        $items = $this->siteprocurment->getAllStore_transfersItems($id);
        if ($this->db->update('pro_store_transfer_items', array('status' => $status, 'note' => $note), array('id' => $id))) {
            foreach ($items as $item) {
                $qb = $status == 'completed' ? ($item->quantity_balance + ($item->quantity - $item->quantity_received)) : $item->quantity_balance;
                $qr = $status == 'completed' ? $item->quantity : $item->quantity_received;
                $this->db->update('pro_purchase_items', array('status' => $status, 'quantity_balance' => $qb, 'quantity_received' => $qr), array('id' => $item->id));
                $this->updateAVCO(array('product_id' => $item->product_id, 'warehouse_id' => $item->warehouse_id, 'quantity' => $item->quantity, 'cost' => $item->real_unit_cost));
            }
            $this->siteprocurment->syncQuantity(NULL, NULL, $items);
            return true;
        }
        return false;
    }

    public function deleteStore_transfers($id){
        $purchase = $this->getStore_transfersByID($id);
        $purchase_items = $this->siteprocurment->getAllStore_transfersItems($id);
        if ($this->db->delete('pro_store_transfer_items', array('store_transfers_id' => $id)) && $this->db->delete('pro_store_transfers', array('id' => $id))) {
            // $this->db->delete('payments', array('store_transfer_id' => $id));
            // if ($purchase->status == 'received' || $purchase->status == 'partial') {
            //     foreach ($purchase_items as $oitem) {
            //         $this->updateAVCO(array('product_id' => $oitem->product_id, 'warehouse_id' => $oitem->warehouse_id, 'quantity' => (0-$oitem->quantity), 'cost' => $oitem->real_unit_cost));
            //         $received = $oitem->quantity_received ? $oitem->quantity_received : $oitem->quantity;
            //         if ($oitem->quantity_balance < $received) {
            //             $clause = array('store_transfer_id' => NULL, 'transfer_id' => NULL, 'product_id' => $oitem->product_id, 'warehouse_id' => $oitem->warehouse_id, 'option_id' => $oitem->option_id);
            //             $this->siteprocurment->setPurchaseItem($clause, ($oitem->quantity_balance - $received));
            //         }
            //     }
            // }
            $this->siteprocurment->syncQuantity(NULL, NULL, $purchase_items);
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

  
 public function loadbatches($productid){
		$type = array('standard','raw');
		$this->db->select('pro_stock_master.*,r.id');
		$this->db->from('recipe r');
		$this->db->join('tax_rates t','r.purchase_tax=t.id','left');
		$this->db->join('pro_stock_master','pro_stock_master.product_id=r.id AND pro_stock_master.store_id='.$this->store_id);
		$this->db->where('r.id',$productid);
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
	function getbatchStockData($item_transfer_id){
		$this->db->select('pi.product_id as id,i.transfer_qty,i.tax_method,i.pending_qty,i.tax,pro_stock_master.stock_in ,pro_stock_master.unique_id as stock_id,pro_stock_master.supplier_id,pro_stock_master.invoice_id,pro_stock_master.selling_price,pro_stock_master.batch,pro_stock_master.expiry,pro_stock_master.cost_price,pro_stock_master.landing_cost,DATE(i.expiry) as expiry_date,i.brand_id as brand_id,i.variant_id as variant_id,i.stock_id as unique_id');
		$this->db->from('pro_store_transfer_item_details as i');
		$this->db->join('pro_store_transfer_items pi', 'pi.id=i.store_transfer_item_id and pi.store_id='.$this->store_id);
		$this->db->join('warehouses_recipe wr', 'wr.recipe_id=pi.product_id and warehouse_id='.$this->store_id,'left');
		$this->db->join('pro_stock_master','pro_stock_master.unique_id=i.stock_id AND pro_stock_master.store_id='.$this->store_id);
		$this->db->where('i.store_transfer_item_id',$item_transfer_id);
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
	 function getStockReference($id){
		$this->db->select('id,reference_no,date');
        $this->db->from('pro_stock_request');
        $this->db->where('id',$id);
		//echo $this->db->get_compiled_select();
        $q = $this->db->get();$data= array();
        if($q->num_rows()>0){
            $data = $q->result();
	    return $data;
	}
		return false;
    }
	 function getStoreindentData($id){
		$q = $this->db->get_where('pro_stock_request',array('id'=>$id));
		return $q->row();
    }
}
