<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<style>
label{ font-weight:bold; }
</style>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?= lang('view_memeber_discount_card_'); ?></h4>
        </div>        
        
<div class="modal-body">
          <div class="table-responsive">
            <table class="custom_tables">
                <tbody><tr>
				 <td colspan="2"> Discount Name : </td>
                    <td colspan="2" class="td-value">  <?php  echo $discount_card->name ; ?></td>
                    <td colspan="2"> Prefix : </td>
                    <td colspan="2" class="td-value">  <?php  echo $discount_card->prefix ; ?></td>
                    
                    <td colspan="2">Starting Serial No : </td>
                    <td colspan="2" class="td-value"> <?php echo $discount_card->serial_no; ?></td>
                    
                    <td colspan="2"> No of Vouchers : </td>
                    
                  <td colspan="2" class="td-value">    <?php  echo $discount_card->no_of_vouchers; ?></td>
                </tr>
			
                <tr>   
				   <td colspan="2"> Selling Price: </td>
                    <td colspan="2" class="td-value">  <?php echo $discount_card->selling_price; ?></td>
                    <td colspan="2"> Valid From: </td>
                    <td colspan="2" class="td-value"> <?php echo date('Y-m-d', strtotime($discount_card->from_date)); ?></td>
                    
                    <td colspan="2">Valid Upto: </td>
                    <td colspan="2" class="td-value">  <?php echo date('Y-m-d', strtotime($discount_card->from_date)); ?></td>
                    
                 
                </tr>
               
                
              
            </tbody></table>
                  
                   
                </div>

        </div>
      </div>
    </div>
        </div>        
    </div>
    <?= form_close(); ?>
</div>


<script>
$("input").prop('readonly', true);

</script>

