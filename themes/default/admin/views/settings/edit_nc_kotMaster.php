<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('edit_NC_Kot_Master'); ?></h4>
        </div>
        <?php echo admin_form_open("system_settings/edit_NCKotMaster/" . $kot_master->id); ?>
		<input type="hidden" name="id" value="<?php echo $kot_master->id ?>">
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>
              <div class="form-group">
                <label for="name"><?php echo $this->lang->line("name"); ?></label>
                <div class="controls"> <?php echo form_input('name',$kot_master->name, 'class="form-control" id="name" autocomplete="off" required="required"'); ?> 
                </div>
            </div>
            <div class="form-group">
                <label for="display_name"><?php echo $this->lang->line("Display_name"); ?></label>
                <div class="controls"> <?php echo form_input('display_name', $kot_master->display_name, 'class="form-control" autocomplete="off" id="display_name" required="required"'); ?> 
                </div>
            </div>
			<div class="form-group">
                <label for="display_name"><?php echo $this->lang->line("No_of_select_box"); ?></label>
                <div class="controls"> <?php echo form_input('no_of_selectbox',$kot_master->no_of_select_box, 'class="form-control numberonly" autocomplete="off" id="no_of_selectbox" required="required"'); ?> 
                </div>
				<br>
				<div class="dy_select">
				<?php   ?>
				<?php if(!empty($kot_master->select_box_master)){ foreach(json_decode($kot_master->select_box_master) as $row){   ?>
				<div class='form-group'><select class='form-control' name='selectType[]' ><option <?php if($row ==1){ echo "selected" ; }  ?> value='1'>customer</option><option <?php if($row==2){ echo "selected" ; }  ?> value='2'>company</option><option <?php if($row==3){ echo "selected" ; }  ?> value='3'>user</option></select></div>
				<?php  } }   ?>
				
				</div>
            </div>
			<div class="form-group">
                <label for="display_name"><?php echo $this->lang->line("No_of_input_box"); ?></label>
                <div class="controls"> <?php echo form_input('no_of_inputbox',$kot_master->no_of_input_box, 'class="form-control numberonly" autocomplete="off" id="no_of_inputbox" required="required"'); ?> 
                </div>
            </div>
            <div class="form-group">
				<label><input type="checkbox" value="1" name="status" <?php echo ($kot_master->active ==1)?"checked":"" ;  ?>>  Active</label>
            </div>
        </div>
        
        <div class="modal-footer">
            <?php echo form_submit('edit_NCKotMaster', lang('edit_NC_Kot_Master'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<?= $modal_js ?>



<script>
$(document).ready(function() {
    $('#no_of_selectbox').blur(function() {
		var number=$(this).val();
		var selectBox="";
		if(number<3){
		 for(var i=0;i<number;i++){
			selectBox +="<div class='form-group'><select class='form-control' name='selectType[]' ><option value='1'>customer</option><option value='2'>company</option><option value='3'>user</option></select></div>";
		} 
		$(".dy_select").html(selectBox);
		}else{
			bootbox.alert("Please Select Valid Number");
		}
	});
	
});

</script>