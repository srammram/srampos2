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
    <script>var assets = '<?=$assets?>';var baseurl = '<?=base_url()?>';var curr_page = 'bbqtables'; var curr_func="refresh_bbqtables";</script>
    
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
                
                	 
                    
                    <input type="hidden" id="order_type" value="<?php echo $order_type; ?>" name="order_type">
                    
                    <div id="tables_box">
                    
                    </div>
                    
                    <div style="clear:both;"></div>
                </div>
                <div style="clear:both;"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade in" id="BBQmenuModal" tabindex="-1" role="dialog" aria-labelledby="BBQmenuModalLabel"
     aria-hidden="true" style="z-index:99999999999">
    <div class="modal-dialog modal-md">
       <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close closebil" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="BBQmenuModalLabel"><?php echo lang('bbq_menu'); ?></h4>

            <input type="hidden" name="order_type" id="ordertype">
            <input type="hidden" name="table_id" id="tableid">
            <input type="hidden" name="split_id" id="splitid">
            <input type="hidden" name="customer_id" id="customerid">           
        </div>
        <div class="modal-body">   
             
            <div class="row text-center  ">
            <div class="col-xs-12 table_front" style="position: relative;top: 0pc;left: 0px;right: 0;bottom: 0;z-index: 99999;
    height: auto;width: 100%;overflow: hidden !important;border-radius: 4px;">
    			<?php
                	$bbqmenu = $this->site->getBBQmenuList();                	
						foreach ($bbqmenu as $tables) {						    						                   
                ?>
				<button class="bbq_menu location" value="<?= $tables->bbq_menu_id ?>" data-id="<?= $tables->bbq_menu_id ?>">
                    <img src="<?=$assets?>images/dine_in.png">                    
                    <p><?=lang($tables->name)?></p>
                </button>
			<?php } ?>
            	<!-- <input type="hidden" name="pop_table_id" id="pop_table_id" > -->
                
                <!-- <button class="orderpath location" value="bbq_link" <?php if($this->Settings->bbq_enable){ echo ''; }else{  echo 'disabled'; }  ?> >
                    <img src="<?=$assets?>images/bbq.png">
                    <p><?=lang('BBQ')?></p>
                </button>
                
                <button class="orderpath location" value="dine_link" <?php if($this->sma->actionPermissions('dinein')){ echo ''; }else{  echo 'disabled'; }  ?> >
                    <img src="<?=$assets?>images/dine_in.png">
                    <p><?=lang('dine_in')?></p>
                </button> -->
            </div>
			</div>
            <div class="clearfix"></div>

        </div>
        
        
    </div>
    </div>
</div>





            	<!-- <label><?=lang('select_sales_menu')?></label>
            	<?php
                	$bbqmenu = $this->site->getBBQmenuList();                   
                ?>
					<select style="display: "  name="bbq_menu_id" class="form-control pos-input-tip bbq_menu_id" id="bbq_menu_id">						
						<?php
						foreach ($bbqmenu as $tables) {						    
						?>
						<option value="<?php echo $tables->id; ?>" data-id="<?php echo $tables->bbq_menu_id; ?>"><?php echo $tables->name; ?>
						</option>
					<?php } ?>

					</select> -->
        </div>
      <!--   <div class="modal-footer">
        	<button type="button" class="btn btn-primary" id="bbq_menu"><?=lang('submit')?></button>            
        </div>   -->
             
    </div>
    </div>
</div>



<div class="modal fade in" id="coverModal" tabindex="-1" role="dialog" aria-labelledby="coverModalLabel"
     aria-hidden="true" style="z-index:99999999999">
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



