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
                
                    <!-- <div id="orderbilling_box">
                    
                    </div> -->
                    <!-- <div class="col-xs-12 kitchen_section">
    
    <ul>   
        <?php if($this->sma->actionPermissions('dinein_bils')){  ?>    
        <li><a href="<?php echo base_url().'admin/pos/order_biller/?type=1'; ?>" class="<?php if($sales_type == 'Dine In'){ ?>active <?php } ?>"><?=lang('dine_in')?></a></li>
        <?php } ?>
        
        <?php if($this->sma->actionPermissions('takeaway_bils')){  ?>
        <li><a href="<?php echo base_url().'admin/pos/order_biller?type=2'; ?>" class="<?php if($sales_type == 'Take Away'){ ?>active <?php } ?>"><?=lang('take_away')?></a></li>
        <?php } ?>
        
        <?php if($this->sma->actionPermissions('door_delivery_bils')){  ?>
        <li><a href="<?php echo base_url().'admin/pos/order_biller?type=3'; ?>" class="<?php if($sales_type == 'Door Delivery'){ ?>active <?php } ?>"><?=lang('door_delivery')?></a></li>
        <?php } ?>
        
     </ul>
  </div> -->

<div class="tableright col-xs-12">
      
      <div id="receiptData" class="col-lg-6 ">
            <div class="form-group  col-lg-3 date_div">
                <label for="method"><?php echo $this->lang->line("from_date"); ?></label>
                  <div class="controls ">
                    <input type="text" name="from_date" value="<?=@isset($_GET['date'])?$_GET['date']:date('Y-m-d');?>" class="form-control datetime" placeholder="From Date " id="from_date" required="required">
                  </div>
            </div>
	    <div class="form-group  col-lg-3">
                <label for="method"><?php echo $this->lang->line("bill_no"); ?></label>
                  <div class="controls ">
                    <input type="text" name="bill_no" value="<?=@$_GET['bill_no']?>" class="form-control kb-text" placeholder="<?=lang('bill_no')?> " id="bill_no">
                  </div>
            </div>
	    <div class="form-group  col-lg-3 date_div">
                <label for="method"><?php echo $this->lang->line("type"); ?></label>
                  <div class="controls ">
                    <select name="type" style="height:37px;" id="type">
			<option value="all" <?php if(@$_GET['type']=="all"){ echo 'selected="selected"';}?>>All</option>
			<option value="0" <?php if(@$_GET['type']=="0"){ echo 'selected="selected"';}?>>Print</option>
			<option value="1" <?php if(@$_GET['type']=="1"){ echo 'selected="selected"';}?>>Dont print</option>
		    </select>
                  </div>
            </div>
            <div class="form-group  col-lg-3 date_div" style="margin-top: 30px;">
              
                <button class="btn btn-block btn-danger" id="reprint-data"><?=lang('submit');?></button>
           
        </div>
    </div>

 <div class="col-xs-12"> 
 

        
        <?php
        if(!empty($sales)){
        ?>
        <ul>
            <?php
            foreach($sales as $sales_row){
                if($sales_row->sales_type_id == 4){
                    $img = 'dine_in.png';
                }elseif($sales_row->sales_type_id == 2){
                    $img = 'take_away.png';
                }elseif($sales_row->sales_type_id == 3){
                    $img = 'delivery.png';
                }
                $split_id = $sales_row->id;

            ?>
          <!--   <div style="clear:both; height:5px;"></div>
                          <div class="col-xs-12" style="padding: 0;"> -->
            <li class="col-md-12">
                <div class="row">

                    <div class="billing_list btn-block order-biller-table order_biller_table">
                    <?php if($sales_row->sales_type_id == 4){ ?>
                    <p class="bil_tab_nam"><?php echo $sales_row->areaname.' / '.$sales_row->tablename; } ?></p>
                    <h2><?php echo $sales_row->reference_no; ?></h2>
                   
                        <?php
                        $cancel_sale_status = $this->site->CancelSalescheckData($sales_row->id);
                        if($cancel_sale_status == TRUE){
                            if($this->sma->actionPermissions('bil_cancel')){ 
                        ?>
                        <div class="col-xs-12" style="padding: 0;">
                        <button type="button" class="btn btn3 padding3 cancel_bill btn-danger" style="height:40px;" id="">
                        &#10062;<?=lang('cancel_bill');?> 
                        </button>
                        <input type="hidden"  class="cancel_bill_id" value="<?php echo $sales_row->id; ?>">
                        </div>
                        <?php
                            }
                        
                        }
                        ?>
                    </div>
                    
                     <?php if(!empty($sales_row->bils)){
                        /*echo "<pre>";
                        print_r($sales_row->bils);die;*/
                        $k=1;
                        foreach($sales_row->bils as $split_order){
                            if (count($split_order->id) > 0) {
                            
                            ?>
                            <div class="col-xs-2 payment-list-container">
                             <h2 class="order-heading" style="margin-top: 0px;"> <?=lang('bill_no')?>: <?php echo $split_order->bill_number; ?></h2>
                             <div class="col-xs-12" style="padding: 0;">
                              <div class="btn-group-vertical btn-block">
                                <?php if($split_order->payment_status == NULL) { ?>
                               <!--  <button type="button" class="btn btn-danger btn_payment" style="height:40px;" <?php if($this->sma->actionPermissions('bil_payment')){ echo ''; }else{  echo 'disabled'; }  ?>>
                                    <i class="fa fa-money" ></i><?=lang('payment');?> 
                                </button> -->
                                <?php }
                                  else{
                                    ?>
                                    <!-- <button disabled="" type="button" class="btn btn-success " style="height:40px;" >
                                        <?php  echo $split_order->payment_status; ?>
                                    </button> -->
                                   
                                    <?php
                                    }?>
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

                                <input type="hidden"  class="totalitems" value="<?php echo $split_order->total_items; ?>">
                            </div>
                            
                            </div>
                             <div style="clear:both; height:5px;"></div>
                          <div class="col-xs-12" style="padding: 0;">
                            <div class="btn-group-vertical btn-block">
                           
                                
                                 
                                 <button type="button" class="btn btn-primary btn-block request_bil" 
                                 data-bil="req_<?=$k; ?>" style="height:40px;" id="" <?php if($this->sma->actionPermissions('bil_print')){ echo ''; }else{  echo 'disabled'; }  ?>>
                                <i class="fa fa-print" ></i><?=lang('bill_reprint');?> 
                                </button>
                                
                                <input type="hidden"  class="billid_req" value="<?php echo $split_order->id; ?>">

                                <input type="hidden"  class="order_split_req" value="<?php echo $sales_row->sales_split_id; ?>">

                                <input type="hidden"  class="salesid_req" value="<?php echo $split_order->sales_id; ?>">
                                <?php 
                                if ($split_order->tax_type == 0)
                                {
                                    $grandtotal = $split_order->total-$split_order->total_discount;
                                }
                                else{
                                    $grandtotal = $split_order->total-$split_order->total_discount+$split_order->total_tax;
                                }
                                ?>
                                <input type="hidden"  class="grandtotal_req" value="<?php echo $grandtotal; ?>">

                                <input type="hidden"  class="totalitems_req" value="<?php echo $split_order->total_items; ?>">
                                
                               
                                <div id="req_<?=$k;?>">                            
                               <button type="button" data-sp="split_<?=$k;?>" class="btn btn-primary btn-block print_bill" value="<?php echo $split_order->id; ?>" style="height:40px; overflow:hidden; visibility:hidden;"  <?php if($this->sma->actionPermissions('bil_print')){ echo ''; }else{  echo 'disabled'; }  ?>>
                                <i class="fa fa-print" ></i><?=lang('sale_bill');?> 
                                </button>
                                 <input type="hidden" id="split_<?=$k;?>"  class="bill_print" value="<?php echo $split_order->id; ?>">
                                </div>
                                
                                
                            </div>
                            </div>
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
/*$(document).ready(ajaxData(<?php echo $type; ?>));
var ajaxDatatimeout = setInterval(ajaxData(<?php echo $type; ?>), 1000);
*/
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
                        <input type="hidden" name="total" id="total" class="total" />
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
                        <div id="payments">
                            <div class="well well-sm well_1" style="padding: 5px 10px;">
                                <div class="payment">
                                    <div class="row">
                                        
                                        <div class="col-sm-12">
                                            <div class="form-group"  style="margin-bottom: 5px;">
                                                <?=lang("paying_by", "paid_by_1");?>
                                                <select name="paid_by[]" id="paid_by_1" class="form-control paid_by">
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
            var ordersplit = $thisObj.siblings('.order_split').val();
            var salesid = $thisObj.siblings('.salesid').val(); 
            var grandtotal = $thisObj.siblings('.grandtotal').val();
	    console.log(grandtotal)
            var count = $thisObj.siblings('.totalitems').val(); 
            $('.bill_id').val(billid);
            $('.sales_id').val(salesid);
            $('.total').val(grandtotal);
            $('.order_split_id').val(ordersplit);
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

        var url = '<?php echo  admin_url('pos/bbq_reprint_view') ?>';
        window.location.href= url +'/?bill_id='+billid;

		// $url = '<?=admin_url().'pos/checkCustomerDiscount'?>';
		
		/*$.ajax({
			url: $url,
			type: "POST",
			data: {bill_id:billid},
			dataType: "json",
			success:function(data){
			    
			    if (!data.no_discount) {
			       console.log(data);
			       if(data.cus_dis.customer_discount_status=="pending") {
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
					// message: "Do You want to apply '"+data.cus_dis.name+"'?",
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
						     url: '<?=admin_url().'pos/updateBillDetails'?>',
						     type: "POST",
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
			    }else{ 
				
				requestBill(billid)}
			    
			}
		    });*/
	    });
	    $(document).on('click', '.btn_payment', function(){
			
            $('#paymentModal').appendTo("body").modal('show');            
            var billid = $(this).siblings('.billid').val(); 
            var ordersplit = $(this).siblings('.order_split').val();
            var salesid = $(this).siblings('.salesid').val(); 
            var grandtotal = $(this).siblings('.grandtotal').val(); 
            var count = $(this).siblings('.totalitems').val(); 
            $('.bill_id').val(billid);
            $('.sales_id').val(salesid);
            $('.total').val(grandtotal);
            $('.order_split_id').val(ordersplit);
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
			$('#amount_<?php echo $currency_row->code; ?>_1').focus().val('');
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
                var phtml = $('#payments').html(),
                    update_html = phtml.replace(/_1/g, '_' + pa);
				
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
						' 9 0 {b}',
			
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
                    url: '<?=admin_url('pos/bbqpaidgatdata_print_billing');?>',
                    dataType: "html",
                    data: {
                        billid: billid
                    },
                    success: function (data) { //console.log(data)
                    if (data != '') {      
                                                    
                                $('#bill-total-table').html(data);
                                Popup($('#bill_tbl').html());                               
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


<script type="text/javascript">

    $('#reprint-data').click(function () {
            var from_date = $('#from_date').val();
	    bill_no = $('#bill_no').val();
	    type = $('#type').val(); 
            var url = '<?php echo  admin_url('pos/bbqreprinter') ?>';
            window.location.href= url +'/?date='+from_date+'&bill_no='+bill_no+'&type='+type;  
    });

$('.sortable_table tbody').sortable({
    containerSelector: 'tr'
});

  // $var = '<?php echo '-'.$pos_settings->reprint_from_last_day ?>';
    $("#from_date").datepicker({
         minDate: '<?php echo '-'.$pos_settings->reprint_from_last_day ?>',
         maxDate: 0,
         dateFormat: 'yy-mm-dd',
    });

</script>


</body>
</html>
