<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php echo "string"; ?>
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
        <h2 class="blue"><i class="fa-fw fa fa-folder-open"></i><?= lang('add_bbq_daywise_discount'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row group-permission">
            <div class="col-lg-12">
                <p class="introtext"><?= lang('add_bbq_daywise_discount'); ?></p>
                <?php 
                        echo admin_form_open("system_settings/add_bbq_daywise_discount/"); ?>

                            <div class="form-group col-lg-6" style="display: none">
                                <?= lang('from_date', 'from_date'); ?>
                                <div class="controls ">
                                  <input type="text" name="from_date" class="form-control" placeholder="From Date " id="from_date"   autocomplete="off">
                                </div>
                            </div>
                            <div class="form-group col-lg-6" style="display: none">
                                <?= lang('to_date', 'to_date'); ?>
                              <div class="controls">
                                <input type="text" name="to_date" class="form-control" placeholder="To Date " id="to_date"    autocomplete="off">
                              </div>
                            </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <?= lang("select_bbq_menu", "select_bbq_menu"); ?>
                                 <select style="display: "  name="bbq_menu_id" class="form-control pos-input-tip bbq_menu_id" id="bbq_menu_id">
                                    <?php
                                    foreach ($bbq_menu as $bbqmenu) {                         
                                    ?>
                                    <option value="<?php echo $bbqmenu->bbq_menu_id; ?>"><?php echo $bbqmenu->name; ?>
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
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']; 
                                foreach($days as $day){
                                    $lobster_data = $this->site->getBBQDAYwiseDiscount($day);                                    
                                    if($lobster_data->days == $day){
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
                                        <input type="checkbox" value="<?= $day ?>" class="checkbox days" name="days[]">
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
                                                        <option value="percentage"  ><?= lang("percentage"); ?></option>
                                                        <option value="amount" ><?= lang("amount"); ?></option>
                                                    </select>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                          <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="control-label" for="disable_editing"><?= lang("adults"); ?></label>
                                                    <input type="text" name="adult_discount_val[]" id="adult_discount_val_<?=$day?>" class="form-control tip numberonly" <?= $disabled?>>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="control-label" for="disable_editing"><?= lang("childs"); ?></label>
                                                    <input type="text" name="child_discount_val[]" id="child_discount_val_<?=$day?>" class="form-control tip numberonly" <?= $disabled?>>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="control-label" for="disable_editing"><?= lang("kids"); ?></label>
                                                <input type="text" name="kids_discount_val[]" id="kids_discount_val_<?=$day?>" class="form-control tip numberonly" <?= $disabled?>>
                                            </div>
                                        </div>
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
$(document).on('click', '#detele_get_buy', function () {
    var id = $(this).attr('data'); 
    $.ajax({
            type: "POST",
            url: "<?=admin_url('system_settings/deleteLobster')?>",
            data: {id: id},
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
/*$(document).on('change', '.adult_buy', function(){    
    var num = $(this).val();
    var days = $(this).attr('id');
    days = days.substring(10, 20);
    $('#adult_get_'+days).select2('data', null);
    for(i=num; i<15; i++){
        $("#adult_get_"+days+" option:contains("+i+")").attr("disabled","disabled");
    }
});
$(document).on('change', '.child_buy', function(){    
    var num = $(this).val();
    var days = $(this).attr('id');
    days = days.substring(10, 20);
    $('#child_get_'+days).select2('data', null);
    for(i=num; i<15; i++){
        $("#child_get_"+days+" option:contains("+i+")").attr("disabled","disabled");
    }
});
$(document).on('change', '.kids_buy', function(){
    var num = $(this).val();
    var days = $(this).attr('id');
    days = days.substring(10, 20);
    $('#kids_get_'+days).select2('data', null);
    for(i=num; i<15; i++){
        $("#kids_get_"+days+" option:contains("+i+")").attr("disabled","disabled");
    }
});*/
</script>
