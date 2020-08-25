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
            <h4 class="modal-title" id="myModalLabel"><?=lang('Wastage')?> - <?=$wastage->reference_no?></h4>
        </div>
		
        <div class="modal-body">
          <div class="table-responsive">
            <table class="custom_tables">
                <tr>
                    <td colspan=2 ><?=lang('date')?> : </td>
                    <td colspan=2 class="td-value"><?=$wastage->date?></td>
                    
                    <td colspan=2 ><?=lang('reference_no')?> : </td>
                    <td colspan=2 class="td-value"><?=$wastage->reference_no?></td>
                    
                     <td colspan=2 ><?=lang('type')?>: </td>
                    <td colspan=2  class="td-value"><?=$wastage->type?></td>
                </tr>
                <tr>
                    
                   
                    <td colspan=2 ><?=lang('status')?>: </td>
                    <td colspan=2  class="td-value"><?=$wastage->status?></td>
					<td colspan=2 ><?=lang('note')?>: </td>
                    <td colspan=2  class="td-value"><?=$wastage->note?></td>
                  
                    
                </tr>
               
                
                
            </table>
                    <table id="ItemData" class="table table-bordered table-condensed table-hover table-striped">
                        <thead>
                        <tr>
                                            <th class="col-md-1" ><?= lang('s.no'); ?></th>
                                            <th class="col-md-2"><?= lang('Product_name'); ?></th>
											<th class="col-md-2"><?= lang('batch'); ?></th>
                                           <th class="col-md-2"><?= lang("Category"); ?></th>
                                            <th class="col-md-2"><?= lang("Subcategory"); ?></th>
                                            <th class="col-md-2"><?= lang("Brand"); ?></th>
                                            <th class="col-md-1"><?= lang("Variant"); ?></th>
											<th class="col-md-1"><?= lang("wastage_quantity"); ?></th>
											<th class="col-md-1"><?= lang("uom"); ?></th>
											<th class="col-md-1"><?= lang("Cost"); ?></th>
                
                                        </tr>
                        </thead>
                        <tbody>
                        <?php $i =1; foreach($wastage_items as $k => $row) : ?>
                        <tr>
                            <td><?=$i?></td>
                            <td><?=$row->product_name; ?></td>
                            <td><?= !empty($row->batch_no && $row->batch_no !='null')?$row->batch_no:''; ?></td>
                            <td><?=$row->category_name; ?></td>
							<td><?=$row->subcategory_name; ?></td>
							<td><?=$row->brand_name; ?></td>
                            <td><?=$row->variant; ?></td>
                            <td><?= $this->sma->formatDecimal($row->wastage_qty); ?></td>
							<td><?= $row->product_unit_code; ?></td>
							<td><?= $row->net_amount; ?></td>
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