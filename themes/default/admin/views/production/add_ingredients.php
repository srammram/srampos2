<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>


<?php
	$attrib = array( 'role' => 'form', 'id' => 'production_form');
	echo admin_form_open("recipe/add_ingredients", $attrib)
?>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?php echo  lang('item_wise_ingredients_mapping'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
				<div class="row">
				<div class="col-lg-12" style="background:#b1d7fd; padding:15px 15px;"> 

				<!--   <p class="introtext"><?php echo lang('enter_info'); ?></p> -->
				<?php echo form_submit('add_production', $this->lang->line("update"), 'id="add_production" class="btn col-lg-1 btn-sm btn-primary pull-right" '); ?>
                        <button type="button" class="btn btn-sm btn-danger col-lg-1 pull-right" id="reset" style="display: none;margin-right:15px;height:30px!important;font-size: 12px!important"><?= lang('reset'); ?></button>
               
					<table class="table custom_tables">
						<tbody>
						<tr> 
						<td>
							<?= lang("item_type", "type") ?>
						</td>
						<td>
							<?php
				
							//$opts = array('standard' => lang('standard'), 'combo' => lang('combo'), 'trade' => lang('trade'), 'production' => lang('production'), 'addon' => lang('addon'));
							$opts = array('production' => lang('production'),'quick_service' => lang('quick_service'), 'semi_finished' => lang('semi_finished'));
							echo form_dropdown('type', $opts, (isset($_POST['type']) ? $_POST['type'] : ($recipe ? $recipe->type : '')), 'class="form-control" id="type" required="required"');
							
							?>
							
						</td>
						<td>
							<?= lang("item_name", "item_name"); ?>
						</td>
						<td>
							<?php // echo form_dropdown('item_name', '', 'class="form-control ttip" id="item_name" data-placement="top" data-trigger="focus" data-bv-notEmpty-message="' . lang('please_add_items_below') . '"'); ?>
							<select name="item_name" id="item_name" class="form-control">
								<option value="0">Select</option>
							</select>
						</td>
						<td>
							<?= lang("qty", "qty"); ?>
						</td>
						<td>
							<?php echo form_input('qty', '', 'class="form-control ttip" id="qty" data-placement="top" data-trigger="focus" data-bv-notEmpty-message="' . lang('please_add_items_below') . '"'); ?>
						</td>
					</tr>
					<tr>
						<td>
							<?= lang("selling_price", "selling_price") ?> 
						</td> 
						<td>
							<input type="text" name="selling_price" class="form-control tip numberonly" maxlength="15" >
						</td>
						<td>
							<?= lang("cost_price", "cost_price") ?> 
						</td> 
						<td>					
							<input type="text" name="cost_price" class="form-control tip numberonly" maxlength="15"  >
						</td> 
					</tr>
					</tbody>
				</table>
				</div>	
		
		
	
		
		</div>
				<div class="clearfix"></div>
                         <div class="col-md-12" id="sticker">
                            <div class="well well-sm">
                              
                                <div class="form-group" style="margin-bottom:0;">
                                    <div class="input-group wide-tip">
                                        <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;">
                                            <i class="fa fa-2x fa-barcode addIcon"></i></div>
                                        <?php // echo form_input('add_item', '', 'class="form-control input-lg" id="add_item" placeholder="' . $this->lang->line("Search Item Name") . '"'); ?>
										<?php
						echo form_input('recipe_product[]', set_value('recipe_product'), 'class="search-purchase-items-new form-control" id="recipe_product_1"  placeholder="' . lang("select") . ' ' . lang("products") . '" style="width:100%;" ');
						?>
                                        <?php if ($Owner || $Admin || $GP['products-add']) { ?>
                                        <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;">
                                            <a href="<?= admin_url('procurment/products/add') ?>" id="addManually1"><i
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
                                  <table id="prTable" class="table items table-striped table-bordered table-condensed table-hover purchase-item-container"> <!-- id="productionTable" -->
                                        <thead>
                                        <tr>
                                           <!-- <th class="col-md-1"><?= lang("S.NO"); ?></th> -->
                                            <th class="col-md-2"><?= lang('item') . lang('name'); ?></th>
											<th class="col-md-2"><?= lang("quantity"); ?></th>
											<th class="col-md-2"><?= lang("UOM"); ?></th>
                                            <th class="col-md-1" style="text-align: center;"><i
                                                    class="fa fa-trash-o"
                                                    style="opacity:0.5; filter:alpha(opacity=50);"></i></th>
                                        </tr>
                                        </thead>
                                        <tbody></tbody>
                                        <!-- <tfoot></tfoot> -->
                                    </table>    

                                </div>                                
                            </div>
                        </div> 

						<!--
						<div class="col-lg-12" style="background:#a6f7a1; margin-top:15px;">
                            <table class="table custom_tables"  >
                                <tbody>
                                    <tr>                                    
                                        <td width="150px">
                                            <?= lang("Total No Of Items", "total_items") ?>
                                        </td>
                                        <td width="150px">
                                            <input  name="total_no_items" id="total_no_items" readonly class="form-control">
                                        </td>
										<td width="150px">
                                            <?= lang("Total No Of Qty", "total_qty") ?>
                                        </td>
                                        <td width="150px">
                                            <input  name="total_no_qty" id="total_no_qty" readonly class="form-control">
                                        </td>
                                         <td width="100px">                                    
                                        </td>
                                        <td width="100px">                                    
                                        </td>                                    
                                        <td>
                                            <?= lang("Remarks/Note", "note") ?>
                                        </td>
                                        <td>
                                            <input  name="note" id="reqnote" class="form-control" value="<?php //echo $inv->note?>" >
                                        </td>                                   
                                    </tr>
                                </tbody>
                            </table>
                        </div> -->
						
                

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
		var type = $(this).val();
		
		$.ajax({
         type: "POST",
         url: "<?= admin_url('recipe/getrecipeItemName') ?>",
         data: {type: type},
         dataType: "json",  
         cache:false,
         success: 
             function(response){
				var len = response.length;

                $("#item_name").empty();
                for( var i = 0; i<len; i++){
                    var id = response[i]['id'];
                    var name = response[i]['name'];
                    $("#item_name").append("<option value='"+id+"'>"+name+"</option>");
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
	     $results[n]['cate_name'] = v.category_name
	     $results[n]['sub_cat_name'] = v.subcategory_name
	     $results[n]['brand_name'] = v.brand_name
             $results[n]['html'] = v.name +' <strong>Cat</strong> -'+v.category_name+' | <strong>Sub</strong> -'+v.subcategory_name+' | <strong>Brand</strong> -'+v.brand_name;
	     
	     
	     $results[n]['text'] = v.name +' Cat-'+v.category_name+' | Sub-'+v.subcategory_name+' | Brand-'+v.brand_name;
             $results[n]['unit'] = v.unit;
             $results[n]['cost'] = v.cost;
	     $results[n]['unit_id'] = v.unit_id;
        })
        console.log($results)
                return {results: $results};
            } else {
                return { results: [{id: '', text: 'No Match Found'}]};
            }
        },
	
    },
    formatResult: function (i) {return '<div>'+i.html+'</div>';},
    formatSelection: function (i) {return '<div>'+i.text+'</div>'; },
  }).on('change', function (v) {
    $lable = v.added.text;
    $pid = v.added.id;
    $cm_id = v.added.cm_id;
    $uniqid = $pid+'-'+$cm_id;
    $unit = v.added.unit;
    $cost = v.added.cost;
    $unitid = v.added.unit_id;
    $('.search-purchase-items-new').select2("val", "");
    $existing_purchase_items.push($uniqid);
        $html = '<tr class="each-purchase-item">';
        $html +='<td  class="col-sm-4"><input class="form-control" type="text" value="'+$lable+'" disabled>';
        $html +='<input class="form-control" type="hidden" id="purchase_item_id" name="purchase_item_id[]" value="'+$pid+'"><input class="form-control" type="hidden" id="item_cm_id[]" name="purchase_item_cm_id[]" value="'+$cm_id+'"></td>';
        $html +='<td  class="col-sm-2"><input class="p-item-quantity form-control" type="text" id="purchase_item_quantity" name="purchase_item_quantity[]" value=""></td>';
	$html +='<td  class="col-sm-2"><input class="form-control" type="hidden" id="purchase_item_unit" name="purchase_item_unit[]" value="'+$unitid+'"><input readonly="readonly" class="p-item-unit form-control" type="test" name="purchase_item[unit][]" value="'+$unit+'"></td>';
        //$html +='<td  class="col-sm-2"><input class="p-item-cost form-control" type="hidden" name="purchase_item[cost][]" value="'+$cost+'"><input class="p-item-price form-control" type="text" name="purchase_item[price][]" value=""></td>';
       
        $html +='<td  class="col-sm-2 text-center"><a href="" data-id="" data-vid="'+$pid+'" class="btn btn-primary btn-xs remove-purchase-item_new"><i class="fa fa-trash-o"></i></a></td>'
        $html +='</tr>';
		
        $('.purchase-item-container tbody').prepend($html);
         $('.purchase-item-container tbody tr:first input[type="checkbox"]').iCheck({
        checkboxClass: 'icheckbox_square-blue',
        radioClass: 'iradio_square-blue',
        increaseArea: '20%'
        });
        $('.search-purchase-items-new').val();
		
		 
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
    }
    var index = $existing_purchase_items.indexOf($uniqid);
        if (index > -1) {
          $existing_purchase_items.splice(index, 1);
        }
    });
//    $(document).on('keyup','.p-item-quantity',function(){
//	$obj = $(this);
//	$q = $obj.val();
//	$c = $obj.closest('.each-purchase-item').find('.p-item-cost').val();
//	$p = $c*$q;
//	$obj.closest('.each-purchase-item').find('.p-item-price').val($p);
//    });
	});
 

 
</script>

<?php  echo form_close(); ?> 
