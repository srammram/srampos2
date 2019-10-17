<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
  <link rel="stylesheet" href="<?= $assets ?>styles/jquery-ui.css">
  <script src="<?= $assets ?>js/jquery-ui.js"></script>
<?php

$v = "";

if ($this->input->post('product')) {
    $v .= "&product=" . $this->input->post('product');
}
if ($this->input->post('reference_no')) {
    $v .= "&reference_no=" . $this->input->post('reference_no');
}
if ($this->input->post('customer')) {
    $v .= "&customer=" . $this->input->post('customer');
}
if ($this->input->post('biller')) {
    $v .= "&biller=" . $this->input->post('biller');
}
if ($this->input->post('warehouse')) {
    $v .= "&warehouse=" . $this->input->post('warehouse');
}
if ($this->input->post('user')) {
    $v .= "&user=" . $this->input->post('user');
}
if ($this->input->post('serial')) {
    $v .= "&serial=" . $this->input->post('serial');
}
if ($this->input->post('start_date')) {
    $v .= "&start_date=" . $this->input->post('start_date');
}
if ($this->input->post('end_date')) {
    $v .= "&end_date=" . $this->input->post('end_date');
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
        <h2 class="blue"><i class="fa-fw fa fa-heart"></i><?= lang('monthly_reports'); ?> <?php
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
                <!-- <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'id' => 'pos-item-sale-report');
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

                        <div class="col-sm-4">
                            <div class="form-group">
                                <?= lang("month", "month"); ?>
                                <?php echo date('Y-m');?>
                                <?php echo form_input('start_date', (date('Y-m')), 'class="form-control " autocomplete="off"  id="start_date"'); ?>
                            </div>
                        </div>
                       <!-- <div class="col-sm-4">
                            <div class="form-group">
                                <?= lang("end_date", "end_date"); ?>
                                <?php echo form_input('end_date', (isset($_POST['end_date']) ? $_POST['end_date'] : ""), 'class="form-control " id="end_date"'); ?>
                            </div>
                        </div>-->
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="category">Show</label>
                                <select name="pagelimit" class="form-control select" id="pagelimit" style="width:100px">
                                <option value=""></option>
                                <option value="1">1</option>
                                <option value="2">2</option>
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
                            class="controls"> <?php echo form_submit('submit_report', $this->lang->line("submit"), 'class="btn btn-primary monthlysale_report"'); ?> 
                        </div>
                    </div>
                </div>
                    <!-- <?php echo form_close(); ?> -->
                <div class="clearfix"></div>
                <div class="table-responsive" id="MonthlyData1">
                    <table style="display:none ">
                        <tr>
                            <th colspan="5"><span style="font-size: 20px"><?=lang('category_wise_monthly_sales_report')?></span></th>
                         </tr>
                         <tr>                            
                            <th colspan="5"><span style="font-size: 20px"><?=lang('month')?>:</th>
                            <th colspan="2"><span style="font-size: 20px" id="month" ></span></th>                            
                         </tr>
                        </table> 

                    <table id="MonthlyData"
                           class="table table-bordered table-hover table-striped table-condensed reports-table">
                       <thead>
                      
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
        $(document).on('click', '.monthlysale_report', function () {
            $url = '<?=admin_url('reports/get_monthly_reports');?>';
            GetData($url);
        });

        $(document).on('change', '#pagelimit', function () {
            $url = '<?=admin_url('reports/get_monthly_reports');?>';
            GetData($url);
        });

        $url = '<?=admin_url('reports/get_monthly_reports');?>';
        GetData($url);
        
    });

function date_format_conversion($date){

     var dateAr = $date.split('-');
     var newDate = dateAr[1] + '-' + dateAr[0];     
     return newDate;

} 

