<!--table--> 
<!--<script type="text/javascript" src="<?=$assets?>pos/js/pos.ajax.js?v=1"></script>-->
<script type="text/javascript">

     var recipe_variant = 0, shipping = 0, p_page = 0, per_page = 0, tcp = "<?=$tcp?>", pro_limit = <?= $pos_settings->pro_limit; ?>,
        brand_id = 0, obrand_id = 0, cat_id = "<?=$pos_settings->default_category?>", ocat_id = "<?=$pos_settings->default_category?>", sub_cat_id = 0, osub_cat_id,
        count = 1, an = 1, DT = <?=$Settings->default_tax_rate?>,
        recipe_tax = 0, invoice_tax = 0, recipe_discount = 0, order_discount = 0, total_discount = 0, total = 0, total_paid = 0, grand_total = 0,
        KB = <?=$pos_settings->keyboard?>, tax_rates =<?php echo json_encode($tax_rates); ?>;
    var protect_delete = <?php if (!$Owner && !$Admin) {echo $pos_settings->pin_code ? '1' : '0';} else {echo '0';} ?>, billers = <?= json_encode($posbillers); ?>, biller = <?= json_encode($posbiller); ?>;
    var username = '<?=$this->session->userdata('username');?>', order_data = '', bill_data = '';

</script>  
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
            if(!empty($table->split_order)){
            ?>
            <div class="row">
            <ul class="col-xs-12">
                <?php
                foreach($table->split_order as $split_order){
					
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
                        <?php } if($this->pos_settings->kot_enable_disable == 1){ ?>
                        
			             <button class="btn btn-danger" id="order_cancel_<?php echo $table->id;  ?>"  OnClick="send_kot('<?php echo $split_order->split_id;  ?>');" ><?php echo lang("kot_print") ?></button>
            <?php } ?>
			
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
                                            if($item->variant!='') :
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
<script type="text/javascript">

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
</script>

<!--table-->