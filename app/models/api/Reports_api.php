<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Reports_api extends CI_Model
{
	public $tables = array();
	protected $_ion_hooks;
	var $limit;
    public function __construct() {
        parent::__construct();
    	$this->load->config('ion_auth', TRUE);
	    $this->limit = 20;
    }
public function login($identity, $password, $remember = FALSE)
    {
        $this->trigger_events('pre_login');

        if (empty($identity) || empty($password)) {
            /*$this->set_error('login_unsuccessful');*/
            return FALSE;
        }
        $this->trigger_events('extra_where');
        $this->load->helper('email');
        $this->identity_column = valid_email($identity) ? 'email' : 'username';
        $query = $this->db->select($this->identity_column . ', username, email, id, password, active, last_login, last_ip_address, avatar, gender, group_id, warehouse_id, biller_id, company_id, view_right, edit_right, allow_discount, show_cost, show_price')
            ->where($this->identity_column, $this->db->escape_str($identity))
            ->limit(1)
            ->get('users');
            /*echo $query;die;*/

       /* if ($this->is_time_locked_out($identity)) {
            //Hash something anyway, just to take up time
            $this->hash_password($password);

            $this->trigger_events('post_login_unsuccessful');
            $this->set_error('login_timeout');

            return FALSE;
        }*/
        	/*echo "<pre>";
        	print_r($query->row());die;*/
        if ($query->num_rows() === 1) {
            $user = $query->row();

            $password = $this->hash_password_db($user->id, $password);
            
            if ($password === TRUE) {

                if ($user->active != 1) {
                	
                    $this->trigger_events('post_login_unsuccessful');
                    $this->set_error('login_unsuccessful_not_active');
                    return FALSE;
                }
                

                $this->set_session($user);

                $this->update_last_login($user->id);
                $this->update_last_login_ip($user->id);
                $ldata = array('user_id' => $user->id, 'ip_address' => $this->input->ip_address(), 'login' => $identity);
		$ldata['group_id'] = $user->group_id;
                $this->db->insert('user_logins', $ldata);
                $this->clear_login_attempts($identity);

                if ($remember && $this->config->item('remember_users', 'ion_auth')) {
                    $this->remember_user($user->id);
                }

                $this->trigger_events(array('post_login', 'post_login_successful'));
                /*$this->set_message('login_successful');*/

                return $user->id;//TRUE;
            }
        }

        //Hash something anyway, just to take up time
        $this->hash_password($password);

        $this->increase_login_attempts($identity);

        $this->trigger_events('post_login_unsuccessful');
        $this->set_error('login_unsuccessful');

        return FALSE;
    }
     public function set_session($user)
    {

        $this->trigger_events('pre_set_session');

        $session_data = array(
            'identity' => $user->{$this->identity_column},
            'username' => $user->username,
            'email' => $user->email,
            'user_id' => $user->id, //everyone likes to overwrite id so we'll use user_id
            'old_last_login' => $user->last_login,
            'last_ip' => $user->last_ip_address,
            'avatar' => $user->avatar,
            'gender' => $user->gender,
            'group_id' => $user->group_id,
            'warehouse_id' => $user->warehouse_id,
            'view_right' => $user->view_right,
            'edit_right' => $user->edit_right,
            'allow_discount' => $user->allow_discount,
            'biller_id' => $user->biller_id,
            'company_id' => $user->company_id,
            'show_cost' => $user->show_cost,
            'show_price' => $user->show_price,
        );

        $this->session->set_userdata($session_data);

        $this->trigger_events('post_set_session');

        return TRUE;
    }
     public function update_last_login($id)
    {
        $this->trigger_events('update_last_login');

        $this->load->helper('date');

        $this->trigger_events('extra_where');

        /*$this->db->update($this->tables['users'], array('last_login' => time()), array('id' => $id));*/
        $this->db->update('users', array('last_login' => time()), array('id' => $id));

        return $this->db->affected_rows() == 1;
    }

    public function update_last_login_ip($id)
    {
        $this->trigger_events('update_last_login_ip');

        $this->trigger_events('extra_where');

        /*$this->db->update($this->tables['users'], array('last_ip_address' => $this->input->ip_address()), array('id' => $id));*/
        $this->db->update('users', array('last_ip_address' => $this->input->ip_address()), array('id' => $id));

        return $this->db->affected_rows() == 1;
    }
    protected function _prepare_ip($ip_address)
    {
        if ($this->db->platform() === 'postgre' || $this->db->platform() === 'sqlsrv' || $this->db->platform() === 'mssql' || $this->db->platform() === 'mysqli' || $this->db->platform() === 'mysql') {
            return $ip_address;
        } else {
            return inet_pton($ip_address);
        }
    }

    public function clear_login_attempts($identity, $expire_period = 86400)
    {
        if ($this->config->item('track_login_attempts', 'ion_auth')) {
            $ip_address = $this->_prepare_ip($this->input->ip_address());

            $this->db->where(array('ip_address' => $ip_address, 'login' => $identity));
            // Purge obsolete login attempts
            $this->db->or_where('time <', time() - $expire_period, FALSE);

            return $this->db->delete('login_attempts');
            /*return $this->db->delete($this->tables['login_attempts']);*/
        }
        return FALSE;
    }
     public function trigger_events($events)
    {
        if (is_array($events) && !empty($events)) {
            foreach ($events as $event) {
                $this->trigger_events($event);
            }
        } else {
            if (isset($this->_ion_hooks->$events) && !empty($this->_ion_hooks->$events)) {
                foreach ($this->_ion_hooks->$events as $name => $hook) {
                    $this->_call_hook($events, $name);
                }
            }
        }
    }
    public function is_time_locked_out($identity)
    {
    	var_dump($this->is_max_login_attempts_exceeded($identity) && $this->get_last_attempt_time($identity) > time() - $this->config->item('lockout_time', 'ion_auth'));die;
        return $this->is_max_login_attempts_exceeded($identity) && $this->get_last_attempt_time($identity) > time() - $this->config->item('lockout_time', 'ion_auth');
    }
    public function is_max_login_attempts_exceeded($identity)
    {
        if ($this->config->item('track_login_attempts', 'ion_auth')) {
            $max_attempts = $this->config->item('maximum_login_attempts', 'ion_auth');
            if ($max_attempts > 0) {
                $attempts = $this->get_attempts_num($identity);
                return $attempts >= $max_attempts;
            }
        }
        return FALSE;
    }
    public function hash_password($password, $salt = false, $use_sha1_override = FALSE)
    {
        if (empty($password)) {
            return FALSE;
        }

        //bcrypt
        if ($use_sha1_override === FALSE && $this->hash_method == 'bcrypt') {
            return $this->bcrypt->hash($password);
        }


        if ($this->store_salt && $salt) {
            return sha1($password . $salt);
        } else {
            $salt = $this->salt();
            return $salt . substr(sha1($salt . $password), 0, -$this->salt_length);
        }
    }

    public function hash_password_db($id, $password, $use_sha1_override = FALSE)
    {
        if (empty($id) || empty($password)) {
        	
            return FALSE;
        }


        $this->trigger_events('extra_where');

        $query = $this->db->select('password, salt')
            ->where('id', $id)
            ->limit(1)
            /*->get($this->tables['users']);*/
            ->get('users');

        $hash_password_db = $query->row();
        
        if ($query->num_rows() !== 1) {
            return FALSE;
        }

 		$this->hash_method = $this->config->item('hash_method', 'ion_auth');

 		/*var_dump($this->hash_method);die;*/
        $this->default_rounds = $this->config->item('default_rounds', 'ion_auth');
        $this->store_salt = $this->config->item('store_salt', 'ion_auth');
        $this->salt_length = $this->config->item('salt_length', 'ion_auth');
     
        // bcrypt

        if ($use_sha1_override === FALSE && $this->hash_method == 'bcrypt') {
        	
            if ($this->bcrypt->verify($password, $hash_password_db->password)) {
            	
                return TRUE;
            }
            
            return FALSE;
        }

        // sha1
        if ($this->store_salt) {
            $db_password = sha1($password . $hash_password_db->salt);
        } else {
            $salt = substr($hash_password_db->password, 0, $this->salt_length);

            $db_password = $salt . substr(sha1($salt . $password), 0, -$this->salt_length);
        }

        if ($db_password == $hash_password_db->password) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function increase_login_attempts($identity)
    {
        if ($this->config->item('track_login_attempts', 'ion_auth')) {
            $ip_address = $this->_prepare_ip($this->input->ip_address());
            return $this->db->insert($this->tables['login_attempts'], array('ip_address' => $ip_address, 'login' => $identity, 'time' => time()));
        }
        return FALSE;
    }
    public function set_error($error)
    {
        $this->errors[] = $error;

        return $error;
    }
    public function hash_code($password)
    {
        return $this->hash_password($password, FALSE, TRUE);
    }

    public function salt()
    {
        return substr(md5(uniqid(rand(), true)), 0, $this->salt_length);
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
    public function PosSettlementReport($start,$end,$warehouse_id,$where_array)
    {  

        if($start){
           $start = $start;
        }
        else{
            $start = date('Y-m-d');
        }
        if($end)
        {
            $end = $end;
        }
        else{
            $end = date('Y-m-d');
        }
    	$s = $this->db->select('default_currency')->get('settings');        
    	if ($s->num_rows() > 0) {				
			$currency_data = $this->site->getCurrencyByID($s->row('default_currency'));
				$defalut_currency = $currency_data->id;
		}
		if ($page == "") {
                $page = 1;
            }

        $limit = $this->limit;
        $offset = ($page - 1) * $limit;

        $where ='';
        if($warehouse_id != 0)
        {
            $where = "AND P.warehouse_id =".$warehouse_id."";
        }
	if(!$where_array['table_whitelisted']){
           $where .= " AND P.table_whitelisted =0";
        }

        $User = "SELECT U.id,U.first_name AS username
            FROM " . $this->db->dbprefix('bils') . " P
            JOIN " . $this->db->dbprefix('users') . " U ON P.created_by = U.id
            JOIN " . $this->db->dbprefix('payments') . " PM ON PM.bill_id = P.id
            WHERE DATE(P.date) BETWEEN '".$start."' AND '".$end."' AND
             P.payment_status ='Completed' ".$where."
            GROUP BY U.id ORDER BY U.username ASC ";

            $u = $this->db->query($User);
        
        if ($u->num_rows() > 0) {
            foreach (($u->result()) as $uow) {

                    $myQuery = "SELECT DATE_FORMAT(P.date, '%Y-%m-%d') as bill_date,DATE_FORMAT(P.date, '%H:%i') as bill_time,WH.name as warehouse,ST.name as bill_type,P.bill_number AS Bill_No,SUM(DISTINCT CASE WHEN ((PM.paid_by = 'cash') AND (SC.currency_id = ".$defalut_currency.")) THEN PM.amount ELSE 0 END) AS Cash,SUM(DISTINCT CASE WHEN ((PM.paid_by = 'cash') AND (SC.currency_id != ".$defalut_currency.")) THEN amount_exchange*currency_rate ELSE 0 END) as For_Ex_usd,SUM(DISTINCT CASE WHEN PM.paid_by = 'CC' THEN PM.amount ELSE 0 END) AS Credit_Card,SUM(DISTINCT CASE WHEN PM.paid_by = 'credit' THEN PM.amount ELSE 0 END) AS credit,SUM(DISTINCT P.paid) AS Bill_amt,SUM(DISTINCT P.balance) AS return_balance,SUM(CASE WHEN ((PM.paid_by = 'cash') AND (SC.currency_id != ".$defalut_currency.")) THEN SC.amount ELSE 0 END) as For_Ex_khr,CASE WHEN T.name  is NOT NULL THEN T.name ELSE ST.name END AS table_name
                    FROM " . $this->db->dbprefix('bils') . " P
                    JOIN " . $this->db->dbprefix('users') . " U ON P.created_by = U.id
                    JOIN " . $this->db->dbprefix('payments') . " PM ON PM.bill_id = P.id
                    JOIN " . $this->db->dbprefix('sale_currency') . " SC ON SC.bil_id = P.id
                    JOIN " . $this->db->dbprefix('warehouses') . " WH ON WH.id = P.warehouse_id
                    JOIN " . $this->db->dbprefix('sales') . " S ON S.id = P.sales_id
                    JOIN " . $this->db->dbprefix('orders') . " O ON O.split_id = S.sales_split_id
                    JOIN " . $this->db->dbprefix('sales_type') . " ST ON ST.id = O.order_type
                    LEFT JOIN " . $this->db->dbprefix('restaurant_tables') . " T ON T.id = S.sales_table_id
                        WHERE DATE(P.date) BETWEEN '".$start."' AND '".$end."' AND
                         P.payment_status ='Completed' AND U.id='".$uow->id."'
                        ".$where." GROUP BY PM.bill_id ORDER BY U.username ASC";
/*echo $myQuery;die;*/
                    $q = $this->db->query($myQuery);
                    if ($q->num_rows() > 0) {
                        foreach (($q->result()) as $row) {
                            $user[$uow->id][] = $row;
                        }
                        $uow->user = $user[$uow->id];
                        $data[] = $uow;
                    }
                }
            return $data;
        }
        return FALSE;
    }
    public function getKotCancelReport($start,$end,$warehouse_id,$page,$table_whitelisted)
	{   
	    $where ='';$where1='';
    
	     if($warehouse_id != 0)
	    {
		$where = "AND P.warehouse_id =".$warehouse_id."";
	    }
	  /*  if(!$where_array['table_whitelisted']){
		$where1 .= " AND O.table_whitelisted =0";
	    }*/
        $where .= " AND O.table_whitelisted =".$table_whitelisted."";
    	/*if ($page == "") {
                $page = 1;
           }*/

        $limit = $this->limit;
        $offset = ($page - 1) * $limit;
        $limits = "";
        
        if ($page) {
            $limits = "LIMIT $offset, $limit";
        }

	    $KotCancel = "SELECT DATE_FORMAT(O.date, '%d-%m-%Y') date,OI.id,R.name AS recipename,OI.order_item_cancel_note,T.name AS table_name,U.username
	    FROM " . $this->db->dbprefix('orders') . " O
	    JOIN " . $this->db->dbprefix('order_items') . " OI ON OI.sale_id = O.id
	    JOIN " . $this->db->dbprefix('recipe') . " R ON R.id = OI.recipe_id 
	    JOIN " . $this->db->dbprefix('kitchen_orders') . " K ON K.id = OI.kitchen_id 
	    JOIN " . $this->db->dbprefix('users') . " U ON U.id = O.created_by  
	    JOIN " . $this->db->dbprefix('restaurant_tables') . " T ON T.id = O.table_id
		WHERE DATE(O.date) BETWEEN '".$start."' AND '".$end."' AND OI.order_item_cancel_status= 1 ".$where1." ".$limits."";    
	    $q = $this->db->query($KotCancel);
	    if ($q->num_rows() > 0) {
		foreach (($q->result()) as $row) {
		    $data[] = $row;
		}
		return $data;
	    }
	    return FALSE;
	}
    public function getKotPendingReport($start,$end,$warehouse_id,$page,$table_whitelisted)
	{   
	    $where ='';$where1='';
	    if($warehouse_id != 0)
	    {
		$where = "AND P.warehouse_id =".$warehouse_id."";
	    }
        $where .= " AND O.table_whitelisted =".$table_whitelisted."";
	   /* if(!$where_array['table_whitelisted']){
		$where1 .= " AND O.table_whitelisted =0";
	    }*/

    	/*if ($page == "") {
                $page = 1;
           }*/

        $limit = $this->limit;
        $offset = ($page - 1) * $limit;

        $limits = "";
        if ($page) {
            $limits = "LIMIT $offset, $limit";
        }


	    $KotPending = "SELECT DATE_FORMAT(O.date, '%d-%m-%Y') AS Orderdate,O.id,U.username,OI.quantity,R.name AS recipename,T.name AS table_name,OI.subtotal
	    FROM " . $this->db->dbprefix('orders') . "  O
	    JOIN " . $this->db->dbprefix('order_items') . " OI ON OI.sale_id = O.id
	    JOIN " . $this->db->dbprefix('recipe') . " R ON OI.recipe_id = R.id
	    LEFT JOIN " . $this->db->dbprefix('restaurant_tables') . " T ON T.id = O.table_id
	    JOIN " . $this->db->dbprefix('kitchen_orders') . " K ON K.sale_id = O.id
	    JOIN " . $this->db->dbprefix('users') . " U ON O.created_by = U.id
	    WHERE DATE(O.date) BETWEEN '".$start."' AND '".$end."' AND  O.split_id NOT IN (SELECT sales_split_id FROM " . $this->db->dbprefix('sales') . " ) AND OI.order_item_cancel_status != 1 ".$where1." AND O.order_type != 4";
	    
	    $q = $this->db->query($KotPending);
	    if ($q->num_rows() > 0) {
		foreach (($q->result()) as $row) {
		    $data[] = $row;
		}
		return $data;
	    }
	    return FALSE;
	}    
    public function getKotDetailsReport($start,$end,$warehouse_id,$page,$table_whitelisted)
	{  
	    $where ='';
	    if($warehouse_id != 0)
	    {
		$where = "AND P.warehouse_id =".$warehouse_id."";
	    }
        /*if(!$where_array['table_whitelisted']){*/
            $where .= " AND P.table_whitelisted =".$table_whitelisted."";
        /*}*/

    	/*if ($page == "") {
                $page = 1;
           }*/

        if($start){
           $start = $start;
        }
        else{
            $start = date('Y-m-d');
        }
        if($end)
        {
            $end = $end;
        }
        else{
            $end = date('Y-m-d');
        }
           
        $limit = $this->limit;
        $offset = ($page - 1) * $limit;

        $limits = "";
        if ($page) {
            $limits = "LIMIT $offset, $limit";
        }

	    $User = "SELECT U.id,U.first_name as username
		FROM " . $this->db->dbprefix('bils') . "  P
		JOIN " . $this->db->dbprefix('users') . " U ON P.created_by = U.id
		JOIN " . $this->db->dbprefix('payments') . " PM ON PM.bill_id = P.id
		WHERE DATE(P.date) BETWEEN '".$start."' AND '".$end."' AND
		 P.payment_status ='Completed' ".$where."
		GROUP BY U.id ORDER BY U.username ASC ".$limits."";
        
        
		$u = $this->db->query($User);
	    
	    if ($u->num_rows() > 0) {
		foreach (($u->result()) as $uow) {
    
			$KotQuery = "SELECT K.id AS kitchenno,DATE_FORMAT(O.date, '%d-%m-%Y') AS kot_date,DATE_FORMAT(O.date, '%H:%i') as kot_time,T.name AS table_name,U.first_name as username,OU.first_name AS steward,R.name AS item,BI.quantity,P.bill_number AS Bill_No, (BI.subtotal-BI.item_discount-BI.off_discount-BI.input_discount+CASE WHEN (BI.tax_type = 1) THEN BI.tax ELSE 0 END) as Bill_amt,DATE_FORMAT(O.date, '%H:%i') as kot_time,(CASE WHEN (BI.tax_type = 1) THEN tax ELSE 0 END) as tax,TY.name AS order_type
			FROM " . $this->db->dbprefix('bils') . " P
			JOIN " . $this->db->dbprefix('sales') . " S ON S.id = P.sales_id
			JOIN " . $this->db->dbprefix('orders') . " O ON O.split_id = S.sales_split_id
			JOIN  " . $this->db->dbprefix('sales_type') . " TY ON TY.id = O.order_type
			JOIN " . $this->db->dbprefix('bil_items') . " BI ON BI.bil_id = P.id
			JOIN " . $this->db->dbprefix('recipe') . " R ON BI.recipe_id = R.id
			LEFT JOIN " . $this->db->dbprefix('restaurant_tables') . " T ON T.id = O.table_id
			JOIN " . $this->db->dbprefix('kitchen_orders') . " K ON K.sale_id = O.id
			JOIN " . $this->db->dbprefix('users') . " U ON U.id = P.created_by
			JOIN " . $this->db->dbprefix('users') . " OU ON OU.id = O.created_by
			WHERE DATE(P.date) BETWEEN '".$start."' AND '".$end."' AND
			P.payment_status ='Completed' ".$where." AND U.id='".$uow->id."' GROUP BY BI.id ORDER BY U.username ASC ";
             
			$q = $this->db->query($KotQuery);
			if ($q->num_rows() > 0) {
			    foreach (($q->result()) as $row) {
				$user[$uow->id][] = $row;
			    }
			    $uow->user = $user[$uow->id];
			    $data[] = $uow;
			}
		    }
            
		return $data;
	    }
	    return FALSE;
	}     
    public function getCashierReport($start,$end,$user,$page,$where_array)
	{
    	/*if ($page == "") {
                $page = 1;
           }*/
	$where='';
	if(!$where_array['table_whitelisted']){
           $where .= " AND P.table_whitelisted =0";
        }
       $limit = $this->limit;
       $offset = ($page - 1) * $limit;

       $limits = "";
        if ($page) {
            $limits = "LIMIT $offset, $limit";
        }

        /*$offset = ($page - 1) * 10;
        $limit = $this->limit;*/

	    $user_report = "SELECT DATE_FORMAT(P.date, '%d-%m-%Y') AS billdate,U.username, P.bill_number,SUM(total-total_discount+CASE WHEN (tax_type= 1) THEN total_tax ELSE 0 END) AS total
	    FROM " . $this->db->dbprefix('bils') . "  P
	    LEFT JOIN " . $this->db->dbprefix('users') . " U ON U.id = P.created_by
		WHERE DATE(P.date) BETWEEN '".$start."' AND '".$end."' AND
		 P.payment_status ='Completed' AND P.created_by =".$user. $where." GROUP BY P.id ".$limits ."";
		 
	    $q = $this->db->query($user_report);
	    if ($q->num_rows() > 0) {
		foreach (($q->result()) as $row) {
		    $data[] = $row;
		}
		return $data;
	    }
	    return FALSE;
	}   
    public function getBill_no($start,$end,$warehouse_id)
	{   
	    $where ='';
	    if($warehouse_id != 0)
	    {
		$where = "AND P.warehouse_id =".$warehouse_id."";
	    }
    
	   $BillNo = "SELECT P.id,P.bill_number
	     FROM " . $this->db->dbprefix('bils') . " AS P
		WHERE DATE(P.date) BETWEEN '".$start."' AND '".$end."' AND
		 P.payment_status ='Completed' ".$where." ";
    
	    $q = $this->db->query($BillNo);
	    if ($q->num_rows() > 0) {
		foreach (($q->result()) as $row) {
		    $data[] = $row;
		}
		return $data;
	    }
	    return FALSE;
	} 
    public function getBillDetailsReport($start,$end,$bill_no,$warehouse_id,$page,$where_array)
	{  
	    $where1 ='';
	    if($warehouse_id != 0)
	    {
	       $where1 = "AND P.warehouse_id =".$warehouse_id."";
	    }
	    if($bill_no)
	    {
	       $where = "AND P.id =".$bill_no."";
	    }
	    else{
		$where = "";
	    }
if(!$where_array['table_whitelisted']){
           $where1 .= " AND P.table_whitelisted =0";
        }
    	/*if ($page == "") {
                $page = 1;
           }*/

        $limit = $this->limit;
        $offset = ($page - 1) * $limit;

        $limits = "";
        if ($page) {
            $limits = "LIMIT $offset, $limit";
        }

	     $bill = "SELECT P.id,P.bill_number
		FROM ". $this->db->dbprefix('bils') ." AS P
		 JOIN ". $this->db->dbprefix('users') ." AS U ON P.created_by = U.id
		 JOIN ". $this->db->dbprefix('payments') ." AS PM ON PM.bill_id = P.id
		WHERE DATE(P.date) BETWEEN '".$start."' AND '".$end."' AND
		 P.payment_status ='Completed' ".$where." ".$where1."
		GROUP BY P.id ORDER BY P.id ASC ".$limits."";
		
		$u = $this->db->query($bill);
	    if ($u->num_rows() > 0) {
		foreach (($u->result()) as $uow) {
    
		$Billdetails = "SELECT W.name AS branch,K.id AS kitchenno,DATE_FORMAT(O.date, '%d-%m-%Y') AS kot_date,T.name AS table_name, U.username,OU.username AS steward,R.name AS item,BI.quantity,(BI.subtotal-BI.item_discount-BI.off_discount-BI.input_discount+CASE WHEN (BI.tax_type = 1) THEN BI.tax ELSE 0 END) as Bill_amt,DATE_FORMAT(P.date, '%H:%i') as bill_time,TY.name AS order_type,P.id as Bill_id
			FROM ".$this->db->dbprefix('bils')." AS P
			JOIN ". $this->db->dbprefix('sales') ." AS S ON S.id = P.sales_id
			JOIN ". $this->db->dbprefix('orders') ." AS O ON O.split_id = S.sales_split_id
			JOIN ". $this->db->dbprefix('sales_type') ." AS TY ON TY.id = O.order_type
			JOIN ". $this->db->dbprefix('bil_items') ." AS BI ON BI.bil_id = P.id
			JOIN ". $this->db->dbprefix('recipe') ." AS R ON BI.recipe_id = R.id
			LEFT JOIN ". $this->db->dbprefix('restaurant_tables') ." AS T ON T.id = O.table_id
			JOIN ". $this->db->dbprefix('kitchen_orders') ." AS K ON K.sale_id = O.id
			JOIN ". $this->db->dbprefix('users') ." AS U ON U.id = P.created_by
			JOIN ". $this->db->dbprefix('users') ." AS OU ON OU.id = O.created_by
            LEFT JOIN " . $this->db->dbprefix('warehouses') . " W ON W.id = P.warehouse_id
			WHERE DATE(P.date) BETWEEN '".$start."' AND '".$end."' AND
			P.payment_status ='Completed' AND P.id='".$uow->id."' GROUP BY BI.id";
    
			$q = $this->db->query($Billdetails);
			if ($q->num_rows() > 0) {
			    foreach (($q->result()) as $row) {
				$user[$uow->id][] = $row;
			    }
			    $uow->user = $user[$uow->id];
			    $data[] = $uow;
			}
		}
		return $data;
	    }
	    return FALSE;
	}  
    public function getCoverAnalysisReport($start,$end,$warehouse_id,$page)
	{
	    $where ='';
	    if($warehouse_id != 0)
	    {
	       $where = "AND P.warehouse_id =".$warehouse_id."";
	    }

        if($start){
           $start = $start;
        }
        else{
            $start = date('Y-m-d');
        }
        if($end)
        {
            $end = $end;
        }
        else{
            $end = date('Y-m-d');
        }

    	/*if ($page == "") {
                $page = 1;
           }*/

        $limit = $this->limit;
        $offset = ($page - 1) * $limit;

        $limits = "";
        if ($page) {
            $limits = "LIMIT $offset, $limit";
        }

	    $cover = "SELECT W.name AS warehouse,SUM(total-total_discount+CASE WHEN (tax_type= 1) THEN total_tax ELSE 0 END) grand_total,SUM(P.round_total) AS round_total,COUNT(P.id) AS tot_bills
	    FROM " . $this->db->dbprefix('bils') . " AS P
        LEFT JOIN  " . $this->db->dbprefix('warehouses') . " AS W ON W.id=P.warehouse_id
		WHERE DATE(P.date) BETWEEN '".$start."' AND '".$end."' AND
		 P.payment_status ='Completed' ".$where." ".$limits."";
	    $q = $this->db->query($cover);
	    if ($q->num_rows() > 0) {
		foreach (($q->result()) as $row) {
		    $data[] = $row;
		}
		return $data;
	    }
	    return FALSE;
	}  
    public function getTaxReport($start,$end,$warehouse_id,$page,$where_array)
	{
	    $where ='';
	    if($warehouse_id != 0)
	    {
		$where = "AND P.warehouse_id =".$warehouse_id."";
	    }
	    if(!$where_array['table_whitelisted']){
           $where .= " OR P.table_whitelisted =0";
        }

        if($start){
           $start = $start;
        }
        else{
            $start = date('Y-m-d');
        }
        if($end)
        {
            $end = $end;
        }
        else{
            $end = date('Y-m-d');
        }

        $limit = $this->limit;
        $offset = ($page - 1) * $limit;

         $limits = "";
        if ($page) {
            $limits = "LIMIT $offset, $limit";
        }

	    $tax_report = "SELECT W.name branch,DATE_FORMAT(P.date, '%d-%m-%Y') AS tax_date,P.bill_number,T.name AS Taxname,SUM(P.total-P.total_discount-CASE WHEN (tax_type= 0) THEN total_tax ELSE 0 END) as bill_value,P.total_tax,SUM(total-total_discount+CASE WHEN (tax_type= 1) THEN total_tax ELSE 0 END) as total
	    FROM " . $this->db->dbprefix('bils') . "  P
	    JOIN " . $this->db->dbprefix('tax_rates') . " T ON T.id = P.tax_id 
        LEFT JOIN " . $this->db->dbprefix('warehouses') . " W ON W.id = P.warehouse_id
		WHERE DATE(P.date) BETWEEN '".$start."' AND '".$end."' AND
		 P.payment_status ='Completed' ".$where." GROUP BY P.id ".$limits."";

	    $q = $this->db->query($tax_report);
	    if ($q->num_rows() > 0) {
		foreach (($q->result()) as $row) {
		    $data[] = $row;
		}
		return $data;
	    }
	    return 0;
	}  
    public function getDiscountDetailsReport($start,$end,$warehouse_id,$page)
	{
	    $where ='';
	    if($warehouse_id != 0)
	    {
		$where = "AND P.warehouse_id =".$warehouse_id."";
	    }

        if($start){
           $start = $start;
        }
        else{
            $start = date('Y-m-d');
        }
        if($end)
        {
            $end = $end;
        }
        else{
            $end = date('Y-m-d');
        }

        $limit = $this->limit;
        $offset = ($page - 1) * $limit;

        $limits = "";
        if ($page) {
            $limits = "LIMIT $offset, $limit";
        }

	    $dis_details = "SELECT DATE_FORMAT(P.date, '%d-%m-%Y') AS dis_date,P.total_discount,P.bill_number,UO.first_name AS username,U.first_name AS cashier,SUM(DISTINCT P.total-P.total_discount+CASE WHEN (P.tax_type= 1) THEN P.total_tax ELSE 0 END) as grand_total,W.name AS branch 
	    FROM " . $this->db->dbprefix('bils') . " P
	    JOIN " . $this->db->dbprefix('users') . " U ON  U.id = P.created_by
	    JOIN " . $this->db->dbprefix('sales') . " S ON S.id = P.sales_id
	    JOIN " . $this->db->dbprefix('orders') . " O ON O.split_id = S.sales_split_id
	    JOIN " . $this->db->dbprefix('users') . " UO ON UO.id = O.created_by
        LEFT JOIN " . $this->db->dbprefix('warehouses') . " W ON W.id = P.warehouse_id
		WHERE DATE(P.date) BETWEEN '".$start."' AND '".$end."' AND
		 P.payment_status ='Completed' ".$where." GROUP BY P.id ".$limits."";
	    $q = $this->db->query($dis_details);
	    if ($q->num_rows() > 0) {
		foreach (($q->result()) as $row) {
		    $data[] = $row;
		}
		return $data;
	    }
	    return FALSE;
	}   
    public function getDiscountsummaryReport($start,$end,$warehouse_id,$page)
	{
	    $where ='';
	    if($warehouse_id != 0)
	    {
		$where = "AND P.warehouse_id =".$warehouse_id."";
	    }

        $limit = $this->limit;
        $offset = ($page - 1) * $limit;

        $limits = "";
        if ($page) {
            $limits = "LIMIT $offset, $limit";
        }

        if($start){
           $start = $start;
        }
        else{
            $start = date('Y-m-d');
        }
        if($end)
        {
            $end = $end;
        }
        else{
            $end = date('Y-m-d');
        }

	    $dis_report = "SELECT DATE_FORMAT(P.date, '%d-%m-%Y') AS dis_date,SUM(P.total_discount) AS total_discount,W.name branch 
	    FROM " . $this->db->dbprefix('bils') . "  P
        LEFT JOIN " . $this->db->dbprefix('warehouses') . " W ON W.id = P.warehouse_id  
		WHERE DATE(P.date) BETWEEN '".$start."' AND '".$end."' AND
		 P.payment_status ='Completed' ".$where."
		 GROUP BY DATE(P.date)  ".$limits."";
		 
	    $q = $this->db->query($dis_report);
	    if ($q->num_rows() > 0) {
		foreach (($q->result()) as $row) {
		    $data[] = $row;
		}
		return $data;
	    }
	    return FALSE;
	}  
      public function getItemSaleReports($start,$end,$warehouse_id,$page,$where_array){    
        /*if ($page == "") {
                $page = 1;
           }*/
       /* $limit = $this->limit;
        $offset = ($page - 1) * $limit;*/

        $limit = $this->limit;
        $offset = ($page - 1) * $limit;

        $limits = "";
        if ($page) {
            $limits = "LIMIT $offset, $limit";
        }

        if($start){
           $start = $start;
        }
        else{
            $start = date('Y-m-d');
        }
        if($end)
        {
            $end = $end;
        }
        else{
            $end = date('Y-m-d');
        }
        $where = '';                
        if(!$where_array['table_whitelisted']){
        $where .= " AND B.table_whitelisted =0";
        }

     /*$this->db->select("recipe_categories.id AS cate_id,recipe_categories.name as category, 'split_order'")
        ->join('recipe', 'recipe.category_id = recipe_categories.id')
        ->join('bil_items', 'bil_items.recipe_id = recipe.id')
        ->join('bils', 'bils.id = bil_items.bil_id')
        ->where('recipe_categories.parent_id', NULL)
        ->where('bils.payment_status', 'Completed')
        ->or_where('recipe_categories.parent_id',0);
        if($warehouse_id != 0){
        $this->db->where('bil_items.warehouse_id', $warehouse_id);    
        }
        if(!$where_array['table_whitelisted']){
        $this->db->where('bils.table_whitelisted',0);
        }
        $this->db->group_by('recipe_categories.id');
        if ($page) {
           $this->db->limit($limit,$offset);
        }
        $t = $this->db->get('recipe_categories');      
        
        $where = '';                
            if(!$where_array['table_whitelisted']){
            $where .= " AND B.table_whitelisted =0";
            }*/

         $Saleitem = "SELECT RC.id AS cate_id,RC.name as category,'split_order'
        FROM " . $this->db->dbprefix('recipe_categories') . " RC 
        JOIN " . $this->db->dbprefix('recipe') . " R  ON R.category_id = RC.id 
        JOIN " . $this->db->dbprefix('bil_items') . " BI  ON BI.recipe_id = R.id
        JOIN " . $this->db->dbprefix('bils') . " B ON B.id = BI.bil_id 
        WHERE B.payment_status ='Completed' AND (RC.parent_id = NULL OR RC.parent_id = 0 ) AND DATE(B.date) BETWEEN '".$start."' AND '".$end."' ".$where." ".$limits." GROUP BY RC.id ";

        /* SELECT `srampos_recipe_categories`.`id` AS `cate_id`, `srampos_recipe_categories`.`name` as `category`, 'split_order'
        FROM `srampos_recipe_categories`
        JOIN `srampos_recipe` ON `srampos_recipe`.`category_id` = `srampos_recipe_categories`.`id`
        JOIN `srampos_bil_items` ON `srampos_bil_items`.`recipe_id` = `srampos_recipe`.`id`
        JOIN `srampos_bils` ON `srampos_bils`.`id` = `srampos_bil_items`.`bil_id`
        WHERE `srampos_recipe_categories`.`parent_id` IS NULL
        AND `srampos_bils`.`payment_status` = 'Completed'
        OR `srampos_recipe_categories`.`parent_id` =0
        AND `srampos_bils`.`table_whitelisted` =0
        GROUP BY `srampos_recipe_categories`.`id`*/
        /*print_r($this->db->last_query());die;*/

        $t = $this->db->query($Saleitem);
        if ($t->num_rows() > 0) {
        
        foreach ($t->result() as $row) {
            $this->db->select("recipe_categories.id AS sub_id,recipe_categories.name AS sub_category, 'order'")
            ->join('recipe', 'recipe.subcategory_id = recipe_categories.id')
            ->join('bil_items', 'bil_items.recipe_id = recipe.id')
            ->join('bils', 'bils.id = bil_items.bil_id')
            ->where('bils.payment_status', 'Completed')
            ->where('recipe.category_id', $row->cate_id);
            if(!$where_array['table_whitelisted']){
            $this->db->where('bils.table_whitelisted',0);
            }
            $this->db->group_by('recipe.subcategory_id');
               
        $s = $this->db->get('recipe_categories');
    
        if ($s->num_rows() > 0) {
        foreach ($s->result() as $sow) {
            
            $myQuery = "SELECT name,quantity,rate,quantity*rate AS amount,warehouse,discount,tax,sum(subtotal-discount+tax1) as subtotal from (SELECT R.id,R.name,SUM(BI.quantity) AS quantity,SUM(BI.subtotal-CASE WHEN (BI.tax_type= 0) THEN BI.tax ELSE 0 END) AS subtotal,SUM(BI.subtotal+BI.item_discount+BI.off_discount+BI.input_discount-CASE WHEN (BI.tax_type = 1) THEN tax ELSE 0 END) AS rate123,R.price AS rate,WH.name as warehouse,SUM(item_discount+off_discount+input_discount) AS discount,SUM(CASE WHEN (BI.tax_type= 1) THEN tax ELSE 0 END) AS tax,SUM(BI.tax) AS tax1 FROM srampos_bil_items BI 
            JOIN srampos_recipe R ON R.id = BI.recipe_id 
            JOIN srampos_bils B ON B.id = BI.bil_id
            JOIN " . $this->db->dbprefix('warehouses') . " WH ON WH.id = BI.warehouse_id
             WHERE DATE(B.date) BETWEEN '".$start."' AND '".$end."' AND
            R.subcategory_id =".$sow->sub_id." AND B.payment_status ='Completed'" .$where." GROUP BY R.id )as X GROUP BY id";

            $o = $this->db->query($myQuery);    
            $split[$row->cate_id][] = $sow;
            if ($o->num_rows() > 0) {                                    
                foreach($o->result() as $oow){
                $order[$sow->sub_id][] = $oow;
                }
            }
            $sow->order = $order[$sow->sub_id];                   
        }                    
              $row->split_order = $split[$row->cate_id];
        }else{
            $row->split_order = array();
        }                
            $data[] = $row;
    
        }            
        return $data;
        }        
        return FALSE;   
    }  
    public function getPopularReports($start,$end,$warehouse_id,$page,$where_array){
    
    	if($start){
           $start = $start;
        }
        else{
            $start = date('Y-m-d');
        }
        if($end)
        {
            $end = $end;
        }
        else{
            $end = date('Y-m-d');
        } 
           
        $limit = $this->limit;
        $offset = ($page - 1) * $limit;
       $limits = "";
        if ($page) {
            $limits = "LIMIT $offset, $limit";
        }

        $where ='';if(!$where_array['table_whitelisted']){
             $where .= " AND B.table_whitelisted =0";
        }  
        if($warehouse_id != 0){
                $where .=' AND B.warehouse_id='.$warehouse_id;    
        }

       

       /*$this->db->select("recipe_categories.id AS cate_id,recipe_categories.name as category,'split_order'")
	    ->join('recipe', 'recipe.category_id = recipe_categories.id')
	    ->join('bil_items', 'bil_items.recipe_id = recipe.id')
	    ->join('bils', 'bils.id = bil_items.bil_id')
	    ->where('recipe_categories.parent_id', NULL)
	    ->where('bils.payment_status', 'Completed')
        ->where('bils.date >=', $start)->where('bils.date <=', $end)
	    ->or_where('recipe_categories.parent_id',0);
	    if($warehouse_id != 0){
		$this->db->where('bils.warehouse_id', $warehouse_id);    
	    }
	    if(!$where_array['table_whitelisted']){
		$this->db->where('bils.table_whitelisted',0);
	    }
	    $this->db->group_by('recipe_categories.id');
	    $this->db->order_by('SUM(' . $this->db->dbprefix('bil_items') . '.quantity)', 'DESC');
	    if ($page) {
           $this->db->limit($limit,$offset);
        }
	    $t = $this->db->get('recipe_categories');      */
	    
         $popQuery = "SELECT RC.id AS cate_id,RC.name as category,'split_order' FROM " . $this->db->dbprefix('recipe_categories') . " RC JOIN " . $this->db->dbprefix('recipe') . " R  ON R.category_id = RC.id 
        JOIN " . $this->db->dbprefix('bil_items') . " BI  ON BI.recipe_id = R.id
        JOIN " . $this->db->dbprefix('bils') . " B ON B.id = BI.bil_id WHERE B.payment_status ='Completed' AND (RC.parent_id = NULL OR RC.parent_id = 0 ) AND DATE(B.date) BETWEEN '".$start."' AND '".$end."' ".$where." ".$limits." GROUP BY RC.id ORDER BY SUM(BI.quantity) DESC";
        
        $t = $this->db->query($popQuery);
	    if ($t->num_rows() > 0) {
		
		foreach ($t->result() as $row) {
			$this->db->select("recipe_categories.id AS sub_id,recipe_categories.name AS sub_category, 'order'")
			->join('recipe', 'recipe.subcategory_id = recipe_categories.id')
			->join('bil_items', 'bil_items.recipe_id = recipe.id')
			->join('bils', 'bils.id = bil_items.bil_id')
			->where('bils.payment_status', 'Completed')
			->where('recipe.category_id', $row->cate_id);
			if($warehouse_id != 0){
			    $this->db->where('bils.warehouse_id', $warehouse_id);
			}
			if(!$where_array['table_whitelisted']){
			    $this->db->where('bils.table_whitelisted',0);
			}
			$this->db->group_by('recipe.subcategory_id');
			$this->db->order_by('SUM(' . $this->db->dbprefix('bil_items') . '.quantity)', 'DESC');
			$s = $this->db->get('recipe_categories');
		    if ($s->num_rows() > 0) {
			    
			    foreach ($s->result() as $sow) {
$where='';if(!$where_array['table_whitelisted']){
           $where .= " AND B.table_whitelisted =0";
        }
                    $myQuery = "SELECT name,quantity,SUM(subtotal) AS rate,discount,tax,sum(subtotal-discount+tax) as subtotal from (SELECT R.id,R.name,SUM(BI.quantity) AS quantity,SUM(BI.subtotal-CASE WHEN (BI.tax_type= 0) THEN BI.tax ELSE 0 END) AS subtotal,SUM(BI.subtotal+BI.item_discount+BI.off_discount+BI.input_discount-CASE WHEN (BI.tax_type = 1) THEN tax ELSE 0 END) AS rate,SUM(item_discount+off_discount+input_discount) AS discount,SUM(CASE WHEN (BI.tax_type= 1) THEN tax ELSE 0 END) AS tax FROM srampos_bil_items BI JOIN srampos_recipe R ON R.id = BI.recipe_id JOIN srampos_bils B ON B.id = BI.bil_id WHERE DATE(B.date) BETWEEN '".$start."' AND '".$end."' AND
            R.subcategory_id =".$sow->sub_id." AND B.payment_status ='Completed'".$where." GROUP BY R.id )as X GROUP BY id ORDER BY quantity DESC ";

				
				/*$myQuery = "SELECT R.name,SUM(BI.item_discount) AS item_discount,SUM(BI.off_discount) AS off_discount,SUM(BI.input_discount) AS input_discount,SUM(BI.quantity) AS quantity,SUM(BI.tax) AS tax,SUM(BI.subtotal-CASE WHEN (BI.tax_type= 0) THEN BI.tax ELSE 0 END) as subtotal
				    FROM " . $this->db->dbprefix('bil_items') . " BI
				    JOIN " . $this->db->dbprefix('recipe') . " R ON R.id = BI.recipe_id
				    JOIN " . $this->db->dbprefix('bils') . " B ON B.id = BI.bil_id
				    WHERE DATE(B.date) BETWEEN '".$start."' AND '".$end."' AND
				    R.subcategory_id =".$sow->sub_id." AND B.payment_status='Completed'
				    GROUP BY R.id ORDER BY SUM(BI.quantity) DESC ";*/
				    
				    $o = $this->db->query($myQuery);
		    
				    $split[$row->cate_id][] = $sow;
				    if ($o->num_rows() > 0) {                                    
					foreach($o->result() as $oow){
					    $order[$sow->sub_id][] = $oow;
					}
				    }
				    $sow->order = $order[$sow->sub_id];                   
			    }                    
			$row->split_order = $split[$row->cate_id];
		    }else{
			$row->split_order = array();
		    }                
		    $data[] = $row;
    
		}            
		return $data;
	    }        
	    return FALSE;
	}   
    public function getNonPopularReports($start,$end,$warehouse_id,$page,$where_array)
    {
    	/*if ($page == "") {
                $page = 1;
           }*/
        if($start){
           $start = $start;
        }
        else{
            $start = date('Y-m-d');
        }
        if($end)
        {
            $end = $end;
        }
        else{
            $end = date('Y-m-d');
        } 
           
        $limit = $this->limit;
        $offset = ($page - 1) * $limit;
       $limits = "";
        if ($page) {
            $limits = "LIMIT $offset, $limit";
        }

        $where ='';if(!$where_array['table_whitelisted']){
             $where .= " AND B.table_whitelisted =0";
        }  
        if($warehouse_id != 0){
                $where .=' AND B.warehouse_id='.$warehouse_id;    
        }

        $NonpopQuery = "SELECT RC.id AS cate_id,RC.name as category,'split_order' FROM " . $this->db->dbprefix('recipe_categories') . " RC JOIN " . $this->db->dbprefix('recipe') . " R  ON R.category_id = RC.id 
        JOIN " . $this->db->dbprefix('bil_items') . " BI  ON BI.recipe_id = R.id
        JOIN " . $this->db->dbprefix('bils') . " B ON B.id = BI.bil_id WHERE B.payment_status ='Completed' AND (RC.parent_id = NULL OR RC.parent_id = 0 ) AND DATE(B.date) BETWEEN '".$start."' AND '".$end."' ".$where." ".$limits." GROUP BY RC.id ORDER BY SUM(BI.quantity) ASC";
        
        $t = $this->db->query($NonpopQuery);

   /* $this->db->select("recipe_categories.id AS cate_id,recipe_categories.name as category, 'split_order'")
	    ->join('recipe', 'recipe.category_id = recipe_categories.id')
	    ->join('bil_items', 'bil_items.recipe_id = recipe.id')
	    ->join('bils', 'bils.id = bil_items.bil_id')
	    ->where('bils.payment_status', 'Completed')
	    ->where('recipe_categories.parent_id', NULL)
        ->where('bils.date >=', $start)->where('bils.date <=', $end)
	    ->or_where('recipe_categories.parent_id',0);
	     if($warehouse_id != 0){
		$this->db->where('bils.warehouse_id', $warehouse_id);    
	    }
	    if(!$where_array['table_whitelisted']){
			    $this->db->where('bils.table_whitelisted',0);
			}
	    $this->db->group_by('recipe_categories.id');
	    $this->db->order_by('SUM(' . $this->db->dbprefix('bil_items') . '.quantity)', 'ASC');
        if ($page) {
           $this->db->limit($limit,$offset);
        }
	    
	    $t = $this->db->get('recipe_categories'); */     
	    
	    if ($t->num_rows() > 0) {
		
		foreach ($t->result() as $row) {
		     $this->db->select("recipe_categories.id AS sub_id,recipe_categories.name AS sub_category, 'order'")
			->join('recipe', 'recipe.subcategory_id = recipe_categories.id')
			->join('bil_items', 'bil_items.recipe_id = recipe.id')
			->join('bils', 'bils.id = bil_items.bil_id')
			->where('bils.payment_status', 'Completed')
			->where('recipe.category_id', $row->cate_id);
			if($warehouse_id != 0){
			    $this->db->where('bils.warehouse_id', $warehouse_id);
			}
			if(!$where_array['table_whitelisted']){
			    $this->db->where('bils.table_whitelisted',0);
			}
			$this->db->group_by('recipe.subcategory_id');
			$this->db->order_by('SUM(' . $this->db->dbprefix('bil_items') . '.quantity)', 'ASC');
			$s = $this->db->get('recipe_categories');
		    if ($s->num_rows() > 0) {
			    
			foreach ($s->result() as $sow) {
				$where='';if(!$where_array['table_whitelisted']){
                $where .= " AND B.table_whitelisted =0";
            }    
                    $myQuery = "SELECT name,quantity,SUM(subtotal) AS rate,discount,tax,sum(subtotal-discount+tax) as subtotal from (SELECT R.id,R.name,SUM(BI.quantity) AS quantity,SUM(BI.subtotal-CASE WHEN (BI.tax_type= 0) THEN BI.tax ELSE 0 END) AS subtotal,SUM(BI.subtotal+BI.item_discount+BI.off_discount+BI.input_discount-CASE WHEN (BI.tax_type = 1) THEN tax ELSE 0 END) AS rate,SUM(item_discount+off_discount+input_discount) AS discount,SUM(CASE WHEN (BI.tax_type= 1) THEN tax ELSE 0 END) AS tax FROM srampos_bil_items BI JOIN srampos_recipe R ON R.id = BI.recipe_id JOIN srampos_bils B ON B.id = BI.bil_id WHERE DATE(B.date) BETWEEN '".$start."' AND '".$end."' AND
            R.subcategory_id =".$sow->sub_id." AND B.payment_status ='Completed'".$where." GROUP BY R.id )as X GROUP BY id ORDER BY quantity ASC ";

				    /*$myQuery = "SELECT R.name,SUM(BI.item_discount) AS item_discount,SUM(BI.off_discount) AS off_discount,SUM(BI.input_discount) AS input_discount,SUM(BI.quantity) AS quantity,SUM(BI.tax) AS tax,SUM(BI.subtotal-CASE WHEN (BI.tax_type= 0) THEN BI.tax ELSE 0 END) as subtotal
				    FROM " . $this->db->dbprefix('bil_items') . " BI
				    JOIN " . $this->db->dbprefix('recipe') . " R ON R.id = BI.recipe_id
				    JOIN " . $this->db->dbprefix('bils') . " B ON B.id = BI.bil_id
				    WHERE DATE(B.date) BETWEEN '".$start."' AND '".$end."' AND
				    R.subcategory_id =".$sow->sub_id." AND B.payment_status='Completed'
				    GROUP BY R.id ORDER BY SUM(BI.quantity) ASC";*/
				    
				    $o = $this->db->query($myQuery);
						   
				    $split[$row->cate_id][] = $sow;
				    if ($o->num_rows() > 0) {                                    
					foreach($o->result() as $oow){
					    $order[$sow->sub_id][] = $oow;
					}
				    }
				    $sow->order = $order[$sow->sub_id];                   
			    }                    
			$row->split_order = $split[$row->cate_id];
		    }else{
			$row->split_order = array();
		    }                
		    $data[] = $row;
    
		}            
		return $data;
	    }        
	    return FALSE;
	}
    public function payments_report($warehouse_id,$where_array,$page) {

    	if ($page == "") {
                $page = 1;
           }

        $limit = $this->limit;
        $offset = ($page - 1) * $limit;

	    $this->db
	    ->select("DATE_FORMAT({$this->db->dbprefix('payments')}.date, '%Y-%m-%d %T') as date, bill_number, " . $this->db->dbprefix('bils') . ".reference_no as sale_ref, CONCAT('cash - ',SUM(DISTINCT CASE  WHEN ((srampos_payments.paid_by = 'cash') AND (srampos_sale_currency.currency_id=2) AND (srampos_sale_currency.amount!='')) THEN srampos_payments.amount 
    WHEN ((srampos_payments.paid_by = 'cash' AND srampos_sale_currency.currency_id=1 AND srampos_sale_currency.amount!='')) THEN (srampos_sale_currency.amount*srampos_sale_currency.currency_rate) ELSE 0 END),' | CC - ',SUM(DISTINCT CASE
    WHEN " . $this->db->dbprefix('payments') . ".paid_by = 'CC'  THEN {$this->db->dbprefix('payments')}.amount
    
    ELSE 0 END),'| credit - ',SUM(DISTINCT CASE  WHEN ((srampos_payments.paid_by = 'credit') AND (srampos_sale_currency.currency_id=2) AND (srampos_sale_currency.amount!='')) THEN srampos_payments.amount 
WHEN ((srampos_payments.paid_by = 'credit' AND srampos_sale_currency.currency_id=1 AND srampos_sale_currency.amount!='')) THEN (srampos_sale_currency.amount*srampos_sale_currency.currency_rate) ELSE 0 END)) paid_by,{$this->db->dbprefix('bils')}.paid  as For_Ex,{$this->db->dbprefix('bils')}.balance,type, {$this->db->dbprefix('payments')}.id as id,{$this->db->dbprefix('payments')}.bill_id")
		    ->from('bils')
		    ->join('payments', 'payments.bill_id = bils.id')
		    ->join('sale_currency', 'sale_currency.bil_id = bils.id')
		    //->order_by('bils.id','ASC')
		    ->group_by('bils.id');
    
    
		if($user = $where_array['user']) {
		    $this->db->where('payments.created_by', $user);
		}
		if($card = $where_array['card']) {
		    $this->db->like('payments.cc_no', $card, 'both');
		}
		if($cheque = $where_array['cheque']) {
		    $this->db->where('payments.cheque_no', $cheque);
		}
		if($transaction_id = $where_array['transaction_id']) {
		    $this->db->where('payments.transaction_id', $transaction_id);
		}
		if($customer = $where_array['customer']) {
		    $this->db->where('bils.customer_id', $customer);
		}
		if($supplier = $where_array['supplier']) {
		    $this->db->where('purchases.supplier_id', $supplier);
		}
		if($biller = $where_array['biller']) {
		    $this->db->where('bils.biller_id', $biller);
		}
		
		if($paid_by = $where_array['paid_by']) {
		    $this->db->where('payments.paid_by', $paid_by);
		}
		if($sale_ref = $where_array['sale_ref']) {
		    $this->db->like('bils.reference_no', $sale_ref, 'both');
		}
		if($start_date = $where_array['start_date']) {
		    $this->db->where('DATE_FORMAT('.$this->db->dbprefix('payments').'.date,"%Y-%m-%d") >=',$start_date);
		}
		if($end_date = $where_array['end_date']) {
		    $this->db->where('DATE_FORMAT('.$this->db->dbprefix('payments').'.date,"%Y-%m-%d") <=',$end_date);
		}
		if(!$where_array['table_whitelisted']){
		    $this->db->where('bils.table_whitelisted', 0); 
		}
		$this->db->limit($limit,$offset);
		$q = $this->db->get();
		//print_R($this->db);exit;
		if ($q->num_rows() > 0) {
		   return $q->result();
		} 
		return false;
	}
    public function sales_report($warehouse_id,$where_array,$page) {

	$product =  $where_array['product'];
	$user =  $where_array['user'];
	$customer =  $where_array['customer'];
	$biller = $where_array['biller'];
	$reference_no = $where_array['reference_no'];
	$start_date = $where_array['start_date'];
	$end_date = $where_array['end_date'];

	$serial =  $where_array['serial'];

		if ($page == "") {
	        $page = 1;
	   }

	    $limit = $this->limit;
        $offset = ($page - 1) * $limit;
	
        $si = "( SELECT id as sale_id, recipe_id as product_id from {$this->db->dbprefix('bil_items')} ";
            if ($product || $serial) { $si .= " WHERE "; }
            if ($product) {
                $si .= " {$this->db->dbprefix('bil_items')}.recipe_id = {$product} ";
            }
            if ($product && $serial) { $si .= " AND "; }
           /* if ($serial) {
                $si .= " {$this->db->dbprefix('bil_items')}.serial_no LIKe '%{$serial}%' ";
            }*/
            $si .= " GROUP BY {$this->db->dbprefix('bil_items')}.bil_id ) FSI";
            
            $this->db
                ->select("DATE_FORMAT(date, '%Y-%m-%d %T') as date, reference_no, biller, customer,grand_total, paid, (grand_total-paid) as balance, payment_status, {$this->db->dbprefix('bils')}.id as id", FALSE)
                ->from('bils')
                ->join($si, 'FSI.sale_id=bils.id', 'left');
              
            $this->db->where('bils.payment_status', 'Completed');
            if ($user) $this->db->where('bils.created_by', $user);
            if ($product) $this->db->where('FSI.product_id', $product);
            if ($biller) $this->db->where('bils.biller_id', $biller);
            if ($customer) $this->db->where('bils.customer_id', $customer);           
            if ($reference_no) $this->db->like('bils.reference_no', $reference_no, 'both');
            if($start_date) $this->db->where('DATE_FORMAT('.$this->db->dbprefix('bils').'.date,"%Y-%m-%d") >=',$start_date);
	    if($end_date) $this->db->where('DATE_FORMAT('.$this->db->dbprefix('bils').'.date,"%Y-%m-%d") <=',$end_date);
	    
	    if(!$where_array['table_whitelisted']){
                $this->db->where('bils.table_whitelisted', 0); 
            }
            $this->db->limit($limit,$offset);
	    $q = $this->db->get();
            if ($q->num_rows() > 0) {
               return $q->result();
            } 
	    return false;
    }
    public function recipes_report($warehouse_id,$where_array,$page) {

    	if ($page == "") {
                $page = 1;
           }

        $limit = $this->limit;
        $offset = ($page - 1) * $limit;

	    $product = $where_array['product'];
        $start_date = $where_array['start_date'];
        $end_date = $where_array['end_date'];
	
	$purchased = "
                (
                    SELECT
                        recipe_id,product_id,SU.name,SP.price,SRP.max_quantity,SRU.name unit_name,
         
                        SUM(CASE
                         WHEN (SU.name='Kg' AND SRU.name='Gram') THEN (SP.price/SRU.operation_value)*SRP.max_quantity
                         WHEN (SU.name='Kg' AND SRU.name='Kg') THEN SP.price*SRP.max_quantity
                         
                         WHEN (SU.name='Litre' AND SRU.name='Millilitre') THEN (SP.price/SRU.operation_value)*SRP.max_quantity
                         WHEN (SU.name='Litre' AND SRU.name='Litre') THEN SP.price*SRP.max_quantity
                         
                         WHEN (SU.name='Package' AND SRU.name='Pieces') THEN (SP.price/SRU.operation_value)*SRP.max_quantity
                         WHEN (SU.name='Package' AND SRU.name='Package') THEN SP.price*SRP.max_quantity
                 
                        ELSE 0 END) purchased
                
                    FROM ".$this->db->dbprefix('recipe_products')." SRP         
                    JOIN ".$this->db->dbprefix('products')." SP on SRP.product_id=SP.id 
                    JOIN ".$this->db->dbprefix('units')." SU on SU.id=SP.unit 
                    JOIN ".$this->db->dbprefix('units')." SRU on SRU.id=SRP.units_id
                    group by SRP.recipe_id
                    order by product_id
                ) P";
        $sold = "
            (
                SELECT recipe_id,SUM(quantity) as quantity,SUM(SBI.unit_price*quantity) as sold FROM ".$this->db->dbprefix('bil_items')." SBI
                            join ".$this->db->dbprefix('bils')." SB on SBI.bil_id=SB.id
                            where SB.payment_status='completed'";
            if ($start_date) {
                $sold .=' AND DATE(SB.date) >="'.$start_date.'"';
            }
            if ($end_date) {
             $sold .=' AND DATE(SB.date) <="'.$end_date.'"';
            }
            if($warehouse_id != 0){
                $sold .=' AND SBI.warehouse_id='.$warehouse_id;    
            }
	    if(!$where_array['table_whitelisted']){
                $sold .= " AND SB.table_whitelisted =0";
            }
        $sold .= " group by SBI.recipe_id
            ) SLSold";
	$this->db
                ->select($this->db->dbprefix('recipe').".code,
                ".$this->db->dbprefix('recipe').".name,
                SUM(P.purchased*SLSold.quantity) as purchased,
                SLSold.sold,
                SUM(SLSold.sold-(P.purchased*SLSold.quantity)) as profitloss,
                SLSold.Quantity
                    ")
                ->from($this->db->dbprefix('recipe'))
                ->join($purchased, $this->db->dbprefix('recipe').".id=P.recipe_id")
                ->join($sold, $this->db->dbprefix('recipe').".id=SLSold.recipe_id")
                ->group_by($this->db->dbprefix('recipe').".id");
		$this->db->limit($limit,$offset);
            $q = $this->db->get();
	    if ($q->num_rows() > 0) {
               return $q->result();
            } 
	    return false;
    }
    public function daysales_report_19052018($warehouse_id,$where_array,$page) {

    	if ($page == "") {
                $page = 1;
           }

        $limit = $this->limit;
        $offset = ($page - 1) * $limit;

	    $start_date = $where_array['start_date'];
	    $where ='';

        if($warehouse_id != 0)
        {
            $where = "AND P.warehouse_id =".$warehouse_id."";
        }
	if(!$where_array['table_whitelisted']){
           $where .= " AND P.table_whitelisted =0";
        }
	
        $mainQuery_select = 'Select RC.name,RC.id,P.bill_number,P.total_tax,P.total_discount,P.grand_total';
        $categoryQuery_select = 'SELECT P.bill_number,RC.name,RC.id cateids,SUM(BI.unit_price) categoryTotal';
        $billQuery_select = 'SELECT P.id AS bill_id,RC.name ,GROUP_CONCAT(DISTINCT  RC.id) cateids, P.bill_number,RC.id,(CASE WHEN (BI.tax_type = 1) THEN total_tax ELSE 0 END) as tax1,P.total_tax as tax,P.total_discount,(P.total-P.total_discount+CASE WHEN (P.tax_type= 1) THEN P.total_tax ELSE 0 END) as grand_total,SUM(P.total_tax) VAT';        
        $cachedQuery = "
            FROM " . $this->db->dbprefix('bils') . " P
            JOIN " . $this->db->dbprefix('bil_items') . " BI ON BI.bil_id = P.id
            JOIN " . $this->db->dbprefix('recipe') . " R ON R.id = BI.recipe_id
            JOIN " . $this->db->dbprefix('recipe_categories') . " RC ON RC.id = R.category_id";
        $mainQuery_group = 'GROUP BY RC.id ORDER BY RC.id ASC';
        $categoryQuery_group = 'GROUP BY RC.id,P.bill_number ORDER BY RC.id ASC';
        $billQuery_group = ' GROUP BY P.bill_number ORDER BY BI.bil_id ASC';
        
        $whereQuery = "WHERE DATE(P.date) = '".$start_date."' AND 
            P.payment_status ='Completed'  ".$where;
            
        $billQuery_limit = "limit $offset,$limit";
        
        
        
	$billQuery = $billQuery_select.' '.$cachedQuery.' '.$whereQuery.' '.$billQuery_group.' '.$billQuery_limit;
	$billQuery_total = $billQuery_select.' '.$cachedQuery.' '.$whereQuery.' '.$billQuery_group;
	$b = $this->db->query($billQuery);
        $t = $this->db->query($billQuery_total);
        if($b->num_rows()==0){return false;}
	//$billnumbers = implode(',',array_column($b->result_array(), 'bill_number'));
	$billnumbers = "'".implode("','",array_column($b->result_array(), 'bill_number'))."'";
	
	$whereQuery = "WHERE P.bill_number in (".$billnumbers.") AND DATE(P.date) = '".$start_date."' AND 
            P.payment_status ='Completed'  ".$where;
	    
	$myQuery = $mainQuery_select.' '.$cachedQuery.' '.$whereQuery.' '.$mainQuery_group;     
        $categoryQuery = $categoryQuery_select.' '.$cachedQuery.' '.$whereQuery.' '.$categoryQuery_group;
            
        
        //$b = $this->db->query($billQuery);       
        $c =  $this->db->query($categoryQuery);
        $q = $this->db->query($myQuery);
        $categories = $c->result_array();
        $AllcategoryIds = array_unique(array_column($c->result_array(), 'cateids'));
        $data = array();
        if ($q->num_rows() > 0) {            
            foreach (($b->result()) as $key => $bill) {
                $data[$key]['bill_number'] = $bill->bill_number;
                $categoryIds = explode(',',$bill->cateids);
                $category_details = array();
                //print_r($AllcategoryIds);exit;
                foreach($AllcategoryIds as $k){
		    
                    if(in_array($k,$categoryIds)){
                        $category_details[] = $this->getDayCategorySale($start_date,$k,$bill->bill_id,$warehouse_id);
                    }
                   else{
                      // $category_details = "";
                    }
                }
		$data[$key]['vat'] = $this->sma->formatMoney($bill->tax);
                $data[$key]['discount'] = $this->sma->formatMoney($bill->total_discount);
                $data[$key]['bill_amt'] = $this->sma->formatMoney($bill->grand_total);
		$data[$key]['category'] = $category_details;                
            }
            //return $data;
	    return array('data'=>$data,'total'=>$t->num_rows());
        }
        return FALSE;
    }
   public function getDayCategorySale($start,$id,$billid,$warehouse_id) {
        
        $where ='';
            if($warehouse_id != 0)
            {
                $where = "AND P.warehouse_id =".$warehouse_id."";
            }
        
        $myquery ="SELECT RC.name,P.bill_number,SUM(BI.subtotal) AS total,SUM(BI.tax) as total_tax,SUM(BI.item_discount+BI.off_discount+BI.input_discount)as discount,BI.tax_type
            FROM " . $this->db->dbprefix('bils') . " P
            JOIN " . $this->db->dbprefix('bil_items') . " BI ON BI.bil_id = P.id
            JOIN " . $this->db->dbprefix('recipe') . " R ON R.id = BI.recipe_id
            JOIN " . $this->db->dbprefix('recipe_categories') . " RC ON RC.id = R.category_id
            WHERE P.id= ".$billid." AND DATE(P.date) = '".$start."' AND RC.id =".$id." AND 
            P.payment_status ='Completed'  ".$where." group by R.category_id " ;
            
            
           
        $q = $this->db->query($myquery);
       
        if ($q->num_rows() > 0) {
            $res = $q->row();
            if($res->tax_type == 1)
            {   
                $value = ($res->total) +($res->total_tax);
            }
            else{
                $value = ($res->total);
            }
            $value = $value -($res->discount);
            /*else
            {
                $value =($res->total)-($res->discount);
            }*/
            return array('name'=>$res->name,'value'=>$this->sma->formatMoney($value));
        }
        return 0;
    }

public function daysales_report($warehouse_id,$where_array,$page)
    {
        $start_date = $where_array['start_date'];

        if($start_date){
           $start_date = $start_date;
        }
        else{
            $start_date = date('Y-m-d');
        }

        $where ='';

        if($warehouse_id != 0)
        {
            $where = "AND P.warehouse_id =".$warehouse_id."";
        }
       if(!$where_array['table_whitelisted']){
           $where .= " OR P.table_whitelisted =0";
        }

        $daily_category_wise = " SELECT RC.id AS category_id,RC.name,SUM(BI.subtotal-BI.item_discount-BI.off_discount-BI.input_discount+CASE WHEN (BI.tax_type = 1) THEN BI.tax ELSE 0 END) as total,W.name branch
         FROM " . $this->db->dbprefix('recipe_categories') . " RC
        LEFT JOIN " . $this->db->dbprefix('recipe') . " R ON R.category_id = RC.id
        JOIN " . $this->db->dbprefix('bil_items') . "  BI ON BI.recipe_id = R.id
        JOIN " . $this->db->dbprefix('bils') . "  B ON B.id = BI.bil_id 
        LEFT JOIN " . $this->db->dbprefix('warehouses') . " W ON W.id = B.warehouse_id
        WHERE DATE(B.date) = '".$start_date."' AND B.payment_status ='Completed' GROUP BY RC.id ".$limits."";
                
        $q = $this->db->query($daily_category_wise);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return array('data'=>$data);
        }
        return FALSE;
    }  
