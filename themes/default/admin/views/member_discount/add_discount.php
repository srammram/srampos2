<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
 <link rel="stylesheet" href="<?= $assets ?>styles/jquery-ui.css">
  <script src="<?= $assets ?>js/jquery-ui.js"></script>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('add_member_discount'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><!-- <?php echo lang('enter_info'); ?> --></p>
		<?php $attrib = array('data-toggle' => 'validator', 'role' => 'form');
          echo admin_form_open("member_discount/add_discounts", $attrib); ?>
                    <p><?= lang('enter_info'); ?></p>
            <div class="col-lg-12">
                <div class="form-group">
                    <?= lang('name', 'name'); ?>
                    <?= form_input('name', set_value('name'), 'class="required form-control tip" id="name" required="required"'); ?>
                </div>
            </div>
             <div class="form-group col-lg-6 date_div">
                <?= lang('from_date', 'from_date'); ?>
                    <div class="controls ">
                   <input type="text" name="start_date" onkeydown="return false" id="start_date"class="form-control " placeholder="From Date "  required="required"  autocomplete="off">
                    </div>
                  </div>
            <div class="form-group col-lg-6 date_div">
                <?= lang('to_date', 'to_date'); ?>
              <div class="controls">
                 <input type="text" name="end_date" onkeydown="return false" id="end_date"class="form-control " placeholder="To Date "  required="required"  autocomplete="off">
              </div>
            </div>
            <div class="form-group col-lg-6">
                <?= lang('from_time', 'from_time'); ?>
                    <div class="controls ">
                     <input type="text" name="start_time" onkeydown="return false" class="form-control time" placeholder="From  Time" id="start_time" required="required"  autocomplete="off">
                    </div>
                  </div>
            <div class="form-group col-lg-6">
                <?= lang('to_time', 'to_time'); ?>
              <div class="controls">
                 <input type="text" name="end_time"  onkeydown="return false" class="form-control time" placeholder="To  Time" id="end_time" required="required"  autocomplete="off">
              </div>
            </div>
            <div class="form-group col-lg-12">
               
              <div class="controls">
                <?= lang('Apply_on', 'Apply_on'); ?> : 
                <label><input type="checkbox" name="weekdays[]" class="form-control r_weekdays" required="required" data-code="mon" value="Monday" autocomplete="off" checked="checked">&nbsp;&nbsp;Monday</label>
                <label><input type="checkbox" name="weekdays[]" class="form-control r_weekdays" required="required" data-code="tue" value="Tuesday" autocomplete="off" checked="checked">&nbsp;&nbsp;Tuesday</label>
                <label><input type="checkbox" name="weekdays[]" class="form-control r_weekdays" required="required" data-code="wed" value="Wednesday" autocomplete="off" checked="checked">&nbsp;&nbsp;Wednesday</label>
                <label><input type="checkbox" name="weekdays[]" class="form-control r_weekdays" required="required" data-code="thu" value="Thursday" autocomplete="off" checked="checked">&nbsp;&nbsp;Thursday</label>
                <label><input type="checkbox" name="weekdays[]" class="form-control r_weekdays" required="required" data-code="fri" value="Friday" autocomplete="off" checked="checked">&nbsp;&nbsp;Friday</label>
                <label><input type="checkbox" name="weekdays[]" class="form-control r_weekdays" required="required" data-code="sat" value="Saturday" autocomplete="off" checked="checked">&nbsp;&nbsp;Saturday</label>
                <label><input type="checkbox" name="weekdays[]" class="form-control r_weekdays" required="required" data-code="sun" value="Sunday" autocomplete="off" checked="checked">&nbsp;&nbsp;Sunday</label>
              </div>
            </div>
            <div class="form-group col-lg-12">
                <div class="discount-container col-lg-12">
                <div class="dis-row">
                   <div class="col-md-2" style="width:13%;top: 25px;position: relative;">
                        <div class="form-group">
                        <label style="padding:7px;">
                                <input type="checkbox" name="status" id="activate-discount" value="1">&nbsp&nbspActive
                            </label>
                        </div>
                    </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <?= lang('discount', 'discount'); ?>
                        <input type="text" name="discount" value="" class="form-control tip numberonly discount">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <?= lang('discount_type', 'discount_type'); ?></br>
                        <select name="discount_type" class="select">
                            <option value="percentage"><?=lang('percentage')?></option>
                            <option value="amount"><?=lang('amount')?></option>
                        </select>
                    </div>
                </div>
                </div>
            </div>
            <div style="clear: both;height: 10px;"></div>
                    <div class="form-group col-lg-12">
                           <div class="box-footer"> <?php echo form_submit('add__discounts', lang('add_discounts'), 'class="btn btn-primary"'); ?> </div>

                <?= form_close(); ?>

            </div>

        </div>
    </div>
</div>

<script>
	$(".numberonly").keypress(function (event){
    if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
        event.preventDefault();
    }
    });
</script>
<script>
    $('#end_date').datepicker({
        dateFormat: "yy-mm-dd" ,
        minDate:  0,      
    });
	$('#start_date').datepicker({
        dateFormat: "yy-mm-dd" ,
        minDate:  0,      
    });
</script>