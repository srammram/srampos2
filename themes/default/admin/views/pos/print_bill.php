<style>
    @media print{
        .convers_seats{margin-top: -10px;}
    }
</style>
<?php 

$biller = $this->site->getCompanyOrderByID($bill_items['biller_id']); ?>
<?php $currency = $this->site->getAllCurrencies();?>
<div id="bill_header">
    <div id="wrapper1">
        <div id="receiptData" style="font-size: 14px!important;">
            <div id="receipt-datareceipt-data">
                <?php if(!$pos_settings->pre_printed_format) : ?>
		<div class="text-center">
                    <?= !empty($biller->logo) ? '<img src="'.base_url('assets/uploads/logos/'.$biller->logo).'" alt="">' : ''; ?>
                    <?php if($pos_settings->print_local_language == 1) : ?>
                    <h3 style="text-transform:uppercase;"><?=$biller->local_lang_name;?></h3>
                    <?php endif; ?>
                    <h3 style="text-transform:uppercase;"><?=$biller->company != '-' ? $biller->company : $biller->name;?></h3>
                    <?php                     
                    if($pos_settings->print_local_language == 1) :
                    echo "<p>" .$biller->local_lang_address;
                    endif;

                    echo "<h4>" . $biller->address . " " . $biller->city . " " . $biller->postal_code . " " . $biller->state . " " . $biller->country .
                    "<br>" . lang("tel") . ": " . $biller->phone.'</h4>';?>
                </div>
                <h4 class="text-center">Invoice</h4>
		<?php endif; 


               if($this->Settings->time_format == 12){
                    $date = new DateTime($bill_items['date']);
                    $created_on = $date->format('Y-m-d h:iA');
                }else{
                    $created_on =  $bill_items['date'];
                }


        ?>
                <p> <?=lang("date")?>: <?=$created_on?>
                <br><?=lang("reference_no")?>: <?=$splits?>
                <br><?=lang("sales_person")?>: <?=$bill_items['created_by']?>
                <br><?=lang("table")?>: <?=$bill_items['table_name']?>
                <br><?=lang("customer")?>: <?=$bill_items['customer_name']?></p>
                <?php if($pos_settings->total_covers == 1){ ?>
                <p class="convers_seats"><?=lang("No of Covers")?>: <?=$bill_items['seats_id']?></p>
            <?php } ?>
            </div>
        </div>
    </div>
</div>



<div id="bill-total-table">
<table class="table table-striped table-condensed" style="font-size:14px!important;">
                    <tr>
                        <th colspan="2"><?=lang("description");?></th>
                        <th><?=lang("price");?></th>
                        <th><?=lang("qty");?></th>
                        <?php 
                        $cols="4";
                        if($manual_discount_amount != 0){
                        $cols="5"; 
                        if ($pos_settings->manual_item_discount_display_option == 1){
                            $dis ="dis(%)";
                        }else{
                            $dis ="dis";
                        }
                        ?>
                                <th><?=lang($dis);?></th>
                           <?php } ?>
                        <th class="text-center"><?=lang("sub_total");?></th>
                    </tr>
                    <tr>
                        <?php
                        $r = 1; $category = 0;
                        $tax_summary = array();                        
                        foreach ($bill_items['item'] as $bill) {     
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
                            echo '<tr><td colspan="2" style="display: table-cell;text-align: -webkit-match-parent;text-decoration:'.$underline.'"  class="no-border">' . ($star)  . ($recipe_name) . ($recipe_variant ? ' ' . $recipe_variant . '' : ''); 

                            $addondetails = $this->site->getAddonByRecipeidAndOrderitemid($bill['recipe_id'],$bill['order_item_id']);
                            $itemaddonamt = 0;
                             if(!empty($addondetails)) :
                                 foreach ($addondetails as $key => $addons) { 
                                    $itemaddonamt += $addons->price*$addons->qty ?>
                                    <br> <span style="color: red;font-weight: bold;"> <?= $addons->addon_name ?> (<?= $addons->qty ?> X  <?=  $addons->price ?> )= <?=  $this->sma->formatMoney($addons->price*$addons->qty) ?></span>
                                    
                                 <?php } 
                             endif;
                             echo  '</td>';

                            echo '<td  class="no-border">'.$this->sma->formatMoney($bill['recipe_price']);
                           
                             if($itemaddonamt != 0) : ?>
                             <br> <span style="color: red;text-align: right;"><?php echo $this->sma->formatMoney($itemaddonamt);?></span>
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
                        }
                        ?>
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
    						if ($bill_items['tax_rate'] != 0) { 
    							echo '<tr><th colspan="'.$cols.'" class="text-right">' . $pos_settings->tax_caption . '</th>';
                            } 
                            if ($pos_settings->display_tax_amt == 1) {
                                echo '<th  class="text-right">' . $this->sma->formatMoney($bill_items['tax']) . '</th>';
                            } 
                            echo '<tr>';
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