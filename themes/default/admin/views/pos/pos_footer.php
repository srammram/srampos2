<?php $this->load->view($this->theme . 'pos/transaction_date_confirm_popup'); ?>

	<script type="text/javascript" src="<?=$assets?>js/jquery.notify.js"></script>
	<?php if($this->site->isSocketEnabled()) { ?>
		<script src="<?=base_url('node_modules/socket.io/node_modules/socket.io-client/socket.io.js')?>"></script>
		<script type="text/javascript" src="<?=$assets?>js/socket/socket_configuration.js?v=1"></script>
		<script type="text/javascript" src="<?=$assets?>js/socket/client.js"></script>
	<?php } ?>


<?php unset($Settings->setting_id, $Settings->smtp_user, $Settings->smtp_pass, $Settings->smtp_port, $Settings->update, $Settings->reg_ver, $Settings->allow_reg, $Settings->default_email, $Settings->mmode, $Settings->timezone, $Settings->restrict_calendar, $Settings->restrict_user, $Settings->auto_reg, $Settings->reg_notification, $Settings->protocol, $Settings->mailpath, $Settings->smtp_crypto, $Settings->corn, $Settings->customer_group, $Settings->srampos_username, $Settings->purchase_code); ?>


<script>
	 var dt_lang = <?=$dt_lang?>, dp_lang = <?=$dp_lang?>, site = <?=json_encode(array('url' => base_url(), 'base_url' => admin_url(), 'assets' => $assets, 'settings' => $Settings, 'dateFormats' => $dateFormats))?>, pos_settings = <?=json_encode($pos_settings);?>, p_page = 0, per_page = 0;

var product_variant = 0, shipping = 0, p_page = 0, per_page = 0, tcp = "<?=$tcp?>", pro_limit = <?= $pos_settings->pro_limit; ?>,
        brand_id = 0, obrand_id = 0, cat_id = "<?=$pos_settings->default_category?>", ocat_id = "<?=$pos_settings->default_category?>", sub_cat_id = 0, osub_cat_id,
        count = 1, an = 1, DT = <?=$Settings->default_tax_rate?>,
        product_tax = 0, invoice_tax = 0, product_discount = 0, order_discount = 0, total_discount = 0, total = 0, total_paid = 0, grand_total = 0,
        KB = <?=$pos_settings->keyboard?>, tax_rates =<?php echo json_encode($tax_rates); ?>;
    var protect_delete = <?php if (!$Owner && !$Admin) {echo $pos_settings->pin_code ? '1' : '0';} else {echo '0';} ?>, billers = <?= json_encode($posbillers); ?>, biller = <?= json_encode($posbiller); ?>;
    var username = '<?=$this->session->userdata('username');?>', order_data = '', bill_data = '';
    

var site_url = '<?=admin_url()?>';
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
 // show_nofication();
// var timeout = setInterval(show_nofication, 1000);

$(document).ready(function(){
	$(".notification").click(function(){
		
		$(this).find(".content_notification").slideToggle(500, function(){
			 if ($("#content_notification").css('display') == 'block'){
				  //clearTimeout(timeout);
				 // alert(timeout)
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
				// timeout = setInterval(show_nofication, 1000); 
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