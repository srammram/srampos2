<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
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
    <link href="<?= $assets ?>styles/flipclock.css" rel="stylesheet"/>
    <link rel="stylesheet" href="<?=$assets?>pos/css/posajax.css" type="text/css"/>
    <link rel="stylesheet" href="<?=$assets?>pos/css/print.css" type="text/css" media="print"/>
    <script type="text/javascript" src="<?=$assets?>js/jquery-2.0.3.min.js"></script>
    <script type="text/javascript" src="<?=$assets?>js/jquery-migrate-1.2.1.min.js"></script>
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
    <style>
	.bootbox.modal.bootbox-confirm{
	    width: auto !important;
	    margin-left: 0px !important;
	    left: 0px !important;
	}
    </style>
    
    <?php if(@$_GET['tid'] ): //&& isset($_SERVER['HTTP_REFERER'])?>
	<script>var curr_page="order_table";var curr_func="update_tables";var tableid = '<?=$_GET['tid']?>';</script>	
    <?php endif; ?>
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
                <?php
                    if ($error) {
                        echo "<div class=\"alert alert-danger\"><button type=\"button\" class=\"close fa-2x\" data-dismiss=\"alert\">&times;</button>" . $error . "</div>";
                    }
                ?>
                <?php
                    if (!empty($message)) {
                        echo "<div class=\"alert alert-success\"><button type=\"button\" class=\"close fa-2x\" data-dismiss=\"alert\">&times;</button>" . $message . "</div>";
                    }
                ?>
                
              
               
                <div id="pos">
                
                	 
                    
                                  
                    
<div class="current_table_order">
	<div class="container">
    	<div class="row">
        

        	
<div id="ordertable_box">
    <div class="col-xs-12 kitchen_section">    
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
    </div>  
