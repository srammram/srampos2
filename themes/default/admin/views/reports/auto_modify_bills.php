<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
  <link rel="stylesheet" href="<?= $assets ?>styles/jquery-ui.css">
  <script src="<?= $assets ?>js/jquery-ui.js"></script>
  <script type="text/javascript" src="<?=$assets ?>js/auto_edit_bills.js"></script>
<?php
$v = "";
if ($this->input->post('start_date')) {
    $v .= "&start_date=" . $this->input->post('start_date');
}
if ($this->input->post('end_date')) {
    $v .= "&end_date=" . $this->input->post('end_date');
}

?>




<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-heart"></i><?= lang('auto_modify_bills'); ?> <?php
            if ($this->input->post('start_date')) {
                echo "From " . $this->input->post('start_date') . " to " . $this->input->post('end_date');
            }
            ?>
        </h2>

        <div class="box-icon">
            <ul class="btn-tasks">
                <li class="dropdown">
                    <a href="#" class="toggle_up tip" title="<?= lang('hide_form') ?>">
                        <i class="icon fa fa-toggle-up"></i>
                    </a>
                </li>
                <li class="dropdown">
                    <a href="#" class="toggle_down tip" title="<?= lang('show_form') ?>">
                        <i class="icon fa fa-toggle-down"></i>
                    </a>
                </li>
            </ul>
        </div>
        <div class="box-icon">
            <ul class="btn-tasks">
                <li class="dropdown">
                    <a href="javascript:void(0);" id="excel_report" class="excel_report" title="<?= lang('download_xls') ?>">
                        <i class="icon fa fa-file-excel-o"></i>
                    </a>
                </li>
                <li class="dropdown">
                    <a href="#" id="image" class="tip" title="<?= lang('save_image') ?>">
                        <i class="icon fa fa-file-picture-o"></i>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?= lang('customize_report'); ?></p>
                <div id="form">
                 <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'id' => 'auto-modifybill-search');
             echo admin_form_open("reports/auto_modify_bills", $attrib);?> 

                    <div class="row">  

						
			<div class="col-sm-2">
                            <div class="form-group">
                                <?= lang("target_amount", "target_amount"); ?>
                                <?php echo form_input('target_amount', ($this->session->userdata('target_amount')), 'class="form-control "  autocomplete="off" id="target_amount"'); ?>                                
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <?= lang("start_date", "start_date"); ?>
                                <?php echo form_input('start_date', ($this->session->userdata('start_date')), 'class="form-control " autocomplete="off"  id="start_date"'); ?>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <?= lang("end_date", "end_date"); ?>
                                <?php echo form_input('end_date', ($this->session->userdata('end_date')), 'class="form-control "  autocomplete="off" id="end_date"'); ?>                                
                            </div>
                        </div>
			<div class="col-sm-2">
                            <div class="form-group">
                                <?= lang("bill_search_type", "bill_search_type"); ?></label>
				<div class="form-group">
				    <?php $bs = ($this->session->userdata('bill_search_type'))?$this->session->userdata('bill_search_type'):'all';?>
				    <select name="bill_search_type" class="form-control select" id="bill_search_type" style="width:100px">
					<option value="all" <?php if($bs=="all") { echo 'selected="selected"';} ?>>All</option>
					<option value="single" <?php if($bs=="single") { echo 'selected="selected"';} ?>>Single Bill</option>
					<option value="range" <?php if($bs=="range") { echo 'selected="selected"';} ?>>Bill Range</option>
				    </select>
				</div>
                            </div>
                        </div>
			
			<div class="col-sm-2 single-bill" style="display: none;">
                            <div class="form-group">
                                <?= lang("bill_no", "bill_no"); ?>
                                <?php echo form_input('bill_no', ($this->session->userdata('bill_no')), 'class="form-control " autocomplete="off"  id="bill_no"'); ?>
                            </div>
                        </div>
			
			<div class="col-sm-2 bill-range" style="display: none;">
                            <div class="form-group">
                                <?= lang("bill_no_from", "bill_no_from"); ?>
                                <?php echo form_input('bill_no_from', ($this->session->userdata('bill_no_from')), 'class="form-control " autocomplete="off"  id="bill_no_from"'); ?>
                            </div>
                        </div>
                        <div class="col-sm-2 bill-range" style="display: none;">
                            <div class="form-group">
                                <?= lang("bill_no_to", "bill_no_to"); ?>
                                <?php echo form_input('bill_no_to', ($this->session->userdata('bill_no_to')), 'class="form-control "  autocomplete="off" id="bill_no_to"'); ?>                                
                            </div>
                        </div>
						
                        <!--<div class="col-sm-2">
                            <div class="form-group">
                             <?= lang("bill_no", "bill_no"); ?>
                             <select class="form-control col-sm-2" name="bill_no" id="bill_no">
                                <option value="">Select</option>
                            </select>                               
                            </div>
                        </div>-->
						
						
						
                      
                        <div class="col-sm-2 type-container">
			<?= lang("type", "type"); ?></label>
                            <div class="form-group">
                                
                                <?php $t_w = ($this->session->userdata('type'))?$this->session->userdata('type'):'all';?>
                                <select name="type" class="form-control select" id="type" style="width:100px">
                                    <option value="all" <?php if($t_w=="all") { echo 'selected="selected"';} ?>>All</option>
                                    <option value="1" <?php if($t_w=="1") { echo 'selected="selected"';} ?>>Dont Print</option>
                                    <option value="0" <?php if($t_w=="0") { echo 'selected="selected"';} ?>>Print</option>
                                </select>
                            </div>
                        </div>
			
                        <!--<div class="col-sm-2">
			    <?= lang("Show", "Show"); ?>
                            <div class="form-group">
                                <?php $limit = $this->session->userdata('list_limit');?>
                               <select name="pagelimit" class="form-control select" id="pagelimit" style="width:100%">
                                    <option value=""></option>
                                    <option value="1" <?php if($limit=="1") { echo 'selected="selected"';} ?>>1</option>
                                    <option value="2" <?php if($limit=="2") { echo 'selected="selected"';} ?>>2</option>
                                    <option value="4" <?php if($limit=="4") { echo 'selected="selected"';} ?>>4</option>
                                    <option value="10"  <?php if(!$limit || $limit=="10") { echo 'selected="selected"';} ?>>10</option>
                                    <option value="15" <?php if($limit=="15") { echo 'selected="selected"';} ?>>15</option>
                                    <option value="30" <?php if($limit=="30") { echo 'selected="selected"';} ?>>30</option>
                                    <option value="100 <?php if($limit=="100") { echo 'selected="selected"';} ?>">100</option>
                                    <option value="all" <?php if($limit=="all") { echo 'selected="selected"';} ?>>All</option>
                                </select>                               
                            </div>
                        </div>-->
			</div>
		
		
			
			<!-----Recipe Category List -------------------------->
			
			<div class="col-sm-2">
			   <div class="form-group">
				<label><input type="checkbox" name="" id="select-all-items">&nbsp;<?= lang("select_all_items"); ?></label>
				    
				    </div>
			</div>
			<?php $index = 0 ; ?>
            
			    <div class="category-container col-lg-12">
			    
			    <div class="product-group-row">
				<div class="recipe-group-list">
				    <ul class="level-1-menu">
				    <?php foreach($recipe_groups as $kk => $row_1) : ?>
					<li class="level-1-menu-li">
					    <div class="level-1-menu-div">
					   <input type="hidden" name="group[<?=$index?>][recipe_group_id][<?=$kk?>][id]" value="<?=@$row_1->id?>">
					    <div class="category-name-container">
						<input type="checkbox" name="group[<?=$index?>][recipe_group_id][<?=$kk?>][id]" value="<?=@$row_1->id?>" class="recipe-group" data-index="<?=$index?>"><label for="pos-door_delivery_bils" class="category-name padding05">
						&nbsp;<?=@$row_1->name?></label><span class="subgroup_hide_show"><i class="fa fa-plus-circle fa-minus-circle" aria-hidden="true"></i></span></div>
					    
					    <?php if(!empty($row_1->sub_category)) : ?>
						<ul class="level-2-menu">
						    <label class="subgroup-title">subgroups</label>
						    <?php foreach($row_1->sub_category as $sk => $row_2) : ?>
						    <li  class="level-2-menu-li">
							<div class="subgroup-strip">
							<input type="hidden" name="group[<?=$index?>][recipe_group_id][<?=$kk?>][sub_category][<?=$sk?>][id]" value="<?=@$row_2->id?>">
							<input type="checkbox" name="group[<?=$index?>][recipe_group_id][<?=$kk?>][sub_category][<?=$sk?>][all]" value="<?=@$row_2->id?>" class="recipe-subgroup" data-index="<?=$index?>"><label for="pos-door_delivery_bils" class="subgroup-name padding05">
						    <?=@$row_2->name?></label><span class="recipe_hide_show"><i class="fa fa-plus-circle fa-minus-circle" aria-hidden="true"></i></span><label for="pos-door_delivery_bils" class="subgroup-item-excluded-label padding05"><input type="checkbox" name="group[<?=$index?>][recipe_group_id][<?=$kk?>][sub_category][<?=$sk?>][type]" value="excluded" class="subgroup-item-excluded skip" data-index="<?=$index?>">
						    <?=@lang('excluded')?></label>
							</div>
							<?php if(!empty($row_2->recipes)) : ?>
							    <ul class="level-3-menu"><div class="items-title">items</div>
								<?php foreach($row_2->recipes as $rk => $row_3) :
								$checked = (in_array($row_3->id,$mapped_rids))?'checked="checked"':'';
								
								?>
								<li>
								    <label for="pos-door_delivery_bils" class="padding05"><input type="checkbox" name="group[<?=$index?>][recipe_group_id][<?=$kk?>][sub_category][<?=$sk?>][recipes][]" value="<?=@$row_3->id?>" class="recipe-item" data-index="<?=@$index.'-'.$row_1->id.'-'.@$row_2->id.'-'.@$row_3->id?>" <?=$checked?>>
								<?=@$row_3->name?></label>
								    
								</li>
								<?php endforeach; ?>
							    </ul>
							<?php endif; ?>
						    </li>
						    <?php endforeach; ?>
						</ul>
					    <?php endif; ?>
					    </div>
					</li>
				    <?php endforeach; ?>
				    </ul>
				</div>
			    </div>
			</div>
		
			
			<!--------- Recipe category list end -------------------->
			
			
			
			
			
			
			
			
			
			
			
			
	    
			<div class="col-sm-12" >
			    <label style="margin-top:19px;"></label>
			    <div class="form-group" style="float:right;">
				    <div
					    class="controls"> <?php echo form_submit('submit_search', $this->lang->line("submit"), 'class="btn btn-primary submit_search"'); ?> 
				    </div>
			    </div>
			</div>
                   
					
                </div>
                     <?php echo form_close(); ?> 
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="<?= $assets ?>js/html2canvas.min.js"></script>
<script>
    $(document).ready(function(){
	if($('#bill_search_type').val()=="single"){
	    $('.bill-range,.type-container').hide();
	    $('.single-bill').show();
	    
	}else if($('#bill_search_type').val()=="range"){
	    $('.bill-range').show();
	    $('.single-bill').hide();
	}
	$('#bill_search_type').change(function(){
	    $type = $(this).val();
	    $('.type-container').show();
	    if ($type=="all") {
		$('.single-bill,.bill-range').hide();
	    }else if ($type=="single") {
		$('.type-container').hide();
		$('.bill-range').hide();
		$('.single-bill').show();
	    }else if ($type=="range") {
		$('.bill-range').show();
		$('.single-bill').hide();
	    }
	});
	$('#end_date').datepicker({
	    dateFormat: "yy-mm-dd" ,
	    maxDate:  0,      
	});
	$("#start_date").datepicker({
	    dateFormat: "yy-mm-dd" ,  
	    maxDate:  0,      
	    onSelect: function(date){            
		var date1 = $('#start_date').datepicker('getDate');           
		var date = new Date( Date.parse( date1 ) ); 
		date.setDate( date.getDate());        
		var newDate = date.toDateString(); 
		newDate = new Date( Date.parse( newDate ) );                      
		$('#end_date').datepicker("option","minDate",newDate);            
	    }
	});
	$('.submit_search').click(function(e){
	    e.preventDefault();   
		
	    $start_date = $('#start_date').val();
	    $end_date = $('#end_date').val();
	    $target_amount = $('#target_amount').val();
	    $error =  false;
	    $('#target_amount').css('border-color','#ccc');
	    $('#end_date').css('border-color','#ccc');
	    $('#start_date').css('border-color','#ccc');
	    if ($start_date=='') {
		$('#start_date').css('border-color','red');
		$error =  true;
	    }
	    if ($end_date=='') {
		$('#end_date').css('border-color','red');
		$error =  true;
	    }
	    if ($target_amount=='') {
		$('#target_amount').css('border-color','red');
		$error =  true;
	    }
	    if ($('.recipe-item:checked').length==0) {
		$error =  true;
		bootbox.alert('Please Select Category/Items to proceed');
	    }
	    if (!$error) {
		bootbox.confirm("Are you sure want modify bills?", function(result){
		    if (result) {
		    
			$.ajax({
			    url:'<?=admin_url('reports/auto_modify_bills')?>',
			    type:'post',
			    dataType:'json',
			    data : $('#auto-modifybill-search').serialize()+'&submit_search=true',
			    success:function(res){
				
				
				$("#myModal").html(res.list);
				$('#myModal').modal('show');
				//if (res.data.length==0) {
				//    bootbox.alert('No Bills Found for this search');
				//}else{
				//    bootbox.alert('Bill modification is running in backend process');
				//    //location.reload();
				//}
			    }
			});
		    }
		});        
	    }else{
		 $('html, body').animate({ scrollTop: 0 }, 'fast');
	    }
	    
	});
	$(document).on('click','#modify-bills',function(){
	    bootbox.confirm("Are you sure want modify bills?", function(result){
		if (result) {
		    $.ajax({
			    url:'<?=admin_url('reports/initiate_auto_modification')?>',
			    type:'post',
			    dataType:'json',
			    data : $('#modify-bills-form').serialize(),
			    success:function(res){
				bootbox.alert('Bill modification initiated, it runs in backend.');
				location.reload();
			    }
		    });
		}
	    });
	});
    })
