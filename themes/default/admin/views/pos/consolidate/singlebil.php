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
<?php if($this->pos_settings->font_family ==0) { ?>
    <link rel="stylesheet" href="<?=$assets?>styles/theme.css" type="text/css"/>
<?php }elseif ($this->pos_settings->font_family ==1) { ?><!-- for kimmo client and font family AKbalthom-KhmerNew  -->
<link rel="stylesheet" href="<?=$assets?>styles/theme_for_kimmo.css" type="text/css"/>
<?php } ?>
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
<style type="text/css">
 @page  
        { 
            size: auto;   /* auto is the initial value */ 
            /* this affects the margin in the printer settings */ 
            margin: -5mm 5mm 5mm 5mm;  
        }
        
    .ui-keyboard div {
    max-width: 300px!important;
}
</style>
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
 if($this->Settings->user_language == 'english' ) { 
         $this->load->view($this->theme . 'pos/pos_header');   
         }else{// for kimmo 
            $this->load->view($this->theme . 'pos/pos_header_kimmo'); 
         }
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
echo admin_form_open("pos/billing_all?order_type=".$order_type."&bill_type=1&bils=".$bils."&table=".$table_id."&splits=".$split_id, $attrib);?>


<input type="hidden" name="order_type" value="<?php echo $order_type; ?>">
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
                <table id="example3" class="table table-bordered table-striped copytabl single_table_bg">
                    <thead>
                        <tr class = "clickable" >    
                        <th><?=lang('cancel')?></th>                        
                            <th style="width: 250px!important;"><?=lang('sale_item')?></th>
                            <th><?=lang('price')?></th>
                            <th><?=lang('sale_qty')?></th>
                            <?php if($Settings->manual_item_discount == 1) { ?>
                                <th><?=lang("item_discount");?></th>
                            <?php } ?>
                            <th><?=lang('cus_discount(%)')?></th>
                            <?php if($pos_settings->manual_and_customer_discount_consolid_percentage_display_option == 1) { ?>
                                <th><?=lang("total_discount(%)");?></th>
                            <?php } ?>
                            <th><?=lang('discount')?></th>
                            <th><?=lang('subtotal')?></th>
                            
                        </tr>
                    </thead>
                <tbody class = "autobilldt"  style="cursor: pointer;">
                <?php
                    $variant_id='';
                    $recipeid_data = array();
					$recipeid_variant_data = array();
                    $recipeid_qty = array();
                    $recipe_variantid = array();
                    $manualitem_discount = array();
					
                    foreach($order_item as $salesitem) 
                    {
                        /*echo "<pre>";
                        print_r($salesitem);*/
                        if($salesitem->variant!=''){  
                            $variant_id = $salesitem->recipe_variant_id;
                        }
                        $recipeid_data[] = $salesitem->recipe_id;
						$recipeid_variant_data[] = $salesitem->recipe_id.$variant_id;
                        $recipeid_qty[] = $salesitem->quantity;

                        $manualitem_discount[] = $salesitem->manual_item_discount;

                        $r_total_discount[$i] = array();
                        $r_subtotal[$i] = array();
			            $discount = $this->site->discountMultiple($salesitem->recipe_id);

                        $khmer_name = $this->site->getrecipeKhmer($salesitem->recipe_id); 

                        $addondetails = $this->site->getAddonByRecipeidAndOrderitemid($salesitem->recipe_id,$salesitem->id);
                              $itemaddonamt =0;
                              if(!empty($addondetails)) :
                                 foreach ($addondetails as $key => $addons) {                                     
                                    $itemaddonamt +=$addons->price*$addons->qty;
                                }                                 
                        endif;

                        $discount_value = '';
                        $manualitem_discount_amt[$i][] = $salesitem->manual_item_discount;

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
                                <button class="btn btn-block btn-lg btn-danger" type="button" id="cancel-item" data-order-id="<?php echo $salesitem->id ?>" OnClick="CancelOrderItem('<?php echo $salesitem->item_status;  ?>', '<?php echo $salesitem->id;  ?>', '<?php echo $split_id;?>','<?php echo $this->GP['pos-cancel_order_remarks'];?>','<?php echo $salesitem->quantity; ?>');" ><i class="fa fa-trash-o"></i></button>
                            </td>
                            
                        	<td>
                                <?php

                                $variant ='';$variant_name=''; $variant_id='';
                                    if($salesitem->variant!="" || $salesitem->variant!=0){                                          
                                        $recipe_variantid[] = $salesitem->recipe_variant_id;
                                        $vari = explode('|',$salesitem->variant);
                                        $variant = $salesitem->variant;
                                        $variant_id = $salesitem->recipe_variant_id;
                                        $variant_name='[<span class="pos-variant-name">'.$variant.'</span>]';
                                    }else{                                        
                                        $recipe_variantid[] = '';
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
							<?php echo $recipe_name.$variant_name;
                            $addondetails = $this->site->getAddonByRecipeidAndOrderitemid($salesitem->recipe_id,$salesitem->id);
                             if(!empty($addondetails)) :
                                 foreach ($addondetails as $key => $addons) { ?>
                                    <br> <span style="color: #0e34ef;font-weight: bold;"> <?= $addons->addon_name ?> (<?= $addons->qty ?> X  <?=  $addons->price ?> ) &nbsp;= <?=  $this->sma->formatMoney($addons->price*$addons->qty) ?></span>
                                    
                                 <?php }
                             endif;
                             if($pos_settings->item_comment_price_option != 0 && $salesitem->comment !='') :
                                  ?>
                                   
                                    
                                 <?php 
                             endif;
                            ?>
                            <input type="hidden" name="split[<?php echo $i;?>][recipe_name][]" value="<?php echo $salesitem->recipe_name;?>">

                        <input type="hidden" name="split[<?php echo $i;?>][recipe_id][]" value="<?php echo $salesitem->recipe_id;?>" class="split-recipe-id recipe_id">
                        <input type="hidden" name="split[<?php echo $i;?>][recipe_code][]" value="<?php echo $salesitem->recipe_code;?>">
                        <input type="hidden" name="split[<?php echo $i;?>][recipe_type][]" value="<?php echo $salesitem->recipe_type;?>">
                        	</td>
                        	<td class="text-right">
							<?php echo $this->sma->formatMoney($salesitem->unit_price);?><br>
                            <?php if($itemaddonamt != 0) : ?>
                            <span style="color: #0e34ef;text-align: right;font-weight: bold;"><?php echo $this->sma->formatMoney($itemaddonamt);?></span>
                            <?php endif; ?>
                            <input type="hidden" name="split[<?php echo $i;?>][unit_price][]" value="<?php echo $salesitem->unit_price;?>" class="unit_price">

                            <input type="hidden" name="split[<?php echo $i;?>][recipe_variant][]" value="<?php echo $variant;?>">
                            <input type="hidden" name="split[<?php echo $i;?>][recipe_variant_id][]" class="variant_id" value="<?php echo $variant_id;?>">
                            </td>
                        	<!-- <td class="text-right">
                            <?php echo $salesitem->quantity;?>
                            <input type="hidden" name="split[<?php echo $i;?>][quantity][]" value="<?php echo $salesitem->quantity;?>" id="recipe-qty-<?=$salesitem->recipe_id?>" class ="quantity" >
                            </td> -->
							
                             <td><div class="qty_number">
							 <?php // for buy x get y complimentry 
							 if($salesitem->unit_price !=0){   ?>
                                <span class="minus ">-</span>
							 <?php  } ?>
                                <span class="text_qty"><?php echo $salesitem->quantity;?> </span>
                                <input type="hidden" name="split[<?php echo $i;?>][quantity][]" value="<?php echo $salesitem->quantity;?>" id="recipe-qty-<?=$salesitem->recipe_id?>" class ="quantity " >

                                <input type="hidden" name="split[<?php echo $i;?>][original_quantity][]" value="<?php echo $salesitem->quantity;?>" id="original_quantity-<?=$salesitem->recipe_id?>" class ="original_quantity" >

                                <input type="hidden" name="split[<?php echo $i;?>][order_item_id][]" value="<?php echo $salesitem->id;?>" id="order_item_id-<?=$salesitem->id?>" class ="order_item_id" >
								 <?php  if($salesitem->unit_price !=0){   ?>
                                   <span class="plus">+</span></div>
								    <?php  } ?>
                             </td>

                            <?php 
                            $totcolspan=0;
                            $tot = 3;
                            if($pos_settings->manual_and_customer_discount_consolid_percentage_display_option == 1) {
                                $totcolspan=1; 
                                $tot = 1.+$tot;
                            }
                             
                                if($Settings->manual_item_discount == 1) {                                   
                                    $tot = 1.+$tot;
                                }
                                
                                $display = "block";                                 
                                $colspan =5.+$totcolspan;
                                $colspan1 =6.+$totcolspan;
                             if($Settings->manual_item_discount == 1) { 
                                $colspan =6.+$totcolspan;
                                $colspan1 =7.+$totcolspan;
                                    $display = "block";
                                }else{
                                    $colspan =5.+$totcolspan;
                                    $colspan1 =6.+$totcolspan;
                                    $display = "none";
                                } 
                            ?>
                            <td style="display:<?php echo $display;?>;">
                                <!-- style="width: 40%;float: left;" -->
                                    <input style="border: none;background:transparent;box-shadow: none;outline: none;"
                                     type="text" name="split[<?php echo $i;?>][manual_item_discount_val][]" value="<?php echo $salesitem->manual_item_discount_val;?>" class ="manual_item_discount_val form-control text-right kb-pad1" count="<?php echo $i; ?>" autocomplete="off" >
                                  
                                    <input style="width: 40%;float: right;display: none;" type="hidden" name="split[<?php echo $i;?>][manual_item_discount][]" value="<?php echo $salesitem->manual_item_discount;?>" class ="form-control pos-input-tip manual_item_discount text-right" readonly="readonly">

                                    <?php
                                     $discount = $this->site->discountMultiple($salesitem->recipe_id);
                                     $per = 0;
                                     if (strpos($salesitem->manual_item_discount_val, '%') !== false) {
                                            $per =  str_replace("%","",$salesitem->manual_item_discount_val);
                                            // $per =$salesitem->manual_item_discount_val;
                                     }else{
                                        if($salesitem->manual_item_discount_val !=0){
                                            $per = $this->site->amount_to_percentage($salesitem->manual_item_discount_val, $price_total);
                                        }
                                     }
                                    ?>
                                     <input style="width: 40%;float: right;" type="hidden" name="split[<?php echo $i;?>][manual_item_discount_per_val][]" value="<?php echo $this->sma->formatDecimal($per) ?>" class ="form-control pos-input-tip manual_item_discount_per_val text-right" id="manual_item_discount_per_val<?=$salesitem->recipe_id.$variant_id?>" readonly="readonly">
                            </td>
                            <td class="text-right">
                            <span class="item_cus_dis_val item_cus_dis" id="item_cus_dis<?=$salesitem->recipe_id.$variant_id?>">0</span>
                            <input type="hidden" name="split[<?php echo $i;?>][item_cus_dis][]" value="" id="item_cus_dis-<?=$salesitem->recipe_id.$variant_id?>" class ="item_cus_dis" >
                            </td>
                            <?php if($pos_settings->manual_and_customer_discount_consolid_percentage_display_option == 1) { ?>
                            <td class="text-right">
                                <span class="manual_and_customer_discount_consolid_percentage_display_option" id="manual_and_customer_discount_consolid_percentage_display_option<?=$salesitem->recipe_id.$variant_id?>"><?php echo $per ?></span>
                            </td>
                            <?php  } ?>
                        	<td class="text-right">
				             <span class="recipe-item-discount-<?=$salesitem->recipe_id?>"> <?php echo $this->sma->formatDecimal($dis); ?> </span>			
                            <input type="hidden" name="split[<?php echo $i;?>][item_discount][]"  value="<?php echo $dis; ?>" id="recipe-item-discount-<?=$salesitem->recipe_id?>" class="item_discount">
                            <input type="hidden" name="split[<?php echo $i;?>][item_discount_id][]"  value="<?php echo $discount[0]; ?>">
                            <input type="hidden" name="split[<?php echo $i;?>][item_discount_val][]" id="recipe-item-discount-val-<?=$salesitem->recipe_id?>" value="<?php echo $discount[1]; ?>">
                            <input type="hidden" name="split[<?php echo $i;?>][item_discount_type][]" id="recipe-item-discount-type-<?=$salesitem->recipe_id?>" value="<?php echo $discount[2]; ?>">
                            <?php 
                            $TotalDiscount = $this->site->TotalDiscount();                            
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
                                    $offer_dis = $totdiscount1;
                                    $sub = $price_total - $dis - $offer_dis;  
                                }        

                            ?>
                            <input type="hidden" name="item_offer_dis[]" value="<?php echo $offer_dis;?>" id="recipe-offer-discount-<?=$salesitem->recipe_id?>">
                            <input type="hidden" name="split[<?php echo $i;?>][item_input_dis][]" value="0" id="recipe-input-discount-<?=$salesitem->recipe_id?>">
                            <input type="hidden" name="split[<?php echo $i;?>][all_discount][]"  value="" id="recipe-total-discount-<?=$salesitem->recipe_id?>">
                            <input type="hidden" name="split[<?php echo $i;?>][item_tax][]" value="<?php echo $salesitem->item_tax;?>">
                        	</td>
                        
                        	<td class = "text-right ">
                                <span class="item_subtotal"><?php echo $this->sma->formatMoney($price_total-$salesitem->manual_item_discount);?></span>
								
                                  <input type="hidden" name="split[<?php echo $i;?>][discounted_subtotal][]" value="<?php echo $this->sma->formatDecimal($price_total-$salesitem->manual_item_discount) ?>" class="discounted_subtotal">

                                <input type="hidden" name="split[<?php echo $i;?>][subtotal][]" value="<?php echo $price_total;?>" class="item_subtotal1">


                                <input type="hidden" name="split[<?php echo $i;?>][addonsubtotal][]" value="<?php echo $itemaddonamt;?>" class="addonsubtotal">

                                <input type="hidden" name="split[<?php echo $i;?>][addon_id][]" value="<?php echo $salesitem->addon_id;?>" class="addon_id">

                                <input type="hidden" name="split[<?php echo $i;?>][addon_qty][]" value="<?php echo $salesitem->addon_qty;?>" class="addon_qty">

                            </td>
                          </tr>
						  <?php  } 
                               $recipeids =  implode(',',$recipeid_data);
                               $recipeidvariantdataids =  implode(',',$recipeid_variant_data);
                               $recipeqtys =  implode(',',$recipeid_qty);
                               $recipevariantids =  implode(',',$recipe_variantid);
                           ?>
					</tbody>
                   <tbody>
               		<tr>
                        <!-- <input type="hidden" name="split[<?php echo $i;?>][manual_discount_amount]" value="<?php echo array_sum($manualitem_discount); ?>" class="total_manual_discount_amount"> -->
                    	<td colspan="3" class="text-right"><?=lang('total_item')?></td>
                        <td class="right_td text-center">
                            <?php if(isset($discount['unique_discount'])) : ?>
                            <input type="hidden" name="unique_discount" value="1">
                                <?php endif; ?>
							<?php echo $total_count; ?>
                            <input type="hidden" name="split[<?php echo $i;?>][total_item]" value="<?php echo $total_count; ?>">
               			</td>
                    
                    	<td colspan="<?php echo $tot-1; ?>" class="text-right"><?=lang('total')?></td>
                        <td class="right_td text-right">
                            <span class="total_price"><?php echo $this->sma->formatMoney(array_sum($subtotal[$i])-array_sum($manualitem_discount_amt[$i]));?></span>
                      	<input type="hidden" name="split[<?php echo $i;?>][total_price]" value="<?php echo array_sum($subtotal[$i]);?>" id="subtotal_<?php echo $i; ?>" class="total_price total_price_textbox">

                        <input type="hidden" name="split[<?php echo $i;?>][all_item_total]" value="<?php echo array_sum($subtotal[$i]);?>" id="all_item_total_<?php echo $i; ?>" class="all_item_total ">


                  		</td>
                        <input type="hidden" id="manual_discount_amount_<?php  echo $i; ?>" name="split[<?php echo $i;?>][manual_discount_amount]" value="<?php echo array_sum($manualitem_discount_amt[$i]); ?>" class="total_manual_discount_amount">
                  	</tr>
