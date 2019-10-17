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
    <link rel="stylesheet" href="<?=$assets?>qsr/css/posajax.css" type="text/css"/>
    <link rel="stylesheet" href="<?=$assets?>qsr/css/print.css" type="text/css" media="print"/>
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
<?php
$currency = $this->site->getAllCurrencies();
?>
<div id="wrapper">
   
	<?php
	$this->load->view($this->theme . 'qsr/qsr_header');
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
                    if ($message) {
                        // echo "<div class=\"alert alert-success\"><button type=\"button\" class=\"close fa-2x\" data-dismiss=\"alert\">&times;</button>" . $message . "</div>";
                    }
                ?>
                <div id="pos">
                
					<?php $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'id' => 'pos-sale-form');
                    echo admin_form_open("qsr", $attrib);?>
                    <div id="leftdiv">
                        <div id="printhead">
                            <h4 style="text-transform:uppercase;"><?php echo $Settings->site_name; ?></h4>
                            <?php
                                echo "<h5 style=\"text-transform:uppercase;\">" . $this->lang->line('order_list') . "</h5>";
                                echo $this->lang->line("date") . " " . $this->sma->hrld(date('Y-m-d H:i:s'));
                            ?>
                        </div>
                        <div id="left-top">
                            <div
                                style="position: absolute; <?=$Settings->user_rtl ? 'right:-9999px;' : 'left:-9999px;';?>"><?php echo form_input('test', '', 'id="test" class="kb-pad"'); ?></div>
                            <div class="form-group">
                                <div class="input-group">
                                <?php
                                    echo form_input('customer', (isset($_POST['customer']) ? $_POST['customer'] : ""), 'id="poscustomer" data-placeholder="' . $this->lang->line("select") . ' ' . $this->lang->line("customer") . '" required="required" class="form-control pos-input-tip" style="width:100%;"');
                                ?>
                                    <div class="input-group-addon no-print" style="padding: 2px 8px; border-left: 0;">
                                        <a href="#" id="toogle-customer-read-attr" class="external">
                                            <i class="fa fa-pencil" id="addIcon" style="font-size: 1.2em;"></i>
                                        </a>
                                    </div>
                                    <div class="input-group-addon no-print" style="padding: 2px 7px; border-left: 0;">
                                        <a href="#" id="view-customer" class="external" data-toggle="modal" data-target="#myModal">
                                            <i class="fa fa-eye" id="addIcon" style="font-size: 1.2em;"></i>
                                        </a>
                                    </div>
                                <?php if ($Owner || $Admin || $GP['customers-add']) { ?>
                                    <div class="input-group-addon no-print" style="padding: 2px 8px;">
                                        <a href="<?=admin_url('customers/add_pos');?>" id="add-customer" class="external" data-toggle="modal" data-target="#myModal">
                                            <i class="fa fa-plus-circle" id="addIcon" style="font-size: 1.5em;"></i>
                                        </a>
                                    </div>
                                <?php } ?>
                                </div>
                                <div style="clear:both;"></div>
                            </div>
                            <div class="no-print">
                              
                                    <div class="form-group">
                                        <?php
                                            $wh[''] = '';
                                                foreach ($warehouses as $warehouse) {
                                                    $wh[$warehouse->id] = $warehouse->name;
                                                }
                                                echo form_dropdown('warehouse', $wh, (isset($_POST['warehouse']) ? $_POST['warehouse'] : $Settings->default_warehouse), 'id="poswarehouse" class="form-control pos-input-tip" data-placeholder="' . $this->lang->line("select") . ' ' . $this->lang->line("warehouse") . '" required="required" style="width:100%;" ');
                                            ?>
                                    </div>
                                
                                <div class="form-group" id="ui">
                                    <?php if ($Owner || $Admin || $GP['products-add']) { ?>
                                    <div class="input-group">
                                    <?php } ?>
                                    <?php echo form_input('add_item', '', 'class="form-control pos-tip" id="add_item" data-placement="top" data-trigger="focus" placeholder="' . $this->lang->line("search_product_by_name_code") . '" title="' . $this->lang->line("au_pr_name_tip") . '"'); ?>
                                    <?php if ($Owner || $Admin || $GP['products-add']) { ?>
                                        <div class="input-group-addon" style="padding: 2px 8px;">
                                            <a href="#" id="addManually">
                                                <i class="fa fa-plus-circle" id="addIcon" style="font-size: 1.5em;"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <?php } ?>
                                    <div style="clear:both;"></div>
                                </div>
                            </div>
                        </div>
                        <div id="print">
                            <div id="left-middle">
                                <div id="recipe-list" class="dragscroll">
                                    <table class="table items table-striped table-fixed table-bordered table-condensed table-hover sortable_table"
                                           id="posTable" style="margin-bottom: 0;">
                                        <thead>
                                        <tr>
                                            <th width="40%"><?=lang("recipe");?></th>
                                            <th width="15%"><?=lang("price");?></th>
                                            <th width="15%"><?=lang("quantity");?></th>
                                            <th width="20%"><?=lang("subtotal");?></th>
                                            <th style="width: 5%; text-align: center;">
                                                <i class="fa fa-trash-o" style="opacity:0.5; filter:alpha(opacity=50);"></i>
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                    <div style="clear:both;"></div>
                                </div>
                            </div>
                            <div style="clear:both;"></div>
                            <div id="left-bottom">
                                <table id="totalTable"
                                       style="width:100%; float:right; padding:5px; color:#000; background: #FFF;">
                                    <tr>
                                        <td style="padding: 5px 10px;border-top: 1px solid #DDD;"><?=lang('items');?></td>
                                        <td class="text-right" style="padding: 5px 10px;font-size: 14px; font-weight:bold;border-top: 1px solid #DDD;">
                                            <span id="titems">0</span>
                                        </td>
                                        <td style="padding: 5px 10px;border-top: 1px solid #DDD;"><?=lang('total');?></td>
                                        <td class="text-right" style="padding: 5px 10px;font-size: 14px; font-weight:bold;border-top: 1px solid #DDD;">
                                            <span id="total">0.00</span>
                                            <input type="hidden" name="sub_total" id="sub_total" value="0">
                                        </td>
                                    </tr>

                                    <tr>
                                        <?php 
                                           $return = $this->site->is_uniqueDiscountExist();
                                            if(empty($return)){
                                             if($Settings->customer_discount !='none') : ?>
                                        	<td >
                                                 
                                                <a href="#" id="ppdiscount">
                                                    <button type="button" class="btn btn-warning btn-block btn-flat"
                                                id="ppdiscount" >  <?=lang('discount'); ?>  </button>
                                                 </a>
                                               <!--  <input type="hidden" name="suspend_id" value="0">
                                                    <?=lang('discount'); ?> -->
                                               

                                               <!--  <?=lang('discount');?>
                                                <a href="#" id="ppdiscount">
                                                    <i class="fa fa-edit"></i>
                                                </a>    -->                                        
                                            </td>
                                        

                                        <td class="text-right" style="padding: 5px 10px;font-weight:bold;">
                                            <span id="tds">0.00</span>
                                        </td>
                                        <?php endif; }?>
                                        <td style="padding: 5px 10px;">
                                        <?php 
                                            if ($pos_settings->tax_type == 0)
                                            {
                                                $taxname = 'Inclusive';
                                            }
                                            else
                                            {
                                                $taxname = 'Exclusive';
                                            }
                                            $getTax = $this->site->getTaxRateByID($this->pos_settings->default_tax);

                                            ?>
                                        <?=lang("Tax ".'('.$taxname.' '.$getTax->name.')');?>
                                            <!--<a href="#" id="pptax2">
                                                <i class="fa fa-edit"></i>
                                            </a>-->
                                        </td>
                                        <td class="text-right" style="padding: 5px 10px;font-size: 14px; font-weight:bold;">
                                            <span id="ttax2">0.00</span>
                                        </td>
                                     
                                       
                                    </tr>
                                    <tr>
                                        <td style="padding: 5px 10px; border-top: 1px solid #666; border-bottom: 1px solid #333; font-weight:bold; background:#333; color:#FFF;" colspan="2">
                                            <?=lang('total_payable');?>
                                            <!--<a href="#" id="pshipping">
                                                <i class="fa fa-plus-square"></i>
                                            </a>-->
                                            <span id="tship"></span>
                                        </td>
                                        <td class="text-right" style="padding:5px 10px 5px 10px; font-size: 14px;border-top: 1px solid #666; border-bottom: 1px solid #333; font-weight:bold; background:#333; color:#FFF;" colspan="2">
                                            <span id="gtotal">0.00</span>
                                        </td>
                                    </tr>
                                </table>

                                <div class="clearfix"></div>
                                <div id="botbuttons" class="col-xs-12 text-center">
                                    <input type="hidden" name="biller" id="biller" value="<?= ($Owner || $Admin || !$this->session->userdata('biller_id')) ? $pos_settings->default_biller : $this->session->userdata('biller_id')?>"/>
                                    <div class="row">
                                        <div class="col-xs-4" style="padding: 0;">
                                            <div class="btn-group-vertical btn-block">
                                                <button type="button" class="btn btn-warning btn-block btn-flat"
                                                id="suspend">
                                                <input type="hidden" name="suspend_id" value="0">
                                                    <?=lang('hold'); ?>
                                                </button>
                                                <button type="button" class="btn btn-danger btn-block btn-flat" style="height: 34px!important;
    font-size: 14px;"
                                                id="reset">
                                                    <?= lang('cancel'); ?>
                                                </button>
                                            </div>

                                        </div>
                                        <div class="col-xs-4" style="padding: 0;">
                                            <div class="btn-group-vertical btn-block">
                                                <button type="button" class="btn btn-info btn-block" id="print_order">
                                                    <?=lang('order');?>
                                                </button>

                                                <button type="button" class="btn btn-primary btn-block" id="print_bill">
                                                    <?=lang('bill');?>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-xs-4" style="padding: 0;">
                                            <button type="button" class="btn btn-success btn-block" id="payment" style="height:67px;">
                                                <i class="fa fa-money" style="margin-right: 5px;"></i><?=lang('payment');?>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div style="clear:both; height:5px;"></div>
                                <div id="num">
                                    <div id="icon"></div>
                                </div>
                                <span id="hidesuspend"></span>
                                <input type="hidden" name="pos_note" value="" id="pos_note">
                                <input type="hidden" name="staff_note" value="" id="staff_note">
								<input type="hidden" name="balance_final_amount" class="balance_amount" value=""/>
                                <input type="hidden" name="due_amount" class="due_amount" value=""/>
                                <input type="hidden" name="total"  class="total" />
                                
                                <div id="payment-con">
                                    <?php for ($i = 1; $i <= 5; $i++) {?>
                                    	<?php
										foreach($currency as $currency_row){
											
											$default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
											
											if($currency_row->code == $default_currency_data->code){
                                              
										?>
                             <input type="hidden" name="amount_<?=$currency_row->code; ?>[]" id="amount_val_<?=$currency_row->code; ?>_<?=$i?>" value=""/>
                                        	<?php
											}else{
											?>
                                       
                             <input type="hidden" name="amount_<?=$currency_row->code; ?>[]" id="amount_val_<?=$currency_row->code; ?>_<?=$i?>" value=""/>
                                            
                                        <?php
											}
										}
										?>
                                       
                                        
                                        <input type="hidden" name="balance_amount[]" class="balance_amount" id="balance_amount_<?=$i?>" value=""/>
                                        <input type="hidden" name="paid_by[]" id="paid_by_val_<?=$i?>" value="cash"/>
                                        <input type="hidden" name="cc_no[]" id="cc_no_val_<?=$i?>" value=""/>
                                        <input type="hidden" name="paying_gift_card_no[]" id="paying_gift_card_no_val_<?=$i?>" value=""/>
                                        <input type="hidden" name="cc_holder[]" id="cc_holder_val_<?=$i?>" value=""/>
                                        <input type="hidden" name="cheque_no[]" id="cheque_no_val_<?=$i?>" value=""/>
                                        <input type="hidden" name="cc_month[]" id="cc_month_val_<?=$i?>" value=""/>
                                        <input type="hidden" name="cc_year[]" id="cc_year_val_<?=$i?>" value=""/>
                                        <input type="hidden" name="cc_type[]" id="cc_type_val_<?=$i?>" value=""/>
                                        <input type="hidden" name="cc_cvv2[]" id="cc_cvv2_val_<?=$i?>" value=""/>
                                        <input type="hidden" name="payment_note[]" id="payment_note_val_<?=$i?>" value=""/>
                                    <?php }
                                    ?>
                                </div>
                                <input name="order_tax" type="hidden" value="<?=$suspend_sale ? $suspend_sale->order_tax_id : ($old_sale ? $old_sale->order_tax_id : $Settings->default_tax_rate2);?>" id="postax2">
                                <input name="discount" type="hidden" value="<?=$suspend_sale ? $suspend_sale->order_discount_id : ($old_sale ? $old_sale->order_discount_id : '');?>" id="posdiscount">

                                <input name="item_discount" type="hidden" value="" id="item_discount">
                                <input name="discount_on_total" type="hidden" value="" id="discount_on_total">

                                <input name="shipping" type="hidden" value="<?=$suspend_sale ? $suspend_sale->shipping : ($old_sale ? $old_sale->shipping :  '0');?>" id="posshipping">
                                <input type="hidden" name="rpaidby" id="rpaidby" value="cash" style="display: none;"/>
                                <input type="hidden" name="total_items" id="total_items" value="0" style="display: none;"/>
                                <input type="submit" id="submit_sale" value="Submit Sale" style="display: none;"/>
                            </div>
                        </div>

                    </div>