function GetData($url) {               
    var start = $('#start_date').val();
    if (start =='') { 
        start  = '<?php echo date('Y-m');?>'; 
    } 
    //var end = $('#end_date').val();
    var warehouse_id = $('#warehouse_id').val();
    var pagelimit = $('#pagelimit').val();    
    if (start !='') {                  
        $('#start_date,#end_date').css('border-color', '#ccc'); 
        /*$('#kot').siblings(".select2-container").find('.select2-choice').css('border-color', '#ccc');*/
                  $.ajax({
                        type: 'POST',
                        url: $url,
                        data: {start: start, warehouse_id: warehouse_id,pagelimit:pagelimit},
                        dataType: "json",
                        cache: false,
                        success: function (data) {
                                $('#MonthlyData').empty(); 
                                if(data.monthly_reports =='empty' || data == 'error'){ 
                                 $('#MonthlyData').append('<tbody><tr><td colspan="6" class="dataTables_empty"><?= lang('sEmptyTable') ?></td></tr></tbody>');
                                }else{
                                    $("#month").text(date_format_conversion(start));
                                    $('#MonthlyData').append(data.monthly_reports);
                                    $columnCnt = $("#MonthlyData > tbody > tr:first > td").length;
                                    $total = '<tr class="text-right" style="font-weight:bold"><td class="text-center">Total</td>';
                                    $grandTotal = '<tr class="text-right" style="font-weight:bold"><td class="text-center">Grand Total</td>';
                                    $total +='<td></td><td></td>';
                                    $grandTotal +='<td></td><td></td>';
                                    for(i=4;i<=$columnCnt;i++){
                                        $val = sumOfColumns($('#MonthlyData'), i);
                                        console.log($val)
                                    $total +='<td>'+formatMoney($val)+'</td>';
                                    $grandTotal += '<td>'+formatMoney($val)+'</td>';
                                    }
                                    $total +='</tr>';
                                    $grandTotal +='</tr>';
                                    $('#MonthlyData').append($total);
                                    // $('#MonthlyData').append($grandTotal);
                                    $('.dataTables_paginate').html(data.pagination);
                                }
                            }

                       
                    });
    }
    else{
        if (start == '') {                    
            $('#start_date').css('border-color', 'red');
        }else{
           $('#start_date').css('border-color', '#ccc'); 
        }
        if (end == '') {                    
            $('#end_date').css('border-color', 'red');
        }else{
            $('#end_date').css('border-color', '#ccc'); 
        }
      
        return false;    
    }  
}   

$(document).ready(function(){
    $("#form").slideDown();
        $('#end_date').datepicker({
        dateFormat: "yy-mm-dd" ,
        maxDate:  0,      
    });
    $('#start_date').datepicker(
                    {
                        dateFormat: "yy-mm",
                        maxDate:  0, 
                        changeMonth: true,
                        changeYear: true,
                        showButtonPanel: true,
                        onClose: function(dateText, inst) {


                            function isDonePressed(){
                                return ($('#ui-datepicker-div').html().indexOf('ui-datepicker-close ui-state-default ui-priority-primary ui-corner-all ui-state-hover') > -1);
                            }

                            if (isDonePressed()){
                                var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                                var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                                $(this).datepicker('setDate', new Date(year, month, 1)).trigger('change');
                                
                                 $('.date-picker').focusout()//Added to remove focus from datepicker input box on selecting date
                            }
                        },
                        beforeShow : function(input, inst) {

                            inst.dpDiv.addClass('month_year_datepicker')

                            if ((datestr = $(this).val()).length > 0) {
                                year = datestr.substring(datestr.length-4, datestr.length);
                                month = datestr.substring(0, 2);
                                $(this).datepicker('option', 'defaultDate', new Date(year, month-1, 1));
                                $(this).datepicker('setDate', new Date(year, month-1, 1));
                                $(".ui-datepicker-calendar").hide();
                            }
                        }
                    })
});

function sumOfColumns(table, columnIndex) {

    var tot = 0;
    table.find("tr").children("td:nth-child(" + columnIndex + ")")
    .each(function() {
        $this = $(this);
        $v = $this.text().replace(/[^0-9.]/gi, ''); 
        console.log($v);
        
        if ($.isNumeric($v)) {
            tot += parseFloat($v);
        }
    });

    return tot.toFixed(6);
}
 $(".excel_report").click(function(){

      $("#MonthlyData1").table2excel({

        // exclude CSS class

        exclude: ".noExl",

        name: "Worksheet Name",

        filename: "Monthly Reports" //do not include extension

      });

    });
</script>
<style>
    .ui-datepicker-calendar,button.ui-datepicker-current {
    display: none;
    }
</style>
