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
echo admin_form_open("pos/bbqbilling?order_type=".$order_type."&bill_type=3&bils=".$bils."&table=".$table_id."&splits=".$split_id, $attrib);?>


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
        <div id="bbq">
        	
            <div class="clearfix"></div>
            <?php 
				$current_days = date('l');
				$buyxgetx = $this->site->getBBQbuyxgetxDAYS($current_days);
				
                if(!empty($order_bbq))
                {
			?>
                <input type="hidden" name="reference_no" value="<?php echo $order_bbq->reference_no; ?>">
                    <input type="hidden" name="customer_id" value="<?php echo $order_bbq->customer_id; ?>">
                    <input type="hidden" name="customer" value="<?php echo $order_bbq->customer; ?>">  
                    <input type="hidden" name="biller_id" value="<?php echo $order_bbq->biller_id; ?>">  
                    <input type="hidden" name="biller" value="<?php echo $order_bbq->biller; ?>"> 
                    <input type="hidden" name="warehouse_id" value="<?php echo $order_bbq->warehouse_id; ?>"> 
                    
            <div class="row">
            <?php
			for($i=0; $i<$bils; $i++){
				
				$adult = $this->site->CalculationBBQbuyget($buyxgetx->adult_buy, $buyxgetx->adult_get, 0);
				$child = $this->site->CalculationBBQbuyget($buyxgetx->child_buy, $buyxgetx->child_get, 0);
				$kids = $this->site->CalculationBBQbuyget($buyxgetx->kids_buy, $buyxgetx->kids_get, 0);
			?>
            <div class="col-xs-6">
            <table  class="table table-bordered table-striped copytabl single_table_bg">
                <thead>
                    <tr>
                        <th>Details</th>
                        <th>Price</th>
                        <th>Covers</th>
                        <th>Discount (Cover)</th>
                        <th>Subtotal</th>
                        
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Adult</td>
                        <td><span><?=$this->sma->formatMoney($order_bbq->adult_price)?></span><input type="hidden" name="adult_price[<?=$i?>]" value="<?=$this->sma->formatDecimal($order_bbq->adult_price)?>" id="adult_price_<?=$i?>"></td>
                        <td>
                        <select name="number_of_adult" id="number_of_adult_<?=$i?>" class="form-control">
                        	<?php
							for($ai=0; $ai<=15; $ai++){
							?>
                        	<option value="<?=$ai?>"><?=$ai?></option>
                            <?php
							}
							?>
                        </select>
                        </td>
                        <td><span id="adult_<?=$i?>"><?= $adult ?></span> 
							<input type="hidden" name="adult_days[<?=$i?>]" id="adult_days_<?=$i?>" value="<?=$current_days?>">
                            <input type="hidden" name="adult_buyx[<?=$i?>]" id="adult_buyx_<?=$i?>" value="<?=$buyxgetx->adult_buy ?>">
                            <input type="hidden" name="adult_getx[<?=$i?>]" id="adult_getx_<?=$i?>" value="<?=$buyxgetx->adult_get?>">
                            <input type="hidden" name="adult_discount_cover[<?=$i?>]" id="adult_discount_cover_<?=$i?>" value="<?= $adult ?>">
                            
							</td>
                           
                        <td>
                        <span id="adult_subprice_text_<?=$i?>"><?=$this->sma->formatMoney(0)?></span> <input type="hidden" name="adult_subprice[<?=$i?>]" value="<?=$this->sma->formatDecimal(0)?>" id="adult_subprice_<?=$i?>">
                        </td>
                    </tr>
                    <tr>
                        <td>Child</td>
                        <td><span><?=$this->sma->formatMoney($order_bbq->child_price)?></span><input type="hidden" name="child_price[<?=$i?>]" value="<?=$this->sma->formatDecimal($order_bbq->child_price)?>" id="child_price_<?=$i?>"></td>
                        <td>
                        <select name="number_of_child" id="number_of_child_<?=$i?>" class="form-control">
                        	<?php
							for($ai=0; $ai<=15; $ai++){
							?>
                        	<option value="<?=$ai?>"><?=$ai?></option>
                            <?php
							}
							?>
                        </select>
                        </td>
                        <td>
                        <span id="child_<?=$i?>"><?= $child ?></span> 
							<input type="hidden" name="child_days[<?=$i?>]" id="child_days_<?=$i?>" value="<?=$current_days?>">
                            <input type="hidden" name="child_buyx[<?=$i?>]" id="child_buyx_<?=$i?>" value="<?=$buyxgetx->child_buy ?>">
                            <input type="hidden" name="child_getx[<?=$i?>]" id="child_getx_<?=$i?>" value="<?=$buyxgetx->child_get?>">
                            <input type="hidden" name="child_discount_cover[<?=$i?>]" id="child_discount_cover_<?=$i?>" value="<?= $child ?>">
                        </td>
                        
                        <td><span id="child_subprice_text_<?=$i?>"><?=$this->sma->formatMoney(0)?></span> <input type="hidden" name="child_subprice[<?=$i?>]" value="<?=$this->sma->formatDecimal(0)?>" id="child_subprice_<?=$i?>"></td>
                    </tr>
                    <tr>
                        <td>Kids</td>
                        <td><span><?=$this->sma->formatMoney($order_bbq->kids_price)?></span><input type="hidden" name="kids_price[<?=$i?>]" value="<?=$this->sma->formatDecimal($order_bbq->kids_price)?>" id="kids_price_<?=$i?>"></td>
                        <td>
                        <select name="number_of_kids" id="number_of_kids_<?=$i?>" class="form-control">
                        	<?php
							for($ai=0; $ai<=15; $ai++){
							?>
                        	<option value="<?=$ai?>"><?=$ai?></option>
                            <?php
							}
							?>
                        </select>
                        </td>
                        <td>
                        <span id="kids_<?=$i?>"><?= $kids ?></span> 
							<input type="hidden" name="kids_days[<?=$i?>]" id="kids_days_<?=$i?>" value="<?=$current_days?>">
                            <input type="hidden" name="kids_buyx[<?=$i?>]" id="kids_buyx_<?=$i?>" value="<?=$buyxgetx->kids_buy ?>">
                            <input type="hidden" name="kids_getx[<?=$i?>]" id="kids_getx_<?=$i?>" value="<?=$buyxgetx->kids_get?>">
                            <input type="hidden" name="kids_discount_cover[<?=$i?>]" id="kids_discount_cover_<?=$i?>" value="<?= $kids ?>">
                        </td>
                        
                        <td>
                        <span id="kids_subprice_text_<?=$i?>"><?=$this->sma->formatMoney(0)?></span> <input type="hidden" name="kids_subprice[<?=$i?>]" value="<?=$this->sma->formatDecimal(0)?>" id="kids_subprice_<?=$i?>">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">Total Covers</td>
                        <td><span id="number_of_covers_text_<?=$i?>"><?= 0 ?></span> <input type="hidden" name="number_of_covers[<?=$i?>]" value="<?=0?>" id="number_of_covers_<?=$i?>"></td>
                    </tr>
                    <tr>
                        <td colspan="4">Total Amount</td>
                        <td><span id="total_amount_text_<?=$i?>"><?=$this->sma->formatMoney(0)?></span> <input type="hidden" name="total_amount[<?=$i?>]" value="<?=$this->sma->formatDecimal( 0)?>" id="total_amount_<?=$i?>"></td>
                    </tr>
                    <?php
                    $final_val = $this->sma->formatDecimal(0);
                    ?>
                    <tr>
                        <td colspan="3">BBQ Discount</td>
                        <td>
                            <?php
                            if($Settings->bbq_discount=='bbq'){
                            ?>
                            <select class="form-control" name="bbq_discount[<?=$i?>]" id="bbq_discount_<?=$i?>">
                                <option value="0">NO</option>
                                <?php
                                foreach($bbq_discount as $discount){
                                ?>
                                <option value="<?=$discount->discount_type == 'amount' ? $discount->discount : $discount->discount.'%' ?>"><?=$discount->name?></option>
                                <?php
                                }
                                ?>
                            </select>
                            <?php
                            }elseif($Settings->bbq_discount=='manual'){
                            ?>
                            <input type="text" name="bbq_discount[<?=$i?>]" autocomplete="off" class="form-control kb-pad" id="bbq_discount_<?=$i?>">
                            <?php
                            }
                            ?>
                        </td>
                        <td><span id="bbq_discount_text_<?=$i?>"><?=$this->sma->formatMoney(0)?></span><input type="hidden" name="bbq_discount_amount[<?=$i?>]" value="<?=$this->sma->formatDecimal(0)?>" id="bbq_discount_amount_<?=$i?>"></td>
                    </tr>
                  
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
                       $final_val = ($final_val + $default_tax);
                       $final_val = $final_val;
                       $sub_val = $final_val;
                    }
                    else{
                        $sub_val = $final_val/(($default_tax/$final_val)+1);
                        $sub_val =  $sub_val;
                        $default_tax = ($sub_val) * ($getTax->rate / 100);
                        $final_val = $sub_val+$default_tax; 
                        $final_val = $final_val;
                    }
                      
                    ?>
                    <td>
                    <input type="hidden" name="tax_type[<?=$i?>]" id="tax_type_<?=$i?>" value="<?php echo $getTaxType; ?>">
            
                    <select style="display: none"  name="ptax[<?=$i?>]" class="form-control pos-input-tip ptax" id="ptax_<?=$i?>">
                        <?php
                        foreach ($tax_rates as $tax) {
                            
                        ?>
                        <option value="<?php echo $tax->id; ?>" <?php if($getTax->id == $tax->id){ echo 'selected'; }else{ echo ''; } ?> data-id="<?php echo $tax->rate; ?>"><?php echo $tax->name; ?></option>
                        <?php
                        }
                        ?>
                    </select>
                    <span style="text-align: right;"> <?php echo '('.$taxtype.' - '.$getTax->name.')' ?></span></td>
                    <td><span id="tax_text_<?=$i?>"><?php echo $this->sma->formatMoney($default_tax); ?></span><input type="hidden" name="tax_amount[<?=$i?>]" id="tax_amount_<?=$i?>" value="<?php echo $this->sma->formatDecimal($default_tax); ?>"></td>
                  </tr> 
                  
                  <tr>
                        <td colspan="4">Grand Total</td>
                        <td><span id="gtotal_text_<?=$i?>"><?php echo $this->sma->formatMoney($final_val); ?></span><input type="hidden" name="gtotal[<?=$i?>]" id="gtotal_<?=$i?>" value="<?=$this->sma->formatDecimal($final_val)?>"></td>
                        
                  </tr>
                  
              
                                        
                </tbody>
            </table>
            </div>
           <?php
			}
		   ?>
            
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
echo form_hidden('action', 'MANUALSPLITBILL-SUBMIT');
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
                   <!-- <button class="btn btn-block btn-lg btn-primary" id="print-sale"><?=lang('print_only');?></button>-->
        </div>

