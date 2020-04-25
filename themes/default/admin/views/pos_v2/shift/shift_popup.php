<?php
$current_shift = $this->currentShift;


$defaultcurdata = $this->defaultcurdata;
$exitShift = $this->exitShift;
$counter_cash = $this->site->lastCounter($this->till_id);


?>    

<div class="modal" id="LoadOpeningCashModal" tabindex="-1" role="dialog" >
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
        	
        	<?php
			if($this->currentShift == 0){
				
			?>
            <div class="modal-header">			
            	 <a href="<?= base_url('pos/login/logout') ?>" class="pull-right btn btn-primary"><?=lang('logout')?></a>
				 <h4 class="modal-title" id="myModalLabel">No Shift Available </h4>
		    </div>
            <?php
			}else{
				/*if($this->Owner || !$this->Admin){
					$GP['pos-shift_create'] = 1;
				}else{
					$GP['pos-shift_create'] = $GP['pos-shift_create'];	
				}*/
				if($GP['pos-shift_create'] == 1 || $this->Owner || $this->Admin){
			?>
		    <div class="modal-header">			
            	 <a href="<?= base_url('pos/login/logout') ?>" class="pull-right btn btn-primary"><?=lang('logout')?></a>
				 <h4 class="modal-title" id="myModalLabel">Shift Create</h4>
		    </div>
            <form method="post" id="create-shift-form">
            
		    <div class="modal-body">
            	<input type="hidden" name="warehouse_id" value="<?= $this->session->userdata('warehouse_id') ?>">
                <input type="hidden" name="till_id" value="<?= $this->till_id ?>">
                <input type="hidden" name="shift_from_time" value="<?= $current_shift->from_time ?>">
                <input type="hidden" name="shift_to_time" value="<?= $current_shift->to_time ?>">
                <input type="hidden" name="shift_start_time" value="<?= date('Y-m-d H:i:s') ?>">
                <input type="hidden" name="shiftmaster_id" value="<?= $current_shift->id ?>">
		 		<?php
				if($this->Settings->shift_opencash_setting_pos == 1){
				?>
                <div class="form-group col-lg-12">
					<h4><?= lang('opening_cash_type', 'opening_cash_type'); ?></h4>
                    
                    <div class="clearfix"></div>
                    <label for="opening_cash_type-none" class="padding03"><input type="radio" value="0" id="opening_cash_type-default" class="opening_cash_type" name="opening_cash_type" <?php echo ($this->Settings->opening_cash_type==0) ? "checked" : ''; ?>>
                    <?= lang('Default') ?></label>&nbsp;&nbsp;
                    <input type="radio" value="1" id="opening_cash_type-carry_forward" class="opening_cash_type" name="opening_cash_type" <?php echo ($this->Settings->opening_cash_type==1) ? "checked" : ''; ?>>
                    <label for="opening_cash_type-carry_forward" class="padding03"><?= lang('carry_forward') ?></label>&nbsp;&nbsp;
                    <input type="radio" value="2" id="opening_cash_type-fixed_opening" class="opening_cash_type" name="opening_cash_type" <?php echo ($this->Settings->opening_cash_type==2) ? "checked" : ''; ?>>
                    <label for="opening_cash_type-fixed_opening" class="padding03"><?= lang('fixed_opening') ?></label>
                    
                </div>
                <?php 
					$disabled = $this->Settings->opening_cash_type == 2 ? '' : 'disabled';  
					$shift_disabled = $this->Settings->opening_cash_type == 2 ? '' : '';  
				?>
                <?php foreach($this->currencies as $c => $cur){ 
				$small_code = strtolower($cur->code);
					if($cur->code == 'USD'){
						 if($this->Settings->opening_cash_type == 1){
							$cash_USD_received = $counter_cash->cash_USD_received ? $counter_cash->cash_USD_received : 0;
							if($this->Settings->shift_opencash_next_days_continue == 1){
								$cash_usd = $cash_USD_received;
							}else{
								if($counter_cash->shift_start == date('d/m/Y')){
									$cash_usd = $cash_USD_received;
								}else{
									$cash_usd = 0;
								}
							}
							
						}elseif($this->Settings->opening_cash_type == 2){
							$cash_usd = $this->Settings->opening_cash_usd;
						}else{
							$cash_usd = 0;
						}
					
				?>
                <div class="col-md-6 ">
                    <div class="form-group">
                        <label class="control-label" for="opening_cash_usd"><?= lang("opening_cash_".$small_code)?></label>
                        <?= form_input('opening_cash_'.$small_code, $cash_usd, ' onkeyup="checkNum(this)" class="form-control opening numberonly"  '.$disabled.' id="opening_cash_'.$small_code.'"'); ?>
                    </div>
                </div>
                <?php
				}elseif($cur->code == 'KHR'){
					 if($this->Settings->opening_cash_type == 1){
						$cash_KHR_received = $counter_cash->cash_KHR_received ? $counter_cash->cash_KHR_received : 0;
						//$cash_khr = $counter_cash->CUR_KHR + $cash_KHR_received;
						
						if($this->Settings->shift_opencash_next_days_continue == 1){
							$cash_khr = $cash_KHR_received;
						}else{
							if($counter_cash->shift_start == date('d/m/Y')){
								$cash_khr = $cash_KHR_received;
							}else{
								$cash_khr = 0;
							}
						}
						
					}elseif($this->Settings->opening_cash_type == 2){
						$cash_khr = $this->Settings->opening_cash_khmr;
					}else{
						$cash_khr = 0;
					}
				?>
                <div class="col-md-6 ">
                    <div class="form-group">
                        <label class="control-label" for="opening_cash_usd"><?= lang("opening_cash_".$small_code)?></label>
                        <?= form_input('opening_cash_'.$small_code, $cash_khr, 'onkeyup="checkNum(this)" class="form-control opening numberonly"  '.$disabled.' id="opening_cash_'.$small_code.'"'); ?>
                    </div>
                </div>
                
                <?php
						}
					}				
				}
				?>
                <div class="col-md-12">
                	<table class="table table-bordered table-striped sales_order_se">
						
						
						<tbody>
							<tr>
							   <td colspan="2">Shift Name :</td>
							   <td ><?=$current_shift->name?> [<?=$current_shift->from_time .'to'.$current_shift->to_time?>]</td>
                            </tr>
                            <tr>
							   <td colspan="2">Open Til Name :</td>
							   <td ><?= $this->till_name ?></td>
                            </tr>
                            <tr>
								<td colspan="2">User Name :</td>
								<?php if($this->Settings->shift_user_handling) : ?>
                                <?php $shiftusers = $this->site->getShiftUsers(); ?>
                                    <td colspan="2"><select name="user_id" class="shift-user select form-control" style="width:30% !important">
                                        <?php foreach($shiftusers as $k => $su) { ?>
                                        <option value="<?=$su->id?>"><?=$su->username?></option>
                                        <?php } ?>
                                    </select>
                                    </td>
                                <?php else : ?>
                                <td>All Users</td>
                                <?php endif; ?>
						    </tr>
                            <tr>
                            	<td colspan="2">Opening Type:</td><td id="opening_type_name"></td>
                            </tr>
                            <tr>
                            	<td colspan="2">Opening Cash:</td>
                            	
                                <td>
                                <span class="total-op-cash"></span><input type="hidden" name="total_cash" class="total-op-cash-val">
                                <?php foreach($this->currencies as $c => $cur){ 
								$code = 'CUR_'.$cur->code;
								?>
                                 <strong class="pull-right"><?=$cur->code?> : 
                                 <?php
								 if($cur->code == 'USD'){
									 if($this->Settings->opening_cash_type == 1){
										$cash_USD_received = $counter_cash->cash_USD_received ? $counter_cash->cash_USD_received : 0;
										if($this->Settings->shift_opencash_next_days_continue == 1){
											$cash_usd = $cash_USD_received;
										}else{
											if($counter_cash->shift_start == date('d/m/Y')){
												$cash_usd = $cash_USD_received;
											}else{
												$cash_usd = 0;
											}
										}
										
									}elseif($this->Settings->opening_cash_type == 2){
										$cash_usd = $this->Settings->opening_cash_usd;
									}else{
										$cash_usd = 0;
									}
								 ?>
                                 <input type="hidden" id="<?= $cur->code ?>" value="<?= $cash_usd ?>">
                                 <input type="text" name="cash[<?=$code?>]" onkeyup="checkNum(this)" class="numberonly form-control cur-val" data-rate="<?=$cur->rate?>" value="<?= $cash_usd ?>" data-code="<?=$cur->code?>" default-cur-rate="<?=$defaultcurdata->rate?>" <?= $shift_disabled ?>>
                                 <?php
								 }elseif($cur->code == 'KHR'){
									 if($this->Settings->opening_cash_type == 1){
										$cash_KHR_received = $counter_cash->cash_KHR_received ? $counter_cash->cash_KHR_received : 0;
										//$cash_khr = $counter_cash->CUR_KHR + $cash_KHR_received;
										
										if($this->Settings->shift_opencash_next_days_continue == 1){
											$cash_khr = $cash_KHR_received;
										}else{
											if($counter_cash->shift_start == date('d/m/Y')){
												$cash_khr = $cash_KHR_received;
											}else{
												$cash_khr = 0;
											}
										}
										
									}elseif($this->Settings->opening_cash_type == 2){
										$cash_khr = $this->Settings->opening_cash_khmr;
									}else{
										$cash_khr = 0;
									}
								 ?>
                                 <input type="hidden" id="<?= $cur->code ?>" value="<?= $cash_khr ?>">
                                 <input type="text" name="cash[<?=$code?>]" onkeyup="checkNum(this)" class="numberonly form-control cur-val" data-rate="<?=$cur->rate?>" value="<?= $cash_khr ?>" data-code="<?=$cur->code?>" default-cur-rate="<?=$defaultcurdata->rate?>" <?= $shift_disabled ?>>
                                 <?php
								 }
								 ?>
                                 </strong>
                                <?php } ?>
                                </td>
                            </tr>
                        </tbody>
					</table>
                </div>
		    </div>
            <div class="clearfix"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="create-shift">Allow Shift</button>
            </div>
            </form>
            <?php
				}else{
			?>
            <div class="modal-header">			
            	 <a href="<?= base_url('pos/login/logout') ?>" class="pull-right btn btn-primary"><?=lang('logout')?></a>
				 <h4 class="modal-title" id="myModalLabel">Access Denied! You Don't Have Right To Access The Requested Page. If You Think, It's By Mistake, Please Contact Administrator.</h4>
		    </div>
            <?php	
				}
			}
			?>
            
		</div>
	</div>
