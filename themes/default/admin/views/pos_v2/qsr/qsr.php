<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<html>
<head>
    <meta charset="utf-8">
    <title><?= "Login " . $Settings->site_name;?></title>
    <script type="text/javascript">if(parent.frames.length !== 0){top.location = '<?=base_url('pos/qsr')?>';}</script>
    <base href="<?=base_url()?>"/>
    <meta http-equiv="cache-control" content="max-age=0"/>
    <meta http-equiv="cache-control" content="no-cache"/>
    <meta http-equiv="expires" content="0"/>
    <meta http-equiv="pragma" content="no-cache"/>
    <link rel="stylesheet" href="<?=$assets?>fonts/barlow_condensed/stylesheet.css" type="text/css">
    <link rel="shortcut icon" href="<?=$assets?>images/favicon.ico"/>
    <link rel="stylesheet" href="<?=$assets?>styles/jquery.mCustomScrollbar.css" type="text/css">
   	<link rel="stylesheet" href="<?=$assets?>styles/helpers/bootstrap.min.css" type="text/css">
   	<link rel="stylesheet" href="<?=$assets?>styles/helpers/font-awesome.min.css" type="text/css">
	<link rel="stylesheet" href="<?=$assets?>styles/theme.css" type="text/css"/>
   	<link rel="stylesheet" href="<?=$assets?>styles/home_frontend.css" type="text/css">
   	<link rel="stylesheet" href="<?=$assets?>styles/responsive_qsr.css" type="text/css">
	<script type="text/javascript" src="<?=$assets?>js/jquery-2.0.3.min.js"></script>
	<script>
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
    <style>
		.table>thead>tr>td.danger, .table>tbody>tr>td.danger, .table>tfoot>tr>td.danger, .table>thead>tr>th.danger, .table>tbody>tr>th.danger, .table>tfoot>tr>th.danger, .table>thead>tr.danger>td, .table>tbody>tr.danger>td, .table>tfoot>tr.danger>td, .table>thead>tr.danger>th, .table>tbody>tr.danger>th, .table>tfoot>tr.danger>th {
    background-color: #4c0a0a;
    padding: 4px 8px;
}

		#item-list .pos-tip	{    z-index: 1;
    position: relative;}
</style>
</head>
<?php
$currency = $this->site->getAllCurrencies();
?>
<body>
	<header class="logo_header">
		<div class="container-fluid">
			<div class="row">
				<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
					<a href="#"><img src="<?=$assets?>images/srampos.png" alt=""></a>
				</div>
				<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
					<h3 class="text-center">Total Payable: 0.00</h3>
				</div>
			</div>
		</div>
	</header>
	<section class="pos_bottom_s">
		<div class="container">
			<div class="row">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-left header_sec_me">
					<ul class="menu_nav">
						<li>
							<button type="button" class="btn center-block" id="payment">
								<div class="img_block">
									<img src="<?=$assets?>images/sprite/payment_y.png">
									<figcaption>Payment</figcaption>
								</div>
							</button>
						</li>
						<li>
							<button type="button" class="btn center-block" id="suspend">
								<div class="img_block">
									<img src="<?=$assets?>images/sprite/new_order_item.png">
									<figcaption>Hold</figcaption>
								</div>
							</button>
						</li>
						<li>
							<button type="button" class="btn center-block" >
							<a id="opened_bills" href="<?php echo base_url('pos/qsr/opened_bills')?>" data-toggle="ajax" tabindex="-1">
								<div class="img_block">
									<img src="<?=$assets?>images/sprite/new_order_item.png">
									<figcaption>Recall</figcaption>
								</div>
								</a>
							</button>
						</li>
						<li>
							<button type="button" class="btn center-block ppdiscount" id="ppdiscount">
								<div class="img_block">
									<img src="<?=$assets?>images/sprite/new_order_item.png">
									<figcaption>Discount</figcaption>
								</div>
							</button>
						</li>
						<li>
							<button type="button" class="btn center-block" id="print_bill">
								<div class="img_block">
									<img src="<?=$assets?>images/sprite/print.png">
									<figcaption>Print</figcaption>
								</div>
							</button>
						</li>
						<li>
							<button type="button" class="btn center-block" id="reset">
								<div class="img_block">
									<img src="<?=$assets?>images/sprite/new_order_item.png">
									<figcaption>Clear</figcaption>
								</div>
							</button>
						</li>
						
						<li>
							<button type="button" class="btn center-block" id="">
								<a  href="<?php echo base_url('pos/qsr/sales_list')?>" style="text-decoration:none;">
								<div class="img_block">
										<img src="<?=$assets?>images/sprite/new_order_item.png">
									<figcaption>More</figcaption>
								</div>
								</a>
							</button>
						</li>
						<li>
							<button type="button" class="btn center-block">
								<a  href="<?php echo base_url('pos/pos/home')?>" style="text-decoration:none;">
								<div class="img_block">
									<img src="<?=$assets?>images/sprite/back.png">
									<figcaption>Back</figcaption>
								</div>
								</a>
							</button>
						</li>
						<li>
							<button type="button" class="btn center-block">
								<div class="img_block">
									<img src="<?=$assets?>images/sprite/exit.png">
									<figcaption>Logout</figcaption>
								</div>
							</button>
						</li>
						
					</ul>
				</div>
			</div>
		</div>
	</section>
	<section>
		<div class="container-fluid">
		       <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'id' => 'pos-sale-form');
                    echo form_open("pos/qsr", $attrib);?>
			<div class="row">
				<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
					<div class="head_left_order">
						<table class="table top_left_order">
						<colgroup>
							<col width="40%">
							<col width="60%">
						</colgroup>
							<tbody>
								
								<tr>
									<td>
										 <div class="form-group">
                                <div class="input-group">
                                <?php
                                $getdefalutcustmer_name = $this->site->getCompanyOrderByID($pos_settings->default_customer);
                                ?>
                                    <select  class="form-control" id="poscustomer" name="customer" required style="width:100%;" >
                          <?php         $customers = $this->site->getAllCompanies('customer');                  if(!empty($customers)){                                                
                                                foreach($customers as $customer){
                                                ?>
                                                    <option <?php if($this->pos_settings->default_customer == $customer->id){ echo 'selected'; }else{ echo ''; } ?>   value="<?php echo $customer->id ?>"><?php echo $customer->name; ?></option>
                                                <?php
                                                }
                                            }
                                            ?>
                                    </select>


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
									</td>
								</tr>
								<tr>
									<td>
									 <div class="form-group" style="display:none;">
                                        <?php
                                            $wh[''] = '';
                                                foreach ($warehouses as $warehouse) {
													if($this->session->userdata('warehouse_id') == $warehouse->id){
                                                    	$wh[$warehouse->id] = $warehouse->name;
													}
                                                }
                                                echo form_dropdown('warehouse', $wh, (isset($_POST['warehouse']) ? $_POST['warehouse'] : $this->session->userdata('warehouse_id')), 'id="poswarehouse" class="form-control pos-input-tip" data-placeholder="' . $this->lang->line("select") . ' ' . $this->lang->line("warehouse") . '" required="required" style="width:100%;" ');
                                            ?>
                                    </div>
										    <?php echo form_input('add_item', '', 'class="form-control pos-tip kb-text-click" id="add_item" data-placement="top" data-trigger="focus" placeholder="' . $this->lang->line("search_product_by_name_code") . '" title="' . $this->lang->line("au_pr_name_tip") . '"'); ?>
									</td>
								</tr>
							</tbody>
						</table>
						<div class="v12" style="display:none;"></div>
						<div class="categorylist_div">
						<table class="table table_middle_s subcategory-list">
							<tbody>
								<tr>
									<td>
										<ul>
											
											  <?php
                            foreach ($categories as $category) {
                                echo "<li><div class='item'><button id=\"category-" . $category->id . "\" type=\"button\" value='" . $category->id . "' class=\"btn btn_default category\" ><span>" . $category->name . "</span></button></div></li>";
                            }
                        ?>

										</ul>

									</td>
								</tr>
							</tbody>
						</table>
						</div>
						
						<table class="table table_bottom_s">
							<tbody>
								<tr>
									<td>
										<ul>
										
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
                                    
                                       
                                         $subhtml = "<li> <div class='item'><button id=\"subcategory-" . $category->id . "\" type=\"button\" value='" . $category->id . "' class=\"btn btn_lightred subcategory slide\" >";
                                         
                                         if(strlen($subcategory_name) < 20){        
                        
                                            $subhtml .= "<span class='name_strong'>" .$subcategory_name. "</span>";
                                        }else{
                                            $subhtml .= "<marquee class='name_strong' behavior='alternate' direction='left' scrollamount='1'>&nbsp;&nbsp;" .$subcategory_name. "&nbsp;&nbsp;</marquee>";
                                        }
                                          $subhtml .=  "</button></div></li>";
                                         
                                         echo $subhtml; 
                                }
                            }
                        ?>
										</ul>

									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
				<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
					<div class="tcb-simple-carousel">
						<div id="myCarousel" class="carousel slide" data-interval="false">
							<div class="carousel-inner">           
								 <div id="item-list">
							 <?php echo $recipe; ?>
							</div>
							</div> 
						</div>
					</div>
					<div class="clearfix"></div>
