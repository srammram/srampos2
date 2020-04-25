<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?=lang('pos_module') . " | " . $Settings->site_name;?></title>
    <script type="text/javascript">if(parent.frames.length !== 0){top.location = '<?=admin_url('pos')?>';}</script>
    <base href="<?=base_url()?>"/>
    <meta http-equiv="cache-control" content="max-age=0"/>
    <meta http-equiv="cache-control" content="no-cache"/>
    <meta http-equiv="expires" content="0"/>
    <meta http-equiv="pragma" content="no-cache"/>
    <link rel="shortcut icon" href="<?=$assets?>images/favicon.ico"/>
      <?php if($this->pos_settings->font_family ==0) { ?>
            <link rel="stylesheet" href="<?=$assets?>styles/theme.css" type="text/css"/>
    <?php }elseif ($this->pos_settings->font_family ==1) { ?><!-- for kimmo client and font family AKbalthom-KhmerNew  -->
        <link rel="stylesheet" href="<?=$assets?>styles/theme_for_kimmo.css" type="text/css"/>
    <?php } ?>
    <link rel="stylesheet" href="<?=$assets?>styles/style.css" type="text/css"/>
    <link rel="stylesheet" href="<?=$assets?>pos/css/posajax.css" type="text/css"/>
    <link rel="stylesheet" href="<?=$assets?>pos/css/print.css" type="text/css" media="print"/>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script type="text/javascript" src="<?=$assets?>js/jquery-2.0.3.min.js"></script>
    <script type="text/javascript" src="<?=$assets?>js/jquery-migrate-1.2.1.min.js"></script>
	
    <link href="<?= $assets ?>styles/flipclock.css" rel="stylesheet"/>    
	<link rel="stylesheet" href="select2.min.css" />
    <script type="text/javascript" src="<?= $assets ?>js/flipclock.min.js"></script>
    
    <!--[if lt IE 9]>
    <script src="<?=$assets?>js/jquery.js"></script>
	
    <![endif]-->
	
    <?php if ($Settings->user_rtl) {?>
        <link href="<?=$assets?>styles/helpers/bootstrap-rtl.min.css" rel="stylesheet"/>
        <link href="<?=$assets?>styles/style-rtl.css" rel="stylesheet"/>
        <script type="text/javascript">
            $(document).ready(function () {
                $('.pull-right, .pull-left').addClass('flip');
            });
        </script>
    <?php }
    ?>
    <script>var assets = '<?=$assets?>';var baseurl = '<?=base_url()?>';var curr_page = 'tables';var curr_func="refresh_tables";</script>
	

    <style>
		body{overflow-y: auto;padding-right: 6px;}
		#cp{padding-left: 1%;}
		#pos{background-color: transparent;position: relative;float: left;width: 100%;}
/*table*/
		.table_sec_pills{margin: 0px 0px;border-bottom: 1px solid #543816;}
		#exTab1 .tab-content {
			color : #333;
			padding : 5px 0px 0px;
		}
		#exTab1 .nav-pills li:first-child{margin-left: 15px;}
		#exTab1 .nav-pills li{margin-top: 0px;margin-left: 10px;}
		#exTab1 .nav-pills{border-bottom: 1px solid #543816;padding-bottom: 5px;margin-top: 5px;}
		#exTab1 .nav-pills > li > a {
			border-radius: 0;
			color:#543816; 
			padding: 6px;
			font-size: 13px;font-weight: bold;
			text-transform: uppercase;
			background-color: #efefef;
			border: 1px solid #ccc;
		}
		#exTab1 .nav-pills>li.active>a,#exTab1 .nav-pills>li.active>a:hover, #exTab1 .nav-pills>li.active>a:focus{background-color: #543816;color: #fff;border: 1px solid #543816;}
			
/* billing */
			
		#exTab2 .tab-content {
			color : #333;
			padding : 5px 0px;
		}
		#exTab2 .nav-pills li:first-child{margin-left: 15px;margin-right: 15px;}
		#exTab2 .nav-pills{float: right;margin-top: 8px;position: absolute;right: 0px;z-index: 2;}
		#exTab2 .nav-pills > li > a {
			border-radius: 0;
			color:#543816; 
			padding: 6px 16px;
			font-size: 15px;font-weight: bold;
			border: 1px solid #543816;
		}
		#exTab2 .nav-pills>li.active>a,#exTab2 .nav-pills>li.active>a:hover, #exTab2 .nav-pills>li.active>a:focus{background-color: #543816;color: #fff;}
		#pos #leftdiv{padding: 7px;}
/*		*/
		#tables_box{ width:100%;}
/*		.tableright {width: 100%; white-space: nowrap;}*/
		.item{width: 80px;height: 80px;display: inline-block;margin-right: 10px;}
		  .tableright {
			position: relative;
			width: 100%;
			overflow-x: hidden;
			overflow-y: hidden;
		/*		white-space: nowrap; */
			transition: all 0.2s;
			will-change: transform;
			user-select: none;
			cursor: pointer;
			  padding-bottom: 5px;
		  }
		  .items.active {
			background: rgba(255,255,255,0.3);
			cursor: grabbing;
			cursor: -webkit-grabbing;
			transform: scale(1);
		  }
/*		*/
		/*		*/
		.order_biller_table{width: 50%;margin: 0px;padding: 0px;}
		.order_biller_table .bil_tab_nam{font-size: 16px;margin: 5px;padding: 4px;font-weight: bold;}
		.order_biller_table h2{font-size: 16px;padding: 0;margin: 5px 0px 10px;}
		.order_biller_table .cancel_bill ,.order_biller_table .request_bil_new{font-size: 16px;padding: 0;margin: 5px 0px 10px;}
		.payment-list-container{padding-right: 0px;padding-left: 0px;}
		.payment-list-container .btn-group{    display: flow-root;}
		.payment-list-container .btn-group .btn,.request_bil{font-size: 16px;height: 41px;padding-left: 0;padding-right: 0;}
		.order_biller_table .cancel_bill, .order_biller_table .request_bil_new{margin: 0px;padding: 3px;font-size: 18px;}
/*		category*/
		#category-list .btn-prni{height: auto;min-height: 60px;background-color: #ffb760;border-radius: 0px;box-shadow: 0px 0px 0px 0px #543816;letter-spacing: 0.15px;margin-right: 9px;margin-bottom: 10px;}
		#category-list .btn-prni.active{background-color: #543816!important;color: #fff!important;font-weight: 500;box-shadow: 0px 0px 0px 0px #543816;}
		#category-list .btn-prni span{width: 120px;font-size: 16px;}
		#category-list{margin: 6.1% 0px 0px;}
		#subcategory-list .btn-prni{height: 70px;min-width: 15% !important;background-color: #f5690a;margin: 7px 0px;}
		#subcategory-list .btn-prni img{display: none;}
		#subcategory-list .btn-prni span,#subcategory-list .btn-prni .sub_strong{border: none;border-radius: 0px;height: 60px;line-height: 60px;box-shadow: 0px 1px 2px 0px #f38500;font-size: 15px!important;letter-spacing: 0.5px; margin: 3px 0px 4px;}
		#subcategory-list .btn-prni .name_strong {
			line-height: 20px;    
			white-space: pre-wrap;
			height: auto;
		}
		#subcategory-list .btn-prni.active{background-color: transparent!important;}
		#subcategory-list{margin: 0px 0px 0px;}
/*		*/
		#pos #proContainer{padding: 5px 0px 0px;}
		#ajaxrecipe{border: none;padding-top: 0px;}
		#ajaxrecipe .btn-prni{background-color: #ffbd6c;border-radius: 0px;height: 80px;position: relative;float: left;min-width: 15% !important;}
		#ajaxrecipe .btn-prni img{display: none;}
		#ajaxrecipe .btn-prni span{font-size: 15px !important;}
		#ajaxrecipe .btn-prni{margin-left: 0px;margin-right: 10px;}
		#ajaxrecipe .btn-prni .name_strong{word-wrap: break-word;overflow: visible;display: block;line-height: 20px;width: 100%;vertical-align: top;height: auto;line-height: 20px;text-transform: capitalize;}
		#ajaxrecipe .btn-prni .price_strong{position: absolute;bottom: 5px;left: 0px;}
		
		/* table slider  */
		#left-button,#right-button{width: 60px;height: 80px;border-radius: 4px;border-color: transparent;}
	#left-button:hover,#left-button:focus,#right-button:hover,#right-button:focus{background-color: #543816;transition: 0.2s all ease-in;}
	#left-button:hover .fa,#left-button:focus .fa,#right-button:hover .fa,#right-button:focus .fa{color: #fff;}
	#left-button:hover,#left-button:focus,#right-button:hover,#right-button:focus{outline: none;box-shadow: none;}
	.left{position: absolute;right: 5%; bottom: 7.4%;display: none;}
	.right{position: absolute;right: 0px;bottom: 7.4%;display: none;}
	.tableright_s{margin-bottom: 2px;padding: 0px;}
	.table_id_small p{white-space: pre-line;margin-top: 0px;}
	#totalTable .right_td,#totalTable .left_td{width: auto;}
		#pos #left-middle{height: auto !important;min-height: 190px !important;}
		#recipe-list,.dragscroll{height: 240px!important;min-height: 190px!important;}
		#left-bottom{padding: 0px;}
		.no-print .select2-container .select2-choice .select2-arrow{padding-top: 0px;}
		#item-list{ height: 175px!important;min-height: auto!important;overflow-y: scroll;}
		.pos-grid-nav{margin-top: 0px;}
		.pos-grid-nav .btn+.btn{right: -20px;}
		.pos-grid-nav .btn+.btn button{top: -75px!important;}
		.req_sound{display: none;}
		.tableright_sed {margin-top: 10%;}
/*		*/
		#left-top .select2-container, #ui #add_item, #left-top .select2-container .select2-choice, #seats_id,#ui #add_item::placeholder{    height: 30px;line-height: 30px;font-size: 14px;font-weight: 600;color: #333;}
		#posTable .sname{font-weight: bold;font-size: 14px;}
		
		.merge-group-list{padding: 20px;vertical-align: middle;}
		.merge-group-list label input[type=checkbox]{margin-right: 5px;}


		.add_cust_sec{z-index: 9999;}
		.table_list .new_split .btn{margin-right: 1px;}
		.table_list .new_split .btn:last-child{margin-right: 0px;}
		.table_list .ul_main{margin-top: 0px;}
		.newsplit{position: absolute;right: -8px;}
		.ad12{margin-top:6%;}
		.modal-co{
			    text-align: center;
				box-shadow: 0px 0px 2px 1px #333;
				margin: 12%;
				padding: 5%;}
		.bootbox-bod{
			font-size: 16px;
			font-weight: bold;
			margin: 10px;}
/*		media query*/
		@media  (max-width: 1920px) and (min-width: 1900px){
			#category-list {margin: 4.2% 0px 0px;}
			#recipe-list, .dragscroll {
				height: 470px!important;
				min-height: 190px!important;
			}
			#item-list {
				height: 452px!important;
				min-height: auto!important;
				overflow-y: scroll;
			}
		}
		@media (max-width: 1680px) and (min-width: 1650px){}
		@media (max-width: 1600px) and (min-width: 1500px){
			#recipe-list, .dragscroll {
				height: 334px!important;
				min-height: 190px!important;
			}
			#item-list {
				height: 295px!important;
			}
			#category-list{margin: 5.1% 0px 0px;}
		}
		@media (max-width: 1440px) and (min-width: 1400px){}
		@media (max-width: 1366px) and (min-width: 1362px){}
		@media (max-width: 1360px) and (min-width: 1300px){}
		@media (max-width: 1280px) and (min-width: 1200px){
			#recipe-list,.dragscroll{height: 210px!important;min-height: 190px!important;}
		}
		@media (max-width: 1024px) and (min-width: 980px){}
		
		.select2-dropdown {
 top: 22px !important;
 left: 8px !important;
}

table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

td, th {
  border: 1px solid #dddddd;
  text-align: left;
  padding: 8px;
}

tr:nth-child(even) {
  background-color: #dddddd;
}
.table_list .item_list .gbgbgb .value_padd{padding-top:0px!important;}
	</style>
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
<?php
$currency = $this->site->getAllCurrencies();

?>
<div id="wrapper">
	<?php
    if($this->Settings->user_language == 'english' ) { 
         $this->load->view($this->theme . 'pos/pos_header');   
         }else{// for kimmo 
            $this->load->view($this->theme . 'pos/pos_header_kimmo'); 
         }
	
	?>
    <div id="content">
        <div class="c1">
            <div class="pos">
                <?php              if ($error) {
                        echo "<div class=\"alert alert-danger\"><button type=\"button\" class=\"close fa-2x\" data-dismiss=\"alert\">&times;</button>" . $error . "</div>";
                    }
                ?>
                <?php
                    //if (!empty($message)) {
                      //  echo "<div class=\"alert alert-success\"><button type=\"button\" class=\"close fa-2x\" data-dismiss=\"alert\">&times;</button>" . $message . "</div>";
                   // }
                ?>
                <div id="pos">
                    <input type="hidden" id="order_type" value="<?php echo $order_type; ?>" name="order_type">
                    <div style="clear:both;"></div>
                    <section class="table_sec_pills">
                    	<div class="container-fluid">
                    		<div class="row">
                    			<div class="col-md-12 col-xs-12" style="padding: 0px;">
                    				<div id="exTab1">	
										
									
   <div class="tableright_s col-sm-12 col-xs-12">
    <ul  class="nav nav-pills">
	  <?php 	$tableid=($_GET['table'])?$_GET['table']:'';  
	            $areaid= $this->site->getTablearea($tableid);
	           if(!empty($areas)){ 
	           $i=1;  
	  foreach($areas as $areas_row){  
	  if(!empty($tableid)){
	  if($areas_row->area_id ==$areaid){ $active="active"; }else{ $active="";  }  ?>
	<li class="<?php echo $active;  ?>"><a  href="#<?php echo $i;  ?>a" data-toggle="tab"><?php echo $areas_row->areas_name; ?></a></li>
	  <?php  }else{   ?>
							<li class="<?php echo ($i==1)?"active":"";  ?>"><a  href="#<?php echo $i;  ?>a" data-toggle="tab"><?php echo $areas_row->areas_name; ?></a></li>				
	  <?php } $i++; }  }   ?>
										</ul>
<div class="tab-content clearfix" >
    <?php    
    if(!empty($areas)){ $i=1;
        foreach($areas as $areas_row){
			 if(!empty($tableid)){
			 if($areas_row->area_id ==$areaid){ $active="active"; }else{ $active="";  }
			 
    ?>
	
       <div class="tab-pane  <?php echo $active;  ?>" id="<?php echo $i;  ?>a">
			 <?php  }else{ ?>
			  <div class="tab-pane <?php echo ($i==1)?"active":"";  ?>" id="<?php echo $i;  ?>a">
		<?php  } ?>
       <div class="tableright" >
	  <?php
		/*switch(!empty($areas_row->tables)){
			
			 case "$table_status == 'Available'":
				echo " $table_status ";
			 break;
			 
			 
			 case "$table_status == 'Ready'":
				echo " $table_status ";
			 break;
			 
			 case "$table_status == 'Kitchen'":
				echo " $table_status ";
			 break;
			 
			 case "$table_status == 'Pending'":
				echo " $table_status ";
			 break;
			 
			 case "$table_status == 'Processing'":
				echo " $table_status ";
			 break;
			 
		
		default:
			echo " $table_status ";
		
		} 
			*/	
	   ?>  
        <?php
	
		
        if(!empty($areas_row->tables)){
            foreach($areas_row->tables as $tables){
                
				if($this->sma->actionPermissions('table_add')){
				
                // $table_status = $this->site->orderTablecheck($tables->table_id);

                /*if($table_status == 'Available'){
                    $disabled = '';
                    $class = 'green_ribbon';
                    $main_class = 'green_class';
                }elseif($table_status == 'In_Kitchen' || $table_status == 'READY'){
                    $disabled = '';
                    $class = 'blue_ribbon';
                    $main_class = 'blue_class';
                }elseif($table_status == 'SERVED'){
                    $disabled = '';
                    $class = 'orange_ribbon';
                    $main_class = 'orange_class';
                }elseif($table_status == 'PENDING'){
                    $disabled = '';
                    $class = 'red_ribbon';
                    $main_class = 'red_class';
                }elseif($table_status == 'Ongoingothers'){
                    $disabled = 'disabled';
                    $class = 'red_ribbon';
                    $main_class = 'red_class';
                }else{
                    $disabled = '';
                    $class = '';
                }
				}else{   $disabled = ''; } */ 

                $table_status = $tables->current_order_status;
                $current_order_user = $tables->current_order_user;
                $user_id = $this->session->userdata('user_id');
               $Alluseraccess = $this->site->getGroupPermissionsAlluseraccess($this->session->userdata('group_id'));  
               // var_dump($Alluseraccess);
               // if($Alluseraccess != 0)   {          
                if($table_status == 0){
                    $available_color = $this->pos_settings->table_available_color;
                    $available_color = (explode("/",$available_color));

                        $table_status = 'Available';
                        $disabled = '';
                        $class = $available_color[0];
                        $main_class = $available_color[1];
                    }
					elseif($table_status == 1 ){
                        $kitchen_color = $this->pos_settings->table_kitchen_color;
                        $kitchen_color = (explode("/",$kitchen_color));
                        $table_status = 'In_Kitchen';
                        $disabled = '';
                        $class = $kitchen_color[0];
                        $main_class = $kitchen_color[1];
                    }elseif($table_status == 2){
                        $table_status = 'READY';
                        $disabled = '';
                        $class = 'blue_ribbon';
                        $main_class = 'blue_class';
                    }elseif($table_status == 3){
                        $table_status = 'SERVED';
                        $disabled = '';
                        $class = 'orange_ribbon';
                        $main_class = 'orange_class';
                    }elseif($table_status == 4){
                        $pending_color = $this->pos_settings->table_pending_color;
                        $pending_color = (explode("/",$pending_color));
                        $table_status = 'PENDING';
                        $disabled = '';
                        $class = $pending_color[0];
                        $main_class = $pending_color[1];
                    }
					//elseif($table_status == 5){
                      //  $table_status = 'Ongoingothers';
                       // $disabled = '';
                      //  $class = 'red_ribbon';
                      // $main_class = 'red_class';
                    //}
					
					else{
                        $disabled = '';
                        $class = '';
                    }                
                 //}else{   $disabled = ''; } 
            }else{
                $table_status = 'Ongoingothers';
                        $disabled = 'disabled';
                        $class = 'red_ribbon';
                        $main_class = 'red_class';

            }
            if($this->pos_settings->table_size == 0){
                $table_class="table_id_small";
            }else{
                $table_class="table_id_big";
            }            
           ?>         
                <div class="item" id="table-<?=$tables->table_id?>">
				<?php  if(isset($tableid) && $tables->table_id ==$tableid){
			$table="active_btn";
		}else{
			$table="";
		}?>
                    <button type="button"  <?php echo $disabled; ?>  class="table_id  <?=$table_class;?> <?php echo $table;  ?> <?php echo $main_class; ?>" value="<?php echo $tables->table_id ?>" >
                         <img src="<?=$assets?>images/table_hun.png" alt="table select">
                         <!-- <?php if($this->Settings->user_language == 'english'){ ?>
                         <p><?php echo $tables->table_name; ?></p>
                        <?php } ?> -->
                        <?php if($this->Settings->user_language != 'english' && $tables->native_name !=''){ ?>
                            <p ><?php echo $tables->native_name; ?></p>
                        <?php }else{ ?>
                            <p><?php echo $tables->table_name; ?></p>
                        <?php } ?>
                         <div class="ribbon <?php echo $class; ?>"> <span  ><?php echo $table_status;  ?></span></div>
                    </button>
                </div>
           <?php  

            }
        }
        ?>
       </div>
       
       </div>
    <?php
	$i++;
        }
    }
    ?>
    <div class="left">
		  <button id="left-button">
			<i class="fa fa-chevron-circle-left fa-2x" aria-hidden="true"></i>
		  </button>
		</div>
		<div class="right">
			<button id="right-button">
				<i class="fa fa-chevron-circle-right fa-2x" aria-hidden="true"></i>
			</button>
		</div>
    </div>
  
</div>

                    			</div>
                    		</div>
                    	</div>
                    </section>
                    <div style="clear:both;"></div>
                    <section class="billing_sec_pills">
						<div class="container-fluid">
							<div class="row">
								<div class="col-md-12 col-xs-12" style="padding: 0px;">
								
									<div id="exTab2">	
									
									
									
									



										<ul  class="nav nav-pills" id="nav  nav-pills1" >
										
										<?php
										
										//echo $this->input->get('table');
								  	$tableactive=$this->site->checkactiveTablestatus($this->input->get('table'));
									//$tablecancelStatus=$this->site->checkTableCancelStatus($this->input->get('table'));
									
									
									  if($tableactive ==1 && empty($this->input->get('split')) && empty($sprequest) ){ ?> 
											<li class="active" id="n1"><a href="#bill2" data-toggle="tab">VIEW BILLING</a></li>
												<?php  }else{  ?>
												
												
												<li class="active" id="n1"><a  href="#bill1" data-toggle="tab">ADD ORDER</a></li>
												
												
											<li ><a href="#bill2" data-toggle="tab">VIEW BILLING</a></li>
												<?php   }  ?>
											<!--<li><a href="#bill3" data-toggle="tab">KITCHEN</a></li>-->
										</ul>
										
										<div class="tab-content clearfix">
											<div class="tab-pane <?php  if($tableactive ==0 || !empty($this->input->get('split'))|| !empty($sprequest) ){ echo 'active' ; }  ?>" id="bill">
												<div>
												
                
                    <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'id' => 'pos-sale-form');
                    echo admin_form_open("pos/sent_to_kitchen_all", $attrib);?>
					
                    <div id="leftdiv">
					
                        <div id="printhead">
                            <h4 style="text-transform:uppercase;"><?php echo $Settings->site_name; ?></h4>
                            <?php
                                echo "<h5 style=\"text-transform:uppercase;\">" . $this->lang->line('order_list') . "</h5>";
                                echo $this->lang->line("date") . " " . $this->sma->hrld(date('Y-m-d H:i:s'));
                            ?>
                        </div>
						
                        <div id="left-top">
                            <div
                                style="position: absolute; <?=$Settings->user_rtl ? 'right:-9999px;' : 'left:-9999px;';?>"><?php echo form_input('test', '', 'id="test" class="kb-text-click"'); ?></div>
                                
                              <div class="no-print col-md-12 col-xs-12" style="padding: 0px;">
                                <?php if ($Owner || $Admin || !empty($this->session->userdata('warehouse_id'))) {
                                    ?>
                                    <div class="form-group" style="display: none;">
                                        <?php
                                            $wh[''] = '';
                                                foreach ($warehouses as $warehouse) {
													if($this->session->userdata('warehouse_id') == $warehouse->id){
                                                    	$wh[$warehouse->id] = $warehouse->name;
													}
                                                }
                                                echo form_dropdown('warehouse', $wh, (isset($_POST['warehouse']) ? $_POST['warehouse'] : $this->session->userdata('warehouse_id')), 'id="poswarehouse" class="form-control pos-input-tip" data-placeholder="' . $this->lang->line("select") . ' ' . $this->lang->line("warehouse") . '" required="required" style="width:100%;" ');
                                            ?>
                                    </div>
                                <?php } else {

                                        $warehouse_input = array(
                                            'type' => 'hidden',
                                            'name' => 'warehouse',
                                            'id' => 'poswarehouse',
                                            'value' => $this->session->userdata('warehouse_id'),
                                        );

                                        echo form_input($warehouse_input);
                                    }
                                ?>
                                
                                	<input type="hidden" value="<?php echo $get_table; ?>" name="table_list_id">
                                    <input type="hidden" value="<?php echo $get_order_type; ?>" name="order_type_id">
                                    <input type="hidden" value="<?php echo !empty($get_split) ? $get_split : ''; ?>" name="split_id">
                                 <div class="form-group col-sm-4 col-xs-12" style="pointer-events:none;padding: 0px 0px 0px 0px;">
                                        <?php
                                            $st[''] = '';
                                                foreach ($sales_types as $type) {
                                                    $st[$type->id] = $type->name;
                                                }
                                                echo form_dropdown('order_type', $st, (isset($_POST['order_type']) ? $_POST['order_type'] : $get_order_type), 'id="posorder_type" class="form-control"   data-placeholder="' . $this->lang->line("select") . ' ' . $this->lang->line("type") . '" required="required" style="width:100%;" ');
                                            ?>
                                    </div>
                               
                              	  <?php
								if(!empty($get_table)){
								?>
                                 <div class="form-group col-sm-4 col-xs-12" style="pointer-events:none;padding: 0px 0px 0px 2px;">
                                 		
                                        <select  class="form-control" data-placeholder="Select Tables" id="postable_list" required style="width:100%;" <?php if($this->sma->actionPermissions('table_edit')){ echo ''; }else{  echo 'disabled'; }  ?>>
                                        	<?php
											if(!empty($areas)){
											foreach($areas as $areas_row){
											?>
                                            <optgroup label="<?php echo $areas_row->areas_name; ?>">
                                            	<?php
												if(!empty($areas_row->tables)){
												foreach($areas_row->tables as $tables){
												?>
                                                    <option  <?php if($get_table == $tables->table_id){ echo 'selected'; }else{ echo ''; } ?> value="<?php echo $tables->table_id ?>"><?php echo $tables->table_name; ?></option>
                                                <?php
												}
												}
												?>
                                             </optgroup>
                                            <?php
											}
											}
											?>
                                        </select>
                                       
                                    </div> 
                              
                                <div class="form-group col-sm-4 col-xs-12" style="padding: 0px 0px 0px 2px;">
                                <input type="text" name="seats_id" id="seats_id" class="form-control  kb-pad text-center " placeholder="<?=lang('how_many_people')?>" >
                                </div>
                                <?php
								}
								?>
                              
                                <div class="form-group col-sm-12 col-xs-12" id="ui" style="padding: 0px;">
                                    
                                    <div class="input-group">
                                    
                                    <?php echo form_input('add_item', '', 'class="form-control pos-tip kb-text-click" id="add_item"  data-trigger="focus" placeholder="' . $this->lang->line("search_recipe_by_name_code") . '" title="' . $this->lang->line("au_pr_name_tip") . '"'); ?>
									
									 
                                    
                                        <div class="input-group-addon" style="padding: 2px 8px;">
                                            <a href="#" id="addManually">
                                                <i class="fa fa-plus-circle" id="addIcon" style="font-size: 2.5em;"></i>
                                            </a>
                                        </div>
                                    </div>
                                    
                                    <div style="clear:both;"></div>
                                </div>
                            </div>
                                
                              
                              
                           
                            <div class="form-group col-sm-12 col-xs-12" style="padding: 0px;">
                                <div class="input-group">
                                <?php
                                    echo form_input('customer', (isset($_POST['customer']) ? $_POST['customer'] : ""), 'id="poscustomer" data-placeholder="' . $this->lang->line("select") . ' ' . $this->lang->line("customer") . '" required="required" class="form-control pos-input-tip" style="width:100%;"');
                                ?>
                                    <div class="input-group-addon no-print" style="padding: 2px 8px; border-left: 0;">
                                        <a href="#" id="toogle-customer-read-attr" class="external">
                                            <i class="fa fa-pencil" id="addIcon" style="font-size: 2.5em;"></i>
                                        </a>
                                    </div>
                                    <div class="input-group-addon no-print" style="padding: 2px 7px; border-left: 0;">
                                        <a href="#" id="view-customer" class="external" data-toggle="modal" data-target="#myModal">
                                            <i class="fa fa-eye" id="addIcon" style="font-size: 2.5em;"></i>
                                        </a>
                                    </div>
                                <?php if ($Owner || $Admin || $GP['customers-add']) { ?>
                                    <div class="input-group-addon no-print" style="padding: 2px 8px;">
                                        <a href="<?=admin_url('customers/add_pos');?>" id="add-customer" class="external" data-toggle="modal" data-target="#myModal">
                                            <i class="fa fa-plus-circle" id="addIcon" style="font-size: 2.5em;"></i>
                                        </a>
                                    </div>
                                <?php } ?>
                                </div>
                                <div style="clear:both;"></div>
                            </div>                            
                        </div>                       
                        
                        <div id="print" class="col-md-12 col-xs-12" style="padding: 0px;">
                            <div id="left-middle">
                                <div id="recipe-list" class="dragscroll">
                                    <table class="table items table-striped table-fixed table-bordered table-condensed table-hover sortable_table"
                                           id="posTable" style="margin-bottom: 0;">
                                        <thead>
                                        <tr>
                                            <th width="40%"><?=lang("recipe");?></th>
                                            <th width="15%"><?=lang("price");?></th>
                                            <?php if($Settings->manual_item_discount == 1) { ?>
                                             <th  width="20%"><?=lang("discount");?> </th>
                                            <?php } ?>
                                            <th width="15%"><?=lang("quantity");?></th>
                                            <th width="10%"><?=lang("subtotal");?></th>
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
                                <table id="totalTable">
                                      <colgroup>
                                       	<col width="20%">
                                       	<col width="20%">
                                       	<col width="20%">
                                       	<col width="40%">
                                       </colgroup> 
                                                                <tr>
                                        <td class="left_td" style="padding: 5px 10px;"><b><?=lang('items');?></b></td>
                                    
<!--                                       	<td class="center_td">:</td>-->
                                       
                                        <td class="right_td text-right" style="padding: 5px 10px;font-size: 14px; font-weight:bold;">
                                            <span id="titems">0</span>
                                        </td>
                                        <td class="left_td" style="padding: 5px 10px;border-top: 1px solid #DDD;"><b><?=lang('total');?></b></td>
                                        <td class="right_td text-right" style="padding: 5px 10px;font-size: 14px; font-weight:bold;padding-right: 10.5%;">
                                            <span id="total">0.00</span>
                                        </td>
                                   </tr>
<!--
                                   <tr>
                                        <td class="left_td" style="padding: 5px 10px;border-top: 1px solid #DDD;"><?=lang('total');?></td>
                                        
                                        <td class="center_td">:</td>
                                    
                                        <td class="right_td text-right" style="padding: 5px 10px;font-size: 14px; font-weight:bold;">
                                            <span id="total">0.00</span>
                                        </td>
                                        
                                    </tr>
