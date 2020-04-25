<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Kot_print extends MY_Controller
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
    function send_to_kot_print(){
	ob_end_clean();
        ignore_user_abort();
        ob_start();
        header("Connection: close");
        echo @json_encode($out);
        header("Content-Length: " . ob_get_length());
        @ob_end_flush();
        flush();
        $kot_print_data = json_decode(file_get_contents('php://input'), true);
	
	
	if($kot_print_data['kot_print_option'] == 1){	
		$this->single_item($kot_print_data['kot_area_print']);
	}else{
		$this->all_items($kot_print_data['kot_area_print']);
	}
	if($kot_print_data['con_kot_print_option'] != 0){	
		$this->kot_consolidated($kot_print_data['kot_con_print']);
	}

	if($kot_print_data['consolidate_kitchens_kot']){	
		$this->consolidate_kitchens_kot($kot_print_data['consolidate_kitchens_kot']);
    }

    }
	
	 function send_to_kot_print_cancelItem(){
	ob_end_clean();
        ignore_user_abort();
        ob_start();
        header("Connection: close");
        echo @json_encode($out);
        header("Content-Length: " . ob_get_length());
        @ob_end_flush();
        flush();
        $kot_print_data = json_decode(file_get_contents('php://input'), true);
	
	
	if($kot_print_data['kot_print_option'] == 1){	
		$this->single_cancelitem($kot_print_data['kot_area_print']);
	}else{
		$this->cancelall_items($kot_print_data['kot_area_print']);
	}
	if($kot_print_data['con_kot_print_option'] != 0){	
		$this->cancel_kot_consolidated($kot_print_data['kot_con_print']);
	}

	if($kot_print_data['consolidate_kitchens_kot']){	
		$this->cancel_consolidate_kitchens_kot($kot_print_data['consolidate_kitchens_kot']);
    }

    }
    function single_item($kitchen_data){
//        ob_end_clean();
//        ignore_user_abort();
//        ob_start();
//        header("Connection: close");
//        echo @json_encode($out);
//        header("Content-Length: " . ob_get_length());
//        @ob_end_flush();
//        flush();
//        $kitchen_data = json_decode(file_get_contents('php://input'), true);
//	$kitchen_data = $kitchen_data['k_data'];
	
	$kitchen_data['orders_details'] = (object) $kitchen_data['orders_details'];
	if(!empty($kitchen_data)){
			$ordered_by = 'N/A';
			$user = $this->site->getUser($kitchen_data['orders_details']->created_by);
			
			if($user){ 
			$ordered_by = $user->first_name.' '.$user->last_name; 
			}
			$biller = $this->site->getCompanyOrderByID($kitchen_data['orders_details']->biller_id);
			$print_header = "";
			if($kitchen_data['orders_details']->order_type == 1){
				$store_name = "Table : #".$kitchen_data['orders_details']->table_name;
			} elseif($kitchen_data['orders_details']->order_type == 2){
				$store_name = "Takeaway : #".$kitchen_data['orders_details']->reference_no;
			} else{
				$store_name = "Delivery : #".$kitchen_data['orders_details']->reference_no;
			}
			//$print_header .= $biller->company;
			//$print_header .= ', ';
			//$print_header .= $biller->address;
						
			if($this->Settings->time_format == 12){
			$date = new DateTime($kitchen_data['orders_details']->created_on);
			$created_on = $date->format('Y-m-d h:iA');
			}else{
				$created_on =  $kitchen_data['orders_details']->created_on;
			}

			//$print_header .= "\n";
			$print_header .= "KOT ORDER";
			if(isset($kitchen_data['orders_details']->is_print_copy)){
			    $print_header .= " - COPY";
			}
			$print_header .= "\n";
			$print_info_common = "";
			if($this->pos_settings->kot_order_no_print_option){
			$print_info_common .= 'Order Number';
			$print_info_common .= ' : ';
			$print_info_common .= $kitchen_data['orders_details']->reference_no;
			$print_info_common .= "\n";
		    }
			$print_info_common .= 'Date';
			$print_info_common .= ' : ';
			// $print_info_common .= $kitchen_data['orders_details']->created_on;
			$print_info_common .= $created_on;
			$print_info_common .= "\n";
			$print_info_common .= 'Order Person';
			$print_info_common .= ' : ';
			$print_info_common .= $ordered_by;
			$print_info_common .= "\n";
			
			
			
			if(!empty($kitchen_data['kitchens'])){
				foreach($kitchen_data['kitchens'] as $order_data){
				    $order_data = (object) $order_data;
					$print_info = ''; 
					$print_info .= $print_info_common;
					$print_info .= 'Kitchen Type';
					$print_info .= ' : ';
					$print_info .= $order_data->name;
					$print_info .= "\n-----------------------------------------------\n";
					
					if(!empty($order_data->kit_o) && !empty($order_data->printers_details)){
						$i =1;
						
						$orderItemCnt = count($order_data->kit_o);						
						foreach($order_data->kit_o as $item_data){
						    
							$print_items = "";
							$list = array();
							$print_items .= '';
							$print_items .= $i;
							$print_items .= ' ';
							if(!empty($item_data['khmer_recipe_image'])){
								$print_items .= $item_data['khmer_recipe_image'];
							}else{
								$print_items .= $item_data['recipe_name'];
							}
							$print_items .= "";
							$print_items .= '   X ';
							$print_items .= $item_data['quantity'];
							$print_items .= "\n";
							$newline = false;
							if($orderItemCnt!=$i){
							    //$newline ="\n";
							}	
							

							$en_recipe_name=$item_data['en_recipe_name'];							
							if( $item_data['en_variant_name']  !=''){
								$en_recipe_name=$item_data['en_recipe_name'].'-'.$item_data['en_variant_name'];	
							}
							if($this->pos_settings->kot_print_lang_option != 1 ){
								$item_name = $this->wraprecipe_name_qty($en_recipe_name,$item_data['quantity'],$newline);
							}else{
								$item_name = $en_recipe_name;
							}

							
							$list[] = array(
								'sno' => $i,
								'en_recipe_name' => $item_name,
								'code'=>$item_data['code'],
								'recipe_name' => $item_data['en_recipe_name'],
								'quantity' => $item_data['quantity'],
								'comment' => $item_data['comment'],
								'khmer_image' => $item_data['khmer_recipe_image'],
								'addons' => $item_data['addons'] ? $item_data['addons'] :'',																
							);
							$i++;
						$print_mirroring = explode(',',$order_data->printers_details['print_mirroring']);
						$Printers = array($order_data->printers_details);
						
						if(!empty($print_mirroring)){
						    $otherPrinters = $this->site->getPrinters($print_mirroring);						    
						    if($otherPrinters){						    
							$Printers = array_merge(array($order_data->printers_details),$otherPrinters);
						    }
						}
						
							//Remote printing KOT
						$kot_print_lang_option = $this->pos_settings->kot_print_lang_option;
						$kot_font_size = $this->pos_settings->kot_font_size;

						foreach($Printers as $k => $print){
						    $receipt = array(
							'store_name' => $store_name,
							'header' => $print_header,
							'info' => $print_info,
							'items' => $print_items,
							'itemlists' => $list,
							'kot_print_lang_option' => $kot_print_lang_option,//0->english,1->local,2->both
							'kot_font_size' => $kot_font_size,//0->small,1->medium,2->large
						    );
						    $data = array(
						    'type'=>'print-receipt',
						    'data'=>array(
							    'printer' => $print,
							    // 'logo'=> base_url().'assets/uploads/logos/'.$biller->logo,
							    'text' => $receipt,
							    'cash_drawer' => ''
						    )
						    );
						    if($this->pos_settings->kot_print_logo){
							$data['data']['logo'] = base_url().'assets/uploads/logos/'.$biller->logo;
						    }
						    /*echo "<pre>";
						    print_r($data);*/
						    
						    if(!empty($this->ws->checkConnection())){
							    $result = $this->ws->send(json_encode($data));						
							    $this->ws->close();						
						    }
						}
						

						}
						
					}
				}
			}//die;
		}
              
    }
	function single_cancelitem($kitchen_data){
//        ob_end_clean();
//        ignore_user_abort();
//        ob_start();
//        header("Connection: close");
//        echo @json_encode($out);
//        header("Content-Length: " . ob_get_length());
//        @ob_end_flush();
//        flush();
//        $kitchen_data = json_decode(file_get_contents('php://input'), true);
//	$kitchen_data = $kitchen_data['k_data'];
	
	$kitchen_data['orders_details'] = (object) $kitchen_data['orders_details'];
	if(!empty($kitchen_data)){
			$ordered_by = 'N/A';
			$user = $this->site->getUser($kitchen_data['orders_details']->created_by);
			
			if($user){ 
			$ordered_by = $user->first_name.' '.$user->last_name; 
			}
			$biller = $this->site->getCompanyOrderByID($kitchen_data['orders_details']->biller_id);
			$print_header = "";
			if($kitchen_data['orders_details']->order_type == 1){
				$store_name = "Table : #".$kitchen_data['orders_details']->table_name;
			} elseif($kitchen_data['orders_details']->order_type == 2){
				$store_name = "Takeaway : #".$kitchen_data['orders_details']->reference_no;
			} else{
				$store_name = "Delivery : #".$kitchen_data['orders_details']->reference_no;
			}
			//$print_header .= $biller->company;
			//$print_header .= ', ';
			//$print_header .= $biller->address;
				
					$cancelHeader="CANCEL KOT ORDER"				;
			if($this->Settings->time_format == 12){
			$date = new DateTime($kitchen_data['orders_details']->created_on);
			$created_on = $date->format('Y-m-d h:iA');
			}else{
				$created_on =  $kitchen_data['orders_details']->created_on;
			}

			//$print_header .= "\n";
			//$print_header .= "CANCEL KOT ORDER";
			if(isset($kitchen_data['orders_details']->is_print_copy)){
			    $print_header .= " - COPY";
			}
			$print_header .= "\n";
			$print_info_common = "";
			if($this->pos_settings->kot_order_no_print_option){
			$print_info_common .= 'Order Number';
			$print_info_common .= ' : ';
			$print_info_common .= $kitchen_data['orders_details']->reference_no;
			$print_info_common .= "\n";
		    }
			$print_info_common .= 'Date';
			$print_info_common .= ' : ';
			// $print_info_common .= $kitchen_data['orders_details']->created_on;
			$print_info_common .= $created_on;
			$print_info_common .= "\n";
			$print_info_common .= 'Order Person';
			$print_info_common .= ' : ';
			$print_info_common .= $ordered_by;
			$print_info_common .= "\n";
			
			
			
			if(!empty($kitchen_data['kitchens'])){
				foreach($kitchen_data['kitchens'] as $order_data){
				    $order_data = (object) $order_data;
					$print_info = ''; 
					$print_info .= $print_info_common;
					$print_info .= 'Kitchen Type';
					$print_info .= ' : ';
					$print_info .= $order_data->name;
					$print_info .= "\n-----------------------------------------------\n";
					
					if(!empty($order_data->kit_o) && !empty($order_data->printers_details)){
						$i =1;
						
						$orderItemCnt = count($order_data->kit_o);						
						foreach($order_data->kit_o as $item_data){
						    
							$print_items = "";
							$list = array();
							$print_items .= '';
							$print_items .= $i;
							$print_items .= ' ';
							if(!empty($item_data['khmer_recipe_image'])){
								$print_items .= $item_data['khmer_recipe_image'];
							}else{
								$print_items .= $item_data['recipe_name'];
							}
							$print_items .= "";
							$print_items .= '   X ';
							$print_items .= $item_data['quantity'];
							$print_items .= "\n";
							$newline = false;
							if($orderItemCnt!=$i){
							    //$newline ="\n";
							}	
							

							$en_recipe_name=$item_data['en_recipe_name'];							
							if( $item_data['en_variant_name']  !=''){
								$en_recipe_name=$item_data['en_recipe_name'].'-'.$item_data['en_variant_name'];	
							}
							if($this->pos_settings->kot_print_lang_option != 1 ){
								$item_name = $this->wraprecipe_name_qty($en_recipe_name,$item_data['quantity'],$newline);
							}else{
								$item_name = $en_recipe_name;
							}

							
							$list[] = array(
								'sno' => $i,
								'en_recipe_name' => $item_name,
								'code'=>$item_data['code'],
								'recipe_name' => $item_data['en_recipe_name'],
								'quantity' => $item_data['quantity'],
								'comment' => $item_data['comment'],
								'khmer_image' => $item_data['khmer_recipe_image'],
								'addons' => $item_data['addons'] ? $item_data['addons'] :'',																
							);
							$i++;
						$print_mirroring = explode(',',$order_data->printers_details['print_mirroring']);
						$Printers = array($order_data->printers_details);
						
						if(!empty($print_mirroring)){
						    $otherPrinters = $this->site->getPrinters($print_mirroring);						    
						    if($otherPrinters){						    
							$Printers = array_merge(array($order_data->printers_details),$otherPrinters);
						    }
						}
						
							//Remote printing KOT
						$kot_print_lang_option = $this->pos_settings->kot_print_lang_option;
						$kot_font_size = $this->pos_settings->kot_font_size;

						foreach($Printers as $k => $print){
						    $receipt = array(
							'store_name' => $store_name,
							'header' => $print_header,
							'info' => $print_info,
							'items' => $print_items,
							'itemlists' => $list,
							'cancelHeader'=>$cancelHeader,
							'kot_print_lang_option' => $kot_print_lang_option,//0->english,1->local,2->both
							'kot_font_size' => $kot_font_size,//0->small,1->medium,2->large
						    );
						    $data = array(
						    'type'=>'print-receipt',
						    'data'=>array(
							    'printer' => $print,
							    // 'logo'=> base_url().'assets/uploads/logos/'.$biller->logo,
							    'text' => $receipt,
							    'cash_drawer' => ''
						    )
						    );
						    if($this->pos_settings->kot_print_logo){
							$data['data']['logo'] = base_url().'assets/uploads/logos/'.$biller->logo;
						    }
						    /*echo "<pre>";
						    print_r($data);*/
						    
						    if(!empty($this->ws->checkConnection())){
							    $result = $this->ws->send(json_encode($data));						
							    $this->ws->close();						
						    }
						}
						

						}
						
					}
				}
			}//die;
		}
              
    }
    function all_items($kitchen_data){
//	ob_end_clean();
//        ignore_user_abort();
//        ob_start();
//        header("Connection: close");
//        echo @json_encode($out);
//        header("Content-Length: " . ob_get_length());
//        @ob_end_flush();
//        flush();
//	
//	
//	$kitchen_data = json_decode(file_get_contents('php://input'), true);
//	$kitchen_data = $kitchen_data['k_data'];
	$kitchen_data['orders_details'] = (object) $kitchen_data['orders_details'];
	
	//echo '<pre>';print_R($kitchen_data);
	if(!empty($kitchen_data)){
			$ordered_by = 'N/A';
			$user = $this->site->getUser($kitchen_data['orders_details']->created_by); 
			if($user){ 
			$ordered_by = $user->first_name.' '.$user->last_name; 
			}
			$biller = $this->site->getCompanyOrderByID($kitchen_data['orders_details']->biller_id);
			$print_header = "";
			if($kitchen_data['orders_details']->order_type == 1 || $kitchen_data['orders_details']->order_type == 4 ){
				$store_name = "Table : #".$kitchen_data['orders_details']->table_name;
			} elseif($kitchen_data['orders_details']->order_type == 2){
				$store_name = "Takeaway : #".$kitchen_data['orders_details']->reference_no;
			} else{
				$store_name = "Delivery : #".$kitchen_data['orders_details']->reference_no;
			}
			//$print_header .= $biller->company;
			//$print_header .= ', ';
			//$print_header .= $biller->address;
			if($this->Settings->time_format == 12){
			$date = new DateTime($kitchen_data['orders_details']->created_on);
			$created_on = $date->format('Y-m-d h:iA');
			}else{
				$created_on =  $kitchen_data['orders_details']->created_on;
			}

			$print_header .= "\n";
			$print_header .= "KOT ORDER";
			if(isset($kitchen_data['orders_details']->is_print_copy)){
			    $print_header .= " - COPY";
			}
			$print_header .= "\n";
			$print_info_common = "";
			if($this->pos_settings->kot_order_no_print_option){
			$print_info_common .= 'Order Number';
			$print_info_common .= ' : ';
			$print_info_common .= $kitchen_data['orders_details']->reference_no;
			$print_info_common .= "\n";
		    }
			$print_info_common .= 'Date';
			$print_info_common .= ' : ';
			$print_info_common .= $created_on;
			$print_info_common .= "\n";
			$print_info_common .= 'Order Person';
			$print_info_common .= ' : ';
			$print_info_common .= $ordered_by;
			$print_info_common .= "\n";
			
			
			
			if(!empty($kitchen_data['kitchens'])){
				foreach($kitchen_data['kitchens'] as $order_data){
				    $order_data = (object) $order_data;
					$print_info = ''; 
					$print_info .= $print_info_common;
					$print_info .= 'Kitchen Type';
					$print_info .= ' : ';
					$print_info .= $order_data->name;
					$print_info .= "\n-----------------------------------------------\n";
					$print_items = "";
					
					if(!empty($order_data->kit_o) && !empty($order_data->printers_details)){
						$i =1;
						$list = array();
						$orderItemCnt = count($order_data->kit_o);
						foreach($order_data->kit_o as $item_data){
							//$item_data  = (array) $item_data;
							$print_items .= '';
							$print_items .= $i;
							$print_items .= ' ';
							if(!empty($item_data['khmer_recipe_image'])){
								$print_items .= $item_data['khmer_recipe_image'];
							}else{
								$print_items .= $item_data['recipe_name'];
							}
							$print_items .= "";
							$print_items .= '   X ';
							$print_items .= $item_data['quantity'];
							$print_items .= "\n";
							$newline = false;
							if($orderItemCnt!=$i){
							    //$newline ="\n";
							}

							$en_recipe_name=$item_data['en_recipe_name'];							
							if($item_data['en_variant_name']  !=''){
								$en_recipe_name=$item_data['en_recipe_name'].'-'.$item_data['en_variant_name'];	
							}
							if($this->pos_settings->kot_print_lang_option != 1 ){
								$item_name = $this->wraprecipe_name_qty($en_recipe_name,$item_data['quantity'],$newline);
							}else{
								$item_name = $en_recipe_name;
							}							
							// var_dump($this->pos_settings->kot_print_lang_option);							
							$list[] = array(
								'sno' => $i,	
								'code'=>$item_data['code'],
								'en_recipe_name' => $item_name,
								'recipe_name' => $item_data['en_recipe_name'],
								'quantity' => $item_data['quantity'],
								'khmer_image' => $item_data['khmer_recipe_image'],
								'comment' => $item_data['comment'],
								'khmer_name' => $item_data['khmer_name'],
								'addons' => $item_data['addons'] ? $item_data['addons'] :'',
								'unwanted_ingredients' => $item_data['unwanted_ingredients'] ? $item_data['unwanted_ingredients'] :'',	
							);
							$i++;
						}
						$print_mirroring = explode(',',$order_data->printers_details['print_mirroring']);
						$Printers = array($order_data->printers_details);
						
						if(!empty($print_mirroring)){
						    $otherPrinters = $this->site->getPrinters($print_mirroring);						    
						    if($otherPrinters){						    
							$Printers = array_merge(array($order_data->printers_details),$otherPrinters);
						    }
						}
						
						$kot_print_lang_option = $this->pos_settings->kot_print_lang_option;
						$kot_font_size = $this->pos_settings->kot_font_size;

							//Remote printing KOT
						foreach($Printers as $k => $print){
						    //Remote printing KOT
						    $receipt = array(
							    'store_name' => $store_name,
							    'header' => $print_header,
							    'info' => $print_info,
							    'items' => $print_items,
							    'itemlists' => $list,
							    'kot_print_lang_option' => $kot_print_lang_option,//0->english,1->local,2->both
								'kot_font_size' => $kot_font_size,//0->small,1->medium,2->large
						    );
						    $data = array(
						    'type'=>'print-receipt',
						    'data'=>array(
							    'printer' => $print,
							    //'logo'=> base_url().'assets/uploads/logos/'.$biller->logo,
							    'text' => $receipt,
							    'cash_drawer' => ''
						    )
						    );
						    if($this->pos_settings->kot_print_logo){
							$data['data']['logo'] = base_url().'assets/uploads/logos/'.$biller->logo;
						    }
						    if(!empty($this->ws->checkConnection())){//echo '<pre>';print_R($data);
						    $result = $this->ws->send(json_encode($data));
						    $this->ws->close();
						    }
						}
					}
				}
			}//die;
		}
    }
    
	function cancelall_items($kitchen_data){
//	ob_end_clean();
//        ignore_user_abort();
//        ob_start();
//        header("Connection: close");
//        echo @json_encode($out);
//        header("Content-Length: " . ob_get_length());
//        @ob_end_flush();
//        flush();
//	
//	
//	$kitchen_data = json_decode(file_get_contents('php://input'), true);
//	$kitchen_data = $kitchen_data['k_data'];
	$kitchen_data['orders_details'] = (object) $kitchen_data['orders_details'];
	
	//echo '<pre>';print_R($kitchen_data);
	if(!empty($kitchen_data)){
			$ordered_by = 'N/A';
			$user = $this->site->getUser($kitchen_data['orders_details']->created_by); 
			if($user){ 
			$ordered_by = $user->first_name.' '.$user->last_name; 
			}
			$biller = $this->site->getCompanyOrderByID($kitchen_data['orders_details']->biller_id);
			$print_header = "";
			if($kitchen_data['orders_details']->order_type == 1 || $kitchen_data['orders_details']->order_type == 4 ){
				$store_name = "Table : #".$kitchen_data['orders_details']->table_name;
			} elseif($kitchen_data['orders_details']->order_type == 2){
				$store_name = "Takeaway : #".$kitchen_data['orders_details']->reference_no;
			} else{
				$store_name = "Delivery : #".$kitchen_data['orders_details']->reference_no;
			}
			//$print_header .= $biller->company;
			//$print_header .= ', ';
			//$print_header .= $biller->address;
			if($this->Settings->time_format == 12){
			$date = new DateTime($kitchen_data['orders_details']->created_on);
			$created_on = $date->format('Y-m-d h:iA');
			}else{
				$created_on =  $kitchen_data['orders_details']->created_on;
			}
			$cancelHeader="CANCEL  KOT ORDER";
			$print_header .= "\n";
		//	$print_header .= "CANCEL KOT ORDER";
			if(isset($kitchen_data['orders_details']->is_print_copy)){
			    $print_header .= " - COPY";
			}
			$print_header .= "\n";
			$print_info_common = "";
			if($this->pos_settings->kot_order_no_print_option){
			$print_info_common .= 'Order Number';
			$print_info_common .= ' : ';
			$print_info_common .= $kitchen_data['orders_details']->reference_no;
			$print_info_common .= "\n";
		    }
			$print_info_common .= 'Date';
			$print_info_common .= ' : ';
			$print_info_common .= $created_on;
			$print_info_common .= "\n";
			$print_info_common .= 'Order Person';
			$print_info_common .= ' : ';
			$print_info_common .= $ordered_by;
			$print_info_common .= "\n";
			
			
			
			if(!empty($kitchen_data['kitchens'])){
				foreach($kitchen_data['kitchens'] as $order_data){
				    $order_data = (object) $order_data;
					$print_info = ''; 
					$print_info .= $print_info_common;
					$print_info .= 'Kitchen Type';
					$print_info .= ' : ';
					$print_info .= $order_data->name;
					$print_info .= "\n-----------------------------------------------\n";
					$print_items = "";
					
					if(!empty($order_data->kit_o) && !empty($order_data->printers_details)){
						$i =1;
						$list = array();
						$orderItemCnt = count($order_data->kit_o);
						foreach($order_data->kit_o as $item_data){
							//$item_data  = (array) $item_data;
							$print_items .= '';
							$print_items .= $i;
							$print_items .= ' ';
							if(!empty($item_data['khmer_recipe_image'])){
								$print_items .= $item_data['khmer_recipe_image'];
							}else{
								$print_items .= $item_data['recipe_name'];
							}
							$print_items .= "";
							$print_items .= '   X ';
							$print_items .= $item_data['quantity'];
							$print_items .= "\n";
							$newline = false;
							if($orderItemCnt!=$i){
							    //$newline ="\n";
							}

							$en_recipe_name=$item_data['en_recipe_name'];							
							if($item_data['en_variant_name']  !=''){
								$en_recipe_name=$item_data['en_recipe_name'].'-'.$item_data['en_variant_name'];	
							}
							if($this->pos_settings->kot_print_lang_option != 1 ){
								$item_name = $this->wraprecipe_name_qty($en_recipe_name,$item_data['quantity'],$newline);
							}else{
								$item_name = $en_recipe_name;
							}							
							// var_dump($this->pos_settings->kot_print_lang_option);							
							$list[] = array(
								'sno' => $i,	
								'code'=>$item_data['code'],
								'en_recipe_name' => $item_name,
								'recipe_name' => $item_data['en_recipe_name'],
								'quantity' => $item_data['quantity'],
								'khmer_image' => $item_data['khmer_recipe_image'],
								'comment' => $item_data['comment'],
								'khmer_name' => $item_data['khmer_name'],
								'addons' => $item_data['addons'] ? $item_data['addons'] :'',
								'unwanted_ingredients' => $item_data['unwanted_ingredients'] ? $item_data['unwanted_ingredients'] :'',	
							);
							$i++;
						}
						$print_mirroring = explode(',',$order_data->printers_details['print_mirroring']);
						$Printers = array($order_data->printers_details);
						
						if(!empty($print_mirroring)){
						    $otherPrinters = $this->site->getPrinters($print_mirroring);						    
						    if($otherPrinters){						    
							$Printers = array_merge(array($order_data->printers_details),$otherPrinters);
						    }
						}
						
						$kot_print_lang_option = $this->pos_settings->kot_print_lang_option;
						$kot_font_size = $this->pos_settings->kot_font_size;

							//Remote printing KOT
						foreach($Printers as $k => $print){
						    //Remote printing KOT
						    $receipt = array(
							    'store_name' => $store_name,
							    'header' => $print_header,
							    'info' => $print_info,
							    'items' => $print_items,
							    'itemlists' => $list,
								'cancelHeader'=>$cancelHeader,
							    'kot_print_lang_option' => $kot_print_lang_option,//0->english,1->local,2->both
								'kot_font_size' => $kot_font_size,//0->small,1->medium,2->large
						    );
						    $data = array(
						    'type'=>'print-receipt',
						    'data'=>array(
							    'printer' => $print,
							    //'logo'=> base_url().'assets/uploads/logos/'.$biller->logo,
							    'text' => $receipt,
							    'cash_drawer' => ''
						    )
						    );
						    if($this->pos_settings->kot_print_logo){
							$data['data']['logo'] = base_url().'assets/uploads/logos/'.$biller->logo;
						    }
						    if(!empty($this->ws->checkConnection())){//echo '<pre>';print_R($data);
						    $result = $this->ws->send(json_encode($data));
						    $this->ws->close();
						    }
						}
					}
				}
			}//die;
		}
    }
    
    function kot_consolidated($kotconsoildprint=false){
	
//	ob_end_clean();
//        ignore_user_abort();
//        ob_start();
//        header("Connection: close");
//        echo @json_encode($out);
//        header("Content-Length: " . ob_get_length());
//        @ob_end_flush();
//        flush();
//	$kotconsoildprint = json_decode(file_get_contents('php://input'), true);
	
	if(!empty($kotconsoildprint['consolid_kot_print_details'])){
		

		foreach($kotconsoildprint['consolid_kot_print_details'] as $order_data){	
			

			if(!empty($kotconsoildprint['consolid_kot_print_details']) && !empty($kotconsoildprint['consolid_kitchens'])){

				$this->kot_consolidated_print($kotconsoildprint);
			}
		}
	}
  }
  function cancel_kot_consolidated($kotconsoildprint=false){
	
//	ob_end_clean();
//        ignore_user_abort();
//        ob_start();
//        header("Connection: close");
//        echo @json_encode($out);
//        header("Content-Length: " . ob_get_length());
//        @ob_end_flush();
//        flush();
//	$kotconsoildprint = json_decode(file_get_contents('php://input'), true);
	
	if(!empty($kotconsoildprint['consolid_kot_print_details'])){
		

		foreach($kotconsoildprint['consolid_kot_print_details'] as $order_data){	
			

			if(!empty($kotconsoildprint['consolid_kot_print_details']) && !empty($kotconsoildprint['consolid_kitchens'])){

				$this->cancel_kot_consolidated_print($kotconsoildprint);
			}
		}
	}
  }
    function kot_consolidated_print($kitchen_data=array()){
                
		/*echo "<pre>";
		print_r($kitchen_data);die;*/
		
		$kitchen_data['orders_details'] = (object) $kitchen_data['orders_details'];
		if(!empty($kitchen_data)){
			$ordered_by = 'N/A';
			$user = $this->site->getUser($kitchen_data['orders_details']->created_by);
			
			if($user){ 
			$ordered_by = $user->first_name.' '.$user->last_name; 
			}
			$biller = $this->site->getCompanyOrderByID($kitchen_data['orders_details']->biller_id);
			$print_header = "";
			if($kitchen_data['orders_details']->order_type == 1 || $kitchen_data['orders_details']->order_type == 4){
				$store_name = "Table : #".$kitchen_data['orders_details']->table_name;
			} elseif($kitchen_data['orders_details']->order_type == 2){
				$store_name = "Takeaway : #".$kitchen_data['orders_details']->reference_no;
			} else{
				$store_name = "Delivery : #".$kitchen_data['orders_details']->reference_no;
			}
			//$print_header .= $biller->company;
			//$print_header .= ', ';
			//$print_header .= $biller->address;
			if($this->Settings->time_format == 12){
			$date = new DateTime($kitchen_data['orders_details']->created_on);
			$created_on = $date->format('Y-m-d h:iA');
			}else{
				$created_on =  $kitchen_data['orders_details']->created_on;
			}

			$print_header .= "\n";
			$print_header .= "CONSOLID KOT ORDER";
			
			if(isset($kitchen_data['orders_details']->is_print_copy)){
			    $print_header .= " - COPY";
			}
			$print_header .= "\n";
			$print_info_common = "";
			if($this->pos_settings->kot_order_no_print_option){
			$print_info_common .= 'Order Number';
			$print_info_common .= ' : ';
			$print_info_common .= $kitchen_data['orders_details']->reference_no;
			$print_info_common .= "\n";
			}
			$print_info_common .= 'Date';
			$print_info_common .= ' : ';
			$print_info_common .= $created_on;
			$print_info_common .= "\n";
			$print_info_common .= 'Order Person';
			$print_info_common .= ' : ';
			$print_info_common .= $ordered_by;
			$print_info_common .= "\n";			
			
			if(!empty($kitchen_data['consolid_kot_print_details'])){

				foreach($kitchen_data['consolid_kot_print_details'] as $order_data){

					/*echo "<pre>";	
					print_r($order_data);		die;	*/	
					$print_info = ''; 
					$print_info .= $print_info_common;
					// $print_info .= 'Kitchen Type';
					// $print_info .= ' : ';
					// $print_info .= $order_data->name;
					$print_info .= "\n-----------------------------------------------\n";
					$print_items = "";
					if(!empty($kitchen_data['consolid_kot_print_details']) && !empty($kitchen_data['consolid_kitchens'])){
						$i =1;
						$list = array();
						/*echo "<pre>";
						print_r($order_data->kit_o);die;*/
						$orderItemCnt = count($kitchen_data['consolid_kitchens']);
						foreach($kitchen_data['consolid_kitchens'] as $item_data){
							
							
							$print_items .= '';
							$print_items .= $i;
							$print_items .= ' ';
							if(!empty($item_data['khmer_recipe_image'])){
								$print_items .= $item_data['khmer_recipe_image'];
							}else{
								$print_items .= $item_data['recipe_name'];
							}
							$print_items .= "";
							$print_items .= '   X ';
							$print_items .= $item_data['quantity'];
							$print_items .= "\n";
							$newline = false;
							if($orderItemCnt!=$i){
							    $newline ="\n";
							}

							$en_recipe_name=$item_data['en_recipe_name'];							
							if( $item_data['en_variant_name']  !=''){
								$en_recipe_name=$item_data['en_recipe_name'].'-'.$item_data['en_variant_name'];	
							}
							if($this->pos_settings->kot_print_lang_option != 1 ){
								$item_name = $this->wraprecipe_name_qty($en_recipe_name,$item_data['quantity'],$newline);
							}else{
								$item_name = $en_recipe_name;
							}

							$list[] = array(
								'sno' => $i,
								'code'=>$item_data['code'],
								'en_recipe_name' => $item_name,
								'khmer_image' => $item_data['khmer_recipe_image'],
								'comment' => $item_data['comment'],
								'quantity' => $item_data['quantity'],	
								'addons' => $item_data['addons'] ? $item_data['addons'] :'',							
							);
							$i++;
						}
						$print_mirroring = explode(',',$order_data['print_mirroring']);
						$Printers = array($order_data);
						
						if(!empty($print_mirroring)){
						    $otherPrinters = $this->site->getPrinters($print_mirroring);						    
						    if($otherPrinters){						    
							$Printers = array_merge(array($order_data),$otherPrinters);
						    }
						}
						
						$kot_print_lang_option = $this->pos_settings->kot_print_lang_option;
						$kot_font_size = $this->pos_settings->kot_font_size;

							//Remote printing KOT
						foreach($Printers as $k => $print){
						    //Remote printing KOT
						    $receipt = array(
							    'store_name' => $store_name,
							    'header' => $print_header,
							    'info' => $print_info,
							    'items' => $print_items,
							    'itemlists' => $list,
							    'kot_print_lang_option' => $kot_print_lang_option,//0->english,1->local,2->both
								'kot_font_size' => $kot_font_size,//0->small,1->medium,2->large
						    );
						    $data = array(
						    'type'=>'print-receipt',
						    'data'=>array(
							    'printer' => $print,
							    //'logo'=> base_url().'assets/uploads/logos/'.$biller->logo,
							    'text' => $receipt,
							    'cash_drawer' => ''
						    )
						    );
						    if($this->pos_settings->consolid_kot_print_logo){
							$data['data']['logo'] = base_url().'assets/uploads/logos/'.$biller->logo;
						    }
						    if(!empty($this->ws->checkConnection())){//echo '<pre>';print_R($data);
						    $result = $this->ws->send(json_encode($data));
						    $this->ws->close();
						    }
						}
						/*echo "<pre>";
						print_r($data);die;*/

					}
				}
				/*echo "<pre>";
						print_r($data);die;*/
			}//die;
		}
    }
	 function cancel_kot_consolidated_print($kitchen_data=array()){
                
		/*echo "<pre>";
		print_r($kitchen_data);die;*/
		
		$kitchen_data['orders_details'] = (object) $kitchen_data['orders_details'];
		if(!empty($kitchen_data)){
			$ordered_by = 'N/A';
			$user = $this->site->getUser($kitchen_data['orders_details']->created_by);
			
			if($user){ 
			$ordered_by = $user->first_name.' '.$user->last_name; 
			}
			$biller = $this->site->getCompanyOrderByID($kitchen_data['orders_details']->biller_id);
			$print_header = "";
			if($kitchen_data['orders_details']->order_type == 1 || $kitchen_data['orders_details']->order_type == 4){
				$store_name = "Table : #".$kitchen_data['orders_details']->table_name;
			} elseif($kitchen_data['orders_details']->order_type == 2){
				$store_name = "Takeaway : #".$kitchen_data['orders_details']->reference_no;
			} else{
				$store_name = "Delivery : #".$kitchen_data['orders_details']->reference_no;
			}
			$cancelHeader="CANCEL CONSOLID KOT ORDER";
			//$print_header .= $biller->company;
			//$print_header .= ', ';
			//$print_header .= $biller->address;
			if($this->Settings->time_format == 12){
			$date = new DateTime($kitchen_data['orders_details']->created_on);
			$created_on = $date->format('Y-m-d h:iA');
			}else{
				$created_on =  $kitchen_data['orders_details']->created_on;
			}

			$print_header .= "\n";
			//$print_header .= "CANCEL CONSOLID KOT ORDER";
			
			if(isset($kitchen_data['orders_details']->is_print_copy)){
			    $print_header .= " - COPY";
			}
			$print_header .= "\n";
			$print_info_common = "";
			if($this->pos_settings->kot_order_no_print_option){
			$print_info_common .= 'Order Number';
			$print_info_common .= ' : ';
			$print_info_common .= $kitchen_data['orders_details']->reference_no;
			$print_info_common .= "\n";
			}
			$print_info_common .= 'Date';
			$print_info_common .= ' : ';
			$print_info_common .= $created_on;
			$print_info_common .= "\n";
			$print_info_common .= 'Order Person';
			$print_info_common .= ' : ';
			$print_info_common .= $ordered_by;
			$print_info_common .= "\n";			
			
			if(!empty($kitchen_data['consolid_kot_print_details'])){

				foreach($kitchen_data['consolid_kot_print_details'] as $order_data){

					/*echo "<pre>";	
					print_r($order_data);		die;	*/	
					$print_info = ''; 
					$print_info .= $print_info_common;
					// $print_info .= 'Kitchen Type';
					// $print_info .= ' : ';
					// $print_info .= $order_data->name;
					$print_info .= "\n-----------------------------------------------\n";
					$print_items = "";
					if(!empty($kitchen_data['consolid_kot_print_details']) && !empty($kitchen_data['consolid_kitchens'])){
						$i =1;
						$list = array();
						/*echo "<pre>";
						print_r($order_data->kit_o);die;*/
						$orderItemCnt = count($kitchen_data['consolid_kitchens']);
						foreach($kitchen_data['consolid_kitchens'] as $item_data){
							
							
							$print_items .= '';
							$print_items .= $i;
							$print_items .= ' ';
							if(!empty($item_data['khmer_recipe_image'])){
								$print_items .= $item_data['khmer_recipe_image'];
							}else{
								$print_items .= $item_data['recipe_name'];
							}
							$print_items .= "";
							$print_items .= '   X ';
							$print_items .= $item_data['quantity'];
							$print_items .= "\n";
							$newline = false;
							if($orderItemCnt!=$i){
							    $newline ="\n";
							}

							$en_recipe_name=$item_data['en_recipe_name'];							
							if( $item_data['en_variant_name']  !=''){
								$en_recipe_name=$item_data['en_recipe_name'].'-'.$item_data['en_variant_name'];	
							}
							if($this->pos_settings->kot_print_lang_option != 1 ){
								$item_name = $this->wraprecipe_name_qty($en_recipe_name,$item_data['quantity'],$newline);
							}else{
								$item_name = $en_recipe_name;
							}

							$list[] = array(
								'sno' => $i,
								'code'=>$item_data['code'],
								'en_recipe_name' => $item_name,
								'khmer_image' => $item_data['khmer_recipe_image'],
								'comment' => $item_data['comment'],
								'quantity' => $item_data['quantity'],	
								'addons' => $item_data['addons'] ? $item_data['addons'] :'',							
							);
							$i++;
						}
						$print_mirroring = explode(',',$order_data['print_mirroring']);
						$Printers = array($order_data);
						
						if(!empty($print_mirroring)){
						    $otherPrinters = $this->site->getPrinters($print_mirroring);						    
						    if($otherPrinters){						    
							$Printers = array_merge(array($order_data),$otherPrinters);
						    }
						}
						
						$kot_print_lang_option = $this->pos_settings->kot_print_lang_option;
						$kot_font_size = $this->pos_settings->kot_font_size;

							//Remote printing KOT
						foreach($Printers as $k => $print){
						    //Remote printing KOT
						    $receipt = array(
							    'store_name' => $store_name,
							    'header' => $print_header,
							    'info' => $print_info,
							    'items' => $print_items,
							    'itemlists' => $list,
								'cancelHeader'=>$cancelHeader,
							    'kot_print_lang_option' => $kot_print_lang_option,//0->english,1->local,2->both
								'kot_font_size' => $kot_font_size,//0->small,1->medium,2->large
						    );
						    $data = array(
						    'type'=>'print-receipt',
						    'data'=>array(
							    'printer' => $print,
							    //'logo'=> base_url().'assets/uploads/logos/'.$biller->logo,
							    'text' => $receipt,
							    'cash_drawer' => ''
						    )
						    );
						    if($this->pos_settings->consolid_kot_print_logo){
							$data['data']['logo'] = base_url().'assets/uploads/logos/'.$biller->logo;
						    }
						    if(!empty($this->ws->checkConnection())){//echo '<pre>';print_R($data);
						    $result = $this->ws->send(json_encode($data));
						    $this->ws->close();
						    }
						}
						/*echo "<pre>";
						print_r($data);die;*/

					}
				}
				/*echo "<pre>";
						print_r($data);die;*/
			}//die;
		}
    }
	
	
