<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<script type="text/javascript">
       
    
    <?php if($store_receivers_id) { ?>
    localStorage.setItem('store_rec_warehouse', '<?= $store_receivers->warehouse_id ?>');
	localStorage.setItem('store_rec_to_store_id', '<?= $store_receivers->to_store_id ?>');
	localStorage.setItem('store_rec_from_store_id', '<?= $store_receivers->from_store_id ?>');
	
	localStorage.setItem('store_rec_requestnumber', '<?= $store_receivers->requestnumber ?>');
	localStorage.setItem('store_rec_requestdate', '<?= $store_receivers->requestdate ?>');
	localStorage.setItem('store_rec_type', '<?= $store_receivers->request_type ?>');
    localStorage.setItem('store_rec_note', '<?= str_replace(array("\r", "\n"), "", $this->sma->decode_html($store_receivers->note)); ?>');
    localStorage.setItem('store_rec_discount', '<?= $store_receivers->order_discount_id ?>');
    localStorage.setItem('store_rec_tax2', '<?= $store_receivers->order_tax_id ?>');
    localStorage.setItem('store_rec_shipping', '<?= $store_receivers->shipping ?>');
    <?php if ($store_receivers->supplier_id) { ?>
        localStorage.setItem('store_rec_supplier', '<?= $store_receivers->supplier_id ?>');
    <?php } ?>
    localStorage.setItem('store_rec_items', JSON.stringify(<?= $store_receiver_items; ?>));
    <?php } ?>

    var count = 1, an = 1, store_receivers_edit = false, product_variant = 0, DT = <?= $Settings->default_tax_rate ?>, DC = '<?= $default_currency->code ?>', shipping = 0,
        product_tax = 0, invoice_tax = 0, total_discount = 0, total = 0,
        tax_rates = <?php echo json_encode($tax_rates); ?>, store_rec_items = {},
        audio_success = new Audio('<?= $assets ?>sounds/sound2.mp3'),
        audio_error = new Audio('<?= $assets ?>sounds/sound3.mp3');
    $(document).ready(function () {
        <?php if($this->input->get('supplier')) { ?>
        if (!localStorage.getItem('store_rec_items')) {
            localStorage.setItem('store_rec_supplier', <?=$this->input->get('supplier');?>);
        }
        <?php } ?>
        <?php //if ($Owner || $Admin) { ?>
        if (!localStorage.getItem('store_rec_date')) {
            $("#store_rec_date").datetimepicker({
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
        $(document).on('change', '#store_rec_date', function (e) {
            localStorage.setItem('store_rec_date', $(this).val());
        });
        if (store_rec_date = localStorage.getItem('store_rec_date')) {
            $('#store_rec_date').val(store_rec_date);
        }
		$("#store_rec_requestnumber").val(localStorage.getItem('store_rec_requestnumber'));
		$("#store_rec_requestdate").val(localStorage.getItem('store_rec_requestdate'));
		$("#store_rec_type").val(localStorage.getItem('store_rec_type'));
		
		$("#store_rec_from_store_id").val(localStorage.getItem('store_rec_from_store_id'));
		$("#store_rec_to_store_id").val(localStorage.getItem('store_rec_to_store_id'));
		
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
        if (!localStorage.getItem('store_rec_tax2')) {
            localStorage.setItem('store_rec_tax2', <?=$Settings->default_tax_rate2;?>);
            setTimeout(function(){ $('#extras').iCheck('check'); }, 1000);
        }
        ItemnTotals();
        $("#add_item").autocomplete({
            // source: '<?= admin_url('procurment/store_receivers/suggestions'); ?>',
            source: function (request, response) {
                $.ajax({
                    type: 'get',
                    url: '<?= admin_url('procurment/store_receivers/suggestions'); ?>',
                    dataType: "json",
                    data: {
                        term: request.term,
                        supplier_id: $("#store_rec_supplier").val()
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
                    var row = add_store_receivers_item(ui.item);
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
                        row = add_store_receivers_item(data.result);
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
		
		var store_rec_from_store_id;
        $('#store_rec_from_store_id').on("select2-focus", function (e) {
            store_rec_from_store_id = $(this).val();
        }).on("select2-close", function (e) {
            if ($(this).val() != '' && $(this).val() == $('#store_rec_to_store_id').val()) {
                $(this).select2('val', store_rec_from_store_id);
                bootbox.alert('<?= lang('please_select_different_store') ?>');
            }
        });
        var store_rec_to_store_id;
        $('#store_rec_to_store_id').on("select2-focus", function (e) {
            store_rec_to_store_id = $(this).val();
        }).on("select2-close", function (e) {
            if ($(this).val() != '' && $(this).val() == $('#store_rec_from_store_id').val()) {
                $(this).select2('val', store_rec_to_store_id);
                bootbox.alert('<?= lang('please_select_different_store') ?>');
            }
        });
		
    });

</script>

<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('add_store_receivers'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?php echo lang('enter_info'); ?></p>
                <?php
                $attrib = array('data-toggle' => 'validator1', 'role' => 'form');
                echo admin_form_open_multipart("procurment/store_receivers/add", $attrib)
                ?>


                <div class="row">
                    <div class="col-lg-12">
						
                       
                        <h2>Store_receivers Details</h2>

                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang("Date", "date") ?>
                                <input type="datetime" name="date" id="store_rec_date" readonly class="form-control" value="<?php echo date('Y-m-d H:i:s') ?>">
                                
                            </div>
                        </div>
                      
                    
                    <div class="col-md-4">
                        <div class="form-group">
                           
                            <?= lang("Request_number", "store_rec_requestnumber"); ?>
                            
                           
                            <?php
                        
                           $qn[''] = '';
                            foreach ($requestnumber as $requestnumber_row) {
                                $qn[$requestnumber_row->id] = $requestnumber_row->reference_no;
                            }
                            echo form_dropdown('requestnumber', $qn, (isset($_POST['requestnumber']) ? $_POST['requestnumber'] : '' ), ' class="form-control input-tip select" data-placeholder="' . lang("select") . ' ' . lang("request_number") . '"style="width:100%;" id="store_rec_requestnumber"  ');
                            ?>
                            </div>
                        
                    </div>
                    
                    <div class="col-md-4">
                            <div class="form-group">
                                <?= lang("Request Date", "date") ?>
                                <input type="datetime" name="requestdate" id="store_rec_requestdate" readonly class="form-control" value="<?php echo date('Y-m-d H:i:s') ?>">
                                
                            </div>
                        </div>
                     
                        
                        <input type="hidden" name="request_type" id="store_rec_type" value="<?php echo 'new' ?>">
                        
                        <div class="col-md-4" >
                            <div class="form-group">
                                <?= lang("from_store", "store_rec_from_store_id"); ?>
                                <?php
                                $fst[''] = '';
                                foreach ($stores as $store) {
                                    $fst[$store->id] = $store->name;
                                }
                                echo form_dropdown('from_store_id', $fst, (isset($_POST['from_store_id']) ? $_POST['from_store_id'] : ''), 'id="store_rec_from_store_id" class="form-control input-tip select" data-placeholder="' . lang("select") . ' ' . lang("from_store") . '" required="required" style="width:100%;" ');
                                ?>
                            </div>
                        </div>
                        
                        <div class="col-md-4" >
                            <div class="form-group">
                                <?= lang("to_store", "store_rec_to_store_id"); ?>
                                <?php
                                $tst[''] = '';
                                foreach ($stores as $store) {
                                    $tst[$store->id] = $store->name;
                                }
                                echo form_dropdown('to_store_id', $tst, (isset($_POST['to_store_id']) ? $_POST['to_store_id'] : ''), 'id="store_rec_to_store_id" class="form-control input-tip select" data-placeholder="' . lang("select") . ' ' . lang("to_store") . '" required="required" style="width:100%;" ');
                                ?>
                            </div>
                        </div>
                        
                        <input type="hidden" name="warehouse" id="store_rec_warehouse" value="<?php echo $Settings->default_warehouse ?>">  
                         
                            
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                   
                                    <?= lang("document", "document") ?>
                                   
                                    <input id="document" type="file" data-browse-label="<?= lang('browse'); ?>" name="document" data-show-upload="false"
                                           data-show-preview="false" class="form-control file">
                                   
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <?= lang("status", "store_rec_status"); ?>
                                    <?php $st['process'] = lang('process');
								if($this->siteprocurment->GETaccessModules('store_received_approved')){
									$st['approved'] = lang('approved');	
								}
                                    echo form_dropdown('status', $st, '', 'class="form-control input-tip" id="store_rec_status"'); ?>
    
                                </div>
                            </div>
                             
                          <div class="clearfix"></div>	
                      


                        <div class="col-md-12" id="sticker">
                       
                            <div class="well well-sm">
                              
                                <div class="form-group" style="margin-bottom:0;">
                                    <div class="input-group wide-tip">
                                        <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;">
                                            <i class="fa fa-2x fa-barcode addIcon"></i></a></div>
                                        <?php echo form_input('add_item', '', 'class="form-control input-lg" id="add_item" placeholder="' . $this->lang->line("add_Purchase Items_to_order") . '"'); ?>
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
                                    <table id="store_receiversTable"
                                            class="table items  table-bordered table-condensed sortable_table" style="background:#fff">
                                        <thead>
                                        <tr>
                                            <th width="200"><?= lang('product') . ' (' . lang('code') .' - '.lang('name') . ')'; ?></th>
											
                                            <th class="col-md-1"><?= lang("request_quantity"); ?></th>
                                            
                                             <th class="col-md-2"><?= lang("available_quantity"); ?></th>
                                              <th class="col-md-2"><?= lang("transfer_quantity"); ?></th>
                                              <th class="col-md-2"><?= lang("pending_quantity"); ?></th>
                                              
                                        
                                           
											
											
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
							
								
				
                        </div>
                        <div class="clearfix"></div>
                        
                        
                        <div class="col-md-12">
                            <div
                                class="from-group"><?php echo form_submit('add_store_receivers', $this->lang->line("submit"), 'id="add_store_receivers" class="btn btn-primary" style="padding: 6px 15px; margin:15px 0;"'); ?>
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
    var supplierid = localStorage.getItem('store_rec_supplier');
	
	if(supplierid != ''){
		$.ajax({
			type: 'get',
			url: '<?= admin_url('procurment/store_receivers/supplier'); ?>',
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


$(document).on('change', '#store_rec_supplier', function(){
	var store_rec_supplier = $(this).val();
	$.ajax({
		type: 'get',
		url: '<?= admin_url('procurment/store_receivers/supplier'); ?>',
		dataType: "json",
		data: {
			supplier_id: store_rec_supplier
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
	if(localStorage.getItem('store_rec_requestnumber') == null){
    	localStorage.setItem('store_rec_requestnumber', '<?= $ref_requestnumber ?>');
		$("#store_rec_requestnumber").val(localStorage.getItem('store_rec_requestnumber'));
		$('#store_rec_requestnumber').trigger('change');
	}
});
<?php
}
?>

$(document).on('change', '#store_rec_requestnumber', function(){
	
	if (localStorage.getItem('store_rec_items')) {
        localStorage.removeItem('store_rec_items');
    }
	if (localStorage.getItem('store_rec_requestnumber')) {
        localStorage.removeItem('store_rec_requestnumber');
    }
	if (localStorage.getItem('store_rec_requestdate')) {
        localStorage.removeItem('store_rec_requestdate');
    }
	if (localStorage.getItem('store_rec_type')) {
        localStorage.removeItem('store_rec_type');
    }
    if (localStorage.getItem('store_rec_discount')) {
        localStorage.removeItem('store_rec_discount');
    }
    if (localStorage.getItem('store_rec_tax2')) {
        localStorage.removeItem('store_rec_tax2');
    }
    if (localStorage.getItem('store_rec_shipping')) {
        localStorage.removeItem('store_rec_shipping');
    }
    if (localStorage.getItem('store_rec_ref')) {
        localStorage.removeItem('store_rec_ref');
    }
    if (localStorage.getItem('store_rec_warehouse')) {
        localStorage.removeItem('store_rec_warehouse');
    }
    if (localStorage.getItem('store_rec_note')) {
        localStorage.removeItem('store_rec_note');
    }
    if (localStorage.getItem('store_rec_supplier')) {
        localStorage.removeItem('store_rec_supplier');
    }
    if (localStorage.getItem('store_rec_currency')) {
        localStorage.removeItem('store_rec_currency');
    }
    if (localStorage.getItem('store_rec_extras')) {
        localStorage.removeItem('store_rec_extras');
    }
    if (localStorage.getItem('store_rec_date')) {
        localStorage.removeItem('store_rec_date');
    }
    if (localStorage.getItem('store_rec_status')) {
        localStorage.removeItem('store_rec_status');
    }
	
	 if (localStorage.getItem('store_rec_from_store_id')) {
        localStorage.removeItem('store_rec_from_store_id');
    }
	 if (localStorage.getItem('store_rec_to_store_id')) {
        localStorage.removeItem('store_rec_to_store_id');
    }
	
    if (localStorage.getItem('store_rec_payment_term')) {
        localStorage.removeItem('store_rec_payment_term');
    }
	
	var store_rec_requestnumber = $(this).val();
	
	$.ajax({
		type: 'get',
		url: '<?= admin_url('procurment/store_receivers/store_receivers_list'); ?>',
		dataType: "json",
		data: {
			poref: store_rec_requestnumber
		},
		success: function (data) {
			
			var store_receivers_value = [];
			$(this).removeClass('ui-autocomplete-loading');
			var items = JSON.stringify(data.value['store_receiversitem']);
			
			var store_receivers = JSON.stringify(data.value['store_receivers']);
			store_receivers_value = $.parseJSON(store_receivers);
			
			
			localStorage.setItem('store_rec_requestnumber',  store_receivers_value["id"]);
			localStorage.setItem('store_rec_requestdate',  store_receivers_value["date"]);
			localStorage.setItem('store_rec_type',  store_receivers_value["request_type"]);
			
			localStorage.setItem('store_rec_from_store_id',  store_receivers_value["from_store_id"]);
			localStorage.setItem('store_rec_to_store_id',  store_receivers_value["to_store_id"]);
			
			localStorage.setItem('store_rec_warehouse', store_receivers_value["warehouse_id"]);
			localStorage.setItem('store_rec_note', store_receivers_value["note"]);
			localStorage.setItem('store_rec_discount', 0);
			localStorage.setItem('store_rec_tax2', store_receivers_value["order_tax_id"]);
			localStorage.setItem('store_rec_shipping', store_receivers_value["shipping"]);
			localStorage.setItem('store_rec_supplier', store_receivers_value["supplier_id"]);
			localStorage.setItem('store_rec_items', items);
			localStorage.setItem('store_receivers_date', store_receivers_value["store_receivers_date"]);
			
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
