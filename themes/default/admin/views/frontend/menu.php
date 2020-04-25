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
   	<link rel="stylesheet" href="<?=$assets?>styles/frontend_new.css" type="text/css">
  	<style>
		body{background: url(<?=$assets?>images/login_srampos.jpg) no-repeat left top;height: 100vh;background-size: cover;}
	</style>
</head>
<body>
	<div class="outer_screen_menu">		
		<div class="col-sm-12 text-center">
			<button class="col-sm-4 col-xs-12">
				<img src="<?=$assets?>images/sprite/menu/dine_in.png" alt="dine in">
				<figcaption>DINE IN</figcaption>
			</button>
			<button class="col-sm-4 col-xs-12">
				<img src="<?=$assets?>images/sprite/menu/bbq.png" alt="bbq">
				<figcaption>BBQ</figcaption>
			</button>
			<button class="col-sm-4 col-xs-12">
				<img src="<?=$assets?>images/sprite/menu/take_away.png" alt="Take Away">
				<figcaption>TAKE AWAY</figcaption>
			</button>
			<button class="col-sm-4 col-xs-12">
				<img src="<?=$assets?>images/sprite/menu/qsr.png" alt="qsr">
				<figcaption>QSR</figcaption>
			</button>
			<button class="col-sm-4 col-xs-12">
				<img src="<?=$assets?>images/sprite/menu/door_delivery.png" alt="Door delivery">
				<figcaption>DOOR DELIVERY</figcaption>
			</button>
			<button class="col-sm-4 col-xs-12">
				<img src="<?=$assets?>images/sprite/menu/reports.png" alt="Reports">
				<figcaption>REPORTS</figcaption>
			</button>
			
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
<?php /*<script type="text/javascript" src="<?=$assets?>pos/js/pos.ajax.js"></script>*/?>
<script type="text/javascript" src="<?=$assets?>js/dragscrollable.js"></script>
<script type="text/javascript" src="<?=$assets?>js/dragscroll.js"></script>
</body>
</html>
