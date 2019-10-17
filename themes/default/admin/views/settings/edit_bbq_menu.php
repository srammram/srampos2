<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
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
        <h2 class="blue"><i class="fa-fw fa fa-folder-open"></i><?= lang('edit_bbq_menu'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row group-permission">
            <div class="col-lg-12">
                <p class="introtext"><?= lang('edit_bbq_menu'); ?></p>
                <?php 
                   echo admin_form_open("system_settings/edit_bbq_menu/". $id); ?>
                        <div class="col-md-6">
                            <div class="form-group">
                                <?= lang("select_bbq_menu", "select_bbq_menu"); ?>
                                 <select style="display: "  name="sale_type" class="form-control pos-input-tip sale_type" id="sale_type">
                                    <?php
                                    foreach ($bbqsale_type as $saletype) {                         
                                    ?>
                                    <option value="<?php echo $saletype->id; ?>" <?php if($saletype->id == $bbqmenu->sale_type){  echo 'selected';  }else{ echo ''; }?> ><?php echo $saletype->name; ?>
                                    </option>
                                <?php } ?>
                                </select>                 
                            </div>
                        </div>                  
                         <div class="col-md-6">   
                           <div class="form-group">                                
                                <?= lang('name', 'name'); ?>
                                <?= form_input('name', set_value('name', $bbqmenu->name), 'class="form-control tip" id="name" required="required"'); ?>
                            </div>                            
                        </div>
                        <div class="form-group">
                            <?= lang("image", "image") ?>
                            <input id="image" type="file" data-browse-label="<?= lang('browse'); ?>" name="userfile" data-show-upload="false" data-show-preview="false" class="form-control file">
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-striped reports-table">
                                <thead>                               
                                <tr>
                                    <th class="text-center" style="width:150px;"><?= lang("days"); ?>
                                    </th>
                                    <th class="text-center"><?= lang("audlt_price"); ?></th>
                                    <th class="text-center"><?= lang("child_price"); ?></th>
                                    <th class="text-center"><?= lang("kid_pride"); ?></th>                                    
                                </tr>                                
                                </thead>
                                <tbody>                               
                                <?php
                                $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                             /*   echo "<pre>";
                                print_r($days);

                                echo "<pre>";*/
                                /*print_r($bbqmenudaywiseprice);*/
                                foreach($bbqmenudaywiseprice as $day){
                                   /* echo "<pre>";
                                    print_r($day->day);*/
                                    $days = $day->day;
                                    // $bbq_menu_price = $this->site->getBBQlobsterDAYS($day);
                                   /* $lobster_data = $this->site->getBBQlobsterDAYS($day);                                    
                                    if($lobster_data->days == $day){
                                        $checked = 'checked';
                                        $disabled = '';
                                    }else{
                                        $checked = '';
                                        $disabled = 'disabled';
                                    }*/
                                ?>                               
                                 <tr class="items">
                                    <td>
                                        <span style="inline-block">
                                        <!-- <input type="checkbox" value="<?= $day ?>" class="checkbox days"  name="days[]"> -->
                                        <input type="hidden" name="days[]" class="form-control tip" value="<?=$days?>">
                                        <label for="warehouse_stock" class="padding05"><?= $days ?></label>
                                        </span>
                                    </td>
                                    <td>                                        
                                         <div class="col-md-12">
                                            <div class="form-group">                                                
                                                    <input type="text" name="adult_price[]" id="adult_price_<?=$day?>" maxlength="15" class="form-control tip numberonly" value="<?php echo $day->adult_price ?>">
                                            </div>
                                    </td>
                                   <td >                                        
                                         <div class="col-md-12">
                                            <div class="form-group">                                                
                                                    <input type="text" name="child_price[]" id="child_price_<?=$day?>"  maxlength="15" class="form-control tip numberonly" value="<?php echo $day->child_price ?>">
                                            </div>
                                    </td>
                                    <td>                                        
                                         <div class="col-md-12">
                                            <div class="form-group">                                                
                                                    <input type="text" name="kids_price[]" id="kids_price_<?=$day?>" maxlength="15" class="form-control tip numberonly" value="<?php echo $day->kids_price ?>">
                                            </div>
                                    </td>
                                </tr>   
                                <?php } ?>                       
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
/*$(document).on('click', '#detele_get_buy', function () {
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
});*/

/*$(document).on('ifChanged','.days', function (e) {
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
    });*/

/*$(document).on('change', '.adult_buy', function(){    
    var num = $(this).val();
    var days = $(this).attr('id');
    days = days.substring(10, 20);
    $('#adult_get_'+days).select2('data', null);
    for(i=num; i<15; i++){
        $("#adult_get_"+days+" option:contains("+i+")").attr("disabled","disabled");
    }
});*/
/*
$(document).on('change', '.child_buy', function(){    
    var num = $(this).val();
    var days = $(this).attr('id');
    days = days.substring(10, 20);
    $('#child_get_'+days).select2('data', null);
    for(i=num; i<15; i++){
        $("#child_get_"+days+" option:contains("+i+")").attr("disabled","disabled");
    }
});*/

$/*(document).on('change', '.kids_buy', function(){
    var num = $(this).val();
    var days = $(this).attr('id');
    days = days.substring(10, 20);
    $('#kids_get_'+days).select2('data', null);
    for(i=num; i<15; i++){
        $("#kids_get_"+days+" option:contains("+i+")").attr("disabled","disabled");
    }
});*/
</script>
