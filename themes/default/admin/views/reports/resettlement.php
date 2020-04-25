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
        <h2 class="blue"><i class="fa-fw fa fa-heart"></i><?= lang('resettlement_report'); ?> <?php
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
                            class="controls"> <?php echo form_submit('submit_report', $this->lang->line("submit"), 'class="btn btn-primary report_settlement"'); ?> 
                          
                        </div>
                    </div>
                </div>
                    <!-- <?php echo form_close(); ?> -->
                <div class="clearfix"></div>

                <div class="table-responsive" id="SlRData1">
                      <table style="display: none">
                        <tr>
                            <th colspan="5"><span style="font-size: 20px"><?=lang('pos_settlement_report')?></span></th>
                         </tr>
                         <tr>                            
                            <th><span style="font-size: 20px"><?=lang('from_date')?>:</th>
                            <th><span style="font-size: 20px" id="from_date" ></span></th>
                            <th><span style="font-size: 20px"><?=lang('to_date')?></span>:</th>
                            <th><span style="font-size: 20px" id="to_date"></span></th>
                         </tr>
                        </table> 

                    <table id="SlRData" class="table table-bordered table-hover table-striped table-condensed reports-table">
                      
                        <thead>                            
                           
                        <tr>
                            <th><?=lang('s.no')?></th>
							<th><?= lang("resettlement_date"); ?></th>
                            <th><?= lang("table"); ?></th>
                            <th><?= lang("seats"); ?></th>
                            <th><?= lang("Customer"); ?></th>
							<th><?= lang("warehouse"); ?></th>
                            <th><?= lang("bill_no"); ?></th>
							<th><?= lang("sale_type"); ?></th>
                            <th><?= lang("time"); ?></th>
                            <th><?= lang("actual_tender"); ?></th>
                            <th><?= lang("actual_amount"); ?></th>
                            <th><?= lang("resettlement_tender"); ?></th>
							 <th><?= lang("resettle_amount"); ?></th>
							  <th><?= lang("settle_user"); ?></th>
                            <th><?= lang("resettle_user"); ?></th>
                           
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
            window.location.href = "<?=admin_url('reports/get_resettlementreports/pdf/?v=1'.$v)?>";
            return false;
        });
        $('#xls').click(function (event) {
            event.preventDefault();
            window.location.href = "<?=admin_url('reports/get_resettlementreports/0/xls/?v=1'.$v)?>";
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
        $(document).on('click', '.report_settlement', function () {
            $url = '<?=admin_url('reports/get_resettlementreports');?>';
            GetData($url);
        });

        $(document).on('change', '#pagelimit', function () {
            $url = '<?=admin_url('reports/get_resettlementreports');?>';
            GetData($url);
        });

           $url = '<?=admin_url('reports/get_resettlementreports');?>';            
           GetData($url);
    });
function date_format_conversion($date){
     var dateAr = $date.split('-');
     var newDate = dateAr[2] + '-' + dateAr[1] + '-' + dateAr[0];
     return newDate;

} 
function GetData($url){   
            /*var recipe = $('#suggest_recipe').val();*/
            var start_date = $('#start_date').val();
            var end_date = $('#end_date').val();
            var warehouse_id = $('#warehouse_id').val();
            var defalut_currency = "<?php echo $Settings->default_currency;?>";
            var pagelimit = $('#pagelimit').val();
            var for_currency  ="<?php echo $this->site->getExchangeCurrency($default_currency)?>";
            

            if (start_date !='' && end_date !='' ) {
                 $('#start_date,#end_date').css('border-color', '#ccc'); 
                  $.ajax({
                        type: 'POST',
                        url: $url,
                        data: {start_date: start_date, end_date: end_date, warehouse_id: warehouse_id, defalut_currency:defalut_currency,pagelimit:pagelimit},
                        dataType: "json",
                        success: function (data) {
                             $('#SlRData > tbody').empty();
                            if(data.settlements =='empty' || data.settlements == 'error'){ 
                               $('#SlRData > tbody').append('<tr><td colspan="6" class="dataTables_empty"><?= lang('sEmptyTable') ?></td></tr>');
                            }
                            else{
                                $('.dataTables_paginate').html(data.pagination);
                                $.each(data.settlements, function (c,d) {
                                    var $row_index = 1;                      
                                        $("#from_date").text(date_format_conversion(start_date));
                                        $("#to_date").text(date_format_conversion(end_date));
                                        $('#SlRData > tbody').append('<tr class="text-right"><td>'+$row_index+'</td><td class="text-center">'+d.resettlement_date+'</td><td class="text-center">'+d.tablen+'</td><td class="text-center">'+d.seats_id+'</td><td class="text-center">'+d.customer+'</td><td class="text-center">'+d.warehouses+'</td><td class="text-center">'+d.reference_no+'</td><td class="text-center">'+d.bill_type+'</td><td class="text-center">'+d.bill_time+'</td><td>'+d.actual_tender+'</td><td>'+d.actual_amount+'</td><td>'+d.resettlement_tender+'</td><td>'+d.resettlement_amount+'</td><td>'+d.settled_user+'</td><td>'+d.resettle_user+'</td></tr>');                     $row_index++;
                                     
                                                
                                });

                                
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

      $("#SlRData1").table2excel({

        // exclude CSS class

        exclude: ".noExl",

        name: "Worksheet Name",

        filename: "ReSettlement Reports  " //do not include extension

      });

    });
</script>