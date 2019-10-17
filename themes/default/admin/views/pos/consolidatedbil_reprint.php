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
	    .available-c-limit{
		color:#f00;
	    }
	    #payment-customer-name{
		float: right;
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
                
                    <div id="orderbilling_box">
                    	
                        
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
                    $img = 'dine_in.png';
                
                $split_id = $sales_row->id;

            ?>
          <!--   <div style="clear:both; height:5px;"></div>
                          <div class="col-xs-12" style="padding: 0;"> -->
            <li class="col-md-12">
                <div class="row">

                    <div class="billing_list btn-block order-biller-table order_biller_table">
                   
                    <p class="bil_tab_nam"><?php echo $sales_row->areaname.' / '.$sales_row->tablename;  ?></p>
                    <h2><?php echo $sales_row->reference_no; ?></h2>
                   
                        
                    </div>
                    <div class=" col-lg-3">
                   
                      <?php if(!empty($sales_row->bils)){
                        /*echo "<pre>";
                        print_r($sales_row->bils);*/                      
						$k=1;
                        foreach($sales_row->bils as $split_order){
                            if (count($split_order->id) > 0) {
                       			$grand_total[$sales_row->sales_split_id][] = $split_order->grand_total;
                            ?>
                             
                              <h2 class="order-heading" style="margin-top: 0px; width:50%;"> <?=lang('bill_no')?>: <?php echo $split_order->bill_number; ?></h2>							
                              <input type="hidden"  class="billid_<?=$k?>" value="<?php echo $split_order->id; ?>">
                              <input type="hidden"  class="order_split_<?=$k?>" value="<?php echo $sales_row->sales_split_id; ?>">
                              <input type="hidden"  class="salesid_<?=$k?>" value="<?php echo $split_order->sales_id; ?>">
                              <input type="hidden"  class="credit-limit_<?=$k?>" value="<?php echo $split_order->credit_limit; ?>">
                            <input type="hidden"  class="company-id_<?=$k?>" value="<?php echo $split_order->company_id; ?>">
                            <input type="hidden"  class="customer-type_<?=$k?>" value="<?php echo $split_order->customer_type; ?>">
                            <input type="hidden"  class="customer-id_<?=$k?>" value="<?php echo $split_order->customer_id; ?>">
                            <input type="hidden"  class="customer-name_<?=$k?>" value="<?php echo $split_order->customer_name; ?>">
                            
                            <input type="hidden"  class="billid_req_<?=$k?>" value="<?php echo $split_order->id; ?>">
                            <input type="hidden"  class="order_split_req_<?=$k?>" value="<?php echo $sales_row->sales_split_id; ?>">
                            <input type="hidden"  class="salesid_req_<?=$k?>" value="<?php echo $split_order->sales_id; ?>">
                              
                              <!--<p><?=lang('total_items_/_covers')?>: <?php echo $split_order->total_items; ?></p>    
                              <p><?=lang('total')?>: <?php echo $split_order->grand_total; ?></p>  -->                                   
                            <?php 
                        }
                        $k++;
						}
                     }
                ?>
                
                    <button type="button" class="btn btn-primary btn-block request_bil" bill-no = "<?=$split_order->bill_number; ?>" data-split="<?=$sales_row->sales_split_id; ?>"  data-bil="req_<?=$sales_row->sales_split_id; ?>" style="height:40px;" id="" <?php if($this->sma->actionPermissions('bil_print')){ echo ''; }else{  echo 'disabled'; }  ?>>
                    <i class="fa fa-print" ></i><?=lang('bill_reprint');?> 
                    </button>
                   
                	
                	<!--<h3>Total : <?php echo array_sum($grand_total[$sales_row->sales_split_id]); ?></h3>-->
                    <input type="hidden"  class="grandtotal" value="<?php echo array_sum($grand_total[$sales_row->sales_split_id]); ?>">
                    <input type="hidden"  class="grandtotal_req" value="<?php echo array_sum($grand_total[$sales_row->sales_split_id]); ?>">
                    
                    
                    
                	</div>
                
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



