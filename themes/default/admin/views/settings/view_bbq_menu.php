<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?= lang('view_bbq_menu'); ?></h4>
        </div>        
        <div class="modal-body">            
        <div class="table-responsive">

                 <?php
                    $sale_type = '';
                    foreach ($bbqsale_type as $saletype) {                         
                     if($saletype->id == $bbqmenu->sale_type){
                      $sale_type = $saletype->name;  
                     }  } ?>

                <div class="form-group">
                    <?= lang('bbq_menu', 'bbq_menu'); ?>
                    <?= form_input('names', set_value('name', $sale_type), 'class="form-control tip" id="names"'); ?>
                </div>


                <div class="form-group">
                    <?= lang('name', 'name'); ?>
                    <?= form_input('name', set_value('name', $bbqmenu->name), 'class="form-control tip" id="name" required="required"'); ?>
                </div>


                    <table class="table table-bordered table-hover table-striped reports-table">
                        <thead>                               
                        <tr>
                            <th class="text-center" style="width:150px;"><?= lang("days"); ?>
                            </th>
                            <th class="text-center"><?= lang("adult_price"); ?></th>
                            <th class="text-center"><?= lang("child_price"); ?></th>
                            <th class="text-center"><?= lang("kid_pride"); ?></th>                                    
                        </tr>                                
                        </thead>
                        <tbody>                               
                        <?php
                        $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                        foreach($bbqmenudaywiseprice as $day){                                   
                            $days = $day->day;                                   
                        ?>                               
                         <tr class="items">
                            <td>
                                <span style="inline-block">                                        
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
        </div>        
    </div>
    <?= form_close(); ?>
</div>
<script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>
<!-- <?= $modal_js ?> -->
<script>
$("input").prop('readonly', true);

</script>

