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
	<div class="container">
    	<div class="row">
        

        	<div id="ordertakeaway_box">
            
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

<?php
$this->load->view($this->theme . 'pos/pos_footer');
?>
<script>
function ajaxData()
{
	
	$.ajax({
	  url: "<?=admin_url('pos/ajaxorder_takeaway');?>",
	  type: "get",
	  success: function(response) {
			$("#ordertakeaway_box").html(response);
	  }
	});
}


var ajaxDatatimeout;
$timeinterval = 120000;
$(document).ready(function(){
    ajaxData();
    setTimeout(function(){
    ajaxDatatimeout = setInterval(function(){ajaxData()}, $timeinterval);
    $timeinterval = 60000;
    },120000)
    
});

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
		<div class="row col-sm-12">
		    <div class="form-group">
			<label><input type="radio" name="cancel_type" class="radio cancel-type" checked value="out_of_stock"><?=lang('out_of_stock')?></label>
			<label><input type="radio" name="cancel_type" class="radio cancel-type" value="spoiled"><?=lang('spoiled')?></label>
			<label><input type="radio" name="cancel_type" class="radio cancel-type" value="reusable"><?=lang('reusable')?></label>
		    </div>
		</div>
                <div class="form-group">
                    <?= lang('remarks'); ?>
                    <?= form_textarea('remarks', '', 'class="form-control kb-text" id="remarks" style="height:80px;"'); ?>
                </div>
                <input type="hidden" id="order_item_id" value=""/>
                <input type="hidden" id="split_order" value=""/>
                <input type="hidden" id="cancel_qty" value=""/>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="cancel_orderitem"><?=lang('submit')?></button>
            </div>
        </div>
    </div>
</div>
<div class="modal" id="CancelAllorderModal" tabindex="-1" role="dialog" aria-labelledby="CancelAllorderModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close cancelclosemodal" data-dismiss="modal"><span aria-hidden="true">
                    <i class="fa fa-2x">&times;</i></span>
                    <span class="sr-only"></span>
                </button>
                <h4 class="modal-title" id="cmModalLabel"></h4>
            </div>
            <div class="modal-body" id="pr_popover_content">
		
		
                <div class="form-group">
                    <?= lang('remarks'); ?>
                    <?= form_textarea('remarks', '', 'class="form-control kb-text" id="cancel-remarks" style="height:80px;"'); ?>
                </div>
                <input type="hidden" id="order_split_id" value=""/>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="cancel_allorderitem"><?=lang('send')?></button>
            </div>
        </div>
    </div>
</div>
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
                    <div><input type="radio" name="bil_type" value="1" checked><?=lang('single_bil')?></div>
                    <!--<div><input type="radio" name="bil_type" value="2"> Auto Split Bil</div>
                    
                     <div class="count_div" ><input type="radio" name="bil_type" value="3" > Manual Split Bil</div> -->
                    <input class="form-control  kb-pad" type="text" name="bils_number_auto" id="bils_number_auto" placeholder="Auto Split" style="display:none;">
                    <input type="text" class="form-control kb-pad" name="bils_number_manual" id="bils_number_manual" placeholder="Manual Split" style="display:none;">
                </div>
				<input type="hidden" name="bil_split_type" id="bil_split_type">
            </div>
            <div class="modal-footer">
                <button type="button" id="updateBil" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </div>
</div>

<div id="order_tbl"><span id="order_span"></span>
    <table id="order-table" class="prT table table-striped" style="margin-bottom:0;" width="100%"></table>
</div>
<div id="bill_tbl"><span id="bill_span"></span>
    <table id="bill-table" width="100%" class="prT table table-striped" style="margin-bottom:0;"></table>
    <table id="bill-total-table" class="prT table" style="margin-bottom:0;" width="100%"></table>
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

    
</script>

