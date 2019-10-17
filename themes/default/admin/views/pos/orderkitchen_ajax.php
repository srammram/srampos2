<!-- kitchen-->   
<link rel="shortcut icon" href="<?=$assets?>images/icon.png"/>
    <link rel="stylesheet" href="<?=$assets?>styles/theme.css" type="text/css"/>
    <link rel="stylesheet" href="<?=$assets?>styles/style.css" type="text/css"/>
    <link href="<?= $assets ?>styles/flipclock.css" rel="stylesheet"/>
    <link rel="stylesheet" href="<?=$assets?>pos/css/posajax.css" type="text/css"/>
    <link rel="stylesheet" href="<?=$assets?>pos/css/print.css" type="text/css" media="print"/>
    <script type="text/javascript" src="<?=$assets?>js/jquery-2.0.3.min.js"></script>
    <script type="text/javascript" src="<?=$assets?>js/jquery-migrate-1.2.1.min.js"></script>
    <script type="text/javascript" src="<?= $assets ?>js/flipclock.min.js"></script>
<style>
	.order_list .item_list .value_padd .flip-clock-wrapper{top: 20px!important;}
</style>
<div class="col-xs-12 kitchen_section">
	<?php
	//echo $kitchen_type;
	if(!empty($reskitchen)){
	?>
	<ul>
    	<?php
		foreach($reskitchen as $kitchen){
		?>
    	<li><a class="<?php if($kitchen->id == $kitchen_value){ echo 'active'; }else{ echo ''; } ?>" href="<?php echo base_url().'admin/pos/order_kitchen?type='.$kitchen->id; ?>"><?php echo $kitchen->name; ?></a></li>
        <?php
		}
		?>
    </ul>
    <?php
	}
	?>