<!--table-->
		<!--	<div class="" id="table_pay_s">
				<table class="table table_item_ls posTable">
					<thead>
						<tr>
							<th>Name</th>
							<th>Price</th>
							<th>Qty</th>
							<th>Dis %</th>
							<th>Dis $/R</th>
							<th>Amount</th>
							<th>Delect</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>01 Lorem ipsum</td>
							<td>25.00</td>
							<td>
								<div class="number_se">
									<span class="minus">-</span>
									<input type="text" class="numberfocus" value="0" name="no_of_adult" id="no_of_adult">
									<span class="plus">+</span>
								</div>
							</td>
							<td><input type="text" value="0.00" class="form-control"></td>
							<td><input type="text" value="0.00" class="form-control"></td>
							<td><input type="text" value="25.00" class="form-control"></td>
							<td><button type="button" class="btn btn_remove"><i class="fa fa-remove"></i></button></td>
						</tr>
						
					</tbody>
				</table>
			</div>-->
			<div class="" id="item_list_m">
			<table class="table table_item_ls " id="posTable">
				<thead>
					<tr>
						<th>Name</th>
						<th>Price</th>
						<th>Dis %</th>
						<th>Qty</th>
						<!--<th>Dis $/R</th>-->
						<th>Amount</th>
						<th>Delete</th>
					</tr>
				</thead>
				<tbody  >
					
				</tbody>
			</table>
		</div>
			<table class="table table_item_ls table_pay_s_foot">
				<tr>
					<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
					<td>Item :<span id="titems">0</span></td>
					<td>Discount :  <span id="tds">0.00</span></td>
					<td><?php if ($pos_settings->tax_type == 0){
                                  $taxname = 'Inclusive';
					              } else{
                                  $taxname = 'Exclusive';
                                  }
                               $getTax = $this->site->getTaxRateByID($this->pos_settings->default_tax);
                               ?>
                     <?=lang("Tax ".'('.$taxname.' '.$getTax->name.')');?> :   <span id="ttax2">0.00</span></td>
					 <td>Total : <span id="total">0.00</span>  <input type="hidden" name="sub_total" id="sub_total" value="0"></td>
					<input type="hidden" name="biller" id="biller" value="<?= ($Owner || $Admin || !$this->session->userdata('biller_id')) ? $pos_settings->default_biller : $this->session->userdata('biller_id')?>"/>
					<input type="hidden" name="suspend_id" value="0">
					  <span id="hidesuspend"></span>    
					<input type="hidden" name="due_amount" class="due_amount" value=""/>
					<input type="hidden" name="balance_final_amount" class="balance_amount" value=""/>
					<input type="hidden" name="total"  class="total" />                                

					<input name="order_tax" type="hidden" value="<?=$suspend_sale ? $suspend_sale->order_tax_id : ($old_sale ? $old_sale->order_tax_id : $Settings->default_tax_rate2);?>" id="postax2">
					<input name="discount" type="hidden" value="<?=$suspend_sale ? $suspend_sale->order_discount_id : ($old_sale ? $old_sale->order_discount_id : '');?>" id="posdiscount">
					<input name="item_discount" type="hidden" value="" id="item_discount">
					<input name="discount_on_total" type="hidden" value="" id="discount_on_total">
					<input name="shipping" type="hidden" value="<?=$suspend_sale ? $suspend_sale->shipping : ($old_sale ? $old_sale->shipping :  '0');?>" id="posshipping">
					<input type="hidden" name="rpaidby" id="rpaidby" value="cash" style="display: none;"/>
					<input type="hidden" name="total_items" id="total_items" value="0" style="display: none;"/>
				</tr>
				<tr>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td>Total Payable :  <span id="gtotal">0.00</span></td>
				</tr>
			</table>
				</div>
			</div>
    	</div>
		
		
		
		