<div class="modal fade in" id="dsModal" tabindex="-1" role="dialog" aria-labelledby="dsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    <i class="fa fa-2x">&times;</i>
                </button>
                <h4 class="modal-title" id="dsModalLabel"><?=lang('edit_order_discount');?></h4>
            </div>           

            <div class="modal-body">
                <?php if($Settings->customer_discount=='customer') : ?>
                     <select style="display: "  name="order_discount_input" class="form-control pos-input-tip order_discount_input" id="order_discount_input" count="<?php echo $i; ?>">
                     <option value="0">No</option> 
                        <?php
                        foreach ($customer_discount as $cusdis) {
                            
                        ?>
                        <option value="<?php echo $cusdis->id; ?>" <?php if($discount_select['dine'] == $cusdis->id){ echo 'selected'; }else{ echo ''; } ?> data-id="<?php echo $cusdis->id; ?>"><?php echo $cusdis->name; ?></option>
                        <?php
                        }
                        ?>
                    </select>
                <?php elseif($Settings->customer_discount=='manual') : ?>
                    <div class="form-group">
                        <?=lang("order_discount", "order_discount_input");?>
                        <?php echo form_input('order_discount_input', '0', 'class="form-control kb-pad order_discount_input" id="order_discount_input"'); ?>
                    </div>
               <?php endif; ?>
            </div>

            <div class="modal-footer">
                <button type="button" id="updateOrderDiscount" class="btn btn-primary"><?=lang('update')?></button>
            </div>
        </div>
    </div>
</div>


                    <?php echo form_close(); ?>
                                        
                    <div id="cp">
                    	
                        
                        
                    	<div id="category-list">
						<?php
                            
                            foreach ($categories as $category) {
                                echo "<button id=\"category-" . $category->id . "\" type=\"button\" value='" . $category->id . "' class=\"btn-prni category\" ><span>" . $category->name . "</span></button>";
                            }
                           
                        ?>
                    </div>
                     
                        
                       <div id="subcategory-list" class="carousel">
                       <div id="scroller">
						<?php
                            if (!empty($subcategories)) {
                                
                                foreach ($subcategories as $category) {

                                   if($this->Settings->user_language == 'khmer'){
                                            
                                            if(!empty($category->khmer_name)){
                                                
                                                $subcategory_name = $category->khmer_name;
                                            }else{
                                                $subcategory_name = $category->name;
                                            }
                                        }else{
                                            $subcategory_name = $category->name;
                                        }
                                    
                                         //$subhtml = "<button id=\"subcategory-" . $category->id . "\" type=\"button\" value='" . $category->id . "' class=\"btn-prni subcategory slide\" ><img src=\"assets/uploads/thumbs/" . ($category->image ? $category->image : 'no_image.png') . "\" class='img-rounded' />";
                                         /*$subhtml = "<button id=\"subcategory-" . $category->id . "\" type=\"button\" value='" . $category->id . "' class=\"btn-prni subcategory slide\" >";*/

                                         if($this->pos_settings->sale_item_display == 0){
                                         $subhtml = "<button id=\"subcategory-" . $category->id . "\" type=\"button\" value='" . $category->id . "' class=\"btn-prni subcategory slide\" ><img src=\"assets/uploads/thumbs/" . ($category->image ? $category->image : 'no_image.png') . "\" class='img-rounded' />";
                                        }else{
                                            $subhtml = "<button id=\"subcategory-" . $category->id . "\" type=\"button\" value='" . $category->id . "' class=\"btn-img subcategory slide\" >";
                                        }                                          
                                        if(strlen($subcategory_name) < 20){        
                        
                                            $subhtml .= "<span class='name_strong'>" .$subcategory_name. "</span>";
                                        }else{
                                            $subhtml .= "<marquee class='name_strong' behavior='alternate' direction='left' scrollamount='1'>&nbsp;&nbsp;" .$subcategory_name. "&nbsp;&nbsp;</marquee>";
                                        }
                                          $subhtml .=  "</button>";
                                         
                                         echo $subhtml; 
                                }
                            }
                        ?>
                        </div>
                    </div>
                        
                    
                        <div id="cpinner">
                            <div class="quick-menu">
                                <div id="proContainer">
                                    <div id="ajaxrecipe">
                                        <div id="item-list">
                                            <?php echo $recipe; ?>
                                        </div>
                                        
                                        <div class="btn-group btn-group-justified pos-grid-nav">
                                            <div class="btn">
                                                <button style="z-index:10002;position: absolute;left: -25px;top: -100px;" class="btn btn-primary pos-tip" title="<?=lang('previous')?>" type="button" id="previous">
                                                    <i class="fa fa-chevron-left"></i>
                                                </button>
                                            </div>
                                            <?php if ($Owner || $Admin || $GP['sales-add_gift_card']) {?>
                                            <div class="btn-group">
                                                <button style="z-index:10003;" class="btn btn-primary pos-tip" type="button" id="sellGiftCard" title="<?=lang('sell_gift_card')?>">
                                                    <i class="fa fa-credit-card" id="addIcon"></i> <?=lang('sell_gift_card')?>
                                                </button>
                                            </div>
                                            <?php }
                                            ?>
                                            <div class="btn">
                                                <button style="z-index:10004;position: absolute;right: 0px;top: -100px;" class="btn btn-primary pos-tip" title="<?=lang('next')?>" type="button" id="next">
                                                    <i class="fa fa-chevron-right"></i>
                                                </button>
                                            </div>
                                        </div>
                                        
                                    </div>
                                    <div style="clear:both;"></div>
                                </div>
                            </div>
                        </div>
                        
                        
                        <div style="clear:both;"></div>
                    </div>
                    <div style="clear:both;"></div>
                </div>
                <div style="clear:both;"></div>
            </div>
        </div>
    </div>
</div>

<?php
$this->load->view($this->theme . 'qsr/qsr_footer');
?>


