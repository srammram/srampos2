<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <base href="<?= admin_url() ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="content-type" content="text/html; charset=ISO-8859-1">
   
    <title><?= $page_title ?> - <?= $Settings->site_name ?></title>
    <link rel="shortcut icon" href="<?= $assets ?>images/favicon.ico"/>
    <link href="<?= $assets ?>styles/theme.css" rel="stylesheet"/>
    <link href="<?= $assets ?>styles/style.css" rel="stylesheet"/>
    <link href="<?= $assets ?>styles/flipclock.css" rel="stylesheet"/>
    <script type="text/javascript" src="<?= $assets ?>js/jquery-2.0.3.min.js"></script>
    <script type="text/javascript" src="<?= $assets ?>js/jquery-migrate-1.2.1.min.js"></script>
    <script type="text/javascript" src="<?= $assets ?>js/jquery.validate.js"></script>
    <script type="text/javascript" src="<?= $assets ?>js/flipclock.min.js"></script>
    <!--[if lt IE 9]>
    <script src="<?= $assets ?>js/jquery.js"></script>
    <![endif]-->
    <noscript><style type="text/css">#loading { display: none; }</style></noscript>
    <?php if ($Settings->user_rtl) { ?>
        <link href="<?= $assets ?>styles/helpers/bootstrap-rtl.min.css" rel="stylesheet"/>
        <link href="<?= $assets ?>styles/style-rtl.css" rel="stylesheet"/>
        <script type="text/javascript">
            $(document).ready(function () { $('.pull-right, .pull-left').addClass('flip'); });
        </script>
    <?php } ?>
    <script type="text/javascript">
        $(window).load(function () {
            $("#loading").fadeOut("slow");
        });
    </script>
	 <script type="text/javascript">
		$(document).ready(function() {
			$("li").on("contextmenu",function(){
			   return false;
			}); 
		}); 
	</script>
  
</head>

<body>
<noscript>
    <div class="global-site-notice noscript">
        <div class="notice-inner">
            <p><strong>JavaScript seems to be disabled in your browser.</strong><br>You must have JavaScript enabled in
                your browser to utilize the functionality of this website.</p>
        </div>
    </div>
