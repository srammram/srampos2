<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('edit_bill'); ?> - <?=$bill->bill_number?> - <?=date('Y-m-d',strtotime($bill->date))?></h2>
    </div>
    <div class="box-content">
        <div class="row">
	   
            <div class="col-lg-12">             

                <?php
		$attrib = array( 'role' => 'form', 'id' => 'recipe_form');
                echo admin_form_open_multipart("reports/edit_dontprint/".$bill->id, $attrib)
                ?>
		<div class="col-md-12">
		    
		    <table id="BillEdit"
                           class="table table-bordered table-hover table-striped table-condensed reports-table">
			<thead>
			    <th><?=lang('remove')?></th>
			    <th><?=lang('item_name')?></th>
			    <th><?=lang('ordered_qty')?></th>
			    <th><?=lang('quantity')?></th>
			    <th><?=lang('unit_price')?></th>
			    <th><?=lang('net_unit_price')?></th>
			    <th><?=lang('manual_discount')?></th>
			    <th><?=lang('edit_manual_discount')?></th>
			    <th><?=lang('item_discount')?></th>
			    <th><?=lang('offer_discount')?></th>
			    <th><?=lang('customer_discount')?></th>
			    <th><?=lang('discount')?></th>
			    <th><?=lang('tax')?></th>			    
			    <th><?=lang('subtotal')?></th>
			    
			</thead>
			<tbody> <?php //echo '<pre>';print_r($bill);?>
			   
			    <?php
			    $total_net_unit_price = 0;
			    $total_customer_discount =0;
			    $total_item_discount=0;
			    $total_offer_discount=0;
			    $total_manual_discount=0;
			    $net_total = 0;
			    $total_discount =0;
			    $total_tax = 0;
			    $service_charge = 0;
			    foreach($bill->bill_items as $i => $items) :
			    $discount = $items->manual_item_discount + $items->item_discount +$items->off_discount + $items->input_discount ;
			    $total_customer_discount += $items->input_discount ;
			    $total_item_discount += $items->item_discount;
			    $total_offer_discount += $items->off_discount;
			    $total_manual_discount +=$items->manual_item_discount;
			    $price_afterMD = $items->net_unit_price-$items->manual_item_discount;
			    //echo $items->subtotal;
			    $net_total += $items->subtotal;
			    $total_discount += $discount;
			    //echo $items->tax;
			    $total_tax += $items->tax;
			    $service_charge += $items->service_charge_amount;
			    //echo $items->net_unit_price.'---';
			    $total_net_unit_price +=$items->net_unit_price;
			    ?>
			    <tr class="item-details item-<?=$items->id?> recipeid-<?=$items->recipe_id?>" data-item = "<?=$items->id?>">
			    <td style="text-align: center;">
				    <a href="" class="remove-item"><i class="fa fa-trash" aria-hidden="true"></i></a>
				</td>
				<td>
				    <?=$items->recipe_name?>
				    <input type="hidden" name="item_bill_id[]" class="item-bill-id" value="<?=$items->bil_id?>">
				    <input type="hidden" name="item_tax_type[]" class="item-tax-type" value="<?=$items->tax_type?>">
				    <input type="hidden" name="item_warehouse[]" class="item-warehouse" value="<?=$items->warehouse_id?>">
				    <input type="hidden" name="recipe_id[]" class="recipe-id" value="<?=$items->recipe_id?>">
				    <input type="hidden" name="recipe_code[]" class="recipe-code" value="<?=$items->recipe_code?>">
				    <input type="hidden" name="recipe_name[]" class="recipe-name" value="<?=$items->recipe_name?>">
				    <input type="hidden" name="recipe_type[]" class="recipe-type" value="<?=$items->recipe_type?>">
				    <input type="hidden" name="order_item_id[]" class="sale-item-id" value="<?=$items->sale_item_id?>">
				    <input type="hidden" name="order_id[]" class="sale-item-order-id" value="<?=$items->order_id?>">
				</td>
				<td><?=$items->quantity?>
				    <input type="hidden" name="ordered_quantity[]" value="<?=$items->quantity?>">
				</td>
				<td><div class="qty_number"><span class="minus ">-</span><input class="form-control change-qty input-sm text-center item-qty" data-id ="<?=$items->id?>"  name="quantity[]" type="text" value="<?=$items->quantity?>"><span class="plus ">+</span></div></td>
				<td class="unit-price-container">
				    <span class="unit-price-label"><?=$this->sma->formatDecimal($items->unit_price)?></span>
				    <input type="hidden" name="unit_price[]" class="unit-price" value="<?=$this->sma->formatDecimal($items->unit_price)?>">
				</td>
				<td class="net-unit-price-container">
				    <span class="net-unit-price-label"><?=$this->sma->formatDecimal($items->net_unit_price)?></span>
				    <input type="hidden" name="net_unit_price[]" class="net-unit-price" value="<?=$this->sma->formatDecimal($items->net_unit_price)?>">
				</td>
				<td><span class="manual-discount-label"><?=$items->manual_item_discount?></span></td>
				<td>
				    <input type="text" name="manual_discount_val[]" class="manual-dis-val" value="<?=$items->manual_item_discount_val?>">
				    <input type="hidden" name="manual_discount[]" class="manual-discount" value="<?=$items->manual_item_discount?>">
				</td>
				<td class="item-discount-container">
				    <?php
					$item_dis = $this->site->getBillItemDiscount($items->discount);
					
				    ?>
				    <span class="item-discount-label"><?=$this->sma->formatDecimal($items->item_discount)?></span>
				    <?php $item_dis = ($item_dis=='')?0:$item_dis;?>
				    <input type="hidden" class="item-dis-val" value="<?=$item_dis?>">
				     <input type="hidden" name="item_discount_id[]" class="" value="<?=$items->discount?>">
				    
				    <input type="hidden" name="item_discount[]" class="item-discount" value="<?=$this->sma->formatDecimal($items->item_discount)?>">
 				</td>
				<td class="offer-discount-container">
				    <span class="offer-discount-label"><?=$this->sma->formatDecimal($items->off_discount)?></span>
				    <!--<input type="hidden" class="offer-dis-val" value="10%">-->
				    <input type="hidden" name="offer_discount[]" class="offer-discount" value="<?=$this->sma->formatDecimal($items->off_discount)?>">
				</td>
				<td class="customer-discount-container">
				    <span class="customer-discount-label"><?=$this->sma->formatDecimal($items->input_discount)?></span>
				    <input type="hidden" class="customer-dis-val" value="0">
				    <input type="hidden" name="customer_discount[]" class="customer-discount" value="<?=$this->sma->formatDecimal($items->input_discount)?>">
				</td>
				<td class="total-discount-container">
				    <span class="total-discount-label"><?=$this->sma->formatDecimal($discount)?></span>
				    <input type="hidden" name="total_discount[]" class="total-discount" value="<?=$this->sma->formatDecimal($discount)?>">
				</td>
				<td class="item-tax-container">
				    <span class="item-tax-label"><?=$this->sma->formatDecimal($items->tax)?></span>
				    <?php $tax_type = ($bill->tax_type==1)?'tax-exclusive':'tax-inclusive';?>
				    <input type="hidden" name="item_tax[]" class="item-tax" tax-type="<?=$tax_type?>" value="<?=$this->sma->formatDecimal($items->tax)?>">
				    <input type="hidden" name="item_sc[]" class="item-sc" value="<?=$this->sma->formatDecimal($items->service_charge_amount)?>">
				</td>
				<td class="item-subtotal-container">				    
				    <span class="item-subtotal-label"><?=$this->sma->formatDecimal($items->subtotal)?></span>
				    <input type="hidden" name="item_subtotal[]" class="item-subtotal" value="<?=$this->sma->formatDecimal($items->subtotal)?>">
				</td>
				
				
			    </tr>
			    <?php endforeach; ?>
			  <tfoot>
			    <tr>
				<td colspan=14>
				    <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;    width: 100%;    float: right;">
                                            <a href="javascript:void(0)" id="add-item" class="btn btn-primary" style="float: right;"><i
                                                    class="fa fa-2x fa-plus addIcon"  style="font-size: 15px;" id="addIcon"></i><span style="font-size: 15px;">Add Items</span></a></div>
                                    </div>
				</td>
			    </tr>
			    <tr style="display: none;">
				<td colspan=2 style="text-align:right"><?=$bill->total_items?></td>
				<td colspan=5 style="text-align:right"><?=$total_manual_discount?></td>
				<td style="text-align:right"><?=$total_item_discount?></td>
				<td style="text-align:right"><?=$total_offer_discount?></td>
				<td style="text-align:right"><?=$total_customer_discount?></td>
				<td style="text-align:right"><?=$bill->total_discount?></td>
				<td style="text-align:right"><?=$bill->total_tax?></td>
				<td style="text-align:right"><?=$bill->total?></td>
				<td></td>
			    </tr>
			    <tr style="display: none;">
				<td colspan=13 style="text-align:right">Total-discount+tax = <?=$bill->grand_total?></td>
			    </tr>
			    <tr>
				<td colspan="13" style="text-align: right;"><?=lang('net_total')?></td>
				<td>
				    <span class="bill-net-total-label"><?=$net_total?></span>
				    <input type="hidden" name="bill_net_total" value="<?=$net_total?>" class="bill-net-total">
				</td>
			    </tr>
			    <tr>
				<td colspan="12" style="text-align: right;"><?=lang('discount')?></td>
				<td>
				    <span class="bill-item-discount-label"><?=$total_item_discount?></span>
				    <input type="hidden" name="bill_item_discount" value="<?=$total_item_discount?>" class="bill-item-discount">
				</td>
				<td><span class="bill-total-after-ID"><?=$net_total-$total_item_discount?></span></td>				
			    </tr>
			    <tr>
				<td colspan="12" style="text-align: right;"><?=lang('offer_discount')?></td>
				<td><span class="bill-offer-discount-val-label"><?=@$off_dis?><?=@$bill->offer_discount_val?></span></td>
				<td>
				    <span class="bill-offer-discount-label"><?=$total_offer_discount?></span>
				    <?php $off_dis = ($off_dis=='')?0:$off_dis;?>
				    <input type="hidden" name="bill_offer_discount_val" value="<?=$off_dis?>" class="bill-offer-discount-val">
				    <input type="hidden" name="bill_offer_discount" value="<?=$total_offer_discount?>" class="bill-offer-discount">	     </td>
			    </tr>
			    <tr>
				<td colspan="12" style="text-align: right;"><?=lang('customer_discount')?></td>
				<td>
				    <select name="bill_customer_discount_id" class="all-customer-discounts">
					<option value=0>No Discount</option>
					<?php foreach($customer_discount as $c => $c_dis) : ?>
					<option value="<?=$c_dis->id?>" <?php if($c_dis->id==$bill->customer_discount_id) { echo 'selected="selected"';}?> data-id="<?=$c_dis->id?>"><?=$c_dis->name?></option>
					<?php endforeach; ?>
				    </select>
				</td>
				<td>
				<span class="bill-customer-discount-label"><?=$total_customer_discount?></span>
				<input type="hidden" name="bill_customer_discount" value="<?=$total_customer_discount?>" class="bill-customer-discount">
				<input type="hidden" name="bill_customer_discount_id" value="<?=$bill->customer_discount_id?>" class="bill-customer-discount-id">
				<input type="hidden" name="bill_customer_discount_type" value="<?=$bill->discount_type?>" class="bill-customer-discount-type">
				
				</td>
			    </tr>
			    <tr>
				<?php $subtotal = $total_net_unit_price - $total_discount;?>
				<td colspan="13" style="text-align: right;"><?=lang('subtotal')?></td>
				<td>
				    <span class="bill-subtotal-label"><?=$subtotal?></span>
				    <input type="hidden" name="bill_subtotal" value="<?=$subtotal?>" class="bill-subtotal">
				</td>
			    </tr>
			    <?php $SC_enabled=false;
				//if($this->pos_settings->default_service_charge !=0 && $this->pos_settings->service_charge_option !=0):
				
			    ?>
			    
			    <?php $ServiceCharge = $this->site->getServiceChargeByID($bill->service_charge_id);
			    if($ServiceCharge){$SC_enabled=true;
			    $serice_charge_amt = $service_charge;   ?>
			    <tr>
				<td colspan="13" style="text-align: right;"><?=$ServiceCharge->name?></td>
				<td>
				    <?php $serice_charge_amt= $this->sma->formatDecimal($serice_charge_amt);?>
				    
				    <span class="bill-sc-label"><?=$serice_charge_amt?></span>
				    <input type="hidden" name="bill_sc_id" value="<?=$ServiceCharge->id?>" class="bill-sc-id">
				    <input type="hidden" name="bill_sc_val" value="<?=$ServiceCharge->rate?>%" class="bill-sc-val">
				    <input type="hidden" name="bill_sc" value="<?=$serice_charge_amt?>" class="bill-sc">
				</td>
			    </tr>
			    <?php }//endif;?>
			    <tr>
				<td colspan="13" style="text-align: right;"><?=lang('tax')?> <?=$bill_tax->name?></td>
				<td>
				    <?php $total_tax= $this->sma->formatDecimal($total_tax);?>
				    <?php $tax_type = ($bill->tax_type==1)?'tax-exclusive':'tax-inclusive';?>
				    <span class="bill-tax-label"><?=$total_tax?></span>
				    <input type="hidden" name="bill_tax_val" value="<?=$bill_tax->rate?>%" class="bill-tax-val">
				    <input type="hidden" name="bill_tax" value="<?=$total_tax?>" class="bill-tax <?=$tax_type?>">
				</td>
			    </tr>
			    <tr>
				<?php
				if($bill->tax_type==1){
				    $grandTotal = $subtotal + $total_tax;
				}else{
				    $grandTotal = $subtotal;
				}				
				?>
				<td colspan="13" style="text-align: right;"><?=lang('grand_total')?></td>
				<td>
				    <span class="bill-grandtotal-label"><?=$grandTotal?></span>
				    <input type="hidden" name="bill_grandtotal" value="<?=$grandTotal?>" class="bill-grandtotal">
				    <input type="hidden" name="bill_total_items" value="<?=$bill->total_items?>" class="bill-total-items">
				    <input type="hidden" name="bill_total_discount" value="<?=$bill->total_discount?>" class="bill-total-discount">
				    <input type="hidden" name="bill_manual_item_discount" value="<?=$bill->manual_item_discount?>" class="bill-manual-item-discount">
				    <input type="hidden" name="bill_id" value="<?=$bill->id?>" class="bill-id">
				    <input type="hidden" name="sale_id" value="<?=$bill->sales_id?>" class="sale-id">
				    
				    <input type="hidden" name="bill_tax_type" value="<?=$bill->tax_type?>" class="bill-tax-type">				    
				    <input type="hidden" name="bill_warehouse_id" value="<?=$bill->warehouse_id?>" class="bill-warehouse-id">
				</td>
				
			    </tr>
			  </tfoot>
			</tbody>
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
	$(document).on('click','.item-details:not(.deleted-item) .minus',function(){        
	    $cnt = parseInt($(this).closest('.qty_number').find('.item-qty').val()) - parseInt(1);
	    if ($cnt==0) {
		return false;
	    }
	    $(this).closest('.qty_number').find('.item-qty').val($cnt);
	    $(this).closest('.qty_number').find('.rquantity').trigger('change');
	    $itemID = $(this).closest('.item-details').attr('data-item');
	    calculateITEM($itemID);	
	});
	$(document).on('click','.item-details:not(.deleted-item) .plus',function(){	
	    $cnt = parseInt($(this).closest('.qty_number').find('.item-qty').val()) + parseInt(1);
	    $(this).closest('.qty_number').find('.item-qty').val($cnt);
	    $(this).closest('.qty_number').find('.rquantity').trigger('change');
	    $itemID = $(this).closest('.item-details').attr('data-item');
	    calculateITEM($itemID);
	});
	
	//// add new item /////
	$(document).on('click','.item_qty_number .minus',function(){
	    $existing_qty = $(this).closest('.item_qty_number').find('.input-item-qty').val();
	    if ($existing_qty=="") {
		$existing_qty = 0;
	    }
	    $cnt = parseInt($existing_qty) - parseInt(1);
	    if ($cnt==0) {
		return false;
	    }
	    $(this).closest('.item_qty_number').find('.input-item-qty').val($cnt);
	    $(this).closest('.item_qty_number').find('.rquantity').trigger('change');	    
	    calculatenewSubtotal($(this).closest('.add-item-row'));
	});
	$(document).on('click','.item_qty_number .plus',function(){
	    $existing_qty = $(this).closest('.item_qty_number').find('.input-item-qty').val();
	    if ($existing_qty=="") {
		$existing_qty = 0;
	    }
	    $cnt = parseInt($existing_qty) + parseInt(1);
	    $(this).closest('.item_qty_number').find('.input-item-qty').val($cnt);
	    $(this).closest('.item_qty_number').find('.rquantity').trigger('change');	    
	    calculatenewSubtotal($(this).closest('.add-item-row'));
	});
	
	
    });
    $(document).on('keyup','.item_qty_number .input-item-qty',function(){
	 calculatenewSubtotal($(this).closest('.add-item-row'));
    })
    
    $('.item-qty').on('change',function(){
	$itemID = $(this).closest('.item-details').attr('data-item');
	calculateITEM($itemID);	
    });
    $('.manual-dis-val').on('change',function(){
	$itemID = $(this).closest('.item-details').attr('data-item');
	$n_p =  $(this).closest('.item-details').find('.net-unit-price').val();
	
	if (parseFloat($(this).val()) > parseFloat($n_p)) {
	    bootbox.alert('Discount amount is more than net price');
	    $(this).val('');
	    return false;
	}
	//alert($itemID)
	calculateITEM($itemID);	
    });
    $(document).on('click','.remove-item',function(e){    	
	e.preventDefault();
	$(this).closest('.item-details').addClass('deleted-item');
	// $('.deleted-item .manual-dis-val').attr('disabled',true);
	if($(this).closest('tr').find(":input:not(:first)").is(':disabled')){		
		$(this).closest('.item-details').removeClass('deleted-item');
		$(this).closest('tr').find(":input:not(:first)").attr('disabled', false);
		
	}else{
		$(this).closest('tr').find(":input:not(:first)").attr('disabled', !this.checked);
	}
		
	calculateBill();
    });
    if ($('.all-customer-discounts').val()!=0) {
	getcustomerDis($('.all-customer-discounts'));
    }
    $('.all-customer-discounts').change(function(){	
	getcustomerDis($(this));
    });
    
    function getcustomerDis($thisobj){
	$val = $thisobj.val();
	
	$recipe_ids = [];
	$('.recipe-id').each(function(n,v){
	    $recipe_ids.push($(this).val());
	});
	
	$.ajax({
	    url: "<?=admin_url('reports/getCustomerDiscount')?>",
	    type: "post",
	    dataType:'json',
	    data: { 
		  discount_id: $val,
		  recipe_ids:$recipe_ids
	    },
	    success: function(response) {
		$.each(response,function(n,v){		    
		    $('.recipeid-'+n).each(function(){
			$itemobj  =$(this);
			$itemID = $itemobj.attr('data-item');
			if (v.discount_type=="percentage") {
			    $itemobj.find('.customer-dis-val').val(v.discount_val+'%');
			    
			    //$nup = parseFloat($itemobj.find('.net-unit-price').val());
			    //$m_dis = parseFloat($itemobj.find('.manual-discount').val());
			    //$i_dis = parseFloat($itemobj.find('.item-discount').val());
			    //$o_dis = parseFloat($itemobj.find('.offer-discount').val());
			    //$dis = $nup-$m_dis-$i_dis-$o_dis;
			    
			}else{
			    $itemobj.find('.customer-dis-val').val(v.discount_val);
			}
			calculateITEM($itemID);
		    });
		});
		
	    }
        });
    }
    function calculateITEM($itemID) {
	$('.item-details.item-'+$itemID).each(function(){
	    $this = $(this);
	    //alert($this.attr('class'));
	    $unit_price = $this.find('.unit-price').val();
	    //alert($unit_price)
	    $item_qty = $this.find('.item-qty').val();
	    $net_unit_price = $unit_price * $item_qty;
	    $manual_discount_val = $this.find('.manual-dis-val').val();
	    //console.log($unit_price+'--'+$item_qty)
	    if ($manual_discount_val==undefined) {$manual_discount_val = '';}
	    
	    if ($manual_discount_val.indexOf("%")!=-1) {
		console.log('wrong')
		$MDP = $manual_discount_val.replace('%','');
		$MD = $net_unit_price *($MDP/100);
		$afterMD = $net_unit_price - $MD;
	    }else{
		if ($manual_discount_val=='') {$manual_discount_val = 0;}
		$MD = $manual_discount_val;
		console.log($net_unit_price +'-'+ $MD)
		$afterMD = $net_unit_price - $MD;
	    }
	    console.log('gg'+$afterMD)
	    /************ ITEM Discount ****************************/
	    $item_discount_val = $this.find('.item-dis-val').val();
	    if ($item_discount_val==undefined) {$item_discount_val = '';}
	    if ($item_discount_val.indexOf("%")!=-1) {
		$IDP = $item_discount_val.replace('%','');
		$ID = $afterMD *($IDP/100);
		$afterID = $afterMD - $ID;
		
	    }else{
		if ($item_discount_val=='') {$item_discount_val = 0;}
		$ID = $item_discount_val;
		$afterID = $afterMD - $ID;
	    }
	    /************ Offer Discount ****************************/
	    $offer_discount_val = $('.bill-offer-discount-val').val();
	    //alert($offer_discount_val)
	    if ($offer_discount_val==undefined) {$offer_discount_val ='';}
	    if ($offer_discount_val.indexOf("%")!=-1) {
		$ODP = $offer_discount_val.replace('%','');
		$OD = $afterID *($ODP/100);
		$afterOD = $afterID - $OD;
	    }else{
		if ($offer_discount_val=='') {$offer_discount_val = 0;}
		$OD = $offer_discount_val;
		$afterOD = $afterID - $OD;
	    }
	    /************ Customer Discount ****************************/
	    $customer_discount_val = $this.find('.customer-dis-val').val();
	    //alert($customer_discount_val)
	    if ($customer_discount_val==undefined) {$customer_discount_val = '';}
	    if ($customer_discount_val.indexOf("%")!=-1) {
		$CDP = $customer_discount_val.replace('%','');
		$CD = $afterOD *($CDP/100);
		$afterCD = $afterOD - $CD;
	    }else{
		if ($customer_discount_val=='') {$customer_discount_val = 0;}
		$CD = $customer_discount_val;
		
		$afterCD = $afterOD - $CD;
	    }
	    console.log($MD +'=='+ $ID +'=='+ $OD +'=='+ $CD);
	    $totalDis = parseFloat($MD) + parseFloat($ID) + parseFloat($OD) + parseFloat($CD);
	    /************ Tax ****************************/
	    $tax_val = $('.bill-tax-val').val();
	    $item_tax = 0;
	    
	    //alert($afterCD)
	    if ($tax_val.indexOf("%")!=-1) {
		$TP = $tax_val.replace('%','');
		$item_tax = $afterCD * ($TP/100);
		$afterTax = $afterCD + $item_tax;
	    }else{
		$afterTax = $afterCD + $item_tax;
	    }
	    <?php if($SC_enabled) : ?>
		/************ Service charge ****************************/
		$sc_val = $('.bill-sc-val').val();
		$item_sc = 0;
		
		//alert($afterCD)
		if ($sc_val.indexOf("%")!=-1) {
		    $TP = $sc_val.replace('%','');
		    $item_sc = $afterCD * ($TP/100);
		    $afterSC = $afterTax + $item_sc;
		}else{
		    $afterSC = $afterTax + $item_sc;
		}
		$this.find('.item-sc').val($item_sc);
	    <?php endif; ?>
	    //alert($item_sc);
	    
	    //$ItemSubtotal = $afterTax;
	    $ItemSubtotal = $afterMD;
	    
	    /// change label values
	    
	    $this.find('.unit-price-label').text(formatDecimal($unit_price));
	    $this.find('.net-unit-price-label').text(formatDecimal($net_unit_price));
	    $this.find('.item-discount-label').text(formatDecimal($ID));
	    $this.find('.offer-discount-label').text(formatDecimal($OD));
	    $this.find('.customer-discount-label').text(formatDecimal($CD));
	    $this.find('.total-discount-label').text(formatDecimal($totalDis));
	    $this.find('.item-tax-label').text(formatDecimal($item_tax));
	    $this.find('.item-subtotal-label').text(formatDecimal($ItemSubtotal));
	    
	    
	    /// change input values
	    $this.find('.unit-price').val($unit_price);
	    $this.find('.net-unit-price').val($net_unit_price);
	    $this.find('.manual-discount').val($MD);
	    $this.find('.item-discount').val($ID);
	    $this.find('.offer-discount').val($OD);
	    $this.find('.customer-discount').val($CD);
	    $this.find('.total-discount').val($totalDis);
	    $this.find('.item-tax').val($item_tax);
	    
	    
	    $this.find('.item-subtotal').val($ItemSubtotal);
	    
	    calculateBill();
	});
    }
    function calculateBill(){
	$net_total = 0;
	$total_item_dis = 0;
	$total_offer_dis = 0;
	$total_customer_dis = 0;
	$sub_total = 0;
	$total_tax = 0;
	$total_service_charge = 0;
	$grand_total = 0;
	$total_items = 0;
	$total_manual_dis =0;
	$total_discount=0;
	$total_dis_after_ID=0;
	$('.item-details:not(.deleted-item)').each(function(){
	    $this = $(this);
	    $total_items +=1;
	    //alert($(this).attr('data-item'));return false;
	    $itemtax = parseFloat($this.find('.item-tax').val());
	    $itemsc = 0;
	    <?php if($SC_enabled) : ?>
		$itemsc = parseFloat($this.find('.item-sc').val());
	    <?php endif; ?>
	    $item_subtotal = parseFloat($this.find('.item-subtotal').val());
	    $manual_dis = parseFloat($this.find('.manual-discount').val());
	    $item_dis = parseFloat($this.find('.item-discount').val());
	    $offer_dis = parseFloat($this.find('.offer-discount').val());
	    $customer_dis =parseFloat($this.find('.customer-discount').val());
	    $discount = parseFloat($item_dis)+parseFloat($offer_dis)+parseFloat($customer_dis);
	    $item_total_discount = $discount+$manual_dis;
	    $item_total =$item_subtotal-$discount;
	    //alert($item_dis+'--'+$offer_dis+'--'+$customer_dis);
	    $total_dis_after_ID += $item_subtotal-$manual_dis-$item_dis;
	    $net_total +=parseFloat($item_subtotal);
	    $total_manual_dis +=parseFloat($manual_dis);
	    $total_item_dis +=parseFloat($item_dis);
	    $total_offer_dis +=parseFloat($offer_dis);
	    $total_customer_dis +=parseFloat($customer_dis);   
	    $sub_total +=$item_total;
	    $total_tax +=$itemtax;
	    $total_service_charge +=$itemsc;
	    if ($this.find('.item-tax').attr('tax-type')=='tax-exclusive') {
		$grand_total+=$item_total+$itemtax+$itemsc;
	    }else{
		$grand_total+=$item_total+$itemsc;
	    }
	    
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
	
	<?php if($SC_enabled) : ?>
	    $('.bill-sc').val(formatDecimal($total_service_charge));
	    $('.bill-sc-label').text(formatDecimal($total_service_charge));
	<?php endif; ?>
	
	///// payment //
	$('.p-amount:eq(0),.p-paid:eq(0)').val(formatDecimal($grand_total));
	$('.p-balance:eq(0),.p-amount-exchange:eq(0)').val(formatDecimal(0));
	
    }
    $pg_total = $('.bill-grandtotal').val(); 
    $('.p-amount:eq(0),.p-paid:eq(0)').val(formatDecimal($pg_total));
    $('.p-balance:eq(0),.p-amount-exchange:eq(0)').val(formatDecimal(0));
    
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
    .qty_number .item-qty,.item_qty_number .item-qty{
	margin: -2px;
	border: none;
	background: none;
    }
</style>
<script>var base_url="<?=admin_url()?>";</script>
<script type="text/javascript" src="<?=$assets?>js/manual_edit_bill.js"></script>
<div class="modal" id="LoadModal" tabindex="-1" role="dialog" >
    
    <div class="modal-dialog modal-lg add_customer_modal" style="width: 100%;">
	<div class="modal-content" style="min-height: 500px">
	    <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
		</button>
		<h4 class="modal-title" id="myModalLabel"><?php echo lang('add_items'); ?></h4>
	    </div>
	    
	    <div class="modal-body">
		<div class="row">
		    <div class="col-md-8">
			<div id="category-list">
			    <?php foreach($categories as $k => $category) : $active_class = ($k==0)?'active':''; ?>
				<button id="category-<?=$category->id?>" data-id="<?=$category->id?>" type="button" value="<?=$category->id?>" class="btn-prni category <?=$active_class?>" tabindex="-1">
				<span><?=$category->name?></span>
				</button>
			    <?php endforeach ; ?>
			    
			    <?php  //echo '<pre>';print_R($all_subcategories);?>
			    
			</div>
			<?php foreach($all_subcategories as $k => $categories) : ?>
			<?php $display = ($k==0)?'block':'none';?>
			 <div class="carousel subcategory-list subcat-<?=$categories->id?>" style="display: <?=$display?>;">
			    <div id="scroller">
				<?php foreach($categories->subcategories as $kk => $subcat) : ?>
				<button id="subcategory-<?=$subcat->id?>" data-cat="<?=$subcat->parent_id?>" data-sub="<?=$subcat->id?>" type="button" value='<?=$subcat->id?>' class="btn-img subcategory slide"  ><span class='name_strong'><?=$subcat->name?></span></button>
				<?php endforeach; ?>
			    </div>
			</div>
			<?php endforeach; ?>
			
			<div class="item-list">
			    
			</div>
		    
		    </div>
		    <div class="col-md-4">
			<div style="height: 470px;overflow: scroll;">
			<table class="table items table-striped table-fixed table-bordered table-condensed table-hover sortable_table" id="posTable" style="margin-bottom: 0;">
			   
			    <thead>
			    <tr>
				<th width="35%"> Sale Items</th>
				<th width="15%">Price </th>
									    <th width="20%">Quantity</th>
				<th width="15%">Subtotal </th>
				<th style="width: 5%; text-align: center;">
				    <i class="fa fa-trash-o" style="opacity:0.5; filter:alpha(opacity=50);"></i>
				</th>
			    </tr>
			    </thead>
			    <tbody class="ui-sortable">
			    </tbody>
                        </table>
			</div>
			<button type="button" class="btn btn-info btn-block" id="add-items-to-bill">Add Items</button>
			
		    </div>
		</div>
	    </div>
	</div>
    </div>
</div>

<style>
    #category-list .btn-prni.active {
	color: #fff;
	box-shadow: 2px 1px 6px 0px #d7a15d;
	background-color: #ff0000;
    }
    
    #category-list .btn-prni {
	height: 30px;
	width: auto;
	font-family: 'source_sans_proregular';
	min-width: 80px !important;
	box-shadow: 2px 1px 6px 0px #333;
	padding: 0px 3px;
	color: #000;
	font-weight: bold;
	outline: none;
	background-color: #d27607;
    }
    .btn-prni {
	border: none;
	cursor: pointer;
	height: 115px;
	margin: 0 0 0px 2px;
	padding: 2px;
	width: 10.5%;
	min-width: 100px;
	overflow: hidden;
	display: inline-block;
	font-size: 13px;
	background-color: transparent;
    }
    #category-list span {
	font-size: 14px;
	position: relative;
	float: left;
	text-align: center;
	width: 100%;
	min-width: 80px !important;
    }
    .btn-img {
    border: none;
    cursor: pointer;
    height: 115px;
    margin: 0 0 0px 2px;
    padding: 2px;
    width: 10.5%;
    min-width: 100px;
    overflow: hidden;
    display: inline-block;
    font-size: 13px;
    background-color: transparent;
}
.subcategory-list .btn-img {
    height: 48px;
    outline: none;
    margin: 0 10px 0px 0px;
    color: #343434;
    min-width: 109px;
    font-size: 17px;
}
.subcategory-list span{
    border: 1.5px solid #f30808;
    border-radius: 50px;
    height: 30px;
    font-size: 14px;
    line-height: 30px;
    font-family: 'source_sans_proregular';
    color: #000;
    font-weight: bold;
    background-color: #f5690a;
    width: 105px;
    overflow: hidden;
    display: block;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.item-list button{
    height: 58px;
    outline: none;
    color: #343434;
    min-width: -6% !important;
    font-size: 14px;
    margin: 0px 0px 15px 15px;
    background-color: #f5690ab0;
    border: 1px solid #f35408e0;
    border-radius: 4px;
}
.item_qty_number .plus {
    cursor: pointer;
    display: inline-block;
    vertical-align: top;
    color: #fff;
    width: 18px;
    height: 18px;
    font: 17px/1 Arial,sans-serif;
    text-align: center;
    border-radius: 50%;
    background-color: #606060;
    line-height: 20px;
    float: right;
    margin-top: 1px;
}
.item_qty_number .minus {
    cursor: pointer;
    display: inline-block;
    vertical-align: top;
    color: #fff;
    width: 18px;
    height: 18px;
    font: 18px/1 Arial,sans-serif;
    text-align: center;
    border-radius: 50%;
    background-clip: padding-box;
    background-color: #606060;
    line-height: 18px;
    float: left;
     margin-top: 1px;
}

.item_qty_number .input-item-qty {
    margin: -2px;
    border: none;
    background: none;
}
.item_qty_number .input-item-qty {
    color: #000;
    display: inline-block;
    vertical-align: top;
    font-size: 16px;
    font-weight: 700;
    line-height: 20px;
    padding: 0 2px;
    width: 30px;
    text-align: center;
    border: none;
    height: 25px;
    /*margin-left: 2px;*/
}
.newly-added-item td {
    background-color: #357f79 !important;
}

</style>


