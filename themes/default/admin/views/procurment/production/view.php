<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<style>
     .td-value{
        font-weight: bold;
     }
</style>
<div class="modal-dialog modal-lg" style="width: 1011px !important;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?=lang('Production_items')?> <?=$production->reference_no?></h4>
        </div>
      
      
        <div class="modal-body">
          <div class="table-responsive">
            <table class="custom_tables">
                <tr>
                    <td colspan=2 ><?=lang('date')?> : </td>
                    <td colspan=2 class="td-value"><?=$production->date?></td>
                    
                    <td colspan=2 ><?=lang('quotation_no')?> : </td>
                    <td colspan=2 class="td-value"><?=$production->reference_no?></td>
                    
                    
                    <td colspan=2 ><?=lang('status')?>: </td>
                    <td colspan=2  class="td-value"><?=$production->status?></td>
                </tr>
               
                
              
                
            </table>
                    <table id="ItemData" class="table table-bordered table-condensed table-hover table-striped">
                        <thead>
                        <tr>
                            <th ><?= lang('s.no'); ?></th>					    
                            <th ><?= lang("store_name"); ?></th>
                            <th><?= lang('code'); ?></th>
                            <th ><?= lang("product_name"); ?></th>
                            <th ><?= lang("qty"); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $i =1; foreach($pro_items as $k => $row) : ?>
                        <tr>
                            <td><?=$i?></td>
                            <td><?=$row['row']->store_name?></td>
                            <td><?=$row['row']->product_code?></td>
                            <td><?=$row['row']->product_name?></td>
                            <td class="text-right"><?=$this->sma->formatDecimal($row['row']->quantity)?></td>
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