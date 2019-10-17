<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('import_ingredients_mapping_by_csv'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <div><a href="<?=admin_url('recipe/mapping_download_sample')?>" class="btn btn-primary pull-right"><i class="fa fa-download"></i>&nbsp<?=lang('Download_sample_file')?></a></div>
                <?php
                $attrib = array('class' => 'form-horizontal', 'data-toggle' => 'validator', 'role' => 'form');
                echo admin_form_open_multipart("recipe/ingredients_import_csv", $attrib)
                ?>
                <div class="row">
                    <div class="col-md-12">

                        <!--<div class="well well-small">
                            <a href="<?php echo base_url(); ?>assets/csv/sample_recipe.csv"
                               class="btn btn-primary pull-right"><i
                                    class="fa fa-download"></i> <?= lang("download_sample_file") ?></a>
                            <span class="text-warning"><?= lang("csv1"); ?></span><br/><?= lang("csv2"); ?> <span
                                class="text-info">(<?= lang("name") . ', ' . lang("code") . ', ' . lang("barcode_symbology") . ', ' .  lang("brand") . ', ' . lang("category_code") . ', ' . lang("unit_code") . ', ' . lang("sale").' '.lang('unit_code') . ', ' . lang("purchase").' '.lang("unit_code") . ', ' .  lang("cost") . ', ' . lang("price") . ', ' . lang("alert_quantity") . ', ' . lang("tax") . ', ' . lang("tax_method") . ', ' . lang("image") . ', ' . lang("subcategory_code") . ', ' . lang("recipe_variants_sep_by"). ', ' . lang("pcf1"). ', ' . lang("pcf2"). ', ' . lang("pcf3"). ', ' . lang("pcf4"). ', ' . lang("pcf5"). ', ' . lang("pcf6"). ', ' . lang("hsn_code"); ?>
                                )</span> <?= lang("csv3"); ?>
                                <p><?= lang('images_location_tip'); ?></p>

                        </div>-->

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="csv_file1"><?= lang("upload_file"); ?></label>
                                <input type="file" data-browse-label="<?= lang('browse'); ?>" name="userfile" class="form-control file" data-show-upload="false" data-show-preview="false" id="csv_file1" required="required"/>
                            </div>

                            <div class="form-group">
                                <?php echo form_submit('import', $this->lang->line("import"), 'class="btn btn-primary"'); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?= form_close(); ?>
            </div>
        </div>
    </div>
	<!--
    <div class="box-content">
        <div class="row">
            <div class="col-lg-3">
                <div class="kitchen_area">
                    <label><?=lang('available_kitchen_area')?></label>
                    <?php if(!empty($kitchens)) : ?>
                    <div class="col-lg-12">
                    <ul class="import-csv-karea">
                    <?php foreach($kitchens as $k => $row) : ?>
                        <li><?=$row->name;?></li>
                    <?php endforeach; ?>
                    </ul>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="warehouse_area">
                    <label><?=lang('available_warehouses')?>[<?=lang('use_id_in_csv')?>]</label>
                    <?php if(!empty($warehouses)) : ?>
                    <div class="col-lg-12">
                    <ul class="import-csv-karea">
                    <?php foreach($warehouses as $k => $row) : ?>
                        <li><label><?=lang('id')?>:</label><?=$row->id;?> &nbsp;&nbsp;&nbsp;<label><?=lang('name')?>:</label><?=$row->name;?></li>
                    <?php endforeach; ?>
                    </ul>
                    <label><?=lang('enter_id\'s_comma_seprated_to_add multiple_warehouse');?></label>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-lg-2">
                <div class="currency_area">
                    <label><?=lang('available_currency')?></label>
                    <?php if(!empty($currency)) : ?>
                    <div class="col-lg-12">
                    <ul class="import-csv-karea">
                    <?php foreach($currency as $k => $row) : ?>
                        <li><?=$row->code;?></li>
                    <?php endforeach; ?>
                    </ul>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
     <div class="box-content">
        <div class="row">
            <div class="col-lg-3">
                <?php $opts =  array('0' => lang('both'), '1' => lang('alakat'), '2' => lang('bbq')); ?>
                 <div class="currency_area">
                    <label><?=lang('alakat/bbq')?></label>
                    <?php if(!empty($opts)) : ?>
                    <div class="col-lg-12">
                    <ul class="import-csv-karea">
                    <?php foreach($opts as $k => $row) : ?>
                        <li><?=$row?> : <?=$k?></li>
                    <?php endforeach; ?>
                    </ul>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-lg-3">
                <?php $opts = array('standard','combo','trade','production','addon'); ?>
                 <div class="currency_area">
                    <label><?=lang('available_sales_item_types')?></label>
                    <?php if(!empty($opts)) : ?>
                    <div class="col-lg-12">
                    <ul class="import-csv-karea">
                    <?php foreach($opts as $k => $row) : ?>
                        <li><?=$row?></li>
                    <?php endforeach; ?>
                    </ul>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
     </div>
     -->
</div>
<style>
    .import-csv-karea li{
        list-style:none;
    }
    .kitchen_area label,.currency_area label,.warehouse_area label{
        font-weight: bold;
    }
</style>
<script>
    $(document).ready(function(){
         setInterval(function(){
  if ($.cookie("fileLoading")) {
    // clean the cookie for future downoads
    $.removeCookie("fileLoading");

    //redirect
    window.location.reload();
  }
},1000);
    })
   
</script>