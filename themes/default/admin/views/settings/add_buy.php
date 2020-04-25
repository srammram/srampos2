<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
  <link rel="stylesheet" href="<?= $assets ?>styles/jquery-ui.css">
  <script src="<?= $assets ?>js/jquery-ui.js"></script>
<div class="box">
  <div class="box-header">
    <h2 class="blue"><i class="fa-fw fa fa-plus"></i>
      <?= lang('add_buy'); ?>
    </h2>
  </div>
  <div class="box-content">
    <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form');
        echo admin_form_open("system_settings/add_buy", $attrib); ?>
    <div class="row">
      <div class="col-lg-12">
        <p class="introtext"><?php echo lang('enter_info'); ?></p>
        <div class="col-md-7">
          <div class="form-group">
            <label for="name"><?php echo $this->lang->line("name"); ?></label>
            <div
							class="controls"> <?php echo form_input('name', '', 'class="form-control" id="name" required="required"'); ?> </div>
          </div>
         
          <div class="form-group">
            <label for="method"><?php echo $this->lang->line("Buy Method"); ?></label>
            <div class="controls">
              <select name="buy_method" id="type" class="form-control select" >
                <option value="buy_x_get_x">Buy X Get X</option>
                <option value="buy_x_get_y">Buy X Get Y</option>
              </select>
            </div>
          </div>
          
          <div class="form-group">
            <label for="method"><?php echo $this->lang->line("Date & Time"); ?></label>
            
           </div>
           <div class="row">
          <div class="form-group  col-lg-6 date_div">
           
           
                  <div class="controls ">
				  <label>From Date</label>
                  
					<input type="text" name="start_date" onkeydown="return false" id="start_date"class="form-control " placeholder="From Date "  required="required">
                  </div>
                </div>
                
                <div class="form-group  col-lg-6 date_div">
                  <div class="controls">
				   <label>To Date</label>
				 
				  
                    <input type="text" name="end_date" onkeydown="return false" id="end_date"class="form-control " placeholder="To Date "  required="required">
                  </div>
                </div>
                
                <div class="form-group  col-lg-6 time_div">
                  <div class="controls ">
				    <label>From Time</label>
				    
                    <input type="text" name="start_time" onkeydown="return false" class="form-control time" placeholder="From  Time" id="start_time" required="required">
                  </div>
                </div>
                <div class="form-group  col-lg-6 time_div">
                  <div class="controls">
				   <label>To Time</label>
                    <input type="text" name="end_time"  onkeydown="return false" class="form-control time" placeholder="To  Time" id="end_time" required="required">
                  </div>
                </div>
            </div>
                          
         <div class="form-group">
               
              <div class="controls">
                <?= lang('Apply_on', 'Apply_on'); ?> : 
                <label><input type="checkbox" name="weekdays[]" class="form-control r_weekdays" required="required" data-code="mon" value="Monday" autocomplete="off" checked="checked">&nbsp;&nbsp;Monday</label>
                <label><input type="checkbox" name="weekdays[]" class="form-control r_weekdays" required="required" data-code="tue" value="Tuesday" autocomplete="off" checked="checked">&nbsp;&nbsp;Tuesday</label>
                <label><input type="checkbox" name="weekdays[]" class="form-control r_weekdays" required="required" data-code="wed" value="Wednesday" autocomplete="off" checked="checked">&nbsp;&nbsp;Wednesday</label>
                <label><input type="checkbox" name="weekdays[]" class="form-control r_weekdays" required="required" data-code="thu" value="Thursday" autocomplete="off" checked="checked">&nbsp;&nbsp;Thursday</label>
                <label><input type="checkbox" name="weekdays[]" class="form-control r_weekdays" required="required" data-code="fri" value="Friday" autocomplete="off" checked="checked">&nbsp;&nbsp;Friday</label>
                <label><input type="checkbox" name="weekdays[]" class="form-control r_weekdays" required="required" data-code="sat" value="Saturday" autocomplete="off" checked="checked">&nbsp;&nbsp;Saturday</label>
                <label><input type="checkbox" name="weekdays[]" class="form-control r_weekdays" required="required" data-code="sun" value="Sunday" autocomplete="off" checked="checked">&nbsp;&nbsp;Sunday</label>
              </div>
            </div>
        
        <fieldset class="buy_buy_x_get_x">
            <legend>Item:
           
            </legend>
            <div id="itemx">
              <div class="well col-lg-12">
                <div class="form-group col-lg-12">
                  <div class="controls">
                    <select name="buy_type"  class="form-control select buy_type" id="buy_type_0" >
                      <option value="Sale Items">Sale Items</option>
                    </select>
                  </div>
                </div>
               
              
              	<div class="form-group col-lg-6 recipe">
                  <div class="controls">
                   <select name="buy_item" class="form-control buy_item select selectx"  id="buy_item_0"  placeholder="<?= lang("select") . ' ' . lang("Sale Item Buy") ?>" >
                   <option value=""></option>
                   		<?php
						foreach($recipe as $recipe_row){
						?>
                      <option value="<?php echo $recipe_row->id; ?>"><?php echo $recipe_row->name; ?></option>
                      <?php
						}
					  ?>
                    </select>
                    
                   </div>
                </div>
                <div class="form-group col-lg-6 recipe_get">
                  <div class="controls">
                   <select name="get_item" id="get_item_0" class="form-control get_item select selecty"   placeholder="<?= lang("select") . ' ' . lang("Sale Item Get") ?>" >
                   <option value=""></option>
                   		<?php
						foreach($recipe as $recipe_row){
						?>
                      <option value="<?php echo $recipe_row->id; ?>"><?php echo $recipe_row->name; ?></option>
                      <?php
						}
					  ?>
                    </select>
                   </div>
                </div>
				<div class="form-group col-lg-6 ">
				<label>Buy Variant</label>
                  <div class="controls">
				  <?php  
                   echo form_dropdown('buy_variant_id', $res, '', 'class="form-control" data-placeholder="' . lang("select") . ' ' . lang("Variants") . '" id="buy_variant_id" style="width:100%;" ');
				   ?>
                   </div>
                </div>
				<div class="form-group col-lg-6 ">
					<label>Get Variant</label>
                  <div class="controls">
				  <?php  
                   echo form_dropdown('get_variant_id', $res, '', 'class="form-control" data-placeholder="' . lang("select") . ' ' . lang("Variants") . '" id="get_variant_id" style="width:100%;" ');
				   ?>
                   </div>
                </div>
				    <div class="form-group col-lg-6 quantity">
                <label for="value"><?php echo $this->lang->line("Buy Quantity"); ?></label>
        
                <div class="controls"> <?php echo form_input('buy_quantity', '', 'class="form-control numberonly" maxlength="2" id="buy_quantity" required="required"'); ?> </div>
            </div>
                       <div class="form-group col-lg-6 quantity">
                <label for="value"><?php echo $this->lang->line("Get Quantity"); ?></label>
        
                <div
                    class="controls"> <?php echo form_input('get_quantity', '', 'class="form-control numberonly" maxlength="2" id="get_quantity" required="required"'); ?> </div>
            </div>         
              </div>
			  
            </div>
          </fieldset>
        
          
          
        </div>
      </div>
    </div>
    <div class="box-footer"> <?php echo form_submit('add_buy', lang('add_buy'), 'class="btn btn-primary"'); ?> </div>
  </div>
  
  <?php echo form_close(); ?> </div>
