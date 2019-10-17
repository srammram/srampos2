<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('add_customer'); ?></h4>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'id' => 'add-customer-form');
        echo admin_form_open_multipart("customers/add", $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>
			
             <div class="form-group">
                    <?= lang("customer_type"); ?>
                    <?php $types = array('0' => lang('select_type'), '1' => lang('local'), '2' => lang('foreign'));
            echo form_dropdown('supplier_type', $types, '', 'class="form-control select" id="supplier_type" required="required"'); ?>
                </div>
                
         
            
            
                
            <div class="row">
                <div class="col-md-12">
                <div class="col-md-4">
                
                	<div class="form-group">
                    <label class="control-label" for="customer_group"><?php echo $this->lang->line("customer_group"); ?></label>
                        <?php
                        foreach ($customer_groups as $customer_group) {
                            $cgs[$customer_group->id] = $customer_group->name;
                        }
                        echo form_dropdown('customer_group', $cgs, $Settings->customer_group, 'class="form-control select" id="customer_group" style="width:100%;" required="required"');
                        ?>
                    </div>
                    
                    <!--<div class="form-group">
                        <label class="control-label" for="price_group"><?php echo $this->lang->line("price_group"); ?></label>
                        <?php
                        //$pgs[''] = lang('select').' '.lang('price_group');
//                        foreach ($price_groups as $price_group) {
//                            $pgs[$price_group->id] = $price_group->name;
//                        }
//                        echo form_dropdown('price_group', $pgs, $Settings->price_group, 'class="form-control select" id="price_group" style="width:100%;"');
                        ?>
                    </div>-->
                    
                    <div class="form-group company">
                        <?= lang("company", "company"); ?>
                        <?php echo form_input('company', '', 'class="form-control tip" id="company" '); ?>
                    </div>
                    <div class="form-group person">
                        <?= lang("name", "name"); ?>
                        <?php echo form_input('name', '', 'class="form-control tip" id="name" data-bv-notempty="true"'); ?>
                    </div>
                     <div class="form-group short_name">
                        <?= lang("short_name"); ?>
                        <?php echo form_input('short_name', '', 'class="form-control tip" id="short_name" '); ?>
                    </div>
                   <!-- <div class="form-group">
                        <?= lang("vat_no", "vat_no"); ?>
                        <?php echo form_input('vat_no', '', 'class="form-control" id="vat_no"'); ?>
                    </div>-->
                    <!--<div class="form-group company">
                    <?= lang("contact_person", "contact_person"); ?>
                    <?php echo form_input('contact_person', '', 'class="form-control" id="contact_person" data-bv-notempty="true"'); ?>
                </div>-->
                    <div class="form-group">
                        <?= lang("email_address", "email_address"); ?>
                        <input type="email" name="email" class="form-control" id="email_address"/>
                    </div>
                    <div class="form-group">
                        <?= lang("phone", "phone"); ?>
                        <input type="tel" name="phone" class="form-control numberonly" required="required" maxlength="10" id="phone"/>
                    </div>
                    
                    <div class="form-group">
                        <?= lang("contact_person"); ?>
                        <input type="text" name="conatct_person_name" class="form-control"  id="conatct_person_name"
                               value=""/>
                    </div>
                </div>
                <div class="col-md-4" style="margin-top:8px !important;">
                    <div class="form-group">
                        <?= lang("mobile_number"); ?>
                        <input type="tel" name="mobile_number" class="form-control numberonly" maxlength="10"  id="mobile_number"
                               value=""/>
                    </div>
		     <div class="form-group">
                        <?= lang("credit_type"); ?>
				<select name="customer_type" class="form-control select" id="customer_type" required="required" title="">
				<option value="none" selected="selected"> Select Type</option>
				<!-- <option value="none"> None</option> -->
                <option value="prepaid"> Prepaid</option>
				<option value="postpaid"> Postpaid</option>
				</select>
                    </div>
		    
                    <div class="form-group credit_days">
                        <?= lang("credit_days"); ?>
                        <input type="text" name="credit_days" class="form-control numberonly" maxlength="3" id="credit_days"
                               value=""/>
                    </div>
                    <div class="form-group credit_limit">
                        <?= lang("credit_limits"); ?>
                        <input type="text" name="credit_limit" class="form-control numberonly"  maxlength="20" id="credit_limit"
                               value=""/>
                    </div>
                    <div class="form-group">
                    <label class="control-label" for="allow_loyalty"><?php echo $this->lang->line("Allow_Loyalty"); ?></label>
                     <select name="allow_loyalty" class="select" id="allow_loyalty">
			<option value="0"><?=lang('No')?></option>
			<option value="1"><?=lang('Yes')?></option>
		     </select>
                    </div>
                   <div class="form-group">
                        <?= lang("address", "address"); ?>
                        <?php echo form_input('address', '', 'class="form-control" id="address" required="required"'); ?>
                    </div>   
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

                </div>
                <div class="col-md-4">
                    
                    <div class="form-group">
                        <?= lang("postal_code", "postal_code"); ?>
                        <?php echo form_input('postal_code', '', 'class="form-control numberonly" maxlength="6" id="postal_code"'); ?>
                    </div>
                    <div class="form-group">
                        <?= lang("country", "country"); ?>
                        <?php echo form_input('country', '', 'class="form-control" id="country"'); ?>
                    </div>
                    <div class="form-group">
                        <?= lang("ccf1", "cf1"); ?>
                        <?php echo form_input('cf1', '', 'class="form-control" id="cf1"'); ?>
                    </div>
                    <div class="form-group">
                        <?= lang("ccf2", "cf2"); ?>
                        <?php echo form_input('cf2', '', 'class="form-control" id="cf2"'); ?>

                    </div>
                    <div class="form-group">
                        <?= lang("ccf3", "cf3"); ?>
                        <?php echo form_input('cf3', '', 'class="form-control" id="cf3"'); ?>
                    </div>
                    <div class="form-group">
                        <?= lang("ccf4", "cf4"); ?>
                        <?php echo form_input('cf4', '', 'class="form-control" id="cf4"'); ?>

                    </div>
                    <div class="form-group">
                        <?= lang("ccf5", "cf5"); ?>
                        <?php echo form_input('cf5', '', 'class="form-control" id="cf5"'); ?>

                    </div>
                    <div class="form-group">
                        <?= lang("ccf6", "cf6"); ?>
                        <?php echo form_input('cf6', '', 'class="form-control" id="cf6"'); ?>
                    </div>
                </div>
                </div>
            </div>


        </div>
        <div class="modal-footer">
            <?php echo form_submit('add_customer', lang('add_customer'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>

<script type="text/javascript">
    $(document).ready(function (e) {
        $('#add-customer-form').bootstrapValidator({
            feedbackIcons: {
                valid: 'fa fa-check',
                invalid: 'fa fa-times',
                validating: 'fa fa-refresh'
            }, excluded: [':disabled']
        });
        $('select.select').select2({minimumResultsForSearch: 7});
        fields = $('.modal-content').find('.form-control');
        $.each(fields, function () {
            var id = $(this).attr('id');
            var iname = $(this).attr('name');
            var iid = '#' + id;
            if (!!$(this).attr('data-bv-notempty') || !!$(this).attr('required')) {
                $("label[for='" + id + "']").append(' *');
                $(document).on('change', iid, function () {
                    $('form[data-toggle="validator"]').bootstrapValidator('revalidateField', iname);
                });
            }
        });
    });
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
