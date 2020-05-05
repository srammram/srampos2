<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<script type="text/javascript">
    var count = 1, an = 1, purchase_invoices_edit = false, product_variant = 0, DT = <?= $Settings->default_tax_rate ?>, DC = '<?= @$default_currency->code ?>', shipping = 0,
        product_tax = 0, invoice_tax = 0, total_discount = 0, total = 0,
        tax_rates = <?php echo json_encode($tax_rates); ?>, pi_items = {},
        audio_success = new Audio('<?= $assets ?>sounds/sound2.mp3'),
        audio_error = new Audio('<?= $assets ?>sounds/sound3.mp3');
		
		 <?php if ($inv) { ?>
        localStorage.setItem('pi_number', '<?= $inv->invoice_id?>');
        localStorage.setItem('pi_invoiceno', '<?=$inv->customer_id?>');
        localStorage.setItem('pi_supplier', '<?=$inv->supplier_id;?>');
        localStorage.setItem('pi_date', '<?=$inv->invoice_date?>');
        localStorage.setItem('delivery_address', '<?=$inv->delivery_address?>');
        localStorage.setItem('supplier_address', '<?= $inv->supplier; ?>');
        localStorage.setItem('invoice_amt', '<?=$inv->invoice_amt?>');
        localStorage.setItem('grn_items', JSON.stringify(<?=$grn_items;?>));
        <?php } ?>
    $(document).ready(function () {
        $(document).on('change', '#grn_date', function (e) {
            localStorage.setItem('grn_date', $(this).val());
        });
        if (grn_date = localStorage.getItem('grn_date')) {
            $('#grn_date').val(grn_date);
        }
		 $(document).on('change', '#note', function (e) {
            localStorage.setItem('note', $(this).val());
        });
        if (note = localStorage.getItem('note')) {
            $('#note').val(note);
        }
		if (pi_date = localStorage.getItem('pi_date')) {
            $('.invoice_date').val(pi_date);
        }
		
		if (invoice_amt = localStorage.getItem('invoice_amt')) {
            $('.invoice_amt').val(invoice_amt);
        }
		if (delivery_address = localStorage.getItem('delivery_address')) {
            $('.delivery_address').val(delivery_address);
        }
		if (pi_supplier = localStorage.getItem('pi_supplier')) {
            $('.pi_supplier').val(pi_supplier);
		    $('.pi_supplier').trigger('change');
        }
		if (currency = localStorage.getItem('currency')) {
            $('.currency').val(currency);
        }
		
	   if (supplier_address = localStorage.getItem('supplier_address')) {
            $('.supplier_address').val(supplier_address);
        }
		if (pi_number = localStorage.getItem('pi_number')) {
            $('.pi_number').val(pi_number);
        }
        ItemnTotals();
    });
</script>

<div class="box">
    <div class="box-header procurment-header">
        <h2 class=""><?= lang('edit_grn'); ?></h2>        
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12" >                
                <?php
                $attrib = array('data-toggle' => 'validator1', 'role' => 'form','id' => 'add_grn');
                echo admin_form_open_multipart("procurment/grn/edit/". $id, $attrib)
                ?>                
                <div class="row">
                    <div class="col-lg-12" style="background:#b1d7fd; padding:15px 15px;">
                        <?php echo form_submit('add_grn', $this->lang->line("save"), 'id="add-grn" class="btn col-lg-1 btn-sm btn-primary pull-right" '); ?>
                        <button type="button" class="btn col-lg-1 btn-sm btn-danger pull-right" id="reset" style="margin-right:15px;height:30px!important;font-size: 12px!important"><?= lang('reset'); ?></button>

                        
                        <table class="table custom_tables">
                            <tbody>
                                <tr>
                                    <td>
                                        <?= lang("date", "date") ?>
                                    </td>
                                    <td>
                                        <input type="datetime" name="date" id="grn_date" readonly class="form-control" value="<?php echo $inv->date; ?>">
                                    
                                    </td>                                              
                                     <td width="150px">
                                        <?= lang("reference_no", "reference_no"); ?>
                                    </td>
                                    <td>
                                        <input  name="reference_no" id="reference_no" readonly tabindex=-1 class="form-control" value="<?php echo $inv->reference_no; ?>">
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
                                        <input  name="note" id="note" class="form-control" value="<?php echo $inv->note;   ?>">
                                    </td>  
                                </tr>
								<tr>
								 <td>
                                        <?= lang("load_pi", "load_pi") ?>
                                    </td>
                                    <td>
                                        <?php  $pi[''] = '';
                                             foreach ($invoicelist as $row) {
                                                $pi[$row->id] = $row->reference_no;
                                             }
                                              echo form_dropdown('pi_number_', $pi, $inv->invoice_id, ' class="form-control pi_number input-tip select" data-placeholder="' . lang("select") . ' ' . lang("pi_number") . '"style="width:100%;" disabled id="pi_number"  ');
                                        ?>
									<input type="hidden" name="pi_number" id="pi_number"  class="required form-control pi_number" value="<?php echo $inv->invoice_id;   ?>">	
                                    </td>
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
												echo form_dropdown('supplier', $sl,$inv->supplier_id, ' data-placeholder="' . $this->lang->line("select") . ' ' . $this->lang->line("supplier") . '"  disabled class="form-control pi_supplier input-tip select" readonly style="width:100%;"');
											?>
											<div class="input-group-addon no-print" >
											
										</div>
										</div>
										</td>
										 <td>
                                        <?= lang("invoice_date", "invoice_date") ?>
                                    </td>
                                    <td>                                        
                                        <input type="datetime"   class="required form-control invoice_date" readonly>
										   <input type="hidden" name="invoice_date" id="invoice_date"  class="required form-control invoice_date" value="<?php echo $inv->invoice_date;   ?>">
                                    </td>
                                </tr>
								<tr>
								<td>
                                        <?= lang("supplier_address", "supplier_address") ?>
                                    </td>
                                    <td> 
                                       <input type="text" name="supplier_address" id="supplier_address" class="required form-control supplier_address" readonly>
									     <input type="hidden"   class="required form-control supplier_address" value="<?php echo $inv->supplier_address;   ?>">
                                    </td>
                                     <td>
                                        <?= lang("invoice_amt", "invoice_amt") ?>
                                    </td>
                                    <td> 
                                       <input type="text"  id="invoice_amt" class="required form-control invoice_amt numberonly" readonly>
									   
									      <input type="hidden" name="invoice_amt"  class="required form-control invoice_amt numberonly" value="">
                                    </td>
									<td>
                                        <?= lang("delivery_address", "delivery_address") ?>
                                    </td>
                                    <td> 
                                       <input type="text"  id="delivery_address" class="required form-control delivery_address numberonly" readonly>
									     <input type="hidden" name="delivery_address"  class="required form-control delivery_address " >
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
                                        <?php echo form_input('add_item', '', 'class="form-control input-lg" id="add_item"  readonly placeholder="' . $this->lang->line("Search Purchase Items") . '"'); ?>
                                       
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
                                    <table id="grntable"
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
						<input name="titems"  readonly="" class="form-control titems" autocomplete="off">
						<input type="hidden" name="titems"   class="form-control titems" autocomplete="off">
					    </td>
					</tr>
					<tr>                                    
					    <td>
						<label for="total_items">total no qty</label>                                    </td>
					    <td>
						<input   readonly="" class="form-control total_items" autocomplete="off">
						<input name="total_items"  type="hidden"  class="form-control total_items" >
					    </td>
					</tr>
				    </tbody>
				    </table>
               

                <?php echo form_close(); ?>

            </div>

        </div>
    </div>
