<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Indent_process_model extends CI_Model{
    public function __construct()
    {
        parent::__construct();
    }
    public function getProductNames($term, $limit = 10){
        $this->db->select('products.*');
        $this->db->from('products');
        $this->db->join('warehouses_products FWP', 'FWP.product_id=products.id and warehouse_id='.$this->store_id);
        $this->db->where("(products.name LIKE '" . $term . "%' OR products.code LIKE '" . $term . "%' OR products.barcode LIKE '" . $term . "%' OR  concat({$this->db->dbprefix('products')}.name, ' (', {$this->db->dbprefix('products')}.code, ')') LIKE '" . $term . "%')");
        $this->db->limit($limit);
        $q = $this->db->get();        
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }
    public function getItemByID($id){
        $q = $this->db->get_where('pro_store_request_items', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    public function getStore_requestByID($id){
        $q = $this->db->get_where('pro_store_request', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getAllStore_requestItems($store_request_id){
        $this->db->select('pro_store_request_items.*')
            ->group_by('pro_store_request_items.id')
            ->order_by('id', 'asc');
        $q = $this->db->get_where('pro_store_request_items', array('store_request_id' => $store_request_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function addStock_request($store_req_items,$indent_reference){
			$table_name = 'pro_stock_request';
			$table_items = 'pro_stock_request_items';
			foreach($store_req_items as $k => $row){
            $data = $row['data'];
            $items = $row['products'];
            $n = $this->siteprocurment->lastidStockRequest();
            $data['reference_no'] = 'SR'.str_pad($n + 1, 5, 0, STR_PAD_LEFT);
            $this->db->insert($table_name, $data);
            $request_id = $this->db->insert_id();p($this->db->error());
            if ($request_id) {         
                $unique_id = $this->site->generateUniqueTableID($request_id);
                if ($request_id) {
                    $this->site->updateUniqueTableId($request_id,$unique_id,$table_name);
                }
                
                foreach ($items as $item) {
                    $item['stock_request_id'] = $unique_id;
                    $this->db->insert($table_items, $item);
                    $i_request_id = $this->db->insert_id();p($this->db->error());
                    $i_unique_id = $this->site->generateUniqueTableID($i_request_id);
                    if ($i_request_id) {
                        $this->site->updateUniqueTableId($i_request_id,$i_unique_id,$table_items);
                    }
                }
                if($data['status']=="approved"){
                    //$this->sync_store->sync_stockRequests($id);
                }
            }
        }
        $u_data['is_processed'] = 1;
        $this->db->where('id',$indent_reference);
        $this->db->update('pro_store_indent_receive',$u_data);
        return true;
        return false;
    }


    public function updateStore_request($id, $data, $items = array())
    {        
        if ($this->db->update('pro_store_request', $data, array('id' => $id)) && $this->db->delete('pro_store_request_items', array('store_request_id' => $id))) {
            foreach ($items as $item) {
                $item['store_request_id'] = $id;
                $this->db->insert('pro_store_request_items', $item);
                $i_request_id = $this->db->insert_id();//p($this->db->error());
                $i_unique_id = $this->site->generateUniqueTableID($i_request_id);
                if ($i_request_id) {
                    $this->site->updateUniqueTableId($i_request_id,$i_unique_id,'pro_store_request_items');
                }
            }
            if($data['status']=="approved"){
		//$this->sync_store->sync_stockRequests($id);
	    }
            return true;
        }        
        return false;
    }

 


    public function deleteStore_request($id)
    {
        if ($this->db->delete('pro_quote_items', array('store_request_id' => $id)) && $this->db->delete('pro_store_request', array('id' => $id))) {
            return true;
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

    function getStoreIndentRequests($store_id){
        $this->db->select('id,reference_no');
        $this->db->from('pro_store_indent_receive');
        $this->db->where('is_completed',0);
        $this->db->where('is_processed',0);
        $this->db->where('from_store_id',$store_id);
        $q = $this->db->get();
        if($q->num_rows()>0){
            return $q->result();
        }
        return false;
    }
    function getIndentRequestsData($store_id,$indent_id){
        $this->db->select('id,reference_no,date');
        $this->db->from('pro_store_indent_receive');
        $this->db->where('is_completed',0);
        $this->db->where('is_processed',0);
        $this->db->where('from_store_id',$store_id);
        $this->db->where('id',$indent_id);
        $q = $this->db->get();$data= array();
        if($q->num_rows()>0){
            $data = $q->row();
            $this->db->from('pro_store_indent_receive_items');
            $this->db->where('store_request_id',$indent_id);
            $p = $this->db->get();
            $data->items = $p->result();
            return $data;
        }
        return false;
    }
    function LoadStock($product_ids,$store_ids){
        $this->db->select('pro_stock_master.product_id,pro_stock_master.store_id,SUM(stock_in) as available_stock,warehouses.name as store_name');
        $this->db->from('pro_stock_master');
        $this->db->join('warehouses','warehouses.id=pro_stock_master.store_id');
        $this->db->where_in('pro_stock_master.product_id',$product_ids);        
        $this->db->where_in('pro_stock_master.store_id',$store_ids);
        $this->db->group_by('pro_stock_master.product_id,store_id');
   
        //echo $this->db->get_compiled_select();
        $q = $this->db->get();
        if($q->num_rows()>0){
            $data = $q->result();
            $stock = array();$all_available_stock = array();
            $cnt = 0;
            foreach($data as $k => $row){
                if($row->min_qty==null){$row->min_qty=0;}
                $stock[$this->store_id.$row->product_id]['stores'][$cnt]['store_id'] = $row->store_id;
                $stock[$this->store_id.$row->product_id]['stores'][$cnt]['store_name'] = $row->store_name;
                $stock[$this->store_id.$row->product_id]['stores'][$cnt]['available_stock'] = $row->available_stock - $row->min_qty;
                $cnt++;
            }
            return $stock;
        }
        return false;
    }
    public function getStock_requestByID($id)
    {
        $q = $this->db->get_where('pro_stock_request', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getAllStock_requestItems($stock_request_id)
    {
        $this->db->select('pro_stock_request_items.*')
           
            ->group_by('pro_stock_request_items.id')
            ->order_by('id', 'asc');
        $q = $this->db->get_where('pro_stock_request_items', array('stock_request_id' => $stock_request_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
   

}
