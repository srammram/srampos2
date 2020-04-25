<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Archival_data extends MY_Controller{
    function __construct() {
        parent::__construct();
    }
	  public function create_notifications(){
		   ob_end_clean();
           ignore_user_abort();
           ob_start();
           header("Connection: close");
           echo @json_encode($out);
           header("Content-Length: " . ob_get_length());
           @ob_end_flush();
           flush();
		   $notification_array = json_decode(file_get_contents('php://input'), true);
	       $this->load->library('socketemitter');
		if(!empty($notification_array)){	
			$all = $this->db->insert('notiy', $notification_array['insert_array']);	
			$notifyid = $this->db->insert_id();
			if(isset($notification_array['from_role']) && $notification_array['from_role'] != SALE){	
				if($notification_array['from_role'] == WAITER){
					$role_form = 'Waiter';
				}elseif($notification_array['from_role'] == KITCHEN){
					$role_form = 'Kitchen';
				}elseif($notification_array['from_role'] == CASHIER){
					$role_form = 'Cashier';					
				}
				if($notification_array['insert_array']['role_id'] == WAITER){
					$role_to = 'Waiter';
				}elseif($notification_array['insert_array']['role_id'] == KITCHEN){
					$role_to = 'Kitchen';
				}elseif($notification_array['insert_array']['role_id'] == CASHIER){
					$role_to = 'Cashier';					
				}
				
				$notification = array(
					'msg' => $role_form.' to  '.$role_to,
					'type' => $notification_array['insert_array']['type'],
					'user_id' => $notification_array['insert_array']['user_id'],	
					'table_id' => $notification_array['insert_array']['table_id'],	
					'role_id' => SALE,
					'warehouse_id' => $notification_array['insert_array']['warehouse_id'],
					'created_on' => date('Y-m-d H:m:s'),
					'is_read' => 0
				);	
				$s = $this->db->insert('notiy', $notification);	
			}
			
		
			$notification_title = $notification_array['insert_array']['type'];
			$notification_message = $notification_array['insert_array']['msg'];
			if($this->isSocketEnabled()){
			    $emit_notification['title'] = $notification_title;
			    $emit_notification['msg'] = $notification_message;
			    $time1 = microtime(true);
			    // echo "step_one:".$time1;
			    $this->socketemitter->setEmit('notification', $emit_notification);
			    $time2 = microtime(true);
			    // echo "step_two:".$time2;
			}
			return $notifyid;
		}
		return false;
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
		$logs=array("start_time"=> date("Y-m-d H:i:s",time()),"process"=>"Insert");
	    $archival_tables = $this->site->get_archival_tables();
		$archival_details = $this->site->get_achival();
	    if($archival_tables &&  date('Y-m-d',strtotime($archival_details->endtime))  != date('Y-m-d') && $archival_details->status !="Processing"){
			 file_put_contents('archival_log/archival_logs.txt',json_encode($logs),FILE_APPEND);
	    $this->site->start_achival_time();
	    foreach($archival_tables as $k => $table){
		$archival_data=$this->site->get_new_archival_data($table->archival_from_table,$table->archival_to_table);
		if(!empty($archival_data)){
			 $result=$this->site->insert_archival_data($archival_data,$table->archival_to_table);
              if($result){
				  $logs=array("total_row:-"=>count($archival_data),"Tablename:-"=>$table->archival_to_table,"Status"=>"Success","result"=>$result);
			  }else{
				  $logs=array("total_row:-"=>count($archival_data),"Tablename:-"=>$table->archival_to_table,"Status"=>"Failed","result"=>$result);
			  }
			       file_put_contents('archival_log/archival_logs.txt',json_encode($logs),FILE_APPEND);
	          }else{
				   $logs=array("total_row:-"=>0,"Tablename:-"=>$table->archival_to_table,"Status"=>"Not Proccess","result"=>"Empty Row");
				   file_put_contents('archival_log/archival_logs.txt',json_encode($logs),FILE_APPEND);
			  }
			  
			 }
			
			  $this->delete();
		      $this->site->end_achival_time();
	    }
    }
	function delete(){
		if($this->Settings->archival_days !=0){
		$archival_date = date('Y-m-d');
		  $this->sales($archival_date);
		  $this->order($archival_date);
		  $this->bill($archival_date);
		  $this->bbq($archival_date);
		  $this->notify($archival_date);
		}
		return false;
	}
	function sales($date){
		$tablename='sales';
		$logs=array("start_time"=> date("Y-m-d H:i:s",time()),"process"=>"Sales delete");
		if(!empty($date)){
		 if($this->site->check_archival_table($tablename)){
			 $where="date(date)<"."'".$date."'";
			 $delete_sales_id=$this->site->select_record($tablename,$where);
			 
			 if(!empty($delete_sales_id)){
				    $result=$this->site->record_delete($tablename,'id',$delete_sales_id);
				  
				   //sale_items 
				    $tablename="sale_items";
				    $this->site->record_delete($tablename,'sale_id',$delete_sales_id);
				  //sale_currency
				    $tablename="sale_currency";
				    $this->site->record_delete($tablename,'sale_id',$delete_sales_id);
					//payments
					$tablename="payments";
				    $this->site->record_delete($tablename,'sale_id',$delete_sales_id);
					//rough_tender_sale_currency
				    $tablename="rough_tender_sale_currency";
				    $this->site->record_delete($tablename,'sale_id',$delete_sales_id);
					
					//rough_tender_payments
				    $tablename="rough_tender_payments";
				    $this->site->record_delete($tablename,'sale_id',$delete_sales_id);
					//archive_sale_items
				    $tablename="archive_sale_items";
				    $this->site->record_delete($tablename,'sale_id',$delete_sales_id);
					//archive_sale_currency
					 $tablename="archive_sale_currency";
				     $this->site->record_delete($tablename,'sale_id',$delete_sales_id);
					 
					 //archive_payments
					 $tablename="archive_payments";
				     $this->site->record_delete($tablename,'sale_id',$delete_sales_id);
					 
					 //sale_return
					 $tablename="sale_return";
				     $this->site->record_delete($tablename,'sale_id',$delete_sales_id);
					 
					 //archive_rough_tender_sale_currency
					 $tablename="archive_rough_tender_sale_currency";
				     $this->site->record_delete($tablename,'sale_id',$delete_sales_id);
					 
					 //archive_rough_tender_payments
					 $tablename="archive_rough_tender_payments";
				     $this->site->record_delete($tablename,'sale_id',$delete_sales_id);
					 
					
					
					 //archive_kitchen_orders
					 $tablename="archive_kitchen_orders";
				     $this->site->record_delete($tablename,'sale_id',$delete_sales_id);
					
					 //archive_order_items
					 $tablename="archive_order_items";
				     $this->site->record_delete($tablename,'sale_id',$delete_sales_id);
					 
					  //archive_sales
					 $tablename="archive_sales";
				     $this->site->record_delete($tablename,'sale_id',$delete_sales_id);
					 $logs[]=array("Status"=>"Sales Data Deleted");
					 file_put_contents('archival_log/archival_logs.txt',json_encode($logs),FILE_APPEND);
					 return true;
			      }
		      }
		   }
		return false;
	}
	function order($date){
		$tablename='orders';
		if(!empty($date)){
			 if($this->site->check_archival_table($tablename)){
			 $where="date(date)<"."'".$date."'";
			 $delete_order_id=$this->site->select_record($tablename,$where);
			  if(!empty($delete_order_id)){
				    $this->site->record_delete($tablename,'id',$delete_order_id);
					 //kitchen_orders 
				    $tablename="kitchen_orders";
				    $this->site->record_delete($tablename,'sale_id',$delete_order_id);
					//order_items
					$tablename="order_items";
				    $this->site->record_delete($tablename,'sale_id',$delete_order_id);
				    //restaurant_table_orders
				    $tablename="restaurant_table_orders";
				    $this->site->record_delete($tablename,'order_id',$delete_order_id);
					//restaurant_table_sessions
				    $tablename="restaurant_table_sessions";
				    $this->site->record_delete($tablename,'order_id',$delete_order_id);
					//addon_sale_items
				    $tablename="addon_sale_items";
				    $this->site->record_delete($tablename,'order_id',$delete_order_id);
					//archive_restaurant_table_orders
				    $tablename="archive_restaurant_table_orders";
				    $this->site->record_delete($tablename,'order_id',$delete_order_id);
					//archive_restaurant_table_sessions
				    $tablename="archive_restaurant_table_sessions";
				    $this->site->record_delete($tablename,'order_id',$delete_order_id);
					//archive_orders
					$tablename="archive_orders";
				    $this->site->record_delete($tablename,'order_id',$delete_order_id);
					 $logs[]=array("Status"=>"Orders Data Deleted");
					 file_put_contents('archival_log/archival_logs.txt',json_encode($logs),FILE_APPEND);
				 
			  }
			 }
		}
		return false;
	}
  function  bill($date){
	$tablename='bils';
	if(!empty($date)){
	   if($this->site->check_archival_table($tablename)){
		 $where="date(date)<"."'".$date."'";
		 $delete_bill_id=$this->site->select_record($tablename,$where);
	    if(!empty($delete_bill_id)){
		  $this->site->record_delete($tablename,'id',$delete_bill_id);
		//bil_items
		 $tablename="bil_items";
		 $this->site->record_delete($tablename,'bil_id',$delete_bill_id);
		 //addon_bill_items
		 $tablename="addon_bill_items";
		$this->site->record_delete($tablename,'bil_id',$delete_bill_id);
		  //archive_bil_items
		 $tablename="archive_bil_items";
		 $this->site->record_delete($tablename,'bil_id',$delete_bill_id);
		   //archive_bils
		 $tablename="archive_bils";
		 $this->site->record_delete($tablename,'bil_id',$delete_bill_id);
		 //archive_bil_items
		 $tablename="archive_bil_items";
		 $this->site->record_delete($tablename,'bil_id',$delete_bill_id);
		  $logs[]=array("Status"=>"Bills Data Deleted");
	     file_put_contents('archival_log/archival_logs.txt',json_encode($logs),FILE_APPEND);
	}
	}
	}
  }
 function bbq($date){
	$tablename='bbq';
	if(!empty($date)){
	   if($this->site->check_archival_table($tablename)){
		  $where="date(created_on)<"."'".$date."'";
		 $delete_bill_id=$this->site->select_record($tablename,$where);
	    if(!empty($delete_bill_id)){
			 $this->site->record_delete($tablename,'id',$delete_bill_id);
		//bil_items
		 $tablename="bbq_bil_items";
		 $this->site->record_delete($tablename,'bil_id',$delete_bill_id);
		 //bil_items
		 $tablename="archive_bbq";
		 $this->site->record_delete($tablename,'bil_id',$delete_bill_id);
		  //bil_items
		 $tablename="archive_bbq_bil_items";
		 $this->site->record_delete($tablename,'bil_id',$delete_bill_id);
		 $logs[]=array("Status"=>"BBq Bills Data Deleted");
			 file_put_contents('archival_log/archival_logs.txt',json_encode($logs),FILE_APPEND);
		}
	   }
	  }
	}
	function notify($date){
	$tablename='notiy';
	if(!empty($date)){
	   if($this->site->check_archival_table($tablename)){
		 $where="date(created_on)<"."'".$date."'";
		 $notifyid=$this->site->select_record($tablename,$where);
	    if(!empty($notifyid)){
		 $result=  $this->site->record_delete($tablename,'id',$notifyid);
		 if($result){
			$logs[]=array("total_row:-"=>$result,"Tablename:-"=>$tablename,"Status"=>"Deleted Success","result"=>$result);
		  }else{
			$logs[]=array("total_row:-"=>$result,"Tablename:-"=>$tablename,"Status"=>"Failed","result"=>$result);
			}
			 file_put_contents('archival_log/archival_logs.txt',json_encode($logs),FILE_APPEND);
		}
	   }
	  }
	}
/* 	function archival_process(){
		$archival_details = $this->site->get_achival();
		$archival_date = date('Y-m-d', strtotime('-'.$this->Settings->archival_days.' days', strtotime(date('Y-m-d '))));
	    if( date('Y-m-d',strtotime($archival_details->endtime))  != date('Y-m-d')){
		     $this->site->start_achival_time();
		     $sales_data=$this->site->get_new_archival_data('srampos_sales','srampos_sales_archival');
			 echo 4;
	    	 if(!empty($sales_data)){
				 echo 3;
			 if($this->site->insert_archival_data($sales_data,'srampos_sales_archival')){
			 $where="date(date)<"."'".$archival_date."'";
			 $delete_sales_id=$this->site->select_record('srampos_sales',$where);
			 echo 2;
			 }	
				if(!empty($delete_sales_id)){
					echo 1;
					 $this->site->record_delete('srampos_sales','id',$delete_sales_id);
					 die;
			 ////kitchen_orders 
			 $tablename="srampos_kitchen_orders_archival";
			 $kitchen_orders=$this->site->get_new_archival_data('srampos_kitchen_orders',$tablename);
			 if($this->site->insert_archival_data($kitchen_orders,$tablename)){
			 $this->site->record_delete($tablename,'sale_id',$delete_sales_id);
			}
		
			 //sale_currency      
			  $tablename="srampos_sale_currency_archival";
			  $sale_currency=$this->site->get_new_archival_data('srampos_sale_currency',$tablename);
			  if($this->site->insert_archival_data($sale_currency,$tablename)){
			    $this->site->record_delete($tablename,'sale_id',$delete_sales_id);
				}
			 //payments               
			$tablename="srampos_payments_archival";
			$payments=$this->site->get_new_archival_data('srampos_payments',$tablename);
			if($this->site->insert_archival_data($payments,$tablename)){
			$this->site->record_delete($tablename,'sale_id',$delete_sales_id);
			}
			
			   //rough_tender_sale_currency  
			   $tablename="srampos_rough_tender_sale_currency_archival";
			   $rough_tender_sale_currency=$this->site->get_new_archival_data('srampos_rough_tender_sale_currency',$tablename);
			   if($this->site->insert_archival_data($rough_tender_sale_currency,$tablename)){
				$this->site->record_delete($tablename,'sale_id',$delete_sales_id);
			  }
			    //rough_tender_payments  
			 $tablename="srampos_rough_tender_payments_archival";
			 $rough_tender_payments=$this->site->get_new_archival_data('srampos_rough_tender_payments',$tablename);
			 if($this->site->insert_archival_data($rough_tender_payments,$tablename)){
			  $this->site->record_delete($tablename,'sale_id',$delete_sales_id);
			 }
			   
			   //archive_sale_items  
			  $tablename="srampos_archive_sale_items_archival";
			  $archive_sale_items=$this->site->get_new_archival_data('srampos_archive_sale_items',$tablename);
			  if($this->site->insert_archival_data($archive_sale_items,$tablename)){
			   $this->site->record_delete($tablename,'sale_id',$delete_sales_id);
			  }
			   
			    //archive_sale_currency  
			   $tablename="srampos_archive_sale_currency_archival";
			   $archive_sale_currency=$this->site->get_new_archival_data('srampos_archive_sale_currency',$tablename);
		    	if($this->site->insert_archival_data($archive_sale_currency,$tablename)){
			   $this->site->record_delete($tablename,'sale_id',$delete_sales_id);
              }

			      //archive_payments 
			   $tablename="srampos_archive_payments_archival";
               $archive_payments=$this->site->get_new_archival_data('srampos_archive_payments',$tablename);
               if($this->site->insert_archival_data($archive_payments,$tablename)){
               $this->site->record_delete($tablename,'sale_id',$delete_sales_id);
               }

			   
			   //sale_return        
			   $tablename="srampos_sale_return_archival";
			   $sale_return=$this->site->get_new_archival_data('srampos_sale_return',$tablename);
				if($this->site->insert_archival_data($sale_return,$tablename)){
				$this->site->record_delete($tablename,'sale_id',$delete_sales_id);
				}
			   //archive_rough_tender_sale_currency          
		        $tablename="srampos_archive_rough_tender_sale_currency_archival";
				$archive_rough_tender_currency=$this->site->get_new_archival_data('srampos_archive_rough_tender_sale_currency',$tablename);
				if($this->site->insert_archival_data($archive_rough_tender_currency,$tablename)){
				$this->site->record_delete($tablename,'sale_id',$delete_sales_id);
				}
			   //archive_rough_tender_payments   
			   $tablename="srampos_archive_rough_tender_payments_archival";
			   $archive_rough_tender_payments=$this->site->get_new_archival_data('srampos_archive_rough_tender_payments',$tablename);
			   if($this->site->insert_archival_data($archive_rough_tender_payments,$tablename)){
			   $this->site->record_delete($tablename,'sale_id',$delete_sales_id);
			   }
			   //order_items               
			   $tablename="srampos_order_items_archival";
			   $order_items=$this->site->get_new_archival_data('srampos_order_items',$tablename);
			   if($this->site->insert_archival_data($order_items,$tablename)){
			   $this->site->record_delete($tablename,'sale_id',$delete_sales_id);
			   }
			   //archive_kitchen_orders   
			   $tablename="srampos_archive_kitchen_orders_archival";
			   $archive_kitchen_orders=$this->site->get_new_archival_data('srampos_archive_kitchen_orders',$tablename);
			   if($this->site->insert_archival_data($archive_kitchen_orders,$tablename)){
			   $this->site->record_delete($tablename,'sale_id',$delete_sales_id);
			   }
			   //archive_order_items   
			   $tablename="srampos_archive_order_items_archival";
			   $archive_order_items=$this->site->get_new_archival_data('srampos_archive_order_items',$tablename);
			   if($this->site->insert_archival_data($archive_order_items,$tablename)){
			   $this->site->record_delete($tablename,'sale_id',$delete_sales_id);
			   }
			    //archive_sales         
			   $tablename="srampos_archive_sales_archival";
			   $archive_order_items=$this->site->get_new_archival_data('srampos_archive_sales',$tablename);
			   if($this->site->insert_archival_data($archive_order_items,$tablename)){
			   $this->site->record_delete($tablename,'id',$delete_sales_id);
			   }
			   	 //sale_items                 
			$tablename="srampos_sale_items_archival";
			$sale_items=$this->site->get_new_archival_data('srampos_sale_items',$tablename);
			if($this->site->insert_archival_data($sale_items,$tablename)){
			$this->site->record_delete($tablename,'sale_id',$delete_sales_id);
			}
			 
			 return true;
			 }
			 }
			  $this->site->end_achival_time();
			 
		}
	}
	function  order_archival(){
		
		$archival_details = $this->site->get_achival();
		$archival_date = date('Y-m-d', strtotime('-'.$this->Settings->archival_days.' days', strtotime(date('Y-m-d '))));
	    if( date('Y-m-d',strtotime($archival_details->endtime))  != date('Y-m-d')){
			   
		    $order_data=$this->site->get_new_archival_data('srampos_orders','srampos_orders_archival');
	    	 if(!empty($order_data)){
			 if($this->site->insert_archival_data($order_data,'srampos_orders_archival')){
			 $where="date(date)<"."'".$archival_date."'";
			 $delete_order_id=$this->site->select_record('srampos_orders',$where);
			 }	
				if(!empty($delete_order_id)){
					 $this->site->record_delete('srampos_orders','id',$delete_order_id);
					 
					  //restaurant_table_orders
					 $tablename="srampos_archive_sales_archival";
					 $archive_order_items=$this->site->get_new_archival_data('srampos_archive_sales',$tablename);
					 if($this->site->insert_archival_data($archive_order_items,$tablename)){
					$this->site->record_delete($tablename,'id',$delete_sales_id);
}
					 
					 
				}
		
		
		
		
		}
	}
	} */
	
}