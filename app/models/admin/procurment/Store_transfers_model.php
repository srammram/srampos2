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
	
	public function getAllStore_transferItems($store_transfer_id)
    {
		
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
	
	public function getAllRequestItems($store_transfers_id)
    {
         $this->db->select('pro_store_request_items.*')
	    ->from('pro_store_request_items')
            ->join('recipe', 'recipe.id=pro_store_request_items.product_id', 'left')
            ->join('recipe_variants', 'recipe_variants.id=pro_store_request_items.option_id', 'left')
            ->where('store_request_id' ,$store_transfers_id)
	    ->group_by('pro_store_request_items.id')
            ->order_by('id', 'asc');
	   /*  echo $this->db->get_compiled_select();
		die; */
		
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
        $this->db->select('pro_store_transfer_items.*,pid.batch,pid.cost_price,pid.landing_cost,pid.selling_price,pid.tax,pid.tax_method,pid.expiry,pid.pending_qty ')
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
		$this->db->where('id',$data['intend_request_id']);
		$this->db->update('pro_stock_request',$u_data);
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
		    $stock_id = $batch['stock_id'];//unset($batch['stock_id']);
		    $batch['store_transfer_item_id'] = $i_unique_id;
		    $batch['store_transfer_id'] = $unique_id;
			$invoice_id = $batch['invoice_id'];
		//	unset($batch['invoice_id']);
		    $this->db->insert('pro_store_transfer_item_details', $batch);
			
		    $item_d_insert_id = $this->db->insert_id();
			
		    $id_unique_id = $this->site->generateUniqueTableID($item_d_insert_id);
		    if ($item_d_insert_id) {
			$this->site->updateUniqueTableId($item_d_insert_id,$id_unique_id,'pro_store_transfer_item_details');
			if($data['status']=="approved"){
            // $this->stock_model->price_master_update($data['to_store'],$item['product_id'],$batch['batch'],$batch['cost_price'],$batch['vendor_id'],$invoice_id,$batch['selling_price']);
			//    $this->stock_model->TransferStockOut($data['product_id'],$batch['transfer_qty'],$stock_id);  
			}
		    }
		}
			}
	    }
	    if($data['status']=="approved"){
		if($this->isStore){		
		   // $this->sync_center->sync_store_transfers($unique_id);	
              //   $sync_now = true;
              //   $this->site->start_sync($sync_now);		
		}else{
		   // $this->sync_store_receivers($unique_id);
		}
	    }
            return true;
        }
	
        return false;
    }

    public function updateStore_transfers($id, $data, $items = array())
    {
		
        if ($this->db->update('pro_store_transfers', $data, array('id' => $id)) && $this->db->delete('pro_store_transfer_items', array('store_transfer_id' => $id))) {
            $store_transfers_id = $id;
            foreach ($items as $item) {
                $item['store_transfer_id'] = $id;
                $this->db->insert('pro_store_transfer_items', $item);
            }
         
            return true;
        }

        return false;
    }

    public function updateStatus($id, $status, $note)
    {
        // $purchase = $this->getStore_transfersByID($id);
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

    public function deleteStore_transfers($id)
    {
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

    public function getPurchasePayments($store_transfer_id)
    {
        $this->db->order_by('id', 'asc');
        $q = $this->db->get_where('payments', array('store_transfer_id' => $store_transfer_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    public function getPaymentByID($id)
    {
        $q = $this->db->get_where('payments', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return FALSE;
    }

    public function getPaymentsForPurchase($store_transfer_id)
    {
        $this->db->select('payments.date, payments.paid_by, payments.amount, payments.reference_no, users.first_name, users.last_name, type')
            ->join('users', 'users.id=payments.created_by', 'left');
        $q = $this->db->get_where('payments', array('store_transfer_id' => $store_transfer_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function addPayment($data = array())
    {
        if ($this->db->insert('payments', $data)) {
            if ($this->siteprocurment->getReference('ppay') == $data['reference_no']) {
                $this->siteprocurment->updateReference('ppay');
            }
            $this->siteprocurment->syncPurchasePayments($data['store_transfer_id']);
            return true;
        }
        return false;
    }

    public function updatePayment($id, $data = array())
    {
        if ($this->db->update('payments', $data, array('id' => $id))) {
            $this->siteprocurment->syncPurchasePayments($data['store_transfer_id']);
            return true;
        }
        return false;
    }

    public function deletePayment($id)
    {
        $opay = $this->getPaymentByID($id);
        if ($this->db->delete('payments', array('id' => $id))) {
            $this->siteprocurment->syncPurchasePayments($opay->store_transfer_id);
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

    public function getExpenseByID($id)
    {
        $q = $this->db->get_where('expenses', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function addExpense($data = array())
    {
        if ($this->db->insert('expenses', $data)) {
            if ($this->siteprocurment->getReference('ex') == $data['reference']) {
                $this->siteprocurment->updateReference('ex');
            }
            return true;
        }
        return false;
    }

    public function updateExpense($id, $data = array())
    {
        if ($this->db->update('expenses', $data, array('id' => $id))) {
            return true;
        }
        return false;
    }

    public function deleteExpense($id)
    {
        if ($this->db->delete('expenses', array('id' => $id))) {
            return true;
        }
        return FALSE;
    }

   

    public function getReturnByID($id)
    {
        $q = $this->db->get_where('return_store_transfers', array('id' => $id), 1);
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

    public function getPurcahseItemByID($id)
    {
        $q = $this->db->get_where('pro_purchase_items', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function returnPurchase($data = array(), $items = array())
    {

        $purchase_items = $this->siteprocurment->getAllStore_transfersItems($data['store_transfers_id']);

        if ($this->db->insert('return_store_transfers', $data)) {
            $return_id = $this->db->insert_id();
            if ($this->siteprocurment->getReference('rep') == $data['reference_no']) {
                $this->siteprocurment->updateReference('rep');
            }
            foreach ($items as $item) {
                $item['return_id'] = $return_id;
                $this->db->insert('return_purchase_items', $item);

                if ($purchase_item = $this->getPurcahseItemByID($item['purchase_item_id'])) {
                    if ($purchase_item->quantity == $item['quantity']) {
                        $this->db->delete('pro_purchase_items', array('id' => $item['purchase_item_id']));
                    } else {
                        $nqty = $purchase_item->quantity - $item['quantity'];
                        $bqty = $purchase_item->quantity_balance - $item['quantity'];
                        $rqty = $purchase_item->quantity_received - $item['quantity'];
                        $tax = $purchase_item->unit_cost - $purchase_item->net_unit_cost;
                        $discount = $purchase_item->item_discount / $purchase_item->quantity;
                        $item_tax = $tax * $nqty;
                        $item_discount = $discount * $nqty;
                        $subtotal = $purchase_item->unit_cost * $nqty;
                        $this->db->update('pro_purchase_items', array('quantity' => $nqty, 'quantity_balance' => $bqty, 'quantity_received' => $rqty, 'item_tax' => $item_tax, 'item_discount' => $item_discount, 'subtotal' => $subtotal), array('id' => $item['purchase_item_id']));
                    }

                }
            }
            $this->calculatePurchaseTotals($data['store_transfer_id'], $return_id, $data['surcharge']);
            $this->siteprocurment->syncQuantity(NULL, NULL, $purchase_items);
            $this->siteprocurment->syncQuantity(NULL, $data['store_transfer_id']);
            return true;
        }
        return false;
    }

    public function calculatePurchaseTotals($id, $return_id, $surcharge)
    {
        $purchase = $this->getStore_transfersByID($id);
        $items = $this->getAllStore_transfersItems($id);
        if (!empty($items)) {
            $total = 0;
            $product_tax = 0;
            $order_tax = 0;
            $product_discount = 0;
            $order_discount = 0;
            foreach ($items as $item) {
                $product_tax += $item->item_tax;
                $product_discount += $item->item_discount;
                $total += $item->net_unit_cost * $item->quantity;
            }
            if ($purchase->order_discount_id) {
                $percentage = '%';
                $order_discount_id = $purchase->order_discount_id;
                $opos = strpos($order_discount_id, $percentage);
                if ($opos !== false) {
                    $ods = explode("%", $order_discount_id);
                    $order_discount = (($total + $product_tax) * (Float)($ods[0])) / 100;
                } else {
                    $order_discount = $order_discount_id;
                }
            }
            if ($purchase->order_tax_id) {
                $order_tax_id = $purchase->order_tax_id;
                if ($order_tax_details = $this->siteprocurment->getTaxRateByID($order_tax_id)) {
                    if ($order_tax_details->type == 2) {
                        $order_tax = $order_tax_details->rate;
                    }
                    if ($order_tax_details->type == 1) {
                        $order_tax = (($total + $product_tax - $order_discount) * $order_tax_details->rate) / 100;
                    }
                }
            }
            $total_discount = $order_discount + $product_discount;
            $total_tax = $product_tax + $order_tax;
            $grand_total = $total + $total_tax + $purchase->shipping - $order_discount + $surcharge;
            $data = array(
                'total' => $total,
                'product_discount' => $product_discount,
                'order_discount' => $order_discount,
                'total_discount' => $total_discount,
                'product_tax' => $product_tax,
                'order_tax' => $order_tax,
                'total_tax' => $total_tax,
                'grand_total' => $grand_total,
                'return_id' => $return_id,
                'surcharge' => $surcharge
            );

            if ($this->db->update('pro_store_transfers', $data, array('id' => $id))) {
                return true;
            }
        } else {
            $this->db->delete('pro_store_transfers', array('id' => $id));
        }
        return FALSE;
    }

    public function getExpenseCategories()
    {
        $q = $this->db->get('expense_categories');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getExpenseCategoryByID($id)
    {
        $q = $this->db->get_where("expense_categories", array('id' => $id), 1);
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
		$this->db->select('pi.product_id as id,i.transfer_qty,i.tax_method,i.pending_qty,i.tax,pro_stock_master.stock_in as available_stock,pro_stock_master.unique_id as stock_id,pro_stock_master.supplier_id,pro_stock_master.invoice_id,pro_stock_master.selling_price,pro_stock_master.batch,pro_stock_master.expiry,pro_stock_master.cost_price,pro_stock_master.landing_cost,DATE(i.expiry) as expiry_date');
		$this->db->from('pro_store_transfer_item_details as i');
		$this->db->join('pro_store_transfer_items pi', 'pi.id=i.store_transfer_item_id and pi.store_id='.$this->store_id);
		$this->db->join('warehouses_recipe wr', 'wr.recipe_id=pi.product_id and warehouse_id='.$this->store_id,'left');
		$this->db->join('pro_stock_master','pro_stock_master.unique_id=i.stock_id AND pro_stock_master.store_id='.$this->store_id);
		$this->db->where('i.store_transfer_item_id',$item_transfer_id);
		$q = $this->db->get();
		if($q->num_rows()>0){
			  foreach (($q->result()) as $k=> $row) {	
			if($row->batch==null){$row->batch="No Batch";}
		      $row->available_stock = $row->available_stock;			
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
}
