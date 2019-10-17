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
<script type="text/javascript">
    $(document).ready(function () {
		
        $('.gen_slug').change(function(e) {
            getSlug($(this).val(), 'products');
        });
        $("#subcategory").select2("destroy").empty().attr("placeholder", "<?= lang('select_category_to_load') ?>").select2({
            placeholder: "<?= lang('select_category_to_load') ?>", data: [
                {id: '', text: '<?= lang('select_category_to_load') ?>'}
            ]
        });
        $('#category').change(function () {
            var v = $(this).val();
            $('#modal-loading').show();
            if (v) {
                $.ajax({
                    type: "get",
                    async: false,
                    url: "<?= admin_url('products/getSubCategories') ?>/" + v,
                    dataType: "json",
                    success: function (scdata) {
                        if (scdata != null) {
                            scdata.push({id: '', text: '<?= lang('select_subcategory') ?>'});
                            $("#subcategory").select2("destroy").empty().attr("placeholder", "<?= lang('select_subcategory') ?>").select2({
                                placeholder: "<?= lang('select_category_to_load') ?>",
                                minimumResultsForSearch: 7,
                                data: scdata
                            });
                        } else {
                            $("#subcategory").select2("destroy").empty().attr("placeholder", "<?= lang('no_subcategory') ?>").select2({
                                placeholder: "<?= lang('no_subcategory') ?>",
                                minimumResultsForSearch: 7,
                                data: [{id: '', text: '<?= lang('no_subcategory') ?>'}]
                            });
                        }
                    },
                    error: function () {
                        bootbox.alert('<?= lang('ajax_error') ?>');
                        $('#modal-loading').hide();
                    }
                });
            } else {
                $("#subcategory").select2("destroy").empty().attr("placeholder", "<?= lang('select_category_to_load') ?>").select2({
                    placeholder: "<?= lang('select_category_to_load') ?>",
                    minimumResultsForSearch: 7,
                    data: [{id: '', text: '<?= lang('select_category_to_load') ?>'}]
                });
            }
            $('#modal-loading').hide();
        });
        $('#code').bind('keypress', function (e) {
            if (e.keyCode == 13) {
                e.preventDefault();
                return false;
            }
        });
    });
