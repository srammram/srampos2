<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
  <link rel="stylesheet" href="<?= $assets ?>styles/jquery-ui.css">
  <script src="<?= $assets ?>js/jquery-ui.js"></script>

<div class="box">
  <div class="box-header">
    <h2 class="blue"><i class="fa-fw fa fa-edit"></i>
      <?= lang('Edit_buy'); ?>
    </h2>
  </div>
  <div class="box-content">
    <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form');
        echo admin_form_open("system_settings/edit_buy/".$id, $attrib); ?>
    <div class="row">
      <div class="col-lg-12">
        <p class="introtext"><?php echo lang('enter_info'); ?></p>
        <div class="col-md-7">
          <div class="form-group">
            <label for="name"><?php echo $this->lang->line("name"); ?></label>
            <div class="controls"> <?php echo form_input('name', $buy->name, 'class="form-control" id="name" required="required"'); ?> </div>
          </div>
         
          <div class="form-group">
            <label for="method"><?php echo $this->lang->line("Buy Method"); ?></label>
            <div class="controls">
           
              <select name="buy_method" id="type" class="form-control select" >
                <option value="buy_x_get_x" <?php if($buy->buy_method == 'buy_x_get_x'){ echo 'selected'; }else{ echo ''; } ?>>Buy X Get X</option>
                <option value="buy_x_get_y" <?php if($buy->buy_method == 'buy_x_get_y'){ echo 'selected'; }else{ echo ''; } ?>>Buy X Get Y</option>
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
                    <input type="text" name="start_date" class="form-control " placeholder="From Date " value="<?php echo date('Y-m-d', strtotime($buy->start_date)); ?>" id="start_date" required="required">
                  </div>
                </div>
                
                <div class="form-group  col-lg-6 date_div">
                  <div class="controls">
				    <label>To Date</label>
                    <input type="text" name="end_date" class="form-control " placeholder="To Date " id="end_date" value="<?php echo date('Y-m-d', strtotime($buy->end_date)); ?>" required="required">
                  </div>
                </div>
                
                <div class="form-group  col-lg-6 time_div">
                  <div class="controls ">
				  <label>From Time</label>
                    <input type="text" name="start_time" class="form-control time" placeholder="From  Time" id="start_time" value="<?php echo date('H:i', strtotime($buy->start_time)); ?>" required="required">
                  </div>
                </div>
                <div class="form-group  col-lg-6 time_div">
                  <div class="controls">
				   <label>To Time</label>
                    <input type="text" name="end_time" class="form-control time" placeholder="To  Time" id="end_time" value="<?php echo date('H:i', strtotime($buy->end_time)); ?>" required="required">
                  </div>
                </div>
            </div>
			<div class="form-group ">
               
              <div class="controls">
                <?php $weekdays = explode(',',$buy->week_days); ?>
                <?= lang('Apply_on', 'Apply_on'); ?> : 
                <label><input type="checkbox" name="weekdays[]" class="form-control r_weekdays" required="required" data-code="mon" value="Monday" autocomplete="off" <?php if(in_array('Monday',$weekdays)) { echo 'checked="checked"';} ?>>&nbsp;&nbsp;Monday</label>
                <label><input type="checkbox" name="weekdays[]" class="form-control r_weekdays" required="required" data-code="tue" value="Tuesday" autocomplete="off" <?php if(in_array('Tuesday',$weekdays)) { echo 'checked="checked"';} ?>>&nbsp;&nbsp;Tuesday</label>
                <label><input type="checkbox" name="weekdays[]" class="form-control r_weekdays" required="required" data-code="wed" value="Wednesday" autocomplete="off" <?php if(in_array('Wednesday',$weekdays)) { echo 'checked="checked"';} ?>>&nbsp;&nbsp;Wednesday</label>
                <label><input type="checkbox" name="weekdays[]" class="form-control r_weekdays" required="required" data-code="thu" value="Thursday" autocomplete="off" <?php if(in_array('Thursday',$weekdays)) { echo 'checked="checked"';} ?>>&nbsp;&nbsp;Thursday</label>
                <label><input type="checkbox" name="weekdays[]" class="form-control r_weekdays" required="required" data-code="fri" value="Friday" autocomplete="off" <?php if(in_array('Friday',$weekdays)) { echo 'checked="checked"';} ?>>&nbsp;&nbsp;Friday</label>
                <label><input type="checkbox" name="weekdays[]" class="form-control r_weekdays" required="required" data-code="sat" value="Saturday" autocomplete="off" <?php if(in_array('Saturday',$weekdays)) { echo 'checked="checked"';} ?>>&nbsp;&nbsp;Saturday</label>
                <label><input type="checkbox" name="weekdays[]" class="form-control r_weekdays" required="required" data-code="sun" value="Sunday" autocomplete="off" <?php if(in_array('Sunday',$weekdays)) { echo 'checked="checked"';} ?>>&nbsp;&nbsp;Sunday</label>
              </div>
            </div>
        <fieldset class="buy_buy_x_get_x">
            <legend>Item:
            </legend>
            <div id="itemx">
            	<?php
				$i=0;
				
				?>
              <div class="well col-lg-12">
                <div class="form-group col-lg-12">
                  <div class="controls">
                    <select name="buy_type"  class="form-control select buy_type" id="buy_type_<?php echo $i; ?>" >
                      <option value="Sale Items" <?php if($item->buy_type == 'Sale Items') ?>>Sale Items</option>
                    </select>
                  </div>
                </div>
              	<div class="form-group col-lg-6 recipe">
                  <div class="controls">
                   <select name="buy_item" class="form-control buy_item selectx"  id="buy_item_<?php echo $i; ?>"  placeholder="<?= lang("select") . ' ' . lang("Sale Item Buy") ?>" >
                   <option value=""></option>
                   		<?php
						foreach($recipe as $recipe_row){
						?>
                      <option value="<?php echo $recipe_row->id; ?>" <?php if($item->buy_item == $recipe_row->id){ echo 'selected'; }else{ echo ''; } ?>><?php echo $recipe_row->name; ?></option>
                      <?php
						}
					  ?>
                    </select>
                    
                   </div>
				   </div>
				 
                
                
                
                <div class="form-group col-lg-6 recipe_get">
                  <div class="controls">
                   <select name="get_item" id="get_item_<?php echo $i; ?>" class="form-control get_item selecty"   placeholder="<?= lang("select") . ' ' . lang("Sale Item Get") ?>" >
                   <option value=""></option>
                   		<?php
						foreach($recipe as $recipe_row){
						?>
                      <option value="<?php echo $recipe_row->id; ?>" <?php if($item->get_item == $recipe_row->id){ echo 'selected'; }else{ echo ''; } ?>><?php echo $recipe_row->name; ?></option>
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
				  foreach ($buy_variants as $b_variant) {
                                    $res[$b_variant->id] = $b_variant->name;
                                } 
                   echo form_dropdown('buy_variant_id', $res, $b_variant->buy_variant_id, 'class="form-control" data-placeholder="' . lang("select") . ' ' . lang("Variants") . '" id="buy_variant_id" style="width:100%;" ');
				   ?>
                   </div>
                </div>
				<div class="form-group col-lg-6 ">
					<label>Get Variant</label>
                  <div class="controls">
				  <?php  
				  foreach ($get_variants as $g_variant) {
                                    $res[$g_variant->id] = $g_variant->name;
                                } 
                   echo form_dropdown('get_variant_id', $res,$g_variant->get_variant_id, 'class="form-control" data-placeholder="' . lang("select") . ' ' . lang("Variants") . '" id="get_variant_id" style="width:100%;" ');
				   ?>
                   </div>
                </div>
				
				 
                        <div class="form-group col-lg-6 quantity">
                <label for="value"><?php echo $this->lang->line("Buy Quantity"); ?></label>
        
                <div class="controls">  <?php echo form_input('buy_quantity',  $buy->buy_quantity, 'class="form-control numberonly" maxlength="2" id="buy_quantity" required="required"'); ?></div>
            </div>
                       <div class="form-group col-lg-6 quantity">
                <label for="value"><?php echo $this->lang->line("Get Quantity"); ?></label>
        
                <div
                    class="controls"> <?php echo form_input('get_quantity',  $buy->get_quantity, 'class="form-control numberonly" maxlength="2" id="get_quantity" required="required"'); ?> </div>
            </div>                  
              </div>
             
            </div>
          </fieldset>
        
          
          
        </div>
      </div>
    </div>
    <div class="box-footer"> <?php echo form_submit('edit_buy', lang('edit_buy'), 'class="btn btn-primary"'); ?> </div>
  </div>
  
  <?php echo form_close(); ?> </div>
<script>
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
$(".numberonly").keypress(function (event){
	
		if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
			event.preventDefault();
		}
	  
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