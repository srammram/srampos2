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
        <h2 class="blue"><i class="fa-fw fa fa-heart"></i><?= lang('modify_bills'); ?> <?php
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
                        <div class="col-sm-2">
                            <div class="form-group">
                                <?= lang("start_date", "start_date"); ?>
                                <?php echo form_input('start_date', ($this->session->userdata('start_date')), 'class="form-control " autocomplete="off"  id="start_date"'); ?>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <?= lang("end_date", "end_date"); ?>
                                <?php echo form_input('end_date', ($this->session->userdata('end_date')), 'class="form-control "  autocomplete="off" id="end_date"'); ?>                                
                            </div>
                        </div>
						
                        <!--<div class="col-sm-2">
                            <div class="form-group">
                             <?= lang("bill_no", "bill_no"); ?>
                             <select class="form-control col-sm-2" name="bill_no" id="bill_no">
                                <option value="">Select</option>
                            </select>                               
                            </div>
                        </div>-->
						
						
						
                         <?php if($this->Owner || $this->Admin) : ?>
                        <div class="col-sm-2">
                        <?= lang("bill_type", "bill_type"); ?>
                            <div class="form-group">
                              
                                <?php $t_w = ($this->session->userdata('table_whitelisted'))?$this->session->userdata('table_whitelisted'):'all';?>
                                <select name="table_whitelisted" class="form-control select" id="table_whitelisted" style="width:100%">
                                    <option value="all" <?php if($t_w=="all") { echo 'selected="selected"';} ?>>All</option>
                                    <option value="dontprint" <?php if($t_w=="dontprint") { echo 'selected="selected"';} ?>>Dont Print</option>
                                    <option value="print" <?php if($t_w=="print") { echo 'selected="selected"';} ?>>Print</option>
                                </select>
                            </div>
                        </div>                    
                    <?php endif; ?> 
						<div class="col-sm-2">
						 <?= lang("Show", "Show"); ?>
                            <div class="form-group">
                               <?php $limit = $this->session->userdata('list_limit');?>
                               <select name="pagelimit" class="form-control select" id="pagelimit" style="width:100%">
                                    <option value=""></option>
                                    <option value="1" <?php if($limit=="1") { echo 'selected="selected"';} ?>>1</option>
                                    <option value="2" <?php if($limit=="2") { echo 'selected="selected"';} ?>>2</option>
                                    <option value="4" <?php if($limit=="4") { echo 'selected="selected"';} ?>>4</option>
                                    <option value="10"  <?php if(!$limit || $limit=="10") { echo 'selected="selected"';} ?>>10</option>
                                    <option value="15" <?php if($limit=="15") { echo 'selected="selected"';} ?>>15</option>
                                    <option value="30" <?php if($limit=="30") { echo 'selected="selected"';} ?>>30</option>
                                    <option value="100 <?php if($limit=="100") { echo 'selected="selected"';} ?>">100</option>
                                    <option value="all" <?php if($limit=="all") { echo 'selected="selected"';} ?>>All</option>
                                </select>                                  
                            </div>
                        </div>
			<div class="col-sm-2">
                            <label style="margin-top:19px;"></label>
                            <div class="form-group">
                                <div class="controls"> <?php echo form_submit('submit_report', $this->lang->line("submit"), 'class="btn btn-primary bill_details"'); ?> 
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
                    <!-- <?php echo form_close(); ?> -->
                <div class="clearfix"></div>
                <div>
                    <label style="color:#10c720;">Print : <span id="print-total"></span></label>
                    <label style="color:red;">Dont print : <span id="dontprint-total"></span></label>
                    <label>Target Amount : </label><input  type="text" name="target_amt" id="target_amt" value="<?=$this->session->userdata('target_amt')?>" style="height:34px;padding:6px 12px;margin-left: 5px; width: 14.4%;"> 
                    <div style="float: right;"><button type="button" class="btn btn-primary delete-bill"><?=lang('delete')?></button>                        
                     <?php if($this->pos_settings->bill_series_settings == 0){ ?>   
                        <button type="button" class="btn btn-primary change-print-bill"><?=lang('change_as_print')?></button>
                    <?php } ?>
                </div>
                </div><br>
                <div class="clearfix"></div>
                <div class="table-responsive">
                    <table id="BillDetailsPrintData"
                           class="table table-bordered table-hover table-striped table-condensed reports-table">
                       <thead>
                          <tr>
                            <th><?= lang("s_no"); ?></th>
                              <th><?= lang("bill_no"); ?></th>
                              <th><?= lang("print_type"); ?></th>
                              <th><?= lang("date"); ?></th>
                              <th><?= lang("table"); ?></th>                              
			      <th><?= lang("reference_no"); ?></th>
                              <th><?= lang("total_items"); ?></th>
                              <th><?= lang("total"); ?></th>
                              <th><?= lang("total_discount"); ?></th>
                              <th><?= lang("total_tax"); ?></th>
                              <th><?= lang("grand_total"); ?></th>
                              <th><?= lang("total_pay"); ?></th>
                              <th><?= lang("balance"); ?></th>
                              <th><?=lang('action')?></th>
                              <th><?=lang('select')?> <input type="checkbox" id="select-all-bills"></th>
                              
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
            GetData($url);
            return false;
        });
        $(document).on('click', '.bill_details', function () {
            $offset = false;
            $url = '<?=admin_url('reports/get_bill_reports');?>';
            GetData($url);
        });
        $(document).on('change', '#target_amt', function () {
            $offset = false;
            $url = '<?=admin_url('reports/get_bill_reports');?>';
            GetData($url);
        });

        $(document).on('change', '#pagelimit', function () {
            $offset = false;
            $url = '<?=admin_url('reports/get_bill_reports');?>';
            GetData($url);
        });
            $url = '<?=admin_url('reports/get_bill_reports');?>';
            GetData($url);
    });

