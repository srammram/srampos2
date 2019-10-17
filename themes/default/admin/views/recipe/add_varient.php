<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<script>
    $(document).ready(function(){
      $('#add-varient')
        .bootstrapValidator(
                            {
                                message: 'Please enter/select a value',
                                //submitButtons: 'input[type="submit"]',
                                
                            }
                        )
        .on('success.form.bv', function(e) {
            // Prevent form submission
            e.preventDefault();
	    $('.counter-form-error').remove();
            $obj = $('#add-varient');
            $url = $obj.attr('data-url');
            $formData = $obj.serialize();
	    //console.log($parentCategory);
	    $.ajax({
                    url: $url,
                    type: "POST",
                    data: $formData,//$formData+'&userfile='+data+'&add_brand=Add Brand',
                    //cache: false,
		    dataType: 'json',
		    //processData: false, // Don't process the files
		    //contentType: false,
                    success:function(data){
			 
                        if (data.error) {
                            $('<div class="counter-form-error">'+data.error+'</div>').insertAfter($('.modal-body p:eq(0)'));
                            $obj.find('input[type="submit"]').attr('disabled',false);//$('#add-counter').live('submit');
                        }else if (data.varient) {
			    $("#myModal .close").trigger('click');
			    location.reload();
			    return false;
                        }
                       
                    },
                   
                });
        });
        
    });
</script>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('add_varient'); ?></h4>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form');
        //echo admin_form_open_multipart("system_settings/add_category", $attrib); ?>
	<form  data-toggle="validator" data-url="<?=admin_url('recipe/add_varient')?>" role="form" enctype="multipart/form-data" method="post" accept-charset="utf-8" novalidate class="bv-form" id="add-varient">
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>

         <!--   
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
                
                
            </div>-->

            <div class="form-group">
                <?= lang('varient_name', 'name'); ?>
                <?= form_input('name', set_value('name'), 'class="form-control gen_slug" id="name" required="required"'); ?>
            </div>
		
            <div class="form-group">
                <?= lang('native_name', 'native_name'); ?>
                <?= form_input('native_name', set_value('native_name'), 'class="form-control" id="native_name" required="required"'); ?>
            </div>
	    <div class="form-group all">
                        <?= lang("variant_code", "code") ?>
                        <div class="input-group">
                            <?= form_input('variant_code', (isset($_POST['variant_code']) ? $_POST['variant_code'] :''), 'class="form-control" id="variant_code"  required="required"') ?>
                            <span class="input-group-addon pointer" id="random_num" style="padding: 1px 10px;">
                                <i class="fa fa-random"></i>
                            </span>
                        </div>
                       <!-- <span class="help-block"><?= lang('you_scan_your_barcode_too') ?></span>-->
                        <label for="code" class="text-danger"></label>
                    </div>
           

            

        </div>
        <div class="modal-footer">
	    <input type="hidden" name="add_varient" value="<?=lang('add_varient')?>">
            <?php echo form_submit('add_varient', lang('add_varient'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
    <script>
	$(document).ready(function(){
	    $('#random_num').click(function(){
            $(this).parent('.input-group').children('input').val(generateCardNo(8));
        });
	})
    </script>
<script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>
<?= $modal_js ?>
