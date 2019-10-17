<div class="col-xs-12 kitchen_section">
    
    <ul>   
        <?php if($this->sma->actionPermissions('dinein_bils')){  ?>    
        <li><a href="<?php echo base_url().'admin/pos/order_biller/?type=1'; ?>" class="<?php if($sales_type == 'Dine In'){ ?>active <?php } ?>"><?=lang('dine_in')?></a></li>
        <?php } ?>
        
        <?php if($this->sma->actionPermissions('takeaway_bils')){  ?>
        <li><a href="<?php echo base_url().'admin/pos/order_biller?type=2'; ?>" class="<?php if($sales_type == 'Take Away'){ ?>active <?php } ?>"><?=lang('take_away')?></a></li>
        <?php } ?>
        
        <?php if($this->sma->actionPermissions('door_delivery_bils')){  ?>
        <li><a href="<?php echo base_url().'admin/pos/order_biller?type=3'; ?>" class="<?php if($sales_type == 'Door Delivery'){ ?>active <?php } ?>"><?=lang('door_delivery')?></a></li>
        <?php } ?>
        
        <?php if($this->Settings->bbq_enable){  ?>
        <li><a href="<?php echo base_url().'admin/pos/biller_bbqtable'; ?>" class="<?php if($sales_type == 'BBQ'){ ?>active <?php } ?>"><?=lang('BBQ')?></a></li>
        <li><a href="<?php echo base_url().'admin/pos/biller_bbqconsolidated'; ?>" class="<?php if($sales_type == 'BBQ WITH DINE IN'){ ?>active <?php } ?>"><?=lang('BBQ With Dine in')?></a></li>
        <?php } ?>
        
     </ul>
  </div>

