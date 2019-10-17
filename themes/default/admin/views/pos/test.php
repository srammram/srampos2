
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>POS Module | Stock Manager Advance</title>
<script src="/cdn-cgi/apps/head/Bx0hUCX-YaUCcleOh3fM_NqlPrk.js"></script><script type="67d37507c7a2d57f19750a70-text/javascript">if(parent.frames.length !== 0){top.location = 'https://sma.tecdiary.com/admin/pos';}</script>
<base href="https://sma.tecdiary.com/" />
<meta http-equiv="cache-control" content="max-age=0" />
<meta http-equiv="cache-control" content="no-cache" />
<meta http-equiv="expires" content="0" />
<meta http-equiv="pragma" content="no-cache" />
<link rel="shortcut icon" href="https://sma.tecdiary.com/themes/default/admin/assets/images/icon.png" />
<link rel="stylesheet" href="https://sma.tecdiary.com/themes/default/admin/assets/styles/theme.css" type="text/css" />
<link rel="stylesheet" href="https://sma.tecdiary.com/themes/default/admin/assets/styles/style.css" type="text/css" />
<link rel="stylesheet" href="https://sma.tecdiary.com/themes/default/admin/assets/pos/css/posajax.css" type="text/css" />
<link rel="stylesheet" href="https://sma.tecdiary.com/themes/default/admin/assets/pos/css/print.css" type="text/css" media="print" />
<script type="67d37507c7a2d57f19750a70-text/javascript" src="https://sma.tecdiary.com/themes/default/admin/assets/js/jquery-2.0.3.min.js"></script>
<script type="67d37507c7a2d57f19750a70-text/javascript" src="https://sma.tecdiary.com/themes/default/admin/assets/js/jquery-migrate-1.2.1.min.js"></script>
<!--[if lt IE 9]>
    <script src="https://sma.tecdiary.com/themes/default/admin/assets/js/jquery.js"></script>
    <![endif]-->
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
<div id="wrapper">
<header id="header" class="navbar">
<div class="container">
<a class="navbar-brand" href="https://sma.tecdiary.com/admin/"><span class="logo"><span class="pos-logo-lg">Stock Manager Advance</span><span class="pos-logo-sm">POS</span></span></a>
<div class="header-nav">
<ul class="nav navbar-nav pull-right">
<li class="dropdown">
<a class="btn account dropdown-toggle" data-toggle="dropdown" href="#">
<img alt="" src="https://sma.tecdiary.com/themes/default/admin/assets/images/male.png" class="mini_avatar img-rounded">
<div class="user">
<span>Welcome! owner</span>
</div>
</a>
<ul class="dropdown-menu pull-right">
<li>
<a href="https://sma.tecdiary.com/admin/auth/profile/1">
<i class="fa fa-user"></i> Profile </a>
</li>
<li>
<a href="https://sma.tecdiary.com/admin/auth/profile/1/#cpassword">
<i class="fa fa-key"></i> Change Password </a>
</li>
<li class="divider"></li>
<li>
<a href="https://sma.tecdiary.com/admin/auth/logout">
<i class="fa fa-sign-out"></i> Logout </a>
</li>
</ul>
</li>
</ul>
<ul class="nav navbar-nav pull-right">
<li class="dropdown">
<a class="btn bblue pos-tip" title="Dashboard" data-placement="bottom" href="https://sma.tecdiary.com/admin/welcome">
<i class="fa fa-dashboard"></i>
</a>
</li>
<li class="dropdown hidden-sm">
<a class="btn pos-tip" title="Settings" data-placement="bottom" href="https://sma.tecdiary.com/admin/pos/settings">
<i class="fa fa-cogs"></i>
</a>
</li>
<li class="dropdown hidden-xs">
<a class="btn pos-tip" title="Calculator" data-placement="bottom" href="#" data-toggle="dropdown">
<i class="fa fa-calculator"></i>
</a>
<ul class="dropdown-menu pull-right calc">
<li class="dropdown-content">
<span id="inlineCalc"></span>
</li>
</ul>
</li>
<li class="dropdown hidden-sm">
<a class="btn pos-tip" title="Shortcuts" data-placement="bottom" href="#" data-toggle="modal" data-target="#sckModal">
<i class="fa fa-key"></i>
</a>
</li>
<li class="dropdown">
<a class="btn pos-tip" title="View Bill Screen" data-placement="bottom" href="https://sma.tecdiary.com/admin/pos/view_bill" target="_blank">
<i class="fa fa-laptop"></i>
</a>
</li>
<li class="dropdown">
<a class="btn blightOrange pos-tip" id="opened_bills" title="<span>Suspended Sales</span>" data-placement="bottom" data-html="true" href="https://sma.tecdiary.com/admin/pos/opened_bills" data-toggle="ajax">
<i class="fa fa-th"></i>
</a>
</li>
<li class="dropdown">
<a class="btn bdarkGreen pos-tip" id="register_details" title="<span>Register Details</span>" data-placement="bottom" data-html="true" href="https://sma.tecdiary.com/admin/pos/register_details" data-toggle="modal" data-target="#myModal">
<i class="fa fa-check-circle"></i>
</a>
</li>
<li class="dropdown">
<a class="btn borange pos-tip" id="close_register" title="<span>Close Register</span>" data-placement="bottom" data-html="true" data-backdrop="static" href="https://sma.tecdiary.com/admin/pos/close_register" data-toggle="modal" data-target="#myModal">
<i class="fa fa-times-circle"></i>
</a>
</li>
<li class="dropdown">
<a class="btn borange pos-tip" id="add_expense" title="<span>Add Expense</span>" data-placement="bottom" data-html="true" href="https://sma.tecdiary.com/admin/purchases/add_expense" data-toggle="modal" data-target="#myModal">
<i class="fa fa-dollar"></i>
</a>
</li>
<li class="dropdown">
<a class="btn bdarkGreen pos-tip" id="today_profit" title="<span>Today's Profit</span>" data-placement="bottom" data-html="true" href="https://sma.tecdiary.com/admin/reports/profit" data-toggle="modal" data-target="#myModal">
<i class="fa fa-hourglass-half"></i>
</a>
</li>
<li class="dropdown">
<a class="btn bdarkGreen pos-tip" id="today_sale" title="<span>Today's Sale</span>" data-placement="bottom" data-html="true" href="https://sma.tecdiary.com/admin/pos/today_sale" data-toggle="modal" data-target="#myModal">
<i class="fa fa-heart"></i>
</a>
</li>
<li class="dropdown hidden-xs">
 <a class="btn bblue pos-tip" title="List Open Registers" data-placement="bottom" href="https://sma.tecdiary.com/admin/pos/registers">
