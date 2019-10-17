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
        <h2 class="blue"><i class="fa-fw fa fa-heart"></i><?= lang('day_wise'); ?> <?php
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
                <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'id' => 'pos-item-sale-report'); 
            // echo admin_form_open("api/v1/posreports/daywise");?>

                    <div class="row">   

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
                        <input type="hidden" class="api_key" value="<?=@$_GET['api-key']?>">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <?= lang("date", "date"); ?>
                                <?php echo form_input('start_date', (isset($_POST['start_date']) ? $_POST['start_date'] : ""), 'class="form-control " autocomplete="off" id="start_date"'); ?>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label for="category">Show</label><br>
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
                        <div class="col-sm-2 form-group"  style="top: 28px;">
                            <div class="">
                                <div class="controls"> <?php echo form_submit('submit_report', $this->lang->line("submit"), 'class="btn btn-primary daywise"'); ?> </div>
                                
                            </div>
                        </div>
                    </div>
                    
                </div>
                    <?php //echo form_close(); ?> 
                <div class="clearfix"></div>

                <div class="table-responsive">
                    
                    <table id="DAYData" class="table table-bordered table-hover table-striped table-condensed reports-table">
                        <thead>
                        <th>
                            <th><?= lang("billno"); ?></th>
                            <th><?= lang("branch"); ?></th>
                            <th><?= lang("vat"); ?></th>
                            <th><?= lang("discount"); ?></th>
                            <th><?= lang("bill_amt"); ?></th>
                        </th>
                  
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
        $("#form").slideDown();
        $('#start_date').datepicker({
            dateFormat: "yy-mm-dd" ,
            maxDate:  0,
        });
        $("#start_date").datepicker("setDate", new Date());
        $(document).on('click', '.pagination a',function(e){
            e.preventDefault();
            $url = $(this).attr("href");
            GetData($url);
            return false;
        });
        $(document).on('click', '.daywise', function () {
            $url = '<?=site_url('api/v1/posreports/get_DaySummaryreports');?>';
            GetData($url);
        });

        $(document).on('change', '#pagelimit', function () {
            $url = '<?=site_url('api/v1/posreports/get_DaySummaryreports');?>';
            GetData($url);
        });
        $url = '<?=site_url('api/v1/posreports/get_DaySummaryreports');?>';
        GetData($url);
      
    });
function GetData($url){
    var start_date = $('#start_date').val();
            var warehouse_id = $('#warehouse_id').val();
            var pagelimit = $('#pagelimit').val();
			var printlist = $('#printlist').val();  
			
            var api_key = $('.api_key').val();
            if (start_date !='') {
                 $('#start_date').css('border-color', '#ccc'); 
                  $.ajax({
                        type: 'POST',
                        url: $url,
                        data: {'api-key':api_key,start_date: start_date,warehouse_id: warehouse_id,pagelimit:pagelimit, printlist:printlist},
                        dataType: "json",
                        cache: false,
                        success: function (data) {
                            $('#DAYData > tbody').empty();
                            
                            if(data.daysummary =='empty' || data.daysummary == 'error'){ 
                                 $('#DAYData').append('<tbody><tr><td colspan="6" class="dataTables_empty"><?= lang('sEmptyTable') ?></td></tr></tbody>');
                            }else{
                                $('#DAYData').empty();
                                $('#DAYData').append(data.daysummary);
                                $columnCnt = $("#DAYData > tbody > tr:first > td").length;
                                $total = '<tr style="font-weight:bold"><td><strong>Total</strong></td>';
                                $grandTotal = '<tr><td><strong>Grand Total</strong></td>';
                                $total +='<td></td><td></td>';
                                $grandTotal +='<td></td><td></td>';
                                for(i=4;i<=$columnCnt;i++){
                                    $val = sumOfColumns($('#DAYData'), i);
                                $total +='<td>'+formatMoney($val)+'</td>';
                                $grandTotal += '<td>'+formatMoney($val)+'</td>';
                                }
                                $total +='</tr>';
                                $grandTotal +='</tr>';
                                
                                // $('#DAYData').append('<tfoot>'+$total+$grandTotal+'</tfoot>');
                                $('#DAYData').append('<tfoot>'+$total+'</tfoot>');
                                
                                $('.dataTables_paginate').html(data.pagination);
                                
                                
                            
                            
                            //$table = $('#DAYData').dataTable();
                            
                            }
                           


                                
                            }
                        /*}*/
                    });  
            }
            else{
                           

                if (start_date =='') {                    
                    $('#start_date').css('border-color', 'red');
                }else{
                   $('#start_date').css('border-color', '#ccc'); 
                }

                
                return false;     
            }  
}



