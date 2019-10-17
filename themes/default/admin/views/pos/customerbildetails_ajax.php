

<div class="tableright col-xs-12">
      
 <div class="col-xs-12"> 
 
        
        <?php
        if(!empty($sales)){
        ?>
        <ul>
            <?php
            foreach($sales as $sales_row){
               
                $split_id = $sales_row->id;

            ?>
          <!--   <div style="clear:both; height:5px;"></div>
                          <div class="col-xs-12" style="padding: 0;"> -->
            <li class="col-md-3">
               

                    <div class="request_bill">
                    <?php if($sales_row->sales_type_id == 1){ ?>
                    <p class="bil_tab_nam"><?php echo $sales_row->areaname.' / '.$sales_row->tablename; } ?></p>
                    <h2><?php echo $sales_row->reference_no; ?></h2>
                   
                       
                   
                    
                     <?php if(!empty($sales_row->bils)){
                       
                        foreach($sales_row->bils as $split_order){
                            if (count($split_order->id) > 0) {
                       
                            ?>
                            <p><?php echo $split_order->customer; ?></p>
                            <p><?php echo $split_order->customer_discount_name; ?></p>
                            <p>
                            <?php
							if($split_order->customer_discount_type == 'percentage_discount'){
								echo $split_order->customer_value.'%';
							}else{
								echo $split_order->customer_value;
							}
							?>
                            </p>
                            <button type="button" style="height:40px;" id="" <?php if($this->sma->actionPermissions('bil_print')){ echo ''; }else{  echo 'disabled'; }  ?>>
                                <?=lang('sale_request_bill');?> 
                                </button>
                                <input type="hidden"  class="bill" value="<?php echo $split_order->id; ?>">
                           
                            
                                                                          
                            <?php 
                        }
                        }
                     }
                ?>
                	 </div>
                </div>
                
            </li>
            <?php
            }
            ?>
        </ul>
        <?php
        }else{
        ?>
        <div class="col-sm-6 col-sm-offset-3 col-xs-12 order_cancel_data alert-danger fade in"> <?=lang('no_record_found')?> </div>
        <?php
        }
        ?>
       
</div> 
        
</div>