<i class="fa fa-list"></i>
</a>
</li>
<li class="dropdown hidden-xs">
<a class="btn bred pos-tip" title="Clear all locally saved data" data-placement="bottom" id="clearLS" href="#">
<i class="fa fa-eraser"></i>
</a>
</li>
</ul>
<ul class="nav navbar-nav pull-right">
<li class="dropdown">
<a class="btn bblack" style="cursor: default;"><span id="display_time"></span></a>
</li>
</ul>
</div>
</div>
</header>
<div id="content">
<div class="c1">
<div class="pos">
<div id="pos">
<form action="https://sma.tecdiary.com/admin/pos" data-toggle="validator" role="form" id="pos-sale-form" method="post" accept-charset="utf-8">
<input type="hidden" name="token" value="aab38df655bb7060059891bde4f3d812" />
<div id="leftdiv">
<div id="printhead">
<h4 style="text-transform:uppercase;">Stock Manager Advance</h4>
<h5 style="text-transform:uppercase;">Order List</h5>Date 20/11/2018 20:41 </div>
<div id="left-top">
<div style="position: absolute; left:-9999px;"><input type="text" name="test" value="" id="test" class="kb-pad" />
</div>
<div class="form-group">
<div class="input-group">
<input type="text" name="customer" value="" id="poscustomer" data-placeholder="Select Customer" required="required" class="form-control pos-input-tip" style="width:100%;" />
<div class="input-group-addon no-print" style="padding: 2px 8px; border-left: 0;">
<a href="#" id="toogle-customer-read-attr" class="external">
<i class="fa fa-pencil" id="addIcon" style="font-size: 1.2em;"></i>
</a>
</div>
<div class="input-group-addon no-print" style="padding: 2px 7px; border-left: 0;">
<a href="#" id="view-customer" class="external" data-toggle="modal" data-target="#myModal">
<i class="fa fa-eye" id="addIcon" style="font-size: 1.2em;"></i>
</a>
</div>
<div class="input-group-addon no-print" style="padding: 2px 8px;">
<a href="https://sma.tecdiary.com/admin/customers/add" id="add-customer" class="external" data-toggle="modal" data-target="#myModal">
<i class="fa fa-plus-circle" id="addIcon" style="font-size: 1.5em;"></i>
</a>
</div>
</div>
<div style="clear:both;"></div>
</div>
<div class="no-print">
<div class="form-group">
<select name="warehouse" id="poswarehouse" class="form-control pos-input-tip" data-placeholder="Select Warehouse" required="required" style="width:100%;">
<option value=""></option>
<option value="1" selected="selected">Warehouse 1</option>
<option value="2">Warehouse 2</option>
</select>
</div>
<div class="form-group" id="ui">
<div class="input-group">
<input type="text" name="add_item" value="" class="form-control pos-tip" id="add_item" data-placement="top" data-trigger="focus" placeholder="Scan/Search product by name/code" title="Please start typing code/name for suggestions or just scan barcode" />
<div class="input-group-addon" style="padding: 2px 8px;">
<a href="#" id="addManually">
<i class="fa fa-plus-circle" id="addIcon" style="font-size: 1.5em;"></i>
</a>
</div>
</div>
<div style="clear:both;"></div>
</div>
</div>
</div>
<div id="print">
<div id="left-middle">
<div id="product-list">
<table class="table items table-striped table-bordered table-condensed table-hover sortable_table" id="posTable" style="margin-bottom: 0;">
<thead>
<tr>
<th width="40%">Product</th>
<th width="15%">Price</th>
<th width="15%">Qty</th>
<th width="20%">Subtotal</th>
<th style="width: 5%; text-align: center;">
<i class="fa fa-trash-o" style="opacity:0.5; filter:alpha(opacity=50);"></i>
</th>
</tr>
</thead>
<tbody>
</tbody>
</table>
<div style="clear:both;"></div>
</div>
</div>
<div style="clear:both;"></div>
<div id="left-bottom">
<table id="totalTable" style="width:100%; float:right; padding:5px; color:#000; background: #FFF;">
<tr>
<td style="padding: 5px 10px;border-top: 1px solid #DDD;">Items</td>
<td class="text-right" style="padding: 5px 10px;font-size: 14px; font-weight:bold;border-top: 1px solid #DDD;">
<span id="titems">0</span>
</td>
<td style="padding: 5px 10px;border-top: 1px solid #DDD;">Total</td>
<td class="text-right" style="padding: 5px 10px;font-size: 14px; font-weight:bold;border-top: 1px solid #DDD;">
<span id="total">0.00</span>
</td>
</tr>
<tr>
<td style="padding: 5px 10px;">Order Tax <a href="#" id="pptax2">
<i class="fa fa-edit"></i>
</a>
</td>
<td class="text-right" style="padding: 5px 10px;font-size: 14px; font-weight:bold;">
<span id="ttax2">0.00</span>
</td>
<td style="padding: 5px 10px;">Discount <a href="#" id="ppdiscount">
<i class="fa fa-edit"></i>
</a>
</td>
<td class="text-right" style="padding: 5px 10px;font-weight:bold;">
<span id="tds">0.00</span>
</td>
</tr>
<tr>
<td style="padding: 5px 10px; border-top: 1px solid #666; border-bottom: 1px solid #333; font-weight:bold; background:#333; color:#FFF;" colspan="2">
Total Payable <a href="#" id="pshipping">
<i class="fa fa-plus-square"></i>
</a>
<span id="tship"></span>
</td>
<td class="text-right" style="padding:5px 10px 5px 10px; font-size: 14px;border-top: 1px solid #666; border-bottom: 1px solid #333; font-weight:bold; background:#333; color:#FFF;" colspan="2">
<span id="gtotal">0.00</span>
</td>
</tr>
</table>
<div class="clearfix"></div>
<div id="botbuttons" class="col-xs-12 text-center">
<input type="hidden" name="biller" id="biller" value="3" />
<div class="row">
<div class="col-xs-4" style="padding: 0;">
<div class="btn-group-vertical btn-block">
<button type="button" class="btn btn-warning btn-block btn-flat" id="suspend">
Suspend </button>
<button type="button" class="btn btn-danger btn-block btn-flat" id="reset">
Cancel </button>
</div>
</div>
<div class="col-xs-4" style="padding: 0;">
<div class="btn-group-vertical btn-block">
<button type="button" class="btn btn-info btn-block" id="print_order">
Order </button>
<button type="button" class="btn btn-primary btn-block" id="print_bill">
Bill </button>
</div>
</div>
<div class="col-xs-4" style="padding: 0;">
<button type="button" class="btn btn-success btn-block" id="payment" style="height:67px;">
<i class="fa fa-money" style="margin-right: 5px;"></i>Payment </button>
</div>
</div>
</div>
<div style="clear:both; height:5px;"></div>
<div id="num">
<div id="icon"></div>
</div>
<span id="hidesuspend"></span>
<input type="hidden" name="pos_note" value="" id="pos_note">
<input type="hidden" name="staff_note" value="" id="staff_note">
<div id="payment-con">
<input type="hidden" name="amount[]" id="amount_val_1" value="" />
<input type="hidden" name="balance_amount[]" id="balance_amount_1" value="" />
<input type="hidden" name="paid_by[]" id="paid_by_val_1" value="cash" />
<input type="hidden" name="cc_no[]" id="cc_no_val_1" value="" />
<input type="hidden" name="paying_gift_card_no[]" id="paying_gift_card_no_val_1" value="" />
<input type="hidden" name="cc_holder[]" id="cc_holder_val_1" value="" />
<input type="hidden" name="cheque_no[]" id="cheque_no_val_1" value="" />
<input type="hidden" name="cc_month[]" id="cc_month_val_1" value="" />
<input type="hidden" name="cc_year[]" id="cc_year_val_1" value="" />
<input type="hidden" name="cc_type[]" id="cc_type_val_1" value="" />
<input type="hidden" name="cc_cvv2[]" id="cc_cvv2_val_1" value="" />
<input type="hidden" name="payment_note[]" id="payment_note_val_1" value="" />
<input type="hidden" name="amount[]" id="amount_val_2" value="" />
<input type="hidden" name="balance_amount[]" id="balance_amount_2" value="" />
<input type="hidden" name="paid_by[]" id="paid_by_val_2" value="cash" />
<input type="hidden" name="cc_no[]" id="cc_no_val_2" value="" />
<input type="hidden" name="paying_gift_card_no[]" id="paying_gift_card_no_val_2" value="" />
<input type="hidden" name="cc_holder[]" id="cc_holder_val_2" value="" />
<input type="hidden" name="cheque_no[]" id="cheque_no_val_2" value="" />
<input type="hidden" name="cc_month[]" id="cc_month_val_2" value="" />
<input type="hidden" name="cc_year[]" id="cc_year_val_2" value="" />
<input type="hidden" name="cc_type[]" id="cc_type_val_2" value="" />
<input type="hidden" name="cc_cvv2[]" id="cc_cvv2_val_2" value="" />
<input type="hidden" name="payment_note[]" id="payment_note_val_2" value="" />
<input type="hidden" name="amount[]" id="amount_val_3" value="" />
<input type="hidden" name="balance_amount[]" id="balance_amount_3" value="" />
<input type="hidden" name="paid_by[]" id="paid_by_val_3" value="cash" />
<input type="hidden" name="cc_no[]" id="cc_no_val_3" value="" />
<input type="hidden" name="paying_gift_card_no[]" id="paying_gift_card_no_val_3" value="" />
<input type="hidden" name="cc_holder[]" id="cc_holder_val_3" value="" />
<input type="hidden" name="cheque_no[]" id="cheque_no_val_3" value="" />
<input type="hidden" name="cc_month[]" id="cc_month_val_3" value="" />
<input type="hidden" name="cc_year[]" id="cc_year_val_3" value="" />
<input type="hidden" name="cc_type[]" id="cc_type_val_3" value="" />
<input type="hidden" name="cc_cvv2[]" id="cc_cvv2_val_3" value="" />
<input type="hidden" name="payment_note[]" id="payment_note_val_3" value="" />
<input type="hidden" name="amount[]" id="amount_val_4" value="" />
<input type="hidden" name="balance_amount[]" id="balance_amount_4" value="" />
<input type="hidden" name="paid_by[]" id="paid_by_val_4" value="cash" />
<input type="hidden" name="cc_no[]" id="cc_no_val_4" value="" />
<input type="hidden" name="paying_gift_card_no[]" id="paying_gift_card_no_val_4" value="" />
<input type="hidden" name="cc_holder[]" id="cc_holder_val_4" value="" />
<input type="hidden" name="cheque_no[]" id="cheque_no_val_4" value="" />
<input type="hidden" name="cc_month[]" id="cc_month_val_4" value="" />
<input type="hidden" name="cc_year[]" id="cc_year_val_4" value="" />
<input type="hidden" name="cc_type[]" id="cc_type_val_4" value="" />
<input type="hidden" name="cc_cvv2[]" id="cc_cvv2_val_4" value="" />
<input type="hidden" name="payment_note[]" id="payment_note_val_4" value="" />
<input type="hidden" name="amount[]" id="amount_val_5" value="" />
<input type="hidden" name="balance_amount[]" id="balance_amount_5" value="" />
<input type="hidden" name="paid_by[]" id="paid_by_val_5" value="cash" />
<input type="hidden" name="cc_no[]" id="cc_no_val_5" value="" />
<input type="hidden" name="paying_gift_card_no[]" id="paying_gift_card_no_val_5" value="" />
<input type="hidden" name="cc_holder[]" id="cc_holder_val_5" value="" />
<input type="hidden" name="cheque_no[]" id="cheque_no_val_5" value="" />
<input type="hidden" name="cc_month[]" id="cc_month_val_5" value="" />
<input type="hidden" name="cc_year[]" id="cc_year_val_5" value="" />
<input type="hidden" name="cc_type[]" id="cc_type_val_5" value="" />
<input type="hidden" name="cc_cvv2[]" id="cc_cvv2_val_5" value="" />
<input type="hidden" name="payment_note[]" id="payment_note_val_5" value="" />
</div>
<input name="order_tax" type="hidden" value="1" id="postax2">
<input name="discount" type="hidden" value="" id="posdiscount">
 <input name="shipping" type="hidden" value="0" id="posshipping">
