<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<script type="text/javascript">
 <?php if ($this->session->userdata('remove_pols')) { ?>
    if (localStorage.getItem('poitems')) {
        localStorage.removeItem('poitems');
    }
	if (localStorage.getItem('porefnumber')) {
        localStorage.removeItem('porefnumber');
    }
    if (localStorage.getItem('podiscount')) {
        localStorage.removeItem('podiscount');
    }
    if (localStorage.getItem('potax2')) {
        localStorage.removeItem('potax2');
    }
    if (localStorage.getItem('poshipping')) {
        localStorage.removeItem('poshipping');
    }
    
    if (localStorage.getItem('powarehouse')) {
        localStorage.removeItem('powarehouse');
    }
    if (localStorage.getItem('ponote')) {
        localStorage.removeItem('ponote');
    }
    if (localStorage.getItem('posupplier')) {
        localStorage.removeItem('posupplier');
    }
    if (localStorage.getItem('pocurrency')) {
        localStorage.removeItem('pocurrency');
    }
    if (localStorage.getItem('poextras')) {
        localStorage.removeItem('poextras');
    }
    if (localStorage.getItem('purchases_order_date')) {
        localStorage.removeItem('purchases_order_date');
    }
	 if (localStorage.getItem('invoice_no')) {
        localStorage.removeItem('invoice_no');
    }
	 if (localStorage.getItem('invoice_date')) {
        localStorage.removeItem('invoice_date');
    }
    if (localStorage.getItem('postatus')) {
        localStorage.removeItem('postatus');
    }
    if (localStorage.getItem('popayment_term')) {
        localStorage.removeItem('popayment_term');
    }
	<?php } ?>
    var count = 1, an = 1, po_edit = true, product_variant = 0, DT = <?= $Settings->default_tax_rate ?>, DC = '<?=$default_currency->code?>', shipping = 0,
        product_tax = 0, invoice_tax = 0, total_discount = 0, total = 0,
        tax_rates = <?php echo json_encode($tax_rates); ?>, poitems = {},
        audio_success = new Audio('<?= $assets ?>sounds/sound2.mp3'),
        audio_error = new Audio('<?= $assets ?>sounds/sound3.mp3');
    $(window).bind("load", function() {
        <?= ($inv->status == 'received' || $inv->status == 'partial') ? '$(".rec_con").show();' : '$(".rec_con").hide();'; ?>
    });
    $(document).ready(function () {
        <?= ($inv->status == 'received' || $inv->status == 'partial') ? '$(".rec_con").show();' : '$(".rec_con").hide();'; ?>
        $('#postatus').change(function(){
            var st = $(this).val();
            if (st == 'received' || st == 'partial') {
                $(".rec_con").show();
            } else {
                $(".rec_con").hide();
            }
        });
      
	  
	  <?php if($quote_id) { ?>
 
	localStorage.setItem('porefnumber', '<?= $quote->id ?>');
   
    <?php } ?>
	
        localStorage.setItem('podate', '<?= date($dateFormats['php_ldate'], strtotime($inv->date))?>');
        localStorage.setItem('posupplier', '<?=$inv->supplier_id?>');
       
        localStorage.setItem('powarehouse', '<?=$inv->warehouse_id?>');
        localStorage.setItem('postatus', '<?=$inv->status?>');
        localStorage.setItem('ponote', '<?= str_replace(array("\r", "\n"), "", $this->sma->decode_html($inv->note)); ?>');
        localStorage.setItem('podiscount', '<?=$inv->order_discount_id?>');
        localStorage.setItem('potax2', '<?=$inv->order_tax_id?>');
        localStorage.setItem('poshipping', '<?=$inv->shipping?>');
        localStorage.setItem('popayment_term', '<?=$inv->payment_term?>');
		
		 localStorage.setItem('invoice_no', '<?=$inv->invoice_no?>');
		  localStorage.setItem('invoice_date', '<?=$inv->invoice_date?>');
		
		
	
		
		
        if (parseFloat(localStorage.getItem('potax2')) >= 1 || localStorage.getItem('podiscount').length >= 1 || parseFloat(localStorage.getItem('poshipping')) >= 1) {
            localStorage.setItem('poextras', '1');
        }
        localStorage.setItem('poitems', JSON.stringify(<?=$inv_items;?>));
		
		$("#porefnumber").val(localStorage.getItem('porefnumber'));
		$("#invoice_no").val(localStorage.getItem('invoice_no'));
		$("#invoice_date").val(localStorage.getItem('invoice_date'));
		
		
        <?php if($this->input->get('supplier')) { ?>
        if (!localStorage.getItem('poitems')) {
            localStorage.setItem('posupplier', <?=$this->input->get('supplier');?>);
        }
        <?php } ?>
        

        <?php if ($Owner || $Admin) { ?>
        $(document).on('change', '#podate', function (e) {
            localStorage.setItem('podate', $(this).val());
        });
        if (podate = localStorage.getItem('podate')) {
            $('#podate').val(podate);
        }
		
		 if (!localStorage.getItem('purchases_order_date')) {
            $("#purchases_order_date").datetimepicker({
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
        $("#invoicedate").datetimepicker({
               format: site.dateFormats.js_sdate, fontAwesome: true, todayBtn: 1, autoclose: 1, minView: 2,startDate: new Date()
            });
        $(document).on('change', '#purchases_order_date', function (e) {
            localStorage.setItem('purchases_order_date', $(this).val());
        });
        if (podate = localStorage.getItem('purchases_order_date')) {
            $('#purchases_order_date').val(podate);
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
        ItemnTotals();
        $("#add_item").autocomplete({
            source: '<?= admin_url('purchases/suggestions'); ?>',
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
                    var row = add_purchase_item(ui.item);
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
                        row = add_purchase_item(data.result);
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
        $('#edit_pruchase').click(function () {
            $(window).unbind('beforeunload');
           // $('form.edit-po-form').submit();
        });

    });


</script>

<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-edit"></i><?= lang('edit_purchase'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <!-- <p class="introtext"><?php echo lang('enter_info'); ?></p> -->
                <?php
                $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'class' => 'edit-po-form');
                echo admin_form_open_multipart("purchases/edit/" . $id, $attrib)
                ?>
                
                
                <div class="row">
                    <div class="col-lg-12">
						
                       
                         <h2>GRN Details</h2>

						<!--<div class="col-md-6" style="padding-bottom: 10px;">
                            <div class="form-group">
                                <div class="col-md-5">
                                
								</div>
                               <div class="col-md-7">
                                <?php// //echo form_input('grn_details', (isset($_POST['grn_details']) ? $_POST['grn_details'] : ""), 'class="form-control input-tip" id="grn_details"'); ?>
								</div>
                            </div>
                        </div>
                        -->
                         <div class="col-md-6" style="padding-bottom: 10px;">
                            <div class="form-group">
                                <div class="col-md-5">
                                 <?= lang("grn_date", "grn_date"); ?>
                                </div>
                                <div class="col-md-7">
								<?php echo form_input('grn_date', ($purchase->grn_date ? $purchase->grn_date : ''), 'class="form-control input-tip totay_date" id="grn_date" required="required"'); ?>
								</div>
                            </div>
                        </div>
                        
                        <div class="col-md-6" style="padding-bottom: 10px;">
                                <div class="form-group">
                                   <div class="col-md-5">
                                    <?= lang("po_number", "porefnumber"); ?>
									</div>
                                   <div class="col-md-7">
                                   
                                    <?php 
									
									foreach ($ponumber as $ponumber_row) {
									
										if($purchase->purchasesorders == $ponumber_row->id)
										{
                                        	$po_order_no = $ponumber_row->reference_no;
										}
                                    }
									
									
									echo form_input('poref_demo', ($po_order_no ? $po_order_no : ''), 'class="form-control input-tip" disabled required="required" id="poref_demo"'); 
									
									
									
									?>
                                   
                                    <?php
									
                                  /* $pn[''] = '';
                                    foreach ($ponumber as $ponumber_row) {
                                        $pn[$ponumber_row->id] = $ponumber_row->reference_no;
                                    }
                                    echo form_dropdown('poref', $pn, ($purchase->purchases_order_no ? $purchase->purchases_order_no : ''), ' class="form-control input-tip select" data-placeholder="' . lang("select") . ' ' . lang("po_number") . '  " style="width:100%;" id="porefnumber" ');*/
                                    ?>
                                    
									</div>
                                </div>
                            </div>
                        <div class="col-md-6" style="padding-bottom: 10px;">
                            <div class="form-group">
                                <div class="col-md-5">
                                 <?= lang("invoce_no", "invoce_no"); ?>
                                </div>
                               <div class="col-md-7">
                                <?php echo form_input('invoice_no', ($purchase->invoice_no ? $purchase->invoice_no : ''), 'class="form-control input-tip" id="invoice_no" required="required"'); ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6" style="padding-bottom: 10px;">
                            <div class="form-group">
                                <div class="col-md-5">
                                 <?= lang("invoce_date", "invoce_date"); ?>
                                </div>
                                <div class="col-md-7">
                                <?php  ?>
                                <?php echo form_input('invoice_date', ($purchase->invoice_date ? $purchase->invoice_date : ''), 'class="form-control input-tip today_date" id="invoicedate" required="required"'); ?>
                                </div>
                            </div>
                        </div>
                       
						
						<div class="col-md-6" style="padding-bottom: 10px;">
                            <div class="form-group">
                                <div class="col-md-5">
                                 <?= lang("po_date", "po_date"); ?>
								</div>
                               <div class="col-md-7">
                                <?php echo form_input('purchases_order_date', ($purchase->purchases_order_date ? $purchase->purchases_order_date : ''), 'class="form-control input-tip totay_date" id="purchases_order_date" required="required"'); ?>
								</div>
                            </div>
                        </div>
                        
                       
                        
                        
                        
						<div class="col-md-6" style="padding-bottom: 10px;">
                                        <div class="form-group">
                                           <div class="col-md-5">
                                            <?= lang("supplier", "posupplier"); ?>
											</div>
                                          
                                            <div class="col-md-7">
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
                          <div class="well col-xs-12">	
                           <div class="col-md-6" style="padding-bottom: 10px;">
                                <div class="form-group">
                                    <div class="col-md-5">
                                     <?= lang("supplier_name", ""); ?>
                                    </div>
                                    <div class="col-md-7">
                                    <input type="text" disabled name="supplier_name" id="supplier_name" class="form-control">
                                    </div>
                                </div>
                            </div>
                             <div class="col-md-6" style="padding-bottom: 10px;">
                                <div class="form-group">
                                    <div class="col-md-5">
                                     <?= lang("code", ""); ?>
                                    </div>
                                    <div class="col-md-7">
                                   <input type="text" disabled name="supplier_code" id="supplier_code" class="form-control">
                                    </div>
                                </div>
                            </div>
                             <div class="col-md-6" style="padding-bottom: 10px;">
                                <div class="form-group">
                                    <div class="col-md-5">
                                     <?= lang("address", ""); ?>
                                    </div>
                                    <div class="col-md-7">
                                    <input type="text" disabled name="supplier_address" id="supplier_address" class="form-control">
                                    </div>
                                </div>
                            </div>
                             <div class="col-md-6" style="padding-bottom: 10px;">
                                <div class="form-group">
                                    <div class="col-md-5">
                                     <?= lang("vat_no", ""); ?>
                                    </div>
                                    <div class="col-md-7">
                                    <input type="text" disabled name="supplier_vatno" id="supplier_vatno" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6" style="padding-bottom: 10px;">
                                <div class="form-group">
                                    <div class="col-md-5">
                                     <?= lang("email ", ""); ?>
                                    </div>
                                    <div class="col-md-7">
                                    <input type="text" disabled name="supplier_email" id="supplier_email" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6" style="padding-bottom: 10px;">
                                <div class="form-group">
                                    <div class="col-md-5">
                                     <?= lang("phone", ""); ?>
                                    </div>
                                    <div class="col-md-7">
                                    <input type="text" disabled name="supplier_phno" id="supplier_phno" class="form-control">
                                    </div>
                                </div>
                            </div>
                         </div>
						<div class="clearfix"></div>
                        
                        
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
                                    echo form_dropdown('warehouse', $wh, ($purchase->warehouse_id ? $purchase->warehouse_id : ''), 'id="powarehouse" class="form-control input-tip select" data-placeholder="' . lang("select") . ' ' . lang("warehouse") . '" required="required" style="width:100%;" ');
                                    ?>
									</div>
                                </div>
                            </div>
                       
						
						
						
                        <div class="col-md-6" style="padding-bottom: 10px;">
                            <div class="form-group">
                               <div class="col-md-5">
                                <?= lang("status", "postatus"); ?>
								</div>
                               <div class="col-md-7">
                                <?php
                                $post = array('received' => lang('received'), 'pending' => lang('pending'), 'ordered' => lang('ordered'));
                                echo form_dropdown('status', $post, ($purchase->status ? $purchase->status : ''), 'id="postatus" class="form-control input-tip select" data-placeholder="' . $this->lang->line("select") . ' ' . $this->lang->line("status") . '" required="required" style="width:100%;" ');
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
                                    class="panel-heading"><?= lang('please_select_these_before_adding_Purchase Items') ?></div>
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
                                <label class="table-label"><?= lang("order_items"); ?></label>

                                <div class="controls table-controls">
                                    <table id="poTable"
                                            class="table items  table-bordered table-condensed sortable_table" style="background:#fff">
                                        <thead>
                                        <tr>
                                            <th width="200"><?= lang('purchase_items') . ' (' . lang('code') .' - '.lang('name') . ')'; ?></th>
                                            <th class="col-md-2"><?= lang('batch_no'); ?></th>
                                            <?php
                                            if ($Settings->product_expiry) {
												 echo '<th class="col-md-2">' . $this->lang->line("mfg_date") . '</th>';
                                                echo '<th class="col-md-2">' . $this->lang->line("expiry_date") . '</th>';
                                            }
                                            ?>
											<th class="col-md-1"><?= lang("unit_cost"); ?>(<span
                                                    class="currency"><?= $default_currency->code ?></span>)</th>
                                            <th class="col-md-1"><?= lang("quantity"); ?></th>
                                            
                                             <th class="col-md-2"><?= lang("tax_method"); ?></th>
                                              <th class="col-md-2"><?= lang("tax_type"); ?></th>
                                              
                                        
                                            
                                            <?php
                                            
                                                echo '<th class="col-md-1">' . $this->lang->line("discount") . '</th>';
                                           
                                            ?>
											
											<th class="col-md-1"><?= lang("net_unit_cost"); ?></th>
                                            <th class="col-md-1"><?= lang("tax_amount"); ?></th>
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
				
                        </div>
                        <div class="clearfix"></div>
                        <input type="hidden" name="total_items" value="" id="total_items" required="required"/>

                        <div class="col-md-12">
                            <div class="form-group">
                                <input type="checkbox" class="checkbox" id="extras" checked value=""/>
                                <label for="extras" class="padding05"><?= lang('more_options') ?></label>
                            </div>
                            <div class="row" id="extras-con">
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
                                        <?= lang("discount_label", "podiscount"); ?>
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

                        </div>
                        <div class="col-md-12">
                            <div
                                class="from-group"><?php echo form_submit('add_pruchase', $this->lang->line("submit"), 'id="add_pruchase" class="btn btn-primary" style="padding: 6px 15px; margin:15px 0;"'); ?>
                                <button type="button" class="btn btn-danger" id="reset"><?= lang('reset') ?></button>
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
    var supplierid = localStorage.getItem('posupplier');
	if(posupplier != ''){
		$.ajax({
			type: 'get',
			url: '<?= admin_url('purchases/supplier'); ?>',
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
		url: '<?= admin_url('purchases/supplier'); ?>',
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

$(document).on('change', '#porefnumber', function(){
	
	if (localStorage.getItem('poitems')) {
        localStorage.removeItem('poitems');
    }
	if (localStorage.getItem('porefnumber')) {
        localStorage.removeItem('porefnumber');
    }
    if (localStorage.getItem('podiscount')) {
        localStorage.removeItem('podiscount');
    }
    if (localStorage.getItem('potax2')) {
        localStorage.removeItem('potax2');
    }
    if (localStorage.getItem('poshipping')) {
        localStorage.removeItem('poshipping');
    }
    
    if (localStorage.getItem('powarehouse')) {
        localStorage.removeItem('powarehouse');
    }
    if (localStorage.getItem('ponote')) {
        localStorage.removeItem('ponote');
    }
    if (localStorage.getItem('posupplier')) {
        localStorage.removeItem('posupplier');
    }
    if (localStorage.getItem('pocurrency')) {
        localStorage.removeItem('pocurrency');
    }
    if (localStorage.getItem('poextras')) {
        localStorage.removeItem('poextras');
    }
    if (localStorage.getItem('purchases_order_date')) {
        localStorage.removeItem('purchases_order_date');
    }
	if (localStorage.getItem('invoice_no')) {
        localStorage.removeItem('invoice_no');
    }
	 if (localStorage.getItem('invoice_date')) {
        localStorage.removeItem('invoice_date');
    }
    if (localStorage.getItem('postatus')) {
        localStorage.removeItem('postatus');
    }
    if (localStorage.getItem('popayment_term')) {
		localStorage.removeItem('postatus');
	}
	var porefnumber = $(this).val();
	$.ajax({
		type: 'get',
		url: '<?= admin_url('purchases/purchase_list'); ?>',
		dataType: "json",
		data: {
			poref: porefnumber
		},
		success: function (data) {
			var purchases_value = [];
			$(this).removeClass('ui-autocomplete-loading');
			var items = JSON.stringify(data.value['purchasesitem']);
			
			var purchases = JSON.stringify(data.value['purchases']);
			purchases_value = $.parseJSON(purchases);

			localStorage.setItem('porefnumber',  purchases_value["id"]);
			localStorage.setItem('powarehouse', purchases_value["warehouse_id"]);
			localStorage.setItem('ponote', purchases_value["note"]);
			localStorage.setItem('podiscount', purchases_value["order_discount_id"]);
			localStorage.setItem('potax2', purchases_value["order_tax_id"]);
			localStorage.setItem('poshipping', purchases_value["shipping"]);
			localStorage.setItem('posupplier', purchases_value["customer_id"]);
			localStorage.setItem('poitems', items);
			localStorage.setItem('purchases_order_date', purchases_value["purchases_order_date"]);
			
			window.location.reload();
			
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
