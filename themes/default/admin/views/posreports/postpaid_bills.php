<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
  <link rel="stylesheet" href="<?= $assets ?>styles/jquery-ui.css">
  <script src="<?= $assets ?>js/jquery-ui.js"></script>
<?php

$v = "";

if ($this->input->post('warehouse')) {
    $v .= "&warehouse=" . $this->input->post('warehouse');
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
        <h2 class="blue"><i class="fa-fw fa fa-heart"></i><?= lang('postpaid_bills_report'); ?> <?php
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
             echo admin_form_open("api/v1/posreports/recipe", $attrib);?> -->

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
                        <input type="hidden" class="api_key" value="<?=@$_GET['api-key']?>">

                       <!-- <div class="col-sm-4">
                            <div class="form-group">
                                <?= lang("start_date", "start_date"); ?>
                                <?php echo form_input('start_date', (isset($_POST['start_date']) ? $_POST['start_date'] : ""), 'class="form-control " id="start_date"'); ?>
                            </div>
                        </div>-->
                       <!-- <div class="col-sm-4">
                            <div class="form-group">
                                <?= lang("end_date", "end_date"); ?>
                                <?php echo form_input('end_date', (isset($_POST['end_date']) ? $_POST['end_date'] : ""), 'class="form-control " id="end_date"'); ?>
                            </div>
                        </div>-->
                         <!--<div class="col-sm-4">
                            <div class="form-group">
                                <?= lang("day_range", "day_range"); ?>
                                <?php echo form_input('day_range', (isset($_POST['day_range']) ? $_POST['day_range'] : ""), 'class="form-control " id="day_range"'); ?>
                            </div>
                        </div>-->
                         <div class="col-sm-3">
                            <div class="form-group">
                             <?= lang("search_customer", "search_customer"); ?>
                             <select class="form-control col-sm-2" name="customer_id" id="customer_id">
                                <option value="">Select</option>
                            </select>                               
                            </div>
                        </div>
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
                            class="controls"> <?php echo form_submit('submit_report', $this->lang->line("submit"), 'class="btn btn-primary postpaid_bills_report"'); ?> 
                        </div>
                    </div>
                </div>
                    <!-- <?php echo form_close(); ?> -->
                <div class="clearfix"></div>
                <div class="table-responsive">
                    <table id="MonthlyData"
                           class="table table-bordered table-hover table-striped table-condensed reports-table">
                       <thead>
                            <th><?=lang('customer_name')?></th>
                            <th><?=lang('Amount')?></th>
                            <th><?=lang('bill_details')?></th>
                            <th><?=lang('make_payment')?></th>
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
            window.location.href = "<?=site_url('api/v1/posreports/getSalesReport/pdf/?v=1'.$v)?>";
            return false;
        });
        $('#xls').click(function (event) {
            event.preventDefault();
            window.location.href = "<?=site_url('api/v1/posreports/getSalesReport/0/xls/?v=1'.$v)?>";
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
        $(document).on('click', '.postpaid_bills_report', function () {
            $url = '<?=site_url('api/v1/posreports/postpaid_bills_report');?>';
            GetData($url);
        });

        $(document).on('change', '#pagelimit', function () {
            $url = '<?=site_url('api/v1/posreports/postpaid_bills_report');?>';
            GetData($url);
        });
        $url = '<?=site_url('api/v1/posreports/postpaid_bills_report');?>';
        GetData($url);
        
    });

function GetData($url) {               
    var start = $('#start_date').val();
    var end = $('#end_date').val();
    var day_range = $('#day_range').val();
    var customer_id = $('#customer_id').val();
    var warehouse_id = $('#warehouse_id').val();
    var pagelimit = $('#pagelimit').val();
    var api_key = $('.api_key').val();
   // if (start !='' && end !='' ) {                  
        $('#start_date,#end_date').css('border-color', '#ccc'); 
        /*$('#kot').siblings(".select2-container").find('.select2-choice').css('border-color', '#ccc');*/
                  $.ajax({
                        type: 'POST',
                        url: $url,
                        data: {'api-key':api_key,start_date: start, end_date: end, warehouse_id: warehouse_id,pagelimit:pagelimit,day_range:day_range,customer_id:customer_id},
                        dataType: "json",
                        cache: false,
                        success: function (data) {
                                 $('#MonthlyData tbody').empty(); 
                                if(data.postpaid_bills =='empty' || data == 'error'){ 
                                 $('#MonthlyData').append('<tbody><tr><td colspan="6" class="dataTables_empty"><?= lang('sEmptyTable') ?></td></tr></tbody>');
                                }else{
                                   $url = '<?=site_url()?>';
                                    $tbody = '<tbody>';
                                    $tbody += '<tr>';
                                    $.each(data.postpaid_bills,function(n,v){
                                        console.log(v)
                                        $tbody += '<td>'+v.customer_name+'</td>';
                                        $tbody += '<td>'+v.amount+'</td>';
                                        $tbody += '<td><a href="'+$url+'api/v1/posreports/customer_postpaid_bills/'+v.customer_id+'" ><?=lang('bill_details')?></a></td>';
                                        if(v.amount!=0){
                                            $tbody += '<td><a style="text-decoration: none" href="'+$url+'api/v1/posreports/postpaid_payment/'+v.customer_id+'" data-toggle="modal" data-target="#myModal"><div class="text-center"><span class="payment_status label label-warning"><?=lang('make_payment')?></span></div></a></td>';

                                            

                                        }else{
                                            $tbody += '<td></td>';
                                        }
                                        $tbody += '</tr>';
                                        $tbody += '<tr>';
                                    });
                                    $tbody += '</tr>';
                                    $tbody += '</tbody>';
                                    $('#MonthlyData').append($tbody);
                                    $('.dataTables_paginate').html(data.pagination);
                                    
                                    if(data.customer_details !='empty' || data.customer_details != 'error'){
                                        $("#customer_id").empty();
                                        $("#customer_id").append("<option value=''>Select</option>");
                                        $.each(data.customer_details, function (a,b){
                                         $("#customer_id").append('<option value=' + b.customer_id + '>' + b.customer_name + '</option>');
                                       });
                                    } 
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

      $("#MonthlyData").table2excel({

        // exclude CSS class

        exclude: ".noExl",

        name: "Worksheet Name",

        filename: "Monthly Reports" //do not include extension

      });

    });
</script>