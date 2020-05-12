<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<script type="text/javascript">
       
    
    <?php if($srr) { ?>
   
	localStorage.setItem('store_rtnrec_to_store_id', '<?= $srr->to_store ?>');
	localStorage.setItem('store_rtnrec_from_store_id', '<?= $srr->from_store ?>');
	
    
    localStorage.setItem('store_rtnrec_items', JSON.stringify(<?= $srr_items; ?>));
    <?php } ?>

    var count = 1, an = 1, store_return_receivers_edit = false, product_variant = 0, DT = <?= $Settings->default_tax_rate ?>, DC = '<?= $default_currency->code ?>', shipping = 0,
        product_tax = 0, invoice_tax = 0, total_discount = 0, total = 0,
        tax_rates = <?php echo json_encode($tax_rates); ?>, store_rtnrec_items = {},
        audio_success = new Audio('<?= $assets ?>sounds/sound2.mp3'),
        audio_error = new Audio('<?= $assets ?>sounds/sound3.mp3');
    $(document).ready(function () {
        <?php if($this->input->get('supplier')) { ?>
        if (!localStorage.getItem('store_rtnrec_items')) {
            localStorage.setItem('store_rtnrec_supplier', <?=$this->input->get('supplier');?>);
        }
        <?php } ?>
        <?php //if ($Owner || $Admin) { ?>
        if (!localStorage.getItem('store_rtnrec_date')) {
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
            localStorage.setItem('store_rtnrec_date', $(this).val());
        });
        if (podate = localStorage.getItem('store_rtnrec_date')) {
            $('#podate').val(podate);
        }
		
		$("#store_rtnrec_requestnumber").val(localStorage.getItem('store_rtnrec_requestnumber'));
        ItemnTotals();
		var store_rtnrec_from_store_id;
        $('#store_rtnrec_from_store_id').on("select2-focus", function (e) {
            store_rtnrec_from_store_id = $(this).val();
        }).on("select2-close", function (e) {
            if ($(this).val() != '' && $(this).val() == $('#store_rtnrec_to_store_id').val()) {
                $(this).select2('val', store_rtnrec_from_store_id);
                bootbox.alert('<?= lang('please_select_different_store') ?>');
            }
        });
        var store_rtnrec_to_store_id;
        $('#store_rtnrec_to_store_id').on("select2-focus", function (e) {
            store_rtnrec_to_store_id = $(this).val();
        }).on("select2-close", function (e) {
            if ($(this).val() != '' && $(this).val() == $('#store_rtnrec_from_store_id').val()) {
                $(this).select2('val', store_rtnrec_to_store_id);
                bootbox.alert('<?= lang('please_select_different_store') ?>');
            }
        });
		
    });

</script>

