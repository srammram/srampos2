<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
  <link rel="stylesheet" href="<?= $assets ?>styles/jquery-ui.css">
  <script src="<?= $assets ?>js/jquery-ui.js"></script>
  

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
        <h2 class="blue"><i class="fa-fw fa fa-heart"></i><?= lang('item_sale_report'); ?> <?php
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
          <!--       <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'id' => 'pos-item-sale-report');
             echo admin_form_open("api/v1/posreports/recipe", $attrib);?> -->
                    <div class="row">
                        <!-- <div class="col-sm-5">
                             <div class="form-group">
                                <?= lang("recipe", "suggest_recipe"); ?>
                                <?php echo form_input('srecipe', (isset($_POST['recipe']) ? $_POST['recipe'] : ""), 'class="form-control" id="suggest_recipe"'); ?>
                                <input type="hidden" name="recipe" value="<?= isset($_POST['recipe']) ? $_POST['recipe'] : "" ?>" id="report_recipe_id" required />
                            </div>
                        </div> -->
                       <!--  <div class="col-md-4">
                         <div class="form-group">
                            <?= lang('category', 'category'); ?>
                            <?php
                            $ct[''] = lang('select').' '.lang('category');
                            foreach ($categories as $category) {
                                $ct[$category->id] = $category->name;
                            }
                            ?>
                            <?= form_dropdown('category', $ct, set_value('category', $expense->category_id), 'class="form-control tip" id="category"'); ?>
                        </div>
                        > -->

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
                            class="controls"> <?php echo form_submit('submit_report', $this->lang->line("submit"), 'class="btn btn-primary submit_itemreport"'); ?> </div>
                             
                    </div>
                </div>
                    <!-- <?php echo form_close(); ?> -->
                <div class="clearfix"></div>

                <div class="table-responsive">
                    <table id="SlRData" class="table table-bordered table-hover table-striped table-condensed reports-table">
                        <thead>
                        <tr>
                            <th><?= lang("item"); ?></th>
							<th><?= lang("warehouse"); ?></th>
                            <th><?= lang("qty"); ?></th>
                            <th><?= lang("rate"); ?></th>
                            <th><?= lang("discount"); ?></th>
                            <th><?= lang("tax"); ?></th>
                            <th><?= lang("sale_value"); ?></th> 
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
            GetData($url);
            return false;
        });
        $(document).on('click', '.submit_itemreport', function () {
            $url = '<?=site_url('api/v1/posreports/get_itemreports');?>';
            GetData($url);
        });

        $(document).on('change', '#pagelimit', function () {
            $url = '<?=site_url('api/v1/posreports/get_itemreports');?>';
            GetData($url);
        });

            $url = '<?=site_url('api/v1/posreports/get_itemreports');?>';            
            GetData($url);

    });
