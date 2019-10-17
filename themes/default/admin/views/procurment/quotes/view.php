<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<style>
     .td-value{
        font-weight: bold;
     }
</style>
<div class="modal-dialog modal-lg" style="width: 1011px !important;">
<?php /*  echo '<pre>';
 print_r($quotations);
die;  */?>
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?=lang('Quotation')?> <?=$quotations->reference_no?></h4>
        </div>
      
      
        <div class="modal-body">
          <div class="table-responsive">
            <table class="custom_tables">
                <tr>
                    <td colspan=2 ><?=lang('date')?> : </td>
                    <td colspan=2 class="td-value"><?=$quotations->date?></td>
                    
                    <td colspan=2 ><?=lang('quotation_no')?> : </td>
                    <td colspan=2 class="td-value"><?=$quotations->reference_no?></td>
                    
                    <td colspan=2 ><?=lang('currency')?>: </td>
                    
                    <?php $currency = '';foreach($currencies as $k => $row) :
                    if($quotations->currency==$row->id){
                        $currency = $row->code;
                    }
                    endforeach; ?>
                    <td colspan=2 class="td-value"><?=$currency?></td>
                </tr>
                <tr>   
                    <td colspan=2 ><?=lang('supplier')?>: </td>
                    <td colspan=2 class="td-value"><?=$supplier_name; ?></td>
                    
                    <td colspan=2 ><?=lang('supplier_address')?>: </td>
                    <td colspan=2 class="td-value"><?=$supplier_address?></td>
                    
                    <td colspan=2 ><?=lang('status')?>: </td>
                    <td colspan=2  class="td-value"><?=$quotations->status?></td>
                </tr>
                <tr>
                    
                    
                    <td colspan=2 ><?=lang('quotation_request_no')?>: </td>
                    <td colspan=2  class="td-value"><?=$quotations->req_reference_no?></td>
                    
                    <td colspan=2 ><?=lang('quotation_request_date')?>: </td>
                    <td colspan=2  class="td-value"><?=$quotations->requestdate?></td>
                    
                </tr>
                
              
                
            </table>
                    <table id="ItemData" class="table table-bordered table-condensed table-hover table-striped">
                        <thead>
                        <tr>
                                            <th ><?= lang('s.no'); ?></th>
                                            <th><?= lang('code'); ?></th>
                                            <th ><?= lang("description"); ?></th>
                                            <th ><?= lang("qty"); ?></th>
                                            <th ><?= lang("cost_price"); ?></th>
                                            <th ><?= lang("selling_price"); ?></th>
                                        </tr>
                        </thead>
                        <tbody>
                        <?php $i =1; foreach($quotations_items as $k => $row) : ?>
                        <tr>
                            <td><?=$i?></td>
                            <td><?=$row['row']->product_code?></td>
                            <td><?=$row['row']->product_name?></td>
                            <td class="text-right"><?php echo number_format((float)$row['row']->quantity, 2, '.', ''); ?></td>
                            <td class="text-right"><?php echo number_format((float)$row['row']->cost_price, 2, '.', ''); ?></td>
                            <td class="text-right"><?php echo number_format((float)$row['row']->selling_price, 2, '.', ''); ?></td>
                            <!-- 
                            <td><?=$row['row']->quantity?></td>
                            <td><?=$row['row']->cost_price?></td>
                            <td><?=$row['row']->selling_price?></td> -->
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