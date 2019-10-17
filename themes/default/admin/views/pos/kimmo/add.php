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
    
    <link href="<?= $assets ?>styles/flipclock.css" rel="stylesheet"/>
    <link rel="stylesheet" href="<?=$assets?>styles/style.css" type="text/css"/>
    <link rel="stylesheet" href="<?=$assets?>pos/css/posajax.css" type="text/css"/>
    <link rel="stylesheet" href="<?=$assets?>pos/css/print.css" type="text/css" media="print"/>
    <script type="text/javascript" src="<?=$assets?>js/jquery-2.0.3.min.js"></script>
    <script type="text/javascript" src="<?=$assets?>js/jquery-migrate-1.2.1.min.js"></script>
    <script type="text/javascript" src="<?= $assets ?>js/flipclock.min.js"></script>
    <script type="text/javascript" src="<?= $assets ?>js/jquery.scannerdetection.js"></script>

    <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script> -->
    <script src="https://files.codepedia.info/files/uploads/iScripts/html2canvas.js"></script>

    <!--[if lt IE 9]>
    <script src="<?=$assets?>js/jquery123.js"></script>
    <![endif]-->
    <?php if ($Settings->user_rtl) {?>
        <link href="<?=$assets?>styles/helpers/bootstrap-rtl.min.css" rel="stylesheet"/>
        <link href="<?=$assets?>styles/style-rtl.css" rel="stylesheet"/>
        <script type="text/javascript">
            $(document).ready(function () {
                $('.pull-right, .pull-left').addClass('flip');
            });
			
        $(".numberonly").keypress(function (event){
            
            if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
                event.preventDefault();
            }
          
        });
            
        </script>
    <?php }
    ?>
    <?php if($this->pos_settings->order_screen_font_size ==1): ?>
        <style type="text/css">
        .name_strong,.category,.subcategory ,.recipe {
            font-weight: bold !important;
            font-size: 15px !important;
        }
        </style>
    <?php endif;?>
