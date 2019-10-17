<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('Edit Preparation'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?php echo lang('enter_info'); ?></p>

                <?php
				$attrib = array('data-toggle' => 'validator', 'role' => 'form');
                echo admin_form_open_multipart("preparation/edit/".$preparation->id)
                ?>

                <div class="col-md-6">
                    <div class="form-group">
						<?= lang("warehouse", "warehouse"); ?>
                        <?php
                        $wh[''] = '';
                        foreach ($warehouses as $warehouse) {
                            $wh[$warehouse->id] = $warehouse->name;
                        }
                        echo form_dropdown('warehouse_id', $wh, $preparation->warehouse_id, 'class="form-control" data-placeholder="' . lang("select") . ' ' . lang("warehouse") . '" required="required" id="warehouse_id" style="width:100%;" ');
                        ?>
                        
                    </div>
                </div>   
				
                 <div class="col-md-6">   
                   <div class="form-group">
                        <?= lang("Preparation Date", "Preparation Date") ?>
                        <?= form_input('preparation_date', (isset($_POST['preparation_date']) ? $_POST['preparation_date'] : $preparation->preparation_date), 'class="form-control tip date" id="preparation_date" required="required"') ?>
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
                <table id="preparationTable"  class="table items  table-bordered table-condensed sortable_table" style="background:#fff">
                    <thead>
                    <tr>
                       	<th>Code</th>
                        <th>Name</th>
                        <th>Given Quantity</th>
                       
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
					<?php if($preparation->status == 'Open'){ echo form_submit('add_purchases_order', $this->lang->line("submit"), 'id="add_purchases_order" class="btn btn-primary" style="padding: 6px 15px; margin:15px 0;"');  } ?>
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
            source: '<?= admin_url('preparation/suggestions'); ?>',
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
										
                    var row = add_preparation_item(ui.item);
                    if (row) {
                        $(this).val('');
                    }
                } else {
                    //audio_error.play();
                    bootbox.alert('<?= lang('no_purchases_item_found') ?>');
                }
            }
        });

       
        function add_preparation_item(item) {
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
				 $("#preparationTable tbody").empty();
				$.each(po_items, function () {
				
                var row_no = this.id;
				var sale_units = this.sale_unit;
				var given_units = this.given_units;
                var newTr = $('<tr id="row_' + row_no + '" class="item_' + this.id + '" data-item-id="' + row_no + '"></tr>');
                tr_html = '<td><input name="product_id[]" id="product_id_' + row_no + '" type="hidden" class="form-control" value="'+this.id+'"><input name="quantity[]" id="quantity_' + row_no + '" type="hidden" class="form-control" value="'+this.quantity+'"><input name="sale_unit[]" id="sale_unit_' + row_no + '" type="hidden" class="form-control" value="'+this.sale_unit+'"><input name="product_code[]" id="product_code_' + row_no + '" type="hidden" class="form-control" value="'+this.code+'"><input name="product_name[]" id="product_name_' + row_no + '" type="hidden" class="form-control" value="'+this.name+'"><span id="code_' + row_no + '">' + this.code + '</span></td>';
                tr_html += '<td><span id="name_' + row_no + '">' + this.name + '</span></td>';
								
				
				tr_html += '<td><input name="given_quantity[]" id="given_quantity_' + row_no + '" data-item="' + row_no + '" class="form-control given_quantity" value="'+this.given_quantity+'"></td>';
				/*tr_html += '<td><select class="form-control given_unit" id="given_unit_' + row_no + '" data-item="' + row_no + '" name="given_unit[]" value="" data-id="' + row_no + '" data-item="' + row_no + '" id="given_unit_' + row_no + '">';
				 $.each(this.units, function () {
					if(given_units == this.id){
						given_select = "selected";
					}else{
						given_select = "";
					}
					 tr_html += '<option value="'+ this.id +'"  '+given_select+'> '+ this.name +'</option>';
				 });
			 
			    tr_html += '</select></td>';*/
			 
                tr_html += '<td><i class="fa fa-times tip del" id="' + row_no + '" title="Remove" style="cursor:pointer;"></i></td>';
                newTr.html(tr_html);
                newTr.appendTo("#preparationTable");
               
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
		if($preparation_item){
		?>
		localStorage.setItem('warehouse_id', '<?= $preparation->warehouse_id?>');
        //localStorage.setItem('preparation_name', '<?=$preparation->preparation_name?>');
        localStorage.setItem('preparation_date', '<?=$preparation->preparation_date?>');
		localStorage.setItem('po_items', JSON.stringify(<?=$preparation_item;?>));
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
		
		$('#warehouse_id').change(function (e) {
			localStorage.setItem('warehouse_id', $(this).val());
		});
		
		if (warehouse_id = localStorage.getItem('warehouse_id')) {
			$('#warehouse_id').val(warehouse_id);
		}
		
		$('#preparation_date').change(function (e) {
			localStorage.setItem('preparation_date', $(this).val());
		});
		
		if (preparation_date = localStorage.getItem('preparation_date')) {
			$('#preparation_date').val(preparation_date);
		}
		
		if (preparation_date = localStorage.getItem('preparation_date')) {
			$('#preparation_date').val(preparation_date);
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
					
					if (localStorage.getItem('preparation_date')) {
						localStorage.removeItem('preparation_date');
					}
					$('#modal-loading').show();
					location.reload();
				}
			});
		});
    });

</script>

