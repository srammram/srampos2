<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('add_customer_group'); ?></h4>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form');
        echo admin_form_open("system_settings/add_customer_group", $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>

            <div class="form-group">
                <label for="name"><?php echo $this->lang->line("group_name"); ?></label>

                <div class="controls"> <?php echo form_input('name', '', 'class="form-control" id="name" required="required"'); ?> </div>
            </div>
            
             <div class="form-group">
                <?= lang('code', 'code'); ?>
               
                <div class="input-group col-md-12">
                	<?= form_input('code', '', 'class="form-control numberonly" id="code" required="required" maxlength="9" '); ?>
                     <span class="" id="random_num" style="    padding: 6px 10px;
    background: #efefef;
    position: relative;
    margin-top: -34px;
    border: 1px solid #ccc;
    float: right;
    z-index: 99;
    cursor: pointer;">
                        <i class="fa fa-random"></i>
                    </span>
                   
                </div>
                
                
            </div>
            
            
            <div class="form-group">
                <label for="name"><?php echo $this->lang->line("Loaylty points"); ?></label>

                <div
                    class="controls"> <?php echo form_input('loayltypoints', '', 'class="form-control numberonly" maxlength="8" id="loayltypoints" required="required"'); ?> </div>
            </div>
            
            <div class="form-group">
            <label for="method"><?php echo $this->lang->line("Discount Method"); ?></label>
            <div class="controls">
              <select name="discount_type" id="discount_type" class="form-control select" >
                 <option value="">Select Type</option>
                <option value="Fixed">Fixed Discount</option>
                <option value="Percentage">Percentage Discount</option>
                
              </select>
            </div>
          </div>
          
            <div class="form-group">
                <label for="percent"><?php echo $this->lang->line("Discount"); ?></label>

                <div
                    class="controls"> <?php echo form_input('percent', '', 'class="form-control" id="percent" required="required"'); ?> </div>
            </div>
        </div>
        <div class="modal-footer">
            <?php echo form_submit('add_customer_group', lang('add_customer_group'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<script>
$(document).ready(function(){

$(document).on('click', '#random_num', function(event){
	event.preventDefault();
			$(this).parent('.input-group').children('input').val(generateCardNo(8));
			 $('form[data-toggle="validator"]').bootstrapValidator('revalidateField', $('#code'));
			 
			
		});
		$(".numberonly").keypress(function (event){
	
		if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
			event.preventDefault();
		}
	  
		});
});
</script>
<?= $modal_js ?>