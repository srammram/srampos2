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
    <style type="text/css">
        #OpenregisterModal{
            z-index: 99999;
        }
        .bootbox-alert
        {
            z-index: 99999;
        }
    </style>
    <script>var curr_page="pos_type";</script>
    <?php if(@$_GET['tid'] && isset($_SERVER['HTTP_REFERER'])): ?>
        <script>var curr_func="update_tables";var tableid = '<?=$_GET['tid']?>';</script>
    <?php endif; ?>
    <?php if(@$_GET['bbqtid'] && isset($_SERVER['HTTP_REFERER'])): ?>
        <script>var curr_func="update_bbqtables";var tableid = '<?=$_GET['bbqtid']?>';</script>	
    <?php endif; ?>
    <script>
       var siteurl = "<?=site_url()?>";
</script>
    <?php if(!$isTransactiondateSet) : ?>
<script type="text/javascript" src="<?=$assets?>pos/js/nightaudit_date.js"></script>
<?php endif; ?>
<?php if($nightaudit_alert = $this->session->flashdata('nightaudit_alert')) : ?>
<script>
    $(document).ready(function(){
	bootbox.alert({
	    message: "<?=$nightaudit_alert?>",
	    size: 'large',
	    className:'nightaudit-alert'
	});
    })
    
</script>
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



