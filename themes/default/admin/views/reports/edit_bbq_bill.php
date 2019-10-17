<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('edit_bill'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
	   
            <div class="col-lg-12">             

                <?php
		$attrib = array( 'role' => 'form', 'id' => 'recipe_form');
                echo admin_form_open_multipart("reports/edit_dontprint/".$bill->bill_number, $attrib)
                ?>
		<div class="col-md-12">
		    
		    <table id="BillEdit"
                           class="table table-bordered table-hover table-striped table-condensed reports-table">
			<thead>
			    
			    <th><?=lang('details')?></th>
			    <th><?=lang('unit_price')?></th>
			    
			    <th><?=lang('covers')?></th>
			    <th><?=lang('change_covers')?></th>
			    <th><?=lang('price')?></th>
			    <th><?=lang('discount')?></th>
			    <th><?=lang('bbq_discount')?></th>
			    <th><?=lang('tax')?></th>
			    <th><?=lang('subtotal')?></th>
			    
			</thead>
			<tbody> <?php //echo '<pre>';print_r($bill);?>
			   
			    <?php
			    $subtotal = 0;$bbqtax=0;$bbqdiscount=0;$bbq_grandtotal=0;
			    foreach($bill->bbq_bill_items as $i => $cover) :
			    $discount = $cover->discount;
			    $subtotal +=$cover->subtotal;
			    $bbqtax +=$cover->tax;
			    $bbqdiscount +=$cover->discount;
			    
			    $bbq_grandtotal +=$cover->subtotal-$cover->discount+$cover->tax;
			    ?>
			    
			    <tr class="cover-details cover-<?=$cover->id?>" data-item = "<?=$cover->id?>">
				<td class="cover-type-container">
				    <span class="cover-type-label"><?=$cover->type?></span>
				    <input type="hidden" name="cover_type[]" class="cover-type" value="<?=$cover->type?>">
				</td>
				<td class="cover-unitprice-container">
				    <?php $unit_price = ($cover->price==0)?0:$cover->price; ?>
				    <span class="cover-unitprice-label"><?=$unit_price?></span>
				    <input type="hidden" name="cover_unitprice[]" class="cover-unitprice" value="<?=$unit_price?>">
				</td>
				
				<td>
				    <span class="cover-count-label"><?=$cover->cover?></span>
				</td>
				<td class="cover-count-container">
				    <div class="qty_number"><span class="minus ">-</span><input class="form-control change-qty input-sm text-center item-qty" data-id ="<?=$cover->id?>"  name="cover_count[]" type="text" value="<?=$cover->cover?>"><span class="plus ">+</span></div>
				</td>
				<td class="cover-price-container">
				    <span class="cover-price-label"><?=$cover->price?></span>
				    <input type="hidden" name="cover_price[]" class="cover-price" value="<?=$cover->price?>">
				</td>
				<td class="cover-discount-container">
				    <span class="cover-discount-label"><?=$cover->discount_cover?></span>
				    <input type="hidden" name="cover_discount[]" class="cover-discount" value="<?=$cover->discount_cover?>">
				</td>
				<td class="cover-bbqdiscount-container">				
				    <span class="cover-bbqdiscount-label"><?=$cover->discount?></span>
				    <input type="hidden" name="cover_bbqdiscount[]" class="cover-bbqdiscount" value="<?=$cover->discount?>">
				</td>
				<td class="cover-tax-container">				
				    <span class="cover-tax-label"><?=$cover->tax?></span>
				    <input type="hidden" name="cover_tax[]" class="cover-tax" value="<?=$cover->tax?>">
				</td>
				
				<td class="cover-subtotal-container">				
				    <span class="cover-subtotal-label"><?=$cover->subtotal?></span>
				    <input type="hidden" name="cover_subtotal[]" class="cover-subtotal" value="<?=$cover->subtotal?>">
				</td>
				
				
			    </tr>
			    <?php endforeach; ?>
			 
			 </tbody>
			 <tfoot>
			 <tr>
			    <td colspan=8 style="text-align:right">Total</td>
			    <td>
				<span class="bbq-subtotal"><?=$subtotal?></span>
				<input type="hidden" name="bbq_subtotal[]" class="bbq-subtotal" value="<?=$subtotal?>">
			    </td>
			 </tr>
			 <tr>
			    <td colspan=7 style="text-align:right">BBQ discount</td>
			   
			    <td>
			    <?php $order_discount = ($bill->order_discount_id=='')?0:$bill->order_discount_id; ?>
							
				<input type="text" name="bbqdiscount_val[]" class="bbqdiscount-val" value="<?=$order_discount?>">
				
			    </td>
			    <td>
				<span class="bbq-discount"><?=$bbqdiscount?></span>
				<input type="hidden" name="bbqdiscount[]" class="bbqdiscount" value="<?=$bbqdiscount?>">
			    </td>
			 </tr>
			 <tr>
			    <td colspan=8 style="text-align:right">Tax <?=$bill_tax->name?></td>
			    <td>
				<span class="bbqtax-label"><?=$bbqtax?></span>
				<?php $bbq_tax_val = ($bill->order_discount_id=='')?0:$bill->order_discount_id; ?>
				<input type="hidden" name="bill_tax_val[]" class="bbq-tax-val" value="<?=$bill_tax->rate?>%">
				<input type="hidden" name="bill_tax[]" class="bbq-tax" value="<?=$bbqtax?>">
			    </td>
			 </tr>
			 <tr>
			    <td colspan=8 style="text-align: right;"><?=lang('grand_total')?></td>
			    <td>
				<span class="bbq-grandtotal"><?=$bbq_grandtotal?></span>
				<input type="hidden" name="bbq_grandtotal[]" class="bbq-grandtotal" value="<?=$bbq_grandtotal?>">
			    </td>
			 </tr>
			 </tfoot>
		    </table> 
                </div>
               
	       <div class="col-md-6" style="float: right;">
		    <div><label><?=lang('Edit_payment')?></label></div>
		    <table id="BillPaymentEdit"
                           class="table table-bordered table-hover table-striped table-condensed reports-table">
			<thead>
			    <th><?=lang('paid_by')?></th>
			    <th><?=lang('amount')?></th>
			    <th><?=lang('paid')?></th>
			    <th><?=lang('balance')?></th>
			    <th><?=lang('amount_exchange')?></th>
			</thead>
			<tbody>
			    <?php foreach($payments as $p => $payment) : ?>
			    <tr>
				<td>
				    <span><?=$payment->paid_by?></span>
				    <input type="hidden" name="payment[<?=$p?>][paid_by]" value="<?=$payment->paid_by?>" class="paid_by">
				    <input type="hidden" name="payment[<?=$p?>][id]" value="<?=$payment->id?>" class="p-id">
				</td>
				<td>
				    
				    <input type="text" name="payment[<?=$p?>][amount]" value="<?=$this->sma->formatDecimal($payment->amount)?>" class="p-amount">
				</td>
				<td>				   
				    <input type="text" name="payment[<?=$p?>][paid]" value="<?=$this->sma->formatDecimal($payment->pos_paid)?>" class="p-paid">
				</td>
				<td>				   
				    <input type="text" name="payment[<?=$p?>][balance]" value="<?=$this->sma->formatDecimal($payment->pos_balance)?>" class="p-balance">
				</td>
				<td>
				    <input type="text" name="payment[<?=$p?>][amount_exchange]" value="<?=$this->sma->formatDecimal($payment->amount_exchange)?>" class="p-amount-exchange">
				</td>
			    </tr>
			    <?php endforeach; ?>
			    
			</tbody>
		    </table>
	       </div>
		<div class="col-md-12">
		    <div class="col-md-12">
			<div class="pull-right form-group">			    
			    <?php echo form_submit('bill_generate', $this->lang->line("generate"), 'class="bill_generate btn btn-primary"'); ?>
			</div>
		    </div>
                </div>
                
                <?= form_close(); ?>

            </div>

        </div>
    </div>
</div>
<script>
    $('.bill_generate').on('click',function(e){
	e.preventDefault();
	$p_amt = 0;
	$('.p-amount').each(function(n,v){
	    $this = $(this);
	    $p_amt += parseFloat($this.val());
	});
	$g_total = $('.bill-grandtotal').val();
	$p_amt = formatDecimal($p_amt);
	//console.log($g_total+'--'+$p_amt);
	if ($g_total<$p_amt || $g_total>$p_amt) {
	   alert('payment amount should be equal to grandtotal');
	   return false;
	}
	$('.bill_generate').closest('form').submit();
    })
    $(document).ready(function(){
	$(document).on('click','.cover-details:not(.deleted-item) .minus',function(){        
	    $cnt = parseInt($(this).closest('.qty_number').find('.item-qty').val()) - parseInt(1);
	    if ($cnt==0) {
		return false;
	    }
	    $(this).closest('.qty_number').find('.item-qty').val($cnt);
	    $(this).closest('.qty_number').find('.rquantity').trigger('change');
	    $itemID = $(this).closest('.cover-details').attr('data-item');
	    
	    calculateCover($itemID);	
	});
	$(document).on('click','.cover-details:not(.deleted-item) .plus',function(){	
	    $cnt = parseInt($(this).closest('.qty_number').find('.item-qty').val()) + parseInt(1);
	    $(this).closest('.qty_number').find('.item-qty').val($cnt);
	    $(this).closest('.qty_number').find('.rquantity').trigger('change');
	    $itemID = $(this).closest('.cover-details').attr('data-item');
	    calculateCover($itemID);
	});
    });
    $('.item-qty').on('change',function(){
	$itemID = $(this).closest('.cover-details').attr('data-item');
	calculateCover($itemID);	
    });
    
    
    
    function calculateCover($itemID) {
	$('.cover-details.cover-'+$itemID).each(function(){
	    $this = $(this);
	   
	    //alert($this.attr('class'));
	    $cover_price = $this.find('.cover-price').val();
	    $cover_unitprice = $this.find('.cover-unitprice').val();
	    //alert($unit_price)
	    $cover_qty = $this.find('.item-qty').val();
	    $net_unit_price = $cover_unitprice * $cover_qty;
	    
	    $discount_val = $this.find('.cover-discount').val();
	    if ($discount_val=='') {$discount_val = 0;}
	    
	    if ($discount_val.indexOf("%")!=-1) {
		console.log('wrong')
		$DP = $discount_val.replace('%','');
		$D = $net_unit_price *($DP/100);
		$afterD = $net_unit_price - $D;
	    }else{
		$D = $discount_val;
		console.log($net_unit_price +'-'+ $D)
		$afterD = $net_unit_price - $D;
	    }
	   
	    $bbq_discount_val = $('.bbqdiscount').val();
	    if ($bbq_discount_val=='') {$bbq_discount_val = 0;}
	    
	    if ($bbq_discount_val.indexOf("%")!=-1) {
		$BBQDP = $bbq_discount_val.replace('%','');
		$BD = $afterD *($BBQDP/100);
		$afterBBQD = $afterD - $BD;
	    }else{
		$BD = $bbq_discount_val;
		console.log($afterD +'-'+ $BD)
		$afterBBQD = $afterD - $BD;
	    }
	    
	    $tax_val = $('.bbq-tax-val').val();
	    $cover_tax = 0;
	    
	    //alert($afterBBQD)
	    if ($tax_val.indexOf("%")!=-1) {
		$TP = $tax_val.replace('%','');
		$cover_tax = $afterBBQD * ($TP/100);alert($cover_tax);
		$afterTax = $afterBBQD + $cover_tax;
	    }else{
		$afterTax = $afterBBQD + $cover_tax;
	    }
	    
	    
	    $coverSubtotal = $afterTax;
	    $this.find('.cover-price-label').text(formatDecimal($net_unit_price));
	    $this.find('.cover-subtotal-label').text(formatDecimal($coverSubtotal));
	    $this.find('.cover-tax-label').text(formatDecimal($cover_tax));
	    $this.find('.cover-price').val(formatDecimal($net_unit_price));
	    $this.find('.cover-tax').val(formatDecimal($cover_tax));
	    $this.find('.cover-subtotal').val(formatDecimal($coverSubtotal));
	    
	    
	    //calculateBill();
	});
    }
    function calculateBill(){
	$net_total = 0;
	$total_item_dis = 0;
	$total_offer_dis = 0;
	$total_customer_dis = 0;
	$sub_total = 0;
	$total_tax = 0;
	$grand_total = 0;
	$total_items = 0;
	$total_manual_dis =0;
	$total_discount=0;
	$total_dis_after_ID=0;
	$('.cover-details:not(.deleted-item)').each(function(){
	    $this = $(this);
	    $total_items +=1;
	    //alert($(this).attr('data-item'));return false;
	    $itemtax = parseFloat($this.find('.item-tax').val());
	    $item_subtotal = parseFloat($this.find('.item-subtotal').val());
	    $manual_dis = parseFloat($this.find('.manual-discount').val());
	    $item_dis = parseFloat($this.find('.item-discount').val());
	    $offer_dis = parseFloat($this.find('.offer-discount').val());
	    $customer_dis =parseFloat($this.find('.customer-discount').val());
	    $discount = $item_dis+$offer_dis+$customer_dis;
	    $item_total_discount = $discount+$manual_dis;
	    $item_total =$item_subtotal-$discount;
	    
	    $total_dis_after_ID += $item_subtotal-$manual_dis-$item_dis;
	    $net_total +=parseFloat($item_subtotal);
	    $total_manual_dis +=parseFloat($manual_dis);
	    $total_item_dis +=parseFloat($item_dis);
	    $total_offer_dis +=parseFloat($offer_dis);
	    $total_customer_dis +=parseFloat($customer_dis);   
	    $sub_total +=$item_total;
	    $total_tax +=$itemtax;	    
	    $grand_total+=$item_total+$itemtax;
	    $total_discount +=$item_total_discount;
	});
	$('.bill-net-total').val(formatDecimal($net_total));
	$('.bill-item-discount').val(formatDecimal($total_item_dis));
	$('.bill-offer-discount').val(formatDecimal($total_offer_dis));
	$('.bill-customer-discount').val(formatDecimal($total_customer_dis));
	$('.bill-subtotal').val(formatDecimal($sub_total));
	$('.bill-tax').val(formatDecimal($total_tax));
	$('.bill-grandtotal').val(formatDecimal($grand_total));
	$('.bill-total-items').val($total_items);
	$('.bill-total-discount').val($total_discount);
	$('.bill-manual-item-discount').val($total_manual_dis);
	
	
	$('.bill-net-total-label').text(formatDecimal($net_total));
	$('.bill-item-discount-label').text(formatDecimal($total_item_dis));
	$('.bill-offer-discount-label').text(formatDecimal($total_offer_dis));
	$('.bill-customer-discount-label').text(formatDecimal($total_customer_dis));
	$('.bill-subtotal-label').text(formatDecimal($sub_total));
	$('.bill-tax-label').text(formatDecimal($total_tax));
	$('.bill-grandtotal-label').text(formatDecimal($grand_total));
	$('.bill-total-after-ID').text(formatDecimal($total_dis_after_ID));
	
    }
</script>
<style>
    .deleted-item{
	opacity:0.4;
    }
    #BillPaymentEdit input{
	width:85px !important;
    }
    .manual-dis-val{
	width:60px !important;
	text-align: center;
    }
    .qty_number .item-qty{
	margin: -2px;
	border: none;
	background: none;
    }
</style>

