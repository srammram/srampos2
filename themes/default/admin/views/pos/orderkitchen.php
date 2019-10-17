<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
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
	if($this->Settings->user_language == 'english' ) { 
         $this->load->view($this->theme . 'pos/pos_header');   
         }else{// for kimmo 
            $this->load->view($this->theme . 'pos/pos_header_kimmo'); 
         }
	?>
     
    <div id="content">
        <div class="c1">
            <div class="pos">
                <?php
                    if ($error) {
                        echo "<div class=\"alert alert-danger\"><button type=\"button\" class=\"close fa-2x\" data-dismiss=\"alert\">&times;</button>" . $error . "</div>";
                    }
                ?>               

                <div id="pos">
                
                            <div class="current_table_order">
                            	<div class="container custom_container">
                            		<div class="row">
                            
                            		<div id="orderkitchen_box">
                                    		
                                    </div>
                                   
                                </div>
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
<script>
function ajaxData(kitchen_type)
{
	$.ajax({
	  url: "<?=admin_url('pos/ajaxorder_kitchen');?>",
	  type: "get",
	  data: { 
		kitchen_type: kitchen_type
	  },
	  success: function(response) {
			$("#orderkitchen_box").html(response);
	  }
	});
}
$(document).ready(ajaxData(<?php echo $kitchen_type; ?>));
var ajaxDatatimeout = setInterval(ajaxData(<?php echo $kitchen_type; ?>), 100000);
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
                    <?= form_textarea('remarks', '', 'class="form-control kb-text" id="remarks" style="height:80px;"'); ?>
                </div>
                <input type="hidden" id="order_item_id" value=""/>
                <input type="hidden" id="split_id" value=""/>
		<input type="hidden" id="cancel_qty" value=""/>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="cancel_orderitem"><?=lang('send')?></button>
            </div>
        </div>
    </div>
</div>

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

<!-- <script type="text/javascript" src="<?=$assets?>js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/perfect-scrollbar.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/select2.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/custom.js"></script>
<script type="text/javascript" src="<?=$assets?>js/jquery.calculator.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/bootstrapValidator.min.js"></script>
<script type="text/javascript" src="<?=$assets?>pos/js/plugins.min.js"></script>
<script type="text/javascript" src="<?=$assets?>pos/js/parse-track-data.js"></script>
<script type="text/javascript" src="<?=$assets?>js/dragscrollable.js"></script>
<script type="text/javascript" src="<?=$assets?>js/dragscroll.js"></script> -->

