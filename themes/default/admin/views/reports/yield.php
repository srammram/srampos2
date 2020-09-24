<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
  <link rel="stylesheet" href="<?= $assets ?>styles/jquery-ui.css">
  <script src="<?= $assets ?>js/jquery-ui.js"></script>
  

<script type="text/javascript">
    $(document).ready(function () {
        $('#form').hide();
        <?php if ($this->input->post('customer')) { ?>
        $('#customer').val(<?= $this->input->post('customer') ?>).select2({
            minimumInputLength: 1,
            data: [],
            initSelection: function (element, callback) {
                $.ajax({
                    type: "get", async: false,
                    url: site.base_url + "customers/suggestions/" + $(element).val(),
                    dataType: "json",
                    success: function (data) {
                        callback(data.results[0]);
                    }
                });
            },
            ajax: {
                url: site.base_url + "customers/suggestions",
                dataType: 'json',
                quietMillis: 15,
                data: function (term, page) {
                    return {
                        term: term,
                        limit: 10
                    };
                },
                results: function (data, page) {
                    if (data.results != null) {
                        return {results: data.results};
                    } else {
                        return {results: [{id: '', text: 'No Match Found'}]};
                    }
                }
            }
        });

        $('#customer').val(<?= $this->input->post('customer') ?>);
        <?php } ?>
        $('.toggle_down').click(function () {
            $("#form").slideDown();
            return false;
        });
        $('.toggle_up').click(function () {
            $("#form").slideUp();
            return false;
        });
    });
</script>


