<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
  <link rel="stylesheet" href="<?= $assets ?>styles/jquery-ui.css">
  <script src="<?= $assets ?>js/jquery-ui.js"></script>
<?= admin_form_open('nightaudit/actions', 'id="action-form"') ?>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-th-list"></i><?= lang('night_audit'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <div class="table-responsive">
                	<?php
					
					$total_sales = 0;
					$complete_sales = 0;
					$pending_sales = 0;
					if(!empty($sales)){
					foreach($sales as $sales_row){
						$total_sales++;
						if($sales_row->sale_status == 'Closed'){
							$complete[] = $sales_row->grand_total;
							$complete_sales++;
						}elseif($sales_row->sale_status == 'Process'){
							$pending[] = $sales_row->grand_total;
							$pending_sales++;
						}
						$total[] = $sales_row->grand_total;
					}
					}
					$complete_sales;
					$pending_sales;
					$total_amount = (!empty($total)) ? array_sum($total) : 0.00;
					$complete_amount = (!empty($complete)) ? array_sum($complete) : 0.00;
					$pending_amount = (!empty($pending)) ? array_sum($pending) : 0.00;
					?>
					<div class="btn btn-success" style="float: right;">
					<?php
						$datetime = strtotime($last_date);
						if(!empty($datetime)){
							$mysqldate = 'Last Audited Date : '.date("d-m-Y", $datetime);
						}else{
							$mysqldate = 'Start Night Audit Today : '.date('d-m-Y');
						}
 					?>
					  	<h3 > <span class="title"><?php echo $mysqldate; ?></span></h3>
					</div>
                    
                	<div class="form-group">
                        <div class="col-md-2">
                            <label>Branch</label>
                        </div>
                        <div class="col-md-4">
                            <select name="warehouses_id" id="warehouses_id" class="form-control">
                            	<?php
								foreach($warehouses as $warehouses_row){
								?>
                            	<option value="<?php echo $warehouses_row->id; ?>"><?php echo $warehouses_row->name; ?></option>
                                <?php
								}
								?>
                            </select>
                         </div> 
                         
                    </div>
                    <div class="clearfix"></div>
                    <br>
                	<div class="form-group">
                        <div class="col-md-2">
                            <label>Dates</label>
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control" autocomplete="off" id="datepicker" name="nightaudit_date" value="<?php echo date('Y-m-d'); ?>">
                         </div> 
                         
                    </div>
                    <?php  
                                       
                    if((!empty($p)) && ($p->blind_night_audit == 0))  {
					?>
                        <input type="hidden" name="total_sales" id="total_sales" value="<?=$total_sales;?>">
						<input type="hidden" name="total_amount" id="total_amount" value="<?=$this->sma->formatMoney($total_amount);?>">
						<input type="hidden" name="complete_sales" id="complete_sales" value="<?=$complete_sales;?>">
						<input type="hidden" name="complete_amount" id="complete_amount" value="<?=$this->sma->formatMoney($complete_amount);?>">
						<input type="hidden" name="pending_sales" id="pending_sales" value="<?=$pending_sales;?>">
						<input type="hidden" name="pending_amount" id="pending_amount" value="<?=$this->sma->formatMoney($pending_amount);?>">
                    <?php
					}else{

                    ?>
                    <div class="clearfix"></div>
                    <br>
                    <div class="nightaudit">
                        <div class="col-md-6" id="night_audit">
                        <table class="table" >
                            <thead>
                                <tr>
                                    <th>Details</th>
                                    <th>Reports</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Total Sales</td>
                                    <td><span id="total_sales_span"><?=$total_sales;?></span><input type="hidden" name="total_sales" id="total_sales" value="<?=$total_sales;?>"></td>
                                </tr>
                                <tr>
                                <?php 
                                    $default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
                                    
                                    $default_currency_code = $default_currency_data->code;    
                                 ?>
                                    <td>Total Amount (<?php echo $default_currency_code ?>)</td>
                                    <td><span id="total_amount_span"><?=$this->sma->formatMoney($total_amount);?></span><input type="hidden" name="total_amount" id="total_amount" value="<?=$this->sma->formatMoney($total_amount);?>"></td>
                                </tr>
                                <tr>
                                    <td>Complete Sales</td>
                                    <td><span id="complete_sales_span"><?=$complete_sales;?></span><input type="hidden" name="complete_sales" id="complete_sales" value="<?=$complete_sales;?>"></td>
                                </tr>
                                <tr>
                                    <td>Complete Amount (<?php echo $default_currency_code ?>)</td>
                                    <td><span id="complete_amount_span"><?=$this->sma->formatMoney($complete_amount);?></span><input type="hidden" name="complete_amount" id="complete_amount" value="<?=$this->sma->formatMoney($complete_amount);?>"></td>
                                </tr>
                                <tr>
                                    <td>Pending Sales</td>
                                    <td><span id="pending_sales_span"><?=$pending_sales;?></span><input type="hidden" name="pending_sales" id="pending_sales" value="<?=$pending_sales;?>"></td>
                                </tr>
                                <tr>
                                    <td>Pending Amount (<?php echo $default_currency_code ?>)</td>
                                    <td><span id="pending_amount_span"><?=$this->sma->formatMoney($pending_amount);?></span><input type="hidden" name="pending_amount" id="pending_amount" value="<?=$this->sma->formatMoney($pending_amount);?>"></td>
                                </tr>
                                
                            </tbody>
                        </table>
                        </div>
                    </div>
                    <?php  }?>
                    
                    
                  
                    
                    <div class="clearfix"></div>
                    <br>
                    
                    <div class="form-group nightaudit_check" <?php if($status == 'yes'){ ?> style="display:none" <?php }else{ ?> style="display:block" <?php } ?>>
                        
                        <div class="col-md-8">
                            <input type="checkbox" class="form-control" value="1" name="nightaudit" id="nightaudit"> Night audit status complete process. once check process also complete.
                         </div> 
                         
                    </div>
                   
                    <div class="clearfix"></div>
                    <br>
                    <div class="col-xs-12" style="padding: 0;">
                        <div class="col-md-2">
                        <input type="hidden" name="form_action" value="" id="form_action"/>
                        <input type="submit" class="btn btn-primary btn-block " <?php if($status == 'yes'){ ?> style="display:none" <?php }else{ ?> style="display:block" <?php } ?> value="Submit" id="action-form-submit" >
                        </div>

                        <div class="col-md-2">                        
                        <input type="button" class="btn btn-primary btn-block print_bill" <?php if($status == 'yes'){ ?> style="display:none" <?php }else{ ?> style="display:block" <?php } ?> value="Print" id="print" >
                        </div>
                    </div>
                   <!--   <div class="col-xs-2" style="padding: 0;">
                        <div class="btn-group-vertical btn-block">
                        <button type="button" class="btn btn-primary btn-block print_bill" style="height:40px;" id="">
                            <i class="fa fa-print" ></i><?=lang('bill');?> 
                            </button>
                            <input type="hidden"  class="bill" value="<?php echo $split_order->id; ?>">
                        </div>
                    </div> -->
                    
                </div>
            </div>
        </div>
    </div>