<!-- don't delete bellow tr -->
                  <!--   <tr style="display: ">
                        <td colspan="<?php echo $colspan;?>">
                        <?=lang("item_total_discount", "item_total_discount");?>
                        </td>
                        <td>
                         <span class="total_manual_discount_amount"><?php echo $this->sma->formatMoney(array_sum($manualitem_discount_amt[$i])); ?></span>
                         <input type="hidden" id="manual_discount_amount_<?php  echo $i; ?>" name="split[<?php echo $i;?>][manual_discount_amount]" value="<?php echo array_sum($manualitem_discount_amt[$i]); ?>" class="total_manual_discount_amount">
                        </td>
                        <td class="right_td text-right" style="padding: 5px 10px;font-size: 14px; font-weight:bold;">
                            <span class="after_manual_dis"><?php echo $this->sma->formatMoney(array_sum($subtotal[$i]) - array_sum($manualitem_discount_amt[$i])); ?></span>
                            <input type="hidden" id="after_manual_dis_textbox_<?php  echo $i; ?>"  name="split[<?php echo $i;?>][after_manual_dis_textbox]" value="<?php echo array_sum($subtotal[$i]) - array_sum($manualitem_discount_amt[$i]); ?>" class="after_manual_dis_textbox">
                        </td>
                    </tr> -->
