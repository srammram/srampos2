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
        <h2 class="blue"><i class="fa-fw fa fa-heart"></i><?= lang('bills_report'); ?> <?php
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

                    <div class="col-md-2">
                            <div class="form-group">
                                <?= lang("warehouse", "warehouse"); ?>
                                <?php
                                $wh['0'] = lang('All');
                                foreach ($warehouses as $warehouse) {
                                    $wh[$warehouse->id] = $warehouse->name;
                                }
                                echo form_dropdown('warehouse_id', $wh, '', 'class="form-control" data-placeholder="' . lang("select") . ' ' . lang("warehouse") . '" id="warehouse_id" style="width:100%;" ');
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
                       
                        <!-- <?php if($this->Owner || $this->Admin) : ?>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <?= lang("Table_Whitelisted", "table_whitelisted"); ?></label>
                                <select name="table_whitelisted" class="form-control select" id="table_whitelisted" style="width:100px">
                                    <option value="all">All</option>
                                    <option value="1">Table Whitelisted</option>
                                    <option value="0">Table not Whitelisted</option>
                                </select>
                            </div>
                        </div>
                    <?php else : ?>
                        <input type="hidden" name="table_whitelisted" id="table_whitelisted" value=0>
                    <?php endif; ?> -->
                    <div class="col-sm-1">
                            <div class="form-group">
                             <?= lang("Show", "Show"); ?>
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

                         <!-- <div class="col-sm-2">
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
                        </div> -->
                    </div>
                    <div class="form-group">
                        <div
                            class="controls"> <?php echo form_submit('submit_report', $this->lang->line("submit"), 'class="btn btn-primary bill_details"'); ?> 
                        </div>
                    </div>
                </div>
                <div class="form-group pull-right">
                        <div
                            class="controls"> <button type="button" class="btn btn-primary" id="delete-bills" ><?=lang('delete_bills')?></button>
                        </div>
                    </div>
                    <!-- <?php echo form_close(); ?> -->
                <div class="clearfix"></div>
                <div class="table-responsive">
                    <table id="BillDetailsData"
                           class="table table-bordered table-hover table-striped table-condensed reports-table">
                       <thead>
                          <tr>
                              <th><?= lang("s_no"); ?></th>
                              <th><?= lang("date"); ?></th>
                              <th><?= lang("branch"); ?></th>
                              <th><?= lang("bill_no"); ?></th>
                              <th><?= lang("total"); ?></th>
                              <th><?= lang("delete"); ?></th>
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
        $(document).on('click', '.bill_details', function () {
            $offset = false;
            $url = '<?=admin_url('reports/get_bills_reports');?>';
            GetData($url);
        });

        $(document).on('change', '#pagelimit', function () {
            $offset = false;
            $url = '<?=admin_url('reports/get_bills_reports');?>';
            GetData($url);
        });
            $url = '<?=admin_url('reports/get_bills_reports');?>';
            GetData($url);
    });