function GetData($url){              
    var start = $('#start_date').val();
    var end = $('#end_date').val();
    var bill_no = $('#bill_no').val();
    var warehouse_id = $('#warehouse_id').val();
    var target_amt = $('#target_amt').val();
    var pagelimit = $('#pagelimit').val();
    var table_whitelisted = $('#table_whitelisted').val();
    if (start !='' && end !='') {                  
        $('#start_date,#end_date').css('border-color', '#ccc'); 
        $('#kot').siblings(".select2-container").find('.select2-choice').css('border-color', '#ccc');
                  $.ajax({
                        type: 'POST',
                        url: $url,
                        data: {target_amt:target_amt,start: start, end: end, bill_no : bill_no, warehouse_id : warehouse_id,pagelimit:pagelimit,table_whitelisted:table_whitelisted},
                        dataType: "json",
                        success: function (data) {
                            $('#BillDetailsPrintData > tbody').empty();

                            if(data.bill_details =='empty' || data.bill_details == 'error'){
                             
                            $('#BillDetailsPrintData > tbody').append('<tr><td colspan="15" class="dataTables_empty"><?= lang('sEmptyTable') ?></td></tr>');    
                            }
                            else{
                                    $('.dataTables_paginate').html(data.pagination);
                                    var total = 0;
                                    var total_discount = 0;
                                    var total_tax = 0;
                                    var grand_total = 0;   
                                    var total_pay = 0;
                                    var balance = 0;
                                    var $row_index = ($offset) ?parseInt($offset)+parseInt(1):1;
                                $.each(data.bill_details, function (a,b) 
                                {
                                   
                                    total += parseFloat(b.total);
                                    total_discount += parseFloat(b.total_discount);
                                    total_tax += parseFloat(b.total_tax);
                                    grand_total += parseFloat(b.grand_total);
                                    total_pay += parseFloat(b.total_pay);
                                    balance += parseFloat(b.balance);
                                    $class = 'print-bill';
                                    if (b.print_type=="dont print"){
                                        $class = 'dont-print-bill';
                                    }
                                    $t_data = '<tr class="text-center '+$class+'"><td>'+$row_index+'</td><td>'+b.bill_number+'</td><td style="text-transform: capitalize;">'+b.print_type+'</td><td>'+b.date+'</td><td>'+b.table_name+'</td><td>'+b.reference_no+'</td><td class="text-right">'+b.total_items+'</td><td>'+b.total+'</td><td>'+b.total_discount+'</td><td>'+b.total_tax+'</td><td>'+b.grand_total+'</td><td >'+b.total_pay+'</td><td class="text-right">'+b.balance+'</td>';
                                    //if (b.print_type=="dont print") {
                                        $t_data +='<td class=""><div class="text-center"><div class="btn-group text-left"><button type="button" class="'+$class+'-btn btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown"> Actions <span class="caret"></span></button>        <ul class="dropdown-menu pull-right" role="menu">';
                                        //alert(b.action)
                                        if (b.action==1) {
                                            $t_data +='<li><i class="fa fa-money"></i>Edited</li>';
                                        }else{
                                              $t_data +='<li><a href="<?=admin_url('reports/edit_dontprint/')?>'+b.id+'" ><i class="fa fa-edit"></i>Edit</a></li>';
                                        }
                                        
                                        $t_data +='<li><a href="<?=admin_url('reports/deleteDontPrintBill/')?>'+b.id+'" class="delete-dont-print" data-amt="'+b.grand_total+'"><i class="fa fa-trash"></i>Delete</a></li>';                                        
                                        <?php if($this->pos_settings->bill_series_settings == 0){ ?>
                                            if (b.print_type=="dont print") {
                                            $t_data +='<li><a href="<?=admin_url('reports/change_toPrint/')?>'+b.id+'" class="change-print" data-amt="'+b.grand_total+'"><i class="fa fa-exchange"></i>Change as print</a></li>';
                                            }else{
                                                $t_data +='<li><a href="<?=admin_url('reports/change_toDontPrint/')?>'+b.id+'" class="change-dont-print" data-amt="'+b.grand_total+'"><i class="fa fa-exchange"></i>Change as Dont print</a></li>';
                                            }
                                        <?php } ?>

                                        $t_data +='</ul>    </div></div></td>';
                                        $t_data +='<td><input type="checkbox" class="select-bill" value="'+b.id+'"></td>';
                                    //}else{
                                    //    $t_data +='<td></td><td></td>';
                                    //}
                                   
                                    
                                    $t_data +='</tr>';
                                   $('#BillDetailsPrintData > tbody').append($t_data);
                                   $row_index++;
                                });
                                $('.select-bill').not('.skip').iCheck({
                                    checkboxClass: 'icheckbox_square-blue',
                                    radioClass: 'iradio_square-blue',
                                    increaseArea: '20%'
                                }).on('ifChanged', function(event){
                                    //console.log($('.select-bill:checked').length +'=='+ $('.select-bill').length)
                                    if($('.select-bill:checked').length == $('.select-bill').length) {
                                        $('#select-all-bills').prop('checked', 'checked');
                                    } else {
                                        $('#select-all-bills').prop('checked',false);
                                    }
                                    $('#select-all-bills').iCheck('update');
                                });

                                $('#BillDetailsPrintData > tbody').append('<tr class="text-right"><td colspan=8>'+formatMoney(total)+'</td><td>'+formatMoney(total_discount)+'</td><td>'+formatMoney(total_tax)+'</td><td style="font-weight:bold">'+formatMoney(grand_total)+'</td><td>'+formatMoney(total_pay)+'</td><td>'+formatMoney(balance)+'</td></tr>');
                           }
                           if (data.p_total==null) {
                            data.p_total = formatMoney(0);
                           }
                           if (data.dp_total==null) {
                            data.dp_total = formatMoney(0);
                           }
                           
                           $('#print-total').text(data.p_total);
                           $('#dontprint-total').text(data.dp_total);
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

    $(document).on('change', '#end_datee', function () {        
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
      $("#BillDetailsPrintData").table2excel({
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
<script>
    $(document).ready(function(){
        $('#select-all-bills').on('ifChecked', function(event) {
            //if (event.target.checked) {
               $('.select-bill').iCheck('check');
            //}else{
              //  $('.select-bill').iCheck('uncheck');
           // }
        });
        $('#select-all-bills').on('ifUnchecked', function(event) {
            //if (event.target.checked) {
               $('.select-bill').iCheck('uncheck');
            //}else{
              //  $('.select-bill').iCheck('uncheck');
           // }
        });
        //$(document).on('ifChecked','.select-bill', function(event) {
        //    if ($('.select-bill').length>$('.select-bill:checked').length) {
        //        alert('7')
        //    }
        //});
        $('.delete-bill').click(function(e){
            e.preventDefault();
            $selected_bills = [];
            $('.select-bill:checked').each(function(){
                $id = $(this).val();
                $selected_bills.push($id);
            });
            if ($selected_bills.length>0) {
                bootbox.confirm({ 
                size: "small",
                message: "Are you sure want to delete these selected bills?",
                callback: function(result){
                    if (result) {
                        $url = '<?=admin_url('reports/deletebills')?>';
                        $.ajax({
                            url :$url,
                            type:'post',
                            data:{'bills':$selected_bills},
                            
                            success:function(){
                                alert('Selected Bills has been deleted successfully');
                                location.reload();
                            }                            
                        });
                    }
                }
            });
            }else{
                alert('Select Bills');
            }
        });
        $('.change-print-bill').click(function(e){
            e.preventDefault();
            $selected_bills = [];
            $('.select-bill:checked').each(function(){
                $id = $(this).val();
                $selected_bills.push($id);
            });
            if ($selected_bills.length>0) {
                bootbox.confirm({ 
                size: "small",
                message: "Are you sure want to change these selected bills?",
                callback: function(result){
                    if (result) {
                        $('#myModal').modal('show');
                        $url = '<?=admin_url('reports/changeBills_toPrint')?>';
                        $.ajax({
                            url :$url,
                            type:'post',
                            data:{'bills':$selected_bills},
                            dataType:'json',
                            success:function(res){
                                if (res.status) {
                                    $('#myModal').modal('hide');
                                    alert('Selected Bills has been changed as print bills');
                                    location.reload();
                                }                                
                            }                            
                        });
                    }
                }
            });
            }else{
                alert('Select Bills');
            }
        });
        $(document).on('click','.delete-dont-print',function(e){
            e.preventDefault();
            $url = $(this).attr('href');
            $amt = $(this).attr('data-amt');
            bootbox.confirm({ 
                size: "small",
                message: "Are you sure?",
                callback: function(result){
                    if (result) {
                        $.ajax({
                            url :$url,
                            type:'post',
                            success:function(){
                                alert('Bill has been deleted successfully');
                                location.reload();
                            }                            
                        });
                    }
                }
            });
            
        });
        $(document).on('click','.change-print',function(e){
            e.preventDefault();
            $url = $(this).attr('href');
            $amt = $(this).attr('data-amt');
            bootbox.confirm({ 
                size: "small",
                message: "Are you sure?",
                callback: function(result){
                    if (result) {
                        $.ajax({
                            url :$url,
                            type:'post',
                            success:function(){
                                alert('Bill has been changed as print bill');
                                location.reload();
                            }                            
                        });
                    }
                }
            });
            
        });
        $(document).on('click','.change-dont-print',function(e){
            e.preventDefault();
            $url = $(this).attr('href');
            $amt = $(this).attr('data-amt');
            bootbox.confirm({ 
                size: "small",
                message: "Are you sure?",
                callback: function(result){
                    if (result) {
                        $.ajax({
                            url :$url,
                            type:'post',
                            success:function(){
                                alert('Bill has been changed as print bill');
                                location.reload();
                            }                            
                        });
                    }
                }
            });
            
        });
    });
</script>
<style>
    tr.dont-print-bill td{
        background-color: #bbb5b5 !important;
    }
    .print-bill-btn,.print-bill-btn:hover{
        background: #f00;
        border: 1px solid #f00;
    }
</style>