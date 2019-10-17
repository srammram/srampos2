<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('add_sales_type'); ?></h4>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form');
        echo admin_form_open("system_settings/add_sales_type", $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>
            
            <div class="form-group">
                <label class="control-label" for="disable_editing"><?= lang("type"); ?></label>
                    <select style="display: "  name="type" class="form-control pos-input-tip type" id="type">
                        <option value="alacarte"><?= lang('alacarte'); ?></option>
                        <option value="bbq"><?= lang('bbq'); ?></option>
                    </select>
            </div>

            <div class="form-group">
                <label for="name"><?php echo $this->lang->line("name"); ?></label>
                <div
                    class="controls"> <?php echo form_input('name', '', 'class="form-control" id="name" required="required"'); ?> 
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <?php echo form_submit('add_sales_type', lang('add_sales_type'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>

<?= $modal_js ?>