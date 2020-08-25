<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<script type="text/javascript">
var wastage_items={};
    var count = 1, an = 1,  product_variant = 0, DT = <?= $Settings->default_tax_rate ?>, DC = '<?= @$default_currency->code ?>', shipping = 0,
        product_tax = 0, invoice_tax = 0, total_discount = 0, total = 0,
        tax_rates = <?php echo json_encode($tax_rates); ?>, pi_items = {},
        audio_success = new Audio('<?= $assets ?>sounds/sound2.mp3'),
        audio_error = new Audio('<?= $assets ?>sounds/sound3.mp3');
    $(document).ready(function () {
        $(document).on('change', '#date', function (e) {
            localStorage.setItem('date', $(this).val());
        });
        if (date = localStorage.getItem('date')) {
            $('#date').val(date);
        }
		 $(document).on('change', '#note', function (e) {
            localStorage.setItem('note', $(this).val());
        });
        if (note = localStorage.getItem('note')) {
            $('#note').val(note);
        }
		 $(document).on('change', '#type', function (e) {
            localStorage.setItem('type', $(this).val());
        });
        if (date = localStorage.getItem('type')) {
            $('#type').val(date);
        }
		 $(document).on('change', '#note', function (e) {
            localStorage.setItem('note', $(this).val());
        });





        ItemnTotals();
    });
</script>

<div class="box">
    <div class="box-header procurment-header">
        <h2 class=""><?= lang('add_wastage'); ?></h2>        
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12" >                
                <?php
                $attrib = array('data-toggle' => 'validator1', 'role' => 'form','id' => 'add_wastage');
                echo admin_form_open_multipart("wastage/add", $attrib)
                ?>                
                <div class="row">
                    <div class="col-lg-12" style="background:#b1d7fd; padding:15px 15px;">
                        <?php echo form_submit('add_wastage', $this->lang->line("save"), 'id="add-grn" class="btn col-lg-1 btn-sm btn-primary pull-right" '); ?>
                        <button type="button" class="btn col-lg-1 btn-sm btn-danger pull-right" id="reset" style="margin-right:15px;height:30px!important;font-size: 12px!important"><?= lang('reset'); ?></button>

                        
                        <table class="table custom_tables">
                            <tbody>
                                <tr>
                                    <td>
                                        <?= lang("date", "date") ?>
                                    </td>
                                    <td>
                                        <input type="datetime" name="date" id="date" readonly class="form-control" value="<?php echo date('Y-m-d H:i:s') ?>">
                                    
                                    </td>                                              
                                     <td width="150px">
                                        <?= lang("reference_no", "reference_no"); ?>
                                    </td>
                                    <td>
                                        <?php
                                        $n = $this->siteprocurment->lastWastageId();
										$n=($n !=0)?$n+1:$this->store_id .'1';
                                       	$reference = 'WTN'.str_pad($n, 8, 0, STR_PAD_LEFT);
                                        ?>
                                        <input  name="reference_no" id="reference_no" readonly tabindex=-1 class="form-control" value="<?php echo $reference ?>">
                                    </td>                                    
                                    <td width="100px">
                                        <?= lang("Type", "Type") ?>
                                    </td>
                                    <td>
                                        <?php
                                        $type[""] = "";
										$type["spoiled"] = "Spoiled";
										$type["overproduction"] = "Overproduction";
										$type["damaged"] = "Damaged";
										$type["expired"] = "Expired";
                                       
                                        echo form_dropdown('type', $type, (isset($_POST['type']) ? $_POST['type'] :""), 'id="type" data-placeholder="' . $this->lang->line("select") . ' ' . $this->lang->line("type") . '" required="required" class="form-control input-tip" style="width:100%;"');
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
                                        <input  name="note" id="note" class="form-control" >
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
                                        <?php echo form_input('add_item', '', 'class="form-control input-lg" id="add_item"   placeholder="' . $this->lang->line("Search  Items") . '"'); ?>
                                       
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
                                    <table id="wastageitemtables"
                                            class="table items  table-bordered table-condensed sortable_table" style="background:#fff">
                                        <thead>
                                                   <tr>
                                          	<th><?=lang('s_no')?></th>
											<th><?=lang('code')?></th>
											<th><?=lang('product_name')?></th>					   
                                         
                                            <th><?=lang('batch')?></th>
                                            <th><?= lang("available_quantity"); ?></th>
                                            <th><?= lang("Wastage_quantity"); ?></th>
                                            <th><?= lang("pending_quantity"); ?></th>
											<th><?=lang('expiry')?></th>
											<th><?=lang('cost_price')?></th>
											<th><?=lang('selling_price')?></th>
											<th><?=lang('tax')?></th>
											<th><?=lang('tax_amount')?></th>
											<th><?=lang('gross')?></th>
											<th><?=lang('total')?></th>
                                            <th style="width: 30px !important; text-align: center;"><i
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
						<input name="titems"  readonly="" class="form-control total_no_items" autocomplete="off">
						<input type="hidden" name="total_no_items"   class="form-control total_no_items" autocomplete="off">
					    </td>
					</tr>
					<tr>                                    
					    <td>
						<label for="total_items">total no qty</label>                                    </td>
					    <td>
						<input   readonly="" class="form-control total_no_qty" autocomplete="off">
						<input name="total_no_qty"  type="hidden"  class="form-control total_no_qty" >
					    </td>
					</tr>
				    </tbody>
				    </table>
               

                <?php echo form_close(); ?>

            </div>

        </div>
    </div>
</div>