<div class="modal fade in" id="dsModal" tabindex="-1" role="dialog" aria-labelledby="dsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" id="reset2" class="close" data-dismiss="modal" aria-hidden="true" onClick="window.location.reload();" >
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
                    <div for="dcode" class="form-group">
                        <?=lang("order_discount", "order_discount_input");?>
                        <?php echo form_input('order_discount_input', '0', 'class="form-control kb-pad order_discount_input dcode" id="order_discount_input"'); ?>
                    </div>
               <?php endif; ?>
            </div>

            <div class="modal-footer reset2">
                <button type="button"  id="updateOrderDiscount" class="btn btn-primary " ><?=lang('update')?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade in" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="payModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true"><i
                class="fa fa-2x">&times;</i></span><span class="sr-only"><?=lang('close');?></span></button>
                <h4 class="modal-title" id="payment-customer-name"></h4>
                <h4 class="modal-title" id="payModalLabel"><?=lang('make_payment');?></h4>
            </div>

            <div class="modal-body" id="payment_content">
                <div class="row">
                    <?php if ($this->pos_settings->taxation_report_settings == 1) { ?>
                       <div class="form-group" style="padding: 5px 10px;">
                            <div class="row">
                                <div class="col-sm-6 taxation_settings">
                                    <label class="control-label" for="taxation_settings"><?= lang("taxation"); ?></label>                    
                                    <input type="radio" value="0" class="checkbox" name="taxation" checked ="checked">
                                    <label for="switch_left">INCLUDE</label>
                                    <input type="radio" value="1" class="checkbox" name="taxation">
                                    <label for="switch_right">EXCLUDE</label>                    
                                </div>
                            </div>
                        </div>
                    <?php  } ?>

                <input type="hidden" name="type" class="type" value="<?php echo $type;?>"/>
                <input type="hidden" name="balance_amount" class="balance_amount" value=""/>
                    <div class="col-md-12 col-sm-12 text-center">
                        <?php /*if ($Owner || $Admin || !$this->session->userdata('biller_id')) { ?>
                            <div class="form-group"  style="margin-bottom: 5px;">
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
                            }*/
                        ?>

                        <input type="hidden" name="credit_limit" id="credit_limit" class="credit_limit" />
                        <input type="hidden" name="company_id" id="company_id" class="company_id" />
                        <input type="hidden" name="customer_type" id="customer_type" class="customer_type" />
                        <input type="hidden" name="customer_id" id="customer_id" class="customer_id"/>
                        <input type="hidden" name="total" id="total" class="total" />
                        <input type="hidden" name="loyaltypoints" id="loyaltypoints" class="loyaltypoints" />
                        <input type="hidden" name="loyalty_used_points" id="loyalty_used_points" class="loyalty_used_points" />

                        <div class="form-group bill_sec_head" style="color: #1F73BB!important;align-self: center;margin-bottom: 5px;">
                            <button type="button" class="btn btn-warning" id="pay_reset" style="cursor: pointer!important;    ">Reset</button>
                               <label><?=lang("bill_amount", "bill_amount");?></label>
                               <?php 
                               $default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
                               ?>
                              <span id="bill_amount" ></span>
                        </div>

                        <div id="payment-list">
                           <?php   
                           $j =0;
                            $paymentMethods = $this->site->getAllPaymentMethods();                                                         
                                foreach ($paymentMethods as $k => $method) { 
                                    $j++;
                                      echo "<button id=\"payment-" . $method->payment_type . "\" type=\"button\" value='" . $method->payment_type . "' class=\"btn-prni payment_type\" data-index='" . $method->payment_type. "' data_id='" . $j. "' ><span>" . $method->display_name . "</span></button>";
                                ?>
                                     <input name="paid_by[]" type="hidden" id="payment_type_<?php echo $method->payment_type; ?>" value="<?php echo $method->payment_type; ?>" class="form-control" autocomplete="off"  />
                            <?php } ?>
                            <div id="sub_items" style="margin-top: 30px;min-height: 165px;">
                            <?php
                             $j =0;
                            $paymentMethods = $this->site->getAllPaymentMethods(); 
                            $display = "block";

                            foreach ($paymentMethods as $key => $method) {   $j++; 
                                if($method->payment_type =='cash'){
                                    $display = "block";
                                }else{
                                    $display = "none";
                                }
                                ?>
                                
                                   <div class="<?=$method->payment_type?>">
                                    <!-- <span style="color: green;font-size: 20px;"><?=$method->payment_type; ?></span> -->
                                    <?php
                                    foreach($currency as $currency_row){
                                        
                                        $default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
                                        
                                        if($currency_row->code == $default_currency_data->code){
                                    ?>                                    
                                    <div class="col-sm-6 base_currency_<?=$method->payment_type.$j?>" id="base_currency_<?=$method->payment_type?>" style="display:<?php echo $display;?>; "> 
                                        <div class="form-group"  style="margin-bottom: 5px;">
                                            <label><?=lang('amount')?> <?=$currency_row->code; ?></label>
                                            <input name="amount_<?php echo $default_currency_data->code; ?>[]" type="text" id="amount_<?php echo $default_currency_data->code; ?>_<?=$method->payment_type?>" data-rate="<?=$currency_row->rate; ?>" data-code="<?=$currency_row->code; ?>" data-id="<?=$currency_row->id; ?>"  class="pa form-control  amount kb-pad amount_base" payment-type="<?=$method->payment_type?>"  autocomplete="off"  />
                                        </div>
                                    </div>
                                    <?php }else { ?>
                                    <div class="col-sm-6 multi_currency_<?=$method->payment_type.$j?>" id="multi_currency_<?=$method->payment_type?>" style="display:<?php echo $display;?>; ">
                                        <div class="form-group" >
                                             <label><?=lang('amount')?> <?=$currency_row->code; ?></label>
                                            <input name="amount_<?=$currency_row->code; ?>[]" type="text" id="amount_<?=$currency_row->code; ?>_<?=$method->payment_type?>" data-rate="<?=$currency_row->rate; ?>" data-code="<?=$currency_row->code; ?>" data-id="<?=$currency_row->id; ?>"  class="pa form-control  amount kb-pad amount_base" payment-type="<?=$method->payment_type?>"  autocomplete="off"/>
                                        </div>
                                    </div>
                                    <?php }   } ?>  

                                    <div class="clearfix"></div>                                    
									   <div class="form-group lc_<?=$method->payment_type.$j?>" id="lc_<?=$method->payment_type?>" style="display: none">    
										   <div class="form-group col-sm-6">
												<label><?=lang('Points')?></label>
												 <input name="paying_loyalty_points[]" type="text" id="<?=$method->payment_type?>" class="pa form-control loyalty_points"  autocomplete="off" />
											</div> 

											<div class="clearfix"></div>       
											<div id="lc_details_<?=$method->payment_type?>" style="color: red;"> </div>
											<div id="lc_reduem_<?=$method->payment_type?>" style="color: green;"></div>

										</div>
                                    <div class="clearfix"></div>
                                    <div class="CC_<?=$method->payment_type.$j?>" id="CC_<?=$method->payment_type?>" style="display: none">
                                         <div class="form-group col-sm-6">
											<label><?=lang('card_no')?> </label>
										   	<input name="cc_no[]" type="text" id="pcc_no_<?=$method->payment_type?>"
                                                    class="form-control kb-pad" placeholder="<?=lang('cc_no')?>"/>
                                        </div> 
                                        <div class="form-group col-sm-6">
                                            <label><?=lang('card_exp_date')?> </label>
										   	<input name="card_exp_date[]" type="text" id="card_exp_date_<?=$method->payment_type?>"
                                                    class="form-control kb-pad" placeholder="<?=lang('card_exp_date')?>"/>
                                        </div> 
                                        <div class="clearfix"></div>                                        
                                    </div>  
                                </div>                                  
                                <?php  } ?> 
                                </div>
                        </div>  
                                <div class="form-group" style="margin-bottom: 5px;">
                                    <div id="userd_tender_list">         
                                    </div>
                                </div>
                                <div class="clearfix"></div> 
                                <div class="col-md-12 col-sm-12">
                                <div class="form-group total_pay"  style="margin-bottom: 5px;">
                                    <table class="table table-condensed table-striped" style="margin-bottom: 0;">
                                        <tr>
                                            <td rowspan="4" class="total_paytd" style="width: 35%!important;">
                                                &nbsp;<?=lang('total_pay')?>
                                            </td> 
                                        </tr>
                                         <?php
                                            foreach($currency as $currency_row) { ?>
                                               <tr><td class="total_paytd"><?php echo $currency_row->code; ?>&nbsp;:&nbsp;&nbsp;<span id="twt_<?php echo $currency_row->code; ?>">0.00</span>
                                                <input type="hidden" id="paid_amt_<?php echo $currency_row->code; ?>"></td></tr>
                                         <?php } ?>
                                    </table>
                                </div>
                            </div>

                            <div class="clearfix"></div> 
                            <div class="col-md-12 col-sm-12">   
                                <div class="form-group balance_pay"  style="margin-bottom: 5px;">
                                    <table class="table table-condensed table-striped" style="margin-bottom: 0;">
                                        <tr>
                                            <td rowspan="4" class="balance_paytd" style="width: 35%!important;">&nbsp;<?=lang('balance_pay')?></td> 
                                        </tr>
                                         <?php
                                            foreach($currency as $currency_row) { ?>
                                               <tr><td class="balance_paytd"><?php echo $currency_row->code; ?>&nbsp;:&nbsp;&nbsp;<span id="balance_<?php echo $currency_row->code; ?>">0.00</span>
                                                   <input type="hidden" id="balance_amt_<?php echo $currency_row->code; ?>"></td></tr>
                                            <?php } ?>
                                    </table>
                                </div>  
                            </div>                                  
                            <div id="payments" class="payment-row" style="display: none">
                                <div class="well well-sm well_1" style="padding: 5px 10px;">
                                    <div class="payment">
                                        <div class="row">                        
                                        </div>
                                    </div>
                                </div>
                            </div>    
             
            <div class="modal-footer">
                <button class="btn btn-block btn-lg btn btn-info" id="submit-sale"><?=lang('send');?></button>
            </div>
        </div>
    </div>
		</div>
		</div>
		</div>
		
		
		<?php echo form_close(); ?>
		
		
		
		
		
		<div id="order_tbl" style="display:none;"><span id="order_span"></span>

    <table id="order-table" class="prT table table-striped" style="margin-bottom:0;" width="100%"></table>
