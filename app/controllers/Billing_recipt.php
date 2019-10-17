<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Billing_recipt extends MY_Controller
{

    function __construct() {
        parent::__construct();
	$params = array(
		'host' => PRINTER_HOST,
		'port' => PRINTER_PORT,
		'path' => ''
	);
	
	$this->load->library('ws',$params);
    }
    function send_to_bill_print(){
	ob_end_clean();
        ignore_user_abort();
        ob_start();
        header("Connection: close");
        echo @json_encode($out);
        header("Content-Length: " . ob_get_length());
        @ob_end_flush();
        flush();
        $bill_details = json_decode(file_get_contents('php://input'), true);
	$billID = $bill_details['bill_id'];
	$type = $bill_details['type'];
	if (extension_loaded('imagick')) {
	    $this->print_bill($billID,$type);
	}

    }
    function print_bill($billID,$type){
	
	$billData['type'] = 'billing-recipt';
	$print = $this->site->getFirstPrint();
	$billData['data']['printer'] = $print;
	
	
	//////////////////// generate PDF /////////////////////
	$this->load->library('tec_mpdf');
	$bill_details = $this->site->get_Bill_receipt_data($billID);
	$data['bill_details'] = $bill_details;
	$html = $this->load->view($this->theme . 'pos/app_bill_print', $data,true);
	//echo '<pre>';print_R($bill_details);exit;
	
	$filepath ='assets/uploads/bill_recipt/';
	if (!file_exists($filepath)) {
		    mkdir($filepath, 0777, true);
	}
	$bill_no = $bill_details->bill_number;
	$recipt_path = 'bill_recipt/'.$bill_no.'.pdf';
	$this->tec_mpdf->generate($html,$recipt_path,'S');
	//
	///////////////////// generate pdf - end ////////////////
	$billData['data']['bill_no'] = $bill_no;
	$billData['data']['pdf'] = base_url($filepath.$bill_no.'.pdf');
	$path = explode('/',trim($_SERVER['PHP_SELF'],'/'));
	$instance = $path[0];
	$billData['data']['unlink_path'] = '../'.$instance.'/assets/uploads/bill_recipt/'.$bill_no.'.pdf';
	//echo '<pre>';print_R($billData);exit;
	
	//exit;
	//$this->site->get_Bill_receipt_data(5293);
	if(!empty($this->ws->checkConnection())){
		$result = $this->ws->send(json_encode($billData));						
		$this->ws->close();						
	}
//        $str = 'before-str-after';
//	if (preg_match('/before-(.*?)-after/', $str, $match) == 1) {
//	    echo $match[1];
//	} 
    }
    function wraprecipe_name_qty($r_name,$r_qty,$newline){
	$wrapped = wordwrap($r_name,20,"\n");
	$lines = explode("\n", $wrapped);
	$wrap_cnt = count($lines)-1;
	//if($wrap_cnt>0){
		$lines[$wrap_cnt] = sprintf('%-20.20s %1.0f',$lines[$wrap_cnt], $r_qty); 
	//}
	$items = implode("\n",$lines);
	if($newline) {
	 $items = $items."\n";
	}
	return $items;
    }
    function test($id){
	$bill_details = $this->site->get_Bill_receipt_data($id);
	$data['bill_details'] = $bill_details;
	$this->load->view($this->theme . 'pos/app_bill_print', $data);
	//$this->site->send_to_bill_print(2);
    }
}
