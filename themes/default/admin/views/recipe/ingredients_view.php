<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<style>
     .td-value{
        font-weight: bold;
     }
</style>
<div class="modal-dialog modal-lg" style="width: 1159px !important;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>            
        </div>
        <div class="modal-body">
            <table class="table custom_tables">
                        <tbody>
                            <tr>
                        <td width="100px">
                            <?= lang("item_type", "type") ?>
                        </td>
                     <td width="350px">
							<?php		
							
							echo $product_recipe_master ? $product_recipe_master->type : '';							
							?>
						</td>
						<td width="100px">
							<?= lang("item_name", "item_name");    ?>
						</td>
						<td width="350px">
							
								<?php  if(!empty($productlist)){
									$product_name =$product_recipe_master->recipe_id;
									$rv=!empty($product_recipe_master->varient_name)?$product_recipe_master->varient_name:"";
									$product_name=$product_name.$rv;
								foreach($productlist as $row){ 
								$variant=!empty($row->varient_name)?$row->varient_name:""; 
									   $product_lst_name=$row->id .$variant; ?>
								 <?php  echo  ($product_lst_name == $product_name)?$row->name .$variant:"";     ?>
								<?php  }  }  ?>
				
							
						</td>
                    </tr>        
<tr>
						<td width="100px">
							<?= lang("qty", "qty"); ?>
						</td>
						<td width="350px">
							<?php echo $product_recipe_master->qty ;  ?>
						</td>
						
						<td width="100px">
								<?= lang("UOM", "UOM"); ?>
							</td>
							<td width="350px">
							
									<?php   if($recipe_uom){ foreach($recipe_uom as $uom){   ?>
									 <?php  echo ($uom->id == $product_recipe_master->uom)?$uom->name:"";   ?>
									<?php   }  } ?>
								
							</td>
</tr>							
                    </tbody>
                </table>

          <div class="table-responsive">            
                    <table id="ItemData" class="table table-bordered table-condensed table-hover table-striped">
                        <thead>
                        <tr>
                            <th ><?= lang('s.no'); ?></th>
                            <th><?= lang('item_name'); ?></th>                                
                            <th ><?= lang("qty"); ?></th>
                            <th ><?= lang("uom"); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $i =1; foreach($product_recipe as $k => $row) : ?>
                        <tr>
                            <?php $row->category_name;
                                 if($row->category_name != ''){
                                    $cat = ' Cat-'.$row->category_name;
                                    }else{
                                        $cat = '';
                                    }                               
                                    if($row->subcategory_name != ''){
                                        $sub_cat = ' Sub-'.$row->subcategory_name;
                                    }else{
                                        $sub_cat = '';
                                    }
                                    if($row->brand_name != ''){
                                        $brand = ' Brand-'.$row->brand_name;
                                    }else{
                                        $brand = '';
                                    }                               
                            ?>
                            <td><?=$i?></td>
                            <td><?=$row->recipe_name.$cat.' | '.$sub_cat.' | '.$brand ?></td>
                            <td><?=$row->quantity?></td>
                            <td><?= $row->units_name; ?></td>
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