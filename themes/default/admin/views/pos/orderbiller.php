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
<style type="text/css" media="all">
            body { color: #000; }
            #wrapper1 { max-width: 480px; margin: 0 auto; padding-top: 20px; }
            .btn { border-radius: 0; margin-bottom: 5px; }
            .bootbox .modal-footer { border-top: 0; text-align: center; }
            h3 { margin: 5px 0; }
            .order_barcodes img { float: none !important; margin-top: 5px; }
            @media print {
                .no-print { display: none; }
                #wrapper1 { max-width: 480px; width: 100%; min-width: 250px; margin: 0 auto; }
                .no-border { border: none !important; }
                .border-bottom { border-bottom: 1px solid #ddd !important; }
                table tfoot { display: table-row-group; }
            }
	    .bootbox.modal{
		background: none !important;
	    }
	    .available-c-limit{
		color:#f00;
	    }
	    #payment-customer-name{
		float: right;
	    }
        </style>
    <script>var curr_page="order_biller";</script>
    <?php if(@$_GET['tid'] && isset($_SERVER['HTTP_REFERER'])): ?>
        <script>var curr_func="update_tables";var tableid = '<?=$_GET['tid']?>';</script>	
    <?php endif; ?>
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
                
                    <div id="orderbilling_box">
                    
                    </div>
                    
                    <div style="clear:both;"></div>
                </div>
                <div style="clear:both;"></div>
            </div>
        </div>
    </div>
</div>

<?php
$this->load->view($this->theme . 'pos/pos_footer');
?>

<script>
function ajaxData(type_id)
{
	
	$.ajax({
	  url: "<?=admin_url('pos/ajaxorder_billing');?>",
	  type: "get",
	  data: { 
		type: type_id
	  },
	  success: function(response) {
			$("#orderbilling_box").html(response);
	  }
	});
}
$(document).ready(ajaxData(<?php echo $type; ?>));
var ajaxDatatimeout = setInterval(ajaxData(<?php echo $type; ?>), 1000);

</script>

<?php
$currency = $this->site->getAllCurrencies();
?>
<div class="modal fade in" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="payModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true"><i
                            class="fa fa-2x">&times;</i></span><span class="sr-only"><?=lang('close');?></span></button>
		<h4 class="modal-title" id="payment-customer-name"></h4>
		<h4 class="modal-title" id="payModalLabel"><?=lang('finalize_sale');?></h4>
                
            </div>
            <div class="modal-body" id="payment_content">

             <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'id' => 'pos-payment-form');
             echo admin_form_open("pos/paymant", $attrib);
             $type = $this->input->get('type');
             ?>
                <div class="row">
                <input type="hidden" name="type" class="type" value="<?php echo $type;?>"/>
                <input type="hidden" name="balance_amount" class="balance_amount" value=""/>
                <input type="hidden" name="due_amount" class="due_amount" value=""/>
                    <div class="col-md-12 col-sm-12">
                        <?php if ($Owner || $Admin || !$this->session->userdata('biller_id')) { ?>
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
                            }
                        ?>
                        <input type="hidden" name="bill_id" id="bill_id" class="bill_id" />
                        <input type="hidden" name="order_split_id" id="order_split_id" class="order_split_id" />
                        <input type="hidden" name="sales_id" id="sales_id" class="sales_id" />
            			<input type="hidden" name="credit_limit" id="credit_limit" class="credit_limit" />
            			<input type="hidden" name="company_id" id="company_id" class="company_id" />
            			<input type="hidden" name="customer_type" id="customer_type" class="customer_type" />
            			<input type="hidden" name="customer_id" id="customer_id" class="customer_id"/>
                        <input type="hidden" name="total" id="total" class="total" />
                        <input type="hidden" name="loyaltypoints" id="loyaltypoints" class="loyaltypoints" />
                        <input type="hidden" name="loyalty_used_points" id="loyalty_used_points" class="loyalty_used_points" />
                        <div class="form-group"  style="margin-bottom: 5px;">
                            <div class="row">
                                <div class="col-sm-6">
                                    <?=form_textarea('sale_note', '', 'id="sale_note" class="form-control kb-text skip" style="height: 50px;" placeholder="' . lang('sale_note') . '" maxlength="250"');?>
                                </div>
                                <div class="col-sm-6">
                                    <?=form_textarea('staffnote', '', 'id="staffnote" class="form-control kb-text skip" style="height: 50px;" placeholder="' . lang('staff_note') . '" maxlength="250"');?>
                                </div>
                            </div>
                        </div>
                        <div class="clearfir"></div>
                        <div id="payments" class="payment-row">
                            <div class="well well-sm well_1" style="padding: 5px 10px;">
                                <div class="payment">
                                    <div class="row">
                                        
                                        <div class="col-sm-12">
                                            <div class="form-group"  style="margin-bottom: 5px;">
                                                <?=lang("paying_by", "paid_by_1");?>
                                                <select name="paid_by[]" id="paid_by_1" data-index="1" class="form-control paid_by">
                                                    <?= $this->sma->paid_opts_front(); ?>
                                                   
                                                </select>
                                            </div>
                                        </div>
                                        <?php
										foreach($currency as $currency_row){
											
											$default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
											
											if($currency_row->code == $default_currency_data->code){
                                              
										?>
                                        <div class="col-sm-6 base_currency">
                                            <div class="form-group"  style="margin-bottom: 5px;">
                                                <label><?=lang('amount')?> <?=$currency_row->code; ?></label>
                                                <input name="amount_<?php echo $default_currency_data->code; ?>[]" type="text" id="amount_<?php echo $default_currency_data->code; ?>_1" data-rate="<?=$currency_row->rate; ?>" data-code="<?=$currency_row->code; ?>" data-id="<?=$currency_row->id; ?>"  class="pa form-control  amount kb-pad" autocomplete="off"  />
                                            </div>
                                        </div>
                                        	<?php
											}else{
											?>
                                        <div class="col-sm-6 multi_currency_1">
                                            <div class="form-group"  style="margin-bottom: 5px;">
                                                 <label><?=lang('amount')?> <?=$currency_row->code; ?></label>
                                                <input name="amount_<?=$currency_row->code; ?>[]" type="text" id="amount_<?=$currency_row->code; ?>_1" data-rate="<?=$currency_row->rate; ?>" data-code="<?=$currency_row->code; ?>" data-id="<?=$currency_row->id; ?>"  class="pa form-control  amount kb-pad" autocomplete="off"/>
                                            </div>
                                        </div>
                                        <?php
											}
										}
										?>
                                        
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group lc_1" style="display: none;">
                                                <div id="lc_details_1" style="color: red;">         
                                                </div>
                                                <div id="lc_reduem_1" style="color: green;">                                                    
                                                </div>                                                
                                                    <div class="form-group "  style="margin-bottom: 5px;">
                                                       <label for="loyalty_points_1">Redemption Points</label> <input name="paying_loyalty_points[]" type="text" id="loyalty_points_1" class="pa form-control loyalty_points" />
                                                    </div>            
                                            </div>

                                            <div class="form-group gc_1" style="display: none;">
                                                <?=lang("gift_card_no", "gift_card_no_1");?>
                                                <input name="paying_gift_card_no[]" type="text" id="gift_card_no_1"
                                                       class="pa form-control kb-pad gift_card_no"/>
                                                <div id="gc_details_1"></div>
                                            </div>
                                            <div class="pcc_1" style="display:none;">
                                                <div class="form-group"  style="margin-bottom: 5px;">
                                                    <input type="text" id="swipe_1" class="form-control kb-text swipe"
                                                           placeholder="<?=lang('swipe')?>"/>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group"  style="margin-bottom: 5px;">
                                                            <input name="cc_no[]" type="text" id="pcc_no_1"
                                                                   class="form-control kb-pad"
                                                                   placeholder="<?=lang('cc_no')?>"/>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group"  style="margin-bottom: 5px;">

                                                            <input name="cc_holer[]" type="text" id="pcc_holder_1"
                                                                   class="form-control kb-text"
                                                                   placeholder="<?=lang('cc_holder')?>"/>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group"  style="margin-bottom: 5px;">
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
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group" style="margin-bottom: 5px;">
                                                          <select class="form-control col-sm-2" name="cc_month[]" id="pcc_month_1">
															<option value="January">January</option>
															<option value="February">February </option>
															<option value="March">March</option>
															<option value="April">April</option>
															<option value="May">May</option>
															<option value="June">June</option>
															<option value="July">July</option>
															<option value="August">August</option>
															<option value="September">September</option>
															<option value="October">October</option>
															<option value="November">November</option>
															<option value="December">December</option>
														</select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <select class="form-control pcc_year" name="cc_year[]" id="pcc_year_1">				
															  </select>

                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group"  style="margin-bottom: 5px;">

                                                            <input name="cc_cvv2[]" type="text" id="pcc_cvv2_1"
                                                                   class="form-control kb-pad"
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
                                            <div class="form-group" style="margin-bottom: 0px;">
                                                <?=lang('payment_note', 'payment_note');?>
                                                <textarea name="payment_note[]" id="payment_note_1"
                                                          class="pa form-control kb-text payment_note"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="multi-payment" class="payment-row"></div>
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
                                    <td><?=lang('total_payable_in')?> <?php echo $currency_row->code; ?></td>
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
                                    <td><?=lang('balance')?> <?php echo $currency_row->code; ?></td>
                                    <td class="text-right"><span id="balance_<?php echo $currency_row->code; ?>">0.00</span>
                                        <input type="hidden" id="balance_amt_<?php echo $currency_row->code; ?>">
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
                                </tbody>
                            </table>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-3 text-center hidden">
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
                    </div>
                </div>
             <?php                
             echo form_hidden('remove_image','No');
             echo form_hidden('action', 'PAYMENT-SUBMIT');
             echo form_close();
             ?>

            </div>
            <div class="modal-footer">
                <button class="btn btn-block btn-lg btn-primary" id="submit-sale1"><?=lang('send');?></button>
            </div>
        </div>
    </div>
