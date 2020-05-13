<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Store_returns_model extends CI_Model{
    public function __construct()
    {
        parent::__construct();
    }
    public function getProductNames($term, $warehouse_id, $store_id, $limit = 10)
    {
        $this->db->select('products.*, warehouses_products.quantity, pro_stock_master.id as stock_id, pro_stock_master.purchase_batch_no, SUM('.$this->db->dbprefix("pro_stock_master").'.quantity) as available_quantity')
			->join('pro_stock_master', 'pro_stock_master.product_id = products.id AND pro_stock_master.transacton_type = "IN" AND pro_stock_master.store_id = '.$store_id.' ')
            ->join('warehouses_products', 'warehouses_products.product_id=products.id', 'left')
            ->group_by('pro_stock_master.purchase_batch_no');

            $this->db->where("(name LIKE '%" . $term . "%' OR code LIKE '%" . $term . "%' OR  concat(name, ' (', code, ')') LIKE '%" . $term . "%')");

        $this->db->limit($limit);
		
        $q = $this->db->get('products');
		
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
  public function getStore_return_ByID($id)
    {
        $q = $this->db->get_where('pro_store_returns', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	public function getStore_return_Items($store_return_id){
        $this->db->select('pro_store_return_items.*')
            ->group_by('pro_store_return_items.id')
            ->order_by('id', 'asc');
        $q = $this->db->get_where('pro_store_return_items', array('store_return_id' => $store_return_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
   

    public function getItemByID($id)
    {
        $q = $this->db->get_where('pro_store_return_items', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getAllStore_returnsItemsWithDetails($store_return_id){
        $this->db->select('pro_store_return_items.id, pro_store_return_items.product_name, pro_store_return_items.product_code, pro_store_return_items.quantity, pro_store_return_items.serial_no, pro_store_return_items.tax, pro_store_return_items.unit_price, pro_store_return_items.val_tax, pro_store_return_items.discount_val, pro_store_return_items.gross_total, products.details, products.hsn_code as hsn_code, products.name as second_name');
        $this->db->join('products', 'products.id=pro_store_return_items.product_id', 'left');
        $this->db->order_by('id', 'asc');
        $q = $this->db->get_where('pro_store_return_items', array('store_return_id' => $store_return_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    public function getStore_returnsByID($id){
        $q = $this->db->get_where('pro_store_returns', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
      public function getStoreReturnsItems($store_return_id){
        $this->db->select('pro_store_return_items.*')
            ->join('recipe', 'recipe.id=pro_store_return_items.product_id', 'left')
            ->group_by('pro_store_return_items.id')
            ->order_by('id', 'asc');
        $q = $this->db->get_where('pro_store_return_items', array('store_return_id' => $store_return_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
	
    public function getAllStore_returnsItems($store_return_id){
        $this->db->select('pro_store_return_items.*, tax_rates.code as tax_code, tax_rates.name as tax_name, tax_rates.rate as tax_rate, products.unit, products.image, products.details as details, product_variants.name as variant, products.hsn_code as hsn_code, products.name as second_name')
            ->join('products', 'products.id=pro_store_return_items.product_id', 'left')
            ->join('product_variants', 'product_variants.id=pro_store_return_items.option_id', 'left')
            ->join('tax_rates', 'tax_rates.id=pro_store_return_items.tax_rate_id', 'left')
            ->group_by('pro_store_return_items.id')
            ->order_by('id', 'asc');
        $q = $this->db->get_where('pro_store_return_items', array('store_return_id' => $store_return_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
	public function getReturnStockData($item_receiver_id){
        $this->db->select('i.id as itemid,i.store_return_id as stri ,i.store_return_item_id as strii,pi.product_id as id,i.selling_price as price,i.batch as batch_no,i.expiry,i.cost_price,i.return_qty,i.tax,i.tax_method,i.received_qty,i.vendor_id,i.landing_cost,i.invoice_id');
        $this->db->from('pro_store_return_item_details as i');
        $this->db->join('pro_store_return_items as pi', 'pi.id=i.store_return_item_id', 'left');
        $this->db->where('i.store_return_item_id', $item_receiver_id);
        //$this->db->group_by('pi.id');
        //echo $this->db->get_compiled_select();
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            return $q->result();
        }
        return false;

    }
    public function addStore_returns($data = array(), $items = array()){
        if ($this->db->insert('pro_store_returns', $data)) {
            $store_return_id = $this->db->insert_id();
            foreach ($items as $item) {
                $item['store_return_id'] = $store_return_id;
                $this->db->insert('pro_store_return_items', $item);
            }
            return true;
        }
        return false;
    }


   public function updateStoreReturns($id, $data, $items = array()){
        if ($this->db->update('pro_store_returns', $data, array('id' => $id))) {
				$return_referenceno=$data['reference_no'];
            foreach ($items as $item) {
                $batches = $item['batches'];unset($item['batches']);
                $store_return_itemid = $item['store_return_itemid'];
                unset($item['store_return_itemid']);
                $this->db->where(array("id" => $store_return_itemid, "store_return_id" => $id));
                $this->db->update('pro_store_return_items', $item);
                if ($batches) {
                    foreach ($batches as $k => $batch) {
                        $store_return_item_details_id = $batch['id'];
                        unset($batch['id']);
                        $batch['store_return_item_id'] = $batch['store_return_item_id'];
                        $batch['store_return_id'] = $id;
                        $this->db->where(array("id" => $store_return_item_details_id, "store_return_item_id" => $batch['store_return_item_id'], "store_return_id" => $batch['store_return_id']));
                        $this->db->update('pro_store_return_item_details', $batch);
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
							
                             if($batch['return_type']=='exist Qty'){
									echo 1;
								 //update stock
									/* $category_mappingID=$this->siteprocurment->item_cost_update_new($stock_update);
									$stock_update['cm_id']     = $category_mappingID ? $category_mappingID :0; */
									$this->stock_master_update($stock_update);
							 }
							  if($batch['return_type']=='damaged'){
									//damaged stock 
									$this->damaged_stock_master_update($stock_update);
							 }
							 
							  if($batch['return_type']=='Order'){
								// purchase order master generate 
								
								$n = $this->siteprocurment->lastidPurchase();
								$n=($n !=0)?$n+1:$this->store_id .'1';
								$reference = 'PO'.str_pad($n , 8, 0, STR_PAD_LEFT);
								$master['status']                    = "process";
								$master['is_return']                 =1;
								$master['return_reference_no']       =$data['reference_no'];
								$master['reference_no']              =$reference;
								$master['created_by']                =$this->session->userdata('user_id');
								$master['created_on']                 = date('Y-m-d H:i:s');
								$master['processed_by']               =$this->session->userdata('user_id');
								$master['processed_on']               =date('Y-m-d H:i:s');
								$master['grand_total']                =$batch['cost_price'];		
								$master['total']                      =$batch['cost_price'];
								$master['total_discount']             =$batch['cost_price'];
								$master['total_tax']                  =$batch['tax'];
								$supplier=$this->siteprocurment->getCompanyByID($batch['vendor_id']);
								$master['no_of_items']                =1;
								$master['no_of_qty']                  =$batch['received_qty'];
								$master['sub_total']                  =$batch['cost_price'];
								$master['supplier_address']           =$supplier->address;
								$master['supplier']                   =$supplier->company;
								$master['supplier_id']                =$batch['vendor_id'];  
								$this->db->insert('pro_purchase_orders', $master);
								
								$id = $this->db->insert_id();
								$UniqueID                  = $this->site->generateUniqueTableID($id);
								$this->site->updateUniqueTableId($id,$UniqueID,'pro_purchase_orders');
								// purchase order item generate
								$Poitem['purchase_order_id']=$UniqueID;
								$Poitem['store_id']         =$batch['store_id'];
								$Poitem['product_id']       =$item['product_id'];
								$Poitem['variant_id']       =$item['variant_id'];
								$Poitem['product_code']     =$item['product_code'];
								$Poitem['product_name']     =$item['product_name'];
								$Poitem['quantity']         =$batch['return_qty'];
								$Poitem['subtotal']         =$batch['net_amount'];
								$Poitem['item_tax_method']  =$batch['tax_method'];
								$Poitem['tax_rate_id']      =$batch['tax'];
								$Poitem['item_tax']         =$batch['tax_amount'];
								$Poitem['landing_cost']     =$batch['landing_cost'];
								$Poitem['selling_price']    =$batch['selling_price'];  
								$Poitem['net_amt']          =$batch['net_amount'];
								$Poitem['cost']             =$batch['cost_price'];
								$Poitem['gross']            =$batch['net_amount'];
								$Poitem['total']            =$batch['net_amount'];
							//  $item['product_unit_id']=$UniqueID;
							//$item['unit_quantity']=$UniqueID;
								// $item['product_unit_code']=$UniqueID;
								$Poitem['category_id']      = $batch['category_id'];
								$Poitem['subcategory_id']   = $batch['subcategory_id'];
								$Poitem['brand_id']         = $batch['brand_id'];
								$Poitem['option_id']         = $item['variant_id'];
								$this->db->insert("pro_purchase_order_items",$Poitem);
								$this->db->insert_id();
							 }
							
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
		echo 1;
		if($q->num_rows()>0){
			$id = $q->row('id');
			$available_quantity      = $q->row('stock_in');
			$available_quantity =($available_quantity)?$available_quantity:0;
			$stock_update['stock_in']=($available_quantity)+($stock_update['stock_in']);
			$this->db->where('id',$id);
			$this->db->update('pro_stock_master',$stock_update);
		}else{
			$this->db->insert('pro_stock_master',$stock_update);
			$insertID                  = $this->db->insert_id();
			$UniqueID                  = $this->site->generateUniqueTableID($insertID);
			$this->site->updateUniqueTableId($insertID,$UniqueID,'pro_stock_master');
			$return_id = $this->db->insert_id();
		}
		
    }
	
	
	function damaged_stock_master_update($stock_update){
        $date         = date('Y-m-d h:m:s');
		unset($stock_update['invoice_date']);
		$stock_update['created_by']=$this->session->userdata('user_id');
		$stock_update['created_on']=$date;
		$this->db->select();
		$this->db->from('damage_stock_master');
		$this->db->where("unique_id",$stock_update['unique_id']);
		$q = $this->db->get();
		if($q->num_rows()>0){
			$id = $q->row('id');
			$available_quantity      = $q->row('stock_in');
			$available_quantity =($available_quantity)?$available_quantity:0;
			$stock_update['stock_in']=($available_quantity)+($stock_update['stock_in']);
			$this->db->where('id',$id);
			$this->db->update('damage_stock_master',$stock_update);
		}else{
			$this->db->insert('damage_stock_master',$stock_update);
			$insertID                  = $this->db->insert_id();
			$UniqueID                  = $this->site->generateUniqueTableID($insertID);
			$this->site->updateUniqueTableId($insertID,$UniqueID,'damage_stock_master');
			$return_id = $this->db->insert_id();
		}
		
    }
	
	
	
	
	

    public function updateStatus($id, $status, $note){
        if ($this->db->update('pro_store_returns', array('status' => $status, 'note' => $note), array('id' => $id))) {
            return true;
        }
        return false;
    }
    public function deleteStore_returns($id){
        if ($this->db->delete('pro_quote_items', array('store_return_id' => $id)) && $this->db->delete('pro_store_returns', array('id' => $id))) {
            return true;
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

    public function getWarehouseProductQuantity($warehouse_id, $product_id)
    {
        $q = $this->db->get_where('warehouses_products', array('warehouse_id' => $warehouse_id, 'product_id' => $product_id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getProductComboItems($pid, $warehouse_id)
    {
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

    public function getProductOptions($product_id, $warehouse_id)
    {
        $this->db->select('product_variants.id as id, product_variants.name as name, product_variants.price as price, product_variants.quantity as total_quantity, warehouses_products_variants.quantity as quantity')
            ->join('warehouses_products_variants', 'warehouses_products_variants.option_id=product_variants.id', 'left')
            //->join('warehouses', 'warehouses.id=product_variants.warehouse_id', 'left')
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

    public function getProductOptionByID($id)
    {
        $q = $this->db->get_where('product_variants', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

}