<!-- don't delete above tr -->              
                    <?php 
                    $HideShow = "visible";
                    $display = "contents";
                        if(array_sum($total_discount[$i]) != 0){
                            $HideShow = "visible";
                            $display = "contents";
                        }else{
                            $HideShow = "hidden";
                            $display = "none";
                        }
                    ?>
                   
                    <tr style="visibility: <?php echo $HideShow;?>;display:<?php echo $display;?>;">
                    	<td colspan="<?php echo $colspan;?>" class="text-right"><?=lang("discount", "order_discount_input");?></td>
                        <td>
                        <span class="itemdiscounts"><?php echo $this->sma->formatMoney(array_sum($total_discount[$i])); ?></span>
                         <input type="hidden" id="item_discounts_<?php  echo $i; ?>"  name="split[<?php echo $i;?>][itemdiscounts]" value="<?php echo array_sum($total_discount[$i]); ?>" class="itemdiscounts">
                        </td>

                        <td class="right_td text-right" style="padding: 5px 10px;font-size: 14px; font-weight:bold;">
                            <span class="after_item_or_manual_dis"><?php echo $this->sma->formatMoney(array_sum($subtotal[$i]) - array_sum($total_discount[$i]) - array_sum($manualitem_discount_amt[$i])); ?>
                            </span>
                            <input type="hidden" id="item_dis_<?php  echo $i; ?>"  name="split[<?php echo $i;?>][item_dis]" value="<?php echo array_sum($subtotal[$i]) - array_sum($total_discount[$i]); ?>" class="after_item_or_manual_dis_textbox"> 
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
                                <td colspan="'.$colspan.'" class="text-right">'.lang('offer_discount').'
                                </td>
                                <td>
                                '.$totdiscount.'

                                <input type="hidden" name="split['.$i.'][tot_dis_id]" value="'.$TotalDiscount[0].'">
                                <input type="hidden" name="split['.$i.'][tot_dis_value]" value="'.$totdiscount.'" class="tot_dis_value">
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

                            <td colspan="<?php echo $colspan-1;?>" class="text-right">
                            <?=lang("customer_discount", "customer_discount");?>
                            </td>
                       
                            <td colspan="2">                            
                            <div class="">
                                <input type="hidden" name="split[<?php echo $i;?>][recipeids]" id="recipeids_<?php echo $i; ?>" value="<?php echo $recipeids; ?>" >
                                <input type="hidden" id="recipeidvariantdataids_<?php echo $i; ?>" value="<?php echo $recipeidvariantdataids; ?>" >

                               <input type="hidden" name="split[<?php echo $i;?>][recipeqtys]" id="recipeqtys_<?php echo $i; ?>" value="<?php echo $recipeqtys; ?>" > 

                               <input type="hidden" name="split[<?php echo $i;?>][$recipevariantids]" id="recipevariantids_<?php echo $i; ?>" value="<?php echo $recipevariantids; ?>" >

                                <?php if($Settings->customer_discount=='customer') : ?>
                                 <input type="hidden" name="dine_in_discount" value="<?= $discount_select['dine'] ?>">
                                 
                                <select style="display: "  name="split[<?php echo $i;?>][order_discount_input]" class="form-control pos-input-tip order_discount_input" id="order_discount_input_<?php echo $i; ?>" count="<?php echo $i; ?>">
                                 <option value="0">No</option> 
                                    <?php
                                    foreach ($customer_discount as $cusdis) {
                                    ?>
                                    <option value="<?php echo $cusdis->id; ?>" <?php if($discount_select['dine'] == $cusdis->id){ echo 'selected'; }else{ echo ''; } ?> data-id="<?php echo $cusdis->id; ?>"><?php echo $cusdis->name; ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                                <input type="hidden" id="order_discount_input_seletedtext" name="order_discount_input_seletedtext">
                                <?php elseif($Settings->customer_discount=='manual') : ?>
                                <input type="text" name="split[<?php echo $i;?>][order_discount_input]" autocomplete="off" class="form-control kb-pad1 pos-input-tip order_discount_input manual-discount" id="order_discount_input_<?php echo $i; ?>" count="<?php echo $i; ?>">
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
                            <td class="text-right" colspan="<?php echo $colspan;?>"> <?=lang("birthday_discount");?> </td>
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

                    <tr>
                        <td class="text-right" colspan="<?php echo $colspan1;?>"> <?=lang("sub_total");?> </td>
                        <?php                             
                            $final_val   = $final_val - $birthday_val;                                
                        ?>
                         <input type="hidden" name="split[<?php echo $i;?>][subtot]" autocomplete="off" class="form-control kb-pad pos-input-tip  subtot subtot_<?php echo $i; ?>" count="<?php echo $i; ?>" value="<?php echo $final_val;?>">

                        <td  class="right_td text-right" style="padding: 5px 10px;font-size: 14px; font-weight:bold;"> <span class="subtot_<?php echo $i; ?>"> <?php echo $this->sma->formatMoney($final_val); ?>  </span> </td>
                    </tr>


                    <?php 
                    /*service Charge 09-04-2019 Sivan start*/ 
                    
                    $serice_charge_amt =0;                   
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
                        <select style="display: none"  name="split[<?php echo $i;?>][service_charge]" class="form-control pos-input-tip service_charge" id="service_charge_<?php echo $i; ?>" count="<?php echo $i; ?>">
                        <?php
                            foreach ($AllServiceCharge as $Service) {  ?>
                            <option value="<?php echo $Service->id; ?>" <?php if($ServiceCharge->id == $Service->id){ echo 'selected'; }else{ echo ''; } ?> data-id="<?php echo $Service->rate; ?>"><?php echo $Service->name; ?></option>
                        <?php }   ?>
                        </select>                    
                        
                    <?php

                    if($this->pos_settings->default_service_charge !=0 && $this->pos_settings->service_charge_option !=0){
                        $serice_charge_amt = ($final_val) * ($ServiceCharge->rate / 100);   
                    }

                        $Service_Charge_Text = $ServiceCharge->name;                      
                    ?>
                     <span style="text-align: right;" id="servicecha_<?php echo $i; ?>"> <?php echo $Service_Charge_Text; ?></span>
                    </td>

                    <td class="right_td text-right" style="padding: 5px 10px;font-size: 14px; font-weight:bold;">
                        <input type="hidden" name="split[<?php echo $i;?>][service_amount]" id="service_amount_<?php echo $i; ?>" value="<?php echo $serice_charge_amt; ?>">                        
                        <span id="spansericechargeamt_<?php echo $i; ?>"><?php echo $this->sma->formatMoney($serice_charge_amt); ?></span>
                    </td>                    
                    </tr>
                    <tr></tr>
                    <!-- service Charge =09-04-2019 end -->

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
                    <td colspan="<?php echo $colspan1;?>" class="text-right">
                        <?php 
                            $getTax = $this->site->getTaxRateByID($this->pos_settings->default_tax);
                        ?>
                    <!-- <div class="col-lg-6 pull-right"> -->
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
                        <!-- </div> -->
                        <?=lang("tax");?>
                    <!-- </td> -->
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
                           $final_val = $final_val+$serice_charge_amt;
                           $sub_val = $final_val;
                        }
                        else{
                            $sub_val = $final_val/(($default_tax/$final_val)+1);
                            $sub_val =  $sub_val;
                            $default_tax = ($sub_val) * ($getTax->rate / 100);
                           $final_val = $sub_val+$default_tax+$serice_charge_amt; 
                           $sub_val =  $final_val;
                        } 
                        ?>

                    <!-- <td colspan="<?php echo $colspan; ?>" align="right"> -->   
                     <span style="text-align: right;" id="ttax2_old_<?php echo $i; ?>"> <?php echo '('.$taxtype.' - '.$getTax->name.')' ?></span>
                    </td>

                    <td class="right_td text-right" style="padding: 5px 10px;font-size: 14px; font-weight:bold;">
                        <input type="hidden" name="split[<?php echo $i;?>][tax_amount]" id="tax_amount_<?php echo $i; ?>" value="<?php echo $default_tax; ?>">                        
                        <span id="ttax2_<?php echo $i; ?>"><?php echo $this->sma->formatMoney($default_tax); ?></span>
                    </td>
                    </tr>

                   <tr>
                   		<td colspan="<?php echo $colspan1;?>" class="text-right"><?=lang('grand_total')?></td>
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
<?php
echo form_hidden('remove_image','No');
echo form_hidden('action', 'SINGLEBILL-SUBMIT');
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
    <button class="btn btn-block btn-lg btn-primary" id="print-sale"><?=lang('print_only');?></button>