</div>
<?php 
         $biller_id = $this->session->userdata('biller_id') ? $this->session->userdata('biller_id') : $pos_settings->default_biller ;         
		 $biller = $this->site->getCompanyOrderByID($biller_id); 

?>
<div id="bill_print" style="display: none">
	<div class="text-center">
                    <?= !empty($biller->logo) ? '<img src="'.base_url('assets/uploads/logos/'.$biller->logo).'" alt="">' : ''; ?>
                    <h3 style="text-transform:uppercase;"><?=$biller->company != '-' ? $biller->company : $biller->name;?></h3>
                    <?php
                    echo "<p>" . $biller->address . " " . $biller->city . " " . $biller->postal_code . " " . $biller->state . " " . $biller->country .
                    "<br>" . lang("tel") . ": " . $biller->phone;

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
                    echo '</p>';
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
                //echo "<span style='font-size:15px;font-weight:bold'>" .lang("bill_no") . ": " . $inv->bill_number . "</span>";
                 if($this->Settings->time_format == 12){
                    $date = new DateTime($inv->created_on);
                    $created_on = $date->format('Y-m-d h:iA');
                    }else{
                        $created_on =  $inv->created_on;
                } echo  "<br>";
                echo lang("date") . ": " . $created_on . "<br>";
                if($pos_settings->order_no_display == 1){
                    echo lang("sale_no_ref") . ": " . $inv->reference_no . "<br>";
                }
                if (!empty($inv->return_sale_ref)) {
                    echo '<p>'.lang("return_ref").': '.$inv->return_sale_ref;
                    if ($inv->return_id) {
                        echo ' <a data-target="#myModal2" data-toggle="modal" href="'.admin_url('sales/modal_view/'.$inv->return_id).'"><i class="fa fa-external-link no-print"></i></a><br>';
                    } else {
                        echo '</p>';
                    }
                }
                echo lang("sales_person") . ": " . $created_by." ".$created_by. "</p>";
                echo "<p>";
                echo lang("customer") . ": " . ($customer->company && $customer->company != '-' ? $customer->company : $customer->name) . "<br>";
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
		
        <table id="bill-table_head" width="100%" class="table table-striped" style="margin-bottom:0;"></table> 
        <table id="bill-table" width="100%" class=" table table-striped" style="margin-bottom:0;"></table>
        <table id="bill-total-table" class="prT table" style="margin-bottom:0;" width="100%"></table>
        <!-- <span id="bill_footer"></span> -->
    </div>
</div>
<div class="modal fade in" id="susModal" tabindex="-1" role="dialog" aria-labelledby="susModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" onClick="window.location.reload();"><i
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
 <div id="bill_tbl" style="display:none;"><span id="bill_span"></span>
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
		
		
		
	</section>
		<script type="text/javascript" src="<?=$assets?>js/jquery-2.0.3.min.js"></script>
	    <script type="text/javascript" src="<?=$assets?>js/jquery.mCustomScrollbar.concat.min.js"></script>
<script>
 
	
<?php unset($Settings->setting_id, $Settings->smtp_user, $Settings->smtp_pass, $Settings->smtp_port, $Settings->update, $Settings->reg_ver, $Settings->allow_reg, $Settings->default_email, $Settings->mmode, $Settings->timezone, $Settings->restrict_calendar, $Settings->restrict_user, $Settings->auto_reg, $Settings->reg_notification, $Settings->protocol, $Settings->mailpath, $Settings->smtp_crypto, $Settings->corn, $Settings->customer_group, $Settings->envato_username, $Settings->purchase_code);?>

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
<!-- new payment screen  start -->
<script type="text/javascript">
    $('#paymentModal').on('shown.bs.modal', function(e) {
        // $("button.payment_type").val("cash").click();        
        $('#customer_id').val($("#poscustomer").val());        
        $('#bill_amount').text(formatMoney($('#gtotal').text()));

            var customer_id = $('.customer_id').val();              
            if (customer_id != '') {
                $.ajax({
                    type: "get", async: false,
                    url: site.base_url + "pos/getCheckLoyaltyAvailable/" + customer_id,
                    dataType: "json",
                    success: function (data) {                            
                        if (data === 0) {
                            $('#payment-loyalty').prop('disabled', true).css('opacity',0.5);
                        } else {
                            $('#payment-loyalty').prop('disabled', false);                        
                        } 
                    }
                });
            } 

        $('#payment-cash').val('cash');    
        if($('#payment-cash').val() == 'cash'){
            $('#payment-cash').trigger('click');                  
            $('#payment-cash').addClass('active');   
        } 

        <?php
            foreach($currency as $currency_row){
                $default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
                if($currency_row->code == $default_currency_data->code){  
                ?>                
                <?php
                }else{                
                ?>
                // $('#amount_<?php echo $currency_row->code; ?>_1').val('');
                <?php
                }
            } ?>
        });
