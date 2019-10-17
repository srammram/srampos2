<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<script type="text/javascript" src="<?=$assets ?>js/customer_discount.js"></script>
<style>
    .table td:first-child {
        font-weight: bold;
    }
    label {
        margin-right: 10px;
    }
</style>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-folder-open"></i><?= lang('edit_bbq_daywise_discount'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row group-permission">
            <div class="col-lg-12">
                <p class="introtext"><?= lang('edit_bbq_daywise_discount'); ?></p>
                <?php 
                        echo admin_form_open("system_settings/edit_bbq_daywise_discount/".$id); ?>

                            <!-- <div class="form-group col-lg-6">
                                <?= lang('from_date', 'from_date'); ?>
                                <div class="controls ">
                                  <input type="text" name="from_date" class="form-control" placeholder="From Date " id="from_date" required="required"  value="<?= $daywisediscount->from_date; ?>"  autocomplete="off">
                                </div>
                            </div>
                            <div class="form-group col-lg-6">
                                <?= lang('to_date', 'to_date'); ?>
                              <div class="controls">
                                <input type="text" name="to_date" class="form-control" placeholder="To Date " id="to_date" required="required"  value="<?= $daywisediscount->to_date; ?>" autocomplete="off">
                              </div>
                            </div> -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <?= lang("select_bbq_menu", "select_bbq_menu"); ?>
                                 <select style="display: "  name="bbq_menu_id" class="form-control pos-input-tip bbq_menu_id" id="bbq_menu_id">
                                    <?php
                                    foreach ($bbq_menu as $bbqmenu) {                         
                                    ?>
                                    <option value="<?php echo $bbqmenu->bbq_menu_id; ?>"  <?php if($daywisediscount->bbq_menu_id == $bbqmenu->bbq_menu_id){  echo 'selected';  }else{ echo ''; }?>  ><?php echo $bbqmenu->name; ?>
                                    </option>
                                <?php } ?>
                                </select>                 
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
                                    <th colspan="" class="text-center"><?= lang("delete"); ?></th>                                    
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']; 
                                foreach($days as $day){
                                    $daywise_data = $this->site->getBBQDaywiseDiscountbyidandday($daywisediscount->id,$day);                                    
                                    if($daywise_data->days == $day){
                                        $checked = 'checked';
                                        $disabled = '';
                                    }else{
                                        $checked = '';
                                        $disabled = 'disabled';
                                    }
                                ?>                               
                                 <tr class="items">
                                    <td>
                                        <span style="inline-block">
                                        <input type="checkbox" value="<?= $day ?>" class="checkbox days" <?php echo $checked; ?> name="days[]">
                                        <label for="warehouse_stock" class="padding05"><?= $day ?></label>
                                        </span>
                                    </td>
                                    <td colspan="3" >
                                      <!--   <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label" for="disable_editing"><?= lang("odd_even"); ?></label>
                                                    <select style="display: "  name="discount_apply_type[]" class="form-control pos-input-tip discount_apply_type" id="odd_even_<?=$day?>" <?= $disabled?> >
                                                        <option value="0">No</option>
                                                        <option value="ODD" <?php if($lobster_data->discount_apply_type == 'ODD') { echo "selected"; }else{ echo ''; } ?> >ODD</option>
                                                        <option value="EVEN" <?php if($lobster_data->discount_apply_type == 'EVEN') { echo "selected"; }else{ echo ''; } ?> >EVEN</option>
                                                    </select>
                                            </div>
                                        </div> -->
                                        
                                          <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="control-label" for="disable_editing"><?= lang("discount_type"); ?></label>
                                                   <select style="display: "  name="discount_type[]" class="form-control pos-input-tip discount_type" id="discount_type_<?=$day?>" <?= $disabled?>>
                                                        <!-- <option value="0">No</option> -->
                                                        <option value="percentage" <?php if($daywise_data->discount_type == 'percentage') { echo "selected"; }else{ echo ''; } ?> ><?= lang("percentage"); ?></option>
                                                        <option value="amount" <?php if($daywise_data->discount_type == 'amount') { echo "selected"; }else{ echo ''; } ?>  ><?= lang("amount"); ?></option>
                                                    </select>
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
                                    <td>
                                         <input type="hidden" name="status[]" id="status_<?=$day?>" class="form-control tip numberonly"  value="<?php echo $daywise_data->status ?>" >
                                        <?php 
                                        if($daywise_data->days == $day){ 
                                            if($daywise_data->status!=1){
                                        ?>                                           
                                          <p id="status_change" value='<?=$daywise_data->status?>' data ="<?php echo $daywise_data->id; ?>" class="btn btn-success a-btn-slide-text">                                            
                                            <span><strong><?= lang("make_active"); ?></strong></span>            
                                        </p>
                                        <?php } else{ ?>
                                            <p id="status_change"  value='<?=$daywise_data->status?>' data ="<?php echo $daywise_data->id; ?>" class="btn btn-danger a-btn-slide-text">                                            
                                            <span><strong><?= lang("make_deactive"); ?></strong></span>            
                                        </p>

                                        <?php }

                                         }else{
                                      
                                    }
                                    ?>

                                    </td>
                                </tr>   
                                 <?php  } ?>                                
                                </tbody>
                            </table>
                        </div>                        
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary"><?=lang('update')?></button>
                        </div>
                        <?php echo form_close();?>
            </div>
        </div>
    </div>
</div>
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
</style>
<script>
$(document).on('click', '#status_change', function () {
    var id = $(this).attr('data');  
    var status = $(this).attr('value');  
    $.ajax({
            type: "POST",
            url: "<?=admin_url('system_settings/change_status_daywisediscount')?>",
            data: {id: id,status:status},
            dataType: "json",
            success: function (data) {
                location.reload();
            }
    });
});

$(document).on('ifChanged','.days', function (e) {
        $this = $(this);
        var days = $(this).val();
        if(($this).is(':checked')){
            $(this).iCheck('check');
            $('#odd_even_'+days).prop("disabled", false);
            $('#discount_type_'+days).prop("disabled", false);
            $('#adult_discount_val_'+days).prop("disabled", false);
            $('#child_discount_val_'+days).prop("disabled", false);
            $('#kids_discount_val_'+days).prop("disabled", false);          
            $('#minimum_cover_'+days).prop("disabled", false);          
        }else{
            $(this).iCheck('uncheck');
            $('#odd_even_'+days).prop("disabled", true);
            $('#discount_type_'+days).prop("disabled", true);
            $('#adult_discount_val_'+days).prop("disabled", true);
            $('#child_discount_val_'+days).prop("disabled", true);
            $('#kids_discount_val_'+days).prop("disabled", true);           
            $('#minimum_cover_'+days).prop("disabled", true);           
        }    
    });
</script>
