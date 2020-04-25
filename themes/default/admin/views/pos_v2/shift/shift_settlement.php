<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?= "Login " . $Settings->site_name;?></title>
    <script type="text/javascript">if(parent.frames.length !== 0){top.location = '<?=admin_url('pos')?>';}</script>
    <base href="<?=base_url()?>"/>
    <meta http-equiv="cache-control" content="max-age=0"/>
    <meta http-equiv="cache-control" content="no-cache"/>
    <meta http-equiv="expires" content="0"/>
    <meta http-equiv="pragma" content="no-cache"/>
    <link rel="stylesheet" href="<?=$assets?>fonts/barlow_condensed/stylesheet.css" type="text/css">
    <link rel="shortcut icon" href="<?=$assets?>images/favicon.ico"/>
   	<link rel="stylesheet" href="<?=$assets?>styles/helpers/bootstrap.min.css" type="text/css">
   	<link rel="stylesheet" href="<?=$assets?>styles/helpers/font-awesome.min.css" type="text/css">
   	<link rel="stylesheet" href="<?=$assets?>styles/jquery.mCustomScrollbar.css" type="text/css">
   	<link href="<?= $assets ?>styles/flipclock.css" rel="stylesheet"/>
   	<link rel="stylesheet" href="<?=$assets?>styles/home_frontend.css" type="text/css">
	<script type="text/javascript" src="<?=$assets?>js/jquery-2.0.3.min.js"></script>
    