$(document).on('click', '#pay_reset', function () {    
      $('#userd_tender_list').html('');
      $(".amount").val('');
      calculateTotals();
});

        $(document).on('click', '.payment_type', function () {    
            // $('#clear-cash-notes').click();
                $index = $( this ).attr('data-index');                
                $data_id = $( this ).attr('data_id');                
                <?php
                foreach($currency as $currency_row){
                    $default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
                    if($currency_row->code == $default_currency_data->code){
                    ?>
                    // $('#amount_<?php echo $currency_row->code; ?>_'+$index).val('');
                    $('#amount_<?php echo $currency_row->code; ?>_'+$index).attr('readonly', false);
                    <?php
                    }else{
                    ?>
                    // $('#amount_<?php echo $currency_row->code; ?>_'+$index).val('');
                    $('#amount_<?php echo $currency_row->code; ?>_'+$index).attr('readonly', false);            
                    <?php
                    }
                } 
               ?>  
                var p_val = $(this).val(),
                id = $(this).attr('id'),
                pa_no = id.substr(id.length - 1);            
                $('#rpaidby').val(p_val);
            if (p_val == 'cash') {    
            
                $('.payment_type.active').removeClass('active');
                $('#payment-cash').addClass('active');                  
                 $('#amount_<?php echo $currency_row->code; ?>_'+$index).attr('readonly', false);  
                 $('.cash').show();               
                 $('#base_currency_'+ $index).show();   
                 $('#multi_currency_'+ $index).show();
                 $('#lc_'+ $index).hide();                 
                 $('#CC_'+ $index).hide(); 
                 $('.credit').hide();   
                 $('.loyalty').hide();  
                 $('.CC').hide();    

                <?php
                foreach($currency as $currency_row){
                $default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
                if($currency_row->code == $default_currency_data->code){
                ?>
                $('#amount_<?php echo $currency_row->code ?>_'+$index).focus();
                <?php
                }else{ ?>
                // $('.amount_<?php echo $currency_row->code ?>_'+$index).val('');
                <?php } }
                ?>     
            } else if (p_val == 'credit') {       
                $('.payment_type.active').removeClass('active');
                $('#payment-'+$index).addClass('active');                  
                $('#amount_<?php echo $currency_row->code; ?>_'+$index).attr('readonly', false);                   
                 $('.credit').show();    
                 $('#base_currency_'+ $index).show();           
                 $('#multi_currency_'+ $index).hide();
                 $('#lc_'+ $index).hide();                 
                 $('#CC_'+ $index).hide();
                 $('.CC').hide();    
                 $('.loyalty').hide(); 
                 $('.cash').hide(); 
                <?php
                foreach($currency as $currency_row){
                $default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
                if($currency_row->code == $default_currency_data->code){
                ?>
                $('#amount_<?php echo $currency_row->code ?>_' + $index).focus();
                <?php
                }else{
                ?>
                // $('.amount_<?php echo $currency_row->code ?>_'+pa_no).val('');
                
                <?php
                }
                }
                ?>                              
            } else if (p_val == 'CC') {  

                $('.payment_type.active').removeClass('active');
                $('#payment-'+$index).addClass('active');

                 $('.CC').show();     
                 $('#CC_'+ $index).show();     
                 $('#base_currency_'+ $index).show();                      
                 $('#multi_currency_'+ $index).hide();
                 $('#lc_'+ $index).hide();
                 $('.credit').hide(); 
                 $('.loyalty').hide(); 
                 $('.cash').hide(); 

                <?php
                    foreach($currency as $currency_row){
                        $default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
                        if($currency_row->code == $default_currency_data->code){
                        ?>
                        $('#amount_<?php echo $currency_row->code ?>_' + $index).focus();
                        <?php
                        }else{
                        ?>
                        // $('.amount_<?php echo $currency_row->code ?>_'+pa_no).val('');                        
                        <?php
                        }
                    }
                ?>
            } else if (p_val == 'loyalty') {

                $('.payment_type.active').removeClass('active');
                $('#payment-'+$index).addClass('active');

                 $('.loyalty').show();               
                 $('#lc_'+ $index).show();               
                 $('#base_currency_'+ $index).show();
                 $('#multi_currency_'+ $index).hide();
                 $('#CC_'+ $index).hide();                 
                 $('.credit').hide(); 
                 $('.CC').hide(); 
                 $('.cash').hide(); 

                // $('#amount_<?php echo $currency_row->code; ?>_'+$index).focus();
                $('#amount_<?php echo $currency_row->code; ?>_'+$index).attr('readonly', true);
                $('#loyalty_points_' + $index).focus();
                $('#loyaltypoints').val(0);

                var customer_id = $('.customer_id').val();  
                var payid = $(this).attr('id'),
                    id = payid.substr(payid.length - 1);
                if (customer_id != '') {
                    $.ajax({
                        type: "get", async: false,
                        url: site.base_url + "pos/get_loyalty_points/" + customer_id,
                        dataType: "json",
                        success: function (data) {                            
                            if (data === false) {
                                $('#loyalty_points_' + $index).parent('.form-group').addClass('has-error');
                                bootbox.alert('Loyalty card number is incorrect or expired.');
                            } else if (data.customer_id !== null && data.customer_id !== $('#poscustomer').val()) {
                                $('#loyalty_points_' + $index).parent('.form-group').addClass('has-error');
                                bootbox.alert('Loyalty card number is not for this customer.');
                            } else {

                                $('#loyaltypoints').val(data.total_points);
                                $('#lc_details_' + $index).html('<small>Card No: ' + data.loyalty_card_no + '&nbsp;&nbsp;Value: ' + data.total_points +'</small>');                                                               
                                $('#loyalty_points_' + $index).parent('.form-group').removeClass('has-error');                                 
                            }
                        }
                    });
                }               
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
           $(this).parents('.payment').find('input').val('');
            $('#pcc_month_'+$index).prepend('<option value="">Month</option>');
            $('#pcc_year_'+$index).prepend('<option value="">Year</option>');
            $('#pcc_month_'+$index).val('');
            $('#pcc_year_'+$index).val('');            
            $amount = $('#balance_amt_<?=$default_currency_data->code?>').val();          
            $amount = parseFloat($amount.match(/([0-9]*\.[0-9]+|[0-9]+)/));
            
            $('#amount_<?=$default_currency_data->code?>_'+$index).removeClass('credit-max');
            $(this).parent('.form-group').find('.available-c-limit').remove();
            if ($( this ).val()=='credit') {                
                $('#amount_<?=$default_currency_data->code?>_'+$index).addClass('credit-max');
                $inputCredit = 0;
                $('.credit-max').each(function(n,v){
                if($(this).attr('id')!="amount_<?=$default_currency_data->code?>_"+$index){
                    $inputCredit += ($(this).val()=='')?0:parseFloat($(this).val());
                }
                })
                $creditlimit = parseFloat($('#credit_limit').val())-parseFloat($inputCredit);                
             if ($('#customer_type').val()=='none'){
                bootbox.alert("Not allowed to use Credit option");
                $(this).parent('.form-group > .available-c-limit').empty();
                return false;
            }

                if($('#customer_type').val()=='prepaid' && $amount>$creditlimit){
                $amount = $creditlimit;
                }
                $amount = ($amount!=0)?$amount:'';
                $(this).parent('.form-group').append('<span class="available-c-limit">Available Customer Credit Limit $'+$('#credit_limit').val()+'</span>')
            }
            console.log($amount);

                if ((p_val != 'loyalty') && (p_val == 'cash')) {                    
                  if($amount>0){                    
                     if($('#amount_<?=$default_currency_data->code?>_cash').val() == ''){
                        $('#amount_<?=$default_currency_data->code?>_cash').val($amount);
                      }
                    }                                      
                }else if ((p_val != 'loyalty') && (p_val == 'CC')) {                    
                  if($amount>0){                    
                     if($('#amount_<?=$default_currency_data->code?>_CC').val() == ''){
                        $('#amount_<?=$default_currency_data->code?>_CC').val($amount);
                      }
                    }
                }else{
                    $('#loyalty_points_cash').focus();
                } 

                /*if ((p_val != 'loyalty') && (p_val == 'cash')) {                    
                  if($amount>0){
                    if($('#amount_USD_cash').val() == ''){
                        $('#amount_USD_cash').val($amount);
                     }
                };                                      
                }else{
                    $('#loyalty_points_cash').focus();
                }*/
                $('.amount').trigger('blur');
        });
            $(document).on('change', '.loyalty_points', function () {
            var loyaltypoints = $("#loyaltypoints").val(); 
            var redemption = $(this).val() ? $(this).val() : 0;
            var customer_id = $("#customer_id").val();    
            $('#loyalty_used_points').val(0);             
            var payid = $(this).attr('id'); 

            if(parseFloat(loyaltypoints) == 0){    
                 bootbox.alert('Loyalty card number is incorrect or expired.');    
                 $('#loyalty_points_' + id).focus().val('');            
             }else if (parseFloat(redemption) <= parseFloat(loyaltypoints)) {
                $bal_amount = $('#balance_amt_<?=$default_currency_data->code?>').val();
                 $bal_amount = parseFloat($bal_amount.match(/([0-9]*\.[0-9]+|[0-9]+)/));
                    $.ajax({
                        type: "get", async: false,
                        url: site.base_url + "pos/validate_loyalty_card/",
                        dataType: "json",
                         data: {
                            redemption: redemption,                        
                            customer_id: customer_id,
                            bal_amount: $bal_amount,
                        }, 
                        success: function (data) {
                            if (data === false) {                                
                                 bootbox.alert('Right Now Not Eligible to use this card number,Please try after some visit.');
                                 $('#loyalty_points_' + payid).focus().val('');                                  
                                 $('#amount_<?php echo $currency_row->code; ?>_'+payid).val('');
                            } else if (data.customer_id !== null && data.customer_id !== $('#poscustomer').val()) {
                                 bootbox.alert('Loyalty card number is not for this customer.');
                            } else if(parseFloat(data.total_redemamount) > parseFloat($bal_amount)) {
                                    bootbox.alert('Already seleted in other payment method Plz check it (OR) use only Blance amount only.');
                                    $('#lc_reduem_' + payid).html('<small>Redemption: ' + parseFloat(data.redemption) + '&nbsp;&nbsp;Amount: ' + parseFloat(data.amount) +'</small>');
                                    $('#loyalty_points_' + payid).focus().val('');
                                    $('#amount_<?php echo $currency_row->code; ?>_'+payid).val('');
                               }else{                                
                                    // $('#loyalty_points_' + id).parent('.form-group').removeClass('has-error');
                                    $('#lc_reduem_' + payid).html('<small>Redemption: ' + parseFloat(data.redemption) + '&nbsp;&nbsp;Amount: ' + parseFloat(data.amount) +'</small>'); 
                                    $('#loyalty_used_points').val(redemption);
                                    // alert('#amount_<?php echo $currency_row->code; ?>_'+payid);                                     
                                    $('#amount_<?php echo $currency_row->code; ?>_'+payid).focus().val(data.total_redemamount);
                                    $('#amount_<?php echo $currency_row->code; ?>_'+payid).attr('readonly', true);
                              }
                        }
                    });
                }else{
                    
                    bootbox.alert('Please Enter less than your points or equal.');  
                     $('#loyalty_points_' + id).focus().val('');
                     $('#amount_<?php echo $currency_row->code; ?>_'+id).val('');
                    
                }           
        });        
</script>
<!-- new payment screen end  -->

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
        //$('#item-list').css("height", wh - 360);
       // $('#item-list').css("min-height", 205);
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
	 if (localStorage.getItem('input_discount')) {
            localStorage.removeItem('input_discount');
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
        $('#poscustomer12').val(localStorage.getItem('poscustomer')).select2({
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
            $('#poscustomer12').on('select2-opening', function () {
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

            $('#poscustomer12').on('select2-close', function () {
                $('.select2-input').removeClass('kb-text');
                $('#test').click();
                $('select, .select').select2('destroy');
                $('select, .select').select2({minimumResultsForSearch: 7});
            });
            $(document).bind('click', '#test', function () {
                var kb = $('#test').keyboard().getkeyboard();
              //  kb.close();
            });

        }
        $(document).on('click', '#s2id_autogen1_search', function () {
            alert();
            $('.select2-input').addClass('kb-text');
        });

        $(document).on('change', '#posbiller', function () {
            var sb = $(this).val();
            $.each(billers, function () {
                if(this.id == sb) {
                    biller = this;
                }
            });
            $('#biller').val(sb);
        });
        
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
            var twt = formatDecimal((total) - recipe_discount-order_discount-offer_total_discount + shipping);
	    if (pos_settings.tax_type == 1) {
		twt +=invoice_tax;
		}
		
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
              $('#twt_<?php echo $currency_row->code; ?>').text('<?php echo $currency_row->symbol; ?>'+formatDecimal(gtotal_<?php echo $currency_row->code; ?>));
            <?php
            }
            ?>
             
            <?php
            }
            ?>
            $('#item_count').text(count);
            $('#paymentModal').modal('show');
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

        $(document).on('focus', '.amount', function () {
            pi = $(this).attr('id');
            calculateTotals();
        }).on('blur', '.amount', function () {
             var inputs = $(".amount_base");
             var arr = $('input[name="pname[]"]').map(function () {
                return this.value; 
            }).get();
            var paid_tenders = '';
                for(var i = 0; i < inputs.length; i++){  
                    if(($.inArray($(inputs[i]).attr('payment-type'),arr)) !== -1){                        
                        $('#userd_tender_'+$(inputs[i]).attr('payment-type')).text($(inputs[i]).attr('payment-type')+' - '+$(inputs[i]).val());
                    }else if($(inputs[i]).val() != 0 && ($.inArray($(inputs[i]).attr('payment-type'),arr)) === -1){                        
                     paid_tenders += '<div type="button" class="btn-prni payment_type" data-index="cash" data_id="1"><input type="hidden"  name="pname[]" value="'+$(inputs[i]).attr('payment-type')+'"><span style="padding-top:5px" id="userd_tender_'+$(inputs[i]).attr('payment-type')+'">'+$(inputs[i]).attr('payment-type')+' - '+$(inputs[i]).val()+'</span></div>';
                    } else if($(inputs[i]).val() === 0){      
                           $('#userd_tender_'+$(inputs[i]).attr('payment-type')).remove();
                    }
            }
            $('#userd_tender_list').append(paid_tenders);             
            calculateTotals();
        });

        function calculateTotals() {
           
            var value_amount = 0;
            var total_paying = 0;
            var ia = $(".amount");
            
            $.each(ia, function (i) {               
                
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
            $('#balance_<?php echo $currency_row->code; ?>').text(formatMoney(total_paying - (gtotal_<?php echo $currency_row->code; ?> * <?php echo $currency_row->rate; ?>) ,'<?php echo $currency_row->symbol; ?>'));

            $('.balance_amount').val(formatDecimal(total_paying - (gtotal_<?php echo $currency_row->code; ?> * <?php echo $currency_row->rate; ?>))); 

            $('#balance_amt_<?php echo $currency_row->code; ?>').val(formatDecimal(total_paying - (gtotal_<?php echo $currency_row->code; ?> * <?php echo $currency_row->rate; ?>) ),'<?php echo $currency_row->symbol; ?>');

            $('.balance_amount').val(formatDecimal(total_paying - (gtotal_<?php echo $currency_row->code; ?> * <?php echo $currency_row->rate; ?>)));
            <?php
            }else{
            ?>
            
            $('#balance_<?php echo $currency_row->code; ?>').text(formatMoney((total_paying - (gtotal_<?php echo $currency_row->code; ?> * <?php echo $currency_row->rate; ?>) ) / <?php echo $currency_row->rate; ?>,'<?php echo $currency_row->symbol; ?>'));
            
            <?php
            }
            
            if($currency_row->code == $default_currency_data->code){
                ?>
                var balance_usd_total_amount = Math.abs((total_paying -  gtotal_<?php echo $currency_row->code; ?> * <?php echo $currency_row->rate; ?>));
                var balance_usd_remaing_float = balance_usd_total_amount.toString().split(".")[1];
                //var balance_usd_remaing_float = Math.abs((balance_usd_total_amount - Math.round(balance_usd_total_amount)) );
                
                var balance_usd_remaing_float = parseFloat('0.'+balance_usd_remaing_float) / parseFloat(0.00025);
                var balance_USD_KHR = parseFloat(balance_usd_remaing_float);
                $('#balance_<?=$default_currency_data->code?>_KHR').text(formatMoney(balance_USD_KHR));
                
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
                        customer_id: $("#poscustomer").val(),
                        recipe_standard: 1
                    },
                    success: function (data) {
                        $(this).removeClass('ui-autocomplete-loading');
                        response(data);
                    }
                });
            },
            minLength: 1,
            autoFocus: true,
            delay: 250,
            response: function (event, ui) {
                if ($(this).val().length >= 16 && ui.content[0].id == 0) {
                    bootbox.alert('<?=lang('no_match_found')?>', function () {
                        $('#add_item').focus();
                    });
                    $(this).val('');
                }
              /*  else if (ui.content.length == 1 && ui.content[0].id != 0) {
                    ui.item = ui.content[0];
                    $(this).data('ui-autocomplete')._trigger('select', 'autocompleteselect', ui);
                    $(this).autocomplete('close');
                }*/
                else if (ui.content.length == 1 && ui.content[0].id == 0) {
                    bootbox.alert('<?=lang('no_match_found')?>', function () {
                        $('#add_item').focus();
                    });
                    $(this).val('');

                }
            },
	    focus: function( event, ui ) {
		event.preventDefault();
		if (item_scanned) {
		    $('.ui-menu-item:eq(0)').trigger('click');
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
		$('#add_item').val('');
            }
        });

		$(document).bind( "change.autocomplete", '#add_item', function( event ) {				
				var add_item = $('#add_item').val();
				 $("#add_item").autocomplete('search', add_item);
                 // $("#add_item").val('');
				
			});

        $(document).bind( "keyup.autocomplete", '#add_item', function( event ) {               
                var add_item = $('#add_item').val();
                 $("#add_item").autocomplete('search', add_item);
                 
            });

        $('#add_item').on('click', function() {

              $(this).keyboard({ usePreview: false, autoAccept: true, alwaysOpen: true, }); 
              $('.ui-keyboard-button').on('click', function() { 
                   $('.ui-keyboard-input-current').trigger('keyup'); 
               });
         });


/*$("#poscustomer").autocomplete({
        source: function (request, response) {
              
                $.ajax({
                    type: 'get',
                    url: '<?=admin_url('customers/suggestions_new');?>',
                    dataType: "json",
                    data: {
                       term: request.term,
                       limit:10
                    },
                    success: function (data) {
                        $(this).removeClass('ui-autocomplete-loading');
                        response(data);
                    }
                });
            },

        select: function( event, ui ) {
            event.preventDefault();            
            $("#poscustomer").val(ui.id);
        }
    });*/

        $("#customer").autocomplete({
            source: function (request, response) {
              
                $.ajax({
                    type: 'get',
                    url: '<?=admin_url('customers/suggestions_new');?>',
                    dataType: "json",
                    data: {
                       term: request.term,
                       limit:10
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
                if ($(this).val().length >= 1 && ui.content[0].id == 0) {
                    bootbox.alert('<?=lang('no_match_found')?>', function () {
                        $('#customer').focus();
                    });
                    $(this).val('');
                }
              /*  else if (ui.content.length == 1 && ui.content[0].id != 0) {
                    ui.item = ui.content[0];
                    $(this).data('ui-autocomplete')._trigger('select', 'autocompleteselect', ui);
                    $(this).autocomplete('close');
                }*/
                else if (ui.content.length == 1 && ui.content[0].id == 0) {
                    bootbox.alert('<?=lang('no_match_found')?>', function () {
                        $('#customer').focus();
                    });
                    $(this).val('');

                }
            },
             select: function( event, ui ) {
                event.preventDefault();
                $("#poscustomer").val(ui.item.item_id);
                $("#customer").val(ui.item.label);
            }
/*            
            select: function (event, ui) {
                event.preventDefault();
                if (ui.id !== 0) {
                    var row = add_invoice_item(ui);
                    if (row)
                        $(this).val('');
                } else {
                    bootbox.alert('<?=lang('no_match_found')?>');
                }
            }*/
        });

        $(document).bind( "change.autocomplete", '#customer', function( event ) {               
                var customer = $('#customer').val();
                 $("#customer").autocomplete('search', customer);
                
            });


        <?php if ($pos_settings->tooltips) {echo '$(".pos-tip").tooltip();';}
        ?>
        // $('#posTable').stickyTableHeaders({fixedOffset: $('#product-list')});
        /*$('#posTable').stickyTableHeaders({scrollableArea: $('#product-list')});
        $('#product-list, #category-list, #subcategory-list, #brands-list').perfectScrollbar({suppressScrollX: true});*/
        $('select, .select').select2({minimumResultsForSearch: 7});
        /*$(document).on('click', '.recipe', function (e) {*/
        $(document).on('click', '.recipe:not(".has-varients")', function (e) {
		if($(this).hasClass("non_transaction")){
			 bootbox.alert('ITEM IS NOT AVAILABLE');
			 return false;
			}
		
            $('#modal-loading').show();
            code = $(this).val(),            
                wh = $('#poswarehouse').val(),
                cu = $('#poscustomer').val();
            $.ajax({
                type: "get",
                url: "<?= base_url('pos/qsr/getrecipeDataByCode')?>",
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
		if($(this).hasClass("non_transaction")){
			 bootbox.alert('ITEM IS NOT AVAILABLE');
			 return false;
			}
              var code = $(this).attr('code');  
                wh = $('#poswarehouse').val(),
                cu = $('#poscustomer').val();
				$vid = $(this).attr('data-id');
            $.ajax({
                type: "get",
                url: "<?= base_url('pos/qsr/getrecipeVarientDataByCode')?>",
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
		$('.table_middle_s').hide();
	       $('.table_bottom_s').hide();
      /*   $('#myVaraintModal .modal-body').html($popcon);
        $('#myVaraintModal').modal('show'); */
		 $('.v12').html($popcon);
		  $('.v12').show();
    });
        
        $(document).on('click', '.category', function () {
            if (cat_id != $(this).val()) {
                $('#open-category').click();
                $('#modal-loading').show();
                cat_id = $(this).val();
                var warehouse_id = '<?php echo $this->session->userdata('warehouse_id'); ?>';
                
                $.ajax({
                    type: "get",
                    url: "<?= base_url('pos/qsr/ajaxcategorydata');?>",
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
                    url: "<?= base_url('pos/qsr/ajaxbranddata');?>",
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
                // warehouse_id = $("#poswarehouse").val();
                var warehouse_id = '<?php echo $this->session->userdata('warehouse_id'); ?>';
                
                $.ajax({
                    type: "get",
                    url: "<?= base_url('pos/qsr/ajaxrecipe');?>",
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
                    url: "<?= base_url('pos/qsr/ajaxrecipe');?>",
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
                    url: "<?= base_url('pos/qsr/ajaxrecipe');?>",
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

        $(document).on('click', '#submit-sale', function () {

            calculateTotals();
            var balance = $('.balance_amount').val();               
            if (balance >= 0) {            
                  $('#submit-sale').text('<?=lang('loading');?>').attr('disabled', true);
                  getTotalDiscount();
                  $('#pos-sale-form').submit();
            }
            else{
                bootbox.alert("Paid amount is less than the payable amount.");     
                return false;           
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
		
		$('.ppdiscount').click(function () {
            if (count <= 1) {
                bootbox.alert('<?=lang('Please Add Product Before Discount. Thank You!');?>');
                return false;
            } else {
                $('#dsModal').modal();
            }
        });
		
		$(document).ready(function() {
    $("#susModal").modal({
        show: false,
        backdrop: 'static'
    });
    
  /*   $("#ppdiscount").click(function() {
       $("#dsModal").modal("show");             
    }); */
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
                $(this).text('<?=lang('loading');?>').attr('disabled', true);
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
            	
                Popup($('#bill_print').html());
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
        var is_chrome = Boolean(mywindow.chrome);
        mywindow.document.write('<html><head><title>Print</title>');
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
        /*mywindow.print();
        mywindow.close();*/
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
                
                $.get('<?= base_url('pos/qsr/p/order'); ?>', {data: JSON.stringify(socket_data)});
            });
            return false;
        }

        function printBill() {
			
            var socket_data = {
                'printer': <?= json_encode($printer); ?>,
                'logo': (biller && biller.logo ? biller.logo : ''),
                'text': bill_data
            };
            $.get('<?= base_url('pos/qsr/p'); ?>', {data: JSON.stringify(socket_data)});
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

        var variantsids ='';
        variantsids = $('.variant_id').map(function() {
            return this.value;
        }).get();


      var discountid = $('#order_discount_input :selected').val();
      var input_discount = 0;

    if(discountid !=0){
            $.ajax({
                type: 'POST',
                url: '<?= base_url('pos/qsr/calculate_customerdiscount');?>',
                dataType: "json",
                 async : false,
                data: {
                    recipeids: recipeids,recipeqtys: recipeqtys,variantsids: variantsids,discountid:discountid,divide: 1
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
<?php elseif($Settings->customer_discount =='manual') : ?>
$('.order_discount_input').keydown(function(){
    if($(this).val()==0){
	$(this).val('');
    }
});
$('.order_discount_input').click(function(){
    if($(this).val()==0){
	$(this).val('');
    }
});
<?php endif;?>
function getTotalDiscount(){

    var total =$('#total').text();    
    $.ajax({
        type: 'POST',
        url: '<?= base_url('pos/qsr/getTotalDiscount');?>',
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
      $('#updateOrderDiscount').on('click', function(e) {
        var unit_price =  $('#total').text(); 
        unit_price = parseFloat(unit_price);
         var sub_total = $('#sub_total').val(); 
        
        var ds = $('#order_discount_input').val() ? $('#order_discount_input').val() : '0';
        /*alert(ds);*/
        input_discount = 0;
    if (ds.indexOf("%") !== -1) {  
            var pds = ds.split("%");       
        if (!isNaN(pds[0])) {

             /*$.ajax({
                type: 'POST',
                url: '<?=admin_url('qsr/Percentage_input_discount');?>',
                dataType: 'JSON',
                // async: false,
                data: {unit_price:sub_total,percentage: pds[0]},
                success: function(data) {
                    
                    if(data != 0){
                      input_discount = parseFloat(data);
                    }else{
                        input_discount = 0;
                    }
                }

            });*/
         /*alert(sub_total);*/
         input_discount = parseFloat(((sub_total) * parseFloat(pds[0])) / 100);
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


</script>


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
<script type="text/javascript" src="<?=$assets?>qsr/js/pos_qsr.ajax.js?v=1"></script>
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
 function formatMoney(x, symbol) {
    if(!symbol) { symbol = ""; }
    if(site.settings.sac == 1) {
        return (site.settings.display_symbol == 1 ? site.settings.symbol : '') +
            ''+formatSA(parseFloat(x).toFixed(site.settings.decimals)) +
            (site.settings.display_symbol == 2 ? site.settings.symbol : '');
    }
    var fmoney = accounting.formatMoney(x, symbol, site.settings.decimals, site.settings.thousands_sep == 0 ? ' ' : site.settings.thousands_sep, site.settings.decimals_sep, "%s%v");
    if(symbol){
       return fmoney; 
    }
    return (site.settings.display_symbol == 1 ? site.settings.symbol : '') +
        fmoney +
        (site.settings.display_symbol == 2 ? site.settings.symbol : '');
}  
</script>
<style>
    .variant-popup{
    display: none;
    }
/*
    .sname{
    max-width:50px;
    }
*/
</style>


<div class="modal fade in" id="myVaraintModal" tabindex="-1" role="dialog" aria-labelledby="VariantModalLabel"
     aria-hidden="true" style="z-index:9999" >
    <div class="modal-dialog modal-md">
    <div class="modal-content">
        
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true" onClick="window.location.reload();"><i class="fa fa-2x">×</i>
            </button>
            <h4 class="modal-title" id="customerModalLabel">Variants</h4>
        </div>
        
        <div class="modal-body">
    </div>
    
    </div>
    </div>
    </div>

    
<script type="text/javascript">
	$('.kb-text-click').keyboard({
        autoAccept: true,
        alwaysOpen: false,
        openOn: 'focus',
        usePreview: false,
        layout: 'custom',
        //layout: 'qwerty',
        display: {
            'bksp': "\u2190",
            'accept': 'return',
            'default': 'ABC',
            'meta1': '123',
            'meta2': '#+='
        },
        customLayout: {
            'default': [
            'q w e r t y u i o p {bksp}',
            'a s d f g h j k l {enter}',
            '{s} z x c v b n m , . {s}',
            '{meta1} {space} {cancel} {accept}'
            ],
            'shift': [
            'Q W E R T Y U I O P {bksp}',
            'A S D F G H J K L {enter}',
            '{s} Z X C V B N M / ? {s}',
            '{meta1} {space} {meta1} {accept}'
            ],
            'meta1': [
            '1 2 3 4 5 6 7 8 9 0 {bksp}',
            '- / : ; ( ) \u20ac & @ {enter}',
            '{meta2} . , ? ! \' " {meta2}',
            '{default} {space} {default} {accept}'
            ],
            'meta2': [
            '[ ] { } # % ^ * + = {bksp}',
            '_ \\ | &lt; &gt; $ \u00a3 \u00a5 {enter}',
            '{meta1} ~ . , ? ! \' " {meta1}',
            '{default} {space} {default} {accept}'
            ]}
        });

    $(document).ready(function (e) {
     $('input[type="checkbox"],[type="radio"]').not('.skip').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%' // optional        
        });
     });

  $('#add_item').focus(function() {
    
    $('.biller-keyboard .ui-keyboard-button').css({"height":"height: 2em !important;"});
    $('.ui-keyboard div').css({"max-width":"660px","width":"100%","margin-left":"5%","margin-top":"-18%"});
    $('.ui-keyboard div').css({"max-width":"660px: 2em !important;"});    
  });  
    $('#customer').focus(function() {
    
    $('.biller-keyboard .ui-keyboard-button').css({"height":"height: 2em !important;"});
    $('.ui-keyboard div').css({"max-width":"660px","width":"100%","margin-left":"5%","margin-top":"-18%"});
    $('.ui-keyboard div').css({"max-width":"660px: 2em !important;"});    
  });  

$('#reference_note').focus(function() {    
    $('.biller-keyboard .ui-keyboard-button').css({"height":"height: 2em !important;"});
    $('.ui-keyboard div').css({"max-width":"660px","width":"100%","margin-left":"27%","margin-top":"-23%"});
    $('.ui-keyboard div').css({"max-width":"660px: 2em !important;"});    
  });  

  $("#add_item").focusout(function () {
    //alert("hello");
});

/*function suspend_cancel() {
    alert('<?php echo $sid; ?>');

}*/
$(document).on('click', '.reset2', function () {    
	$("#updateOrderDiscount").val('');
});

function suspend_cancel(){

    var sid ="<?php echo $sid; ?>";
    // alert(sid);
    if(sid){
        $.ajax({
            type: "get", async: false,
            url: "<?= base_url('pos/qsr/delete')?>/" + sid,
            // url: '<?=admin_url('qsr/delete');?>',
            dataType: 'JSON',            
            data: {sid:sid},
            success: function(data) {
                localStorage.clear();
                location.reload();
            }
        });
 }
}


$(document).on('click', '.vritem', function() {
	$('.v12').empty();
	//$('#popup_id_s').hide();
	$('.table_middle_s').show();
	$('.table_bottom_s').show();
	$('#cpinner').show();
	
	
});
$(document).on('click', '.iclose', function() {
	$("#sent_to_kitchen").prop("disabled", false);
	$('.v12').empty();
	$('.table_middle_s').show();
	$('.table_bottom_s').show();
	$('#cpinner').show();
	
	
});
</script>

	<script>
		$(document).ready(function(){
			$('#myCarousel,#myCarousel1,#myCarousel2').carousel();
		});
	</script>
	<script>
	$(function(){
		$("#item-list").mCustomScrollbar({
			 theme:"dark-3" ,
		});
		$("#table_pay_s").mCustomScrollbar({
			 theme:"dark-3" ,
		});
		$("#item_list_m").mCustomScrollbar({
			 theme:"dark-3" ,
		});
	});
	</script>
	<script>
		$(document).ready(function() {
			$('.minus').click(function () {
				var $input = $(this).parent().find('input');
				var count = parseInt($input.val()) - 1;
				count = count < 0 ? 0 : count;
				$input.val(count);
				$input.change();
				return false;
			});
			$('.plus').click(function () {
				var $input = $(this).parent().find('input');
				$input.val(parseInt($input.val()) + 1);
				$input.change();
				return false;
			});
		});
	</script>
</body>
</html>
