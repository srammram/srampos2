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
                    <button type="button" class="btn btn-primary btn-block request_bil" 
                                 data-bil="req_<?=$k; ?>" style="height:40px;" id="" <?php /*if($this->sma->actionPermissions('bil_print')){ echo ''; }else{  echo 'disabled'; } */ ?>>
                                <i class="fa fa-print" ></i><?=lang('bill_reprint');?> 
                  </button>

                        <?php
                       /* $cancel_sale_status = $this->site->CancelSalescheckData($sales_row->id);
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
                        
                        }*/
                        ?>
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
	 
        <?php if ($pos_settings->tooltips) {echo '$(".pos-tip").tooltip();';}
        ?>

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
                    success: function (data) {console.log(data)
                    if (data != '') {      
                     var bill_totals = '';
                       var bill_head ='' ;

                         bill_head += (data.biller.logo != "test") ? '<div id="wrapper1"><div id="receiptData"><div id="receipt-datareceipt-data"><div class="text-center"><img  src='+base_url+'assets/uploads/logos/'+data.biller.logo +' alt="" >': "";

                         bill_head += '<h3 style="text-transform:uppercase;">'+data.biller.company+'</h3>';
						
                         bill_head += '<p>'+data.biller.address+"  "+data.biller.city+" "+data.biller.postal_code+"  "+data.biller.state+"  "+data.biller.country+'<br>'+'<?= lang('tel'); ?>'+': '+data.biller.phone+'</p><br><?= lang('copy_bill'); ?></div>';
						
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
							
                                bill_totals += '<tbody><tr><td colspan="2" class="no-border">'+r+': &nbsp;&nbsp'+ recipe_name+ '</td><td class="no-border">'+ formatMoney(b.net_unit_price) +'</td><td class="no-border">'+ formatDecimal(b.quantity) +'</td><td class="no-border text-right">'+ formatMoney(b.subtotal) +'</td></tr></tbody>';
                            });


							 bill_totals += '<tfoot><tr class="bold"><th colspan="4" class="text-right">'+'<?=lang("items");?>'+'</th><th  class="text-right">'+formatDecimal(data.billdata.total_items)+'</th></tr>';
							 
							 
                            bill_totals += '<tr class="bold"><th colspan="4" class="text-right">'+'<?=lang("total");?>'+'</th><th   class="text-right">'+formatMoney(data.billdata.total)+'</th></tr>';
							

                            if(data.billdata.total_discount > 0) {
                                    bill_totals += '<tr class="bold"><th class="text-right" colspan="4">'+data.discount+'</th><th   class="text-right">'+formatMoney(data.billdata.total_discount)+'</th></tr>';
                                }
							<?php if($pos_settings->display_tax==1) : ?>	
							 if (data.billdata.tax_rate != 0) {
                                    //bill_totals += '<tr class="bold"><th colspan="4" class="text-right" >Tax ('+data.billdata.tax_name+') </th><th    class="text-right">'+formatMoney(data.billdata.total_tax)+'</th></tr>';
				    $taxtype = '<?=lang('tax_exclusive')?> '+ data.billdata.tax_name;
				    if(data.billdata.tax_type==0){
				    $taxtype = '<?=lang('tax_inclusive')?> '+data.billdata.tax_name;
				    }
									bill_totals += '<tr class="bold"><th colspan="5" class="text-right" >'+$taxtype+'  </th></tr>';
                                }
				<?php endif; ?>
                           $grandTotal =data.billdata.grand_total;
			   if(data.billdata.tax_type==0){
			    $grandTotal = parseFloat(data.billdata.grand_total) + parseFloat(data.billdata.total_tax);
			   }
                            bill_totals += '<tr class="bold"><th colspan="4" class="text-right" >'+lang.grand_total+'</th><th colspan="2"  class="text-right">'+formatMoney($grandTotal)+'</th></tr></tfoot></table>';

                                $('#bill_header').empty();
                                $('#bill_header').append(bill_head);

                                $('#bill-total-table').empty();                                
                                $('#bill-total-table').append(bill_totals);
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
            var url = '<?php echo  admin_url('qsr/reprinter') ?>';
            window.location.href= url +'/?date='+from_date;  
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
