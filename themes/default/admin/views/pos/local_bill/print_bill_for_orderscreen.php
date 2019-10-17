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

//$biller = $this->site->getCompanyOrderByID($bill_items->biller_id]); ?>
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
                                <img src="<?=base_url()?>/themes/default/admin/assets/images/logo_sugar_palm.jpg" alt="" width="76px;" style="position: absolute;left: -17px;top: 5px;">
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
                                <h3 align="center" style="margin-top: -15px;line-heig ht: 30px;"><?= lang("native_invoice") ?><br><?= lang("invoice") ?></h3>
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
                                        <td align="left"><?= lang("native_no_of_guests") ?>:<?= $inv->seats_id; ?><br><?= lang("no_of_guests") ?>:<?= $inv->seats_id; ?></td>
                                        <!-- <td align="left"><?= $inv->seats; ?></td> -->
                                    </tr>
                                    <tr>
                                        <td align="left"><?= lang("native_customer") ?>:<?=$inv->customer_name?><br><?= lang("customer") ?>:<?= $inv->customer_name?> </td>
                                        <!-- <td align="left"></td> -->
                                    </tr>
                                </table>
                            </td>
                            <?php  
                               if($this->Settings->time_format == 12){
                                    $date = new DateTime($inv->date);
                                    $created_on = $date->format('Y-m-d h:iA');
                                    }else{
                                    $created_on =  $inv->date;
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
                                <th colspan="2" ><?= lang("native_description") ?><br><?= lang("description") ?></th>
                                <th><?= lang("native_qty") ?><br><?= lang("qty") ?></th>
                                <th><?= lang("native_price") ?><br><?= lang("price") ?></th>
                                <th style="white-space: nowrap; overflow: hidden; text-overflow:ellipsis;width: 23%!important "><?= lang("native_amount") ?><br><?= lang("amount") ?></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php   
                        $r = 1; $category = 0;$sno=0;
                        $tax_summary = array();
                       foreach ($billitemdata as $bill) {
                        $sno++;
                        $recipe_variant='';
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
                            

                            /*if($this->Settings->user_language == 'khmer'){
                                if(!empty($bill->khmer_name)){
                                    $recipe_name = $bill->khmer_name;
                                }else{
                                    $recipe_name = $bill->recipe_name;
                                }
                            }else{
                                $recipe_name = $bill->recipe_name;
                            }

                            $recipe_variant='';
                            if($bill->recipe_variant!='' || $bill->recipe_variant!=0){                                
                                $recipe_variant = ' - '.$bill->recipe_variant;
                            }*/
                             $star ='';                               
                            if($order_discount_input != 0 ){
                                if($this->settings->customer_discount != 'none'){

                                  if($this->settings->customer_discount == 'customer'){
                                        $check = $this->site->Check_item_Discount_customer($bill->recipe_id,$order_discount_input);
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
                            echo '<tr style="font-size:12px!important;"><td class="no-border text-left">'.$sno.'</td><td colspan="2" style="display: table-cell;text-align: -webkit-match-parent;font-size:12px;text-decoration:'.$underline.'"  class="no-border">' . ($star)  . ($recipe_name) . ($recipe_variant ? ' ' . $recipe_variant . '' : ''); 

                            $addondetails = $this->site->getAddonByRecipeidAndOrderitemid($bill->recipe_id,$bill->order_item_id);
                            $itemaddonamt != 0;
                             if(!empty($addondetails)) :
                                 foreach ($addondetails as $key => $addons) { 
                                    $itemaddonamt += $addons->price*$addons->qty ?>
                                    <br> <span style="color: red;font-weight: bold;"> <?= $addons->addon_name ?> (<?= $addons->qty ?> X  <?=  $addons->price ?> )= <?=  $this->sma->formatMoney($addons->price*$addons->qty) ?></span>
                                 <?php } 
                             endif;
                             echo  '</td>';
                            echo '<td class="no-border text-center">'.$bill->quantity.'</td>';
                            echo '<td  class="no-border">'.$this->sma->formatMoney($bill->net_unit_price);
                           
                             if($itemaddonamt != 0) : ?>
                            <span style="color: red;text-align: right;"><?php echo $this->sma->formatMoney($itemaddonamt);?></span>
                            <?php endif; 
                            echo  '</td>';
                           
                            
                        if($manual_discount_amount != 0){
                             if ($pos_settings->manual_item_discount_display_option == 1){
                                 echo '<td " class="no-border text-right">'.floor($bill->manual_item_discount_per_val).'</td>';
                                }else{
                                    echo '<td " class="no-border ">'.$this->sma->formatMoney($bill->manual_item_discount).'</td>';
                                }
                            } 
                            echo '<td " class="no-border text-right">'.$this->sma->formatMoney($bill->subtotal-$bill->manual_item_discount).'</td></tr>';
                            $r++;
                        } ?>

                        </tbody>
                    </table>
                </td>
            </tr>
            <?php $cols = "4"; ?>
                <table class="table table-striped table-condensed" style="font-size:14px!important;">                    
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="6" class="text-right"><?=lang("total");?></th>
                            <th class="text-right"><?=$this->sma->formatMoney($inv->total-$inv->manual_item_discount);?></th>
                        </tr>
                        <?php
                        $total_dis_without_manual =  ($inv->total_discount - $inv->manual_item_discount);
                        if ($total_dis_without_manual != 0) {
                            if($inv->discount_type == 'manual'){
                                if($inv->discount_val){
                                    $disname = $inv->discount_val;
                                }else{
                                    $disname = 'Discount';
                                }
                                echo '<tr><th colspan="6" class="text-right">' . lang("discount") . '('.$disname.')</th><th class="text-right">' . $this->sma->formatMoney($total_dis_without_manual) . '</th></tr>';
                            } else {
                                if($discounnames){
                                    $disname = $discounnames;
                                }else{
                                    $disname = 'Discount';
                                }
                            echo '<tr><th colspan="6" class="text-right">' .$disname. '</th><th class="text-right">' . $this->sma->formatMoney($total_dis_without_manual) . '</th></tr>';
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
                            $taxcol = "4";
                            if($inv->manual_item_discount != 0)
                            {
                                $taxcol = "5";
                            }
                            if( $pos_settings->tax_caption != ''){
                                  echo '<tr><th colspan="'.$taxcol.'" class="text-right">' . $pos_settings->tax_caption. '</th>';
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

                            <?php
                             if ($pos_settings->print_option == 1) {
                              foreach($currency as $currency_row){ 
                                  ?>
                            <tr>
                                <th colspan="<?php echo $cols; ?>" class="text-right"><?=lang("grand_total");?><?php echo '('.$currency_row->code.')'?></th> 
                                <?php                               
                                $amt = $this->sma->formatDecimal($grandtotal);                                  
                                $r1 = $this->sma->formatDecimal($riel);                                               
                                // $round_riel = round($riel, 2);
                                $round_riel = $riel/ $exchange_rate;
                                $round_riel = round($round_riel / 100) * 100;


                               if($this->Settings->default_currency != $currency_row->id){
                                    $final_amt = $grandtotal/ $currency_row->rate;
                                    $final_amt = round($final_amt / 100) * 100; ?>
                                    <!-- <th class="text-right"><?= $currency_row->symbol ?><?=$final_amt;?> -->
                                        <th class="text-right"><?= $this->sma->exchangeformatMoney($final_amt,$currency_row->symbol)?>
                                <?php }else{
                                    $final_amt = $grandtotal/ $currency_row->rate;?>
                                    <th class="text-right"><?=$this->sma->formatMoney($final_amt,$currency_row->symbol);?>
                              <?php  }

                                ?>

                                <!-- <th class="text-right"><?=$this->sma->formatMoney($final_amt,$currency_row->symbol);?> -->
                                <?php if($this->Settings->default_currency == $currency_row->id)
                                {
                                $exchange_curr_code = $this->site->getExchangeCurrency($this->Settings->default_currency);
                                echo '('.$exchange_curr_code.$round_riel.')' ;  
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
                        } } else {
                            ?>
                             <?php foreach($currency as $currency_row){ ?>
                                <tr>
                                    <th colspan="6" class="text-right"><?=lang("grand_total");?><?php echo '('.$currency_row->code.')'?></th> 

                                    <th class="text-right"><?=$this->sma->formatMoney(($return_sale ? ($inv->grand_total+$return_sale->grand_total) : $inv->grand_total) / $currency_row->rate,$currency_row->symbol);?></th>
                                </tr>
                             <?php  } ?>  
                       <?php } ?>                      
                        
                        <?php       
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
                        
                    </tfoot>
                </table>
        </table>   
        </div>
        <?= $biller->invoice_footer ? '<p class="text-center">'.$this->sma->decode_html($biller->invoice_footer).'</p>' : ''; ?>
    </div>
</div>
</table>
</div>