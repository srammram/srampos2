<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
 <link rel="stylesheet" href="<?= $assets ?>styles/jquery-ui.css">
  <script src="<?= $assets ?>js/jquery-ui.js"></script>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('edit_member_discount'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <p class="introtext"><!-- <?php echo lang('enter_info'); ?> --></p>
         <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form');
          echo admin_form_open("member_discount/edit_member_discount_card/".$id, $attrib); ?>
                    <p><?= lang('enter_info'); ?></p>
					<div class="col-lg-8">
                <div class="form-group">
                    <label>Discount </label>
					<select  name="discount" class="form-control">
					<?php   if(!empty($discount)){ foreach($discount as $row){  ?>
					<option value="<?php echo $row->id;  ?>" <?php if($discount_card->member_discount_id ==$row->id){ echo "selected";}  ?>><?php  echo $row->name;    ?></option>
					<?php   }   }  ?>
					</select>
                 
                </div>
            </div>
            <div class="col-lg-8">
                <div class="form-group">
                    <label>Prefix</label>
                    <?= form_input('prefix',$discount_card->prefix, 'class="required form-control tip" id="name" required="required"'); ?>
                </div>
            </div>
			<div class="col-lg-8">
                <div class="form-group">
                    <label>Starting Serial No </label>
                    <?= form_input('serial_no', $discount_card->serial_no, 'class="required form-control tip" id="name" required="required"'); ?>
                </div>
            </div>
			<div class="col-lg-8">
                <div class="form-group">
                    <label>No of Vouchers </label>
                    <?= form_input('vouchers',  $discount_card->no_of_vouchers, 'class="required form-control tip" id="name" required="required"'); ?>
                </div>
            </div>
			<div class="col-lg-8">
                <div class="form-group">
                    <label>Selling Price </label>
                    <?= form_input('selling_price',$discount_card->selling_price, 'class="required form-control tip" id="selling_price" required="required"'); ?>
                </div>
            </div>
			<div class="col-lg-12">
                    <div class="form-group">
                        <label>Valid Required</label><br>
                        <select name="valid_req" class="select">
                            <option value="enabled" <?php if($discount_card->valid_req == 'enabled'){ echo 'selected'; }else{ echo ''; } ?>><?=lang('enabled')?></option>
                            <option value="disbled" <?php if($discount_card->valid_req == 'disbled'){ echo 'selected'; }else{ echo ''; } ?>><?=lang('disbled')?></option>
                        </select>
                    </div>
					</div>
                
             <div class="form-group col-lg-6 date_div">
               <label>Valid From</label>
                    <div class="controls ">
                   <input type="text" name="valid_from" onkeydown="return false" id="start_date" class="form-control " placeholder="From Date "  required="required"  autocomplete="off" value="<?php echo date('Y-m-d', strtotime($discount_card->from_date)); ?>">
                    </div>
                  </div>
              <div class="form-group col-lg-6 date_div">
                <label>Valid Upto</label>
                <div class="controls">
                 <input type="text" name="valid_upto" onkeydown="return false" id="end_date"class="form-control " placeholder="To Date "  required="required"  autocomplete="off" value="<?php echo date('Y-m-d', strtotime($discount_card->to_date)); ?>">
                </div>
            </div>
            <div style="clear: both;height: 10px;"></div>
                    <div class="form-group col-lg-12">
                           <div class="box-footer"> <?php echo form_submit('add_card', lang('Save'), 'class="btn btn-primary"'); ?> </div>
                    </div>
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