</div>

<div id="order_tbl"><span id="order_span"></span>
    <table id="order-table" class="prT table table-striped" style="margin-bottom:0;" width="100%"></table>
</div>

<div id="bill_tbl"><span id="bill_span"></span>
    <div id="bill_header"></div>
   <!--  <table id="bill-table" width="100%" class="prT table table-striped" style="margin-bottom:0;"></table> -->
   <div id="bill-total-table"></div>
    <!-- <table id="bill-total-table" class="prT table table table-striped " ></table> -->
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
                <div class="form-group">
                    <?= lang('remarks'); ?>
                    <?= form_textarea('remarks', '', 'class="form-control" id="remarks" style="height:80px;"'); ?>
                </div>
                <input type="hidden" id="sale_id" value=""/>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="cancel_orderitem"><?=lang('send')?></button>
            </div>
        </div>
    </div>
</div>


<?php unset($Settings->setting_id, $Settings->smtp_user, $Settings->smtp_pass, $Settings->smtp_port, $Settings->update, $Settings->reg_ver, $Settings->allow_reg, $Settings->default_email, $Settings->mmode, $Settings->timezone, $Settings->restrict_calendar, $Settings->restrict_user, $Settings->auto_reg, $Settings->reg_notification, $Settings->protocol, $Settings->mailpath, $Settings->smtp_crypto, $Settings->corn, $Settings->customer_group, $Settings->srampos_username, $Settings->purchase_code);?>
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
    var username = '<?=$this->session->userdata('username');?>', order_data = '', bill_data = '';

    $(document).ready(function () {

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
       /* $('#paymentModal').on('change', '#amount_<?=$i?>', function (e) {
            $('#amount_val_<?=$i?>').val($(this).val());
        });
        $('#paymentModal').on('blur', '#amount_<?=$i?>', function (e) {
            $('#amount_val_<?=$i?>').val($(this).val());
        });*/
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
		
		<?php
			foreach($currency as $currency_row){
			$default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
			if($currency_row->code == $default_currency_data->code){
			?>
			var gtotal_<?php echo $currency_row->code; ?> = 0;
			<?php
			}else{
			?>
			 var gtotal_<?php echo $currency_row->code; ?> = 0;
			<?php
			}
			?>
			<?php
			}
			?>
	    function payment_popup(){
	    $('#paymentModal').appendTo("body").modal('show', {backdrop: 'static', keyboard: false});            
            var billid = $thisObj.siblings('.billid').val();             
            var customer_id = $thisObj.siblings('.customer-id').val(); 
            var customer_type = $thisObj.siblings('.customer-type').val(); 
            var company_id = $thisObj.siblings('.company-id').val(); 

            var ordersplit = $thisObj.siblings('.order_split').val();
            var salesid = $thisObj.siblings('.salesid').val(); 
            var grandtotal = $thisObj.siblings('.grandtotal').val();
	        var credit_limit = $thisObj.siblings('.credit-limit').val();
	        console.log(credit_limit)
            var count = $thisObj.siblings('.totalitems').val(); 
            $('.bill_id').val(billid);
            $('.sales_id').val(salesid);
            $('.total').val(grandtotal);
            $('.order_split_id').val(ordersplit);
            $('.credit_limit').val(credit_limit);

            $('.customer_id').val(customer_id);
            $('.customer_type').val(customer_type);
	        $('.company_id').val(company_id);

            var twt = formatDecimal(grandtotal);
	        console.log('grandtotal-'+grandtotal)
            console.log('bil-'+billid);
            if (count == 0) {
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
            //$('#paymentModal').appendTo("body").modal('show');
			<?php
			foreach($currency as $currency_row){
			$default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
			if($currency_row->code == $default_currency_data->code){	
			?>
			$('#amount_<?php echo $currency_row->code; ?>_1').focus().val('');
			<?php
			}else{
			?>
            $('#amount_<?php echo $currency_row->code; ?>_1').val('');
			<?php
			}
			}
			?>
	    }
	    

	    $(document).on('change', '#choose-discount', function(){
		$('#discount-name').text($('#choose-discount option:selected').text());
	    });
	    $(document).on('click', '.request_bil', function(){
		$thisObj = $(this);
		var billid = $(this).parents('.payment-list-container').find('.billid').val();
		var ordersplit = $(this).parents('.payment-list-container').find('.order_split').val();
		var salesid = $(this).parents('.payment-list-container').find('.salesid').val(); 
		var grandtotal = $(this).parents('.payment-list-container').find('.grandtotal').val(); 
		var count = $(this).parents('.payment-list-container').find('.totalitems').val();        
		$url = '<?=admin_url().'pos/checkCustomerDiscount'?>';
		$.ajax({
			url: $url,
			type: "POST",
			data: {bill_id:billid},
			dataType: "json",
			success:function(data){
			    
			    if (data.unique_discount == 0) {			       
			    var discounttype =  "<?php echo $Settings->customer_discount ?>";                
				if("<?php echo $Settings->customer_discount ?>" == "customer" ) {                 
				$dropdown = '<select id="choose-discount">';
				$dropdown +='<option value="0">No Discount</option>';
				$.each( data.all_dis, function( index, value ){
				    $selected = (data.cus_dis.customer_discount_id == value.id)?"selected='selected'":"";
				    $dropdown +='<option value="'+value.id+'" '+$selected+'>'+value.name+'</option>';
				});
				$dropdown +='</select>';
				if (data.cus_dis.customer_discount_id != 0) {
					$msg = "<div id='discount-confirm-msg'>Do You want to apply \"<span id='discount-name'>"+data.cus_dis.name+"</span>\"?</div>";
				} else {
					$msg = "<div id='discount-confirm-msg'>Do You want to apply any discount?</div>";
				}				
				    bootbox.confirm({
					closeButton: true,
					message: $dropdown+$msg,					
					 buttons: {
					     confirm: {
						 label: 'Apply',
						 className: 'btn-success'
                         
					     },
					     cancel: {
						 label: 'Cancel',
						 className: 'btn-danger'
					     }
					 },
					 callback: function (result) {
					   bootbox.hideAll();$('.modal-backdrop').remove();
					     if (result) {
						dis_id  = $('#choose-discount').val();
						 $.ajax({
						     url: '<?=admin_url().'pos/DINEINupdateBillDetails'?>',//updateBillDetails
						     type: "GET",
						     data: {bill_id:billid,dis_id:dis_id},
						     dataType: "json",
						     success:function(data){

							 if (!data.no_discount) {
							     $thisObj.parents('.payment-list-container').find('.grandtotal').val(data.amount);
							     $thisObj.siblings('.grandtotal_req').val(data.amount);
							 }
							 requestBill(billid);//payment_popup($thisObj);
						     }
						 });
					     }else{  requestBill(billid);}
					     
					 }
				     });
				    return false;
			       }else{ requestBill(billid)}
			    }else{ requestBill(billid)}
			    
			}
		    });
	    });
		
		$(document).on('click', '.request_bil_new', function(){


		    $(".well-sm:not(:first)").remove();
            $('.close-payment').trigger('click');
            var pa = 1;
            var phtml = $('#payments').html(),
                    update_html = phtml.replace(/_1/g, '_' + pa);
                update_html= update_html.replace(/data-index="1"/g,'data-index="'+pa+'"');
                calculateTotals();
                pa--;

			$thisObj = $(this);
			var billid = $(this).parents('.payment-list-container').find('.billid').val();
			var ordersplit = $(this).parents('.payment-list-container').find('.order_split').val();
			var salesid = $(this).parents('.payment-list-container').find('.salesid').val(); 
			var grandtotal = $(this).parents('.payment-list-container').find('.grandtotal').val(); 
			var count = $(this).parents('.payment-list-container').find('.totalitems').val();
			$url = '<?=admin_url().'pos/DINEINcheckCustomerDiscount'?>';
			$.ajax({
			url: $url,
			type: "POST",
			data: {bill_id:billid},
			dataType: "json",
			success:function(data){

			    if (data.unique_discount == 0) {	
			     console.log(data);			       
                                      
                 if("<?php echo $Settings->customer_discount ?>" == "customer" ) {            
    			 $dropdown = '<select id="choose-discount">';
    			 $dropdown +='<option value="0">No Discount</option>';
				$.each( data.all_dis, function( index, value ){
				    $selected = (data.cus_dis.customer_discount_id == value.id)?"selected='selected'":"";
				    $dropdown +='<option value="'+value.id+'" '+$selected+'>'+value.name+'</option>';
				});
				$dropdown +='</select>';
				if (data.cus_dis.customer_discount_id != 0) {
					$msg = "<div id='discount-confirm-msg'>Do You want to apply \"<span id='discount-name'>"+data.cus_dis.name+"</span>\"?</div>";
				} else {
					$msg = "<div id='discount-confirm-msg'>Do You want to apply any discount?</div>";
				}
				    bootbox.confirm({
					message: $dropdown+$msg,					
					 buttons: {
					     confirm: {
						 label: 'Apply',
						 className: 'btn-success'
                         
					     },
					     cancel: {
						 label: 'Cancel',
						 className: 'btn-danger'
					     }
					 },
					 callback: function (result) {					   
					     if (result) {
						dis_id  = $('#choose-discount').val();
						 $.ajax({
						     url: '<?=admin_url().'pos/DINEINupdateBillDetails'?>',
						     type: "GET",
						     data: {bill_id:billid,dis_id:dis_id},
						     dataType: "json",
						     success:function(data){
								 if (!data.no_discount) {
									 $thisObj.parents('.payment-list-container').find('.grandtotal').val(data.amount);
									 $thisObj.siblings('.grandtotal_req').val(data.amount);
									 payment_popup($thisObj);
								 }else{
									payment_popup($thisObj);
								 }
						     }
						 });
					     }else{  
						 	payment_popup($thisObj);
						}
					     
					 }
				     });
				    return false;
			       
			    }else{ 
                    payment_popup($thisObj);
                }
                }else{ 
                    payment_popup($thisObj);
                }
			}
		    });
		});
		
	    $(document).on('click', '.btn_payment', function(){
			
            $('#paymentModal').appendTo("body").modal('show');            
            var billid = $(this).siblings('.billid').val(); 
            var ordersplit = $(this).siblings('.order_split').val();
            var salesid = $(this).siblings('.salesid').val(); 
            var grandtotal = $(this).siblings('.grandtotal').val(); 
            var count = $(this).siblings('.totalitems').val();
	    var credit_limit =  $(this).siblings('.credit-limit').val();
	    var customer_type =  $(this).siblings('.customer-type').val();
	    var company_id =  $(this).siblings('.company-id').val();
	    var customer_id =  $(this).siblings('.customer-id').val();
	    $('#payment-customer-name').text('Customer : '+$(this).siblings('.customer-name').val());
	    $('#multi-payment').html('');
	    //console.log(credit_limit)
            $('.bill_id').val(billid);
            $('.sales_id').val(salesid);
            $('.total').val(grandtotal);
            $('.order_split_id').val(ordersplit);
	    $('.credit_limit').val(credit_limit);
	    $('.company_id').val(company_id);
	    $('.customer_type').val(customer_type);
	    $('.customer_id').val(customer_id);
	    $('.available-c-limit').remove();
	    pa = 2;
            var twt = formatDecimal(grandtotal);
            
            if (count == 0) {
                bootbox.alert('<?=lang('x_total');?>');
                return false;
            }
			
			<?php
			foreach($currency as $currency_row){
			$default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
			if($currency_row->code == $default_currency_data->code){
			?>
			 gtotal_<?php echo $currency_row->code; ?> = (parseFloat(twt) / parseFloat(<?php echo $currency_row->rate; ?>)) * <?php echo $default_currency_data->rate; ?>;
			 $('#twt_<?php echo $currency_row->code; ?>').text(formatMoney(gtotal_<?php echo $currency_row->code; ?>,'<?php echo $currency_row->symbol; ?>'));
			<?php
			}else{
			?>
			  gtotal_<?php echo $currency_row->code; ?> = (parseFloat(twt) / parseFloat(<?php echo $currency_row->rate; ?>)) * <?php echo $default_currency_data->rate; ?>;
			  $('#twt_<?php echo $currency_row->code; ?>').text(formatMoney(gtotal_<?php echo $currency_row->code; ?>,'<?php echo $currency_row->symbol; ?>'));
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
			$('#amount_<?php echo $currency_row->code; ?>_1').focus().val(grandtotal);
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
			//$('#amount_<?php echo $currency_row->code; ?>_1').focus().val('');
			<?php
			}else{
			?>
            $('#amount_<?php echo $currency_row->code; ?>_1').val('');
			<?php
			}
			}
			?>
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
        $(document).on('click', '.addButton', function () {            
            if (pa <= 2) {                
				$('#paymentModal').css('overflow-y', 'scroll');
                $('#paid_by_1, #pcc_type_1').select2('destroy');
		        $('#amount_USD_2').removeClass('credit-max');
                var phtml = $('#payments').html(),
                    update_html = phtml.replace(/_1/g, '_' + pa);
				update_html= update_html.replace(/data-index="1"/g,'data-index="'+pa+'"');
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
			
        $('#multi-payment').append('<button type="button" class="close close-payment" style="margin: -10px 0px 0 0;"><i class="fa fa-2x">&times;</i></button>' + update_html);$('.pcc_2').hide();$('.multi_currency_2').show();
		 $('#multi-payment').find('.available-c-limit').remove();
         $('.lc_'+pa).hide();
         // $amount =  $('#balance_USD').text();
		 $amount =  $('#balance_amt_USD').val();
		 $amount = parseFloat($amount.match(/([0-9]*\.[0-9]+|[0-9]+)/));

		    $('#amount_USD_2').attr('readonly',false);
            $('#amount_USD_2').val($amount);		 
		 
                $('#paid_by_1, #pcc_type_1, #paid_by_' + pa + ', #pcc_type_' + pa).select2({minimumResultsForSearch: 7});
                read_card();
                pa++;
				
				
				$('.kb-pad').keyboard({
					restrictInput: true,
					preventPaste: true,
					autoAccept: true,
					alwaysOpen: false,
					openOn: 'click',
					usePreview: false,
					layout: 'custom',
					maxLength: 10,
					display: {
						'b': '\u2190:Backspace',
					},
					customLayout: {
						'default': [
						'1 2 3 4',
						'5 6 7 8  ',
						' 9 0 . {b}',
			
						' {accept} {cancel}'
						]
					}
				});
				$('.kb-text').keyboard({
					autoAccept: true,
					alwaysOpen: false,
					openOn: 'focus',
					usePreview: false,
					layout: 'custom',
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
				
            } else {
                bootbox.alert('<?=lang('max_reached')?>');
                return false;
            }
           
            $('#paymentModal').css('overflow-y', 'scroll');
        });

        $(document).on('click', '.close-payment', function () {
            $(this).next().remove();
            $(this).remove();
			calculateTotals();
            pa--;
        });

        $(document).on('focus', '.amount', function () {
			<?php $default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency); ?>
            pi_<?php echo $default_currency_data->code; ?> = $(this).attr('id');
            calculateTotals();
        }).on('blur', '.amount', function () {
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
            
    		$('#balance_<?php echo $currency_row->code; ?>').text(formatMoney(total_paying - (gtotal_<?php echo $currency_row->code; ?> * <?php echo $currency_row->rate; ?>) ),'<?php echo $currency_row->symbol; ?>');

            $('#balance_amt_<?php echo $currency_row->code; ?>').val(formatDecimal(total_paying - (gtotal_<?php echo $currency_row->code; ?> * <?php echo $currency_row->rate; ?>) ),'<?php echo $currency_row->symbol; ?>');

            // id="balance_<?php echo $currency_row->code; ?>"
    		$('.balance_amount').val(formatDecimal(total_paying - (gtotal_<?php echo $currency_row->code; ?> * <?php echo $currency_row->rate; ?>)),'<?php echo $currency_row->symbol; ?>');
    		<?php
    		}else{
                $getExchangesymbol = $this->site->getExchangeCurrency($this->Settings->default_currency);
    		?>
    		$('#balance_<?php echo $currency_row->code; ?>').text(formatMoney((total_paying - (gtotal_<?php echo $currency_row->code; ?> * <?php echo $currency_row->rate; ?>) ) / <?php echo $currency_row->rate; ?>,'<?php echo $getExchangesymbol; ?>'));
    		
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

        $(document).on('change', 'select.paid_by', function () {

            $('#clear-cash-notes').click();
			$index = $( this ).attr('data-index');
			<?php
			foreach($currency as $currency_row){
			$default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
			if($currency_row->code == $default_currency_data->code){
			?>
			$('#amount_<?php echo $currency_row->code; ?>_'+$index).val('');
            $('#amount_<?php echo $currency_row->code; ?>_'+id).attr('readonly', false);
			<?php
			}else{
			?>
			$('#amount_<?php echo $currency_row->code; ?>_'+$index).val('');
            $('#amount_<?php echo $currency_row->code; ?>_'+id).attr('readonly', false);			
			<?php
			}
			}
			?>
		
            $('#amount_'+$index).val('');
			$('#amount_USD_'+$index).val('');
            var p_val = $(this).val(),
                id = $(this).attr('id'),
                pa_no = id.substr(id.length - 1);
            $('#rpaidby').val(p_val);
            if (p_val == 'cash' || p_val == 'other' || p_val == 'credit') {
                $('#amount_<?php echo $currency_row->code; ?>_'+pa_no).attr('readonly', false);
                $('.lc_' + pa_no).hide();
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
                $('.lc_' + pa_no).hide();
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
                $('.lc_' + pa_no).hide();
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
            } else if (p_val == 'loyalty') {
                
                $('.lc_' + pa_no).show();
                $('.pcheque_' + pa_no).hide();
                $('.gc_' + pa_no).hide();
                $('.pcash_' + pa_no).hide();
                $('.pcc_' + pa_no).hide();+
                $('.multi_currency_'+pa_no).hide();
                $('#amount_<?php echo $currency_row->code; ?>_'+pa_no).focus();
                $('#amount_<?php echo $currency_row->code; ?>_'+pa_no).attr('readonly', true);
                $('#loyalty_points_' + pa_no).focus();
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
                                $('#loyalty_points_' + id).parent('.form-group').addClass('has-error');
                                bootbox.alert('Gift card number is incorrect or expired.');
                            } else if (data.customer_id !== null && data.customer_id !== $('#poscustomer').val()) {
                                $('#loyalty_points_' + id).parent('.form-group').addClass('has-error');
                                bootbox.alert('Gift card number is not for this customer.');
                            } else {
                                $('#loyaltypoints').val(data.total_points);
                                $('#lc_details_' + id).html('<small>Card No: ' + data.loyalty_card_no + '&nbsp;&nbsp;Value: ' + data.total_points +'</small>');                                                               
                                $('#loyalty_points_' + id).parent('.form-group').removeClass('has-error');                                 
                            }
                        }
                    });
                }               
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
				$(this).parents('.payment').find('input').val('');
			$('#pcc_month_'+$index).prepend('<option value="">Month</option>');
			$('#pcc_year_'+$index).prepend('<option value="">Year</option>');
			$('#pcc_month_'+$index).val('');
			$('#pcc_year_'+$index).val('');
			// $amount = $('#balance_USD').text();
            $amount = $('#balance_amt_USD').val();			
            $amount = parseFloat($amount.match(/([0-9]*\.[0-9]+|[0-9]+)/));
			
			$('#amount_USD_'+$index).removeClass('credit-max');
			$(this).parent('.form-group').find('.available-c-limit').remove();
			if ($( this ).val()=='credit') {
			    //$creditlimit = $('#credit_limit').val();
			    $('#amount_USD_'+$index).addClass('credit-max');
			    $inputCredit = 0;
			    $('.credit-max').each(function(n,v){
				if($(this).attr('id')!="amount_USD_"+$index){
				    $inputCredit += ($(this).val()=='')?0:parseFloat($(this).val());
				}
			    })
			    $creditlimit = parseFloat($('#credit_limit').val())-parseFloat($inputCredit);
			    //alert($('#credit_limit').val());alert($inputCredit)
			    //console.log('$inputCredit-'+$inputCredit)
			    //console.log('val()-'+$('#credit_limit').val())
			    //console.log('climit-'+$creditlimit)
			    //console.log('$amount-'+$amount)
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
                if (p_val != 'loyalty') {
			      if($amount>0){$('#amount_USD_'+$index).val($amount)};
                }else{
                    $('#loyalty_points_' + $index).focus();
                }
        });

       
        $(document).on('change', '.loyalty_points', function () {
            var loyaltypoints = $("#loyaltypoints").val(); 
            var redemption = $(this).val() ? $(this).val() : 0;
            var customer_id = $("#customer_id").val();    
            $('#loyalty_used_points').val(0);             
            var payid = $(this).attr('id'),
                id = payid.substr(payid.length - 1);
            if(parseFloat(loyaltypoints) == 0){    
                 bootbox.alert('Gift card number is incorrect or expired.');    
                 $('#loyalty_points_' + id).focus().val('');            
             }else if (parseFloat(redemption) <= parseFloat(loyaltypoints)) {
                $bal_amount = $('#balance_amt_USD').val();
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
                                 $('#loyalty_points_' + id).focus().val('');                                  
                                 $('#amount_<?php echo $currency_row->code; ?>_'+id).val('');
                            } else if (data.customer_id !== null && data.customer_id !== $('#poscustomer').val()) {
                                // bootbox.alert('Gift card number is not for this customer.');
                            } else if(data.total_redemamount > $bal_amount) {
                                    bootbox.alert('Please Enter less than your points or equal.');
                                    $('#lc_reduem_' + id).html('<small>Redemption: ' + parseFloat(data.redemption) + '&nbsp;&nbsp;Amount: ' + parseFloat(data.amount) +'</small>');
                                    $('#loyalty_points_' + id).focus().val('');
                                    $('#amount_<?php echo $currency_row->code; ?>_'+id).val('');
                               }else{                                
                                    // $('#loyalty_points_' + id).parent('.form-group').removeClass('has-error');
                                    $('#lc_reduem_' + id).html('<small>Redemption: ' + parseFloat(data.redemption) + '&nbsp;&nbsp;Amount: ' + parseFloat(data.amount) +'</small>'); 
                                    $('#loyalty_used_points').val(redemption);       
                                    $('#amount_<?php echo $currency_row->code; ?>_'+id).focus().val(data.total_redemamount);
                                    $('#amount_<?php echo $currency_row->code; ?>_'+id).attr('readonly', true);
                              }
                        }
                    });
                }else{
                    
                    bootbox.alert('Please Enter less than your points or equal.');  
                     $('#loyalty_points_' + id).focus().val('');
                     $('#amount_<?php echo $currency_row->code; ?>_'+id).val('');
                    
                }           
        });


$(document).on('change', '.credit-max', function () {

    if ($('#customer_type').val()=='prepaid') {
	//$creditlimit = $('#credit_limit').val();
	$inputCredit = 0;
	$index = $(this).parents('.payment-row').find('select').attr('data-index');
	//console.log('index'+$index)
	$('.credit-max').each(function(n,v){
	    console.log($(this).attr('id')+'=='+"amount_USD_"+$index);
	    if($(this).attr('id')!="amount_USD_"+$index){
		$inputCredit += ($(this).val()=='')?0:parseFloat($(this).val());
	    }
	});
	//console.log('DDclimit-'+$('#credit_limit').val())
	//console.log('DD$inputCredit-'+$inputCredit)
	$creditlimit = parseFloat($('#credit_limit').val())-parseFloat($inputCredit);
	//console.log('$creditlimit'+$creditlimit+'--'+$(this).val());
	if(parseFloat($(this).val())>parseFloat($creditlimit)){$(this).val('');alert('Amount Exceeds credit limit');}
    }
    
});

		
        $(document).on('click', '#submit-sale1', function () {
            var balance = $('.balance_amount').val();
            if (balance >= 0) {
                  $('#pos-payment-form').submit();
            }
            else{
                bootbox.alert("Paid amount is less than the payable amount.");
            }  
        });

        $(document).on('click', '#submit-sale', function () {
           
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

      

        
    });

    $(document).on('click', '.cancel_bill', function(e) {
        e.preventDefault();
        var cancel_id = $(this).siblings('.cancel_bill_id').val();
        bootbox.confirm(lang.r_u_sure, function(result) {
        if(result == true) {
                $("#sale_id").val('');
                $("#sale_id").val(cancel_id);
                $('#remarks').val(''); 
                $('#CancelorderModal').show();
            }
        });
        return false;
    });

$(document).on('click','#cancel_orderitem',function(){
     var cancel_remarks = $('#remarks').val(); 
     var sale_id = $('#sale_id').val(); 
     if($.trim(cancel_remarks) != ''){
        
        $.ajax({
            type: "get",
            url:"<?=admin_url('pos/cancel_sale');?>",                
            data: {cancel_remarks: cancel_remarks, sale_id: sale_id},
            dataType: "json",
            success: function (data) {
                if(data.msg == 'success'){
                         $('#CancelorderModal').hide(); 
                         location.reload();
                }else{
                    bootbox.alert('<?=lang('uanble_to_cancle');?>');
                    return false;
                }
            }    
        }).done(function () {
          
        });
     }   
});

$(document).on('click', '.closemodal', function () {
    $('#remarks').val('');
    $('#sale_id').val('');
    $('#CancelorderModal').hide(); 
});

   // $(document).ready(function () {
       // $(document).on('click', '.print_bill', function () {
		 function requestBill(billid){

            var base_url = '<?php echo base_url(); ?>';
            //var billid = $(this).val(); 
			
			//var billid = $(this).val(); 
			//alert(billid);
            if (billid != '') {
                $.ajax({
                    type: 'get',
                    async: false,                    
                    ContentType: "application/json",
                    url: '<?=admin_url('pos/gatdata_print_billing');?>',
                    dataType: "json",
                    data: {
                        billid: billid
                    },
                    success: function (data) {
                    if (data != '') {      
                     var bill_totals = '';
                       var bill_head ='' ;

                         bill_head += (data.biller.logo != "test") ? '<div id="wrapper1"><div id="receiptData"><div id="receipt-datareceipt-data"><div class="text-center"><img  src='+base_url+'assets/uploads/logos/'+data.biller.logo +' alt="" >': "";

                         bill_head += '<h3 style="text-transform:uppercase;">'+data.biller.company+'</h3>';
						
                         bill_head += '<p>'+data.biller.address+"  "+data.biller.city+" "+data.biller.postal_code+"  "+data.biller.state+"  "+data.biller.country+'<br>'+'<?= lang('tel'); ?>'+': '+data.biller.phone+'</p></div>';
						
                         bill_head += '<p>'+'<?= lang('bill_no'); ?>'+': '+data.billdata.bill_number+'<br>'+'<?= lang('date'); ?>'+': '+data.inv.created_on+'<br>'+'<?= lang('sale_no_ref'); ?>'+': '+data.inv.reference_no+'<br>'+'<?= lang('sales_person'); ?>'+': '+data.created_by.first_name+' '+data.created_by.last_name+'<br>'+'<?= lang('cashier'); ?>'+': '+data.cashier.first_name+' '+data.cashier.last_name;
			 
			 if(data.billdata.order_type==1){
			    bill_head +='<br>'+'<?= lang('Table'); ?>'+': '+data.billdata.table_name;
			 }else{
			 
			 }
			 bill_head += '</p>';
                         bill_head += '<p>'+'<?= lang('customer'); ?>'+': '+data.customer.name+'</p>';
						 
                            if(typeof data.delivery_person  != "undefined"){
							 bill_head += '<p>'+'<?= lang('phone'); ?>'+': '+data.customer.phone+'</p>';
							 
							  bill_head += '<p>'+'<?= lang('delivery_address'); ?>'+': </p>';
							  bill_head += '<address>'+data.customer.address+' <br>'+data.customer.city+' '+data.customer.state+' '+data.customer.country+'<br>Pincode : '+data.customer.postal_code+'</address>';
							  
							  
							  bill_head += '<p>'+'<?= lang('delivery_person'); ?>'+': '+data.delivery_person.first_name+' '+data.delivery_person.last_name+' ('+data.delivery_person.user_number+')</p>';
							  bill_head += '<p>'+'<?= lang('phone'); ?>'+': '+data.delivery_person.phone+'</p>';
							 }

                         bill_totals += '<table class="table table-striped table-condensed"><thead><th colspan="2">'+'<?=lang("description");?>'+'</th><th>'+'<?=lang("price");?>'+'</th><th>'+'<?=lang("qty");?>'+'</th><th class="text-right">'+'<?=lang("sub_total");?>'+'</th></thead>';

                            var r =0;
							
                           $.each(data.billitemdata, function(a,b) {
							  
                            r++;
							var recipe_name;
							<?php
							if($this->Settings->user_language == 'khmer'){
								
							?>
							if(b.khmer_name != ''){
								recipe_name = b.khmer_name;
							}else{
								recipe_name = b.recipe_name;
							}
							<?php
							}else{
							?>
							recipe_name = b.recipe_name;
							<?php

							}
							?>
							var recipe_variant='';
                            if(b.recipe_variant!=''){                                
                                recipe_variant = ' - ['+b.recipe_variant+']';
                            }else{                                
                                recipe_variant='';
                            }

                                bill_totals += '<tbody><tr><td colspan="2" class="no-border">'+r+': &nbsp;&nbsp'+ recipe_name+'' +recipe_variant+'</td><td class="no-border">'+ formatMoney(b.net_unit_price) +'</td><td class="no-border">'+ formatDecimal(b.quantity) +'</td><td class="no-border text-right">'+ formatMoney(b.subtotal) +'</td></tr></tbody>';
                            });


							 bill_totals += '<tfoot><tr class="bold"><th colspan="4" class="text-right">'+'<?=lang("items");?>'+'</th><th  class="text-right">'+formatDecimal(data.billdata.total_items)+'</th></tr>';
							 
							 
                            bill_totals += '<tr class="bold"><th colspan="4" class="text-right">'+'<?=lang("total");?>'+'</th><th   class="text-right">'+formatMoney(data.billdata.total)+'</th></tr>';
							

                            if(data.billdata.total_discount > 0) {
									if(data.billdata.discount_type == 'manual'){
										bill_totals += '<tr class="bold"><th class="text-right" colspan="4">'+lang.discount+'('+data.billdata.discount_val+')</th><th   class="text-right">'+formatMoney(data.billdata.total_discount)+'</th></tr>';
									} else {
                                    bill_totals += '<tr class="bold"><th class="text-right" colspan="4">'+data.discount+'</th><th   class="text-right">'+formatMoney(data.billdata.total_discount)+'</th></tr>';
									}
                                }
								
							
							//if (data.billdata.tax_rate != 0) {
                                    //bill_totals += '<tr class="bold"><th colspan="4" class="text-right" >Tax ('+data.billdata.tax_name+') </th><th    class="text-right">'+formatMoney(data.billdata.total_tax)+'</th></tr>';
				    $taxtype = '<?=lang('tax_exclusive')?> '+ data.billdata.tax_name;
				    if(data.billdata.tax_type==0){
				    $taxtype = '<?=lang('tax_inclusive')?> '+data.billdata.tax_name;
				    }
									//bill_totals += '<tr class="bold"><th colspan="5" class="text-right" >'+$taxtype+'  </th></tr>';
                               // }
				
                           $grandTotal =data.billdata.grand_total;
			   if(data.billdata.tax_type==0){
			    $grandTotal = parseFloat(data.billdata.grand_total) + parseFloat(data.billdata.total_tax);
			   }
                            bill_totals += '<tr class="bold"><th colspan="4" class="text-right" ><span class="pull-left">'+$taxtype+'</span>'+lang.grand_total+'</th><th colspan="2"  class="text-right">'+formatMoney($grandTotal)+'</th></tr></tfoot></table>';
						
                                $('#bill_header').empty();
                                $('#bill_header').append(bill_head);

                                $('#bill-total-table').empty();                                
                                $('#bill-total-table').append(bill_totals);
								<?php if($pos_settings->remote_printing == 1){?>
                                Popup($('#bill_tbl').html());  
								<?php }else{?>
									printOrder(data);						
								<?php }?>
                            }
                    } 
                });
            }
		 }
       // });
   // });

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
<script type="text/javascript" src="<?=$assets?>pos/js/pos.bills.js"></script>
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
		maxLength: 10,
        display: {
            'b': '\u2190:Backspace',
        },
        customLayout: {
            'default': [
            '1 2 3 4',
            '5 6 7 8  ',
            ' 9 0 . {b}',

            ' {accept} {cancel}'
            ]
        }
    });
