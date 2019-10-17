<!--table-->   
<div class="col-xs-12 kitchen_section">
	
	<ul>
    	<?php if($this->sma->actionPermissions('dinein_orders')){  ?>
    	<li><a href="<?php echo base_url().'admin/pos/order_table'; ?>"  ><?=lang('dine_in')?></a></li>
        <?php } ?>
        
        <?php if($this->sma->actionPermissions('takeaway_orders')){  ?>
        <li><a href="<?php echo base_url().'admin/pos/order_takeaway'; ?>" ><?=lang('take_away')?></a></li>
        <?php } ?>
        
        <?php if($this->sma->actionPermissions('door_delivery_orders')){  ?>
        <li><a href="<?php echo base_url().'admin/pos/order_doordelivery'; ?>" ><?=lang('door_delivery')?></a></li>
        <?php } ?>
        
        <?php if($this->Settings->bbq_enable){  ?>
        <li><a href="<?php echo base_url().'admin/pos/order_bbqtable'; ?>" class="active"><?=lang('BBQ')?></a></li>
        <?php } ?>
       
    </ul>
    
</div>                            
<div class="table_list col-xs-12">
    <?php
    if(!empty($tables)){
    ?>
    <ul class="col-xs-12 ul_main">
        <?php
        foreach($tables as $table){
           if($this->site->checkTableStatus($table->id) == FALSE)
           {
        ?>
        <li class="col-xs-12 li_main">

            <div class="table_head">
                <img src="<?=$assets?>images/order-table.png" alt="">
                <span class="odr_name"><?php echo $table->name; ?></span>
            </div>

			

            <div style="clear:both;"></div>
            <?php
            if(!empty($table->split_order)){
            ?>
            <div class="row">
            <ul class="col-xs-12">
                <?php

               /* echo "<pre>";
                print_r($table->split_order);die;*/
                foreach($table->split_order as $split_order){
					
                    if($this->site->splitBBQCheckSalestable($split_order->split_id) == FALSE){
						
                        $count_item = $this->site->BBQsplitCountcheck($split_order->split_id);
                        $dineinbbqboth = $this->site->dineinbbqbothCheck($split_order->split_id);
						
                ?>
                <div class="row">

                    <li class="col-xs-6 text-left split">
                       <h2> 
					   <?php if($this->sma->actionPermissions('change_multiple_status')){ ?>
                       <label class="control control--checkbox" style="left:15px; top:10px;">
                            <input type="checkbox" class="multiple_check multiple_<?php echo $split_order->split_id; ?>" data-order="<?php echo $split_order->split_id; ?>">
                            <div class="control__indicator"></div>
                        </label>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <?php } ?>
					   <?php echo $split_order->split_id.' ('.$split_order->name.')'; ?></h2>

                       
                    </li>
                    
                    
		   
                    <li class="col-xs-6 text-right new_split">
                    <script>            
                    $(document).ready(function () {
            
                            <?php
                                $current_time = date('Y-m-d H:i:s');
                                $created_time = $split_order->session_started;
                                
                                $diff = strtotime($current_time) -  strtotime($created_time);
                            ?>

                            var clock;
                            clock = $('.clock_<?php echo $split_order->split_id ?>').FlipClock(<?php echo $diff ?>,{  
                                clockFace: 'HourlyCounter',  
                            }); 
                        });
                
                    </script>



                     <span href="javascript:void(0)" class="clock_<?php echo $split_order->split_id;?>" style="margin:0px; left:3%;" start_time="<?php echo $split_order->session_started;  ?>"></span>

                     <!-- <span class="btn btn-info">10.10.00</span> -->
						<?php if($this->sma->actionPermissions('new_order_create')){
						 ?>
                           
						<?php
						$checkorder = $this->site->BBQcheckorders($table->id, $split_order->split_id, $split_order->customer_id);
						if($checkorder == FALSE){
						?>
                        <a href="<?=admin_url('pos').'/bbq/?order=4&table='.$table->id.'&split='.$split_order->split_id.'&same_customer='.$split_order->customer_id.'&set=1'?>"> <button  class="btn btn-info"><?php echo lang("order_item") ?></button></a> 
                        <?php
						}else{
						?>
                         <button  class="btn btn-info new_order" data-table="<?= $table->id ?>"><?php echo lang("order_item") ?></button>
                         
                         <a class="bbq_link_<?= $table->id ?>" href="<?=admin_url('pos').'/bbq/?order=4&table='.$table->id.'&split='.$split_order->split_id.'&same_customer='.$split_order->customer_id.'&set=1'?>"> </a>
                         
                          <a class="dine_link_<?= $table->id ?>" href="<?=admin_url('pos').'/?order=1&table='.$table->id.'&split='.$split_order->split_id.'&same_customer='.$split_order->customer_id.''?>"> </a>
                         <?php } ?>
                        
                        <?php 	
						} ?>
                        
                        <?php 
                        $billgenrator_check = $this->pos_settings->default_billgenerator;
                        
                        if($billgenrator_check == 0){
                        $orderstatus = $this->site->getOrderStatus($split_order->split_id);
                        
                        if($orderstatus == TRUE) 
                        {?>
                          <button   OnClick="bilGenerator(<?php echo $table->id;  ?>, '<?php echo $split_order->split_id;  ?>', '<?php echo $count_item; ?>', '<?=$dineinbbqboth ? $dineinbbqboth : 0 ?>');" class="btn btn-warning" id="main_split_<?php echo $split_order->split_id;  ?>" <?php if($this->sma->actionPermissions('bil_generator')){ echo ''; }else{  echo 'disabled'; }  ?>><?php echo lang("bill_generator") ?></button>
                          <input type="hidden" id="count_item" value="<?php echo $count_item; ?>">

                        <?php
                        } 

                       }
                       else{
                        ?>
                        <button OnClick="bilGenerator(<?php echo $table->id;  ?>, '<?php echo $split_order->split_id;  ?>', '<?php echo $count_item; ?>','<?=$dineinbbqboth ? $dineinbbqboth : 0 ?>');" class="btn btn-warning" id="main_split_<?php echo $split_order->split_id;  ?>" <?php if($this->sma->actionPermissions('bil_generator')){ echo ''; }else{  echo 'disabled'; }  ?>><?php echo lang("bill_generator") ?></button>
                         <input type="hidden" id="<?php echo $split_order->split_id;?>_count_item" value="<?php echo $count_item; ?>">
                         
                         <!--  <input type="hidden" id="count_item" value="<?php echo $count_item; ?>"> -->
                          <?php
                        } ?>
                        
                        <a href="javascript:void(0);" data-bbq="<?=$split_order->split_id?>" class="btn btn-danger cover_edit"><span class="pull-left" style="width:120px; text-align:center; font-size:12px; font-weight:bold;"><i class="fa fa-edit"></i> Edit BBQ Cover</span></a>
                        <button class="btn btn-danger" id="order_cancel_<?php echo $table->id;  ?>"  OnClick="send_kot('<?php echo $split_order->split_id;  ?>');" ><?php echo lang("kot_print") ?></button>
                    </li>
                    <div style="clear:both;"></div>
                    <?php
                    
                    if(!empty($split_order->order)){
                    ?>
                    <li class="col-xs-12 ">
                        <ul class="col-xs-12 item_list">
                            <?php
                            foreach($split_order->order as $order){
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
                             
                             if(!empty($order->item)){
                             ?>
                             <div class="row">
                            <li class="col-xs-12">
                                <div class="row">
                                <ul class="col-xs-12">
                                    <?php
                                    
                                    $status_disabled_array = array('Served', 'Inprocess', 'Preparing', 'Closed');
                                    foreach($order->item as $item){
                                        
                                        $addons = $this->site->getAddonByRecipe($item->recipe_id, $item->addon_id);
                                       
                                    ?>
                                    <li class="col-xs-4 value_padd <?php if(!in_array($item->item_status, $status_disabled_array)){ echo 'itm_padd'; } ?> ">
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
											<?php echo $recipe_name; ?> <span>( x <?php echo $item->quantity; ?>)</span> </h3>
					</div>
                                        <div class="col-xs-6">
						<div class="col-xs-2" style="float: right;padding-right: 1px;">
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
                                            <b class="<?php echo $color;?>"><?php echo ($item->item_status=='Cancel') ?'Cancelled':$item->item_status; ?></b>
                                            </div>
                                           
                                            <p class="text-left">
                                            
                                                
                                            
                                               <!-- <a href="javascript:void(0)"><small>Notes:</small> <img src="<?=$assets?>images/small-img.png" alt=""></a>-->
                                            <!--<button class="btn btn-warning" style="margin:0px;">$ <?php echo $item->subtotal; ?></button>-->
                                            </p>
                                            
                                             <p class="text-left text-danger" style="min-height:0px;">
                                             <?php
                                            if(!empty($addons)){
                                            ?>
                                                Addons : 	
                                                <?php
                                                foreach($addons as $addons_row){
                                                    echo '<small class="text-danger">'.$addons_row->addon_name.' | '.'</small>';
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
                                        <div class="col-xs-4 text-right">
                                            
                                            
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
                                </div>
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
                        <li class="col-xs-6 text-left split">
                       <h2> '.$split_order->split_id.' ('.$split_order->name.')'.'</h2>
                    </li>
                        <li class="col-xs-12 ">
                        <ul class="col-xs-12 item_list text-center">
                        <h2 class="text-danger">'.lang('bil_generator_msg').'</h2>
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
            <div class="row">
            <div class="col-md-6">
                    <div class="merge-group-list">
                        
                    </div>
                </div> 
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
            <input type="hidden" name="change_split_id" id="change_split_id">
            <label><?=lang('customers')?></label>
                <select style="display: "  name="changed_table_id" class="form-control pos-input-tip changed_customer_id" id="changed_customer_id">
                <option value="0">No</option>
                    <?php
                    foreach ($avil_customers as $customer) {
                        
                    ?>
                    <option value="<?php echo $customer->id; ?>" data-id="<?php echo $customer->id; ?>"><?php echo $customer->name; ?></option>
                    <?php
                    }
                    ?>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" id="OrderChangeCustomer" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">

</script>

<!--table-->