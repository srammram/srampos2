<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?><!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title><?=lang('pos_module') . " | " . $Settings->site_name;?></title>
<script type="text/javascript">if(parent.frames.length !== 0){top.location = '<?=admin_url('pos')?>';}</script>
<base href="<?=base_url()?>"/>
<meta http-equiv="cache-control" content="max-age=0"/>
<meta http-equiv="cache-control" content="no-cache"/>
<meta http-equiv="expires" content="0"/>
<meta http-equiv="pragma" content="no-cache"/>
<link rel="shortcut icon" href="<?=$assets?>images/favicon.ico"/>
<link rel="stylesheet" href="<?=$assets?>styles/theme.css" type="text/css"/>
<link rel="stylesheet" href="<?=$assets?>styles/style.css" type="text/css"/>
<link rel="stylesheet" href="<?=$assets?>pos/css/posajax.css" type="text/css"/>
<link rel="stylesheet" href="<?=$assets?>pos/css/print.css" type="text/css" media="print"/>
<script type="text/javascript" src="<?=$assets?>js/jquery-2.0.3.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/jquery-migrate-1.2.1.min.js"></script>
<script type="text/javascript">
var site = <?=json_encode(array('url' => base_url(), 'base_url' => admin_url('/'), 'assets' => $assets, 'settings' => $Settings, 'dateFormats' => $dateFormats))?>, pos_settings = <?=json_encode($pos_settings);?>;
var lang = {
    unexpected_value: '<?=lang('unexpected_value');?>',
    select_above: '<?=lang('select_above');?>',
    r_u_sure: '<?=lang('r_u_sure');?>',
    bill: '<?=lang('bill');?>',
    order: '<?=lang('order');?>',
    total: '<?=lang('total');?>',
    items: '<?=lang('items');?>',
    discount: '<?=lang('discount');?>',
    order_tax: '<?=lang('order_tax');?>',
    grand_total: '<?=lang('grand_total');?>',
    total_payable: '<?=lang('total_payable');?>',
    rounding: '<?=lang('rounding');?>',
    merchant_copy: '<?=lang('merchant_copy');?>'
};

</script>
<!-- <script src="<?= $assets ?>js/core.js"></script> -->
<!--[if lt IE 9]>
<script src="<?=$assets?>js/jquery.js"></script>
<![endif]-->
<?php if ($Settings->user_rtl) {?>
<link href="<?=$assets?>styles/helpers/bootstrap-rtl.min.css" rel="stylesheet"/>
<link href="<?=$assets?>styles/style-rtl.css" rel="stylesheet"/>
<script type="text/javascript">
$(document).ready(function () {
$('.pull-right, .pull-left').addClass('flip');
});
</script>
<?php }
?>
</head>
<body>

<noscript>
<div class="global-site-notice noscript">
<div class="notice-inner">
<p><strong>JavaScript seems to be disabled in your browser.</strong><br>You must have JavaScript enabled in
your browser to utilize the functionality of this website.</p>
</div>
</div>
</noscript>



<div id="wrapper">


<?php
$this->load->view($this->theme . 'pos/pos_header');
?>

<div id="content">
<div class="c1">
<div class="pos">
<?php
if ($error) {
echo "<div class=\"alert alert-danger\"><button type=\"button\" class=\"close fa-2x\" data-dismiss=\"alert\">&times;</button>" . $error . "</div>";
}
?>
<?php
if (!empty($message)) {
echo "<div class=\"alert alert-success\"><button type=\"button\" class=\"close fa-2x\" data-dismiss=\"alert\">&times;</button>" . $message . "</div>";
}
?>

<div id="pos">

<?php $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'id' => 'pos-autosplitbill-form');
echo admin_form_open("pos/bbqconsolidated?bbq_order_type=".$bbq_order_type."&dine_order_type=".$dine_order_type."&bill_type=4&bils=".$bils."&table=".$table_id."&splits=".$split_id, $attrib);?>


<input type="hidden" name="bbq_order_type" value="<?php echo $bbq_order_type; ?>">
<input type="hidden" name="dine_order_type" value="<?php echo $dine_order_type; ?>">
<input type="hidden" name="bill_type" value="<?php echo $bill_type;?>" />
<input type="hidden" name="bils" value="<?php echo $bils;?>" />
<input type="hidden" name="table" value="<?php echo $table_id;?>" />
<input type="hidden" name="splits" value="<?php echo $split_id;?>" />



<div id="cp" class="single_split_table">