/*kitchen consolidate kot */	
        function consolidate_kitchens_kot($kitchen_data){
//	ob_end_clean();
//        ignore_user_abort();
//        ob_start();
//        header("Connection: close");
//        echo @json_encode($out);
//        header("Content-Length: " . ob_get_length());
//        @ob_end_flush();
//        flush();
//	
//	
//	$kitchen_data = json_decode(file_get_contents('php://input'), true);
//	$kitchen_data = $kitchen_data['k_data'];
	$kitchen_data['orders_details'] = (object) $kitchen_data['orders_details'];
	
	//echo '<pre>';print_R($kitchen_data);
	if(!empty($kitchen_data)){
			$ordered_by = 'N/A';
			$user = $this->site->getUser($kitchen_data['orders_details']->created_by); 
			if($user){ 
			$ordered_by = $user->first_name.' '.$user->last_name; 
			}
			$biller = $this->site->getCompanyOrderByID($kitchen_data['orders_details']->biller_id);
			$print_header = "";
			if($kitchen_data['orders_details']->order_type == 1 || $kitchen_data['orders_details']->order_type == 4 ){
				$store_name = "Table : #".$kitchen_data['orders_details']->table_name;
			} elseif($kitchen_data['orders_details']->order_type == 2){
				$store_name = "Takeaway : #".$kitchen_data['orders_details']->reference_no;
			} else{
				$store_name = "Delivery : #".$kitchen_data['orders_details']->reference_no;
			}
			//$print_header .= $biller->company;
			//$print_header .= ', ';
			//$print_header .= $biller->address;
			if($this->Settings->time_format == 12){
			$date = new DateTime($kitchen_data['orders_details']->created_on);
			$created_on = $date->format('Y-m-d h:iA');
			}else{
				$created_on =  $kitchen_data['orders_details']->created_on;
			}

			$print_header .= "\n";
			$print_header .= "KITCHEN CONSOLID COPY KOT ORDER";
			if(isset($kitchen_data['orders_details']->is_print_copy)){
			    $print_header .= " - COPY";
			}
			$print_header .= "\n";
			$print_info_common = "";
			if($this->pos_settings->kot_order_no_print_option){
			$print_info_common .= 'Order Number';
			$print_info_common .= ' : ';
			$print_info_common .= $kitchen_data['orders_details']->reference_no;
			$print_info_common .= "\n";
		    }
			$print_info_common .= 'Date';
			$print_info_common .= ' : ';
			$print_info_common .= $created_on;
			$print_info_common .= "\n";
			$print_info_common .= 'Order Person';
			$print_info_common .= ' : ';
			$print_info_common .= $ordered_by;
			$print_info_common .= "\n";
			
			
			
			if(!empty($kitchen_data['kitchens'])){
				foreach($kitchen_data['kitchens'] as $order_data){
				    $order_data = (object) $order_data;
					$print_info = ''; 
					$print_info .= $print_info_common;
					$print_info .= 'Kitchen Type';
					$print_info .= ' : ';
					$print_info .= $order_data->name;
					$print_info .= "\n-----------------------------------------------\n";
					$print_items = "";
					
					if(!empty($order_data->kit_o) && !empty($order_data->printers_details)){
						$i =1;
						$list = array();
						$orderItemCnt = count($order_data->kit_o);
						foreach($order_data->kit_o as $item_data){
							//$item_data  = (array) $item_data;
							$print_items .= '';
							$print_items .= $i;
							$print_items .= ' ';
							if(!empty($item_data['khmer_recipe_image'])){
								$print_items .= $item_data['khmer_recipe_image'];
							}else{
								$print_items .= $item_data['recipe_name'];
							}
							$print_items .= "";
							$print_items .= '   X ';
							$print_items .= $item_data['quantity'];
							$print_items .= "\n";
							$newline = false;
							if($orderItemCnt!=$i){
							    //$newline ="\n";
							}
														
							$en_recipe_name=$item_data['en_recipe_name'];							
							if( $item_data['en_variant_name']  !=''){
								$en_recipe_name=$item_data['en_recipe_name'].'-'.$item_data['en_variant_name'];	
							}
							if($this->pos_settings->kot_print_lang_option != 1 ){
								$item_name = $this->wraprecipe_name_qty($en_recipe_name,$item_data['quantity'],$newline);
							}else{
								$item_name = $en_recipe_name;
							}							

							$list[] = array(
								'sno' => $i,
								'code'=>$item_data['code'],
								'en_recipe_name' => $item_name,
								'khmer_image' => $item_data['khmer_recipe_image'],
								'quantity' => $item_data['quantity'],
								'addons' => $item_data['addons'] ? $item_data['addons'] :'',	
								'comment' => $item_data['comment'],
								'kot_print_lang_option' => $kot_print_lang_option,//0->english,1->local,2->both
								//'kot_font_size' => $kot_font_size,//0->small,1->medium,2->large
								//'comment' => $item_data['comment']
							);
							$i++;
						}
						$print_mirroring = explode(',',$order_data->printers_details['print_mirroring']);
						$Printers = array($order_data->printers_details);
						
						if(!empty($print_mirroring)){
						    $otherPrinters = $this->site->getPrinters($print_mirroring);						    
						    if($otherPrinters){						    
							$Printers = array_merge(array($order_data->printers_details),$otherPrinters);
						    }
						}
						
						$kot_print_lang_option = $this->pos_settings->kot_print_lang_option;
						$kot_font_size = $this->pos_settings->kot_font_size;
							//Remote printing KOT
						foreach($Printers as $k => $print){
						    //Remote printing KOT
						    $receipt = array(
							    'store_name' => $store_name,
							    'header' => $print_header,
							    'info' => $print_info,
							    'items' => $print_items,
							    'itemlists' => $list,
							    'kot_print_lang_option' => $kot_print_lang_option,//0->english,1->local,2->both
								'kot_font_size' => $kot_font_size,//0->small,1->medium,2->large
						    );
						    $data = array(
						    'type'=>'print-receipt',
						    'data'=>array(
							    'printer' => $print,
							    //'logo'=> base_url().'assets/uploads/logos/'.$biller->logo,
							    'text' => $receipt,
							    'cash_drawer' => ''
						    )
						    );
						    if($this->pos_settings->kot_print_logo){
							$data['data']['logo'] = base_url().'assets/uploads/logos/'.$biller->logo;
						    }
						    if(!empty($this->ws->checkConnection())){//echo '<pre>';print_R($data);
						    $result = $this->ws->send(json_encode($data));
						    $this->ws->close();
						    }
						}
					}
				}
			}//die;
		}
    }
	 function cancel_consolidate_kitchens_kot($kitchen_data){
//	ob_end_clean();
//        ignore_user_abort();
//        ob_start();
//        header("Connection: close");
//        echo @json_encode($out);
//        header("Content-Length: " . ob_get_length());
//        @ob_end_flush();
//        flush();
//	
//	
//	$kitchen_data = json_decode(file_get_contents('php://input'), true);
//	$kitchen_data = $kitchen_data['k_data'];
	$kitchen_data['orders_details'] = (object) $kitchen_data['orders_details'];
	
	//echo '<pre>';print_R($kitchen_data);
	if(!empty($kitchen_data)){
			$ordered_by = 'N/A';
			$user = $this->site->getUser($kitchen_data['orders_details']->created_by); 
			if($user){ 
			$ordered_by = $user->first_name.' '.$user->last_name; 
			}
			$biller = $this->site->getCompanyOrderByID($kitchen_data['orders_details']->biller_id);
			$print_header = "";
			if($kitchen_data['orders_details']->order_type == 1 || $kitchen_data['orders_details']->order_type == 4 ){
				$store_name = "Table : #".$kitchen_data['orders_details']->table_name;
			} elseif($kitchen_data['orders_details']->order_type == 2){
				$store_name = "Takeaway : #".$kitchen_data['orders_details']->reference_no;
			} else{
				$store_name = "Delivery : #".$kitchen_data['orders_details']->reference_no;
			}
			//$print_header .= $biller->company;
			//$print_header .= ', ';
			//$print_header .= $biller->address;
			if($this->Settings->time_format == 12){
			$date = new DateTime($kitchen_data['orders_details']->created_on);
			$created_on = $date->format('Y-m-d h:iA');
			}else{
				$created_on =  $kitchen_data['orders_details']->created_on;
			}

			$print_header .= "\n";
			$print_header .= "CANCEL KITCHEN CONSOLID COPY KOT ORDER";
			if(isset($kitchen_data['orders_details']->is_print_copy)){
			    $print_header .= " - COPY";
			}
			$print_header .= "\n";
			$print_info_common = "";
			if($this->pos_settings->kot_order_no_print_option){
			$print_info_common .= 'Order Number';
			$print_info_common .= ' : ';
			$print_info_common .= $kitchen_data['orders_details']->reference_no;
			$print_info_common .= "\n";
		    }
			$print_info_common .= 'Date';
			$print_info_common .= ' : ';
			$print_info_common .= $created_on;
			$print_info_common .= "\n";
			$print_info_common .= 'Order Person';
			$print_info_common .= ' : ';
			$print_info_common .= $ordered_by;
			$print_info_common .= "\n";
			
			
			
			if(!empty($kitchen_data['kitchens'])){
				foreach($kitchen_data['kitchens'] as $order_data){
				    $order_data = (object) $order_data;
					$print_info = ''; 
					$print_info .= $print_info_common;
					$print_info .= 'Kitchen Type';
					$print_info .= ' : ';
					$print_info .= $order_data->name;
					$print_info .= "\n-----------------------------------------------\n";
					$print_items = "";
					
					if(!empty($order_data->kit_o) && !empty($order_data->printers_details)){
						$i =1;
						$list = array();
						$orderItemCnt = count($order_data->kit_o);
						foreach($order_data->kit_o as $item_data){
							//$item_data  = (array) $item_data;
							$print_items .= '';
							$print_items .= $i;
							$print_items .= ' ';
							if(!empty($item_data['khmer_recipe_image'])){
								$print_items .= $item_data['khmer_recipe_image'];
							}else{
								$print_items .= $item_data['recipe_name'];
							}
							$print_items .= "";
							$print_items .= '   X ';
							$print_items .= $item_data['quantity'];
							$print_items .= "\n";
							$newline = false;
							if($orderItemCnt!=$i){
							    //$newline ="\n";
							}
														
							$en_recipe_name=$item_data['en_recipe_name'];							
							if( $item_data['en_variant_name']  !=''){
								$en_recipe_name=$item_data['en_recipe_name'].'-'.$item_data['en_variant_name'];	
							}
							if($this->pos_settings->kot_print_lang_option != 1 ){
								$item_name = $this->wraprecipe_name_qty($en_recipe_name,$item_data['quantity'],$newline);
							}else{
								$item_name = $en_recipe_name;
							}							

							$list[] = array(
								'sno' => $i,
								'code'=>$item_data['code'],
								'en_recipe_name' => $item_name,
								'khmer_image' => $item_data['khmer_recipe_image'],
								'quantity' => $item_data['quantity'],
								'addons' => $item_data['addons'] ? $item_data['addons'] :'',	
								'comment' => $item_data['comment'],
								'kot_print_lang_option' => $kot_print_lang_option,//0->english,1->local,2->both
								//'kot_font_size' => $kot_font_size,//0->small,1->medium,2->large
								//'comment' => $item_data['comment']
							);
							$i++;
						}
						$print_mirroring = explode(',',$order_data->printers_details['print_mirroring']);
						$Printers = array($order_data->printers_details);
						
						if(!empty($print_mirroring)){
						    $otherPrinters = $this->site->getPrinters($print_mirroring);						    
						    if($otherPrinters){						    
							$Printers = array_merge(array($order_data->printers_details),$otherPrinters);
						    }
						}
						
						$kot_print_lang_option = $this->pos_settings->kot_print_lang_option;
						$kot_font_size = $this->pos_settings->kot_font_size;
							//Remote printing KOT
						foreach($Printers as $k => $print){
						    //Remote printing KOT
						    $receipt = array(
							    'store_name' => $store_name,
							    'header' => $print_header,
							    'info' => $print_info,
							    'items' => $print_items,
							    'itemlists' => $list,
							    'kot_print_lang_option' => $kot_print_lang_option,//0->english,1->local,2->both
								'kot_font_size' => $kot_font_size,//0->small,1->medium,2->large
						    );
						    $data = array(
						    'type'=>'print-receipt',
						    'data'=>array(
							    'printer' => $print,
							    //'logo'=> base_url().'assets/uploads/logos/'.$biller->logo,
							    'text' => $receipt,
							    'cash_drawer' => ''
						    )
						    );
						    if($this->pos_settings->kot_print_logo){
							$data['data']['logo'] = base_url().'assets/uploads/logos/'.$biller->logo;
						    }
						    if(!empty($this->ws->checkConnection())){//echo '<pre>';print_R($data);
						    $result = $this->ws->send(json_encode($data));
						    $this->ws->close();
						    }
						}
					}
				}
			}//die;
		}
    }

