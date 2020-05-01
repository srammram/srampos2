<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<script type="text/javascript">
    var default_store = '<?=$default_store?>';
    <?php if($quotes_id) { ?>
    localStorage.setItem('qu_warehouse', '<?= $quotes->warehouse_id ?>');
	localStorage.setItem('qu_requestnumber', '<?= $quotes->requestnumber ?>');
	localStorage.setItem('qu_requestdate', '<?= $quotes->requestdate ?>');
    localStorage.setItem('qu_note', '<?= str_replace(array("\r", "\n"), "", $this->sma->decode_html($quotes->note)); ?>');
    localStorage.setItem('qu_discount','<?= $quotes->order_discount_id ?>');
    localStorage.setItem('qu_tax2', '<?= $quotes->order_tax_id ?>');
    localStorage.setItem('qu_shipping', '<?= $quotes->shipping ?>');
    <?php if ($quotes->supplier_id) { ?>
        localStorage.setItem('qu_supplier', '<?= $quotes->supplier_id ?>');
    <?php } ?>
    localStorage.setItem('qu_items', JSON.stringify(<?= $quote_items; ?>));
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
        $("#qu_date").prop('disabled', true);
        if (!localStorage.getItem('qu_date')) {            
            $("#qu_date").datetimepicker({
                format: site.dateFormats.js_ldate,
                fontAwesome: true,
                pickDate: false, 
                ignoreReadonly: true,
                enableOnReadonly: false,
                language: 'common',
                weekStart: 1,
                todayBtn: 1,
                autoclose: 1,
                todayHighlight: 1,
                startView: 2,
                forceParse: 0
            }).datetimepicker('update', new Date());
        }
        $(document).on('change', '#qu_date', function (e) {
            localStorage.setItem('qu_date', $(this).val());
        });
        if (podate = localStorage.getItem('qu_date')) {
            $('#qu_date').val(podate);
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
                    var row = add_quotes_item(ui.item);
                    if (row)
                        $(this).val('');
                } else {
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
        <h2 class=""><?= lang('add_purchase_quotes'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
    <?php $attrib = array('data-toggle' => 'validator1', 'role' => 'form','id'=>'add-purchase-invoice');
     echo admin_form_open_multipart("procurment/quotes/add", $attrib)   ?>
                <div class="row">
                    <div class="col-lg-12" style="background:#b1d7fd; padding:15px 15px;">
                        <?php echo form_submit('add_request', $this->lang->line("save"), 'id="add_request" class="btn col-lg-1 btn-sm btn-primary pull-right" '); ?>
                        <button type="button" class="btn col-lg-1 btn-sm btn-danger pull-right" id="reset" style="margin-right:15px;height:30px!important;font-size: 12px!important"><?= lang('Reset'); ?></button>
                        <input type="hidden" name="warehouse" id="reqwarehouse" value="<?php echo $Settings->default_warehouse ?>"> 
                        <input type="hidden" name="biller" id="reqbiller" value="<?php echo $Settings->default_biller ?>"> 
                        <table class="table custom_tables">
                            <tbody>
                                <tr>  
                                    <td>
                                        <?= lang("date", "date") ?>
                                    </td>
                                    <td>
                                       <input type="datetime" name="date" id="qu_date" readonly class="form-control" value="<?php echo date('Y-m-d') ?>">
                                    </td>
                                    <td>
                                        <?= lang("quotation_no", "quotation_no") ?>
                                    </td>
                                    <td>
                                        <?php 
                                        $n = $this->siteprocurment->lastidQuotation();
                                        $reference = str_pad($n + 1, 5, 0, STR_PAD_LEFT);
                                        ?>
                                       <input type="text" name="quotation_no" id="quotation_no" readonly class="form-control" value="<?php echo $reference; ?>">
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
                                        echo form_dropdown('currency', $c, (isset($_POST['currency']) ? $_POST['currency'] : $Settings->default_currency), 'id="reqcurrency" data-placeholder="' . $this->lang->line("select") . ' ' . $this->lang->line("currencies") . '" required="required" class="form-control input-tip" style="width:100%;"');
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
                                        echo form_dropdown('supplier', $sl, '', 'id="qu_supplier" data-placeholder="' . $this->lang->line("select") . ' ' . $this->lang->line("supplier") . '" placeholder="' . $this->lang->line("select") . ' ' . $this->lang->line("supplier") . '"required="required" class="form-control input-tip" style="width:100%;"');
                                        ?>
                                    </td>
                                    <td width="100px">
                                        <?= lang("supplier_address", "supplier_address") ?>
                                    </td>
                                    <td width="350px">
                                        <input  name="supplier_address" id="qutsupplier_address" readonly class="form-control" value="">
                                    </td>
                                     <td width="100px">
                                        <?= lang("status", "qu_status"); ?>
                                    </td>
                                    <td width="350px">
                                        
                                    <?php $st['process'] = lang('process');
                                        if($this->siteprocurment->hasApprovedPermission()){
                                            $st['approved'] = lang('approved'); 
                                        }
                                       echo form_dropdown('status', $st, '', 'class="form-control input-tip" id="qu_status"'); ?>
                                    </td>
                                </tr>
                                <tr>
                                     <td>
                                        <?= lang("request_date_and_time", "request_date_and_time") ?>
                                    </td>
                                     <td>
                                     <input type="datetime" name="requestdate" id="qu_requestdate" readonly class="form-control" value="<?php echo date('Y-m-d') ?>">
                                    </td>
                                    <td width="150px">
                                        <?= lang("request_number", "request_number"); ?>
                                    </td>
                                    <td>
                                       <?php                        
                                       
                                            foreach ($requestnumber as $requestnumber_row) {
                                                $qn[$requestnumber_row->id] = $requestnumber_row->reference_no;
                                            }
                                            echo form_dropdown('requestnumber[]', $qn, '', ' class="form-control input-tip select" multiple data-placeholder="' . lang("select") . ' ' . lang("request_number") . '"style="width:100%;" id="qu_requestnumber"  ');
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
                                         <a href="javascript:void(0);" ><i
                                                    class="fa fa-2x fa-search addIcon" id="addIcon"></i></a></div>
                                        
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
                                            class="table items  table-bordered table-condensed sortable_table"  style="background:#fff;">
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
                                            <th class="col-md-2"><?= lang("uom"); ?></th>
                                            <th class="col-md-2"><?= lang("Selling.Price"); ?></th>
											  <th class="col-md-2"><?= lang("Delivery_to_warehouse"); ?></th>
                                            <th class="col-md-1" style="text-align: center;">
                                              <i class="fa fa-trash-o" style="opacity:0.5; filter:alpha(opacity=50);"></i>
                                            </th>
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
                                            <input  name="note" id="reqnote" class="form-control" >
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
    var supplierid = localStorage.getItem('qu_supplier');	
    $('#qu_supplier').val(supplierid);
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
            localStorage.setItem('qutsupplier_address', data.supplier_address);
            $('#qutsupplier_address').val(data.supplier_address);
			      
		}
	});
    localStorage.setItem('qu_supplier', $(this).val());
});
if (localStorage.getItem('qutsupplier_address')) {
   $('#qutsupplier_address').val(localStorage.getItem('qutsupplier_address'));
 } 