<div class="tableright col-xs-12">
      
 <div class="col-xs-12"> 
 
        
        <?php
        if(!empty($sales)){
        ?>
        <ul>
            <?php
            foreach($sales as $sales_row){
               
               /*echo "<pre>";
               print_r($sales_row);die;*/
                    $img = 'dine_in.png';
                
                $split_id = $sales_row->id;

            ?>
          <!--   <div style="clear:both; height:5px;"></div>
                          <div class="col-xs-12" style="padding: 0;"> -->
            <li class="col-md-12">
                <div class="row">

                    <div class="billing_list btn-block order-biller-table order_biller_table">
                   
                    <p class="bil_tab_nam"><?php echo $sales_row->areaname.' / '.$sales_row->tablename;  ?></p>
                    <h2><?php echo $sales_row->reference_no; ?></h2>
                   
                        <?php
                        $cancel_sale_status = $this->site->CancelSalescheckData($sales_row->id);
                        if($cancel_sale_status == TRUE){
                            if($this->sma->actionPermissions('bil_cancel')){ 
                        ?>
                        <div class="col-xs-12" style="padding: 0;">
                        <button type="button" class="btn btn3 padding3 cancel_bill btn-danger" style="height:40px;" id="">
                        &#10062;<?=lang('cancel_bill');?> 
                        </button>
                        <input type="hidden"  class="sales_split_id" value="<?php echo $sales_row->sales_split_id; ?>">
                        </div>
                        <?php
                            }
                        
                        }
                        ?>
                    </div>
                    <div class="well col-lg-3">
            <div class="btn-group btn-block">
                <button type="button" class="btn btn-success  btn-block request_bil_new"  data-split="<?=$sales_row->sales_split_id; ?>"  data-item="payment" style="height:40px;" <?php if($this->sma->actionPermissions('bil_payment')){ echo ''; }else{  echo 'disabled'; }  ?>>
                <i class="fa fa-money" ></i><?=lang('payment');?> 
                </button>
            
                <!--******** Rough tender button *********************-->
                    <?php if($this->Settings->rough_tender) {
                    // $RT_disabled = ($rough_tender = $this->site->isRoughTenderDone_saleID($sales_row->id))?'disabled="disabled"':'';
                    $RT_disabled = ($rough_tender = $this->site->isRoughTenderDone($sales_row->id))?'disabled="disabled"':'';
                    if($rough_tender){ ?>
                    <?php foreach($rough_tender as $k => $rt_val) : ?>
                        <?php if($rt_val->paid_by=="cash" || $rt_val->paid_by=="credit") : ?>
                        <input type="hidden" class="rt-<?=$rt_val->paid_by?>" value="<?=$this->sma->formatDecimal($rt_val->pos_paid,2)?>">
                        <?php elseif($rt_val->paid_by=="CC") : ?>
                            <input type="hidden" class="rt-<?=$rt_val->paid_by?>" value="<?=$this->sma->formatDecimal($rt_val->pos_paid,2)?>">
                            <input type="hidden" class="rt-card-no" value="<?=$rt_val->cc_no?>">
                        <?php elseif($rt_val->paid_by=="loyalty") : ?>
                            <input type="hidden" class="rt-<?=$rt_val->paid_by?>" value="<?=$rt_val->loyalty_points?>">
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <?php }
                    ?>
                    <button type="button" class="btn btn-danger rough-tender-payment"  data-split="<?=$sales_row->sales_split_id; ?>"  data-item="rough-tender" id="RT-BNO<?php echo $split_order->bill_number; ?>" style="height:40px;" <?= $RT_disabled?> <?php if($this->sma->actionPermissions('bil_payment')){ echo ''; }else{  echo 'disabled'; }  ?>>
                        <i class="fa fa-money" ></i><?=lang('rough_tender');?> 
                    </button>
                    <?php } ?>
                <!--******** Rough tender button - END *********************-->
                       <?php 
                       $finaltotal = 0; 
                        if(!empty($sales_row->bils)){    
                            foreach($sales_row->bils as $split_order){                                
                                $Billno = $split_order->bill_number;
                                // $finaltotal += $split_order->total-$split_order->total_discount-$split_order->bbq_cover_discount;
                            }
                        }                        
                        ?>

                    <button type="button" class="btn btn-primary btn-block request_bil" data-split="<?=$sales_row->sales_split_id; ?>"  data-bil="req_<?=$sales_row->sales_split_id; ?>" billno="<?=$Billno; ?>" style="height:40px;" id="" <?php if($this->sma->actionPermissions('bil_print')){ echo ''; }else{  echo 'disabled'; }  ?>>
                    <i class="fa fa-print" ></i><?=lang('sale_bill');?> 
                    </button>
                     <?php if(!empty($sales_row->bils)){
                      /*  echo "<pre>";
                        print_r($sales_row->bils);*/
                        $k=1;
                        foreach($sales_row->bils as $split_order){
                           /* echo "<pre>";
                        print_r($split_order);*/

                            if (count($split_order->id) > 0) {

                        /*SUM(B.total-B.total_discount+CASE WHEN (B.tax_type= 1) THEN B.total_tax ELSE 0 

                        END) as grand_total*/
                                if($split_order->tax_type == 1){
                                    $tax = $split_order->total_tax;
                                }
                                else{
                                    $tax = 0;
                                }//$split_order->total-$split_order->total_discount-$split_order->bbq_cover_discount
                                $grand_total[$sales_row->sales_split_id][] = $split_order->total-$split_order->total_discount-$split_order->bbq_cover_discount-$split_order->bbq_daywise_discount+$tax;
                            ?>
                             
                              <h2 class="order-heading" style="margin-top: 0px; width:50%;"> <?=lang('order_ref_no')?>: <?php echo $split_order->bill_sequence_number; ?></h2>                          
                              <input type="hidden"  class="billid_<?=$k?>" value="<?php echo $split_order->id; ?>">
                              <input type="hidden"  class="order_split_<?=$k?>" value="<?php echo $sales_row->sales_split_id; ?>">
                              <input type="hidden"  class="salesid_<?=$k?>" value="<?php echo $split_order->sales_id; ?>">
                              <input type="hidden"  class="credit-limit_<?=$k?>" value="<?php echo $split_order->credit_limit; ?>">
                            <input type="hidden"  class="company-id_<?=$k?>" value="<?php echo $split_order->company_id; ?>">
                            <input type="hidden"  class="customer-type_<?=$k?>" value="<?php echo $split_order->customer_type; ?>">
                            <input type="hidden"  class="customer-allow-loyalty_<?=$k?>" value="<?php echo $split_order->allow_loyalty; ?>">
                            <input type="hidden"  class="customer-id_<?=$k?>" value="<?php echo $split_order->customer_id; ?>">
                            <input type="hidden"  class="customer-name_<?=$k?>" value="<?php echo $split_order->customer_name; ?>">
                            
                            <input type="hidden"  class="loyalty_available" value="<?php  echo $this->site->getCheckLoyaltyAvailable($split_order->customer_id); ?>">
                            
                            <input type="hidden"  class="billid_req_<?=$k?>" value="<?php echo $split_order->id; ?>">
                            <input type="hidden"  class="order_split_req_<?=$k?>" value="<?php echo $sales_row->sales_split_id; ?>">
                            <input type="hidden"  class="salesid_req_<?=$k?>" value="<?php echo $split_order->sales_id; ?>">
                              
                              <!--<p><?=lang('total_items_/_covers')?>: <?php echo $split_order->total_items; ?></p>    
                              <p><?=lang('total')?>: <?php echo $split_order->grand_total; ?></p>  -->                                   
                            <?php 
                        }
                        $k++;
                        }
                     }
                ?>
                    <!--<h3>Total : <?php echo array_sum($grand_total[$sales_row->sales_split_id]); ?></h3>-->
                    <!-- <input type="text"  class="grandtotal" value="<?php echo array_sum($grand_total[$sales_row->sales_split_id]); ?>">
                    <input type="text"  class="grandtotal_req" value="<?php echo array_sum($grand_total[$sales_row->sales_split_id]); ?>"> -->
                     <?php 
                       $finaltotal = 0; 
                        if(!empty($sales_row->bils)){                      
                        
                            foreach($sales_row->bils as $split_order){                                 
                                 if($split_order->tax_type == 1){
                                    $tax = $split_order->total_tax;
                                }
                                else{
                                    $tax = 0;
                                }
                                $Billno = $split_order->bill_number;
                                $finaltotal += $split_order->total-$split_order->total_discount-$split_order->bbq_cover_discount-$split_order->bbq_daywise_discount+$tax;
                            }
                        }
                         
                        ?>

                    <input type="hidden"  class="grandtotal" value="<?php echo $finaltotal; ?>">
                    <input type="hidden"  class="grandtotal_req" value="<?php echo $finaltotal; ?>">                   
                    
                    </div>
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
        <div>
</div>