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
/*  mani*/
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
    #bill_amount{font-size: 30px;font-weight: bold;}
    .payment_type .active, .btn-prni.active{border: 1px solid #1F73BB;box-shadow: none;transition: all 0.2s ease-in;}
    #paymentModal .modal-footer{border: none;}
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
/*  end mani*/        
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
        </style>

    <?php if(@$_GET['bbqtid'] && isset($_SERVER['HTTP_REFERER'])): ?>
        <script>var curr_page="order_bbqbiller";var curr_func="update_bbqtables";var tableid = '<?=$_GET['bbqtid']?>';</script>
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
      url: "<?=admin_url('pos/ajaxbbq_billing');?>",
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
var ajaxDatatimeout = setInterval(ajaxData(<?php echo $type; ?>), 10);

</script>
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
<?php
$currency = $this->site->getAllCurrencies();
?>
<div class="modal fade in" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="payModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true"><i
                            class="fa fa-2x">&times;</i></span><span class="sr-only"><?=lang('close');?></span></button>
        <h4 class="modal-title" id="payment-customer-name"></h4>
        <h4 class="modal-title" id="payModalLabel"><?=lang('finalize_sale');?>(<span id="new_customer_name"></span>)</h4>
                
            </div>
            <div class="modal-body" id="payment_content">
                 <div class="btn btn-warning pull-right">
                    <a href="<?=admin_url('customers/new_customer');?>" id="add-customer" class="external" data-toggle="modal" data-target="#myModal">
                        Add New Customer <i class="fa fa-plus-circle" id="addIcon" style="font-size: 1.5em; "></i>
                    </a>
                </div> 
             <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'id' => 'pos-payment-form');
             echo admin_form_open("pos/bbqpaymant", $attrib);
             $type = 4;
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
                        <input type="hidden" name="bill_id" id="bill_id" class="bill_id" />
                        <input type="hidden" name="order_split_id" id="order_split_id" class="order_split_id" />
                        <input type="hidden" name="sales_id" id="sales_id" class="sales_id" />
                        <input type="hidden" name="credit_limit" id="credit_limit" class="credit_limit" />
                        <input type="hidden" name="company_id" id="company_id" class="company_id" />
                        <input type="hidden" name="customer_type" id="customer_type" class="customer_type" />
                        <input type="hidden" name="customer_id" id="customer_id" class="customer_id"/>
                        <input type="hidden" name="loyaltypoints" id="loyaltypoints" class="loyaltypoints" />
                        <input type="hidden" name="total" id="total" class="total" />
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
                                      echo "<button id=\"payment-" . $method->payment_type . "\" type=\"button\" value='" . $method->payment_type . "' class=\"btn-prni payment_type\" data-index='" . $method->payment_type. "' data_id='" . $j. "' ><span>" . $method->display_name . "</span></button>";
                                ?>
                                     <input name="paid_by[]" type="hidden" id="payment_type_<?php echo $method->payment_type; ?>" value="<?php echo $method->payment_type; ?>" class="form-control" autocomplete="off"  />
                            <?php } ?>
                            <div id="sub_items"  style="margin-top: 30px;min-height: 165px;">
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
                                      <div class="col-sm-6 CC_<?=$method->payment_type.$j?>" id="CC_<?=$method->payment_type?>" style="display: none">
                                         <div class="form-group col-md-12">
                                                <label><?=lang('card_no')?> </label>
                                            <input name="cc_no[]" type="text" maxlength="20" id="pcc_no_<?=$method->payment_type?>"
                                                    class="form-control kb_pad_length cc_no" placeholder="<?=lang('cc_no')?>"/>
                                        </div> 
                                        <div class="form-group col-md-12">
                                            <label><?=lang('card_exp_date')?> </label>
                                            <input name="card_exp_date[]" type="text" maxlength="6" value ="" id="card_exp_date_<?=$method->payment_type?>"
                                                    class="form-control crd_exp" placeholder="MM/YYYY"/>
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
                                            <input name="paying_loyalty_points[]" type="text" id="loyalty_points_<?=$method->payment_type?>" class="pa form-control loyalty_points"  autocomplete="off" />
                                        </div> 
                                        <div class="clearfix"></div>       
                                        <div id="lc_details_<?=$method->payment_type?>" style="color: red;"> </div>
                                        <div id="lc_reduem_<?=$method->payment_type?>" style="color: green;"></div>
                                    </div>
                                     <div class="clearfix"></div>
                                      <!-- <div class="col-sm-6 CC_<?=$method->payment_type.$j?>" id="CC_<?=$method->payment_type?>" style="display: none">
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
                            <button class="btn btn-block btn-lg btn btn-info" id="submit-sale1"><?=lang('send');?></button>
                        </div>
                    </div>
                </div>
            </div>


                       
                        
                        <!-- <div id="payments" class="payment-row">
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
                        </div> -->
                        <!-- <div id="multi-payment" class="payment-row"></div>
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
                                </tr>
                                </tbody>
                            </table>
                            <div class="clearfix"></div>
                        </div> -->
