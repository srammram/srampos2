<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Sync_center
{
	
    public function __construct() {
        $this->CI =& get_instance();	
    }
    function sync_new_sales($saleid){
	if($this->CI->isStore && $this->CI->centerdb_connected){   
	//tables :  sales,sale_items,payment,sale_currency
	$s = $this->CI->db->get_where('sales',array('id'=>$saleid));
	if($s->num_rows()>0){
	  $sales = $s->row();
	  /// sale items
	  $si = $this->CI->db->get_where('sale_items',array('sale_id'=>$saleid));
	  $sale_items = $si->result();
	  /// payemnts
	  $p = $this->CI->db->get_where('payments',array('sale_id'=>$saleid));
	  $payments = $p->result();
	  /// sale currency
	  $sc = $this->CI->db->get_where('sale_currency',array('sale_id'=>$saleid));
	  $sale_currencies = $sc->result();
	  
	  /// settlement currency
	  $se_c = $this->CI->db->get_where('settlement_currency',array('sale_id'=>$saleid));
	  $settlement_currencies = $se_c->result();
	  
	  ///  sale_card_payment
	  $scards = $this->CI->db->get_where('sale_card_payment',array('sale_id'=>$saleid));
	  $sale_cards = $scards->result();
	  ///  sale_wallet_payment
	  $scards = $this->CI->db->get_where('sale_wallet_payment',array('sale_id'=>$saleid));
	  $sale_wallets = $scards->result();
	  
	  ///  sale_coupon_payment
	  $scards = $this->CI->db->get_where('sale_coupon_payment',array('sale_id'=>$saleid));
	  $sale_coupons = $scards->result();
	  
	  ///  sale_coupon_payment
	  $scards = $this->CI->db->get_where('sale_bankcheque_payment',array('sale_id'=>$saleid));
	  $sale_bankcheques = $scards->result();
	  
	  ///  sale_coupon_payment
	  $scards = $this->CI->db->get_where('sale_loyalty_payment',array('sale_id'=>$saleid));
	  $sale_loyalties = $scards->result();
	  //sync//
	  unset($sales->saleid);
	  $this->CI->centerdb->insert('sales',$sales);
	  foreach($sale_items as $k => $sale_item){
	    unset($sale_item->s_no);
	    $this->CI->centerdb->insert('sale_items',$sale_item);
	  }
	  foreach($payments as $k => $payment){
	    unset($payment->s_no);
	    $this->CI->centerdb->insert('payments',$payment);
	  }
	  foreach($sale_currencies as $k => $sale_currency){
	    unset($sale_currency->s_no);
	    $this->CI->centerdb->insert('sale_currency',$sale_currency);
	  }
	  foreach($settlement_currencies as $k => $settlement_currency){
	    unset($settlement_currency->s_no);
	    $this->CI->centerdb->insert('settlement_currency',$settlement_currency);
	  }
	  foreach($sale_cards as $k => $sale_card){
	    unset($sale_card->s_no);
	    $this->CI->centerdb->insert('sale_card_payment',$sale_card);
	  }
	  foreach($sale_wallets as $k => $sale_wallet){
	    unset($sale_wallet->s_no);
	    $this->CI->centerdb->insert('sale_wallet_payment',$sale_wallet);
	  }
	  
	  foreach($sale_coupons as $k => $sale_coupon){
	    unset($sale_coupon->s_no);
	    $this->CI->centerdb->insert('sale_coupon_payment',$sale_coupon);
	  }
	  
	  foreach($sale_bankcheques as $k => $sale_bankcheque){
	    unset($sale_bankcheque->s_no);
	    $this->CI->centerdb->insert('sale_bankcheque_payment',$sale_bankcheque);
	  }
	  
	   foreach($sale_loyalties as $k => $sale_loyalty){
	    unset($sale_loyalty->s_no);
	    $this->CI->centerdb->insert('sale_loyalty_payment',$sale_loyalty);
	  }
	}
	}
    }
    function sync_update_sales($saleid,$sale,$order_items,$payments,$multicurrecy,$settlement_currency,$sale_cards,$sale_wallets,$sale_coupons,$sale_bankcheque,$sale_loyalty){
	if($this->CI->isStore && $this->CI->centerdb_connected){   
	    ////update sales
	    $this->CI->centerdb->where('id',$saleid);
	    $this->CI->centerdb->where('store_id',$this->CI->store_id);
	    $this->CI->centerdb->update('sales',$sale);
	    //// update sale items
	    $this->CI->centerdb->where('sale_id',$saleid);
	    $this->CI->centerdb->where('store_id',$this->CI->store_id);
	    $this->CI->centerdb->delete('sale_items');
	    
	    foreach($order_items as $k => $order){
		$order['sale_id'] = $saleid;
		$order['store_id'] = $this->CI->store_id;
		$this->CI->centerdb->insert('sale_items',$order);
	    }
		    
		    
	    //// update payments
	    
	    $this->CI->centerdb->where('sale_id',$saleid);
	    $this->CI->centerdb->where('store_id',$this->CI->store_id);
	    $this->CI->centerdb->delete('payments');
	    
	    if(isset($payments) && !empty($payments)){
		foreach($payments as $p => $payment){
		    $payment['sale_id'] = $saleid;
		    $payment['store_id'] = $this->CI->store_id;
		    $this->CI->centerdb->insert('payments',$payment);
		} 
	    }
	    
	    //// update sale currency
	    $this->CI->centerdb->where('sale_id',$saleid);
	    $this->CI->centerdb->where('store_id',$this->CI->store_id);
	    $this->CI->centerdb->delete('sale_currency');
	    if(isset($multicurrecy) && !empty($multicurrecy)){
		foreach($multicurrecy as $m => $m_c){
		    $m_c['sale_id'] = $saleid;
		    $m_c['store_id'] = $this->CI->store_id;
		    $this->CI->centerdb->insert('sale_currency',$m_c); 
		} 
	    }
	    
	    //// update settlement currency
	    $this->CI->centerdb->where('sale_id',$saleid);
	    $this->CI->centerdb->where('store_id',$this->CI->store_id);
	    $this->CI->centerdb->delete('settlement_currency');
	    if(isset($settlement_currency) && !empty($settlement_currency)){
		foreach($settlement_currency as $m => $m_c){
		    $m_c['sale_id'] = $saleid;
		    $m_c['store_id'] = $this->CI->store_id;
		    $this->CI->centerdb->insert('settlement_currency',$m_c); 
		} 
	    }
	    
	    //// update sale cards
	    $this->CI->centerdb->where('sale_id',$saleid);
	    $this->CI->centerdb->where('store_id',$this->CI->store_id);
	    $this->CI->centerdb->delete('sale_card_payment');
	    if(isset($sale_cards) && !empty($sale_cards)){
		foreach($sale_cards as $m => $mc_c){
		    $mc_c['sale_id'] = $saleid;
		    $mc_c['store_id'] = $this->CI->store_id;
		    $this->CI->centerdb->insert('sale_card_payment',$mc_c); 
		} 
	    }
	    
	    //// update sale wallets
	    $this->CI->centerdb->where('sale_id',$saleid);
	    $this->CI->centerdb->where('store_id',$this->CI->store_id);
	    $this->CI->centerdb->delete('sale_wallet_payment');
	    if(isset($sale_wallets) && !empty($sale_wallets)){
		foreach($sale_wallets as $m => $mc_c){
		    $mc_c['sale_id'] = $saleid;
		    $mc_c['store_id'] = $this->CI->store_id;
		    $this->CI->centerdb->insert('sale_wallet_payment',$mc_c); 
		} 
	    }
	    
	    
	    //// update sale coupon
	    $this->CI->centerdb->where('sale_id',$saleid);
	    $this->CI->centerdb->where('store_id',$this->CI->store_id);
	    $this->CI->centerdb->delete('sale_coupon_payment');
	    if(isset($sale_coupons) && !empty($sale_coupons)){
		foreach($sale_coupons as $m => $mc_c){
		    $mc_c['sale_id'] = $saleid;
		    $mc_c['store_id'] = $this->CI->store_id;
		    $this->CI->centerdb->insert('sale_coupon_payment',$mc_c); 
		} 
	    }
	    //// update sale bankcheque
	    $this->CI->centerdb->where('sale_id',$saleid);
	    $this->CI->centerdb->where('store_id',$this->CI->store_id);
	    $this->CI->centerdb->delete('sale_bankcheque_payment');
	    if(isset($sale_bankcheque) && !empty($sale_bankcheque)){
		foreach($sale_bankcheque as $m => $mc_c){
		    $mc_c['sale_id'] = $saleid;
		    $mc_c['store_id'] = $this->CI->store_id;
		    $this->CI->centerdb->insert('sale_bankcheque_payment',$mc_c); 
		} 
	    }
	    
	    //// update sale loyalty
	    $this->CI->centerdb->where('sale_id',$saleid);
	    $this->CI->centerdb->where('store_id',$this->CI->store_id);
	    $this->CI->centerdb->delete('sale_loyalty_payment');
	    if(isset($sale_loyalty) && !empty($sale_loyalty)){
		foreach($sale_loyalty as $m => $mc_c){
		    $mc_c['sale_id'] = $saleid;
		    $mc_c['store_id'] = $this->CI->store_id;
		    $this->CI->centerdb->insert('sale_loyalty_payment',$mc_c); 
		} 
	    }
	}
    }
    function sync_new_sale_orders($s_orderid){
	if($this->CI->isStore && $this->CI->centerdb_connected){   
	    //tables :  sales,sale_items,payment,sale_currency
	    $SO = $this->CI->db->get_where('sales_order',array('id'=>$s_orderid));
	    if($SO->num_rows()>0){
	      $sales_order = $SO->row();
	      /// sale items
	      $SOI = $this->CI->db->get_where('sales_order_items',array('sales_order_id'=>$s_orderid));
	      $sales_order_items = $SOI->result();	 
	      
	      //sync//
	      unset($sales_order->so_id);
	      $this->CI->centerdb->insert('sales_order',$sales_order);
	      foreach($sales_order_items as $k => $sales_order_item){
		unset($sales_order_item->s_no);
		$this->CI->centerdb->insert('sales_order_items',$sales_order_item);
	      }
	    }
	}
    }
     function sync_update_sale_orders($so_id,$sale,$order_items){
	if($this->CI->isStore && $this->CI->centerdb_connected){   
	    ////update sales
	    
	    $this->CI->centerdb->where('id',$so_id);
	    $this->CI->centerdb->where('store_id',$this->CI->store_id);
	    $this->CI->centerdb->update('sales_order',$sale);//echo '<pre>';print_R($this->CI->db->error());exit;
	    //// update sale items
	    $this->CI->centerdb->where('sales_order_id',$so_id);
	    $this->CI->centerdb->where('store_id',$this->CI->store_id);
	    $this->CI->centerdb->delete('sales_order_items');
	    
	    foreach($order_items as $k => $order){
		$order['sales_order_id'] = $so_id;
		$order['store_id'] = $this->CI->store_id;
		$this->CI->centerdb->insert('sales_order_items',$order);
	    }
	    return true;
	}
    }
    function sync_new_suspend_sales($saleid){
	if($this->CI->isStore && $this->CI->centerdb_connected){   
	    //tables :  suspended_bills,suspended_items
	    $s = $this->CI->db->get_where('suspended_bills',array('id'=>$saleid));
	    if($s->num_rows()>0){
	      $suspended_bill = $s->row();
	      /// sale items
	      $si = $this->CI->db->get_where('suspended_items',array('suspend_id'=>$saleid));
	      $suspended_items = $si->result();
	     
	      
	      //sync//
	      unset($suspended_bill->suspendid);
	      $this->CI->centerdb->insert('suspended_bills',$suspended_bill);
	      foreach($suspended_items as $k => $suspended_item){
		unset($suspended_item->s_no);
		$this->CI->centerdb->insert('suspended_items',$suspended_item);
	      }
	    }
	}
    }
    
    function sync_update_suspendbills($where,$data){
	if($this->CI->isStore && $this->CI->centerdb_connected){   
	    $this->CI->centerdb->where($where);
	    $this->CI->centerdb->update('suspended_bills',$data);
	}
    }
    
    function sync_update_so($where,$data){
	if($this->CI->isStore && $this->CI->centerdb_connected){   
	    $this->CI->centerdb->where($where);
	    $this->CI->centerdb->update('sales_order',$data);
	}
    }
    
    function sync_new_expenseEntry($expense_id){
	if($this->CI->isStore && $this->CI->centerdb_connected){   
	    //tables :  expenses,expense_details
	    $s = $this->CI->db->get_where('expenses',array('id'=>$expense_id));
	    if($s->num_rows()>0){
	      $expenses = $s->row();
	      /// expense details
	      $ed = $this->CI->db->get_where('expense_details',array('expense_id'=>$expense_id));
	      $expense_details= $ed->result();
	      unset($expenses->s_no);
	      $this->CI->centerdb->insert('expenses',$expenses);
		foreach($expense_details as $k => $expense_detail){
		  unset($expense_detail->s_no);
		  $this->CI->centerdb->insert('expense_details',$expense_detail);
		}
	    }
	}
    }
    
    function sync_update_expenseEntry($expense_id,$expense){
	if($this->CI->isStore && $this->CI->centerdb_connected){   
	    //tables :  expenses,expense_details
	    $this->CI->db->where('id',$expense_id);
	    $this->CI->db->update('expenses',$expense);
	    
	    //delete expense category data entyr
	    $this->CI->db->where('expense_id',$id);
	    $this->CI->db->delete('expense_details');
	    
	    //insert new
	    $ed = $this->CI->db->get_where('expense_details',array('expense_id'=>$expense_id));
	    foreach($ed as $k => $row){
		unset($row->s_no);
		$this->CI->db->insert('expense_details',$row);
	    }
	}
    }
    
    function sync_new_return_sale_orders($s_orderid,$original_bill_items,$update_salebill){
	if($this->CI->isStore && $this->CI->centerdb_connected){   
	    //tables :  sales,sale_items,payment,sale_currency
	    $SO = $this->CI->db->get_where('returns',array('id'=>$s_orderid));
	    if($SO->num_rows()>0){
	      $sales_order = $SO->row();
	      /// sale items
	      $SOI = $this->CI->db->get_where('return_items',array('return_id'=>$s_orderid));
	      $return_order_items = $SOI->result();	 
	      
	      //sync//
	      unset($sales_order->s_no);
	      $this->CI->centerdb->insert('returns',$sales_order);
	      foreach($return_order_items as $k => $sales_order_item){
		  unset($sales_order_item->s_no);
		$this->CI->centerdb->insert('return_items',$sales_order_item);
	      }
	      foreach($original_bill_items as $k => $row){
		    $this->CI->centerdb->where(array('id'=>$row['id'],'sale_id'=>$row['sale_id']));
		    $this->CI->centerdb->update('sale_items',$row);
		}
		 if(!empty($update_salebill)){
			$this->CI->centerdb->where(array('id'=>$update_salebill['id']));
			$this->CI->centerdb->update('sales',$update_salebill);
		    }	
	    }
	}
    }
     function sync_update_return_sale_orders($return_id,$sale,$order_items,$original_bill_items){
	if($this->CI->isStore && $this->CI->centerdb_connected){   
	    ////update sales
	    
	    $this->CI->centerdb->where('id',$return_id);
	    $this->CI->centerdb->where('store_id',$this->CI->store_id);
	    $this->CI->centerdb->update('returns',$sale);//echo '<pre>';print_R($this->CI->db->error());exit;
	    //// update sale items
	    $this->CI->centerdb->where('return_id',$return_id);
	    $this->CI->centerdb->where('store_id',$this->CI->store_id);
	    $this->CI->centerdb->delete('return_items');
	    
	    foreach($order_items as $k => $order){
		$order['return_id'] = $return_id;
		$order['store_id'] = $this->CI->store_id;
		$this->CI->centerdb->insert('return_items',$order);
	    }
	    foreach($original_bill_items as $k => $row){
		$this->CI->centerdb->where(array('id'=>$row['id'],'sale_id'=>$row['sale_id']));
		$this->CI->centerdb->update('sale_items',$row);
	    }
	    return true;
	}
    }
    
    
    /////////////// FROM STORE //////////////////////
    
   
    function sync_salesorders(){

	/** tables
	 * orders
	 * order_items
	 * kitchen_orders //sale_id is the foreign from orders table
	 * restaurant_table_orders //order_id is the foreign from orders table
	 * restaurant_table_sessions //order_id is the foreign from orders table
	 * */
	
	$table_orders = 'orders';
	$table_order_items = 'order_items';

	$table_kitchen_orders = 'kitchen_orders';
	$table_restaurant_table_orders = 'restaurant_table_orders';
	$table_restaurant_table_sessions = 'restaurant_table_sessions';
	$db1 = $this->CI->db->get_where($table_orders,array('warehouse_id'=>$this->CI->store_id))->result_array();
	$db2 = $this->CI->centerdb->get_where($table_orders,array('warehouse_id'=>$this->CI->store_id))->result_array();		
	// var_dump($this->CI->centerdb->last_query());die;
	$data = $this->compare_server_local($db1,$db2,$table_orders);
	/*echo "<pre>";
	print_r($data);die;*/
	if(isset($data['update']) && !empty($data['update'])){
	    foreach($data['update'] as $k => $update_data){
		unset($update_data->so_id);
		$sale_id = $update_data['id'];
		$this->CI->centerdb->where('id',$sale_id);
		$this->CI->centerdb->update($table_orders,$update_data);		
		$where = array('sale_id'=>$sale_id,'warehouse_id',$this->CI->store_id);		
		$sale_order_item_data = $this->sync_tables($table_order_items,$where);			
		$kitchen_orders_data = $this->sync_tables($table_kitchen_orders,$where);
		$where1 = array('order_id'=>$sale_id,'warehouse_id',$this->CI->store_id);
		$restaurant_table_orders_data = $this->sync_tables($table_restaurant_table_orders,$where);	
		$restaurant_table_sessions_data = $this->sync_tables($restaurant_table_sessions,$where);
	    }
	}
	if(isset($data['insert']) && !empty($data['insert'])){
	    foreach($data['insert'] as $k => $insert_data){
	    	/*echo "<pre>";
		print_r($insert_data);die;*/
		unset($insert_data->s_no);

		// $this->CI->centerdb->insert($table_orders,$insert_data);
		$sale_id = $insert_data['id'];
		$sale_order_items = $this->getSaleOrderItems($sale_id);
		$this->CI->centerdb->insert_batch($table_order_items,$sale_order_items);

		$kitchen_orders = $this->getKitchenOrders($sale_id);
		$this->CI->centerdb->insert_batch($table_kitchen_orders,$kitchen_orders);

		$Restaurant_table_orders = $this->getRestaurantTableOrders($sale_id);
		$this->CI->centerdb->insert_batch($table_restaurant_table_orders,$Restaurant_table_orders);

		$table_order_sessions = $this->getRestaurantTableSessions($sale_id);
		$this->CI->centerdb->insert_batch($table_restaurant_table_sessions,$table_order_sessions);
		
	    }
	    
	}
	if(isset($data['delete']) && !empty($data['delete'])){
	    foreach($data['delete'] as $k => $deleteID){
		$sale_id = $deleteID;
		$this->CI->centerdb->where('id',$sale_id);
		$this->CI->centerdb->delete($table_orders);
		
		$this->CI->centerdb->where('sale_id',$sale_id);
		$this->CI->centerdb->delete($table_order_items);
		
	    }
	}
    }

     function sync_sales(){
	/** tables
	 * sales
	 * sale_items	
	 * */	
	$table_sales = 'sales';
	$table_saleitems = 'sale_items';	
	$db1 = $this->CI->db->get_where($table_sales,array('warehouse_id'=>$this->CI->store_id))->result_array();
	$db2 = $this->CI->centerdb->get_where($table_sales,array('warehouse_id'=>$this->CI->store_id))->result_array();
	$data = $this->compare_server_local($db1,$db2,$table_sales);

	if(isset($data['update']) && !empty($data['update'])){
	    foreach($data['update'] as $k => $update_data){	    	
			unset($update_data->saleid);
			$sale_id = $update_data['id'];
			$this->CI->centerdb->where('id',$sale_id);
			$this->CI->centerdb->update($table_sales,$update_data);		
			$where = array('sale_id'=>$sale_id,'warehouse_id',$this->CI->store_id);		
			$saleitem_data = $this->sync_tables($table_saleitems,$where);				
	    }
	}
	if(isset($data['insert']) && !empty($data['insert'])){		
	    foreach($data['insert'] as $k => $insert_data){
		unset($insert_data->saleid);
		// $this->CI->centerdb->insert($table_sales,$insert_data);
		$sale_id = $insert_data['id'];		
		$sale_items = $this->getSaleItems_saleid($sale_id);		
		$this->CI->centerdb->insert_batch($table_saleitems,$sale_items);		
	    }
	    
	}
	if(isset($data['delete']) && !empty($data['delete'])){
	    foreach($data['delete'] as $k => $deleteID){
		$sale_id = $deleteID;
		$this->CI->centerdb->where('id',$sale_id);
		$this->CI->centerdb->delete($table_sales);
		
		$this->CI->centerdb->where('sale_id',$sale_id);
		$this->CI->centerdb->delete($table_saleitems);		
	    }
	}
	
    }
    function sync_salesbils(){

		/** tables
		 * bils
		 * bil_items
		 * payments
		 * sale_currency
		 **/
		
		$table_bils = 'bils';
		$table_bils_items = 'bil_items';

		$table_payments = 'payments';
		$table_sale_currency = 'sale_currency';
		
		$db1 = $this->CI->db->get_where($table_bils,array('warehouse_id'=>$this->CI->store_id))->result_array();
		$db2 = $this->CI->centerdb->get_where($table_bils,array('warehouse_id'=>$this->CI->store_id))->result_array();		
		$data = $this->compare_server_local($db1,$db2,$table_bils);
		/*echo "<pre>";
		print_r($data);die;*/
		if(isset($data['update']) && !empty($data['update'])){
		    foreach($data['update'] as $k => $update_data){
			unset($update_data->so_id);
			$bill_id = $update_data['id'];
			$this->CI->centerdb->where('id',$bill_id);
			$this->CI->centerdb->update($table_bils,$update_data);			
			$where = array('bil_id'=>$bill_id);		
			// $where = array('bil_id'=>$bill_id,'warehouse_id',$this->CI->store_id);		
			$where1 = array('bill_id'=>$bill_id,'warehouse_id',$this->CI->store_id);		
			$bils_items_data = $this->sync_tables($table_bils_items,$where);
			$payment_data = $this->sync_tables($table_sale_currency,$where);			
			$sale_currency_data = $this->sync_tables($table_payments,$where1);
		    }
		}

		if(isset($data['insert']) && !empty($data['insert'])){
		    foreach($data['insert'] as $k => $insert_data){
			unset($insert_data->so_id);
			// $this->CI->centerdb->insert($table_bils,$insert_data);
			$bill_id = $insert_data['id'];

			$bill_items = $this->getSaleBillItems_billid($bill_id);		
			$this->CI->centerdb->insert_batch($table_bils_items,$bill_items);

			$payments = $this->getPayments_billid($bill_id);		
			$this->CI->centerdb->insert_batch($table_payments,$payments);
			// print_r($this->CI->centerdb->last_query());die;
			$SaleCurrency = $this->getSaleCurrency_billid($bill_id);		
			$this->CI->centerdb->insert_batch($table_sale_currency,$SaleCurrency);			

		    }	    
		}

		if(isset($data['delete']) && !empty($data['delete'])){
		    foreach($data['delete'] as $k => $deleteID){
			$bill_id = $deleteID;
			$this->CI->centerdb->where('id',$bill_id);
			$this->CI->centerdb->delete($table_bils);
			
			$this->CI->centerdb->where('bil_id',$bill_id);
			$this->CI->centerdb->delete($table_bils_items);

			$this->CI->centerdb->where('bill_id',$bill_id);
			$this->CI->centerdb->delete($table_payments);
			
			$this->CI->centerdb->where('bil_id',$bill_id);
			$this->CI->centerdb->delete($table_sale_currency);
			
		    }
		}
    }
    function sync_holdsales(){
	/** tables
	 * suspended_bills
	 * suspended_items
	 * */
	
	$table_name = 'suspended_bills';
	$table_items = 'suspended_items';
	
	$db1 = $this->CI->db->get_where($table_name,array('store_id',$this->CI->store_id))->result_array();
	$db2 = $this->CI->centerdb->get_where($table_name,array('store_id',$this->CI->store_id))->result_array();		
	$data = $this->compare_server_local($db1,$db2,$table_name);
	if(isset($data['update']) && !empty($data['update'])){
	    foreach($data['update'] as $k => $update_data){
		unset($update_data->suspendid);
		$sale_id = $update_data['id'];
		$this->CI->centerdb->where('id',$sale_id);
		$this->CI->centerdb->update($table_name,$update_data);
		
		$where = array('suspend_id'=>$sale_id,'store_id',$this->CI->store_id);		
		$sale_order_item_data = $this->sync_tables($table_items,$where);
	    }
	}
	if(isset($data['insert']) && !empty($data['insert'])){
	    foreach($data['insert'] as $k => $insert_data){
		unset($insert_data->suspendid);
		$this->CI->centerdb->insert($table_name,$insert_data);
		$sale_id = $insert_data['id'];
		$sale_items = $this->getSaleSuspendedItems_saleid($sale_id);
		
		$this->CI->centerdb->insert_batch($table_items,$sale_items);
	    }
	    
	}
	if(isset($data['delete']) && !empty($data['delete'])){
	    foreach($data['delete'] as $k => $deleteID){
		$sale_id = $deleteID;
		$this->CI->centerdb->where('id',$sale_id);
		$this->CI->centerdb->delete($table_name);
		
		$this->CI->centerdb->where('suspend_id',$sale_id);
		$this->CI->centerdb->delete($table_items);
		
	    }
	}
    }
    function sync_returnsales(){
	/** tables
	 * return
	 * return_items
	 * */
	
	$table_name = 'returns';
	$table_items = 'return_items';
	
	$db1 = $this->CI->db->get_where($table_name,array('store_id',$this->CI->store_id))->result_array();
	$db2 = $this->CI->centerdb->get_where($table_name,array('store_id',$this->CI->store_id))->result_array();		
	$data = $this->compare_server_local($db1,$db2,$table_name);
	if(isset($data['update']) && !empty($data['update'])){
	    foreach($data['update'] as $k => $update_data){
		unset($update_data->s_no);
		$sale_id = $update_data['id'];
		$this->CI->centerdb->where('id',$sale_id);
		$this->CI->centerdb->update($table_name,$update_data);
		
		$where = array('return_id'=>$sale_id,'store_id',$this->CI->store_id);		
		$sale_order_item_data = $this->sync_tables($table_items,$where);
	    }
	}
	if(isset($data['insert']) && !empty($data['insert'])){
	    foreach($data['insert'] as $k => $insert_data){
		unset($insert_data->s_no);
		$this->CI->centerdb->insert($table_name,$insert_data);
		$sale_id = $insert_data['id'];
		$sale_items = $this->getSaleReturnItems_saleid($sale_id);
		
		$this->CI->centerdb->insert_batch($table_items,$sale_items);
	    }
	    
	}
	if(isset($data['delete']) && !empty($data['delete'])){
	    foreach($data['delete'] as $k => $deleteID){
		$sale_id = $deleteID;
		$this->CI->centerdb->where('id',$sale_id);
		$this->CI->centerdb->delete($table_name);
		
		$this->CI->centerdb->where('return_id',$sale_id);
		$this->CI->centerdb->delete($table_items);
		
	    }
	}
    }
    
    function sync_expenses(){
	/** tables
	 * expenses
	 * expense_details
	 * */
	
	$table_name = 'expenses';
	$table_items = 'expense_details';
	
	$db1 = $this->CI->db->get_where($table_name,array('store_id',$this->CI->store_id))->result_array();
	$db2 = $this->CI->centerdb->get_where($table_name,array('store_id',$this->CI->store_id))->result_array();		
	$data = $this->compare_server_local($db1,$db2,$table_name);
	if(isset($data['update']) && !empty($data['update'])){
	    foreach($data['update'] as $k => $update_data){
		unset($update_data->s_no);
		$expense_id = $update_data['id'];
		$this->CI->centerdb->where('id',$expense_id);
		$this->CI->centerdb->update($table_name,$update_data);
		
		$where = array('expense_id'=>$expense_id,'store_id',$this->CI->store_id);		
		$expense_details = $this->sync_tables($table_items,$where);
	    }
	}
	if(isset($data['insert']) && !empty($data['insert'])){
	    foreach($data['insert'] as $k => $insert_data){
		unset($insert_data->s_no);
		$this->CI->centerdb->insert($table_name,$insert_data);
		$expense_id = $insert_data['id'];
		$expense_details = $this->getExpenses_id($expense_id);
		
		$this->CI->centerdb->insert_batch($table_items,$expense_details);
	    }
	    
	}
	if(isset($data['delete']) && !empty($data['delete'])){
	    foreach($data['delete'] as $k => $deleteID){
		$expense_id = $deleteID;
		$this->CI->centerdb->where('id',$expense_id);
		$this->CI->centerdb->delete($table_name);
		
		$this->CI->centerdb->where('expense_id',$expense_id);
		$this->CI->centerdb->delete($table_items);
		
	    }
	}
    }
    function sync_dailysettlement(){
	$table_name = 'settlement';
	$where = array('store_id'=>$this->CI->store_id);
	$this->sync_tables($table_name,$where);
    }
    function sync_po(){
	/** tables
	 * pro_purchase_orders
	 * pro_purchase_order_items
	 * */
	
	$table_name = 'pro_purchase_orders';
	$table_items = 'pro_purchase_order_items';
	
	$db1 = $this->CI->db->get_where($table_name,array('store_id',$this->CI->store_id))->result_array();
	$db2 = $this->CI->centerdb->get_where($table_name,array('store_id',$this->CI->store_id))->result_array();		
	$data = $this->compare_server_local($db1,$db2,$table_name);
	if(isset($data['update']) && !empty($data['update'])){
	    foreach($data['update'] as $k => $update_data){
		$id = $update_data['id'];
		$this->CI->centerdb->where('id',$id);
		$this->CI->centerdb->update($table_name,$update_data);
		
		$where = array('purchase_order_id'=>$id,'store_id',$this->CI->store_id);		
		$item_details = $this->sync_tables($table_items,$where);
	    }
	}
	if(isset($data['insert']) && !empty($data['insert'])){
	    foreach($data['insert'] as $k => $insert_data){
		$this->CI->centerdb->insert($table_name,$insert_data);
		$id = $insert_data['id'];
		$item_details = $this->getPOItems_id($id);
		
		$this->CI->centerdb->insert_batch($table_items,$item_details);
	    }
	    
	}
	if(isset($data['delete']) && !empty($data['delete'])){
	    foreach($data['delete'] as $k => $deleteID){
		$id = $deleteID;
		$this->CI->centerdb->where('id',$id);
		$this->CI->centerdb->delete($table_name);
		
		$this->CI->centerdb->where('purchase_order_id',$id);
		$this->CI->centerdb->delete($table_items);
		
	    }
	}
    }
    function sync_purchase_invoice(){
	/** tables
	 * pro_purchase_invoices
	 * pro_purchase_invoice_items
	 * */
	
	$table_name = 'pro_purchase_invoices';
	$table_items = 'pro_purchase_invoice_items';
	
	$db1 = $this->CI->db->get_where($table_name,array('store_id',$this->CI->store_id))->result_array();
	$db2 = $this->CI->centerdb->get_where($table_name,array('store_id',$this->CI->store_id))->result_array();		
	$data = $this->compare_server_local($db1,$db2,$table_name);
	if(isset($data['update']) && !empty($data['update'])){
	    foreach($data['update'] as $k => $update_data){
		$id = $update_data['id'];
		$this->CI->centerdb->where('id',$id);
		$this->CI->centerdb->update($table_name,$update_data);
		
		$where = array('invoice_id'=>$id,'store_id',$this->CI->store_id);		
		$item_details = $this->sync_tables($table_items,$where);
	    }
	}
	if(isset($data['insert']) && !empty($data['insert'])){
	    foreach($data['insert'] as $k => $insert_data){
		$this->CI->centerdb->insert($table_name,$insert_data);
		$id = $insert_data['id'];
		$item_details = $this->getPIItems_id($id);
		
		$this->CI->centerdb->insert_batch($table_items,$item_details);
	    }
	    
	}
	if(isset($data['delete']) && !empty($data['delete'])){
	    foreach($data['delete'] as $k => $deleteID){
		$id = $deleteID;
		$this->CI->centerdb->where('id',$id);
		$this->CI->centerdb->delete($table_name);
		
		$this->CI->centerdb->where('invoice_id',$id);
		$this->CI->centerdb->delete($table_items);
		
	    }
	}
    }
    function sync_purchase_return(){
	/** tables
	 * pro_purchase_returns
	 * pro_purchase_return_items
	 * */
	
	$table_name = 'pro_purchase_returns';
	$table_items = 'pro_purchase_return_items';
	
	$db1 = $this->CI->db->get_where($table_name,array('store_id',$this->CI->store_id))->result_array();
	$db2 = $this->CI->centerdb->get_where($table_name,array('store_id',$this->CI->store_id))->result_array();		
	$data = $this->compare_server_local($db1,$db2,$table_name);
	if(isset($data['update']) && !empty($data['update'])){
	    foreach($data['update'] as $k => $update_data){
		$id = $update_data['id'];
		$this->CI->centerdb->where('id',$id);
		$this->CI->centerdb->update($table_name,$update_data);
		
		$where = array('return_id'=>$id,'store_id',$this->CI->store_id);		
		$item_details = $this->sync_tables($table_items,$where);
	    }
	}
	if(isset($data['insert']) && !empty($data['insert'])){
	    foreach($data['insert'] as $k => $insert_data){
		$this->CI->centerdb->insert($table_name,$insert_data);
		$id = $insert_data['id'];
		$item_details = $this->getPRItems_id($id);
		
		$this->CI->centerdb->insert_batch($table_items,$item_details);
	    }
	    
	}
	if(isset($data['delete']) && !empty($data['delete'])){
	    foreach($data['delete'] as $k => $deleteID){
		$id = $deleteID;
		$this->CI->centerdb->where('id',$id);
		$this->CI->centerdb->delete($table_name);
		
		$this->CI->centerdb->where('return_id',$id);
		$this->CI->centerdb->delete($table_items);
		
	    }
	}
    }
    
    function sync_store_transfers($id=false){
	/** tables
	 * pro_store_transfers
	 * pro_store_transfer_items
	 * pro_store_transfer_item_details
	 * */
	if($this->CI->centerdb_connected){
	    $table_name = 'pro_store_transfers';
	    $table_items = 'pro_store_transfer_items';
	    $table_item_details ='pro_store_transfer_item_details';
	    if($id){
		$db1 = $this->CI->db->get_where($table_name,array('store_id'=>$this->CI->store_id,'id'=>$id,'status'=>'approved'))->result_array();
		$db2 = $this->CI->centerdb->get_where($table_name,array('store_id'=>$this->CI->store_id,'id'=>$id))->result_array();	
	    }else{
		$db1 = $this->CI->db->get_where($table_name,array('store_id'=>$this->CI->store_id,'status'=>'approved'))->result_array();
		$db2 = $this->CI->centerdb->get_where($table_name,array('store_id'=>$this->CI->store_id))->result_array();	
	    }
	    	
	    $data = $this->compare_server_local($db1,$db2);
	    
	    if(isset($data['insert']) && !empty($data['insert'])){
		foreach($data['insert'] as $k => $insert_data){
		    unset($insert_data->s_no);
		    $this->CI->centerdb->insert($table_name,$insert_data);
		    $id = $insert_data['id'];
		    $where = array('store_transfer_id'=>$id,'store_id'=>$this->CI->store_id);		
		    $items = $this->sync_tables($table_items,$where);
		    
		    $where = array('store_transfer_id'=>$id,'store_id'=>$this->CI->store_id);		
		    $item_details = $this->sync_tables($table_item_details,$where);
		    
		    $this->sync_store_receivers($insert_data);
		}
		
	    }
	
	}
    }
    function sync_store_receivers($insertData){
	/** tables
	 * pro_store_receivers
	 * pro_store_receiver_items
	 * pro_store_receiver_item_details
	 * */
	//p($insertData,1);
	
	$q = $this->CI->db->get_where('pro_store_transfer_items',array('store_transfer_id'=>$insertData['id']));
	$items_data = $q->result_array();
	
	$table_name = 'pro_store_receivers';
	$table_items = 'pro_store_receiver_items';
        $table_item_details = 'pro_store_receiver_item_details';
        $insert_data = $insertData;
        $insert_data['store_id'] = $insertData['to_store'];
        $insert_data['approved_by'] = '';
        $insert_data['approved_on'] = '0000-00-00 00:00:00';
	$insert_data['processed_by'] = '';
        $insert_data['processed_on'] = '0000-00-00 00:00:00';
        $insert_data['status'] = 'new stock in';
	$insert_data['date'] = date('Y-m-d H:i:s');
        $n = $this->lastidStoreReceiver();
	$reference = 'SRE'.str_pad($n + 1, 5, 0, STR_PAD_LEFT);
        $insert_data['reference_no'] = str_replace('ST','SRE',$insert_data['reference_no']);
        $this->CI->centerdb->insert($table_name,$insert_data);
        $insert_id =$this->CI->centerdb->insert_id();
        $unique_id = $this->CI->site->generateUniqueTableID($insert_id,$insert_data['store_id']);
	if ($insert_id) {
	    $this->CI->centerdb->set('id',$unique_id);
            $this->CI->centerdb->where('s_no',$insert_id);
            $this->CI->centerdb->update($table_name);
	}
	
        foreach($items_data as $k => $item){
	    unset($item->s_no);
            $item['store_id'] = $insertData['to_store'];
	    $item['store_receiver_id'] = $unique_id;
	   
	    
	    unset($item['store_transfer_id']);
	   
	    unset($item['available_qty']);
	    unset($item['pending_qty']);
            $this->CI->centerdb->insert($table_items,$item);
            $i_insert_id =$this->CI->centerdb->insert_id();
            $i_unique_id = $this->CI->site->generateUniqueTableID($i_insert_id,$insert_data['store_id']);
            if ($i_insert_id) {
                $this->CI->centerdb->set('id',$i_unique_id);
                $this->CI->centerdb->where('s_no',$i_insert_id);
                $this->CI->centerdb->update($table_items);
		
		$i_details = $this->getStoreTItemDetails($item['id']);
		foreach($i_details as $kk => $item_d){
		    $item_d['store_id'] = $insertData['to_store'];
		    $item_d['store_receiver_id'] = $unique_id;
		    $item_d['store_receiver_item_id'] = $i_unique_id;
		    $item_d['transfer_qty'] = $item_d['transfer_qty'];
		    unset($item_d['store_transfer_id']);unset($item_d['store_transfer_item_id']);
		    unset($item_d['request_qty']);
		    unset($item_d['available_qty']);
		    unset($item_d['transfer_qty']);
		    unset($item_d['pending_qty']);
		    $this->CI->centerdb->insert($table_item_details,$item_d);
		    $id_insert_id =$this->CI->centerdb->insert_id();//p($this->CI->centerdb->error());exit;
		    $id_unique_id = $this->CI->site->generateUniqueTableID($id_insert_id,$insert_data['store_id']);
		    if ($id_insert_id) {
			$this->CI->centerdb->set('id',$id_unique_id);
			$this->CI->centerdb->where('s_no',$id_insert_id);
			$this->CI->centerdb->update($table_item_details);
		    }
		}
		
            }
        }
        
    }
    function sync_storeIndentRequests($id=false){
	/** tables
	 * pro_store_request
	 * pro_store_request_items
	 * */
	if($this->CI->centerdb_connected){
	    $i_table_name = 'pro_store_request';$r_table_name = 'pro_store_indent_receive';
	    
	    $i_table_items = 'pro_store_request_items';$r_table_items = 'pro_store_indent_receive_items';
	    
	    /* sync store indent */
	    if($id){
		$db1 = $this->CI->db->get_where($i_table_name,array('store_id'=>$this->CI->store_id,'id'=>$id,'status'=>'approved'))->result_array();
		$db2 = $this->CI->centerdb->get_where($i_table_name,array('store_id'=>$this->CI->store_id,'id'=>$id))->result_array();	
	    }else{
		$db1 = $this->CI->db->get_where($i_table_name,array('store_id'=>$this->CI->store_id,'status'=>'approved'))->result_array();
		$db2 = $this->CI->centerdb->get_where($i_table_name,array('store_id'=>$this->CI->store_id))->result_array();	
	    }
	    //echo $this->CI->store_id;
	    	
	    $data = $this->compare_server_local($db1,$db2);
	    
	    if(isset($data['insert']) && !empty($data['insert'])){
		foreach($data['insert'] as $k => $insert_data){
		    unset($insert_data->s_no);
		    $this->CI->centerdb->insert($i_table_name,$insert_data);
		    $id = $insert_data['id'];
		    $item_details = $this->getSR_Items_id($id);
		    
		    $this->CI->centerdb->insert_batch($i_table_items,$item_details);
		    
		}
		
	    }
	    
	    /* sync store indent receiver */
	    if($id){
		$db1 = $this->CI->db->get_where($i_table_name,array('store_id'=>$this->CI->store_id,'id'=>$id,'status'=>'approved'))->result_array();
		$db2 = $this->CI->centerdb->get_where($r_table_name,array('store_id'=>$this->CI->store_id,'id'=>$id))->result_array();	
	    }else{
		$db1 = $this->CI->db->get_where($i_table_name,array('store_id'=>$this->CI->store_id,'status'=>'approved'))->result_array();
		$db2 = $this->CI->centerdb->get_where($r_table_name,array('store_id'=>$this->CI->store_id))->result_array();	
	    }
	    	
	    $data = $this->compare_server_local($db1,$db2);
	    
	    if(isset($data['insert']) && !empty($data['insert'])){
		foreach($data['insert'] as $k => $insert_data){
		    unset($insert_data->s_no);
		    $this->CI->centerdb->insert($r_table_name,$insert_data);
		    $id = $insert_data['id'];
		    $item_details = $this->getSR_Items_id($id);
		    
		    $this->CI->centerdb->insert_batch($r_table_items,$item_details);
		    
		}
		
	    }
	   
	}
    }
    function sync_issued_giftvouchers(){
	$table_name = 'giftvouchers_issued';
	$where = array('store_id'=>$this->CI->store_id);
	$this->sync_tables($table_name,$where);
    }
    function sync_blocked_giftvouchers(){
	$table_name = 'giftvouchers_blocked';
	$where = array('store_id'=>$this->CI->store_id);
	$this->sync_tables($table_name,$where);
    }
    
    /***********loyalty ******************/
    function sync_issued_loyaltycards(){
	$table_name = 'loyaltycard_issued';
	$where = array('store_id'=>$this->CI->store_id);
	$this->sync_tables($table_name,$where);
    }
    function sync_blocked_loyaltycards(){
	$table_name = 'loyaltycards_blocked';
	$where = array('store_id'=>$this->CI->store_id);
	$this->sync_tables($table_name,$where);
    }
    /************* shift management ********************/
    function sync_shiftcreation(){
	$table_name = 'shifts';
	$where = array('store_id'=>$this->CI->store_id);
	$this->sync_tables($table_name,$where);
    }
    
    function sync_shiftsettlement(){
	$table_name = 'shifts_settlement';
	
	/// compare settlement ////
	$de_b1 = $this->CI->db->list_fields($table_name);
	$de_b2 = $this->CI->centerdb->list_fields($table_name);
	$de_b1_flip = array_flip($de_b1);
	$de_b2_flip = array_flip($de_b2);
	$result=array_diff_key($de_b1_flip,$de_b2_flip);
	foreach($result as $k => $row){
	    $new_col = $k;
	    $query = "ALTER TABLE {$this->CI->centerdb->dbprefix('shifts_settlement')} ADD  `".$new_col."` VARCHAR(100) NULL";
	    $this->CI->centerdb->query($query);
	}
	
	
	$where = array('store_id'=>$this->CI->store_id);
	$this->sync_tables($table_name,$where);
    }
    
    function sync_shiftcard_settlement(){
	$table_name = 'shift_card_settlement';
	$where = array('store_id'=>$this->CI->store_id);
	$this->sync_tables($table_name,$where);
    }
    function sync_shift_coupon_settlement(){
	$table_name = 'shift_coupon_settlement';
	$where = array('store_id'=>$this->CI->store_id);
	$this->sync_tables($table_name,$where);
    }
    function sync_shift_wallet_settlement(){
	$table_name = 'shift_wallet_settlement';
	$where = array('store_id'=>$this->CI->store_id);
	$this->sync_tables($table_name,$where);
    }
    function sync_shift_cheque_settlement(){
	$table_name = 'shift_cheque_settlement';
	$where = array('store_id'=>$this->CI->store_id);
	$this->sync_tables($table_name,$where);
    }
    
     function sync_notifications(){
	$table = 'notifications';
	$this->sync_tables($table);
    }
    
    function sync_tables($table_name,$where){
	$db1 = $this->CI->db->get_where($table_name,$where)->result_array();
	$db2 = $this->CI->centerdb->get_where($table_name,$where)->result_array();		
	$a = $this->compare_server_local($db1,$db2,$table_name);
	return $a;
	
    }
   
    
    function compare_server_local($DB1,$DB2,$table=false){
	$result = array();$localkeys = array();
	$localDB =  array();
	$serverDB = array();
	//*************** change Auto increment ID as array Key ****************//
	    foreach($DB1 as $key => $val) {
		$k = $val['id'];
		$localDB[$k] = $val; 
	    }
	    foreach($DB2 as $key => $val) {
		$k = $val['id'];
		$serverDB[$k] = $val; 
	    }
	//*************** Compare ServerDB and LocalDB  - For update and Insert ****************//
	foreach($localDB as $key => $val) { 
	    $key = $val['id'];
	    if(isset($serverDB[$key])){
		if(is_array($val)  && is_array($serverDB[$key])){		
		    $array_diff = array_diff($val, $serverDB[$key]);
		    if(!empty($array_diff)){
			if(isset($val['s_no'])){unset($val['s_no']);}
			$result['update'][$key] = $val;
		    }
		}
	    }else{
		if(isset($val['s_no'])){unset($val['s_no']);}
		$result['insert'][$key] = $val;
	    }
	    $localkeys[$key] = true;	
	    //// check for delete	
	}
	//*************** Compare ServerDB and LocalDB  - For Delete ****************//
	$l = array_diff_key($serverDB,$localkeys); 
	foreach($l as $k => $lk){ 
	    $result['delete'][] = $k;
	}
	//*************** sync table ************//
	$store_id = $this->CI->store_id;
	if($table){
	   $this->sync_table($result,$table); 
	}
	
	/*echo "<pre>";
	print_r($result);die;*/
	return $result;
    }
    function sync_table($data,$table){
	if(isset($data['update']) && !empty($data['update'])){
	    $updateData = $data['update'];
	    $this->update_table($updateData,$table);
	}
	if(isset($data['insert']) && !empty($data['insert'])){
	    $insertData = $data['insert'];
	    $this->insert_table($insertData,$table);
	}
	if(isset($data['delete']) && !empty($data['delete'])){
	    $deleteIDS = $data['delete'];
	    $this->delete_table($deleteIDS,$table);
	}
    }
    function update_table($data,$table){
	$this->CI->centerdb->update_batch($table,$data,'id');
    }
    function insert_table($data,$table){
	$this->CI->centerdb->insert_batch($table,$data);
    }
    function delete_table($deleteIds,$table){
	$this->CI->centerdb->where_in('id',$deleteIds);
	$this->CI->centerdb->delete($table);
    }
    
    
    /////////////// FROM STORE - END /////////////////////
    

    function getSaleOrderItems($orderid){
		$q = $this->CI->db->get_where('order_items',array('sale_id'=>$orderid));
		$data =  array();
		foreach($q->result() as $k => $row){
		    unset($row->s_no);
		    $data[$k] = $row;
		}
		return $data;
    }

    function getKitchenOrders($orderid){
		$q = $this->CI->db->get_where('kitchen_orders',array('sale_id'=>$orderid));
		$data =  array();
		foreach($q->result() as $k => $row){
		    unset($row->s_no);
		    $data[$k] = $row;
		}
		return $data;
    }

    function getRestaurantTableOrders($orderid){
		$q = $this->CI->db->get_where('restaurant_table_orders',array('order_id'=>$orderid));
		$data =  array();
		foreach($q->result() as $k => $row){
		    unset($row->s_no);
		    $data[$k] = $row;
		}
		return $data;
    }
    function getRestaurantTableSessions($orderid){
		$q = $this->CI->db->get_where('restaurant_table_sessions',array('order_id'=>$orderid));
		$data =  array();
		foreach($q->result() as $k => $row){
		    unset($row->s_no);
		    $data[$k] = $row;
		}
		return $data;
    }    
    
    function getSaleItems_saleid($saleid){
	$q = $this->CI->db->get_where('sale_items',array('sale_id'=>$saleid));	
	$data =  array();
	foreach($q->result() as $k => $row){
	    unset($row->s_no);
	    $data[$k] = $row;
	}
	return $data;	
    }
    
    function getSaleBillItems_billid($bill_id){
	$q = $this->CI->db->get_where('bil_items',array('bil_id'=>$bill_id));
	$data =  array();
	foreach($q->result() as $k => $row){
	    unset($row->s_no);
	    $data[$k] = $row;
	}
	return $data;
    }



    function getPayments_billid($bill_id){
    	
	$q = $this->CI->db->get_where('payments',array('bill_id'=>$bill_id));
	/*print_r($this->CI->db->last_query());
	print_r($this->CI->db->error());die;*/
	$data =  array();
	foreach($q->result() as $k => $row){
	    unset($row->s_no);
	    $data[$k] = $row;
	}
	return $data;	
    }
    function getSaleCurrency_billid($bill_id){
	$q = $this->CI->db->get_where('sale_currency',array('bil_id'=>$bill_id));
	$data =  array();
	foreach($q->result() as $k => $row){
	    unset($row->s_no);
	    $data[$k] = $row;
	}
	return $data;
    }
   

    function getSaleSuspendedItems_saleid($saleid){
	$q = $this->CI->db->get_where('suspended_items',array('suspend_id'=>$saleid));
	$data =  array();
	foreach($q->result() as $k => $row){
	    unset($row->s_no);
	    $data[$k] = $row;
	}
	return $data;
    }
    function getSaleReturnItems_saleid($saleid){
	$q = $this->CI->db->get_where('return_items',array('return_id'=>$saleid));
	$data =  array();
	foreach($q->result() as $k => $row){
	    unset($row->s_no);
	    $data[$k] = $row;
	}
	return $data;
    }
    function getExpenses_id($expense_id){
	$q = $this->CI->db->get_where('expense_details',array('expense_id'=>$expense_id));
	$data =  array();
	foreach($q->result() as $k => $row){
	    unset($row->s_no);
	    $data[$k] = $row;
	}
	return $data;
    }
    function getPOItems_id($id){
	$q = $this->CI->db->get_where('pro_purchase_order_items',array('purchase_order_id'=>$id));
	$data =  array();
	foreach($q->result() as $k => $row){
	    unset($row->s_no);
	    $data[$k] = $row;
	}
	return $data;
    }
    function getPIItems_id($id){
	$q = $this->CI->db->get_where('pro_purchase_invoice_items',array('invoice_id'=>$id));
	$data =  array();
	foreach($q->result() as $k => $row){
	    unset($row->s_no);
	    $data[$k] = $row;
	}
	return $data;
    }
    function getPRItems_id($id){
	$q = $this->CI->db->get_where('pro_purchase_return_items',array('return_id'=>$id));
	$data =  array();
	foreach($q->result() as $k => $row){
	    unset($row->s_no);
	    $data[$k] = $row;
	}
	return $data;
    }
    function getPST_Items_id($id){
	$q = $this->CI->db->get_where('pro_store_transfer_items',array('store_transfer_id'=>$id));
	$data =  array();
	foreach($q->result() as $k => $row){
	    unset($row->s_no);
	    $data[$k] = $row;
	}
	return $data;
    }
    public function lastidStoreReceiver(){//get from center server
	$this->CI->centerdb->order_by('id' , 'DESC');
        $q = $this->CI->centerdb->get('pro_store_receivers');
        if ($q->num_rows() > 0) {
            return $q->row('id');
        }
        return 0;
    }
    function getSR_Items_id($id){
	$q = $this->CI->db->get_where('pro_store_request_items',array('store_request_id'=>$id));
	$data =  array();
	foreach($q->result() as $k => $row){
	    unset($row->s_no);
	    $data[$k] = $row;
	}
	return $data;
    }
    function getStoreTItemDetails($id){
	$q = $this->CI->db->get_where('pro_store_transfer_item_details',array('store_transfer_item_id'=>$id));
	$data =  array();
	foreach($q->result_array() as $k => $row){
	    unset($row['s_no']);
	    $data[$k] = $row;
	}
	return $data;
    }
}