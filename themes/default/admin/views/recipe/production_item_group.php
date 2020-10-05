<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
          
            <h4 class="modal-title" id="myModalLabel"><?= lang('Production_item_groups'); ?></h4>
			  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                <i class="fa fa-2x">&times;</i>
            </button>
        </div>
        <div class="modal-body">
		<div class="row">
	<div class="clearfix"></div>
                         <div class="col-md-12" id="sticker">
                            <div class="well well-sm">
                            
                                <div class="form-group" style="margin-bottom:0;">
                                    <div class="input-group wide-tip">
                                        <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;">
                                            <i class="fa fa-2x fa-barcode addIcon"></i></div>
                                        <?php // echo form_input('add_item', '', 'class="form-control input-lg" id="add_item" placeholder="' . $this->lang->line("Search Item Name") . '"'); ?>
										<?php echo form_input('recipe_product[]', set_value('recipe_product'), 'class="search-purchase-items form-control" id="recipe_product_1"  placeholder="' . lang("select") . ' ' . lang("ingredients") . '" style="width:100%;" '); ?>
                                 
                                      
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
					<?php	  echo admin_form_open("recipe/proudctionItemGroups/". $parentItemid."/".$production_id, $attrib);  ?>
					  <input type="hidden" name="recipe_id" value="<?php echo $product_recipe->recipe_id;    ?>">
							  <input type="hidden" name="variant_id" value="<?php echo $product_recipe->variant_id;   ?>">
							  <input type="hidden" name="production_id" value="<?php echo $production_id;   ?>">
							    <input type="hidden" name="parentItemId" value="<?php  echo $parentItemid;  ?>">
								 <input type="hidden" class="parentItemCatgoryId" name="parentItemCatgoryId" value="<?php  echo $product_recipe->category_id;  ?>">
								  <input type="hidden" class="parentItemSubCatgeoryId" name="parentItemSubCatgeoryId" value="<?php  echo $product_recipe->sub_category_id;  ?>">
          		<div class="col-md-12">
                            <div class="control-group table-group">
                                <label class="table-label"><?= lang("items"); ?></label>
                                <div class="controls table-controls">
                                  <table id="prTable" class="table items table-striped table-bordered table-condensed table-hover purchase-item"> <!-- id="productionTable" -->
                                        <thead>
                                        <tr>
                                           
                                            <th class="col-md-2"><?= lang('item') . lang('name'); ?></th>
											<th class="col-md-2"><?= lang("quantity"); ?></th>
											<th class="col-md-2"><?= lang("UOM"); ?></th>
                                            <th class="col-md-1" style="text-align: center;"><i
                                                   class="fa fa-trash-o"
                                                    style="opacity:0.5; filter:alpha(opacity=50);"></i></th>
                                        </tr>
                                        </thead>
                                       <tbody>
						<?php  $existingPIDS = array();							
						?>
						<?php foreach($groupItem as $k => $row) :
						array_push($existingPIDS,$row->product_id.'-'.$row->cm_id);
							if($row->r_item_customizable == 0 ){ 
							    $disabled ='disabled="disabled"';
					        }else{
					            $disabled ='';
					        }
					        if($row->item_customizable ==1){
					        	$check =1;
					        }else{
					        	$check = 0;
					        }
						?>
						    <tr class="each-purchase-item">
						    				    
							<td  class="col-sm-8">
								<?php $row->category_name; if($row->category_name != ''){
									$cat = ' Cat-'.$row->category_name;
								}else{
									$cat = '';
								} 								
								if($row->subcategory_name != ''){
									$sub_cat = ' Sub-'.$row->subcategory_name;
								}else{
									$sub_cat = '';
								}
								if($row->brand_name != ''){
									$brand = ' Brand-'.$row->brand_name;
								}else{
									$brand = '';
								} 								
								?>
							    <input class="form-control" type="text" value="<?=$row->recipe_name.$cat.' | '.$sub_cat.' | '.$brand ?>" disabled> 
							    <input class="form-control" type="hidden" name="purchase_item_id[]" value="<?=$row->product_id?>">
							    <input class="form-control" type="hidden" name="purchase_item_cm_id[]" value="<?=$row->cm_id?>">
							</td>
							<td  class="col-sm-1"><input class="p-item-quantity form-control numberonly" type="text" name="purchase_item_quantity[]" value="<?=$row->quantity?>" maxlength="15" ><input class="form-control" type="hidden" id="category_id[]" name="category_id[]" value="<?=$row->category_id?>"><input class="form-control" type="hidden" id="subcategory_id[]" name="subcategory_id[]" value="<?=$row->sub_category_id?>"><input class="form-control" type="hidden" id="brand_id[]" name="brand_id[]" value="<?=$row->brand_id?>"></td>
							
							<td>
							
								<select name="purchase_item_unit[]" id="purchase_item_unit" data-index="1" class="form-control purchase_item_unit">
                                    <?= $this->site->unit_of_measurement($row->product_id,$row->unit_id); ?>                                   
                                </select>
							</td>
       						<td class="col-sm-1 text-center"><a href="" data-id="<?=$row->id?>" data-vid="<?=$row->product_id?>" class="btn btn-primary btn-xs remove-purchase-item"><i class="fa fa-trash-o"></i></a></td>
						    </tr>
						<?php endforeach; ?>
						</tbody>
                        </table>    

                    </div>         
	<?php echo form_submit('add_production', $this->lang->line("update"), 'id="add_production" style="float:left ! important;width:72px;;" class="btn col-lg-1 btn-sm btn-primary pull-right" '); ?>					
                </div>
            </div> 
			<?php  echo form_close(); ?> 
        </div>
    </div>
