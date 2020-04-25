<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Shift_model extends CI_Model{

    public function __construct()
    {
        parent::__construct();
    }
	
	function createShift($data){
		$q = $this->db->insert('shifts', $data);
		if($q){
			return true;
		}
		return false;
	}
	
	function listshift($start_date, $end_date){
		$this->db->select('s.id, s.shift_start_time, s.shift_end_time, sm.name as shiftmaster_name');
		$this->db->from('shifts s');
		$this->db->join('shiftmaster sm', 'sm.id = s.shiftmaster_id', 'left');
		$this->db->where('s.settled', 1);
		$q = $this->db->get();
		if ($q->num_rows() > 0) {
			return $q->result();	
		}
		return false;
	}
	
	function getShiftView($shift_id){
		$this->db->select('s.id as sid, sm.name as shift_name, c.first_name as created_name, u.first_name as assigned_name, t.till_name, s.warehouse_id, s.till_id, s.user_id, s.total_cash, s.created_on, s.shift_from_time, s.shift_to_time, s.shift_start_time, s.shift_end_time, s.shiftmaster_id, s.CUR_USD, s.CUR_KHR, ss.*');
		$this->db->from('shifts s');
		$this->db->join('shifts_settlement ss', 'ss.shift_id = s.id', 'left');
		$this->db->join('tills t', 't.id = s.till_id', 'left');
		$this->db->join('users c', 'c.id = s.created_by', 'left');
		$this->db->join('users u', 'u.id = s.user_id', 'left');
		$this->db->join('shiftmaster sm', 'sm.id = s.shiftmaster_id', 'left');
		$this->db->where('s.id', $shift_id);
		$q = $this->db->get();
		if ($q->num_rows() > 0) {
			return $q->row_array();	
		}
		return false;	
	}
	function getPayment($shift_id, $warehouse_id, $till_id){
		$res = array();
		$this->db->select('CUR_USD as OPEN_USD, CUR_KHR as OPEN_KHR');
		$this->db->where('id',$shift_id);
		$q = $this->db->get('shifts');
		if ($q->num_rows() > 0) {
			$r = $q->row_array();
			$r['ACTUAL_USD'] = 0;
			$r['ACTUAL_KHR'] = 0;
			
			$s = $this->db->select('s.id, s.grand_total')->from('sales s')->where('s.shift_id', $shift_id)->where('s.till_id', $till_id)->get();
			if ($s->num_rows() > 0) {
				foreach (($s->result()) as $sow) {
					$sale_id[] = $sow->id;
					$sale_amount[] = $sow->grand_total;
				}
			}
			
			$r['ACTUAL_USD'] = array_sum($sale_amount) ? array_sum($sale_amount) : 0;
			
			$r['RECEIVED_USD'] = 0;
			$r['RECEIVED_KHR'] = 0;
			$r['DIFFERENCE_USD'] = 0;
			$r['DIFFERENCE_KHR'] = 0;
			$res['cash'] = $r;
			$p = $this->db->select('SUM(amount) as amount')->where_in('sale_id', $sale_id)->where('paid_by', 'CC')->get('payments');
			//print_r($this->db->last_query());
			if ($p->num_rows() > 0) {
				$card = $p->row('amount');
			}
			$res['card'] = $card;
			
			return $res;
		}
		return false;
	}
	function updateDontcontinueShift($id){
		$this->db->where('id',$id);
		$q = $this->db->update('shifts', array('continued_shift' => 2));
		if($q){
			return true;
		}
		return false;
			
	}
	
	function checkcounter($warehouse_id, $till_id){
	 	$this->db->select('s.*, t.till_name as till_name');
	    $this->db->from('shifts s');
		$this->db->join('tills t', 't.id = s.till_id');
	    $this->db->where('s.till_id',$till_id);
	    //if($user_id){
			//$this->db->where('user_id',$user_id);
		//}
	    $this->db->where('s.settled',0);
	    $this->db->where('s.warehouse_id',$warehouse_id);
	    $this->db->order_by('s.id','desc');
	    $q = $this->db->get();
		//print_r($this->db->last_query());
	    if($q->num_rows()>0){
			$end_time =  $q->row('shift_end_time');
			if($end_time=='0000-00-00 00:00:00'){
				return TRUE;
			}
	    }
		return FALSE;
    }
	
	
	function add_settlement($id, $settlement){
		$this->db->where('id',$id);
		$q = $this->db->update('shifts', array('settled' => 1, 'shift_end_time' => date('Y-m-d H:i:s')));
		//$q = $this->db->update('shifts', array('settled' => 0));
		if($q){
			$this->db->insert('shifts_settlement', $settlement);
			return true;
		}
		return false;
			
	}
	
	function getShiftSales_Details($id, $till_id, $user_id){
		$this->db->select('s.id as id, u.first_name, c.first_name as created_name, s.shift_end_time, ss.*');
		$this->db->from('shifts s');
		$this->db->join('shifts_settlement ss', 'ss.shift_id = s.id');
		$this->db->join('users u', 'u.id = s.user_id', 'left');
		$this->db->join('users c', 'c.id = s.created_by', 'left');
		$this->db->where('s.id', $id);
		$this->db->where('s.till_id', $till_id);
		$q = $this->db->get();
		if($q->num_rows()>0){
			$row = $q->row();
			
			$row->sale_cash = '0.00';
			$row->sale_card = '0.00';
			$row->sale_giftvoucher = '0.00';
			$row->sale_wallet = '0.00';
			
			$sp = $this->db->select('ss.id, sp.paid_by, sp.amount, sp.amount_exchange')->from('sales ss')->join('payments sp', 'sp.sale_id = ss.id', 'left')->where('ss.shift_id', $id)->get();
			
			if($sp->num_rows()>0){
				foreach($sp->result() as $sprow){
					if($sprow->paid_by == 'cash'){
						
						$cash[] = $sprow->amount;
						$cash_exchange[] = $sprow->amount_exchange;
						
					}elseif($sprow->paid_by == 'CC'){
						$CC[] = $sprow->amount;
					}
				}
				
				$row->sale_cash = round(array_sum($cash) + (array_sum($cash_exchange)*0.00024390244)/1.00000000000 - array_sum($CC), 2);
				$row->sale_card = array_sum($CC);
			}
			
			$data = $q->row_array();
			
			
			$row->denominations = array();
			//print_r($data);
			$cash_columns_query ="SHOW COLUMNS FROM srampos_shifts_settlement LIKE 'cash_%_actual' ";	    
			$c = $this->db->query($cash_columns_query)->result();
			$cash_column = array();
			
			foreach($c as $k => $column){
				$f = $column->Field;
				if (preg_match('/cash_(.*?)_actual/', $f, $match) == 1) {
					//$match[1];
				}
				array_push($cash_column,$match[1]);
			
			}
			foreach($cash_column as $cur){
				$cash_de_columns_query ="SHOW COLUMNS FROM srampos_shifts_settlement LIKE '".$cur."%'";    
				$c_d = $this->db->query($cash_de_columns_query)->result();	
				
				foreach($c_d as $cd => $de_column){
					
					$df = $de_column->Field;
					$df = str_replace($cur.'_','',$df);
					$row->denominations[$cur][$de_column->Field] = $data[$cur.'_'.$df];
				}
			}
			//print_r($row);
	    	return $row;	
	    }
		return FALSE;	
	}
	
	
	
	function updateShift($shift_id,$approved_by){
		$this->db->where('id',$shift_id);
		$q = $this->db->update('shifts', array('continued_shift' => 1, 'continued_shift_approved_by' => $approved_by));
		if($q){
			return true;
		}
		return false;
			
	}

}
