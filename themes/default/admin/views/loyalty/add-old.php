<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!-- <script type="text/javascript" src="<?=$assets ?>js/loyalty.js"></script> -->
<link rel="shortcut icon" href="<?=$assets?>images/favicon.ico"/>
<link rel="stylesheet" href="<?=$assets?>styles/theme.css" type="text/css"/>
<link rel="stylesheet" href="<?=$assets?>styles/style.css" type="text/css"/>
<link rel="stylesheet" href="<?=$assets?>pos/css/posajax.css" type="text/css"/>
<link rel="stylesheet" href="<?=$assets?>pos/css/print.css" type="text/css" media="print"/>
<script type="text/javascript" src="<?=$assets?>js/jquery-2.0.3.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/jquery-migrate-1.2.1.min.js"></script>

<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('loyalty_configuration'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">              
                <?php           


                $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'id' => 'add-loyalty-form' );
                echo admin_form_open("loyalty_settings/add", $attrib)
                ?>

                <div class="row">
                    <div class="col-lg-12">    
                        <fieldset class="scheduler-border">
                            <legend class="scheduler-border"><?= lang('main_settings') ?></legend>

                            <div class="form-group col-lg-6">
                                <?= lang('from_date', 'from_date'); ?>
                                    <div class="controls ">
                                      <input type="text" name="from_date" class="form-control" placeholder="From Date " id="from_date" required="required" value="" autocomplete="off">
                                    </div>
                            </div>
                            <div class="form-group col-lg-6">
                                <?= lang('to_date', 'to_date'); ?>
                              <div class="controls">
                                <input type="text" name="to_date" class="form-control" placeholder="To Date " id="to_date" required="required" value="" autocomplete="off">
                              </div>
                            </div>

                             <div class="form-group col-lg-6">
                                <?= lang('prefix', 'prefix'); ?>
                                    <div class="controls ">
                                      <input type="text" name="prefix" class="form-control" placeholder="Prefix " id="prefix" required="required" value="" autocomplete="off">
                                    </div>
                            </div>
                            <div class="form-group col-lg-6">
                                <?= lang('Card_number', 'card_number'); ?>
                              <div class="controls">
                                <input type="text" name="card_number" class="form-control" placeholder="Card Number" id="card_number" required="required" value="" autocomplete="off">
                              </div>
                            </div>

                             <div class="form-group col-lg-6">
                                <?= lang('Eligibity_point', 'Eligibity_point'); ?>
                                    <div class="controls ">
                                      <input type="text" name="eligibity_point" class="form-control" placeholder="Eligibity Point " id="eligibity_point" required="required" value="" autocomplete="off">
                                    </div>
                            </div>
                            <div class="form-group col-lg-6">
                                <?= lang('status', 'status'); ?>
                                <div class="controls">
                                    <?php
                                    $status = array(0 => lang('active'), 1 => lang('inactive'));
                                    echo form_dropdown('status', $status, '', 'id="status" class="form-control tip" required="required" style="width:100%;"');
                                    ?>
                                </div>                             
                            </div>
                    </fieldset>

                    <fieldset class="scheduler-border">
                            <legend class="scheduler-border"><?= lang('accumulation') ?></legend>

                            <div class="form-group col-lg-12">
                                <div class="form-group">
                                <?= lang("accumulation") ?>
                                <button type="button" class="btn btn-primary btn-xs" id="add_accumulation"><i class="fa fa-plus"></i>
                                </button>
                              </div>

                            <table class="loyalty-container">
                                <thead>
                                    <th class="col-sm-2"><?=lang('Name')?></th>
                                    <th class="col-sm-2"><?=lang('start_price')?></th>
                                    <th class="col-sm-2"><?=lang('end_price')?></th>
                                    <th class="col-sm-2"><?=lang('per_amount')?></th>
                                    <th class="col-sm-2"><?=lang('per_point')?></th>
                                    <th class="col-sm-2"><?=lang('remove')?></th>
                                </thead>
                                <tbody class="each-accumulation"> 
                                    <tr class="accumulation-count_1 accumulation-count">
                                        <td class="col-sm-2">
                                            <input class="form-control" type="text" name="accumulation_name[]" id="accumulation_name">
                                        </td>
                                        <td class="col-sm-2">
                                            <input class="form-control" type="text" name="start_price[]" id="start_price">
                                        </td>
                                        <td class="col-sm-2">
                                            <input class="form-control" type="text" name="end_price[]" id="end_price">
                                        </td>
                                        <td class="col-sm-2">
                                            <input class="form-control" type="text" name="per_amount[]" id="per_amount">
                                        </td>
                                        <td class="col-sm-2">
                                            <input class="form-control" type="text" name ="per_point[]" id="per_point">
                                        </td>
                                        <td class="col-sm-2">
                                            <input type="checkbox" name="status[]" checked="checked" value="1"> &nbsp;&nbsp;<a href="javascript:void(0);" data-id="" class="btn btn-primary btn-xs remove-accumulation-loyalty"><i class="fa fa-trash-o"></i></a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            </div>
                    </fieldset>
                    <fieldset class="scheduler-border">
                            <legend class="scheduler-border"><?= lang('reedemption') ?></legend>

                            <div class="form-group col-lg-12">
                                <div class="form-group">
                                <?= lang("reedemption") ?>
                                <button type="button" class="btn btn-primary btn-xs" id="add_reedemption"><i class="fa fa-plus"></i>
                                </button>
                              </div>

                            <table class="loyalty-container">
                                <thead>
                                    <th class="col-sm-2"><?=lang('per_point')?></th>
                                    <th class="col-sm-2"><?=lang('per_amount')?></th>                                    
                                    <th class="col-sm-2"><?=lang('remove')?></th>
                                </thead>
                                <tbody class="each-reedemption"> 
                                    <tr class="reedemption-count_1 reedemption-count">
                                        <td class="col-sm-2">
                                            <input class="form-control" type="text" name="per_point" id="per_point">
                                        </td>
                                        <td class="col-sm-2">
                                            <input class="form-control" type="text" name="per_amount" id="per_amount"></td>
                                        <td class="col-sm-2">
                                           <input type="checkbox"  class="checkbox" name="status[]" checked="checked" value="1">&nbsp;&nbsp;<a href="javascript:void(0);" data-id="" class="btn btn-primary btn-xs remove-reedemption-loyalty"><i class="fa fa-trash-o"></i></a></td>
                                    </tr>
                                </tbody>
                            </table>
                            </div>
                    </fieldset>                    
                    </div>
                </div>
           
             <div style="clear: both;height: 10px;"></div>
                <div class="form-group col-lg-12">    
                <button class="btn btn-block btn-lg btn-primary" id="add_loyalty"><?=lang('submit');?></button>                
                    <!-- <?php echo form_submit('add_loyalty', $this->lang->line("submit"), 'class="btn btn-primary" id="add_loyalty"'); ?> -->
                </div>
                <?= form_close(); ?>
                 </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="<?= $assets ?>styles/jquery-ui.css">
