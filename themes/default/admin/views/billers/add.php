<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<script>
    $(document).ready(function(){
      $('#add-counter,#edit-counter')
        .bootstrapValidator(
                            {
                                message: 'Please enter/select a value',
                                submitButtons: 'input[type="submit"]',
                                excluded: [':disabled'],
                                fields: {
                                check_logo : {
                                    validators: {
                                        callback: {
                                            message: 'Please enter/select a value',
                                            callback: function(value, validator, $field) {
                                                if ($('.s2id_biller_logo select2_chosen').text() =='') {                                
                                                   return true;
                                                }
                    
                                              
                    
                                                return true;
                                            }
                                        }
                                    }
                                }
                            }
                            }
                        )
        .on('success.form.bv', function(e) {
            // Prevent form submission
            e.preventDefault();
            $obj = $(this);
            $url = $obj.attr('data-url');
            $formData = $obj.serialize();
           $.ajax({
                    url: $url,
                    type: "POST",
                    data: $formData,//+= '&add_biller=Add Counter ',
                    dataType: "json",
                    success:function(data){
                        //$('#mymodal').on('hidden.bs.modal', function() {
                        //    return false;
                        //});
                        //$("#add-counter").scrollTop(0);
                        //console.log(data);
                        if (data.error) {
                            $('<div class="counter-form-error">'+data.error+'</div>').insertAfter($('.modal-body p:eq(0)'));
                            $obj.find('input[type="submit"]').attr('disabled',false);//$('#add-counter').live('submit');
                        }else if (data.success) {
                           location.reload();
                        }
                       return false;
                        //$("html, body").animate({scrollTop: 0}, 100);
                        
                       
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
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('add_biller'); ?></h4>
        </div>
        <?php $attrib = array('data-toggle' => 'validatorr', 'role' => 'form','id'=>'add-counter');
       // echo admin_form_open_multipart("billers/add", $attrib); ?>
       <form data-url="<?=admin_url('billers/add');?>" role="form" id="add-counter" enctype="multipart/form-data" method="post" accept-charset="utf-8">
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <?= lang("logo", "biller_logo"); ?>
                        <?php
                        $biller_logos[''] = '';
                        foreach ($logos as $key => $value) {
                            $biller_logos[$value] = $value;
                        }
                        echo form_dropdown('logo', $biller_logos, '', 'class="form-control select" id="biller_logo" data-bv-notempty="true" required="required"'); ?>
                        <input type="hidden" data-validate="true"  name="check_logo" value="" data-bv-field="check_logo">
                    </div>
                </div>

                <div class="col-md-6">
                    <div id="logo-con" class="text-center"></div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group company">
                        <?= lang("company", "company"); ?>
                        <?php echo form_input('company', '', 'class="form-control tip" id="company" data-bv-notempty="true"'); ?>
                    </div>
                    <div class="form-group person">
                        <?= lang("name", "name"); ?>
                        <?php echo form_input('name', '', 'class="form-control tip" id="name" data-bv-notempty="true" required="required"'); ?>
                    </div>
                    <div class="form-group">
                        <?= lang("vat_no", "vat_no"); ?>
                        <?php echo form_input('vat_no', '', 'class="form-control " id="vat_no"'); ?>
                    </div>
                    <!--<div class="form-group company">
                    <?= lang("contact_person", "contact_person"); ?>
                    <?php echo form_input('contact_person', '', 'class="form-control" id="contact_person" data-bv-notempty="true"'); ?>
                </div>-->
                    <div class="form-group">
                        <?= lang("email_address", "email_address"); ?>
                        <input type="email" name="email" class="form-control"  id="email_address" />
                    </div>
                    <div class="form-group">
                        <?= lang("phone", "phone"); ?>
                        <input type="tel" name="phone" onkeypress="return Validate(event);" class="form-control" required="required" maxlength="30" id="phone"/>
                    </div>
                    <div class="form-group">
                        <?= lang("address", "address"); ?>
                        <?php echo form_input('address', '', 'class="form-control" id="address" required="required" required="required"'); ?>
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
                        <?= lang("city", "city"); ?>
                        <?php echo form_input('city', '', 'class="form-control" id="city" required="required"'); ?>
                    </div>
                     <div class="form-group">
                        <?= lang("state", "state"); ?>
                        <?php
                        if ($this->Settings->indian_gst) {
                            $states = $this->gst->getIndianStates();
                            echo form_dropdown('state', $states, '', 'class="form-control select" id="state"');
                        } else {
                            echo form_input('state', '', 'class="form-control" id="state"');
                        }
                        ?>
                    </div>
                    <div class="form-group">
                        <?= lang("local_lang_name", "local_lang_name"); ?>
                        <?php echo form_input('local_lang_name', '', 'class="form-control" id="local_lang_name"'); ?>
                    </div>
                    <div class="form-group">
                        <?= lang("bcf1", "cf1"); ?>
                        <?php echo form_input('cf1', '', 'class="form-control" id="cf1"'); ?>
                    </div>
               
                   
                </div>
                 <div class="col-md-4">
                       <div class="form-group">
                        <?= lang("bcf2", "cf2"); ?>
                        <?php echo form_input('cf2', '', 'class="form-control" id="cf2"'); ?>

                    </div>
                    <div class="form-group">
                        <?= lang("bcf3", "cf3"); ?>
                        <?php echo form_input('cf3', '', 'class="form-control" id="cf3"'); ?>
                    </div>
                    <div class="form-group">
                        <?= lang("bcf4", "cf4"); ?>
                        <?php echo form_input('cf4', '', 'class="form-control" id="cf4"'); ?>

                    </div>
                     <div class="form-group">
                        <?= lang("bcf5", "cf5"); ?>
                        <?php echo form_input('cf5', '', 'class="form-control" id="cf5"'); ?>

                    </div>
                    <div class="form-group">
                        <?= lang("bcf6", "cf6"); ?>
                        <?php echo form_input('cf6', '', 'class="form-control" id="cf6"'); ?>
                    </div>
                     <div class="form-group">
                        <?= lang("bcf6", "local_lang_address"); ?>
                        <?php echo form_input('local_lang_address', '', 'class="form-control" id="local_lang_address"'); ?>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang("invoice_footer", "invoice_footer"); ?>
                        <?php echo form_textarea('invoice_footer', '', 'class="form-control skip" id="invoice_footer" style="height:100px;"'); ?>
                    </div>
                </div>
            </div>


        </div>
        <div class="modal-footer">
            <input type="hidden" name="add_biller" value="<?=lang('add_biller')?>">
            <?php echo form_submit('add_biller', lang('add_biller'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<script type="text/javascript" charset="utf-8">
    $(document).ready(function () {
        $('#biller_logo').change(function (event) {
            var biller_logo = $(this).val();
            $('#logo-con').html('<img src="<?=base_url('assets/uploads/logos')?>/' + biller_logo + '" alt="">');
        });
    });
</script>
<script>
$(".numberonly").keypress(function (event){
    
    if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
        event.preventDefault();
    }
  
    });

 function Validate(event) {
        // var regex = new RegExp("^[0-9-!@#$%*?/]"); reference
        var regex = new RegExp("^[0-9-/]");
        var key = String.fromCharCode(event.charCode ? event.which : event.charCode);
        if (!regex.test(key)) {
            event.preventDefault();
            return false;
        }
    }      


</script>
<?= $modal_js ?>
