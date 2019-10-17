<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<link rel="stylesheet" href="<?= $assets ?>styles/jquery-ui.css">
  <script src="<?= $assets ?>js/jquery-ui.js"></script>
  
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-heart"></i><?= lang('customer_postpaid_bills_report'); ?> <?php
            if ($this->input->post('start_date')) {
                echo "From " . $this->input->post('start_date') . " to " . $this->input->post('end_date');
            }
            ?>
        </h2>        
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <div id="form">
                <!-- <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'id' => 'pos-item-sale-report');
             echo admin_form_open("reports/recipe", $attrib);?> -->

                    <div class="row">  
                        

                        <div class="col-sm-4">
                            <div class="form-group">
                                <?= lang("start_date", "start_date"); ?>
                                <?php echo form_input('start_date', (isset($_POST['start_date']) ? $_POST['start_date'] : ""), 'class="form-control " id="start_date"'); ?>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <?= lang("end_date", "end_date"); ?>
                                <?php echo form_input('end_date', (isset($_POST['end_date']) ? $_POST['end_date'] : ""), 'class="form-control " id="end_date"'); ?>
                            </div>
                        </div>
                         <!--<div class="col-sm-4">
                            <div class="form-group">
                                <?= lang("day_range", "day_range"); ?>
                                <?php echo form_input('day_range', (isset($_POST['day_range']) ? $_POST['day_range'] : ""), 'class="form-control " id="day_range"'); ?>
                            </div>
                        </div>-->
                         
                             <input type="hidden" value="<?=@$customer_id?>" name="customer_id"id="customer_id">                            
                            
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
                    
                    <div class="form-group">
                        <div
                            class="controls"> <?php echo form_submit('submit_report', $this->lang->line("submit"), 'class="btn btn-primary postpaid_bills_report"'); ?> 
                        </div>
                    </div>
                    </div>
                </div>
                    <!-- <?php echo form_close(); ?> -->
                
                
                <div class="clearfix"></div>
                <div class="table-responsive">
                    <table id="MonthlyData"
                           class="table table-bordered table-hover table-striped table-condensed reports-table">
                       <thead>
                            <th><?= $this->lang->line("bill_number"); ?></th>
                            <th><?= $this->lang->line("credit_amount"); ?></th>
                            <th><?= $this->lang->line("amount_paid"); ?></th>
                            <th><?= $this->lang->line("amount_payable"); ?></th>
                            <th><?= $this->lang->line("due_date"); ?></th>
                            <th><?= $this->lang->line("paid_on"); ?></th>
                            <th><?= $this->lang->line("exceeded_days"); ?></th>
                            <th><?= $this->lang->line("status"); ?></th>
                            <th><?= $this->lang->line("make_payment"); ?></th>
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
        $(document).on('click', '.postpaid_bills_report', function () {
            $url = '<?=admin_url('reports/getCustomerPostpaid_bills');?>';
            GetData($url);
        });

        $(document).on('change', '#pagelimit', function () {
            $url = '<?=admin_url('reports/getCustomerPostpaid_bills');?>';
            GetData($url);
        });
        $url = '<?=admin_url('reports/getCustomerPostpaid_bills');?>';
        GetData($url);
        
    });

function GetData($url) {               
    var start = $('#start_date').val();
    var end = $('#end_date').val();
    var day_range = $('#day_range').val();
    var customer_id = $('#customer_id').val();
    var warehouse_id = $('#warehouse_id').val();
    var pagelimit = $('#pagelimit').val();    
   // if (start !='' && end !='' ) {                  
        $('#start_date,#end_date').css('border-color', '#ccc'); 
        /*$('#kot').siblings(".select2-container").find('.select2-choice').css('border-color', '#ccc');*/
                  $.ajax({
                        type: 'POST',
                        url: $url,
                        data: {start_date: start, end_date: end, warehouse_id: warehouse_id,pagelimit:pagelimit,day_range:day_range,customer_id:customer_id},
                        dataType: "json",
                        cache: false,
                        success: function (data) {
                                 $('#MonthlyData tbody').empty(); 
                                if(data.postpaid_bills =='empty' || data == 'error'){ 
                                 $('#MonthlyData').append('<tbody><tr><td colspan="6" class="dataTables_empty"><?= lang('sEmptyTable') ?></td></tr></tbody>');
                                }else{
                                   $url = '<?=admin_url()?>';
                                    $tbody = '<tbody>';
                                    $tbody += '<tr>';
                                    $.each(data.postpaid_bills,function(n,v){
                                        console.log(v)
                                        $tbody += '<td>'+v.bill_number+'</td>';
                                        $tbody += '<td>'+v.credit_amount+'</td>';
                                        $tbody += '<td>'+v.amount_paid+'</td>';
                                        $tbody += '<td>'+v.amount_payable+'</td>';
                                        $tbody += '<td>'+v.due_date+'</td>';
                                        $tbody += '<td>'+v.paid_on+'</td>';
                                        $tbody += '<td>'+v.exceeded_days+'</td>';
                                        $tbody += '<td>'+v.status+'</td>';
                                        if(v.amount_payable!=0){
                                            $tbody += '<td><a style="text-decoration: none" href="'+$url+'reports/postpaid_payment/'+v.company_id+'/'+v.bill_id+'" data-toggle="modal" data-target="#myModal"><div class="text-center"><span class="payment_status label label-warning"><?=lang('make_payment')?></span></div></a></td>';

                                        }else{
                                            $tbody += '<td></td>';
                                        }
                                        $tbody += '</tr>';
                                        $tbody += '<tr>';
                                    });
                                    $tbody += '</tr>';
                                    $tbody += '<tr style="font-weight: bold;">';
                                    $tbody += '<td style="width:186px;">Total Credit Amount:</td><td>'+data.total_amount.bill.credit_amount+'</td>';
                                    //$tbody += '</tr>';
                                    //$tbody += '<tr style="font-weight: bold;">';
                                    $tbody += '<td>Total Paid:</td><td>'+data.total_amount.bill.amount_paid+'</td>';
                                    //$tbody += '</tr>';
                                    //$tbody += '<tr style="font-weight: bold;">';
                                    $tbody += '<td>Total Payable:</td><td>'+data.total_amount.bill.amount+'</td>';
                                    $tbody += '</tr>';
                                    $tbody += '</tbody>';
                                    $('#MonthlyData').append($tbody);
                                    $('.dataTables_paginate').html(data.pagination);
                                    
                                    //if(data.customer_details !='empty' || data.customer_details != 'error'){
                                    //    $("#customer_id").empty();
                                    //    $("#customer_id").append("<option value=''>Select</option>");
                                    //    $.each(data.customer_details, function (a,b){
                                    //     $("#customer_id").append('<option value=' + b.customer_id + '>' + b.customer_name + '</option>');
                                    //   });
                                    //} 
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

    //var tot = 0;
    //table.find("tr").children("td:nth-child(" + columnIndex + ")")
    //.each(function() {
    //    $this = $(this);
    //    $v = $this.text().replace(/[^0-9.]/gi, ''); 
    //    console.log($v);
    //    
    //    if ($.isNumeric($v)) {
    //        tot += parseFloat($v);
    //    }
    //});
    //
    //return tot.toFixed(6);
}

</script>
