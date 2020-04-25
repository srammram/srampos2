<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<style>
label{ font-weight:bold; }
</style>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?= lang('view_bbq_menu'); ?></h4>
        </div>        
        <div class="modal-body">     
          <div class="form-group">
            <label for="name"><?php echo $this->lang->line("name"); ?></label>
            <div class="controls"> <?php echo  $buy->name ; ?> </div>
          </div>
         
          <div class="form-group">
            <label for="method"><?php echo $this->lang->line("Buy Method"); ?></label>
            <div class="controls">
           <?php if($buy->buy_method == 'buy_x_get_x'){ echo 'Buy X Get X'; }
                 if($buy->buy_method == 'buy_x_get_y'){ echo 'Buy X Get Y'; } ?>
            </div>
          </div>
          
          <div class="form-group">
            <label for="method"><?php echo $this->lang->line("Date & Time"); ?></label>
           </div>
           <div class="row">
          <div class="form-group  col-lg-6 date_div">
                  <div class="controls ">
				  <label>From Date</label><br>
                    <?php echo date('d-m-Y', strtotime($buy->start_date)); ?>
                  </div>
                </div>
                
                <div class="form-group  col-lg-6 date_div">
                  <div class="controls">
				   <label>To Date</label><br>
                    <?php echo date('d-m-Y', strtotime($buy->end_date)); ?>
                  </div>
                </div>
                
                <div class="form-group  col-lg-6 time_div">
                  <div class="controls ">
				  <label>From Time</label><br>
                    <?php echo date('H:i', strtotime($buy->start_time)); ?>
                  </div>
                </div>
                <div class="form-group  col-lg-6 time_div">
                  <div class="controls">
				  <label>To Time</label><br>
                    <?php echo date('H:i', strtotime($buy->end_time)); ?>
                  </div>
                </div>
            </div>
                          
      
           
        
        <fieldset class="buy_buy_x_get_x">
            <legend>Item:
          
            </legend>
            <div id="itemx">
            	
              <div class="well col-lg-12">
                <div class="form-group col-lg-12">
                  <div class="controls">
                     <?php if($item_row->buy_type == 'Sale Items') ?>
                  </div>
                </div>
              	<div class="form-group col-lg-6 recipe">
                  <div class="controls">
                   		<?php  foreach($recipe as $recipe_row){
						  if($item_row->buy_item == $recipe_row->id){ echo $recipe_row->name; }
						}
					  ?>
                   </div>
                </div>
                <div class="form-group col-lg-6 recipe_get">
                  <div class="controls">
                   		<?php
						foreach($recipe as $recipe_row){
					     if($item_row->get_item == $recipe_row->id){ echo $recipe_row->name; }
						}
					  ?>
                   </div>
                </div>
				<div class="form-group col-lg-6 ">
				<label>Buy Variant</label>
                  <div class="controls">
				  <?php  
				  foreach ($buy_variants as $b_variant) {
                                    $res[$b_variant->id] = $b_variant->name;
                                } 
                   echo form_dropdown('buy_variant_id', $res, $b_variant->buy_variant_id, 'class="form-control" data-placeholder="' . lang("select") . ' ' . lang("Variants") . '" id="buy_variant_id" style="width:100%;" disabled');
				   ?>
                   </div>
                </div>
				<div class="form-group col-lg-6 ">
					<label>Get Variant</label>
                  <div class="controls">
				  <?php 
				  foreach ($get_variants as $g_variant) {
                                    $res1[$g_variant->id] = $g_variant->name;
									if($g_variant->id ==$g_variant->get_variant_id){
										echo $g_variant->name;;
									}
                                } 
                   echo form_dropdown('get_variant_id', $res,$g_variant->get_variant_id, 'class="form-control" data-placeholder="' . lang("select") . ' ' . lang("Variants") . '" id="get_variant_id" style="width:100%;" disabled style="border:none;"');
				   ?>
                   </div>
                </div>
                     <div class="form-group quantity col-md-6">
                <label for="value"><?php echo $this->lang->line("Buy Quantity"); ?></label>
                <div class="controls"> <?php echo $buy->buy_quantity ; ?> </div>
            </div>
            <div class="form-group col-md-6 quantity">
                <label for="value"><?php echo $this->lang->line("Get Quantity"); ?></label>
                <div  class="controls"> <?php echo  $buy->get_quantity ?> </div>
            </div>           
              </div>
             
            </div>
			
          </fieldset>
        
          
          
        </div>
      </div>
    </div>
        </div>        
    </div>
    <?= form_close(); ?>
</div>
<script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>
<!-- <?= $modal_js ?> -->
<script>
$("input").prop('readonly', true);

</script>