$(document).ready(function(e) {
	if(localStorage.getItem('qu_requestnumber') == null){
    	localStorage.setItem('qu_requestnumber', '<?= $ref_requestnumber ?>');
		$("#qu_requestnumber").val(localStorage.getItem('qu_requestnumber'));
		$('#qu_requestnumber').trigger('change');
	}else{
		qu_requestnumber=localStorage.getItem('qu_requestnumber')
		qu_requestnumber=qu_requestnumber.split(",");
		$("#qu_requestnumber").val(qu_requestnumber);
		
		
	}
});
$(document).on('change', '#qu_requestnumber', function(e){
	if ($('#qu_supplier').val()!=null) {
            $('#qu_supplier').select2("readonly", true);
			localStorage.setItem('qu_requestnumber', $(this).val());
        } else {
		    $("#qu_requestnumber").select2("val", "");
            bootbox.alert(lang.select_supplier);
            item = null;
            return;
        }

	//var qu_requestnumber = $(this).val();
	var qu_requestnumber =e.added.id;
	$.ajax({
		type: 'get',
		url: '<?= admin_url('procurment/quotes/quotes_list'); ?>',
		dataType: "json",
		data: {
			poref: qu_requestnumber
		},
		success: function (data) {
			$(this).removeClass('ui-autocomplete-loading');
		     $.each(data, function (key, val) {
              add_quotes_item(val);
			});
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