<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('edit_store_return_receivers'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?php echo lang('enter_info'); ?></p>
                <?php
                $attrib = array('data-toggle' => 'validator1', 'role' => 'form');
                echo admin_form_open_multipart("procurment/store_return_receivers/edit/".$id, $attrib)
                ?>
				 <?php echo form_submit('add_store_return_receivers', $this->lang->line("save"), 'id="add_store_return_receivers" class="btn col-lg-1 btn-sm btn-primary pull-right"'); ?>
					<button type="button" class="btn col-lg-1 btn-sm btn-danger pull-right" style="margin-right:15px;height:30px!important;font-size: 12px!important" id="reset"><?= lang('reset') ?></button>

                <div class="row">
                    <div class="col-lg-12">
                        <h2>Store_return_receivers Details</h2>

                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang("Date", "date") ?>
                                <input type="datetime" name="date" id="store_rtnrec_date" readonly class="form-control" value="<?php echo $srr->date ?>">
                                
                            </div>
                        </div> 
                      <div class="col-md-4" >
                            <div class="form-group">
                                <?= lang("from_store", "store_rtnrec_from_store_id"); ?>
                                <?php
                                $fst[''] = '';
                                foreach ($stores as $store) {
                                    $fst[$store->id] = $store->name;
                                }
                                echo form_dropdown('from_store_id', $fst, (isset($_POST['from_store_id']) ? $_POST['from_store_id'] :$srr->to_store), 'id="store_rtnrec_from_store_id" class="form-control input-tip select" data-placeholder="' . lang("select") . ' ' . lang("from_store") . '" required="required" style="width:100%;" disabled ');
                                ?>
                            </div>
                        </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
							<?= lang("receiver_number", "receiver_number"); ?>
							<input type="text" class="form-control" value="<?php echo  $srr->store_receiver_refno;   ?>" readonly>
							<input type="hidden" class="form-control" name="receiver_id" value="<?php echo  $srr->store_receiver_id;   ?>" >
							
                            </div>
						</div>
						<div class="col-md-4">
                            <div class="form-group">
                                <?= lang("Receiver Date", "Receiver date") ?>
                                <input type="datetime" name="store_rtnreceivers_date" id="store_rtnreceivers_date" readonly class="form-control" value="<?php echo  $srr->store_receiver_date; ?>">
                                
                            </div>
                        </div>
                        <input type="hidden" name="request_type" id="store_rtnrec_type" value="<?php echo 'return' ?>">
                        <input type="hidden" name="warehouse" id="store_rtnrec_warehouse" value="<?php echo $Settings->default_warehouse ?>">  
                            <div class="col-md-4">
                                <div class="form-group">
                                    <?= lang("document", "document") ?>
                                    <input id="document" type="file" data-browse-label="<?= lang('browse'); ?>" name="document" data-show-upload="false"
                                           data-show-preview="false" class="form-control file">
                                   
                                </div>
                            </div>
                              <div class="col-md-4">
                                <div class="form-group">
								   <?= lang("remarks", "remarks"); ?>
                                   <input type="text" name="remarks" value="" class="form-control"  value="<?php echo  $srr->remarks;   ?>">
                                </div>
                            </div>
							
							
                            <div class="col-md-4">
                                <div class="form-group">
                                    <?= lang("status", "store_rtnrec_status"); ?>
                                    <?php $st['process'] = lang('process');
									$st['approved'] = lang('approved');	
                                    echo form_dropdown('status', $st, '', 'class="form-control input-tip" id="store_rtnrec_status"'); ?>
    
                                </div>
                            </div>
                    
                          <div class="clearfix"></div>	
                        <div class="col-md-12" id="sticker">
                        </div>
                        <div class="col-md-12">
                            <div class="control-group table-group">
                                <label class="table-label"><?= lang("items"); ?></label>

                                <div class="controls table-controls">
                                    <table id="store_return_receiversTable"
                                            class="table items  table-bordered table-condensed sortable_table" style="background:#fff">
                                      <thead>
                                       <tr>
                                            <th><?=lang('s_no')?></th>
											<th><?=lang('code')?></th>
											<th><?=lang('product_name')?></th>
                                            <th><?= lang("request_quantity"); ?></th>    
                                            <th><?= lang("transfer_quantity"); ?></th>
                                            <th><?= lang("pending_quantity"); ?></th>
											<th><?=lang('batch')?></th>
											<th><?=lang('expiry')?></th>
											<th><?=lang('cost_price')?></th>
											<th><?=lang('selling_price')?></th>
											<th><?=lang('tax')?></th>
											<th><?=lang('gross')?></th>
											<th><?=lang('tax_amount')?></th>
											<th><?=lang('total')?></th>
                                            <th style="width: 30px !important; text-align: center;"><i
                                                    class="fa fa-trash-o"
                                                    style="opacity:0.5; filter:alpha(opacity=50);"></i></th>
                                        </tr>
                                        <tbody></tbody>
                                        <tfoot>
										
										</tfoot>
                                    </table>
                                </div>
                            </div>
							
								
				
                        </div>
                        <div class="clearfix"></div>
                     
                        <div class="col-md-12">
                           <table class="table " style="padding: 4px;border-top: none!important;width:30%;">
						<tbody>
						<tr>                                    
					    <td>
						<label for="total_no_items"><?=lang('total_no_items')?></label>                                    </td>
					    <td>
						<input name="total_no_items" id="total_no_items" readonly class="form-control">
					    </td>
						</tr>
					<tr>                                    
					    <td>
						<label for="total_no_qty"><?=lang('total_no_qty')?></label>                                    </td>
					    <td>
						<input name="total_no_qty" id="total_no_qty" readonly class="form-control">
					    </td>
					</tr>
				    </tbody>
				    </table>
                        </div>
                    </div>
                </div>
                <?php echo form_close(); ?>

            </div>

        </div>
    </div>
</div>


</script>