</div>



<script type="text/javascript">
	var $existing_purchase_items =[];
	var $existing_purchase_items_ids = [];
 $(document).ready(function () {
	 <?php if(!empty($existingPIDS)) : ?>
        var $existing_purchase_items_ids = <?=json_encode($existingPIDS)?>;
        <?php endif; ?>
    	$.each($existing_purchase_items_ids,function(n,v){
            $existing_purchase_items.push(v);
        });
    $(document).on('ifChecked', '.Pitem_customizable', function() {
        if(this.checked) {           
            $(this).parent().parent().find('.Pcustomizable').val(1);             
        }
    });
    $(document).on('ifUnchecked', '.Pitem_customizable', function() {        
          $(this).parent().parent().find('.Pcustomizable').val(0);
    });

	$("#production_form").validate({
		ignore: [],
	});
	


 $('.search-purchase-items').select2({
       minimumInputLength: 1,
       ajax: {
        url: site.base_url+"recipe/search_purchase_itemsNew",
        dataType: 'json',
        type:'post',
        quietMillis: 15,
        data: function (term, page) {
            return {
                term: term,
                existing:$existing_purchase_items,
                item_type:$('#type').val(),
				category_id:$('.parentItemCatgoryId').val(),
				subcategory_id:$('.parentItemSubCatgeoryId').val()
            };
        },
        results: function (data, page) {
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
			$results[n]['item_customizable'] = v.item_customizable;
			  $results[n]['brandid'] = v.brandid;
			   $results[n]['cat_id'] = v.cat_id;
			    $results[n]['sub_id'] = v.sub_id;
        })
       // console.log($results)
                return {results: $results};
            } else {
                return { results: [{id: '', text: 'No Match Found'}]};
            }
        },
	
    },
    formatResult: function (i) {return '<div>'+i.html+'</div>';},
    formatSelection: function (i) {return '<div>'+i.text+'</div>'; },
  }).on('change', function (v) {
	  console.log(v.added);
    $lable = v.added.text;
    $pid = v.added.id;
    $cm_id = v.added.cm_id;
    // $uniqid = $pid+'-'+$cm_id;
    $uniqid = $cm_id;
    $unit = v.added.unit;
    $cost = v.added.cost;
    $unitid = v.added.unit_id;
    $item_customizable = v.added.item_customizable;    
    $('.search-purchase-items').select2("val", "");
    $newarr =[];
    $existing_purchase_items.push($uniqid);
      var Uom = v.added.units;       
	  console.log(v);
    	var htm = "";
		htm+= "<select name='purchase_item_unit[]' class='select2-container form-control'><option value='' >Select </option>";
		//$.each(Uom, function (a, b) {
		htm+= "<option value="+Uom.id +">"+Uom.name+"</option>";
	//	});
		htm+= "</select>";	
		if($item_customizable == 0){
            $disabled ='disabled="disabled"';
        }else{
            $disabled ='';
        }
        $html = '<tr class="each-purchase-item">';
        //$html +='<td  class="col-sm-1"><input class="p-item-quantity form-control Pitem_customizable" '+$disabled+'  type="checkbox" id="Pitem_customizable" value="1" ><input class="form-control Pcustomizable" type="hidden"  name="Pitem_customizable[]"></td>';

        $html +='<td  class="col-sm-4"><input class="form-control" type="text" value="'+$lable+'" disabled>';
        $html +='<input class="form-control" type="hidden" id="purchase_item_id" name="purchase_item_id[]" value="'+$pid+'"><input class="form-control" type="hidden" id="item_cm_id[]" name="purchase_item_cm_id[]" value="'+$cm_id+'"></td>';
        $html +='<td  class="col-sm-2"><input class="p-item-quantity form-control numberonly" maxlength="15" type="text" id="purchase_item_quantity" name="purchase_item_quantity[]" value=""><input class="form-control" type="hidden" id="category_id[]" name="category_id[]" value="'+v.added.cat_id+'"><input class="form-control" type="hidden" id="subcategory_id[]" name="subcategory_id[]" value="'+v.added.sub_id+'"><input class="form-control" type="hidden" id="brand_id[]" name="brand_id[]" value="'+v.added.brandid+'"></td>';
		// $html +='<td  class="col-sm-2"><input class="form-control" type="hidden" id="purchase_item_unit" name="purchase_item_unit[]" value="'+$unitid+'"><input readonly="readonly" class="p-item-unit form-control" type="test" name="purchase_item[unit][]" value="'+$unit+'"></td>';

		$html +='<td  class="col-sm-2">'+htm+'</td>';

        $html +='<td  class="col-sm-2 text-center"><a href="" data-id="" data-vid="'+$pid+'" class="btn btn-primary btn-xs remove_item"><i class="fa fa-trash-o"></i></a></td>'
        $html +='</tr>';
		
        $('.purchase-item tbody').append($html);
        $('.search-purchase-items').val();
		
		 
    });
	 $('.purchase-item tbody tr:first input[type="checkbox"]').iCheck({
        checkboxClass: 'icheckbox_square-blue',
        radioClass: 'iradio_square-blue',
        increaseArea: '20%'
        });
    $(document).on('click','.remove_item',function(e){
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
            }
        }
        })
    }else{
        $obj.closest('tr').remove();
        alert('Removed');
    }
    var index = $existing_purchase_items.indexOf($uniqid);
        if (index > -1) {
          $existing_purchase_items.splice(index, 1);
        }
    });

	});
 

$(document).on("keypress", ".numberonly", function (event) {
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


