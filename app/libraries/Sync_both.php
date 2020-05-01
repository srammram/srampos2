<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Sync_both 
{
    public function __construct() {
       $this->CI =& get_instance();
	
    }
    
    
        
    function compare_server_local($DB1,$DB2,$table,$access,$update_db){
	$result = array();$localkeys = array();
	$toDB =  array();
	$fromDB = array();
	//*************** change Auto increment ID as array Key ****************//
	    foreach($DB1 as $key => $val) {
		$k = $val['id'];
		$fromDB[$k] = $val; 
	    }
	    foreach($DB2 as $key => $val) {
		$k = $val['id'];
		$toDB[$k] = $val; 
	    }
	//*************** Compare ServerDB and LocalDB  - For update and Insert ****************//
	foreach($fromDB as $key => $val) { 
	    $key = $val['id'];
	    if(isset($toDB[$key])){
		if($access['update']){
		    if(is_array($val)  && is_array($toDB[$key])){
			
			$array_diff = array_diff($val, $toDB[$key]);
			if(!empty($array_diff)){
			    if(isset($val['s_no'])){unset($val['s_no']);}
			    $result['update'][$key] = $val;
			}
		    }
		}
	    }else {
		if($access['insert']){
		    if(isset($val['s_no'])){unset($val['s_no']);}
		    $result['insert'][$key] = $val;
		}
	    }
	    $localkeys[$key] = true;	
	    //// check for delete	
	}
	//*************** Compare ServerDB and LocalDB  - For Delete ****************//
	if($access['delete']){
	    $l = array_diff_key($toDB,$localkeys); 
	    foreach($l as $k => $lk){ 
		$result['delete'][] = $k;
	    }
	}
	//*************** sync table ************//
	$store_id = $this->CI->store_id;
	$this->sync_table($result,$table,$update_db);
	//echo $update_db;
	//p($result);
	
	return true;
    }
    function sync_table($data,$table,$update_db){
	if(isset($data['update']) && !empty($data['update'])){
	    $updateData = $data['update'];
	    $this->update_table($updateData,$table,$update_db);
	}
	if(isset($data['insert']) && !empty($data['insert'])){
	    $insertData = $data['insert'];
	    $this->insert_table($insertData,$table,$update_db);
	}
	if(isset($data['delete']) && !empty($data['delete'])){
	    $deleteIDS = $data['delete'];
	    $this->delete_table($deleteIDS,$table,$update_db);
	}
    }
    function update_table($data,$table,$update_db){
	$this->CI->$update_db->update_batch($table,$data,'id');
    }
    function insert_table($data,$table,$update_db){
	$this->CI->$update_db->insert_batch($table,$data);
    }
    function delete_table($deleteIds,$table,$update_db){
	$this->CI->$update_db->where_in('id',$deleteIds);
	$this->CI->$update_db->delete($table);
    }

	 function sync_customers(){
		$table = 'companies';
		$access['store']['insert'] = true;
		$access['store']['update'] = false;
		$access['store']['delete'] = false;
	
		$access['center']['insert'] = true;
		$access['center']['update'] = true;
		$access['center']['delete'] = true;
	
		$table_name = 'companies';
		$this->CI->centerdb->from($table_name);
		$this->CI->centerdb->where('group_id',3);
		$center = $this->CI->centerdb->get()->result_array();
	
		$this->CI->db->from($table_name);
		$this->CI->db->where('group_id',3);
		$store = $this->CI->db->get()->result_array();
	
		$update_db = 'centerdb';
		$a = $this->compare_server_local($store,$center,$table_name,$access['store'],$update_db);
		if($a){
	    $update_db = 'db';
	    $this->CI->centerdb->from($table_name);
	    $this->CI->centerdb->where('group_id',3);
	    $center = $this->CI->centerdb->get()->result_array();
	    $a = $this->compare_server_local($center,$store,$table_name,$access['center'],$update_db);
	}
    }
	
    function sync_tills(){
		$table = 'tills';
		$access['store']['insert'] = true;
		$access['store']['update'] = false;
		$access['store']['delete'] = false;
	
		$access['center']['insert'] = false;
		$access['center']['update'] = true;
		$access['center']['delete'] = true;
		$this->sync_tables($table,$access);
    }
	 function sync_printers(){
		$table = 'printers';
		$access['store']['insert'] = true;
		$access['store']['update'] = false;
		$access['store']['delete'] = false;
	
		$access['center']['insert'] = false;
		$access['center']['update'] = true;
		$access['center']['delete'] = true;
		$this->sync_tables($table,$access);
    }
	
	
	
	
    function sync_giftvoucher_status(){
		$table = 'giftvouchers_status';
		$access['store']['insert'] = false;
		$access['store']['update'] = true;
		$access['store']['delete'] = false;
	
		$access['center']['insert'] = true;
		$access['center']['update'] = false;
		$access['center']['delete'] = false;
		$this->sync_tables($table,$access);
    }
	
	
	
	
	
	
	
    function sync_tendertype_status(){
	$table = 'tender_type_status';
	$access['store']['insert'] = false;
	$access['store']['update'] = true;
	$access['store']['delete'] = false;
	
	$access['center']['insert'] = true;
	$access['center']['update'] = true;
	$access['center']['delete'] = true;
	$this->sync_tables($table,$access);
    }
   
    function sync_loyaltycard_status(){
	$table = 'loyaltycard_status';
	$access['store']['insert'] = false;
	$access['store']['update'] = true;
	$access['store']['delete'] = false;
	
	$access['center']['insert'] = true;
	$access['center']['update'] = false;
	$access['center']['delete'] = false;
	$this->sync_tables($table,$access);
    }
    function sync_loyalty_customer(){
	$table = 'loyalty_customer';
	$access['store']['insert'] = true;
	$access['store']['update'] = false;
	$access['store']['delete'] = false;
	
	$access['center']['insert'] = true;
	$access['center']['update'] = true;
	$access['center']['delete'] = false;
	$this->sync_tables($table,$access);
    }
    function sync_loyalty_customer_data(){
	$table = 'loyalty_customer_data';
	$access['store']['insert'] = true;
	$access['store']['update'] = false;
	$access['store']['delete'] = false;
	
	$access['center']['insert'] = true;
	$access['center']['update'] = false;
	$access['center']['delete'] = false;
	$this->sync_tables($table,$access);
    }
    function sync_stock(){
	$table = 'pro_stock_master';
	$access['store']['delete'] = false;
	$access['store']['insert'] = false;
	$access['store']['update'] = true;
	
	
	$access['center']['insert'] = true;
	$access['center']['update'] = false;
	$access['center']['delete'] = false;
	$this->sync_tables($table,$access);
    }

    
    
    function sync_store_receivers(){
	/** tables
	 * pro_store_receiver
	 * pro_store_receiver_items
	 * pro_store_receiver_item_details
	 * */
	$table = 'pro_store_receivers';
	$table_items = 'pro_store_receiver_items';
	$table_item_details = 'pro_store_receiver_item_details';
	$access['store']['insert'] = false;
	$access['store']['update'] = true;
	$access['store']['delete'] = false;
	
	$access['center']['insert'] = false;
	$access['center']['update'] = false;
	$access['center']['delete'] = false;
	if($this->CI->centerdb_connected){
	    $this->sync_tables($table,$access);
	    $this->sync_tables($table_items,$access);
	    $this->sync_tables($table_item_details,$access);
	}
    }
    
    
    
    
    function sync_tables($table_name,$access){
	$center = $this->CI->centerdb->get_where($table_name,array('store_id'=>$this->CI->store_id))->result_array();	
	$store = $this->CI->db->get_where($table_name,array('store_id'=>$this->CI->store_id))->result_array();
	$update_db = 'centerdb';
	$a = $this->compare_server_local($store,$center,$table_name,$access['store'],$update_db);
	if($a){
	 $update_db = 'db';
	 $center = $this->CI->centerdb->get_where($table_name,array('store_id'=>$this->CI->store_id))->result_array();	
	 $a = $this->compare_server_local($center,$store,$table_name,$access['center'],$update_db);   
	}
	//echo $table_name.'-start-<br>';echo '<pre>';print_R($a);
    }
   

/////////////// FROM CENTER - END /////////////////////  
}