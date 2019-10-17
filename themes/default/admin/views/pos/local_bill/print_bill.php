<style>
    @media print{
        .convers_seats{margin-top: -10px;}
    }
    h3{ margin: 5px 0;font-size: 18px; }
    h1{ margin: 5px 0;font-size: 22px;font-weight: bold;}
    h4{ margin: 8px 0;font-size:14px;font-weight: bold;}        
    .hr_line{border: 1px solid #333;margin: 5px 0px;}
</style>
<?php 

$biller = $this->site->getCompanyOrderByID($bill_items['biller_id']); ?>
<?php $currency = $this->site->getAllCurrencies();?>
<div id="bill_header">
    <div id="wrapper1">
        <div id="receiptData" style="font-size: 14px!important;">
            <div id="receipt-datareceipt-data">
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
                                <h3 align="center" style="margin-top: -15px;line-height: 30px;"><?= lang("native_invoice") ?><br><?= lang("invoice") ?></h3>
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
                                        <td align="left"><?= lang("native_no_of_guests") ?>:<?= $bill_items['seats_id']; ?><br><?= lang("no_of_guests") ?>:<?= $bill_items['seats_id']; ?></td>
                                        <!-- <td align="left"><?= $inv->seats; ?></td> -->
                                    </tr>
                                    <tr>
                                        <td align="left"><?= lang("native_customer") ?>:<?=$bill_items['customer_name']?><br><?= lang("customer") ?>:<?= $bill_items['customer_name']?> </td>
                                        <!-- <td align="left"></td> -->
                                    </tr>
                                </table>
                            </td>
                            <?php  
                               if($this->Settings->time_format == 12){
                                    $date = new DateTime($bill_items['date']);
                                    $created_on = $date->format('Y-m-d h:iA');
                                    }else{
                                    $created_on =  $bill_items['date'];
                                }
                            ?>
                            <td width="45%">
                                <table width="100%" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td align="left"><?= lang("native_reference_no") ?>:<?=$splits?><br><?= lang("reference_no")?>:<?=$splits?></td>
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
                                <th><?= lang("native_no") ?><br><?= lang("no") ?></th>
                                <th colspan="2"><?= lang("native_description") ?><br><?= lang("description") ?></th>
                                <th><?= lang("native_qty") ?><br><?= lang("qty") ?></th>
                                <th><?= lang("native_price") ?><br><?= lang("price") ?></th>
                                <th><span style="white-space: nowrap; overflow: hidden; text-overflow:ellipsis;width: 50%!important "><?= lang("native_amount") ?></span><br><span style="white-space: nowrap; overflow: hidden; text-overflow:ellipsis;width: 50%!important "><?= lang("amount") ?></span></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php   
                        $r = 1; $category = 0;$sno=0;
                        $tax_summary = array();
                       foreach ($bill_items['item'] as $bill) {
                        $sno++;
                           $recipe_variant='';
                            if($this->Settings->user_language == 'khmer'){
                                if(!empty($bill['recipe_native_name'])){                                    
                                    $recipe_name = $bill['recipe_native_name'];
                                }else{
                                    $recipe_name = $bill['recipe_name'];
                                }
                                if($bill['variant_native_name']!='' || $bill['variant_native_name']!=0){
                                    $recipe_variant = ' - '.$bill['variant_native_name'];
                                }
                            }else{
                                $recipe_name = $bill['recipe_name'];
                                if($bill['recipe_variant']!='' || $bill['recipe_variant']!=0){                                
                                 $recipe_variant = ' - '.$bill['recipe_variant'];
                                }
                            }


                             $star ='';                               
                            if($order_discount_input != 0 ){
                                if($this->settings->customer_discount != 'none'){

                                  if($this->settings->customer_discount == 'customer'){
                                        $check = $this->site->Check_item_Discount_customer($bill['recipe_id'],$order_discount_input);
                                  } 
                                      if($check || $bill['manual_item_discount'] != 0){
                                        $star ='';
                                      }else{
                                        $star ='*';
                                      }
                                }                               
                            }

                            if($bill['manual_item_discount'] != 0){
                                $underline ='underline';
                           }else{
                                $underline ='none';
                           }                            
                            echo '<tr><td class="no-border text-center">'.$sno.'</td><td colspan="2" style="display: table-cell;text-align: -webkit-match-parent;text-decoration:'.$underline.'"  class="no-border">' . ($star)  . ($recipe_name) . ($recipe_variant ? ' ' . $recipe_variant . '' : ''); 

                            $addondetails = $this->site->getAddonByRecipeidAndOrderitemid($bill['recipe_id'],$bill['order_item_id']);
                            $itemaddonamt != 0;
                             if(!empty($addondetails)) :
                                 foreach ($addondetails as $key => $addons) { 
                                    $itemaddonamt += $addons->price*$addons->qty ?>
                                    <br> <span style="color: red;font-weight: bold;"> <?= $addons->addon_name ?> (<?= $addons->qty ?> X  <?=  $addons->price ?> )= <?=  $this->sma->formatMoney($addons->price*$addons->qty) ?></span>
                                    
                                 <?php } 
                             endif;
                             echo  '</td>';

                            echo '<td  class="no-border">'.$this->sma->formatMoney($bill['recipe_price']);
                           
                             if($itemaddonamt != 0) : ?>
                            <span style="color: red;text-align: right;"><?php echo $this->sma->formatMoney($itemaddonamt);?></span>
                            <?php endif; 
                            echo  '</td>';
                            echo '<td class="no-border text-center">'.$bill['recipe_qty'].'</td>';
                            
                        if($manual_discount_amount != 0){
                             if ($pos_settings->manual_item_discount_display_option == 1){
                                 echo '<td " class="no-border text-right">'.floor($bill['manual_item_discount_per_val']).'</td>';
                                }else{
                                    echo '<td " class="no-border ">'.$this->sma->formatMoney($bill['manual_item_discount']).'</td>';
                                }
                            } 
                            echo '<td " class="no-border text-right">'.$this->sma->formatMoney($bill['recipe_subtotal']-$bill['manual_item_discount']).'</td></tr>';
                            $r++;
                        } ?>

                        </tbody>
                    </table>
                </td>
            </tr>
        </table>   
        </div>
    </div>
</div>



<div id="bill-total-table">
<table class="table table-striped table-condensed" style="font-size:14px!important;">
                   
                    <tr>
                        <tr>
                            <th colspan="<?php echo $cols; ?>" class="text-right"><?=lang("items");?></th>
                            <th class="text-right"><?=$bill_items['item_cnt']?></th>
                        </tr>
                        <tr>
                            <th colspan="<?php echo $cols; ?>" class="text-right"><?=lang("total");?></th>
                            <th class="text-right"><?=$this->sma->formatMoney($bill_items['total'])?></th>
                        </tr>
                       <?php $dis = $bill_items['discount'];
                       /* if ($manual_discount_amount != 0) {
                            echo '<tr><th colspan="'.$cols.'" class="text-right">';?><?=lang("manual_discount_amount");?> <?php echo '</th><th class="text-right">' . $this->sma->formatMoney($manual_discount_amount) . '</th></tr>';
                        }*/
                        $discounnames = $discounnames;
                        if($discounnames == ''){
                            $discounnames ='Discount';
                        }
						if ($dis != 0) {
                            echo '<tr><th colspan="'.$cols.'" class="text-right">' .$discounnames. '</th><th class="text-right">' . $this->sma->formatMoney($dis) . '</th></tr>';
                        }
                         ?>
                        <?php
                        if ($pos_settings->display_tax == 1) {  
                                                     
    						if ($bill_items['tax_rate'] != 0 && $pos_settings->display_tax == 1) {                                 
    							echo '<tr><th colspan="'.$cols.'" class="text-right">' . $pos_settings->tax_caption . '</th>';
                            } 
                            if ($pos_settings->display_tax_amt == 1) {
                                echo '<th  class="text-right">' . $this->sma->formatMoney($bill_items['tax']) . '</th><tr>';
                            }                             
                        }

                        if ($bill_items['service_amount'] != 0) {     
                               echo '<tr><th colspan="'.$cols.'" class="text-right">' . $bill_items['service_charge_name'] . '</th>';
                            
                                echo '<th  class="text-right">' . $this->sma->formatMoney($bill_items['service_amount']) . '</th><tr>';
                        }

						?>
                         <?php
                             if ($pos_settings->print_option == 1) {
                              foreach($currency as $currency_row){ 
                                  ?>
                            <tr>
                                <th colspan="<?php echo $cols; ?>" class="text-right"><?=lang("grand_total");?><?php echo '('.$currency_row->code.')'?></th> 
                                <?php   
                                $n = $bill_items['grand_total'];//1.25
                                $whole = floor($n); // 1
                                $riel = $n - $whole; // .25
                                $exchange_rate = $this->site->getExchangeRatey($this->Settings->default_currency);

                                $amt = $this->sma->formatDecimal($bill_items['grand_total']);                                  
                                $r1 = $this->sma->formatDecimal($riel);                                               
                                // $round_riel = round($riel, 2);
                                $round_riel = $riel/ $exchange_rate;
                                $round_riel = round($round_riel / 100) * 100;

                                if($this->Settings->default_currency != $currency_row->id){
                                    $final_amt = $amt/ $currency_row->rate;
                                    $final_amt = round($final_amt / 100) * 100; ?>
                                    <!-- <th class="text-right"><?= $currency_row->symbol ?><?=$this->sma->formatMoney($final_amt,$currency_row->symbol);?> -->
                                        <th class="text-right"><?=$this->sma->exchangeformatMoney($final_amt,$currency_row->symbol);?>
                                <?php }else{
                                    $final_amt = $amt/ $currency_row->rate; ?>
                                    <th class="text-right"><?=$this->sma->formatMoney($final_amt,$currency_row->symbol);?>
                                <?php   } ?>

                                <!-- <th class="text-right"><?=$this->sma->formatMoney($final_amt,$currency_row->symbol);?> -->
                                <?php if($this->Settings->default_currency == $currency_row->id)
                                {
                                $exchange_curr_code = $this->site->getExchangeCurrency($this->Settings->default_currency);
                                echo '('.$exchange_curr_code.$round_riel.')' ;                                                      
                                // echo '('.$this->sma->formatMoney($round_riel / $exchange_rate,$exchange_curr_code).')' ;

                                }?> </th>
                            </tr>
                            <?php }  } else{       
                            $grandtotal=  $bill_items['grand_total'];                  
                            ?>
                            <tr>
                                <th colspan="<?php echo $cols; ?>" class="text-right"><?=lang("grand_total");?></th>
                                <th class="text-right"><?=$this->sma->formatMoney($grandtotal);?></th>
                            </tr>
                            <?php
                        }  
                        if ($pos_settings->discount_note_display_option==1) {      
                            if ($bill_items['discount'] != 0) {
                                if($this->settings->customer_discount != 'none'){
                                    echo '<tr><th colspan="5" class="text-left"><small>* Bill Discount is not applied to these items</small></th></tr>';
                                }
                            } 
                             if($manual_discount_amount != 0){
                                echo '<tr><th colspan="5" class="text-left"><small>Underlined Items are manually Discount is applied.</small></th></tr>';
                            }
                        }    

                        ?>

                      <!--   <tr>
                            <th colspan="4" class="text-right"><?=lang("grand_total");?></th>
                            <th class="text-right"><?=$this->sma->formatMoney($bill_items['grand_total'])?></th>
                        </tr> -->
                        <?= $biller->invoice_footer ? '<tr><th colspan="5" class="text-center">'.$this->sma->decode_html($biller->invoice_footer).'</th></tr>' : ''; ?>

                    </tr>
                    
                    <!-- <tfoot>
                      
                    </tfoot> -->
                </table>
</div>