<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
  <link rel="stylesheet" href="<?= $assets ?>styles/jquery-ui.css">
  <script src="<?= $assets ?>js/jquery-ui.js"></script>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-heart"></i><?= lang('bbq_report'); ?> <?php
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
                             <?= lang("summary_items", "summary_items"); ?>
                             <select class="form-control col-sm-2" name="summary_items" id="summary_items">                                
                                <option value="bbq_summary">BBQ Summary </option>
                                <option value="bbq_bills">BBQ Bill Wise</option>
                                <option value="bbq_items">BBQ Items Wise</option>
                            </select>                               
                            </div>
                        </div>
                        <input type="hidden" class="api_key" value="<?=@$_GET['api-key']?>">
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
                            class="controls"> <?php echo form_submit('submit_report', $this->lang->line("submit"), 'class="btn btn-primary submit_itemreport"'); ?> </div>
                             
                    </div>
                </div>
                    <!-- <?php echo form_close(); ?> -->
                <div class="clearfix"></div>

                <div class="table-responsive">
                    <table id="SlRData" class="table table-bordered table-hover table-striped table-condensed reports-table">
                        <thead>
                        <tr>
                            <th><?= lang("s_no"); ?></th>
                            <th><?= lang("date&time"); ?></th>
							<th><?= lang("customer_name"); ?></th>
                            <th><?= lang("bill_no"); ?></th>
                            <th><?= lang("no_of_adult"); ?></th>
                            <th><?= lang("no_of_child"); ?></th>
                            <th><?= lang("no_of_kid"); ?></th>
                            <th><?= lang("total"); ?></th> 
                            <th><?= lang("tax"); ?></th> 
                            <th><?= lang("discount"); ?></th> 
                            <th><?= lang("grand_total"); ?></th> 
                            <th><?= lang("payment_type"); ?></th> 
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
      /*  $('#pdf').click(function (event) {
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
        });*/
        $(document).on('click', '.pagination a',function(e){
            e.preventDefault();
            $url = $(this).attr("href");
            $url_seg = $url.split('/');
            $count = $url_seg.length-1;           
            $offset = (isNaN($url_seg[$count]))?false:$url_seg[$count];
            GetData($url);
            return false;
        });

        $(document).on('click', '.submit_itemreport', function () {
            $offset = false;
            $url = '<?=site_url('api/v1/posreports/get_bbqrports');?>';
            GetData($url);
        });

        $(document).on('change', '#pagelimit', function () {
            $offset = false;
            $url = '<?=site_url('api/v1/posreports/get_bbqrports');?>';
            GetData($url);
        });

            $url = '<?=site_url('api/v1/posreports/get_bbqrports');?>';            
            GetData($url);

    });