</div>
<script>
$(document).on('change', '#pi_number', function(){
	if (localStorage.getItem('grn_items')) {
        localStorage.removeItem('grn_items');
    }
	if (localStorage.getItem('pi_number')) {
        localStorage.removeItem('pi_number');
    }
    if (localStorage.getItem('pi_invoiceno')) {
        localStorage.removeItem('pi_invoiceno');
    }
    if (localStorage.getItem('pi_warehouse')) {
        localStorage.removeItem('pi_warehouse');
    }
    if (localStorage.getItem('pi_supplier')) {
        localStorage.removeItem('pi_supplier');
    }
    if (localStorage.getItem('pi_currency')) {
        localStorage.removeItem('pi_currency');
    }
    if (localStorage.getItem('pi_date')) {
        localStorage.removeItem('pi_date');
    }
	  if (localStorage.getItem('delivery_address')) {
        localStorage.removeItem('delivery_address');
    }
	 if (localStorage.getItem('supplier_address')) {
        localStorage.removeItem('supplier_address');
    }
	if (localStorage.getItem('invoice_amt')) {
        localStorage.removeItem('invoice_amt');
    }
	
	var pi_number = $(this).val();
	$.ajax({
		type: 'get',
		url: '<?= admin_url('procurment/grn/purchase_invoice_list'); ?>',
		dataType: "json",
		data: {
			poref: pi_number
		},
		success: function (data) {
			var purchase_invoices_value = [];
			$(this).removeClass('ui-autocomplete-loading');
			var items = JSON.stringify(data.value['purchase_invoicesitem']);
			var purchase_invoices = JSON.stringify(data.value['purchase_invoices']);
			purchase_invoices_value = $.parseJSON(purchase_invoices);
			localStorage.setItem('pi_number',purchase_invoices_value["id"]);
			localStorage.setItem('pi_date',purchase_invoices_value["date"]);
			localStorage.setItem('pi_supplier',purchase_invoices_value["supplier_id"]);
			localStorage.setItem('supplier_address',purchase_invoices_value["supplier_address"]);
			localStorage.setItem('pi_currency',purchase_invoices_value["currency"]);
			localStorage.setItem('pi_invoiceno',purchase_invoices_value["invoice_no"]);
			localStorage.setItem('invoice_amt',purchase_invoices_value["invoice_amt"]);
			localStorage.setItem('delivery_address',purchase_invoices_value["address"]);
			$('.invoice_date').val(localStorage.getItem('pi_date'));
			$('.supplier_address').val(localStorage.getItem('supplier_address'));
			$('.invoice_amt').val(localStorage.getItem('invoice_amt'));
			$('.delivery_address').val(localStorage.getItem('delivery_address'));
			$('.pi_supplier').val(localStorage.getItem('pi_supplier'));
			$('.pi_supplier').change();
			$('.currency').val(localStorage.getItem('pi_currency'));
			$('.currency').change();
			localStorage.setItem('grn_items', items);
			loadItems();
		}
	});
});

</script>