<?php unset($Settings->setting_id, $Settings->smtp_user, $Settings->smtp_pass, $Settings->smtp_port, $Settings->update, $Settings->reg_ver, $Settings->allow_reg, $Settings->default_email, $Settings->mmode, $Settings->timezone, $Settings->restrict_calendar, $Settings->restrict_user, $Settings->auto_reg, $Settings->reg_notification, $Settings->protocol, $Settings->mailpath, $Settings->smtp_crypto, $Settings->corn, $Settings->customer_group, $Settings->srampos_username, $Settings->purchase_code);?>


<script>
var recipe_variant = 0, shipping = 0, p_page = 0, per_page = 0, pro_limit = <?= $pos_settings->pro_limit; ?>,
brand_id = 0, obrand_id = 0, cat_id = "<?=$pos_settings->default_category?>", ocat_id = "<?=$pos_settings->default_category?>", sub_cat_id = 0, osub_cat_id,
count = 1, an = 1, DT = <?=$Settings->default_tax_rate?>,
recipe_tax = 0, invoice_tax = 0, recipe_discount = 0, order_discount = 0, total_discount = 0, total = 0, total_paid = 0, grand_total = 0,
KB = <?=$pos_settings->keyboard?>, tax_rates =<?php echo json_encode($tax_rates); ?>;
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
		url: '<?=admin_url('pos/bbqbillprint');?>',
		//dataType: "json",
		data: $('#pos-autosplitbill-form').serialize(),
		success: function (data) {
			$obj.text('<?=lang('print_only');?>').attr('disabled', false);
			Popup(data);
		}
});