function GetData($url) {    
            /*var recipe = $('#suggest_recipe').val();*/
            var start_date = $('#start_date').val();
            var end_date = $('#end_date').val();
            var warehouse_id = $('#warehouse_id').val();
            var summary_items = $('#summary_items').val();
            var pagelimit = $('#pagelimit').val();    
			var printlist = $('#printlist').val();  
			
            var api_key = $('.api_key').val();
            if (start_date !='' && end_date !='' ) {
                 $('#start_date,#end_date').css('border-color', '#ccc'); 
                  $.ajax({
                        type: 'POST',
                        url: $url,                    
                        data: {'api-key':api_key,start_date: start_date, end_date: end_date, warehouse_id: warehouse_id,summary_items:summary_items,pagelimit:pagelimit, printlist:printlist},
                        dataType: "json",
                         success: function (data) {
                             
                            if(data.bbqrports =='empty'){ 
                                 $('#SlRData > tbody').empty();  
                                $('#SlRData > tbody').append('<tr><td colspan="12" class="dataTables_empty"><?= lang('sEmptyTable') ?></td></tr>');
                                
                            }
                            else if(data.bbqrports == 'error'){
                                
                            }
                            else{
                                // bbq_items
                                $('.dataTables_paginate').html(data.pagination);
                                $('#SlRData > tbody').empty();                                                              
                                if(summary_items == 'bbq_summary')
                                {                                     

                                  $('#SlRData > thead').empty();                                 

                                  $('#SlRData > thead').append('<tr><th><?= lang("s.no"); ?></th><th><?= lang("date&time"); ?></th><th><?= lang("customer_name"); ?></th><th><?= lang("bill_no"); ?></th><th><?= lang("no_of_adult"); ?></th><th><?= lang("no_of_child"); ?></th><th><?= lang("no_of_kid"); ?></th><th><?= lang("total"); ?></th><th><?= lang("tax"); ?></th><th><?= lang("discount"); ?></th><th><?= lang("grand_total"); ?></th><th><?= lang("payment_type"); ?></th></tr>');
                                  var $row_index = ($offset) ?parseInt($offset)+parseInt(1):1;
                                  $.each(data.bbqrports, function (a,b) 
                                    {
                                       

                                         $('#SlRData > tbody').append('<tr class="text-center"><td>'+$row_index+'</td><td>'+b.bill_date+'</td><td>'+b.customer_name+'</td><td>'+b.bill_number+'</td><td>'+b.no_of_adult+'</td><td>'+b.no_of_child+'</td><td>'+b.no_of_kids+'</td><td>'+formatMoney(b.total)+'</td><td class="text-right">'+formatMoney(b.total_tax)+'</td><td class="text-right">'+formatMoney(b.total_discount)+'</td><td class="text-right">'+formatMoney(b.grand_total)+'</td><td>'+b.paid_by+'</td></tr>');
                                    $row_index++;
                                    });
                                } 
                                else if(summary_items == 'bbq_bills')
                                {                                     

                                 $('#SlRData > thead').empty();
                                    $('#SlRData > thead').append('<tr><th><?= lang("s.no"); ?></th><th><?= lang("item"); ?></th><th><?= lang("Pcs_Per_condo"); ?></th><th><?= lang("Ordered_Qty_Condo"); ?></th><th><?= lang("Ordered_Qty_Pcs"); ?></th><th><?= lang("Return_Qty_Condo(Pcs)"); ?></th><th><?= lang("Total_Consumed_Qty_Condo(Pcs)"); ?></th><th><?= lang("Avg_Cost_Price_Per_unit"); ?></th><th><?= lang("Cost_Price_for_ordered_Qty"); ?></th><th><?= lang("Return_Amount/Price"); ?></th><th></tr>');                                   

                                    var grand_cost_price =0;
                                    var grand_retrun_price =0;
                                    var grand_profit =0;

                                    $.each(data.bbqrports, function (a,b) 
                                    {
                                        var $row_index = ($offset) ?parseInt($offset)+parseInt(1):1;

                                        var cost_price =0;
                                        var retrun_price =0;
                                        var profit =0;

                                        $.each(b.user, function (c,d) 
                                        {            
                                            cost_price +=(parseFloat(d.orderpcs*d.avgpcsrate));
                                            retrun_price +=(parseFloat(d.returnqty_condopcs*d.avgpcsrate));
                                            // profit += (parseFloat(d.profit));

                                            grand_cost_price +=(parseFloat(d.orderpcs*d.avgpcsrate));
                                            grand_retrun_price +=(parseFloat(d.returnqty_condopcs*d.avgpcsrate));
                                            // grand_profit += (parseFloat(d.profit));

                                            $('#SlRData > tbody').append('<tr><td>'+$row_index+'</td><td class="text-center">'+d.recipe_name+'</td><td class="text-center">'+d.pcs_percondo+'</td><td class="text-center">'+d.order_qtycondo+'</td><td class="text-center">'+d.orderpcs+'</td><td class="text-center">'+d.returnqty_condopcs+'</td><td class="text-center">'+(d.orderpcs -d.returnqty_condopcs)+'</td><td class="text-right">'+formatMoney(d.avgpcsrate)+'</td><td class="text-right">'+formatMoney(d.orderpcs*d.avgpcsrate)+'</td><td class="text-right">'+formatMoney(d.returnqty_condopcs*d.avgpcsrate)+'</td></tr>');


                                           /* $('#SlRData > tbody').append('<tr><td>'+$row_index+'</td><td class="text-center">'+d.item+'</td><td class="text-center">'+d.pcs_percondo+'</td><td class="text-center">'+d.order_qtycondo+'</td><td class="text-center">'+d.ordered_qtypcs+'</td><td class="text-center">'+d.returnqty_condopcs+'</td><td class="text-center">'+d.totalconsum_qtycondopcs+'</td><td class="text-right">'+formatMoney(d.AvgCost_PricePerunit)+'</td><td class="text-right">'+formatMoney(d.costper_orderqty)+'</td><td class="text-right">'+formatMoney(d.Return_Price)+'</td><td class="text-right">'+formatMoney(d.profit)+'</td></tr>');*/
                                            $row_index++;
                                        });

                                         $('#SlRData > tbody').append('<tr style="font-weight:bold"><td colspan="8" >Bill Total: '+b.bill_number+'</td><td class="text-right">'+formatMoney(cost_price)+'</td><td class="text-right">'+formatMoney(retrun_price)+'</td></tr>');

                                    });  

                                  $('#SlRData > tbody').append('<tr style="font-weight:bold"><td colspan="8" >Grand Total: </td><td class="text-right">'+formatMoney(grand_cost_price)+'</td><td class="text-right">'+formatMoney(grand_retrun_price)+'</td></tr>');

                                }else{
                                    $('#SlRData > thead').empty();
                                    $('#SlRData > thead').append('<tr><th><?= lang("s.no"); ?></th><th><?= lang("item"); ?></th><th><?= lang("Pcs_Per_condo"); ?></th><th><?= lang("Ordered_Qty_Condo"); ?></th><th><?= lang("Ordered_Qty_Pcs"); ?></th><th><?= lang("Return_Qty_Condo(Pcs)"); ?></th><th><?= lang("Total_Consumed_Qty_Condo(Pcs)"); ?></th><th><?= lang("Avg_Cost_Price_Per_unit"); ?></th><th><?= lang("Cost_Price_for_ordered_Qty"); ?></th><th><?= lang("Return_amount/Profit"); ?></th></tr>');
                                    var $row_index = ($offset) ?parseInt($offset)+parseInt(1):1;

                                    var cost_price =0;
                                    var retrun_price =0;
                                    var profit =0;

                                    $.each(data.bbqrports, function (a,b) 
                                    {            
                                        /*cost_price +=(parseFloat(b.costper_orderqty));
                                        retrun_price +=(parseFloat( b.Return_Price));*/
                                        cost_price +=(parseFloat(b.orderpcs*b.avgpcsrate));
                                            retrun_price +=(parseFloat(b.returnqty_condopcs*b.avgpcsrate));

                                        // profit += (parseFloat(b.profit));

                                         $('#SlRData > tbody').append('<tr><td>'+$row_index+'</td><td class="text-center">'+b.item+'</td><td class="text-center">'+b.pcs_percondo+'</td><td class="text-center">'+b.order_qtycondo+'</td><td class="text-center">'+b.orderpcs+'</td><td class="text-center">'+b.returnqty_condopcs+'</td><td class="text-center">'+(b.orderpcs -b.returnqty_condopcs)+'</td><td class="text-right">'+formatMoney(b.avgpcsrate)+'</td><td class="text-right">'+formatMoney(b.orderpcs*b.avgpcsrate)+'</td><td class="text-right">'+formatMoney(b.returnqty_condopcs*b.avgpcsrate)+'</td></tr>');
                                        $row_index++;
                                    });

                                  $('#SlRData > tbody').append('<tr style="font-weight:bold"><td colspan="8" >Total: </td><td class="text-right">'+formatMoney(cost_price)+'</td><td class="text-right">'+formatMoney(retrun_price)+'</td></tr>');
                                }
                                
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

      $("#SlRData").table2excel({

        // exclude CSS class

        exclude: ".noExl",

        name: "Worksheet Name",

        filename: "Item Sale Reports " //do not include extension

      });

    });
</script>