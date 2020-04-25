<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
 <link rel="stylesheet" href="<?= $assets ?>styles/jquery-ui.css">
  <script src="<?= $assets ?>js/jquery-ui.js"></script>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?= lang('view_memeber_discount'); ?></h4>
        </div>        
        <div class="modal-body">  



 <div class="table-responsive">
            <table class="custom_tables">
                <tbody><tr>
                    <td colspan="2">  <?= lang('name', 'name'); ?>      </td>
                    <td colspan="2" class="td-value"> :   <?php   echo   $discount->name;  ?></td>
                    
                    <td colspan="2">  <?= lang('from_date', 'from_date'); ?>  </td>
                    <td colspan="2" class="td-value">   :                 <?php echo date('Y-m-d', strtotime($discount->from_date)); ?></td>
                    
                    <td colspan="2"> <?= lang('to_date', 'to_date'); ?></td>
                              <td colspan="2">         :   <?php echo date('Y-m-d', strtotime($discount->to_date)); ?></td>
                </tr>
                <tr>   
				<td colspan="2">
                    <?= lang('from_time', 'from_time'); ?>  </td>
                    <td colspan="2" class="td-value"> :<?php echo date('H:i', strtotime($discount_card->from_date)); ?></td>
                <td colspan="2">    
                     <?= lang('to_time', 'to_time'); ?> </td>
                    <td colspan="2" class="td-value">  : <?php echo date('H:i', strtotime($discount->to_time)); ?></td>
					<td colspan="2">    
                     <?= lang('discount', 'discount'); ?> </td>
                    <td colspan="2" class="td-value">  : <?php echo $discount->discount; ?></td>
					<td colspan="2">    
                     <?= lang('discount type', 'discount type'); ?> </td>
                    <td colspan="2" class="td-value">  : <?php echo $discount->discount_type; ?></td>
                </tr>
            </tbody></table>
                    <table id="ItemData" class="table table-bordered table-condensed table-hover table-striped">
                        <thead>
                        <tr>
                         <th>S.no</th>					    
                         <th>Status</th>
			             <th> Days</th>
                         </tr>
                        </thead>
                        <tbody>
                             	<tr><td>1</td> <td> <?php if(in_array('Monday',explode(',',$discount->week_days))) { echo '<button type="button" class="btn btn-primary active">';}else{ echo ' <button type="button" class="btn btn-primary disabled">';   } ?></td><td>&nbsp;&nbsp;Monday</td></tr>
								<tr><td>2</td><td><?php if(in_array('Monday',explode(',',$discount->week_days))) { echo '<button type="button" class="btn btn-primary active">';}else{ echo ' <button type="button" class="btn btn-primary disabled">';   } ?>  </td><td>&nbsp;&nbsp;Tuesday</td></tr>
								<tr><td>3</td><td><?php if(in_array('Monday',explode(',',$discount->week_days))) { echo '<button type="button" class="btn btn-primary active">';}else{ echo ' <button type="button" class="btn btn-primary disabled">';   } ?></td><td>&nbsp;&nbsp;Wednesday</td></tr>
								<tr><td>4</td><td> <?php if(in_array('Monday',explode(',',$discount->week_days))) { echo '<button type="button" class="btn btn-primary active">';}else{ echo ' <button type="button" class="btn btn-primary disabled">';   } ?> </td><td>&nbsp;&nbsp;Thursday</td></tr>
								<tr><td>5</td><td> <?php if(in_array('Monday',explode(',',$discount->week_days))) { echo '<button type="button" class="btn btn-primary active">';}else{ echo ' <button type="button" class="btn btn-primary disabled">';   } ?>  </td><td>&nbsp;&nbsp;Friday</td></tr>
								<tr><td>6</td><td> <?php if(in_array('Monday',explode(',',$discount->week_days))) { echo '<button type="button" class="btn btn-primary active">';}else{ echo ' <button type="button" class="btn btn-primary disabled">';   } ?>  </td><td>&nbsp;&nbsp;Saturday</td></tr>
								<tr><td>7</td><td> <?php if(in_array('Monday',explode(',',$discount->week_days))) { echo '<button type="button" class="btn btn-primary active">';}else{ echo ' <button type="button" class="btn btn-primary disabled">';   } ?> </td><td>&nbsp;&nbsp;Sunday</td></tr>
								
                        </tbody>
                    </table>
                   
                </div>

        </div>
      </div>
            </div>

        </div>
    </div>
</div>
