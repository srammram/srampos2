<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<script type="text/javascript">
    <?php if ($this->session->userdata('remove_pols')) { ?>
    if (localStorage.getItem('po_items')) {
        localStorage.removeItem('po_items');
    }
	if (localStorage.getItem('po_quatationnumber')) {
        localStorage.removeItem('po_quatationnumber');
    }
    if (localStorage.getItem('po_discount')) {
        localStorage.removeItem('po_discount');
    }
    if (localStorage.getItem('po_tax2')) {
        localStorage.removeItem('po_tax2');
    }
    if (localStorage.getItem('po_shipping')) {
        localStorage.removeItem('po_shipping');
    }
    if (localStorage.getItem('po_ref')) {
        localStorage.removeItem('po_ref');
    }
    if (localStorage.getItem('po_warehouse')) {
        localStorage.removeItem('po_warehouse');
    }
    if (localStorage.getItem('po_note')) {
        localStorage.removeItem('po_note');
    }
    if (localStorage.getItem('po_supplier')) {
        localStorage.removeItem('po_supplier');
    }
    if (localStorage.getItem('po_currency')) {
        localStorage.removeItem('po_currency');
    }
    if (localStorage.getItem('po_extras')) {
        localStorage.removeItem('po_extras');
    }
    if (localStorage.getItem('po_date')) {
        localStorage.removeItem('po_date');
    }
    if (localStorage.getItem('po_status')) {
        localStorage.removeItem('po_status');
    }
    if (localStorage.getItem('po_payment_term')) {
        localStorage.removeItem('po_payment_term');
    }
    <?php $this->sma->unset_data('remove_pols');
} ?>
    <?php if($purchases_order_id) { ?>
    localStorage.setItem('po_warehouse', '<?= $purchases_order->warehouse_id ?>');
	localStorage.setItem('po_quatationnumber', '<?= $purchases_order->po_quatationnumber ?>');
    localStorage.setItem('po_note', '<?= str_replace(array("\r", "\n"), "", $this->sma->decode_html($purchases_order->note)); ?>');
    localStorage.setItem('po_discount', '<?= $purchases_order->order_discount_id ?>');
    localStorage.setItem('po_tax2', '<?= $purchases_order->order_tax_id ?>');
    localStorage.setItem('po_shipping', '<?= $purchases_order->shipping ?>');
    <?php if ($purchases_order->supplier_id) { ?>
        localStorage.setItem('po_supplier', '<?= $purchases_order->supplier_id ?>');
    <?php } ?>
    localStorage.setItem('po_items', JSON.stringify(<?= $quote_items; ?>));
    <?php } ?>

    var count = 1, an = 1, purchases_order_edit = false, product_variant = 0, DT = <?= $Settings->default_tax_rate ?>, DC = '<?= $default_currency->code ?>', shipping = 0,
        product_tax = 0, invoice_tax = 0, total_discount = 0, total = 0,
        tax_rates = <?php echo json_encode($tax_rates); ?>, po_items = {},
        audio_success = new Audio('<?= $assets ?>sounds/sound2.mp3'),
        audio_error = new Audio('<?= $assets ?>sounds/sound3.mp3');
    $(document).ready(function () {
        <?php if($this->input->get('supplier')) { ?>
        if (!localStorage.getItem('po_items')) {
            localStorage.setItem('po_supplier', <?=$this->input->get('supplier');?>);
        }
        <?php } ?>
        <?php if ($Owner || $Admin) { ?>
        if (!localStorage.getItem('po_date')) {
            $("#podate").datetimepicker({
                format: site.dateFormats.js_ldate,
                fontAwesome: true,
                language: 'sma',
                weekStart: 1,
                todayBtn: 1,
                autoclose: 1,
                todayHighlight: 1,
                startView: 2,
                forceParse: 0
            }).datetimepicker('update', new Date());
        }
        $(document).on('change', '#podate', function (e) {
            localStorage.setItem('po_date', $(this).val());
        });
        if (podate = localStorage.getItem('po_date')) {
            $('#podate').val(podate);
        }
		$("#po_quatationnumber").val(localStorage.getItem('po_quatationnumber'));
		
		if (!localStorage.getItem('iodate')) {
            $("#iodate").datetimepicker({
                format: site.dateFormats.js_ldate,
                fontAwesome: true,
                language: 'sma',
                weekStart: 1,
                todayBtn: 1,
                autoclose: 1,
                todayHighlight: 1,
                startView: 2,
                forceParse: 0
            }).datetimepicker('update', new Date());
        }
        $(document).on('change', '#iodate', function (e) {
            localStorage.setItem('iodate', $(this).val());
        });
        if (iodate = localStorage.getItem('iodate')) {
            $('#iodate').val(iodate);
        }
        <?php } ?>
        if (!localStorage.getItem('po_tax2')) {
            localStorage.setItem('po_tax2', <?=$Settings->default_tax_rate2;?>);
            setTimeout(function(){ $('#extras').iCheck('check'); }, 1000);
        }
        ItemnTotals();
        $("#add_item").autocomplete({
            // source: '<?= admin_url('purchases_order/suggestions'); ?>',
            source: function (request, response) {
                $.ajax({
                    type: 'get',
                    url: '<?= admin_url('purchases_order/suggestions'); ?>',
                    dataType: "json",
                    data: {
                        term: request.term,
                        supplier_id: $("#posupplier").val()
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
                    //audio_error.play();
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
                    //audio_error.play();
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
                    var row = add_purchase_order_item(ui.item);
                    if (row)
                        $(this).val('');
                } else {
                    //audio_error.play();
                    bootbox.alert('<?= lang('no_match_found') ?>');
                }
            }
        });

        $(document).on('click', '#addItemManually', function (e) {
			
            if (!$('#mcode').val()) {
                $('#mError').text('<?= lang('product_code_is_required') ?>');
                $('#mError-con').show();
                return false;
            }
            if (!$('#mname').val()) {
                $('#mError').text('<?= lang('product_name_is_required') ?>');
                $('#mError-con').show();
                return false;
            }
            if (!$('#mcategory').val()) {
                $('#mError').text('<?= lang('product_category_is_required') ?>');
                $('#mError-con').show();
                return false;
            }
            if (!$('#munit').val()) {
                $('#mError').text('<?= lang('product_unit_is_required') ?>');
                $('#mError-con').show();
                return false;
            }
            if (!$('#mcost').val()) {
                $('#mError').text('<?= lang('product_cost_is_required') ?>');
                $('#mError-con').show();
                return false;
            }
            if (!$('#mprice').val()) {
                $('#mError').text('<?= lang('product_price_is_required') ?>');
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
                        row = add_purchase_order_item(data.result);
                    } else {
                        msg = data.msg;
                    }
                }
            });
            if (row) {
                $('#mModal').modal('hide');
                //audio_success.play();
            } else {
                $('#mError').text(msg);
                $('#mError-con').show();
            }
            return false;

        });
    });

