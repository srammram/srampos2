<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?= lang('supplier_type'); ?></h4>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form');
        echo admin_form_open_multipart("suppliers/add", $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>

            <div class="form-group">
                    <?= lang("supplier_type", "supplier_type"); ?>
                    <?php $types = array('0' => lang('Select Type'), '1' => lang('Local'), '2' => lang('Foreign'));
            echo form_dropdown('supplier_type', $types, '', 'class="form-control select" id="supplier_type" required="required"'); ?>
                </div>

            <div class="row">
                <div class="col-md-12">
                <div class="col-md-4">
                    <div class="form-group company">
                        <?= lang("company", "company"); ?>
                        <?php echo form_input('company', '', 'class="form-control tip" id="company" '); ?>
                    </div>
                    <div class="form-group person">
                        <?= lang("name", "name"); ?>
                        <?php echo form_input('name', '', 'class="form-control tip" id="name" data-bv-notempty="true"'); ?>
                    </div>
                    <div class="form-group short_name">
                        <?= lang("short_name", "short_name"); ?>
                        <?php echo form_input('short_name', '', 'class="form-control tip" id="short_name" '); ?>
                    </div>
                    <div class="form-group">
                        <?= lang("vat_no", "vat_no"); ?>
                        <?php echo form_input('vat_no', '', 'class="form-control" id="vat_no"'); ?>
                    </div>
                    <!--<div class="form-group company">
                    <?= lang("contact_person", "contact_person"); ?>
                    <?php echo form_input('contact_person', '', 'class="form-control" id="contact_person" data-bv-notempty="true"'); ?>
                </div>-->
                    <div class="form-group">
                        <?= lang("email_address", "email_address"); ?>
                        <input type="email" name="email" class="form-control"  id="email_address"/>
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
                    <div class="form-group">
                        <?= lang("mobile_number"); ?>
                        <input type="tel" name="mobile_number" maxlength="10" class="form-control numberonly"  id="mobile_number"
                               value=""/>
                    </div>
                    
                </div>

                <div class="col-md-4" style="margin-top: 10px !important;">
               
                    <div class="form-group">
                        <?= lang("credit_days"); ?>
                        <input type="text" name="credit_days" maxlength="3" class="form-control numberonly" id="credit_days"
                               value=""/>
                    </div>
                    <div class="form-group">
                        <?= lang("credit_limits"); ?>
                        <input type="text" name="credit_limit" maxlength="20" class="form-control numberonly"  id="credit_limit"
                               value=""/>
                    </div>
                    
                    <div class="form-group">
                        <?= lang("bank_details"); ?>
                        <textarea name="bank_details" class="form-control skip" id="bank_details" style="height:100px;" ></textarea>
                    </div>
                

                  <!--   <div class="form-group">
                        <?= lang("Bank Details", "Bank Details"); ?>
                        <input type="text" name="bank_details" class="form-control" id="bank_details"
                               value="<?= $supplier->bank_details ?>"/>
                    </div> -->
                    <div class="form-group">
                        <?= lang("tax_type"); ?>
                         <?php
                        $tr[""] = "";
                        foreach ($tax_rates as $tax) {
                            $tr[$tax->id] = $tax->name;
                        }
                        echo form_dropdown('tax_type', $tr, "", 'class="form-control tax_type" ');
                        ?>
                       
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
                        <?= lang("scf1", "cf1"); ?>
                        <?php echo form_input('cf1', '', 'class="form-control" id="cf1"'); ?>
                    </div>
                    <div class="form-group">
                        <?= lang("scf2", "cf2"); ?>
                        <?php echo form_input('cf2', '', 'class="form-control" id="cf2"'); ?>

                    </div>
                    <div class="form-group">
                        <?= lang("scf3", "cf3"); ?>
                        <?php echo form_input('cf3', '', 'class="form-control" id="cf3"'); ?>
                    </div>
                    <div class="form-group">
                        <?= lang("scf4", "cf4"); ?>
                        <?php echo form_input('cf4', '', 'class="form-control" id="cf4"'); ?>

                    </div>
                    <div class="form-group">
                        <?= lang("scf5", "cf5"); ?>
                        <?php echo form_input('cf5', '', 'class="form-control" id="cf5"'); ?>

                    </div>
                    <div class="form-group">
                        <?= lang("scf6", "cf6"); ?>
                        <?php echo form_input('cf6', '', 'class="form-control" id="cf6"'); ?>
                    </div>
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
