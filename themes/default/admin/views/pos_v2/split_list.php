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
   	<link rel="stylesheet" href="<?=$assets?>styles/palered_theme.css" type="text/css">
	   <script type="text/javascript" src="<?=$assets?>js/jquery-2.0.3.min.js"></script>
    <script type="text/javascript" src="<?=$assets?>js/jquery-migrate-1.2.1.min.js"></script>
	<link href="<?= $assets ?>styles/flipclock.css" rel="stylesheet"/>
    <script type="text/javascript" src="<?= $assets ?>js/flipclock.min.js"></script>
	<style>
	.tablebtn{width: 120px;
    height: 34px;
    padding: 0px;
    background-color: #d6311a;
    color: #fff;
    font-family: 'barlow_condensedregular';
    font-size: 20px;
    font-weight: normal;
    border: 1px solid #d6311a;
	}
		.menu_nav li button,.menu_nav li figure{width: 10.7%;}
	</style>
    
</head>
<body>

	<section class="pos_bottom_s">
		<div class="container-fluid">
			<div class="row">
				<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
					 <?php if ($this->Settings->logo3) { ?>
							<a href="<?php echo base_url('pos/pos/'); ?>"><img src="<?=base_url()?>assets/uploads/logos/<?=$this->Settings->logo3?>" alt="<?=$this->Settings->site_name?>" class="sram_table_logo" width="100%" /></a>
					   <?php   } ?>
				</div>
				<div class="col-lg-10 col-md-10 col-sm-10 col-xs-12 text-left header_sec_me">
					<ul class="menu_nav">
						<li>
						<?php $current_order_type=!empty($order_type)?$order_type:'1'; ?>
						<a href="<?php echo base_url('/pos/pos/split_list/?type=1')  ?>">
							<button>
								<img src="<?=$assets?>images/sprite/order.png">
								<figcaption>Order</figcaption>
							</button>
							</a>
						</li>
						<li>
							<a href="<?php echo base_url('/pos/pos/invoice_list/?type=1')  ?>">
							<button>
								<img src="<?=$assets?>images/sprite/payment_y.png">
								<figcaption>Payment</figcaption>
							
							</button>
								</a>
						</li>
						<li>
							<a href="<?php echo base_url('/pos/pos/reprint')  ?>">
							<button>
								<img src="<?=$assets?>images/sprite/print.png">
								<figcaption>Re Print</figcaption>
								
							</button>
							</a>
						</li>
						<li>
						<a href="<?php echo base_url('/pos/pos/')  ?>">
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
<section class="kitchen_section">
<div class="container">
<div class="row">
	<div class="col-md-12 col-xs-12" style="padding: 0px;">
		<ul class="split_list_sec">
    	<?php if($this->sma->actionPermissions('dinein_orders')){  ?>
    	<li><a href="<?php echo base_url().'pos/pos/split_list/?type=1'; ?>"><?=lang('dine_in')?></a></li>
        <?php } ?>
        
        <?php if($this->sma->actionPermissions('takeaway_orders')){  ?>
        <li><a href="<?php echo base_url().'pos/pos/split_list/?type=2'; ?>" class="active"><?=lang('take_away')?></a></li>
        <?php } ?>
        
        <?php if($this->sma->actionPermissions('door_delivery_orders')){  ?>
        <li><a href="<?php echo base_url().'pos/pos/split_list/?type=3'; ?>"><?=lang('door_delivery')?></a></li>
        <?php } ?>
       <!-- <?php if($this->Settings->bbq_enable){  ?>
        <li><a href="<?php echo base_url().'admin/pos/order_bbqtable'; ?>" ><?=lang('BBQ')?></a></li>
        <?php } ?>-->
    </ul>
	</div>
