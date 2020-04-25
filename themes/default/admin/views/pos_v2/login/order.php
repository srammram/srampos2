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
										<img src="<?=$assets?>images/sprite/order.png">
										<figcaption>Order</figcaption>
									</div>
								</figure>
							</a>
						</li>
						<li>
							<a href="#">
								<figure class="text-center">
									<div class="img_block">
										<img src="<?=$assets?>images/sprite/invoice.png">
										<figcaption>Invoice</figcaption>
									</div>
									
								</figure>
							</a>
						</li>
						<li>
							<a href="#">
								<figure class="text-center">
									<div class="img_block">
										<img src="<?=$assets?>images/sprite/payment.png">
										<figcaption>Payment</figcaption>
									</div>
									
								</figure>
							</a>
						</li>
						<li>
							<a href="#">
								<figure class="text-center">
									<div class="img_block">
										<img src="<?=$assets?>images/sprite/table_changing.png">
										<figcaption>Table Changing</figcaption>
									</div>
									
								</figure>
							</a>
						</li>
						<li>
							<a href="#">
								<figure class="text-center">
									<div class="img_block">
										<img src="<?=$assets?>images/sprite/room_change.png">
										<figcaption>Room Change</figcaption>
									</div>
									
								</figure>
							</a>
						</li>
						<li>
							<a href="#">
								<figure class="text-center">
									<div class="img_block">
										<img src="<?=$assets?>images/sprite/cashier.png">
										<figcaption>Cashier</figcaption>
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
		</div>
		<div class="container">
		<div class="row">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-left slider_sec">
				<div id="exTab1">	
					<ul class="nav nav-pills">
						<li class="active"><a href="#1a" data-toggle="tab" tabindex="-1">GROUND FLOOR</a></li>				
						<li class=""><a href="#2a" data-toggle="tab" tabindex="-1">First Floor</a></li>				
						<li class=""><a href="#3a" data-toggle="tab" tabindex="-1">Second Floor</a></li>				
					</ul>
					<div class="tab-content clearfix">
						<div class="tab-pane active" id="1a">
						   <div class="tableright">
						   		<div class="tcb-simple-carousel">
									<div id="myCarousel" class="carousel slide" data-interval="false">
										<div class="carousel-inner">           
											<div class="item active">
												<button type="button" class="btn btn-default btn_violet btn_orange">
													<span class="number_s">01</span>
													<p class="timer">00 H 30 M 12 S</p>
												</button>
												<button type="button" class="btn btn-default btn_violet btn_orange">
													<span class="number_s">02</span>
													<p class="timer">00 H 30 M 12 S</p>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">03</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">04</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">05</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">06</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">07</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">08</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">09</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">10</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">11</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">12</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">13</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">14</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">15</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">16</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">17</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">18</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">19</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">20</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">21</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">22</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">23</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">24</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">25</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">26</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">27</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">28</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">29</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">30</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">31</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">32</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">33</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">34</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">35</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">36</span>
												</button>
											</div>
											<div class="item">
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">01</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">02</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">03</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">04</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">05</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">06</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">07</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">08</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">09</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">10</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">11</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">12</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">13</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">14</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">15</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">16</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">17</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">18</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">19</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">20</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">21</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">22</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">23</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">24</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">25</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">26</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">27</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">28</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">29</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">30</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">31</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">32</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">33</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">34</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">35</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">36</span>
												</button>
											 </div>  
										</div> 
									   <div class="carousel-controls">
										  <a class="carousel-control left" href="#myCarousel" data-slide="prev"><span class="fa fa-angle-double-left"></span></a>
										  <a class="carousel-control right" href="#myCarousel" data-slide="next"><span class="fa fa-angle-double-right"></span></a>
									  </div>               
									</div>
								</div>
						   </div>
						</div>
						<div class="tab-pane" id="2a">
							<div class="tableright">
								<div class="tcb-simple-carousel">
									<div id="myCarousel1" class="carousel slide" data-interval="false">
										<div class="carousel-inner">           
											<div class="item active">
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">01</span>
												</button>
												<button type="button" class="btn btn-default btn_violet btn_orange">
													<span class="number_s">02</span>
													<p class="timer">00 H 30 M 12 S</p>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">03</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">04</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">05</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">06</span>
												</button>
												<button type="button" class="btn btn-default btn_violet btn_orange">
													<span class="number_s">07</span>
													<p class="timer">00 H 30 M 12 S</p>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">08</span>
												</button>
												<button type="button" class="btn btn-default btn_violet btn_orange">
													<span class="number_s">09</span>
													<p class="timer">00 H 30 M 12 S</p>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">10</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">11</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">12</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">13</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">14</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">15</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">16</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">17</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">18</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">19</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">20</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">21</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">22</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">23</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">24</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">25</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">26</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">27</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">28</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">29</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">30</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">31</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">32</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">33</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">34</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">35</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">36</span>
												</button>
											</div>
											<div class="item">
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">01</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">02</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">03</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">04</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">05</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">06</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">07</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">08</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">09</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">10</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">11</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">12</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">13</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">14</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">15</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">16</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">17</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">18</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">19</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">20</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">21</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">22</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">23</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">24</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">25</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">26</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">27</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">28</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">29</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">30</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">31</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">32</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">33</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">34</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">35</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">36</span>
												</button>
											 </div>  
										</div> 
									   <div class="carousel-controls">
										  <a class="carousel-control left" href="#myCarousel1" data-slide="prev"><span class="fa fa-angle-double-left"></span></a>
										  <a class="carousel-control right" href="#myCarousel1" data-slide="next"><span class="fa fa-angle-double-right"></span></a>
									  </div>               
									</div>
								</div>
							</div>
						</div>
						<div class="tab-pane" id="3a">
							<div class="tableright">
								<div class="tcb-simple-carousel">
									<div id="myCarousel2" class="carousel slide" data-interval="false">
										<div class="carousel-inner">           
											<div class="item active">
												<button type="button" class="btn btn-default btn_violet btn_orange">
													<span class="number_s">01</span>
													<p class="timer">00 H 30 M 12 S</p>
												</button>
												<button type="button" class="btn btn-default btn_violet btn_orange">
													<span class="number_s">02</span>
													<p class="timer">00 H 30 M 12 S</p>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">03</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">04</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">05</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">06</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">07</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">08</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">09</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">10</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">11</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">12</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">13</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">14</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">15</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">16</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">17</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">18</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">19</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">20</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">21</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">22</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">23</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">24</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">25</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">26</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">27</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">28</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">29</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">30</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">31</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">32</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">33</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">34</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">35</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">36</span>
												</button>
											</div>
											<div class="item">
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">01</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">02</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">03</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">04</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">05</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">06</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">07</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">08</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">09</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">10</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">11</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">12</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">13</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">14</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">15</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">16</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">17</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">18</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">19</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">20</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">21</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">22</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">23</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">24</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">25</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">26</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">27</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">28</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">29</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">30</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">31</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">32</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">33</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">34</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">35</span>
												</button>
												<button type="button" class="btn btn-default btn_violet">
													<span class="number_s">36</span>
												</button>
											 </div>  
										</div> 

									   <div class="carousel-controls">
										  <a class="carousel-control left" href="#myCarousel2" data-slide="prev"><span class="fa fa-angle-double-left"></span></a>
										  <a class="carousel-control right" href="#myCarousel2" data-slide="next"><span class="fa fa-angle-double-right"></span></a>
									  </div>               
									</div>
								</div>
							</div>      
						</div>
					</div>
				</div>
			</div>
		</div>
			<div class="row">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-left slider_sec">
					
				</div>
			</div>
    	</div>
    	<div class="container">
    		<div class="row">
    			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 top_foot">
    				<table class="table">
    					<tbody>
    						<tr>
    							<td>Open Til : <?= $this->till_name ?></td>
    							<td>Status : Open</td>
    							<td>Floor: All</td>
    							<td>Information</td>
    							<td>12/12/2019  6:17:25 PM</td>
    							<td>Sale</td>
    							<td>0</td>
    							<td>Cash in</td>
    							<td>Display</td>
    						</tr>
    					</tbody>
    				</table>
    			</div>
    		</div>
    		<div class="row">
    			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 bottom_foot">
    				<table class="table">
    					<tbody>
    						<tr>
    							<td>
    								<table>
    									<tr>
    										<td><button type="button" class="btn btn-default btn_vio">8</button></td>
    									</tr>
    									<tr>
    										<td><button type="button" class="btn btn-default btn_orange">78</button></td>
    									</tr>
    									<tr>
    										<td><button type="button" class="btn btn-default btn_green">0</button></td>
    									</tr>
    								</table>
    							</td>
    							<td>
    								<table class="table" >
    									<colgroup>
    										<col width="75%">
    										<col width="5%">
    										<col width="20%">
    									</colgroup>
    									<tr>
    										<td colspan="3">ពត៌មានរហូតដល់/TIL info</td>
    									</tr>
    									<tr>
    										<td>បើកសាច់ប្រាក់ / Opening Cash (USD)</td>
    										<td>:</td>
    										<td>5$</td>
    									</tr>
    									<tr>
    										<td>បើកសាច់ប្រាក់ / Opening Cash (KHR)</td>
    										<td>:</td>
    										<td>5$</td>
    									</tr>
    								</table>
    							</td>
    							<td>
    								<table class="table" >
    									<colgroup>
    										<col width="50%">
    										<col width="5%">
    										<col width="49%">
    									</colgroup>
    									<tr>
    										<td>មោះ​អ្នកប្រើប្រាស់ / User Name</td>
    										<td>:</td>
    										<td>Cashier 02</td>
    									</tr>
    									<tr>
    										<td>សាខា/Branch </td>
    										<td>:</td>
    										<td>002 Kimmo Korean 7 Level</td>
    									</tr>
    								</table>
    							</td>
    							<td>
    								<table class="table">
    									<colgroup>
    										<col width="75%">
    										<col width="5%">
    										<col width="20%">
    									</colgroup>
    									<tr>
    										<td>តុល្យភាព / Balance (USD)</td>
    										<td>:</td>
    										<td>8$</td>
    									</tr>
    									<tr>
    										<td>តុល្យភាព / Balance (KHR)</td>
    										<td>:</td>
    										<td>9$</td>
    									</tr>
    									<tr>
    										<td>ឈ្មោះ / Till Name</td>
    										<td>:</td>
    										<td>mani</td>
    									</tr>
    								</table>
    							</td>
    							<td>
    								<table class="table">
    									<colgroup>
    										<col width="75%">
    										<col width="5%">
    										<col width="20%">
    									</colgroup>
    									<tr>
    										<td>បង់លុយ / Paid (USD)</td>
    										<td>:</td>
    										<td>8$</td>
    									</tr>
    									<tr>
    										<td>បង់លុយ / Paid (KHR)</td>
    										<td>:</td>
    										<td>9$</td>
    									</tr>
    									<tr>
    										<td>ចំណាយសរុប / Total Paid (USD)</td>
    										<td>:</td>
    										<td>$500</td>
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
</body>
</html>
