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
   	<link rel="stylesheet" href="<?=$assets?>styles/palered_theme.css" type="text/css">
  	<style>
		body{background: url(<?=$assets?>images/login_srampos.jpg) no-repeat left top;height: 100vh;background-size: cover;}
		.outer_screen_menu button figcaption{font-size: 18px;}
	</style>
</head>
<body>

	<section class="pos_bottom_s" style="background-color: transparent;">
		<div class="container">
			<div class="row">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-left header_sec_me" style="margin-top: 10px;">
					<ul class="menu_nav">
						<li>
						<a href="<?php echo base_url('/pos/pos/home')  ?>">
							<button class="pull-right">
								<img src="<?=$assets?>images/sprite/back.png">
								<figcaption>Back</figcaption>
							</button>
							</a>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</section>
	<div class="outer_screen_menu">		
		<div class="col-sm-12 text-center">
			<div class="row">
				<button class="col-sm-4 col-xs-12">
					<a href="<?php  echo base_url('pos/pos/reports/?type=1');  ?>" style=" text-decoration: none;">
						<img src="<?=$assets?>images/sprite/menu/report1.png" alt="dine in">
						<figcaption>Today Item Wise Sale Report</figcaption>
					</a>
				</button>
				<button class="col-sm-4 col-xs-12">
					<a href="<?php  echo base_url('pos/pos/reports/?type=2');  ?>" style=" text-decoration: none;">
						<img src="<?=$assets?>images/sprite/menu/day_summary_re.png" alt="bbq">
						<figcaption>Day's Summary</figcaption>
					</a>
				</button>
				<button class="col-sm-4 col-xs-12">
					<a href="<?php  echo base_url('pos/pos/reports/?type=3');  ?>" style="text-decoration: none;">
						<img src="<?=$assets?>images/sprite/menu/cashier_wise_re.png" alt="Take Away">
						<figcaption>Cashier Wise Sales Report</figcaption>
					</a>
				</button>
			</div>
			<div class="row">
				<button class="col-sm-4 col-sm-offset-2 col-xs-12">
					<a href="<?php  echo base_url('pos/pos/reports/?type=4');  ?>" style=" text-decoration: none;">
					<img src="<?=$assets?>images/sprite/menu/pos_settlement_re.png" alt="qsr">
					<figcaption>POS Settlement Report</figcaption>
					</a>
				</button>
				<button class="col-sm-4 col-xs-12">
					<a href="<?php  echo base_url('pos/pos/reports/?type=5');  ?>" style=" text-decoration: none;">
						<img src="<?=$assets?>images/sprite/menu/shift_wise_re.png" alt="Door delivery">
						<figcaption>Shift Wise Report</figcaption>
					</a>
				</button>
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
<?php /*<script type="text/javascript" src="<?=$assets?>pos/js/pos.ajax.js"></script>*/?>
<script type="text/javascript" src="<?=$assets?>js/dragscrollable.js"></script>
<script type="text/javascript" src="<?=$assets?>js/dragscroll.js"></script>
<?php 
echo $this->load->view($this->theme.'pos_v2/shift/shift_popup'); 
?>
</body>
</html>
