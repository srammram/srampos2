<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
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
         <style type="text/css" media="all">
             @page  
        { 
            size: auto;   /* auto is the initial value */ 
            /* this affects the margin in the printer settings */ 
           margin: <?=$pos_settings->pre_printed_header?>mm 5mm <?=$pos_settings->print_footer_space?>mm 5mm; 
        } 
            body { color: #000; }
            #wrapper { max-width: 480px; margin: 0 auto; padding-top: 20px; }
            .btn { border-radius: 0; margin-bottom: 5px; }
            .bootbox .modal-footer { border-top: 0; text-align: center; }
            h3 { margin: 5px 0; }
            .order_barcodes img { float: none !important; margin-top: 5px; }
            @media print {
                .no-print { display: none; 
                  page-break-inside: avoid !important;
                  /*page-break-before: always; */
                }
                #wrapper { max-width: 480px; width: 100%; min-width: 250px; margin: 0 auto;page-break-inside: avoid !important; }
                .no-border { border: none !important; }
                .border-bottom { border-bottom: 1px solid #ddd !important; }
                table tfoot { display: table-row-group; }
            }
        </style>
        <?php if(isset($socket_tableid) && isset($_SERVER['HTTP_REFERER'])):$tableid =$socket_tableid;?>
            <script type="text/javascript" src="<?=$assets?>js/jquery-2.0.3.min.js"></script>
            <script>var curr_page="view_bbqbill";var curr_func="update_bbqtables";var tableid = '<?=$tableid?>';</script>
            <script src="<?=base_url('node_modules/socket.io/node_modules/socket.io-client/socket.io.js')?>"></script>
            <script type="text/javascript" src="<?=$assets?>js/socket/socket_configuration.js"></script>
            <script type="text/javascript" src="<?=$assets?>js/socket/client.js"></script>
        <?php endif; ?>
    </head>

    <body>
        <?php
    } ?>
    <div id="wrapper">
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
                    <?php if(!$pos_settings->pre_printed_format) { ?> 
                    <?= !empty($biller->logo) ? '<img src="'.base_url('assets/uploads/logos/'.$biller->logo).'" alt="">' : ''; ?>
                    <h3 style="text-transform:uppercase;font-weight: bold;"><?=$biller->company != '-' ? $biller->company : $biller->name;?></h3>
                    <?php
                    echo "<h4 style='font-weight:bold'>" . $biller->address . " " . $biller->city . " " . $biller->postal_code . " " . $biller->state . " " . $biller->country .
                    "<br>" . lang("tel") . ": " . $biller->phone; ?>
<?php } ?>
                    <?php if($pos_settings->reprint_bill_caption) { ?> 
                    <div class="col-sm-12 text-center">
                        <h4 style="font-weight:bold;"><?=lang('copy_bill');?></h4>
                    </div>
                    <?php } ?>

                    <?php 

                    // comment or remove these extra info if you don't need
                    if (!empty($biller->cf1) && $biller->cf1 != "-") {
                        echo "<br>" . lang("bcf1") . ": " . $biller->cf1;
                    }
                    if (!empty($biller->cf2) && $biller->cf2 != "-") {
                        echo "<br>" . lang("bcf2") . ": " . $biller->cf2;
                    }
                    if (!empty($biller->cf3) && $biller->cf3 != "-") {
                        echo "<br>" . lang("bcf3") . ": " . $biller->cf3;
                    }
                    if (!empty($biller->cf4) && $biller->cf4 != "-") {
                        echo "<br>" . lang("bcf4") . ": " . $biller->cf4;
                    }
                    if (!empty($biller->cf5) && $biller->cf5 != "-") {
                        echo "<br>" . lang("bcf5") . ": " . $biller->cf5;
                    }
                    if (!empty($biller->cf6) && $biller->cf6 != "-") {
                        echo "<br>" . lang("bcf6") . ": " . $biller->cf6;
                    }
                    // end of the customer fields

                    echo "<br>";
                    if ($pos_settings->cf_title1 != "" && $pos_settings->cf_value1 != "") {
                        echo $pos_settings->cf_title1 . ": " . $pos_settings->cf_value1 . "<br>";
                    }
                    if ($pos_settings->cf_title2 != "" && $pos_settings->cf_value2 != "") {
                        echo $pos_settings->cf_title2 . ": " . $pos_settings->cf_value2 . "<br>";
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
                    $date = new DateTime($inv->created_on);
                    $created_on = $date->format('Y-m-d h:iA');
                    }else{
                        $created_on =  $inv->created_on;
                }
                echo "<p>" ."<span style='font-size:15px;font-weight:bold'>".lang("bill_no") . ": " . $inv->bill_number . "</span><br>";
                echo lang("date") . ": " . $created_on . "<br>";
                echo lang("sale_no_ref") . ": " . $inv->reference_no . "<br>";
                if (!empty($inv->return_sale_ref)) {
                    echo '<p>'.lang("return_ref").': '.$inv->return_sale_ref;
                    if ($inv->return_id) {
                        echo ' <a data-target="#myModal2" data-toggle="modal" href="'.admin_url('sales/modal_view/'.$inv->return_id).'"><i class="fa fa-external-link no-print"></i></a><br>';
                    } else {
                        echo '</p>';
                    }
                }

                echo lang("customer") . ": " . ($customer->company && $customer->company != '-' ? $customer->company : $customer->name) . "<br>";
                 echo lang("sales_person") . ": " . $created_by->first_name." ".$created_by->last_name . "</br>";
                 echo lang("cashier") . ": " . $cashier->cashier;
                echo "</br>";
                if($tableno->order_type == 'BBQ'){
                    echo "Table :";echo $tableno->table_name . "<br>";
                }
                
                
                
                echo "</p>";
                ?>

                <div style="clear:both;"></div>
                <table class="table table-striped table-condensed">
                    <thead>
                        <th colspan="1"><?=lang("details");?></th>
                        <th><?=lang("price");?></th>
                        <th><?=lang("no_of_cover");?></th>
                        <!--  <th><?=lang("discount_cover");?></th> -->
                        <th class="text-right"><?=lang("sub_total");?></th>
                    </thead>
                    <tbody>
                        <?php
                        $r = 1; $category = 0;
                        $tax_summary = array();
                        
                        foreach ($billi_tems as $bill) {
                            
                                                    
                            echo '<tr><td colspan="1" class="no-border">' . $r . ': &nbsp;&nbsp;' .$bill->type . '</span></td>'; 

                            echo '<td  class="no-border">'.$this->sma->formatMoney($bill->price).'</td>';
                            echo '<td class="no-border text-center">'.$bill->cover.'</td>';
                            // echo '<td class="no-border">'.$bill->discount_cover.'</td>';
                            
                            echo '<td " class="no-border text-right">'.$this->sma->formatMoney($bill->price*$bill->cover).'</td></tr>';
                            $r++;
                        }
                        
                        ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" class="text-right"><?=lang("total");?></th>
                            <th class="text-right"><?=$this->sma->formatMoney($inv->total);?></th>
                        </tr>
                        
                          <?php if($inv->total_discount != 0) { ?>
                           <tr>
                                <!-- <th colspan="3" class="text-right"><?=lang("discount");?></th> -->
                                <?php if($discount !='') {?>
                                <th colspan="3" class="text-right"><?= $discount; ?></th>
                            <?php }else{ ?>
                                <th colspan="3" class="text-right"><?=lang("discount");?></th>
                            <?php } ?>
                                <th class="text-right"><?=$this->sma->formatMoney($inv->total_discount);?></th>
                            </tr>
                        <?php } ?>

                        <?php if($inv->bbq_cover_discount != 0) { ?>
                           <tr>
                                <!-- <th colspan="3" class="text-right"><?=lang("discount_cover");?>(<?= $DiscountCovers; ?>)</th> -->
                                <th colspan="3" class="text-right"><?=lang("cover_discount");?></th>
                                <th class="text-right"><?=$this->sma->formatMoney($inv->bbq_cover_discount);?></th>
                            </tr>
                        <?php } ?>
                        <?php if($inv->bbq_daywise_discount != 0) { ?>
                           <tr>                                
                                <th colspan="3" class="text-right"><?=lang("bbq_daywise_discount");?></th>
                                <th class="text-right"><?=$this->sma->formatMoney($inv->bbq_daywise_discount);?></th>
                            </tr>
                        <?php } ?>

                        <?php 
                        if($inv->birthday_discount != 0) {                  
                            echo '<tr><th colspan="3" class="text-right">' . lang("birthday_discount") .'(' . $this->pos_settings->birthday_discount . ')'.'</th><th class="text-right">' . $this->sma->formatMoney($inv->birthday_discount) . '</th></tr>';
                        } ?>

                            <?php
                            if($inv->service_charge_amount !=0)
                            {  $ServiceCharge = $this->site->getServiceChargeByID($inv->service_charge_id); ?>
                             <tr>
                               <th colspan="3" class="text-right"><?= $ServiceCharge->name;?></th>
                                <th class="text-right"><?=$this->sma->formatMoney($inv->service_charge_amount);?></th>
                            </tr>
                           <?php  }

                             if ($inv->tax_type == 0)
                            {
                                $taxname = 'Inclusive';
                                $grandtotal = $inv->total-$inv->total_discount-$inv->bbq_daywise_discount+$inv->service_charge_amounts;
                            }
                            else
                            {
                                $taxname = 'Exclusive';
                                $grandtotal = $inv->total-$inv->total_discount-$inv->bbq_daywise_discount+$inv->total_tax+$inv->service_charge_amount;
                            }
                            ?>
                            <tr>
                                <th colspan="3" class="text-right"><?=lang("grand_total");?></th>
                                <th class="text-right"><?=$this->sma->formatMoney($grandtotal);?></th>
                            </tr>
                            <?php
                      
                       // if ($inv->paid < ($inv->grand_total + $inv->rounding)) {
                            ?>
                            <!--<tr>
                                <th colspan="4" class="text-right"><?=lang("paid_amount");?></th>
                                <th class="text-right"><?=$this->sma->formatMoney($return_sale ? ($inv->paid+$return_sale->paid) : $inv->paid);?></th>
                            </tr>
                            <tr>
                                <th colspan="4" class="text-right"><?=lang("due_amount");?></th>
                                <th class="text-right"><?=$this->sma->formatMoney(($return_sale ? (($inv->grand_total + $inv->rounding)+$return_sale->grand_total) : ($inv->grand_total + $inv->rounding)) - ($return_sale ? ($inv->paid+$return_sale->paid) : $inv->paid));?></th>
                            </tr>-->
                            <?php
                       // } ?>
                        <tr>
                        <?php
                        //if ($pos_settings->display_tax==1 && $inv->tax_rate != 0) {
                            //echo '<tr><th colspan="4" class="text-right">' . lang("tax_inclusive (".$inv->tax_name.")") . '</th><th class="text-right">' . $this->sma->formatMoney($return_sale ? ($inv->order_tax+$return_sale->order_tax) : $inv->total_tax) . '</th></tr>';
                            echo '<tr><th colspan="5" class="text-left">*<small>' . lang("tax ".$taxname." (".$inv->tax_name.")") . '</small></th></tr>';
                        //}
                        ?>
                        </tr>
                    </tfoot>
                </table>
                <?php
                if ($payments) {
                    echo '<table class="table table-striped table-condensed"><tbody>';
                    /*echo "<pre>";
                    print_r($payments);die;*/
                    foreach ($payments as $payment) {
                        echo '<tr>';
                        if (($payment->paid_by == 'cash' || $payment->paid_by == 'credit' || $payment->paid_by == 'deposit') && $payment->pos_paid) {
                            echo '<td>' . lang("paid_by") . ': ' . lang($payment->paid_by) . '</td>';
                            echo '<td>' . lang("amount") . ': ' . $this->sma->formatMoney($payment->pos_paid == 0 ? $payment->amount : $payment->pos_paid) . ($payment->return_id ? ' (' . lang('returned') . ')' : '') . '</td>';

                             $exchange_curr_code = $this->site->getExchangeCurrency($this->Settings->default_currency);

                             echo '<td>' . lang("foreign_exchange") . ': ' . $this->sma->formatMoney($payment->amount_exchange,$exchange_curr_code) . '</td>';

                        } elseif (($payment->paid_by == 'loyalty')) {      
                            echo '<td>' . lang("paid_by") . ': ' . lang($payment->paid_by) . '</td>';
                            echo '<td>' . lang("amount") . ': ' . $this->sma->formatMoney($payment->pos_paid == 0 ? $payment->amount : $payment->pos_paid) . ($payment->return_id ? ' (' . lang('returned') . ')' : '') . '</td>';
                            
                        } elseif (($payment->paid_by == 'CC' || $payment->paid_by == 'ppp' || $payment->paid_by == 'stripe')) {//&& $payment->cc_no
                            
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

                if ($return_payments) {
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
                }
                ?>
                
                
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
                

                <?= $Settings->invoice_view > 0 ? $this->gst->summary($rows, $return_rows, ($return_sale ? $inv->product_tax+$return_sale->product_tax : $inv->product_tax)) : ''; ?>

                <?= $customer->award_points != 0 && $Settings->each_spent > 0 ? '<p class="text-center">'.lang('this_sale').': '.floor(($inv->grand_total/$Settings->each_spent)*$Settings->ca_point)
                .'<br>'.
                lang('total').' '.lang('award_points').': '. $customer->award_points . '</p>' : ''; ?>
                <?= $inv->note ? '<p class="text-center">' . $this->sma->decode_html($inv->note) . '</p>' : ''; ?>
                <?= $payment->staff_note ? '<p class="no-print"><strong>' . lang('staff_note') . ':</strong> ' . $this->sma->decode_html($payment->staff_note) . '</p>' : ''; ?>
                <?= $biller->invoice_footer ? '<p class="text-center">'.$this->sma->decode_html($biller->invoice_footer).'</p>' : ''; ?>
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
                    <a class="btn btn-block btn-warning" href="<?= admin_url('pos/bbqreprinter/'); ?>"><?= lang("back_to_BBQ_Reprint"); ?></a>
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