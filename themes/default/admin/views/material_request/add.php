<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<script type="text/javascript">
    <?php if ($this->session->userdata('remove_pols')) { ?>
    if (localStorage.getItem('materialrequestitems')) {
            localStorage.removeItem('materialrequestitems');
        }
        if (localStorage.getItem('materialrequestdiscount')) {
            localStorage.removeItem('materialrequestdiscount');
        }
        if (localStorage.getItem('materialrequesttax2')) {
            localStorage.removeItem('materialrequesttax2');
        }
        if (localStorage.getItem('materialrequestshipping')) {
            localStorage.removeItem('materialrequestshipping');
        }
        if (localStorage.getItem('materialrequestref')) {
            localStorage.removeItem('materialrequestref');
        }
        if (localStorage.getItem('materialrequestwarehouse')) {
            localStorage.removeItem('materialrequestwarehouse');
        }
        if (localStorage.getItem('materialrequestnote')) {
            localStorage.removeItem('materialrequestnote');
        }
        if (localStorage.getItem('materialrequestsupplier')) {
            localStorage.removeItem('materialrequestsupplier');
        }
        if (localStorage.getItem('materialrequestcurrency')) {
            localStorage.removeItem('materialrequestcurrency');
        }
        if (localStorage.getItem('materialrequestextras')) {
            localStorage.removeItem('materialrequestextras');
        }
        if (localStorage.getItem('materialrequestdate')) {
            localStorage.removeItem('materialrequestdate');
        }
        if (localStorage.getItem('materialrequeststatus')) {
            localStorage.removeItem('materialrequeststatus');
        }
        if (localStorage.getItem('materialrequestpayment_term')) {
            localStorage.removeItem('materialrequestpayment_term');
        }
    <?php $this->sma->unset_data('remove_pols');
} ?>
    <?php if($material_request_id) { ?>
    localStorage.setItem('materialrequestwarehouse', '<?= $materialrequest->warehouse_id ?>');
    localStorage.setItem('materialrequestnote', '<?= str_replace(array("\r", "\n"), "", $this->sma->decode_html($materialrequest->note)); ?>');
    localStorage.setItem('materialrequestdiscount', '<?= $materialrequest->order_discount_id ?>');
    localStorage.setItem('materialrequesttax2', '<?= $materialrequest->order_tax_id ?>');
    localStorage.setItem('materialrequestshipping', '<?= $materialrequest->shipping ?>');
    /*<?php if ($quote->supplier_id) { ?>
        localStorage.setItem('quotesupplier', '<?= $materialrequest->supplier_id ?>');
    <?php } ?>*/
    localStorage.setItem('materialrequestitems', JSON.stringify(<?= $materialrequestitems; ?>));
    <?php } ?>

    var count = 1, an = 1, po_edit = false, product_variant = 0, DT = <?= $Settings->default_tax_rate ?>, DC = '<?= $default_currency->code ?>', shipping = 0,
        product_tax = 0, invoice_tax = 0, total_discount = 0, total = 0,
        tax_rates = <?php echo json_encode($tax_rates); ?>, materialrequestitems = {},
        audio_success = new Audio('<?= $assets ?>sounds/sound2.mp3'),
        audio_error = new Audio('<?= $assets ?>sounds/sound3.mp3');
    $(document).ready(function () {
        <?php if($this->input->get('supplier')) { ?>
        if (!localStorage.getItem('materialrequestitems')) {
            localStorage.setItem('quotesupplier', <?=$this->input->get('supplier');?>);
        }
        <?php } ?>
        <?php if ($Owner || $Admin) { ?>
        if (!localStorage.getItem('materialrequestdate')) {
            $("#materialrequestdate").datetimepicker({
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
        $(document).on('change', '#materialrequestdate', function (e) {
            localStorage.setItem('materialrequestdate', $(this).val());
        });
        if (podate = localStorage.getItem('materialrequestdate')) {
            $('#materialrequestdate').val(podate);
        }
		
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
        if (!localStorage.getItem('materialrequesttax2')) {
            localStorage.setItem('materialrequesttax2', <?=$Settings->default_tax_rate2;?>);
            setTimeout(function(){ $('#extras').iCheck('check'); }, 1000);
        }
        ItemnTotals();
        $("#add_item").autocomplete({
            /*source: '<?= admin_url('material_request/suggestions'); ?>',*/
            source: function (request, response) {
                $.ajax({
                    type: 'get',
                    url: '<?= admin_url('material_request/suggestions'); ?>',
                    dataType: "json",
                    data: {
                        term: request.term
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
                    /*audio_error.play();*/
                    bootbox.alert('<?= lang('no_match_found') ?>', function () {
                        /*$('#add_item').focus();*/
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
                    /*audio_error.play();*/
                    bootbox.alert('<?= lang('no_match_found') ?>', function () {
                        /*$('#add_item').focus();*/
                    });
                    $(this).removeClass('ui-autocomplete-loading');
                    $(this).val('');
                }
            },
            select: function (event, ui) {
                event.preventDefault();
                if (ui.item.id !== 0) {                                    
                    var row = add_material_request_item(ui.item);
                    if (row)
                        $(this).val('');
                } else {
                    //audio_error.play();
                    bootbox.alert('<?= lang('no_match_found') ?>');
                }
            }
        });

        $(document).on('click', '#addItemManually', function (e) {
			alert('hi');
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
                        row = add_material_request_item(data.result);
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
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('add_material_request'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?php echo lang('enter_info'); ?></p>
                <?php
                $attrib = array('data-toggle' => 'validator', 'role' => 'form');
                echo admin_form_open_multipart("material_request/add", $attrib)
                ?>


                <div class="row">
                    <div class="col-lg-12">
						
                        <div class="form-group" style="display:none;">
                            
                                <?php
                                $tr[""] = "";
                                foreach ($tax_rates as $tax) {
                                    $tr[$tax->id] = $tax->name;
                                }
                                echo form_dropdown('ptax[]', $tr, "", 'class="form-control product_tax" style="width:100%;"');
                                ?>
                            	
                        </div>
                        
                        <div class="col-md-6" style="padding-bottom: 10px;">
                        <div class="all">
                            <div class="col-md-5">
                                <?= lang("material_request_no", "poref"); ?>
                            </div>
                           <div class="input-group col-md-7">
                            <?php echo form_input('material_request_no', (isset($_POST['material_request_no']) ? $_POST['material_request_no'] : ""), 'class="form-control input-tip" id="material_request_no"  required="required" '); ?>
                            <span class="input-group-addon pointer" id="random_num" style="padding: 1px 10px;">
                                <i class="fa fa-random"></i>
                                </span>
                            </div>
                        </div>
                    </div>
					<div class="col-md-6" style="padding-bottom: 10px;">
                        <div class="form-group all">
                            <div class="col-md-5">
                             <?= lang("material_request_date", "poref"); ?>
							</div>
                            <div class="col-md-7">
                                <?php echo form_input('material_request_date', (isset($_POST['material_request_date']) ? $_POST['material_request_date'] : ""), 'class="form-control input-tip totay_date" id="material_request_date" required="required" '); ?>
								</div>
                            </div>
                        </div>
                        
                        <?php if ($Owner || $Admin || !$this->session->userdata('warehouse_id')) { ?>
                            <div class="col-md-6" style="padding-bottom: 10px;">
                                <div class="form-group all">
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
                                <div class="form-group all">
                                   <div class="col-md-5">
                                    <?= lang("to_warehouse", "to_warehouse"); ?>
                                    </div>
                                   <div class="col-md-7">
                                    <?php
                                    $wh[''] = '';
                                    foreach ($warehouses as $warehouse) {
                                        $wh[$warehouse->id] = $warehouse->name;
                                    }
                                    echo form_dropdown('to_warehouse', $wh, (isset($_POST['to_warehouse']) ? $_POST['to_warehouse'] : 0), 'id="to_warehouse" class="form-control input-tip select" data-placeholder="' . lang("select") . ' ' . lang("to_warehouse") . '" required="required" style="width:100%;" ');
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
                                    class="panel-heading"><?= lang('please_select_these_before_adding_purchase_items') ?></div>
                                <div class="panel-body" style="padding: 5px;">
                                    

                                </div>
                            </div>
                            <div class="clearfix"></div>
                        </div>


                        <div class="col-md-offset-2 col-md-8" id="sticker">
                            <div class="well well-sm">
                               <h3>Add Purchase Items to Order list</h3>
                                <div class="form-group" style="margin-bottom:0;">
                                    <div class="input-group wide-tip">
                                        <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;">
                                            <i class="fa fa-2x fa-barcode addIcon"></i></a></div>
                                        <?php echo form_input('add_item', '', 'class="form-control input-lg" id="add_item" placeholder="' . $this->lang->line("add_purchase_items_to_order") . '"'); ?>
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
                                <label class="table-label"><?= lang("order_items"); ?></label>

                                <div class="controls table-controls">
                                    <table id="material_request_Table"
                                            class="table items  table-bordered table-condensed sortable_table" style="background:#fff">
                                        <thead>
                                        <tr>
                                            <th width="200"><?= lang('purchase_items') . ' (' . lang('code') .' - '.lang('name') . ')'; ?></th>
											 <?php
                                            if ($Settings->product_expiry) {
                                                echo '<th class="col-md-2">' . $this->lang->line("expiry_date") . '</th>';
                                            }
                                            ?>
                                            <th class="col-md-1"><?= lang("net_unit_cost"); ?>
                                            </th>

                                            <th class="col-md-1"><?= lang("quantity"); ?></th>
											<?php
                                            if ($Settings->tax1) {
                                                echo '<th class="col-md-1">' . $this->lang->line("product_tax") . '</th>';
                                            }
                                            ?>
                                            <th><?= lang("subtotal"); ?> (<span
                                                    class="currency"><?= $default_currency->code ?></span>)
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
					
                    <div class="clearfix"></div>
                        <input type="hidden" name="total_items" value="" id="total_items" required="required"/>
                        <div class="col-md-12">
                            <div
                                class="from-group"><?php echo form_submit('add_material_request', $this->lang->line("submit"), 'id="add_material_request" class="btn btn-primary" style="padding: 6px 15px; margin:15px 0;"'); ?>
                                <button type="button" class="btn btn-danger" id="reset"><?= lang('reset') ?></button>
                            </div>
                        </div>
                        		
					<div id="bottom-total" class="well well-sm" style="margin-bottom: 0;">
                    <table class="table table-bordered table-condensed totals" style="margin-bottom:0;">
                        <tr class="warning">
                            <td><?= lang('items') ?> <span class="totals_val pull-right" id="titems">0</span></td>
                            <td><?= lang('total') ?> <span class="totals_val pull-right" id="total">0.00</span></td>
                            <td><?= lang('shipping') ?> <span class="totals_val pull-right" id="tship">0.00</span></td>
                            <td><?= lang('grand_total') ?> <span class="totals_val pull-right" id="gtotal">0.00</span>
                            </td>
                        </tr>
                    </table>
                </div>

                <!--  <div class="col-md-12">
                            <div class="form-group">
                                <input type="checkbox" class="checkbox" id="extras" value=""/>
                                <label for="extras" class="padding05"><?= lang('more_options') ?></label>
                            </div>
                            <div class="row" id="extras-con" style="display: none;">
                                <?php if ($Settings->tax1) { ?>
                                    <div class="col-md-6" style="padding-bottom: 10px;">
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
                                    </div>
                                <?php } ?>

                                <div class="col-md-6" style="padding-bottom: 10px;">
                                    <div class="form-group">
                                       <div class="col-md-5">
                                        <?= lang("discount", "podiscount"); ?>
										</div>
                                       <div class="col-md-7">
                                        <?php echo form_input('discount', '', 'class="form-control input-tip" id="podiscount"'); ?>
										</div>
                                    </div>
                                </div>

                                <div class="col-md-6" style="padding-bottom: 10px;">
                                    <div class="form-group">
                                       <div class="col-md-5">
                                        <?= lang("shipping", "poshipping"); ?>
										</div>
                                       <div class="col-md-7">
                                        <?php echo form_input('shipping', '', 'class="form-control input-tip" id="poshipping"'); ?>
										</div>
                                    </div>
                                </div>

                                <div class="col-md-6" style="padding-bottom: 10px;">
                                    <div class="form-group">
                                       <div class="col-md-5">
                                        <?= lang("payment_term", "popayment_term"); ?>
										</div>
                                       <div class="col-md-7">
                                        <?php echo form_input('payment_term', '', 'class="form-control tip" data-trigger="focus" data-placement="top" title="' . lang('payment_term_tip') . '" id="popayment_term"'); ?>
										</div>
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="form-group">
                                <?= lang("note", "ponote"); ?>
                                <?php echo form_textarea('note', (isset($_POST['note']) ? $_POST['note'] : ""), 'class="form-control" id="ponote" style="margin-top: 10px; height: 100px;"'); ?>
                            </div>

                </div> -->                        
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
<script type="text/javascript">
    $(document).ready(function () {
    var to_warehouse;
        $('#to_warehouse').on("select2-focus", function (e) {
            to_warehouse = $(this).val();
        }).on("select2-close", function (e) {
            if ($(this).val() != '' && $(this).val() == $('#powarehouse').val()) {
                $(this).select2('val', to_warehouse);
                bootbox.alert('<?= lang('please_select_different_warehouse') ?>');
            }
        });
        var from_warehouse;
        $('#powarehouse').on("select2-focus", function (e) {
            from_warehouse = $(this).val();
        }).on("select2-close", function (e) {
            if ($(this).val() != '' && $(this).val() == $('#to_warehouse').val()) {
                $(this).select2('val', from_warehouse);
                bootbox.alert('<?= lang('please_select_different_warehouse') ?>');
            }
        });

    });
</script>