<script>/* 

$(document).ready(function(e) {
    

var c=1;
$('#addItemx').click(function () {
	
	
		var html = '<div class="well col-lg-12"> <div class="form-group col-lg-12"> <div class="controls"> <select name="buy_type[]" id="buy_type_'+c+'" class="form-control select buy_type" > <option value="Sale Items">Sale Items</option> <option value="Sale Groups">Sale Groups</option> </select> </div> </div>  <div class="form-group col-lg-6 recipe"> <div class="controls"> <select name="buy_item[]" id="buy_item_'+c+'" class="form-control buy_item select"  placeholder="<?= lang("select") . ' ' . lang("Sale Item Buy") ?>" > <option value=""></option> <?php foreach($recipe as $recipe_row){ ?> <option value="<?php echo $recipe_row->id; ?>"><?php echo $recipe_row->name; ?></option> <?php } ?> </select> </div> </div> <div class="form-group col-lg-6 recipe_get"> <div class="controls"> <select name="get_item[]" id="get_item_'+c+'" class="form-control get_item select"  placeholder="<?= lang("select") . ' ' . lang("Sale Item Get") ?>" ><option value=""></option> <?php foreach($recipe as $recipe_row){ ?> <option value="<?php echo $recipe_row->id; ?>"><?php echo $recipe_row->name; ?></option> <?php } ?> </select> </div> </div> <button type="button" class="btn btn-primary pull-right btn-xs deleteItemx"><i class="fa fa-trash-o"></i></button></div>';
		
		
		$('#itemx').append(html);
		$('#buy_type_'+c).select2();
		$('#in_list_'+c).select2();
		$('#get_item_'+c).select2();
		$('#buy_item_'+c).select2();
		
		c++;
		
});

$("body").on('click','.deleteItemx', function(){
	$(this).closest('.well').remove();
});

$(".numberonly").keypress(function (e){
	
		if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
			event.preventDefault();
		}
	  
		});
	}); */
	$('#end_date').datepicker({
        dateFormat: "yy-mm-dd" ,
        minDate:  0,      
    });
	$('#start_date').datepicker({
        dateFormat: "yy-mm-dd" ,
        minDate:  0,      
    });
	 $(function () {
    $('#start_time').datetimepicker({
         pickDate: false,
        minuteStep: 01,
        pickerPosition: 'bottom-right',
        format: 'hh:ii p',
        autoclose: true,
        showMeridian: true,
        startView: 1,
        maxView: 1,        
    });
});

    $(function () {
    $('#end_time').datetimepicker({
         pickDate: false,
        minuteStep: 01,
        pickerPosition: 'bottom-right',
        format: 'hh:ii p',
        autoclose: true,
        showMeridian: true,
        startView: 1,
        maxView: 1,           
    });
});
	
