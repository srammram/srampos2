<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
  <link rel="stylesheet" href="<?= $assets ?>styles/jquery-ui.css">
  <script src="<?= $assets ?>js/jquery-ui.js"></script>
<?php

$v = "";

if ($this->input->post('start_date')) {
    $v .= "&start_date=" . $this->input->post('start_date');
}

?>

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
        <h2 class="blue"><i class="fa-fw fa fa-heart"></i><?= lang('stock_audit_rep'); ?> <?php
            if ($this->input->post('start_date')) {
                echo "From " . $this->input->post('start_date');
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
                    <a href="#" id="xls" class="tip" title="<?= lang('download_xls') ?>">
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
         <!--        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'id' => 'pos-item-sale-report');
             echo admin_form_open("reports/recipe", $attrib);?> -->

                    <div class="row">   

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

                       <div class="col-md-4">
                            <div class="form-group">
                                <?= lang("product", "product"); ?>
                                <?php
                                $prd['0'] = lang('all');
                                foreach ($Products as $product) {
                                    $prd[$product->id] = $product->name;
                                }
                                echo form_dropdown('product_id', $prd, '', 'class="form-control" data-placeholder="' . lang("select") . ' ' . lang("recipe") . '" id="product_id" style="width:100%;" ');
                                ?>
                                
                            </div>
                        </div>  

                        <div class="col-sm-4">
                            <div class="form-group">
                                <?= lang("date", "date"); ?>
                                <?php echo form_input('start_date', (isset($_POST['start_date']) ? $_POST['start_date'] : ""), 'class="form-control " autocomplete="off"  id="start_date"'); ?>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="category">Show</label>
                                <select name="pagelimit" class="form-control select" id="pagelimit" style="width:100px">
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
                            class="controls"> <?php echo form_submit('submit_report', $this->lang->line("submit"), 'class="btn btn-primary stock_audit"'); ?> </div>
                    </div>
                </div>
                    <!-- <?php echo form_close(); ?> -->
                <div class="clearfix"></div>

                <div class="table-responsive">
                    <table id="StockAuditData" class="table table-bordered table-hover table-striped table-condensed reports-table dataTable">
                        <thead>
                        <tr>
                            <th><?= lang("date"); ?></th>
                            <th><?= lang("product_name"); ?></th>
                            <th><?= lang("opening_stock"); ?></th>
                            <th><?= lang("current_stock"); ?></th>
                            <th><?= lang("consumed_of_the_day"); ?></th>
                            <th><?= lang("stock_avli_and_physical"); ?></th>
                            <th><?= lang("variance"); ?></th>
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
            window.location.href = "<?=admin_url('reports/getSalesReport/pdf/?v=1'.$v)?>";
            return false;
        });
        $('#xls').click(function (event) {
            event.preventDefault();
            window.location.href = "<?=admin_url('reports/getSalesReport/0/xls/?v=1'.$v)?>";
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
        $(document).on('click', '.stock_audit', function () {
            $url = '<?=admin_url('reports/get_StockAuditreports');?>';
            GetData($url);
        });

        $(document).on('change', '#pagelimit', function () {
            $url = '<?=admin_url('reports/get_StockAuditreports');?>';
            GetData($url);
        });
    });

function GetData($url){

            var start_date = $('#start_date').val();
            var product_id = $('#product_id').val();
            var warehouse_id = $('#warehouse_id').val();
            var pagelimit = $('#pagelimit').val();
            if (start_date !='' ) {
                
                 $('#start_date').css('border-color', '#ccc');
                 /*$('#product_id').siblings(".select2-container").find('.select2-choice').css('border-color', '#ccc'); */
                  $.ajax({
                        type: 'POST',
                        url: $url,
                        data: {start_date: start_date,product_id: product_id,warehouse_id: warehouse_id,pagelimit:pagelimit},
                        dataType: "json",
                        cache: false,
                        success: function (data) {
                            $('#StockAuditData > tbody').empty();
                            $('.dataTables_paginate').html(data.pagination);
                                $.each(data.stock_audit, function (a,b) 
                                {
                                   if(b.given_quantity != null){

                                    var  current = parseInt(b.given_quantity) -parseInt(b.soldQty);

                                    $('#StockAuditData > tbody').append('<tr class="text-center"><td>'+b.bill_date+'</td><td>'+b.name +'</td><td>'+b.given_quantity+'<input type="hidden"  class="given_quantity" value="'+b.given_quantity+'"/></td><td>'+current+'<input type="hidden"  class="current" value="'+current+'"/></td><td>'+b.soldQty+'<input type="hidden"  class="sold" value="'+b.soldQty+'"/></td><td><input type="text"  class="avail_stock form-control" /><span class="errmsg"></span></td><td><sapn class="variance"></span></td></tr>');   
                                   }
                                   else
                                   {
                                    $('#StockAuditData > tbody').append('<tr><td colspan="7" class="dataTables_empty"><?= lang('sEmptyTable') ?></td></tr>');  
                                   }               
                                });                                
                            }
                    });  
            }
            else{  
                if (start_date == '') { 
                    $('#start_date').css('border-color', 'red');
                }else{
                    
                   $('#start_date').css('border-color', '#ccc'); 
                }
               /* if (product_id == '') {  
                    $('#product_id').siblings(".select2-container").find('.select2-choice').css('border-color', 'red');
                } 
                else
                {
                    $('#product_id').siblings(".select2-container").find('.select2-choice').css('border-color', '#ccc');
                }*/
                return false;     
            }  
        }

$(document).ready(function(){
    $("#form").slideDown();
        $('#start_date').datepicker({
        dateFormat: "yy-mm-dd" ,
        maxDate:  0,      
    });
});
 $(document).on('keypress','.avail_stock',function (e) { 

     if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
        
        $(".errmsg").html("Digits Only").show().delay(2000).fadeOut();
        
        return false;
    }
   });
   
$(document).on('keyup','.avail_stock',function () { 

        var avail_stock = $(this).val();
        
        var sold =   $(this).parent().parent().children().find('.sold').val(); 
        var current =   $(this).parent().parent().children().find('.current').val(); 
        var given_quantity =   $(this).parent().parent().children().find('.given_quantity').val(); 

        var variance = parseInt(current) - parseInt(avail_stock);
        if(isNaN(variance))
        {
         variance  ='';
        }
        if(parseInt(variance) < 5 &&  parseInt(variance) > 0){
            $(this).parent().parent().children().find('.variance').css("color", "Green");
        }
        else  if(parseInt(variance) < 10 &&  parseInt(variance) > 5){
            $(this).parent().parent().children().find('.variance').css("color", "Orange");
        }
         else  if(parseInt(variance) >= 10){
            $(this).parent().parent().children().find('.variance').css("color", "red");
        }
        /*else if(parseInt(variance) < 10 parseInt(variance) > 5){
            $(this).parent().parent().children().find('.variance').css("color", "Orange");
        }
         else if(parseInt(variance) > 10){
            $(this).parent().parent().children().find('.variance').css("color", "red");
        }*/
        $(this).parent().parent().children().find('.variance').text(variance);    
}); 


</script>
<style type="text/css">
.errmsg
{
color: red;
}
</style>