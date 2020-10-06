<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
	$attrib = array( 'role' => 'form', 'id' => 'production_form');
	echo admin_form_open("recipe/ingredients_mapping", $attrib);
?>
<style type="text/css">
    .errorClass
    {
    border-color:#FF0000;
    border: 2px solid red !important;
    } 

</style>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?php echo  lang('item_wise_ingredients_mapping'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
				<div class="row">
				<div class="col-lg-12" style="background:#b1d7fd; padding:15px 15px;"> 

				<?php echo form_submit('add_production', $this->lang->line("update"), 'id="add_production" class="btn col-lg-1 btn-sm btn-primary pull-right" '); ?>
                        <button type="button" class="btn btn-sm btn-danger col-lg-1 pull-right" id="reset" style="display: none;margin-right:15px;height:30px!important;font-size: 12px!important"><?= lang('reset'); ?></button>
					<table class="table custom_tables">
						<tbody>
						<tr> 
							<td width="100px">
								<?= lang("item_type", "type") ?>
							</td>
							<td width="350px">
								<?php
								$opts = array('0' => lang('select'),'standard' => lang('standard'), 'production' => lang('production'),'quick_service' => lang('quick_service'), 'semi_finished' => lang('semi_finished'), 'addon' => lang('addon'));
								echo form_dropdown('type', $opts, (isset($_POST['type']) ? $_POST['type'] : ($recipe ? $recipe->type : '')), 'class="form-control" id="type" required="required"');
								?>
							</td>
							<td width="100px">
								<?= lang("item_name", "item_name"); ?>
							</td>
							<td width="350px">
								<select name="item_name" id="item_name" class="form-control ttip" >
									<option value="0">Select</option>
								</select>
							</td>
						
						</tr>
						<tr>
							<td width="100px">
								<?= lang("qty", "qty"); ?>
							</td>
							<td width="350px">
								<?php echo form_input('qty', '', 'class="form-control numberonly ttip" id="qty" data-placement="top" data-trigger="focus" data-bv-notEmpty-message="' . lang('please_add_items_below') . '" style="width:100%;"  '); ?>
							</td>
							<td width="100px">
								<?= lang("UOM", "UOM"); ?>
							</td>
							<td width="350px">
								<select name="item_uom" id="item_uom" class="form-control ttip" >
									<option value="0">Select</option>
								</select>
							</td>
							<td style="display: none">
								<?= lang("selling_price", "selling_price") ?> 
							</td> 
							<td style="display: none">
								<input type="text" name="selling_price" class="form-control tip numberonly" id="selling_price" maxlength="10" >
                                <input type="hidden" name="variant_id" class="form-control tip" id="variant_id">
                                <input type="hidden" name="recipe_id" class="form-control tip" id="recipe_id">
							</td>
						</tr>	
						<tr style="display: none">
							<td>
								<?= lang("cost_price", "cost_price") ?> 
							</td> 
							<td>					
								<input type="text" name="cost_price" id="cost_price" class="form-control tip numberonly" maxlength="15"  >
							</td> 
						</tr>
					</tbody>
				</table>
				</div>	
		</div>
		<br /><br />
				<div class="clearfix"></div>
                         <div class="col-md-12" id="sticker">
                            <div class="well well-sm">
                              
                                <div class="form-group" style="margin-bottom:0;">
                                    <div class="input-group wide-tip">
                                        <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;">
                                            <i class="fa fa-2x fa-barcode addIcon"></i></div>
                                      
										<?php
											echo form_input('recipe_product[]', set_value('recipe_product'), 'class="search-purchase-items-new form-control" id="recipe_product_1"  placeholder="' . lang("select") . ' ' . lang("ingredients") . '" style="width:100%;" ');
										?>
                                        <?php if ($Owner || $Admin || $GP['products-add']) { ?>
                                        <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;">
                                            <a href="<?= admin_url('recipe/list_ingredients') ?>" id="addManually1"><i
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
									<table id="prTable" class="table items table-striped table-bordered table-condensed table-hover purchase-item-container"> 
                                        <thead>
                                        <tr>
                                           <th class="col-md-1"><?= lang("customizable"); ?></th>
                                            <th class="col-md-2"><?= lang('item') . lang('name'); ?></th>
											<th class="col-md-2"><?= lang("quantity"); ?></th>
											<th class="col-md-2"><?= lang("UOM"); ?></th>       
											<th class="col-md-1" style="text-align: center;"><i
                                                    class="fa fa-trash-o"
                                                    style="opacity:0.5; filter:alpha(opacity=50);"></i></th>
                                        </tr>
                                        </thead>
                                        <tbody></tbody>
                                       
									</table>    

                                </div>                                
                            </div>
                        </div> 
					</div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
	var $existing_purchase_items =[];
 $(document).ready(function () {
	$("#production_form").validate({
		ignore: [],
	});
	$('#type').change(function () {
        // $("#item_name").val(null).trigger("change"); 
        $("#item_name").select2("val", "");
        $('.purchase-item-container tbody').empty();
        $('#selling_price').val('');
        $('#cost_price').val('');
        $('#variant_id').val('');
        $('#qty').val(''); 
		var type = $(this).val();
		
		$.ajax({
         type: "POST",
         url: "<?= admin_url('recipe/getrecipeItemName') ?>",
         data: {type: type},
         dataType: "json",  
         cache:false,
         success: 
		 
             function(response){
				// alert(response);
				 if(response < 1 && response == null){
					 var len = 0;
				 }else{
					 var len = response.length;
				 }
				 
				if(len != 0 || len != null || len != ''){
					$("#item_name").empty();
                    $("#item_name").append("<option value='0'>Select</option>");
					for( var i = 0; i<len; i++){
						var id = response[i]['id'];
						var name = response[i]['name'];
                        var varient_name = response[i]['varient_name'];
                        var variant_id = response[i]['attr_id'];
						var price = 0;
						if(!varient_name){
							varient_name = '';
                            price =response[i]['recipe_price'];                            
						}else{
							varient_name = ' - '+varient_name;
                            price = response[i]['variant_price'];                            
						}

						$("#item_name").append("<option price='"+price+"' variant_id='"+variant_id+"'  recipe_id= '"+id+"'  value='"+id+"'>"+name+varient_name+"</option>");
					// $("#item_name").html(name);
					}
				}else{
					alert("No Result Found");
				}
			}	
		});
	});
	
	$('#item_name').change(function () {
    
		var item_id = $(this).val();
		
		$.ajax({
         type: "POST",
         url: "<?= admin_url('recipe/getrecipeItemUOM') ?>",
         data: {item_id: item_id},
         dataType: "json",  
         cache:false,
         success: 
		 
             function(response){
				// alert(response);
				 if(response < 1 && response == null){
					 var len = 0;
				 }else{
					 var len = response.length;
				 }
				 
				if(len != 0 || len != null || len != ''){
					$("#item_uom").empty();
                    $("#item_uom").append("<option value='0'>Select</option>");
					for( var i = 0; i<len; i++){
						var id = response[i]['id'];
						var name = response[i]['name'];
						$("#item_uom").append("<option  value='"+id+"'>"+name+"</option>");					}
				}else{
					alert("No Result Found");
				}
			}	
		});
	});
	
	
	
	
 $('.search-purchase-items-new').select2({
       minimumInputLength: 1,
       ajax: {
        url: site.base_url+"recipe/search_purchase_items_new",
        dataType: 'json',
        type:'post',
        quietMillis: 15,
        data: function (term, page) {
            return {
                term: term,
                existing:$existing_purchase_items,
                item_type:$('#type').val()
            };
        },
        results: function (data, page) {
        console.log(data)
            if(data != null) {
        $results = []
        $.each(data,function(n,v){
             $results[n] = {};
             $results[n]['id'] = v.id
			 $results[n]['cm_id'] = v.cm_id
			if(v.category_name != ''){
				$results[n]['cate_name'] = v.category_name
				var cat_label = ' <strong>Cat</strong>';
				var cat = ' - '+v.category_name;
			 }else{
				var cat = '';
			 }
			 
			 if(v.subcategory_name != ''){
				$results[n]['sub_cat_name'] = v.subcategory_name
				var sub_label = ' | <strong>Sub</strong>';
				var sub = ' - '+v.subcategory_name;
			 }else{
				var sub = '';
			 }
			 
			 if(v.brand_name != ''){
				$results[n]['brand_name'] = v.brand_name
				var brand_label = ' | <strong>Brand</strong>';
				var brand = ' - '+v.brand_name;
			 }else{
				var brand = '';
			 }
             $results[n]['html'] = v.name+cat_label+cat+sub_label+sub+brand_label+brand;
			 $results[n]['text'] = v.name+' Cat '+cat+' | Sub '+sub+' | Brand'+brand;
             $results[n]['unit'] = v.unit_name;
             $results[n]['cost'] = v.cost;
             $results[n]['unit_id'] = v.unit;
			 $results[n]['units'] = v.units;
			 $results[n]['brandid'] = v.brandid;
			 $results[n]['cat_id'] = v.cat_id;
			 $results[n]['sub_id'] = v.sub_id;
			 $results[n]['variant_id'] = v.variant_id;
			 $results[n]['item_customizable'] = v.item_customizable;
        })
                return {results: $results};
            } else {
                return { results: [{id: '', text: 'No Match Found'}]};
            }
        },
	
    },
    formatResult: function (i) {return '<div>'+i.html+'</div>';},
    formatSelection: function (i) {return '<div>'+i.text+'</div>'; },
  }).on('change', function (v) {
	  console.log(v)
    $lable = v.added.text;
    $pid = v.added.id;
    $cm_id = v.added.cm_id;
    // $uniqid = $pid+'-'+$cm_id;
    $uniqid = $cm_id;
    $unit = v.added.unit;
    $cost = v.added.cost;
    $unitid = v.added.unit_id;
    $item_customizable = v.added.item_customizable;    
    $('.search-purchase-items-new').select2("val", "");
    $existing_purchase_items.push($uniqid);
    var Uom = v.added.units;       
        var htm = "";
        htm+= "<select name='purchase_item_unit[]' class='select2-container form-control purchase_item_unit'><option value='' >Select </option>";
       // $.each(Uom, function (a, b) {
        htm+= "<option value="+Uom.id +">"+Uom.name+"</option>";                                                    
        //});
        htm+= "</select>";  
        if($item_customizable == 0){
            $disabled ='disabled="disabled"';
        }else{
            $disabled ='';
        }
        $html = '<tr class="each-purchase-item">';
        $html +='<td  class="col-sm-1"><input class="p-item-quantity form-control item_customizable" '+$disabled+'  type="checkbox" id="item_customizable" ><input class="form-control customizable" type="hidden"  name="item_customizable[]"></td>';
        $html +='<td  class="col-sm-8"><input class="form-control" type="text" value="'+$lable+'" disabled>';      
        $html +='<input class="form-control" type="hidden" id="purchase_item_id" name="purchase_item_id[]" value="'+$pid+'"><input class="form-control" type="hidden" id="item_cm_id[]" name="purchase_item_cm_id[]" value="'+$cm_id+'"><input class="form-control" type="hidden" id="category_id[]" name="category_id[]" value="'+v.added.cat_id+'"><input class="form-control" type="hidden" id="subcategory_id[]" name="subcategory_id[]" value="'+v.added.sub_id+'"><input class="form-control" type="hidden" id="brand_id[]" name="brand_id[]" value="'+v.added.brandid+'"></td>';
        $html +='<td  class="col-sm-1"><input class="p-item-quantity form-control purchase_item_quantity numberonly piq'+$uniqid+'" type="text" id="purchase_item_quantity" name="purchase_item_quantity[]" value="" maxlength="5" autofocus="autofocus" ><input class="form-control" type="hidden" id="variant_id" name="item_variant_id[]" value="'+v.added.variant_id+'"></td>';        
	// $html +='<td  class="col-sm-1"><input class="form-control" type="hidden" id="purchase_item_unit" name="purchase_item_unit[]" value="'+$unitid+'"><input readonly="readonly" class="p-item-unit form-control" type="test" name="purchase_item[unit][]" value="'+$unit+'"></td>';
        //$html +='<td  class="col-sm-2"><input class="p-item-cost form-control" type="hidden" name="purchase_item[cost][]" value="'+$cost+'"><input class="p-item-price form-control" type="text" name="purchase_item[price][]" value=""></td>';
        $html +='<td  class="col-sm-2">'+htm+'</td>';
        $html +='<td  class="col-sm-1 text-center"><a href="" data-id="" data-vid="'+$pid+'" class="btn btn-primary btn-xs remove-purchase-item_new"><i class="fa fa-trash-o"></i></a></td>'
        $html +='</tr>';
		
        $('.purchase-item-container tbody').append($html);
		$('.piq'+$uniqid).focus();
		//$(this).siblings('.purchase_item_quantity').focus();
        
        $('.search-purchase-items-new').val();		 
    });
 $(' input[type="checkbox"]').iCheck({
        checkboxClass: 'icheckbox_square-blue',
        radioClass: 'iradio_square-blue',
        increaseArea: '20%'
        });
    $(document).on('ifChecked', '.item_customizable', function() {
        if(this.checked) {           
            $(this).parent().parent().find('.customizable').val(1);             
        }
    });
    $(document).on('ifUnchecked', '.item_customizable', function() {        
          $(this).parent().parent().find('.customizable').val(0);
    });

    $(document).on('click','.remove-purchase-item_new',function(e){
    e.preventDefault();
    $obj = $(this);
    $id = $obj.attr('data-id');
    if ($id!='') {
        $.ajax({
        type: "get", async: false,
        url: site.base_url+'recipe/delete_purchase_item/'+$id,
        dataType: "json",
        success: function (data) {
            if (data.status=="success") {
            $obj.closest('tr').remove();
                alert('Removed');           
            
            }
            
        }
        })
         }else{
        $obj.closest('tr').remove();
        alert('Removed');
        var arr = $existing_purchase_items;       
        var itemtoRemove = $id;
        arr.splice($.inArray(itemtoRemove, arr), 1);
        $existing_purchase_items =arr;

    }
   
    });

	});
    $(document).on('change', '#item_name', function(e) {
       $('#selling_price').val('');
       $('#cost_price').val('');
       $('#variant_id').val('');
       $('#recipe_id').val('');
       $('#qty').val('');       
       $value = $('#item_name option:selected').attr('price');
       $variant_id = $('#item_name option:selected').attr('variant_id');       
       $recipe_id = $('#item_name option:selected').attr('recipe_id');       
       $('#selling_price').val(formatDecimal($value));
       $('#variant_id').val($variant_id);
       $('#recipe_id').val($recipe_id);
       $('#cost_price').val(formatDecimal($value));
       $('#qty').val(1);              
    });


$('#add_production').on('click', function() { 
    $errorcnt =0;
    $('.errorClass').removeClass('errorClass');
    $('.purchase_item_unit').each(function(){
      if($(this).val() ==''){
        $errorcnt++;
        $(this).parent().parent().find('.purchase_item_unit').addClass('errorClass');
      }     
   });

    $('.purchase_item_quantity').each(function(){
      if($(this).val() ==''){
        $errorcnt++;
        $(this).parent().parent().find('.purchase_item_quantity').addClass('errorClass');
      }     
   });
       if($errorcnt > 0){
             return false;
       }else{
             $('#add_production').submit();
       }
});


$(document).on("keypress", ".numberonly", function (event) {
  /*  if( ! ( event.which >= 48 && event.which <= 57 ) )
        event.preventDefault();*/
    
    if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
        event.preventDefault();
    }  
});

$(document).on("keypress", ".floatonly", function (event) {
    if(event.which < 46
    || event.which > 59) {
        event.preventDefault();
    } // prevent if not number/dot

    if(event.which == 46
    && $(this).val().indexOf('.') != -1) {
        event.preventDefault();
    } // prevent if already dot
});

</script>

<?php  echo form_close(); ?> 
