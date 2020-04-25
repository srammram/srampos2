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
   	<link rel="stylesheet" href="<?=$assets?>styles/helpers/jquery-ui.css" type="text/css">
   	<link rel="stylesheet" href="<?=$assets?>styles/home_frontend.css" type="text/css">
	<style>
		.menu_nav li a figure{width: 10.7%;}
	</style>
    
</head>
<body>
	<header class="logo_header">
		<div class="container-fluid">
			<div class="row">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<a href="#"><img src="<?=$assets?>images/srampos.png" alt=""></a>
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
							<a href="#">
								<figure class="text-center">
									<div class="img_block">
										<img src="<?=$assets?>images/sprite/new_split.png">
										<figcaption>New Split</figcaption>
									</div>
								</figure>
							</a>
						</li>
						<li>
							<a href="#">
								<figure class="text-center">
									<div class="img_block">
										<img src="<?=$assets?>images/sprite/change_table.png">
										<figcaption>Change Table</figcaption>
									</div>
									
								</figure>
							</a>
						</li>
						<li>
							<a href="#">
								<figure class="text-center">
									<div class="img_block">
										<img src="<?=$assets?>images/sprite/change_customer.png">
										<figcaption>Change customer</figcaption>
									</div>
									
								</figure>
							</a>
						</li>
						<li>
							<a href="#">
								<figure class="text-center">
									<div class="img_block">
										<img src="<?=$assets?>images/sprite/merge_bill.png">
										<figcaption>Merge Bill</figcaption>
									</div>
									
								</figure>
							</a>
						</li>
						<li>
							<a href="#">
								<figure class="text-center">
									<div class="img_block">
										<img src="<?=$assets?>images/sprite/new_order_item.png">
										<figcaption>New order Item</figcaption>
									</div>
								</figure>
							</a>
						</li>
						<li>
							<a href="#">
								<figure class="text-center">
									<div class="img_block">
										<img src="<?=$assets?>images/sprite/print.png">
										<figcaption>KoT print</figcaption>
									</div>
								</figure>
							</a>
						</li>
						<li>
							<a href="#">
								<figure class="text-center">
									<div class="img_block">
										<img src="<?=$assets?>images/sprite/cancel_all.png">
										<figcaption>Cancel All</figcaption>
									</div>
								</figure>
							</a>
						</li>
						<li>
							<a href="#">
								<figure class="text-center">
									<div class="img_block">
										<img src="<?=$assets?>images/sprite/bill_generator.png">
										<figcaption>Bill generator</figcaption>
									</div>
								</figure>
							</a>
						</li>
						<li>
							<a href="#">
								<figure class="text-center">
									<div class="img_block">
										<img src="<?=$assets?>images/sprite/exit.png">
										<figcaption>Exit</figcaption>
									</div>
								</figure>
							</a>
						</li>
					</ul>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-left payment_s">
					<ul>
						<li>
							<table>
								<tr>
									<td>
										<p>GROUND FLOOR/ TABLE 2</p>
										<p>SALES 20191215141246</p>
									</td>
									<td>
										<a href="#">
											<figure class="text-center">
												<img src="<?=$assets?>images/sprite/payment_y.png">
												<figcaption>Payment</figcaption>
											</figure>
										</a>
									</td>
									<td>
										<a href="#">
											<figure class="text-center">
												<img src="<?=$assets?>images/sprite/invoice.png">
												<figcaption>Rough Tender</figcaption>
											</figure>
										</a>
									</td>
									<td>
										<a href="#">
											<figure class="text-center">
												<img src="<?=$assets?>images/sprite/print.png">
												<figcaption>Bill Print</figcaption>
											</figure>
										</a>
									</td>
								</tr>
							</table>
						</li>
						<li>
							<table>
								<tr>
									<td>
										<p>GROUND FLOOR/ TABLE 2</p>
										<p>SALES 20191215141246</p>
									</td>
									<td>
										<a href="#">
											<figure class="text-center">
												<img src="<?=$assets?>images/sprite/payment_y.png">
												<figcaption>Payment</figcaption>
											</figure>
										</a>
									</td>
									<td>
										<a href="#">
											<figure class="text-center">
												<img src="<?=$assets?>images/sprite/invoice.png">
												<figcaption>Rough Tender</figcaption>
											</figure>
										</a>
									</td>
									<td>
										<a href="#">
											<figure class="text-center">
												<img src="<?=$assets?>images/sprite/print.png">
												<figcaption>Bill Print</figcaption>
											</figure>
										</a>
									</td>
								</tr>
							</table>
						</li>
						
					</ul>
				</div>
			</div>
		</div>
	</section>
	<section>
		<div class="container">
			<div class="row">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding: 0px;">
