<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Apisetting_api extends CI_Model
{

    public function __construct() {
        parent::__construct();
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
	
	public function refreshDevices($devices_key, $data_array){
		$this->db->where('devices_key', $devices_key);
		$q = $this->db->update('device_detail', $data_array);
        if ($q) {
            return TRUE;
        }
		return FALSE;	
	}
	
	public function updateDevices($api_key, $devices_key, $devices_type, $api_type){
		$this->db->where('key', $api_key);
		$q = $this->db->update('api_keys', array('devices_type' => $devices_type, 'devices_key' => $devices_key, 'api_type' => $api_type));
        if ($q) {
			$dd = $this->db->get_where('device_detail', array('devices_key' => $devices_key), 1);
			if ($dd->num_rows() == 0) {
				$this->db->insert('device_detail', array('device_type' => 'Android or IOS', 'devices_key' => $devices_key, 'created' => date('Y-m-d H:i:s')));	
			}
            return TRUE;
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

}
