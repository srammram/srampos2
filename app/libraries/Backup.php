<?php defined('BASEPATH') OR exit('No direct script access allowed');
/*
 *  ==============================================================================
 *  Author  : Tharani
 *  Email   : info@srampos.com
 *  ==============================================================================
 */

class Backup
{
    public $ftp_details ='oo';
    public function __construct() {
	$CI =& get_instance();
	$CI->load->model('site');
	$this->ftp_details = $CI->site->getAutoback_details();
    }
    public function pos_db(){
	$CI =& get_instance();
	if(date('Y-m-d',strtotime($this->ftp_details->ftp_db_last_backup)) != date('Y-m-d')){
	    
	    $data['db_backup_process'] = 'completed';
	    $CI->site->update_dbbackup_date($data);
	}
	if(date('Y-m-d',strtotime($this->ftp_details->ftp_db_last_backup)) != date('Y-m-d')  && $this->ftp_details->db_backup_process!='ongoing'){
	    
	    $CI->site->update_dbbackup_date($data);
	    $CI->load->dbutil();
	    $date = date('d-m-Y');
	    $prefs = array(
		'format' => 'txt',
		'filename' => 'srampos_'.$date.'.sql'
	    );
	    $back = $CI->dbutil->backup($prefs);
	    $backup =& $back;
	    
	    $db_name = $date.'.sql';
	    
	    //ftpConnect($path,$backup);
	    $temppath = $CI->Settings->backup_path.'/database/';
	    if (!file_exists($temppath)) {
		mkdir($temppath, 0777, true);
	    }
	    
	    $local_path= $temppath.$db_name;
	    if(write_file($local_path, $backup)){

		$ftp_conn = $this->ftpConnect();
		
		$remote_folder = $this->ftp_details->ftp_db_backup_path;
		$remote_path = $remote_folder.'/'.$db_name;
		
		@$this->ftp_mkdir_recusive($ftp_conn, $remote_folder);
		if (ftp_put($ftp_conn, $remote_path,$local_path,FTP_BINARY))
		  {
		    $data['ftp_db_last_backup'] = date('Y-m-d H:i:s');
		    $data['db_backup_process'] = 'completed';
		    
		  //echo "Successfully uploaded.";
		  }
		else
		  {
		    $data['ftp_db_last_backup'] = $this->ftp_details->ftp_db_last_backup;
		    $data['db_backup_process'] = 'completed';
		    
		  //echo "Error uploading.";
		  }
		  $CI->site->update_dbbackup_date($data);
		//close connection
		ftp_close($ftp_conn);
		//unlink($local_path);
		
		
	    }
	}
    }
    public function pos_folder(){
	$CI =& get_instance();
	$CI->load->library('zip');
	
	
	$lastbackup_date = date('Y-m-d',strtotime($this->ftp_details->ftp_files_last_backup));
	$today = date('y-m-d');
	$date1=date_create($lastbackup_date);
	$date2=date_create($today);
	$diff=date_diff($date1,$date2);
	$no_of_days_diff =  $diff->format("%R%a days");
	
	if($no_of_days_diff >= 7 && $this->ftp_details->files_backup_process!='ongoing') {
	    $data['files_backup_process'] = 'ongoing';
	    $CI->site->update_filesbackup_date($data);	    
	    $data['files_backup_process'] = 'ongoing';
	    $CI->site->update_filesbackup_date($data);	    
	    ///ZIP Files ////
	    $filename = date('Y-m-d').'.zip';
	    $temp_filepath = $CI->Settings->backup_path.'/files/';
	    if (!file_exists($temp_filepath)) {
		mkdir($temp_filepath, 0777, true);
	    }
	    $limit = ini_get('memory_limit');
	    $exec_time = ini_get('max_execution_time');
	    ini_set('memory_limit', -1);
	    ini_set('max_execution_time', 0);

	   
	    $filepath = $temp_filepath.$filename;
	  
	    $CI->zip->read_dir(FCPATH.'app',false);
	    $CI->zip->read_dir(FCPATH.'assets', false);
	    $CI->zip->read_dir(FCPATH.'files', false);
	    $CI->zip->read_dir(FCPATH.'install', false);
	    $CI->zip->read_dir(FCPATH.'node_modules', false);
	    $CI->zip->read_dir(FCPATH.'system', false);
	    $CI->zip->read_dir(FCPATH.'themes', false);
	    $CI->zip->read_dir(FCPATH.'vendor', false);
	    $CI->zip->read_file(FCPATH.'.htaccess', false);
	    $CI->zip->read_file(FCPATH.'index.php', false);
	    $CI->zip->read_file(FCPATH.'server.js', false);
	    $CI->zip->read_file(FCPATH.'serverdb.js', false);
	    $CI->zip->read_file(FCPATH.'startserver.bat', false);
	    $CI->zip->archive($filepath);
	    
	  
	    /// ftP//
	    $ftp_conn = $this->ftpConnect();	    
	    $remote_folder = $this->ftp_details->ftp_files_backup_path;
	   
	   
	    $remote_path = $remote_folder.'/'.$filename;
	    @$this->ftp_mkdir_recusive($ftp_conn, $remote_folder);
	    if(ftp_put($ftp_conn,$remote_path,$filepath,FTP_BINARY)){
		 
		$data['ftp_files_last_backup'] = date('Y-m-d H:i:s');
		$data['files_backup_process'] = 'completed';
	      //echo "Successfully uploaded.";
	    }
	    else
	    {
	      $data['ftp_files_last_backup'] = $this->ftp_details->ftp_files_last_backup;
	      $data['files_backup_process'] = 'completed';
	    //echo "Error uploading.";
	    }
	    
	    
	    $CI->site->update_filesbackup_date($data);	   
	    ftp_close($ftp_conn);
	    //array_map('unlink', glob("$temp_filepath/*.*"));
	    //rmdir($temp_filepath);
	    
	    ini_set('memory_limit', $limit);
	    ini_set('max_execution_time', $exec_time); 
	}else{
	    //echo "already there";
	}
	
    }
    function ftpConnect(){
	$ftp_server = $this->ftp_details->ftp_host;
	$ftp = ftp_connect($ftp_server);// or die("Could not connect to $ftp_server");
	$ftp_username=$this->ftp_details->ftp_username;
	$ftp_userpass=$this->ftp_details->ftp_password;
	$login = ftp_login($ftp, $ftp_username, $ftp_userpass);
	ftp_pasv($ftp, true);
	return $ftp;
    }
    