</div>

<div class="modal" id="LoadShiftContinue" tabindex="-1" role="dialog" >
	<div class="modal-dialog modal-lg ShiftContinue">
		<div class="modal-content">
		    <div class="modal-header">			
			<h4 class="modal-title" id="myModalLabel">Continue Shift - Authorizaion</h4>
		    </div>
		    <div class="modal-body">
		    <div class="col-sm-12 text-center">
		    <div class="login_form">
			<form method="post" id="continue-shift-form">

				<div class="input-group col-sm-12">
  						<span><button type="button" class="btn btn-primary center-block" id="continue-shift">Approve</button></span>
  					</div>
			</form>
				</div>	
	    		</div>		
	    
	    
		    </div>
		</div>
	</div>
</div>

<?php if($this->till_id == 0 && $this->Settings->open_til_enable == 1){ ?>
	<script>
	    var site_url = '<?= base_url(); ?>';
        $(document).ready(function(){
			
			bootbox.alert({
				message: "Open Til missing",
				size: 'large',
				callback: function(){	
					window.location = site_url+'pos/login/logout';		
				}
			});
        })
        
    </script>
<?php } ?>

<?php ?>
<script>

/*bootbox.confirm({
			closeButton: false,
			message: "Shift Time ends. Do you want to continue?",
			buttons: {
				confirm: {
					label: 'Yes',
					className: 'btn-success'
				},
				cancel: {
					label: 'No',
					className: 'btn-danger'
				}
			},
			callback: function (result) {
				
					if(result == true){
						window.location.href = '<?= base_url('pos/shift/continue_shift') ?>';	
					}else{
						window.location.href = '<?= base_url('pos/shift/dont_continue_shift') ?>';	
					}
				}
			});*/
