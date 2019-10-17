<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
if (!empty($variants)) {
    foreach ($variants as $variant) {
        $vars[] = addslashes($variant->name);
    }
} else {
    $vars = array();
}
?>
<script type="text/javascript" src="<?=$assets ?>js/sale_item.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('.gen_slug').change(function(e) {
            getSlug($(this).val(), 'recipe');
        });
        $("#subcategory").select2("destroy").empty().attr("placeholder", "<?= lang('select_category_to_load') ?>").select2({
            placeholder: "<?= lang('select_category_to_load') ?>", minimumResultsForSearch: 7, data: [
                {id: '', text: '<?= lang('select_subcategory') ?>'}
            ]
        });
	loadCategories('standard');
	//$('.addnew-recipe-category').show();
	//$('.addnew-product-category').hide();
	//
	//$('.addnew-recipe-subcategory').show();
	//	$('.addnew-product-subcategory').hide();
		
	$('#type').change(function () {
            var v = $(this).val();
	    if (v=="standard" || v=="production" || v=="quick_service" || v=="combo") {
		//$('.addnew-recipe-category').show();
		//$('.addnew-product-category').hide();
		//$('.addnew-recipe-subcategory').show();
		//$('.addnew-product-subcategory').hide();
	    }else{
		//$('.addnew-recipe-category').hide();
		//$('.addnew-product-category').show();
		//$('.addnew-recipe-subcategory').hide();
		//$('.addnew-product-subcategory').show();
	    }
	  
	
	
            $('#modal-loading').show();
            if (v) {
		loadCategories(v);
                
            } else {
                $("#subcategory").select2("destroy").empty().attr("placeholder", "<?= lang('select_category_to_load') ?>").select2({
                    placeholder: "<?= lang('select_subcategory') ?>",
                    minimumResultsForSearch: 7,
                    data: [{id: '', text: '<?= lang('select_subcategory') ?>'}]
                });//$('#subcategory').val(27)
            }
            $('#modal-loading').hide();
			$('#ajaxCall').hide();
        });
        $('#category').change(function () {
            var v = $(this).val();
	    var type = $('#type').val();
            $('#modal-loading').show();
            if (v) {
		
                $.ajax({
                    type: "get",
                    async: false,
                    url: "<?= admin_url('recipe/getrecipeSubCategories') ?>/"+type+"/" + v,
                    dataType: "json",
                    success: function (scdata) {
			console.log(scdata)
                        if (scdata != null) {
                            scdata.push({id: '', text: '<?= lang('select_subcategory') ?>'});
                            $("#subcategory").select2("destroy").empty().attr("placeholder", "<?= lang('select_subcategory') ?>").select2({
                                placeholder: "<?= lang('select_subcategory') ?>",
                                minimumResultsForSearch: 7,
                                data: scdata,
				
                            });
			    if ($('#subcategory-hidden').val()!='') {
				$h_v = $('#subcategory-hidden').val();
				$('#subcategory').val([$h_v]).trigger('change');
				$('#subcategory-hidden').val('');
			    }
			    
			    //$('#subcategory').val([27]).trigger('change');
			    //$('#subcategory').val(27);
                        } else {
                            $("#subcategory").select2("destroy").empty().attr("placeholder", "<?= lang('no_subcategory') ?>").select2({
                                placeholder: "<?= lang('no_subcategory') ?>",
                                minimumResultsForSearch: 7,
                                data: [{id: '', text: '<?= lang('no_subcategory') ?>'}],
				tags:true,
                            });//$('#subcategory').val(27)
                        }
                    },
                    error: function () {
                        bootbox.alert('<?= lang('ajax_error') ?>');
                        $('#modal-loading').hide();
                    }
                });
            } else {
                $("#subcategory").select2("destroy").empty().attr("placeholder", "<?= lang('select_category_to_load') ?>").select2({
                    placeholder: "<?= lang('select_subcategory') ?>",
                    minimumResultsForSearch: 7,
                    data: [{id: '', text: '<?= lang('select_subcategory') ?>'}]
                });//$('#subcategory').val(27)
            }
            $('#modal-loading').hide();
			$('#ajaxCall').hide();
        });
        $('#code').bind('keypress', function (e) {
            if (e.keyCode == 13) {
                e.preventDefault();
                return false;
            }
        });
	$('input[name="add_recipe"]').click(function(e){
	    e.preventDefault();
	    $('#piece-error,.group-error').remove();
	    if ($('.sale_type[value="2"]').is(":checked")) {
		if($('#piece').val()==''){
		    $('<label for="unit" id="piece-error" class="text-danger" style="display: block;">Please enter/select a value</label>').insertAfter('#piece');
		   // return false;
		}
	    }
	    $type_val  = $('#type').val();
	    if ($type_val == 'standard' || $type_val == 'combo' || $type_val == 'production' || $type_val == 'quick_service') {
		if($('#category').val()==''){
		    $('<label for="unit" class="group-error text-danger" style="display: block;">Please enter/select a value</label>').insertAfter('#sale-category-container');
		   
		}
		if($('#subcategory').val()==''){
		    $('<label for="unit"  class="group-error text-danger" style="display: block;">Please enter/select a value</label>').insertAfter('#sale-subcategory-container');		   
		}
		if($('#brand').val()==''){
		    $('<label for="unit"  class="group-error text-danger" style="display: block;">Please enter/select a value</label>').insertAfter('#sale-brand-container');		   
		}
		if($('#sale-purchase-cost').val()==''){
		    $('<label for="unit"  class="group-error text-danger" style="display: block;">Please enter/select a value</label>').insertAfter('#sale-purchase-cost');		   
		}
		if($('#sale-selling-price').val()==''){
		    $('<label for="unit"  class="group-error text-danger" style="display: block;">Please enter/select a value</label>').insertAfter('#sale-selling-price');		   
		}
	    }else{
		
		if ($('.recipe-group:checked').length==0 || $('.recipe-subgroup:checked').length==0 || $('.recipe-brand:checked').length==0) {
		    $('<label for="unit"  class="group-error text-danger" style="display: block;">Please enter/select a value</label>').insertBefore('.recipe-group-list');
		}
		$('.recipe-brand:checked').each(function(n,v){
		    $dindex = $(this).attr('data-index');
		    $pc_class = 'pc-'+$dindex;
		    $sp_class = 'sp-'+$dindex;
		  //  alert($('.'+$pc_class).val())
		    if ($('.'+$pc_class).val()=='') {
			$('<label for="unit"  class="group-error text-danger" style="font-size: 10px;display: block;">Please enter/select a value</label>').insertAfter('.'+$pc_class);
		    }
		    if ($('.'+$sp_class).val()=='') {
			$('<label for="unit"  class="group-error text-danger" style="font-size: 10px;display: block;">Please enter/select a value</label>').insertAfter('.'+$sp_class);
		    }
		});
	    }
	    if ($type_val == 'standard' || $type_val == 'combo' || $type_val == 'addon' || $type_val == 'production' || $type_val == 'quick_service') {
		if($('#kitchens_id').val()==''){
		    $('<label for="unit"  class="group-error text-danger" style="font-size: 13px;display: block !important;">Please enter/select a value</label>').insertAfter('#kitchen-dropdown-container');
		}
	    }
	    var canvasHeight = 60;
	    var canvas = document.getElementById('myCanvas');
	    var context = canvas.getContext('2d');
	    var maxWidth = 350;
	    var lineHeight = 35;
	    var x = (canvas.width - maxWidth) / 2;
	    var y = 60;
	    var text = $('#local_lang_name').val();
	    $arrayWords = [];
	    $stringLength = text.length;
	    $wordsCnt = Math.ceil($stringLength/20);
	    canvasHeight = (($wordsCnt-1)*40)+60;
	    var $start = 0;var $end =20;
	    for(var $n = 0; $n < $wordsCnt; $n++) {
		$str = text.substring($start, $end);
		$start = $end;$end =$start+20;
		$arrayWords.push(' '+$str);
	    }
	    
	    /// set height ///
	    $('#myCanvas').attr('height',canvasHeight);
	    ///// end-set height //////////
	    text =  $arrayWords.join('');
	    // context.font = '36px KHMEROSBATTAMBANG-REGULAR';
	    context.font = '36px AKbalthom Kbach';
	    context.fillStyle = '#333';
	    wrapText(context, text, x, y, maxWidth, lineHeight);
	    $('#recipe-name-img').val(canvas.toDataURL());
	    $('#recipe_form').submit();        
	});
    });
    function wrapText(context, text, x, y, maxWidth, lineHeight) {
        var words = text.split(' ');
        var line = '';

        for(var n = 0; n < words.length; n++) {
          var testLine = line + words[n] + ' ';
	  if(n==0){testLine =testLine.trim()}
          var metrics = context.measureText(testLine);
          var testWidth = metrics.width;
          if (testWidth > maxWidth && n > 0) {
            context.fillText(line, x, y);
            line = words[n] + ' ';
            y += lineHeight;
          }
          else {
            line = testLine;
          }
        }
        context.fillText(line, x, y);
	
    }
    
    function loadCategories(v) {
	$.ajax({
                    type: "get",
                    async: false,
                    url: "<?= admin_url('recipe/getRecipeCategories') ?>/" + v,
                    dataType: "json",
                    success: function (scdata) {
			console.log(scdata)
                        if (scdata != null) {
                            scdata.push({id: '', text: '<?= lang('select_category') ?>'});
                            $("#category").select2("destroy").empty().attr("placeholder", "<?= lang('select_category') ?>").select2({
                                placeholder: "<?= lang('select_category_to_load') ?>",
                                minimumResultsForSearch: 7,
                                data: scdata,
				
                            });
			    if ($('#category-hidden').val()!='') {
				$h_v = $('#category-hidden').val();
				$('#category').val([$h_v]).trigger('change');
				$('#category-hidden').val('');
			    }
			    
			    //$('#subcategory').val([27]).trigger('change');
			    //$('#subcategory').val(27);
                        } else {
                            $("#category").select2("destroy").empty().attr("placeholder", "<?= lang('no_category') ?>").select2({
                                placeholder: "<?= lang('no_category') ?>",
                                minimumResultsForSearch: 7,
                                data: [{id: '', text: '<?= lang('no_category') ?>'}],
				tags:true,
                            });//$('#subcategory').val(27)
                        }
                    },
                    error: function () {
                        bootbox.alert('<?= lang('ajax_error') ?>');
                        $('#modal-loading').hide();
                    }
                });
    }
    function loadSubcategories(type,v){
	
	$.ajax({
                    type: "get",
                    async: false,
                    url: "<?= admin_url('recipe/getrecipeSubCategories') ?>/"+type+"/" + v,
                    dataType: "json",
                    success: function (scdata) {
			console.log(scdata)
                        if (scdata != null) {
                            scdata.push({id: '', text: '<?= lang('select_subcategory') ?>'});
                            $("#subcategory").select2("destroy").empty().attr("placeholder", "<?= lang('select_subcategory') ?>").select2({
                                placeholder: "<?= lang('select_subcategory') ?>",
                                minimumResultsForSearch: 7,
                                data: scdata,
				
                            });
			    if ($('#subcategory-hidden').val()!='') {
				$h_v = $('#subcategory-hidden').val();
				//alert($h_v)
				$('#subcategory').val([$h_v]).trigger('change');
				$('#subcategory-hidden').val('');
			    }
			    
			    //$('#subcategory').val([27]).trigger('change');
			    //$('#subcategory').val(27);
                        } else {
                            $("#subcategory").select2("destroy").empty().attr("placeholder", "<?= lang('no_subcategory') ?>").select2({
                                placeholder: "<?= lang('no_subcategory') ?>",
                                minimumResultsForSearch: 7,
                                data: [{id: '', text: '<?= lang('no_subcategory') ?>'}],
				tags:true,
                            });//$('#subcategory').val(27)
                        }
                    },
                    error: function () {
                        bootbox.alert('<?= lang('ajax_error') ?>');
                        $('#modal-loading').hide();
                    }
                });
    }
