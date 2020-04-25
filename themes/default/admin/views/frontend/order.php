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
   	<link rel="stylesheet" href="<?=$assets?>styles/home_frontend.css" type="text/css">
    
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
										<img src="<?=$assets?>images/sprite/save_order.png">
										<figcaption>Save order</figcaption>
									</div>
								</figure>
							</a>
						</li>
						<li>
							<a href="#">
								<figure class="text-center">
									<div class="img_block">
										<img src="<?=$assets?>images/sprite/invoice.png">
										<figcaption>Cancel Order</figcaption>
									</div>
									
								</figure>
							</a>
						</li>
						<li>
								<table class="table tab_sr_s">
									<tbody>
										<tr>
										<td>
											<table>
												<colgroup>
													<col width="30%">
													<col width="50%">
												</colgroup>
												<tr>
													<td>Table</td>
													<td><input type="text" class="form-control" value="TAB 31"></td>
												</tr>
												<tr>
													<td>User Name</td>
													<td>
														<div class="input-group">

														<input type="text" class="form-control" value="Mani">
														<div class="input-group-addon">
															<a href="#"><i class="fa fa-pencil"></i></a>
														</div>
														<div class="input-group-addon">
															<a href="#"><i class="fa fa-eye"></i></a>
														</div>
														<div class="input-group-addon">
															<a href="#"> <i class="fa fa-plus-circle"></i></a>
														</div>
													  </div>

													</td>
												</tr>
											</table>
										</td>
										<td>
											<table>
												<colgroup>
													<col width="30%">
													<col width="70%">
												</colgroup>
												<tr>
													<td>Persons</td>
													<td><input type="text" class="form-control" value="TAB 31"></td>
												</tr>
											</table>
										</td>
									</tr>
									</tbody>

								</table>
						</li>
						<li>
							<a href="#">
								<figure class="text-center pull-right">
									<div class="img_block">
										<img src="<?=$assets?>images/sprite/back.png">
										<figcaption>Back</figcaption>
									</div>
									
								</figure>
							</a>
						</li>
						
					</ul>
				</div>
			</div>
		</div>
	</section>
	<section>
		<div class="container-fluid">
			<div class="row">
				<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
					<div class="head_left_order">
						<table class="table top_left_order">
						<colgroup>
							<col width="40%">
							<col width="60%">
						</colgroup>
							<tbody>
								<tr>
									<td>
										<input type="text" class="form-control" value="Code...">
									</td>
									<td>
										<input type="text" class="form-control" value="Item Name...">
									</td>
								</tr>
							</tbody>
						</table>
						<table class="table table_middle_s">
							<tbody>
								<tr>
									<td>
										<ul>
											<li>
												<div class="item"><button type="button" class="btn btn_default">Royal Suki Set</button></div>
											</li>
											<li>
												<div class="item"><button type="button" class="btn btn_default">Vegtables, Meat & Meetball</button></div>
											</li>
											<li>
												<div class="item"><button type="button" class="btn btn_default">Noodles & Steamed Rice</button></div>
											</li>
											<li>
												<div class="item"><button type="button" class="btn btn_default">Khmer-Thai Foods</button></div>
											</li>
											<li>
												<div class="item"><button type="button" class="btn btn_default">resh Beverages</button></div>
											</li>
											<li>
												<div class="item"><button type="button" class="btn btn_default">Alcohol</button></div>
											</li>
											<li>
												<div class="item"><button type="button" class="btn btn_default">Europe</button></div>
											</li>
											<li>
												<div class="item"><button type="button" class="btn btn_default">Europe</button></div>
											</li>
											<li>
												<div class="item"><button type="button" class="btn btn_default">Royal Suki Set</button></div>
											</li>

										</ul>

									</td>
								</tr>
							</tbody>
						</table>
						<table class="table table_bottom_s">
							<tbody>
								<tr>
									<td>
										<ul>
											<li>
												<div class="item"><button type="button" class="btn btn_lightred">Royal Suki Set</button></div>
											</li>
											<li>
												<div class="item"><button type="button" class="btn btn_lightred">Royal Suki Set</button></div>
											</li>
											<li>
												<div class="item"><button type="button" class="btn btn_lightred">Royal Suki Set</button></div>
											</li>
											<li>
												<div class="item"><button type="button" class="btn btn_lightred">Royal Suki Set</button></div>
											</li>
											<li>
												<div class="item"><button type="button" class="btn btn_lightred">Royal Suki Set</button></div>
											</li>
											<li>
												<div class="item"><button type="button" class="btn btn_lightred">Royal Suki Set</button></div>
											</li>
											<li>
												<div class="item"><button type="button" class="btn btn_lightred">Royal Suki Set</button></div>
											</li>
											<li>
												<div class="item"><button type="button" class="btn btn_lightred">Royal Suki Set</button></div>
											</li>
											<li>
												<div class="item"><button type="button" class="btn btn_lightred">Royal Suki Set</button></div>
											</li>

										</ul>

									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
				<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
					<div class="tcb-simple-carousel">
						<div id="myCarousel" class="carousel slide" data-interval="false">
								<div class="carousel-inner">           
									<div class="item active">
										<button type="button" class="btn btn_item">
											<figure>
												<img src="<?=$assets?>images/sprite/kemo.png">
												<figcaption>07 Loremipsum ias ldy</figcaption>
											</figure>
										</button>
										<button type="button" class="btn btn_item">
											<figure>
												<img src="<?=$assets?>images/sprite/kemo.png">
												<figcaption>07 Loremipsum ias ldy</figcaption>
											</figure>
										</button>
										<button type="button" class="btn btn_item btn_yellow_s">
											<figure>
												<img src="<?=$assets?>images/sprite/kemo.png">
												<figcaption>07 Loremipsum ias ldy</figcaption>
											</figure>
										</button>
										<button type="button" class="btn btn_item">
											<figure>
												<img src="<?=$assets?>images/sprite/kemo.png">
												<figcaption>07 Loremipsum ias ldy</figcaption>
											</figure>
										</button>
										<button type="button" class="btn btn_item">
											<figure>
												<img src="<?=$assets?>images/sprite/kemo.png">
												<figcaption>07 Loremipsum ias ldy</figcaption>
											</figure>
										</button>
										<button type="button" class="btn btn_item">
											<figure>
												<img src="<?=$assets?>images/sprite/kemo.png">
												<figcaption>07 Loremipsum ias ldy</figcaption>
											</figure>
										</button>
										<button type="button" class="btn btn_item">
											<figure>
												<img src="<?=$assets?>images/sprite/kemo.png">
												<figcaption>07 Loremipsum ias ldy</figcaption>
											</figure>
										</button>
										<button type="button" class="btn btn_item">
											<figure>
												<img src="<?=$assets?>images/sprite/kemo.png">
												<figcaption>07 Loremipsum ias ldy</figcaption>
											</figure>
										</button>
										<button type="button" class="btn btn_item">
											<figure>
												<img src="<?=$assets?>images/sprite/kemo.png">
												<figcaption>07 Loremipsum ias ldy</figcaption>
											</figure>
										</button>
										<button type="button" class="btn btn_item">
											<figure>
												<img src="<?=$assets?>images/sprite/kemo.png">
												<figcaption>07 Loremipsum ias ldy</figcaption>
											</figure>
										</button>
									
									</div>
									<div class="item">
										<button type="button" class="btn btn_item">
											<figure>
												<img src="<?=$assets?>images/sprite/kemo.png">
												<figcaption>07 Loremipsum ias ldy</figcaption>
											</figure>
										</button>
										<button type="button" class="btn btn_item">
											<figure>
												<img src="<?=$assets?>images/sprite/kemo.png">
												<figcaption>07 Loremipsum ias ldy</figcaption>
											</figure>
										</button>
										<button type="button" class="btn btn_item">
											<figure>
												<img src="<?=$assets?>images/sprite/kemo.png">
												<figcaption>07 Loremipsum ias ldy</figcaption>
											</figure>
										</button>
										<button type="button" class="btn btn_item">
											<figure>
												<img src="<?=$assets?>images/sprite/kemo.png">
												<figcaption>07 Loremipsum ias ldy</figcaption>
											</figure>
										</button>
										<button type="button" class="btn btn_item">
											<figure>
												<img src="<?=$assets?>images/sprite/kemo.png">
												<figcaption>07 Loremipsum ias ldy</figcaption>
											</figure>
										</button>
										<button type="button" class="btn btn_item">
											<figure>
												<img src="<?=$assets?>images/sprite/kemo.png">
												<figcaption>07 Loremipsum ias ldy</figcaption>
											</figure>
										</button>
										<button type="button" class="btn btn_item">
											<figure>
												<img src="<?=$assets?>images/sprite/kemo.png">
												<figcaption>07 Loremipsum ias ldy</figcaption>
											</figure>
										</button>
										<button type="button" class="btn btn_item">
											<figure>
												<img src="<?=$assets?>images/sprite/kemo.png">
												<figcaption>07 Loremipsum ias ldy</figcaption>
											</figure>
										</button>
										<button type="button" class="btn btn_item">
											<figure>
												<img src="<?=$assets?>images/sprite/kemo.png">
												<figcaption>07 Loremipsum ias ldy</figcaption>
											</figure>
										</button>
										<button type="button" class="btn btn_item">
											<figure>
												<img src="<?=$assets?>images/sprite/kemo.png">
												<figcaption>07 Loremipsum ias ldy</figcaption>
											</figure>
										</button>
									 </div>  
								</div> 
								
