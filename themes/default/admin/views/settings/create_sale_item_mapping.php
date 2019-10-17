<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('create_sale_item_mapping'); ?></h4>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form');
        echo admin_form_open("system_settings/create_sale_item_mapping", $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>
            <?php $sale_type =$this->uri->segment(4); ?>
            <input type="hidden" name="sale_type" id="sale_type" value="<?php echo $sale_type; ?>">
            <div class="form-group">
                <?= lang('print_mirroring', 'print_mirroring'); ?>
                <?php       
                 $getavaildays=$this->site->saleitemsmappeddaysbysaletype($sale_type);
                 foreach ($getavaildays as $key => $value) {
                    $result[] = $value->days;
                }
                 $weeksdays = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];       
                 if($result){
                    $notmapweeksdays = array_diff($weeksdays, $result);                 
                 }else{
                    $notmapweeksdays = $weeksdays;                 
                 }          

                ?>
                <select name="days" class="form-control select2">
                     <!-- <?php if(in_array($row->id,$print_mirroring)) { echo 'selected="selected"';} ?> -->
                    <?php foreach($notmapweeksdays as  $row): ?>
                    <option value="<?=$row?>"><?=$row?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="modal-footer">
            <?php echo form_submit('create_sale_item_mapping', lang('create_sale_item_mapping'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<?= $modal_js ?>
