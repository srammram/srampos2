<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<script>
    
</script>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-cog"></i><?= lang('sync_settings'); ?></h2>

       
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

            <p class="introtext"><?= lang('update_info'); ?></p>
            <?php //if(!empty($center_db_tables))  :?>
                <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form');
                echo admin_form_open_multipart("system_settings/sync_tables", $attrib);
                ?>
                <div class="row">
                    <div class="col-lg-12">
                        <fieldset class="scheduler-border">
                            <legend class="scheduler-border"><?= lang('DB_tables') ?></legend>
                           
                            <div class="col-md-4">
                                <div class="form-group">
                                    
                                    <label><input type="checkbox" name="data[]" <?=(in_array('products',$enabled_tables))?'checked="checked"':'';?> <?=$denied?> value="products">&nbsp;&nbsp;<?= lang("recipe"); ?></label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><input type="checkbox" name="data[]" <?=(in_array('users',$enabled_tables))?'checked="checked"':'';?> <?=$denied?> value="users">&nbsp;&nbsp;<?= lang("users"); ?></label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><input type="checkbox" name="data[]" <?=(in_array('suppliers',$enabled_tables))?'checked="checked"':'';?> <?=$denied?> value="suppliers">&nbsp;&nbsp;<?= lang("suppliers"); ?></label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><input type="checkbox" name="data[]" <?=(in_array('customers',$enabled_tables))?'checked="checked"':'';?> <?=$denied?> value="customers">&nbsp;&nbsp;<?= lang("customers"); ?></label>
                                </div>
                            </div>
                           <!--  <div class="col-md-4">
                                <div class="form-group">
                                    <label><input type="checkbox" name="data[]" <?=(in_array('tills',$enabled_tables))?'checked="checked"':'';?> <?=$denied?> value="tills">&nbsp;&nbsp;Sync Tills</label>
                                </div>
                            </div> -->
                            <!-- <div class="col-md-4">
                                <div class="form-group">
                                    <label><input type="checkbox" name="data[]" <?=(in_array('holdnotes',$enabled_tables))?'checked="checked"':'';?> <?=$denied?> value="holdnotes">&nbsp;&nbsp;Sync Hold Notes</label>
                                </div>
                            </div> -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><input type="checkbox" name="data[]" <?=(in_array('printers',$enabled_tables))?'checked="checked"':'';?> <?=$denied?> value="printers">&nbsp;&nbsp;<?= lang("printers"); ?></label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><input type="checkbox" name="data[]" <?=(in_array('tender_types',$enabled_tables))?'checked="checked"':'';?> <?=$denied?> value="tender_types">&nbsp;&nbsp;<?= lang("tender_types"); ?></label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><input type="checkbox" name="data[]" <?=(in_array('currencies',$enabled_tables))?'checked="checked"':'';?> <?=$denied?> value="currencies">&nbsp;&nbsp;<?= lang("currencies"); ?></label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><input type="checkbox" name="data[]" <?=(in_array('stores',$enabled_tables))?'checked="checked"':'';?> <?=$denied?> value="stores">&nbsp;&nbsp;<?= lang("stores"); ?></label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><input type="checkbox" name="data[]" <?=(in_array('tax',$enabled_tables))?'checked="checked"':'';?> <?=$denied?> value="tax">&nbsp;&nbsp;<?= lang("taxs"); ?></label>
                                </div>
                            </div>
                           <!--  <div class="col-md-4">
                                <div class="form-group">
                                    <label><input type="checkbox" name="data[]" <?=(in_array('expense_category',$enabled_tables))?'checked="checked"':'';?> <?=$denied?> value="expense_category">&nbsp;&nbsp;&nbsp;&nbsp;Sync Expense Category</label>
                                </div>
                            </div> -->
                            <!-- <div class="col-md-4">
                                <div class="form-group">
                                    <label><input type="checkbox" name="data[]" <?=(in_array('calendar',$enabled_tables))?'checked="checked"':'';?> <?=$denied?> value="calendar">&nbsp;&nbsp;&nbsp;&nbsp;Sync Calendar</label>
                                </div>
                            </div> -->
                            <!-- <div class="col-md-4">
                                <div class="form-group">
                                    <label><input type="checkbox" name="data[]" <?=(in_array('giftvoucher',$enabled_tables))?'checked="checked"':'';?> <?=$denied?> value="giftvoucher">&nbsp;&nbsp;&nbsp;&nbsp;Sync Giftvoucher</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><input type="checkbox" name="data[]" <?=(in_array('commissionslab',$enabled_tables))?'checked="checked"':'';?> <?=$denied?> value="commissionslab">&nbsp;&nbsp;&nbsp;&nbsp;Sync Commission Slab</label>
                                </div>
                            </div> -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><input type="checkbox" name="data[]" <?=(in_array('group_permission',$enabled_tables))?'checked="checked"':'';?> <?=$denied?> value="group_permission">&nbsp;&nbsp;&nbsp;&nbsp;<?= lang("group_permission"); ?></label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><input type="checkbox" name="data[]" <?=(in_array('sales',$enabled_tables))?'checked="checked"':'';?> <?=$denied?> value="sales">&nbsp;&nbsp;&nbsp;&nbsp;<?= lang("sales"); ?></label>
                                </div>
                            </div>
                          <!--   <div class="col-md-4">
                                <div class="form-group">
                                    <label><input type="checkbox" name="data[]" <?=(in_array('expenses',$enabled_tables))?'checked="checked"':'';?> <?=$denied?> value="expenses">&nbsp;&nbsp;&nbsp;&nbsp;Sync Expenses</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><input type="checkbox" name="data[]" <?=(in_array('daily_settlement',$enabled_tables))?'checked="checked"':'';?> <?=$denied?> value="daily_settlement">&nbsp;&nbsp;&nbsp;&nbsp;Sync Daily Settlement</label>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><input type="checkbox" name="data[]" <?=(in_array('purchase_order',$enabled_tables))?'checked="checked"':'';?> <?=$denied?> value="purchase_order">&nbsp;&nbsp;&nbsp;&nbsp;Sync Purchase Order</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><input type="checkbox" name="data[]" <?=(in_array('purchase_invoice',$enabled_tables))?'checked="checked"':'';?> <?=$denied?> value="purchase_invoice">&nbsp;&nbsp;&nbsp;&nbsp;Sync Purchase Invoice</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><input type="checkbox" name="data[]" <?=(in_array('purchase_return',$enabled_tables))?'checked="checked"':'';?> <?=$denied?> value="purchase_return">&nbsp;&nbsp;&nbsp;&nbsp;Sync Purchase Return</label>
                                </div>
                            </div> 
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><input type="checkbox" name="data[]" <?=(in_array('stock',$enabled_tables))?'checked="checked"':'';?> <?=$denied?> value="stock">&nbsp;&nbsp;&nbsp;&nbsp;Sync Stock</label>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><input type="checkbox" name="data[]" <?=(in_array('store_indent_request',$enabled_tables))?'checked="checked"':'';?> <?=$denied?> value="store_indent_request">&nbsp;&nbsp;&nbsp;&nbsp;Sync Store Indent Request</label>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><input type="checkbox" name="data[]" <?=(in_array('stock_request',$enabled_tables))?'checked="checked"':'';?> <?=$denied?> value="stock_request">&nbsp;&nbsp;&nbsp;&nbsp;Sync Stock Request</label>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><input type="checkbox" name="data[]" <?=(in_array('stock_transfer',$enabled_tables))?'checked="checked"':'';?> <?=$denied?> value="stock_transfer">&nbsp;&nbsp;&nbsp;&nbsp;Sync Stock Transfer</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><input type="checkbox" name="data[]" <?=(in_array('promotions',$enabled_tables))?'checked="checked"':'';?> <?=$denied?> value="promotions">&nbsp;&nbsp;&nbsp;&nbsp;Sync promotions</label>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><input type="checkbox" name="data[]" <?=(in_array('shift',$enabled_tables))?'checked="checked"':'';?> <?=$denied?> value="shift">&nbsp;&nbsp;&nbsp;&nbsp;Sync Shift</label>
                                </div>
                            </div>
                            
                             <div class="col-md-4">
                                <div class="form-group">
                                    <label><input type="checkbox" name="data[]" <?=(in_array('countries',$enabled_tables))?'checked="checked"':'';?> <?=$denied?> value="countries">&nbsp;&nbsp;&nbsp;&nbsp;Sync Countries</label>
                                </div>
                            </div>
                             
                              <div class="col-md-4">
                                <div class="form-group">
                                    <label><input type="checkbox" name="data[]" <?=(in_array('banks',$enabled_tables))?'checked="checked"':'';?> <?=$denied?> value="banks">&nbsp;&nbsp;&nbsp;&nbsp;Sync Banks</label>
                                </div>
                            </div>
                              
                              <div class="col-md-4">
                                <div class="form-group">
                                    <label><input type="checkbox" name="data[]" <?=(in_array('notifications',$enabled_tables))?'checked="checked"':'';?> <?=$denied?> value="notifications">&nbsp;&nbsp;&nbsp;&nbsp;Sync Notifications</label>
                                </div>
                            </div>
                              
                              <div class="col-md-4">
                                <div class="form-group">
                                    <label><input type="checkbox" name="data[]" <?=(in_array('loyalty',$enabled_tables))?'checked="checked"':'';?> <?=$denied?> value="loyalty">&nbsp;&nbsp;&nbsp;&nbsp;Sync Loyalty</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><input type="checkbox" name="data[]" <?=(in_array('device_detail',$enabled_tables))?'checked="checked"':'';?> <?=$denied?> value="device_detail">&nbsp;&nbsp;&nbsp;&nbsp;Sync Device Details</label>
                                </div>
                            </div>-->
                            
                    </fieldset>


                </div>
            </div>
            <div class="cleafix"></div>
            <div class="form-group">
                <div class="controls">
                    <?= form_submit('sync_tables', lang("Update"), 'class="btn btn-primary btn-lg"'); ?>
                </div>
            </div>
            <?= form_close(); ?>
            <?php// else : ?>
               <!-- <p class="introtext"><?= lang('No internet connection or center server might be down'); ?></p>-->
            <?php //endif; ?>
        </div>
    </div>

</div>
</div>
