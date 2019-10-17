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
            <h4 class="modal-title" id="myModalLabel"><?=lang('Quotation_request')?> <?=$q_request->reference_no?></h4>
        </div>
      
      
        <div class="modal-body">
          <div class="table-responsive">
            <table class="custom_tables">
                <tr>
                    <td colspan=2 ><?=lang('date')?> : </td>
                    <td colspan=2 class="td-value"><?=$q_request->date?></td>
                    
                    <td colspan=2 ><?=lang('quotation_no')?> : </td>
                    <td colspan=2 class="td-value"><?=$q_request->reference_no?></td>
                    
                    <td colspan=2 ><?=lang('currency')?>: </td>
                    
                    <?php $currency = '';foreach($currencies as $k => $row) :
                    if($q_request->currency==$row->id){
                        $currency = $row->code;
                    }
                    endforeach; ?>
                    <td colspan=2 class="td-value"><?=$currency?></td>
                </tr>
                <tr>   
                    <td colspan=2 ><?=lang('supplier')?>: </td>
                    <td colspan=2 class="td-value"><?=$q_request->supplier?></td>
                    
                    <td colspan=2 ><?=lang('supplier_address')?>: </td>
                    <td colspan=2 class="td-value"><?=$q_request->supplier_address?></td>
                    
                    <td colspan=2 ><?=lang('status')?>: </td>
                    <td colspan=2  class="td-value"><?=$q_request->status?></td>
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
                                            <th ><?= lang("cost_price"); ?></th>
                                            <th ><?= lang("selling_price"); ?></th>
                                        </tr>
                        </thead>
                        <tbody>
                        <?php $i =1; foreach($q_request_items as $k => $row) : ?>
                        <tr>
                            <td><?=$i?></td>
                            <td><?=$row['row']->store_name?></td>
                            <td><?=$row['row']->product_code?></td>
                            <td><?=$row['row']->product_name?></td>
							<td class="text-right"><?php echo number_format((float)$row['row']->quantity, 2, '.', ''); ?></td>
							<td class="text-right"><?php echo number_format((float)$row['row']->cost_price, 2, '.', ''); ?></td>
                            <td class="text-right"><?php echo number_format((float)$row['row']->selling_price, 2, '.', ''); ?></td>
							
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