<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Shift extends MY_Controller
{

    function __construct() {
        parent::__construct();
		
		$this->lang->admin_load('posnew', $this->Settings->user_language);
		$this->load->model('pos/shift_model');
		$this->load->library('ion_auth');
        $this->load->library('form_validation');
    }
	
	function checkcounter($counter_name){
		$res = array();
		$r = $this->shift_model->checkcounter($this->session->userdata('warehouse_id'), $counter_name);
		if($r == TRUE){
			$res = array('status' => 'success');
		}else{
			$res = array('status' => 'error');
		}
		echo json_encode($res);
	}
	
	function listshift(){
		$html = '';	
		$start_date = $_GET['start_date'] ? $_GET['start_date'] : date('Y-m-d');
		$end_date = $_GET['end_date'] ? $_GET['end_date'] : date('Y-m-d');
		$res = $this->shift_model->listshift($start_date, $end_date);
		if(!empty($res)){
			foreach($res as $row){
			$html .= '<tr><td>'.$row->shift_start_time.' to '.$row->shift_end_time.'</td><td>'.$row->shiftmaster_name.'</td><td><a class="btn btn-success" href="'.base_url('pos/shift/shift_settlement_view/'.$row->id).'">View</a></td></tr>';
			}
		}else{
			$html .= '<tr><td colspan="3">No Data</td></tr>';
		}
		echo $html;
	}

	function shift_settlement(){
		if($_GET['till_id'] != 0){
			$till_id = $_GET['till_id'];
		}else{
			$till_id = $this->till_id;
		}
		
		
		$pendingSettlement = $this->site->getpendingshift($this->session->userdata('warehouse_id'), $till_id,$this->session->userdata('user_id'));	
		if(empty($pendingSettlement)){
			redirect('pos/login/logout');
		}
		
		$this->data['pendingSettlement'] = $pendingSettlement;
		$this->data['payment_type'] = $this->shift_model->getPayment($pendingSettlement->id, $pendingSettlement->warehouse_id, $pendingSettlement->till_id);
		
		$this->data['get_till_id'] = $till_id;
		$this->data['tils'] = $this->site->getTils();
		
		$this->load->view($this->theme . 'pos_v2/shift/shift_settlement', $this->data);
	}
	
	function shift_settlement_view($shift_id){
		
		$settlement = $this->shift_model->getShiftView($shift_id);	
		
		$this->data['settlement'] = $settlement;
		$this->load->view($this->theme . 'pos_v2/shift/shift_settlement_view', $this->data);
	}
	
	function add_settlement(){
		$shift_id = $this->input->post('shift_id');
		if($shift_id){
			$settlement = array(
				'shift_id' => $this->input->post('shift_id'),
				'warehouse_id' => $this->input->post('warehouse_id'),
				'till_id' => $this->input->post('till_id'),
				'user_id' => $this->input->post('user_id'),
				'created_on' => date('Y-m-d H:i:s'),
				'created_by' => $this->session->userdata('user_id'),
				'no_of_bills' => $this->input->post('no_of_bills'),
				'no_of_items' => $this->input->post('no_of_items'),
				'bill_total' => $this->input->post('bill_total'),
				'default_currency' => $this->input->post('default_currency'),
				'cash_open' => $this->input->post('cash_open'),
				'cash_actual' => $this->input->post('cash_actual'),
				'cash_received' => $this->input->post('cash_received'),
				'cash_difference' => $this->input->post('cash_difference'),
				
				'giftvoucher_open' => $this->input->post('giftvoucher_open'),
				'giftvoucher_actual' => $this->input->post('giftvoucher_actual'),
				'giftvoucher_received' => $this->input->post('giftvoucher_received'),
				'giftvoucher_difference' => $this->input->post('giftvoucher_difference'),
				
				'wallet_open' => $this->input->post('wallet_open'),
				'wallet_actual' => $this->input->post('wallet_actual'),
				'wallet_received' => $this->input->post('wallet_received'),
				'wallet_difference' => $this->input->post('wallet_difference'),
				
				'card_open' => $this->input->post('card_open'),
				'card_actual' => $this->input->post('card_actual'),
				'card_received' => $this->input->post('card_received'),
				'card_difference' => $this->input->post('card_difference'),
				
				'USD_1' => $this->input->post('USD_1'),
				'USD_2' => $this->input->post('USD_2'),
				'USD_5' => $this->input->post('USD_5'),
				'USD_10' => $this->input->post('USD_10'),
				'USD_20' => $this->input->post('USD_20'),
				'USD_50' => $this->input->post('USD_50'),
				'USD_100' => $this->input->post('USD_100'),
				'USD_200' => $this->input->post('USD_200'),
				'USD_500' => $this->input->post('USD_500'),
				'KHR_100' => $this->input->post('KHR_100'),
				'KHR_1000' => $this->input->post('KHR_1000'),
				'KHR_2000' => $this->input->post('KHR_2000'),
				'KHR_5000' => $this->input->post('KHR_5000'),
				'KHR_10000' => $this->input->post('KHR_10000'),
				'KHR_50000' => $this->input->post('KHR_50000'),
				'cash_USD_actual' => $this->input->post('cash_USD_actual'),
				'cash_KHR_actual' => $this->input->post('cash_KHR_actual'),
				'cash_USD_received' => $this->input->post('cash_USD_received'),
				'cash_KHR_received' => $this->input->post('cash_KHR_received'),
				'cash_USD_difference' => $this->input->post('cash_USD_difference'),
				'cash_KHR_difference' => $this->input->post('cash_KHR_difference'),
				'opening_cash_USD' => $this->input->post('opening_cash_USD'),
				'opening_cash_KHR' => $this->input->post('opening_cash_KHR'),
				'reprint' => 1
			);
			$res = $this->shift_model->add_settlement($shift_id, $settlement);
			if($res == TRUE){
				$this->session->unset_userdata('dont_continue_shift');
				echo json_encode(array('print'=>1,'shift_id'=>$shift_id));exit;
			}else{
				redirect('pos/login/logout');
			}
		}		
    }
	
	function get_shift_data($id,$ajax=NULL){
		if($_GET['reprint'] != 0){
			$this->db->update('shifts_settlement', array('reprint' => $_GET['reprint'] +1), array('shift_id' => $id));
		}
		$till_id = $this->till_id;
		$user_id = $this->session->userdata('user_id');
		$this->data['settlement'] = $this->shift_model->getShiftSales_Details($id, $till_id, $user_id);
		//print_r($this->data['settlement']);
		$html = $this->load->view($this->theme . 'pos_v2/shift/print_settlement', $this->data, true);
		if($ajax){
			echo $html;
		}else{
			return $html;
		}
    }
	
	function create_shift(){
		$data = array(
			'warehouse_id' => $this->input->post('warehouse_id'),
			'till_id' => $this->input->post('till_id'),
			'user_id' => $this->input->post('user_id') ? $this->input->post('user_id') : 0,
			'total_cash' => $this->input->post('total_cash'),
			'created_on' => date('Y-m-d H:i:s'),
			'created_by' => $this->session->userdata('user_id'),
			'shift_from_time' => $this->input->post('shift_from_time'),
			'shift_to_time' => $this->input->post('shift_to_time'),
			'shift_start_time' => date('Y-m-d H:i:s'),
			'shiftmaster_id' => $this->input->post('shiftmaster_id')
		);
		if(isset($_POST['cash'])){
			foreach($_POST['cash'] as $k => $cur_cash){
				$data[$k] = $cur_cash;
			}
		}
		if($this->shift_model->createShift($data) == TRUE){
			echo json_encode(array('status'=>'1'));
		}else{
			echo json_encode(array('status'=>'0'));
		}
		
	}
	
	function dont_continue_shift(){
		
		
		 $this->session->set_userdata('dont_continue_shift',true);
		$exitShift = $this->exitShift;
		
		if($exitShift){
			$shift_id = $exitShift->id;
			$this->shift_model->updateDontcontinueShift($shift_id);
			
			redirect('pos/shift/shift_settlement');
		}		
    }
	
	function continue_shift(){
		$approved_by = $this->session->userdata('user_id');
		$exitShift = $this->exitShift;
		
		if($exitShift){
			$shift_id = $exitShift->id;
			$this->shift_model->updateShift($shift_id,$approved_by);
			
			redirect($_SERVER["HTTP_REFERER"]);	
		}
	}

}
