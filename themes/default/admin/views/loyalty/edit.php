<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!-- <script type="text/javascript" src="<?=$assets ?>js/customer_discount.js"></script> -->
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('edit_loyalty_configuration'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext">

                <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form','id'=>'edit-loyalty-form');
                     echo admin_form_open("loyalty_settings/edit/".$loyalty->id, $attrib); ?>
                      
                        <fieldset class="scheduler-border">
                            <legend class="scheduler-border"><?= lang('main_settings') ?></legend>

                            <div class="form-group col-lg-12">
                                <?= lang('name', 'name'); ?>
                                <?= form_input('name', set_value('name', $loyalty->name), 'class="form-control tip" id="name" required="required"'); ?>                                
                            </div>

                            <div class="form-group col-lg-6">
                                <?= lang('from_date', 'from_date'); ?>
                                    <div class="controls ">
                                        <?php 
                                            $from_date = date("Y-m-d", strtotime($loyalty->from_date));
                                        ?>
                                      <input type="text" name="from_date" class="form-control" placeholder="From Date " id="from_date" required="required" value="<?=@$from_date?>" autocomplete="off">
                                    </div>
                            </div>
                            <div class="form-group col-lg-6">
                                <?= lang('to_date', 'to_date'); ?>
                                  <div class="controls">
                                    <?php
                                      $end_date = date("Y-m-d", strtotime($loyalty->end_date));
                                    ?>
                                    <input type="text" name="to_date" class="form-control" placeholder="To Date " id="to_date" required="required" value="<?=@$end_date?>" autocomplete="off">
                                  </div>
                            </div>

                            <div class="form-group col-lg-6">
                                <?= lang('prefix', 'prefix'); ?>
                                    <div class="controls ">
                                      <input type="text" name="prefix" class="form-control" placeholder="Prefix " id="prefix" required="required" value="<?=$loyalty->prefix ?>" autocomplete="off">
                                    </div>
                            </div>
                            <div class="form-group col-lg-6">
                                <?= lang('Card_number', 'card_number'); ?>
                                  <div class="controls">
                                    <input type="text" name="card_number" class="form-control" placeholder="Card Number" id="card_number" required="required" value="<?=$loyalty->serial_number ?>"   autocomplete="off">
                                  </div>
                            </div>

                            <div class="form-group col-lg-6">
                                <?= lang('Eligibity_point', 'Eligibity_point'); ?>
                                <div class="controls ">
                                  <input type="text" name="eligibity_point" class="form-control" placeholder="Eligibity Point " id="eligibity_point" required="required" value="<?=$loyalty->eligibity_point ?>"  autocomplete="off">
                                </div>
                            </div>
                            <div class="form-group col-lg-6">
                                <?= lang('loyalty_status'); ?>
                                <div class="controls">
                                    <?php
                                    $loyalty_status = array(1 => lang('active'), 0 => lang('inactive'));
                                    echo form_dropdown('loyalty_status', $loyalty_status,  $loyalty->status, 'id="loyalty_status" class="form-control tip" required="required" style="width:100%;"');
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
                                    <th class="col-sm-2"><?=lang('start_price')?></th>
                                    <th class="col-sm-2"><?=lang('end_price')?></th>
                                    <th class="col-sm-2"><?=lang('per_amount')?></th>
                                    <th class="col-sm-2"><?=lang('point')?></th>
                                    <th class="col-sm-2"><?=lang('remove')?></th>
                                </thead>
                                <tbody class="each-accumulation"> 
                                     <?php                                      
                                     foreach ($accumulation as $k => $accum) :
                                    ?>
                                      <tr class="accumulation-count_1 accumulation-count">
                                       <!--  <td class="col-sm-2">
                                            <input class="form-control" type="text" name="accumulation_name[]"  value="<?= $accum->accumulation_name; ?>">
                                         </td>  -->  

                                         <td class="col-sm-2">
                                            <input class="form-control" type="text" name="start_price[]"   value="<?=$accum->start_amount?>">
                                         </td> 
                                          <td class="col-sm-2">
                                            <input class="form-control" type="text" name="end_price[]"  value="<?=$accum->end_amount?>">
                                         </td> 
                                          <td class="col-sm-2">
                                            <input class="form-control" type="text" name="per_amount[]" value="<?=$accum->per_amounts?>">
                                         </td> 
                                          <td class="col-sm-2">
                                            <input class="form-control" type="text" name ="per_point[]" value="<?=$accum->per_points?>">
                                         </td>                                        
                                        <td class="col-sm-2"><a "javascript:void(0);"  data-vid="<?=$accum->id?>" data-id="<?=$accum->id?>" class="btn btn-primary btn-xs remove-accumulation-loyalty"><i class="fa fa-trash-o"></i></a></td>
                                      </tr>
                                    <?php endforeach ; ?>
                                </tbody>
                            </table>
                            </div>
                    </fieldset>
                    <fieldset class="scheduler-border">
                            <legend class="scheduler-border"><?= lang('redemption') ?></legend>

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
                                    <?php                                      
                                     foreach ($reedemption as $k => $reedem) :
                                    ?>
                                      <tr class="reedemption-count_1 reedemption-count">
                                        <td class="col-sm-2">
                                            <input class="form-control" type="text" name="reedemption_per_point[]"  value="<?= $reedem->points; ?>">
                                         </td>   

                                         <td class="col-sm-2">
                                            <input class="form-control" type="text" name="reedemption_per_amount[]"   value="<?=$reedem->amount?>">
                                         </td>                                      
                                        <td class="col-sm-2"><a "javascript:void(0);"  data-vid="<?=$reedem->id?>" data-id="<?=$reedem->id?>" class="btn btn-primary btn-xs remove-reedemption-loyalty"><i class="fa fa-trash-o"></i></a></td>
                                      </tr>
                                    <?php endforeach ; ?>                                   
                                </tbody>
                            </table>
                            </div>
                    </fieldset>  
            <div style="clear: both;height: 10px;"></div>
                    <div class="form-group col-lg-12">
                          <?php echo form_submit('add_loyalty', $this->lang->line("submit"), 'class="btn btn-primary"'); ?>
                    </div>
                <?= form_close(); ?>
            </div>
        </div>
    </div>
</div>
<link rel="stylesheet" href="<?= $assets ?>styles/jquery-ui.css">
<script src="<?= $assets ?>js/jquery-ui.js"></script>
<script>
    $(".numberonly").keypress(function (event){    
        if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
            event.preventDefault();
        }
    });
