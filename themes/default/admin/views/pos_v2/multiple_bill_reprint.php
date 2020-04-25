<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php $currency = $this->site->getAllCurrencies();
?>

<?php 
 foreach ($bill_id as $billid) {
 if (!empty($modal)) { ?>
<div class="modal-dialog no-modal-header" role="document"><div class="modal-content"><div class="modal-body">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i></button>
    <?php
} else {
    ?><!doctype html>
    <html>
    <head>
        <meta charset="utf-8">
        <title><?=$page_title . " " . lang("no") . " " . $inv[$billid]->id;?></title>
        <base href="<?=base_url()?>"/>
        <meta http-equiv="cache-control" content="max-age=0"/>
        <meta http-equiv="cache-control" content="no-cache"/>
        <meta http-equiv="expires" content="0"/>
        <meta http-equiv="pragma" content="no-cache"/>
        <link rel="shortcut icon" href="<?=$assets?>images/favicon.ico"/>
        <link rel="stylesheet" href="<?=$assets?>styles/theme.css" type="text/css"/>
        <link rel="stylesheet" href="<?=$assets?>styles/helpers/bootstrap.min.css" type="text/css" />
        
        <style type="text/css" media="all">
        @page  
        { 
            size: auto;   /* auto is the initial value */ 
            /* this affects the margin in the printer settings */ 
           margin: <?=$pos_settings[$billid]->pre_printed_header?>mm 5mm <?=$pos_settings[$billid]->print_footer_space?>mm 5mm; 
        } 
          

            body { color: #000; }
            #wrapper { max-width: 480px; margin: 0 auto; padding-top: 20px; }
            .btn { border-radius: 0; margin-bottom: 5px; }
            .bootbox .modal-footer { border-top: 0; text-align: center; }
            h3 { margin: 5px 0; }
            .order_barcodes img { float: none !important; margin-top: 5px; }
            @media print {
                 #wrapper { 
                    page-break-before: always;
                  }

                .no-print { display: none; 
                  page-break-inside: auto !important;

                  /*page-break-before: always; */
                }

                #wrapper { max-width: 480px; width: 100%; min-width: 250px; margin: 0; page-break-before: always; page-break-after: always; }
                .no-border { border: none !important; }
                .border-bottom { border-bottom: 1px solid #ddd !important; }
                table tfoot { display: table-row-group; }
                    body, html {
                    margin-top:0px!important;
                    padding-top:0px!important;
                    }
                    #plugin {
                    height: 100%;
                    position: fixed;                    
                    width: 100%;
                    z-index: 1;
                     margin-top:0px!important;
                }
            }
        </style>
        
        <?php if(isset($socket_tableid) && isset($_SERVER['HTTP_REFERER'])):$tableid =$socket_tableid;?>
            <script type="text/javascript" src="<?=$assets?>js/jquery-2.0.3.min.js"></script>
            <script>var curr_page="view_bill";var curr_func="update_tables";var tableid = '<?=$tableid?>';</script>
            <script src="<?=base_url('node_modules/socket.io/node_modules/socket.io-client/socket.io.js')?>"></script>
            <script type="text/javascript" src="<?=$assets?>js/socket/socket_configuration.js"></script>
            <script type="text/javascript" src="<?=$assets?>js/socket/client.js"></script>
        <?php endif; ?>
    </head>

    <body>
        <?php

    } ?>
    <?php 
    /*echo "<pre>";
        print_r($bill_id);*/
       
        /*echo "<pre>";
        var_dump($message[$billid]);*/
        ?>
    <div id="wrapper" class="wrapper_<?php echo $billid ?>" style="margin-top: -20px!important;">
        <div id="receiptData">
            <div class="no-print">
                <?php
                if ($message[$billid]) {
                    ?>
                    <div class="alert alert-success">
                        <button data-dismiss="alert" class="close" type="button">×</button>
                        <?=is_array($message[$billid]) ? print_r($message[$billid], true) : $message[$billid];?>
                    </div>
                    <?php
                } ?>
            </div>
            <div id="receipt-datareceipt-data">
                <div class="text-center">
                    <?php if(!$pos_settings[$billid]->pre_printed_format) { ?> 
                    <?= !empty($biller[$billid]->logo) ? '<img src="'.base_url('assets/uploads/logos/'.$biller[$billid]->logo).'" alt="">' : ''; ?>
                    <?php if($pos_settings[$billid]->print_local_language == 1) : ?>
                    <h3 style="text-transform:uppercase;"><?=$biller[$billid]->local_lang_name;?></h3>
                    <?php endif; ?>
                    <h3 style="text-transform:uppercase;"><?=$biller[$billid]->company != '-' ? $biller[$billid]->company : $biller[$billid]->name;?></h3>
                    <?php
                    if($pos_settings[$billid]->print_local_language == 1) :
                    echo "<p>" .$biller[$billid]->local_lang_address;
                    endif;                     
                    echo "<h4 style='font-weight: bold;'>" . $biller[$billid]->address . " " . $biller[$billid]->city . " " . $biller[$billid]->postal_code . " " . $biller[$billid]->state . " " . $biller[$billid]->country .
                    "<br>" . lang("tel") . ": " . $biller[$billid]->phone; ?>
                    <?php } ?>
                    <?php if($pos_settings[$billid]->reprint_bill_caption) { ?> 
                    <h3 class="text-center">RECEIPT</h3>
                    <?php } ?>
                    <?php 

                    // comment or remove these extra info if you don't need
                    if (!empty($biller[$billid]->cf1) && $biller[$billid]->cf1 != "-") {
                        echo "<br>" . lang("bcf1") . ": " . $biller[$billid]->cf1;
                    }
                    if (!empty($biller[$billid]->cf2) && $biller[$billid]->cf2 != "-") {
                        echo "<br>" . lang("bcf2") . ": " . $biller[$billid]->cf2;
                    }
                    if (!empty($biller[$billid]->cf3) && $biller[$billid]->cf3 != "-") {
                        echo "<br>" . lang("bcf3") . ": " . $biller[$billid]->cf3;
                    }
                    if (!empty($biller[$billid]->cf4) && $biller[$billid]->cf4 != "-") {
                        echo "<br>" . lang("bcf4") . ": " . $biller[$billid]->cf4;
                    }
                    if (!empty($biller[$billid]->cf5) && $biller[$billid]->cf5 != "-") {
                        echo "<br>" . lang("bcf5") . ": " . $biller[$billid]->cf5;
                    }
                    if (!empty($biller[$billid]->cf6) && $biller[$billid]->cf6 != "-") {
                        echo "<br>" . lang("bcf6") . ": " . $biller[$billid]->cf6;
                    }
                    // end of the customer fields                    
                    if ($pos_settings[$billid]->cf_title1 != "" && $pos_settings[$billid]->cf_value1 != "") {
                        echo $pos_settings[$billid]->cf_title1 . ": " . $pos_settings[$billid]->cf_value1 . "<br>";
                    }
                    if ($pos_settings[$billid]->cf_title2 != "" && $pos_settings[$billid]->cf_value2 != "") {
                        echo $pos_settings[$billid]->cf_title2 . ": " . $pos_settings[$billid]->cf_value2 . "<br>";
                    }
                    echo '</h4>';
                    ?>
                </div>
                <?php
                if ($Settings->invoice_view == 1 || $Settings->indian_gst) {
                    ?>
                    <div class="col-sm-12 text-center">
                        <h4 style="font-weight:bold;"><?=lang('tax_invoice');?></h4>
                    </div>
                    <?php
                }

                if($this->Settings->time_format == 12){
                    $date = new DateTime($inv[$billid]->created_on);
                    $created_on = $date->format('Y-m-d h:iA');
                    }else{
                        $created_on =  $inv[$billid]->created_on;
                }

                echo "<span style='font-size:15px;font-weight:bold'>" .lang("bill_no") . ": " . $inv[$billid]->bill_number . "</span></br>";
                echo lang("date") . ": " . $created_on . "<br>";
                echo lang("sale_no_ref") . ": " . $inv[$billid]->reference_no . "<br>";
                if (!empty($inv[$billid]->return_sale_ref)) {
                    echo '<p>'.lang("return_ref").': '.$inv[$billid]->return_sale_ref;
                    if ($inv[$billid]->return_id) {
                        echo ' <a data-target="#myModal2" data-toggle="modal" href="'.admin_url('sales/modal_view/'.$inv[$billid]->return_id).'"><i class="fa fa-external-link no-print"></i></a><br>';
                    } else {
                        echo '</p>';
                    }
                }

                echo lang("customer") . ": " . ($customer[$billid]->company && $customer[$billid]->company != '-' ? $customer[$billid]->company : $customer[$billid]->name) . "<br>";
                  echo lang("sales_person") . ": " . $cashier[$billid]->sales_associate. "<br>";
                 echo lang("cashier") . ": " . $cashier[$billid]->cashier. "</br>";
               // echo "<p>";
                if($tableno[$billid]->order_type == 'Dine in'){
                    echo "Table :";echo $tableno[$billid]->table_name . "<br>";
                }
                
                if(!empty($delivery_person)){
                    echo 'Delivery Address <br>';
                    
                    
                    echo $customer[$billid]->address . "<br>";
                    echo $customer[$billid]->city ." ".$customer[$billid]->state." ".$customer[$billid]->country ."<br>";
                    echo lang("tel") . ": " . $customer[$billid]->phone . "<br><br>";
                    echo "Delivery Person : " .$delivery_person[$billid]->first_name.' '.$delivery_person[$billid]->last_name.' ('.$delivery_person[$billid]->user_number.')';
                    echo "<br>Phone : ".$delivery_person[$billid]->phone ;
                }
                /*if ($pos_settings[$billid]->customer_details) {
                    if ($customer[$billid]->vat_no != "-" && $customer[$billid]->vat_no != "") {
                        echo "<br>" . lang("vat_no") . ": " . $customer[$billid]->vat_no;
                    }
                    echo lang("tel") . ": " . $customer[$billid]->phone . "<br>";
                    echo lang("address") . ": " . $customer[$billid]->address . "<br>";
                    echo $customer[$billid]->city ." ".$customer[$billid]->state." ".$customer[$billid]->country ."<br>";
                    if (!empty($customer[$billid]->cf1) && $customer[$billid]->cf1 != "-") {
                        echo "<br>" . lang("ccf1") . ": " . $customer[$billid]->cf1;
                    }
                    if (!empty($customer[$billid]->cf2) && $customer[$billid]->cf2 != "-") {
                        echo "<br>" . lang("ccf2") . ": " . $customer[$billid]->cf2;
                    }
                    if (!empty($customer[$billid]->cf3) && $customer[$billid]->cf3 != "-") {
                        echo "<br>" . lang("ccf3") . ": " . $customer[$billid]->cf3;
                    }
                    if (!empty($customer[$billid]->cf4) && $customer[$billid]->cf4 != "-") {
                        echo "<br>" . lang("ccf4") . ": " . $customer[$billid]->cf4;
                    }
                    if (!empty($customer[$billid]->cf5) && $customer[$billid]->cf5 != "-") {
                        echo "<br>" . lang("ccf5") . ": " . $customer[$billid]->cf5;
                    }
                    if (!empty($customer[$billid]->cf6) && $customer[$billid]->cf6 != "-") {
                        echo "<br>" . lang("ccf6") . ": " . $customer[$billid]->cf6;
                    }
                }*/                
                ?>

                <!-- <div style="clear:both;"></div> -->
                <table class="table table-striped table-condensed" style="font-size:14px!important;";>
                    <thead>
                        <th colspan="2"><?=lang("description");?></th>
                        <th><?=lang("price");?></th>
                        <th><?=lang("qty");?></th>
                        <th><?=lang("discount");?></th>
                        <?php 
                        $cols="5";
                        if($inv[$billid]->manual_item_discount != 0){
                            $cols="5"; 
                            if ($pos_settings[$billid]->manual_item_discount_display_option == 1){
                                $dis ="dis(%)";
                            }else{
                                $dis ="dis";
                            }
                        ?>
                            <th><?=lang($dis);?></th>
                        <?php } ?>
                        <th class="text-right"><?=lang("sub_total");?></th>
                    </thead>
                    <tbody>
                        <?php
                        $r = 1; $category = 0;
                        $tax_summary = array();
                        
                        foreach ($billi_tems[$billid] as $bill) {
                            /*echo "<pre>";
                            print_r($bill);*/
                            
                            /*if ($pos_settings[$billid]->item_order == 0 && $category != $bill->recipe_id) {
                                $category = $bill->recipe_id;
                                echo '<tr><td class="no-border"><strong>'.$bill->recipe_name.'</strong></td>';
                            }*/
                            
                            if($this->Settings->user_language == 'khmer'){
                                if(!empty($bill->khmer_name)){
                                    $recipe_name = $bill->khmer_name;
                                }else{
                                    $recipe_name = $bill->recipe_name;
                                }
                            }else{
                                $recipe_name = $bill->recipe_name;
                            }

                            $star ='';                            
                            if ($inv[$billid]->total_discount != 0 && !empty($inv[$billid]->customer_discount_id)) {
                                if($this->settings->customer_discount != 'none'){
                                  if($this->settings->customer_discount == 'customer'){                                
                                        $check = $this->site->Check_item_Discount_customer($bill->recipe_id,$inv[$billid]->customer_discount_id);
                                  }
                                      if($check || $bill->manual_item_discount != 0){
                                        $star ='';
                                      }else{
                                        $star ='*';
                                      }
                                }
                           }
                          if($bill->manual_item_discount != 0){
                                $underline ='underline';
                           }else{
                                $underline ='none';
                           }    
                            /*24-01-2019*/
                            /*echo '<tr><td colspan="2" class="no-border"><span style="display: inherit;">' . $r . '. &nbsp;&nbsp;</span><span style="display: table-cell;text-align: -webkit-match-parent;">' . ($recipe_name) . ($bill->variant ? ' ' . $bill->variant . '' : '') . ' </span></td>';*/

                            echo '<tr><td colspan="2" class="no-border"><span style="display: table-cell;text-align: -webkit-match-parent;text-decoration:'.$underline.'">' . ($star)  . '' . ($recipe_name) . ($bill->variant ? ' ' . $bill->variant . '' : '') . ' </span></td>';
                            
                            /*echo '<tr><td colspan="2" class="no-border">' . $r . ': &nbsp;&nbsp;' . ($recipe_name) . ($bill->variant ? ' (' . $bill->variant . ')' : '') . '<span class="pull-right">' . ($bill->tax_code ? '*'.$bill->tax_code : '') . '</span></td>';*/ 

                            echo '<td  class="no-border">'.$this->sma->formatMoney($bill->net_unit_price).'</td>';

                            echo '<td class="no-border text-center">'.$bill->quantity.'</td>';
                            $dis_v=0;
                            if($inv[$billid]->discount_val  && $bill->input_discount!=0){
                                $dis_v = str_replace('Discount ','',$inv[$billid]->discount_val);              
                            }
                            echo '<td class="no-border text-center">'.$dis_v.'</td>';
                            if($inv[$billid]->manual_item_discount != 0){
                                if ($pos_settings[$billid]->manual_item_discount_display_option == 1){
                                echo '<td class="no-border text-right">'.$bill->manual_item_discount_per_val.'</td>';
                                }else{
                                    echo '<td class="no-border">'.$this->sma->formatMoney($bill->manual_item_discount).'</td>';
                                }
                            }                            
                            echo '<td " class="no-border text-right">'.$this->sma->formatMoney($bill->subtotal-$bill->manual_item_discount-$bill->input_discount).'</td></tr>';
                            $r++;
                        }
                        
                        if ($return_rows) {
                            echo '<tr class="warning"><td colspan="100%" class="no-border"><strong>'.lang('returned_items').'</strong></td></tr>';
                            foreach ($return_rows as $row) {
                                if ($pos_settings[$billid]->item_order == 1 && $category != $row->category_id) {
                                    $category = $row->category_id;
                                    echo '<tr><td colspan="100%" class="no-border"><strong>'.$row->category_name.'</strong></td></tr>';
                                }
                                echo '<tr><td colspan="2" class="no-border">#' . $r . ': &nbsp;&nbsp;' . product_name($row->product_name, ($printer ? $printer->char_per_line : null)) . ($row->variant ? ' (' . $row->variant . ')' : '') . '<span class="pull-right">' . ($row->tax_code ? '*'.$row->tax_code : '') . '</span></td></tr>';

                                echo '<tr><td class="no-border border-bottom text-right">' . $this->sma->formatQuantity($row->quantity) . ' x '.$this->sma->formatMoney($row->unit_price).($row->item_tax != 0 ? ' - '.lang('tax').' <small>('.($Settings->indian_gst ? $row->tax : $row->tax_code).')</small> '.$this->sma->formatMoney($row->item_tax).' ('.lang('hsn_code').': '.$row->hsn_code.')' : '').'</td><td class="no-border border-bottom text-right">' . $this->sma->formatMoney($row->subtotal) . '</td></tr>';

                                // echo '<tr><td class="no-border border-bottom">' . $this->sma->formatQuantity($row->quantity) . ' x ';
                                // if ($row->item_discount != 0) {
                                //     echo '<del>' . $this->sma->formatMoney($row->net_unit_price + ($row->item_discount / $row->quantity) + ($row->item_tax / $row->quantity)) . '</del> ';
                                // }
                                // echo $this->sma->formatMoney($row->net_unit_price + ($row->item_tax / $row->quantity)) . '</td><td class="no-border border-bottom text-right">' . $this->sma->formatMoney($row->subtotal) . '</td></tr>';
                                $r++;
                            }
                        }

                        ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="<?php echo $cols; ?>" class="text-right"><?=lang("total");?></th>
                            <th class="text-right"><?=$this->sma->formatMoney($inv[$billid]->total-$inv[$billid]->manual_item_discount-$inv[$billid]->total_discount);?></th>
                        </tr>
                        <?php
                        /*$dis = $this->site->getDiscountsAmt($inv[$billid]->id);*/
                        $total_dis_without_manual =  ($inv[$billid]->total_discount - $inv[$billid]->manual_item_discount);

                        if ($total_dis_without_manual != 0) {
                            if($inv[$billid]->discount_type == 'manual'){
                                 if($inv[$billid]->discount_val){
                                    $disname = $inv[$billid]->discount_val;
                                }else{
                                    $disname = 'Discount';
                                }

                                //echo '<tr><th colspan="'.$cols.'" class="text-right">' . lang("discount") . '('.$disname.')</th><th class="text-right">' . $this->sma->formatMoney($total_dis_without_manual) . '</th></tr>';
                            } else {
                                if($discounnames){
                                    $disname = $discounnames;
                                }else{
                                    $disname = 'Discount';
                                }

                           // echo '<tr><th colspan="'.$cols.'" class="text-right">' .$disname. '</th><th class="text-right">' . $this->sma->formatMoney($total_dis_without_manual) . '</th></tr>';
                            }
                        }
                        if ($inv[$billid]->birthday_discount != 0) {
                            $dpos = strpos($this->pos_settings->birthday_discount, '%');
                             if ($dpos !== false) {
                              echo '<tr><th colspan="'.$cols.'" class="text-right">' . lang("birthday_discount") .'(' . $this->pos_settings->birthday_discount . ')'.'</th><th class="text-right">' . $this->sma->formatMoney($inv[$billid]->birthday_discount) . '</th></tr>';
                            }
                            else{
                                 echo '<tr><th colspan="'.$cols.'" class="text-right">' . lang("birthday_discount") . '</th><th class="text-right">' . $this->sma->formatMoney($inv[$billid]->birthday_discount) . '</th></tr>';
                            }
                        }     
                                           
                        if ($inv[$billid]->tax_type == 0)
                            {
                                $taxname = 'Inclusive';
                                $grandtotal = $inv[$billid]->total-$inv[$billid]->total_discount-$inv[$billid]->birthday_discount;
                            }
                            else
                            {
                                $taxname = 'Exclusive';
                                $grandtotal = $inv[$billid]->total-$inv[$billid]->total_discount-$inv[$billid]->birthday_discount+$inv[$billid]->total_tax;
                            }

                        if ($pos_settings[$billid]->display_tax==1 && $inv[$billid]->tax_rate != 0) {
                            echo '<tr><th colspan="'.$cols.'" class="text-right">' . $pos_settings[$billid]->tax_caption. '</th>';

                            if ($pos_settings[$billid]->display_tax_amt==1 && $inv[$billid]->tax_rate != 0) {
                                 echo '<th colspan="2" class="text-right">' . lang($this->sma->formatMoney($inv[$billid]->total_tax)."") . '</th>';
                              }
                              echo '</tr>';
                        }      
                        /*if ($inv[$billid]->shipping != 0) {
                            echo '<tr><th colspan="3">' . lang("shipping") . '</th><th class="text-right">' . $this->sma->formatMoney($inv[$billid]->shipping) . '</th></tr>';
                        }

                        if ($return_sale) {
                            if ($return_sale->surcharge != 0) {
                                echo '<tr><th colspan="3">' . lang("order_discount") . '</th><th class="text-right">' . $this->sma->formatMoney($return_sale->surcharge) . '</th></tr>';
                            }
                        }
*/
                        if ($Settings->indian_gst) {
                            if ($inv[$billid]->cgst > 0) {
                                $cgst = $return_sale ? $inv[$billid]->cgst + $return_sale->cgst : $inv[$billid]->cgst;
                                echo '<tr><td colspan="5">' . lang('cgst') .'</td><td class="text-right">' . ( $Settings->format_gst ? $this->sma->formatMoney($cgst) : $cgst) . '</td></tr>';
                            }
                            if ($inv[$billid]->sgst > 0) {
                                $sgst = $return_sale ? $inv[$billid]->sgst + $return_sale->sgst : $inv[$billid]->sgst;
                                echo '<tr><td colspan="5">' . lang('sgst') .'</td><td class="text-right">' . ( $Settings->format_gst ? $this->sma->formatMoney($sgst) : $sgst) . '</td></tr>';
                            }
                            if ($inv[$billid]->igst > 0) {
                                $igst = $return_sale ? $inv[$billid]->igst + $return_sale->igst : $inv[$billid]->igst;
                                echo '<tr><td colspan="5">' . lang('igst') .'</td><td class="text-right">' . ( $Settings->format_gst ? $this->sma->formatMoney($igst) : $igst) . '</td></tr>';
                            }
                        }

                        if ($pos_settings[$billid]->rounding || $inv[$billid]->rounding != 0) {

                            $n = $grandtotal;//1.25
                                $whole = floor($n); // 1
                                $riel = $n - $whole; // .25
                                $exchange_rate = $this->site->getExchangeRatey($this->Settings->default_currency);

                            ?>

                           <?php if ($pos_settings[$billid]->print_option == 1) {  
                              foreach($currency as $currency_row){ ?>
                            <tr>
                                <th colspan="<?php echo $cols; ?>" class="text-right"><?=lang("grand_total");?><?php echo '('.$currency_row->code.')'?></th> 
                                <?php                               
                                $amt = $this->sma->formatDecimal($grandtotal);                                  
                                $r1 = $this->sma->formatDecimal($riel);                                               
                                // $round_riel = round($riel, 2);
                                
                                $round_riel = $riel/ $exchange_rate;
                                $round_riel = round($round_riel / 100) * 100;

                                // $round_riel = ceil($riel / 100) * 100;

                                if($this->Settings->default_currency != $currency_row->id){
                                    $final_amt = $grandtotal/ $currency_row->rate;
                                    $final_amt = round($final_amt / 100) * 100; ?>
                                    <!-- <th class="text-right"><?= $currency_row->symbol ?><?=$final_amt;?> -->
                                        <th class="text-right"><?= $this->sma->exchangeformatMoney($final_amt,$currency_row->symbol)?>
                                <?php  }else{
                                    $final_amt = $grandtotal/ $currency_row->rate; ?>
                                    <th class="text-right"><?=$this->sma->formatMoney($final_amt,$currency_row->symbol);?>
                                <?php }
                                ?>
                            <!-- <th class="text-right"><?=$this->sma->formatMoney($final_amt,$currency_row->symbol);?> -->
                                
                                <?php if($this->Settings->default_currency == $currency_row->id)
                                {
                                $exchange_curr_code = $this->site->getExchangeCurrency($this->Settings->default_currency);
                                  echo '('.$exchange_curr_code.$round_riel.')';
                                  // echo '('.$this->sma->formatMoney($round_riel / $exchange_rate,$exchange_curr_code).')' ;

                                }?> </th>
                            </tr>
                            <?php }  } else{                         
                            ?>

                            <tr>
                                <th colspan="<?php echo $cols; ?>" class="text-right"><?=lang("grand_total");?></th>
                                <th class="text-right"><?=$this->sma->formatMoney($grandtotal);?></th>
                            </tr>
                            <?php
                        } }else {


                            ?>
                          
                            <?php foreach($currency as $currency_row){ ?>
                                <tr>
                                    <th colspan="<?php echo $cols; ?>" class="text-right"><?=lang("grand_total");?><?php echo '('.$currency_row->code.')'?></th> 

                                    <th class="text-right"><?=$this->sma->formatMoney(($return_sale ? ($inv[$billid]->grand_total+$return_sale->grand_total) : $inv[$billid]->grand_total) / $currency_row->rate,$currency_row->symbol);?></th>
                                </tr>
                             <?php  } ?> 

                            <?php }
                        if ($inv[$billid]->paid < ($inv[$billid]->grand_total + $inv[$billid]->rounding)) {
                            ?>
                            <!-- <tr>
                                <th colspan="6" class="text-right"><?=lang("paid_amount");?></th>
                                <th class="text-right"><?=$this->sma->formatMoney($return_sale ? ($inv[$billid]->paid+$return_sale->paid) : $inv[$billid]->paid);?></th>
                            </tr>
                            <tr>
                                <th colspan="6" class="text-right"><?=lang("due_amount");?></th>
                                <th class="text-right"><?=$this->sma->formatMoney(($return_sale ? (($inv[$billid]->grand_total + $inv[$billid]->rounding)+$return_sale->grand_total) : ($inv[$billid]->grand_total + $inv[$billid]->rounding)) - ($return_sale ? ($inv[$billid]->paid+$return_sale->paid) : $inv[$billid]->paid));?></th>
                            </tr> -->
                            <?php
                        } ?>
                        <tr>
                        <?php
                        
                            //echo '<tr><th colspan="4" class="text-right">' . lang("tax_inclusive (".$inv[$billid]->tax_name.")") . '</th><th class="text-right">' . $this->sma->formatMoney($return_sale ? ($inv[$billid]->order_tax+$return_sale->order_tax) : $inv[$billid]->total_tax) . '</th></tr>';
                             $taxcol = "3";
                            if($inv[$billid]->manual_item_discount != 0)
                            {
                                $taxcol = "4";
                            }
                            
                        if ($pos_settings[$billid]->discount_note_display_option==1) {       
                           if ($inv[$billid]->total_discount != 0) {
                             if($this->settings->customer_discount != 'none'){
                                  echo '<tr><th colspan="5" class="text-left"><small>* Bill Discount is not applied to these items</small></th></tr>';
                                }
                                if($inv[$billid]->manual_item_discount != 0){
                                    echo '<tr><th colspan="5" class="text-left"><small>Underlined Items are manually Discount is applied.</small></th></tr>';
                                }
                            } 
                         }    

                        ?>
                        </tr>
                    </tfoot>
                </table>
                <?php
              
                if ($payments[$billid]) {
                    echo '<table class="table table-striped table-condensed" style="margin-top:-15px!important;"><tbody>';                   
                    foreach ($payments[$billid] as $payment) {
                        echo '<tr>';
                        if (($payment->paid_by == 'cash' || $payment->paid_by == 'credit' || $payment->paid_by == 'deposit') && $payment->pos_paid) {
                            echo '<td>' . lang("paid_by12") . ': ' . lang($payment->paid_by) . '</td>';
                            echo '<td>' . lang("amount") . ': ' . $this->sma->formatMoney($payment->pos_paid == 0 ? $payment->amount : $payment->pos_paid) . ($payment->return_id ? ' (' . lang('returned') . ')' : '') . '</td>';

                             $exchange_curr_code = $this->site->getExchangeCurrency($this->Settings->default_currency);

                             echo '<td>' . lang("foreign_exchange") . ': ' . $this->sma->formatMoney($payment->amount_exchange,$exchange_curr_code) . '</td>';
                     } elseif (($payment->paid_by == 'loyalty') ) {      
                            echo '<td>' . lang("paid_by") . ': ' . lang($payment->paid_by) . '</td>';
                            echo '<td>' . lang("amount") . ': ' . $this->sma->formatMoney($payment->pos_paid == 0 ? $payment->amount : $payment->pos_paid) . ($payment->return_id ? ' (' . lang('returned') . ')' : '') . '</td>';
                            
                        } elseif (($payment->paid_by == 'CC' || $payment->paid_by == 'ppp' || $payment->paid_by == 'stripe') ) {//&& $payment->cc_no
                            
                            echo '<td>' . lang("paid_by") . ': ' . lang($payment->paid_by) . '</td>';
                            echo '<td>' . lang("amount") . ': ' . $this->sma->formatMoney($payment->pos_paid) . ($payment->return_id ? ' (' . lang('returned') . ')' : '') . '</td>';
                            echo '<td>' . lang("no") . ': ' . 'xxxx xxxx xxxx ' . substr($payment->cc_no, -4) . '</td>';
                            echo '<td>' . lang("name") . ': ' . $payment->cc_holder . '</td>';
                        } elseif ($payment->paid_by == 'Cheque' && $payment->cheque_no) {
                            echo '<td>' . lang("paid_by") . ': ' . lang($payment->paid_by) . '</td>';
                            echo '<td>' . lang("amount") . ': ' . $this->sma->formatMoney($payment->pos_paid) . ($payment->return_id ? ' (' . lang('returned') . ')' : '') . '</td>';
                            echo '<td>' . lang("cheque_no") . ': ' . $payment->cheque_no . '</td>';
                        } elseif ($payment->paid_by == 'gift_card' && $payment->pos_paid) {
                            echo '<td>' . lang("paid_by") . ': ' . lang($payment->paid_by) . '</td>';
                            echo '<td>' . lang("no") . ': ' . $payment->cc_no . '</td>';
                            echo '<td>' . lang("amount") . ': ' . $this->sma->formatMoney($payment->pos_paid) . ($payment->return_id ? ' (' . lang('returned') . ')' : '') . '</td>';
                            echo '<td>' . lang("balance") . ': ' . ($payment->pos_balance > 0 ? $this->sma->formatMoney($payment->pos_balance) : 0) . '</td>';
                        } elseif ($payment->paid_by == 'other' && $payment->amount) {
                            echo '<td>' . lang("paid_by") . ': ' . lang($payment->paid_by) . '</td>';
                            echo '<td>' . lang("amount") . ': ' . $this->sma->formatMoney($payment->pos_paid == 0 ? $payment->amount : $payment->pos_paid) . ($payment->return_id ? ' (' . lang('returned') . ')' : '') . '</td>';
                            echo $payment->payment_note ? '</tr><td colspan="2">' . lang("payment_note") . ': ' . $payment->payment_note . '</td>' : '';
                        }
                        echo '</tr>';
                    }
                    echo '</tbody></table>';
                }

                /*if ($return_payments) {
                    echo '<strong>'.lang('return_payments').'</strong><table class="table table-striped table-condensed"><tbody>';

                    foreach ($return_payments as $payment) {
                        $payment->amount = (0-$payment->amount);
                        echo '<tr>';
                        if (($payment->paid_by == 'cash' || $payment->paid_by == 'deposit') && $payment->pos_paid) {
                            echo '<td>' . lang("paid_by") . ': ' . lang($payment->paid_by) . '</td>';
                            echo '<td>' . lang("amount") . ': ' . $this->sma->formatMoney($payment->pos_paid == 0 ? $payment->amount : $payment->pos_paid) . ($payment->return_id ? ' (' . lang('returned') . ')' : '') . '</td>';
                            echo '<td>' . lang("change") . ': ' . ($payment->pos_balance > 0 ? $this->sma->formatMoney($payment->pos_balance) : 0) . '</td>';
                        } elseif (($payment->paid_by == 'CC' || $payment->paid_by == 'ppp' || $payment->paid_by == 'stripe') && $payment->cc_no) {
                            echo '<td>' . lang("paid_by") . ': ' . lang($payment->paid_by) . '</td>';
                            echo '<td>' . lang("amount") . ': ' . $this->sma->formatMoney($payment->pos_paid) . ($payment->return_id ? ' (' . lang('returned') . ')' : '') . '</td>';
                            echo '<td>' . lang("no") . ': ' . 'xxxx xxxx xxxx ' . substr($payment->cc_no, -4) . '</td>';
                            echo '<td>' . lang("name") . ': ' . $payment->cc_holder . '</td>';
                        } elseif ($payment->paid_by == 'Cheque' && $payment->cheque_no) {
                            echo '<td>' . lang("paid_by") . ': ' . lang($payment->paid_by) . '</td>';
                            echo '<td>' . lang("amount") . ': ' . $this->sma->formatMoney($payment->pos_paid) . ($payment->return_id ? ' (' . lang('returned') . ')' : '') . '</td>';
                            echo '<td>' . lang("cheque_no") . ': ' . $payment->cheque_no . '</td>';
                        } elseif ($payment->paid_by == 'gift_card' && $payment->pos_paid) {
                            echo '<td>' . lang("paid_by") . ': ' . lang($payment->paid_by) . '</td>';
                            echo '<td>' . lang("no") . ': ' . $payment->cc_no . '</td>';
                            echo '<td>' . lang("amount") . ': ' . $this->sma->formatMoney($payment->pos_paid) . ($payment->return_id ? ' (' . lang('returned') . ')' : '') . '</td>';
                            echo '<td>' . lang("balance") . ': ' . ($payment->pos_balance > 0 ? $this->sma->formatMoney($payment->pos_balance) : 0) . '</td>';
                        } elseif ($payment->paid_by == 'other' && $payment->amount) {
                            echo '<td>' . lang("paid_by") . ': ' . lang($payment->paid_by) . '</td>';
                            echo '<td>' . lang("amount") . ': ' . $this->sma->formatMoney($payment->pos_paid == 0 ? $payment->amount : $payment->pos_paid) . ($payment->return_id ? ' (' . lang('returned') . ')' : '') . '</td>';
                            echo $payment->payment_note ? '</tr><td colspan="2">' . lang("payment_note") . ': ' . $payment->payment_note . '</td>' : '';
                        }
                        echo '</tr>';
                    }
                    echo '</tbody></table>';
                }*/
                ?>
                
              <?php  if ($pos_settings[$billid]->print_option == 1) { ?>
                <table class="table table-striped table-condensed" style="margin-top:-15px!important;">
                    <tfoot>
                        <?php                        
                            $n = $inv[$billid]->balance;//1.25
                            $whole = floor($n); // 1
                            $riel = $n - $whole; // .25
                            $exchange_rate = $this->site->getExchangeRatey($this->Settings->default_currency);
                        foreach($currency as $currency_row){
                        ?>
                        <tr>
                            <th class="text-right"><?=lang("change ".$currency_row->code."");?></th>
                            <th class="text-right"><?=$this->sma->formatMoney($inv[$billid]->balance / $currency_row->rate,$currency_row->symbol);?>
                                 <?php if($this->Settings->default_currency == $currency_row->id)
                            {
                                $change_riel = $riel/ $exchange_rate;
                               $change_riel = round($change_riel / 100) * 100; 

                               $exchange_curr_code = $this->site->getExchangeCurrency($this->Settings->default_currency);
                               echo '('.$exchange_curr_code.$change_riel.')' ;   
                               //echo '('.$this->sma->formatMoney($riel / $exchange_rate,$exchange_curr_code).')' ;

                             }?> 
                            </th>
                        </tr>
                        <?php }  ?>
                    </tfoot>
                </table>
            <?php } else{?>                
                <table class="table table-striped table-condensed">                    
                    <tfoot>
                        <?php
                        $currency = $this->site->getAllCurrencies();
                        
                        foreach($currency as $currency_row){
                        ?>
                        <tr>
                            <th class="text-right"><?=lang("change ".$currency_row->code."");?></th>
                            <th class="text-right"><?=$this->sma->formatMoney($inv[$billid]->balance / $currency_row->rate,$currency_row->symbol);?></th>
                        </tr>
                        <?php
                        }
                        ?>
                        
                    </tfoot>
                </table>
                <?php } ?>
                

                <?= $Settings->invoice_view > 0 ? $this->gst->summary($rows, $return_rows, ($return_sale ? $inv[$billid]->product_tax+$return_sale->product_tax : $inv[$billid]->product_tax)) : ''; ?>

                <?= $customer[$billid]->award_points != 0 && $Settings->each_spent > 0 ? '<p class="text-center">'.lang('this_sale').': '.floor(($inv[$billid]->grand_total/$Settings->each_spent)*$Settings->ca_point)
                .'<br>'.
                lang('total').' '.lang('award_points').': '. $customer[$billid]->award_points . '</p>' : ''; ?>
                <?= $inv[$billid]->note ? '<p class="text-center">' . $this->sma->decode_html($inv[$billid]->note) . '</p>' : ''; ?>
                <?= $payment->staff_note ? '<p class="no-print"><strong>' . lang('staff_note') . ':</strong> ' . $this->sma->decode_html($payment->staff_note) . '</p>' : ''; ?>
                <?= $biller[$billid]->invoice_footer ? '<p class="text-center" style="margin-top:-15px!important;">'.$this->sma->decode_html($biller[$billid]->invoice_footer).'</p>' : ''; ?>
            </div>

            <div class="order_barcodes text-center">
                <?php if($this->Settings->enable_barcode) : ?>
                <img src="<?= admin_url('misc/barcode/'.$this->sma->base64url_encode($inv[$billid]->reference_no).'/code128/74/0/1'); ?>" alt="<?= $inv[$billid]->reference_no; ?>" class="bcimg" />
                <?php endif; ?>
                <?php if($this->Settings->enable_qrcode) : ?>
                    <br>
                    <?= $this->sma->qrcode('link', urlencode($inv[$billid]->reference_no), 2); ?>
                <?php endif; ?>
            </div>
            <div style="clear:both;"></div>
        </div>
         <div style="width:100%; position:relative; min-height:100px;"></div>

        <div id="buttons" style="padding-top:10px; text-transform:uppercase;     padding-top: 10px;
    text-transform: uppercase;
    position: fixed;
    bottom: 0px;
    min-height: 100px;
    background: #fff;
    width: 100%;
    padding:0px 30%;
    left: 0px;" class="no-print">
            <hr style="margin-top: 0px;">
            <?php
            if ($message[$billid]) {
                ?>
                <div class="alert alert-success">
                    <button data-dismiss="alert" class="close" type="button">×</button>
                    <?=is_array($message[$billid]) ? print_r($message[$billid], true) : $message[$billid];?>
                </div>
                <?php
            } ?>
            <?php
            if (!empty($modal)) {
                ?>
                <div class="btn-group btn-group-justified" role="group" aria-label="...">
                    <div class="btn-group" role="group">
                        <?php
                        if ($pos->remote_printing == 1) {
                            echo '<button onclick="window.print();" class="btn btn-block btn-primary">'.lang("print").'</button>';
                        } else {
                            echo '<button onclick="return printReceipt()" class="btn btn-block btn-primary">'.lang("print").'</button>';
                        }

                        ?>
                    </div>
                    <div class="btn-group" role="group">
                        <a class="btn btn-block btn-success" href="#" id="email"><?= lang("email"); ?></a>
                    </div>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close'); ?></button>
                    </div>
                </div>
                <?php
            } else {
                ?>
                <span class="pull-right col-xs-12">
                    <?php
                    echo '<button onclick="window.print();" class="btn btn-block btn-primary">'.lang("print").'</button>';

                     //echo '<button" id="print_all" class="btn btn-block btn-primary">'.lang("print").'</button>';
                    /*if ($pos->remote_printing == 1) {
                        echo '<button onclick="window.print();" class="btn btn-block btn-primary">'.lang("print").'</button>';
                    } else {
                        echo '<button onclick="return printReceipt()" class="btn btn-block btn-primary">'.lang("print").'</button>';
                        echo '<button onclick="return openCashDrawer()" class="btn btn-block btn-default">'.lang("open_cash_drawer").'</button>';
                    }*/
                    ?>
                </span>
                <!-- <span class="pull-left col-xs-12"><a class="btn btn-block btn-success" href="#" id="email"><?= lang("email"); ?></a></span> -->
                <span class="col-xs-12">
                    <!-- <a class="btn btn-block btn-warning" id="reprinter"  href="javascript:void(0)"><?= lang("back_to_reprint"); ?></a> -->
                    <a class="btn btn-block btn-warning" href="<?= base_url('pos/pos/reprint'); ?>"><?= lang("back_to_reprint"); ?></a> 
                </span>
                <?php
            }
            if ($pos_settings[$billid]->remote_printing == 1) {
                ?>
                <div style="clear:both;"></div>
                <div class="col-xs-12" style="background:#F5F5F5; padding:10px; display: none;">
                    <p style="font-weight:bold;">
                        Please don't forget to disble the header and footer in browser print settings.
                    </p>
                    <p style="text-transform: capitalize;">
                        <strong>FF:</strong> File &gt; Print Setup &gt; Margin &amp; Header/Footer Make all --blank--
                    </p>
                    <p style="text-transform: capitalize;">
                        <strong>chrome:</strong> Menu &gt; Print &gt; Disable Header/Footer in Option &amp; Set Margins to None
                    </p>
                </div>
                <?php
            } ?>
            <div style="clear:both;"></div>
           
        </div>
    </div>
    <div style="clear:both;"></div>
    <!-- <script type="text/javascript">
    PrintDiv("#wrapper_<?php echo $billid?>");
    </script> -->
    <div class="page-break"></div>

<?php } ?>
    <?php
    if( ! $modal) {
        ?>
        <script type="text/javascript" src="<?= $assets ?>js/jquery-2.0.3.min.js"></script>
        <script type="text/javascript" src="<?= $assets ?>js/bootstrap.min.js"></script>
        <script type="text/javascript" src="<?= $assets ?>js/jquery.dataTables.min.js"></script>
        <script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>
        <?php
    }
    ?>
    <script type="text/javascript">

