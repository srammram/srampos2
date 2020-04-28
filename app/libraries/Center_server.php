<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Center_server {
    public function __construct() {
       $this->CI =& get_instance();
	
    }
    /////////////// FROM CENTER //////////////////////
    
    function connect(){
	$q = $this->CI->db->get('server_db_credentials');
	if($q->num_rows()>0){
	    $hosts = $q->result();
	    foreach($hosts as $k => $host){
			
			
		if(!$this->CI->centerdb_connected){
		    $config_db['hostname'] = $host->host;
		    $config_db['username'] = $host->username;
		    $config_db['password'] = $host->password;
		    $config_db['database'] = $host->db_name;
		    $config_db['dbdriver'] = 'mysqli';
			//$config_db['port'] = '3308';
		    $config_db['dbprefix'] = 'srampos_';
		    $config_db['pconnect'] = FALSE;
		    $config_db['db_debug'] = FALSE;
		    $config_db['char_set'] = 'utf8';
		    $config_db['dbcollat'] = 'utf8_general_ci';
			//$config_db['options'] = array(mysqli::ATTR_TIMEOUT => 1);
		    $this->CI->centerdb = $this->CI->load->database($config_db, TRUE);
			//print_r($this->CI->centerdb);die;
		    if($this->CI->centerdb->conn_id) {
			$this->CI->centerdb_connected =  true;
		    }
		    break;
		}
		
	    }
	}
	return false;
    }

}