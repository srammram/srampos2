<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Autobackup extends MY_Controller
{

    function __construct() {
        parent::__construct();
    }
    function index(){
        ob_end_clean();
        ignore_user_abort();
        ob_start();
        header("Connection: close");
        echo @json_encode($out);
        header("Content-Length: " . ob_get_length());
        @ob_end_flush();
        flush();
        //$limit = ini_get('memory_limit');
        //$exec_time = ini_get('max_execution_time');
        //ini_set('memory_limit', -1);
        //ini_set('max_execution_time', 0);
        
        $this->load->library('backup');
        $this->backup->pos_folder();
        $this->backup->pos_db();
        
        
//        ini_set('memory_limit', $limit);
//	ini_set('max_execution_time', $exec_time);
        
              
    }
    function test(){
        $this->load->dbutil();
        $date = date('d-m-Y');
        $prefs = array(
            'format' => 'txt',
            'filename' => 'srampos_'.$date.'.sql'
        );
	echo $back = $this->dbutil->backup($prefs);
	$backup =& $back;
    }



}
