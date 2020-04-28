<?php defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {
    function __construct()
    {
        parent::__construct();
        $this->Settings = $this->site->get_setting();
        $this->pos_settings = $this->site->get_posSetting();
        ini_set('memory_limit',-1);
        //define('SOCKET_PORT',$this->Settings->socket_port);
        //define('SOCKET_HOST',$this->Settings->socket_host);  
        $thisStore = $this->site->getThisStore(); 
		
        $this->isWarehouse = ($thisStore->type==1)?false:true;
        $this->isStore = ($thisStore->type==1)?true:false;
        $this->store_id = $thisStore->id;
        $this->store_name = $thisStore->name;
		$this->myIp = getHostByName(php_uname('n'));
		
		  $this->centerdb_connected =  false;
        if($this->isStore){
            $this->center_server->connect();
           //echo $this->centerdb_connected;exit;
        }
		if(!empty($this->myIp)){
			$counter = $this->site->getCounter($this->myIp);
			if(!empty($counter)){
				$this->till_name = $counter->till_name;
				$this->till_id = $counter->id;
			}else{
				$this->till_name = 'N/A';
				$this->till_id = 0;
			}
		}else{
			$this->till_name = 'N/A';
			$this->till_id = 0;
		}
		
		$exitShift = $this->site->exitShift($this->till_id);
		$continueShift = $this->site->continueShift($this->till_id);
		$dontcontinueShift = $this->site->dontcontinueShift($this->till_id);
		$getShiftmaster = $this->site->getShiftmaster();
		
		$this->currencies = $this->site->getAllCurrencies();
		$this->defaultcurdata =  $this->site->defaultCurrencyData($this->Settings->default_currency);
		if(!empty($getShiftmaster)){
			$this->currentShift = $getShiftmaster;	
		}else{
			$this->currentShift = 0;
		}
		
		if(!empty($exitShift)){
			$this->isShiftCreated = 1;	
			$this->exitShift = $exitShift;	
		}else{
			$this->isShiftCreated = 0;
		}
		$this->exitShift->id;
		if(!empty($continueShift)){
			$this->iscontinueShift = 1;	
		}else{
			$this->iscontinueShift = 0;
		}
		
		if(!empty($dontcontinueShift)){
			$this->dontcontinueShift = 1;	
		}else{
			$this->dontcontinueShift = 0;
		}
		
		if($this->isShiftCreated == 1){
			$this->ShiftID = $exitShift->id;
		}else{
			if($this->iscontinueShift == 1){
				$this->ShiftID = $continueShift->id;
			}else{
				$this->ShiftID = 0;
			}
		}

        if($sma_language = $this->input->cookie('sma_language', TRUE)) {
            $this->config->set_item('language', $sma_language);
            $this->lang->admin_load('sma', $sma_language);
            $this->Settings->user_language = $sma_language;
        } else {
            $this->config->set_item('language', $this->Settings->language);
            $this->lang->admin_load('sma', $this->Settings->language);
            $this->Settings->user_language = $this->Settings->language;
        }
        if($rtl_support = $this->input->cookie('sma_rtl_support', TRUE)) {
            $this->Settings->user_rtl = $rtl_support;
        } else {
            $this->Settings->user_rtl = $this->Settings->rtl;
        }
        $this->theme = $this->Settings->theme.'/admin/views/';
        if(is_dir(VIEWPATH.$this->Settings->theme.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR)) {
            $this->data['assets'] = base_url() . 'themes/' . $this->Settings->theme . '/assets/';
        } else {
            $this->data['assets'] = base_url() . 'themes/default/admin/assets/';
        }
        if(empty($this->Settings->excel_header_color)) {
            $this->Settings->excel_header_color = 'd28f16';
        } 
        if(empty($this->Settings->excel_footer_color)) {
            $this->Settings->excel_footer_color = 'ffc000';
        }
        
        $this->data['Settings'] = $this->Settings;
        $this->loggedIn = $this->sma->logged_in();

        if($this->loggedIn) {
            if($this->Settings->transaction_date==1){
                $this->site->set_cur_transaction_date();
            }
            $this->data['isTransactiondateSet'] = $this->site->isTransactionDateSet();
            if($this->session->userdata('assigned_stores')){
              $this->data['user_assigned_stores'] = $this->session->userdata('assigned_stores');  
            }
            
            /////// autobackup //////////////
            if($this->session->userdata('admin_panel') && $this->Settings->ftp_autobackup_enable==1){
                $this->load->library('backup');
                @$this->backup->initiate();  
            }
            
            
            $this->data['default_store'] = $this->session->userdata('warehouse_id');
            $this->data['pos_store'] = $this->session->userdata('warehouse_id');
            
            $this->data['pos_store_name'] = $this->session->userdata('store_name');
            /////// autobackup - end//////////////
			
			//-------------------- achrival start  -----------------//
			 if($this->session->userdata('admin_panel') && $this->Settings->archival==1){
                $this->load->library('archival');
                @$this->archival->start();  
            } 
			//--------------------archival end     ----------------//
            $this->data['isNightauditDone'] = ($this->Settings->night_audit_rights)?$this->site->getPreviousDayNightAudit($this->Settings->default_warehouse):1;
            $this->default_currency = $this->site->getCurrencyByCode($this->Settings->default_currency);
            $this->data['default_currency'] = $this->default_currency;
            $this->Owner = $this->sma->in_group('owner') ? TRUE : NULL;
            $this->data['Owner'] = $this->Owner;
            $this->Customer = $this->sma->in_group('customer') ? TRUE : NULL;
            $this->data['Customer'] = $this->Customer;
            $this->Supplier = $this->sma->in_group('supplier') ? TRUE : NULL;
            $this->data['Supplier'] = $this->Supplier;
            $this->Admin = $this->sma->in_group('admin') ? TRUE : NULL;
            $this->data['Admin'] = $this->Admin;

            if($sd = $this->site->getDateFormat($this->Settings->dateformat)) {
                $dateFormats = array(
                    'js_sdate' => $sd->js,
                    'php_sdate' => $sd->php,
                    'mysq_sdate' => $sd->sql,
                    'js_ldate' => $sd->js . ' hh:ii',
                    'php_ldate' => $sd->php . ' H:i',
                    'mysql_ldate' => $sd->sql . ' %H:%i'
                    );
            } else {
                $dateFormats = array(
                    'js_sdate' => 'mm-dd-yyyy',
                    'php_sdate' => 'm-d-Y',
                    'mysq_sdate' => '%m-%d-%Y',
                    'js_ldate' => 'mm-dd-yyyy hh:ii:ss',
                    'php_ldate' => 'm-d-Y H:i:s',
                    'mysql_ldate' => '%m-%d-%Y %T'
                    );
            }
            if(file_exists(APPPATH.'controllers'.DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR.'Pos.php')) {
                define("POS", 1);
            } else {
                define("POS", 0);
            }
            if(file_exists(APPPATH.'controllers'.DIRECTORY_SEPARATOR.'shop'.DIRECTORY_SEPARATOR.'Shop.php')) {
                define("SHOP", 1);
            } else {
                define("SHOP", 0);
            }

             if(file_exists(APPPATH.'controllers'.DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR.'System_settings.php')) {
                define("WAREHOUSES", 1);
            } else {
                define("WAREHOUSES", 0);
            }
            if(!$this->Owner && !$this->Admin) {
                $gp = $this->site->checkPermissions();
                $this->GP = $gp[0];
                $this->data['GP'] = $gp[0];
            } else {
                $this->data['GP'] = NULL;
            }
            $this->dateFormats = $dateFormats;
            $this->data['dateFormats'] = $dateFormats;
            $this->load->language('calendar');
            //$this->default_currency = $this->Settings->currency_code;
            //$this->data['default_currency'] = $this->default_currency;
            $this->m = strtolower($this->router->fetch_class());
            $this->v = strtolower($this->router->fetch_method());
            $this->data['m']= $this->m;
            $this->data['v'] = $this->v;
            $this->data['dt_lang'] = json_encode(lang('datatables_lang'));
            $this->data['dp_lang'] = json_encode(array('days' => array(lang('cal_sunday'), lang('cal_monday'), lang('cal_tuesday'), lang('cal_wednesday'), lang('cal_thursday'), lang('cal_friday'), lang('cal_saturday'), lang('cal_sunday')), 'daysShort' => array(lang('cal_sun'), lang('cal_mon'), lang('cal_tue'), lang('cal_wed'), lang('cal_thu'), lang('cal_fri'), lang('cal_sat'), lang('cal_sun')), 'daysMin' => array(lang('cal_su'), lang('cal_mo'), lang('cal_tu'), lang('cal_we'), lang('cal_th'), lang('cal_fr'), lang('cal_sa'), lang('cal_su')), 'months' => array(lang('cal_january'), lang('cal_february'), lang('cal_march'), lang('cal_april'), lang('cal_may'), lang('cal_june'), lang('cal_july'), lang('cal_august'), lang('cal_september'), lang('cal_october'), lang('cal_november'), lang('cal_december')), 'monthsShort' => array(lang('cal_jan'), lang('cal_feb'), lang('cal_mar'), lang('cal_apr'), lang('cal_may'), lang('cal_jun'), lang('cal_jul'), lang('cal_aug'), lang('cal_sep'), lang('cal_oct'), lang('cal_nov'), lang('cal_dec')), 'today' => lang('today'), 'suffix' => array(), 'meridiem' => array()));
            $this->Settings->indian_gst = FALSE;
            if ($this->Settings->invoice_view > 0) {
                $this->Settings->indian_gst = $this->Settings->invoice_view == 2 ? TRUE : FALSE;
                $this->Settings->format_gst = TRUE;
                $this->load->library('gst');
            }
        }
		date_default_timezone_set( $this->Settings->timezone);
    }

    function page_construct($page, $meta = array(), $data = array()) {
        $meta['message'] = isset($data['message']) ? $data['message'] : $this->session->flashdata('message');
        $meta['error'] = isset($data['error']) ? $data['error'] : $this->session->flashdata('error');
        $meta['warning'] = isset($data['warning']) ? $data['warning'] : $this->session->flashdata('warning');
        $meta['info'] = $this->site->getNotifications();
        $meta['events'] = $this->site->getUpcomingEvents();
        $meta['ip_address'] = $this->input->ip_address();
        $meta['Owner'] = $data['Owner'];
        $meta['Admin'] = $data['Admin'];
        $meta['Supplier'] = $data['Supplier'];
        $meta['Customer'] = $data['Customer'];
        $meta['Settings'] = $data['Settings'];
        $meta['dateFormats'] = $data['dateFormats'];
        $meta['assets'] = $data['assets'];
        $meta['isNightauditDone'] =  $data['isNightauditDone'];
        $meta['GP'] = $data['GP'];
        $meta['qty_alert_num'] = $this->site->get_total_qty_alerts();
        $meta['exp_alert_num'] = $this->site->get_expiring_qty_alerts();
        $meta['shop_sale_alerts'] = SHOP ? $this->site->get_shop_sale_alerts() : 0;
        $meta['shop_payment_alerts'] = SHOP ? $this->site->get_shop_payment_alerts() : 0;
		if($this->Settings->procurment == 1){
			$meta['access_info'] = $this->siteprocurment->getAccessNotifications($this->session->userdata('user_id'));
		}else{
			$meta['access_info'] = '';
		}
        $this->load->view($this->theme . 'header', $meta);
        $this->load->view($this->theme . $page, $data);
        $this->load->view($this->theme . 'footer');
    }

}
