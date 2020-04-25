<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!DOCTYPE html>
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
   	<link href="https://cdnjs.cloudflare.com/ajax/libs/iCheck/1.0.2/skins/square/_all.css" rel="stylesheet"/>
		<link rel="stylesheet" href="<?=$assets?>styles/theme.css" type="text/css"/>
  	<link href="<?= $assets ?>fonts/akbalthom_khmerlight/stylesheet.css">
   	<link rel="stylesheet" href="<?=$assets?>styles/palered_theme.css" type="text/css">
	<script type="text/javascript" src="<?=$assets?>js/jquery-2.0.3.min.js"></script>
    
</head>
<style>
	.top_foot {background-color: #810819;margin: 10px 0px;}
	.menu_nav li button,.menu_nav li figure{width:11.7%;}
.btn_orange{ 
    width: 160px;
    height: 100px;
 background-color: #ff5200;
    border-color: #ff5200;
    border-radius: 0px;
    color: #fff;
    font-size: 25px;
    font-family: 'barlow_condensedmedium';
    margin: 0px 1px 4px;
    text-align: center;
	position: relative;
	}
	.btn_gray{ 
    width: 160px;
    height:100px;
 background-color: #808080 ;
    border-color: #808080 ;
    border-radius: 0px;
    color: #fff;
    font-size: 25px;
    font-family: 'barlow_condensedmedium';
    margin: 0px 1px 4px;
    text-align: center;
	position: relative;
	}
	.table_id {padding: 0px;}
	.table_id button
	{background-color: transparent;
    border: none;
    width: 160px;
    height: 100px;
    padding: 0px;
    margin: 0px -1px -4px;}
	
	.disable_list{pointer-events: none;opacity: 0.6;}
	.flip-clock-before{pointer-events: none;opacity: 0.6;}
	.flip-clock-active{pointer-events: none;opacity: 0.6;}
	.menu_nav li figure{width: 11.7%;}
	
	.flip-clock-wrapper {left:20%!important;top: 10%!important;float: left;}
	.flip-clock-wrapper ul li a div div.inn{font-size: 18px;}
	.flip-clock-wrapper ul li a div div.inn,.flip-clock-wrapper ul{background-color: transparent!important;}
	.btn_violet .flip-clock-wrapper ul li a div div.inn,.btn_violet .flip-clock-wrapper ul{background-color: #7a70f3!important;}
	.btn_orange .flip-clock-wrapper ul li a div div.inn,.btn_orange .flip-clock-wrapper ul{background-color: #ff5200!important;}
	.btn_gray .flip-clock-wrapper ul li a div div.inn,.btn_gray .flip-clock-wrapper ul{background-color: #808080!important;}
	.btn_orange.table_id:hover {background-color: #ff5200;border-color: #ff5200;}
	.btn_gray.table_id:hover {background-color: #808080;border-color:#808080;}
	.btn_violet.table_id:hover {background-color: #7a70f3;border-color: #7a70f3;}
	.flip-clock-dot {width: 2px;height: 2px;left: 3px;background: #000;}
	.flip-clock-divider{height: 23px;}
	.flip-clock-dot.bottom{bottom: 8px;}
	.bottom_foot .table .table tr td{padding:0px;}
</style>
<body>
	
	<section class="pos_bottom_s">
		<div class="container-fluid">
			<div class="row">
				<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12 logo_sec">
				      <?php if ($this->Settings->logo3) { ?>
                <a href="<?php echo base_url('pos/pos/'); ?>"><img src="<?=base_url()?>assets/uploads/logos/<?=$this->Settings->logo3?>" alt="<?=$this->Settings->site_name?>" class="sram_table_logo" width="100%" /></a>
           <?php   } ?>
					
				</div>
				<div class="col-lg-10 col-md-10 col-sm-10 col-xs-12 text-left header_sec_me">
					<ul class="menu_nav">
						<li>
							<a href="<?php echo base_url('/pos/pos/home')  ?>">
								<figure class="text-center">
									<img src="<?=$assets?>images/sprite/home_icon.png">
									<figcaption>Home</figcaption>
									<figcaption>ទំព័រមុខ</figcaption>
								</figure>
							</a>
						</li>
						<li>
							<a href="<?php echo base_url('/pos/pos/split_list')  ?>">
								<figure class="text-center">
									<img src="<?=$assets?>images/sprite/order.png">
									<figcaption>Order</figcaption>
									<figcaption>កម្ម៉ង់</figcaption>
								</figure>
							</a>
						</li>
						<li>
							<a href="<?php echo base_url('/pos/pos/invoice_list')  ?>">
								<figure class="text-center">
									<img src="<?=$assets?>images/sprite/invoice.png">
									<figcaption>Invoice</figcaption>
									<figcaption>វិក័យបត្រ័</figcaption>
								</figure>
							</a>
						</li>
						<li>
							<a href="<?php echo base_url('/pos/pos/reprint')  ?>">
							<button>
								<img src="<?=$assets?>images/sprite/print.png">
								<figcaption>Re Print</figcaption>
								<figcaption>ព្រីនសារឡើងវិញ</figcaption>
							</button>
							</a>
						</li>
						<li>
							<a href="<?php echo base_url('/pos/pos/report')  ?>">
								<button>
									<img src="<?=$assets?>images/sprite/report_icon.png">
									<figcaption>Reports/</figcaption>
									<figcaption>របាយការណ៏</figcaption>
								</button>
							</a>
						</li>
						<?php /*?><li>
							<a href="javascript:void(0)">
								<figure class="text-center">
									<div class="img_block">
										<img src="<?=$assets?>images/sprite/room_change.png">
										<figcaption>Room Change</figcaption>
									</div>
								</figure>
							</a>
						</li>
						<li>
							<a href="javascript:void(0)">
								<figure class="text-center">
									<div class="img_block">
										<img src="<?=$assets?>images/sprite/cashier.png">
										<figcaption>Cashier</figcaption>
									</div>
									
								</figure>
							</a>
						</li><?php */?>
						
						<li>
							<a href="<?php  echo base_url('pos/login/logout') ?>">
								<button class="pull-right">
									<img src="<?=$assets?>images/sprite/exit.png">
									<figcaption>Exit</figcaption>
									<figcaption>ចាកចេញ</figcaption>
								</button>
							</a>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</section>
	<section>
		<div class="container">
		<div class="row">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-left slider_sec">
				<div id="exTab1">	
					<ul class="nav nav-pills">
					<?php 	$i=1;   foreach($areas as $areas_row){  ?>
					<li class="<?php echo ($i==1)?"active":"";  ?>  area_class" data-area_id="<?php echo $areas_row->area_id;  ?>"><a  href="#<?php echo $areas_row->area_id;  ?>a" data-toggle="tab"><?php echo $areas_row->areas_name; ?></a></li>				
	                    <?php $i++;  }     ?>
								
					</ul>
					<div class="tab-content clearfix">
					 <?php     if(!empty($areas)){ $i=1;
							foreach($areas as $areas_row){ ?>
						<div class="tab-pane <?php echo ($i==1)?"active":""; ?>" id="<?php echo $areas_row->area_id;  ?>a">
						   <div class="tableright">
						   		<div class="tcb-simple-carousel">
						   		<div class="btn-group btn-group-justified" style="position: absolute;">
                                            <div class="btn">
                                                <button style="z-index:99;position: absolute;left: -45px;" class="btn btn-danger " title="" type="button" id="previous" data-original-title="Previous" data-area_id="<?php echo $areas_row->area_id;  ?>" tabindex="-1">
                                                    <i class="fa fa-arrow-circle-o-left fa-2x" aria-hidden="true"></i>
                                                </button>
                                            </div>
											<div class="btn">
                                                <button style="z-index:99;position: absolute;right: -25px;" class="btn btn-danger " title="" type="button" id="next" data-original-title="Next" data-area_id="<?php echo $areas_row->area_id;  ?>" tabindex="-1">
                                                    <i class="fa fa-arrow-circle-o-right fa-2x" aria-hidden="true"></i>
                                                </button>
                                            </div>
                                        </div>
									<div class="item active" id="itemlist_<?php echo $areas_row->area_id;  ?>" >
				          	<?php		if(!empty($areas_row->tables)){
							      foreach($areas_row->tables as $tables){
									   $avl_bill=$this->site->avl_bill($tables->table_id);
								   $table_status[]=$tables->current_order_status;
								   $table_class='';
							       $link='';
								    switch($tables->current_order_status){
									case 0:
									$table_class='btn_violet';
									$link=base_url('pos/pos/order/?type=1&table='.$tables->table_id);
									break;
									case 1:
									$table_class='btn_orange';
									$link=base_url('pos/pos/payment/?type=1&table='.$tables->table_id);
									break;
									case 4:
									$table_class='btn_gray';
									$link=base_url('pos/pos/payment/?type=1&table='.$tables->table_id);
									break;
								}
							 $table=''; $main_class='';
							if($areas_row->area_id ==$tables->area_id){
								$avl_order=$this->site->avl_order($tables->table_id);  ?>
								<div class="btn btn-default <?= $table_class ;?> table_id  <?=$table_class;?> <?php echo $table;  ?> <?php echo $main_class; ?>">
								<a href="<?= $link ;?>">
									<button type="button"  value="<?php echo $tables->table_id ?>">
											<span class="number_s"><?php echo $tables->table_name ?></span>
											<?php  if($tables->current_order_status ==1){  ?>
				                   	<script>            
					$(document).ready(function () {
					    <?php $current_time = date('Y-m-d H:i:s');
						      $created_time = $tables->last_order_placed_time;
						     // $diff = strtotime($current_time) -  strtotime($created_time);
						     $diff1 = (strtotime($current_time) -  strtotime($created_time));
						     $limit_time = $this->Settings->default_preparation_time;
						     if($diff >= $limit_time){
						     $diff = 0; 
					         }else{
						      $diff = $limit_time - $diff; 
					          } ?>
					var clock;
					clock = $('.clock_<?php echo $tables->table_id ?>').FlipClock(<?php echo $diff1 ?>,{  
						clockFace: 'HourlyCounter', 
						autoStart: true,
						// countdown: true, 
					}); 
				});
			</script>

<span  class="clock_<?php echo $tables->table_id;?>" start_time="<?php echo $tables->last_order_placed_time;  ?>"></span>
<?php   }  ?>
</button>
</a>
<label class="setting" data-title="setting_table_<?php echo $tables->table_id ?>"><i class="fa fa-cog" aria-hidden="true"></i></label>

<div id="setting_table_<?php echo $tables->table_id ?>" class="setting_list_menu" style="display:none">
<a href="javascript:void(0);" onclick="hide('setting_table_<?php echo $tables->table_id ?>')"><i class="fa fa-times" aria-hidden="true"></i></a>
<ul>

<?php  if($this->sma->actionPermissions('new_split_create')){  ?>
		<li <?php echo ($tables->current_order_status ==0 || $tables->current_order_status==1)?"":"class='disable_list'" ?>><a href="<?php  echo base_url('pos/pos/order/?type=1&table='.$tables->table_id); ?>">1.New Split/បំបែកថ្មី</a></li>
<?php  }   ?>
		
		
		<?php   if($this->sma->actionPermissions('new_order_create')){  ?>
		<li <?php echo !empty($avl_order)?"":"class='disable_list'" ?>><a href="<?php  echo base_url('pos/pos/order/?type=1&table='.$tables->table_id.'&split='.$avl_order->split_id.'&same_customer='.$avl_order->customer_id); ?>">2.New Order Item/កម៉្មងម្ហូបថ្មី</a></li>
		<?php   }    ?>
		
		
		
		<?php  $order_count=count($avl_order);  ?>
		
		
		
		<li <?php echo !empty($avl_order)?"":"class='disable_list'" ?>><a href="<?php  echo base_url('pos/pos/payment/?type=1&table='.$tables->table_id.'&req=Invoice'); ?>">3.Invoice/វិក័យបត្រ័</a></li>
		
		
		<?php // $avl_bill=$this->site->avl_bill($tables->table_id);  ?>
		<li <?php echo  !empty($avl_bill)?"":"class='disable_list'" ?>><a href="<?php  echo base_url('pos/pos/payment/?type=1&table='.$tables->table_id.'&req=Payment'); ?>">4.Payment/ទូទាត់</a></li>
		
		
		<?php  if($pos_settings->merge_bill == 1){  ?>
		<li class="merge_bill   <?php echo  ($order_count ==1 && $tables->current_order_status !=0)?"":"disable_list" ?>" table_id="<?php echo $tables->table_id ?>" <?php echo  ($order_count ==1 && $tables->current_order_status !=0)?"data-split_id=".$avl_order->split_id:"" ?> ><a href="#">5.Merge Table/បញ្ចូលទុក</a></li>
		<?php } ?>
		
		
		<?php if($pos_settings->table_change == 1) {  ?>
		<li class="change_table   <?php echo  ($order_count ==1 && $tables->current_order_status !=0)?"":"disable_list" ?>"<?php echo  ($order_count ==1 && $tables->current_order_status !=0)?"data-split_id=".$avl_order->split_id:"disable_list" ?>><a href="javascript:void(0)">6.Change Table/ប្ដូរតុ</a></li>
		<?php  }  ?>
		
		<?php  if($this->sma->actionPermissions('change_customer')){  ?>
		<li class="change_customer   <?php echo  ($order_count ==1 && $tables->current_order_status !=0)?"":"disable_list" ?>"<?php echo  ($order_count ==1 && $tables->current_order_status !=0)?"data-split_id=".$avl_order->split_id ."   "."data-customer_id=".$avl_order->customer_id :"disable_list" ?>  ><a href="javascript:void(0)">7.Change Customer /ប្ដូរភ្ញៀវ</a></li>
		
		<?php  } ?>
		
		<!--<li class="new_split   <?php echo  ($tables->current_order_status ==0)?"disable_list":"" ?>"><a href="<?php  echo base_url('pos/pos/order/?type=1&table='.$tables->table_id.'&spr=1'); ?>">6.New Split</a></li>-->
		
		
		<?php  if($this->sma->actionPermissions('kot_print')){  ?>
		<li  class="<?php echo  ($order_count ==1 && $tables->current_order_status !=0)?"k":"disable_list" ?>"<?php echo  ($order_count ==1 && $tables->current_order_status !=0)?"onclick=send_kot('".$avl_order->split_id."');":"" ?>><a href="javascript:void(0)">8.Copy Kot/ចំលងKOT</a></li>
		<?php  }  ?>
		
		
		
		<?php if($this->sma->actionPermissions('cancel_order_items')){   ?>
		<li class="<?php echo  ($order_count ==1 && $tables->current_order_status !=0)?"":"disable_list" ?>"<?php echo  ($order_count ==1 && $tables->current_order_status !=0)?"onclick=CancelAllOrderItems('".$tables->table_id."','1','".$avl_order->split_id."');":"" ?>><a href="javascript:void(0)">9.Cancel All/លុបទាំងអស់</a></li>
		<?php   }   ?>
		
		<?php if($this->sma->actionPermissions('bil_generator')){  ?>
		<li class="<?php echo  ($order_count ==1 && $tables->current_order_status !=0)?"":"disable_list" ?>"<?php echo  ($order_count ==1 && $tables->current_order_status !=0)?"onclick=bilGenerator('".$tables->table_id."','".$avl_order->split_id."');":"" ?>><a href="javascript:void(0)">10.New Split generator/បំបែកតុថ្មី</a></li>
		
		<?php   }  ?>

	</ul>
</div>
<div class="split_s">
	<div class="btn-group-vertical" role="group">
	  <button type="button" class="btn btn-danger"><?php echo  !empty($order_count)? $order_count:0; ?></button>
	 <button type="button" class="btn btn-danger"><?php echo  !empty(count($avl_bill))? count($avl_bill):0; ?></button>
	</div>
</div>
</div>
<?php 
  }
  } 
  $i++;
  }  ?>
</div>

</div> 

</div>
</div>
<?php   
}
} ?>					
			</div>
		</div>
			<div class="row">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-left slider_sec">
					
				</div>
			</div>
    	</div>
	</section>
	<section>
    	<div class="container">
    		<div class="row">
    			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 top_foot">
    				<table class="table">
    					<tbody>
    						<tr>
							<?php  if(!empty($till->system_ip)){  ?>
    							<?php /*?><td>បើចំហរហូត/Open Til : <?php echo !empty($till->system_ip)? $till->system_ip:'Nil'; ?> </td>
    							<td>ស្ថានភាព/Status : បើក/Open</td><?php */?>
							<?php   } ?>   
    							<td>ជាន់/Floor: ទាំងអស់/<?php echo $active_area->area;   ?></td>
<!--    							<td align="center">ព័ត៌មាន/Till Information</td>-->
    							<td align="right"><?php  echo date('d/m/Y');  ?></td>
    							<!--<td>Sale</td>
    							<td>0</td>
    							<td>Cash in</td>
    							<td>Display</td>-->
    						</tr>
    					</tbody>
    				</table>
    			</div>
    		</div>
    		<div class="row">
    			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 bottom_foot">
    				<table class="table table-bordered">
    					<tbody>
							<tr>
								<td><button type="button" class="btn btn-default btn_vio"><?php   if(!empty($table_status)){
									$status=array_count_values($table_status);
									echo $status['0'];
									}else{ echo 0 ; }; ?></button>
								</td>

								<td>
									<table>
										<colgroup>
											<col width="85%">
											<col width="5%">
											<col width="10%">
										</colgroup>
										<tr>
											<td>ពត៌មានរហូតដល់/TIL info </td>
											<td>:</td>
											<td><?php echo !empty($till->till_name)? $till->till_name:'Nil'; ?></td>
										</tr>
									</table>
								</td>
								<td>
									<table>
										<tr>
											<td>មោះ​អ្នកប្រើប្រាស់ / User Name</td>
											<td>:</td>
											<td><?php echo !empty($user->username)? $user->username:'Nil'; ?></td>
										</tr>
									</table>
								</td>
								<td>
									<table>
										<tr>
											<td>តុល្យភាព / Balance (USD)</td>
											<td>:</td>
											<td><?php     echo !empty($USD_sales_details->balance)?$this->sma->formatMoney($USD_sales_details->balance,$USD_sales_details->symbol):$this->sma->formatMoney(0.00,'$');  ?></td>
										</tr>
									</table>

								</td>
								<td>
									<table>
										<colgroup>
											<col width="85%">
											<col width="5%">
											<col width="10%">
										</colgroup>
										<tr>
											<td>បង់លុយ / Paid (USD)</td>
											<td>:</td>
											<td> <?php echo !empty($USD_sales_details->paid)?$this->sma->formatMoney($USD_sales_details->paid,$USD_sales_details->symbol):$this->sma->formatMoney(0.00,'$');  ?></td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td>
									<button type="button" class="btn btn-default btn_orange"><?php 

									
									$status=array_count_values($table_status);
									if(!empty($status['1'])){ echo  $status['1'];
									}else{ echo 0 ; } ; ?></button>
								</td>
								<td>
									<table>
										<colgroup>
											<col width="85%">
											<col width="5%">
											<col width="10%">
										</colgroup>
										<tr>
											<td>បើកសាច់ប្រាក់ / Opening Cash (USD)</td>
											<td>:</td>
											<td> <?php echo !empty($openning_cash->CUR_USD)?$this->sma->formatMoney($openning_cash->CUR_USD,'៛') :$this->sma->formatMoney(0.00,'៛');  ?></td>
										</tr>
									</table>
								</td>
								<td>
									<table>

										<tr>
											<td>សាខា/Branch </td>
											<td>:</td>
											<td><?php echo !empty($user->warehouses)? $user->warehouses:'Nil'; ?></td>
										</tr>
									</table>
								</td>
								<td>
									<table>

										<tr>
											<td>តុល្យភាព / Balance (KHR)</td>
											<td>:</td>
										<!--	<td><?php echo !empty($KHR_sales_details->balance)?$this->sma->formatMoney($KHR_sales_details->balance,$KHR_sales_details->symbol) :0;  ?></td>-->
											<td><?php echo !empty($KHR_sales_details->balance)?$this->sma->formatMoney(0.00,$KHR_sales_details->symbol) :$this->sma->formatMoney(0.00,'៛');;  ?></td>
										</tr>
									</table>
								</td>
								<td>
									<table>
										<colgroup>
											<col width="85%">
											<col width="5%">
											<col width="10%">
										</colgroup>
										<tr>
											<td>បង់លុយ / Paid (KHR)</td>
											<td>:</td>
											<td> <?php echo !empty($KHR_sales_details->paid)?$this->sma->formatMoney($KHR_sales_details->paid,$KHR_sales_details->symbol) :$this->sma->formatMoney(0.00,'៛'); ?></td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td>
									<button type="button" class="btn btn-default btn_green"><?php   $status=array_count_values($table_status);
									if(!empty($status['4'])){ echo  $status['4'];
									}else{ echo 0 ; }  ?>
									</button>
								</td>
								<td>
									<table>
										<colgroup>
											<col width="85%">
											<col width="5%">
											<col width="10%">
										</colgroup>
										<tr>
											<td>បើកសាច់ប្រាក់ / Opening Cash (KHR)</td>
											<td>:</td>
											<td> <?php echo !empty($openning_cash->CUR_KHR)?$this->sma->formatMoney($openning_cash->CUR_KHR,'៛') :$this->sma->formatMoney(0.00,'៛');  ?></td>
										</tr>
									</table>
								</td>
								<td>
									<table>
										<tr>
											<td>ឈ្មោះ / Till Name</td>
											<td>:</td>
											<td><?php echo !empty($till->system_name)? $till->system_name:'Nil'; ?></td>
										</tr>
									</table>
								</td>
								<td>
									<table>
										<tr>
											<td> ការលក់របស់អ្នកប្រើដែលមានប្រាជ្ញា / <br>User Wise Sales</td>
											<td>:</td>
											<td> <?php echo !empty($usere_sales_details->total)?$this->sma->formatMoney($usere_sales_details->total,$this->Settings->symbol) :0;  ?></td>
										</tr>
									</table>
								</td>
								<td colspan="1" align="right">
									<table>
										<tr>
											<td>ចំណាយសរុប / Total Paid (USD)</td>
											<td>:</td>
											<td> <?php echo !empty($sales_details->total)?$this->sma->formatMoney($sales_details->total,$this->Settings->symbol) :0;  ?></td>
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
	<div class="modal fade in" id="splits-merge-Modal" tabindex="-1" role="dialog" aria-labelledby="splits-merge-ModalLabel" aria-hidden="true" style="background: rgba(0,0,0,0.5);">
						<div class="modal-dialog modal-sm">
							<div class="modal-content">
								<div class="modal-header">
										<div class="btn-group pull-right">
										<button type="button" id="Mergesplits" class="btn btn-primary">Submit</button>
										<button type="button" class="btn btn-danger mergeclose" data-dismiss="modal" aria-hidden="true">Cancel</button>
									</div>
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
								
								</div>
							</div>
						</div>
					</div>
		<div class="modal fade in" id="table-change-Modal" tabindex="-1" role="dialog" aria-labelledby="table-change-ModalLabel" aria-hidden="true" style="background: rgba(0,0,0,0.5);">
						<div class="modal-dialog modal-sm">
							<div class="modal-content">
								<div class="modal-header">
									<div class="btn-group pull-right">
										<button type="button" id="OrderChangeTable" class="btn btn-primary">Submit</button>
										<button type="button" class="btn btn-danger closemodal" data-dismiss="modal" aria-hidden="true">Cancel</button>
									</div>
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
<div class="modal fade in" id="bilModal" tabindex="-1" role="dialog" aria-labelledby="bilModalLabel" aria-hidden="true" style="background: rgba(0,0,0,0.5);">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
			 <div class="btn-group pull-right">
              	<button type="button" id="updateBil" class="btn btn-primary">Submit</button>
                <button type="button" class="btn btn-danger closebil" data-dismiss="modal" aria-hidden="true">Cancel</button>
				</div>
                <h4 class="modal-title" id="bilModalLabel">BILL TYPES</h4>
            </div>
            <div class="modal-body">
              
              <div class="form-group">
                  <!--  <div style="margin-bottom: 10px;"><input type="radio" class="" name="bil_type" value="1" checked> <?=lang('single_bill')?></div>-->
                    <div class="count_div" style="margin-bottom: 10px;"><input type="radio" name="bil_type" value="2"> <?=lang('auto_split_bill')?></div>
                     <div class="count_div" style="margin-bottom: 5px;"><input type="radio" name="bil_type" value="3" > Manual Split Bill</div> 
                    <input class="form-control kb-pad " type="text" name="bils_number_auto" id="bils_number_auto" placeholder="<?=lang('auto_split')?>" style="display:none;">
                    <input type="text" class="form-control  kb-pad" name="bils_number_manual" id="bils_number_manual" placeholder="Manual Split" style="display:none;">
                </div>
				<input type="hidden" name="bil_split_type" id="bil_split_type">
                <input type="hidden" name="bil_table_type" id="bil_table_type">
            </div>
            <div class="modal-footer">
            
            </div>
        </div>
    </div>
</div>
<div class="modal fade in" id="customer-change-Modal" tabindex="-1" role="dialog" aria-labelledby="customer-change-ModalLabel" aria-hidden="true" style="background: rgba(0,0,0,0.5);">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<div class="btn-group pull-right">
										<button type="button" id="OrderChangeCustomer" class="btn btn-primary">Submit</button>
										<button type="button" class="btn btn-danger closemodal" data-dismiss="modal" aria-hidden="true">Cancel</button>
									</div>
									<h4 class="modal-title" id="customer-change-ModalLabel"><?=lang('change_customer')?></h4>
								</div>
								<div class="modal-body">
									<div style="position: absolute; <?=$Settings->user_rtl ? 'right:-9999px;' : 'left:-9999px;';?>"><?php echo form_input('test', '', 'id="test" class="kb-pad"'); ?></div>

								<input type="hidden" name="change_split_id" id="change_split_id">
								
									<label><?=lang('customers')?></label>
								 <?php
									echo form_input('customer', (isset($_POST['customer']) ? $_POST['customer'] : ""), 'id="poscustomer" data-placeholder="' . $this->lang->line("select") . ' ' . $this->lang->line("customer") . '" required="required" class="form-control pos-input-tip" style="width:100%;"');
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
								
							</div>
						</div>
					</div>
				</div></div>
<!--scripts-->
	<script type="text/javascript" src="<?=$assets?>js/jquery-2.0.3.min.js"></script>
	<script type="text/javascript" src="<?=$assets?>js/bootstrap.min.js"></script>
	<script type="text/javascript" src="<?=$assets?>js/jquery-ui.min.js"></script>
	<script type="text/javascript" src="<?=$assets?>js/select2.min.js"></script>
	<script type="text/javascript" src="<?=$assets?>js/jquery.dataTables.min.js"></script>
	<script type="text/javascript" src="<?=$assets?>js/jquery.mCustomScrollbar.concat.min.js"></script>
	<script type="text/javascript" src="<?=$assets?>js/bootstrapValidator.min.js"></script>
	<script type="text/javascript" src="<?=$assets?>js/flipclock.min.js"></script>
	<script type="text/javascript" src="<?=$assets?>styles/helpers/icheck/square/icheck.min.js"></script>
	<script type="text/javascript" src="<?=$assets?>js/bootbox.min.js"></script>
	<script type="text/javascript" src="<?=$assets?>js/custom.js"></script>
	<script type="text/javascript" src="<?=$assets?>pos/js/plugins.min.js"></script>
	
		<script>
		var site = <?=json_encode(array('url' => base_url(), 'base_url' => admin_url('/'), 'assets' => $assets, 'settings' => $Settings,
		'dateFormats' => $dateFormats,'pos_settings'=>json_encode($pos_settings)))?>, pos_settings = <?=json_encode($pos_settings);?>;;
		var p_page =0,pro_limit =25,table_pro_limit=35,KB = <?=$pos_settings->keyboard?>;
		$(document).on('click','.merge_bill',function(e){	
        e.preventDefault();
        $('#merge_split_id').val('');
        var current_split = $(this).data("split_id");
		console.log(current_split);
        var table_id = $(this).attr("table_id");
        $('#merge_split_id').val(current_split);
        $('#merge_table_id').val(table_id);
        $('.merge-group-list').empty();
         $.ajax({
            type: "post",
           url:"<?=base_url('pos/pos/get_splits_for_merge');?>",                
            data: {current_split: current_split,table_id:table_id},
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
            url:"<?= base_url('pos/pos/multiple_splits_mergeto_singlesplit_for_consolidate');?>",                
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
	bootbox.alert('Please select any one split');
	return false;
}

return false;
});
		$('.mergeclose').click(function () {
               /*  $('#merge_split_id').val('');
                $('#merge_table_id').val('');        */         
                $('#splits-merge-Modal').hide();                 
            });
	$(document).on('click','.change_table',function(e){	
       e.preventDefault();
        $('#change_split_id').val('');
        var change_split = $(this).data("split_id")
		$('#table-change-Modal').show();
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
            url:"<?= base_url('pos/pos/change_table_number_all');?>",                
            data: {change_split_id: change_split_id, changed_table_id: changed_table_id},
            dataType: "json",
            success: function (data) {
                if(data.msg == 'success'){
                         $('#table-change-Modal').hide(); 
                         //location.reload();
						   window.location.href = "<?= base_url('pos/pos/'); ?>";
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
	</script>
	<script>
		$(document).ready(function(){
			$('#myCarousel,#myCarousel1,#myCarousel2').carousel();
		});
		$(document).on('click', '.setting',function(){
			$(".setting_list_menu").css("display", "none");
			var id = $(this).attr('data-title');
			$('#'+id).toggle();
		});
	
	</script>
	<script>
	$(function(){
		$(".table_list_carousel").mCustomScrollbar({
			 theme:"dark-3" ,
		});
        });
		function hide(target) {document.getElementById(target).style.display = 'none';}
		 $("input[type='checkbox'], input[type='radio']").iCheck({
                checkboxClass: 'icheckbox_square',
                radioClass: 'iradio_square'
            });
	</script>
	<script type="text/javascript">
	function send_kot($split_id) {
                       	$.ajax({
                        type: "post",
                        url:"<?=base_url('pos/pos/kot_print_copy/');?>"+$split_id,           
                        success: function (data) {
							$(".setting_list_menu").css("display", "none");
                            bootbox.alert('sent to kot print');
                        }    
                    })
    }
	function CancelAllOrderItems( table_id ,$remarks=0, splitid){
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
				    url:"<?= base_url('pos/pos/cancel_all_order_items');?>",                
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
		function bilGenerator( table_id, split_id, count_id ){    
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
				$(document).on('click','#updateBil',function(){
                 var table_id = $('#bil_table_type').val(); 
            	 /*var count_item = $('#count_item').val();*/ 
            	 var split_id = $('#bil_split_type').val(); 
            	 var count_item = $('#'+split_id+'_count_item').val();
				 var bil_type = $('input[name=bil_type]:checked').val();
				 if($('input[name=bil_type]:checked').val() == null){
					     bootbox.alert('PLEASE SELECT SPLIT');
				 }
				 var url = '<?php echo  base_url('pos/pos') ?>';
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

				 if(bils > 0 && bils !=1){
					 window.location.href= '<?php echo base_url();   ?>pos/pos/billing/?order_type=1&bill_type='+bil_type+'&bils='+bils+'&table='+table_id+'&splits='+split_id;
				 }else{
					 if(bil_type == 2){
				//	  $('#bils_number_auto').css('border-color', 'red');
				       bootbox.alert('PLEASE ENTER THE SPLIT NUMBER');
					 }
					 if(bil_type == 3){
					 //$('#bils_number_manual').css('border-color', 'red');
					  bootbox.alert('PLEASE ENTER THE SPLIT NUMBER');
					 }
            	 	//alert('Please enter 1 or more than 1');
				 }

            });
			
			$('.closebil').click(function () {
            	$("#bil_table_type").val('');
				$("#bil_split_type").val('');
            	$('#bilModal').hide();
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
			 $("input[type='checkbox'], input[type='radio']").iCheck({
                checkboxClass: 'icheckbox_square',
                radioClass: 'iradio_square'
            });
			
		$('.cancelclosemodal').click(function () {
                $('#remarks').val('');
                $('#order_table_id').val('');                
                $('#CancelAllorderModal').hide();                 
            });
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
                        url:"<?= base_url('pos/pos/cancel_all_order_items');?>",                
                        data: {table_id:table_id,cancel_remarks: cancel_remarks,split_table_id:split_table_id},
                        dataType: "json",
                        success: function (data) {
			            $obj.attr('disabled',false);
			            $obj.text($submit_text);			    
                            if(data.msg == 'success'){
                            	     $('#CancelAllorderModal').hide(); 
									 window.location.href = "<?= base_url('pos/pos/'); ?>";               	
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
			
	function nav_pointer() {
    var pp = p_page == 'n' ? 0 : p_page;
    (pp == 0) ? $('#previous').attr('disabled', true) : $('#previous').attr('disabled', false);
    ((pp+table_pro_limit) > tcp) ? $('#next').attr('disabled', true) : $('#next').attr('disabled', false);
}
			  $('#next').click(function () {
			  $area_id=$(this).data("area_id");
			  tcp=100;
                 if (p_page == 'n') {
                      p_page = 0
                 }
					p_page = p_page + table_pro_limit;
			    if (tcp >= table_pro_limit && p_page < tcp) {
                $('#modal-loading').show();
				var warehouse_id = '<?php echo $this->session->userdata('warehouse_id'); ?>';
                var order_type = "<?php echo $get_order_type; ?>";  
                $.ajax({
                    type: "get",
                    url: "<?= base_url('pos/pos/get_tables');?>",
                    data: {area_id: $area_id, warehouse_id: warehouse_id, per_page: p_page,order_type: order_type},
                    dataType: "html",
                    success: function (data) {
						if (!$.trim(data)){   
							 $('#next').prop('disabled', true);
							return false;
						}
                        $('#itemlist_'+$area_id).empty();
                        var newPrs = $('<div></div>');
                        newPrs.html(data);
                        newPrs.appendTo('#itemlist_'+$area_id);
                        nav_pointer();
                    }
                }).done(function () {
                    $('#modal-loading').hide();
                });
            } else {
                p_page = p_page - table_pro_limit;
            }
        });

        $('#previous').click(function () {
			 $area_id=$(this).data("area_id");
			 	 $('#next').prop('disabled', false);
            if (p_page == 'n') {
                p_page = 0;
            }
            if (p_page != 0) {
                $('#modal-loading').show();
                p_page = p_page - table_pro_limit;
                if (p_page == 0) {
                    p_page = 'n'
                }
				var warehouse_id = '<?php echo $this->session->userdata('warehouse_id'); ?>';
                var order_type = "<?php echo $get_order_type; ?>";
                $.ajax({
                    type: "get",
                    url: "<?= base_url('pos/pos/get_tables');?>",
                    data: {area_id: $area_id, warehouse_id: warehouse_id,  per_page: p_page,order_type:order_type},
                    dataType: "html",
                    success: function (data) {
						
                        $('#itemlist_'+$area_id).empty();
                        var newPrs = $('<div></div>');
                        newPrs.html(data);
                        newPrs.appendTo('#itemlist_'+$area_id);
                        nav_pointer();
                    }
                }).done(function () {
                    $('#modal-loading').hide();
                });
            }
        });
	$(document).on('click','.change_customer',function(e){	
        e.preventDefault();
        $('#change_split_id').val('');
        var change_split = $(this).data("split_id");
		var customer_id = $(this).data("customer_id");
		$("#poscustomer").val(customer_id);
		$("#poscustomer").trigger("change");
		//console.log(55)
		$('#customer-change-Modal').show(); 
		$('#change_split_id').val(change_split);
    });
	$(document).on('click','#OrderChangeCustomer',function(){
     var change_split_id = $('#change_split_id').val(); 
      var changed_customer_id =  $("#poscustomer").val();
     if($.trim(changed_customer_id) != '' && $.trim(changed_customer_id) != 0){
        $.ajax({
            type: "POST",
            url:"<?= base_url('pos/pos/change_customer_number');?>",                
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
<?php if ($this->session->userdata('remove_posls')) {?>
        <?php $this->sma->unset_data('remove_posls');}
        ?>
		<?php
		if($order_type == 3 && !empty($same_customer) && !empty($this->input->get('customer'))){
			$customer = $this->input->get('customer');
		}elseif($order_type == 3 && empty($same_customer) && !empty($this->input->get('customer'))){
			$customer = $this->input->get('customer');
		}elseif($order_type == 3 && !empty($same_customer) && empty($this->input->get('customer'))){
			$customer = $same_customer;
		}elseif($order_type == 3 && empty($same_customer) && empty($this->input->get('customer'))){
			$customer = '';
		}elseif($order_type != 3 && !empty($same_customer) && !empty($this->input->get('customer'))){
			$customer = $this->input->get('customer');
		}elseif($order_type != 3 && empty($same_customer) && !empty($this->input->get('customer'))){
			$customer = $this->input->get('customer');
		}elseif($order_type != 3 && !empty($same_customer) && empty($this->input->get('customer'))){
			$customer = $same_customer;
		}elseif($order_type != 3 && empty($same_customer) && empty($this->input->get('customer'))){
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
		 function display_keyboards() {

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
        $('.kb-text-click').keyboard({
        autoAccept: true,
        alwaysOpen: false,
        openOn: 'click',
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
 }
 
 $(document).on('click','.area_class',function(e){	
 $('#next').data('area_id',$(this).data("area_id"));
 $('#previous').data('area_id',$(this).data("area_id"));
 });
	</script>
<?php 
echo $this->load->view($this->theme.'pos_v2/shift/shift_popup'); 
?>
</body>
</html>