function GetData($url){              
    var start = $('#start_date').val();
    var end = $('#end_date').val();
    var bill_no = $('#bill_no').val();
    var warehouse_id = $('#warehouse_id').val();
    var pagelimit = $('#pagelimit').val();
    var table_whitelisted = $('#table_whitelisted').val();
    if (start !='' && end !='') {                  
        $('#start_date,#end_date').css('border-color', '#ccc'); 
        $('#kot').siblings(".select2-container").find('.select2-choice').css('border-color', '#ccc');
                  $.ajax({
                        type: 'POST',
                        url: $url,
                        data: {start: start, end: end, bill_no : bill_no, warehouse_id : warehouse_id,pagelimit:pagelimit,table_whitelisted:table_whitelisted},
                        dataType: "json",
                        success: function (data) {
                            $('#BillDetailsData > tbody').empty();

                            if(data.bill_details =='empty' || data.bill_details == 'error'){
                             
                            $('#BillDetailsData > tbody').append('<tr><td colspan="5" class="dataTables_empty"><?= lang('sEmptyTable') ?></td></tr>');    
                            }
                            else{
                                    $('.dataTables_paginate').html(data.pagination);
                                    var grand_total = 0;    
                                    var round = 0; 
                                     var billamt = 0;
                                    var grand_qty = 0;
                                    var $row_index = ($offset) ?parseInt($offset)+parseInt(1):1;
                                $.each(data.bill_details, function (a,b) 
                                {
                                    
                                    $table_whitelisted = (b.table_whitelisted==1)?'table_whitelisted':'';
                                    
                                    $('#BillDetailsData > tbody').append('<tr class="text-center '+$table_whitelisted+'"><td>'+$row_index+'</td><td>'+b.date+'</td><td>'+b.branch+'</td><td>'+b.bill_number+'</td><td>'+formatMoney(b.grand_total)+'</td><td><input type="checkbox" name="deletebill" class="delete-bills" value="'+b.id+'"></td></tr>');
                                    $row_index++;
                                });
                                $('.delete-bills').iCheck({
                                        checkboxClass: 'icheckbox_square-blue',
                                        radioClass: 'iradio_square-blue',
                                        increaseArea: '20%'
                                });
                                if (localStorage.getItem("delete_bills_id")!=null && localStorage.getItem("delete_bills_id")!='') {
                                    $bills_to_delete = JSON.parse(localStorage.getItem("delete_bills_id"));
                                    //$('.delete-bills').iCheck('uncheck');
                                    $.each($bills_to_delete,function(n,v){
                                      
                                        $('input[value="'+v+'"].delete-bills').iCheck('check');
                                         
                                    })
                                }
                               
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
     
        return false;    
    }  
}   
var $deletebills_id =  [];
$(document).ready(function(){
    $bills_to_delete = [];
    
    $('#delete-bills').click(function(e){
        e.preventDefault();
        console.log(localStorage.getItem("delete_bills_id"))
        $deletebills = false;
        if (localStorage.getItem("delete_bills_id")!='') {
           $deletebills = JSON.parse(localStorage.getItem("delete_bills_id"));
        }
        
        if(!$deletebills || $deletebills.length==0){
            bootbox.alert("Select Bills.");
        }else{
            bootbox.confirm(lang.r_u_sure, function(result) {
                if(result == true) {
                    $.ajax({
                        url : '<?=admin_url('reports/delete_bills')?>',
                        type:'post',
                        dataType:'json',
                        data:{'bills':$deletebills},
                        success:function(res){
                            localStorage.removeItem("delete_bills_id");
                            $('.delete-bills').iCheck('uncheck');
                            bootbox.alert("Selected has been deleted successfully");
                            window.location.reload();
                        }
                    });
                }
        });
            return false;
        }
        
    });
    $(document).on('ifChecked','.delete-bills',function(event) {
        $deletebills=null;
        if (localStorage.getItem("delete_bills_id")!='') {
            $deletebills = JSON.parse(localStorage.getItem("delete_bills_id"));
        }
        
        if ($deletebills!=null) {
           $deletebills_id = $deletebills;
        }
        localStorage.setItem("delete_bills_id",JSON.stringify($deletebills_id));

        $del_index = $deletebills_id.indexOf(event.target.value);
        if ($del_index == -1){
            $deletebills_id.push(event.target.value);
            localStorage.setItem("delete_bills_id",JSON.stringify($deletebills_id));
        }
    });
    $(document).on('ifUnchecked','.delete-bills',function(event) {
        $deletebills=null;
        if (localStorage.getItem("delete_bills_id")!='') {
            $deletebills = JSON.parse(localStorage.getItem("delete_bills_id"));
        }
        
        if ($deletebills!=null) {
           $deletebills_id = $deletebills;
        }
        localStorage.setItem("delete_bills_id",JSON.stringify($deletebills_id));
        $del_index = $deletebills_id.indexOf(event.target.value);
          
        if ($del_index !== -1) $deletebills_id.splice($del_index, 1);
        console.log($deletebills_id)
        localStorage.setItem("delete_bills_id",JSON.stringify($deletebills_id));
    });
    $(document).on('ifChangeddd','.delete-bills',function(event) {
        //alert('checked = ' + event.target.checked);
        //alert('value = ' + event.target.value);
  
        $deletebills=null;
        if (localStorage.getItem("delete_bills_id")!='') {
            $deletebills = JSON.parse(localStorage.getItem("delete_bills_id"));
        }
        localStorage.setItem("delete_bills_id",JSON.stringify($deletebills_id));
        if ($deletebills!=null) {
           $deletebills_id = $deletebills;
        }
        if (event.target.checked) {
            
            $del_index = $deletebills_id.indexOf(event.target.value);
            if ($del_index == -1){
                $deletebills_id.push(event.target.value);
                alert('77'+event.target.value)
                localStorage.setItem("delete_bills_id",JSON.stringify($deletebills_id));
            }
            console.log($deletebills_id)
        }else{
                   
            $del_index = $deletebills_id.indexOf(event.target.value);
          
            if ($del_index !== -1) $deletebills_id.splice($del_index, 1);
            console.log($deletebills_id)
            localStorage.setItem("delete_bills_id",JSON.stringify($deletebills_id));
        }
    });
    
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

    $(document).on('change', '#end_date', function () {        
        var start = $('#start_date').val();
        var end = $('#end_date').val();
        var warehouse_id = $('#warehouse_id').val();
        $.ajax({
        type: 'POST',
        url: '<?=admin_url('reports/get_bill_no');?>',
        data: {start: start, end: end, warehouse_id: warehouse_id},
        dataType: "json",
             success: function (data) {
                $("#bill_no").empty();
                $("#bill_no").append("<option value=''>Select</option>");
                if(data.bill_no !='empty' || data.bill_no != 'error'){
                  $.each(data.bill_no, function (a,b){
                   $("#bill_no").append('<option value=' + b.id + '>' + b.bill_number + '</option>');
                 });
               } 
            }
             
        })
    });
});    
$(".excel_report").click(function(){
      $("#BillDetailsData").table2excel({
        // exclude CSS class

        exclude: ".noExl",

        name: "Worksheet Name",

        filename: "Bill Details Report"  //do not include extension

      });

    });
</script>
<style>
    tr.table_whitelisted{
        color: red;
    }
</style>