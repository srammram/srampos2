<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!-- <script type="text/javascript" src="<?=$assets ?>js/customer_discount.js"></script> -->
  <link rel="stylesheet" href="<?= $assets ?>styles/jquery-ui.css">
  <script src="<?= $assets ?>js/jquery-ui.js"></script>

  <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form','id'=>'loyalty-card-form');
                      echo admin_form_open("loyalty_settings/loyalty_card_add/", $attrib); ?>
<!-- 
<?= admin_form_open('loyalty_settings/loyalty_card_add', 'id="loyalty-card-form"') ?> -->

<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('loyalty_card_generation'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <p class="introtext">
                        <div class="col-lg-12">                           
                                <?= lang('Loyalty_name', 'loyalty_name'); ?>

                                <div class="controls">
                                    <select style="display: "  name="loyalty_name" class="form-control pos-input-tip loyalty_name" id="loyalty_name" >
                                   <option value="0">No</option> 
                                    <?php
                                    foreach ($loyalty as $loyal) {?>
                                      <option value="<?php echo $loyal->id; ?>"  data-prefix="<?php echo $loyal->prefix; ?>" data-serial_number="<?php echo $loyal->serial_number; ?>" ><?php echo $loyal->name; ?>
                                      </option>
                                    <?php }?>
                                </select>
                                </div>                             
                            </div>  
                                          
                            <div class="form-group col-lg-6">
                                 <?= lang('prefix', 'prefix'); ?>
                                    <div class="controls ">
                                      <input type="text" name="prefix" readonly class="form-control" placeholder="Prefix " id="prefix"  value="" autocomplete="off">
                                    </div>
                            </div>                         
                        
                            <div class="form-group col-lg-6">
                                    <?= lang('Card_number', 'card_number'); ?>
                                  <div class="controls">
                                    <input type="text" name="card_number" readonly  class="form-control" placeholder="Card Number" id="card_number"  value="" autocomplete="off">
                                  </div>
                            </div>
                        
                            <div class="form-group col-lg-6">
                                    <?= lang('Number_of_Cards', 'number_of_Cards'); ?>
                                  <div class="controls">
                                    <input type="text" name="number_of_cards"  maxlength="3" class="form-control numberonly" placeholder="Number Of Cards " id="number_of_cards"  value="" autocomplete="off">
                                  </div>
                            </div>

                           
                        
                        <div class="form-group col-lg-4">                                                
                       </div>

                    <div class="form-group col-lg-4"> 
                        <input type="submit" class="btn btn-primary " value="Submit" id="card_generate" >                           
                    </div>              
            </div>

        </div>
    </div>
</div>
<?= form_close() ?>
<script>
$(".numberonly").keypress(function (event){    
    if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
        event.preventDefault();
    }  
});
</script>
<script>
    $(document).ready(function(){
        $('#expiry_date').datepicker({
            dateFormat: "yy-mm-dd" ,
            minDate:  0,      
        });

  $(document).on('click', '#card_generate', function () {        
        var number_of_cards = $("#number_of_cards").val();
        if(number_of_cards)
        {            
            $(this).val('<?=lang('loading');?>').attr('disabled', true);                
            $('#loyalty-card-form').submit();
        }     
        else{
            bootbox.alert('Please Enter number_of_cards Fields');
            return false;
        }   
    }); 

    $(document).on('change', '.loyalty_name', function () {
            $this_obj = $(this).val();            
            $("#prefix").val('');
            $("#card_number").val('');
            if($this_obj != 0){
                var data_prefix = $("#loyalty_name").find(':selected').attr('data-prefix');
                var data_serial_number = $("#loyalty_name").find(':selected').attr('data-serial_number');
                $("#prefix").val(data_prefix);
                $("#card_number").val(data_serial_number);
            }else{
                $("#prefix").val('');
                $("#card_number").val('');
            }

            /*$.ajax({
                type: 'POST',
                url: '<?=admin_url('loyalty_settings/getLoyaltyNames');?>',
                dataType: "json",
                 async : false,
                data: {
                    recipeids: recipeids,recipeqtys: recipeqtys,discountid:$this_obj.val(),divide: divide
                },
                success: function (data) {                
                    console.log(data);
                    input_discount += data;
                }
           });*/
        });


    /*$("#app_wrapper").on("contextmenu",function(){             
       return false;
    }); 
    
    document.onkeydown = function(e) {
        if(event.keyCode == 123) {
        return false;
        }
        if(e.ctrlKey && e.shiftKey && e.keyCode == 'I'.charCodeAt(0)){
        return false;
        }
        if(e.ctrlKey && e.shiftKey && e.keyCode == 'J'.charCodeAt(0)){
        return false;
        }
        if(e.ctrlKey && e.keyCode == 'U'.charCodeAt(0)){
        return false;
        }
    }*/
}); 
</script>

