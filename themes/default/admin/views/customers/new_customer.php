<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<script type="text/javascript">
    //    $page = '<?=$_GET['type']?>';
    $(document).ready(function(){
      $('#add-customer-form')
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
            var form = $('#add-customer-form')[0];
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
                            $('<div class="counter-form-error">'+data.error+'</div>').insertAfter($('.modal-body1 span:eq(0)'));
                            $obj.find('input[type="submit"]').attr('disabled',false);//$('#add-counter').live('submit');
                        } else if (data.success) {
                              $('#new_customer_id').val(data.success.new_customer_id ? data.success.new_customer_id : 0);
                     $('#new_customer_name').text(data.success.name ? data.success.name : '');
                             $(".pop_close").trigger("click");
          
            var iname = $(this).attr('name');
            
            var iid = '#' + id;
            if (!!$(this).attr('data-bv-notempty') || !!$(this).attr('required')) {
                $("label[for='" + id + "']").append(' *');
                $(document).on('change', iid, function () {
                    $('form[data-toggle="validator"]').bootstrapValidator('revalidateField', iname);
                });
            }
       // });
                        }
                    },
                   
                });
                      
        });
        
    });
</script>
<div class="modal-dialog modal-lg ">
    <div class="modal-content close_pop">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('add_customer'); ?></h4>
        </div>
        <?php //$attrib = array('data-toggle' => 'validator', 'role' => 'form', class='bv-form' 'id' => 'add-customer-form');
       // echo admin_form_open_multipart("customers/add_pos", $attrib); ?>
        <form data-url="<?=admin_url('customers/new_customer')?>" data-toggle="validator" role="form"  method="post" accept-charset="utf-8" novalidate class="bv-form" id="add-customer-form">
        <div class="modal-body1">
            <span><?= lang('enter_info'); ?></span>
			
             
                
         
            
            
            <div class="row"> 
            <div class="col-md-12">
                <div class="col-md-6">
                <div class="form-group">
                    <label><?= lang("customer_type"); ?></label>
                    <?php $types = array('0' => lang('select_type'), '1' => lang('local'), '2' => lang('foreign'));
            echo form_dropdown('supplier_type', $types, '1', 'class="form-control select" id="supplier_type" required="required"'); ?>
                </div>
                	<div class="form-group">
                    <label class="control-label" for="customer_group"><?php echo $this->lang->line("customer_group"); ?></label>
                        <?php
                        foreach ($customer_groups as $customer_group) {
                            $cgs[$customer_group->id] = $customer_group->name;
                        }
                        echo form_dropdown('customer_group', $cgs, $Settings->customer_group, 'class="form-control select" id="customer_group" style="width:100%;" required="required"');
                        ?>
                    </div>
                    
		    <div class="form-group">
                        <?= lang("address", "address"); ?>
                        <?php echo form_input('address', '', 'class="form-control" id="address" required="required"'); ?>
                    </div>
                    
                    <div class="form-group">
                        <?= lang("email_address", "email_address"); ?>
                        <input type="email" name="email" class="form-control" id="email_address"/>
                    </div>
                    
                    
                    
                  
                    
		     
                    
                   

                </div>
                <div class="col-md-6">
                    
		    <div class="form-group person">
                        <?= lang("name", "name"); ?>
                        <?php echo form_input('name', '', 'class="form-control tip" id="name" data-bv-notempty="true"'); ?>
                    </div>
                    <div class="form-group">
                        <?= lang("phone", "phone"); ?>
                        <input type="tel" name="phone" class="form-control numberonly"  maxlength="10" id="phone"/>
                    </div>
                    
                    
                    <div class="form-group">
                        <?= lang("mobile_number"); ?>
                        <input type="tel" name="mobile_number" class="form-control numberonly" maxlength="10"  id="mobile_number"
                               value=""/>
                    </div>
                    <div class="form-group" style="margin-top: 55px !important;">
                    <label class="control-label" for="allow_loyalty"><?php echo $this->lang->line("Allow_Loyalty"); ?></label>
                     <select name="allow_loyalty" class="select" id="allow_loyalty" style="margin-top: 5px !important;">
			<option value="0"><?=lang('No')?></option>
			<option value="1"><?=lang('Yes')?></option>
		     </select>
                    </div>
                    
                   
                </div>
            </div>
	    </div>


        </div>
        <div class="modal-footer">
            <input type="hidden" id='cids' value="">
            <input type="hidden" id='cname' value="">
            <input type="hidden" name="add_customer" value="<?=lang('add_customer')?>">
            <?php echo form_submit('add_customer', lang('add_customer'), 'class="btn btn-primary"'); ?>
            <button type="button"  class="btn btn-default pop_close" data-dismiss="modal">Close</button>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>

<script type="text/javascript">
   /* $(document).ready(function (e) {
       /* $('#add-customer-form').bootstrapValidator({
            feedbackIcons: {
                valid: 'fa fa-check',
                invalid: 'fa fa-times',
                validating: 'fa fa-refresh'
            }, excluded: [':disabled']
        });*/
      /*  $('select.select').select2({minimumResultsForSearch: 7});
        fields = $('.modal-content').find('.form-control');*/

                         //$(".modal-content").hide();
      /* $.each(fields, function () {
            var id = $(this).attr('id');
            var iname = $(this).attr('name');
            alert(id);
            var iid = '#' + id;
            if (!!$(this).attr('data-bv-notempty') || !!$(this).attr('required')) {
                $("label[for='" + id + "']").append(' *');
                $(document).on('change', iid, function () {
                    $('form[data-toggle="validator"]').bootstrapValidator('revalidateField', iname);
                });
            }
        });
    });*/
</script>

<script>
$(".numberonly").keypress(function (event){
	
	if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
		event.preventDefault();
	}
  
	});
$(document).ready(function(){
    $('#customer_type').change(function(){
	if($(this).val()=="prepaid"){
	    $('.credit_days').hide().find('input').val('');
	    $('.credit_limit').hide().find('input').val('');
	    
	}else{
	    $('.credit_days').show();
	    $('.credit_limit').show();
	}
    });
})
</script>


