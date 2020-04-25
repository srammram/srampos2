<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?= "Login " . $Settings->site_name;?></title>
    <script type="text/javascript">if(parent.frames.length !== 0){top.location = '<?=admin_url('pos')?>';}</script>
    <base href="<?=base_url()?>"/>
    <meta http-equiv="cache-control" content="max-age=0"/>
    <meta http-equiv="cache-control" content="no-cache"/>
    <meta http-equiv="expires" content="0"/>
    <meta http-equiv="pragma" content="no-cache"/>
    <link rel="stylesheet" href="<?=$assets?>fonts/barlow_condensed/stylesheet.css" type="text/css">
    <link rel="shortcut icon" href="<?=$assets?>images/favicon.ico"/>
   	<link rel="stylesheet" href="<?=$assets?>styles/helpers/bootstrap.min.css" type="text/css">
	    
   	<link rel="stylesheet" href="<?=$assets?>styles/helpers/font-awesome.min.css" type="text/css">
   	<link rel="stylesheet" href="<?=$assets?>styles/jquery.mCustomScrollbar.css" type="text/css">
   	<link rel="stylesheet" href="<?=$assets?>styles/helpers/jquery-ui.css" type="text/css">
   	<link rel="stylesheet" href="<?=$assets?>styles/helpers/select2.min.css" type="text/css">
	<link rel="stylesheet" href="<?=$assets?>styles/theme.css" type="text/css"/>
   	<link rel="stylesheet" href="<?=$assets?>styles/home_frontend.css" type="text/css">
	   <script type="text/javascript" src="<?=$assets?>js/jquery-2.0.3.min.js"></script>
    <script type="text/javascript" src="<?=$assets?>js/jquery-migrate-1.2.1.min.js"></script>
	<link href="<?= $assets ?>styles/flipclock.css" rel="stylesheet"/>
	
    <script type="text/javascript" src="<?= $assets ?>js/flipclock.min.js"></script>
	<style>
	.tablebtn{width: 120px;
    height: 34px;
    padding: 0px;
    background-color: #d6311a;
    color: #fff;
    font-family: 'barlow_condensedregular';
    font-size: 20px;
    font-weight: normal;
    border: 1px solid #d6311a;
	}
		.menu_nav li button,.menu_nav li figure{width: 10.7%;}
	</style>
    