return false;

});

<?php
for($i=0; $i<$bils; $i++){
?>

$(document).on('change', '#number_of_adult_<?=$i?>', function(){
	var number_of_adult = $('#number_of_adult_<?=$i?>').val();
	var adult_price = $('#adult_price_<?=$i?>').val();
	
	var number_of_child = $('#number_of_child_<?=$i?>').val();
	var child_price = $('#child_price_<?=$i?>').val();
	
	var number_of_kids = $('#number_of_kids_<?=$i?>').val();
	var kids_price = $('#kids_price_<?=$i?>').val();
	
	var adult_buyx = $('#adult_buyx_<?=$i?>').val();
	var adult_getx = $('#adult_getx_<?=$i?>').val();
	var adult_discount = CalculationBBQbuyget(adult_buyx, adult_getx, number_of_adult);
		
	var child_buyx = $('#child_buyx_<?=$i?>').val();
	var child_getx = $('#child_getx_<?=$i?>').val();
	var child_discount = CalculationBBQbuyget(child_buyx, child_getx, number_of_child);
	
	var kids_buyx = $('#kids_buyx_<?=$i?>').val();
	var kids_getx = $('#kids_getx_<?=$i?>').val();
	var kids_discount = CalculationBBQbuyget(kids_buyx, kids_getx, number_of_kids);
	
	var adult_subprice = parseFloat(adult_price * number_of_adult) - parseFloat(adult_price * adult_discount);
	var child_subprice = parseFloat(child_price * number_of_child) - parseFloat(child_price * child_discount);
	var kids_subprice = parseFloat(kids_price * number_of_kids) - parseFloat(kids_price * kids_discount);
	
	var number_of_covers = parseInt(number_of_adult) + parseInt(number_of_child) + parseInt(number_of_kids);
	var total_amount = adult_subprice + child_subprice + kids_subprice;
	
	var ds = $('#bbq_discount_<?=$i?>').val();
	if(ds == ''){
		ds = '0%';
	}else{
		ds = ds;
	}
	var unit_price = adult_subprice + child_subprice + kids_subprice;
	
	var taxtype = $('#tax_type_<?=$i?>').val();
	var pr_tax = $('#ptax_<?=$i?>').children(":selected").data("id");
	var pr_tax_val = 0;
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
	var final_amount = parseFloat(unit_price) - parseFloat(input_discount);
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
        final_amount = parseFloat(final_amount+final_tax);
		
	}else{
		
		var sub_val = final_amount/((pr_tax_val/final_amount)+1);
        final_tax_amount = sub_val * (pr_tax / 100);
        final_amount = sub_val+final_tax_amount; 
	}
	
	if(final_amount >= 0 ){
		
		$('#adult_<?=$i?>').text(adult_discount);
		$('#adult_discount_cover_<?=$i?>').val(adult_discount);
		
		$('#kids_<?=$i?>').text(kids_discount);
		$('#kids_discount_cover_<?=$i?>').val(kids_discount);
		
		$('#child_<?=$i?>').text(child_discount);
		$('#child_discount_cover_<?=$i?>').val(child_discount);
		
		
		
		$('#adult_subprice_text_<?=$i?>').text(formatMoney(adult_subprice));
		$('#adult_subprice_<?=$i?>').val(formatDecimal(adult_subprice));
		
		$('#child_subprice_text_<?=$i?>').text(formatMoney(child_subprice));
		$('#child_subprice_<?=$i?>').val(formatDecimal(child_subprice));
		
		$('#kids_subprice_text_<?=$i?>').text(formatMoney(kids_subprice));
		$('#kids_subprice_<?=$i?>').val(formatDecimal(kids_subprice));
		
		$('#number_of_covers_text_<?=$i?>').text(number_of_covers);
		$('#number_of_covers_<?=$i?>').val(number_of_covers);
		
		$('#total_amount_text_<?=$i?>').text(formatMoney(total_amount));
		$('#total_amount_<?=$i?>').val(formatDecimal(total_amount));
		
		$('#bbq_discount_text_<?=$i?>').text(formatMoney(input_discount));
		$('#bbq_discount_amount_<?=$i?>').val(formatDecimal(input_discount));
		
		$('#tax_text_<?=$i?>').text(formatMoney(final_tax_amount));
		$('#tax_amount_<?=$i?>').val(formatDecimal(final_tax_amount));
		
		$('#gtotal_text_<?=$i?>').text(formatMoney(final_amount));
		$('#gtotal_<?=$i?>').val(formatDecimal(final_amount));
		
	}else{
		bootbox.alert('Discount should not grater than total');
	}
	
});