<style type="text/css">
    #ajaxrecipe .btn-img span {
        height: 15px;
        line-height: 15px;
        font-family: 'AKbalthom-KhmerNew';
        font-size: 13px;
        display: inline-block;
        width: auto;
        min-width: auto;
    }
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
                    if ($message) {
                        echo "<div class=\"alert alert-success\"><button type=\"button\" class=\"close fa-2x\" data-dismiss=\"alert\">&times;</button>" . $message . "</div>";
                    }
                ?>
                <div id="pos">
                
                    <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'id' => 'pos-sale-form');
                    echo admin_form_open("pos/sent_to_kitchen", $attrib);?>
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
                                
                              <div class="no-print">
                                <?php if ($Owner || $Admin || !empty($this->session->userdata('warehouse_id'))) {
                                    ?>
                                    <div class="form-group">
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
                                 <div class="form-group" style="pointer-events:none;">
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
                                 <div class="form-group" style="pointer-events:none;">
                                 		
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
                              
                                <div class="form-group">
                                <input type="text" name="seats_id" id="seats_id" class="form-control  kb-pad text-center " placeholder="<?=lang('how_many_people')?>" >
                                </div>
                                <?php
								}
								?>
                              
                                <div class="form-group" id="ui">
                                    
                                    <div class="input-group">
                                    
                                    <?php echo form_input('add_item', '', 'class="form-control pos-tip kb-text-click" id="add_item"  data-trigger="focus" placeholder="' . $this->lang->line("search_recipe_by_name_code") . '" title="' . $this->lang->line("au_pr_name_tip") . '"'); ?>
                                    
                                        <div class="input-group-addon" style="padding: 2px 8px;">
                                            <a href="#" id="addManually">
                                                <i class="fa fa-plus-circle" id="addIcon" style="font-size: 1.5em;"></i>
                                            </a>
                                        </div>
                                    </div>
                                    
                                    <div style="clear:both;"></div>
                                </div>
                            </div>
                                
                              
                              
                           
                            <div class="form-group">
                                <div class="input-group">
                                <?php
                                    echo form_input('customer', (isset($_POST['customer']) ? $_POST['customer'] : ""), 'id="poscustomer" data-placeholder="' . $this->lang->line("select") . ' ' . $this->lang->line("customer") . '" required="required" class="form-control pos-input-tip" style="width:100%;"');
                                ?>
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
                                <?php if ($Owner || $Admin || $GP['customers-add']) { ?>
                                    <div class="input-group-addon no-print" style="padding: 2px 8px;">
                                        <a href="<?=admin_url('customers/add_pos');?>" id="add-customer" class="external" data-toggle="modal" data-target="#myModal">
                                            <i class="fa fa-plus-circle" id="addIcon" style="font-size: 1.5em;"></i>
                                        </a>
                                    </div>
                                <?php } ?>
                                </div>
                                <div style="clear:both;"></div>
                            </div>                            
                        </div>                       
                        
                        <div id="print">
                            <div id="left-middle">
                                <div id="recipe-list" class="dragscroll">
                                    <table class="table items table-striped table-fixed table-bordered table-condensed table-hover sortable_table"
                                           id="posTable" style="margin-bottom: 0;">
                                        <thead>
                                        <tr>
                                            <th width="40%"><?=lang("recipe");?></th>
                                            <th width="15%"><?=lang("price");?></th>
                                            <?php if($Settings->manual_item_discount == 1) { ?>
                                             <th width="20%"><?=lang("discount");?></th>
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
                                       
                                    <tr>
                                        <td class="left_td" style="padding: 5px 10px;"><?=lang('items');?></td>
                                    
                                       	<td class="center_td">:</td>
                                       
                                        <td class="right_td text-right" style="padding: 5px 10px;font-size: 14px; font-weight:bold;">
                                            <span id="titems">0</span>
                                        </td>
                                   </tr>
                                   <tr>
                                        <td class="left_td" style="padding: 5px 10px;border-top: 1px solid #DDD;"><?=lang('total');?></td>
                                        
                                        <td class="center_td">:</td>
                                    
                                        <td class="right_td text-right" style="padding: 5px 10px;font-size: 14px; font-weight:bold;">
                                            <span id="total">0.00</span>
                                        </td>
                                        
                                    </tr>
                                   
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
                <div style="clear:both;"></div>
            </div>
        </div>
    </div>
</div>

<?php
$this->load->view($this->theme . 'pos/pos_footer');
?>


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
                                                            <!-- <input type="text" id="pcc_type_1" class="form-control" placeholder="<?=lang('card_type')?>" />-->
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

<div class="modal" id="prModal" tabindex="-1" role="dialog" aria-labelledby="prModalLabel" aria-hidden="true">
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
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true"><i
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
<div class="modal fade in" id="myModal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
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
        <?php if ($sid) { ?>
        localStorage.setItem('positems', JSON.stringify(<?=$items;?>));
        <?php } ?>

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

        <?php if ($pos_settings->tooltips) {echo '$(".pos-tip").tooltip();';}
        ?>
        // $('#posTable').stickyTableHeaders({fixedOffset: $('#recipe-list')});
        //$('#posTable').stickyTableHeaders({scrollableArea: $('#recipe-list')});
        //$('#recipe-list, #category-list, #subcategory-list, #brands-list').perfectScrollbar({suppressScrollX: true});
        $('select, .select').select2({minimumResultsForSearch: 7});

        /*$(document).on('click', '.recipe', function (e) {*/
        $(document).on('click', '.recipe:not(".has-varients")', function (e) {
            $('#modal-loading').show();
            code = $(this).val(),
			
                wh = $('#poswarehouse').val(),
                cu = $('#poscustomer').val();
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
        });

    $(document).on('click', '.recipe-varient', function (e) {        
        var code = $(this).attr('code');        
        $('#myVaraintModal').modal('hide');        
        $('#modal-loading').hide();        
        var wh = $('#poswarehouse').val();
        var cu = $('#poscustomer').val();
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
                    url: "<?=admin_url('pos/ajaxcategorydata');?>",
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
                    url: "<?=admin_url('pos/ajaxrecipe');?>",
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
                    url: "<?=admin_url('pos/ajaxrecipe');?>",
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
			
          	if (count == 1) {
                bootbox.alert('Select recipe');
                return false;
            }else{

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
console.log((assoc)); //false
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
            
        });
        
      
        $(document).on('click','.has-varients',function(){
        $obj = $(this);
        $v = $obj.attr('value');
        $popcon = $obj.closest('span').find('.variant-popup').html();
        $('#myVaraintModal .modal-body').html($popcon);
        $('#myVaraintModal').modal('show');
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
<script type="text/javascript" src="<?=$assets?>pos/js/pos.ajax.js?v=1"></script>
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

</body>
</html>