<div id="wrapper" class="order_type">
	
   <div class="col-md-3 col-md-offset-9"> 
  	<ul class="flash_btn">
    
  	<li><a class="req_status btn btn-default pull-left" href="<?= admin_url('pos/order_biller/?type=1'); ?>" data-notify-type="notice"><?=lang('request_bil')?></a>
    <input type="hidden" name="rep_count" id="rep_count" value=""><span class="req_sound"></span></li>
    
   <li class="dropdown lanfuage_str" style="    z-index: 9999999; top:0px; right:30%;">
        <a class="btn tip" title="<?= lang('language') ?>" data-placement="bottom" data-toggle="dropdown"
           href="#">
            <button type="button" class="btn btn-default pull-left" ><img src="<?= base_url('assets/images/' . $Settings->user_language . '.png'); ?>" alt=""></button>
        </a>
        <ul class="dropdown-menu pull-right">
            <?php $scanned_lang_dir = array_map(function ($path) {
                return basename($path);
            }, glob(APPPATH . 'language/*', GLOB_ONLYDIR));
            foreach ($scanned_lang_dir as $entry) { 
                if($entry == 'english' || $entry == 'khmer'){
            ?>
                <li>
                    <a href="<?= admin_url('welcome/language/' . $entry); ?>">
                        <img src="<?= base_url('assets/images/'.$entry.'.png'); ?>" class="language-img">
                        &nbsp;&nbsp;<?= ucwords($entry); ?>
                    </a>
                </li>
            <?php } } ?>
          
           
        </ul>
    </li>
    
   <li><a href="<?php echo site_url('frontend/logout'); ?>" onClick=" localStorage.clear(); "><button type="button" class="btn btn-default pull-left" title="Log Out"><i class="fa fa-sign-out" aria-hidden="true"></i> <?=lang('logout1')?></button></a></li>
   
   </ul>
   
   
   </div>
  <div class="bg_table_front">
  	
  
  	<div class="table_front">
  	 	<div class="row center_content">
             <?php if ($this->Settings->logo3) { ?>
                <img src="<?=base_url()?>assets/uploads/logos/<?=$this->Settings->logo3?>" alt="<?=$this->Settings->site_name?>" class="sram_table_logo" />
           <?php   } ?>

  	 		<!-- <img src="<?=$assets?>images/front_logo.png" alt="logo" class="sram_table_logo"> -->
  	 	</div>
         <?php if($this->Settings->bbq_enable){ ?>
        <button class="location bbq" value="1" <?php if(!$isNightauditDone) {echo 'disabled';} ?> <?php if($this->Settings->bbq_enable){ echo ''; }else{  echo 'disabled'; }  ?> >
   	 		<img src="<?=$assets?>images/bbq.png">
   	 		<p><?=lang('BBQ')?></p>
	   	</button>
       <?php } ?>
        
   	 	<button class="location dine_in" value="1" <?php if(!$isNightauditDone) {echo 'disabled';} ?> <?php if($this->sma->actionPermissions('dinein')){ echo ''; }else{  echo 'disabled'; }  ?> >
   	 		<img src="<?=$assets?>images/dine_in.png">
   	 		<p><?=lang('dine_in')?></p>
	   	</button>
        
	   	<button class="location take_away" value="2"  <?php if(!$isNightauditDone) {echo 'disabled';} ?> <?php if($this->sma->actionPermissions('takeaway')){ echo ''; }else{  echo 'disabled'; }  ?> >
	   		<img src="<?=$assets?>images/take_away.png">
	   		<p><?=lang('take_away')?></p>
	   	</button>
	   	<button class="location door_delivery" value="3"  <?php if(!$isNightauditDone) {echo 'disabled';} ?> <?php if($this->sma->actionPermissions('door_delivery')){ echo ''; }else{  echo 'disabled'; }  ?> >
	   		<img src="<?=$assets?>images/delivery.png">
	   		<p><?=lang('door_delivery')?></p>
	   	</button>
	   	<button class="location" value="4" data-title="<?php if($this->sma->actionPermissions('dinein_orders')){ echo 'order_table'; }elseif($this->sma->actionPermissions('takeaway_orders')){ echo 'order_takeaway'; }elseif($this->sma->actionPermissions('door_delivery_orders')){ echo 'order_doordelivery'; }else{  echo 'disabled'; }  ?>" <?php if($this->sma->actionPermissions('orders')){ echo ''; }else{  echo 'disabled'; }  ?>
       	<?php if($this->sma->actionPermissions('dinein_orders') || $this->sma->actionPermissions('takeaway_orders') || $this->sma->actionPermissions('door_delivery_orders')){ echo ''; }else{  echo 'disabled'; }  ?> 
         >
   	 		<img src="<?=$assets?>images/order.png">
   	 		<p><?=lang('orders')?></p>
	   	</button>
	   	<button class="location" value="5"  <?php if($this->sma->actionPermissions('kitchens')){ echo ''; }else{  echo 'disabled'; }  ?> <?php if($this->sma->actionPermissions('kitchen_view')){ echo ''; }else{  echo 'disabled'; }  ?>  >
	   		<img src="<?=$assets?>images/kitchenpos.png">
	   		<p><?=lang('kitchen')?></p>
	   	</button>
	   	<button class="location" value="6"  <?php if($this->sma->actionPermissions('billing')){ echo ''; }else{  echo 'disabled'; }  ?> >
	   		<img src="<?=$assets?>images/billing.png">
	   		<p><?=lang('billing')?></p>
	   	</button>
		<a href="<?php  echo base_url('admin/pos/consolidate') ?>">
		<button class="location"    <?php if($this->sma->actionPermissions('billing')){ echo ''; }else{  echo 'disabled'; }  ?> >
	   		<img src="<?=$assets?>images/cons.png">
	   		<p><?=lang('consolidate')?></p>
	   	</button>
	   	</a>
   </div>
  </div>
</div>


