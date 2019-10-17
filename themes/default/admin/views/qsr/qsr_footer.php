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
 
var timeout = setInterval(show_nofication, 1000000);

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