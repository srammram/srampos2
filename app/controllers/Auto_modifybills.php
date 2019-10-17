<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Auto_modifybills extends MY_Controller
{

    function __construct() {
        parent::__construct();
	$this->load->admin_model('reports_model');
    }
    function modify(){
	ob_end_clean();
        ignore_user_abort();
        ob_start();
        header("Connection: close");
        echo @json_encode($out);
        header("Content-Length: " . ob_get_length());
        @ob_end_flush();
        flush();
        $bills = json_decode(file_get_contents('php://input'), true);
	
	foreach($bills as $k => $row){
	    $bill_id = $row['bill_id'];
	    if(isset($row['remaining_bill_items']) && !empty($row['remaining_bill_items'])){
		$bill = $this->reports_model->getBillDetails($bill_id);
		$remaining_ids =$row['remaining_bill_items'] ;
		$bill_items = $this->reports_model->getBillItems_ids($bill_id,$remaining_ids);
		$this->auto_edit_bill($bill,$bill_items);
	    }else{
		$this->auto_delete_bill($bill_id);
	    }
	}
    }
    
    function auto_delete_bill($bill_id){
	$bill_ids[] = $bill_id;
	$this->reports_model->deleteDontPrintBill($bill_ids);
    }
    
    function auto_edit_bill($bill_details,$bill_items){
	//echo 'edit';
	//echo '<pre>';print_R($bill_items);exit;
	$bill_id = $bill_details->id;
	$bill_total = 0;
	$bill_grand_total = 0;
	$bill_tax = 0;
	$bill_total_discount = 0;
	$manual_item_discount = 0;
	$total_items = 0;
	$service_charge = 0;
	$bill_item_ids = array();
	$order_item_ids =  array();
	//echo '<pre>';print_R($bill_items);
	foreach($bill_items as $k => $item){
	    $item_dis = $item->manual_item_discount+$item->item_discount+$item->off_discount+$item->input_discount;
	    $bill_total += $item->net_unit_price - $item->manual_item_discount;
	    if($bill_details->tax_type==1){
		$item_grand_total = $item->net_unit_price+$item->tax+$item->service_charge_amount-$item_dis;
	    }else{
		$item_grand_total = $item->net_unit_price+$item->service_charge_amount-$item_dis;
	    }
	    $bill_grand_total += $item_grand_total;
	    $bill_tax += $item->tax;
	    
	    $bill_total_discount += $item_dis;
	    $manual_item_discount += $item->manual_item_discount;
	    $total_items +=1;
	    $service_charge +=$item->service_charge_amount;
	    array_push($bill_item_ids,$item->id);
	    array_push($order_item_ids,$item->sale_item_id);
	}
	$bill_data['total'] = $bill_total;
	$bill_data['total_discount'] = $bill_total_discount;
	$bill_data['manual_item_discount'] = $manual_item_discount;
	$bill_data['service_charge_amount'] = $service_charge;
	$bill_data['total_tax'] = $bill_tax;
	$bill_data['grand_total'] = $bill_grand_total;
	$bill_data['round_total'] = $bill_grand_total;
	$bill_data['total_pay'] = $bill_grand_total;
	
	$bill_data['total_items'] = $total_items;
	$bill_data['paid'] = $bill_grand_total;
	$bill_data['balance'] = 0;
	//$bill_data['unique_discount'] = 0;
	//$bill_data['bbq_cover_discount'] = 0;
	
	$sale_id = $bill_details->sales_id;
	$sale_data['paid'] =$bill_grand_total;
	$sale_data['grand_total'] = $bill_grand_total;
	
	$p = 0;
	$pay = $this->reports_model->getFirstPayment($bill_id);
	$payment[$p]['date'] = $pay->date ;
	$payment[$p]['paid_on'] = $pay->paid_on ;
	$payment[$p]['amount'] = $bill_grand_total ;
	$payment[$p]['pos_paid'] = $bill_grand_total ;
	$payment[$p]['pos_balance'] = 0 ;
	$payment[$p]['type'] = 'received';
	$payment[$p]['amount_exchange'] = 0;
	$payment[$p]['paid_by'] = 'cash';
	$payment[$p]['bill_id'] =  $bill_id;
	$payment[$p]['sale_id'] =  $sale_id;
	$c_cnt = 0;
	$default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
	    $currency = $this->site->getAllCurrencies();
	foreach($currency as $currency_row){
						
		if($default_currency_data->code == $currency_row->code){
			$multi_currency[$c_cnt] = array(
			
				'sale_id' => $sale_id,
				'bil_id' => $bill_id,
				'currency_id' => $currency_row->id,
				'currency_rate' => $currency_row->rate,
				'amount' => $bill_grand_total,
			);
		}else{
			$multi_currency[$c_cnt] = array(
			
				'sale_id' => $sale_id,
				'bil_id' => $bill_id,
				'currency_id' => $currency_row->id,
				'currency_rate' => $currency_row->rate,
				'amount' => 0,
			);
		}
		$c_cnt++;
	}
	//echo '<pre>';
	//print_r($bill_data);
	//print_r($sale_data);
	//print_r($bill_item_ids);
	//print_r($payment);
	//print_r($multi_currency);
	//print_r($order_item_ids);exit;
	$this->reports_model->auto_modify_bills($bill_id,$sale_id,$bill_data,$sale_data,$bill_item_ids,$payment,$multi_currency,$order_item_ids);
	return true;
    }
 
}
?>