<script src="<?= $assets ?>js/jquery-ui.js"></script>
<style type="text/css">
    .each-accumulation td,.each-reedemption td
    {
        padding-top: 20px;
    }
</style>

<script type="text/javascript">
    $(document).ready(function(){

    var ra = 0;
    $('#add_accumulation').on('click', function () {
        if (ra <= 10) {

            var html = '<tr class="accumulation-count_' + ra + ' accumulation-count" style="margin-bottom: 100px!important;"><td class="col-sm-2"><input class="form-control" type="text" name="accumulation_name[]" id="accumulation_name"></td><td class="col-sm-2"><input class="form-control" type="text" name="start_price[]" id="start_price"></td><td class="col-sm-2"><input class="form-control" type="text" name="end_price[]" id="end_price"></td><td class="col-sm-2"><input class="form-control" type="text" name="per_amount[]" id="per_amount"></td><td class="col-sm-2"><input class="form-control" type="text" name ="per_point[]" id="per_point"></td><td class="col-sm-2"><input type="checkbox"  class="checkbox" name="status[]" checked="checked" value="1"></div>&nbsp;&nbsp;<a href="javascript:void(0);" data-id="" class="btn btn-primary btn-xs remove-accumulation-loyalty"><i class="fa fa-trash-o"></i></a></td></tr><br>';
             
            $('.each-accumulation').append(html);
            $('.checkbox').iCheck({
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'iradio_square-blue',
                increaseArea: '20%'
            });            
            ra++;           
        }else{
            bootbox.alert("<?= lang('max_reached') ?>");            
            return false;
        }
    });

$("body").on('click','.remove-accumulation-loyalty', function(){
            $(this).closest('.accumulation-count').remove();
        });

 var re = 0;
    $('#add_reedemption').on('click', function () {
        if (re <= 10) {


            var html = '<tr class="reedemption-count_'+re+' reedemption-count"><td class="col-sm-2"><input class="form-control" type="text" name="per_point[]" id="per_point"></td><td class="col-sm-2"><input class="form-control" type="text" name="per_amount[]" id="per_amount"></td><td class="col-sm-2"><input type="checkbox"  class="checkbox" name="status[]" checked="checked" value="1"></div>&nbsp;&nbsp;<a href="javascript:void(0);" data-id="" class="btn btn-primary btn-xs remove-reedemption-loyalty"><i class="fa fa-trash-o"></i></a></td></tr>';
             
            $('.each-reedemption').append(html);
            $('.checkbox').iCheck({
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'iradio_square-blue',
                increaseArea: '20%'
            });            
            re++;           
        }else{
            bootbox.alert("<?= lang('max_reached') ?>");            
            return false;
        }
    });

$("body").on('click','.remove-reedemption-loyalty', function(){
            $(this).closest('.reedemption-count').remove();
        });

$(document).on('ifChecked', '.checkth, .checkft, .checkbox ', function(event) {
    $('.checkth, .checkft').iCheck('check');
    $('.multi-select').each(function() {
        $(this).iCheck('check');
    });
});
$(document).on('ifUnchecked', '.checkth, .checkft, .checkbox', function(event) {
    $('.checkth, .checkft').iCheck('uncheck');
    $('.multi-select').each(function() {
        $(this).iCheck('uncheck');
    });
});
$(document).on('ifUnchecked', '.multi-select, .checkbox', function(event) {
    $('.checkth, .checkft').attr('checked', false);
    $('.checkth, .checkft').iCheck('update');
});



});

