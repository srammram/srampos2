<html>
    <head>
    <style>@page {
     margin:5px 5px 0px 5px;
     
    }</style>
        <meta charset="utf-8">
        <title> No 5293</title>
	<style>
	body {
	font-size: 16pt;
}
	</style>
	<style>

@page { sheet-size: 220mm 370mm; }

/*@page bigger { sheet-size: 220mm 370mm; }

@page toc { sheet-size: 58cm 297cm; }*/

h1.bigsection {
        /*page-break-before: always;
        page: bigger;*/
}

</style>
    </head>

    <body>
        <div>
            <table  style="width:55%">
	    <thead>
                <tr><th>
		
		<?=!empty($bill_details->biller->logo) ? '<img src="'.base_url('assets/uploads/logos/'.$bill_details->biller->logo).'" alt="">' : ''; ?>
		
		</th>
		</tr>
		<?php if($this->pos_settings->print_local_language == 1) : ?>
                <tr><th><h3 style="text-transform:uppercase;"><?=$bill_details->biller->local_lang_name?></h3></th></tr>
                <?php endif; ?>
		<?php $b_name = ($bill_details->biller->company != '-') ? $bill_details->biller->company : $bill_details->biller->name; ?>
                <tr><th><h3 style="text-transform:uppercase;"><?=$b_name?></h3></th></tr>
		
                <tr><th><h4><?=$bill_details->biller->address . " " . $bill_details->biller->city . " " . $bill_details->biller->postal_code . " " . $bill_details->biller->state . " " . $bill_details->biller->country ?>
                    <br><?= lang("tel") . ": " . $bill_details->biller->phone?></h4></th></tr>
                <tr><th><h3 class="text-center">RECEIPT</h3></th></tr>
                <tr>
                    <th style="text-align:left"><span style="font-weight:bold">Bill No : <?=$bill_details->bill_number?></span><br><span>Sale No/Ref: <?=$bill_details->reference?></span><br>
                    
                    <?php if($this->Settings->time_format == 12){
                        $date = new DateTime($bill_details->created_on);
                        $created_on = $date->format('Y-m-d h:iA');
                        }else{
                            $created_on =  $bill_details->created_on;
                        }
                    $customer =  ($bill_details->customer->company && $bill_details->customer->company != '-' ? $bill_details->customer->company : $bill_details->customer->name);
                    ?>
                        
                    Date: <?=$created_on?><br> Customer: <?=$customer?><br>Sales Associate: <?=$bill_details->cashier->sales_associate?> <br>Cashier: <?=$bill_details->cashier->cashier?><br>
                    <?php if($bill_details->tableno->order_type == 'Dine in' || $bill_details->tableno->order_type == 'BBQ'){ ?>
                       Table :<?=$bill_details->tableno->table_name?><br>
                    <?php } ?>
                    </th>
                </tr>
		</thead>
            </table>
	    <?php if(isset($bill_details->bill_items) && !empty($bill_details->bill_items)) : ?>
	    <table width=67%  style="">
		<thead style="text-align:left">
		    <th style="text-align:left;width: 3%;">Description</th>
		    <th style="text-align:left;">Price</th>
		    <th style="text-align:left;">Qty</th>
		    <th style="text-align:left;">Discount</th>
		    <th style="text-align:left;">Sub Total</th>
		<thead>
		<tbody>
	
                <?php foreach($bill_details->bill_items as $k => $items){ ?>
                    <tr style="text-align:left">
                        <td style="text-align:left;width: 3%;"><?=$items->recipe_name?></td>
                        <td style="text-align:left;width: 2%;"><?=$this->sma->formatDecimal($items->unit_price)?></td>
                        <td style="text-align:left;width: 2%;"><?=$items->quantity?></td>
                        <td style="text-align:left;width: 2%;"><?=$items->customer_discount_val?></td>
                        <td style="text-align:left;width: 2%;"><?=$this->sma->formatDecimal($items->subtotal)?></td>
                    </tr>
                <?php } ?>
	
                <?php if ($bill_details->tax_type == 0)
                            {
                                $taxname = 'Inclusive';
                                $grandtotal = $bill_details->total-$bill_details->total_discount-$bill_details->birthday_discount;
                            }
                            else
                            {
                                $taxname = 'Exclusive';
                                $grandtotal = $bill_details->total-$bill_details->total_discount-$bill_details->birthday_discount+$bill_details->total_tax;
                            } ?>
			    
			    
	
		</tbody>
		<tfoot><!---dont remove-->
		    <tr>
			<td>0</td>
			<td>0</td>
			<td>0</td>
			<td>0</td>
			<td>0</td>
		    </tr>
		</tfoot>
	    </table>
	    
	    <table   style="width:51%">
		<tbody>
		    <tr>
			
			<td style="text-align: right;width: 80%;font-weight:bold">Total</td>
			<td style="text-align: right;font-weight:bold"><?=$this->sma->formatMoney($bill_details->total)?></td>
		    </tr>
		    
	<?php if ($this->pos_settings->display_tax==1 && $bill_details->tax_rate != 0) { ?>
	    
	    
		    <tr>
			
			<td style="text-align: right;font-weight:bold"><?=$this->pos_settings->tax_caption?></td>
			<td style="text-align: right;font-weight:bold"><?=$this->sma->formatMoney($bill_details->total_tax)?></td>
		    </tr>
	<?php } ?>
	<tr>
			
			<td style="text-align: right;font-weight:bold">Grand Total</td>
			<td style="text-align: right;font-weight:bold"><?=$this->sma->formatMoney($grandtotal)?></td>
		    </tr>
		</tbody>
		<!--<tfoot>
		    <tr>
			
			<td></td>
			<td></td>
		    </tr>
		</tfoot>-->
	    </table>
	    <?php endif; ?>
	    <?php if(isset($bill_details->bbq)){ ?>
	    
		<?php if(isset($bill_details->bill_items) && !empty($bill_details->bill_items)) { ?><p style="">Bill No : <?=$bill_details->bbq->bill_number?></p><?php } ?>
		<table style="width:67%">
                    <thead>
                        <th style="text-align:left;width: 3%;">Details</th>
                        <th style="text-align:left;">Price</th>
                        <th style="text-align:left;">No of covers</th>
                        <th style="text-align:left;">Subtotal</th>
                    </thead>
		<tbody>
		<?php foreach($bill_details->bbq->bbq_covers as $b => $bbq){ ?>
		    
		   <tr>
		    <td style="text-align:left;width: 3%;"><?=($b+1)?>:<?=$bbq->type?></td>
		    <td style="text-align:left;width: 2%;"><?=$bbq->price?></td>
		    <td style="text-align:left;width: 2%;"><?=$bbq->cover?></td>
		    <td style="text-align:left;width: 2%;"><?=$bbq->subtotal?></td>
		    </tr>
		<?php } ?>
		
		<?php if ($bill_details->bbq->tax_type == 0)
                            {
                                $taxname = 'Inclusive';
                                $bbq_grandtotal = $bill_details->bbq->total-$bill_details->bbq->total_discount-$bill_details->bbq->birthday_discount;
                            }
                            else
                            {
                                $taxname = 'Exclusive';
                                $bbq_grandtotal = $bill_details->bbq->total-$bill_details->bbq->total_discount-$bill_details->bbq->birthday_discount+$bill_details->bbq->total_tax;
                            } ?>
                    
		</tbody>
		<tfoot>
		    <tr>
			<td>0</td>
			<td>0</td>
			<td>0</td>
			<td>gTotal</td>
		    </tr>
		</tfoot>
		</table>
		<table style="width:54%;">
		    <tr>
			
			<td style="text-align: right;width: 80%;font-weight:bold;">Total</td>
			<td style="text-align: right;"><?=$this->sma->formatMoney($bill_details->bbq->total)?></td>
			</tr>
			<tr>
			
			<td style="text-align: right;width: 80%;font-weight:bold;">Grand Total</td>
			<td style="text-align: right;"><?=$this->sma->formatMoney($bbq_grandtotal)?></td>
		    </tr>
		</table>
	    <?php } ?>
	    <?php if($bill_details->order_type==1 && @$bill_details->bbq->order_type==4){
		$final_total = $grandtotal + $bbq_grandtotal;
		$default_currency = $this->site->defaultCurrencyData($this->Settings->default_currency); ?>
		<table class="table table-striped table-condensed">
                    
                    
                    <tfoot>
                    	
                        <tr>
                            <th class="text-right"><?=lang("final_total")?></th>
                            <th class="text-right"><?=$this->sma->formatMoney($final_total,$default_currency->symbol)?></th>
                        </tr>
                       
                        
                    </tfoot>
                </table>
	    <?php } ?>
	    
	    
	    <table style="width:56%">
                <tbody>
                <?php foreach($bill_details->payments as $kk => $payment){
                    $exchange_curr_code = $this->site->getExchangeCurrency($this->Settings->default_currency); ?>
                    <tr><td>Paid By: <?=$payment->paid_by?></td>
                    <td>Amount : <?=$this->sma->formatMoney($payment->amount)?></td>
                    <td>Foreign Exchange:<?=$exchange_curr_code.$payment->amount_exchange?></td>
                    </tr>
                <?php }?>
                </tbody>
	    </table>
	    
	   <table style="text-align:right;width:56%">                    
                    <tbody>
                        <?php $currency = $this->site->getAllCurrencies();
                        $exchange_rate = $this->site->getExchangeRatey($this->Settings->default_currency);
			foreach($currency as $currency_row){
                            $change_riel = $bill_details->balance/ $exchange_rate;
                            $change_riel = round($change_riel / 100) * 100; ?>
						
			    <tr>
			    
				    <td style="text-align: right;"><?=lang("change ".$currency_row->code)?></td>
				    <td style="text-align:center"><?=$this->sma->formatMoney($bill_details->balance / $currency_row->rate,$currency_row->symbol)?></td>
				</tr>
                      <?php } ?>
                        
                    </tbody>
                </table>
		
		
	   <p style="width:56%;text-align:center"><?=$bill_details->inv_footer?></p>
	    
	    
	    
        </div>
</body>
</html>