/*kitchen consolidate kot */
    function wraprecipe_name_qty($r_name,$r_qty,$newline){

    if($this->pos_settings->kot_font_size == 0)	{
    	$wrapped = wordwrap($r_name,37,"\n");
    }elseif($this->pos_settings->kot_font_size == 1){
    	$wrapped = wordwrap($r_name,18,"\n");
    }else{
		$wrapped = wordwrap($r_name,19,"\n");    	
    }
	
	$lines = explode("\n", $wrapped);
	$wrap_cnt = count($lines)-1;
	//if($wrap_cnt>0){
		// $lines[$wrap_cnt] = sprintf('%-20.20s %1.0f',$lines[$wrap_cnt], $r_qty); 
	if($this->pos_settings->kot_font_size == 0)	{
		$lines[$wrap_cnt] = sprintf('%-37.37s %2.0f',$lines[$wrap_cnt], $r_qty); 
	}elseif($this->pos_settings->kot_font_size == 1){
		// $lines[$wrap_cnt] = sprintf('%-19.20s %2.0f',$lines[$wrap_cnt], $r_qty); 
		$lines[$wrap_cnt] = sprintf('%-18.18s %2.0f', $lines[$wrap_cnt], $r_qty); 

		// $lines[$wrap_cnt] = sprintf('%-3.37s %8.0f',$lines[$wrap_cnt], $r_qty); 
	}else{
		$lines[$wrap_cnt] = sprintf('%-19.19s %2.0f',$lines[$wrap_cnt], $r_qty); 
	}
	//}
	$items = implode("\n",$lines);
	if($newline) {
	 $items = $items."\n";
	}
	return $items;
    }

}
