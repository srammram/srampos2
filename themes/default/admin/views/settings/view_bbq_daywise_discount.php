<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<style>
    .group-permission ul{
    list-style: none;    
    }
    .reports ul{
    -moz-column-count: 4 !important;
    -moz-column-gap: 23px;
    -webkit-column-count: 4 !important;
    -webkit-column-gap: 23px;
    column-count: 4 !important;
    column-gap: 0px;/*23px;*/
    }
    .orders-settings ul,.billing-settings ul,.group-permission ul{
    -moz-column-count: 3;
    -moz-column-gap: 23px;
    -webkit-column-count: 3;
    -webkit-column-gap: 23px;
    column-count: 3;
    column-gap: 0px;/*23px;*/
    }
    .restaurants-group-permission ul li{
     /*-moz-column-count: 1 !important;
    -moz-column-gap: 23px;
    -webkit-column-count: 1 !important;
    -webkit-column-gap: 23px;
    column-count: 1 !important;
    column-gap: 0px;*//*23px;*/
     display: block;
    float: left;
    width:45%
    }
    .modal-dialog {
    width: 904px!important;
    margin: 30px auto!important;
}
</style>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?= lang('view_bbq_daywise_discount'); ?></h4>
        </div>        
        <div class="modal-body">            
        <div class="table-responsive">
                
                                <?php
                                    $saletype ='';                                    
                                    foreach ($bbq_menu as $bbqmenu) {                                                           
                                     if($daywisediscount->bbq_menu_id == $bbqmenu->bbq_menu_id){ 
                                           $saletype = $bbqmenu->name;
                                        }
                                } ?>

                            <div class="col-md-6">
                            <div class="form-group">
                                <div class="form-group">
                                <?= lang('bbq_menu', 'bbq_menu'); ?>
                                <?= form_input('names', set_value('name', $saletype), 'class="form-control tip" id="names"'); ?>
                            </div>

                            </div>
                        </div> 


                  <div class="table-responsive">
                            <table class="table table-bordered table-hover table-striped reports-table">
                                <thead>
                                <tr>
                                    <th rowspan="1" class="text-center" style="width:150px;"><?= lang("days"); ?>
                                    </th>
                                    <th colspan="3" class="text-center"><?= lang("discount_condition"); ?></th>
                                    <th colspan="" class="text-center"><?= lang("discount"); ?></th>                                    
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']; 
                                foreach($days as $day){
                                    $daywise_data = $this->site->getBBQDaywiseDiscountbyidandday($daywisediscount->id,$day);                                    
                                    if($daywise_data->days == $day){
                                        $checked = 'checked';
                                        $disabled = 'disabled';
                                    }else{
                                        $checked = '';
                                        $disabled = 'disabled';
                                    }
                                ?>                               
                                 <tr class="items">
                                    <td>
                                        <span style="inline-block">
                                        
                                        <label for="warehouse_stock" class="padding05"><?= $day ?></label>
                                        </span>
                                    </td>
                                    <td colspan="3">                                        
                                          <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="control-label" for="disable_editing"><?= lang('discount_type', 'discount_type'); ?></label>
                                                <input type="text" name="adult_discount_val[]" id="adult_discount_val_<?=$day?>" class="form-control tip numberonly" value="<?php echo $daywise_data->discount_type; ?>" <?= $disabled?>>
                                               
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                          <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="control-label" for="disable_editing"><?= lang("adults"); ?></label>
                                                    <input type="text" name="adult_discount_val[]" id="adult_discount_val_<?=$day?>" class="form-control tip numberonly" value="<?php echo $daywise_data->adult_discount_val ?>" <?= $disabled?>>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="control-label" for="disable_editing"><?= lang("childs"); ?></label>
                                                    <input type="text" name="child_discount_val[]" id="child_discount_val_<?=$day?>" class="form-control tip numberonly"   value="<?php echo $daywise_data->child_discount_val ?>"  <?= $disabled?>>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="control-label" for="disable_editing"><?= lang("kids"); ?></label>
                                                <input type="text" name="kids_discount_val[]" id="kids_discount_val_<?=$day?>" class="form-control tip numberonly"  value="<?php echo $daywise_data->kids_discount_val ?>"   <?= $disabled?>>
                                            </div>
                                        </div>
                                    </td>
                                </tr>   
                                 <?php  } ?>                                
                                </tbody>
                            </table>
                        </div> 
                </div>             
        </div>        
    </div>
    <?= form_close(); ?>
</div>
<script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>
<!-- <?= $modal_js ?> -->
<script>
$("input").prop('readonly', true);


$(document).ready(function() {
    $('input[type="checkbox"],[type="radio"]').not('.skip').iCheck({
        checkboxClass: 'icheckbox_square-blue',
        radioClass: 'iradio_square-blue',
        increaseArea: '20%'
    });   
});
</script>

