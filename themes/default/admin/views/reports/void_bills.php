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
        <h2 class="blue"><i class="fa-fw fa fa-heart"></i><?= lang('void_bill'); ?> <?php
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
                            class="controls"> <?php echo form_submit('submit_report', $this->lang->line("submit"), 'class="btn btn-primary voidmail"'); ?> </div>
                    </div>
                </div>
                    <!-- <?php echo form_close(); ?> -->
                <div class="clearfix"></div>

                <div class="table-responsive">
                    <table id="VoidBillData" class="table table-bordered table-hover table-striped table-condensed reports-table">
                        <thead>
                        <tr>
                            <th><?= lang("ref_no"); ?></th>
                            <th><?= lang("bill_date"); ?></th>
                            <th><?= lang("branch"); ?></th>
                            <th><?= lang("cancel_reason"); ?></th>
                            <th><?= lang("cerated_by"); ?></th>
                            <th><?= lang("cancelled"); ?></th>
                            <th><?= lang("kot"); ?></th>
                            <th><?= lang("item"); ?></th>
                            <th><?= lang("variant"); ?></th>
                            <th><?= lang("qty"); ?></th>
                            <th><?= lang("value"); ?></th>
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
        $(document).on('click', '.voidmail', function () {
            $url = '<?=admin_url('reports/get_voidbills_reports');?>';
            GetData($url);
        });

        $(document).on('change', '#pagelimit', function () {
            $url = '<?=admin_url('reports/get_voidbills_reports');?>';
            GetData($url);
        });
            $url = '<?=admin_url('reports/get_voidbills_reports');?>';
            GetData($url);        
    });

function GetData($url){ 
            
            var start_date = $('#start_date').val();
            var end_date = $('#end_date').val();
            var warehouse_id = $('#warehouse_id').val();
            var pagelimit = $('#pagelimit').val();
            if (start_date !='' && end_date !='' ) {
                 $('#start_date,#end_date').css('border-color', '#ccc'); 
                  $.ajax({
                        type: 'POST',
                        url: $url,
                        data: {start_date: start_date, end_date: end_date, warehouse_id: warehouse_id,pagelimit:pagelimit},
                        dataType: "json",
                        success: function (data) {
                             
                            if(data.void_bills =='empty'){ 

                                $('#VoidBillData > tbody').empty(); 
                                $('#VoidBillData > tbody').append('<tr><td colspan="8" class="dataTables_empty"><?= lang('sEmptyTable') ?></td></tr>');
                                
                            }
                            else if(data.void_bills == 'error'){
                                
                            }
                            else{
                                $('.dataTables_paginate').html(data.pagination);
                                $('#VoidBillData > tbody').empty();
                                var total  = 0;   
                                $.each(data.void_bills, function (a,b) 
                                {
                                    total  += parseFloat(b.bill_amt);

                                     $('#VoidBillData > tbody').append('<tr class="text-center"><td>'+b.bill_number+'</td><td>'+b.date+'</td><td>'+b.branch+'</td><td>'+b.order_item_cancel_note+'</td><td>'+b.created_by+'</td><td>'+b.Canceled+'</td><td>'+b.kot+'</td><td>'+b.recipename+'</td><td>'+b.variant+'</td><td>'+b.quantity+'</td><td class="text-right">'+formatMoney(b.bill_amt)+'</td></tr>');
                                });

                                 $('#VoidBillData > tbody').append('<tr style="font-weight:bold" ><td><strong>Total: </strong></td><td class="text-right" colspan="10">'+formatMoney(total)+'</td></tr>');
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
</script>