<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php $currency = $this->site->getAllCurrencies();
?>

<?php if (!empty($modal)) { ?>
<div class="modal-dialog no-modal-header" role="document"><div class="modal-content"><div class="modal-body">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i></button>
    <?php
} else {
    ?><!doctype html>
    <html>
    <head>
        <meta charset="utf-8">
        <title><?=$page_title . " " . lang("no") . " " . $inv->id;?></title>
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
           margin: <?=$pos_settings->pre_printed_header?>mm 5mm <?=$pos_settings->print_footer_space?>mm 5mm; 
        } 
       /* @media print {
            body, html {
            margin-top:0px!important;
            padding-top:0px!important;
            }
        }*/
            body { color: #000; }
            #wrapper { max-width: 480px; margin: 0 auto; padding-top: 20px; }
            .btn { border-radius: 0; margin-bottom: 5px; }
            .bootbox .modal-footer { border-top: 0; text-align: center; }            
            h3{ margin: 5px 0;font-size: 18px; }
            h1{ margin: 5px 0;font-size: 22px;font-weight: bold;}
            h4{ margin: 8px 0;font-size:14px;font-weight: bold;}
            .order_barcodes img { float: none !important; margin-top: 5px; }
			.hr_line{border: 1px solid #333;margin: 5px 0px;}
			/*04-07-2019*/
			 table{border: none!important;}
			.table{margin-bottom: 0px!important;}
			.table>thead>tr>th, .table>tbody>tr>th, .table>tfoot>tr>th, .table>thead>tr>td, .table>tbody>tr>td, .table>tfoot>tr>td{padding: 2px;}
			.table>tbody>tr>td{text-align: left;}
			.table>tbody>tr>td:last-child,.table>thead>tr>th:last-child{text-align: right;}
			/*04-07-2019*/
            @media print {
                .no-print { display: none; 
                  page-break-inside: avoid !important;

                  /*page-break-before: always; */
                }
                #wrapper { max-width: 480px; width: 100%; min-width: 250px; margin: 0 auto;page-break-inside: avoid !important; }
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
    <div id="wrapper" style="margin-top: -20px!important;">
        <div id="receiptData">
            <div class="no-print">
                <?php
                if ($message) {
                    ?>
                    <div class="alert alert-success">
                        <button data-dismiss="alert" class="close" type="button">×</button>
                        <?=is_array($message) ? print_r($message, true) : $message;?>
                    </div>
                    <?php
                } ?>
            </div>
            <div id="receipt-datareceipt-data">
                <div class="text-center">
                     <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td>
                    <table width="100%" cellpadding="0" cellspacing="0">
                        <tr>

                            <td align="center">
                              <div style="position: relative;float: left;width: 100%;display: block;">
                              	<img src="<?=$assets?>/images/logo_sugar_palm.jpg" alt="" width="76px;" style="position: absolute;left: -17px;top: 5px;">
                              </div>
                               
                                <?php if($pos_settings->print_local_language == 1) : ?>
                                     <h1 style="text-transform:uppercase;"><?=$biller->local_lang_name;?></h1>
                                <?php endif; ?>
                                <!-- <h1>ដឹស៊ូហ្គឺផាមរែសសត៊ូែ៉ង់រែន បា</h1> -->
                                <h3 style="text-transform:uppercase;"><?=$biller->company != '-' ? $biller->company : $biller->name;?></h3>
                                <?php if($biller->vat_no != '') : ?>
                                <h4><?= lang("native_vattin_no") ?>(<?=lang("VATTIN")?>): <?=$biller->vat_no?></h4>
                            <?php endif; ?>
                                <?php if($pos_settings->print_local_language == 1) :   ?>
                                    <h4><?= $biller->local_lang_address; ?></h4>
                                 <?php endif; ?>   
                                <!-- <h4>អាសយដ្ឋា ន៖ផ្ល៊ូវ២៧ ភ៊ូមិវត្តប៊ូព៌ សង្កា ត្់សាលាកំលែើក ក្កុង លខត្តលសៀមរាប</h4> -->
                                <h4><?=  $biller->address . " " . $biller->city . " " . $biller->postal_code . " " . $biller->state . " " . $biller->country ?></h4>
                                <h4><?= lang("native_telephone") ?> <?= $biller->phone; ?></h4>
                                <!-- <h4>Street 27, Wat Bo Village, Sangkat Sala Komreuk, Krong Siem Reap</h4>
                                <h4>ទ៊ូែស័ពទលេខ៖ 063 63 62 060, 012 81 81 43</h4> -->
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <hr class="hr_line">
                            </td>
                        </tr>
                    </table>
                </td>
                
            </tr>
             <?php if($pos_settings->reprint_bill_caption) { ?> 
            <tr>
                <td>
                    <table width="100%" cellpadding="0" cellspacing="0">
                        <tr>
                            <td>
                                <h3 align="center" ><?= lang("native_invoice") ?><br><?= lang("invoice") ?></h3>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr><?php } ?>
            <tr>
                <td>
                    <table width="100%" cellpadding="0" cellspacing="0">
                        <tr>
                            <td width="55%">
                                <table width="100%" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td align="left"><?= lang("native_no_of_guests") ?>:<?= $inv->seats; ?><br><?= lang("no_of_guests") ?>:<?= $inv->seats; ?></td>
                                        <!-- <td align="left"><?= $inv->seats; ?></td> -->
                                    </tr>
                                    <tr>
                                        <td align="left"><?= lang("native_customer") ?>:<?= ($customer->company && $customer->company != '-' ? $customer->company : $customer->name) ?><br><?= lang("customer") ?>:<?= ($customer->company && $customer->company != '-' ? $customer->company : $customer->name) ?></td>
                                        <!-- <td align="left"></td> -->
                                    </tr>
                                </table>
                            </td>
                            <?php  
                               if($this->Settings->time_format == 12){
                                    $date = new DateTime($inv->created_on);
                                    $created_on = $date->format('Y-m-d h:iA');
                                    }else{
                                    $created_on =  $inv->created_on;
                                }
                            ?>
                            <td width="45%">
                                <table width="100%" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td align="left"><?= lang("native_invoice_no") ?>:<?= $inv->bill_number; ?><br><?= lang("invoice_no")?>:<?= $inv->bill_number; ?></td>
                                        <!-- <td><?= $inv->bill_number; ?></td> -->
                                    </tr>
                                    <tr>
                                        <td align="left"><?= lang("native_date") ?>:<?= $created_on; ?><br><?= lang("date") ?>:<?= $created_on; ?></td>
                                        <!-- <td><?= $created_on; ?></td> -->
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <table class="table" width="100%" style="margin-top: 5px;border: 1px solid #ccc;padding: 5px;margin-bottom:0px!important;">
                    <colgroup>
                        <col width="10%">
                        <col width="35%">
                        <col width="15%">
                        <col width="20%">
                        <col width="20%">
                    </colgroup>
                        <thead>
                            <tr>
                                <?= lang("native_no");?>
                                <th><?= lang("native_no") ?><br><?= lang("no") ?></th>
                                <th colspan="2"><?= lang("native_description") ?><br><?= lang("description") ?></th>
                                <th><?= lang("native_qty") ?><br><?= lang("qty") ?></th>
                                <th><?= lang("native_price") ?><br><?= lang("price") ?></th>
                                <th><?= lang("native_amount") ?><br><?= lang("amount") ?></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php   
                        $sno =0;  
                       foreach ($billi_tems as $bill) {   
                       $sno++;                         
                            $recipe_variant='';
                            if($this->Settings->user_language == 'khmer'){
                                if(!empty($bill->khmer_name)){
                                    $recipe_name = $bill->khmer_name;
                                }else{
                                    $recipe_name = $bill->recipe_name;
                                }
                                $native_variant = $this->site->getrecipevariantKhmer($bill->recipe_variant_id);

                                if($native_variant!='' || $native_variant!=0){    
                                    $recipe_variant = ' - '.$native_variant;
                                 }   

                            }else{
                                $recipe_name = $bill->recipe_name;
                                if($bill->recipe_variant!='' || $bill->recipe_variant!=0){                                
                                  $recipe_variant = ' - '.$bill->recipe_variant;
                                }
                            }

                            $star ='';                            
                            if ($inv->total_discount != 0 && !empty($inv->customer_discount_id)) {
                                if($this->settings->customer_discount != 'none'){
                                  if($this->settings->customer_discount == 'customer'){                                
                                        $check = $this->site->Check_item_Discount_customer($bill->recipe_id,$inv->customer_discount_id);
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
                         echo '<tr><td class="no-border text-center">'.$sno.'</td><td colspan="2" class="no-border"><span style="display: table-cell;text-align: -webkit-match-parent;text-decoration:'.$underline.'">' . ($star)  . '' . ($recipe_name) . ($recipe_variant? '  ' . $recipe_variant . '' : '') . ' </span>';                            
                            $addondetails = $this->site->getAddonByRecipeidAndBillitemid($bill->recipe_id,$bill->id);
                            $itemaddonamt =0;
                             if(!empty($addondetails)) :
                                 $i =1;
                                 foreach ($addondetails as $key => $addons) { 
                                    $i++;
                                    if($i > 1) {
                                        $br ='<br>';
                                    }
                                     $itemaddonamt += $addons->price*$addons->qty;
                                    $addqty = (int)$addons->qty;
                                    echo  '<span style="color: red;font-weight: bold;"> '.$addons->addon_name.'('.$addqty.'X'.$addons->price.') = '. $this->sma->formatMoney($addons->price*$addons->qty).'</span>'.$br.'';
                                } 
                             endif;
                            echo '</td>';
                            echo '<td class="no-border text-center">'.$bill->quantity.'</td>';
                            echo '<td  class="no-border">'.$this->sma->formatMoney($bill->net_unit_price).'';
                            if($itemaddonamt != 0) : 
                            echo '<br><span style="color: red;text-align: right;">'.$this->sma->formatMoney($itemaddonamt).'</span>';
                             endif;
                            echo '</td>';                           
                            $dis_v=0;
                            if($inv->discount_val  && $bill->input_discount!=0){
                                $dis_v = str_replace('Discount ','',$inv->discount_val);              
                            }
                            // echo '<td class="no-border text-center">'.$dis_v.'</td>';
                            if($inv->manual_item_discount != 0){
                                if ($pos_settings->manual_item_discount_display_option == 1){
                                echo '<td class="no-border text-right">'.$bill->manual_item_discount_per_val.'</td>';
                                }else{
                                    echo '<td class="no-border">'.$this->sma->formatMoney($bill->manual_item_discount).'</td>';
                                }
                            }                            
                            echo '<td " class="no-border text-right">'.$this->sma->formatMoney($bill->subtotal-$bill->manual_item_discount-$bill->input_discount).'</td></tr>';
                            $r++;
                        } ?>

                        </tbody>
                    </table>
                </td>
            </tr>
        </table>                   
                </div>                
                <table class="table table-striped table-condensed 44" style="font-size:14px!important;";>
                    <tfoot>
                        <tr>
                            <th colspan="<?php echo $cols; ?>" class="text-right"><?=lang("total");?></th>
                            <th class="text-right"><?=$this->sma->formatMoney($inv->total-$inv->manual_item_discount);?></th>
                        </tr>
                        <?php                        
                        $total_dis_without_manual =  ($inv->total_discount);

                        if ($total_dis_without_manual != 0) {
                            if($inv->discount_type == 'manual'){
                                 if($inv->discount_val){
                                    $disname = $inv->discount_val;
                                }else{
                                    $disname = 'Discount';
                                }
                                echo '<tr><th colspan="'.$cols.'" class="text-right">' . lang("discount") . '('.$disname.')</th><th class="text-right">' . $this->sma->formatMoney($total_dis_without_manual) . '</th></tr>';
                            } else {
                                if($discounnames){
                                    $disname = $discounnames;
                                }else{
                                    $disname = 'Discount';
                                }

                           echo '<tr><th colspan="'.$cols.'" class="text-right">' .$disname. '</th><th class="text-right">' . $this->sma->formatMoney($total_dis_without_manual) . '</th></tr>';
                            }
                        }
                        if ($inv->birthday_discount != 0) {
                            $dpos = strpos($this->pos_settings->birthday_discount, '%');
                             if ($dpos !== false) {
                              echo '<tr><th colspan="'.$cols.'" class="text-right">' . lang("birthday_discount") .'(' . $this->pos_settings->birthday_discount . ')'.'</th><th class="text-right">' . $this->sma->formatMoney($inv->birthday_discount) . '</th></tr>';
                            }
                            else{
                                 echo '<tr><th colspan="'.$cols.'" class="text-right">' . lang("birthday_discount") . '</th><th class="text-right">' . $this->sma->formatMoney($inv->birthday_discount) . '</th></tr>';
                            }
                        }     
                        if ($inv->service_charge_amount != 0) {
                            $taxcol = "4";
                            if($inv->manual_item_discount != 0)
                            {
                                $taxcol = "5";
                            }
                            $ServiceCharge = $this->site->getServiceChargeByID($inv->service_charge_id);

                             echo '<tr><th colspan="'.$taxcol.'" class="text-right">' . $ServiceCharge->name. '</th>';
                                 echo '<th colspan="2" class="text-right">' . lang($this->sma->formatMoney($inv->service_charge_amount)."") . '</th></tr>';
                        }                                                                   
                        if ($inv->tax_type == 0)
                            {
                                $taxname = 'Inclusive';
                                $grandtotal = $inv->total-$inv->total_discount-$inv->birthday_discount+$inv->service_charge_amount;
                            }
                            else
                            {
                                $taxname = 'Exclusive';
                                $grandtotal = $inv->total-$inv->total_discount-$inv->birthday_discount+$inv->total_tax+$inv->service_charge_amount;
                            }

                        if ($pos_settings->display_tax==1 && $inv->tax_rate != 0) {
                            if( $pos_settings->tax_caption != ''){
                                echo '<tr><th colspan="'.$cols.'" class="text-right">' . $pos_settings->tax_caption. '</th>';
                            }
                            if ($pos_settings->display_tax_amt==1 && $inv->tax_rate != 0) {
                                 echo '<th colspan="2" class="text-right">' . lang($this->sma->formatMoney($inv->total_tax)."") . '</th>';
                              }
                              echo '</tr>';
                        }      

                        if ($pos_settings->rounding || $inv->rounding != 0) {
                            $n = $grandtotal;//1.25
                                $whole = floor($n); // 1
                                $riel = $n - $whole; // .25
                                $exchange_rate = $this->site->getExchangeRatey($this->Settings->default_currency);
                            ?>

                           <?php if ($pos_settings->print_option == 1) {  
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
                                    <th class="text-right"><?=$this->sma->formatMoney(($return_sale ? ($inv->grand_total+$return_sale->grand_total) : $inv->grand_total) / $currency_row->rate,$currency_row->symbol);?></th>
                                </tr>
                             <?php  } ?> 
                            <?php }
                        if ($inv->paid < ($inv->grand_total + $inv->rounding)) {
                            ?>                           
                            <?php
                        } ?>
                        <tr>
                        <?php
                             $taxcol = "3";
                            if($inv->manual_item_discount != 0)
                            {
                                $taxcol = "4";
                            }                            
                        if ($pos_settings->discount_note_display_option==1) {       
                           if ($inv->total_discount != 0) {
                             if($this->settings->customer_discount != 'none'){
                                  echo '<tr><th colspan="5" class="text-left"><small>* Bill Discount is not applied to these items</small></th></tr>';
                                }
                                if($inv->manual_item_discount != 0){
                                    echo '<tr><th colspan="5" class="text-left"><small>Underlined Items are manually Discount is applied.</small></th></tr>';
                                }
                            } 
                         }    
                        ?>
                        </tr>
                    </tfoot>
                </table>
                <?php              
                if ($payments) {
                    echo '<table class="table table-striped table-condensed" ><tbody>';
                    foreach ($payments as $payment) {
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
                
                ?>
                
              <?php  if ($pos_settings->print_option == 1) { ?>
                <table class="table table-striped table-condensed" >
                    <!-- <tfoot>
                        <?php                        
                            $n = $inv->balance;//1.25
                            $whole = floor($n); // 1
                            $riel = $n - $whole; // .25
                            $exchange_rate = $this->site->getExchangeRatey($this->Settings->default_currency);
                        foreach($currency as $currency_row){
                        ?>
                        <tr>
                            <th class="text-right"><?=lang("change ".$currency_row->code."");?></th>
                            <th class="text-right"><?=$this->sma->formatMoney($inv->balance / $currency_row->rate,$currency_row->symbol);?>
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
                    </tfoot> -->
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
                            <th class="text-right"><?=$this->sma->formatMoney($inv->balance / $currency_row->rate,$currency_row->symbol);?></th>
                        </tr>
                        <?php
                        }
                        ?>
                        
                    </tfoot>
                </table>
                <?php } ?>
                

                <?= $Settings->invoice_view > 0 ? $this->gst->summary($rows, $return_rows, ($return_sale ? $inv->product_tax+$return_sale->product_tax : $inv->product_tax)) : ''; ?>

                <?= $customer->award_points != 0 && $Settings->each_spent > 0 ? '<p class="text-center">'.lang('this_sale').': '.floor(($inv->grand_total/$Settings->each_spent)*$Settings->ca_point)
                .'<br>'.
                lang('total').' '.lang('award_points').': '. $customer->award_points . '</p>' : ''; ?>
                <?= $inv->note ? '<p class="text-center">' . $this->sma->decode_html($inv->note) . '</p>' : ''; ?>
                <?= $payment->staff_note ? '<p class="no-print"><strong>' . lang('staff_note') . ':</strong> ' . $this->sma->decode_html($payment->staff_note) . '</p>' : ''; ?>
                <?= $biller->invoice_footer ? '<p class="text-center" >'.$this->sma->decode_html($biller->invoice_footer).'</p>' : ''; ?>
            </div>

            <div class="order_barcodes text-center">
                <?php if($this->Settings->enable_barcode) : ?>
                <img src="<?= admin_url('misc/barcode/'.$this->sma->base64url_encode($inv->reference_no).'/code128/74/0/1'); ?>" alt="<?= $inv->reference_no; ?>" class="bcimg" />
                <?php endif; ?>
                <?php if($this->Settings->enable_qrcode) : ?>
                    <br>
                    <?= $this->sma->qrcode('link', urlencode($inv->reference_no), 2); ?>
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
            if ($message) {
                ?>
                <div class="alert alert-success">
                    <button data-dismiss="alert" class="close" type="button">×</button>
                    <?=is_array($message) ? print_r($message, true) : $message;?>
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
                    <a class="btn btn-block btn-warning" id="reprinter"  href="javascript:void(0)"><?= lang("back_to_reprint"); ?></a>
                    <!-- <a class="btn btn-block btn-warning" href="<?= admin_url('pos/reprinter'); ?>"><?= lang("back_to_reprint"); ?></a> -->
                </span>
                <?php
            }
            if ($pos_settings->remote_printing == 1) {
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
        
        $(document).ready(function () {
            $('#email').click(function () {
                bootbox.prompt({
                    title: "<?= lang("email_address"); ?>",
                    inputType: 'email',
                    value: "<?= $customer->email; ?>",
                    callback: function (email) {
                        if (email != null) {
                            $.ajax({
                                type: "post",
                                url: "<?= admin_url('pos/email_receipt') ?>",
                                data: {<?= $this->security->get_csrf_token_name(); ?>: "<?= $this->security->get_csrf_hash(); ?>", email: email, id: <?= $inv->id; ?>},
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
        if ($pos_settings->remote_printing == 1) {
            ?>
            $(window).load(function () {
                
                window.print();
                return false;
            });
            <?php
        }
        ?>

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