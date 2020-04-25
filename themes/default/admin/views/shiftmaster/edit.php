<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!-- <script type="text/javascript" src="<?=$assets ?>js/customer_discount.js"></script> -->
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('edit_shiftmaster'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext">

                <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form','id'=>'edit-shiftmaster');
                     echo admin_form_open("shiftmaster/edit/".$shiftmaster->id, $attrib); ?>
                      
					<fieldset class="scheduler-border">
                            <legend class="scheduler-border"><?= lang('edit') ?></legend>

							<div class="form-group col-lg-6">
                                <?= lang('code', 'code'); ?>
                                <div class="input-group col-md-12">
                                <?= form_input('code', $shiftmaster->code, 'class="form-control numberonly" id="code" readonly required="required" maxlength="9" '); ?>
                                 
                                </div>
                            </div>
                            <div class="form-group col-lg-6">
                                <?= lang('name', 'name'); ?>
                                <?= form_input('name', $shiftmaster->name, 'class="form-control tip" id="name" required="required"'); ?>
                            </div>
							<div style="clear: both;height: 10px;"></div>
                            <?php
							$hours = ["00","01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17","18","19","20","21","22","23"];
							$minutes = ["00","01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17","18","19","20","21","22","23","24","25","26","27","28","29","30","31","32","33","34","35","36","37","38","39","40","41","42","43","44","45","46","47","48","49","50","51","52","53","54","55","56","57","58","59"];
							$from_time = explode(':', $shiftmaster->from_time);
							$to_time = explode(':', $shiftmaster->to_time);
							?>
                            <div class="form-group col-lg-6">
                                <?= lang('from_time', 'from_time'); ?>
                                    <div class="controls row">
                                      <div class="col-sm-6">
                                    <select name="from_hours" id="from_hours" class="form-control">
                                        <option value="">Select Hours</option>
                                        <?php
                                        foreach($hours as $h){
											if($h == $from_time[0]){
												$selected = 'selected';
											}else{
												$selected = '';
											}
                                            echo '<option '.$selected.' value="'.$h.'">'.$h.'</option>';
                                        }
                                        ?>
                                  </select>
                                  </div>
                                  <div class="col-sm-6">
                                	<select name="from_minutes" id="from_minutes" class="form-control">
                                        <option value="">Select Minutes</option>
                                        <?php
                                        foreach($minutes as $m){
											if($m == $from_time[1]){
												$selected = 'selected';
											}else{
												$selected = '';
											}
                                            echo '<option '.$selected.' value="'.$m.'">'.$m.'</option>';
                                        }
                                        ?>
                                  </select>
                                  </div>
                                    </div>
                            </div>
                            <div class="form-group col-lg-6">
                                <?= lang('to_time', 'to_time'); ?>
                              <div class="controls row">
                              		
                                    <div class="col-sm-6">
                                    <select name="to_hours" id="to_hours" class="form-control">
                                        <option value="">Select Hours</option>
                                        <?php
                                        foreach($hours as $h){
											if($h == $to_time[0]){
												$selected = 'selected';
											}else{
												$selected = '';
											}
                                            echo '<option '.$selected.' value="'.$h.'">'.$h.'</option>';
                                        }
                                        ?>
                                  </select>
                                  </div>
                                  <div class="col-sm-6">
                                	<select name="to_minutes" id="to_minutes" class="form-control">
                                        <option value="">Select Minutes</option>
                                        <?php
                                        foreach($minutes as $m){
											if($m == $to_time[1]){
												$selected = 'selected';
											}else{
												$selected = '';
											}
                                            echo '<option '.$selected.' value="'.$m.'">'.$m.'</option>';
                                        }
                                        ?>
                                  </select>
                                  </div>
                              </div>
                            </div>

                          
                    </fieldset>
            <div style="clear: both;height: 10px;"></div>
                    <div class="form-group col-lg-12">
                          <?php echo form_submit('edit_shiftmaster', $this->lang->line("submit"), 'class="btn btn-primary"'); ?>
                    </div>
                <?= form_close(); ?>
            </div>
        </div>
    </div>
</div>
<link rel="stylesheet" href="<?= $assets ?>styles/jquery-ui.css">
<script src="<?= $assets ?>js/jquery-ui.js"></script>
<script>
$('#random_num').click(function(event){
	event.preventDefault();
	$(this).parent('.input-group').children('input').val(generateCardNo(8));
	 $('form[data-toggle="validator"]').bootstrapValidator('revalidateField', $('#code'));
});
$(".numberonly").keypress(function (event){    
    if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
        event.preventDefault();
    }  
});
</script>



