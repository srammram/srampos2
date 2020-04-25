<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<style>
     .td-value{
        font-weight: bold;
     }
	 select::-ms-expand
    {
        display: none;
    }
	
	select {
    -moz-appearance: none;
    -webkit-appearance: none;
    appearance: none;
    text-indent: 0.1;
    text-overflow: '';
}
</style>
<div class="modal-dialog modal-lg" style="width: 1011px !important;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?=lang('purchase_order')?> <?=$orders->reference_no?></h4>
        </div>
      
      <?php /*  echo '<pre>';
	 print_r($orders);
	 die;  */
	  ?>
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
				<?php
					/*  sl[""] = "";
					foreach ($suppliers as $supplier) {
						echo $sl[$supplier->id] = $supplier->name;
					}  */
				?>
                <tr>   
                    <td colspan=2 ><?=lang('supplier')?>: </td>
                    <td colspan=2 class="td-value"><?php

						// $sl[""] = "";
                        $supplier = '';
						foreach ($suppliers as $supplier) {                            
                            if($orders->supplier_id == $supplier->id ){
                               $supplier = $supplier->name; 
                               break;
                            }
                            
							// $sl[$supplier->id] = $supplier->name;
						}
                        echo $supplier;
						// echo form_dropdown('supplier', $sl, (isset($orders->supplier_id) ? $orders->supplier_id : 0), 'id="po_supplier" data-placeholder="' . $this->lang->line("select") . ' ' . $this->lang->line("supplier") . '"  class="form-control input-tip select" disabled style="width:100%; border:none; background-color:transparent; box-shadow:none; font-weight:bold; color: #333; padding-right:200px;"');
						?></td>
                    
                    <td colspan=2 ><?=lang('supplier_address')?> : </td>                    
                    <td colspan=2 class="td-value"><?=$orders->supplier_address?></td>
                    
                    <td colspan=2 ><?=lang('status')?> : </td>
                    <td colspan=2  class="td-value"><?=$orders->status?></td>
                </tr>
                <tr>
                    
                    
                    <td colspan=2 ><?=lang('quotation_no')?>: </td>
                    <td colspan=2  class="td-value"><?=$orders->req_reference_no?></td>
                    
                    <td colspan=2 ><?=lang('quotation_date')?> : </td>
                    <td colspan=2  class="td-value"><?=$orders->quotation_date?></td>
                    <?php $tax_method = ($orders->tax_method==1)?'Exclusive':'Inclusive'; ?>
                    <td colspan=2 ><?=lang('tax_type')?>: </td>
                    
                    <td colspan=2  class="td-value"><?=$tax_method?></td>
                </tr>
                
                <tr>
                    <td colspan=2 ><?=lang('shipping_charge')?> : </td>
                    <td colspan=2  class="td-value"><?=$orders->shipping?></td>
                    
                    <td colspan=2 ><?=lang('round_off')?> : </td>
                    <td colspan=2  class="td-value"><?=$orders->round_off?></td>
                    
                    
                </tr>
                <tr>
                    <td colspan=2 ><?=lang('bill_disc')?> : </td>
                    <td colspan=2  class="td-value"><?=$orders->bill_disc?></td>
                    
                    <td colspan=2 ><?=lang('bill_disc_amount')?> : </td>
                    <td colspan=2  class="td-value"><?=$orders->bill_disc_val?></td>
                    <td colspan=2 ><?=lang('net_amount')?> : </td>
                    <td colspan=2  class="td-value"><?=$orders->grand_total?></td>
                </tr>
            </table>
			<div class="table-responsives">
                    <table id="ItemData" class="table table-bordered table-condensed table-hover table-striped">
                        <thead>
                        <tr>
                                            <th ><?= lang('s.no'); ?></th>
                                            <th><?= lang('code'); ?></th>
                                            <th ><?= lang("description"); ?></th>
                                            <th ><?= lang("qty"); ?></th>
                                            <th ><?= lang("uom"); ?></th>
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
                            <td><?=$row['row']->unit_name?></td>
                            <td><?=$row['row']->cost?></td>
                            <td><?=$row['row']->gross?></td>
                            <td><?=$row['row']->item_disc?></td>
                            <td><?=$row['row']->item_disc_amt?></td>
                            <td><?=$row['row']->total?></td>
                            <td><?=$row['row']->item_bill_disc_amt?></td>
                            <td><?=$row['row']->total?></td>
                            <td><?=$row['row']->tax_rate?></td>
                            <td><?=$row['row']->item_tax?></td>
                            <td><?=$row['row']->landing_cost?></td>
                            <td><?=$row['row']->selling_price?></td>
                            <td><?=$row['row']->margin?></td>
                            <td><?=$row['row']->net_amt?></td>
                        </tr>
                        <?php $i++;endforeach; ?>
                        </tbody>
                    </table>
					</div>
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
                            <td colspan=5 class="pull-right"><?=lang('net_amt')?></td>
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