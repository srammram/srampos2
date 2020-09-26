<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<link rel="stylesheet" href="<?= $assets ?>styles/jquery-ui.css">
  <script src="<?= $assets ?>js/jquery-ui.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
       /* $('#form').hide();        
        $('.toggle_down').click(function () {
            $("#form").slideDown();
            return false;
        });
        $('.toggle_up').click(function () {
            $("#form").slideUp();
            return false;
        });*/
    });
</script>
<?php
$v = "";
if ($this->input->post('warehouse_id')) {
    $v
     .= "&warehouse_id=" . $this->input->post('warehouse_id');
}
if ($this->input->post('type')) {
    $v .= "&type=" . $this->input->post('type');
}

if ($this->input->post('recipe_id')) {
    $v .= "&recipe_id=" . $this->input->post('recipe_id');
}

if ($this->input->post('category_id')) {
    $v .= "&category_id=" . $this->input->post('category_id');
}

if ($this->input->post('subcategory_id')) {
    $v .= "&subcategory_id=" . $this->input->post('subcategory_id');
}

?>
<script>
   /*  $(document).ready(function () {        
        oTable = $('#PrRData').dataTable({
            "aaSorting": [[1, "asc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= admin_url('reports/item_stock_details/?v=1'.$v) ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            "fnRowCallback" : function(nRow, aData, iDisplayIndex){
                var oSettings = oTable.fnSettings();
                $index = oSettings._iDisplayStart+parseInt(iDisplayIndex) +parseInt(1) ;
                $("td:first", nRow).html($index);
                return nRow;
            },
            "aoColumns": [null,null, null,null,null,null,null,null,null,null,null,null,null,null],
            "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
               
            }
        }).fnSetFilteringDelay().dtFilter([
          //  {column_number: 1, filter_default_label: "[<?=lang('brand');?>]", filter_type: "text", data: []},
        ], "footer");
    }); */
</script>



<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-heart"></i><?= lang('stock_report'); ?> <?php
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
                    <a href="javascript:void(0);" id="xls" class="excel_report" title="<?= lang('download_xls') ?>">
                        <i class="icon fa fa-file-excel-o"></i>
                    </a>
                </li>
               
            </ul>
        </div>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                 <div id="form">
                    <div class="row">   
                      <div class="col-md-4">
                            <div class="form-group">
                                <?= lang("warehouse", "warehouse"); ?>
                                <?php
                                $wh['0'] = lang('all');
                                foreach ($warehouses as $warehouse) {
                                    $wh[$warehouse->id] = $warehouse->name;
                                }
                                echo form_dropdown('warehouse_id', $wh, $this->input->post('warehouse_id'), 'class="form-control" data-placeholder="' . lang("select") . ' ' . lang("warehouse") . '" id="warehouse_id" style="width:100%;" ');
                                ?>
                            </div>
                        </div>  

                       <div class="col-md-4">
                            <div class="form-group">
                                <div class="form-group">
                                    <?= lang("item_type", "type") ?>
                                    <?php                                    
                                    $opts = array('0' => lang('all'), 'standard' => lang('standard'), 'production' => lang('production'),'quick_service' => lang('quick_service'),'combo' => lang('combo'), 'addon' => lang('addon'),'semi_finished' => lang('semi_finished'),'raw' => lang('raw'),'service' => lang('service'));
                                    echo form_dropdown('type', $opts, $this->input->post('type'), 'class="form-control" id="type" required="required"');
                                    ?>
                                </div>
                            </div>
                        </div>  
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang("recipe", "recipe"); ?>
                                <?php
                                $recip['0'] = lang('all');
                                foreach ($sale_items as $item) {
                                    $recip[$item->id] = $item->name;
                                }
                                echo form_dropdown('recipe_id', $recip, $this->input->post('recipe_id'), 'class="form-control" data-placeholder="' . lang("select") . ' ' . lang("recipe") . '" id="recipe_id" style="width:100%;" ');
                                ?>
                                
                            </div>
                        </div>                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang("category", "category"); ?>
                                <?php  $ca['0'] = lang('all');
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
<div class="form-group col-sm-1">
                            <div class="form-group">
                             <?= lang("Show", "Show"); ?>
                               <select name="pagelimit" class="form-control select" id="pagelimit">
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
					<a href="<?php echo base_url("/admin/reports/item_stock");   ?>"><button type="button" class="btn col-lg-1 btn-sm btn-danger " style="margin-right:15px;height:30px!important;font-size: 12px!important" id="reset" tabindex="-1">Reset</button></a>
                        <div
                            class="controls"> <?php echo form_submit('submit_report', $this->lang->line("submit"), 'class="btn btn-primary stock_reports"'); ?> </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="PrRData"
                           class="table table-bordered table-hover table-striped table-condensed reports-table" style="white-space: nowrap;">
                        <thead>
                        <tr>
                            <th><?= lang("s.no") ?></th>
                            <th><?= lang("product") ?></th>
                            <th><?= lang("type") ?></th>
                            <th><?= lang("category") ?></th>
                            <th><?= lang("subcategory") ?></th>
                            <th><?= lang("brand") ?></th>
							<th><?= lang("variant") ?></th>
                            <th><?= lang("batch") ?></th>
                            <th><?= lang("stock_in") ?></th>
                            <th><?= lang("stock_out") ?></th>
                            <th><?= lang("current_stock") ?></th>  
                            <th><?= lang("uom") ?></th>                            
                            <th><?= lang("cost_price") ?></th>
                            <th><?= lang("selling_price") ?></th>
                            <th><?= lang("expiry") ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td colspan="14" class="dataTables_empty"><?= lang('loading_data_from_server') ?></td>
                        </tr>
                        </tbody>
                        <tfoot class="dtFilter">
                        <tr class="active">
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
							  <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                        </tfoot>
                    </table>
					  <div class="col-md-6 text-right" style="float:left">
                        <div class="dataTables_paginate paging_bootstrap"></div>
                    </div>
                </div>
            <div class="form-group">
                <!-- <div
                    class="controls"> <?php echo form_submit('submit_report', $this->lang->line("submit"), 'class="btn btn-primary"'); ?> 
                </div> -->
            </div>
            
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="<?= $assets ?>js/html2canvas.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
     $(".excel_report").click(function(){
      $("#SlRData").table2excel({       
        exclude: ".noExl",
        name: "Worksheet Name",
        filename: "Customer Loyalty Point Report " //do not include extension
      });
    });
    });