</div></div></div>
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

<!--<div class="modal fade in" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
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

<!-- new payment screen  start -->
<script type="text/javascript">
    $('#paymentModal').on('shown.bs.modal', function(e) {
        
        var loyalty_available = $('.loyalty_available').val();  
 $('#payment-loyalty').prop('disabled', false);
        if(loyalty_available == 0)
        {
            $('#payment-loyalty').prop('disabled', true).css('opacity',0.5);
        }
        else{
            $('#payment-loyalty').prop('disabled', false);
        }

        // $("button.payment_type").val("cash").click();
        $('#payment-cash').val('cash');    
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
      $('.amount').trigger('blur');
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
                                bootbox.alert('Gift card number is incorrect or expired.');
                            } else if (data.customer_id !== null && data.customer_id !== $('#poscustomer').val()) {
                                $('#loyalty_points_' + $index).parent('.form-group').addClass('has-error');
                                bootbox.alert('Gift card number is not for this customer.');
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
            console.log($amount);
                //if ((p_val != 'loyalty') && (p_val == 'cash')) {                    
                //  if($amount>0){
                //    if($('#amount_USD_cash').val() == ''){
                //        $('#amount_USD_cash').val($amount);
                //     }
                //}                                     
                //}else{
                //    $('#loyalty_points_cash').focus();
                //}
    
        if ((p_val != 'loyalty') && (p_val == 'cash')) {                    
                  if($amount>0){                    
                     if($('#amount_USD_cash').val() == ''){
            if (rt_cash!='' && rt_cash!=undefined) {
                $amount = rt_cash;
                      }
              $('#amount_USD_cash').val($amount);
                    }
          }
                }else if ((p_val != 'loyalty') && (p_val == 'CC')) {                    
                  if($amount>0){
            if (rt_cc!='' && rt_cc!=undefined) {
                $amount = rt_cc;
            }
                     if($('#amount_USD_CC').val() == ''){
            if (rt_cc!='' && rt_cc!=undefined) {
                $amount = rt_cc;
                           
            }
                         $('#amount_USD_CC').val($amount);
            
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
                $('#amount_USD_credit').val(rt_credit);
                
            }
        }
        $('.amount').trigger('blur');
        });
            $(document).on('change', '.loyalty_points', function () {
            var loyaltypoints = $("#loyaltypoints").val(); 
            var redemption = $(this).val() ? $(this).val() : 0;
            var customer_id = $("#customer_id").val();    
            $('#loyalty_used_points').val(0);             
            var payid = $(this).attr('id');                
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
                                 $('#loyalty_points_' + payid).focus().val('');                                  
                                 $('#amount_<?php echo $currency_row->code; ?>_'+payid).val('');
                            } else if (data.customer_id !== null && data.customer_id !== $('#poscustomer').val()) {
                                // bootbox.alert('Gift card number is not for this customer.');
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
                     $('#loyalty_points_' + id).focus().val('');
                     $('#amount_<?php echo $currency_row->code; ?>_'+id).val('');
                    
                }           
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
                $('.balance_amount').val('');
                $(".amount").val('');

            var billid = $thisObj.siblings('.billid').val(); 
            var customer_id = $thisObj.siblings('.customer-id').val(); 
            var customer_type = $thisObj.siblings('.customer-type').val(); 
            var allow_loyalty = $thisObj.siblings('.customer-allow-loyalty').val(); 
            var ordersplit = $thisObj.siblings('.order_split').val();
            var salesid = $thisObj.siblings('.salesid').val(); 
            var grandtotal = $thisObj.siblings('.grandtotal').val();
        var credit_limit = $thisObj.siblings('.credit-limit').val();
            console.log(credit_limit)
         // rough tender start//
        rt_cash = '';rt_credit='';rt_cc='';rt_loyalty='';
        
        rt_cash = $thisObj.siblings('.rt-cash').val(); 
            rt_credit = $thisObj.siblings('.rt-credit').val();
            rt_cc = $thisObj.siblings('.rt-CC').val();
        rt_loyalty = $thisObj.siblings('.rt-loyalty').val();
        // rough tender - end //
            var customer_name = $thisObj.siblings('.customer-name').val();
            $('#new_customer_name').text(customer_name);

            var count = $thisObj.siblings('.totalitems').val(); 
            $('.bill_id').val(billid);
            $('.sales_id').val(salesid);
            $('.total').val(grandtotal);
            $('.order_split_id').val(ordersplit);
            $('.credit_limit').val(credit_limit);
            $('.customer_id').val(customer_id);
            var twt = formatDecimal(grandtotal);
            console.log('grandtotal-'+grandtotal);
            $('#bill_amount').text(formatMoney(grandtotal));

            console.log('bil-'+billid);
               if(allow_loyalty==0){
                 $('#payment-loyalty').hide();
            }
            if (customer_type =='none' || customer_type==undefined || customer_type==0 ) {                
                $('#payment-credit').hide();
            }
            

            if (count == 0) {
                bootbox.alert('<?=lang('x_total');?>');
                return false;
            }
                  if(allow_loyalty==0){
        $('#payment-loyalty').hide();
        }
            <?php
            foreach($currency as $currency_row){
            $default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
            if($currency_row->code == $default_currency_data->code){
            ?>
             gtotal_<?php echo $currency_row->code; ?> = (parseFloat(twt) / parseFloat(<?php echo $currency_row->rate; ?>)) * <?php echo $default_currency_data->rate; ?>;
             $('#twt_<?php echo $currency_row->code; ?>').text(formatMoney(gtotal_<?php echo $currency_row->code; ?>));

             $('#paid_amt_<?php echo $currency_row->code; ?>').val(formatDecimal(gtotal_<?php echo $currency_row->code; ?>));

            <?php
            }else{
            ?>
              gtotal_<?php echo $currency_row->code; ?> = (parseFloat(twt) / parseFloat(<?php echo $currency_row->rate; ?>)) * <?php echo $default_currency_data->rate; ?>;
              $('#twt_<?php echo $currency_row->code; ?>').text(formatMoney(gtotal_<?php echo $currency_row->code; ?>));

              $('#paid_amt_<?php echo $currency_row->code; ?>').val(formatDecimal(gtotal_<?php echo $currency_row->code; ?>));
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
        <?php if($pos_settings->discount_popup_screen_in_bill_print == 0) :?>
            requestBill(billid);
            return false;
        <?php endif; ?>  

        var ordersplit = $(this).parents('.payment-list-container').find('.order_split').val();
        var salesid = $(this).parents('.payment-list-container').find('.salesid').val(); 
        var grandtotal = $(this).parents('.payment-list-container').find('.grandtotal').val(); 
        var count = $(this).parents('.payment-list-container').find('.totalitems').val();
        var bbq_discount =  '<?php echo $this->Settings->bbq_discount; ?>';
        $url = '<?=admin_url().'pos/BBQcheckCustomerDiscount'?>';
        $.ajax({
            url: $url,
            type: "POST",
            data: {bill_id:billid},
            dataType: "json",
            success:function(data){
              if("<?php echo $Settings->bbq_discount ?>" == "bbq" ){
                if (!data.no_discount) {
                console.log(data);                 
                $dropdown = '<select id="choose-discount">';
                $dropdown +='<option value="0">No Discount</option>';
                $.each( data.all_dis, function( index, value ){
                    $selected = (data.cus_dis.customer_discount_id == value.id)?"selected='selected'":"";
                    $dropdown +='<option value="'+value.id+'" '+$selected+'>'+value.name+'</option>';
                });
                $dropdown +='</select>';
                $msg = "<div id='discount-confirm-msg'>Do You want to apply \"<span id='discount-name'>"+data.cus_dis.name+"</span>\"?</div>";
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
                             url: '<?=admin_url().'pos/BBQupdateBillDetails'?>',
                             type: "POST",
                             data: {bill_id:billid,dis_id:dis_id},
                             dataType: "json",
                             success:function(data){                               
                                
                             if (!data.no_discount) {
                                 $thisObj.parents('.payment-list-container').find('.grandtotal').val(data.amount);
                                 $thisObj.siblings('.grandtotal_req').val(data.amount);
                             }
                             requestBill(billid);
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
                $('.post-rough-tender').remove();
                $('.taxation_settings').show();

                <?php if($pos_settings->discount_popup_screen_in_payment == 0) :?>
                    payment_popup($thisObj);
                   return false;
               <?php endif; ?>

                
            }
            var billid = $(this).parents('.payment-list-container').find('.billid').val();
            var ordersplit = $(this).parents('.payment-list-container').find('.order_split').val();
            var salesid = $(this).parents('.payment-list-container').find('.salesid').val(); 
            var grandtotal = $(this).parents('.payment-list-container').find('.grandtotal').val(); 
            var count = $(this).parents('.payment-list-container').find('.totalitems').val();
            var bbq_discount =  '<?php echo $this->Settings->bbq_discount; ?>';
            $url = '<?=admin_url().'pos/BBQcheckCustomerDiscount'?>';
            $.ajax({
            url: $url,
            type: "POST",
            data: {bill_id:billid},
            dataType: "json",
            success:function(data){             
               
             if(bbq_discount == "bbq" ){
                if (!data.no_discount) {
                 console.log(data);                
                $dropdown = '<select id="choose-discount">';
                $dropdown +='<option value="0">No Discount</option>';

                $.each( data.all_dis, function( index, value ){
                    $selected = (data.cus_dis.customer_discount_id == value.id)?"selected='selected'":"";
                    $dropdown +='<option value="'+value.id+'" '+$selected+'>'+value.name+'</option>';
                });

                $dropdown +='</select>';
                if (data.cus_dis.customer_request_id != 0) {
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
                             url: '<?=admin_url().'pos/BBQupdateBillDetails'?>',
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
        
        $('#balance_<?php echo $currency_row->code; ?>').text(formatMoney(total_paying - (gtotal_<?php echo $currency_row->code; ?> * <?php echo $currency_row->rate; ?>) ),'<?php echo $currency_row->symbol; ?>');

        $('#balance_amt_<?php echo $currency_row->code; ?>').val(formatDecimal(total_paying - (gtotal_<?php echo $currency_row->code; ?> * <?php echo $currency_row->rate; ?>) ),'<?php echo $currency_row->symbol; ?>');

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
        <?php
        $getExchangesymbol = $this->site->getExchangeCurrency($this->Settings->default_currency);
        ?>
        var exchangeSymbol = '<?php echo $this->site->getExchangeCurrency($this->Settings->default_currency) ?>';
        $('#balance_USD_KHR').text(formatMoney(balance_USD_KHR, exchangeSymbol));
        
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
var $print_header_space = '<?=$pos_settings->pre_printed_header?>mm';
var $print_footer_space = '<?=$pos_settings->print_footer_space?>mm';
var pre_printed = '<?=$pos_settings->pre_printed_format?>';
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
                    url: '<?=admin_url('pos/bbqgatdata_print_billing');?>',
                    dataType: "html",
                    data: {
                        billid: billid
                    },
                    success: function (data) {
                        
                        if (data != '') {      
                            $('#bill-total-table').html(data);
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
    mywindow.document.write("<style type='text/css' media = 'print'>@page {margin: "+$print_header_space+" 5mm "+$print_footer_space+" 5mm;}</style>");
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
/*$('.kb-text').keyboard({
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
        });*/
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
                                    bill_totals += '<tr class="bold"><th class="text-right" colspan="4">'+lang.discount+'('+billData.billdata.discount_val+')</th><th   class="text-right">'+formatMoney(billData.billdata.total_discount)+'</th></tr>';
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

$('.crd_exp').datetimepicker({format: 'mm/yyyy', fontAwesome: true, todayBtn: 1, autoclose: 1, minView: 3,startDate: new Date(),viewMode : 'months',startView: "year", 
    minViewMode: "months" });    
</script>

</body>
</html>
