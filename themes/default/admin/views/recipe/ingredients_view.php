<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<style>
     .td-value{
        font-weight: bold;
     }
</style>
<div class="modal-dialog modal-lg" style="width: 1159px !important;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>            
        </div>
        <div class="modal-body">
            <table class="table custom_tables">
                        <tbody>
                            <tr>
                        <td width="100px">
                            <?= lang("item_type", "type") ?>
                        </td>
                        <td width="350px">
                            <?php                                           
                            $opts = array('0' => lang('select'), 'production' => lang('production'),'quick_service' => lang('quick_service'), 'semi_finished' => lang('semi_finished'));
                            echo form_dropdown('type', $opts, (isset($_POST['type']) ? $_POST['type'] : ($recipe ? $recipe->type : '')), 'class="form-control" id="type" required="required" disabled');                            
                            ?>
                        </td>
                        <td width="100px">
                            <?= lang("item_name", "item_name"); ?>
                        </td>
                        <td width="350px">
                            <select name="item_name" id="item_name" class="form-control" disabled>                                
                                <?php
                                $variant ='';
                                if($recipe->varient_name != ''){
                                    $variant =' - '.$recipe->varient_name;
                                }?>
                                <option value="<?php echo  $recipe->id ?>" <?php if($recipe->name == $_POST['item_name']) { ?>   selected= selected  <?php  } ?> ><?php echo  $recipe->name. $variant; ?></option>
                                
                            </select>  
                            <input type="hidden" name="item_name" value="<?php echo  $recipe->id ?>" >
                        </td>                        
                    </tr>                  
                    </tbody>
                </table>

          <div class="table-responsive">            
                    <table id="ItemData" class="table table-bordered table-condensed table-hover table-striped">
                        <thead>
                        <tr>
                            <th ><?= lang('s.no'); ?></th>
                            <th><?= lang('item_name'); ?></th>                                
                            <th ><?= lang("qty"); ?></th>
                            <th ><?= lang("uom"); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $i =1; foreach($product_recipe as $k => $row) : ?>
                        <tr>
                            <?php $row->category_name;
                                 if($row->category_name != ''){
                                    $cat = ' Cat-'.$row->category_name;
                                    }else{
                                        $cat = '';
                                    }                               
                                    if($row->subcategory_name != ''){
                                        $sub_cat = ' Sub-'.$row->subcategory_name;
                                    }else{
                                        $sub_cat = '';
                                    }
                                    if($row->brand_name != ''){
                                        $brand = ' Brand-'.$row->brand_name;
                                    }else{
                                        $brand = '';
                                    }                               
                            ?>
                            <td><?=$i?></td>
                            <td><?=$row->recipe_name.$cat.' | '.$sub_cat.' | '.$brand ?></td>
                            <td><?=$row->quantity?></td>
                            <td><?= $row->units_name; ?></td>
                        </tr>
                        <?php $i++;endforeach; ?>
                        </tbody>
                    </table>
                
                </div>

        </div>
        <div class="modal-footer">
            
        </div>
    </div>
    
</div>
<?= $modal_js ?>