</noscript>
<div id="loading"></div>
<div id="app_wrapper">
 <?php if(!isset($isMobileApp)) : ?>
    <header id="header" class="navbar">
        <div class="container">
            <a class="navbar-brand" href="<?= admin_url() ?>"><span class="logo"><?= $Settings->site_name ?></span></a>

            <div class="btn-group visible-xs pull-right btn-visible-sm">
                <button class="navbar-toggle btn" type="button" data-toggle="collapse" data-target="#sidebar_menu">
                    <span class="fa fa-bars"></span>
                </button>
                <a href="<?= admin_url('users/profile/' . $this->session->userdata('user_id')); ?>" class="btn">
                    <span class="fa fa-user"></span>
                </a>
                <a href="<?= admin_url('logout'); ?>" class="btn">
                    <span class="fa fa-sign-out"></span>
                </a>
            </div>
            <div class="header-nav">
                <ul class="nav navbar-nav pull-right">
                    <li class="dropdown">
                        <a class="btn account dropdown-toggle" data-toggle="dropdown" href="#">
                            <img alt="" src="<?= $this->session->userdata('avatar') ? base_url() . 'assets/uploads/avatars/thumbs/' . $this->session->userdata('avatar') : base_url('assets/images/' . $this->session->userdata('gender') . '.png'); ?>" class="mini_avatar img-rounded">

                            <div class="user">
                                <span><?= lang('welcome') ?> <?= $this->session->userdata('username'); ?></span>
                            </div>
                        </a>
                        <ul class="dropdown-menu pull-right">
                            <li>
                                <a href="<?= admin_url('users/profile/' . $this->session->userdata('user_id')); ?>">
                                    <i class="fa fa-user"></i> <?= lang('profile'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?= admin_url('users/profile/' . $this->session->userdata('user_id') . '/#cpassword'); ?>"><i class="fa fa-key"></i> <?= lang('change_password'); ?>
                                </a>
                            </li>
                            <li class="divider"></li>
                            <li>
                                <a href="<?= admin_url('logout'); ?>">
                                    <i class="fa fa-sign-out"></i> <?= lang('logout'); ?>
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
				
                <ul class="nav navbar-nav pull-right">
				<?php   if($this->isStore){   ?>
				 <li class="dropdown hidden-xs" >
                      <i class="fa fa-exchange" <?php echo  ($this->centerdb_connected)? "style='color:green;font-size: 32px;' ":"style='color:red;font-size: 32px;'"   ?> aria-hidden="true"></i>
                    </li>
					
			<?php  	} ?>
                    <li class="dropdown hidden-xs"><a class="btn tip" title="<?= lang('dashboard') ?>" data-placement="bottom" href="<?= admin_url('welcome') ?>"><i class="fa fa-dashboard"></i></a></li>
                    <?php if (SHOP) { ?>
                    <li class="dropdown hidden-xs"><a class="btn tip" title="<?= lang('shop') ?>" data-placement="bottom" href="<?= base_url() ?>"><i class="fa fa-shopping-cart"></i></a></li>
                    <?php } ?>
                    <?php if ($Owner) { ?>
                    <li class="dropdown hidden-sm">
                        <a class="btn tip" title="<?= lang('settings') ?>" data-placement="bottom" href="<?= admin_url('system_settings') ?>">
                            <i class="fa fa-cogs"></i>
                        </a>
                    </li>
                    <?php } ?>
                    <li class="dropdown hidden-xs">
                        <a class="btn tip" title="<?= lang('calculator') ?>" data-placement="bottom" href="#" data-toggle="dropdown">
                            <i class="fa fa-calculator"></i>
                        </a>
                        <ul class="dropdown-menu pull-right calc">
                            <li class="dropdown-content">
                                <span id="inlineCalc"></span>
                            </li>
                        </ul>
                    </li>
                    <?php if ($info) { ?>
                        <li class="dropdown hidden-sm">
                            <a class="btn tip" title="<?= lang('notifications') ?>" data-placement="bottom" href="#" data-toggle="dropdown">
                                <i class="fa fa-info-circle"></i>
                                <span class="number blightOrange black"><?= sizeof($info) ?></span>
                            </a>
                            <ul class="dropdown-menu pull-right content-scroll">
                                <li class="dropdown-header"><i class="fa fa-info-circle"></i> <?= lang('notifications'); ?></li>
                                <li class="dropdown-content">
                                    <div class="scroll-div">
                                        <div class="top-menu-scroll">
                                            <ol class="oe">
                                                <?php foreach ($info as $n) {
                                                    echo '<li>' . $n->comment . '</li>';
                                                } ?>
                                            </ol>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </li>
                    <?php } ?>
                    
                    <?php //if ($this->Settings->procurment == 1) { ?>
                        <li class="dropdown hidden-sm procurment_notification">
                            <a class="btn tip" title="<?= lang('notifications') ?>" data-placement="bottom" href="#" data-toggle="dropdown">
                                <i class="fa fa-info-circle"></i>
                                <span class="number blightOrange black procurment_number"><?php //sizeof($access_info) ?></span>
                                <input type="hidden" name="procurment_notification_key" id="procurment_notification_key">
                            </a>
                            <ul class="dropdown-menu pull-right content-scroll">
                                <li class="dropdown-header"><i class="fa fa-info-circle"></i> <?= lang('notifications'); ?></li>
                                <li class="dropdown-content">
                                    <div class="scroll-div">
                                        <div class="top-menu-scroll">
                                            <ol class="oe procurment_list">
                                                <?php //foreach ($access_info as $an) {
                                                   // echo '<li><a href="javascript:void(0)"><h3>"'.$an->title.'"</h3><p>"'.$an->message.'"</p></a></li>';
                                                //} ?>
                                            </ol>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </li>
                    <?php //} ?>
                    
                    
                    
                    <?php if ($events) { ?>
                        <li class="dropdown hidden-xs">
                            <a class="btn tip" title="<?= lang('calendar') ?>" data-placement="bottom" href="#" data-toggle="dropdown">
                                <i class="fa fa-calendar"></i>
                                <span class="number blightOrange black"><?= sizeof($events) ?></span>
                            </a>
                            <ul class="dropdown-menu pull-right content-scroll">
                                <li class="dropdown-header">
                                <i class="fa fa-calendar"></i> <?= lang('upcoming_events'); ?>
                                </li>
                                <li class="dropdown-content">
                                    <div class="top-menu-scroll">
                                        <ol class="oe">
                                            <?php foreach ($events as $event) {
                                                echo '<li>' . date($dateFormats['php_ldate'], strtotime($event->start)) . ' <strong>' . $event->title . '</strong><br>'.$event->description.'</li>';
                                            } ?>
                                        </ol>
                                    </div>
                                </li>
                                <li class="dropdown-footer">
                                    <a href="<?= admin_url('calendar') ?>" class="btn-block link">
                                        <i class="fa fa-calendar"></i> <?= lang('calendar') ?>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    <?php } else { ?>
                    <li class="dropdown hidden-xs">
                        <a class="btn tip" title="<?= lang('calendar') ?>" data-placement="bottom" href="<?= admin_url('calendar') ?>">
                            <i class="fa fa-calendar"></i>
                        </a>
                    </li>
                    <?php } ?>
                    <li class="dropdown hidden-sm">
                        <a class="btn tip" title="<?= lang('styles') ?>" data-placement="bottom" data-toggle="dropdown"
                           href="#">
                            <i class="fa fa-css3"></i>
                        </a>
                        <ul class="dropdown-menu pull-right">
                            <li class="bwhite noPadding">
                                <a href="#" id="fixed" class="">
                                    <i class="fa fa-angle-double-left"></i>
                                    <span id="fixedText">Fixed</span>
                                </a>
                                <a href="#" id="cssLight" class="grey">
                                    <i class="fa fa-stop"></i> Grey
                                </a>
                                <a href="#" id="cssBlue" class="blue">
                                    <i class="fa fa-stop"></i> Blue
                                </a>
                                <a href="#" id="cssBlack" class="black">
                                   <i class="fa fa-stop"></i> Black
                               </a>
                                <a href="#" id="cssdarkBlue" class="dark_blue">
                                    <i class="fa fa-stop"></i> Dark Blue
                                </a>
                           </li>
                        </ul>
                    </li>
                    <li class="dropdown hidden-xs">
                        <a class="btn tip" title="<?= lang('language') ?>" data-placement="bottom" data-toggle="dropdown"
                           href="#">
                            <img src="<?= base_url('assets/images/' . $Settings->user_language . '.png'); ?>" alt="">
                        </a>
                        <ul class="dropdown-menu pull-right">
                            <?php $scanned_lang_dir = array_map(function ($path) {
                                return basename($path);
                            }, glob(APPPATH . 'language/*', GLOB_ONLYDIR));
                            foreach ($scanned_lang_dir as $entry) { 
							if($entry == 'english' || $entry == 'khmer'){
								?>
                                <li>
                                    <a href="<?= admin_url('welcome/language/' . $entry); ?>">
                                        <img src="<?= base_url('assets/images/'.$entry.'.png'); ?>" class="language-img">
                                        &nbsp;&nbsp;<?= ucwords($entry); ?>
                                    </a>
                                </li>
                            <?php } } ?>
                            <li class="divider"></li>
                            <li>
                                <a href="<?= admin_url('welcome/toggle_rtl') ?>">
                                    <i class="fa fa-align-<?=$Settings->user_rtl ? 'right' : 'left';?>"></i>
                                    <?= lang('toggle_alignment') ?>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <?php /* if ($Owner && $Settings->update) { ?>
                    <li class="dropdown hidden-sm">
                        <a class="btn blightOrange tip" title="<?= lang('update_available') ?>"
                            data-placement="bottom" data-container="body" href="<?= admin_url('system_settings/updates') ?>">
                            <i class="fa fa-download"></i>
                        </a>
                    </li>
                        <?php } */ ?>
                    <?php if (($Owner || $Admin || $GP['reports-quantity_alerts'] || $GP['reports-expiry_alerts']) && ($qty_alert_num > 0 || $exp_alert_num > 0 || $shop_sale_alerts)) { ?>
                        <li class="dropdown hidden-sm">
                            <a class="btn blightOrange tip" title="<?= lang('alerts') ?>"
                                data-placement="left" data-toggle="dropdown" href="#">
                                <i class="fa fa-exclamation-triangle"></i>
                                <span class="number bred black"><?= $qty_alert_num+(($Settings->product_expiry) ? $exp_alert_num : 0)+$shop_sale_alerts+$shop_payment_alerts; ?></span>
                            </a>
                            <ul class="dropdown-menu pull-right">
                                <?php if ($qty_alert_num > 0) { ?>
                                <li>
                                    <a href="<?= admin_url('reports/quantity_alerts') ?>" class="">
                                        <span class="label label-danger pull-right" style="margin-top:3px;"><?= $qty_alert_num; ?></span>
                                        <span style="padding-right: 35px;"><?= lang('quantity_alerts') ?></span>
                                    </a>
                                </li>
                                <?php } ?>
                                <?php if ($Settings->product_expiry) { ?>
                                <li>
                                    <a href="<?= admin_url('reports/expiry_alerts') ?>" class="">
                                        <span class="label label-danger pull-right" style="margin-top:3px;"><?= $exp_alert_num; ?></span>
                                        <span style="padding-right: 35px;"><?= lang('expiry_alerts') ?></span>
                                    </a>
                                </li>
                                <?php } ?>
                                <?php if ($shop_sale_alerts) { ?>
                                <li>
                                    <a href="<?= admin_url('sales?shop=yes&delivery=no') ?>" class="">
                                        <span class="label label-danger pull-right" style="margin-top:3px;"><?= $shop_sale_alerts; ?></span>
                                        <span style="padding-right: 35px;"><?= lang('sales_x_delivered') ?></span>
                                    </a>
                                </li>
                                <?php } ?>
                                <?php if ($shop_payment_alerts) { ?>
                                <li>
                                    <a href="<?= admin_url('sales?shop=yes&attachment=yes') ?>" class="">
                                        <span class="label label-danger pull-right" style="margin-top:3px;"><?= $shop_payment_alerts; ?></span>
                                        <span style="padding-right: 35px;"><?= lang('manual_payments') ?></span>
                                    </a>
                                </li>
                                <?php } ?>
                            </ul>
                        </li>
                    <?php } ?>
                    <?php
                        $user_group_id = $this->session->userdata('group_id');
                     if (($user_group_id == 5) OR ($user_group_id == 6) OR ($user_group_id == 7) OR ($user_group_id == 8) ) {

                     ?>
                    <!-- <li class="dropdown hidden-xs">
                        <a class="btn bdarkGreen tip" title="<?= lang('pos') ?>" data-placement="bottom" href="<?= admin_url('pos') ?>">
                            <i class="fa fa-th-large"></i> <span class="padding05"><?= lang('pos') ?></span>
                        </a>
                    </li> -->
                    <?php } ?>

                    <?php if ($Owner) { ?>
                        <li class="dropdown">
                            <a class="btn bdarkGreen tip" id="today_profit" title="<span><?= lang('today_profit') ?></span>"
                                data-placement="bottom" data-html="true" href="<?= admin_url('reports/profit') ?>"
                                data-toggle="modal" data-target="#myModal">
                                <i class="fa fa-hourglass-2"></i>
                            </a>
                        </li>
                    <?php } ?>
                    <?php if ($Owner || $Admin) { ?>
                    <?php if (POS) { ?>
                    <li class="dropdown hidden-xs">
                        <a class="btn bblue tip" title="<?= lang('list_open_registers') ?>" data-placement="bottom" href="<?= admin_url('pos/registers') ?>">
                            <i class="fa fa-list"></i>
                        </a>
                    </li>
                    <?php } ?>
                    <li class="dropdown hidden-xs">
                        <a class="btn bred tip" title="<?= lang('clear_ls') ?>" data-placement="bottom" id="clearLS" href="#">
                            <i class="fa fa-eraser"></i>
                        </a>
                    </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </header>
<?php endif; ?>
    <div class="container" id="container">
        <div class="row" id="main-con">
        <table class="lt"><tr>
        <?php if(!isset($isMobileApp)) : ?>
        <td class="sidebar-con">
         
            <div id="sidebar-left">
                <div class="sidebar-nav nav-collapse collapse navbar-collapse" id="sidebar_menu">
                    <ul class="nav main-menu">
                        <li class="mm_welcome">
                            <a href="<?= admin_url() ?>">
                                <i class="fa fa-dashboard"></i>
                                <span class="text"> <?= lang('dashboard'); ?></span>
                            </a>
                        </li>

                        <?php
                        if ($Owner || $Admin) {
                            ?>
                            <?php if($this->Settings->night_audit_rights == 1){ ?>
                            <li class="mm_audit">
                                <a href="<?= admin_url('nightaudit') ?>">
                                    <i class="fa fa-star"></i>
                                    <span class="text"> <?= lang('night_audit'); ?></span>
                                </a>
                            </li>
                            <?php } ?>
                              <!--<li class="mm_products">
                                <a class="dropmenu" href="#">
                                    <i class="fa fa-barcode"></i>
                                    <span class="text"> <?= lang('products'); ?> </span>
                                    <span class="chevron closed"></span>
                                </a>
                                <ul>
                                  <li id="products_index">
                                        <a class="submenu" href="<?= admin_url('products'); ?>">
                                            <span class="text"> <?= lang('products_list'); ?></span>
                                        </a>
                                    </li>
                                    <li id="products_add">
                                        <a class="submenu" href="<?= admin_url('products/add'); ?>">
                                            <span class="text"> <?= lang('add_products'); ?></span>
                                        </a>
                                    </li>
                                    
                                    <li id="products_import_csv">
                                        <a class="submenu" href="<?= admin_url('products/import_csv'); ?>">
                                            <span class="text"> <?= lang('import_csv'); ?></span>
                                        </a>
                                    </li>
                                    <li id="products_count_stock">
                                        <a class="submenu" href="<?= admin_url('products/count_stock'); ?>">
                                            <span class="text"> <?= lang('stock_audit'); ?></span>
                                        </a>
                                    </li>
                                      <li id="products_stock_counts">
                                        <a class="submenu" href="<?= admin_url('products/stock_counts'); ?>">
                                            
                                            <span class="text"> <?= lang('stock_counts'); ?></span>
                                        </a>
                                    </li>
                                </ul>
                            </li>-->
                            
                            <li class="mm_recipe">
                                <a class="dropmenu" href="#">
                                    <i class="fa fa-star"></i>
                                    <span class="text"> <?= lang('item_master'); ?> </span>
                                    <span class="chevron closed"></span>
                                </a>
                                <ul>
                                    <li id="recipe_index">
                                        <a class="submenu" href="<?= admin_url('recipe'); ?>">
                                            <span class="text"> <?= lang('item_list'); ?></span>
                                        </a>
                                    </li>
                                    <li id="recipe_add">
                                        <a class="submenu" href="<?= admin_url('recipe/add'); ?>">
                                            <span class="text"> <?= lang('add_item'); ?></span>
                                        </a>
                                    </li>
                                      <li id="recipe_import_csv">
                                        <a class="submenu" href="<?= admin_url('recipe/import_csv'); ?>">
                                            <span class="text"> <?= lang('import_csv'); ?></span>
                                        </a>
                                    </li>
                                    
                                    <li id="recipe_varients">
                                            <a class="submenu" href="<?= admin_url('recipe/varients'); ?>">
                                                <span class="text"> <?= lang('variants'); ?></span>
                                            </a>
                                    </li>
                                    <li id="recipe_ItemsWith_varients">
                                            <a class="submenu" href="<?= admin_url('recipe/ItemsWith_varients'); ?>">
                                                <span class="text"> <?= lang('ItemsWith_varients'); ?></span>
                                            </a>
                                    </li>                                  

									<li id="recipe_addons">
                                            <a class="submenu" href="<?= admin_url('recipe/recipe_and_variant_addon'); ?>">
                                                <span class="text"> <?= lang('recipe_and_variant_addon'); ?></span>
                                            </a>
                                    </li>

									<!-- <li id="ingredients_add">
                                        <a class="submenu" href="<?= admin_url('recipe/add_ingredients'); ?>">
                                            <span class="text"> <?= lang('add_ingredients'); ?></span>
                                        </a>
                                    </li>
									
									<li id="ingredients_list">
                                        <a class="submenu" href="<?= admin_url('recipe/list_ingredients'); ?>">
                                            <span class="text"> <?= lang('list_ingredients'); ?></span>
                                        </a>
                                    </li>
									
									
									<li id="ingredients_import_csv">
                                        <a class="submenu" href="<?= admin_url('recipe/ingredients_import_csv'); ?>">
                                            <span class="text"> <?= lang('import_ingredients'); ?></span>
                                        </a>
                                    </li> -->
                                   
                                </ul>
                            </li>
                            
                          <!--  <li class="mm_production">
                                <a class="dropmenu" href="#">
                                    <i class="fa fa-suitcase"></i>
                                    <span class="text"> <?= lang('production'); ?> </span>
                                    <span class="chevron closed"></span>
                                </a>
                                <ul>
								
                                    <li id="production_index">
                                        <a class="submenu" href="<?= admin_url('production'); ?>">
                                            <span class="text"> <?= lang('production'); ?></span>
                                        </a>
                                    </li>  -->
                                    
									<!--
                                     <li id="production_balance">
                                        <a class="submenu" href="<?= admin_url('production/balance'); ?>">
                                            <span class="text"> <?= lang('balance_production'); ?></span>
                                        </a>
                                    </li>
                                </ul>
                            </li>  -->
                            
                           <!--  <li class="mm_saleitem_to_purchasesitem">
                                <a href="<?= admin_url('saleitem_to_purchasesitem') ?>">
                                    <i class="fa fa-dashboard"></i>
                                    <span class="text"> <?= lang('bill_of_material'); ?></span>
                                </a>
                            </li> -->
                    
                            <li  style="display: none" class="mm_sales <?= strtolower($this->router->fetch_method()) == 'sales' ? 'mm_pos' : '' ?>">
                                <a class="dropmenu" href="#">
                                    <i class="fa fa-balance-scale"></i>
                                    <span class="text"> <?= lang('sales'); ?>
                                    </span> <span class="chevron closed"></span>
                                </a>
                                <ul>
                                    <li id="sales_index">
                                        <a class="submenu" href="<?= admin_url('sales'); ?>">
                                            <span class="text"> <?= lang('sales_list'); ?></span>
                                        </a>
                                    </li>
                                  
                                    <li id="sales_deliveries">
                                        <a class="submenu" href="<?= admin_url('sales/deliveries'); ?>">
                                            <span class="text"> <?= lang('deliveries'); ?></span>
                                        </a>
                                    </li>
                                  
                                </ul>
                            </li>	


                           <!-- <li class="mm_quotes">
                                <a class="dropmenu" href="#">
                                    <i class="fa fa-binoculars"></i>
                                    <span class="text"> <?= lang('quotations'); ?> </span>
                                    <span class="chevron closed"></span>
                                </a>
                                <ul>
                                    <li id="quotes_index">
                                        <a class="submenu" href="<?= admin_url('quotes'); ?>">
                                            <span class="text"> <?= lang('quotations_list'); ?></span>
                                        </a>
                                    </li>
                                    <li id="quotes_add">
                                        <a class="submenu" href="<?= admin_url('quotes/add'); ?>">
                                            <span class="text"> <?= lang('quotations_add'); ?></span>
                                        </a>
                                    </li>
                                </ul>
                            </li>-->

                            <!--<li class="mm_purchases_order">
                                <a class="dropmenu" href="#">
                                    <i class="fa fa-external-link-square"></i>
                                    <span class="text"> <?= lang('purchases_order'); ?> </span>
                                    <span class="chevron closed"></span>
                                </a>
                                <ul>
                                    <li id="purchases_order_index">
                                        <a class="submenu" href="<?= admin_url('purchases_order'); ?>">
                                            <span class="text"> <?= lang('list_purchases_order'); ?></span>
                                        </a>
                                    </li>
                                    <li id="purchases_order_add">
                                        <a class="submenu" href="<?= admin_url('purchases_order/add'); ?>">
                                            <span class="text"> <?= lang('add_purchase_order'); ?></span>
                                        </a>
                                    </li>
                                </ul>
                            </li> -->                      

                            <!--<li class="mm_purchases">
                                <a class="dropmenu" href="#">
                                    <i class="fa fa-star"></i>
                                    <span class="text"> <?= lang('GRN'); ?>
                                    </span> <span class="chevron closed"></span>
                                </a>
                                <ul>
                                    <li id="purchases_index">
                                        <a class="submenu" href="<?= admin_url('purchases'); ?>">
                                            <span class="text"> <?= lang('list_GRN'); ?></span>
                                        </a>
                                    </li>
                                    <li id="purchases_add">
                                        <a class="submenu" href="<?= admin_url('purchases/add'); ?>">
                                            <span class="text"> <?= lang('add_GRN'); ?></span>
                                        </a>
                                    </li>
                                     
                                </ul>
                            </li>-->
                            
                            <!--<li class="mm_material_request">
                                <a class="dropmenu" href="#">
                                    <i class="fa fa-star-o"></i>
                                    <span class="text"> <?= lang('material_request'); ?> </span>
                                    <span class="chevron closed"></span>
                                </a>
                                <ul>
                                    <li id="material_request_index">
                                       <a class="submenu" href="<?= admin_url('material_request'); ?>">
                                            <span class="text"> <?= lang('material_request'); ?></span>
                                        </a>
                                    </li>
                                    <li id="material_request_add">
                                        <a class="submenu" href="<?= admin_url('material_request/add'); ?>">
                                            <span class="text"> <?= lang('add_material_request'); ?></span>
                                        </a>
                                    </li>
                                     <li id="transfers_purchase_by_csv">
                                        <a class="submenu" href="<?= admin_url('transfers/transfer_by_csv'); ?>">
                                            <span class="text"> <?= lang('add_transfer_by_csv'); ?></span>
                                        </a>
                                    </li> 
                                </ul>
                            </li>-->

                            <!--<li class="mm_transfers">
                                <a class="dropmenu" href="#">
                                    <i class="fa fa-star-o"></i>
                                    <span class="text"> <?= lang('stock_transfer'); ?> </span>
                                    <span class="chevron closed"></span>
                                </a>
                                <ul>
                                    <li id="transfers_index">
                                        <a class="submenu" href="<?= admin_url('transfers'); ?>">
											<span class="text"> <?= lang('stock_transfer_list'); ?></span>
                                        </a>
                                    </li>
                                    <li id="transfers_add">
                                        <a class="submenu" href="<?= admin_url('transfers/add'); ?>">
                                            <span class="text"> <?= lang('add_stock_transfer'); ?></span>
                                        </a>
                                    </li>
                                     <li id="transfers_purchase_by_csv">
                                        <a class="submenu" href="<?= admin_url('transfers/transfer_by_csv'); ?>">
                                            <span class="text"> <?= lang('add_transfer_by_csv'); ?></span>
                                        </a>
                                    </li> 
                                </ul>
                            </li>-->
                            
                            <?php
							//if($this->Settings->procurment == 1){
							?>
                            <li class="mm_production" style="display: none">
                                <a class="dropmenu" href="#">
                                    <i class="fa fa-suitcase"></i>
                                    <span class="text"> <?= lang('preparation'); ?> </span>
                                    <span class="chevron closed"></span>
                                </a>
                                <ul>
                                    <li id="preparation_index">
                                        <a class="submenu" href="<?= admin_url('preparation'); ?>">
                                            <span class="text"> <?= lang('preparation '); ?></span>
                                        </a>
                                    </li>
                                    <li id="preparation_add">
                                        <a class="submenu" href="<?= admin_url('preparation/add'); ?>">
                                            <span class="text"> <?= lang('add_preparation'); ?></span>
                                        </a>
                                    </li>
                                     <li id="preparation_balance">
                                        <a class="submenu" href="<?= admin_url('preparation/balance'); ?>">
                                            <span class="text"> <?= lang('balance_preparation'); ?></span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <?php
                            if($this->Settings->procurment == 1){
                            ?>
                                <li class="mm_recipe_management">
                                    <a class="dropmenu" href="#">
                                        <i class="fa fa-bar-chart-o"></i>
                                        <span class="text"> <?= lang('recipe_management'); ?> </span>
                                        <span class="chevron closed"></span>
                                    </a>
                                    <ul class="level-2-menu">                                    
                                        
                                        <li id="recipe_management_production">
                                            <a class="dropmenu" href="javascript:void(0)">
                                                <span class="text">  <?= lang('production'); ?> </span>
                                                <!--<span class="chevron closed"></span>-->
                                            </a>
                                            <ul class="level-3-menu">
                                                <li id="recipe_management_production_index">
                                                    <a class="submenu" href="<?= admin_url('procurment/production'); ?>">
                                                        <span class="text"> <?= lang('list'); ?></span>
                                                    </a>
                                                </li>
                                                
                                                <li id="recipe_management_production_add">
                                                    <a class="submenu" href="<?= admin_url('procurment/production/add'); ?>">
                                                        <span class="text"> <?= lang('create'); ?></span>
                                                    </a>
                                                </li>                                                                                      
                                           </ul>
                                        </li>  
                                    <!-- </ul> -->
                                     <!-- <ul class="level-2-menu">      -->
                                        <li id="recipe_management_ingredients">
                                            <a class="dropmenu" href="javascript:void(0)">
                                                <span class="text">  <?= lang('ingredients'); ?> </span>
                                                <!--<span class="chevron closed"></span>-->
                                            </a>
                                            <ul class="level-3-menu">
                                                <li id="recipe_management_ingredients_index">
                                                    <a class="submenu" href="<?= admin_url('recipe/list_ingredients'); ?>">
                                                        <span class="text"> <?= lang('list_ingredients'); ?></span>
                                                    </a>
                                                </li>

                                                <li id="recipe_management_ingredients_add">
                                                    <a class="submenu" href="<?= admin_url('recipe/add_ingredients'); ?>">
                                                        <span class="text"> <?= lang('add_ingredients'); ?></span>
                                                    </a>
                                                </li>   

                                                 <li id="recipe_management_ingredients_import_csv">
                                                    <a class="submenu" href="<?= admin_url('recipe/ingredients_import_csv'); ?>">
                                                        <span class="text"> <?= lang('import_ingredients'); ?></span>
                                                    </a>
                                                </li>  

                                           </ul>
                                        </li>  
                                    </ul>
                                </li>

                            <li class="mm_procurment">
                                <a class="dropmenu" href="#">
                                    <i class="fa fa-bar-chart-o"></i>
                                    <span class="text"> <?= lang('inventory'); ?> </span>
                                    <span class="chevron closed"></span>
                                </a>
                                <ul class="level-2-menu"> 
							<?php  if($this->isStore):  ?>								
                                    <?php if($this->Settings->supply_chain) : ?>
									
                                    <li id="procurment_store_request">
                                        <a class="dropmenu" href="javascript:void(0)">
                                            <span class="text">  <?= lang('store_indent_request'); ?> </span>
                                            <!--<span class="chevron closed"></span>-->
                                        </a>
                                        <ul class="level-3-menu">
                                            <li id="procurment_store_request_index">
                                                <a class="submenu" href="<?= admin_url('procurment/store_request'); ?>">
                                                    <span class="text"> <?= lang('list'); ?></span>
                                                </a>
                                            </li>
                                            <li id="procurment_store_request_add">
                                                <a class="submenu" href="<?= admin_url('procurment/store_request/add'); ?>">
                                                    <span class="text"> <?= lang('create'); ?></span>
                                                </a>
                                            </li>
                                       </ul>
                                    </li>
                                   <?php endif; ?>
	<?php endif;  ?>
								   <!--
                                    <li id="procurment_request">
                                        <a class="dropmenu" href="javascript:void(0)">
                                            <span class="text">  <?= lang('quotation_request'); ?> </span>
                                            <!--<span class="chevron closed"></span>
                                        </a>
                                        <ul class="level-3-menu">
                                            <li id="procurment_request_index">
                                                <a class="submenu" href="<?= admin_url('procurment/request'); ?>">
                                                    <span class="text"> <?= lang('list'); ?></span>
                                                </a>
                                            </li>
                                            
                                            <li id="procurment_request_add">
                                                <a class="submenu" href="<?= admin_url('procurment/request/add'); ?>">
                                                    <span class="text"> <?= lang('create'); ?></span>
                                                </a>
                                            </li>

                                                                                      
                                       </ul>
                                    </li>
                                    -->
									   <?php if($this->isWarehouse) : ?>
									 <li id="procurment_store_indent_receive">
                                        <a class="dropmenu" href="javascript:void(0)">
                                            <span class="text">  <?= lang('store_indent_request_receive'); ?> </span>
                                        </a>
                                        <ul class="level-3-menu">
                                           <li id="procurment_store_indent_receive_index">
                                        <a class="submenu" href="<?= admin_url('procurment/store_indent_receive'); ?>">
                                                   <span class="text"> <?= lang('list'); ?></span></a>
                                        </li>
                                       </ul>
                                   </li>
									 <?php endif; ?>
									
									        <?php if($this->isWarehouse) : ?>
                                            <li id="procurment_indent_process">
                                              <a class="dropmenu" href="javascript:void(0)">
                                              <span class="text">  <?= lang('indent_process'); ?> </span>
											</a>
								            <ul class="level-3-menu">
									        <li id="procurment_indent_process_index">
											<a class="submenu" href="<?= admin_url('procurment/indent_process'); ?>">
											<span class="text"> <?= lang('list'); ?></span>
											</a>
											</li>
										<li id="procurment_indent_process_add">
										<a class="submenu" href="<?= admin_url('procurment/indent_process/add'); ?>">
										<span class="text"> <?= lang('create'); ?></span>
										</a>
										</li>
									</ul>
							         	</li>
							
									 <?php endif; ?>
									
									
									<?php if($this->isWarehouse) : ?>
                                    <li id="procurment_quotes">
                                        <a class="dropmenu" href="javascript:void(0)">
                                            <span class="text">  <?= lang('purchase_quotation'); ?> </span>
                                            <!--<span class="chevron closed"></span>-->
                                        </a>
                                        <ul class="level-3-menu">
                                            <li id="procurment_quotes_index">
                                                <a class="submenu" href="<?= admin_url('procurment/quotes'); ?>">
                                                    <span class="text"> <?= lang('list'); ?></span>
                                                </a>
                                            </li>
                                            <li id="procurment_quotes_add">
                                                <a class="submenu" href="<?= admin_url('procurment/quotes/add'); ?>">
                                                    <span class="text"> <?= lang('create'); ?></span>
                                                </a>
                                            </li>                                           
                                            
                                       </ul>
                                    </li>
                                     <?php endif; ?>
									
									
									 
									
									
									<?php if($this->isWarehouse) : ?>
                                    <li id="procurment_purchase_orders">
                                        <a class="dropmenu" href="javascript:void(0)">
                                            <span class="text">  <?= lang('purchase_orders'); ?> </span>
                                            <!--<span class="chevron closed"></span>-->
                                        </a>
                                        <ul class="level-3-menu">
                                            <li id="procurment_purchase_orders_index">
                                                <a class="submenu" href="<?= admin_url('procurment/purchase_orders'); ?>">
                                                    <span class="text"> <?= lang('list'); ?></span>
                                                </a>
                                            </li>
                                              <script type="text/javascript">
                                                $(document).on('click','.sidebar_fadeout_pur_ord', function(){
                                                       $("#main-menu-act").trigger("click")
                                                });
                                            </script>
                                            <li id="procurment_purchase_orders_add">
                                                <a class="submenu sidebar_fadeout_pur_ord" href="<?= admin_url('procurment/purchase_orders/add'); ?>">
                                                    <span class="text"> <?= lang('create'); ?></span>
                                                </a>
                                            </li>
                                       </ul>
                                    </li>
                                     <?php endif; ?>
                                    <li id="procurment_purchase_invoices">
                                        <a class="dropmenu" href="javascript:void(0)">
                                            <span class="text">  <?= lang('purchase_invoices'); ?> </span>
                                            <!--<span class="chevron closed"></span>-->
                                        </a>
                                        <ul class="level-3-menu">
                                            <li id="procurment_purchase_invoices_index">
                                                <a class="submenu" href="<?= admin_url('procurment/purchase_invoices'); ?>">
                                                    <span class="text"> <?= lang('list'); ?></span>
                                                </a>
                                            </li>
                                            <script type="text/javascript">
                                                $(document).on('click','.sidebar_fadeout_pur_inv', function(){
                                                       $("#main-menu-act").trigger("click")
                                                });
                                            </script>
                                            <li id="procurment_purchase_invoices_add">
                                                <a class="submenu sidebar_fadeout_pur_inv" href="<?= admin_url('procurment/purchase_invoices/add'); ?>">
                                                    <span class="text"> <?= lang('create'); ?></span>
                                                </a>
                                            </li>                                          
                                                                                        
                                       </ul>
                                    </li>
									  <li id="procurment_grn">
                                        <a class="dropmenu" href="javascript:void(0)">
                                            <span class="text">  <?= lang('goods_received_note'); ?> </span>
                                            <!--<span class="chevron closed"></span>-->
                                        </a>
                                        <ul class="level-3-menu">
                                            <li id="procurment_grn_index">
                                                <a class="submenu" href="<?= admin_url('procurment/grn'); ?>">
                                                    <span class="text"> <?= lang('list'); ?></span>
                                                </a>
                                            </li>
                                               <script type="text/javascript">
                                                $(document).on('click','.sidebar_fadeout_pur_ret', function(){
                                                       $("#main-menu-act").trigger("click")
                                                });
                                            </script>
                                            <li id="procurment_grn_add">
                                                <a class="submenu sidebar_fadeout_pur_ret" href="<?= admin_url('procurment/grn/add'); ?>">
                                                    <span class="text"> <?= lang('create'); ?></span>
                                                </a>
                                            </li>                                          
                                                                                        
                                       </ul>
                                    </li>
                                   <li id="procurment_purchase_returns">
                                        <a class="dropmenu" href="javascript:void(0)">
                                            <span class="text">  <?= lang('purchase_returns'); ?> </span>
                                            <!--<span class="chevron closed"></span>-->
                                        </a>
                                        <ul class="level-3-menu">
                                            <li id="procurment_purchase_returns_index">
                                                <a class="submenu" href="<?= admin_url('procurment/purchase_returns'); ?>">
                                                    <span class="text"> <?= lang('list'); ?></span>
                                                </a>
                                            </li>
                                               <script type="text/javascript">
                                                $(document).on('click','.sidebar_fadeout_pur_ret', function(){
                                                       $("#main-menu-act").trigger("click")
                                                });
                                            </script>
                                            <li id="procurment_purchase_returns_add">
                                                <a class="submenu sidebar_fadeout_pur_ret" href="<?= admin_url('procurment/purchase_returns/add'); ?>">
                                                    <span class="text"> <?= lang('create'); ?></span>
                                                </a>
                                            </li>                                          
                                                                                        
                                       </ul>
                                    </li>
                                   <!--<li id="procurment_index">
                                        <a class="dropmenu" href="javascript:void(0)">
                                            <span class="text">  <?= lang('purchase_returns'); ?> </span>
                                            
                                        </a>
                                        <ul class="level-3-menu">
                                            <li id="procurment_index">
                                                <a class="submenu" href="<?= admin_url('procurment/purchase_returns'); ?>">
                                                    <span class="text"> <?= lang('list_purchase_returns'); ?></span>
                                                </a>
                                            </li>
                                            <li id="procurment_index">
                                                <a class="submenu" href="<?= admin_url('procurment/purchase_returns/add'); ?>">
                                                    <span class="text"> <?= lang('purchase_returns_add'); ?></span>
                                                </a>
                                            </li>
                                       </ul>
                                    </li>-->
                                    
                                   <?php  if($this->Settings->supply_chain) : ?>
                                    
                                       <li id="procurment_store_transfers">
                                           <a class="dropmenu" href="javascript:void(0)">
                                               <span class="text">  <?= lang('store_transfers'); ?> </span>
                                               <!--<span class="chevron closed"></span> -->
                                           </a>
                                           <ul class="level-3-menu">
                                               <li id="procurment_store_transfers_index">
                                                   <a class="submenu" href="<?= admin_url('procurment/store_transfers'); ?>">
                                                       <span class="text"> <?= lang('list'); ?></span>
                                                   </a>
                                               </li>
                                               <li id="procurment_store_transfers_add">
                                                   <a class="submenu" href="<?= admin_url('procurment/store_transfers/add'); ?>">
                                                       <span class="text"> <?= lang('create'); ?></span>
                                                   </a>
                                               </li>
                                          </ul>
                                       </li>
                                       
                                       <li id="procurment_store_receivers">
                                           <a class="dropmenu" href="javascript:void(0)">
                                               <span class="text">  <?= lang('store_receivers'); ?> </span>
                                               <!--<span class="chevron closed"></span> -->
                                           </a>
                                           <ul class="level-3-menu">
                                               <li id="procurment_store_receivers_index">
                                                   <a class="submenu" href="<?= admin_url('procurment/store_receivers'); ?>">
                                                       <span class="text"> <?= lang('list'); ?></span>
                                                   </a>
                                               </li>
                                               <li id="procurment_store_receivers_add">
                                                   <a class="submenu" href="<?= admin_url('procurment/store_receivers/add'); ?>">
                                                       <span class="text"> <?= lang('create'); ?></span>
                                                   </a>
                                               </li>
                                          </ul>
                                       </li>
                                       <!--
                                       <li id="procurment_store_returns">
                                           <a class="dropmenu" href="javascript:void(0)">
                                               <span class="text">  <?= lang('store_returns'); ?> </span>
                                               <!--<span class="chevron closed"></span> 
                                           </a>
                                           <ul class="level-3-menu">
                                               <li id="procurment_store_returns_index">
                                                   <a class="submenu" href="<?= admin_url('procurment/store_returns'); ?>">
                                                       <span class="text"> <?= lang('list'); ?></span>
                                                   </a>
                                               </li>
                                               <li id="procurment_store_returns_add">
                                                   <a class="submenu" href="<?= admin_url('procurment/store_returns/add'); ?>">
                                                       <span class="text"> <?= lang('create'); ?></span>
                                                   </a>
                                               </li>
                                          </ul>
                                       </li>
                                       
                                        <li id="procurment_store_return_receivers">
                                           <a class="dropmenu" href="javascript:void(0)">
                                               <span class="text">  <?= lang('store_return_receivers'); ?> </span>
                                               <!--<span class="chevron closed"></span> 
                                           </a>
                                           <ul class="level-3-menu">
                                               <li id="procurment_store_return_receivers_index">
                                                   <a class="submenu" href="<?= admin_url('procurment/store_return_receivers'); ?>">
                                                       <span class="text"> <?= lang('list'); ?></span>
                                                   </a>
                                               </li>
                                               <li id="procurment_store_return_receivers_add">
                                                   <a class="submenu" href="<?= admin_url('procurment/store_return_receivers/add'); ?>">
                                                       <span class="text"> <?= lang('create'); ?></span>
                                                   </a>
                                               </li>
                                          </ul>
                                       </li> -->
                                    <?php  endif; ?> 
                                 
                                </ul>
                            </li>
                            
							<?php
							}
							?>
                                                        <li class="mm_tables">
                                <a class="dropmenu" href="#">
                                    <i class="fa fa-cutlery"></i>
                                    <span class="text"> <?= lang('restaurents'); ?> </span>
                                    <span class="chevron closed"></span>
                                </a>
                                <ul>
                                  
                                  <?php if(!$this->Settings->qsr){ ?>
                                    <li id="tables_areas">
                                        <a class="submenu" href="<?= admin_url('tables/areas'); ?>">
                                            <span class="text"> <?= lang('table_areas'); ?></span>
                                        </a>
                                    </li>
                                    <li id="tables_index">
                                        <a class="submenu" href="<?= admin_url('tables/index'); ?>">
                                            <span class="text"> <?= lang('tables'); ?></span>
                                        </a>
                                    </li>
                                    <?php } ?>
                                    
                                    <li id="tables_kitchens">
                                        <a class="submenu" href="<?= admin_url('tables/kitchens'); ?>">
                                            <span class="text"> <?= lang('kitchens'); ?></span>
                                        </a>
                                    </li>
                                    
                                </ul>
                            </li>
                            <li class="mm_auth mm_customers mm_suppliers mm_billers">
                                <a class="dropmenu" href="#">
                                    <i class="fa fa-users"></i>
                                    <span class="text"> <?= lang('people'); ?> </span>
                                    <span class="chevron closed"></span>
                                </a>
                                <ul>
                                    <?php if ($Owner) { ?>
                                    <li id="auth_users">
                                        <a class="submenu" href="<?= admin_url('users'); ?>">
                                            <span class="text"> <?= lang('user_list'); ?></span>
                                        </a>
                                    </li>
                                    <li id="auth_create_user">
                                        <a class="submenu" href="<?= admin_url('users/create_user'); ?>">
                                            <span class="text"> <?= lang('new_user'); ?></span>
                                        </a>
                                    </li>
                                    <li id="billers_index">
                                        <a class="submenu" href="<?= admin_url('billers'); ?>">
                                            <span class="text"> <?= lang('counters_list'); ?></span>
                                        </a>
                                    </li>
                                    <li id="billers_index">
                                        <a class="submenu" href="<?= admin_url('billers/add'); ?>" data-toggle="modal" data-target="#myModal">
                                            <span class="text"> <?= lang('add_counter'); ?></span>
                                        </a>
                                    </li>
                                    <?php } ?>
                                    
                                    <li id="customers_index">
                                        <a class="submenu" href="<?= admin_url('customers'); ?>">
                                            <span class="text"> <?= lang('customers_list'); ?></span>
                                        </a>
                                    </li>
                                    <li id="customers_index">
                                        <a class="submenu" href="<?= admin_url('customers/add'); ?>" data-toggle="modal" data-target="#myModal">
                                            <span class="text"> <?= lang('add_customer'); ?></span>
                                        </a>
                                    </li>
                                    <li id="suppliers_index">
                                        <a class="submenu" href="<?= admin_url('suppliers'); ?>">
                                            <span class="text"> <?= lang(' supplier_list'); ?></span>
                                        </a>
                                    </li>
                                    <li id="suppliers_index">
                                        <a class="submenu" href="<?= admin_url('suppliers/add'); ?>" data-toggle="modal" data-target="#myModal">
                                            <span class="text"> <?= lang('add_supplier'); ?></span>
                                        </a>
                                    </li>
                                </ul>
                            </li>

                            <li class="mm_loyal">
                                <a class="dropmenu" href="#">
                                    <i class="fa fa-credit-card"></i>
                                    <span class="text"> <?= lang('loyalty'); ?> </span>
                                    <span class="chevron closed"></span>
                                </a>
                                <ul>
                                    <?php if ($Owner) { ?>
                                    <li id="loyalty_list">
                                        <a class="submenu" href="<?= admin_url('loyalty_settings'); ?>">
                                            <span class="text"> <?= lang('loyalty_settings'); ?></span>
                                        </a>
                                    </li>
                                    <!-- <li id="loyalty_add">
                                        <a class="submenu" href="<?= admin_url('loyalty_settings/add'); ?>">
                                            <span class="text"> <?= lang('add_loyalty'); ?></span>
                                        </a>
                                    </li> -->  
                                     <li id="loyalty_card">
                                        <a class="submenu" href="<?= admin_url('loyalty_settings/loyalty_card'); ?>">
                                            <span class="text"> <?= lang('loyalty_card'); ?></span>
                                        </a>
                                    </li>   
                                    <?php } ?>                                   
                                </ul>
                            </li>
                            <li class="mm_shiftmaster">
                                <a class="dropmenu" href="#">
                                    <i class="fa fa-credit-card"></i>
                                    <span class="text"> <?= lang('shiftmaster'); ?> </span>
                                    <span class="chevron closed"></span>
                                </a>
                                <ul>
                                    <?php if ($Owner) { ?>
                                    <li id="shiftmaster_list">
                                        <a class="submenu" href="<?= admin_url('shiftmaster'); ?>">
                                            <span class="text"> <?= lang('shiftmaster'); ?></span>
                                        </a>
                                    </li>
                                     <li id="shiftmaster_add">
                                        <a class="submenu" href="<?= admin_url('shiftmaster/add'); ?>">
                                            <span class="text"> <?= lang('add_shiftmaster'); ?></span>
                                        </a>
                                    </li>   
                                      
                                    <?php } ?>                                   
                                </ul>
                            </li>
                            <!-- <li class="mm_maintenance">
                                <a class="dropmenu" href="#">
                                    <i class="fa fa-cog"></i>
                                    <span class="text"> <?= lang('maintenance'); ?> </span>
                                    <span class="chevron closed"></span>
                                </a>
                                <ul>
                                    <?php if ($Owner) { ?>
                                    <li id="maintenance_upgrade">
                                        <a class="submenu" href="<?= site_url('maintenance/upgrade'); ?>">
                                            <span class="text"> <?= lang('upgrade'); ?></span>
                                        </a>
                                    </li>
                                   
                                    <?php } ?>                                   
                                </ul>
                            </li> -->
                             <li class="mm_member_discount">
                                <a class="dropmenu" href="#">
                                    <i class="fa fa-credit-card"></i>
                                    <span class="text"> <?= lang('member_discount'); ?> </span>
                                    <span class="chevron closed"></span>
                                </a>
                                <ul>
                                  
                                  	  <li id="system_settings_member_discount">
                                            <a href="<?= admin_url('member_discount') ?>">
                                                <span class="text"> <?= lang('member_discount'); ?></span>
                                            </a>
                                        </li>
										  <li id="system_settings_member_discount">
                                            <a href="<?= admin_url('member_discount/member_discount_card') ?>">
                                                <span class="text"> <?= lang('member_discount_card'); ?></span>
                                            </a>
                                        </li>
										 <li id="system_settings_member_discount">
                                            <a href="<?= admin_url('member_discount/member_discount_card_issue') ?>">
                                                <span class="text"> <?= lang('card Issue'); ?></span>
                                            </a>
                                        </li>
                                    
                                </ul>
                            </li>
                            <li class="mm_notifications">
                                <a class="submenu" href="<?= admin_url('notifications'); ?>">
                                    <i class="fa fa-info-circle"></i>
                                    <span class="text"> <?= lang('notifications'); ?></span>
                                </a>
                            </li>
                            
                                 
                            
                            <?php if ($Owner) { ?>
                                <li class="mm_system_settings <?= strtolower($this->router->fetch_method()) == 'sales' ? '' : 'mm_pos' ?>">
                                    <a class="dropmenu" href="#">
                                        <i class="fa fa-cog"></i>
                                        <span class="text"> <?= lang('settings'); ?> </span>
                                        <span class="chevron closed"></span>
                                    </a>
                                    <ul>
                                        <li id="system_settings_index">
                                            <a href="<?= admin_url('system_settings') ?>">
                                                <span class="text"> <?= lang('system_settings'); ?></span>
                                            </a>
                                        </li>
                                        <?php if (POS) { ?>
                                        <li id="pos_settings">
                                            <a href="<?= admin_url('pos/settings') ?>">
                                                <span class="text"> <?= lang('pos_settings'); ?></span>
                                            </a>
                                        </li>
                                        <li id="pos_printers">
                                            <a href="<?= admin_url('pos/printers') ?>">
                                                <span class="text"> <?= lang('list_printers'); ?></span>
                                            </a>
                                        </li>
                                        <li id="pos_add_printer">
                                            <a href="<?= admin_url('pos/add_printer') ?>">
                                                <span class="text"> <?= lang('add_printer'); ?></span>
                                            </a>
                                        </li>
                                        <li id="system_settings_tills">
                                            <a href="<?= admin_url('system_settings/tills') ?>">
                                                <span class="text"> <?= lang('tills'); ?></span>
                                            </a>
                                        </li>
                                         <li id="system_settings_shift_time">
                                            <a href="<?= admin_url('system_settings/shift_time') ?>">
                                                <span class="text"> <?= lang('shift_time'); ?></span>
                                            </a>
                                        </li>
                                        <li id="pos_open_register">
                                            <a href="<?= admin_url('pos/open_register') ?>">
                                                <span class="text"> <?= lang('open_register'); ?></span>
                                            </a>
                                        </li>
                                         <li id="system_settings_sales_type">
                                            <a href="<?= admin_url('system_settings/sales_type') ?>">
                                                <span class="text"> <?= lang('sales_type'); ?></span>
                                            </a>
                                        </li>
                                        <li id="system_settings_category_subcategory_sale_items_mapping">
                                            <a href="<?= admin_url('system_settings/category_subcategory_sale_items_mapping') ?>">
                                                <span class="text"> <?= lang('sales_items_mapping'); ?></span>
                                            </a>
                                        </li>
                                        <?php } ?>
                                        <li id="system_settings_paymentmethods">
                                            <a href="<?= admin_url('system_settings/payment_methods') ?>">
                                                <span class="text"> <?= lang('Payment Methods'); ?></span>
                                            </a>
                                        </li>
                                         <!--<li id="system_settings_bbq_categories">
                                            <a href="<?= admin_url('system_settings/bbqitems/1') ?>">
                                                <span class="text"> <?= lang('BBQ_items'); ?></span>
                                            </a>
                                        </li>-->
                                        <li id="bbq_menu">
                                            <a href="<?= admin_url('system_settings/bbq_menu') ?>">
                                                <span class="text"> <?= lang('bbq_menu'); ?></span>
                                            </a>
                                        </li>

                                        <li id="bbq_discounts">
                                            <a href="<?= admin_url('system_settings/bbq_discounts') ?>">
                                                <span class="text"> <?= lang('BBQ_discounts'); ?></span>
                                            </a>
                                        </li>
                                        <li id="bbq_daywise_discounts">
                                            <a href="<?= admin_url('system_settings/bbq_daywise_discount') ?>">
                                                <span class="text"> <?= lang('bbq_daywise_discount'); ?></span>
                                            </a>
                                        </li>
                                        <li id="bbqbuyxgetx">
                                            <a href="<?= admin_url('system_settings/bbqbuyxgetx') ?>">
                                                <span class="text"> <?= lang('BBQ_buy_x_get_x'); ?></span>
                                            </a>
                                        </li>
                                        <li id="bbq_lobster_discount">
                                            <a href="<?= admin_url('system_settings/bbq_lobster_discount') ?>">
                                                <span class="text"> <?= lang('bbq_lobster_discount'); ?></span>
                                            </a>
                                        </li>
                                        
                                        <li id="system_settings_customfeedback">
                                            <a href="<?= admin_url('system_settings/customfeedback') ?>">
                                                <span class="text"> <?= lang('custom_feedback'); ?></span>
                                            </a>
                                        </li>
                                        <li id="system_settings_change_logo">
                                            <a href="<?= admin_url('system_settings/change_logo') ?>" data-toggle="modal" data-target="#myModal">
                                                <span class="text"> <?= lang('change_logo'); ?></span>
                                            </a>
                                        </li>
                                        
                                        <li id="system_settings_warehouses">
                                            <a href="<?= admin_url('system_settings/warehouses') ?>">
                                                <span class="text"> <?= lang('stores'); ?></span>
                                            </a>
                                        </li>
                                       
                                        <li id="system_settings_currencies">
                                            <a href="<?= admin_url('system_settings/currencies') ?>">
                                                <span class="text"> <?= lang('currencies'); ?></span>
                                            </a>
                                        </li>
                                        <li id="system_settings_customer_groups">
                                            <a href="<?= admin_url('system_settings/customer_groups') ?>">
                                                <span class="text"> <?= lang('customer_groups'); ?></span>
                                            </a>
                                        </li>
                                        <!--<li id="system_settings_price_groups">
                                            <a href="<?= admin_url('system_settings/price_groups') ?>">
                                                <span class="text"> <?= lang('price_groups'); ?></span>
                                            </a>
                                        </li>-->
                                        <li id="system_settings_categories">
                                            <a href="<?= admin_url('system_settings/categories') ?>">
                                                <span class="text"> <?= lang('categories'); ?></span>
                                            </a>
                                        </li>
                                        <li id="system_settings_recipe_categories">
                                            <a href="<?= admin_url('system_settings/recipecategories') ?>">
                                                <span class="text"> <?= lang('recipe_groups'); ?></span>
                                            </a>
                                        </li>
                                        <li id="system_settings_expense_categories">
                                            <a href="<?= admin_url('system_settings/expense_categories') ?>">
                                                <span class="text"> <?= lang('expense_categories'); ?></span>
                                            </a>
                                        </li>
                                        <li id="system_settings_units">
                                            <a href="<?= admin_url('system_settings/units') ?>">
                                                <span class="text"> <?= lang('units'); ?></span>
                                            </a>
                                        </li>
                                        <li id="system_settings_brands">
                                            <a href="<?= admin_url('system_settings/brands') ?>">
                                                <span class="text"> <?= lang('brands'); ?></span>
                                            </a>
                                        </li>
                                       <?php /*<li id="system_settings_variants">
                                            <a href="<?= admin_url('system_settings/variants') ?>">
                                                <span class="text"> <?= lang('variants'); ?></span>
                                            </a>
                                        </li>*/?>
                                        <!--<li id="system_settings_sales_type">
                                            <a href="<?= admin_url('system_settings/sales_type') ?>">
                                                <span class="text"> <?= lang('sales_type'); ?></span>
                                            </a>
                                        </li>-->
                                        <li id="system_settings_tax_rates">
                                            <a href="<?= admin_url('system_settings/tax_rates') ?>">
                                                <span class="text"> <?= lang('tax_rates'); ?></span>
                                            </a>
                                        </li>
                                         <li id="system_settings_service_charge">
                                            <a href="<?= admin_url('system_settings/service_charge') ?>">
                                                <span class="text"> <?= lang('service_charge'); ?></span>
                                            </a>
                                        </li>
										
										<li id="system_settings_discounts">
                                            <a href="<?= admin_url('system_settings/discounts') ?>">
                                                <span class="text"> <?= lang('discounts'); ?></span>
                                            </a>
                                        </li>
                                        <li id="customer_discounts">
                                            <a href="<?= admin_url('system_settings/customer_discounts') ?>">
                                                <span class="text"> <?= lang('customer_discounts'); ?></span>
                                            </a>
                                        </li>
                                        
                                        <li id="system_settings_tax_rates">
                                            <a href="<?= admin_url('system_settings/buy_get') ?>">
                                                <span class="text"> <?= lang('buy_get'); ?></span>
                                            </a>
                                        </li>
                                        
                                        <li id="system_settings_email_templates">
                                            <a href="<?= admin_url('system_settings/email_templates') ?>">
                                                <span class="text"> <?= lang('email_templates'); ?></span>
                                            </a>
                                        </li>
                                        <li id="system_settings_user_groups">
                                            <a href="<?= admin_url('system_settings/user_groups') ?>">
                                                <span class="text"> <?= lang('group_permissions'); ?></span>
                                            </a>
                                        </li>
                                        <li id="system_settings_backups">
                                            <a href="<?= admin_url('system_settings/backups') ?>">
                                                <span class="text"> <?= lang('backups'); ?></span>
                                            </a>
                                        </li>
                                        <li id="system_settings_recipe_feedback_mapping">
                                            <a href="<?= admin_url('system_settings/recipe_feedback_mapping') ?>">
                                                <span class="text"> <?= lang('recipe_feedback_mapping'); ?></span>
                                            </a>
                                        </li>
										 <li id="system_settings_Wallets">
                                            <a href="<?= admin_url('system_settings/Wallets') ?>">
                                                <span class="text"> <?= lang('Wallets'); ?></span>
                                            </a>
                                        </li>
										
										
										 <li id="system_settings_Wallets">
                                            <a href="<?= admin_url('system_settings/ncKotMaster') ?>">
                                                <span class="text"> <?= lang('NC_Kot_Master'); ?></span>
                                            </a>
                                        </li>
									
                                        <!-- <li id="system_settings_updates">
                                            <a href="<?= admin_url('system_settings/updates') ?>">
                                                <i class="fa fa-upload"></i><span class="text"> <?= lang('updates'); ?></span>
                                            </a>
                                        </li> -->
                                    </ul>
                                </li>
                            <?php } ?>
                            
                            <li class="mm_reports">
                                <a class="dropmenu" href="#">
                                    <i class="fa fa-bar-chart-o"></i>
                                    <span class="text"> <?= lang('reports'); ?> </span>
                                    <span class="chevron closed"></span>
                                </a>
                                <ul class="level-2-menu">
                                    <li id="reports_index">
                                        <a href="<?= admin_url('reports') ?>">
                                            <span class="text"> <?= lang('overview_chart'); ?></span>
                                        </a>
                                    </li>
                                    
                                    <li id="reports-sales">
                                        <a class="dropmenu" href="javascript:void(0)">
                                            <span class="text">  <?= lang('sales'); ?> </span>
                                            <!--<span class="chevron closed"></span>-->
                                        </a>
                                        <ul class="level-3-menu">
                                            <li id="reports_warehouse_stock">
                                                <a class="submenu" href="<?= admin_url('reports/warehouse_stock') ?>">
                                                    <span class="text"> <?= lang('warehouse_stock'); ?></span>
                                                </a>
                                            </li>
                                          <li id="reports_best_sellers">
                                              <a  class="submenu"  href="<?= admin_url('reports/best_sellers') ?>">
                                                  <span class="text"> <?= lang('best_sellers'); ?></span>
                                              </a>
                                          </li>   
                                          <?php if(!$this->Settings->qsr){ ?>      
                                          <li id="reports_bbq_reports">
                                              <a  class="submenu"  href="<?= admin_url('reports/bbq_reports') ?>">
                                                  <span class="text"> <?= lang('bbq_reports'); ?></span>
                                              </a>
                                          </li>

                                          <li id="reports_bbq_notification_reports">
                                              <a  class="submenu"  href="<?= admin_url('reports/bbq_notification_reports') ?>">
                                                  <span class="text"> <?= lang('bbq_notification_reports'); ?></span>
                                              </a>
                                          </li> 
                                          <?php } ?> 
                                          <li id="reports_recipe">
                                              <a  class="submenu"  href="<?= admin_url('reports/recipe') ?>">
                                                  <span class="text"> <?= lang('item_sale_report'); ?></span>
                                              </a>
                                          </li>
                                          
      
                                           <li id="reports_pos_settlement">
                                              <a  class="submenu"  href="<?= admin_url('reports/pos_settlement') ?>">
                                                  <span class="text"> <?= lang('pos_settlement_report'); ?></span>
                                              </a>
                                          </li>
                                          <?php if(!$this->Settings->qsr){ ?>
                                          <li id="reports_kot_details">
                                              <a  class="submenu"  href="<?= admin_url('reports/kot_details') ?>">
                                                  <span class="text"> <?= lang('kot_details_report'); ?></span>
                                              </a>
                                          </li>

                                          <li id="reports_user_reports">
                                              <a  class="submenu"  href="<?= admin_url('reports/user_reports') ?>">
                                                  <span class="text"> <?= lang('user_report'); ?></span>
                                              </a>
                                          </li>
                                          
                                           <li id="reports_home_delivery">
                                              <a  class="submenu"  href="<?= admin_url('reports/home_delivery') ?>">
                                                  <span class="text"> <?= lang('home_delivery_report'); ?></span>
                                              </a>
                                          </li>
                                          <li id="reports_take_away">
                                              <a  class="submenu"  href="<?= admin_url('reports/take_away') ?>">
                                                  <span class="text"> <?= lang('take_away'); ?></span>
                                              </a>
                                          </li>
                                          <?php } ?>
                                           <li id="reports_daywise">
                                              <a  class="submenu"  href="<?= admin_url('reports/daywise') ?>">
                                                  <span class="text"> <?= lang('day_wise'); ?></span>
                                              </a>
                                          </li> 
                                           <li id="reports_days_reports">
                                              <a  class="submenu"  href="<?= admin_url('reports/days_reports') ?>">
                                                  <span class="text"> <?= lang('day_wise_sale_report'); ?></span>
                                              </a>
                                          </li> 
                                          <li id="reports_shifttime_reports">
                                              <a  class="submenu"  href="<?= admin_url('reports/shifttime_reports') ?>">
                                                  <span class="text"> <?= lang('shift_time_reports'); ?></span>
                                              </a>
                                          </li> 

                                          <?php if(!$this->Settings->qsr){ ?>
                                          <li id="reports_bill_details">
                                              <a  class="submenu"  href="<?= admin_url('reports/bill_details') ?>">
                                                  <span class="text"> <?= lang('bill_details_report'); ?></span>
                                              </a>
                                          </li>
                                      <?php } ?>
                                          <li id="reports_postpaid_bills">
                                              <a  class="submenu"  href="<?= admin_url('reports/postpaid_bills') ?>">
                                                  <span class="text"> <?= lang('postpaid_bills_report'); ?></span>
                                              </a>
                                          </li> 
                                          <li id="reports_monthly_reports">
                                              <a  class="submenu"  href="<?= admin_url('reports/monthly_reports') ?>">
                                                  <span class="text"> <?= lang('category_wise_monthly_sales_report'); ?></span>
                                              </a>
                                          </li> 
                                          <li id="reports_hourly_wise">
                                              <a  class="submenu"  href="<?= admin_url('reports/hourly_wise') ?>">
                                                  <span class="text"> <?= lang('hourly_wise'); ?></span>
                                              </a>
                                          </li> 
                                          <li id="reports_discount_summary">
                                              <a  class="submenu"  href="<?= admin_url('reports/discount_summary') ?>">
                                                  <span class="text"> <?= lang('discount_summary'); ?></span>
                                              </a>
                                          </li>   
                                          <li id="reports_void_bills">
                                              <a  class="submenu"  href="<?= admin_url('reports/void_bills') ?>">
                                                  <span class="text"> <?= lang('void_bill'); ?></span>
                                              </a>
                                          </li>   
                                          <li id="reports_tax_reports">
                                              <a  class="submenu"  href="<?= admin_url('reports/tax_reports') ?>">
                                                  <span class="text"> <?= lang('tax_reports'); ?></span>
                                              </a>
                                          </li>   
                                          <li id="reports_popular_analysis">
                                              <a  class="submenu"  href="<?= admin_url('reports/popular_analysis') ?>">
                                                  <span class="text"> <?= lang('popular_analysis_reports'); ?></span>
                                              </a>
                                          </li>
                                          <li id="reports_cover_analysis">
                                              <a  class="submenu"  href="<?= admin_url('reports/cover_analysis') ?>">
                                                  <span class="text"> <?= lang('cover_analysis'); ?></span>
                                              </a>
                                          </li>
                                          <?php if(!$this->Settings->qsr){ ?>
                                          <?php if($this->Settings->recipe_time_management) : ?>
                                            <li id="reports_order_timing">
                                                <a class="submenu" href="<?= admin_url('reports/order_timing') ?>">
                                                    <span class="text"> <?= lang('order_time_report'); ?></span>
                                                </a>
                                            </li>
                                            <?php endif; } ?>
                                            <li id="reports_products">
                                                <a  class="submenu"  href="<?= admin_url('reports/products') ?>">
                                                    <span class="text"> <?= lang('products_report'); ?></span>
                                                </a>
                                            </li>
                                            <!-- <li id="reports_adjustments">
                                                <a  class="submenu"  href="<?= admin_url('reports/adjustments') ?>">
                                                    <span class="text"> <?= lang('adjustments_report'); ?></span>
                                                </a>
                                            </li> -->
                                            <li id="reports_categories">
                                                <a  class="submenu"  href="<?= admin_url('reports/categories') ?>">
                                                    <span class="text"> <?= lang('categories_report'); ?></span>
                                                </a>
                                            </li>
                                            <li id="reports_brands">
                                                <a  class="submenu"  href="<?= admin_url('reports/brands') ?>">
                                                    <span class="text"> <?= lang('brands_report'); ?></span>
                                                </a>
                                            </li>
                                            <li id="reports_daily_sales">
                                                <a  class="submenu"  href="<?= admin_url('reports/daily_sales') ?>">
                                                    <span class="text"> <?= lang('daily_sales'); ?></span>
                                                </a>
                                            </li>
                                            <li id="reports_monthly_sales">
                                                <a  class="submenu"  href="<?= admin_url('reports/monthly_sales') ?>">
                                                    <span class="text"> <?= lang('monthly_sales'); ?></span>
                                                </a>
                                            </li>
                                            <li id="reports_sales">
                                                <a  class="submenu"  href="<?= admin_url('reports/sales') ?>">
                                                    <span class="text"> <?= lang('sales_report'); ?></span>
                                                </a>
                                            </li>
                                            <li id="reports_loyalty_points">
                                                <a  class="submenu"  href="<?= admin_url('reports/loyalty_points') ?>">
                                                    <span class="text"> <?= lang('loyalty_point_summary'); ?></span>
                                                </a>
                                            </li>
                                            <li id="reports_bill_reprint">
                                              <a  class="submenu"  href="<?= admin_url('reports/bill_reprint') ?>">
                                                  <span class="text"> <?= lang('bill_reprint'); ?></span>
                                              </a>
                                          </li>
										     <li id="reports_nc_kot">
                                              <a  class="submenu"  href="<?= admin_url('reports/nc_kot') ?>">
                                                  <span class="text"> <?= lang('NC_Kot'); ?></span>
                                              </a>
                                          </li> 
										  <li id="reports_nc_kot">
                                              <a  class="submenu"  href="<?= admin_url('reports/resettlement') ?>">
                                                  <span class="text"> <?= lang('resettlement'); ?></span>
                                              </a>
                                          </li> 
                                       </ul>
                                    </li>
                                    <li>
                                        <a class="dropmenu" href="javascript:void(0)">
                                            <span class="text">  <?= lang('purchase'); ?> </span>
                                            <!--<span class="chevron closed"></span>-->
                                        </a>
                                        <ul class="level-3-menu">
                                           <li id="reports_daily_purchases">
                                                <a class="submenu" href="<?= admin_url('reports/daily_purchases') ?>">
                                                    <span class="text"> <?= lang('daily_purchases'); ?></span>
                                                </a>
                                            </li>
                                            <li id="reports_monthly_purchases">
                                                <a class="submenu" href="<?= admin_url('reports/monthly_purchases') ?>">
                                                    <span class="text"> <?= lang('monthly_purchases'); ?></span>
                                                </a>
                                            </li>
                                            <li id="reports_purchases">
                                                <a class="submenu" href="<?= admin_url('reports/purchases') ?>">
                                                    <span class="text"> <?= lang('purchases_report'); ?></span>
                                                </a>
                                            </li>
                                            <li id="reports_store_request_reports">
                                                <a class="submenu" href="<?= admin_url('reports/store_request_reports') ?>">
                                                    <span class="text"> <?= lang('store_request_reports'); ?></span>
                                                </a>
                                            </li>
                                            <li id="reports_quotes_request_reports">
                                                <a class="submenu" href="<?= admin_url('reports/quotes_request_reports') ?>">
                                                    <span class="text"> <?= lang('quotes_request_reports'); ?></span>
                                                </a>
                                            </li>
                                            <li id="reports_quotation_reports">
                                                <a class="submenu" href="<?= admin_url('reports/quotation_reports') ?>">
                                                    <span class="text"> <?= lang('quotation_reports'); ?></span>
                                                </a>
                                            </li>
                                            <li id="reports_purchase_order_reports">
                                                <a class="submenu" href="<?= admin_url('reports/purchase_order_reports') ?>">
                                                    <span class="text"> <?= lang('purchase_order_reports'); ?></span>
                                                </a>
                                            </li>
                                            <li id="reports_purchase_invoice_reports">
                                                <a class="submenu" href="<?= admin_url('reports/purchase_invoice_reports') ?>">
                                                    <span class="text"> <?= lang('purchase_invoice_reports'); ?></span>
                                                </a>
                                            </li>
                                            <li id="reports_purchase_return_reports">
                                                <a class="submenu" href="<?= admin_url('reports/purchase_return_reports') ?>">
                                                    <span class="text"> <?= lang('purchase_return_reports'); ?></span>
                                                </a>
                                            </li>
                                            <li id="reports_purchase_order_summary_reports">
                                                <a class="submenu" href="<?= admin_url('reports/purchase_order_summary_reports') ?>">
                                                    <span class="text"> <?= lang('purchase_order_summary_reports'); ?></span>
                                                </a>
                                            </li>
                                            <li id="reports_purchase_invoice_summary_reports">
                                                <a class="submenu" href="<?= admin_url('reports/purchase_invoice_summary_reports') ?>">
                                                    <span class="text"> <?= lang('purchase_invoice_summary_reports'); ?></span>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li>
                                        <a class="dropmenu" href="javascript:void(0)">
                                            <span class="text">  <?= lang('inventory'); ?> </span>
                                            <!--<span class="chevron closed"></span>-->
                                        </a>
                                        <ul class="level-3-menu">
                                         <li id="reports_item_stock">
                                              <a  class="submenu"  href="<?= admin_url('reports/item_stock') ?>">
                                                  <span class="text"> <?= lang('item_stock_report'); ?></span>
                                              </a>
                                          </li>  
                                            <li id="reports_stock_audit">
                                                <a class="submenu" href="<?= admin_url('reports/stock_audit') ?>">
                                                    <span class="text"> <?= lang('stock_audit'); ?></span>
                                                </a>
                                            </li>   
                                            <li id="reports_quantity_alerts">
                                                <a class="submenu" href="<?= admin_url('reports/quantity_alerts') ?>">
                                                    <span class="text"> <?= lang('product_quantity_alerts'); ?></span>
                                                </a>
                                            </li>
                                            <?php if ($Settings->product_expiry) { ?>
                                            <li id="reports_expiry_alerts">
                                                <a class="submenu" href="<?= admin_url('reports/expiry_alerts') ?>">
                                                    <span class="text"> <?= lang('product_expiry_alerts'); ?></span>
                                                </a>
                                            </li>
                                            <?php } ?>
                                        </ul>
                                    </li>
                                    <li>
                                        <a class="dropmenu" href="javascript:void(0)">
                                            <span class="text">  <?= lang('Payment&Receipts'); ?> </span>
                                            <!--<span class="chevron closed"></span>-->
                                        </a>
                                        <ul class="level-3-menu">
                                            <li id="reports_payments">
                                                <a class="submenu" href="<?= admin_url('reports/payments') ?>">
                                                    <span class="text"> <?= lang('payments_report'); ?></span>
                                                </a>
                                            </li>
                                            <li id="reports_profit_loss">
                                                <a class="submenu" href="<?= admin_url('reports/profit_loss') ?>">
                                                    <span class="text"> <?= lang('profit_and_loss'); ?></span>
                                                </a>
                                            </li>
                                           
                                            <!-- <li id="reports_expenses">
                                                <a href="<?= admin_url('reports/expenses') ?>">
                                                    <span class="text"> <?= lang('expenses_report'); ?></span>
                                                </a>
                                            </li> -->
                                            <li id="reports_customers">
                                                <a class="submenu" href="<?= admin_url('reports/customers') ?>">
                                                    <span class="text"> <?= lang('customers_report'); ?></span>
                                                </a>
                                            </li>
                                            <li id="reports_suppliers">
                                                <a class="submenu" href="<?= admin_url('reports/suppliers') ?>">
                                                    <span class="text"> <?= lang('suppliers_report'); ?></span>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li>
                                        <a class="dropmenu" href="javascript:void(0)">
                                            <span class="text">  <?= lang('people'); ?> </span>
                                            <!--<span class="chevron closed"></span>-->
                                        </a>
                                        <ul class="level-3-menu">
                                             <li id="reports_users">
                                                <a class="submenu" href="<?= admin_url('reports/users') ?>">
                                                    <span class="text"> <?= lang('staff_report'); ?></span>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                    <?php if(!$this->Settings->qsr){ ?>
                                    <li>
                                        <a class="dropmenu" href="javascript:void(0)">
                                            <span class="text">  <?= lang('feedback'); ?> </span>
                                            <!--<span class="chevron closed"></span>-->
                                        </a>
                                        <ul class="level-3-menu">
                                            <li id="reports_feedback">
                                                <a class="submenu" href="<?= admin_url('reports/feedback') ?>">
                                                    <span class="text"> <?= lang('feedback'); ?></span>
                                                </a>
                                            </li> 
                                        </ul>
                                    </li>
                                <?php } ?>
                                <?php if($Owner || $Admin){ ?>
                                    <li>
                                        <a class="dropmenu" href="javascript:void(0)">
                                            <span class="text">  <?= lang('Bill'); ?> </span>
                                            <!--<span class="chevron closed"></span>-->
                                        </a>
                                        <ul class="level-3-menu">
                                            <li id="reports_items_mapping_for_modify_bills">
                                                <a class="submenu" href="<?= admin_url('reports/items_mapping_for_modify_bills') ?>">
                                                    <span class="text"> <?= lang('reports_items_mapping_for_modify_bills'); ?></span>
                                                </a>
                                            </li>
                                            
                                            <li id="reports_modify_bills">
                                                <a class="submenu" href="<?= admin_url('reports/modify_bills') ?>">
                                                    <span class="text"> <?= lang('modify_bills'); ?></span>
                                                </a>
                                            </li>
                                            <li id="reports_restore_bills">
                                                <a class="submenu" href="<?= admin_url('reports/restore_bills') ?>">
                                                    <span class="text"> <?= lang('restore_bills'); ?></span>
                                                </a>
                                            </li>
                                            <li id="reports_auto_modify_bills">
                                                <a class="submenu" href="<?= admin_url('reports/auto_modify_bills') ?>">
                                                    <span class="text"> <?= lang('auto_modify_bills'); ?></span>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                <?php } ?>
                                      
                                                                                                       
                                    <?php /*if (POS) { ?>
                                    <li id="reports_register">
                                        <a href="<?= admin_url('reports/register') ?>">
                                            <span class="text"> <?= lang('register_report'); ?></span>
                                        </a>
                                    </li>
                                    <?php } */?>
                                    
                                    
                                    
                                   
                                </ul>
                            </li>
                            <?php if ($Owner && file_exists(APPPATH.'controllers'.DIRECTORY_SEPARATOR.'shop'.DIRECTORY_SEPARATOR.'Shop.php')) { ?>
                            <li class="mm_shop_settings mm_api_settings">
                                <a class="dropmenu" href="#">
                                    <i class="fa fa-shopping-cart"></i><span class="text"> <?= lang('front_end'); ?> </span>
                                    <span class="chevron closed"></span>
                                </a>
                                <ul>
                                    <li id="shop_settings_index">
                                        <a href="<?= admin_url('shop_settings') ?>">
                                            <i class="fa fa-cog"></i><span class="text"> <?= lang('shop_settings'); ?></span>
                                        </a>
                                    </li>
                                    <li id="shop_settings_slider">
                                        <a href="<?= admin_url('shop_settings/slider') ?>">
                                            <i class="fa fa-file"></i><span class="text"> <?= lang('slider_settings'); ?></span>
                                        </a>
                                    </li>
                                    <?php if ($this->Settings->apis) { ?>
                                    <li id="api_settings_index">
                                        <a href="<?= admin_url('api_settings') ?>">
                                            <i class="fa fa-key"></i><span class="text"> <?= lang('api_keys'); ?></span>
                                        </a>
                                    </li>
                                    <?php } ?>
                                    <li id="shop_settings_pages">
                                        <a href="<?= admin_url('shop_settings/pages') ?>">
                                            <i class="fa fa-file"></i><span class="text"> <?= lang('list_pages'); ?></span>
                                        </a>
                                    </li>
                                    <li id="shop_settings_pages">
                                        <a href="<?= admin_url('shop_settings/add_page') ?>">
                                            <i class="fa fa-plus-circle"></i><span class="text"> <?= lang('add_page'); ?></span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <?php } ?>

                        <?php
                        } else { // not owner and not admin
                            ?>
                            <?php if ($GP['nightaudit-index']) { ?>

                            <?php if($this->Settings->night_audit_rights == 1){ ?>
                            <li class="mm_audit">
                                <a href="<?= admin_url('nightaudit') ?>">
                                    <i class="fa fa-star"></i>
                                    <span class="text"> <?= lang('night_audit'); ?></span>
                                </a>
                            </li>
                            <?php } } ?>

                           
                            <?php if ($GP['recipe-index'] || $GP['recipe-add'] || $GP['recipe-csv']) { ?>
                                <li class="mm_products">
                                    <a class="dropmenu" href="#">
                                        <i class="fa fa-barcode"></i>
                                        <span class="text"> <?= lang('item_master'); ?>
                                        </span> <span class="chevron closed"></span>
                                    </a>
                                    <ul>
                                        <?php if($GP['recipe-index']) : ?>
                                        <li id="recipe_index">
                                            <a class="submenu" href="<?= admin_url('recipe'); ?>">
                                                <span class="text"> <?= lang('item_master'); ?></span>
                                            </a>
                                        </li>
                                        <?php endif; ?>
                                        <?php if($GP['recipe-add']) : ?>
                                        <li id="recipe_add">
                                            <a class="submenu" href="<?= admin_url('recipe/add'); ?>">
                                                <span class="text"> <?= lang('add_item'); ?></span>
                                            </a>
                                        </li>
                                        <?php endif; ?>
                                        <?php if($GP['recipe-csv']) : ?>
                                        <li id="recipe-import_csv">
                                            <a class="submenu" href="<?= admin_url('recipe/import_csv'); ?>">
                                                <span class="text"> <?= lang('import_csv'); ?></span>
                                            </a>
                                        </li>
                                        <?php endif; ?>
                                    </ul>
                                </li>
                            <?php } ?>
                            <?php if ($GP['production-index'] || $GP['production-add'] || $GP['production-balance']) { ?>
                               <!-- <li class="mm_products">
                                    <a class="dropmenu" href="#">
                                        <i class="fa fa-barcode"></i>
                                        <span class="text"> <?= lang('Production'); ?>
                                        </span> <span class="chevron closed"></span>
                                    </a>
                                    <ul>
                                        <?php if($GP['production-index']) : ?>
                                        <li id="production_index">
                                            <a class="submenu" href="<?= admin_url('production'); ?>">
                                                <span class="text"> <?= lang('production'); ?></span>
                                            </a>
                                        </li>
                                        <?php endif; ?>
                                        <?php if($GP['production-add']) : ?>
                                        <li id="production_add">
                                            <a class="submenu" href="<?= admin_url('production/add'); ?>">
                                                <span class="text"> <?= lang('add_production'); ?></span>
                                            </a>
                                        </li>
                                        <?php endif; ?>
                                        <?php if($GP['production-balance']) : ?>
                                        <li id="production-import_csv">
                                            <a class="submenu" href="<?= admin_url('production/balance'); ?>">
                                                <span class="text"> <?= lang('balance_production'); ?></span>
                                            </a>
                                        </li>
                                        <?php endif; ?>
                                    </ul>
                                </li>-->
                            <?php } ?>
                            <?php if ($GP['saleitem_to_purchasesitem-index']) { ?>
                               <!--  <li class="dropdown hidden-xs">
                                    <a class="btn tip"  data-placement="bottom" href="<?= admin_url('saleitem_to_purchasesitem') ?>">
                                    <i class="fa fa-dashboard"></i>
                                    <span class="text"><?= lang('bill_of_materials') ?></span>
                                    </a>
                                </li> -->
                            <?php } ?>
                            <?php if ($GP['sales-index'] || $GP['sales-deliveries']) { ?>
                            <li class="mm_sales <?= strtolower($this->router->fetch_method()) == 'sales' ? 'mm_pos' : '' ?>">
                                <a class="dropmenu" href="#">
                                    <i class="fa fa-balance-scale"></i>
                                    <span class="text"> <?= lang('sales'); ?>
                                    </span> <span class="chevron closed"></span>
                                </a>
                                <ul>
                                    <li id="sales_index">
                                        <a class="submenu" href="<?= admin_url('sales'); ?>">
                                            <span class="text"> <?= lang('list_sales'); ?></span>
                                        </a>
                                    </li>
                                    <?php if (POS && $GP['pos-index']) { ?>
                                   <!-- <li id="pos_sales">
                                        <a class="submenu" href="<?= admin_url('pos/sales'); ?>">
                                            <i class="fa fa-heart"></i><span class="text"> <?= lang('pos_sales'); ?></span>
                                        </a>
                                    </li>-->
                                    <?php } ?>
                                    <?php if ($GP['sales-add']) { ?>
                                    <!--<li id="sales_add">
                                        <a class="submenu" href="<?= admin_url('sales/add'); ?>">
                                            <i class="fa fa-plus-circle"></i><span class="text"> <?= lang('add_sale'); ?></span>
                                        </a>
                                    </li>-->
                                    <?php }
                                    if ($GP['sales-deliveries']) { ?>
                                    <li id="sales_deliveries">
                                        <a class="submenu" href="<?= admin_url('sales/deliveries'); ?>">
                                            <span class="text"> <?= lang('deliveries'); ?></span>
                                        </a>
                                    </li>
                                    <?php }
                                    if ($GP['sales-gift_cards']) { ?>
                                   <!-- <li id="sales_gift_cards">
                                        <a class="submenu" href="<?= admin_url('sales/gift_cards'); ?>">
                                            <i class="fa fa-gift"></i><span class="text"> <?= lang('gift_cards'); ?></span>
                                        </a>
                                    </li>-->
                                    <?php } ?>
                                </ul>
                            </li>
                            <?php } ?>

                           
                            
                            <?php
							if($this->Settings->procurment == 1){
                                /*echo "<pre>";
                                print_r($GP);*/
							?>
                            <?php if($GP['production-add'] || $GP['production-index'] || $GP['recipe-list_ingredients'] || $GP['recipe-add_ingredients'] || $GP['recipe-ingredients_import_csv']) : ?>

                             <li class="mm_recipe_management">
                                <a class="dropmenu" href="#">
                                    <i class="fa fa-bar-chart-o"></i>
                                    <span class="text"> <?= lang('recipe_management'); ?> </span>
                                    <span class="chevron closed"></span>
                                </a>
                                <ul class="level-2-menu">                                    
                                    <?php if($GP['production-add'] || $GP['production-index']) : ?>
                                    <li id="recipe_management_production">
                                        <a class="dropmenu" href="javascript:void(0)">
                                            <span class="text">  <?= lang('production'); ?> </span>
                                        </a>
                                        <ul class="level-3-menu">

                                            <?php if($GP['production-index']) : ?>
                                            <li id="recipe_management_production_index">
                                                <a class="submenu" href="<?= admin_url('procurment/production'); ?>">
                                                    <span class="text"> <?= lang('list'); ?></span>
                                                </a>
                                            </li>
                                            <?php endif; ?>  

                                            <?php if($GP['production-add']) : ?>
                                            <li id="recipe_management_production_add">
                                                <a class="submenu" href="<?= admin_url('procurment/production/add'); ?>">
                                                    <span class="text"> <?= lang('create'); ?></span>
                                                </a>
                                            </li>
                                            <?php endif; ?>  
                                       </ul>
                                    </li>  
                                <?php endif; ?>     

                                <?php if( $GP['recipe-list_ingredients'] || $GP['recipe-add_ingredients'] || $GP['recipe-ingredients_import_csv']) : ?>

                                    <li id="recipe_management_ingredients">
                                        <a class="dropmenu" href="javascript:void(0)">
                                            <span class="text">  <?= lang('ingredients'); ?> </span>
                                        </a>
                                        <ul class="level-3-menu">
                                            <?php if($GP['recipe-list_ingredients']) : ?>
                                                <li id="recipe_management_ingredients_index">
                                                    <a class="submenu" href="<?= admin_url('recipe/list_ingredients'); ?>">
                                                        <span class="text"> <?= lang('list_ingredients'); ?></span>
                                                    </a>
                                                </li>
                                            <?php endif; ?>

                                            <?php if($GP['recipe-add_ingredients']) : ?>
                                                <li id="recipe_management_ingredients_add">
                                                    <a class="submenu" href="<?= admin_url('recipe/add_ingredients'); ?>">
                                                        <span class="text"> <?= lang('add_ingredients'); ?></span>
                                                    </a>
                                                </li>   
                                            <?php endif; ?>

                                            <?php if($GP['recipe-ingredients_import_csv']) : ?>
                                             <li id="recipe_management_ingredients_import_csv">
                                                <a class="submenu" href="<?= admin_url('recipe/ingredients_import_csv'); ?>">
                                                    <span class="text"> <?= lang('import_ingredients'); ?></span>
                                                </a>
                                            </li>  
                                            <?php endif; ?>
                                       </ul>
                                    </li>  
                                    <?php endif; ?>     
                                </ul>
                              </li>
                            <?php endif; ?>                            
                            
                            <li class="mm_procurment">
                                <a class="dropmenu" href="#">
                                    <i class="fa fa-bar-chart-o"></i>
                                    <span class="text"> <?= lang('inventory'); ?> </span>
                                    <span class="chevron closed"></span>
                                </a>
                                <ul class="level-2-menu">
                                    <!--<li id="procurment_index">
                                        <a href="<?= admin_url('procurment/inventory/stores') ?>">
                                            <span class="text"> <?= lang('stores'); ?></span>
                                        </a>
                                    </li>-->
									<?php   if($this->isStore){  ?>
                                    <?php if($this->Settings->supply_chain) : ?>
                                    <?php if($GP['store_request_add'] || $GP['store_request_index']) : ?>

                                    <li id="procurment_store_request">
                                        <a class="dropmenu" href="javascript:void(0)">
                                            <span class="text">  <?= lang('store_indent_request'); ?> </span>
                                            <!--<span class="chevron closed"></span>-->
                                        </a>
                                        <ul class="level-3-menu">
                                            <?php if($GP['store_request_index']) : ?>
                                            <li id="procurment_store_request_index">
                                                <a class="submenu" href="<?= admin_url('procurment/store_request'); ?>">
                                                    <span class="text"> <?= lang('list'); ?></span>
                                                </a>
                                            </li>
                                            <?php endif; ?>
                                            <?php if($GP['store_request_add']) : ?>
                                            <li id="procurment_store_request_add">
                                                <a class="submenu" href="<?= admin_url('procurment/store_request/add'); ?>">
                                                    <span class="text"> <?= lang('create'); ?></span>
                                                </a>
                                            </li>
                                            <?php endif; ?>
                                       </ul>
                                    </li>
                                    <?php endif; ?>
                                    <?php endif; ?>
									<?php  }  ?>
                                    <!-- <?php if($GP['production-add'] || $GP['production-index']) : ?>
                                    <li id="procurment_production">
                                        <a class="dropmenu" href="javascript:void(0)">
                                            <span class="text">  <?= lang('production'); ?> </span>
                                            
                                        </a>
                                        <ul class="level-3-menu">
                                         <?php if($GP['production-index']) : ?>
                                            <li id="procurment_production_index">
                                                <a class="submenu" href="<?= admin_url('procurment/production'); ?>">
                                                    <span class="text"> <?= lang('list'); ?></span>
                                                </a>
                                            </li>
                                            <?php endif; ?>
                                            <?php if($GP['production-add']) : ?>
                                            <li id="procurment_production_add">
                                                <a class="submenu" href="<?= admin_url('procurment/production/add'); ?>">
                                                    <span class="text"> <?= lang('create'); ?></span>
                                                </a>
                                            </li>
                                            <?php endif; ?>

                                                                                      
                                       </ul>
                                    </li>
                                    <?php endif; ?> -->
                                    <?php //if($GP['request-add'] || $GP['request-index']) : ?>
                                 <!--   <li id="procurment_request">
                                        <a class="dropmenu" href="javascript:void(0)">
                                            <span class="text">  <?= lang('quotation_request'); ?> </span>
                                            <!--<span class="chevron closed"></span>
                                        </a>
                                        <ul class="level-3-menu">
                                            <?php if($GP['request-index']) : ?>
                                            <li id="procurment_request_index">
                                                <a class="submenu" href="<?= admin_url('procurment/request'); ?>">
                                                    <span class="text"> <?= lang('list'); ?></span>
                                                </a>
                                            </li>
                                            <?php endif; ?>
                                            
                                            <?php if($GP['request-add']) : ?>
                                            <li id="procurment_request_add">
                                                <a class="submenu" href="<?= admin_url('procurment/request/add'); ?>">
                                                    <span class="text"> <?= lang('create'); ?></span>
                                                </a>
                                            </li>
                                            <?php endif; ?>

                                                                                      
                                       </ul>
                                    </li>  --->
                                    <?php //endif; ?>
									
									
									
									
									
									
                                    <?php if($GP['quotes-add'] || $GP['quotes-index']) : ?>
                                    <li id="procurment_quotes">
                                        <a class="dropmenu" href="javascript:void(0)">
                                            <span class="text">  <?= lang('purchase_quotation'); ?> </span>
                                            <!--<span class="chevron closed"></span>-->
                                        </a>
                                        <ul class="level-3-menu">
                                            <?php if($GP['quotes-index']) : ?>
                                            <li id="procurment_quotes_index">
                                                <a class="submenu" href="<?= admin_url('procurment/quotes'); ?>">
                                                    <span class="text"> <?= lang('list'); ?></span>
                                                </a>
                                            </li>
                                            <?php endif; ?>
                                            <?php if($GP['quotes-add']) : ?>
                                            <li id="procurment_quotes_add">
                                                <a class="submenu" href="<?= admin_url('procurment/quotes/add'); ?>">
                                                    <span class="text"> <?= lang('create'); ?></span>
                                                </a>
                                            </li>
                                            <?php endif; ?>
                                            
                                       </ul>
                                    </li>
                                    <?php   endif; ?>
									
									   <?php if($this->isWarehouse) : ?>
									 <li id="procurment_store_indent_receive">
                                        <a class="dropmenu" href="javascript:void(0)">
                                            <span class="text">  <?= lang('store_indent_request_receive'); ?> </span>
                                        </a>
                                        <ul class="level-3-menu">
                                           <li id="procurment_store_indent_receive_index">
                                        <a class="submenu" href="<?= admin_url('procurment/store_indent_receive'); ?>">
                                                   <span class="text"> <?= lang('list'); ?></span></a>
                                        </li>
                                       </ul>
                                   </li>
									 <?php   endif; ?>
									
									
                                    <?php if($GP['purchase_orders-add'] || $GP['purchase_orders-index']) : ?>
                                    <li id="procurment_purchase_orders">
                                        <a class="dropmenu" href="javascript:void(0)">
                                            <span class="text">  <?= lang('purchase_orders'); ?> </span>
                                            <!--<span class="chevron closed"></span>-->
                                        </a>
                                        <ul class="level-3-menu">
                                            <?php if($GP['purchase_orders-index']) : ?>
                                            <li id="procurment_purchase_orders_index">
                                                <a class="submenu" href="<?= admin_url('procurment/purchase_orders'); ?>">
                                                    <span class="text"> <?= lang('list'); ?></span>
                                                </a>
                                            </li>
                                            <?php endif; ?>
                                            <?php if($GP['purchase_orders-add']) : ?>
                                            <li id="procurment_purchase_orders_add">
                                                <a class="submenu" href="<?= admin_url('procurment/purchase_orders/add'); ?>">
                                                    <span class="text"> <?= lang('create'); ?></span>
                                                </a>
                                            </li>
                                            <?php endif; ?>
                                       </ul>
                                    </li>
                                    <?php endif; ?>
                                    <?php if($GP['purchase_invoices-add'] || $GP['purchase_invoices-index']) : ?>
                                    <li id="procurment_purchase_invoices">
                                        <a class="dropmenu" href="javascript:void(0)">
                                            <span class="text">  <?= lang('purchase_invoices'); ?> </span>
                                            <!--<span class="chevron closed"></span>-->
                                        </a>
                                        <ul class="level-3-menu">
                                            <?php if($GP['purchase_invoices-index']) : ?>
                                            <li id="procurment_purchase_invoices_index">
                                                <a class="submenu" href="<?= admin_url('procurment/purchase_invoices'); ?>">
                                                    <span class="text"> <?= lang('list'); ?></span>
                                                </a>
                                            </li>
                                            <?php endif; ?>
                                            <?php if($GP['purchase_invoices-add']) : ?>
                                            <li id="procurment_purchase_invoices_add">
                                                <a class="submenu" href="<?= admin_url('procurment/purchase_invoices/add'); ?>">
                                                    <span class="text"> <?= lang('create'); ?></span>
                                                </a>
                                            </li>                                          
                                            <?php endif; ?>                                     
                                       </ul>
                                    </li>
                                    <?php endif; ?>
									 <li id="procurment_grn">
                                        <a class="dropmenu" href="javascript:void(0)">
                                            <span class="text">  <?= lang('goods_received_note'); ?> </span>
                                            <!--<span class="chevron closed"></span>-->
                                        </a>
                                        <ul class="level-3-menu">
                                            <li id="procurment_grn_index">
                                                <a class="submenu" href="<?= admin_url('procurment/grn'); ?>">
                                                    <span class="text"> <?= lang('list'); ?></span>
                                                </a>
                                            </li>
                                               <script type="text/javascript">
                                                $(document).on('click','.sidebar_fadeout_pur_ret', function(){
                                                       $("#main-menu-act").trigger("click")
                                                });
                                            </script>
                                            <li id="procurment_grn_add">
                                                <a class="submenu sidebar_fadeout_pur_ret" href="<?= admin_url('procurment/grn/add'); ?>">
                                                    <span class="text"> <?= lang('create'); ?></span>
                                                </a>
                                            </li>                                          
                                                                                        
                                       </ul>
                                    </li>
                                    <?php 
                                     /*echo "<pre>";
                              print_r($GP);*/
                               if($GP['purchase_returns-add'] || $GP['purchase_returns-index'] ) : ?>

                                        <li id="procurment_purchase_returns">
                                            <a class="dropmenu" href="javascript:void(0)">
                                                <span class="text">  <?= lang('purchase_returns'); ?> </span>
                                            </a>
                                            <ul class="level-3-menu">
                                                <li id="procurment_purchase_returns_index">
                                                    <a class="submenu" href="<?= admin_url('procurment/purchase_returns'); ?>">
                                                        <span class="text"> <?= lang('list'); ?></span>
                                                    </a>
                                                </li>
                                                   <script type="text/javascript">
                                                    $(document).on('click','.sidebar_fadeout_pur_ret', function(){
                                                           $("#main-menu-act").trigger("click")
                                                    });
                                                </script>
                                                <li id="procurment_purchase_returns_add">
                                                    <a class="submenu sidebar_fadeout_pur_ret" href="<?= admin_url('procurment/purchase_returns/add'); ?>">
                                                        <span class="text"> <?= lang('create'); ?></span>
                                                    </a>
                                                </li>                                          
                                                                                            
                                           </ul>
                                        </li>
                                    <?php endif; ?>
<!--                                     <?php if($GP['purchase_returns_add'] || $GP['purchase_returns_index']) : ?>
                                    <li id="procurment_purchase_returns">
                                        <a class="dropmenu" href="javascript:void(0)">
                                            <span class="text">  <?= lang('purchase_returns'); ?> </span>
                                            
                                        </a>
                                        <ul class="level-3-menu">
                                            <?php if($GP['purchase_returns_index']) : ?>
                                            <li id="procurment_purchase_returns_index">
                                                <a class="submenu" href="<?= admin_url('procurment/purchase_returns'); ?>">
                                                    <span class="text"> <?= lang('list'); ?></span>
                                                </a>
                                            </li>
                                            <?php endif; ?>
                                            <?php if($GP['purchase_returns_add']) : ?>
                                            <li id="procurment_purchase_returns_add">
                                                <a class="submenu" href="<?= admin_url('procurment/purchase_returns/add'); ?>">
                                                    <span class="text"> <?= lang('create'); ?></span>
                                                </a>
                                            </li>
                                            <?php endif; ?>
                                                                                        
                                       </ul>
                                    </li>
                                   <?php endif; ?> -->
                                   
                                    <?php if($this->Settings->supply_chain) : ?>
                                    <?php if($GP['store_transfers_add'] || $GP['store_transfers_index']) : ?>
                                        <li id="procurment_store_transfers">
                                            <a class="dropmenu" href="javascript:void(0)">
                                                <span class="text">  <?= lang('store_transfers'); ?> </span>
                                                <!--<span class="chevron closed"></span>-->
                                            </a>
                                            <ul class="level-3-menu">
                                                <?php if($GP['store_transfers_index']) : ?>
                                                <li id="procurment_store_transfers_index">
                                                    <a class="submenu" href="<?= admin_url('procurment/store_transfers'); ?>">
                                                        <span class="text"> <?= lang('list'); ?></span>
                                                    </a>
                                                </li>
                                                <?php endif; ?>
                                                <?php if($GP['store_transfers_add']) : ?>
                                                <li id="procurment_store_transfers_add">
                                                    <a class="submenu" href="<?= admin_url('procurment/store_transfers/add'); ?>">
                                                        <span class="text"> <?= lang('create'); ?></span>
                                                    </a>
                                                </li>
                                                <?php endif; ?>
                                           </ul>
                                        </li>
                                        <?php endif; ?>
                                        <?php if($GP['store_receivers_add'] || $GP['store_receivers_index']) : ?>
                                        <li id="procurment_store_receivers">
                                            <a class="dropmenu" href="javascript:void(0)">
                                                <span class="text">  <?= lang('store_receivers'); ?> </span>
                                                <!--<span class="chevron closed"></span>-->
                                            </a>
                                            <ul class="level-3-menu">
                                                <?php if($GP['store_receivers_index']) : ?>
                                                <li id="procurment_store_receivers_index">
                                                    <a class="submenu" href="<?= admin_url('procurment/store_receivers'); ?>">
                                                        <span class="text"> <?= lang('list'); ?></span>
                                                    </a>
                                                </li>
                                                <?php endif; ?>
                                                <?php if($GP['store_receivers_add']) : ?>
                                                <li id="procurment_store_receivers_add">
                                                    <a class="submenu" href="<?= admin_url('procurment/store_receivers/add'); ?>">
                                                        <span class="text"> <?= lang('create'); ?></span>
                                                    </a>
                                                </li>
                                                <?php endif; ?>
                                           </ul>
                                        </li>
                                        <?php endif; ?>
                                        <?php if($GP['store_returns_add'] || $GP['store_returns_index']) : ?>
                                        <li id="procurment_store_returns">
                                            <a class="dropmenu" href="javascript:void(0)">
                                                <span class="text">  <?= lang('store_returns'); ?> </span>
                                                <!--<span class="chevron closed"></span>-->
                                            </a>
                                            <ul class="level-3-menu">
                                                <?php if($GP['store_returns_index']) : ?>
                                                <li id="procurment_store_returns_index">
                                                    <a class="submenu" href="<?= admin_url('procurment/store_returns'); ?>">
                                                        <span class="text"> <?= lang('list'); ?></span>
                                                    </a>
                                                </li>
                                                <?php endif; ?>
                                                <?php if($GP['store_returns_add']) : ?>
                                                <li id="procurment_store_returns_add">
                                                    <a class="submenu" href="<?= admin_url('procurment/store_returns/add'); ?>">
                                                        <span class="text"> <?= lang('create'); ?></span>
                                                    </a>
                                                </li>
                                                <?php endif; ?>
                                           </ul>
                                        </li>
                                        <?php endif; ?>
                                        <?php if($GP['store_return_receivers_add'] || $GP['store_return_receivers_index']) : ?>
                                         <li id="procurment_store_return_receivers">
                                            <a class="dropmenu" href="javascript:void(0)">
                                                <span class="text">  <?= lang('store_return_receivers'); ?> </span>
                                                <!--<span class="chevron closed"></span>-->
                                            </a>
                                            <ul class="level-3-menu">
                                                <?php if($GP['store_return_receivers_index']) : ?>
                                                <li id="procurment_store_return_receivers_index">
                                                    <a class="submenu" href="<?= admin_url('procurment/store_return_receivers'); ?>">
                                                        <span class="text"> <?= lang('list'); ?></span>
                                                    </a>
                                                </li>
                                                <?php endif; ?>
                                                <?php if($GP['store_return_receivers_add']) : ?>
                                                <li id="procurment_store_return_receivers_add">
                                                    <a class="submenu" href="<?= admin_url('procurment/store_return_receivers/add'); ?>">
                                                        <span class="text"> <?= lang('create'); ?></span>
                                                    </a>
                                                </li>
                                                <?php endif; ?>
                                           </ul>
                                        </li>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                      
                                   
                                </ul>
                            </li>
							<?php
							}
							?>

                            <?php if ($GP['quotes-index'] || $GP['quotes-add']) { ?>
                            <!--<li class="mm_quotes">
                                <a class="dropmenu" href="#">
                                    <i class="fa fa-binoculars"></i>
                                    <span class="text"> <?= lang('quotes'); ?> </span>
                                    <span class="chevron closed"></span>
                                </a>
                                <ul>
                                    <li id="sales_index">
                                        <a class="submenu" href="<?= admin_url('quotes'); ?>">
                                            <span class="text"> <?= lang('list_quotes'); ?></span>
                                        </a>
                                    </li>
                                    <?php if ($GP['quotes-add']) { ?>
                                    <li id="sales_add">
                                        <a class="submenu" href="<?= admin_url('quotes/add'); ?>">
                                           <span class="text"> <?= lang('add_quote'); ?></span>
                                        </a>
                                    </li>
                                    <?php } ?>
                                </ul>
                            </li>-->
                            <?php } ?>

                        <?php if ($GP['purchases_order-index'] || $GP['purchases_order-add']) { ?>
                            <!--<li class="mm_purchases_order">
                                <a class="dropmenu" href="#">
                                    <i class="fa fa-star"></i>
                                    <span class="text"> <?= lang('purchases'); ?>
                                    </span> <span class="chevron closed"></span>
                                </a>
                                <ul>
                                    <?php if ($GP['purchases_order-index']) : ?>
                                    <li id="purchases_order_index">
                                        <a class="submenu" href="<?= admin_url('purchases_order'); ?>">
                                            <span class="text"> <?= lang('list_purchases_order'); ?></span>
                                        </a>
                                    </li>
                                    <?php endif; ?>
                                    <?php if ($GP['purchases_order-add']) : ?>
                                    <li id="purchases_order_add">
                                        <a class="submenu" href="<?= admin_url('purchases_order/add'); ?>">
                                            <span class="text"> <?= lang('Add_purchase'); ?></span>
                                        </a>
                                    </li>
                                    <?php endif; ?>
                                </ul>
                            </li>-->
                            <?php } ?>
                            <?php if ($GP['purchases-index'] || $GP['purchases-add']) { ?>
                            <!--<li class="mm_purchases">
                                <a class="dropmenu" href="#">
                                    <i class="fa fa-star"></i>
                                    <span class="text"> <?= lang('inventory'); ?>
                                    </span> <span class="chevron closed"></span>
                                </a>
                                <ul>
                                    <?php if ($GP['purchases-index']) { ?>
                                    <li id="purchases_index">
                                        <a class="submenu" href="<?= admin_url('purchases'); ?>">
                                           <span class="text"> <?= lang('list_inventory'); ?></span>
                                        </a>
                                    </li>
                                    <?php } ?>
                                     <?php if ($GP['purchases-add']) { ?>
                                    <li id="purchases_add">
                                        <a class="submenu" href="<?= admin_url('purchases/add'); ?>"
                                            data-toggle="modal" data-target="#myModal">
                                            </i><span class="text"> <?= lang('add_inventory'); ?></span>
                                        </a>
                                    </li>
                                    <?php } ?>
                                </ul>
                            </li>-->
                            <?php } ?>
                            <?php if ($GP['material_request-index'] || $GP['material_request-add']) { ?>
                            <!--<li class="mm_material_request">
                                <a class="dropmenu" href="#">
                                    <i class="fa fa-star"></i>
                                    <span class="text"> <?= lang('material_request'); ?>
                                    </span> <span class="chevron closed"></span>
                                </a>
                                <ul>
                                    <?php if ($GP['material_request-index']) : ?>
                                    <li id="material_request_index">
                                        <a class="submenu" href="<?= admin_url('material_request'); ?>">
                                            <span class="text"> <?= lang('material_requests'); ?></span>
                                        </a>
                                    </li>
                                    <?php endif; ?>
                                    <?php if ($GP['material_request-add']) : ?>
                                    <li id="material_request_add">
                                        <a class="submenu" href="<?= admin_url('material_request/add'); ?>">
                                            <span class="text"> <?= lang('Add_material_request'); ?></span>
                                        </a>
                                    </li>
                                    <?php endif; ?>
                                </ul>
                            </li>-->
                            <?php } ?>
                            <?php if ($GP['transfers-index'] || $GP['transfers-add']) { ?>
                            <!--<li class="mm_transfers">
                                <a class="dropmenu" href="#">
                                    <i class="fa fa-star-o"></i>
                                    <span class="text"> <?= lang('transfers'); ?> </span>
                                    <span class="chevron closed"></span>
                                </a>
                                <ul>
                                    <li id="transfers_index">
                                        <a class="submenu" href="<?= admin_url('transfers'); ?>">
                                            <i class="fa fa-star-o"></i><span class="text"> <?= lang('list_transfers'); ?></span>
                                        </a>
                                    </li>
                                    <?php if ($GP['transfers-add']) { ?>
                                    <li id="transfers_add">
                                        <a class="submenu" href="<?= admin_url('transfers/add'); ?>">
                                            <i class="fa fa-plus-circle"></i><span class="text"> <?= lang('add_transfer'); ?></span>
                                        </a>
                                    </li>
                                    <?php } ?>
                                </ul>
                            </li>-->
                            <?php } ?>

 <?php if ($GP['system_settings-warehouses'] || $GP['tables-areas'] || $GP['tables-index'] || $GP['tables-kitchens']) { ?>
                            <li class="mm_restaurents <?= strtolower($this->router->fetch_method()) == 'restaurents' ? 'mm_pos' : '' ?>">
                                <a class="dropmenu" href="#">
                                    <i class="fa fa-cutlery"></i>
                                    <span class="text"> <?= lang('Restaurents'); ?>
                                    </span> <span class="chevron closed"></span>
                                </a>
                                <ul>
                                   
                                    <?php //if (POS && $GP['restaurents-areas-index']) { ?>
                                     <?php if ($GP['tables-areas']) { ?>
                                    <li id="restaurents-areas-index">
                                        <a class="submenu" href="<?= admin_url('tables/areas'); ?>">
                                           <span class="text"> <?= lang('table_areas'); ?></span>
                                        </a>
                                    </li>
                                    <?php } ?>
                                     <?php //if (TABLES && $GP['tables-index']) { ?>
                                    <?php if ($GP['tables-index']) { ?>
                                    <li id="restaurents-tables">
                                        <a class="submenu" href="<?= admin_url('tables/index'); ?>">
                                            <span class="text"> <?= lang('tables'); ?></span>
                                        </a>
                                    </li>
                                    <?php }
                                    if ($GP['tables-kitchens']) { ?>
                                    <li id="kitchens-index">
                                        <a class="submenu" href="<?= admin_url('tables/kitchens'); ?>">
                                            <span class="text"> <?= lang('kitchens'); ?></span>
                                        </a>
                                    </li>
                                    <?php }?>
                                </ul>
                            </li>
                            <?php } ?>
                            
                            <?php if ($GP['auth-users'] || $GP['auth-create_user'] || $GP['billers-index'] || $GP['billers-add'] || $GP['customers-index'] || $GP['customers-add'] || $GP['suppliers-index'] || $GP['suppliers-add']) { ?>
                            <li class="mm_auth mm_customers mm_suppliers mm_billers">
                                <a class="dropmenu" href="#">
                                    <i class="fa fa-users"></i>
                                    <span class="text"> <?= lang('people'); ?> </span>
                                    <span class="chevron closed"></span>
                                </a>
                                <ul>
                                    <?php if ($GP['auth-users']) { ?>
                                    <li id="auth-users">
                                        <a class="submenu" href="<?= admin_url('users'); ?>">
                                            <span class="text"> <?= lang('users_list'); ?></span>
                                        </a>
                                    </li>
                                    <?php } ?>
                                    <?php if ($GP['auth-create_user']) { ?>
                                    <li id="auth-create_user">
                                        <a class="submenu" href="<?= admin_url('auth/create_user'); ?>">
                                            <span class="text"> <?= lang('Add_user'); ?></span>
                                        </a>
                                    </li>
                                    <?php } ?>
                                    <?php if ($GP['billers-index']) { ?>
                                    <li id="billers-index">
                                        <a class="submenu" href="<?= admin_url('billers'); ?>">
                                            <span class="text"> <?= lang('counters_list'); ?></span>
                                        </a>
                                    </li>
                                    <?php } ?>
                                    <?php if ($GP['billers-add']) { ?>
                                    <li id="billers-add">
                                        <a class="submenu" href="<?= admin_url('billers/add'); ?>" data-toggle="modal" data-target="#myModal">
                                            <span class="text"> <?= lang('Add_counter'); ?></span>
                                        </a>
                                    </li>
                                    <?php } ?>
                                    <?php if ($GP['customers-index']) { ?>
                                    <li id="customers_index">
                                        <a class="submenu" href="<?= admin_url('customers'); ?>">
                                            <span class="text"> <?= lang('list_customers'); ?></span>
                                        </a>
                                    </li>
                                    <?php }
                                    if ($GP['customers-add']) { ?>
                                    <li id="customers_index">
                                        <a class="submenu" href="<?= admin_url('customers/add'); ?>" data-toggle="modal" data-target="#myModal">
                                            <span class="text"> <?= lang('add_customer'); ?></span>
                                        </a>
                                    </li>
                                    <?php }
                                    if ($GP['suppliers-index']) { ?>
                                    <li id="suppliers_index">
                                        <a class="submenu" href="<?= admin_url('suppliers'); ?>">
                                            <span class="text"> <?= lang('list_suppliers'); ?></span>
                                        </a>
                                    </li>
                                    <?php }
                                    if ($GP['suppliers-add']) { ?>
                                    <li id="suppliers_index">
                                        <a class="submenu" href="<?= admin_url('suppliers/add'); ?>" data-toggle="modal" data-target="#myModal">
                                            <span class="text"> <?= lang('add_supplier'); ?></span>
                                        </a>
                                    </li>
                                    <?php } ?>
                                </ul>
                            </li>
                            <?php } ?>
                            
                             <?php if ($GP['shiftmaster-index'] || $GP['shiftmaster-add']) { ?>
                            <li class="mm_shiftmaster">
                                <a class="dropmenu" href="#">
                                    <i class="fa fa-credit-card"></i>
                                    <span class="text"> <?= lang('shiftmaster'); ?> </span>
                                    <span class="chevron closed"></span>
                                </a>
                                <ul>
                                    <?php if ($GP['shiftmaster-index']) { ?> 
                                    <li id="shiftmaster_list">
                                        <a class="submenu" href="<?= admin_url('shiftmaster'); ?>">
                                            <span class="text"> <?= lang('shiftmaster'); ?></span>
                                        </a>
                                    </li>
                                    <?php } ?>
                                    <?php if ($GP['shiftmaster-add']) { ?>
                                     <li id="shiftmaster_add">
                                        <a class="submenu" href="<?= admin_url('shiftmaster/add'); ?>">
                                            <span class="text"> <?= lang('add_shiftmaster'); ?></span>
                                        </a>
                                    </li>  
                                    <?php } ?> 
                                      
                                                           
                                </ul>
                            </li>
                             <?php } ?>
                            
                             <?php if ($GP['pos-printers'] || $GP['pos-add_printer'] || $GP['system_settings-payment_methods']|| $GP['system_settings-customfeedback']|| $GP['system_settings-change_logo']|| $GP['system_settings-currencies']|| $GP['system_settings-customer_groups']|| $GP['system_settings-categories'] || $GP['system_settings-recipecategories'] || $GP['system_settings-expense_categories'] || $GP['system_settings-units'] || $GP['system_settings-brands'] || $GP['system_settings-sales_type'] || $GP['system_settings-tax_rates'] || $GP['system_settings-discounts'] || $GP['system_settings-customer_discounts'] || $GP['system_settings-buy_get'] || $GP['system_settings-email_templates'] || $GP['system_settings-backups'] || $GP['system_settings-group_permissions'] || $GP['system_settings-bbq_menu']) { ?>
<li class="mm_system_settings <?= strtolower($this->router->fetch_method()) == 'sales' ? '' : 'mm_pos' ?>">
                                    <a class="dropmenu" href="#">
                                        <i class="fa fa-cog"></i>
                                        <span class="text"> <?= lang('settings'); ?> </span>
                                        <span class="chevron closed"></span>
                                    </a>
                                    <ul>
                                       <?php if ($GP['pos-printers']) { ?>
                                        <li id="pos_printers">
                                            <a href="<?= admin_url('pos/printers') ?>">
                                                <span class="text"> <?= lang('list_printers'); ?></span>
                                            </a>
                                        </li>
                                        <?php } if ($GP['pos-add_printer']) { ?>
                                        <li id="pos_add_printer">
                                            <a href="<?= admin_url('pos/add_printer') ?>">
                                                <span class="text"> <?= lang('add_printer'); ?></span>
                                            </a>
                                        </li>
                                        <?php } ?>
                                        
                                        <li id="system_settings_tills">
                                            <a href="<?= admin_url('system_settings/tills') ?>">
                                                <span class="text"> <?= lang('tills'); ?></span>
                                            </a>
                                        </li>
                                       
                                        <?php if ($GP['system_settings-payment_methods']) { ?>
                                        <li id="system_settings_paymentmethods">
                                            <a href="<?= admin_url('system_settings/payment_methods') ?>">
                                                <span class="text"> <?= lang('Payment Methods'); ?></span>
                                            </a>
                                        </li>
                                        
                                        <?php }                                        
                                        if ($GP['system_settings-bbq_menu']) { ?>
                                        <li id="system_settings_bbq_menu">
                                            <a href="<?= admin_url('system_settings/bbq_menu') ?>">
                                                <span class="text"> <?= lang('bbq_menu'); ?></span>
                                            </a>
                                        </li>
                                        
                                        <?php }
                                         if ($GP['system_settings-customfeedback']) { ?>
                                        <li id="system_settings_customfeedback">
                                            <a href="<?= admin_url('system_settings/customfeedback') ?>">
                                                <span class="text"> <?= lang('custom_feedback'); ?></span>
                                            </a>
                                        </li>
                                        <?php } if ($GP['system_settings-change_logo']) { ?>
                                        <li id="system_settings_change_logo">
                                            <a href="<?= admin_url('system_settings/change_logo') ?>" data-toggle="modal" data-target="#myModal">
                                                <span class="text"> <?= lang('change_logo'); ?></span>
                                            </a>
                                        </li>
                                        <?php } if ($GP['system_settings-currencies']) { ?>
                                        <li id="system_settings_currencies">
                                            <a href="<?= admin_url('system_settings/currencies') ?>">
                                                <span class="text"> <?= lang('currencies'); ?></span>
                                            </a>
                                        </li>
                                        <?php } if ($GP['system_settings-customer_groups']) { ?>
                                        <li id="system_settings_customer_groups">
                                            <a href="<?= admin_url('system_settings/customer_groups') ?>">
                                                <span class="text"> <?= lang('customer_groups'); ?></span>
                                            </a>
                                        </li>
                                        <?php } if ($GP['system_settings-categories']) { ?>
                                        <li id="system_settings_categories">
                                            <a href="<?= admin_url('system_settings/categories') ?>">
                                                <span class="text"> <?= lang('categories'); ?></span>
                                            </a>
                                        </li>
                                        <?php } if ($GP['system_settings-recipecategories']) { ?>
                                        <li id="system_settings_recipe_categories">
                                            <a href="<?= admin_url('system_settings/recipecategories') ?>">
                                                <span class="text"> <?= lang('recipe_groups'); ?></span>
                                            </a>
                                        </li>
                                        <?php } if ($GP['system_settings-expense_categories']) { ?>
                                        <li id="system_settings_expense_categories">
                                            <a href="<?= admin_url('system_settings/expense_categories') ?>">
                                                <span class="text"> <?= lang('expense_categories'); ?></span>
                                            </a>
                                        </li>
                                        <?php } if ($GP['system_settings-units']) { ?>
                                        <li id="system_settings_units">
                                            <a href="<?= admin_url('system_settings/units') ?>">
                                                <span class="text"> <?= lang('units'); ?></span>
                                            </a>
                                        </li>
                                        <?php } if ($GP['system_settings-brands']) { ?>
                                        <li id="system_settings_brands">
                                            <a href="<?= admin_url('system_settings/brands') ?>">
                                                <span class="text"> <?= lang('brands'); ?></span>
                                            </a>
                                        </li>
                                        <?php } if ($GP['system_settings-group_permissions']) { ?>
                                        <li id="system_settings_user_groups">
                                            <a href="<?= admin_url('system_settings/user_groups') ?>">
                                                <span class="text"> <?= lang('group_permissions'); ?></span>
                                            </a>
                                        </li>
                                        <?php } if ($GP['system_settings-sales_type']) { ?>
                                         <li id="system_settings_sales_type">
                                            <a href="<?= admin_url('system_settings/sales_type') ?>">
                                                <span class="text"> <?= lang('sales_type'); ?></span>
                                            </a>
                                        </li>
                                        <?php } if ($GP['system_settings-tax_rates']) { ?>
                                        <li id="system_settings_tax_rates">
                                            <a href="<?= admin_url('system_settings/tax_rates') ?>">
                                                <span class="text"> <?= lang('tax_rates'); ?></span>
                                            </a>
                                        </li>
                                        <?php } if ($GP['system_settings-discounts']) { ?>
                                        <li id="system_settings_tax_rates">
                                            <a href="<?= admin_url('system_settings/discounts') ?>">
                                                <span class="text"> <?= lang('discounts'); ?></span>
                                            </a>
                                        </li>
                                        <?php } if ($GP['system_settings-customer_discounts']) { ?>
                                        <li id="customer_discounts">
                                            <a href="<?= admin_url('system_settings/customer_discounts') ?>">
                                                <span class="text"> <?= lang('customer_discounts'); ?></span>
                                            </a>
                                        </li>
                                        <?php } if ($GP['system_settings-buy_get']) { ?>
                                        <li id="system_settings_tax_rates">
                                            <a href="<?= admin_url('system_settings/buy_get') ?>">
                                                <span class="text"> <?= lang('buy_get'); ?></span>
                                            </a>
                                        </li>
                                        <?php } if ($GP['system_settings-email_templates']) { ?>
                                        <li id="system_settings_email_templates">
                                            <a href="<?= admin_url('system_settings/email_templates') ?>">
                                                <span class="text"> <?= lang('email_templates'); ?></span>
                                            </a>
                                        </li>
                                        <?php } if ($GP['system_settings-backups']) { ?>
                                        <li id="system_settings_backups">
                                            <a href="<?= admin_url('system_settings/backups') ?>">
                                                <span class="text"> <?= lang('backups'); ?></span>
                                            </a>
                                        </li>
                                        <?php }?>
                                    </ul>
                                    </li>
                            <?php } ?> 
                            <?php
                            $reports_settings = array_intersect_key( $GP, array_flip( preg_grep( '/^reports/i', array_keys( $GP ) ) ) );
                            $reports_access = preg_grep( '/^1/i',$reports_settings);
                            //print_R($reports_access);exit;
                            if(!empty($reports_access)) : ?>
                                <li class="mm_reports">
                                   <a class="dropmenu" href="#">
                                       <i class="fa fa-bar-chart-o"></i>
                                       <span class="text"> <?= lang('reports'); ?> </span>
                                       <span class="chevron closed"></span>
                                   </a>
                                   <ul>
                                      <?php foreach($reports_access as $k => $val) :  ?>
                                      <?php
                                          $split = explode('-',$k);
                                          $id = str_replace('-','_',$k);
                                          $action = str_replace('-','/',$k);
                                          $lang = $split[1];
                                      ?>
                                          <li id="<?=$id?>">
                                              <a href="<?= admin_url($action) ?>">
                                                  <span class="text"> <?= lang($lang); ?></span>
                                              </a>
                                          </li>
                                      <?php endforeach; ?>   
                                   </ul>
                                </li>
                            <?php endif; ?>

                        <?php } ?>
                    </ul>
                </div>
                <a href="#" id="main-menu-act" class="minified visible-md visible-lg">
                    <i class="fa fa-angle-double-left"></i>
                </a>
            </div>
            
            </td>
            <?php endif; ?>
        <td class="content-con">
            <div id="content">
             <?php if(!isset($isMobileApp)) : ?>
                <div class="row">
                    <div class="col-sm-12 col-md-12">
                        <ul class="breadcrumb">
                            <?php
                            foreach ($bc as $b) {
                                if ($b['link'] === '#') {
                                    echo '<li class="active">' . $b['page'] . '</li>';
                                } else {
                                    echo '<li><a href="' . $b['link'] . '">' . $b['page'] . '</a></li>';
                                }
                            }
                            ?>
                            <!--<li class="right_log hidden-xs">
                               <?php echo lang('enter_info'); ?>
                            </li>-->
                        </ul>
                        
                    </div>
                </div>
                <?php endif; ?>
                <div class="row">
                    <div class="col-lg-12">
                        <?php if ($message) { ?>
                            <div class="alert alert-success">
                                <button data-dismiss="alert" class="close" type="button">x</button>
                                <?= $message; ?>
                            </div>
                        <?php } ?>
                        <?php if ($error) { ?>
                            <div class="alert alert-danger">
                                <button data-dismiss="alert" class="close" type="button">x</button>
                                <?= $error; ?>
                            </div>
                        <?php } ?>
                        <?php if ($warning) { ?>
                            <div class="alert alert-warning">
                                <button data-dismiss="alert" class="close" type="button">x</button>
                                <?= $warning; ?>
                            </div>
                        <?php } ?>
                        <?php
                        if (@$info) {
                            foreach ($info as $n) {
                                if (!$this->session->userdata('hidden' . $n->id)) {
                                    ?>
                                    <div class="alert alert-info">
                                        <a href="#" id="<?= $n->id ?>" class="close hideComment external"
                                           data-dismiss="alert">&times;</a>
                                        <?= $n->comment; ?>
                                    </div>
                                <?php }
                            }
                        } ?>
                        <div class="alerts-con"></div>
<style>
 .main-menu .mm_reports a.submenu{
    height: 20px !important;
    background-color: transparent !important;
    color: #696969 !important;
    padding-top: 3px !important;
    padding-left: 16px !important;
    border-bottom: none !important;
}
.main-menu .mm_reports a.submenu span{
    white-space: normal;
   
    display: inline-block;
    width: 190px;
}
.mm_reports ul li ul li{
    background : none !important;
}
.main-menu .mm_procurment a.submenu{
    height: 20px !important;
    background-color: transparent !important;
    color: #696969 ;
    padding-top: 3px !important;
    padding-left: 16px !important;
    border-bottom: none !important;
}
.main-menu .mm_procurment a.submenu span{
    white-space: normal;
   
    display: inline-block;
    width: 190px;
}
.mm_procurment ul li ul li{
    background : none !important;
}

.main-menu .mm_recipe_management a.submenu{
    height: 20px !important;
    background-color: transparent !important;
    color: #696969 ;
    padding-top: 3px !important;
    padding-left: 16px !important;
    border-bottom: none !important;
}
.main-menu .mm_recipe_management a.submenu span{
    white-space: normal;
   
    display: inline-block;
    width: 190px;
}
.mm_recipe_management ul li ul li{
    background : none !important;
}
</style>

