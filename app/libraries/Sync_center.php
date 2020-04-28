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
	
}