$(document).on("click","#print_all",function() {

    <?php foreach ($bill_id as $billid) { ?>
        var data = $(".wrapper_<?php echo $billid?>").html();        
        PrintDiv(data);
    <?php } ?>
});
        
$(document).ready(function () {
            $('#email').click(function () {
                bootbox.prompt({
                    title: "<?= lang("email_address"); ?>",
                    inputType: 'email',
                    value: "<?= $customer[$billid]->email; ?>",
                    callback: function (email) {
                        if (email != null) {
                            $.ajax({
                                type: "post",
                                url: "<?= admin_url('pos/email_receipt') ?>",
                                data: {<?= $this->security->get_csrf_token_name(); ?>: "<?= $this->security->get_csrf_hash(); ?>", email: email, id: <?= $inv[$billid]->id; ?>},
                                dataType: "json",
                                success: function (data) {
                                    bootbox.alert({message: data.msg, size: 'small'});
                                },
                                error: function () {
                                    bootbox.alert({message: '<?= lang('ajax_request_failed'); ?>', size: 'small'});
                                    return false;
                                }
                            });
                        }
                    }
                });
                return false;
            });
        });

        <?php
        if ($pos_settings[$billid]->remote_printing == 1) {
            ?>
            $(window).load(function () {
                 // PrintDiv(window());
                window.print();
                return false;
            });
            <?php
        }
        ?>
         // page-break-after: always;

