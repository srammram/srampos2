<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
  <link rel="stylesheet" href="<?= $assets ?>styles/jquery-ui.css">
  <script src="<?= $assets ?>js/jquery-ui.js"></script>
<?php

$v = "";

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
        <h2 class="blue"><i class="fa-fw fa fa-heart"></i><?= lang('tax_reports'); ?> <?php
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
                        <input type="hidden" class="api_key" value="<?=@$_GET['api-key']?>">
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
                            class="controls"> <?php echo form_submit('submit_report', $this->lang->line("submit"), 'class="btn btn-primary taxreports"'); ?> </div>
                    </div>
                </div>
                    <!-- <?php echo form_close(); ?> -->
                <div class="clearfix"></div>

                <div class="table-responsive">
                    <table id="TaxData" class="table table-bordered table-hover table-striped table-condensed reports-table">
                        <thead>
                        <tr>
                            <th><?= lang("s.no"); ?></th>
                            <th><?= lang("branch"); ?></th>
                            <th><?= lang("date"); ?></th>
                            <th><?= lang("bill_no"); ?></th>
                            <th><?= lang("tax"); ?></th>
                            <th><?= lang("bill_value"); ?></th>
                            <th><?= lang("tax_value"); ?></th>
                            <th><?= lang("total"); ?></th>
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
            $url_seg = $url.split('/');
            $count = $url_seg.length-1;           
            $offset = (isNaN($url_seg[$count]))?false:$url_seg[$count];
            GetData($url);
            return false;
        });
        $(document).on('click', '.taxreports', function () {
            $offset = false;
            $url = '<?=site_url('api/v1/posreports/get_tax_reports');?>';
            GetData($url);
        });

        $(document).on('change', '#pagelimit', function () {
            $offset = false;
            $url = '<?=site_url('api/v1/posreports/get_tax_reports');?>';
            GetData($url);
        });
            $url = '<?=site_url('api/v1/posreports/get_tax_reports');?>';
            GetData($url);        
    });


        function GetData($url){ 
            var start_date = $('#start_date').val();
            var end_date = $('#end_date').val();
            var warehouse_id = $('#warehouse_id').val();
            var pagelimit = $('#pagelimit').val();
            var api_key = $('.api_key').val();
            if (start_date !='' && end_date !='' ) {
                 $('#start_date,#end_date').css('border-color', '#ccc'); 
                  $.ajax({
                        type: 'POST',
                        url: $url,
                        data: {'api-key':api_key,start_date: start_date, end_date: end_date, warehouse_id: warehouse_id,pagelimit:pagelimit},
                        dataType: "json",
                        success: function (data) {
                             
                            if(data.tax =='empty'){ 

                                $('#TaxData > tbody').empty(); 
                                $('#TaxData > tbody').append('<tr><td colspan="8" 2class="dataTables_empty"><?= lang('sEmptyTable') ?></td></tr>');
                                
                            }
                            else if(data.tax == 'error'){
                                
                            }
                            else{
                                $('.dataTables_paginate').html(data.pagination);
                                $('#TaxData > tbody').empty();
                                var total  = 0;   
                                var total_tax  = 0;   
                                var total_bill  = 0;
                                var $row_index = ($offset) ?parseInt($offset)+parseInt(1):1;
                                $.each(data.tax, function (a,b) 
                                {
                                    var Taxname; 
                                    
                                    var billAmt; 
                                    
                                    if(b.tax_type != 0)
                                    {
                                      Taxname = 'Exclusive -('+b.Taxname+')';
                                      billAmt = (parseFloat(b.total)-parseFloat(b.total_discount));
                                    }
                                    else
                                    {
                                        billAmt = (parseFloat(b.total)-parseFloat(b.total_discount)-parseFloat(b.total_tax));

                                        Taxname = 'Inclusive -('+b.Taxname+')';

                                    }
                                    total_bill += parseFloat(billAmt);
                                    total  += parseFloat(b.final_amt);
                                    total_tax  += parseFloat(b.total_tax);

                                     $('#TaxData > tbody').append('<tr class="text-center"><td>'+$row_index+'</td><td>'+b.branch+'</td><td>'+b.tax_date+'</td><td>'+b.bill_number+'</td><td>'+Taxname+'</td><td class="text-right">'+formatMoney(billAmt)+'</td><td class="text-right">'+formatMoney(b.total_tax)+'</td><td class="text-right">'+formatMoney(b.final_amt)+'</td></tr>');
                                    $row_index++;
                                });

                                 $('#TaxData > tbody').append('<tr style="font-weight:bold"><td>Total</td><td colspan="4"></td><td class="text-right" >'+formatMoney(total_bill)+'</td><td class="text-right" >'+formatMoney(total_tax)+'</td><td class="text-right">'+formatMoney(total)+'</td></tr>');
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
$(".excel_report").click(function(){

      $("#TaxData").table2excel({

        // exclude CSS class

        exclude: ".noExl",

        name: "Worksheet Name",

        filename: "Tax Reports" //do not include extension

      });

    });
</script>