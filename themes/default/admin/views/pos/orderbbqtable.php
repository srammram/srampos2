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
    <link href="<?= $assets ?>styles/flipclock.css" rel="stylesheet"/>
    <link rel="stylesheet" href="<?=$assets?>pos/css/posajax.css" type="text/css"/>
    <link rel="stylesheet" href="<?=$assets?>pos/css/print.css" type="text/css" media="print"/>
    <script type="text/javascript" src="<?=$assets?>js/jquery-2.0.3.min.js"></script>
    <script type="text/javascript" src="<?=$assets?>js/jquery-migrate-1.2.1.min.js"></script>
    <script type="text/javascript" src="<?= $assets ?>js/flipclock.min.js"></script>
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
    <style>
	.bootbox.modal.bootbox-confirm{
	    width: auto !important;
	    margin-left: 0px !important;
	    left: 0px !important;
	}
	
	.bootbox.modal{
		 width: auto !important;
	    margin-left: 0px !important;
	    left: 0px !important;	
	}
    </style>
    
    <?php if(@$_GET['bbqtid'] && isset($_SERVER['HTTP_REFERER'])): ?>
        <script>var curr_page="order_bbqtable";var curr_func="update_bbqtables";var tableid = '<?=$_GET['bbqtid']?>';</script>	
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
                
                	 
                    
                                  
                    
<div class="current_table_order">
	<div class="container custom_container">
    	<div class="row">
        

        	
				<div id="ordertable_box">
                
                </div>
            
                    
                    
        <div class="clearfix"></div>
        	
    	</div>
    </div>
</div>


</div>
</div>
</div>
</div>
</div>

<div class="modal fade in" id="dineinModal" tabindex="-1" role="dialog" aria-labelledby="dineinModalLabel"
     aria-hidden="true" style="z-index:9999">
    <div class="modal-dialog modal-md">
       <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="customerModalLabel"><?php echo lang('Dine In Types'); ?></h4>
        </div>
        
        <div class="modal-body">
           
			
             
            <div class="row text-center  ">
            <div class="col-xs-12 table_front" style="    position: relative;
    top: 0pc;
    left: 0px;
    right: 0;
    bottom: 0;
    z-index: 99999;
    height: auto;
    width: 100%;
    overflow: hidden !important;
    border-radius: 4px;
">
            	<input type="hidden" name="pop_table_id" id="pop_table_id" >
                
                <button class="orderpath location" value="bbq_link" <?php if($this->Settings->bbq_enable){ echo ''; }else{  echo 'disabled'; }  ?> >
                    <img src="<?=$assets?>images/bbq.png">
                    <p><?=lang('BBQ')?></p>
                </button>
                
                <button class="orderpath location" value="dine_link" <?php if($this->sma->actionPermissions('dinein')){ echo ''; }else{  echo 'disabled'; }  ?> >
                    <img src="<?=$assets?>images/dine_in.png">
                    <p><?=lang('dine_in')?></p>
                </button>
            </div>
			</div>
            <div class="clearfix"></div>

        </div>
        
        
    </div>
    </div>
</div>