public function daysales_BillDetails($warehouse_id,$where_array,$page,$category_id)
    {
        $limit = $this->limit;
    
        $offset = ($page - 1) * $limit;

        $limits = "";
        if ($page) {
            $limits = "LIMIT $offset, $limit";
        }

        $start_date = $where_array['start_date'];

        if($start_date){
           $start_date = $start_date;
        }
        else{
            $start_date = date('Y-m-d');
        }

        $where ='';

        if($warehouse_id != 0)
        {
            $where = "AND B.warehouse_id =".$warehouse_id."";
        }
       if(!$where_array['table_whitelisted']){
           $where .= " AND B.table_whitelisted =0";
        }else{
            $where .= " OR B.table_whitelisted = ".$where_array['table_whitelisted']."";
        }
        /*SELECT B.bill_number,RC.name,SUM(BI.subtotal-CASE WHEN (BI.tax_type= 0) THEN BI.tax ELSE 0 END) AS subtotal,SUM(BI.item_discount+BI.off_discount+BI.input_discount) AS discount,SUM(BI.tax) AS tax,SUM(BI.subtotal-BI.item_discount-BI.off_discount-BI.input_discount+CASE WHEN (BI.tax_type = 1) THEN BI.tax ELSE 0 END) as total
        FROM srampos_recipe_categories RC
        JOIN srampos_recipe R ON R.category_id = RC.id
        JOIN srampos_bil_items BI ON BI.recipe_id = R.id
        JOIN srampos_bils B ON B.id = BI.bil_id  WHERE 
        DATE(B.date) BETWEEN '2018-01-18' AND '2018-05-18' AND B.payment_status ='Completed' AND RC.id = 1 GROUP BY B.id*/
        $daily_category_details = " SELECT B.bill_number,RC.name,SUM(BI.subtotal-CASE WHEN (BI.tax_type= 0) THEN BI.tax ELSE 0 END) AS subtotal,SUM(BI.item_discount+BI.off_discount+BI.input_discount) AS discount,SUM(BI.tax) AS tax,SUM(BI.subtotal-BI.item_discount-BI.off_discount-BI.input_discount+CASE WHEN (BI.tax_type = 1) THEN BI.tax ELSE 0 END) as total,W.name branch 
        FROM " . $this->db->dbprefix('recipe_categories') . " RC
        LEFT JOIN " . $this->db->dbprefix('recipe') . " R ON R.category_id = RC.id
        JOIN " . $this->db->dbprefix('bil_items') . "  BI ON BI.recipe_id = R.id
        JOIN " . $this->db->dbprefix('bils') . "  B ON B.id = BI.bil_id  
        LEFT JOIN " . $this->db->dbprefix('warehouses') . " W ON W.id = B.warehouse_id
        WHERE DATE(B.date) = '".$start_date."' AND B.payment_status ='Completed'  AND RC.id = ".$category_id."  ".$where." GROUP BY B.id ".$limits." ";
                
        $q = $this->db->query($daily_category_details);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return array('data'=>$data);
        }
        return FALSE;
    }      
 

