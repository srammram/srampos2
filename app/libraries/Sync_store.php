<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Sync_store {
	
    public function __construct() {
       $this->CI =& get_instance();		
    }
    /////////////// FROM CENTER //////////////////////
    
     function compare_server_local($DB1,$DB2,$table=false){
	$result = array();$localkeys = array();
	$localDB =  array();
	$serverDB = array();
	//*************** change Auto increment ID as array Key ****************//
	    foreach($DB1 as $key => $val) {
		$k = $val['id'];
		$serverDB[$k] = $val; 
	    }
	    foreach($DB2 as $key => $val) {
		$k = $val['id'];
		$localDB[$k] = $val; 
	    }
	//*************** Compare ServerDB and LocalDB  - For update and Insert ****************//
	foreach($serverDB as $key => $val) { 
	    $key = $val['id'];
	    if(isset($localDB[$key])){
		if(is_array($val)  && is_array($localDB[$key])){		
		    //$array_diff = array_diff($val, $localDB[$key]);
		    //$array_diff = array_merge(array_diff($val, $localDB[$key]), array_diff($localDB[$key],$val));
		    $array_diff = array_merge(array_diff_assoc($val, $localDB[$key]), array_diff_assoc($localDB[$key],$val));
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
	$l = array_diff_key($localDB,$localkeys); 
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
    function update_table($data,$table){
	if($table=="warehouses"){
	    foreach($data as $k => $row){
		unset($row['this_store']);
		$data[$k] = $row;
	    }
	}
	$this->CI->db->update_batch($table,$data,'id');
    }
    function insert_table($data,$table){
	if($table=="currency_ex_rates"){
	    foreach($data as $k => $row){
		unset($row['id']);
		$data[$k] = $row;
	    }
	}
	$this->CI->db->insert_batch($table,$data);//p($this->db->error());
    }
    function delete_table($deleteIds,$table){
	$this->CI->db->where_in('id',$deleteIds);
	$this->CI->db->delete($table);
    }
    function sync_warehouse_recipe(){
	$field_name = 'warehouse_id';
	$table = 'warehouses_recipe';
	$this->sync_tables_store_id($table,$field_name);
	return true;
    }
	 function sync_categories(){
	$table = 'categories';
	$this->sync_tables($table);
    }
	function sync_category_mapping(){
	$table = 'category_mapping';
	$this->sync_tables($table);
    }
	 function sync_units(){
	$table = 'units';
	$this->sync_tables($table);
    }
    function sync_brands(){
	$table = 'brands';
	$this->sync_tables($table);
    }
	 function sync_variants(){
	$table = 'variants';
	$this->sync_tables($table);
    }
	
    function sync_recipes(){
	$table_name = 'recipe';
	$this->CI->centerdb->select('r.*');
	$this->CI->centerdb->from('recipe r');
	$this->CI->centerdb->join('warehouses_recipe w','w.recipe_id=r.id');
	$this->CI->centerdb->where('warehouse_id',$this->CI->store_id);
	//echo $this->CI->centerdb->get_compiled_select();
	$db1 = $this->CI->centerdb->get()->result_array();
    
	$this->CI->db->select('r.*');
	$this->CI->db->from('recipe r');
	$this->CI->db->join('warehouses_recipe w','w.recipe_id=r.id');
	$this->CI->db->where('warehouse_id',$this->CI->store_id);
	$db2 = $this->CI->db->get()->result_array();
	$a = $this->compare_server_local($db1,$db2,$table_name);
    }
	 function sync_recipe_product(){
    	$table = 'recipe_products';
	    $this->sync_tables($table);
    }
	
	function sync_recipe_combo_items(){
		$table = 'recipe_combo_items';
	    $this->sync_tables($table);
	}
	function sync_recipe_photos(){
		$table = 'recipe_photos';
	    $this->sync_tables($table);
	}
    function sync_recipe_variant_values(){
		$table = 'recipe_variants_values';
	    $this->sync_tables($table);
	}
    function sync_recipe_variants(){
		$table = 'recipe_variants';
	    $this->sync_tables($table);
	}
	
	function sync_restaurant_kitchens(){
	$table_name = 'restaurant_kitchens';
	$this->CI->centerdb->select('r.*');
	$this->CI->centerdb->from('restaurant_kitchens r');
	$this->CI->centerdb->where('warehouse_id',$this->CI->store_id);
	$db1 = $this->CI->centerdb->get()->result_array();
    
	$this->CI->db->select('r.*');
	$this->CI->db->from('restaurant_kitchens r');
	$this->CI->db->where('warehouse_id',$this->CI->store_id);
	$db2 = $this->CI->db->get()->result_array();
	$a = $this->compare_server_local($db1,$db2,$table_name);
    }
	
	 function sync_restaurant_tables(){
	$table_name = 'restaurant_tables';
	$this->CI->centerdb->select('r.*');
	$this->CI->centerdb->from('restaurant_tables r');
	$this->CI->centerdb->where('warehouse_id',$this->CI->store_id);
	$db1 = $this->CI->centerdb->get()->result_array();
    
	$this->CI->db->select('r.*');
	$this->CI->db->from('restaurant_tables r');
	$this->CI->db->where('warehouse_id',$this->CI->store_id);
	$db2 = $this->CI->db->get()->result_array();
	$a = $this->compare_server_local($db1,$db2,$table_name);
    }
	
	/* function sync_restaurant_kitchens(){
	$table_name = 'restaurant_areas';
	$this->CI->centerdb->select('r.*');
	$this->CI->centerdb->from('restaurant_areas r');
	$this->CI->centerdb->where('warehouse_id',$this->CI->store_id);
	$db1 = $this->CI->centerdb->get()->result_array();
    
	$this->CI->db->select('r.*');
	$this->CI->db->from('restaurant_areas r');
	$this->CI->db->where('warehouse_id',$this->CI->store_id);
	$db2 = $this->CI->db->get()->result_array();
	$a = $this->compare_server_local($db1,$db2,$table_name);
    } */
	function sync_taxrates(){
		$table = 'tax_rates';
		$this->sync_tables($table);
    }
	function sync_user_groups(){
		$table = 'groups';
		$return = $this->sync_tables($table);
		return true;
    }
	 function sync_user_store_access(){
		$table = 'user_store_access';
		$return = $this->sync_tables_store_id($table);
		return true;
    }
	function sync_users(){	
	    $table_name = 'users';
	    $this->CI->centerdb->select('u.*');
	    $this->CI->centerdb->from('users u');
	    $this->CI->centerdb->join('user_store_access ua','ua.user_id=u.id');
	    $this->CI->centerdb->where('store_id',$this->CI->store_id);
	    $db1 = $this->CI->centerdb->get()->result_array();
	    
	    $this->CI->db->select('u.*');
	    $this->CI->db->from('users u');
	    $this->CI->db->join('user_store_access ua','ua.user_id=u.id');
	    $this->CI->db->where('store_id',$this->CI->store_id);
	    $db2 = $this->CI->db->get()->result_array();
	    $a = $this->compare_server_local($db1,$db2,$table_name);
	
    }
	  function sync_vendors(){
		$table_name = 'companies';
		$this->CI->centerdb->from($table_name);
		$this->CI->centerdb->where('group_id',4);
		$db1 = $this->CI->centerdb->get()->result_array();
		$this->CI->db->from($table_name);
		$this->CI->db->where('group_id',4);
		$db2 = $this->CI->db->get()->result_array();
		$a = $this->compare_server_local($db1,$db2,$table_name);
    }
  	 function sync_customer_groups(){
		$table = 'customer_groups';
		$this->sync_tables($table);
	 }
	 function sync_expense_categories(){
	$table = 'expense_categories';
	$this->sync_tables($table);
    }
     function sync_tendertypes(){
	    $table = 'payment_methods';
  	    $this->sync_tables($table);
	    
    }
	   function sync_calendar(){
	    $table = 'calendar';$this->sync_tables($table);	
    }
	
	
	
	
	
	
	
	
	
	
	
	
    function sync_price_master(){
	$table = 'price_master';
	$result = $this->sync_tables_store_id($table);
	if(isset($result['update']) && !empty($result['update'])){
	    foreach($result['update'] as $k => $row){
		$s_price['selling_price'] = $row['price'];
		$this->CI->db->where('unique_id',$row['unique_id']);
		$this->CI->db->update('pro_stock_master',$s_price);
	    }
	}
    }
    function sync_product_alert_quantity(){
	$table = 'product_alert_quantity';$this->sync_tables_store_id($table);    
    }
 
    function sync_giftvoucher(){
	/**
	 *giftvoucher_master
	 *giftvoucher_details
	 *giftvouchers
	 **/
	$table = 'giftvoucher_master';$this->sync_tables($table);	
	$table = 'giftvoucher_details';$this->sync_tables($table);	
	$table = 'giftvouchers';$this->sync_tables($table);
    }
    function sync_currencies(){
	/**
	 *currencies
	 *currency_denominations
	 *currency_ex_rates
	 ***/
	
	$table = 'currencies';$this->sync_tables($table);
	/// compare exchange rate coloumns ////
	$tb1 = $this->CI->db->list_fields('currency_ex_rates');
	$tb2 = $this->CI->centerdb->list_fields('currency_ex_rates');
	$tb1_flip = array_flip($tb1);
	$tb2_flip = array_flip($tb2);
	$result=array_diff_key($tb2_flip,$tb1_flip);
	foreach($result as $k => $row){
	    $new_col = $k;
	    $query = "ALTER TABLE {$this->CI->db->dbprefix('currency_ex_rates')} ADD  `".$new_col."` VARCHAR(100) NULL";
	    $this->CI->db->query($query);
	}
	
	/// compare denominations coloumns ////
	$de_b1 = $this->CI->db->list_fields('currency_denominations');
	$de_b2 = $this->CI->centerdb->list_fields('currency_denominations');
	$de_b1_flip = array_flip($de_b1);
	$de_b2_flip = array_flip($de_b2);
	$result=array_diff_key($de_b2_flip,$de_b1_flip);
	foreach($result as $k => $row){
	    $new_col = $k;
	    $query = "ALTER TABLE {$this->CI->db->dbprefix('currency_denominations')} ADD  `".$new_col."` VARCHAR(100) NULL";
	    $this->CI->db->query($query);
	}
	/// compare settlement ////
	$de_b1 = $this->CI->db->list_fields('shifts_settlement');
	$de_b2 = $this->CI->centerdb->list_fields('shifts_settlement');
	$de_b1_flip = array_flip($de_b1);
	$de_b2_flip = array_flip($de_b2);
	$result=array_diff_key($de_b2_flip,$de_b1_flip);
	foreach($result as $k => $row){
	    $new_col = $k;
	    $query = "ALTER TABLE {$this->CI->db->dbprefix('shifts_settlement')} ADD  `".$new_col."` VARCHAR(100) NULL";
	    $this->CI->db->query($query);
	}
	
	$table = 'currency_denominations';$this->sync_tables($table);	
	$table = 'currency_ex_rates';$this->sync_tables($table);
	$this->CI->site->isCurrencyRatesUpdated();
    }
	
    function sync_grouppermission(){
	$table = 'permissions';
	$this->sync_tables($table);
    }
    function sync_StockRequests(){
	/** tables
	 * pro_stock_request
	 * pro_stock_request_items
	 * */
	if($this->CI->centerdb_connected){
	    $table_name = 'pro_stock_request';
	    $table_items = 'pro_stock_request_items';
	    $db1 = $this->CI->centerdb->get_where($table_name,array('to_store_id'=>$this->CI->store_id))->result_array();	
	    $db2 = $this->CI->db->get_where($table_name,array('to_store_id'=>$this->CI->store_id))->result_array();
	    $data = $this->compare_server_local($db1,$db2);
	    if(isset($data['insert']) && !empty($data['insert'])){
		foreach($data['insert'] as $k => $insert_data){
		    $this->CI->db->insert($table_name,$insert_data);
		    $id = $insert_data['id'];
		    $item_details = $this->getStockRequest_Items_id($id);
		    $this->CI->db->insert_batch($table_items,$item_details);
		 }
	    }
	   
	}
    }
   
	function sync_StockReceiver(){
	/** tables
	 * pro_store_receivers
	 * pro_store_receiver_items
	 * */
	if($this->CI->centerdb_connected){
	    $table_name = 'pro_store_receivers';
	    $table_items = 'pro_store_receiver_items';
	    $table_item_details = 'pro_store_receiver_item_details';
	    $db1 = $this->CI->centerdb->get_where($table_name,array('to_store'=>$this->CI->store_id))->result_array();	
	    $db2 = $this->CI->db->get_where($table_name,array('to_store'=>$this->CI->store_id))->result_array();
	    $data = $this->compare_server_local($db1,$db2);
	    if(isset($data['insert']) && !empty($data['insert'])){
		foreach($data['insert'] as $k => $insert_data){
		    $this->CI->db->insert($table_name,$insert_data);
		    $id = $insert_data['id'];
		    /** item */
		    $items = $this->getStockReceive_Items_id($id);		    
		    $this->CI->db->insert_batch($table_items,$items);
		    /* item details */
		    $item_details = $this->getStockReceive_Items_details_id($id);		    
		    $this->CI->db->insert_batch($table_item_details,$item_details);
		    
		}
		
	    }
	   
	}
    }
	
	
   
  
  
  
  
  
  
  
  
  
  
  
  
    function sync_stores(){
	$table = 'warehouses';
	$this->sync_tables_with_images($table);
    }
   
 
    function sync_countries(){
	$table = 'countries';
	$this->sync_tables($table);
    }
   
    function sync_notifications(){
	$table = 'notifications';
	$this->sync_tables($table);
    }
   
    function sync_sync_settings(){
	$table = 'sync_settings';
	$this->sync_tables($table);
	return true;
    }
    function sync_dateFormat(){
	$table = 'date_format';
	$this->sync_tables($table);
    }
 
    function sync_shiftmaster(){
	$table = 'shiftmaster';
	$this->sync_tables($table);
    }
    
   
   
    function sync_device_detail(){
	$table = 'device_detail';
	$this->sync_tables($table);
    }
   
    
    
    function sync_tables($table_name){
	$db1 = $this->CI->centerdb->get($table_name)->result_array();	
	$db2 = $this->CI->db->get($table_name)->result_array();	
	$a = $this->compare_server_local($db1,$db2,$table_name);
	return true;
	
    }
    function sync_tables_with_images($table_name){
	$db1 = $this->CI->centerdb->get($table_name)->result_array();	
	$db2 = $this->CI->db->get($table_name)->result_array();	
	$a = $this->compare_server_local($db1,$db2,$table_name);
	return true;
    }
    function sync_tables_store_id($table_name,$field_name=false){
	 $field_name = ($field_name)?$field_name:'store_id';
	 $db1 = $this->CI->centerdb->get_where($table_name,array($field_name=>$this->CI->store_id))->result_array();	
	 $db2 = $this->CI->db->get_where($table_name,array($field_name=>$this->CI->store_id))->result_array();	
	 $a = $this->compare_server_local($db1,$db2,$table_name);
	 return $a;
    }
    
   

/////////////// FROM CENTER - END /////////////////////
    function getStockRequest_Items_id($id){
	$q = $this->CI->centerdb->get_where('pro_stock_request_items',array('stock_request_id'=>$id));
	$data =  array();
	foreach($q->result_array() as $k => $row){
	    unset($row['s_no']);
	    $data[$k] = $row;
	}
	return $data;
    }
    function getStockReceive_Items_id($id){
	$q = $this->CI->centerdb->get_where('pro_store_receiver_items',array('store_receiver_id'=>$id));
	$data =  array();
	foreach($q->result_array() as $k => $row){
	    unset($row['s_no']);
	    $data[$k] = $row;
	}
	return $data;
    }
    function getStockReceive_Items_details_id($id){
	$q = $this->CI->centerdb->get_where('pro_store_receiver_item_details',array('store_receiver_id'=>$id));
	$data =  array();
	foreach($q->result_array() as $k => $row){
	    unset($row['s_no']);
	    $data[$k] = $row;
	}
	  return $data;
    }
}