</head>
<body>
	<header class="logo_header">
		<div class="container-fluid">
			<div class="row">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<a href="<?php echo base_url('pos/pos/'); ?>"><img src="<?=$assets?>images/srampos.png" alt=""></a>
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
						<a href="<?php echo base_url('/pos/qsr/')  ?>">
							<button class="pull-right">
							
								<img src="<?=$assets?>images/sprite/back.png">
								<figcaption>Back</figcaption>
							
							</button>
								</a>
						</li>
					</ul>
				</div>
			</div>
			
		</div>
	</section>
	<section class="drop_down_list">
		<div class="container">
			<div class="row">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 drop_down_list_s" style="padding: 0px;">
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
						<div class="form-group">
							<label for="" class="col-sm-4">Date</label>
							<div class="col-sm-7">
								<input type="text" class="form-control" placeholder="Date">
							</div>
						</div>
					</div>
					<div class="col-lg-4 col-md-8 col-sm-8 col-xs-12">
						<div class="form-group">
							<input type="button" class="btn btn-danger" value="Submit">
						</div>
					</div>
				</div>
			</div>
				<div class="row">
   		
			<table class="table">
		  <?php  if(!empty($sales)){ 
            foreach($sales as $sales_row){ ?>
            <tr>
             <td style="color:#fff;"> <?=lang('bill_no')?>: <?php echo $sales_row->bill_number; ?> </td>
		     <td style="color:#fff;"><?php echo $sales_row->reference_no; ?></td>
	         <td style="color:#fff;"> 
                 <button type="button" class="tablebtn cancel_bill" ><?=lang('cancel_bill');?> </button>
				 <button type="button" class="tablebtn resettle-payment" id="resettle-payment" <?php if($this->sma->actionPermissions('bil_payment')){ echo ''; }else{  echo 'disabled'; }  ?> >
                                      <?php  echo lang('resettle_bill'); ?>                             </button>
				 <input type="hidden"  class="billid_req" value="<?php echo $sales_row->id; ?>">
               <input type="hidden"  class="cancel_bill_id" value="<?php echo $sales_row->id; ?>">
		       <input type="hidden"  class="cancel_sale_id" value="<?php echo $sales_row->sales_id; ?>">
                 </td>
            </tr>
            <?php
            }
            ?>
        
        <?php
        }else{
        ?>
        <div class="col-sm-6 col-sm-offset-3 col-xs-12 order_cancel_data alert-danger fade in"> <?=lang('no_record_found')?> </div>
        <?php
        }
        ?>
		</table>
   		</div>
   		<div class="row">
   			<div class="row">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-left payment_s">
					<ul>
						<li>
							<div class="col-sm-12 col-xs-12 pay_li_group">
								<div class="col-sm-6 col-xs-12 payment_list_container_li">
									<table class="payment-list-container">
									<tr>
										<td>
											<p>GROUND FLOOR/ TABLE 2</p>
											<p>SALES 20191215141246</p>
										</td>
										<td>
											<button type="button" class="btn">
												<figure class="text-center">
													<img src="<?=$assets?>images/sprite/payment_y.png">
													<figcaption>Payment</figcaption>
												</figure>
											</button>
										</td>
										<td>
											<button type="button" class="btn">
												<figure class="text-center">
													<img src="<?=$assets?>images/sprite/invoice.png">
													<figcaption>Rough Tender</figcaption>
												</figure>
											</button>

										</td>
										<td>
											<button type="button" class="btn">
												<figure class="text-center">
													<img src="<?=$assets?>images/sprite/print.png">
													<figcaption>Bill Print</figcaption>
												</figure>
											</button>
										</td>
										<td>
										   <button type="button" class="btn">
												<figure class="text-center">
													<img src="<?=$assets?>images/sprite/cancel_all.png">
													<figcaption>Cancel Bill</figcaption>
												</figure>
											</button>
										</td>
									</tr>
									</table>
								</div>
								<div class="col-sm-6 col-xs-12 payment_list_container_li">
									<table class="payment-list-container">
									<tr>
										<td>
											<p>GROUND FLOOR/ TABLE 2</p>
											<p>SALES 20191215141246</p>
										</td>
										<td>
											<button type="button" class="btn">
												<figure class="text-center">
													<img src="<?=$assets?>images/sprite/payment_y.png">
													<figcaption>Payment</figcaption>
												</figure>
											</button>
										</td>
										<td>
											<button type="button" class="btn">
												<figure class="text-center">
													<img src="<?=$assets?>images/sprite/invoice.png">
													<figcaption>Rough Tender</figcaption>
												</figure>
											</button>

										</td>
										<td>
											<button type="button" class="btn">
												<figure class="text-center">
													<img src="<?=$assets?>images/sprite/print.png">
													<figcaption>Bill Print</figcaption>
												</figure>
											</button>
										</td>
										<td>
										   <button type="button" class="btn">
												<figure class="text-center">
													<img src="<?=$assets?>images/sprite/cancel_all.png">
													<figcaption>Cancel Bill</figcaption>
												</figure>
											</button>
										</td>
									</tr>
									</table>
								</div>
								<div class="col-sm-6 col-xs-12 payment_list_container_li">
									<table class="payment-list-container">
									<tr>
										<td>
											<p>GROUND FLOOR/ TABLE 2</p>
											<p>SALES 20191215141246</p>
										</td>
										<td>
											<button type="button" class="btn">
												<figure class="text-center">
													<img src="<?=$assets?>images/sprite/payment_y.png">
													<figcaption>Payment</figcaption>
												</figure>
											</button>
										</td>
										<td>
											<button type="button" class="btn">
												<figure class="text-center">
													<img src="<?=$assets?>images/sprite/invoice.png">
													<figcaption>Rough Tender</figcaption>
												</figure>
											</button>

										</td>
										<td>
											<button type="button" class="btn">
												<figure class="text-center">
													<img src="<?=$assets?>images/sprite/print.png">
													<figcaption>Bill Print</figcaption>
												</figure>
											</button>
										</td>
										<td>
										   <button type="button" class="btn">
												<figure class="text-center">
													<img src="<?=$assets?>images/sprite/cancel_all.png">
													<figcaption>Cancel Bill</figcaption>
												</figure>
											</button>
										</td>
									</tr>
									</table>
								</div>
								<div class="col-sm-6 col-xs-12 payment_list_container_li">
									<table class="payment-list-container">
									<tr>
										<td>
											<p>GROUND FLOOR/ TABLE 2</p>
											<p>SALES 20191215141246</p>
										</td>
										<td>
											<button type="button" class="btn">
												<figure class="text-center">
													<img src="<?=$assets?>images/sprite/payment_y.png">
													<figcaption>Payment</figcaption>
												</figure>
											</button>
										</td>
										<td>
											<button type="button" class="btn">
												<figure class="text-center">
													<img src="<?=$assets?>images/sprite/invoice.png">
													<figcaption>Rough Tender</figcaption>
												</figure>
											</button>

										</td>
										<td>
											<button type="button" class="btn">
												<figure class="text-center">
													<img src="<?=$assets?>images/sprite/print.png">
													<figcaption>Bill Print</figcaption>
												</figure>
											</button>
										</td>
										<td>
										   <button type="button" class="btn">
												<figure class="text-center">
													<img src="<?=$assets?>images/sprite/cancel_all.png">
													<figcaption>Cancel Bill</figcaption>
												</figure>
											</button>
										</td>
									</tr>
									</table>
								</div>
							</div>
							
						</li>
						
						
					</ul>
				</div>
			</div>
			</div>
   	</div>
    	</div>
	</section>


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

<script>
 
   $( '.cancel_bill' ).on( 'click', function (e) { 
        e.preventDefault();
        var cancel_id = $(this).siblings('.cancel_sale_id').val();
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
	
$(document).on('click','#cancel_paymentbill',function(){
     var cancel_remarks = $('#remarks').val(); 
     var sale_id = $('#sale_id').val(); 
     if($.trim(cancel_remarks) != ''){
        
        $.ajax({
            type: "get",
            url:"<?= base_url('pos/qsr/cancel_sale');?>",                
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
 </script>

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
        var url = '<?php echo  base_url('pos/qsr/reprint_view') ?>';
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


<script type="text/javascript">

    $('#reprint-data').click(function () {
            var from_date = $('#from_date').val();             
            var url = '<?php echo  base_url('pos/qsr/cancel_bill') ?>';
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