</div>



<div class="modal" id="CancelorderModal" tabindex="-1" role="dialog" aria-labelledby="CancelorderModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close closemodal" data-dismiss="modal"><span aria-hidden="true">
                    <i class="fa fa-2x">&times;</i></span>
                    <span class="sr-only"></span>
                </button>
                <h4 class="modal-title" id="cmModalLabel"></h4>
            </div>
            <div class="modal-body" id="pr_popover_content">
        <div class="row col-sm-12">
            <div class="form-group">
            <label><input type="radio" name="cancel_type" class="radio cancel-type" checked value="out_of_stock"><?=lang('out_of_stock')?></label>
            <label><input type="radio" name="cancel_type" class="radio cancel-type" value="spoiled"><?=lang('spoiled')?></label>
            <label><input type="radio" name="cancel_type" class="radio cancel-type" value="reusable"><?=lang('reusable')?></label>
            </div>
        </div>
        
                <div class="form-group">
                    <?= lang('remarks'); ?>
                    <?= form_textarea('remarks', '', 'class="form-control kb-text" id="remarks" style="height:80px;"'); ?>
                </div>
                <input type="hidden" id="order_item_id" value=""/>
                <input type="hidden" id="split_order" value=""/>
        <input type="hidden" id="cancel_qty" value=""/>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="cancel_orderitem"><?=lang('send')?></button>
            </div>
        </div>
    </div>
</div>


<?php unset($Settings->setting_id, $Settings->smtp_user, $Settings->smtp_pass, $Settings->smtp_port, $Settings->update, $Settings->reg_ver, $Settings->allow_reg, $Settings->default_email, $Settings->mmode, $Settings->timezone, $Settings->restrict_calendar, $Settings->restrict_user, $Settings->auto_reg, $Settings->reg_notification, $Settings->protocol, $Settings->mailpath, $Settings->smtp_crypto, $Settings->corn, $Settings->customer_group, $Settings->srampos_username, $Settings->purchase_code);?>
<script>
var recipe_variant = 0, shipping = 0, p_page = 0, per_page = 0, pro_limit = <?= $pos_settings->pro_limit; ?>,
brand_id = 0, obrand_id = 0, cat_id = "<?=$pos_settings->default_category?>", ocat_id = "<?=$pos_settings->default_category?>", sub_cat_id = 0, osub_cat_id,
count = 1, an = 1, DT = <?=$Settings->default_tax_rate?>,
recipe_tax = 0, invoice_tax = 0, recipe_discount = 0, order_discount = 0, total_discount = 0, total = 0, total_paid = 0, grand_total = 0,
KB = <?=$pos_settings->keyboard?>, tax_rates =<?php echo json_encode($tax_rates);?>; service_charge =<?php echo json_encode($service_charge); ?>;
var protect_delete = <?php if (!$Owner && !$Admin) {echo $pos_settings->pin_code ? '1' : '0';} else {echo '0';} ?> 

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
                var is_chrome = Boolean(mywindow.chrome);
                mywindow.document.write('<html><head><title>Print</title>');
                mywindow.document.write("<style type='text/css' media = 'print'>@page {margin: "+$print_header_space+" 5mm "+$print_footer_space+" 5mm;}</style>");
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