</div>
</div>
</section> 
	<section class="drop_down_list">
		<div class="container">
		<?php   $attrib = array( 'role' => 'form', 'id' => 'Sinvoicelist');  ?>
		  <?php echo form_open("pos/pos/split_list", $attrib);?>
			<div class="row">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 drop_down_list_s" style="padding: 0px;">
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
						<div class="form-group">
							<label for="" class="col-sm-5">Table</label>
							<div class="col-sm-7">
								<select class="form-control select" name="table_id">
								<option value="">All</option>
								<?php if(!empty($tables)){ foreach($tables as $table){  ?>
									<option value="<?php  echo $table->id ?>"  <?php echo ($table->id==$table_id)?  "selected":''; ?>><?php echo $table->name;   ?></option>
								<?php   } }  ?>
								</select>
							</div>
						</div>
					</div>
					<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
						<div class="form-group">
						
							<label for="" class="col-sm-5">Steward</label>
							<div class="col-sm-7">
								<select class="form-control select" name="steward_id">
							<option value="">All</option>
								<?php if($steward){ foreach($steward as $user) { ?>
									<option value="<?php  echo $user->id ?>"  <?php echo ($user->id==$steward_id)?  "selected":''; ?>><?php  echo !empty($user->first_name)?$user->first_name:$user->username;   ?></option>
								<?php   } }  ?>
								</select>
							</div>
						</div>
					</div>
						<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
						 <input type="submit" class="tablebtn" value="Submit">
					</div>
				</div>
			</div>
				<?php   echo form_close();  ?>
				<div class="row">
   		<div class="col-md-12 col-xs-12 table_allorder" style="padding: 0px;">
			<table class="table">
			  <?php
			  switch($current_order_type){
				  case 1:
                      $tables = $this->site->AllOrdersTableList_($table_id,$steward_id);
                      if(!empty($tables)){
                      foreach($tables as $table){
                      if($this->site->checkTableStatus($table->id) == FALSE){
                      $splitorder = $this->site->GetALlSplitsFromOrders($table->id);
                      if(!empty($splitorder)){
                      foreach($splitorder as $split_order){
                     if($this->site->splitCheckSalestable($split_order->split_id) == FALSE){
                        $count_item = $this->site->splitCountcheck($split_order->split_id);
						$dineinbbqboth = $this->site->dineinbbqbothCheck($split_order->split_id);
                ?>
				        <script>            
                        $(document).ready(function () {
                            <?php
                                $current_time = date('Y-m-d H:i:s');
                                $created_time = $split_order->session_started;
                                // $diff = strtotime($current_time) -  strtotime($created_time);
                                $diff1 = (strtotime($current_time) -  strtotime($created_time));
                                $limit_time = $this->Settings->default_preparation_time;
                                if($diff >= $limit_time){
                                $diff = 0; 
                               }else{
                                 $diff = $limit_time - $diff; 
                               }
                            ?>
                                var clock;
                                clock = $('.clock_<?php echo $split_order->split_id ?>').FlipClock(<?php echo $diff1 ?>,{  
                                clockFace: 'HourlyCounter', 
                                autoStart: true,
                            }); 
                        });
                
                    </script>
                 <tr>
   					<td><?php echo $table->name; ?></td>
   					<td>	<?php echo $split_order->split_id.' ('.$split_order->name.')'; ?></td>
   					 <td>   <span  class="clock_<?php echo $split_order->split_id;?>" start_time="<?php echo $split_order->session_started;  ?>"></span></td>
   					<td>Status :<span class="text-success">Inprocess</span></td>
   					<td>Steward :<span><?php echo  $table->first_name  ; 
					?></span></td>
   					<td>  <a style="color:#fff;text-decoration:none;" href="<?= base_url('pos/pos/payment/').'/?type=1&table='.$table->id.'&split_id='.$split_order->split_id.'&req=Invoice';  ?>"><button type="button" class="btn"> Process</button></a></td>
   				</tr>
                 <?php
                    }
                }
            } 
           }
        }?>
		</table>
    <?php
    }
	break;
	case 2: 
	           $splitorder = $this->site->getAllTakeawayorder();
			   if(!empty($splitorder)){
	  foreach($splitorder as $split_order){
	?>
	 <script>            
                          $(document).ready(function () {
                            <?php
                                $current_time = date('Y-m-d H:i:s');
                                $created_time = $split_order->created_on;
                                // $diff = strtotime($current_time) -  strtotime($created_time);
                                $diff1 = (strtotime($current_time) -  strtotime($created_time));
                                $limit_time = $this->Settings->default_preparation_time;
                                if($diff >= $limit_time){
                                $diff = 0; 
                               }else{
                                 $diff = $limit_time - $diff; 
                               }
                            ?>
                                var clock;
                                clock = $('.clock_<?php echo $split_order->split_id ?>').FlipClock(<?php echo $diff1 ?>,{  
                                clockFace: 'HourlyCounter', 
                                autoStart: true,
                            }); 
                        });
                    </script>
	                  <tr>
   					<td>	<?php echo $split_order->split_id.' ('.$split_order->customer.')'; ?></td>
   					 <td>   <span  class="clock_<?php echo $split_order->split_id;?>" start_time="<?php echo $split_order->session_started;  ?>"></span></td>
   					<td>Status :<span class="text-success">Inprocess</span></td>
   					<td>Steward :<span><?php echo  $table->first_name  ; 
					?></span></td>
   					<td>  <a style="color:#fff;text-decoration:none;" href="<?= base_url('pos/pos/payment/').'/?type=2&split_id='.$split_order->split_id.'&req=Invoice';  ?>"><button type="button" class="btn"> Process</button></a></td>
   				</tr>
         	<?php 
			   }
			  }
	break;
	case 3:
	  $splitorder = $this->site->getAllDoordeliveryorder();
	  if(!empty($splitorder)){
	  foreach($splitorder as $split_order){
	?>
	 <script>            
                          $(document).ready(function () {
                            <?php
                                $current_time = date('Y-m-d H:i:s');
                                $created_time = $split_order->created_on;
                                // $diff = strtotime($current_time) -  strtotime($created_time);
                                $diff1 = (strtotime($current_time) -  strtotime($created_time));
                                $limit_time = $this->Settings->default_preparation_time;
                                if($diff >= $limit_time){
                                $diff = 0; 
                               }else{
                                 $diff = $limit_time - $diff; 
                               }
                            ?>
                                var clock;
                                clock = $('.clock_<?php echo $split_order->split_id ?>').FlipClock(<?php echo $diff1 ?>,{  
                                clockFace: 'HourlyCounter', 
                                autoStart: true,
                            }); 
                        });
                
                    </script>
					<tr>
   					<td><?php echo $split_order->split_id.' ('.$split_order->customer.')'; ?></td>
   					 <td><span  class="clock_<?php echo $split_order->split_id;?>" start_time="<?php echo $split_order->session_started;  ?>"></span></td>
   					<td>Status :<span class="text-success">Inprocess</span></td>
   					<td>Steward :<span><?php echo  $table->first_name  ; 
					?></span></td>
   					<td>  <a style="color:#fff;text-decoration:none;" href="<?= base_url('pos/pos/payment/').'/?type=3&split_id='.$split_order->split_id.'&req=Invoice';  ?>"><button type="button" class="btn"> Process</button></a></td>
   				   </tr>
        	<?php  }  } break;  }   ?>
   		</div>
   	    </div>
    	</div>
	</section>
<!--scripts-->
<script type="text/javascript" src="<?=$assets?>js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/perfect-scrollbar.min.js"></script>
<?php /*<script type="text/javascript" src="<?=$assets?>pos/js/pos.ajax.js"></script>*/?>
	<script>
		$(function(){
//			$("#bill_generation").mCustomScrollbar({
//				 theme:"dark-3" ,
//			});
			$(".table_allorder").mCustomScrollbar({
				 theme:"dark-3" ,
			});
        });
		
		$('.select').select2();
	</script>


</body>
</html>