-->
                                   
                                </table>
                                

                                <div class="clearfix"></div>
                                <div id="botbuttons" class="col-xs-12 text-center">
                                    <input type="hidden" name="biller" id="biller" value="<?= ($Owner || $Admin || !$this->session->userdata('biller_id')) ? $pos_settings->default_biller : $this->session->userdata('biller_id')?>"/>
                                    <div class="row">
                                        <div class="col-xs-6" style="padding: 0;">
                                            <div class="btn-group-vertical btn-block">
                                                
                                                <button type="button" class="btn btn-success btn-block" style="height:67px;"  id="reset" <?php if($this->sma->actionPermissions('orders_cancel')){ echo ''; }else{  echo 'disabled'; }  ?>>
                                                     <i class="fa fa-ban" aria-hidden="true" style="margin-right: 5px;"></i><?= lang('order_cancel'); ?>
                                                </button>
                                            </div>

                                        </div>
                                        <div class="col-xs-6" style="padding: 0;">
                                            <div class="btn-group-vertical btn-block">
                                               
                                                
												<button type="button" class="btn btn-info btn-block" id="sent_to_kitchen" style="height:67px;" <?php if($this->sma->actionPermissions('sendtokitchen')){ echo ''; }else{  echo 'disabled'; }  ?>>
                                                    <i class="fa fa-paper-plane" aria-hidden="true" style="margin-right: 5px;"></i><?=lang('send_to_kitchen');?>
                                                </button>
                                               
                                            </div>
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
                                    <?php for ($i = 1; $i <= 5; $i++) {?>
                                        <input type="hidden" name="amount[]" id="amount_val_<?=$i?>" value=""/>
                                        <input type="hidden" name="balance_amount[]" id="balance_amount_<?=$i?>" value=""/>
                                        <input type="hidden" name="paid_by[]" id="paid_by_val_<?=$i?>" value="cash"/>
                                        <input type="hidden" name="cc_no[]" id="cc_no_val_<?=$i?>" value=""/>
                                        <input type="hidden" name="paying_gift_card_no[]" id="paying_gift_card_no_val_<?=$i?>" value=""/>
                                        <input type="hidden" name="cc_holder[]" id="cc_holder_val_<?=$i?>" value=""/>
                                        <input type="hidden" name="cheque_no[]" id="cheque_no_val_<?=$i?>" value=""/>
                                        <input type="hidden" name="cc_month[]" id="cc_month_val_<?=$i?>" value=""/>
                                        <input type="hidden" name="cc_year[]" id="cc_year_val_<?=$i?>" value=""/>
                                        <input type="hidden" name="cc_type[]" id="cc_type_val_<?=$i?>" value=""/>
                                        <input type="hidden" name="cc_cvv2[]" id="cc_cvv2_val_<?=$i?>" value=""/>
                                        <input type="hidden" name="payment_note[]" id="payment_note_val_<?=$i?>" value=""/>
                                    <?php }
                                    ?>
                                </div>
                                <input name="order_tax" type="hidden" value="<?=$suspend_sale ? $suspend_sale->order_tax_id : ($old_sale ? $old_sale->order_tax_id : $Settings->default_tax_rate2);?>" id="postax2">
                                <input name="discount" type="hidden" value="<?=$suspend_sale ? $suspend_sale->order_discount_id : ($old_sale ? $old_sale->order_discount_id : '');?>" id="posdiscount">
                                <input name="shipping" type="hidden" value="<?=$suspend_sale ? $suspend_sale->shipping : ($old_sale ? $old_sale->shipping :  '0');?>" id="posshipping">
                                <input type="hidden" name="rpaidby" id="rpaidby" value="cash" style="display: none;"/>
                                <input type="hidden" name="total_items" id="total_items" value="0" style="display: none;"/>
                                <input type="submit" id="submit_sale" value="Submit Sale" style="display: none;"/>
                            </div>
                        </div>

                    </div>
                    <?php echo form_close(); ?>
                    
                    <div id="cp">
                    	
                        
                        <div  class="col-md-12" id="popup_id_s"><div class="ad12"></div> <div class="v12"></div></div>
                    	<div id="category-list">
						<?php
                            
                            foreach ($categories as $category) {
								if($this->Settings->user_language == 'khmer'){
									if(!empty($category->khmer_name)){
										$category_name = $category->khmer_name;
									}else{
										$category_name = $category->name;
									}
								}else{
									$category_name = $category->name;
								}
                                	echo "<button id=\"category-" . $category->id . "\" type=\"button\" value='" . $category->id . "' class=\"btn-prni category\" ><span>" . $category_name . "</span></button>";
								
                            }
                           
                        ?>
                    </div>
                     
                        
                       <div id="subcategory-list" class="carousel">
                       <div id="scroller">
						<?php
                            if (!empty($subcategories)) {
                               
                                foreach ($subcategories as $category) {
									
										if($this->Settings->user_language == 'khmer'){
											
											if(!empty($category->khmer_name)){
												
												$subcategory_name = $category->khmer_name;
											}else{
												$subcategory_name = $category->name;
											}
										}else{
											$subcategory_name = $category->name;
										}
									   if($this->pos_settings->subcategory_display == 0){
                                   		 $subhtml = "<button id=\"subcategory-" . $category->id . "\" type=\"button\" value='" . $category->id . "' class=\"btn-prni subcategory slide\" ><img src=\"assets/uploads/thumbs/" . ($category->image ? $category->image : 'no_image.png') . "\" class='img-rounded' />";
                                        }else{
                                            $subhtml = "<button id=\"subcategory-" . $category->id . "\" type=\"button\" value='" . $category->id . "' class=\"btn-img subcategory slide\" >";
                                        } 


										 if(strlen($subcategory_name) < 20){		
						
											$subhtml .= "<span class='name_strong'>" .$subcategory_name. "</span>";
										}else{
											$subhtml .= "<marquee class='name_strong' behavior='alternate' direction='left' scrollamount='1'>&nbsp;&nbsp;" .$subcategory_name. "&nbsp;&nbsp;</marquee>";
										}
										  $subhtml .=  "</button>";
										 
										 echo $subhtml;										 
									
                                }
                            }
                        ?>
                        </div>
                    </div>
                        
                    
                        <div id="cpinner">
                            <div class="quick-menu">
                                <div id="proContainer">
                                    <div id="ajaxrecipe">
                                        <div id="item-list">
                                            <?php echo $recipe; ?>
                                        </div>
                                        
                                        <div class="btn-group btn-group-justified pos-grid-nav">
                                            <div class="btn">
                                                <button style="z-index:10002;position: absolute;left: -25px;top: -100px;" class="btn btn-primary pos-tip" title="<?=lang('previous')?>" type="button" id="previous">
                                                    <i class="fa fa-chevron-left"></i>
                                                </button>
                                            </div>
                                            <?php if ($Owner || $Admin || $GP['sales-add_gift_card']) {?>
                                            <!-- <div class="btn-group">
                                                <button style="z-index:10003;" class="btn btn-primary pos-tip" type="button" id="sellGiftCard" title="<?=lang('sell_gift_card')?>">
                                                    <i class="fa fa-credit-card" id="addIcon"></i> <?=lang('sell_gift_card')?>
                                                </button>
                                            </div> -->
                                            <?php }
                                            ?>
                                            <div class="btn">
                                                <button style="z-index:10004;position: absolute;right: 0px;top: -100px;" class="btn btn-primary pos-tip" title="<?=lang('next')?>" type="button" id="next">
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
											</div>
											
											<div class="tab-pane <?php if($tableactive ==1 && empty($this->input->get('split')) && empty($sprequest) && $tablecancelStatus !=1){ echo 'active';  } ?>" id="bill2">
												<div class="current_table_order">
			<div class="container-fluid">
				<div class="row">
				<div class="col-sm-7 col-xs-12">
				<div id="ordertable_box">
					<!--<div class="col-xs-12 kitchen_section">    
						<ul>
							<?php if($this->sma->actionPermissions('dinein_orders')){  ?>
							<li><a href="<?php echo base_url().'admin/pos/order_table'; ?>" class="active" ><?=lang('dine_in')?></a></li>
							<?php } ?>

							<?php if($this->sma->actionPermissions('takeaway_orders')){  ?>
							<li><a href="<?php echo base_url().'admin/pos/order_takeaway'; ?>" ><?=lang('take_away')?></a></li>
							<?php } ?>

							<?php if($this->sma->actionPermissions('door_delivery_orders')){  ?>
							<li><a href="<?php echo base_url().'admin/pos/order_doordelivery'; ?>" ><?=lang('door_delivery')?></a></li>
							<?php } ?>
							<?php if($this->Settings->bbq_enable){  ?>
							<li><a href="<?php echo base_url().'admin/pos/order_bbqtable'; ?>"><?=lang('BBQ')?></a></li>
							<?php } ?>       
						</ul>    
					</div>  -->
					<div class="table_list col-xs-12" style="padding: 0px;">
						<?php
						$table_id = !empty($this->input->get('table')) ? $this->input->get('table') : '';
                         if(!empty($table_id)){
						$tables = $this->site->GetALlOrdersTableList($table_id);
						if(!empty($tables)){
						?>
						<ul class="col-xs-12 ul_main" style="padding: 0px;">
							<?php
							foreach($tables as $table){
							   if($this->site->checkTableStatus($table->id) == FALSE)
							   {
							?>
							<li class="col-xs-12 li_main" style="padding: 0px;">

								<div class="table_head">
									<img src="<?=$assets?>images/order-table.png" alt="">
									<span class="odr_name"><?php echo $table->name; ?></span>
								</div>

								<?php if($this->sma->actionPermissions('new_split_create')){ ?>
								<div class="newsplit">
									 <a href="<?=admin_url('pos/consolidate').'/?order=1&table='.$table->id.'&spr=1'?>"> <button   class="btn btn-success pull-right newsplit"><?php echo lang("new_split") ?></button></a>                               
								</div>
								<?php } ?>

								<div style="clear:both;"></div>
								<?php
								$splitorder = $this->site->GetALlSplitsFromOrders($table->id);
								if(!empty($splitorder)){
								?>
								<div class="row">
								<ul class="col-xs-12" style="padding: 0px;">
									<?php

								   /* echo "<pre>";
									print_r($table->split_order);die;*/
									foreach($splitorder as $split_order){

										if($this->site->splitCheckSalestable($split_order->split_id) == FALSE){
											$count_item = $this->site->splitCountcheck($split_order->split_id);
											$dineinbbqboth = $this->site->dineinbbqbothCheck($split_order->split_id);

									?>
									<div class="row" style="margin-bottom: 15px;">

										<li class="col-xs-6 text-left split">
										   <h2 style="margin: 10px 0px 0px;"> 
										   <?php if($this->sma->actionPermissions('change_multiple_status')){ ?>
										   <label class="control control--checkbox" style="left:15px; top:2px;">
												<input type="checkbox" class="multiple_check multiple_<?php echo $split_order->split_id; ?>" data-order="<?php echo $split_order->split_id; ?>">
												<div class="control__indicator"></div>
											</label>
											&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											<?php } ?>

											<?php echo $split_order->split_id.' ('.$split_order->name.')'; ?></h2>


										</li>
										<?php
										if($dineinbbqboth == FALSE){
										?>
										<li class="col-xs-6 text-right">
										
										
										<?php if($pos_settings->merge_bill == 1) {  ?> 
												<span split="<?php echo $split_order->split_id; ?>" table_id ="<?php echo $split_order->table_id; ?>" class="btn btn-info merge_bill"><?php echo lang("merge_bill") ?></span>
											<?php } ?>
										
										 <?php if($pos_settings->table_change == 1) {?> 
											<span split="<?php echo $split_order->split_id; ?>" class="btn btn-info change_table"><?php echo lang("change_table") ?></span>
											<?php } ?> 
										 <?php //if($pos_settings->table_change == 1) {?> 
											<span split="<?php echo $split_order->split_id; ?>" class="btn btn-info change_customer"><?php echo lang("change_customer") ?></span>
											<?php // } ?> 

											

										</li>
										<?php
										}
										?>

										<li class="col-xs-12 text-right new_split" style="margin-bottom: 5px;">
										<script>            
										$(document).ready(function () {

												<?php
													$current_time = date('Y-m-d H:i:s');
													$created_time = $split_order->session_started;

													// $diff = strtotime($current_time) -  strtotime($created_time);
													$diff1 = (strtotime($current_time) -  strtotime($created_time));
													$limit_time = $this->Settings->default_preparation_time;
													if($diff >= $limit_time)
												   {
													$diff = 0; 
												   }
												   else{
													 $diff = $limit_time - $diff; 
												   }

												?>

												var clock;
												clock = $('.clock_<?php echo $split_order->split_id ?>').FlipClock(<?php echo $diff1 ?>,{  
													clockFace: 'HourlyCounter', 
													autoStart: true,
													// countdown: true, 
												}); 
											});

										</script>



										 <span href="javascript:void(0)" class="clock_<?php echo $split_order->split_id;?>" style="margin:0px;left: 50%;top: 56px;" start_time="<?php echo $split_order->session_started;  ?>"></span>

										 <!-- <span class="btn btn-info">10.10.00</span> -->
											<?php if($this->sma->actionPermissions('new_order_create')){ ?>


											<a href="<?=admin_url('pos/consolidate').'/?order=1&table='.$table->id.'&split='.$split_order->split_id.'&same_customer='.$split_order->customer_id.''?>"> <button  class="btn btn-info"><?php echo lang("order_item") ?></button></a> 
											<?php } ?>

								<button class="btn btn-danger" id="order_cancel_<?php echo $table->id;  ?>"  OnClick="send_kot('<?php echo $split_order->split_id;  ?>');" ><?php echo lang("kot_print") ?></button>

								<button class="btn btn-danger" id="order_cancel_<?php echo $table->id;  ?>"  OnClick="CancelAllOrderItems('<?php echo $table->id;  ?>','<?php echo $this->GP['pos-cancel_order_remarks'];?>','<?php echo $split_order->split_id;?>');" <?php if($this->sma->actionPermissions('cancel_order_items')){ echo ''; }else{  echo 'disabled'; }  ?> ><?php echo lang("cancel_all") ?></button>
											<?php
											if($dineinbbqboth == FALSE){
											?>
											<?php 


											$billgenrator_check = $this->pos_settings->default_billgenerator;

											if($billgenrator_check == 0){
											$orderstatus = $this->site->getOrderStatus($split_order->split_id);

											if($orderstatus == TRUE) 
											{?>
											  <button   OnClick="bilGenerator(<?php echo $table->id;  ?>, '<?php echo $split_order->split_id;  ?>', '<?php echo $count_item; ?>');" class="btn btn-warning" id="main_split_<?php echo $split_order->split_id;  ?>" <?php if($this->sma->actionPermissions('bil_generator')){ echo ''; }else{  echo 'disabled'; }  ?>><?php echo lang("bill_generator") ?></button>
											  <input type="hidden" id="count_item" value="<?php echo $count_item; ?>">

											<?php
											} 

										   }
										   else{
											?>
											<button OnClick="bilGenerator(<?php echo $table->id;  ?>, '<?php echo $split_order->split_id;  ?>', '<?php echo $count_item; ?>');" class="btn btn-warning" id="main_split_<?php echo $split_order->split_id;  ?>" <?php if($this->sma->actionPermissions('bil_generator')){ echo ''; }else{  echo 'disabled'; }  ?>><?php echo lang("bill_generator") ?></button>
											 <input type="hidden" id="<?php echo $split_order->split_id;?>_count_item" value="<?php echo $count_item; ?>">

											 <!--  <input type="hidden" id="count_item" value="<?php echo $count_item; ?>"> -->
											  <?php
											} ?>

											<?php
											}
											?>

										</li>
										<div style="clear:both;"></div>
										<?php
										$orders =$this->site->GetALlSplitsOrders($split_order->split_id,$table->id);
										if(!empty($orders)){
										?>
										<li class="col-xs-12 ">
											<ul class="col-xs-12 item_list">
												<?php
												foreach($orders as $order){
												?>
												<li class="col-xs-6 text-left waiter">
												   <?php echo $order->reference_no; ?>
												</li>
												<li class="col-xs-6 text-right order_status ">
													<span><?=lang('status')?> : <small><?php echo $order->order_status;  ?></small></span>
													<?php

													$allCancelorders = $this->site->allOrdersCancelStatus($order->id);

													if($allCancelorders == TRUE){
													?>
													<button type="button" class="btn btn-warning waiter_cancel_order" name="waiter_cancel_order" value="<?php echo $order->id; ?>"><?php echo lang("hide") ?></button>
													<?php
													}
													?>
												</li>

												<div style="clear:both;"></div>
												 <hr>
												 <?php
												 $split_order_items = $this->site->GetALlSplitsOrderItems($order->id);
												 if(!empty($split_order_items)){
												 ?>
												 <div class="row">
												<li class="col-xs-12">
													<div class="row">
													<ul class="col-xs-12 gbbgb">
														<?php

														$status_disabled_array = array('Served', 'Inprocess', 'Preparing', 'Closed');
														foreach($split_order_items as $item){

															$addons = $this->site->getAddonByRecipeidAndOrderitemid($item->recipe_id, $item->id);

														?>
														<li class="col-xs-6 value_padd <?php if(!in_array($item->item_status, $status_disabled_array)){ echo 'itm_padd'; } ?> ">
															<div class="col-xs-2"><img src="<?php echo site_url().'assets/uploads/thumbs/'.$item->image; ?>" alt="" height="70px" width="70px" ></div>
										<div class="col-xs-10">
											 <h3>
											<?php
											if($this->Settings->user_language == 'khmer'){
												if(!empty($item->khmer_name)){
													$recipe_name = $item->khmer_name;
												}else{
													$recipe_name = $item->recipe_name;
												}
											}else{
												$recipe_name = $item->recipe_name;
											}
											?>

                                           <?php $variant = '';
                                            if(!empty($item->variant) || ($item->variant!=0)) {
                                                /*$vari = explode('|',$item->variant);*/
                                                $vari = $item->variant;
                                                $variant = '[<span class="pos-variant-name">'.$vari.'</span>]';
                                            } ?>
                                            <?php echo $recipe_name.$variant; ?> <span>( x <?php echo $item->quantity; ?>)</span>
					     </h3>
										</div>
															<div class="col-xs-8">
											 					<div class="col-xs-6">



																   <!-- <a href="javascript:void(0)"><small>Notes:</small> <img src="<?=$assets?>images/small-img.png" alt=""></a>-->
																   <?php $sub_total = $this->sma->formatMoney($item->subtotal - $item->manual_item_discount);
																   /*var_dump($item->subtotal);
																   var_dump($item->manual_item_discount);*/
																	?>
																<button class="btn btn-warning" style="margin:0px;"><?php echo $sub_total; ?></button>
																</div>
																<div class="col-xs-6" style="float: right;padding-right: 1px;padding-left: 8px;">
																<?php 
																$color ='';
																if($item->item_status =='Inprocess'){
																  $color ='text-inprocess';
																}
																elseif($item->item_status =='Preparing')
																{
																	$color ='text-preparing';
																}
																elseif($item->item_status =='Ready')
																{
																	 $color ='text-ready';
																}
																elseif($item->item_status =='Cancel')
																{
																	 $color ='text-cancel';
																}

																/*echo $item->item_status;
																echo $color;*/
																?>
																<b class="<?php echo $color;?>" style="float: left;"><?php echo ($item->item_status=='Cancel') ?'Cancelled':$item->item_status; ?></b>
																</div>

										<p class="text-left text-danger" style="min-height:0px;margin-top: 10%">
                                             <?php                                             
                                            if(!empty($addons)){
                                            ?>
                                                <p class="add_on_s">Addons : 	</p>
                                                <?php
                                                foreach($addons as $addons_row){
					              	              echo ' <h3>        '.$addons_row->addon_name.' <span>('.$addons_row->qty.'X'.$addons_row->price.')</span>
					                                   </h3><div class="col-xs-6"><button class="btn btn-primary" style="margin:0px;" tabindex="-1">'.$this->sma->formatMoney($addons_row->subtotal).'</button><div class="col-xs-6" style="float: right;padding-right: 1px;padding-left: 8px;"><b class="text-inprocess" style="float: left;">Inprocess</b></div></div><br>';
                                                }
                                                ?>
                                                <?php
                                            }
                                            ?>
                                            </p>			
																<?php
																if($item->buy_id != 0 && $item->total_get_quantity !=0){
																?>
																<p class="text-left text-warning" style="min-height:0px;">
																<?php $get_item =  $this->site->getrecipeByID($item->get_item) ?>
																Buy <?php echo $item->buy_quantity; ?> Get <?php echo $item->get_quantity ?> (<?php echo $get_item->name; ?> X <?php echo $item->total_get_quantity; ?>)
																</p>
																<?php
																}
																?>
															</div>
															<div class="col-xs-2 text-right">
																<?php
																if(!in_array($item->item_status, $status_disabled_array)){
																?>
																<?php 
																$style = 'toshow';
																if($item->item_status !='Cancel')
																{
																   /* $style ='toHide';*/

																?>
																 <?php if($this->sma->actionPermissions('change_single_status')){ ?>
																<label class="control control--checkbox <?php echo $style;  ?>">

																<input type="checkbox" name="status_update_<?php echo $split_order->split_id; ?>[]" value="<?php echo $item->id;  ?>" title="<?php echo $item->item_status; ?>" data-type="<?php echo $item->id;  ?>" data-split="<?php echo $split_order->split_id; ?>" class="multiple_status status_<?php echo $split_order->split_id; ?>">
																<div class="control__indicator"></div>
																</label>
																<?php } ?>

																<?php } ?>
																<?php
																}
																?>

																	<?php

																	$cancel_report = $this->site->getTableCancelstatus($item->id);
																	if($cancel_report == FALSE){
																	?>
																	<?php if($item->item_status!='Cancel') : ?>
																	<button class="btn btn-danger" id="item_cancel_<?php echo $item->id;  ?>"  OnClick="CancelOrderItem('<?php echo $item->item_status;  ?>', '<?php echo $item->id;  ?>', '<?php echo $split_order->split_id;  ?>','<?php echo $this->GP['pos-cancel_order_remarks'];?>','<?php echo $item->quantity; ?>');" <?php if($this->sma->actionPermissions('cancel_order_items')){ echo ''; }else{  echo 'disabled'; }  ?> ><?php echo lang("cancel") ?></button>
																	<?php endif; ?>
																	<?php
																	}else{
																	?>
																	<a href="javascript:void(0)" data-toggle="tooltip" data-placement="bottom" data-original-title="" aria-describedby="tooltip" title="<?php echo $item->order_item_cancel_note; ?>" class="hide orderCancelled"><br><small>This item is cancelled </small> <img src="<?=$assets?>images/small-img.png" alt=""></a>
																	<?php																
																	}
																	?>






															</div>
														</li>
														<?php
														}
														?>
													</ul>
											
												</li>
												
												
												
												
												</div>
												
												<?php
												 }
												?>
												<?php
												}
												?>
												<button data-status="Ready" data-id="" data-split-id = "<?php echo $split_order->split_id; ?>"  type="button" class="btn btn-success kitchen_status preparing_<?php echo $split_order->split_id; ?> pull-right" style="display:none;"><?php echo lang("served") ?></button>
													<button data-status="Served" data-id=""  data-split-id = "<?php echo $split_order->split_id; ?>"   type="button" class="btn btn-success kitchen_status ready_<?php echo $split_order->split_id; ?> pull-right" style="display:none;" ><?php echo lang("closed") ?></button>
											</ul>
										</li>

										<?php
										}
										?>
										<div style="clear:both;"></div>

									  </div>
									 <?php
										}else{

											echo '<div class="row">
											<li class="col-xs-12 text-left split">
										   <h2> '.$split_order->split_id.' ('.$split_order->name.')'.'</h2>
										</li>
											<li class="col-xs-12 ">
											<ul class="col-xs-12 item_list text-center" style="margin:0px 0px 10px;">
											<h2 class="text-danger">'.lang('bil_generator_msg1').'</h2>
											</ul>
											</li></div>';	

										}
									}
									 ?> 
								</ul>
								</div>
								<?php
								}
								?>
							</li>
							<?php
							   }
							}
							?>
						</ul>
						<?php
						}
						}else{
						?>
						<div class="col-sm-6 col-sm-offset-3 col-xs-12 order_cancel_data alert-danger fade in">
						   <?=lang('no_record_found')?>
						</div>
						<?php
						}
						?>
					</div>     
					<div class="modal fade in" id="table-change-Modal" tabindex="-1" role="dialog" aria-labelledby="table-change-ModalLabel" aria-hidden="true" style="background: rgba(0,0,0,0.5);">
						<div class="modal-dialog modal-sm">
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close closemodal" data-dismiss="modal" aria-hidden="true"><i
											class="fa fa-2x">&times;</i></button>
									<h4 class="modal-title" id="table-change-ModalLabel"><?=lang('table_change')?></h4>
								</div>
								<div class="modal-body">
								<input type="hidden" name="change_split_id" id="change_split_id">
								<label><?=lang('tables')?></label>
									<select style="display: "  name="changed_table_id" class="form-control pos-input-tip changed_table_id" id="changed_table_id">
									<option value="0">No</option>
										<?php
										foreach ($avil_tables as $tables) {

										?>
										<option value="<?php echo $tables->id; ?>" data-id="<?php echo $tables->id; ?>"><?php echo $tables->name; ?></option>
										<?php
										}
										?>
									</select>
								</div>
								<div class="modal-footer">
									<button type="button" id="OrderChangeTable" class="btn btn-primary">Submit</button>
								</div>
							</div>
						</div>
					</div>
					<div class="modal fade in" id="splits-merge-Modal" tabindex="-1" role="dialog" aria-labelledby="splits-merge-ModalLabel" aria-hidden="true" style="background: rgba(0,0,0,0.5);">
						<div class="modal-dialog modal-sm">
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close closmergeemodal" data-dismiss="modal" aria-hidden="true"><i
											class="fa fa-2x">&times;</i></button>
									<h4 class="modal-title" id="splits-merge-ModalLabel"><?=lang('order_merge')?></h4>
								</div>
								<div class="modal-body">
								<input type="hidden" name="merge_split_id" id="merge_split_id">
								<input type="hidden" name="merge_table_id" id="merge_table_id">
								</div>
								 <div class="discount-container">
										<div class="merge-group-list">
										</div>
									</div>
								<div class="modal-footer">
									<button type="button" id="Mergesplits" class="btn btn-primary">Submit</button>
								</div>
							</div>
						</div>
					</div>
					<div class="modal fade in" id="customer-change-Modal" tabindex="-1" role="dialog" aria-labelledby="customer-change-ModalLabel" aria-hidden="true" style="background: rgba(0,0,0,0.5);">
						<div class="modal-dialog modal-sm">
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close closemodal" data-dismiss="modal" aria-hidden="true"><i
											class="fa fa-2x">&times;</i></button>
									<h4 class="modal-title" id="customer-change-ModalLabel"><?=lang('change_customer')?></h4>
								</div>
								<div class="modal-body">
									<div style="position: absolute; <?=$Settings->user_rtl ? 'right:-9999px;' : 'left:-9999px;';?>"><?php echo form_input('test', '', 'id="test" class="kb-pad"'); ?></div>

								<input type="hidden" name="change_split_id" id="change_split_id">
								<label><?=lang('customers')?></label>
								 <?php
									echo form_input('customer', (isset($_POST['customer']) ? $_POST['customer'] : ""), 'id="poscustomer1" data-placeholder="' . $this->lang->line("select") . ' ' . $this->lang->line("customer") . '" required="required" class="form-control pos-input-tip" style="width:100%;"');
								?>
								   <!--  <select style="display: "  name="changed_table_id" class="form-control pos-input-tip changed_customer_id" id="changed_customer_id">
									<option value="0">No</option>
										<?php
										//foreach ($avil_customers as $customer) {

										?>
										<option value="<?php echo $customer->id; ?>" data-id="<?php echo $customer->id; ?>"><?php echo $customer->name; ?></option>
										<?php
									//	}
										?>
									</select> -->
								</div>
								<div class="modal-footer">
									<button type="button" id="OrderChangeCustomer" class="btn btn-primary">Submit</button>
								</div>
							</div>
						</div>
					</div>
				</div></div>
				<div class="col-sm-5 col-xs-12">
					
					<div class="modal fade in" id="paymentModal" tabindex="-1" data-backdrop="static"   data-keyboard="false" role="dialog" aria-labelledby="payModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true"><i
                            class="fa fa-2x">&times;</i></span><span class="sr-only"><?=lang('close');?></span></button>
		<h4 class="modal-title" id="payment-customer-name"></h4>
		<h4 class="modal-title" id="payModalLabel"><?=lang('make_payment');?>(<span id="new_customer_name"></span>)</h4>
            <div style="position: absolute; <?=$Settings->user_rtl ? 'right:-9999px;' : 'left:-9999px;';?>"><?php echo form_input('test', '', 'id="test" class="kb-pad"'); ?></div>

            </div>
            <div class="modal-body" id="payment_content" >
                <div class="btn btn-warning pull-right">
                    <a href="<?=admin_url('customers/new_customer');?>" id="add-customer" class="external" data-toggle="modal" data-target="#myModal">
                        Add New Customer <i class="fa fa-plus-circle" id="addIcon" style="font-size: 1.5em; "></i>
                    </a>
                </div>
                
             <?php $attrib = array( 'autocomplete'=>"off" ,'role' => 'form', 'id' => 'pos-payment-form');
             echo admin_form_open("pos/paymant_all", $attrib);
             $type = $this->input->get('type');
             ?>
                <div class="row">
                    <?php // if ($pos_settings->taxation_report_settings == 1) { ?>
                       <div class="form-group" style="padding: 5px 10px;">
                            <div class="row">
                                <div class="col-sm-6 taxation_settings">
                                    <label class="control-label" for="taxation_settings"><?= lang("print_option"); ?></label>
                                    <input type="radio" value="0" class="checkbox" name="taxation" checked ="checked">
                                    <label for="switch_left">Print</label>
                                    <input type="radio" value="1" class="checkbox" name="taxation">
                                    <label for="switch_right">Don't Print</label>                    
                                </div>
                            </div>
                        </div>
                    <?php  //} ?>
                <input type="hidden" name="type" class="type" value="<?php echo $type;?>"/>
                <input type="hidden" name="balance_amount" class="balance_amount" value=""/>
                <input type="hidden" name="due_amount" class="due_amount" value=""/>
                    <div class="col-md-12 col-sm-12 text-center">
                        <?php if ($Owner || $Admin || !$this->session->userdata('biller_id')) { ?>
                            <div class="form-group"  style="margin-bottom: 5px;">
                                <?=lang("biller", "biller");?>
                                <?php
                                    foreach ($billers as $biller) {
                                        $btest = ($biller->company && $biller->company != '-' ? $biller->company : $biller->name);
                                        $bl[$biller->id] = $btest;
                                        $posbillers[] = array('logo' => $biller->logo, 'company' => $btest);
                                        if ($biller->id == $pos_settings->default_biller) {
                                            $posbiller = array('logo' => $biller->logo, 'company' => $btest);
                                        }
                                    }
                                    echo form_dropdown('biller', $bl, (isset($_POST['biller']) ? $_POST['biller'] : $pos_settings->default_biller), 'class="form-control" id="posbiller" required="required"');
                                ?>
                            </div>
                        <?php } else {
                                $biller_input = array(
                                    'type' => 'hidden',
                                    'name' => 'biller',
                                    'id' => 'posbiller',
                                    'value' => $this->session->userdata('biller_id'),
                                );

                                echo form_input($biller_input);

                                foreach ($billers as $biller) {
                                    $btest = ($biller->company && $biller->company != '-' ? $biller->company : $biller->name);
                                    $posbillers[] = array('logo' => $biller->logo, 'company' => $btest);
                                    if ($biller->id == $this->session->userdata('biller_id')) {
                                        $posbiller = array('logo' => $biller->logo, 'company' => $btest);
                                    }
                                }
                            }
                        ?>
                        <input type="hidden" name="new_customer_id" id="new_customer_id" value="0">
                        <input type="hidden" name="eligibity_point" id="eligibity_point" value="<?= $eligibity_point ?>">
                        <input type="hidden" name="bill_id" id="bill_id" class="bill_id" />
                        <input type="hidden" name="order_split_id" id="order_split_id" class="order_split_id" />
                        <input type="hidden" name="sales_id" id="sales_id" class="sales_id" />
            			<input type="hidden" name="credit_limit" id="credit_limit" class="credit_limit" />
            			<input type="hidden" name="company_id" id="company_id" class="company_id" />
            			<input type="hidden" name="customer_type" id="customer_type" class="customer_type" />
                        <input type="hidden" name="customer_id" id="customer_id" class="customer_id"/>
            			
                        <input type="hidden" name="total" id="total" class="total" />
                        <input type="hidden" name="loyalty_available" id="loyaltyavailable" class="loyaltyavailable" />
                        <input type="hidden" name="loyaltypoints" id="loyaltypoints" class="loyaltypoints" />
                        <input type="hidden" name="loyalty_used_points" id="loyalty_used_points" class="loyalty_used_points" />
                        <div class="form-group bill_sec_head" style="color: #1F73BB!important;font-size: 20px!important;align-self: center;margin-bottom: 5px;">
                            <button type="button" class="btn btn-danger" id="reset" style="cursor: pointer!important;"><label style="margin-top: 0px !important;"><?=lang('reset')?> </label></button>
                               <?=lang("bill_amount", "bill_amount");?>
                               <?php 
                               $default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
                               ?>
                               <span id="bill_amount" >&#x20b9;</span>
                        </div>
                        <div id="payment-list">
                           <?php   
                           $j =0;
                            $paymentMethods = $this->site->getAllPaymentMethods();                                                         
                                foreach ($paymentMethods as $k => $method) { 
                                    $j++;
                                      echo "<button id=\"payment-" . $method->payment_type . "\" type=\"button\" value='" . $method->payment_type . "' class=\"btn-prni payment_type \" data-index='" . $method->payment_type. "' data_id='" . $j. "' ><span>" . $method->display_name . "</span></button>";
                                ?>
                                     <input name="paid_by[]" type="hidden" id="payment_type_<?php echo $method->payment_type; ?>" value="<?php echo $method->payment_type; ?>" class="form-control" autocomplete="off"  />
                            <?php } ?>
                            <div id="sub_items" style="margin-top: 30px;min-height: 165px;">

                               

                                <div class="form-group col-md-6">
                                    <!-- <label><?=lang('customer_name','customer_name')?></label> -->
                                     <input readonly type="hidden" id="loyalty_card_customer_name"  readonly="" class="pa form-control loyalty_card_customer_name"  autocomplete="off" />
                                </div> 
                                <div class="clearfix"></div>


                            <?php
                             $j =0;
                            $paymentMethods = $this->site->getAllPaymentMethods(); 
                            $display = "block";

                            foreach ($paymentMethods as $key => $method) {   $j++; 
                                if($method->payment_type =='cash'){
                                    $display = "block";
                                }else{
                                    $display = "none";
                                }
                                ?>
                                   <div class="col-sm-12 <?=$method->payment_type?>">
                                    <!-- <span style="color: green;font-size: 20px;"><?=$method->payment_type; ?></span> -->
                                    <?php if($method->payment_type=="loyalty") : ?>
				    <div class="form-group col-md-6">
					<label><?=lang('search_loyalty_customer','search_loyalty_customer')?></label>
					<?php
					    echo form_input('loyalty_customer', (isset($_POST['loyalty_customer']) ? $_POST['loyalty_customer'] : ""), 'id="loyalty_customer" data-placeholder="' . $this->lang->line("select") . ' ' . $this->lang->line("loyalty_customer") . '" required="required" class="form-control pos-input-tip" autocomplete="off" style="width:100%;"');
					?>
				    </div>
				    <?php endif; ?>
				    <div class="col-sm-6 CC_<?=$method->payment_type.$j?>" id="CC_<?=$method->payment_type?>" style="display: none">
                                         <div class="form-group col-md-12">
                                                <label><?=lang('card_no')?> </label>
								   			<input name="cc_no[]" type="text" maxlength="20" id="pcc_no_<?=$method->payment_type?>"
                                                    class="form-control kb_pad_length cc_no" placeholder="<?=lang('cc_no')?>"/>
                                        </div> 
                                        <div class="form-group col-md-12">
											<label><?=lang('card_exp_date')?> </label>
										   	<input name="card_exp_date[]" type="text" maxlength="6" value ="" id="card_exp_date_<?=$method->payment_type?>"
                                                    class="form-control crd_exp datetime" placeholder="MM/YYYY"/>
                                        </div> 
                                        <div class="clearfix"></div>                                        
                                    </div>  
				    <?php
                                    foreach($currency as $currency_row){
                                        
                                        $default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
                                        
                                        if($currency_row->code == $default_currency_data->code){
                                    ?>                                    
                                    <div class="col-sm-6 base_currency_<?=$method->payment_type.$j?>" id="base_currency_<?=$method->payment_type?>" style="display:<?php echo $display;?>; "> 
                                        <div class="form-group"  style="margin-bottom: 5px;">
                                            <label><?=lang('amount')?> <?=$currency_row->code; ?></label>
                                            <input name="amount_<?php echo $default_currency_data->code; ?>[]" type="text" id="amount_<?php echo $default_currency_data->code; ?>_<?=$method->payment_type?>" data-rate="<?=$currency_row->rate; ?>" data-code="<?=$currency_row->code; ?>" data-id="<?=$currency_row->id; ?>"  class="pa form-control  amounts kb-pad amount_base" payment-type="<?=$method->payment_type?>" autocomplete="off"  />
                                        </div>
                                    </div>
                                    <?php }else { ?>
                                    <div class="col-sm-6 multi_currency_<?=$method->payment_type.$j?>" id="multi_currency_<?=$method->payment_type?>" style="display:<?php echo $display;?>; ">
                                        <div class="form-group" >
                                             <label><?=lang('amount')?> <?=$currency_row->code; ?></label>
                                            <input name="amount_<?=$currency_row->code; ?>[]" type="text" id="amount_<?=$currency_row->code; ?>_<?=$method->payment_type?>" data-rate="<?=$currency_row->rate; ?>" data-code="<?=$currency_row->code; ?>" data-id="<?=$currency_row->id; ?>"  class="pa form-control  amounts kb-pad amount_base" payment-type="<?=$method->payment_type?>"  autocomplete="off"/>
                                        </div>
                                    </div>
                                    <?php }   } ?>  

                                    <div class="clearfix"></div>                                    
                                       
                                   <div class="form-group lc_<?=$method->payment_type.$j?>" id="lc_<?=$method->payment_type?>" style="display: none">    
                                        <div class="form-group col-md-6">
                                            <label><?=lang('Points')?></label>
                                             <input name="paying_loyalty_points[]" type="text" id="loyalty_points_<?=$method->payment_type?>" idd="<?=$method->payment_type?>" class="pa form-control loyalty_points kb-pad"  autocomplete="off" />
                                        </div> 

                                        <div class="clearfix"></div>       
                                        <div id="lc_details_<?=$method->payment_type?>" style="color: red;"> </div>
                                        <div id="lc_reduem_<?=$method->payment_type?>" style="color: green;"></div>
                                    </div>
                                    <div class="clearfix"></div>
                                    <!-- <div class="col-sm-6 CC_<?=$method->payment_type.$j?>" id="CC_<?=$method->payment_type?>" style="display: none">
                                         <div class="form-group col-md-6">
                                                <label><?=lang('card_no')?> </label>
								   			<input name="cc_no[]" type="text" id="pcc_no_<?=$method->payment_type?>"
                                                    class="form-control kb-pad" placeholder="<?=lang('cc_no')?>"/>
                                        </div> 
                                        <div class="form-group col-md-6">
											<label><?=lang('card_exp_date')?> </label>
										   	<input name="card_exp_date[]" type="text" id="card_exp_date_<?=$method->payment_type?>"
                                                    class="form-control kb-pad" placeholder="<?=lang('card_exp_date')?>"/>
                                        </div> 
                                        <div class="clearfix"></div>                                        
                                    </div>  --> 
                                    <div style="margin-bottom: 10px"></div>
                                </div>                                  
                                <?php  } ?> 
                                </div>
                        </div>  
                                <div class="form-group" style="margin-bottom: 5px;">
                                    <div id="userd_tender_list">         
                                    </div>
                                </div>
                                <div class="col-md-12 col-sm-12">
                                <div class="form-group total_pay"  style="margin-bottom: 5px;">
                                    <table class="table table-condensed table-striped" style="margin-bottom: 0;">
                                        <tr>
                                            <td rowspan="4" class="total_paytd" style="width: 50%!important;text-align: center">
                                                &nbsp;<?=lang('total_pay')?>
                                            </td> 
                                        </tr>
                                         <?php
										 
                                            foreach($currency as $currency_row) { ?>
                                               <tr><td class="total_paytd" style="text-align: left;"><?php echo $currency_row->code; ?>&nbsp;:&nbsp;&nbsp;<span id="twt_<?php echo $currency_row->code; ?>">0.00</span>
                                                <input type="hidden" id="paid_amt_<?php echo $currency_row->code; ?>"></td></tr>
                                         <?php } ?>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-12 col-sm-12">   
                                <div class="form-group balance_pay"  style="margin-bottom: 5px;">
                                    <table class="table  table-condensed table-striped" style="margin-bottom: 0;">
                                        <tr>
                                            <td rowspan="4" class="balance_paytd" style="width: 50%!important;text-align: center">&nbsp;<?=lang('balance_pay')?></td> 
                                        </tr>
                                         <?php
                                            foreach($currency as $currency_row) { ?>
                                               <tr><td class="balance_paytd" style="text-align: left;"><?php echo $currency_row->code; ?>&nbsp;:&nbsp;&nbsp;<span id="balance_<?php echo $currency_row->code; ?>">0.00</span>
                                                   <input type="hidden" id="balance_amt_<?php echo $currency_row->code; ?>"></td></tr>
                                            <?php } ?>
                                    </table>
                                </div>  
                            </div>                                  
                            <div id="payments" class="payment-row" style="display: none">
                                <div class="well well-sm well_1" style="padding: 5px 10px;">
                                    <div class="payment">
                                        <div class="row">                        
                                        </div>
                                    </div>
                                </div>
                            </div>    
             <?php                
             echo form_hidden('remove_image','No');
             echo form_hidden('action', 'PAYMENT-SUBMIT');
             echo form_close();
             ?>
            <div class="modal-footer">
                <button type="button" class="btn btn-block btn-lg btn btn-info" id="submit-sale1"><?=lang('send');?></button>
            </div>
        </div>
    </div>
