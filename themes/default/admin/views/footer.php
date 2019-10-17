<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php if(!isset($isMobileApp)) : ?>
<div class="clearfix"></div>
<?= '</div></div></div></td></tr></table></div></div>'; ?>
<div class="clearfix"></div>
<footer>
<a href="#" id="toTop" class="blue" style="position: fixed; bottom: 30px; right: 30px; font-size: 30px; display: none;">
    <i class="fa fa-chevron-circle-up"></i>
</a>

    <p style="text-align:center;">&copy; <?= date('Y') . " " . $Settings->site_name; ?> (<a href="<?= base_url('documentation.pdf'); ?>" target="_blank">v<?= $Settings->version; ?></a>
        ) <?php if ($_SERVER["REMOTE_ADDR"] == '127.0.0.1') {
            echo ' - Page rendered in <strong>{elapsed_time}</strong> seconds';
        } ?></p>
</footer>
<?= '</div>'; ?>
<div class="modal fade in" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"></div>
<div class="modal fade in" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2" aria-hidden="true"></div>
<?php if(isset($user_assigned_stores)) : ?>
<div class="modal fade in" id="myStoreSelectModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('Select Store'); ?></h4>
        </div>
      <form id="login-store">
        <div class="modal-body">
            <div class="form-group">
                <label><?=lang('select_store')?></label>
                
                <?php $st = array();
                if(isset($user_assigned_stores)) :
                    foreach($user_assigned_stores as $k => $row){                    
                        $st[$row->id] = $row->name; 
                    }
                endif; 
                echo form_dropdown('store_id', $st, '', 'class="form-control input-tip" id="select-store"'); ?> 
                                   
            </div>
	    <div class="modal-footer">
            <?php echo form_submit('login_store', lang('login'), 'class="btn btn-primary"'); ?>
        </div>
        </div>
      </form>
    </div>
    </div>    
</div>
<?php endif; ?>

<div id="modal-loading" style="display: none;">
    <div class="blackbg"></div>
    <div class="loader"></div>
</div>
<div id="ajaxCall"><i class="fa fa-spinner fa-pulse"></i></div>
<?php unset($Settings->setting_id, $Settings->smtp_user, $Settings->smtp_pass, $Settings->smtp_port, $Settings->update, $Settings->reg_ver, $Settings->allow_reg, $Settings->default_email, $Settings->mmode, $Settings->timezone, $Settings->restrict_calendar, $Settings->restrict_user, $Settings->auto_reg, $Settings->reg_notification, $Settings->protocol, $Settings->mailpath, $Settings->smtp_crypto, $Settings->corn, $Settings->customer_group, $Settings->srampos_username, $Settings->purchase_code); ?>
<script type="text/javascript">
var dt_lang = <?=$dt_lang?>, dp_lang = <?=$dp_lang?>, site = <?=json_encode(array('url' => base_url(), 'base_url' => admin_url(), 'assets' => $assets, 'settings' => $Settings, 'dateFormats' => $dateFormats))?>;
var lang = {paid: '<?=lang('paid');?>', pending: '<?=lang('pending');?>', completed: '<?=lang('completed');?>', ordered: '<?=lang('ordered');?>', received: '<?=lang('received');?>', partial: '<?=lang('partial');?>', sent: '<?=lang('sent');?>', r_u_sure: '<?=lang('r_u_sure');?>', due: '<?=lang('due');?>', returned: '<?=lang('returned');?>', transferring: '<?=lang('transferring');?>', active: '<?=lang('active');?>', inactive: '<?=lang('inactive');?>', unexpected_value: '<?=lang('unexpected_value');?>', select_above: '<?=lang('select_above');?>',select_supplier: '<?=lang('select_supplier');?>', download: '<?=lang('download');?>' ,r_u_sure_reset: '<?=lang('r_u_sure_reset');?>'};
</script>
<?php
$s2_lang_file = read_file('./assets/config_dumps/s2_lang.js');
foreach (lang('select2_lang') as $s2_key => $s2_line) {
    $s2_data[$s2_key] = str_replace(array('{', '}'), array('"+', '+"'), $s2_line);
}
$s2_file_date = $this->parser->parse_string($s2_lang_file, $s2_data, true);
?>
<script type="text/javascript" src="<?= $assets ?>js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?= $assets ?>js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="<?= $assets ?>js/jquery.dataTables.dtFilter.min.js"></script>
<script type="text/javascript" src="<?= $assets ?>js/select2.min.js"></script>
<script type="text/javascript" src="<?= $assets ?>js/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?= $assets ?>js/bootstrapValidator.min.js"></script>
<script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>
<script type="text/javascript" src="<?= $assets ?>js/jquery.calculator.min.js"></script>
<!-- <script type="text/javascript" src="<?= $assets ?>js/core.js"></script> -->
<script type="text/javascript" src="<?= $assets ?>js/core.js?v=1"></script>
<script type="text/javascript" src="<?= $assets ?>js/perfect-scrollbar.min.js"></script>
<script src="<?= $assets ?>js/jquery.table2excel.min.js"></script>