<!--table-->
		<div class="col-xs-12" id="bill_generation" style="padding: 0px;">
			<table class="table table_item_ls table_bill_list">
				<thead>
					<tr>
						<th>Cancel</th>
						<th>Sale Item</th>
						<th>Price</th>
						<th>Qty</th>
						<th>Item Discount</th>
						<th>Customer Discount %</th>
						<th>Discount</th>
						<th>Subtotal</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><button type="button" class="btn btn_remove"><i class="fa fa-remove"></i></button></td>
						<td>Spicy Kaffir lime Spicy KaffirSpicy Kaffir </td>
						<td>25.00</td>
						<td>
							<div class="number_se">
								<span class="minus">-</span>
								<input type="text" class="numberfocus" value="0" name="no_of_adult" id="no_of_adult">
								<span class="plus">+</span>
							</div>
						</td>
						<td><input type="text" value="0.00" class="form-control"></td>
						<td><input type="text" value="0.00" class="form-control"></td>
						<td><input type="text" value="0.00" class="form-control"></td>
						<td><input type="text" value="25.00" class="form-control"></td>
					</tr>
					<tr>
						<td><button type="button" class="btn btn_remove"><i class="fa fa-remove"></i></button></td>
						<td>Spicy Kaffir lime Spicy KaffirSpicy Kaffir </td>
						<td>25.00</td>
						<td>
							<div class="number_se">
								<span class="minus">-</span>
								<input type="text" class="numberfocus" value="0" name="no_of_adult" id="no_of_adult">
								<span class="plus">+</span>
							</div>
						</td>
						<td><input type="text" value="0.00" class="form-control"></td>
						<td><input type="text" value="0.00" class="form-control"></td>
						<td><input type="text" value="0.00" class="form-control"></td>
						<td><input type="text" value="25.00" class="form-control"></td>
					</tr>
					<tr>
						<td><button type="button" class="btn btn_remove"><i class="fa fa-remove"></i></button></td>
						<td>Spicy Kaffir lime Spicy KaffirSpicy Kaffir </td>
						<td>25.00</td>
						<td>
							<div class="number_se">
								<span class="minus">-</span>
								<input type="text" class="numberfocus" value="0" name="no_of_adult" id="no_of_adult">
								<span class="plus">+</span>
							</div>
						</td>
						<td><input type="text" value="0.00" class="form-control"></td>
						<td><input type="text" value="0.00" class="form-control"></td>
						<td><input type="text" value="0.00" class="form-control"></td>
						<td><input type="text" value="25.00" class="form-control"></td>
					</tr>
				</tbody>
			</table>
		</div>
			<table class="table table_bottom_s">
				<tbody>
					<tr>
						<td align="right";>
							<table>
								<tr>
									<td>Total Items : </td>
									<td><input type="text" value="1" class="form-control"></td>
								</tr>
							</table>
						</td>
						<td align="right">
							<table>
								<tr>
									<td>Total Items : </td>
									<td><input type="text" value="$ 25000.00" class="form-control"></td>
								</tr>
							</table>
						</td>
					</tr>
				</tbody>
			</table>
			<table class="table table_last_s">
				<tbody>
					<tr>
						<td align="right";>
							<table>
								<tr>
									<td valign="top";>Customer Discount</td>
									<td style="text-align: right;">
										<table>
											<tr>
												<td style="text-align: right;">
													<table>
														<tr>
															<td>
																<select class="form-control">
																	<option>5%</option>
																	<option>10%</option>
																</select>
															</td>
															<td><input type="text" value="$0.00" class="form-control"></td>
														</tr>
														<tr>
															<td>Sub Total : </td>
															<td><input type="text" value="$25.00" class="form-control"></td>
														</tr>
														<tr>
															<td>Grand Total	: </td>
															<td><input type="text" value="$25.00" class="form-control"></td>
														</tr>
													</table>
												</td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
						</td>
						
					</tr>
				</tbody>
			</table>
		
				</div>
			</div>
    	</div>
	</section>
	
	
<!--scripts-->
<script type="text/javascript" src="<?=$assets?>js/jquery-2.0.3.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/jquery.mCustomScrollbar.concat.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/jquery-ui.js"></script>
<script type="text/javascript" src="<?=$assets?>js/perfect-scrollbar.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/select2.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/custom.js"></script>
<script type="text/javascript" src="<?=$assets?>js/jquery.calculator.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/bootstrapValidator.min.js"></script>
<script type="text/javascript" src="<?=$assets?>pos/js/plugins.min.js"></script>
<script type="text/javascript" src="<?=$assets?>pos/js/parse-track-data.js"></script>
<!--<script type="text/javascript" src="<?=$assets?>pos/js/pos.ajax.js?v=1"></script>-->
<script type="text/javascript" src="<?=$assets?>pos/js/pos_consolidate.ajax.js?v=1"></script>
	<script>
	$(function(){
		$("#bill_generation").mCustomScrollbar({
			 theme:"dark-3" ,
		});
	
        });
	</script>
</body>
</html>