</script>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('add_recipe'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
	   
            <div class="col-lg-12">

                <p class="introtext"><!-- <?php echo lang('enter_info'); ?> --></p>

                <?php
				$attrib = array( 'role' => 'form', 'id' => 'recipe_form');
                echo admin_form_open_multipart("recipe/add", $attrib)
                ?>
		<fieldset class="scheduler-border">
		    <legend class="scheduler-border"><?=lang('item_info')?></legend>
		    <div class="col-md-12">
			<div class="col-md-6">
			   <div class="form-group all">
				<?= lang("recipe_code", "code") ?>
				<div class="input-group">
				    <?= form_input('code', (isset($_POST['code']) ? $_POST['code'] : ($recipe ? $recipe->code : '')), 'class="form-control" id="code"  required="required"') ?>
				    <span class="input-group-addon pointer" id="random_num" style="padding: 1px 10px;">
					<i class="fa fa-random"></i>
				    </span>
				</div>
				<span class="help-block"><?= lang('you_scan_your_barcode_too') ?></span>
				<label for="code" class="text-danger"></label>
			    </div>
			    <div class="form-group all">
				<?= lang("recipe_name", "name") ?>
				<?= form_input('name', (isset($_POST['name']) ? $_POST['name'] : ($recipe ? $recipe->name : '')), 'class="form-control gen_slug" id="name" required="required"'); ?>
			    </div>
			    
			    <div class="form-group">
							<?= lang('local_lang_name', 'local_lang_name'); ?>
				<?= form_input('khmer_name', set_value('khmer_name'), 'class="form-control" id="local_lang_name" '); ?>
			    </div>
			     <div class="form-group">
				    <label><input type="checkbox" name="special_item" value="1"  class="special_item"> <?= lang('special_item') ?></label>
				    <label><input type="checkbox" name="item_customizable" value="1"  class="item_customizable"> <?= lang('item_customizable') ?></label>
				</div>
			    <div class="form-group">
				<?= lang("item_type", "type") ?>
				<?php
	
				//$opts = array('standard' => lang('standard'), 'combo' => lang('combo'), 'trade' => lang('trade'), 'production' => lang('production'), 'addon' => lang('addon'));
				$opts = array('standard' => lang('standard'), 'production' => lang('production'),'quick_service' => lang('quick_service'),'combo' => lang('combo'), 'addon' => lang('addon'),'semi_finished' => lang('semi_finished'),'raw' => lang('raw'),'service' => lang('service'));
				echo form_dropdown('type', $opts, (isset($_POST['type']) ? $_POST['type'] : ($recipe ? $recipe->type : '')), 'class="form-control" id="type" required="required"');
				?>
			    </div>
			    <div class="form-group sale_types">
				<?= lang("sale_type", "sale_type") ?>
				<?php
	
				$opts = array('1' => lang('dine_in'), '2' => lang('bbq'), '3' => lang('both'));
				
				foreach($opts as $k => $row){ ?>
				    <label><input type="radio" name="recipe_standard" value="<?=$k?>" <?php if(in_array($k,$_POST['recipe_standard'])){ echo 'checked="checked"';}?> class="sale_type"><?=$row?></label>
				<?php }
				
				?>
			    </div>
			    
			    <!--<div class="form-group all">
				<?= lang("category", "category") ?>
				<div class="input-group">
				    <?php
				    $cat[''] = "";
				    foreach ($categories as $category) {
					$cat[$category->id] = $category->name;
				    }
				    echo form_dropdown('category', $cat, (isset($_POST['category']) ? $_POST['category'] : ($recipe ? $recipe->category_id : '')), 'class="form-control select select-category" id="category" placeholder="' . lang("select") . " " . lang("category") . '" required="required" style="width:100%"')
				    ?>
				    <div class="input-group-addon no-print" style="padding: 2px 5px;">
					<a href="<?= admin_url('system_settings/add_recipecategory'); ?>" id="add-supplier1" class="external" data-toggle="modal" data-target="#myModal">
					    <i class="fa fa-2x fa-plus-square" id="addIcon1"></i>
					</a>
				    </div>
				</div>
			    </div>-->
			   
			    <?php //if(!$this->Settings->qsr){ ?>
				<div class="form-group kitchen-type-container">
						     <?= lang("kitchen_type", "kitchen_type"); ?>
						     <div class="input-group" style="width: 100%;" id="kitchen-dropdown-container">
				 <?php
				 $rk[''] = '';
				 foreach ($reskitchen as $kitchen) {
				     $rk[$kitchen->id] = $kitchen->name;
							     if($kitchen->is_default == 1){
								     $default_kitchen = $kitchen->id;
							     }
				 }
				 echo form_dropdown('kitchens_id', $rk, $default_kitchen, 'class="form-control" data-placeholder="' . lang("select") . ' ' . lang("kitchen_type") . '" style="width:100%;" id="kitchens_id" ');
				 ?>
				 </div>
				</div>
			    <?php //} ?>
			    <div class="form-group">
						     <?= lang("purchase_tax", "purchase_tax"); ?>
				 <?php
				 $tax_r[''] = '';
				 foreach ($tax_rates as $tax_rate) {
				     $tax_r[$tax_rate->id] = $tax_rate->code;
							     
				 }
				 echo form_dropdown('purchase_tax', $tax_r, $tax_rates[0]->id, 'class="form-control" data-placeholder="' . lang("select") . ' ' . lang("purchase_tax") . '" style="width:100%;" id="purchase_tax" required="required" ');
				 ?>
				 
				</div>
			</div>
			<div class="col-md-6">
			    <div class="<?= $recipe ? 'text-warning' : '' ?>">
                            <strong><?= lang("warehouse") ?></strong><br>                            
				<?php if (!empty($warehouses)) {
				    foreach ($warehouses as $warehouse) { ?>
				    <?php $checked="";if($this->Settings->default_warehouse==$warehouse->id) : 
				    $checked = 'checked="checked"';
				    endif; ?>
					<div class="form-group">
					    <input type="checkbox" class="checkbox" name="warehouse[]"  <?=$checked?> value="<?php echo $warehouse->id; ?>"> <?php echo $warehouse->name; ?>
					</div>
				<?php } }?>
			    </div>
			    <div class="form-group">
				<?= lang("supplier", "supplier") ?>
				<?php
				$sp = array();//false;
				foreach ($suppliers as $supplier) {
				    $sp[$supplier->id] = $supplier->name;
				}
				echo form_dropdown('supplier[]', $sp, (isset($_POST['supplier']) ? $_POST['supplier'] : ''), 'class="form-control select"  id="select-supplier" multiple placeholder="' . lang("select") . " " . lang("supplier") . '" style="width:100%"')
				?>
				
			    </div>
			    <div class="form-group">
				<?= lang("status", "type") ?>
				<?php
				$st = array('1' => lang('Active'),'0' => lang('Inactive'));
				echo form_dropdown('is_status', $st, (isset($_POST['is_status']) ? $_POST['is_status'] : ($product->active ? $product->active : 0)), 'class="form-control" id="is_status" required="required"');
				?>
			    </div>
			    <div class="form-group variant-container">
				<div class="form-group">
				    <?= lang("Varients", "varients") ?>
				    <!--<button type="button" class="btn btn-primary btn-xs" id="addNewVarient"><i class="fa fa-plus"></i>
				    </button>-->
				</div>
				<div class="row" id="varient-con">                            
				    <div class="col-xs-12">                                
					 <div class="form-group">
			<?php echo form_input('varients[]', set_value('recipe_addon'), 'class="form-control search-varients"  placeholder="' . lang("select") . ' ' . lang("varient") . '"  style="width:100%;" ');
					    ?>
					</div>
				    </div>                            
				</div>
				<div class="row">
				    <table class="varients-container">              
					<thead>
					<!--s-->
					<th class="col-sm-4"><?=lang('Variant_Name')?></th>
					<th class="col-sm-4"><?=lang('price')?></th>
					<th class="col-sm-2"><?=lang('Status')?></th>
					<th class="col-sm-2"><?=lang('remove')?></th>
					<th class="col-sm-2"><?=lang('preferred')?></th>
					</thead>
					<tbody>
					
					</tbody>
				    </table>
				</div>
			    </div>
			<!-------------------- based on types --------------->
			    
				<!---------- add on form --->
			    <div class="form-group " style="display: none">
				<div class="form-group">
				    <?= lang("addon", "addon") ?>
				    <button type="button" class="btn btn-primary btn-xs" id="addRecipe_addon"><i class="fa fa-plus"></i>
				    </button>
				</div>
				<div class="row" id="supplier-con">                            
				    <div class="col-xs-12">                                
					 <div class="form-group">
					    <?php
					    echo form_input('recipe_addon[]', set_value('recipe_addon'), 'class="form-control rrecipe_addon" id="recipe_addon_1"  placeholder="' . lang("select") . ' ' . lang("addon") . '"  style="width:100%;" ');
					    ?>
					</div>
				    </div>                            
				</div>
				<div id="ex-recipe_addon"></div>
			    </div>
				<!------- add sales items form ------------>
			    <div class="combo" style="display:none;">
				<div class="form-group">
				    <?= lang("add_recipe", "add_item"); ?>
				    <?php echo form_input('add_item', '', 'class="form-control ttip" id="add_item" data-placement="top" data-trigger="focus" data-bv-notEmpty-message="' . lang('please_add_items_below') . '" placeholder="' . $this->lang->line("add_item") . '"'); ?>
				</div>
				<div class="control-group table-group">
				    <label class="table-label" for="combo"><?= lang("combo_recipe"); ?></label>
	
				    <div class="controls table-controls">
					<table id="prTable"
					       class="table items table-striped table-bordered table-condensed table-hover">
					    <thead>
					    <tr>
						<th><?= lang('recipe') . ' ' . lang('name'); ?></th>
						 <th><?= lang("quantity"); ?></th>
						<th><?= lang("unit_price"); ?></th>
						<th class="col-md-1 col-sm-1 col-xs-1 text-center">
						    <i class="fa fa-trash-o" style="opacity:0.5; filter:alpha(opacity=50);"></i>
						</th>
					    </tr>
					    </thead>
					    <tbody></tbody>
					</table>
				    </div>
				</div>
			    </div>
			    <!-------------------- based on types --------------->
			    
			    <div class="form-group all recipe-image-container">
				<?= lang("recipe_image", "image") ?>
				<input id="recipe_image" type="file" data-browse-label="<?= lang('browse'); ?>" name="recipe_image" data-show-upload="false"
				       data-show-preview="false" accept="image/*" class="form-control file">
			    </div>
	
			    <!--<div class="form-group all">
				<?= lang("recipe_gallery_images", "images") ?>
				<input id="images" type="file" data-browse-label="<?= lang('browse'); ?>" name="userfile[]" multiple data-show-upload="false"
				       data-show-preview="false" class="form-control file" accept="image/*">
			    </div>
			    <div id="img-details"></div>-->
			    <?php if($this->Settings->recipe_time_management) : ?>
				<div class="form-group">
				    <?= lang("preparation_time", "preparation_time") ?>
				    <?= form_input('preparation_time', (isset($_POST['preparation_time']) ? $_POST['preparation_time'] : (($this->Settings->recipe_time_management && $this->Settings->default_preparation_time!=0)? $this->Settings->default_preparation_time : '')), 'class="form-control tip numberonly" maxlength="3" id="preparation_time"') ?>
				</div>
			    <?php endif; ?>	
			    
			 
			</div>
		    </div>
		</fieldset>
		
		<!--
		<fieldset class="scheduler-border">
		    <legend class="scheduler-border"><?=lang('purchase_items')?></legend>
		    <div class="standard" style="display: none;">
				<div class="form-group standard">
				    <div class="form-group">
					<?= lang("products", "products") ?>
					<!--<button type="button" class="btn btn-primary btn-xs" id="addRecipe_product"><i class="fa fa-plus"></i>
					</button>
				    </div>
				    <div class="row" id="supplier-con">
					<div class="col-xs-12">
					    <div class="form-group">
						<?php
						echo form_input('recipe_product[]', set_value('recipe_product'), 'class="search-purchase-items form-control" id="recipe_product_1"  placeholder="' . lang("select") . ' ' . lang("products") . '" style="width:100%;" ');
						?>
					    </div>
					</div>                            
					<div class="row col-xs-12">
					    <table class="purchase-item-container">              
						<thead>
					
						<th class="col-sm-4"><?=lang('Item_Name')?></th>
						<th class="col-sm-2"><?=lang('quanity')?></th>
						<th class="col-sm-2"><?=lang('unit')?></th>
						<!--<th class="col-sm-2"><?=lang('cost')?></th>
						<th class="col-sm-2"><?=lang('remove')?></th>
						</thead>
						<tbody>
						
						</tbody>
					    </table>
					</div>
					                         
					                           
					
					
				    
				    </div>
				    <div id="ex-recipe_product"></div>
				</div>	
			    </div>
		</fieldset> -->
			<fieldset class="scheduler-border">
		    <legend class="scheduler-border"><?=lang('batch_and_expiry_config')?></legend>
		    <div class="col-md-12">
			<div class="col-md-6">
			    <div class="form-group all">
				
                                        <label class="control-label" for="batch_required" style="padding: 4px 7px 4px 0px;"><?= lang("batch_required"); ?></label>
                                        <div class="switch-field">
                                        
                                            <input type="radio" value="0"  id="batch_required-switch-left" class="switch_left skip" name="batch_required" <?php echo ($Settings->batch_required==0) ? "checked" : ''; ?>>
                                            <label for="batch_required-switch-left">No</label>
                                            <input type="radio" value="1" id="batch_required-switch-right" class="switch_right skip" name="batch_required" <?php echo ($Settings->batch_required==1) ? "checked" : ''; ?>>
                                            <label for="batch_required-switch-right">YES</label>
                                        </div>
			    </div>
			</div>
			<div class="col-md-6">
			    <div class="form-group all">
				<label class="control-label" for="expiry_date_required" style="padding: 4px 7px 4px 0px;"><?= lang("expiry_date_required"); ?></label>
                                        <div class="switch-field">
                                        
                                            <input type="radio" value="0"  id="expiry_date_required-switch-left" class="switch_left skip" name="expiry_date_required" <?php echo ($Settings->expiry_date_required==0) ? "checked" : ''; ?>>
                                            <label for="expiry_date_required-switch-left">No</label>
                                            <input type="radio" value="1" id="expiry_date_required-switch-right" class="switch_right skip" name="expiry_date_required" <?php echo ($Settings->expiry_date_required==1) ? "checked" : ''; ?>>
                                            <label for="expiry_date_required-switch-right">YES</label>
                                        </div>
			    </div>
			</div>
			<div class="col-md-6">
			    <div class="form-group">
				<?= lang("type_expiry", "type_expiry") ?>
				<?php
				$type_e = array('' => 'Select Type', 'days' => lang('Day'),'months' => lang('Month'),'year' => lang('Year'));
				echo form_dropdown('type_expiry', $type_e, (isset($_POST['type_expiry']) ? $_POST['type_expiry'] : ($product ? $product->type_expiry : '')), 'class="form-control" id="type_expiry" ');
							
				?>
				<label for="type_expiry" class="text-danger"> is Empty</label>
			    </div>
			</div>
			<div class="col-md-6">
			    <div class="form-group all">
				<?= lang("value_expiry", "value_expiry") ?>
				<?= form_input('value_expiry', (isset($_POST['value_expiry']) ? $_POST['value_expiry'] : ($product ? $product->value_expiry : '')), 'class="form-control numberonly" id="value_expiry" maxlength="3"'); ?>
			    </div>			    
			</div>
		    </div>
		</fieldset>
		<fieldset class="scheduler-border">
		    <legend class="scheduler-border"><?=lang('Category_mapping')?></legend>
		    <div class="col-md-12">
			<div class="sale-category-mapping">
			    <div class="col-md-6">
				<div class="form-group all">
				   <?= lang("category", "category") ?>
				   <div class="input-group" id="sale-category-container">
				   <div class="controls" id="cat_data"> <?php
				       echo form_input('category', ($recipe ? $recipe->category_id : ''), 'class="form-control select-category" id="category"  placeholder="' . lang("select_category_to_load") . '"');
				       ?>
				   <input type="hidden" id="category-hidden">
				   </div>
				   <div class="input-group-addon no-print addnew-recipe-category" style="padding: 2px 5px;">
					   <a href="<?= admin_url('system_settings/add_recipecategory?type=add_sale'); ?>" id="add-supplier1" class="external" data-toggle="modal" data-target="#myModal">
					       <i class="fa fa-2x fa-plus-square" id="addIcon1"></i>
					   </a>
				   </div>
				   
				   </div>
			       </div>
			    </div>
			    <div class="col-md-6">
				<div class="form-group all">
				    <?= lang("subcategory", "subcategory") ?>
				    <div class="input-group" id="sale-subcategory-container">
				    <div class="controls" id="subcat_data"> <?php
					echo form_input('subcategory', ($recipe ? $recipe->subcategory_id : ''), 'class="form-control" id="subcategory"  placeholder="' . lang("select_subcategory") . '"');
					?>
				    <input type="hidden" id="subcategory-hidden">
				    </div>
    <div class="input-group-addon no-print addnew-recipe-subcategory" style="padding: 2px 5px;">
					    <a href="<?= admin_url('system_settings/add_recipecategory?sub=1&type=add_sale'); ?>" id="add-supplier1" class="external" data-toggle="modal" data-target="#myModal">
						<i class="fa fa-2x fa-plus-square" id="addIcon1"></i>
					    </a>
					</div>
    
					</div>
				</div>
			    </div>
			    <div class="col-md-3">			
				<div class="form-group">
				    <?= lang("brand", "brand") ?>
				    <div class="input-group" id="sale-brand-container">
					<?php
					    $br[''] = "";
					    foreach ($brands as $brand) {
						$br[$brand->id] = $brand->name;
					    }
					    echo form_dropdown('brand', $br, (isset($_POST['brand']) ? $_POST['brand'] : ($product ? $product->brand : '')), 'class="form-control select select-brand" id="brand" placeholder="' . lang("select") . " " . lang("brand") . '" style="width:100%" ');
					?>
					<div class="input-group-addon no-print" style="padding: 2px 5px;">
					    <a href="<?= admin_url('system_settings/add_brand'); ?>" id="add-supplier" class="external" data-toggle="modal" data-target="#myModal">
						<i class="fa fa-2x fa-plus-square" id="addIcon"></i>
					    </a>
					</div>
				    </div>
				    <label for="brand" class="text-danger"> is Empty</label>
				</div>
			    </div>
			    <div class="col-md-3">
								<div class="form-group">
								    <?= lang("purchase_cost", "purchase_cost") ?> 
								    
								<input type="text" name="purchase_cost" class="form-control tip numberonly" maxlength="15"  id="sale-purchase-cost">
								</div>
							    </div>
			    <div class="col-md-3">
				<div class="form-group">
				    <?= lang("selling_price", "selling_price") ?> 
				   
				    <input type="text" name="selling_price" class="form-control tip numberonly" maxlength="15"  id="sale-selling-price">
				</div>
			    </div>
			    <div class="col-md-3">
				<div class="form-group">
				    <?= lang("stock", "stock") ?> 
				   
				    <input type="text" name="stock" class="form-control tip numberonly" maxlength="15"  id="sale-stock">
				</div>
			    </div>
			</div>
			<div class="cate-group-row purchase-category-mapping" style="display: none;">
			    <?php $index = 0 ; ?>
			    <a href="<?=admin_url()?>system_settings/add_category?type=add_sale" id="add-supplier1" class="external" data-toggle="modal" data-target="#myModal" data-backdrop="static" style="float: right;"><i class="fa fa-2x fa-plus-square" id="addIcon1"></i>Add Category</a>
			    <div class="recipe-group-list">
				<ul class="level-1-menu">
				<?php foreach($recipe_groups as $kk => $row_1) : ?>
				    <li class="level-1-menu-li">
					<div class="level-1-menu-div">
				       <input type="hidden" name="" value="<?=@$row_1->id?>">
					<div class="category-name-container">
					    <input type="checkbox" name="" value="<?=@$row_1->id?>" class="recipe-group" data-index="<?=$index?>"><label for="pos-door_delivery_bils" class="category-name padding05">
					    &nbsp;<?=@$row_1->name?></label><span class="subgroup_hide_show"><i class="fa fa-plus-circle" aria-hidden="true"></i></span></div>
					<ul class="level-2-menu parent-cate-<?=$row_1->id?>" style="display: none;">
						<label class="subgroup-title">subgroups</label>
						<a href="<?=admin_url()?>system_settings/add_category?sub=1&parent=<?=$row_1->id?>&type=add_sale" id="add-supplier1" class="external" data-toggle="modal" data-target="#myModal" data-backdrop="static" style="float: right;"><i class="fa fa-2x fa-plus-square" id="addIcon1"></i>Add Sub Category</a>
					<?php if(!empty($row_1->sub_category)) : ?>
					    
						<?php foreach($row_1->sub_category as $sk => $row_2) : ?>
						<li  class="level-2-menu-li">
						    <div class="subgroup-strip">
						    <input type="hidden" name="" value="<?=@$row_2->id?>">
						    <input type="checkbox" name="" value="<?=@$row_2->id?>" class="recipe-subgroup" data-index="<?=$index?>"><label for="pos-door_delivery_bils" class="subgroup-name padding05">
						<?=@$row_2->name?></label><span class="recipe_hide_show"><i class="fa fa-plus-circle fa-minus-circle" aria-hidden="true"></i></span><label for="pos-door_delivery_bils" class="subgroup-item-excluded-label padding05"><input type="checkbox" name="group[<?=$index?>][recipe_group_id][<?=$kk?>][sub_category][<?=$sk?>][type]" value="excluded" class="subgroup-item-excluded skip" data-index="<?=$index?>">
						<?=@lang('excluded')?></label>
						    </div>
						    <?php if(!empty($brands)) : ?>
							<ul class="level-3-menu"><div class="items-title">brands</div>
							    <?php foreach($brands as $rk => $row_3) :
							    $checked = (in_array($row_3->id,$mapped_rids))?'checked="checked"':'';
							    
							    ?>
							    <li>
								<div class="col-md-3">
								<label for="pos-door_delivery_bils" class="recipe-brand-label padding05"><input type="checkbox" name="group[<?=$index?>][recipe_group_id][<?=@$row_1->id?>][sub_category][<?=@$row_2->id?>][brands][]" value="<?=@$row_3->id?>" class="recipe-brand" data-index="<?=@$index.'-'.$row_1->id.'-'.@$row_2->id.'-'.@$row_3->id?>" <?=$checked?>>
							    <?=@$row_3->name?></label>
								</div>
							    <div class="col-md-3">
								<div class="form-group">
								    <?= lang("purchase_cost", "purchase_cost") ?> 
								    
								<input type="text" name="group[<?=$index?>][recipe_group_id][<?=@$row_1->id?>][sub_category][<?=@$row_2->id?>][purchase_cost][]" class="form-control tip numberonly pc-<?=@$index.'-'.$row_1->id.'-'.@$row_2->id.'-'.@$row_3->id?>" maxlength="15">
								</div>
							    </div>
							    <div class="col-md-3">
								<div class="form-group">
								    <?= lang("selling_price", "selling_price") ?> 
								   
								    <input type="text" name="group[<?=$index?>][recipe_group_id][<?=@$row_1->id?>][sub_category][<?=@$row_2->id?>][selling_price][]" class="form-control tip numberonly sp-<?=@$index.'-'.$row_1->id.'-'.@$row_2->id.'-'.@$row_3->id?>"" maxlength="15">
								</div>
							    </div>
							    <div class="col-md-3">
								<div class="form-group">
								    <?= lang("stock", "stock") ?> 
								   
								    <input type="text" name="group[<?=$index?>][recipe_group_id][<?=@$row_1->id?>][sub_category][<?=@$row_2->id?>][stock][]" class="form-control tip numberonly sc-<?=@$index.'-'.$row_1->id.'-'.@$row_2->id.'-'.@$row_3->id?>"" maxlength="15">
								</div>
							    </div>
								
							    </li>
							    <?php endforeach; ?>
							</ul>
						    <?php endif; ?>
						</li>
						<?php endforeach; ?>
					    
					<?php endif; ?>
					</ul>
					</div>
				    </li>
				<?php endforeach; ?>
				</ul>
			    </div>
			</div>
			
			
			
			
			
		    </div>
		</fieldset>
		<fieldset class="scheduler-border">
		    <legend class="scheduler-border"><?=lang('price_details')?></legend>
		    <div class="col-md-12">
			<div class="col-md-4">
			    <div class="form-group">
				<?= lang("currency_type", "currency_type"); ?>
				<?php $currency_type ='';
				$rct[''] = '';
				foreach ($rescurrency as $currency) {
				    $rct[$currency->rate] = $currency->code;
				    if($this->Settings->default_currency == $currency->id){
					$currency_type = $currency->rate;
				    }
				}
				echo form_dropdown('currency_type', $rct, (isset($_POST['currency_type']) ? $_POST['currency_type'] : ($currency_type? $currency_type : '')), 'class="form-control" data-placeholder="' . lang("select") . ' ' . lang("currency_type") . '" style="width:100%;" id="currency_type" required="required" ');
				?>
				
			    </div>
			</div>
			<!--<div class="col-md-4">
			    <div class="form-group">
				<?= lang("purchase_cost", "purchase_cost") ?> *
				<?= form_input('purchase_cost', (isset($_POST['purchase_cost']) ? $_POST['purchase_cost'] : ($product ? $this->sma->formatDecimal($product->purchase_cost) : '')), 'class="form-control tip numberonly" maxlength="15" id="cost"') ?>
			    </div>
			</div>
			<div class="col-md-4">
			    <div class="form-group">
				<?= lang("selling_price", "selling_price") ?> *
				<?= form_input('cost', (isset($_POST['cost']) ? $_POST['cost'] : ($product ? $this->sma->formatDecimal($product->cost) : '')), 'class="form-control tip numberonly" maxlength="15" id="cost"') ?>
			    </div>
			</div>-->
		    </div>
		</fieldset>
		<fieldset class="scheduler-border">
		    <legend class="scheduler-border"><?=lang('UOM_details')?></legend>
		    <div class="col-md-12">
			<div class="col-md-6">
			    <div class="form-group ">
				<?= lang('product_unit', 'unit'); ?>
				<?php
				$pu[''] = lang('select').' '.lang('unit');
				foreach ($base_units as $bu) {
				    $pu[$bu->id] = $bu->name .' ('.$bu->code.')';
				}
				?>
				<?= form_dropdown('unit', $pu, set_value('unit', ($product ? $product->unit : '')), 'class="form-control tip" id="unit" required="required" style="width:100%;"'); ?>
			    </div>
			</div>
			<div class="col-md-6 sales_unit_container">
			    <div class="form-group ">
				<?= lang('sales_unit', 'sales_unit'); ?>
				<?php $uopts[''] = lang('select_unit_first');
				if(isset($sub_units)){
				    foreach ($sub_units as $su) {
				    $uopts[$su->id] = $su->name .' ('.$su->code.')';
				}} ?>
				<?= form_dropdown('default_sale_unit', $uopts, ($_POST['default_sale_unit'] ? $_POST['default_sale_unit'] : ''), 'class="form-control" id="default_sale_unit" style="width:100%;"'); ?>
			    </div>
			</div>
			<div class="col-md-6 purchase_unit_container" style="display: none;">
			    <div class="form-group ">
				<?= lang('purchase_unit', 'purchase_unit'); ?>
				<?= form_dropdown('default_purchase_unit', $uopts, ($_POST['default_sale_unit'] ? $_POST['default_sale_unit'] : ''), 'class="form-control" id="default_purchase_unit" style="width:100%;"'); ?>
			    </div>
			</div>
			<div class="col-md-6">
			    <div class="form-group">
				<?= lang("piece", "piece") ?>
				<?= form_input('piece', (isset($_POST['piece']) ? $_POST['piece'] : ($recipe ? $recipe->piece : '')), 'class="form-control tip numberonly" maxlength="3" id="piece"') ?>
			    </div>
			</div>
		    </div>
		</fieldset>
		<fieldset class="scheduler-border">
		    <legend class="scheduler-border"><?=lang('stock_details')?></legend>
		    <div class="col-md-12">
			<div class="col-md-6">
			    <div class="form-group">
				<?= lang("stock_quantity", "stock_quantity") ?>
				<?= form_input('stock_quantity', (isset($_POST['stock_quantity']) ? $_POST['stock_quantity'] : ($recipe ? $recipe->stock_quantity : '')), 'class="form-control tip numberonly" maxlength="3" id="stock_quantity"') ?>
			    </div>
			</div>
			<div class="col-md-6">
			    <div class="form-group all">
				<?= lang("maximum_quantity", "maximum_quantity") ?>
				<?= form_input('maximum_quantity', (isset($_POST['maximum_quantity']) ? $_POST['maximum_quantity'] : ($product ? $product->maximum_quantity : '')), 'class="form-control numberonly" id="maximum_quantity" maxlength="3" '); ?>
			    </div>
			</div>
			<div class="col-md-6">
			    <div class="form-group all">
				<?= lang("minimum_quantity", "minimum_quantity") ?>
				<?= form_input('minimum_quantity', (isset($_POST['minimum_quantity']) ? $_POST['minimum_quantity'] : ($product ? $product->minimum_quantity : '')), 'class="form-control numberonly" id="minimum_quantity" maxlength="3" '); ?>
			    </div>
			</div>
			<div class="col-md-6">			    
			    <div class="form-group all">
				<?= lang("reorder_quantity", "reorder_quantity") ?>
				<?= form_input('reorder_quantity', (isset($_POST['reorder_quantity']) ? $_POST['reorder_quantity'] : ($product ? $product->reorder_quantity : '')), 'class="form-control numberonly" id="reorder_quantity" maxlength="3"  '); ?>
			    </div>
			</div>
		    </div>
		</fieldset>
	
		
                <div class="col-md-5">                   
                    <?php if ($Settings->invoice_view == 2) { ?>
                        <div class="form-group">
                            <?= lang('hsn_code', 'hsn_code'); ?>
                            <?= form_input('hsn_code', set_value('hsn_code', ($recipe ? $recipe->hsn_code : '')), 'class="form-control" id="hsn_code"'); ?>
                        </div>
                    <?php } ?>
                    <input type="hidden" name="tax_amount" id="tax_amount" value="">
                    <input type="hidden" name="price" id="price" value="">
                </div>
                <div class="col-md-6 col-md-offset-1">
                     <div class="clearfix"></div>
                    <div class="digital" style="display:none;">
                        <div class="form-group digital">
                            <?= lang("digital_file", "digital_file") ?>
                            <input id="digital_file" type="file" data-browse-label="<?= lang('browse'); ?>" name="digital_file" data-show-upload="false"
                                   data-show-preview="false" class="form-control file">
                        </div>
                        <div class="form-group">
                            <?= lang('file_link', 'file_link'); ?>
                            <?= form_input('file_link', set_value('file_link'), 'class="form-control" id="file_link"'); ?>
                        </div>
                    </div>
		</div>
                </div>
		<div class="col-md-12">
		    <div class="col-md-12">
			<div class="pull-right form-group">
			    <canvas id="myCanvas" width="360" height="100" style="display: none;"></canvas>
			    <input type="hidden" name="recipe_name_img" id="recipe-name-img" value="">
			    <?php echo form_submit('add_recipe', $this->lang->line("add_recipe"), 'class="btn btn-primary"'); ?>
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
        $('form[data-toggle="validator"]').bootstrapValidator({ excluded: [':disabled'] });
        var audio_success = new Audio('<?= $assets ?>sounds/sound2.mp3');
        var audio_error = new Audio('<?= $assets ?>sounds/sound3.mp3');
        var items = {};
        <?php
        if($combo_items) {
            foreach($combo_items as $item) {
            //echo 'ietms['.$item->id.'] = '.$item.';';
                if($item->code) {
                    echo 'add_recipe_item('.  json_encode($item).');';
                }
            }
        }
        ?>
        <?=isset($_POST['cf']) ? '$("#extras").iCheck("check");': '' ?>
        $('#extras').on('ifChecked', function () {
            $('#extras-con').slideDown();
        });
        $('#extras').on('ifUnchecked', function () {
            $('#extras-con').slideUp();
        });

        <?= isset($_POST['promotion']) ? '$("#promotion").iCheck("check");': '' ?>
        $('#promotion').on('ifChecked', function (e) {
            $('#promo').slideDown();
        });
        $('#promotion').on('ifUnchecked', function (e) {
            $('#promo').slideUp();
        });

        $('.attributes').on('ifChecked', function (event) {
            $('#options_' + $(this).attr('id')).slideDown();
        });
        $('.attributes').on('ifUnchecked', function (event) {
            $('#options_' + $(this).attr('id')).slideUp();
        });
        //$('#cost').removeAttr('required');
        $('#digital_file').change(function () {
            if ($(this).val()) {
                $('#file_link').removeAttr('required');
                $('form[data-toggle="validator"]').bootstrapValidator('removeField', 'file_link');
            } else {
                $('#file_link').attr('required', 'required');
                $('form[data-toggle="validator"]').bootstrapValidator('addField', 'file_link');
            }
        });
        $('#type').change(function () {
            var t = $(this).val();
	    var sale_type = $('#sale_type').val();
	    if (t == 'standard' || t == 'combo' || t == 'production' || t == 'quick_service') {
		$('.sale_types').show();
		$('.variant-container').show();
		
	    }else{
		$('.sale_types').hide();
		$('.variant-container').hide();
		
	    }
	    //kitchen type
	     if (t == 'standard' || t == 'combo' || t == 'addon' || t == 'production' || t == 'quick_service') {
		$('.kitchen-type-container').show();
	    }else {
		$('.kitchen-type-container').hide();
	    }
	    //recipe-image-container
	    if (t == 'standard' || t == 'combo' || t == 'addon' || t == 'production' || t == 'quick_service') {
		$('.recipe-image-container').show();
	    }else {
		$('.recipe-image-container').hide();
	    }
	    //purchase unit - sale unit
	    if (t == 'standard' || t == 'combo' || t == 'addon' || t == 'production' || t == 'quick_service') {
		$('.purchase_unit_container').hide();
		$('.sales_unit_container').show();
	    }else {
		$('.purchase_unit_container').show();
		$('.sales_unit_container').hide();
	    }
	    
	    if (t == 'standard' || t == 'combo' || t == 'production' || t == 'addon' || t == 'service' || t == 'quick_service') {
		$('.sale-category-mapping').show();
		$('.purchase-category-mapping').hide();
	    }else{
		$('.sale-category-mapping').hide();
		$('.purchase-category-mapping').show();
	    }
            if (t == 'addon' || t == 'production' || t == 'quick_service') {
                $('.standard').slideDown();
		
		$('.raw_div').show();
		
            } else if(t == 'semi_finished'){
		
		$('.standard').slideDown();
		$('.raw_div').show();
	    }else {
                $('.standard').slideUp();
                //$('#track_quantity').iCheck('check');
                //$('#unit').attr('disabled', false);
               // $('#cost').attr('disabled', false);
            }
            if (t !== 'digital') {
                $('.digital').slideUp();
                $('#file_link').removeAttr('required');
                $('form[data-toggle="validator"]').bootstrapValidator('removeField', 'file_link');
            } else {
                $('.digital').slideDown();
                $('#file_link').attr('required', 'required');
                $('form[data-toggle="validator"]').bootstrapValidator('addField', 'file_link');
            }
            if (t !== 'combo') {
                $('.combo').slideUp();
            } else {
                $('.combo').slideDown();
            }
	    if(t == 'standard' || t=='combo' || t=="production" || t == 'quick_service') {
		    $('.addon').slideDown();
	    }else{
		$('.addon').slideUp();
	    }
            if (t == 'standard' || t == 'combo') {
                $('.standard_combo').slideDown();
            } else {
                $('.standard_combo').slideUp();
            }
        });

        var t = $('#type').val();
         if (t == 'addon' || t == 'production' || t == 'quick_service') {
                $('.standard').slideDown();
               // $('#unit').attr('disabled', true);
               // $('#cost').attr('disabled', true);
                //$('#track_quantity').iCheck('uncheck');
            } else {
                $('.standard').slideUp();
                //$('#track_quantity').iCheck('check');
                //$('#unit').attr('disabled', false);
               // $('#cost').attr('disabled', false);
            }
        if (t !== 'digital') {
            $('.digital').slideUp();
            $('#file_link').removeAttr('required');
            $('form[data-toggle="validator"]').bootstrapValidator('removeField', 'file_link');
        } else {
            $('.digital').slideDown();
            $('#file_link').attr('required', 'required');
            $('form[data-toggle="validator"]').bootstrapValidator('addField', 'file_link');
        }
        if (t !== 'combo') {
            $('.combo').slideUp();
        } else {
            $('.combo').slideDown();
        }
		if (t == 'addon') {
            $('.addon').slideUp();
        } else {
            $('.addon').slideDown();
        }
        if (t == 'standard' || t == 'combo') {
            $('.standard_combo').slideDown();
        } else {
            $('.standard_combo').slideUp();
        }

        $("#add_item").autocomplete({
            source: '<?= admin_url('recipe/suggestions'); ?>',
            minLength: 1,
            autoFocus: false,
            delay: 250,
            response: function (event, ui) {
                if ($(this).val().length >= 16 && ui.content[0].id == 0) {
                    //audio_error.play();
                    bootbox.alert('<?= lang('no_recipe_found') ?>', function () {
                        $('#add_item').focus();
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
                    bootbox.alert('<?= lang('no_recipe_found') ?>', function () {
                        $('#add_item').focus();
                    });
                    $(this).val('');

                }
            },
            select: function (event, ui) {
                event.preventDefault();
                if (ui.item.id !== 0) {
                    var row = add_recipe_item(ui.item);
                    if (row) {
                        $(this).val('');
                    }
                } else {
                    //audio_error.play();
                    bootbox.alert('<?= lang('no_recipe_found') ?>');
                }
            }
        });

        <?php
        if($this->input->post('type') == 'combo') {
            $c = isset($_POST['combo_item_code']) ? sizeof($_POST['combo_item_code']) : 0;
            for ($r = 0; $r <= $c; $r++) {
                if(isset($_POST['combo_item_code'][$r]) && isset($_POST['combo_item_quantity'][$r]) && isset($_POST['combo_item_price'][$r])) {
                    $items[] = array('id' => $_POST['combo_item_id'][$r], 'name' => $_POST['combo_item_name'][$r], 'code' => $_POST['combo_item_code'][$r], 'qty' => $_POST['combo_item_quantity'][$r], 'price' => $_POST['combo_item_price'][$r]);
                }
            }
            echo '
            var ci = '.(isset($items) ? json_encode($items) : "''").';
            $.each(ci, function() { add_recipe_item(this); });
            ';
        }
        ?>
        function add_recipe_item(item) {
            if (item == null) {
                return false;
            }
            item_id = item.id;
            if (items[item_id]) {
                items[item_id].qty = (items[item_id].qty) + 1;
            } else {
                items[item_id] = item;
            }
            var pp = 0;
            $("#prTable tbody").empty();
            $.each(items, function () {
                var row_no = this.id;
                var newTr = $('<tr id="row_' + row_no + '" class="item_' + this.id + '" data-item-id="' + row_no + '"></tr>');
				
                tr_html = '<td><input name="combo_item_id[]" type="hidden" value="' + this.id + '"><input name="combo_item_name[]" type="hidden" value="' + this.name + '"><input name="combo_item_code[]" type="hidden" value="' + this.code + '"><span id="name_' + row_no + '">' + this.name + '</span></td>';
				
				 tr_html += '<td><input class="form-control text-center rquantity" name="combo_item_quantity[]" type="number" value="'+ this.qty +'"></td>';
               
			    tr_html += '<td><input class="form-control text-center rprice" name="combo_item_price[]" type="text"  value="' + formatDecimal(this.price) + '" data-id="' + row_no + '" data-item="' + this.id + '" id="combo_item_price_' + row_no + '" onClick="this.select();"></td>';
				
                tr_html += '<td class="text-center"><i class="fa fa-times tip del" id="' + row_no + '" title="Remove" style="cursor:pointer;"></i></td>';
                newTr.html(tr_html);
                newTr.appendTo("#prTable");
                pp += formatDecimal(parseFloat(this.price)*parseFloat(this.qty));
            });
            $('.item_' + item_id).addClass('warning');
            $('#cost').val(pp);
            return true;
        }

        function calculate_price() {
			
            var rows = $('#prTable').children('tbody').children('tr');
            var pp = 0;
            $.each(rows, function () {
				
				
                pp += formatDecimal(parseFloat($(this).find('.rprice').val())*parseFloat($(this).find('.rquantity').val()));
            });
			
            $('#cost').val(pp);
            return true;
        }

        $(document).on('change', '.rquantity, .rprice', function () {
			
            calculate_price();
        });

        $(document).on('click', '.del', function () {
            var id = $(this).attr('id');
            delete items[id];
            $(this).closest('#row_' + id).remove();
            calculate_price();
        });
	$('.raw_div').hide();
	$(document).on('change', '#sale_type', function () {			
            var id = $(this).val();
	    var type = $('#type').val();
	    console.log(id)
	    		
        });
		
        var su = 2;
		
        $('#addRecipe_product').click(function () {
			var d = $('#sale_type').val();
			if(d == 1){
				$('.bbq_div').hide();
				$('.alakat_div').show();
				$('.p_div').hide();
			}else if(d == 2){
				$('.alakat_div').hide();
				$('.bbq_div').show();
				$('.p_div').hide();
			}else{
				$('.alakat_div').show();
				$('.bbq_div').show();
				$('.p_div').hide();
			}
			
            if (su <= 25) {
               
                var html = '<div style="clear:both;height:5px;"></div><div class="row product_box"><div class="col-xs-12"><div class="form-group"><input type="hidden" name="recipe_product[]", class="form-control rrecipe_product" id="recipe_product_' + su + '" placeholder="<?= lang("select") . ' ' . lang("product") ?>" style="width:100%;display: block !important;" /></div></div><div class="col-xs-3 alakat_div">Sukki</div><div class="col-xs-3 alakat_div"><div class="form-group"><input type="text" name="recipe_min_quantity[]"  id="recipe_min_quantity_' + su + '" class="form-control tip numberonly" maxlength="3"   placeholder="<?= lang('min_qty') ?>" /></div></div><div class="col-xs-3 alakat_div"><div class="form-group"><input type="text" name="recipe_max_quantity[]"  id="recipe_max_quantity_' + su + '" class="form-control tip numberonly" maxlength="3"  placeholder="<?= lang('max_qty') ?>" /></div></div><div class="col-xs-3 alakat_div"><div class="form-group"><input type="text" name="recipe_units[]"  id="recipe_units_' + su + '" class="form-control tip" readonly   placeholder="<?= lang('units') ?>" /><input type="hidden" name="recipe_units_id[]"  id="recipe_units_id_' + su + '" class="form-control tip"   placeholder="<?= lang('units') ?>" /></div></div><div class="col-xs-3 bbq_div">BBQ</div><div class="col-xs-3 bbq_div"><div class="form-group"><input type="text" name="bbq_min_quantity[]"  id="bbq_min_quantity_' + su + '" class="form-control tip numberonly" maxlength="3"   placeholder="<?= lang('min_qty') ?>" /></div></div><div class="col-xs-3 bbq_div"><div class="form-group"><input type="text" name="bbq_max_quantity[]"  id="bbq_max_quantity_' + su + '" class="form-control tip numberonly" maxlength="3"  placeholder="<?= lang('max_qty') ?>" /></div></div><div class="col-xs-3 bbq_div"><div class="form-group"><input type="text" name="bbq_units[]"  id="bbq_units_' + su + '" class="form-control tip" readonly   placeholder="<?= lang('units') ?>" /><input type="hidden" name="bbq_units_id[]"  id="bbq_units_id_' + su + '" class="form-control tip"   placeholder="<?= lang('units') ?>" /></div><button type="button" class="btn btn-primary btn-xs deleteRecipe_product"><i class="fa fa-trash-o"></i></button></div>';
				
				 html +='</div>';
				 
                $('#ex-recipe_product').append(html);
                var sup = $('#recipe_product_' + su);
                recipe_products(sup);
                su++;
				
				
				
            } else {
                bootbox.alert('<?= lang('max_reached') ?>');
                return false;
            }
        });
		
		$("body").on('click','.deleteRecipe_product', function(){
			$(this).closest('.product_box').remove();
		});
		
		 var ra = 2;
		
        $('#addRecipe_addon').click(function () {
            if (ra <= 25) {
               
                var html = '<div style="clear:both;height:5px;"></div><div class="row addon_box"><div class="col-xs-12"><div class="form-group"><input type="hidden" name="recipe_addon[]", class="form-control rrecipe_addon" id="recipe_addon_' + ra + '" placeholder="<?= lang("select") . ' ' . lang("addon") ?>" style="width:100%;display: block !important;" /></div></div><div class="col-xs-12"><button type="button" class="btn btn-primary btn-xs deleteRecipe_addon"><i class="fa fa-trash-o"></i></button></div>';
				
				 html +='</div>';
				 
                $('#ex-recipe_addon').append(html);
				var sup = $('#recipe_addon_' + ra);
                recipe_addon(sup);
                ra++;
				
				
				
            } else {
                bootbox.alert('<?= lang('max_reached') ?>');
                return false;
            }
        });
		
		$("body").on('click','.deleteRecipe_addon', function(){
			$(this).closest('.addon_box').remove();
		});
		
		$("body").on('change','.rrecipe_product', function(e) {
           var id = $(this).val();
		   var name = $(this).attr('id').substring(15);
		   $.ajax({
			url : '<?php echo admin_url('recipe/product_units'); ?>', 
			type : 'GET',
			data : 'term='+id,
			dataType : "json",
			success : function(data) {
				
				$('#recipe_units_'+name).val(data.results[0].name);
				$('#recipe_units_id_'+name).val(data.results[0].id);
				
				$('#bbq_units_'+name).val(data.results[0].name);
				$('#bbq_units_id_'+name).val(data.results[0].id);
			}
		});
		   
		});

        var _URL = window.URL || window.webkitURL;
        $("input#images").on('change.bs.fileinput', function () {
            var ele = document.getElementById($(this).attr('id'));
            var result = ele.files;
            $('#img-details').empty();
            for (var x = 0; x < result.length; x++) {
                var fle = result[x];
                for (var i = 0; i <= result.length; i++) {
                    var img = new Image();
                    img.onload = (function (value) {
                        return function () {
                            ctx[value].drawImage(result[value], 0, 0);
                        }
                    })(i);
                    img.src = 'images/' + result[i];
                }
            }
        });
        var variants = <?=json_encode($vars);?>;
        $(".select-tags").select2({
            tags: variants,
            tokenSeparators: [","],
            multiple: true
        });
        $(document).on('ifChecked', '#attributes', function (e) {
            $('#attr-con').slideDown();
        });
        $(document).on('ifUnchecked', '#attributes', function (e) {
            $(".select-tags").select2("val", "");
            $('.attr-remove-all').trigger('click');
            $('#attr-con').slideUp();
        });
        $('#addAttributes').click(function (e) {
            e.preventDefault();
            var attrs_val = $('#attributesInput').val(), attrs;
            attrs = attrs_val.split(',');
            for (var i in attrs) {
                if (attrs[i] !== '') {
                    
                        $('#attrTable').show().append('<tr class="attr"><td><input type="hidden" name="attr_name[]" value="' + attrs[i] + '"><span>' + attrs[i] + '</span></td><td class="quantity text-center"><input type="text" name="attr_quantity[]" value="0"><span></span></td><td class="price text-right"><input type="text" name="attr_price[]" value="0"></span></td><td class="text-center"><i class="fa fa-times delAttr"></i></td></tr>');
                    
                }
            }
        });
//$('#attributesInput').on('select2-blur', function(){
//    $('#addAttributes').click();
//});
        $(document).on('click', '.delAttr', function () {
            $(this).closest("tr").remove();
        });
        $(document).on('click', '.attr-remove-all', function () {
            $('#attrTable tbody').empty();
            $('#attrTable').hide();
        });
        var row, warehouses = <?= json_encode($warehouses); ?>;
        $(document).on('click', '.attr td:not(:last-child)', function () {
            row = $(this).closest("tr");
            $('#aModalLabel').text(row.children().eq(0).find('span').text());
            $('#awarehouse').select2("val", (row.children().eq(1).find('input').val()));
            $('#aquantity').val(row.children().eq(2).find('input').val());
            $('#aprice').val(row.children().eq(3).find('span').text());
            $('#aModal').appendTo('body').modal('show');
        });

        $('#aModal').on('shown.bs.modal', function () {
            $('#aquantity').focus();
            $(this).keypress(function( e ) {
                if ( e.which == 13 ) {
                    $('#updateAttr').click();
                }
            });
        });
        /*$(document).on('click', '#updateAttr', function () {
            var wh = $('#awarehouse').val(), wh_name;
            $.each(warehouses, function () {
                if (this.id == wh) {
                    wh_name = this.name;
                }
            });
            row.children().eq(1).html('<input type="hidden" name="attr_warehouse[]" value="' + wh + '"><input type="hidden" name="attr_wh_name[]" value="' + wh_name + '"><span>' + wh_name + '</span>');
            row.children().eq(2).html('<input type="hidden" name="attr_quantity[]" value="' + ($('#aquantity').val() ? $('#aquantity').val() : 0) + '"><span>' + decimalFormat($('#aquantity').val()) + '</span>');
            row.children().eq(3).html('<input type="hidden" name="attr_price[]" value="' + $('#aprice').val() + '"><span>' + currencyFormat($('#aprice').val()) + '</span>');
            $('#aModal').modal('hide');
        });*/
    });

    <?php if ($recipe) { ?>
    $(document).ready(function () {
        var t = "<?=$recipe->type?>";
        if (t !== 'standard') {
            $('.standard').slideUp();
           // $('#cost').attr('required', 'required');
           // $('#track_quantity').iCheck('uncheck');
            //$('form[data-toggle="validator"]').bootstrapValidator('addField', 'cost');
        } else {
            $('.standard').slideDown();
           // $('#track_quantity').iCheck('check');
           // $('#cost').removeAttr('required');
           // $('form[data-toggle="validator"]').bootstrapValidator('removeField', 'cost');
        }
        if (t !== 'digital') {
            $('.digital').slideUp();
            $('#file_link').removeAttr('required');
            $('form[data-toggle="validator"]').bootstrapValidator('removeField', 'file_link');
        } else {
            $('.digital').slideDown();
            $('#file_link').attr('required', 'required');
            $('form[data-toggle="validator"]').bootstrapValidator('addField', 'file_link');
        }
        if (t !== 'combo') {
            $('.combo').slideUp();
            //$('#add_item').removeAttr('required');
            //$('form[data-toggle="validator"]').bootstrapValidator('removeField', 'add_item');
        } else {
            $('.combo').slideDown();
            //$('#add_item').attr('required', 'required');
            //$('form[data-toggle="validator"]').bootstrapValidator('addField', 'add_item');
        }
        $("#code").parent('.form-group').addClass("has-error");
        $("#code").focus();
        $("#recipe_image").parent('.form-group').addClass("text-warning");
        $("#images").parent('.form-group').addClass("text-warning");
        $.ajax({
            type: "get", async: false,
            url: "<?= admin_url('recipe/getrecipeSubCategories') ?>/" + <?= $recipe->category_id ?>,
            dataType: "json",
            success: function (scdata) {
                if (scdata != null) {
                    $("#subcategory").select2("destroy").empty().attr("placeholder", "<?= lang('select_subcategory') ?>").select2({
                        placeholder: "<?= lang('select_subcategory') ?>",
                        data: scdata
                    });
		    
                } else {
                    $("#subcategory").select2("destroy").empty().attr("placeholder", "<?= lang('no_subcategory') ?>").select2({
                        placeholder: "<?= lang('no_subcategory') ?>",
                        data: [{id: '', text: '<?= lang('no_subcategory') ?>'}]
                    });
                }
            }
        });
      
      

        var whs = $('.wh');
        $.each(whs, function () {
            $(this).val($('#r' + $(this).attr('id')).text());
        });
    });
    <?php } ?>
    $(document).ready(function() {
        $('#unit').change(function(e) {
            var v = $(this).val();
            if (v) {
                $.ajax({
                    type: "get",
                    async: false,
                    url: "<?= admin_url('recipe/getSubUnits') ?>/" + v,
                    dataType: "json",
                    success: function (data) {
                        $('#default_sale_unit').select2("destroy").empty().select2({minimumResultsForSearch: 7});
                        $('#default_purchase_unit').select2("destroy").empty().select2({minimumResultsForSearch: 7});
                        $.each(data, function () {
                            $("<option />", {value: this.id, text: this.name+' ('+this.code+')'}).appendTo($('#default_sale_unit'));
                            $("<option />", {value: this.id, text: this.name+' ('+this.code+')'}).appendTo($('#default_purchase_unit'));
                        });
                        $('#default_sale_unit').select2('val', v);
                        $('#default_purchase_unit').select2('val', v);
                    },
                    error: function () {
                        bootbox.alert('<?= lang('ajax_error') ?>');
                    }
                });
            } else {
                $('#default_sale_unit').select2("destroy").empty();
                $('#default_purchase_unit').select2("destroy").empty();
                $("<option />", {value: '', text: '<?= lang('select_unit_first') ?>'}).appendTo($('#default_sale_unit'));
                $("<option />", {value: '', text: '<?= lang('select_unit_first') ?>'}).appendTo($('#default_purchase_unit'));
                $('#default_sale_unit').select2({minimumResultsForSearch: 7}).select2('val', '');
                $('#default_purchase_unit').select2({minimumResultsForSearch: 7}).select2('val', '');
            }
        });
    });
</script>
<script>
$(document).on('change', '#cost, #tax_rate, #tax_method', function () {
	
        var unit_price = parseFloat($('#cost').val());
		var pr_tax = $('#tax_rate').children(":selected").attr("data-id");
		var item_tax_method = $('#tax_method').val();
        var pr_tax_val = 0, pr_tax_rate = 0;

		if (item_tax_method == 0) {
			pr_tax_val = formatDecimal(((unit_price) * parseFloat(pr_tax)) / (100 + parseFloat(pr_tax)), 4);
			pr_tax_rate = formatDecimal('10.00') + '%';
			unit_price -= pr_tax_val;
		} else {
			pr_tax_val = formatDecimal((((unit_price) * parseFloat(pr_tax)) / 100), 4);
			pr_tax_rate = formatDecimal('10.00') + '%';
		}

        $('#price').val(unit_price);
        $('#tax_amount').val(pr_tax_val);
    });
</script>


<script type="text/javascript">

	$("#recipe_form").validate({
		ignore: [],
        
	});
	
</script>

<script>
     var $existingVarients = [];var $existing_purchase_items =[];
    $(document).ready(function(){
    $('.search-varients').autocomplete({
        html: true,
    source: function(request, response) {       
        $('ul.ui-autocomplete').html('');
                $.ajax({
                url: '<?= admin_url('recipe/search_varients')?>',
                type: 'post',
                dataType: 'json',
                data: 'term=' +request.term,
                success: function(data) {
         console.log(data)
                    $result = [];
                    $.each(data, function(n, v) {
                        var str = [];
                        str['label'] = v.name;
                        str['value'] =v.name;
                        str['id'] = v.id;
                        $result.push(str);
                    });
                    response($result);
                }
                });           
        },
        select: function (event, ui) {
            var label = ui.item.label;
            var value = ui.item.value;
            var id = ui.item.id;
        
        $html = '<li class="each-varient">';
        $html +='<strong>'+label+'</strong><input class="form-control" type="text" name="v_id[]" value="'+id+'"><input class="form-control" type="text" name="v_price[]" value=""><img name="v_file[]" src="">'
        $html +='</li>';
        $('.varients-container').prepend($html);
        $('.search-varients').val();
        }
    });
    $('#type_expiry').change(function(){
	$val = $(this).val();
	console.log($val)
	if ($val=="year"){ 
	   $('#value_expiry').attr('readonly','readonly');
	}else{
	    $('#value_expiry').attr('readonly',false);
	}
    });
    //$("#supplier option")[0].remove();
    });
</script>


<style>
    .each-varient td,.each-purchase-item td{
    text-align: center;
    }
    .each-varient td input,.each-purchase-item td input{
    margin-bottom: 10px;
    }
    label[for=warehouse[]]
    {
    display: inline-block;
    width: 160px !important;
    max-width: 187px;
    top: -22px;
    left: 60px;
    position: relative;
    }
    
    .purchase-item-quantity{
	width:84px;
    }
    .each-purchase-item input{
	padding: 0 !important;
    }
    .p-item-unit{
	    width: 37px !important;
	    font-size:12px;
    }
.switch-field {
  position: absolute;
  display: inline;
}

.switch-title {
  margin-bottom: 6px;
}

.switch-field input {
    position: absolute !important;
    clip: rect(0, 0, 0, 0);
    height: 1px;
    width: 1px;
    border: 0;
    overflow: hidden;
}

.switch-field label {
  float: left;
}

.switch-field label {
  display: inline-block;
  width: 35px;
  background-color: #fffff;
  color: #000000;
  font-size: 14px;
  font-weight: normal;
  text-align: center;
  text-shadow: none;
  padding: 3px 5px;
  border: 1px solid rgba(0, 0, 0, 0.2);
  -webkit-box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.3), 0 1px rgba(255, 255, 255, 0.1);
  box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.3), 0 1px rgba(255, 255, 255, 0.1);
  -webkit-transition: all 0.1s ease-in-out;
  -moz-transition:    all 0.1s ease-in-out;
  -ms-transition:     all 0.1s ease-in-out;
  -o-transition:      all 0.1s ease-in-out;
  transition:         all 0.1s ease-in-out;
}