<div class="modal fade in" id="customerModal" tabindex="-1" role="dialog" aria-labelledby="customerModalLabel"
     aria-hidden="true" style="z-index:9999" data-backdrop="static" data-keyboard="false" >
    <div class="modal-dialog modal-md">
       <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="customerModalLabel"><?php echo lang('add_BBQ'); ?></h4>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'id' => 'add-customer-form');
        echo admin_form_open_multipart("pos/add_bbq", $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>						
            <div class="row">
                <div class="col-md-12">
                	
                    <input type="hidden" name="warehouse_id" id="warehouse_id" value="<?php echo $this->session->userdata('warehouse_id'); ?>">
                    
                    <input type="hidden" name="table_id" id="table_id" value="">
                    <input type="hidden" name="bbq_set_id" id="bbq_set_id" value="1">
                    <input type="hidden" name="bbq_menu_id" id="bbq_menu_id" value="0">   

                    <div class="form-group col-lg-12">
						<?= lang("customer_type"); ?>
                        <?php $types = array('0' => lang('select_type'), '1' => lang('new_customer'), '2' => lang('regular'));
                echo form_dropdown('customer_type', $types, 1, 'class="form-control select2" id="customer_type" required="required"'); ?>
                    </div>
                    
                    <div class="form-group col-lg-12 customer" style="display:none">
						<?= lang("customer"); ?>
                        <?php
						$rc[''] = "";
						if(!empty($rcustomer)){
							foreach ($rcustomer as $customer) {
								$rc[$customer->id] = $customer->name;
							}
						}
						echo form_dropdown('customer_id', $rc, '', 'class="form-control select2 customer_id" id="customer_id" placeholder="' . lang("select") . " " . lang("customer") . '" style="width:100%"')
						?>
                    </div>
                    
                    
                    <div class="form-group col-lg-6">
                        <?= lang("name", "name"); ?>
                        <?php echo form_input('name', '', 'class="form-control tip" id="name" required="required" data-bv-notempty="true"'); ?>
                    </div>
                    
                    <div class="form-group col-lg-6">
                        <?= lang("phone", "phone"); ?>
                        <input type="tel" name="phone" class="form-control numberonly" required="required" maxlength="10" id="phone"/>
                    </div>
                    
                    <div class="form-group col-lg-4">
						<?= lang("cover_adults"); ?>
                        <?php $coversLimit = $Settings->bbq_covers_limit;
						for($i=1; $i<=$coversLimit; $i++){
						$adults[$i] = $i;
						}
                echo form_dropdown('number_of_adult', $adults, '', 'class="form-control select" id="number_of_adult" required="required"'); ?>
                    </div>
                    
                    <div class="form-group col-lg-4">
						<?= lang("cover_childs"); ?>
                        <?php 
						for($j=0; $j<=$coversLimit; $j++){
						$childs[$j] = $j;
						}
                echo form_dropdown('number_of_child', $childs, '', 'class="form-control select" id="number_of_child"'); ?>
                    </div>
                    
                    <div class="form-group col-lg-4">
						<?= lang("cover_kids"); ?>
                        <?php 
						for($j=0; $j<=$coversLimit; $j++){
						$childs[$j] = $j;
						}
                echo form_dropdown('number_of_kids', $childs, '', 'class="form-control select" id="number_of_kids"'); ?>
                    </div>
                    
                    
                                       
                    <div class="form-group adult_price col-lg-4">
                        <?= lang("adult_price", "adult_price"); ?>
                        <?php echo form_input('adult_price', 0, 'class="form-control tip" readonly id="adult_price" '); ?>
                    </div>
                    <div class="form-group child_price col-lg-4">
                        <?= lang("child_price", "child_price"); ?>
                        <?php echo form_input('child_price', 0, 'class="form-control tip" readonly id="child_price" '); ?>
                        <?php echo form_hidden('discount', '', 'class="form-control tip" readonly id="discount" '); ?>
                    </div>
                    <div class="form-group kids_price col-lg-4">
                        <?= lang("kids_price", "kids_price"); ?>
                        <?php echo form_input('kids_price', 0, 'class="form-control tip" readonly id="kids_price" '); ?>
                    </div>
                </div>
            </div>


        </div>
        <div class="modal-footer">
            <?php echo form_submit('add_bbq', lang('submit'), 'class="btn btn-primary"'); ?>
        </div>
        <?php echo form_close(); ?>
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
	  url: "<?=admin_url('pos/ajax_bbq_tables');?>",
	  type: "get",
	  success: function(response) {
			$("#tables_box").html(response);
	  }
	});
}
function ajaxData_table($tableid)
{
	$.ajax({
	  url: "<?=admin_url('pos/ajax_bbq_tables_byID');?>",
	  type: "post",
	  data:{id:$tableid},
	  success: function(response) {
	    console.log(6)
			$("#tables_box #table-"+$tableid).html(response);
	  }
	});
}
$(document).ready(ajaxData);
//var ajaxDatatimeout = setInterval(ajaxData, 100000000);
</script>


<script type="text/javascript">



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
	});	
});

	$('.closebil').click(function () {
		$("#ordertype").val('');
		$("#tableid").val('');
		$("#splitid").val('');
		$("#customerid").val('');
		$('#BBQmenuModal').hide();
    });

