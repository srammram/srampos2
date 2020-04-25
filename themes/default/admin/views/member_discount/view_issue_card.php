<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<style>
label{ font-weight:bold; }
</style>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?= lang('View_issued_card'); ?></h4>
        </div>        
        
		<div class="modal-body">
          <div class="table-responsive">
            <table class="custom_tables">
                <tbody><tr>
				 <td colspan="2"> Discount Name : </td>
                    <td colspan="2" class="td-value">  <?php  echo $issue_card->name ; ?></td>
                    <td colspan="2"> Discount Card Number : </td>
                    <td colspan="2" class="td-value">  <?php  echo $issue_card->card_no ; ?></td>
                    
                    <td colspan="2">Customer : </td>
                    <td colspan="2" class="td-value"> <?php echo $issue_card->customer; ?></td>
                    
                    <td colspan="2"> Selling Price : </td>
                    
                  <td colspan="2" class="td-value">    <?php  echo $issue_card->selling_price; ?></td>
                </tr>
			
                <tr>   
				   <td colspan="2"> Valid Upto: </td>
                    <td colspan="2" class="td-value">  <?php echo date('Y-m-d', strtotime($issue_card->valid_upto)); ?></td>
                    <td colspan="2"> Disount: </td>
                    <td colspan="2" class="td-value"> <?php echo $issue_card->discount; ?> </td>
                    
                    <td colspan="2">Discount Type: </td>
                    <td colspan="2" class="td-value">   <?php echo $issue_card->discounttype; ?></td>
                    
                 
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

