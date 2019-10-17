<!--table-->  
<div class="col-xs-12 kitchen_section">
	
	<ul>
    	<?php if($this->sma->actionPermissions('dinein_orders')){  ?>
    	<li><a href="<?php echo base_url().'admin/pos/order_table'; ?>"><?=lang('dine_in')?></a></li>
        <?php } ?>
        
        <?php if($this->sma->actionPermissions('takeaway_orders')){  ?>
        <li><a href="<?php echo base_url().'admin/pos/order_takeaway'; ?>" ><?=lang('take_away')?></a></li>
        <?php } ?>
        
        <?php if($this->sma->actionPermissions('door_delivery_orders')){  ?>
        <li><a href="<?php echo base_url().'admin/pos/order_doordelivery'; ?>" class="active"><?=lang('door_delivery')?></a></li>
        <?php } ?>
        <?php if($this->Settings->bbq_enable){  ?>
        <li><a href="<?php echo base_url().'admin/pos/order_bbqtable'; ?>" ><?=lang('BBQ')?></a></li>
        <?php } ?>
       
    </ul>
    
</div>                              
<div class="table_list col-xs-12">
    
    <ul class="col-xs-12 ul_main">
        
        <div style="clear:both;"></div>
        <?php
        if(!empty($doordelivery)){
        ?>
        <div class="row">
        <ul class="col-xs-12">
            <?php
            foreach($doordelivery as $doordelivery_row){ 
                if($this->site->splitCheckSalestable($doordelivery_row->split_id) == FALSE){
                    $count_item = $this->site->splitCountcheck($doordelivery_row->split_id);
            ?>
            <div class="row">
                <li class="col-xs-6 text-left split">
                   <h2> <?php echo $doordelivery_row->split_id; ?>- <?php echo $doordelivery_row->customer;?>  </h2>
                </li>
                
                <li class="col-xs-6 text-right new_split">
                	<?php if($this->sma->actionPermissions('new_order_create')){ ?>
                    <a href="<?=admin_url('pos').'/?order=3&split='.$doordelivery_row->split_id.'&same_customer='.$doordelivery_row->customer_id.''?>"> <button class="btn btn-info"><?php echo lang("order_item") ?></button></a> 
                    <?php }   if($this->pos_settings->kot_enable_disable == 1){ ?>
		              <button class="btn btn-danger" id="order_cancel_<?php echo $table->id;  ?>"  OnClick="send_kot('<?php echo $doordelivery_row->split_id;  ?>');" ><?php echo lang("kot_print") ?></button>
                <?php } ?>
                    <button class="btn btn-danger" id="order_cancel_<?php echo $doordelivery_row->split_id;  ?>"  OnClick="CancelAllOrderItems('<?php echo $doordelivery_row->split_id;  ?>','<?php echo $this->GP['pos-cancel_order_remarks'];?>');" <?php if($this->sma->actionPermissions('cancel_order_items')){ echo ''; }else{  echo 'disabled'; }  ?> ><?php echo lang("cancel_all") ?></button>
		    
                      <button OnClick="bilGeneratordoordelivery('<?php echo $doordelivery_row->split_id;  ?>', '<?php echo $count_item; ?>');" class="btn btn-warning" id="main_split_<?php echo $doordelivery_row->split_id;  ?>" <?php if($this->sma->actionPermissions('bil_generator')){ echo ''; }else{  echo 'disabled'; }  ?>><?php echo lang("bill_generator") ?></button>
                    
                </li>
                <div style="clear:both;"></div>
                <?php
                if(!empty($doordelivery_row->order)){
                ?>
                <li class="col-xs-12 ">
                    <ul class="col-xs-12 item_list">
                        <?php
                        
                        foreach($doordelivery_row->order as $order){
                        ?>
                        <li class="col-xs-6 text-left waiter">
                           <?php echo $order->reference_no; ?>
                        </li>
                        <li class="col-xs-6 text-right order_status">
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
                                <li class="col-xs-4 value_padd <?php if(!in_array($item->item_status, $status_disabled_array)){ echo 'itm_padd'; } ?>">
                                    <div class="col-xs-2"><img src="<?php echo site_url().'assets/uploads/thumbs/'.$item->image; ?>" alt="" height="70px" width="70px" ></div>
                                    <div class="col-xs-10">
                                        <h3><?php
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
											<?php echo $recipe_name; ?> <span>( x <?php echo $item->quantity; ?>)</span>
											</h3>
					</div>
                                        <div class="col-xs-6">
					     <div class="col-xs-2">
						<?php $sub_total = $this->sma->formatMoney($item->subtotal); ?>
						<button class="btn btn-warning" style="margin:0px;">$ <?php echo $sub_total ?></button>
					     </div>
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
                                     
                                        <p class="text-left text-danger" style="min-height:40px;">
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
                                            <label class="control control--checkbox hide">
                                            <input type="checkbox" name="status_update_<?php echo $doordelivery_row->split_id; ?>[]" value="<?php echo $item->id;  ?>" title="<?php echo $item->item_status; ?>" data-type="<?php echo $item->id;  ?>" data-split="<?php echo $doordelivery_row->split_id; ?>" class="multiple_status status_<?php echo $doordelivery_row->split_id; ?>">
                                            <div class="control__indicator"></div>
                                            </label>
                                            <?php
                                            }
                                            ?>
                                            
                                            <?php
                                            
                                            $cancel_report = $this->site->getTableCancelstatus($item->id);
                                            if($cancel_report == FALSE){
                                            ?>
                                            
                                            <button <?php echo $disabled; ?> class="btn btn-danger" id="item_cancel_<?php echo $item->id;  ?>"  OnClick="CancelOrderItem('<?php echo $item->item_status;  ?>', '<?php echo $item->id;  ?>', '<?php echo $doordelivery_row->split_id;  ?>','<?php echo $this->GP['pos-cancel_order_remarks'];?>','<?php echo $item->quantity; ?>');" <?php if($this->sma->actionPermissions('cancel_order_items')){ echo ''; }else{  echo 'disabled'; }  ?> ><?php echo lang("cancel") ?></button>
                                            
                                            <?php
                                            }else{
                                            ?>
                                            <a href="javascript:void(0)" data-toggle="tooltip" data-placement="bottom" data-original-title="" aria-describedby="tooltip" title="<?php echo $item->order_item_cancel_note; ?>" class="orderCancelled hide"><br><small>This item is cancelled  </small> <img src="<?=$assets?>images/small-img.png" alt=""></a>
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
                        <button data-status="Ready" data-id="" data-split-id = "<?php echo $takeaway_row->split_id; ?>"  type="button" class="btn btn-success kitchen_status preparing_<?php echo $takeaway_row->split_id; ?> pull-right" style="display:none;"><?php echo lang("served") ?></button>
                                <button data-status="Served" data-id=""  data-split-id = "<?php echo $takeaway_row->split_id; ?>"   type="button" class="btn btn-success kitchen_status ready_<?php echo $takeaway_row->split_id; ?> pull-right" style="display:none;" ><?php echo lang("closed") ?></button>
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
                   <h2> '.$doordelivery_row->split_id.'</h2>
                </li>
                    <li class="col-xs-12 ">
                    <ul class="col-xs-12 item_list text-center">
                    <h2 class="text-success">'.lang('bil_generator_msg').'</h2>
                    </ul>
                    </li></div>';	
                    
                }
            }
             ?> 
        </ul>
        </div>
       <?php
        }else{
        ?>
       <div class="col-sm-6 col-sm-offset-3 col-xs-12 order_cancel_data alert-danger fade in"><?=lang('no_record_found')?> </div>
        <?php
        }
        ?>
       
    </ul>
   
</div>
<!--table-->