</script>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-edit"></i><?= lang('edit_product'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <p class="introtext"><?php echo lang('update_info'); ?></p>
                <?php
                $attrib = array('role' => 'form', 'id' => 'products_form');
                echo admin_form_open_multipart("products/edit/" . $product->id, $attrib)
                ?>
                <div class="col-md-5">
                	<div class="form-group">
                        <?= lang("supplier", "supplier") ?>
                        <?php
                        $sp[''] = "";
                        foreach ($suppliers as $supplier) {
                            $sp[$supplier->id] = $supplier->name;
                        }
                        echo form_dropdown('supplier', $sp, (isset($_POST['supplier']) ? $_POST['supplier'] : ($product ? $product->supplier1 : '')), 'class="form-control select"  placeholder="' . lang("select") . " " . lang("supplier") . '" style="width:100%"')
                        ?>
                    </div>
                    
                    <div class="form-group">
                        <?= lang("product_type", "type") ?>
                        <?php
                        $opts = array('' =>  'Select Type', 'semi_finished' => lang('semi_finished'),'raw' => lang('raw'),'service' => lang('service'));
                        echo form_dropdown('type', $opts, (isset($_POST['type']) ? $_POST['type'] : ($product ? $product->type : '')), 'class="form-control" id="type" required="required"');
                        ?>
                    </div>
                    <div class="form-group all">
                        <?= lang("product_name", "name") ?>
                        <?= form_input('name', (isset($_POST['name']) ? $_POST['name'] : ($product ? $product->name : '')), 'class="form-control gen_slug" id="name" required="required"'); ?>
                    </div>
                    
                     <div class="form-group all">
                        <?= lang("khmer_name", "khmer_name") ?>
                        <?= form_input('khmer_name', (isset($_POST['khmer_name']) ? $_POST['khmer_name'] : ($product ? $product->khmer_name : '')), 'class="form-control " id="khmer_name" required="required"'); ?>
                    </div>
                    
                    <div class="form-group all">
                        <?= lang("product_code", "code") ?>
                        <?= form_input('code', (isset($_POST['code']) ? $_POST['code'] : ($product ? $product->code : '')), 'class="form-control" id="code"  required="required"') ?>
                        <span class="help-block"><?= lang('you_scan_your_barcode_too') ?></span>
                    </div>
                    
                    <div class="form-group all" style="display:none ">
                        <?= lang("barcode_symbology", "barcode_symbology") ?>
                        <?php
                        $bs = array('code25' => 'Code25', 'code39' => 'Code39', 'code128' => 'Code128', 'ean8' => 'EAN8', 'ean13' => 'EAN13', 'upca' => 'UPC-A', 'upce' => 'UPC-E');
                        echo form_dropdown('barcode_symbology', $bs, (isset($_POST['barcode_symbology']) ? $_POST['barcode_symbology'] : ($product ? $product->barcode_symbology : 'code128')), 'class="form-control select" id="barcode_symbology" required="required" style="width:100%;"');
                        ?>
                    </div>

                    <div class="form-group all">
                       
                        <?= form_hidden('slug', set_value('slug', ($product ? $product->slug : '')), 'class="form-control tip" id="slug" required="required"'); ?>
                    </div>
					
                  
			
                   <div class="form-group all">
                        <?= lang("minimum_quantity", "minimum_quantity") ?>
                        <?= form_input('minimum_quantity', (isset($_POST['minimum_quantity']) ? $_POST['minimum_quantity'] : ($product ? $product->minimum_quantity : '')), 'class="form-control numberonly" id="minimum_quantity" maxlength="3" '); ?>
                    </div>
                    
                    <div class="form-group all">
                        <?= lang("reorder_quantity", "reorder_quantity") ?>
                        <?= form_input('reorder_quantity', (isset($_POST['reorder_quantity']) ? $_POST['reorder_quantity'] : ($product ? $product->reorder_quantity : '')), 'class="form-control numberonly" maxlength="3" id="reorder_quantity" '); ?>
                    </div>
                    
                    <div class="form-group all">
                        <?= lang("open_stock_quantity", "open_stock_quantity") ?>
                        <?= form_input('open_stock_quantity', (isset($_POST['open_stock_quantity']) ? $_POST['open_stock_quantity'] : ($product ? $product->open_stock_quantity : '')), 'class="form-control numberonly" maxlength="3" id="open_stock_quantity" '); ?>
                    </div>
                    
                    
                    
                    <div class="form-group">
                        <?= lang("type_expiry", "type_expiry") ?>
                        <?php
                        $type_e = array('' => 'Select Type', 'day' => lang('Day'),'month' => lang('Month'),'year' => lang('Year'));
                        echo form_dropdown('type_expiry', $type_e, (isset($_POST['type_expiry']) ? $_POST['type_expiry'] : ($product ? $product->type_expiry : '')), 'class="form-control" id="type_expiry" ');
						
                        ?>
                    </div>
                    <div class="form-group all">
                        <?= lang("value_expiry", "value_expiry") ?>
                        <?= form_input('value_expiry', (isset($_POST['value_expiry']) ? $_POST['value_expiry'] : ($product ? $product->value_expiry : '')), 'class="form-control numberonly" maxlength="3" id="value_expiry" '); ?>
                    </div>
                    
                    <div class="form-group all">
                        <?= lang("brand") ?>
                        <?php
                        $br[''] = "";
                        foreach ($brands as $brand) {
                            $br[$brand->id] = $brand->name;
                        }
                        echo form_dropdown('brand', $br, (isset($_POST['brand']) ? $_POST['brand'] : ($product ? $product->brand : '')), 'class="form-control select" id="brand" placeholder="' . lang("select") . " " . lang("brand") . '" style="width:100%"')
                        ?>
                    </div>
                    <div class="form-group all">
                        <?= lang("group", "category") ?>
                        <?php
                        $cat[''] = "";
                        foreach ($categories as $category) {
                            $cat[$category->id] = $category->name;
                        }
                        echo form_dropdown('category', $cat, (isset($_POST['category']) ? $_POST['category'] : ($product ? $product->category_id : '')), 'class="form-control select" id="category" placeholder="' . lang("select") . " " . lang("category") . '" required="required" style="width:100%"')
                        ?>
                    </div>
                    <div class="form-group all">
                        <?= lang("subgroup", "subcategory") ?>
                        <div class="controls" id="subcat_data"> <?php
                            echo form_input('subcategory', ($product ? $product->subcategory_id : ''), 'class="form-control" id="subcategory"  placeholder="' . lang("select_category_to_load") . '"');
                            ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <?= lang('product_unit', 'unit'); ?>
                        <?php
                        $pu[''] = lang('select').' '.lang('unit');
                        foreach ($base_units as $bu) {
                            $pu[$bu->id] = $bu->name .' ('.$bu->code.')';
                        }
                        ?>
                        <?= form_dropdown('unit', $pu, set_value('unit', $product->unit), 'class="form-control tip" required="required" id="unit" style="width:100%;"'); ?>
                    </div>
                    <div class="form-group">
                        <?= lang('default_sale_unit', 'default_sale_unit'); ?>
                        <?php
                        $uopts[''] = lang('select').' '.lang('unit');
                        foreach ($subunits as $sunit) {
                            $uopts[$sunit->id] = $sunit->name .' ('.$sunit->code.')';
                        }
                        ?>
                        <?= form_dropdown('default_sale_unit', $uopts, $product->sale_unit, 'class="form-control" id="default_sale_unit" style="width:100%;"'); ?>
                    </div>
                    <div class="form-group">
                        <?= lang('default_purchase_unit', 'default_purchase_unit'); ?>
                        <?= form_dropdown('default_purchase_unit', $uopts, $product->purchase_unit, 'class="form-control" id="default_purchase_unit" style="width:100%;"'); ?>
                    </div>
                    <div class="form-group all">
                        <?= lang('product_cost', 'product_cost'); ?>
                        <?= form_input('product_cost', $product->cost, 'class="form-control tip numberonly" maxlength="15" id="product_cost"'); ?>
                    </div>
                    
                   
                    <div class="form-group all">
                        <?= lang("product_image", "product_image") ?>
                        <input id="product_image" type="file" data-browse-label="<?= lang('browse'); ?>" name="product_image" data-show-upload="false"
                               data-show-preview="false" accept="image/*" class="form-control file">
                    </div>

                    <div class="form-group all">
                        <?= lang("product_gallery_images", "images") ?>
                        <input id="images" type="file" data-browse-label="<?= lang('browse'); ?>" name="userfile[]" multiple data-show-upload="false"
                               data-show-preview="false" class="form-control file" accept="image/*">
                    </div>
                    <div id="img-details"></div>
                </div>
                

				<div class="col-xs-7">
                	<?php if($product->type == 'semi_finished' ){  $b = 'block'; }else{   $b = 'none'; } ?>
                	<div class="form-group semi_finished" style="display:<?php echo $b; ?>">
                        <div class="form-group">
                            <?= lang("Purchase Items", "Purchase Items") ?>
                            <button type="button" class="btn btn-primary btn-xs" id="addRaw_product"><i class="fa fa-plus"></i>
                            </button>
                        </div>
                        <div class="row" id="supplier-con">
                        	<?php
							
							for($j=0; $j<count($product_raw); $j++){
								
							?>
                        	<div class="product_box">
                            <div class="col-xs-12">
                                <div class="form-group">
                                	 <input type="text" name="" id="rraw_product_name_<?php echo $j; ?>" value="<?php echo $product_raw[$j]->product_name; ?>" class="form-control ">
                                    <?php
                                    echo form_input('raw_product[]', $product_raw[$j]->raw_id, 'class="form-control rraw_product" id="raw_product_'.$j.'"  placeholder="' . lang("select") . ' ' . lang("Purchases Items") . '" style="width:100%; margin-top:-54px; opacity:0;" ');
                                    ?>
                                   
                                </div>
                            </div>
                            <div class="col-xs-4">
                                <div class="form-group">
                                    <?= form_input('raw_min_quantity[]', $product_raw[$j]->min_quantity, 'class="form-control tip numberonly"  maxlength="3"  id="raw_min_quantity_'.$j.'" placeholder="' . lang('min_quantity') . '"'); ?>
                                </div>
                            </div>
                            <div class="col-xs-4">
                                <div class="form-group">
                                    <?= form_input('raw_max_quantity[]', $product_raw[$j]->max_quantity, 'class="form-control tip numberonly"   maxlength="3" id="raw_max_quantity_'.$j.'" placeholder="' . lang('max_quantity') . '"'); ?>
                                </div>
                            </div>
                           
                            <div class="col-xs-4">
                                <div class="form-group">
                                   <?= form_input('raw_units[]', $product_raw[$j]->units_name, 'class="form-control tip" readonly  id="raw_units_'.$j.'" placeholder="' . lang('units') . '"'); ?>
                                 
                                   
                                 <input type="hidden" name="raw_units_id[]" id="raw_units_id_<?php echo $j; ?>" value="<?php echo $product_raw[$j]->units_id; ?>">
                                </div>
                                <?php
								if($j != 0){
								?>
                                <button type="button" class="btn btn-primary btn-xs deleteRaw_product"><i class="fa fa-trash-o"></i></button>
                                <?php
								}
								?>
                            </div>
                            </div>
                            <?php
							}
							?>
                        </div>
                        <div id="ex-raw_product"></div>
                    </div>
                </div>
                
                <div class="col-md-12">
					

                    <div class="form-group all">
                        <?= lang("product_details", "product_details") ?>
                        <?= form_textarea('product_details', (isset($_POST['product_details']) ? $_POST['product_details'] : ($product ? $product->product_details : '')), 'class="form-control" id="details"'); ?>
                    </div>
                    <div class="form-group all">
                        <?= lang("product_details_for_invoice", "details") ?>
                        <?= form_textarea('details', (isset($_POST['details']) ? $_POST['details'] : ($product ? $product->details : '')), 'class="form-control" id="details"'); ?>
                    </div>
					
                    <div class="form-group">
                        <?= lang("status", "type") ?>
                        <?php
                        $st = array('1' => lang('Active'),'0' => lang('Inactive'));
                        echo form_dropdown('is_status', $st, (isset($_POST['is_status']) ? $_POST['is_status'] : ($product->active ? $product->active : 0)), 'class="form-control" id="is_status" required="required"');
                        ?>
                    </div>
                    
                    <div class="form-group">
                        <?php echo form_submit('edit_product', $this->lang->line("edit_product"), 'class="btn btn-primary"'); ?>
                    </div>

                </div>
                <?= form_close(); ?>

            </div>

        </div>
    </div>
