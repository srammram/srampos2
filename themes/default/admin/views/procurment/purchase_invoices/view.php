<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<style>
     .td-value{
        font-weight: bold;
     }
</style>
<div class="modal-dialog modal-lg" style="width: 1259px !important;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?=lang('purchase_invoice')?> <?=$orders->reference_no?></h4>
        </div>
      
      
        <div class="modal-body">
          <div class="table-responsive">
            <table class="custom_tables">
                <tr>
                    <td colspan=2 ><?=lang('date')?> : </td>
                    <td colspan=2 class="td-value"><?=$orders->date?></td>
                    
                    <td colspan=2 ><?=lang('purchase_no')?> : </td>
                    <td colspan=2 class="td-value"><?=$orders->reference_no?></td>
                    
                    <td colspan=2 ><?=lang('currency')?>: </td>
                    
                    <?php $currency = '';foreach($currencies as $k => $row) :
                    if($orders->currency==$row->id){
                        $currency = $row->code;
                    }
                    endforeach; ?>
                    <td colspan=2 class="td-value"><?=$currency?></td>
                </tr>
                <tr>   
                    <?php
                        $supplier = '';
                        $supplier_address = '';
                        foreach ($suppliers as $supplier) {                                             
                            if($orders->supplier_id == $supplier->id ){
                               $supplier_address = $supplier->address.','.$supplier->city.','.$supplier->state;
                               $supplier = $supplier->name;                                
                               break;
                            }                            
                        }    
                        ?>
                    <td colspan=2 ><?=lang('supplier')?>: </td>
                    <td colspan=2 class="td-value"><?=$supplier?></td>
                    
                    <td colspan=2 ><?=lang('supplier_address')?>: </td>
                    <td colspan=2 class="td-value"><?=$supplier_address?></td>
                    
                    <td colspan=2 ><?=lang('status')?>: </td>
                    <td colspan=2  class="td-value"><?=$orders->status?></td>
                </tr>
                <tr>
                    
                    
                    <td colspan=2 ><?=lang('po_no')?>: </td>
                    <td colspan=2  class="td-value"><?=$orders->po_no?></td>
                    
                    <td colspan=2 ><?=lang('po_date')?>: </td>
                    <td colspan=2  class="td-value"><?=$orders->po_date?></td>
                    <?php $tax_method = ($orders->tax_method==1)?'Exclusive':'Inclusive'; ?>
                    <td colspan=2 ><?=lang('tax_type')?>: </td>
                    
                    <td colspan=2  class="td-value"><?=$tax_method?></td>
                </tr>
                
                <tr>
                    <td colspan=2 ><?=lang('shipping_charge')?>: </td>
                    <td colspan=2  class="td-value"><?=$orders->shipping?></td>
                    
                    <td colspan=2 ><?=lang('round_off')?>: </td>
                    <td colspan=2  class="td-value"><?=$orders->round_off?></td>
                    
                    
                </tr>
                <tr>
                    <td colspan=2 ><?=lang('bill_disc')?>: </td>
                    <td colspan=2  class="td-value"><?=$orders->bill_disc?></td>
                    
                    <td colspan=2 ><?=lang('bill_disc_amount')?>: </td>
                    <td colspan=2  class="td-value"><?=$orders->bill_disc_val?></td>
                    <td colspan=2 ><?=lang('net_amount')?>: </td>
                    <td colspan=2  class="td-value"><?=$orders->grand_total?></td>
                </tr>
            </table>
                    <table id="ItemData" class="table table-bordered table-condensed table-hover table-striped">
                        <thead>
                        <tr>
                                            <th ><?= lang('s.no'); ?></th>
                                            <th><?= lang('code'); ?></th>
                                            <th ><?= lang("description"); ?></th>
                                            <th ><?= lang("qty"); ?></th>
                                            <th ><?= lang("batch"); ?></th>
                                            <th ><?= lang("expiry_date"); ?></th>
                                            <th ><?= lang("cost_price"); ?></th>
                                            <th ><?= lang("gross"); ?></th>
                                            <th ><?= lang("item_dis"); ?></th>
                                            <th ><?= lang("item_dis_amt"); ?></th>
                                            <th ><?= lang("subtotal"); ?></th>
                                            <th ><?= lang("bill_disc"); ?></th>
                                            <th ><?= lang("subtotal"); ?></th>
                                            <th ><?= lang("tax_%"); ?></th>
                                            <th ><?= lang("tax_amt"); ?></th>
                                            <th ><?= lang("landing_cost"); ?></th>
                                            <th ><?= lang("selling_price"); ?></th>
                                            <th ><?= lang("margin_%"); ?></th>
                                            <th class="col-md-1"><?= lang("net_amt"); ?></th>
                                        </tr>
                        </thead>
                        <tbody>
                        <?php $i =1; foreach($po_order_items as $k => $row) : ?>
                        <tr>
                            <td><?=$i?></td>
                            <td><?=$row['row']->product_code?></td>
                            <td><?=$row['row']->product_name?></td>
                            <td><?=$row['row']->quantity?></td>
                            <td><?=$row['row']->batch_no?></td>
                            <td><?=$row['row']->expiry?> <?=$row['row']->expiry_type?></td>
                            <td><?=$row['row']->cost?></td>
                            <td><?=$row['row']->gross?></td>
                            <td><?=$row['row']->item_disc?></td>
                            <td><?=$row['row']->item_disc_amt?></td>
                            <td><?=$row['row']->total?></td>
                            <td><?=$row['row']->item_bill_disc_amt?></td>
                            <td><?=$row['row']->total?></td>
                            <td><?=$row['row']->tax_rate?></td>
                            <td><?=$row['row']->tax?></td>
                            <td><?=$row['row']->landing_cost?></td>
                            <td><?=$row['row']->selling_price?></td>
                            <td><?=$row['row']->margin?></td>
                            <td><?=$row['row']->net_amt?></td>
                        </tr>
                        <?php $i++;endforeach; ?>
                        </tbody>
                    </table>
                    <table class="custom_tables" style="width:100%">
                        <tr>
                            <td><?=lang('total_no_items')?></td>
                            <td class="td-value"><?=$orders->no_of_items?></td>
                            <td colspan=5 class="pull-right"><?=lang('gross')?></td>
                            <td class="td-value"><?=$orders->total?></td>
                        </tr>
                        <tr>                            
                            <td><?=lang('total_no_qty')?></td>
                            <td class="td-value"><?=$orders->no_of_qty?></td>
                            <td colspan=5 class="pull-right"><?=lang('item_disc')?></td>
                            <td class="td-value"><?=$orders->item_discount?></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td colspan=5 class="pull-right"><?=lang('bill_disc')?></td>
                            <td class="td-value"><?=$orders->bill_disc_val?></td>
                        </tr>
                        <tr>                            
                            <td></td>
                            <td></td>
                            <td colspan=5 class="pull-right"><?=lang('subtotal')?></td>
                            <td class="td-value"><?=$orders->sub_total?></td>
                        </tr>
                        <tr>                            
                            <td></td>
                            <td></td>
                            <td colspan=5 class="pull-right"><?=lang('tax')?></td>
                            <td class="td-value"><?=$orders->total_tax?></td>
                        </tr>
                        <tr>                            
                            <td></td>
                            <td></td>
                            <td colspan=5 class="pull-right"><?=lang('shipping_charge')?></td>
                            <td class="td-value"><?=$orders->shipping?></td>
                        </tr>
                        <tr>                            
                            <td></td>
                            <td></td>
                            <td colspan=5 class="pull-right"><?=lang('round_off')?></td>
                            <td class="td-value"><?=$orders->round_off?></td>
                        </tr>
                        <tr>                            
                            <td></td>
                            <td></td>
                            <td colspan=5 class="pull-right"><?=lang('new_amt')?></td>
                            <td class="td-value"><?=$orders->grand_total?></td>
                        </tr>
                    </table>
                </div>

        </div>
        <div class="modal-footer">
            
        </div>
    </div>
    
</div>
<?= $modal_js ?>