<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?=lang('pos_module') . " | " . $Settings->site_name;?></title>
    <script type="text/javascript">if(parent.frames.length !== 0){top.location = '<?=admin_url('qsr')?>';}</script>
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
    <link rel="stylesheet" href="<?= $assets ?>styles/jquery-ui.css">
    <script src="<?= $assets ?>js/jquery-ui.js"></script>
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
                    if (!empty($message)) {
                        echo "<div class=\"alert alert-success\"><button type=\"button\" class=\"close fa-2x\" data-dismiss=\"alert\">&times;</button>" . $message . "</div>";
                    }
                ?>
                
                <div id="pos">
                

<div class="tableright col-xs-12">
     <div id="receiptData" class="col-lg-6 ">
            <div class="form-group  col-lg-3 date_div">
                <label for="method"><?php echo $this->lang->line("from_date"); ?></label>
                  <div class="controls ">
                    <input type="text" name="from_date" class="form-control datetime" placeholder="From Date " id="from_date" required="required">
                  </div>
            </div>
            <div class="form-group  col-lg-3 date_div" style="margin-top: 30px;">
              
                <button class="btn btn-block btn-danger" id="reprint-data"><?=lang('submit');?></button>
           
        </div>
    </div>
      
 <div class="col-xs-12"> 
 
        
        <?php
        if(!empty($sales)){
            /*echo "<pre>";
            print_r($sales);die;*/
        ?>
        <ul>
            <?php
            foreach($sales as $sales_row){
            ?>
            <li class="col-md-12">
                <div class="row">

                    <div class="billing_list btn-block order-biller-table order_biller_table">
                <h2 class="order-heading" style="margin-top: 0px;"> <?=lang('bill_no')?>: <?php echo $sales_row->bill_number; ?></h2>
                    <h2><?php echo $sales_row->reference_no; ?></h2>
                    <input type="hidden"  class="billid_req" value="<?php echo $sales_row->id; ?>">

                    
                    </div>
                        <?php // echo "<pre>";print_R($sales_row);
			if(!empty($sales_row->bils)){
                        /*echo "<pre>";
                        print_r($sales_row->bils);die;*/
						$k=1;
                        foreach($sales_row->bils as $split_order){
                            if (count($split_order->id) > 0) {
                       		
                            ?>
                            <div class="col-xs-2 payment-list-container">
                             <h2 class="order-heading" style="margin-top: 0px;"> <?=lang('bill_no')?>: <?php echo $split_order->bill_number; ?></h2>
                             <div class="col-xs-12" style="padding: 0;">
                              <div class="btn-group-vertical btn-block resettle-block">
				<button  type="button" class="btn btn-success resettle-payment"  id="resettle-payment" style="height:40px;" <?php if($this->sma->actionPermissions('bil_payment')){ echo ''; }else{  echo 'disabled'; }  ?> style="height:40px;" >
                                        <?php  echo lang('resettle_bill'); ?>
                                    </button>
                                
                                <input type="hidden"  class="billid" value="<?php echo $split_order->id; ?>">

                                <input type="hidden"  class="order_split" value="<?php echo $sales_row->sales_split_id; ?>">

                                <input type="hidden"  class="salesid" value="<?php echo $split_order->sales_id; ?>">
                                <?php 
                                if ($split_order->tax_type == 0)
                                {
                                    $grandtotal = $split_order->total-$split_order->total_discount;
                                }
                                else{
                                    $grandtotal = $split_order->total-$split_order->total_discount+$split_order->total_tax;
                                }
                                  
                                ?>                                
                                <input type="hidden"  class="grandtotal" value="<?php echo $grandtotal; ?>">
                				<input type="hidden"  class="credit-limit" value="<?php echo $split_order->credit_limit; ?>">
                				<input type="hidden"  class="company-id" value="<?php echo $split_order->company_id; ?>">
                				<input type="hidden"  class="customer-type" value="<?php echo $split_order->customer_type; ?>">
                				<input type="hidden"  class="customer-id" value="<?php echo $split_order->customer_id; ?>">
                				<input type="hidden"  class="customer-name" value="<?php echo $split_order->customer_name; ?>">
                                <input type="hidden"  class="totalitems" value="<?php echo $split_order->total_items; ?>">
                                <input type="hidden"  class="loyalty_available" value="<?php  echo $this->site->getCheckLoyaltyAvailable($split_order->customer_id); ?>">
                            </div>
                            
                            </div>
                             <div style="clear:both; height:5px;"></div>
                          
                        </div>                                                 
                            <?php 
                        }
                        $k++;
						}
                     }
                ?>
                
                </div>
                
            </li>
            <?php
            }
            ?>
        </ul>
        <?php
        }else{
        ?>
        <div class="col-sm-6 col-sm-offset-3 col-xs-12 order_cancel_data alert-danger fade in"> <?=lang('no_record_found')?> </div>
        <?php
        }
        ?>
        <div>