<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-heart"></i><?= lang('Yields_report'); ?> <?php
            if ($this->input->post('start_date')) {
                echo "From " . $this->input->post('start_date') . " to " . $this->input->post('end_date');
            }
            ?>
        </h2>

        <div class="box-icon">
            <ul class="btn-tasks">
            <li class="dropdown">
                <a href="#" class="toggle_up tip" title="<?= lang('hide_form') ?>">
                    <i class="icon fa fa-toggle-up"></i>
                </a>
            </li>
            <li class="dropdown">
                <a href="#" class="toggle_down tip" title="<?= lang('show_form') ?>">
                    <i class="icon fa fa-toggle-down"></i>
                </a>
            </li>
            </ul>
        </div>
        <div class="box-icon">
            <ul class="btn-tasks">
                <li class="dropdown">
                    <a href="javascript:void(0);" id="excel_report" class="excel_report" title="<?= lang('download_xls') ?>">
                        <i class="icon fa fa-file-excel-o"></i>
                    </a>
                </li>
                <li class="dropdown">
                    <a href="#" id="image" class="tip" title="<?= lang('save_image') ?>">
                        <i class="icon fa fa-file-picture-o"></i>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?= lang('customize_report'); ?></p>
                <div id="form">
                       <div class="col-md-4">
                            <div class="form-group">
                                <?= lang("warehouse", "warehouse"); ?>
                                <?php
                                $wh['0'] = lang('all');
                                foreach ($warehouses as $warehouse) {
                                    $wh[$warehouse->id] = $warehouse->name;
                                }
                                echo form_dropdown('warehouse_id', $wh, '', 'class="form-control" data-placeholder="' . lang("select") . ' ' . lang("warehouse") . '" id="warehouse_id" style="width:100%;" ');
                                ?>
                                
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <?= lang("start_date", "start_date"); ?>
                                <?php echo form_input('start_date', ($this->session->userdata('start_date')), 'class="form-control " autocomplete="off"  id="start_date"'); ?>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <?= lang("end_date", "end_date"); ?>
                                <?php echo form_input('end_date', ($this->session->userdata('end_date')), 'class="form-control "  autocomplete="off" id="end_date"'); ?>
                            </div>
                        </div>
						   
						 <div class="col-md-4">
                            <div class="form-group">
                                <?= lang("sale_items", "sale_items"); ?>
                                <?php
                                $res['0'] = lang('all');                                     
                                foreach ($recipes as $recp) {
                                    $res[$recp->id] = $recp->text;
                                }                           
                                echo form_dropdown('recipe_id', $res, '', 'class="form-control" data-placeholder="' . lang("select") . ' ' . lang("sale_items") . '" id="recipe_id" style="width:100%;" ');
                                ?>
                                
                            </div>
                        </div>
						<div class="col-md-4">
                            <div class="form-group">
                                <?= lang("varient", "varient"); ?>
                                <?php
                                $va['0'] = lang('all');
                                foreach ($varients as $varient) {
                                    $va[$varient->id] = $varient->name;
                                }
                                echo form_dropdown('varient_id', $va, '', 'class="form-control" data-placeholder="' . lang("select") . ' ' . lang("varient") . '" id="varient_id" style="width:100%;" ');
                                ?>
                                
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang("category", "category"); ?>
                                <?php
                                $ca['0'] = lang('all');
                                foreach ($categories as $category) {
                                    $ca[$category->id] = $category->name;
                                }
                                echo form_dropdown('category_id', $ca, '', 'class="form-control" data-placeholder="' . lang("select") . ' ' . lang("categories") . '" id="category_id" style="width:100%;" ');
                                ?>
                                
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang("sub_group", "sub_group"); ?>
                                <?php
                                $sub['0'] = lang('all');   
                                foreach ($sub_categories as $sub_cat) {
                                    $sub[$sub_cat->id] = $sub_cat->name;
                                }                            
                                echo form_dropdown('subcategory_id', $sub, '', 'class="form-control" data-placeholder="' . lang("select") . ' ' . lang("subcategory") . '" id="subcategory_id" style="width:100%;" ');
                                ?>
                                
                            </div>
                        </div>

                    
						
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="category">Show</label>
                                <select name="pagelimit" class="form-control select" id="pagelimit" style="width:100%">
                                <option value=""></option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="4">4</option>
                                <option value="10" selected="selected">10</option>
                                <option value="15">15</option>
                                <option value="30">30</option>
                                <option value="100">100</option>
                                <option value="0">All</option>
                                </select>
                            </div>
                        </div>
                    </div>
					
                    <div class="form-group">
                        <div  
                            class="controls"> <?php echo form_submit('submit_report', $this->lang->line("submit"), 'class="btn btn-primary submit_itemreport" style="margin-top:30px;"'); ?> </div>
                             
                    </div>
                </div>
                    
                <div class="clearfix"></div>
                <div class="table-responsive">
                    <table id="SlRData" class="table table-bordered table-hover table-striped table-condensed reports-table">
                        <thead>
                        <tr>
                            <th><?= lang("item"); ?></th>
                            <th><?= lang("variant"); ?></th>
							<th><?= lang("warehouse"); ?></th>
                            <th><?= lang("Yield (%)"); ?></th>
							  <th><?= lang("Yield_amount"); ?></th>
                         
							</tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                    <div class="col-md-6 text-right" style="float:right">
                        <div class="dataTables_paginate paging_bootstrap"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="<?= $assets ?>js/html2canvas.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('#pdf').click(function (event) {
            event.preventDefault();
            window.location.href = "<?=admin_url('reports/get_ItemwiseYieldReport/pdf/?v=1'.$v)?>";
            return false;
        });
        $('#xls').click(function (event) {
            event.preventDefault();
            window.location.href = "<?=admin_url('reports/get_ItemwiseYieldReport/0/xls/?v=1'.$v)?>";
            return false;
        });
        $('#image').click(function (event) {
            event.preventDefault();
            html2canvas($('.box'), {
                onrendered: function (canvas) {
                    openImg(canvas.toDataURL());
                }
            });
            return false;
        });
        $(document).on('click', '.pagination a',function(e){
            e.preventDefault();
            $url = $(this).attr("href");
            GetData($url);
            return false;
        });
        $(document).on('click', '.submit_itemreport', function () {
            $url = '<?=admin_url('reports/get_ItemwiseYieldReport');?>';
            GetData($url);
        });

        $(document).on('change', '#pagelimit', function () {
            $url = '<?=admin_url('reports/get_ItemwiseYieldReport');?>';
            GetData($url);
        });
            $url = '<?=admin_url('reports/get_ItemwiseYieldReport');?>';            
            GetData($url);

    });
