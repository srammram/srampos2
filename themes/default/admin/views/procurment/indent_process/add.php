<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<style>
    .a_stock,.t_stock,.qty{
	width:80px;
	margin: 2px;
    }
    .a_stock,.t_stock{
	margin: 2px;
	/*position: absolute;*/
    }
    .stock-store-name{
    width: 100px;}
</style>

<div class="box">
    <div class="box-header procurment-header">
        <h2 class=""><?= lang('indent_processing'); ?></h2>        
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12" >                
                <?php
                $attrib = array('data-toggle' => 'validator1', 'role' => 'form','id' => 'indent-processing-form');
                echo admin_form_open_multipart("procurment/indent_process/add", $attrib)
                ?>                
                <div class="row">

                    <div class="col-lg-12" style="background:#b1d7fd; padding:15px 15px;">
                        <?php //echo form_submit('add_store_request', $this->lang->line("save"), 'id="add_store_request" class="btn col-lg-1 btn-sm btn-primary pull-right" '); ?>
			<input type="button" name="add_store_request" value="Save" id="add_store_request" class="process-indent btn col-lg-1 btn-sm btn-primary pull-right" autocomplete="off">
                        <button type="button" class="btn col-lg-1 btn-sm btn-danger pull-right" id="reset" style="margin-right:15px;height:30px!important;font-size: 12px!important"><?= lang('reset'); ?></button>
						<input type="hidden" name="warehouse" id="store_reqwarehouse" 
                        value="<?php echo $Settings->default_warehouse ?>">
                        <input type="hidden" name="biller" id="reqbiller" value="<?php echo $Settings->default_biller ?>"> 
                    
                     <table class="table custom_tables">
                            <tbody>
                                <tr>
                                    <td>
                                        <?= lang("date", "date") ?>
                                    </td>
                                    <td>
                                        <input type="datetime" name="date" id="store_reqdate" readonly class="form-control" value="<?php echo date('Y-m-d H:i:s') ?>">
                                     <input type="hidden" name="request_type" id="store_reqtype" value="<?php echo 'new' ?>">
                                    </td>                                              
                       
                                    <td width="150px">
                                        <?= lang("from_store", "store_reqfrom_store_id"); ?>
                                    </td>
                                    <td>
                                        <?php
                                            $tst[''] = '';
                                           foreach ($all_stores as $store) {
					                        	if($store->id==$this->store_id){ continue;}
                                               $tst[$store->id] = $store->name;
                                           }
										
                                           echo form_dropdown('from_store_id', $tst, (isset($_POST['from_store_id']) ? $_POST['from_store_id'] : ''), 'id="from_store_id" class="form-control input-tip select from_store_id" data-placeholder="' . lang("select") . ' ' . lang("to_store") . '"   style="width:100%;" ');
                                        ?>
                                    </td>
				                        <td width="150px">
                                        <?= lang("Select_indent_no", "Select_indent_no"); ?>
                                    </td>
				                     <td>
                                        <select name="indent_id" id="select-indent" class="indent-request-dropdown">
					                        <option value=""><?=lang('select_indent')?></option>
					                      </select>
                                    </td>
                                </tr>
                                <tr>
									<td width="150px">
                                        <?= lang("indent_date", "indent_date"); ?>
                                    </td>
                                   <td>
                                     <input type="text" name="indent_date" id="indent_date" readonly class="form-control" value="">
                                    </td> 
                                    <td>
                                        <?= lang("document", "document") ?>                                        
                                    </td>
                                    <td>
                                        <input id="document" type="file" data-browse-label="" name="document" 
                                        data-show-upload="false" data-show-preview="false" class="form-control file">
                                    </td>
                                    
                                    
                                     <td>
                                        <?= lang("Remarks/Note", "note") ?>
                                    </td>
                                    <td>
                                        <input  name="note" id="reqnote" class="form-control" >
                                    </td>  
                                </tr>
				
				<tr>
				    <td width="150px">
                                        <?= lang("processing_from", "processing_from"); ?>
                                    </td>
				    <td colspan=5>
					<select name="processing_from[]" id="processing_from"  class="processing_from form-control input-tip " style="width:100%;" placeholder="select-stores">
					<option value="">Select Store</option>
					    <?php foreach ($all_stores as $store) : ?>
					    <option value="<?=$store->id?>"><?=$store->name?></option>
					    <?php endforeach; ?>
					    
					</select>
                                    
				    </td>
				    <td><button  type="button"  class="btn btn-primary" id="load-stock"><?=lang('load_stock')?></button></td>
				</tr>
                            </tbody>
                        </table>
		     
                        
                     </div> 

                        <div class="clearfix"></div>
                        <!-- <div class="col-md-12" id="sticker">
                            <div class="well well-sm">
                              
                                <div class="form-group" style="margin-bottom:0;">
                                    <div class="input-group wide-tip">
                                        <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;">
                                            <i class="fa fa-2x fa-barcode addIcon"></i></div>
                                        <?php echo form_input('add_item', '', 'class="form-control input-lg" id="add_item" placeholder="' . $this->lang->line("Search Purchase Items") . '"'); ?>
                                        <?php if ($Owner || $Admin || $GP['products-add']) { ?>
                                        <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;">
                                            <a href="<?= admin_url('procurment/products/add') ?>" id="addManually1"><i
                                                    class="fa fa-2x fa-plus addIcon" id="addIcon"></i></a></div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>-->

                       <!--  <div class="col-md-12" id="sticker">
                            <div class="well well-sm">
                                <div class="form-group" style="margin-bottom:0;">
                                    <div class="input-group wide-tip">
                                        <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;">
                                            <i class="fa fa-2x fa-barcode addIcon"></i></div>
                                        <?php echo form_input('add_item', '', 'class="form-control input-lg" id="add_item" placeholder="' . $this->lang->line("add_product_to_order") . '"'); ?>
                                        <?php if ($Owner || $Admin || $GP['products-add']) { ?>
                                        <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;">
                                            <a href="javascript:void(0)" id="addManually" class="tip"
                                               title="<?= lang('add_product_manually') ?>"><i
                                                    class="fa fa-2x fa-plus-circle addIcon" id="addIcon"></i></a></div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div> -->
                        <div class="clearfix"></div>
                        <div class="col-md-12">
                            <div class="control-group table-group">
                                <label class="table-label"><?= lang("items"); ?> *</label>

                                <div class="controls table-controls">
                                    <table id="store_reqTable"
                                            class="table items  table-bordered table-condensed sortable_table" style="background:#fff">
                                        <thead>
                                        <tr>
					    <th><?= lang('s_no');?></th>
					    <th><?= lang('code');?></th>  
                                            <th><?= lang('product');?></th>                                            
                                            <th><?= lang("Request_Quantity"); ?></th>
					    <th><?= lang("Assign_store"); ?></th>
                                            <th style="text-align:center !important"><i
                                                    class="fa fa-trash-o"
                                                    style="opacity:0.5; filter:alpha(opacity=50);"></i></th>
                                        </tr>
                                        </thead>
                                        <tbody></tbody>
                                        <tfoot></tfoot>
                                    </table>
				    <table class="table total_item_qty_tables" style="padding: 4px;border-top: none!important;width:30%;">
				    <tbody>
					<tr>                                    
					    <td>
						<label for="total_no_items"><?=lang('total_no_items')?></label>                                    </td>
					    <td>
						<input name="total_no_items" id="total_no_items" readonly class="form-control">
					    </td>
					</tr>
					<tr>                                    
					    <td>
						<label for="total_no_qty"><?=lang('total_no_qty')?></label>                                    </td>
					    <td>
						<input name="total_no_qty" id="total_no_qty" readonly class="form-control">
					    </td>
					</tr>
				    </tbody>
				    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
               
               <div class="col-lg-12" style="background:#a6f7a1; margin-top:15px;">
                    <table class="table custom_tables" style="table-layout:fixed">
                            <tbody>
                                <tr>                                    
                                    <td>
                                        <?= lang("logged_by", "logged_by") ?>
                                    </td>
                                    <td>
                                        <input  name="logged_by" id="logged_by" value="<?php echo $this->session->userdata('username'); ?>" readonly tabindex=-1 class="form-control">
                                    </td>
                                     <td>                                    
                                    </td>
                                    <td>                                    
                                    </td>                                    
                                    <td>
                                        <?= lang("till/counter_name", "counter_name") ?>
                                    </td>
                                    <td>
                                        <input  name="counter_name" id="counter_name" value="" class="form-control" >
                                    </td>                                   
                                </tr>
                            </tbody>
                        </table>
                    </div>

                <?php echo form_close(); ?>

            </div>

        </div>
    </div>
</div>

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
                    <?php if ($Settings->tax1) { ?>
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><?= lang('product_tax') ?></label>
                            <div class="col-sm-8">
                                <!-- <?php
                                $tr[""] = "";
                                foreach ($tax_rates as $tax) {
                                    $tr[$tax->id] = $tax->name;
                                }
                                echo form_dropdown('ptax', $tr, "", 'id="ptax" class="form-control pos-input-tip" style="width:100%;"');
                                ?> -->
                            </div>
                        </div>
                    <?php } ?>
                    <div class="form-group">
                        <label for="pquantity" class="col-sm-4 control-label"><?= lang('quantity') ?></label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="pquantity">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="punit" class="col-sm-4 control-label"><?= lang('product_unit') ?></label>
                        <div class="col-sm-8">
                            <div id="punits-div"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="poption" class="col-sm-4 control-label"><?= lang('product_option') ?></label>
                        <div class="col-sm-8">
                            <div id="poptions-div"></div>
                        </div>
                    </div>
                    <?php if ($Settings->product_discount && ($Owner || $Admin || $this->session->userdata('allow_discount'))) { ?>
                        <div class="form-group">
                            <label for="pdiscount" class="col-sm-4 control-label"><?= lang('product_discount') ?></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="pdiscount">
                            </div>
                        </div>
                    <?php } ?>
                    <div class="form-group">
                        <label for="pprice" class="col-sm-4 control-label"><?= lang('unit_price') ?></label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="pprice" <?= ($Owner || $Admin || $GP['edit_price']) ? '' : 'readonly'; ?>>
                        </div>
                    </div>
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th style="width:25%;"><?= lang('net_unit_price'); ?></th>
                            <th style="width:25%;"><span id="net_price"></span></th>
                            <th style="width:25%;"><?= lang('product_tax'); ?></th>
                            <th style="width:25%;"><span id="pro_tax"></span></th>
                        </tr>
                    </table>
                    <input type="hidden" id="punit_price" value=""/>
                    <input type="hidden" id="old_tax" value=""/>
                    <input type="hidden" id="old_qty" value=""/>
                    <input type="hidden" id="old_price" value=""/>
                    <input type="hidden" id="row_id" value=""/>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="editItem"><?= lang('submit') ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="mModal" tabindex="-1" role="dialog" aria-labelledby="mModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true"><i
                            class="fa fa-2x">&times;</i></span><span class="sr-only"><?=lang('close');?></span></button>
                <h4 class="modal-title" id="mModalLabel"><?= lang('add_product_manually') ?></h4>
            </div>
            <div class="modal-body" id="pr_popover_content">
                <form class="form-horizontal" role="form">
                    <div class="form-group">
                        <label for="mcode" class="col-sm-4 control-label"><?= lang('product_code') ?> *</label>

                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="mcode">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="mname" class="col-sm-4 control-label"><?= lang('product_name') ?> *</label>

                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="mname">
                        </div>
                    </div>
                    <?php if ($Settings->tax1) { ?>
                        <div class="form-group">
                            <label for="mtax" class="col-sm-4 control-label"><?= lang('product_tax') ?> *</label>

                            <div class="col-sm-8">
                               <!--  <?php
                                $tr[""] = "";
                                foreach ($tax_rates as $tax) {
                                    $tr[$tax->id] = $tax->name;
                                }
                                echo form_dropdown('mtax', $tr, "", 'id="mtax" class="form-control input-tip select" style="width:100%;"');
                                ?> -->
                            </div>
                        </div>
                    <?php } ?>
                    <div class="form-group">
                        <label for="mquantity" class="col-sm-4 control-label"><?= lang('quantity') ?> *</label>

                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="mquantity">
                        </div>
                    </div>
                    <?php if ($Settings->product_discount && ($Owner || $Admin || $this->session->userdata('allow_discount'))) { ?>
                        <div class="form-group">
                            <label for="mdiscount"
                                   class="col-sm-4 control-label"><?= lang('product_discount') ?></label>

                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="mdiscount">
                            </div>
                        </div>
                    <?php } ?>
                    <div class="form-group">
                        <label for="mprice" class="col-sm-4 control-label"><?= lang('unit_price') ?> *</label>

                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="mprice">
                        </div>
                    </div>
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th style="width:25%;"><?= lang('net_unit_price'); ?></th>
                            <th style="width:25%;"><span id="mnet_price"></span></th>
                            <th style="width:25%;"><?= lang('product_tax'); ?></th>
                            <th style="width:25%;"><span id="mpro_tax"></span></th>
                        </tr>
                    </table>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="addItemManually"><?= lang('submit') ?></button>
            </div>
        </div>
    </div>
</div>
