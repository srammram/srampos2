<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('add_table'); ?></h4>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form');
        echo admin_form_open_multipart("tables/add", $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>

            <div class="form-group">                
                <input type="radio" value="alacarte" id="alacarte" class="checkbox" name="sale_type" checked>
                <label for="alacarte" class="padding03"><?= lang('dine_in') ?></label>
                <input type="radio" value="bbq" id="bbq" class="checkbox" name="sale_type">
                <label for="bbq" class="padding03"><?= lang('BBQ') ?></label>
            </div>
            
            <div class="form-group">
                <?= lang('name', 'name'); ?>
                <?= form_input('name', '', 'class="form-control" id="name" required="required"'); ?>
            </div>
            <div class="form-group">
                <?= lang('native_name', 'native_name'); ?>
                <?= form_input('native_name', '', 'class="form-control" id="native_name"'); ?>
            </div>

            <div class="form-group all">
				<?= lang("description", "description") ?>
                <?= form_textarea('description', '', 'class="form-control" id="details"'); ?>
            </div>
			<div class="form-group">
                <?= lang('max_seats', 'max_seats'); ?>
                <?= form_input('max_seats', '', 'class="form-control numberonly" id="max_seats" maxlength="2" required="required"'); ?>
            </div>
            <div class="form-group">
                <?= lang("table_area", "Table_area"); ?>
                <?php
                $ar[''] = '';
                foreach ($areas as $area) {
                    $ar[$area->id] = $area->name;
                }
                echo form_dropdown('area_id', $ar, '', 'class="form-control" data-placeholder="' . lang("select") . ' ' . lang("area") . '" required="required" style="width:100%;" ');
                ?>
                
            </div>
	    <div class="form-group">
                <?= lang("map_steward", "map_steward"); ?>*
                <?php
                $ar[''] = '';
	        	$ur[''] = lang('select_steward');
                foreach ($users as $user) {
                    $ur[$user->id] = $user->first_name;
                }
                echo form_dropdown('steward_id', $ur,'', 'class="form-control" data-placeholder="' . lang("select") . ' ' . lang("steward") . '" required="required" style="width:100%;" ');
                ?>
                
            </div>
	    <div class="form-group">
		<label><input name="whitelisted" type="hidden" value="0"><input name="whitelisted" type="checkbox" value="1">Whitelisted</label>
	    </div>
          

            

        </div>
        <div class="modal-footer">
            <?php echo form_submit('add', lang('add'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>

<?= $modal_js ?>