</div>
</div>
</div>
</div>
<div id="order_tbl"><span id="order_span"></span>
    <table id="order-table" class="prT table table-striped" style="margin-bottom:0;" width="100%"></table>
</div>

<div id="bill_tbl"><span id="bill_span"></span>
    <div id="bill_header"></div>
   <!--  <table id="bill-table" width="100%" class="prT table table-striped" style="margin-bottom:0;"></table> -->
   <div id="bill-total-table"></div>
    <!-- <table id="bill-total-table" class="prT table table table-striped " ></table> -->
    <span id="bill_footer"></span>
</div>

<!-- <div class="modal fade in" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true"></div>
<div class="modal fade in" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2"
     aria-hidden="true"></div>
<div id="modal-loading" style="display: none;">
    <div class="blackbg"></div>
    <div class="loader"></div>
</div> -->
<!--
<div class="modal fade in" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true" style="z-index:9999999999" data-backdrop="static" data-keyboard="false" >
</div>-->

<div class="modal" id="salesCancelorderModal" tabindex="-1" role="dialog" aria-labelledby="CancelorderModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close closemodal" data-dismiss="modal"><span aria-hidden="true">
                    <i class="fa fa-2x">&times;</i></span>
                    <span class="sr-only"></span>
                </button>
                <h4 class="modal-title" id="cmModalLabel"></h4>
            </div>
            <div class="modal-body" id="pr_popover_content">
                <div class="form-group">
                    <?= lang('remarks'); ?>
                    <?= form_textarea('remarks', '', 'class="form-control" id="salecancelremarks" style="height:80px;"'); ?>
                </div>
                <input type="hidden" id="sale_id" value=""/>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="salescancel_orderitem"><?=lang('send')?></button>
            </div>
        </div>
    </div>
</div>
<!------------------  billing block --------------------------------------------->
			 <div id="pos">                
                    <div id="orderbilling_box">       


<div class="tableright_sed col-xs-12" style="padding: 0px;">
 <div class="col-xs-12" style="padding: 0px;"> 
 
        
        <?php
		$tables = $this->site->GetALlOrdersTableList($table_id);
		
        if(!empty($sales)){
        ?>
        <ul>
            <?php
            foreach($sales as $sales_row){
                if($sales_row->sales_type_id == 1){
                    $img = 'dine_in.png';
                }elseif($sales_row->sales_type_id == 2){
                    $img = 'take_away.png';
                }elseif($sales_row->sales_type_id == 3){
                    $img = 'delivery.png';
                }
                $split_id = $sales_row->id;

            ?>
          <!--   <div style="clear:both; height:5px;"></div>
                          <div class="col-xs-12" style="padding: 0;"> -->
            <li class="col-md-12">
                <div class="row">

                    <div class="col-xs-6 billing_list btn-block order-biller-table order_biller_table">
                    <?php if($sales_row->sales_type_id == 1){ ?>
                    <p class="bil_tab_nam"><?php echo $sales_row->areaname.' / '.$sales_row->tablename; } ?></p>
                    <h2><?php echo $sales_row->reference_no; ?></h2>
                   
                        <?php
                        $cancel_sale_status = $this->site->CancelSalescheckData($sales_row->id);
                        if($cancel_sale_status == TRUE){
							if($this->sma->actionPermissions('bil_cancel')){ 
                        ?>
                        <div class="col-xs-12" style="padding: 0;">
                        <button type="button" class="btn btn3 padding3 cancel_bill btn-danger" id="">
                        &#10062;<?=lang('cancel_bill');?> 
                        </button>
                        <input type="hidden"  class="cancel_bill_id" value="<?php echo $sales_row->id; ?>">
                        </div>
                        <?php
							}
						
                        }
                        ?>
                    </div>
                    
                     <?php if(!empty($sales_row->bils)){
                        /*echo "<pre>";
                        print_r($sales_row->bils);die;*/
						$k=1;
                        foreach($sales_row->bils as $split_order){
                            if (count($split_order->id) > 0) {
                       		
                            ?>
                            <div class="col-xs-6" style="padding-right: 0px;">
                            <div class="payment-list-container">
                             <h2 class="order-heading" style="margin-top: 0px;"> <?=lang('sale_ref_no')?>: <?php echo $split_order->bill_sequence_number; ?></h2>
                             <div class="col-xs-12" style="padding: 0;">
                              <div class="btn-group btn-block">
                                <?php if($split_order->payment_status == null) { ?>
                                <button type="button" class="btn btn-success request_bil_new" data-item="payment" id="BNO<?php echo $split_order->bill_number; ?>"  <?php if($this->sma->actionPermissions('bil_payment')){ echo ''; }else{  echo 'disabled'; }  ?>>
                                    <i class="fa fa-money" ></i><?=lang('payment');?> 
                                </button>
				<?php if($this->Settings->rough_tender) {
				$RT_disabled = ($rough_tender = $this->site->isRoughTenderDone($split_order->id))?'disabled="disabled"':'';
				if($rough_tender){ ?>
				<?php foreach($rough_tender as $k => $rt_val) : ?>
					<?php if($rt_val->paid_by=="cash" || $rt_val->paid_by=="credit") : ?>
					<input type="hidden" class="rt-<?=$rt_val->paid_by?>" value="<?=$this->sma->formatDecimal($rt_val->pos_paid,2)?>">
					<?php elseif($rt_val->paid_by=="CC") : ?>
						<input type="hidden" class="rt-<?=$rt_val->paid_by?>" value="<?=$this->sma->formatDecimal($rt_val->pos_paid,2)?>">
						<input type="hidden" class="rt-card-no" value="<?=$rt_val->cc_no?>">
					<?php elseif($rt_val->paid_by=="loyalty") : ?>
						<input type="hidden" class="rt-<?=$rt_val->paid_by?>" value="<?=$rt_val->loyalty_points?>">
					<?php endif; ?>
				<?php endforeach; ?>
				<?php }
				?>
				<button type="button" class="btn btn-warning rough-tender-payment" data-item="rough-tender" id="RT-BNO<?php echo $split_order->bill_number; ?>" <?= $RT_disabled?> <?php if($this->sma->actionPermissions('bil_payment')){ echo ''; }else{  echo 'disabled'; }  ?>>
                                    <i class="fa fa-money" ></i><?=lang('rough_tender');?> 
                                </button>
				<?php } ?>
                                <?php }
                                  else{
                                    ?>
                                    <button disabled="" type="button" class="btn btn-success " >
                                        <?php  echo $split_order->payment_status; ?>
                                    </button>
                                   
                                    <?php
                                    }?>
                                <input type="hidden"  class="billid" value="<?php echo $split_order->id; ?>">

                                <input type="hidden"  class="order_split" value="<?php echo $sales_row->sales_split_id; ?>">

                                <input type="hidden"  class="salesid" value="<?php echo $split_order->sales_id; ?>">
                                <?php 
                                if ($split_order->tax_type == 0)
                                {
                                    $grandtotal = $split_order->total-$split_order->total_discount+$split_order->service_charge_amount;
                                }
                                else{
                                    $grandtotal = $split_order->total-$split_order->total_discount+$split_order->total_tax+$split_order->service_charge_amount;
                                }
                                  
                                ?>                                
                                <input type="hidden"  class="grandtotal" value="<?php echo $grandtotal; ?>">
                				<input type="hidden"  class="credit-limit" value="<?php echo $split_order->credit_limit; ?>">
                				<input type="hidden"  class="company-id" value="<?php echo $split_order->company_id; ?>">
                				<input type="hidden"  class="customer-type" value="<?php echo $split_order->customer_type; ?>">
						<input type="hidden"  class="customer-allow-loyalty" value="<?php echo $split_order->allow_loyalty; ?>">
                				<input type="hidden"  class="customer-id" value="<?php echo $split_order->customer_id; ?>">
                				<input type="hidden"  class="customer-name" value="<?php echo $split_order->customer_name; ?>">
                                <input type="hidden"  class="totalitems" value="<?php echo $split_order->total_items; ?>">
                                <input type="hidden"  class="loyalty_available" value="<?php  echo $this->site->getCheckLoyaltyAvailable($split_order->customer_id); ?>">
                            </div>
                            
                            </div>
                             <div style="clear:both; height:5px;"></div>
                          <div class="col-xs-12" style="padding: 0;">
                            <div class="btn-group-vertical btn-block">
                          
                               
								
                                 <button type="button"  class="btn btn-primary btn-block request_bil"  
                                 data-bil="req_<?=$k; ?>"id="bil" <?php if($this->sma->actionPermissions('bil_print')){ echo ''; }else{  echo 'disabled'; }  ?>>
                                <i class="fa fa-print" ></i><?=lang('sale_bill');?> 
                                </button>
                                
                                <input type="hidden"  class="billid_req" value="<?php echo $split_order->id; ?>">

                                <input type="hidden"  class="order_split_req" value="<?php echo $sales_row->sales_split_id; ?>">

                                <input type="hidden"  class="salesid_req" value="<?php echo $split_order->sales_id; ?>">
                                <?php 
                                if ($split_order->tax_type == 0)
                                {
                                    $grandtotal = $split_order->total-$split_order->total_discount;
                                }
                                else{
                                    $grandtotal = $split_order->total-$split_order->total_discount+$split_order->total_tax;
                                }
                                ?>
                                <input type="hidden"  class="grandtotal_req" value="<?php echo $grandtotal; ?>">

                                <input type="hidden"  class="totalitems_req" value="<?php echo $split_order->total_items; ?>">
                                
                               
                                <div id="req_<?=$k;?>">                            
                               <button type="button" data-sp="split_<?=$k;?>" class="btn btn-primary btn-block print_bill" value="<?php echo $split_order->id; ?>" style="height:40px; overflow:hidden; visibility:hidden;"  <?php if($this->sma->actionPermissions('bil_print')){ echo ''; }else{  echo 'disabled'; }  ?>>
                                <i class="fa fa-print" ></i><?=lang('sale_bill');?> 
                                </button>
                                 <input type="hidden" id="split_<?=$k;?>"  class="bill_print" value="<?php echo $split_order->id; ?>">
                                </div>
                                
                                
                            </div>
                            </div>
                            </div>
                        </div>                                                 
                            <?php 
                        }
                        $k++;
						}
                     }
                ?>
                </div>
                
            </li>
            <?php
            }
            ?>
        </ul>

        <?php
		
        }elseif(!empty($tables)){
        ?>
        <div class="col-sm-6 col-sm-offset-3 col-xs-12 order_cancel_data order_cancel_data_s alert-danger fade in"> <?=lang('no_bill')?> </div>
        <?php
        }else{
			 ?>
        <div class="col-sm-6 col-sm-offset-3 col-xs-12 order_cancel_data order_cancel_data_s alert-danger fade in"> <?=lang('order_not_placed_yet')?> </div>
        <?php
		}
        ?>
        <div>
</div>


<!-----------------   billing  block end ------------------------>






					
                    </div>                    
                    <div style="clear:both;"></div>
                </div>
                <div style="clear:both;"></div>		
					
				</div>
				<div class="clearfix"></div>
				</div>
			</div>
		</div>
											</div>
											<div class="tab-pane" id="bill3">
												!
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</section>
                    
                </div>
                <div style="clear:both;"></div>
            </div>
        </div>
    </div>
</div>

<!--       add billing           -->

 








<!--   end billing        --->

<!--             order table    ------------>

                         
                    



</div>
</div>
</div>
</div>
</div>









<!----------        order table                     ----------->

<?php
$this->load->view($this->theme . 'pos/pos_footer');
?>

<!--
<div class="modal fade in" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="payModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true"><i
                            class="fa fa-2x">&times;</i></span><span class="sr-only"><?=lang('close');?></span></button>
                <h4 class="modal-title" id="payModalLabel"><?=lang('finalize_sale');?></h4>
            </div>
            <div class="modal-body" id="payment_content">
                <div class="row">
                    <div class="col-md-10 col-sm-9">
                        <?php if ($Owner || $Admin || !$this->session->userdata('biller_id')) { ?>
                            <div class="form-group">
                                <?=lang("biller", "biller");?>
                                <?php
                                    foreach ($billers as $biller) {
                                        $btest = ($biller->company && $biller->company != '-' ? $biller->company : $biller->name);
                                        $bl[$biller->id] = $btest;
                                        $posbillers[] = array('logo' => $biller->logo, 'company' => $btest);
                                        if ($biller->id == $pos_settings->default_biller) {
                                            $posbiller = array('logo' => $biller->logo, 'company' => $btest);
                                        }
                                    }
                                    echo form_dropdown('biller', $bl, (isset($_POST['biller']) ? $_POST['biller'] : $pos_settings->default_biller), 'class="form-control" id="posbiller" required="required"');
                                ?>
                            </div>
                        <?php } else {
                                $biller_input = array(
                                    'type' => 'hidden',
                                    'name' => 'biller',
                                    'id' => 'posbiller',
                                    'value' => $this->session->userdata('biller_id'),
                                );

                                echo form_input($biller_input);

                                foreach ($billers as $biller) {
                                    $btest = ($biller->company && $biller->company != '-' ? $biller->company : $biller->name);
                                    $posbillers[] = array('logo' => $biller->logo, 'company' => $btest);
                                    if ($biller->id == $this->session->userdata('biller_id')) {
                                        $posbiller = array('logo' => $biller->logo, 'company' => $btest);
                                    }
                                }
                            }
                        ?>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-6">
                                    <?=form_textarea('sale_note', '', 'id="sale_note" class="form-control kb-text skip" style="height: 100px;" placeholder="' . lang('sale_note') . '" maxlength="250"');?>
                                </div>
                                <div class="col-sm-6">
                                    <?=form_textarea('staffnote', '', 'id="staffnote" class="form-control kb-text skip" style="height: 100px;" placeholder="' . lang('staff_note') . '" maxlength="250"');?>
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
                                                <?=lang("amount", "amount_1");?>
                                                <input name="amount[]" type="text" id="amount_1"
                                                       class="pa form-control kb-pad1 amount"/>
                                            </div>
                                        </div>
                                        <div class="col-sm-5 col-sm-offset-1">
                                            <div class="form-group">
                                                <?=lang("paying_by", "paid_by_1");?>
                                                <select name="paid_by[]" id="paid_by_1" class="form-control paid_by">
                                                    <?= $this->sma->paid_opts(); ?>
                                                    <?=$pos_settings->paypal_pro ? '<option value="ppp">' . lang("paypal_pro") . '</option>' : '';?>
                                                    <?=$pos_settings->stripe ? '<option value="stripe">' . lang("stripe") . '</option>' : '';?>
                                                    <?=$pos_settings->authorize ? '<option value="authorize">' . lang("authorize") . '</option>' : '';?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-11">
                                            <div class="form-group gc_1" style="display: none;">
                                                <?=lang("gift_card_no", "gift_card_no_1");?>
                                                <input name="paying_gift_card_no[]" type="text" id="gift_card_no_1"
                                                       class="pa form-control kb-pad gift_card_no"/>

                                                <div id="gc_details_1"></div>
                                            </div>
                                            <div class="pcc_1" style="display:none;">
                                                <div class="form-group">
                                                    <input type="text" id="swipe_1" class="form-control swipe"
                                                           placeholder="<?=lang('swipe')?>"/>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <input name="cc_no[]" type="text" id="pcc_no_1"
                                                                   class="form-control"
                                                                   placeholder="<?=lang('cc_no')?>"/>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">

                                                            <input name="cc_holer[]" type="text" id="pcc_holder_1"
                                                                   class="form-control"
                                                                   placeholder="<?=lang('cc_holder')?>"/>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <select name="cc_type[]" id="pcc_type_1"
                                                                    class="form-control pcc_type"
                                                                    placeholder="<?=lang('card_type')?>">
                                                                <option value="Visa"><?=lang("Visa");?></option>
                                                                <option
                                                                    value="MasterCard"><?=lang("MasterCard");?></option>
                                                                <option value="Amex"><?=lang("Amex");?></option>
                                                                <option
                                                                    value="Discover"><?=lang("Discover");?></option>
                                                            </select>
                                                            <!-- <input type="text" id="pcc_type_1" class="form-control" placeholder="<?=lang('card_type')?>" />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <input name="cc_month[]" type="text" id="pcc_month_1"
                                                                   class="form-control"
                                                                   placeholder="<?=lang('month')?>"/>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">

                                                            <input name="cc_year" type="text" id="pcc_year_1"
                                                                   class="form-control"
                                                                   placeholder="<?=lang('year')?>"/>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">

                                                            <input name="cc_cvv2" type="text" id="pcc_cvv2_1"
                                                                   class="form-control"
                                                                   placeholder="<?=lang('cvv2')?>"/>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="pcheque_1" style="display:none;">
                                                <div class="form-group"><?=lang("cheque_no", "cheque_no_1");?>
                                                    <input name="cheque_no[]" type="text" id="cheque_no_1"
                                                           class="form-control cheque_no"/>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <?=lang('payment_note', 'payment_note');?>
                                                <textarea name="payment_note[]" id="payment_note_1"
                                                          class="pa form-control kb-text payment_note"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="multi-payment"></div>
                        <button type="button" class="btn btn-primary col-md-12 addButton"><i
                                class="fa fa-plus"></i> <?=lang('add_more_payments')?></button>
                        <div style="clear:both; height:15px;"></div>
                        <div class="font16">
                            <table class="table table-bordered table-condensed table-striped" style="margin-bottom: 0;">
                                <tbody>
                                <tr>
                                    <td width="25%"><?=lang("total_items");?></td>
                                    <td width="25%" class="text-right"><span id="item_count">0.00</span></td>
                                    <td width="25%"><?=lang("total_payable");?></td>
                                    <td width="25%" class="text-right"><span id="twt">0.00</span></td>
                                </tr>
                                <tr>
                                    <td><?=lang("total_paying");?></td>
                                    <td class="text-right"><span id="total_paying">0.00</span></td>
                                    <td><?=lang("balance");?></td>
                                    <td class="text-right"><span id="balance">0.00</span></td>
                                </tr>
                                </tbody>
                            </table>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-3 text-center">
                        <span style="font-size: 1.2em; font-weight: bold;"><?=lang('quick_cash');?></span>

                        <div class="btn-group btn-group-vertical">
                            <button type="button" class="btn btn-lg btn-info quick-cash" id="quick-payable">0.00
                            </button>
                            <?php
                                foreach (lang('quick_cash_notes') as $cash_note_amount) {
                                    echo '<button type="button" class="btn btn-lg btn-warning quick-cash">' . $cash_note_amount . '</button>';
                                }
                            ?>
                            <button type="button" class="btn btn-lg btn-danger"
                                    id="clear-cash-notes"><?=lang('clear');?></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-block btn-lg btn-primary" id="submit-sale"><?=lang('submit');?></button>
            </div>
        </div>
    </div>
</div>
-->
<div class="modal" id="cmModal" tabindex="-1" role="dialog" aria-labelledby="cmModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">
                    <i class="fa fa-2x">&times;</i></span>
                    <span class="sr-only"><?=lang('close');?></span>
                </button>
                <h4 class="modal-title" id="cmModalLabel"></h4>
            </div>
            <div class="modal-body" id="pr_popover_content">
                <div class="form-group">
                    <?= lang('comment', 'icomment'); ?>
                    <?= form_textarea('comment', '', 'class="form-control kb-text" id="icomment" style="height:80px;"'); ?>
                </div>
                <div class="form-group" style="display: none">
                    <?= lang('ordered', 'iordered'); ?>
                    <?php
                    $opts = array(0 => lang('no'), 1 => lang('yes'));
                    ?>
                    <?= form_dropdown('ordered', $opts, '', 'class="form-control" id="iordered" style="width:100%;"'); ?>
                </div>
                <input type="hidden" id="irow_id" value=""/>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="editComment"><?=lang('submit')?></button>
            </div>
        </div>
    </div>
</div>

<!-- <div class="modal" id="prModal" tabindex="-1" role="dialog" aria-labelledby="prModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true"><i
                            class="fa fa-2x">&times;</i></span><span class="sr-only"><?=lang('close');?></span></button>
                <h4 class="modal-title" id="prModalLabel"></h4>
            </div>
            <div class="modal-body" id="pr_popover_content">
                <form class="form-horizontal" role="form">
                   
                   
                   
                   
                   
                    
                    <div class="form-group">
                        <label for="poption" class="col-sm-4 control-label"><?=lang('recipe_addon')?></label>
                        <div class="col-sm-8">
                            <div id="poaddon-div"></div>
                        </div>
                    </div>
                  
                   
                   
                    <div class="form-group">
                        <label for="pquantity" class="col-sm-4 control-label"><?=lang('quantity')?></label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control kb-pad"  id="pquantity">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="pprice" class="col-sm-4 control-label"><?=lang('unit_price')?></label>

                        <div class="col-sm-8">
                            <input type="text" class="form-control kb-pad" disabled id="pprice" <?= ($Owner || $Admin || $GP['edit_price']) ? '' : 'readonly'; ?>>
                        </div>
                    </div>
                    
                    <input type="hidden" id="punit_price" value=""/>
                    <input type="hidden" id="old_tax" value=""/>
                    <input type="hidden" id="old_qty" value=""/>
                    <input type="hidden" id="old_price" value=""/>
                    <input type="hidden" id="row_id" value=""/>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="editItem"><?=lang('submit')?></button>
            </div>
        </div>
    </div>
</div> -->

<div class="modal" id="prModal1" tabindex="-1" role="dialog" aria-labelledby="prModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true"><i
                            class="fa fa-2x">&times;</i></span><span class="sr-only"><?=lang('close');?></span></button>
                <h4 class="modal-title" id="prModalLabel"></h4>
            </div>
            <div class="modal-body" id="pr_popover_content">
                <form class="form-horizontal" role="form">
                    <div class="form-group text-center">
                       
                        <div class="col-sm-10">
                            <table class="table table-bordered table-striped">
                                 <thead>
                                     <tr>
                                         <th><?= lang('check'); ?></th>
                                         <th><?= lang('recipe_addon'); ?></th>
                                         <th><?= lang('quantity'); ?></th>
                                         <th><?= lang('price'); ?></th>
                                     </tr>
                                 </thead>
                                 <tbody id="poaddon-div"></tbody>
                            </table>                            
                        </div>
                    </div>  
                                     
                     <div class="form-group" style="display: none">
                        <label for="addonamount" class="col-sm-4 control-label"><?=lang('addonamount')?></label>
                        <div class="col-sm-8">
                            <input type="hidden" class="form-control kb-pad" name="addonamount[]" id="addonamount">
                        </div>
                    </div>

                    <input type="hidden" id="punit_price" value=""/>
                    <input type="hidden" id="old_tax" value=""/>
                    <input type="hidden" id="old_qty" value=""/>
                    <input type="hidden" id="old_price" value=""/>
                    <input type="hidden" id="row_id" value=""/>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="AddonItem"><?=lang('submit')?></button>
            </div>
        </div>
    </div>
</div>

<!-- Customizable model start -->
<div class="modal" id="cuModal" tabindex="-1" role="dialog" aria-labelledby="cuModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true"><i
                            class="fa fa-2x">&times;</i></span><span class="sr-only"><?=lang('close');?></span></button>
                <h4 class="modal-title" id="cuModalLabel"></h4>
            </div>
            <div class="modal-body" id="pr_popover_content">
                <form class="form-horizontal" role="form">
                    <div class="form-group text-center">
                       
                        <div class="col-sm-10">
                            <table class="table table-bordered table-striped">
                                 <thead>
                                     <tr>
                                        <th><?= lang('check'); ?></th>
                                        <th><?= lang('name'); ?></th>  
                                        <th><?= lang('qty'); ?></th>
                                        <th><?= lang('uom'); ?></th>
                                     </tr>
                                 </thead>
                                 <tbody id="pocustomize-div"></tbody>
                            </table>                            
                        </div>
                    </div>                               
                </form>
            </div>
            <input type="hidden" id="row_id1" value=""/>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="CustomizeItem"><?=lang('submit')?></button>
            </div>
        </div>
    </div>
</div>
<!-- Customizable model end -->