<div class="modal" id="OpenregisterModal" tabindex="-1" role="dialog" aria-labelledby="OpenregisterModalLabel" aria-hidden="true">

    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close closemodal" data-dismiss="modal"><span aria-hidden="true">
                    <i class="fa fa-2x">&times;</i></span>
                    <span class="sr-only"></span>
                </button>
                <h4 class="modal-title" id="cmModalLabel"></h4>
            </div>
            <div class="box-header">
            <div class="form-group">
                <h2 class="blue"><?= lang("open_register"); ?></h2>
                </div>
            </div>
           <div class="modal-body" id="pr_popover_content">
                <div class="form-group">
                    <input type="hidden" id="order_type" value=""/>
                    <div class="form-group">
                        <?= lang('cash_in_hand', 'cash_in_hand') ?>
                        <?= form_input('cash_in_hand', '', 'id="cash_in_hand" class="form-control"'); ?>
                    </div>
                     <button type="button" class="btn btn-primary" id="open_register"><?=lang('open_register')?></button>
                    <div class="clearfix"></div>
                </div>
            </div>
        <!-- </div> -->

            <!-- <div class="modal-body" id="pr_popover_content">
                <div class="form-group">
                    <?= lang('remarks'); ?>
                    <?= form_textarea('remarks', '', 'class="form-control kb-text" id="remarks" style="height:80px;"'); ?>
                </div>
                <input type="hidden" id="order_item_id" value=""/>
                <input type="hidden" id="split_id" value=""/>
        <input type="hidden" id="cancel_qty" value=""/>
            </div> -->
            <!-- <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="cancel_orderitem"><?=lang('send')?></button>
            </div> -->
        </div>
    </div>
</div>

<?php if(isset($multi_uniq_discounts) && count($multi_uniq_discounts)) : ?>
    <div class="modal" style="z-index: 99999;"id="multiunique_discount" tabindex="-1" role="dialog" aria-labelledby="multiunique_discount" aria-hidden="true">

    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
               <!-- <button type="button" class="close closemodal" data-dismiss="modal"><span aria-hidden="true">
                    <i class="fa fa-2x">&times;</i></span>
                    <span class="sr-only"></span>
                </button>-->
                <h4 class="modal-title" id="cmModalLabel"></h4>
            </div>
            <div class="box-header">
          
           <div class="modal-body" id="multi-unique-discount">
	    <table class="table">
		<thead>
		    <th>Name</th>
		    <th>Discount Method</th>
		    <th>Discount Type</th>
		    <th>Discount</th>
		    <th></th>
		</thead>
	    
	    <?php foreach($multi_uniq_discounts as $k => $row) : ?>
	    <tr>
		<td><?=$row->name?></td>
		<td><?=($row->type=="discount_simple")?lang('Simple_Discount'):lang('Discount_on_total');?></td>
		<td><?=($row->discount_type=="fixed_discount")?lang('Fixed'):lang('Percentage');?></td>
		<td><?=$row->discount?></td>
		<td><button type="button" class="btn btn-success set-unique-discount" data-url="<?=admin_url('pos/set_unique_discount')?>" data-id="<?=$row->discount_id?>"><?=lang('select')?></button></td>
	    </tr>
                <!--<button type="button" class="set-unique-discount" data-url="<?=admin_url('pos/set_unique_discount')?>" data-id="<?=$row->discount_id?>">
		    <label></label></br>
		   <span><?=($row->type=="discount_simple")?lang('Item_Discount'):lang('Discount_on_total');?></span>
		   </br>
		    <?php if($row->discount_type=="fixed_discount") : ?>
		    <label><?=lang('fixed_amount').' '.$row->discount?></label>
		    <?php else : ?>
		    <label><?=$row->discount.'%'?></label>
		    </br>
		    
		    <?php endif; ?>
		</button>-->
		<?php endforeach; ?>
		</table>
            </div>        
        </div>
    </div>
</div>
    <script>
	$(document).ready(function(){
	    $('#multiunique_discount').modal({ backdrop: 'static',keyboard: false});
	    $('#multiunique_discount').modal('show');
	})
    </script>
<?php endif; ?>
<div class="modal fade in" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true"></div>
<div class="modal fade in" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2"
     aria-hidden="true"></div>
<div id="modal-loading" style="display: none;">
    <div class="blackbg"></div>
    <div class="loader"></div>
</div>
<?php unset($Settings->setting_id, $Settings->smtp_user, $Settings->smtp_pass, $Settings->smtp_port, $Settings->update, $Settings->reg_ver, $Settings->allow_reg, $Settings->default_email, $Settings->mmode, $Settings->timezone, $Settings->restrict_calendar, $Settings->restrict_user, $Settings->auto_reg, $Settings->reg_notification, $Settings->protocol, $Settings->mailpath, $Settings->smtp_crypto, $Settings->corn, $Settings->customer_group, $Settings->srampos_username, $Settings->purchase_code);?>


