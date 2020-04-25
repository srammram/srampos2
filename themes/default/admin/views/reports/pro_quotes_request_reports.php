<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
  <link rel="stylesheet" href="<?= $assets ?>styles/jquery-ui.css">
  <script src="<?= $assets ?>js/jquery-ui.js"></script>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-heart"></i><?= lang('quotation_request_report'); ?> <?php
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
                       <div class="col-md-3">
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

                       

                        <div class="col-sm-3">
                            <div class="form-group">
                                <?= lang("start_date", "start_date"); ?>
                                <?php echo form_input('start_date', ($this->session->userdata('start_date')), 'class="form-control " autocomplete="off"  id="start_date"'); ?>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <?= lang("end_date", "end_date"); ?>
                                <?php echo form_input('end_date', ($this->session->userdata('end_date')), 'class="form-control "  autocomplete="off" id="end_date"'); ?>
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
                            class="controls"> <?php echo form_submit('submit_report', $this->lang->line("submit"), 'class="btn btn-primary submit_itemreport"'); ?> </div>
                             
                    </div>
                </div>
                    <!-- <?php echo form_close(); ?> -->
                <div class="clearfix"></div>

                <div class="table-responsive">
                    <table id="SlRData" class="table table-bordered table-hover table-striped table-condensed reports-table">
                        <thead>
                        <tr>
                            <th><?= lang("s_no"); ?></th>
                            <th><?= lang("reference"); ?></th>
                            <th><?= lang("date"); ?></th>
                            <th><?= lang("store_intend_request"); ?></th>
			                <th><?= lang("store_request"); ?></th>
                            <th><?= lang("supplier"); ?></th>
                            <th><?= lang("product_code"); ?></th>
                            <th><?= lang("product_name"); ?></th>
                            <th><?= lang("quantity"); ?></th>
                            <th><?= lang("cost"); ?></th> 
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
    var $offset = false;
    $(document).ready(function () {
      /*  $('#pdf').click(function (event) {
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
        });*/
        $(document).on('click', '.pagination a',function(e){
            e.preventDefault();
            $url = $(this).attr("href");
            $url_seg = $url.split('/');
            $count = $url_seg.length-1;           
            $offset = (isNaN($url_seg[$count]))?false:$url_seg[$count];
            GetData($url);
            return false;
        });

        $(document).on('click', '.submit_itemreport', function () {
            $offset = false;
            $url = '<?=admin_url('reports/get_quotes_request_reports');?>';
            GetData($url);
        });

        $(document).on('change', '#pagelimit', function () {
            $offset = false;
            $url = '<?=admin_url('reports/get_quotes_request_reports');?>';
            GetData($url);
        });

            $url = '<?=admin_url('reports/get_quotes_request_reports');?>';            
            GetData($url);

    });
function GetData($url) {    
            /*var recipe = $('#suggest_recipe').val();*/
            var start_date = $('#start_date').val();
            var end_date = $('#end_date').val();
            var warehouse_id = $('#warehouse_id').val();
            var summary_items = $('#summary_items').val();
            var pagelimit = $('#pagelimit').val();     

            if (start_date !='' && end_date !='' ) {
                 $('#start_date,#end_date').css('border-color', '#ccc'); 
                  $.ajax({
                        type: 'POST',
                        url: $url,                    
                        data: {start_date: start_date, end_date: end_date, warehouse_id: warehouse_id,summary_items:summary_items,pagelimit:pagelimit},
                        dataType: "json",
                         success: function (data) {
                             
                            if(data.report =='empty'){ 
                                 $('#SlRData > tbody').empty();  
                                $('#SlRData > tbody').append('<tr><td colspan="9" class="dataTables_empty"><?= lang('sEmptyTable') ?></td></tr>');
                                
                            }
                            else if(data.report == 'error'){
                                
                            }
                            else{
                                // bbq_items
                                $('.dataTables_paginate').html(data.pagination);
                                $('#SlRData > tbody').empty();                                                              
                                
                                   
                                    var $row_index = ($offset) ?parseInt($offset)+parseInt(1):1;

                                    var quantity =0;
                                    var cost_price =0;

                                    $.each(data.report, function (a,b) 
                                    {            
                                        /*cost_price +=(parseFloat(b.costper_orderqty));
                                        retrun_price +=(parseFloat( b.Return_Price));*/
                                        quantity +=parseFloat(b.quantity);
                                        cost_price +=parseFloat(b.unit_price);

                                        // profit += (parseFloat(b.profit));

                                         $('#SlRData > tbody').append('<tr><td>'+$row_index+'</td><td>'+b.date+'</td><td class="text-center">'+b.reference_no+'</td><td class="text-center">'+b.store_request+'</td><td class="text-center">'+b.store_name+'</td><td class="text-center">'+b.supplier+'</td><td class="text-center">'+b.product_code+'</td><td class="text-center">'+b.product_name+'</td><td class="text-center">'+b.quantity+'</td><td class="text-center">'+parseFloat(b.unit_price)+'</td></tr>');
                                        $row_index++;
                                    });

                                  $('#SlRData > tbody').append('<tr style="font-weight:bold"><td colspan="8" >Total: </td><td class="text-right">'+formatDecimal(quantity)+'</td><td class="text-right">'+formatMoney(cost_price)+'</td></tr>');
                                
                                
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
$(".excel_report").click(function(){

      $("#SlRData").table2excel({

        // exclude CSS class

        exclude: ".noExl",

        name: "Worksheet Name",

        filename: "Item Sale Reports " //do not include extension

      });

    });
</script>