<div class="modal fade in" id="gcModal" tabindex="-1" role="dialog"  aria-labelledby="mModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i
                        class="fa fa-2x">&times;</i></button>
                <h4 class="modal-title" id="myModalLabel"><?=lang('sell_gift_card');?></h4>
            </div>
            <div class="modal-body">
                <p><?=lang('enter_info');?></p>

                <div class="alert alert-danger gcerror-con" style="display: none;">
                    <button data-dismiss="alert" class="close" type="button">×</button>
                    <span id="gcerror"></span>
                </div>
                <div class="form-group">
                    <?=lang("card_no", "gccard_no");?> *
                    <div class="input-group">
                        <?php echo form_input('gccard_no', '', 'class="form-control" id="gccard_no"'); ?>
                        <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;">
                            <a href="#" id="genNo"><i class="fa fa-cogs"></i></a>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="gcname" value="<?=lang('gift_card')?>" id="gcname"/>

                <div class="form-group">
                    <?=lang("value", "gcvalue");?> *
                    <?php echo form_input('gcvalue', '', 'class="form-control" id="gcvalue"'); ?>
                </div>
                <div class="form-group">
                    <?=lang("price", "gcprice");?> *
                    <?php echo form_input('gcprice', '', 'class="form-control" id="gcprice"'); ?>
                </div>
                <div class="form-group">
                    <?=lang("customer", "gccustomer");?>
                    <?php echo form_input('gccustomer', '', 'class="form-control" id="gccustomer"'); ?>
                </div>
                <div class="form-group">
                    <?=lang("expiry_date", "gcexpiry");?>
                    <?php echo form_input('gcexpiry', $this->sma->hrsd(date("Y-m-d", strtotime("+2 year"))), 'class="form-control date" id="gcexpiry"'); ?>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" id="addGiftCard" class="btn btn-primary"><?=lang('sell_gift_card')?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade in" id="mModal"  data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="mModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" id="reset1" data-dismiss="modal"><span aria-hidden="true"><i
                            class="fa fa-2x">&times;</i></span><span class="sr-only"><?=lang('close');?></span></button>
                <h4 class="modal-title" id="mModalLabel"><?=lang('add_recipe_manually')?></h4>
            </div>
            <div class="modal-body" id="pr_popover_content">
                <form class="form-horizontal" role="form">
                    <div class="form-group">
                        <label for="mcode" class="col-sm-4 control-label"><?=lang('recipe_code')?> *</label>

                        <div class="col-sm-8">
                            <input type="text" class="form-control kb-pad" id="mcode">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="mname" class="col-sm-4 control-label"><?=lang('recipe_name')?> *</label>

                        <div class="col-sm-8">
                            <input type="text" class="form-control kb-text" id="mname">
                        </div>
                    </div>
                    <?php if ($Settings->tax1) {
                        ?>
                     <!--    <div class="form-group">
                            <label for="mtax" class="col-sm-4 control-label"><?=lang('recipe_tax')?> *</label>

                            <div class="col-sm-8">
                                <?php
                                    $tr[""] = "";
                                        foreach ($tax_rates as $tax) {
                                            $tr[$tax->id] = $tax->name;
                                        }
                                        echo form_dropdown('mtax', $tr, "", 'id="mtax" class="form-control pos-input-tip" style="width:100%;"');
                                    ?>
                            </div>
                        </div> -->
                    <?php }
                    ?>
                    <div class="form-group">
                        <label for="mquantity" class="col-sm-4 control-label"><?=lang('quantity')?> *</label>

                        <div class="col-sm-8">
                            <input type="text" class="form-control kb-pad" id="mquantity">
                        </div>
                    </div>
                    <?php if ($Settings->product_discount && ($Owner || $Admin || $this->session->userdata('allow_discount'))) {?>
                        <!-- <div class="form-group">
                            <label for="mdiscount"
                                   class="col-sm-4 control-label"><?=lang('recipe_discount')?></label>

                            <div class="col-sm-8">
                                <input type="text" class="form-control kb-pad" id="mdiscount">
                            </div>
                        </div> -->
                    <?php }
                    ?>
                    <div class="form-group">
                        <label for="mprice" class="col-sm-4 control-label"><?=lang('unit_price')?> *</label>

                        <div class="col-sm-8">
                            <input type="text" class="form-control kb-pad" id="mprice">
                        </div>
                    </div>
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th style="width:25%;"><?=lang('net_unit_price');?></th>
                            <th style="width:25%;"><span id="mnet_price"></span></th>
                         <!--    <th style="width:25%;"><?=lang('recipe_tax');?></th>
                            <th style="width:25%;"><span id="mpro_tax"></span></th> -->
                        </tr>
                    </table>
                </form>
            </div>
            <div class="modal-footer">
			                        <!--     <button type="button" class="btn btn-danger" id="reset1" style="cursor: pointer!important;"><label style="margin-top: 0px !important;"><?=lang('reset')?> </label></button>
-->
                <button type="button" class="btn btn-primary" id="addItemManually"><?=lang('submit')?></button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade in" id="sckModal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">
                <i class="fa fa-2x">&times;</i></span><span class="sr-only"><?=lang('close');?></span>
                </button>
                <button type="button" class="btn btn-xs btn-default no-print pull-right" style="margin-right:15px;" onclick="window.print();">
                    <i class="fa fa-print"></i> <?= lang('print'); ?>
                </button>
                <h4 class="modal-title" id="mModalLabel"><?=lang('shortcut_keys')?></h4>
            </div>
            <div class="modal-body" id="pr_popover_content">
                <table class="table table-bordered table-striped table-condensed table-hover"
                       style="margin-bottom: 0px;">
                    <thead>
                    <tr>
                        <th><?=lang('shortcut_keys')?></th>
                        <th><?=lang('actions')?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td><?=$pos_settings->focus_add_item?></td>
                        <td><?=lang('focus_add_item')?></td>
                    </tr>
                    <tr>
                        <td><?=$pos_settings->add_manual_recipe?></td>
                        <td><?=lang('add_manual_recipe')?></td>
                    </tr>
                    <tr>
                        <td><?=$pos_settings->customer_selection?></td>
                        <td><?=lang('customer_selection')?></td>
                    </tr>
                    <tr>
                        <td><?=$pos_settings->add_customer?></td>
                        <td><?=lang('add_customer')?></td>
                    </tr>
                    <tr>
                        <td><?=$pos_settings->toggle_category_slider?></td>
                        <td><?=lang('toggle_category_slider')?></td>
                    </tr>
                    <tr>
                        <td><?=$pos_settings->toggle_subcategory_slider?></td>
                        <td><?=lang('toggle_subcategory_slider')?></td>
                    </tr>
                    <tr>
                        <td><?=$pos_settings->cancel_sale?></td>
                        <td><?=lang('cancel_sale')?></td>
                    </tr>
                    <tr>
                        <td><?=$pos_settings->suspend_sale?></td>
                        <td><?=lang('suspend_sale')?></td>
                    </tr>
                    <tr>
                        <td><?=$pos_settings->print_items_list?></td>
                        <td><?=lang('print_items_list')?></td>
                    </tr>
                    <tr>
                        <td><?=$pos_settings->finalize_sale?></td>
                        <td><?=lang('finalize_sale')?></td>
                    </tr>
                    <tr>
                        <td><?=$pos_settings->today_sale?></td>
                        <td><?=lang('today_sale')?></td>
                    </tr>
                    <tr>
                        <td><?=$pos_settings->open_hold_bills?></td>
                        <td><?=lang('open_hold_bills')?></td>
                    </tr>
                    <tr>
                        <td><?=$pos_settings->close_register?></td>
                        <td><?=lang('close_register')?></td>
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
                <h4 class="modal-title" id="dsModalLabel"><?=lang('edit_order_discount');?></h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <?=lang("order_discount", "order_discount_input");?>
                    <?php echo form_input('order_discount_input', '', 'class="form-control kb-pad" id="order_discount_input"'); ?>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" id="updateOrderDiscount" class="btn btn-primary"><?=lang('update')?></button>
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
                <h4 class="modal-title" id="sModalLabel"><?=lang('shipping');?></h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <?=lang("shipping", "shipping_input");?>
                    <?php echo form_input('shipping_input', '', 'class="form-control kb-pad" id="shipping_input"'); ?>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" id="updateShipping" class="btn btn-primary"><?=lang('update')?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade in" id="txModal" tabindex="-1" role="dialog" aria-labelledby="txModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i
                        class="fa fa-2x">&times;</i></button>
                <h4 class="modal-title" id="txModalLabel"><?=lang('edit_order_tax');?></h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <?=lang("order_tax", "order_tax_input");?>
<?php
    $tr[""] = "";
    foreach ($tax_rates as $tax) {
        $tr[$tax->id] = $tax->name;
    }
    echo form_dropdown('order_tax_input', $tr, "", 'id="order_tax_input" class="form-control pos-input-tip" style="width:100%;"');
?>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" id="updateOrderTax" class="btn btn-primary"><?=lang('update')?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade in" id="susModal" tabindex="-1" role="dialog" aria-labelledby="susModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i
                        class="fa fa-2x">&times;</i></button>
                <h4 class="modal-title" id="susModalLabel"><?=lang('suspend_sale');?></h4>
            </div>
            <div class="modal-body">
                <p><?=lang('type_reference_note');?></p>

                <div class="form-group">
                    <?=lang("reference_note", "reference_note");?>
                    <?= form_input('reference_note', (!empty($reference_note) ? $reference_note : ''), 'class="form-control kb-text" id="reference_note"'); ?>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" id="suspend_sale" class="btn btn-primary"><?=lang('submit')?></button>
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
<div class="modal fade in add_cust_sec" id="myModal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true"></div>
<div class="modal fade in" id="myModal2" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2"
     aria-hidden="true"></div>
<div id="modal-loading" style="display: none;">
    <div class="blackbg"></div>
    <div class="loader"></div>
</div>


<?php unset($Settings->setting_id, $Settings->smtp_user, $Settings->smtp_pass, $Settings->smtp_port, $Settings->update, $Settings->reg_ver, $Settings->allow_reg, $Settings->default_email, $Settings->mmode, $Settings->timezone, $Settings->restrict_calendar, $Settings->restrict_user, $Settings->auto_reg, $Settings->reg_notification, $Settings->protocol, $Settings->mailpath, $Settings->smtp_crypto, $Settings->corn, $Settings->customer_group, $Settings->srampos_username, $Settings->purchase_code);?>
<script type="text/javascript">
var site = <?=json_encode(array('url' => base_url(), 'base_url' => admin_url('/'), 'assets' => $assets, 'settings' => $Settings, 'dateFormats' => $dateFormats))?>, pos_settings = <?=json_encode($pos_settings);?>;
var lang = {
    unexpected_value: '<?=lang('unexpected_value');?>',
    select_above: '<?=lang('select_above');?>',
    r_u_sure: '<?=lang('r_u_sure');?>',
    bill: '<?=lang('bill');?>',
    order: '<?=lang('order');?>',
    total: '<?=lang('total');?>',
    items: '<?=lang('items');?>',
    discount: '<?=lang('discount');?>',
    order_tax: '<?=lang('order_tax');?>',
    grand_total: '<?=lang('grand_total');?>',
    total_payable: '<?=lang('total_payable');?>',
    rounding: '<?=lang('rounding');?>',
    merchant_copy: '<?=lang('merchant_copy');?>'
};
</script>