<?php 
///if($this->Settings->procurment == 1){ echo $m;exit;
?>

<?= ($m == 'quotes' && ($v == 'add' || $v == 'edit')) ? '<script type="text/javascript" src="' . $assets . 'js/procurment/quotes.js?v1=1"></script>' : ''; ?>
<?= ($m == 'purchase_orders' && ($v == 'add' || $v == 'edit')) ? '<script type="text/javascript" src="' . $assets . 'js/procurment/purchase_orders.js?v1=1"></script>' : ''; ?>
<?= ($m == 'purchase_returns' && ($v == 'add' || $v == 'edit')) ? '<script type="text/javascript" src="' . $assets . 'js/procurment/purchase_returns.js?v1=1"></script>' : ''; ?>
<?= ($m == 'purchase_invoices' && ($v == 'add' || $v == 'edit')) ? '<script type="text/javascript" src="' . $assets . 'js/procurment/purchase_invoices.js?v1=1"></script>' : ''; ?>
<?= ($m == 'request' && ($v == 'add' || $v == 'edit')) ? '<script type="text/javascript" src="' . $assets . 'js/procurment/request.js?v1=1"></script>' : ''; ?>
<?= ($m == 'store_request' && ($v == 'add' || $v == 'edit')) ? '<script type="text/javascript" src="' . $assets . 'js/procurment/store_request.js?v1=1"></script>' : ''; ?>
<?= ($m == 'store_transfers' && ($v == 'add' || $v == 'edit')) ? '<script type="text/javascript" src="' . $assets . 'js/procurment/store_transfers.js?v1=1"></script>' : ''; ?>
<?= ($m == 'store_receivers' && ($v == 'add' || $v == 'edit')) ? '<script type="text/javascript" src="' . $assets . 'js/procurment/store_receivers.js?v1=1"></script>' : ''; ?>
<?= ($m == 'store_return_receivers' && ($v == 'add' || $v == 'edit')) ? '<script type="text/javascript" src="' . $assets . 'js/procurment/store_return_receivers.js?v1=1"></script>' : ''; ?>
<?= ($m == 'store_returns' && ($v == 'add' || $v == 'edit')) ? '<script type="text/javascript" src="' . $assets . 'js/procurment/store_returns.js?v1=1"></script>' : ''; ?>
<?= ($m == 'production' && ($v == 'add' || $v == 'edit')) ? '<script type="text/javascript" src="' . $assets . 'js/procurment/production.js?v1=1"></script>' : ''; ?>

<?php //} ?>


<?= ( ($m == 'products' || $m == 'recipe') && ($v == 'add_adjustment' || $v == 'edit_adjustment')) ? '<script type="text/javascript" src="' . $assets . 'js/adjustments.js"></script>' : ''; ?>

