<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Sync_center{
    public function __construct() {
        $this->CI =& get_instance();	
    }
 
    function sync_storeIndentRequests($id=false){
	/** tables
	 * pro_store_request
	 * pro_store_request_items
	 * */
	if($this->CI->centerdb_connected){
	    $i_table_name = 'pro_store_request';$r_table_name = 'pro_store_indent_receive';
	    $i_table_items = 'pro_store_request_items';$r_table_items = 'pro_store_indent_receive_items';
	    /* sync store indent */
	    if($id){
			
		$db1 = $this->CI->db->get_where($i_table_name,array('store_id'=>$this->CI->store_id,'id'=>$id,'status'=>'approved'))->result_array();
		$db2 = $this->CI->centerdb->get_where($i_table_name,array('store_id'=>$this->CI->store_id,'id'=>$id))->result_array();	
		
	    }else{
		$db1 = $this->CI->db->get_where($i_table_name,array('store_id'=>$this->CI->store_id,'status'=>'approved'))->result_array();
		$db2 = $this->CI->centerdb->get_where($i_table_name,array('store_id'=>$this->CI->store_id))->result_array();	
	    }
	    $data = $this->compare_server_local($db1,$db2);
	
	    if(isset($data['insert']) && !empty($data['insert'])){
		foreach($data['insert'] as $k => $insert_data){
		    unset($insert_data->s_no);
		    $this->CI->centerdb->insert($i_table_name,$insert_data);
		    $id = $insert_data['id'];
		    $item_details = $this->getSR_Items_id($id);
		    $this->CI->centerdb->insert_batch($i_table_items,$item_details);
		}
	    }
	    /* sync store indent receiver */
	    if($id){
		$db1 = $this->CI->db->get_where($i_table_name,array('store_id'=>$this->CI->store_id,'id'=>$id,'status'=>'approved'))->result_array();
		
		$db2 = $this->CI->centerdb->get_where($r_table_name,array('store_id'=>$this->CI->store_id,'id'=>$id))->result_array();	
		
	    }else{
		$db1 = $this->CI->db->get_where($i_table_name,array('store_id'=>$this->CI->store_id,'status'=>'approved'))->result_array();
		$db2 = $this->CI->centerdb->get_where($r_table_name,array('store_id'=>$this->CI->store_id))->result_array();	
	    }	
	    $data = $this->compare_server_local($db1,$db2);
	    if(isset($data['insert']) && !empty($data['insert'])){
		foreach($data['insert'] as $k => $insert_data){
		    unset($insert_data->s_no,$insert_data['sync_status'],$insert_data['created_on']);
		    $this->CI->centerdb->insert($r_table_name,$insert_data);
		/* 	print_r($this->CI->centerdb->error());
			die; */
		    $id = $insert_data['id'];
		    $item_details = $this->getSR_Items_id($id);
		    $this->CI->centerdb->insert_batch($r_table_items,$item_details);
		}
	    }
	   
	}
    }
	
 function sync_purchase_invoice($id=false){
	/** tables
	 * pro_purchase_invoices
	 * pro_purchase_invoice_items
	 * */
	if($this->CI->centerdb_connected){
	    $i_table_name = 'pro_purchase_invoices';
	    $i_table_items = 'pro_purchase_invoice_items';
	    /* sync store to center purchase invoice */
		$db1 = $this->CI->db->get_where($i_table_name,array('store_id'=>$this->CI->store_id,'id'=>$id,'status'=>'approved'))->result_array();
		$db2 = $this->CI->centerdb->get_where($i_table_name,array('store_id'=>$this->CI->store_id,'id'=>$id))->result_array();	
	    $data = $this->compare_server_local($db1,$db2);
	    if(isset($data['insert']) && !empty($data['insert'])){
		foreach($data['insert'] as $k => $insert_data){
		    unset($insert_data->s_no);
		    $this->CI->centerdb->insert($i_table_name,$insert_data);
		    $id = $insert_data['id'];
		    $item_details = $this->getPI_Items($id);
		    $this->CI->centerdb->insert_batch($i_table_items,$item_details);
		}
	    }
	  
	}
    }
	
	
	 function sync_grn($id=false){
	/** tables
	 * pro_grn
	 * pro_grn_items
	 * */
	if($this->CI->centerdb_connected){
	    $i_table_name = 'pro_grn';
	    $i_table_items = 'pro_grn_items';
	    /* sync store to center purchase invoice */
		$db1 = $this->CI->db->get_where($i_table_name,array('store_id'=>$this->CI->store_id,'id'=>$id,'status'=>'approved'))->result_array();
		$db2 = $this->CI->centerdb->get_where($i_table_name,array('store_id'=>$this->CI->store_id,'id'=>$id))->result_array();	
	    $data = $this->compare_server_local($db1,$db2);
	    if(isset($data['insert']) && !empty($data['insert'])){
		foreach($data['insert'] as $k => $insert_data){
		    unset($insert_data->s_no);
		    $this->CI->centerdb->insert($i_table_name,$insert_data);
		    $id = $insert_data['id'];
		    $item_details = $this->getGRN_Items($id);
		    $this->CI->centerdb->insert_batch($i_table_items,$item_details);
		}
	    }
	  
	}
    }
	
	
	function compare_server_local($DB1,$DB2,$table=false){
		$result = array();$localkeys = array();
		$localDB =  array();
	    $serverDB = array();
	//*************** change Auto increment ID as array Key ****************//
	    foreach($DB1 as $key => $val) {
		$k = $val['id'];
		$localDB[$k] = $val; 
	    }
	    foreach($DB2 as $key => $val) {
		$k = $val['id'];
		$serverDB[$k] = $val; 
	    }
	//*************** Compare ServerDB and LocalDB  - For update and Insert ****************//
	foreach($localDB as $key => $val) { 
	    $key = $val['id'];
	    if(isset($serverDB[$key])){
		if(is_array($val)  && is_array($serverDB[$key])){		
		    $array_diff = array_diff($val, $serverDB[$key]);
		    if(!empty($array_diff)){
			if(isset($val['s_no'])){unset($val['s_no']);}
			$result['update'][$key] = $val;
		    }
		}
	    }else{
		if(isset($val['s_no'])){unset($val['s_no']);}
		$result['insert'][$key] = $val;
	    }
	    $localkeys[$key] = true;	
	    //// check for delete	
	}
	//*************** Compare ServerDB and LocalDB  - For Delete ****************//
	$l = array_diff_key($serverDB,$localkeys); 
	foreach($l as $k => $lk){ 
	    $result['delete'][] = $k;
	}
	//*************** sync table ************//
	$store_id = $this->CI->store_id;
	if($table){
	   $this->sync_table($result,$table); 
	}
	return $result;
    }
	function sync_table($data,$table){
	if(isset($data['update']) && !empty($data['update'])){
	    $updateData = $data['update'];
	    $this->update_table($updateData,$table);
	}
	if(isset($data['insert']) && !empty($data['insert'])){
	    $insertData = $data['insert'];
	    $this->insert_table($insertData,$table);
	}
	if(isset($data['delete']) && !empty($data['delete'])){
	    $deleteIDS = $data['delete'];
	    $this->delete_table($deleteIDS,$table);
	}
    }
   function getSR_Items_id($id){
		$q = $this->CI->db->get_where('pro_store_request_items',array('store_request_id'=>$id));
		$data =  array();
		foreach($q->result() as $k => $row){
			unset($row->s_no);
			$data[$k] = $row;
		}
		return $data;
    }
	
	function getPI_Items($id){
		$q = $this->CI->db->get_where('pro_purchase_invoice_items',array('invoice_id'=>$id));
		$data =  array();
		foreach($q->result() as $k => $row){
			unset($row->s_no);
			$data[$k] = $row;
		}
		return $data;
    }
	
	function getGRN_Items($id){
		$q = $this->CI->db->get_where('pro_grn_items',array('grn_id'=>$id));
		$data =  array();
		foreach($q->result() as $k => $row){
			unset($row->s_no);
			$data[$k] = $row;
		}
		return $data;
    }
  function sync_stock_auto($unique_id){
	$table_name = 'pro_stock_master';
	$this->CI->db->select("*");
	$this->CI->db->where("store_id",$this->CI->store_id);
  	$this->CI->db->where("unique_id",$unique_id);
	$q=$this->CI->db->get($table_name);
	$db1 =$q->result_array();
	$this->CI->centerdb->select("*");
	$this->CI->centerdb->where("store_id",$this->CI->store_id);
 	$this->CI->centerdb->where("unique_id",$unique_id);
	$q1=$this->CI->centerdb->get($table_name);
	$db2 =$q1->result_array();	
	$data = $this->compare_server_local_stock($db1,$db2,$table_name);
	return true;
	}
	function compare_server_local_stock($DB1,$DB2,$table=false){
	$result = array();$localkeys = array();
	$localDB =  array();
	$serverDB = array();
	//*************** change Auto increment ID as array Key ****************//
	    foreach($DB1 as $key => $val) {
		$k = $val['id'];
		$localDB[$k] = $val; 
	    }
	    foreach($DB2 as $key => $val) {
		$k = $val['id'];
		$serverDB[$k] = $val; 
	    }
		
	//*************** Compare ServerDB and LocalDB  - For update and Insert ****************//
	foreach($localDB as $key => $val) { 
	    $key = $val['id'];
	    if(isset($serverDB[$key])){
		if(is_array($val)  && is_array($serverDB[$key])){		
		    $array_diff = array_diff($val, $serverDB[$key]);
		    if(!empty($array_diff)){
			if(isset($val['s_no'])){unset($val['s_no']);}
			$result['update'][$key] = $val;
		    }
		  }
		
	    }else{
		if(isset($val['s_no'])){unset($val['s_no']);}
		$result['insert'][$key] = $val;
	    }
	    $localkeys[$key] = true;	
	    //// check for delete	
	}
	//*************** Compare ServerDB and LocalDB  - For Delete ****************//
	$l = array_diff_key($serverDB,$localkeys); 
	foreach($l as $k => $lk){ 
	    $result['delete'][] = $k;
	}
	//*************** sync table ************//
	$store_id = $this->CI->store_id;
	if($table){
	   $this->sync_table($result,$table); 
	}
	return $result;
    }
	function sync_stock(){
	$table_name = 'pro_stock_master';
	$this->CI->db->select("*");
	$this->CI->db->where("store_id",$this->CI->store_id);
//	$this->CI->db->where("store_id",2);
	$q=$this->CI->db->get($table_name);
	$db1 =$q->result_array();
	
	$this->CI->centerdb->select("*");
	$this->CI->centerdb->where("store_id",$this->CI->store_id);
//	$this->CI->centerdb->where("store_id",2);
	$q1=$this->CI->centerdb->get($table_name);
	$db2 =$q1->result_array();	
	$data = $this->compare_server_local_stock($db1,$db2,$table_name);
	return true;
	}
	
	
   function update_table($data,$table){
	   $this->CI->centerdb->update_batch($table,$data,'id');
   }
   
    function insert_table($data,$table){
	    $this->CI->centerdb->insert_batch($table,$data);
    }
	
    function delete_table($deleteIds,$table){
	  $this->CI->centerdb->where_in('id',$deleteIds);
	  $this->CI->centerdb->delete($table);
    }
	
	
    function sync_store_transfers($id=false){
	/** tables
	 * pro_store_transfers
	 * pro_store_transfer_items
	 * pro_store_transfer_item_details
	 * */
	if($this->CI->centerdb_connected){
	    $table_name = 'pro_store_transfers';
	    $table_items = 'pro_store_transfer_items';
	    $table_item_details ='pro_store_transfer_item_details';
	    if($id){
		$db1 = $this->CI->db->get_where($table_name,array('store_id'=>$this->CI->store_id,'id'=>$id,'status'=>'approved'))->result_array();
		$db2 = $this->CI->centerdb->get_where($table_name,array('store_id'=>$this->CI->store_id,'id'=>$id))->result_array();	
	    }else{
		$db1 = $this->CI->db->get_where($table_name,array('store_id'=>$this->CI->store_id,'status'=>'approved'))->result_array();
		$db2 = $this->CI->centerdb->get_where($table_name,array('store_id'=>$this->CI->store_id))->result_array();	
	    }
	    	
	    $data = $this->compare_server_local($db1,$db2);
	    
	    if(isset($data['insert']) && !empty($data['insert'])){
		foreach($data['insert'] as $k => $insert_data){
		    unset($insert_data->s_no);
		    $this->CI->centerdb->insert($table_name,$insert_data);
		    $id = $insert_data['id'];
		    $where = array('store_transfer_id'=>$id,'store_id'=>$this->CI->store_id);		
		    $items = $this->sync_tables($table_items,$where);
		    
		    $where = array('store_transfer_id'=>$id,'store_id'=>$this->CI->store_id);		
		    $item_details = $this->sync_tables($table_item_details,$where);
		    
		    $this->sync_store_receivers($insert_data);
		}
		
	    }
	
	}
    }
	
	
}