function sumOfColumns(table, columnIndex) {
    var tot = 0;
    table.find("tr").children("td:nth-child(" + columnIndex + ")")
    .each(function() {
        $this = $(this);
        $v = $this.text().replace(/[^0-9.]/gi, ''); 
        if ($.isNumeric($v)) {
            tot += parseFloat($v);
        }
    });
    return tot;
}

</script>
<?php
$v = "";

if ($this->input->post('start_date')) {
    $v .= "&start_date=" . $this->input->post('start_date');
}
if ($this->input->post('warehouse_id')) {
    $v .= "&warehouse_id=" . $this->input->post('warehouse_id');
}
?>
<script>
$(".excel_report").click(function(){
    

      $("#DAYData").table2excel({

        // exclude CSS class

        exclude: ".noExl",

        name: "Worksheet Name",

        filename: "Day Wise Sales Summary" //do not include extension

      });

    });

//    $(document).ready(function () {
//        oTable = $('#DAYData').dataTable({
//            "aaSorting": [[1, "asc"]],
//            
//            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
//            "iDisplayLength": <?= $Settings->rows_per_page ?>,
//            'bProcessing': true, 'bServerSide': true,
//            'sAjaxSource': '<?= site_url('api/v1/posreports/get_DaySummaryapi/v1/posreports/?v=1'.$v) ?>',
//            'fnServerData': function (sSource, aoData, fnCallback) {
//                //"columns": aoData.thead,
//                aoData.push({
//                    "name": "<?= $this->security->get_csrf_token_name() ?>",
//                    "value": "<?= $this->security->get_csrf_hash() ?>"
//                });
//                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
//            },
//            "columns": [
//            { "data": "name" },
//            { "data": "position" },
//            { "data": "office" },
//            { "data": "extn" },
//            { "data": "start_date" },
//            { "data": "salary" }
//        ],
//            "aoColumns": [null,{
//                "mRender": currencyFormat,
//                "bSearchable": false
//            }, {"mRender": currencyFormat, "bSearchable": false}, {
//                "mRender": currencyFormat,
//                "bSearchable": false
//            }, {"mRender": currencyFormat, "bSearchable": false}, {"mRender": currencyFormat,"bSortable": false}],
//            "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
//                var purchases = 0, total = 0, paid = 0, balance = 0;
//                for (var i = 0; i < aaData.length; i++) {
//                    purchases += parseFloat(aaData[aiDisplay[i]][4]);
//                    total += parseFloat(aaData[aiDisplay[i]][5]);
//                    paid += parseFloat(aaData[aiDisplay[i]][6]);
//                    balance += parseFloat(aaData[aiDisplay[i]][7]);
//                }
//               // var nCells = nRow.getElementsByTagName('th');
//                //nCells[4].innerHTML = decimalFormat(parseFloat(purchases));
//                //nCells[5].innerHTML = currencyFormat(parseFloat(total));
//                //nCells[6].innerHTML = currencyFormat(parseFloat(paid));
//               // nCells[7].innerHTML = currencyFormat(parseFloat(balance));
//            }
//        }).fnSetFilteringDelay().dtFilter([
//            {column_number: 0, filter_default_label: "[<?=lang('company');?>]", filter_type: "text", data: []},
//            {column_number: 1, filter_default_label: "[<?=lang('name');?>]", filter_type: "text", data: []},
//            {column_number: 2, filter_default_label: "[<?=lang('phone');?>]", filter_type: "text", data: []},
//            {column_number: 3, filter_default_label: "[<?=lang('email_address');?>]", filter_type: "text", data: []},
//        ], "footer");
//    });
//    function currencyFormat_1(x) {
//        console.log(x)
//    return '<div class="text-right">'+formatMoney(x != null ? x : 0)+'</div>';
//}

</script>