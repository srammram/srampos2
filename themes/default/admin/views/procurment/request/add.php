<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<style type="text/css">
     .invoice-error{
    border: 2px solid #F00 !important;
    }
	
	#s2id_autogen2{ width:100%; }
</style>
<script type="text/javascript">
    var default_store = '<?=$default_store?>';
    var count = 1, an = 1, product_variant = 0, DT = <?= $Settings->default_tax_rate ?>, allow_discount = <?= ($Owner || $Admin || $this->session->userdata('allow_discount')) ? 1 : 0; ?>,
        product_tax = 0, invoice_tax = 0, total_discount = 0, total = 0, shipping = 0,
        tax_rates = <?php echo json_encode($tax_rates); ?>;
         var reqitems = {};
    
    $(document).ready(function () {
        <?php if($this->input->get('customer')) { ?>
        if (!localStorage.getItem('reqitems')) {
            localStorage.setItem('reqcustomer', <?=$this->input->get('customer');?>);
        }
        <?php } ?>
        $("#reqdate").prop('disabled', true);
        <?php //if ($Owner || $Admin) { ?>
        if (!localStorage.getItem('reqdate')) {
            $("#reqdate").datetimepicker({
                format: site.dateFormats.js_sdate,
                fontAwesome: true,
                language: 'common',
                weekStart: 1,
                todayBtn: 1,
                autoclose: 1,
                todayHighlight: 1,
                startView: 1,
                forceParse: 0
            }).datetimepicker('update', new Date());
        }
        // format: site.dateFormats.js_sdate, fontAwesome: true, todayBtn: 1, autoclose: 1, minView: 2,startDate: new Date()
        $(document).on('change', '#reqdate', function (e) {
            localStorage.setItem('reqdate', $(this).val());
        });
        if (reqdate = localStorage.getItem('reqdate')) {
            $('#reqdate').val(reqdate);
        }
        <?php //} ?>
        $("#qu_requestnumber").val(localStorage.getItem('qu_requestnumber'));

        $(document).on('change', '#reqbiller', function (e) {
            localStorage.setItem('reqbiller', $(this).val());
        });
        if (reqbiller = localStorage.getItem('reqbiller')) {
            $('#reqbiller').val(reqbiller);
        }
	$(document).on('change', '#reqsupplier', function (e) {
	    localStorage.setItem('reqsupplier', $(this).val());
	});  
		/*$(document).on('change', '#reqsupplier', function (e) {            
			$.ajax({
				type: 'get',
				url: '<?= admin_url('procurment/request/supplier_details'); ?>',
				dataType: "json",
				data: {					
					supplier_id: $(this).val(),
				},
				success: function (data) {
					$('#reqsupplier_address').val(data.details);
					localStorage.setItem('reqsupplier_address', data.details);
					$('#add_item').focus();
				}
			});
            localStorage.setItem('reqsupplier', $(this).val());
        });
        if (reqsupplier_address = localStorage.getItem('reqsupplier_address')) {
            $('#reqsupplier_address').val(reqsupplier_address);
        }*/
	if (reqsupplier = localStorage.getItem('reqsupplier')) {
	   
	    var reqsupplier = JSON.parse("[" + reqsupplier + "]");
	    $.each(reqsupplier,function(n,v){
		$('#reqsupplier option[value="'+v+'"]').attr('selected',true);
	    })
        }
		
        if (!localStorage.getItem('reqtax2')) {
            localStorage.setItem('reqtax2', <?=$Settings->default_tax_rate2;?>);
        }
        ItemnTotals();
        $("#add_item").autocomplete({
            source: function (request, response) {
                if (!$('#reqsupplier').val()) {
                    $('#add_item').val('').removeClass('ui-autocomplete-loading');
                    bootbox.alert('<?=lang('select_above');?>');
                    //response('');
                    $('#add_item').focus();
                    return false;
                }
                $.ajax({
                    type: 'get',
                    url: '<?= admin_url('procurment/request/suggestions'); ?>',
                    dataType: "json",
                    data: {
                        term: request.term,
                        warehouse_id: $("#reqwarehouse").val(),
                        supplier_id: $("#reqsupplier").val()
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
    });
</script>

<div class="box">
    <div class="box-header procurment-header">        
        <h2 class=""><?= lang('add_quotation_request'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <!--<p class="introtext"><?php echo lang('enter_info'); ?></p>-->
                <?php
                $attrib = array('data-toggle' => 'validator1', 'role' => 'form', 'id' => 'add-quotation-request');
                echo admin_form_open_multipart("procurment/request/add", $attrib)
                ?>


                <div class="row">
                    <div class="col-lg-12" style="background:#b1d7fd; padding:15px 15px;">
                    	<?php echo form_submit('add_request', $this->lang->line("save"), 'id="add_request" class="btn col-lg-1 btn-sm btn-primary pull-right" '); ?>
                        <button type="button" class="btn col-lg-1 btn-sm btn-danger pull-right" id="reset" style="margin-right:15px;height:30px!important;font-size: 12px!important"><?= lang('reset'); ?></button>                       
                        
                        <input type="hidden" name="warehouse" id="reqwarehouse" value="<?php echo $Settings->default_warehouse ?>"> 
                        <input type="hidden" name="biller" id="reqbiller" value="<?php echo $Settings->default_biller ?>"> 
                        
                        <table class="table custom_tables">
                        	<tbody>
                            	<tr>
                                    <td>
                                        <?= lang("date", "date") ?>
                                    </td>
                                    <td>
                                        <input  name="date" id="reqdate" readonly class="form-control" value="<?php echo date('Y-m-d') ?>">
                                    </td>
                                	
                                    <td width="150px">
                                    	<?= lang("quotation_req_no", "quotation_req_no") ?>
                                    </td>
                                    <td>
                                    	<?php
										$n = $this->siteprocurment->lastidRequest();
										$n2 = str_pad($n + 1, 5, 0, STR_PAD_LEFT);
										?>
                                    	<input  name="reference_no" id="reqref" readonly class="form-control"  value="<?php echo $n2 ?>">
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
                                        <?= lang("supplier", "reqsupplier") ?>
                                    </td>
                                    <td width="350px">
                                        <?php
                                        $sl = array();
										
                                        foreach ($suppliers as $supplier) {
                                            $sl[$supplier->id] = $supplier->name;
                                        }
                                        echo form_dropdown('supplier[]', $sl,  (isset($_POST['supplier']) ? $_POST['supplier'] : $Settings->default_supplier), 'id="reqsupplier"   multiple  data-placeholder="' . $this->lang->line("select") . ' ' . $this->lang->line("supplier") . '"required="required" class="form-control input-tip select"'); 
                                        ?>
                                    </td>

                                    <td width="100px">
                                        <?= lang("document", "document") ?>
                                    </td>
                                    <td width="350px" >
                                        <input id="document" type="file" data-browse-label="" name="document" data-show-upload="false"
                                       data-show-preview="false" class="form-control file">
                                    </td>

                                	<td width="100px" style="display: none;">
                                    	<?= lang("supplier_address", "supplier_address") ?>
                                    </td>
                                    <td width="350px" style="display: none;">
                                    	<input  name="supplier_address" id="reqsupplier_address" readonly class="form-control" value="">
                                    </td>
                                    
                                	<td>
                                    	<?= lang("status", "reqstatus") ?>
                                    </td>
                                    <td>
                                    	<?php 
										$st['process'] = lang('process');
										if($this->siteprocurment->hasApprovedPermission()){
											$st['approved'] = lang('approved');	
										}
										echo form_dropdown('status', $st, '', 'class="form-control input-tip" id="reqstatus"'); ?>
                                    </td>                                    
                                </tr>
                                <!--<tr>
                                     <td>
                                        <?= lang("request_date", "request_date") ?>
                                    </td>
                                     <td>
                                     <input type="datetime" name="requestdate" id="qu_requestdate" readonly class="form-control" value="<?php echo date('Y-m-d') ?>">
                                    </td> -->
                                   <!-- <td width="150px">
                                        <?= lang("request_number", "qu_requestnumber"); ?>
                                    </td>
                                    <td>
                                       <?php                        
                                        //   $qn[''] = '';
                                        //    foreach ($store_request as $requestnumber_row) {
                                        //        $qn[$requestnumber_row->id] = $requestnumber_row->reference_no;
                                        //    }
                                        //    echo form_dropdown('requestnumber', $qn, '', ' class="form-control input-tip select" data-placeholder="' . lang("select") . ' ' . lang("request_number") . '"style="width:100%;" id="qu_requestnumber"  ');
                                        ?>
                                    </td> 


                                	<td>
                                    	<?= lang("document", "document") ?>
                                    </td>
                                    <td>
                                    	<input id="document" type="file" data-browse-label="" name="document" data-show-upload="false"
                                       data-show-preview="false" class="form-control file">
                                    </td>
                                </tr> -->
                            </tbody>
                        </table>
                     </div>  

                        <div class="col-md-12" style="display: none">
                            <div class="control-group table-group">
                                <label class="table-label"><?= lang("Store_request_list"); ?></label>
                                <div class="controls table-controls">
                                    <table id="StoreReqTable"
                                            class="table items  table-bordered table-condensed sortable_table" style="background:#fff">
                                        <thead>
                                        <tr>
                                            <th class="col-md-1" style="text-align:center !important;"><?= lang("S.NO"); ?></th>
                                            <th class="col-md-1" style="text-align:center !important;"><?= lang("check"); ?></th>
                                            <th class="col-md-2" style="text-align:center !important;"><?= lang("store_name"); ?></th>
                                            <th class="col-md-2" style="text-align:center !important;"><?= lang('referance_no'); ?></th>
                                            <th class="col-md-2" style="text-align:center !important;"><?= lang('request_date'); ?></th>
                                            <!-- <th class="col-md-2"><?= lang('no_of_qty'); ?></th> -->
                                        </tr>
                                        </thead>
                                        <tbody>
                                             <?php  
                                                 if(!empty($store_request))
                                                 {      
                                                    $i = 1;
                                                    foreach($store_request as $value) 
                                                    {
                                                     echo '<tr class = "text-center clickable"><td>'.$i.'</td><td><input name="store_request_id[]" type = "checkbox" id="store_id_'.$value->id.'" class = "store_id" value="'.$value->id.'"></td><td class = " view" style = "width:15%;">'.$value->store_name .'</td><td class = "view"  style="width: 3%;">'.$value->reference_no.'</td><td class = "view">'.$value->date.'</td></tr>';
                                                    $i++;
                                                    } 
                                                 }
                                            ?>

                                          <!--   <?php                        
                                           $qn[''] = '';
                                            foreach ($store_request as $requestnumber_row) {
                                                $qn[$requestnumber_row->id] = $requestnumber_row->reference_no;
                                            }
                                            echo form_dropdown('requestnumber', $qn, '', ' class="form-control input-tip select" data-placeholder="' . lang("select") . ' ' . lang("request_number") . '"style="width:100%;" id="qu_requestnumber"  ');
                                        ?> -->
                                        </tbody>                                        
                                    </table>                                    
                                </div>                                
                            </div>
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
                                    <table id="reqTable"
                                            class="table items  table-bordered table-condensed sortable_table" style="background:#fff">
                                        <thead>
                                        <tr>
                                        	<th class="col-md-1"><?= lang("S.NO"); ?></th>
                                            <th class="col-md-2"><?= lang("code"); ?></th>
                                            <th class="col-md-2"><?= lang('product') . lang('name'); ?></th>
                                             <th class="col-md-2"><?= lang("Category"); ?></th>
                                            <th class="col-md-2"><?= lang("Subcategory"); ?></th>
                                            <th class="col-md-2"><?= lang("Brand"); ?></th>
                                            <th class="col-md-2"><?= lang("quantity"); ?></th>
                                            <th class="col-md-2"><?= lang("uom"); ?></th>
                                            <th class="col-md-2"><?= lang("Cost.Price"); ?></th>
                                            <th class="col-md-2"><?= lang("Selling.Price"); ?></th>
                                           <th class="col-md-1" style="text-align: center;"><i
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
                        
                     <div class="col-lg-12" style="background:#a6f7a1; margin-top:15px;">
                    <table class="table custom_tables">
                            <tbody>
                                <tr>                                    
                                    <td width="150px">
                                        <?= lang("Total No Of Qty", "total_items") ?>
                                    </td>
                                    <td width="150px">
                                        <input  name="total_items" id="total_items" readonly class="form-control">
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
                <form class="form-horizontal" role="form" >
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
                                <?php
                                $tr[""] = "";
                                foreach ($tax_rates as $tax) {
                                    $tr[$tax->id] = $tax->name;
                                }
                                echo form_dropdown('mtax', $tr, "", 'id="mtax" class="form-control input-tip select" style="width:100%;"');
                                ?>
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

<script type="text/javascript">
    <?php
if(!empty($ref_requestnumber)){
?>
$(document).ready(function(e) {    


    if(localStorage.getItem('qu_requestnumber') == null){
        localStorage.setItem('qu_requestnumber', '<?= $ref_requestnumber ?>');
        $("#qu_requestnumber").val(localStorage.getItem('qu_requestnumber'));
        $('#qu_requestnumber').trigger('change');
    }
});
<?php
}
?>
$(document).ready(function(e) {    

//$('#reqsupplier').select('open');
//$('select[id^="reqsupplier"]').eq(1).focus();

    var store_id_check = JSON.parse(localStorage.getItem('store_id_check'));      
     var store_id_check = JSON.parse(localStorage.getItem('store_id_check'));     
     if(store_id_check)  { 
    $.each(store_id_check, function(i, val){                
          $('input[name="store_request_id[]"][value="' + val+ '"]').prop("checked", true);          
    });}

});      


    
   $selecte_stores = localStorage.getItem("store_id_check");
   
   if ($selecte_stores==null || $selecte_stores=="[]") {
    //localStorage.removeItem('reqitems');
   }

$('.store_id').on('ifChecked', function(event){       
localStorage.removeItem("store_id_check");
      var checkedVals = $('.store_id:checkbox:checked').map(function() {        
            return this.value;
        }).get();      
      console.log(checkedVals);   
       localStorage.setItem("store_id_check", JSON.stringify(checkedVals));

       // localStorage.setItem('store_id_check', checkedVals);

    if (localStorage.getItem('reqitems')) {
        localStorage.removeItem('reqitems');
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
    
    
    $.ajax({
        type: 'get',
        url: '<?= admin_url('procurment/request/store_list'); ?>',
        dataType: "json",
        data: {
            poref: checkedVals
        },
        success: function (data) {                   
            var quotes_value = [];
            $(this).removeClass('ui-autocomplete-loading');
            var items = JSON.stringify(data.value['reqitems']);
            var quotes = JSON.stringify(data.value['quotes']);
            quotes_value = $.parseJSON(quotes);            
            localStorage.setItem('qu_warehouse', quotes_value["warehouse_id"]);            
            localStorage.setItem('qu_tax2', quotes_value["order_tax_id"]);
            localStorage.setItem('qu_shipping', quotes_value["shipping"]);
            localStorage.setItem('qu_supplier', quotes_value["supplier_id"]);            
            localStorage.setItem('reqitems', items);
            location.reload();          
        }       
    });      
});



$('.store_id').on('ifUnchecked', function (event) {    
    var checkedVals = $('.store_id:checkbox:checked').map(function() {             
            return this.value;
        }).get();

    console.log(checkedVals);   
    localStorage.setItem("store_id_check", JSON.stringify(checkedVals));

        if (localStorage.getItem('reqitems')) {
        localStorage.removeItem('reqitems');
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
    // var qu_requestnumber = $(this).val();
    
    $.ajax({
        type: 'get',
        url: '<?= admin_url('procurment/request/store_list'); ?>',
        dataType: "json",
        data: {
            poref: checkedVals
        },
        success: function (data) {                   
            var quotes_value = [];
            $(this).removeClass('ui-autocomplete-loading');
            var items = JSON.stringify(data.value['reqitems']);
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
            localStorage.setItem('reqitems', items);    
            localStorage.setItem($(this).attr("id"), $(this).attr('checked'));        
            location.reload();          
        }       
    });
});



$(document).ready(function(){
  $("input.store_id").each(function() {
       var savedValue = localStorage.getItem( $(this).attr("id") );
       if (savedValue)
       {
         $('input[name=store_id]').attr('checked', true);
       }
  }); 
 
});


    $(document).on('change', '#qu_requestnumber', function(){
    
    if (localStorage.getItem('reqitems')) {
        localStorage.removeItem('reqitems');
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
        url: '<?= admin_url('procurment/request/store_list'); ?>',
        dataType: "json",
        data: {
            poref: qu_requestnumber
        },
        success: function (data) {                   
            var quotes_value = [];
            $(this).removeClass('ui-autocomplete-loading');
            var items = JSON.stringify(data.value['reqitems']);
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
            localStorage.setItem('reqitems', items);            
            // location.reload();          
        }       
    });
});
</script>