</script>
<?php ?>

<?php if($this->isShiftCreated == 1 && $this->Settings->shift_enable == 1 && $this->till_id != 0 && $this->Settings->open_til_enable == 1){ 
$start_hr = explode(':',$current_shift->from_time);$end_hr = explode(':',$current_shift->to_time);
?>
<script>


var shift_starts = '<?=$current_shift->from_time?>';
var shift_ends = '<?=$current_shift->to_time?>';

var exit_starts = '<?=$exitShift->shift_from_time?>';
var exit_ends = '<?=$exitShift->shift_to_time?>';

var shift_end_hr = '<?=$end_hr[0]?>';
var shift_start_hr = '<?=$start_hr[0]?>';
var continued_shift = '<?=$exitShift->continued_shift?>';
var shift_id = '<?=$exitShift->id?>';
var DontContinueShift = false;

DontContinueShift = '<?=$this->session->userdata('dont_continue_shift');?>';

var $shifts_ends_alert=false;

if(shift_starts == exit_starts && shift_ends == exit_ends)
{

setInterval(function(){
	
	var d = new Date();
	var $sec = d.getSeconds();
	$sec = ($sec.toString().length==1)?'0'+$sec:$sec;
	var $min = d.getMinutes();
	$min = ($min.toString().length==1)?'0'+$min:$min;
	var $hrs = d.getHours();
	$hrs = ($hrs.toString().length==1)?'0'+$hrs:$hrs;
	 
	$now = $hrs+':'+$min+':'+$sec;
	$shift_end = shift_ends;
	$t_start='';
	$t_end ='';$now_date='';
	$shiftend_popup = false;
	if (shift_ends<shift_starts) {
	    
	    $today = new Date();
	    $yesterday = new Date($today);
	    $yesterday.setDate($today.getDate() - 1);
	    $y_d = $yesterday.getDate();
	    $y_m = $yesterday.getMonth()+1;
	    $y_y = $yesterday.getFullYear();
	    
	    
	    $nextday = new Date($today);
	    $nextday.setDate($today.getDate() + 1);
	    $n_d = $nextday.getDate();
	    $n_m = $nextday.getMonth()+1;
	    $n_y = $nextday.getFullYear();
	    
	    $t_d = $today.getDate();
	    $t_m = $today.getMonth()+1;
	    $t_y = $today.getFullYear();
	    
	    $time_p1 = [];
	    shift_start_hr = parseInt(shift_start_hr);
	    shift_end_hr = parseInt(shift_end_hr);
	    for($i=shift_start_hr;$i<=23;$i++){
		    $i = ($i.toString().length==1)?0+$i:$i;
			$time_p1.push($i);
	    }
	    $time_p2 = [];
	    for($i=shift_end_hr;$i>=0;$i--){
		    $i = ($i.toString().length==1)?0+$i:$i;
			$time_p2.push($i);
	    }
	    $hrs = parseInt($hrs);
	    if ($time_p1.indexOf($hrs)!=-1) {
			$t_start =  $t_y+'-'+$t_m+'-'+$t_d+' '+shift_starts;
			$t_end =  $n_y+'-'+$n_m+'-'+$n_d+' '+shift_ends;
			var $t_start = new Date($t_start);
			var $t_end = new Date($t_end);
			$now_date  = new Date($t_y+'-'+$t_m+'-'+$t_d+' '+$now);
	    }else if ($time_p2.indexOf($hrs)!=-1) {
			$t_start =  $y_y+'-'+$y_m+'-'+$y_d+' '+shift_starts;
			$t_end =  $t_y+'-'+$t_m+'-'+$t_d+' '+shift_ends;
			var $t_start = new Date($t_start);
			var $t_end = new Date($t_end);
			$now_date  = new Date($t_y+'-'+$t_m+'-'+$t_d+' '+$now);
	    }else{
			$shiftend_popup = true;
		
	    }	 
	}
	
	
		
		
	if (!$shifts_ends_alert && !DontContinueShift && continued_shift==0) {
		
		if ((shift_starts<shift_ends && $now > $shift_end) ||(!$shiftend_popup && shift_ends<shift_starts && $now_date>$t_end) || $shiftend_popup){	    
		$shifts_ends_alert = true;
		bootbox.confirm({
			closeButton: false,
			message: "Shift Time ends. Do you want to continue?",
			buttons: {
				confirm: {
					label: 'Yes',
					className: 'btn-success'
				},
				cancel: {
					label: 'No',
					className: 'btn-danger'
				}
			},
			callback: function (result) {
				
					if(result == true){
						window.location.href = '<?= base_url('pos/shift/continue_shift') ?>';	
					}else{
						window.location.href = '<?= base_url('pos/shift/dont_continue_shift') ?>';	
					}
				}
			});
		}
	}
}, 1000);


}else{
	if (!$shifts_ends_alert && !DontContinueShift && continued_shift==0) {
		bootbox.confirm({
			closeButton: false,
			message: "Shift Time ends. Do you want to continue?",
			buttons: {
				confirm: {
					label: 'Yes',
					className: 'btn-success'
				},
				cancel: {
					label: 'No',
					className: 'btn-danger'
				}
			},
			callback: function (result) {
				
					if(result == true){
						window.location.href = '<?= base_url('pos/shift/continue_shift') ?>';	
					}else{
						window.location.href = '<?= base_url('pos/shift/dont_continue_shift') ?>';	
					}
				}
			});
	}
}
</script>


<?php } 