    function ftp_mkdir_recusive($con_id,$path){
	$parts = explode("/",$path);
	$return = true;
	$fullpath = "";
	foreach($parts as $part){
		if(empty($part)){
			$fullpath .= "/";
			continue;
		}
		$fullpath .= $part."/";
		@ftp_mkdir($con_id, $fullpath);
		ftp_chmod ( $con_id, 0777, $fullpath);
		
	}
	return $return;
    }
    function initiate(){
	$CI =& get_instance();
	if(date('Y-m-d',strtotime($this->ftp_details->ftp_db_last_backup)) != date('Y-m-d')){
	    
	    $data['db_backup_process'] = 'completed';
	    $CI->site->update_dbbackup_date($data);
	}
	$lastbackup_date = date('Y-m-d',strtotime($this->ftp_details->ftp_files_last_backup));
	$today = date('y-m-d');
	$date1=date_create($lastbackup_date);
	$date2=date_create($today);
	$diff=date_diff($date1,$date2);
	$no_of_days_diff =  $diff->format("%R%a days");
	if($no_of_days_diff >= 7 ){
	    
	    $data['files_backup_process'] = 'completed';
	    $CI->site->update_filesbackup_date($data);
	}
	
	   
	   
	
	if((date('Y-m-d',strtotime($this->ftp_details->ftp_db_last_backup)) != date('Y-m-d')  && $this->ftp_details->db_backup_process!='ongoing') || (date('Y-m-d',strtotime($this->ftp_details->ftp_files_last_backup)) != date('Y-m-d') && $no_of_days_diff >= 7 && $this->ftp_details->files_backup_process!='ongoing')) {
	
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, site_url('autobackup'));
	    curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_VERBOSE, true);
	    $result = curl_exec($ch);
	    curl_close($ch);
	}
    }
   
    
}