public function monthlysales_report($warehouse_id,$where_array,$page)
    {
        $limit = $this->limit;
    
        $offset = ($page - 1) * $limit;

        $limits = "";
        if ($page) {
            $limits = "LIMIT $offset, $limit";
        }

        $start_date = $where_array['start_date'];
        $end_date = $where_array['end_date'];
        if($start_date){
           $start_date = $start_date;
        }
        else{
            $start_date = date('Y-m-d');
        }

        if($end_date){
           $end_date = $end_date;
        }
        else{
            $end_date = date('Y-m-d');
        }
        $where ='';

        if($warehouse_id != 0)
        {
            $where = "AND P.warehouse_id =".$warehouse_id."";
        }
       if(!$where_array['table_whitelisted']){
           $where .= " OR P.table_whitelisted =0";
        }

        $monthly_sale_category_wise = " SELECT RC.id AS category_id,RC.name,SUM(BI.subtotal-BI.item_discount-BI.off_discount-BI.input_discount+CASE WHEN (BI.tax_type = 1) THEN BI.tax ELSE 0 END) as total,W.name branch
        FROM " . $this->db->dbprefix('recipe_categories') . " RC
        LEFT JOIN " . $this->db->dbprefix('recipe') . " R ON R.category_id = RC.id
        JOIN " . $this->db->dbprefix('bil_items') . "  BI ON BI.recipe_id = R.id
        JOIN " . $this->db->dbprefix('bils') . "  B ON B.id = BI.bil_id  
        LEFT JOIN " . $this->db->dbprefix('warehouses') . " W ON W.id = B.warehouse_id
        WHERE DATE(B.date) BETWEEN '".$start_date."' AND '".$end_date."' AND B.payment_status ='Completed' GROUP BY RC.id ".$limits."";
        $q = $this->db->query($monthly_sale_category_wise);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return array('data'=>$data);
        }
        return FALSE;
    }     
