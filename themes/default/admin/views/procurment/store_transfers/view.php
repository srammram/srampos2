<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<style>
.td-value {
    font-weight: bold;
}
</style>
<div class="modal-dialog modal-lg" style="width: 1011px !important;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?=lang('store_transfers')?> - <?=$store_tranf_details->reference_no?></h4>
        </div>
        <?php
		/* echo '<pre>';
      print_r($store_req);
	  die; */
	  ?>

        <div class="modal-body">
            <div class="table-responsive">
                <table class="custom_tables">
                    <tr>
                        <td colspan=2><?=lang('date')?> : </td>
                        <td colspan=2 class="td-value"><?= date('Y-m-d',strtotime($store_tranf_details->created_on))?></td>

                        <td colspan=2><?=lang('reference_no')?> : </td>
                        <td colspan=2 class="td-value"><?=$store_tranf_details->reference_no?></td>

                        <td colspan=2><?=lang('status')?>: </td>
                        <td colspan=2 class="td-value"><?=$store_tranf_details->status?></td>
                    </tr>
                    <tr>
                        <td colspan=2><?=lang('From_store')?>: </td>
                        <td colspan=2 class="td-value"><?=$fromstore->name?></td>
                        <td colspan=2><?=lang('to_store')?>: </td>
                        <td colspan=2 class="td-value"><?=$to_store->name?></td>

                    </tr>
                    <tr>
                        <td colspan=2><?=lang('remarks')?>: </td>
                        <td colspan=2 class="td-value"><?php        echo wordwrap($store_tranf_details->remarks,50,"<br>\n");                   ?></td>
                    </tr>
                </table>
            
                <div class="controls table-controls">
                    <table id="store_transfersTable" class="table items  table-bordered table-condensed sortable_table"
                        style="background:#fff">
                        <thead>
                            <tr>
                                <th>s no</th>
                                <th>Code</th>
                                <th>Product Name</th>

                                <th>request quantity</th>
                                <th>transfer quantity</th>
                                <th>pending quantity</th>
                                <th>batch</th>
                                <th>Expiry</th>
                                <th>cost price</th>
                                <th>selling price</th>
                                <th>Tax</th>
                                <th>gross</th>
                                <th>tax amount</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody class="ui-sortable">
						
						 <?php $i =1; foreach($store_tranf_items as $k => $row) : ?>
                            <tr class="order-item-row warning" data-item="21"><input type="hidden"
                                    name="store_receive_itemid[]" value="23">
                                <td><?=$i?><input type="hidden" name="product_id[]" value="1"></td>
                                <td><?=$row['row']->product_code?></td>
                                <td><?=$row['row']->product_name?></td>
                                <td><?=$row['row']->quantity?></td>
                                <td colspan="11">
								<?php  
                                           $item_details=$this->siteprocurment->getstore_transfer_items_details($row['row']->store_transfer_id,$row['row']->id);
											if(!empty($item_details)){  foreach($item_details as $item){
								?>
                                    <table style="width: 100%;"
                                        class="table items  table-bordered table-condensed batch-table">
                                        <thead>
                                            <tr>
                                                <th>batch</th>
                                                <th>t.qty</th>
                                                <th>received qty</th>
                                                <th>expiry</th>
                                                <th>c.price</th>
                                                <th>s.price</th>
                                                <th>tax</th>
                                                <th>gross</th>
                                                <th>tax amt</th>
                                                <th>total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="batch-row" data-item="21" data-batch="0">
                                              
                                                <td><?php echo   !empty($item->batch)?$item->batch:'No batch';  ?></td>
                                                <td><?php echo $item->transfer_qty;  ?></td>
                                              
                                                <td><?php echo $item->received_qty;  ?></td>
                                                <td><?php echo $item->expiry;  ?></td>
                                                <td><?php echo $item->cost_price;  ?></td>
                                                <td><?php echo $item->selling_price;  ?></td>
                                                <td><?php echo $item->tax;  ?></td>
                                                <td>
												<?php echo $item->gross_amount;  ?>
                                                </td>
												  <td>
												<?php echo $item->tax_amount;  ?>
                                                </td>
												  <td>
												<?php echo $item->net_amount;  ?>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
											<?php  }  } ?>
                                </td>
                            </tr>
							  <?php $i++;endforeach; ?>
                        </tbody>
                        <tfoot>

                        </tfoot>
                    </table>
                    <table class="table total_item_qty_tables" style="padding: 4px;border-top: none!important;width:30%;">
                        <tbody>
                            <tr>
                                <td>
                                    <label for="total_no_items">total no items</label> </td>
                                <td>
                                   <?=$store_tranf_details->total_no_items?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for="total_no_qty">total no qty</label> </td>
                                <td>
                                   <?=$store_tranf_details->total_no_qty?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>

        </div>
        <div class="modal-footer">

        </div>
    </div>

</div>
<?= $modal_js ?>