function GetData($url) {    
            /*var recipe = $('#suggest_recipe').val();*/
            var start_date = $('#start_date').val();
            var end_date = $('#end_date').val();
            var warehouse_id = $('#warehouse_id').val();
            var pagelimit = $('#pagelimit').val();
			var printlist = $('#printlist').val();  
			
            var api_key = $('.api_key').val();
            if (start_date !='' && end_date !='' ) {
                 $('#start_date,#end_date').css('border-color', '#ccc'); 
                  $.ajax({
                        type: 'POST',
                        url: $url,                    
                        data: {'api-key':api_key,start_date: start_date, end_date: end_date, warehouse_id: warehouse_id,pagelimit:pagelimit, printlist:printlist},
                        dataType: "json",
                        success: function (data) {
                            $('#SlRData > tbody').empty(); 
                            if(data.itemreports =='empty' || data.itemreports == 'error'){ 
                                 $('#SlRData > tbody').append('<tr><td colspan="7" class="dataTables_empty"><?= lang('sEmptyTable') ?></td></tr>');
                            }
                            else{
                                $('.dataTables_paginate').html(data.pagination);
                                $('#SlRData > tbody').empty();
                                $('#SlRData > tbody').append('<tr><td style = "text-align:left"><strong>From: </strong>   '+start_date+'</td><td style = "text-align:left"><strong>To: </strong>   '+end_date+'</td></tr>');
                                 
                                var grand_qty  = 0;
                                var grand_total = 0;
                                var grand_rate = 0;
                                var grand_tax = 0;
                                var grand_discount = 0;
                                var round = 0;
                                $.each(data.round, function (r,o) 
                                {
                                    round =parseFloat(o.round);
                                }); 
                                $.each(data.itemreports, function (a,b) 
                                {  
                                  $('#SlRData > tbody').append('<tr><td style = "text-align:left"><strong>GROUP: </strong>   '+b.category+'</td></tr>');

                                         var grp_qty  = 0;
                                         var grp_total = 0;
                                         var grp_rate = 0;
                                         var grp_tax = 0;
                                         var grp_discount = 0;
                                        $.each(b.split_order, function (c,d) 
                                        {  
                                            $('#SlRData > tbody').append('<tr><td style = "text-align:left"><strong>Sub Group:</strong>'+d.sub_category+'</td></tr>');
                                            
                                            /*round = parseFloat(d.grand_total)- parseFloat(d.round_total);*/   
                                               var sub_qty  = 0;
                                                var sub_rate = 0;
                                                var sub_tax = 0;
                                                var sub_discount = 0;
                                                var sub_total = 0;   
                                             $.each(d.order, function (e,f) 
                                             {  
                                             /*SUB START*/
                                                

                                               sub_qty += parseFloat(f.quantity);
                                               sub_rate += (parseFloat(f.rate)*(parseFloat(f.quantity)));

                                              sub_tax += (parseFloat(f.tax)); 
                                               sub_discount += parseFloat(f.item_discount)+parseFloat(f.off_discount)+ parseFloat(f.input_discount);

                                               sub_total += (parseFloat(f.amt));
                                            
                                             /*SUB END*/

                                             /*GROUP START*/
                                               grp_qty += parseFloat(f.quantity);
                                               grp_rate += (parseFloat(f.rate));

                                               grp_tax += (parseFloat(f.tax));
                                               
                                               grp_discount += parseFloat(f.item_discount)+parseFloat(f.off_discount)+ parseFloat(f.input_discount);
                                               grp_total +=(parseFloat(f.amt));
                                            /*GROUP END*/

                                            /*GRAND START*/
                                               grand_qty += parseFloat(f.quantity);
                                              grand_rate += (parseFloat(f.rate));

                                              grand_tax += (parseFloat(f.tax));


                                               grand_discount += parseFloat(f.item_discount)+parseFloat(f.off_discount)+ parseFloat(f.input_discount);

                                               grand_total += (parseFloat(f.amt));
                                             /*GRAND END*/
                                             var subtotal = parseFloat(f.subtotal);
                                             var tax = parseFloat(f.tax);

                                              var rate = (parseFloat(f.subtotal));

                                               /*var rate = (parseFloat(f.subtotal) - (parseFloat(f.item_discount) + parseFloat(f.off_discount) + parseFloat(f.input_discount))+(parseFloat(f.tax)));*/

                                               var dis = (parseFloat(f.item_discount) + parseFloat(f.off_discount) + parseFloat(f.input_discount));
                                                $('#SlRData > tbody').append('<tr  class="text-right"><td class="text-center">'+f.name+'</td><td>'+f.warehouse+'</td><td>'+formatQuantity(f.quantity)+'</td><td>'+formatMoney(f.rate)+'</td><td>'+formatMoney(dis)+'</td><td>'+formatMoney(f.tax)+'</td><td>'+formatMoney(f.amt)+'</td></tr>');
                                            }); 
                                             $('#SlRData > tbody').append('<tr style="font-weight:bold" class="text-right"><td class="text-left">Sub Total:</td><td>&nbsp;</td><td>'+formatQuantity(sub_qty)+'</td><td>'+formatMoney(sub_rate)+'</td><td>'+formatMoney(sub_discount)+'</td><td>'+formatMoney(sub_tax)+'</td><td>'+formatMoney(sub_total)+'</td></tr>');
                                       });

                                        $('#SlRData > tbody').append('<tr style="font-weight:bold" class="text-right"><td class="text-left">Group Total:</td><td>&nbsp;</td><td>'+formatQuantity(grp_qty)+'</td><td>'+formatMoney(grp_rate+grp_discount)+'</td><td>'+formatMoney(grp_discount)+'</td><td>'+formatMoney(grp_tax)+'</td><td>'+formatMoney(grp_total)+'</td></tr>');
                                });
                               /* if(round != 0){

                                  $('#SlRData > tbody').append('<tr style="font-weight:bold"  class="text-right"><td class="text-left" colspan="5">Round :</td><td>&nbsp;</td><td>'+formatMoney(round)+'</td></tr>');
                                }    */

                                  $('#SlRData > tbody').append('<tr style="font-weight:bold"  class="text-right"><td class="text-left">Grand Total:</td><td>&nbsp;</td><td>'+formatQuantity(grand_qty)+'</td><td>'+formatMoney(grand_rate+grand_discount)+'</td><td>'+formatMoney(grand_discount)+'</td><td>'+formatMoney(grand_tax)+'</td><td>'+formatMoney((grand_total))+'</td></tr>');
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