/*var $print_header_space = '<?=$pos_settings->pre_printed_header?>mm';
var $print_footer_space = '<?=$pos_settings->print_footer_space?>mm';*/

var $print_header_space = '10';
var $print_footer_space = '10';

var pre_printed = '<?=$pos_settings->pre_printed_format?>';


function PrintDiv(data) {
                var mywindow = window.open('', 'sma_pos_print', 'height=500,width=300');
                var is_chrome = Boolean(mywindow.chrome);
                mywindow.document.write('<html><head><title>Print</title>');
                mywindow.document.write("<style type='text/css' media = 'print'>@page {margin: "+$print_header_space+" 5mm "+$print_footer_space+" 5mm;},html, body {border: 1px solid white;height: 99%;page-break-after: always;page-break-before: always;},{ div.page-break { page-break-after: always; } #wrapper {page-break-before: always;}}</style>");
                mywindow.document.write('<link rel="stylesheet" href="<?=$assets?>styles/helpers/bootstrap.min.css" type="text/css" />');
                mywindow.document.write('</head><body >');
                mywindow.document.write(data);
                mywindow.document.write('</body></html>');
               if (is_chrome) {
                 setTimeout(function() { // wait until all resources loaded 
                    mywindow.document.close(); // necessary for IE >= 10
                    mywindow.focus(); // necessary for IE >= 10
                    mywindow.print(); // change window to winPrint
                    mywindow.close(); // change window to winPrint
                 }, 250);
               } else {
                    mywindow.document.close(); // necessary for IE >= 10
                    mywindow.focus(); // necessary for IE >= 10

                    mywindow.print();
                    mywindow.close();
               }

                return true;
            }

  $('#reprinter').click(function () {    
            var from_date = localStorage.getItem('reprint_from_date') ? localStorage.getItem('reprint_from_date') : "<?php echo date('Y-m-d'); ?>";
            type = localStorage.getItem('reprint_type') ? localStorage.getItem('reprint_type') : 0;            
            bill_no = '';
            var url = '<?php echo  admin_url('pos/reprinter') ?>';
            window.location.href= url +'/?date='+from_date+'&bill_no='+bill_no+'&type='+type;
    });

    </script>
    <?php /* include FCPATH.'themes'.DIRECTORY_SEPARATOR.$Settings->theme.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'pos'.DIRECTORY_SEPARATOR.'remote_printing.php'; */ ?>
    <?php include 'remote_printing.php'; ?>
    <?php
    if($modal) {
        ?>
    </div>
</div>
</div>
<?php
} else {
    ?>
</body>
</html>
<?php
}
?>