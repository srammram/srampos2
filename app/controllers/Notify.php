<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Notify extends CI_Controller
{

    function __construct() {
        parent::__construct();
        $this->lang->admin_load('sma');
    }

    function error_404() {
        $this->session->set_flashdata('error', lang('error_404_message').site_url($this->uri->uri_string()));
        redirect('/');
    }

    function csrf($msg = NULL) {
        $data['page_title'] = lang('csrf_error');
        if (!$msg) { $msg = lang('cesr_error_msg'); }
        $this->session->set_flashdata('error', $msg);
        redirect('/', 'location');
    }

    function offline($msg = NULL) {
        $data['page_title'] = lang('site_offline');
        $data['msg'] = $msg;
        $this->load->view('default/notify', $data);
    }

    function payment_success($msg = NULL) {
        $data['page_title'] = lang('payment');
        $data['msg'] = $msg ? $msg : lang('thank_you');
        $data['msg1'] = lang('payment_added');
        $this->load->view('default/notify', $data);
    }

    function payment_failed($msg = NULL) {
        $data['page_title'] = lang('payment');
        $data['msg'] = $msg ? $msg : lang('error');
        $data['msg1'] = lang('payment_failed');
        $this->load->view('default/notify', $data);
    }

    function payment() {
        $data['page_title'] = lang('payment');
        $data['msg'] = lang('info');
        $data['msg1'] = lang('payment_processing');
        $this->load->view('default/notify', $data);
    }
    function bbqnotification(){
        //$this->load->library('socketemitter');
        $this->load->library('firebase');
        $this->load->library('push');
        $this->load->model('site');
        $table_id = $this->input->post('table_id');
	$user_id = $this->input->post('user_id');
        $steward_id = $this->site->getSteward($table_id);
	$group_id = $this->input->post('group_id');
	$warehouse_id = $this->input->post('warehouse_id');
	$stop_count = $this->input->post('stop_count');
	
	$bbq = $this->input->post('bbq');
	$table_name = $this->site->getTablename($table_id);
	$notification_message = $table_name.' - Customer has sent BBQ Covers.';
	$notification_title = 'BBQ Covers validation request - '.$bbq;
	//$notification_array['from_role'] = $group_id;
        $device_token = $this->site->deviceDetails($steward_id);
	$notification_array['insert_array'] = array(
		'msg' => $notification_message,
		'type' => $notification_title,
		//'reference'=>$bbq,
		'table_id' => $table_id,
		'user_id' => $user_id,
		'to_user_id' => $steward_id,	
		'role_id' => $group_id,
		'warehouse_id' => $warehouse_id,
		'created_on' => date('Y-m-d H:i:s'),
		'is_read' => 0,
		'respective_steward'=>$steward_id,
		'split_id'=>$bbq,
		'tag'=>'bbq-cover-validation',
	);
	$notifyID = $this->site->add_notification($notification_array);
		
	foreach($device_token as $k =>$device){
		$title = $notification_title;
		$message = $notification_message;
		$push_data = $this->push->setPush($title,$message);
		if($this->site->isSocketEnabled() && $push_data == true && isset($device->socket_id) && $device->socket_id!=''){
			$json_data = '';
			$response_data = '';
			$json_data = $this->push->getPush();
			$regId_data = $device->device_token;
                        $socketid = $device->socket_id;
                        $bbq_code = $bbq;
                        $table_id = $table_id;
			//$response_data = $this->firebase->send($regId_data, $json_data);
			$this->site->send_BBQpushNotification($title,$message,$socketid,$bbq_code,$table_id,$notifyID,'bbq_cover_validation');
        
        
		}
	}

	//$notification['title'] = $notification_title;
	//$notification['msg'] = $notification_message;
	//$notification['socketid'] = $this->input->post('socketid');
	//$event = 'notification';
	//$edata = $notification;
	//$this->socketemitter->setEmit($event, $edata);
		//if($count < 2){	
			$this->site->setTimeout('is_bbqCoversValidated',$bbq,1);
		//}
    }
	
	
	function bbqnotification_stop(){

		$this->load->model('site');
		$stop_count = $this->input->post('stop_count');
		$tag = 'bbq-cover-validation';
		$bbq = $this->input->post('bbq');
		$q = $this->db->update('notiy', array('stop' => 1), array('split_id' => $bbq, 'tag' => $tag));
		// if($q){
		$Settings = $this->site->get_setting();
	    $timeout = $Settings->notification_start_interval;
			sleep($timeout);		
			$this->bbqnotification_start($bbq,$tag);			
			//$this->setTimeout('bbqnotification_start',$bbq,$tag);
		// }
    }
	
	public function bbqnotification_start($bbq, $tag){
		 $this->load->model('site');		
		//$tag = 'bbq-cover-validation';
		$bbq_code = $bbq;
		$notify_data = $this->db->select('*')->from('notiy')->where('split_id', $bbq)->where('tag', $tag)->order_by('split_id')->get();
		$q = $this->db->update('notiy', array('stop' => 0), array('split_id' => $bbq, 'tag' => $tag));
		
		// $q = $this->db->update('notiy', array('stop' => 0), array('split_id' => $bbq_code, 'tag' => $tag));
		if($q){
			 //$this->load->library('socketemitter');
			// $q = $this->db->update('notiy', array('stop' => 110), array('split_id' => $bbq, 'tag' => $tag));
			$table_id = $notify_data->row('table_id');
			$user_id = $notify_data->row('user_id');
			$steward_id = $this->site->getSteward($table_id);
			$notifyID = $notify_data->row('id');
			
			$table_name = $this->site->getTablename($table_id);
			$notification_message = $table_name.' - Customer has sent BBQ Covers.';
			$notification_title = 'BBQ Covers validation request - '.$bbq;
			//$notification_array['from_role'] = $group_id;
			$device_token = $this->site->deviceDetails($steward_id);
			
			
			foreach($device_token as $k =>$device){
				$title = $notification_title;
				$message = $notification_message;
				$push_data = $this->push->setPush($title,$message);
				if($this->site->isSocketEnabled() && $push_data == true && isset($device->socket_id) && $device->socket_id!=''){
				$json_data = '';
				$response_data = '';
				$json_data = $this->push->getPush();
				$regId_data = $device->device_token;
				$socketid = $device->socket_id;
				$bbq_code = $bbq;
				$table_id = $table_id;
				//$response_data = $this->firebase->send($regId_data, $json_data);
				$this->site->send_BBQpushNotification($title,$message,$socketid,$bbq_code,$table_id,$notifyID,'bbq_cover_validation');
				
				
			 }
			}
			$this->site->setTimeout('is_bbqCoversValidated',$bbq,1);
		}
    }
	
	
	function bbqreturn_notification_stop(){
		$this->load->model('site');
		//$table_id = $this->input->post('table_id');
		//$user_id = $this->input->post('user_id');
		//$steward_id = $this->site->getSteward($table_id);
		//$group_id = $this->input->post('group_id');
		//$warehouse_id = $this->input->post('warehouse_id');
		$stop_count = $this->input->post('stop_count');
		$tag = 'bbq-return';
		$bbq = $this->input->post('bbq');
		//$table_name = $this->site->getTablename($table_id);
		
		$q = $this->db->update('notiy', array('stop' => 1), array('split_id' => $bbq, 'tag' => $tag));
		if($q){
			$this->setTimeout('bbqreturn_notification_start',$bbq,$tag);
		}
    }
	
	function billRequestNotification_stop(){
		$this->load->model('site');
		//$table_id = $this->input->post('table_id');
		//$user_id = $this->input->post('user_id');
		//$steward_id = $this->site->getSteward($table_id);
		//$group_id = $this->input->post('group_id');
		//$warehouse_id = $this->input->post('warehouse_id');
		$stop_count = $this->input->post('stop_count');
		
		$bbq = $this->input->post('bbq');
		//$table_name = $this->site->getTablename($table_id);
		
		$this->db->update('notiy', array('stop' => 1), array('split_id' => $bbq, 'tag' => 'bill-request'));
    }
	
    function bbqreturn_notification(){
        //$this->load->library('socketemitter');
        $this->load->library('firebase');
        $this->load->library('push');
        $this->load->model('site');
        $table_id = $this->input->post('table_id');
	$user_id = $this->input->post('user_id');
	$bbq = $this->input->post('bbq');
        $steward_id = $this->site->getBBQSteward($bbq);
	$group_id = $this->input->post('group_id');
	$warehouse_id = $this->input->post('warehouse_id');
	
	
	$table_name = $this->site->getTablename($table_id);
	$notification_message = $table_name.' - Customer has requested BBQ Return.';
	$notification_title = 'BBQ Return Request - '.$bbq;
	//$notification_array['from_role'] = $group_id;
        $device_token = $this->site->deviceDetails($steward_id);
	$notification_array['insert_array'] = array(
		'msg' => $notification_message,
		'type' => $notification_title,
		//'reference'=>$bbq,
		'table_id' => $table_id,
		'user_id' => $user_id,
		'to_user_id' => $steward_id,	
		'role_id' => $group_id,
		'warehouse_id' => $warehouse_id,
		'created_on' => date('Y-m-d H:i:s'),
		'is_read' => 0,
		'respective_steward'=>$steward_id,
		'split_id'=>$bbq,
		'tag'=>'bbq-return',
	);
	
	$notifyID = $this->site->add_notification($notification_array);
		
	foreach($device_token as $k =>$device){
		$title = $notification_title;
		$message = $notification_message;
		$push_data = $this->push->setPush($title,$message);
		if($this->site->isSocketEnabled() && $push_data == true && isset($device->socket_id) && $device->socket_id!=''){
			$json_data = '';
			$response_data = '';
			$json_data = $this->push->getPush();
			$regId_data = $device->device_token;
                        $socketid = $device->socket_id;
                        $bbq_code = $bbq;
                        $table_id = $table_id;
			//$response_data = $this->firebase->send($regId_data, $json_data);
			$this->site->send_BBQReturnpushNotification($title,$message,$socketid,$bbq_code,$table_id,$notifyID,'bbq_return');
        
        
		}
	}

	//$notification['title'] = $notification_title;
	//$notification['msg'] = $notification_message;
	//$notification['socketid'] = $this->input->post('socketid');
	//$event = 'notification';
	//$edata = $notification;
	//$this->socketemitter->setEmit($event, $edata);		
	$this->site->setTimeout('is_bbqReturnCompleted',$bbq,1);
    }
    function billRequestNotification(){
	$split_id= $this->input->post('split_id');	
	$this->site->setTimeout('BillRequestNotification',$split_id,1);
    }
    function paymentRequestNotification(){
	$split_id= $this->input->post('split_id');	
	$this->site->setTimeout('PaymentRequestNotification',$split_id,1);
    }
   
    function updatesocketjs(){
	$port= $this->input->post('port');
	$setting = $this->site->get_setting();
	file_put_contents('themes/default/admin/assets/js/socket/socket_configuration.js','var socket_port='.$setting->socket_port.';var socket_host="'.$setting->socket_host.'";var socket_enable="'.$setting->socket_enable.'";');
	file_put_contents('themes\default\admin\assets\js\socket\socket_configuration.js','var socket_port='.$setting->socket_port.';var socket_host="'.$setting->socket_host.'";var socket_enable="'.$setting->socket_enable.'";');
    }
    


}