</script>

<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('add_purchases_order'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?php echo lang('enter_info'); ?></p>
                <?php
                $attrib = array('data-toggle' => 'validator', 'role' => 'form');
                echo admin_form_open_multipart("purchases_order/add", $attrib)
                ?>


                <div class="row">
                    <div class="col-lg-12">
						

						<!--<div class="col-md-6" style="padding-bottom: 10px;">
                            <div class="form-group">
                                <div class="col-md-5">
                                 
								</div>
                               <div class="col-md-7">
                                <?php// echo form_input('purchases_order_details', (isset($_POST['purchases_order_details']) ? $_POST['purchases_order_details'] : ""), 'class="form-control input-tip" id="purchases_order_details"'); ?>
								</div>
                            </div>
                        </div>-->
                        
                        <div class="col-md-3" style="padding-bottom: 10px;">
                        <div class="form-group all">
                            <div class="col-md-12 row">
                                 <?= lang("po_number", "poref"); ?>
                            </div>

                            <div class="input-group col-md-12">
                            <?php echo form_input('purchases_order_no', (isset($_POST['purchases_order_no']) ? $_POST['purchases_order_no'] : ""), 'class="form-control input-tip" id="purchases_order_no"'); ?>
                                <span class="input-group-addon pointer" id="random_num" style="padding: 1px 10px;">
                                    <i class="fa fa-random"></i>
                                </span>
                            </div>
                        <!-- <span class="help-block"><?= lang('you_scan_your_barcode_too') ?></span> -->
                        </div>
                    </div>
                    
                   <!-- <div class="col-md-6" style="padding-bottom: 10px;">
                                <div class="form-group">
                                   <div class="col-md-5">
                                    <?= lang("quatation_number", "po_quatationnumber"); ?>
									</div>
                                   <div class="col-md-7">
                                   
                                    <?php
								
                                   $qn[''] = '';
                                    foreach ($quatationnumber as $quatationnumber_row) {
                                        $qn[$quatationnumber_row->id] = $quatationnumber_row->reference_no;
                                    }
                                    echo form_dropdown('quatationnumber', $qn, (isset($_POST['quatationnumber']) ? $_POST['quatationnumber'] : '' ), ' class="form-control input-tip select" data-placeholder="' . lang("select") . ' ' . lang("quatation_number") . '"style="width:100%;" id="po_quatationnumber" required="required" ');
                                    ?>
									</div>
                                </div>
                            </div>-->
                            
                        <!-- <div class="col-md-6" style="padding-bottom: 10px;">
                            <div class="form-group">
                                <div class="col-md-5">
                                 <?= lang("po_number", "poref"); ?>
								</div>
                               <div class="col-md-7">
                                <?php echo form_input('purchases_order_no', (isset($_POST['purchases_order_no']) ? $_POST['purchases_order_no'] : ""), 'class="form-control input-tip" id="purchases_order_no"'); ?>
								</div>
                            </div>
                        </div> -->
						
						<div class="col-md-3" style="padding-bottom: 10px;">
                            <div class="form-group">
                                <div class="col-md-12">
                                 <?= lang("po_date", "poref"); ?>
								</div>
                               <div class="col-md-12">
                                <?php 
								$current_date = date('d-m-Y');
								echo form_input('purchases_order_date', $current_date, 'class="form-control input-tip " readonly id="purchases_order_date"'); ?>
								</div>
                            </div>
                        </div>
                        
                        <div class="col-md-3" style="padding-bottom: 10px;">
                            <div class="form-group">
                                <div class="col-md-12">
                                 <?= lang("expected_date_of_goods", "poref"); ?>
                                </div>
                                <div class="col-md-12">
								<?php echo form_input('purchases_order_expected_date', (isset($_POST['purchases_order_expected_date']) ? $_POST['purchases_order_expected_date'] : ""), 'class="form-control input-tip totay_date" id="purchases_order_expected_date"'); ?>
								</div>
                            </div>
                        </div>
                        
                        
                        
						<div class="col-md-3" style="padding-bottom: 10px;">
                                        <div class="form-group">
                                           <div class="col-md-12">
                                            <?= lang("supplier", "posupplier"); ?>
											</div>
                                           <div class="col-md-12">
                                            <?php if ($Owner || $Admin || $GP['suppliers-add'] || $GP['suppliers-index']) { ?><div class="input-group"><?php } ?>
                                                <input type="hidden" name="supplier" value="" id="posupplier"
                                                       class="form-control" style="width:100%;"
                                                       placeholder="<?= lang("select") . ' ' . lang("supplier") ?>">
                                                <input type="hidden" name="supplier_id" value="" id="supplier_id"
                                                       class="form-control">
                                                      
                                                <?php if ($Owner || $Admin || $GP['suppliers-index']) { ?>
                                                    <div class="input-group-addon no-print" style="padding: 2px 5px; border-left: 0;">
                                                        <a href="#" id="view-supplier" class="external" data-toggle="modal" data-target="#myModal">
                                                            <i class="fa fa-2x fa-user" id="addIcon"></i>
                                                        </a>
                                                    </div>
                                                <?php } ?>
                                                <?php if ($Owner || $Admin || $GP['suppliers-add']) { ?>
                                                <div class="input-group-addon no-print" style="padding: 2px 5px;">
                                                    <a href="<?= admin_url('suppliers/add'); ?>" id="add-supplier" class="external" data-toggle="modal" data-target="#myModal">
                                                        <i class="fa fa-2x fa-plus-square" id="addIcon"></i>
                                                    </a>
                                                </div>
                                            <?php } ?>
                                            <?php if ($Owner || $Admin || $GP['suppliers-add'] || $GP['suppliers-index']) { ?></div><?php } ?>
											</div>
                                        </div>
                                    </div>
                              
                          <div class="clearfix"></div>	
                          <div class="col-xs-12">
                          <div class="well col-xs-12">	
                          
                          <input type="hidden" disabled name="supplier_vatno" id="supplier_vatno" class="form-control">
                          <input type="hidden" disabled name="supplier_email" id="supplier_email" class="form-control">
                          
                           <div class="col-md-3" style="padding-bottom: 10px;">
                                <div class="form-group">
                                    <div class="col-md-12">
                                     <?= lang("supplier_name", "poref"); ?>
                                    </div>
                                    <div class="col-md-12">
                                    <input type="text" disabled name="supplier_name" id="supplier_name" class="form-control">
                                    </div>
                                </div>
                            </div>
                             <div class="col-md-3" style="padding-bottom: 10px;">
                                <div class="form-group">
                                    <div class="col-md-12">
                                     <?= lang("code", "poref"); ?>
                                    </div>
                                    <div class="col-md-12">
                                   <input type="text" disabled name="supplier_code" id="supplier_code" class="form-control">
                                    </div>
                                </div>
                            </div>
                             <div class="col-md-3" style="padding-bottom: 10px;">
                                <div class="form-group">
                                    <div class="col-md-12">
                                     <?= lang("address", "poref"); ?>
                                    </div>
                                    <div class="col-md-12">
                                    <input type="text" disabled name="supplier_address" id="supplier_address" class="form-control">
                                    </div>
                                </div>
                            </div>
                             <!--<div class="col-md-6" style="padding-bottom: 10px;">
                                <div class="form-group">
                                    <div class="col-md-5">
                                     <?= lang("vat_no", "poref"); ?>
                                    </div>
                                    <div class="col-md-7">
                                    <input type="text" disabled name="supplier_vatno" id="supplier_vatno" class="form-control">
                                    </div>
                                </div>
                            </div>-->
                            <!--<div class="col-md-6" style="padding-bottom: 10px;">
                                <div class="form-group">
                                    <div class="col-md-5">
                                     <?= lang("email ", "poref"); ?>
                                    </div>
                                    <div class="col-md-7">
                                    <input type="text" disabled name="supplier_email" id="supplier_email" class="form-control">
                                    </div>
                                </div>
                            </div>-->
                            <div class="col-md-3" style="padding-bottom: 10px;">
                                <div class="form-group">
                                    <div class="col-md-12">
                                     <?= lang("phone", "poref"); ?>
                                    </div>
                                    <div class="col-md-12">
                                    <input type="text" disabled name="supplier_phno" id="supplier_phno" class="form-control">
                                    </div>
                                </div>
                            </div>
                         </div>
                         </div>
						<div class="clearfix"></div>			
                        
                        <?php if ($Owner || $Admin || !$this->session->userdata('warehouse_id')) { ?>
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
                                    echo form_dropdown('warehouse', $wh, (isset($_POST['warehouse']) ? $_POST['warehouse'] : $Settings->default_warehouse), 'id="powarehouse" class="form-control input-tip select" data-placeholder="' . lang("select") . ' ' . lang("warehouse") . '" required="required" style="width:100%;" ');
                                    ?>
									</div>
                                </div>
                            </div>
                        <?php } else {
                            $warehouse_input = array(
                                'type' => 'hidden',
                                'name' => 'warehouse',
                                'id' => 'slwarehouse',
                                'value' => $this->session->userdata('warehouse_id'),
                            );

                            echo form_input($warehouse_input);
                        } ?>
						
						
						

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
<div class="clearfix"></div>

<hr>
<div class="clearfix"></div>
                       <!-- <div class="col-md-12">
                            <div class="panel panel-warning">
                                <div
                                    class="panel-heading"><?= lang('please_select_these_before_adding_Purchase Items') ?></div>
                                <div class="panel-body" style="padding: 5px;">
                                    

                                </div>
                            </div>
                            <div class="clearfix"></div>
                        </div>-->


                        <div class="col-md-offset-2 col-md-8" id="sticker">
                            <div class="well well-sm">
                               <h3>Add Purchase Items to Order list</h3>
                                <div class="form-group" style="margin-bottom:0;">
                                    <div class="input-group wide-tip">
                                        <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;">
                                            <i class="fa fa-2x fa-barcode addIcon"></i></a></div>
                                        <?php echo form_input('add_item', '', 'class="form-control input-lg" id="add_item" placeholder="' . $this->lang->line("add_Purchase Items_to_order") . '"'); ?>
                                        <?php if ($Owner || $Admin || $GP['products-add']) { ?>
                                        <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;">
                                            <a href="<?= admin_url('products/add') ?>" id="addManually1"><i
                                                    class="fa fa-2x fa-plus addIcon" id="addIcon"></i></a></div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="control-group table-group">
                                <label class="table-label"><?= lang("product"); ?></label>

                                <div class="controls table-controls">
                                    <table id="purchasesorderTable"
                                            class="table items  table-bordered table-condensed sortable_table" style="background:#fff">
                                        <thead>
                                        <tr>
                                            <th width="130"><?= lang('code'); ?></th>
                                            <th width="150"><?= lang('name'); ?></th>
											<th class="col-md-1"><?= lang("unit_cost"); ?></th>
                                            <th class="col-md-1"><?= lang("quantity"); ?></th>
                                             
                                            <th class="col-md-1"><?= lang("discount"); ?></th> 
                                            <th class="col-md-1"><?= lang("discount_amount"); ?></th> 
                                            
                                            <th class="col-md-1"><?= lang("tax_method"); ?></th>
                                            <th class="col-md-1"><?= lang("tax"); ?></th>
                                            <th class="col-md-1"><?= lang("tax_amount"); ?></th>
                                            <th class="col-md-1"><?= lang("landed_cost"); ?></th>
                                            <th><?= lang("subtotal"); ?> (<span
                                                    class="currency"><?= $default_currency->symbol ?></span>)
                                            </th>
                                              
                                           
											
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
							
								<!--<div id="bottom-total" class="well well-sm" style="margin-bottom: 0;">-->
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
                            <td><?= lang('discount') ?> <span class="totals_val pull-right" id="tds">0.00</span></td>
							</tr>
							<tr>
							<td colspan="4"></td>
                            <td><?= lang('cash_discount') ?> <span class="totals_val pull-right" id="cashtds">0.00</span></td>
							</tr>
							<tr>
							
                            <?php if ($Settings->tax2) { ?>
							<td colspan="4"></td>
                                <td><?= lang('tax') ?> <span class="totals_val pull-right" id="ttax2">0.00</span></td>
                            <?php } ?>
							</tr>
							<tr>
							<td colspan="4"></td>
                            <td><?= lang('shipping_charges') ?> <span class="totals_val pull-right" id="tship">0.00</span></td>
							</tr>
							<tr>
							<td colspan="4"></td>
                            <td class="total_top"><?= lang('grand_total') ?> <span class="totals_val pull-right" id="gtotal">0.00</span></td>
							</tr>
                       
                    </table>
                <!--</div>-->
				
                        </div>
                        <div class="clearfix"></div>
                        <input type="hidden" name="total_items" value="" id="total_items" required="required"/>

                        <div class="col-md-12">
                        	<hr>
                            <div class="clerafix"></div>
                            <!--<div class="form-group">
                                <input type="checkbox" class="checkbox" id="extras" value=""/>
                                <label for="extras" class="padding05"><?= lang('more_options') ?></label>
                            </div>-->
                            <div class="row" id="extras-con">
                                <?php if ($Settings->tax1) { ?>
                                    <!--<div class="col-md-6" style="padding-bottom: 10px;">
                                        <div class="form-group">
                                           <div class="col-md-5">
                                            <?= lang('order_tax', 'potax2') ?>
											</div>
                                           <div class="col-md-7">
                                            <?php
                                            $tr[""] = "";
                                            foreach ($tax_rates as $tax) {
                                                $tr[$tax->id] = $tax->name;
                                            }
                                            echo form_dropdown('order_tax', $tr, "", 'id="potax2" class="form-control input-tip select" style="width:100%;"');
                                            ?>
											</div>
                                        </div>
                                    </div>-->
                                <?php } ?>

                                <div class="col-md-6" style="padding-bottom: 10px;">
                                    <div class="form-group">
                                       <div class="col-md-5">
                                        <?= lang("Cash_discount", "podiscount"); ?>
										</div>
                                       <div class="col-md-7">
                                        <?php echo form_input('discount', '', 'class="form-control input-tip kb-pad" id="podiscount" maxlength="15"'); ?>
										</div>
                                    </div>
                                </div>

                                <div class="col-md-6" style="padding-bottom: 10px;">
                                    <div class="form-group">
                                       <div class="col-md-5">
                                        <?= lang("shipping_charges", "poshipping"); ?>
										</div>
                                       <div class="col-md-7">
                                        <?php echo form_input('shipping', '', 'class="form-control input-tip" id="poshipping"'); ?>
										</div>
                                    </div>
                                </div>

                                
                            </div>
                            <div class="clearfix"></div>
                            <!--<div class="form-group">
                                <?= lang("note", "ponote"); ?>
                                <?php echo form_textarea('note', (isset($_POST['note']) ? $_POST['note'] : ""), 'class="form-control" id="ponote" style="margin-top: 10px; height: 100px;"'); ?>
                            </div>-->

                        </div>
                        <div class="col-md-12">
                            <div
                                class="from-group"><?php echo form_submit('add_purchases_order', $this->lang->line("submit"), 'id="add_purchases_order" class="btn btn-primary" style="height: 45px !important;
font-size: 16px;
font-weight: bold;" '); ?>
                                <button type="button" class="btn btn-warning" id="reset"><?= lang('reset') ?></button>
                            </div>
                        </div>
                    </div>
                </div>
                

                <?php echo form_close(); ?>

            </div>

        </div>
    </div>
</div>

<script>
$(document).ready(function(e) {
    var supplierid = localStorage.getItem('po_supplier');
	if(posupplier != ''){
		$.ajax({
			type: 'get',
			url: '<?= admin_url('purchases_order/supplier'); ?>',
			dataType: "json",
			data: {	supplier_id: supplierid	},
			success: function (data) {
				$(this).removeClass('ui-autocomplete-loading');
				$('#supplier_name').val(data.supplier_name);
				$('#supplier_code').val(data.supplier_code);
				$('#supplier_vatno').val(data.supplier_vatno);
				$('#supplier_address').val(data.supplier_address);
				$('#supplier_email').val(data.supplier_email);
				$('#supplier_phno').val(data.supplier_phno);
			}
		});
	}
});


$(document).on('change', '#posupplier', function(){
	var posupplier = $(this).val();
	$.ajax({
		type: 'get',
		url: '<?= admin_url('purchases_order/supplier'); ?>',
		dataType: "json",
		data: {
			supplier_id: posupplier
		},
		success: function (data) {
			$(this).removeClass('ui-autocomplete-loading');
			$('#supplier_name').val(data.supplier_name);
			$('#supplier_code').val(data.supplier_code);
			$('#supplier_vatno').val(data.supplier_vatno);
			$('#supplier_address').val(data.supplier_address);
			$('#supplier_email').val(data.supplier_email);
			$('#supplier_phno').val(data.supplier_phno);
		}
	});
});


$(document).on('change', '#po_quatationnumber', function(){
	
	if (localStorage.getItem('po_items')) {
        localStorage.removeItem('po_items');
    }
	if (localStorage.getItem('po_quatationnumber')) {
        localStorage.removeItem('po_quatationnumber');
    }
    if (localStorage.getItem('po_discount')) {
        localStorage.removeItem('po_discount');
    }
    if (localStorage.getItem('po_tax2')) {
        localStorage.removeItem('po_tax2');
    }
    if (localStorage.getItem('po_shipping')) {
        localStorage.removeItem('po_shipping');
    }
    if (localStorage.getItem('po_ref')) {
        localStorage.removeItem('po_ref');
    }
    if (localStorage.getItem('po_warehouse')) {
        localStorage.removeItem('po_warehouse');
    }
    if (localStorage.getItem('po_note')) {
        localStorage.removeItem('po_note');
    }
    if (localStorage.getItem('po_supplier')) {
        localStorage.removeItem('po_supplier');
    }
    if (localStorage.getItem('po_currency')) {
        localStorage.removeItem('po_currency');
    }
    if (localStorage.getItem('po_extras')) {
        localStorage.removeItem('po_extras');
    }
    if (localStorage.getItem('po_date')) {
        localStorage.removeItem('po_date');
    }
    if (localStorage.getItem('po_status')) {
        localStorage.removeItem('po_status');
    }
    if (localStorage.getItem('po_payment_term')) {
        localStorage.removeItem('po_payment_term');
    }
	
	var po_quatationnumber = $(this).val();
	$.ajax({
		type: 'get',
		url: '<?= admin_url('purchases_order/quotes_list'); ?>',
		dataType: "json",
		data: {
			poref: po_quatationnumber
		},
		success: function (data) {
			
			var purchases_value = [];
			$(this).removeClass('ui-autocomplete-loading');
			var items = JSON.stringify(data.value['purchasesitem']);
			
			var purchases = JSON.stringify(data.value['purchases']);
			purchases_value = $.parseJSON(purchases);

			localStorage.setItem('po_quatationnumber',  purchases_value["id"]);
			localStorage.setItem('po_warehouse', purchases_value["warehouse_id"]);
			localStorage.setItem('po_note', purchases_value["note"]);
			localStorage.setItem('po_discount', purchases_value["order_discount_id"]);
			localStorage.setItem('po_tax2', purchases_value["order_tax_id"]);
			localStorage.setItem('po_shipping', purchases_value["shipping"]);
			localStorage.setItem('po_supplier', purchases_value["customer_id"]);
			localStorage.setItem('po_items', items);
			localStorage.setItem('purchases_order_date', purchases_value["purchases_order_date"]);
			
			location.reload();
			
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