<script type="text/javascript">
    var recipe_variant = 0, shipping = 0, p_page = 0, per_page = 0, tcp = "<?=$tcp?>", pro_limit = <?= $pos_settings->pro_limit; ?>,
        brand_id = 0, obrand_id = 0, cat_id = "<?=$pos_settings->default_category?>", ocat_id = "<?=$pos_settings->default_category?>", sub_cat_id = 0, osub_cat_id,
        count = 1, an = 1, DT = <?=$Settings->default_tax_rate?>,
        recipe_tax = 0, invoice_tax = 0, recipe_discount = 0, order_discount = 0, total_discount = 0, total = 0, total_paid = 0, grand_total = 0,
        KB = <?=$pos_settings->keyboard?>, tax_rates =<?php echo json_encode($tax_rates); ?>;
    var protect_delete = <?php if (!$Owner && !$Admin) {echo $pos_settings->pin_code ? '1' : '0';} else {echo '0';} ?>, billers = <?= json_encode($posbillers); ?>, biller = <?= json_encode($posbiller); ?>;
    var username = '<?=$this->session->userdata('username');?>', order_data = '', bill_data = '';

    function widthFunctions(e) {
        var wh = $(window).height(),
            lth = $('#left-top').height(),
            lbh = $('#left-bottom').height();
        $('#item-list').css("height", wh - 360);
        $('#item-list').css("min-height", 205);
        $('#left-middle').css("height", wh - lth - lbh - 102);
        $('#left-middle').css("min-height", 278);
        $('#recipe-list').css("height", wh - lth - lbh - 107);
        $('#recipe-list').css("min-height", 278);
    }
    $(window).bind("resize", widthFunctions);
	
	 
			
    $(document).ready(function () {
        $('#view-customer').click(function(){
            $('#myModal').modal({remote: site.base_url + 'customers/view_customer/' + $("input[name=customer]").val()});
            //$('#myModal').modal('show');
			
        });
        $('textarea').keydown(function (e) {
            if (e.which == 13) {
               var s = $(this).val();
               $(this).val(s+'\n').focus();
               e.preventDefault();
               return false;
            }
        });
        <?php if ($sid) { ?>
        localStorage.setItem('positems', JSON.stringify(<?=$items;?>));
        <?php } ?>
      /*   if (localStorage.getItem("positems") === null) {
 alert('notset');
}else{
	alert('isset');
} */
        <?php if ($oid) { ?>
        localStorage.setItem('positems', JSON.stringify(<?=$items;?>));
        <?php } ?>

<?php if ($this->session->userdata('remove_posls')) {?>
        if (localStorage.getItem('positems')) {
            localStorage.removeItem('positems');
        }
        if (localStorage.getItem('posdiscount')) {
            localStorage.removeItem('posdiscount');
        }
        if (localStorage.getItem('postax2')) {
            localStorage.removeItem('postax2');
        }
        if (localStorage.getItem('posshipping')) {
            localStorage.removeItem('posshipping');
        }
        if (localStorage.getItem('poswarehouse')) {
            localStorage.removeItem('poswarehouse');
        }
        if (localStorage.getItem('posnote')) {
            localStorage.removeItem('posnote');
        }
        if (localStorage.getItem('poscustomer')) {
            localStorage.removeItem('poscustomer');
        }
        if (localStorage.getItem('posbiller')) {
            localStorage.removeItem('posbiller');
        }
        if (localStorage.getItem('poscurrency')) {
            localStorage.removeItem('poscurrency');
        }
        if (localStorage.getItem('posnote')) {
            localStorage.removeItem('posnote');
        }
        if (localStorage.getItem('staffnote')) {
            localStorage.removeItem('staffnote');
        }
        <?php $this->sma->unset_data('remove_posls');}
        ?>
        widthFunctions();
       
		<?php
		if($get_order_type == 3 && !empty($same_customer) && !empty($this->input->get('customer'))){
			$customer = $this->input->get('customer');
		}elseif($get_order_type == 3 && empty($same_customer) && !empty($this->input->get('customer'))){
			$customer = $this->input->get('customer');
		}elseif($get_order_type == 3 && !empty($same_customer) && empty($this->input->get('customer'))){
			$customer = $same_customer;
		}elseif($get_order_type == 3 && empty($same_customer) && empty($this->input->get('customer'))){
			$customer = '';
		}elseif($get_order_type != 3 && !empty($same_customer) && !empty($this->input->get('customer'))){
			$customer = $this->input->get('customer');
		}elseif($get_order_type != 3 && empty($same_customer) && !empty($this->input->get('customer'))){
			$customer = $this->input->get('customer');
		}elseif($get_order_type != 3 && !empty($same_customer) && empty($this->input->get('customer'))){
			$customer = $same_customer;
		}elseif($get_order_type != 3 && empty($same_customer) && empty($this->input->get('customer'))){
			$customer = $customer->id;	
		}else{
			$customer = '';
		}
		
		?>
		<?php
		if(!empty($customer)){
		?>
		 if (localStorage.getItem('poscustomer')) {
			localStorage.removeItem('poscustomer');
		 }
		if (!localStorage.getItem('poscustomer')) {
            localStorage.setItem('poscustomer', <?=$customer;?>);
        }
		<?php
		}
		?>
		
        if (!localStorage.getItem('postax2')) {
            localStorage.setItem('postax2', <?=$Settings->default_tax_rate2;?>);
        }
		
		
        $('.select').select2({minimumResultsForSearch: 7});
      
		
        $('#poscustomer').val(localStorage.getItem('poscustomer')).select2({
			
            minimumInputLength: 1,
            data: [],
            initSelection: function (element, callback) {
                $.ajax({
                    type: "get", async: false,
                    url: "<?=admin_url('customers/getCustomer')?>/" + $(element).val(),
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
                $('.select2-input').addClass('kb-text-click');
                display_keyboards();
                $('.select2-input').bind('change.keyboard', function (e, keyboard, el) {
                    if (el && el.value != '' && el.value.length > 0 && sct != el.value) {
                        sct = el.value;
                    }
                    if(!el && sct.length > 0) {
                        $('.select2-input').addClass('select2-active');
                       // setTimeout(function() {
                            $.ajax({
                                type: "get",
                                async: false,
                                url: "<?=admin_url('customers/suggestions')?>/?term=" + sct,
                                dataType: "json",
                                success: function (res) {
                                    if (res.results != null) {
                                        $('#poscustomer').select2({data: res}).select2('open');
                                        $('.select2-input').removeClass('select2-active');
                                    } else {
                                         bootbox.alert('no_match_found');
                                        $('#poscustomer').select2('close');
                                        $('#test').click();
                                    }
                                }
                            });
                        //}, 500);
                    }
                });
            });

            $('#poscustomer').on('select2-close', function () {
                $('.select2-input').removeClass('kb-text-click');                
                $('#test').click();
                $('select, .select').select2('destroy');
                $('select, .select').select2({minimumResultsForSearch: 7});
            });
            $(document).bind('click', '#test', function () {
                var kb = $('#test').keyboard().getkeyboard();                
                kb.close();
            });

        }


       

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
                            bootbox.alert('<?=lang('incorrect_gift_card')?>');
                        } else if (data.customer_id !== null && data.customer_id !== $('#poscustomer').val()) {
                            $('#gift_card_no_' + id).parent('.form-group').addClass('has-error');
                            bootbox.alert('<?=lang('gift_card_not_for_customer')?>');
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
                bootbox.alert('<?=lang('max_reached')?>');
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
            <?php if ($pos_settings->rounding) {?>
            $('#balance').text(formatMoney(total_paying - round_total));
            $('#balance_' + pi).val(formatDecimal(total_paying - round_total));
            total_paid = total_paying;
            grand_total = round_total;
            <?php } else {?>
            $('#balance').text(formatMoney(total_paying - gtotal));
            $('#balance_' + pi).val(formatDecimal(total_paying - gtotal));
            total_paid = total_paying;
            grand_total = gtotal;
            <?php }
            ?>
        }

        $("#add_item").autocomplete({
            source: function (request, response) {
                if (!$('#poscustomer').val()) {
                    $('#add_item').val('').removeClass('ui-autocomplete-loading');
                    bootbox.alert('<?=lang('Please choose customer');?>');
                    //response('');
                    $('#add_item').focus();
                    return false;
                }
                $.ajax({
                    type: 'get',
                    url: '<?=admin_url('sales/suggestions');?>',
                    dataType: "json",
                    data: {
                        term: request.term,
                        warehouse_id: $("#poswarehouse").val(),
                        customer_id: $("#poscustomer").val(),
                        recipe_standard: 1
                    },
                    success: function (data) {
                        $(this).removeClass('ui-autocomplete-loading');
                        response(data);
                    }
                });
            },
            minLength: 1,
            autoFocus: true,
            delay: 250,
            response: function (event, ui) {
				
                if ($(this).val().length >= 16 && ui.content[0].id == 0) {
					
                    bootbox.alert('<?=lang('no_match_found')?>', function () {
                        $('#add_item').focus();
                    });
                    $(this).val('');
                }
                //else if (ui.content.length == 1 && ui.content[0].id != 0) {
					
                  //  ui.item = ui.content[0];
                  //  $(this).data('ui-autocomplete')._trigger('select', 'autocompleteselect', ui);
                  //  $(this).autocomplete('close');
               // }
                else if (ui.content.length == 1 && ui.content[0].id == 0) {
					
                    bootbox.alert('<?=lang('no_match_found')?>', function () {
                        $('#add_item').focus();
                    });
                    $(this).val('');

                }
		
            },
	    focus: function( event, ui ) {
		event.preventDefault();
		if (item_scanned) {
		    
		    $('.ui-menu-item:eq(0)').trigger('click');
		}
		
		
	    },
            select: function (event, ui) {
                event.preventDefault();
		item_scanned = false;
                if (ui.item.id !== 0) {
                    var row = add_invoice_item(ui.item);
                    if (row)
                        $(this).val('');
                } else {
                    bootbox.alert('<?=lang('no_match_found')?>');
                }
		 $('#add_item').val('');
            }
        });
		
		
		
		
        
        $(document).bind( "change.autocomplete", '#add_item', function( event ) {               
                var add_item = $('#add_item').val();
                 $("#add_item").autocomplete('search', add_item);
                
            });

  $(document).bind( "change.filter", '#myInput', function( event ) {   
   $('#myInput').trigger('keyup');             
                //var myInput = $('#change').val();
                 //$("#myInput").filter('search', myInput);
                
            });

        <?php if ($pos_settings->tooltips) {echo '$(".pos-tip").tooltip();';}
        ?>
        // $('#posTable').stickyTableHeaders({fixedOffset: $('#recipe-list')});
        //$('#posTable').stickyTableHeaders({scrollableArea: $('#recipe-list')});
        //$('#recipe-list, #category-list, #subcategory-list, #brands-list').perfectScrollbar({suppressScrollX: true});
        $('select, .select').select2({minimumResultsForSearch: 7});

        /*$(document).on('click', '.recipe', function (e) {*/
        $(document).on('click', '.recipe:not(".has-varients")', function (e) {
			if($(this).hasClass("non_transaction")){
			 bootbox.alert('ITEM IS NOT AVAILABLE');
			 return false;
			}
			
            $('#modal-loading').show();
			
            code = $(this).val(),
			
                wh = $('#poswarehouse').val(),
                cu = $('#poscustomer').val();
				var tb = $('#postable_list').val();
				if(tb === undefined || tb === null){
					$('#popup_id_s').show();
					$('#category-list').hide();
	                 $('#subcategory-list').hide();
	                $('#cpinner').hide();
					$('#item-list').hide();
			// bootbox.alert('Please Select the Table');
					$("div").removeClass("blackbg");
					$('#popup_id_s').html('<div class="modal-co"><div class=""><div class="bootbox-bod">Please Select the Table</div></div><div class=""><button data-bb-handler="ok" type="button" class="btn btn-primary" id="k">OK</button></div></div>');
				}else{
            $.ajax({
                type: "get",
                url: "<?=admin_url('pos/getrecipeDataByCode')?>",
                data: {code: code, warehouse_id: wh, customer_id: cu},
                dataType: "json",
                success: function (data) {
					
                    e.preventDefault();
                    if (data !== null) {
						
                        add_invoice_item(data);
                        $('#modal-loading').hide();
                    } else {
                        bootbox.alert('<?=lang('no_match_found')?>');
                        $('#modal-loading').hide();
                    }
                }
            });
				}
        });

 $(document).on('click', '#k', function (e) {  
 $('#popup_id_s').hide();
	$('#category-list').show();
	$('#subcategory-list').show();
	$('#cpinner').show();
	$('#item-list').show();
 
 });
 
    $(document).on('click', '.recipe-varient', function (e) {  
//alert('test');	
if($(this).hasClass("non_transaction")){
			 bootbox.alert('ITEM IS NOT AVAILABLE');
			 return false;
			}
        var code = $(this).attr('code');        
        $('#myVaraintModal').modal('hide');        
        $('#modal-loading').hide();        
        var wh = $('#poswarehouse').val();
        var cu = $('#poscustomer').val();
		var tb = $('#postable_list').val();
        $vid = $(this).attr('data-id');
		
            $.ajax({
                type: "get",
                url: "<?=admin_url('pos/getrecipeVarientDataByCode')?>",
                data: {code: code, warehouse_id: wh, customer_id: cu,variant:$vid},
                dataType: "json",
                success: function (data) {                    
                    e.preventDefault();
                    if (data !== null) {                        
                        add_invoice_item(data);
                        $('#modal-loading').hide();
                    } else {
                        bootbox.alert('<?=lang('no_match_found')?>');
                        $('#modal-loading').hide();
                    }
                }
            });
        });

        $(document).on('click', '.category', function () {
            if (cat_id != $(this).val()) {
                $('#open-category').click();
                $('#modal-loading').show();
                var order_type = "<?php echo $get_order_type; ?>";
                cat_id = $(this).val();
				var warehouse_id = '<?php echo $this->session->userdata('warehouse_id'); ?>';
                var order_type = "<?php echo $get_order_type; ?>";
                $.ajax({
                    type: "get",
                    url: "<?=admin_url('pos/ajaxcategorydata_consolidate');?>",
                    data: {category_id: cat_id, warehouse_id: warehouse_id, recipe_standard: 1,order_type:order_type},
                    dataType: "json",
                    success: function (data) {
                        $('#item-list').empty();
                        var newPrs = $('<div></div>');
                        newPrs.html(data.recipe);
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
                    url: "<?=admin_url('pos/ajaxbranddata');?>",
                    data: {brand_id: brand_id},
                    dataType: "json",
                    success: function (data) {
                        $('#item-list').empty();
                        var newPrs = $('<div></div>');
                        newPrs.html(data.recipe);
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
				var warehouse_id = '<?php echo $this->session->userdata('warehouse_id'); ?>';
                var order_type = "<?php echo $get_order_type; ?>";				
                $.ajax({
                    type: "get",
                    url: "<?=admin_url('pos/ajaxrecipe');?>",
                    data: {category_id: cat_id, warehouse_id: warehouse_id, subcategory_id: sub_cat_id, per_page: p_page,order_type:order_type},
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
				var warehouse_id = '<?php echo $this->session->userdata('warehouse_id'); ?>';
                var order_type = "<?php echo $get_order_type; ?>";  
                $.ajax({
                    type: "get",
                    url: "<?=admin_url('pos/ajaxrecipe_consolidate');?>",
                    data: {category_id: cat_id, warehouse_id: warehouse_id, subcategory_id: sub_cat_id, per_page: p_page,order_type: order_type},
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
				var warehouse_id = '<?php echo $this->session->userdata('warehouse_id'); ?>';
                var order_type = "<?php echo $get_order_type; ?>";
                $.ajax({
                    type: "get",
                    url: "<?=admin_url('pos/ajaxrecipe_consolidate');?>",
                    data: {category_id: cat_id, warehouse_id: warehouse_id, subcategory_id: sub_cat_id, per_page: p_page,order_type:order_type},
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

     
    });


    function wrapText_addon_new(context, text, x, y, maxWidth, lineHeight,canvasHeight,qty) {
        $txt = text.rtrim();
        var words = $txt.split(' ');
        var line = ''; 
        for(var n = 0; n < words.length; n++) {
          var testLine = line + words[n];          
        if(n==0){testLine =testLine.trim()}
          var metrics = context.measureText(testLine);          
          var testWidth = metrics.width;
          var testHeight = metrics.height;          
              if (testWidth > maxWidth && n > 0) {
                context.fillText(line, x, y);
                line = words[n] + ' ';                        
                y += lineHeight;            
              }else {             
                line = testLine;
          }
        }       
         context.fillText(line, x, y);        
         if (y > 41) {
             context.fillText(qty,maxWidth * 1.2, y/2);             
         }else{
             context.fillText(qty,maxWidth * 1.2, y);             
         }
    }


    function wrapText_addon(context, text, x, y, maxWidth, lineHeight,canvasHeight) {
        $txt = text.rtrim();
        var words = $txt.split(' ');
        var line = ''; 
        for(var n = 0; n < words.length; n++) {
          var testLine = line + words[n];          
        if(n==0){testLine =testLine.trim()}
          var metrics = context.measureText(testLine);          
          var testWidth = metrics.width;
          var testHeight = metrics.height;          
              if (testWidth > maxWidth && n > 0) {
                context.fillText(line, x, y);
                line = words[n] + ' ';                        
                y += lineHeight;            
              }else {             
                line = testLine;
          }
        }       
         context.fillText(line, x, y);        
         /*if (y > 41) {
             context.fillText(qty,maxWidth * 1.2, y/2);             
         }else{
             context.fillText(qty,maxWidth * 1.2, y);             
         }*/
    }

String.prototype.rtrim = function () {
    return this.replace(/((\s*\S+)*)\s*/, "$1");
}

function wrapText(context, text, x, y, maxWidth, lineHeight,canvasHeight,qty) {
        $txt = text.rtrim();
        var words = $txt.split(' ');
        var line = '';

// console.log('word length'+words.length);
// alert(words.length);
//  for(var n = 0; n < words.length; n++) {
// console.log('word-'+n + '--' + words[n]);
//     }
        for(var n = 0; n < words.length; n++) {
          var testLine = line + words[n];
          // console.log('test line'+testLine);
      if(n==0){testLine =testLine.trim()}
          var metrics = context.measureText(testLine);
          // console.log('metrics'+metrics);
          var testWidth = metrics.width;
          var testHeight = metrics.height;
          // console.log('testwidth'+testWidth);
          // console.log('testHeight'+testHeight);

          if (testWidth > maxWidth && n > 0) {
            context.fillText(line, x, y);
            line = words[n] + ' ';
            // console.log('nword'+words[n]);
            // console.log(words[n+1]);
            // if (true) {}
            y += lineHeight;
            // console.log('if x value'+x);
          // console.log('if y value'+y);
          }

          else {
             // console.log('else x value'+x);
          // console.log('else y value'+y);
            line = testLine;
          }
        }

        // if(canvasHeight > 60){
          // context.fillText(qty,maxWidth * 1.2, canvasHeight / 2)
        // }else{
        
        // }
        // console.log('canvas height' +canvasHeight / 2);
        // console.log(y);
       /* if(canvasHeight > 60){
            context.fillText(line, x, canvasHeight / 2);
        }else{
            context.fillText(line, x, y);
        }*/
         context.fillText(line, x, y);

             // console.log('name values'+x +',' + y )
         if (y > 41) {

             context.fillText(qty,maxWidth * 1.2, y/2)
             // console.log('qty values'+maxWidth * 1.2 +',' + y/2 )
         }else{

             context.fillText(qty,maxWidth * 1.2, y)
             // console.log('qty values'+maxWidth * 1.2 +',' + y)
         }
    }
    $(document).ready(function () {
		
		$('#sent_to_kitchen').click(function () {
			
          	if (count == 1 ) {
                bootbox.alert('Select recipe');
               
//$('.ad12 div').length
//alert(ad12);
//$("div.ad12").children().length
			  return false;
            }else{
				
				//var c1 = $(".ad12 > div").length
				//if (c1 == 1 ) {
               // bootbox.alert('Select Addon');
//alert(c1);
 //return false;
				//} else{
				<?php if($this->pos_settings->kot_enable_disable == 1){ 
				if($this->pos_settings->kot_print_lang_option ==1){  ?>

				var recipe_variant_id = [];
				    $('.recipe_variant_id').each(function(){
				        recipe_variant_id.push($(this).val());
				    });

				$.each(recipe_variant_id, function (key, val) {
				var canvasHeight = 60;
				var canvas = document.getElementById('myCanvas'+val);
				var context = canvas.getContext('2d');
				var maxWidth = 400;
				var lineHeight = 40;
				//var x = (canvas.width - maxWidth) / 2;
				var x = 5;
				var y = 40;            
				var variant_native_name = $('.item_khmer_name'+val).val();
				var qty = $('.rquantity'+val).val();

				var text = variant_native_name;            
				$arrayWords = [];
				$stringLength = text.length;
				$wordsCnt = Math.ceil($stringLength/28);				
				canvasHeight = (($wordsCnt-1)*40)+60;				
				var $start = 0;var $end =28;
				for(var $n = 0; $n < $wordsCnt; $n++) {
				    $str = text.substring($start, $end);
				    $start = $end;$end =$start+28;				    
				    if($str.length !=0){				    	
				      	$arrayWords.push($str+' ');
				    }				        
				}
				/// set height ///
				$('#myCanvas'+val).attr('height',canvasHeight);   
				///// end-set height //////////
				text =  $arrayWords.join('');
				// context.font = '23px KHMEROSBATTAMBANG-REGULAR';
				// context.font = '23px KHMEROSBATTAMBANG-REGULAR';
				// context.font = 'bold 28px Arial';
				context.font = '26px KHMEROSBATTAMBANG-REGULAR';
				context.fillStyle = '#000';
				wrapText(context, text, x, y, maxWidth, lineHeight,canvasHeight,qty);
				$('#recipe-name-img'+val).val(canvas.toDataURL());
/*recipe variants end*/

var item_addon_names = $('.item_addon_names'+val).val();
var item_addon_qty = $('.item_addon_qty'+val).val();
var addon_names_array = item_addon_names.split(',');
var addon_qty_array = item_addon_qty.split(',');
// console.log(addon_names_array);
// console.log(addon_qty_array);



var assoc = {};
for(var i=0; i<addon_names_array.length; i++) {
    if(addon_names_array[i] != ''){
        assoc[addon_names_array[i]] = addon_qty_array[i];
    }
    
}

/*var result = new array();
for(i=0; i< addon_names_array.length && i < addon_qty_array.length; ++i){
    result[addon_names_array[i]] = addon_qty_array[i];
    // result[addon_names_array[] = addon_qty_array[i];
}*/
//console.log((assoc)); //false
var recipe_addon_base = [];

// if (typeof assoc !== 'undefined' && assoc.length > 0) {
    // alert(jQuery.isEmptyObject(assoc));
    // if(jQuery.isEmptyObject(assoc) != false){
       /* if(isEmpty(assoc)) {
            alert('empty');
        }else{
            alert('not empty');
        }*/
if(!isEmpty(assoc)) {        
$.each(assoc, function (s, p) { 
    var canvasHeight = 60;
    var canvas = document.getElementById('addon_myCanvas'+val); 
    var context = canvas.getContext('2d');
    var maxWidth = 400;
    var lineHeight = 40;                
    /*var x = 50;*/
    var x = 5;
    var y = 40; 
    var variant_native_name =s;  
    // alert(variant_native_name);        
    var qty = p;                  
    var text = "[+]"+variant_native_name;            
    $arrayWords = [];
    $stringLength = text.length;
    $wordsCnt = Math.ceil($stringLength/28);                
    canvasHeight = (($wordsCnt-1)*40)+60;               
    var $start = 0;var $end =28;
    for(var $n = 0; $n < $wordsCnt; $n++) {
        $str = text.substring($start, $end);
        $start = $end;$end =$start+28;                    
        if($str.length !=0){                        
            $arrayWords.push($str+' ');
        }
    }
    $('#addon_myCanvas'+val).attr('height',canvasHeight);   
    text =  $arrayWords.join('');                
    // context.font = 'bold 28px AKbalthom Kbach';    
    context.font = '26px KHMEROSBATTAMBANG-REGULAR';  
    context.fillStyle = '#000';
    /*wrapText_addon(context, text, x, y, maxWidth, lineHeight,canvasHeight);  */
    wrapText_addon_new(context, text, x, y, maxWidth, lineHeight,canvasHeight,qty);           
    // console.log(canvas.toDataURL());
    $rr = (canvas.toDataURL());
    recipe_addon_base.push($rr);

    // alert(s+p);
});
}
    $recipe_addon_base = recipe_addon_base.join('sivan');
    $('#addon-name-img'+val).val($recipe_addon_base);  
    // console.log($recipe_addon_base);
// console.log(assoc);


/*alert(addon_names_array);
alert(addon_qty_array);*/

                /*var recipe_addon_base = [];
                $('.recipe_addon_id'+val).each(function(s, p){                        
                var p =$(this).val();                    
                var canvasHeight = 60;
                var canvas = document.getElementById('addon_myCanvas'+p);
                var context = canvas.getContext('2d');
                var maxWidth = 400;
                var lineHeight = 40;                
                var x = 50;
                var y = 40;                            
                var variant_native_name =$(this).parent().find(".addon_native_name").val();          
                var qty = $(this).closest('td').next('td').find('.addon_quantity').val();                  
                var text = '+'+variant_native_name+'['+qty+']';            
                $arrayWords = [];
                $stringLength = text.length;
                $wordsCnt = Math.ceil($stringLength/28);                
                canvasHeight = (($wordsCnt-1)*40)+60;               
                var $start = 0;var $end =28;
                for(var $n = 0; $n < $wordsCnt; $n++) {
                    $str = text.substring($start, $end);
                    $start = $end;$end =$start+28;                    
                    if($str.length !=0){                        
                        $arrayWords.push($str+' ');
                    }
                }
                $('#addon_myCanvas'+p).attr('height',canvasHeight);   
                text =  $arrayWords.join('');                
                context.font = 'bold 28px AKbalthom Kbach';        
                context.fillStyle = '#000';
                wrapText_addon(context, text, x, y, maxWidth, lineHeight,canvasHeight);                
                // console.log(canvas.toDataURL());
                $rr = (canvas.toDataURL());
                recipe_addon_base.push($rr);
                // $('.addon-name-img'+p).val(canvas.toDataURL());
                }); 

                $recipe_addon_base = recipe_addon_base.join('sivan');
                $('#addon-name-img'+val).val($recipe_addon_base);  
                console.log($recipe_addon_base);*/
				});     
<?php  } } ?>
				     /*return false;*/
					$('#pos_note').val(localStorage.getItem('posnote'));
					$('#staff_note').val(localStorage.getItem('staffnote'));
					$(this).text('<?=lang('loading');?>').attr('disabled', true);
					$('#pos-sale-form').submit();
				}
				//}
            
        });
		
		
		

     /*    $(document).on('click','.has-varients1',function(){
        $obj = $(this);
        $v = $obj.attr('value');
        $popcon = $obj.closest('span').find('.variant-popup').html();
        $('#myVaraintModal .modal-body').html($popcon);
        $('#myVaraintModal').modal('show');
		$('#popup_id_s').show();
					$('#category-list').hide();
	$('#subcategory-list').hide();
	$('#cpinner').hide();
	$('#item-list').hide();
			// bootbox.alert('Please Select the Table');
					$("div").removeClass("blackbg");
					$('#popup_id_s').html('<div class="modal-co"><div class=""><div class="bootbox-bod">Please Select the Table</div></div><div class=""><button data-bb-handler="ok" type="button" class="btn btn-primary" id="k">OK</button></div></div>');
		
    }); */
	
	
	
	/* $(document).ready(function(){
  $(".recipe-11155").dblclick(function(){
	   $('#popup_id_s').show();
    $(this).hide();
  });
}); */
	
	/*  $(document).on('click','.recipe-11155',function(){
		 $('.v12').show();
		 
		// $('#category-list').show();
	//$('#subcategory-list').show();
	//$('#cpinner').show();
	//$('#item-list').show();
	$('html, body').animate({
    scrollTop: $(window).scrollTop() + 100
}); 
	
		 
      });*/
	  
	  
	 
	 
			  $(document).on("keyup",'#myInput', function() {	
				var value = $(this).val().toLowerCase();
				$("#myTable tr").filter(function() {
				  $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
				});
			  });
			
				
	 
        $(document).on('click','.has-varients',function(){
				var tb = $('#postable_list').val();
				if(tb === undefined || tb === null){
					$('#popup_id_s').show();
					$('#category-list').hide();
	                 $('#subcategory-list').hide();
	                $('#cpinner').hide();
					$('#item-list').hide();
			// bootbox.alert('Please Select the Table');
					$("div").removeClass("blackbg");
					$('#popup_id_s').html('<div class="modal-co"><div class=""><div class="bootbox-bod">Please Select the Table</div></div><div class=""><button data-bb-handler="ok" type="button" class="btn btn-primary" id="k">OK</button></div></div>');
					return false;
				}
        $obj = $(this);
        $v = $obj.attr('value');
        $popcon = $obj.closest('span').find('.variant-popup').html();
		$('#category-list').hide();
	$('#subcategory-list').hide();
	$('#cpinner').hide();
	$('#item-list').hide();
        $('.v12').html($popcon);
		$('.v12').show();
		//$('#popup_id_s').css({"padding-top":"45px "});
		//$('.recipe-varient').removeClass();

	//	$('#popup_id_s').css({"overflow-y": "scroll "});
        //$('#myVaraintModal').modal('show');

    });
		
		
    });
function isEmpty(obj) {
    for(var key in obj) {
        if(obj.hasOwnProperty(key))
            return false;
    }
    return true;
}

</script>
 <script src="select2.min.js"></script>
<script>
$("#country").select2( {
 placeholder: "Select variants",
 allowClear: true
 } );
</script>
<?php
    $s2_lang_file = read_file('./assets/config_dumps/s2_lang.js');
    foreach (lang('select2_lang') as $s2_key => $s2_line) {
        $s2_data[$s2_key] = str_replace(array('{', '}'), array('"+', '+"'), $s2_line);
    }
    $s2_file_date = $this->parser->parse_string($s2_lang_file, $s2_data, true);
?>
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
<!--<script type="text/javascript" src="<?=$assets?>pos/js/pos.ajax.js?v=1"></script>-->
<script type="text/javascript" src="<?=$assets?>pos/js/pos_consolidate.ajax.js?v=1"></script>

<!--<script type="text/javascript" src="<?=$assets?>pos/js/jquery.keyboard.extension-all.min.js"></script>
<script type="text/javascript" src="<?=$assets?>pos/js/jquery.keyboard.js"></script>
<script type="text/javascript" src="<?=$assets?>pos/js/jquery.keyboard.min.js?v=1"></script>-->

<script type="text/javascript" src="<?=$assets?>js/dragscrollable.js"></script>
<script type="text/javascript" src="<?=$assets?>js/dragscroll.js"></script>

<script>
	$('#subcategory-list, #scroller').dragscrollable({
    dragSelector: 'button', 
    acceptPropagatedEvent: false
});
	</script>
	
<script type="text/javascript">
$('.sortable_table tbody').sortable({
    containerSelector: 'tr'
});
   
</script>
<script type="text/javascript" charset="UTF-8"><?=$s2_file_date?></script>
<style>
    .variant-popup{
    display: none;
    }
    .sname{
    max-width:50px;
    }
    .addon_css,.comment_css{font-size: 14px !important;position: relative;padding: 2px 9px!important;}
    .customize_css{font-size: 14px !important;background-color: #000!important;position: relative; padding: 2px 5px!important;}

	@media (max-width: 1280px) and (min-width: 1200px){
	.addon_css, .comment_css {
    font-size: 12px !important;
    position: relative;
    padding: 2px 8px!important;
	}
	.customize_css {
    font-size: 12px !important;
    background-color: #000!important;
    position: relative;
    padding: 2px 2px!important;
}
	
}
</style>


<div class="modal fade in" id="myVaraintModal" tabindex="-1" role="dialog" aria-labelledby="VariantModalLabel"
     aria-hidden="true" style="z-index:9999">
    <div class="modal-dialog modal-md">
    <div class="modal-content">
        
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">×</i>
            </button>
            <h4 class="modal-title" id="customerModalLabel">Variants</h4>
        </div>
        
        <div class="modal-body">
    </div>
    
    </div>
    </div>
    </div>

<script>/* 
function ajaxData()
{
    <?php if($isNightauditDone) : ?>
	$.ajax({
	  url: "<?=admin_url('pos/ajax_tables_all');?>",
	  type: "get",
	  success: function(response) {
	    console.log(6)
			$("#exTab1").html(response);
	  }
	});
    <?php endif; ?>
} *//* 
function ajaxData_table($tableid)
{
	
	$.ajax({
	  url: "<?=admin_url('pos/ajax_table_byID_all');?>",
	  type: "post",
	  data:{id:$tableid},
	  success: function(response) {
	    console.log(6)
			$("#exTab1 #table-"+$tableid).html(response);
	  }
	});
}
$(document).ready(ajaxData); */
//var ajaxDatatimeout = setInterval(ajaxData, 100000000);
</script>




<script type="text/javascript">
$(document).on('click', '.table_id', function(){	
//var order_type = $('#order_type').val();	
var order_type =1;
	var table_id = $(this).val();
	var url = '<?php echo  admin_url('pos/consolidate') ?>';	
  if (localStorage.getItem("positems") === null) {
 $.ajax({
			type: "get",
			url: "<?=admin_url('pos/tablecheckdine');?>",
			data: {table_id: table_id, order_type: order_type},
			dataType: "json",
			success: function (data) {
				ajaxDatatimeout = setInterval(ajaxData, 1000);
				if(data.status == 'bill-generated'){
					window.location.href= url +'/order_biller/?type='+order_type+'&table='+table_id;	
				}else if(data.status == 'success'){
					window.location.href= url +'/order_table/?table='+table_id;	
				}else{
					window.location.href= url +'/?order='+order_type+'&table='+table_id;	
				}

			}

		}).done(function () {
			$('#modal-loading').hide();
		});
}else{

  bootbox.confirm("Changes you made may not be saved", function(result) {
        if(result == true) {
            $('#modal-loading').show();
		$.ajax({
			type: "get",
			url: "<?=admin_url('pos/tablecheckdine');?>",
			data: {table_id: table_id, order_type: order_type},
			dataType: "json",
			success: function (data) {
				ajaxDatatimeout = setInterval(ajaxData, 1000);
				if(data.status == 'bill-generated'){
					window.location.href= url +'/order_biller/?type='+order_type+'&table='+table_id;	
				}else if(data.status == 'success'){
					window.location.href= url +'/order_table/?table='+table_id;	
				}else{
					window.location.href= url +'/?order='+order_type+'&table='+table_id;	
				}
			}

		}).done(function () {
			$('#modal-loading').hide();
		});
        }
        });
} 
	
	/* 
	//var order_type = $('#order_type').val();	
	var order_type =1;
	var table_id = $(this).val();
	var url = '<?php echo  admin_url('pos/consolidate') ?>';
	$('#modal-loading').show();
		
		$.ajax({
			type: "get",
			url: "<?=admin_url('pos/tablecheckdine');?>",
			data: {table_id: table_id, order_type: order_type},
			dataType: "json",
			success: function (data) {
				ajaxDatatimeout = setInterval(ajaxData, 1000);
				if(data.status == 'bill-generated'){
					window.location.href= url +'/order_biller/?type='+order_type+'&table='+table_id;	
				}else if(data.status == 'success'){
					window.location.href= url +'/order_table/?table='+table_id;	
				}else{
					window.location.href= url +'/?order='+order_type+'&table='+table_id;	
				}
			}

		}).done(function () {
			$('#modal-loading').hide();
		}); */
	
});
</script>

<script>
function ajaxData(table_id)
{	
	$.ajax({
	  url: "<?=admin_url('pos/ajaxorder_table');?>",
	  type: "get",
	  data: { 
		table: table_id
	  },
	  success: function(response) {
			$("#ordertable_box").html(response);
	  }
	});
}
var ajaxDatatimeout;
// $timeinterval = 60000;
$(document).ready(function(){
    // ajaxData(<?php echo $tableid; ?>);
   /* setTimeout(function(){
    ajaxDatatimeout = setInterval(function(){ajaxData(<?php echo $tableid; ?>)}, $timeinterval);
   
    },120000)*/
    
});
</script>

<div class="modal fade in" id="bilModal" tabindex="-1" role="dialog" aria-labelledby="bilModalLabel" aria-hidden="true" style="background: rgba(0,0,0,0.5);">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close closebil" data-dismiss="modal" aria-hidden="true"><i
                        class="fa fa-2x">&times;</i></button>
                <h4 class="modal-title" id="bilModalLabel">BILL TYPES</h4>
            </div>
            <div class="modal-body">
              
              <div class="form-group">
                    <div style="margin-bottom: 10px;"><input type="radio" class="" name="bil_type" value="1" checked> <?=lang('single_bill')?></div>
                   
                    <div class="count_div" style="margin-bottom: 10px;"><input type="radio" name="bil_type" value="2"> <?=lang('auto_split_bill')?></div>
                   
                     <div class="count_div" style="margin-bottom: 5px;"><input type="radio" name="bil_type" value="3" > Manual Split Bill</div> 
                    <input class="form-control kb-pad " type="text" name="bils_number_auto" id="bils_number_auto" placeholder="<?=lang('auto_split')?>" style="display:none;">
                    <input type="text" class="form-control  kb-pad" name="bils_number_manual" id="bils_number_manual" placeholder="Manual Split" style="display:none;">
                </div>
				<input type="hidden" name="bil_split_type" id="bil_split_type">
                <input type="hidden" name="bil_table_type" id="bil_table_type">
            </div>
            <div class="modal-footer">
                <button type="button" id="updateBil" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="CancelorderModal" tabindex="-1" role="dialog" aria-labelledby="CancelorderModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close closemodal" data-dismiss="modal"><span aria-hidden="true">
                    <i class="fa fa-2x">&times;</i></span>
                    <span class="sr-only"></span>
                </button>
                <h4 class="modal-title" id="cmModalLabel"></h4>
            </div>
            <div class="modal-body" id="pr_popover_content">
		<div class="row col-sm-12">
		    <div class="form-group">
			<label><input type="radio" name="cancel_type" class="radio cancel-type" checked value="out_of_stock"><?=lang('out_of_stock')?></label>
			<label><input type="radio" name="cancel_type" class="radio cancel-type" value="spoiled"><?=lang('spoiled')?></label>
			<label><input type="radio" name="cancel_type" class="radio cancel-type" value="reusable"><?=lang('reusable')?></label>
		    </div>
		</div>
		
                <div class="form-group">
                    <?= lang('remarks'); ?>
                    <?= form_textarea('remarks', '', 'class="form-control kb-text" id="remarks" style="height:80px;"'); ?>
                </div>
                <input type="hidden" id="order_item_id" value=""/>
                <input type="hidden" id="split_order" value=""/>
		        <input type="hidden" id="cancel_qty" value=""/>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="cancel_orderitem"><?=lang('send')?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="CancelAllorderModal" tabindex="-1" role="dialog" aria-labelledby="CancelAllorderModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close cancelclosemodal" data-dismiss="modal"><span aria-hidden="true">
                    <i class="fa fa-2x">&times;</i></span>
                    <span class="sr-only"></span>
                </button>
                <h4 class="modal-title" id="cmModalLabel"></h4>
            </div>
            <div class="modal-body" id="pr_popover_content">
		
		
                <div class="form-group">
                    <?= lang('remarks'); ?>
                    <?= form_textarea('remarks', '', 'class="form-control kb-text" id="cancel-remarks" style="height:80px;"'); ?>
                </div>
                <input type="hidden" id="order_table_id" value=""/>
                <input type="hidden" id="split_table_id" value=""/>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="cancel_allorderitem"><?=lang('send')?></button>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
 $(document).ready(function(e) {
	 $(document).on('click','.orderCancelled',function(e){	
	 	var isShowing = $(this).data("isShowing");
		$('.orderCancelled').removeData("isShowing");
		 if (isShowing != "true") {
			$('.orderCancelled').not(this).tooltip("hide");
			$(this).data("isShowing", "true");
			$(this).tooltip("show");
		  } else {
			$(this).tooltip("hide");
		  }
	 }).tooltip({
		  animation: true,
		  trigger: "manual",
		  placement: "auto"
		});
	 /*var hasToolTip = $(".orderCancelled");
			
			hasToolTip.on("click", function(e) {
				
			  e.preventDefault();
			  var isShowing = $(this).data("isShowing");
			  hasToolTip.removeData("isShowing");
			  if (isShowing != "true") {
				hasToolTip.not(this).tooltip("hide");
				$(this).data("isShowing", "true");
				$(this).tooltip("show");
			  } else {
				$(this).tooltip("hide");
			  }
			}).tooltip({
			  animation: true,
			  trigger: "manual",
			  placement: "auto"
			});*/
	
	$(document).on('click','.waiter_cancel_order',function(e){	
	//$(".waiter_cancel_order").click(function(e) {
        var order_id = $(this).val();
		$('#modal-loading').show();
		 $.ajax({
			type: "get",
			 url:"<?=admin_url('pos/order_cancel_waiter');?>",                
			data: {order_id: order_id},
			dataType: "json",
			success: function (data) {
				location.reload();
			}
		}).done(function () {
			$('#modal-loading').hide();
		});
    });	
	
	 $(".itm_padd").click(function (e) {
    
		if (!$(e.target).is('input:checkbox')) {
			
			var $checkbox = $(this).find('input:checkbox');
			$checkbox.trigger( "click" );
		}
	
	});
	$('.order_list .item_list .itm_padd_content a').click(
    function(e) {
        e.stopPropagation();
    });
	$('.order_list .item_list .btn').click(
    function(e) {
        e.stopPropagation();
    });
	
    //$(".multiple_status").click(function(e) {
	$(document).on('click','.multiple_status',function(e){	
		var item_id = $(this).attr('data-type');
		var status = $(this).attr('title');
		var split_id = $(this).attr('data-split');
		
		var val = [];
		var processArray = [];
		var prepareArray = [];
		$('.status_'+split_id+':checkbox:checked').each(function(i){
			var currentValue = $(this).val();
			val[i] = currentValue;
			if($(this).attr('title') == 'Ready'){
				processArray[i] = currentValue;
			} else if($(this).attr('title') == 'Served') {
				prepareArray[i] = currentValue;
			}
			$('.multiple_'+split_id).prop('checked', false);
		});
		if( (processArray.length > 0) && (prepareArray.length == 0) ){
			
			$(".ready_"+split_id).hide();
			$(".preparing_"+split_id).show();
			$(".ready_"+split_id).attr('data-id', '');
			$(".preparing_"+split_id).attr('data-id', val);
			
		} else if( (prepareArray.length > 0) && (processArray.length == 0) ){
			$(".preparing_"+split_id).hide();
			$(".ready_"+split_id).show();
			$(".preparing_"+split_id).attr('data-id', '');
			$(".ready_"+split_id).attr('data-id', val);
		}else{
			$(".preparing_"+split_id).hide();
			$(".ready_"+split_id).hide();	
			
			$(".preparing_"+split_id).attr('data-id', '');
			$(".ready_"+split_id).attr('data-id', '');
			
		}
			
    });
	
	//$(".multiple_check").change(function(){ 
	$(document).on('click','.multiple_check',function(){
		var order = $(this).attr('data-order');
	 	$('.status_'+order).prop('checked', $(this).prop("checked"));
		
		
		var arr = $.map($('.status_'+order+':checked'), function(e,i) {
			return +e.value;
		});
				
		var val = [];
		var processArray = [];
		var prepareArray = [];
		
		$.each(arr, function( index, value ) {
		  	
			var currentValue = value;
			val[index] = currentValue;
			
			if($("input[value="+currentValue+"]").attr('title') == 'Ready'){
				processArray[index] = currentValue;
			} else if($("input[value="+currentValue+"]").attr('title') == 'Served') {
				prepareArray[index] = currentValue;
			}
		});
		
		if( (processArray.length > 0) && (prepareArray.length == 0) ){
			
			$(".ready_"+order).hide();
			$(".preparing_"+order).show();
			$(".ready_"+order).attr('data-id', '');
			$(".preparing_"+order).attr('data-id', val);
			
		} else if( (prepareArray.length > 0) && (processArray.length == 0) ){
			$(".preparing_"+order).hide();
			$(".ready_"+order).show();
			$(".preparing_"+order).attr('data-id', '');
			$(".ready_"+order).attr('data-id', val);
		}else{
			$(".preparing_"+order).hide();
			$(".ready_"+order).hide();	
			
			$(".preparing_"+order).attr('data-id', '');
			$(".ready_"+order).attr('data-id', '');
			
		}
		
		
	});
	
	//$(".kitchen_status").click(function(e) {
	$(document).on('click','.kitchen_status',function(e){	
        var status = $(this).attr('data-status');
		var split_id = $(this).attr('data-split-id');
		var id = $(this).attr('data-id');
		
		$('#modal-loading').show();
		 $.ajax({
			type: "get",
			 url:"<?=admin_url('pos/update_order_item_status');?>",                
			data: {status: status, order_item_id: id,split_id: split_id},
			dataType: "json",
			success: function (data) {
				location.reload();
			   
				
			}
		}).done(function () {
			$('#modal-loading').hide();
		});
		
    });
});
		
		
		function splitItem( table_id, split_id )
		{
			$('#modal-loading').show();
			$.ajax({
				type: "get",
				url: "<?=admin_url('pos/ajaxSplititemdata');?>",
				data: {table_id: table_id, split_id: split_id},
				dataType: "json",
				success: function (data) {
					$('#tableorderleft').empty();
					var newPrs = $('<div></div>');
					newPrs.html(data.order_item);
					newPrs.appendTo("#tableorderleft");
					
				}
			}).done(function () {
				$('#modal-loading').hide();
			});
		}
		
		function orderItem( order_id, table_id, split_id )
		{
			$('#modal-loading').show();
			$.ajax({
				type: "get",
				url: "<?=admin_url('pos/ajaxOrderitemdata');?>",
				data: {order_id: order_id, table_id: table_id, split_id: split_id},
				dataType: "json",
				success: function (data) {
					$('#tableorderleft').empty();
					var newPrs = $('<div></div>');
					newPrs.html(data.order_item);
					newPrs.appendTo("#tableorderleft");
					
				}
			}).done(function () {
				$('#modal-loading').hide();
			});
		}
		
		
   
            function updateOrderStatus( status, id ,split_id)
            {    
            	
                $('#modal-loading').hide();
				//clearTimeout(ajaxDatatimeout);
                if (confirm('Are you sure?')) { 
                    $.ajax({
                        type: "get",
                        url:"<?=admin_url('pos/update_order_item_status');?>",                
                        data: {status: status, order_item_id: id,split_id: split_id},
                        dataType: "json",
                        success: function (data) {
							//ajaxDatatimeout = setInterval(ajaxData, 60000);
                           
                        }
                    }).done(function () {
                        $('#modal-loading').hide();
                    });
                }
                else{

                }
            }
		
	 		function CancelOrderItem( status, id, split_id ,$remarks=0,$quantity)
            {    
				//clearTimeout(ajaxDatatimeout);
				
            	$("#order_item_id").val(id);
				$("#split_order").val(split_id);
				
				
			if ($quantity>1) {
			    $inputoptions =[];
			    for (i = 0; i < $quantity; i++) {
				$v = i+1;
				$inputoptions[i] = {text: $v,value:$v};
			    }
			
			    bootbox.prompt({ 
				title: "Enter Quantity to cancel",
				inputType:'select',
				inputOptions :$inputoptions,
				callback: function(qty){
				    if (qty!=null) {
					 $cancelQty = qty;
					if ($quantity==qty) {
					    $cancelQty = 'all';
					}
					
					cancelorderPopup(id ,split_id,$remarks,$cancelQty);
					$('#cancel_qty').val($cancelQty);
				    }else{
					
				    }
				   
				}
			    });
				
			}else{
				
			    $cancelQty = 'all';
			    cancelorderPopup(id ,split_id,$remarks,$cancelQty);
			    $('#cancel_qty').val($cancelQty);
			} 
			

            }
	    function cancelorderPopup(id,split_id,$remarks,$cancelQty){
			 if($remarks!=0){
				    $('#remarks').val('');
				    
				    $('#CancelorderModal').show();
				}else{
					alert('2');
				    $msg = ($cancelQty!='all')?'Are you sure want to cancel '+$cancelQty+' Qty?':'Are you sure want to cancel this item?';
		   /*  bootbox.confirm({
			message: $msg,
			buttons: {
			    confirm: {
				label: 'Yes',
				className: 'btn-success'
			    },
			    cancel: {
				label: 'No',
				className: 'btn-danger'
			    }
			},
			callback: function (result) {
			  // console.log(result)
			    if (result) {				
				$.ajax({
				    type: "get",
				    url:"<?=admin_url('pos/cancel_order_items');?>",                
				    data: {order_item_id: id, split_id: split_id,cancelqty:$cancelQty},
				    dataType: "json",
				    success: function (data) {
					if(data.msg == 'success'){
						//ajaxDatatimeout = setInterval(ajaxData, 60000);
						location.reload();      	                      	
					}else{
					    alert('not update waiter');
					}
				    }    
				}).done(function () {
				      
				});
				  
			    }else{
					       //requestBill(billid);
			    }
			    
			}
		    }); */
		} 
	    }
		
		
		
		
	    $('#remarks').on('focus',function(){
		$('#remarks').css('border','1px solid #ccc');
	    });
		
		
		
            $(document).on('click','#cancel_orderitem',function(){
            	$(this).attr('disabled',false);
		     //   $(this).text('please wait...');
//alert('Please choose any one');
            	 var cancel_remarks = $('#remarks').val();
		 var cancel_type = $('.cancel-type:checked').val(); 
            	 var order_item_id = $('#order_item_id').val(); 
				 var split_id = $("#split_order").val();
				 var $cancelQty = $('#cancel_qty').val();
            	 if($.trim(cancel_remarks) != ''){
            	 	$.ajax({
                        type: "get",
                        url:"<?=admin_url('pos/cancel_order_items');?>",                
                        data: {cancel_type:cancel_type,cancel_remarks: cancel_remarks, order_item_id: order_item_id, split_id: split_id,cancelqty:$cancelQty},
                        dataType: "json",
                        success: function (data) {
                            if(data.msg == 'success'){
                            	     $('#CancelorderModal').hide(); 
									 //ajaxDatatimeout = setInterval(ajaxData, 60000);
									 location.reload();      	                      	
                            }else{
                                alert('not update waiter');
                            }
                        }    
                    }).done(function () {
				      
					});
            	 } else{
		        $('#remarks').css('border','1px solid red');
		 }

            });
            $('.closemodal').click(function () {
            	$('#remarks').val('');
            	$('#order_item_id').val('');
				$('#split_order').val('');
				$('#cancel_qty').val('');
 				$('#CancelorderModal').hide(); 
				//ajaxDatatimeout = setInterval(ajaxData, 60000);
            });
	    $('.cancelclosemodal').click(function () {
            	$('#remarks').val('');
            	$('#order_table_id').val('');
				
 				$('#CancelAllorderModal').hide(); 
				//ajaxDatatimeout = setInterval(ajaxData, 60000);
            });
			
			function bilGenerator( table_id, split_id, count_id )
            {    
            	$("#bil_table_type").val(table_id);
				$("#bil_split_type").val(split_id);
				if(count_id == 0 || count_id == 1){
					$(".count_div").hide();
				}
				else{
					$(".count_div").show();
				}
            	$('#bilModal').show();

            }
			$(document).on('change','#bils_number_auto',function(){
				if($(this).val()>0){
					 $('#bils_number_auto').css('border', '1px solid #ccc');
				}
			});
			$(document).on('change','#bils_number_manual',function(){
				if($(this).val()>0){
					 $('#bils_number_manual').css('border', '1px solid #ccc');
				}
			});
			$(document).on('click','#updateBil',function(){
                 var table_id = $('#bil_table_type').val(); 
            	 /*var count_item = $('#count_item').val();*/ 
            	 var split_id = $('#bil_split_type').val(); 
            	 var count_item = $('#'+split_id+'_count_item').val();
				 var bil_type = $('input[name=bil_type]:checked').val();
				 var url = '<?php echo  admin_url('pos') ?>';
				 if(bil_type == 1){
					 var bils = 1;
				 }else if(bil_type == 2){
					 var bils = $('#bils_number_auto').val(); 
				 }else if(bil_type == 3){
                    var bils = $('#bils_number_manual').val();
					//enter value is equal to 1 automatically  will go to single split
                    if(bils == 1){
                        bil_type = 1;                        
                    }
					
                    if(count_item <bils){
                        bootbox.alert('<?=lang('manual_bill');?>');
                        return false;
                    }
				 }

				 if(bils > 0){
					 window.location.href= url +'/billing_all/?order_type=1&bill_type='+bil_type+'&bils='+bils+'&table='+table_id+'&splits='+split_id;
				 }else{
					 if(bil_type == 2){
					  $('#bils_number_auto').css('border-color', 'red');
					 }
					 if(bil_type == 3){
					 $('#bils_number_manual').css('border-color', 'red');
					 }
            	 	//alert('Please enter 1 or more than 1');
				 }

            });
			
			$('.closebil').click(function () {
            	$("#bil_table_type").val('');
				$("#bil_split_type").val('');
            	$('#bilModal').hide();
            });
			
		
	$('.print_bill').click(function () {
            if (count == 1) {
                bootbox.alert('<?=lang('x_total');?>');
                return false;
            }
            <?php if ($pos_settings->remote_printing != 1) { ?>
                printBill();
            <?php } else { ?>
                Popup($('#bill_tbl').html());
            <?php } ?>
        });
		
		
$(document).ready(function() {
    //$('input[type=radio][name=bil_type]').change(function() {
		$('input[type=radio][name=bil_type]').on('ifChanged', function() {

		$("#bils_number_auto").val('');
		$("#bils_number_manual").val('');
        if (this.value == 1) {
            $("#bils_number_auto").hide();
			$("#bils_number_manual").hide();
        }else if (this.value == 2) {
			$("#bils_number_manual").hide();
			$("#bils_number_auto").show();
        }else if (this.value == 3) {
            $("#bils_number_auto").hide();
			$("#bils_number_manual").show();
			
        }
    });
});

</script>
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
<script>

 $('#poscustomer1').val(localStorage.getItem('poscustomer')).select2({
            
            minimumInputLength: 1,
            data: [],
            initSelection: function (element, callback) {
                $.ajax({
                    type: "get", async: false,
                    url: "<?=admin_url('customers/getCustomer')?>/" + $(element).val(),
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
            $('#poscustomer1').on('select2-opening', function () {
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
                                url: "<?=admin_url('customers/suggestions')?>/?term=" + sct,
                                dataType: "json",
                                success: function (res) {
                                    if (res.results != null) {
                                        $('#poscustomer1').select2({data: res}).select2('open');
                                        $('.select2-input').removeClass('select2-active');
                                    } else {
                                        bootbox.alert('no_match_found');
                                        $('#poscustomer1').select2('close');
                                        $('#test').click();
                                    }
                                }
                            });
                        }, 500);
                    }
                });
            });

            $('#poscustomer1').on('select2-close', function () {
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


	/*  function display_keyboards() {

    $('.kb-text').keyboard({
        autoAccept: true,
        alwaysOpen: false,
        openOn: 'focus',
        usePreview: false,
        layout: 'custom',
        //layout: 'qwerty',
        display: {
            'bksp': "\u2190",
            'accept': 'return',
            'default': 'ABC',
            'meta1': '123',
            'meta2': '#+='
        },
        customLayout: {
            'default': [
            'q w e r t y u i o p {bksp}',
            'a s d f g h j k l {enter}',
            '{s} z x c v b n m , . {s}',
            '{meta1} {space} {cancel} {accept}'
            ],
            'shift': [
            'Q W E R T Y U I O P {bksp}',
            'A S D F G H J K L {enter}',
            '{s} Z X C V B N M / ? {s}',
            '{meta1} {space} {meta1} {accept}'
            ],
            'meta1': [
            '1 2 3 4 5 6 7 8 9 0 {bksp}',
            '- / : ; ( ) \u20ac & @ {enter}',
            '{meta2} . , ? ! \' " {meta2}',
            '{default} {space} {default} {accept}'
            ],
            'meta2': [
            '[ ] { } # % ^ * + = {bksp}',
            '_ \\ | &lt; &gt; $ \u00a3 \u00a5 {enter}',
            '{meta1} ~ . , ? ! \' " {meta1}',
            '{default} {space} {default} {accept}'
            ]}
        });
		
		
		
    $('.kb-pad').keyboard({
        restrictInput: true,
        preventPaste: true,
        autoAccept: true,
        alwaysOpen: false,
        openOn: 'click',
        usePreview: false,
        layout: 'custom',
        display: {
            'b': '\u2190:Backspace',
        },
        customLayout: {
            'default': [
            '1 2 3 {b}',
            '4 5 6 . {clear}',
            '7 8 9 0 %',
            '{accept} {cancel}'
            ]
        }
    });
    var cc_key = (site.settings.decimals_sep == ',' ? ',' : '{clear}');
    $('.kb-pad1').keyboard({
        restrictInput: true,
        preventPaste: true,
        autoAccept: true,
        alwaysOpen: false,
        openOn: 'click',
        usePreview: false,
        layout: 'custom',
        display: {
            'b': '\u2190:Backspace',
        },
        customLayout: {
            'default': [
            '1 2 3 {b}',
            '4 5 6 . '+cc_key,
            '7 8 9 0 %',
            '{accept} {cancel}'
            ]
        }
    });

 }  
 */

/*$('.kb-pad').keyboard({
        restrictInput: true,
        preventPaste: true,
        autoAccept: true,
        alwaysOpen: false,
        openOn: 'click',
        usePreview: false,
        layout: 'custom',
		maxLength: 4,
        display: {
            'b': '\u2190:Backspace',
        },
        customLayout: {
            'default': [
            '1 2 3 4',
            '5 6 7 8  ',
            ' 9 0 {b}',

            ' {accept} {cancel}'
            ]
        }
    });
	$('.kb-text').keyboard({
        autoAccept: true,
        alwaysOpen: false,
        openOn: 'focus',
        usePreview: false,
        layout: 'custom',
        display: {
            'bksp': "\u2190",
            'accept': 'return',
            'default': 'ABC',
            'meta1': '123',
            'meta2': '#+='
        },
        customLayout: {
            'default': [
            'q w e r t y u i o p {bksp}',
            'a s d f g h j k l {enter}',
            '{s} z x c v b n m , . {s}',
            '{meta1} {space} {cancel} {accept}'
            ],
            'shift': [
            'Q W E R T Y U I O P {bksp}',
            'A S D F G H J K L {enter}',
            '{s} Z X C V B N M / ? {s}',
            '{meta1} {space} {meta1} {accept}'
            ],
            'meta1': [
            '1 2 3 4 5 6 7 8 9 0 {bksp}',
            '- / : ; ( ) \u20ac & @ {enter}',
            '{meta2} . , ? ! \' " {meta2}',
            '{default} {space} {default} {accept}'
            ],
            'meta2': [
            '[ ] { } # % ^ * + = {bksp}',
            '_ \\ | &lt; &gt; $ \u00a3 \u00a5 {enter}',
            '{meta1} ~ . , ? ! \' " {meta1}',
            '{default} {space} {default} {accept}'
            ]}
        });
*/
 	$('.kb-text1').keyboard({
        autoAccept: true,
        alwaysOpen: false,
        openOn: 'focus',
        usePreview: false,
        layout: 'custom',
        display: {
            'bksp': "\u2190",
            'accept': 'return',
            'default': 'ABC',
            'meta1': '123',
            'meta2': '#+='
        },
        customLayout: {
            'default': [
            'q w e r t y u i o p {bksp}',
            'a s d f g h j k l {enter}',
            '{s} z x c v b n m , . {s}',
            '{meta1} {space} {cancel} {accept}'
            ],
            'shift': [
            'Q W E R T Y U I O P {bksp}',
            'A S D F G H J K L {enter}',
            '{s} Z X C V B N M / ? {s}',
            '{meta1} {space} {meta1} {accept}'
            ],
            'meta1': [
            '1 2 3 4 5 6 7 8 9 0 {bksp}',
            '- / : ; ( ) \u20ac & @ {enter}',
            '{meta2} . , ? ! \' " {meta2}',
            '{default} {space} {default} {accept}'
            ],
            'meta2': [
            '[ ] { } # % ^ * + = {bksp}',
            '_ \\ | &lt; &gt; $ \u00a3 \u00a5 {enter}',
            '{meta1} ~ . , ? ! \' " {meta1}',
            '{default} {space} {default} {accept}'
            ]}
        });
$(document).on('click','.change_table',function(e){	
        e.preventDefault();
        $('#change_split_id').val('');
        var change_split = $(this).attr("split");
		$('#table-change-Modal').show();
		$('#change_split_id').val(change_split);
    });	
$(document).on('click','.change_customer',function(e){	
        e.preventDefault();
        $('#change_split_id').val('');
        var change_split = $(this).attr("split");
		//console.log(55)
		$('#customer-change-Modal').show(); 
		$('#change_split_id').val(change_split);
    });
$(document).on('click', '.closemodal', function () {
    $('#changed_table_id').val('');
    $('#change_split_id').val('');
    $('#table-change-Modal').hide();
    $('#customer-change-Modal').hide(); 
});
$(document).on('click','#OrderChangeTable',function(){

     var change_split_id = $('#change_split_id').val(); 
      
      var changed_table_id =  $("#changed_table_id option:selected").val();
     if($.trim(changed_table_id) != '' && $.trim(changed_table_id) != 0){
        
        $.ajax({
            type: "POST",
            url:"<?=admin_url('pos/change_table_number_all');?>",                
            data: {change_split_id: change_split_id, changed_table_id: changed_table_id},
            dataType: "json",
            success: function (data) {
                if(data.msg == 'success'){
                         $('#table-change-Modal').hide(); 
                         //location.reload();
						   window.location.href = site.base_url+"pos/consolidate/order_table/?table="+changed_table_id;
                }else{
                    bootbox.alert('<?=lang('uanble_to_cancle');?>');
                    return false;
                }
            }    
        })
     }   
     else{
     	bootbox.alert('<?=lang('please_select_changing_table');?>');
        return false;
     }
});
$(document).on('click','#OrderChangeCustomer',function(){

     var change_split_id = $('#change_split_id').val(); 
      
      var changed_customer_id =  $("#poscustomer1").val();
     
     if($.trim(changed_customer_id) != '' && $.trim(changed_customer_id) != 0){
      
        $.ajax({
            type: "POST",
            url:"<?=admin_url('pos/change_customer_number');?>",                
            data: {change_split_id: change_split_id, changed_customer_id: changed_customer_id},
            dataType: "json",
            success: function (data) {
                if(data.msg == 'success'){
                         $('#customer-change-Modal').hide(); 
                         location.reload();
                }else{
                    bootbox.alert('<?=lang('uanble_to_cancle');?>');
                    return false;
                }
            }    
        })
     }   
     else{
     	bootbox.alert('<?=lang('please_select_changing_customer');?>');
        return false;
     }
});
$(document).ready(function(){
    $('.cancel-type').iCheck({
			checkboxClass: 'icheckbox_square-blue',
			radioClass: 'iradio_square-blue',
			increaseArea: '20%'
		});
});

$(document).on('click','.merge_bill',function(e){	
//alert('test');
        e.preventDefault();
        $('#merge_split_id').val('');
        var current_split = $(this).attr("split");
        var table_id = $(this).attr("table_id");
        $('#merge_split_id').val(current_split);
        $('#merge_table_id').val(table_id);
        $('.merge-group-list').empty();


         $.ajax({
            type: "GET",
            url:"<?=admin_url('pos/get_splits_for_merge');?>",                
            data: {current_split: current_split},
            dataType: "json",
            success: function (res) {
				var check_Box ='';
				$('.merge_table_id').html('');
				$('.merge-group-list').empty();
				if(res.data){
				$.each(res.data, function(i, item) {
					check_Box = "<label>"+"<input type='checkbox' class='merge' name='merge[]' value='" + item.split_id + "'/>" + item.name +"</label>"+ "<br/>";
		             //check_Box = "<input type='checkbox' class='merge'  id='merge_id'"+ table_id + "  name='merge[]' value='" + item.split_id + "'/>" + item.name + "<br/>";

					//alert(check_Box);
					$(check_Box).appendTo('.merge-group-list');
				});
				}else{
					$('.merge-group-list').text("No Order Found.")
				}
            }    
        });
		$('#splits-merge-Modal').show();
    });	 

$(document).on('click', '.closmergeemodal', function () {
    $('#merge_split_id').val('');
    $('#merge_table_id').val('');
    $('#splits-merge-Modal').hide();
    $('.merge-group-list').empty();
});

$(document).on('click','#Mergesplits',function(){
var checkedNum = $('input[name="merge[]"]:checked').length; 
var current_split = $('#merge_split_id').val();
var merge_table_id = $('#merge_table_id').val();
var merge_splits = [];
var i = 0;
if(checkedNum > 0){
       $('.merge:checked').each(function () {
           merge_splits[i++] = $(this).val();           
       });
       $.ajax({
            type: "POST",
            url:"<?=admin_url('pos/multiple_splits_mergeto_singlesplit_for_consolidate');?>",                
            data: {merge_splits: merge_splits, current_split: current_split, merge_table_id:merge_table_id},
            dataType: "json",
            success: function (data) {
                if(data.msg == 'success'){
                         $('#splits-merge-Modal').hide(); 
                        location.reload();
                }else{
                    bootbox.alert('<?=lang('uanble_to_merge');?>');
                    return false;
                }
            }    
        });   
}else{
	alert('Please select any one split');
	return false;
}

return false;
});

</script>
<script>
    /************ cancel all order items **************/
    function CancelAllOrderItems( table_id ,$remarks=0, splitid)
            {    
// alert(splitid);
                //clearTimeout(ajaxDatatimeout);
                $cancelQty = 'all';
                $('#order_table_id').val(table_id); 
                $('#split_table_id').val(splitid); 
                cancelAllorderPopup(table_id,$remarks,splitid);
			
            }
	   
            function cancelAllorderPopup(table_id,$remarks){
                if($remarks!=0){
                            $('#remarks').val('');
                            
                            $('#CancelAllorderModal').show();
                }else{
                    $msg = 'Are you sure want to cancel this order?';
		    bootbox.confirm({
			message: $msg,
			buttons: {
			    confirm: {
				label: 'Yes',
				className: 'btn-success'
			    },
			    cancel: {
				label: 'No',
				className: 'btn-danger'
			    }
			},
			callback: function (result) {
				var split_table_id = $('#split_table_id').val(); 
			    if (result) {				
				$.ajax({
				    type: "get",
				    url:"<?=admin_url('pos/cancel_all_order_items');?>",                
				    data: {table_id: table_id,split_table_id: split_table_id},
				    dataType: "json",
				    success: function (data) {
					if(data.msg == 'success'){
						//ajaxDatatimeout = setInterval(ajaxData, 6000);
						location.reload();      	                      	
					}else{
					    alert('not update waiter');
					}
				    }    
				}).done(function () {
				      
				});
				  
			    }else{
					       //requestBill(billid);
			    }
			    
			}
		    });
		}
	    }
    function send_kot($split_id) {
	$.ajax({
                        type: "post",
                        url:"<?=admin_url('pos/kot_print_copy/');?>"+$split_id,                
                        
                        success: function (data) {
                            bootbox.alert('sent to kot print');
                        }    
                    })
    }
    $(document).ready(function(){
	$(document).on('click','#cancel_allorderitem',function(){
	    $obj =$(this);
            	 var cancel_remarks = $('#cancel-remarks').val();
		 
            	 var table_id = $('#order_table_id').val(); 
            	 var split_table_id = $('#split_table_id').val(); 
            	 if($.trim(cancel_remarks) != ''){
		    $(this).attr('disabled',true);
		    $submit_text = $(this).text();
			
		   $(this).text('please wait...');
            	 	$.ajax({
                        type: "get",
                        url:"<?=admin_url('pos/cancel_all_order_items_consolidate');?>",                
                        data: {table_id:table_id,cancel_remarks: cancel_remarks,split_table_id:split_table_id},
                        dataType: "json",
                        success: function (data) {
			      $obj.attr('disabled',false);
			      $obj.text($submit_text);			    
                            if(data.msg == 'success'){
				
                            	     $('#CancelAllorderModal').hide(); 
									// ajaxDatatimeout = setInterval(ajaxData, 1000);
									 location.reload();      	                      	
                            }else{
				
                                alert('not cancelled');
                            }
                        }    
                    }).done(function () {
				      
					});
            	 } else{
		        $('#cancel-remarks').css('border','1px solid red');
		 }

            });
    })  
	    </script>
    
<?php unset($Settings->setting_id, $Settings->smtp_user, $Settings->smtp_pass, $Settings->smtp_port, $Settings->update, $Settings->reg_ver, $Settings->allow_reg, $Settings->default_email, $Settings->mmode, $Settings->timezone, $Settings->restrict_calendar, $Settings->restrict_user, $Settings->auto_reg, $Settings->reg_notification, $Settings->protocol, $Settings->mailpath, $Settings->smtp_crypto, $Settings->corn, $Settings->customer_group, $Settings->srampos_username, $Settings->purchase_code);?>
<script type="text/javascript">


var site = <?=json_encode(array('url' => base_url(), 'base_url' => admin_url('/'), 'assets' => $assets, 'settings' => $Settings,'pos_settings' => $pos_settings, 'dateFormats' => $dateFormats)) ?>;

 var KB = <?=$pos_settings->keyboard?>;

var lang = {
    unexpected_value: '<?=lang('unexpected_value');?>',
    select_above: '<?=lang('select_above');?>',
    r_u_sure: '<?=lang('r_u_sure');?>',
    bill: '<?=lang('bill');?>',
    order: '<?=lang('order');?>',
    total: '<?=lang('total');?>',
    items: '<?=lang('items');?>',
    discount: '<?=lang('discount');?>',
    order_tax: '<?=lang('order_tax');?>',
    grand_total: '<?=lang('grand_total');?>',
    total_payable: '<?=lang('total_payable');?>',
    rounding: '<?=lang('rounding');?>',
    merchant_copy: '<?=lang('merchant_copy');?>'
};
</script>
	
<!-- new payment screen  start -->
<script type="text/javascript">

    $('#paymentModal').on('shown.bs.modal', function(e) {
        $('#userd_tender_list').html('');
        var loyalty_available = $('.loyaltyavailable').val();          
        /*$('#payment-loyalty').prop('disabled', false);
        if(loyalty_available == 0)
        {            
            $('#payment-loyalty').prop('disabled', true).css('opacity',0.5);
        }
        else{
            $('#payment-loyalty').prop('disabled', false);
        }*/
        // $("button.payment_type").val("cash").click();
        $('#payment-cash').val('cash');
	
//        if($('#payment-cash').val() == 'cash'){
//            $('#payment-cash').trigger('click');                  
//            $('#payment-cash').addClass('active');   
//        }
//	if (rt_cc!='' && rt_cc!=undefined) {
//	    $('#payment-CC').trigger('click');                  
//            $('#payment-CC').addClass('active');   
//	}
//	if (rt_credit!='' && rt_credit!=undefined) {
//	    $('#payment-credit').trigger('click');                  
//            $('#payment-credit').addClass('active');   
//	}
	if (rt_loyalty!='' && rt_loyalty!=undefined) {
	    $('#payment-loyalty').trigger('click');                  
            $('#payment-loyalty').addClass('active');
	    
	}
	if (rt_credit!='' && rt_credit!=undefined) {
		console.log(rt_credit);
	    $('#payment-credit').trigger('click');                  
            $('#payment-credit').addClass('active');   
	}
	if($('#payment-cash').val() == 'cash'){
            $('#payment-cash').trigger('click');                  
            $('#payment-cash').addClass('active');   
        }
	if (rt_cc!='' && rt_cc!=undefined) {
	    $('#payment-CC').trigger('click');                  
            $('#payment-CC').addClass('active');   
	}
	

        <?php
            foreach($currency as $currency_row){
                $default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
                if($currency_row->code == $default_currency_data->code){  
                ?>                
                <?php
                }else{                
                ?>
                // $('#amount_<?php echo $currency_row->code; ?>_1').val('');
                <?php
                }
            } ?>
        });
$(document).on('click', '#reset', function () {    
      $('#userd_tender_list').html('');
      $('.crd_exp,.cc_no').val('');      
      $(".amounts").val('');
      $('.amounts').trigger('blur');
      calculateTotalsbill();
});

$(document).on('click', '#reset1', function () {    
   
	$("#mcode").val('');
	$("#mname").val('');
	$("#mquantity").val('');
	$("#mprice").val('');
	$("#mnet_price").val('');
   
});

//$(document).on('click', '#posTable', function () {    
   
	//$("#posable").val('');
	
   
//});
        $(document).on('click', '.payment_type', function () {             
            // $('#clear-cash-notes').click();
                
                $index = $( this ).attr('data-index');                
                $data_id = $( this ).attr('data_id');                
                <?php
                foreach($currency as $currency_row){
                    $default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
                    if($currency_row->code == $default_currency_data->code){
                    ?>
                    // $('#amount_<?php echo $currency_row->code; ?>_'+$index).val('');
                    $('#amount_<?php echo $currency_row->code; ?>_'+$index).attr('readonly', false);
                    <?php
                    }else{
                    ?>
                    // $('#amount_<?php echo $currency_row->code; ?>_'+$index).val('');
                    $('#amount_<?php echo $currency_row->code; ?>_'+$index).attr('readonly', false);            
                    <?php
                    }
                } 
               ?>  
        
                /*$('#amount_'+$index).val('');
                $('#amount_USD_'+$index).val('');*/
                var p_val = $(this).val(),
                id = $(this).attr('id'),
                pa_no = id.substr(id.length - 1);            
                $('#rpaidby').val(p_val);
            if (p_val == 'cash') {    
            
                $('.payment_type.active').removeClass('active');
                $('#payment-cash').addClass('active');  
                $('#amount_<?php echo $currency_row->code; ?>_'+$index).attr('readonly', false);  

                $('.cash').show();               
                $('#base_currency_'+ $index).show();   
                $('#multi_currency_'+ $index).show();
                $('#lc_'+ $index).hide();                 
                $('#CC_'+ $index).hide(); 
                $('.credit').hide();   
                $('.loyalty').hide();  
                $('.CC').hide();    

                <?php
                foreach($currency as $currency_row){
                $default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
                if($currency_row->code == $default_currency_data->code){
                ?>
                $('#amount_<?php echo $currency_row->code ?>_'+$index).focus();
                <?php
                }else{ ?>
                // $('.amount_<?php echo $currency_row->code ?>_'+$index).val('');
                <?php } }
                ?>     
            } else if (p_val == 'credit') {       
                $('.payment_type.active').removeClass('active');
                $('#payment-'+$index).addClass('active');  
                $('#amount_<?php echo $currency_row->code; ?>_'+$index).attr('readonly', false);    
                
                $('.credit').show();    
                $('#base_currency_'+ $index).show();           
                $('#multi_currency_'+ $index).hide();
                $('#lc_'+ $index).hide();                 
                $('#CC_'+ $index).hide();
                $('.CC').hide();    
                $('.loyalty').hide(); 
                $('.cash').hide(); 
                <?php
                foreach($currency as $currency_row){
                $default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
                if($currency_row->code == $default_currency_data->code){
                ?>
                $('#amount_<?php echo $currency_row->code ?>_' + $index).focus();
                <?php
                }else{
                ?>
                // $('.amount_<?php echo $currency_row->code ?>_'+pa_no).val('');
                
                <?php
                }
                }
                ?>                              
            } else if (p_val == 'CC') {  

                $('.payment_type.active').removeClass('active');
                $('#payment-'+$index).addClass('active');

                 $('.CC').show();     
                 $('#CC_'+ $index).show();     
                 $('#base_currency_'+ $index).show();                      
                 $('#multi_currency_'+ $index).hide();
                 $('#lc_'+ $index).hide();
                 $('.credit').hide(); 
                 $('.loyalty').hide(); 
                 $('.cash').hide(); 

                <?php
                    foreach($currency as $currency_row){
                        $default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
                        if($currency_row->code == $default_currency_data->code){
                        ?>
                        $('#amount_<?php echo $currency_row->code ?>_' + $index).focus();
                        <?php
                        }else{
                        ?>
                        // $('.amount_<?php echo $currency_row->code ?>_'+pa_no).val('');                        
                        <?php
                        }
                    }
                ?>
            } else if (p_val == 'loyalty') {

                $('.payment_type.active').removeClass('active');
                $('#payment-'+$index).addClass('active');

                 $('.loyalty').show();               
                 $('#lc_'+ $index).show();               
                 $('#base_currency_'+ $index).show();
                 $('#multi_currency_'+ $index).hide();
                 $('#CC_'+ $index).hide();                 
                 $('.credit').hide(); 
                 $('.CC').hide(); 
                 $('.cash').hide(); 

                $('#amount_<?php echo $currency_row->code; ?>_'+$index).focus();
                $('#amount_<?php echo $currency_row->code; ?>_'+$index).attr('readonly', true);
                $('#loyalty_points_' + $index).focus();
                $('#loyaltypoints').val(0);                
                $('#lc_details_' + $index).html(''); 
                $('#lc_reduem_' + $index).html('');

                var loyalty_customer_id = $('#loyalty_customer').val();
                // alert(loyalty_customer_id);

                var customer_id ='';
                var bill_customer_id = $('.customer_id').val(); 
                if(loyalty_customer_id){
                    customer_id = loyalty_customer_id;
                }else{
                    customer_id =  bill_customer_id;
                }
                // alert(bill_customer_id);
                var payid = $(this).attr('id'),
                    id = payid.substr(payid.length - 1);
                if (customer_id != '') {
                    $.ajax({
                        type: "get", async: false,
                        url: site.base_url + "pos/get_loyalty_points/" + customer_id,
                        dataType: "json",
                        success: function (data) { 

                            if (data.points === false && data.redemption === 0) {     
                            
                                $('#loyalty_points_' + $index).parent('.form-group').addClass('has-error');
                                bootbox.alert('Not Eligible To use Loyalty Card.');
                            } else if ((data.points.total_points == 0) || (data.points.loyalty_card_no == '')) { 
                                
                                bootbox.alert('Right Now Not Eligible to Loyalty,Please try after some visit.');
                                ('#lc_details_' + $index).html(''); 
                                $('#lc_reduem_' + $index).html(''); 
                            } else {          
                                                          
                                $('#loyaltypoints').val(data.points.total_points);
                                $('#lc_details_' + $index).html('<small>Card No: ' + data.points.loyalty_card_no + '&nbsp;&nbsp;Value: ' + data.points.total_points +'</small>'); 

                                $('#lc_reduem_' + $index).html('<small>Redemption: ' + parseFloat(data.redemption.redempoint) + '&nbsp;&nbsp;Amount: ' + parseFloat(data.redemption.amount) +'</small>'); 

                                $('#loyalty_points_' + $index).parent('.form-group').removeClass('has-error');                                 
                            }
                        }
                    });
                }               
             }        
            
             var currentYear = new Date().getFullYear();  

                for (var i = 1; i <= 20; i++ ) {
                    $(".pcc_year").append(

                        $("<option></option>")
                            .attr("value", currentYear)
                            .text(currentYear)

                    );
                    currentYear++;
                }
           $(this).parents('.payment').find('input').val('');
            $('#pcc_month_'+$index).prepend('<option value="">Month</option>');
            $('#pcc_year_'+$index).prepend('<option value="">Year</option>');
            $('#pcc_month_'+$index).val('');
            $('#pcc_year_'+$index).val('');            
            $amount = $('#balance_amt_<?=$default_currency_data->code?>').val();              
            $amount = parseFloat($amount.match(/([0-9]*\.[0-9]+|[0-9]+)/));
            
            $('#amount_<?=$default_currency_data->code?>_'+$index).removeClass('credit-max');
            $(this).parent('.form-group').find('.available-c-limit').remove();
            if ($( this ).val()=='credit') {                
                $('#amount_<?=$default_currency_data->code?>_'+$index).addClass('credit-max');
                $inputCredit = 0;
                $('.credit-max').each(function(n,v){
                if($(this).attr('id')!="amount_<?=$default_currency_data->code?>_"+$index){
                    $inputCredit += ($(this).val()=='')?0:parseFloat($(this).val());
                }
                });
                $creditlimit = parseFloat($('#credit_limit').val())-parseFloat($inputCredit);                
             if ($('#customer_type').val()=='none'){
                bootbox.alert("Not allowed to use Credit option");
                $(this).parent('.form-group > .available-c-limit').empty();
                return false;
            }

                if($('#customer_type').val()=='prepaid' && $amount>$creditlimit){
                $amount = $creditlimit;
                }
                $amount = ($amount!=0)?$amount:'';
                $(this).parent('.form-group').append('<span class="available-c-limit">Available Customer Credit Limit $'+$('#credit_limit').val()+'</span>');
            }

            $('#amount_<?=$default_currency_data->code?>_'+$index).removeClass('creditcard-max');            
            if ($( this ).val()=='CC') {                
                    $('#amount_<?=$default_currency_data->code?>_'+$index).addClass('creditcard-max');
                    $inputCreditcard = 0;

                    $('.creditcard-max').each(function(n,v){
                        if($(this).attr('id')!="amount_<?=$default_currency_data->code?>_"+$index){
                            $inputCreditcard += ($(this).val()=='')?0:parseFloat($(this).val());
                        }
                    });                
            }

           // console.log($amount);
                // $('#amount_USD_cash').val('');
		
                if ((p_val != 'loyalty') && (p_val == 'cash')) {                    
                  if($amount>0){                    
                     if($('#amount_<?=$default_currency_data->code?>_cash').val() == ''){
			if (rt_cash!='' && rt_cash!=undefined) {
			    $amount = rt_cash;
                      }
		          $('#amount_<?=$default_currency_data->code?>_cash').val($amount);
                    }
		  }
                }else if ((p_val != 'loyalty') && (p_val == 'CC')) {                    
                  if($amount>0){
		    if (rt_cc!='' && rt_cc!=undefined) {
			    $amount = rt_cc;
		    }
                     if($('#amount_<?=$default_currency_data->code?>_CC').val() == ''){
			if (rt_cc!='' && rt_cc!=undefined) {
			    $amount = rt_cc;
			   			   
			}
                         $('#amount_<?=$default_currency_data->code?>_CC').val($amount);
			
                      }
                    }
                }else if (p_val == 'loyalty'){
                    $('#loyalty_points_cash').focus();
		    if (rt_loyalty!='' && rt_loyalty!=undefined) {
			    $('#loyalty_points_loyalty').val(rt_loyalty);
			    $('.loyalty_points').trigger('change');
		    }
		    
                } else{
		    if (rt_credit!='' && rt_credit!=undefined) {
			    $('#amount_<?=$default_currency_data->code?>_credit').val(rt_credit);
			    
		    }
		}
		$('.amounts').trigger('blur');
        });
 

            $(document).on('change', '.loyalty_points', function () {
            var loyaltypoints = $("#loyaltypoints").val();             
            var redemption = $(this).val() ? $(this).val() : 0;
            var customer_id = $("#customer_id").val();    
            $('#loyalty_used_points').val(0);             
            var payid = $(this).attr('idd'); 
            if(parseFloat(loyaltypoints) == 0){    
                 bootbox.alert('Gift card number is incorrect or expired.');    
                 $('#loyalty_points_' + payid).focus().val('');            
             }else if (parseFloat(redemption) <= parseFloat(loyaltypoints)) {
                $bal_amount = $('#balance_amt_<?=$default_currency_data->code?>').val();
                 $bal_amount = parseFloat($bal_amount.match(/([0-9]*\.[0-9]+|[0-9]+)/));
                    $.ajax({
                        type: "get", async: false,
                        url: site.base_url + "pos/validate_loyalty_card/",
                        dataType: "json",
                         data: {
                            redemption: redemption,                        
                            customer_id: customer_id,
                            bal_amount: $bal_amount,
                        }, 
                        success: function (data) {
                            if (data === false) {          
                                 bootbox.alert('Right Now Not Eligible to use this card number,Please try after some visit.');
                                 $('#loyalty_points_' + payid).focus().val('');                                  
                                 $('#amount_<?php echo $currency_row->code; ?>_'+payid).val('');
                            } else if(parseFloat(data.total_redemamount) > parseFloat($bal_amount)) {      
                                    bootbox.alert('Already seleted in other payment method Plz check it (OR) use only Balance amount only.');
                                    $('#lc_reduem_' + payid).html('<small>Redemption: ' + parseFloat(data.redemption) + '&nbsp;&nbsp;Amount: ' + parseFloat(data.amount) +'</small>');
                                    $('#loyalty_points_' + payid).focus().val('');
                                    $('#amount_<?php echo $currency_row->code; ?>_'+payid).val('');
                               }else{                                                                                  
                                    // $('#loyalty_points_' + id).parent('.form-group').removeClass('has-error');
                                    $('#lc_reduem_' + payid).html('<small>Redemption: ' + parseFloat(data.redemption) + '&nbsp;&nbsp;Amount: ' + parseFloat(data.amount) +'</small>'); 
                                    $('#loyalty_used_points').val(redemption); 
                                    $('#amount_<?php echo $currency_row->code; ?>_'+payid).focus().val(data.total_redemamount);
                                    $('#amount_<?php echo $currency_row->code; ?>_'+payid).attr('readonly', true);
                              }
                        }
                    });
                }else{
                    
                    bootbox.alert('Please Enter less than your points or equal.');  
                     $('#loyalty_points_' + payid).focus().val('');
                     $('#amount_<?php echo $currency_row->code; ?>_'+payid).val('');
                    
                }           
        });

        
        var customer_id = $('.customer_id').val();  
        $('#loyalty_customer').val(customer_id).select2({
            
            minimumInputLength: 1,
            data: [],
            initSelection: function (element, callback) {
                $.ajax({
                    type: "get", async: false,
                    url: "<?=admin_url('customers/getCustomer')?>/" + $(element).val(),
                    dataType: "json",
                    success: function (data) {
                        callback(data[0]);
                    }
                });
            },
            ajax: {
                url: site.base_url + "pos/loyalty_customer",
                dataType: 'json',
                quietMillis: 15,
                data: function (term, page) {
                    return {
                        term: term,
                        limit: 1
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
            $('#loyalty_customer').on('select2-opening', function () {
                sct = '';
                $('.select2-input').addClass('kb-pad-all');  
                display_keyboards();       
                $('select, .select').select2('destroy');    
                // alert($(this).next().parent().parent().html());                    
               /*   $('input[name="default"]').addClass('kbtext');*/
                $('.select2-input').bind('change.keyboard', function (e, keyboard, el) {
                    
                    if (el && el.value != '' && el.value.length > 0 && sct != el.value) {
                        sct = el.value;
                    }
                    if(!el && sct.length > 0) {
                        $('.select2-input').addClass('select2-active');
                         // setTimeout(function() {
                            $.ajax({
                                type: "get",
                                async: false,
                                url: "<?=admin_url('pos/loyalty_customer')?>/?term=" + sct,
                                dataType: "json",
                                success: function (res) {
                                    if (res.results != null) {                                                          
                                        // $('#loyalty_customer').select2({data: res}).select2('open');
                                        $('.select2-input').removeClass('select2-active');
                                    } else {
                                         bootbox.alert('no_match_found');
                                        $('#loyalty_customer').select2('close');
                                        // $('#test').click();
                                    }
                                }
                            });
                         // }, 500);
                    }
                });
                

            });

            $('#loyalty_customer').on('select2-close', function () {
                $('.select2-input').removeClass('kb-pad-all');                
                $('#test').click();
                $('select, .select').select2('destroy');
                $('select, .select').select2({minimumResultsForSearch: 7});
            });
            $(document).bind('click', '#test', function () {
                var kb = $('#test').keyboard().getkeyboard();
                kb.close();
            });
        }    


        /*if($('.ui-keyboard-keyset').is(':visible')) {
            alert();
        }*/


        /*$(document).on('.ui-keyboard-keyset:visible', function () {
            alert();
        });*/
        
        // $("#loyalty_customer").on("change", function (e) {
            // $(document).on('change', '#loyalty_customer', function () {
        $(document).on('change',"#loyalty_customer", function () {
            var loyalty_customer_id = $(this).val();
            var myVar = $('.payment_type.active').val();               
            if(myVar =='loyalty'){

            var customer_id = loyalty_customer_id;  
                var payid = $(this).attr('id'),
                    id = payid.substr(payid.length - 1);
                if (customer_id != '') {
                    $.ajax({
                        type: "get", async: false,
                        url: site.base_url + "pos/get_loyalty_points/" + customer_id,
                        dataType: "json",
                        success: function (data) {                             
                            if (data === false) {
                                $('#loyalty_points_' + $index).parent('.form-group').addClass('has-error');
                                bootbox.alert('Not Eligible To use Loyalty Points.');
                            } else if ((data.points.total_points == 0) || (data.points.loyalty_card_no == '')) {
                                bootbox.alert('Right Now Not Eligible to Loyalty,Please try after some visit.');
                                ('#lc_details_' + $index).html(''); 
                                $('#lc_reduem_' + $index).html('');
                            } else {
                                
                                $('#loyaltypoints').val(data.points.total_points);
                                $('#lc_details_' + $index).html('<small>Card No: ' + data.points.loyalty_card_no + '&nbsp;&nbsp;Value: ' + data.points.total_points +'</small>'); 
                                $('#lc_reduem_' + $index).html('<small>Redemption: ' + parseFloat(data.redemption.redempoint) + '&nbsp;&nbsp;Amount: ' + parseFloat(data.redemption.amount) +'</small>'); 
                                $('#loyalty_points_' + $index).parent('.form-group').removeClass('has-error');                                 
                            }
                        }
                    });               
               }
            }
        });        
        
        
</script>
<!-- new payment screen end  -->











<script type="text/javascript">
    var username = '<?=$this->session->userdata('username');?>', order_data = '', bill_data = '';
    var rt_cash = '';
	    var rt_credit ='';
	    var rt_cc ='';
	    var rt_loyalty='';
    $(document).ready(function () {

        $(document).on('change', '#posbiller', function () {
            var sb = $(this).val();
            $.each(billers, function () {
                if(this.id == sb) {
                    biller = this;
                }
            });
            $('#biller').val(sb);
        });

        <?php for ($i = 1; $i <= 5; $i++) {?>
       
       /* $('#paymentModal').on('select2-close', '#paid_by_<?=$i?>', function (e) {
            $('#paid_by_val_<?=$i?>').val($(this).val());
        });
        $('#paymentModal').on('change', '#pcc_no_<?=$i?>', function (e) {
            $('#cc_no_val_<?=$i?>').val($(this).val());
        });
        $('#paymentModal').on('change', '#pcc_holder_<?=$i?>', function (e) {
            $('#cc_holder_val_<?=$i?>').val($(this).val());
        });
        $('#paymentModal').on('change', '#gift_card_no_<?=$i?>', function (e) {
            $('#paying_gift_card_no_val_<?=$i?>').val($(this).val());
        });
        $('#paymentModal').on('change', '#pcc_month_<?=$i?>', function (e) {
            $('#cc_month_val_<?=$i?>').val($(this).val());
        });
        $('#paymentModal').on('change', '#pcc_year_<?=$i?>', function (e) {
            $('#cc_year_val_<?=$i?>').val($(this).val());
        });
        $('#paymentModal').on('change', '#pcc_type_<?=$i?>', function (e) {
            $('#cc_type_val_<?=$i?>').val($(this).val());
        });
        $('#paymentModal').on('change', '#pcc_cvv2_<?=$i?>', function (e) {
            $('#cc_cvv2_val_<?=$i?>').val($(this).val());
        });
        $('#paymentModal').on('change', '#cheque_no_<?=$i?>', function (e) {
            $('#cheque_no_val_<?=$i?>').val($(this).val());
        });
        $('#paymentModal').on('change', '#payment_note_<?=$i?>', function (e) {
            $('#payment_note_val_<?=$i?>').val($(this).val());
        });*/
        <?php }
        ?>
		<?php
		$default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
		?>
		var currency_json = <?php echo json_encode($currency); ?>;
		var default_currency_rate = <?php echo $default_currency_data->rate; ?>;
		var default_currency_code = '<?php echo $default_currency_data->code; ?>';
		
		<?php
			foreach($currency as $currency_row){
			$default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
			if($currency_row->code == $default_currency_data->code){
			?>
			var gtotal_<?php echo $currency_row->code; ?> = 0;
			<?php
			}else{
			?>
			 var gtotal_<?php echo $currency_row->code; ?> = 0;
			<?php
			}
			?>
			<?php
			}
			?>
	    
	    function payment_popup(){
            $('#paymentModal').css('overflow-y', 'scroll');
	        $('#paymentModal').appendTo("body").modal('show', {backdrop: 'static', keyboard: false});  
            $('.balance_amount').val('');
            $(".amounts").val('');
            var billid = $thisObj.siblings('.billid').val();                         
            var customer_type = $thisObj.siblings('.customer-type').val(); 
            var company_id = $thisObj.siblings('.company-id').val();
	        var allow_loyalty = $thisObj.siblings('.customer-allow-loyalty').val(); 
            var ordersplit = $thisObj.siblings('.order_split').val();
            var salesid = $thisObj.siblings('.salesid').val(); 
            var grandtotal = $thisObj.siblings('.grandtotal').val();
            var credit_limit = $thisObj.siblings('.credit-limit').val();
	    // rough tender start//
	    rt_cash = '';rt_credit='';rt_cc='';rt_loyalty='';
	    
	    rt_cash = $thisObj.siblings('.rt-cash').val(); 
            rt_credit = $thisObj.siblings('.rt-credit').val();
            rt_cc = $thisObj.siblings('.rt-CC').val();
	    rt_loyalty = $thisObj.siblings('.rt-loyalty').val();
	    // rough tender - end //
	    
			var customer_name = $thisObj.siblings('.customer-name').val();
	        $('#new_customer_name').text(customer_name);
			
	        //console.log(credit_limit)
            var count = $thisObj.siblings('.totalitems').val(); 
            $('.bill_id').val(billid);
            $('.sales_id').val(salesid);
            $('.total').val(grandtotal);
            $('.order_split_id').val(ordersplit);
            $('.credit_limit').val(credit_limit);

            if(allow_loyalty==0){
                 $('#payment-loyalty').hide();
            }
            if (customer_type =='none' || customer_type==undefined || customer_type==0 ) {                
                $('#payment-credit').hide();
            }
			
            // $('.loyalty_available').val(loyalty_available);
            $('.customer_type').val(customer_type);
	        $('.company_id').val(company_id);

            var twt = formatDecimal(grandtotal);
            $('#bill_amount').text(formatMoney(grandtotal));
	      //  console.log('grandtotal-'+grandtotal)
          //  console.log('bil-'+billid);
            if (count == 0) {
                bootbox.alert('<?=lang('x_total');?>');
                return false;
            }
			
			<?php
            $exchange_curr_code = $this->site->getExchangeCurrency($this->Settings->default_currency);
            $exchange_rate = $this->site->getExchangeRatey($this->Settings->default_currency);
            
			foreach($currency as $currency_row){
		      	$default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
        			if($currency_row->code == $default_currency_data->code){
        			?>
        			 gtotal_<?php echo $currency_row->code; ?> = (parseFloat(twt) / parseFloat(<?php echo $currency_row->rate; ?>)) * <?php echo $default_currency_data->rate; ?>;

                    var decimals = twt - Math.floor(twt);
                    decimals = decimals.toFixed(2);
                    var currency_id = <?php echo $currency_row->id;?>;
                    var  $exchange_curr_code = "<?php echo $exchange_curr_code;?>"; 
                    var  $exchange_rate = "<?php echo $exchange_rate;?>";                    
                    if(currency_id == "<?php echo $this->Settings->default_currency;?>" && $exchange_rate != '' ){
                        $decimals = decimals/ $exchange_rate;
                        $decimals =  Math.round($decimals / 100) * 100;
                         var $riel = '('+$exchange_curr_code+($decimals)+')';
                    }
                    else{
                        var $riel = '';
                    }

                    $('#twt_<?php echo $currency_row->code; ?>').text(formatMoney(gtotal_<?php echo $currency_row->code; ?>)+''+$riel);

        			 $('#paid_amt_<?php echo $currency_row->code; ?>').val(formatDecimal(gtotal_<?php echo $currency_row->code; ?>));
        			<?php
        			}else{ ?>
                var  $exchange_curr_code = "<?php echo $exchange_curr_code;?>"; 
    			gtotal_<?php echo $currency_row->code; ?> = (parseFloat(twt) / parseFloat(<?php echo $currency_row->rate; ?>)) * <?php echo $default_currency_data->rate; ?>;
                     $final_amt = twt/ <?php echo $currency_row->rate ?>;
                     $final_amt =  Math.round($final_amt / 100) * 100;
                    $amt =$exchange_curr_code+$final_amt;
                    
    			$('#twt_<?php echo $currency_row->code; ?>').text($amt);
                $('#paid_amt_<?php echo $currency_row->code; ?>').val(formatDecimal(gtotal_<?php echo $currency_row->code; ?>));
    			<?php } ?>
			<?php } ?>
			
            $('#item_count').text(count);
            //$('#paymentModal').appendTo("body").modal('show');
			<?php
			foreach($currency as $currency_row){
			$default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
			if($currency_row->code == $default_currency_data->code){	
			?>
			$('#amount_<?php echo $currency_row->code; ?>_1').focus().val('');
			<?php
			}else{
			?>
            $('#amount_<?php echo $currency_row->code; ?>_1').val('');
			<?php
			}
			}
			?>
	    }
		
	  // $(document).on('click', '.edit1', function(){
		
		// $("button").attr("disabled", "disabled");
	//  setTimeout(function() {
		   //var that = this;
   // $(this).attr("disabled", true);
    //setTimeout(function() { enableedit1(that) }, 1000);
          // $("button").removeAttr("disabled");      
       //}, 9000);
		//});

	    $(document).on('change', '#choose-discount', function(){
		$('#discount-name').text($('#choose-discount option:selected').text());
	    });
	    $(document).on('click', '.request_bil', function(){
			
      //$(this).prop('disabled', true);
	  
	  $("button").attr("disabled", "disabled");
	  setTimeout(function() {
           $("button").removeAttr("disabled");      
       }, 1000);
	  
		$thisObj = $(this);
		var billid = $(this).parents('.payment-list-container').find('.billid').val();
        <?php if($pos_settings->discount_popup_screen_in_bill_print == 0) :?>
            requestBill(billid);
			//alert(1);
			
            return false;
        <?php endif; ?>  

		var ordersplit = $(this).parents('.payment-list-container').find('.order_split').val();
		var salesid = $(this).parents('.payment-list-container').find('.salesid').val(); 
		var grandtotal = $(this).parents('.payment-list-container').find('.grandtotal').val(); 
		var count = $(this).parents('.payment-list-container').find('.totalitems').val();        
		$url = '<?=admin_url().'pos/checkCustomerDiscount'?>';
		$.ajax({
			url: $url,
			type: "get",
			data: {bill_id:billid},
			dataType: "json",
			success:function(data){
			    
			    if (data.unique_discount == 0) {			       
			    var discounttype =  "<?php echo $Settings->customer_discount ?>";                
				if("<?php echo $Settings->customer_discount ?>" == "customer" ) {                 
				$dropdown = '<select id="choose-discount">';
				$dropdown +='<option value="0">No Discount</option>';
				$.each( data.all_dis, function( index, value ){
				    $selected = (data.cus_dis.customer_discount_id == value.id)?"selected='selected'":"";
				    $dropdown +='<option value="'+value.id+'" '+$selected+'>'+value.name+'</option>';
				});
				$dropdown +='</select>';
				if (data.cus_dis.customer_discount_id != 0) {
					$msg = "<div id='discount-confirm-msg'>Do You want to apply \"<span id='discount-name'>"+data.cus_dis.name+"</span>\"?</div>";
				} else {
					$msg = "<div id='discount-confirm-msg'>Do You want to apply any discount?</div>";
				}				
				    bootbox.confirm({
					closeButton: true,
					message: $dropdown+$msg,					
					 buttons: {
					     confirm: {
						 label: 'Apply',
						 className: 'btn-success'
                         
					     },
					     cancel: {
						 label: 'Cancel',
						 className: 'btn-danger'
					     }
					 },
					 callback: function (result) {
					   bootbox.hideAll();$('.modal-backdrop').remove();
					     if (result) {
						dis_id  = $('#choose-discount').val();
						 $.ajax({
						     url: '<?=admin_url().'pos/DINEINupdateBillDetails'?>',//updateBillDetails
						     type: "GET",
						     data: {bill_id:billid,dis_id:dis_id},
						     dataType: "json",
						     success:function(data){

							 if (!data.no_discount) {
							     $thisObj.parents('.payment-list-container').find('.grandtotal').val(data.amount);
							     $thisObj.siblings('.grandtotal_req').val(data.amount);
							 }
							 requestBill(billid);//payment_popup($thisObj);
						     }
						 });
					     }else{  requestBill(billid);}
					     
					 }
				     });
				    return false;
			       }else{ requestBill(billid)}
			    }else{ requestBill(billid)}
			    
			}
		    });
			
			$(this).prop('disabled', true);
	    });
		
		$(document).on('click', '.request_bil_new,.rough-tender-payment', function(){


		    /*$(".well-sm:not(:first)").remove();
            $('.close-payment').trigger('click');
            var pa = 1;
            var phtml = $('#payments').html(),
                    update_html = phtml.replace(/_1/g, '_' + pa);
                update_html= update_html.replace(/data-index="1"/g,'data-index="'+pa+'"');
                calculateTotals();
                pa--;*/
			
			$thisObj = $(this);            
			if ($thisObj.attr('data-item')=="rough-tender") {
			    $('#pos-payment-form').prepend('<input type="hidden" name="rough_tender" value=1 class="post-rough-tender">');
			   // $('.taxation_settings').hide();                
                  <?php if($pos_settings->discount_popup_screen_in_rough_payment == 0) :?>
                    payment_popup($thisObj);
                    return false;
                <?php endif; ?>  

			}else{                
                $('.taxation_settings').show();
                $('.post-rough-tender').remove();
                <?php if($pos_settings->discount_popup_screen_in_payment == 0) :?>
                    payment_popup($thisObj);
                   return false;
               <?php endif; ?>  
			}
			
			var billid = $(this).parents('.payment-list-container').find('.billid').val();
			var ordersplit = $(this).parents('.payment-list-container').find('.order_split').val();
			var salesid = $(this).parents('.payment-list-container').find('.salesid').val(); 
            var grandtotal = $(this).parents('.payment-list-container').find('.grandtotal').val(); 
            var customer_id = $(this).parents('.payment-list-container').find('.customer-id').val();
			var loyalty_available = $(this).parents('.payment-list-container').find('.loyalty_available').val();
            $('.customer_id').val(customer_id);            
            $('.loyaltyavailable').val(loyalty_available);            
			var count = $(this).parents('.payment-list-container').find('.totalitems').val();
			$url = '<?=admin_url().'pos/DINEINcheckCustomerDiscount'?>';
			$.ajax({
			url: $url,
			type: "POST",
			data: {bill_id:billid},
			dataType: "json",
			success:function(data){

			    if (data.unique_discount == 0) {	
			     //console.log(data);			       
                                      
                 if("<?php echo $Settings->customer_discount ?>" == "customer" ) {            
    			 $dropdown = '<select id="choose-discount">';
    			 $dropdown +='<option value="0">No Discount</option>';
				$.each( data.all_dis, function( index, value ){
				    $selected = (data.cus_dis.customer_discount_id == value.id)?"selected='selected'":"";
				    $dropdown +='<option value="'+value.id+'" '+$selected+'>'+value.name+'</option>';
				});
				$dropdown +='</select>';
				if (data.cus_dis.customer_discount_id != 0) {
					$msg = "<div id='discount-confirm-msg'>Do You want to apply \"<span id='discount-name'>"+data.cus_dis.name+"</span>\"?</div>";
				} else {
					$msg = "<div id='discount-confirm-msg'>Do You want to apply any discount?</div>";
				}
				    bootbox.confirm({
					message: $dropdown+$msg,					
					 buttons: {
					     confirm: {
						 label: 'Apply',
						 className: 'btn-success'
                         
					     },
					     cancel: {
						 label: 'Cancel',
						 className: 'btn-danger'
					     }
					 },
					 callback: function (result) {					   
					     if (result) {
						dis_id  = $('#choose-discount').val();
						 $.ajax({
						     url: '<?=admin_url().'pos/DINEINupdateBillDetails'?>',
						     type: "GET",
						     data: {bill_id:billid,dis_id:dis_id},
						     dataType: "json",
						     success:function(data){
								 if (!data.no_discount) {
									 $thisObj.parents('.payment-list-container').find('.grandtotal').val(data.amount);
									 $thisObj.siblings('.grandtotal_req').val(data.amount);
									 payment_popup($thisObj);
								 }else{
									payment_popup($thisObj);
								 }
						     }
						 });
					     }else{  
						 	payment_popup($thisObj);
						}
					     
					 }
				     });
				    return false;
			       
			    }else{ 
                    payment_popup($thisObj);
                }
                }else{ 
                    payment_popup($thisObj);
                }
			}
		    });
		});
		
	    
        $('#paymentModal').on('show.bs.modal', function(e) {
            $('#submit-sale').text('<?=lang('submit');?>').attr('disabled', false);
        });
        $('#paymentModal').on('shown.bs.modal', function(e) {
                $("#loyalty_customer").val(null).trigger("change"); 

	    $("select.paid_by").val("cash").change();
			<?php
			foreach($currency as $currency_row){
    			$default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
    			if($currency_row->code == $default_currency_data->code){	
    			?>
    			//$('#amount_<?php echo $currency_row->code; ?>_1').focus().val('');
    			<?php
    			}else{
    			?>
                $('#amount_<?php echo $currency_row->code; ?>_cash').val('');
    			<?php
    			}
			}
			?>
        });
		
		<?php
		foreach($currency as $currency_row){
		$default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
		if($currency_row->code == $default_currency_data->code){	
		?>
		var pi_<?php echo $currency_row->code; ?> = 'amount_<?php echo $currency_row->code; ?>_1';
		<?php
		}else{
		?>
		var pi_<?php echo $currency_row->code; ?> = 'amount_<?php echo $currency_row->code; ?>_1';
		<?php
		}
		}
		?>
        
        $(document).on('focus', '.amounts', function () {            
			<?php $default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency); ?>
            pi_<?php echo $default_currency_data->code; ?> = $(this).attr('id');
            calculateTotalsbill();
        }).on('blur', '.amounts', function () {

             var inputs = $(".amount_base");
             var arr = $('input[name="pname[]"]').map(function () {
                return this.value; 
            }).get();
            var paid_tenders = '';
             for(var i = 0; i < inputs.length; i++){  
                    if(($.inArray($(inputs[i]).attr('payment-type'),arr)) !== -1){                        
                        $('#userd_tender_'+$(inputs[i]).attr('payment-type')).text($(inputs[i]).attr('payment-type')+' - '+$(inputs[i]).val());
                    }else if($(inputs[i]).val() != 0 && ($.inArray($(inputs[i]).attr('payment-type'),arr)) === -1){                        
                     paid_tenders += '<div type="button" class="btn-prni paid_payments" data-index="cash" data_id="1"><input type="hidden"  name="pname[]" value="'+$(inputs[i]).attr('payment-type')+'"><span style="padding-top:5px" id="userd_tender_'+$(inputs[i]).attr('payment-type')+'">'+$(inputs[i]).attr('payment-type')+' - '+$(inputs[i]).val()+'</span></div>';
                    } else if($(inputs[i]).val() === 0){      
                           $('#userd_tender_'+$(inputs[i]).attr('payment-type')).remove();
                    }
            } 
            $('#userd_tender_list').append(paid_tenders);
            calculateTotalsbill();
        });

/*var arr = $('input[name="pname[]"]').map(function () {
                return this.value; 
            }).get();
var paid_tenders = '';

$(".amount_base").on("blur", function(){
    var sum=0;
    $(".amount_base").each(function(){
        if($(this).val() == 0){
            $('#used_tender_type_'+$(this).attr('payment-type')).remove();
        }else if(($.inArray($(this).attr('payment-type'),arr)) !== -1 ){ 
            $('#userd_tender_'+$(this).attr('payment-type')).text($(this).attr('payment-type')+' - '+$(this).val());
        }else if($(this).val() != 0 && ($.inArray($(this).attr('payment-type'),arr)) === -1){
            paid_tenders += '<div type="button" class="btn-prni used_tender_type" id="used_tender_type_'+$(this).attr('payment-type')+'" data-index="cash" data_id="1"><input type="hidden"  name="pname[]" value="'+$(this).attr('payment-type')+'"><span style="padding-top:5px" id="userd_tender_'+$(this).attr('payment-type')+'">'+$(this).attr('payment-type')+' - '+$(this).val()+'</span></div>';
        }

    });
$('#userd_tender_list').append(paid_tenders);
    
});*/
 function calculateTotalsbill() {
	
	 	var value_amount = 0;
	 	var total_paying = 0;
		var ia = $(".amounts");
		$.each(ia, function (i) {
			var code = $(this).attr('data-code');
			var rate = $(this).attr('data-rate');
			var cost_v = $(this).val();
			var a  = default_currency_code;
			var c  = default_currency_rate;
         //   console.log('cost_v'+cost_v);
		//	console.log('rate'+rate);
			if(code == default_currency_code){
				value_amount = cost_v;
			}else{
				value_amount = cost_v * rate;
			}
			var this_amount = formatCNum(value_amount ? value_amount : 0);
			total_paying += parseFloat(this_amount);
		});
		
		$('#total_paying').text(formatMoney(total_paying));		
		
		<?php
		foreach($currency as $currency_row){
    		$default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
            $exchange_curr_code = $this->site->getExchangeCurrency($this->Settings->default_currency);
            $exchange_rate = $this->site->getExchangeRatey($this->Settings->default_currency);
            $getExchangeRatecode = $this->site->getExchangeRatecode($this->Settings->default_currency);

    		if($currency_row->code == $default_currency_data->code){ ?>

                var currency_id = <?php echo $currency_row->id;?>;
                var  $exchange_curr_code = "<?php echo $exchange_curr_code;?>"; 
                var  $exchange_rate = "<?php echo $exchange_rate;?>";

                var decimals = formatDecimal((total_paying - (gtotal_<?php echo $currency_row->code; ?> * <?php echo $currency_row->rate; ?>)));
                  decimals = Math.abs(decimals);
                    // decimals = decimals.toFixed(2);
                    decimals = decimals - Math.floor(decimals);
                    decimals = decimals.toFixed(2);

                    /*console.log((decimals));
                    console.log(decimals);*/
             if(currency_id == "<?php echo $this->Settings->default_currency;?>" && $exchange_rate != '' ){
                    $decimals = decimals/ $exchange_rate;
                    $decimals =  Math.round($decimals / 100) * 100;
                     var $riel = '('+$exchange_curr_code+($decimals)+')';
                }
                else{
                    var $riel = '';
                }       

                // $exchange_amt = (total_paying - (gtotal_<?php echo $getExchangeRatecode; ?> * <?php echo $exchange_rate; ?>))/ <?php echo $exchange_rate ?>;
               /* if($exchange_amt < 0){                     
                   total_paying = total_paying+0.01;
                }*/                                

            $('#balance_<?php echo $currency_row->code; ?>').text(formatMoney(total_paying - (gtotal_<?php echo $currency_row->code; ?> * <?php echo $currency_row->rate; ?>))+''+$riel);

    		/*$('#balance_<?php echo $currency_row->code; ?>').text(formatMoney(total_paying - (gtotal_<?php echo $currency_row->code; ?> * <?php echo $currency_row->rate; ?>) ),'<?php echo $currency_row->symbol; ?>');*/

            $('#balance_amt_<?php echo $currency_row->code; ?>').val(formatDecimal(total_paying - (gtotal_<?php echo $currency_row->code; ?> * <?php echo $currency_row->rate; ?>) ),'<?php echo $currency_row->symbol; ?>');

    		$('.balance_amount').val(formatDecimal(total_paying - (gtotal_<?php echo $currency_row->code; ?> * <?php echo $currency_row->rate; ?>)),'<?php echo $currency_row->symbol; ?>');
    		<?php
    		}else{
                $getExchangesymbol = $this->site->getExchangeCurrency($this->Settings->default_currency);
    		?>
                var $exchange_curr_code = "<?php echo $exchange_curr_code;?>"; 
                $bal_final_amt = (total_paying - (gtotal_<?php echo $currency_row->code; ?> * <?php echo $currency_row->rate; ?>))/ <?php echo $currency_row->rate ?>;
                $bal_final_amt =  Math.round($bal_final_amt / 100) * 100;

                $bal_amt =$exchange_curr_code+$bal_final_amt;

                $('#balance_<?php echo $currency_row->code; ?>').text($bal_amt);

    		// $('#balance_<?php echo $currency_row->code; ?>').text(formatMoney((total_paying - (gtotal_<?php echo $currency_row->code; ?> * <?php echo $currency_row->rate; ?>) ) / <?php echo $currency_row->rate; ?>,'<?php echo $getExchangesymbol; ?>'));
    		
    		<?php }
    		
    		if($currency_row->code == $default_currency_data->code){
    		?>
    		var balance_usd_total_amount = Math.abs((total_paying -  gtotal_<?php echo $currency_row->code; ?> * <?php echo $currency_row->rate; ?>));
    		var balance_usd_remaing_float = balance_usd_total_amount.toString().split(".")[1];
    		//var balance_usd_remaing_float = Math.abs((balance_usd_total_amount - Math.round(balance_usd_total_amount)) );
    		var balance_usd_remaing_float = parseFloat('0.'+balance_usd_remaing_float) / parseFloat($exchange_rate);
    		var balance_USD_KHR = parseFloat(balance_usd_remaing_float);
    		$('#balance_<?=$default_currency_data->code?>_KHR').text(formatMoney(balance_USD_KHR));
    		
    		<?php
    		}	
    	}
		?>
		total_paid = total_paying;
		grand_total = gtotal_<?php echo $default_currency_data->code; ?>;
    }      

    <?php if ($pos_settings->tooltips) {echo '$(".pos-tip").tooltip();';} ?>
$(document).on('change', '.credit-max', function () {

    if ($('#customer_type').val()=='prepaid') {	
	$inputCredit = 0;
	$index = $(this).parents('.payment-row').find('select').attr('data-index');	
	$('.credit-max').each(function(n,v){
	    console.log($(this).attr('id')+'=='+"amount_<?=$default_currency_data->code?>_"+$index);
	    if($(this).attr('id')!="amount_<?=$default_currency_data->code?>_"+$index){
		$inputCredit += ($(this).val()=='')?0:parseFloat($(this).val());
	    }
	});	
	$creditlimit = parseFloat($('#credit_limit').val())-parseFloat($inputCredit);	
	if(parseFloat($(this).val())>parseFloat($creditlimit)){$(this).val('');alert('Amount Exceeds credit limit');}
    }    
});		

$(document).on('change', '.creditcard-max', function () {
var balance = $('.balance_amount').val();
var total_check = $('#total').val();
    $inputCreditcard = 0;
    $index = $( this ).attr('payment-type');   
    $('.creditcard-max').each(function(n,v){        
        if($(this).attr('id') =="amount_<?=$default_currency_data->code?>_"+$index){
            $inputCreditcard += ($(this).val()=='')?0:parseFloat($(this).val());
        }
    });     
    var total_check = $('#total').val();
    if(parseFloat(balance)>0){$(this).val('');alert('Amount Exceeds Payable Total');}    
});
        
        $(document).on('click', '#submit-sale', function () {
           
            if (total_paid == 0 || total_paid < grand_total) {
                
                bootbox.confirm("<?=lang('paid_l_t_payable');?>", function (res) {
                    if (res == true) {
                        $('#pos_note').val(localStorage.getItem('posnote'));
                        $('#staff_note').val(localStorage.getItem('staffnote'));
                        $('#submit-sale').text('<?=lang('loading');?>').attr('disabled', true);
                        $('#pos-sale-form').submit();
                    }
                });
                return false;
            } else {
                $('#pos_note').val(localStorage.getItem('posnote'));
                $('#staff_note').val(localStorage.getItem('staffnote'));
                $(this).text('<?=lang('loading');?>').attr('disabled', true);
                $('#pos-sale-form').submit();
            }
        });
    });
    $(document).on('click', '.cancel_bill', function(e) {
        e.preventDefault();
        var cancel_id = $(this).siblings('.cancel_bill_id').val();
        bootbox.confirm(lang.r_u_sure, function(result) {
        if(result == true) {
                $("#sale_id").val('');
                $("#sale_id").val(cancel_id);
                $('#salecancelremarks').val(''); 
                $('#salesCancelorderModal').show();
            }
        });
        return false;
    });

$(document).on('click','#salescancel_orderitem',function(){
     var cancel_remarks = $('#salecancelremarks').val(); 
     var sale_id = $('#sale_id').val(); 
     if($.trim(cancel_remarks) != ''){
        
        $.ajax({
            type: "get",
            url:"<?=admin_url('pos/cancel_sale_consolidate');?>",                
            data: {cancel_remarks: cancel_remarks, sale_id: sale_id},
            dataType: "json",
            success: function (data) {
                if(data.msg == 'success'){
                         $('#salesCancelorderModal').hide(); 
                         location.reload();
                }else{
                    bootbox.alert('<?=lang('uanble_to_cancle');?>');
                    return false;
                }
            }    
        }).done(function () {
          
        });
     }   
});

$(document).on('click', '.closemodal', function () {
    $('#remarks').val('');
    $('#sale_id').val('');
    $('#CancelorderModal').hide(); 
});

var $print_header_space = '<?=$pos_settings->pre_printed_header?>mm';
var $print_footer_space = '<?=$pos_settings->print_footer_space?>mm';
var pre_printed = '<?=$pos_settings->pre_printed_format?>';

    
  
		 function requestBill(billid){
			



 
//alert('Sent to Normal Print');


   

            var base_url = '<?php echo base_url(); ?>';            
            if (billid != '') {
				
                $.ajax({
                    type: 'get',
					
                    async: false,                    
                    ContentType: "application/json",
                    url: '<?=admin_url('pos/gatdata_print_billing');?>',
                    dataType: "json",
                    data: {
                        billid: billid
                    },
					
                    success: function (data) {
						
                    if (data != '') {      
                     var bill_totals = '';
                       var bill_head ='' ;
			if (pre_printed==0) {
                         bill_head += (data.biller.logo != "test") ? '<div id="wrapper1"><div id="receiptData"><div id="receipt-datareceipt-data"><div class="text-center"><img  src='+base_url+'assets/uploads/logos/'+data.biller.logo +' alt="" >': "";

                         <?php if($pos_settings->print_local_language == 1) :?>
                            bill_head += '<h3 style="text-transform:uppercase;">'+data.biller.local_lang_name+'</h3>';
                         <?php endif; ?>   

                         bill_head += '<h3 style="text-transform:uppercase;">'+data.biller.company+'</h3>';
						
                        <?php if($pos_settings->print_local_language == 1) :?>
                            bill_head += '<p>'+data.biller.local_lang_address+'</p>';
                        <?php endif; ?>  

                         bill_head += '<h4 style="font-weight: bold;">'+data.biller.address+"  "+data.biller.city+" "+data.biller.postal_code+"  "+data.biller.state+"  "+data.biller.country+'<br>'+'<?= lang('tel'); ?>'+': '+data.biller.phone+'</h4></div>';
			}
						 bill_head += '<h3 class="text-center" style="margin-top: 10px">INVOICE</h3>';
                          
                        <?php
                        if($this->Settings->time_format == 12){ ?>
                            var created_on = formatDate(data.inv.created_on);
                        <?php }else {?>
                            var created_on = data.inv.created_on;
                        <?php }
                        ?>
                        function formatDate(date) {
                            var d = new Date(date);
                            var hh = d.getHours();
                            var m = d.getMinutes();
                            var s = d.getSeconds();
                            var dd = "AM";
                            var h = hh;
                            if (h >= 12) {
                                h = hh-12;
                                dd = "PM";
                            }
                            if (h == 0) {
                                h = 12;
                            }
                            m = m<10?"0"+m:m;

                            s = s<10?"0"+s:s;

                            /* if you want 2 digit hours:
                            h = h<10?"0"+h:h; */

                            var pattern = new RegExp("0?"+hh+":"+m+":"+s);

                            var replacement = h+":"+m;
                            /* if you want to add seconds
                            replacement += ":"+s;  */
                            replacement += " "+dd;  
                            return date.replace(pattern,replacement);
                        }

                        // bill_head += '<p>'+'<?= lang('bill_no'); ?>'+': '+data.billdata.bill_number+'<br>'+'<?= lang('date'); ?>'+': '+created_on+'<br>';
                        bill_head +='<?= lang('sale_no_ref'); ?>'+': '+data.inv.reference_no+'<br>';
                        /*<?php if($pos_settings->order_no_display == 1) :?>
                             bill_head +='<?= lang('sale_no_ref'); ?>'+': '+data.inv.reference_no+'<br>';
                        <?php endif; ?>  */                       

                         bill_head += '<?= lang('sales_person'); ?>'+': '+data.created_by.first_name+' '+data.created_by.last_name+'<br>'+'<?= lang('cashier'); ?>'+': '+data.cashier.first_name+' '+data.cashier.last_name;			 
            			 if(data.billdata.order_type==1){
            			    bill_head +='<br>'+'<?= lang('Table'); ?>'+': '+data.billdata.table_name;
            			 }else{
            			 
            			 }
			             bill_head += '</p>';
                         bill_head += '<p style="margin-top: -10px">'+'<?= lang('customer'); ?>'+': '+data.customer.name+'</p>';
                         if (site.pos_settings.total_covers==1) {
                         bill_head += '<p style="margin-top: -10px">'+'<?= lang('No of Covers'); ?>'+': '+data.billdata.seats+'</p>';
                     }
                            if(typeof data.delivery_person  != "undefined"){
							 bill_head += '<p>'+'<?= lang('phone'); ?>'+': '+data.customer.phone+'</p>';
							  bill_head += '<p>'+'<?= lang('delivery_address'); ?>'+': </p>';
							  bill_head += '<address>'+data.customer.address+' <br>'+data.customer.city+' '+data.customer.state+' '+data.customer.country+'<br>Pincode : '+data.customer.postal_code+'</address>';							  
							  bill_head += '<p>'+'<?= lang('delivery_person'); ?>'+': '+data.delivery_person.first_name+' '+data.delivery_person.last_name+' ('+data.delivery_person.user_number+')</p>';
							  bill_head += '<p>'+'<?= lang('phone'); ?>'+': '+data.delivery_person.phone+'</p>';
							 }

                        bill_totals += '<table class="table table-striped table-condensed" style="margin-top: -10px;font-size:14px!important;"><th colspan="2">'+'<?=lang("description");?>'+'</th><th>'+'<?=lang("price");?>'+'</th><th class="text-center">'+'<?=lang("qty");?>'+'</th>';
			    if (site.pos_settings.bill_print_format==2) {
				bill_totals += '<th class="no-border text-center" style="margin-top: -10px">'+'<?=lang("dis(%)");?>'+'</th>';
			    }else{
				if(data.billdata.manual_item_discount != 0){
				    if(site.pos_settings.manual_item_discount_display_option == 1){
				    bill_totals += '<th class="no-border text-center" style="margin-top: -10px">'+'<?=lang("dis(%)");?>'+'</th>';
				}else{
				    bill_totals += '<th class="no-border text-center" style="margin-top: -10px">'+'<?=lang("dis");?>'+'</th>';
				}
				}
			    }

                        bill_totals += '<th class="text-right">'+'<?=lang("sub_total");?>'+'</th>';

                            var r =0;
							
                           $.each(data.billitemdata, function(a,b) {
							  
                            r++;
							var recipe_name;
							<?php
							if($this->Settings->user_language == 'khmer'){
								
							?>
							if(b.khmer_name != ''){
								recipe_name = b.khmer_name;
							}else{
								recipe_name = b.recipe_name;
							}
							<?php
							}else{
							?>
							recipe_name = b.recipe_name;
							<?php } ?>
							var recipe_variant='';
                            if(b.recipe_variant!='' && b.recipe_variant!= 0){                                
                                recipe_variant = ' - ['+b.recipe_variant+']';
                            }else{                                
                                recipe_variant='';
                            }

                            if(b.manual_item_discount != 0){
                                $underline ='underline';
                            }else{
                              $underline ='none';
                            }
                            var star ='*';                            
                            if(b.star == '' || b.manual_item_discount != 0){
                              star ="";
                            }
                            
                            // bill_totals += '<tr><td colspan="2" class="no-border"><span style="display: inherit;">'+ star+ '</span><span style="display: table-cell;text-align: -webkit-match-parent;text-decoration:'+$underline+'">'+ recipe_name+ ''+ recipe_variant+ ' </span></td><td class="no-border">'+ formatMoney(b.net_unit_price) +'</td><td class="no-border text-center">'+ formatDecimal(b.quantity) +'</td>';

                            bill_totals += '<tr><td colspan="2" class="no-border"><span style="display: inherit;">'+ star+ '</span><span style="display: table-cell;text-align: -webkit-match-parent;text-decoration:'+$underline+'">'+ recipe_name+ ''+ recipe_variant+ ' </span>';

                            if(typeof b.addondetails.length != 'undefined'){ 
                                $addon_i =1;    
                                $itemaddonamt =0;                           
                                $br ='';                               
                                $.each(b.addondetails, function(s,p) {
                                    $itemaddonamt =p.price*p.qty;
                                    $addon_i =1;
                                    if($addon_i > 1) {
                                        $br ='<br>';
                                    }                                    
                                    bill_totals += '<span style="color: red;font-weight: bold;"> '+p.addon_name+'('+parseInt(p.qty)+'X'+p.price+') = '+ formatMoney(p.price*p.qty)+'</span>'+$br+'';
                                    
                                }); 
                             }

                            bill_totals += '</td>';

                            bill_totals += '<td class="no-border">'+ formatMoney(b.net_unit_price) +'</td><td class="no-border text-center">'+ formatDecimal(b.quantity) +'</td>';


                                $cols = "4";
                                $cols1 = "5";
        				 if (site.pos_settings.bill_print_format==2) {
        				    bill_totals += '<td class="no-border text-right">'+ b.customer_discount_val +'</td>';
        				 }else{
        				     if(data.billdata.manual_item_discount != 0){
        					$cols = "5";
        					$cols1 = "6";
        					if(site.pos_settings.manual_item_discount_display_option == 1){
        					bill_totals += '<td class="no-border text-right">'+ Math.floor(b.manual_item_discount_per_val) +'</td>';
        					}else{
        					    bill_totals += '<td class="no-border text-right">'+ formatMoney(b.manual_item_discount) +'</td>';
        					}
        				    }
        				 }
                           

if (site.pos_settings.bill_print_format==2) {
    bill_totals += '<td class="no-border text-right">'+ formatMoney(b.subtotal-b.manual_item_discount-b.input_discount) +'</td></tr>';
}else{
                            bill_totals += '<td class="no-border text-right">'+ formatMoney(b.subtotal-b.manual_item_discount) +'</td></tr>';
}
                            /*bill_totals += '<tr><td colspan="2" class="no-border"><span style="display: inherit;">'+r+': &nbsp;'+ b.star+ '&nbsp;</span><span style="display: table-cell;text-align: -webkit-match-parent;text-decoration:'+$underline+'"">'+ recipe_name+ ' </span></td><td class="no-border">'+ formatMoney(b.net_unit_price) +'</td><td class="no-border text-center">'+ formatDecimal(b.quantity) +'</td><td class="no-border text-right">'+ formatMoney(b.subtotal) +'</td></tr>';*/


                                /*bill_totals += '<tbody><tr><td colspan="2" class="no-border">'+r+': &nbsp;&nbsp'+ recipe_name+'' +recipe_variant+'</td><td class="no-border">'+ formatMoney(b.net_unit_price) +'</td><td class="no-border">'+ formatDecimal(b.quantity) +'</td><td class="no-border text-right">'+ formatMoney(b.subtotal) +'</td></tr></tbody>';*/
                            });

                                $cols = "4";
                                $cols1 = "5";
                            if(data.billdata.manual_item_discount != 0){
                                $cols = "5";
                                $cols1 = "6";
                            }
			    if (site.pos_settings.bill_print_format==2) {
				$cols = "5";
                                $cols1 = "6";
			    }

							 bill_totals += '<tr class="bold"><th colspan="'+$cols+'" class="text-right">'+'<?=lang("items");?>'+'</th><th  class="text-right">'+formatDecimal(r)+'</th></tr>';
							 
			    if (site.pos_settings.bill_print_format==1) {			 
				bill_totals += '<tr class="bold"><th colspan="'+$cols+'"class="text-right">'+'<?=lang("total");?>'+'</th><th   class="text-right">'+formatMoney(data.billdata.total - data.billdata.manual_item_discount)+'</th></tr>';
			    }else{
				bill_totals += '<tr class="bold"><th colspan="'+$cols+'"class="text-right">'+'<?=lang("total");?>'+'</th><th   class="text-right">'+formatMoney(data.billdata.total - data.billdata.manual_item_discount- data.billdata.order_discount)+'</th></tr>';
			    }
							
							$total_dis_without_manual =  formatDecimal(data.billdata.total_discount - data.billdata.manual_item_discount);
							if (site.pos_settings.bill_print_format==1) {
                            if($total_dis_without_manual > 0) {
									if(data.billdata.discount_type == 'manual'){
                                        // alert(data.discount);
                                        if(data.discount.discount_val != ''){
                                            var disname = data.billdata.discount_val;
                                        }else{
                                            var disname = '';
                                        } 
										bill_totals += '<tr class="bold"><th class="text-right" colspan="'+$cols+'">'+lang.discount+'('+disname+')</th><th   class="text-right">'+formatMoney($total_dis_without_manual)+'</th></tr>';
									} else {
                                        
                                        if(data.discount){
                                            var disname = data.discount;
                                        }else{
                                            var disname = '';
                                        }
                                    bill_totals += '<tr class="bold"><th class="text-right" colspan="'+$cols+'">'+disname+'</th><th   class="text-right">'+formatMoney($total_dis_without_manual)+'</th></tr>';
									}
                                }
							}else{
							    
							}

                            if(data.billdata.service_charge_id != 0 && data.billdata.service_charge_amount != 0){
                                bill_totals += '<tr class="bold">';
                                bill_totals += '<th colspan="'+$cols+'" class="text-right" >'+data.billdata.service_charge_display_value+' </th>';
                                    bill_totals += '<th colspan="1" class="text-right" >'+data.billdata.service_charge_amount+' </th>';
                                bill_totals += '</tr>';
                            }                                    
                           <?php if($pos_settings->display_tax==1) : ?>
                            if (data.billdata.tax_rate != 0) {
                                    $taxtype = '<?=lang('tax_exclusive')?> '+ data.billdata.tax_name;
                                if(data.billdata.tax_type==0){
                                       $taxtype = '<?=lang('tax_inclusive')?> '+data.billdata.tax_name;
                                }
                                bill_totals += '<tr class="bold">';
                                bill_totals += '<th colspan="'+$cols+'" class="text-right" ><?php echo $pos_settings->tax_caption; ?>  </th>';
                                <?php if($pos_settings->display_tax_amt==1) : ?>
                                    bill_totals += '<th colspan="1" class="text-right" >'+formatMoney(data.billdata.total_tax)+'  </th>';
                                <?php endif; ?>
                                bill_totals += '</tr>';
                            }
                        <?php endif; ?>                        
                 
                       if(data.billdata.tax_type==0){
                        $grandTotal = parseFloat(data.billdata.total) -parseFloat(data.billdata.total_discount) -parseFloat(data.billdata.birthday_discount)  + parseFloat(data.billdata.service_charge_amount);
                       }else{
                        $grandTotal = parseFloat(data.billdata.total) -parseFloat(data.billdata.total_discount) -parseFloat(data.billdata.birthday_discount)  + parseFloat(data.billdata.total_tax) + parseFloat(data.billdata.service_charge_amount);
                       }    

                        $grandTotal =data.billdata.grand_total;
                        $grand_Total =formatMoney(data.billdata.grand_total);
                        var substr = $grand_Total.split('.');
                        $riel =  substr[1]; 


                       var decimals = $grandTotal - Math.floor($grandTotal);
                       decimals = decimals.toFixed(2);

                        <?php
                        $exchange_rate = $this->site->getExchangeRatey($this->Settings->default_currency);

                        if ($pos_settings->print_option == 1) {  
                        $exchange_curr_code = $this->site->getExchangeCurrency($this->Settings->default_currency);    
                        foreach($currency as $currency_row):?>
                        var currency_rate = <?php echo $currency_row->rate;?>;
                        var currency_id = <?php echo $currency_row->id;?>;
                        var  $exchange_rate = "<?php echo $exchange_rate;?>";                    
                        var  $exchange_curr_code = "<?php echo $exchange_curr_code;?>";  
                    
                        var currency_symbol = '<?php echo $currency_row->symbol;?>';                    
                        var grandTotal = formatMoney($grandTotal/currency_rate, currency_symbol);          
                        if(currency_id == "<?php echo $this->Settings->default_currency;?>"){
                            $decimals = decimals/ $exchange_rate;
                            $decimals =  Math.round($decimals / 100) * 100;
                             var $riel = '<br>('+$exchange_curr_code+($decimals)+')';
                        }
                        else{
                            var $riel = '';
                        }
                        
                    bill_totals += '<tr class="bold"><th colspan="'+$cols+'" class="text-right" >'+lang.grand_total;

                       <?php 
                           if($this->Settings->default_currency != $currency_row->id){ ?>
                            // exchange amount 
                                    $final_amt = $grandTotal/ currency_rate;
                                    $final_amt =  Math.round($final_amt / 100) * 100;

                                    /*bill_totals += '</th><th colspan="2"  class="text-right">'+$exchange_curr_code+$final_amt+'</th>';*/
                                    bill_totals += '</th><th colspan="2"  class="text-right">'+exchangeformatMoney($final_amt, $exchange_curr_code)+'</th>';

                               <?php  }else{ ?>
                                    $final_amt = $grandTotal/ currency_rate;
                                    bill_totals += '</th><th colspan="2"  class="text-right">'+formatMoney($final_amt, currency_symbol)+''+$riel+'</th>';
                      <?php  } ?>


                      

                      /* bill_totals += '<tr class="bold"><th colspan="4" class="text-right" >'+lang.grand_total+'(<?php echo $currency_row->code;?>)</th><th colspan="2"  class="text-right">'+formatMoney($final_amt, currency_symbol)+''+$riel+'</th></tr>';*/
                       bill_totals += '</tr>';
               
               <?php endforeach; }else{ ?>
                    <?php $default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
                    $exchange_curr_code = $this->site->getExchangeCurrency($this->Settings->default_currency); 

                     ?>
                    var default_currency_rate = <?php echo $default_currency_data->rate; ?>;
                    var currency_symbol = '<?php echo $currency_row->symbol;?>';                    
                    var grandTotal = formatMoney($grandTotal/default_currency_rate, currency_symbol); 
                    var  $exchange_rate = "<?php echo $exchange_rate;?>";                    
                    var  $exchange_curr_code = "<?php echo $exchange_curr_code;?>";

                    bill_totals += '<tr class="bold"><th colspan="'+$cols+'" class="text-right" >'+lang.grand_total+'(<?php echo $default_currency_data->code;?>)</th><th colspan="2"  class="text-right">'+formatMoney($grandTotal)+'</th></tr>';
                <?php }?>
               
               <?php if($pos_settings->discount_note_display_option == 1){?>
                    if(data.billdata.total_discount != 0){
                       bill_totals += '<tr><th colspan="'+$cols1+'" class="text-left"><small>* Bill Discount is not applied to these items.</small></th></tr>';
                    }

                    if(data.billdata.manual_item_discount != 0){
                       bill_totals += '<tr><th colspan="'+$cols1+'" class="text-left"><small>Underlined Items are manually Discount is applied.</small></th></tr>';
                     }                     
                <?php } ?> 
                 if(data.biller.invoice_footer != ''){
                          bill_totals += '<tr><th colspan="'+$cols1+'" class="text-center"><small>'+data.biller.invoice_footer +'</small></th></tr>';
                       }
                            
                    bill_totals += '</table>';

                              /* $grandTotal =data.billdata.grand_total;
                			   if(data.billdata.tax_type==0){
                			      $grandTotal = parseFloat(data.billdata.grand_total) + parseFloat(data.billdata.total_tax);
                			   } 
                            bill_totals += '<tr class="bold"><th colspan="4" class="text-right" ><span class="pull-left">'+$taxtype+'</span>'+lang.grand_total+'</th><th colspan="2"  class="text-right">'+formatMoney($grandTotal)+'</th></tr></tfoot></table>';*/
						
                                $('#bill_header').empty();
				
				$('#bill_header').append(bill_head);
				
                              //  $( "#bill" ).on("click", function( bil ) {
	//alert( "This will be displayed only once." );
//	$(this).off(bil);
//});

                                $('#bill-total-table').empty();                                
                                $('#bill-total-table').append(bill_totals);
								<?php if($pos_settings->remote_printing == 1){?>
                                PrintDiv($('#bill_tbl').html());  
								<?php }else{?>
									printOrder(data);						
								<?php }?>
                            }
                    } 
                });
            }
		 }
       // });
   // });

    $(function () {
        $(".alert").effect("shake");
        setTimeout(function () {
            $(".alert").hide('blind', {}, 500)
        }, 15000);
        <?php if ($pos_settings->display_time) {?>
        var now = new moment();
        $('#display_time').text(now.format((site.dateFormats.js_sdate).toUpperCase() + " HH:mm"));
        setInterval(function () {
            var now = new moment();
            $('#display_time').text(now.format((site.dateFormats.js_sdate).toUpperCase() + " HH:mm"));
        }, 1000);
        <?php }
        ?>
    });
    
   
   <?php
    
    if ($pos_settings->remote_printing == 1) { ?>
        function PrintDiv(data) {
                var mywindow = window.open('', 'sma_pos_print', 'height=500,width=300');
                var is_chrome = Boolean(mywindow.chrome);
                mywindow.document.write('<html><head><title>Print</title>');
                mywindow.document.write("<style type='text/css' media = 'print'>@page {margin: "+$print_header_space+" 5mm "+$print_footer_space+" 5mm;}</style>");
                mywindow.document.write('<link rel="stylesheet" href="<?=$assets?>styles/helpers/bootstrap.min.css" type="text/css" />');
                mywindow.document.write('</head><body >');
                mywindow.document.write(data);
                mywindow.document.write('</body></html>');
               if (is_chrome) {
                 setTimeout(function() { // wait until all resources loaded 
                    mywindow.document.close(); // necessary for IE >= 10
                    mywindow.focus(); // necessary for IE >= 10
                    mywindow.print(); // change window to winPrint
                    mywindow.close(); // change window to winPrint
                 }, 250);
               } else {
                    mywindow.document.close(); // necessary for IE >= 10
                    mywindow.focus(); // necessary for IE >= 10

                    mywindow.print();
                    mywindow.close();
               }

                return true;
            }

    /*function Popup(data) {
	
        var mywindow = window.open('', 'sma_pos_print', 'height=500,width=300');
	
        mywindow.document.write('<html><head><title>Print</title>');
        mywindow.document.write('<link rel="stylesheet" href="<?=$assets?>styles/helpers/bootstrap.min.css" type="text/css" />');
        mywindow.document.write('</head><body >');
        mywindow.document.write(data);
        mywindow.document.write('</body></html>');
        mywindow.print();
        mywindow.close();
        return true;
    }*/
    <?php }
    ?>
</script>

<script>
$('.kb-pad').keyboard({
        restrictInput: true,
        preventPaste: true,
        autoAccept: true,
        alwaysOpen: false,
        openOn: 'click',
        usePreview: false,
        layout: 'custom',
        maxLength: 10,
        display: {
            'b': '\u2190:Backspace',
        },
        customLayout: {
            'default': [
            '1 2 3 4',
            '5 6 7 8  ',
            ' 9 0 . {b}',

            ' {accept} {cancel}'
            ]
        }
    });
	$('.kb-pad1').keyboard({
        restrictInput: true,
        preventPaste: true,
        autoAccept: true,
        alwaysOpen: false,
        openOn: 'click',
        usePreview: false,
        layout: 'custom',
        maxLength: 10,
        display: {
            'b': '\u2190:Backspace',
        },
        customLayout: {
            'default': [
            '1 2 3 4',
            '5 6 7 8  ',
            ' 9 0 . {b}',

            ' {accept} {cancel}'
            ]
        }
    });
	
$('.kb_pad_length').keyboard({
        restrictInput: true,
        preventPaste: true,
        autoAccept: true,
        alwaysOpen: false,
        openOn: 'click',
        usePreview: false,
        layout: 'custom',
        maxLength: 10,
        display: {
            'b': '\u2190:Backspace',
        }, 
        maxLength : 20,
        customLayout: {
            'default': [
            '1 2 3 4',
            '5 6 7 8  ',
            ' 9 0 - {b}',
            ' {accept} {cancel}'
            ]
        },
        
    });
$('.kb_pad_exp').keyboard({
        restrictInput: true,
        preventPaste: true,
        autoAccept: true,
        alwaysOpen: false,
        openOn: 'click',
        usePreview: false,
        layout: 'custom',
		maxLength: 10,
        display: {
            'b': '\u2190:Backspace',
        }, 
        maxLength : 6,
        customLayout: {
            'default': [
            '1 2 3 4',
            '5 6 7 8  ',
            ' 9 0 {b}',
            ' {accept} {cancel}'
            ]
        },
        
    });
</script>
<script>

function formatMoney(x, symbol) {
    if(!symbol) { symbol = ""; }
    if(site.settings.sac == 1) {
        return (site.settings.display_symbol == 1 ? site.settings.symbol : '') +
            ''+formatSA(parseFloat(x).toFixed(site.settings.decimals)) +
            (site.settings.display_symbol == 2 ? site.settings.symbol : '');
    }
    var fmoney = accounting.formatMoney(x, symbol, site.settings.decimals, site.settings.thousands_sep == 0 ? ' ' : site.settings.thousands_sep, site.settings.decimals_sep, "%s%v");
    if(symbol){
       return fmoney; 
    }
    return (site.settings.display_symbol == 1 ? site.settings.symbol : '') +
        fmoney +
        (site.settings.display_symbol == 2 ? site.settings.symbol : '');
}
function exchangeformatMoney(x, symbol) {
    if(!symbol) { symbol = ""; }
    if(site.settings.sac == 1) {
        return (site.settings.display_symbol == 1 ? site.settings.symbol : '') +
            ''+formatSA(parseFloat(x).toFixed(site.settings.exchange_decimals)) +
            (site.settings.display_symbol == 2 ? site.settings.symbol : '');
    }
    var fmoney = accounting.formatMoney(x, symbol, site.settings.exchange_decimals, site.settings.thousands_sep == 0 ? ' ' : site.settings.thousands_sep, site.settings.decimals_sep, "%s%v");
    if(symbol){
       return fmoney; 
    }
    return (site.settings.display_symbol == 1 ? site.settings.symbol : '') +
        fmoney +
        (site.settings.display_symbol == 2 ? site.settings.symbol : '');
}

</script>

<style type="text/css">
.payment_type .active,.btn-prni.active{
    background-color: #1F73BB!important;
    color: #fff!important;
}
.payment_type{
    cursor: pointer;
    height: 33px;
    margin: 0 0 0px 2px;
    padding: 2px;
    width: 10.5%;
    min-width: 100px;
    overflow: hidden;
    display: inline-block;
    font-size: 13px;
    color: block;
    border: 1px solid black
   } 
/*

#reset{
    cursor: pointer;
    height: 33px;
    margin: 0 0 0px 2px;
    padding: 2px;
    width: 10.5%;
    min-width: 100px;
    overflow: hidden;
    display: inline-block;
    font-size: 13px;
    color: block;
    border: 1px solid black
   } 
*/


   .paid_payments{
    cursor: pointer;
    height: 33px;
    margin: 0 0 0px 2px;
    padding: 2px;
    width: 10.5%;
    min-width: 100px;
    overflow: hidden;
    display: inline-block;
    font-size: 13px;
    color: block;
    border: 1px solid black
   } 

.total_paytd{
    background-color: #1A2127!important;  
    color: #1F73BB;
    font-weight: bold;
    font-size: 16px;;
}

.balance_paytd{
    background-color: #1F73BB!important;  
    color: #FFF;
    font-weight: bold;
    font-size: 16px;;
}

.used_tender_type{
    cursor: pointer;
    height: 33px;
    margin: 0 0 0px 2px;
    padding: 2px;
    width: 10.5%;
    min-width: 100px;
    overflow: hidden;
    display: inline-block;
    font-size: 13px;
    color: block;
    border: 1px solid black
   }
   .taxation_settings label{
    float: none;
   }
   .base_currency_CC2 {
    padding-left: 31px;
}
.biller-keyboard .ui-keyboard-button{
	height: 2em !important;
    }

</style>

<script type="text/javascript">
//    $(document).ready(function (e) {
//    $('input[type="checkbox"],input[type=radio]').not('.skip').iCheck({  
//            checkboxClass: 'icheckbox_square-blue',
//            radioClass: 'iradio_square-blue',
//            increaseArea: '20%' // optional        
//        });
//     });

    
$(document).on('click', "#new_customer_submit", function(e) {
	var form = $(this);
	var total_check = $('#total').val();
	var eligibity_point  = $('#eligibity_point').val();
	$.ajax({
		type: "POST",
		url: site.base_url + "customers/new_customer",
          //url: "<?=admin_url('customers/new_customer')?>",
		data: $('#new-customer-form').serialize(), // serializes the form's elements.
		dataType: 'json',
		success: function(data)
		{

			if(data.msg == 'error'){

				$('#msg_error').html(data.msg_error);
			}else{
			   $('#new_customer_id').val(data.new_customer_id ? data.new_customer_id : 0);
			   $('#new_customer_name').text(data.name ? data.name : '');
			}
		}
	});
	e.preventDefault(); // avoid to execute the actual submit of the form.
	return false;
	
});
$(document).on('click', '#submit-sale1', function () {
			
            var balance = $('.balance_amount').val();
            if (balance >= 0 && balance !='') {   
		      $(this).attr('disabled',true);
                $('#pos-payment-form').submit();
            }
            else{
                
                bootbox.alert("Paid amount is less than the payable amount.");
                return false;
            }  
        });

/*$('.crd_exp').on("change", function() {
  var str = $(this).val().slice(0, 2);
  if(str){
  var str1 = "/";
  var str2 = $(this).val().slice(2, 6);
    var res = str.concat(str1, str2);
    if(res!=""){
    $('.crd_exp').val(res);
}}

});*/
$('.crd_exp').datetimepicker({format: 'mm/yyyy', 
fontAwesome: true, 
todayBtn: 1, 
autoclose: 1,
 minView: 3,
// startDate: new Date(),
 viewMode : 'months',
 startView: "year", 
    minViewMode: "months" });
</script>

<script type="text/javascript" src="<?=$assets?>js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/perfect-scrollbar.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/select2.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="<?=$assets?>pos/js/plugins.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/custom.js"></script>
<link href="<?= $assets ?>styles/jquery.mCustomScrollbar.css" rel="stylesheet"/> 
    <script type="text/javascript" src="<?= $assets ?>js/jquery.mCustomScrollbar.concat.min.js"></script>
<script>
$("#tables_box").mCustomScrollbar({
	   axis:"x",
      theme:"dark"
});
</script>
<script>
const slider = document.querySelector('.tableright');
let isDown = false;
let startX;
let scrollLeft;

slider.addEventListener('mousedown', (e) => {
  isDown = true;
  slider.classList.add('active');
  startX = e.pageX - slider.offsetLeft;
  scrollLeft = slider.scrollLeft;
});
slider.addEventListener('mouseleave', () => {
  isDown = false;
  slider.classList.remove('active');
});
slider.addEventListener('mouseup', () => {
  isDown = false;
  slider.classList.remove('active');
});
slider.addEventListener('mousemove', (e) => {
  if(!isDown) return;
  e.preventDefault();
  const x = e.pageX - slider.offsetLeft;
  const walk = (x - startX) * 3; //scroll-fast
  slider.scrollLeft = scrollLeft - walk;
  console.log(walk);
});
  $('#right-button').click(function() {
      event.preventDefault();
      $('.tableright').animate({
        scrollLeft: "+=300px"
      }, "slow");
   });
   
   
   
   
     $('#left-button').click(function() {
      event.preventDefault();
      $('.tableright').animate({
        scrollLeft: "-=300px"
      }, "slow");
   });
   
   $(document).ready(function () {
    // Handler for .ready() called.
    $('html, body').animate({
        scrollTop: $('#cp').offset().top
    }, 'fast');
});
$('#cp').click(function (event) {
  scrollTop: jQuery(this).offset().top-300
  
  //event.preventDefault();
  //$('#cp').scrollView();
});


   $("document").ready(function(){
  $(window).scrollTo("#cp")
})
   
 //  $(document).on('click', '#edit1', function () {
	
	//alert('test1');
 //});
</script>
</body>
</html>