/*var mywindow = window.open('', 'sma_pos_print', 'height=500,width=300');
mywindow.document.write('<html><head><title>Print</title>');
mywindow.document.write('<link rel="stylesheet" href="<?=$assets?>styles/helpers/bootstrap.min.css" type="text/css" />');
mywindow.document.write('</head><body >');
mywindow.document.write(data);
mywindow.document.write('</body></html>');
mywindow.print();
mywindow.close();
return true;*/
}
<?php }
?>
</script>

<script type="text/javascript">
var $print_header_space = '<?=$pos_settings->pre_printed_header?>mm';
var $print_footer_space = '<?=$pos_settings->print_footer_space?>mm';
var pre_printed = '<?=$pos_settings->pre_printed_format?>';

$(document).on('click', '#submit-sale', function () {
    
$(this).text('<?=lang('loading');?>').attr('disabled', true);
$('#pos-autosplitbill-form').submit();
return false;

});

$(document).ready(function(){
    $('.cancel-type').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%'
        });
});

            function CancelOrderItem( status, id, split_id ,$remarks=0,$quantity)
            {   
                $("#order_item_id").val(id);
                $("#split_order").val(split_id);
                
                
            if ($quantity>1) {
                $inputoptions =[];
                for (i = 0; i < $quantity; i++) {
                $v = i+1;
                $inputoptions[i] = {text: $v,value:$v};
                }
            
                bootbox.prompt({ 
                title: "Enter Quantity to cancel",
                inputType:'select',
                inputOptions :$inputoptions,
                callback: function(qty){
                    if (qty!=null) {
                     $cancelQty = qty;
                    if ($quantity==qty) {
                        $cancelQty = 'all';
                    }
                    cancelorderPopup(id ,split_id,$remarks,$cancelQty);
                    $('#cancel_qty').val($cancelQty);
                    }else{
                    
                    }
                   
                }
                });
            }else{
                $cancelQty = 'all';
                cancelorderPopup(id ,split_id,$remarks,$cancelQty);
                $('#cancel_qty').val($cancelQty);
            } 
            

            }
        function cancelorderPopup(id,split_id,$remarks,$cancelQty){
            if($remarks!=0){
                    $('#remarks').val('');
                    
                    $('#CancelorderModal').show();
                }else{
                    $msg = ($cancelQty!='all')?'Are you sure want to cancel '+$cancelQty+' Qty?':'Are you sure want to cancel this item?';
            bootbox.confirm({
            message: $msg,
            buttons: {
                confirm: {
                label: 'Yes',
                className: 'btn-success'
                },
                cancel: {
                label: 'No',
                className: 'btn-danger'
                }
            },
            callback: function (result) {
               console.log(result)
                if (result) {               
                $.ajax({
                    type: "get",
                    url:"<?=admin_url('pos/cancel_order_items');?>",                
                    data: {order_item_id: id, split_id: split_id,cancelqty:$cancelQty},
                    dataType: "json",
                    success: function (data) {
                    if(data.msg == 'success'){
                        
                        location.reload();                                  
                    }else{
                        alert('not update waiter');
                    }
                    }    
                }).done(function () {
                      
                });
                  
                }else{
                           //requestBill(billid);
                }
                
            }
            });
        }
        }
        $('#remarks').on('focus',function(){
        $('#remarks').css('border','1px solid #ccc');
        });
            $(document).on('click','#cancel_orderitem',function(){
                $(this).attr('disabled',true);
                $(this).text('please wait...');

                 var cancel_remarks = $('#remarks').val();
         var cancel_type = $('.cancel-type:checked').val(); 
                 var order_item_id = $('#order_item_id').val(); 
                 var split_id = $("#split_order").val();
                 var $cancelQty = $('#cancel_qty').val();
                 if($.trim(cancel_remarks) != ''){
                    $.ajax({
                        type: "get",
                        url:"<?=admin_url('pos/cancel_order_items');?>",                
                        data: {cancel_type:cancel_type,cancel_remarks: cancel_remarks, order_item_id: order_item_id, split_id: split_id,cancelqty:$cancelQty},
                        dataType: "json",
                        success: function (data) {
                            if(data.msg == 'success'){
                                     $('#CancelorderModal').hide(); 
                                     
                                     location.reload();                                 
                            }else{
                                alert('not update waiter');
                            }
                        }    
                    }).done(function () {
                      
                    });
                 } else{
                $('#remarks').css('border','1px solid red');
         }

            });
            $('.closemodal').click(function () {
                $('#remarks').val('');
                $('#order_item_id').val('');
                $('#split_order').val('');
                $('#cancel_qty').val('');
                $('#CancelorderModal').hide();                 
            });
        $('.cancelclosemodal').click(function () {
                $('#remarks').val('');
                $('#order_table_id').val('');                
                $('#CancelAllorderModal').hide();                 
            });
            

/*$(document).on('click', '#cancel-item', function () {    
    $('#CancelorderModal').show();    
     return false;
});*/

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
if($Settings->customer_discount=='customer') { ?>

$(document).ready(function(e) {
    var dis_id = $('.order_discount_input').val();   
    var dis_id = "<?php $this->site->CheckCustomerDiscountAppliedBySplitID($split_id)?>";     
    if(dis_id!=null){
        $('.order_discount_input').trigger('change');
    }    
});
<?php 
}
?>

