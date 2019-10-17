<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<script type="text/javascript">
    var count = 1, an = 1, po_edit = true, product_variant = 0, DT = <?= $Settings->default_tax_rate ?>, DC = '<?=$default_currency->code?>', shipping = 0,
        product_tax = 0, invoice_tax = 0, total_discount = 0, total = 0,
        tax_rates = <?php echo json_encode($tax_rates); ?>, quoteitems = {},
        audio_success = new Audio('<?= $assets ?>sounds/sound2.mp3'),
        audio_error = new Audio('<?= $assets ?>sounds/sound3.mp3');        
   
    $(document).ready(function () {
     
        <?php if ($inv) { ?>
        localStorage.setItem('quotedate', '<?= date($dateFormats['php_ldate'], strtotime($inv->date))?>');
        localStorage.setItem('quotesupplier', '<?=$inv->customer_id?>');
        /*localStorage.setItem('quoteref', '<?=$inv->reference_no?>');*/
        localStorage.setItem('quotewarehouse', '<?=$inv->warehouse_id?>');   
        localStorage.setItem('quotediscount', '<?=$inv->order_discount_id?>');
        localStorage.setItem('quotetax2', '<?=$inv->order_tax_id?>');
        localStorage.setItem('quoteshipping', '<?=$inv->shipping?>');
        
        if (parseFloat(localStorage.getItem('quotetax2')) >= 1 || localStorage.getItem('quotediscount').length >= 1 || parseFloat(localStorage.getItem('quoteshipping')) >= 1) {
            localStorage.setItem('quoteextras', '1');
        }
        localStorage.setItem('quoteitems', JSON.stringify(<?=$inv_items;?>));
		
        <?php } ?>

        <?php if ($Owner || $Admin) { ?>
        $(document).on('change', '#podate', function (e) {
            localStorage.setItem('quotedate', $(this).val());
        });
        if (podate = localStorage.getItem('quotedate')) {
            $('#podate').val(podate);
        }
        <?php } ?>
        ItemnTotals();
        $("#add_item").autocomplete({
            source: '<?= admin_url('quotes/suggestions'); ?>',
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
               /* else if (ui.content.length == 1 && ui.content[0].id != 0) {
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
                    var row = add_quote_item(ui.item);
					
                    if (row)
                        $(this).val('');
                } else {
                    bootbox.alert('<?= lang('no_match_found') ?>');
                }
            }
        });

        $(document).on('click', '#addItemManually', function (e) {
            if (!$('#mcode').val()) {
                $('#mError').text('<?=lang('product_code_is_required')?>');
                $('#mError-con').show();
                return false;
            }
            if (!$('#mname').val()) {
                $('#mError').text('<?=lang('product_name_is_required')?>');
                $('#mError-con').show();
                return false;
            }
            if (!$('#mcategory').val()) {
                $('#mError').text('<?=lang('product_category_is_required')?>');
                $('#mError-con').show();
                return false;
            }
            if (!$('#munit').val()) {
                $('#mError').text('<?=lang('product_unit_is_required')?>');
                $('#mError-con').show();
                return false;
            }
            if (!$('#mcost').val()) {
                $('#mError').text('<?=lang('product_cost_is_required')?>');
                $('#mError-con').show();
                return false;
            }
            if (!$('#mprice').val()) {
                $('#mError').text('<?=lang('product_price_is_required')?>');
                $('#mError-con').show();
                return false;
            }

            var msg, row = null, product = {
                type: 'standard',
                code: $('#mcode').val(),
                name: $('#mname').val(),
                tax_rate: $('#mtax').val(),
                tax_method: $('#mtax_method').val(),
                category_id: $('#mcategory').val(),
                unit: $('#munit').val(),
                cost: $('#mcost').val(),
                price: $('#mprice').val()
            };

            $.ajax({
                type: "get", async: false,
                url: site.base_url + "products/addByAjax",
                data: {token: "<?= $csrf; ?>", product: product},
                dataType: "json",
                success: function (data) {
                    if (data.msg == 'success') {
                        row = add_quote_item(data.result);
                    } else {
                        msg = data.msg;
                    }
                }
            });
            if (row) {
                $('#mModal').modal('hide');
            } else {
                $('#mError').text(msg);
                $('#mError-con').show();
            }
            return false;

        });
        $(window).bind('beforeunload', function (e) {
            $.get('<?=admin_url('welcome/set_data/remove_pols/1');?>');
            if (count > 1) {
                var message = "You will loss data!";
                return message;
            }
        });
        $('#reset').click(function (e) {
            $(window).unbind('beforeunload');
        });
        $('#edit_quotation').click(function () {            
            $(window).unbind('beforeunload');
           // $('form.edit-quote-form').submit();
            
        });

    });


</script>

<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-edit"></i><?= lang('edit_quote'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?php echo lang('enter_info'); ?></p>
                <?php
                $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'class' => 'edit-quote-form');
                echo admin_form_open_multipart("quotes/edit/" . $inv->id, $attrib)
                ?>


                <div class="row">
                    <div class="col-lg-12">

                        <?php /*if ($Owner || $Admin) { ?>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <?= lang("date", "podate"); ?>
                                    <?php echo form_input('date', (isset($_POST['date']) ? $_POST['date'] : $this->sma->hrld($purchase->date)), 'class="form-control input-tip datetime" id="podate" required="required"'); ?>
                                </div>
                            </div>
                        <?php } */?>
                       <!--  <div class="col-md-6" style="padding-bottom: 10px;">
                            <div class="form-group">
								<div class="col-md-5">
                                <?= lang("reference_no", "poref"); ?>
								</div>
								<div class="col-md-7">
                                <?php echo form_input('reference_no', (isset($_POST['reference_no']) ? $_POST['reference_no'] : $quotation->reference_no), 'class="form-control input-tip" id="poref" required="required"'); ?>
								</div>
                            </div>
                        </div>
						
						
						<div class="col-md-6" style="padding-bottom: 10px;">
                            <div class="form-group">
                                <div class="col-md-5">
                                 <?= lang("invoce_no", "poref"); ?>
								</div>
                               <div class="col-md-7">
                                <?php echo form_input('invoice_no', (isset($_POST['invoice_no']) ? $_POST['invoice_no'] : ""), 'class="form-control input-tip" id="invoice_no"'); ?>
								</div>
                            </div>
                        </div>
						
						<div class="col-md-6" style="padding-bottom: 10px;">
                            <div class="form-group">
                                <div class="col-md-5">
                                 <?= lang("invoce_date", "poref"); ?>
                                </div>
                                <div class="col-md-7">
                           
								<?php echo form_input('date', (isset($_POST['date']) ? $_POST['date'] : ""), 'class="form-control input-tip datetime" id="podate" required="required"'); ?>
								</div>
                            </div>
                        </div> -->
                        
                         <div class="col-md-6" style="padding-bottom: 10px;">
                        <div class="form-group all">
                            <div class="col-md-5">
                                 <?= lang("quote_order_no", "poref"); ?>
                            </div>

                            <div class="input-group col-md-7">
                           <?php echo form_input('quotation_order_no', (isset($_POST['quotation_order_no']) ? $_POST['quotation_order_no'] : $quotation->reference_no), 'class="form-control input-tip" id="quotation_order_no"'); ?>
                                <span class="input-group-addon pointer" id="random_num" style="padding: 1px 10px;">
                                    <i class="fa fa-random"></i>
                                </span>
                            </div>
                        <span class="help-block"><?= lang('you_scan_your_barcode_too') ?></span>
                        </div>
                    </div>
                    
                        <!-- <div class="col-md-6" style="padding-bottom: 10px;">
                            <div class="form-group">
                                <div class="col-md-5">
                                 <?= lang("quote_order_no", "poref"); ?>
								</div>
                               <div class="col-md-7">
                                <?php echo form_input('quotation_order_no', (isset($_POST['quotation_order_no']) ? $_POST['quotation_order_no'] : ""), 'class="form-control input-tip" id="quotation_order_no"'); ?>
								</div>
                            </div>
                        </div> -->
						
						<div class="col-md-6" style="padding-bottom: 10px;">
                            <div class="form-group">
                                <div class="col-md-5">
                                 <?= lang("quote_order_date", "poref"); ?>
								</div>
                               <div class="col-md-7">
                               <!-- <?php echo form_input('reference_no', (isset($_POST['reference_no']) ? $_POST['reference_no'] : $quotation->reference_no), 'class="form-control input-tip" id="poref" required="required"'); ?> -->

                                <?php echo form_input('quotation_order_date', (isset($_POST['quotation_order_date']) ? $_POST['quotation_order_date'] : $quotation->date), 'class="form-control input-tip totay_date" id="quotation_order_date"'); ?>
								</div>
                            </div>
                        </div>
						
						
						<div class="col-md-6" style="padding-bottom: 10px;">
							<div class="form-group">
								<div class="col-md-5">
								<?= lang("supplier", "posupplier"); ?>
								</div>
								<div class="col-md-7">
								<div class="input-group">
									<input type="hidden" name="supplier" value="" id="posupplier"
										   class="form-control" style="width:100%;"
										   placeholder="<?= lang("select") . ' ' . lang("supplier") ?>">

									<div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;">
										<a href="#" id="removeReadonly">
											<i class="fa fa-unlock" id="unLock"></i>
										</a>
									</div>
								</div>
								<input type="hidden" name="supplier_id" value="" id="supplier_id" class="form-control">
								</div>
							</div>
						</div>
						
						
                        <div class="col-md-6" style="padding-bottom: 10px;">
                            <div class="form-group">
								<div class="col-md-5">
                                <?= lang("warehouse", "powarehouse"); ?>
								</div>
								<div class="col-md-7">
                                <?php
                                $wh[''] = '';
                                foreach ($warehouses as $warehouse) {
                                    $wh[$warehouse->id] = $warehouse->name;
                                }
                                echo form_dropdown('warehouse', $wh, (isset($_POST['warehouse']) ? $_POST['warehouse'] : $quotation->warehouse_id), 'id="warehouse" class="form-control input-tip select" data-placeholder="' . $this->lang->line("select") . ' ' . $this->lang->line("warehouse") . '"  style="width:100%;" ');
                                ?>
								</div>
                            </div>
                        </div>
                        
                      
                        
                       
                        <div class="col-md-6" style="padding-bottom: 10px;">
                            <div class="form-group">
								<div class="col-md-5">
                                <?= lang("document", "document") ?>
								</div>
								<div class="col-md-7">
                                <input id="document" type="file" data-browse-label="<?= lang('browse'); ?>" name="document" data-show-upload="false"
                                       data-show-preview="false" class="form-control file">
								</div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="panel panel-warning">
                                <div
                                    class="panel-heading"><?= lang('please_select_before_add_product') ?></div>
                                <div class="panel-body" style="padding: 5px;">
                                    

                                </div>
                            </div>
                            <div class="clearfix"></div>
                        </div>

                        <div class="col-md-offset-2 col-md-8" id="sticker">
                            <div class="well well-sm">
                                <div class="form-group" style="margin-bottom:0;">
                                    <div class="input-group wide-tip">
                                        <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;">
                                            <i class="fa fa-2x fa-barcode addIcon"></i></a></div>
                                        <?php echo form_input('add_item', '', 'class="form-control input-lg" id="add_item" placeholder="' . $this->lang->line("add_product_to_order") . '"'); ?>
                                        <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;">
                                            <a href="#" id="addManually"><i class="fa fa-2x fa-plus-circle addIcon"
                                                                            id="addIcon"></i></a></div>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
						
						
						
						

                        <div class="col-md-12">
                            <div class="control-group table-group">
                                <label class="table-label"><?=lang("order_items"); ?></label>

                                <div class="controls table-controls">
                                    <table id="quotesTable"
                                            class="table items  table-bordered table-condensed sortable_table" style="background:#fff">
                                        <thead>
                                        <tr>
                                            <th class="col-md-4"><?= lang('product') . ' (' . lang('code') .' - '.lang('name') . ')'; ?></th>
											                                            
                                            <th class="col-md-1"><?= lang("unit_cost"); ?>(<span
                                                    class="currency"><?= $default_currency->code ?></span>)</th>
                                            <th class="col-md-1"><?= lang("quantity"); ?></th>
                                            
											
											<th class="col-md-2"><?= lang("tax_method"); ?></th>
                                              <th class="col-md-2"><?= lang("tax_type"); ?></th>
											  
                                            <?php
                                            if ($Settings->product_discount) {
                                                echo '<th class="col-md-1">' . $this->lang->line("discount") . '</th>';
                                            }
                                            ?>
											<th class="col-md-1"><?= lang("net_unit_cost"); ?></th>
                                            <?php
                                            if ($Settings->tax1) {
                                                echo '<th class="col-md-1">' . $this->lang->line("product_tax") . '</th>';
                                            }
                                            ?>
                                            <th><?= lang("subtotal"); ?> (<span
                                                    class="currency"><?= $default_currency->code ?></span>)
                                            </th>
                                            <th style="width: 30px !important; text-align: center;">
                                                <i class="fa fa-trash-o" style="opacity:0.5; filter:alpha(opacity=50);"></i>
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody></tbody>
                                        <tfoot></tfoot>
                                    </table>
                                </div>
                            </div>
							
							<table class="table table-bordered table-condensed totals" style="margin-bottom:0;">
                        
								<tr>
								<td colspan="4"></td>
								<td ><?= lang('items') ?> <span class="totals_val pull-right" id="titems">0</span></td>
								</tr>
								<tr>
								<td colspan="4"></td>
								<td><?= lang('total') ?> <span class="totals_val pull-right" id="total">0.00</span></td>
								</tr>
								<tr>
								<td colspan="4"></td>
								<td><?= lang('order_discount') ?> <span class="totals_val pull-right" id="tds">0.00</span></td>
								</tr>
								<tr>
								
								<?php if ($Settings->tax2) { ?>
								<td colspan="4"></td>
									<td><?= lang('order_tax') ?> <span class="totals_val pull-right" id="ttax2">0.00</span></td>
								<?php } ?>
								</tr>
								<tr>
								<td colspan="4"></td>
								<td><?= lang('shipping') ?> <span class="totals_val pull-right" id="tship">0.00</span></td>
								</tr>
								<tr>
								<td colspan="4"></td>
								<td class="total_top"><?= lang('grand_total') ?> <span class="totals_val pull-right" id="gtotal">0.00</span></td>
								</tr>
						   
						</table>
							
							
                        </div>
                        <div class="clearfix"></div>
                        <input type="hidden" name="total_items" value="" id="total_items" required="required"/>

                        <div class="col-md-12">
                            <div class="form-group">
                                <input type="checkbox" class="checkbox" id="extras" value=""/>
                                <label for="extras" class="padding05"><?= lang('more_options') ?></label>
                            </div>
                            <div class="row" id="extras-con" style="display: none;">
                                <?php if ($Settings->tax1) { ?>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <?= lang('order_tax', 'potax2') ?>
                                            <?php
                                            $tr[""] = "";
                                            foreach ($tax_rates as $tax) {
                                                $tr[$tax->id] = $tax->name;
                                            }
                                            echo form_dropdown('order_tax', $tr, "", 'id="potax2" class="form-control input-tip select" style="width:100%;"');
                                            ?>
                                        </div>
                                    </div>
                                <?php } ?>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <?= lang("discount", "podiscount"); ?>
                                        <?php echo form_input('discount', '', 'class="form-control input-tip" id="podiscount"'); ?>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <?= lang("shipping", "poshipping"); ?>
                                        <?php echo form_input('shipping', '', 'class="form-control input-tip" id="poshipping"'); ?>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <?= lang("payment_term", "popayment_term"); ?>
                                        <?php echo form_input('payment_term', '', 'class="form-control tip" data-trigger="focus" data-placement="top" title="' . lang('payment_term_tip') . '" id="popayment_term"'); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="form-group">
                                <?= lang("note", "ponote"); ?>
                                <?php echo form_textarea('note', (isset($_POST['note']) ? $_POST['note'] : ""), 'class="form-control" id="ponote" style="margin-top: 10px; height: 100px;"'); ?>
                            </div>

                        </div>
                        <div class="col-md-12">
                            <div
                                class="from-group"><?php echo form_submit('edit_quotation', $this->lang->line("submit"), 'id="edit_quotation" class="btn btn-primary" style="padding: 6px 15px; margin:15px 0;"'); ?>
                                <button type="button" class="btn btn-danger" id="reset"><?= lang('reset') ?></button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php /*<div id="bottom-total" class="well well-sm" style="margin-bottom: 0;">
                    <table class="table table-bordered table-condensed totals" style="margin-bottom:0;">
                        <tr class="warning">
                            <td><?= lang('items') ?> <span class="totals_val pull-right" id="titems">0</span></td>
                            <td><?= lang('total') ?> <span class="totals_val pull-right" id="total">0.00</span></td>
                            <td><?= lang('order_discount') ?> <span class="totals_val pull-right" id="tds">0.00</span></td>
                            <?php if ($Settings->tax2) { ?>
                                <td><?= lang('order_tax') ?> <span class="totals_val pull-right" id="ttax2">0.00</span></td>
                            <?php } ?>
                            <td><?= lang('shipping') ?> <span class="totals_val pull-right" id="tship">0.00</span></td>
                            <td><?= lang('grand_total') ?> <span class="totals_val pull-right" id="gtotal">0.00</span></td>
                        </tr>
                    </table>
                </div>*/?>

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
                    <div class="form-group">
                        <label for="pquantity" class="col-sm-4 control-label"><?= lang('quantity') ?></label>

                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="pquantity">
                        </div>
                    </div>
                    <?php if ($Settings->product_expiry) { ?>
                        <div class="form-group">
                            <label for="pexpiry" class="col-sm-4 control-label"><?= lang('product_expiry') ?></label>

                            <div class="col-sm-8">
                                <input type="text" class="form-control date" id="pexpiry">
                            </div>
                        </div>
                    <?php } ?>
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
                            <label for="pdiscount"
                                   class="col-sm-4 control-label"><?= lang('product_discount') ?></label>

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