<input type="hidden" name="rpaidby" id="rpaidby" value="cash" style="display: none;" />
<input type="hidden" name="total_items" id="total_items" value="0" style="display: none;" />
<input type="submit" id="submit_sale" value="Submit Sale" style="display: none;" />
</div>
</div>
</div>
</form> <div id="cp">
<div id="cpinner">
<div class="quick-menu">
<div id="proContainer">
<div id="ajaxproducts">
<div id="item-list">
<div><button id="product-0108" type="button" value='IT01' title="Canon 1100d" class="btn-prni btn-default product pos-tip" data-container="body"><img src="https://sma.tecdiary.com/assets/uploads/thumbs/77712cc7a2ad7f32dfab19bab0160303.png" alt="Canon 1100d" class='img-rounded' /><span>Canon 1100d</span></button><button id="product-0109" type="button" value='IT02' title="Computer Set 1" class="btn-prni btn-default product pos-tip" data-container="body"><img src="https://sma.tecdiary.com/assets/uploads/thumbs/85c11f17a9a065ca27388b6c0e437b35.png" alt="Computer Set 1" class='img-rounded' /><span>Computer Set 1</span></button><button id="product-0110" type="button" value='IT03' title="Computer Set 2" class="btn-prni btn-default product pos-tip" data-container="body"><img src="https://sma.tecdiary.com/assets/uploads/thumbs/c956a14ddaaa35f68df354c7c6b182dd.png" alt="Computer Set 2" class='img-rounded' /><span>Computer Set 2</span></button><button id="product-0111" type="button" value='IT04' title="Hard Disk" class="btn-prni btn-default product pos-tip" data-container="body"><img src="https://sma.tecdiary.com/assets/uploads/thumbs/a8867c6d3770f724b2f95e042d4afaff.png" alt="Hard Disk" class='img-rounded' /><span>Hard Disk</span></button><button id="product-0112" type="button" value='IT05' title="Keyboard" class="btn-prni btn-default product pos-tip" data-container="body"><img src="https://sma.tecdiary.com/assets/uploads/thumbs/c58dff3817e2b1a63f94f8d11c13eaf1.png" alt="Keyboard" class='img-rounded' /><span>Keyboard</span></button><button id="product-0114" type="button" value='IT07' title="Laptop" class="btn-prni btn-default product pos-tip" data-container="body"><img src="https://sma.tecdiary.com/assets/uploads/thumbs/160f38cbac757e0e8b196d2c9e44781b.png" alt="Laptop" class='img-rounded' /><span>Laptop</span></button><button id="product-0113" type="button" value='IT06' title="Mouse" class="btn-prni btn-default product pos-tip" data-container="body"><img src="https://sma.tecdiary.com/assets/uploads/thumbs/b788176a4110f54b772860f833317c5d.png" alt="Mouse" class='img-rounded' /><span>Mouse</span></button><button id="product-0115" type="button" value='IT08' title="RAM" class="btn-prni btn-default product pos-tip" data-container="body"><img src="https://sma.tecdiary.com/assets/uploads/thumbs/22cc732278de0559e502af8180bf6502.png" alt="RAM" class='img-rounded' /><span>RAM</span></button></div> </div>
<div class="btn-group btn-group-justified pos-grid-nav">
<div class="btn-group">
<button style="z-index:10002;" class="btn btn-primary pos-tip" title="Previous" type="button" id="previous">
<i class="fa fa-chevron-left"></i>
</button>
</div>
<div class="btn-group">
<button style="z-index:10003;" class="btn btn-primary pos-tip" type="button" id="sellGiftCard" title="Sell Gift Card">
<i class="fa fa-credit-card" id="addIcon"></i> Sell Gift Card </button>
</div>
<div class="btn-group">
<button style="z-index:10004;" class="btn btn-primary pos-tip" title="Next" type="button" id="next">
<i class="fa fa-chevron-right"></i>
</button>
</div>
</div>
</div>
<div style="clear:both;"></div>
</div>
</div>
</div>
<div style="clear:both;"></div>
</div>
<div style="clear:both;"></div>
</div>
<div style="clear:both;"></div>
</div>
</div>
</div>
</div>
<div class="rotate btn-cat-con">
<button type="button" id="open-brands" class="btn btn-info open-brands">Brands</button>
<button type="button" id="open-subcategory" class="btn btn-warning open-subcategory">Sub Categories</button>
<button type="button" id="open-category" class="btn btn-primary open-category">Categories</button>
</div>
<div id="brands-slider">
<div id="brands-list">
<button id="brand-1" type="button" value='1' class="btn-prni brand"><img src="assets/uploads/thumbs/no_image.png" class='img-rounded img-thumbnail' /><span>Timberland</span></button><button id="brand-2" type="button" value='2' class="btn-prni brand"><img src="assets/uploads/thumbs/no_image.png" class='img-rounded img-thumbnail' /><span>Guess</span></button><button id="brand-3" type="button" value='3' class="btn-prni brand"><img src="assets/uploads/thumbs/no_image.png" class='img-rounded img-thumbnail' /><span>Hush Puppies</span></button> </div>
</div>
<div id="category-slider">

<div id="category-list">
<button id="category-1" type="button" value='1' class="btn-prni category"><img src="assets/uploads/thumbs/no_image.png" class='img-rounded img-thumbnail' /><span>Computers</span></button><button id="category-2" type="button" value='2' class="btn-prni category"><img src="assets/uploads/thumbs/no_image.png" class='img-rounded img-thumbnail' /><span>Fruits</span></button><button id="category-3" type="button" value='3' class="btn-prni category"><img src="assets/uploads/thumbs/no_image.png" class='img-rounded img-thumbnail' /><span>Toys</span></button> </div>
</div>
<div id="subcategory-slider">

<div id="subcategory-list">
</div>
</div>
<div class="modal fade in" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="payModalLabel" aria-hidden="true">
<div class="modal-dialog modal-lg">
<div class="modal-content">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true"><i class="fa fa-2x">&times;</i></span><span class="sr-only">Close</span></button>
<h4 class="modal-title" id="payModalLabel">Finalize Sale</h4>
</div>
<div class="modal-body" id="payment_content">
<div class="row">
<div class="col-md-10 col-sm-9">
<div class="form-group">
<label for="biller">Biller</label> <select name="biller" class="form-control" id="posbiller" required="required">
<option value="3" selected="selected">Test Biller</option>
</select>
</div>
<div class="form-group">
<div class="row">
<div class="col-sm-6">
<textarea name="sale_note" cols="40" rows="10" id="sale_note" class="form-control kb-text skip" style="height: 100px;" placeholder="Sale Note" maxlength="250"></textarea>
</div>
<div class="col-sm-6">
<textarea name="staffnote" cols="40" rows="10" id="staffnote" class="form-control kb-text skip" style="height: 100px;" placeholder="Staff Note" maxlength="250"></textarea>
</div>
</div>
</div>
<div class="clearfir"></div>
<div id="payments">
<div class="well well-sm well_1">
<div class="payment">
<div class="row">
<div class="col-sm-5">
<div class="form-group">
<label for="amount_1">Amount</label> <input name="amount[]" type="text" id="amount_1" class="pa form-control kb-pad1 amount" />
</div>
</div>
<div class="col-sm-5 col-sm-offset-1">
<div class="form-group">
<label for="paid_by_1">Paying by</label> <select name="paid_by[]" id="paid_by_1" class="form-control paid_by">
<option value="cash">Cash</option>
<option value="gift_card">Gift Card</option>
<option value="CC">Credit Card</option>
<option value="Cheque">Cheque</option>
<option value="other">Other</option><option value="deposit">Deposit</option> <option value="ppp">Paypal Pro</option> <option value="stripe">Stripe</option> <option value="authorize">Authorize.net</option> </select>
</div>
</div>
</div>
<div class="row">
<div class="col-sm-11">
<div class="form-group gc_1" style="display: none;">
<label for="gift_card_no_1">Gift Card No</label> <input name="paying_gift_card_no[]" type="text" id="gift_card_no_1" class="pa form-control kb-pad gift_card_no" />
<div id="gc_details_1"></div>
</div>
<div class="pcc_1" style="display:none;">
<div class="form-group">
<input type="text" id="swipe_1" class="form-control swipe" placeholder="Swipe" />
</div>
<div class="row">
<div class="col-md-6">
 <div class="form-group">
<input name="cc_no[]" type="text" id="pcc_no_1" class="form-control" placeholder="Credit Card No" />
</div>
</div>
<div class="col-md-6">
<div class="form-group">
<input name="cc_holer[]" type="text" id="pcc_holder_1" class="form-control" placeholder="Holder Name" />
</div>
</div>
<div class="col-md-3">
<div class="form-group">
<select name="cc_type[]" id="pcc_type_1" class="form-control pcc_type" placeholder="Card Type">
<option value="Visa">Visa</option>
<option value="MasterCard">MasterCard</option>
<option value="Amex">Amex</option>
<option value="Discover">Discover</option>
</select>

</div>
</div>
<div class="col-md-3">
<div class="form-group">
<input name="cc_month[]" type="text" id="pcc_month_1" class="form-control" placeholder="Month" />
</div>
</div>
<div class="col-md-3">
<div class="form-group">
<input name="cc_year" type="text" id="pcc_year_1" class="form-control" placeholder="Year" />
</div>
</div>
<div class="col-md-3">
<div class="form-group">
<input name="cc_cvv2" type="text" id="pcc_cvv2_1" class="form-control" placeholder="Security Code" />
</div>
</div>
 </div>