</div>                            
<div class="order_list col-xs-12">
    <?php
    if(!empty($orders)){                                       
    ?>
    <ul class="col-xs-12 ul_main">
        <?php
        
        foreach($orders as $orders_list){ //print_R($orders);exit;
        ?>
        <li class="col-xs-12 li_main kitchen-order-container order-details-<?=$orders_list->id?>">
            <div class="order_head">
                <img src="<?=$assets?>images/kitchen.png" alt="">
                <span class="odr_name"><?php echo $orders_list->reference_no; ?></span>
            </div>
           <?php 
              if($orders_list->tablename)
                 {
                       echo '<span class="test_table">'.$orders_list->tablename.'</span>'; 
                 }
                 else{
                    if($orders_list->order_type == 2)
                    {
                        echo '<span class="odr_takeway">Take Away</span>';
                    }else{
                        echo '<span class="odr_delivery">Door Delivery</span>';
                    }
                    
                 }
                ?>
	    <div style="float:right">
	    <button class="btn btn-danger" OnClick="send_kot('<?php echo $orders_list->id; ?>','<?php echo $kitchen_value;?>');" ><?php echo lang("kot_print") ?></button>
	    </div>
                <div style="clear:both;"></div>
             
            <ul class="col-xs-12 split_box">
                <li class="col-xs-6 text-left waiter">
                	<?php  if($this->sma->actionPermissions('kitchen_change_multiple_status')){   ?> 
                    <label class="control control--checkbox" style="left:0px;">
                        <input type="checkbox" class="multiple_check multiple_<?php echo $orders_list->id; ?>" data-order="<?php echo $orders_list->id; ?>">
                        <div class="control__indicator"></div>
                    </label>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <?php  } ?>
                    
                    <?php echo $orders_list->username; ?>
                </li>
                <li class="col-xs-6 text-right order_status">
                    <span><?=lang('status')?> : <small>Open</small></span>
                     <?php if($this->sma->actionPermissions('kot_print')){   ?> 
                    <button type="button" class="kitchen_print btn btn-warning" id="<?php echo $orders_list->id; ?>">Print</button>
                    <?php } ?>
                    
                    <div id="viewkitchen_<?php echo $orders_list->id; ?>" style="display:none;">
                    	<?php
						$biller = $this->site->getCompanyOrderByID($orders_list->biller_id);
						
						?>
                        <center>
                        	<img src="<?php echo base_url().'assets/uploads/logos/'.$biller->logo; ?>">
                            <p><?php echo $biller->company.', '.$biller->address; ?></p>
                            <h2>KOT</h2>
                        </center>
                    	
                    	<h2 class="text-left">
			
                        <br><small><?=lang('order_number')?> : <?php echo $orders_list->reference_no; ?></small>
                        <br><small><?=lang('order_person')?> : <?php $user = $this->site->getUser($this->session->userdata('user_id')); if($user){ echo $user->first_name.' '.$user->last_name; } ?></small>
                        <br><small><?=lang('kitchen_type')?> : <?php $kitchen = $this->site->getKitchenByID($kitchen_value); if($kitchen){  echo $kitchen->name; }  ?></small><br><small><?=lang('table')?> : <?php echo $orders_list->table_name; ?></small>
                        </h2>
                    	<table class="table" >
                        	
                            <thead>
                                <tr>
                                    <th><?=lang('sale_item')?></th>
                                    <th><?=lang('quantity')?></th>
                                    <th><?=lang('addon')?></th>
                                    <th><?=lang('free_item')?></th>
                                    <th><?=lang('free_quantity')?></th>
                                </tr>
                            </thead>
                            <tbody>
                            	<?php
								 foreach($orders_list->order_items as $item){
                                    
									$addons = $this->site->getAddonByRecipe($item->recipe_id, $item->addon_id);
									$get_item =  $this->site->getrecipeByID($item->id);
								?>
                            	<tr class="<?=$item->status?>" style="<?php if($item->status=="Cancel") { ?>text-decoration:line-through;<?php } ?>">
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
											<?php // echo $recipe_name; ?>
                                	<td><?php echo $recipe_name; ?> </td>
                                    <td><?php echo $item->quantity; ?></td>
                                    <td>
                                    <?php
									if($addons){
    									foreach($addons as $addons_row){
                                       		echo '<small class="text-danger">'.$addons_row->addon_name.' , '.'</small>';
                                    	}
                                    }
									?>
                                    </td>
                                    <td><?php echo $get_item->name ? $get_item->name : NULL; ?></td>
                                    <td><?php echo $item->total_get_quantity; ?></td>
                                </tr>
                                <?php
								 }
								?>
                            </tbody>
                         </table>
                    </div>
                    
                </li>
            </ul>
            <div style="clear:both;"></div>
            <?php
            if(!empty($orders_list->order_items)){
            ?>
            <ul class="col-xs-12 item_list">
            
                <?php

                 foreach($orders_list->order_items as $item){
					  $addons = $this->site->getAddonByRecipe($item->recipe_id, $item->addon_id);
					  $get_item =  $this->site->getrecipeByID($item->recipe_id);
					
                ?>
                <li class="col-xs-6 value_padd itm_padd">
                    <div class="col-xs-2"><img src="<?php echo site_url().'assets/uploads/thumbs/'.$item->image; ?>" alt="" height="70px" width="70px" >
                    </div>

                   
                    <div class="col-xs-6">
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
											<?php echo $recipe_name; ?>
                                            
						
                         <span>( x <?php echo $item->quantity; ?>)
                         </span><span class="recipe-qty" style="display:none;"><?php echo $item->quantity; ?></span>
			 <?php 
                                            $color ='';
                                            if($item->status =='Inprocess'){
                                              $color ='text-inprocess';
                                            }
                                            elseif($item->status =='Preparing')
                                            {
                                                $color ='text-preparing';
                                            }
                                            elseif($item->status =='Ready')
                                            {
                                                 $color ='text-ready';
                                            }
                                            elseif($item->status =='Cancel')
                                            {
                                                 $color ='text-cancel';
                                            }
                                            
                                            /*echo $item->item_status;
                                            echo $color;*/
                                            ?>
                         <b class="<?php echo $color;?>"><?php echo ($item->status=='Cancel') ?'Cancelled':$item->status; ?></b>
                        </h3>
                        <p class="text-left">
                           <!-- <a href="#"><small>Notes:</small> <img src="<?=$assets?>images/small-img.png" alt=""></a>-->
                        </p>
                        <p class="text-left text-danger" style="min-height:0px;">
							 <?php
                            if(!empty($addons)){
                            ?>
                                Addons : 	
                                <?php
                                foreach($addons as $addons_row){
                                    echo '<small class="text-danger">'.$addons_row->addon_name.' , '.'</small>';
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
                            <?php $get_item =  $this->site->getrecipeByID($item->get_item); ?>
                            Buy <?php echo $item->buy_quantity; ?> Get <?php echo $item->get_quantity ?> (<?php echo $get_item->name; ?> X <?php echo $item->total_get_quantity; ?>)
                            </p>
                            <?php
                            }
                            ?>
                    </div>
                
              <!--   var clock;

            $(document).ready(function() {

                
                clock = new FlipClock($('.clock'), 10, {
                    clockFace: 'Counter',
                    autoStart: true,
                    countdown: true
                });
                
            }); -->
	   
	  
		<?php if($this->Settings->recipe_time_management && (($item->preparation_time!='' &&  $item->preparation_time!=0  && $p_time = $item->preparation_time) || ($this->Settings->default_preparation_time!='' &&  $this->Settings->default_preparation_time!=0 && $p_time = $this->Settings->default_preparation_time))) : ?>
                <script>            
                    $(document).ready(function () {
            
                            <?php

                                /*$current_time = date('Y-m-d H:i:s');
                                $created_time = $item->time_started;*/
                                $current_time = date('Y-m-d H:i:s');
                                $created_time = $item->time_started;
                                $id = $item->id;
                                $diff = (strtotime($current_time) -  strtotime($created_time));
								//if(in_array($get_item->category_id, array('16', '18', '20'))){
								//	$limit_time = 900;
								//}else{
								//	$limit_time = 600;
								//}
				               $limit_time = $p_time;//$item->preparation_time;
                               if($diff >= $limit_time)
                               { $diff = $diff - $limit_time;  ?>

                                
                                 var clock;
                                    clock = $('.clock_<?php echo $item->id ?>').FlipClock(<?php echo $diff ?>,{  
                                        clockFace: 'MinuteCounter', 
                                         // autoStart: true,
                                         // countdown: true, 
                                    });
                                    $('.flip-clock-wrapper ul li a div.up div.inn').css('background-color', '#fff').css('color', 'red');
                                    $('.flip-clock-wrapper ul li a div.down div.inn').css('background-color', '#fff').css('color', 'red');
                                    $('.flip-clock-wrapper ul.play li.flip-clock-active').css('background-color', '#fff').css('color', 'red');
                                    $('.flip-clock-wrapper ul.play li.flip-clock-active').css('background-color', '#fff').css('color', 'red');
                                    
                               <?php }
                               else{ 

                                 $diff = $limit_time - $diff; ?>                                 
                                 var clock;
                                clock = $('.clock_<?php echo $item->id ?>').FlipClock(<?php echo $diff ?>,{  
                                clockFace: 'MinuteCounter',
                                autoStart: true,
                                countdown: true,
                                
                                stop: function() {
                                $.ajax({
                                    type: "POST",
                                    url:"<?=admin_url('pos/update_timeout_notify');?>",          
                                    data: {id: <?php echo $id ?>},
                                    dataType: "json",
                                    success: function (data) {
                                       
                                    }    
                                });
                                    
                                }

                            }); 

                              <?php  }

                            ?>

                            

                           
                        });
                
                    </script>
                <?php if($item->status!='Cancel') : ?>
                    <span href="javascript:void(0)" class="clock clock_<?php echo $item->id;?>" data-created_time="<?php echo $item->time_started;?>" data-current_time="<?php echo $current_time;?>" data-order_item_id="<?php echo $item->id;?>" style="margin:2em;" ></span>
                 <?php endif; ?>
		 <?php endif; ?> 
		<script>            
                    /*$(document).ready(function () {
            
                            <?php
                                $current_time = date('Y-m-d H:i:s');
                                

                                $created_time = $split_order->session_started;
                                
                                $diff = strtotime($current_time) - strtotime($created_time);
                            ?>

                            var clock;
                            clock = $('.clock_<?php echo $split_order->split_id ?>').FlipClock(<?php echo $diff ?>,{  
                                clockFace: 'HourlyCounter'  
                            }); 
                        });*/
                
                    </script>
                <!-- 
                     <span class="clock_<?php echo $split_order->split_id;?>" style="margin:2em;" start_time="<?php echo $split_order->session_started;  ?>"></span> -->

                    <div class="col-xs-4 text-right">
			<?php $remarks = 0;if($this->GP['pos-cancel_order_remarks']) :
				$remarks = 1;
				endif;
			?>
			
                        <!--<button class="btn btn-danger" id="item_cancel_<?php echo $item->id;  ?>"  OnClick="CancelOrderItem('<?php echo $item->status;  ?>', '<?php echo $item->id;  ?>', '<?php echo $orders_list->split_id;  ?>','<?php echo $remarks ?>');" <?php if($this->sma->actionPermissions('kitchen_cancel_order_items')){ echo ''; }else{  echo 'disabled'; }  ?> ><?php echo lang("cancel") ?>
                        </button>-->
			<?php
                                                
                                                $cancel_report = $this->site->getTableCancelstatus($item->id);
                                                if($cancel_report == FALSE){
                                                ?>
                                                <?php if($item->status!='Cancel') : ?>
                                                <button class="btn btn-danger" id="item_cancel_<?php echo $item->id;  ?>"  OnClick="CancelOrderItem('<?php echo $item->status;  ?>', '<?php echo $item->id;  ?>', '<?php echo $orders_list->split_id;  ?>','<?php echo $remarks ?>','<?php echo $item->quantity; ?>');" <?php if($this->sma->actionPermissions('kitchen_cancel_order_items')){ echo ''; }else{  echo 'disabled'; }  ?> ><?php echo lang("cancel") ?>
                        </button><?php endif; ?>
                                                
                                                <?php
                                                }else{
                                                ?>
                                                <a href="javascript:void(0)" data-toggle="tooltip" data-placement="bottom" data-original-title="" aria-describedby="tooltip" title="<?php echo $item->order_item_cancel_note; ?>" class="hide orderCancelled"><br><small>This item is cancelled </small> <img src="<?=$assets?>images/small-img.png" alt=""></a>
                                                <?php																
                                                }
                                                ?>
                       
                    
                        
                        <?php if($this->sma->actionPermissions('kitchen_change_single_status')){   ?>
			<?php if($cancel_report == FALSE){ ?>
			<?php if($item->status!='Cancel') : ?>
                        <label class="control control--checkbox">
                        <input type="checkbox" name="status_update_<?php echo $orders_list->id; ?>[]" value="<?php echo $item->id;  ?>" title="<?php echo $item->status; ?>" data-type="<?php echo $item->id;  ?>" data-order="<?php echo $orders_list->id; ?>" class="kitchen-order-item-id multiple_status status_<?php echo $orders_list->id; ?>" data-item-name="<?=$recipe_name?>" data-item-qty="<?=$item->quantity?>">
                        <div class="control__indicator"></div>
                        </label>
			<?php endif ;?>
			<?php } ?>
                        <?php } ?>
                        
                    </div>
                </li>
                
                
                <?php
                 }
                ?>
                <div class="clearfix"></div>
                
                <button data-status="Inprocess" data-id="" data-order-type="<?php echo $orders_list->order_type;  ?>" data-order-id = " <?php echo $orders_list->id; ?>"  type="button" class="btn btn-success kitchen_status preparing_<?php echo $orders_list->id; ?> pull-right" style="display:none;"><?php echo lang("preparing") ?></button>
                <button data-status="Preparing" data-id="" data-order-type="<?php echo $orders_list->order_type;  ?>" data-order-id = " <?php echo $orders_list->id; ?>"  type="button" class="btn btn-success kitchen_status ready_<?php echo $orders_list->id; ?> pull-right" style="display:none;" ><?php echo lang("ready") ?></button>
                
                
            </ul>
            <?php
            }
            ?>
        </li>
        <?php
        }
        ?>
    </ul>
   <?php
    }else{
    ?>
    <div class="col-sm-6 col-sm-offset-3 col-xs-12 order_cancel_data alert-danger fade in">   No record found  </div>
    <?php
    }
    ?>
</div>
<script type="text/javascript">
    /*$(document).ready(function () {

    $('.clock').each(function () {
        var clock;
        
        var created_time  = $(this).attr('data-created_time');
        var current_time  = $(this).attr('data-current_time');
        var order_item_id = $(this).attr('data-order_item_id');

       

        clock = $(this).FlipClock(5,{  
        clockFace: 'MinuteCounter',
        autoStart: true,
        countdown: true,
            callbacks: {
                stop: function () {
                    alert(1);
                }
            }
        });
      
    })
});*/
</script>
<!--Kitchen-->