<script>
var recipe_variant = 0, shipping = 0, p_page = 0, per_page = 0, tcp = "<?=$tcp?>", pro_limit = <?= $pos_settings->pro_limit; ?>,
        brand_id = 0, obrand_id = 0, cat_id = "<?=$pos_settings->default_category?>", ocat_id = "<?=$pos_settings->default_category?>", sub_cat_id = 0, osub_cat_id,
        count = 1, an = 1, DT = <?=$Settings->default_tax_rate?>,
        recipe_tax = 0, invoice_tax = 0, recipe_discount = 0, order_discount = 0, total_discount = 0, total = 0, total_paid = 0, grand_total = 0,
        KB = <?=$pos_settings->keyboard?>, tax_rates =<?php echo json_encode($tax_rates); ?>;
    var protect_delete = <?php if (!$Owner && !$Admin) {echo $pos_settings->pin_code ? '1' : '0';} else {echo '0';} ?>, billers = <?= json_encode($posbillers); ?>, biller = <?= json_encode($posbiller); ?>;
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
		
$('.location').click(function () {
	var order_type = $(this).val();		
	var order_title = $(this).attr('data-title');		
    var url = '<?php echo  admin_url('pos') ?>';
	var register_data = '<?php echo  $register_data ?>';
	if(register_data == 'none'){
           $('#order_type').val(order_type);
           $('#OpenregisterModal').show();
    }
    else
    {
        if(order_type == 1 || order_type == 2 || order_type == 3){        
            window.location.href= url +'/?order='+order_type; 
        }else if(order_type == 4){
            window.location.href= url +'/'+order_title; 
        }else if(order_type == 5){
            window.location.href= url +'/order_kitchen';    
        }else if(order_type == 6){
            window.location.href= url +'/order_biller/?type=1'; 
        }
    }
});

$(document).on('click','#open_register',function(){
   var cash_in_hand = $('#cash_in_hand').val();
   var order_type = $('#order_type').val();
   var url = '<?php echo  admin_url('pos') ?>';
   $(this).text('<?=lang('loading');?>').attr('disabled', true);

    if($.trim(cash_in_hand) != ''){
    $.ajax({
    type: "post",
    url:"<?=admin_url('pos/user_open_register');?>",                
    data: {cash_in_hand: cash_in_hand},
    dataType: "json",
        success: function (data) {
            if(data.msg == 'success'){
                $('#OpenregisterModal').hide(); 
                window.location.href= url +'/?order='+order_type;   
            }else
            {
                 alert('Something is wrong');
            }
        }    
    });
    } 
    else{
      $('#remarks').css('border','1px solid red');
    }               

});

