<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('Add_Area'); ?></h4>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form');
        echo admin_form_open_multipart("tables/add_area", $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>


            
			<div class="form-group">
                <input type="radio" value="suki" id="suki" class="checkbox" name="type" checked>
                <label for="suki" class="padding03"><?= lang('dine_in') ?></label>
                <input type="radio" value="bbq" id="bbq" class="checkbox" name="type" >
                <label for="bbq" class="padding03"><?= lang('BBQ') ?></label>
              
            </div>
			
			<div class="form-group">
                <?= lang("warehouse", "warehouse"); ?>
                <?php
                $wh[''] = '';
                foreach ($warehouses as $warehouse) {
                    $wh[$warehouse->id] = $warehouse->name;
                }
                echo form_dropdown('warehouse_id', $wh, '', 'class="form-control" data-placeholder="' . lang("select") . ' ' . lang("warehouse") . '" required="required" style="width:100%;" ');
                ?>
                
            </div>
            <div class="form-group">
                <?= lang('name', 'name'); ?>
                <?= form_input('name', '', 'class="form-control" id="name" required="required"'); ?>
            </div>
            <div class="form-group all">
				<?= lang("description", "description") ?>
                <?= form_textarea('description', '', 'class="form-control" id="details"'); ?>
            </div>
            <div class="form-group">
                <?= lang("printer", "printer_id"); ?>
                <?php
                $wh[''] = '';
                foreach ($printers as $printer) {
                $wh[$printer->id] = $printer->title;
                }

                echo form_dropdown('printer_id', $wh, (isset($_POST['printer_id']) ? $_POST['printer_id'] : ''), 'class="form-control select"  placeholder="' . lang("select") . " " . lang("Printer") . '" style="width:100%" ')
                ?>
                
            </div>
            

        </div>
        <div class="modal-footer">
            <?php echo form_submit('add_area', lang('add_area'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>
<?= $modal_js ?>
