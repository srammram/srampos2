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
        <h2 class="blue"><i class="fa-fw fa fa-heart"></i><?= lang('kot_details_report'); ?> <?php
            if ($this->input->post('start_date')) {
                echo "From " . $this->input->post('start_date') . " to " . $this->input->post('end_date');
            }
            ?>
        </h2>

        <div class="box-icon">
            <ul class="btn-tasks">
               
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

                        <div class="col-sm-3">
                            <div class="form-group">
                             <?= lang("report_type", "report_type"); ?>
                             <select class="form-control col-sm-2" name="kot" id="kot">
                                <!-- <option value="">Select</option> -->
                                <option value="kot_details">KOT Details Reports</option>
                                <option value="kot_pending">Pending Kot Reports</option>
                                <option value="kot_cancel">Cancel Kot Reports</option>
                            </select>                               
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
						
						<div class="col-md-3">
                            <div class="form-group">
                                <?= lang("varient", "varient"); ?>
                                <?php
                                $va['0'] = lang('all');
                                foreach ($varients as $varient) {
                                    $va[$varient->id] = $varient->name;
                                }
                                echo form_dropdown('varient_id', $va, '', 'class="form-control" data-placeholder="' . lang("select") . ' ' . lang("varient") . '" id="varient_id" style="width:100%;" ');
                                ?>
                                
                            </div>
                        </div>
						
                         <div class="col-sm-3">
                            <div class="form-group">
                                <label for="category">Show</label>
                                <select name="pagelimit" class="form-control select" id="pagelimit" style="width:100%">
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
                            class="controls"> <?php echo form_submit('submit_report', $this->lang->line("submit"), 'class="btn btn-primary kot_report"'); ?> 
                        </div>
                       
                    </div>
                </div>
                    <!-- <?php echo form_close(); ?> -->
                <div class="clearfix"></div>
                <div class="table-responsive">
                    <table id="KOTData"
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
        $(document).on('click', '.kot_report', function () {
            $url = '<?=admin_url('reports/get_kotdetailsreports');?>';
            GetData($url);
        });

        $(document).on('change', '#pagelimit', function () {
            $url = '<?=admin_url('reports/get_kotdetailsreports');?>';
            GetData($url);
        });
           $url = '<?=admin_url('reports/get_kotdetailsreports');?>';            
           GetData($url);
    });