<div class="modal fade in" id="coverModal" tabindex="-1" role="dialog" aria-labelledby="coverModalLabel"
     aria-hidden="true" style="z-index:99999999999" data-backdrop="static" data-keyboard="false" >
    <div class="modal-dialog modal-md">
       <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="coverModalLabel"><?php echo lang('BBQ_Cover'); ?></h4>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'id' => 'add-customer-form');
        echo admin_form_open_multipart("pos/edit_bbq", $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>
			
             
            <div class="row" id="BBQcode">
                
            </div>
            
        </div>
        <div class="modal-footer">
            <?php echo form_submit('edit_bbq', lang('submit'), 'class="btn btn-primary"'); ?>
        </div>
        <?php echo form_close(); ?>
    </div>
    </div>
</div>

<?php
$this->load->view($this->theme . 'pos/pos_footer');
?>
<script>
function ajaxData(table_id){
	$.ajax({
	  url: "<?=admin_url('pos/ajaxorder_bbqtable');?>",
	  type: "get",
	  data: { 
		table: table_id
	  },
	  success: function(response) {
			$("#ordertable_box").html(response);
	  }
	});
}
$(document).ready(ajaxData(<?php echo $tableid; ?>));
var ajaxDatatimeout = setInterval(ajaxData(<?php echo $tableid; ?>), 60000);
$(document).on('click', '.cover_edit', function(){		
	var bbqcode = $(this).attr('data-bbq');	
	$.ajax({
		type: "get",
		url: "<?=admin_url('pos/BBQcode');?>",
		data: {bbqcode: bbqcode},
		dataType: "html",
		success: function (data) {
			$('#BBQcode').html(data);
			$('#coverModal').css('overflow-y', 'scroll');
			$('#coverModal').appendTo("body").modal('show', {backdrop: 'static', keyboard: false}); 
				
		}
	
	})
	
});
</script>

<div class="modal fade in" id="bilModal" tabindex="-1" role="dialog" aria-labelledby="bilModalLabel" aria-hidden="true" style="background: rgba(0,0,0,0.5);">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close closebil" data-dismiss="modal" aria-hidden="true"><i
                        class="fa fa-2x">&times;</i></button>
                <h4 class="modal-title" id="bilModalLabel"><?=lang('bil_type')?></h4>
            </div>
            <div class="modal-body">
              
              <div class="form-group">
              
                    <div ><input type="radio" name="bil_type" class = "bil_type bbqradio1" value="1" checked> <span class="bbqradio1"><?=lang('single_bil')?></span></div>
                    
                     <?php if($this->sma->actionPermissions('auto_bil')){ ?>
                    <div class="count_div "><input type="radio" name="bil_type" class = "bil_type bbqradio2" value="2"> <span class="bbqradio2"><?=lang('auto_split_bil')?></span></div>
                    <?php } ?>
                     <div class="count_div " ><input type="radio" name="bil_type" class="bil_type bbqradio3" value="3" > <span class="bbqradio3">Manual Split Bil</span></div> 
                     
                     <div class=""><input type="radio" name="bil_type" class = "bil_type bbqdineradio1" value="4" checked> <span class="bbqdineradio1"><?=lang('single_bil')?></span></div>
                     
                    <input class="form-control kb-pad " type="text" class = "bil_type"  name="bils_number_auto" id="bils_number_auto" placeholder="<?=lang('auto_split')?>" style="display:none;">
                    <input type="text" class="form-control  kb-pad" name="bils_number_manual" id="bils_number_manual" placeholder="Manual Split" style="display:none;">
                </div>
				<input type="hidden" name="bil_split_type" id="bil_split_type">
                <input type="hidden" name="bil_table_type" id="bil_table_type">
            </div>
            <div class="modal-footer">
                <button type="button" id="updateBil" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="CancelorderModal" tabindex="-1" role="dialog" aria-labelledby="CancelorderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
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
                <input type="hidden" id="order_item_id" value=""/>
                <input type="hidden" id="split_order" value=""/>
		<input type="hidden" id="cancel_qty" value=""/>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="cancel_orderitem"><?=lang('send')?></button>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">

$(document).on('click', '.bbq', function(){
	
	
	var table_id = $(this).val();
	$('#table_id').val(table_id);
	//$('#customerModal').css('overflow-y', 'scroll');
	$('#customerModal').appendTo("body").modal('show', {backdrop: 'static', keyboard: false}); 
	//window.location.href= url +'/bbq?order=4&table='+table_id+'&split='+split_id+'&same_customer='+customer_id+'&set=1';
	
});


$(document).on('change', '#customer_type', function(){
	var type = $(this).val();
	if(type == 1){
		$('.customer').hide();
		$('#company').val('');
		$('#name').val('');
		$('#email_address').val('');
		$('#phone').val('');
		$('#address').val('');
		$('#city').val('');
		$('#state').val('');
		$('#postal_code').val('');
		$('#country').val('');
		$('.customer_id').val('');
		
	}else{
		$('.customer').show();
	}
});

$(document).on('change', '.customer_id', function(){
	
	var customer_id = $(this).val();
	$.ajax({
		type: "get",
		async: false,
		url: "<?= admin_url('pos/getCustomerBYID') ?>/" + customer_id,
		dataType: "json",
		success: function (data) {
			$('#company').val(data.company);
			$('#name').val(data.name);
			$('#email_address').val(data.email_address);
			$('#phone').val(data.phone);
			$('#address').val(data.address);
			$('#city').val(data.city);
			$('#state').val(data.state);
			$('#postal_code').val(data.postal_code);
			$('#country').val(data.country);
			$('.customer_id').val(data.customer_id);
			

		}
	});
	
});

$(document).on('click', '.new_order', function(){
	var tb = $(this).attr('data-table');
	$('#pop_table_id').val(tb);
	$('#dineinModal').appendTo("body").modal('show', {backdrop: 'static', keyboard: false});
});

$(document).on('click', '.orderpath', function(){
	var alink = $(this).val();
	var table = $('#pop_table_id').val();
	var href = $('.'+alink+'_'+table).attr('href');
     window.location.href = href;
});
		
 $(document).ready(function(e) {
	 
	 $(document).on('click','.orderCancelled',function(e){	
	 	var isShowing = $(this).data("isShowing");
		$('.orderCancelled').removeData("isShowing");
		 if (isShowing != "true") {
			$('.orderCancelled').not(this).tooltip("hide");
			$(this).data("isShowing", "true");
			$(this).tooltip("show");
		  } else {
			$(this).tooltip("hide");
		  }
	 }).tooltip({
		  animation: true,
		  trigger: "manual",
		  placement: "auto"
		});
	 /*var hasToolTip = $(".orderCancelled");
			
			hasToolTip.on("click", function(e) {
				
			  e.preventDefault();
			  var isShowing = $(this).data("isShowing");
			  hasToolTip.removeData("isShowing");
			  if (isShowing != "true") {
				hasToolTip.not(this).tooltip("hide");
				$(this).data("isShowing", "true");
				$(this).tooltip("show");
			  } else {
				$(this).tooltip("hide");
			  }
			}).tooltip({
			  animation: true,
			  trigger: "manual",
			  placement: "auto"
			});*/
	
	$(document).on('click','.waiter_cancel_order',function(e){	
	//$(".waiter_cancel_order").click(function(e) {
        var order_id = $(this).val();
		$('#modal-loading').show();
		 $.ajax({
			type: "get",
			 url:"<?=admin_url('pos/order_cancel_waiter');?>",                
			data: {order_id: order_id},
			dataType: "json",
			success: function (data) {
				location.reload();
			}
		}).done(function () {
			$('#modal-loading').hide();
		});
    });	
	
	 $(".itm_padd").click(function (e) {
    
		if (!$(e.target).is('input:checkbox')) {
			
			var $checkbox = $(this).find('input:checkbox');
			$checkbox.trigger( "click" );
		}
	
	});
	$('.order_list .item_list .itm_padd_content a').click(
    function(e) {
        e.stopPropagation();
    });
	$('.order_list .item_list .btn').click(
    function(e) {
        e.stopPropagation();
    });
	
    //$(".multiple_status").click(function(e) {
	$(document).on('click','.multiple_status',function(e){	
		var item_id = $(this).attr('data-type');
		var status = $(this).attr('title');
		var split_id = $(this).attr('data-split');
		
		var val = [];
		var processArray = [];
		var prepareArray = [];
		$('.status_'+split_id+':checkbox:checked').each(function(i){

			var currentValue = $(this).val();
			val[i] = currentValue;
			if($(this).attr('title') == 'Ready'){
				processArray[i] = currentValue;
			} else if($(this).attr('title') == 'Served') {
				prepareArray[i] = currentValue;
			}
			$('.multiple_'+split_id).prop('checked', false);
		});
		if( (processArray.length > 0) && (prepareArray.length == 0) ){
			
			$(".ready_"+split_id).hide();
			$(".preparing_"+split_id).show();
			$(".ready_"+split_id).attr('data-id', '');
			$(".preparing_"+split_id).attr('data-id', val);
			
		} else if( (prepareArray.length > 0) && (processArray.length == 0) ){
			$(".preparing_"+split_id).hide();
			$(".ready_"+split_id).show();
			$(".preparing_"+split_id).attr('data-id', '');
			$(".ready_"+split_id).attr('data-id', val);
		}else{
			$(".preparing_"+split_id).hide();
			$(".ready_"+split_id).hide();	
			
			$(".preparing_"+split_id).attr('data-id', '');
			$(".ready_"+split_id).attr('data-id', '');
			
		}
			
    });
	
	//$(".multiple_check").change(function(){ 
	$(document).on('click','.multiple_check',function(){
		var order = $(this).attr('data-order');
	 	$('.status_'+order).prop('checked', $(this).prop("checked"));
		
		
		var arr = $.map($('.status_'+order+':checked'), function(e,i) {
			return +e.value;
		});
				
		var val = [];
		var processArray = [];
		var prepareArray = [];
		
		$.each(arr, function( index, value ) {
		  	
			var currentValue = value;
			val[index] = currentValue;
			
			if($("input[value="+currentValue+"]").attr('title') == 'Ready'){
				processArray[index] = currentValue;
			} else if($("input[value="+currentValue+"]").attr('title') == 'Served') {
				prepareArray[index] = currentValue;
			}
		});
		
		if( (processArray.length > 0) && (prepareArray.length == 0) ){
			
			$(".ready_"+order).hide();
			$(".preparing_"+order).show();
			$(".ready_"+order).attr('data-id', '');
			$(".preparing_"+order).attr('data-id', val);
			
		} else if( (prepareArray.length > 0) && (processArray.length == 0) ){
			$(".preparing_"+order).hide();
			$(".ready_"+order).show();
			$(".preparing_"+order).attr('data-id', '');
			$(".ready_"+order).attr('data-id', val);
		}else{
			$(".preparing_"+order).hide();
			$(".ready_"+order).hide();	
			
			$(".preparing_"+order).attr('data-id', '');
			$(".ready_"+order).attr('data-id', '');
			
		}
		
		
	});
	
	//$(".kitchen_status").click(function(e) {
	$(document).on('click','.kitchen_status',function(e){	
        var status = $(this).attr('data-status');
		var split_id = $(this).attr('data-split-id');
		var id = $(this).attr('data-id');
		
		$('#modal-loading').show();
		 $.ajax({
			type: "get",
			 url:"<?=admin_url('pos/update_order_item_status');?>",                
			data: {status: status, order_item_id: id,split_id: split_id},
			dataType: "json",
			success: function (data) {
				location.reload();
			   
				
			}
		}).done(function () {
			$('#modal-loading').hide();
		});
		
    });
});
		
		
		function splitItem( table_id, split_id )
		{
			$('#modal-loading').show();
			$.ajax({
				type: "get",
				url: "<?=admin_url('pos/ajaxSplititemdata');?>",
				data: {table_id: table_id, split_id: split_id},
				dataType: "json",
				success: function (data) {
					$('#tableorderleft').empty();
					var newPrs = $('<div></div>');
					newPrs.html(data.order_item);
					newPrs.appendTo("#tableorderleft");
					
				}
			}).done(function () {
				$('#modal-loading').hide();
			});
		}
		
		function orderItem( order_id, table_id, split_id )
		{
			$('#modal-loading').show();
			$.ajax({
				type: "get",
				url: "<?=admin_url('pos/ajaxOrderitemdata');?>",
				data: {order_id: order_id, table_id: table_id, split_id: split_id},
				dataType: "json",
				success: function (data) {
					$('#tableorderleft').empty();
					var newPrs = $('<div></div>');
					newPrs.html(data.order_item);
					newPrs.appendTo("#tableorderleft");
					
				}
			}).done(function () {
				$('#modal-loading').hide();
			});
		}
		
		
   
            function updateOrderStatus( status, id ,split_id)
            {    
            	
                $('#modal-loading').hide();
				clearTimeout(ajaxDatatimeout);
                if (confirm('Are you sure?')) { 
                    $.ajax({
                        type: "get",
                        url:"<?=admin_url('pos/update_order_item_status');?>",                
                        data: {status: status, order_item_id: id,split_id: split_id},
                        dataType: "json",
                        success: function (data) {
							ajaxDatatimeout = setInterval(ajaxData, 60000);
                           
                        }
                    }).done(function () {
                        $('#modal-loading').hide();
                    });
                }
                else{

                }
            }
		
	 		function CancelOrderItem( status, id, split_id ,$remarks=0,$quantity)
            {    
				clearTimeout(ajaxDatatimeout);
				
            	$("#order_item_id").val(id);
				$("#split_order").val(split_id);
				
				
			if ($quantity>1) {
			    $inputoptions =[];
			    for (i = 0; i < $quantity; i++) {
				$v = i+1;
				$inputoptions[i] = {text: $v,value:$v};
			    }
			
			    bootbox.prompt({ 
				title: "Enter Quantity to cancel",
				inputType:'select',
				inputOptions :$inputoptions,
				callback: function(qty){
				    if (qty!=null) {
					 $cancelQty = qty;
					if ($quantity==qty) {
					    $cancelQty = 'all';
					}
					cancelorderPopup(id ,split_id,$remarks,$cancelQty);
					$('#cancel_qty').val($cancelQty);
				    }else{
					
				    }
				   
				}
			    });
			}else{
			    $cancelQty = 'all';
			    cancelorderPopup(id ,split_id,$remarks,$cancelQty);
			    $('#cancel_qty').val($cancelQty);
			} 
			

            }
	    function cancelorderPopup(id,split_id,$remarks,$cancelQty){
			if($remarks!=0){
				    $('#remarks').val('');
				    $('#CancelorderModal').show();
				}else{
				    $msg = ($cancelQty!='all')?'Are you sure want to cancel '+$cancelQty+' Qty?':'Are you sure want to cancel this item?';
		    bootbox.confirm({
			message: $msg,
			buttons: {
			    confirm: {
				label: 'Yes',
				className: 'btn-success'
			    },
			    cancel: {
				label: 'No',
				className: 'btn-danger'
			    }
			},
			callback: function (result) {
			   console.log(result)
			    if (result) {				
				$.ajax({
				    type: "get",
				    url:"<?=admin_url('pos/cancel_order_items');?>",                
				    data: {order_item_id: id, split_id: split_id,cancelqty:$cancelQty},
				    dataType: "json",
				    success: function (data) {
					if(data.msg == 'success'){
						ajaxDatatimeout = setInterval(ajaxData, 60000);
						location.reload();      	                      	
					}else{
					    alert('not update waiter');
					}
				    }    
				}).done(function () {
				      
				});
				  
			    }else{
					       //requestBill(billid);
			    }
			    
			}
		    });
		}
	    }
	    $('#remarks').on('focus',function(){
		$('#remarks').css('border','1px solid #ccc');
	    });
            $(document).on('click','#cancel_orderitem',function(){
            	 var cancel_remarks = $('#remarks').val(); 
            	 var order_item_id = $('#order_item_id').val(); 
				 var split_id = $("#split_order").val();
				 var $cancelQty = $('#cancel_qty').val();
            	 if($.trim(cancel_remarks) != ''){
            	 	$.ajax({
                        type: "get",
                        url:"<?=admin_url('pos/cancel_order_items');?>",                
                        data: {cancel_remarks: cancel_remarks, order_item_id: order_item_id, split_id: split_id,cancelqty:$cancelQty},
                        dataType: "json",
                        success: function (data) {
                            if(data.msg == 'success'){
                            	     $('#CancelorderModal').hide(); 
									 ajaxDatatimeout = setInterval(ajaxData, 60000);
									 location.reload();      	                      	
                            }else{
                                alert('not update waiter');
                            }
                        }    
                    }).done(function () {
				      
					});
            	 } else{
		        $('#remarks').css('border','1px solid red');
		 }

            });
            $('.closemodal').click(function () {
            	$('#remarks').val('');
            	$('#order_item_id').val('');
				$('#split_order').val('');
				$('#cancel_qty').val('');
 				$('#CancelorderModal').hide(); 
				ajaxDatatimeout = setInterval(ajaxData, 60000);
            });
			
			function bilGenerator( table_id, split_id, count_id, dinebbq )
            {   
            	$("#bil_table_type").val(table_id);

				$( ".bil_type" ).prop( "checked", false );
				
				$("#bils_number_auto").hide();
				$("#bils_number_manual").hide();
				$('#bils_number_auto').val(''); 
				$('#bils_number_manual').val('');  

				$("#bil_split_type").val(split_id);
				
				
				if(dinebbq == 1){
					$("input[name=bil_type][value='4']").prop("checked",true);
					$('.bbqradio1').hide();
					$('.bbqradio2').hide();
					$('.bbqradio3').hide();
					$('.bbqdineradio1').show();
				}else{
					$("input[name=bil_type][value='1']").prop("checked",true);	
					$('.bbqdineradio1').hide();
					$('.bbqradio1').show();
					$('.bbqradio2').show();
					$('.bbqradio3').show();
				}
				
				if(count_id == 0 || count_id == 1){
					$(".count_div").hide();
				}
				else{
					$(".count_div").show();
				}
            	$('#bilModal').show();
            }
			$(document).on('click','#updateBil',function(){
				
                 var table_id = $('#bil_table_type').val(); 
            	// var count_item = $('#count_item').val();
            	 var split_id = $('#bil_split_type').val(); 
            	 var count_item = $('#'+split_id+'_count_item').val();
				 var bil_type = $('input[name=bil_type]:checked').val();
				 
				 var url = '<?php echo  admin_url('pos') ?>';
				 if(bil_type == 1){
					 var bils = 1;
				 }else if(bil_type == 2){
					 var bils = $('#bils_number_auto').val(); 
				 }else if(bil_type == 3){
                    var bils = $('#bils_number_manual').val();
                    if(bils == 1)
                    {
                        bil_type = 1;                        
                    }
					
                    if(parseInt(bils) > parseInt(count_item)){
                       // bootbox.alert('<?=lang('manual_bill');?>');
					   bootbox.alert('Split('+bils+') Count Should Not Greater Than No of Covers('+count_item+')');
                        return false;
                    }else if(count_item == bils ){
						 window.location.href= url +'/bbqbilling/?order_type=4&bill_type='+bil_type+'&bils='+bils+'&table='+table_id+'&splits='+split_id;
					}else{
						if(bils > 0){
					
							 window.location.href= url +'/bbqbilling/?order_type=4&bill_type='+bil_type+'&bils='+bils+'&table='+table_id+'&splits='+split_id;
						 }else{
							 bootbox.alert('Check any input feild empty');
                        	 return false;
							
						 }
					}
					
				 }else if(bil_type == 4){
					
					 var bils = 1;
					 if(bils == 1){
						
					  window.location.href= url +'/bbqconsolidated/?bbq_order_type=4&dine_order_type=1&bill_type='+bil_type+'&bils='+bils+'&table='+table_id+'&splits='+split_id;
					  return true;
					 
					 }
				 }

				 if(bils > 0){
					
					 window.location.href= url +'/bbqbilling/?order_type=4&bill_type='+bil_type+'&bils='+bils+'&table='+table_id+'&splits='+split_id;
				 }else{
            	 	alert('Check any input feild empty');
				 }

            });
			
			$('.closebil').click(function () {
            	$("#bil_table_type").val('');
				$("#bil_split_type").val('');
            	$('#bilModal').hide();
            });
			
		
	$('.print_bill').click(function () {
            if (count == 1) {
                bootbox.alert('<?=lang('x_total');?>');
                return false;
            }
            <?php if ($pos_settings->remote_printing != 1) { ?>
                printBill();
            <?php } else { ?>
                Popup($('#bill_tbl').html());
            <?php } ?>
        });
		
$(document).ready(function() {
    $('input[type=radio][name=bil_type]').change(function() {

		$("#bils_number_auto").val('');
		$("#bils_number_manual").val('');
        if (this.value == 1) {
            $("#bils_number_auto").hide();
			$("#bils_number_manual").hide();
        }else if (this.value == 2) {
			$("#bils_number_manual").hide();
			$("#bils_number_auto").show();
        }else if (this.value == 3) {
            $("#bils_number_auto").hide();
			$("#bils_number_manual").show();
			
        }
    });
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
<?php /*<script type="text/javascript" src="<?=$assets?>pos/js/pos.ajax.js"></script>*/?>
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
		maxLength: 4,
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

$(document).on('click','.change_table',function(e){	
        e.preventDefault();
        $('#change_split_id').val('');
        var change_split = $(this).attr("split");
		$('#table-change-Modal').show();
		$('#change_split_id').val(change_split);
    });	
$(document).on('click','.change_customer',function(e){	
        e.preventDefault();
        $('#change_split_id').val('');
        var change_split = $(this).attr("split");
		console.log(55)
		$('#customer-change-Modal').show(); 
		$('#change_split_id').val(change_split);
    });
$(document).on('click', '.closemodal', function () {
    $('#changed_table_id').val('');
    $('#change_split_id').val('');
    $('#table-change-Modal').hide();
    $('#customer-change-Modal').hide(); 
});
$(document).on('click','#OrderChangeTable',function(){

     var change_split_id = $('#change_split_id').val(); 
      
      var changed_table_id =  $("#changed_table_id option:selected").val();
     if($.trim(changed_table_id) != '' && $.trim(changed_table_id) != 0){
        
        $.ajax({
            type: "POST",
            url:"<?=admin_url('pos/change_table_number');?>",                
            data: {change_split_id: change_split_id, changed_table_id: changed_table_id},
            dataType: "json",
            success: function (data) {
                if(data.msg == 'success'){
                         $('#table-change-Modal').hide(); 
                         location.reload();
                }else{
                    bootbox.alert('<?=lang('uanble_to_cancle');?>');
                    return false;
                }
            }    
        })
     }   
     else{
     	bootbox.alert('<?=lang('please_select_changing_table');?>');
        return false;
     }
});
$(document).on('click','#OrderChangeCustomer',function(){

     var change_split_id = $('#change_split_id').val(); 
      
      var changed_customer_id =  $("#changed_customer_id option:selected").val();
     
     if($.trim(changed_customer_id) != '' && $.trim(changed_customer_id) != 0){
      
        $.ajax({
            type: "POST",
            url:"<?=admin_url('pos/change_customer_number');?>",                
            data: {change_split_id: change_split_id, changed_customer_id: changed_customer_id},
            dataType: "json",
            success: function (data) {
                if(data.msg == 'success'){
                         $('#customer-change-Modal').hide(); 
                         location.reload();
                }else{
                    bootbox.alert('<?=lang('uanble_to_cancle');?>');
                    return false;
                }
            }    
        })
     }   
     else{
     	bootbox.alert('<?=lang('please_select_changing_customer');?>');
        return false;
     }
});

$(document).on('click','.merge_bill',function(e){	
        e.preventDefault();
        $('#merge_split_id').val('');
        var current_split = $(this).attr("split");
        var table_id = $(this).attr("table_id");
        $('#merge_split_id').val(current_split);
        $('#merge_table_id').val(table_id);
        $('.merge-group-list').empty();

         $.ajax({
            type: "GET",
            url:"<?=admin_url('pos/get_splits_for_merge');?>",                
            data: {current_split: current_split},
            dataType: "json",
            success: function (res) {
				var check_Box ='';
				$('.merge_table_id').html('');
				$('.merge-group-list').empty();
				$.each(res.data, function(i, item) {
					check_Box = "<input type='checkbox' class='merge'  name='merge[]' value='" + item.split_id + "'/>" + item.name + "<br/>";
					$(check_Box).appendTo('.merge-group-list');
				});
				
            }    
        });
		$('#splits-merge-Modal').show();
    });	 

$(document).on('click', '.closmergeemodal', function () {
    $('#merge_split_id').val('');
    $('#merge_table_id').val('');
    $('#splits-merge-Modal').hide();
    $('.merge-group-list').empty();
});

$(document).on('click','#Mergesplits',function(){
var checkedNum = $('input[name="merge[]"]:checked').length; 
var current_split = $('#merge_split_id').val();
var merge_table_id = $('#merge_table_id').val();
var merge_splits = [];
if(checkedNum > 0){
       $('.merge:checked').each(function () {
           merge_splits[i++] = $(this).val();           
       });
       $.ajax({
            type: "POST",
            url:"<?=admin_url('pos/multiple_splits_mergeto_singlesplit');?>",                
            data: {merge_splits: merge_splits, current_split: current_split, merge_table_id:merge_table_id},
            dataType: "json",
            success: function (data) {
                if(data.msg == 'success'){
                         $('#splits-merge-Modal').hide(); 
                         location.reload();
                }else{
                    bootbox.alert('<?=lang('uanble_to_merge');?>');
                    return false;
                }
            }    
        });   
}else{
	alert('Please select any one split');
	return false;
}

return false;
});
</script>

<style>
    .modal-dialog{
    width: 500px;
    }
    .modal-body{
        /*height: 500px;*/
        max-height:1000px;
    }
    .recipe-group-list ul li{
        list-style: none;
        float: left;
    position: relative;
    margin-right: 20px;
    width: 200px;
    }
    #add-more{
            float: left;
    }
    .list-group{
     background:none!important;
     color:inherit;
     border:none; 
     padding:0!important;
     font: inherit;
     /*border is optional*/
     border-bottom:1px solid #444; 
     cursor: pointer;
    }
</style>
<script>
    function send_kot($split_id) {
	$.ajax({
                        type: "post",
                        url:"<?=admin_url('pos/kot_print_copy/');?>"+$split_id,                
                        
                        success: function (data) {
                            bootbox.alert('sent to kot print');
                        }    
                    })
    }
</script>
</body>
</html>