$(document).on('change', '#number_of_child_<?=$i?>', function(){
	var number_of_adult = $('#number_of_adult_<?=$i?>').val();
	var adult_price = $('#adult_price_<?=$i?>').val();
	
	var number_of_child = $('#number_of_child_<?=$i?>').val();
	var child_price = $('#child_price_<?=$i?>').val();
	
	var number_of_kids = $('#number_of_kids_<?=$i?>').val();
	var kids_price = $('#kids_price_<?=$i?>').val();
	
	var adult_buyx = $('#adult_buyx_<?=$i?>').val();
	var adult_getx = $('#adult_getx_<?=$i?>').val();
	var adult_discount = CalculationBBQbuyget(adult_buyx, adult_getx, number_of_adult);
		
	var child_buyx = $('#child_buyx_<?=$i?>').val();
	var child_getx = $('#child_getx_<?=$i?>').val();
	var child_discount = CalculationBBQbuyget(child_buyx, child_getx, number_of_child);
	
	var kids_buyx = $('#kids_buyx_<?=$i?>').val();
	var kids_getx = $('#kids_getx_<?=$i?>').val();
	var kids_discount = CalculationBBQbuyget(kids_buyx, kids_getx, number_of_kids);
	
	var adult_subprice = parseFloat(adult_price * number_of_adult) - parseFloat(adult_price * adult_discount);
	var child_subprice = parseFloat(child_price * number_of_child) - parseFloat(child_price * child_discount);
	var kids_subprice = parseFloat(kids_price * number_of_kids) - parseFloat(kids_price * kids_discount);
	
	var number_of_covers = parseInt(number_of_adult) + parseInt(number_of_child) + parseInt(number_of_kids);
	var total_amount = adult_subprice + child_subprice + kids_subprice;
	
	var ds = $('#bbq_discount_<?=$i?>').val();
	if(ds == ''){
		ds = '0%';
	}else{
		ds = ds;
	}
	var unit_price = adult_subprice + child_subprice + kids_subprice;
	
	var taxtype = $('#tax_type_<?=$i?>').val();
	var pr_tax = $('#ptax_<?=$i?>').children(":selected").data("id");
	var pr_tax_val = 0;
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
	var final_amount = parseFloat(unit_price) - parseFloat(input_discount);
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
        final_amount = parseFloat(final_amount+final_tax);
		
	}else{
		
		var sub_val = final_amount/((pr_tax_val/final_amount)+1);
        final_tax_amount = sub_val * (pr_tax / 100);
        final_amount = sub_val+final_tax_amount; 
	}
	
	if(final_amount >= 0 ){
		
		$('#adult_<?=$i?>').text(adult_discount);
		$('#adult_discount_cover_<?=$i?>').val(adult_discount);
		
		$('#kids_<?=$i?>').text(kids_discount);
		$('#kids_discount_cover_<?=$i?>').val(kids_discount);
		
		$('#child_<?=$i?>').text(child_discount);
		$('#child_discount_cover_<?=$i?>').val(child_discount);
		
		$('#adult_subprice_text_<?=$i?>').text(formatMoney(adult_subprice));
		$('#adult_subprice_<?=$i?>').val(formatDecimal(adult_subprice));
		
		$('#child_subprice_text_<?=$i?>').text(formatMoney(child_subprice));
		$('#child_subprice_<?=$i?>').val(formatDecimal(child_subprice));
		
		$('#kids_subprice_text_<?=$i?>').text(formatMoney(kids_subprice));
		$('#kids_subprice_<?=$i?>').val(formatDecimal(kids_subprice));
		
		$('#number_of_covers_text_<?=$i?>').text(number_of_covers);
		$('#number_of_covers_<?=$i?>').val(number_of_covers);
		
		$('#total_amount_text_<?=$i?>').text(formatMoney(total_amount));
		$('#total_amount_<?=$i?>').val(formatDecimal(total_amount));
		
		$('#bbq_discount_text_<?=$i?>').text(formatMoney(input_discount));
		$('#bbq_discount_amount_<?=$i?>').val(formatDecimal(input_discount));
		
		$('#tax_text_<?=$i?>').text(formatMoney(final_tax_amount));
		$('#tax_amount_<?=$i?>').val(formatDecimal(final_tax_amount));
		
		$('#gtotal_text_<?=$i?>').text(formatMoney(final_amount));
		$('#gtotal_<?=$i?>').val(formatDecimal(final_amount));
		
	}else{
		bootbox.alert('Discount should not grater than total');
	}
	
});