$(document).on('change', '.order_discount_input', function () {

    $("#order_discount_input_seletedtext").val('');
    $this_obj = $(this);
    var find_attr = $(this).attr('count');
    var subtotal  = 0;
    var tax_amount  = $('#tax_amount_'+ find_attr).val();
    var taxtype  = $('#tax_type_'+ find_attr).val();    
    var unit_price = 0;
    var off_discount = $('#off_discount_'+ find_attr).val() ? $('#off_discount_'+ find_attr).val() : 0;
    var discount = $('#item_discounts_'+ find_attr).val() ? $('#item_discounts_'+ find_attr).val() : 0;
    var manual_discount_amount = 0.00;//$('#manual_discount_amount_'+ find_attr).val();
    // var manual_discount_amount = $('#manual_discount_amount_'+ find_attr).val();
    
    if(typeof off_discount == "undefined" || parseFloat(off_discount) == 0)
    { 
        discount = parseFloat(discount)+parseFloat(manual_discount_amount);
        subtotal  = $('#subtotal_'+ find_attr).val();
        unit_price = parseFloat($('#item_dis_'+ find_attr).val());        
    }
    else{
        discount = parseFloat(discount)+parseFloat(off_discount)+parseFloat(manual_discount_amount);
        subtotal  = $('#subtotal_'+ find_attr).val();
        unit_price = parseFloat($('#offer_discount_'+ find_attr).val());
    }



// alert(manual_discount_amount);

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

var item_quantity = [];
// split[1][quantity][]
$("input[name='split["+ find_attr+"][quantity][]']").each(function() {
    item_quantity.push($(this).val());
});
item_quantity = item_quantity.join(',');
// alert(quantity);
 var recipeids  = $('#recipeids_'+ find_attr).val();
 var recipeidvariantdataids  = $('#recipeidvariantdataids_'+ find_attr).val();
 var recipevariantids  = $('#recipevariantids_'+ find_attr).val();
var recipeqtys  = item_quantity;
// var recipeqtys  = $('#recipeqtys_'+ find_attr).val();

var manualitemdis = [];
    $('.manual_item_discount').each(function(){
        manualitemdis.push($(this).val());
    });
    manualitemdis = manualitemdis.join(',');

var addonsubtotal = [];
$('.addonsubtotal').each(function(){
    addonsubtotal.push($(this).val());
});
addonsubtotal = addonsubtotal.join(',');



var off_discounts  = off_discounts;
var input_discount = 0;
var divide = "<?php echo $bils;?>";
$split_id ="<?php echo $split_id;?>";
$customer_id ="<?php echo $order_data['customer_id']; ?>";
$table_id ="<?php echo $table_id; ?>";
<?php if($Settings->customer_discount=='customer') : ?>
if(ds !=0){
    
    $("#order_discount_input_seletedtext").val($(this).find('option:selected').text());
        $.ajax({
                type: 'POST',
                url: '<?=admin_url('pos/calculate_customerdiscount');?>',
                dataType: "json",
                 async : false,
                data: {
                    recipeids: recipeids,recipevariantids: recipevariantids,recipeqtys: recipeqtys,manualitemdis: manualitemdis,addonsubtotal: addonsubtotal,discountid:$this_obj.val(),divide: divide,split_id:$split_id,customer_id:$customer_id,table_id:$table_id
                },
                success: function (data) {
                   $.each( data, function( index, value ){  
                   $manual_item_discount_per_val = $('#manual_item_discount_per_val'+value.id).val();                   
                    input_discount += value.disamt;
                    if(value.disamt != 0){                        
                        $('#item_cus_dis'+value.id).text(value.discount_val);
                        $('#item_cus_dis-'+value.id).val(value.disamt);
                        $('#manual_and_customer_discount_consolid_percentage_display_option'+value.id).text((parseInt($manual_item_discount_per_val) + parseInt(value.discount_val)));
                    }else{
                        $('#item_cus_dis'+value.id).text('0');
                        $('#item_cus_dis-'+value.id).val(0);
                        $('#manual_and_customer_discount_consolid_percentage_display_option'+value.id).text((parseInt($manual_item_discount_per_val) + parseInt(0)));
                    }                    
                });                    
                }
           });
       

}else{
    
    $recipeids = recipeids.split(',');
    $recipeidvariantdataids = recipeidvariantdataids.split(',');
    console.log($recipeids);
    $.each($recipeidvariantdataids,function(i,v){console.log(v);
        $('.item_cus_dis').text('0');
        $('[id^=recipe-input-discount-]').val('');
        $manual_item_discount_per_val = $('#manual_item_discount_per_val'+v).val(); 

        $('#manual_and_customer_discount_consolid_percentage_display_option'+v).text((parseInt($manual_item_discount_per_val) ));

        $t_discount = parseFloat($('#recipe-item-discount-'+v).val()) + parseFloat($('#recipe-offer-discount-'+v).val());
        $('#recipe-total-discount-'+v).val($t_discount);
    });
   
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
var final_discount =  parseFloat(input_discount)+parseFloat(discount);
var final_amount = parseFloat(subtotal) - parseFloat(final_discount);
var final_discount =  parseFloat(input_discount)+parseFloat(discount);
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
    var final_amount = parseFloat(final_amount);    
    var final_amount_before_input =(parseFloat(subtotal)) - (parseFloat(discount));
} 
 
/*service charge*/
var service_charge_val = 0;
<?php
if($this->pos_settings->default_service_charge !=0 && $this->pos_settings->service_charge_option !=0){  ?>
var servicecharge = $('#service_charge_'+find_attr).children(":selected").data("id");

    if (servicecharge !== null && servicecharge != 0) {
        $.each(service_charge, function () {                       
        service_charge_val = parseFloat(((final_amount) * parseFloat(servicecharge)) / 100);        
        });
    }
<?php  } ?>
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
        final_amount = parseFloat(final_amount+final_tax+service_charge_val);
        finalamount = parseFloat(final_amount);
        sub_val = parseFloat(finalamount);
    }
    else
    {   
        sub_val = final_amount/((pr_tax_val/final_amount)+1);        
        final_tax_amount = sub_val * (pr_tax / 100);
        final_amount = sub_val+final_tax_amount+service_charge_val; 

    }
   
if(final_amount >= 0 ){
    
    $('#tdis_'+ find_attr).val(formatDecimal(input_discount));
    $('#tds_'+ find_attr).text(formatMoney(input_discount));
    $('.birthday_discount_'+ find_attr).text(formatDecimal(birthday));
    $('.birthday_discount_'+ find_attr).val(formatDecimal(birthday));
    $('#ttax2_'+ find_attr).text(formatMoney(final_tax_amount));
    $('#tax_amount_'+ find_attr).val(formatDecimal(final_tax_amount));
    $('#gtotal_'+ find_attr).text(formatMoney(final_amount));
    $('#grand_total_'+ find_attr).val(formatDecimal(final_amount));
    $('#round_total_'+ find_attr).val(formatDecimal(final_amount));
    $('#service_amount_'+ find_attr).val(formatDecimal(service_charge_val));
    $('#spansericechargeamt_'+ find_attr).text(formatMoney(service_charge_val));
    manualdis1(find_attr);

}else{
    bootbox.alert('Discount should not grater than total', function(){
        location.reload(); 
     });
}
});

    $(document).on('keyup','.comment_price',function(){

        var comment_price = $(this).val();

        var find_attr = $(this).attr('count');                    
        var item_qty = $(this).parent().siblings().find(".quantity").val();            
        var recipe_id = $(this).parent().parent().find(".recipe_id").val();         
        var unit_price = $(this).parent().siblings().find(".unit_price").val();         
        var addonsubtotal = $(this).parent().siblings().find(".addonsubtotal").val(); 
        var manualds = $(this).parent().siblings().find(".manual_item_discount_val").val() ? $(this).parent().siblings().find(".manual_item_discount_val").val() : 0; 
        var item_cus_dis_val = $(this).parent().siblings().find(".item_cus_dis_val").text() ? $(this).parent().siblings().find(".item_cus_dis_val").text() : 0;        
        $sub =parseFloat(comment_price)+parseFloat(unit_price*item_qty)+parseFloat(addonsubtotal);
        var manual_item_ds = 0;
            if(manualds != 0){
            if (manualds.indexOf("%") !== -1) {
                var manualpds = manualds.split("%");
                if (!isNaN(manualpds[0])) {
                   manual_item_ds = formatDecimal((parseFloat(($sub * parseFloat(manualpds[0])) / 100)), 4);
                    
                } else {
                    manual_item_ds = formatDecimal(manualds);
                }
            } else {
                 manual_item_ds = formatDecimal(manualds);
            } } else{
                manual_item_ds = formatDecimal(manualds);
            }  

        $(this).parent().find('.manual_item_discount').val(manual_item_ds); 

        var sub_total = (parseFloat(unit_price*item_qty)+parseFloat(addonsubtotal)+parseFloat(comment_price)-parseFloat(manual_item_ds));


         if(parseFloat(sub_total) <= 0 ){
                 bootbox.alert('Discount should not grater than Subtotal', function(){
                location.reload();
                });
            }

             $(this).parent().find('.manual_item_discount').val(manual_item_ds); 
                var per = 0;
                if (manualds.indexOf('%') !== -1) {
                  per = manualds.replace("%", "");                  
                } else {
                    per = (manualds / unit_price*item_qty) * 100;                                      
                }

                var ds = $('#recipe-item-discount-type-'+recipe_id).val();
                  
                var itemdisperval = $('#recipe-item-discount-val-'+recipe_id).val();
                    if (ds != '') {                        
                        if (ds == 'percentage_discount') {                            
                            recipe_item_discount = parseFloat((( (parseFloat(comment_price)+parseFloat(unit_price*item_qty)+parseFloat(addonsubtotal)) - manual_item_ds) * parseFloat(itemdisperval)) / 100);
                            
                        } else {
                            recipe_item_discount = parseFloat(itemdisperval);
                        }
                    } else {
                        recipe_item_discount = parseFloat(itemdisperval);
                    }

            $(this).parent().parent().find('#recipe-item-discount-'+recipe_id).val(formatDecimal(recipe_item_discount)); 
            $(this).parent().parent().find('.recipe-item-discount-'+recipe_id).text(formatDecimal(recipe_item_discount)); 
            $(this).parent().parent().find('.item_subtotal').text(formatMoney(sub_total)); 
            $(this).parent().parent().find('.discounted_subtotal').val(formatDecimal(sub_total)); 
            $(this).parent().parent().find('.item_subtotal1').val(formatDecimal(unit_price*item_qty+parseFloat(addonsubtotal)+parseFloat(comment_price))); 
            $(this).parent().parent().find('.manual_item_discount_per_val').val(formatDecimal(per));                
            $(this).parent().parent().find('.manual_and_customer_discount_consolid_percentage_display_option').text((parseInt(per) + parseInt(item_cus_dis_val)));   

            $(this).val($(this).val());  
            manualdis(find_attr);         
    });


    $(document).on('click','.minus',function(){
    $original_qty = parseInt($(this).closest('.qty_number').find('.original_quantity').val());    
    $cnt = parseInt($(this).closest('.qty_number').find('.quantity').val()) - parseInt(1);
    $order_item_id = $(this).closest('.qty_number').find('.order_item_id').val();     
    $split_id ="<?php echo $split_id;?>";
    $action = 'minus';
    $msg = 'Are you sure want to Decrease Quantity?';
                bootbox.confirm({
            message: $msg,
            buttons: {
                confirm: {
                label: 'Yes',
                className: 'btn-success'
                },
                cancel: {
                label: 'No',
                className: 'btn-danger'
                }
            },
            callback: function (result) {               
                if (result) {               
                $.ajax({
                    type: "get",
                    url:"<?=admin_url('pos/sale_item_qty_adjustment');?>",                
                    data: {order_item_id: $order_item_id, action: $action, split_id: $split_id},
                    dataType: "json",
                    contentType: false,                           
                    success: function (data) {                        
                    if(data.msg == 'success'){        
                        location.reload(); 
                    }else{
                        alert('Something is wrong please');
                    }
                    }    
                }).done(function () {
                });
                }
            }
            });
       
});

