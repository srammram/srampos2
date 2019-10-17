<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Login_api extends CI_Model
{

    public function __construct() {
        parent::__construct();
    }
	
	public function deviceGET($user_number){
		$this->db->select('users.id, device_detail.device_token');
		$this->db->join('device_detail', 'device_detail.user_id = users.id', 'left');
		$this->db->where('users.user_number', $user_number);
		$q = $this->db->get('users');
		if ($q->num_rows() == 1) {
			
			return  $q->row('device_token');
			
		}
		return FALSE;
	}

	public function GetuserByID($user_number){
		
				
		$query = $this->db->select('*')
            ->where('user_number', $user_number)
            ->limit(1)
            ->get('users');
			
		if ($query->num_rows() === 1) {
            $user = $query->row();
			
			/*#########*/
				/*$current_date = date('Y-m-d');
		
				$u = $this->db->select('*')->where('to_user_id', $query->row('id'))->where('is_read', 0)->where('warehouse_id', $query->row('warehouse_id'))->where('DATE(created_on)', $current_date)->get('notiy');
				if ($u->num_rows() > 0) {
					foreach($u->result() as $uow){
						$user_no[] = $uow;
					}
				}
				
				$r =$this->db->select('*')->where('role_id', $query->row('group_id'))->where('to_user_id', 0)->where('warehouse_id', $query->row('warehouse_id'))->where('is_read', 0)->where('DATE(created_on)', $current_date)->get('notiy');
				if ($r->num_rows() > 0) {
					foreach($r->result() as $row){
						$group_no[] = $row;
					}
				}
				
				if(!empty($user_no) && empty($group_no)){
					$notification_list = $user_no;
				}elseif(empty($user_no) && !empty($group_no)){
					$notification_list = $group_no;
				}elseif(!empty($user_no) && !empty($group_no)){
					$notification_list = array_merge($user_no, $group_no);
				}
		
				$user->notification_count = count($notification_list);*/
				/*#############*/
				
			$p = $this->db->select('pos-waiter, pos-kitchen, pos-cashier')->where('group_id', $query->row('group_id'))->get('permissions');
			if ($p->num_rows() > 0) {
				
				$user->waiter = $p->row('pos-waiter');
				$user->kitchen = $p->row('pos-kitchen');
				$user->cashier = $p->row('pos-cashier');

				/*$user->waiter = $p->row('pos-orders');
				$user->kitchen = $p->row('pos-kitchens');
				$user->cashier = $p->row('pos-billing');*/
				
				
				
			}
			
			
			$s = $this->db->select('*')->get('settings');
			if ($s->num_rows() > 0) {
				
				$default_currency_data = $this->site->getCurrencyByID($s->row('default_currency'));
				$user->base_currency_id = $default_currency_data->id;
				$user->base_currency_code = $default_currency_data->code;
				$user->base_currency_rate = $default_currency_data->rate;
				
				$user->bbq_discount = $s->row('bbq_discount');
				$user->bbq_enable = $s->row('bbq_enable');
				if($s->row('bbq_enable') == 1){
					$user->bbq_return_enable = 1;
				}else{
					$user->bbq_return_enable = 0;
				}
				$user->bbq_adult_price = $s->row('bbq_adult_price');
				$user->bbq_child_price = $s->row('bbq_child_price');
				$user->bbq_kids_price = $s->row('bbq_kids_price');
				
			}
			
			
			
			
			$ldata = array('user_id' => $user->id, 'ip_address' => $user->ip_address, 'login' => $user->id);
			$ldata['group_id'] = $user->group_id;
			$this->db->insert('user_logins', $ldata);
			
			$data = $user;
			
			return $data;
        }
		return FALSE;
	}
	
	/*public function Checknightaudit($branch_id) {
		$date_format = 'Y-m-d';
		$yesterday = strtotime('-1 day');
		$previous_date = date($date_format, $yesterday);
		$check_row = $this->db->get('nightaudit');
		if($check_row->num_rows > 0){
			$this->db->where('nightaudit_date', $previous_date);
			$this->db->where('warehouse_id', $branch_id);
			$q = $this->db->get('nightaudit');
			if ($q->num_rows() > 0) {
				 return TRUE;
			}
		}else{
			return TRUE;
		}
        return FALSE;
    }*/
	
	public function Checknightaudit($branch_id) {
		$date_format = 'Y-m-d';
		$yesterday = strtotime('-1 day');
		$previous_date = date($date_format, $yesterday);
		$check_row = $this->db->get('nightaudit');

		$installed_date = $this->Settings->installed_date;
		$install = strtotime($installed_date);        
		$install_date = date('Y-m-d', $install);
		$today_date = date('Y-m-d');
		
		$q = $this->db->select('*')->get('settings');
		$night_audit_rights = $q->row('night_audit_rights');
		if($night_audit_rights == 1){
			if($install_date < $today_date){
				
				if($check_row->num_rows() > 0){
				    $todaytransactionDay = $this->site->getTransactionDate_nightaudit();
				    $previousTransactionDay = $this->site->getLastDayTransactionDate();
				    if (!$todaytransactionDay || $todaytransactionDay==date('Y-m-d')){
					$this->db->where('nightaudit_date', $previous_date);
					$this->db->where('warehouse_id', $branch_id);
					$q = $this->db->get('nightaudit');
					if ($q->num_rows() > 0) {
						
						 return TRUE;
					}
					else{
						return FALSE;
					}
				    }else{
					return true;
				    }
				}
				else{	
					return FALSE;
						
				}
				
			}
			else{
				
				return TRUE;
			}
		
		}else{
			return TRUE;
		}
        return FALSE;
    }
	
	public function userDevices($devices_key, $data_array1, $data_array2){
		
		$dd = $this->db->get_where('device_detail', array('user_id' => $data_array1['user_id'],'group_id' => $data_array1['group_id']), 1);
		if ($dd->num_rows() >0) {
			$this->db->where(array('user_id' => $data_array1['user_id'],'group_id' => $data_array1['group_id']));
			$this->db->update('device_detail', $data_array1);
			return TRUE;
		}else{
		    
			$this->db->insert('device_detail', $data_array2);
			return TRUE;
		}
		return FALSE;
	}
	
	public function userlogoutDevices($devices_key, $data_array){
		//$dd = $this->db->get_where('device_detail', array('devices_key' => $devices_key), 1);
		$dd = $this->db->get_where('device_detail', array('user_id' => $data_array['user_id'],'group_id' => $data_array['group_id']), 1);
		if ($dd->num_rows() == 1) {
			$this->db->where(array('user_id' => $data_array['user_id'],'group_id' => $data_array['group_id']));
			$this->db->update('device_detail', $data_array);
			return TRUE;
		}
		return FALSE;
	}
	
}