<!--
							   <div class="carousel-controls">
								  <a class="carousel-control left" href="#myCarousel" data-slide="prev"><span class="fa fa-angle-double-left"></span></a>
								  <a class="carousel-control right" href="#myCarousel" data-slide="next"><span class="fa fa-angle-double-right"></span></a>
							  </div>               
-->
							</div>
					</div>
					<div class="clearfix"></div>
<!--table-->
			<table class="table table_item_ls">
				<thead>
					<tr>
						<th><span><button type="button" class="btn btn_addon">Add Ons</button></span>Name</th>
						<th>Price</th>
						<th>Qty</th>
						<th>Dis %</th>
						<th>Dis $/R</th>
						<th>Amount</th>
						<th>Delect</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>01 Lorem ipsum</td>
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
						<td><input type="text" value="25.00" class="form-control"></td>
						<td><button type="button" class="btn btn_remove"><i class="fa fa-remove"></i></button></td>
					</tr>
					<tr>
						<td>01 Lorem ipsum</td>
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
						<td><input type="text" value="25.00" class="form-control"></td>
						<td><button type="button" class="btn btn_remove"><i class="fa fa-remove"></i></button></td>
					</tr>
					<tr>
						<td>01 Lorem ipsum</td>
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
						<td><input type="text" value="25.00" class="form-control"></td>
						<td><button type="button" class="btn btn_remove"><i class="fa fa-remove"></i></button></td>
					</tr>
				</tbody>
			</table>
				</div>
			</div>
    	</div>
	</section>
	
	
<!--scripts-->
	<script type="text/javascript" src="<?=$assets?>js/jquery-2.0.3.min.js"></script>
	<script type="text/javascript" src="<?=$assets?>js/bootstrap.min.js"></script>
	<script type="text/javascript" src="<?=$assets?>js/jquery-ui.min.js"></script>
	<script type="text/javascript" src="<?=$assets?>js/select2.min.js"></script>
	<script type="text/javascript" src="<?=$assets?>js/jquery.dataTables.min.js"></script>
	<script type="text/javascript" src="<?=$assets?>js/bootstrapValidator.min.js"></script>
	<script>
		$(document).ready(function(){
			$('#myCarousel,#myCarousel1,#myCarousel2').carousel();
		});
	</script>
	<script>
		$(document).ready(function() {
			$('.minus').click(function () {
				var $input = $(this).parent().find('input');
				var count = parseInt($input.val()) - 1;
				count = count < 0 ? 0 : count;
				$input.val(count);
				$input.change();
				return false;
			});
			$('.plus').click(function () {
				var $input = $(this).parent().find('input');
				$input.val(parseInt($input.val()) + 1);
				$input.change();
				return false;
			});
		});
	</script>
</body>
</html>
