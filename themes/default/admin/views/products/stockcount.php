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
        <h2 class="blue"><i class="fa-fw fa fa-heart"></i><?= lang('count_stock'); ?> <?php
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
          

                    <div class="row">   

                      <div class="col-md-5">
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

                       <div class="col-md-5">
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
                    </div>
                    <div class="form-group">
                        <div
                            class="controls"> <?php echo form_submit('submit_report', $this->lang->line("submit"), 'class="btn btn-primary stockcount"'); ?> </div>
                    </div>
                </div>
                 
             
            <form role="form" id="count-stock" enctype="multipart/form-data" method="post" accept-charset="utf-8">
            <input type="hidden" id="product_name" name="product_name">
            <input type="hidden" id="whearhouse" name="whearhouse"> 
                <div class="clearfix"></div>

                <div class="table-responsive">
                    <table id="StockAuditData" class="table table-bordered table-hover table-striped table-condensed reports-table dataTable">
                        <thead>
                        <tr>
                            <th><?= lang("sno"); ?></th>
                            <th><?= lang("product_name"); ?></th>
                            <th><?= lang("current_stock"); ?></th>
                            <th><?= lang("stock_avli_and_physical"); ?></th>
                            <th><?= lang("variance"); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                 <div class="form-group savecounts" style="display: none">
                        <div class="controls"> <?php echo form_submit('save_stockcounts', $this->lang->line("save"), 'class="btn btn-primary stock_count"'); ?> 
                        </div>
                </div>
                <?php echo form_close(); ?> 
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
    });

 $(document).on('click', '.stockcount', function () { 
            
            var product_id = $('#product_id').val();
            var warehouse_id = $('#warehouse_id').val();
            
                  $.ajax({
                        type: 'POST',
                        url: '<?=admin_url('products/get_StockAuditreports');?>',
                        data: {product_id: product_id,warehouse_id: warehouse_id},
                        dataType: "json",
                        cache: false,
                        success: function (data) {
                            $('#StockAuditData > tbody').empty();
                              $i= 0;
                              if(data.stockcount != 'empty') {
                                $.each(data.stockcount, function (a,b) 
                                {
                                    $('.savecounts').css('display','block');
                                    var current = (parseInt(b.open_stock) +(parseInt(b.purchased_qty))-(parseInt(b.soldQty)));
                                    $i++;
                                    $('#StockAuditData > tbody').append('<tr class="text-center"><td>'+$i+'</td><td><input type="hidden" name="stock['+$i+'][id]" class="form-control" value="'+b.id+'" /><input type="hidden" name="stock['+$i+'][produ_name]" class="form-control" value="'+b.name+'" /><input type="hidden" name="stock['+$i+'][produ_code]" class="form-control" value="'+b.code+'" />'+b.name+'</td><td>'+current+'<input type="hidden"  class="current" name="stock['+$i+'][current]" value="'+current+'"/></td><td><input type="text" name="stock['+$i+'][avail_stock]" class="avail_stock form-control" value="" /><span class="errmsg"></span></td><td><input type="hidden"  name="stock['+$i+'][variance]" class="variant" value="0" /><sapn class="variance"></span></td></tr>');   
                                                  
                                }); 
							  } else {
								  $('#StockAuditData > tbody').append('<tr><td colspan="8" class="dataTables_empty"><?= lang('sEmptyTable') ?></td></tr>');
							  }
                                                        
                            }
                    });  
           
        });

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

        var variance = parseInt(avail_stock) - parseInt(current);
        if(isNaN(variance))
        {
         variance  ='';
        }
        if(parseInt(variance) > -5 &&  parseInt(variance) < 0){
            $(this).parent().parent().children().find('.variance').css("color", "Green");
        }
        else  if(parseInt(variance) > -10 &&  parseInt(variance) < -5){
            $(this).parent().parent().children().find('.variance').css("color", "Orange");
        }
         else  if(parseInt(variance) <= -10){
            $(this).parent().parent().children().find('.variance').css("color", "red");
        }
       
        $(this).parent().parent().children().find('.variance').text(variance);    
        $(this).parent().parent().children().find('.variant').val(variance);    
}); 
$(document).on('click', '.stock_count', function (e) { 
    var count = 0;
    $.each($('.avail_stock'),function() {
        if (this.value != "") {
           count++;
       } 
    });
   var formdata =  $('#count-stock').serialize();
    if(count !=0) 
    {   e.preventDefault();
        $.ajax({
            type : 'POST',
            url  : '<?=admin_url('products/save_stockcounts');?>',  
            data : formdata,
            dataType: "json",
            success:function(data){
                console.log(data);
                window.location.reload();
                return true;
            }
        });
    }
    else{
        alert('please fill any one physical_stock');
        return false;
    }
});
$(document).on('change', '#product_id', function () {
    $('#StockAuditData > tbody').empty();
    $('.savecounts').css('display','none');
    var product_name = $("#product_id option:selected").text();
    $('#product_name').val(product_name);
});
$(document).on('change', '#warehouse_id', function () {
    $('#StockAuditData > tbody').empty();
    $('.savecounts').css('display','none');
    var warehouse_id = $(this).val();
    $('#whearhouse').val(warehouse_id);
});
</script>
<style type="text/css">
.errmsg
{
color: red;
}
</style>