function GetData($url) {    
            /*var recipe = $('#suggest_recipe').val();*/
            var start_date = $('#start_date').val();
            var end_date = $('#end_date').val();
            var warehouse_id = $('#warehouse_id').val();
			var varient_id = $('#varient_id').val();
            var category_id = $('#category_id').val();
            var category_id = $("#category_id option:selected").val();
            var subcategory_id = $('#subcategory_id').val();
            var subcategory_id = $("#subcategory_id option:selected").val();
            var recipe_id = $("#recipe_id option:selected").val();
            var pagelimit = $('#pagelimit').val();  
			var dis_status = $('#dis_status').val();  			
            if (start_date !='' && end_date !='' ) {
                 $('#start_date,#end_date').css('border-color', '#ccc'); 
                  $.ajax({
                        type: 'POST',
                        url: $url,                    
                        data: {start_date: start_date, end_date: end_date, warehouse_id: warehouse_id,varient_id: varient_id,pagelimit:pagelimit,category_id:category_id,subcategory_id:subcategory_id,recipe_id:recipe_id,dis_status:dis_status},
                        dataType: "json",
                        success: function (data) {
                            $('#SlRData > tbody').empty(); 
                            if(data.wastageReport =='empty' || data.wastageReport == 'error'){ 
                                 $('#SlRData > tbody').append('<tr><td colspan="6" class="dataTables_empty"><?= lang('sEmptyTable') ?></td></tr>');
                            }
                            else{
                                $('.dataTables_paginate').html(data.pagination);
                                $('#SlRData > tbody').empty();
                                $('#SlRData > tbody').append('<tr><td style = "text-align:left"><strong>From: </strong>   '+start_date+'</td><td style = "text-align:left"><strong>To: </strong>   '+end_date+'</td></tr>');
                                var grand_qty  = 0;
                                var grand_total = 0;
                                <?php 
                                     $display='none';
                                     if($this->pos_settings->default_service_charge !=0 && $this->pos_settings->service_charge_option !=0) { ?>
                                            $display='visible';
                                <?php } ?>
                               
                                $.each(data.yield, function (a,b) {  
                                  $('#SlRData > tbody').append('<tr><td style = "text-align:left"><strong>GROUP: </strong>   '+b.category+'</td></tr>');
                                         var grp_qty  = 0;
                                         var grp_price = 0;
                                        $.each(b.split_order, function (c,d) {  
                                            $('#SlRData > tbody').append('<tr><td style = "text-align:left"><strong>Sub Group:</strong>'+d.sub_category+'</td></tr>');  
                                               var sub_qty  = 0;
                                               var sub_price = 0;
											   var cost=0;
                                             $.each(d.order, function (e,f) {
												 cost =formatDecimal((f.ap_cost/f.yield)*100);
												 sub_price += parseFloat(cost);
                                                $('#SlRData > tbody').append('<tr  class="text-right"><td class="text-center">'+f.name+'</td><td class="text-center">'+f.variant+'</td><td>'+f.warehouse+'</td><td>'+formatQuantity(f.yield)+'</td><td>'+formatMoney(cost)+'</td></tr>');
                                            }); 
											grp_price +=parseFloat(sub_price);
                                             $('#SlRData > tbody').append('<tr style="font-weight:bold" class="text-right"><td class="text-left">Sub Total:</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>'+formatMoney(sub_price)+'</td></tr>');
                                       });

                                        $('#SlRData > tbody').append('<tr style="font-weight:bold" class="text-right"><td class="text-left">Group Total:</td><td></td><td>&nbsp;</td><td>&nbsp;</td><td>'+formatMoney(grp_price)+'</td></tr>');
                                });
                              
                                /*   $('#SlRData > tbody').append('<tr style="font-weight:bold"  class="text-right"><td class="text-left">Grand Total:</td><td>&nbsp;</td><td>&nbsp;</td><td>'+formatQuantity(grand_qty)+'</td><td>'+formatMoney(grand_rate+grand_discount)+'</td><td>'+formatMoney(grand_discount)+'</td><td></td><td style="display:<?php echo $display; ?>">'+formatMoney(grand_service_charge)+'</td><td>'+formatMoney(grand_tax)+'</td><td>'+formatMoney((grand_total))+'</td></tr>'); */
                            }
                        }
                    });  
            }
            else{
                if (start_date =='') {                    
                    $('#start_date').css('border-color', 'red');
                }else{
                   $('#start_date').css('border-color', '#ccc'); 
                }
                if (end_date =='') {                    
                    $('#end_date').css('border-color', 'red');
                }else{
                   $('#end_date').css('border-color', '#ccc'); 
                }
                return false;     
            }  
        };   