<script type="text/javascript">
		
		$(document).ready(function(e) {
			
			var hasToolTip = $(".orderCancelled");
			
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
			});
			
			$(".waiter_cancel_order").click(function(e) {
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
	
	
	
	//$(".kitchen_status").click(function(e) {
	$(document).on('click','.kitchen_status',function(e){		
        var status = $(this).attr('data-status');
		var split_id = $(this).attr('data-split-id');
		var id = $(this).attr('data-id');
		clearTimeout(ajaxDatatimeout);
		$('#modal-loading').show();
		 $.ajax({
			type: "get",
			 url:"<?=admin_url('pos/update_order_item_status');?>",                
			data: {status: status, order_item_id: id,split_id: split_id},
			dataType: "json",
			success: function (data) {
				ajaxDatatimeout = setInterval(ajaxData, 60000);
				//location.reload();
			   
				
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
$(document).ready(function() {

			$('.updateOrder').each(function(index,item){
			   var get_status = $(this).text();
				if($.trim(get_status) == "Preparing")
				{
					$(this).prop('disabled', true);
					$(this).parent().parent().parent().prop('disabled', true);
				  
				}else if($.trim(get_status) == "Closed" ){
					$(this).prop('disabled', true);
					$(this).siblings().prop('disabled', true);
					$(this).parent().parent().parent().prop('disabled', true);
				}else if($.trim(get_status) == "Inprocess"){
					$(this).prop('disabled', true);
					$(this).parent().parent().parent().prop('disabled', true);
				}
				else{
					$(this).prop('disabled', false);
				}
			   
				});
				$('.orderCancelled').each(function(index,item){
					var cancel = $(this).attr('title');

					if($.trim(cancel) == "Cancelled"){
						$(this).siblings().prop('disabled', true);
					}else{
						$(this).siblings().prop('disabled', false);
					}
				});
			});
			
			
			
   
            function updateOrderStatus( status, id ,split_id)
            {    
            	
                $('#modal-loading').hide();
                if (confirm('Are you sure?')) { 
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
                }
                else{

                }
            }
		
	 		/*function CancelOrderItem( status, id, split_id )
            {    
            	$("#order_item_id").val(id);
				$("#split_order").val(split_id);
            	$('#remarks').val(''); 
            	$('#CancelorderModal').show();
				clearTimeout(ajaxDatatimeout);

            }*/
            function CancelOrderItem( status, id, split_id ,$remarks=0,$quantity)
            {    
				// clearTimeout(ajaxDatatimeout);
				
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
	        	$(this).attr('disabled',true);
		        $(this).text('please wait...');
		        
            	 var cancel_remarks = $('#remarks').val();
		 		 var cancel_type = $('.cancel-type:checked').val(); 
            	 var order_item_id = $('#order_item_id').val(); 
				 var split_id = $("#split_order").val();
				 var $cancelQty = $('#cancel_qty').val();				 
            	 if($.trim(cancel_remarks) != ''){
            	 	$.ajax({
                        type: "get",
                        url:"<?=admin_url('pos/cancel_order_items');?>",                
                        data: {cancel_type:cancel_type,cancel_remarks: cancel_remarks, order_item_id: order_item_id, split_id: split_id,cancelqty:$cancelQty},
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

            /*$(document).on('click','#cancel_orderitem',function(){
            	 var cancel_remarks = $('#remarks').val(); 
            	 var order_item_id = $('#order_item_id').val(); 
				 var split_id = $("#split_order").val();
            	 if($.trim(cancel_remarks) != ''){
            	 	$.ajax({
                        type: "get",
                        url:"<?=admin_url('pos/cancel_order_items');?>",                
                        data: {cancel_remarks: cancel_remarks, order_item_id: order_item_id, split_id: split_id},
                        dataType: "json",
                        success: function (data) {
                            if(data.msg == 'success'){
                            	     $('#CancelorderModal').hide(); 
									 ajaxDatatimeout = setInterval(ajaxData, 60000);     	                      	
                            }else{
                                alert('not update waiter');
                            }
                        }    
                    }).done(function () {
				      
					});
            	 }            	  

            });*/
            $('.closemodal').click(function () {
            	$('#remarks').val('');
            	$('#order_item_id').val('');
				$('#split_order').val('');
				$('#cancel_qty').val('');
 				$('#CancelorderModal').hide(); 
				ajaxDatatimeout = setInterval(ajaxData, 60000);
            });
	    $('.cancelclosemodal').click(function () {
            	$('#remarks').val('');
            	$('#order_split_id').val('');
				
 				$('#CancelAllorderModal').hide(); 
				ajaxDatatimeout = setInterval(ajaxData, 60000);
            });
			
		
	
		
</script>
<script>
			
			function bilGeneratorTakeaway( split_id, count_id )
            {    
				$("#bil_split_type").val(split_id);
				if(count_id == 0 || count_id == 1){
					$(".count_div").hide();
				}
            	$('#bilModal').show();

            }
			
			$(document).on('click','#updateBil',function(){
            	 var split_id = $('#bil_split_type').val(); 
				 var bil_type = $('input[name=bil_type]:checked').val();
				 var url = '<?php echo  admin_url('pos') ?>';
				 if(bil_type == 1){
					 var bils = 1;
				 }else if(bil_type == 2){
					 var bils = $('#bils_number_auto').val(); 
				 }else if(bil_type == 3){
					 var bils = $('#bils_number_manual').val(); 
				 }
				 if(bils > 0){
					 window.location.href= url +'/billing/?order_type=2&bill_type='+bil_type+'&bils='+bils+'&splits='+split_id;
				 }else{
            	 	alert('Check any input feild empty');
				 }

            });
			
			$('.closebil').click(function () {
				$("#bil_split_type").val('');
            	$('#bilModal').hide();
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
    $(document).ready(function(){
	$('.cancel-type').iCheck({
	    checkboxClass: 'icheckbox_square-blue',
	    radioClass: 'iradio_square-blue',
	    increaseArea: '20%'
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
<script type="text/javascript" src="<?=$assets?>pos/js/pos.ajax.js"></script>
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
</script>
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
    /************ cancel all order items **************/
    function CancelAllOrderItems( split_id ,$remarks=0)
            {    
            	alert(split_id);
                clearTimeout(ajaxDatatimeout);
                $cancelQty = 'all';
                $('#order_split_id').val(split_id); 
                cancelAllorderPopup(split_id,$remarks);
			
            }
	   
            function cancelAllorderPopup(split_id,$remarks){
                if($remarks!=0){
                            $('#remarks').val('');
                            
                            $('#CancelAllorderModal').show();
                }else{
                    $msg = 'Are you sure want to cancel this order?';
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
			    // var split_id = $("#split_order").val();
				$.ajax({
				    type: "get",
				    url:"<?=admin_url('pos/cancel_all_order_items_bySplitID');?>",                
				    data: {split_id: split_id},
				    dataType: "json",
				    success: function (data) {
					if(data.msg == 'success'){
						//ajaxDatatimeout = setInterval(ajaxData, 60000);
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
	
    $(document).ready(function(){
	$(document).on('click','#cancel_allorderitem',function(){
            	 var cancel_remarks = $('#cancel-remarks').val();
		 
            	 var split_id = $('#order_split_id').val(); 
				
            	 if($.trim(cancel_remarks) != ''){
		    $(this).attr('disabled',true);
		    $submit_text = $(this).text();
		    $(this).text('please wait...');
            	 	$.ajax({
                        type: "get",
                        url:"<?=admin_url('pos/cancel_all_order_items_bySplitID');?>",                
                        data: {split_id:split_id,cancel_remarks: cancel_remarks},
                        dataType: "json",
                        success: function (data) {
			  //  $obj.attr('disabled',false);
			   // $obj.text($submit_text);
                            if(data.msg == 'success'){
                            	     $('#CancelAllorderModal').hide(); 
									
									 location.reload();      	                      	
                            }else{
				//$(this).attr('disabled',false);
                                alert('not cancelled');
                            }
                        }    
                    }).done(function () {
				      
					});
            	 } else{
		        $('#cancel-remarks').css('border','1px solid red');
		 }

            });
    })  
	    
    /******************** cancel all order items - end *******************/
</script>



</body>
</html>