</div>
<div class="pcheque_1" style="display:none;">
<div class="form-group"><label for="cheque_no_1">Cheque No</label> <input name="cheque_no[]" type="text" id="cheque_no_1" class="form-control cheque_no" />
</div>
</div>
<div class="form-group">
<label for="payment_note">Payment Note</label> <textarea name="payment_note[]" id="payment_note_1" class="pa form-control kb-text payment_note"></textarea>
</div>
</div>
</div>
</div>
</div>
</div>
<div id="multi-payment"></div>
<button type="button" class="btn btn-primary col-md-12 addButton"><i class="fa fa-plus"></i> Add More Payments</button>
<div style="clear:both; height:15px;"></div>
<div class="font16">
<table class="table table-bordered table-condensed table-striped" style="margin-bottom: 0;">
<tbody>
<tr>
<td width="25%">Total Items</td>
<td width="25%" class="text-right"><span id="item_count">0.00</span></td>
<td width="25%">Total Payable</td>
<td width="25%" class="text-right"><span id="twt">0.00</span></td>
</tr>
<tr>
<td>Total Paying</td>
<td class="text-right"><span id="total_paying">0.00</span></td>
<td>Balance</td>
<td class="text-right"><span id="balance">0.00</span></td>
</tr>
</tbody>
</table>
<div class="clearfix"></div>
</div>
</div>
<div class="col-md-2 col-sm-3 text-center">
<span style="font-size: 1.2em; font-weight: bold;">Quick Cash</span>
<div class="btn-group btn-group-vertical">
<button type="button" class="btn btn-lg btn-info quick-cash" id="quick-payable">0.00
</button>
<button type="button" class="btn btn-lg btn-warning quick-cash">10</button><button type="button" class="btn btn-lg btn-warning quick-cash">20</button><button type="button" class="btn btn-lg btn-warning quick-cash">50</button><button type="button" class="btn btn-lg btn-warning quick-cash">100</button><button type="button" class="btn btn-lg btn-warning quick-cash">500</button><button type="button" class="btn btn-lg btn-warning quick-cash">1000</button><button type="button" class="btn btn-lg btn-warning quick-cash">5000</button> <button type="button" class="btn btn-lg btn-danger" id="clear-cash-notes">Clear</button>
</div>
</div>
</div>
</div>
<div class="modal-footer">
<button class="btn btn-block btn-lg btn-primary" id="submit-sale">Submit</button>
</div>
</div>
</div>
</div>
<div class="modal" id="cmModal" tabindex="-1" role="dialog" aria-labelledby="cmModalLabel" aria-hidden="true">
<div class="modal-dialog">
<div class="modal-content">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">
<i class="fa fa-2x">&times;</i></span>
<span class="sr-only">Close</span>
</button>
<h4 class="modal-title" id="cmModalLabel"></h4>
</div>
<div class="modal-body" id="pr_popover_content">
<div class="form-group">
<label for="icomment">Comment</label> <textarea name="comment" cols="40" rows="10" class="form-control" id="icomment" style="height:80px;"></textarea>
</div>
<div class="form-group">
<label for="iordered">Ordered</label> <select name="ordered" class="form-control" id="iordered" style="width:100%;">
<option value="0">No</option>
<option value="1">Yes</option>
</select>
</div>
<input type="hidden" id="irow_id" value="" />
</div>
<div class="modal-footer">
<button type="button" class="btn btn-primary" id="editComment">Submit</button>
</div>
</div>
</div>
</div>
<div class="modal" id="prModal" tabindex="-1" role="dialog" aria-labelledby="prModalLabel" aria-hidden="true">
<div class="modal-dialog">
<div class="modal-content">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true"><i class="fa fa-2x">&times;</i></span><span class="sr-only">Close</span></button>
<h4 class="modal-title" id="prModalLabel"></h4>
</div>
<div class="modal-body" id="pr_popover_content">
<form class="form-horizontal" role="form">
<div class="form-group">
<label class="col-sm-4 control-label">Product Tax</label>
<div class="col-sm-8">
<select name="ptax" id="ptax" class="form-control pos-input-tip" style="width:100%;">
<option value="" selected="selected"></option>
<option value="1">No Tax</option>
<option value="2">VAT @10%</option>
<option value="3">GST @6%</option>
<option value="4">VAT @20%</option>
<option value="5">GST @0%</option>
</select>
</div>
</div>
<div class="form-group">
<label for="pserial" class="col-sm-4 control-label">Serial No</label>
<div class="col-sm-8">
<input type="text" class="form-control kb-text" id="pserial">
</div>
</div>
<div class="form-group">
<label for="pquantity" class="col-sm-4 control-label">Quantity</label>
<div class="col-sm-8">
<input type="text" class="form-control kb-pad" id="pquantity">
</div>
</div>
<div class="form-group">
<label for="punit" class="col-sm-4 control-label">Product Unit</label>
<div class="col-sm-8">
<div id="punits-div"></div>
</div>
</div>
<div class="form-group">
<label for="poption" class="col-sm-4 control-label">Product Option</label>
<div class="col-sm-8">
<div id="poptions-div"></div>
</div>
</div>
 <div class="form-group">
<label for="pdiscount" class="col-sm-4 control-label">Product Discount</label>
<div class="col-sm-8">
<input type="text" class="form-control kb-pad" id="pdiscount">
</div>
</div>
<div class="form-group">
<label for="pprice" class="col-sm-4 control-label">Unit Price</label>
<div class="col-sm-8">
<input type="text" class="form-control kb-pad" id="pprice">
</div>
</div>
<table class="table table-bordered table-striped">
<tr>
<th style="width:25%;">Net Unit Price</th>
<th style="width:25%;"><span id="net_price"></span></th>
<th style="width:25%;">Product Tax</th>
<th style="width:25%;"><span id="pro_tax"></span></th>
</tr>
</table>
<input type="hidden" id="punit_price" value="" />
<input type="hidden" id="old_tax" value="" />
<input type="hidden" id="old_qty" value="" />
<input type="hidden" id="old_price" value="" />
<input type="hidden" id="row_id" value="" />
</form>
</div>
<div class="modal-footer">
<button type="button" class="btn btn-primary" id="editItem">Submit</button>
</div>
</div>
</div>
</div>
<div class="modal fade in" id="gcModal" tabindex="-1" role="dialog" aria-labelledby="mModalLabel" aria-hidden="true">
<div class="modal-dialog">
<div class="modal-content">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i></button>
<h4 class="modal-title" id="myModalLabel">Sell Gift Card</h4>
</div>
<div class="modal-body">
<p>Please fill in the information below. The field labels marked with * are required input fields.</p>
<div class="alert alert-danger gcerror-con" style="display: none;">
<button data-dismiss="alert" class="close" type="button">×</button>
<span id="gcerror"></span>
</div>
<div class="form-group">
<label for="gccard_no">Card No</label> *
<div class="input-group">
<input type="text" name="gccard_no" value="" class="form-control" id="gccard_no" />
<div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;">
<a href="#" id="genNo"><i class="fa fa-cogs"></i></a>
</div>
</div>
</div>
<input type="hidden" name="gcname" value="Gift Card" id="gcname" />
<div class="form-group">
<label for="gcvalue">Value</label> *
<input type="text" name="gcvalue" value="" class="form-control" id="gcvalue" />
</div>
<div class="form-group">
<label for="gcprice">Price</label> *
<input type="text" name="gcprice" value="" class="form-control" id="gcprice" />
</div>
<div class="form-group">
<label for="gccustomer">Customer</label> <input type="text" name="gccustomer" value="" class="form-control" id="gccustomer" />
</div>
<div class="form-group">
<label for="gcexpiry">Expiry Date</label> <input type="text" name="gcexpiry" value="20/11/2020" class="form-control date" id="gcexpiry" />
</div>
</div>
<div class="modal-footer">
<button type="button" id="addGiftCard" class="btn btn-primary">Sell Gift Card</button>
</div>
</div>
</div>
</div>
<div class="modal fade in" id="mModal" tabindex="-1" role="dialog" aria-labelledby="mModalLabel" aria-hidden="true">
<div class="modal-dialog">
<div class="modal-content">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true"><i class="fa fa-2x">&times;</i></span><span class="sr-only">Close</span></button>
<h4 class="modal-title" id="mModalLabel">Add Product Manually</h4>
</div>
<div class="modal-body" id="pr_popover_content">
<form class="form-horizontal" role="form">
<div class="form-group">
<label for="mcode" class="col-sm-4 control-label">Product Code *</label>
<div class="col-sm-8">
<input type="text" class="form-control kb-text" id="mcode">
</div>
</div>
<div class="form-group">
<label for="mname" class="col-sm-4 control-label">Product Name *</label>
<div class="col-sm-8">
<input type="text" class="form-control kb-text" id="mname">
</div>
</div>
<div class="form-group">
<label for="mtax" class="col-sm-4 control-label">Product Tax *</label>
<div class="col-sm-8">
<select name="mtax" id="mtax" class="form-control pos-input-tip" style="width:100%;">
<option value="" selected="selected"></option>
<option value="1">No Tax</option>
<option value="2">VAT @10%</option>
<option value="3">GST @6%</option>
<option value="4">VAT @20%</option>
<option value="5">GST @0%</option>
</select>
</div>
</div>
<div class="form-group">
<label for="mquantity" class="col-sm-4 control-label">Quantity *</label>
<div class="col-sm-8">
<input type="text" class="form-control kb-pad" id="mquantity">
</div>
</div>
<div class="form-group">
<label for="mdiscount" class="col-sm-4 control-label">Product Discount</label>
<div class="col-sm-8">
<input type="text" class="form-control kb-pad" id="mdiscount">
</div>
</div>
<div class="form-group">
<label for="mprice" class="col-sm-4 control-label">Unit Price *</label>
<div class="col-sm-8">
<input type="text" class="form-control kb-pad" id="mprice">
</div>
</div>
<table class="table table-bordered table-striped">
<tr>
<th style="width:25%;">Net Unit Price</th>
<th style="width:25%;"><span id="mnet_price"></span></th>
<th style="width:25%;">Product Tax</th>
<th style="width:25%;"><span id="mpro_tax"></span></th>
</tr>
</table>
</form>
</div>
<div class="modal-footer">
<button type="button" class="btn btn-primary" id="addItemManually">Submit</button>
</div>
</div>
</div>
</div>
<div class="modal fade in" id="sckModal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
<div class="modal-dialog">
<div class="modal-content">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">
<i class="fa fa-2x">&times;</i></span><span class="sr-only">Close</span>
</button>
<button type="button" class="btn btn-xs btn-default no-print pull-right" style="margin-right:15px;" onclick="window.print();">
<i class="fa fa-print"></i> Print </button>
<h4 class="modal-title" id="mModalLabel">Shortcut Keys</h4>
</div>
<div class="modal-body" id="pr_popover_content">
<table class="table table-bordered table-striped table-condensed table-hover" style="margin-bottom: 0px;">
<thead>
<tr>
<th>Shortcut Keys</th>
<th>Actions</th>
</tr>
</thead>
<tbody>
<tr>
<td>Ctrl+F3</td>
<td>Focus Add Item Input</td>
</tr>
<tr>
<td>Ctrl+Shift+M</td>
<td>Add Manual Item to Sale</td>
</tr>
<tr>
<td>Ctrl+Shift+C</td>
<td>Customer Input</td>
</tr>
<tr>
<td>Ctrl+Shift+A</td>
<td>Add Customer</td>
</tr>
<tr>
<td>Ctrl+F11</td>
<td>Toggle Categories Slider</td>
</tr>
<tr>
<td>Ctrl+F12</td>
<td>Toggle Subcategories Slider</td>
</tr>
<tr>
<td>F4</td>
<td>Cancel Sale</td>
</tr>
<tr>
<td>F7</td>
<td>Suspend Sale</td>
</tr>
<tr>
<td>F9</td>
<td>Print items list</td>
</tr>
<tr>
<td>F8</td>
<td>Finalize Sale</td>
</tr>
<tr>
<td>Ctrl+F1</td>
<td>Today's Sale</td>
</tr>
<tr>
<td>Ctrl+F2</td>
<td>Open Suspended Sales</td>
</tr>
<tr>
<td>Ctrl+F10</td>
<td>Close Register</td>
</tr>
</tbody>
</table>
</div>
</div>
</div>
</div>
<div class="modal fade in" id="dsModal" tabindex="-1" role="dialog" aria-labelledby="dsModalLabel" aria-hidden="true">
<div class="modal-dialog modal-sm">
<div class="modal-content">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
<i class="fa fa-2x">&times;</i>
</button>
<h4 class="modal-title" id="dsModalLabel">Edit Order Discount</h4>
</div>
<div class="modal-body">
<div class="form-group">
<label for="order_discount_input">Order Discount</label> <input type="text" name="order_discount_input" value="" class="form-control kb-pad" id="order_discount_input" />
</div>
</div>
<div class="modal-footer">
<button type="button" id="updateOrderDiscount" class="btn btn-primary">Update</button>
 </div>