$(document).on('click','.plus',function(){

     $order_item_id = $(this).closest('.qty_number').find('.order_item_id').val();     
     $split_id ="<?php echo $split_id;?>";
     $action = 'plus';
     $msg = 'Are you sure want to Increase Quantity?';
                bootbox.confirm({
            message: $msg,
            buttons: {
                confirm: {
                label: 'Yes',
                className: 'btn-success'
                },
                cancel: {
                label: 'No',
                className: 'btn-danger'
                }
            },
            callback: function (result) {               
                if (result) {               
                $.ajax({
                    type: "get",
                    url:"<?=admin_url('pos/sale_item_qty_adjustment');?>",                
                    data: {order_item_id: $order_item_id, action: $action, split_id: $split_id},
                    dataType: "json",
                    // cache : false, 
                    contentType: false,                           
                    // processData : false,
                    success: function (data) {                        
                    if(data.msg == 'success'){        
                        location.reload();                                         
                    }else{
                        alert('Something is wrong please');
                    }
                    }    
                }).done(function () {
                });
                }
            }
            });
});

    $('.manual_item_discount_val').each(function () {   
    $(this).change(function () {    
    var find_attr = $(this).attr('count');                    
       var item_qty = $(this).parent().siblings().find(".quantity").val();         
       var recipe_id = $(this).parent().siblings().find(".recipe_id").val();         
       var recipe_id = $(this).parent().siblings().find(".recipe_id").val();         
       var unit_price = $(this).parent().siblings().find(".unit_price").val();         
       var addonsubtotal = $(this).parent().siblings().find(".addonsubtotal").val(); 
       var comment_price = $(this).parent().siblings().find(".comment_price").val() ? $(this).parent().siblings().find(".comment_price").val() : 0; 
       var item_cus_dis_val = $(this).parent().siblings().find(".item_cus_dis_val").text() ? $(this).parent().siblings().find(".item_cus_dis_val").text() : 0;  

        var manualds = $(this).val();
        var manual_item_ds = 0;
            if(manualds != 0){
            if (manualds.indexOf("%") !== -1) {
                var manualpds = manualds.split("%");
                if (!isNaN(manualpds[0])) {
                    manual_item_ds = formatDecimal((parseFloat((((unit_price*item_qty)+parseFloat(addonsubtotal)+parseFloat(comment_price)) * parseFloat(manualpds[0])) / 100)), 4);
                    
                } else {
                    manual_item_ds = formatDecimal(manualds);
                }
            } else {
                 manual_item_ds = formatDecimal(manualds);
            } } else{
                manual_item_ds = formatDecimal(manualds);
            }  
            var sub_total = (parseFloat(unit_price*item_qty)+parseFloat(addonsubtotal)+parseFloat(comment_price)-parseFloat(manual_item_ds));
 
             if(parseFloat(sub_total) <= 0 ){
                 bootbox.alert('Discount should not grater than Subtotal', function(){
                location.reload();
                });
            }

             $(this).parent().find('.manual_item_discount').val(manual_item_ds); 
                var per = 0;
                if (manualds.indexOf('%') !== -1) {
                  per = manualds.replace("%", "");                  
                } else {
                    per = (manualds / unit_price*item_qty) * 100;                                      
                }

                var ds = $('#recipe-item-discount-type-'+recipe_id).val();
                  
                var itemdisperval = $('#recipe-item-discount-val-'+recipe_id).val();
                    if (ds != '') {                        
                        if (ds == 'percentage_discount') {
                            recipe_item_discount = parseFloat(((unit_price*item_qty - manual_item_ds) * parseFloat(itemdisperval)) / 100);
                        } else {
                            recipe_item_discount = parseFloat(itemdisperval);
                        }
                    } else {
                        recipe_item_discount = parseFloat(itemdisperval);
                    }

            $(this).parent().parent().find('#recipe-item-discount-'+recipe_id).val(formatDecimal(recipe_item_discount)); 
            $(this).parent().parent().find('.recipe-item-discount-'+recipe_id).text(formatDecimal(recipe_item_discount)); 

            $(this).parent().parent().find('.item_subtotal').text(formatMoney(sub_total)); 
            $(this).parent().parent().find('.discounted_subtotal').val(formatDecimal(sub_total)); 
            $(this).parent().parent().find('.item_subtotal1').val(formatDecimal(unit_price*item_qty+parseFloat(addonsubtotal)+parseFloat(comment_price))); 
            $(this).parent().parent().find('.manual_item_discount_per_val').val(formatDecimal(per));                
             $(this).parent().parent().find('.manual_and_customer_discount_consolid_percentage_display_option').text((parseInt(per) + parseInt(item_cus_dis_val)));   
                              
            $(this).val($(this).val());  
            manualdis(find_attr);
            
    });    
});

