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
        <h2 class="blue"><i class="fa-fw fa fa-heart"></i><?= lang('open_close_registers'); ?> <?php
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
               <!--       <div class="col-md-3">
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
                        </div> -->

                        <div class="col-sm-3">
                            <div class="form-group">
                             <?= lang("register_type", "register_type"); ?>
                             <select class="form-control col-sm-2" name="open_close" id="open_close">
                                <option value="">Select</option>
                                <option value="open">Open Register</option>
                                <option value="close">Close Register</option>
                            </select>                               
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <?= lang("start_date", "start_date"); ?>
                                <?php echo form_input('start_date', (isset($_POST['start_date']) ? $_POST['start_date'] : ""), 'class="form-control "  autocomplete="off" id="start_date"'); ?>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <?= lang("end_date", "end_date"); ?>
                                <?php echo form_input('end_date', (isset($_POST['end_date']) ? $_POST['end_date'] : ""), 'class="form-control " autocomplete="off"  id="end_date"'); ?>
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
                            class="controls"> <?php echo form_submit('submit_report', $this->lang->line("submit"), 'class="btn btn-primary popular_analy"'); ?> 
                        </div>
                    </div>
                </div>
                    <!-- <?php echo form_close(); ?> -->
                <div class="clearfix"></div>
                <div class="table-responsive">
                    <table id="opencloseData"
                           class="table table-bordered table-hover table-striped table-condensed reports-table">
                           <thead></thead>
                       
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
        $(document).on('click', '.popular_analy', function () {
            $url = '<?=admin_url('reports/get_open_close_reports');?>';
            GetData($url);
        });

        $(document).on('change', '#pagelimit', function () {
            $url = '<?=admin_url('reports/get_open_close_reports');?>';
            GetData($url);
        });
    });

function GetData($url){               
    var start_date = $('#start_date').val();
    var end_date = $('#end_date').val();
    var open_close = $('#open_close').val();
    var pagelimit = $('#pagelimit').val();   
    /*var warehouse_id = $('#warehouse_id').val();*/  
    if (start_date !='' && end_date !='' && open_close != '') {                  
        $('#start_date,#end_date').css('border-color', '#ccc'); 
       $('#open_close').siblings(".select2-container").find('.select2-choice').css('border-color', '#ccc');
                  $.ajax({
                        type: 'POST',
                        url: $url,
                        data: {start_date: start_date, end_date: end_date, open_close:open_close,pagelimit:pagelimit},
                        dataType: "json",
                        success: function (data) {
                            $('#opencloseData > tbody').empty();  
                            $('#opencloseData > thead').empty();  
                            if(data.open_close_data =='empty' || data.open_close_data == 'error'){
                            $('#opencloseData > thead').append('<tr><th><?= lang("s.no"); ?></th><th><?= lang("date"); ?></th><th><?= lang("created_by"); ?></th><th><?= lang("recived_from"); ?></th><th><?= lang("amt"); ?></th></tr>'); 
                            $('#opencloseData > tbody').append('<tr><td colspan="5" class="dataTables_empty"><?= lang('sEmptyTable') ?></td></tr>');    
                            }
                            else{
                                var $row_index = 1;
                                $('.dataTables_paginate').html(data.pagination);
                                if(open_close == 'open')
                                {                                      
                                    $('#opencloseData > thead').append('<tr><th><?= lang("s.no"); ?></th><th><?= lang("date"); ?></th><th><?= lang("created_by"); ?></th><th><?= lang("recived_from"); ?></th><th><?= lang("amt"); ?></th></tr>');
                                    $('#opencloseData > tbody').empty(); 
                                    $.each(data.open_close_data, function (a,b) 
                                    {   
                                        $('#opencloseData > tbody').append('<tr class="text-center"><td>'+$row_index+'</td>><td>'+b.open_payment_date+'</td>><td>'+b.creared_by+'</td><td>'+b.recived_from+'</td><td>'+b.cash_in_hand+'</td></tr>');
                                    $row_index++;
                                    });
                                }
                                else {
                                    
                                    $('#opencloseData > thead').append('<tr><th><?= lang("s.no"); ?></th><th><?= lang("date"); ?></th><th><?= lang("recived_from"); ?></th><th><?= lang("amt"); ?></th><th><?= lang("created_by"); ?></th></tr>');
                                    $('#opencloseData > tbody').empty(); 
                                    $.each(data.open_close_data, function (a,b) 
                                    {   
                                        $('#opencloseData > tbody').append('<tr class="text-center"><td>'+$row_index+'</td><td>'+b.close_payment_date+'</td>><td>'+b.recived_from+'</td><td>'+b.close_amt+'</td><td>'+b.creared_by+'</td></tr>');
                                    $row_index++;
                                    });
                               }                               
                           } 
                        }
                    });
    }
    else{
        if (start_date == '') {                    
            $('#start_date').css('border-color', 'red');
        }else{
           $('#start_date').css('border-color', '#ccc'); 
        }
        if (end_date == '') {                    
            $('#end_date').css('border-color', 'red');
        }else{
            $('#end_date').css('border-color', '#ccc'); 
        }
        if (open_close == '') {  
            $('#open_close').siblings(".select2-container").find('.select2-choice').css('border-color', 'red');
        } 
        else
        {
            $('#open_close').siblings(".select2-container").find('.select2-choice').css('border-color', '#ccc');
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

var data = $('#open_close').select2('data');

      $("#opencloseData").table2excel({

        // exclude CSS class

        exclude: ".noExl",

        name: "Worksheet Name",

        filename: data.text //do not include extension

      });

    });
</script>