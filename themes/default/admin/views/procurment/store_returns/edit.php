<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<script type="text/javascript">
    var count = 1, an = 1, DT = <?= $Settings->default_tax_rate ?>, allow_discount = <?= ($Owner || $Admin || $this->session->userdata('allow_discount')) ? 1 : 0; ?>,
        product_tax = 0, invoice_tax = 0, total_discount = 0, total = 0, shipping = 0,
        tax_rates = <?php echo json_encode($tax_rates); ?>;
    var audio_success = new Audio('<?=$assets?>sounds/sound2.mp3');
    var audio_error = new Audio('<?=$assets?>sounds/sound3.mp3');
    $(document).ready(function () {
        <?php if ($inv) { ?>
        localStorage.setItem('store_rtn_date', '<?= date($dateFormats['php_ldate'], strtotime($inv->date))?>');
        localStorage.setItem('store_rtn_customer', '<?=$inv->customer_id?>');
		localStorage.setItem('store_rtn_from_store_id', '<?=$inv->from_store_id?>');
		localStorage.setItem('store_rtn_to_store_id', '<?=$inv->to_store_id?>');
        localStorage.setItem('store_rtn_biller', '<?=$inv->biller_id?>');
        localStorage.setItem('store_rtn_supplier', '<?=$inv->supplier_id?>');
        localStorage.setItem('store_rtn_ref', '<?=$inv->reference_no?>');
        localStorage.setItem('store_rtn_warehouse', '<?=$inv->warehouse_id?>');
        localStorage.setItem('store_rtn_status', '<?=$inv->status?>');
        localStorage.setItem('store_rtn_note', '<?= $inv->note; ?>');
        localStorage.setItem('store_rtn_discount', '<?=$inv->order_discount_id?>');
        localStorage.setItem('store_rtn_tax2', '<?=$inv->order_tax_id?>');
        localStorage.setItem('store_rtn_shipping', '<?=$inv->shipping?>');
        localStorage.setItem('store_rtn_items', JSON.stringify(<?=$inv_items;?>));
        <?php } ?>
        <?php //if ($Owner || $Admin) { ?>
        $(document).on('change', '#store_rtn_date', function (e) {
            localStorage.setItem('store_rtn_date', $(this).val());
        });
        if (store_rtn_date = localStorage.getItem('store_rtn_date')) {
            $('#store_rtn_date').val(store_rtn_date);
        }
        <?php //} ?>
        $(document).on('change', '#store_rtn_biller', function (e) {
            localStorage.setItem('store_rtn_biller', $(this).val());
        });
        if (store_rtn_biller = localStorage.getItem('store_rtn_biller')) {
            $('#store_rtn_biller').val(store_rtn_biller);
        }
        ItemnTotals();
       $("#add_item").autocomplete({
            source: function (request, response) {
                if (!$('#store_rtn_from_store_id').val() && !$('#store_rtn_to_store_id').val() ) {
                    $('#add_item').val('').removeClass('ui-autocomplete-loading');
                    bootbox.alert('<?=lang('select_above_from_store_and_to_store');?>');
                    //response('');
                    $('#add_item').focus();
                    return false;
                }
                $.ajax({
                    type: 'get',
                    url: '<?= admin_url('procurment/store_returns/suggestions'); ?>',
                    dataType: "json",
                    data: {
                        term: request.term,
                        warehouse_id: $("#store_rtn_warehouse").val(),
                        store_id: $("#store_rtn_from_store_id").val()
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
        $('#edit_store_returns').click(function () {
            $(window).unbind('beforeunload');
            $('form.edit-req-form').submit();
        });
		
		var store_rtn_from_store_id;
        $('#store_rtn_from_store_id').on("select2-focus", function (e) {
            store_rtn_from_store_id = $(this).val();
        }).on("select2-close", function (e) {
            if ($(this).val() != '' && $(this).val() == $('#store_rtn_to_store_id').val()) {
                $(this).select2('val', store_rtn_from_store_id);
                bootbox.alert('<?= lang('please_select_different_store') ?>');
            }
        });
        var store_rtn_to_store_id;
        $('#store_rtn_to_store_id').on("select2-focus", function (e) {
            store_rtn_to_store_id = $(this).val();
        }).on("select2-close", function (e) {
            if ($(this).val() != '' && $(this).val() == $('#store_rtn_from_store_id').val()) {
                $(this).select2('val', store_rtn_to_store_id);
                bootbox.alert('<?= lang('please_select_different_store') ?>');
            }
        });
		
    });
</script>


<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-edit"></i><?= lang('edit_store_returns'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?php echo lang('enter_info'); ?></p>
                <?php
                $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'class' => 'edit-req-form');
                echo admin_form_open_multipart("procurment/store_returns/edit/" . $id, $attrib)
                ?>

                <div class="row">
                    <div class="col-lg-12">
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang("Date", "date") ?>
                                <input type="datetime" name="date" id="store_rtn_date" readonly class="form-control" value="<?php echo $inv->date ?>">
                                
                            </div>
                        </div>
                        
                        
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang("reference_no", "store_rtn_ref"); ?>
                                <?php echo form_input('reference_no', (isset($_POST['reference_no']) ? $_POST['reference_no'] : ''), 'class="form-control input-tip" id="store_rtn_ref" required="required" readonly'); ?>
                            </div>
                        </div>
                      
                      
                        <input type="hidden" name="request_type" id="store_rtn_type" value="<?php echo $inv->request_type ?>">
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang("from_store", "store_rtn_from_store_id"); ?>
                                <?php
                                $fst[''] = '';
                                foreach ($stores as $store) {
                                    $fst[$store->id] = $store->name;
                                }
                                echo form_dropdown('from_store_id', $fst, (isset($_POST['from_store_id']) ? $_POST['from_store_id'] : $inv->from_store_id), 'id="store_rtn_from_store_id" class="form-control input-tip select" data-placeholder="' . lang("select") . ' ' . lang("from_store") . '" style="width:100%;" disabled ');
                                ?>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang("to_store", "store_rtn_to_store_id"); ?>
                                <?php
                                $tst[''] = '';
                                foreach ($stores as $store) {
                                    $tst[$store->id] = $store->name;
                                }
                                echo form_dropdown('to_store_id', $tst, (isset($_POST['to_store_id']) ? $_POST['to_store_id'] : $inv->to_store_id), 'id="store_rtn_to_store_id" class="form-control input-tip select" data-placeholder="' . lang("select") . ' ' . lang("to_store") . '"  style="width:100%;" disabled ');
                                ?>
                            </div>
                        </div>
                        
                        
                      

                        

                       

                        

                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang("document", "document") ?>
                                <input id="document" type="file" data-browse-label="<?= lang('browse'); ?>" name="document" data-show-upload="false"
                                       data-show-preview="false" class="form-control file">
                            </div>
                        </div>
                        
                         <div class="col-md-4">
                            <div class="form-group">
                                <?= lang("status", "store_rtn_status"); ?>
                                <?php $st['process'] = lang('process');
								if($this->siteprocurment->GETaccessModules('store_return_approved')){
									$st['approved'] = lang('approved');	
									$st['completed'] = lang('completed');	
								}
                                echo form_dropdown('status', $st, $inv->status, 'class="form-control input-tip" id="store_rtn_status"'); ?>

                            </div>
                        </div>

                       
                       <input type="hidden" name="warehouse" id="store_rtn_warehouse" value="<?php echo $inv->warehouse_id ?>">
                       
                        <div class="col-md-12" id="sticker">
                            <div class="well well-sm">
                                <div class="form-group" style="margin-bottom:0;">
                                    <div class="input-group wide-tip">
                                        <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;">
                                            <i class="fa fa-2x fa-barcode addIcon"></i></a></div>
                                        <?php echo form_input('add_item', '', 'class="form-control input-lg" id="add_item" placeholder="' . $this->lang->line("add_product_to_order") . '"'); ?>
                                        <?php if ($Owner || $Admin || $GP['products-add']) { ?>
                                        <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;">
                                            <a href="javascript:void(0)" id="addManually" class="tip" title="<?= lang('add_product_manually') ?>">
                                                <i class="fa fa-2x fa-plus-circle addIcon" id="addIcon"></i>
                                            </a>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-md-12">
                            <div class="control-group table-group">
                                <label class="table-label"><?= lang("items"); ?> *</label>

                                <div class="controls table-controls">
                                    <table id="store_rtn_Table"
                                            class="table items  table-bordered table-condensed sortable_table" style="background:#fff">
                                        <thead>
                                        <tr>
                                            <th class="col-md-4"><?= lang('product') . ' (' . lang('code') .' - '.lang('name') . ')'; ?></th>
                                           
                                            <th class="col-md-2"><?= lang("batch_no"); ?></th>
                                             <th class="col-md-2"><?= lang("available_quantity"); ?></th>
                                            <th class="col-md-1"><?= lang("quantity"); ?></th>
                                            
                                            <th style="width: 30px !important; text-align: center;"><i
                                                    class="fa fa-trash-o"
                                                    style="opacity:0.5; filter:alpha(opacity=50);"></i></th>
                                        </tr>
                                        </thead>
                                        <tbody></tbody>
                                        <tfoot></tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                       <input type="hidden" name="total_items" value="" id="total_items"/>

                        <div class="row" id="bt">
                            <div class="col-sm-12">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <?= lang("note", "store_rtn_note"); ?>
                                        <?php echo form_textarea('note', (isset($_POST['note']) ? $_POST['note'] : ""), 'class="form-control" id="store_rtn_note" style="margin-top: 10px; height: 100px;"'); ?>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <?php
						if($inv->status == 'process'){
						?>
                        <div class="col-sm-12">
                            <div
                                class="fprom-group"><?php echo form_submit('edit_store_returns', $this->lang->line("submit"), 'id="edit_store_returns" class="btn btn-primary" style="padding: 6px 15px; margin:15px 0;"'); ?>
                                <button type="button" class="btn btn-danger" id="reset"><?= lang('reset') ?></div>
                        </div>
                        <?php
						}
						?>
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