$("#category_id").change(function(){   
   $('#subcategory_id').select2();
  var category_id = $("#category_id").val();
  var $options = $();  
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
});   
$("#subcategory_id").change(function(){   
   $('#recipe_id').select2();
		var subcategory_id = $("#subcategory_id").val();
		var $options = $();  
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
});   

    /*$(document).on('click', '.stock_reports', function (e) {        
         e.preventDefault();
        $("#testForm").submit();
    });*/
	

    var $offset = false;
    $(document).ready(function () {
       
        $('#xls').click(function (event) {
            event.preventDefault();
            window.location.href = "<?=admin_url('reports/getCurrentStock_excel')?>";
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
            $url_seg = $url.split('/');
            $count = $url_seg.length-1;           
            $offset = (isNaN($url_seg[$count]))?false:$url_seg[$count];
            GetData($url);
            return false;
        });
        $(document).on('click', '.stock_reports', function () {
            $offset = false;
            $url = '<?=$details_url?>';
            GetData($url);
        });

        $(document).on('change', '#pagelimit', function () {
            $offset = false;
            $url = '<?=$details_url?>';
            GetData($url);
        });
            $url = '<?=$details_url?>';
            GetData($url);
        $('#viewSettlement').on("hidden.bs.modal", function(){
				$(this).removeData();
		});
    });
	
