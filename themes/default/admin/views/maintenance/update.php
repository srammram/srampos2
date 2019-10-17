<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>


<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('Product_upgrade'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
	    <label><?=lang('current_version')?>:<span><?=$cur_version?></span> <span><?=lang('applied_on')?>:<?=date('Y-m-d',strtotime($last_applied_on))?></span></label>

            </div>

        </div>
	<div class="row">
            <div class="col-lg-12">
		<label><?=lang('Update')?>:<span><?=($latest_version!='')?lang('available').' version -'.$latest_version:lang('not_available');?></span></label>
		<?php if($latest_version!=''):?>
		    <button type="button" class="btn btn-primary update-product" data-version="<?=$latest_version?>">update</button>
		<?php endif; ?>
		
		<p><?=@$network_error?></p>
            </div>
        </div>
    </div>
</div>


<script>
    $(document).ready(function(){
	$('.update-product').click(function(){
	    $version = $(this).attr('data-version');
	    $('.ajaxCall-label').remove();
	    $('#ajaxCall').append('<div class="ajaxCall-label">Please wait.Backup is in progress<div>');
	    $.ajax({
		url:'<?=site_url('maintenance/backup')?>',
		type:'post',
		dataType:'json',
		data:{'version':$version},
		success:function(res){
		    $('.ajaxCall-label').remove();
		    $('#ajaxCall').append('<div class="ajaxCall-label">Please wait.downloading '+$version+'<div>');
		    $.ajax({
			url:'<?=site_url('maintenance/download')?>',
			type:'post',
			dataType:'json',
			data:{'version':$version},
			success:function(res){
			    console.log(res)
			    //location.reload();
			    $('.ajaxCall-label').remove();
			    $('#ajaxCall').append('<div class="ajaxCall-label">Please wait.upgrading to '+$version+'<div>');
			    $.ajax({
				url:'<?=site_url('maintenance/extract')?>',
				type:'post',
				data:{'version':$version,'file_path':res.file_path,'db_path':res.db_path},
				success:function(res){
				    location.reload();
				}
			    });
			}
		    });
		    
		}
	    })
	});
    });
</script>
<style>
#ajaxCall{
    /*background: none repeat scroll 0 0 black;*/
    position: fixed;
    display: none;
    opacity: 0.8;
    z-index: 1000001;
    left: 0;
    top: 0;
    right: 0;
    border-radius:0px;
    height: 100%;
    width: 100%;
}
</style>
