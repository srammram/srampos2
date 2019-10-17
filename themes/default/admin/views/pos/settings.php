<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-cogs"></i><?= lang('pos_settings'); ?></h2>
        <?php if(isset($pos->purchase_code) && ! empty($pos->purchase_code) && $pos->purchase_code != 'purchase_code') { ?>
        <div class="box-icon">
            <ul class="btn-tasks">
                <!-- <li class="dropdown"><a href="<?= admin_url('pos/updates') ?>" class="toggle_down"><i
                    class="icon fa fa-upload"></i><span class="padding-right-10"><?= lang('updates'); ?></span></a>
                </li> -->
            </ul>
        </div>
        <?php } ?>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?= lang('update_info'); ?></p>

                <?php
                $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'id' => 'pos_setting');
                echo admin_form_open("pos/settings", $attrib);
                ?>

                <fieldset class="scheduler-border">
                    <legend class="scheduler-border"><?= lang('pos_config') ?></legend>
                    <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                            <?= lang('pro_limit', 'limit'); ?>
                            <?= form_input('pro_limit', $pos->pro_limit, 'class="form-control" id="limit" required="required"'); ?>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                            <?= lang('delete_code', 'pin_code'); ?>
                            <?= form_password('pin_code', $pos->pin_code, 'class="form-control" pattern="[0-9]{4,8}"id="pin_code"'); ?>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                            <?= lang('default_category', 'default_category'); ?>
                            <?php
                            $ct[''] = lang('select').' '.lang('default_category');
                            foreach ($categories as $catrgory) {
                                $ct[$catrgory->id] = $catrgory->name;
                            }
                            echo form_dropdown('category', $ct, $pos->default_category, 'class="form-control" id="default_category" required="required" style="width:100%;"');
                            ?>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                            <?= lang('default_biller', 'default_biller'); ?>
                            <?php
                            $bl[0] = "";
                            foreach ($billers as $biller) {
                                $bl[$biller->id] = $biller->company != '-' ? $biller->company : $biller->name;
                            }
                            if (isset($_POST['biller'])) {
                                $biller = $_POST['biller'];
                            } else {
                                $biller = "";
                            }
                            echo form_dropdown('biller', $bl, $pos->default_biller, 'class="form-control" id="default_biller" required="required" style="width:100%;"');
                            ?>
                        </div>
                    </div>                    
                    <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                            <?= lang('default_customer', 'customer1'); ?>
                            <?= form_input('customer', (isset($_POST['customer']) ? $_POST['customer'] : $pos->default_customer), 'id="customer1" data-placeholder="' . lang("select") . ' ' . lang("customer") . '" required="required" class="form-control" style="width:100%;"'); ?>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                            <?= lang('display_time', 'display_time'); ?>
                            <?php
                            $yn = array('1' => lang('yes'), '0' => lang('no'));
                            echo form_dropdown('display_time', $yn, $pos->display_time, 'class="form-control" id="display_time" required="required"');
                            ?>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                            <?= lang('onscreen_keyboard', 'keyboard'); ?>
                            <?php
                            echo form_dropdown('keyboard', $yn, $pos->keyboard, 'class="form-control" id="keyboard" required="required"');
                            ?>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                            <?= lang('recipe_button_color', 'recipe_button_color'); ?>
                            <?php $col = array('default' => lang('default'), 'primary' => lang('primary'), 'info' => lang('info'), 'warning' => lang('warning'), 'danger' => lang('danger'));
                            echo form_dropdown('recipe_button_color', $col, $pos->recipe_button_color, 'class="form-control" id="recipe_button_color" required="required"');
                            ?>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                            <?= lang('tooltips', 'tooltips'); ?>
                            <?php
                            echo form_dropdown('tooltips', $yn, $pos->tooltips, 'class="form-control" id="tooltips" required="required"');
                            ?>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                            <?= lang('rounding', 'rounding'); ?>
                            <?php
                            $rnd = array('0' => lang('disable'), '1' => lang('to_nearest_005'), '2' => lang('to_nearest_050'), '3' => lang('to_nearest_number'), '4' => lang('to_next_number'));
                            echo form_dropdown('rounding', $rnd, $pos->rounding, 'class="form-control" id="rounding" required="required"');
                            ?>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                            <?= lang('item_order', 'item_order'); ?>
                            <?php $oopts = array(0 => lang('default'), 1 => lang('category')); ?>
                            <?= form_dropdown('item_order', $oopts, $pos->item_order, 'class="form-control" id="item_order" required="required"'); ?>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                            <?= lang('after_sale_page', 'after_sale_page'); ?>
                            <?php $popts = array(0 => lang('receipt'), 1 => lang('pos')); ?>
                            <?= form_dropdown('after_sale_page', $popts, $pos->after_sale_page, 'class="form-control" id="after_sale_page" required="required"'); ?>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                            <?= lang('display_customer_details', 'customer_details'); ?>
                            <?php $popts = array(0 => lang('no'), 1 => lang('yes')); ?>
                            <?= form_dropdown('customer_details', $popts, $pos->customer_details, 'class="form-control" id="customer_details" required="required"'); ?>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                            <?= lang('default_billgenerator','default_billgenerator'); ?>
                            <?php $billgen =  array(0 => lang('no'), 1 => lang('yes')); 
                            ?>
                            <?= form_dropdown('default_billgenerator', $billgen, $pos->default_billgenerator, 'class="form-control" id="default_billgenerator" required="required"'); ?>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                            <?= lang('merge_bill'); ?>
                            <?php $merger_bill =  array(0 => lang('no'), 1 => lang('yes')); 
                            ?>
                            <?= form_dropdown('merge_bill', $merger_bill, $pos->merge_bill, 'class="form-control" id="merge_bill" required="required"'); ?>
                        </div>
                    </div>
                    
                    
                                        

                    <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                            <?= lang('open_sale_register'); ?>
                            <?php $billgen =  array(0 => lang('disable'), 1 => lang('enable')); 
                            ?>
                            <?= form_dropdown('open_sale_register', $billgen, $pos->open_sale_register, 'class="form-control" id="open_sale_register" required="required"'); ?>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                            <?= lang('birthday_discount_for_alacarte'); ?>
                            <?php $bd =  array(0 => lang('disable'), 1 => lang('enable')); 
                            ?>
                            <?= form_dropdown('birthday_enable', $bd, $pos->birthday_enable, 'class="form-control" id="birthday_enable" required="required"'); ?>
                        </div>
                    </div>
                    
                    <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                            <?= lang('birthday_discount'); ?>
                            <?php 
                            for($m=0; $m<100; $m++){
                                $bdadata[$m.'%'] = $m.'%';                                
                            }
                            ?>
                            <?= form_dropdown('birthday_discount', $bdadata, $pos->birthday_discount, 'class="form-control" id="birthday_discount" required="required"'); ?>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                            <?= lang('birthday_discount_for_BBQ'); ?>
                            <?php $bd =  array(0 => lang('disable'), 1 => lang('enable')); 
                            ?>
                            <?= form_dropdown('birthday_enable_bbq', $bd, $pos->birthday_enable_bbq, 'class="form-control" id="birthday_enable_bbq" required="required"'); ?>
                        </div>
                    </div>
                    
                    <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                            <?= lang('birthday_discount_for_bbq'); ?>
                            <?php 
                            for($m=0; $m<100; $m++){
                                $bdadata[$m.'%'] = $m.'%';                                
                            }
                            ?>
                            <?= form_dropdown('birthday_discount_for_bbq', $bdadata, $pos->birthday_discount_for_bbq, 'class="form-control" id="birthday_discount_for_bbq" required="required"'); ?>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                            <?= lang('reprint_from_last_N_day'); ?>
                            <?= form_input('reprint_from_last_day', $pos->reprint_from_last_day, 'class="form-control numberonly" id="reprint_from_last_day" maxlength="2" required="required"'); ?>
                        </div>
                    </div>

                   

                   <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                            <?= lang('loyalty_option', 'loyalty_option'); ?>
                            <?php $loyalty_option =  array(0 => lang('disable'), 1 => lang('enable')); 
                            ?>
                            <?= form_dropdown('loyalty_option', $loyalty_option, $pos->loyalty_option, 'class="form-control" id="loyalty_option" required="required"'); ?>
                        </div>
                    </div>                  

                   
                </fieldset>

                <fieldset class="scheduler-border">
                    <legend class="scheduler-border"><?= lang('pos_orderscreen_settings') ?></legend>
                    <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                            <?= lang('category_display','category_display'); ?>
                            <?php $itemdisplay =  array(0 => lang('image'), 1 => lang('button')); 
                            ?>
                            <?= form_dropdown('category_display', $itemdisplay, $pos->category_display, 'class="form-control" id="category_display" required="required"'); ?>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                            <?= lang('subcategory_display','subcategory_display'); ?>
                            <?php $itemdisplay =  array(0 => lang('image'), 1 => lang('button')); 
                            ?>
                            <?= form_dropdown('subcategory_display', $itemdisplay, $pos->subcategory_display, 'class="form-control" id="subcategory_display" required="required"'); ?>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                            <?= lang('sale_item_display','sale_item_display'); ?>
                            <?php $itemdisplay =  array(0 => lang('image'), 1 => lang('button')); 
                            ?>
                            <?= form_dropdown('sale_item_display', $itemdisplay, $pos->sale_item_display, 'class="form-control" id="sale_item_display" required="required"'); ?>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                            <?= lang('order_item_customization', 'order_item_customization'); ?>
                            <?php $order_item_customization =  array(0 => lang('disable'), 1 => lang('enable')); 
                            ?>
                            <?= form_dropdown('order_item_customization', $order_item_customization, $pos->order_item_customization, 'class="form-control" id="order_item_customization" '); ?>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                            <?= lang('item_addon', 'item_addon'); ?>
                            <?php $item_addon =  array(0 => lang('disable'), 1 => lang('enable')); 
                            ?>
                            <?= form_dropdown('item_addon', $item_addon, $pos->item_addon, 'class="form-control" id="item_addon" required="item_addon"'); ?>
                        </div>
                    </div>
                  
                    <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                            <?= lang('item_comment', 'item_comment'); ?>
                            <?php $item_comment =  array(0 => lang('disable'), 1 => lang('enable')); 
                            ?>
                            <?= form_dropdown('item_comment', $item_comment, $pos->item_comment, 'class="form-control" id="item_comment" required="required"'); ?>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                            <?= lang('item_comment_price_option', 'item_comment_price_option'); ?>
                            <?php $item_comment_price_option =  array(0 => lang('disable'), 1 => lang('enable')); 
                            ?>
                            <?= form_dropdown('item_comment_price_option', $item_comment_price_option, $pos->item_comment_price_option, 'class="form-control" id="item_comment_price_option" required="required"'); ?>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                            <?= lang('sales_item_in_pos', 'sales_item_in_pos'); ?>
                            <?php $sales_item_in_pos =  array(1 => lang('by_sale_items'), 2 => lang('by_day_mapping')); 
                            ?>
                            <?= form_dropdown('sales_item_in_pos', $sales_item_in_pos, $pos->sales_item_in_pos, 'class="form-control" id="sales_item_in_pos" required="required"'); ?>
                        </div>
                    </div> 

                    <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                            <?= lang('categories_list_by', 'categories_list_by'); ?>
                            <?php $categories_list_by =  array(0 => lang('order_by_id'), 1 => lang('order_by_Alphaname')); 
                            ?>
                            <?= form_dropdown('categories_list_by', $categories_list_by, $pos->categories_list_by, 'class="form-control" id="categories_list_by" required="required"'); ?>
                        </div>
                    </div>

                </fieldset>

                <fieldset class="scheduler-border">
                    <legend class="scheduler-border"><?= lang('table_&_pos_header_settings') ?></legend>
                    <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                            <?= lang('order_screen_font_size','order_screen_font_size'); ?>
                            <?php $orderscreenfontsize =  array(0 => lang('medium'), 1 => lang('high')); 
                            ?>
                            <?= form_dropdown('order_screen_font_size', $orderscreenfontsize, $pos->order_screen_font_size, 'class="form-control" id="pos_header" required="required"'); ?>
                        </div>
                    </div>                    
                    <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                            <?= lang('font_family','font_family'); ?>
                            <?php $fontfamily =  array(0 => lang('open_sansregular'), 1 => lang('Akbalthom_KhmerNew')); 
                            ?>
                            <?= form_dropdown('font_family', $fontfamily, $pos->font_family, 'class="form-control" id="font_family" required="required"'); ?>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                            <?= lang('table_change','table_change'); ?>
                            <?php $tablechange =  array(0 => lang('no'), 1 => lang('yes')); 
                            ?>
                            <?= form_dropdown('table_change', $tablechange, $pos->table_change, 'class="form-control" id="table_change" required="required"'); ?>
                        </div>
                    </div>
                   <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                            <?= lang('table_size','table_size'); ?>
                            <?php $tablesize =  array(0 => lang('small'), 1 => lang('large')); 
                            ?>
                            <?= form_dropdown('table_size', $tablesize, $pos->table_size, 'class="form-control" id="table_size" required="required"'); ?>
                        </div>
                    </div>                    

                    <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                             <?= lang('table_available_color','table_available_color'); ?>
                            <?php $available_color =  array('green_ribbon/green_class' => lang('green'), 'blue_ribbon/blue_class' => lang('blue'),'orange_ribbon/orange_class' => lang('orange'),'red_ribbon/red_class' => lang('red'),'grey_ribbon/grey_class' => lang('grey')); 
                            ?>
                            <?= form_dropdown('table_available_color', $available_color, $pos->table_available_color, 'class="form-control" id="table_available_color" required="required"'); ?>

                            <!-- <?= lang('table_available_color', 'table_available_color'); ?><strong> *</strong>
                            <select class="form-control" name="table_available_color">
                                <option value="green_ribbon/green_class"><?= lang('green')?></option>
                                <option value="blue_ribbon/blue_class"><?= lang('blue')?></option>
                                <option value="orange_ribbon/orange_class"><?= lang('orange')?></option>
                                <option value="red_ribbon/red_class"><?= lang('red')?></option>
                                <option value="grey_ribbon/grey_class"><?= lang('grey')?></option>
                            </select>   -->                          
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                            <?= lang('table_kitchen_color','table_kitchen_color'); ?>
                            <?php $kitchen_color =  array('green_ribbon/green_class' => lang('green'), 'blue_ribbon/blue_class' => lang('blue'),'orange_ribbon/orange_class' => lang('orange'),'red_ribbon/red_class' => lang('red'),'grey_ribbon/grey_class' => lang('grey')); 
                            ?>
                            <?= form_dropdown('table_kitchen_color', $kitchen_color, $pos->table_kitchen_color, 'class="form-control" id="table_kitchen_color" required="required"'); ?>                            
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                            <?= lang('table_pending_color','table_pending_color'); ?>
                            <?php $pending_color =  array('green_ribbon/green_class' => lang('green'), 'blue_ribbon/blue_class' => lang('blue'),'orange_ribbon/orange_class' => lang('orange'),'red_ribbon/red_class' => lang('red'),'grey_ribbon/grey_class' => lang('grey')); 
                            ?>
                            <?= form_dropdown('table_pending_color', $pending_color, $pos->table_pending_color, 'class="form-control" id="table_pending_color" required="required"'); ?>
                        </div>
                    </div>  
                    <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                            <?= lang('table_display_option','table_display_option'); ?>
                            <?php $tableicon =  array(0 => lang('with_table_icon'), 1 => lang('without_table_icon')); 
                            ?>
                            <?= form_dropdown('table_display_option', $tableicon, $pos->table_display_option, 'class="form-control" id="table_display_option" required="required"'); ?>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                            <?= lang('pos_types_display_option','pos_types_display_option'); ?>
                            <?php $pos_types =  array(0 => lang('with_icon'), 1 => lang('without_icon')); 
                            ?>
                            <?= form_dropdown('pos_types_display_option', $pos_types, $pos->pos_types_display_option, 'class="form-control" id="pos_types_display_option" required="required"'); ?>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                            <?= lang('variant_display_option','variant_display_option'); ?>
                            <?php $variant_display =  array(0 => lang('botton_format'), 1 => lang('list_format')); 
                            ?>
                            <?= form_dropdown('variant_display_option', $variant_display, $pos->variant_display_option, 'class="form-control" id="variant_display_option" required="required"'); ?>
                        </div>
                    </div>                  
                </fieldset>

                <fieldset class="scheduler-border">
                    <legend class="scheduler-border"><?= lang('tax_and_taxation_and_service_charge') ?></legend>

                     <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                            <?= lang('default_tax', 'default_tax'); ?>
                            <?php
                            $t[0] = "";
                            foreach ($taxs as $tax) {
                                $t[$tax->id] = $tax->name;
                            }
                            if (isset($_POST['tax'])) {
                                $tax = $_POST['tax'];
                            } else {
                                $tax = "";
                            }
                            echo form_dropdown('tax', $t, $pos->default_tax, 'class="form-control" id="default_tax" required="required" style="width:100%;"');
                            ?>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                            <?= lang('tax_type', 'tax_type'); ?>
                             <?php 
                             $taxtype =  array(0 => lang('Inclusive'), 1 => lang('Exclusive'));                              
                             ?>
                            <?= form_dropdown('tax_type', $taxtype, $pos->tax_type, 'class="form-control" id="tax_type" required="required" style="width:100%;"'); ?>
                        </div>
                    </div>


                    <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                            <?= lang('display_tax', 'display_tax'); ?>
                            <?php $display_tax =  array(0 => lang('OFF'), 1 => lang('ON')); 
                            ?>
                            <?= form_dropdown('display_tax', $display_tax, $pos->display_tax, 'class="form-control" id="display_tax" required="required"'); ?>
                        </div>
                    </div> 
                  

                    <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                            <?= lang('display_tax_amt', 'display_tax_amt'); ?>
                            <?php $display_tax_amt =  array(0 => lang('OFF'), 1 => lang('ON')); 
                            ?>
                            <?= form_dropdown('display_tax_amt', $display_tax_amt, $pos->display_tax_amt, 'class="form-control" id="display_tax_amt" required="required"'); ?>
                        </div>
                    </div> 
                  
                     <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                            <?= lang('default_service_charge', 'default_service_charge'); ?>
                            <?php
                            $sc[0] = "";
                            foreach ($service_charge as $service) {
                                $sc[$service->id] = $service->name;
                            }
                           /* if (isset($_POST['default_service_charge'])) {
                                $service = $_POST['default_service_charge'];
                            } else {
                                $service = "";
                            }*/
                            echo form_dropdown('default_service_charge', $sc, $pos->default_service_charge, 'class="form-control" id="default_service_charge" required="required" style="width:100%;"');
                            ?>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                            <?= lang('service_charge_option', 'service_charge_option'); ?>
                             <?php 
                             $servicechargeoption =  array(0 => lang('disabled'), 1 => lang('enabled'));
                             ?>
                            <?= form_dropdown('service_charge_option', $servicechargeoption, $pos->service_charge_option, 'class="form-control" id="service_charge_option" required="required" style="width:100%;"'); ?>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                            <?= lang('taxation_report_settings', 'taxation_report_settings'); ?>
                            <?php $taxation_report_settings =  array(0 => lang('OFF'), 1 => lang('ON')); 
                            ?>
                            <?= form_dropdown('taxation_report_settings', $taxation_report_settings, $pos->taxation_report_settings, 'class="form-control" id="taxation_report_settings" required="required"'); ?>
                        </div>
                    </div>                  

                    <div class="col-md-4 col-sm-4 taxation_password_settings" style="display:<?=($pos->taxation_report_settings==1)?'block':'none'?>">     
                        <div class="form-group">                           
                                <label class="control-label" for="taxation_all"><?= lang("taxation_all")?></label>
                                <?= form_input('taxation_all', ($pos->taxation_all!=0)?$pos->taxation_all:'', 'class="form-control numberonly" id="taxation_all"'); ?>                           
                        </div>
                    </div> 

                     <div class="col-md-4 col-sm-4 taxation_password_settings" style="display:<?=($pos->taxation_report_settings==1)?'block':'none'?>">                     
                        <div class="form-group ">                           
                                <label class="control-label" for="taxation_include"><?= lang("taxation_include")?></label>
                                <?= form_input('taxation_include', ($pos->taxation_include!=0)?$pos->taxation_include:'', 'class="form-control numberonly" id="taxation_include"'); ?>                           
                        </div>
                    </div>
                    

                     <div class="col-md-4 col-sm-4 taxation_password_settings" style="display:<?=($pos->taxation_report_settings==1)?'block':'none'?>">
                        <div class="form-group ">                           
                                <label class="control-label" for="taxation_exclude"><?= lang("taxation_exclude")?></label>
                                <?= form_input('taxation_exclude', ($pos->taxation_exclude!=0)?$pos->taxation_exclude:'', 'class="form-control numberonly" id="taxation_exclude"'); ?>                           
                        </div>

                    </div>

                    <div class="col-md-4 col-sm-4 taxation_password_settings" style="display:<?=($pos->taxation_report_settings==1)?'block':'none'?>">
                        <div class="form-group ">                           
                                <label class="control-label" for="taxation_bill_start_from"><?= lang("taxation_bill_start_from")?></label>
                                <?= form_input('taxation_bill_start_from', ($pos->taxation_bill_start_from!=0)?$pos->taxation_bill_start_from:'', 'class="form-control " maxlength="10" id="taxation_bill_start_from"'); ?>                           
                        </div>
                    </div>

                     <div class="col-md-4 col-sm-4 taxation_password_settings" style="display:<?=($pos->taxation_report_settings==1)?'block':'none'?>">
                        <div class="form-group ">                           
                                <label class="control-label" for="taxation_bill_prefix"><?= lang("taxation_bill_prefix")?></label>
                                <?= form_input('taxation_bill_prefix', ($pos->taxation_bill_prefix!='')?$pos->taxation_bill_prefix:'', 'class="form-control"  maxlength="10" id="taxation_bill_prefix"'); ?>
                        </div>
                    </div>
                       <div class="col-md-4 col-sm-4 taxation_password_settings" style="display:<?=($pos->taxation_report_settings==1)?'block':'none'?>">
                        <div class="form-group">
                            <?= lang('bill_series_settings', 'bill_series_settings'); ?>
                            <?php $bill_series_settings =  array(0 => lang('continue_series'), 1 => lang('print_dont_print_series')); 
                            ?>
                            <?= form_dropdown('bill_series_settings', $bill_series_settings, $pos->bill_series_settings, 'class="form-control" id="bill_series_settings" required="required"'); ?>
                        </div>
                    </div> 

                    <div class="col-md-4 col-sm-4 tax_caption">
                        <div class="form-group ">                           
                                <label class="control-label" for="tax_caption"><?= lang("tax_caption")?></label>
                                <?= form_input('tax_caption', ($pos->tax_caption!='')?$pos->tax_caption:'', 'class="form-control"  maxlength="20" id="tax_caption"'); ?>
                        </div>
                    </div>
                </fieldset>
                <fieldset class="scheduler-border">
                    <legend class="scheduler-border"><?= lang('pos_printing_and_kot') ?></legend>

                    <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                            <?= lang('kot_enable_disable', 'kot_enable_disable'); ?>
                            <?php $kot =  array(0 => lang('disabled'), 1 => lang('enabled')); 
                            ?>
                            <?= form_dropdown('kot_enable_disable', $kot, $pos->kot_enable_disable, 'class="form-control" id="kot_enable_disable" required="required"'); ?>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                            <?= lang('kot_font_size', 'kot_font_size'); ?>
                            <?php $kotfont =  array(0 => lang('small'), 1 => lang('medium'), 2 => lang('large')); 
                            ?>
                            <?= form_dropdown('kot_font_size', $kotfont, $pos->kot_font_size, 'class="form-control" id="kot_font_size" required="required"'); ?>
                        </div>
                    </div>

                   <!--  <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                            <?= lang('kot_enable_disable', 'kot_enable_disable'); ?>
                            <?php $kot =  array(0 => lang('disabled'), 1 => lang('enabled')); 
                            ?>
                            <?= form_dropdown('kot_enable_disable', $kot, $pos->kot_enable_disable, 'class="form-control" id="kot_enable_disable" required="required"'); ?>
                        </div>
                    </div> -->


                    <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                            <?= lang('consolidated_kot_print','consolidated_kot_print'); ?>
                            <?php
                            $kotconsolid['0'] = lang('disable');
                            $printer_opts = array();
                            if (!empty($printers)) {
                                foreach ($printers as $printer) {
                                    $kotconsolid[$printer->id] = $printer->title;
                                }
                            }
                            echo form_dropdown('consolidated_kot_print', $kotconsolid, $pos->consolidated_kot_print, 'class="form-control" id="consolidated_kot_print" required="required" style="width:100%;"');
                            ?>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                            <?= lang('consolidated_kot_print_option', 'consolidated_kot_print_option'); ?>
                            <?php $kot =  array(0 => lang('table_area_wise_kot'), 1 => lang('all_items_consolidated_kot')); 
                            ?>
                            <?= form_dropdown('consolidated_kot_print_option', $kot, $pos->consolidated_kot_print_option, 'class="form-control" id="consolidated_kot_print_option" required="required"'); ?>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                            <?= lang('kot_print_option', 'kot_print_option'); ?>
                            <?php $kot =  array(0 => lang('all_items'), 1 => lang('single_items')); 
                            ?>
                            <?= form_dropdown('kot_print_option', $kot, $pos->kot_print_option, 'class="form-control" id="kot_print_option" required="required"'); ?>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                            <?= lang('kot_print_lang_option', 'kot_print_lang_option'); ?>
                            <?php $kot_lang =  array(0 => lang('english'), 1 => lang('local_language'), 2 => lang('both')); 
                            ?>
                            <?= form_dropdown('kot_print_lang_option', $kot_lang, $pos->kot_print_lang_option, 'class="form-control" id="kot_print_lang_option" required="required"'); ?>
                        </div>
                    </div>    
                   <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                            <label class="control-label" for="qsr_kot_print" style="position: relative;top: 5px;padding-right: 10px;"><?= lang("qsr_kot_print"); ?></label>
                            <div class="switch-field">
                            
                            <input type="radio" value="0" id="qsr_kot_print_switch_left" class="skip" name="qsr_kot_print" <?php echo ($pos->qsr_kot_print==0) ? "checked" : ''; ?>>
                            <label for="qsr_kot_print_switch_left">OFF</label>
                            <input type="radio" value="1" id="qsr_kot_print_switch_right" class="skip" name="qsr_kot_print" <?php echo ($pos->qsr_kot_print==1) ? "checked" : ''; ?>>
                            <label for="qsr_kot_print_switch_right">ON</label>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                            <label class="control-label" for="kot_print_logo" style="position: relative;top: 5px;padding-right: 10px;"><?= lang("kot_print_logo"); ?></label>
                            <div class="switch-field">
                            
                            <input type="radio" value="0" id="kot_print_logo_switch_left" class="skip" name="kot_print_logo" <?php echo ($pos->kot_print_logo==0) ? "checked" : ''; ?>>
                            <label for="kot_print_logo_switch_left">OFF</label>
                            <input type="radio" value="1" id="kot_print_logo_switch_right" class="skip" name="kot_print_logo" <?php echo ($pos->kot_print_logo==1) ? "checked" : ''; ?>>
                            <label for="kot_print_logo_switch_right">ON</label>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                            <?= lang('Order_no_display', 'Order_no_display'); ?>
                            <?php $order_no_display =  array(0 => lang('no'), 1 => lang('yes')); 
                            ?>
                            <?= form_dropdown('order_no_display', $order_no_display, $pos->order_no_display, 'class="form-control" id="order_no_display" required="required"'); ?>
                        </div>
                    </div> 

                    <div class="col-md-4">
                        <div class="form-group">
                            <?= lang('printing', 'remote_printing'); ?>
                            <?php
                            $opts = array(0 => lang('local_install'), 1 => lang('web_browser_print'), 3 => lang('php_pos_print_app'));
                            ?>
                            <?= form_dropdown('remote_printing', $opts, $pos->remote_printing, 'class="form-control select2" id="remote_printing" style="width:100%;" required="required"'); ?>
                            
                            <?php if (DEMO) { ?>
                            <span class="help-block">On demo, you can test web printing only.</span>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <?= lang('Print_option', 'Print_option'); ?>
                            <?php
                            $opts = array(0 => lang('without_exchange_amount'), 1 => lang('with_exchange_amount'));
                            ?>
                            <?= form_dropdown('print_option', $opts, $pos->print_option, 'class="form-control select2" id="print_option" style="width:100%;" required="required"'); ?>                            
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <?= lang('Print_local_langaue', 'Print_local_langaue'); ?>
                            <?php
                            $opts = array(0 => lang('without_local_language'), 1 => lang('with_local_language'));
                            ?>
                            <?= form_dropdown('print_local_language', $opts, $pos->print_local_language, 'class="form-control select2" id="print_local_language" style="width:100%;" required="required"'); ?>                            
                        </div>
                    </div>
                
                    <div class="col-md-4 col-sm-4" >
                        <div class="form-group">
                            <label class="control-label" for="pre_printed_format" style="position: relative;top: 5px;padding-right: 10px;"><?= lang("pre_printed_format"); ?></label>
                            <div class="switch-field">
                            
                            <input type="radio" value="1" id="pre_printed_format_switch_left" class="skip pre_printed_format" name="pre_printed_format" <?php echo ($pos->pre_printed_format==1) ? "checked" : ''; ?>>
                            <label for="pre_printed_format_switch_left">YES</label>
                            <input type="radio" value="0" id="pre_printed_format_switch_right" class="skip pre_printed_format" name="pre_printed_format" <?php echo ($pos->pre_printed_format==0) ? "checked" : ''; ?>>
                            <label for="pre_printed_format_switch_right">ON</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <?= lang('consolid_kot_print_logo', 'consolid_kot_print_logo'); ?>
                            <?php
                            $consolidkotprintlogo = array(0 => lang('disabled'), 1 => lang('enabled'));
                            ?>
                            <?= form_dropdown('consolid_kot_print_logo', $consolidkotprintlogo, $pos->consolid_kot_print_logo, 'class="form-control select2" id="consolid_kot_print_logo" style="width:100%;" required="required"'); ?>                            
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <?= lang('kot_order_no_print_option', 'kot_order_no_print_option'); ?>
                            <?php
                            $kotprintorderno = array(0 => lang('disabled'), 1 => lang('enabled'));
                            ?>
                            <?= form_dropdown('kot_order_no_print_option', $kotprintorderno, $pos->kot_order_no_print_option, 'class="form-control select2" id="kot_order_no_print_option" style="width:100%;" required="required"'); ?>                            
                        </div>
                    </div>                    

                    <div class="col-md-12 row">
                    <div class="col-md-4 col-sm-4 pre_printed_header" >
                        <div class="form-group ">                           
                                <label class="control-label" for="pre_printed_header"><?= lang("print_header_space")?></label>
                                <?= form_input('pre_printed_header', ($pos->pre_printed_header!=0)?$pos->pre_printed_header:'', 'class="form-control " maxlength="10" id="pre_printed_header"'); ?>                           
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-4 print_footer_space" >
                        <div class="form-group ">                           
                                <label class="control-label" for="print_footer_space"><?= lang("print_footer_space")?></label>
                                <?= form_input('print_footer_space', ($pos->print_footer_space!=0)?$pos->print_footer_space:'', 'class="form-control " maxlength="10" id="print_footer_space"'); ?>                           
                        </div>
                    </div>
                     <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                            <label class="control-label" for="reprint_bill_caption" style="position: relative;top: 5px;padding-right: 10px;"><?= lang("reprint_bill_caption"); ?></label>
                            <div class="switch-field">
                            
                            <input type="radio" value="0" id="reprint_bill_caption_switch_left" class="skip" name="reprint_bill_caption" <?php echo ($pos->reprint_bill_caption==0) ? "checked" : ''; ?>>
                            <label for="reprint_bill_caption_switch_left">OFF</label>
                            <input type="radio" value="1" id="reprint_bill_caption_switch_right" class="skip" name="reprint_bill_caption" <?php echo ($pos->reprint_bill_caption==1) ? "checked" : ''; ?>>
                            <label for="reprint_bill_caption_switch_right">ON</label>
                            </div>
                        </div>
                    </div>
                    </div>

                     <div class="col-md-4">
                        <div class="form-group">
                            <?= lang('bill_print_format', 'bill_print_format'); ?>
                            <?php
                            $billprintformat = array(1 => lang('discount_apply_after_subtotal'), 2 => lang('discount_apply_in_item_rows'), 3 => lang('Indian Tax'), 4 => lang('local_language'));
                            ?>
                            <?= form_dropdown('bill_print_format', $billprintformat, $pos->bill_print_format, 'class="form-control select2" id="bill_print_format" style="width:100%;" required="required"'); ?>                            
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <?= lang('billgeneration_screen', 'billgeneration_screen'); ?>
                            <?php
                            $billgeneration_screen = array(1 => lang('global'), 2 => lang('template 2'));
                            ?>
                            <?= form_dropdown('billgeneration_screen', $billgeneration_screen, $pos->billgeneration_screen, 'class="form-control select2" id="billgeneration_screen" style="width:100%;" required="required"'); ?>                            
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <?= lang('customer_discount_editable', 'customer_discount_editable'); ?>
                            <?php
                            $customer_discount_editable = array(1 => lang('yes'), 0 => lang('no'));
                            ?>
                            <?= form_dropdown('customer_discount_editable', $customer_discount_editable, $pos->customer_discount_editable, 'class="form-control select2" id="customer_discount_editable" style="width:100%;" required="required"'); ?>                            
                        </div>
                    </div>

                    <div class="clearfix"></div>

                    <div class="printers">

                        <div class="col-md-6">
                            <div class="form-group">
                                <?= lang("auto_print", 'auto_print'); ?> <strong>*</strong>
                                <?= form_dropdown('auto_print', $yn, $pos->auto_print, 'class="form-control select2" id="auto_print" style="width:100%;"'); ?>
                            </div>
                        </div>

                        <div class="ppp">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <?= lang('use_local_printers', 'local_printers'); ?>
                                    <?= form_dropdown('local_printers', $yn, set_value('local_printers', $pos->local_printers), 'class="form-control tip" id="local_printers"  required="required"'); ?>
                                </div>
                            </div>
                        </div>

                        <div class="lp">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <?= lang('receipt_printer', 'receipt_printer'); ?> <strong>*</strong>
                                    <?php
                                    $printer_opts = array();
                                    if (!empty($printers)) {
                                        foreach ($printers as $printer) {
                                            $printer_opts[$printer->id] = $printer->title;
                                        }
                                    }
                                    ?>
                                    <?= form_dropdown('receipt_printer', $printer_opts, $pos->printer, 'class="form-control select2" id="receipt_printer" style="width:100%;"'); ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <?= lang('order_printers', 'order_printers'); ?> <strong>*</strong>
                                    <?= form_dropdown('order_printers[]', $printer_opts, '', 'multiple class="form-control select2" id="order_printers" style="width:100%;"'); ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <?= lang('cash_drawer_codes', 'cash_drawer_codes'); ?>
                                    <?= form_input('cash_drawer_codes', $pos->cash_drawer_codes, 'class="form-control" id="cash_drawer_codes" placeholder="\x1C"'); ?>
                                </div>
                            </div>
                        </div>
                    </div>

                     <div class="col-md-4">
                        <div class="form-group">
                            <?= lang('manual_item_discount_display_option', 'manual_item_discount_display_option'); ?>
                            <?php
                            $manualdisdisplayoption = array(0 => lang('amount'), 1 => lang('percentage'));
                            ?>
                            <?= form_dropdown('manual_item_discount_display_option', $manualdisdisplayoption, $pos->manual_item_discount_display_option, 'class="form-control select2" id="manual_item_discount_display_option" style="width:100%;" required="required"'); ?>                            
                        </div>
                    </div>
                     <div class="col-md-4">
                        <div class="form-group">
                            <?= lang('discount_popup_screen_in_rough_payment', 'discount_popup_screen_in_rough_payment'); ?>
                            <?php
                            $discountpopupscreeninroughpayment = array(0 => lang('disabled'), 1 => lang('enabled'));
                            ?>
                            <?= form_dropdown('discount_popup_screen_in_rough_payment', $discountpopupscreeninroughpayment, $pos->discount_popup_screen_in_rough_payment, 'class="form-control select2" id="discount_popup_screen_in_rough_payment" style="width:100%;" required="required"'); ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <?= lang('discount_popup_screen_in_bill_print', 'discount_popup_screen_in_bill_print'); ?>
                            <?php
                            $discountpopupscreeninbillprint = array(0 => lang('disabled'), 1 => lang('enabled'));
                            ?>
                            <?= form_dropdown('discount_popup_screen_in_bill_print', $discountpopupscreeninbillprint, $pos->discount_popup_screen_in_bill_print, 'class="form-control select2" id="discount_popup_screen_in_bill_print" style="width:100%;" required="required"'); ?>                            
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <?= lang('discount_note_display_option', 'discount_note_display_option'); ?>
                            <?php
                            $discountnotedisplayoption = array(0 => lang('disabled'), 1 => lang('enabled'));
                            ?>
                            <?= form_dropdown('discount_note_display_option', $discountnotedisplayoption, $pos->discount_note_display_option, 'class="form-control select2" id="discount_note_display_option" style="width:100%;" required="required"'); ?>                            
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <?= lang('discount_popup_screen_in_payment', 'discount_popup_screen_in_payment'); ?>
                            <?php
                            $discountpopupscreeninpayment = array(0 => lang('disabled'), 1 => lang('enabled'));
                            ?>
                            <?= form_dropdown('discount_popup_screen_in_payment', $discountpopupscreeninpayment, $pos->discount_popup_screen_in_payment, 'class="form-control select2" id="discount_popup_screen_in_payment" style="width:100%;" required="required"'); ?>                            
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <?= lang('manual_and_customer_discount_consolidate_percentage_display_option', 'manual_and_customer_discount_consolidate_percentage_display_option'); ?>
                            <?php
                            $manual_and_customer_discount_consolid_percentage_display_option = array(0 => lang('disabled'), 1 => lang('enabled'));
                            ?>
                            <?= form_dropdown('manual_and_customer_discount_consolid_percentage_display_option', $manual_and_customer_discount_consolid_percentage_display_option, $pos->manual_and_customer_discount_consolid_percentage_display_option, 'class="form-control select2" id="manual_and_customer_discount_consolid_percentage_display_option" style="width:100%;" required="required"'); ?>                            
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <?= lang('consolidated_reprint_print', 'consolidated_reprint_print'); ?>
                            <?php
                            $consolidatedreprintprint = array(0 => lang('disabled'), 1 => lang('enabled'));
                            ?>
                            <?= form_dropdown('consolidated_reprint_print', $consolidatedreprintprint, $pos->consolidated_reprint_print, 'class="form-control select2" id="consolidated_reprint_print" style="width:100%;" required="required"'); ?>                            
                        </div>
                    </div> 
                     <!--  <div class="col-md-4"> -->
                          <div class="col-md-6 "> 
                        <div class="form-group">
                            <label class="control-label" for="total_covers" style="position: relative;top: 5px;padding-right: 10px;"> <?= lang('No Of Covers'); ?></label>
                            <div class="switch-field">
                            
                            <input type="radio" value="0" id="total_covers_switch_left" class="skip" name="total_covers" <?php echo ($pos->total_covers==0) ? "checked" : ''; ?>>
                            <label for="total_covers_switch_left">OFF</label>
                            <input type="radio" value="1" id="total_covers_switch_right" class="skip" name="total_covers" <?php echo ($pos->total_covers==1) ? "checked" : ''; ?>>
                            <label for="total_covers_switch_right">ON</label>
                            </div>
                       <!--  </div> -->
                    </div>
                    </div>    


                </fieldset>
                <fieldset class="scheduler-border">
                    <legend class="scheduler-border"><?= lang('custom_fileds') ?></legend>
                    <div class="col-md-6 col-sm-6">
                        <div class="form-group">
                            <?= lang('cf_title1', 'tcf1'); ?>
                            <?= form_input('cf_title1', $pos->cf_title1, 'class="form-control tip" id="tcf1"'); ?>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-6">
                        <div class="form-group">
                            <?= lang('cf_value1', 'vcf1'); ?>
                            <?= form_input('cf_value1', $pos->cf_value1, 'class="form-control tip" id="vcf1"'); ?>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-6">
                        <div class="form-group">
                            <?= lang('cf_title2', 'tcf2'); ?>
                            <?= form_input('cf_title2', $pos->cf_title2, 'class="form-control tip" id="tcf2"'); ?>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-6">
                        <div class="form-group">
                            <?= lang('cf_value2', 'vcf2'); ?>
                            <?= form_input('cf_value2', $pos->cf_value2, 'class="form-control tip" id="vcf2"'); ?>
                        </div>
                    </div>
                </fieldset>
                <fieldset class="scheduler-border">
                    <legend class="scheduler-border"><?= lang('shortcuts') ?></legend>
                    <p><?= lang('shortcut_heading') ?></p>

                    <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                            <?= lang('focus_add_item', 'focus_add_item'); ?>
                            <?= form_input('focus_add_item', $pos->focus_add_item, 'class="form-control tip" id="focus_add_item"'); ?>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                            <?= lang('add_manual_recipe', 'add_manual_recipe'); ?>
                            <?= form_input('add_manual_recipe', $pos->add_manual_recipe, 'class="form-control tip" id="add_manual_recipe"'); ?>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                            <?= lang('customer_selection', 'customer_selection'); ?>
                            <?= form_input('customer_selection', $pos->customer_selection, 'class="form-control tip" id="customer_selection"'); ?>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                            <?= lang('add_customer', 'add_customer'); ?>
                            <?= form_input('add_customer', $pos->add_customer, 'class="form-control tip" id="add_customer"'); ?>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                            <?= lang('toggle_category_slider', 'toggle_category_slider'); ?>
                            <?= form_input('toggle_category_slider', $pos->toggle_category_slider, 'class="form-control tip" id="toggle_category_slider"'); ?>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                            <?= lang('toggle_subcategory_slider', 'toggle_subcategory_slider'); ?>
                            <?= form_input('toggle_subcategory_slider', $pos->toggle_subcategory_slider, 'class="form-control tip" id="toggle_subcategory_slider"'); ?>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                            <?= lang('toggle_brands_slider', 'toggle_brands_slider'); ?>
                            <?= form_input('toggle_brands_slider', $pos->toggle_brands_slider, 'class="form-control tip" id="toggle_brands_slider"'); ?>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                            <?= lang('cancel_sale', 'cancel_sale'); ?>
                            <?= form_input('cancel_sale', $pos->cancel_sale, 'class="form-control tip" id="cancel_sale"'); ?>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                            <?= lang('suspend_sale', 'suspend_sale'); ?>
                            <?= form_input('suspend_sale', $pos->suspend_sale, 'class="form-control tip" id="suspend_sale"'); ?>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                            <?= lang('print_items_list', 'print_items_list'); ?>
                            <?= form_input('print_items_list', $pos->print_items_list, 'class="form-control tip" id="print_items_list"'); ?>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                            <?= lang('finalize_sale', 'finalize_sale'); ?>
                            <?= form_input('finalize_sale', $pos->finalize_sale, 'class="form-control tip" id="finalize_sale"'); ?>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                            <?= lang('today_sale', 'today_sale'); ?>
                            <?= form_input('today_sale', $pos->today_sale, 'class="form-control tip" id="today_sale"'); ?>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                            <?= lang('open_hold_bills', 'open_hold_bills'); ?>
                            <?= form_input('open_hold_bills', $pos->open_hold_bills, 'class="form-control tip" id="open_hold_bills"'); ?>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                            <?= lang('close_register', 'close_register'); ?>
                            <?= form_input('close_register', $pos->close_register, 'class="form-control tip" id="close_register"'); ?>
                        </div>
                    </div>
                </fieldset>

                <fieldset class="scheduler-border" style="display: none">
                    <legend class="scheduler-border"><?= lang('payment_gateways') ?></legend>
                    <?php
                    if ($paypal_balance) {
                        if (! isset ($paypal_balance['error']) ) {
                            echo '<div class="alert alert-success"><button data-dismiss="alert" class="close" type="button">×</button><strong>' . lang('paypal_balance') . '</strong><p>';
                            $blns = sizeof($paypal_balance['amount']);
                            $r = 1;
                            foreach ($paypal_balance['amount'] as $balance) {
                                echo lang('balance') . ': ' . $balance['L_AMT'] . ' (' . $balance['L_CURRENCYCODE'] . ')';
                                if ($blns != $r) {
                                    echo ', ';
                                }
                                $r++;
                            }
                            echo '</p></div>';
                        } else {
                            echo '<div class="alert alert-danger"><button data-dismiss="alert" class="close" type="button">×</button><p>';
                            foreach ($paypal_balance['message'] as $msg) {
                                echo $msg['L_SHORTMESSAGE'].' ('.$msg['L_ERRORCODE'].'): '.$msg['L_LONGMESSAGE'].'<br>';
                            }
                            echo '</p></div>';
                        }
                    }
                    ?>
                    <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                            <?= lang('paypal_pro', 'paypal_pro'); ?>
                            <?= form_dropdown('paypal_pro', $yn, $pos->paypal_pro, 'class="form-control" id="paypal_pro" required="required"'); ?>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div id="paypal_pro_con">
                        <div class="col-md-3 col-sm-3">
                            <div class="form-group">
                                <?= lang('APIUsername', 'APIUsername'); ?>
                                <?= form_input('APIUsername', $APIUsername, 'class="form-control tip" id="APIUsername"'); ?>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-3">
                            <div class="form-group">
                                <?= lang('APIPassword', 'APIPassword'); ?>
                                <?= form_input('APIPassword', $APIPassword, 'class="form-control tip" id="APIPassword"'); ?>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-6">
                            <div class="form-group">
                                <?= lang('APISignature', 'APISignature'); ?>
                                <?= form_input('APISignature', $APISignature, 'class="form-control tip" id="APISignature"'); ?>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <?php
                    if ($stripe_balance) {
                        echo '<div class="alert alert-success"><button data-dismiss="alert" class="close" type="button">×</button><strong>' . lang('stripe_balance') . '</strong>';
                        echo '<p>' . lang('pending_amount') . ': ' . $stripe_balance['pending_amount'] . ' (' . $stripe_balance['pending_currency'] . ')';
                        echo ', ' . lang('available_amount') . ': ' . $stripe_balance['available_amount'] . ' (' . $stripe_balance['available_currency'] . ')</p>';
                        echo '</div>';
                    }
                    ?>
                    <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                            <?= lang('stripe', 'stripe'); ?>
                            <?= form_dropdown('stripe', $yn, $pos->stripe, 'class="form-control" id="stripe" required="required"'); ?>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div id="stripe_con">
                        <div class="col-md-6 col-sm-6">
                            <div class="form-group">
                                <?= lang('stripe_secret_key', 'stripe_secret_key'); ?>
                                <?= form_input('stripe_secret_key', $stripe_secret_key, 'class="form-control tip" id="stripe_secret_key"'); ?>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-6">
                            <div class="form-group">
                                <?= lang('stripe_publishable_key', 'stripe_publishable_key'); ?>
                                <?= form_input('stripe_publishable_key', $stripe_publishable_key, 'class="form-control tip" id="stripe_publishable_key"'); ?>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                            <?= lang('authorize', 'authorize'); ?>
                            <?= form_dropdown('authorize', $yn, $pos->authorize, 'class="form-control" id="authorize" required="required"'); ?>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div id="authorize_con">
                        <div class="col-md-6 col-sm-6">
                            <div class="form-group">
                                <?= lang('api_login_id', 'api_login_id'); ?>
                                <?= form_input('api_login_id', $api_login_id, 'class="form-control tip" id="api_login_id"'); ?>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-6">
                            <div class="form-group">
                                <?= lang('api_transaction_key', 'api_transaction_key'); ?>
                                <?= form_input('api_transaction_key', $api_transaction_key, 'class="form-control tip" id="api_transaction_key"'); ?>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </fieldset>

                <button type="submit" class="btn btn-primary">
                <?= lang('update_settings') ?>
                </button>

              <!--   <?= form_submit('update_settings', lang('update_settings'), 'class="btn btn-primary"'); ?> -->

                <?= form_close(); ?>
            </div>

        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function (e) {
        $("#order_printers").select2().select2('val', <?= $pos->order_printers; ?>);
        if ($('#remote_printing').val() == 1) {
            $('.printers').slideUp();
        } else if ($('#remote_printing').val() == 0) {
            $('.printers').slideDown();
            $('.ppp').slideUp();
            $('.lp').slideDown();
        } else {
            $('.printers').slideDown();
            $('.ppp').slideDown();
            if ($('#local_printers').val() == 1) {
                $('.lp').slideUp();
            } else {
                $('.lp').slideDown();
            }
        }
        $('#remote_printing').change(function () {
            if ($(this).val() == 1) {
                $('.printers').slideUp();
            } else if ($(this).val() == 0) {
                $('.printers').slideDown();
                $('.ppp').slideUp();
                $('.lp').slideDown();
            } else {
                $('.printers').slideDown();
                $('.ppp').slideDown();
                if ($('#local_printers').val() == 1) {
                    $('.lp').slideUp();
                } else {
                    $('.lp').slideDown();
                }
            }
        });
        $('#local_printers').change(function () {
            if ($(this).val() == 1) {
                $('.lp').slideUp();
            } else {
                $('.lp').slideDown();
            }
        });


        $('#pos_setting').bootstrapValidator({
            feedbackIcons: {
                valid: 'fa fa-check',
                invalid: 'fa fa-times',
                validating: 'fa fa-refresh'
            }, excluded: [':disabled']
        });
        $('select.select').select2({minimumResultsForSearch: 7});
        $('input[type="checkbox"],[type="radio"]').not('.skip').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%' // optional
        });

        $('#customer1').val('<?= $pos->default_customer; ?>').select2({
            minimumInputLength: 1,
            data: [],
            initSelection: function (element, callback) {
                $.ajax({
                    type: "get", async: false,
                    url: site.base_url+"customers/getCustomer/" + $(element).val(),
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

        $('#paypal_pro').change(function () {
            var pp = $(this).val();
            if (pp == 1) {
                $('#paypal_pro_con').slideDown();
            } else {
                $('#paypal_pro_con').slideUp();
            }
        });
        $('#stripe').change(function () {
            var st = $(this).val();
            if (st == 1) {
                $('#stripe_con').slideDown();
            } else {
                $('#stripe_con').slideUp();
            }
        });
        $('#authorize').change(function () {
            var st = $(this).val();
            if (st == 1) {
                $('#authorize_con').slideDown();
            } else {
                $('#authorize_con').slideUp();
            }
        });
        var st = '<?=$pos->stripe?>';
        var pp = '<?=$pos->paypal_pro?>';
        var az = '<?=$pos->authorize?>';
        if (st == 1) {
            $('#stripe_con').slideDown();
        } else {
            $('#stripe_con').slideUp();
        }
        if (pp == 1) {
            $('#paypal_pro_con').slideDown();
        } else {
            $('#paypal_pro_con').slideUp();
        }
        if (st == 1) {
            $('#authorize_con').slideDown();
        } else {
            $('#authorize_con').slideUp();
        }

    });
</script>
<style>
.switch-field {
  position: absolute;
  display: inline;
}

.switch-title {
  margin-bottom: 6px;
}

.switch-field input {
    position: absolute !important;
    clip: rect(0, 0, 0, 0);
    height: 1px;
    width: 1px;
    border: 0;
    overflow: hidden;
}

.switch-field label {
  float: left;
}

.switch-field label {
  display: inline-block;
  width: 35px;
  background-color: #fffff;
  color: #000000;
  font-size: 14px;
  font-weight: normal;
  text-align: center;
  text-shadow: none;
  padding: 3px 5px;
  border: 1px solid rgba(0, 0, 0, 0.2);
  -webkit-box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.3), 0 1px rgba(255, 255, 255, 0.1);
  box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.3), 0 1px rgba(255, 255, 255, 0.1);
  -webkit-transition: all 0.1s ease-in-out;
  -moz-transition:    all 0.1s ease-in-out;
  -ms-transition:     all 0.1s ease-in-out;
  -o-transition:      all 0.1s ease-in-out;
  transition:         all 0.1s ease-in-out;
}

.switch-field label:hover {
	cursor: pointer;
}

.switch-field input:checked + label {
  background-color: #2489c5;
  -webkit-box-shadow: none;
  box-shadow: none;
  color: #fff;
}

.switch-field label:first-of-type {
  border-radius: 13px 0 0 13px;
}

.switch-field label:last-of-type {
  border-radius: 0 13px 13px 0;
}
</style>
<script>
    $(document).ready(function(){
       $('#taxation_report_settings').change(function(){
         if($(this).val()==1){
            $('.taxation_password_settings').show();
         }else{
            $('.taxation_password_settings').hide();
         }
       });
       
$(document).on('change', '#taxation_all', function (e) {

    var taxation_all = $(this).val();
    var taxation_include = $('#taxation_include').val();
    var taxation_exclude = $('#taxation_exclude').val(); 
       /*alert(taxation_all);
       alert(taxation_include);*/
        if((taxation_all === taxation_include) || (taxation_all === taxation_exclude)){
            bootbox.alert('Please Enter Different Pin Code');            
            $(this).val("<?php echo ($pos->taxation_all!=0) ?$pos->taxation_all:'' ?>");
        }        
});

$(document).on('change', '#taxation_include', function (e) {

    var taxation_include = $(this).val();
    var taxation_all = $('#taxation_all').val();
    var taxation_exclude = $('#taxation_exclude').val(); 
       
        if((taxation_include === taxation_all) || (taxation_include === taxation_exclude)){
            bootbox.alert('Please Enter Different Pin Code');            
            $(this).val("<?php echo ($pos->taxation_include!=0)?$pos->taxation_include:'' ?>");
        }        
});

$(document).on('change', '#taxation_exclude', function (e) {

    var taxation_exclude = $(this).val();
    var taxation_include = $('#taxation_include').val();
    var taxation_all = $('#taxation_all').val(); 
       
        if((taxation_exclude === taxation_include) || (taxation_exclude === taxation_all)){
            bootbox.alert('Please Enter Different Pin Code');            
            $(this).val("<?php echo ($pos->taxation_exclude!=0)?$pos->taxation_exclude:'' ?>");
        }        
});


       
    });
</script>
