<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('edit_wallets'); ?></h4>
        </div>
        <?php echo admin_form_open("system_settings/edit_wallets/" . $tills->id); ?>
		<input type="hidden" name="id" value="<?php echo $wallets->id ?>">
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>

            <div class="form-group">
                <label for="name"><?php echo $this->lang->line("name"); ?></label>
                <div class="controls"> <?php echo form_input('name',$wallets->name, 'class="form-control" id="name" required="required"'); ?> 
                </div>
            </div>
            <div class="form-group">
                <label for="display_name"><?php echo $this->lang->line("Display_name"); ?></label>
                <div  class="controls"> <?php echo form_input('display_name',$wallets->displayname, 'class="form-control" id="display_name" required="required"'); ?> 
                </div>
            </div>
            <div class="form-group">
				<label><input type="checkbox" value="1" <?php echo ($wallets->active ==1)?"checked":"" ;  ?>  name="status">  Active</label>
            </div>

        </div>
        <div class="modal-footer">
            <?php echo form_submit('edit_wallets', lang('edit_wallets'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<?= $modal_js ?>