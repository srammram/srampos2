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
        <h2 class="blue"><i class="fa-fw fa fa-heart"></i><?= lang('order_time_report'); ?> <?php
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
                       <div class="col-sm-3">
                            <div class="form-group">
                                <?= lang("Type", "type"); ?></label>
                                <select name="printlist" class="form-control select" id="printlist" style="width:100px">
                                    <option value="0">All</option>
                                    <option value="2">Include</option>
                                    <option value="1">Exclude</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div
                            class="controls"> <?php echo form_submit('submit_report', $this->lang->line("submit"), 'class="btn btn-primary kot_report"'); ?> 
                        </div>
                    </div>
                </div>
                    <!-- <?php echo form_close(); ?> -->
                <div class="clearfix"></div>
                <div class="table-responsive">
                    <table id="OrderTimingData"
                           class="table table-bordered table-hover table-striped table-condensed reports-table">
                       <thead>
                      <tr>
                            <th><?= lang("s.no"); ?></th>
                            <th><?= lang("branch"); ?></th>
                            <th><?= lang("order_id"); ?></th>
                            <th><?= lang("order_no"); ?></th>
                            <th><?= lang("recipe_name"); ?></th>
                            <th><?= lang("table_name"); ?></th>
                            <th><?= lang("start_time"); ?></th>
                            <th><?= lang("end_time"); ?></th>
                            <th><?= lang("default_preparation_time"); ?></th>
                            <th><?= lang("preparation_time"); ?></th>
                            <th><?= lang("time_different"); ?></th>
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
        $(document).on('click', '.kot_report', function () {
            $offset = false;
            $url = '<?=site_url('api/v1/posreports/get_ordertiming_details');?>';
            GetData($url);
        });

        $(document).on('change', '#pagelimit', function () {
            $offset = false;
            $url = '<?=site_url('api/v1/posreports/get_ordertiming_details');?>';
            GetData($url);
        });
            $url = '<?=site_url('api/v1/posreports/get_ordertiming_details');?>';
            GetData($url);        
    });

function GetData($url){              
    var start_date = $('#start_date').val();
    var end_date = $('#end_date').val();
    var pagelimit = $('#pagelimit').val();   
    var warehouse_id = $('#warehouse_id').val();
	var printlist = $('#printlist').val();  
	
    var api_key = $('.api_key').val();
    if (start_date !='' && end_date !='') {                  
        $('#start_date,#end_date').css('border-color', '#ccc'); 
        $.ajax({
            type: 'POST',
            url: $url,
            data: {'api-key':api_key,start_date: start_date, end_date: end_date,warehouse_id: warehouse_id,pagelimit:pagelimit, printlist:printlist},
            dataType: "json",
            success: function (data) {
            $('#OrderTimingData > tbody').empty(); 
            if(data.ordertime =='empty' || data.ordertime == 'error'){
                
                $('#OrderTimingData > tbody').append('<tr><td colspan="11" class="dataTables_empty"><?= lang('sEmptyTable') ?></td></tr>');    
                }
                else{ 
                    $('#OrderTimingData > tbody').empty();
                    var $row_index = ($offset) ?parseInt($offset)+parseInt(1):1;
                    $.each(data.ordertime, function (a,b) 
                    {
                        $class = '';$timediff='';
                    //$default_preparation_time = (b.preparation_time/60).toFixed(1);
                    //$preparedTime = (b.prepared_time/60).toFixed(1);
                    //console.log($preparedTime+'='+$default_preparation_time+'=='+($preparedTime - $default_preparation_time))
                    if (b.default_preparation_time!=0) {
                        
                        $class = (parseInt(b.default_preparation_time) > parseInt(b.preparedTime))?'less-time':'time-exceeded';
                    b.default_preparation_time = b.default_preparation_time+' mins';
                        //$timediff = ($preparedTime - $default_preparation_time).toFixed(1)+' mins';
                    }
                    
                      //$('#OrderTimingData > tbody').append('<tr class="text-center"><td>'+b.id+'</td><td>'+b.reference_no+'</td>><td>'+b.recipe_name+'</td><td>'+b.table_name+'</td><td>'+b.time_started+'</td><td>'+b.time_end+'</td><td>'+$default_preparation_time+' mins</td><td class="'+$class+'">'+$preparedTime+' mins</td><td>'+$timediff+'</td></tr>');
                      $('#OrderTimingData > tbody').append('<tr class="text-center"><td>'+$row_index+'</td><td>'+b.branch+'</td><td>'+b.id+'</td><td>'+b.reference_no+'</td>><td>'+b.recipe_name+'</td><td>'+b.table_name+'</td><td>'+b.time_started+'</td><td>'+b.time_end+'</td><td>'+b.default_preparation_time+'</td><td class="'+$class+'">'+b.preparedTime+' mins</td><td>'+b.timediff+' mins</td></tr>');
                      $row_index++;
                    });
                    $('.dataTables_paginate').html(data.pagination);
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
</script>
<style>
td.less-time {
    background: #008000 !important;
}
td.time-exceeded {
    background: #F00000 !important;
}
</style>