$(document).on('click', '.table_id', function(){	

	var menucount = "<?php echo $this->site->getBBQmenuListCount(); ?>";
	var order_type = $('#order_type').val();	
	var table_id = $(this).val();
	var split_id = $(this).attr('data-split');
	var customer_id = $(this).attr('dataCustomer');	
	var url = '<?php echo  admin_url('pos') ?>';

	if(split_id == ''){

    if(menucount > 1){
    	$('#BBQmenuModal').css('overflow-y', 'scroll');
		$('#ordertype').val($('#order_type').val());
		$('#tableid').val($(this).val());
		$('#splitid').val($(this).attr('data-split'));
		$('#customerid').val($(this).attr('dataCustomer'));
		$('#BBQmenuModal').css('overflow-y', 'scroll');
		// $('#BBQmenuModal').appendTo("body").modal('show', {backdrop: 'static', keyboard: true}); 
    	$('#BBQmenuModal').show();

    }else{	

	var order_type = $('#order_type').val();	
	var table_id = $(this).val();
	var split_id = $(this).attr('data-split');
	var customer_id = $(this).attr('dataCustomer');	
	var url = '<?php echo  admin_url('pos') ?>';
	$('#modal-loading').show();		
		$.ajax({
			type: "get",
			url: "<?=admin_url('pos/tablecheck');?>",
			data: {table_id: table_id, order_type: order_type},
			dataType: "json",
			success: function (data) {
				// ajaxDatatimeout = setInterval(ajaxData, 1000);
				if(data.status == 'success'){					
					window.location.href= url +'/order_bbqtable/?table='+table_id;	
				}else{
					if(split_id != ''){
						window.location.href= url +'/bbq?order=4&table='+table_id+'&split='+split_id+'&same_customer='+customer_id+'&set=1';
					}else{

						$('#table_id').val(table_id);
                        $('#adult_price').val(data.adult_price);
                        $('#child_price').val(data.child_price);
                        $('#kids_price').val(data.kids_price);
                        $('#bbq_menu_id').val(data.bbq_menu_id);                        
						$('#customerModal').css('overflow-y', 'scroll');
	 					$('#customerModal').appendTo("body").modal('show', {backdrop: 'static', keyboard: false}); 
					}
				}
			}

		}).done(function () {
			$('#modal-loading').hide();
		});
	}
	}else{
		var order_type = $('#order_type').val();	
		var table_id = $(this).val();
		var split_id = $(this).attr('data-split');
		var customer_id = $(this).attr('dataCustomer');	
		var url = '<?php echo  admin_url('pos') ?>';

		window.location.href= url +'/bbq?order=4&table='+table_id+'&split='+split_id+'&same_customer='+customer_id+'&set=1';
	}	
	
});

$(document).on('click', '.bbq_menu', function(){	
	var menu_id =$(this).val();			 
	var order_type = $('#ordertype').val();	
	var table_id = $('#tableid').val();
	var split_id = $('#splitid').val();
	var customer_id = $('#customerid').val();	
	var url = '<?php echo  admin_url('pos') ?>';
	$('#BBQmenuModal').hide();	
	$('#modal-loading').show();		
		$.ajax({
			type: "get",
			url: "<?=admin_url('pos/tablecheckwithbbq');?>",
			data: {table_id: table_id, order_type: order_type,menu_id:menu_id},
			dataType: "json",
			success: function (data) {
				$('#adult_price').val(data.adult_price);
				$('#child_price').val(data.child_price);
				$('#kids_price').val(data.kids_price);
				$('#bbq_menu_id').val(data.bbq_menu_id);
				$('#table_id').val(table_id);
				$('#customerModal').css('overflow-y', 'scroll');
				$('#customerModal').appendTo("body").modal('show', {backdrop: 'static', keyboard: false}); 					
			}

		}).done(function () {
			$('#modal-loading').hide();
		});



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

$(document).on('change', '#bbq_set_id', function(){
	var type = $(this).val();
	var aprice = $('option:selected', this).attr('data-adultprice');
	var cprice = $('option:selected', this).attr('data-childprice');
	var kprice = $('option:selected', this).attr('data-kidsprice');
	var dis = $('option:selected', this).attr('data-discount');
	
	if(type != ''){
		
		$('.adult_price').show();
		$('.child_price').show();
		$('.kids_price').show();
		$('#adult_price').val(aprice);
		$('#child_price').val(cprice);
		$('#kids_price').val(kprice);
		$('#discount').val(dis);
		
	}else{
		$('.adult_price').hide();
		$('.child_price').hide();
		$('.kids_price').hide();
		$('#adult_price').val('');
		$('#child_price').val('');
		$('#kids_price').val('');
		$('#discount').val('');
	}
});


$(document).on('click', '.cover_cancel', function(){
	 var bbqcode = $(this).attr('data-bbq');	
	
	 bootbox.confirm("Are you sure?", function(result) {
        if(result == true) {
            $.ajax({
				type: "get",
				async: false,
				url: "<?= admin_url('pos/cancelBBQ') ?>/" + bbqcode,
				dataType: "json",
				success: function (data) {
					
					location.reload();				
		
				}
			});
           
        }
	});
	return false;
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

$(".numberonly").keypress(function (event){
	if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
		event.preventDefault();
	}
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
<script type="text/javascript" src="<?=$assets?>js/dragscrollable.js"></script>
<script type="text/javascript" src="<?=$assets?>js/dragscroll.js"></script>
<script>
$(document).ready(function() {
    $('#customer_type').select2();
	$('#customer_id').select2();
});
</script>
</body>
</html>
