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
		    unset($insert_data->s_no);
		    $this->CI->centerdb->insert($r_table_name,$insert_data);
		    $id = $insert_data['id'];
		    $item_details = $this->getSR_Items_id($id);
		    $this->CI->centerdb->insert_batch($r_table_items,$item_details);
		}
	    }
	   
	}
    }
	
	
	
}