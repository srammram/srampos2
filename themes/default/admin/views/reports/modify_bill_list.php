<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<style>
 .delete-bill,#delete-bill-label{
  color: red;
 }
 .modify-bills{
      overflow: scroll;
    height: 500px;
 }
</style>
<script>
 $(document).ready(function(){
  $del_amt = 0;
  $('.del-item-g-total').each(function(){
   $val = $(this).val();
   $del_amt +=parseFloat($val);
   
  });
  $('.total-del-amt').text(formatDecimal($del_amt));
 })
 
 
</script>
<div class="modal-dialog modal-lg" style="width:90%">
    <div class="modal-content col-md-12">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><span id="delete-bill-label">*</span>Bill Items to be Deleted</h4>
	    <span>Target Amount : <?=$target_amount?></span>
	    Delete Amount : <span class="total-del-amt"></span>
        </div>
      
      <form method="post" id="modify-bills-form">
        <div class="col-sm-12 modal-body modify-bills">
          <div class="table-responsive">
                    <table id="BillData" class="table table-bordered table-condensed table-hover table-striped">
                        <thead>
                        <tr class="primary">
                            <th><?= lang("recipe_name") ?></th>
			    <th><?=lang("net_unit_price")?></th>
			    <th><?=lang("manual_item_discount")?></th>
			    <th><?=lang("item_discount")?></th>
			    <th><?=lang("off_discount")?></th>
			    <th><?=lang("customer_discount")?></th>
			    <th><?=lang("total_disocunt")?></th>
			    <th><?=lang("total_tax")?></th>
			    <th><?=lang('service_charge')?></th>
			    <th><?= lang("grand_total") ?></th>						
                        </tr>
                        </thead>
                        <tbody>
                        <?php $cnt = 0;foreach($bill_list as $k => $bill) : ?>
			 <tr><td style="font-weight:bold;">Date: <?=date('Y-m-d',strtotime($bill->date))?></td></tr>
			 <tr>
			  <td style="font-weight:bold;">Bill No: <?=$bill->bill_number?>
			  <input type="hidden" name="bill[<?=$cnt?>][bill_id]" value="<?=$bill->id?>"></td>
			  <td style="font-weight:bold;">Total : <?=$this->sma->formatDecimal($bill->total)?></td>
			  <td style="font-weight:bold;">Total Discount : <?=$this->sma->formatDecimal($bill->total_discount)?></td>
			  <td style="font-weight:bold;">Total Tax : <?=$this->sma->formatDecimal($bill->total_tax)?></td>
			  <td style="font-weight:bold;">Service charge : <?=$this->sma->formatDecimal($bill->service_charge_amount)?></td>
			  <td style="font-weight:bold;">Grand Total : <?=$this->sma->formatDecimal($bill->grand_total)?></td>
			 </tr>
			 <?php foreach($bill->del_bill_items as $kk => $item) : ?>
			  <tr class="delete-bill">
			   <td><?=$item->recipe_name?><input type="hidden" name="bill[<?=$cnt?>][del_bill_items][]" value="<?=$item->id?>"></td></td>
			   <td><?=$this->sma->formatDecimal($item->net_unit_price)?></td>
			   <td><?=$this->sma->formatDecimal($item->manual_item_discount)?></td>
			   <td><?=$this->sma->formatDecimal($item->item_discount)?></td>
			   <td><?=$this->sma->formatDecimal($item->off_discount)?></td>
			   <td><?=$this->sma->formatDecimal($item->input_discount)?></td>
			   <td><?=$this->sma->formatDecimal($item->disocunt)?></td>
			   <td><?=$this->sma->formatDecimal($item->tax)?></td>
			   <td><?=$this->sma->formatDecimal($item->service_charge_amount)?></td>
			   <td><input type="hidden" class="del-item-g-total" value="<?=$this->sma->formatDecimal($item->grand_total)?>"><?=$this->sma->formatDecimal($item->grand_total)?></td>
			  </tr>
			 <?php endforeach; ?>
			 <?php foreach($bill->remaining_bill_items as $kk => $item) : ?>
			 
			  <tr class="remaining-bill">
			   <td><?=$item->recipe_name?><input type="hidden" name="bill[<?=$cnt?>][remaining_bill_items][]" value="<?=$item->id?>"></td>
			   <td><?=$this->sma->formatDecimal($item->net_unit_price)?></td>
			   <td><?=$this->sma->formatDecimal($item->manual_item_discount)?></td>
			   <td><?=$this->sma->formatDecimal($item->item_discount)?></td>
			   <td><?=$this->sma->formatDecimal($item->off_discount)?></td>
			   <td><?=$this->sma->formatDecimal($item->input_discount)?></td>
			   <td><?=$this->sma->formatDecimal($item->disocunt)?></td>
			   <td><?=$this->sma->formatDecimal($item->tax)?></td>
			   <td><?=$this->sma->formatDecimal($item->service_charge_amount)?></td>
			   <td><?=$this->sma->formatDecimal($item->grand_total)?></td>
			  </tr>
			 <?php endforeach; ?>
			<?php $cnt++;
			endforeach; ?>
                        </tbody>

                        
                    </table>
                </div>

        </div>
        <div class="modal-footer">
            <button type="button" id="modify-bills" class="btn btn-primary">Modify</button>
        </div>
	</form>
    </div>
    
</div>
<?= $modal_js ?>