<?php
$currency = $this->site->getAllCurrencies();
?>


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
            var billid = $thisObj.siblings('.billid_1').val(); 
            var ordersplit = $thisObj.siblings('.order_split_1').val();
            var salesid = $thisObj.siblings('.salesid_1').val(); 
            var grandtotal = $thisObj.siblings('.grandtotal').val();
	    var credit_limit = $thisObj.siblings('.credit-limit_').val();
	    console.log(credit_limit)
            var count = $thisObj.siblings('.totalitems').val(); 
            $('.bill_id').val(billid);
            $('.sales_id').val(salesid);
            $('.total').val(grandtotal);
            $('.order_split_id').val(ordersplit);
	    $('.credit_limit').val(credit_limit);
            var twt = formatDecimal(grandtotal);
	    console.log('grandtotal-'+grandtotal)
            console.log('bil-'+billid);
            /*if (count == 0) {
                bootbox.alert('<?=lang('x_total');?>');
                return false;
            }*/
			
			
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
	    
	    $(document).on('click', '.request_bil_1', function(){
		$thisObj = $(this);
		var billid = $(this).siblings('.billid_req_1').val(); 
		var ordersplit = $(this).siblings('.order_split_req_1').val();
		var salesid = $(this).siblings('.salesid_req_1').val(); 
		var grandtotal = $(this).siblings('.grandtotal_req_1').val(); 
		var count = $(this).siblings('.totalitems_req_1').val();
		var bilnumber = $(this).attr('data-bil');
					
		$url = '<?=admin_url().'pos/checkCustomerDiscount'?>';
		$.ajax({
			url: $url,
			type: "POST",
			data: {bill_id:billid},
			dataType: "json",
			success:function(data){
			    
			    if (!data.no_discount) {
			       console.log(data);
			       if(data.customer_discount_status=="pending") {
				
				    bootbox.confirm({
					 message: "Do You want to apply '"+data.name+"'?",
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
					    console.log(result)
					     if (result) {		
                         alert(result);			   
						 $.ajax({
						     url: '<?=admin_url().'pos/updateBillDetails'?>',
						     type: "POST",
						     data: {bill_id:billid},
						     dataType: "json",
						     success:function(data){
								 
									//requestBill(billid);
						     }
						 });
					     }else{
								//requestBill(billid);
							}
					     
						}
				     });
				    return false;
			       }else{
					   
								//requestBill(billid);
					}
			    }else{
					
								//requestBill(billid);
				}
			    
			}
		    });
	    });
		
	    $(document).on('change', '#choose-discount', function(){
		$('#discount-name').text($('#choose-discount option:selected').text());
	    });
	    $(document).on('click', '.request_bil', function(){
		$thisObj = $(this);
		var billid = $(this).parents('.payment-list-container').find('.billid').val();        
        var ordersplit = $(this).attr('data-split');        
		var bill_no = $(this).attr('bill-no');       
        // alert(bill_no);
		var salesid = $(this).parents('.payment-list-container').find('.salesid').val(); 
		var grandtotal = $(this).parents('.payment-list-container').find('.grandtotal').val(); 
		var count = $(this).parents('.payment-list-container').find('.totalitems').val();
        var url = '<?php echo  admin_url('pos/consolidated_reprint_view') ?>';
        window.location.href= url +'/?bill_no='+bill_no;

		/*$url = '<?=admin_url().'pos/checkCustomerDiscount'?>';
		requestBill(ordersplit)*/
		/*$.ajax({
			url: $url,
			type: "POST",
			data: {bill_id:ordersplit},
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
                                
                                //$('.grandtotal').val(data);
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
		    });*/
	    });
		
		$(document).on('click', '.request_bil_new', function(){
			
			$thisObj = $(this);
			var billid = $(this).siblings('.billid_1').val(); 
            var ordersplit = $(this).siblings('.order_split_1').val();
            var salesid = $(this).siblings('.salesid_1').val(); 
            var grandtotal = $(this).siblings('.grandtotal').val(); 
            var count = $(this).siblings('.totalitems').val();
	    var credit_limit =  $(this).siblings('.credit-limit_1').val();
	    var customer_type =  $(this).siblings('.customer-type_1').val();
	    var company_id =  $(this).siblings('.company-id_1').val();
	    var customer_id =  $(this).siblings('.customer-id_1').val();
		
			//alert(ordersplit);
			$url = '<?=admin_url().'pos/CONcheckCustomerDiscount'?>';
			$.ajax({
			url: $url,
			type: "POST",
			data: {ordersplit:ordersplit},
			dataType: "json",
			success:function(data){
			    
			    if (!data.no_discount) {
			       console.log(data);
			       
				$dropdown = '<label>Customer Discount</label><br><select id="customer-discount">';
				$dropdown +='<option value="0">No Discount</option>';
				if(data.all_cus_dis){
				    $.each( data.all_cus_dis, function( index, value ){
					$selected = (data.cus_dis.customer_discount_id == value.id)?"selected='selected'":"";
					$dropdown +='<option value="'+value.id+'" '+$selected+'>'+value.name+'</option>';
				    });
				}
				$dropdown +='</select><br>';
				
				$dropdown1 = '<label>BBQ Discount</label><br><select id="bbq-discount">';
				$dropdown1 +='<option value="0">No Discount</option>';
				if(data.all_bbq_dis){
				    $.each( data.all_bbq_dis, function( index, value ){
				    $selected = (data.bbq_dis.customer_discount_id == value.id)?"selected='selected'":"";
				    $dropdown1 +='<option value="'+value.id+'" '+$selected+'>'+value.name+'</option>';
				});
				}
				$dropdown1 +='</select>';
				
				
				/*if (data.cus_dis.customer_discount_id != 0) {
					$msg = "<div id='discount-confirm-msg'>Do You want to apply \"<span id='discount-name'>"+data.cus_dis.name+"</span>\"?</div>";
				} else {
					$msg = "<div id='discount-confirm-msg'>Do You want to apply any discount?</div>";
				}*/
				
				
				    bootbox.confirm({
					message: $dropdown+$dropdown1,
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
						cus_dis_id  = $('#customer-discount').val();
						bbq_dis_id  = $('#bbq-discount').val();
						 $.ajax({
						     url: '<?=admin_url().'pos/CONupdateBillDetails'?>',
						     type: "GET",
						     data: {ordersplit:ordersplit,cus_dis_id:cus_dis_id, bbq_dis_id:bbq_dis_id},
						     dataType: "json",
						     success:function(data){
                                
                                //$('.grandtotal').val(data.amount);
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
			    
			}
		    });
		});
		
	    $(document).on('click', '.btn_payment', function(){
			
			
            $('#paymentModal').appendTo("body").modal('show');            
            var billid = $(this).siblings('.billid_1').val(); 
            var ordersplit = $(this).siblings('.order_split_1').val();
            var salesid = $(this).siblings('.salesid_1').val(); 
            var grandtotal = $(this).siblings('.grandtotal').val(); 
            var count = $(this).siblings('.totalitems').val();
	    var credit_limit =  $(this).siblings('.credit-limit_1').val();
	    var customer_type =  $(this).siblings('.customer-type_1').val();
	    var company_id =  $(this).siblings('.company-id_1').val();
	    var customer_id =  $(this).siblings('.customer-id_1').val();
		
	    $('#payment-customer-name').text('Customer : '+$(this).siblings('.customer-name_1').val());
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
				update_html= update_html.replace(/data-index="1"/g,'data-index="'+pa+'"')
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
		 $amount =  $('#balance_USD').text();
		 $amount = parseFloat($amount.match(/([0-9]*\.[0-9]+|[0-9]+)/));
		 
		    
		    $('#amount_USD_2').val($amount)
		 
		 
		 
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

        $(document).on('change', 'select.paid_by', function () {
            $('#clear-cash-notes').click();
			$index = $( this ).attr('data-index');
			<?php
			foreach($currency as $currency_row){
			$default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
			if($currency_row->code == $default_currency_data->code){
			?>
			$('#amount_<?php echo $currency_row->code; ?>_'+$index).val('');
			<?php
			}else{
			?>
			$('#amount_<?php echo $currency_row->code; ?>_'+$index).val('');
			
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
				$(this).parents('.payment').find('input').val('');
			$('#pcc_month_'+$index).prepend('<option value="">Month</option>');
			$('#pcc_year_'+$index).prepend('<option value="">Year</option>');
			$('#pcc_month_'+$index).val('');
			$('#pcc_year_'+$index).val('');
			$amount = $('#balance_USD').text();
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
			console.log($amount)
			if($amount>0){$('#amount_USD_'+$index).val($amount)};
			
			
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
		 function requestBill(ordersplit){
			
            var base_url = '<?php echo base_url(); ?>';
            //var billid = $(this).val(); 
			
			//var billid = $(this).val(); 
			//alert(billid);
			//alert(ordersplit);
            if (ordersplit != '') {
                $.ajax({
                    type: 'get',
                    async: false,                    
                    ContentType: "application/json",
                    url: '<?=admin_url('pos/bbqcondata_print_billing');?>',
                    dataType: "html",
                    data: {
                        ordersplit: ordersplit
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
        mywindow.document.write('<link rel="stylesheet" href="<?=$assets?>styles/helpers/bootstrap.min.css" type="text/css" />');
        mywindow.document.write('</head><body >');
        mywindow.document.write(data);
        mywindow.document.write('</body></html>');
        mywindow.print();
        // mywindow.close();
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

<script>

    $("#from_date").datepicker({
         minDate: '<?php echo '-'.$pos_settings->reprint_from_last_day ?>',
         maxDate: 0,
         dateFormat: 'yy-mm-dd',
    });

    $('#reprint-data').click(function () {
            var from_date = $('#from_date').val();
	    bill_no = $('#bill_no').val();
	    type = $('#type').val(); 
            var url = '<?php echo  admin_url('pos/consolidatedreprinter') ?>';
            window.location.href= url +'/?date='+from_date+'&bill_no='+bill_no+'&type='+type;  
    });
</script>


</body>
</html>
