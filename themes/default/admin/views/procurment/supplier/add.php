<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<script>
    $(document).ready(function(){
      $('#add-supplier')
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
            $obj = $(this);
            $url = $obj.attr('data-url');
            $formData = $obj.serialize();
	    var form = $('#add-supplier')[0];
	    var data = new FormData(form);
	    $.ajax({
                    url: $url,
                    type: "POST",
                    data: data,//$formData+'&userfile='+data+'&add_brand=Add Brand',
                    cache: false,
		    dataType: 'json',
		    processData: false, // Don't process the files
		    contentType: false,
                    success:function(data){
			 
                        if (data.error) {
                            $('<div class="counter-form-error">'+data.error+'</div>').insertAfter($('.modal-body p:eq(0)'));
                            $obj.find('input[type="submit"]').attr('disabled',false);//$('#add-counter').live('submit');
                        }else if (data.supplier) {
			    $("#myModal .close").trigger('click');
			    
			    
			    var newStateLabel = data.supplier.name;
			    var newStateVal =data.supplier.id				
			    
			    console.log(newStateLabel+'--'+newStateVal);
			    var newState = new Option(newStateLabel,newStateVal, false, true);
				// Append it to the select
			
			    
			    $("select#pi_supplier").append(newState).trigger('change');
			    
			    return false;
                        }
                       
                    },
                   
                });
        });
        
    });
</script>
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?= lang('add_supplier'); ?></h4>
        </div>
        
	<form data-url="<?=admin_url('procurment/supplier/add')?>" data-toggle="validator" role="form" enctype="multipart/form-data" method="post" accept-charset="utf-8" novalidate class="bv-form" id="add-supplier">
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>

            <div class="row">
                <div class="col-md-6">
                  
                    <div class="form-group person">
                        <?= lang("name", "name"); ?>
                        <?php echo form_input('name', '', 'class="form-control tip" id="name" data-bv-notempty="true"'); ?>
                    </div>
                   
                    
                 
                    <div class="form-group">
                        <?= lang("email_address", "email_address"); ?>
                        <input type="email" name="email" class="form-control"  id="email_address"/>
                    </div>
                    
                    <div class="form-group">
                        <?= lang("mobile_number","mobile_number"); ?>
                        <input type="tel" name="mobile_number" maxlength="10" class="form-control numberonly"  id="mobile_number"
                               value=""/>
                    </div>
                   
		    <div class="form-group">
                        <?= lang("address", "address"); ?>
                        <?php echo form_input('address', '', 'class="form-control" id="address" required="required"'); ?>
                    </div>
                  
                   
                    
                    
                </div>
                <div class="col-md-6">
                
                	
                    <div class="form-group">
                        <?= lang("city", "city"); ?>
                        <?php echo form_input('city', '', 'class="form-control" id="city" required="required"'); ?>
                    </div>
                    <div class="form-group">
                        <?= lang("state", "state"); ?>
                        <?php
                        if ($this->Settings->indian_gst) {
                            $states = $this->gst->getIndianStates(true);
                            echo form_dropdown('state', $states, '', 'class="form-control select" id="state"');
                        } else {
                            echo form_input('state', '', 'class="form-control" id="state"');
                        }
                        ?>
                    </div>


                    <div class="form-group">
                        <?= lang("postal_code", "postal_code"); ?>
                        <?php echo form_input('postal_code', '', 'class="form-control numberonly" maxlength="6" id="postal_code"'); ?>
                    </div>
                    <div class="form-group">
                        <?= lang("country", "country"); ?>
                        <?php echo form_input('country', '', 'class="form-control" id="country"'); ?>
                    </div>
                    
                </div>
            </div>


        </div>
        <div class="modal-footer">
            <?php echo form_submit('add_supplier', lang('add_supplier'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>

<script>
$(".numberonly").keypress(function (event){
	
	if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
		event.preventDefault();
	}
  
	});
</script>
<?= $modal_js ?>