<div class="modal fade in" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="payModalLabel" style="z-index: 9997;"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true"><i
                            class="fa fa-2x">&times;</i></span><span class="sr-only"><?=lang('close');?></span></button>
                <h4 class="modal-title" id="payModalLabel"><?=lang('finalize_sale');?></h4>
            </div>
            <div class="modal-body" id="payment_content">
                <div class="row">
                	<input type="hidden" name="balance_amount" class="balance_amount" value=""/>
                	<input type="hidden" name="due_amount" class="due_amount" value=""/>
                    <input type="hidden" name="total" id="total" class="total" />
                
                    <div class="col-md-12 col-sm-12">
                        <?php if ($Owner || $Admin || !$this->session->userdata('biller_id')) { ?>
                            <div class="form-group">
                                <?=lang("biller", "biller");?>
                                <?php
                                    foreach ($billers as $biller) {
                                        $btest = ($biller->company && $biller->company != '-' ? $biller->company : $biller->name);
                                        $bl[$biller->id] = $btest;
                                        $posbillers[] = array('logo' => $biller->logo, 'company' => $btest);
                                        if ($biller->id == $pos_settings->default_biller) {
                                            $posbiller = array('logo' => $biller->logo, 'company' => $btest);
                                        }
                                    }
                                    echo form_dropdown('biller', $bl, (isset($_POST['biller']) ? $_POST['biller'] : $pos_settings->default_biller), 'class="form-control" id="posbiller" required="required"');
                                ?>
                            </div>
                        <?php } else {
                                $biller_input = array(
                                    'type' => 'hidden',
                                    'name' => 'biller',
                                    'id' => 'posbiller',
                                    'value' => $this->session->userdata('biller_id'),
                                );

                                echo form_input($biller_input);

                                foreach ($billers as $biller) {
                                    $btest = ($biller->company && $biller->company != '-' ? $biller->company : $biller->name);
                                    $posbillers[] = array('logo' => $biller->logo, 'company' => $btest);
                                    if ($biller->id == $this->session->userdata('biller_id')) {
                                        $posbiller = array('logo' => $biller->logo, 'company' => $btest);
                                    }
                                }
                            }
                        ?>
                        
                        <div class="form-group" style="display: none                 ">
                            <div class="row">
                                <div class="col-sm-6">
                                    <?=form_textarea('sale_note', '', 'id="sale_note" class="form-control kb-text skip" style="height: 100px;" placeholder="' . lang('sale_note') . '" maxlength="250"');?>
                                </div>
                                <div class="col-sm-6">
                                    <?=form_textarea('staffnote', '', 'id="staffnote" class="form-control kb-text skip" style="height: 100px;" placeholder="' . lang('staff_note') . '" maxlength="250"');?>
                                </div>
                            </div>
                        </div>
                        <div class="clearfir"></div>
                        <div id="payments">
                            <div class="well well-sm well_1">
                                <div class="payment">
                                    <div class="row">
                                        <!--<div class="col-sm-5">
                                            <div class="form-group">
                                                <?=lang("amount", "amount_1");?>
                                                <input name="amount[]" type="text" id="amount_1"
                                                       class="pa form-control kb-pad1 amount"/>
                                            </div>
                                        </div>-->
                                        <div class="col-sm-12">
                                            <div class="form-group"  style="margin-bottom: 5px;">
                                                <?=lang("paying_by", "paid_by_1");?>
                                                <select name="paid_by[]" id="paid_by_1" data-index="1" class="form-control paid_by">
                                                    <?= $this->sma->paid_opts_front(); ?>
                                                   
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <!-- <div class="col-sm-12">
                                            <div class="form-group">
                                                <?=lang("paying_by", "paid_by_1");?>
                                                <select name="paid_by[]" id="paid_by_1" class="form-control paid_by">
                                                    <?= $this->sma->paid_opts_front(); ?>
                                                    <?=$pos_settings->paypal_pro ? '<option value="ppp">' . lang("paypal_pro") . '</option>' : '';?>
                                                    <?=$pos_settings->stripe ? '<option value="stripe">' . lang("stripe") . '</option>' : '';?>
                                                    <?=$pos_settings->authorize ? '<option value="authorize">' . lang("authorize") . '</option>' : '';?>
                                                </select>
                                            </div>
                                        </div> -->
                                        
                                        <?php
										foreach($currency as $currency_row){
											
											$default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
											
											if($currency_row->code == $default_currency_data->code){
                                              
										?>
                                        <div class="col-sm-6 base_currency">
                                            <div class="form-group"  style="margin-bottom: 5px;">
                                                <label>Amount <?=$currency_row->code; ?></label>
                                                <input name="amount_<?php echo $default_currency_data->code; ?>[]" type="text" id="amount_<?php echo $default_currency_data->code; ?>_1" data-rate="<?=$currency_row->rate; ?>" data-code="<?=$currency_row->code; ?>" data-id="<?=$currency_row->id; ?>"  class="pa form-control  amount kb-pad" autocomplete="off"  />
                                            </div>
                                        </div>
                                        	<?php
											}else{
											?>
                                        <div class="col-sm-6 multi_currency_1">
                                            <div class="form-group"  style="margin-bottom: 5px;">
                                                 <label>Amount <?=$currency_row->code; ?></label>
                                                <input name="amount_<?=$currency_row->code; ?>[]" type="text" id="amount_<?=$currency_row->code; ?>_1" data-rate="<?=$currency_row->rate; ?>" data-code="<?=$currency_row->code; ?>" data-id="<?=$currency_row->id; ?>"  class="pa form-control  amount kb-pad" autocomplete="off"/>
                                            </div>
                                        </div>
                                        <?php
											}
										}
										?>
                                        
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-11">
                                            <div class="form-group gc_1" style="display: none;">
                                                <?=lang("gift_card_no", "gift_card_no_1");?>
                                                <input name="paying_gift_card_no[]" type="text" id="gift_card_no_1"
                                                       class="pa form-control kb-pad gift_card_no"/>

                                                <div id="gc_details_1"></div>
                                            </div>
                                            <div class="pcc_1" style="display:none;">
                                                <div class="form-group">
                                                    <input type="text" id="swipe_1" class="form-control swipe"
                                                           placeholder="<?=lang('swipe')?>"/>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <input name="cc_no[]" type="text" id="pcc_no_1"
                                                                   class="form-control"
                                                                   placeholder="<?=lang('cc_no')?>"/>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">

                                                            <input name="cc_holer[]" type="text" id="pcc_holder_1"
                                                                   class="form-control"
                                                                   placeholder="<?=lang('cc_holder')?>"/>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <select name="cc_type[]" id="pcc_type_1"
                                                                    class="form-control pcc_type"
                                                                    placeholder="<?=lang('card_type')?>">
                                                                <option value="Visa"><?=lang("Visa");?></option>
                                                                <option
                                                                    value="MasterCard"><?=lang("MasterCard");?></option>
                                                                <option value="Amex"><?=lang("Amex");?></option>
                                                                <option
                                                                    value="Discover"><?=lang("Discover");?></option>
                                                            </select>
                                                            <!-- <input type="text" id="pcc_type_1" class="form-control" placeholder="<?=lang('card_type')?>" />-->
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <input name="cc_month[]" type="text" id="pcc_month_1"
                                                                   class="form-control"
                                                                   placeholder="<?=lang('month')?>"/>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">

                                                            <input name="cc_year" type="text" id="pcc_year_1"
                                                                   class="form-control"
                                                                   placeholder="<?=lang('year')?>"/>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">

                                                            <input name="cc_cvv2" type="text" id="pcc_cvv2_1"
                                                                   class="form-control"
                                                                   placeholder="<?=lang('cvv2')?>"/>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="pcheque_1" style="display:none;">
                                                <div class="form-group"><?=lang("cheque_no", "cheque_no_1");?>
                                                    <input name="cheque_no[]" type="text" id="cheque_no_1"
                                                           class="form-control cheque_no"/>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <?=lang('payment_note', 'payment_note');?>
                                                <textarea name="payment_note[]" id="payment_note_1"
                                                          class="pa form-control kb-text payment_note"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                        <div id="multi-payment"></div>
                        <button type="button" class="btn btn-primary col-md-12 addButton"><i
                                class="fa fa-plus"></i> <?=lang('add_more_payments')?></button>
                        <div style="clear:both; height:15px;"></div>
                        <div class="font16">
                            <table class="table table-bordered table-condensed table-striped" style="margin-bottom: 0;">
                                <tbody>
                                
                                <tr>
                                	
                                    <td ><?=lang("total_items");?></td>
                                    <td  class="text-right"><span id="item_count">0.00</span></td>
                                    <?php
									foreach($currency as $currency_row){
									?>
                                    <td>Total Payable In <?php echo $currency_row->code; ?></td>
                                    <td  class="text-right"><span id="twt_<?php echo $currency_row->code; ?>">0.00</span></td>
                                    <?php
									}
									?>
                                </tr>
                                
                                <tr>
                                    <td><?=lang("total_paying");?></td>
                                    <td class="text-right"><span id="total_paying">0.00</span>
                                    
                                    </td>
                                    <?php
									foreach($currency as $currency_row){
									?>
                                    <td>Balance <?php echo $currency_row->code; ?></td>
                                    <td class="text-right"><span id="balance_<?php echo $currency_row->code; ?>">0.00</span>
                                    <?php
									//statically added for imediate requirment USD
									if($currency_row->code == 'USD'){
									?>
                                     (<span id="balance_USD_KHR" class="text-danger">0.00</span>)
                                    <?php
									}
									?>
                                    </td>
                                    <?php
									}
									?>
                                   <!-- <td>Balance USD</td>
                                    <td class="text-right"><span id="balance_USD">0.00</span> (<span id="balance_USD_KHR" class="text-danger">0.00</span>)</td>-->
                                </tr>
                                <!--<tr>
                                    <td width="25%"><?=lang("total_items");?></td>
                                    <td width="25%" class="text-right"><span id="item_count">0.00</span></td>
                                    <td width="25%"><?=lang("total_payable");?></td>
                                    <td width="25%" class="text-right"><span id="twt">0.00</span></td>
                                </tr>
                                <tr>
                                    <td><?=lang("total_paying");?></td>
                                    <td class="text-right"><span id="total_paying">0.00</span></td>
                                    <td><?=lang("balance");?></td>
                                    <td class="text-right"><span id="balance">0.00</span></td>
                                </tr>-->
                                </tbody>
                            </table>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                    
               
                <!-- <div class="col-md-2 col-sm-3 text-center">
                        <span style="font-size: 1.2em; font-weight: bold;"><?=lang('quick_cash');?></span>

                        <div class="btn-group btn-group-vertical">
                            <button type="button" class="btn btn-lg btn-info quick-cash" id="quick-payable">0.00
                            </button>
                            <?php
                                foreach (lang('quick_cash_notes') as $cash_note_amount) {
                                    echo '<button type="button" class="btn btn-lg btn-warning quick-cash">' . $cash_note_amount . '</button>';
                                }
                            ?>
                            <button type="button" class="btn btn-lg btn-danger"
                                    id="clear-cash-notes"><?=lang('clear');?></button>
                        </div>
                    </div> -->
 </div>                    

            </div>
            <div class="modal-footer">
                <button class="btn btn-block btn-lg btn-primary" id="submit-sale"><?=lang('submit');?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="cmModal" tabindex="-1" role="dialog" aria-labelledby="cmModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">
                    <i class="fa fa-2x">&times;</i></span>
                    <span class="sr-only"><?=lang('close');?></span>
                </button>
                <h4 class="modal-title" id="cmModalLabel"></h4>
            </div>
            <div class="modal-body" id="pr_popover_content">
                <div class="form-group">
                    <?= lang('comment', 'icomment'); ?>
                    <?= form_textarea('comment', '', 'class="form-control" id="icomment" style="height:80px;"'); ?>
                </div>
                <div class="form-group">
                    <?= lang('ordered', 'iordered'); ?>
                    <?php
                    $opts = array(0 => lang('no'), 1 => lang('yes'));
                    ?>
                    <?= form_dropdown('ordered', $opts, '', 'class="form-control" id="iordered" style="width:100%;"'); ?>
                </div>
                <input type="hidden" id="irow_id" value=""/>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="editComment"><?=lang('submit')?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="prModal" tabindex="-1" role="dialog" aria-labelledby="prModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true"><i
                            class="fa fa-2x">&times;</i></span><span class="sr-only"><?=lang('close');?></span></button>
                <h4 class="modal-title" id="prModalLabel"></h4>
            </div>
            <div class="modal-body" id="pr_popover_content">
                <form class="form-horizontal" role="form">
                   
                    <div class="form-group">
                        <label for="poption" class="col-sm-4 control-label"><?=lang('recipe_addon')?></label>
                        <div class="col-sm-8">
                            <div id="poaddon-div"></div>
                        </div>
                    </div>
                  
                  
                    <div class="form-group">
                        <label for="pquantity" class="col-sm-4 control-label"><?=lang('quantity')?></label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control kb-pad" disabled id="pquantity">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="pprice" class="col-sm-4 control-label"><?=lang('unit_price')?></label>

                        <div class="col-sm-8">
                            <input type="text" class="form-control kb-pad" disabled id="pprice" <?= ($Owner || $Admin || $GP['edit_price']) ? '' : 'readonly'; ?>>
                        </div>
                    </div>
                    <input type="hidden" id="punit_price" value=""/>
                    <input type="hidden" id="old_tax" value=""/>
                    <input type="hidden" id="old_qty" value=""/>
                    <input type="hidden" id="old_price" value=""/>
                    <input type="hidden" id="row_id" value=""/>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="editItem"><?=lang('submit')?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade in" id="gcModal" tabindex="-1" role="dialog" aria-labelledby="mModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i
                        class="fa fa-2x">&times;</i></button>
                <h4 class="modal-title" id="myModalLabel"><?=lang('sell_gift_card');?></h4>
            </div>
            <div class="modal-body">
                <p><?=lang('enter_info');?></p>

                <div class="alert alert-danger gcerror-con" style="display: none;">
                    <button data-dismiss="alert" class="close" type="button">×</button>
                    <span id="gcerror"></span>
                </div>
                <div class="form-group">
                    <?=lang("card_no", "gccard_no");?> *
                    <div class="input-group">
                        <?php echo form_input('gccard_no', '', 'class="form-control" id="gccard_no"'); ?>
                        <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;">
                            <a href="#" id="genNo"><i class="fa fa-cogs"></i></a>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="gcname" value="<?=lang('gift_card')?>" id="gcname"/>

                <div class="form-group">
                    <?=lang("value", "gcvalue");?> *
                    <?php echo form_input('gcvalue', '', 'class="form-control" id="gcvalue"'); ?>
                </div>
                <div class="form-group">
                    <?=lang("price", "gcprice");?> *
                    <?php echo form_input('gcprice', '', 'class="form-control" id="gcprice"'); ?>
                </div>
                <div class="form-group">
                    <?=lang("customer", "gccustomer");?>
                    <?php echo form_input('gccustomer', '', 'class="form-control" id="gccustomer"'); ?>
                </div>
                <div class="form-group">
                    <?=lang("expiry_date", "gcexpiry");?>
                    <?php echo form_input('gcexpiry', $this->sma->hrsd(date("Y-m-d", strtotime("+2 year"))), 'class="form-control date" id="gcexpiry"'); ?>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" id="addGiftCard" class="btn btn-primary"><?=lang('sell_gift_card')?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade in" id="mModal" tabindex="-1" role="dialog" aria-labelledby="mModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true"><i
                            class="fa fa-2x">&times;</i></span><span class="sr-only"><?=lang('close');?></span></button>
                <h4 class="modal-title" id="mModalLabel"><?=lang('add_product_manually')?></h4>
            </div>
            <div class="modal-body" id="pr_popover_content">
                <form class="form-horizontal" role="form">
                    <div class="form-group">
                        <label for="mcode" class="col-sm-4 control-label"><?=lang('product_code')?> *</label>

                        <div class="col-sm-8">
                            <input type="text" class="form-control kb-text" id="mcode">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="mname" class="col-sm-4 control-label"><?=lang('product_name')?> *</label>

                        <div class="col-sm-8">
                            <input type="text" class="form-control kb-text" id="mname">
                        </div>
                    </div>
                    <?php if ($Settings->tax1) {
                        ?>
                        <div class="form-group">
                            <label for="mtax" class="col-sm-4 control-label"><?=lang('product_tax')?> *</label>

                            <div class="col-sm-8">
                                <?php
                                    $tr[""] = "";
                                        foreach ($tax_rates as $tax) {
                                            $tr[$tax->id] = $tax->name;
                                        }
                                        echo form_dropdown('mtax', $tr, "", 'id="mtax" class="form-control pos-input-tip" style="width:100%;"');
                                    ?>
                            </div>
                        </div>
                    <?php }
                    ?>
                    <div class="form-group">
                        <label for="mquantity" class="col-sm-4 control-label"><?=lang('quantity')?> *</label>

                        <div class="col-sm-8">
                            <input type="text" class="form-control kb-pad" id="mquantity">
                        </div>
                    </div>
                    <?php if ($Settings->product_discount && ($Owner || $Admin || $this->session->userdata('allow_discount'))) {?>
                        <div class="form-group">
                            <label for="mdiscount"
                                   class="col-sm-4 control-label"><?=lang('product_discount')?></label>

                            <div class="col-sm-8">
                                <input type="text" class="form-control kb-pad" id="mdiscount">
                            </div>
                        </div>
                    <?php }
                    ?>
                    <div class="form-group">
                        <label for="mprice" class="col-sm-4 control-label"><?=lang('unit_price')?> *</label>

                        <div class="col-sm-8">
                            <input type="text" class="form-control kb-pad" id="mprice">
                        </div>
                    </div>
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th style="width:25%;"><?=lang('net_unit_price');?></th>
                            <th style="width:25%;"><span id="mnet_price"></span></th>
                            <th style="width:25%;"><?=lang('product_tax');?></th>
                            <th style="width:25%;"><span id="mpro_tax"></span></th>
                        </tr>
                    </table>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="addItemManually"><?=lang('submit')?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade in" id="sckModal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">
                <i class="fa fa-2x">&times;</i></span><span class="sr-only"><?=lang('close');?></span>
                </button>
                <button type="button" class="btn btn-xs btn-default no-print pull-right" style="margin-right:15px;" onclick="window.print();">
                    <i class="fa fa-print"></i> <?= lang('print'); ?>
                </button>
                <h4 class="modal-title" id="mModalLabel"><?=lang('shortcut_keys')?></h4>
            </div>
            <div class="modal-body" id="pr_popover_content">
                <table class="table table-bordered table-striped table-condensed table-hover"
                       style="margin-bottom: 0px;">
                    <thead>
                    <tr>
                        <th><?=lang('shortcut_keys')?></th>
                        <th><?=lang('actions')?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td><?=$pos_settings->focus_add_item?></td>
                        <td><?=lang('focus_add_item')?></td>
                    </tr>
                    <tr>
                        <td><?=$pos_settings->add_manual_product?></td>
                        <td><?=lang('add_manual_product')?></td>
                    </tr>
                    <tr>
                        <td><?=$pos_settings->customer_selection?></td>
                        <td><?=lang('customer_selection')?></td>
                    </tr>
                    <tr>
                        <td><?=$pos_settings->add_customer?></td>
                        <td><?=lang('add_customer')?></td>
                    </tr>
                    <tr>
                        <td><?=$pos_settings->toggle_category_slider?></td>
                        <td><?=lang('toggle_category_slider')?></td>
                    </tr>
                    <tr>
                        <td><?=$pos_settings->toggle_subcategory_slider?></td>
                        <td><?=lang('toggle_subcategory_slider')?></td>
                    </tr>
                    <tr>
                        <td><?=$pos_settings->cancel_sale?></td>
                        <td><?=lang('cancel_sale')?></td>
                    </tr>
                    <tr>
                        <td><?=$pos_settings->suspend_sale?></td>
                        <td><?=lang('suspend_sale')?></td>
                    </tr>
                    <tr>
                        <td><?=$pos_settings->print_items_list?></td>
                        <td><?=lang('print_items_list')?></td>
                    </tr>
                    <tr>
                        <td><?=$pos_settings->finalize_sale?></td>
                        <td><?=lang('finalize_sale')?></td>
                    </tr>
                    <tr>
                        <td><?=$pos_settings->today_sale?></td>
                        <td><?=lang('today_sale')?></td>
                    </tr>
                    <tr>
                        <td><?=$pos_settings->open_hold_bills?></td>
                        <td><?=lang('open_hold_bills')?></td>
                    </tr>
                    <tr>
                        <td><?=$pos_settings->close_register?></td>
                        <td><?=lang('close_register')?></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade in" id="sModal" tabindex="-1" role="dialog" aria-labelledby="sModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    <i class="fa fa-2x">&times;</i>
                </button>
                <h4 class="modal-title" id="sModalLabel"><?=lang('shipping');?></h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <?=lang("shipping", "shipping_input");?>
                    <?php echo form_input('shipping_input', '', 'class="form-control kb-pad" id="shipping_input"'); ?>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" id="updateShipping" class="btn btn-primary"><?=lang('update')?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade in" id="txModal" tabindex="-1" role="dialog" aria-labelledby="txModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i
                        class="fa fa-2x">&times;</i></button>
                <h4 class="modal-title" id="txModalLabel"><?=lang('edit_order_tax');?></h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <?=lang("order_tax", "order_tax_input");?>