function manualdis(find_attr){

    var sum = 0;
    var item_discount = 0;
    $('.item_discount').each(function(){
        item_discount += parseFloat(this.value);
    });

    $(".itemdiscounts").text(formatMoney(item_discount));
    var itemdiscounts = $(".itemdiscounts").val(formatDecimal(item_discount));

    var item_subtotal1 = 0;
    var all_item_total = 0;
    $('.discounted_subtotal').each(function(){
        all_item_total += (($(this).parent().siblings().find(".quantity").val()) * $(this).parent().siblings().find(".unit_price").val());
        item_subtotal1 += parseFloat(this.value);
    });
     var addonsubtotal = 0;
    $('.addonsubtotal').each(function(){
        addonsubtotal += parseFloat(this.value);
    });

    var commentsubtotal = 0;
    $('.comment_price').each(function(){
        commentsubtotal += parseFloat(this.value);
    });    

    $(".total_manual_discount_amount").val(formatDecimal(sum));
    $(".total_manual_discount_amount").text(formatMoney(sum));
    var total_price_textbox = item_subtotal1;
    
    var after_manual_dis = total_price_textbox - sum;    
    $(".after_manual_dis_textbox").val(formatDecimal(after_manual_dis));

    var after_manual_dis_textbox = after_manual_dis;//$(".after_manual_dis_textbox").val();    
    var itemdiscounts = $(".itemdiscounts").val();
    var after_manual_dis = after_manual_dis_textbox -itemdiscounts; 
    $(".after_item_or_manual_dis").text(formatMoney(item_subtotal1-itemdiscounts));
    $(".after_item_or_manual_dis_textbox").val(formatDecimal(item_subtotal1-itemdiscounts));    
    $(".total_price").text(formatMoney(item_subtotal1));
    $(".total_price").val(formatDecimal(item_subtotal1));
    $(".all_item_total").val(formatDecimal((all_item_total+addonsubtotal+commentsubtotal)));
    
    $total_price = $("#subtotal_"+find_attr).val();
    $item_dis = $("#item_dis_"+find_attr).val();
    var tota_ds = $('.tot_dis_value').val() ? $('.tot_dis_value').val() : '0';
    if (tota_ds.indexOf("%") !== -1) {
        var pds = tota_ds.split("%");
        if (!isNaN(pds[0])) {
            total_discount = parseFloat((($item_dis) * parseFloat(pds[0])) / 100);
        } else {
            total_discount = parseFloat(tota_ds);
        }
    } else {
        total_discount = parseFloat(tota_ds);
    }       
    $("#offer_discount_"+find_attr).val(formatDecimal(parseFloat($item_dis)-parseFloat(total_discount)));
    $(".subtot_"+find_attr).text(formatMoney(parseFloat($item_dis)-parseFloat(total_discount)));
    $(".subtot_"+find_attr).val(formatDecimal(parseFloat($item_dis)-parseFloat(total_discount)));
    $("#tds1_"+find_attr).text(formatMoney(total_discount));
    $("#off_discount_"+find_attr).val(formatDecimal(total_discount));
    $('.order_discount_input').trigger('change');            
}

function manualdis1(find_attr){
    var sum = 0;
    $('.manual_item_discount').each(function(){
        sum += parseFloat(this.value);
    });

    var item_discount = 0;
    $('.item_discount').each(function(){
        item_discount += parseFloat(this.value);
    });

    $(".itemdiscounts").text(formatMoney(item_discount));
    var itemdiscounts = $(".itemdiscounts").val(formatDecimal(item_discount));

    var item_subtotal1 = 0;
    $('.discounted_subtotal').each(function(){
        item_subtotal1 += parseFloat(this.value);
    });

    $(".total_manual_discount_amount").val(formatDecimal(sum));
    $(".total_manual_discount_amount").text(formatMoney(sum));
    var total_price_textbox = item_subtotal1;
    
    var after_manual_dis = total_price_textbox - sum;    
    $(".after_manual_dis_textbox").val(formatDecimal(after_manual_dis));

    var after_manual_dis_textbox = after_manual_dis;//$(".after_manual_dis_textbox").val();    
    var itemdiscounts = $(".itemdiscounts").val();
    var after_manual_dis = after_manual_dis_textbox -itemdiscounts; 
    $(".after_item_or_manual_dis").text(formatMoney(item_subtotal1-itemdiscounts));
    $(".after_item_or_manual_dis_textbox").val(formatDecimal(item_subtotal1-itemdiscounts));    
    $(".total_price").text(formatMoney(item_subtotal1));
    $(".total_price").val(formatDecimal(item_subtotal1));
   
    
    $total_price = $("#subtotal_"+find_attr).val();
    $item_dis = $("#item_dis_"+find_attr).val();

    var tota_ds = $('.tot_dis_value').val() ? $('.tot_dis_value').val() : '0';
    if (tota_ds.indexOf("%") !== -1) {
        var pds = tota_ds.split("%");
        if (!isNaN(pds[0])) {
            total_discount = parseFloat((($item_dis) * parseFloat(pds[0])) / 100);
        } else {
            total_discount = parseFloat(tota_ds);
        }
    } else {
        total_discount = parseFloat(tota_ds);
    }  
     // alert(parseFloat($item_dis)-parseFloat(total_discount)-parseFloat($item_customer_dis));
    $item_customer_dis = $("#tdis_"+find_attr).val();     
    $("#offer_discount_"+find_attr).val(formatDecimal(parseFloat($item_dis)-parseFloat(total_discount)));
    $(".subtot_"+find_attr).text(formatMoney(parseFloat($item_dis)-parseFloat(total_discount)-parseFloat($item_customer_dis)));
    $(".subtot_"+find_attr).val(formatDecimal(parseFloat($item_dis)-parseFloat(total_discount)-parseFloat($item_customer_dis)));
    $("#tds1_"+find_attr).text(formatMoney(total_discount));
    $("#off_discount_"+find_attr).val(formatDecimal(total_discount));
}

$(document).on("focus", '.manual_item_discount_val', function (e) {
  var element = $(this)[0];
    var len = $(this).val().length * 2;
        element.setSelectionRange(len, len);
		if($(this).val() <=0){
			$(this).val('');
		} 
    }).on("click", '.manual_item_discount_val', function (e) {
        $(this).val($(this).val());
        $(this).focus();
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

$('.kb-pad1').keyboard({
        restrictInput: true,
    css: {
        container: 'number-keyboard'
    },
        preventPaste: true,
        autoAccept: true,
        alwaysOpen: false,
        openOn: 'click',
        usePreview: false,
        layout: 'custom',
        display: {
            'b': '\u2190:Backspace',
        },
        customLayout: {
            'default': [
            '1 2 3 {b}',
            '4 5 6 . {clear}',
            '7 8 9 0 %',
            '{accept} {cancel}'
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



<script type="text/javascript">

</script>
</body>
</html>
