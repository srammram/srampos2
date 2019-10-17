<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Customersetting_api extends CI_Model
{

    public function __construct() {
        parent::__construct();
    }

	public function Alltablecategory($warehouse_id){
		
		$this->db->select('restaurant_areas.*');
		$this->db->join('restaurant_tables', 'restaurant_tables.warehouse_id = '.$warehouse_id.' AND restaurant_tables.area_id = restaurant_areas.id ');
		$this->db->group_by('restaurant_areas.id');
		$query = $this->db->get('restaurant_areas');
		// print_r($this->db->last_query());die;
		if ($query->num_rows() > 0) {
			 foreach ($query->result() as $row) {
				 $data[] = $row;
			 }
			  return $data;
		}
		 return FALSE;
	}
	
	public function fetchData($warehouse_id, $table_id, $waiter_id){
		$q = $this->db->get_where('users', array('id' => $waiter_id), 1);
        if ($q->num_rows() == 1) {
			
			$waiter_group_id = $q->row('group_id');
			$biller_id  = $q->row('biller_id');
			
			$data['waiter_group_id'] = $waiter_group_id;
			$data['biller_id'] =  $biller_id;
			$data['table_id'] = $table_id;
			$data['waiter_id'] = $waiter_id;
			$data['warehouse_id'] = $warehouse_id;
			    $s = $this->db->get_where('settings');
			    $settings = $s->row();
			    $data['socket_port'] = $settings->socket_port;
			    $data['socket_host'] = $settings->socket_host;
            return $data;
        }
		return FALSE;
	}
	public function GetwaiterDetails($waiter_id){
		$q = $this->db->get_where('users', array('id' => $waiter_id), 1);
        if ($q->num_rows() == 1) {
			
            return $q->row();
        }
		return FALSE;
	}
	
	public function GetBuild($type){
		$this->db->select('id, code AS version_code, version AS version_name, CONCAT(build_location, build_files) AS files');
		$q = $this->db->get_where('buildapi', array('type' => $type), 1);
        if ($q->num_rows() == 1) {
			
            return $q->row();
        }
		return FALSE;
	}
	
	
	
	public function checkDevices($api_key){
		$q = $this->db->get_where('api_keys', array('key' => $api_key), 1);
        if ($q->num_rows() == 1) {
			
            return $q->row();
        }
		return FALSE;
	}
	
	public function updateDevices($api_key, $devices_key, $devices_type, $api_type){
		$this->db->where('key', $api_key);
		$q = $this->db->update('api_keys', array('devices_type' => $devices_type, 'devices_key' => $devices_key, 'api_type' => $api_type));
        if ($q) {
            return TRUE;
        }
		return FALSE;
	}
	
	public function GetAlltables($warehouse_id, $area_id, $bbq_type){
		
		$this->db->where('warehouse_id', $warehouse_id);
		$this->db->where('area_id', $area_id);
		// $this->db->where('sale_type', $bbq_type);
		$q = $this->db->get('restaurant_tables');
		// print_r($this->db->last_query());die;		
		if ($q->num_rows() > 0) {
			
			foreach (($q->result()) as $row) {
				$row->bbq_enable = 0;
				if($row->sale_type == 'bbq'){
					$row->bbq_enable = 1;
				}
				if($bbq_type == 'bbq'){
					$row->bbq_enable_val = 1;
				}else{
					$row->bbq_enable_val = 0;
				}
				$data[] = $row;
			}
			return $data;
		}
        return FALSE;
	}
	
	public function GetAllapitype(){
		$q = $this->db->get('group_api');
		if ($q->num_rows() > 0) {			
			foreach (($q->result()) as $row) {
				$data[] = $row;
			}
			return $data;
		}
        return FALSE;
	}
	
	public function GetAllwarehouse(){
		$q = $this->db->get('warehouses');
		if ($q->num_rows() > 0) {
			
			foreach (($q->result()) as $row) {
				$data[] = $row;
			}
			return $data;
		}
        return FALSE;
	}
	
	public function GetAllwaiter($warehouse_id){
		$WAITER = WAITER;
		$SALE = SALE;
		$this->db->where('warehouse_id', $warehouse_id);
		$this->db->where_in('group_id', array($WAITER, $SALE));
		$q = $this->db->get('users');
		if ($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				$data[] = $row;
			}
			return $data;
		}
        return FALSE;
	}
    
    

}
