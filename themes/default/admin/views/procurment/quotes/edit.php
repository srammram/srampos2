<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<script type="text/javascript">
    var default_store = '<?=$default_store?>';
    
    <?php if($inv) { ?>
    localStorage.setItem('qu_warehouse', '<?= $inv->warehouse_id ?>');
	localStorage.setItem('qu_requestnumber', '<?= $inv->request_id ?>');
	localStorage.setItem('qu_requestdate', '<?= $inv->requestdate ?>');
    localStorage.setItem('qu_note', '<?= $inv->note; ?>');
    localStorage.setItem('qu_discount', '<?= $inv->order_discount_id ?>');
    localStorage.setItem('qu_tax2', '<?= $inv->order_tax_id ?>');
    localStorage.setItem('qu_shipping', '<?= $inv->shipping ?>');
    <?php if ($inv->supplier_id) { ?>
        localStorage.setItem('qu_supplier', '<?= $inv->supplier_id ?>');
    <?php } ?>
    localStorage.setItem('qu_items', JSON.stringify(<?= $inv_items; ?>));
    <?php } ?>

    var count = 1, an = 1, quotes_edit = false, product_variant = 0, DT = <?= $Settings->default_tax_rate ?>, DC = '<?= $default_currency->code ?>', shipping = 0,
        product_tax = 0, invoice_tax = 0, total_discount = 0, total = 0,
        tax_rates = <?php echo json_encode($tax_rates); ?>, qu_items = {},
        audio_success = new Audio('<?= $assets ?>sounds/sound2.mp3'),
        audio_error = new Audio('<?= $assets ?>sounds/sound3.mp3');
    $(document).ready(function () {
        <?php if($this->input->get('supplier')) { ?>
        if (!localStorage.getItem('qu_items')) {
            localStorage.setItem('qu_supplier', <?=$this->input->get('supplier');?>);
        }
        <?php } ?>
        <?php //if ($Owner || $Admin) { ?>
        if (!localStorage.getItem('qu_date')) {
            $("#podate").datetimepicker({
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
        $(document).on('change', '#podate', function (e) {
            localStorage.setItem('qu_date', $(this).val());
        });
        if (podate = localStorage.getItem('qu_date')) {
            $('#podate').val(podate);
        }
		
		$("#qu_requestnumber").val(localStorage.getItem('qu_requestnumber'));
		$("#qu_requestdate").val(localStorage.getItem('qu_requestdate'));
		
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
        if (!localStorage.getItem('qu_tax2')) {
            localStorage.setItem('qu_tax2', <?=$Settings->default_tax_rate2;?>);
            setTimeout(function(){ $('#extras').iCheck('check'); }, 1000);
        }
        ItemnTotals();
        $("#add_item").autocomplete({
            // source: '<?= admin_url('procurment/quotes/suggestions'); ?>',
            source: function (request, response) {
                $.ajax({
                    type: 'get',
                    url: '<?= admin_url('procurment/quotes/suggestions'); ?>',
                    dataType: "json",
                    data: {
                        term: request.term,
                        supplier_id: $("#qu_supplier").val()
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
                    var row = add_quotes_item(ui.item);
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
                        row = add_quotes_item(data.result);
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
    <div class="box-header  procurment-header">
        <h2 class=""><?= lang('edit_purchase_quotes'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <!-- <p class="introtext"><?php echo lang('enter_info'); ?></p> -->
                <?php
                $attrib = array('data-toggle' => 'validator1', 'role' => 'form','id'=>'add-purchase-quotation');
                echo admin_form_open_multipart("procurment/quotes/edit/".$id, $attrib)
                ?>
                <div class="row">
                <div class="col-lg-12" style="background:#b1d7fd; padding:15px 15px;">                       
                        <!-- <h2>Quotes Details</h2> -->    
                        <?php echo form_submit('add_quotes', $this->lang->line("update"), 'id="add_quotes" class="btn col-lg-1 btn-sm btn-primary pull-right" '); ?>
                        <button type="button" class="btn btn-sm btn-danger col-lg-1 pull-right" id="reset" style="display: none;margin-right:15px;height:30px!important;font-size: 12px!important"><?= lang('reset'); ?></button>

                        <input type="hidden" name="warehouse" id="reqwarehouse" value="<?php echo $inv->warehouse ? $inv->warehouse : $Settings->default_warehouse ?>"> 
                        <input type="hidden" name="biller" id="reqbiller" value="<?php echo $inv->biller ? $inv->biller : $Settings->default_biller ?>">

                        <input type="hidden" name="reference_no" id="reference_no" value="<?php echo $inv->reference_no ? $inv->reference_no : '' ?>">
                    <table class="table custom_tables">
                        <tbody>
                            <tr> 
                              <td>
                                    <?= lang("date", "date") ?>
                              </td>
                              <td>
                                <input type="datetime" name="date" id="qu_date" readonly class="form-control" value="<?php echo $inv->date ?>">
                               </td>
                               <td>
                                    <?= lang("quotation_no", "quotation_no") ?>
                                </td>
                                <td>
                                   <input type="text" name="quotation_no" id="quotation_no" readonly class="form-control" value="<?php echo $inv->reference_no; ?>">
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
                                    echo form_dropdown('currency', $c, $inv->currency, 'id="reqcurrency" data-placeholder="' . $this->lang->line("select") . ' ' . $this->lang->line("currencies") . '" required="required" class="form-control input-tip" style="width:100%;"');
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td width="100px">
                                    <?= lang("supplier", "qu_supplier"); ?>
                                </td>
                                <td width="350px">
                                    <?php
                                    $sl = array();
                                    foreach ($suppliers as $supplier) {
                                        $sl[$supplier->id] = $supplier->name;
                                    }
                                    echo form_dropdown('supplier', $sl, $inv->supplier_id, 'id="qu_supplier" data-placeholder="' . $this->lang->line("select") . ' ' . $this->lang->line("supplier") . '" required="required" class="form-control input-tip select" style="width:100%;"');
                                    ?>
                                </td>
                                <td width="100px">
                                    <?= lang("supplier_address", "supplier_address") ?>
                                </td>
                                <td width="350px">
                                    <input  name="supplier_address" id="qutsupplier_address" readonly class="form-control" value="<?php echo $inv->supplier_address ?>">
                                </td>
                                <td width="100px">
                                    <?= lang("status", "qu_status"); ?>
                                </td>
                                <td width="350px">                                        
                                <?php $st['process'] = lang('process');
                                    if($this->siteprocurment->hasApprovedPermission()){
                                        $st['approved'] = lang('approved'); 
                                    }
                                   echo form_dropdown('status', $st, $inv->status, 'class="form-control input-tip" id="qu_status"'); ?>
                                </td>                                    
                           </tr>
                           <tr>
                               <td>
                                    <?= lang("request_date", "request_date") ?>
                                </td>
                                <td>
                                <input type="datetime" name="requestdate" id="qu_requestdate" readonly class="form-control" value="<?php echo $inv->requestdate ?>">
                                </td>
                                <td width="150px">
                                    <?= lang("quotation_request_number", "quotation_request_number"); ?>
                                </td>
                                <td width="150px">
                                     <?php                        
                                       $qn[''] = '';
                                        foreach ($requestnumber as $requestnumber_row) {
                                            $qn[$requestnumber_row->id] = $requestnumber_row->reference_no;
                                        }
                                        echo form_dropdown('requestnumber', $qn, $inv->request_id, ' class="form-control input-tip select" data-placeholder="' . lang("select") . ' ' . lang("request_number") . '"style="width:100%;" id="qu_requestnumber"  ');
                                        ?>
                                </td> 
                                <td>
                                    <?= lang("document", "document") ?>
                                </td>
                                <td>
                                    <input id="document" type="file" data-browse-label="" name="document" data-show-upload="false"
                                   data-show-preview="false" class="form-control file">
                                </td>                                  
                           </tr>
                           <tr>
                                <td> <?= lang("store_request_no", "store_request_no");?></td>
                                <td>
                                     <input type="text" name="store_request_no" id="store_request_no"  readonly="" class="form-control">
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
                                <label class="table-label"><?= lang("items"); ?></label>
                                <div class="controls table-controls">
                                    <table id="quotesTable"
                                            class="table items  table-bordered table-condensed sortable_table" style="background:#fff">
                                        <thead>
                                        <tr>
                                            <th class="col-md-1"><?= lang("S.NO"); ?></th>
                                            <th class="col-md-2"><?= lang("code"); ?></th>
                                            <th class="col-md-2"><?= lang('product') . lang('name'); ?></th>
											<th class="col-md-2"><?= lang("Category"); ?></th>
                                            <th class="col-md-2"><?= lang("Subcategory"); ?></th>
                                            <th class="col-md-2"><?= lang("Brand"); ?></th>
                                            <th class="col-md-2"><?= lang("Cost.Price"); ?></th>
                                            <th class="col-md-2"><?= lang("Quantity"); ?></th>
                                            <th class="col-md-2"><?= lang("Selling.Price"); ?></th>
											<th class="col-md-1" style="text-align: center;">
                                                  <i class="fa fa-trash-o"
                                                    style="opacity:0.5; filter:alpha(opacity=50);"></i></th>
                                        </tr>
                                        </thead>
                                        <tbody></tbody>
                                        <!-- <tfoot></tfoot> -->
                                    </table>                                    
                                </div>                                
                            </div>
                        </div>                        
                       </div>

                        <div class="col-lg-12" style="background:#a6f7a1; margin-top:15px;">
                            <table class="table custom_tables" >
                                <tbody>
                                    <tr>                                    
                                        <td width="150px">
                                            <?= lang("Total No Of Items", "total_items") ?>
                                        </td>
                                        <td width="150px">
                                            <input  name="total_no_items" id="total_no_items" readonly class="form-control">
                                        </td>
					 <td width="150px">
                                            <?= lang("Total No Of Qty", "total_qty") ?>
                                        </td>
                                        <td width="150px">
                                            <input  name="total_no_qty" id="total_no_qty" readonly class="form-control">
                                        </td>
                                         <td width="100px">                                    
                                        </td>
                                        <td width="100px">                                    
                                        </td>                                    
                                        <td>
                                            <?= lang("Remarks/Note", "note") ?>
                                        </td>
                                        <td>
                                            <input  name="note" id="reqnote" class="form-control" value="<?=$inv->note?>" >
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

<script>
$(document).ready(function(e) {
     $('#qu_requestnumber').prop('readonly', true);

    var supplierid = localStorage.getItem('qu_supplier');
	
	if(supplierid != ''){
		$.ajax({
			type: 'get',
			url: '<?= admin_url('procurment/quotes/supplier'); ?>',
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


$(document).on('change', '#qu_supplier', function(){
	var qu_supplier = $(this).val();
	$.ajax({
		type: 'get',
		url: '<?= admin_url('procurment/quotes/supplier'); ?>',
		dataType: "json",
		data: {
			supplier_id: qu_supplier
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


$(document).on('change', '#qu_requestnumber', function(){
	
	if (localStorage.getItem('qu_items')) {
        localStorage.removeItem('qu_items');
    }
	if (localStorage.getItem('qu_requestnumber')) {
        localStorage.removeItem('qu_requestnumber');
    }
	if (localStorage.getItem('qu_requestdate')) {
        localStorage.removeItem('qu_requestdate');
    }
    if (localStorage.getItem('qu_discount')) {
        localStorage.removeItem('qu_discount');
    }
    if (localStorage.getItem('qu_tax2')) {
        localStorage.removeItem('qu_tax2');
    }
    if (localStorage.getItem('qu_shipping')) {
        localStorage.removeItem('qu_shipping');
    }
    if (localStorage.getItem('qu_ref')) {
        localStorage.removeItem('qu_ref');
    }
    if (localStorage.getItem('qu_warehouse')) {
        localStorage.removeItem('qu_warehouse');
    }
    if (localStorage.getItem('qu_note')) {
        localStorage.removeItem('qu_note');
    }
    if (localStorage.getItem('qu_supplier')) {
        localStorage.removeItem('qu_supplier');
    }
    if (localStorage.getItem('qu_currency')) {
        localStorage.removeItem('qu_currency');
    }
    if (localStorage.getItem('qu_extras')) {
        localStorage.removeItem('qu_extras');
    }
    if (localStorage.getItem('qu_date')) {
        localStorage.removeItem('qu_date');
    }
    if (localStorage.getItem('qu_status')) {
        localStorage.removeItem('qu_status');
    }
    if (localStorage.getItem('qu_payment_term')) {
        localStorage.removeItem('qu_payment_term');
    }
	
	var qu_requestnumber = $(this).val();
	
	$.ajax({
		type: 'get',
		url: '<?= admin_url('procurment/quotes/quotes_list'); ?>',
		dataType: "json",
		data: {
			poref: qu_requestnumber
		},
		success: function (data) {
			
			var quotes_value = [];
			$(this).removeClass('ui-autocomplete-loading');
			var items = JSON.stringify(data.value['quotesitem']);
			
			var quotes = JSON.stringify(data.value['quotes']);
			quotes_value = $.parseJSON(quotes);
			

			localStorage.setItem('qu_requestnumber',  quotes_value["id"]);
			localStorage.setItem('qu_requestdate',  quotes_value["date"]);
			localStorage.setItem('qu_warehouse', quotes_value["warehouse_id"]);
			localStorage.setItem('qu_note', quotes_value["note"]);
			localStorage.setItem('qu_discount', 0);
			localStorage.setItem('qu_tax2', quotes_value["order_tax_id"]);
			localStorage.setItem('qu_shipping', quotes_value["shipping"]);
			localStorage.setItem('qu_supplier', quotes_value["supplier_id"]);
			localStorage.setItem('qu_items', items);
			localStorage.setItem('quotes_date', quotes_value["quotes_date"]);
			
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
