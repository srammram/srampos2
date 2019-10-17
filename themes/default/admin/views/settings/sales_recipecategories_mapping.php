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
        <h2 class="blue"><i class="fa-fw fa fa-folder-open"></i><?= lang("sales_recipecategories_mapping"); ?></h2>
    </div>
    <div class="box-content">
        <div class="row group-permission">
            <div class="col-lg-12">
                <!-- <p class="introtext"><?= lang('buy_x_get_x'); ?></p> -->
                <?php 
                    echo admin_form_open("system_settings/sales_recipecategories_mapping/"); ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-striped reports-table">
                            <thead>
                            <tr>
                                <th rowspan="1" class="text-center" style="width:150px;"><?= lang("days"); ?>
                                </th>
                                <th colspan="5" class="text-center"><?= lang("recipe_category"); ?></th>
                            </tr>
                            </thead>
                            <tbody>
                         	<?php
							$days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
							foreach($days as $day){
                                /*echo "<pre>";
                                print_r($day);*/
								/*$buyxgetx_data = $this->site->getBBQbuyxgetxDAYS($day);                                    
								if($buyxgetx_data->days == $day){
									$checked = 'checked';
									$disabled = '';
								}else{
									$checked = '';
									$disabled = 'disabled';
								}*/
							?>
                            <input type="hidden" value="<?= $day ?>" class="checkbox days" name="days[]">
                           
                             <tr class="items">
                            	<td>
                                	<span style="inline-block">                                       
                                    <label for="warehouse_stock" class="padding05"><?= $day ?></label>
                                    </span>
                                </td>
                                <td>
                                    <?php 
                                    foreach($recipe_groups as $cate ) :
                                        $buyxgetx_data = $this->site->Getsalesrecipecategoriesmapping($day);
                                        $HiddenProducts = explode(',',$buyxgetx_data->categories_id);
                                        if (in_array($cate->id, $HiddenProducts)) {
                                          $checked = 'checked';
                                        } else {
                                          $checked = '';
                                        }

                                     ?>                                            
                                        <span style="inline-block">
                                            <input type="checkbox" value="<?= $cate->id ?>" class="checkbox days" <?php echo $checked; ?> name="categories[<?= $day ?>][]">
                                            <label for="warehouse_stock" class="padding05"><?= $cate->name ?></label>
                                        </span>
                                      <?php endforeach; ?>
                                </td>                                   
                            </tr>   
                            <?php } ?>
							</tbody>
                        </table>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary"><?=lang('update')?></button>
                    </div>
                    <?php echo form_close();
                   ?>
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
     display: block;
    float: left;
    width:45%
    }
</style>
<script>

</script>