?>


<?php if($this->isShiftCreated == 0 && $this->Settings->shift_enable == 1 && $this->iscontinueShift == 0 && $this->till_id != 0 && $this->Settings->open_til_enable == 1){ ?>
<script>

	
function calculate_opcash_defaultcurrency() {
	$default_op_cash = 0;
	$('.cur-val').each(function(n,v){
	    $input_val = $(this).val();
	    $this_default_cur_rate = $(this).attr('default-cur-rate');
	    $rate = $(this).attr('data-rate');
	    $this_cash = ($input_val*$rate)/$this_default_cur_rate;
	    $default_op_cash += parseFloat($this_cash);
	});
	
	$('.total-op-cash').text($default_op_cash.toFixed(2));
	$('.total-op-cash-val').val($default_op_cash);
	
}

$(document).ready(function(){
	
	<?php
	
	if($this->dontcontinueShift == 0){
	?>
	$('#LoadOpeningCashModal').modal({backdrop: 'static', keyboard: false});
	<?php
	}else{
	?>
	window.location.href = '<?= base_url('pos/shift/shift_settlement') ?>';
	<?php
	}
	?>
	var opening_type_name= '<?php if($this->Settings->opening_cash_type == 1){ echo 'Carry Forward'; }elseif($this->Settings->opening_cash_type == 2){ echo 'Fixed Opening'; }else{ echo 'Default'; } ?>';
	$('#opening_type_name').text(opening_type_name);
	calculate_opcash_defaultcurrency();
	$('.cur-val').keyup(function(){
	    calculate_opcash_defaultcurrency()
	});
});

$(document).on('click', '.opening_cash_type', function(event) {
	//alert($(this).val());
	if ($(this).val() == 2) {
			$('#opening_type_name').text('Fixed Opening');
			$('.opening').attr('disabled', false);
			//$('.cur-val').attr('readonly', false);
			<?php foreach($this->currencies as $c => $cur){ ?>
			$('input[data-code=<?= $cur->code ?>]').val(0);
			<?php } ?>
			calculate_opcash_defaultcurrency();
	}else if ($(this).val() == 1) {
			$('#opening_type_name').text('Carry Forward');
			$('.opening').val(0);
			$('.opening').attr('disabled', true);
			//$('.cur-val').attr('readonly', true);
			<?php
			if($this->Settings->opening_cash_type == 1){
			?>
			
			<?php foreach($this->currencies as $c => $cur){ ?>
			$('input[data-code=<?= $cur->code ?>]').val($('#<?= $cur->code ?>').val());
			<?php } ?>
			<?php
			}else{
			?>
			<?php foreach($currencies as $c => $cur){ ?>
			$('input[data-code=<?= $cur->code ?>]').val(0);
			<?php } ?>
			<?php
			}
			?>
			calculate_opcash_defaultcurrency();
			
		} else {
			$('#opening_type_name').text('Default');
			$('.opening').val(0);
			$('.opening').attr('disabled', true);
			//$('.cur-val').attr('readonly', true);
			<?php foreach($this->currencies as $c => $cur){ ?>
			$('input[data-code=<?= $cur->code ?>]').val(0);
			<?php } ?>
			calculate_opcash_defaultcurrency();
		}
	
});
	
<?php foreach($this->currencies as $c => $cur){ ?>
	
$(document).on('change', '#opening_cash_<?= strtolower($cur->code) ?>', function(){
	var opening_cash = $(this).val();
	$('input[data-code=<?= $cur->code ?>]').val(opening_cash);
	calculate_opcash_defaultcurrency();
});
<?php
}
?>
$(document).on('click', '#create-shift', function(){
	bootbox.confirm("Are you sure want to Create Shift?", function(result){
	    if(result){
			$.ajax({
				type: 'post',
				url: '<?= base_url('pos/shift/create_shift') ?>',
				data:$('#create-shift-form').serialize(),
				success: function (res) {
					
						bootbox.alert({
							message: "Shift has been created successfully. Click OK to logout and login to proceed sales",
							callback: function (response) {
								window.location.href = '<?= base_url('pos/login/logout') ?>';
							}
						})
					
				}
			});
	    }
	});
})

function checkNum(input) {
	input.value = input.value.match(/^\d+\d{0,1}/);  
}
</script>
<?php } ?>