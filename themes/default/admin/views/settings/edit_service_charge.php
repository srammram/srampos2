<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('edit_tax_rate'); ?></h4>
        </div>
        <?php echo admin_form_open("system_settings/edit_service_charge/" . $id); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>

            <div class="form-group">
                <label class="control-label" for="name"><?php echo $this->lang->line("name"); ?></label>

                <div class="controls"> <?php echo form_input('name', $service_charge->name, 'class="form-control" id="name" required="required"'); ?> </div>
            </div>
            <div class="form-group">
                <label class="control-label" for="display_value"><?php echo $this->lang->line("display_value"); ?></label>

                <div class="controls"> <?php echo form_input('display_value', $service_charge->display_value, 'class="form-control" id="display_value"'); ?> </div>
            </div>
            <div class="form-group">
                <label class="control-label" for="service_charge_rate"><?php echo $this->lang->line("service_charge_rate"); ?></label>

                <div class="controls"> <?php echo form_input('rate', $service_charge->rate, 'class="form-control numberonly" id="rate" maxlength="8" required="required"'); ?> </div>
            </div>           
        </div>
        <div class="modal-footer">
            <?php echo form_submit('edit_service_charge', lang('edit_service_charge'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<script>
$(".numberonly").keypress(function (event){
	
	if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
		event.preventDefault();
	}
  
	});
</script>
<?= $modal_js ?>