</script>
<script>
$('.kb-text').keyboard({
        autoAccept: true,
        alwaysOpen: false,
        openOn: 'focus',
        usePreview: false,
        layout: 'custom',
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
        function printOrder(billData) {console.log(billData);
            if (socket.readyState == 1) {
                if (order_printers == '') {

                    var socket_data = { 'printer': false, 'order': true,
                    'logo': (billData.biller && billData.biller.logo ? site.base_url+'assets/uploads/logos/'+billData.biller.logo : ''),
                    'text': order_data };
                    socket.send(JSON.stringify({type: 'print-receipt', data: socket_data}));

                } else {

                $.each(order_printers, function() {
					var bill_header = billData.biller.address+"  "+billData.biller.city+" "+billData.biller.postal_code+"  "+billData.biller.state+"  "+billData.biller.country+"\n";
					bill_header += '<?= lang('tel'); ?>'+': '+billData.biller.phone;
	/*			
	 bill_head += '<p>'+'<?= lang('bill_no'); ?>'+': '+data.billdata.bill_number+'<br>'+'<?= lang('date'); ?>'+': '+data.inv.date+'<br>'+'<?= lang('sale_no_ref'); ?>'+': '+data.inv.reference_no+'<br>'+'<?= lang('sales_person'); ?>'+': '+data.created_by.first_name+' '+data.created_by.last_name;
			 
			 if(data.billdata.order_type==1){
			    bill_head +='<br>'+'<?= lang('Table'); ?>'+': '+data.billdata.table_name;
			 }else{
			 
			 }
			 bill_head += '</p>';
                         bill_head += '<p>'+'<?= lang('customer'); ?>'+': '+data.customer.name+'</p>';
						 
                            if(typeof data.delivery_person  != "undefined"){
							 bill_head += '<p>'+'<?= lang('phone'); ?>'+': '+data.customer.phone+'</p>';
							 
							  bill_head += '<p>'+'<?= lang('delivery_address'); ?>'+': </p>';
							  bill_head += '<address>'+data.customer.address+' <br>'+data.customer.city+' '+data.customer.state+' '+data.customer.country+'<br>Pincode : '+data.customer.postal_code+'</address>';
							  
							  
							  bill_head += '<p>'+'<?= lang('delivery_person'); ?>'+': '+data.delivery_person.first_name+' '+data.delivery_person.last_name+' ('+data.delivery_person.user_number+')</p>';
							  bill_head += '<p>'+'<?= lang('phone'); ?>'+': '+data.delivery_person.phone+'</p>';
							 }*/
				
					
					
					
					var bill_info = '<?= lang('bill_no'); ?>'+': '+billData.billdata.bill_number+"\n";
					bill_info += '<?= lang('date'); ?>'+': '+billData.inv.date+"\n";
					bill_info += '<?= lang('sale_no_ref'); ?>'+': '+billData.inv.reference_no+"\n";
					bill_info += '<?= lang('sales_person'); ?>'+': '+billData.created_by.first_name+' '+billData.created_by.last_name+"\n";
					
					var bill_items = "Description         Price   Qty  Sub Total\n";
					var r =1;
					$.each(billData.billitemdata, function(a,b) {
							  
							var recipename;
							
							recipename = b.recipe_name;
								bill_items += printLine(recipe_name(addslashes(r+" "+ recipename), 15)+ "  "+ (formatMoney(b.net_unit_price) + " " +formatDecimal(b.quantity) + " "+formatMoney(b.subtotal)),45,'')+"\n";
                                //bill_items += printLine(recipe_name(addslashes("#".$r." ".$row->recipe_name), 15)."  ".($this->sma->formatMoney($row->net_unit_price)." ".($row->quantity)." ".($this->sma->formatMoney($row->subtotal))), $char_per_line, ' '); ?>" + "\n";
								//bill_items += r+' &nbsp;&nbsp'+ recipe_name+  formatMoney(b.net_unit_price) + formatDecimal(b.quantity) + formatMoney(b.subtotal) ;
								r++;
							});
					
					var bill_totals = '<tfoot><tr class="bold"><th colspan="4" class="text-right">'+'<?=lang("items");?>'+'</th><th  class="text-right">'+formatDecimal(billData.billdata.total_items)+'</th></tr>';
							 
							 
                            bill_totals += '<tr class="bold"><th colspan="4" class="text-right">'+'<?=lang("total");?>'+'</th><th   class="text-right">'+formatMoney(billData.billdata.total)+'</th></tr>';
							

                            if(billData.billdata.total_discount > 0) {
                                    bill_totals += '<tr class="bold"><th class="text-right" colspan="4">'+lang.discount+'</th><th   class="text-right">'+formatMoney(billData.billdata.total_discount)+'</th></tr>';
                                }
								
							<?php if($pos_settings->display_tax==1) : ?>
							if (billData.billdata.tax_rate != 0) {
                                    //bill_totals += '<tr class="bold"><th colspan="4" class="text-right" >Tax ('+data.billdata.tax_name+') </th><th    class="text-right">'+formatMoney(data.billdata.total_tax)+'</th></tr>';
				    $taxtype = '<?=lang('tax_exclusive')?> '+ billData.billdata.tax_name;
				    if(billData.billdata.tax_type==0){
				    $taxtype = '<?=lang('tax_inclusive')?> '+billData.billdata.tax_name;
				    }
									bill_totals += '<tr class="bold"><th colspan="5" class="text-right" >'+$taxtype+'  </th></tr>';
                                }
				<?php endif; ?>
					
					var order_data = {
						'store_name':billData.billdata.biller,
						'header': bill_header,
						'info':bill_info,
						'items':bill_items,
						'totals':bill_totals
					};
                    var socket_data = { 'printer': this,
                    'logo': (billData.biller && billData.biller.logo ? site.base_url+'assets/uploads/logos/'+billData.biller.logo : ''),
                    'text': order_data };
                    socket.send(JSON.stringify({type: 'print-receipt-data', data: socket_data}));
                });

            }
                return false;
            } else {
                bootbox.alert('<?= lang('pos_print_error'); ?>');
                return false;
            }
        }
		function addslashes(string) {
			return string.replace(/\\/g, '\\\\').
				replace(/\u0008/g, '\\b').
				replace(/\t/g, '\\t').
				replace(/\n/g, '\\n').
				replace(/\f/g, '\\f').
				replace(/\r/g, '\\r').
				replace(/'/g, '\\\'').
				replace(/"/g, '\\"');
		}
		function printLine(str) {
			var size = pos_settings.char_per_line;
			var len = str.length;
			var res = str.split(":");
			var newd = res[0];
			for(i=1; i<(size-len); i++) {
				newd += " ";
			}
			newd += (res[1])?res[1]:'';
			return newd;
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

<script type="text/javascript" charset="UTF-8"><?=$s2_file_date?></script>
<?php
if (isset($print) && !empty($print)) {
    /* include FCPATH.'themes'.DIRECTORY_SEPARATOR.$Settings->theme.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'pos'.DIRECTORY_SEPARATOR.'remote_printing.php'; */
    include 'remote_printing.php';
}
?>




</body>
</html>