<div class="table_list col-xs-12">
    <?php
    $table_id = !empty($this->input->get('table')) ? $this->input->get('table') : '';
    
    $tables = $this->site->GetALlOrdersTableList($table_id);
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

			<?php if($this->sma->actionPermissions('new_split_create')){ ?>
            <div class="newsplit">
                 <a href="<?=admin_url('pos').'/?order=1&table='.$table->id.''?>"> <button   class="btn btn-success pull-right newsplit"><?php echo lang("new_split") ?></button></a>                               
            </div>
            <?php } ?>

            <div style="clear:both;"></div>
            <?php
            $splitorder = $this->site->GetALlSplitsFromOrders($table->id);
            if(!empty($splitorder)){
            ?>
            <div class="row">
            <ul class="col-xs-12">
                <?php

               /* echo "<pre>";
                print_r($table->split_order);die;*/
                foreach($splitorder as $split_order){
					
                    if($this->site->splitCheckSalestable($split_order->split_id) == FALSE){
						
                        $count_item = $this->site->splitCountcheck($split_order->split_id);
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
                    <?php
					if($dineinbbqboth == FALSE){
					?>
                    <li class="col-xs-6 text-right">
                     <?php if($pos_settings->table_change == 1) {?> 
                        <span split="<?php echo $split_order->split_id; ?>" class="btn btn-info change_table"><?php echo lang("change_table") ?></span>
                        <?php } ?> 
					 <?php //if($pos_settings->table_change == 1) {?> 
                        <span split="<?php echo $split_order->split_id; ?>" class="btn btn-info change_customer"><?php echo lang("change_customer") ?></span>
                        <?php // } ?> 

                        <?php if($pos_settings->merge_bill == 1) {  ?> 
                            <span split="<?php echo $split_order->split_id; ?>" table_id ="<?php echo $split_order->table_id; ?>" class="btn btn-info merge_bill"><?php echo lang("merge_bill") ?></span>
                        <?php } ?>

                    </li>
                    <?php
					}
					?>
		   
                    <li class="col-xs-6 text-right new_split">
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



                     <span href="javascript:void(0)" class="clock_<?php echo $split_order->split_id;?>" style="margin:0px;left: 24%;" start_time="<?php echo $split_order->session_started;  ?>"></span>

                     <!-- <span class="btn btn-info">10.10.00</span> -->
						<?php if($this->sma->actionPermissions('new_order_create')){ ?>
                           

                        <a href="<?=admin_url('pos').'/?order=1&table='.$table->id.'&split='.$split_order->split_id.'&same_customer='.$split_order->customer_id.''?>"> <button  class="btn btn-info"><?php echo lang("order_item") ?></button></a> 
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
                                <ul class="col-xs-12">
                                    <?php
                                    
                                    $status_disabled_array = array('Served', 'Inprocess', 'Preparing', 'Closed');
                                    foreach($split_order_items as $item){
                                        
                                        $addons = $this->site->getAddonByRecipeidAndOrderitemid($item->recipe_id, $item->id);
                                       
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

                                            <?php $variant = '';
                                            if($item->variant!='' && $item->variant!=0) :
                                            /*$vari = explode('|',$item->variant);*/
                                            $vari = $item->variant;
                                            $variant = '[<span class="pos-variant-name">'.$vari.'</span>]';
                                            endif; ?>
                                            <?php echo $recipe_name.$variant; ?> <span>( x <?php echo $item->quantity; ?>)</span>
					     </h3>
					</div>
                                        <div class="col-xs-6">
					     <div class="col-xs-2">
                                            
                                                
                                            
                                               <!-- <a href="javascript:void(0)"><small>Notes:</small> <img src="<?=$assets?>images/small-img.png" alt=""></a>-->
                                               <?php $sub_total = $this->sma->formatMoney($item->subtotal - $item->manual_item_discount);
                                               /*var_dump($item->subtotal);
                                               var_dump($item->manual_item_discount);*/
                                                ?>
                                            <button class="btn btn-warning" style="margin:0px;"><?php echo $sub_total; ?></button>
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
                                           
                                            
                                            
                                             <p class="text-left text-danger" style="min-height:0px;margin-top: 10%">
                                             <?php                                             
                                            if(!empty($addons)){
                                            ?>
                                                <p class="add_on_s">Addons : 	</p>
                                                <?php
                                                foreach($addons as $addons_row){
                                                    echo '<small class="text-danger add_on_li">'.$addons_row->addon_name.':('.$addons_row->qty.'X'.$addons_row->price.') ='.$this->sma->formatMoney($addons_row->subtotal).'  '.'</small><br>';
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
                <div style="position: absolute; <?=$Settings->user_rtl ? 'right:-9999px;' : 'left:-9999px;';?>"><?php echo form_input('test', '', 'id="test" class="kb-pad"'); ?></div>

            <input type="hidden" name="change_split_id" id="change_split_id">
            <label><?=lang('customers')?></label>
             <?php
                echo form_input('customer', (isset($_POST['customer']) ? $_POST['customer'] : ""), 'id="poscustomer" data-placeholder="' . $this->lang->line("select") . ' ' . $this->lang->line("customer") . '" required="required" class="form-control pos-input-tip" style="width:100%;"');
            ?>
               <!--  <select style="display: "  name="changed_table_id" class="form-control pos-input-tip changed_customer_id" id="changed_customer_id">
                <option value="0">No</option>
                    <?php
                    foreach ($avil_customers as $customer) {
                        
                    ?>
                    <option value="<?php echo $customer->id; ?>" data-id="<?php echo $customer->id; ?>"><?php echo $customer->name; ?></option>
                    <?php
                    }
                    ?>
                </select> -->
            </div>
            <div class="modal-footer">
                <button type="button" id="OrderChangeCustomer" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </div>
</div>

                </div>
            
                    
                    
        <div class="clearfix"></div>
        	
    	</div>
    </div>
</div>


</div>
</div>
</div>
</div>
</div>

<?php
$this->load->view($this->theme . 'pos/pos_footer');
?>
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
                <h4 class="modal-title" id="bilModalLabel"><?=lang('bil_type')?></h4>
            </div>
            <div class="modal-body">
              
              <div class="form-group">
                    <div><input type="radio" name="bil_type" value="1" checked> <?=lang('single_bil')?></div>
                     <?php if($this->sma->actionPermissions('auto_bil')){ ?>
                    <div class="count_div"><input type="radio" name="bil_type" value="2"> <?=lang('auto_split_bil')?></div>
                    <?php } ?>
                     <div class="count_div" ><input type="radio" name="bil_type" value="3" > Manual Split Bil</div> 
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
				    $msg = ($cancelQty!='all')?'Are you sure want to cancel '+$cancelQty+' Qty?':'Are you sure want to cancel this item?';
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
			   console.log(result)
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
		    });
		}
	    }
	    $('#remarks').on('focus',function(){
		$('#remarks').css('border','1px solid #ccc');
	    });
            $(document).on('click','#cancel_orderitem',function(){
            	$(this).attr('disabled',true);
		        $(this).text('please wait...');

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
                    if(bils == 1)
                    {
                        bil_type = 1;                        
                    }
                    if(count_item < bils){
                        bootbox.alert('<?=lang('manual_bill');?>');
                        return false;
                    }
				 }

				 if(bils > 0){
					 window.location.href= url +'/billing/?order_type=1&bill_type='+bil_type+'&bils='+bils+'&table='+table_id+'&splits='+split_id;
				 }else{
            	 	alert('Check any input feild empty');
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
    $('input[type=radio][name=bil_type]').change(function() {

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
                                        $('#poscustomer').select2({data: res}).select2('open');
                                        $('.select2-input').removeClass('select2-active');
                                    } else {
                                         bootbox.alert('no_match_found');
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

 }  

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
		console.log(55)
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
            url:"<?=admin_url('pos/change_table_number');?>",                
            data: {change_split_id: change_split_id, changed_table_id: changed_table_id},
            dataType: "json",
            success: function (data) {
                if(data.msg == 'success'){
                         $('#table-change-Modal').hide(); 
                         location.reload();
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
      
      var changed_customer_id =  $("#poscustomer").val();
     
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
				$.each(res.data, function(i, item) {
					check_Box = "<input type='checkbox' class='merge'  name='merge[]' value='" + item.split_id + "'/>" + item.name + "<br/>";
					$(check_Box).appendTo('.merge-group-list');
				});
				
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
            url:"<?=admin_url('pos/multiple_splits_mergeto_singlesplit');?>",                
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
                        url:"<?=admin_url('pos/cancel_all_order_items');?>",                
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
	    
    /******************** cancel all order items - end *******************/
</script>
</body>
</html>
