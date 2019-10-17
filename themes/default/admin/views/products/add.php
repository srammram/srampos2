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
            placeholder: "<?= lang('select_category_to_load') ?>", minimumResultsForSearch: 7, data: [
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
			    if ($('#subcategory-hidden').val()!='') {
				$h_v = $('#subcategory-hidden').val();
				$('#subcategory').val([$h_v]).trigger('change');
				$('#subcategory-hidden').val('');
			    }
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
			$('#ajaxCall').hide();
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
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('add_product'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?php echo lang('enter_info'); ?></p>

                <?php
                $attrib = array('role' => 'form', 'id' => 'products_form');
                echo admin_form_open_multipart("products/add", $attrib)
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
                        $opts = array( '' => 'Select Type', 'semi_finished' => lang('semi_finished'),'raw' => lang('raw'),'service' => lang('service'));
                        echo form_dropdown('type', $opts, set_value('type', ($product ? $product->type : '')), 'class="form-control" id="type" required="required"');
                        ?>
                        <label for="type" class="text-danger"> is Empty.</label>
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
                        <div class="input-group">
                            <?= form_input('code', (isset($_POST['code']) ? $_POST['code'] : ($product ? $product->code : '')), 'class="form-control" id="code"  required="required"') ?>
                            <span class="input-group-addon pointer" id="random_num" style="padding: 1px 10px;">
                                <i class="fa fa-random"></i>
                            </span>
                        </div>
                        <span class="help-block"><?= lang('you_scan_your_barcode_too') ?></span>
                        <label for="code" class="text-danger"></label>
                    </div>
                    
                    <div class="form-group all" style="display:none ">
                        <?= lang("barcode_symbology", "barcode_symbology") ?>
                        <?php
                        $bs = array('code25' => 'Code25', 'code39' => 'Code39', 'code128' => 'Code128', 'ean8' => 'EAN8', 'ean13' => 'EAN13', 'upca' => 'UPC-A', 'upce' => 'UPC-E');
                        echo form_dropdown('barcode_symbology', $bs, (isset($_POST['barcode_symbology']) ? $_POST['barcode_symbology'] : ($product ? $product->barcode_symbology : 'code128')), 'class="form-control select" id="barcode_symbology" required="required" style="width:100%;"');
                        ?>
                    </div>

                    <div class="form-group all">
                      <input type="hidden" name="slug" value="" id="slug">
                       
                    </div>
					
                    
                    <div class="form-group all">
                        <?= lang("minimum_quantity", "minimum_quantity") ?>
                        <?= form_input('minimum_quantity', (isset($_POST['minimum_quantity']) ? $_POST['minimum_quantity'] : ($product ? $product->minimum_quantity : '')), 'class="form-control numberonly" id="minimum_quantity" maxlength="3" '); ?>
                    </div>
                    
                    <div class="form-group all">
                        <?= lang("reorder_quantity", "reorder_quantity") ?>
                        <?= form_input('reorder_quantity', (isset($_POST['reorder_quantity']) ? $_POST['reorder_quantity'] : ($product ? $product->reorder_quantity : '')), 'class="form-control numberonly" id="reorder_quantity" maxlength="3"  '); ?>
                    </div>
                    
                    <div class="form-group all">
                        <?= lang("open_stock_quantity", "open_stock_quantity") ?>
                        <?= form_input('open_stock_quantity', (isset($_POST['open_stock_quantity']) ? $_POST['open_stock_quantity'] : ($product ? $product->open_stock_quantity : '')), 'class="form-control numberonly" id="open_stock_quantity" maxlength="3" '); ?>
                    </div>
                    
                 
                    
                    <div class="form-group">
                        <?= lang("type_expiry", "type_expiry") ?>
                        <?php
                        $type_e = array('' => 'Select Type', 'day' => lang('Day'),'month' => lang('Month'),'year' => lang('Year'));
                        echo form_dropdown('type_expiry', $type_e, (isset($_POST['type_expiry']) ? $_POST['type_expiry'] : ($product ? $product->type_expiry : '')), 'class="form-control" id="type_expiry" ');
						
                        ?>
                        <label for="type_expiry" class="text-danger"> is Empty</label>
                    </div>
                    <div class="form-group all">
                        <?= lang("value_expiry", "value_expiry") ?>
                        <?= form_input('value_expiry', (isset($_POST['value_expiry']) ? $_POST['value_expiry'] : ($product ? $product->value_expiry : '')), 'class="form-control numberonly" id="value_expiry" maxlength="3"'); ?>
                    </div>
                    
                    <div class="form-group">
                       
                     
                      <?= lang("brand", "brand") ?>
                       <div class="input-group">
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
                    
                    <div class="form-group">
                       
                      <?= lang("group", "category") ?>
                       <div class="input-group">
                              <?php
                        $cat[''] = "";
                        foreach ($categories as $category) {
                            $cat[$category->id] = $category->name;
                        }
                        echo form_dropdown('category', $cat, (isset($_POST['category']) ? $_POST['category'] : ($product ? $product->category_id : '')), 'class="form-control select select-category" id="category" placeholder="' . lang("select") . " " . lang("group") . '" required="required" style="width:100%"')
                        ?>
                                  
                           
                           
                            <div class="input-group-addon no-print" style="padding: 2px 5px;">
                                <a href="<?= admin_url('system_settings/add_category'); ?>" id="add-supplier1" class="external" data-toggle="modal" data-target="#myModal">
                                    <i class="fa fa-2x fa-plus-square" id="addIcon1"></i>
                                </a>
                            </div>
                            
                      
                       </div>
                         <label for="category" class="text-danger"> is Empty</label>
                    </div>
                    
                   
                    <div class="form-group all">
                        <?= lang("sub_group", "subcategory") ?>
                        <div class="controls" id="subcat_data"> <?php
                            echo form_input('subcategory', ($product ? $product->subcategory_id : ''), 'class="form-control" id="subcategory"  placeholder="' . lang("select_sub_group") . '"');
                            ?>
			     <input type="hidden" id="subcategory-hidden">
                        </div>
                    </div>
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
                    
                    <div class="form-group ">
                        <?= lang('default_sale_unit', 'default_sale_unit'); ?>
                        <?php $uopts[''] = lang('select_unit_first'); ?>
                        <?= form_dropdown('default_sale_unit', $uopts, ($product ? $product->sale_unit : ''), 'class="form-control" id="default_sale_unit" style="width:100%;"'); ?>
                    </div>
                    <div class="form-group ">
                        <?= lang('default_purchase_unit', 'default_purchase_unit'); ?>
                        <?= form_dropdown('default_purchase_unit', $uopts, ($product ? $product->purchase_unit : ''), 'class="form-control" id="default_purchase_unit" style="width:100%;"'); ?>
                    </div>      
 
                   <div class="form-group all">
                    <!--<input type="text" id="product_cost" class="form-control tip" required="required" placeholder="<?php echo $this->Settings->symbol;?>"  >
  
   -->
                    <?php echo $this->Settings->symbol;?>
                        <?= lang('product_cost', 'product_cost'); ?> 
                        
                        <?= form_input('product_cost', set_value('product_cost'), 'class="form-control tip numberonly" maxlength="15" id="product_cost"'); ?> 
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
                	<div class="form-group semi_finished">
                        <div class="form-group">
                            <?= lang("Purchase Items", "Purchase Items") ?>
                            <button type="button" class="btn btn-primary btn-xs" id="addRaw_product"><i class="fa fa-plus"></i>
                            </button>
                        </div>
                        <div class="row" id="supplier-con">
                            <div class="col-xs-12">
                                <div class="form-group">
                                    <?php
                                    echo form_input('raw_product[]', set_value('raw_product'), 'class="form-control rraw_product" id="raw_product_1"  placeholder="' . lang("select") . ' ' . lang("Purchase Items") . '" style="width:100%;" ');
                                    ?>
                                </div>
                            </div>
                            <div class="col-xs-4">
                                <div class="form-group">
                                    <?= form_input('raw_min_quantity[]', set_value('raw_min_quantity'), 'class="form-control tip numberonly rraw_min_quantity"  maxlength="3"  id="raw_min_quantity_1" placeholder="' . lang('min_quantity') . '"'); ?>
                                </div>
                            </div>
                            <div class="col-xs-4">
                                <div class="form-group max_change">
                                    <?= form_input('raw_max_quantity[]', set_value('raw_max_quantity'), 'class="form-control tip numberonly rraw_max_quantity" maxlength="3"   id="raw_max_quantity_1" placeholder="' . lang('max_quantity') . '"'); ?>
                                    <input type="hidden" name="raw_price[]" id="raw_price_1" class="rraw_price" value="">
                                </div>
                            </div>
                           
                            <div class="col-xs-4">
                                <div class="form-group">
                                   <?= form_input('raw_units[]', set_value('raw_units'), 'class="form-control tip rraw_units" readonly  id="raw_units_1" placeholder="' . lang('units') . '"'); ?>
                                   
                                   <input type="hidden" name="raw_units_id[]" class="rraw_units_id" id="raw_units_id_1" >
                                   
                                    
                                </div>
                            </div>
                        </div>
                        <div id="ex-raw_product"></div>
                    </div>
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
                        <?php echo form_submit('add_product', $this->lang->line("add_product"), 'class="btn btn-primary"'); ?>
                    </div>

                </div>
                <?= form_close(); ?>

            </div>

        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
       
        
        $('#type').change(function () {
            var t = $(this).val();
            if (t !== 'semi_finished') {
                $('.semi_finished').slideUp();
                //$('#unit').attr('disabled', true);
               // $('#cost').attr('disabled', true);
                $('#track_quantity').iCheck('uncheck');
            } else {
                $('.semi_finished').slideDown();
                $('#track_quantity').iCheck('check');
               // $('#unit').attr('disabled', false);
                //$('#cost').attr('disabled', false);
            }
            if (t !== 'digital') {
                $('.digital').slideUp();
                $('#file_link').removeAttr('required');
               // $('form[data-toggle="validator"]').bootstrapValidator('removeField', 'file_link');
            } else {
                $('.digital').slideDown();
                $('#file_link').attr('required', 'required');
               // $('form[data-toggle="validator"]').bootstrapValidator('addField', 'file_link');
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

        var t = $('#type').val();
        if (t !== 'semi_finished') {
            $('.semi_finished').slideUp();
            //$('#unit').attr('disabled', true);
           // $('#cost').attr('disabled', true);
            $('#track_quantity').iCheck('uncheck');
        } else {
            $('.semi_finished').slideDown();
            $('#track_quantity').iCheck('check');
            //$('#unit').attr('disabled', false);
           // $('#cost').attr('disabled', false);
        }
        if (t !== 'digital') {
            $('.digital').slideUp();
            $('#file_link').removeAttr('required');
           // $('form[data-toggle="validator"]').bootstrapValidator('removeField', 'file_link');
        } else {
            $('.digital').slideDown();
            $('#file_link').attr('required', 'required');
           // $('form[data-toggle="validator"]').bootstrapValidator('addField', 'file_link');
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

       
		var su = 2;
		
        $('#addRaw_product').click(function () {
            if (su <= 25) {
               
                var html = '<div style="clear:both;height:5px;"></div><div class="row product_box"><div class="col-xs-12"><div class="form-group"><input type="hidden" name="raw_product[]", class="form-control rraw_product" id="raw_product_' + su + '" placeholder="<?= lang("select") . ' ' . lang("product") ?>" style="width:100%;display: block !important;" /></div></div><div class="col-xs-4"><div class="form-group"><input type="text" name="raw_min_quantity[]"  id="raw_min_quantity_' + su + '" class="form-control tip numberonly rraw_min_quantity_" maxlength="3"   placeholder="<?= lang('min_quantity') ?>" /></div></div><div class="col-xs-4"><div class="form-group max_change"><input type="text" name="raw_max_quantity[]"  id="raw_max_quantity_' + su + '" class="form-control rraw_max_quantity tip numberonly"  maxlength="3"  placeholder="<?= lang('max_quantity') ?>" /><input type="hidden" name="raw_price[]" id="raw_price_' + su + '" class="rraw_price" value=""></div></div><div class="col-xs-4"><div class="form-group"><input type="text" name="raw_units[]"   id="raw_units_' + su + '" class="form-control tip raw_units" readonly   placeholder="<?= lang('units') ?>" /><input type="hidden" name="raw_units_id[]"  id="raw_units_id_' + su + '" class="form-control tip raw_units_id"   placeholder="<?= lang('units') ?>" /></div><button type="button" class="btn btn-primary btn-xs deleteRaw_product"><i class="fa fa-trash-o"></i></button></div>';
				
				 html +='</div>';
				 
                $('#ex-raw_product').append(html);
                var sup = $('#raw_product_' + su);
                raw_products(sup);
                su++;
				
				
				
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
		 
		   $.ajax({
			url : '<?php echo admin_url('products/raw_units'); ?>', 
			type : 'GET',
			data : 'term='+id,
			dataType : "json",
			success : function(data) {
				$('#raw_price_'+name).val(data.results[0].cost);
				$('#raw_units_'+name).val(data.results[0].name);
				$('#raw_units_id_'+name).val(data.results[0].id);
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
     
    });

   
    $(document).ready(function() {
        $('#unit').change(function(e) {
            var v = $(this).val();
            if (v) {
                $.ajax({
                    type: "get",
                    async: false,
                    url: "<?= admin_url('products/getSubUnits') ?>/" + v,
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







<script type="text/javascript">

function calculate_price() {
			
		var rows = $('.max_change');
		var pp = 0;
		$.each(rows, function () {
			pp += formatDecimal(parseFloat($(this).find('.rraw_price').val()) * parseFloat($(this).find('.rraw_max_quantity').val()));
			
		});
		$('#product_cost').val(pp);
		return true;
	}

	$(document).on('change', '.rraw_max_quantity', function () {
		calculate_price();
	});
	
	$(document).on('change', '#type', function(){
		$('#product_cost').val('');
	});

	$("#products_form").validate({
		ignore: [],
        
	});
	
</script>