</head>
<style>
	.shift_management_h h3{margin-top: 0px;}
	.shift_management{background-color: #380404;padding-top: 20px;}
	.cur li{margin-bottom: 25px;}
	.shift_management .table{background-color: #200202;margin-bottom: 10px;}
	.shift_management .table tr td{border: none;padding: 0px;color: #fff;}
	.shift_management .table tr td input{width: 100%;padding: 4px;border: 1px solid #cacaca;color: #333;font-size: 14px;font-weight: bold;}
	.shift_management .table tr td input:read-only{background-color: #eee;}
	.shift_management .table tr td:first-child input{text-align: right;}
	.currency_na{color: #d12312;border-bottom: 1px solid #d12312;margin-bottom: 10px;}
	.shift_management .payment_t tr th{color: #fff;padding: 8px;font-size: 16px;font-weight: normal;}
	.shift_management .payment_t tr td{color: #fff;padding: 8px;border: 1px solid #eee;}
	.shift_management .payment_t{margin-top: 40px;}
	.shift_management .payment_t tr td table tr td{border:none!important;}
	.foot_shift_manage .table tr td{text-transform: capitalize!important;font-size: 14px;}
	.foot_shift_manage .table tr td .form-control{border-radius: 0px;height: 30px;}
	.foot_shift_manage .table tr td .btn{border-radius: 0px;height: 30px;}
</style>
<body>
	<?php
	$defaultcurdata = $this->defaultcurdata;
	
	?>
	
	<section class="pos_bottom_s">
		<div class="container-fluid">
			<div class="row">
				<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12 logo_sec">
					 <?php if ($this->Settings->logo3) { ?>
							<a href="<?php echo base_url('pos/pos/'); ?>"><img src="<?=base_url()?>assets/uploads/logos/<?=$this->Settings->logo3?>" alt="<?=$this->Settings->site_name?>" class="sram_table_logo" width="100%" /></a>
					   <?php   } ?>
				</div>
				<div class="col-lg-10 col-md-10 col-sm-10 col-xs-12 text-left header_sec_me">
					<ul class="menu_nav">
						<li>
							<a href="<?php echo base_url('/pos/pos/home')  ?>">
								<figure class="text-center">
									<div class="img_block">
										<img src="<?=$assets?>images/sprite/home_icon.png">
										<figcaption>Home</figcaption>
									</div>
								</figure>
							</a>
						</li>
						<li>
							<a href="<?php echo base_url('/pos/pos/split_list')  ?>">
								<figure class="text-center">
									<div class="img_block">
										<img src="<?=$assets?>images/sprite/order.png">
										<figcaption>Order</figcaption>
									</div>
								</figure>
							</a>
						</li>
						<li>
							<a href="<?php echo base_url('/pos/pos/invoice_list')  ?>">
								<figure class="text-center">
									<div class="img_block">
										<img src="<?=$assets?>images/sprite/invoice.png">
										<figcaption>Invoice</figcaption>
									</div>
									
								</figure>
							</a>
						</li>
					<li>
							<a href="<?php echo base_url('/pos/pos/reprint')  ?>">
							<button>
								<img src="<?=$assets?>images/sprite/print.png">
								<figcaption>Re Print</figcaption>
							</button>
							</a>
						</li>
						<li>
							<a href="<?php echo base_url('/pos/pos/report')  ?>">
								<figure class="text-center">
									<div class="img_block">
										<img src="<?=$assets?>images/sprite/report_icon.png">
										<figcaption>Reports</figcaption>
									</div>
								</figure>
							</a>
						</li>
						
						
						<li>
							<a href="<?php  echo base_url('pos/login/logout') ?>">
								<button class="pull-right">
									<img src="<?=$assets?>images/sprite/exit.png">
									<figcaption>Exit</figcaption>
								</button>
							</a>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</section>
    <form action="<?= base_url('pos/shift/add_settlement') ?>" method="post" name="add_settlement" id="add_settlement" class="add_settlement">
    <input type="hidden" name="shift_id" value="<?= $pendingSettlement->id ?>">
    <input type="hidden" name="warehouse_id" value="<?= $pendingSettlement->warehouse_id ?>">
    <input type="hidden" name="till_id" value="<?= $pendingSettlement->till_id ?>">
    <input type="hidden" name="user_id" value="<?= $pendingSettlement->user_id ?>">
    <input type="hidden" name="no_of_bills" value="<?= $pendingSettlement->no_of_bills ?>">
    <input type="hidden" name="no_of_items" value="<?= $pendingSettlement->no_of_items ?>">
    <input type="hidden" name="bill_total" value="<?= $pendingSettlement->bill_total ?>">
    <input type="hidden" name="default_currency" value="<?= $defaultcurdata->id ?>">
    <input type="hidden" name="default_cur_rate" id="default_cur_rate" value="<?=$defaultcurdata->rate?>">
    <section class="foot_shift_manage">
    	<div class="container">
    		<div class="row">
    		  <div class="col-sm-12">
           	<h3 class="text-center">Shift Settlement</h3>
           </div>
    			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 top_foot">
    				<table class="table">
    					<tbody>
    						<tr>
    							<td>
    								<table style="width: 100%">
    									<tr>
    										<td>Open Til</td>
    										<td>
    											<select name="counter_name" id="counter_name" class="form-control" style="width:120px;">
                                    	<option value="">Select Tils</option>
                                        <?php
										if(!empty($tils)){
											foreach($tils as $tils_row){
											if($get_till_id == $tils_row->id){
												$selected = 'selected';
											}else{
												$selected = '';	
											}
										?>
                                        <option <?= $selected ?> value="<?= $tils_row->id ?>"><?= $tils_row->till_name ?></option>
                                        <?php
											}
										}
										?>
                                    </select>
    										</td>
    									</tr>
    								</table>
    							</td>
    							<td>
    								<table>
    									<tr>
    										<td>Settlement Date :</td>
    										<td><?= date('Y-m-d') ?></td>
    									</tr>
    								</table>
    							</td>
    							
    							<td>
    								<table>
    									<tr>
    										<td>Shift Name :</td>
    										<td>
												<?= $pendingSettlement->shift_name ?>
   											</td>
    									</tr>
    								</table>
    							</td>
                                <td>
    								<table>
    									<tr>
    										<td>Created By :</td>
    										<td>
												<?= $pendingSettlement->created_name ?>
   											</td>
    									</tr>
    								</table>
    							</td>
    							<td>User: 
								<?php
								if($this->Settings->shift_user_handling == 1){
									echo $pendingSettlement->assigned_name;
								}else{
									echo 'All Empolyee';
								}
								?>
                              
								</td>
    							<td>
                                	<input type="button" name="add_settlement_submit" id="save-settlement" value="Submit" class="btn btn-success">
                                	 <?php
								if($GP['pos-shift_view'] == 1 || $this->Owner || $this->Admin){
								?>
                                <td>
                                	<button type="button" id="viewshift" class="btn btn-primary">View</button>
                                </td>
    							<?php
								}
								?>
                                </td>
    						</tr>
    					</tbody>
    				</table>
    			</div>
    		</div>

    	</div>
	</section>
    
    
	<section class="shift_management_h">
		<div class="container">
         
           <div class="row">
           	<div class="col-sm-12 col-xs-12 shift_management">
           		<!--<div class="col-sm-1 col-xs-12">
					<ul class="cur">
						<li>
							<button type="button" class="btn btn-primary">USD</button>
						</li>
						<li>
							<button type="button" class="btn btn-warning">KHR</button>
						</li>
					</ul>
           		</div>-->
           		<div class="col-sm-3 col-xs-12">
                
           		<legend class="currency_na">USD</legend>
           			<table class="table">
           				<tbody>
           					<tr>
           						<td><input type="text" readonly value="500x"></td>
           						<td><input type="text" name="USD_500" id="USD_500" data-code="USD" data-value="500" class="multiple_currency" onkeyup="checkNum(this)" ></td>
           						<td><input type="text" name="TOTALUSD_500" id="TOTALUSD_500" class="TOTALUSD"  readonly></td>
           					</tr>
           					<tr>
           						<td><input type="text" readonly value="200x"></td>
           						<td><input type="text" name="USD_200" id="USD_200" data-code="USD" data-value="200" class="multiple_currency" onkeyup="checkNum(this)"></td>
           						<td><input type="text" name="TOTALUSD_200" id="TOTALUSD_200"  class="TOTALUSD" readonly></td>
           					</tr>
           					<tr>
           						<td><input type="text" readonly value="100x"></td>
           						<td><input type="text" name="USD_100" id="USD_100" data-code="USD" data-value="100" class="multiple_currency" onkeyup="checkNum(this)"></td>
           						<td><input type="text" name="TOTALUSD_100" id="TOTALUSD_100"  class="TOTALUSD" readonly></td>
           					</tr>
           					<tr>
           						<td><input type="text" readonly value="50x"></td>
           						<td><input type="text" name="USD_50" id="USD_50" data-code="USD" data-value="50" class="multiple_currency" onkeyup="checkNum(this)"></td>
           						<td><input type="text" name="TOTALUSD_50" id="TOTALUSD_50" class="TOTALUSD"  readonly></td>
           					</tr>
           					<tr>
           						<td><input type="text" readonly value="20x"></td>
           						<td><input type="text" name="USD_20" id="USD_20" data-code="USD" data-value="20" class="multiple_currency" onkeyup="checkNum(this)"></td>
           						<td><input type="text" name="TOTALUSD_20" id="TOTALUSD_20" class="TOTALUSD"  readonly></td>
           					</tr>
           					<tr>
           						<td><input type="text" readonly value="10x"></td>
           						<td><input type="text" name="USD_10" id="USD_10" data-code="USD" data-value="10" class="multiple_currency" onkeyup="checkNum(this)"></td>
           						<td><input type="text" name="TOTALUSD_10" id="TOTALUSD_10"  class="TOTALUSD" readonly></td>
           					</tr>
           					<tr>
           						<td><input type="text" readonly value="5x"></td>
           						<td><input type="text" name="USD_5" id="USD_5" data-code="USD" data-value="5" class="multiple_currency" onkeyup="checkNum(this)"></td>
           						<td><input type="text" name="TOTALUSD_5" id="TOTALUSD_5" class="TOTALUSD" readonly></td>
           					</tr>
           					<tr>
           						<td><input type="text" readonly value="2x"></td>
           						<td><input type="text" name="USD_2" id="USD_2" data-code="USD" data-value="2" class="multiple_currency" onkeyup="checkNum(this)"></td>
           						<td><input type="text" name="TOTALUSD_2" id="TOTALUSD_2" class="TOTALUSD" readonly></td>
           					</tr>
           					<tr>
           						<td><input type="text" readonly value="1x"></td>
           						<td><input type="text" name="USD_1" id="USD_1" data-code="USD" data-value="1" class="multiple_currency" onkeyup="checkNum(this)"></td>
           						<td><input type="text" name="TOTALUSD_1" id="TOTALUSD_1" class="TOTALUSD"  readonly></td>
           					</tr>
           					<tr>
           						<td  colspan="2"><input type="text" value="Total" readonly></td>
           						<td><input type="text" name="WITHDRAWUSD" id="WITHDRAWUSD"></td>
           					</tr>
           					
           				</tbody>
           			</table>
           			<legend class="currency_na">KHR</legend>
           			<table class="table">
           				<tbody>
           					<tr>
           						<td><input type="text" readonly value="50000x"></td>
           						<td><input type="text" name="KHR_50000" id="KHR_50000" data-code="KHR" data-value="50000" class="multiple_currency" onkeyup="checkNum(this)"></td>
           						<td><input type="text" name="TOTALKHR_50000" id="TOTALKHR_50000" class="TOTALKHR" readonly></td>
           					</tr>
           					<tr>
           						<td><input type="text" readonly value="10000x"></td>
           						<td><input type="text" name="KHR_10000" id="KHR_10000" data-code="KHR" data-value="10000" class="multiple_currency" onkeyup="checkNum(this)"></td>
           						<td><input type="text" name="TOTALKHR_10000" id="TOTALKHR_10000" class="TOTALKHR" readonly></td>
           					</tr>
           					<tr>
           						<td><input type="text" readonly value="5000x"></td>
           						<td><input type="text" name="KHR_5000" id="KHR_5000" data-code="KHR" data-value="5000" class="multiple_currency" onkeyup="checkNum(this)"></td>
           						<td><input type="text" name="TOTALKHR_5000" id="TOTALKHR_5000" class="TOTALKHR" readonly></td>
           					</tr>
           					<tr>
           						<td><input type="text" readonly value="2000x"></td>
           						<td><input type="text" name="KHR_2000" id="KHR_2000" data-code="KHR" data-value="2000" class="multiple_currency" onkeyup="checkNum(this)"></td>
           						<td><input type="text" name="TOTALKHR_2000" id="TOTALKHR_2000" class="TOTALKHR"  readonly></td>
           					</tr>
           					<tr>
           						<td><input type="text" readonly value="1000x"></td>
           						<td><input type="text" name="KHR_1000" id="KHR_1000" data-code="KHR" data-value="1000" class="multiple_currency" onkeyup="checkNum(this)"></td>
           						<td><input type="text" name="TOTALKHR_1000" id="TOTALKHR_1000" class="TOTALKHR" readonly></td>
           					</tr>
           					<tr>
           						<td><input type="text" readonly value="100x"></td>
           						<td><input type="text" name="KHR_100" id="KHR_100" data-code="KHR" data-value="100" class="multiple_currency" onkeyup="checkNum(this)"></td>
           						<td><input type="text" name="TOTALKHR_100" id="TOTALKHR_100" class="TOTALKHR"  readonly></td>
           					</tr>
           					<tr>
           						<td  colspan="2"><input type="text" value="Total" readonly></td>
           						<td><input type="text" name="WITHDRAWKHR" id="WITHDRAWKHR"></td>
           					</tr>
           					
           				</tbody>
           			</table>
           		</div>
           		<div class="col-sm-9 col-xs-12">
           			<table class="table payment_t table-bordered">
           				<thead>
           					<tr>
           						<th>Payment Type</th>
           						<th>Opening Cash</th>
           						<th>Actual</th>
           						<th>Received</th>
           						<th>Difference</th>
           					</tr>
           				</thead>
           				<tbody>
                        	<?php
							
							if(!empty($payment_type['cash'])){
								$payment_type['cash'];
							?>
                        	
           					<tr>
           						<td rowspan="3" align="left" valign="middle" style="vertical-align: middle;" >Cash</td>
                                
           						<td>
           							<table>
                                    	<?php foreach($this->currencies as $c => $cur){  ?>
           								<tr>
           									<td><?=$cur->code?> : <span id="html_opening_cash_<?=$cur->code?>"><?= number_format($payment_type['cash']['OPEN_'.$cur->code], 2) ?></span><input type="hidden" class="open_cash" value="<?= $payment_type['cash']['OPEN_'.$cur->code] ?>" name="opening_cash_<?=$cur->code?>" id="opening_cash_<?=$cur->code?>" default-cur-rate="<?=$defaultcurdata->rate?>" data-rate="<?=$cur->rate?>" data-code="<?=$cur->code?>"></td>
           								</tr>
           								<?php } ?>
                                        
           								<input type="hidden" name="cash_open" id="cash_open">
           							</table>
                                    
           						</td>
                                
                                <td>
           							<table>
                                    	<?php foreach($this->currencies as $c => $cur){  
											
										?>
           								<tr  class="<?= $cur->code == 'KHR' ? 'hidden' : '' ?>">
           									<td><?=$cur->code?>  : <span id="html_cash_<?=$cur->code?>_actual"><?= 
											$cur->code == 'USD' ? number_format($payment_type['cash']['ACTUAL_'.$cur->code] - $payment_type['card'], 2) : number_format($payment_type['cash']['ACTUAL_'.$cur->code], 2) ?></span><input type="hidden" class="cash_actual" name="cash_<?=$cur->code?>_actual" value="<?= $cur->code == 'USD' ? number_format($payment_type['cash']['ACTUAL_'.$cur->code] - $payment_type['card'], 2) : number_format($payment_type['cash']['ACTUAL_'.$cur->code], 2) ?>" id="cash_<?=$cur->code?>_actual" default-cur-rate="<?=$defaultcurdata->rate?>" data-rate="<?=$cur->rate?>" data-code="<?=$cur->code?>"></td>
           								</tr>
           								<?php } ?>
           								<input type="hidden" name="cash_actual" id="cash_actual">
           							</table>
           						</td>
                                
                                 <td>
           							<table>
                                    	<?php foreach($this->currencies as $c => $cur){  ?>
           								<tr>
           									<td><?=$cur->code?> : <span id="html_cash_<?=$cur->code?>_received"><?= $payment_type['cash']->RECEIVED_USD ?></span><input type="hidden" class="cash_received" name="cash_<?=$cur->code?>_received" id="cash_<?=$cur->code?>_received" default-cur-rate="<?=$defaultcurdata->rate?>" data-rate="<?=$cur->rate?>" data-code="<?=$cur->code?>"></td>
           								</tr>
           								<?php } ?>
           								<input type="hidden" name="cash_received" id="cash_received">
           							</table>
           						</td>
                                
                                <td>
           							<table>
                                    	<?php foreach($this->currencies as $c => $cur){  ?>
           								<tr>
           									<td><?=$cur->code?> : <span id="html_cash_<?=$cur->code?>_difference"><?= $payment_type['cash']->DIFFERENCE_USD ?></span><input type="hidden" class="cash_difference" name="cash_<?=$cur->code?>_difference" id="cash_<?=$cur->code?>_difference" default-cur-rate="<?=$defaultcurdata->rate?>" data-rate="<?=$cur->rate?>" data-code="<?=$cur->code?>"></td>
           								</tr>
           								<?php } ?>
           								<input type="hidden" name="cash_difference" id="cash_difference">
           							</table>
           						</td>
           						
           						
           						
           					</tr>
                            
                            <?php
							}
							?>
                       </tbody>
                       <tbody>     
                            <tr class="hidden">
                            	
           						<td colspan="2" align="left" valign="middle" style="vertical-align: middle;" >Giftvoucher
                                <input type="hidden" name="giftvoucher_open" id="giftvoucher_open" value="0"></td>
                                <td><span id="html_giftvoucher_actual">0.00</span><input type="hidden" name="giftvoucher_actual" id="giftvoucher_actual" value="0"></td>
                                <td><input type="text" name="giftvoucher_received" id="giftvoucher_received" onkeyup="checkNum(this)"></td>
                                <td><span id="html_giftvoucher_difference">0.00</span><input type="hidden" name="giftvoucher_difference" id="giftvoucher_difference" value="0"></td>
                                
                            </tr>
                            <tr>
                            	
           						<td colspan="2"  align="left" valign="middle" style="vertical-align: middle;" >Card
                                <input type="hidden" name="card_open" id="card_open" value="0"></td>
                                <td><span id="html_card_actual"><?= number_format($payment_type['card'], 2) ?></span><input type="hidden" name="card_actual" id="card_actual" value="<?= $payment_type['card'] ?>"></td>
                                <td><input type="text" name="card_received" id="card_received" onkeyup="checkNum(this)"></td>
                                <td><span id="html_card_difference"><?= '-'.number_format($payment_type['card'], 2) ?></span><input type="hidden" name="card_difference" id="card_difference" value="<?= $payment_type['card'] ?>"></td>
                                
                            </tr>
                            <tr class="hidden">
                            	
           						<td colspan="2"  align="left" valign="middle" style="vertical-align: middle;" >Wallet
                                <input type="hidden" name="wallet_open" id="wallet_open" value="0"></td>
                                <td><span id="html_wallet_actual">0.00</span><input type="hidden" name="wallet_actual" id="wallet_actual" value="0"></td>
                                <td><input type="text" name="wallet_received" id="wallet_received" onkeyup="checkNum(this)"></td>
                                <td><span id="html_wallet_difference">0.00</span><input type="hidden" name="wallet_difference" id="wallet_difference" value="0"></td>
                                
                            </tr>
                           
                        </tbody>    
           				
           			</table>
           		</div>
           	</div>
           </div>
    	</div>
	</section>
	
	</form>
    
    
  <div class="modal" id="shiftView" tabindex="-1" role="dialog" >
	<div class="modal-dialog modal-md">
		<div class="modal-content">
		    <div class="modal-header">	
            <button type="button" class="close" data-dismiss="modal" style="font-size: 50px !important;right: 15px;">&times;</button>		
			<h4 class="modal-title" id="myModalLabel">View - Shift</h4>
		    </div>
		    <div class="modal-body">
		    	
					
                    <table class="table">
                    	<thead>
                        	<tr style="color:#FFFFFF !important; font-size:18px;">
                            	<th>Shift Date & Time</th>
                                <th>Shift Name</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody class="view_shift">
                        	
                        </tbody>
                    </table>
	    			
		    </div>
		</div>
	</div>
</div>

	<script type="text/javascript" src="<?=$assets?>js/jquery-2.0.3.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/perfect-scrollbar.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/select2.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/custom.js"></script>
<script type="text/javascript" src="<?=$assets?>js/jquery.calculator.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/bootstrapValidator.min.js"></script>
<script type="text/javascript" src="<?=$assets?>pos/js/plugins.min.js"></script>
<script type="text/javascript" src="<?=$assets?>pos/js/parse-track-data.js"></script>


	
    <script>
	function print_popupsettlement(html,$reload=false) {
		
        var mywindow = window.open('', 'sma_pos_print', 'height=500,width=1000');
        mywindow.document.write('<html><head><title>Print</title>');
        //mywindow.document.write('<link rel="stylesheet" href="'+assets+'css/bootstrap.min.css" type="text/css" />');
        mywindow.document.write('</head><body>');
        mywindow.document.write(html);
        mywindow.document.write('</body></html>');
        var is_chrome = Boolean(mywindow.chrome);
       setTimeout(function () {
		   
            mywindow.print();
            mywindow.close();
             
			if($reload){location.reload();}
       },100);
        
        return true;
    }
	
	function calculate_opcash_defaultcurrency() {
		$default_op_cash = 0;
		$('.open_cash').each(function(n,v){
			$input_val = $(this).val();
			$this_default_cur_rate = $(this).attr('default-cur-rate');
			$rate = $(this).attr('data-rate');
			$this_cash = ($input_val*$rate)/$this_default_cur_rate;
			$default_op_cash += parseFloat($this_cash);
		});
		$('#cash_open').val($default_op_cash);
		
	}
	function calculate_actcash_defaultcurrency() {
		$default_act_cash = 0;
		$('.cash_actual').each(function(n,v){
			$input_val = $(this).val();
			$this_default_cur_rate = $(this).attr('default-cur-rate');
			$rate = $(this).attr('data-rate');
			$this_cash = ($input_val*$rate)/$this_default_cur_rate;
			$default_act_cash += parseFloat($this_cash);
		});
		$('#cash_actual').val($default_act_cash);
		
	}
	function calculate_reccash_defaultcurrency() {
		$default_rec_cash = 0;
		$('.cash_received').each(function(n,v){
			$input_val = $(this).val();
			$this_default_cur_rate = $(this).attr('default-cur-rate');
			$rate = $(this).attr('data-rate');
			$this_cash = ($input_val*$rate)/$this_default_cur_rate;
			$default_rec_cash += parseFloat($this_cash);
		});
		$('#cash_received').val($default_rec_cash);
		
	}
	function calculate_diffcash_defaultcurrency() {
		$default_diff_cash = 0;
		$('.cash_difference').each(function(n,v){
			$input_val = $(this).val();
			$this_default_cur_rate = $(this).attr('default-cur-rate');
			$rate = $(this).attr('data-rate');
			$this_cash = ($input_val*$rate)/$this_default_cur_rate;
			$default_diff_cash += parseFloat($this_cash);
		});
		$('#cash_difference').val($default_diff_cash);
		
	}
	
	function calculateWithdrawAmount($cur_code) {
		$amt = 0;
		$('.TOTAL'+$cur_code).each(function(n,v){
			$v = ($(this).val()=='')?0:$(this).val();
			$amt += parseFloat($v); 
		});
		$('#WITHDRAW'+$cur_code).val($amt.toFixed(2));
		$('#cash_'+$cur_code+'_received').val($amt);
		$('#html_cash_'+$cur_code+'_received').text($amt.toFixed(2));
		
		$opening_cash = parseFloat($('#opening_cash_'+$cur_code).val());
		$actual_cash = parseFloat($('#cash_'+$cur_code+'_actual').val());
		
		$difference_cash = $amt - ($opening_cash + $actual_cash);
		
		
		$('#cash_'+$cur_code+'_difference').val($difference_cash);
		$('#html_cash_'+$cur_code+'_difference').text($difference_cash.toFixed(2));
		
	}
	$(document).ready(function(e) {
		<?php foreach($this->currencies as $c => $cur) { ?>
        calculateWithdrawAmount('<?=$cur->code?>');
		<?php } ?>
		calculate_opcash_defaultcurrency();
		calculate_actcash_defaultcurrency();
		calculate_reccash_defaultcurrency();
		calculate_diffcash_defaultcurrency();
		
    });
	
	$(document).on('keyup','#giftvoucher_received',function(){
		var giftvoucher_actual = parseFloat($('#giftvoucher_actual').val());
        var giftvoucher_received = parseFloat($(this).val());
		var giftvoucher_difference = giftvoucher_actual + giftvoucher_received;
		$('#giftvoucher_difference').val(giftvoucher_difference);
		$('#html_giftvoucher_difference').text(giftvoucher_difference.toFixed(2));
    });
	
	$(document).on('keyup','#card_received',function(){
		var card_actual = parseFloat($('#card_actual').val());
        var card_received = parseFloat($(this).val());
		var card_difference = card_received - card_actual;
		$('#card_difference').val(card_difference);
		$('#html_card_difference').text(card_difference.toFixed(2));
    });
	
	$(document).on('keyup','#wallet_received',function(){
		var wallet_actual = parseFloat($('#wallet_actual').val());
        var wallet_received = parseFloat($(this).val());
		var wallet_difference = wallet_actual + wallet_received;
		$('#wallet_difference').val(wallet_difference);
		$('#html_wallet_difference').text(wallet_difference.toFixed(2));
    });
	
	
	$(document).on('keyup','.multiple_currency',function(){
        $this = $(this);
        $multiply_by = $this.val();
        $cur_code = $this.attr('data-code');
        $denomination = $this.attr('data-value');
        $total = $denomination * $multiply_by;
       // alert($multiply_by);
		//alert($cur_code);
		//alert($denomination);
        $('#TOTAL'+$cur_code+'_'+$denomination).val($total.toFixed(2));
        calculateWithdrawAmount($cur_code);
		calculate_opcash_defaultcurrency();
		calculate_actcash_defaultcurrency();
		calculate_reccash_defaultcurrency();
		calculate_diffcash_defaultcurrency();
    });
	//$('#print').click(function(){
		//print_popup();
		//stop_reload = false;
	//});
	$('#save-settlement').click(function(){
		var base_url = '<?= base_url('pos/shift/add_settlement') ?>';
		bootbox.confirm("Are you sure want to Settle Shift transactions ?", function(result){
			if(result){
				$.ajax({
					type: 'post',
					url: '<?= base_url('pos/shift/add_settlement') ?>',
					data:$('#add_settlement').serialize(),
					dataType: "json",
					success: function (data) {
						$(this).attr('disabled','disabled');
						$shift_id = data.shift_id;
						
						
							$.ajax({
							  type: 'get',
							  dataType:"html",
							  url: '<?= base_url('pos/shift/get_shift_data/') ?>'+$shift_id+'/1',
							  
							  success: function (res) {
								  print_popupsettlement(res,true);
								 
							  }
							});
					}
				});
				
	
			}
		})
	});
	
	$(document).on('click', '#viewshift', function(){
		var start_date = $('#start_date').val();
		var end_date = $('#end_date').val();
		
		$.ajax({
		  type: 'get',
		  dataType:"html",
		  url: '<?= base_url('pos/shift/listshift/?start_date='.$start_date.'&end_date='.$end_date) ?>',
		  success: function (res) {
			  $('.view_shift').html(res);
			  $('#shiftView').modal({backdrop: 'static', keyboard: false});
		  }
		});
		
	});
	$(document).on('change', '#counter_name', function(){
		var site_url = '<?= base_url(); ?>';
		var counter_name = $(this).val();
		$.ajax({
		  type: 'get',
		  dataType:"json",
		  url: '<?= base_url('pos/shift/checkcounter/') ?>'+counter_name,
		  success: function (res) {
			 if(res.status == 'success'){
				 bootbox.alert({
					message: "Available Counter",
					size: 'large',
					callback: function(){	
						window.location = site_url+'pos/shift/shift_settlement/'+counter_name;		
					}
				});
			 }else{
				bootbox.alert({
					message: "No Shift in Counter",
					callback: function(){	
						window.location = site_url+'pos/shift/shift_settlement/';		
					}
				}); 
			 }
		  }
		});
	});
	
	function checkNum(input) {
		input.value = input.value.match(/^\d+\d{0,1}/);  
	}
	</script>
<?php 
//echo $this->load->view($this->theme.'pos_v2/shift/shift_popup'); 
?>
</body>
</html>