<?php
    $tr[""] = "";
    foreach ($tax_rates as $tax) {
        $tr[$tax->id] = $tax->name;
    }
    echo form_dropdown('order_tax_input', $tr, "", 'id="order_tax_input" class="form-control pos-input-tip" style="width:100%;"');
?>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" id="updateOrderTax" class="btn btn-primary"><?=lang('update')?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade in" id="susModal" tabindex="-1" role="dialog" aria-labelledby="susModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i
                        class="fa fa-2x">&times;</i></button>
                <h4 class="modal-title" id="susModalLabel"><?=lang('suspend_sale');?></h4>
            </div>
            <div class="modal-body">
                <p><?=lang('type_reference_note');?></p>

                <div class="form-group">
                    <?=lang("reference_note", "reference_note");?>
                    <?= form_input('reference_note', (!empty($reference_note) ? $reference_note : ''), 'class="form-control kb-text" id="reference_note"'); ?>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" id="suspend_sale" class="btn btn-primary"><?=lang('submit')?></button>
            </div>
        </div>
    </div>
</div>

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

<?php unset($Settings->setting_id, $Settings->smtp_user, $Settings->smtp_pass, $Settings->smtp_port, $Settings->update, $Settings->reg_ver, $Settings->allow_reg, $Settings->default_email, $Settings->mmode, $Settings->timezone, $Settings->restrict_calendar, $Settings->restrict_user, $Settings->auto_reg, $Settings->reg_notification, $Settings->protocol, $Settings->mailpath, $Settings->smtp_crypto, $Settings->corn, $Settings->customer_group, $Settings->envato_username, $Settings->purchase_code);?>
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

