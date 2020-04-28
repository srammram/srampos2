<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<style>
     .td-value{
        font-weight: bold;
     }
</style>
<div class="modal-dialog modal-lg" style="width: 1011px !important;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?=lang('Store_request')?> - <?=$store_req->reference_no?></h4>
        </div>
		
        <div class="modal-body">
          <div class="table-responsive">
            <table class="custom_tables">
                <tr>
                    <td colspan=2 ><?=lang('date')?> : </td>
                    <td colspan=2 class="td-value"><?=$store_req->date?></td>
                    
                    <td colspan=2 ><?=lang('quotation_no')?> : </td>
                    <td colspan=2 class="td-value"><?=$store_req->reference_no?></td>
                    
                     <td colspan=2 ><?=lang('status')?>: </td>
                    <td colspan=2  class="td-value"><?=$store_req->status?></td>
                </tr>
                <tr>
                    
                    <?php
                    $from_store = '';
                    $to_store = '';
					foreach ($stores as $store) {
						 if($store_req->from_store_id==$store->id){
					    	$from_store = $store->name;
						 }      
					}
					echo $from_store;
                    foreach($stores as $k => $row) :
                        if($store_req->to_store_id==$row->id){
                            $to_store = $row->name;
                        }                    
                    endforeach; ?>
                    <td colspan=2 ><?=lang('From_store')?>: </td>
                    <td colspan=2  class="td-value"><?=$from_store?></td>
                    
                    <td colspan=2 ><?=lang('to_store')?>: </td>
                    <td colspan=2  class="td-value"><?=$to_store?></td>
                    
                </tr>
                <tr>
                    <td colspan=2 ><?=lang('note')?>: </td>
                    <td colspan=2  class="td-value"><?=$store_req->note?></td>
                </tr>
                
              
                
            </table>
                    <table id="ItemData" class="table table-bordered table-condensed table-hover table-striped">
                        <thead>
                        <tr>
                                            <th ><?= lang('s.no'); ?></th>
                                            <th><?= lang('code'); ?></th>
                                            <th ><?= lang("description"); ?></th>
                                            <th ><?= lang("qty"); ?></th>
                                        </tr>
                        </thead>
                        <tbody>
                        <?php $i =1; foreach($store_req_items as $k => $row) : ?>
                        <tr>
                            <td><?=$i?></td>
                            <td><?=$row['row']->product_code?></td>
                            <td><?=$row['row']->product_name?></td>
                            <td><?=$row['row']->quantity?></td>
                        </tr>
                        <?php $i++;endforeach; ?>
                        </tbody>
                    </table>
                   
                </div>

        </div>
        <div class="modal-footer">
            
        </div>
    </div>
    
</div>
<?= $modal_js ?>