.switch-field label:hover {
	cursor: pointer;
}

.switch-field input:checked + label {
  background-color: #2489c5;
  -webkit-box-shadow: none;
  box-shadow: none;
  color: #fff;
}

.switch-field label:first-of-type {
  border-radius: 13px 0 0 13px;
}

.switch-field label:last-of-type {
  border-radius: 0 13px 13px 0;
}

</style>
<style>
.recipe-group-list ul.level-1-menu li,.recipe-group-list ul.level-2-menu li {
    list-style: none;
   /* float: left;*/
    position: relative;
   /* margin-right: 20px;*/
    /*width: 200px;*/
}
.recipe-group-list ul.level-3-menu li {
    list-style: none;
    float: left !important;
    position: relative;
    margin-right: 20px;
    /*min-width: 200px !important;*/
    width:100%
}
.recipe-group-list ul.level-1-menu>li , .recipe-group-list ul.level-2-menu>li{
  clear: both;
}
.level-2-menu{
    text-indent:15px;
}
.level-3-menu{
    text-indent:25px;
}
.level-1-menu-li{
    padding: 5px;
    /*min-height: 130px;*/
}
.level-1-menu-div{
    background-color: #f8f6f6;
    border-radius: 10px;
    overflow: hidden;
    /*padding: 10px 10px 10px 10px;*/
    /*padding: 5px;*/
    position: relative;
    box-shadow: inset 0 3px 3px -3px rgba(0, 0, 0, 0.3);
    background: linear-gradient(181deg, #ffffff 0%, #ececec 100%);
}
.weekdays-selector{
    width:198px;
    float: right;
    text-indent: 1px;

}
.weekdays-selector input {
  display: none!important;
  margin-right: 3px;
}

.weekdays-selector label {
      display: inline-block;
    border-radius: 6px;
    background: #dddddd;
    height: 21px;
    width: 17px;
    margin-right: 3px;
    line-height: 23px;
    text-align: center;
    cursor: pointer;
}

.weekdays-selector input[type=checkbox]:checked + label {
  background: #2AD705;
  color: #ffffff;
}
.subgroup_hide_show{
    float: right;
    position: relative;
    /* background: grey; */
    /* float: left; */
    /* top: 24px; */
    right: 0.5%;
    top: 3px;
    font-size: 20px;
}
.level-1-menu-div .category-name-container{
    background: grey;
        padding: 4px;
        cursor: pointer;
    /*width: 100%;
    
    height: 32px;
    top: -4px;
    position: relative;*/
}
.subgroup-item-excluded-label{
    display: none;
}
.disabled-day + label{
    background: #d31919 !important;
  color: #ffffff !important;
}
.subgroup-title{
    text-indent: 5px;
    font-weight: bold;
    text-transform: uppercase;
}
.items-title{
   padding-left: 2%;
    font-weight: bold;
    text-transform: uppercase;
}
.subgroup-strip{
    padding: 4px;
        margin: 9px;
     background: linear-gradient(181deg, #ffffff 0%, #a0a0a0fc 100%);
}
.recipe_hide_show{
    float: right;
    font-size: 20px;
}
.weekday-disabled + label{
    background: #817e7a !important;
    color: #ffffff !important;
}
.recipe-brand{
    top:30px;
    position: relative;
}
.recipe-brand-label {
    margin-top:25px;
}
</style>

