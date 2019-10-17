<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<link rel="stylesheet" href="<?=$assets?>styles/jquery-ui.css">
  <script src="<?=$assets?>js/jquery-ui.js"></script>

<script type="text/javascript">
    var default_store = '<?=$default_store?>';
    
//    <?php if($purchase_invoices_id) { ?>
//    localStorage.setItem('pi_warehouse', '<?= $purchase_invoices->warehouse_id ?>');
//	localStorage.setItem('pi_requestnumber', '<?= $purchase_invoices->requestnumber ?>');
//	localStorage.setItem('pi_requestdate', '<?= $purchase_invoices->requestdate ?>');
//    localStorage.setItem('pi_note', '<?= str_replace(array("\r", "\n"), "", $this->sma->decode_html($purchase_invoices->note)); ?>');
//    localStorage.setItem('pi_discount', '<?= $purchase_invoices->order_discount_id ?>');
//    localStorage.setItem('pi_tax2', '<?= $purchase_invoices->order_tax_id ?>');
//    localStorage.setItem('pi_shipping', '<?= $purchase_invoices->shipping ?>');
//    <?php if ($purchase_invoices->supplier_id) { ?>
//        localStorage.setItem('pi_supplier', '<?= $purchase_invoices->supplier_id ?>');
//    <?php } ?>
//    localStorage.setItem('pi_items', JSON.stringify(<?= $quote_items; ?>));
//    <?php } ?>

    var count = 1, an = 1, purchase_invoices_edit = false, product_variant = 0, DT = <?= $Settings->default_tax_rate ?>, DC = '<?= @$default_currency->code ?>', shipping = 0,
        product_tax = 0, invoice_tax = 0, total_discount = 0, total = 0,
        tax_rates = <?php echo json_encode($tax_rates); ?>, pi_items = {},
        audio_success = new Audio('<?= $assets ?>sounds/sound2.mp3'),
        audio_error = new Audio('<?= $assets ?>sounds/sound3.mp3');
    $(document).ready(function () {
        <?php if($this->input->get('supplier')) { ?>
        if (!localStorage.getItem('pi_items')) {
            //localStorage.setItem('pi_supplier', <?=$this->input->get('supplier');?>);
        }
        <?php } ?>
		
		
        <?php //if ($Owner || $Admin) { ?>
        if (!localStorage.getItem('pi_date')) {
           /* $("#pi_date").datetimepicker({
                format: site.dateFormats.js_sdate,
                fontAwesome: true,
                language: 'common',
                weekStart: 1,
                todayBtn: 1,
                autoclose: 1,
                todayHighlight: 1,
                startView: 2,
                forceParse: 0
            }).datetimepicker('update', new Date());*/
        }
        if (!localStorage.getItem('invoice_date')) {
            $("#invoice_date").datetimepicker({
                /*format: site.dateFormats.js_sdate,*/
                format: site.dateFormats.js_sdate,
                fontAwesome: true,
                todayBtn: 1, 
                autoclose: 1, 
                minView:2,
                maxDate: new Date(),
                endDate: new Date(),                
            }).datetimepicker('update', new Date());
        }

        $(document).on('change', '#pi_date', function (e) {
            localStorage.setItem('pi_date', $(this).val());
        });
        if (podate = localStorage.getItem('pi_date')) {
            $('#pi_date').val(podate);
        }
	if (localStorage.getItem('pi_requestnumber')!=null) {
	    $('#pi_requestnumber').val(localStorage.getItem('pi_requestnumber'));
	    $('#pi_requestnumber').change();

	}
	$("#pi_requestdate").val(localStorage.getItem('pi_requestdate'));
		
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
        if (!localStorage.getItem('pi_tax2')) {
            localStorage.setItem('pi_tax2', <?=$Settings->default_tax_rate2;?>);
            setTimeout(function(){ $('#extras').iCheck('check'); }, 1000);
        }
        ItemnTotals();
        $("#add_item").autocomplete({            
            source: function (request, response) {
                $.ajax({
                    type: 'get',
                    url: '<?= admin_url('procurment/purchase_invoices/suggestions'); ?>',
                    dataType: "json",
                    data: {
                        term: request.term,
                        supplier_id: $("#pi_supplier").val()
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
		    console.log(ui.item);
                    var row = add_purchase_invoices_item(ui.item);
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
                        row = add_purchase_invoices_item(data.result);
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
        <h2 class=""><?= lang('add_purchase_invoice'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
	    
            <div class="col-lg-12">
                <!-- <p class="introtext"><?php echo lang('enter_info'); ?></p> -->
                <?php
                $attrib = array('data-toggle' => 'validator1', 'role' => 'form','id'=>'add-purchase-invoice');
                echo admin_form_open_multipart("procurment/purchase_invoices/add", $attrib)
                ?>
                <div class="row">
		    
                    <div class="col-lg-12" style="background:#b1d7fd; padding:15px 15px;">
			<?php echo form_submit('add_purchase_invoices', $this->lang->line("save"), 'id="add_purchase_orders" class="btn col-lg-1 btn-sm btn-primary pull-right"'); ?>
                                <button type="button" class="btn col-lg-1 btn-sm btn-danger pull-right" style="margin-right:15px;height:30px!important;font-size: 12px!important" id="reset"><?= lang('reset') ?></button>
                        <table class="table custom_tables">
                            <tbody>
                                <tr>
                                    <td>
                                        <?= lang("Date", "date") ?>
                                    </td>
                                    <td>
                                        <input type="datetime" name="date" id="pi_date" readonly tabindex=-1 class="form-control" value="<?php echo date('Y-m-d') ?>">
                                    </td>                                    
                                    <td width="150px">
                                        <?= lang("reference_no", "reference_no"); ?>
                                    </td>
                                    <td>
                                        <?php
                                        $n = $this->siteprocurment->lastidPurchaseInv();
                                        $n2 = str_pad($n + 1, 5, 0, STR_PAD_LEFT);
                                        ?>
                                        <input  name="reference_no" id="reference_no" readonly tabindex=-1 class="form-control" value="<?php echo $n2 ?>">
                                    </td>                                    
                                    <td width="100px">
                                        <?= lang("Currency", "reqcurrency") ?>
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
                                    <td width="100px">
                                        <?= lang("supplier", "pi_supplier"); ?>
                                    </td>
                                    <td width="350px">
					<div class="input-group">
                                        <?php

                                        $sl[""] = "";
                                        foreach ($suppliers as $supplier) {
                                            $sl[$supplier->id] = $supplier->name;
                                        }
                                        echo form_dropdown('supplier', $sl, (isset($_POST['supplier']) ? $_POST['supplier'] : 0), 'id="pi_supplier" data-placeholder="' . $this->lang->line("select") . ' ' . $this->lang->line("supplier") . '"  class="form-control input-tip select" style="width:100%;"');
                                        ?>
					<div class="input-group-addon no-print" style="padding: 2px 5px;">
					    <a href="<?= admin_url('procurment/supplier/add'); ?>" id="add-supplier1" class="external" data-toggle="modal" data-target="#myModal">
						<i class="fa fa-2x fa-plus-square" id="addIcon1"></i>
					    </a>
					</div>
					</div>
                                    </td>

                                    <td width="100px">
                                        <?= lang("Supplier Address", "supplier_address") ?>
                                    </td>
                                    <td width="350px">
                                        <input  name="supplier_address" id="supplier_address" readonly tabindex=-1 class="form-control" value="">
                                    </td>                                    
                                    <td>
                                        <?= lang("status", "pi_status") ?>
                                    </td>
                                    <td>                                        
                                            <?php $st['process'] = lang('process');
                                        if($this->siteprocurment->hasApprovedPermission()){
                                            $st['approved'] = lang('approved'); 
                                        }
                                        echo form_dropdown('status', $st, '', 'class="form-control input-tip" id="pi_status"'); ?>
                                    </td>                                    
                                </tr>
                                <tr>
                                    <td>
                                        <?= lang("document", "document") ?>
                                    </td>
                                    <td>
                                        <!-- <input id="document" type="file" data-browse-label="<?= lang('browse'); ?>" name="document" data-show-upload="false"
                                           data-show-preview="false" class="form-control file"> -->
                                           <input id="document" type="file" data-browse-label="" name="document" data-show-upload="false"
                                       data-show-preview="false" class="form-control file">
                                    </td>
                                    <td>
                                        <?= lang("load_po", "load_po") ?>
                                    </td>
                                    <td>
                                        <?php
                                           $po[''] = '';
                                            foreach ($purchaseorder as $purchaseorder_row) {
                                                $po[$purchaseorder_row->id] = $purchaseorder_row->reference_no;
                                            }
                                            echo form_dropdown('po_number', $po, (isset($_POST['po_number']) ? $_POST['po_number'] : 0 ), ' class="form-control input-tip select" data-placeholder="' . lang("select") . ' ' . lang("po_number") . '"style="width:100%;" id="pi_requestnumber"  ');
                                        ?>
                                    </td>
                                    <td>
                                        <?= lang("tax_type", "tax_type") ?>
                                    </td>
                                    <td>
                                       <?php
                                        $tm = array('1' => lang('exclusive'), '0' => lang('inclusive'));
                                        echo form_dropdown('tax_method', $tm, "1", 'id="tax_method" class="form-control pos-input-tip" style="width:100%"');
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <?= lang("invoice_date", "invoice_date") ?>
                                    </td>
                                    <td>                                        
                                        <input type="datetime" name="invoice_date" id="invoice_date"  class="required form-control" value="<?php echo date('Y-m-d') ?>">
                                    </td>
                                    <td>
                                        <?= lang("invoice_no", "invoice_no") ?>
                                    </td>
                                    <td>                                        
                                        <input type="datetime" name="invoice_no" id="invoice_no"  class="required form-control" value="">
                                    </td>
                                     <td>
                                        <?= lang("invoice_amt", "invoice_amt") ?>
                                    </td>
                                    <td> 
                                       <input type="text" name="invoice_amt" id="invoice_amt" class="required form-control numberonly" value="">
                                    </td>
                                </tr>
                                <tr>                                   
                                    <td>
                                        <?= lang("remarks_note", "ponote") ?>
                                    </td>
                                    <td colspan="3">                                        
                                        <input type="text" name="note" id="pi_note" class="form-control" value="">
                                    </td>                                                                   
                                    <td>
                                        <?= lang("bill_disc", "bill_disc") ?>
                                    </td>
                                    <td>
                                       <div class="col-sm-6" style="padding-left:0px;"><input type="text" name="bill_disc" class="number_percentage_only form-control text-right bill_disc"  value="" style="display: inline-block!important;"></div>
                                       <div class="col-sm-6" style="padding-right:0px;"><input type="text" name="bill_disc_val" class="form-control text-right bill_disc_val"  readonly="" tabindex=-1 value="" style="display: inline-block!important;"></div>
                                    </td>

                                </tr>
                                 <tr>   
                                                                       
                                    <td>
                                        <?= lang("shipping_charge", "shipping_charge") ?>
                                    </td>
                                    <td>
                                       <input type="text" name="shipping_charge" id="feright_chargers_shipping"  class="form-control text-right numberonly" value="">
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
                                       <input type="text" name="bill_net_amt" readonly tabindex=-1 class="form-control text-right net_amt" value="">
                                    </td>
                                </tr>                                
                            </tbody>
                        </table>                          
                    </div>
                    <!-- 
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang("Request Date", "date") ?>
                                <input type="datetime" name="requestdate" id="pi_requestdate" readonly class="form-control" value="<?php echo date('Y-m-d H:i:s') ?>">
                                
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang("supplier", "pi_supplier"); ?>
                                <input type="hidden" name="supplier" value="" id="pi_supplier" class="form-control" required="required" style="width:100%;" placeholder="<?= lang("select") . ' ' . lang("supplier") ?>">
                                <input type="hidden" name="supplier_id" value="" id="supplier_id" class="form-control" >
                            </div>
                        </div>
                        
                        <input type="hidden" name="warehouse" id="pi_warehouse" value="<?php echo $Settings->default_warehouse ?>">  
                           
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                   
                                    <?= lang("document", "document") ?>
                                   
                                    <input id="document" type="file" data-browse-label="<?= lang('browse'); ?>" name="document" data-show-upload="false"
                                           data-show-preview="false" class="form-control file">
                                   
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <?= lang("status", "pi_status"); ?>
                                    <?php $st['process'] = lang('process');
								if($this->siteprocurment->GETaccessModules('purchase_invoices_approved')){
									$st['approved'] = lang('approved');	
								}
                                    echo form_dropdown('status', $st, '', 'class="form-control input-tip" id="pi_status"'); ?>
    
                                </div>
                            </div>
                             
                          <div class="clearfix"></div>	
                        <div class="col-md-12" id="sticker">
                        -->
                        <div class="clearfix"></div>
                         <div class="col-md-12" id="sticker">
                            <div class="well well-sm">
                              
                                <div class="form-group" style="margin-bottom:0;">
                                    <div class="input-group wide-tip">
                                        <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;">
                                            <i class="fa fa-2x fa-barcode addIcon"></i></a></div>
                                        <?php echo form_input('add_item', '', 'class="form-control input-lg" id="add_item" placeholder="' . $this->lang->line("add_Purchase Items_to_order") . '"'); ?>
                                        <?php if ($Owner || $Admin || $GP['products-add']) { ?>
                                        <div class="input-group-addon" style="display: none;padding-left: 10px; padding-right: 10px;">
                                            <a href="<?= admin_url('procurment/products/add') ?>" id="addManually1"><i
                                                    class="fa fa-2x fa-plus addIcon" id="addIcon"></i></a></div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
<style>
    .total_item_qty_tables tbody tr td:last-child{width: 12%;}
</style>
                        <div class="col-md-12">
                            <div class="control-group table-group">
                                <label class="table-label"><?= lang("items"); ?></label></div>
                                <!-- <div class="controls table-controls"> -->
                                    <div class="col-sm-12 response_table_se table_responsive">  


                                    <table id="purchase_invoicesTable"
                                            class="table items  table-bordered table-condensed sortable_table" style="background:#fff;" >
                                        <thead>
                                        <tr>
                                            <th ><?= lang('s.no'); ?></th>
                                            <th><?= lang('code'); ?></th>
                                            <th ><?= lang("description"); ?></th>
											<th class="col-md-2"><?= lang("Category"); ?></th>
                                            <th ><?= lang("Subcategory"); ?></th>
                                            <th ><?= lang("Brand"); ?></th>
											<th ><?= lang("purchase_order_qty"); ?></th>
                                            <th ><?= lang("qty"); ?></th>
                                            <th ><?= lang("uom"); ?></th>
                                            <th ><?= lang("batch_no"); ?></th>
                                            <!-- <th ><?= lang("mfg_date"); ?></th> -->
                                            <th ><?= lang("expiry_date"); ?></th>
                                            <th ><?= lang("cost_price"); ?></th>
                                            <th ><?= lang("gross"); ?></th>
                                            <th ><?= lang("item_dis"); ?></th>
                                            <th ><?= lang("item_dis_amt"); ?></th>
                                            <th ><?= lang("subtotal"); ?></th>
                                            <th ><?= lang("bill_disc"); ?></th>
                                            <th ><?= lang("subtotal"); ?></th>
                                            <!-- <th class="col-md-2"><?= lang("tax_method"); ?></th> -->
                                            <th ><?= lang("tax_%"); ?></th>
                                            <th ><?= lang("tax_amt"); ?></th>
                                            <th ><?= lang("landing_cost"); ?></th>
                                            <th ><?= lang("selling_price"); ?></th>
                                            <th ><?= lang("margin_%"); ?></th>
											<!-- <th class="col-md-1"><?= lang("unit_cost"); ?>(<span
                                                    class="currency"><?= $default_currency->code ?></span>)</th> -->
                                            <!-- <th class="col-md-1"><?= lang("quantity"); ?></th> -->
                                            
                                            <!-- <th class="col-md-2"><?= lang("tax_method"); ?></th>
                                            <th class="col-md-2"><?= lang("tax_type"); ?></th>  -->                                           
                                            <?php
                                            /*if ($Settings->product_discount) {
                                                echo '<th class="col-md-1">' . $this->lang->line("discount") . '</th>';
                                            }*/
                                            ?>
											
											<th  class="col-sm-1"><?= lang("net_amt"); ?></th>
                                            <!-- <th class="col-md-1"><?= lang("tax_amount"); ?></th>
                                            <th><?= lang("subtotal"); ?> (<span
                                                    class="currency"><?= $default_currency->code ?></span>)
                                            </th>-->
                                            <th style="width: 30px !important; text-align: center;"><i
                                                    class="fa fa-trash-o"
                                                    style="opacity:0.5; filter:alpha(opacity=50);"></i></th> 
                                        </tr>
                                        </thead>
                                        <tbody></tbody>
                                        <!-- <tfoot>
										</tfoot> -->
                                    </table>
                                </div>
                            
							
								<!--<div id="bottom-total" class="well well-sm" style="margin-bottom: 0;">-->
                        <table class="table total_item_qty_tables" style="padding: 4px;border-top: none!important">
                            <tbody>
                                <tr>                                    
                                    <td>
                                        <?= lang("total_no_items", "total_no_items") ?>
                                    </td>

                                    <td>
                                        <input  name="total_no_items" id="total_no_items" readonly tabindex=-1 class="form-control">
                                    </td>       
                                    <td width="50%"></td>                                                                 
                                    <td >
                                        <?= lang("gross", "gross") ?>
                                    </td>
                                    <td class="text-right">
                                        <input  name="final_gross_amt" id="final_gross_amt" readonly tabindex=-1 class="form-control text-right">
                                    </td>
                                </tr>
                                 <tr>                                    
                                    <td width="150px">
                                        <?= lang("total_no_qty", "total_no_qty") ?>
                                    </td>
                                    <td width="150px">
                                        <input  name="total_no_qty" id="total_no_qty" readonly tabindex=-1 class="form-control ">
                                    </td>
                                    <td width="50%"></td>                                                                 
                                    <td >
                                        <?= lang("item_disc", "item_disc") ?>
                                    </td>
                                    <td class="text-right">
                                        <input  name="item_disc" id="item_disc" readonly tabindex=-1 class="form-control text-right">
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
                                        <input  name="bill_disc" id="bill_disc_val" readonly tabindex=-1 class="form-control text-right bill_disc_val">
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
                                        <input  name="sub_total" id="sub_total" readonly tabindex=-1 class="form-control text-right">
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
                                        <input  name="tax" id="tax" readonly tabindex=-1 class="form-control text-right">
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
                                        <input  name="freight" id="freight" readonly tabindex=-1 class="form-control text-right">
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
                                        <input  name="round_off" id="round_off" readonly tabindex=-1 class="form-control text-right">
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
                                        <input  name="net_amt" readonly tabindex=-1 class="form-control text-right net_amt">
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
                                        <?= lang("discount", "pi_discount"); ?>
										</div>
                                       <div class="col-md-7">
                                        <?php echo form_input('discount', '', 'class="form-control input-tip" id="pi_discount"'); ?>
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
                            <div class="clearfix"></div>
                            <!-- <div class="form-group">
                                <?= lang("note", "ponote"); ?>
                                <?php echo form_textarea('note', (isset($_POST['note']) ? $_POST['note'] : ""), 'class="form-control" id="ponote" style="margin-top: 10px; height: 100px;"'); ?>
                            </div> -->

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
                                        <input  name="logged_by" id="logged_by" readonly tabindex=-1 class="form-control" value="<?= ucwords($this->session->userdata('username')); ?>">
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
    console.log(localStorage.getItem('round_off'))
    $round_off = localStorage.getItem('round_off');
    $freight = localStorage.getItem('freight');
    $('#round_off,#round_off_amt').val($round_off);
    $('#freight,#feright_chargers_shipping').val($freight);
    $('#pi_note').val(localStorage.getItem('pi_note'));
    $('#invoice_amt').val(localStorage.getItem('invoice_amt'));
    
    if (localStorage.getItem('currency')==null) {
	localStorage.setItem('currency',<?=$Settings->default_currency?>);
    }
    $('#currency').val(localStorage.getItem('currency'));
    if (localStorage.getItem('pi_status')==null) {
	localStorage.setItem('pi_status','process');
    }
    $('#pi_status').val(localStorage.getItem('pi_status'));
    if (localStorage.getItem('tax_method')==null) {
	localStorage.setItem('tax_method',1);
    }
    $('#tax_method').val(localStorage.getItem('tax_method'));
    if(localStorage.getItem('pi_supplier')!=null){
	
	var supplierid = localStorage.getItem('pi_supplier');
	$('#pi_supplier').val(supplierid);
	
    }

});
function get_supplier_details(supplierid){
    if(supplierid != ''){
		$.ajax({
			type: 'get',
			url: '<?= admin_url('procurment/purchase_invoices/supplier'); ?>',
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
}
/*$(document).on('change', '#tax_method', function(){
    localStorage.setItem('tax_method', $('#tax_method').val());
    alert($('#tax_method').val());
});*/


$(document).on('change', '#pi_supplier', function(){
	var pi_supplier = $(this).val();
    
	$.ajax({
		type: 'get',
		url: '<?= admin_url('procurment/purchase_invoices/supplier'); ?>',
		dataType: "json",
		data: {
			supplier_id: pi_supplier
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
$('#tax_method').trigger('change');
$('.bill_disc').trigger('change');
	if(localStorage.getItem('pi_requestnumber') == null){
    	localStorage.setItem('pi_requestnumber', '<?= $ref_requestnumber ?>');
		$("#pi_requestnumber").val(localStorage.getItem('pi_requestnumber'));
		$('#pi_requestnumber').trigger('change');
	}
});
<?php
}
?>



$(document).on('change', '#pi_requestnumber', function(){
	
	if (localStorage.getItem('pi_items')) {
        localStorage.removeItem('pi_items');
    }
	if (localStorage.getItem('pi_requestnumber')) {
        localStorage.removeItem('pi_requestnumber');
    }
	if (localStorage.getItem('pi_requestdate')) {
        localStorage.removeItem('pi_requestdate');
    }
    if (localStorage.getItem('pi_discount')) {
        localStorage.removeItem('pi_discount');
    }
    if (localStorage.getItem('pi_tax2')) {
        localStorage.removeItem('pi_tax2');
    }
    if (localStorage.getItem('pi_shipping')) {
        localStorage.removeItem('pi_shipping');
    }
    if (localStorage.getItem('pi_ref')) {
        localStorage.removeItem('pi_ref');
    }
    if (localStorage.getItem('pi_warehouse')) {
        localStorage.removeItem('pi_warehouse');
    }
    if (localStorage.getItem('pi_note')) {
        localStorage.removeItem('pi_note');
    }
    if (localStorage.getItem('pi_supplier')) {
     //   localStorage.removeItem('pi_supplier');
    }
    if (localStorage.getItem('pi_currency')) {
        localStorage.removeItem('pi_currency');
    }
    if (localStorage.getItem('pi_extras')) {
        localStorage.removeItem('pi_extras');
    }
    if (localStorage.getItem('pi_date')) {
        localStorage.removeItem('pi_date');
    }
    if (localStorage.getItem('pi_status')) {
        localStorage.removeItem('pi_status');
    }
    if (localStorage.getItem('pi_payment_term')) {
        localStorage.removeItem('pi_payment_term');
    }
	
	var pi_requestnumber = $(this).val();
	
	$.ajax({
		type: 'get',
		url: '<?= admin_url('procurment/purchase_invoices/purchase_orders_list'); ?>',
		dataType: "json",
		data: {
			poref: pi_requestnumber
		},
		success: function (data) {
			
			var purchase_invoices_value = [];
			$(this).removeClass('ui-autocomplete-loading');
			var items = JSON.stringify(data.value['purchase_invoicesitem']);
			
			var purchase_invoices = JSON.stringify(data.value['purchase_invoices']);
			purchase_invoices_value = $.parseJSON(purchase_invoices);
			

			localStorage.setItem('pi_requestnumber',  purchase_invoices_value["id"]);
			localStorage.setItem('pi_requestdate',  purchase_invoices_value["date"]);
			localStorage.setItem('pi_warehouse', purchase_invoices_value["warehouse_id"]);
			localStorage.setItem('pi_note', purchase_invoices_value["note"]);
			localStorage.setItem('pi_discount', 0);
			localStorage.setItem('pi_tax2', purchase_invoices_value["order_tax_id"]);
			localStorage.setItem('pi_shipping', purchase_invoices_value["shipping"]);
			localStorage.setItem('pi_supplier', purchase_invoices_value["supplier_id"]);
			localStorage.setItem('bill_disc', purchase_invoices_value["bill_disc"]);
			localStorage.setItem('bill_disc_percentage', purchase_invoices_value["bill_disc_val"]);
			localStorage.setItem('round_off', purchase_invoices_value["round_off"]);
			$('#round_off_amt').val(localStorage.getItem('round_off'));
			localStorage.setItem('freight', purchase_invoices_value["shipping"]);
			$('#feright_chargers_shipping').val(localStorage.getItem('freight'));
			$('#pi_supplier').val(localStorage.getItem('pi_supplier'));
			$('#pi_supplier').change();
			localStorage.setItem('pi_items', items);
			localStorage.setItem('purchase_invoices_date', purchase_invoices_value["purchase_invoices_date"]);
			loadItems();
			//location.reload();
			
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
								echo form_dropdown('ptax_method', $tm, "1", 'id="ptax_method" class="form-control pos-input-tip" style="width:100%"');
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
                                echo form_dropdown('tax_method', $tm, '1', 'class="form-control select" id="mtax_method" placeholder="' . lang("select") . ' ' . lang("tax_method") . '" style="width:100%"')
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
   
</style>
