<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
  <link rel="stylesheet" href="<?= $assets ?>styles/jquery-ui.css">
  <script src="<?= $assets ?>js/jquery-ui.js"></script>
<div class="box">
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?= lang('customize_report'); ?></p>
                <div id="form">
                        
                        <input type="hidden" class="api_key" value="<?=@$_GET['api-key']?>">

                        <div class="col-sm-2">
                            <div class="form-group">
                                <?= lang("username", "username"); ?>
                                <?php echo form_input('username', ($this->session->userdata('username')), 'class="form-control "  id="username"'); ?>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <?= lang("password", "password"); ?>
                                <?php echo form_input('password', ($this->session->userdata('password')), 'class="form-control " id="password"'); ?>
                            </div>
                        </div>
                        
                        <div class="col-sm-2 form-group" style="top:30px;">
                        <div
                            class="controls"> <?php echo form_submit('submit', $this->lang->line("submit"), 'class="btn btn-primary submit_itemreport"'); ?> </div>
                             
                    </div>
                    </div>
                    
                </div>
                    <!-- <?php echo form_close(); ?> -->
                <div class="clearfix"></div>

               
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="<?= $assets ?>js/html2canvas.min.js"></script>
<script type="text/javascript">
    var $offset = false;
    $(document).ready(function () {
    
        $(document).on('click', '.submit_itemreport', function () {
            $offset = false;
            $url = '<?=site_url('api/v1/posreports/login');?>';
            GetData($url);
        });

       
    });
function GetData($url) {    
            /*var recipe = $('#suggest_recipe').val();*/
            var username = $('#username').val();
            var password = $('#password').val();    
            var api_key = $('.api_key').val();
            if (username !='' && username !='' ) {
             
                  $.ajax({
                        type: 'POST',
                        url: $url,                    
                        data: {'api-key':api_key,devices_key:352774061466607,username: username, password: password},
                        dataType: "json",
                         success: function (data) {
                             console.log(data)
                           // alert ('logged in');
                            window.location.href = "<?=site_url('api/v1/posreports/reportslist?api-key=')?>"+api_key;
                        }
                    });  
            }
            else{
                if (start_date =='') {                    
                    $('#start_date').css('border-color', 'red');
                }else{
                   $('#start_date').css('border-color', '#ccc'); 
                }
                if (end_date =='') {                    
                    $('#end_date').css('border-color', 'red');
                }else{
                   $('#end_date').css('border-color', '#ccc'); 
                }
                return false;     
            }  
        };   
</script>