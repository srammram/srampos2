<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

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
                    <h3 style="text-transform:uppercase;"><?=$biller->company != '-' ? $biller->company : $biller->name;?></h3>
                    <?php
                    echo "<h4>" . $biller->address . " " . $biller->city . " " . $biller->postal_code . " " . $biller->state . " " . $biller->country .
                    "<br>" . lang("tel") . ": " . $biller->phone;
			}
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
                      <?php if($pos_settings->reprint_bill_caption) { ?> 
                    <div class="col-sm-12 text-center">
                        <h3 style="font-weight:bold;"><?=lang('Invoice');?></h3>
                    </div>
                    <?php } ?>

                </div>
                <?php
                if ($Settings->invoice_view == 1 || $Settings->indian_gst) {
                    ?>
                    <div class="col-sm-12 text-center">
                        <h4 style="font-weight:bold;"><?=lang('tax_invoice');?></h4>
                    </div>
                    <?php
                }
                // echo "<p>" .lang("bill_no") . ": " . $dine['inv']->bill_number . "<br>";
                echo lang("date") . ": " . $this->sma->hrld($dine['inv']->date) . "<br>";
                echo lang("sale_no_ref") . ": " . $dine['reference_no'] . "<br>";
                if (!empty($dine['inv']->return_sale_ref)) {
                    echo '<p>'.lang("return_ref").': '.$dine['inv']->return_sale_ref;
                    if ($dine['inv']->return_id) {
                        echo ' <a data-target="#myModal2" data-toggle="modal" href="'.admin_url('sales/modal_view/'.$dine['inv']->return_id).'"><i class="fa fa-external-link no-print"></i></a><br>';
                    } else {
                        echo '</p>';
                    }
                }

                echo lang("customer") . ": " . ($customer->company && $customer->company != '-' ? $customer->company : $customer->name) . "<br>";
                 echo lang("sales_person") . ": " . $dine['created_by']->first_name." ". $dine['created_by']->last_name. "<br>";
                 echo lang("cashier") . ": " . $dine['cashier']->first_name." ". $dine['cashier']->last_name ;				 
                echo "<p>";
				
                if($dine['tableno']->order_type == 'BBQ'){
                    echo "Table :";echo $dine['tableno']->table_name . "<br>";
                }
                
				if(!empty($delivery_person)){
					echo 'Delivery Address <br>';
					
					
                    echo $customer->address . "<br>";
                    echo $customer->city ." ".$customer->state." ".$customer->country ."<br>";
					echo lang("tel") . ": " . $customer->phone . "<br><br>";
					echo "Delivery Person : " .$delivery_person->first_name.' '.$delivery_person->last_name.' ('.$delivery_person->user_number.')';
					echo "<br>Phone : ".$delivery_person->phone ;
				}
                /*if ($pos_settings->customer_details) {
                    if ($customer->vat_no != "-" && $customer->vat_no != "") {
                        echo "<br>" . lang("vat_no") . ": " . $customer->vat_no;
                    }
                    echo lang("tel") . ": " . $customer->phone . "<br>";
                    echo lang("address") . ": " . $customer->address . "<br>";
                    echo $customer->city ." ".$customer->state." ".$customer->country ."<br>";
                    if (!empty($customer->cf1) && $customer->cf1 != "-") {
                        echo "<br>" . lang("ccf1") . ": " . $customer->cf1;
                    }
                    if (!empty($customer->cf2) && $customer->cf2 != "-") {
                        echo "<br>" . lang("ccf2") . ": " . $customer->cf2;
                    }
                    if (!empty($customer->cf3) && $customer->cf3 != "-") {
                        echo "<br>" . lang("ccf3") . ": " . $customer->cf3;
                    }
                    if (!empty($customer->cf4) && $customer->cf4 != "-") {
                        echo "<br>" . lang("ccf4") . ": " . $customer->cf4;
                    }
                    if (!empty($customer->cf5) && $customer->cf5 != "-") {
                        echo "<br>" . lang("ccf5") . ": " . $customer->cf5;
                    }
                    if (!empty($customer->cf6) && $customer->cf6 != "-") {
                        echo "<br>" . lang("ccf6") . ": " . $customer->cf6;
                    }
                }*/
                echo "</p>";
                ?>

                <div style="clear:both;"></div>
                <table class="table table-striped table-condensed">
                    <thead>
                        <th colspan="2"><?=lang("description");?></th>
                        <th><?=lang("price");?></th>
                        <th><?=lang("qty");?></th>
                        <th><?=lang("sub_total");?></th>
                    </thead>
                    <tbody>
                        <?php
                        $r = 1; $category = 0;
                        $tax_summary = array();
			if(count($dine['billi_tems'])) { 				
                        foreach ($dine['billi_tems'] as $bill) {
                            
                            /*if ($pos_settings->item_order == 0 && $category != $bill->recipe_id) {
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
							
                            echo '<tr><td colspan="2" class="no-border">' . $r . ': &nbsp;&nbsp;' . ($recipe_name) . ($bill->variant ? ' (' . $bill->variant . ')' : '') . '<span class="pull-right">' . ($bill->tax_code ? '*'.$bill->tax_code : '') . '</span></td>'; 

                            echo '<td  class="no-border">'.$this->sma->formatMoney($bill->net_unit_price).'</td>';
                            echo '<td class="no-border">'.$bill->quantity.'</td>';
                            
                            echo '<td " class="no-border text-right">'.$this->sma->formatMoney($bill->subtotal).'</td></tr>';
                            $r++;
                        }
			}
                        if ($return_rows) {
                            echo '<tr class="warning"><td colspan="100%" class="no-border"><strong>'.lang('returned_items').'</strong></td></tr>';
                            foreach ($return_rows as $row) {
                                if ($pos_settings->item_order == 1 && $category != $row->category_id) {
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
                            <th colspan="4" class="text-right"><?=lang("total");?></th>
                            <th class="text-right"><?=$this->sma->formatMoney($return_sale ? (($dine['inv']->total + $dine['inv']->recipe_tax)+($return_sale->total + $return_sale->recipe_tax)) : ($dine['inv']->total + $dine['inv']->recipe_tax));?></th>
                        </tr>
                        <?php
                        $dis = $this->site->getDiscountsAmt($dine['inv']->id);

                        
                        if($dine['inv']->total_discount > 0) {
                            echo '<tr><th colspan="4" class="text-right">' . $dine['discounnames']. '</th><th class="text-right">' . $this->sma->formatMoney($dine['inv']->total_discount) . '</th></tr>';
                        }
                        

						/*if ($dis != 0) {
							if($dine['inv']->discount_type == 'manual'){
								echo '<tr><th colspan="4" class="text-right">' . lang("discount") . '('.$dine['inv']->discount_val.')</th><th class="text-right">' . $this->sma->formatMoney($dis) . '</th></tr>';
							} else {
                            echo '<tr><th colspan="4" class="text-right">' . lang("discount") . '</th><th class="text-right">' . $this->sma->formatMoney($dis) . '</th></tr>';
							}
						}*/

                        if ($dine['inv']->shipping != 0) {
                            echo '<tr><th colspan="3">' . lang("shipping") . '</th><th class="text-right">' . $this->sma->formatMoney($dine['inv']->shipping) . '</th></tr>';
                        }

                        if ($return_sale) {
                            if ($return_sale->surcharge != 0) {
                                echo '<tr><th colspan="3">' . lang("order_discount") . '</th><th class="text-right">' . $this->sma->formatMoney($return_sale->surcharge) . '</th></tr>';
                            }
                        }

                        if ($Settings->indian_gst) {
                            if ($dine['inv']->cgst > 0) {
                                $cgst = $return_sale ? $dine['inv']->cgst + $return_sale->cgst : $dine['inv']->cgst;
                                echo '<tr><td colspan="5">' . lang('cgst') .'</td><td class="text-right">' . ( $Settings->format_gst ? $this->sma->formatMoney($cgst) : $cgst) . '</td></tr>';
                            }
                            if ($dine['inv']->sgst > 0) {
                                $sgst = $return_sale ? $dine['inv']->sgst + $return_sale->sgst : $dine['inv']->sgst;
                                echo '<tr><td colspan="5">' . lang('sgst') .'</td><td class="text-right">' . ( $Settings->format_gst ? $this->sma->formatMoney($sgst) : $sgst) . '</td></tr>';
                            }
                            if ($dine['inv']->igst > 0) {
                                $igst = $return_sale ? $dine['inv']->igst + $return_sale->igst : $dine['inv']->igst;
                                echo '<tr><td colspan="5">' . lang('igst') .'</td><td class="text-right">' . ( $Settings->format_gst ? $this->sma->formatMoney($igst) : $igst) . '</td></tr>';
                            }
                        }

                        if ($pos_settings->rounding || $dine['inv']->rounding != 0) {

                             if ($dine['inv']->tax_type == 0)
                            {
                                $taxname = 'Inclusive';
                                $grandtotal = $dine['inv']->total-$dine['inv']->total_discount;
                            }
                            else
                            {
                                $taxname = 'Exclusive';
                                $grandtotal = $dine['inv']->total-$dine['inv']->total_discount+$dine['inv']->total_tax;

                            }
                            ?>
                            <!--<tr>
                                <th colspan="4"><?=lang("rounding");?></th>
                                <th class="text-right"><?= $this->sma->formatMoney($dine['inv']->rounding);?></th>
                            </tr>-->
                            <tr>
                                <th colspan="4" class="text-right"><?=lang("grand_total");?></th>
                                <th class="text-right"><?=$this->sma->formatMoney($grandtotal);?></th>
                            </tr>
                            <?php
							$final_total[] = $grandtotal;
                        } else {

							$final_total[] = $return_sale ? ($dine['inv']->grand_total+$return_sale->grand_total) : $dine['inv']->grand_total;
                            ?>
                            <tr>
                                <th colspan="6" class="text-right"><?=lang("grand_total");?></th>
                                <th class="text-right"><?=$this->sma->formatMoney($return_sale ? ($dine['inv']->grand_total+$return_sale->grand_total) : $dine['inv']->grand_total);?></th>
                            </tr>
                            <?php
                        }
                        if ($dine['inv']->paid < ($dine['inv']->grand_total + $dine['inv']->rounding)) {
                            ?>
                           
                            <?php
                        } ?>
						<tr>
						<?php
						if ($pos_settings->display_tax==1 && $dine['inv']->tax_rate != 0) {
                            //echo '<tr><th colspan="4" class="text-right">' . lang("tax_inclusive (".$dine['inv']->tax_name.")") . '</th><th class="text-right">' . $this->sma->formatMoney($return_sale ? ($dine['inv']->order_tax+$return_sale->order_tax) : $dine['inv']->total_tax) . '</th></tr>';
							echo '<tr><th colspan="5" class="text-left">*<small>' . lang("tax ".$taxname." (".$dine['inv']->tax_name.")") . '</small></th></tr>';
                        }
						?>
						</tr>
                    </tfoot>
                </table>
                <?php
                

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
                
                
                <!--<table class="table table-striped table-condensed">
                    
                    
                    <tfoot>
                    	<?php
                        $currency = $this->site->getAllCurrencies();
                        
						foreach($currency as $currency_row){
						?>
                        <tr>
                            <th class="text-right"><?=lang("change ".$currency_row->code."");?></th>
                            <th class="text-right"><?=$this->sma->formatMoney($dine['inv']->balance / $currency_row->rate,$currency_row->symbol);?></th>
                        </tr>
                        <?php
						}
						?>
                        
                    </tfoot>
                </table>-->
                

               
            </div>
            
            <div id="receipt-datareceipt-data">
               
                <?php
                if ($Settings->invoice_view == 1 || $Settings->indian_gst) {
                    ?>
                    <div class="col-sm-12 text-center">
                        <h4 style="font-weight:bold;"><?=lang('tax_invoice');?></h4>
                    </div>
                    <?php
                }
                echo lang("sale_no_ref") . ": " . $bbq['reference_no'] . "<br>";
                /*echo "<p>" .lang("bill_no") . ": " . $bbq['inv']->bill_number . "<br>";*/
                
                echo "</p>";
                ?>

                <div style="clear:both;"></div>
                <table class="table table-striped table-condensed">
                    <thead>
                        <th colspan="2"><?=lang("details");?></th>
                        <th><?=lang("price");?></th>
                        <th><?=lang("no_of_cover");?></th>
                        <!-- <th><?=lang("discount_cover");?></th> -->
                        <th><?=lang("sub_total");?></th>
                    </thead>
                    <tbody>
                        <?php
                        $r = 1; $category = 0;
                        $tax_summary = array();
						
                        foreach ($bbq['billi_tems'] as $bill) {                           
                           							
                            echo '<tr><td colspan="2" class="no-border">' . $r .':&nbsp;' .$bill->type . '</span></td>'; 
                            echo '<td  class="no-border">'.$this->sma->formatMoney($bill->price).'</td>';
                            echo '<td class="no-border text-center">'.$bill->cover.'</td>';
							// echo '<td class="no-border text-center">'.$bill->discount_cover.'</td>';                            
                            echo '<td " class="no-border text-right">'.$this->sma->formatMoney($bill->subtotal).'</td></tr>';
                            $r++;
                        }
                        
                        ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="4" class="text-right"><?=lang("total");?></th>
                            <th class="text-right"><?=$this->sma->formatMoney($bbq['inv']->total);?></th>
                        </tr>
                        <?php 
                            if($bbq['inv']->bbq_cover_discount != 0) { ?>
                                <tr>
                                    <!-- <th colspan="4" class="text-right"><?=lang("cover_discount");?>(<?=$bbq['DiscountCovers'];?>)</th> -->
                                    <th colspan="4" class="text-right"><?=lang("cover_discount");?></th>
                                    <th class="text-right"><?=$this->sma->formatMoney($bbq['inv']->bbq_cover_discount);?></th>
                               </tr>
                        <?php  } 
                            if($bbq['inv']->bbq_daywise_discount != 0) { ?>
                                <tr>                                    
                                    <th colspan="4" class="text-right"><?=lang("bbq_daywise_discount");?></th>
                                    <th class="text-right"><?=$this->sma->formatMoney($bbq['inv']->bbq_daywise_discount);?></th>
                               </tr>
                        <?php  } ?>

                        <?php 
                            if($bbq['inv']->total_discount != 0) { ?>
                           <tr>
                                <!-- <th colspan="4" class="text-right"><?=lang("discount");?></th> -->
                                <th colspan="4" class="text-right"><?= $bbq['discount']; ?></th>
                                <th class="text-right"><?=$this->sma->formatMoney($bbq['inv']->total_discount);?></th>
                           </tr>
                        <?php  } ?>
                            <?php
							 if ($bbq['inv']->tax_type == 0)
                            {
                                $taxname = 'Inclusive';
                                $grandtotal = $bbq['inv']->total-$bbq['inv']->total_discount-$bbq['inv']->bbq_cover_discount-$bbq['inv']->bbq_daywise_discount;
                            }
                            else
                            {
                                $taxname = 'Exclusive';
                                $grandtotal = $bbq['inv']->total-$bbq['inv']->total_discount-$bbq['inv']->bbq_cover_discount-$bbq['inv']->bbq_daywise_discount+$bbq['inv']->total_tax;
                            }
							?>
                            <tr>
                                <th colspan="4" class="text-right"><?=lang("grand_total");?></th>
                                <th class="text-right"><?=$this->sma->formatMoney($grandtotal);?></th>
                            </tr>
                            
                            <?php
                      		$final_total[] = $grandtotal;
                       // if ($bbq['inv']->paid < ($bbq['inv']->grand_total + $bbq['inv']->rounding)) {
                            ?>
                            <!--<tr>
                                <th colspan="4" class="text-right"><?=lang("paid_amount");?></th>
                                <th class="text-right"><?=$this->sma->formatMoney($return_sale ? ($bbq['inv']->paid+$return_sale->paid) : $bbq['inv']->paid);?></th>
                            </tr>
                            <tr>
                                <th colspan="4" class="text-right"><?=lang("due_amount");?></th>
                                <th class="text-right"><?=$this->sma->formatMoney(($return_sale ? (($bbq['inv']->grand_total + $bbq['inv']->rounding)+$return_sale->grand_total) : ($bbq['inv']->grand_total + $bbq['inv']->rounding)) - ($return_sale ? ($bbq['inv']->paid+$return_sale->paid) : $bbq['inv']->paid));?></th>
                            </tr>-->
                            <?php
                       // } ?>
						<tr>
						<?php
						//if ($pos_settings->display_tax==1 && $bbq['inv']->tax_rate != 0) {
                            //echo '<tr><th colspan="4" class="text-right">' . lang("tax_inclusive (".$bbq['inv']->tax_name.")") . '</th><th class="text-right">' . $this->sma->formatMoney($return_sale ? ($bbq['inv']->order_tax+$return_sale->order_tax) : $bbq['inv']->total_tax) . '</th></tr>';
							echo '<tr><th colspan="6" class="text-left">*<small>' . lang("tax ".$taxname." (".$bbq['inv']->tax_name.")") . '</small></th></tr>';
                        //}
						?>
						</tr>
                    </tfoot>
                </table>
                
                
                <table class="table table-striped table-condensed">
                    
                    
                    <tfoot>
                    	
                        <tr>
                            <th class="text-right"><?=lang("final_total");?></th>
                            <th class="text-right"><?=$this->sma->formatMoney(array_sum($final_total),$currency_row->symbol);?></th>
                        </tr>
                       
                        
                    </tfoot>
                </table>
                
                <?php
				
				if ($dine['payments']) {
                    echo '<table class="table table-striped table-condensed"><tbody>';
                    /*echo "<pre>";
                    print_r($payments);die;*/
                    foreach ($dine['payments'] as $payment) {
                        echo '<tr>';
                        if (($payment->paid_by == 'cash' || $payment->paid_by == 'credit' || $payment->paid_by == 'deposit') && $payment->pos_paid) {
                            echo '<td>' . lang("paid_by") . ': ' . lang($payment->paid_by) . '</td>';
                            echo '<td>' . lang("amount") . ': ' . $this->sma->formatMoney($payment->pos_paid == 0 ? $payment->amount : $payment->pos_paid) . ($payment->return_id ? ' (' . lang('returned') . ')' : '') . '</td>';

                             $exchange_curr_code = $this->site->getExchangeCurrency($this->Settings->default_currency);

							 echo '<td>' . lang("foreign_exchange") . ': ' . $this->sma->formatMoney($payment->amount_exchange,$exchange_curr_code) . '</td>';


                            
                            
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
				
                if ($bbq['payments']) {
                    echo '<table class="table table-striped table-condensed"><tbody>';
                    /*echo "<pre>";
                    print_r($payments);die;*/
                    foreach ($bbq['payments'] as $payment) {
                        echo '<tr>';
                        if (($payment->paid_by == 'cash' || $payment->paid_by == 'credit' || $payment->paid_by == 'deposit') && $payment->pos_paid) {
                            echo '<td>' . lang("paid_by") . ': ' . lang($payment->paid_by) . '</td>';
                            echo '<td>' . lang("amount") . ': ' . $this->sma->formatMoney($payment->pos_paid == 0 ? $payment->amount : $payment->pos_paid) . ($payment->return_id ? ' (' . lang('returned') . ')' : '') . '</td>';

                             $exchange_curr_code = $this->site->getExchangeCurrency($this->Settings->default_currency);

							 echo '<td>' . lang("foreign_exchange") . ': ' . $this->sma->formatMoney($payment->amount_exchange,$exchange_curr_code) . '</td>';


                            
                            
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
                            <th class="text-right"><?=$this->sma->formatMoney($bbq['inv']->balance / $currency_row->rate,$currency_row->symbol);?></th>
                        </tr>
                        <?php
						}
						?>
                        
                    </tfoot>
                </table>
                

                <?= $Settings->invoice_view > 0 ? $this->gst->summary($rows, $return_rows, ($return_sale ? $bbq['inv']->product_tax+$return_sale->product_tax : $bbq['inv']->product_tax)) : ''; ?>

                <?= $customer->award_points != 0 && $Settings->each_spent > 0 ? '<p class="text-center">'.lang('this_sale').': '.floor(($bbq['inv']->grand_total/$Settings->each_spent)*$Settings->ca_point)
                .'<br>'.
                lang('total').' '.lang('award_points').': '. $customer->award_points . '</p>' : ''; ?>
                <?= $bbq['inv']->note ? '<p class="text-center">' . $this->sma->decode_html($bbq['inv']->note) . '</p>' : ''; ?>
                <?= $payment->staff_note ? '<p class="no-print"><strong>' . lang('staff_note') . ':</strong> ' . $this->sma->decode_html($payment->staff_note) . '</p>' : ''; ?>
                <?= $biller->invoice_footer ? '<p class="text-center">'.$this->sma->decode_html($biller->invoice_footer).'</p>' : ''; ?>
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