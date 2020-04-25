<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('edit_tills'); ?></h4>
        </div>
        <?php echo admin_form_open("system_settings/edit_tills/" . $tills->id); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>

            <div class="form-group">                
                <label class="control-label" for="disable_editing"><?= lang("warehouse"); ?></label>
                <select  name="warehouse_id" class="form-control pos-input-tip warehouse_id" id="warehouse_id">
                    <?php
                    foreach($warehouses as $w){
						if($tills->warehouse_id == $w->id){
							$selected = 'selected';
						}else{
							$selected = '';
						}
                    ?>
                    <option <?= $selected ?> value="<?= $w->id ?>"><?= $w->name; ?></option>
                    <?php
                    }
                    ?>
                </select>
                
            </div>

            <div class="form-group">
                <label class="control-label" for="system_name"><?php echo $this->lang->line("system_name"); ?></label>
                <div class="controls"> <?php echo form_input('system_name', $tills->system_name, 'class="form-control" id="system_name" required="required"'); ?> 
                </div>
            </div>
            <div class="form-group">
                <label class="control-label" for="system_ip"><?php echo $this->lang->line("system_ip"); ?></label>
                <div class="controls"> <?php echo form_input('system_ip', $tills->system_ip, 'class="form-control" id="system_ip" required="required"'); ?> 
                </div>
            </div>
            <div class="form-group">
                <label class="control-label" for="till_name"><?php echo $this->lang->line("till_name"); ?></label>
                <div class="controls"> <?php echo form_input('till_name', $tills->till_name, 'class="form-control" id="till_name" required="required"'); ?> 
                </div>
            </div>

        </div>
        <div class="modal-footer">
            <?php echo form_submit('edit_tills', lang('edit_tills'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<?= $modal_js ?>