$('.closemodal').click(function () {               
   $('#OpenregisterModal').hide(); 
});

 $(document).ready(function() {
        $('#cash_in_hand1').change(function(e) {
            if ($(this).val() && !is_numeric($(this).val())) {
                bootbox.alert("<?= lang('unexpected_value'); ?>");
                $(this).val('');
            }
        })
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

<script src="<?=base_url('node_modules/socket.io/node_modules/socket.io-client/socket.io.js')?>"></script>
<script type="text/javascript" src="<?=$assets?>js/socket/socket_configuration.js?v=1"></script>
<script type="text/javascript" src="<?=$assets?>js/socket/client.js"></script>

<script type="text/javascript" src="<?=$assets?>pos/js/pos.ajax.js"></script>
<script type="text/javascript" src="<?=$assets?>js/dragscrollable.js"></script>
<script type="text/javascript" src="<?=$assets?>js/dragscroll.js"></script>
<script type="text/javascript" src="<?=$assets?>js/jquery.notify.js"></script>
<script>
$(document).on('click', '.bbq', function(){
	 var url = '<?php echo  admin_url('pos') ?>';
	 window.location.href= url +'/bbq_tables/?order=4'; 
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
<script>
	$('#subcategory-list, #scroller').dragscrollable({
    dragSelector: 'button', 
    acceptPropagatedEvent: false
});
	</script>
	<script>
//		$('#left-middle ,#recipe-list').dragscrollable({
//			dragSelector: 'table', 
//			acceptPropagatedEvent: false
//		});
//		$(document).ready(function(){
//			$("#left-middle ,#recipe-list").dragscrollable({
//				axis:"x",
//
//			});
//		});
	</script>
<script type="text/javascript">
$('.sortable_table tbody').sortable({
    containerSelector: 'tr'
});
   
</script>
<script type="text/javascript" charset="UTF-8"><?=$s2_file_date?></script>
<?php
if (isset($print) && !empty($print)) {
    /* include FCPATH.'themes'.DIRECTORY_SEPARATOR.$Settings->theme.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'pos'.DIRECTORY_SEPARATOR.'remote_printing.php'; */
    include 'remote_printing.php';
}
?>

<script>
function show_nofication()
 {	
 	$.get('<?=admin_url('pos/notification');?>', function(data)
 	{	
		var datahtml = new Array();
		var rdata = JSON.parse(data);	
		var key_array =  new Array();
		for(i=0; i<rdata.count; i++){
			datahtml += '<li><a href="javascript:void(0)"><h3 class="limit_cont" data-max-characters="5">'+rdata.list[i].type+'</h3><p class="limit_cont" data-max-characters="90">'+rdata.list[i].msg+'</p></a></li>';
			key_array.push(rdata.list[i].id);
		}
		$('#notification_area').text(rdata.count);
		$('#notification_key').val(key_array.join());
		$('.list_notification').html(datahtml); 
	});	
 }	
 
// var timeout = setInterval(show_nofication, 1000);

$(document).ready(function(){
	$(".notification").click(function(){
		
		$(this).find(".content_notification").slideToggle(500, function(){
			 if ($("#content_notification").css('display') == 'block'){
				  clearTimeout(timeout);
				  
				 var notification_key = $('#notification_key').val();
				
				 $.ajax({
				  url: "<?=admin_url('pos/nitification_clear');?>",
				  type: "post",
				  data: { 
					notification_id: notification_key
				  },
				  success: function(response) {
						$('#notification_area').text(0);
						$('#notification_key').val();
						$('.list_notification').html(); 
				  }
				});
				
			 }else{
				timeout = setInterval(show_nofication, 1000); 
			 }
		});
		 
	});
});

function show_request_bil()
 {	
 	$.get('<?=admin_url('pos/request_bil');?>', function(data)
 	{	
		var reqdata = JSON.parse(data);
		if(reqdata != ''){
			$('.req_status').addClass('flash');
		}else{
			$('.req_status').removeClass('flash');
		}
		
		if(localStorage.getItem('soundvalue') < reqdata.req_length){
			$('.req_sound').addClass('notify');
			$.notifySetup({sound: '<?php echo base_url(); ?>assets/notification/to-the-point.ogg'});
			$('.notify').notify();

			$("#rep_count").val(reqdata.req_length);
			localStorage.setItem('soundvalue', $("#rep_count").val());
		}else{
			
			$('.req_sound').removeClass('notify');
			localStorage.setItem('soundvalue', reqdata.req_length);
		}
		
	});	
 }	
 
// var request_time = setInterval(show_request_bil, 1000);

$(".limit_cont").each(function() {
	var textMaxChar = $(this).attr('data-max-characters');

	length = $(this).text().length;
	if(length > textMaxChar) {
		$(this).text($(this).text().substr(0, textMaxChar) + '...');
	}
});

$(".content_notification").mCustomScrollbar({
	setHeight: "250px",
	theme:"dark"
});
	$("#recipe-list").mCustomScrollbar({
	setHeight: "250px",
	theme:"dark"
});
	$("#item-list > div > div").mCustomScrollbar({
		setHeight: "200px",
	theme:"dark"
});
	
</script>
<?php $this->load->view($this->theme . 'pos/transaction_date_confirm_popup'); ?>
</body>
</html>