</script>
<script>
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

    var ra = 0;
    $('#add_accumulation').on('click', function () {
        if (ra <= 10) {

            var html = '<tr class="accumulation-count_' + ra + ' accumulation-count" style="margin-bottom: 100px!important;"><td class="col-sm-2"><input class="form-control" type="text" name="start_price[]" id="start_price"></td><td class="col-sm-2"><input class="form-control" type="text" name="end_price[]" id="end_price"></td><td class="col-sm-2"><input class="form-control" type="text" name="per_amount[]" id="per_amount"></td><td class="col-sm-2"><input class="form-control" type="text" name ="per_point[]" id="per_point"></td><td class="col-sm-2"><a href="javascript:void(0);" data-id="" class="btn btn-primary btn-xs remove-accumulation-loyalty"><i class="fa fa-trash-o"></i></a></td></tr><br>';
             
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
            var html = '<tr class="reedemption-count_'+re+' reedemption-count"><td class="col-sm-2"><input class="form-control" type="text" name="reedemption_per_point[]" id="reedemption_per_point"></td><td class="col-sm-2"><input class="form-control" type="text" name="reedemption_per_amount[]" id="reedemption_per_amount"></td><td class="col-sm-2"><a href="javascript:void(0);" data-id="" class="btn btn-primary btn-xs remove-reedemption-loyalty"><i class="fa fa-trash-o"></i></a></td></tr>';
             
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
		
 });
</script>

