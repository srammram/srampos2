<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<style>
label{ font-weight:bold; }
</style>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?= lang('view_memeber_discount_card_status'); ?></h4>
        </div>        
        
<div class="modal-body">
          <div class="table-responsive">
            <table class="custom_tables">
                <tbody><tr>
                    <td colspan="2"> Prefix : </td>
                    <td colspan="2" class="td-value">  <?php  echo $discount_card->prefix ; ?></td>
                    
                    <td colspan="2">Starting Serial No : </td>
                    <td colspan="2" class="td-value"> <?php echo $discount_card->serial_no; ?></td>
                    
                    <td colspan="2"> No of Vouchers : </td>
                    
                                        <td colspan="2" class="td-value">    <?php  echo $discount_card->no_of_vouchers; ?></td>
                </tr>
                <tr>   
                    <td colspan="2"> Valid From: </td>
                    <td colspan="2" class="td-value"> <?php echo date('Y-m-d', strtotime($discount_card->from_date)); ?></td>
                    
                    <td colspan="2">Valid Upto: </td>
                    <td colspan="2" class="td-value">  <?php echo date('Y-m-d', strtotime($discount_card->from_date)); ?></td>
                    
                 
                </tr>
               
                
              
            </tbody></table>
                    <table id="ItemData" class="table table-bordered table-condensed table-hover table-striped">
                        <thead>
                        <tr>
                                            <th>S.no</th>					    
                                            <th>Card Number</th>
                                            
											<th> Status</th>
                                            <th> Issued On</th>
                                            <th> Blocked On</th>
                                           
                                        </tr>
                        </thead>
                        <tbody>
                             	<?php   if(!empty($discount_card_status)){ $i=1; foreach($discount_card_status as $card){  ?>                   <tr>
                            <td><?php echo $i ; ?></td>
                            <td><?php  echo $card->card_no; ?></td>
                            <td><?php  switch($card->status){
								case 1:
								echo '<span class="row_status label label-default">Not Issued</span>';
								break;
								case 2:
									echo '<span class="row_status label label-success"> Issued</span>';
								break;
								
								case 4:
									echo '<span class="row_status label label-danger"> Blocked</span>';
								break;
							}
							?></td>
                            <td><?php  if(!empty($card->issued_on)){ echo $card->issued_on;   } ?></td>
							<td><?php  if(!empty($card->blocked_on)){ echo $card->blocked_on;   } ?></td>
                        </tr>
                         <?php $i++; }  } ?>    
                        </tbody>
                    </table>
                   
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

