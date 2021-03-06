<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('edit_recipe_and_variant_addon'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?php echo lang('enter_info'); ?></p>

                <?php
				$attrib = array('data-toggle' => 'validator', 'role' => 'form');
                echo admin_form_open_multipart("recipe/edit_recipe_and_variant_addon/".$addon->id)
                ?>

                <div class="col-md-6">
                    <div class="form-group">
                        <?= lang("sale_items", "sale_items"); ?>
                        <?php                        
                        $recipes['0'] =   lang("select");
                        foreach ($Allrecipe as $recipe) {
                            $recipes[$recipe->id] = $recipe->name;
                        }
                        echo form_dropdown('recipe_id', $recipes, $addon->recipe_id, 'class="form-control" data-placeholder="' . lang("select") . ' ' . lang("recipe") . '" required="required" id="recipe_id" style="width:100%;" ');
                        ?>                        
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <?= lang("variant_name", "variant_name"); ?>
                        <?php                        
                        $vari['0'] =   lang("select");
                        foreach ($recipevariants as $variant) {
                            $vari[$variant->id] = $variant->name;
                        }
                        echo form_dropdown('variant_id', $vari, $addon->variant_id, 'class="form-control" data-placeholder="' . lang("select") . ' ' . lang("variants") . '" required="required" id="variant_id" style="width:100%;" ');
                        ?>                        
                    </div>
                </div>
               
                <div class="col-xs-6 col-xs-offset-3">
                	<div class="form-group">
						<div class="form-group" style="margin-bottom:0;">
                            <div class="input-group wide-tip">
                                <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;">
                                    <i class="fa fa-2x fa-barcode addIcon"></i></a></div>
                                <?php echo form_input('addon_item', '', 'class="form-control input-lg" id="addon_item" placeholder="' . $this->lang->line("recipe_and_variant_addon_add") . '"'); ?>
                            </div>
                        </div>
                        
                    </div>
                </div>
              	<div class="col-xs-12"> 
                <table id="RecipeTable"  class="table items  table-bordered table-condensed sortable_table" style="background:#fff">
                    <thead>
                    <tr>
                       	<th>Code</th>
                        <th>Name</th>
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
					 <?php echo form_submit('add_purchases_order', $this->lang->line("submit"), 'id="add_purchases_order" class="btn btn-primary" style="padding: 6px 15px; margin:15px 0;"');  ?>
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
        $("#addon_item").autocomplete({
            source: '<?= admin_url('recipe/get_addon_suggestions'); ?>',
            minLength: 1,
            autoFocus: false,
            delay: 250,
            response: function (event, ui) {
                if ($(this).val().length >= 16 && ui.content[0].id == 0) {
                    bootbox.alert('<?= lang('no_addon_item_found') ?>', function () {
                        $('#addon_item').focus();
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
                    bootbox.alert('<?= lang('no_addon_item_found') ?>', function () {
                        $('#addon_item').focus();
                    });
                    $(this).val('');

                }
            },
            select: function (event, ui) {
                event.preventDefault();
                if (ui.item.id !== 0) {
										
                    var row = addon_item(ui.item);
                    if (row) {
                        $(this).val('');
                    }
                } else {                   
                    bootbox.alert('<?= lang('no_addon_item_found') ?>');
                }
            }
        });

       
        function addon_item(item) {
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
				 $("#RecipeTable tbody").empty();
				$.each(po_items, function () {
				
                var row_no = this.id;
				// var sale_units = this.sale_unit;
				// var given_units = this.given_units;
                var newTr = $('<tr id="row_' + row_no + '" class="item_' + this.id + '" data-item-id="' + row_no + '"></tr>');
                tr_html = '<td><input name="addon_id[]" id="addon_id_' + row_no + '" type="hidden" class="form-control" value="'+this.id+'"><input name="addon_code[]" id="addon_code_' + row_no + '" type="hidden" class="form-control" value="'+this.code+'"><input name="addon_name[]" id="addon_name_' + row_no + '" type="hidden" class="form-control" value="'+this.name+'"><span id="code_' + row_no + '">' + this.code + '</span></td>';
                tr_html += '<td><span id="name_' + row_no + '">' + this.name + '</span></td>';											
                tr_html += '<td><i class="fa fa-times tip del" id="' + row_no + '" title="Remove" style="cursor:pointer;"></i></td></tr>';
                newTr.html(tr_html);
                newTr.appendTo("#RecipeTable");
               
            });
			}
		
		}

        $(document).on('click', '.del', function () {
			var row = $(this).closest('tr');
			var item_id = row.attr('data-item-id');
			delete po_items[item_id];
			row.remove();	
          	
        });
		
		<?php if($addon_item){ ?>         

		localStorage.setItem('po_items', JSON.stringify(<?=$addon_item;?>));
        
		<?php } ?>
		if (localStorage.getItem('po_items')) {
			loadItems();
		}
		$(document).on('change', '.given_quantity', function () {
			var item_id = $(this).closest('tr').attr('data-item-id');
			po_items[item_id].given_quantity = $(this).val();
			localStorage.setItem('po_items', JSON.stringify(po_items));
		});
		
		
		
		
			
		$('#reset').click(function (e) {
			bootbox.confirm(lang.r_u_sure, function (result) {
				if (result) {
					if (localStorage.getItem('po_items')) {
						localStorage.removeItem('po_items');
					}					
					$('#modal-loading').show();
					location.reload();
				}
			});
		});
    });

</script>