$(document).ready(function(){
    $("#form").slideDown();
        $('#end_date').datepicker({
        dateFormat: "yy-mm-dd" ,
        maxDate:  0,      
    });
    $("#start_date").datepicker({
        dateFormat: "yy-mm-dd" ,  
        maxDate:  0,      
        onSelect: function(date){            
            var date1 = $('#start_date').datepicker('getDate');           
            var date = new Date( Date.parse( date1 ) ); 
            date.setDate( date.getDate());        
            var newDate = date.toDateString(); 
            newDate = new Date( Date.parse( newDate ) );                      
            $('#end_date').datepicker("option","minDate",newDate);            
        }
    });
});


$("#category_id").change(function(){
   // $("#subcategory_id").select2("destroy").empty();
   $('#subcategory_id').select2();
  var category_id = $("#category_id").val();
  var $options = $();
  /*if (category_id !=0) {*/
   $.ajax({
            type: "post", async: false,
            url: "<?= admin_url('reports/getItem_report_SubCategories') ?>",
              data: { 
            category_id: category_id
            },
            dataType: "json",
            success: function (scdata) {
              if(scdata != null){
                 $options = $options.add($('<option>').attr('value', 0).html('All'));
                 $.each(scdata, function(key, value) {
                    $options = $options.add($('<option>').attr('value', value.id).html(value.text));
                        });
                 $('#subcategory_id').html($options).trigger('change');
              } else {
                $("#subcategory_id").val(null).trigger('change');
              }
            }
        });
 /*} else {
    $("#subcategory_id").select2("destroy").empty();
            }*/
           
});   
$("#subcategory_id").change(function(){   
   $('#recipe_id').select2();
  var subcategory_id = $("#subcategory_id").val();
  var $options = $();
  // if (subcategory_id !=0) {
   $.ajax({
            type: "post", async: false,
            url: "<?= admin_url('reports/getItem_by_SubCategories') ?>",
              data: { 
            subcategory_id: subcategory_id
            },
            dataType: "json",
            success: function (scdata) {
              if(scdata != null){
                 $options = $options.add($('<option>').attr('value', 0).html('All'));
                 $.each(scdata, function(key, value) {
                    $options = $options.add($('<option>').attr('value', value.id).html(value.text));
                        });
                 $('#recipe_id').html($options).trigger('change');
              } else {                
                $("#recipe_id").val(null).trigger('change');                
              }
            }
        });
 /*} else {
    $("#recipe_id").select2("destroy").empty();
 }*/
           
});   

$(".excel_report").click(function(){

      $("#SlRData").table2excel({

        // exclude CSS class

        exclude: ".noExl",

        name: "Worksheet Name",

        filename: "Yield Reports " //do not include extension

      });

    });
</script>