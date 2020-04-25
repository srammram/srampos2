<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
 
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('add_issue_discount'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <p class="introtext"><!-- <?php echo lang('enter_info'); ?> --></p>
            <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form');
          echo admin_form_open("member_discount/add_issue_card", $attrib); ?>
                    <p><?= lang('enter_info'); ?></p>
            <div class="col-lg-8">
                <div class="form-group">
				<label>Discount</label>
                  <select name="discount_id" class="form-control  select discount"  id="dicount_id"  placeholder="<?= lang("select") . ' ' . lang("select Discount") ?>" required="required">
                   <option value=""></option>
                   		<?php foreach($discount as $row){  ?>
                      <option value="<?php echo $row->id; ?>"><?php echo $row->name; ?></option>
                      <?php } ?>
                    </select>
                </div>
            </div>
			<div class="form-group col-lg-8 ">
					<label>Discount Card</label>
                  <div class="controls">
				  <?php  
                   echo form_dropdown('discount_card', $res, '', 'class="form-control" data-placeholder="' . lang("select") . ' ' . lang("card") . '" id="discount_card" required="required" style="width:100%;" ');
				   ?>
                   </div>
                </div>
				<div class="form-group col-lg-8 ">
					<label>Customer</label>
                  <div class="controls">
				  <select name="customer" class="form-control  select customer"  placeholder="<?= lang("select") . ' ' . lang("select customer") ?>" required="required" >
                   <option value=""></option>
                   		<?php foreach($customers as $row){  ?>
                      <option value="<?php echo $row->id; ?>"><?php echo $row->name; ?></option>
                      <?php } ?>
                    </select>
                   </div>
                </div>
				
				   <div class="col-lg-12">
			<div class="col-lg-4">
                <div class="form-group">
                    <label>Selling Price </label>
                    <?= form_input('selling_price1', set_value('selling_price'), 'class="required form-control tip selling_price" id="selling_price"  readonly'); ?>
							<input type="hidden" name="selling_price" class="selling_price">
                </div>
            </div>
			<div class="col-lg-4">
                <div class="form-group">
                    <label>Valid Upto </label>
                    <?= form_input('valid_upto1', set_value('valid_upto'), 'class="required form-control tip valito" id="valito" required="required" readonly'); ?>
							<input type="hidden" name="valito" class="valito">
                </div>
            </div>
			<div class="col-lg-4">
                <div class="form-group">
                    <label>Disount </label>
                    <?= form_input('discount_', set_value('discount'), 'class="required form-control tip discount_" id="discount_" required="required" readonly'); ?>
							<input type="hidden" name="discount" class="discount_">
                </div>
            </div>
			<div class="col-lg-4">
                <div class="form-group">
                    <label>Discount Type </label>
                    <?= form_input('discount_type1', set_value('discount_type'), 'class="required form-control tip discount_type" id="discount_type" required="required"  readonly' ); ?>
					<input type="hidden" name="discount_type" class="discount_type">
                </div>
            </div>
			
                
            
            <div style="clear: both;height: 10px;"></div>
                    <div class="form-group col-lg-12">
                           <div class="box-footer"> <?php echo form_submit('add_card', lang('issue_card'), 'class="btn btn-primary"'); ?> </div>
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
$('.discount').on('change', function (e) {
	var optionSelected = $("option:selected", this);
    var valueSelected = this.value;
	var $options = $();
	 $.ajax({
            type: "post", async: false,
            url: "<?=admin_url('member_discount/get_ajax_card_details')?>",
            data: {discount_id: valueSelected},
            dataType: "json",
            success: function (scdata) {
              if(scdata != null){
                 $options = $options.add($('<option>').attr('value', "").html('Select Card'));
                 $.each(scdata, function(key, value) {
                 $options = $options.add($('<option>').attr('value', value.id).html(value.card_no));
                 });
                 $('#discount_card').html($options).trigger('change');
                } else {                
                $("#discount_card").val(null).trigger('change');                
              }
            }
        });
});
	$('#discount_card').on('change', function (e) {
    var optionSelected = $("option:selected", this);
    var valueSelected = this.value;
	var $options = $();
	 $.ajax({
            type: "post", async: false,
             url: "<?=admin_url('member_discount/get_discount_card_details')?>",
              data: {card_id: valueSelected},
            dataType: "json",
            success: function (data) {
              if(data != null){
                 $(".selling_price").val(data.selling_price);
				 $(".valito").val(data.to_date);
				 $(".discount_").val(data.discount);
				 $(".discount_type").val(data.discount_type);
              } else {                
                $("#get_variant_id").val(null).trigger('change');                
              }
            }
        });
	
	
    });

</script>