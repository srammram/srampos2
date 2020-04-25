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
   	<link rel="stylesheet" href="<?=$assets?>styles/helpers/select2.min.css" type="text/css">
   	<link rel="stylesheet" href="<?=$assets?>styles/home_frontend.css" type="text/css">
	<style>
		.menu_nav li button,.menu_nav li figure{width: 10.7%;}
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
							<button>
								<img src="<?=$assets?>images/sprite/order.png">
								<figcaption>Order</figcaption>
							</button>
						</li>
						<li>
							<button>
								<img src="<?=$assets?>images/sprite/payment_y.png">
								<figcaption>Payment</figcaption>
							</button>
						</li>
						<li>
							<button>
								<img src="<?=$assets?>images/sprite/print.png">
								<figcaption>Re Print</figcaption>
							</button>
						</li>
						<li>
							<button class="pull-right">
								<img src="<?=$assets?>images/sprite/back.png">
								<figcaption>Back</figcaption>
							</button>
						</li>
					</ul>
				</div>
			</div>
			
		</div>
	</section>
	<section class="drop_down_list">
		<div class="container">
			<div class="row">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 drop_down_list_s" style="padding: 0px;">
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
						<div class="form-group">
							<label for="" class="col-sm-5">Table</label>
							<div class="col-sm-7">
								<select class="form-control select">
									<option>All</option>
									<option>All</option>
									<option>All</option>
								</select>
							</div>
						</div>
					</div>
					<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
						<div class="form-group">
							<label for="" class="col-sm-5">Steward</label>
							<div class="col-sm-7">
								<select class="form-control select">
									<option>Tamil</option>
									<option>Tamil</option>
									<option>Tamil</option>
								</select>
							</div>
						</div>
					</div>
				</div>
			</div>
   	<div class="row">
   		<div class="col-md-12 col-xs-12 table_allorder" style="padding: 0px;">
   			<table class="table">
   				<tr>
   					<td>TABLE 11</td>
   					<td>SPLIT20191221141856017 (Walkin Customer)</td>
   					<td>Status :<span class="text-success">Inprocess</span></td>
   					<td>Steward :<span>Tamil</span></td>
   					<td><button type="button" class="btn">Process</button></td>
   				</tr>
   				<tr>
   					<td>TABLE 12</td>
   					<td>SPLIT20191221141856017 (Walkin Customer)</td>
   					<td>Status :<span class="text-success">Inprocess</span></td>
   					<td>Steward :<span>Tamil</span></td>
   					<td><button type="button" class="btn">Process</button></td>
   				</tr>
   				<tr>
   					<td>TABLE 13</td>
   					<td>SPLIT20191221141856017 (Walkin Customer)</td>
   					<td>Status :<span class="text-success">Inprocess</span></td>
   					<td>Steward :<span>Tamil</span></td>
   					<td><button type="button" class="btn">Process</button></td>
   				</tr>
   				<tr>
   					<td>TABLE 14</td>
   					<td>SPLIT20191221141856017 (Walkin Customer)</td>
   					<td>Status :<span class="text-success">Inprocess</span></td>
   					<td>Steward :<span>Tamil</span></td>
   					<td><button type="button" class="btn">Process</button></td>
   				</tr>
   				<tr>
   					<td>TABLE 15</td>
   					<td>SPLIT20191221141856017 (Walkin Customer)</td>
   					<td>Status :<span class="text-success">Inprocess</span></td>
   					<td>Steward :<span>Tamil</span></td>
   					<td><button type="button" class="btn">Process</button></td>
   				</tr>
   				<tr>
   					<td>TABLE 16</td>
   					<td>SPLIT20191221141856017 (Walkin Customer)</td>
   					<td>Status :<span class="text-success">Inprocess</span></td>
   					<td>Steward :<span>Tamil</span></td>
   					<td><button type="button" class="btn">Process</button></td>
   				</tr>
   				<tr>
   					<td>TABLE 16</td>
   					<td>SPLIT20191221141856017 (Walkin Customer)</td>
   					<td>Status :<span class="text-success">Inprocess</span></td>
   					<td>Steward :<span>Tamil</span></td>
   					<td><button type="button" class="btn">Process</button></td>
   				</tr>
   				<tr>
   					<td>TABLE 16</td>
   					<td>SPLIT20191221141856017 (Walkin Customer)</td>
   					<td>Status :<span class="text-success">Inprocess</span></td>
   					<td>Steward :<span>Tamil</span></td>
   					<td><button type="button" class="btn">Process</button></td>
   				</tr>
   				<tr>
   					<td>TABLE 16</td>
   					<td>SPLIT20191221141856017 (Walkin Customer)</td>
   					<td>Status :<span class="text-success">Inprocess</span></td>
   					<td>Steward :<span>Tamil</span></td>
   					<td><button type="button" class="btn">Process</button></td>
   				</tr>
   				<tr>
   					<td>TABLE 16</td>
   					<td>SPLIT20191221141856017 (Walkin Customer)</td>
   					<td>Status :<span class="text-success">Inprocess</span></td>
   					<td>Steward :<span>Tamil</span></td>
   					<td><button type="button" class="btn">Process</button></td>
   				</tr>
   				<tr>
   					<td>TABLE 16</td>
   					<td>SPLIT20191221141856017 (Walkin Customer)</td>
   					<td>Status :<span class="text-success">Inprocess</span></td>
   					<td>Steward :<span>Tamil</span></td>
   					<td><button type="button" class="btn">Process</button></td>
   				</tr>
   				<tr>
   					<td>TABLE 16</td>
   					<td>SPLIT20191221141856017 (Walkin Customer)</td>
   					<td>Status :<span class="text-success">Inprocess</span></td>
   					<td>Steward :<span>Tamil</span></td>
   					<td><button type="button" class="btn">Process</button></td>
   				</tr>
   				
   				
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
<script type="text/javascript" src="<?=$assets?>js/select2.full.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/custom.js"></script>
<script type="text/javascript" src="<?=$assets?>js/jquery.calculator.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/bootstrapValidator.min.js"></script>
<script type="text/javascript" src="<?=$assets?>pos/js/plugins.min.js"></script>
<script type="text/javascript" src="<?=$assets?>pos/js/parse-track-data.js"></script>
<!--<script type="text/javascript" src="<?=$assets?>pos/js/pos.ajax.js?v=1"></script>-->
<!--<script type="text/javascript" src="<?=$assets?>pos/js/pos_consolidate.ajax.js?v=1"></script>-->
	<script>
		$(function(){
			$("#bill_generation").mCustomScrollbar({
				 theme:"dark-3" ,
			});
			$(".table_allorder").mCustomScrollbar({
				 theme:"dark-3" ,
			});
        });
		
		$('.select').select2();
	</script>
</body>
</html>
