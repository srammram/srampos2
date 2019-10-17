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
      <?php if($this->pos_settings->font_family ==0) { ?>
            <link rel="stylesheet" href="<?=$assets?>styles/theme.css" type="text/css"/>
    <?php }elseif ($this->pos_settings->font_family ==1) { ?><!-- for kimmo client and font family AKbalthom-KhmerNew  -->
        <link rel="stylesheet" href="<?=$assets?>styles/theme_for_kimmo.css" type="text/css"/>
    <?php } ?>
    <link rel="stylesheet" href="<?=$assets?>styles/style.css" type="text/css"/>
    <link rel="stylesheet" href="<?=$assets?>pos/css/posajax.css" type="text/css"/>
    <link rel="stylesheet" href="<?=$assets?>pos/css/print.css" type="text/css" media="print"/>
    <script type="text/javascript" src="<?=$assets?>js/jquery-2.0.3.min.js"></script>
    <script type="text/javascript" src="<?=$assets?>js/jquery-migrate-1.2.1.min.js"></script>
    <link href="<?= $assets ?>styles/flipclock.css" rel="stylesheet"/>    
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

    <script>var assets = '<?=$assets?>';var baseurl = '<?=base_url()?>';var curr_page = 'tables';var curr_func="refresh_tables";</script>
    

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
                <?php
                    if (!empty($message)) {
                        echo "<div class=\"alert alert-success\"><button type=\"button\" class=\"close fa-2x\" data-dismiss=\"alert\">&times;</button>" . $message . "</div>";
                    }
                ?>
                
              
               
                <div id="pos">
                
                	 
                    
                    <input type="hidden" id="order_type" value="<?php echo $order_type; ?>" name="order_type">
                    
                    <div id="tables_box">
			<?php if(!$isNightauditDone) : ?>
			    <div class="col-sm-6 col-sm-offset-3 col-xs-12 order_cancel_data alert-danger fade in">
				<?=lang('Please do nightaudit')?>
			    </div>				    
			<?php endif; ?>
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
function ajaxData()
{
    <?php if($isNightauditDone) : ?>
	$.ajax({
	  url: "<?=admin_url('pos/ajax_tables');?>",
	  type: "get",
	  success: function(response) {
	    console.log(6)
			$("#tables_box").html(response);
	  }
	});
    <?php endif; ?>
}
function ajaxData_table($tableid)
{
	
	$.ajax({
	  url: "<?=admin_url('pos/ajax_table_byID');?>",
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
$(document).on('click', '.table_id', function(){		

	
	var order_type = $('#order_type').val();	
	var table_id = $(this).val();
	var url = '<?php echo  admin_url('pos') ?>';
	$('#modal-loading').show();
		
		$.ajax({
			type: "get",
			url: "<?=admin_url('pos/tablecheckdine');?>",
			data: {table_id: table_id, order_type: order_type},
			dataType: "json",
			success: function (data) {
				ajaxDatatimeout = setInterval(ajaxData, 1000);
				if(data.status == 'bill-generated'){
					window.location.href= url +'/order_biller/?type='+order_type+'&table='+table_id;	
				}else if(data.status == 'success'){
					window.location.href= url +'/order_table/?table='+table_id;	
				}else{
					window.location.href= url +'/?order='+order_type+'&table='+table_id;	
				}
			}

		}).done(function () {
			$('#modal-loading').hide();
		});
	
});
</script>
<script type="text/javascript" src="<?=$assets?>js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/perfect-scrollbar.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/select2.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="<?=$assets?>pos/js/plugins.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/custom.js"></script>

</body>
</html>