$(document).on('change', '#number_of_kids_<?=$i?>', function(){
	var number_of_adult = $('#number_of_adult_<?=$i?>').val();
	var adult_price = $('#adult_price_<?=$i?>').val();
	
	var number_of_child = $('#number_of_child_<?=$i?>').val();
	var child_price = $('#child_price_<?=$i?>').val();
	
	var number_of_kids = $('#number_of_kids_<?=$i?>').val();
	var kids_price = $('#kids_price_<?=$i?>').val();
	
	var adult_buyx = $('#adult_buyx_<?=$i?>').val();
	var adult_getx = $('#adult_getx_<?=$i?>').val();
	var adult_discount = CalculationBBQbuyget(adult_buyx, adult_getx, number_of_adult);
		
	var child_buyx = $('#child_buyx_<?=$i?>').val();
	var child_getx = $('#child_getx_<?=$i?>').val();
	var child_discount = CalculationBBQbuyget(child_buyx, child_getx, number_of_child);
	
	var kids_buyx = $('#kids_buyx_<?=$i?>').val();
	var kids_getx = $('#kids_getx_<?=$i?>').val();
	var kids_discount = CalculationBBQbuyget(kids_buyx, kids_getx, number_of_kids);
	
	var adult_subprice = parseFloat(adult_price * number_of_adult) - parseFloat(adult_price * adult_discount);
	var child_subprice = parseFloat(child_price * number_of_child) - parseFloat(child_price * child_discount);
	var kids_subprice = parseFloat(kids_price * number_of_kids) - parseFloat(kids_price * kids_discount);
	
	var number_of_covers = parseInt(number_of_adult) + parseInt(number_of_child) + parseInt(number_of_kids);
	var total_amount = adult_subprice + child_subprice + kids_subprice;
	
	var ds = $('#bbq_discount_<?=$i?>').val();
	if(ds == ''){
		ds = '0%';
	}else{
		ds = ds;
	}
	var unit_price = adult_subprice + child_subprice + kids_subprice;
	
	var taxtype = $('#tax_type_<?=$i?>').val();
	var pr_tax = $('#ptax_<?=$i?>').children(":selected").data("id");
	var pr_tax_val = 0;
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
	var final_amount = parseFloat(unit_price) - parseFloat(input_discount);
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
        final_amount = parseFloat(final_amount+final_tax);
		
	}else{
		
		var sub_val = final_amount/((pr_tax_val/final_amount)+1);
        final_tax_amount = sub_val * (pr_tax / 100);
        final_amount = sub_val+final_tax_amount; 
	}
	
	if(final_amount >= 0 ){
		
		$('#adult_<?=$i?>').text(adult_discount);
		$('#adult_discount_cover_<?=$i?>').val(adult_discount);
		
		$('#kids_<?=$i?>').text(kids_discount);
		$('#kids_discount_cover_<?=$i?>').val(kids_discount);
		
		$('#child_<?=$i?>').text(child_discount);
		$('#child_discount_cover_<?=$i?>').val(child_discount);
		
		$('#adult_subprice_text_<?=$i?>').text(formatMoney(adult_subprice));
		$('#adult_subprice_<?=$i?>').val(formatDecimal(adult_subprice));
		
		$('#child_subprice_text_<?=$i?>').text(formatMoney(child_subprice));
		$('#child_subprice_<?=$i?>').val(formatDecimal(child_subprice));
		
		$('#kids_subprice_text_<?=$i?>').text(formatMoney(kids_subprice));
		$('#kids_subprice_<?=$i?>').val(formatDecimal(kids_subprice));
		
		$('#number_of_covers_text_<?=$i?>').text(number_of_covers);
		$('#number_of_covers_<?=$i?>').val(number_of_covers);
		
		$('#total_amount_text_<?=$i?>').text(formatMoney(total_amount));
		$('#total_amount_<?=$i?>').val(formatDecimal(total_amount));
		
		$('#bbq_discount_text_<?=$i?>').text(formatMoney(input_discount));
		$('#bbq_discount_amount_<?=$i?>').val(formatDecimal(input_discount));
		
		$('#tax_text_<?=$i?>').text(formatMoney(final_tax_amount));
		$('#tax_amount_<?=$i?>').val(formatDecimal(final_tax_amount));
		
		$('#gtotal_text_<?=$i?>').text(formatMoney(final_amount));
		$('#gtotal_<?=$i?>').val(formatDecimal(final_amount));
		
	}else{
		bootbox.alert('Discount should not grater than total');
	}
	
});


