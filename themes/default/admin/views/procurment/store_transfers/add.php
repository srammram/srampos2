<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<script>
    var store_transitems = {};
    $(document).ready(function(){
	if (localStorage.getItem('store_transfrom_store_id')!=null) {
	    $('#store_transfrom_store_id').val(localStorage.getItem('store_transfrom_store_id'));
	}
	if (localStorage.getItem('store_transto_store_id')!=null) {
	    $('#store_transto_store_id').val(localStorage.getItem('store_transto_store_id'));
	}
	/* if (localStorage.getItem('store_transto_store_id')!=null) {
	    $('#store_transto_store_id').val(9);
	} */
	if (localStorage.getItem('store_transrequestnumber')!=null) {
	    $("#store_transrequestnumber").val(localStorage.getItem('store_transrequestnumber'));
	}
	if (localStorage.getItem('intend_request_date')!=null) {
	    $("#intend_request_date").val(localStorage.getItem('intend_request_date'));
	}
    });
    //localStorage.setItem('store_transitems', JSON.stringify(<?= $store_transfers_items; ?>));
    
</script>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('add_store_transfers'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?php echo lang('enter_info'); ?></p>
                <?php
                $attrib = array('data-toggle' => 'validator1', 'role' => 'form','id'=>'store-transfer-form');
                echo admin_form_open_multipart("procurment/store_transfers/add", $attrib)
                ?>
                <div class="row">
                    <div class="col-lg-12">
		                   	<?php echo form_submit('add_store_transfers', $this->lang->line("save"), 'id="add_store_transfers" class="btn col-lg-1 btn-sm btn-primary pull-right"'); ?>
                                <button type="button" class="btn col-lg-1 btn-sm btn-danger pull-right" style="margin-right:15px;height:30px!important;font-size: 12px!important" id="reset"><?= lang('reset') ?></button>
                        <h2>Store_transfers Details</h2>

                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang("Date", "date") ?>
                                <input type="datetime" name="date" id="store_transdate" readonly class="form-control" value="<?php echo date('Y-m-d H:i:s') ?>">
                                
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang("from_warehouse", "store_transfrom_store_id"); ?>
                              <input type="text" name="from_store_name" id="from_store_name" readonly class="form-control" value="<?=$this->store_name?>">
							  <input type="hidden" name="from_store_id" id="from_store_id" readonly class="form-control" value="<?=$this->store_id?>">
                            </div>
                        </div>
                        
						 <div class="col-md-4">
                            <div class="form-group">
                                <?= lang("to_store", "store_transto_store_id"); ?>
                                <?php
                                $tst[''] = '';
                                            foreach ($all_stores as $store) {
                                                $tst[$store->id] = $store->name;
                                            }
                                echo form_dropdown('to_store_id', $tst, (isset($_POST['to_store_id']) ? $_POST['to_store_id'] : ''), 'id="store_transto_store_id" class="form-control input-tip select" data-placeholder="' . lang("select") . ' ' . lang("to_store") . '" required="required" style="width:100%;" ');
                                ?>
                            </div>
                        </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <?= lang("store_request_no", "store_transrequestnumber"); ?>
                            <?php
                           $qn[''] = '';
                            foreach ($store_req as $store_req_row) {
                                $qn[$store_req_row->id] = $store_req_row->reference_no;
                            }
                            echo form_dropdown('intend_request_id', $qn, (isset($_POST['intend_request_id']) ? $_POST['intend_request_id'] : '' ), ' class="form-control input-tip select" data-placeholder="' . lang("select") . ' ' . lang("request_number") . '"style="width:100%;" id="store_transrequestnumber"  ');
                            ?>
                            </div>
                        
                    </div>
					
                    
                    
						<div class="col-md-4">
                            <div class="form-group">
                                <?= lang("intend_request_date", "date") ?>
                                <input type="text" name="intend_request_date" id="intend_request_date" readonly class="form-control" value="">
                                
                            </div>
                        </div>
                       
                       
                         <input type="hidden" name="request_type" id="store_transtype" value="<?php echo 'new' ?>">
                      
                       
                        
                        <input type="hidden" name="warehouse" id="store_transwarehouse" value="<?php echo $Settings->default_warehouse ?>">  
                            <div class="col-md-4">
                                <div class="form-group">
                                    <?= lang("document", "document") ?>
                                  <input id="document" type="file" data-browse-label="<?= lang('browse'); ?>" name="document" data-show-upload="false"
                                           data-show-preview="false" class="form-control file">
                                   
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <?= lang("status", "store_transstatus"); ?>
                                    <?php $st['process'] = lang('process');
								if($this->siteprocurment->hasApprovedPermission()){
									$st['approved'] = lang('approved');	
								}
                                    echo form_dropdown('status', $st, '', 'class="form-control input-tip" id="store_transstatus"'); ?>
    
                                </div>
                            </div>
                             <div class="col-md-4">
                                <div class="form-group">
                                    <?= lang("remarks", "remarks"); ?>
                                 <input type="text" name="remarks" class="form-control">
                                </div>
                            </div>
                          <div class="clearfix"></div>	
                      

                        <div class="col-md-12" id="sticker">
                            <div class="well well-sm">
                              
                                <div class="form-group" style="margin-bottom:0;">
                                    <div class="input-group wide-tip">
                                        <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;">
                                            <i class="fa fa-2x fa-barcode addIcon"></i></a></div>
                                        <?php echo form_input('add_item', '', 'class="form-control input-lg" id="add_item" placeholder="' . $this->lang->line("add_Purchase Items_to_order") . '"'); ?>
                                        <?php if ($Owner || $Admin || $GP['products-add']) { ?>
                                        <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;">
                                            <a href="<?= admin_url('procurment/products/add') ?>" id="addManually1"><i
                                                    class="fa fa-2x fa-plus addIcon" id="addIcon"></i></a></div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
							
                        <div class="col-md-12">
                            <div class="control-group table-group">
                                <label class="table-label"><?= lang("items"); ?></label>

                                <div class="controls table-controls">
                                    <table id="store_transfersTable"
                                            class="table items  table-bordered table-condensed sortable_table" style="background:#fff">
                                        <thead>
                                        <tr>
                                          	<th><?=lang('s_no')?></th>
											<th><?=lang('code')?></th>
											<th><?=lang('product_name')?></th>					   
                                            <th><?= lang("request_quantity"); ?></th>
                                            <th><?=lang('batch')?></th>
                                            <th><?= lang("available_quantity"); ?></th>
                                            <th><?= lang("transfer_quantity"); ?></th>
                                            <th><?= lang("pending_quantity"); ?></th>
											<th><?=lang('expiry')?></th>
											<th><?=lang('cost_price')?></th>
											<th><?=lang('selling_price')?></th>
											<th><?=lang('tax')?></th>
											<th><?=lang('tax_amount')?></th>
											<th><?=lang('gross')?></th>
											<th><?=lang('total')?></th>
                                            <th style="width: 30px !important; text-align: center;"><i
                                            class="fa fa-trash-o"
                                            style="opacity:0.5; filter:alpha(opacity=50);"></i></th>
                                        </tr>
                                        </thead>
                                        <tbody></tbody>
                                        <tfoot>
										
										</tfoot>
                                    </table>
                                </div>
                            </div>
							
								
				
                        </div>
                        <div class="clearfix"></div>
                        
                          <table class="table " style="padding: 4px;border-top: none!important;width:30%;">
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
                

                <?php echo form_close(); ?>

            </div>

        </div>
    </div>
</div>

<script>


$(document).on('change', '#store_transrequestnumber', function(){
	
	if (localStorage.getItem('store_transitems')) {
        localStorage.removeItem('store_transitems');
    }
	if (localStorage.getItem('store_transrequestnumber')) {
        localStorage.removeItem('store_transrequestnumber');
    }
	if (localStorage.getItem('intend_request_date')) {
        localStorage.removeItem('intend_request_date');
    }
	if (localStorage.getItem('store_transtype')) {
        localStorage.removeItem('store_transtype');
    }
    if (localStorage.getItem('store_transdiscount')) {
        localStorage.removeItem('store_transdiscount');
    }
    if (localStorage.getItem('store_transtax2')) {
        localStorage.removeItem('store_transtax2');
    }
    if (localStorage.getItem('store_transshipping')) {
        localStorage.removeItem('store_transshipping');
    }
    if (localStorage.getItem('store_transref')) {
        localStorage.removeItem('store_transref');
    }
    if (localStorage.getItem('store_transwarehouse')) {
        localStorage.removeItem('store_transwarehouse');
    }
    if (localStorage.getItem('store_transnote')) {
        localStorage.removeItem('store_transnote');
    }
    if (localStorage.getItem('store_transsupplier')) {
        localStorage.removeItem('store_transsupplier');
    }
    if (localStorage.getItem('store_transcurrency')) {
        localStorage.removeItem('store_transcurrency');
    }
    if (localStorage.getItem('store_transextras')) {
        localStorage.removeItem('store_transextras');
    }
    if (localStorage.getItem('store_transdate')) {
        localStorage.removeItem('store_transdate');
    }
    if (localStorage.getItem('store_transstatus')) {
        localStorage.removeItem('store_transstatus');
    }
	
	 if (localStorage.getItem('store_transfrom_store_id')) {
        localStorage.removeItem('store_transfrom_store_id');
    }
	 if (localStorage.getItem('store_transto_store_id')) {
        localStorage.removeItem('store_transto_store_id');
    }
	
    if (localStorage.getItem('store_transpayment_term')) {
        localStorage.removeItem('store_transpayment_term');
    }
	
	var store_transrequestnumber = $(this).val();
	
	$.ajax({
		type: 'get',
		url: '<?= admin_url('procurment/store_transfers/store_transfers_list'); ?>',
		dataType: "json",
		data: {
			poref: store_transrequestnumber
		},
		success: function (data) {
			
			var store_transfers_value = [];
			$(this).removeClass('ui-autocomplete-loading');
			var items = JSON.stringify(data.value['store_transfersitem']);
			console.log(items)
			var store_transfers = JSON.stringify(data.value['store_transfers']);
			store_transfers_value = $.parseJSON(store_transfers);
			
			
			localStorage.setItem('store_transrequestnumber',  store_transfers_value["id"]);
			
			localStorage.setItem('intend_request_date',  store_transfers_value["date"]);
			 $("#intend_request_date").val(localStorage.getItem('intend_request_date'));
			localStorage.setItem('store_transtype',  store_transfers_value["request_type"]);
			
			localStorage.setItem('store_transfrom_store_id',  store_transfers_value["from_store_id"]);
			localStorage.setItem('store_transto_store_id',  store_transfers_value["to_store_id"]);
			
			localStorage.setItem('store_transwarehouse', store_transfers_value["warehouse_id"]);
			localStorage.setItem('store_transnote', store_transfers_value["note"]);
			localStorage.setItem('store_transdiscount', 0);
			localStorage.setItem('store_transtax2', store_transfers_value["order_tax_id"]);
			localStorage.setItem('store_transshipping', store_transfers_value["shipping"]);
			localStorage.setItem('store_transsupplier', store_transfers_value["supplier_id"]);
			localStorage.setItem('store_transitems', items);
			localStorage.setItem('store_transfers_date', store_transfers_value["store_transfers_date"]);
			
			loadItems();
			//location.reload();
			
		}
		
		
	});
});
</script>

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
                                <?php
                                $tr[""] = "";
                                foreach ($tax_rates as $tax) {
                                    $tr[$tax->id] = $tax->name;
                                }
                                echo form_dropdown('ptax', $tr, "", 'id="ptax" class="form-control pos-input-tip" style="width:100%;"');
                                ?>
				
                            </div>
                        </div>
						<div class="form-group">
							<label class="col-sm-4 control-label"><?= lang("tax_method") ?></label>
							<div class="col-sm-8">
								<?php
								$tm = array('1' => lang('exclusive'), '0' => lang('inclusive'));
								echo form_dropdown('ptax_method', $tm, "", 'id="ptax_method" class="form-control pos-input-tip" style="width:100%"');
								?>
							</div>
                        </div>
						
                    <?php } ?>
                    <div class="form-group">
                        <label for="pquantity" class="col-sm-4 control-label"><?= lang('quantity') ?></label>

                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="pquantity">
                        </div>
                    </div>
					
				<!-- 	<div class="form-group">
                            <label for="pbatch_no" class="col-sm-4 control-label"><?= lang('Batch_no') ?></label>

                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="pbatch_no">
                            </div>
                        </div> -->
						
                   <!--  <?php if ($Settings->product_expiry) { ?>
                    	<div class="form-group">
                            <label for="pmfg" class="col-sm-4 control-label"><?= lang('Product_mfg') ?></label>

                            <div class="col-sm-8">
                                <input type="text" class="form-control date" id="pmfg">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="pexpiry" class="col-sm-4 control-label"><?= lang('Product_expiry') ?></label>

                            <div class="col-sm-8">
                                <input type="text" class="form-control date" id="pexpiry">
                            </div>
                        </div>
                    <?php } ?> -->
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
                    <?php if ($Settings->product_discount) { ?>
                        <div class="form-group">
                            <label for="pdiscount" class="col-sm-4 control-label"><?= lang('product_discount') ?></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="pdiscount">
                            </div>
                        </div>
                    <?php } ?>
                    <div class="form-group">
                        <label for="pcost" class="col-sm-4 control-label"><?= lang('unit_cost') ?></label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="pcost">
                        </div>
                    </div>
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th style="width:25%;"><?= lang('net_unit_cost'); ?></th>
                            <th style="width:25%;"><span id="net_cost"></span></th>
                            <th style="width:25%;"><?= lang('product_tax'); ?></th>
                            <th style="width:25%;"><span id="pro_tax"></span></th>
                        </tr>
                    </table>
                    <div class="panel panel-default">
                        <div class="panel-heading"><?= lang('calculate_unit_cost'); ?></div>
                        <div class="panel-body">

                            <div class="form-group">
                                <label for="pcost" class="col-sm-4 control-label"><?= lang('subtotal') ?></label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="psubtotal">
                                        <div class="input-group-addon" style="padding: 2px 8px;">
                                            <a href="#" id="calculate_unit_price" class="tip" title="<?= lang('calculate_unit_cost'); ?>">
                                                <i class="fa fa-calculator"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" id="punit_cost" value=""/>
                    <input type="hidden" id="old_tax" value=""/>
                    <input type="hidden" id="old_qty" value=""/>
                    <input type="hidden" id="old_cost" value=""/>
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
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true"><i
                            class="fa fa-2x">&times;</i></span><span class="sr-only"><?=lang('close');?></span></button>
                <h4 class="modal-title" id="mModalLabel"><?= lang('add_standard_product') ?></h4>
            </div>
            <div class="modal-body" id="pr_popover_content">
                <div class="alert alert-danger" id="mError-con" style="display: none;">
                    <!--<button data-dismiss="alert" class="close" type="button">×</button>-->
                    <span id="mError"></span>
                </div>
                <div class="row">
                    <div class="col-md-6 col-sm-6">
                        <div class="form-group">
                            <?= lang('product_code', 'mcode') ?> *
                            <input type="text" class="form-control" id="mcode">
                        </div>
                        <div class="form-group">
                            <?= lang('product_name', 'mname') ?> *
                            <input type="text" class="form-control" id="mname">
                        </div>
                        <div class="form-group">
                            <?= lang('category', 'mcategory') ?> *
                            <?php
                            $cat[''] = "";
                            foreach ($categories as $category) {
                                $cat[$category->id] = $category->name;
                            }
                            echo form_dropdown('category', $cat, '', 'class="form-control select" id="mcategory" placeholder="' . lang("select") . " " . lang("category") . '" style="width:100%"')
                            ?>
                        </div>
                        <div class="form-group">
                            <?= lang('unit', 'munit') ?> *
                            <input type="text" class="form-control" id="munit">
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-6">
                        <div class="form-group">
                            <?= lang('cost', 'mcost') ?> *
                            <input type="text" class="form-control" id="mcost">
                        </div>
                        <div class="form-group">
                            <?= lang('price', 'mprice') ?> *
                            <input type="text" class="form-control" id="mprice">
                        </div>

                        <?php if ($Settings->tax1) { ?>
                            <div class="form-group">
                                <?= lang('product_tax', 'mtax') ?>
                                <?php
                                $tr[""] = "";
                                foreach ($tax_rates as $tax) {
                                    $tr[$tax->id] = $tax->name;
                                }
                                echo form_dropdown('mtax', $tr, "", 'id="mtax" class="form-control input-tip select" style="width:100%;"');
                                ?>
                            </div>
                            <div class="form-group all">
                                <?= lang("tax_method", "mtax_method") ?>
                                <?php
                                $tm = array('0' => lang('inclusive'), '1' => lang('exclusive'));
                                echo form_dropdown('tax_method', $tm, '', 'class="form-control select" id="mtax_method" placeholder="' . lang("select") . ' ' . lang("tax_method") . '" style="width:100%"')
                                ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="addItemManually"><?= lang('submit') ?></button>
            </div>
        </div>
    </div>
</div>