</div>


<?= form_close() ?>

  <script>

$(document).ready(function ($) { 
$("#datepicker").val('');  
    var array = <?php  echo  $dates ? json_encode($dates) : json_encode(explode(',',date('Y-m-d'))); ?>;
	
    $("#datepicker").datepicker({        
        dateFormat: "yy-mm-dd",
        maxDate : -1,
        beforeShowDay: function (date) {
            if ($.inArray($.datepicker.formatDate('yy-mm-dd', date), array) > -1) {
                console.log('in')
                return [true, "", "Available"];
            } else {
                console.log('n')
                return [false, '', "Not Available"];
            }
        }
    });    
});

	 $("#datepicker").on("change",function(){
        var warehouse = $('#warehouses_id').val();
		var dates = $(this).val();
		$.ajax({
			type: "get",
			url:"<?=admin_url('nightaudit/getNightauditData');?>",                
			data: {dates: dates, warehouses_id: warehouse},
			dataType: "json",
			success: function (data) {
				$('#total_sales_span').text(data.total_sales);
				$('#total_sales').text(data.total_sales);
				$('#total_amount_span').text(data.total_amount);
				$('#total_amount').text(data.total_amount);
				$('#complete_sales_span').text(data.complete_sales);
				$('#complete_sales').text(data.complete_sales);
				$('#complete_amount_span').text(data.complete_amount);
				$('#complete_amount').text(data.complete_amount);
				$('#pending_sales_span').text(data.pending_sales);
				$('#pending_sales').text(data.pending_sales);
				$('#pending_amount_span').text(data.pending_amount);
				$('#pending_amount').text(data.pending_amount);

				if(data.before_status == 'no'){
                    bootbox.alert('Please Complte all previous Date Audit');
                    $('.nightaudit_check').hide();
                    $('#action-form-submit').hide();
                    $('#print').hide();
                     return false;
                }else{
                    $('.nightaudit_check').show();
                    $('#action-form-submit').show();
                    $('#print').show();
                }

				if(data.status == 'yes'){
                    $('.nightaudit_check').hide();
                    $('#action-form-submit').hide();
                    $('#print').hide();
                }else{
                    $('.nightaudit_check').show();
                    $('#action-form-submit').show();
                    $('#print').show();
                }
			}    
		})
    });

	
	$(document).on('change', '#warehouses_id', function(){
		var warehouse = $('#warehouses_id').val();
		var date = $('#nightaudit_date').val();
		
		$.ajax({
			type: "get",
			url:"<?=admin_url('nightaudit/getNightauditData');?>",                
			data: {dates: date, warehouses_id: warehouse},
			dataType: "json",
			success: function (data) {
				$('#total_sales_span').text(data.total_sales);
				$('#total_sales').text(data.total_sales);
				$('#total_amount_span').text(data.total_amount);
				$('#total_amount').text(data.total_amount);
				$('#complete_sales_span').text(data.complete_sales);
				$('#complete_sales').text(data.complete_sales);
				$('#complete_amount_span').text(data.complete_amount);
				$('#complete_amount').text(data.complete_amount);
				$('#pending_sales_span').text(data.pending_sales);
				$('#pending_sales').text(data.pending_sales);
				$('#pending_amount_span').text(data.pending_amount);
				$('#pending_amount').text(data.pending_amount);
				
				if(data.status == 'yes'){
					$('.nightaudit_check').hide();
					$('#action-form-submit').hide();
				}else{
					$('.nightaudit_check').show();
					$('#action-form-submit').show();
				}
			}    
		});
		
	});
		
$(document).ready(function () {
       $(document).on('click', '#print', function () {
        var select_date = $("#datepicker").val();
        if(select_date)
        {
           Popup($('#night_audit').html());
        }
        else{
            bootbox.alert('Please Select Date');
            return false;
        }   
                                               
        });


    $(document).on('click', '#action-form-submit', function () {        
        var select_date = $("#datepicker").val();
        if(select_date)
        {            
            $(this).val('<?=lang('loading');?>').attr('disabled', true);                
            $('#action-form').submit();
        }     
        else{
            bootbox.alert('Please Select Date');
            return false;
        }   
    }); 
});        
    
  
    function Popup(data) {
        var mywindow = window.open('', 'sma_pos_print', 'height=500,width=300');
        mywindow.document.write('<html><head><title>Print</title>');
        mywindow.document.write('<link rel="stylesheet" href="<?=$assets?>styles/helpers/bootstrap.min.css" type="text/css" />');
        mywindow.document.write('<link rel="stylesheet" href="<?=$assets?>styles/helpers/print.css" type="text/css" />');
        mywindow.document.write('</head><body >');
        mywindow.document.write(data);
        mywindow.document.write('</body></html>');
        mywindow.print();
        mywindow.close();
        return true;
    }
    
  </script>