$(document).on('change', '#bbq_discount_<?=$i?>', function(){
	var ds = $(this).val();
	var unit_price = $('#total_amount_<?=$i?>').val();
	var taxtype = $('#tax_type_<?=$i?>').val();
	var pr_tax = $('#ptax_<?=$i?>').children(":selected").data("id");
	var pr_tax_val = 0;
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
	var final_amount = parseFloat(unit_price) - parseFloat(input_discount);
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
        final_amount = parseFloat(final_amount+final_tax);
		
	}else{
		
		var sub_val = final_amount/((pr_tax_val/final_amount)+1);
        final_tax_amount = sub_val * (pr_tax / 100);
        final_amount = sub_val+final_tax_amount; 
	}
	
	if(final_amount >= 0 ){
		$('#bbq_discount_text_<?=$i?>').text(formatMoney(input_discount));
		$('#bbq_discount_amount_<?=$i?>').val(formatDecimal(input_discount));
		
		$('#tax_text_<?=$i?>').text(formatMoney(final_tax_amount));
		$('#tax_amount_<?=$i?>').val(formatDecimal(final_tax_amount));
		
		$('#gtotal_text_<?=$i?>').text(formatMoney(final_amount));
		$('#gtotal_<?=$i?>').val(formatDecimal(final_amount));
		
	}else{
		bootbox.alert('Discount should not grater than total');
	}
	
});

<?php
}
?>
function CalculationBBQbuyget(buy, get, cover){
	var paid = 0;
	var quotient;
	if(buy != 0 && get != 0){
		quotient = parseInt(cover / buy);
		paid = (get * quotient);
	}
	return paid; 
}

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
