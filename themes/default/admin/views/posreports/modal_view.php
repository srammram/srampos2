<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <!--<button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="password_reports_close"><i class="fa fa-2x">&times;</i>
            </button>-->
            <h4 class="modal-title" id="myModalLabel"><?= lang('report_view_access'); ?></h4>
        </div>
        <?php 
        /*echo "<pre>";
        print_r($this->session->userdata());*/
        // $attrib = array('data-toggle' => 'validator', 'role' => 'form');
        // echo admin_form_open("reports/report_pass/" . $id, $attrib); ?>
        <div class="modal-body">          

            <div class="form-group">
                <?= lang('Pass code', 'pass_code'); ?>
                <input type="hidden" class="api_key" value="<?=$_GET['api-key']?>">   
                <input type="password" class="form-control" id="pass_code" name="pass_code" value="">               
            </div>

                    
        </div>
        <div class="modal-footer">
            <?= form_submit('submit', lang('submit'), 'class="btn btn-primary submit"'); ?>
        </div>
    </div>
    <?= form_close(); ?>
</div>

    <script type="text/javascript" src="<?=$assets?>js/jquery-2.0.3.min.js"></script>

<script type="text/javascript" src="<?=$assets?>js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?=$assets?>pos/js/plugins.min.js"></script>
<link rel="stylesheet" href="<?=$assets?>pos/css/posajax.css" type="text/css"/>
<style type="text/css">
    .ui-keyboard{height: 100px;}
    .ui-keyboard div{max-width: 400px;    margin-left: 33%;    margin-top: -20%;    box-shadow: 0px 0px 2px rgba(50, 50, 50, 0.50);}
</style>

<script>

   $(document).on('click', '.submit', function () {

            var pass_code = $('#pass_code').val();
            var api_key = $('.api_key').val();
            $url = '<?=site_url('api/v1/posreports/report_view_access');?>';   
            if (pass_code !='' ) {
                $('#pass_code').css('border-color', '#ccc');
                $('.pass-error').remove();
                  $.ajax({
                        type: 'POST',
                        url: $url,                    
                        data: {'api-key':api_key,pass_code: pass_code},
                        dataType: "json",
                        success: function (data) {                            
                            if(data != 0){                                                        
                                 window.location.href = window.location.href;
                            }else{                                
                                 $('<label class="pass-error" style="color:red">Enter valid Passcode</label>').insertAfter('#pass_code');
                                 return false;         
                            }
                        } 
                   });    
            } 
            else{
                $('#pass_code').css('border-color', 'red'); 
            }
        });
$(document).on('click', '#password_reports_close', function () {    
    window.location = "<?php base_url();  ?>";
    // location.reload();
});    


$('.kb-pad').keyboard({
        restrictInput: true,
        preventPaste: true,
        autoAccept: true,
        alwaysOpen: false,
        openOn: 'click',
        usePreview: false,
        layout: 'custom',
        maxLength: 10,
        display: {
            'b': '\u2190:Backspace',
        },
        customLayout: {
            'default': [
            '1 2 3 4',
            '5 6 7 8  ',
            ' 9 0 . {b}',

            ' {accept} {cancel}'
            ]
        }
    });


</script>
<?= $modal_js ?>

<style type="text/css">
    .fade.in{
        background: aliceblue !important;
    }
</style>