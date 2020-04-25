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
	<header class="logo_header">
		<div class="container-fluid">
			<div class="row">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<a href="<?php echo base_url('pos/pos/'); ?>"><img src="<?=$assets?>images/srampos.png" alt=""></a>
				</div>
			</div>
		</div>
	</header>
	<section class="pos_bottom_s">
		<div class="container">
			<div class="row">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-left header_sec_me">
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

    
	<section class="shift_management_h">
		<div class="container">
         
           <div class="row">
           <div class="col-sm-12">
           	<h3 class="text-center">Shift Settlement View
            
            <?php
			if($GP['pos-shift_settlement'] == 1 || $this->Owner || $this->Admin){
			?>
            <a href="<?= base_url('pos/shift/shift_settlement') ?>">
			<button class="btn btn-danger pull-right">
				
				Back
				
			</button>
            </a>
            <?php
			}
			?>
            
            </h3>
           </div>
           	<div class="col-sm-12 col-xs-12 shift_management">
           		
                <?php
				//print_r($settlement);
				?>
           		<div class="col-sm-3 col-xs-12">
                
           		<legend class="currency_na">USD</legend>
           			<table class="table">
           				<tbody>
           					<tr>
           						<td><input type="text" readonly value="500x"></td>
           						<td><input type="text" name="USD_500" id="USD_500" value="<?= $settlement['USD_500'] ?>" ></td>
           						<td><input type="text" name="TOTALUSD_500" id="TOTALUSD_500" class="TOTALUSD" value="<?= $settlement['USD_500'] * 500 ?>"  readonly></td>
           					</tr>
           					<tr>
           						<td><input type="text" readonly value="200x"></td>
           						<td><input type="text" name="USD_200" id="USD_200" value="<?= $settlement['USD_200'] ?>"></td>
           						<td><input type="text" name="TOTALUSD_200" id="TOTALUSD_200" class="TOTALUSD"  value="<?= $settlement['USD_200'] * 200 ?>" readonly></td>
           					</tr>
           					<tr>
           						<td><input type="text" readonly value="100x"></td>
           						<td><input type="text" name="USD_100" id="USD_100" value="<?= $settlement['USD_100'] ?>"></td>
           						<td><input type="text" name="TOTALUSD_100" id="TOTALUSD_100" class="TOTALUSD"  value="<?= $settlement['USD_100'] * 100 ?>" readonly></td>
           					</tr>
           					<tr>
           						<td><input type="text" readonly value="50x"></td>
           						<td><input type="text" name="USD_50" id="USD_50" value="<?= $settlement['USD_50'] ?>"></td>
           						<td><input type="text" name="TOTALUSD_50" id="TOTALUSD_50" class="TOTALUSD" value="<?= $settlement['USD_50'] * 50 ?>"  readonly></td>
           					</tr>
           					<tr>
           						<td><input type="text" readonly value="20x"></td>
           						<td><input type="text" name="USD_20" id="USD_20" value="<?= $settlement['USD_20'] ?>"></td>
           						<td><input type="text" name="TOTALUSD_20" id="TOTALUSD_20" class="TOTALUSD" value="<?= $settlement['USD_20'] * 20 ?>"  readonly></td>
           					</tr>
           					<tr>
           						<td><input type="text" readonly value="10x"></td>
           						<td><input type="text" name="USD_10" id="USD_10" value="<?= $settlement['USD_10'] ?>"></td>
           						<td><input type="text" name="TOTALUSD_10" id="TOTALUSD_10" class="TOTALUSD"  value="<?= $settlement['USD_10'] * 10 ?>" readonly></td>
           					</tr>
           					<tr>
           						<td><input type="text" readonly value="5x"></td>
           						<td><input type="text" name="USD_5" id="USD_5" value="<?= $settlement['USD_5'] ?>"></td>
           						<td><input type="text" name="TOTALUSD_5" id="TOTALUSD_5" class="TOTALUSD" value="<?= $settlement['USD_5'] * 5 ?>" readonly></td>
           					</tr>
           					<tr>
           						<td><input type="text" readonly value="2x"></td>
           						<td><input type="text" name="USD_2" id="USD_2" value="<?= $settlement['USD_2'] ?>"></td>
           						<td><input type="text" name="TOTALUSD_2" id="TOTALUSD_2" class="TOTALUSD" value="<?= $settlement['USD_2'] * 2 ?>" readonly></td>
           					</tr>
           					<tr>
           						<td><input type="text" readonly value="1x"></td>
           						<td><input type="text" name="USD_1" id="USD_1" value="<?= $settlement['USD_1'] ?>"></td>
           						<td><input type="text" name="TOTALUSD_1" id="TOTALUSD_1" class="TOTALUSD" value="<?= $settlement['USD_1'] * 1 ?>"  readonly></td>
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
           						<td><input type="text" name="KHR_50000" id="KHR_50000" value="<?= $settlement['KHR_50000'] ?>"></td>
           						<td><input type="text" name="TOTALKHR_50000" id="TOTALKHR_50000" class="TOTALKHR" value="<?= $settlement['KHR_50000'] * 50000 ?>" readonly></td>
           					</tr>
           					<tr>
           						<td><input type="text" readonly value="10000x"></td>
           						<td><input type="text" name="KHR_10000" id="KHR_10000" value="<?= $settlement['KHR_10000'] ?>"></td>
           						<td><input type="text" name="TOTALKHR_10000" id="TOTALKHR_10000" class="TOTALKHR" value="<?= $settlement['KHR_10000'] * 10000 ?>" readonly></td>
           					</tr>
           					<tr>
           						<td><input type="text" readonly value="5000x"></td>
           						<td><input type="text" name="KHR_5000" id="KHR_5000" value="<?= $settlement['KHR_5000'] ?>"></td>
           						<td><input type="text" name="TOTALKHR_5000" id="TOTALKHR_5000" class="TOTALKHR" value="<?= $settlement['KHR_5000'] * 5000 ?>" readonly></td>
           					</tr>
           					<tr>
           						<td><input type="text" readonly value="2000x"></td>
           						<td><input type="text" name="KHR_2000" id="KHR_2000" value="<?= $settlement['KHR_2000'] ?>"></td>
           						<td><input type="text" name="TOTALKHR_2000" id="TOTALKHR_2000" class="TOTALKHR" value="<?= $settlement['KHR_2000'] * 2000 ?>"  readonly></td>
           					</tr>
           					<tr>
           						<td><input type="text" readonly value="1000x"></td>
           						<td><input type="text" name="KHR_1000" id="KHR_1000" value="<?= $settlement['KHR_1000'] ?>"></td>
           						<td><input type="text" name="TOTALKHR_1000" id="TOTALKHR_1000" class="TOTALKHR" value="<?= $settlement['KHR_1000'] * 1000 ?>" readonly></td>
           					</tr>
           					<tr>
           						<td><input type="text" readonly value="100x"></td>
           						<td><input type="text" name="KHR_100" id="KHR_100" value="<?= $settlement['KHR_100'] ?>"></td>
           						<td><input type="text" name="TOTALKHR_100" id="TOTALKHR_100" class="TOTALKHR" value="<?= $settlement['KHR_100'] * 100 ?>"  readonly></td>
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
                        	
                        	
           					<tr>
           						<td rowspan="3" align="left" valign="middle" style="vertical-align: middle;" >Cash</td>
                                
           						<td>
           							<table>
                                    	<?php foreach($this->currencies as $c => $cur){  ?>
           								<tr>
           									<td><?=$cur->code?> : <span><?= number_format($settlement['opening_cash_'.$cur->code], 2) ?></span></td>
           								</tr>
           								<?php } ?>
                                        
           								
           							</table>
                                    
           						</td>
                                
                                <td>
           							<table>
                                    	<?php foreach($this->currencies as $c => $cur){  ?>
           								<tr class="<?= $cur->code == 'KHR' ? 'hidden' : '' ?>">
           									<td><?=$cur->code?> : <span><?= number_format($settlement['cash_'.$cur->code.'_actual'], 2) ?></span></td>
           								</tr>
           								<?php } ?>
                                        
           							</table>
           						</td>
                                
                                 <td>
           							<table>
                                    	<?php foreach($this->currencies as $c => $cur){  ?>
           								<tr>
           									<td><?=$cur->code?> : <span><?= number_format($settlement['cash_'.$cur->code.'_received'], 2) ?></span></td>
           								</tr>
           								<?php } ?>
           							</table>
           						</td>
                                
                                <td>
           							<table>
                                    	<?php foreach($this->currencies as $c => $cur){  ?>
           								<tr>
           									<td><?=$cur->code?> : <span><?= number_format($settlement['cash_'.$cur->code.'_difference'], 2) ?></span></td>
           								</tr>
           								<?php } ?>
           							</table>
           						</td>
           						
           						
           						
           					</tr>
                            
                           
                       </tbody>
                       <tbody>     
                            <tr  class="hidden">
                            	
           						<td colspan="2" align="left" valign="middle" style="vertical-align: middle;" >Giftvoucher
                                </td>
                                <td><span><?= number_format($settlement['giftvoucher_actual'], 2) ?></span></td>
                                <td><span><?= number_format($settlement['giftvoucher_received'], 2) ?></span></td>
                                <td><span><?= number_format($settlement['giftvoucher_difference'], 2) ?></span></td>
                                
                            </tr>
                            <tr>
                            	
           						<td colspan="2"  align="left" valign="middle" style="vertical-align: middle;" >Card</td>
                                <td><span><?= number_format($settlement['card_actual'], 2) ?></span></td>
                                <td><span><?= number_format($settlement['card_received'], 2) ?></span></td>
                                <td><span><?= number_format($settlement['card_difference'], 2) ?></span></td>
                                
                            </tr>
                            <tr  class="hidden">
                            	
           						<td colspan="2"  align="left" valign="middle" style="vertical-align: middle;" >Wallet</td>
                                <td><span><?= number_format($settlement['wallet_actual'], 2) ?></span></td>
                                <td><span><?= number_format($settlement['wallet_received'], 2) ?></span></td>
                                <td><span><?= number_format($settlement['wallet_difference'], 2) ?></span></td>
                                
                            </tr>
                           
                        </tbody>    
           				
           			</table>
           		</div>
           	</div>
           </div>
    	</div>
	</section>
	<section class="foot_shift_manage">
    	<div class="container">
    		<div class="row">
    			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 top_foot">
    				<table class="table">
    					<tbody>
    						<tr>
    							<td>Open Til : <?= $settlement['till_name'] ?> </td>
    							<td>
    								<table>
    									<tr>
    										<td>Settlement Date :</td>
    										<td><?= $settlement['created_on'] ?></td>
    									</tr>
    								</table>
    							</td>
    							
    							<td>
    								<table>
    									<tr>
    										<td>Shift Name :</td>
    										<td>
												<?= $settlement['shift_name'] ?>
   											</td>
    									</tr>
    								</table>
    							</td>
                                <td>
    								<table>
    									<tr>
    										<td>Created By :</td>
    										<td>
												<?= $settlement['created_name'] ?>
   											</td>
    									</tr>
    								</table>
    							</td>
    							<td>User: 
                                <?php
								if($settlement['assigned_name'] != ''){
									echo $settlement['assigned_name'];
								}else{
									echo 'All Empolyee';
								}
								?>
                                </td>
                                <?php
								if($GP['pos-shift_reprint'] == 1 || $this->Owner || $this->Admin){
								?>
    							<td>
                                	<button type="button" class="btn btn-danger" id="reprint-settlement">Reprint</button>
                                </td>
                                <?php
								}
								?>
    						</tr>
    					</tbody>
    				</table>
    			</div>
    		</div>

    	</div>
	</section>

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
	
	function calculateWithdrawAmount($cur_code) {
		$amt = 0;
		$('.TOTAL'+$cur_code).each(function(n,v){
			$v = ($(this).val()=='')?0:$(this).val();
			$amt += parseFloat($v); 
		});
		$('#WITHDRAW'+$cur_code).val($amt.toFixed(2));
	}
	$(document).ready(function(e) {
		<?php foreach($this->currencies as $c => $cur) { ?>
        calculateWithdrawAmount('<?=$cur->code?>');
		<?php } ?>		
    });
	
	$('#reprint-settlement').click(function(){
		var base_url = '<?= base_url('pos/shift/add_settlement') ?>';
		bootbox.confirm("Are you sure want to Reprint Settlement ?", function(result){
			if(result){
				
				$.ajax({
				  type: 'get',
				  dataType:"html",
				  url: '<?= base_url('pos/shift/get_shift_data/'.$settlement['sid'].'/1?reprint='.$settlement['reprint']) ?>',
				  success: function (res) {
					  print_popupsettlement(res,true);
					 
				  }
				});
			}
		})
	});
	
	</script>
<?php 
echo $this->load->view($this->theme.'pos_v2/shift/shift_popup'); 
?>
</body>
</html>