</div>
                    
                    <div style="clear:both;"></div>
                </div>
                <div style="clear:both;"></div>
            </div>
        </div>
    </div>
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
                    <?= form_textarea('remarks', '', 'class="form-control kb-text" id="remarks" style="height:80px;"'); ?>
                </div>
                <input type="hidden" id="sale_id" value=""/>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="cancel_paymentbill"><?=lang('send')?></button>
            </div>
        </div>
    </div>
</div>


<?php
$this->load->view($this->theme . 'qsr/qsr_footer');
?>



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

  $(document).on('click', '.request_bil', function(){      
        var billid = $(this).siblings('.billid_req').val();        
        var url = '<?php echo  admin_url('qsr/reprint_view') ?>';
        window.location.href= url +'/?bill_id='+billid;
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
		maxLength: 18,
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
		<form action="<?=admin_url('qsr/re_payment')?>" autocomplete="off" role="form" id="pos-re-payment-form" method="post" accept-charset="utf-8">
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
		<input type="hidden" name="bill_id" class="re-pay-bill-id" value=""/>
		<input type="hidden" name="date" class="paid-date" value="<?=$_GET['date']?>"/>
		<input type="hidden" name="sale_id" class="re-pay-sale-id" value=""/>
		
                <input type="hidden" name="type" class="type" value="<?php echo $type;?>"/>
                <input type="hidden" name="balance_amount" class="balance_amount" value=""/>
                    <div class="col-md-12 col-sm-12 text-center">
                       
                        <input type="hidden" name="credit_limit" id="credit_limit" class="credit_limit" />
                        <input type="hidden" name="company_id" id="company_id" class="company_id" />
                        <input type="hidden" name="customer_type" id="customer_type" class="customer_type" />
                        <input type="hidden" name="customer_id" id="customer_id" class="customer_id"/>
                        <input type="hidden" name="total" id="total" class="total" />
                        <input type="hidden" name="loyaltypoints" id="loyaltypoints" class="loyaltypoints" />
                        <input type="hidden" name="loyalty_used_points" id="loyalty_used_points" class="loyalty_used_points" />

                        <div class="form-group bill_sec_head" style="color: #1F73BB!important;font-size: 200!important;align-self: center;margin-bottom: 5px;">
                            <button type="button" class="btn btn-warning" id="pay_reset" style="cursor: pointer!important;    color: #fff;background-color: #d9534f;height: 35px!important;">Reset</button>
                               <?=lang("bill_amount", "bill_amount");?>
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
                <button class="btn btn-block btn-lg btn btn-info" id="submit-re-payment"><?=lang('send');?></button>
            </div>
        </div>
    </div>
		</form>
</div>
</div>
</div>
</div>

<script type="text/javascript">

 

$('.sortable_table tbody').sortable({
    containerSelector: 'tr'
});

  // $var = '<?php echo '-'.$pos_settings->reprint_from_last_day ?>';
    $("#from_date").datepicker({
         minDate: '<?php echo '-'.$pos_settings->reprint_from_last_day ?>',
         maxDate: 0,
         dateFormat: 'yy-mm-dd',
    });
     <?php
        $default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
        ?>
        var currency_json = <?php echo json_encode($currency); ?>;
        var default_currency_rate = <?php echo $default_currency_data->rate; ?>;
        var default_currency_code = '<?php echo $default_currency_data->code; ?>';
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
	    console.log('tt'+total_paying)
            $('#total_paying').text(formatMoney(total_paying));
	    <?php
        foreach($currency as $currency_row){
            $default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
            if($currency_row->code == $default_currency_data->code){
            ?>
	   
            $('#balance_<?php echo $currency_row->code; ?>').text(formatMoney(total_paying - (gtotal_<?php echo $currency_row->code; ?> * <?php echo $currency_row->rate; ?>) ));

            $('.balance_amount').val(formatDecimal(total_paying - (gtotal_<?php echo $currency_row->code; ?> * <?php echo $currency_row->rate; ?>))); 

            $('#balance_amt_<?php echo $currency_row->code; ?>').val(formatDecimal(total_paying - (gtotal_<?php echo $currency_row->code; ?> * <?php echo $currency_row->rate; ?>) ),'<?php echo $currency_row->symbol; ?>');

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
    }
    $(document).ready(function(){
	$('#reprint-data').click(function () {
            var from_date = $('#from_date').val();             
            var url = '<?php echo  admin_url('qsr/resettle_bill') ?>';
            window.location.href= url +'/?date='+from_date;  
	});
	$(document).on('click', '#pay_reset', function () {    
	    $('#userd_tender_list').html('');
	    $(".amount").val('');
	    calculateTotals();
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
	})
	  $(document).on('click', '#resettle-payment', function(){
            twt = $(this).closest('.resettle-block').find('.grandtotal').val();
	    $billid = $(this).closest('.resettle-block').find('.billid').val();
	    $saleid = $(this).closest('.resettle-block').find('.salesid').val();
	    $('.re-pay-bill-id').val($billid);
	    $('.re-pay-sale-id').val($saleid);
            $('.total').val(twt);
            
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
            //$('#amount_USD_cash').val(twt);
            $('#paymentModal').modal('show');
           <?php
            foreach($currency as $currency_row){
            $default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
            if($currency_row->code == $default_currency_data->code){    
            ?>
            /*$('#amount_<?php echo $currency_row->code; ?>_1').focus().val('');*/
           // $('#amount_<?php echo $currency_row->code; ?>_1').focus().val(twt);

            <?php
            }else{
            ?>
            $('#amount_<?php echo $currency_row->code; ?>_1').val('');
            <?php
            }
            }
            ?>

                     
        });
	  $(document).on('click', '#submit-re-payment', function () {

            var balance = $('.balance_amount').val();
	   // alert(balance);return false;
            if (balance >= 0) {            
                  $('#submit-re-payment').text('Loading...').attr('disabled', true);
                  
                  $('#pos-re-payment-form').submit();
            }
            else{
                bootbox.alert("Paid amount is less than the payable amount.");     
                return false;           
            }  
        });
$('#paymentModal').on('shown.bs.modal', function(e) {
        // $("button.payment_type").val("cash").click();        
        $('#customer_id').val($("#poscustomer").val());        
        $('#bill_amount').text(formatMoney($('.total').val()));

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
        

        <?php
            foreach($currency as $currency_row){
                $default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
                if($currency_row->code == $default_currency_data->code){  
                ?>
		$('#amount_<?php echo $currency_row->code; ?>_cash').val('');
                <?php
                }else{                
                ?>
                 $('#amount_<?php echo $currency_row->code; ?>_cash').val('');
                <?php
                }
            } ?>
	    if($('#payment-cash').val() == 'cash'){
	    
            $('#payment-cash').trigger('click');                  
            $('#payment-cash').addClass('active');   
        } 
	   // calculateTotals();   
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
            $amount = $('#balance_amt_USD').val();          
            $amount = parseFloat($amount.match(/([0-9]*\.[0-9]+|[0-9]+)/));
            console.log('cc'+$amount);
            $('#amount_USD_'+$index).removeClass('credit-max');
            $(this).parent('.form-group').find('.available-c-limit').remove();
            if ($( this ).val()=='credit') {                
                $('#amount_USD_'+$index).addClass('credit-max');
                $inputCredit = 0;
                $('.credit-max').each(function(n,v){
                if($(this).attr('id')!="amount_USD_"+$index){
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
	 
            console.log('uu'+$amount);
   
                if ((p_val != 'loyalty') && (p_val == 'cash')) {                    
                  if($amount>0){                    
                     if($('#amount_USD_cash').val() == ''){
                        $('#amount_USD_cash').val($amount);
                      }
                    }                                      
                }else if ((p_val != 'loyalty') && (p_val == 'CC')) {                    
                  if($amount>0){                    
                     if($('#amount_USD_CC').val() == ''){
                        $('#amount_USD_CC').val($amount);
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
        });
    })

</script>
 <style type="text/css" media="all">
  .ui-autocomplete {
    max-height: 150px;
    overflow-y: auto;
    /* prevent horizontal scrollbar */
    overflow-x: hidden;
    /* add padding to account for vertical scrollbar */
    padding-right: 20px;
}
/* IE 6 doesn't support max-height
 * we use height instead, but this forces the menu to always be this tall
 */
html .ui-autocomplete {
    height: 150px;
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
	

#category-list .btn-prni.active{
	    background-color: #f5690a!important;
}
#subcategory-list .active{
	    background-color: #fff!important;
}
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
   .taxation_settings label{
    float: none;
   }
</style>    


</body>
</html>


