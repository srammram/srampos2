<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Untitled Document</title>
<meta content="width=device-width, initial-scale=1" name="viewport">
 <style type="text/css" media="all">
	body{background-color:#efefef; }
	.main_table{width: 100%;margin: 0 auto;background-color: #fff;}
	.table thead tr th{text-align: left;}
	h3{margin: 0px;}
	.table,table{width: 100%!important;}
	.table tr td,table body tr td,.table tr th,table thead tr th{border: none!important;padding: 2px!important;}
	.table tr td,.table body tr td,table tr th,.table thead tr th{text-align: left!important;}
	table tr td:last-child,.table body tr td:last-child,.table thead tr th:last-child,table tr th:last-child{text-align: right!important;}
	.table thead tr th:first-child,table tr th:first-child,.table body tr td:first-child,table tr td:first-child{text-align: left!important;}
	.table tr td h3{font-size: 18px;font-weight: bold;}
	 @page{
			size: auto; 
			margin: 0mm 5mm 0mm 5mm;
	      }
	@media print{
		#header,#footer{display: none!important;}
		body{font-size: 14px;font-weight: bold;}
		table tr td{font-size: 14px;font-weight: bold;}
			}
	</style>
</head>

<body>
	
    <table class="table main_table" >
		<tr>
			<td>
				<table class="table"  width="100%">
					<tr>
						<td colspan="3" align="center" style="text-align: center!important;font-size: 20px;"><b>Shift Settlment Print</b> </td>
					</tr>
					<tr>
						<td align="left">
							<table class="table">
								
								<tr>
									<td>Counter No</td>
									<td>:</td>
									<td style="text-align: left!important"><?=$this->till_name?></td>
								</tr>
								<tr>
									<td>User Name </td>
									<td>:</td>
									<td style="text-align: left!important"><?= $settlement->first_name ? $settlement->first_name : 'All User' ?></td>
								</tr>
                                <tr>
									<td>Created Name </td>
									<td>:</td>
									<td style="text-align: left!important"><?= $settlement->created_name ?></td>
								</tr>
							</table>
						</td>
						<td align="right">
							<table class="table">
							
								<tr>
									<td>Print Date </td>
									<td>:</td>
									<td style="text-align: left!important"><?=date('d/m/Y')?> </td>
								</tr>
								<tr>
									<td>Print Time </td>
									<td>:</td>
									<td style="text-align: left!important"><?=date('H:i:s')?> </td>
								</tr>
							</table>
						</td>
					</tr>
					
					
				</table>
			</td>
			
		</tr>
		<tr>
			<td align="center"><hr></td>
		</tr>
		<tr>
			<td>
				<table class="table"  >
					<tr>
						<td>Print Count   </td>
						<td>:</td>
						<td><?= $settlement->reprint ?></td>
					</tr>
					
					<tr>
						<td>Bill Closing Time   </td>
						<td>:</td>
						<td><?= $settlement->shift_end_time ?></td>
					</tr>
					<tr>
						<td>Total No.of Bills </td>
						<td>:</td>
						<td><?= $settlement->no_of_bills ?></td>
					</tr>
					<tr>
						<td>Total No. of Items</td>
						<td>:</td>
						<td><?= $settlement->no_of_items ?></td>
					</tr>
					<tr>
						<td>Opening Cash </td>
						<td>:</td>
						<td><?= $this->sma->formatDecimal($settlement->cash_open) ?></td>
					</tr>
					<tr>
						<td>Total Sales Amount</td>
						<td>:</td>
						<td><?= $this->sma->formatDecimal($settlement->bill_total) ?></td>
					</tr>
					
					<tr>
						<td>All Total  </td>
						<td>:</td>
						<td><?= $this->sma->formatDecimal($settlement->cash_open + $settlement->bill_total) ?></td>
					</tr>
				</table>
			</td>
		</tr>
        
        <tr>
			<td align="center"><hr></td>
		</tr>
        <?php
		//echo '<pre>';
		//print_r($settlement);
		?>
        <tr>
			<td>
				<table class="table">
                	<thead>
                    	<tr>
                        	<th>Tender Wise Sales :</th>
                            <th></th>
                            <th></th>
                            
                        </tr>
                    </thead>
                    <tbody>
					<tr>
						<td>Cash  </td>
						<td>:</td>
						<td><?= $settlement->bill_total - $settlement->sale_card ?></td>
					</tr>
                    <tr>
						<td>Card  </td>
						<td>:</td>
						<td><?= $settlement->sale_card ?></td>
					</tr>
                    <tr style="display:none">
						<td>Gift Voucher  </td>
						<td>:</td>
						<td><?= $settlement->sale_giftvoucher ?></td>
					</tr>
                    <tr  style="display:none">
						<td>Wallet </td>
						<td>:</td>
						<td><?= $settlement->sale_wallet ?></td>
					</tr>
					</tbody>
					
					
				</table>
			</td>
		</tr>
        
        
        
        <tr>
			<td align="center"><hr></td>
		</tr>
        
        <tr>
        	<td>
        		<table class="table" width="100%">
        			<tr>
        				<td>
        					<table class="table"  width="100%">
        						<thead>
        							<tr>
                                        <th width="300">Cash </th>
                                        <th width="300">Opening.Amount</th>
                                        <th width="300">Actual.Amount</th>
                                        <th width="300">Received.Amount </th>
                                        <th>Sh/Ex</th>
							        </tr>
        						</thead>
                                <tbody>
                                	<?php
									foreach($this->currencies as $c => $cur){ 
										if($cur->code == 'USD'){
									?>
                                    <tr>
                                    	<td><?= $cur->code ?></td>
                                        <td><?= $settlement->opening_cash_USD ?></td>
                                        <td><?= $settlement->cash_USD_actual ?></td>
                                        <td><?= $settlement->cash_USD_received ?></td>
                                        <td><?= $settlement->cash_USD_difference ?></td>
                                    </tr>
                                    <?php }elseif($cur->code == 'KHR'){ ?>
                                    <tr>
                                    	<td><?= $cur->code ?></td>
                                        <td><?= $settlement->opening_cash_KHR ?></td>
                                        <td><?= $settlement->cash_KHR_actual ?></td>
                                        <td><?= $settlement->cash_KHR_received ?></td>
                                        <td><?= $settlement->cash_KHR_difference ?></td>
                                    </tr>
                                    
                                    <?php
									}
									}
									?>
                                </tbody>
       					 </table>
        			</td>
       			 </tr>
       		 </table>
        	</td>
        </tr>
        
        <tr>
        	<td>
        		<table class="table" width="100%">
        			<tr>
        				<td>
        					<table class="table"  width="100%">
                            	
        						<thead>
        							<tr>
                                        <th width="300">Card </th>
                                        <th width="300">Opening.Amount</th>
                                        <th width="300">Actual.Amount</th>
                                        <th width="300">Received.Amount </th>
                                        <th>Sh/Ex</th>
							        </tr>
        						</thead>
                                <tbody>
                                	<tr>
                                    	<td></td>
                                        <td><?= $settlement->card_open ?></td>
                                        <td><?= $settlement->card_actual ?></td>
                                        <td><?= $settlement->card_received ?></td>
                                        <td><?= $settlement->card_difference ?></td>
                                    </tr>
                                </tbody>
       					 </table>
        			</td>
       			 </tr>
       		 </table>
        	</td>
        </tr>
        
        <tr   style="display:none">
        	<td>
        		<table class="table" width="100%">
        			<tr>
        				<td>
        					<table class="table"  width="100%">
        						<thead>
        							<tr>
                                        <th width="300">Gift Voucher </th>
                                        <th width="300">Opening.Amount</th>
                                        <th width="300">Actual.Amount</th>
                                        <th width="300">Received.Amount </th>
                                        <th>Sh/Ex</th>
							        </tr>
        						</thead>
                                <tbody>
                                	<tr>
                                    	<td></td>
                                        <td><?= $settlement->giftvoucher_open ?></td>
                                        <td><?= $settlement->giftvoucher_actual ?></td>
                                        <td><?= $settlement->giftvoucher_received ?></td>
                                        <td><?= $settlement->giftvoucher_difference ?></td>
                                    </tr>
                                </tbody>
       					 </table>
        			</td>
       			 </tr>
       		 </table>
        	</td>
        </tr>
        
        <tr    style="display:none">
        	<td>
        		<table class="table" width="100%">
        			<tr>
        				<td>
        					<table class="table"  width="100%">
        						<thead>
        							<tr>
                                        <th width="300">Wallet </th>
                                        <th width="300">Opening.Amount</th>
                                        <th width="300">Actual.Amount</th>
                                        <th width="300">Received.Amount </th>
                                        <th>Sh/Ex</th>
							        </tr>
        						</thead>
                                <tbody>
                                	<tr>
                                    	<td></td>
                                        <td><?= $settlement->wallet_open ?></td>
                                        <td><?= $settlement->wallet_actual ?></td>
                                        <td><?= $settlement->wallet_received ?></td>
                                        <td><?= $settlement->wallet_difference ?></td>
                                    </tr>
                                </tbody>
       					 </table>
        			</td>
       			 </tr>
       		 </table>
        	</td>
        </tr>
        
        
        
        <tr>
			<td align="center"><hr></td>
		</tr>
        
        <tr>
			<td>
				<table class="table"  width="100%">
				    
					<?php foreach($this->currencies as $c => $cur) { ?>
					<?php //if($val['actual']!=0):?>
					<tr>
						<td><h3>Cash - <?=$cur->code?> - Details: </h3></td>
					</tr>
					<tr>
						<td>
							<table class="table" >
							    <?php 
								foreach($settlement->denominations as $d => $d_val) { 
									if($d == $cur->code){
										foreach($d_val as $v => $r){
								?>
							    	<tr>
                                    	<td width="500"><?= str_replace($cur->code.'_','',$v); ?></td>
                                    	<td width="100">x</td>
										<td width="100"><?=$r?></td>
										<td width="100">=</td>
                                        <td><?=$this->sma->formatDecimal(str_replace($cur->code.'_','',$v)*$r)?></td>
                                    </tr>
								
								<?php }}}?>									
							</table>
						</td>
					</tr>
					
				    <?php
					}
					 ?>
				
				</table>
			</td>
		</tr>
       
	</table>
</body>
</html>