</div>
</div>
</div>
<div class="modal fade in" id="sModal" tabindex="-1" role="dialog" aria-labelledby="sModalLabel" aria-hidden="true">
<div class="modal-dialog modal-sm">
<div class="modal-content">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
<i class="fa fa-2x">&times;</i>
</button>
<h4 class="modal-title" id="sModalLabel">Shipping</h4>
</div>
<div class="modal-body">
<div class="form-group">
<label for="shipping_input">Shipping</label> <input type="text" name="shipping_input" value="" class="form-control kb-pad" id="shipping_input" />
</div>
</div>
<div class="modal-footer">
<button type="button" id="updateShipping" class="btn btn-primary">Update</button>
</div>
</div>
</div>
</div>
<div class="modal fade in" id="txModal" tabindex="-1" role="dialog" aria-labelledby="txModalLabel" aria-hidden="true">
<div class="modal-dialog modal-sm">
<div class="modal-content">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i></button>
<h4 class="modal-title" id="txModalLabel">Edit Order Tax</h4>
</div>
<div class="modal-body">
<div class="form-group">
<label for="order_tax_input">Order Tax</label><select name="order_tax_input" id="order_tax_input" class="form-control pos-input-tip" style="width:100%;">
<option value="" selected="selected"></option>
<option value="1">No Tax</option>
<option value="2">VAT @10%</option>
<option value="3">GST @6%</option>
<option value="4">VAT @20%</option>
<option value="5">GST @0%</option>
</select>
</div>
</div>
<div class="modal-footer">
<button type="button" id="updateOrderTax" class="btn btn-primary">Update</button>
</div>
</div>
</div>
</div>
<div class="modal fade in" id="susModal" tabindex="-1" role="dialog" aria-labelledby="susModalLabel" aria-hidden="true">
<div class="modal-dialog">
<div class="modal-content">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i></button>
<h4 class="modal-title" id="susModalLabel">Suspend Sale</h4>
</div>
<div class="modal-body">
<p>Please type reference note and submit to suspend this sale</p>
<div class="form-group">
<label for="reference_note">Reference Note</label> <input type="text" name="reference_note" value="" class="form-control kb-text" id="reference_note" />
</div>
</div>
<div class="modal-footer">
<button type="button" id="suspend_sale" class="btn btn-primary">Submit</button>
</div>
</div>
</div>
</div>
<div id="order_tbl"><span id="order_span"></span>
<table id="order-table" class="prT table table-striped" style="margin-bottom:0;" width="100%"></table>
</div>
<div id="bill_tbl"><span id="bill_span"></span>
<table id="bill-table" width="100%" class="prT table table-striped" style="margin-bottom:0;"></table>
<table id="bill-total-table" class="prT table" style="margin-bottom:0;" width="100%"></table>
<span id="bill_footer"></span>
</div>
<div class="modal fade in" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"></div>
<div class="modal fade in" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2" aria-hidden="true"></div>
<div id="modal-loading" style="display: none;">
<div class="blackbg"></div>
<div class="loader"></div>
</div>
<script type="67d37507c7a2d57f19750a70-text/javascript">
var site = {"url":"https:\/\/sma.tecdiary.com\/","base_url":"https:\/\/sma.tecdiary.com\/admin\/\/","assets":"https:\/\/sma.tecdiary.com\/themes\/default\/admin\/assets\/","settings":{"logo":"logo2.png","logo2":"logo3.png","site_name":"Stock Manager Advance","language":"english","default_warehouse":"1","accounting_method":"0","default_currency":"USD","default_tax_rate":"1","rows_per_page":"10","version":"3.4.8","default_tax_rate2":"1","dateformat":"5","sales_prefix":"SALE","quote_prefix":"QUOTE","purchase_prefix":"PO","transfer_prefix":"TR","delivery_prefix":"DO","payment_prefix":"IPAY","return_prefix":"RETURNSL","returnp_prefix":"","expense_prefix":"","item_addition":"0","theme":"default","product_serial":"1","default_discount":"1","product_discount":"1","discount_method":"1","tax1":"1","tax2":"1","overselling":"0","iwidth":"800","iheight":"800","twidth":"60","theight":"60","watermark":"0","smtp_host":"pop.gmail.com","bc_fix":"4","auto_detect_barcode":"1","captcha":"0","reference_format":"2","racks":"1","attributes":"1","product_expiry":"0","decimals":"0","qty_decimals":"0","decimals_sep":".","thousands_sep":",","invoice_view":"0","default_biller":"3","rtl":"0","each_spent":null,"ca_point":null,"each_sale":null,"sa_point":null,"sac":"1","display_all_products":"0","display_symbol":"0","symbol":"","remove_expired":"0","barcode_separator":"-","set_focus":"0","price_group":"1","barcode_img":"1","ppayment_prefix":"POP","disable_editing":"90","qa_prefix":"","update_cost":"0","apis":"1","state":"AN","pdf_lib":"mpdf","user_language":"english","user_rtl":"0","indian_gst":false},"dateFormats":{"js_sdate":"dd\/mm\/yyyy","php_sdate":"d\/m\/Y","mysq_sdate":"%d\/%m\/%Y","js_ldate":"dd\/mm\/yyyy hh:ii","php_ldate":"d\/m\/Y H:i","mysql_ldate":"%d\/%m\/%Y %H:%i"}}, pos_settings = {"pos_id":"1","cat_limit":"22","pro_limit":"20","default_category":"1","default_customer":"1","default_biller":"3","display_time":"1","cf_title1":"GST Reg","cf_title2":"VAT Reg","cf_value1":"123456789","cf_value2":"987654321","receipt_printer":"BIXOLON SRP-350II","cash_drawer_codes":"x1C","focus_add_item":"Ctrl+F3","add_manual_product":"Ctrl+Shift+M","customer_selection":"Ctrl+Shift+C","add_customer":"Ctrl+Shift+A","toggle_category_slider":"Ctrl+F11","toggle_subcategory_slider":"Ctrl+F12","cancel_sale":"F4","suspend_sale":"F7","print_items_list":"F9","finalize_sale":"F8","today_sale":"Ctrl+F1","open_hold_bills":"Ctrl+F2","close_register":"Ctrl+F10","keyboard":"1","pos_printers":"BIXOLON SRP-350II, BIXOLON SRP-350II","java_applet":"0","product_button_color":"default","tooltips":"1","paypal_pro":"1","stripe":"1","rounding":"0","char_per_line":"42","pin_code":null,"purchase_code":"purchase_code","envato_username":"envato_username","version":"3.2.7","after_sale_page":"0","item_order":"0","authorize":"1","toggle_brands_slider":null,"remote_printing":"1","printer":null,"order_printers":null,"auto_print":"0","customer_details":null,"local_printers":null};
var lang = {
    unexpected_value: 'Unexpected value provided!',
    select_above: 'Please select above first',
    r_u_sure: 'Are you sure?',
    bill: 'Bill',
    order: 'Order',
    total: 'Total',
    items: 'Items',
    discount: 'Discount',
    order_tax: 'Order Tax',
    grand_total: 'Grand Total',
    total_payable: 'Total Payable',
    rounding: 'Rounding',
    merchant_copy: 'Merchant Copy'
};
</script>
<script type="67d37507c7a2d57f19750a70-text/javascript">
    var product_variant = 0, shipping = 0, p_page = 0, per_page = 0, tcp = "8", pro_limit = 20,
        brand_id = 0, obrand_id = 0, cat_id = "1", ocat_id = "1", sub_cat_id = 0, osub_cat_id,
        count = 1, an = 1, DT = 1,
        product_tax = 0, invoice_tax = 0, product_discount = 0, order_discount = 0, total_discount = 0, total = 0, total_paid = 0, grand_total = 0,
        KB = 1, tax_rates =[{"id":"1","name":"No Tax","code":"NT","rate":"0.0000","type":"2"},{"id":"2","name":"VAT @10%","code":"VAT10","rate":"10.0000","type":"1"},{"id":"3","name":"GST @6%","code":"S","rate":"6.0000","type":"1"},{"id":"4","name":"VAT @20%","code":"VT20","rate":"20.0000","type":"1"},{"id":"5","name":"GST @0%","code":"Z","rate":"0.0000","type":"2"}];
    var protect_delete = 0, billers = [{"logo":"logo1.png","company":"Test Biller"}], biller = {"logo":"logo1.png","company":"Test Biller"};
    var username = 'owner', order_data = '', bill_data = '';

    function widthFunctions(e) {
        var wh = $(window).height(),
            lth = $('#left-top').height(),
            lbh = $('#left-bottom').height();
        $('#item-list').css("height", wh - 140);
        $('#item-list').css("min-height", 515);
        $('#left-middle').css("height", wh - lth - lbh - 102);
        $('#left-middle').css("min-height", 278);
        $('#product-list').css("height", wh - lth - lbh - 107);
        $('#product-list').css("min-height", 278);
    }
    $(window).bind("resize", widthFunctions);
    $(document).ready(function () {
        $('#view-customer').click(function(){
            $('#myModal').modal({remote: site.base_url + 'customers/view/' + $("input[name=customer]").val()});
            $('#myModal').modal('show');
        });
        $('textarea').keydown(function (e) {
            if (e.which == 13) {
               var s = $(this).val();
               $(this).val(s+'\n').focus();
               e.preventDefault();
               return false;
            }
        });
        
        
        widthFunctions();
                        if (!localStorage.getItem('poscustomer')) {
            localStorage.setItem('poscustomer', 1);
        }
                if (!localStorage.getItem('postax2')) {
            localStorage.setItem('postax2', 1);
        }
        $('.select').select2({minimumResultsForSearch: 7});
        // var customers = [{
        //     id: 1,
        //     text: 'Walk-in Customer'
        // }];
        $('#poscustomer').val(localStorage.getItem('poscustomer')).select2({
            minimumInputLength: 1,
            data: [],
            initSelection: function (element, callback) {
                $.ajax({
                    type: "get", async: false,
                    url: "https://sma.tecdiary.com/admin/customers/getCustomer/" + $(element).val(),
                    dataType: "json",
                    success: function (data) {
                        callback(data[0]);
                    }
                });
            },
            ajax: {
                url: site.base_url + "customers/suggestions",
                dataType: 'json',
                quietMillis: 15,
                data: function (term, page) {
                    return {
                        term: term,
                        limit: 10
                    };
                },
                results: function (data, page) {
                    if (data.results != null) {
                        return {results: data.results};
                    } else {
                        return {results: [{id: '', text: 'No Match Found'}]};
                    }
                }
            }
        });
        if (KB) {
            display_keyboards();

            var result = false, sct = '';
            $('#poscustomer').on('select2-opening', function () {
                sct = '';
                $('.select2-input').addClass('kb-text');
                display_keyboards();
                $('.select2-input').bind('change.keyboard', function (e, keyboard, el) {
                    if (el && el.value != '' && el.value.length > 0 && sct != el.value) {
                        sct = el.value;
                    }
                    if(!el && sct.length > 0) {
                        $('.select2-input').addClass('select2-active');
                        setTimeout(function() {
                            $.ajax({
                                type: "get",
                                async: false,
                                url: "https://sma.tecdiary.com/admin/customers/suggestions/?term=" + sct,
                                dataType: "json",
                                success: function (res) {
                                    if (res.results != null) {
                                        $('#poscustomer').select2({data: res}).select2('open');
                                        $('.select2-input').removeClass('select2-active');
                                    } else {
                                        // bootbox.alert('no_match_found');
                                        $('#poscustomer').select2('close');
                                        $('#test').click();
                                    }
                                }
                            });
                        }, 500);
                    }
                });
            });

            $('#poscustomer').on('select2-close', function () {
                $('.select2-input').removeClass('kb-text');
                $('#test').click();
                $('select, .select').select2('destroy');
                $('select, .select').select2({minimumResultsForSearch: 7});
            });
            $(document).bind('click', '#test', function () {
                var kb = $('#test').keyboard().getkeyboard();
                kb.close();
            });

        }

        $(document).on('change', '#posbiller', function () {
            var sb = $(this).val();
            $.each(billers, function () {
                if(this.id == sb) {
                    biller = this;
                }
            });
            $('#biller').val(sb);
        });

                $('#paymentModal').on('change', '#amount_1', function (e) {
            $('#amount_val_1').val($(this).val());
        });
        $('#paymentModal').on('blur', '#amount_1', function (e) {
            $('#amount_val_1').val($(this).val());
        });
        $('#paymentModal').on('select2-close', '#paid_by_1', function (e) {
            $('#paid_by_val_1').val($(this).val());
        });
        $('#paymentModal').on('change', '#pcc_no_1', function (e) {
            $('#cc_no_val_1').val($(this).val());
        });
        $('#paymentModal').on('change', '#pcc_holder_1', function (e) {
            $('#cc_holder_val_1').val($(this).val());
        });
        $('#paymentModal').on('change', '#gift_card_no_1', function (e) {
            $('#paying_gift_card_no_val_1').val($(this).val());
        });
        $('#paymentModal').on('change', '#pcc_month_1', function (e) {
            $('#cc_month_val_1').val($(this).val());
        });
        $('#paymentModal').on('change', '#pcc_year_1', function (e) {
            $('#cc_year_val_1').val($(this).val());
        });
        $('#paymentModal').on('change', '#pcc_type_1', function (e) {
            $('#cc_type_val_1').val($(this).val());
        });
        $('#paymentModal').on('change', '#pcc_cvv2_1', function (e) {
            $('#cc_cvv2_val_1').val($(this).val());
        });
        $('#paymentModal').on('change', '#cheque_no_1', function (e) {
            $('#cheque_no_val_1').val($(this).val());
        });
        $('#paymentModal').on('change', '#payment_note_1', function (e) {
            $('#payment_note_val_1').val($(this).val());
        });
                $('#paymentModal').on('change', '#amount_2', function (e) {
            $('#amount_val_2').val($(this).val());
        });
        $('#paymentModal').on('blur', '#amount_2', function (e) {
            $('#amount_val_2').val($(this).val());
        });
        $('#paymentModal').on('select2-close', '#paid_by_2', function (e) {
            $('#paid_by_val_2').val($(this).val());
        });
        $('#paymentModal').on('change', '#pcc_no_2', function (e) {
            $('#cc_no_val_2').val($(this).val());
        });
        $('#paymentModal').on('change', '#pcc_holder_2', function (e) {
            $('#cc_holder_val_2').val($(this).val());
        });
        $('#paymentModal').on('change', '#gift_card_no_2', function (e) {
            $('#paying_gift_card_no_val_2').val($(this).val());
        });
        $('#paymentModal').on('change', '#pcc_month_2', function (e) {
            $('#cc_month_val_2').val($(this).val());
        });
        $('#paymentModal').on('change', '#pcc_year_2', function (e) {
            $('#cc_year_val_2').val($(this).val());
        });
        $('#paymentModal').on('change', '#pcc_type_2', function (e) {
            $('#cc_type_val_2').val($(this).val());
        });
        $('#paymentModal').on('change', '#pcc_cvv2_2', function (e) {
            $('#cc_cvv2_val_2').val($(this).val());
        });
        $('#paymentModal').on('change', '#cheque_no_2', function (e) {
            $('#cheque_no_val_2').val($(this).val());
        });
        $('#paymentModal').on('change', '#payment_note_2', function (e) {
            $('#payment_note_val_2').val($(this).val());
        });
                $('#paymentModal').on('change', '#amount_3', function (e) {
            $('#amount_val_3').val($(this).val());
        });
        $('#paymentModal').on('blur', '#amount_3', function (e) {
            $('#amount_val_3').val($(this).val());
        });
        $('#paymentModal').on('select2-close', '#paid_by_3', function (e) {
            $('#paid_by_val_3').val($(this).val());
        });
        $('#paymentModal').on('change', '#pcc_no_3', function (e) {
            $('#cc_no_val_3').val($(this).val());
        });
        $('#paymentModal').on('change', '#pcc_holder_3', function (e) {
            $('#cc_holder_val_3').val($(this).val());
        });
        $('#paymentModal').on('change', '#gift_card_no_3', function (e) {
            $('#paying_gift_card_no_val_3').val($(this).val());
        });
        $('#paymentModal').on('change', '#pcc_month_3', function (e) {
            $('#cc_month_val_3').val($(this).val());
        });
        $('#paymentModal').on('change', '#pcc_year_3', function (e) {
            $('#cc_year_val_3').val($(this).val());
        });
        $('#paymentModal').on('change', '#pcc_type_3', function (e) {
            $('#cc_type_val_3').val($(this).val());
        });
        $('#paymentModal').on('change', '#pcc_cvv2_3', function (e) {
            $('#cc_cvv2_val_3').val($(this).val());
        });
        $('#paymentModal').on('change', '#cheque_no_3', function (e) {
            $('#cheque_no_val_3').val($(this).val());
        });
        $('#paymentModal').on('change', '#payment_note_3', function (e) {
            $('#payment_note_val_3').val($(this).val());
        });
                $('#paymentModal').on('change', '#amount_4', function (e) {
            $('#amount_val_4').val($(this).val());
        });
        $('#paymentModal').on('blur', '#amount_4', function (e) {
            $('#amount_val_4').val($(this).val());
        });
        $('#paymentModal').on('select2-close', '#paid_by_4', function (e) {
            $('#paid_by_val_4').val($(this).val());
        });
        $('#paymentModal').on('change', '#pcc_no_4', function (e) {
            $('#cc_no_val_4').val($(this).val());
        });
        $('#paymentModal').on('change', '#pcc_holder_4', function (e) {
            $('#cc_holder_val_4').val($(this).val());
        });
        $('#paymentModal').on('change', '#gift_card_no_4', function (e) {
            $('#paying_gift_card_no_val_4').val($(this).val());
        });
        $('#paymentModal').on('change', '#pcc_month_4', function (e) {
            $('#cc_month_val_4').val($(this).val());
        });
        $('#paymentModal').on('change', '#pcc_year_4', function (e) {
            $('#cc_year_val_4').val($(this).val());
        });
        $('#paymentModal').on('change', '#pcc_type_4', function (e) {
            $('#cc_type_val_4').val($(this).val());
        });
        $('#paymentModal').on('change', '#pcc_cvv2_4', function (e) {
            $('#cc_cvv2_val_4').val($(this).val());
        });
        $('#paymentModal').on('change', '#cheque_no_4', function (e) {
            $('#cheque_no_val_4').val($(this).val());
        });
        $('#paymentModal').on('change', '#payment_note_4', function (e) {
            $('#payment_note_val_4').val($(this).val());
        });
                $('#paymentModal').on('change', '#amount_5', function (e) {
            $('#amount_val_5').val($(this).val());
        });
        $('#paymentModal').on('blur', '#amount_5', function (e) {
            $('#amount_val_5').val($(this).val());
        });
        $('#paymentModal').on('select2-close', '#paid_by_5', function (e) {
            $('#paid_by_val_5').val($(this).val());
        });
        $('#paymentModal').on('change', '#pcc_no_5', function (e) {
            $('#cc_no_val_5').val($(this).val());
        });
        $('#paymentModal').on('change', '#pcc_holder_5', function (e) {
            $('#cc_holder_val_5').val($(this).val());
        });
        $('#paymentModal').on('change', '#gift_card_no_5', function (e) {
            $('#paying_gift_card_no_val_5').val($(this).val());
        });
        $('#paymentModal').on('change', '#pcc_month_5', function (e) {
            $('#cc_month_val_5').val($(this).val());
        });
        $('#paymentModal').on('change', '#pcc_year_5', function (e) {
            $('#cc_year_val_5').val($(this).val());
        });
        $('#paymentModal').on('change', '#pcc_type_5', function (e) {
            $('#cc_type_val_5').val($(this).val());
        });
        $('#paymentModal').on('change', '#pcc_cvv2_5', function (e) {
            $('#cc_cvv2_val_5').val($(this).val());
        });
        $('#paymentModal').on('change', '#cheque_no_5', function (e) {
            $('#cheque_no_val_5').val($(this).val());
        });
        $('#paymentModal').on('change', '#payment_note_5', function (e) {
            $('#payment_note_val_5').val($(this).val());
        });
        
        $('#payment').click(function () {
                        var twt = formatDecimal((total + invoice_tax) - order_discount + shipping);
            if (count == 1) {
                bootbox.alert('Please add product before payment. Thank you!');
                return false;
            }
            gtotal = formatDecimal(twt);
                        $('#twt').text(formatMoney(gtotal));
            $('#quick-payable').text(gtotal);
                        $('#item_count').text(count - 1);
            $('#paymentModal').appendTo("body").modal('show');
            $('#amount_1').focus();
        });
        $('#paymentModal').on('show.bs.modal', function(e) {
            $('#submit-sale').text('Submit').attr('disabled', false);
        });
        $('#paymentModal').on('shown.bs.modal', function(e) {
            $('#amount_1').focus().val(0);
            $('#quick-payable').click();
        });
        var pi = 'amount_1', pa = 2;
        $(document).on('click', '.quick-cash', function () {
            if ($('#quick-payable').find('span.badge').length) {
                $('#clear-cash-notes').click();
            }
            var $quick_cash = $(this);
            var amt = $quick_cash.contents().filter(function () {
                return this.nodeType == 3;
            }).text();
            var th = ',';
            var $pi = $('#' + pi);
            amt = formatDecimal(amt.split(th).join("")) * 1 + $pi.val() * 1;
            $pi.val(formatDecimal(amt)).focus();
            var note_count = $quick_cash.find('span');
            if (note_count.length == 0) {
                $quick_cash.append('<span class="badge">1</span>');
            } else {
                note_count.text(parseInt(note_count.text()) + 1);
            }
        });
        $(document).on('click', '#quick-payable', function () {
            $('#clear-cash-notes').click();
            $(this).append('<span class="badge">1</span>');
            $('#amount_1').val(grand_total);
        });
        $(document).on('click', '#clear-cash-notes', function () {
            $('.quick-cash').find('.badge').remove();
            $('#' + pi).val('0').focus();
        });

        $(document).on('change', '.gift_card_no', function () {
            var cn = $(this).val() ? $(this).val() : '';
            var payid = $(this).attr('id'),
                id = payid.substr(payid.length - 1);
            if (cn != '') {
                $.ajax({
                    type: "get", async: false,
                    url: site.base_url + "sales/validate_gift_card/" + cn,
                    dataType: "json",
                    success: function (data) {
                        if (data === false) {
                            $('#gift_card_no_' + id).parent('.form-group').addClass('has-error');
                            bootbox.alert('Gift card number is incorrect or expired.');
                        } else if (data.customer_id !== null && data.customer_id !== $('#poscustomer').val()) {
                            $('#gift_card_no_' + id).parent('.form-group').addClass('has-error');
                            bootbox.alert('Gift card number is not for this customer.');
                        } else {
                            $('#gc_details_' + id).html('<small>Card No: ' + data.card_no + '<br>Value: ' + data.value + ' - Balance: ' + data.balance + '</small>');
                            $('#gift_card_no_' + id).parent('.form-group').removeClass('has-error');
                            //calculateTotals();
                            $('#amount_' + id).val(gtotal >= data.balance ? data.balance : gtotal).focus();
                        }
                    }
                });
            }
        });

        $(document).on('click', '.addButton', function () {
            if (pa <= 5) {
                $('#paid_by_1, #pcc_type_1').select2('destroy');
                var phtml = $('#payments').html(),
                    update_html = phtml.replace(/_1/g, '_' + pa);
                pi = 'amount_' + pa;
                $('#multi-payment').append('<button type="button" class="close close-payment" style="margin: -10px 0px 0 0;"><i class="fa fa-2x">&times;</i></button>' + update_html);
                $('#paid_by_1, #pcc_type_1, #paid_by_' + pa + ', #pcc_type_' + pa).select2({minimumResultsForSearch: 7});
                read_card();
                pa++;
            } else {
                bootbox.alert('Max allowed limit reached.');
                return false;
            }
            if (KB) { display_keyboards(); }
            $('#paymentModal').css('overflow-y', 'scroll');
        });

        $(document).on('click', '.close-payment', function () {
            $(this).next().remove();
            $(this).remove();
            pa--;
        });

        $(document).on('focus', '.amount', function () {
            pi = $(this).attr('id');
            calculateTotals();
        }).on('blur', '.amount', function () {
            calculateTotals();
        });

        function calculateTotals() {
            var total_paying = 0;
            var ia = $(".amount");
            $.each(ia, function (i) {
                var this_amount = formatCNum($(this).val() ? $(this).val() : 0);
                total_paying += parseFloat(this_amount);
            });
            $('#total_paying').text(formatMoney(total_paying));
                        $('#balance').text(formatMoney(total_paying - gtotal));
            $('#balance_' + pi).val(formatDecimal(total_paying - gtotal));
            total_paid = total_paying;
            grand_total = gtotal;
                    }

        $("#add_item").autocomplete({
            source: function (request, response) {
                if (!$('#poscustomer').val()) {
                    $('#add_item').val('').removeClass('ui-autocomplete-loading');
                    bootbox.alert('Please select above first');
                    //response('');
                    $('#add_item').focus();
                    return false;
                }
                $.ajax({
                    type: 'get',
                    url: 'https://sma.tecdiary.com/admin/sales/suggestions',
                    dataType: "json",
                    data: {
                        term: request.term,
                        warehouse_id: $("#poswarehouse").val(),
                        customer_id: $("#poscustomer").val()
                    },
                    success: function (data) {
                        $(this).removeClass('ui-autocomplete-loading');
                        response(data);
                    }
                });
            },
            minLength: 1,
            autoFocus: false,
            delay: 250,
            response: function (event, ui) {
                if ($(this).val().length >= 16 && ui.content[0].id == 0) {
                    bootbox.alert('No matching result found! Product might be out of stock in the selected warehouse.', function () {
                        $('#add_item').focus();
                    });
                    $(this).val('');
                }
                else if (ui.content.length == 1 && ui.content[0].id != 0) {
                    ui.item = ui.content[0];
                    $(this).data('ui-autocomplete')._trigger('select', 'autocompleteselect', ui);
                    $(this).autocomplete('close');
                }
                else if (ui.content.length == 1 && ui.content[0].id == 0) {
                    bootbox.alert('No matching result found! Product might be out of stock in the selected warehouse.', function () {
                        $('#add_item').focus();
                    });
                    $(this).val('');

                }
            },
            select: function (event, ui) {
                event.preventDefault();
                if (ui.item.id !== 0) {
                    var row = add_invoice_item(ui.item);
                    if (row)
                        $(this).val('');
                } else {
                    bootbox.alert('No matching result found! Product might be out of stock in the selected warehouse.');
                }
            }
        });

        $(".pos-tip").tooltip();        // $('#posTable').stickyTableHeaders({fixedOffset: $('#product-list')});
        $('#posTable').stickyTableHeaders({scrollableArea: $('#product-list')});
        $('#product-list, #category-list, #subcategory-list, #brands-list').perfectScrollbar({suppressScrollX: true});
        $('select, .select').select2({minimumResultsForSearch: 7});

        $(document).on('click', '.product', function (e) {
            $('#modal-loading').show();
            code = $(this).val(),
                wh = $('#poswarehouse').val(),
                cu = $('#poscustomer').val();
            $.ajax({
                type: "get",
                url: "https://sma.tecdiary.com/admin/pos/getProductDataByCode",
                data: {code: code, warehouse_id: wh, customer_id: cu},
                dataType: "json",
                success: function (data) {
                    e.preventDefault();
                    if (data !== null) {
                        add_invoice_item(data);
                        $('#modal-loading').hide();
                    } else {
                        bootbox.alert('No matching result found! Product might be out of stock in the selected warehouse.');
                        $('#modal-loading').hide();
                    }
                }
            });
        });

        $(document).on('click', '.category', function () {
            if (cat_id != $(this).val()) {
                $('#open-category').click();
                $('#modal-loading').show();
                cat_id = $(this).val();
                $.ajax({
                    type: "get",
                    url: "https://sma.tecdiary.com/admin/pos/ajaxcategorydata",
                    data: {category_id: cat_id},
                    dataType: "json",
                    success: function (data) {
                        $('#item-list').empty();
                        var newPrs = $('<div></div>');
                        newPrs.html(data.products);
                        newPrs.appendTo("#item-list");
                        $('#subcategory-list').empty();
                        var newScs = $('<div></div>');
                        newScs.html(data.subcategories);
                        newScs.appendTo("#subcategory-list");
                        tcp = data.tcp;
                        nav_pointer();
                    }
                }).done(function () {
                    p_page = 'n';
                    $('#category-' + cat_id).addClass('active');
                    $('#category-' + ocat_id).removeClass('active');
                    ocat_id = cat_id;
                    $('#modal-loading').hide();
                    nav_pointer();
                });
            }
        });
        $('#category-' + cat_id).addClass('active');

        $(document).on('click', '.brand', function () {
            if (brand_id != $(this).val()) {
                $('#open-brands').click();
                $('#modal-loading').show();
                brand_id = $(this).val();
                $.ajax({
                    type: "get",
                    url: "https://sma.tecdiary.com/admin/pos/ajaxbranddata",
                    data: {brand_id: brand_id},
                    dataType: "json",
                    success: function (data) {
                        $('#item-list').empty();
                        var newPrs = $('<div></div>');
                        newPrs.html(data.products);
                        newPrs.appendTo("#item-list");
                        tcp = data.tcp;
                        nav_pointer();
                    }
                }).done(function () {
                    p_page = 'n';
                    $('#brand-' + brand_id).addClass('active');
                    $('#brand-' + obrand_id).removeClass('active');
                    obrand_id = brand_id;
                    $('#category-' + cat_id).removeClass('active');
                    $('#subcategory-' + sub_cat_id).removeClass('active');
                    cat_id = 0; sub_cat_id = 0;
                    $('#modal-loading').hide();
                    nav_pointer();
                });
            }
        });

        $(document).on('click', '.subcategory', function () {
            if (sub_cat_id != $(this).val()) {
                $('#open-subcategory').click();
                $('#modal-loading').show();
                sub_cat_id = $(this).val();
                $.ajax({
                    type: "get",
                    url: "https://sma.tecdiary.com/admin/pos/ajaxproducts",
                    data: {category_id: cat_id, subcategory_id: sub_cat_id, per_page: p_page != 0 ? p_page : 'n' },
                    dataType: "html",
                    success: function (data) {
                        $('#item-list').empty();
                        var newPrs = $('<div></div>');
                        newPrs.html(data);
                        newPrs.appendTo("#item-list");
                    }
                }).done(function () {
                    p_page = 'n';
                    $('#subcategory-' + sub_cat_id).addClass('active');
                    $('#subcategory-' + osub_cat_id).removeClass('active');
                    $('#modal-loading').hide();
                });
            }
        });

        $('#next').click(function () {
            if (p_page == 'n') {
                p_page = 0
            }
            p_page = p_page + pro_limit;
            if (tcp >= pro_limit && p_page < tcp) {
                $('#modal-loading').show();
                $.ajax({
                    type: "get",
                    url: "https://sma.tecdiary.com/admin/pos/ajaxproducts",
                    data: {category_id: cat_id, subcategory_id: sub_cat_id, per_page: p_page != 0 ? p_page : 'n'},
                    dataType: "html",
                    success: function (data) {
                        $('#item-list').empty();
                        var newPrs = $('<div></div>');
                        newPrs.html(data);
                        newPrs.appendTo("#item-list");
                        nav_pointer();
                    }
                }).done(function () {
                    $('#modal-loading').hide();
                });
            } else {
                p_page = p_page - pro_limit;
            }
        });

        $('#previous').click(function () {
            if (p_page == 'n') {
                p_page = 0;
            }
            if (p_page != 0) {
                $('#modal-loading').show();
                p_page = p_page - pro_limit;
                if (p_page == 0) {
                    p_page = 'n'
                }
                $.ajax({
                    type: "get",
                    url: "https://sma.tecdiary.com/admin/pos/ajaxproducts",
                    data: {category_id: cat_id, subcategory_id: sub_cat_id, per_page: p_page != 0 ? p_page : 'n'},
                    dataType: "html",
                    success: function (data) {
                        $('#item-list').empty();
                        var newPrs = $('<div></div>');
                        newPrs.html(data);
                        newPrs.appendTo("#item-list");
                        nav_pointer();
                    }

                }).done(function () {
                    $('#modal-loading').hide();
                });
            }
        });

        $(document).on('change', '.paid_by', function () {
            $('#clear-cash-notes').click();
            $('#amount_1').val(grand_total);
            var p_val = $(this).val(),
                id = $(this).attr('id'),
                pa_no = id.substr(id.length - 1);
            $('#rpaidby').val(p_val);
            if (p_val == 'cash' || p_val == 'other') {
                $('.pcheque_' + pa_no).hide();
                $('.pcc_' + pa_no).hide();
                $('.pcash_' + pa_no).show();
                $('#amount_' + pa_no).focus();
            } else if (p_val == 'CC' || p_val == 'stripe' || p_val == 'ppp' || p_val == 'authorize') {
                $('.pcheque_' + pa_no).hide();
                $('.pcash_' + pa_no).hide();
                $('.pcc_' + pa_no).show();
                $('#swipe_' + pa_no).focus();
            } else if (p_val == 'Cheque') {
                $('.pcc_' + pa_no).hide();
                $('.pcash_' + pa_no).hide();
                $('.pcheque_' + pa_no).show();
                $('#cheque_no_' + pa_no).focus();
            } else {
                $('.pcheque_' + pa_no).hide();
                $('.pcc_' + pa_no).hide();
                $('.pcash_' + pa_no).hide();
            }
            if (p_val == 'gift_card') {
                $('.gc_' + pa_no).show();
                $('.ngc_' + pa_no).hide();
                $('#gift_card_no_' + pa_no).focus();
            } else {
                $('.ngc_' + pa_no).show();
                $('.gc_' + pa_no).hide();
                $('#gc_details_' + pa_no).html('');
            }
        });

        $(document).on('click', '#submit-sale', function () {
            if (total_paid == 0 || total_paid < grand_total) {
                bootbox.confirm("Paid amount is less than the payable amount. Please press OK to submit the sale.", function (res) {
                    if (res == true) {
                        $('#pos_note').val(localStorage.getItem('posnote'));
                        $('#staff_note').val(localStorage.getItem('staffnote'));
                        $('#submit-sale').text('Loading...').attr('disabled', true);
                        $('#pos-sale-form').submit();
                    }
                });
                return false;
            } else {
                $('#pos_note').val(localStorage.getItem('posnote'));
                $('#staff_note').val(localStorage.getItem('staffnote'));
                $(this).text('Loading...').attr('disabled', true);
                $('#pos-sale-form').submit();
            }
        });
        $('#suspend').click(function () {
            if (count <= 1) {
                bootbox.alert('Please add product before suspending the sale. Thank you!');
                return false;
            } else {
                $('#susModal').modal();
            }
        });
        $('#suspend_sale').click(function () {
            ref = $('#reference_note').val();
            if (!ref || ref == '') {
                bootbox.alert('Please type reference note and submit to suspend this sale');
                return false;
            } else {
                suspend = $('<span></span>');
                                suspend.html('<input type="hidden" name="suspend" value="yes" /><input type="hidden" name="suspend_note" value="' + ref + '" />');
                                suspend.appendTo("#hidesuspend");
                $('#total_items').val(count - 1);
                $('#pos-sale-form').submit();

            }
        });
    });

    $(document).ready(function () {
        $('#print_order').click(function () {
            if (count == 1) {
                bootbox.alert('Please add product before payment. Thank you!');
                return false;
            }
                            Popup($('#order_tbl').html());
                    });
        $('#print_bill').click(function () {
            if (count == 1) {
                bootbox.alert('Please add product before payment. Thank you!');
                return false;
            }
                            Popup($('#bill_tbl').html());
                    });
    });

    $(function () {
        $(".alert").effect("shake");
        setTimeout(function () {
            $(".alert").hide('blind', {}, 500)
        }, 15000);
                var now = new moment();
        $('#display_time').text(now.format((site.dateFormats.js_sdate).toUpperCase() + " HH:mm"));
        setInterval(function () {
            var now = new moment();
            $('#display_time').text(now.format((site.dateFormats.js_sdate).toUpperCase() + " HH:mm"));
        }, 1000);
            });
        function Popup(data) {
        var mywindow = window.open('', 'sma_pos_print', 'height=500,width=300');
        mywindow.document.write('<html><head><title>Print</title>');
        mywindow.document.write('<link rel="stylesheet" href="https://sma.tecdiary.com/themes/default/admin/assets/styles/helpers/bootstrap.min.css" type="text/css" />');
        mywindow.document.write('</head><body >');
        mywindow.document.write(data);
        mywindow.document.write('</body></html>');
        mywindow.print();
        mywindow.close();
        return true;
    }
    </script>
