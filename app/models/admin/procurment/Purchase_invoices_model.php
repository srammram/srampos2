<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Purchase_invoices_model extends CI_Model{
    public function __construct()
    {
        parent::__construct();
    }
	public function addStorepurchasewise($data, $quote_id, $product_id){
		$this->db->where('purchase_order_id', $quote_id);
		$this->db->where('product_id', $product_id);
		$this->db->delete('delivery_store');
		if($this->db->insert_batch('delivery_store', $data)){
			
			return true;
		}
		return false;
	}
	
	public function productQuotesID($product_id, $quote_id){
		$this->db->where('purchase_order_id', $quote_id);
		$this->db->where('product_id', $product_id);
		$q = $this->db->get('delivery_store');
		if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
		return false;
	}
	
	public function getProductStores($product_id){
		$this->db->select('stores_products.store_id, stores.name');
		$this->db->join('pro_stores', 'stores.id = stores_products.store_id');
		$this->db->where('stores_products.product_id', $product_id);
		$q = $this->db->get('pro_stores_products');
		if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
		return false;
	}
	
    public function getProductNames($term, $limit = 10){
	    $type = array('standard','raw');
	    $this->db->select('r.*,t.rate as purchase_tax_rate');
	    $this->db->from('recipe r');
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
	
	public function getReqBYID($id){
        $q = $this->db->get_where('pro_purchase_orders', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	
	public function getQuoteByID($id){
        $q = $this->db->get_where('pro_purchase_invoices', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	
	
	public function getAllQuoteItems($purchase_order_id){
        $this->db->select('pro_purchase_invoice_items.*, tax_rates.code as tax_code, tax_rates.name as tax_name, tax_rates.rate as tax_rate, products.unit, products.details as details, product_variants.name as variant, products.hsn_code as hsn_code')
            ->join('products', 'products.id=pro_purchase_invoice_items.product_id', 'left')
            ->join('product_variants', 'product_variants.id=pro_purchase_invoice_items.option_id', 'left')
            ->join('tax_rates', 'tax_rates.id=pro_purchase_invoice_items.tax_rate_id', 'left')
            ->group_by('pro_purchase_invoice_items.id')
            ->order_by('id', 'asc');
        $q = $this->db->get_where('pro_purchase_invoice_items', array('purchase_order_id' => $purchase_order_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
    public function getAllProducts(){
        $q = $this->db->get('products');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getProductByID($id){
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
    public function getProductsByCode($code){
        $this->db->select('*')->from('products')->like('code', $code, 'both');
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    public function getProductByCode($code){
        $q = $this->db->get_where('products', array('code' => $code), 1);
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

    public function getAllPurchase_invoices(){
        $q = $this->db->get('pro_purchase_invoices');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }
	
	public function getAllRequestItems($purchase_invoices_id){
        $this->db->select('pro_purchase_order_items.*, tax_rates.code as tax_code, tax_rates.name as tax_name, tax_rates.rate as tax_rate, products.unit, products.details as details, product_variants.name as variant, products.hsn_code as hsn_code')
            ->join('products', 'products.id=pro_purchase_order_items.product_id', 'left')
            ->join('product_variants', 'product_variants.id=pro_purchase_order_items.option_id', 'left')
            ->join('tax_rates', 'tax_rates.id=pro_purchase_order_items.tax_rate_id', 'left')
            ->group_by('pro_purchase_order_items.id')
            ->order_by('id', 'asc');
        $q = $this->db->get_where('pro_purchase_order_items', array('purchase_order_id' => $purchase_invoices_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
    public function getAllPurchase_invoicesItems($purchase_invoices_id)
    {
        $this->db->select('pro_purchase_invoice_items.*')
            ->join('recipe', 'recipe.id=pro_purchase_invoice_items.product_id', 'left')
            ->join('recipe_variants', 'recipe_variants.id=pro_purchase_invoice_items.variant_id', 'left')
            ->join('tax_rates', 'tax_rates.id=pro_purchase_invoice_items.tax_rate_id', 'left')
            ->group_by('pro_purchase_invoice_items.id,pro_purchase_invoice_items.variant_id')
            ->order_by('id', 'asc');
        $q = $this->db->get_where('pro_purchase_invoice_items', array('invoice_id' => $purchase_invoices_id));
		//print_r($this->db->last_query());die;
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
        $q = $this->db->get_where('pro_purchase_invoice_items', array('id' => $id), 1);
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

    public function getPurchase_invoicesByID($id)
    {
        $this->db->select('pro_purchase_invoices.*,pro_purchase_orders.reference_no as po_no,pro_purchase_orders.date as po_date')
	->from('pro_purchase_invoices')
	->join('pro_purchase_orders','pro_purchase_orders.id=pro_purchase_invoices.po_number','left')
	->where('pro_purchase_invoices.id', $id)
	->limit(1);
	//echo $this->db->get_compiled_select();
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	
	 public function getRequestByID($id)
    {
        $q = $this->db->get_where('pro_request', array('id' => $id), 1);
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

    public function getProductWarehouseOptionQty($option_id, $warehouse_id){
        $q = $this->db->get_where('warehouses_products_variants', array('option_id' => $option_id, 'warehouse_id' => $warehouse_id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function addProductOptionQuantity($option_id, $warehouse_id, $quantity, $product_id){
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

    public function addPurchase_invoices($data, $items,$po_array){
        if ($this->db->insert('pro_purchase_invoices', $data)) {
            $id = $this->db->insert_id();
			$UniqueID                  = $this->site->generateUniqueTableID($id);
			$this->site->updateUniqueTableId($id,$UniqueID,'pro_purchase_invoices');
            if($po_array){
			     $this->db->update('pro_purchase_orders', $po_array, array('id' => $data['po_number']));
            }
            foreach ($items as $item) {
                /*** insert invoice items **/
				$item['invoice_id'] = $UniqueID;
				$cp = str_replace('.','_',$item['cost']);
				$item['pi_uniqueId']=$item['store_id'].$item['product_id'].$item['variant_id'].$item['batch_no'].$item['category_id'].$item['subcategory_id'].$item['brand_id'].$cp.$data['supplier_id'].$id;
				
                $this->db->insert('pro_purchase_invoice_items', $item);
            }		
			
			if($this->isStore && $data['status']=="approved"){	
			$this->sync_center->sync_purchase_invoice($UniqueID);
			}
            return true;
        }
        return false;
    }

    public function updatePurchase_invoices($id, $data, $items,$po_array){
		 
        if ($this->db->update('pro_purchase_invoices', $data, array('id' => $id))) {
			$this->db->update('pro_purchase_orders', $po_array, array('id' => $data['po_number']));
            $purchase_invoices_id = $id;
            foreach ($items as $item) {
               $item['invoice_id'] = $id;	
				$item['pi_uniqueId']=$item['store_id'].$item['product_id'].$item['variant_id'].$item['batch_no'].$item['category_id'].$item['subcategory_id'].$item['brand_id'].$cp.$data['supplier_id'].$id;			   
			   $this->db->insert('pro_purchase_invoice_items', $item);
            }       
			if($this->isStore && $data['status']=="approved"){	
			$this->sync_center->sync_purchase_invoice($id);
			}
            return true;
        }
        return false;
    }

    public function updateStatus($id, $status, $note){
        $items = $this->siteprocurment->getAllPurchase_invoicesItems($id);
        if ($this->db->update('pro_purchase_invoice_items', array('status' => $status, 'note' => $note), array('id' => $id))) {
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

    public function deletePurchase_invoices($id){
        $purchase = $this->getPurchase_invoicesByID($id);
        $purchase_items = $this->siteprocurment->getAllPurchase_invoicesItems($id);
        if ($this->db->delete('pro_purchase_invoice_items', array('purchase_invoices_id' => $id)) && $this->db->delete('pro_purchase_invoices', array('id' => $id))) {
         
            $this->siteprocurment->syncQuantity(NULL, NULL, $purchase_items);
            return true;
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

    public function getPurchasePayments($purchase_order_id){
        $this->db->order_by('id', 'asc');
        $q = $this->db->get_where('payments', array('purchase_order_id' => $purchase_order_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    public function getPaymentByID($id){
        $q = $this->db->get_where('payments', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getPaymentsForPurchase($purchase_order_id){
        $this->db->select('payments.date, payments.paid_by, payments.amount, payments.reference_no, users.first_name, users.last_name, type')
            ->join('users', 'users.id=payments.created_by', 'left');
        $q = $this->db->get_where('payments', array('purchase_order_id' => $purchase_order_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function addPayment($data = array()){
        if ($this->db->insert('payments', $data)) {
            if ($this->siteprocurment->getReference('ppay') == $data['reference_no']) {
                $this->siteprocurment->updateReference('ppay');
            }
            $this->siteprocurment->syncPurchasePayments($data['purchase_order_id']);
            return true;
        }
        return false;
    }

    public function updatePayment($id, $data = array()){
        if ($this->db->update('payments', $data, array('id' => $id))) {
            $this->siteprocurment->syncPurchasePayments($data['purchase_order_id']);
            return true;
        }
        return false;
    }

    public function deletePayment($id){
        $opay = $this->getPaymentByID($id);
        if ($this->db->delete('payments', array('id' => $id))) {
            $this->siteprocurment->syncPurchasePayments($opay->purchase_order_id);
            return true;
        }
        return FALSE;
    }

    public function getProductOptions($product_id){
        $q = $this->db->get_where('product_variants', array('product_id' => $product_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getProductVariantByName($name, $product_id){
        $q = $this->db->get_where('product_variants', array('name' => $name, 'product_id' => $product_id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getExpenseByID($id){
        $q = $this->db->get_where('expenses', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function addExpense($data = array()){
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
        $q = $this->db->get_where('return_purchase_invoices', array('id' => $id), 1);
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

    public function getPurcahseItemByID($id){
        $q = $this->db->get_where('pro_purchase_items', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function returnPurchase($data = array(), $items = array()){
        $purchase_items = $this->siteprocurment->getAllPurchase_invoicesItems($data['purchase_invoices_id']);
        if ($this->db->insert('return_purchase_invoices', $data)) {
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
            $this->calculatePurchaseTotals($data['purchase_order_id'], $return_id, $data['surcharge']);
            $this->siteprocurment->syncQuantity(NULL, NULL, $purchase_items);
            $this->siteprocurment->syncQuantity(NULL, $data['purchase_order_id']);
            return true;
        }
        return false;
    }

    public function calculatePurchaseTotals($id, $return_id, $surcharge){
        $purchase = $this->getPurchase_invoicesByID($id);
        $items = $this->getAllPurchase_invoicesItems($id);
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

            if ($this->db->update('pro_purchase_invoices', $data, array('id' => $id))) {
                return true;
            }
        } else {
            $this->db->delete('pro_purchase_invoices', array('id' => $id));
        }
        return FALSE;
    }

    public function getExpenseCategories(){
        $q = $this->db->get('expense_categories');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getExpenseCategoryByID($id){
        $q = $this->db->get_where("expense_categories", array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function updateAVCO($data){
        if ($wp_details = $this->getWarehouseProductQuantity($data['warehouse_id'], $data['product_id'])) {
            $total_cost = (($wp_details->quantity * $wp_details->avg_cost) + ($data['quantity'] * $data['cost']));
            $total_quantity = $wp_details->quantity + $data['quantity'];
            if (!empty($total_quantity)) {
                $avg_cost = ($total_cost / $total_quantity);
                $this->db->update('warehouses_products', array('avg_cost' => $avg_cost), array('product_id' => $data['product_id'], 'warehouse_id' => $data['warehouse_id']));
            }
        } else {
            $this->db->insert('warehouses_products', array('product_id' => $data['product_id'], 'warehouse_id' => $data['warehouse_id'], 'avg_cost' => $data['cost'], 'quantity' => 0));
        }
    }
    
    function isInvoiceExist($invoice_no,$supplier_id,$edit_id){
	$this->db->select();
	$this->db->from('pro_purchase_invoices');
	$this->db->where('invoice_no',$invoice_no);
	$this->db->where('supplier_id',$supplier_id);
	if($edit_id){
	   $this->db->where('id !=',$edit_id);
	}
	$q = $this->db->get();
	return $q->num_rows();
    }
    function stock_master_update($stock_update){
        $date =date('Y-m-d h:m:s');
		$store_id 	= $stock_update['store_id'];
        $product_id = $stock_update['product_id'];
		$variant_id = $stock_update['variant_id'];
		$category_id = $stock_update['category_id'];
		$subcategory_id = $stock_update['subcategory_id'];
		$brand_id = $stock_update['brand_id'];
		$cm_id = $stock_update['cm_id']; //category_mapping ID
		$invoice_id = $stock_update['invoice_id'];
		$batch=$stock_update['batch'];
		$expiry=$stock_update['expiry'];
        $inv_date=$stock_update['invoice_date'];
		$qty=$stock_update['stock_in'];
		$this->db->select();
		$this->db->from('pro_stock_master');
		$this->db->where("unique_id",$stock_update['unique_id']);
		$q = $this->db->get();
		if($q->num_rows()>0){
			$id = $q->row('id');
			$this->db->where('id',$id);
			$this->db->update('pro_stock_master',$stock_update);

             $ledger_query ='insert into srampos_pro_stock_ledger(stock_id,store_id, product_id,variant_id, cm_id, category_id, subcategory_id, brand_id, transaction_identify,transaction_type,transaction_qty,date)values('.$id.','.$store_id.','.$product_id.','.$variant_id.', '.$cm_id.', '.$category_id.', '.$subcategory_id.', '.$brand_id.', "Invoice","I",'.$qty.',"'.$date.'")';
                       $this->db->query($ledger_query); 
                      //  print_r($this->db->error());die;
		}else{
			$this->db->insert('pro_stock_master',$stock_update);
           // print_r($this->db->last_query());
			$return_id = $this->db->insert_id();
            
            $ledger_query ='insert into srampos_pro_stock_ledger(stock_id,store_id, product_id,variant_id, cm_id, category_id, subcategory_id, brand_id, transaction_identify,transaction_type,transaction_qty,date)values('.$return_id.','.$store_id.','.$product_id.','.$variant_id.', '.$cm_id.', '.$category_id.', '.$subcategory_id.', '.$brand_id.', "Invoice","I",'.$qty.',"'.$date.'")';
            $this->db->query($ledger_query); 
		}
        //print_r($this->db->error());die;
    }
    
    public function getPurchase_ordersByID($id){
        $q = $this->db->get_where('pro_purchase_orders', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    public function getAllPurchase_ordersItems($purchase_orders_id){
        $this->db->select('pro_purchase_order_items.*,u.name as Bunit_name,us.name as stockUnitname')
            ->join('recipe', 'recipe.id=pro_purchase_order_items.product_id', 'left')
            ->join('recipe_variants', 'recipe_variants.id=pro_purchase_order_items.option_id', 'left')
            ->join('tax_rates', 'tax_rates.id=pro_purchase_order_items.item_tax_method', 'left')
            ->join('units u','u.id=recipe.unit','left')
			->join('units us','us.id=recipe.purchase_unit','left')
            ->group_by('pro_purchase_order_items.id')
            ->order_by('id', 'asc');
        $q = $this->db->get_where('pro_purchase_order_items', array('purchase_order_id' => $purchase_orders_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
     public function getstoreRequestByID($id){
        $q = $this->db->get_where('pro_store_indent_receive', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

}