$('.selectx').on('change', function (e) {
	var optionSelected = $("option:selected", this);
    var valueSelected = this.value;
 /*  if($('#type').val() =='buy_x_get_x'){
	$(".selecty option[value='"+valueSelected+"']").prop('selected', true);
		} */
	var $options = $();
	 $.ajax({
            type: "post", async: false,
             url: "<?=admin_url('system_settings/recipe_variant')?>",
              data: {recipe_id: valueSelected},
            dataType: "json",
            success: function (scdata) {
              if(scdata != null){
                 $options = $options.add($('<option>').attr('value', "").html('Variants'));
                 $.each(scdata, function(key, value) {
                    $options = $options.add($('<option>').attr('value', value.id).html(value.name));
                        });
                 $('#buy_variant_id').html($options).trigger('change');
              } else {                
                $("#buy_variant_id").val(null).trigger('change');                
              }
            }
        });
});
	$('.selecty').on('change', function (e) {
    var optionSelected = $("option:selected", this);
    var valueSelected = this.value;
	var $options = $();
	 $.ajax({
            type: "post", async: false,
             url: "<?=admin_url('system_settings/recipe_variant')?>",
              data: {recipe_id: valueSelected},
            dataType: "json",
            success: function (scdata) {
              if(scdata != null){
                 $options = $options.add($('<option>').attr('value', "").html('Variants'));
                 $.each(scdata, function(key, value) {
                    $options = $options.add($('<option>').attr('value', value.id).html(value.name));
                        });
                 $('#get_variant_id').html($options).trigger('change');
              } else {                
                $("#get_variant_id").val(null).trigger('change');                
              }
            }
        });
	
	
    });
</script>