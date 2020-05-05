<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<script type="text/javascript">
    var count = 1, an = 1, product_variant = 0, DT = <?= $Settings->default_tax_rate ?>, allow_discount = <?= ($Owner || $Admin || $this->session->userdata('allow_discount')) ? 1 : 0; ?>,
        product_tax = 0, invoice_tax = 0, total_discount = 0, total = 0, shipping = 0;
         /*tax_rates = <?php echo json_encode($tax_rates); ?>;*/
    var audio_success = new Audio('<?=$assets?>sounds/sound2.mp3');
    var audio_error = new Audio('<?=$assets?>sounds/sound3.mp3');
    $(document).ready(function () {
        <?php if($this->input->get('customer')) { ?>
        if (!localStorage.getItem('store_reqitems')) {
            localStorage.setItem('store_reqcustomer', <?=$this->input->get('customer');?>);
        }
        <?php } ?>
        <?php //if ($Owner || $Admin) { ?>
        if (!localStorage.getItem('store_reqdate')) {
            $("#store_reqdate").datetimepicker({
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
        $(document).on('change', '#store_reqdate', function (e) {
            localStorage.setItem('store_reqdate', $(this).val());
        });
        if (store_reqdate = localStorage.getItem('store_reqdate')) {
            $('#store_reqdate').val(store_reqdate);
        }
        <?php //} ?>
        $(document).on('change', '#store_reqbiller', function (e) {
            localStorage.setItem('store_reqbiller', $(this).val());
        });
        if (store_reqbiller = localStorage.getItem('store_reqbiller')) {
            $('#store_reqbiller').val(store_reqbiller);
        }
		$(document).on('change', '#store_reqtype', function (e) {
            localStorage.setItem('store_reqtype', $(this).val());
        });
        if (store_reqbiller = localStorage.getItem('store_reqtype')) {
            $('#store_reqtype').val(store_reqtype);
        }
        if (!localStorage.getItem('store_reqtax2')) {
            localStorage.setItem('store_reqtax2', <?=$Settings->default_tax_rate2;?>);
        }
        ItemnTotals();
        $("#add_item").autocomplete({
            source: function (request, response) {
                if (!$('#store_reqfrom_store_id').val() && !$('#store_reqto_store_id').val() ) {
                    $('#add_item').val('').removeClass('ui-autocomplete-loading');
                    bootbox.alert('<?=lang('select_above_from_store_and_to_store');?>');
                    //response('');
                    $('#add_item').focus();
                    return false;
                }
                $.ajax({
                    type: 'get',
                    url: '<?= admin_url('procurment/store_request/suggestions'); ?>',
                    dataType: "json",
                    data: {
                        term: request.term,
                        warehouse_id: $("#store_reqwarehouse").val(),
                        supplier_id: $("#store_reqsupplier").val()
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
		
		
		var store_reqfrom_store_id;
        $('#store_reqfrom_store_id').on("select2-focus", function (e) {
            store_reqfrom_store_id = $(this).val();
        }).on("select2-close", function (e) {
            if ($(this).val() != '' && $(this).val() == $('#store_reqto_store_id').val()) {
                $(this).select2('val', store_reqfrom_store_id);
                bootbox.alert('<?= lang('please_select_different_store') ?>');
            }
        });
        var store_reqto_store_id;
        $('#store_reqto_store_id').on("select2-focus", function (e) {
            store_reqto_store_id = $(this).val();
        }).on("select2-close", function (e) {
            if ($(this).val() != '' && $(this).val() == $('#store_reqfrom_store_id').val()) {
                $(this).select2('val', store_reqto_store_id);
                bootbox.alert('<?= lang('please_select_different_store') ?>');
            }
        });
		
    });
</script>

<div class="box">
    <div class="box-header procurment-header">
        <h2 class=""><?= lang('add_grn'); ?></h2>        
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12" >                
                <?php
                $attrib = array('data-toggle' => 'validator1', 'role' => 'form','id' => 'add-store-request');
                echo admin_form_open_multipart("procurment/grn/add", $attrib)
                ?>                
                <div class="row">
                    <div class="col-lg-12" style="background:#b1d7fd; padding:15px 15px;">
                        <?php echo form_submit('add_grn', $this->lang->line("save"), 'id="add_grn" class="btn col-lg-1 btn-sm btn-primary pull-right" '); ?>
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
                                    <td>
                                        <?= lang("document", "document") ?>                                        
                                    </td>
                                    <td>
                                        <input id="document" type="file" data-browse-label="" name="document" 
                                        data-show-upload="false" data-show-preview="false" class="form-control file">
                                    </td>
                                    <td>
                                        <?= lang("status", "store_reqstatus"); ?>
                                    </td>
                                    <td>
                                        <?php $st['process'] = lang('process');
                                        if($this->siteprocurment->hasApprovedPermission()){
                                            $st['approved'] = lang('approved'); 
                                        }
                                        echo form_dropdown('status', $st, '', 'class="form-control input-tip" id="store_reqstatus"'); ?> 
                                    </td>
                                     <td>
                                        <?= lang("Remarks/Note", "note") ?>
                                    </td>
                                    <td>
                                        <input  name="note" id="reqnote" class="form-control" >
                                    </td>  
                                </tr>
								<tr>
                                    <td>
                                        <?= lang("invoice_date", "invoice_date") ?>
                                    </td>
                                    <td>                                        
                                        <input type="datetime" name="invoice_date" id="invoice_date"  class="required form-control">
                                    </td>
                                    <td>
                                        <?= lang("invoice_no", "invoice_no") ?>
                                    </td>
                                    <td>                                        
                                        <input type="text" name="invoice_no" id="invoice_no"  class="required form-control" value="">
                                    </td>
                                    <td>
                                        <?= lang("load_pi", "load_pi") ?>
                                    </td>
                                    <td>
                                        <?php
                                           $po[''] = '';
                                            foreach ($purchaseorder as $purchaseorder_row) {
                                                $po[$purchaseorder_row->id] = $purchaseorder_row->reference_no;
                                            }
                                            echo form_dropdown('po_number', $po, (isset($_POST['po_number']) ? $_POST['po_number'] : 0 ), ' class="form-control input-tip select" data-placeholder="' . lang("select") . ' ' . lang("pi_number") . '"style="width:100%;" id="pi_requestnumber"  ');
                                        ?>
                                    </td>
                                </tr>
								<tr>
                               
                                
                                     <td>
                                        <?= lang("invoice_amt", "invoice_amt") ?>
                                    </td>
                                    <td> 
                                       <input type="text" name="invoice_amt" id="invoice_amt" class="required form-control numberonly" value="">
                                    </td>
									<td>
                                        <?= lang("delivery_address", "delivery_address") ?>
                                    </td>
                                    <td> 
                                       <input type="text" name="delivery_address" id="delivery_address" class="required form-control numberonly" value="">
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
                                       
                                        <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;">
                                            <a href="javascript:void(0)" id="addManually1"><i
                                                    class="fa fa-2x fa-search addIcon" id="addIcon"></i></a></div>
                                     
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
                                    <table id="store_reqTable"
                                            class="table items  table-bordered table-condensed sortable_table" style="background:#fff">
                                        <thead>
                                        <tr>
                                            <th class="col-md-4"><?= lang('product') . ' (' . lang('code') .' - '.lang('name') . ')'; ?></th>
                                            <th class="col-md-2"><?= lang("Category"); ?></th>
                                            <th class="col-md-2"><?= lang("Subcategory"); ?></th>
                                            <th class="col-md-2"><?= lang("Brand"); ?></th>
                                            <th class="col-md-1"><?= lang("PI.Quantity"); ?></th>
											<th class="col-md-1"><?= lang("received_quantity"); ?></th>
                                            <th class="col-md-1 text-center" style="text-align:center !important"><i
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

                     
                    </div>
					
                </div>
               <table class="table total_item_qty_tables" style="padding: 4px;border-top: none!important;width:30%">
				    <tbody>
					<tr>                                    
					    <td>
						<label for="titems">total no items</label>                                    </td>
					    <td>
						<input name="titems" id="titems" readonly="" class="form-control" autocomplete="off">
					    </td>
					</tr>
					<tr>                                    
					    <td>
						<label for="total_items">total no qty</label>                                    </td>
					    <td>
						<input name="total_items" id="total_items" readonly="" class="form-control" autocomplete="off">
					    </td>
					</tr>
				    </tbody>
				    </table>
               

                <?php echo form_close(); ?>

            </div>

        </div>
    </div>
</div>