</div>
<script>
var rp = <?php echo count($product_raw) ?> + 1;
		
        $('#addRaw_product').click(function () {
			
            if (rp <= 25) {
               
                var html = '<div style="clear:both;height:5px;"></div><div class="row"><div class="col-xs-12"><div class="form-group"><input type="hidden" name="raw_product[]", class="form-control rraw_product" id="raw_product_' + rp + '" placeholder="<?= lang("select") . ' ' . lang("product") ?>" style="width:100%;display: block !important;" /></div></div><div class="col-xs-4"><div class="form-group"><input type="text" name="raw_min_quantity[]"  id="raw_min_quantity_' + rp + '" class="form-control tip numberonly" maxlength="3"   placeholder="<?= lang('min_quantity') ?>" /></div></div><div class="col-xs-4"><div class="form-group"><input type="text" name="raw_max_quantity[]"  id="raw_max_quantity_' + rp + '" class="form-control tip numberonly" maxlength="3"  placeholder="<?= lang('max_quantity') ?>" /></div></div><div class="col-xs-4"><div class="form-group"><input type="text" name="raw_units[]"  id="raw_units_' + rp + '" class="form-control tip" readonly   placeholder="<?= lang('units') ?>" /><input type="hidden" name="raw_units_id[]"  id="raw_units_id_' + rp + '" class="form-control tip"   placeholder="<?= lang('units') ?>" /></div><button type="button" class="btn btn-primary btn-xs deleteRaw_product"><i class="fa fa-trash-o"></i></button></div>';
				
				 html +='</div>';
				 
                $('#ex-raw_product').append(html);
                var sup = $('#raw_product_' + rp);
                raw_products(sup);
                rp++;
				
				
				
            } else {
                bootbox.alert('<?= lang('max_reached') ?>');
                return false;
            }
        });
		
		$("body").on('click','.deleteRaw_product', function(){
			$(this).closest('.product_box').remove();
		});
		
		
		
		
		$("body").on('change','.rraw_product', function(e) {
           var id = $(this).val();
		   var name = $(this).attr('id').substring(12);
		   $('#rraw_product_name_'+name).hide();
			$('.rraw_product').css({"opacity":"1","margin-top":"0px"});	
		   $.ajax({
			url : '<?php echo admin_url('products/raw_units'); ?>', 
			type : 'GET',
			data : 'term='+id,
			dataType : "json",
			success : function(data) {
				
				$('#raw_units_'+name).val(data.results[0].name);
				$('#raw_units_id_'+name).val(data.results[0].id);
				
			}
		});
		   
		});

</script>

<script type="text/javascript">
    $(document).ready(function () {
      
        //$('#cost').removeAttr('required');
        $('#type').change(function () {
            var t = $(this).val();
            if (t !== 'semi_finished') {
                $('.semi_finished').slideUp();
                //$('#unit').attr('disabled', true);
               // $('#cost').attr('disabled', true);
            } else {
                $('.semi_finished').slideDown();
               // $('#unit').attr('disabled', false);
               // $('#cost').attr('disabled', false);
            }
            if (t !== 'digital') {
                $('.digital').slideUp();
            } else {
                $('.digital').slideDown();
            }
            if (t !== 'combo') {
                $('.combo').slideUp();
            } else {
                $('.combo').slideDown();
            }
            if (t == 'semi_finished' || t == 'combo') {
                $('.semi_finished_combo').slideDown();
            } else {
                $('.semi_finished_combo').slideUp();
            }
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
       
	   

       
    });

</script>





<script type="text/javascript">

	$("#products_form").validate({
		ignore: [],
        
	});
	
	

</script>