<script type="text/javascript">
    var product_variant = 0, shipping = 0, p_page = 0, per_page = 0, tcp = "<?=$tcp?>", pro_limit = <?= $pos_settings->pro_limit; ?>,
        brand_id = 0, obrand_id = 0, cat_id = "<?=$pos_settings->default_category?>", ocat_id = "<?=$pos_settings->default_category?>", sub_cat_id = 0, osub_cat_id,
        count = 1, an = 1, DT = <?=$Settings->default_tax_rate?>,
        product_tax = 0, invoice_tax = 0, product_discount = 0, recipe_discount = 0,order_discount = 0, total_discount = 0, total = 0, total_paid = 0, grand_total = 0,
        KB = <?=$pos_settings->keyboard?>, tax_rates =<?php echo json_encode($tax_rates); ?>;
    var protect_delete = <?php if (!$Owner && !$Admin) {echo $pos_settings->pin_code ? '1' : '0';} else {echo '0';} ?>, billers = <?= json_encode($posbillers); ?>, biller = <?= json_encode($posbiller); ?>;
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
  /*  function widthFunctions(e) {
        var wh = $(window).height(),
            lth = $('#left-top').height(),
            lbh = $('#left-bottom').height();
        $('#item-list').css("height", wh - 140);
        $('#item-list').css("min-height", 515);
        $('#left-middle').css("height", wh - lth - lbh - 102);
        $('#left-middle').css("min-height", 278);
        $('#product-list').css("height", wh - lth - lbh - 107);
        $('#product-list').css("min-height", 278);
    }*/
    $(window).bind("resize", widthFunctions);
    $(document).ready(function () {
        $('#view-customer').click(function(){
            $('#myModal').modal({remote: site.base_url + 'customers/view/' + $("input[name=customer]").val()});
            $('#myModal').modal('show');
        });
        $('textarea').keydown(function (e) {
            if (e.which == 13) {
               var s = $(this).val();
               $(this).val(s+'\n').focus();
               e.preventDefault();
               return false;
            }
        });
        <?php if ($sid) { ?>
        
        localStorage.setItem('positems', JSON.stringify(<?=$items;?>));

        <?php } ?>

        <?php if ($oid) { ?>
        localStorage.setItem('positems', JSON.stringify(<?=$items;?>));
        <?php } ?>

<?php if ($this->session->userdata('remove_posls')) {?>

    if (localStorage.getItem('order_discount_input')) {
            localStorage.removeItem('order_discount_input');
        }
    // localStorage.removeItem('order_discount_input');

        if (localStorage.getItem('positems')) {
            localStorage.removeItem('positems');
        }
        if (localStorage.getItem('posdiscount')) {
            localStorage.removeItem('posdiscount');
        }
        if (localStorage.getItem('postax2')) {
            localStorage.removeItem('postax2');
        }
        if (localStorage.getItem('posshipping')) {
            localStorage.removeItem('posshipping');
        }
        if (localStorage.getItem('poswarehouse')) {
            localStorage.removeItem('poswarehouse');
        }
        if (localStorage.getItem('posnote')) {
            localStorage.removeItem('posnote');
        }
        if (localStorage.getItem('poscustomer')) {
            localStorage.removeItem('poscustomer');
        }
        if (localStorage.getItem('posbiller')) {
            localStorage.removeItem('posbiller');
        }
        if (localStorage.getItem('poscurrency')) {
            localStorage.removeItem('poscurrency');
        }
        if (localStorage.getItem('posnote')) {
            localStorage.removeItem('posnote');
        }
        if (localStorage.getItem('staffnote')) {
            localStorage.removeItem('staffnote');
        }
        <?php $this->sma->unset_data('remove_posls');}
        ?>
        widthFunctions();
        <?php if ($suspend_sale) {?>
        localStorage.setItem('postax2', '<?=$suspend_sale->order_tax_id;?>');
        localStorage.setItem('posdiscount', '<?=$suspend_sale->order_discount_id;?>');
        localStorage.setItem('poswarehouse', '<?=$suspend_sale->warehouse_id;?>');
        localStorage.setItem('poscustomer', '<?=$suspend_sale->customer_id;?>');
        localStorage.setItem('posbiller', '<?=$suspend_sale->biller_id;?>');
        localStorage.setItem('posshipping', '<?=$suspend_sale->shipping;?>');
        <?php }
        ?>
        <?php if ($old_sale) {?>
        localStorage.setItem('postax2', '<?=$old_sale->order_tax_id;?>');
        localStorage.setItem('posdiscount', '<?=$old_sale->order_discount_id;?>');
        localStorage.setItem('poswarehouse', '<?=$old_sale->warehouse_id;?>');
        localStorage.setItem('poscustomer', '<?=$old_sale->customer_id;?>');
        localStorage.setItem('posbiller', '<?=$old_sale->biller_id;?>');
        localStorage.setItem('posshipping', '<?=$old_sale->shipping;?>');
        <?php }
        ?>
<?php if ($this->input->get('customer')) {?>
        if (!localStorage.getItem('positems')) {
            localStorage.setItem('poscustomer', <?=$this->input->get('customer');?>);
        } else if (!localStorage.getItem('poscustomer')) {
            localStorage.setItem('poscustomer', <?=$customer->id;?>);
        }
        <?php } else {?>
        if (!localStorage.getItem('poscustomer')) {
            localStorage.setItem('poscustomer', <?=$customer->id;?>);
        }
        <?php }
        ?>
        if (!localStorage.getItem('postax2')) {
            localStorage.setItem('postax2', <?=$Settings->default_tax_rate2;?>);
        }
        $('.select').select2({minimumResultsForSearch: 7});
        // var customers = [{
        //     id: <?=$customer->id;?>,
        //     text: '<?=$customer->company == '-' ? $customer->name : $customer->company;?>'
        // }];
        $('#poscustomer').val(localStorage.getItem('poscustomer')).select2({
            minimumInputLength: 1,
            data: [],
            initSelection: function (element, callback) {
                $.ajax({
                    type: "get", async: false,
                    url: "<?=admin_url('customers/getCustomer')?>/" + $(element).val(),
                    dataType: "json",
                    success: function (data) {
                        callback(data[0]);
                    }
                });
            },
            ajax: {
                url: site.base_url + "customers/suggestions",
                dataType: 'json',
                quietMillis: 15,
                data: function (term, page) {
                    return {
                        term: term,
                        limit: 10
                    };
                },
                results: function (data, page) {
                    if (data.results != null) {
                        return {results: data.results};
                    } else {
                        return {results: [{id: '', text: 'No Match Found'}]};
                    }
                }
            }
        });
        if (KB) {
            display_keyboards();

            var result = false, sct = '';
            $('#poscustomer').on('select2-opening', function () {
                sct = '';
                $('.select2-input').addClass('kb-text');
                display_keyboards();
                $('.select2-input').bind('change.keyboard', function (e, keyboard, el) {
                    if (el && el.value != '' && el.value.length > 0 && sct != el.value) {
                        sct = el.value;
                    }
                    if(!el && sct.length > 0) {
                        $('.select2-input').addClass('select2-active');
                        setTimeout(function() {
                            $.ajax({
                                type: "get",
                                async: false,
                                url: "<?=admin_url('customers/suggestions')?>/?term=" + sct,
                                dataType: "json",
                                success: function (res) {
                                    if (res.results != null) {
                                        $('#poscustomer').select2({data: res}).select2('open');
                                        $('.select2-input').removeClass('select2-active');
                                    } else {
                                        // bootbox.alert('no_match_found');
                                        $('#poscustomer').select2('close');
                                        $('#test').click();
                                    }
                                }
                            });
                        }, 500);
                    }
                });
            });

            $('#poscustomer').on('select2-close', function () {
                $('.select2-input').removeClass('kb-text');
                $('#test').click();
                $('select, .select').select2('destroy');
                $('select, .select').select2({minimumResultsForSearch: 7});
            });
            $(document).bind('click', '#test', function () {
                var kb = $('#test').keyboard().getkeyboard();
                kb.close();
            });

        }

        $(document).on('change', '#posbiller', function () {
            var sb = $(this).val();
            $.each(billers, function () {
                if(this.id == sb) {
                    biller = this;
                }
            });
            $('#biller').val(sb);
        });

        <?php for ($i = 1; $i <= 5; $i++) {?>
		
        
		
		<?php
		foreach($currency as $currency_row){
		
		$default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
		
		if($currency_row->code == $default_currency_data->code){
		  
		?>
		$('#paymentModal').on('change', '#amount_<?=$currency_row->code?>_<?=$i?>', function (e) {
            $('#amount_val_<?=$currency_row->code?>_<?=$i?>').val($(this).val());
        });
        $('#paymentModal').on('blur', '#amount_<?=$currency_row->code?>_<?=$i?>', function (e) {
            $('#amount_val_<?=$currency_row->code?>_<?=$i?>').val($(this).val());
        });
		<?php
		}else{
		?>
		
		$('#paymentModal').on('change', '#amount_<?=$currency_row->code?>_<?=$i?>', function (e) {
            $('#amount_val_<?=$currency_row->code?>_<?=$i?>').val($(this).val());
        });
        $('#paymentModal').on('blur', '#amount_<?=$currency_row->code?>_<?=$i?>', function (e) {
            $('#amount_val_<?=$currency_row->code?>_<?=$i?>').val($(this).val());
        });
		
		<?php
		}
		}
		?>
		
        $('#paymentModal').on('select2-close', '#paid_by_<?=$i?>', function (e) {
            $('#paid_by_val_<?=$i?>').val($(this).val());
        });
        $('#paymentModal').on('change', '#pcc_no_<?=$i?>', function (e) {
            $('#cc_no_val_<?=$i?>').val($(this).val());
        });
        $('#paymentModal').on('change', '#pcc_holder_<?=$i?>', function (e) {
            $('#cc_holder_val_<?=$i?>').val($(this).val());
        });
        $('#paymentModal').on('change', '#gift_card_no_<?=$i?>', function (e) {
            $('#paying_gift_card_no_val_<?=$i?>').val($(this).val());
        });
        $('#paymentModal').on('change', '#pcc_month_<?=$i?>', function (e) {
            $('#cc_month_val_<?=$i?>').val($(this).val());
        });
        $('#paymentModal').on('change', '#pcc_year_<?=$i?>', function (e) {
            $('#cc_year_val_<?=$i?>').val($(this).val());
        });
        $('#paymentModal').on('change', '#pcc_type_<?=$i?>', function (e) {
            $('#cc_type_val_<?=$i?>').val($(this).val());
        });
        $('#paymentModal').on('change', '#pcc_cvv2_<?=$i?>', function (e) {
            $('#cc_cvv2_val_<?=$i?>').val($(this).val());
        });
        $('#paymentModal').on('change', '#cheque_no_<?=$i?>', function (e) {
            $('#cheque_no_val_<?=$i?>').val($(this).val());
        });
        $('#paymentModal').on('change', '#payment_note_<?=$i?>', function (e) {
            $('#payment_note_val_<?=$i?>').val($(this).val());
        });
        <?php }
        ?>
		
		<?php
		$default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
		?>
		var currency_json = <?php echo json_encode($currency); ?>;
		var default_currency_rate = <?php echo $default_currency_data->rate; ?>;
		var default_currency_code = '<?php echo $default_currency_data->code; ?>';
	
		$(document).on('click', '#payment', function(){
            var offer_total_discount = localStorage.getItem('offer_total_discount') ? localStorage.getItem('offer_total_discount') : 0;
			<?php if ($sid) {?>
            suspend = $('<span></span>');
            suspend.html('<input type="hidden" name="delete_id" value="<?php echo $sid; ?>" />');
            suspend.appendTo("#hidesuspend");
            <?php }
            ?>
			var twt = formatDecimal((total + invoice_tax) - recipe_discount-order_discount-offer_total_discount + shipping);
			$('.total').val(twt);
            if (count == 1) {
                bootbox.alert('<?=lang('x_total');?>');
                return false;
            }
			<?php
			foreach($currency as $currency_row){
			$default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
			if($currency_row->code == $default_currency_data->code){
			?>
			 gtotal_<?php echo $currency_row->code; ?> = (parseFloat(twt) / parseFloat(<?php echo $currency_row->rate; ?>)) * <?php echo $default_currency_data->rate; ?>;
			 $('#twt_<?php echo $currency_row->code; ?>').text(formatMoney(gtotal_<?php echo $currency_row->code; ?>));
			<?php
			}else{
			?>
			  gtotal_<?php echo $currency_row->code; ?> = (parseFloat(twt) / parseFloat(<?php echo $currency_row->rate; ?>)) * <?php echo $default_currency_data->rate; ?>;
			  $('#twt_<?php echo $currency_row->code; ?>').text(formatMoney(gtotal_<?php echo $currency_row->code; ?>));
			<?php
			}
			?>
			 
			<?php
			}
			?>
			$('#item_count').text(count);
            $('#paymentModal').appendTo("body").modal('show');
			<?php
			foreach($currency as $currency_row){
			$default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
			if($currency_row->code == $default_currency_data->code){	
			?>
			/*$('#amount_<?php echo $currency_row->code; ?>_1').focus().val('');*/
            $('#amount_<?php echo $currency_row->code; ?>_1').focus().val(twt);

			<?php
			}else{
			?>
            $('#amount_<?php echo $currency_row->code; ?>_1').val('');
			<?php
			}
			}
			?>
			
		});
        
		
        $('#paymentModal').on('show.bs.modal', function(e) {
            $('#submit-sale').text('<?=lang('submit');?>').attr('disabled', false);
        });
        $('#paymentModal').on('shown.bs.modal', function(e) {

           $("select.paid_by").val("cash").change();

			<?php
			foreach($currency as $currency_row){
			$default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
			if($currency_row->code == $default_currency_data->code){	
			?>
			// $('#amount_<?php echo $currency_row->code; ?>_1').focus().val('');
            offer_total_discount = localStorage.getItem('offer_total_discount') ? localStorage.getItem('offer_total_discount') : 0;
            var twt = formatDecimal((total + invoice_tax) - recipe_discount-order_discount-offer_total_discount + shipping);
            $('#amount_<?php echo $currency_row->code; ?>_1').focus().val(twt);
			<?php
			}else{
			?>
            $('#amount_<?php echo $currency_row->code; ?>_1').val('');
			<?php
			}
			}
			?>
        });
		
       // var pi = 'amount_1', pa = 2;
		
		<?php
		foreach($currency as $currency_row){
		$default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
		if($currency_row->code == $default_currency_data->code){	
		?>
		var pi_<?php echo $currency_row->code; ?> = 'amount_<?php echo $currency_row->code; ?>_1';
		<?php
		}else{
		?>
		var pi_<?php echo $currency_row->code; ?> = 'amount_<?php echo $currency_row->code; ?>_1';
		<?php
		}
		}
		?>
		
       var pa = 2;
	   
        $(document).on('click', '.quick-cash', function () {
            if ($('#quick-payable').find('span.badge').length) {
                $('#clear-cash-notes').click();
            }
            var $quick_cash = $(this);
            var amt = $quick_cash.contents().filter(function () {
                return this.nodeType == 3;
            }).text();
            var th = ',';
            var $pi = $('#' + pi);
            amt = formatDecimal(amt.split(th).join("")) * 1 + $pi.val() * 1;
            $pi.val(formatDecimal(amt)).focus();
            var note_count = $quick_cash.find('span');
            if (note_count.length == 0) {
                $quick_cash.append('<span class="badge">1</span>');
            } else {
                note_count.text(parseInt(note_count.text()) + 1);
            }
        });
        $(document).on('click', '#quick-payable', function () {
            $('#clear-cash-notes').click();
            $(this).append('<span class="badge">1</span>');
            $('#amount_1').val(grand_total);
        });
        $(document).on('click', '#clear-cash-notes', function () {
            $('.quick-cash').find('.badge').remove();
            $('#' + pi).val('0').focus();
        });

        $(document).on('change', '.gift_card_no', function () {
            var cn = $(this).val() ? $(this).val() : '';
            var payid = $(this).attr('id'),
                id = payid.substr(payid.length - 1);
            if (cn != '') {
                $.ajax({
                    type: "get", async: false,
                    url: site.base_url + "sales/validate_gift_card/" + cn,
                    dataType: "json",
                    success: function (data) {
                        if (data === false) {
                            $('#gift_card_no_' + id).parent('.form-group').addClass('has-error');
                            bootbox.alert('<?=lang('incorrect_gift_card')?>');
                        } else if (data.customer_id !== null && data.customer_id !== $('#poscustomer').val()) {
                            $('#gift_card_no_' + id).parent('.form-group').addClass('has-error');
                            bootbox.alert('<?=lang('gift_card_not_for_customer')?>');
                        } else {
                            $('#gc_details_' + id).html('<small>Card No: ' + data.card_no + '<br>Value: ' + data.value + ' - Balance: ' + data.balance + '</small>');
                            $('#gift_card_no_' + id).parent('.form-group').removeClass('has-error');
                            //calculateTotals();
                            $('#amount_' + id).val(gtotal >= data.balance ? data.balance : gtotal).focus();
                        }
                    }
                });
            }
        });

        $(document).on('click', '.addButton', function () {
            if (pa <= 5) {
                $('#paid_by_1, #pcc_type_1').select2('destroy');
                var phtml = $('#payments').html(),
                    update_html = phtml.replace(/_1/g, '_' + pa);
               // pi = 'amount_' + pa;
				
				<?php
		foreach($currency as $currency_row){
		$default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
		if($currency_row->code == $default_currency_data->code){	
		?>
		var pi_<?php echo $currency_row->code; ?> = 'amount_<?php echo $currency_row->code; ?>_'+pa;
		<?php
		}else{
		?>
		var pi_<?php echo $currency_row->code; ?> = 'amount_<?php echo $currency_row->code; ?>_'+pa;
		<?php
		}
		}
		?>
				
                $('#multi-payment').append('<button type="button" class="close close-payment" style="margin: -10px 0px 0 0;"><i class="fa fa-2x">&times;</i></button>' + update_html);
                $('#paid_by_1, #pcc_type_1, #paid_by_' + pa + ', #pcc_type_' + pa).select2({minimumResultsForSearch: 7});
                read_card();
                pa++;
            } else {
                bootbox.alert('<?=lang('max_reached')?>');
                return false;
            }
            if (KB) { display_keyboards(); }
            $('#paymentModal').css('overflow-y', 'scroll');
        });

        $(document).on('click', '.close-payment', function () {
            $(this).next().remove();
            $(this).remove();
            pa--;
        });

        $(document).on('focus', '.amount', function () {
            pi = $(this).attr('id');
            calculateTotals();
        }).on('blur', '.amount', function () {
            calculateTotals();
        });

        function calculateTotals() {
			
            //var total_paying = 0;
            //var ia = $(".amount");
			
			var value_amount = 0;
			var total_paying = 0;
			var ia = $(".amount");
			
            $.each(ia, function (i) {
				
                //var this_amount = formatCNum($(this).val() ? $(this).val() : 0);
                //total_paying += parseFloat(this_amount);
				
				var code = $(this).attr('data-code');
				var rate = $(this).attr('data-rate');
				var cost_v = $(this).val();
				var a  = default_currency_code;
				var c  = default_currency_rate;
				
				if(code == default_currency_code){
					value_amount = cost_v;
				}else{
					value_amount = cost_v * rate;
				}
				var this_amount = formatCNum(value_amount ? value_amount : 0);
				total_paying += parseFloat(this_amount);
				
            });
			$('#total_paying').text(formatMoney(total_paying));
			
          <?php
		foreach($currency as $currency_row){
		$default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
		if($currency_row->code == $default_currency_data->code){
		?>
		$('#balance_<?php echo $currency_row->code; ?>').text(formatMoney(total_paying - (gtotal_<?php echo $currency_row->code; ?> * <?php echo $currency_row->rate; ?>) ));
		$('.balance_amount').val(formatDecimal(total_paying - (gtotal_<?php echo $currency_row->code; ?> * <?php echo $currency_row->rate; ?>)));
		<?php
		}else{
		?>
		
		$('#balance_<?php echo $currency_row->code; ?>').text(formatMoney((total_paying - (gtotal_<?php echo $currency_row->code; ?> * <?php echo $currency_row->rate; ?>) ) / <?php echo $currency_row->rate; ?>));
		
		<?php
		}
		
		if($currency_row->code == 'USD'){
		?>
		var balance_usd_total_amount = Math.abs((total_paying -  gtotal_<?php echo $currency_row->code; ?> * <?php echo $currency_row->rate; ?>));
		var balance_usd_remaing_float = balance_usd_total_amount.toString().split(".")[1];
		//var balance_usd_remaing_float = Math.abs((balance_usd_total_amount - Math.round(balance_usd_total_amount)) );
		
		var balance_usd_remaing_float = parseFloat('0.'+balance_usd_remaing_float) / parseFloat(0.00025);
		var balance_USD_KHR = parseFloat(balance_usd_remaing_float);
		$('#balance_USD_KHR').text(formatMoney(balance_USD_KHR));
		
		<?php
		}
		
		
		}
		?>
		
		total_paid = total_paying;
		grand_total = gtotal_<?php echo $default_currency_data->code; ?>;
		
        }

        $("#add_item").autocomplete({
            source: function (request, response) {
                if (!$('#poscustomer').val()) {
                    $('#add_item').val('').removeClass('ui-autocomplete-loading');
                    bootbox.alert('<?=lang('select_above');?>');
                    //response('');
                    $('#add_item').focus();
                    return false;
                }
                $.ajax({
                    type: 'get',
                    url: '<?=admin_url('sales/suggestions');?>',
                    dataType: "json",
                    data: {
                        term: request.term,
                        warehouse_id: $("#poswarehouse").val(),
                        customer_id: $("#poscustomer").val()
                    },
                    success: function (data) {
                        $(this).removeClass('ui-autocomplete-loading');
                        response(data);
                    }
                });
            },
            minLength: 1,
            autoFocus: false,
            delay: 250,
            response: function (event, ui) {
                if ($(this).val().length >= 16 && ui.content[0].id == 0) {
                    bootbox.alert('<?=lang('no_match_found')?>', function () {
                        $('#add_item').focus();
                    });
                    $(this).val('');
                }
                else if (ui.content.length == 1 && ui.content[0].id != 0) {
                    ui.item = ui.content[0];
                    $(this).data('ui-autocomplete')._trigger('select', 'autocompleteselect', ui);
                    $(this).autocomplete('close');
                }
                else if (ui.content.length == 1 && ui.content[0].id == 0) {
                    bootbox.alert('<?=lang('no_match_found')?>', function () {
                        $('#add_item').focus();
                    });
                    $(this).val('');

                }
            },
            select: function (event, ui) {
                event.preventDefault();
                if (ui.item.id !== 0) {
                    var row = add_invoice_item(ui.item);
                    if (row)
                        $(this).val('');
                } else {
                    bootbox.alert('<?=lang('no_match_found')?>');
                }
            }
        });

        <?php if ($pos_settings->tooltips) {echo '$(".pos-tip").tooltip();';}
        ?>
        // $('#posTable').stickyTableHeaders({fixedOffset: $('#product-list')});
        /*$('#posTable').stickyTableHeaders({scrollableArea: $('#product-list')});
        $('#product-list, #category-list, #subcategory-list, #brands-list').perfectScrollbar({suppressScrollX: true});*/
        $('select, .select').select2({minimumResultsForSearch: 7});

        /*$(document).on('click', '.recipe', function (e) {*/
        $(document).on('click', '.recipe:not(".has-varients")', function (e) {
            
            $('#modal-loading').show();
            code = $(this).val(),
			
                wh = $('#poswarehouse').val(),
                cu = $('#poscustomer').val();
            $.ajax({
                type: "get",
                url: "<?=admin_url('qsr/getrecipeDataByCode')?>",
                data: {code: code, warehouse_id: wh, customer_id: cu},
                dataType: "json",
                success: function (data) {
					
                    e.preventDefault();
                    if (data !== null) {						
                        add_invoice_item(data);  
                        $('#order_discount_input').trigger('change');                      
                        $('#modal-loading').hide();
                    } else {
                        bootbox.alert('<?=lang('no_match_found')?>');
                        $('#modal-loading').hide();
                    }
                }
            });
        });

    $(document).on('click', '.recipe-varient', function (e) {
            
            code = $(this).val(),
        $('#myVaraintModal').modal('hide');$('#modal-loading').show();
                wh = $('#poswarehouse').val(),
                cu = $('#poscustomer').val();
        $vid = $(this).attr('data-id');
        
            $.ajax({
                type: "get",
                url: "<?=admin_url('qsr/getrecipeVarientDataByCode')?>",
                data: {code: code, warehouse_id: wh, customer_id: cu,variant:$vid},
                dataType: "json",
                success: function (data) {
                    
                    e.preventDefault();
                    if (data !== null) {                        
                        add_invoice_item(data);
                        $('#order_discount_input').trigger('change');
                        $('#modal-loading').hide();
                    } else {
                        bootbox.alert('<?=lang('no_match_found')?>');
                        $('#modal-loading').hide();
                    }
                }
            });
        });

    $(document).on('click','.has-varients',function(){
        $obj = $(this);
        $v = $obj.attr('value');
        $popcon = $obj.closest('span').find('.variant-popup').html();
        $('#myVaraintModal .modal-body').html($popcon);
        $('#myVaraintModal').modal('show');
    });
        
    

        $(document).on('click', '.category', function () {
            if (cat_id != $(this).val()) {
                $('#open-category').click();
                $('#modal-loading').show();
                cat_id = $(this).val();
				var warehouse_id = '<?php echo $this->session->userdata('warehouse_id'); ?>';
				
                $.ajax({
                    type: "get",
                    url: "<?=admin_url('qsr/ajaxcategorydata');?>",
                    data: {category_id: cat_id, warehouse_id: warehouse_id},
                    dataType: "json",
                    success: function (data) {
                        $('#item-list').empty();
                        var newPrs = $('<div></div>');
                        newPrs.html(data.recipe);
                        newPrs.appendTo("#item-list");
                        $('#subcategory-list').empty();
                        var newScs = $('<div></div>');
                        newScs.html(data.subcategories);
                        newScs.appendTo("#subcategory-list");
                        tcp = data.tcp;
                        nav_pointer();
                    }
                }).done(function () {
                    p_page = 'n';
                    $('#category-' + cat_id).addClass('active');
                    $('#category-' + ocat_id).removeClass('active');
                    ocat_id = cat_id;
                    $('#modal-loading').hide();
                    nav_pointer();
                });
            }
        });
		
        $('#category-' + cat_id).addClass('active');

        $(document).on('click', '.brand', function () {
            if (brand_id != $(this).val()) {
                $('#open-brands').click();
                $('#modal-loading').show();
                brand_id = $(this).val();
                $.ajax({
                    type: "get",
                    url: "<?=admin_url('qsr/ajaxbranddata');?>",
                    data: {brand_id: brand_id},
                    dataType: "json",
                    success: function (data) {
                        $('#item-list').empty();
                        var newPrs = $('<div></div>');
                        newPrs.html(data.recipe);
                        newPrs.appendTo("#item-list");
                        tcp = data.tcp;
                        nav_pointer();
                    }
                }).done(function () {
                    p_page = 'n';
                    $('#brand-' + brand_id).addClass('active');
                    $('#brand-' + obrand_id).removeClass('active');
                    obrand_id = brand_id;
                    $('#category-' + cat_id).removeClass('active');
                    $('#subcategory-' + sub_cat_id).removeClass('active');
                    cat_id = 0; sub_cat_id = 0;
                    $('#modal-loading').hide();
                    nav_pointer();
                });
            }
        });

        $(document).on('click', '.subcategory', function () {
            if (sub_cat_id != $(this).val()) {
                $('#open-subcategory').click();
                $('#modal-loading').show();
                sub_cat_id = $(this).val();
				var warehouse_id = '<?php echo $this->session->userdata('warehouse_id'); ?>';
				
                $.ajax({
                    type: "get",
                    url: "<?=admin_url('qsr/ajaxrecipe');?>",
                    data: {category_id: cat_id, warehouse_id: warehouse_id, subcategory_id: sub_cat_id, per_page: p_page},
                    dataType: "html",
                    success: function (data) {
                        $('#item-list').empty();
                        var newPrs = $('<div></div>');
                        newPrs.html(data);
                        newPrs.appendTo("#item-list");
                    }
                }).done(function () {
                    p_page = 'n';
                    $('#subcategory-' + sub_cat_id).addClass('active');
                    $('#subcategory-' + osub_cat_id).removeClass('active');
                    $('#modal-loading').hide();
                });
            }
        });
		
		

        $('#next').click(function () {
            if (p_page == 'n') {
                p_page = 0
            }
            p_page = p_page + pro_limit;
            if (tcp >= pro_limit && p_page < tcp) {
                $('#modal-loading').show();
				var warehouse_id = '<?php echo $this->session->userdata('warehouse_id'); ?>';
                $.ajax({
                    type: "get",
                    url: "<?=admin_url('qsr/ajaxrecipe');?>",
                    data: {category_id: cat_id, warehouse_id: warehouse_id, subcategory_id: sub_cat_id, per_page: p_page},
                    dataType: "html",
                    success: function (data) {
                        $('#item-list').empty();
                        var newPrs = $('<div></div>');
                        newPrs.html(data);
                        newPrs.appendTo("#item-list");
                        nav_pointer();
                    }
                }).done(function () {
                    $('#modal-loading').hide();
                });
            } else {
                p_page = p_page - pro_limit;
            }
        });

        
		
		 $('#previous').click(function () {
            if (p_page == 'n') {
                p_page = 0;
            }
            if (p_page != 0) {
                $('#modal-loading').show();
                p_page = p_page - pro_limit;
                if (p_page == 0) {
                    p_page = 'n'
                }
				var warehouse_id = '<?php echo $this->session->userdata('warehouse_id'); ?>';
                $.ajax({
                    type: "get",
                    url: "<?=admin_url('qsr/ajaxrecipe');?>",
                    data: {category_id: cat_id, warehouse_id: warehouse_id, subcategory_id: sub_cat_id, per_page: p_page},
                    dataType: "html",
                    success: function (data) {
                        $('#item-list').empty();
                        var newPrs = $('<div></div>');
                        newPrs.html(data);
                        newPrs.appendTo("#item-list");
                        nav_pointer();
                    }

                }).done(function () {
                    $('#modal-loading').hide();
                });
            }
        });

       /* $(document).on('change', '.paid_by', function () {
            $('#clear-cash-notes').click();
            $('#amount_1').val(grand_total);
            var p_val = $(this).val(),
                id = $(this).attr('id'),
                pa_no = id.substr(id.length - 1);
            $('#rpaidby').val(p_val);
            if (p_val == 'cash' || p_val == 'other') {
                $('.pcheque_' + pa_no).hide();
                $('.pcc_' + pa_no).hide();
                $('.pcash_' + pa_no).show();
                $('#amount_' + pa_no).focus();
            } else if (p_val == 'CC' || p_val == 'stripe' || p_val == 'ppp' || p_val == 'authorize') {
                $('.pcheque_' + pa_no).hide();
                $('.pcash_' + pa_no).hide();
                $('.pcc_' + pa_no).show();
                $('#swipe_' + pa_no).focus();
            } else if (p_val == 'Cheque') {
                $('.pcc_' + pa_no).hide();
                $('.pcash_' + pa_no).hide();
                $('.pcheque_' + pa_no).show();
                $('#cheque_no_' + pa_no).focus();
            } else {
                $('.pcheque_' + pa_no).hide();
                $('.pcc_' + pa_no).hide();
                $('.pcash_' + pa_no).hide();
            }
            if (p_val == 'gift_card') {
                $('.gc_' + pa_no).show();
                $('.ngc_' + pa_no).hide();
                $('#gift_card_no_' + pa_no).focus();
            } else {
                $('.ngc_' + pa_no).show();
                $('.gc_' + pa_no).hide();
                $('#gc_details_' + pa_no).html('');
            }
        });*/
		
		 $(document).on('change', '.paid_by', function () {
            $('#clear-cash-notes').click();
			
			<?php
			foreach($currency as $currency_row){
			$default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
			if($currency_row->code == $default_currency_data->code){
			?>
			$('#amount_<?php echo $currency_row->code; ?>_1').val('');
			<?php
			}else{
			?>
			$('#amount_<?php echo $currency_row->code; ?>_1').val('');
			
			<?php
			}
			}
			?>
		
            $('#amount_1').val('');
			$('#amount_USD_1').val('');
            var p_val = $(this).val(),
                id = $(this).attr('id'),
                pa_no = id.substr(id.length - 1);
            $('#rpaidby').val(p_val);
            if (p_val == 'cash' || p_val == 'other') {
				$('.gc_' + pa_no).hide();
                $('.pcheque_' + pa_no).hide();
                $('.pcc_' + pa_no).hide();
                $('.pcash_' + pa_no).show();
				
				<?php
				foreach($currency as $currency_row){
				$default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
				if($currency_row->code == $default_currency_data->code){
				?>
				$('#amount_<?php echo $currency_row->code ?>_' + pa_no).focus();
				<?php
				}else{
				?>
				$('.amount_<?php echo $currency_row->code ?>_'+pa_no).val('');
				
				<?php
				}
				}
				?>
               
				
				$('.multi_currency_'+pa_no).show();
            } else if (p_val == 'CC' || p_val == 'stripe' || p_val == 'ppp' || p_val == 'authorize') {
                $('.pcheque_' + pa_no).hide();
				$('.gc_' + pa_no).hide();
                $('.pcash_' + pa_no).hide();
                $('.pcc_' + pa_no).show();+
				$('.multi_currency_'+pa_no).hide();
				<?php
				foreach($currency as $currency_row){
				$default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
				if($currency_row->code == $default_currency_data->code){
				?>
				$('#amount_<?php echo $currency_row->code ?>_' + pa_no).focus();
				<?php
				}else{
				?>
				$('.amount_<?php echo $currency_row->code ?>_'+pa_no).val('');
				
				<?php
				}
				}
				?>
            } else if (p_val == 'gift_card') {
                $('.gc_' + pa_no).show();
                $('.ngc_' + pa_no).hide();
				$('.multi_currency_'+pa_no).hide();
				<?php
				foreach($currency as $currency_row){
				$default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
				if($currency_row->code == $default_currency_data->code){
				?>
				$('#amount_<?php echo $currency_row->code ?>_' + pa_no).focus();
				<?php
				}else{
				?>
				$('.amount_<?php echo $currency_row->code ?>_'+pa_no).val('');
				
				<?php
				}
				}
				?>
            } else {
                $('.ngc_' + pa_no).show();
                $('.gc_' + pa_no).hide();
                $('#gc_details_' + pa_no).html('');
				$('.multi_currency_'+pa_no).hide();
				<?php
				foreach($currency as $currency_row){
				$default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
				if($currency_row->code == $default_currency_data->code){
				?>
				$('#amount_<?php echo $currency_row->code ?>_' + pa_no).focus();
				<?php
				}else{
				?>
				$('.amount_<?php echo $currency_row->code ?>_'+pa_no).val('');
				
				<?php
				}
				}
				?>
            }        
			
			 var currentYear = new Date().getFullYear();  

				for (var i = 1; i <= 20; i++ ) {
					$(".pcc_year").append(

						$("<option></option>")
							.attr("value", currentYear)
							.text(currentYear)

					);
					currentYear++;
				}
			
			
        });

        $(document).on('click', '#submit-sale', function () {

            calculateTotals();
            var balance = $('.balance_amount').val();            
            /*alert(balance);return false;*/
            if (balance >= 0) {
                  //localStorage.removeItem('order_discount_input');
                  $('#submit-sale').text('<?=lang('loading');?>').attr('disabled', true);
                  getTotalDiscount();
                  $('#pos-sale-form').submit();
            }
            else{
                bootbox.alert("Paid amount is less than the payable amount.");
            }  
        });

        $(document).on('click', '#submit-sale_old', function () {

            if (total_paid == 0 || total_paid < grand_total) {
                bootbox.confirm("<?=lang('paid_l_t_payable');?>", function (res) {
                    if (res == true) {
                        $('#pos_note').val(localStorage.getItem('posnote'));
                        $('#staff_note').val(localStorage.getItem('staffnote'));
                        $('#submit-sale').text('<?=lang('loading');?>').attr('disabled', true);
                        $('#pos-sale-form').submit();
                    }
                });
                return false;
            } else {
                $('#pos_note').val(localStorage.getItem('posnote'));
                $('#staff_note').val(localStorage.getItem('staffnote'));
                $(this).text('<?=lang('loading');?>').attr('disabled', true);
                $('#pos-sale-form').submit();
            }
        });
        $('#suspend').click(function () {
            if (count <= 1) {
                bootbox.alert('<?=lang('x_suspend');?>');
                return false;
            } else {
                $('#susModal').modal();
            }
        });
        $('#suspend_sale').click(function () {
            ref = $('#reference_note').val();
            if (!ref || ref == '') {
                bootbox.alert('<?=lang('type_reference_note');?>');
                return false;
            } else {
                suspend = $('<span></span>');
                <?php if ($sid) {?>
                suspend.html('<input type="hidden" name="delete_id" value="<?php echo $sid; ?>" /><input type="hidden" name="suspend" value="yes" /><input type="hidden" name="suspend_note" value="' + ref + '" />');
                <?php } else {?>
                suspend.html('<input type="hidden" name="suspend" value="yes" /><input type="hidden" name="suspend_note" value="' + ref + '" />');
                <?php }
                ?>
                suspend.appendTo("#hidesuspend");
                $('#total_items').val(count - 1);
                $('#pos-sale-form').submit();

            }
        });
    });

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

    $(function () {
        $(".alert").effect("shake");
        setTimeout(function () {
            $(".alert").hide('blind', {}, 500)
        }, 15000);
        <?php if ($pos_settings->display_time) {?>
        var now = new moment();
        $('#display_time').text(now.format((site.dateFormats.js_sdate).toUpperCase() + " HH:mm"));
        setInterval(function () {
            var now = new moment();
            $('#display_time').text(now.format((site.dateFormats.js_sdate).toUpperCase() + " HH:mm"));
        }, 1000);
        <?php }
        ?>
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
<?php
    $s2_lang_file = read_file('./assets/config_dumps/s2_lang.js');
    foreach (lang('select2_lang') as $s2_key => $s2_line) {
        $s2_data[$s2_key] = str_replace(array('{', '}'), array('"+', '+"'), $s2_line);
    }
    $s2_file_date = $this->parser->parse_string($s2_lang_file, $s2_data, true);
?>
<!--<script type="text/javascript" src="<?=$assets?>js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/perfect-scrollbar.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/select2.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/custom.js"></script>
<script type="text/javascript" src="<?=$assets?>js/jquery.calculator.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/bootstrapValidator.min.js"></script>
<script type="text/javascript" src="<?=$assets?>qsr/js/plugins.min.js"></script>
<script type="text/javascript" src="<?=$assets?>qsr/js/parse-track-data.js"></script>
<script type="text/javascript" src="<?=$assets?>qsr/js/pos.ajax.js"></script>-->
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
				
                $.get('<?= admin_url('qsr/p/order'); ?>', {data: JSON.stringify(socket_data)});
            });
            return false;
        }

        function printBill() {
            var socket_data = {
                'printer': <?= json_encode($printer); ?>,
                'logo': (biller && biller.logo ? biller.logo : ''),
                'text': bill_data
            };
            $.get('<?= admin_url('qsr/p'); ?>', {data: JSON.stringify(socket_data)});
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
            socket = new WebSocket('ws://127.0.0.1:6441');
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
<script type="text/javascript">

/*loadItems();*/
/*Sivan 05-10-2018 start qsr customer discount*/
<?php if($Settings->customer_discount =='customer') : ?>
$(document).on('change', '#order_discount_input', function () {      
    var recipeids ='';
         recipeids = $('.rid').map(function() {
            return this.value;
        }).get();

       var recipeqtys ='';
        recipeqtys = $('.rquantity').map(function() {
            return this.value;
        }).get();

      var discountid = $('#order_discount_input :selected').val();
      var input_discount = 0;

    if(discountid !=0){
            $.ajax({
                type: 'POST',
                url: '<?=admin_url('qsr/calculate_customerdiscount');?>',
                dataType: "json",
                 async : false,
                data: {
                    recipeids: recipeids,recipeqtys: recipeqtys,discountid:discountid,divide: 1
                },
                success: function (data) {                    
                    console.log(data);
                    input_discount += data; 
                    localStorage.setItem('input_discount', JSON.stringify(input_discount)); 
                    loadItems();
                    /*localStorage.setItem('order_discount_input', JSON.stringify($this_obj.val()));*/
                }
            });
    }
    else{
        localStorage.setItem('input_discount', JSON.stringify(0));
        loadItems();
        /*localStorage.setItem('order_discount_input', JSON.stringify($this_obj.val()));*/
    }
// getTotalDiscount();
});
<?php endif;?>
function getTotalDiscount(){

    var total =$('#total').text();    
    $.ajax({
        type: 'POST',
        url: '<?=admin_url('qsr/getTotalDiscount');?>',
        dataType: 'JSON',
        async: false,
        data: {value:total},
        success: function(data) {
            if(data != 0){
                localStorage.setItem('offer_total_discount', data);
                // alert(data);
                offer_total_discount = localStorage.getItem('offer_total_discount') ? localStorage.getItem('offer_total_discount') : 0;
                /*alert(offer_total_discount);*/
                /*localStorage.setItem('offer_total_discount', JSON.stringify(data));*/
                console.log(data);
            }else{
                localStorage.setItem('offer_total_discount', 0);
                /*localStorage.setItem('offer_total_discount', JSON.stringify(0));*/
            }
        }
    });
}
<?php if($Settings->customer_discount =='manual') : ?>
      $('#order_discount_input').on('change', function(e) {
        var unit_price =  $('#total').text(); 
        unit_price = parseFloat(unit_price);
        var ds = $('#order_discount_input').val() ? $('#order_discount_input').val() : '0';
        /*alert(ds);*/
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
    /*alert(input_discount);*/
    localStorage.setItem('input_discount', JSON.stringify(input_discount));
    localStorage.setItem('order_discount_input', ds);
    loadItems();
    /*localStorage.setItem('order_discount_input', JSON.stringify(input_discount));*/
    
});
// getTotalDiscount();
<?php endif;?>
/*Sivan 05-10-2018 End qst customer discount*/

$('.sortable_table tbody').sortable({
    containerSelector: 'tr'
});
</script>
<script type="text/javascript" charset="UTF-8"><?=$s2_file_date?></script>
<div id="ajaxCall"><i class="fa fa-spinner fa-pulse"></i></div>
<?php
if (isset($print) && !empty($print)) {
    /* include FCPATH.'themes'.DIRECTORY_SEPARATOR.$Settings->theme.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'pos'.DIRECTORY_SEPARATOR.'remote_printing.php'; */
    include 'remote_printing.php';
}
?>

<script type="text/javascript" src="<?=$assets?>js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/perfect-scrollbar.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/select2.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/custom.js"></script>
<script type="text/javascript" src="<?=$assets?>js/jquery.calculator.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/bootstrapValidator.min.js"></script>
<script type="text/javascript" src="<?=$assets?>qsr/js/plugins.min.js"></script>
<script type="text/javascript" src="<?=$assets?>qsr/js/parse-track-data.js"></script>
<script type="text/javascript" src="<?=$assets?>qsr/js/pos.ajax.js?v=1"></script>
<script type="text/javascript" src="<?=$assets?>js/dragscrollable.js"></script>
<script type="text/javascript" src="<?=$assets?>js/dragscroll.js"></script>

<script>
    $('#subcategory-list, #scroller').dragscrollable({
    dragSelector: 'button', 
    acceptPropagatedEvent: false
});
    </script>
    
<script type="text/javascript">
$('.sortable_table tbody').sortable({
    containerSelector: 'tr'
});
   
</script>
<style>
    .variant-popup{
    display: none;
    }
    .sname{
    max-width:50px;
    }
</style>


<div class="modal fade in" id="myVaraintModal" tabindex="-1" role="dialog" aria-labelledby="VariantModalLabel"
     aria-hidden="true" style="z-index:9999">
    <div class="modal-dialog modal-md">
    <div class="modal-content">
        
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">×</i>
            </button>
            <h4 class="modal-title" id="customerModalLabel">Variants</h4>
        </div>
        
        <div class="modal-body">
    </div>
    
    </div>
    </div>
    </div>
</body>
</html>