<script type="text/javascript">
$('.sortable_table tbody').sortable({
    containerSelector: 'tr'
});
   
 
 $(document).ready(function(e) {
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
		var order = $(this).attr('data-order');
		
		var val = [];
		var processArray = [];
		var prepareArray = [];
		$('.status_'+order+':checkbox:checked').each(function(i){
			
			var currentValue = $(this).val();
			val[i] = currentValue;
			if($(this).attr('title') == 'Inprocess'){
				processArray[i] = currentValue;
			} else if($(this).attr('title') == 'Preparing') {
				prepareArray[i] = currentValue;
			}
			
			
			$('.multiple_'+order).prop('checked', false);
			
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
			if($("input[value="+currentValue+"]").attr('title') == 'Inprocess'){
				processArray[index] = currentValue;
			} else if($("input[value="+currentValue+"]").attr('title') == 'Preparing') {
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
		//clearTimeout(ajaxDatatimeout);
        var status = $(this).attr('data-status');
		var order_type = $(this).attr('data-order-type');
		var order_id = $(this).attr('data-order-id');
		var id = $(this).attr('data-id');
		
		$('#modal-loading').show();
		 $.ajax({
			type: "get",
			url:"<?=admin_url('pos/update_order_statusfrom_kitchen');?>",                
			data: {status: status, order_item_id: id, order_id: order_id, order_type: order_type},
			dataType: "json",
			success: function (data) {
				//ajaxDatatimeout = setInterval(ajaxData, 1000);
				location.reload();
			   
			}
		}).done(function () {
			$('#modal-loading').hide();
		});
		
    });
});

$(document).ready(function() {
    var get_status = $("#updateKitchen").text();     
      if($.trim(get_status) == "Ready" || $.trim(get_status) == "Serve" || $.trim(get_status) == "Complete" || $.trim(get_status) == "Closed" ) 
      { 
         $('#updateKitchen' ).prop('disabled', true);
      }
      else{
         $('#updateKitchen').prop('disabled', false);
      }
 });

      $('#remarks').on('focus',function(){
		$('#remarks').css('border','1px solid #ccc');
	    });
	
     function CancelOrderItem( status, id ,split_id,$remarks,$quantity){

                //clearTimeout(ajaxDatatimeout);
                $("#order_item_id").val(id);
                $("#split_id").val(split_id);console.log($remarks);
		
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
				    var cancel_type = "kitchen_cancel";
				    $.ajax({
					type: "get",
					url:"<?=admin_url('pos/cancel_order_items');?>",                
					data: {cancel_type:cancel_type,order_item_id: id, split_id: split_id,cancelqty:$cancelQty},
					dataType: "json",
					success: function (data) {
					    if(data.msg == 'success'){
						 //ajaxDatatimeout = setInterval(ajaxData, 1000);
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

            $(document).on('click','#cancel_orderitem',function(){
                 var cancel_remarks = $('#remarks').val(); 
                 var order_item_id = $('#order_item_id').val(); 
                 var split_id =  $("#split_id").val();
		 var $cancelQty = $('#cancel_qty').val();
		 var cancel_type = "kitchen_cancel";
                 if($.trim(cancel_remarks) != ''){
                    $.ajax({
                        type: "get",
                        url:"<?=admin_url('pos/cancel_order_items');?>",                
                        data: {cancel_type:cancel_type,cancel_remarks: cancel_remarks, order_item_id: order_item_id, split_id: split_id,cancelqty:$cancelQty},
                        dataType: "json",
                        success: function (data) {
                            if(data.msg == 'success'){
                                     $('#CancelorderModal').hide(); 
									 //ajaxDatatimeout = setInterval(ajaxData, 1000);
									 location.reload();
                            }else{
                                alert('not update waiter');
                            }
                        }    
                    }).done(function () {
                      
                    });
                 }  else{
		        $('#remarks').css('border','1px solid red');
		 }               

            });
            $('.closemodal').click(function () {
                $('#remarks').val('');
                $('#order_item_id').val('');
		$('#cancel_qty').val('');
                $('#CancelorderModal').hide(); 
				//ajaxDatatimeout = setInterval(ajaxData, 1000);
            });
            
    

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
</script>
<script type="text/javascript" charset="UTF-8"><?=$s2_file_date?></script>
<script>
$(document).ready(function () {
   $(document).on('click', '.kitchen_print', function () {
	   var id =   $(this).attr('id');
	    orderItems = $(this).closest('.kitchen-order-container').find('.kitchen-order-item-id:checked');
	    $oids = [];
	    $.each(orderItems,function(){
		$oids.push($(this).val());
	    });
	   
	   Popup($('#viewkitchen_'+id).html());
	});
});	
  
function Popup(data) {
	var mywindow = window.open('', 'sma_pos_print', 'height=500,width=300');
	mywindow.document.write('<html><head><title>Print</title>');
	mywindow.document.write('<link rel="stylesheet" href="<?=$assets?>styles/helpers/bootstrap.min.css" type="text/css" />');
	mywindow.document.write('<link rel="stylesheet" href="<?=$assets?>styles/helpers/print.css" type="text/css" />');
	mywindow.document.write('</head><body >');
	mywindow.document.write(data);
	mywindow.document.write('</body></html>');
	mywindow.print();
	mywindow.close();
	return true;
}
</script>
</body>
</html>                            

<script>
    function send_kot($order_id,$kitchen_id) {
	
	orderItems = $('.order-details-'+$order_id).find('.kitchen-order-item-id:checked');
	$oids = [];
	$.each(orderItems,function(){
	    $oids.push($(this).val());
	});
	
	if ($oids.length!=0) {	    	
	    $.ajax({
			    type: "post",
			    url:"<?=admin_url('pos/kitchen_kot_print_copy/');?>"+$order_id+'/'+$kitchen_id,                
			    data:{order_item_ids:$oids},
			    success: function (data) {
				$.each(orderItems,function(){
				    $(this).attr('checked',false);
				})
				bootbox.alert('sent to kot print');
			    }    
	    });
	}else{
	    bootbox.alert('Select order Items');
	}
    }
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




