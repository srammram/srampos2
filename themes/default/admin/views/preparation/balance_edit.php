<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('Balance Production Edit'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?php echo lang('enter_info'); ?></p>

                <?php
				$attrib = array('data-toggle' => 'validator', 'role' => 'form');
                echo admin_form_open_multipart("production/balance_edit/".$production->id)
                ?>

                <div class="col-md-4">
                    <div class="form-group">
						<?= lang("warehouse", "warehouse"); ?>
                        <?php
                        $wh[''] = '';
                        foreach ($warehouses as $warehouse) {
                            $wh[$warehouse->id] = $warehouse->name;
                        }
                        echo form_dropdown('warehouse_id', $wh, $production->warehouse_id, 'class="form-control" data-placeholder="' . lang("select") . ' ' . lang("warehouse") . '" required="required" id="warehouse_id" style="width:100%;" ');
                        ?>
                        
                    </div>
                </div>   
				<div class="col-md-4">
                    <div class="form-group">
                        <?= lang("Production Name", "Production Name") ?>
                        <?= form_input('production_name', (isset($_POST['production_name']) ? $_POST['production_name'] : $production->production_name), 'class="form-control tip" id="production_name" required="required"') ?>
                    </div>
                 </div>
                 <div class="col-md-4">   
                   <div class="form-group">
                        <?= lang("Production Date", "Production Date") ?>
                        <?= form_input('production_date', (isset($_POST['production_date']) ? $_POST['production_date'] : $production->production_date), 'class="form-control tip date" id="production_date" required="required"') ?>
                    </div>
                    
                </div>
               
                <div class="col-xs-6 col-xs-offset-3">
                	<div class="form-group">
						<div class="form-group" style="margin-bottom:0;">
                            <div class="input-group wide-tip">
                                <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;">
                                    <i class="fa fa-2x fa-barcode addIcon"></i></a></div>
                                <?php echo form_input('purchases_item', '', 'class="form-control input-lg" id="purchases_item" placeholder="' . $this->lang->line("add_Purchase Items_to_order") . '"'); ?>
                                
                            </div>
                        </div>
                        
                    </div>
                </div>
              	<div class="col-xs-12"> 
                <table id="productionTable"  class="table items  table-bordered table-condensed sortable_table" style="background:#fff">
                    <thead>
                    <tr>
                       	<th>Code</th>
                        <th>Name</th>
                        <th>Given Quantity</th>
                        <th>Given Units</th>
                        <th>Balance Quantity</th>
                        <th>Balance Units</th>
                        <th style="width: 30px !important; text-align: center;"><i class="fa fa-trash-o"  style="opacity:0.5; filter:alpha(opacity=50);"></i></th>
                    </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot>
                    
                    </tfoot>
                </table>  
               
                 
                </div>
                 <div class="col-md-12">
                    <div  class="from-group">
					<?php if($production->status == 'Open'){ echo form_submit('add_purchases_order', $this->lang->line("submit"), 'id="add_purchases_order" class="btn btn-primary" style="padding: 6px 15px; margin:15px 0;"');  } ?>
                        <button type="button" class="btn btn-danger" id="reset"><?= lang('reset') ?></button>
                    </div>
                </div>
                
                

         </div>

                </div>

                
                <?= form_close(); ?>

            </div>

        </div>
    </div>
</div>

<script type="text/javascript">

	
    $(document).ready(function () {
		var po_items = {};
        $("#purchases_item").autocomplete({
            source: '<?= admin_url('production/suggestions'); ?>',
            minLength: 1,
            autoFocus: false,
            delay: 250,
            response: function (event, ui) {
                if ($(this).val().length >= 16 && ui.content[0].id == 0) {
                    //audio_error.play();
                    bootbox.alert('<?= lang('no_purchases_item_found') ?>', function () {
                        $('#purchases_item').focus();
                    });
                    $(this).val('');
                }
                else if (ui.content.length == 1 && ui.content[0].id != 0) {
                    ui.item = ui.content[0];
                    $(this).data('ui-autocomplete')._trigger('select', 'autocompleteselect', ui);
                    $(this).autocomplete('close');
                    $(this).removeClass('ui-autocomplete-loading');
                }
                else if (ui.content.length == 1 && ui.content[0].id == 0) {
                    //audio_error.play();
                    bootbox.alert('<?= lang('no_purchases_item_found') ?>', function () {
                        $('#purchases_item').focus();
                    });
                    $(this).val('');

                }
            },
            select: function (event, ui) {
                event.preventDefault();
                if (ui.item.id !== 0) {
										
                    var row = add_production_item(ui.item);
                    if (row) {
                        $(this).val('');
                    }
                } else {
                    //audio_error.play();
                    bootbox.alert('<?= lang('no_purchases_item_found') ?>');
                }
            }
        });

       
        function add_production_item(item) {
			if (item == null) {
                return false;
            }
             item_id = item.id;
			 if (po_items[item_id]) {
				 
			 }else{
			 	po_items[item_id] = item;       
			 }
			localStorage.setItem('po_items', JSON.stringify(po_items));
    		loadItems();
            return true;
        }
		
		function loadItems() {
			
			if (localStorage.getItem('po_items')) {
				po_items = JSON.parse(localStorage.getItem('po_items'));
				 $("#productionTable tbody").empty();
				$.each(po_items, function () {
				
                var row_no = this.id;
				var sale_units = this.sale_unit;
				var given_units = this.given_units;
				var balance_units = this.balance_units;
                var newTr = $('<tr id="row_' + row_no + '" class="item_' + this.id + '" data-item-id="' + row_no + '"></tr>');
                tr_html = '<td><input name="product_id[]" id="product_id_' + row_no + '" type="hidden" class="form-control" value="'+this.id+'"><input name="quantity[]" id="quantity_' + row_no + '" type="hidden" class="form-control" value="'+this.quantity+'"><input name="sale_unit[]" id="sale_unit_' + row_no + '" type="hidden" class="form-control" value="'+this.sale_unit+'"><input name="product_code[]" id="product_code_' + row_no + '" type="hidden" class="form-control" value="'+this.code+'"><input name="product_name[]" id="product_name_' + row_no + '" type="hidden" class="form-control" value="'+this.name+'"><span id="code_' + row_no + '">' + this.code + '</span></td>';
                tr_html += '<td><span id="name_' + row_no + '">' + this.name + '</span></td>';
								
				
				tr_html += '<td><input name="given_quantity[]" id="given_quantity_' + row_no + '" data-item="' + row_no + '" class="form-control given_quantity" value="'+this.given_quantity+'"></td>';
				tr_html += '<td><select class="form-control given_unit" id="given_unit_' + row_no + '" data-item="' + row_no + '" name="given_unit[]" value="" data-id="' + row_no + '" data-item="' + row_no + '" id="given_unit_' + row_no + '">';
				 $.each(this.units, function () {
					if(given_units == this.id){
						given_select = "selected";
					}else{
						given_select = "";
					}
					 tr_html += '<option value="'+ this.id +'"  '+given_select+'> '+ this.name +'</option>';
				 });
			 
			    tr_html += '</select></td>';
				
				tr_html += '<td><input name="balance_quantity[]" id="balance_quantity_' + row_no + '" data-item="' + row_no + '" class="form-control balance_quantity" value="'+this.balance_quantity+'"></td>';
				tr_html += '<td><select class="form-control balance_unit" id="balance_unit_' + row_no + '" data-item="' + row_no + '" name="balance_unit[]" value="" data-id="' + row_no + '" data-item="' + row_no + '" id="balance_unit_' + row_no + '">';
				 $.each(this.units, function () {
					if(balance_units == this.id){
						balance_select = "selected";
					}else{
						balance_select = "";
					}
					 tr_html += '<option value="'+ this.id +'"  '+balance_select+'> '+ this.name +'</option>';
				 });
			 
			    tr_html += '</select></td>';
			 
                tr_html += '<td><i class="fa fa-times tip del" id="' + row_no + '" title="Remove" style="cursor:pointer;"></i></td>';
                newTr.html(tr_html);
                newTr.appendTo("#productionTable");
               
            });
			}
		
		}

        $(document).on('click', '.del', function () {
			var row = $(this).closest('tr');
			var item_id = row.attr('data-item-id');
			delete po_items[item_id];
			row.remove();	
          	
        });
		
		<?php
		if($production_item){
		?>
		localStorage.setItem('warehouse_id', '<?= $production->warehouse_id?>');
        localStorage.setItem('production_name', '<?=$production->production_name?>');
        localStorage.setItem('production_date', '<?=$production->production_date?>');
		localStorage.setItem('po_items', JSON.stringify(<?=$production_item;?>));
		<?php
		}
		?>
		if (localStorage.getItem('po_items')) {
			loadItems();
		}
		$(document).on('change', '.given_quantity', function () {
			var item_id = $(this).closest('tr').attr('data-item-id');
			po_items[item_id].given_quantity = $(this).val();
			localStorage.setItem('po_items', JSON.stringify(po_items));
		});
		$(document).on('change', '.given_unit', function () {
			var item_id = $(this).closest('tr').attr('data-item-id');
			po_items[item_id].given_units = $(this).val();
			localStorage.setItem('po_items', JSON.stringify(po_items));
		});
		$(document).on('change', '.balance_quantity', function () {
			var item_id = $(this).closest('tr').attr('data-item-id');
			po_items[item_id].balance_quantity = $(this).val();
			localStorage.setItem('po_items', JSON.stringify(po_items));
		});
		$(document).on('change', '.balance_unit', function () {
			var item_id = $(this).closest('tr').attr('data-item-id');
			po_items[item_id].balance_units = $(this).val();
			localStorage.setItem('po_items', JSON.stringify(po_items));
		});
		$('#warehouse_id').change(function (e) {
			localStorage.setItem('warehouse_id', $(this).val());
		});
		
		if (warehouse_id = localStorage.getItem('warehouse_id')) {
			$('#warehouse_id').val(warehouse_id);
		}
		$('#production_name').change(function (e) {
			localStorage.setItem('production_name', $(this).val());
		});
		
		if (production_name = localStorage.getItem('production_name')) {
			$('#production_name').val(production_name);
		}
		$('#production_date').change(function (e) {
			localStorage.setItem('production_date', $(this).val());
		});
		
		if (production_date = localStorage.getItem('production_date')) {
			$('#production_date').val(production_date);
		}
		
		if (production_date = localStorage.getItem('production_date')) {
			$('#production_date').val(production_date);
		}
		
		
			
		$('#reset').click(function (e) {
			bootbox.confirm(lang.r_u_sure, function (result) {
				if (result) {
					if (localStorage.getItem('po_items')) {
						localStorage.removeItem('po_items');
					}
					if (localStorage.getItem('warehouse_id')) {
						localStorage.removeItem('warehouse_id');
					}
					if (localStorage.getItem('production_name')) {
						localStorage.removeItem('production_name');
					}
					if (localStorage.getItem('production_date')) {
						localStorage.removeItem('production_date');
					}
					$('#modal-loading').show();
					location.reload();
				}
			});
		});
    });

</script>