<script type="67d37507c7a2d57f19750a70-text/javascript" src="https://sma.tecdiary.com/themes/default/admin/assets/js/bootstrap.min.js"></script>
<script type="67d37507c7a2d57f19750a70-text/javascript" src="https://sma.tecdiary.com/themes/default/admin/assets/js/jquery-ui.min.js"></script>
<script type="67d37507c7a2d57f19750a70-text/javascript" src="https://sma.tecdiary.com/themes/default/admin/assets/js/perfect-scrollbar.min.js"></script>
<script type="67d37507c7a2d57f19750a70-text/javascript" src="https://sma.tecdiary.com/themes/default/admin/assets/js/select2.min.js"></script>
<script type="67d37507c7a2d57f19750a70-text/javascript" src="https://sma.tecdiary.com/themes/default/admin/assets/js/jquery.dataTables.min.js"></script>
<script type="67d37507c7a2d57f19750a70-text/javascript" src="https://sma.tecdiary.com/themes/default/admin/assets/js/custom.js"></script>
<script type="67d37507c7a2d57f19750a70-text/javascript" src="https://sma.tecdiary.com/themes/default/admin/assets/js/jquery.calculator.min.js"></script>
<script type="67d37507c7a2d57f19750a70-text/javascript" src="https://sma.tecdiary.com/themes/default/admin/assets/js/bootstrapValidator.min.js"></script>
<script type="67d37507c7a2d57f19750a70-text/javascript" src="https://sma.tecdiary.com/themes/default/admin/assets/pos/js/plugins.min.js"></script>
<script type="67d37507c7a2d57f19750a70-text/javascript" src="https://sma.tecdiary.com/themes/default/admin/assets/pos/js/parse-track-data.js"></script>
<script type="67d37507c7a2d57f19750a70-text/javascript" src="https://sma.tecdiary.com/themes/default/admin/assets/pos/js/pos.ajax.js"></script>
<script type="67d37507c7a2d57f19750a70-text/javascript">
$('.sortable_table tbody').sortable({
    containerSelector: 'tr'
});
</script>
<script type="67d37507c7a2d57f19750a70-text/javascript" charset="UTF-8">(function ($) { "use strict"; $.fn.select2.locales['sma'] = { formatMatches: function (matches) { if (matches === 1) { return "One result is available, press enter to select it."; } return matches + "results are available, use up and down arrow keys to navigate."; }, formatNoMatches: function () { return "No matches found"; }, formatInputTooShort: function (input, min) { var n = min - input.length; return "Please type "+n+" or more characters"; }, formatInputTooLong: function (input, max) { var n = input.length - max; if(n == 1) { return "Please delete "+n+" character"; } else { return "Please delete "+n+" characters"; } }, formatSelectionTooBig: function (n) { if(n == 1) { return "You can only select "+n+" item"; } else { return "You can only select "+n+" items"; } }, formatLoadMore: function (pageNumber) { return "Loading more results..."; }, formatSearching: function () { return "Searching..."; }, formatAjaxError: function() { return "Ajax request failed"; }, }; $.extend($.fn.select2.defaults, $.fn.select2.locales['sma']); })(jQuery);</script>
<div id="ajaxCall"><i class="fa fa-spinner fa-pulse"></i></div>
<script src="https://ajax.cloudflare.com/cdn-cgi/scripts/2448a7bd/cloudflare-static/rocket-loader.min.js" data-cf-nonce="67d37507c7a2d57f19750a70-" defer=""></script></body>
</html>
