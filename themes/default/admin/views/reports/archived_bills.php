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
        <h2 class="blue"><i class="fa-fw fa fa-heart"></i><?= lang('archived_bills'); ?> <?php
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
                       <!-- <div class="col-sm-3">
                            <div class="form-group">
                                <?= lang("Table_Whitelisted", "table_whitelisted"); ?></label>
                                <?php $t_w = ($this->session->userdata('table_whitelisted'))?$this->session->userdata('table_whitelisted'):'all';?>
                                <select name="table_whitelisted" class="form-control select" id="table_whitelisted" style="width:100px">
                                    <option value="all" <?php if($t_w=="all") { echo 'selected="selected"';} ?>>All</option>
                                    <option value="1" <?php if($t_w=="1") { echo 'selected="selected"';} ?>>Dont Print</option>
                                    <option value="0" <?php if($t_w=="0") { echo 'selected="selected"';} ?>>Print</option>
                                </select>
                            </div>
                        </div>   -->                 
                    <?php endif; ?>
                    <div class="col-sm-2">
                    <?= lang("bill_action", "bill_action"); ?>
                        <div class="form-group">
                            <?php $action = ($this->session->userdata('bill_action'))?$this->session->userdata('bill_action'):'all';?>
                            <select name="bill_action" class="form-control select" id="bill_action" style="width:100%">
                                <option value="all" <?php if($action=="all") { echo 'selected="selected"';} ?>>All</option>
                                <option value="1" <?php if($action=="1") { echo 'selected="selected"';} ?>>Edited</option>
                                <option value="2" <?php if($action=="2") { echo 'selected="selected"';} ?>>Deleted</option>
                            </select>
                        </div>
                    </div> 
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
						<!--
						<div class="col-md-2">
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
                        </div>-->

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
						<div class="col-sm-2">
						<label style="margin-top:19px;"></label>
							<div class="form-group">
								<div
									class="controls"> <?php echo form_submit('submit_report', $this->lang->line("submit"), 'class="btn btn-primary bill_details"'); ?> 
								</div>
							</div>
						</div>
                    </div>
					
                </div>
                    <!-- <?php echo form_close(); ?> -->
                <div class="clearfix"></div>
                <!--<div>
                    <label>Print : <span id="print-total"></span></label>
                    <label>Dont print : <span id="dontprint-total"></span></label>
                </div>-->
                <div class="table-responsive">
                    <table id="BillDetailsPrintData"
                           class="table table-bordered table-hover table-striped table-condensed reports-table">
                       <thead>
                          <tr>
                              <th><?= lang("s_no"); ?></th>
                              <th><?= lang("date"); ?></th>
                              <th><?= lang("bill_no"); ?></th>
                              <th><?= lang("amount"); ?></th>
                              <th><?= lang("current_amount"); ?></th>
                              <th><?= lang("type"); ?></th> 
                              <th><?=lang('action')?></th>
                              
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
<script type="text/javascript">var $offset = false;
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
            $url = '<?=admin_url('reports/get_archived_bills');?>';
            GetData($url);
        });

        $(document).on('change', '#pagelimit', function () {
            $offset = false;
            $url = '<?=admin_url('reports/get_archived_bills');?>';
            GetData($url);
        });
            $url = '<?=admin_url('reports/get_archived_bills');?>';
            GetData($url);
    });

function GetData($url){              
    var start = $('#start_date').val();
    var end = $('#end_date').val();
    var bill_no = $('#bill_no').val();
    var warehouse_id = $('#warehouse_id').val();

    var pagelimit = $('#pagelimit').val();
    var bill_action = $('#bill_action').val();
    if (start !='' && end !='') {                  
        $('#start_date,#end_date').css('border-color', '#ccc'); 
        $('#kot').siblings(".select2-container").find('.select2-choice').css('border-color', '#ccc');
                  $.ajax({
                        type: 'POST',
                        url: $url,
                        data: {start: start, end: end, bill_no : bill_no, warehouse_id : warehouse_id,pagelimit:pagelimit,bill_action:bill_action},
                        dataType: "json",
                        success: function (data) {
                            $('#BillDetailsPrintData > tbody').empty();

                            if(data.bill_details =='empty' || data.bill_details == 'error'){
                             
                            $('#BillDetailsPrintData > tbody').append('<tr><td colspan="12" class="dataTables_empty"><?= lang('sEmptyTable') ?></td></tr>');    
                            }
                            else{
                                    $('.dataTables_paginate').html(data.pagination);
                                    var total = 0;
                                    var total_discount = 0;
                                    var total_tax = 0;
                                    var grand_total = 0;   
                                    var total_pay = 0;
                                    var balance = 0;var $current_amt=0;
                                    var $row_index = ($offset) ?parseInt($offset)+parseInt(1):1;

                                $.each(data.bill_details, function (a,b) 
                                {
                                   if (b.current_amt==null) {
                                    b.current_amt = 0;
                                   }
                                    total += parseFloat(b.total);
                                    total_discount += parseFloat(b.total_discount);
                                    total_tax += parseFloat(b.total_tax);
                                    grand_total += parseFloat(b.grand_total);
                                    total_pay += parseFloat(b.total_pay);
                                    balance += parseFloat(b.balance);
                                    $current_amt +=parseFloat(b.current_amt);
                                    $action =  (b.action==1)?'Edited':'Deleted';
                                    $color =  (b.action==1)?'green':'red';
                                    
                                    $t_data = '<tr class="text-center" style="color:'+$color+'"><td>'+$row_index+'</td><td>'+b.date+'</td><td>'+b.bill_number+'</td><td>'+b.grand_total+'</td><td>'+b.current_amt+'</td><td>'+$action+'</td>';
                                   
                                   $t_data +='<td class=""><div class="text-center"><div class="btn-group text-left"><button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown"> Actions <span class="caret"></span></button>        <ul class="dropdown-menu pull-right" role="menu">';
                                   if (b.action==1) {
                                    $t_data +='<li><a href="<?=admin_url('reports/restore_modified_bill/')?>'+b.id+'"><i class="fa fa-undo"></i>restore bill</a></li>';
                                   }else if (b.action==2) {
                                    $t_data +='<li><a href="<?=admin_url('reports/restore_deleted_bill/')?>'+b.id+'"><i class="fa fa-undo"></i>restore bill</a></li>';
                                   }
                                    
                                    $t_data +='</tr>';
                                   $('#BillDetailsPrintData > tbody').append($t_data);
                                    $row_index++;
                                });

                                $('#BillDetailsPrintData > tbody').append('<tr class="text-right"><td colspan=4>'+formatMoney(grand_total)+'</td><td>'+formatMoney($current_amt)+'</td><td></td><td></td></tr>');
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
    });
</script>