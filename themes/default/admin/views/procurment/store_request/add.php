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
        <h2 class=""><?= lang('add_store_request'); ?></h2>        
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12" >                
                <?php
                $attrib = array('data-toggle' => 'validator1', 'role' => 'form','id' => 'add-store-request');
                echo admin_form_open_multipart("procurment/store_request/add", $attrib)
                ?>                
                <div class="row">
                    <div class="col-lg-12" style="background:#b1d7fd; padding:15px 15px;">
                        <?php echo form_submit('add_store_request', $this->lang->line("save"), 'id="add_store_request" class="btn col-lg-1 btn-sm btn-primary pull-right" '); ?>
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
                                       <input type="text" name="from_store_name" id="from_store_name" readonly class="form-control" value="<?=$this->store_name?>">
									    <input type="hidden" name="from_store_id" id="from_store_id" readonly class="form-control" value="<?=$this->store_id?>">
                                    </td>
                                    <td width="150px">
                                        <?= lang("to_warehouse", "store_reqto_store_id"); ?>
                                    </td>
                                    <td>
                                         <?php
                                            $tst[''] = '';
                                            foreach ($all_stores as $store) {
                                                $tst[$store->id] = $store->name;
                                            }
										/* 	print_r($tst);
											die; */
											
											// Search
										//	 echo $_POST['from_store_id'];
										//	$pos = array_search($_POST['from_store_id'], $tst);

										//	echo $_POST['from_store_id']. 'found at: ' . $pos;

											// Remove from array
											//unset($tst[$pos]);

											//print_r($tst); 
											/*
											 if(in_array($_POST['from_store_id'], $tst))
											{
												echo 'yes';
											}else{
												echo 'no';
											}  */
                                            echo form_dropdown('to_store_id', $tst, (isset($_POST['to_store_id']) ? $_POST['to_store_id'] : ''), 'id="store_reqto_store_id" class="form-control input-tip select" data-placeholder="' . lang("select") . ' ' . lang("to_store") . '"   style="width:100%;" ');
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
                                            <th class="col-md-1"><?= lang("Quantity"); ?></th>
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
