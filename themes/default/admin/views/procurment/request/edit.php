<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<script type="text/javascript">
    var default_store = '<?=$default_store?>';
    var count = 1, an = 1, DT = <?= $Settings->default_tax_rate ?>, allow_discount = <?= ($Owner || $Admin || $this->session->userdata('allow_discount')) ? 1 : 0; ?>,
        product_tax = 0, invoice_tax = 0, total_discount = 0, total = 0, shipping = 0,
        tax_rates = <?php echo json_encode($tax_rates); ?>;
    var audio_success = new Audio('<?=$assets?>sounds/sound2.mp3');
    var audio_error = new Audio('<?=$assets?>sounds/sound3.mp3');
    $(document).ready(function () {
        <?php if ($inv) { ?>
        localStorage.setItem('reqdate', '<?= date($dateFormats['php_ldate'], strtotime($inv->date))?>');
        localStorage.setItem('reqcustomer', '<?=$inv->customer_id?>');
        localStorage.setItem('reqbiller', '<?=$inv->biller_id?>');
        localStorage.setItem('reqsupplier', '<?=$inv->supplier_id?>');
        localStorage.setItem('reqref', '<?=$inv->reference_no?>');
        localStorage.setItem('reqwarehouse', '<?=$inv->warehouse_id?>');
        localStorage.setItem('reqstatus', '<?=$inv->status?>');
        localStorage.setItem('reqnote', '<?= $inv->note; ?>');
		localStorage.setItem('reqcurrency', '<?= $inv->currency; ?>');
		localStorage.setItem('reqsupplier_address', '<?= $inv->supplier_address; ?>');
        localStorage.setItem('reqdiscount', '<?=$inv->order_discount_id?>');
        localStorage.setItem('reqtax2', '<?=$inv->order_tax_id?>');
        localStorage.setItem('reqshipping', '<?=$inv->shipping?>');
        localStorage.setItem('reqitems', JSON.stringify(<?=$inv_items;?>));
        <?php } ?>
        <?php //if ($Owner || $Admin) { ?>
        $(document).on('change', '#reqdate', function (e) {
            localStorage.setItem('reqdate', $(this).val());
        });
        if (reqdate = localStorage.getItem('reqdate')) {
            $('#reqdate').val(reqdate);
        }
        <?php //} ?>
        $(document).on('change', '#reqbiller', function (e) {
            localStorage.setItem('reqbiller', $(this).val());
        });
        if (reqbiller = localStorage.getItem('reqbiller')) {
            $('#reqbiller').val(reqbiller);
        }
		$(document).on('change', '#reqsupplier', function (e) {
			$.ajax({
				type: 'get',
				url: '<?= admin_url('procurment/request/supplier_details'); ?>',
				dataType: "json",
				data: {					
					supplier_id: $(this).val()
				},
				success: function (data) {
					$('#reqsupplier_address').val(data.details);
					localStorage.setItem('reqsupplier_address', data.details);
				}
			});
            localStorage.setItem('reqsupplier', $(this).val());
        });
        if (reqsupplier_address = localStorage.getItem('reqsupplier_address')) {
            $('#reqsupplier_address').val(reqsupplier_address);
        }
		 if (reqsupplier = localStorage.getItem('reqsupplier')) {
            $('#reqsupplier').val(reqsupplier);
        }
		
        ItemnTotals();
       $("#add_item").autocomplete({
            source: function (request, response) {
                if (!$('#reqsupplier').val()) {
                    $('#add_item').val('').removeClass('ui-autocomplete-loading');
                    bootbox.alert('<?=lang('select_above');?>');
                    //response('');
                    $('#add_item').focus();
                    return false;
                }
                $.ajax({
                    type: 'get',
                    url: '<?= admin_url('procurment/request/suggestions'); ?>',
                    dataType: "json",
                    data: {
                        term: request.term,
                        warehouse_id: $("#reqwarehouse").val(),
                        supplier_id: $("#reqsupplier").val()
                    },
                    success: function (data) {
                        $(this).removeClass('ui-autocomplete-loading');
                        response(data);
                    }
                });
            },
            minLength: 1,
            autoFocus: false,
            delay: 250,
            response: function (event, ui) {
                if ($(this).val().length >= 16 && ui.content[0].id == 0) {
                    bootbox.alert('<?= lang('no_match_found') ?>', function () {
                        $('#add_item').focus();
                    });
                    $(this).removeClass('ui-autocomplete-loading');
                    $(this).val('');
                }
                /*else if (ui.content.length == 1 && ui.content[0].id != 0) {
                    ui.item = ui.content[0];
                    $(this).data('ui-autocomplete')._trigger('select', 'autocompleteselect', ui);
                    $(this).autocomplete('close');
                    $(this).removeClass('ui-autocomplete-loading');
                }*/
                else if (ui.content.length == 1 && ui.content[0].id == 0) {
                    bootbox.alert('<?= lang('no_match_found') ?>', function () {
                        $('#add_item').focus();
                    });
                    $(this).removeClass('ui-autocomplete-loading');
                    $(this).val('');

                }
            },
            select: function (event, ui) {
                event.preventDefault();
                if (ui.item.id !== 0) {
                    var row = add_invoice_item(ui.item);
                    if (row)
                        $(this).val('');
                } else {
                    bootbox.alert('<?= lang('no_match_found') ?>');
                }
            }
        });

        $(window).bind('beforeunload', function (e) {
            $.get('<?= admin_url('welcome/set_data/remove_quls/1'); ?>');
            if (count > 1) {
                var message = "You will loss data!";
                return message;
            }
        });
        $('#reset').click(function (e) {
            $(window).unbind('beforeunload');
        });
        $('#edit_request').click(function () {
            $(window).unbind('beforeunload');
            $('form.edit-req-form').submit();
			$('#edit_request').attr('disabled',false);
        });
    });