<div id="cpinner">
<div class="quick-menu">
<div id="proContainer">
    <div id="ajaxrecipe">
        <div id="item-list">
        	<?php
			if($order_type == 3){
			?>
        	<div class="col-lg-4 col-lg-offset-4">
            <label><?=lang('delivery_person')?></label>
            <?php
			$delivery_person = $this->site->getDeliveryPersonall($this->session->userdata('warehouse_id'));
			
			?>
        	<select name="delivery_person_id" id="delivery_person_id" class="form-control">
            <?php
			foreach($delivery_person as $delivery_person_row){
			?>
            	<option value="<?php echo $delivery_person_row->id; ?>"><?php echo $delivery_person_row->first_name.' '.$delivery_person_row->last_name.' ['.$delivery_person_row->description.']'; ?></option>
            <?php
			}
			?>
            </select>
            </div>
            <?php
			}
			?>
            <div class="clearfix"></div>
            <?php 
                if(!empty($order_item))
                {
                $total_count = count($order_item);
                $split_count = $bils;
                for($i=1;$i<=$split_count;$i++){

            ?>
            <div class="col-xs-12">
                <table id="example3" class="table table-bordered table-striped copytabl single_table_bg"><thead><tr class = "clickable" ><th><?=lang('sale_item')?></th><th><?=lang('price')?></th><th><?=lang('sale_qty')?></th><th><?=lang('discount')?></th><th><?=lang('subtotal')?></th></tr></thead>
                <tbody class = "autobilldt"  style="cursor: pointer;">
                <?php
					$recipeid_data = array();$recipeid_qty = array();
                    foreach($order_item as $salesitem) 
                    {
						$recipeid_data[] = $salesitem->recipe_id;
                                                $recipeid_qty[] = $salesitem->quantity;
                        $r_total_discount[$i] = array();
                        $r_subtotal[$i] = array();
			$discount = $this->site->discountMultiple($salesitem->recipe_id);//echo $salesitem->recipe_id;print_R($discount);exit;
                        $khmer_name = $this->site->getrecipeKhmer($salesitem->recipe_id);
						
                          $discount_value = '';
						if(!empty($discount)){
                           
							if($discount[2] == 'percentage_discount'){

				              $discount_value = $discount[1].'%';

							}else{
								$discount_value =$discount[1];
							}
							
							 $price_total = $salesitem->subtotal;
							 $dis = $this->site->calculateDiscount($discount_value, $price_total);
							 $subtotal[$i][] = $price_total;
                                                         $r_subtotal[$i][] = $price_total;
							 $total_tax[$i][] = $salesitem->item_tax;
							 $total_discount[$i][] = $dis;
                                                         $r_total_discount[$i][] = $dis;
						}else{
							 $dis = 0;
							 $price_total = $salesitem->subtotal;
							 $subtotal[$i][] = $salesitem->subtotal;
							 $total_tax[$i][] = $salesitem->item_tax;
							 $total_discount[$i][] = $dis;
                                                         $r_total_discount[$i][] = $dis;
                                                         $r_subtotal[$i][] = $price_total;
						}
						
                        ?>

                        <tr class = "clickable">
                        	<td>
                                <?php
                                $variant ='';$variant_name=''; $variant_id=0;
                                    if($salesitem->variant!=''){                                        
                                        $vari = explode('|',$salesitem->variant);
                                        $variant = $salesitem->variant;
                                        $variant_id = $salesitem->recipe_variant_id;
                                        $variant_name='     -  '.'[<span class="pos-variant-name">'.$variant.'</span>]';
                                    }                                    
                                ?>

                            <?php
							if($this->Settings->user_language == 'khmer'){
									if(!empty($khmer_name)){
										$recipe_name = $khmer_name;
									}else{
										$recipe_name = $salesitem->recipe_name;
									}
								}else{
									$recipe_name = $salesitem->recipe_name;
								}
							?>
							
                            <?php echo $recipe_name.$variant_name;?>
                            <input type="hidden" name="split[<?php echo $i;?>][recipe_name][]" value="<?php echo $salesitem->recipe_name;?>">

                        <input type="hidden" name="split[<?php echo $i;?>][recipe_id][]" value="<?php echo $salesitem->recipe_id;?>" class="split-recipe-id">

                        <input type="hidden" name="split[<?php echo $i;?>][recipe_code][]" value="<?php echo $salesitem->recipe_code;?>">

                        <input type="hidden" name="split[<?php echo $i;?>][recipe_type][]" value="<?php echo $salesitem->recipe_type;?>">

                        	</td>
                        	<td class="text-right">
							<?php echo $this->sma->formatMoney($salesitem->unit_price);?>
                            <input type="hidden" name="split[<?php echo $i;?>][unit_price][]" value="<?php echo $salesitem->unit_price;?>">
                            <input type="hidden" name="split[<?php echo $i;?>][recipe_variant][]" value="<?php echo $variant;?>">
                            <input type="hidden" name="split[<?php echo $i;?>][recipe_variant_id][]" value="<?php echo $variant_id;?>">
                            </td>
                        	<td class="text-right">
							<?php echo $salesitem->quantity;?>
                            <input type="hidden" name="split[<?php echo $i;?>][quantity][]" value="<?php echo $salesitem->quantity;?>" id="recipe-qty-<?=$salesitem->recipe_id?>">
                            </td>
                        	<td class="text-right">
				<?php echo $dis; ?>			
                            <input type="hidden" name="split[<?php echo $i;?>][item_discount][]"  value="<?php echo $dis; ?>" id="recipe-item-discount-<?=$salesitem->recipe_id?>">
                            <input type="hidden" name="split[<?php echo $i;?>][item_discount_id][]"  value="<?php echo $discount[0]; ?>">

                            <?php 
                            $TotalDiscount = $this->site->TotalDiscount();
                            
                            //echo array_sum($r_subtotal[$i]).'--'.array_sum($r_total_discount[$i]);
                            $value =array_sum($r_subtotal[$i]) - array_sum($r_total_discount[$i]);
                            $offer_dis = 0;
                            $sub = 0;
                             if($TotalDiscount[0] != 0)
                                {                                     
                                 if($TotalDiscount[3] == 'percentage_discount'){
                                        $totdiscount = $TotalDiscount[1].'%';

                                    }else{
                                        $totdiscount =$TotalDiscount[1];
                                    }

                                    $totdiscount1 = $this->site->calculateDiscount($totdiscount, $value);
                                    
                                    //$offer_dis = $this->site->calculate_Discount($totdiscount1, ($price_total-$dis),array_sum($subtotal[$i]) - array_sum($total_discount[$i]));
                                    $offer_dis = $totdiscount1;
                                    $sub = $price_total - $dis - $offer_dis;  
                                }        

                            ?>
                            <input type="hidden" name="item_offer_dis[]" value="<?php echo $offer_dis;?>" id="recipe-offer-discount-<?=$salesitem->recipe_id?>">
                            <input type="hidden" name="split[<?php echo $i;?>][item_input_dis][]" value="0" id="recipe-input-discount-<?=$salesitem->recipe_id?>">
                            <input type="hidden" name="split[<?php echo $i;?>][all_discount][]"  value="" id="recipe-total-discount-<?=$salesitem->recipe_id?>">
                            <input type="hidden" name="split[<?php echo $i;?>][item_tax][]" value="<?php echo $salesitem->item_tax;?>">
                        	</td>
                        
                        	<td class = "text-right">
								<?php echo $this->sma->formatMoney($price_total);?>
                                <input type="hidden" name="split[<?php echo $i;?>][subtotal][]" value="<?php echo $price_total;?>">
                            </td>
                          </tr>
						  <?php } $recipeids =  implode(',',$recipeid_data);$recipeqtys =  implode(',',$recipeid_qty);?>
					</tbody>
                   <tbody>
                   		<tr>
                        	<td colspan="4" ><?=lang('total_item')?></td>
                            <td class="right_td text-right">
                                <?php if(isset($discount['unique_discount'])) : ?>
                                <input type="hidden" name="unique_discount" value="1">
                                    <?php endif; ?>
								<?php echo $total_count; ?>
                                <input type="hidden" name="split[<?php echo $i;?>][total_item]" value="<?php echo $total_count; ?>">
                   			</td>
                         </tr>
                  	<tr>
                    	<td colspan="4"><?=lang('total_price')?></td>
                        <td class="right_td text-right"><?php echo $this->sma->formatMoney(array_sum($subtotal[$i]));?>
                      	<input type="hidden" name="split[<?php echo $i;?>][total_price]" value="<?php echo array_sum($subtotal[$i]);?>" id="subtotal_<?php echo $i; ?>">
                  		</td>
                  	</tr>
                                      
                    <?php 
                    $HideShow = "visible";
                    $display = "contents";
                    if(array_sum($total_discount[$i]) != 0){
                        $HideShow = "visible";
                        $display = "contents";
                        
                        }
                        else{
                            $HideShow = "hidden";
                            $display = "none";
                            }
                    ?>
                   
                    <tr style="visibility: <?php echo $HideShow;?>;display:<?php echo $display;?>;">

                    	<td colspan="3">
                    	<?=lang("total_discount", "order_discount_input");?>
                        </td>
                        <td>
                        <?php echo $this->sma->formatMoney(array_sum($total_discount[$i])); ?>
                         <input type="hidden" id="item_discounts_<?php  echo $i; ?>"  name="split[<?php echo $i;?>][itemdiscounts]" value="<?php echo array_sum($total_discount[$i]); ?>">
                        </td>
                   
                         <td class="right_td text-right" style="padding: 5px 10px;font-size: 14px; font-weight:bold;">
                                <span ><?php echo $this->sma->formatMoney(array_sum($subtotal[$i]) - array_sum($total_discount[$i])); ?></span>
                               <input type="hidden" id="item_dis_<?php  echo $i; ?>"  name="split[<?php echo $i;?>][item_dis]" value="<?php echo array_sum($subtotal[$i]) - array_sum($total_discount[$i]); ?>"> 
                                
                        </td>
                    </tr>
                    
                    <?php 
                    $val = 0;
                    $date =date('Y-m-d');
                    $TotalDiscount = $this->site->TotalDiscount(); 
                  
                    $value =array_sum($subtotal[$i]) - array_sum($total_discount[$i]);

                    if($TotalDiscount[0] != 0)
                    { 
                         
                         if($TotalDiscount[3] == 'percentage_discount'){

                                $totdiscount = $TotalDiscount[1].'%';

                            }else{
                                $totdiscount =$TotalDiscount[1];
                            }
                            
                            $totdiscount1 = $this->site->calculateDiscount($totdiscount, $value);

                 $sub_total =array_sum($subtotal[$i]) - array_sum($total_discount[$i]); 
                        
                         if((!isset($discount['unique_discount']) || isset($discount['only_offer_dis'])) && $TotalDiscount[2]  <= $sub_total)
                         {  
                            $val =$value - $totdiscount1;
                             echo '<tr>
                                <td colspan="3">'.lang('offer_discount').'
                                </td>
                                <td>
                                '.$totdiscount.'

                                <input type="hidden" name="split['.$i.'][tot_dis_id]" value="'.$TotalDiscount[0].'">
                                <input type="hidden" name="split['.$i.'][tot_dis_value]" value="'.$totdiscount.'">
                                </td>
                                <td class="right_td text-right" style="padding: 5px 10px;font-size: 14px; font-weight:bold;">

                                <input type="hidden" id="offer_discount_'.$i.'" name="split['.$i.'][tot_dis1]" value="'.$val.'">

                                <input id="off_discount_'.$i.'"  type="hidden" name="split['.$i.'][offer_dis]" value="'.$totdiscount1.'">

                                  <span id="tds1_'.$i.'">'.$this->sma->formatMoney($totdiscount1).'</span>
                                </td>
                                </tr>';
                        }
                    }
                    if($val)
                    {
                        $final_val = $val;
                    }
                    else
                    {
                        $final_val = $value;
                    }
                    ?>
                    <?php if(!isset($discount['unique_discount'])) : ?>
                        <tr>
                            <td colspan="3">
                            <?=lang("other_discount", "order_discount_input");?>
                            </td>
                       
                            <td>
                            <!-- <?php echo form_input('split['.$i.'][order_discount_input]', '', 'class="form-control kb-pad order_discount_input"  autocomplete="off" id="order_discount_input_'.$i.'"  count="'. $i.'"'); ?> -->
                            <div class="">
                                <input type="hidden" name="split[<?php echo $i;?>][recipeids]" id="recipeids_<?php echo $i; ?>" value="<?php echo $recipeids; ?>" >
                                <input type="hidden" name="split[<?php echo $i;?>][recipeqtys]" id="recipeqtys_<?php echo $i; ?>" value="<?php echo $recipeqtys; ?>" >
                               
                                <?php if($Settings->customer_discount=='customer') : ?>
                                <input type="hidden" name="dine_in_discount" value="<?= $discount_select['dine'] ?>">
                                
                                 
                                <select style="display: "  name="split[<?php echo $i;?>][order_discount_input]" class="form-control pos-input-tip order_discount_input" id="order_discount_input_<?php echo $i; ?>" count="<?php echo $i; ?>">
                                <option value="0">No</option>
                                    <?php
                                    foreach ($customer_discount as $cusdis) {
                                        
                                    ?>
                                    <option  <?php if($discount_select['dine'] == $cusdis->id){ echo 'selected'; }else{ echo ''; } ?> value="<?php echo $cusdis->id; ?>" data-id="<?php echo $cusdis->id; ?>"><?php echo $cusdis->name; ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                                <?php elseif($Settings->customer_discount=='manual') : ?>
                                <input type="text" name="split[<?php echo $i;?>][order_discount_input]" autocomplete="off" class="form-control kb-pad pos-input-tip order_discount_input manual-discount" id="order_discount_input_<?php echo $i; ?>" count="<?php echo $i; ?>">
                                <?php endif; ?>
                                </div>
                            </td>
                            <td class="right_td text-right" style="padding: 5px 10px;font-size: 14px; font-weight:bold;">
                         
                            <input type="hidden" id="tdis_<?php  echo $i; ?>" name="split[<?php echo $i;?>][discount_amount]" value="0">
                             <span id="tds_<?php echo $i; ?>"><?php echo $this->sma->formatMoney(0); ?></span>
                             
                             <!--<input type="hidden" id="max-allow-discount-percent_<?php  echo $i; ?>"  value="<?=($current_user->max_discount_percent!=0)?$current_user->max_discount_percent:'';?>">-->
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php 
                        if(!empty($order_data))
                        {
                           $custimerid = $order_data['customer_id'];                     
                           $check = $this->site->Check_birthday_discount_isavail($order_data['customer_id']);
                        if($this->pos_settings->birthday_enable != 0  && $this->pos_settings->birthday_discount != 0){
                        if($check == true){
                         ?>
                            <tr>
                                <td colspan="4"> <?=lang("birthday_discount");?> </td>
                                <?php
                                 $birday = $this->pos_settings->birthday_discount;                             
                                    $birthday_val = $this->site->calculateDiscount($birday, $final_val);
                                    $final_val   = $final_val - $birthday_val;
                                ?>
                                 <input type="hidden" name="split[<?php echo $i;?>][birthday_discount]" autocomplete="off" class="form-control kb-pad pos-input-tip  birthday-discount birthday_discount_<?php echo $i; ?>" count="<?php echo $i; ?>" value="<?php echo $birthday_val;?>">

                                 <input type="hidden" name="split[<?php echo $i;?>][after_birthday_discount]" autocomplete="off" class="form-control kb-pad pos-input-tip  birthday-discount" id="after_birthday_discount_<?php echo $i; ?>" count="<?php echo $i; ?>" value="<?php echo $final_val;?>">
                                <td  class="right_td text-right" style="padding: 5px 10px;font-size: 14px; font-weight:bold;"> <span class="birthday_discount_<?php echo $i; ?>"> <?php echo $this->sma->formatMoney($birthday_val); ?>  </span> </td>
                            </tr>
                    <?php } } } ?>
                    <!-- dine service Charge -->
                    <?php 
                    $serice_charge_amt =0;                   
                    $colspan1 = 4;
                    if($this->pos_settings->default_service_charge !=0 && $this->pos_settings->service_charge_option !=0){
                        $ServiceHideShow = "visible";
                        $Servicedisplay = "contents";
                     }else{
                        $ServiceHideShow = "hidden";
                        $Servicedisplay = "none";
                    }
                    ?>
                    <tr style="visibility: <?php echo $ServiceHideShow;?>;display:<?php echo $Servicedisplay; ?> ">
                    <td colspan="<?php echo $colspan1;?>" class="text-right">
                        <?php 
                            $AllServiceCharge = $this->site->getAllSericeCharges();
                            $ServiceCharge = $this->site->getServiceChargeByID($this->pos_settings->default_service_charge);
                        ?>                    
                        <select style="display: none"  name="split[<?php echo $i;?>][dine_service_charge]" class="form-control pos-input-tip service_charge" id="dine_service_charge_<?php echo $i; ?>" count="<?php echo $i; ?>">
                        <?php
                            foreach ($AllServiceCharge as $Service) {  ?>
                            <option value="<?php echo $Service->id; ?>" <?php if($ServiceCharge->id == $Service->id){ echo 'selected'; }else{ echo ''; } ?> data-id="<?php echo $Service->rate; ?>"><?php echo $Service->name; ?></option>
                        <?php }   ?>
                        </select>                    
                        
                    <?php
                    if($this->pos_settings->default_service_charge !=0 && $this->pos_settings->service_charge_option !=0){
                        $dinserice_charge_amt = ($final_val) * ($ServiceCharge->rate / 100);  
                    } 

                        $dineServiceCharge_Text = $ServiceCharge->name;                      
                        ?>
                     <span style="text-align: right;" id="servicecha_<?php echo $i; ?>"> <?php echo $dineServiceCharge_Text; ?></span>
                    </td>

                    <td class="right_td text-right" style="padding: 5px 10px;font-size: 14px; font-weight:bold;">
                        <input type="hidden" name="split[<?php echo $i;?>][dine_service_amount]" id="dine_service_amount_<?php echo $i; ?>" value="<?php echo $dinserice_charge_amt; ?>">                        
                        <span id="dine_spansericechargeamt_<?php echo $i; ?>"><?php echo $this->sma->formatMoney($dinserice_charge_amt); ?></span>
                    </td>                    
                    </tr><tr></tr>

                    <!-- dine service Charge -->

                    <?php                     
                    $getTaxType = $this->pos_settings->tax_type;
                    //$this->pos_settings->default_tax == no tax
                    //$getTaxType == inclusive tax, so that tax hide

                    $HideShow = "visible";
                    $display = "contents";
                    if($this->pos_settings->default_tax != 1 && $getTaxType != 0)
                        {       
                        $HideShow = "visible";
                        $display = "contents";
                        }
                        else{
                            $HideShow = "hidden";
                            $display = "none";
                       }
                    ?>
                    <tr style="visibility: <?php echo $HideShow;?>;display:<?php echo $display; ?> ">

                    <td colspan="3" >
                    <?=lang("tax");?>
                        <?php 
                            $getTax = $this->site->getTaxRateByID($this->pos_settings->default_tax);
                        ?>
                    <div class="col-lg-6 pull-right">
                   
                        <input type="hidden" name="split[<?php echo $i;?>][tax_type]" id="tax_type_<?php echo $i; ?>" value="<?php echo $getTaxType; ?>">

                        <select style="display: none"  name="split[<?php echo $i;?>][ptax]" class="form-control pos-input-tip ptax" id="ptax_<?php echo $i; ?>" count="<?php echo $i; ?>">
                        	<?php
							foreach ($tax_rates as $tax) {
								
							?>
                        	<option value="<?php echo $tax->id; ?>" <?php if($getTax->id == $tax->id){ echo 'selected'; }else{ echo ''; } ?> data-id="<?php echo $tax->rate; ?>"><?php echo $tax->name; ?></option>
                            <?php
							}
							?>
                        </select>
                        </div>
                    </td>
                    <?php
                        $default_tax = ($final_val) * ($getTax->rate / 100);
                       
                        
                        $taxtype ='';
                        $style = 'block';
                        if($getTaxType != 0){      
                            $colspan = 1;
                            $style = 'block';
                            $taxtype = lang("exclusive");
                        }
                        else{
                            $colspan = 1;
                            $style = 'none';
                            $taxtype = lang("inclusive");
                        }

                        if($getTaxType != 0){
                           $final_val = ($final_val + $default_tax);
                           $final_val = $final_val+$dinserice_charge_amt;
                           $sub_val = $final_val;
                        }
                        else{

                            $sub_val = $final_val/(($default_tax/$final_val)+1);
                           $sub_val =  $sub_val;

                            $default_tax = ($sub_val) * ($getTax->rate / 100);

                           $final_val = $sub_val+$default_tax+$dinserice_charge_amt; 
                           $final_val = $final_val;
                        } 
                        ?>

                    <td colspan="<?php echo $colspan; ?>" align="right" 
                    >   
                     <span style="text-align: right;" id="ttax2_old_<?php echo $i; ?>"> <?php echo '('.$taxtype.' - '.$getTax->name.')' ?></span>
                        
                    </td>

                    <td class="right_td text-right" style="padding: 5px 10px;font-size: 14px; font-weight:bold;">
                        <input type="hidden" name="split[<?php echo $i;?>][tax_amount]" id="tax_amount_<?php echo $i; ?>" value="<?php echo $default_tax; ?>">
                        
                        <span id="ttax2_<?php echo $i; ?>"><?php echo $this->sma->formatMoney($default_tax); ?></span>
                        </td>
                    </tr>

                   <tr>
                   		<td colspan="4"><?=lang('grand_total')?></td>
                   		<td class="right_td text-right" style="padding: 5px 10px;font-size: 14px; font-weight:bold;">
                   		<span id="gtotal_<?php echo $i; ?>">
				  		<?php
                        echo $this->sma->formatMoney($final_val);

                         /*echo $this->site->FinalamountRound(($final_val) + $default_tax);*/ ?>
                   		</span>
                   		<input type="hidden" name="split[<?php echo $i;?>][grand_total]" value="<?php echo (($sub_val)); ?>" id="grand_total_<?php echo $i;?>">

                        <input type="hidden" name="split[<?php echo $i;?>][round_total]" value="<?php echo (($sub_val)); ?>" id="round_total_<?php echo $i;?>">

                   <?php 
                    if(!empty($order_data))
                        {?>
                            <input type="hidden" name="split[<?php echo $i;?>][reference_no]" value="<?php echo $order_data['reference_no']; ?>">

                            <input type="hidden" name="split[<?php echo $i;?>][customer_id]" value="<?php echo $order_data['customer_id']; ?>">

                            <input type="hidden" name="split[<?php echo $i;?>][customer]" value="<?php echo $order_data['customer']; ?>">  

                            <input type="hidden" name="split[<?php echo $i;?>][biller_id]" value="<?php echo $order_data['biller_id']; ?>">  

                            <input type="hidden" name="split[<?php echo $i;?>][biller]" value="<?php echo $order_data['biller']; ?>">  
                                                                                     
                        <?php 
                        } ?>
                        </td>
                        </tr>
                   </tbody>
                   </table>
                   
                   </div>
				   <?php
             	 } 
          		}
                else{
                        admin_redirect("pos/order_table");
                    }
   				?>     
        </div>
        
        
        <div class="btn-group btn-group-justified pos-grid-nav">
           
        </div>
     
    </div>
   
</div>
</div>
</div>


<div style="clear:both;"></div>
</div>

<div id="cp" class="single_split_table">

<div id="cpinner">
<div class="quick-menu">
<div id="proContainer">
    <div id="ajaxrecipe">
        <div id="bbq">
        	
            <div class="clearfix"></div>
            <?php 
				$current_days = date('l');
				$buyxgetx = $this->site->getBBQbuyxgetxDAYS($current_days);
				
                if(!empty($order_bbq))
                {
                    $DaywiseDiscount = $this->site->getBBQDaywiseDiscountforpos($order_bbq->bbq_menu_id);   

                    if($order_bbq->bbq_menu_id !=1){
                        $lobsterdiscount = $this->site->getBBQlobsterDAYS($current_days);                        
                        $adult = 0; 
                        $child = 0; 
                        $kids = 0; 
                        if($lobsterdiscount->minimum_cover <= $order_bbq->number_of_adult && $order_bbq->number_of_adult != 0){
                            
                            $adult = $this->site->CalculationBBQlobster($order_bbq->number_of_adult,$order_bbq->adult_price,$lobsterdiscount->discount_apply_type,$lobsterdiscount->discount_type,$lobsterdiscount->adult_discount_val);

                            $child = $this->site->CalculationBBQlobster($order_bbq->number_of_child,$order_bbq->child_price,$lobsterdiscount->discount_apply_type,$lobsterdiscount->discount_type,$lobsterdiscount->adult_discount_val);

                            $kids = $this->site->CalculationBBQlobster($order_bbq->number_of_kids,$order_bbq->kids_price,$lobsterdiscount->discount_apply_type,$lobsterdiscount->discount_type,$lobsterdiscount->adult_discount_val);
                        }

                    }else{
                        $adult = $this->site->CalculationBBQbuyget($buyxgetx->adult_buy, $buyxgetx->adult_get, $order_bbq->number_of_adult);
                        $child = $this->site->CalculationBBQbuyget($buyxgetx->child_buy, $buyxgetx->child_get, $order_bbq->number_of_child);
                        $kids = $this->site->CalculationBBQbuyget($buyxgetx->kids_buy, $buyxgetx->kids_get, $order_bbq->number_of_kids);
                    }

					
					
			?>
            <div class="row">
            <div class="col-xs-12">
            	<p></p>
            	<table  class="table table-bordered table-striped copytabl single_table_bg">
                	<thead>
                    	<tr>
                        	<th>Details</th>
                            <th>Price</th>
                            <th>Covers</th>                            
                            <th>Discount (Cover)</th>
                            <th>Day Discount</th>
                            <th>Subtotal</th>
                            
                        </tr>
                    </thead>
                    <tbody>
                    	<tr>
                        	<td>Adult</td>
                            <td><span><?=$this->sma->formatMoney($order_bbq->adult_price)?></span><input type="hidden" name="adult_price" value="<?=$this->sma->formatDecimal($order_bbq->adult_price)?>" id="adult_price"></td>
                            <td><span><?=$order_bbq->number_of_adult?></span><input type="hidden" name="number_of_adult" value="<?=$order_bbq->number_of_adult?>" id="number_of_adult"></td>
                            <td><span id="adult"><?= $adult ?></span> 
							<input type="hidden" name="adult_days" id="adult_days" value="<?=$current_days?>">
                            <input type="hidden" name="adult_buyx" id="adult_buyx" value="<?=$buyxgetx->adult_buy ?>">
                            <input type="hidden" name="adult_getx" id="adult_getx" value="<?=$buyxgetx->adult_get?>">
                            <input type="hidden" name="adult_discount_cover" id="adult_discount_cover" value="<?= $adult ?>">
                            </td>
                            <?php

                            $adult_discoint =0;
                            if($order_bbq->bbq_menu_id !=1){
                                $adultsubprice = $order_bbq->adult_price * $order_bbq->number_of_adult;
                                if($lobsterdiscount->minimum_cover <= $order_bbq->number_of_adult && $order_bbq->number_of_adult != 0){
                                         $adult_discoint = $this->site->CalculationBBQlobster($order_bbq->number_of_adult,$order_bbq->adult_price,$lobsterdiscount->discount_apply_type,$lobsterdiscount->discount_type,$lobsterdiscount->adult_discount_val);
                                }
                                $adult_subprice = ($order_bbq->adult_price * $order_bbq->number_of_adult);
                            }else{
                                 $adultsubprice = $order_bbq->adult_price * $order_bbq->number_of_adult;
                                 $adult_subprice = ($order_bbq->adult_price * $order_bbq->number_of_adult);
                                 $adult_discoint = $order_bbq->adult_price * $adult;
                            
                            }
                             ?>
                            <td class="text-right">
                            <?php
                               $adult_daywisediscount =0;
                                if($DaywiseDiscount->adult_discount_val !=0){
                                    $amount = $adultsubprice - $adult_discoint;
                                    $adult_daywisediscount = $this->site->CalculationBBQDauwiseDiscount($amount,$DaywiseDiscount->discount_type, $DaywiseDiscount->adult_discount_val);
                                } 
                            ?>
                            <span><?=$this->sma->formatMoney($adult_daywisediscount)?></span>
                             <input type="hidden" name="adult_daywise_discount" value="<?=$this->sma->formatDecimal($adult_daywisediscount)?>" id="adult_daywisediscount">
                             <!-- <input type="text" name="adult_subprice" class="numberonly number-pad"  maxlength="10" value="<?=$this->sma->formatDecimal($adultsubprice)?>" id="adult_subprice"> -->

                         </td>

                            <td class="text-right"><span><?=$this->sma->formatMoney($adult_subprice)?></span> <input type="hidden" name="adult_subprice" value="<?=$this->sma->formatDecimal($adult_subprice)?>" id="adult_subprice"></td>
                        </tr>
                        <tr>
                        	<td>Child</td>
                            <td><span><?=$this->sma->formatMoney($order_bbq->child_price)?></span><input type="hidden" name="child_price" value="<?=$this->sma->formatDecimal($order_bbq->child_price)?>" id="child_price"></td>
                            <td><span><?=$order_bbq->number_of_child?></span><input type="hidden" name="number_of_child" value="<?=$order_bbq->number_of_child?>" id="number_of_child"></td>
                            <td><span id="child"><?= $child ?></span> 
							<input type="hidden" name="child_days" id="child_days" value="<?=$current_days?>">
                            <input type="hidden" name="child_buyx" id="child_buyx" value="<?=$buyxgetx->child_buy ?>">
                            <input type="hidden" name="child_getx" id="child_getx" value="<?=$buyxgetx->child_get?>">
                            <input type="hidden" name="child_discount_cover" id="child_discount_cover" value="<?= $child ?>">
                            </td>
                            <?php
                                $child_discoint = 0;
                            if($order_bbq->bbq_menu_id !=1){
                                $childsubprice = $order_bbq->child_price * $order_bbq->number_of_child;
                                if($lobsterdiscount->minimum_cover <= $order_bbq->number_of_child && $order_bbq->number_of_child != 0){
                                        $child_discoint = $this->site->CalculationBBQlobster($order_bbq->number_of_child,$order_bbq->child_price,$lobsterdiscount->discount_apply_type,$lobsterdiscount->discount_type,$lobsterdiscount->child_discount_val);
                                }
                                $child_subprice = ($order_bbq->child_price * $order_bbq->number_of_child); 
                            }else{
                                $childsubprice = $order_bbq->child_price * $order_bbq->number_of_child;
                                $child_subprice = ($order_bbq->child_price * $order_bbq->number_of_child); 
                                $child_discoint = $order_bbq->child_price * $child;
                            } 
                            ?>
                            <td class="text-right">
                                <?php
                                   $child_daywisediscount =0;
                                    if($DaywiseDiscount->child_discount_val !=0){
                                        $amount = $childsubprice - $child_discoint;
                                        $child_daywisediscount = $this->site->CalculationBBQDauwiseDiscount($amount,$DaywiseDiscount->discount_type, $DaywiseDiscount->child_discount_val);
                                    } 
                                ?>
                                <span><?=$this->sma->formatMoney($child_daywisediscount)?></span>
                                 <input type="hidden" name="child_daywise_discount" value="<?=$this->sma->formatDecimal($child_daywisediscount)?>" id="child_daywisediscount">
                            </td>
                            <td class="text-right"><span><?=$this->sma->formatMoney($child_subprice)?></span> <input type="hidden" name="child_subprice" value="<?=$this->sma->formatDecimal($child_subprice)?>" id="child_subprice"></td>
                        </tr>
                        <tr>
                        	<td>Kids</td>
                            <td><span><?=$this->sma->formatMoney($order_bbq->kids_price)?></span><input type="hidden" name="kids_price" value="<?=$this->sma->formatDecimal($order_bbq->kids_price)?>" id="kids_price"></td>
                            <td><span><?=$order_bbq->number_of_kids?></span><input type="hidden" name="number_of_kids" value="<?=$order_bbq->number_of_kids?>" id="number_of_kids"></td>
                            <td><span id="adult"><?= $kids ?></span> 
                            <input type="hidden" name="kids_days" id="kids_days" value="<?=$current_days?>">
                            <input type="hidden" name="kids_buyx" id="kids_buyx" value="<?=$buyxgetx->kids_buy ?>">
                            <input type="hidden" name="kids_getx" id="kids_getx" value="<?=$buyxgetx->kids_get?>">
                            <input type="hidden" name="kids_discount_cover" id="kids_discount_cover" value="<?= $kids ?>">
							</td>
                            <?php 
                            $kids_discoint = 0;
                            if($order_bbq->bbq_menu_id !=1){
                                 $kidssubprice = ($order_bbq->kids_price * $order_bbq->number_of_kids);
                                 if($lobsterdiscount->minimum_cover <= $order_bbq->number_of_kids && $order_bbq->number_of_kids != 0){
                                        $kids_discoint = $this->site->CalculationBBQlobster($order_bbq->number_of_kids,$order_bbq->kids_price,$lobsterdiscount->discount_apply_type,$lobsterdiscount->discount_type,$lobsterdiscount->kids_discount_val);
                                }                                
                                 $kids_subprice = ($order_bbq->kids_price * $order_bbq->number_of_kids);                                
                            }else{
                                $kidssubprice = ($order_bbq->kids_price * $order_bbq->number_of_kids);
                                 $kids_subprice = ($order_bbq->kids_price * $order_bbq->number_of_kids);
                                $kids_discoint = $order_bbq->kids_price * $kids;
                            }
                            ?>
                            <td class="text-right">
                                <?php
                                   $kids_daywisediscount =0;
                                    if($DaywiseDiscount->kids_discount_val !=0){
                                        $amount = $kidssubprice - $kids_discoint;
                                        $kids_daywisediscount = $this->site->CalculationBBQDauwiseDiscount($amount,$DaywiseDiscount->discount_type, $DaywiseDiscount->kids_discount_val);
                                    } 
                                ?>
                                <span><?=$this->sma->formatMoney($kids_daywisediscount)?></span>
                                 <input type="hidden" name="kids_daywise_discount" value="<?=$this->sma->formatDecimal($kids_daywisediscount)?>" id="kids_daywisediscount">
                            </td>

                            <td class="text-right"><span><?=$this->sma->formatMoney($kids_subprice)?></span> <input type="hidden" name="kids_subprice" value="<?=$this->sma->formatDecimal($kids_subprice)?>" id="kids_subprice"></td>
                        </tr>
                        <tr>
                        	<td colspan="5">Total Covers</td>
                            <td class="text-right"><span><?=$order_bbq->number_of_adult + $order_bbq->number_of_child + $order_bbq->number_of_kids?></span> <input type="hidden" name="number_of_covers" value="<?=$order_bbq->number_of_adult + $order_bbq->number_of_child + $order_bbq->number_of_kids?>" id="number_of_covers"></td>
                        </tr>
                        <tr>
                            <input type="hidden" name="bbq_cover_discount" id="bbq_cover_discount" value="<?php echo  $adult_discoint + $child_discoint +  $kids_discoint ?>">
                            <input type="hidden" name="bbq_daywise_discount" id="bbq_daywise_discount"  value="<?php echo  $adult_daywisediscount + $child_daywisediscount + $kids_daywisediscount ?>">
                            <input type="hidden" name="bbq_daywise_discount_id" id="bbq_daywise_discount_id"  value="<?php echo$DaywiseDiscount->id ?>">
                        	<td colspan="5">Total Amount</td>
                            <td class="text-right"><span><?=$this->sma->formatMoney( ($adultsubprice) + ($childsubprice) + ($kidssubprice) )?></span> <input type="hidden" name="total_amount" value="<?=$this->sma->formatDecimal( ($adultsubprice) + ($childsubprice) + ($kidssubprice) )?>" id="total_amount"></td>
                        </tr>
                        <?php if($adult_discoint + $child_discoint + $kids_discoint > 0){
                         ?>
                        <tr>                            
                            <td colspan="5">Total Cover Discount</td>
                            <td class="text-right"><span><?=$this->sma->formatMoney( ($adult_discoint) + ($child_discoint) + ($kids_discoint) )?></span></td>
                        </tr>
                       <?php } ?>

                        <?php if($adult_daywisediscount + $child_daywisediscount + $kids_daywisediscount > 0){ ?>
                        <tr>                            
                            <td colspan="5">Total Day Wise Discount</td>
                            <td class="text-right"><span><?=$this->sma->formatMoney( ($adult_daywisediscount) + ($child_daywisediscount) + ($kids_daywisediscount) )?></span></td>
                        </tr>
                       <?php } ?>

                        <?php
                        $tot_cover_dis =  $adult_discoint + $child_discoint + $kids_discoint;
                        $tot_cover_day_dis =  $adult_daywisediscount + $child_daywisediscount + $kids_daywisediscount;
						$final_val = $this->sma->formatDecimal((($adult_subprice) + ($child_subprice) + ($kids_subprice))-$tot_cover_dis-$tot_cover_day_dis);
                        
                         if($Settings->bbq_discount !='none'){  
						?>
                        <tr>
                        	<td colspan="4">BBQ Discount</td>
                            <td>
                            	<?php
								if($Settings->bbq_discount=='bbq'){
								?>
                                 <input type="hidden" name="bbq_in_discount" value="<?= $discount_select['bbq'] ?>">
                                 <input type="hidden" name="bbq_discount_id" id="bbq_discount_id" value="">
                            	<select class="form-control" name="bbq_discount" id="bbq_discount">
                                	<option value="0">NO</option>
                                    <?php
									foreach($bbq_discount as $discount){
									?>
                                   	<option data-id="<?= $discount->id ?>" <?php if($discount_select['bbq'] == $discount->id){ echo 'selected'; }else{ echo ''; } ?> value="<?=$discount->discount_type == 'amount' ? $discount->discount : $discount->discount.'%' ?>"><?=$discount->name?></option>
                                    <?php
									}
									?>
                                </select>
                                <?php
								}elseif($Settings->bbq_discount=='manual'){
								?>
                                <input type="text" name="bbq_discount" autocomplete="off" class="form-control kb-pad" id="bbq_discount">
                                <?php
								}
								?>
                            </td>
                            <td class="text-right"><span id="bbq_discount_text"><?=$this->sma->formatMoney(0)?></span><input type="hidden" name="bbq_discount_amount" value="<?=$this->sma->formatDecimal(0)?>" id="bbq_discount_amount"></td>
                        </tr>                      
                      <?php } ?>

                        <?php 
                            if(!empty($order_bbq->customer_id))
                            {
                               $custimerid = $order_bbq->customer;                                                    
                               $check = $this->site->Check_bbq_birthday_discount_isavail($order_data['customer_id']);
                            if($this->pos_settings->birthday_enable_bbq != 0  && $this->pos_settings->birthday_discount_for_bbq != 0){
                            if($check == true){
                             ?>
                                <tr>
                                    <td colspan="5"> <?=lang("birthday_discount");?> </td>
                                    <?php
                                     $birday = $this->pos_settings->birthday_discount_for_bbq;                             
                                        $birthday_val = $this->site->calculateDiscount($birday, $final_val);
                                        $final_val   = $final_val - $birthday_val;
                                    ?>
                                     <input type="hidden" name="birthday_discount_for_bbq" autocomplete="off" class="form-control kb-pad pos-input-tip  birthday-discount birthday_discount_for_bbq"  value="<?php echo $birthday_val;?>">

                                     <input type="hidden" name="after_birthday_discount_for_bbq" autocomplete="off" class="form-control kb-pad pos-input-tip  birthday-discount" id="after_birthday_discount_for_bbq" value="<?php echo $final_val;?>">
                                    <td  class="right_td text-right" style="padding: 5px 10px;font-size: 14px; font-weight:bold;"> <span class="birthday_discount_for_bbq"> <?php echo $this->sma->formatMoney($birthday_val); ?>  </span> </td>
                                </tr>
                        <?php } } } ?>

                    <?php 
                    /*bbq service Charge 10-04-2019 Sivan start*/ 
                    $bbq_serice_charge_amt =0;                   
                    if($this->pos_settings->default_service_charge !=0 && $this->pos_settings->service_charge_option !=0){
                        $ServiceHideShow = "visible";
                        $Servicedisplay = "contents";
                     }else{
                        $ServiceHideShow = "hidden";
                        $Servicedisplay = "none";
                    }
                    ?>
                    <tr style="visibility: <?php echo $ServiceHideShow;?>;display:<?php echo $Servicedisplay; ?> ">
                        
                        <?php 
                            $AllServiceCharge = $this->site->getAllSericeCharges();
                            $ServiceCharge = $this->site->getServiceChargeByID($this->pos_settings->default_service_charge);
                            $Service_Charge_Text = $ServiceCharge->name;  
                        ?>              
                        <td colspan="5"><span style="text-align: right;" id="bbq_servicecha_"> <?php echo $Service_Charge_Text; ?></span></td>      
                        <select style="display: none" name="bbq_service_charge" class="form-control pos-input-tip bbq_service_charge" id="bbq_service_charge">
                        <?php
                            foreach ($AllServiceCharge as $Service) {  ?>
                            <option value="<?php echo $Service->id; ?>" <?php if($ServiceCharge->id == $Service->id){ echo 'selected'; }else{ echo ''; } ?> data-id="<?php echo $Service->rate; ?>"><?php echo $Service->name; ?></option>
                        <?php }   ?>
                        </select>                    
                        
                        <?php         
                        if($this->pos_settings->default_service_charge !=0 && $this->pos_settings->service_charge_option !=0){           
                           $bbq_serice_charge_amt = ($final_val) * ($ServiceCharge->rate / 100);
                        }
                        ?>
                    </td>
                    <td class="right_td text-right" style="padding: 5px 10px;font-size: 14px; font-weight:bold;">
                        <input type="hidden" name="bbq_service_amount" id="bbq_service_amount" value="<?php echo $bbq_serice_charge_amt; ?>">                        
                        <span id="bbq_spansericechargeamt"><?php echo $this->sma->formatMoney($bbq_serice_charge_amt); ?></span>
                    </td>                    
                    </tr>
                    <tr></tr>
                    <!-- bbq service Charge =10-04-2019 end -->

                       <?php                       
						$getTaxType = $this->pos_settings->tax_type;
						$HideShow = "visible";
						$display = "contents";
						if($this->pos_settings->default_tax != 1 && $getTaxType != 0)
						{       
							$HideShow = "visible";
							$display = "contents";
						}
						else{
							$HideShow = "hidden";
							$display = "none";
						}
					  ?>
                      <tr style="visibility: <?php echo $HideShow;?>;display:<?php echo $display; ?> ">
                        <td colspan="3">Tax</td>
                      	<?php
						$getTax = $this->site->getTaxRateByID($this->pos_settings->default_tax);
						$default_tax = ($final_val) * ($getTax->rate / 100);
						$taxtype ='';
                        $style = 'block';
                        if($getTaxType != 0){      
                            $colspan = 1;
                            $style = 'block';
                            $taxtype = lang("exclusive");
                        }
                        else{
                            $colspan = 1;
                            $style = 'none';
                            $taxtype = lang("inclusive");
                        }

                        if($getTaxType != 0){
                           $final_val = ($final_val + $default_tax+$bbq_serice_charge_amt);
                           $final_val = $final_val;
                           $sub_val = $final_val;
                        }
                        else{
							$sub_val = $final_val/(($default_tax/$final_val)+1);
							$sub_val =  $sub_val;
							$default_tax = ($sub_val) * ($getTax->rate / 100);
							$final_val = $sub_val+$default_tax+$bbq_serice_charge_amt; 
							$final_val = $final_val;
						}
                          
						?>
                        <td>
                        <input type="hidden" name="tax_type" id="tax_type" value="<?php echo $getTaxType; ?>">

                        <select style="display: none"  name="ptax" class="form-control pos-input-tip ptax" id="ptax">
                        	<?php
							foreach ($tax_rates as $tax) {
								
							?>
                        	<option value="<?php echo $tax->id; ?>" <?php if($getTax->id == $tax->id){ echo 'selected'; }else{ echo ''; } ?> data-id="<?php echo $tax->rate; ?>"><?php echo $tax->name; ?></option>
                            <?php
							}
							?>
                        </select>
                        <span style="text-align: right;"> <?php echo '('.$taxtype.' - '.$getTax->name.')' ?></span></td>
                        <td class="text-right"><span id="tax_text" ><?php echo $this->sma->formatMoney($default_tax); ?></span><input type="hidden" name="tax_amount" id="tax_amount" value="<?php echo $this->sma->formatDecimal($default_tax); ?>"></td>
                      </tr> 
                      
                      <tr>
                      		<td colspan="5">Grand Total</td>
                            <td class="text-right"><span id="gtotal_text"><?php echo $this->sma->formatMoney($final_val); ?></span><input type="hidden" name="gtotal" id="gtotal" value="<?=$this->sma->formatDecimal($final_val)?>"</td>
                            
                      </tr>
                      
                      <input type="hidden" name="reference_no" value="<?php echo $order_bbq->reference_no; ?>">
                    <input type="hidden" name="customer_id" value="<?php echo $order_bbq->customer_id; ?>">
                    <input type="hidden" name="customer" value="<?php echo $order_bbq->customer; ?>">  
                    <input type="hidden" name="biller_id" value="<?php echo $order_bbq->biller_id; ?>">  
                    <input type="hidden" name="biller" value="<?php echo $order_bbq->biller; ?>"> 
                    <input type="hidden" name="warehouse_id" value="<?php echo $order_bbq->warehouse_id; ?>"> 
                                            
                    </tbody>
                </table>
            </div>
            </div>
            <?php
				}else{
                        admin_redirect("pos/order_bbqtable");
				}
   				?>     
        </div>
        
        
        <div class="btn-group btn-group-justified pos-grid-nav">
           
        </div>
     
    </div>
   
</div>
</div>
</div>


<div style="clear:both;"></div>
</div>

<?php
echo form_hidden('remove_image','No');
echo form_hidden('action', 'CONSOLIDATED-SUBMIT');
echo form_close();
?>
</div>





<div style="clear:both;"></div>
</div>
<div style="clear:both;"></div>

</div>
</div>

</div>

<?php
$this->load->view($this->theme . 'pos/pos_footer');
?>


<div id="order_tbl"><span id="order_span"></span>
<table id="order-table" class="prT table table-striped" style="margin-bottom:0;" width="100%"></table>
</div>
<div id="bill_tbl"><span id="bill_span"></span>
<table id="bill-table" width="100%" class="prT table table-striped" style="margin-bottom:0;"></table>
<table id="bill-total-table" class="prT table" style="margin-bottom:0;" width="100%"></table>
<span id="bill_footer"></span>
</div>
<div class="modal fade in" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
aria-hidden="true"></div>
<div class="modal fade in" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2"
aria-hidden="true"></div>
<div id="modal-loading" style="display: none;">
<div class="blackbg"></div>
<div class="loader"></div>
</div>


        <div class="modal-footer adjust">
                   <button class="btn btn-block btn-lg btn-primary" id="submit-sale"><?=lang('send');?></button>
                    
        </div>

<?php unset($Settings->setting_id, $Settings->smtp_user, $Settings->smtp_pass, $Settings->smtp_port, $Settings->update, $Settings->reg_ver, $Settings->allow_reg, $Settings->default_email, $Settings->mmode, $Settings->timezone, $Settings->restrict_calendar, $Settings->restrict_user, $Settings->auto_reg, $Settings->reg_notification, $Settings->protocol, $Settings->mailpath, $Settings->smtp_crypto, $Settings->corn, $Settings->customer_group, $Settings->srampos_username, $Settings->purchase_code);?>


<script>
var recipe_variant = 0, shipping = 0, p_page = 0, per_page = 0, pro_limit = <?= $pos_settings->pro_limit; ?>,
brand_id = 0, obrand_id = 0, cat_id = "<?=$pos_settings->default_category?>", ocat_id = "<?=$pos_settings->default_category?>", sub_cat_id = 0, osub_cat_id,
count = 1, an = 1, DT = <?=$Settings->default_tax_rate?>,
recipe_tax = 0, invoice_tax = 0, recipe_discount = 0, order_discount = 0, total_discount = 0, total = 0, total_paid = 0, grand_total = 0,
KB = <?=$pos_settings->keyboard?>, tax_rates =<?php echo json_encode($tax_rates); ?>,service_charge =<?php echo json_encode($service_charge); ?>;
var protect_delete = <?php if (!$Owner && !$Admin) {echo $pos_settings->pin_code ? '1' : '0';} else {echo '0';} ?> /*billers = <?= json_encode($posbillers); ?>, biller = <?= json_encode($posbiller); ?>;*/

var username = '<?=$this->session->userdata('username');?>', order_data = '', bill_data = '';

function widthFunctions(e) {
var wh = $(window).height(),
lth = $('#left-top').height(),
lbh = $('#left-bottom').height();
$('#item-list').css("height", wh - 360);
$('#item-list').css("min-height", 205);
$('#left-middle').css("height", wh - lth - lbh - 102);
$('#left-middle').css("min-height", 278);
$('#recipe-list').css("height", wh - lth - lbh - 107);
$('#recipe-list').css("min-height", 278);
}
$(window).bind("resize", widthFunctions);

$(document).ready(function () {
$('#print_order').click(function () {
if (count == 1) {
bootbox.alert('<?=lang('x_total');?>');
return false;
}
<?php if ($pos_settings->remote_printing != 1) { ?>
printOrder();
<?php } else { ?>
Popup($('#order_tbl').html());
<?php } ?>
});


$('#print_bill').click(function () {
if (count == 1) {
bootbox.alert('<?=lang('x_total');?>');
return false;
}
<?php if ($pos_settings->remote_printing != 1) { ?>
printBill();
<?php } else { ?>
Popup($('#bill_tbl').html());
<?php } ?>
});
});


<?php if ($pos_settings->remote_printing == 1) { ?>
function Popup(data) {
var mywindow = window.open('', 'sma_pos_print', 'height=500,width=300');
mywindow.document.write('<html><head><title>Print</title>');
mywindow.document.write('<link rel="stylesheet" href="<?=$assets?>styles/helpers/bootstrap.min.css" type="text/css" />');
mywindow.document.write('</head><body >');
mywindow.document.write(data);
mywindow.document.write('</body></html>');
mywindow.print();
mywindow.close();
return true;
}
<?php }
?>
</script>

<script type="text/javascript">

function formatMoney(x, symbol) {
    if(!symbol) { symbol = ""; }
    if(site.settings.sac == 1) {
        return (site.settings.display_symbol == 1 ? site.settings.symbol : '') +
            ''+formatSA(parseFloat(x).toFixed(site.settings.decimals)) +
            (site.settings.display_symbol == 2 ? site.settings.symbol : '');
    }
    var fmoney = accounting.formatMoney(x, symbol, site.settings.decimals, site.settings.thousands_sep == 0 ? ' ' : site.settings.thousands_sep, site.settings.decimals_sep, "%s%v");
    return (site.settings.display_symbol == 1 ? site.settings.symbol : '') +
        fmoney +
        (site.settings.display_symbol == 2 ? site.settings.symbol : '');
}


$(document).on('change', '#bbq_discount', function(){
	var ds = $(this).val();
	//var bbq_dis_id = $(this 'option:selected', this).attr('data-id');
        var bbq_dis_id = $('option:selected', this).attr('data-id');
	var unit_price = $('#total_amount').val();
    var bbq_cover_discount = $('#bbq_cover_discount').val();
    var bbq_daywise_discount = $('#bbq_daywise_discount').val();

    bbq_cover_discount = bbq_cover_discount ? bbq_cover_discount :0;
    bbq_daywise_discount = bbq_daywise_discount ? bbq_daywise_discount :0;
    var bbq_price = (unit_price - bbq_cover_discount-bbq_daywise_discount); 
    // alert(bbq_price);       
	var taxtype = $('#tax_type').val();
	var pr_tax = $('#ptax').children(":selected").data("id");
	var pr_tax_val = 0;
	if (ds.indexOf("%") !== -1) {            
		var pds = ds.split("%");
		if (!isNaN(pds[0])) {
            console.log('ss'+bbq_price);
		     input_discount = parseFloat(((bbq_price) * parseFloat(pds[0])) / 100);
		} else {
		    input_discount = parseFloat(ds);
		}
	}
	else{            
		input_discount = parseFloat(ds);
	}
	var final_amount = parseFloat(bbq_price) - parseFloat(input_discount);
    // console.log(final_amount);
    var bbq_birthday = 0;
    <?php 
    if(!empty($order_bbq->customer_id))
      {
        $custimerid = $order_bbq->customer_id; 
      }  
    ?>  
var final_discount =  parseFloat(input_discount)+parseFloat(bbq_cover_discount)+parseFloat(bbq_daywise_discount);
    var check = <?php echo json_encode($this->site->Check_bbq_birthday_discount_isavail($custimerid )); ?>;     
    var bdydis = <?php echo json_encode($this->pos_settings->birthday_discount); ?>;  

    if(check != 0){
        var disbirthday = parseFloat(unit_price) - (parseFloat(final_discount));
            bbq_birthday = parseFloat(((disbirthday) * parseFloat(bdydis)) / 100);
            
        var final_amount = parseFloat(unit_price) - parseFloat(final_discount)-parseFloat(bbq_birthday);
        var final_amount_before_input =(parseFloat(unit_price)) - parseFloat(final_discount)-parseFloat(bbq_birthday);
    }
    else
    {    
        var final_amount = parseFloat(unit_price) - (parseFloat(final_discount));
        var final_amount_before_input =(parseFloat(unit_price)) - (parseFloat(bbq_cover_discount+bbq_daywise_discount));
    }
/*service charge*/
var bbq_service_charge_val = 0;
<?php
if($this->pos_settings->default_service_charge !=0 && $this->pos_settings->service_charge_option !=0){  ?>
var bbq_servicecharge = $('#bbq_service_charge').children(":selected").data("id");

var bbq_service_charge_val = 0;
if (bbq_servicecharge !== null && bbq_servicecharge != 0) {
    $.each(service_charge, function () {                       
    bbq_service_charge_val = parseFloat(((final_amount) * parseFloat(bbq_servicecharge)) / 100);        
    });
}
<?php } ?>
/*service charge*/


	if (pr_tax != 0) {
		$.each(tax_rates, function () {                       
		pr_tax_val = parseFloat(((final_amount) * parseFloat(pr_tax)) / 100);
		pr_tax_rate = (pr_tax) + '%';                
		});
	}
	var final_tax;
	var final_tax_amount;
	if(taxtype != 0)
    {
		final_tax = parseFloat(pr_tax_val);
        final_tax_amount = parseFloat(final_tax);
        final_amount = parseFloat(final_amount+final_tax+bbq_service_charge_val);
		
	}else{
		
		var sub_val = final_amount/((pr_tax_val/final_amount)+1);
        final_tax_amount = sub_val * (pr_tax / 100);
        final_amount = sub_val+final_tax_amount+bbq_service_charge_val; 
	}
	
	if(final_amount >= 0 ){
		$('#bbq_discount_text').text(formatMoney(input_discount));
		$('#bbq_discount_amount').val(formatDecimal(input_discount));
		
        $('.birthday_discount_for_bbq').text(formatDecimal(bbq_birthday));
        $('.birthday_discount_for_bbq').val(formatDecimal(bbq_birthday));

		$('#tax_text').text(formatMoney(final_tax_amount));
		$('#tax_amount').val(formatDecimal(final_tax_amount));
		
		$('#gtotal_text').text(formatMoney(final_amount));
		$('#gtotal').val(formatDecimal(final_amount));
		$('#bbq_discount_id').val(bbq_dis_id);

        $('#bbq_service_amount').val(formatDecimal(bbq_service_charge_val));
        $('#bbq_spansericechargeamt').text(formatMoney(bbq_service_charge_val));

		
	}else{
		bootbox.alert('Discount should not grater than total');
	}
	
});

$(document).on('click', '#submit-sale', function () {
    
$(this).text('<?=lang('loading');?>').attr('disabled', true);
$('#pos-autosplitbill-form').submit();
return false;

});
$(document).on('click', '#print-sale', function () {
    $obj = $(this);
$(this).text('<?=lang('loading');?>').attr('disabled', true);
$data = $('#pos-autosplitbill-form').serialize();
$.ajax({
        type: 'POST',
                url: '<?=admin_url('pos/billprint');?>',
                //dataType: "json",
                data: $('#pos-autosplitbill-form').serialize(),
                success: function (data) {
                    $obj.text('<?=lang('print_only');?>').attr('disabled', false);
                    Popup(data);
                }
});

return false;

});

$(document).on('change', '.ptax', function () {

var find_attr = $(this).attr('count');    
var subtotal  = 0;
var tax_amount  = $('#tax_amount_'+ find_attr).val();
var unit_price = 0;
var off_discount = $('#off_discount_'+ find_attr).val();
var discount = $('#item_discounts_'+ find_attr).val();

if(typeof off_discount == "undefined")
{ 
discount = discount;
subtotal  = $('#item_dis_'+ find_attr).text();
unit_price = parseFloat($('#item_dis_'+ find_attr).val());
}
else{
discount = off_discount;
subtotal  = $('#tds1_'+ find_attr).text();
unit_price = parseFloat($('#tds1_'+ find_attr).text());
}

/*var ds = $('#order_discount_input_'+ find_attr).val() ? $('#order_discount_input_'+ find_attr).val() : '0';*/

var pr_tax = $('#ptax_'+find_attr).children(":selected").data("id");

    if (ds.indexOf("%") !== -1) {            
        var pds = ds.split("%");
        if (!isNaN(pds[0])) {
        item_discount = parseFloat(((unit_price) * parseFloat(pds[0])) / 100);
        } else {
        item_discount = parseFloat(ds);
        }
    } else {            
        item_discount = parseFloat(ds);
    }
    var final_discount =  parseFloat(item_discount)  + parseFloat(discount);
    var final_discount_amount = parseFloat(unit_price) - parseFloat(item_discount);

    var pr_tax_val = 0;
    if (pr_tax !== null && pr_tax != 0) {
        $.each(tax_rates, function () {                       
        pr_tax_val = parseFloat(((final_discount_amount) * parseFloat(pr_tax)) / 100);
        pr_tax_rate = (pr_tax) + '%';                
        });
    }
    var final_tax = parseFloat(pr_tax_val);
    $('#tax_amount_'+ find_attr).val(parseFloat(final_tax));
    $('#ttax2_'+ find_attr).text(parseFloat(final_tax));
    $('#ttax2_old_'+ find_attr).text(parseFloat(final_tax));    
});

$(document).on('change', '.order_discount_input', function () {
    $this_obj = $(this);
    var find_attr = $(this).attr('count');
    var subtotal  = 0;
    var tax_amount  = $('#tax_amount_'+ find_attr).val();
    var taxtype  = $('#tax_type_'+ find_attr).val();    
    var unit_price = 0;
    var off_discount = $('#off_discount_'+ find_attr).val();
    var discount = $('#item_discounts_'+ find_attr).val();
    


    if(typeof off_discount == "undefined")
    { 
        discount = discount;
        subtotal  = $('#subtotal_'+ find_attr).val();
        unit_price = parseFloat($('#item_dis_'+ find_attr).val());
    }
    else{
        discount = parseFloat(off_discount)+parseFloat(discount);
        subtotal  = $('#subtotal_'+ find_attr).val();
        unit_price = parseFloat($('#offer_discount_'+ find_attr).val());
    }


    //if ($('#order_discount_input_'+find_attr).val()!='' && $('#max-allow-discount-percent_'+find_attr).length>0 && $('#max-allow-discount-percent_'+find_attr).val()!='') {
    //    $val = $('#max-allow-discount-percent_'+find_attr).val();
    //    $gtotal = subtotal;//$('#grand_total_'+find_attr).val();
    //    $disval = $('#order_discount_input_'+find_attr).val();
    //
    //    if ($disval.indexOf('%')!=-1) {
    //        if ($val<parseFloat($disval)) { bootbox.alert('Discount should not be Greater than '+$val+'%'); $('#order_discount_input_'+find_attr).val('');return false; }
    //    }else{
    //        $f_val = ($disval*100)/$gtotal;
    //        
    //        if ($val<$f_val) { bootbox.alert('Discount should not be Greater than '+$val+'%');$('#order_discount_input_'+find_attr).val(''); return false; }
    //    }
    //}

    var ds = $('#order_discount_input_'+ find_attr).val() ? $('#order_discount_input_'+ find_attr).val() : '0';

    var pr_tax = $('#ptax_'+find_attr).children(":selected").data("id");


var values = [];
$("input[name='item_offer_dis[]']").each(function() {
    values.push($(this).val());
});



 var off_discounts = []; 
    $('input[name="item_offer_dis[]"]').each(function(){
     
      off_discounts.push($(this).val());
    });

var recipeids  = $('#recipeids_'+ find_attr).val();
var recipeqtys  = $('#recipeqtys_'+ find_attr).val();
var off_discounts  = off_discounts;
input_discount = 0;
var divide = "<?php echo $bils;?>";
<?php if($Settings->customer_discount=='customer') : ?>
if(ds !=0){
    
        $.ajax({
                type: 'POST',
                url: '<?=admin_url('pos/calculate_customerdiscount');?>',
                dataType: "json",
                 async : false,
                data: {
                    recipeids: recipeids,recipeqtys: recipeqtys,discountid:$this_obj.val(),divide: divide
                },
                success: function (data) {
                    /*$(this).removeClass('ui-autocomplete-loading');*/
                    console.log(data);
                    input_discount += data;
                    //$.each(data,function(i,v){
                    //   $qty = $('#recipe-qty-'+v.id).val();
                    //   $('#recipe-input-discount-'+v.id).val(v.disamt*$qty);
                    //   $t_discount = parseFloat($('#recipe-item-discount-'+v.id).val()) + parseFloat($('#recipe-offer-discount-'+v.id).val()) + parseFloat($('#recipe-input-discount-'+v.id).val());
                    //   $('#recipe-total-discount-'+v.id).val($t_discount);
                    //   input_discount += v.disamt*$qty;
                    //});
                }
           });
}else{
    $recipeids = recipeids.split(',');
    console.log($recipeids)
    $.each($recipeids,function(i,v){console.log(v)
        $('[id^=recipe-input-discount-]').val('');
        $t_discount = parseFloat($('#recipe-item-discount-'+v).val()) + parseFloat($('#recipe-offer-discount-'+v).val());
        $('#recipe-total-discount-'+v).val($t_discount);
    })
   
} 
<?php elseif($Settings->customer_discount=='manual') : ?>
if (ds.indexOf("%") !== -1) {            
    var pds = ds.split("%");
    if (!isNaN(pds[0])) {
    input_discount = parseFloat(((unit_price) * parseFloat(pds[0])) / 100);
    } else {
    input_discount = parseFloat(ds);
    }
}
else{            
    input_discount = parseFloat(ds);
}
<?php endif;?>
//input_discount = 0;
var final_discount =  parseFloat(input_discount)+parseFloat(discount);

var final_amount = parseFloat(subtotal) - parseFloat(final_discount);

var birthday = 0;

<?php 
if(!empty($order_data))
  {
    $custimerid = $order_data['customer_id']; 
  }  
?>  
       
var check = <?php echo json_encode($this->site->Check_birthday_discount_isavail($custimerid )); ?>;     
var bdydis = <?php echo json_encode($this->pos_settings->birthday_discount); ?>;  

if(check != 0){
    var disbirthday = parseFloat(subtotal) - (parseFloat(final_discount));
        birthday = parseFloat(((disbirthday) * parseFloat(bdydis)) / 100);
        
    var final_amount = parseFloat(subtotal) - parseFloat(final_discount)-parseFloat(birthday);
    var final_amount_before_input =(parseFloat(subtotal)) - parseFloat(final_discount)-parseFloat(birthday);
}
else
{    
    var final_amount = parseFloat(subtotal) - (parseFloat(final_discount));
    var final_amount_before_input =(parseFloat(subtotal)) - (parseFloat(discount));
}

/*service charge*/
var dine_service_charge_val = 0;
<?php
if($this->pos_settings->default_service_charge !=0 && $this->pos_settings->service_charge_option !=0){  ?>
var servicecharge = $('#dine_service_charge_'+find_attr).children(":selected").data("id");

var dine_service_charge_val = 0;
if (servicecharge !== null && servicecharge != 0) {
    $.each(service_charge, function () {                       
    dine_service_charge_val = parseFloat(((final_amount) * parseFloat(servicecharge)) / 100);        
    });
}
<?php } ?>
/*service charge*/

var pr_tax_val = 0;
if (pr_tax !== null && pr_tax != 0) {
    $.each(tax_rates, function () {                       
    pr_tax_val = parseFloat(((final_amount) * parseFloat(pr_tax)) / 100);
    pr_tax_rate = (pr_tax) + '%';                
    });
}
    var final_tax;
    var final_tax_amount;
    
    
    if(taxtype != 0)
    {
        final_tax = parseFloat(pr_tax_val);
        final_tax_amount = parseFloat(final_tax);
        final_amount = parseFloat(final_amount+final_tax+dine_service_charge_val);
        finalamount = parseFloat(final_amount);
        sub_val = parseFloat(finalamount);
    }
    else
    {   
        sub_val = final_amount/((pr_tax_val/final_amount)+1);        
        final_tax_amount = sub_val * (pr_tax / 100);
        final_amount = sub_val+final_tax_amount+dine_service_charge_val; 
    }

   

if(final_amount >= 0 ){
    $('#tdis_'+ find_attr).val(formatDecimal(input_discount));
    $('#tds_'+ find_attr).text(formatMoney(input_discount));

    $('.birthday_discount_'+ find_attr).text(formatDecimal(birthday));
    $('.birthday_discount_'+ find_attr).val(formatDecimal(birthday));

    $('#ttax2_'+ find_attr).text(formatMoney(final_tax_amount));
    $('#tax_amount_'+ find_attr).val(formatDecimal(final_tax_amount));
    $('#gtotal_'+ find_attr).text(formatMoney(final_amount));
    $('#grand_total_'+ find_attr).val(formatDecimal(sub_val));
    $('#round_total_'+ find_attr).val(formatDecimal(sub_val));
    $('#dine_service_amount_'+ find_attr).val(formatDecimal(dine_service_charge_val));
    $('#dine_spansericechargeamt_'+ find_attr).text(formatMoney(dine_service_charge_val));

}else{
    bootbox.alert('Discount should not grater than total');
    $('#order_discount_input_'+ find_attr).val('');
    final_amount = formatDecimal(unit_price);        
    $('#tdis_'+ find_attr).val(formatDecimal(input_discount));
    $('#tds_'+ find_attr).text(formatMoney(input_discount));

    $('.birthday_discount_'+ find_attr).text(formatDecimal(birthday));
    $('.birthday_discount_'+ find_attr).val(formatDecimal(birthday));

    $('#ttax2_'+ find_attr).text(formatMoney(final_tax_amount));
    $('#gtotal_'+ find_attr).text(formatMoney(final_amount));
    $('#grand_total_'+ find_attr).val(formatDecimal(sub_val));
    $('#round_total_'+ find_attr).val(formatDecimal(sub_val));
    $('#dine_service_amount_'+ find_attr).val(formatDecimal(dine_service_charge_val));
    $('#dine_spansericechargeamt_'+ find_attr).text(formatMoney(dine_service_charge_val));
    return false;	
}
});

function formatDecimal(x, d) {

if (!d) { d = 2; }
return parseFloat(accounting.formatNumber(x, d, '', '.'));

}
$('.table_id').click(function () {
var order_type = $('#order_type').val();        
var table_id = $(this).val();
var url = '<?php echo  admin_url('pos') ?>';
//window.location.href= url +'/?order='+order_type+'&table='+table_id;  
$('#modal-loading').show();

$.ajax({
type: "get",
url: "<?=admin_url('pos/tablecheck');?>",
data: {table_id: table_id, order_type: order_type},
dataType: "json",
success: function (data) {

if(data.status == 'success'){
window.location.href= url +'/order_table/?table='+table_id; 
}else{
window.location.href= url +'/?order='+order_type+'&table='+table_id;    
}
}

}).done(function () {
$('#modal-loading').hide();
});

});


<?php
if($discount_select['dine'] != 0){
?>
$(document).ready(function(e) {
    $('.order_discount_input').trigger('change');
});
<?php
}
?>
<?php
if($discount_select['bbq'] != 0){
?>
$(document).ready(function(e) {
    $('#bbq_discount').trigger('change');
});
<?php
}
?>

</script>
<script type="text/javascript" src="<?=$assets?>js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/perfect-scrollbar.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/select2.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/custom.js"></script>
<script type="text/javascript" src="<?=$assets?>js/jquery.calculator.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/bootstrapValidator.min.js"></script>
<script type="text/javascript" src="<?=$assets?>pos/js/plugins.min.js"></script>
<script type="text/javascript" src="<?=$assets?>pos/js/parse-track-data.js"></script>
<!-- <script type="text/javascript" src="<?=$assets?>pos/js/pos.ajax.js"></script> -->
<script type="text/javascript" src="<?=$assets?>js/dragscrollable.js"></script>
<script type="text/javascript" src="<?=$assets?>js/dragscroll.js"></script>
<script>
$('.kb-pad').keyboard({
restrictInput: true,
preventPaste: true,
autoAccept: true,
alwaysOpen: false,
openOn: 'click',
usePreview: false,
layout: 'custom',
maxLength: 12,
display: {
'b': '\u2190:Backspace',
},
customLayout: {
'default': [
'1 2 3 4',
'5 6 7 8  ',
'9 0 % {b}',
' {accept} {cancel}'
]
}
});
$("#delivery_person_id").select2();
</script>
<?php
if ( ! $pos_settings->remote_printing) {
?>
<script type="text/javascript">
var order_printers = <?= json_encode($order_printers); ?>;
function printOrder() {
$.each(order_printers, function() {
var socket_data = { 'printer': this,
'logo': (biller && biller.logo ? biller.logo : ''),
'text': order_data };
$.get('<?= admin_url('pos/p/order'); ?>', {data: JSON.stringify(socket_data)});
});
return false;
}

function printBill() {
var socket_data = {
'printer': <?= json_encode($printer); ?>,
'logo': (biller && biller.logo ? biller.logo : ''),
'text': bill_data
};
$.get('<?= admin_url('pos/p'); ?>', {data: JSON.stringify(socket_data)});
return false;
}
</script>
<?php
} elseif ($pos_settings->remote_printing == 2) {
?>
<script src="<?= $assets ?>js/socket.io.min.js" type="text/javascript"></script>
<script type="text/javascript">
socket = io.connect('http://localhost:6440', {'reconnection': false});

function printBill() {
if (socket.connected) {
var socket_data = {'printer': <?= json_encode($printer); ?>, 'text': bill_data};
socket.emit('print-now', socket_data);
return false;
} else {
bootbox.alert('<?= lang('pos_print_error'); ?>');
return false;
}
}

var order_printers = <?= json_encode($order_printers); ?>;
function printOrder() {
if (socket.connected) {
$.each(order_printers, function() {
var socket_data = {'printer': this, 'text': order_data};
socket.emit('print-now', socket_data);
});
return false;
} else {
bootbox.alert('<?= lang('pos_print_error'); ?>');
return false;
}
}
</script>
<?php

} elseif ($pos_settings->remote_printing == 3) {

?>
<script type="text/javascript">
try {
socket = new WebSocket('<?php echo PRINTER_SOCKET; ?>');
socket.onopen = function () {
console.log('Connected');
return;
};
socket.onclose = function () {
console.log('Not Connected');
return;
};
} catch (e) {
console.log(e);
}

var order_printers = <?= $pos_settings->local_printers ? "''" : json_encode($order_printers); ?>;
function printOrder() {
if (socket.readyState == 1) {

if (order_printers == '') {

var socket_data = { 'printer': false, 'order': true,
'logo': (biller && biller.logo ? site.base_url+'assets/uploads/logos/'+biller.logo : ''),
'text': order_data };
socket.send(JSON.stringify({type: 'print-receipt', data: socket_data}));

} else {

$.each(order_printers, function() {
var socket_data = { 'printer': this,
'logo': (biller && biller.logo ? site.base_url+'assets/uploads/logos/'+biller.logo : ''),
'text': order_data };
socket.send(JSON.stringify({type: 'print-receipt', data: socket_data}));
});

}
return false;
} else {
bootbox.alert('<?= lang('pos_print_error'); ?>');
return false;
}
}

function printBill() {
if (socket.readyState == 1) {
var socket_data = {
'printer': <?= $pos_settings->local_printers ? "''" : json_encode($printer); ?>,
'logo': (biller && biller.logo ? site.base_url+'assets/uploads/logos/'+biller.logo : ''),
'text': bill_data
};
socket.send(JSON.stringify({type: 'print-receipt', data: socket_data}));
return false;
} else {
bootbox.alert('<?= lang('pos_print_error'); ?>');
return false;
}
}
</script>
<?php
}
?>
<script>
$('#subcategory-list, #scroller').dragscrollable({
dragSelector: 'button', 
acceptPropagatedEvent: false
});
</script>
<script>
//      $('#left-middle ,#recipe-list').dragscrollable({
//          dragSelector: 'table', 
//          acceptPropagatedEvent: false
//      });
//      $(document).ready(function(){
//          $("#left-middle ,#recipe-list").dragscrollable({
//              axis:"x",
//
//          });
//      });
</script>
<script type="text/javascript">
/*$('.sortable_table tbody').sortable({
containerSelector: 'tr'
});*/

</script>
<script type="text/javascript" charset="UTF-8"><?=$s2_file_date?></script>
<?php
if (isset($print) && !empty($print)) {
/* include FCPATH.'themes'.DIRECTORY_SEPARATOR.$Settings->theme.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'pos'.DIRECTORY_SEPARATOR.'remote_printing.php'; */
include 'remote_printing.php';
}
?>



</body>
</html>
