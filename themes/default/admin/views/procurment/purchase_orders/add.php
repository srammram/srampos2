<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<script type="text/javascript">
    var default_store = '<?=$default_store?>';
    
    <?php if($purchase_orders_id) { ?>
    localStorage.setItem('po_warehouse', '<?= $purchase_orders->warehouse_id ?>');
    localStorage.setItem('po_requestnumber', '<?= $purchase_orders->requestnumber ?>');
    localStorage.setItem('po_requestdate', '<?= $purchase_orders->requestdate ?>');
    localStorage.setItem('po_note', '<?= str_replace(array("\r", "\n"), "", $this->sma->decode_html($purchase_orders->note)); ?>');
    localStorage.setItem('po_discount', '<?= $purchase_orders->order_discount_id ?>');
    localStorage.setItem('po_tax2', '<?= $purchase_orders->order_tax_id ?>');
    localStorage.setItem('po_shipping', '<?= $purchase_orders->shipping ?>');
    <?php if ($purchase_orders->supplier_id) { ?>
        localStorage.setItem('po_supplier', '<?= $purchase_orders->supplier_id ?>');
    <?php } ?>
    localStorage.setItem('po_items', JSON.stringify(<?= $quote_items; ?>));
    <?php } ?>

    var count = 1, an = 1, purchase_orders_edit = false, product_variant = 0, DT = <?= $Settings->default_tax_rate ?>, DC = '<?= $default_currency->code ?>', shipping = 0,
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
        <?php //if ($Owner || $Admin) { ?>
        if (!localStorage.getItem('po_date')) {
            $("#po_date").datetimepicker({
                format: site.dateFormats.js_ldate,
                fontAwesome: true,
                language: 'common',
                weekStart: 1,
                todayBtn: 1,
                autoclose: 1,
                todayHighlight: 1,
                startView: 2,
                forceParse: 0
            }).datetimepicker('update', new Date());
        }
        $(document).on('change', '#po_date', function (e) {
            localStorage.setItem('po_date', $(this).val());
        });
        if (podate = localStorage.getItem('po_date')) {
            $('#po_date').val(podate);
        }
        $("#po_requestnumber").val(localStorage.getItem('po_requestnumber'));
        $("#po_requestdate").val(localStorage.getItem('po_requestdate'));
        
        if (!localStorage.getItem('iodate')) {
            $("#iodate").datetimepicker({
                format: site.dateFormats.js_ldate,
                fontAwesome: true,
                language: 'common',
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
        <?php //} ?>
        if (!localStorage.getItem('po_tax2')) {
            localStorage.setItem('po_tax2', <?=$Settings->default_tax_rate2;?>);
            setTimeout(function(){ $('#extras').iCheck('check'); }, 1000);
        }
        ItemnTotals();
        $("#add_item").autocomplete({
            // source: '<?= admin_url('procurment/purchase_orders/suggestions'); ?>',
            source: function (request, response) {
                $.ajax({
                    type: 'get',
                    url: '<?= admin_url('procurment/purchase_orders/suggestions'); ?>',
                    dataType: "json",
                    data: {
                        term: request.term,
                        supplier_id: $("#po_supplier").val()
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
                    var row = add_purchase_orders_item(ui.item);
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
                        row = add_purchase_orders_item(data.result);
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
    <div class="box-header procurment-header">
        <h2 class=""><?= lang('add_purchase_orders'); ?></h2>
        <!-- <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('add_purchase_orders'); ?></h2> -->
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <!-- <p class="introtext"><?php echo lang('enter_info'); ?></p> -->
                <?php
                $attrib = array('data-toggle' => 'validator1', 'role' => 'form','id' => 'add-purchase-order');
                echo admin_form_open_multipart("procurment/purchase_orders/add", $attrib)
                ?>
                <div class="row">
                    
                    <div class="col-lg-12" style="background:#b1d7fd; padding:15px 15px;">
                       <?php echo form_submit('add_purchase_orders', $this->lang->line("save"), 'id="add_purchase_orders" class="btn col-lg-1 btn-sm btn-primary pull-right"'); ?>
                                <button type="button" class="btn col-lg-1 btn-sm btn-danger pull-right" style="margin-right:15px;height:30px!important;font-size: 12px!important" id="reset"><?= lang('reset') ?></button>
                                
                    <table class="table custom_tables">
                            <tbody>
                                <tr>
                                    <td>
                                        <?= lang("date", "date") ?>
                                    </td>
                                    <td>
                                        <input type="datetime" name="date" id="po_date" readonly class="form-control" value="<?php echo date('Y-m-d H:i:s') ?>">
                                    </td>
                                    <td>
                                        <?= lang("purchase_no", "purchase_no") ?>
                                    </td>
                                    <td>
                                        <?php
                                        /*$reference = 'PO'.date('YmdHis');            
                                        $date = date('Y-m-d H:i:s');*/
                                        
                                        $n = $this->siteprocurment->lastidPurchase();
                                        $n2 = str_pad($n + 1, 5, 0, STR_PAD_LEFT);
                                        ?>
                                        <input  name="reference_no" id="reference_no" readonly class="form-control" value="<?php echo $n2 ?>">
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
                                        echo form_dropdown('currency', $c, (isset($_POST['currency']) ? $_POST['currency'] : $Settings->default_currency), 'id="currency" data-placeholder="' . $this->lang->line("select") . ' ' . $this->lang->line("currencies") . '" required="required" class="form-control input-tip" style="width:100%;"');
                                        ?>
                                    </td>                                    
                                </tr> 
                                <tr>
                                    <td>
                                        <?= lang("supplier", "po_supplier"); ?>
                                    </td>
                                    <td>
                                        <?php

                                        $sl[""] = "";
                                        foreach ($suppliers as $supplier) {
                                            $sl[$supplier->id] = $supplier->name;
                                        }
                                        echo form_dropdown('supplier', $sl, (isset($_POST['supplier']) ? $_POST['supplier'] : 0), 'id="po_supplier" data-placeholder="' . $this->lang->line("select") . ' ' . $this->lang->line("supplier") . '"  class="form-control input-tip select" style="width:100%;"');
                                        ?>
                                    </td>
                                     <td width="100px">
                                        <?= lang("supplier_address", "supplier_address") ?>
                                    </td>
                                    <td width="350px">
                                        <input  name="supplier_address" id="supplier_address" readonly class="form-control" value="">
                                    </td>
                                    <td>
                                        <?= lang("status", "po_status"); ?>
                                    </td>
                                    <td>
                                        <?php $st['process'] = lang('process');
                                        if($this->siteprocurment->hasApprovedPermission()){
                                            $st['approved'] = lang('approved'); 
                                        }
                                        echo form_dropdown('status', $st, '', 'class="form-control input-tip" id="po_status"'); ?>
                                    </td>
                                </tr>  
                                <tr>
                                    <td>
                                        <?= lang("quotation_date", "quotation_date") ?>
                                    </td>
                                    <td>
                                        <input type="datetime" name="requestdate" id="po_requestdate" readonly class="form-control" value="<?php echo date('Y-m-d H:i:s') ?>">
                                    </td>
                                    <td>
                                        <?= lang("load_quotation", "load_quotation") ?>
                                    </td>       
                                    <td>                                        
                                        <?php
                                    
                                       $qn[''] = '';
                                        foreach ($requestnumber as $requestnumber_row) {
                                            $qn[$requestnumber_row->id] = $requestnumber_row->reference_no;
                                        }
                                        echo form_dropdown('requestnumber', $qn, (isset($_POST['requestnumber']) ? $_POST['requestnumber'] : '' ), ' class="form-control input-tip select" data-placeholder="' . lang("select") . ' ' . lang("request_number") . '"style="width:100%;" id="po_requestnumber"  ');
                                        ?>
                                    </td>
                                    <td>
                                        <?= lang("tax_type", "tax_type") ?>
                                    </td>
                                    <td>
                                       <?php
                                        $tm = array('1' => lang('exclusive'), '0' => lang('inclusive'));
                                        echo form_dropdown('tax_method', $tm, "", 'id="tax_method" class="form-control pos-input-tip" style="width:100%"');
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                     <?= lang("document", "document") ?>   
                                    </td>     
                                    <td>
                                        <input id="document" type="file" data-browse-label="" name="document" 
                                        data-show-upload="false" data-show-preview="false" class="form-control file">
                                    </td>  
                                    <td>
                                        <?= lang("remarks_note", "ponote") ?>
                                    </td>
                                    <td colspan="1">                                        
                                        <input type="text" name="note" id="po_note" class="form-control" value="">
                                    </td> 
                                     <td>
                                        <?= lang("bill_disc", "bill_disc") ?>
                                    </td>
                                    <td style="display: inline-block!important;">
                                       <input type="text" name="bill_disc" class="number_percentage_only form-control text-right bill_disc"  value="" >
                                       <input type="text" name="bill_disc_val" class="form-control text-right bill_disc_val bill_disc_val_css"  readonly="" value="">
                                    </td>
                                </tr>   
                                <tr>                                   
                                    <td>
                                        <?= lang(" shipping_charges", "feright_chargers_shipping") ?>
                                    </td>
                                    <td>
                                       <input type="text" name="feright_chargers_shipping" id="feright_chargers_shipping"  class="form-control text-right numberonly" value="">
                                    </td>                                
                                    <td>
                                        <?= lang("round_off", "round_off") ?>
                                    </td>
                                    <td>
                                       <input type="text" name="round_off" id="round_off_amt"  class="form-control text-right number_minus"  value="">
                                    </td>                                                                   
                                    <td>
                                        <?= lang("net_amt", "net_amt") ?>
                                    </td>
                                    <td>
                                       <input type="text" name="bill_net_amt" readonly class="form-control text-right net_amt" value="">
                                    </td>
                                </tr>                  
                            </tbody>
                        </table>                          
                    </div>
                        
                        <input type="hidden" name="warehouse" id="po_warehouse" value="<?php echo $Settings->default_warehouse ?>">  
                            <div class="clearfix"></div>
                            <div class="col-md-12" id="sticker">
                                <div class="well well-sm">                                  
                                    <div class="form-group" style="margin-bottom:0;">
                                        <div class="input-group wide-tip">
                                            <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;">
                                                <i class="fa fa-2x fa-barcode addIcon"></i></div>
                                            <?php echo form_input('add_item', '', 'class="form-control input-lg" id="add_item" placeholder="' . $this->lang->line("search_purchase_items") . '"'); ?>
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
<style>
    /* .response_table_se{overflow-x: scroll;width: 940px;padding: 0px;} */
    
    .total_item_qty_tables tbody tr td:last-child{width: 12%;}
</style>
                        <div class="col-md-12">
                            <div class="control-group table-group">
                                <label class="table-label"><?= lang("items"); ?></label>
                                <!-- <div class="controls table-controls"> -->
                                    <div class='col-sm-12 response_table_se table_responsive'>  
                                    <table id="purchase_ordersTable"
                                            class="table items  table-bordered table-condensed sortable_table" style="background:#fff; display: block !important;">
                                        <thead>
                                        <tr>
                                            <th ><?= lang('s.no'); ?></th>
                                            <th><?= lang('code'); ?></th>
                                            <th ><?= lang("description"); ?></th>
                                            <th class="col-md-2"><?= lang("Category"); ?></th>
                                            <th class="col-md-2"><?= lang("Subcategory"); ?></th>
                                            <th class="col-md-2"><?= lang("Brand"); ?></th>
                                            <th ><?= lang("qty"); ?></th>
                                            <th ><?= lang("uom"); ?></th>
                                            <!-- <th ><?= lang("batch_no"); ?></th>
                                            <th ><?= lang("expiry_date"); ?></th> -->
                                            <th ><?= lang("cost_price"); ?></th>
                                            <th ><?= lang("gross"); ?></th>
                                            <th ><?= lang("item_dis"); ?></th>
                                            <th ><?= lang("item_dis_amt"); ?></th>
                                            <th ><?= lang("subtotal"); ?></th>
                                            <th ><?= lang("bill_disc"); ?></th>
                                            <th ><?= lang("subtotal"); ?></th>
                                            <th ><?= lang("tax_%"); ?></th>
                                            <th ><?= lang("tax_amt"); ?></th>
                                            <th ><?= lang("landing_cost"); ?></th>
                                            <th ><?= lang("selling_price"); ?></th>
                                            <th ><?= lang("margin_%"); ?></th>
                                            <th class="col-md-1"><?= lang("net_amt"); ?></th>
                                            <th style="width: 30px!important; text-align: center;">
                                                <i class="fa fa-trash-o" style="opacity:0.5; filter:alpha(opacity=50);"></i></th>
                                        </tr>
                                        </thead>
                                        <tbody></tbody>
                                        <!-- <tfoot>
                                        </tfoot> -->
                                    </table>
                                </div>
                            </div>                                
                        <table class="table total_item_qty_tables" style="padding: 4px;border-top: none!important">
                            <tbody>
                                <tr>                                    
                                    <td>
                                        <?= lang("total_no_items", "total_no_items") ?>
                                    </td>
                                    <td>
                                        <input name="total_no_items" id="total_no_items" readonly class="form-control">
                                    </td>       
                                    <td width="50%"></td>                                                                 
                                    <td >
                                        <?= lang("gross", "gross") ?>
                                    </td>
                                    <td class="text-right">
                                       <input name="final_gross_amt" id="final_gross_amt" readonly class="form-control text-right">
                                    </td>
                                </tr>
                                 <tr>                                    
                                    <td width="150px">
                                        <?= lang("total_no_qty", "total_no_qty") ?>
                                    </td>
                                    <td width="150px">
                                        <input name="total_no_qty" id="total_no_qty" readonly class="form-control ">
                                    </td>
                                    <td width="50%"></td>                                                                 
                                    <td >
                                        <?= lang("item_disc", "item_disc") ?>
                                    </td>
                                    <td class="text-right">
                                        <input name="item_disc" id="item_disc" readonly class="form-control text-right">
                                    </td>
                                </tr>
                                 <tr>                                    
                                    <td width="150px"></td>
                                    <td width="150px"></td>
                                    <td width="45%"></td>                                                                 
                                    <td width="10%">
                                        <?= lang("bill_disc", "bill_disc") ?>
                                    </td>
                                    <td class="text-right">
                                        <input  id="bill_disc_val" readonly class="form-control text-right bill_disc_val">
                                    </td>
                                </tr>
                                 <tr>                                    
                                    <td width="150px"></td>
                                    <td width="150px"></td>
                                    <td width="45%"></td>                                                                 
                                    <td width="10%">
                                        <?= lang("sub_total", "sub_total") ?>
                                    </td>
                                    <td class="text-right">
                                        <input name="sub_total" id="sub_total" readonly class="form-control text-right">
                                    </td>
                                </tr>    
                                <tr> 
                                    <td width="150px"></td>
                                    <td width="150px"></td>
                                    <td width="45%"></td>                                                                 
                                    <td width="10%">
                                        <?= lang("tax", "tax") ?>
                                    </td>
                                    <td class="text-right">
                                        <input name="tax" id="tax" readonly class="form-control text-right">
                                    </td>
                                </tr>                                                                                                       
                                <tr> 
                                    <td width="150px"></td>
                                    <td width="150px"></td>
                                    <td width="45%"></td>                                                                 
                                    <td width="10%">
                                        <?= lang("shipping_charges", "shipping_charges") ?>
                                    </td>
                                    <td class="text-right">
                                        <input name="freight" id="freight" readonly class="form-control text-right">
                                    </td>
                                </tr>                                                                                                       
                                <tr> 
                                    <td width="150px"></td>
                                    <td width="150px"></td>
                                    <td width="45%"></td>                                                                 
                                    <td width="10%">
                                        <?= lang("round_off", "round_off") ?>
                                    </td>
                                    <td class="text-right">
                                        <input name="round_off" id="round_off" readonly class="form-control text-right">
                                    </td>
                                </tr>                                                                                                       
                                <tr> 
                                    <td width="150px"></td>
                                    <td width="150px"></td>
                                    <td width="45%"></td>                                                                 
                                    <td width="10%">
                                        <?= lang("net_amt", "freight") ?>
                                    </td>
                                    <td class="text-right">
                                        <input name="net_amt" readonly class="form-control text-right net_amt">
                                    </td>
                                </tr>                                                                                                       
                            </tbody>
                        </table>                                    
                    <table class="table table-bordered table-condensed totals" style="margin-bottom:0;display: none;" >
                        
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
                <!--</div>-->
                        <div class="clearfix"></div>
                       <input type="hidden" name="total_items" value="" id="total_items"/ >

                        <div class="col-md-12" style="display: none">
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
                                        <?= lang("discount", "po_discount"); ?>
                                        </div>
                                       <div class="col-md-7">
                                        <?php echo form_input('discount', '', 'class="form-control input-tip" id="po_discount"'); ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6" style="padding-bottom: 10px;">
                                    <div class="form-group">
                                       <div class="col-md-5">
                                        <?= lang("Shipping_charges", "poshipping"); ?>
                                        </div>
                                       <div class="col-md-7">
                                        <?php echo form_input('shipping', '', 'class="form-control input-tip" id="poshipping"'); ?>
                                        </div>
                                    </div>
                                </div>                                
                            </div>
                           
                        </div>
                        
                    </div>

                    </div>
                <div class="col-lg-12" style="background:#a6f7a1; margin-top:15px;">
                    <table class="table custom_tables">
                            <tbody>                                
                                <tr>                                    
                                    <td width="150px">
                                        <?= lang("logged_by", "logged_by") ?>
                                    </td>
                                    <td width="150px">
                                        <input  name="logged_by" id="logged_by" readonly class="form-control" value="<?= ucfirst($this->session->userdata('username')); ?>">
                                    </td>
                                     <td width="100px">                                    
                                    </td>
                                    <td width="100px">                                    
                                    </td>                                    
                                    <td>
                                        <?= lang("till/counter_name", "counter_name") ?>
                                    </td>
                                    <td>
                                        <input  name="counter_name" id="counter_name" class="form-control" >
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

<script>
$(document).ready(function(e) {

    $round_off = localStorage.getItem('round_off');
    $freight = localStorage.getItem('freight');
    $('#round_off,#round_off_amt').val($round_off);
    $('#freight,#feright_chargers_shipping').val($freight);
    $('#po_note').val(localStorage.getItem('po_note'));
    $('#invoice_amt').val(localStorage.getItem('invoice_amt'));
    
    if (localStorage.getItem('currency')==null) {
    localStorage.setItem('currency',2);
    }
    $('#currency').val(localStorage.getItem('currency'));
    if (localStorage.getItem('po_status')==null) {
    localStorage.setItem('po_status','process');
    }
    $('#po_status').val(localStorage.getItem('po_status'));
    if (localStorage.getItem('tax_method')==null) {
    localStorage.setItem('tax_method',1);
    }
    $('#tax_method').val(localStorage.getItem('tax_method'));
    var supplierid = localStorage.getItem('po_supplier');
    $('#po_supplier').val(supplierid);
    
    if(supplierid != ''){
        $.ajax({
            type: 'get',
            url: '<?= admin_url('procurment/purchase_orders/supplier'); ?>',
            dataType: "json",
            data: { supplier_id: supplierid },
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


$(document).on('change', '#po_supplier', function(){
    var po_supplier = $(this).val();
    $.ajax({
        type: 'get',
        url: '<?= admin_url('procurment/purchase_orders/supplier'); ?>',
        dataType: "json",
        data: {
            supplier_id: po_supplier
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

<?php
if(!empty($ref_requestnumber)){
?>
$(document).ready(function(e) {
    if(localStorage.getItem('po_requestnumber') == null){
        localStorage.setItem('po_requestnumber', '<?= $ref_requestnumber ?>');
        $("#po_requestnumber").val(localStorage.getItem('po_requestnumber'));
        $('#po_requestnumber').trigger('change');
    }
});
<?php
}
?>

$(document).on('change', '#po_requestnumber', function(){
    
    if (localStorage.getItem('po_items')) {
        localStorage.removeItem('po_items');
    }
    if (localStorage.getItem('po_requestnumber')) {
        localStorage.removeItem('po_requestnumber');
    }
    if (localStorage.getItem('po_requestdate')) {
        localStorage.removeItem('po_requestdate');
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
    
    var po_requestnumber = $(this).val();
    
    $.ajax({
        type: 'get',
        url: '<?= admin_url('procurment/purchase_orders/purchase_orders_list'); ?>',
        dataType: "json",
        data: {
            poref: po_requestnumber
        },
        success: function (data) {
            
            var purchase_orders_value = [];
            $(this).removeClass('ui-autocomplete-loading');
            var items = JSON.stringify(data.value['purchase_ordersitem']);
            
            var purchase_orders = JSON.stringify(data.value['purchase_orders']);
            purchase_orders_value = $.parseJSON(purchase_orders);
            

            localStorage.setItem('po_requestnumber',  purchase_orders_value["id"]);
            localStorage.setItem('po_requestdate',  purchase_orders_value["date"]);
            localStorage.setItem('po_warehouse', purchase_orders_value["warehouse_id"]);
            localStorage.setItem('po_note', purchase_orders_value["note"]);
            localStorage.setItem('po_discount', 0);
            localStorage.setItem('po_tax2', purchase_orders_value["order_tax_id"]);
            localStorage.setItem('po_shipping', purchase_orders_value["shipping"]);
            localStorage.setItem('po_supplier', purchase_orders_value["supplier_id"]);
            localStorage.setItem('po_items', items);
            localStorage.setItem('purchase_orders_date', purchase_orders_value["purchase_orders_date"]);
            
            location.reload();
            
        }
        
        
    });
});
</script>

<div class="modal" id="DSModal" tabindex="-1" role="dialog" aria-labelledby="DSModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true"><i
                            class="fa fa-2x">&times;</i></span><span class="sr-only"><?=lang('close');?></span></button>
                <h4 class="modal-title" id="DSModalLabel"></h4>
            </div>
            <div class="modal-body" id="pr_popover_content">
                <form class="form-horizontal" role="form">
                    
                    <div class="form-group">
                        <label for="pquantity" class="col-sm-4 control-label"><?= lang('total_quantity') ?></label>

                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="dsquantity" readonly>
                        </div>
                    </div>
                    <input type="hidden" id="dsproduct_id" value=""/>
                    <input type="hidden" id="dsrow_id" value=""/>
                    <input type="hidden" id="dsquote_id" value=""/>
                    <div class="ds_addon">
                        
                    </div>
                    
                </form>
            </div>
            
            <div class="clearfix"></div>
            <br>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="DSItem"><?= lang('submit') ?></button>
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
                    
                <!--    <div class="form-group">
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

<script>
var ds = 1
$(document).on('click', '.ds_add', function () {
     var product_id = $(this).attr('data-type');
       var row = $(this).attr('data-title');
    if (ds <= 5) {
      
        $.ajax({
            type: "get", 
            url: site.base_url+"purchase_orders/getProductStoresDelete/?product_id="+product_id+"&row="+row,
            dataType: "html",
            success: function (data) {
                $('.ds_addon').append(data);
            }
        });
        ds++;
        
    } else {
        bootbox.alert('<?= lang('max_reached') ?>');
        return false;
    }
});

$("body").on('click','.ds_delete', function(){
    $(this).closest('.ds').remove();
});

$(document).on('change', '.store_quantity', function () {
    
    $(this).siblings('input').val($(this).val());
    var sum = 0;
    $('.store_quantity').each(function()
    {
        
        sum += parseFloat($(this).val());
        
    });
    $('#dsquantity').val(sum);
});

</script>
<style type="text/css">
    .total_item_qty_tables>tbody>tr>td
    {
        border-top: none!important;
    }    
    .item-dis-type{
    display: none !important;
    }
    .rdiscount{
    float: left;
    width:60px;
    }
    .item-dis-type + label{
    line-height: 19px;
    text-align: center;
    background: #dddddd;
    width: 17px;
    }
    .item-dis-type:checked + label{
    background: #428bca;
    }
    .invoice-error{
    border: 2px solid #F00 !important;
    }
    
    .stores-popup::before{
            content: " ";
    position: absolute;
    /*top: 50%;*/
    right: 100%;
    margin-top: -5px;
    border-width: 5px; 
    border-style: solid;
    border-color: transparent black transparent transparent;
    }
    .stores-popup{
        display: none;
        margin-left: 61px;
        margin-top: -16px;
        position: absolute;
        z-index: 20;
        background: #fff;
        padding: 20px;
       
        text-align: center;
        border-radius: 2px;
        
        
    }
    .store-qty-save{
        height: 24px;
        padding: 1px;
    }
	.bill_disc {width: 49%;display: inline-block!important; margin-right:1%; float:left; }
	
	.bill_disc_val_css {width: 50%;display: inline-block!important; }
</style>