<script type="text/javascript" charset="UTF-8">var oTable = '', r_u_sure = "<?=lang('r_u_sure')?>";
    <?=$s2_file_date?>
    $.extend(true, $.fn.dataTable.defaults, {"oLanguage":<?=$dt_lang?>});
    $.fn.datetimepicker.dates['sma'] = <?=$dp_lang?>;
    $(window).load(function () {

        var seg2 = '<?=$this->uri->segment(2)?>';
	    var seg3 = '<?=$this->uri->segment(3)?>';

        var mm = '<?=$m?>';
        var vv = '<?=$m?>_<?=$v?>';  console.log('mm-'+mm); 
    	if (seg2=="procurment" && seg3 !="production") {

    	    $('.mm_'+seg2).addClass('active');
                $('.mm_'+seg2).find("ul").first().slideToggle();
    	    
    	    $('#'+seg2+'_<?=$m?>').addClass('active');
                $('#'+seg2+'_<?=$m?>').find("ul").first().slideToggle();
    	    
    	    
    	    $('#'+seg2+'_<?=$m?>_<?=$v?>').addClass('active');
    	    
    	}else if(seg3 == 'production' || seg3 == 'add_ingredients' || seg3 == 'list_ingredients' || seg3 == 'ingredients_import_csv'|| seg3 == 'edit_ingredients'){  
            var mm = '<?=$m?>';
            var vv = '<?=$v?>';                        
            if(mm =='recipe' && vv =='list_ingredients'){
                seg3 ='recipe_management';                 
                  var mm = 'ingredients'; 
                 var  vv = 'index';                  
            }else if(mm =='recipe' && vv =='add_ingredients'){
                seg3 ='recipe_management';                 
                  var mm = 'ingredients'; 
                 var  vv = 'add';                  
            }else if(mm =='recipe' && vv =='edit_ingredients'){
                seg3 ='recipe_management';                 
                  var mm = 'ingredients'; 
                 var  vv = 'edit';                  
            }else if(mm =='recipe' && vv =='ingredients_import_csv'){
                seg3 ='recipe_management';                 
                  var mm = 'ingredients'; 
                 var  vv = 'import_csv';                  
            }else if(seg3 ='production'){
                seg3 ='recipe_management';
            }        
            $('.mm_'+seg3).addClass('active');
            $('.mm_'+seg3).find("ul").first().slideToggle();
            $('#'+seg3+'_'+mm).addClass('active');
            $('#'+seg3+'_'+mm).find("ul").first().slideToggle();
            $('#'+seg3+'_'+mm+'_'+vv).addClass('active');
        
        }else if (mm=='reports') {

    	    $('.mm_<?=$m?>').addClass('active');
                $('.mm_<?=$m?>').find("ul").first().slideToggle();
                $('#<?=$m?>_<?=$v?>').addClass('active');
                $('.mm_<?=$m?> a .chevron').removeClass("closed").addClass("opened");
    	   
    	    $('#'+vv).closest('ul.level-3-menu').slideToggle();
    	 }else if(mm != 'system_settings' && seg3 !="production"){   
                $('.mm_<?=$m?>').addClass('active');
                $('.mm_<?=$m?>').find("ul").first().slideToggle();
                $('#<?=$m?>_<?=$v?>').addClass('active');
                $('.mm_<?=$m?> a .chevron').removeClass("closed").addClass("opened");
        }else{	    
                if(vv == 'system_settings_index')
                {                
                    $('.mm_system_settings,.mm_pos').addClass('active');
                    $('.mm_pos').find("ul").first().slideToggle();
                    $('.mm_tables').removeClass('active');
                    $('#system_settings_index').addClass('active');
                }else if(vv == 'system_settings_warehouses')
                {
                    $('.mm_tables,.mm_system_settings').addClass('active');
                    $('.mm_system_settings').find("ul").first().slideToggle();
                    $('.mm_system_settings').removeClass('active');
                    $('#system_settings_warehouses').addClass('active');
                }else{
                    $('.mm_<?=$m?>').addClass('active');
                    $('.mm_<?=$m?>').find("ul").first().slideToggle();
                    $('#<?=$m?>_<?=$v?>').addClass('active');
                    $('.mm_<?=$m?> a .chevron').removeClass("closed").addClass("opened");
                }
        }
    });
	
	function procurment_notification()
	 {	
	 	$.ajax({
		type: "GET",
			url: '<?=admin_url('procurment/inventory/notification');?>',
			success: function(data){
				
				var datahtml;
			
				var rdata = JSON.parse(data);	
				var key_array =  new Array();
				var count = 0;
				
				if(rdata){
					count = rdata.length;
				
					for(i=0; i<rdata.length; i++){
						if(rdata[i].links != ''){
							var links = rdata[i].links;
						}else{
							var links = 'javascript:void(0)';
						}
						datahtml += '<li><a href="'+links+'"><h3>'+rdata[i].title+'</h3><p>'+rdata[i].message+'</p></a></li>';
						key_array.push(rdata[i].id);
					}
				}
				
				
				$('.procurment_number').text(count);
				$('#procurment_notification_key').val(key_array.join());
				$('.procurment_list').html(datahtml); 
				
			}
		});
	 	
	 }	
</script>
<?= (DEMO) ? '<script src="'.$assets.'js/ppp_ad.min.js"></script>' : ''; ?>

<script>
$(".numberonly").keypress(function (event){
	
	if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
		event.preventDefault();
	}
  
	});

$(document).ready(function(){
    $('form').attr('autocomplete', 'off');
    $('input').attr('autocomplete', 'off');
});

</script>
<?php endif; ?>
<script src="<?=base_url('node_modules/socket.io/node_modules/socket.io-client/socket.io.js')?>"></script>
<script type="text/javascript" src="<?=$assets?>js/socket/client.js"></script>
<script>
$(document).ready(function(){
	/*procurment_notification(); */
});
    <?php
       $URL = $this->uri->segment(2);       
       $report_view_access=$this->session->userdata('report_view_access');
         if($this->pos_settings->taxation_report_settings == 1 && $URL == 'reports' && $report_view_access == 0 ) {
     ?> 
        $('#myModal').modal({remote: site.base_url + 'reports/modal_view/'});         
    <?php } ?>
    <?php if(isset($user_assigned_stores)) : ?>
     $('#myStoreSelectModal').modal({backdrop:'static', keyboard:false});
    <?php endif; ?>
    $(document).ready(function(){
	$('#login-store input[type="submit"]').click(function(e){
	    e.preventDefault();
	    $.ajax({
		url : '<?=admin_url()?>auth/set_store/',
		type:"post",
		data :{'store_id':$('#select-store').val()},
		dataType:'json',
		success:function(res){
		   
			window.location.reload();
		    
		}
	    })
	})
    })
</script>
</body>
</html>