function GetData($url){               
    var start_date = $('#start_date').val();
    var end_date = $('#end_date').val();
    var kot = $('#kot').val();
    var warehouse_id = $('#warehouse_id').val();
	var varient_id = $('#varient_id').val();
    var pagelimit = $('#pagelimit').val();
    if (start_date !='' && end_date !='' && kot !='' ) {                  
        $('#start_date,#end_date').css('border-color', '#ccc'); 
        $('#kot').siblings(".select2-container").find('.select2-choice').css('border-color', '#ccc');
                  $.ajax({
                        type: 'POST',
                        url: $url,
                        data: {start_date: start_date, end_date: end_date, kot: kot, warehouse_id: warehouse_id,varient_id: varient_id,pagelimit:pagelimit},
                        dataType: "json",
                        success: function (data) {
                            $('#KOTData > tbody').empty();  
                            $('#KOTData > thead').empty();  
                            if(data.kotdetails =='empty' || data.kotdetails == 'error'){
                            $('#KOTData > thead').append('<tr><th><?= lang("s.no"); ?></th><th><?= lang("kot_no"); ?></th><th><?= lang("user"); ?></th><th><?= lang("table"); ?></th><th><?= lang("item"); ?></th><th><?= lang("qty"); ?></th><th><?= lang("value"); ?></th><th><?= lang("bill_no"); ?></th></tr>'); 
                            $('#KOTData > tbody').append('<tr><td colspan="8" class="dataTables_empty"><?= lang('sEmptyTable') ?></td></tr>');    
                            }
                            else{
                                var $row_index = 1;
                                $('.dataTables_paginate').html(data.pagination);
                                if(kot == 'kot_cancel')
                                {                                      
                                    $('#KOTData > thead').append('<tr><th><?= lang("s.no"); ?></th><th><?= lang("kot_no"); ?></th><th><?= lang("kot_date"); ?></th><th><?= lang("user"); ?></th><th><?= lang("recipe"); ?></th><th><?= lang("Quantity"); ?></th><th><?= lang("variant"); ?></th><th><?= lang("table"); ?></th><th><?= lang("Cancel_type"); ?></th><th><?= lang("status"); ?></th></tr>');
                                    $('#KOTData > tbody').empty(); 
                                    $.each(data.kotdetails, function (a,b) 
                                    {   
                                        $('#KOTData > tbody').append('<tr class="text-center"><td>'+$row_index+'</td><td>'+b.id+'</td><td>'+b.date+'</td>><td>'+b.username+'</td><td>'+b.recipename+'</td><td>'+b.quantity+'</td><td>'+b.variant+'</td><td>'+b.table_name+'</td><td>'+b.item_cancel_type+'</td><td>'+b.order_item_cancel_note+'</td></tr>');
                                    $row_index++;
                                    });
                                }
                                else if(kot == 'kot_pending'){
                                    var pen_qty = 0;
                                    var pen_tot = 0;

                                    $('#KOTData > tbody,#KOTData > thead').empty(); 
                                    $('#KOTData > thead').append('<tr><th><th><?= lang("s.no"); ?></th><?= lang("kot_no"); ?></th><th><?= lang("date"); ?></th><th><?= lang("table_no"); ?></th><th><?= lang("item12"); ?></th><th><?= lang("variant"); ?></th><th><?= lang("user"); ?></th><th><?= lang("qty"); ?></th><th><?= lang("amount"); ?></th></tr>');
                                     $.each(data.kotdetails, function (c,d) 
                                        {   
                                            pen_qty +=parseFloat(d.quantity);
                                            pen_tot +=parseFloat(d.subtotal);
                                            var table_name ='';
                                        if(d.table_name == null)
                                        {
                                            table_name ='N/A';
                                        }        
                                        else{
                                            table_name =d.table_name;
                                        } 

                                            /*pen_tot +=parseFloat(d.subtotal);*/


                                            $('#KOTData > tbody').append('<tr class="text-center"><td>'+$row_index+'</td><td>'+d.id+'</td><td >'+d.Orderdate+'</td><td>'+table_name+'</td><td>'+d.recipename+'</td><td>'+d.variant+'</td><td>'+d.username+'</td><td class="text-right">'+formatQuantity(d.quantity)+'</td><td class="text-right">'+formatMoney(d.subtotal)+'</td></tr>');
                                        $row_index++;
                                        });
                                     $('#KOTData > tbody').append('<tr style="font-weight:bold"><td>Total</td><td colspan="6"></td><td  class="text-right">'+formatQuantity(pen_qty)+'</td><td class="text-right">'+formatMoney(pen_tot)+'</td></tr>');
                               }
                               else
                                {  

                                    $('#KOTData > thead').append('<tr><th><?= lang("s.no"); ?></th><th><?= lang("kot_no"); ?></th><th><?= lang("kot_date"); ?></th><th><?= lang("time"); ?></th><th><?= lang("bill_no"); ?></th><th><?= lang("user"); ?></th><th><?= lang("table"); ?></th><th><?= lang("order_type"); ?></th><th><?= lang("steward"); ?></th><th><?= lang("item"); ?></th><th><?= lang("variant"); ?></th><th><?= lang("qty"); ?></th><th><?= lang("value"); ?></th></tr>');
                                    var grd_amt = 0;
                                    var round = 0;
                                    var grand_qty = 0;
                                    $.each(data.kotdetails, function (e,f) 
                                    { 
                                       round = parseFloat(f.round);
                                       var amt = 0;
                                       var qty = 0;
                                       $.each(f.user, function (g,h) 
                                       {  

                                        //amt += parseFloat(h.Bill_amt) -parseFloat(h.item_discount) - parseFloat(h.off_discount)- parseFloat(h.input_discount) + parseFloat(h.tax);

                                        amt += parseFloat(h.Bill_amt);

                                        var  rate = parseFloat(h.Bill_amt) -parseFloat(h.item_discount) - parseFloat(h.off_discount)- parseFloat(h.input_discount) + parseFloat(h.tax);

                                        grd_amt += parseFloat(h.Bill_amt);
                                        qty +=parseFloat(h.quantity);
                                        grand_qty +=parseFloat(h.quantity);
                                        var table ='';
                                        if(h.table_name == null)
                                        {
                                            table ='N/A';
                                        }        
                                        else{
                                            table =h.table_name;
                                        }                      
                                         $('#KOTData > tbody').append('<tr class="text-center"><td>'+$row_index+'</td><td>'+h.kitchenno+'</td><td>'+h.kot_date+'</td><td>'+h.kot_time+'</td><td>'+h.Bill_No+'</td><td>'+h.username+'</td><td>'+table+'</td><td>'+h.order_type+'</td><td>'+h.steward+'</td><td>'+h.item+'</td><td>'+h.variant+'</td><td class="text-right">'+formatQuantity(h.quantity)+'</td><td class="text-right">'+formatMoney(h.Bill_amt)+'</td></tr>');      
                                        $row_index++;
                                       });

                                     $('#KOTData > tbody').append('<tr style="font-weight:bold"><td>User Total:</td><td colspan="10"></td><td  class="text-right">'+formatQuantity(qty)+'</td><td class="text-right">'+formatMoney(amt)+'</td></tr>');

                                    
                                  });


                                  /*if(round!= 0) 
                                  {
                                    $('#KOTData > tbody').append('<tr style="font-weight:bold"  class="text-right"><td class="text-left" colspan="10">Round :</td><td>'+formatMoney(round)+'</td></tr>');
                                  }*/

                                  $('#KOTData > tbody').append('<tr style="font-weight:bold"><td><strong>Grand Total:</strong></td><td colspan="10"></td><td  class="text-right">'+formatQuantity((grand_qty))+'</td><td class="text-right">'+formatMoney(grd_amt)+'</td></tr>');    
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
        if (kot == '') {  
            $('#kot').siblings(".select2-container").find('.select2-choice').css('border-color', 'red');
        } 
        else
        {
            $('#kot').siblings(".select2-container").find('.select2-choice').css('border-color', '#ccc');
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

var data = $('#kot').select2('data');


      $("#KOTData").table2excel({

        // exclude CSS class

        exclude: ".noExl",

        name: "Worksheet Name",

        filename: data.text //do not include extension

      });

    });
</script>