public function monthlysales_BillDetails($warehouse_id,$where_array,$page,$category_id)
    {
        $limit = $this->limit;    
        $offset = ($page - 1) * $limit;

        $limits = "";
        if ($page) {
            $limits = "LIMIT $offset, $limit";
        }

        $start_date = $where_array['start_date'];        
        $end_date = $where_array['end_date'];        
        $where ='';

        if($warehouse_id != 0)
        {
            $where = "AND P.warehouse_id =".$warehouse_id."";
        }
       if(!$where_array['table_whitelisted']){
           $where .= " OR P.table_whitelisted =0";
        }
        
        $monthly_category_details = " SELECT B.bill_number,RC.name,SUM(BI.subtotal-CASE WHEN (BI.tax_type= 0) THEN BI.tax ELSE 0 END) AS subtotal,SUM(BI.item_discount+BI.off_discount+BI.input_discount) AS discount,SUM(BI.tax) AS tax,SUM(BI.subtotal-BI.item_discount-BI.off_discount-BI.input_discount+CASE WHEN (BI.tax_type = 1) THEN BI.tax ELSE 0 END) as total,W.name branch
        FROM " . $this->db->dbprefix('recipe_categories') . " RC
        LEFT JOIN " . $this->db->dbprefix('recipe') . " R ON R.category_id = RC.id
        JOIN " . $this->db->dbprefix('bil_items') . "  BI ON BI.recipe_id = R.id
        JOIN " . $this->db->dbprefix('bils') . "  B ON B.id = BI.bil_id 
        LEFT JOIN " . $this->db->dbprefix('warehouses') . " W ON W.id = B.warehouse_id
        WHERE DATE(B.date) BETWEEN '".$start_date."' AND '".$end_date."' AND B.payment_status ='Completed'  AND RC.id = ".$category_id." GROUP BY B.id ".$limits."";
               
        $q = $this->db->query($monthly_category_details);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return array('data'=>$data);
        }
        return FALSE;
    } 
    public function monthlysales_report_18052018($warehouse_id,$where_array,$page) {

        /*if ($page == "") {
                $page = 1;
           }*/

        $limit = $this->limit;
    
        $offset = ($page - 1) * $limit;

        $limits = "";
        if ($page) {
            $limits = "LIMIT $offset, $limit";
        }

        $start_date = $where_array['start_date'];
        $end_date = $where_array['end_date'];
        $where ='';

        if($warehouse_id != 0)
        {
            $where = "AND P.warehouse_id =".$warehouse_id."";
        }
    if(!$where_array['table_whitelisted']){
           $where .= " AND P.table_whitelisted =0";
        }
        $mainQuery_select = 'Select RC.name,RC.id,P.bill_number,P.total_tax,P.total_discount,P.grand_total';
        $categoryQuery_select = 'SELECT P.bill_number,RC.name,RC.id cateids,SUM(BI.unit_price) categoryTotal';
        $billQuery_select = 'SELECT P.id AS bill_id,RC.name ,GROUP_CONCAT(DISTINCT  RC.id) cateids, P.bill_number,RC.id,(CASE WHEN (BI.tax_type = 1) THEN total_tax ELSE 0 END) as tax1,P.total_tax as tax,P.total_discount,(P.total-P.total_discount+CASE WHEN (P.tax_type= 1) THEN P.total_tax ELSE 0 END) as grand_total,SUM(P.total_tax) VAT';        
        $cachedQuery = "
            FROM " . $this->db->dbprefix('bils') . " P
            JOIN " . $this->db->dbprefix('bil_items') . " BI ON BI.bil_id = P.id
            JOIN " . $this->db->dbprefix('recipe') . " R ON R.id = BI.recipe_id
            JOIN " . $this->db->dbprefix('recipe_categories') . " RC ON RC.id = R.category_id";
        $mainQuery_group = 'GROUP BY RC.id ORDER BY RC.id ASC';
        $categoryQuery_group = 'GROUP BY RC.id,P.bill_number ORDER BY RC.id ASC';
        $billQuery_group = ' GROUP BY P.bill_number ORDER BY BI.bil_id ASC';
    
        
        $whereQuery = "WHERE DATE(P.date) BETWEEN '".$start_date."' AND '".$end_date."' AND 
            P.payment_status ='Completed'  ".$where;
        
        $billQuery_limit = $limits;        
        $billQuery = $billQuery_select.' '.$cachedQuery.' '.$whereQuery.' '.$billQuery_group.' '.$billQuery_limit;
    $billQuery_total = $billQuery_select.' '.$cachedQuery.' '.$whereQuery.' '.$billQuery_group;
    $b = $this->db->query($billQuery);
        $t = $this->db->query($billQuery_total);
    if($b->num_rows()==0){return false;}
        //$billnumbers = implode(',',array_column($b->result_array(), 'bill_number'));
        $billnumbers = "'".implode("','",array_column($b->result_array(), 'bill_number'))."'";
    
    $whereQuery = "WHERE P.bill_number in (".$billnumbers.") AND DATE(P.date) BETWEEN '".$start_date."' AND '".$end_date."' AND 
            P.payment_status ='Completed'  ".$where;
    $myQuery = $mainQuery_select.' '.$cachedQuery.' '.$whereQuery.' '.$mainQuery_group;     
        $categoryQuery = $categoryQuery_select.' '.$cachedQuery.' '.$whereQuery.' '.$categoryQuery_group;
            
        
        //$b = $this->db->query($billQuery);       
        $c =  $this->db->query($categoryQuery);
        $q = $this->db->query($myQuery);
        $categories = $c->result_array();
        $AllcategoryIds = array_unique(array_column($c->result_array(), 'cateids'));
        $data = array();
        if ($q->num_rows() > 0) {
            foreach (($b->result()) as $key => $bill) {
                $data[$key]['bill_number'] = $bill->bill_number;
                $categoryIds = explode(',',$bill->cateids);
                $category_details = array();
                //print_r($categoryIds);exit;
                foreach($AllcategoryIds as $k){
                    if(in_array($k,$categoryIds)){
                        $category_details[] = $this->getMonthlyCategorySale($start_date,$end_date,$k,$bill->bill_id,$warehouse_id);
                    }
                   else{
                       //$data['tbody'][$key][] = "";
                    }
                }
        $data[$key]['vat'] = $this->sma->formatMoney($bill->tax);
                $data[$key]['discount'] = $this->sma->formatMoney($bill->total_discount);
                $data[$key]['bill_amt'] = $this->sma->formatMoney($bill->grand_total);
        $data[$key]['category'] = $category_details;                
                
            }
        //print_R($data);exit;
            //return $data;
        return array('data'=>$data,'total'=>$t->num_rows());
        }
        return FALSE;
    }

    public function getMonthlyCategorySale($start,$end,$id,$billid,$warehouse_id) {

    	
    	
        $where ='';
            if($warehouse_id != 0)
            {
                $where = "AND P.warehouse_id =".$warehouse_id."";
            }
        
        $myquery ="SELECT RC.name,P.bill_number,SUM(BI.subtotal) AS total,SUM(BI.tax) as total_tax,SUM(BI.item_discount+BI.off_discount+BI.input_discount)as discount,BI.tax_type
            FROM " . $this->db->dbprefix('bils') . " P
            JOIN " . $this->db->dbprefix('bil_items') . " BI ON BI.bil_id = P.id
            JOIN " . $this->db->dbprefix('recipe') . " R ON R.id = BI.recipe_id
            JOIN " . $this->db->dbprefix('recipe_categories') . " RC ON RC.id = R.category_id
            WHERE P.id= ".$billid." AND DATE(P.date) BETWEEN '".$start."' AND '".$end."' AND RC.id =".$id." AND 
            P.payment_status ='Completed'  ".$where." group by R.category_id" ;
           
        $q = $this->db->query($myquery);
       
        if ($q->num_rows() > 0) {
            $res = $q->row();
            if($res->tax_type == 1)
            {   
                $value = ($res->total) +($res->total_tax);
            }
            else{
                $value = ($res->total);
            }
            $value = $value -($res->discount);
            /*else
            {
                $value =($res->total)-($res->discount);
            }*/
            return array('name'=>$res->name,'value'=>$this->sma->formatMoney($value));
        }
        
        return 0;
    }
     public function hourlysales_report($warehouse_id,$where_array,$page) {

    if ($page == "") {
            $page = 1;
       }

    $limit = $this->limit;
    $offset = ($page - 1) * $limit;

    $start_date = $where_array['start_date'];
    $end_date = $where_array['end_date'];
    $time_start = $where_array['start_time'];
    $time_end = $where_array['end_time'];
    /*$time_range = $where_array['time_range'];*/
    $where ='';

        if($warehouse_id != 0)
        {
            $where = "AND B.warehouse_id =".$warehouse_id."";
        }
       if(!$where_array['table_whitelisted']){
           $where .= " AND B.table_whitelisted =0";
        }

        $myQuery = "SELECT RC.name,SUM(BI.quantity) AS quantity, SUM(BI.subtotal-BI.item_discount-BI.off_discount-BI.input_discount+CASE WHEN (BI.tax_type = 1) THEN BI.tax ELSE 0 END) as total
        FROM " . $this->db->dbprefix('bils') . " B
        JOIN " . $this->db->dbprefix('bil_items') . " BI ON BI.bil_id = B.id
        JOIN " . $this->db->dbprefix('recipe') . " R ON R.id = BI.recipe_id
        JOIN " . $this->db->dbprefix('recipe_categories') . " RC ON RC.id = R.category_id
        WHERE B.payment_status ='Completed' AND DATE(date) BETWEEN '".$start_date."' AND '".$end_date."'
        AND HOUR(date) BETWEEN '".$time_start."' AND '".$time_end."' GROUP BY R.category_id";

        /*$myQuery = "SELECT R.id recipeid,R.name,count(BI.quantity) qty,(BI.unit_price) AS val
        FROM " . $this->db->dbprefix('bils') . " P
        JOIN " . $this->db->dbprefix('bil_items') . " BI ON BI.bil_id = P.id
        JOIN " . $this->db->dbprefix('recipe') . " R ON R.id = BI.recipe_id
        JOIN " . $this->db->dbprefix('recipe_categories') . " RC ON RC.id = R.category_id
        WHERE DATE(P.date) BETWEEN '".$start_date."' AND '".$end_date."' AND
        P.payment_status ='Completed'  ".$where."
        GROUP BY R.id ORDER BY R.id,RC.id ASC";*/

        $q = $this->db->query($myQuery);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
 
    }
    public function hourlysales_report_25052018($warehouse_id,$where_array,$page) {

	if ($page == "") {
            $page = 1;
       }

    $limit = $this->limit;
    $offset = ($page - 1) * $limit;

	$start_date = $where_array['start_date'];
	$end_date = $where_array['end_date'];
	$time_start = $where_array['start_time'];
	$time_end = $where_array['end_time'];
	$time_range = $where_array['time_range'];
	$where ='';
        if($warehouse_id != 0)
        {
            $where = "AND P.warehouse_id =".$warehouse_id."";
        }
	if(!$where_array['table_whitelisted']){
           $where .= " AND P.table_whitelisted =0";
        }
        $myQuery = "SELECT R.id recipeid,R.name,count(BI.quantity) qty,(BI.unit_price) AS val
        FROM " . $this->db->dbprefix('bils') . " P
        JOIN " . $this->db->dbprefix('bil_items') . " BI ON BI.bil_id = P.id
        JOIN " . $this->db->dbprefix('recipe') . " R ON R.id = BI.recipe_id
        JOIN " . $this->db->dbprefix('recipe_categories') . " RC ON RC.id = R.category_id
        WHERE DATE(P.date) BETWEEN '".$start_date."' AND '".$end_date."' AND
        P.payment_status ='Completed'  ".$where."
        GROUP BY R.id ORDER BY R.id,RC.id ASC";
        /*echo $myQuery;die;*/
	$limit = " limit $offset,$limit";
	$t = $this->db->query($myQuery);
        $myQuery .=$limit;
        $q = $this->db->query($myQuery);
        if ($q->num_rows() > 0) {
            $result = $q->result_array();
            $timeArray = array();$first = true;
            $to='';
            //$time_start = 6;$time_end = 23;
            for($i=$time_start;$i<$time_end;$i++){
                if($first){
                    $first = false;
                    $frm = $i;
                    $to = $i+$time_range;
                }else{
                   $frm = ($to);//$k+$time_range;
                   $to = $frm+$time_range;//$k+$time_range+$time_range;
                  
                } 
                if($to > $time_end){ 
                    $to = $time_end;
                }
                $frmTo = $frm.'-'.$to;
                $timeArray[$frm] = $frmTo;
                if($frm==$time_end || $to==$time_end || ($frm < $time_end && $to > $time_end)){  break;}
            }
           $conditions['start'] = $start_date;
           $conditions['end'] = $end_date;
           $conditions['where'] = $where;
           return  $this->hourlysale_calculation($result,$timeArray,$time_range,$conditions);
	}
     return FALSE;
    }
    public function hourlysale_calculation_bkk($data,$timerange,$time_range,$conditions){

    	

        $return['thead'] = array();
        $return['thead'][] = 'Item';
        $return['thead'][] = 'Qty/Val';
        $first=true;
        $to = '';
        foreach($timerange as $k => $range){
            $return['thead'][] = $range;
        }
        
       
        $return['tbody'] = array();
        $timerangeBody = $timerange;
        foreach($data as $key => $row){ 
            $timerangeBody = $timerange;
           
            $return['tbody'][$key][] = $row['name'];
            $return['tbody'][$key][] = 'Qty/val';
            $qtyval = $this->getRecipeQty_val($row['recipeid'],$conditions['start'],$conditions['end'],$conditions['where']);

            
            $cnt = 1;
            $qtyval_1 = array();
            $itemTotal_price = '';
            //print_r($qtyval);//exit;
            foreach($qtyval as $k => $k_val){                
                foreach($timerangeBody as $t => $t_val){
                    $tarray = explode('-',$t_val);
                    
                    if($tarray[0] <= $k_val['time'] && $tarray[1] > $k_val['time']){
                      
                      if(isset($qtyval_1[$t_val])){ 
                        $cnt = $cnt+$k_val['qty'];
                        $qtyval_1[$t_val]['qty'] = $cnt;
                        $itemTotal_price = $itemTotal_price + $k_val['more_qty_total'];
                        $qtyval_1[$t_val]['item_total_price'] = $itemTotal_price;
                          
                      }else{ 
                        $cnt = $k_val['qty'];
                        $qtyval_1[$t_val]['qty'] = $cnt;//exit;
                        $itemTotal_price = $k_val['more_qty_total'];
                        $qtyval_1[$t_val]['item_total_price'] = $itemTotal_price;
                      }
                      $qtyval_1[$t_val]['price'] = 'price';
                      $qtyval_1[$t_val]['time'] = $k_val['time'];
                      /*echo $k_val['tax_type'];die;*/
                      if($k_val['tax_type'] != 0)
                      { 
                        $final_val = $k_val['subtotal']-$k_val['item_discount']-$k_val['off_discount']-$k_val['input_discount']+$k_val['tax'];
                      }
                      else{
                        $final_val = $k_val['subtotal']-$k_val['item_discount']-$k_val['off_discount']-$k_val['input_discount'];
                      }
                      /*$k_val['val'] = $final_val;*/
                      $qtyval_1[$t_val]['val'] = $k_val['val'];
                    }
                }
            }
          foreach($timerangeBody as $t => $t_val){
             foreach($qtyval_1 as $k => $k_val){ 
                    if($k == $t_val){
                            //$timerangeBody[$t] =   '<td><span class="recipe-qty">'.$k_val['qty'].'</span></br><span  class="recipe-val">'.($k_val['val']*$k_val['qty']).'</span></td>';
                            $timerangeBody[$t] =   'Qty - '.$k_val['qty'].' / Val - '.$k_val['item_total_price'];
                    }
                }
            }
            foreach($timerangeBody as $td_key => $td_val){
                $tdvalarray = explode('-',$td_val);
                if(!is_numeric($tdvalarray[0])){
                    $return['tbody'][$key][] = $td_val;
                }else{
                    $return['tbody'][$key][] = '-';
                }
            }
           
        }
       
        return $return;;
    }
    public function hourlysale_calculation($data,$timerange,$time_range,$conditions){

    	

        $return['thead'] = array();
        $return['thead'][] = 'Item';
        $return['thead'][] = 'Qty/Val';
        $first=true;
        $to = '';
        foreach($timerange as $k => $range){
            $return['thead'][] = $range;
        }
        
       
        $return['tbody'] = array();
        $timerangeBody = $timerange;
        foreach($data as $key => $row){ 
            $timerangeBody = $timerange;
           
            $return['tbody'][$key][] = $row['name'];
            $return['tbody'][$key][] = 'Qty/val';
            foreach($timerangeBody as $t => $t_val){
                    $timebetween = explode('-',$t_val);
                    $items[$t_val] = $this->getRecipeQty_val($row['recipeid'],$conditions['start'],$conditions['end'],$conditions['where'],$timebetween);
                    if($items[$t_val]){
                        $return['tbody'][$key][] = 'Qty - '.$items[$t_val]["qty"].' / Val - '.$items[$t_val]["more_qty_total"];
                    }else{
                        $return['tbody'][$key][] = '-';
                    }
                    
            }
           
        }
       
        return $return;;
    }
    function getRecipeQty_vall($id,$start,$end,$where){

    	

          $myQuery = "SELECT R.id,R.name,(BI.quantity) AS qty,BI.unit_price AS val,BI.subtotal,BI.item_discount,BI.off_discount,BI.input_discount,BI.tax_type,BI.tax,DATE_FORMAT(P.date,'%H') time,(BI.subtotal-BI.item_discount-BI.off_discount-BI.input_discount+CASE WHEN (BI.tax_type = 1) THEN BI.tax ELSE 0 END) AS val12,SUM(BI.subtotal-BI.item_discount-BI.off_discount-BI.input_discount+CASE WHEN (BI.tax_type = 1) THEN BI.tax ELSE 0 END) as val
            FROM " . $this->db->dbprefix('bils') . " P
            JOIN " . $this->db->dbprefix('bil_items') . " BI ON BI.bil_id = P.id
            JOIN " . $this->db->dbprefix('recipe') . " R ON R.id = BI.recipe_id
            JOIN " . $this->db->dbprefix('recipe_categories') . " RC ON RC.id = R.category_id
            WHERE DATE(P.date) BETWEEN '".$start."' AND '".$end."' AND BI.recipe_id = $id AND
            P.payment_status ='Completed'  ".$where." GROUP BY R.id
             ORDER BY RC.id ASC ";
              
             /*echo $myQuery;die;*/
            $q = $this->db->query($myQuery);
            if ($q->num_rows() > 0) {
                return $q->result_array();
            }
    }
     function getRecipeQty_val($id,$start,$end,$where,$timebetween){
        //$id = 167;

    	
          $myQuery = "SELECT P.bill_number,R.id,R.name,SUM(BI.quantity) qty,BI.unit_price val,BI.subtotal,BI.item_discount,BI.off_discount,BI.input_discount,DATE_FORMAT(P.date,'%H') time,BI.tax,BI.tax_type,(BI.subtotal-BI.item_discount-BI.off_discount-BI.input_discount+CASE WHEN (BI.tax_type = 1) THEN BI.tax ELSE 0 END) AS one_qty_total,SUM(BI.subtotal-BI.item_discount-BI.off_discount-BI.input_discount+CASE WHEN (BI.tax_type = 1) THEN BI.tax ELSE 0 END) as more_qty_total

            FROM " . $this->db->dbprefix('bils') . " P
            JOIN " . $this->db->dbprefix('bil_items') . " BI ON BI.bil_id = P.id
            JOIN " . $this->db->dbprefix('recipe') . " R ON R.id = BI.recipe_id
            JOIN " . $this->db->dbprefix('recipe_categories') . " RC ON RC.id = R.category_id
            WHERE DATE(P.date) BETWEEN '".$start."' AND '".$end."' AND BI.recipe_id = $id AND DATE_FORMAT(P.date,'%H') >= ".$timebetween[0]." AND DATE_FORMAT(P.date,'%H') < ".$timebetween[1]." AND
            P.payment_status ='Completed'  ".$where." GROUP BY R.id
             ORDER BY RC.id ASC";
              //P.payment_status ='Completed'  ".$where." GROUP BY R.id, P.bill_number
             /*echo $myQuery;die;*/
            $q = $this->db->query($myQuery);
           // print_r($q->result_array());exit;
            if ($q->num_rows() > 0) {
                return $q->row_array();
            }
	    return false;
    }
    public function categories_report($warehouse_id,$where_array,$page) {

    	/*if ($page == "") {
                $page = 1;
           }*/

        $limit = $this->limit;
        $offset = ($page - 1) * $limit;

		$start_date = $where_array['start_date'];
		$end_date = $where_array['end_date'];

       /* if($start_date){
           $start_date = $start_date;
        }
        else{
            $start_date = date('Y-m-d');
        }
        if($end_date)
        {
            $end_date = $end_date;
        }
        else{
            $end_date = date('Y-m-d');
        }*/

		$category = $where_array['category'];
	
	 $pp = "( SELECT pp.category_id as category, SUM( pi.quantity ) purchasedQty, SUM( pi.subtotal ) totalPurchase from {$this->db->dbprefix('products')} pp
                left JOIN " . $this->db->dbprefix('purchase_items') . " pi ON pp.id = pi.product_id
                left join " . $this->db->dbprefix('purchases') . " p ON p.id = pi.purchase_id ";

        $sp = "( SELECT sp.category_id as category, SUM( si.quantity ) soldQty, SUM( si.subtotal ) totalSale12,SUM(DISTINCT s.total-s.total_discount+CASE WHEN (s.tax_type = 1) THEN s.total_tax ELSE 0 END) as totalSale
         from {$this->db->dbprefix('recipe')} sp
                left JOIN " . $this->db->dbprefix('bil_items') . " si ON sp.id = si.recipe_id
                left join " . $this->db->dbprefix('bils') . " s ON s.id = si.bil_id ";
            
        if ($start_date || $warehouse_id) {
            $pp .= " WHERE ";
            $sp .= " WHERE ";
            if ($start_date) {
                //$start_date = $this->sma->fld($start_date);
                //$end_date = $end_date ? $this->sma->fld($end_date) : date('Y-m-d');
                $pp .= " p.date >= '{$start_date}' AND p.date < '{$end_date}' ";
                $sp .= " s.date >= '{$start_date}' AND s.date < '{$end_date}' ";
                if ($warehouse_id) {
                    $pp .= " AND ";
                    $sp .= " AND ";
                }
            }
            if ($warehouse_id) {
                $pp .= " pi.warehouse_id = '{$warehouse_id}' ";
                $sp .= " si.warehouse_id = '{$warehouse_id}' ";
            }
        }
        $pp .= " GROUP BY pp.category_id ) PCosts";
        $sp .= " GROUP BY sp.category_id ) PSales";

	
	$this->db
            ->select($this->db->dbprefix('recipe_categories') . ".id as cid, " .$this->db->dbprefix('recipe_categories') . ".code, " . $this->db->dbprefix('recipe_categories') . ".name,
                SUM( COALESCE( PCosts.purchasedQty, 0 ) ) as PurchasedQty,
                SUM( COALESCE( PSales.soldQty, 0 ) ) as SoldQty,
                SUM( COALESCE( PCosts.totalPurchase, 0 ) ) as TotalPurchase,
                SUM( COALESCE( PSales.totalSale, 0 ) ) as TotalSales,
                (SUM( COALESCE( PSales.totalSale, 0 ) )- SUM( COALESCE( PCosts.totalPurchase, 0 ) ) ) as Profit", FALSE)
            ->from('recipe_categories')
            ->join($sp, 'recipe_categories.id = PSales.category', 'left')
            ->join($pp, 'recipe_categories.id = PCosts.category', 'left');

        if ($category) {
            $this->db->where('recipe_categories.id', $category);
        }
        $this->db->group_by('recipe_categories.id, recipe_categories.code, recipe_categories.name, PSales.SoldQty, PSales.totalSale, PCosts.purchasedQty, PCosts.totalPurchase');
        //$this->db->unset_column('cid');

       if ($page) {
           $this->db->limit($limit,$offset);
        }
       $q = $this->db->get();
	//print_R($this->db);exit;
        if ($q->num_rows() > 0) {
           return $q->result();
        } 
        return false;
    }
    public function brands_report($warehouse_id,$where_array,$page) {

    	/*if ($page == "") {
                $page = 1;
           }*/

        $limit = $this->limit;
        $offset = ($page - 1) * $limit;

		$start_date = $where_array['start_date'];
		$end_date = $where_array['end_date'];
        /*if($start_date){
           $start_date = $start_date;
        }
        else{
            $start_date = date('Y-m-d');
        }
        if($end_date)
        {
            $end_date = $end_date;
        }
        else{
            $end_date = date('Y-m-d');
        }*/
		$brand = $where_array['brand'];
	
		$pp = "( SELECT pp.brand as brand, SUM( pi.quantity ) purchasedQty, SUM( pi.subtotal ) totalPurchase from {$this->db->dbprefix('products')} pp
                left JOIN " . $this->db->dbprefix('purchase_items') . " pi ON pp.id = pi.product_id
                left join " . $this->db->dbprefix('purchases') . " p ON p.id = pi.purchase_id ";

        $sp = "( SELECT sp.brand as brand, SUM( si.quantity ) soldQty,SUM(s.total-s.total_discount+CASE WHEN (s.tax_type= 1) THEN s.total_tax ELSE 0 END) as totalSale, SUM( si.subtotal ) totalSale1 from {$this->db->dbprefix('products')} sp
                left JOIN " . $this->db->dbprefix('bil_items') . " si ON sp.id = si.recipe_id
                left join " . $this->db->dbprefix('bils') . " s ON s.id = si.bil_id ";
                /*echo $sp;die;*/
        if ($start_date || $warehouse_id) {
            $pp .= " WHERE ";
            $sp .= " WHERE ";
            if ($start_date) {
                $start_date = $this->sma->fld($start_date);
                $end_date = $end_date ? $this->sma->fld($end_date) : date('Y-m-d');
                $pp .= " p.date >= '{$start_date}' AND p.date < '{$end_date}' ";
                $sp .= " s.date >= '{$start_date}' AND s.date < '{$end_date}' ";
                if ($warehouse_id) {
                    $pp .= " AND ";
                    $sp .= " AND ";
                }
            }
            if ($warehouse_id) {
                $pp .= " pi.warehouse_id = '{$warehouse_id}' ";
                $sp .= " si.warehouse_id = '{$warehouse_id}' ";
            }
        }
        $pp .= " GROUP BY pp.brand ) PCosts";
        $sp .= " GROUP BY sp.brand ) PSales";
	
	$this->db
                ->select($this->db->dbprefix('brands') . ".id as id, " . $this->db->dbprefix('brands') . ".name,
                    SUM( COALESCE( PCosts.purchasedQty, 0 ) ) as PurchasedQty,
                    SUM( COALESCE( PSales.soldQty, 0 ) ) as SoldQty,
                    SUM( COALESCE( PCosts.totalPurchase, 0 ) ) as TotalPurchase,
                    SUM( COALESCE( PSales.totalSale, 0 ) ) as TotalSales,
                    (SUM( COALESCE( PSales.totalSale, 0 ) )- SUM( COALESCE( PCosts.totalPurchase, 0 ) ) ) as Profit", FALSE)
                ->from('brands')
                ->join($sp, 'brands.id = PSales.brand', 'left')
                ->join($pp, 'brands.id = PCosts.brand', 'left');

            if ($brand) {
                $this->db->where('brands.id', $brand);
            }
            $this->db->group_by('brands.id, brands.name, PSales.SoldQty, PSales.totalSale, PCosts.purchasedQty, PCosts.totalPurchase');
	     if ($page) {
           $this->db->limit($limit,$offset);
        }
	    $q = $this->db->get();
		//print_R($this->db);exit;
            if ($q->num_rows() > 0) {
               return $q->result();
            } 
            return false;
    }
    public function quantity_alerts_report($warehouse_id,$where_array,$page) {

    	if ($page == "") {
                $page = 1;
           }
        $limit = $this->limit;
        $offset = ($page - 1) * $limit;
        $base_url = base_url()."assets/uploads/thumbs/";
	    if ($warehouse_id) {

                $this->db
                    ->select('CONCAT("'.$base_url.'", image) as image, code, name, wp.quantity, alert_quantity')
                    ->from('products')
                    ->join("( SELECT * from {$this->db->dbprefix('warehouses_products')} WHERE warehouse_id = {$warehouse_id}) wp", 'products.id=wp.product_id', 'left')
                    ->where('alert_quantity > wp.quantity', NULL)
                    ->or_where('wp.quantity', NULL)
                    ->where('track_quantity', 1)
                    ->group_by('products.id');
	} else {
	    $this->db
		->select('CONCAT("'.$base_url.'", image) as image, code, name, quantity, alert_quantity')
		->from('products')
		->where('alert_quantity > quantity', NULL)
		->where('track_quantity', 1);
	}
	$this->db->limit($limit,$offset);
	$q = $this->db->get();
	     //print_R($this->db);exit;
	 if ($q->num_rows() > 0) {
	    return $q->result();
	 } 
	 return false;
    }
    public function customers_report($warehouse_id,$where,$page) {

        $start_date = $where['start_date'];
        $end_date = $where['end_date'];
        $customer = $where['customer_id'];

        if ($start_date) {
            $start_date = date('Y-m-d');
            $end_date = date('Y-m-d');
        }

        $limit = $this->limit;
        $offset = ($page - 1) * $limit;

	    $s = "( SELECT customer_id, count(id) as total, COALESCE(sum(grand_total), 0) as total_amount, COALESCE(sum(paid), 0) as paid, ( COALESCE(sum(grand_total), 0) - COALESCE(sum(paid), 0)) as balance from {$this->db->dbprefix('bils')}  ";
        $s .= " WHERE ";
        $s .= " payment_status = 'Completed' ";
       
       /* if ($start_date) {
            $s .=' AND DATE(date) >="'.$start_date.'"';
        }
        if ($end_date) {
         $s .=' AND DATE(date) <="'.$end_date.'"';
        }*/

          $s .= " GROUP BY customer_id ) FS";
            $this->db
                ->select($this->db->dbprefix('companies') . ".id as customer_id, company, name, phone, email, FS.total, FS.total_amount, FS.paid, FS.balance", FALSE)
                ->from("companies")
                ->join($s, 'FS.customer_id=companies.id')
                ->where('companies.group_name', 'customer')
                /*->where('bils.payment_status', 'Completed')*/
                ->group_by('companies.id');

           if ($page) {
               $this->db->limit($limit,$offset);
            }
            $q = $this->db->get();		
            if ($q->num_rows() > 0) {
               return $q->result();
            } 
            return false;
    }
    function getSalesReport($warehouse_id,$where,$page)
    {
        
        $start_date = $where['start_date'];
        $end_date = $where['end_date'];
        $customer = $where['customer_id'];

       /*if($start_date){
           $start_date = $start_date;
        }
        else{
            $start_date = date('Y-m-d');
        }
        if($end_date)
        {
            $end_date = $end_date;
        }
        else{
            $end_date = date('Y-m-d');
        }*/
       
        $si = "( SELECT bil_id as sale_id, recipe_id as product_id from {$this->db->dbprefix('bil_items')} ";
                   
        $si .= " GROUP BY {$this->db->dbprefix('bil_items')}.bil_id ) FSI";  
             
        $this->db
        ->select("DATE_FORMAT(date, '%Y-%m-%d %T') as date, reference_no, biller, customer, grand_total, paid, (grand_total-paid) as balance, payment_status, {$this->db->dbprefix('bils')}.id as id", FALSE)
        ->from('bils')
        ->join($si, 'FSI.sale_id=bils.id', 'left');
               
        if ($customer) {
            $this->db->where('bils.customer_id', $customer);
        }
            
        /*if date not post, it take today dates*/
        /*$this->db->where($this->db->dbprefix('bils').'.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');*/

        /*if(!$where['table_whitelisted']){
         $this->db->where('bils.table_whitelisted',0);
        }else{
            $this->db->or_where('bils.table_whitelisted',$where['table_whitelisted']);
        }*/

        if($page){
           $this->db->limit($limit,$offset);
        }

        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
            $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    } 

    public function suppliers_report($page) {

    	if ($page == "") {
                $page = 1;
           }

        $limit = $this->limit;
        $offset = ($page - 1) * $limit;

	    $p = "( SELECT supplier_id, count(" . $this->db->dbprefix('purchases') . ".id) as total, COALESCE(sum(grand_total), 0) as total_amount, COALESCE(sum(paid), 0) as paid, ( COALESCE(sum(grand_total), 0) - COALESCE(sum(paid), 0)) as balance from {$this->db->dbprefix('purchases')} GROUP BY {$this->db->dbprefix('purchases')}.supplier_id ) FP";            
            $this->db
                ->select($this->db->dbprefix('companies') . ".id as supplier_id, company, name, phone, email, FP.total, FP.total_amount, FP.paid, FP.balance", FALSE)
                ->from("companies")
                ->join($p, 'FP.supplier_id=companies.id')
                ->where('companies.group_name', 'supplier')
                ->group_by('companies.id');
                
	$this->db->limit($limit,$offset);
	$q = $this->db->get();
	     
	 if ($q->num_rows() > 0) {
	    return $q->result();
	 } 
	 return false;
    }
    function getPurchasesReport($warehouse_id,$where,$page)
    {
        $start_date = $where['start_date'];
        $end_date = $where['end_date'];
        $supplier = $where['supplier_id'];

            $pi = "( SELECT purchase_id, product_id from {$this->db->dbprefix('purchase_items')} ";
            /*if ($product) {
                $pi .= " WHERE {$this->db->dbprefix('purchase_items')}.product_id = {$product} ";
            }*/
            $pi .= " GROUP BY {$this->db->dbprefix('purchase_items')}.purchase_id ) FPI";
            
            $this->db
                ->select("DATE_FORMAT({$this->db->dbprefix('purchases')}.date, '%Y-%m-%d %T') as date, reference_no, {$this->db->dbprefix('warehouses')}.name as wname, supplier,grand_total, paid, (grand_total-paid) as balance, {$this->db->dbprefix('purchases')}.status, {$this->db->dbprefix('purchases')}.id as id", FALSE)
                ->from('purchases')
                ->join($pi, 'FPI.purchase_id=purchases.id', 'left')
                ->join('warehouses', 'warehouses.id=purchases.warehouse_id', 'left');
                // ->group_by('purchases.id');

           /* if ($user) {
                $this->datatables->where('purchases.created_by', $user);
            }
            if ($product) {
                $this->datatables->where('FPI.product_id', $product, FALSE);
            }*/
            if ($supplier) {
                $this->db->where('purchases.supplier_id', $supplier);
            }
           /* if ($warehouse) {
                $this->datatables->where('purchases.warehouse_id', $warehouse);
            }*/
           /* if ($reference_no) {
                $this->datatables->like('purchases.reference_no', $reference_no, 'both');
            }*/
           /* if ($start_date) {
                $this->datatables->where($this->db->dbprefix('purchases').'.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }*/
        if($page){
           $this->db->limit($limit,$offset);
        }

        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
            $data[] = $row;
            }
            return $data;
        }
        return FALSE;
          

    }    
    public function purchases_report($warehouse_id,$where_array,$page) {
	
	   if ($page == "") {
                $page = 1;
        }

       $limit = $this->limit;
       $offset = ($page - 1) * $limit;

	$start_date = $where_array['start_date'];
	$end_date = $where_array['end_date'];
	$product = $where_array['product'];
	$supplier = $where_array['supplier'];
	$user = $where_array['user'];
	$reference_no = $where_array['reference_no'];
/*	
    ( SELECT id as sale_id, recipe_id as product_id, GROUP_CONCAT(CONCAT({$this->db->dbprefix('bil_items')}.recipe_name, '(', {$this->db->dbprefix('bil_items')}.quantity,')') SEPARATOR '|') as item_nane from {$this->db->dbprefix('bil_items')} ";*/

	
	/*$pi = "( SELECT purchase_id, product_id, (GROUP_CONCAT(CONCAT({$this->db->dbprefix('purchase_items')}.product_name, '(', {$this->db->dbprefix('purchase_items')}.quantity,')') SEPARATOR '|')) as item_nane from {$this->db->dbprefix('purchase_items')} ";
            if ($product) {
                $pi .= " WHERE {$this->db->dbprefix('purchase_items')}.product_id = {$product} ";
            }
            $pi .= " GROUP BY {$this->db->dbprefix('purchase_items')}.purchase_id ) FPI";

          
            $this->db
                ->select("DATE_FORMAT({$this->db->dbprefix('purchases')}.date, '%Y-%m-%d %T') as date, reference_no, {$this->db->dbprefix('warehouses')}.name as wname, supplier, (FPI.item_nane) as iname, grand_total, paid, (grand_total-paid) as balance, {$this->db->dbprefix('purchases')}.status, {$this->db->dbprefix('purchases')}.id as id", FALSE)
                ->from('purchases')
                ->join($pi, 'FPI.purchase_id=purchases.id', 'left')
                ->join('warehouses', 'warehouses.id=purchases.warehouse_id', 'left');*/
        $pi = "( SELECT purchase_id, product_id, (GROUP_CONCAT(CONCAT({$this->db->dbprefix('purchase_items')}.product_name, '(', {$this->db->dbprefix('purchase_items')}.quantity,')') SEPARATOR '|')) as item_nane from {$this->db->dbprefix('purchase_items')} ";
            if ($product) {
                $pi .= " WHERE {$this->db->dbprefix('purchase_items')}.product_id = {$product} ";
            }
            $pi .= " GROUP BY {$this->db->dbprefix('purchase_items')}.purchase_id ) FPI";
            $this->db
           /* $this->load->library('datatables');
            $this->datatables*/
                ->select("DATE_FORMAT({$this->db->dbprefix('purchases')}.date, '%Y-%m-%d %T') as date, reference_no, {$this->db->dbprefix('warehouses')}.name as wname, supplier, (FPI.item_nane) as iname, grand_total, paid, (grand_total-paid) as balance, {$this->db->dbprefix('purchases')}.status, {$this->db->dbprefix('purchases')}.id as id", FALSE)
                ->from('purchases')
                ->join($pi, 'FPI.purchase_id=purchases.id', 'left')
                ->join('warehouses', 'warehouses.id=purchases.warehouse_id', 'left');                
                // ->group_by('purchases.id');

            if ($user) {
                $this->db->where('purchases.created_by', $user);
            }
            if ($product) {
                $this->db->where('FPI.product_id', $product, FALSE);
            }
            if ($supplier) {
                $this->db->where('purchases.supplier_id', $supplier);
            }
            if ($warehouse_id) {
                $this->db->where('purchases.warehouse_id', $warehouse_id);
            }
            if ($reference_no) {
                $this->db->like('purchases.reference_no', $reference_no, 'both');
            }
            if ($start_date) {
                $this->db->where($this->db->dbprefix('purchases').'.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }
    $this->db->limit($limit,$offset);
	$q = $this->db->get();
	//print_R($this->db);exit;
	if ($q->num_rows() > 0) {
	    return $q->result();
	} 
	return false;
    }
    public function takeaway_report($warehouse_id,$where_array,$page) {

   /* if ($page == "") {
        $page = 1;
    }*/

    $limit = $this->limit;
    $offset = ($page - 1) * $limit;

     $limits = "";
        if ($page) {
            $limits = "LIMIT $offset, $limit";
        }


	$start_date = $where_array['start_date'];
	$end_date = $where_array['end_date'];
	
     if($start_date){
           $start_date = $start_date;
        }
        else{
            $start_date = date('Y-m-d');
        }
        if($end_date)
        {
            $end_date = $end_date;
        }
        else{
            $end_date = date('Y-m-d');
        }
        
	$where ='';
        if($warehouse_id != 0)
        {
            $where = "AND P.warehouse_id =".$warehouse_id."";
        }

        $takeaway = "SELECT DATE_FORMAT(P.date, '%d-%m-%Y') AS Orderdate,U.username,P.bill_number AS Bill_No,W.name branch,SUM(P.total-P.total_discount+CASE WHEN (P.tax_type= 1) THEN P.total_tax ELSE 0 END) AS grand_total,CP.address,CP.name,CONCAT('cash - ',SUM(DISTINCT CASE  WHEN ((PA.paid_by = 'cash') AND (SC.currency_id=2) AND (SC.amount!='')) THEN PA.amount 
WHEN ((PA.paid_by = 'cash' AND SC.currency_id=1 AND SC.amount!='')) THEN (SC.amount*SC.currency_rate) ELSE 0 END),' | CC - ',SUM(DISTINCT CASE
WHEN PA.paid_by = 'CC'  THEN PA.amount

ELSE 0 END),' | credit - ',SUM(DISTINCT CASE
WHEN PA.paid_by = 'credit'  THEN PA.amount

ELSE 0 END)) paid_by
        FROM " . $this->db->dbprefix('bils') . " P
        JOIN " . $this->db->dbprefix('users') . "  U ON P.created_by = U.id
        JOIN " . $this->db->dbprefix('sales') . " S ON S.id = P.sales_id
        JOIN " . $this->db->dbprefix('orders') . " O ON O.split_id = S.sales_split_id
        JOIN " . $this->db->dbprefix('companies') . " CP ON CP.id = O.customer_id
        JOIN " . $this->db->dbprefix('warehouses') . " W ON W.id = P.warehouse_id
        JOIN " . $this->db->dbprefix('payments') . " PA ON PA.bill_id = P.id
        JOIN " . $this->db->dbprefix('sale_currency') . " SC ON SC.bil_id = P.id  
            WHERE DATE(P.date) BETWEEN '".$start_date."' AND '".$end_date."' AND
             P.payment_status ='Completed' AND O.order_type = 2 ".$where."  GROUP BY P.id ".$limits."";
        /*echo $takeaway;die;*/
        $q = $this->db->query($takeaway);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    public function homedelivery_report($warehouse_id,$customer_id,$where_array,$page) {
    	/*if ($page == "") {
                $page = 1;
           }*/

        $limit = $this->limit;
        $offset = ($page - 1) * $limit;

        $limits = "";
        if ($page) {
            $limits = "LIMIT $offset, $limit";
        }

    	$start_date = $where_array['start_date'];
    	$end_date = $where_array['end_date'];

    if($start_date){
           $start_date = $start_date;
        }
        else{
            $start_date = date('Y-m-d');
        }
        if($end_date)
        {
            $end_date = $end_date;
        }
        else{
            $end_date = date('Y-m-d');
        }

	
	$where ='';
        if($warehouse_id != 0)
        {
            $where = "AND P.warehouse_id =".$warehouse_id."";
        }
        if($customer != 0)
        {
            $where .= "AND P.customer_id =".$customer."";
        }

        $HomeDelivery = "SELECT DATE_FORMAT(P.date, '%d-%m-%Y') AS Orderdate,U.username,P.bill_number AS Bill_No,W.name branch,SUM(P.total-P.total_discount+CASE WHEN (P.tax_type= 1) THEN P.total_tax ELSE 0 END) AS grand_total,CP.address,CP.name,CONCAT('cash - ',SUM(DISTINCT CASE  WHEN ((PA.paid_by = 'cash') AND (SC.currency_id=2) AND (SC.amount!='')) THEN PA.amount 
WHEN ((PA.paid_by = 'cash' AND SC.currency_id=1 AND SC.amount!='')) THEN (SC.amount*SC.currency_rate) ELSE 0 END),' | CC - ',SUM(DISTINCT CASE
WHEN PA.paid_by = 'CC'  THEN PA.amount

ELSE 0 END),' | credit - ',SUM(DISTINCT CASE
WHEN PA.paid_by = 'credit'  THEN PA.amount

ELSE 0 END)) paid_by,U1.first_name delivery_person,GP.name group_name
        FROM " . $this->db->dbprefix('bils') . " P
        JOIN " . $this->db->dbprefix('users') . "  U ON P.created_by = U.id
        JOIN " . $this->db->dbprefix('sales') . " S ON S.id = P.sales_id
        JOIN " . $this->db->dbprefix('orders') . " O ON O.split_id = S.sales_split_id
        JOIN " . $this->db->dbprefix('companies') . " CP ON CP.id = O.customer_id
        JOIN " . $this->db->dbprefix('customer_groups') . " GP ON GP.id = CP.customer_group_id
        JOIN " . $this->db->dbprefix('users') . " U1 ON U1.id = P.delivery_person_id
        JOIN " . $this->db->dbprefix('warehouses') . " W ON W.id = P.warehouse_id
        JOIN " . $this->db->dbprefix('payments') . " PA ON PA.bill_id = P.id
        JOIN " . $this->db->dbprefix('sale_currency') . " SC ON SC.bil_id = P.id  
            WHERE DATE(P.date) BETWEEN '".$start_date."' AND '".$end_date."' AND
             P.payment_status ='Completed' AND O.order_type = 2 ".$where."  GROUP BY P.id ".$limits." ";
        
        $q = $this->db->query($HomeDelivery);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    public function ordertiming_report($warehouse_id,$where_array,$page) {

    	/*if ($page == "") {
                $page = 1;
           }*/

        $limit = $this->limit;
        $offset = ($page - 1) * $limit;
        $limits = "";
        if ($page) {
            $limits = "LIMIT $offset, $limit";
        }

		$default_p_time = $this->Settings->default_preparation_time;
		$start_date = $where_array['start_date'];
		$end_date = $where_array['end_date'];

        if($start_date){
           $start_date = $start_date;
        }
        else{
            $start_date = date('Y-m-d');
        }
        if($end_date)
        {
            $end_date = $end_date;
        }
        else{
            $end_date = date('Y-m-d');
        }
	
	$where ='';
        if($warehouse_id != 0)
        {
            $where = "AND B.warehouse_id =".$warehouse_id."";
        }
	 if(!$where_array['table_whitelisted']){
           $where .= " AND O.table_whitelisted =0";
        }

        $Ordertime = "SELECT O.reference_no,O.id,R.name AS recipe_name,T.name AS table_name,TIMESTAMPDIFF(SECOND,OI.time_started,OI.time_end) AS prepared_time,OI.time_started,OI.time_end,U.username,CASE WHEN R.preparation_time!=0 THEN R.preparation_time ELSE ".$default_p_time."  END preparation_time
        FROM " . $this->db->dbprefix('orders') . " O
        JOIN " . $this->db->dbprefix('order_items') . " OI ON OI.sale_id = O.id
        JOIN " . $this->db->dbprefix('recipe') . " R ON R.id = OI.recipe_id 
        JOIN " . $this->db->dbprefix('bil_items') . " BI ON R.id = BI.recipe_id  
        JOIN " . $this->db->dbprefix('bils') . " B ON B.id = BI.bil_id and B.sales_id=OI.sale_id
        JOIN " . $this->db->dbprefix('users') . " U ON U.id = O.created_by 
        JOIN " . $this->db->dbprefix('restaurant_tables') . " T ON T.id = O.table_id
            WHERE DATE(O.date) BETWEEN '".$start_date."' AND '".$end_date."' AND B.payment_status ='Completed'".$where." group by O.reference_no,OI.recipe_id ".$limits."";
        
        $q = $this->db->query($Ordertime);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $default_preparation_time = ($row->preparation_time!=0)?round(($row->preparation_time/60),1):0;
                $row->default_preparation_time = "$default_preparation_time";

		/*$row->default_preparation_time = ($row->preparation_time!=0)?round(($row->preparation_time/60),1):0;*/
                $prepared_time = round(($row->prepared_time/60),1); 
                /*$row->preparedTime = round(($row->prepared_time/60),1);*/

                $prepared_time = round(($row->prepared_time/60),1);
                $row->preparedTime ="$prepared_time";

                $row->timediff = round(($row->preparedTime-$row->default_preparation_time),1);
                $row->timediff = (strpos($row->timediff, '-')===false)?'After '.trim($row->timediff,'-'):'Before '.trim($row->timediff,'-');
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    public function feedback_report($warehouse_id,$where_array,$page) {
	
    	if ($page == "") {
                $page = 1;
           }

        $limit = $this->limit;
        $offset = ($page - 1) * $limit;
        
        if ($warehouse_id) {
            $this->db
                ->select($this->db->dbprefix('recipe') . ".name as recipe_name,".$this->db->dbprefix('restaurant_tables') . ".name as table_name, ".$this->db->dbprefix('feedback') . ".status as status,".$this->db->dbprefix('companies') . ".customer_group_name as customer_group_name,message,".$this->db->dbprefix('warehouses') . ".name as warehouses", FALSE)
                ->from("feedback")
                ->join('companies', 'companies.id=feedback.customer_id')
                ->join('restaurant_tables', 'restaurant_tables.id=feedback.table_id')
                ->join('recipe', 'recipe.id = feedback.item_id')
                ->join('warehouses', 'warehouses.id=feedback.warehouse_id');
                
        } else {
            $this->db
                ->select($this->db->dbprefix('recipe') . ".name as recipe_name,".$this->db->dbprefix('restaurant_tables') . ".name as table_name, ".$this->db->dbprefix('feedback') . ".status as status,".$this->db->dbprefix('companies') . ".customer_group_name as customer_group_name,message,".$this->db->dbprefix('warehouses') . ".name as warehouses", FALSE)
                ->from("feedback")
                ->join('companies', 'companies.id=feedback.customer_id')
                ->join('restaurant_tables', 'restaurant_tables.id=feedback.table_id')
                ->join('recipe', 'recipe.id = feedback.item_id')
                ->join('warehouses', 'warehouses.id=feedback.warehouse_id');
                
        }
        $this->db->limit($limit,$offset);
        $q = $this->db->get();
	     //print_R($this->db);exit;
	 if ($q->num_rows() > 0) {
	    return $q->result();
	 } 
	 return false;
    }
	public function GetAllwarehouse($page){
		$q = $this->db->get('warehouses');
		if ($q->num_rows() > 0) {
			
			foreach (($q->result()) as $row) {
				$data[] = $row;
			}
			return $data;
		}
	    return FALSE;
	}
	public function GetAllusers($page){
		$q = $this->db->get('users');
		if ($q->num_rows() > 0) {
			
			foreach (($q->result()) as $row) {
				$data[] = $row;
			}
			return $data;
		}
	    return FALSE;
	}	
	public function GetAllBrand($page){
		$q = $this->db->get('brands');
		if ($q->num_rows() > 0) {
			
			foreach (($q->result()) as $row) {
				$data[] = $row;
			}
			return $data;
		}
	    return FALSE;
	}
	public function GetAllCategories($page){
		$q = $this->db->get('categories');
		if ($q->num_rows() > 0) {
			
			foreach (($q->result()) as $row) {
				$data[] = $row;
			}
			return $data;
		}
	    return FALSE;
	}
    public function staff_report($where_array,$page) {

	/*if ($page == "") {
	    $page = 1;
	}*/
    
	$limit = $this->limit;
	$offset = ($page - 1) * $limit;	   
	$this->db->select($this->db->dbprefix('users').".id as id, first_name, last_name, email, company, ".$this->db->dbprefix('groups').".name, active");
	$this->db->from("users");
	$this->db->join('groups', 'users.group_id=groups.id', 'left');
	$this->db->where('company_id', NULL);
	if (!$this->Owner) {
	    $this->db->where('group_id !=', 1);
	}
	$this->db->group_by('users.id');
    if($page){
        $this->db->limit($limit,$offset);
    }
	
    
	$q = $this->db->get();
	if ($q->num_rows() > 0) {
	    foreach (($q->result()) as $row) {
		$data[] = $row;
	    }
	    return $data;
	}
	return FALSE;
    }
    public function adjustments_report($warehouse_id,$where_array,$page) {
	
	$start_date = $where_array['start_date'];
	$end_date = $where_array['end_date'];
	$product = $where_array['product'];
	$user = $where_array['user'];
	$reference_no = $where_array['reference_no'];
	$serial = $where_array['serial'];
	if ($page == "") {
	    $page = 1;
	}
    
	$limit = $this->limit;
	$offset = ($page - 1) * $limit;	   
	
	 $ai = "( SELECT adjustment_id, product_id, serial_no, GROUP_CONCAT(CONCAT({$this->db->dbprefix('products')}.name, '__', (CASE WHEN {$this->db->dbprefix('adjustment_items')}.type  = 'subtraction' THEN (0-{$this->db->dbprefix('adjustment_items')}.quantity) ELSE {$this->db->dbprefix('adjustment_items')}.quantity END)) SEPARATOR '___') as item_nane from {$this->db->dbprefix('adjustment_items')} LEFT JOIN {$this->db->dbprefix('products')} ON {$this->db->dbprefix('products')}.id={$this->db->dbprefix('adjustment_items')}.product_id ";
            if ($product || $serial) { $ai .= " WHERE "; }
            if ($product) {
                $ai .= " {$this->db->dbprefix('adjustment_items')}.product_id = {$product} ";
            }
            if ($product && $serial) { $ai .= " AND "; }
            if ($serial) {
                $ai .= " {$this->db->dbprefix('adjustment_items')}.serial_no LIKe '%{$serial}%' ";
            }
            $ai .= " GROUP BY {$this->db->dbprefix('adjustment_items')}.adjustment_id ) FAI";
            
            $this->db
            ->select("DATE_FORMAT(date, '%Y-%m-%d %T') as date, reference_no, warehouses.name as wh_name, CONCAT({$this->db->dbprefix('users')}.first_name, ' ', {$this->db->dbprefix('users')}.last_name) as created_by, note, FAI.item_nane as iname, {$this->db->dbprefix('adjustments')}.id as id", FALSE)
            ->from('adjustments')
            ->join($ai, 'FAI.adjustment_id=adjustments.id', 'left')
            ->join('users', 'users.id=adjustments.created_by', 'left')
            ->join('warehouses', 'warehouses.id=adjustments.warehouse_id', 'left');

            if ($user) {
                $this->db->where('adjustments.created_by', $user);
            }
            if ($product) {
                $this->db->where('FAI.product_id', $product);
            }
            if ($serial) {
                $this->db->like('FAI.serial_no', $serial);
            }
            if ($warehouse_id) {
                $this->db->where('adjustments.warehouse_id', $warehouse_id);
            }
            if ($reference_no) {
                $this->db->like('adjustments.reference_no', $reference_no, 'both');
            }
            if ($start_date) {
                $this->db->where($this->db->dbprefix('adjustments').'.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

	$this->db->limit($limit,$offset);
    
	$q = $this->db->get();
	if ($q->num_rows() > 0) {
	    foreach (($q->result()) as $row) {
		$data[] = $row;
	    }
	    return $data;
	}
	return FALSE;
    }
    
    public function products_report($warehouse_id,$where_array,$page) {

       /* $product = $this->input->get('product') ? $this->input->get('product') : NULL;
        
        $warehouse_id = $this->input->get('warehouse') ? $this->input->get('warehouse') : NULL;*/
        /*$start_date = $this->input->get('start_date') ? $this->input->get('start_date') : NULL;
        $end_date = $this->input->get('end_date') ? $this->input->get('end_date') : NULL;*/

        $warehouse_id = $where_array['warehouse_id'];
        $product = $where_array['product'];

        $start_date = $where_array['start_date'];
        $end_date = $where_array['end_date'];

        $purchased = "
                (
                    SELECT
                        recipe_id,product_id,SU.name,SP.price,SRP.max_quantity,SRU.name unit_name,
         
                        SUM(CASE
                         WHEN (SU.name='Kg' AND SRU.name='Gram') THEN (SP.price/SRU.operation_value)*SRP.max_quantity
                         WHEN (SU.name='Kg' AND SRU.name='Kg') THEN SP.price*SRP.max_quantity
                         
                         WHEN (SU.name='Litre' AND SRU.name='Millilitre') THEN (SP.price/SRU.operation_value)*SRP.max_quantity
                         WHEN (SU.name='Litre' AND SRU.name='Litre') THEN SP.price*SRP.max_quantity
                         
                         WHEN (SU.name='Package' AND SRU.name='Pieces') THEN (SP.price/SRU.operation_value)*SRP.max_quantity
                         WHEN (SU.name='Package' AND SRU.name='Package') THEN SP.price*SRP.max_quantity
                 
                        ELSE 0 END) purchased
                
                    FROM ".$this->db->dbprefix('recipe_products')." SRP         
                    JOIN ".$this->db->dbprefix('products')." SP on SRP.product_id=SP.id 
                    JOIN ".$this->db->dbprefix('units')." SU on SU.id=SP.unit 
                    JOIN ".$this->db->dbprefix('units')." SRU on SRU.id=SRP.units_id
                    group by SRP.recipe_id
                    order by product_id
                ) P";
        $sold = "
            (
                SELECT recipe_id,SUM(quantity) as quantity,SUM(SBI.unit_price*quantity) as sold FROM ".$this->db->dbprefix('bil_items')." SBI
                            join ".$this->db->dbprefix('bils')." SB on SBI.bil_id=SB.id
                            where SB.payment_status='completed'";
            if ($start_date) {
                $sold .=' AND DATE_FORMAT(SB.date, "%Y-%m-%d") >="'.$start_date.'"';
            }
            if ($end_date) {
             $sold .=' AND DATE_FORMAT(SB.date, "%Y-%m-%d") <="'.$end_date.'"';
            }
            if($warehouse_id != 0){
                $sold .=' AND SBI.warehouse_id='.$warehouse_id;    
            }
           if(!$where_array['table_whitelisted']){
               $sold .= " AND SB.table_whitelisted =0";
            }else{
                $sold .= " OR SB.table_whitelisted = ".$where_array['table_whitelisted']."";
            }            
           $sold .= " group by SBI.recipe_id
            ) SLSold";

            $this->db
                    ->select(
                        $this->db->dbprefix('recipe').".code,
                ".$this->db->dbprefix('recipe').".name,
                SUM(P.purchased*SLSold.quantity) as purchased,
                SLSold.sold,
                SUM(SLSold.sold-(P.purchased*SLSold.quantity)) as profitloss,
                SLSold.Quantity
                    ")
                    ->from($this->db->dbprefix('recipe'))
                    ->join($purchased, $this->db->dbprefix('recipe').".id=P.recipe_id")
                    ->join($sold, $this->db->dbprefix('recipe').".id=SLSold.recipe_id")
                    ->group_by($this->db->dbprefix('recipe').".id");
                    
            if ($product) {
                $this->datatables->where($this->db->dbprefix('recipe') . ".id", $product);
            }

        if ($page) {
           $this->db->limit($limit,$offset);
        }

        $q = $this->db->get();
        if ($q->num_rows() > 0) {
        foreach (($q->result()) as $row) {
           $data[] = $row;
        }
        return $data;
        }
        return FALSE;
    }

    public function products_report_old($warehouse_id,$where_array,$page) {
	
	$start_date = $where_array['start_date'];
	$end_date = $where_array['end_date'];
     if($start_date){
           $start_date = $start_date;
        }
        else{
            $start_date = date('Y-m-d');
        }
        if($end_date)
        {
            $end_date = $end_date;
        }
        else{
            $end_date = date('Y-m-d');
        }

	$product = $where_array['product'];
	
	if ($page == "") {
	    $page = 1;
	}
    
	$limit = $this->limit;
	$offset = ($page - 1) * $limit;	   
	
	$purchased = "
                (
                    SELECT
                        recipe_id,product_id,SU.name,SP.price,SRP.max_quantity,SRU.name unit_name,
         
                        SUM(CASE
                         WHEN (SU.name='Kg' AND SRU.name='Gram') THEN (SP.price/SRU.operation_value)*SRP.max_quantity
                         WHEN (SU.name='Kg' AND SRU.name='Kg') THEN SP.price*SRP.max_quantity
                         
                         WHEN (SU.name='Litre' AND SRU.name='Millilitre') THEN (SP.price/SRU.operation_value)*SRP.max_quantity
                         WHEN (SU.name='Litre' AND SRU.name='Litre') THEN SP.price*SRP.max_quantity
                         
                         WHEN (SU.name='Package' AND SRU.name='Pieces') THEN (SP.price/SRU.operation_value)*SRP.max_quantity
                         WHEN (SU.name='Package' AND SRU.name='Package') THEN SP.price*SRP.max_quantity
                 
                        ELSE 0 END) purchased
                
                    FROM ".$this->db->dbprefix('recipe_products')." SRP         
                    JOIN ".$this->db->dbprefix('products')." SP on SRP.product_id=SP.id 
                    JOIN ".$this->db->dbprefix('units')." SU on SU.id=SP.unit 
                    JOIN ".$this->db->dbprefix('units')." SRU on SRU.id=SRP.units_id
                    group by SRP.recipe_id
                    order by product_id
                ) P";
        $sold = "
            (
                SELECT recipe_id,SUM(quantity) as quantity,SUM(SBI.unit_price*quantity) as sold FROM ".$this->db->dbprefix('bil_items')." SBI
                            join ".$this->db->dbprefix('bils')." SB on SBI.bil_id=SB.id
                            where SB.payment_status='completed'";
            if ($start_date) {
                $sold .=' AND DATE_FORMAT(SB.date, "%Y-%m-%d") >="'.$start_date.'"';
            }
            if ($end_date) {
             $sold .=' AND DATE_FORMAT(SB.date, "%Y-%m-%d") <="'.$end_date.'"';
            }
            if($warehouse_id != 0){
                $sold .=' AND SBI.warehouse_id='.$warehouse_id;    
            }
            if(!$this->Owner && !$this->Admin){
                $sold .= " AND SB.table_whitelisted =0";
            }
         $sold .= " group by SBI.recipe_id
            ) SLSold";

	 $this->db
            ->select(
                    $this->db->dbprefix('recipe').".code,
            ".$this->db->dbprefix('recipe').".name,
            SUM(P.purchased*SLSold.quantity) as purchased,
            SLSold.sold,
            SUM(SLSold.sold-(P.purchased*SLSold.quantity)) as profitloss,
            SLSold.Quantity
                ")
                ->from($this->db->dbprefix('recipe'))
                ->join($purchased, $this->db->dbprefix('recipe').".id=P.recipe_id")
                ->join($sold, $this->db->dbprefix('recipe').".id=SLSold.recipe_id")
                ->group_by($this->db->dbprefix('recipe').".id");
        if ($product) {
            $this->datatables->where($this->db->dbprefix('recipe') . ".id", $product);
        }
            

	$this->db->limit($limit,$offset);
    
	$q = $this->db->get();
	if ($q->num_rows() > 0) {
	    foreach (($q->result()) as $row) {
		$data[] = $row;
	    }
	    return $data;
	}
	return FALSE;
    }
    public function void_bills_report($warehouse_id,$where_array,$page) {
	
	$start_date = $where_array['start_date'];
	$end_date = $where_array['end_date'];
    if($start_date){
           $start_date = $start_date;
        }
        else{
            $start_date = date('Y-m-d');
        }
        if($end_date)
        {
            $end_date = $end_date;
        }
        else{
            $end_date = date('Y-m-d');
        }
	
	/*if ($page == "") {
	    $page = 1;
	}*/
    
	$limit = $this->limit;
	$offset = ($page - 1) * $limit;	

    $limits = "";
        if ($page) {
            $limits = "LIMIT $offset, $limit";
        }  
	
	$where ='';
        if($warehouse_id != 0)
        {
            $where = "AND P.warehouse_id =".$warehouse_id."";
        }

          $Void_Bills = "SELECT B.bill_number,DATE_FORMAT(B.date, '%d-%m-%Y') date,SUM(BI.subtotal-BI.item_discount-BI.off_discount-BI.input_discount+CASE WHEN (BI.tax_type = 1) THEN BI.tax ELSE 0 END) as bill_amt,BI.quantity,OI.id,BI.recipe_name  AS recipename,OI.order_item_cancel_note,T.name AS table_name,UO.username AS created_by,U.username AS Canceled,OI.unit_price,K.id AS kot,W.name as branch
        FROM " . $this->db->dbprefix('bils') . " B
       JOIN  " . $this->db->dbprefix('bil_items') . " BI ON BI.bil_id =B.id
       JOIN  " . $this->db->dbprefix('sales') . " S ON S.id =B.sales_id
       JOIN  " . $this->db->dbprefix('orders') . " O ON O.split_id =S.sales_split_id
        JOIN " . $this->db->dbprefix('order_items') . " OI ON OI.sale_id = O.id
        JOIN " . $this->db->dbprefix('recipe') . " R ON R.id = OI.recipe_id 
        JOIN " . $this->db->dbprefix('kitchen_orders') . " K ON K.id = OI.kitchen_id 
        JOIN " . $this->db->dbprefix('users') . " U ON U.id = OI.order_item_cancel_id 
        JOIN " . $this->db->dbprefix('users') . " UO ON UO.id = O.created_by 
        JOIN " . $this->db->dbprefix('restaurant_tables') . " T ON T.id = O.table_id
        JOIN " . $this->db->dbprefix('warehouses') . " W ON W.id = B.warehouse_id
            WHERE DATE(B.date) BETWEEN '".$start_date."' AND '".$end_date."' AND B.bil_status= 'Cancelled' ".$where." GROUP BY BI.id ".$limits. "";

        $q = $this->db->query($Void_Bills);    
	if ($q->num_rows() > 0) {
	    foreach (($q->result()) as $row) {
		$data[] = $row;
	    }
	    return $data;
	}
	return FALSE;
    }
    public function products_expiry_report($warehouse_id,$where_array,$page) {
	
	$date = date('Y-m-d', strtotime('+3 months'));
	if ($page == "") {
	    $page = 1;
	}
    
	$limit = $this->limit;
	$offset = ($page - 1) * $limit;	   
	$base_url = base_url()."assets/uploads/thumbs/";
       if ($warehouse_id) {
            $this->db
                ->select("CONCAT('".$base_url."', image) as image, product_code, product_name, quantity_balance, warehouses.name, expiry")
                ->from('purchase_items')
                ->join('products', 'products.id=purchase_items.product_id', 'left')
                ->join('warehouses', 'warehouses.id=purchase_items.warehouse_id', 'left')
                ->where('warehouse_id', $warehouse_id)
                ->where('expiry !=', NULL)->where('expiry !=', '0000-00-00')
                ->where('expiry <', $date);
        } else {
            $this->db
                ->select("CONCAT('".$base_url."', image) as image, product_code, product_name, quantity_balance, warehouses.name, expiry")
                ->from('purchase_items')
                ->join('products', 'products.id=purchase_items.product_id', 'left')
                ->join('warehouses', 'warehouses.id=purchase_items.warehouse_id', 'left')
                ->where('expiry !=', NULL)->where('expiry !=', '0000-00-00')
                ->where('expiry <', $date);
        }
	$this->db->limit($limit,$offset);
	$q = $this->db->get();
	if ($q->num_rows() > 0) {
	    foreach (($q->result()) as $row) {
		$data[] = $row;
	    }
	    return $data;
	}
	return FALSE;
    }
    public function best_sellers_report($warehouse_id,$where_array,$page) {
	
	
	if ($page == "") {
	    $page = 1;
	}
    
	$limit = $this->limit;
	$offset = ($page - 1) * $limit;	   
	
	$y1 = date('Y', strtotime('-1 month'));
        $m1 = date('m', strtotime('-1 month'));  
        
        $m1sdate = $y1.'-'.$m1.'-01 00:00:00';
        $m1edate = $y1.'-'.$m1.'-'. days_in_month($m1, $y1) . ' 23:59:59';
        $data['m1'] = date('M Y', strtotime($y1.'-'.$m1));
        $data['m1bs'] = $this->getBestSeller($m1sdate, $m1edate, $warehouse_id);        


        $y2 = date('Y', strtotime('-2 months'));
        $m2 = date('m', strtotime('-2 months'));
        $m2sdate = $y2.'-'.$m2.'-01 00:00:00';
        $m2edate = $y2.'-'.$m2.'-'. days_in_month($m2, $y2) . ' 23:59:59';
        $data['m2'] = date('M Y', strtotime($y2.'-'.$m2));
        $data['m2bs'] = $this->getBestSeller($m2sdate, $m2edate, $warehouse_id);      

        $y3 = date('Y', strtotime('-3 months'));
        $m3 = date('m', strtotime('-3 months'));
        $m3sdate = $y3.'-'.$m3.'-01 23:59:59';
        $data['m3'] = date('M Y', strtotime($y3.'-'.$m3)).' - '.$this->data['m1'];
        $data['m3bs'] = $this->getBestSeller($m3sdate, $m1edate, $warehouse_id);        

        $y4 = date('Y', strtotime('-12 months'));
        $m4 = date('m', strtotime('-12 months'));
        $m4sdate = $y4.'-'.$m4.'-01 23:59:59';
        $data['m4'] = date('M Y', strtotime($y4.'-'.$m4)).' - '.$this->data['m1'];
        $data['m4bs'] = $this->getBestSeller($m4sdate, $m1edate, $warehouse_id);
        
	
	return $data;
	
	return FALSE;
    }
    function getBestSeller($start_date, $end_date, $warehouse_id){	
	$this->db
            ->select("recipe_name, recipe_code")->select_sum('quantity')
            ->join('bils', 'bils.id = bil_items.bil_id', 'left')
            ->where('date >=', $start_date)->where('date <=', $end_date)
            ->group_by('recipe_name, recipe_code')->order_by('sum(quantity)', 'desc')->limit(10);
        if ($warehouse_id) {
            $this->db->where('bil_items.warehouse_id', $warehouse_id);
        }
	//$this->db->limit($limit,$offset);
        $q = $this->db->get('bil_items');
	if ($q->num_rows() > 0) {	    
	    return $q->result();
	}
	return false;
    }
    
    public function warehouse_stock_report($warehouse_id,$where_array,$page) {
	
	if ($page == "") {
	    $page = 1;
	}
	
	$limit = $this->limit;
	$offset = ($page - 1) * $limit;
	if($warehouse_id!='') :
	    $q = $this->db->query("SELECT SUM(by_price) as stock_by_price, SUM(by_cost) as stock_by_cost FROM ( Select sum(COALESCE(" . $this->db->dbprefix('warehouses_products') . ".quantity, 0))*price as by_price, sum(COALESCE(" . $this->db->dbprefix('warehouses_products') . ".quantity, 0))*cost as by_cost FROM " . $this->db->dbprefix('products') . " JOIN " . $this->db->dbprefix('warehouses_products') . " ON " . $this->db->dbprefix('warehouses_products') . ".product_id=" . $this->db->dbprefix('products') . ".id WHERE " . $this->db->dbprefix('warehouses_products') . ".warehouse_id = ? GROUP BY " . $this->db->dbprefix('products') . ".id )a", array($warehouse_id)." LIMIT $offset, $limit");
	else:
	    $q = $this->db->query("SELECT SUM(by_price) as stock_by_price, SUM(by_cost) as stock_by_cost FROM ( Select COALESCE(sum(" . $this->db->dbprefix('warehouses_products') . ".quantity), 0)*price as by_price, COALESCE(sum(" . $this->db->dbprefix('warehouses_products') . ".quantity), 0)*cost as by_cost FROM " . $this->db->dbprefix('products') . " JOIN " . $this->db->dbprefix('warehouses_products') . " ON " . $this->db->dbprefix('warehouses_products') . ".product_id=" . $this->db->dbprefix('products') . ".id GROUP BY " . $this->db->dbprefix('products') . ".id )a LIMIT $offset, $limit");
	endif;

        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    public function monthly_sales_report_old($warehouse_id,$where_array) {
	$year = $where_array['year'];
	$month = $where_array['month'];
	
	$myQuery = "SELECT DATE_FORMAT( date,  '%M' ) AS date,SUM( COALESCE( total_tax, 0 ) ) AS tax, SUM( COALESCE( recipe_tax, 0 ) ) AS tax1, SUM( COALESCE( order_tax, 0 ) ) AS tax2, SUM( COALESCE( grand_total, 0 ) ) AS total,SUM(total-CASE WHEN (tax_type= 0) THEN total_tax ELSE 0 END) AS total, SUM( COALESCE( total_discount, 0 ) ) AS discount, SUM( COALESCE( shipping, 0 ) ) AS shipping,SUM(total-total_discount+CASE WHEN (tax_type= 1) THEN total_tax ELSE 0 END) AS grand_total
            FROM " . $this->db->dbprefix('bils') . " WHERE ";
        if ($warehouse_id) {
            $myQuery .= " warehouse_id = {$warehouse_id} AND ";
        }
	if(!$where_array['table_whitelisted']){
           $myQuery .= " table_whitelisted =0 AND ";
        }
        $myQuery .= " DATE_FORMAT( date,  '%Y-%m' ) =  '{$year}-{$month}'
            GROUP BY date_format( date, '%c' ) ORDER BY date_format( date, '%c' ) ASC";
        $q = $this->db->query($myQuery, false);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;

    }
    public function getMonthlySales($year, $warehouse_id = NULL)
    {
        $myQuery = "SELECT DATE_FORMAT( date,  '%M' ) AS month,SUM(total-CASE WHEN (tax_type= 0) THEN total_tax ELSE 0 END) AS total, SUM( COALESCE( total_discount, 0 ) ) AS discount,SUM( COALESCE( total_tax, 0 ) ) AS tax,   SUM( COALESCE( shipping, 0 ) ) AS shipping,SUM(total-total_discount+CASE WHEN (tax_type= 1) THEN total_tax ELSE 0 END) AS grand_total
            FROM " . $this->db->dbprefix('bils') . " WHERE payment_status ='Completed' AND ";
        if ($warehouse_id) {
            $myQuery .= " warehouse_id = {$warehouse_id} AND ";
        }
        
/*if(!$this->Owner && !$this->Admin){
            $myQuery .= " table_whitelisted =0 AND";
        }*/
        $myQuery .= " DATE_FORMAT( date,  '%Y' ) =  '{$year}'
            GROUP BY date_format( date, '%c' ) ORDER BY date_format( date, '%c' ) ASC";
            
        $q = $this->db->query($myQuery, false);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    public function getDailySales($year, $month, $warehouse_id = NULL)
    {

        $myQuery = "SELECT DATE_FORMAT( date,  '%d-%m-%Y') AS date, SUM( COALESCE( total_tax, 0 ) ) AS tax,  SUM( COALESCE( total_discount, 0 ) ) AS discount, SUM( COALESCE( shipping, 0 ) ) AS shipping,SUM(total-CASE WHEN (tax_type= 0) THEN total_tax ELSE 0 END) AS total,SUM(total-total_discount+CASE WHEN (tax_type= 1) THEN total_tax ELSE 0 END) AS grand_total
            FROM " . $this->db->dbprefix('bils') . " WHERE payment_status ='Completed'AND ";

        if ($warehouse_id) {
            $myQuery .= " warehouse_id = {$warehouse_id} AND ";
        }
        if(!$this->Owner && !$this->Admin){
            $myQuery .= " table_whitelisted =0 AND";
        }
        $myQuery .= " DATE_FORMAT( date,  '%Y-%c' ) =  '{$year}-{$month}'
            GROUP BY DATE_FORMAT( date,  '%e' ) ORDER BY DATE_FORMAT( date,  '%d-%m-%Y') ASC";
           
        $q = $this->db->query($myQuery, false);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    public function getDailyPurchases($year, $month, $warehouse_id = NULL)
    {
        $myQuery = "SELECT DATE_FORMAT( date,  '%d-%m-%Y' ) AS date, SUM( COALESCE( product_tax, 0 ) ) AS product_tax, SUM( COALESCE( order_tax, 0 ) ) AS order_tax, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( total_discount, 0 ) ) AS discount, SUM( COALESCE( shipping, 0 ) ) AS shipping
            FROM " . $this->db->dbprefix('purchases') . " WHERE ";
        if ($warehouse_id) {
            $myQuery .= " warehouse_id = {$warehouse_id} AND ";
        }
        $myQuery .= " DATE_FORMAT( date,  '%Y-%c' ) =  '{$year}-{$month}'
            GROUP BY DATE_FORMAT( date,  '%e' ) ORDER BY DATE_FORMAT( date,  '%d-%m-%Y') ASC";
            
        $q = $this->db->query($myQuery, false);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
        public function getMonthlyPurchases($year, $warehouse_id = NULL)
    {
        $myQuery = "SELECT DATE_FORMAT( date,  '%M' ) AS month, SUM( COALESCE( product_tax, 0 ) ) AS product_tax, SUM( COALESCE( order_tax, 0 ) ) AS order_tax, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( total_discount, 0 ) ) AS discount, SUM( COALESCE( shipping, 0 ) ) AS shipping
            FROM " . $this->db->dbprefix('purchases') . " WHERE";
/*for future dont delete*/

     /*$myQuery = "SELECT DATE_FORMAT( date,  '%M' ) AS month, SUM( COALESCE( product_tax, 0 ) ) AS product_tax, SUM( COALESCE( order_tax, 0 ) ) AS order_tax, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( total_discount, 0 ) ) AS discount, SUM( COALESCE( shipping, 0 ) ) AS shipping
    FROM " . $this->db->dbprefix('purchases') . " WHERE grand_total > 0 AND ";*/

        if ($warehouse_id) {
            $myQuery .= " warehouse_id = {$warehouse_id} AND ";
        }
        $myQuery .= " DATE_FORMAT( date,  '%Y' ) =  '{$year}'
            GROUP BY date_format( date, '%c' ) ORDER BY date_format( date, '%c' ) ASC ";
            
        $q = $this->db->query($myQuery, false);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

   
    public function getUser($id = NULL) {
        if (!$id) {
            $id = $this->session->userdata('user_id');
        }
        $q = $this->db->get_where('users', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
  public function getProducts()
    {
        $this->db->select('id, code, name');
        $q = $this->db->get('products');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }    
public function getStockVariance($start,$product_id,$warehouse_id,$page)
    {   
        $where ='';
        $prd_where ='';
        $prd_where1 ='';
        if($warehouse_id != 0)
        {
            $where = "AND P.warehouse_id =".$warehouse_id."";
        }
        if($product_id != 0)
        {
            $prd_where = "AND PR.id =".$product_id."";
        }
        if($product_id != 0)
        {
            $prd_where1 = "WHERE PR.id =".$product_id."";
        }

        $limit = $this->limit;
        $offset = ($page - 1) * $limit; 

        $limits = "";            
        if ($page) {
           $limits = "LIMIT $offset, $limit";
        }
$originalDate = $start;

$newDate = date("d-m-Y", strtotime($originalDate));

        $myQuery = "SELECT '".$newDate."'

 AS bill_date,PR.id,PR.name,PU.name AS productunit,(CASE
        WHEN SU.name = 'Gram' and PU.name = 'Kg'  AND P.payment_status ='Completed'  ".$prd_where." AND DATE(P.date) = '".$start."'   THEN SUM(RP.max_quantity)/1000
        WHEN SU.name = 'Kg' and PU.name = 'Kg' AND P.payment_status ='Completed' ".$prd_where." AND DATE(P.date) = '".$start."'  THEN SUM(RP.max_quantity)
        WHEN SU.name = 'Millilitre' and PU.name = 'Litre'  AND P.payment_status ='Completed' ".$prd_where." AND DATE(P.date) = '".$start."'  THEN SUM(RP.max_quantity)/1000
        WHEN SU.name = 'Litre' and PU.name = 'Litre' AND P.payment_status ='Completed'  ".$prd_where." AND DATE(P.date) = '".$start."' THEN SUM(RP.max_quantity)
        WHEN SU.name = 'Pieces' and PU.name = 'Package' AND P.payment_status ='Completed' ".$prd_where."  AND DATE(P.date) = '".$start."' THEN SUM(RP.max_quantity)/12
        WHEN SU.name = 'Package' and PU.name = 'Package' AND P.payment_status ='Completed' ".$prd_where." AND DATE(P.date) = '".$start."' THEN SUM(RP.max_quantity)        
        ELSE 0
        END) AS soldQty,(COALESCE( PI.given_quantity, 0 ) ) AS given_quantity
        FROM srampos_products PR
        LEFT JOIN srampos_recipe_products RP ON RP.product_id = PR.id  
        LEFT JOIN srampos_bil_items BI ON  BI.recipe_id  = RP.recipe_id
        LEFT JOIN srampos_bils P ON P.id  = BI.bil_id
        LEFT JOIN srampos_production_items AS PI ON PI.product_id = PR.id 
        LEFT JOIN srampos_production AS PN  ON PN.id = PI.production_id
        LEFT JOIN srampos_units SU ON SU.id = RP.units_id
        LEFT JOIN srampos_units PU ON PU.id = PR.unit ".$prd_where1."
        GROUP by PR.name ".$limits." ";
        
        $q = $this->db->query($myQuery);

        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }    
 public function getTotalPurchases($start, $end, $warehouse_id = NULL)
    {

        $this->db->select('count(id) as total,IFNULL(sum(COALESCE(grand_total, 0)), 0) as total_amount, IFNULL(sum(COALESCE(paid, 0)), 0) as paid,IFNULL(sum(COALESCE(total_tax, 0)), 0) as tax', FALSE)
            ->where('status !=', 'pending')
            ->where('date BETWEEN ' . $start . ' and ' . $end);
        if ($warehouse_id) {
            $this->db->where('warehouse_id', $warehouse_id);
        }
        $q = $this->db->get('purchases');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    public function getTotalSales($start, $end, $warehouse_id = NULL)
    {
        $this->db->select('count(id) as total, IFNULL(sum(COALESCE(grand_total, 0)), 0) as total_amount,IFNULL(sum(COALESCE(paid, 0)), 0) as paid,IFNULL(sum(COALESCE(total_tax, 0)), 0) as tax', FALSE)
            ->where('sale_status !=', 'pending')
            ->where('date BETWEEN ' . $start . ' and ' . $end);
        if ($warehouse_id) {
            $this->db->where('warehouse_id', $warehouse_id);
        }
        $q = $this->db->get('sales');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    } 
    public function getTotalExpenses($start, $end, $warehouse_id = NULL)
    {

        $this->db->select('count(id) as total, IFNULL(sum(COALESCE(amount, 0)), 0) as total_amount', TRUE)
            ->where('date BETWEEN ' . $start . ' and ' . $end);
        if ($warehouse_id) {
            $this->db->where('warehouse_id', $warehouse_id);
        }
        $q = $this->db->get('expenses');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    public function getTotalPaidAmount($start, $end)
    {
        $this->db->select('count(id) as total, IFNULL(sum(COALESCE(amount, 0)), 0) as total_amount', FALSE)
            ->where('type', 'sent')
            ->where('date BETWEEN ' . $start . ' and ' . $end);
        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }           
    public function getTotalReceivedAmount($start, $end)
    {
        $this->db->select('count(id) as total, IFNULL(sum(COALESCE(amount, 0)), 0) as total_amount', FALSE)
            ->where('type', 'received')
            ->where('date BETWEEN ' . $start . ' and ' . $end);
        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getTotalReceivedCashAmount($start, $end)
    {
        $this->db->select('count(id) as total, IFNULL(sum(COALESCE(amount, 0)), 0) as total_amount', FALSE)
            ->where('type', 'received')->where('paid_by', 'cash')
            ->where('date BETWEEN ' . $start . ' and ' . $end);
        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getTotalReceivedCCAmount($start, $end)
    {
        $this->db->select('count(id) as total, IFNULL(sum(COALESCE(amount, 0)), 0) as total_amount', FALSE)
            ->where('type', 'received')->where('paid_by', 'CC')
            ->where('date BETWEEN ' . $start . ' and ' . $end);
        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getTotalReceivedChequeAmount($start, $end)
    {
        $this->db->select('count(id) as total, IFNULL(sum(COALESCE(amount, 0)), 0) as total_amount', FALSE)
            ->where('type', 'received')->where('paid_by', 'Cheque')
            ->where('date BETWEEN ' . $start . ' and ' . $end);
        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getTotalReceivedPPPAmount($start, $end)
    {
        $this->db->select('count(id) as total, IFNULL(sum(COALESCE(amount, 0)), 0) as total_amount', FALSE)
            ->where('type', 'received')->where('paid_by', 'ppp')
            ->where('date BETWEEN ' . $start . ' and ' . $end);
        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getTotalReceivedStripeAmount($start, $end)
    {
        $this->db->select('count(id) as total, IFNULL(sum(COALESCE(amount, 0)), 0) as total_amount', FALSE)
            ->where('type', 'received')->where('paid_by', 'stripe')
            ->where('date BETWEEN ' . $start . ' and ' . $end);
        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getTotalReturnedAmount($start, $end)
    {
        $this->db->select('count(id) as total, IFNULL(sum(COALESCE(amount, 0)), 0) as total_amount', FALSE)
            ->where('type', 'returned')
            ->where('date BETWEEN ' . $start . ' and ' . $end);
        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    public function getStaffDailySales($user_id, $year, $month, $warehouse_id = NULL)
    {
        $myQuery = "SELECT DATE_FORMAT( date,  '%d-%m-%Y' ) AS date, SUM( COALESCE( total_tax, 0 ) ) AS tax,SUM( COALESCE( grand_total, 0 ) ) AS grand_total, SUM( COALESCE( total, 0 ) ) AS total, SUM( COALESCE( total_discount, 0 ) ) AS discount, SUM( COALESCE( shipping, 0 ) ) AS shipping
            FROM " . $this->db->dbprefix('bils')." WHERE ";

        if ($warehouse_id) {
            $myQuery .= " warehouse_id = {$warehouse_id} AND ";
        }
        if(!$this->Owner && !$this->Admin){
            $myQuery .= " table_whitelisted =0 AND";
        }
        $myQuery .= " created_by = {$user_id} AND DATE_FORMAT( date,  '%Y-%c' ) =  '{$year}-{$month}'
            GROUP BY DATE_FORMAT( date,  '%e' )";
             
        $q = $this->db->query($myQuery, false);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getStaffMonthlySales($user_id, $year, $warehouse_id = NULL)
    {
        $myQuery = "SELECT DATE_FORMAT( date,  '%M' ) AS date, SUM( COALESCE( total_tax, 0 ) ) AS tax,  SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( total_discount, 0 ) ) AS discount, SUM( COALESCE( shipping, 0 ) ) AS shipping
            FROM " . $this->db->dbprefix('bils') . " WHERE ";
        if ($warehouse_id) {
            $myQuery .= " warehouse_id = {$warehouse_id} AND ";
        }
        /*if(!$this->Owner && !$this->Admin){
            $myQuery .= " table_whitelisted =0 AND";
        }*/
        $myQuery .= " created_by = {$user_id} AND DATE_FORMAT( date,  '%Y' ) =  '{$year}'
            GROUP BY date_format( date, '%c' ) ORDER BY date_format( date, '%c' ) ASC";
            /*echo $myQuery;die;*/
        $q = $this->db->query($myQuery, false); 
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    function getStaffSalesReport($warehouse_id,$user,$page)
    {   
        /*$start_date = $where['start_date'];
        $end_date = $where['end_date'];
        $user = $where['user'];*/

       /*if($start_date){
           $start_date = $start_date;
        }
        else{
            $start_date = date('Y-m-d');
        }
        if($end_date)
        {
            $end_date = $end_date;
        }
        else{
            $end_date = date('Y-m-d');
        }*/
       
        $si = "( SELECT bil_id as sale_id, recipe_id as product_id  from {$this->db->dbprefix('bil_items')} ";
        /*$si .= " WHERE ";*/
           /* if ($product || $serial)*/  /*$si .= " WHERE "; */
            /*if ($product) {
                $si .= " {$this->db->dbprefix('bil_items')}.recipe_id = {$product} ";
            }
            if ($product && $serial) { $si .= " AND "; }*/
           /* if ($serial) {
                $si .= " {$this->db->dbprefix('bil_items')}.serial_no LIKe '%{$serial}%' ";
            }*/
            $si .= " GROUP BY {$this->db->dbprefix('bil_items')}.bil_id ) FSI";            
            $this->db
                ->select("{$this->db->dbprefix('warehouses')}.name as branch,DATE_FORMAT(date, '%Y-%m-%d %T') as date, reference_no, biller, customer, grand_total, paid,  payment_status, {$this->db->dbprefix('bils')}.id as id", FALSE)
                ->from('bils')
                ->join($si, 'FSI.sale_id=bils.id', 'left')
                ->join('warehouses', 'warehouses.id=bils.warehouse_id', 'left');
                $this->db->where('bils.payment_status', 'Completed');
                // ->group_by('sales.id');

           /* if ($user) {
                $this->db->where('bils.created_by', $user);
            }*/
          /*  if ($product) {
                $this->db->where('FSI.product_id', $product);
            }*/
            /*if ($serial) {
                $this->datatables->like('FSI.serial_no', $serial);
            }*/
          /*  if ($biller) {
                $this->db->where('bils.biller_id', $biller);
            }
            if ($customer) {
                $this->db->where('bils.customer_id', $customer);
            }*/
            /*if ($warehouse) {
                $this->db->where('bils.warehouse_id', $warehouse);
            }*/
            /*if ($reference_no) {
                $this->db->like('bils.reference_no', $reference_no, 'both');
            }*/
          /*  if ($start_date) {
                $this->db->where($this->db->dbprefix('bils').'.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }*/
        /*    if(!$this->Owner && !$this->Admin){
                $this->datatables->where('bils.table_whitelisted', 0); 
            }
*/
        if($page){
           $this->db->limit($limit,$offset);
        }

        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
            $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }   

 function getStaffPurchasedReport($warehouse_id,$user,$page)
    {   
        $pi = "( SELECT purchase_id, product_id from {$this->db->dbprefix('purchase_items')} ";
           /* if ($product) {
                $pi .= " WHERE {$this->db->dbprefix('purchase_items')}.product_id = {$product} ";
            }*/
            $pi .= " GROUP BY {$this->db->dbprefix('purchase_items')}.purchase_id ) FPI";
            
            $this->db
                ->select("'sno',DATE_FORMAT({$this->db->dbprefix('purchases')}.date, '%Y-%m-%d %T') as date, reference_no, {$this->db->dbprefix('warehouses')}.name as wname, supplier,grand_total, paid, (grand_total-paid) as balance, {$this->db->dbprefix('purchases')}.status, {$this->db->dbprefix('purchases')}.id as id", FALSE)
                ->from('purchases')
                ->join($pi, 'FPI.purchase_id=purchases.id', 'left')
                ->join('warehouses', 'warehouses.id=purchases.warehouse_id', 'left');
                // ->group_by('purchases.id');

            if ($user) {
                $this->db->where('purchases.created_by', $user);
            }
            /*if ($product) {
                $this->db->where('FPI.product_id', $product, FALSE);
            }
            if ($supplier) {
                $this->db->where('purchases.supplier_id', $supplier);
            }
            if ($warehouse) {
                $this->db->where('purchases.warehouse_id', $warehouse);
            }
            if ($reference_no) {
                $this->db->like('purchases.reference_no', $reference_no, 'both');
            }
            if ($start_date) {
                $this->db->where($this->db->dbprefix('purchases').'.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }*/
        if($page){
           $this->db->limit($limit,$offset);
        }

        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
            $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }  
 function getStaffPaymentReport($warehouse_id,$user,$page)
    {   

        $this->db
        ->select("'sno',".$this->db->dbprefix('warehouses') . ".name as branch, DATE_FORMAT({$this->db->dbprefix('payments')}.date, '%Y-%m-%d %T') as date, bill_number, " . $this->db->dbprefix('bils') . ".reference_no as sale_ref, CONCAT('cash - ',SUM(DISTINCT CASE  WHEN ((srampos_payments.paid_by = 'cash') AND (srampos_sale_currency.currency_id=2) AND (srampos_sale_currency.amount!='')) THEN srampos_payments.amount 
        WHEN ((srampos_payments.paid_by = 'cash' AND srampos_sale_currency.currency_id=1 AND srampos_sale_currency.amount!='')) THEN (srampos_sale_currency.amount*srampos_sale_currency.currency_rate) ELSE 0 END),' | CC - ',SUM(DISTINCT CASE
        WHEN " . $this->db->dbprefix('payments') . ".paid_by = 'CC'  THEN {$this->db->dbprefix('payments')}.amount

        ELSE 0 END),'| credit - ',SUM(DISTINCT CASE  WHEN ((srampos_payments.paid_by = 'credit') AND (srampos_sale_currency.currency_id=2) AND (srampos_sale_currency.amount!='')) THEN srampos_payments.amount 
        WHEN ((srampos_payments.paid_by = 'credit' AND srampos_sale_currency.currency_id=1 AND srampos_sale_currency.amount!='')) THEN (srampos_sale_currency.amount*srampos_sale_currency.currency_rate) ELSE 0 END)) paid_by,{$this->db->dbprefix('bils')}.paid  as For_Ex,{$this->db->dbprefix('bils')}.balance,type, {$this->db->dbprefix('payments')}.id as id,{$this->db->dbprefix('payments')}.bill_id")
        ->from('bils')
        ->join('payments', 'payments.bill_id = bils.id')
        ->join('sale_currency', 'sale_currency.bil_id = bils.id')
        ->join('warehouses', 'warehouses.id = bils.warehouse_id')
        //->order_by('bils.id','ASC')
        ->group_by('bils.id');


           /* if ($user) {
                $this->db->where('payments.created_by', $user);
            }
            if ($card) {
                $this->db->like('payments.cc_no', $card, 'both');
            }
            if ($cheque) {
                $this->db->where('payments.cheque_no', $cheque);
            }
            if ($transaction_id) {
                $this->db->where('payments.transaction_id', $transaction_id);
            }
            if ($customer) {
                $this->db->where('bils.customer_id', $customer);
            }*/
           /* if ($supplier) {
                $this->db->where('purchases.supplier_id', $supplier);
            }*/
           /* if ($biller) {
                $this->db->where('bils.biller_id', $biller);
            }
            if ($customer) {
                $this->db->where('bils.customer_id', $customer);
            }*/
           /* if ($payment_ref) {
                $this->db->like('payments.reference_no', $payment_ref, 'both');
            }*/
           /* if ($paid_by) {
                $this->db->where('payments.paid_by', $paid_by);
            }
            if ($sale_ref) {
                $this->db->like('bils.reference_no', $sale_ref, 'both');
            }*/
          
           /* if ($start_date) {
                $this->db->where($this->db->dbprefix('payments').'.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }
            if(!$this->Owner && !$this->Admin){
            $this->db->where('bils.table_whitelisted', 0); 
           }*/
        if($page){
           $this->db->limit($limit,$offset);
        }

        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
            $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }     
    function getStaffLoginReport($warehouse_id,$user,$page)
    {   
        $this->db
                ->select("login, ip_address, (CASE WHEN time != '' THEN DATE_FORMAT(time, '%Y-%m-%d %T') ELSE '0000-00-00' END) AS time")
                ->from("user_logins")
                ->where('user_id', $user);
            if ($login_start_date) {
                $this->db->where("time BETWEEN '{$login_start_date}' and '{$login_end_date}'", NULL, FALSE);
            }
        
        if($page){
           $this->db->limit($limit,$offset);
        }

        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
            $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }  
 public function HomedeliveryCostomer() {

    $HomeDelivery = "SELECT C.id AS customer_id,C.name
        FROM " . $this->db->dbprefix('companies') . " C        
        JOIN " . $this->db->dbprefix('orders') . " O ON O.customer_id = C.id
            WHERE  O.order_type = 3   GROUP BY C.id";        
        $q = $this->db->query($HomeDelivery);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

}
