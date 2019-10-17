<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?><!DOCTYPE html>
<?php      
    $default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);   
?>  

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
  @page  
        { 
            size: auto;   /* auto is the initial value */ 
            /* this affects the margin in the printer settings */ 
            margin: -5mm 5mm 5mm 5mm;  
        } 

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
/*	mani*/
	.ui-keyboard{height: 100px;}
	#paymentModal .modal-header{background-color: #1A2127!important;}
	#paymentModal .modal-header h4{text-align: center;font-size: 20px;color: #fff;font-weight: normal;text-transform: capitalize;}
	#paymentModal .modal-header button{opacity: 1;color: #fff;}
	.ui-keyboard div{max-width: 400px;    margin-left: 68%;    margin-top: -33%;    box-shadow: 0px 0px 2px rgba(50, 50, 50, 0.50);}
	.table tbody tr td{border: none;}
	.form-group label{float: left;}
	.bill_sec_head{padding: 15px 0px;}
	.bill_sec_head label{float: none;text-align: center;font-size: 20px;text-transform: capitalize;}
    .payment_type{border-radius: 5px;}
	.paid_payments{border-radius: 5px;}
	#bill_amount{font-size: 30px;font-weight: bold;}
	.payment_type .active, .btn-prni.active{border: 1px solid #1F73BB;box-shadow: none;transition: all 0.2s ease-in;}
	#paymentModal .modal-footer{border: none;}
    /*#myModal{ display: none !important; }*/
/*    .ui-keyboard .ui-keyboard-keyset{
    max-width: 700px;
    margin-left: 0;
    margin-top: 14px;
    margin: -14% auto 0px;
}*/
	@media (max-width: 1366px) and (min-width: 1362px){
			.ui-keyboard{height: 10px;}
		.ui-keyboard div {max-width: 300px;margin-left: 71%;margin-top: -37%;}
		.clearfix{height: 5px;}
		.CC_CC2,.lc_loyalty4 {
    position: relative;
    float: left;
    width: 100%;
}
	}
/*	end mani*/
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
		type: type_id,table:'<?=$_GET['table']?>'
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
?>`



<div class="modal fade in" id="paymentModal" tabindex="-1" data-backdrop="static"   data-keyboard="false" role="dialog" aria-labelledby="payModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true"><i
                            class="fa fa-2x">&times;</i></span><span class="sr-only"><?=lang('close');?></span></button>
		<h4 class="modal-title" id="payment-customer-name"></h4>
		<h4 class="modal-title" id="payModalLabel"><?=lang('make_payment');?>(<span id="new_customer_name"></span>)</h4>
            <div style="position: absolute; <?=$Settings->user_rtl ? 'right:-9999px;' : 'left:-9999px;';?>"><?php echo form_input('test', '', 'id="test" class="kb-pad"'); ?></div>

            </div>
            <div class="modal-body" id="payment_content" >
                <div class="btn btn-warning pull-right">
                    <a href="<?=admin_url('customers/new_customer');?>" id="add-customer" class="external" data-toggle="modal" data-target="#myModal">
                        Add New Customer <i class="fa fa-plus-circle" id="addIcon" style="font-size: 1.5em; "></i>
                    </a>
                </div>
                
             <?php $attrib = array( 'autocomplete'=>"off" ,'role' => 'form', 'id' => 'pos-payment-form');
             echo admin_form_open("pos/paymant", $attrib);
             $type = $this->input->get('type');
             ?>
                <div class="row">
                    <?php if ($pos_settings->taxation_report_settings == 1) { ?>
                       <div class="form-group" style="padding: 5px 10px;">
                            <div class="row">
                                <div class="col-sm-6 taxation_settings">
                                    <label class="control-label" for="taxation_settings"><?= lang("print_option"); ?></label>
                                    <input type="radio" value="0" class="checkbox" name="taxation" checked ="checked">
                                    <label for="switch_left">Print</label>
                                    <input type="radio" value="1" class="checkbox" name="taxation">
                                    <label for="switch_right">Don't Print</label>                    
                                </div>
                            </div>
                        </div>
                    <?php  } ?>
                <input type="hidden" name="type" class="type" value="<?php echo $type;?>"/>
                <input type="hidden" name="balance_amount" class="balance_amount" value=""/>
                <input type="hidden" name="due_amount" class="due_amount" value=""/>
                    <div class="col-md-12 col-sm-12 text-center">
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
                        <input type="hidden" name="new_customer_id" id="new_customer_id" value="0">
                        <input type="hidden" name="eligibity_point" id="eligibity_point" value="<?= $eligibity_point ?>">
                        <input type="hidden" name="bill_id" id="bill_id" class="bill_id" />
                        <input type="hidden" name="order_split_id" id="order_split_id" class="order_split_id" />
                        <input type="hidden" name="sales_id" id="sales_id" class="sales_id" />
            			<input type="hidden" name="credit_limit" id="credit_limit" class="credit_limit" />
            			<input type="hidden" name="company_id" id="company_id" class="company_id" />
            			<input type="hidden" name="customer_type" id="customer_type" class="customer_type" />
                        <input type="hidden" name="customer_id" id="customer_id" class="customer_id"/>
            			
                        <input type="hidden" name="total" id="total" class="total" />
                        <input type="hidden" name="loyalty_available" id="loyaltyavailable" class="loyaltyavailable" />
                        <input type="hidden" name="loyaltypoints" id="loyaltypoints" class="loyaltypoints" />
                        <input type="hidden" name="loyalty_used_points" id="loyalty_used_points" class="loyalty_used_points" />
                        <div class="form-group bill_sec_head" style="color: #1F73BB!important;font-size: 20px!important;align-self: center;margin-bottom: 5px;">
                            <button type="button" class="btn btn-danger" id="reset" style="cursor: pointer!important;"><label style="margin-top: 0px !important;"><?=lang('reset')?> </label></button>
                               <?=lang("bill_amount", "bill_amount");?>
                               <?php 
                               $default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
                               ?>
                               <span id="bill_amount" >&#x20b9;</span>
                        </div>
                        <div id="payment-list">
                           <?php   
                           $j =0;
                            $paymentMethods = $this->site->getAllPaymentMethods();                                                         
                                foreach ($paymentMethods as $k => $method) { 
                                    $j++;
                                      echo "<button id=\"payment-" . $method->payment_type . "\" type=\"button\" value='" . $method->payment_type . "' class=\"btn-prni payment_type \" data-index='" . $method->payment_type. "' data_id='" . $j. "' ><span>" . $method->display_name . "</span></button>";
                                ?>
                                     <input name="paid_by[]" type="hidden" id="payment_type_<?php echo $method->payment_type; ?>" value="<?php echo $method->payment_type; ?>" class="form-control" autocomplete="off"  />
                            <?php } ?>
                            <div id="sub_items" style="margin-top: 30px;min-height: 165px;">

                               

                                <div class="form-group col-md-6">
                                    <!-- <label><?=lang('customer_name','customer_name')?></label> -->
                                     <input readonly type="hidden" id="loyalty_card_customer_name"  readonly="" class="pa form-control loyalty_card_customer_name"  autocomplete="off" />
                                </div> 
                                <div class="clearfix"></div>


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
                                   <div class="col-sm-12 <?=$method->payment_type?>">
                                    <!-- <span style="color: green;font-size: 20px;"><?=$method->payment_type; ?></span> -->
                                    <?php if($method->payment_type=="loyalty") : ?>
				    <div class="form-group col-md-6">
					<label><?=lang('search_loyalty_customer','search_loyalty_customer')?></label>
					<?php
					    echo form_input('loyalty_customer', (isset($_POST['loyalty_customer']) ? $_POST['loyalty_customer'] : ""), 'id="loyalty_customer" data-placeholder="' . $this->lang->line("select") . ' ' . $this->lang->line("loyalty_customer") . '" required="required" class="form-control pos-input-tip" autocomplete="off" style="width:100%;"');
					?>
				    </div>
				    <?php endif; ?>
				    <div class="col-sm-6 CC_<?=$method->payment_type.$j?>" id="CC_<?=$method->payment_type?>" style="display: none">
                                         <div class="form-group col-md-12">
                                                <label><?=lang('card_no')?> </label>
								   			<input name="cc_no[]" type="text" maxlength="20" id="pcc_no_<?=$method->payment_type?>"
                                                    class="form-control kb_pad_length cc_no" placeholder="<?=lang('cc_no')?>"/>
                                        </div> 
                                        <div class="form-group col-md-12">
											<label><?=lang('card_exp_date')?> </label>
										   	<input name="card_exp_date[]" type="text" maxlength="6" value ="" id="card_exp_date_<?=$method->payment_type?>"
                                                    class="form-control kb_pad_exp crd_exp" placeholder="MMYYYY"/>
                                        </div> 
                                        <div class="clearfix"></div>                                        
                                    </div>  
				    <?php
                                    foreach($currency as $currency_row){
                                        
                                        $default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
                                        
                                        if($currency_row->code == $default_currency_data->code){
                                    ?>                                    
                                    <div class="col-sm-6 base_currency_<?=$method->payment_type.$j?>" id="base_currency_<?=$method->payment_type?>" style="display:<?php echo $display;?>; "> 
                                        <div class="form-group"  style="margin-bottom: 5px;">
                                            <label><?=lang('amount')?> <?=$currency_row->code; ?></label>
                                            <input name="amount_<?php echo $default_currency_data->code; ?>[]" type="text" id="amount_<?php echo $default_currency_data->code; ?>_<?=$method->payment_type?>" data-rate="<?=$currency_row->rate; ?>" data-code="<?=$currency_row->code; ?>" data-id="<?=$currency_row->id; ?>"  class="pa form-control  amount kb-pad amount_base" payment-type="<?=$method->payment_type?>" autocomplete="off"  />
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
                                        <div class="form-group col-md-6">
                                            <label><?=lang('Points')?></label>
                                             <input name="paying_loyalty_points[]" type="text" id="loyalty_points_<?=$method->payment_type?>" idd="<?=$method->payment_type?>" class="pa form-control loyalty_points kb-pad"  autocomplete="off" />
                                        </div> 

                                        <div class="clearfix"></div>       
                                        <div id="lc_details_<?=$method->payment_type?>" style="color: red;"> </div>
                                        <div id="lc_reduem_<?=$method->payment_type?>" style="color: green;"></div>
                                    </div>
                                    <div class="clearfix"></div>
                                    <!-- <div class="col-sm-6 CC_<?=$method->payment_type.$j?>" id="CC_<?=$method->payment_type?>" style="display: none">
                                         <div class="form-group col-md-6">
                                                <label><?=lang('card_no')?> </label>
								   			<input name="cc_no[]" type="text" id="pcc_no_<?=$method->payment_type?>"
                                                    class="form-control kb-pad" placeholder="<?=lang('cc_no')?>"/>
                                        </div> 
                                        <div class="form-group col-md-6">
											<label><?=lang('card_exp_date')?> </label>
										   	<input name="card_exp_date[]" type="text" id="card_exp_date_<?=$method->payment_type?>"
                                                    class="form-control kb-pad" placeholder="<?=lang('card_exp_date')?>"/>
                                        </div> 
                                        <div class="clearfix"></div>                                        
                                    </div>  --> 
                                    <div style="margin-bottom: 10px"></div>
                                </div>                                  
                                <?php  } ?> 
                                </div>
                        </div>  
                                <div class="form-group" style="margin-bottom: 5px;">
                                    <div id="userd_tender_list">         
                                    </div>
                                </div>
                                <div class="col-md-12 col-sm-12">
                                <div class="form-group total_pay"  style="margin-bottom: 5px;">
                                    <table class="table table-condensed table-striped" style="margin-bottom: 0;">
                                        <tr>
                                            <td rowspan="4" class="total_paytd" style="width: 50%!important;text-align: center">
                                                &nbsp;<?=lang('total_pay')?>
                                            </td> 
                                        </tr>
                                         <?php
                                            foreach($currency as $currency_row) { ?>
                                               <tr><td class="total_paytd" style="text-align: left;"><?php echo $currency_row->code; ?>&nbsp;:&nbsp;&nbsp;<span id="twt_<?php echo $currency_row->code; ?>">0.00</span>
                                                <input type="hidden" id="paid_amt_<?php echo $currency_row->code; ?>"></td></tr>
                                         <?php } ?>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-12 col-sm-12">   
                                <div class="form-group balance_pay"  style="margin-bottom: 5px;">
                                    <table class="table  table-condensed table-striped" style="margin-bottom: 0;">
                                        <tr>
                                            <td rowspan="4" class="balance_paytd" style="width: 50%!important;text-align: center">&nbsp;<?=lang('balance_pay')?></td> 
                                        </tr>
                                         <?php
                                            foreach($currency as $currency_row) { ?>
                                               <tr><td class="balance_paytd" style="text-align: left;"><?php echo $currency_row->code; ?>&nbsp;:&nbsp;&nbsp;<span id="balance_<?php echo $currency_row->code; ?>">0.00</span>
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
             <?php                
             echo form_hidden('remove_image','No');
             echo form_hidden('action', 'PAYMENT-SUBMIT');
             echo form_close();
             ?>
            <div class="modal-footer">
                <button type="button" class="btn btn-block btn-lg btn btn-info" id="submit-sale1"><?=lang('send');?></button>
            </div>
        </div>
    </div>
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

<!-- <div class="modal fade in" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true"></div>
<div class="modal fade in" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2"
     aria-hidden="true"></div>
<div id="modal-loading" style="display: none;">
    <div class="blackbg"></div>
    <div class="loader"></div>
</div> -->

<div class="modal fade in" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true" style="z-index:9999999999" data-backdrop="static" data-keyboard="false" >
     
     
     
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


var site = <?=json_encode(array('url' => base_url(), 'base_url' => admin_url('/'), 'assets' => $assets, 'settings' => $Settings,'pos_settings' => $pos_settings, 'dateFormats' => $dateFormats)) ?>;

 var KB = <?=$pos_settings->keyboard?>;

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

<!-- new payment screen  start -->
<script type="text/javascript">

    $('#paymentModal').on('shown.bs.modal', function(e) {
        $('#userd_tender_list').html('');
        var loyalty_available = $('.loyaltyavailable').val();          
        /*$('#payment-loyalty').prop('disabled', false);
        if(loyalty_available == 0)
        {            
            $('#payment-loyalty').prop('disabled', true).css('opacity',0.5);
        }
        else{
            $('#payment-loyalty').prop('disabled', false);
        }*/
        // $("button.payment_type").val("cash").click();
        $('#payment-cash').val('cash');
	
//        if($('#payment-cash').val() == 'cash'){
//            $('#payment-cash').trigger('click');                  
//            $('#payment-cash').addClass('active');   
//        }
//	if (rt_cc!='' && rt_cc!=undefined) {
//	    $('#payment-CC').trigger('click');                  
//            $('#payment-CC').addClass('active');   
//	}
//	if (rt_credit!='' && rt_credit!=undefined) {
//	    $('#payment-credit').trigger('click');                  
//            $('#payment-credit').addClass('active');   
//	}
	if (rt_loyalty!='' && rt_loyalty!=undefined) {
	    $('#payment-loyalty').trigger('click');                  
            $('#payment-loyalty').addClass('active');
	    
	}
	if (rt_credit!='' && rt_credit!=undefined) {
	    $('#payment-credit').trigger('click');                  
            $('#payment-credit').addClass('active');   
	}
	if($('#payment-cash').val() == 'cash'){
            $('#payment-cash').trigger('click');                  
            $('#payment-cash').addClass('active');   
        }
	if (rt_cc!='' && rt_cc!=undefined) {
	    $('#payment-CC').trigger('click');                  
            $('#payment-CC').addClass('active');   
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
$(document).on('click', '#reset', function () {    
      $('#userd_tender_list').html('');
      $('.crd_exp,.cc_no').val('');      
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
        
                /*$('#amount_'+$index).val('');
                $('#amount_USD_'+$index).val('');*/
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

                $('#amount_<?php echo $currency_row->code; ?>_'+$index).focus();
                $('#amount_<?php echo $currency_row->code; ?>_'+$index).attr('readonly', true);
                $('#loyalty_points_' + $index).focus();
                $('#loyaltypoints').val(0);                
                $('#lc_details_' + $index).html(''); 
                $('#lc_reduem_' + $index).html('');

                var loyalty_customer_id = $('#loyalty_customer').val();
                // alert(loyalty_customer_id);

                var customer_id ='';
                var bill_customer_id = $('.customer_id').val(); 
                if(loyalty_customer_id){
                    customer_id = loyalty_customer_id;
                }else{
                    customer_id =  bill_customer_id;
                }
                // alert(bill_customer_id);
                var payid = $(this).attr('id'),
                    id = payid.substr(payid.length - 1);
                if (customer_id != '') {
                    $.ajax({
                        type: "get", async: false,
                        url: site.base_url + "pos/get_loyalty_points/" + customer_id,
                        dataType: "json",
                        success: function (data) { 

                            if (data.points === false && data.redemption === 0) {     
                            
                                $('#loyalty_points_' + $index).parent('.form-group').addClass('has-error');
                                bootbox.alert('Not Eligible To use Loyalty Card.');
                            } else if ((data.points.total_points == 0) || (data.points.loyalty_card_no == '')) { 
                                
                                bootbox.alert('Right Now Not Eligible to Loyalty,Please try after some visit.');
                                ('#lc_details_' + $index).html(''); 
                                $('#lc_reduem_' + $index).html(''); 
                            } else {          
                                                          
                                $('#loyaltypoints').val(data.points.total_points);
                                $('#lc_details_' + $index).html('<small>Card No: ' + data.points.loyalty_card_no + '&nbsp;&nbsp;Value: ' + data.points.total_points +'</small>'); 

                                $('#lc_reduem_' + $index).html('<small>Redemption: ' + parseFloat(data.redemption.redempoint) + '&nbsp;&nbsp;Amount: ' + parseFloat(data.redemption.amount) +'</small>'); 

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
                });
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
                $(this).parent('.form-group').append('<span class="available-c-limit">Available Customer Credit Limit $'+$('#credit_limit').val()+'</span>');
            }

            $('#amount_<?=$default_currency_data->code?>_'+$index).removeClass('creditcard-max');            
            if ($( this ).val()=='CC') {                
                    $('#amount_<?=$default_currency_data->code?>_'+$index).addClass('creditcard-max');
                    $inputCreditcard = 0;

                    $('.creditcard-max').each(function(n,v){
                        if($(this).attr('id')!="amount_<?=$default_currency_data->code?>_"+$index){
                            $inputCreditcard += ($(this).val()=='')?0:parseFloat($(this).val());
                        }
                    });                
            }

            console.log($amount);
                // $('#amount_USD_cash').val('');
		
                if ((p_val != 'loyalty') && (p_val == 'cash')) {                    
                  if($amount>0){                    
                     if($('#amount_<?=$default_currency_data->code?>_cash').val() == ''){
			if (rt_cash!='' && rt_cash!=undefined) {
			    $amount = rt_cash;
                      }
		      $('#amount_<?=$default_currency_data->code?>_cash').val($amount);
                    }
		  }
                }else if ((p_val != 'loyalty') && (p_val == 'CC')) {                    
                  if($amount>0){
		    if (rt_cc!='' && rt_cc!=undefined) {
			    $amount = rt_cc;
		    }
                     if($('#amount_<?=$default_currency_data->code?>_CC').val() == ''){
			if (rt_cc!='' && rt_cc!=undefined) {
			    $amount = rt_cc;
			   			   
			}
                         $('#amount_<?=$default_currency_data->code?>_CC').val($amount);
			
                      }
                    }
                }else if (p_val == 'loyalty'){
                    $('#loyalty_points_cash').focus();
		    if (rt_loyalty!='' && rt_loyalty!=undefined) {
			    $('#loyalty_points_loyalty').val(rt_loyalty);
			    $('.loyalty_points').trigger('change');
		    }
		    
                } else{
		    if (rt_credit!='' && rt_credit!=undefined) {
			    $('#amount_<?=$default_currency_data->code?>_credit').val(rt_credit);
			    
		    }
		}
		$('.amount').trigger('blur');
        });
 

            $(document).on('change', '.loyalty_points', function () {
            var loyaltypoints = $("#loyaltypoints").val();             
            var redemption = $(this).val() ? $(this).val() : 0;
            var customer_id = $("#customer_id").val();    
            $('#loyalty_used_points').val(0);             
            var payid = $(this).attr('idd'); 
            if(parseFloat(loyaltypoints) == 0){    
                 bootbox.alert('Gift card number is incorrect or expired.');    
                 $('#loyalty_points_' + payid).focus().val('');            
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
                            } else if(parseFloat(data.total_redemamount) > parseFloat($bal_amount)) {      
                                    bootbox.alert('Already seleted in other payment method Plz check it (OR) use only Blance amount only.');
                                    $('#lc_reduem_' + payid).html('<small>Redemption: ' + parseFloat(data.redemption) + '&nbsp;&nbsp;Amount: ' + parseFloat(data.amount) +'</small>');
                                    $('#loyalty_points_' + payid).focus().val('');
                                    $('#amount_<?php echo $currency_row->code; ?>_'+payid).val('');
                               }else{                                                                                  
                                    // $('#loyalty_points_' + id).parent('.form-group').removeClass('has-error');
                                    $('#lc_reduem_' + payid).html('<small>Redemption: ' + parseFloat(data.redemption) + '&nbsp;&nbsp;Amount: ' + parseFloat(data.amount) +'</small>'); 
                                    $('#loyalty_used_points').val(redemption); 
                                    $('#amount_<?php echo $currency_row->code; ?>_'+payid).focus().val(data.total_redemamount);
                                    $('#amount_<?php echo $currency_row->code; ?>_'+payid).attr('readonly', true);
                              }
                        }
                    });
                }else{
                    
                    bootbox.alert('Please Enter less than your points or equal.');  
                     $('#loyalty_points_' + payid).focus().val('');
                     $('#amount_<?php echo $currency_row->code; ?>_'+payid).val('');
                    
                }           
        });

        
        var customer_id = $('.customer_id').val();  
        $('#loyalty_customer').val(customer_id).select2({
            
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
                url: site.base_url + "pos/loyalty_customer",
                dataType: 'json',
                quietMillis: 15,
                data: function (term, page) {
                    return {
                        term: term,
                        limit: 1
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
            $('#loyalty_customer').on('select2-opening', function () {
                sct = '';
                $('.select2-input').addClass('kb-pad-all');  
                display_keyboards();       
                $('select, .select').select2('destroy');    
                // alert($(this).next().parent().parent().html());                    
               /*   $('input[name="default"]').addClass('kbtext');*/
                $('.select2-input').bind('change.keyboard', function (e, keyboard, el) {
                    
                    if (el && el.value != '' && el.value.length > 0 && sct != el.value) {
                        sct = el.value;
                    }
                    if(!el && sct.length > 0) {
                        $('.select2-input').addClass('select2-active');
                         // setTimeout(function() {
                            $.ajax({
                                type: "get",
                                async: false,
                                url: "<?=admin_url('pos/loyalty_customer')?>/?term=" + sct,
                                dataType: "json",
                                success: function (res) {
                                    if (res.results != null) {                                                          
                                        // $('#loyalty_customer').select2({data: res}).select2('open');
                                        $('.select2-input').removeClass('select2-active');
                                    } else {
                                         bootbox.alert('no_match_found');
                                        $('#loyalty_customer').select2('close');
                                        // $('#test').click();
                                    }
                                }
                            });
                         // }, 500);
                    }
                });
                

            });

            $('#loyalty_customer').on('select2-close', function () {
                $('.select2-input').removeClass('kb-pad-all');                
                $('#test').click();
                $('select, .select').select2('destroy');
                $('select, .select').select2({minimumResultsForSearch: 7});
            });
            $(document).bind('click', '#test', function () {
                var kb = $('#test').keyboard().getkeyboard();
                kb.close();
            });
        }    


        /*if($('.ui-keyboard-keyset').is(':visible')) {
            alert();
        }*/


        /*$(document).on('.ui-keyboard-keyset:visible', function () {
            alert();
        });*/
        
        // $("#loyalty_customer").on("change", function (e) {
            // $(document).on('change', '#loyalty_customer', function () {
        $(document).on('change',"#loyalty_customer", function () {
            var loyalty_customer_id = $(this).val();
            var myVar = $('.payment_type.active').val();               
            if(myVar =='loyalty'){

            var customer_id = loyalty_customer_id;  
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
                                bootbox.alert('Not Eligible To use Loyalty Points.');
                            } else if ((data.points.total_points == 0) || (data.points.loyalty_card_no == '')) {
                                bootbox.alert('Right Now Not Eligible to Loyalty,Please try after some visit.');
                                ('#lc_details_' + $index).html(''); 
                                $('#lc_reduem_' + $index).html('');
                            } else {
                                
                                $('#loyaltypoints').val(data.points.total_points);
                                $('#lc_details_' + $index).html('<small>Card No: ' + data.points.loyalty_card_no + '&nbsp;&nbsp;Value: ' + data.points.total_points +'</small>'); 
                                $('#lc_reduem_' + $index).html('<small>Redemption: ' + parseFloat(data.redemption.redempoint) + '&nbsp;&nbsp;Amount: ' + parseFloat(data.redemption.amount) +'</small>'); 
                                $('#loyalty_points_' + $index).parent('.form-group').removeClass('has-error');                                 
                            }
                        }
                    });               
               }
            }
        });        
        
        
</script>
<!-- new payment screen end  -->











<script type="text/javascript">
    var username = '<?=$this->session->userdata('username');?>', order_data = '', bill_data = '';
    var rt_cash = '';
	    var rt_credit ='';
	    var rt_cc ='';
	    var rt_loyalty='';
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
       
       /* $('#paymentModal').on('select2-close', '#paid_by_<?=$i?>', function (e) {
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
        });*/
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
        $('#paymentModal').css('overflow-y', 'scroll');
	    $('#paymentModal').appendTo("body").modal('show', {backdrop: 'static', keyboard: false});  
            var billid = $thisObj.siblings('.billid').val();                         
            var customer_type = $thisObj.siblings('.customer-type').val(); 
            var company_id = $thisObj.siblings('.company-id').val();
	    var allow_loyalty = $thisObj.siblings('.customer-allow-loyalty').val(); 
            var ordersplit = $thisObj.siblings('.order_split').val();
            var salesid = $thisObj.siblings('.salesid').val(); 
            var grandtotal = $thisObj.siblings('.grandtotal').val();
            var credit_limit = $thisObj.siblings('.credit-limit').val();
	    // rough tender start//
	    rt_cash = '';rt_credit='';rt_cc='';rt_loyalty='';
	    
	    rt_cash = $thisObj.siblings('.rt-cash').val(); 
            rt_credit = $thisObj.siblings('.rt-credit').val();
            rt_cc = $thisObj.siblings('.rt-CC').val();
	    rt_loyalty = $thisObj.siblings('.rt-loyalty').val();
	    // rough tender - end //
	    
			var customer_name = $thisObj.siblings('.customer-name').val();
	        $('#new_customer_name').text(customer_name);
			
	        console.log(credit_limit)
            var count = $thisObj.siblings('.totalitems').val(); 
            $('.bill_id').val(billid);
            $('.sales_id').val(salesid);
            $('.total').val(grandtotal);
            $('.order_split_id').val(ordersplit);
            $('.credit_limit').val(credit_limit);

            if(allow_loyalty==0){
	    $('#payment-loyalty').hide();
	    }
			
            // $('.loyalty_available').val(loyalty_available);
            $('.customer_type').val(customer_type);
	        $('.company_id').val(company_id);

            var twt = formatDecimal(grandtotal);
            $('#bill_amount').text(formatMoney(grandtotal));
	        console.log('grandtotal-'+grandtotal)
            console.log('bil-'+billid);
            if (count == 0) {
                bootbox.alert('<?=lang('x_total');?>');
                return false;
            }
			
			<?php
            $exchange_curr_code = $this->site->getExchangeCurrency($this->Settings->default_currency);
            $exchange_rate = $this->site->getExchangeRatey($this->Settings->default_currency);
            
			foreach($currency as $currency_row){
		      	$default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
        			if($currency_row->code == $default_currency_data->code){
        			?>
        			 gtotal_<?php echo $currency_row->code; ?> = (parseFloat(twt) / parseFloat(<?php echo $currency_row->rate; ?>)) * <?php echo $default_currency_data->rate; ?>;

                    var decimals = twt - Math.floor(twt);
                    decimals = decimals.toFixed(2);
                    var currency_id = <?php echo $currency_row->id;?>;
                    var  $exchange_curr_code = "<?php echo $exchange_curr_code;?>";                    
                    var  $exchange_rate = "<?php echo $exchange_rate;?>";                    
                    if($exchange_curr_code != '' && currency_id == "<?php echo $this->Settings->default_currency;?>"){
                        $decimals = decimals/ $exchange_rate;
                        $decimals =  Math.round($decimals / 100) * 100;
                         var $riel = '('+$exchange_curr_code+($decimals)+')';
                    }
                    else{
                        var $riel = '';
                    }

                    $('#twt_<?php echo $currency_row->code; ?>').text(formatMoney(gtotal_<?php echo $currency_row->code; ?>)+''+$riel);

        			 $('#paid_amt_<?php echo $currency_row->code; ?>').val(formatDecimal(gtotal_<?php echo $currency_row->code; ?>));
        			<?php
        			}else{ ?>
                var  $exchange_curr_code = "<?php echo $exchange_curr_code;?>"; 
    			gtotal_<?php echo $currency_row->code; ?> = (parseFloat(twt) / parseFloat(<?php echo $currency_row->rate; ?>)) * <?php echo $default_currency_data->rate; ?>;
                     $final_amt = twt/ <?php echo $currency_row->rate ?>;
                     $final_amt =  Math.round($final_amt / 100) * 100;
                    $amt =$exchange_curr_code+$final_amt;
                    
    			$('#twt_<?php echo $currency_row->code; ?>').text($amt);
                $('#paid_amt_<?php echo $currency_row->code; ?>').val(formatDecimal(gtotal_<?php echo $currency_row->code; ?>));
    			<?php } ?>
			<?php } ?>
			
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
        <?php if($pos_settings->discount_popup_screen_in_bill_print == 0) :?>
            requestBill(billid);
            return false;
        <?php endif; ?>  

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
		
		$(document).on('click', '.request_bil_new,.rough-tender-payment', function(){


		    /*$(".well-sm:not(:first)").remove();
            $('.close-payment').trigger('click');
            var pa = 1;
            var phtml = $('#payments').html(),
                    update_html = phtml.replace(/_1/g, '_' + pa);
                update_html= update_html.replace(/data-index="1"/g,'data-index="'+pa+'"');
                calculateTotals();
                pa--;*/
			
			$thisObj = $(this);            
			if ($thisObj.attr('data-item')=="rough-tender") {
			    $('#pos-payment-form').prepend('<input type="hidden" name="rough_tender" value=1 class="post-rough-tender">');
			    $('.taxation_settings').hide();                
                  <?php if($pos_settings->discount_popup_screen_in_rough_payment == 0) :?>
                    payment_popup($thisObj);
                    return false;
                <?php endif; ?>  

			}else{                
                $('.taxation_settings').show();
                $('.post-rough-tender').remove();
                <?php if($pos_settings->discount_popup_screen_in_payment == 0) :?>
                    payment_popup($thisObj);
                   return false;
               <?php endif; ?>  
			}
			
			var billid = $(this).parents('.payment-list-container').find('.billid').val();
			var ordersplit = $(this).parents('.payment-list-container').find('.order_split').val();
			var salesid = $(this).parents('.payment-list-container').find('.salesid').val(); 
            var grandtotal = $(this).parents('.payment-list-container').find('.grandtotal').val(); 
            var customer_id = $(this).parents('.payment-list-container').find('.customer-id').val();
			var loyalty_available = $(this).parents('.payment-list-container').find('.loyalty_available').val();
            $('.customer_id').val(customer_id);            
            $('.loyaltyavailable').val(loyalty_available);            
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
		
	    
        $('#paymentModal').on('show.bs.modal', function(e) {
            $('#submit-sale').text('<?=lang('submit');?>').attr('disabled', false);
        });
        $('#paymentModal').on('shown.bs.modal', function(e) {
                $("#loyalty_customer").val(null).trigger("change"); 

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
                $('#amount_<?php echo $currency_row->code; ?>_cash').val('');
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
        
        $(document).on('focus', '.amount', function () {            
			<?php $default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency); ?>
            pi_<?php echo $default_currency_data->code; ?> = $(this).attr('id');
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
                     paid_tenders += '<div type="button" class="btn-prni paid_payments" data-index="cash" data_id="1"><input type="hidden"  name="pname[]" value="'+$(inputs[i]).attr('payment-type')+'"><span style="padding-top:5px" id="userd_tender_'+$(inputs[i]).attr('payment-type')+'">'+$(inputs[i]).attr('payment-type')+' - '+$(inputs[i]).val()+'</span></div>';
                    } else if($(inputs[i]).val() === 0){      
                           $('#userd_tender_'+$(inputs[i]).attr('payment-type')).remove();
                    }
            } 
            $('#userd_tender_list').append(paid_tenders);
            calculateTotals();
        });

/*var arr = $('input[name="pname[]"]').map(function () {
                return this.value; 
            }).get();
var paid_tenders = '';

$(".amount_base").on("blur", function(){
    var sum=0;
    $(".amount_base").each(function(){
        if($(this).val() == 0){
            $('#used_tender_type_'+$(this).attr('payment-type')).remove();
        }else if(($.inArray($(this).attr('payment-type'),arr)) !== -1 ){ 
            $('#userd_tender_'+$(this).attr('payment-type')).text($(this).attr('payment-type')+' - '+$(this).val());
        }else if($(this).val() != 0 && ($.inArray($(this).attr('payment-type'),arr)) === -1){
            paid_tenders += '<div type="button" class="btn-prni used_tender_type" id="used_tender_type_'+$(this).attr('payment-type')+'" data-index="cash" data_id="1"><input type="hidden"  name="pname[]" value="'+$(this).attr('payment-type')+'"><span style="padding-top:5px" id="userd_tender_'+$(this).attr('payment-type')+'">'+$(this).attr('payment-type')+' - '+$(this).val()+'</span></div>';
        }

    });
$('#userd_tender_list').append(paid_tenders);
    
});*/
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
            $exchange_curr_code = $this->site->getExchangeCurrency($this->Settings->default_currency);
            $exchange_rate = $this->site->getExchangeRatey($this->Settings->default_currency);

    		if($currency_row->code == $default_currency_data->code){ ?>

                var currency_id = <?php echo $currency_row->id;?>;
                var  $exchange_curr_code = "<?php echo $exchange_curr_code;?>"; 
                var  $exchange_rate = "<?php echo $exchange_rate;?>";

                var decimals = formatDecimal((total_paying - (gtotal_<?php echo $currency_row->code; ?> * <?php echo $currency_row->rate; ?>)));
                  decimals = Math.abs(decimals);
                    // decimals = decimals.toFixed(2);
                    decimals = decimals - Math.floor(decimals);
                    decimals = decimals.toFixed(2);

                    /*console.log((decimals));
                    console.log(decimals);*/
             if($exchange_curr_code != '' && currency_id == "<?php echo $this->Settings->default_currency;?>"){
                    $decimals = decimals/ $exchange_rate;
                    $decimals =  Math.round($decimals / 100) * 100;
                     var $riel = '('+$exchange_curr_code+($decimals)+')';
                }
                else{
                    var $riel = '';
                }

            $('#balance_<?php echo $currency_row->code; ?>').text(formatMoney(total_paying - (gtotal_<?php echo $currency_row->code; ?> * <?php echo $currency_row->rate; ?>))+''+$riel);

            
    		/*$('#balance_<?php echo $currency_row->code; ?>').text(formatMoney(total_paying - (gtotal_<?php echo $currency_row->code; ?> * <?php echo $currency_row->rate; ?>) ),'<?php echo $currency_row->symbol; ?>');*/

            $('#balance_amt_<?php echo $currency_row->code; ?>').val(formatDecimal(total_paying - (gtotal_<?php echo $currency_row->code; ?> * <?php echo $currency_row->rate; ?>) ),'<?php echo $currency_row->symbol; ?>');

    		$('.balance_amount').val(formatDecimal(total_paying - (gtotal_<?php echo $currency_row->code; ?> * <?php echo $currency_row->rate; ?>)),'<?php echo $currency_row->symbol; ?>');
    		<?php
    		}else{
                $getExchangesymbol = $this->site->getExchangeCurrency($this->Settings->default_currency);
    		?>
                var $exchange_curr_code = "<?php echo $exchange_curr_code;?>"; 
                    
                $bal_final_amt = (total_paying - (gtotal_<?php echo $currency_row->code; ?> * <?php echo $currency_row->rate; ?>))/ <?php echo $currency_row->rate ?>;
                $bal_final_amt =  Math.round($bal_final_amt / 100) * 100;
                $bal_amt =$exchange_curr_code+$bal_final_amt;
                $('#balance_<?php echo $currency_row->code; ?>').text($bal_amt);

    		// $('#balance_<?php echo $currency_row->code; ?>').text(formatMoney((total_paying - (gtotal_<?php echo $currency_row->code; ?> * <?php echo $currency_row->rate; ?>) ) / <?php echo $currency_row->rate; ?>,'<?php echo $getExchangesymbol; ?>'));
    		
    		<?php }
    		
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
        /*$("#add_item").autocomplete({
            source: function (request, response) {
                
                $.ajax({
                    type: 'get',
                    url: '<?=admin_url('sales/suggestions');?>',
                    dataType: "json",
                    data: {
                        term: request.term,
                        warehouse_id: $("#poswarehouse").val(),
                        customer_id: $("#loyalty_customer").val()
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
        });*/

        <?php if ($pos_settings->tooltips) {echo '$(".pos-tip").tooltip();';}
        ?>

        

       



$(document).on('change', '.credit-max', function () {

    if ($('#customer_type').val()=='prepaid') {	
	$inputCredit = 0;
	$index = $(this).parents('.payment-row').find('select').attr('data-index');	
	$('.credit-max').each(function(n,v){
	    console.log($(this).attr('id')+'=='+"amount_<?=$default_currency_data->code?>_"+$index);
	    if($(this).attr('id')!="amount_<?=$default_currency_data->code?>_"+$index){
		$inputCredit += ($(this).val()=='')?0:parseFloat($(this).val());
	    }
	});	
	$creditlimit = parseFloat($('#credit_limit').val())-parseFloat($inputCredit);	
	if(parseFloat($(this).val())>parseFloat($creditlimit)){$(this).val('');alert('Amount Exceeds credit limit');}
    }    
});		

$(document).on('change', '.creditcard-max', function () {
var balance = $('.balance_amount').val();
var total_check = $('#total').val();
    $inputCreditcard = 0;
    $index = $( this ).attr('payment-type');   
    $('.creditcard-max').each(function(n,v){        
        if($(this).attr('id') =="amount_<?=$default_currency_data->code?>_"+$index){
            $inputCreditcard += ($(this).val()=='')?0:parseFloat($(this).val());
        }
    });     
    var total_check = $('#total').val();
    if(parseFloat(balance)>0){$(this).val('');alert('Amount Exceeds Payable Total');}    
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

var $print_header_space = '<?=$pos_settings->pre_printed_header?>mm';
var $print_footer_space = '<?=$pos_settings->print_footer_space?>mm';
var pre_printed = '<?=$pos_settings->pre_printed_format?>';

    
   
		 function requestBill(billid){

            var base_url = '<?php echo base_url(); ?>';            
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
			if (pre_printed==0) {
                         bill_head += (data.biller.logo != "test") ? '<div id="wrapper1"><div id="receiptData"><div id="receipt-datareceipt-data"><div class="text-center"><img  src='+base_url+'assets/uploads/logos/'+data.biller.logo +' alt="" >': "";

                         <?php if($pos_settings->print_local_language == 1) :?>
                            bill_head += '<h3 style="text-transform:uppercase;">'+data.biller.local_lang_name+'</h3>';
                         <?php endif; ?>   

                         bill_head += '<h3 style="text-transform:uppercase;">'+data.biller.company+'</h3>';
						
                        <?php if($pos_settings->print_local_language == 1) :?>
                            bill_head += '<p>'+data.biller.local_lang_address+'</p>';
                        <?php endif; ?>  

                         bill_head += '<h4 style="font-weight: bold;">'+data.biller.address+"  "+data.biller.city+" "+data.biller.postal_code+"  "+data.biller.state+"  "+data.biller.country+'<br>'+'<?= lang('tel'); ?>'+': '+data.biller.phone+'</h4></div>';
			}
						 bill_head += '<h3 class="text-center" style="margin-top: 10px">INVOICE</h3>';
                          
                        <?php
                        if($this->Settings->time_format == 12){ ?>
                            var created_on = formatDate(data.inv.created_on);
                        <?php }else {?>
                            var created_on = data.inv.created_on;
                        <?php }
                        ?>
                        function formatDate(date) {
                            var d = new Date(date);
                            var hh = d.getHours();
                            var m = d.getMinutes();
                            var s = d.getSeconds();
                            var dd = "AM";
                            var h = hh;
                            if (h >= 12) {
                                h = hh-12;
                                dd = "PM";
                            }
                            if (h == 0) {
                                h = 12;
                            }
                            m = m<10?"0"+m:m;

                            s = s<10?"0"+s:s;

                            /* if you want 2 digit hours:
                            h = h<10?"0"+h:h; */

                            var pattern = new RegExp("0?"+hh+":"+m+":"+s);

                            var replacement = h+":"+m;
                            /* if you want to add seconds
                            replacement += ":"+s;  */
                            replacement += " "+dd;  
                            return date.replace(pattern,replacement);
                        }

                        // bill_head += '<p>'+'<?= lang('bill_no'); ?>'+': '+data.billdata.bill_number+'<br>'+'<?= lang('date'); ?>'+': '+created_on+'<br>';
                        bill_head +='<?= lang('sale_no_ref'); ?>'+': '+data.inv.reference_no+'<br>';
                        /*<?php if($pos_settings->order_no_display == 1) :?>
                             bill_head +='<?= lang('sale_no_ref'); ?>'+': '+data.inv.reference_no+'<br>';
                        <?php endif; ?>  */                       

                         bill_head += '<?= lang('sales_person'); ?>'+': '+data.created_by.first_name+' '+data.created_by.last_name+'<br>'+'<?= lang('cashier'); ?>'+': '+data.cashier.first_name+' '+data.cashier.last_name;			 
            			 if(data.billdata.order_type==1){
            			    bill_head +='<br>'+'<?= lang('Table'); ?>'+': '+data.billdata.table_name;
            			 }else{
            			 
            			 }
			             bill_head += '</p>';
                         bill_head += '<p style="margin-top: -10px">'+'<?= lang('customer'); ?>'+': '+data.customer.name+'</p>';
                         if (site.pos_settings.total_covers==1) {
                         bill_head += '<p style="margin-top: -10px">'+'<?= lang('No of Covers'); ?>'+': '+data.billdata.seats+'</p>';
                     }
                            if(typeof data.delivery_person  != "undefined"){
							 bill_head += '<p>'+'<?= lang('phone'); ?>'+': '+data.customer.phone+'</p>';
							  bill_head += '<p>'+'<?= lang('delivery_address'); ?>'+': </p>';
							  bill_head += '<address>'+data.customer.address+' <br>'+data.customer.city+' '+data.customer.state+' '+data.customer.country+'<br>Pincode : '+data.customer.postal_code+'</address>';							  
							  bill_head += '<p>'+'<?= lang('delivery_person'); ?>'+': '+data.delivery_person.first_name+' '+data.delivery_person.last_name+' ('+data.delivery_person.user_number+')</p>';
							  bill_head += '<p>'+'<?= lang('phone'); ?>'+': '+data.delivery_person.phone+'</p>';
							 }

                        bill_totals += '<table class="table table-striped table-condensed" style="margin-top: -10px;font-size:14px!important;"><th colspan="2">'+'<?=lang("description");?>'+'</th><th>'+'<?=lang("price");?>'+'</th><th>'+'<?=lang("qty");?>'+'</th>';
			    if (site.pos_settings.bill_print_format==2) {
				bill_totals += '<th class="no-border text-center" style="margin-top: -10px">'+'<?=lang("dis(%)");?>'+'</th>';
			    }else{
				if(data.billdata.manual_item_discount != 0){
				    if(site.pos_settings.manual_item_discount_display_option == 1){
				    bill_totals += '<th class="no-border text-center" style="margin-top: -10px">'+'<?=lang("dis(%)");?>'+'</th>';
				}else{
				    bill_totals += '<th class="no-border text-center" style="margin-top: -10px">'+'<?=lang("dis");?>'+'</th>';
				}
				}
			    }

                        bill_totals += '<th class="text-right">'+'<?=lang("sub_total");?>'+'</th>';

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
							<?php } ?>
							var recipe_variant='';
                            if(b.recipe_variant!=''){                                
                                recipe_variant = ' - ['+b.recipe_variant+']';
                            }else{                                
                                recipe_variant='';
                            }

                            if(b.manual_item_discount != 0){
                                $underline ='underline';
                            }else{
                              $underline ='none';
                            }
                            var star ='*';                            
                            if(b.star == '' || b.manual_item_discount != 0){
                              star ="";

                            }

                            bill_totals += '<tr><td colspan="2" class="no-border"><span style="display: inherit;">'+ star+ '</span><span style="display: table-cell;text-align: -webkit-match-parent;text-decoration:'+$underline+'">'+ recipe_name+ ' </span></td><td class="no-border">'+ formatMoney(b.net_unit_price) +'</td><td class="no-border text-center">'+ formatDecimal(b.quantity) +'</td>';
                                $cols = "4";
                                $cols1 = "5";
				 if (site.pos_settings.bill_print_format==2) {
				    bill_totals += '<td class="no-border text-right">'+ b.customer_discount_val +'</td>';
				 }else{
				     if(data.billdata.manual_item_discount != 0){
					$cols = "5";
					$cols1 = "6";
					if(site.pos_settings.manual_item_discount_display_option == 1){
					bill_totals += '<td class="no-border text-right">'+ Math.floor(b.manual_item_discount_per_val) +'</td>';
					}else{
					    bill_totals += '<td class="no-border text-right">'+ formatMoney(b.manual_item_discount) +'</td>';
					}
				    }
				 }
                           

                if (site.pos_settings.bill_print_format==2) {
                bill_totals += '<td class="no-border text-right">'+ formatMoney(b.subtotal-b.manual_item_discount-b.input_discount) +'</td></tr>';
                }else{
                bill_totals += '<td class="no-border text-right">'+ formatMoney(b.subtotal-b.manual_item_discount) +'</td></tr>';
                }
                            /*bill_totals += '<tr><td colspan="2" class="no-border"><span style="display: inherit;">'+r+': &nbsp;'+ b.star+ '&nbsp;</span><span style="display: table-cell;text-align: -webkit-match-parent;text-decoration:'+$underline+'"">'+ recipe_name+ ' </span></td><td class="no-border">'+ formatMoney(b.net_unit_price) +'</td><td class="no-border text-center">'+ formatDecimal(b.quantity) +'</td><td class="no-border text-right">'+ formatMoney(b.subtotal) +'</td></tr>';*/


                                /*bill_totals += '<tbody><tr><td colspan="2" class="no-border">'+r+': &nbsp;&nbsp'+ recipe_name+'' +recipe_variant+'</td><td class="no-border">'+ formatMoney(b.net_unit_price) +'</td><td class="no-border">'+ formatDecimal(b.quantity) +'</td><td class="no-border text-right">'+ formatMoney(b.subtotal) +'</td></tr></tbody>';*/
                            });

                                $cols = "4";
                                $cols1 = "5";
                            if(data.billdata.manual_item_discount != 0){
                                $cols = "5";
                                $cols1 = "6";
                            }
                            if (site.pos_settings.bill_print_format==2) {
                                 $cols = "5";
                                 $cols1 = "6";
                            }

							 bill_totals += '<tr class="bold"><th colspan="'+$cols+'" class="text-right">'+'<?=lang("items");?>'+'</th><th  class="text-right">'+formatDecimal(r)+'</th></tr>';
							 
            			    if (site.pos_settings.bill_print_format==1) {			 
            				bill_totals += '<tr class="bold"><th colspan="'+$cols+'"class="text-right">'+'<?=lang("total");?>'+'</th><th   class="text-right">'+formatMoney(data.billdata.total - data.billdata.manual_item_discount)+'</th></tr>';
            			    }else{
            				bill_totals += '<tr class="bold"><th colspan="'+$cols+'"class="text-right">'+'<?=lang("total");?>'+'</th><th   class="text-right">'+formatMoney(data.billdata.total - data.billdata.manual_item_discount- data.billdata.order_discount)+'</th></tr>';
            			    }
							
							$total_dis_without_manual =  formatDecimal(data.billdata.total_discount - data.billdata.manual_item_discount);
							if (site.pos_settings.bill_print_format==1) {
                            if($total_dis_without_manual > 0) {
									if(data.billdata.discount_type == 'manual'){
                                        if(data.discount){
                                            var disname = data.billdata.discount_val;
                                        }else{
                                            var disname = 'Discount';
                                        } 
										bill_totals += '<tr class="bold"><th class="text-right" colspan="'+$cols+'">'+lang.discount+'('+disname+')</th><th   class="text-right">'+formatMoney($total_dis_without_manual)+'</th></tr>';
									} else {
                                        
                                        if(data.discount){
                                            var disname = data.discount;
                                        }else{
                                            var disname = 'Discount';
                                        }
                                    bill_totals += '<tr class="bold"><th class="text-right" colspan="'+$cols+'">'+disname+'</th><th   class="text-right">'+formatMoney($total_dis_without_manual)+'</th></tr>';
									}
                                }
							}else{
							    
							}

                            if(data.billdata.service_charge_id != 0 && data.billdata.service_charge_amount != 0){
                                bill_totals += '<tr class="bold">';
                                bill_totals += '<th colspan="'+$cols+'" class="text-right" >'+data.billdata.service_charge_display_value+' </th>';
                                    bill_totals += '<th colspan="1" class="text-right" >'+data.billdata.service_charge_amount+' </th>';
                                bill_totals += '</tr>';
                            }      

                            if(data.billdata.tax_type==0){
                                $grandTotal = parseFloat(data.billdata.total) -parseFloat(data.billdata.total_discount) -parseFloat(data.billdata.birthday_discount)  + parseFloat(data.billdata.service_charge_amount);
                               }else{
                                $grandTotal = parseFloat(data.billdata.total) -parseFloat(data.billdata.total_discount) -parseFloat(data.billdata.birthday_discount)  + parseFloat(data.billdata.total_tax) + parseFloat(data.billdata.service_charge_amount);
                               } 
                            
                            <?php if($pos_settings->display_tax==1) : ?>
                                
                                bill_totals += '<tr>';       
                                bill_totals += '<th ></th>';                     
                                bill_totals += '<th ><?php echo $pos_settings->tax_caption; ?></th>';
                                $.each(data.tax_splits, function(k,d) {   
                                    bill_totals += '<th >'+d.name+'</th>';
                                });   
                                bill_totals += '</tr>';   

                                bill_totals += '<tr>';
                                bill_totals += '<th></th>';                     
                                bill_totals += '<th>'+formatDecimal(data.tax_rate.rate)+'</th>';
                                 $.each(data.tax_splits, function(s,p) {   
                                    $tax_amount = ((($grandTotal) * p.rate) / 100);   
                                    bill_totals += '<th>'+formatMoney($tax_amount)+'</th>';  
                                }); 

                                bill_totals += '<th class="text-right">'+formatMoney(data.billdata.total_tax)+'</th>';  
                                bill_totals += '</tr>';

                            <?php endif; ?>            

                           /*<?php if($pos_settings->display_tax==1) : ?>
                            if (data.billdata.tax_rate != 0) {
                                    $taxtype = '<?=lang('tax_exclusive')?> '+ data.billdata.tax_name;
                                if(data.billdata.tax_type==0){
                                       $taxtype = '<?=lang('tax_inclusive')?> '+data.billdata.tax_name;
                                }
                                bill_totals += '<tr class="bold">';
                                bill_totals += '<th colspan="'+$cols+'" class="text-right" ><?php echo $pos_settings->tax_caption; ?>  </th>';
                                <?php if($pos_settings->display_tax_amt==1) : ?>
                                    bill_totals += '<th colspan="1" class="text-right" >'+formatMoney(data.billdata.total_tax)+'  </th>';
                                <?php endif; ?>
                                bill_totals += '</tr>';
                            }
                        <?php endif; ?>  */                      
                 
                          

                        $grandTotal =data.billdata.grand_total;
                        $grand_Total =formatMoney(data.billdata.grand_total);
                        var substr = $grand_Total.split('.');
                        $riel =  substr[1]; 


                       var decimals = $grandTotal - Math.floor($grandTotal);
                       decimals = decimals.toFixed(2);

                        <?php
                        $exchange_rate = $this->site->getExchangeRatey($this->Settings->default_currency);

                        if ($pos_settings->print_option == 1) {  
                        $exchange_curr_code = $this->site->getExchangeCurrency($this->Settings->default_currency);    
                        foreach($currency as $currency_row):?>
                        var currency_rate = <?php echo $currency_row->rate;?>;
                        var currency_id = <?php echo $currency_row->id;?>;
                        var  $exchange_rate = "<?php echo $exchange_rate;?>";                    
                        var  $exchange_curr_code = "<?php echo $exchange_curr_code;?>";  
                    
                        var currency_symbol = '<?php echo $currency_row->symbol;?>';                    
                        var grandTotal = formatMoney($grandTotal/currency_rate, currency_symbol);          
                        if(currency_id == "<?php echo $this->Settings->default_currency;?>"){
                            $decimals = decimals/ $exchange_rate;
                            $decimals =  Math.round($decimals / 100) * 100;
                             var $riel = '<br>('+$exchange_curr_code+($decimals)+')';
                        }
                        else{
                            var $riel = '';
                        }
                        
                    bill_totals += '<tr class="bold"><th colspan="'+$cols+'" class="text-right" >'+lang.grand_total;

                       <?php 
                           if($this->Settings->default_currency != $currency_row->id){ ?>
                            // exchange amount 
                                    $final_amt = $grandTotal/ currency_rate;
                                    $final_amt =  Math.round($final_amt / 100) * 100;

                                    /*bill_totals += '</th><th colspan="2"  class="text-right">'+$exchange_curr_code+$final_amt+'</th>';*/
                                    bill_totals += '</th><th colspan="2"  class="text-right">'+exchangeformatMoney($final_amt, $exchange_curr_code)+'</th>';

                               <?php  }else{ ?>
                                    $final_amt = $grandTotal/ currency_rate;
                                    bill_totals += '</th><th colspan="2"  class="text-right">'+formatMoney($final_amt, currency_symbol)+''+$riel+'</th>';
                      <?php  } ?>


                      

                      /* bill_totals += '<tr class="bold"><th colspan="4" class="text-right" >'+lang.grand_total+'(<?php echo $currency_row->code;?>)</th><th colspan="2"  class="text-right">'+formatMoney($final_amt, currency_symbol)+''+$riel+'</th></tr>';*/
                       bill_totals += '</tr>';
               
               <?php endforeach; }else{ ?>

                    <?php $default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
                    $exchange_curr_code = $this->site->getExchangeCurrency($this->Settings->default_currency); 

                     ?>
                    var default_currency_rate = <?php echo $default_currency_data->rate; ?>;
                    var currency_symbol = '<?php echo $currency_row->symbol;?>';                    
                    var grandTotal = formatMoney($grandTotal/default_currency_rate, currency_symbol); 
                    var  $exchange_rate = "<?php echo $exchange_rate;?>";                    
                    var  $exchange_curr_code = "<?php echo $exchange_curr_code;?>";

                    bill_totals += '<tr class="bold"><th colspan="'+$cols+'" class="text-right" >'+lang.grand_total+'(<?php echo $default_currency_data->code;?>)</th><th colspan="2"  class="text-right">'+formatMoney($grandTotal)+'</th></tr>';
                <?php }?>
               
               <?php if($pos_settings->discount_note_display_option == 1){?>
                    if(data.billdata.total_discount != 0){
                       bill_totals += '<tr><th colspan="'+$cols1+'" class="text-left"><small>* Bill Discount is not applied to these items.</small></th></tr>';
                    }

                    if(data.billdata.manual_item_discount != 0){
                       bill_totals += '<tr><th colspan="'+$cols1+'" class="text-left"><small>Underlined Items are manually Discount is applied.</small></th></tr>';
                     }
                     if(data.biller.invoice_footer != ''){
                          bill_totals += '<tr><th colspan="'+$cols1+'" class="text-center"><small>'+data.biller.invoice_footer +'</small></th></tr>';
                       }
                <?php } ?> 
                 if(data.biller.invoice_footer != ''){
                          bill_totals += '<tr><th colspan="'+$cols1+'" class="text-center"><small>'+data.biller.invoice_footer +'</small></th></tr>';
                       }
                            
                    bill_totals += '</table>';

                              /* $grandTotal =data.billdata.grand_total;
                			   if(data.billdata.tax_type==0){
                			      $grandTotal = parseFloat(data.billdata.grand_total) + parseFloat(data.billdata.total_tax);
                			   } 
                            bill_totals += '<tr class="bold"><th colspan="4" class="text-right" ><span class="pull-left">'+$taxtype+'</span>'+lang.grand_total+'</th><th colspan="2"  class="text-right">'+formatMoney($grandTotal)+'</th></tr></tfoot></table>';*/
						
                                $('#bill_header').empty();
				
				$('#bill_header').append(bill_head);
				
                                

                                $('#bill-total-table').empty();                                
                                $('#bill-total-table').append(bill_totals);
								<?php if($pos_settings->remote_printing == 1){?>
                                PrintDiv($('#bill_tbl').html());  
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
    
   
   <?php
    
    if ($pos_settings->remote_printing == 1) { ?>
        function PrintDiv(data) {
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
            }

    /*function Popup(data) {
	
        var mywindow = window.open('', 'sma_pos_print', 'height=500,width=300');
	
        mywindow.document.write('<html><head><title>Print</title>');
        mywindow.document.write('<link rel="stylesheet" href="<?=$assets?>styles/helpers/bootstrap.min.css" type="text/css" />');
        mywindow.document.write('</head><body >');
        mywindow.document.write(data);
        mywindow.document.write('</body></html>');
        mywindow.print();
        mywindow.close();
        return true;
    }*/
    <?php }
    ?>
</script>

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
$('.kb_pad_length').keyboard({
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
        maxLength : 20,
        customLayout: {
            'default': [
            '1 2 3 4',
            '5 6 7 8  ',
            ' 9 0 - {b}',
            ' {accept} {cancel}'
            ]
        },
        
    });
$('.kb_pad_exp').keyboard({
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
        maxLength : 6,
        customLayout: {
            'default': [
            '1 2 3 4',
            '5 6 7 8  ',
            ' 9 0 {b}',
            ' {accept} {cancel}'
            ]
        },
        
    });
</script>
<script>

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
function exchangeformatMoney(x, symbol) {
    if(!symbol) { symbol = ""; }
    if(site.settings.sac == 1) {
        return (site.settings.display_symbol == 1 ? site.settings.symbol : '') +
            ''+formatSA(parseFloat(x).toFixed(site.settings.exchange_decimals)) +
            (site.settings.display_symbol == 2 ? site.settings.symbol : '');
    }
    var fmoney = accounting.formatMoney(x, symbol, site.settings.exchange_decimals, site.settings.thousands_sep == 0 ? ' ' : site.settings.thousands_sep, site.settings.decimals_sep, "%s%v");
    if(symbol){
       return fmoney; 
    }
    return (site.settings.display_symbol == 1 ? site.settings.symbol : '') +
        fmoney +
        (site.settings.display_symbol == 2 ? site.settings.symbol : '');
}

</script>

<style type="text/css">
.payment_type .active,.btn-prni.active{
    background-color: #1F73BB!important;
    color: #fff!important;
}
.payment_type{
    cursor: pointer;
    height: 33px;
    margin: 0 0 0px 2px;
    padding: 2px;
    width: 10.5%;
    min-width: 100px;
    overflow: hidden;
    display: inline-block;
    font-size: 13px;
    color: block;
    border: 1px solid black
   } 

#reset{
    cursor: pointer;
    height: 33px;
    margin: 0 0 0px 2px;
    padding: 2px;
    width: 10.5%;
    min-width: 100px;
    overflow: hidden;
    display: inline-block;
    font-size: 13px;
    color: block;
    border: 1px solid black
   } 
{
    cursor: pointer;
    height: 33px;
    margin: 0 0 0px 2px;
    padding: 2px;
    width: 10.5%;
    min-width: 100px;
    overflow: hidden;
    display: inline-block;
    font-size: 13px;
    color: block;
    border: 1px solid black
   } 

   .paid_payments{
    cursor: pointer;
    height: 33px;
    margin: 0 0 0px 2px;
    padding: 2px;
    width: 10.5%;
    min-width: 100px;
    overflow: hidden;
    display: inline-block;
    font-size: 13px;
    color: block;
    border: 1px solid black
   } 

.total_paytd{
    background-color: #1A2127!important;  
    color: #1F73BB;
    font-weight: bold;
    font-size: 16px;;
}

.balance_paytd{
    background-color: #1F73BB!important;  
    color: #FFF;
    font-weight: bold;
    font-size: 16px;;
}

.used_tender_type{
    cursor: pointer;
    height: 33px;
    margin: 0 0 0px 2px;
    padding: 2px;
    width: 10.5%;
    min-width: 100px;
    overflow: hidden;
    display: inline-block;
    font-size: 13px;
    color: block;
    border: 1px solid black
   }
   .taxation_settings label{
    float: none;
   }
   .base_currency_CC2 {
    padding-left: 31px;
}
.biller-keyboard .ui-keyboard-button{
	height: 2em !important;
    }

</style>

<script type="text/javascript">
    $(document).ready(function (e) {
     $('input[type="checkbox"],[type="radio"]').not('.skip').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%' // optional        
        });
     });

    
$(document).on('click', "#new_customer_submit", function(e) {
	var form = $(this);
	var total_check = $('#total').val();
	var eligibity_point  = $('#eligibity_point').val();
	$.ajax({
		type: "POST",
		url: site.base_url + "customers/new_customer",
          //url: "<?=admin_url('customers/new_customer')?>",
		data: $('#new-customer-form').serialize(), // serializes the form's elements.
		dataType: 'json',
		success: function(data)
		{

			if(data.msg == 'error'){

				$('#msg_error').html(data.msg_error);
			}else{
			   $('#new_customer_id').val(data.new_customer_id ? data.new_customer_id : 0);
			   $('#new_customer_name').text(data.name ? data.name : '');
			}
		}
	});
	e.preventDefault(); // avoid to execute the actual submit of the form.
	return false;
	
});
$(document).on('click', '#submit-sale1', function () {
			
            var balance = $('.balance_amount').val();
            if (balance >= 0) {   
		  $(this).attr('disabled',true);	           
                  $('#pos-payment-form').submit();
            }
            else{
                
                bootbox.alert("Paid amount is less than the payable amount.");
                return false;
            }  
        });

$('.crd_exp').on("change", function() {
  var str = $(this).val().slice(0, 2);
  if(str){
  var str1 = "/";
  var str2 = $(this).val().slice(2, 6);
    var res = str.concat(str1, str2);
    if(res!=""){
    $('.crd_exp').val(res);
}}

});
</script>

</body>
</html>
