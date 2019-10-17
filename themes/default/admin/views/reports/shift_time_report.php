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
        <h2 class="blue"><i class="fa-fw fa fa-heart"></i><?= lang('shift_wise_report'); ?> <?php
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


                        <!-- <div class="col-sm-3">
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
                        </div> -->
						
						<!-- <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label" for="user"><?= lang("created_by"); ?></label>
                                <?php                                
                                foreach ($users as $user) {
                                    $us[$user->id] = $user->first_name . " " . $user->last_name;
                                }
                                echo form_dropdown('user', $us, (isset($_POST['user']) ? $_POST['user'] : ""), 'class="form-control" id="user" data-placeholder="' . $this->lang->line("select") . " " . $this->lang->line("user") . '"');
                                ?>
                            </div>
                        </div> -->

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
                        <div class="col-md-3">
                            <div class="form-group">
                                <?= lang("shift_time", "shift_time"); ?>
                                <?php
                                $shift['all'] = lang('all');
                                foreach ($shifttime as $shifts) {
                                    $shift[$shifts->id] = $shifts->name;
                                }
                                echo form_dropdown('shift', $shift, '', 'class="form-control" data-placeholder="' . lang("select") . ' ' . lang("shift") . '" id="shift" style="width:100%;" ');
                                ?>
                                
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
                    <table id="SlRData"
                           class="table table-bordered table-hover table-striped table-condensed reports-table">
                       <thead>
                            <tr>
                                <th><?= lang("s.no"); ?>
                                <th><?= lang("date"); ?></th>
                                <th><?= lang("username"); ?></th>
                                <th><?= lang("warehouse"); ?></th>
                                <th><?= lang("bill_no"); ?></th>
                                <th><?= lang("sale_type"); ?></th>
                                <th><?= lang("time"); ?></th>
                                <th><?= lang("cash"); ?></th>
                                <th><?= lang("credit_card"); ?></th>
                                <th><?= lang("credit"); ?></th>
                                <th><?= lang("foreign_exchange"); ?></th>
                                <!-- <th><?= lang("usd"); ?></th> -->
                                <th><?= lang("return_balance"); ?></th>
                                <th><?= lang("amt"); ?></th>                      
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
            $url = '<?=admin_url('reports/get_shifttime_reports');?>';
            GetData($url);
        });

        $(document).on('change', '#pagelimit', function () {
            $offset = false;
            $url = '<?=admin_url('reports/get_shifttime_reports');?>';
            GetData($url);
        });

           $url = '<?=admin_url('reports/get_shifttime_reports');?>';            
           GetData($url);
    });

 function GetData($url){               
    var start = $('#start_date').val();
    var end = $('#end_date').val();
    var user = $('#user').val();
    var shift = $('#shift').val();
    var pagelimit = $('#pagelimit').val();
    var defalut_currency = "<?php echo $Settings->default_currency;?>";            
    var for_currency  ="<?php echo $this->site->getExchangeCurrency($Settings->default_currency)?>";
// alert(for_currency);
    if (start !='' && end !='' && user !='' ) {                  
        $('#start_date,#end_date').css('border-color', '#ccc'); 
        $('#user').siblings(".select2-container").find('.select2-choice').css('border-color', '#ccc');
                  $.ajax({
                        type: 'POST',
                        url: $url,
                        data: {start: start, end: end, user: user,pagelimit:pagelimit,shift:shift, defalut_currency:defalut_currency,},
                        dataType: "json",
                        success: function (data) {
                             $('#SlRData > tbody').empty();
                            if(data.shift_report =='empty' || data.shift_report == 'error'){ 
                               $('#SlRData > tbody').append('<tr><td colspan="5" class="dataTables_empty"><?= lang('sEmptyTable') ?></td></tr>');
                            }
                            else{
                                $('.dataTables_paginate').html(data.pagination);
                                var total_cash  = 0;
                                var total_cc = 0;
                                var total_credit = 0;
                                var total_foreign = 0;
                                var toforusd = 0;
                                var total_amt = 0;
                                var total_bal = 0;

                                $.each(data.shift_report, function (a,b) 
                                { //alert(b.day);
                                    var cash  = 0;
                                    var cc = 0;
                                    var credit = 0;
                                    var foreign = 0;
                                    var forusd = 0;
                                    var amt = 0;
                                    var bal = 0;
                                    var $row_index = 1;

                                    $('#SlRData > tbody').append('<tr class="text-right" style="font-weight:bold" class="text-right"><td>Shift</td><td class="text-center">'+b.name+'</td><td  colspan="2" class="text-center">From '+b.start_time+' - To '+b.end_time+' </td></tr>');                    
                                     

                                     $.each(b.user, function (c,d) 
                                     {  
                                        cash  += parseFloat(d.Cash);
                                        cc  += parseFloat(d.Credit_Card);
                                        credit  += parseFloat(d.credit);
                                        foreign += parseFloat(d.ForEx);
                                        forusd+= parseFloat(d.For_Ex);
                                        /*usd += parseFloat(d.USD);*/
                                        //amt += (parseFloat(d.Credit_Card) + parseFloat(d.Cash) + parseFloat(d.For_Ex) -parseFloat(d.return_balance));
                                        amt += (parseFloat(d.Credit_Card) + parseFloat(d.Cash) + parseFloat(d.credit) + parseFloat(d.For_Ex)-parseFloat(d.return_balance));
                                        bal += parseFloat(d.return_balance);

                                        total_cash  += parseFloat(d.Cash);
                                        total_cc  += parseFloat(d.Credit_Card);
                                        total_credit  += parseFloat(d.credit);
                                        total_foreign += parseFloat(d.ForEx);

                                        toforusd +=parseFloat(d.For_Ex);
                                        
                                        /*total_usd += parseFloat(d.USD);*/
                                        total_amt += (parseFloat(d.Credit_Card) + parseFloat(d.Cash) + parseFloat(d.credit) + parseFloat(d.For_Ex)-parseFloat(d.return_balance));
                                        total_bal += parseFloat(d.return_balance);

                                      $('#SlRData > tbody').append('<tr class="text-right"><td>'+$row_index+'</td><td class="text-center">'+d.bill_date+'</td><td class="text-center">'+d.username+'</td><td class="text-center">'+d.warehouse+'</td><td class="text-center">'+d.Bill_No+'</td><td class="text-center">'+d.bill_type+'</td><td class="text-center">'+d.bill_time+'</td><td>'+formatMoney(d.Cash)+'</td><td>'+formatMoney(d.Credit_Card)+'</td><td>'+formatMoney(d.credit)+'</td><td>'+formatMoney(d.ForEx,for_currency)+'</td><td>'+formatMoney(d.return_balance)+'</td><td>'+formatMoney((parseFloat(d.Credit_Card) + parseFloat(d.Cash) + parseFloat(d.For_Ex)- parseFloat(d.return_balance )))+'</td></tr>');                     $row_index++;
                                     });  
                                      $('#SlRData > tbody').append('<tr style="font-weight:bold" class="text-right"><td colspan="7" class="text-center">Day Total: </td><td >'+formatMoney(cash)+'</td><td>'+formatMoney(cc)+'</td><td>'+formatMoney(credit)+'</td><td>'+formatMoney(foreign,for_currency)+'('+formatMoney(forusd)+')</td><td>'+formatMoney(bal)+'</td><td>'+formatMoney(amt)+'</td></tr>');             
                                });

                                 $('#SlRData > tbody').append('<tr style="font-weight:bold" class="text-right"><td colspan="7" class="text-center">Grand Total: </td><td>'+formatMoney(total_cash)+'</td><td>'+formatMoney(total_cc)+'</td><td>'+formatMoney(total_credit)+'</td><td>'+formatMoney(total_foreign,for_currency)+'('+formatMoney(toforusd)+')</td><td>'+formatMoney(total_bal)+'</td><td>'+formatMoney(total_amt)+'</td></tr>');
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