<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Sync extends MY_Controller{
    function __construct() {
        parent::__construct();
    }
    function start_sync($particular_sync=false){
	    ob_end_clean();
        ignore_user_abort();
        ob_start();
        header("Connection: close");
        echo @json_encode($out);
        header("Content-Length: " . ob_get_length());
        @ob_end_flush();
        flush();
	if($this->centerdb_connected){
	     $this->site->update_sync_startTime();
	     $sync_settings = $this->sync_store->sync_sync_settings();
	     if($sync_settings){
		 if($particular_sync){
		    $tables[0] = $particular_sync;
		}else{
		 $tables = $this->site->getSyncEnabledTables();   
		}
	    foreach($tables as $k => $table_name){
		if($this->centerdb_connected){
		    switch($table_name){
			case 'stores':			    
			    $this->sync_store->sync_stores();
			    break;
			case 'products':
			    if($this->sync_store->sync_warehouse_recipe()){
				$this->sync_store->sync_categories();	
				$this->sync_store->sync_category_mapping();					
				$this->sync_store->sync_units();
				$this->sync_store->sync_brands();
				$this->sync_store->sync_variants();
				$this->sync_store->sync_recipes();
				$this->sync_store->sync_recipe_product();
				$this->sync_store->sync_recipe_combo_items();
				$this->sync_store->sync_recipe_photos();
				$this->sync_store->sync_recipe_variant_values();
				$this->sync_store->sync_recipe_variants();
			    }
			    break;
				case 'kitchen':
			    if($this->sync_store->sync_restaurant_kitchens()){
					$this->sync_store->sync_restaurant_kitchens();
					$this->sync_store->sync_restaurant_tables();
			    }
			    break;
				
				
			/* 	case 'currencies':			    
			    $this->sync_store->sync_currencies();
			    break; */
			
			    case 'tax':
			    $this->sync_store->sync_taxrates();
			    break;
					
			    case 'users':
			    $this->sync_store->sync_user_groups();
			    if($this->sync_store->sync_user_store_access()){
				$this->sync_store->sync_users();
			    }
			    break;
				
				case 'suppliers':
			    $this->sync_store->sync_vendors();
			    break;
				
				case 'customers':
			    $this->sync_both->sync_customers();			    
			    $this->sync_store->sync_customer_groups();
			    break;
				case 'tills':
			    $this->sync_both->sync_tills();
			    break;
				case 'printers':
			    $this->sync_both->sync_printers();
			    break;
					
			    case 'tender_types':			    
			    $this->sync_store->sync_tendertypes();
			   // $this->sync_both->sync_tendertype_status();
			    break;
				case 'expense_category':
			    $this->sync_store->sync_expense_categories();
			    break;
				case 'calendar':
			    $this->sync_store->sync_calendar();
			    break;
			    
				case 'group_permission':
			    $this->sync_store->sync_grouppermission();
			    break;
				
				
			case 'store_indent_request':
			    $this->sync_center->sync_storeIndentRequests();
			    break;
			case 'stock_request':
			    $this->sync_store->sync_StockRequests();
			    break;
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
		
		
			
		
			
			case 'giftvoucher':
			    $this->sync_store->sync_giftvoucher();
			    $this->sync_center->sync_issued_giftvouchers();
			    $this->sync_center->sync_blocked_giftvouchers();
			    $this->sync_both->sync_giftvoucher_status();
			    break;
			
					
			case 'promotions':
			    $this->sync_store->sync_billdiscount();
			    break;
			case 'sales':
			    $this->sync_center->sync_sales();
			    //$this->sync_center->sync_sales_flag();
			    $this->sync_center->sync_salesorders();
			    $this->sync_center->sync_holdsales();
			    $this->sync_center->sync_returnsales();
				$this->sync_center->sync_exchangesales();
			    break;
			case 'expenses':
			    $this->sync_center->sync_expenses();
			    break;
			case 'daily_settlement':
			    $this->sync_center->sync_dailysettlement();
			    break;
			//case 'purchase_order':
			//    $this->sync_center->sync_po();
			//    break;
			//case 'purchase_invoice':
			//    $this->sync_center->sync_purchase_invoice();
			//    break;
			//case 'purchase_return':
			//    $this->sync_center->sync_purchase_return();
			//    break;
			case 'stock':
			    $this->sync_both->sync_stock();
				$this->sync_center->sync_stock();
			    break;
			
			case 'stock_transfer':
			    $this->sync_center->sync_store_transfers();
			    $this->sync_store->sync_StockReceiver();
			    $this->sync_both->sync_store_receivers();
			    break;
			case 'shift':
			    $this->sync_store->sync_shiftmaster();
			    $this->sync_center->sync_shiftcreation();
			    $this->sync_center->sync_shiftsettlement();// compare columns and add new column
			    $this->sync_center->sync_shiftcard_settlement();
			    $this->sync_center->sync_shift_wallet_settlement();
			    $this->sync_center->sync_shift_coupon_settlement();
			    $this->sync_center->sync_shift_cheque_settlement();
			    break;
			case 'countries':			    
			    $this->sync_store->sync_countries();
			    break;
			case 'banks':			    
			    $this->sync_store->sync_banks();
			    break;
			case 'notifications':			    
			    $this->sync_center->sync_notifications();
			    break;
			case 'device_detail':			    
			    $this->sync_store->sync_device_detail();
			    break;
			
		    }
		}
	    }
	    }
	    $this->site->update_sync_endTime();
	}

    }

    /*** Sync products **/
    
    /** Sync People**/
	/**
	 * users - from store
	 * billers - from store
	 * customers - from both
	 * suppliers - from store
	 **/
    
    /** Sync Gift Voucher - from Store**/
	
    
    /** Sync Currencies - from Store**/

	
    /** Sync System Setting - No Sync happen **/
    /** Sync POS Setting - No Sync happen **/
    /** Sync Printers - No Sync happen **/
    /** Sync LOGO - No Sync happen **/
}