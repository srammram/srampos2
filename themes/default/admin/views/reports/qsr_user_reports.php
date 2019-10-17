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

<script>
    /*$(document).ready(function () {
        oTable = $('#KOTData').dataTable({
            "aaSorting": [[0, "desc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= admin_url('reports/getRecipeReport/?v=1' . $v) ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                nRow.id = aData[9];
                nRow.className = (aData[5] > 0) ? "invoice_link2" : "invoice_link2 warning";
                return nRow;
            },
            "aoColumns": [null, {"mRender": currencyFormat}, {"mRender": currencyFormat}],
            "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
                var gtotal = 0, paid = 0, balance = 0;
                for (var i = 0; i < aaData.length; i++) {
                    gtotal += parseFloat(aaData[aiDisplay[i]][1]);
                    paid += parseFloat(aaData[aiDisplay[i]][2]);
                    
                }
                var nCells = nRow.getElementsByTagName('th');
                nCells[1].innerHTML = currencyFormat(parseFloat(gtotal));
                nCells[2].innerHTML = currencyFormat(parseFloat(paid));                
            }
        }).fnSetFilteringDelay().dtFilter([
        {column_number: 0, filter_default_label: "[<?=lang('recipe_name');?>]", filter_type: "text", data: []},
            
        ], "footer");
    });*/
</script>
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
        <h2 class="blue"><i class="fa-fw fa fa-heart"></i><?= lang('user_report'); ?> <?php
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


                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label" for="user"><?= lang("group"); ?></label>
                                <?php
                                $gr[""] = lang('select').' '.lang('group');
                                foreach ($groups as $group) {
                                    $gr[$group->id] = $group->name;
                                }
                                echo form_dropdown('group', $gr, (isset($_POST['group']) ? $_POST['group'] : ""), 'class="form-control" id="group" data-placeholder="' . $this->lang->line("select") . " " . $this->lang->line("group") . '"');
                                ?>
                            </div>
                        </div>
						
						<div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label" for="user"><?= lang("created_by"); ?></label>
                                <?php
                                // $us[""] = lang('select').' '.lang('user');
                                foreach ($users as $user) {
                                    $us[$user->id] = $user->first_name . " " . $user->last_name;
                                }
                                echo form_dropdown('user', $us, (isset($_POST['user']) ? $_POST['user'] : ""), 'class="form-control" id="user" data-placeholder="' . $this->lang->line("select") . " " . $this->lang->line("user") . '"');
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
                            class="controls"> <?php echo form_submit('submit_report', $this->lang->line("submit"), 'class="btn btn-primary user_report"'); ?> 
                        </div>
                        
                    </div>
                </div>
                    <!-- <?php echo form_close(); ?> -->
                <div class="clearfix"></div>
                <div class="table-responsive">
                    <table id="USERData"
                           class="table table-bordered table-hover table-striped table-condensed reports-table">
                       <thead>
                            <tr>
                                <th><?= lang("s.no"); ?>
                                <th><?= lang("date"); ?></th>
                                <th><?= lang("user"); ?></th>
                                <th><?= lang("order_type"); ?></th>                                
                                <th><?= lang("bill_no"); ?></th>
                                <th><?= lang("grand_total"); ?></th>                            
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
            $url_seg = $url.split('/');
            $count = $url_seg.length-1;           
            $offset = (isNaN($url_seg[$count]))?false:$url_seg[$count];
            GetData($url);
            return false;
        });
        $(document).on('click', '.user_report', function () {
            $offset = false;
            $url = '<?=admin_url('reports/get_qsruser_reports');?>';
            GetData($url);
        });

        $(document).on('change', '#pagelimit', function () {
            $offset = false;
            $url = '<?=admin_url('reports/get_qsruser_reports');?>';
            GetData($url);
        });

           $url = '<?=admin_url('reports/get_qsruser_reports');?>';            
           GetData($url);
    });

 function GetData($url){               
    var start = $('#start_date').val();
    var end = $('#end_date').val();
    var user = $('#user').val();
    var group = $('#group').val();
    var pagelimit = $('#pagelimit').val();
    if (start !='' && end !='' && user !='' ) {                  
        $('#start_date,#end_date').css('border-color', '#ccc'); 
        $('#user').siblings(".select2-container").find('.select2-choice').css('border-color', '#ccc');
                  $.ajax({
                        type: 'POST',
                        url: $url,
                        data: {start: start, end: end, user: user,pagelimit:pagelimit,group:group},
                        dataType: "json",
                        success: function (data) {
                            $('#USERData > tbody').empty();  
                             $('#USERData > thead').empty(); 
                            $('#USERData > thead').append('<tr><th><?= lang("s.no"); ?><th><?= lang("date"); ?></th><th><?= lang("user"); ?></th><th><?= lang("order_type"); ?></th><th><?= lang("bill_no"); ?></th><th><?= lang("grand_total"); ?></th></tr>'); 

                            if(data.user_report =='empty'){
                             
                            $('#USERData > tbody').append('<tr><td colspan="6" class="dataTables_empty"><?= lang('sEmptyTable') ?></td></tr>');    
                            }
                            else if(data.user_report == 'error'){                               
                            }
                            else{
                                    $('.dataTables_paginate').html(data.pagination);
                                    var amt = 0;      
                                    var grand_total = 0;
                                    var $row_index = ($offset) ?parseInt($offset)+parseInt(1):1;
                                    $.each(data.user_report, function (a,b) 
                                    {
                                        if(b.tax_type !=0){

                                          grand_total = parseFloat(b.total)-parseFloat(b.total_discount)+parseFloat(b.total_tax);

                                          amt += parseFloat(b.total)-parseFloat(b.total_discount)+parseFloat(b.total_tax);
                                        }
                                        else{
                                            grand_total =parseFloat(b.total)-parseFloat(b.total_discount);

                                            amt += parseFloat(b.total)-parseFloat(b.total_discount)
                                        }
                                                                          
                                        $('#USERData > tbody').append('<tr class="text-center"><td>'+$row_index+'</td><td >'+b.billdate+'</td><td >'+b.username+'</td><td>'+b.order_type+'</td><td>'+b.bill_number+'</td><td  class="text-right">'+formatMoney(grand_total)+'</td></tr>');                 
                                        $row_index++;
                                    });

                                     $('#USERData > tbody').append('<tr style="font-weight:bold"><td ><strong>Grand Total:</strong></td><td colspan="5"   class="text-right">'+formatMoney(amt)+'</td></tr>');
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
        if (user == '') {  
            $('#user').siblings(".select2-container").find('.select2-choice').css('border-color', 'red');
        } 
        else
        {
            $('#user').siblings(".select2-container").find('.select2-choice').css('border-color', '#ccc');
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

      $("#USERData").table2excel({

        // exclude CSS class

        exclude: ".noExl",

        name: "Worksheet Name",

        filename: "User Report" //do not include extension

      });

    });
</script>