</script>


<div class="box">
    <div class="box-header">        
        <h2 class=""><?= lang('edit_quotation_request'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <!--<p class="introtext"><?php echo lang('enter_info'); ?></p>-->
                <?php
                $attrib = array('data-toggle' => 'validator1', 'role' => 'form', 'class' => 'edit-req-form', 'id' => 'add-quotation-request');
                echo admin_form_open_multipart("procurment/request/edit/" . $id, $attrib)
                ?>

                <div class="row">
                    <div class="col-lg-12" style="background:#b1d7fd; padding:15px 15px;">
                    	<?php
						if($inv->status == 'process'){
						?>
                        <?php echo form_submit('edit_request', $this->lang->line("update"), 'id="edit_request" class="btn col-lg-1 btn-sm btn-primary pull-right" '); ?>
                        <button type="button" class="btn btn-sm btn-danger col-lg-1 pull-right" id="reset" style="display: none;margin-right:15px;height:30px!important;font-size: 12px!important"><?= lang('reset'); ?></button>
                        <?php
						}
						?>
                        
                        <input type="hidden" name="warehouse" id="reqwarehouse" value="<?php echo $inv->warehouse ? $inv->warehouse : $Settings->default_warehouse ?>"> 
                        <input type="hidden" name="biller" id="reqbiller" value="<?php echo $inv->biller ? $inv->biller : $Settings->default_biller ?>"> 
                        
                        <table class="table custom_tables">
                        	<tbody>
                            	<tr>                                	
                                	<td>
                                    	<?= lang("date", "date") ?>
                                    </td>
                                    <td>
                                    	<input  name="date" id="reqdate" readonly class="form-control" value="<?php echo $inv->date ?>">
                                    </td>
                                    <td width="150px">
                                    	<?= lang("quotation_req_no", "quotation_req_no") ?>
                                    </td>
                                    <td>                                    	
                                    	<input  name="reference_no" id="reqref" readonly class="form-control" value="<?php echo $inv->reference_no ?>">
                                    </td>
                                    
                                    <td width="100px">
                                    	<?= lang("currency", "reqcurrency") ?>
                                    </td>
                                    <td>
                                    	<?php
										$c[""] = "";
										foreach ($currencies as $currencie) {
											$c[$currencie->id] = $currencie->code;
										}
										echo form_dropdown('currency', $c, ($inv->currency ? $inv->currency : $Settings->default_currency), 'id="reqcurrency" data-placeholder="' . $this->lang->line("select") . ' ' . $this->lang->line("currencies") . '" required="required" class="form-control input-tip" style="width:100%;"');
										?>
                                    </td>   
                                </tr>
                                <tr>
                                    <td width="100px">
                                        <?= lang("supplier", "reqsupplier") ?>
                                    </td>
                                    <td width="350px">
                                        <?php
                                        $sl = array();
                                        foreach ($suppliers as $supplier) {
                                            $sl[$supplier->id] = $supplier->name;
                                        }
                                        echo form_dropdown('supplier', $sl, ($inv->supplier_id ? $inv->supplier : $Settings->default_supplier), 'id="reqsupplier" data-placeholder="' . $this->lang->line("select") . ' ' . $this->lang->line("supplier") . '" required="required" class="form-control input-tip select" style="width:100%;"');
                                        ?>
                                    </td>

                                	<td width="100px">
                                    	<?= lang("supplier_address", "supplier_address") ?>
                                    </td>
                                    <td width="350px">
                                    	<input  name="supplier_address" id="reqsupplier_address" readonly class="form-control" value="<?= $inv->supplier_address ?>">
                                    </td>
                                    
                                	<td>
                                    	<?= lang("status", "reqstatus") ?>
                                    </td>
                                    <td>
                                    	<?php 
										$st['process'] = lang('process');
										if($this->siteprocurment->hasApprovedPermission()){
											$st['approved'] = lang('approved');	
										}
										echo form_dropdown('status', $st, $inv->status, 'class="form-control input-tip" id="reqstatus"'); ?>
                                    </td>                                    
                                </tr>
                                <tr>
                                	<td>
                                    	<?= lang("document", "document") ?>
                                    </td>
                                    <td>
                                    	<input id="document" type="file" data-browse-label="" name="document" data-show-upload="false"
                                       data-show-preview="false" class="form-control file">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                      </div>
                       
                       <div class="clearfix"></div>
                         <div class="col-md-12" id="sticker">
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
                        </div>


                        <div class="col-md-12">
                            <div class="control-group table-group">
                               
                                <div class="controls table-controls">
                                    <table id="reqTable"
                                            class="table items  table-bordered table-condensed sortable_table" style="background:#fff">
                                        <thead>
                                        <tr>
                                            <th class="col-md-1"><?= lang("S.NO"); ?></th>
                                            <th class="col-md-2"><?= lang("code"); ?></th>
                                            <th class="col-md-2"><?= lang('product') . lang('name'); ?></th>
                                            <th class="col-md-2"><?= lang("Category"); ?></th>
                                            <th class="col-md-2"><?= lang("Subcategory"); ?></th>
                                            <th class="col-md-2"><?= lang("Brand"); ?></th>
                                            <th class="col-md-2"><?= lang("qantity"); ?></th>
                                            <th class="col-md-2"><?= lang("Cost.Price"); ?></th>
                                            <th class="col-md-2"><?= lang("Selling.Price"); ?></th>
                                           <th class="col-md-1" style="text-align: center;"><i
                                                    class="fa fa-trash-o"
                                                    style="opacity:0.5; filter:alpha(opacity=50);"></i></th>
                                        </tr>
                                        </thead>
                                        <tbody></tbody>
                                        <!-- <tfoot></tfoot> -->
                                    </table>                                                                       
                                </div>
                            </div>
                        </div>
						
                    
                    	<div class="col-lg-12" style="background:#a6f7a1; margin-top:15px;">
                    <table class="table custom_tables">
                            <tbody>
                                <tr>                                    
                                    <td width="150px">
                                        <?= lang("Total No Of Qty", "total_items") ?>
                                    </td>
                                    <td width="150px">
                                        <input  name="total_items" id="total_items" readonly class="form-control">
                                    </td>
                                    <td width="150px">                                    
                                    </td>
                                    <td>
                                        <?= lang("Remarks/Note", "note") ?>
                                    </td>
                                    <td colspan="3">
                                        <input  name="note" id="reqnote" value="<?= $inv->note; ?>" class="form-control" >
                                    </td>
                                    <td>                                    
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
                    <?php } ?>
                    <?php if ($Settings->product_serial) { ?>
                        <div class="form-group">
                            <label for="pserial" class="col-sm-4 control-label"><?= lang('serial_no') ?></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="pserial">
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
                    <?php if ($Settings->product_discount) { ?>
                        <div class="form-group">
                            <label for="pdiscount" class="col-sm-4 control-label">
                                <?= lang('product_discount') ?>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="pdiscount" <?= ($Owner || $Admin || $this->session->userdata('allow_discount')) ? '' : 'readonly="true"'; ?>>
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
                                <?php
                                $tr[""] = "";
                                foreach ($tax_rates as $tax) {
                                    $tr[$tax->id] = $tax->name;
                                }
                                echo form_dropdown('mtax', $tr, "", 'id="mtax" class="form-control input-tip select" style="width:100%;"');
                                ?>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="form-group">
                        <label for="mquantity" class="col-sm-4 control-label"><?= lang('quantity') ?> *</label>

                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="mquantity">
                        </div>
                    </div>
                    <?php if ($Settings->product_discount) { ?>
                        <div class="form-group">
                            <label for="mdiscount" class="col-sm-4 control-label">
                                <?= lang('product_discount') ?>
                            </label>

                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="mdiscount" <?= ($Owner || $Admin || $this->session->userdata('allow_discount')) ? '' : 'readonly="true"'; ?>>
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
