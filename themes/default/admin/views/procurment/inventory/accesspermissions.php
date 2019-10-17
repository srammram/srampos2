<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<style>
    .table td:first-child {
        font-weight: bold;
    }

    label {
        margin-right: 10px;
    }
</style>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-folder-open"></i><?= lang('user_permissions'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?= lang("user_procurment_permissions"); ?></p>

                <?php if (!empty($user)) {
                  
                        echo admin_form_open("procurment/inventory/accesspermission/" . $id); ?>
                        <input type="hidden" name="user_id" id="user_id" value="<?=$user->id?>">
                        <input type="hidden" name="group_id" id="group_id" value="<?=$user->group_id?>">
                        <input type="hidden" name="access_id" id="access_id" value="1">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-striped reports-table">

                                <thead>
                                <tr>
                                    <th colspan="6"
                                        class="text-center"><?php echo $user->first_name . '  ' . $user->last_name . ' ( ' . $this->lang->line("procurment_permissions").')'; ?></th>
                                </tr>
                                <tr>
                                    <th rowspan="2" class="text-center"><?= lang("module_name"); ?>
                                    </th>
                                    <th colspan="5" class="text-center"><?= lang("permissions"); ?></th>
                                </tr>
                                
                                </thead>
                                <tbody>
                                           	
                                                <tr>
                                                	<td width="200"><?= lang('store'); ?></td>
                                                    <td  colspan="5">
                                                    	<span style="display:inline-block;">
                                                            <input type="checkbox" value="store" 
															<?php 
															if(!empty($m)){ 
																if(in_array('store', json_decode($m->modules))){ 
																	echo 'checked'; 
																}else{ 
																	echo ''; 
																} 
															}else{ echo ''; } ?> class="checkbox"   
                                                             name="modules[]"  >
                                                            <label  class="padding05"><?= lang('list'); ?></label>
                                                        </span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                	<td width="200"><?= lang('products'); ?></td>
                                                    <td  colspan="5">
                                                    	<span style="display:inline-block;">
                                                            <input type="checkbox" value="products" 
															<?php 
															if(!empty($m)){ 
																if(in_array('products', json_decode($m->modules))){ 
																	echo 'checked'; 
																}else{ 
																	echo ''; 
																} 
															}else{ echo ''; } ?> class="checkbox"   
                                                             name="modules[]"  >
                                                            <label  class="padding05"><?= lang('list'); ?></label>
                                                        </span>
                                                        <span style="display:inline-block;">
                                                            <input type="checkbox" value="products_add" 
															<?php 
															if(!empty($m)){ 
																if(in_array('products_add', json_decode($m->modules))){ 
																	echo 'checked'; 
																}else{ 
																	echo ''; 
																} 
															}else{ echo ''; } ?> class="checkbox"   
                                                             name="modules[]"  >
                                                            <label  class="padding05"><?= lang('add'); ?></label>
                                                        </span>
                                                        <span style="display:inline-block;">
                                                            <input type="checkbox" value="products_edit" 
															<?php 
															if(!empty($m)){ 
																if(in_array('products_edit', json_decode($m->modules))){ 
																	echo 'checked'; 
																}else{ 
																	echo ''; 
																} 
															}else{ echo ''; } ?> class="checkbox"   
                                                             name="modules[]"  >
                                                            <label  class="padding05"><?= lang('edit'); ?></label>
                                                        </span>
                                                        <span style="display:inline-block;">
                                                            <input type="checkbox" value="products_delete" 
															<?php 
															if(!empty($m)){ 
																if(in_array('products_delete', json_decode($m->modules))){ 
																	echo 'checked'; 
																}else{ 
																	echo ''; 
																} 
															}else{ echo ''; } ?> class="checkbox"   
                                                             name="modules[]"  >
                                                            <label  class="padding05"><?= lang('delete'); ?></label>
                                                        </span>
                                                        <span style="display:inline-block;">
                                                            <input type="checkbox" value="products_view" 
															<?php 
															if(!empty($m)){ 
																if(in_array('products_view', json_decode($m->modules))){ 
																	echo 'checked'; 
																}else{ 
																	echo ''; 
																} 
															}else{ echo ''; } ?> class="checkbox"   
                                                             name="modules[]"  >
                                                            <label  class="padding05"><?= lang('view'); ?></label>
                                                        </span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                	<td width="200"><?= lang('recipe'); ?></td>
                                                    <td  colspan="5">
                                                    	<span style="display:inline-block;">
                                                            <input type="checkbox" value="recipe" 
															<?php 
															if(!empty($m)){ 
																if(in_array('recipe', json_decode($m->modules))){ 
																	echo 'checked'; 
																}else{ 
																	echo ''; 
																} 
															}else{ echo ''; } ?> class="checkbox"   
                                                             name="modules[]"  >
                                                            <label  class="padding05"><?= lang('list'); ?></label>
                                                        </span>
                                                        <span style="display:inline-block;">
                                                            <input type="checkbox" value="recipe_add" 
															<?php 
															if(!empty($m)){ 
																if(in_array('recipe_add', json_decode($m->modules))){ 
																	echo 'checked'; 
																}else{ 
																	echo ''; 
																} 
															}else{ echo ''; } ?> class="checkbox"   
                                                             name="modules[]"  >
                                                            <label  class="padding05"><?= lang('add'); ?></label>
                                                        </span>
                                                        <span style="display:inline-block;">
                                                            <input type="checkbox" value="recipe_edit" 
															<?php 
															if(!empty($m)){ 
																if(in_array('recipe_edit', json_decode($m->modules))){ 
																	echo 'checked'; 
																}else{ 
																	echo ''; 
																} 
															}else{ echo ''; } ?> class="checkbox"   
                                                             name="modules[]"  >
                                                            <label  class="padding05"><?= lang('edit'); ?></label>
                                                        </span>
                                                        <span style="display:inline-block;">
                                                            <input type="checkbox" value="recipe_delete" 
															<?php 
															if(!empty($m)){ 
																if(in_array('recipe_delete', json_decode($m->modules))){ 
																	echo 'checked'; 
																}else{ 
																	echo ''; 
																} 
															}else{ echo ''; } ?> class="checkbox"   
                                                             name="modules[]"  >

                                                            <label  class="padding05"><?= lang('delete'); ?></label>
                                                        </span>
                                                        <span style="display:inline-block;">
                                                            <input type="checkbox" value="recipe_view" 
															<?php 
															if(!empty($m)){ 
																if(in_array('recipe_view', json_decode($m->modules))){ 
																	echo 'checked'; 
																}else{ 
																	echo ''; 
																} 
															}else{ echo ''; } ?> class="checkbox"   
                                                             name="modules[]"  >
                                                            <label  class="padding05"><?= lang('view'); ?></label>
                                                        </span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                	<td width="200"><?= lang('purchase_request'); ?></td>
                                                    <td  colspan="5">
                                                    	<span style="display:inline-block;">
                                                            <input type="checkbox" value="purchase_request" 
															<?php 
															if(!empty($m)){ 
																if(in_array('purchase_request', json_decode($m->modules))){ 
																	echo 'checked'; 
																}else{ 
																	echo ''; 
																} 
															}else{ echo ''; } ?> class="checkbox"   
                                                             name="modules[]"  >
                                                            <label  class="padding05"><?= lang('list'); ?></label>
                                                        </span>
                                                        <span style="display:inline-block;">
                                                            <input type="checkbox" value="purchase_request_add" 
															<?php 
															if(!empty($m)){ 
																if(in_array('purchase_request_add', json_decode($m->modules))){ 
																	echo 'checked'; 
																}else{ 
																	echo ''; 
																} 
															}else{ echo ''; } ?> class="checkbox"   
                                                             name="modules[]"  >
                                                            <label  class="padding05"><?= lang('add'); ?></label>
                                                        </span>
                                                        <span style="display:inline-block;">
                                                            <input type="checkbox" value="purchase_request_edit" 
															<?php 
															if(!empty($m)){ 
																if(in_array('purchase_request_edit', json_decode($m->modules))){ 
																	echo 'checked'; 
																}else{ 
																	echo ''; 
																} 
															}else{ echo ''; } ?> class="checkbox"   
                                                             name="modules[]"  >
                                                            <label  class="padding05"><?= lang('edit'); ?></label>
                                                        </span>
                                                        <span style="display:inline-block;">
                                                            <input type="checkbox" value="purchase_request_delete" 
															<?php 
															if(!empty($m)){ 
																if(in_array('purchase_request_delete', json_decode($m->modules))){ 
																	echo 'checked'; 
																}else{ 
																	echo ''; 
																} 
															}else{ echo ''; } ?> class="checkbox"   
                                                             name="modules[]"  >
                                                            <label  class="padding05"><?= lang('delete'); ?></label>
                                                        </span>
                                                        <span style="display:inline-block;">
                                                            <input type="checkbox" value="purchase_request_view" 
															<?php 
															if(!empty($m)){ 
																if(in_array('purchase_request_view', json_decode($m->modules))){ 
																	echo 'checked'; 
																}else{ 
																	echo ''; 
																} 
															}else{ echo ''; } ?> class="checkbox"   
                                                             name="modules[]"  >
                                                            <label  class="padding05"><?= lang('view'); ?></label>
                                                        </span>
                                                        <span style="display:inline-block;">
                                                            <input type="checkbox" value="purchase_request_approved" 
															<?php 
															if(!empty($m)){ 
																if(in_array('purchase_request_approved', json_decode($m->modules))){ 
																	echo 'checked'; 
																}else{ 
																	echo ''; 
																} 
															}else{ echo ''; } ?> class="checkbox"   
                                                             name="modules[]"  >
                                                            <label  class="padding05"><?= lang('approved'); ?></label>
                                                        </span>
                                                        <span style="display:inline-block;">
                                                            <input type="checkbox" value="purchase_request_cancel" 
															<?php 
															if(!empty($m)){ 
																if(in_array('purchase_request_cancel', json_decode($m->modules))){ 
																	echo 'checked'; 
																}else{ 
																	echo ''; 
																} 
															}else{ echo ''; } ?> class="checkbox"   
                                                             name="modules[]"  >
                                                            <label  class="padding05"><?= lang('cancel'); ?></label>
                                                        </span>
                                                        
                                                    </td>
                                                </tr>
                                                <tr>
                                                	<td width="200"><?= lang('purchase_quatation'); ?></td>
                                                    <td  colspan="5">
                                                    	<span style="display:inline-block;">
                                                            <input type="checkbox" value="purchase_quatation" 
															<?php 
															if(!empty($m)){ 
																if(in_array('purchase_quatation', json_decode($m->modules))){ 
																	echo 'checked'; 
																}else{ 
																	echo ''; 
																} 
															}else{ echo ''; } ?> class="checkbox"   
                                                             name="modules[]"  >
                                                            <label  class="padding05"><?= lang('list'); ?></label>
                                                        </span>
                                                        <span style="display:inline-block;">
                                                            <input type="checkbox" value="purchase_quatation_add" 
															<?php 
															if(!empty($m)){ 
																if(in_array('purchase_quatation_add', json_decode($m->modules))){ 
																	echo 'checked'; 
																}else{ 
																	echo ''; 
																} 
															}else{ echo ''; } ?> class="checkbox"   
                                                             name="modules[]"  >
                                                            <label  class="padding05"><?= lang('add'); ?></label>
                                                        </span>
                                                        <span style="display:inline-block;">
                                                            <input type="checkbox" value="purchase_quatation_edit" 
															<?php 
															if(!empty($m)){ 
																if(in_array('purchase_quatation_edit', json_decode($m->modules))){ 
																	echo 'checked'; 
																}else{ 
																	echo ''; 
																} 
															}else{ echo ''; } ?> class="checkbox"   
                                                             name="modules[]"  >
                                                            <label  class="padding05"><?= lang('edit'); ?></label>
                                                        </span>
                                                        <span style="display:inline-block;">
                                                            <input type="checkbox" value="purchase_quatation_delete" 
															<?php 
															if(!empty($m)){ 
																if(in_array('purchase_quatation_delete', json_decode($m->modules))){ 
																	echo 'checked'; 
																}else{ 
																	echo ''; 
																} 
															}else{ echo ''; } ?> class="checkbox"   
                                                             name="modules[]"  >
                                                            <label  class="padding05"><?= lang('delete'); ?></label>
                                                        </span>
                                                        <span style="display:inline-block;">
                                                            <input type="checkbox" value="purchase_quatation_view" 
															<?php 
															if(!empty($m)){ 
																if(in_array('purchase_quatation_view', json_decode($m->modules))){ 
																	echo 'checked'; 
																}else{ 

																	echo ''; 
																} 
															}else{ echo ''; } ?> class="checkbox"   
                                                             name="modules[]"  >
                                                            <label  class="padding05"><?= lang('view'); ?></label>
                                                        </span>
                                                        <span style="display:inline-block;">
                                                            <input type="checkbox" value="purchase_quatation_approved" 
															<?php 
															if(!empty($m)){ 
																if(in_array('purchase_quatation_approved', json_decode($m->modules))){ 
																	echo 'checked'; 
																}else{ 
																	echo ''; 
																} 
															}else{ echo ''; } ?> class="checkbox"   
                                                             name="modules[]"  >
                                                            <label  class="padding05"><?= lang('approved'); ?></label>
                                                        </span>
                                                        <span style="display:inline-block;">
                                                            <input type="checkbox" value="purchase_quatation_cancel" 
															<?php 
															if(!empty($m)){ 
																if(in_array('purchase_quatation_cancel', json_decode($m->modules))){ 
																	echo 'checked'; 
																}else{ 
																	echo ''; 
																} 
															}else{ echo ''; } ?> class="checkbox"   
                                                             name="modules[]"  >
                                                            <label  class="padding05"><?= lang('cancel'); ?></label>
                                                        </span>
                                                        
                                                    </td>
                                                </tr>
                                                <tr>
                                                	<td width="200"><?= lang('purchase_orders'); ?></td>
                                                    <td  colspan="5">
                                                    	<span style="display:inline-block;">
                                                            <input type="checkbox" value="purchase_orders" 
															<?php 
															if(!empty($m)){ 
																if(in_array('purchase_orders', json_decode($m->modules))){ 
																	echo 'checked'; 
																}else{ 
																	echo ''; 
																} 
															}else{ echo ''; } ?> class="checkbox"   
                                                             name="modules[]"  >
                                                            <label  class="padding05"><?= lang('list'); ?></label>
                                                        </span>
                                                        <span style="display:inline-block;">
                                                            <input type="checkbox" value="purchase_orders_add" 
															<?php 
															if(!empty($m)){ 
																if(in_array('purchase_orders_add', json_decode($m->modules))){ 
																	echo 'checked'; 
																}else{ 
																	echo ''; 
																} 
															}else{ echo ''; } ?> class="checkbox"   
                                                             name="modules[]"  >
                                                            <label  class="padding05"><?= lang('add'); ?></label>
                                                        </span>
                                                        <span style="display:inline-block;">
                                                            <input type="checkbox" value="purchase_orders_edit" 
															<?php 
															if(!empty($m)){ 
																if(in_array('purchase_orders_edit', json_decode($m->modules))){ 
																	echo 'checked'; 
																}else{ 
																	echo ''; 
																} 
															}else{ echo ''; } ?> class="checkbox"   
                                                             name="modules[]"  >
                                                            <label  class="padding05"><?= lang('edit'); ?></label>
                                                        </span>
                                                        <span style="display:inline-block;">
                                                            <input type="checkbox" value="purchase_orders_delete" 
															<?php 
															if(!empty($m)){ 
																if(in_array('purchase_orders_delete', json_decode($m->modules))){ 
																	echo 'checked'; 
																}else{ 
																	echo ''; 
																} 
															}else{ echo ''; } ?> class="checkbox"   
                                                             name="modules[]"  >
                                                            <label  class="padding05"><?= lang('delete'); ?></label>
                                                        </span>
                                                        <span style="display:inline-block;">
                                                            <input type="checkbox" value="purchase_orders_view" 
															<?php 
															if(!empty($m)){ 
																if(in_array('purchase_orders_view', json_decode($m->modules))){ 
																	echo 'checked'; 
																}else{ 
																	echo ''; 
																} 
															}else{ echo ''; } ?> class="checkbox"   
                                                             name="modules[]"  >
                                                            <label  class="padding05"><?= lang('view'); ?></label>
                                                        </span>
                                                        <span style="display:inline-block;">
                                                            <input type="checkbox" value="purchase_orders_approved" 
															<?php 
															if(!empty($m)){ 
																if(in_array('purchase_orders_approved', json_decode($m->modules))){ 
																	echo 'checked'; 
																}else{ 
																	echo ''; 
																} 
															}else{ echo ''; } ?> class="checkbox"   
                                                             name="modules[]"  >
                                                            <label  class="padding05"><?= lang('approved'); ?></label>
                                                        </span>
                                                        <span style="display:inline-block;">
                                                            <input type="checkbox" value="purchase_orders_cancel" 
															<?php 
															if(!empty($m)){ 
																if(in_array('purchase_orders_cancel', json_decode($m->modules))){ 
																	echo 'checked'; 
																}else{ 
																	echo ''; 
																} 
															}else{ echo ''; } ?> class="checkbox"   
                                                             name="modules[]"  >
                                                            <label  class="padding05"><?= lang('cancel'); ?></label>
                                                        </span>
                                                        
                                                    </td>
                                                </tr>
                                                <tr>
                                                	<td width="200"><?= lang('purchase_invoices'); ?></td>
                                                    <td  colspan="5">
                                                    	<span style="display:inline-block;">
                                                            <input type="checkbox" value="purchase_invoices" 
															<?php 
															if(!empty($m)){ 
																if(in_array('purchase_invoices', json_decode($m->modules))){ 
																	echo 'checked'; 
																}else{ 
																	echo ''; 
																} 
															}else{ echo ''; } ?> class="checkbox"   
                                                             name="modules[]"  >
                                                            <label  class="padding05"><?= lang('list'); ?></label>
                                                        </span>
                                                        <span style="display:inline-block;">
                                                            <input type="checkbox" value="purchase_invoices_add" 
															<?php 
															if(!empty($m)){ 
																if(in_array('purchase_invoices_add', json_decode($m->modules))){ 
																	echo 'checked'; 
																}else{ 
																	echo ''; 
																} 
															}else{ echo ''; } ?> class="checkbox"   
                                                             name="modules[]"  >
                                                            <label  class="padding05"><?= lang('add'); ?></label>
                                                        </span>
                                                        <span style="display:inline-block;">
                                                            <input type="checkbox" value="purchase_invoices_edit" 
															<?php 
															if(!empty($m)){ 
																if(in_array('purchase_invoices_edit', json_decode($m->modules))){ 
																	echo 'checked'; 
																}else{ 
																	echo ''; 
																} 
															}else{ echo ''; } ?> class="checkbox"   
                                                             name="modules[]"  >
                                                            <label  class="padding05"><?= lang('edit'); ?></label>
                                                        </span>
                                                        <span style="display:inline-block;">
                                                            <input type="checkbox" value="purchase_invoices_delete" 
															<?php 
															if(!empty($m)){ 
																if(in_array('purchase_invoices_delete', json_decode($m->modules))){ 
																	echo 'checked'; 
																}else{ 
																	echo ''; 
																} 
															}else{ echo ''; } ?> class="checkbox"   
                                                             name="modules[]"  >
                                                            <label  class="padding05"><?= lang('delete'); ?></label>
                                                        </span>
                                                        <span style="display:inline-block;">
                                                            <input type="checkbox" value="purchase_invoices_view" 
															<?php 
															if(!empty($m)){ 
																if(in_array('purchase_invoices_view', json_decode($m->modules))){ 
																	echo 'checked'; 
																}else{ 
																	echo ''; 
																} 
															}else{ echo ''; } ?> class="checkbox"   
                                                             name="modules[]"  >
                                                            <label  class="padding05"><?= lang('view'); ?></label>
                                                        </span>
                                                        <span style="display:inline-block;">
                                                            <input type="checkbox" value="purchase_invoices_approved" 
															<?php 
															if(!empty($m)){ 
																if(in_array('purchase_invoices_approved', json_decode($m->modules))){ 
																	echo 'checked'; 
																}else{ 
																	echo ''; 
																} 
															}else{ echo ''; } ?> class="checkbox"   
                                                             name="modules[]"  >
                                                            <label  class="padding05"><?= lang('approved'); ?></label>
                                                        </span>
                                                        <span style="display:inline-block;">
                                                            <input type="checkbox" value="purchase_invoices_cancel" 
															<?php 
															if(!empty($m)){ 
																if(in_array('purchase_invoices_cancel', json_decode($m->modules))){ 
																	echo 'checked'; 
																}else{ 
																	echo ''; 
																} 
															}else{ echo ''; } ?> class="checkbox"   
                                                             name="modules[]"  >
                                                            <label  class="padding05"><?= lang('cancel'); ?></label>
                                                        </span>
                                                        
                                                    </td>
                                                </tr>
                                                <tr>
                                                	<td width="200"><?= lang('purchase_return'); ?></td>
                                                    <td  colspan="5">
                                                    	<span style="display:inline-block;">
                                                            <input type="checkbox" value="purchase_return" 
															<?php 
															if(!empty($m)){ 
																if(in_array('purchase_return', json_decode($m->modules))){ 
																	echo 'checked'; 
																}else{ 
																	echo ''; 
																} 
															}else{ echo ''; } ?> class="checkbox"   
                                                             name="modules[]"  >
                                                            <label  class="padding05"><?= lang('list'); ?></label>
                                                        </span>
                                                        <span style="display:inline-block;">
                                                            <input type="checkbox" value="purchase_return_add" 
															<?php 
															if(!empty($m)){ 
																if(in_array('purchase_return_add', json_decode($m->modules))){ 
																	echo 'checked'; 
																}else{ 
																	echo ''; 
																} 
															}else{ echo ''; } ?> class="checkbox"   
                                                             name="modules[]"  >
                                                            <label  class="padding05"><?= lang('add'); ?></label>
                                                        </span>
                                                        <span style="display:inline-block;">
                                                            <input type="checkbox" value="purchase_return_edit" 
															<?php 
															if(!empty($m)){ 
																if(in_array('purchase_return_edit', json_decode($m->modules))){ 
																	echo 'checked'; 
																}else{ 
																	echo ''; 
																} 
															}else{ echo ''; } ?> class="checkbox"   
                                                             name="modules[]"  >
                                                            <label  class="padding05"><?= lang('edit'); ?></label>
                                                        </span>
                                                        <span style="display:inline-block;">
                                                            <input type="checkbox" value="purchase_return_delete" 
															<?php 
															if(!empty($m)){ 
																if(in_array('purchase_return_delete', json_decode($m->modules))){ 
																	echo 'checked'; 
																}else{ 
																	echo ''; 
																} 
															}else{ echo ''; } ?> class="checkbox"   
                                                             name="modules[]"  >
                                                            <label  class="padding05"><?= lang('delete'); ?></label>
                                                        </span>
                                                        <span style="display:inline-block;">
                                                            <input type="checkbox" value="purchase_return_view" 
															<?php 
															if(!empty($m)){ 
																if(in_array('purchase_return_view', json_decode($m->modules))){ 
																	echo 'checked'; 
																}else{ 
																	echo ''; 
																} 
															}else{ echo ''; } ?> class="checkbox"   
                                                             name="modules[]"  >
                                                            <label  class="padding05"><?= lang('view'); ?></label>
                                                        </span>
                                                        <span style="display:inline-block;">
                                                            <input type="checkbox" value="purchase_return_approved" 
															<?php 
															if(!empty($m)){ 
																if(in_array('purchase_return_approved', json_decode($m->modules))){ 
																	echo 'checked'; 
																}else{ 
																	echo ''; 
																} 
															}else{ echo ''; } ?> class="checkbox"   
                                                             name="modules[]"  >
                                                            <label  class="padding05"><?= lang('approved'); ?></label>
                                                        </span>
                                                        <span style="display:inline-block;">
                                                            <input type="checkbox" value="purchase_return_cancel" 
															<?php 
															if(!empty($m)){ 
																if(in_array('purchase_return_cancel', json_decode($m->modules))){ 
																	echo 'checked'; 
																}else{ 
																	echo ''; 
																} 
															}else{ echo ''; } ?> class="checkbox"   
                                                             name="modules[]"  >
                                                            <label  class="padding05"><?= lang('cancel'); ?></label>
                                                        </span>
                                                        
                                                    </td>
                                                </tr>
                                                <tr>
                                                	<td width="200"><?= lang('store_request'); ?></td>
                                                    <td  colspan="5">
                                                    	<span style="display:inline-block;">
                                                            <input type="checkbox" value="store_request" 
															<?php 
															if(!empty($m)){ 
																if(in_array('store_request', json_decode($m->modules))){ 
																	echo 'checked'; 
																}else{ 
																	echo ''; 
																} 
															}else{ echo ''; } ?> class="checkbox"   
                                                             name="modules[]"  >
                                                            <label  class="padding05"><?= lang('list'); ?></label>
                                                        </span>
                                                        <span style="display:inline-block;">
                                                            <input type="checkbox" value="store_request_add" 
															<?php 
															if(!empty($m)){ 
																if(in_array('store_request_add', json_decode($m->modules))){ 
																	echo 'checked'; 
																}else{ 
																	echo ''; 
																} 
															}else{ echo ''; } ?> class="checkbox"   
                                                             name="modules[]"  >
                                                            <label  class="padding05"><?= lang('add'); ?></label>
                                                        </span>
                                                        <span style="display:inline-block;">
                                                            <input type="checkbox" value="store_request_edit" 
															<?php 
															if(!empty($m)){ 
																if(in_array('store_request_edit', json_decode($m->modules))){ 
																	echo 'checked'; 
																}else{ 
																	echo ''; 
																} 
															}else{ echo ''; } ?> class="checkbox"   
                                                             name="modules[]"  >
                                                            <label  class="padding05"><?= lang('edit'); ?></label>
                                                        </span>
                                                        <span style="display:inline-block;">
                                                            <input type="checkbox" value="store_request_delete" 
															<?php 
															if(!empty($m)){ 
																if(in_array('store_request_delete', json_decode($m->modules))){ 
																	echo 'checked'; 
																}else{ 
																	echo ''; 
																} 
															}else{ echo ''; } ?> class="checkbox"   
                                                             name="modules[]"  >
                                                            <label  class="padding05"><?= lang('delete'); ?></label>
                                                        </span>
                                                        <span style="display:inline-block;">
                                                            <input type="checkbox" value="store_request_view" 
															<?php 
															if(!empty($m)){ 
																if(in_array('store_request_view', json_decode($m->modules))){ 
																	echo 'checked'; 
																}else{ 
																	echo ''; 
																} 
															}else{ echo ''; } ?> class="checkbox"   
                                                             name="modules[]"  >
                                                            <label  class="padding05"><?= lang('view'); ?></label>
                                                        </span>
                                                        <span style="display:inline-block;">
                                                            <input type="checkbox" value="store_request_approved" 
															<?php 
															if(!empty($m)){ 
																if(in_array('store_request_approved', json_decode($m->modules))){ 
																	echo 'checked'; 
																}else{ 
																	echo ''; 
																} 
															}else{ echo ''; } ?> class="checkbox"   
                                                             name="modules[]"  >
                                                            <label  class="padding05"><?= lang('approved'); ?></label>
                                                        </span>
                                                        <span style="display:inline-block;">
                                                            <input type="checkbox" value="store_request_cancel" 
															<?php 
															if(!empty($m)){ 
																if(in_array('store_request_cancel', json_decode($m->modules))){ 
																	echo 'checked'; 
																}else{ 
																	echo ''; 
																} 
															}else{ echo ''; } ?> class="checkbox"   
                                                             name="modules[]"  >
                                                            <label  class="padding05"><?= lang('cancel'); ?></label>
                                                        </span>
                                                        
                                                    </td>
                                                </tr>
                                                <tr>
                                                	<td width="200"><?= lang('store_transfer'); ?></td>
                                                    <td  colspan="5">
                                                    	<span style="display:inline-block;">
                                                            <input type="checkbox" value="store_transfer" 
															<?php 
															if(!empty($m)){ 
																if(in_array('store_transfer', json_decode($m->modules))){ 
																	echo 'checked'; 
																}else{ 
																	echo ''; 
																} 
															}else{ echo ''; } ?> class="checkbox"   
                                                             name="modules[]"  >
                                                            <label  class="padding05"><?= lang('list'); ?></label>
                                                        </span>
                                                        <span style="display:inline-block;">
                                                            <input type="checkbox" value="store_transfer_add" 
															<?php 
															if(!empty($m)){ 
																if(in_array('store_transfer_add', json_decode($m->modules))){ 
																	echo 'checked'; 
																}else{ 
																	echo ''; 
																} 
															}else{ echo ''; } ?> class="checkbox"   
                                                             name="modules[]"  >
                                                            <label  class="padding05"><?= lang('add'); ?></label>
                                                        </span>
                                                        <span style="display:inline-block;">
                                                            <input type="checkbox" value="store_transfer_edit" 
															<?php 
															if(!empty($m)){ 
																if(in_array('store_transfer_edit', json_decode($m->modules))){ 
																	echo 'checked'; 
																}else{ 
																	echo ''; 
																} 
															}else{ echo ''; } ?> class="checkbox"   
                                                             name="modules[]"  >
                                                            <label  class="padding05"><?= lang('edit'); ?></label>
                                                        </span>
                                                        <span style="display:inline-block;">
                                                            <input type="checkbox" value="store_transfer_delete" 
															<?php 
															if(!empty($m)){ 
																if(in_array('store_transfer_delete', json_decode($m->modules))){ 
																	echo 'checked'; 
																}else{ 
																	echo ''; 
																} 
															}else{ echo ''; } ?> class="checkbox"   
                                                             name="modules[]"  >
                                                            <label  class="padding05"><?= lang('delete'); ?></label>
                                                        </span>
                                                        <span style="display:inline-block;">
                                                            <input type="checkbox" value="store_transfer_view" 
															<?php 
															if(!empty($m)){ 
																if(in_array('store_transfer_view', json_decode($m->modules))){ 
																	echo 'checked'; 
																}else{ 
																	echo ''; 
																} 
															}else{ echo ''; } ?> class="checkbox"   
                                                             name="modules[]"  >
                                                            <label  class="padding05"><?= lang('view'); ?></label>
                                                        </span>
                                                        <span style="display:inline-block;">
                                                            <input type="checkbox" value="store_transfer_approved" 
															<?php 
															if(!empty($m)){ 
																if(in_array('store_transfer_approved', json_decode($m->modules))){ 
																	echo 'checked'; 
																}else{ 
																	echo ''; 
																} 
															}else{ echo ''; } ?> class="checkbox"   
                                                             name="modules[]"  >
                                                            <label  class="padding05"><?= lang('approved'); ?></label>
                                                        </span>
                                                        <span style="display:inline-block;">
                                                            <input type="checkbox" value="store_transfer_cancel" 
															<?php 
															if(!empty($m)){ 
																if(in_array('store_transfer_cancel', json_decode($m->modules))){ 
																	echo 'checked'; 
																}else{ 
																	echo ''; 
																} 
															}else{ echo ''; } ?> class="checkbox"   
                                                             name="modules[]"  >
                                                            <label  class="padding05"><?= lang('cancel'); ?></label>
                                                        </span>
                                                        
                                                    </td>
                                                </tr>
                                                <tr>
                                                	<td width="200"><?= lang('store_received'); ?></td>
                                                    <td  colspan="5">
                                                    	<span style="display:inline-block;">
                                                            <input type="checkbox" value="store_received" 
															<?php 
															if(!empty($m)){ 
																if(in_array('store_received', json_decode($m->modules))){ 
																	echo 'checked'; 
																}else{ 
																	echo ''; 
																} 
															}else{ echo ''; } ?> class="checkbox"   
                                                             name="modules[]"  >
                                                            <label  class="padding05"><?= lang('list'); ?></label>
                                                        </span>
                                                        <span style="display:inline-block;">
                                                            <input type="checkbox" value="store_received_add" 
															<?php 
															if(!empty($m)){ 
																if(in_array('store_received_add', json_decode($m->modules))){ 
																	echo 'checked'; 
																}else{ 
																	echo ''; 
																} 
															}else{ echo ''; } ?> class="checkbox"   
                                                             name="modules[]"  >
                                                            <label  class="padding05"><?= lang('add'); ?></label>
                                                        </span>
                                                        <span style="display:inline-block;">
                                                            <input type="checkbox" value="store_received_edit" 
															<?php 
															if(!empty($m)){ 
																if(in_array('store_received_edit', json_decode($m->modules))){ 
																	echo 'checked'; 
																}else{ 
																	echo ''; 
																} 
															}else{ echo ''; } ?> class="checkbox"   
                                                             name="modules[]"  >
                                                            <label  class="padding05"><?= lang('edit'); ?></label>
                                                        </span>
                                                        <span style="display:inline-block;">
                                                            <input type="checkbox" value="store_received_delete" 
															<?php 
															if(!empty($m)){ 
																if(in_array('store_received_delete', json_decode($m->modules))){ 
																	echo 'checked'; 
																}else{ 
																	echo ''; 
																} 
															}else{ echo ''; } ?> class="checkbox"   
                                                             name="modules[]"  >
                                                            <label  class="padding05"><?= lang('delete'); ?></label>
                                                        </span>
                                                        <span style="display:inline-block;">
                                                            <input type="checkbox" value="store_received_view" 
															<?php 
															if(!empty($m)){ 
																if(in_array('store_received_view', json_decode($m->modules))){ 
																	echo 'checked'; 
																}else{ 
																	echo ''; 
																} 
															}else{ echo ''; } ?> class="checkbox"   
                                                             name="modules[]"  >
                                                            <label  class="padding05"><?= lang('view'); ?></label>
                                                        </span>
                                                        <span style="display:inline-block;">
                                                            <input type="checkbox" value="store_received_approved" 
															<?php 
															if(!empty($m)){ 
																if(in_array('store_received_approved', json_decode($m->modules))){ 
																	echo 'checked'; 
																}else{ 
																	echo ''; 
																} 
															}else{ echo ''; } ?> class="checkbox"   
                                                             name="modules[]"  >
                                                            <label  class="padding05"><?= lang('approved'); ?></label>
                                                        </span>
                                                        <span style="display:inline-block;">
                                                            <input type="checkbox" value="store_received_cancel" 
															<?php 
															if(!empty($m)){ 
																if(in_array('store_received_cancel', json_decode($m->modules))){ 
																	echo 'checked'; 
																}else{ 
																	echo ''; 
																} 
															}else{ echo ''; } ?> class="checkbox"   
                                                             name="modules[]"  >
                                                            <label  class="padding05"><?= lang('cancel'); ?></label>
                                                        </span>
                                                        
                                                    </td>
                                                </tr>
                                                <tr>
                                                	<td width="200"><?= lang('store_return'); ?></td>
                                                    <td  colspan="5">
                                                    	<span style="display:inline-block;">
                                                            <input type="checkbox" value="store_return" 
															<?php 
															if(!empty($m)){ 
																if(in_array('store_return', json_decode($m->modules))){ 
																	echo 'checked'; 
																}else{ 
																	echo ''; 
																} 
															}else{ echo ''; } ?> class="checkbox"   
                                                             name="modules[]"  >
                                                            <label  class="padding05"><?= lang('list'); ?></label>
                                                        </span>
                                                        <span style="display:inline-block;">
                                                            <input type="checkbox" value="store_return_add" 
															<?php 
															if(!empty($m)){ 
																if(in_array('store_return_add', json_decode($m->modules))){ 
																	echo 'checked'; 
																}else{ 
																	echo ''; 
																} 
															}else{ echo ''; } ?> class="checkbox"   
                                                             name="modules[]"  >
                                                            <label  class="padding05"><?= lang('add'); ?></label>
                                                        </span>
                                                        <span style="display:inline-block;">
                                                            <input type="checkbox" value="store_return_edit" 
															<?php 
															if(!empty($m)){ 
																if(in_array('store_return_edit', json_decode($m->modules))){ 
																	echo 'checked'; 
																}else{ 
																	echo ''; 
																} 
															}else{ echo ''; } ?> class="checkbox"   
                                                             name="modules[]"  >
                                                            <label  class="padding05"><?= lang('edit'); ?></label>
                                                        </span>
                                                        <span style="display:inline-block;">
                                                            <input type="checkbox" value="store_return_delete" 
															<?php 
															if(!empty($m)){ 
																if(in_array('store_return_delete', json_decode($m->modules))){ 
																	echo 'checked'; 
																}else{ 
																	echo ''; 
																} 
															}else{ echo ''; } ?> class="checkbox"   
                                                             name="modules[]"  >
                                                            <label  class="padding05"><?= lang('delete'); ?></label>
                                                        </span>
                                                        <span style="display:inline-block;">
                                                            <input type="checkbox" value="store_return_view" 
															<?php 
															if(!empty($m)){ 
																if(in_array('store_return_view', json_decode($m->modules))){ 
																	echo 'checked'; 
																}else{ 
																	echo ''; 
																} 
															}else{ echo ''; } ?> class="checkbox"   
                                                             name="modules[]"  >
                                                            <label  class="padding05"><?= lang('view'); ?></label>
                                                        </span>
                                                        <span style="display:inline-block;">
                                                            <input type="checkbox" value="store_return_approved" 
															<?php 
															if(!empty($m)){ 
																if(in_array('store_return_approved', json_decode($m->modules))){ 
																	echo 'checked'; 
																}else{ 
																	echo ''; 
																} 
															}else{ echo ''; } ?> class="checkbox"   
                                                             name="modules[]"  >
                                                            <label  class="padding05"><?= lang('approved'); ?></label>
                                                        </span>
                                                        <span style="display:inline-block;">
                                                            <input type="checkbox" value="store_return_cancel" 
															<?php 
															if(!empty($m)){ 
																if(in_array('store_return_cancel', json_decode($m->modules))){ 
																	echo 'checked'; 
																}else{ 
																	echo ''; 
																} 
															}else{ echo ''; } ?> class="checkbox"   
                                                             name="modules[]"  >
                                                            <label  class="padding05"><?= lang('cancel'); ?></label>
                                                        </span>
                                                        
                                                    </td>
                                                </tr>
                                                <tr>
                                                	<td width="200"><?= lang('store_return_received'); ?></td>
                                                    <td  colspan="5">
                                                    	<span style="display:inline-block;">
                                                            <input type="checkbox" value="store_return_received" 
															<?php 
															if(!empty($m)){ 
																if(in_array('store_return_received', json_decode($m->modules))){ 
																	echo 'checked'; 
																}else{ 
																	echo ''; 
																} 
															}else{ echo ''; } ?> class="checkbox"   
                                                             name="modules[]"  >
                                                            <label  class="padding05"><?= lang('list'); ?></label>
                                                        </span>
                                                        <span style="display:inline-block;">
                                                            <input type="checkbox" value="store_return_received_add" 
															<?php 
															if(!empty($m)){ 
																if(in_array('store_return_received_add', json_decode($m->modules))){ 
																	echo 'checked'; 
																}else{ 
																	echo ''; 
																} 
															}else{ echo ''; } ?> class="checkbox"   
                                                             name="modules[]"  >
                                                            <label  class="padding05"><?= lang('add'); ?></label>
                                                        </span>
                                                        <span style="display:inline-block;">
                                                            <input type="checkbox" value="store_return_received_edit" 
															<?php 
															if(!empty($m)){ 
																if(in_array('store_return_received_edit', json_decode($m->modules))){ 
																	echo 'checked'; 
																}else{ 
																	echo ''; 
																} 
															}else{ echo ''; } ?> class="checkbox"   
                                                             name="modules[]"  >
                                                            <label  class="padding05"><?= lang('edit'); ?></label>
                                                        </span>
                                                        <span style="display:inline-block;">
                                                            <input type="checkbox" value="store_return_received_delete" 
															<?php 
															if(!empty($m)){ 
																if(in_array('store_return_received_delete', json_decode($m->modules))){ 
																	echo 'checked'; 
																}else{ 
																	echo ''; 
																} 
															}else{ echo ''; } ?> class="checkbox"   
                                                             name="modules[]"  >
                                                            <label  class="padding05"><?= lang('delete'); ?></label>
                                                        </span>
                                                        <span style="display:inline-block;">
                                                            <input type="checkbox" value="store_return_received_view" 
															<?php 
															if(!empty($m)){ 
																if(in_array('store_return_received_view', json_decode($m->modules))){ 
																	echo 'checked'; 
																}else{ 
																	echo ''; 
																} 
															}else{ echo ''; } ?> class="checkbox"   
                                                             name="modules[]"  >
                                                            <label  class="padding05"><?= lang('view'); ?></label>
                                                        </span>
                                                        <span style="display:inline-block;">
                                                            <input type="checkbox" value="store_return_received_approved" 
															<?php 
															if(!empty($m)){ 
																if(in_array('store_return_received_approved', json_decode($m->modules))){ 
																	echo 'checked'; 
																}else{ 
																	echo ''; 
																} 
															}else{ echo ''; } ?> class="checkbox"   
                                                             name="modules[]"  >
                                                            <label  class="padding05"><?= lang('approved'); ?></label>
                                                        </span>
                                                        <span style="display:inline-block;">
                                                            <input type="checkbox" value="store_return_received_cancel" 
															<?php 
															if(!empty($m)){ 
																if(in_array('store_return_received_cancel', json_decode($m->modules))){ 
																	echo 'checked'; 
																}else{ 
																	echo ''; 
																} 
															}else{ echo ''; } ?> class="checkbox"   
                                                             name="modules[]"  >
                                                            <label  class="padding05"><?= lang('cancel'); ?></label>
                                                        </span>
                                                        
                                                    </td>
                                                </tr>
                                            </tbody>
                            </table>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary"><?=lang('update')?></button>
                        </div>
                        <?php echo form_close();
                   
                } else {
                    echo $this->lang->line("user_x_allowed");
                } ?>


            </div>
        </div>
    </div>
</div>
