<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?= lang('add_shift_time'); ?></h4>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form');
        echo admin_form_open("system_settings/add_shift_time", $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>
<!-- 
            <div class="form-group">
                <?= lang('currency_code', 'code'); ?>
                <?= form_input('code', set_value('code'), 'class="form-control tip" id="code" required="required"'); ?>
            </div> -->

            <div class="form-group">
                <?= lang('name', 'name'); ?>
                <?= form_input('name', set_value('name'), 'class="form-control tip" id="name" required="required"'); ?>
            </div>

            <div class="form-group">
                <?= lang('start_time', 'start_time'); ?>                
                <?= form_input('start_time', set_value('start_time'), 'class="form-control tip" id="start_time" required="required"'); ?>
            </div>

            <div class="form-group">
                <?= lang('end_time', 'end_time'); ?>
                <?= form_input('end_time', set_value('end_time'), 'class="form-control tip" id="end_time" maxlength="15"  required="required"'); ?>
            </div>

            <!-- <div class="form-group">
                <input type="checkbox" value="1" name="auto_update" id="auto_update">
                <label class="padding-left-10" for="auto_update"><?= lang("auto_update_rate"); ?></label>
            </div> -->
        </div>
        <div class="modal-footer">
            <?= form_submit('add_shift_time', lang('add_shift_time'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?= form_close(); ?>
</div>
<script>
    $(document).ready(function(){
    $('form').attr('autocomplete', 'off');
    $('input').attr('autocomplete', 'off');
});


    $(function () {
    $('#start_time').datetimepicker({
         pickDate: false,
        minuteStep: 05,
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
        minuteStep: 05,
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
</script>
<?= $modal_js ?>
