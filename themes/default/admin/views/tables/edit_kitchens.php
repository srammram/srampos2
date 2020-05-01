<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('edit_kitchen'); ?></h4>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form');
        echo admin_form_open_multipart("tables/edit_kitchen/" . $kitchen->id, $attrib); ?>
        <div class="modal-body">
            <p><?= lang('update_info'); ?></p>
            
            <div class="form-group">
                <?= lang('name', 'name'); ?>
                <?= form_input('name', $kitchen->name, 'class="form-control" id="name" autocomplete="off" required="required"'); ?>
            </div>
            <div class="form-group all">
				<?= lang("description", "description") ?>
                <?= form_textarea('description', $kitchen->description, 'class="form-control" id="details"'); ?>
            </div>
			
            <div class="form-group ">
                <?= lang("warehouse", "warehouse"); ?>
                <?php
                $wh[''] = '';
                foreach ($warehouses as $warehouse) {
                    $wh[$warehouse->id] = $warehouse->name;
                }
                echo form_dropdown('warehouse_id', $wh, $kitchen->warehouse_id, 'class="form-control" data-placeholder="' . lang("select") . ' ' . lang("warehouse") . '" required="required"  style="width:100%;" ');
                ?>
            </div>

            <div class="form-group">
                <?= lang("printer", "printer_id").' *'; ?>
                <?php
                $printers[''] = '';
                foreach ($printers as $printer) {
                    $printers[$printer->id] = $printer->title;
                }               
                  echo form_dropdown('printer_id', $printers, (isset($_POST['printer_id']) ? $_POST['printer_id'] : ($kitchen ? $kitchen->printer_id : '')), 'class="form-control select"  placeholder="' . lang("select") . " " . lang("Printer") . '" style="width:100%" required="required"');
                ?>
            </div>

            <div class="form-group">
                <?= lang("kitchen_consolid_printer", "kitchen_consolid_printer").' *'; ?>
                    <?php
                    $print['0'] = lang("select_printer");
                    foreach ($printers as $printer) {
                    $print[$printer->id] = $printer->title;
                    }
                    echo form_dropdown('kitchen_consolid_printer_id', $print, (isset($_POST['kitchen_consolid_printer_id']) ? $_POST['kitchen_consolid_printer_id'] : ($kitchen ? $kitchen->kitchen_consolid_printer_id : '')), 'class="form-control select"  placeholder="' . lang("select") . " " . lang("Printer") . '" style="width:100%" ');
                ?>                
            </div>

           <div class="form-group">
           		<input type="checkbox" name="is_default" <?php if($kitchen->is_default == 1){ ?> checked <?php } ?> value="1"> 
                <?= lang("is_default", "is_default"); ?>
           </div>
            
            
            <?php echo form_hidden('id', $kitchen->id); ?>
        </div>
        <div class="modal-footer">
            <?php echo form_submit('edit_kitchen', lang('edit_kitchen'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>
<?= $modal_js ?>
 