</script>

<style>
.recipe-group-list ul.level-1-menu li,.recipe-group-list ul.level-2-menu li {list-style: none;position: relative;}
.recipe-group-list ul.level-3-menu li {list-style: none;float: left !important;position: relative;margin-right: 20px;/*min-width: 200px !important;*/width:30%}
.recipe-group-list ul.level-1-menu>li , .recipe-group-list ul.level-2-menu>li{
  clear: both;
}
.level-2-menu{text-indent:15px;}
.level-3-menu{text-indent:25px;}
.level-1-menu-li{padding: 5px;}
.level-1-menu-div{background-color: #f8f6f6;border-radius: 10px;overflow: hidden;/*padding: 10px 10px 10px 10px;*//*padding: 5px;*/position: relative;box-shadow: inset 0 3px 3px -3px rgba(0, 0, 0, 0.3);background: linear-gradient(181deg, #ffffff 0%, #ececec 100%);}
.weekdays-selector{width:198px;float: right;text-indent: 1px;}
.weekdays-selector input {display: none!important;margin-right: 3px;}
.weekdays-selector label {display: inline-block;border-radius: 6px;background: #dddddd;height: 21px;width: 17px;margin-right: 3px;line-height: 23px;text-align: center;cursor: pointer;}
.weekdays-selector input[type=checkbox]:checked + label {background: #2AD705;color: #ffffff;}
.subgroup_hide_show{float: right;position: relative;right: 0.5%;top: 3px;font-size: 20px;}
.level-1-menu-div .category-name-container{background: grey;padding: 4px;cursor: pointer;}
.subgroup-item-excluded-label{display: none;}
.disabled-day + label{background: #d31919 !important;color: #ffffff !important;}
.subgroup-title{text-indent: 5px;font-weight: bold;text-transform: uppercase;}
.items-title{padding-left: 2%;font-weight: bold;text-transform: uppercase;}
.subgroup-strip{padding: 4px;margin: 9px;background: linear-gradient(181deg, #ffffff 0%, #a0a0a0fc 100%);}
.recipe_hide_show{float: right;font-size: 20px;}
.weekday-disabled + label{background: #817e7a !important;    color: #ffffff !important;}
</style>
   