$(document).ready(function(){    
        $('#to_date').datepicker({
        dateFormat: "yy-mm-dd" ,
        minDate:  0,      
    });
    $("#from_date").datepicker({
        dateFormat: "yy-mm-dd" ,  
        minDate:  0,      
        onSelect: function(date){            
            var date1 = $('#from_date').datepicker('getDate');           
            var date = new Date( Date.parse( date1 ) ); 
            date.setDate( date.getDate());        
            var newDate = date.toDateString(); 
            newDate = new Date( Date.parse( newDate ) );                      
            $('#to_date').datepicker("option","minDate",newDate);            
        }
    });

    $(document).on('click','#add_loyalty',function(e){
        $("#add-loyalty-form").submit();
        
    });
});


/*$(document).ready(function(){
    
        $('#to_date').datepicker({
        dateFormat: "yy-mm-dd" ,
        minDate:  0,     
        fontAwesome: true, 
        todayBtn: 1, 
        autoclose: 1,
        autoclose: 1, 
        minView: 2 
    });
    $("#from_date").datepicker({
        dateFormat: "yy-mm-dd" ,  
        minDate:  0,      
        onSelect: function(date){            
            var date1 = $('#from_date').datepicker('getDate');           
            var date = new Date( Date.parse( date1 ) ); 
            date.setDate( date.getDate());        
            var newDate = date.toDateString(); 
            newDate = new Date( Date.parse( newDate ) );                      
            $('#to_date').datepicker("option","maxDate",newDate);            
        }
    });
});*/

</script>