function GetData($url){   
    var recipe_id           = $('#recipe_id').val();
    var type                = $('#type').val();
    var warehouse_id        = $('#warehouse_id').val();
    var category_id         = $('#category_id').val();
    var subcategory_id      = $('#subcategory_id').val();
    var pagelimit           = $('#pagelimit').val();
                  $.ajax({
                        type: 'POST',
                        url: $url,
                        data: {'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>', recipe_id : recipe_id,pagelimit:pagelimit,type:type,warehouse_id:warehouse_id,category_id:category_id,subcategory_id:subcategory_id},
                        dataType: "json",
                        success: function (data) {
                            $('#PrRData > tbody').empty();
                            if(data.reports =='empty' || data.reports == 'error'){
                            $('#PrRData > tbody').append('<tr><td colspan="5" class="dataTables_empty"><?= lang('sEmptyTable') ?></td></tr>');    
                            }
                            else{
                                $('.dataTables_paginate').html(data.pagination);
                                var $row_index = ($offset) ?parseInt($offset)+parseInt(1):1;
				                $t_stock_in =0;
								$t_stock_out = 0;
								$t_stock_current = 0;
				                $t_stock_v_cost_price = 0;
				                $t_stock_v_selling_price = 0;
				               $.each(data.reports, function (n,v) {
								   $current_stock=0
								 $stock_in = 0;
								 $stock_out = 0;
								 $stock_current = 0;
								 $stock_v_selling_price = 0;
								 $stock_v_cost_price = 0;
							      if (v.stock) {
                                    $.each(v.stock, function (a,b) {
										$uom=(b.unit_name !=null)?b.unit_name:'';
								    $s_v_c_p = b.stock_in * b.cost_price;
								    $s_v_l_c = b.stock_in * b.selling_price;
									$current_stock =b.stock_in;
                                    $html = '<tr>';
                                    $html +='<td>'+$row_index+'</td>';
                                    $html +='<td>'+b.item_name+'</td>';
									$html +='<td>'+b.type+'</td>';
									$html +='<td>'+b.category_name+'</td>';
									$html +='<td>'+b.subcategory_name+'</td>';
									$html +='<td>'+b.brand_name+'</td>';
									$html +='<td>'+b.variant+'</td>';
									$html +='<td>'+b.batch+'</td>';
									$html +='<td>'+formatDecimals(b.stock_in,4)+'</td>';
									$html +='<td>'+formatDecimals( b.stock_out,4)+'</td>';
									$html +='<td>'+formatDecimals($current_stock,4)+'</td>';
									$html +='<td>'+$uom+'</td>';
									$html +='<td>'+b.cost_price+'</td>';
									$html +='<td>'+b.selling_price+'</td>';
									$html +='<td>'+b.expiry_date+'</td>';
                                    $html +='</tr>';
                                    $('#PrRData > tbody').append($html);
                                    $row_index++;
									$stock_in +=parseInt(b.stock_in);
									$stock_out +=parseInt(b.stock_out);
									$stock_current += formatDecimals($current_stock,4);
									//$t_stock_v_cost_price += $s_v_s_p;
									//$stock_v_cost_price += $s_v_s_p;
                                });
				                    $t_stock_in +=$stock_in;
				                    $t_stock_out += $stock_out;
				                    $t_stock_current += $stock_current;
									$footer = '<tr style="font-weight:bold">';
									$footer +='<td colspan=8><?=lang('total')?></td>';
									$footer +='<td colspan=1>'+$stock_in+'</td>';
									$footer +='<td>'+formatDecimals($stock_out)+'</td>';
									$footer +='<td></td>';
									$footer +='<td></td>';
									$footer +='<td></td>';
									$footer +='<td></td>';
									$footer +='<td></td>';
									$footer +='</tr>';
									$('#PrRData > tbody').append($footer);
							       }
			                  	});
									/* $footer = '<tr style="font-weight:bold">';
									$footer +='<td colspan=7><?=lang('Grand Total')?></td>';
									$footer +='<td colspan=1>'+$t_stock_in+'</td>';
									$footer +='<td>'+formatDecimals($t_stock_out)+'</td>';
									$footer +='<td>'+formatDecimals($t_stock_current)+'</td>';
									$footer +='<td></td>';
									$footer +='<td></td>';
									$footer +='<td></td>';
									$footer +='<td></td>';
									$footer +='</tr>';
									$('#PrRData > tbody').append($footer); */
                           } 
                        }
                    });
    //}
    //else{
    //    if (start == '') {                    
    //        $('#start_date').css('border-color', 'red');
    //    }else{
    //       $('#start_date').css('border-color', '#ccc'); 
    //    }
    //    if (end == '') {                    
    //        $('#end_date').css('border-color', 'red');
    //    }else{
    //        $('#end_date').css('border-color', '#ccc'); 
    //    }
    // 
    //    return false;    
    //}  
}   

$(document).ready(function(){
    $("#form").slideDown();
        $('#end_date').datepicker({
        dateFormat: "yy-mm-dd" ,
        maxDate:  0,      
    });
	$('#search-expiry').datepicker({
        dateFormat: "yy-mm-dd" ,
            
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

    $(document).on('change', '#end_date', function () {        
        var start = $('#start_date').val();
        var end = $('#end_date').val();
        var warehouse_id = $('#warehouse_id').val();
        $.ajax({
        type: 'POST',
        url: "<?=admin_url('reports/get_bill_no');?>",
        data: {'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>',start: start, end: end, warehouse_id: warehouse_id},
        dataType: "json",
             success: function (data) {
                $("#bill_no").empty();
                $("#bill_no").append("<option value=''>Select</option>");
                if(data.bill_no !='empty' || data.bill_no != 'error'){
                  $.each(data.bill_no, function (a,b){
                   $("#bill_no").append('<option value=' + b.id + '>' + b.bill_number + '</option>');
                 });
               } 
            }
             
        })
    });
});

$(document).ready(function(){
    $('#search-invoice').select2({
       minimumInputLength: 1,
       ajax: {
        url: site.base_url+"reports/search_invoice",
        dataType: 'json',
        quietMillis: 15,
        data: function (term, page) {
            return {
                term: term,
                limit: 10
            };
        },
        results: function (data, page) {
            if(data.results != null) {
                return { results: data.results };
            } else {
                return { results: [{id: '', text: 'No Match Found'}]};
            }
        }
    }
});

})

</script>
</script>