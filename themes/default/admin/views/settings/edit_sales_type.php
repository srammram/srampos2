<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('edit_sales_type'); ?></h4>
        </div>
        <?php echo admin_form_open("system_settings/edit_sales_type/" . $sales_type->id); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>

            <div class="form-group">
                <label class="control-label" for="disable_editing"><?= lang("type"); ?></label>
                    <select style="display: "  name="type" class="form-control pos-input-tip type" id="type">
                        <option value="alacarte" <?php if($sales_type->type == 'alacarte') { echo "selected"; }else{ echo ''; } ?> ><?= lang('alacarte'); ?></option>
                        <option value="bbq" <?php if($sales_type->type == 'bbq') { echo "selected"; }else{ echo ''; } ?> ><?= lang('bbq'); ?></option>
                    </select>
            </div>

            <div class="form-group">
                <label class="control-label" for="name"><?php echo $this->lang->line("name"); ?></label>
                <div class="controls"> <?php echo form_input('name', $sales_type->name, 'class="form-control" id="name" required="required"'); ?> 
                </div>
            </div>

        </div>
        <div class="modal-footer">
            <?php echo form_submit('edit_sales_type', lang('edit_sales_type'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<?= $modal_js ?>