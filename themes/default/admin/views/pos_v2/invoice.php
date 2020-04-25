<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?= "Login " . $Settings->site_name;?></title>
    <script type="text/javascript">if(parent.frames.length !== 0){top.location = '<?=admin_url('pos')?>';}</script>
    <base href="<?=base_url()?>"/>
    <meta http-equiv="cache-control" content="max-age=0"/>
    <meta http-equiv="cache-control" content="no-cache"/>
    <meta http-equiv="expires" content="0"/>
    <meta http-equiv="pragma" content="no-cache"/>
    <link rel="stylesheet" href="<?=$assets?>fonts/barlow_condensed/stylesheet.css" type="text/css">
    <link rel="shortcut icon" href="<?=$assets?>images/favicon.ico"/>
   	<link rel="stylesheet" href="<?=$assets?>styles/helpers/bootstrap.min.css" type="text/css">
   	<link rel="stylesheet" href="<?=$assets?>styles/helpers/font-awesome.min.css" type="text/css">
   	<link rel="stylesheet" href="<?=$assets?>styles/jquery.mCustomScrollbar.css" type="text/css">
   	<link rel="stylesheet" href="<?=$assets?>styles/helpers/jquery-ui.css" type="text/css">
   	<link rel="stylesheet" href="<?=$assets?>styles/helpers/icheck/square/_all.css" type="text/css">
	<link rel="stylesheet" href="<?=$assets?>styles/theme.css" type="text/css"/>
   	<link rel="stylesheet" href="<?=$assets?>styles/palered_theme.css" type="text/css">
	<style>
	body,* {
		-webkit-print-color-adjust: exact !important;   /* Chrome, Safari */
		color-adjust: exact !important;                 /*Firefox*/
	}
	.menu_nav li button,.menu_nav li figure{width:10.745%;}
	.pos-variant-name1{color: #e47345; position: relative;}
	.number-keyboard {top: 0px!important;}
	.number-keyboard  div{width: 325px;position: absolute;right: 4%;top: 75%;}
	#choose-discount{ color:black; }
	.order_discount_input {
			background-color: #fff!important;
			width: 70% !important;
			color: #000 !important;
			border-radius: 0;
			font-size: 20px !important;
		}
		.mCS-light-3.mCSB_scrollTools .mCSB_dragger .mCSB_dragger_bar, .mCS-dark-3.mCSB_scrollTools .mCSB_dragger .mCSB_dragger_bar{width: 12px!important;}
		.mCS-light-3.mCSB_scrollTools .mCSB_draggerRail, .mCS-dark-3.mCSB_scrollTools .mCSB_draggerRail{width: 16px!important;}
		.mCSB_inside > .mCSB_container{    margin-right: 10px!important;}
		
		.nc_kot_sec .mCS-light-3.mCSB_scrollTools .mCSB_dragger .mCSB_dragger_bar, .nc_kot_sec .mCS-dark-3.mCSB_scrollTools .mCSB_dragger .mCSB_dragger_bar{width: 6px!important;}
		.nc_kot_sec .mCS-light-3.mCSB_scrollTools .mCSB_draggerRail, .nc_kot_sec .mCS-dark-3.mCSB_scrollTools .mCSB_draggerRail{width: 6px!important;}
		.nc_kot_sec .mCSB_inside > .mCSB_container{    margin-right: 0px!important;}
		.nc_kot_sec .panel{background-color:transparent;border-color: #5f0714;margin-bottom:10px;width: 97%;}
		.nc_kot_sec .panel-primary .panel-heading{background-color: #5f0714;border-color: #5f0714;padding:3px 15px;text-align:left;}
		.nc_kot_sec .panel-primary .form-group{margin-bottom:0px;}
		.nc_kot_sec .panel-primary .panel-body{padding:10px;}
		.nc_kot_sec{height:200px;}
		
	@media print{
		body,* {
			-webkit-print-color-adjust: exact !important;   /* Chrome, Safari */
			color-adjust: exact !important;                 /*Firefox*/
		}
	}
	</style>
	
</head>
<body>
<?php  $currency = $this->site->getAllCurrencies(); ?>
	<section class="pos_bottom_s" style="background-color: transparent;">
		<div class="container">
			<div class="row">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-left header_sec_me">
					<ul class="menu_nav">
							<li >
								<a class="f" <?php echo $order_class;  ?> <?php  if(empty($sales) && $this->sma->actionPermissions('new_order_create')){ ?> href="<?=base_url('pos/pos/order').'/?type='.$order_type.'&table='.$table_id.'&split='.$split_order->split_id.'&same_customer='.$split_order->customer_id.''?>"  <?php   }  ?> <?php echo $order_class;  ?>>
									<figure class="text-center" <?php echo  ($order_class =='disabled')? 'style="background-color: #73493b !important;border-color: #73493b;
    pointer-events: none;
    cursor: not-allowed;"':'';  ?>>
										<div class="img_block">
											<img src="<?=$assets?>images/sprite/new_order_item.png">
											<figcaption <?php echo  ($order_class =='disabled')? 'style="color:black;"':'';  ?>>New order Item</figcaption>
											<figcaption <?php echo  ($order_class =='disabled')? 'style="color:black;"':'';  ?>>កម៉្មងម្ហូបថ្មី</figcaption>
										</div>
									</figure>
								</a>
								
							</li>
						<li>
								<a  <?php  if(empty($sales )&& $order_type ==1  && $this->sma->actionPermissions('new_split_create')){ ?> href="<?=base_url('pos/pos/order').'/?type='.$order_type.'&table='.$table_id.'&spr=1'?>" <?php  }else{  'href="javascript:void(0)"'; }  ?>  >
								<figure class="text-center" <?php echo  ($order_class =='disabled')? 'style="background-color: #73493b !important;border-color: #73493b;
									pointer-events: none;
									cursor: not-allowed;color:black !important;"':'';  ?>>
									<div class="img_block">
										<img src="<?=$assets?>images/sprite/new_split.png">
										<figcaption  <?php echo  ($order_class =='disabled')? 'style="color:black;"':'';  ?>>New Split</figcaption>
										<figcaption <?php echo  ($order_class =='disabled')? 'style="color:black;"':'';  ?>> បំបែកថ្មី</figcaption>
									</div>
								</figure>
							</a>
						</li>
						<li>
							<button <?php   echo $order_type_disabled;    ?> <?php if($pos_settings->table_change != 1) { echo "disabled";  }?>  class="change_table" split="<?php echo $split_order->split_id; ?>" <?php echo $order_class;  ?>>
								<img src="<?=$assets?>images/sprite/change_table.png">
								<figcaption>Change Table</figcaption>
								<figcaption>ប្ដូរតុ</figcaption>
							</button>
						</li>
						 
						  <?php //if($pos_settings->table_change == 1) {?> 
						<li>
							<button  class="change_customer" <?php  if($this->sma->actionPermissions('change_customer')){ echo ""; }else{  echo "disabled";  }  ?> split="<?php echo $split_order->split_id; ?>" customer_id="<?php echo $split_order->customer_id; ?>" <?php echo $order_class;  ?>>
								<img src="<?=$assets?>images/sprite/change_customer.png">
								<figcaption>Change customer</figcaption>
								<figcaption>ប្ដូរភ្ញៀវ</figcaption>
							</button>
						</li>
						 <?php //}  ?>
						
						<li>
							<button <?php   echo $order_type_disabled;    ?> <?php  if($pos_settings->merge_bill != 1){ echo "disabled"; }    ?> class="merge_bill" split="<?php echo $split_order->split_id; ?>" table_id ="<?php echo $table_id; ?>" <?php echo $order_class;  ?>>
								<img src="<?=$assets?>images/sprite/merge_bill.png">
								<figcaption>Merge Bill</figcaption>
								<figcaption>បញ្ចូលទុក</figcaption>
							</button>
						</li>
						 
						<li>
							<button  OnClick="send_kot('<?php echo $split_order->split_id;  ?>');" <?php echo $order_class;  ?> <?php  if($this->sma->actionPermissions('kot_print')){ echo ""; }else{  echo "disabled";  }  ?>>
								<img src="<?=$assets?>images/sprite/print.png">
								<figcaption>KoT print</figcaption>
								<figcaption>ព្រីនមុខម្ហូប</figcaption>
							</button>
						</li>
						<li>
						<?php $count_item = $this->site->splitCountcheck($split_order->split_id);  ?>
							<button id="order_cancel_<?php echo $table_id;  ?>"  OnClick="CancelAllOrderItems('<?php echo $table_id;  ?>','<?php echo $this->GP['pos-cancel_order_remarks'];?>','<?php echo $split_order->split_id;  ?>');" <?php if($this->sma->actionPermissions('cancel_order_items')){ echo ''; }else{  echo 'disabled'; }  ?> <?php echo $order_class;  ?>>
								<img src="<?=$assets?>images/sprite/cancel_all.png">
								<figcaption>Cancel All</figcaption>
								<figcaption>លុបទាំងអស់</figcaption>
							</button>
						</li>
						<li>
							<button <?php   echo $order_type_disabled;    ?> OnClick="bilGenerator(<?php echo $table_id;  ?>, '<?php echo $split_order->split_id;  ?>', '<?php echo $count_item; ?>');" class="" id="main_split_<?php echo $split_order->split_id;  ?>" <?php if($this->sma->actionPermissions('bil_generator')){ echo ''; }else{  echo 'disabled'; }  ?> <?php echo $order_class;  ?>>
							 <input type="hidden" id="<?php echo $split_order->split_id;?>_count_item" value="<?php echo $count_item; ?>">
								<img src="<?=$assets?>images/sprite/bill_generator.png">
								<figcaption>Split generator</figcaption>
								<figcaption>បែងចែក</figcaption>
							</button>
						</li>
						<li>
							<button class="billsave"  <?php echo $order_class;  ?> <?php if($this->sma->actionPermissions('bil_generator')){ echo ''; }else{  echo 'disabled'; }  ?>>
							 <input type="hidden" id="<?php echo $split_order->split_id;?>_count_item" value="<?php echo $count_item; ?>">
								<img src="<?=$assets?>images/sprite/bill_generator.png">
								<figcaption>Bill </figcaption>
								<figcaption>វិក័យប័ត្រ</figcaption>
							</button>
						</li>
						
						<li>
							<button id="print-sale"  <?php echo $order_class;  ?> <?php if($this->sma->actionPermissions('bil_print')){ echo ''; }else{  echo 'disabled'; }  ?>>
								<img src="<?=$assets?>images/sprite/print.png">
								<figcaption>Print Only</figcaption>
								<figcaption>ព្រីន</figcaption>
							</button>
						</li>
						<li>
						<a href="<?php echo base_url('/pos/pos/')  ?>">
							<button class="pull-right">
								<img src="<?=$assets?>images/sprite/back.png">
								<figcaption>Back</figcaption>
								<figcaption>ត្រលប់</figcaption>
							</button>
							</a>
						</li>
					</ul>
				</div>
			</div>
			<div class="row">
			<?php    
			 if(!empty($sales)){ 
		       $sale_table=$this->site->get_sales_details($split_id);   ?>
			   <div class="table_head">
                	<div class="img_s"><img src="<?=$assets?>images/order-table.png" alt=""></div>
                	<span class="odr_name"><?php echo   $sale_table->name ?></span>
					<h4><?php echo $sale_table->split_id .'('.$sale_table->customer.')'; ?></h4>
				</div>
			 <?php   }   ?>
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-left payment_s">
			    <ul>
			   <?php
                  foreach($sales as $sales_row){
                     $split_id = $sales_row->id;
				?>
                <li>
                <div class="row">
                   	<div class="col-sm-12 col-xs-12 pay_li_group">
                    <div class="col-xs-12 billing_list btn-block order-biller-table order_biller_table text-center">
                        <?php
                        $cancel_sale_status = $this->site->CancelSalescheckData($sales_row->id);
                        if($cancel_sale_status == TRUE){
							if($this->sma->actionPermissions('bil_cancel')){ 
                        ?>
                        <?php  }  } ?>
                    </div>
                     <?php if(!empty($sales_row->bils)){
                        /*echo "<pre>";
                        print_r($sales_row->bils);die;*/
						$k=1;
                        foreach($sales_row->bils as $split_order){
                            if (count($split_order->id) > 0) {
                            ?>
                            <div class="col-sm-6 col-xs-12 payment_list_container_li">
							<table class="payment-list-container">
							<tbody>
								<tr>
								  <td>
									<p><?php echo $sales_row->areaname;  ?>/ <?php echo $sales_row->tablename;  ?></p>
									<p><?php echo $sales_row->reference_no;  ?></p>
									</td>
								  <td>
									<?php if($split_order->payment_status == null) { ?>
									<button type="button" class="btn btn-success request_bil_new" data-item="payment" id="BNO<?php echo $split_order->bill_number; ?>"  <?php if($this->sma->actionPermissions('bil_payment')){ echo ''; }else{  echo 'disabled'; }  ?>>

											<figure class="text-center">
													<img src="<?=$assets?>images/sprite/payment_y.png">
													<figcaption>Payment</figcaption>
												</figure>
									</button>
					<?php if($this->Settings->rough_tender) {
					$RT_disabled = ($rough_tender = $this->site->isRoughTenderDone($split_order->id))?'disabled="disabled"':'';
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
						            <input type="hidden"  class="billid" value="<?php echo $split_order->id; ?>">
									<input type="hidden"  class="order_split" value="<?php echo $sales_row->sales_split_id; ?>">
									<input type="hidden"  class="salesid" value="<?php echo $split_order->sales_id; ?>">
									<?php 
									if ($split_order->tax_type == 0){
										$grandtotal = $split_order->total-$split_order->total_discount+$split_order->service_charge_amount;
									}else{
										$grandtotal = $split_order->total-$split_order->total_discount+$split_order->total_tax+$split_order->service_charge_amount;
									} 
									?>                                
									<input type="hidden"  class="grandtotal" value="<?php echo $grandtotal; ?>">
									<input type="hidden"  class="credit-limit" value="<?php echo $split_order->credit_limit; ?>">
									<input type="hidden"  class="company-id" value="<?php echo $split_order->company_id; ?>">
									<input type="hidden"  class="customer-type" value="<?php echo $split_order->customer_type; ?>">
									<input type="hidden"  class="customer-allow-loyalty" value="<?php echo $split_order->allow_loyalty; ?>">
									<input type="hidden"  class="customer-id" value="<?php echo $split_order->customer_id; ?>">
									<input type="hidden"  class="customer-name" value="<?php echo $split_order->customer_name; ?>">
									<input type="hidden"  class="totalitems" value="<?php echo $split_order->total_items; ?>">
									<input type="hidden"  class="loyalty_available" value="<?php  echo $this->site->getCheckLoyaltyAvailable($split_order->customer_id); ?>">
					</td>
					<td>
					<button type="button" class="btn btn-warning rough-tender-payment" data-item="rough-tender" id="RT-BNO<?php echo $split_order->bill_number; ?>" <?= $RT_disabled?> <?php if($this->sma->actionPermissions('bil_payment')){ echo ''; }else{  echo 'disabled'; }  ?>>

					<figure class="text-center">
						<img src="<?=$assets?>images/sprite/invoice.png">
							<figcaption>Rough Tender</figcaption>
							</figure>
						</button>

								<?php } ?>
									<?php }
									  else{
										?>
										<button disabled="" type="button" class="btn btn-success " >
											<?php  echo $split_order->payment_status; ?>
										</button>
										<?php
										}?>
									<input type="hidden"  class="billid" value="<?php echo $split_order->id; ?>">
									<input type="hidden"  class="order_split" value="<?php echo $sales_row->sales_split_id; ?>">
									<input type="hidden"  class="salesid" value="<?php echo $split_order->sales_id; ?>">
									<?php 
									if ($split_order->tax_type == 0){
										$grandtotal = $split_order->total-$split_order->total_discount+$split_order->service_charge_amount;
									}else{
										$grandtotal = $split_order->total-$split_order->total_discount+$split_order->total_tax+$split_order->service_charge_amount;
									} 
									?>                                
									<input type="hidden"  class="grandtotal" value="<?php echo $grandtotal; ?>">
									<input type="hidden"  class="credit-limit" value="<?php echo $split_order->credit_limit; ?>">
									<input type="hidden"  class="company-id" value="<?php echo $split_order->company_id; ?>">
									<input type="hidden"  class="customer-type" value="<?php echo $split_order->customer_type; ?>">
									<input type="hidden"  class="customer-allow-loyalty" value="<?php echo $split_order->allow_loyalty; ?>">
									<input type="hidden"  class="customer-id" value="<?php echo $split_order->customer_id; ?>">
									<input type="hidden"  class="customer-name" value="<?php echo $split_order->customer_name; ?>">
									<input type="hidden"  class="totalitems" value="<?php echo $split_order->total_items; ?>">
									<input type="hidden"  class="loyalty_available" value="<?php  echo $this->site->getCheckLoyaltyAvailable($split_order->customer_id); ?>">
								</div>
								</div>
								</td>
								<td>
									 <button type="button"  class="btn btn-primary btn-block request_bil"  
									 data-bil="req_<?=$k; ?>"  data-billid="<?php echo $split_order->id; ?>" id="bil" <?php if($this->sma->actionPermissions('bil_print')){ echo ''; }else{  echo 'disabled'; }  ?>>
								   <figure class="text-center">
								   <img src="<?=$assets?>images/sprite/print.png">
								   <figcaption>Bill Print</figcaption>
									</button>
									<input type="hidden"  class="billid_req" value="<?php echo $split_order->id; ?>">
									<input type="hidden"  class="order_split_req" value="<?php echo $sales_row->sales_split_id; ?>">
									<input type="hidden"  class="salesid_req" value="<?php echo $split_order->sales_id; ?>">
									<?php 
									if ($split_order->tax_type == 0){
										$grandtotal = $split_order->total-$split_order->total_discount;
									}else{
										$grandtotal = $split_order->total-$split_order->total_discount+$split_order->total_tax;
									}
									?>
									<input type="hidden"  class="grandtotal_req" value="<?php echo $grandtotal; ?>">
									<input type="hidden"  class="totalitems_req" value="<?php echo $split_order->total_items; ?>">
									</td>
                                     <?php if($this->sma->actionPermissions('bil_cancel')){   ?>
										<td>
										   <button type="button" class="btn   cancel_bill btn-danger" id="">
						                 <figure class="text-center">
								     <img src="<?=$assets?>images/sprite/cancel_all.png">
								           <figcaption>Cancel Bill</figcaption>
                                       </button>
                                          <input type="hidden"  class="cancel_bill_id" value="<?php echo $sales_row->id; ?>">
										 <div id="req_<?=$k;?>" style="display: none;"> 
										 <button type="button" data-sp="split_<?=$k;?>" class="btn btn-primary btn-block print_bill" value="<?php echo $split_order->id; ?>" style="height:40px; overflow:hidden; visibility:hidden;"  <?php if($this->sma->actionPermissions('bil_print')){ echo ''; }else{  echo 'disabled'; }  ?>>
									<figure class="text-center">
									  <img src="<?=$assets?>images/sprite/print.png">
									    <figcaption>Bill Print</figcaption>
									</figure>
									</button>
									<input type="hidden" id="split_<?=$k;?>"  class="bill_print" value="<?php echo $split_order->id; ?>">
									</div>
									</td>		
									 <?php    }   ?>
								<?php 
							} ?>
							</tr>
							</tbody>
							</table>
							</div>
                     <?php   $k++;
						}
                     }
					 
                ?>
                	</div>
                </div>
                
            </li>
			<?php  }  ?>	
							
					</ul>
				</div>
			</div>
		</div>
	</section>
	 
	<section>
	
<?php $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'id' => 'pos-autosplitbill-form');
echo form_open("pos/pos/billing?order_type=".$order_type."&bill_type=1&bils=".$bils."&table=".$table_id."&splits=".$split_id, $attrib);?>
<input type="hidden" name="order_type" value="<?php echo $order_type; ?>">
<input type="hidden" name="bill_type" value="<?php echo $bill_type;?>" />
<input type="hidden" name="bils" value="<?php echo $bils;?>" />
<input type="hidden" name="table" value="<?php echo $table_id;?>" />
<input type="hidden" name="splits" value="<?php echo $split_id;?>" />
		<div class="container">
			<div class="row">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding: 0px;">
<!--table-->
  <?php 
         if(!empty($order_item)){ 
		   $order_table=$this->site->get_order_details($split_id);
		  
			?>
			  <div class="table_head">
                <div class="img_s"><img src="<?=$assets?>images/order-table.png" alt=""></div>
                <span class="odr_name"><?php echo   $order_table->name ?></span>
				<h4><?php echo $order_table->split_id .'('.$order_table->customer.')'; ?></h4>
            </div> 
			<?php
			if($order_type == 3){
			?>
        	<div class="col-lg-4 col-lg-offset-4">
            <label><?=lang('delivery_person')?></label>
            <?php
			$delivery_person = $this->site->getDeliveryPersonall($this->session->userdata('warehouse_id'));
			
			?>
        	<select name="delivery_person_id" id="delivery_person_id" class="form-control">
            <?php
			foreach($delivery_person as $delivery_person_row){
			?>
            	<option value="<?php echo $delivery_person_row->id; ?>"><?php echo $delivery_person_row->first_name.' '.$delivery_person_row->last_name.' ['.$delivery_person_row->description.']'; ?></option>
            <?php
			}
			?>
            </select>
            </div>
            <?php
			}
			?>
		<table class="table table_item_ls table_bill_list">
		<colgroup>
			<col width="8%">
			<col width="30%">
			<col width="8%">
			<col width="15%">
			<col width="10%">
			<col width="15%">
			<col width="10%">
			<col width="10%">
		</colgroup>
			<thead>
					<tr>
						<th>Cancel</th>
						<th>Sale Item</th>
						<th>Price</th>
						<th>Qty</th>
						<th>Item Discount</th>
						<th>Customer Discount %</th>
						<th>Discount</th>
						<th>Subtotal</th>
					</tr>
				</thead>
		</table>
		<div class="col-xs-12 table_bill_list_roll" id="bill_generation" style="padding: 0px;">
			<table class="table table_item_ls table_bill_list">
				<colgroup>
					<col width="8%">
					<col width="30%">
					<col width="8%">
					<col width="15%">
					<col width="10%">
					<col width="15%">
					<col width="10%">
					<col width="10%">
				</colgroup>
				<tbody>
        	
            <div class="clearfix"></div>
            <?php                
                $total_count = count($order_item);
                $split_count = $bils;
                for($i=1;$i<=$split_count;$i++){

            ?>
            <div class="col-xs-12">
               
                <tbody class = "autobilldt"  style="cursor: pointer;border:none;">
                <?php
                    $variant_id='';
                    $recipeid_data = array();
					$recipeid_variant_data = array();
                    $recipeid_qty = array();
                    $recipe_variantid = array();
                    $manualitem_discount = array();
                    foreach($order_item as $salesitem) {
                      /*   echo "<pre>";
                        print_r($salesitem);
						die; */
                        if($salesitem->variant!=''){  
                            $variant_id = $salesitem->recipe_variant_id;
                        }
						if($salesitem->unit_price>0){
                        $recipeid_data[] = $salesitem->recipe_id;
						}
						$recipeid_variant_data[] = $salesitem->recipe_id.$variant_id;
                        $recipeid_qty[] = $salesitem->quantity;
                        $manualitem_discount[] = $salesitem->manual_item_discount;
                        $r_total_discount[$i] = array();
                        $r_subtotal[$i] = array();
			            $discount = $this->site->discountMultiple($salesitem->recipe_id);
                        $khmer_name = $this->site->getrecipeKhmer($salesitem->recipe_id);
                        $addondetails = $this->site->getAddonByRecipeidAndOrderitemid($salesitem->recipe_id,$salesitem->id);
                              $itemaddonamt =0;
                              if(!empty($addondetails)) :
                                 foreach ($addondetails as $key => $addons) {                                     
                                    $itemaddonamt +=$addons->price*$addons->qty;
                                }                                 
                        endif;
                        $discount_value = '';
                        $manualitem_discount_amt[$i][] = $salesitem->manual_item_discount;
						if(!empty($discount)){
							if($discount[2] == 'percentage_discount'){
				              $discount_value = $discount[1].'%';
							}else{
								$discount_value =$discount[1];
							}
							 $price_total = $salesitem->subtotal;
							 $dis = $this->site->calculateDiscount($discount_value, $price_total);
							 $subtotal[$i][] = $price_total;
                             $r_subtotal[$i][] = $price_total;
							 $total_tax[$i][] = $salesitem->item_tax;
							 $total_discount[$i][] = $dis;
                             $r_total_discount[$i][] = $dis;
						}else{
							 $dis = 0;
							 $price_total = $salesitem->subtotal;
							 $subtotal[$i][] = $salesitem->subtotal;
							 $total_tax[$i][] = $salesitem->item_tax;
							 $total_discount[$i][] = $dis;
                             $r_total_discount[$i][] = $dis;
                             $r_subtotal[$i][] = $price_total;
						}
                        ?>
                        <tr class = "clickable">
                            <td>
                                <button class="btn btn_remove" type="button" id="cancel-item" data-order-id="<?php echo $salesitem->id ?>" OnClick="CancelOrderItem('<?php echo $salesitem->item_status;  ?>', '<?php echo $salesitem->id;  ?>', '<?php echo $split_id;?>','<?php echo $this->GP['pos-cancel_order_remarks'];?>','<?php echo $salesitem->quantity; ?>');" ><i class="fa fa-remove"></i></button>
                            </td>
                        	<td>
                                <?php
                                $variant ='';$variant_name=''; $variant_id='';
                                    if($salesitem->variant!="" || $salesitem->variant!=0){                                          
                                        $recipe_variantid[] = $salesitem->recipe_variant_id;
                                        $vari = explode('|',$salesitem->variant);
                                        $variant = $salesitem->variant;
                                        $variant_id = $salesitem->recipe_variant_id;
                                        $variant_name='[<span class="pos-variant-name1">'.$variant.'</span>]';
                                    }else{                                        
                                        $recipe_variantid[] = '';
                                    }
							if($this->Settings->user_language == 'khmer'){
									if(!empty($khmer_name)){
										$recipe_name = $khmer_name;
									}else{
										$recipe_name = $salesitem->recipe_name;
									}
								}else{
									$recipe_name = $salesitem->recipe_name;
								}
                            ?>
							<?php echo $recipe_name.$variant_name;
                            $addondetails = $this->site->getAddonByRecipeidAndOrderitemid($salesitem->recipe_id,$salesitem->id);
                             if(!empty($addondetails)) :
                                 foreach ($addondetails as $key => $addons) { ?>
                                    <br> <span style="color: #0e34ef;font-weight: bold;"> <?= $addons->addon_name ?> (<?= $addons->qty ?> X  <?=  $addons->price ?> ) &nbsp;= <?=  $this->sma->formatMoney($addons->price*$addons->qty) ?></span>
                                 <?php }
                             endif;
                             if($pos_settings->item_comment_price_option != 0 && $salesitem->comment !='') :
                                 
                             endif;
                            ?>
                            <input type="hidden" name="split[<?php echo $i;?>][recipe_name][]" value="<?php echo $salesitem->recipe_name;?>">

                            <input type="hidden" name="split[<?php echo $i;?>][recipe_id][]" value="<?php echo $salesitem->recipe_id;?>" class="split-recipe-id recipe_id">
                            <input type="hidden" name="split[<?php echo $i;?>][recipe_code][]" value="<?php echo $salesitem->recipe_code;?>">
                           <input type="hidden" name="split[<?php echo $i;?>][recipe_type][]" value="<?php echo $salesitem->recipe_type;?>">
                        	</td>
                        	<td class="text-right">
							<?php echo $this->sma->formatMoney($salesitem->unit_price);?><br>
                            <?php if($itemaddonamt != 0) : ?>
                            <span style="color: #0e34ef;text-align: right;font-weight: bold;"><?php echo $this->sma->formatMoney($itemaddonamt);?></span>
                            <?php endif; ?>
                            <input type="hidden" name="split[<?php echo $i;?>][unit_price][]" value="<?php echo $salesitem->unit_price;?>" class="unit_price">

                            <input type="hidden" name="split[<?php echo $i;?>][recipe_variant][]" value="<?php echo $variant;?>">
                            <input type="hidden" name="split[<?php echo $i;?>][recipe_variant_id][]" class="variant_id" value="<?php echo $variant_id;?>">
                            </td>
                        	<!-- <td class="text-right">
                            <?php echo $salesitem->quantity;?>
                            <input type="hidden" name="split[<?php echo $i;?>][quantity][]" value="<?php echo $salesitem->quantity;?>" id="recipe-qty-<?=$salesitem->recipe_id?>" class ="quantity" >
                            </td> -->
							
                             <td><div class="qty_number number_se">
							 <?php // for buy x get y complimentry 
							 if($salesitem->unit_price !=0){   ?>
                                <span class="minus ">-</span>
							 <?php  } ?>
                                <span class="text_qty numberfocus" style="height: 28px;width: 37px;text-align: center;font-size: 20px;border: 1px solid #ddd;border-radius: 0px;display: inline-block;vertical-align: middle;background-color: transparent;"><?php echo $salesitem->quantity;?> </span>
                                <input type="hidden" name="split[<?php echo $i;?>][quantity][]" value="<?php echo $salesitem->quantity;?>" id="recipe-qty-<?=$salesitem->recipe_id?>" class ="quantity " >

                                <input type="hidden" name="split[<?php echo $i;?>][original_quantity][]" value="<?php echo $salesitem->quantity;?>" id="original_quantity-<?=$salesitem->recipe_id?>" class ="original_quantity" >

                                <input type="hidden" name="split[<?php echo $i;?>][order_item_id][]" value="<?php echo $salesitem->id;?>" id="order_item_id-<?=$salesitem->id?>" class ="order_item_id" >
								 <?php  if($salesitem->unit_price !=0){   ?>
                                   <span class="plus">+</span></div>
								    <?php  } ?>
                             </td>

                            <?php 
                            $totcolspan=0;
                            $tot = 3;
                            if($pos_settings->manual_and_customer_discount_consolid_percentage_display_option == 1) {
                                $totcolspan=1; 
                                $tot = 1.+$tot;
                            }
                            if($Settings->manual_item_discount == 1) {                                   
                                    $tot = 1.+$tot;
                             }  
                                $display = "block";                                 
                                $colspan =5.+$totcolspan;
                                $colspan1 =6.+$totcolspan;
                             if($Settings->manual_item_discount == 1) { 
                                $colspan =6.+$totcolspan;
                                $colspan1 =7.+$totcolspan;
                                    $display = "block";
                                }else{
                                    $colspan =5.+$totcolspan;
                                    $colspan1 =6.+$totcolspan;
                                    $display = "none";
                                } 
                            ?>
                            <td style="display:<?php echo $display;?>;">
                                <!-- style="width: 40%;float: left;" -->
                                    <input style="border: none;background:transparent;box-shadow: none;outline: none;"
                                     type="text" name="split[<?php echo $i;?>][manual_item_discount_val][]" value="<?php echo $salesitem->manual_item_discount_val;?>" class ="manual_item_discount_val form-control text-right kb-pad1" count="<?php echo $i; ?>" autocomplete="off" >
                                  
                                    <input style="width: 40%;float: right;display: none;" type="hidden" name="split[<?php echo $i;?>][manual_item_discount][]" value="<?php echo $salesitem->manual_item_discount;?>" class ="form-control pos-input-tip manual_item_discount text-right" readonly>

                                    <?php
                                     $discount = $this->site->discountMultiple($salesitem->recipe_id);
                                     $per = 0;
                                     if (strpos($salesitem->manual_item_discount_val, '%') !== false) {
                                            $per =  str_replace("%","",$salesitem->manual_item_discount_val);
                                            // $per =$salesitem->manual_item_discount_val;
                                     }else{
                                        if($salesitem->manual_item_discount_val !=0){
                                            $per = $this->site->amount_to_percentage($salesitem->manual_item_discount_val, $price_total);
                                        }
                                     }
                                    ?>
                                     <input style="width: 40%;float: right;" type="hidden" name="split[<?php echo $i;?>][manual_item_discount_per_val][]" value="<?php echo $this->sma->formatDecimal($per) ?>" class ="form-control pos-input-tip manual_item_discount_per_val text-right" id="manual_item_discount_per_val<?=$salesitem->recipe_id.$variant_id?>" readonly>
                            </td>
                            <td class="text-right">
                            <span class="item_cus_dis_val item_cus_dis" id="item_cus_dis<?=$salesitem->recipe_id.$variant_id?>">0</span>
                            <input type="hidden" name="split[<?php echo $i;?>][item_cus_dis][]" value="" id="item_cus_dis-<?=$salesitem->recipe_id.$variant_id?>" class ="item_cus_dis" >
                            </td>
                            <?php if($pos_settings->manual_and_customer_discount_consolid_percentage_display_option == 1) { ?>
                            <td class="text-right">
                                <span class="manual_and_customer_discount_consolid_percentage_display_option" id="manual_and_customer_discount_consolid_percentage_display_option<?=$salesitem->recipe_id.$variant_id?>"><?php echo $per ?></span>
                            </td>
                            <?php  } ?>
                        	<td class="text-right">
				             <span class="recipe-item-discount-<?=$salesitem->recipe_id?>"> <?php echo $this->sma->formatDecimal($dis); ?> </span>			
                            <input type="hidden" name="split[<?php echo $i;?>][item_discount][]"  value="<?php echo $dis; ?>" id="recipe-item-discount-<?=$salesitem->recipe_id?>" class="item_discount">
                            <input type="hidden" name="split[<?php echo $i;?>][item_discount_id][]"  value="<?php echo $discount[0]; ?>">
                            <input type="hidden" name="split[<?php echo $i;?>][item_discount_val][]" id="recipe-item-discount-val-<?=$salesitem->recipe_id?>" value="<?php echo $discount[1]; ?>">
                            <input type="hidden" name="split[<?php echo $i;?>][item_discount_type][]" id="recipe-item-discount-type-<?=$salesitem->recipe_id?>" value="<?php echo $discount[2]; ?>">
                            <?php 
                            $TotalDiscount = $this->site->TotalDiscount();                            
                            $value =array_sum($r_subtotal[$i]) - array_sum($r_total_discount[$i]);
                            $offer_dis = 0;
                            $sub = 0;
                             if($TotalDiscount[0] != 0)
                                {                                     
                                 if($TotalDiscount[3] == 'percentage_discount'){
                                        $totdiscount = $TotalDiscount[1].'%';

                                    }else{
                                        $totdiscount =$TotalDiscount[1];
                                    }
                                    $totdiscount1 = $this->site->calculateDiscount($totdiscount, $value);
                                    $offer_dis = $totdiscount1;
                                    $sub = $price_total - $dis - $offer_dis;  
                                }        

                            ?>
                            <input type="hidden" name="item_offer_dis[]" value="<?php echo $offer_dis;?>" id="recipe-offer-discount-<?=$salesitem->recipe_id?>">
                            <input type="hidden" name="split[<?php echo $i;?>][item_input_dis][]" value="0" id="recipe-input-discount-<?=$salesitem->recipe_id?>">
                            <input type="hidden" name="split[<?php echo $i;?>][all_discount][]"  value="" id="recipe-total-discount-<?=$salesitem->recipe_id?>">
                            <input type="hidden" name="split[<?php echo $i;?>][item_tax][]" value="<?php echo $salesitem->item_tax;?>">
                        	</td>
                        
                        	<td class = "text-right ">
                                <span class="item_subtotal"><?php echo $this->sma->formatMoney($price_total-$salesitem->manual_item_discount);?></span>
                                  <input type="hidden" name="split[<?php echo $i;?>][discounted_subtotal][]" value="<?php echo $this->sma->formatDecimal($price_total-$salesitem->manual_item_discount) ?>" class="discounted_subtotal">
                                <input type="hidden" name="split[<?php echo $i;?>][subtotal][]" value="<?php echo $price_total;?>" class="item_subtotal1">
                                <input type="hidden" name="split[<?php echo $i;?>][addonsubtotal][]" value="<?php echo $itemaddonamt;?>" class="addonsubtotal">
                                <input type="hidden" name="split[<?php echo $i;?>][addon_id][]" value="<?php echo $salesitem->addon_id;?>" class="addon_id">
                                <input type="hidden" name="split[<?php echo $i;?>][addon_qty][]" value="<?php echo $salesitem->addon_qty;?>" class="addon_qty">
                            </td>
                          </tr>
						  <?php  } 
                               $recipeids =  implode(',',$recipeid_data);
                               $recipeidvariantdataids =  implode(',',$recipeid_variant_data);
                               $recipeqtys =  implode(',',$recipeid_qty);
                               $recipevariantids =  implode(',',$recipe_variantid);
                           ?>
                            </tbody>
				</table>
             		
                  
                  
                   </div>
                   <table class="table table_item_ls table_bill_list table_invoice_l">
             			<tbody>
               		<tr style="background-color: #320505;">
                        <!-- <input type="hidden" name="split[<?php echo $i;?>][manual_discount_amount]" value="<?php echo array_sum($manualitem_discount); ?>" class="total_manual_discount_amount"> -->
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    	<td align="right" class="text-right"><?=lang('total_item')?></td>
                        <td class="right_td text-center" style="width: 50px;">
                            <?php if(isset($discount['unique_discount'])) : ?>
                            <input type="hidden" name="unique_discount" value="1">
                                <?php endif; ?>
							<?php echo $total_count; ?>
                            <input type="hidden" name="split[<?php echo $i;?>][total_item]" value="<?php echo $total_count; ?>">
               			</td>
<!--						<td align="right" style="background-color: #320505;">-->
						
						
						<td  class="text-right"><?=lang('total')?></td>
<!--						<td></td>-->
                        <td class="right_td text-right" style="width: 150px;">
                         <span class="total_price"><?php echo $this->sma->formatMoney(array_sum($subtotal[$i])-array_sum($manualitem_discount_amt[$i]));?></span>
                      	<input type="hidden" name="split[<?php echo $i;?>][total_price]" value="<?php echo array_sum($subtotal[$i]);?>" id="subtotal_<?php echo $i; ?>" class="total_price total_price_textbox">
                        <input type="hidden" name="split[<?php echo $i;?>][all_item_total]" value="<?php echo array_sum($subtotal[$i]);?>" id="all_item_total_<?php echo $i; ?>" class="all_item_total ">
                        </td>
						
							
<!--						</td>-->
                        <input type="hidden" id="manual_discount_amount_<?php  echo $i; ?>" name="split[<?php echo $i;?>][manual_discount_amount]" value="<?php echo array_sum($manualitem_discount_amt[$i]); ?>" class="total_manual_discount_amount">
                  	</tr>
<!-- don't delete bellow tr -->
                  <!--   <tr style="display: ">
                        <td colspan="<?php echo $colspan;?>">
                        <?=lang("item_total_discount", "item_total_discount");?>
                        </td>
                        <td>
                         <span class="total_manual_discount_amount"><?php echo $this->sma->formatMoney(array_sum($manualitem_discount_amt[$i])); ?></span>
                         <input type="hidden" id="manual_discount_amount_<?php  echo $i; ?>" name="split[<?php echo $i;?>][manual_discount_amount]" value="<?php echo array_sum($manualitem_discount_amt[$i]); ?>" class="total_manual_discount_amount">
                        </td>
                        <td class="right_td text-right" style="padding: 5px 10px;font-size: 14px; font-weight:bold;">
                            <span class="after_manual_dis"><?php echo $this->sma->formatMoney(array_sum($subtotal[$i]) - array_sum($manualitem_discount_amt[$i])); ?></span>
                            <input type="hidden" id="after_manual_dis_textbox_<?php  echo $i; ?>"  name="split[<?php echo $i;?>][after_manual_dis_textbox]" value="<?php echo array_sum($subtotal[$i]) - array_sum($manualitem_discount_amt[$i]); ?>" class="after_manual_dis_textbox">
                        </td>
                    </tr> -->
<!-- don't delete above tr -->              
                    <?php 
                    $HideShow = "visible";
                    $display = "contents";
                        if(array_sum($total_discount[$i]) != 0){
                            $HideShow = "visible";
                            $display = "contents";
                        }else{
                            $HideShow = "hidden";
                            $display = "none";
                        }
                    ?>
                    <tr style="visibility: <?php echo $HideShow;?>;display:<?php echo $display;?>;">
                    	<td colspan="8" class="text-right"><?=lang("discount", "order_discount_input");?></td>
                        <td>
                        <span class="itemdiscounts"><?php echo $this->sma->formatMoney(array_sum($total_discount[$i])); ?></span>
                         <input type="hidden" id="item_discounts_<?php  echo $i; ?>"  name="split[<?php echo $i;?>][itemdiscounts]" value="<?php echo array_sum($total_discount[$i]); ?>" class="itemdiscounts">
                        </td>

                        <td class="right_td text-right" style="padding: 5px 10px;">
                            <span class="after_item_or_manual_dis"><?php echo $this->sma->formatMoney(array_sum($subtotal[$i]) - array_sum($total_discount[$i]) - array_sum($manualitem_discount_amt[$i])); ?>
                            </span>
                            <input type="hidden" id="item_dis_<?php  echo $i; ?>"  name="split[<?php echo $i;?>][item_dis]" value="<?php echo array_sum($subtotal[$i]) - array_sum($total_discount[$i]); ?>" class="after_item_or_manual_dis_textbox"> 
                        </td>
                    </tr>
                    
                    <?php 
                    $val = 0;
                    $date =date('Y-m-d');
                    $TotalDiscount = $this->site->TotalDiscount();
                    $value =array_sum($subtotal[$i]) - array_sum($total_discount[$i]);
                    if($TotalDiscount[0] != 0){    
                         if($TotalDiscount[3] == 'percentage_discount'){
                                $totdiscount = $TotalDiscount[1].'%';
                            }else{
                                $totdiscount =$TotalDiscount[1];
                            }
                            $totdiscount1 = $this->site->calculateDiscount($totdiscount, $value);
                        $sub_total =array_sum($subtotal[$i]) - array_sum($total_discount[$i]); 
                         if((!isset($discount['unique_discount']) || isset($discount['only_offer_dis'])) && $TotalDiscount[2]  <= $sub_total){  
                            $val =$value - $totdiscount1;

                             echo '<tr>                             
                                <td colspan="'.$colspan.'" class="text-right">'.lang('offer_discount').'
                                </td>
                                <td>
                                '.$totdiscount.'

                                <input type="hidden" name="split['.$i.'][tot_dis_id]" value="'.$TotalDiscount[0].'">
                                <input type="hidden" name="split['.$i.'][tot_dis_value]" value="'.$totdiscount.'" class="tot_dis_value">
                                </td>
                                <td class="right_td text-right" style="padding: 5px 10px;">

                                <input type="hidden" id="offer_discount_'.$i.'" name="split['.$i.'][tot_dis1]" value="'.$val.'">

                                <input id="off_discount_'.$i.'"  type="hidden" name="split['.$i.'][offer_dis]" value="'.$totdiscount1.'">

                                  <span id="tds1_'.$i.'">'.$this->sma->formatMoney($totdiscount1).'</span>
                                </td>
                                </tr>';
                        }
                    }
                    if($val)
                    {
                        $final_val = $val;
                    }
                    else
                    {
                        $final_val = $value;
                    }
                    ?>
                    <?php if(!isset($discount['unique_discount'])) : ?>
                        <tr>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
                            <td class="text-right" colspan="2">
                            <?=lang("customer_discount", "customer_discount");?>
                            </td>
                            <td style="width: 200px;">                            
                            <div class="" >
                                <input type="hidden" name="split[<?php echo $i;?>][recipeids]" id="recipeids_<?php echo $i; ?>" value="<?php echo $recipeids; ?>" >
                                <input type="hidden" id="recipeidvariantdataids_<?php echo $i; ?>" value="<?php echo $recipeidvariantdataids; ?>" >
                               <input type="hidden" name="split[<?php echo $i;?>][recipeqtys]" id="recipeqtys_<?php echo $i; ?>" value="<?php echo $recipeqtys; ?>" > 
                               <input type="hidden" name="split[<?php echo $i;?>][$recipevariantids]" id="recipevariantids_<?php echo $i; ?>" value="<?php echo $recipevariantids; ?>" >
                                <?php if($Settings->customer_discount=='customer') : ?>
                                 <input type="hidden" name="dine_in_discount" value="<?= $discount_select['dine'] ?>">
                                <select style="display: "  name="split[<?php echo $i;?>][order_discount_input]" class="form-control pos-input-tip order_discount_input" id="order_discount_input_<?php echo $i; ?>" count="<?php echo $i; ?>">
                                 <option value="0">No</option> 
                                    <?php
                                    foreach ($customer_discount as $cusdis) {
                                    ?>
                                    <option value="<?php echo $cusdis->id; ?>" <?php if($discount_select['dine'] == $cusdis->id){ echo 'selected'; }else{ echo ''; } ?> data-id="<?php echo $cusdis->id; ?>"><?php echo $cusdis->name; ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                                <input type="hidden" id="order_discount_input_seletedtext" name="order_discount_input_seletedtext">
                                <?php elseif($Settings->customer_discount=='manual') : ?>
								<?php $member_dis= ($member_discount->discounttype =='percentage')? $member_discount->discount.'%':$member_discount->discount; ?>
								
								
                                <input style="background-color:#fff;width:50%;color:black;" type="text" name="split[<?php echo $i;?>][order_discount_input]" autocomplete="off" class="form-control kb-pad1 pos-input-tip order_discount_input manual-discount" id="order_discount_input_<?php echo $i; ?>" value="<?php echo !empty($member_dis)?$member_dis:'';   ?>" count="<?php echo $i; ?>"  <?php echo ($this->sma->actionPermissions('member_discount'))?"":"readonly";  ?>>
								
								<?php  if(!$this->sma->actionPermissions('member_discount')){   ?>
								<input style="background-color:#fff;width:50%;color:black;" type="hidden" name="split[<?php echo $i;?>][order_discount_input]" autocomplete="off" class="form-control kb-pad1 pos-input-tip order_discount_input manual-discount" id="order_discount_input_<?php echo $i; ?>" value="<?php echo !empty($member_dis)?$member_dis:'';   ?>" count="<?php echo $i; ?>"  <?php ($this->sma->actionPermissions('member_discount'))?"":"readonly";  ?>>
								<?php   }   ?>
								
								
								<input type="hidden" name="member_dicount_card_number" value="<?php echo $member_discount->card_no ;  ?>">
								<input type="hidden" name="member_discount" value="<?php echo $member_discount->discount ;  ?>">
								<input type="hidden" name="member_discount_type" value="<?php echo $member_discount->discounttype ;  ?>">
                                <?php endif; ?>
                                </div>
                            </td>
                            <td class="right_td text-right" style="padding: 5px 10px;">
                         
                            <input type="hidden" id="tdis_<?php  echo $i; ?>" name="split[<?php echo $i;?>][discount_amount]" value="0">
                             <span id="tds_<?php echo $i; ?>"><?php echo $this->sma->formatMoney(0); ?></span>
                             <!--<input type="hidden" id="max-allow-discount-percent_<?php  echo $i; ?>"  value="<?=($current_user->max_discount_percent!=0)?$current_user->max_discount_percent:'';?>">-->
                            </td>
                        </tr>
                    <?php endif; ?>

                  <?php 
                   if(!empty($order_data))
                    {
                       $custimerid = $order_data['customer_id'];                     
                       $check = $this->site->Check_birthday_discount_isavail($order_data['customer_id']);                       
                   if($this->pos_settings->birthday_enable != 0  && $this->pos_settings->birthday_discount != 0){
                    if($check == true){
                     ?>
                        <tr>
                            <td class="text-right" colspan="<?php echo $colspan;?>"> <?=lang("birthday_discount");?> </td>
                            <?php
                             $birday = $this->pos_settings->birthday_discount;                             
                                $birthday_val = $this->site->calculateDiscount($birday, $final_val);
                                $final_val   = $final_val - $birthday_val;
                            ?>
                             <input type="hidden" name="split[<?php echo $i;?>][birthday_discount]" autocomplete="off" class="form-control kb-pad pos-input-tip  birthday-discount birthday_discount_<?php echo $i; ?>" count="<?php echo $i; ?>" value="<?php echo $birthday_val;?>">

                             <input type="hidden" name="split[<?php echo $i;?>][after_birthday_discount]" autocomplete="off" class="form-control kb-pad pos-input-tip  birthday-discount" id="after_birthday_discount_<?php echo $i; ?>" count="<?php echo $i; ?>" value="<?php echo $final_val;?>">
                            <td  class="right_td text-right" style="padding: 5px 10px;font-size: 14px; font-weight:bold;"> <span class="birthday_discount_<?php echo $i; ?>"> <?php echo $this->sma->formatMoney($birthday_val); ?>  </span> </td>
                    </tr>
                <?php } } } ?>
                    <tr>
                       
                        <td class="text-right" colspan="8"> <?=lang("sub_total");?> </td>
                        <?php                             $final_val   = $final_val - $birthday_val;      ?>
                         <input type="hidden" name="split[<?php echo $i;?>][subtot]" autocomplete="off" class="form-control kb-pad pos-input-tip  subtot subtot_<?php echo $i; ?>" count="<?php echo $i; ?>" value="<?php echo $final_val;?>">
                        <td  class="right_td text-right" style="padding: 5px 10px;"> <span class="subtot_<?php echo $i; ?>"> <?php echo $this->sma->formatMoney($final_val); ?>  </span> </td>
                    </tr>
                    <?php 
                    
                    
                    $serice_charge_amt =0;                   
                    if($this->pos_settings->default_service_charge !=0 && $this->pos_settings->service_charge_option !=0){
                        $ServiceHideShow = "visible";
                        $Servicedisplay = "contents";
                     }else{
                        $ServiceHideShow = "hidden";
                        $Servicedisplay = "none";
                    }
                    ?>
                    <tr style="visibility: <?php echo $ServiceHideShow;?>;display:<?php echo $Servicedisplay; ?> ">
                    <td colspan="<?php echo $colspan1;?>" class="text-right">
                        <?php 
                            $AllServiceCharge = $this->site->getAllSericeCharges();
                            $ServiceCharge = $this->site->getServiceChargeByID($this->pos_settings->default_service_charge);
                        ?>                    
                        <select style="display: none"  name="split[<?php echo $i;?>][service_charge]" class="form-control pos-input-tip service_charge" id="service_charge_<?php echo $i; ?>" count="<?php echo $i; ?>">
                        <?php
                            foreach ($AllServiceCharge as $Service) {  ?>
                            <option value="<?php echo $Service->id; ?>" <?php if($ServiceCharge->id == $Service->id){ echo 'selected'; }else{ echo ''; } ?> data-id="<?php echo $Service->rate; ?>"><?php echo $Service->name; ?></option>
                        <?php }   ?>
                        </select>                    
                        
                    <?php

                    if($this->pos_settings->default_service_charge !=0 && $this->pos_settings->service_charge_option !=0){
                        $serice_charge_amt = ($final_val) * ($ServiceCharge->rate / 100);   
                    }

                        $Service_Charge_Text = $ServiceCharge->name;                      
                    ?>
                     <span style="text-align: right;" id="servicecha_<?php echo $i; ?>"> <?php echo $Service_Charge_Text; ?></span>
                    </td>

                    <td class="right_td text-right" style="padding: 5px 10px;">
                        <input type="hidden" name="split[<?php echo $i;?>][service_amount]" id="service_amount_<?php echo $i; ?>" value="<?php echo $serice_charge_amt; ?>">                        
                        <span id="spansericechargeamt_<?php echo $i; ?>"><?php echo $this->sma->formatMoney($serice_charge_amt); ?></span>
                    </td>                    
                    </tr>
                    <tr></tr>
                    

                    <?php 
                    $getTaxType = $this->pos_settings->tax_type;
                    //$this->pos_settings->default_tax == no tax
                    //$getTaxType == inclusive tax, so that tax hide

                    $HideShow = "visible";
                    $display = "contents";
                    if($this->pos_settings->default_tax != 1 && $getTaxType != 0)
                        {       
                        $HideShow = "visible";
                        $display = "contents";
                        }
                        else{
                            $HideShow = "hidden";
                            $display = "none";
                       }
                    ?>
                    <tr style="visibility: <?php echo $HideShow;?>;display:<?php echo $display; ?> ">
                    <td colspan="8" class="text-right">
                        <?php 
                            $getTax = $this->site->getTaxRateByID($this->pos_settings->default_tax);
                        ?>
                    <!-- <div class="col-lg-6 pull-right"> -->
                        <input type="hidden" name="split[<?php echo $i;?>][tax_type]" id="tax_type_<?php echo $i; ?>" value="<?php echo $getTaxType; ?>">

                        <select style="display: none"  name="split[<?php echo $i;?>][ptax]" class="form-control pos-input-tip ptax" id="ptax_<?php echo $i; ?>" count="<?php echo $i; ?>">
                        	<?php
							foreach ($tax_rates as $tax) {
								
							?>
                        	<option value="<?php echo $tax->id; ?>" <?php if($getTax->id == $tax->id){ echo 'selected'; }else{ echo ''; } ?> data-id="<?php echo $tax->rate; ?>"><?php echo $tax->name; ?></option>
                            <?php
							}
							?>
                        </select>
                        <!-- </div> -->
                        <?=lang("tax");?>
                    <!-- </td> -->
                    <?php
                        $default_tax = ($final_val) * ($getTax->rate / 100);                        
                        $taxtype ='';
                        $style = 'block';
                        if($getTaxType != 0){      
                            $colspan = 1;
                            $style = 'block';
                            $taxtype = lang("exclusive");
                        }
                        else{
                            $colspan = 1;
                            $style = 'none';
                            $taxtype = lang("inclusive");
                        }
                        if($getTaxType != 0){
                           $final_val = ($final_val + $default_tax);
                           $final_val = $final_val+$serice_charge_amt;
                           $sub_val = $final_val;
                        }
                        else{
                            $sub_val = $final_val/(($default_tax/$final_val)+1);
                            $sub_val =  $sub_val;
                            $default_tax = ($sub_val) * ($getTax->rate / 100);
                            $final_val = $sub_val+$default_tax+$serice_charge_amt; 
                            $sub_val =  $final_val;
                        } 
                        ?>

                    <!-- <td colspan="<?php echo $colspan; ?>" align="right"> -->   
                     <span style="text-align: right;" id="ttax2_old_<?php echo $i; ?>"> <?php echo '('.$taxtype.' - '.$getTax->name.')' ?></span>
                    </td>

                    <td class="right_td text-right" >
                        <input type="hidden" name="split[<?php echo $i;?>][tax_amount]" id="tax_amount_<?php echo $i; ?>" value="<?php echo $default_tax; ?>">                        
                        <span  style="float:right;"id="ttax2_<?php echo $i; ?>"><?php echo $this->sma->formatMoney($default_tax); ?></span>
                    </td>
                    </tr>

                   <tr>
                   		<td colspan="8" class="text-right"><?=lang('grand_total')?></td>
                   		<td class="right_td text-right" style="padding: 5px 10px;">
                   		<span style="float:right;" id="gtotal_<?php echo $i; ?>">
				  		<?php
                        echo $this->sma->formatMoney($final_val);
                         /*echo $this->site->FinalamountRound(($final_val) + $default_tax);*/ ?>
                   		</span>
                   		<input type="hidden" name="split[<?php echo $i;?>][grand_total]" value="<?php echo (($sub_val)); ?>" id="grand_total_<?php echo $i;?>">
                        <input type="hidden" name="split[<?php echo $i;?>][round_total]" value="<?php echo (($sub_val)); ?>" id="round_total_<?php echo $i;?>">
                   <?php 
                    if(!empty($order_data))
                        {?>
                            <input type="hidden" name="split[<?php echo $i;?>][reference_no]" value="<?php echo $order_data['reference_no']; ?>">
                            <input type="hidden" name="split[<?php echo $i;?>][customer_id]" value="<?php echo $order_data['customer_id']; ?>">
                            <input type="hidden" name="split[<?php echo $i;?>][customer]" value="<?php echo $order_data['customer']; ?>">
                            <input type="hidden" name="split[<?php echo $i;?>][biller_id]" value="<?php echo $order_data['biller_id']; ?>">
                            <input type="hidden" name="split[<?php echo $i;?>][biller]" value="<?php echo $order_data['biller']; ?>">  
                        <?php 
                        } ?>
                        </td>
                        </tr>
                        </tbody>
             		</table>
				   <?php
             	 } 
          		}
               elseif(empty($sales)){
                        redirect("pos/pos");  }
   				?>     
    <?php
echo form_hidden('remove_image','No');
echo form_hidden('action', 'SINGLEBILL-SUBMIT');
echo form_close();
?>
</div>

				</tbody>
			</table>
		</div>
			
				</div>
			</div>
    	</div>
		
		
		
<div style="clear:both;"></div>
</div>
<div style="clear:both;"></div>
</div>
</div>
</div>
	</section>
	<div class="modal fade in" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true" style="z-index:9999999999" data-backdrop="static" data-keyboard="false" >
</div>

			<div class="modal fade in" id="customer-change-Modal" tabindex="-1" role="dialog" aria-labelledby="customer-change-ModalLabel" aria-hidden="true" style="background: rgba(0,0,0,0.5);">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<div class="btn-group pull-right">
										<button type="button" id="OrderChangeCustomer" class="btn btn-primary">Submit</button>
										<button type="button" class="btn btn-danger closemodal" data-dismiss="modal" aria-hidden="true">Cancel</button>
									</div>
									<h4 class="modal-title" id="customer-change-ModalLabel"><?=lang('change_customer')?></h4>
								</div>
								<div class="modal-body">
									<div style="position: absolute; <?=$Settings->user_rtl ? 'right:-9999px;' : 'left:-9999px;';?>"><?php echo form_input('test', '', 'id="test" class="kb-pad"'); ?></div>

								<input type="hidden" name="change_split_id" id="change_split_id">
								
									<label><?=lang('customers')?></label>
								 <?php
									echo form_input('customer', (isset($_POST['customer']) ? $_POST['customer'] : ""), 'id="poscustomer" data-placeholder="' . $this->lang->line("select") . ' ' . $this->lang->line("customer") . '" required="required" class="form-control  kb-text  pos-input-tip" style="width:100%;"');
								?>
								   <!--  <select style="display: "  name="changed_table_id" class="form-control pos-input-tip changed_customer_id" id="changed_customer_id">
									<option value="0">No</option>
										<?php
										//foreach ($avil_customers as $customer) {

										?>
										<option value="<?php echo $customer->id; ?>" data-id="<?php echo $customer->id; ?>"><?php echo $customer->name; ?></option>
										<?php
									//	}
										?>
									</select> -->
								
								</div>
								
							</div>
						</div>
					</div>
				</div></div>
					<div class="modal fade in" id="table-change-Modal" tabindex="-1" role="dialog" aria-labelledby="table-change-ModalLabel" aria-hidden="true" style="background: rgba(0,0,0,0.5);">
						<div class="modal-dialog modal-sm">
							<div class="modal-content">
								<div class="modal-header">
									<div class="btn-group pull-right">
										<button type="button" id="OrderChangeTable" class="btn btn-primary">Submit</button>
										<button type="button" class="btn btn-danger closemodal" data-dismiss="modal" aria-hidden="true">Cancel</button>
									</div>
									
									<h4 class="modal-title" id="table-change-ModalLabel"><?=lang('table_change')?></h4>
								</div>
								<div class="modal-body">
								<input type="hidden" name="change_split_id" id="change_split_id">
								<label><?=lang('tables')?></label>
									<select style="display: "  name="changed_table_id" class="form-control pos-input-tip changed_table_id" id="changed_table_id">
									<option value="0">No</option>
										<?php
										foreach ($avil_tables as $tables) {
										?>
										<option value="<?php echo $tables->id; ?>" data-id="<?php echo $tables->id; ?>"><?php echo $tables->name; ?></option>
										<?php
										}
										?>
									</select>
								</div>
								<div class="modal-footer">
									
								</div>
							</div>
						</div>
					</div>
							<div class="modal fade in" id="splits-merge-Modal" tabindex="-1" role="dialog" aria-labelledby="splits-merge-ModalLabel" aria-hidden="true" style="background: rgba(0,0,0,0.5);">
						<div class="modal-dialog modal-sm">
							<div class="modal-content">
								<div class="modal-header">
									<div class="btn-group pull-right">
										<button type="button" id="Mergesplits" class="btn btn-primary">Submit</button>
										<button type="button" class="btn btn-danger mergeclose" data-dismiss="modal" aria-hidden="true">Cancel</button>
									</div>
									<h4 class="modal-title" id="splits-merge-ModalLabel"><?=lang('order_merge')?></h4>
								</div>
								<div class="modal-body">
								<input type="hidden" name="merge_split_id" id="merge_split_id">
								<input type="hidden" name="merge_table_id" id="merge_table_id">
								</div>
								 <div class="discount-container">
										<div class="merge-group-list">
										</div>
								</div>
								<div class="modal-footer"></div>
							</div>
						</div>
					</div>
								<div class="modal fade in" id="splits-merge-Modal" tabindex="-1" role="dialog" aria-labelledby="splits-merge-ModalLabel" aria-hidden="true" style="background: rgba(0,0,0,0.5);">
						<div class="modal-dialog modal-sm">
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close closemodal" data-dismiss="modal" aria-hidden="true"><i
											class="fa fa-2x">&times;</i></button>
									<h4 class="modal-title" id="splits-merge-ModalLabel"><?=lang('order_merge')?></h4>
								</div>
								<div class="modal-body">
								<input type="hidden" name="merge_split_id" id="merge_split_id">
								<input type="hidden" name="merge_table_id" id="merge_table_id">
								</div>
								 <div class="discount-container">
										<div class="merge-group-list">
										</div>
									</div>
								<div class="modal-footer">
									<button type="button" id="Mergesplits" class="btn btn-primary">Submit</button>
								</div>
							</div>
						</div>
					</div>
					
<div class="modal" id="CancelAllorderModal" tabindex="-1" role="dialog" aria-labelledby="CancelAllorderModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
				  <div class="btn-group pull-right">
					<button type="button" class="btn btn-primary" id="cancel_allorderitem"><?=lang('send')?></button>
					<button type="button" class="btn btn-danger cancelclosemodal" data-dismiss="modal">Cancel
					</button>
				  </div>
					<h4 class="modal-title" id="cmModalLabel"></h4>
            </div>
            <div class="modal-body" id="pr_popover_content">
                <div class="form-group">
                    <?= lang('remarks'); ?>
                    <?= form_textarea('remarks', '', 'class="form-control kb-text-click" id="cancel-remarks" style="height:80px;"'); ?>
                </div>
                <input type="hidden" id="order_table_id" value=""/>
                <input type="hidden" id="split_table_id" value=""/>
            </div>
            <div class="modal-footer">
                
            </div>
        </div>
    </div>
</div>

<div class="modal fade in" id="bilModal" tabindex="-1" role="dialog" aria-labelledby="bilModalLabel" aria-hidden="true" style="background: rgba(0,0,0,0.5);">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
              <div class="btn-group pull-right">
              	<button type="button" id="updateBil" class="btn btn-primary">Submit</button>
                <button type="button" class="btn btn-danger closebil" data-dismiss="modal" aria-hidden="true">Cancel</button>
              </div>
                <h4 class="modal-title" id="bilModalLabel">BILL TYPES</h4>
            </div>
            <div class="modal-body">
              
              <div class="form-group">
                  <!--  <div style="margin-bottom: 10px;"><input type="radio" class="" name="bil_type" value="1" checked> <?=lang('single_bill')?></div>-->
                    <div class="count_div" style="margin-bottom: 10px;"><input type="radio" name="bil_type" value="2"> <?=lang('auto_split_bill')?></div>
                     <div class="count_div" style="margin-bottom: 5px;"><input type="radio" name="bil_type" value="3" > Manual Split Bill</div> 
                    <input class="form-control kb-pad " type="text" name="bils_number_auto" id="bils_number_auto" maxlength="2" placeholder="<?=lang('auto_split')?>" style="display:none;">
                    <input type="text" class="form-control  kb-pad" name="bils_number_manual" id="bils_number_manual" placeholder="Manual Split" style="display:none;">
                </div>
				<input type="hidden" name="bil_split_type" id="bil_split_type">
                <input type="hidden" name="bil_table_type" id="bil_table_type">
            </div>
            <div class="modal-footer">
                
            </div>
        </div>
    </div>
</div>
<div class="modal" id="CancelorderModal" tabindex="-1" role="dialog" aria-labelledby="CancelorderModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close closemodal" data-dismiss="modal"><span aria-hidden="true">
                    <i class="fa fa-2x">&times;</i></span>
                    <span class="sr-only"></span>
                </button>
                <h4 class="modal-title" id="cmModalLabel"></h4>
            </div>
            <div class="modal-body" id="pr_popover_content">
		<div class="row col-sm-12">
		    <div class="form-group">
			<label><input type="radio" name="cancel_type" class="radio cancel-type" checked value="out_of_stock"><?=lang('out_of_stock')?></label>
			<label><input type="radio" name="cancel_type" class="radio cancel-type" value="spoiled"><?=lang('spoiled')?></label>
			<label><input type="radio" name="cancel_type" class="radio cancel-type" value="reusable"><?=lang('reusable')?></label>
		    </div>
		</div>
		
                <div class="form-group">
                    <?= lang('remarks'); ?>
                    <?= form_textarea('remarks', '', 'class="form-control kb-text" id="remarks" style="height:80px;"'); ?>
                </div>
                <input type="hidden" id="order_item_id" value=""/>
                <input type="hidden" id="split_order" value=""/>
		        <input type="hidden" id="cancel_qty" value=""/>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="cancel_orderitem"><?=lang('send')?></button>
            </div>
        </div>
    </div>
</div>
		<div class="modal" id="salesCancelorderModal" tabindex="-1" role="dialog" 
aria-labelledby="CancelorderModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close closemodal" data-dismiss="modal"><span aria-hidden="true">
                    <i class="fa fa-2x">&times;</i></span>
                    <span class="sr-only"></span>
                </button>
                <h4 class="modal-title" id="cmModalLabel"></h4>
            </div>
            <div class="modal-body" id="pr_popover_content">
                <div class="form-group">
                    <?= lang('remarks'); ?>
                    <?= form_textarea('remarks', '', 'class="form-control" id="salecancelremarks" style="height:80px;"'); ?>
                </div>
                <input type="hidden" id="sale_id" value=""/>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="salescancel_orderitem"><?=lang('send')?></button>
            </div>
        </div>
    </div>
	</div>		
<div class="modal fade in" id="paymentModal" tabindex="-1" data-backdrop="static"   data-keyboard="false" role="dialog" aria-labelledby="payModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true"><i
                            class="fa fa-2x">&times;</i></span><span class="sr-only"><?=lang('close');?></span></button>
		<h4 class="modal-title" id="payment-customer-name"></h4>
		<h4 class="modal-title" id="payModalLabel"><?=lang('make_payment');?>(<span id="new_customer_name"></span>)</h4>
            <div style="position: absolute; <?=$Settings->user_rtl ? 'right:-9999px;' : 'left:-9999px;';?>"><?php echo form_input('test', '', 'id="test" class="kb-pad"'); ?></div>

            </div>
            <div class="modal-body" id="payment_content" >
                <div class="btn btn-warning pull-right">
                    <a href="<?=admin_url('customers/new_customer');?>" id="add-customer" class="external" data-toggle="modal" data-target="#myModal">
                        Add New Customer <i class="fa fa-plus-circle" id="addIcon" style="font-size: 1.5em; "></i>
                    </a>
                </div>
                
             <?php $attrib = array( 'autocomplete'=>"off" ,'role' => 'form', 'id' => 'pos-payment-form');
             echo form_open("pos/pos/paymant_all", $attrib);
             $type = $this->input->get('type');
             ?>
                <div class="row">
                    <?php // if ($pos_settings->taxation_report_settings == 1) { ?>
                       <div class="form-group" style="padding: 5px 10px;">
                            <div class="row">
                                <div class="col-sm-6 taxation_settings">
                                    <label class="control-label" for="taxation_settings"><?= lang("print_option"); ?></label>
                                    <input type="radio" value="0" class="checkbox" name="taxation" checked ="checked">
                                    <label for="switch_left">Print</label>
                                    <input type="radio" value="1" class="checkbox" name="taxation">
                                    <label for="switch_right">Don't Print</label>                    
                                </div>
                            </div>
                        </div>
                    <?php  //} ?>
                <input type="hidden" name="type" class="type" value="<?php echo $type;?>"/>
                <input type="hidden" name="balance_amount" class="balance_amount" value=""/>
                <input type="hidden" name="due_amount" class="due_amount" value=""/>
                    <div class="col-md-12 col-sm-12 text-center">
                        <?php if ($Owner || $Admin || !$this->session->userdata('biller_id')) { ?>
                            <div class="form-group"  style="margin-bottom: 5px;">
                                <?=lang("biller", "biller");?>
                                <?php
                                    foreach ($billers as $biller) {
                                        $btest = ($biller->company && $biller->company != '-' ? $biller->company : $biller->name);
                                        $bl[$biller->id] = $btest;
                                        $posbillers[] = array('logo' => $biller->logo, 'company' => $btest);
                                        if ($biller->id == $pos_settings->default_biller) {
                                            $posbiller = array('logo' => $biller->logo, 'company' => $btest);
                                        }
                                    }
                                    echo form_dropdown('biller', $bl, (isset($_POST['biller']) ? $_POST['biller'] : $pos_settings->default_biller), 'class="form-control" id="posbiller" required="required"');
                                ?>
                            </div>
                        <?php } else {
                                $biller_input = array(
                                    'type' => 'hidden',
                                    'name' => 'biller',
                                    'id' => 'posbiller',
                                    'value' => $this->session->userdata('biller_id'),
                                );

                                echo form_input($biller_input);

                                foreach ($billers as $biller) {
                                    $btest = ($biller->company && $biller->company != '-' ? $biller->company : $biller->name);
                                    $posbillers[] = array('logo' => $biller->logo, 'company' => $btest);
                                    if ($biller->id == $this->session->userdata('biller_id')) {
                                        $posbiller = array('logo' => $biller->logo, 'company' => $btest);
                                    }
                                }
                            }
                        ?>
                        <input type="hidden" name="new_customer_id" id="new_customer_id" value="0">
                        <input type="hidden" name="eligibity_point" id="eligibity_point" value="<?= $eligibity_point ?>">
                        <input type="hidden" name="bill_id" id="bill_id" class="bill_id" />
                        <input type="hidden" name="order_split_id" id="order_split_id" class="order_split_id" />
                        <input type="hidden" name="sales_id" id="sales_id" class="sales_id" />
            			<input type="hidden" name="credit_limit" id="credit_limit" class="credit_limit" />
            			<input type="hidden" name="company_id" id="company_id" class="company_id" />
            			<input type="hidden" name="customer_type" id="customer_type" class="customer_type" />
                        <input type="hidden" name="customer_id" id="customer_id" class="customer_id"/>
            			
                        <input type="hidden" name="total" id="total" class="total" />
                        <input type="hidden" name="loyalty_available" id="loyaltyavailable" class="loyaltyavailable" />
                        <input type="hidden" name="loyaltypoints" id="loyaltypoints" class="loyaltypoints" />
                        <input type="hidden" name="loyalty_used_points" id="loyalty_used_points" class="loyalty_used_points" />
                        <div class="form-group bill_sec_head" style="color: #1F73BB!important;font-size: 20px!important;align-self: center;margin-bottom: 5px;">
                            <button type="button" class="btn btn-danger" id="reset_payment" style="cursor: pointer!important;"><label style="margin-top: 0px !important;"><?=lang('reset')?> </label></button>
                               <?=lang("bill_amount", "bill_amount");?>
                               <?php 
                               $default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
                               ?>
                               <span id="bill_amount" >&#x20b9;</span>
                        </div>
                        <div id="payment-list">
                           <?php   
                           $j =0;
                            $paymentMethods = $this->site->getAllPaymentMethods();                                                         
                                foreach ($paymentMethods as $k => $method) { 
                                    $j++;
                                      echo "<button id=\"payment-" . $method->payment_type . "\" type=\"button\" value='" . $method->payment_type . "' class=\"btn-prni payment_type \" data-index='" . $method->payment_type. "' data_id='" . $j. "' ><span>" . $method->display_name . "</span></button>";
                                ?>
                                     <input name="paid_by[]" type="hidden" id="payment_type_<?php echo $method->payment_type; ?>" value="<?php echo $method->payment_type; ?>" class="form-control" autocomplete="off"  />
                            <?php } ?>
                            <div id="sub_items" style="margin-top: 30px;min-height: 165px;">
                                <div class="form-group col-md-6">
                                    <!-- <label><?=lang('customer_name','customer_name')?></label> -->
                                     <input readonly type="hidden" id="loyalty_card_customer_name"  readonly="" class="pa form-control loyalty_card_customer_name"  autocomplete="off" />
                                </div> 
                                <div class="clearfix"></div>

                            <?php
                             $j =0;
                            $paymentMethods = $this->site->getAllPaymentMethods(); 
                            $display = "block";
                            foreach ($paymentMethods as $key => $method) {   $j++; 
                                if($method->payment_type =='cash'){
                                    $display = "block";
                                }else{
                                    $display = "none";
                                }
                                ?>
                                   <div class="col-sm-12 <?=$method->payment_type?>">
                                    <!-- <span style="color: green;font-size: 20px;"><?=$method->payment_type; ?></span> -->
                                    <?php if($method->payment_type=="loyalty") : ?>
				                        <div class="form-group col-md-6">
					                <label><?=lang('search_loyalty_customer','search_loyalty_customer')?></label>
					         <?php
					            echo form_input('loyalty_customer', (isset($_POST['loyalty_customer']) ? $_POST['loyalty_customer'] : ""), 'id="loyalty_customer" data-placeholder="' . $this->lang->line("select") . ' ' . $this->lang->line("loyalty_customer") . '" required="required" class="form-control pos-input-tip" autocomplete="off" style="width:100%;"');
					       ?>
				    </div>
				    <?php endif; ?>
				                <div class="col-sm-6 CC_<?=$method->payment_type.$j?>" id="CC_<?=$method->payment_type?>" style="display: none">
                                         <div class="form-group col-md-12">
                                                <label><?=lang('card_no')?> </label>
								   			<input name="cc_no[]" type="text" maxlength="20" id="pcc_no_<?=$method->payment_type?>"
                                                    class="form-control kb_pad_length cc_no" placeholder="<?=lang('cc_no')?>"/>
                                        </div> 
                                        <div class="form-group col-md-12">
											<label><?=lang('card_exp_date')?> </label>
										   	<input name="card_exp_date[]" type="text" maxlength="6" value ="" id="card_exp_date_<?=$method->payment_type?>"
                                                    class="form-control crd_exp datetime " placeholder="MM/YYYY" />
                                        </div> 
                                        <div class="clearfix"></div>                                        
                                    </div>  
				    <?php
                                    foreach($currency as $currency_row){
                                        $default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
                                        if($currency_row->code == $default_currency_data->code){
                                    ?>   
									<?php   if($method->payment_type =='nc_kot'){   ?>
                                    <div class="col-sm-6 base_currency_<?=$method->payment_type.$j?>" id="base_currency_<?=$method->payment_type?>" style="display:<?php echo $display;?>; "> 
                                        <div class="form-group"  style="margin-bottom: 5px;">
                                    <input name="amount_<?php echo $default_currency_data->code; ?>[]" type="hidden" id="amount_<?php echo $default_currency_data->code; ?>_<?=$method->payment_type?>" data-rate="<?=$currency_row->rate; ?>" data-code="<?=$currency_row->code; ?>" data-id="<?=$currency_row->id; ?>"  class="pa form-control  amounts allowdecimalpoint kb-pad-qty amount_base" payment-type="<?=$method->payment_type?>" autocomplete="off"  />
                                        </div>
                                    </div>
								<?php	}else{  ?>
									<div class="col-sm-6 base_currency_<?=$method->payment_type.$j?>" id="base_currency_<?=$method->payment_type?>" style="display:<?php echo $display;?>; "> 
                                        <div class="form-group"  style="margin-bottom: 5px;">
                                            <label><?=lang('amount')?> <?=$currency_row->code; ?></label>
                                            <input name="amount_<?php echo $default_currency_data->code; ?>[]" type="text" id="amount_<?php echo $default_currency_data->code; ?>_<?=$method->payment_type?>" data-rate="<?=$currency_row->rate; ?>" data-code="<?=$currency_row->code; ?>" data-id="<?=$currency_row->id; ?>"  class="pa form-control  amounts allowdecimalpoint kb-pad-qty amount_base" payment-type="<?=$method->payment_type?>" autocomplete="off"  />
                                        </div>
                                    </div>	
										
										
										
								<?php	}  ?>
                                    <?php }else { ?>
                                    <div class="col-sm-6 multi_currency_<?=$method->payment_type.$j?>" id="multi_currency_<?=$method->payment_type?>" style="display:<?php echo $display;?>; ">
                                        <div class="form-group" >
                                             <label><?=lang('amount')?> <?=$currency_row->code; ?></label>
                                            <input name="amount_<?=$currency_row->code; ?>[]" type="text" id="amount_<?=$currency_row->code; ?>_<?=$method->payment_type?>" data-rate="<?=$currency_row->rate; ?>" data-code="<?=$currency_row->code; ?>" data-id="<?=$currency_row->id; ?>"  class="pa form-control  amounts kb-pad-qty allowdecimalpoint	 amount_base" payment-type="<?=$method->payment_type?>"  autocomplete="off"/>
                                        </div>
                                    </div>
                                    <?php }   } ?>  

                                    <div class="clearfix"></div>                                    
                                       
                                   <div class="form-group lc_<?=$method->payment_type.$j?>" id="lc_<?=$method->payment_type?>" style="display: none">    
                                        <div class="form-group col-md-6">
                                            <label><?=lang('Points')?></label>
                                             <input name="paying_loyalty_points[]" type="text" id="loyalty_points_<?=$method->payment_type?>" idd="<?=$method->payment_type?>" class="pa form-control loyalty_points kb-pad"  autocomplete="off" />
                                        </div> 

                                        <div class="clearfix"></div>       
                                        <div id="lc_details_<?=$method->payment_type?>" style="color: red;"> </div>
                                        <div id="lc_reduem_<?=$method->payment_type?>" style="color: green;"></div>
                                    </div>
                                    <div class="clearfix"></div>
									 <div class="form-group ws_<?=$method->payment_type.$j?>" id="ws_<?=$method->payment_type?>" style="display: none">    
									 <?php  $wallets= $this->site->getWallets();  ?>
									 <div class="form-group col-md-6">
                                       <label><?=lang('type')?></label>
										<select class="form-control"  name="wallet_type">
										<option value="">Select</option>
										<?php   if($wallets){ foreach($wallets as $wallet){      ?>
										<option value="<?php  echo $wallet->id;  ?>"><?php  echo $wallet->name;  ?></option>
										<?php   }   }   ?>
										</select>
										</div>                               
                                        
                                    </div>
									<div class="clearfix"></div>
									 <div class="nc_kot_sec nk_<?=$method->payment_type.$j?>" id="nk_<?=$method->payment_type?>" style="display: none"> 
									 <?php $nc_kot_master=$this->site->get_ncKotMasters();  if(!empty($nc_kot_master)){ foreach($nc_kot_master as $row){ ?>
										<div class="panel panel-primary">
											<div class="panel-heading"><div class="radio">
									<label><input type="radio" name="master_active" value="<?php echo $row->id ;   ?>"><?php echo $row->display_name ;   ?></label>
			                           </div></div>
											<div class="panel-body">
											<?php if(!empty($row->no_of_input_box)){
													for($i=0;$i<$row->no_of_input_box;$i++){								
												?>
												<label class="col-md-4">Comments</label>
											   <div class="col-md-8">
											   <input type="text" name="master_input[<?php echo $row->id;  ?>][]" class="form-control"  />
											   </div>
											
											<?php } } ?>
											<?php if(!empty($row->no_of_select_box)){ 
                                               //   for($j=0;$j<$row->no_of_select_box;$j++){
											foreach(json_decode($row->select_box_master) as $r){   ?>
												<div class="form-group col-md-12">
													<label class="col-md-4"><?php switch($r){
														case 1:
														   echo 'Customer';
														   $list=$this->site->getAllCompanies('customer');
														break;
													    case 2:
														   echo 'Company';
														   $list=$this->site->getAllCompanies('supplier');
														   break;
														case 3:
														   echo 'User';
														   $list=$this->site->getUsers();
														break;


													}														?></label>
													<div class="col-md-8">
														<select class="form-control" name="master_select[<?php echo $row->id;  ?>][<?php echo $r;   ?>][]">
														<option value="">Select</option>
														<?php if(!empty($list)){ foreach($list as $s){   ?>
														<option value="<?php  echo $s->id;  ?>"><?php  echo $s->name; ?></option>
														<?php } }   ?>
														</select>
													</div>
												</div>
									 <?php // }
									 } }  ?> 
											</div>
										</div>			

							           <?php }  }     ?>
										
									                            
                                        
                                    </div>
									<div class="clearfix"></div>
                                    <!-- <div class="col-sm-6 CC_<?=$method->payment_type.$j?>" id="CC_<?=$method->payment_type?>" style="display: none">
                                         <div class="form-group col-md-6">
                                                <label><?=lang('card_no')?> </label>
								   			<input name="cc_no[]" type="text" id="pcc_no_<?=$method->payment_type?>"
                                                    class="form-control kb-pad" placeholder="<?=lang('cc_no')?>"/>
                                        </div> 
                                        <div class="form-group col-md-6">
											<label><?=lang('card_exp_date')?> </label>
										   	<input name="card_exp_date[]" type="text" id="card_exp_date_<?=$method->payment_type?>"
                                                    class="form-control kb-pad" placeholder="<?=lang('card_exp_date')?>"/>
                                        </div> 
                                        <div class="clearfix"></div>                                        
                                    </div>  --> 
                                    <div style="margin-bottom: 10px"></div>
                                </div>                                  
                                <?php  } ?> 
                                </div>
                        </div>  
                                <div class="form-group" style="margin-bottom: 5px;">
                                    <div id="userd_tender_list">         
                                    </div>
                                </div>
                                <div class="col-md-12 col-sm-12">
                                <div class="form-group total_pay"  style="margin-bottom: 5px;">
                                    <table class="table table-condensed table-striped" style="margin-bottom: 0;">
                                        <tr>
                                            <td rowspan="4" class="total_paytd" style="width: 50%!important;text-align: center">
                                                &nbsp;<?=lang('total_pay')?>
                                            </td> 
                                        </tr>
                                         <?php   foreach($currency as $currency_row) { ?>
                                               <tr><td class="total_paytd" style="text-align: left;"><?php echo $currency_row->code; ?>&nbsp;:&nbsp;&nbsp;<span id="twt_<?php echo $currency_row->code; ?>"> &nbsp;&nbsp;0.00 &nbsp;&nbsp;</span>
                                                <input type="hidden" id="paid_amt_<?php echo $currency_row->code; ?>"></td></tr>
                                         <?php } ?>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-12 col-sm-12">   
                                <div class="form-group balance_pay"  style="margin-bottom: 5px;">
                                    <table class="table  table-condensed table-striped" style="margin-bottom: 0;">
                                        <tr>
                                            <td rowspan="4" class="balance_paytd" style="width: 50%!important;text-align: center">&nbsp;Change Pay</td> 
                                        </tr>
                                         <?php     foreach($currency as $currency_row) { ?>
                                               <tr><td class="balance_paytd" style="text-align: left;"><?php echo $currency_row->code; ?>&nbsp;:&nbsp;&nbsp;<span id="balance_<?php echo $currency_row->code; ?>">0.00</span>
                                                   <input type="hidden" id="balance_amt_<?php echo $currency_row->code; ?>"></td></tr>
                                            <?php } ?>
                                    </table>
                                </div>  
                            </div>                                  
                            <div id="payments" class="payment-row" style="display: none">
                                <div class="well well-sm well_1" style="padding: 5px 10px;">
                                    <div class="payment">
                                        <div class="row">                        
                                        </div>
                                    </div>
                                </div>
                            </div>    
             <?php                
             echo form_hidden('remove_image','No');
             echo form_hidden('action', 'PAYMENT-SUBMIT');
             echo form_close();
             ?>
            <div class="modal-footer">
                <button type="button" class="btn btn-block btn-lg btn btn-info" id="submit-sale"><?=lang('send');?></button>
            </div>
        </div>
    </div>
</div>


<div id="bill_tbl"  style="display:none;"><span id="bill_span"></span>
    <div id="bill_header"></div>
   <!--  <table id="bill-table" width="100%" class="prT table table-striped" style="margin-bottom:0;"></table> -->
   <div id="bill-total-table"></div>
    <!-- <table id="bill-total-table" class="prT table table table-striped " ></table> -->
    <span id="bill_footer"></span>
</div>
	<script>
	var site = <?=json_encode(array('url' => base_url(), 'base_url' => admin_url('/'), 'assets' => $assets, 'settings' => $Settings, 'dateFormats' => $dateFormats))?>, pos_settings = <?=json_encode($pos_settings);?>;
	
	</script>
<!--scripts-->
<script type="text/javascript" src="<?=$assets?>js/jquery-2.0.3.min.js"></script>
<!--<script type="text/javascript" src="<?=$assets?>js/jquery.mCustomScrollbar.concat.min.js"></script>-->
<script type="text/javascript" src="<?=$assets?>js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/jquery-ui.js"></script>
<script type="text/javascript" src="<?=$assets?>js/perfect-scrollbar.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/select2.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/custom.js"></script>
<script type="text/javascript" src="<?=$assets?>js/jquery.calculator.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/bootstrapValidator.min.js"></script>
<script type="text/javascript" src="<?=$assets?>pos/js/plugins.min.js"></script>
<script type="text/javascript" src="<?=$assets?>pos/js/parse-track-data.js"></script>
<!--<script type="text/javascript" src="<?=$assets?>pos/js/pos.ajax.js?v=1"></script>-->
<script type="text/javascript" src="<?=$assets?>pos/js/pos_v2.ajax.js?v=1"></script>
<script type="text/javascript" src="<?=$assets?>styles/helpers/icheck/square/icheck.min.js"></script>

<script type="text/javascript">
<?php unset($Settings->setting_id, $Settings->smtp_user, $Settings->smtp_pass, $Settings->smtp_port, $Settings->update, $Settings->reg_ver, $Settings->allow_reg, $Settings->default_email, $Settings->mmode, $Settings->timezone, $Settings->restrict_calendar, $Settings->restrict_user, $Settings->auto_reg, $Settings->reg_notification, $Settings->protocol, $Settings->mailpath, $Settings->smtp_crypto, $Settings->corn, $Settings->customer_group, $Settings->srampos_username, $Settings->purchase_code);?>
var recipe_variant = 0, shipping = 0, p_page = 0, per_page = 0,tcp = "<?=$tcp?>", pro_limit = <?= $pos_settings->pro_limit; ?>,
brand_id = 0, obrand_id = 0, cat_id = "<?=$pos_settings->default_category?>", ocat_id = "<?=$pos_settings->default_category?>", sub_cat_id = 0, osub_cat_id,
count = 1, an = 1, DT = <?=$Settings->default_tax_rate?>,
recipe_tax = 0, invoice_tax = 0, recipe_discount = 0, order_discount = 0, total_discount = 0, total = 0, total_paid = 0, grand_total = 0,
KB = <?=$pos_settings->keyboard?>, tax_rates =<?php echo json_encode($tax_rates);?>; service_charge =<?php echo json_encode($service_charge); ?>;
var protect_delete = <?php if (!$Owner && !$Admin) {echo $pos_settings->pin_code ? '1' : '0';} else {echo '0';} ?> 

var site = <?=json_encode(array('url' => base_url(), 'base_url' => admin_url('/'), 'assets' => $assets, 'settings' => $Settings,
 'dateFormats' => $dateFormats,'pos_settings'=>json_encode($pos_settings)))?>, pos_settings = <?=json_encode($pos_settings);?>;;

var lang = {
    unexpected_value: '<?=lang('unexpected_value');?>',
    select_above: '<?=lang('select_above');?>',
    r_u_sure: '<?=lang('r_u_sure');?>',
    bill: '<?=lang('bill');?>',
    order: '<?=lang('order');?>',
    total: '<?=lang('total');?>',
    items: '<?=lang('items');?>',
    discount: '<?=lang('discount');?>',
    order_tax: '<?=lang('order_tax');?>',
    grand_total: '<?=lang('grand_total');?>',
    total_payable: '<?=lang('total_payable');?>',
    rounding: '<?=lang('rounding');?>',
    merchant_copy: '<?=lang('merchant_copy');?>'
};
</script>

<script type="text/javascript">
		$(".allowdecimalpoint").on("keypress keyup blur",function (event) {
            //this.value = this.value.replace(/[^0-9\.]/g,'');
            $(this).val($(this).val().replace(/[^0-9\.]/g,''));
            if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
                event.preventDefault();
            }
        });
    function widthFunctions(e) {
        var wh = $(window).height(),
        lth = $('#left-top').height(),
        lbh = $('#left-bottom').height();
        $('#item-list').css("height", wh - 360);
        $('#item-list').css("min-height", 205);
        $('#left-middle').css("height", wh - lth - lbh - 102);
        $('#left-middle').css("min-height", 278);
        $('#recipe-list').css("height", wh - lth - lbh - 107);
        $('#recipe-list').css("min-height", 278);
    }
    $(window).bind("resize", widthFunctions);
    $(document).ready(function () {
        $('#view-customer').click(function(){
            $('#myModal').modal({remote: site.base_url + 'customers/view_customer/' + $("input[name=customer]").val()});
            $('#myModal').modal('show');
        });
        $('textarea').keydown(function (e) {
            if (e.which == 13) {
               var s = $(this).val();
               $(this).val(s+'\n').focus();
               e.preventDefault();
               return false;
            }
        });
		<?php if ($this->session->userdata('remove_posls')) {?>
        <?php $this->sma->unset_data('remove_posls');}
        ?>
		<?php
		if($order_type == 3 && !empty($same_customer) && !empty($this->input->get('customer'))){
			$customer = $this->input->get('customer');
		}elseif($order_type == 3 && empty($same_customer) && !empty($this->input->get('customer'))){
			$customer = $this->input->get('customer');
		}elseif($order_type == 3 && !empty($same_customer) && empty($this->input->get('customer'))){
			$customer = $same_customer;
		}elseif($order_type == 3 && empty($same_customer) && empty($this->input->get('customer'))){
			$customer = '';
		}elseif($order_type != 3 && !empty($same_customer) && !empty($this->input->get('customer'))){
			$customer = $this->input->get('customer');
		}elseif($order_type != 3 && empty($same_customer) && !empty($this->input->get('customer'))){
			$customer = $this->input->get('customer');
		}elseif($order_type != 3 && !empty($same_customer) && empty($this->input->get('customer'))){
			$customer = $same_customer;
		}elseif($order_type != 3 && empty($same_customer) && empty($this->input->get('customer'))){
			$customer = $customer->id;	
		}else{
			$customer = '';
		}
		?>
		<?php  if(!empty($customer)){  ?>
		 if (localStorage.getItem('poscustomer')) {
			localStorage.removeItem('poscustomer');
		 }
		if (!localStorage.getItem('poscustomer')) {
            localStorage.setItem('poscustomer', <?=$customer;?>);
        }
		<?php  }   ?>
        $('.select').select2({minimumResultsForSearch: 7});
        $('#poscustomer').val(localStorage.getItem('poscustomer')).select2({
            minimumInputLength: 1,
            data: [],
            initSelection: function (element, callback) {
                $.ajax({
                    type: "get", async: false,
                    url: "<?=admin_url('customers/getCustomer')?>/" + $(element).val(),
                    dataType: "json",
                    success: function (data) {
                        callback(data[0]);
                    }
                });
            },
            ajax: {
                url: site.base_url + "customers/suggestions_with_discount_card",
                dataType: 'json',
                quietMillis: 15,
                data: function (term, page) {
                    return {
                        term: term,
                        limit: 10
                    };
                },
                results: function (data, page) {
                    if (data.results != null) {
                        return {results: data.results};
                    } else {
                        return {results: [{id: '', text: 'No Match Found'}]};
                    }
                }
            }
        });
        if (KB) {
            display_keyboards();
            var result = false, sct = '';
            $('#poscustomer').on('select2-opening', function () {
                sct = '';
                $('.select2-input').addClass('kb-text-click');
                display_keyboards();
                $('.select2-input').bind('change.keyboard', function (e, keyboard, el) {
                    if (el && el.value != '' && el.value.length > 0 && sct != el.value) {
                        sct = el.value;
                    }
                    if(!el && sct.length > 0) {
                        $('.select2-input').addClass('select2-active');
                       // setTimeout(function() {
                            $.ajax({
                                type: "get",
                                async: false,
                                url: "<?=admin_url('customers/suggestions_with_discount_card')?>/?term=" + sct,
                                dataType: "json",
                                success: function (res) {
                                    if (res.results != null) {
                                        $('#poscustomer').select2({data: res}).select2('open');
                                        $('.select2-input').removeClass('select2-active');
                                    } else {
                                         bootbox.alert('no_match_found');
                                        $('#poscustomer').select2('close');
                                        $('#test').click();
                                    }
                                }
                            });
                        //}, 500);
                    }
                });
            });

            $('#poscustomer').on('select2-close', function () {
                $('.select2-input').removeClass('kb-text-click');                
                $('#test').click();
                $('select, .select').select2('destroy');
                $('select, .select').select2({minimumResultsForSearch: 7});
            });
            $(document).bind('click', '#test', function () {
                var kb = $('#test').keyboard().getkeyboard();                
                kb.close();
            });

        }
    });
function display_keyboards() {
    $('.kb-text-click').keyboard({
        autoAccept: true,
        alwaysOpen: false,
        openOn: 'focus',
        usePreview: false,
        layout: 'custom',
        //layout: 'qwerty',
        display: {
            'bksp': "\u2190",
            'accept': 'return',
            'default': 'ABC',
            'meta1': '123',
            'meta2': '#+='
        },
        customLayout: {
            'default': [
            'q w e r t y u i o p {bksp}',
            'a s d f g h j k l {enter}',
            '{s} z x c v b n m , . {s}',
            '{meta1} {space} {cancel} {accept}'
            ],
            'shift': [
            'Q W E R T Y U I O P {bksp}',
            'A S D F G H J K L {enter}',
            '{s} Z X C V B N M / ? {s}',
            '{meta1} {space} {meta1} {accept}'
            ],
            'meta1': [
            '1 2 3 4 5 6 7 8 9 0 {bksp}',
            '- / : ; ( ) \u20ac & @ {enter}',
            '{meta2} . , ? ! \' " {meta2}',
            '{default} {space} {default} {accept}'
            ],
            'meta2': [
            '[ ] { } # % ^ * + = {bksp}',
            '_ \\ | &lt; &gt; $ \u00a3 \u00a5 {enter}',
            '{meta1} ~ . , ? ! \' " {meta1}',
            '{default} {space} {default} {accept}'
            ]}
        });
    $('.kb-pad').keyboard({
        restrictInput: true,
        preventPaste: true,
        autoAccept: true,
        alwaysOpen: false,
        openOn: 'click',
        usePreview: false,
        layout: 'custom',
        display: {
            'b': '\u2190:Backspace',
        },
        customLayout: {
            'default': [
            '1 2 3 {b}',
            '4 5 6 . {clear}',
            '7 8 9 0 %',
            '{accept} {cancel}'
            ]
        }
    });
    var cc_key = (site.settings.decimals_sep == ',' ? ',' : '{clear}');
    $('.kb-pad1').keyboard({
        restrictInput: true,
        preventPaste: true,
        autoAccept: true,
        alwaysOpen: false,
        openOn: 'click',
        usePreview: false,
        layout: 'custom',
        display: {
            'b': '\u2190:Backspace',
        },
        customLayout: {
            'default': [
            '1 2 3 {b}',
            '4 5 6 . '+cc_key,
            '7 8 9 0 %',
            '{accept} {cancel}'
            ]
        }
    });

 }  
</script>
	<script>
	KB = <?=$pos_settings->keyboard ?>;
	$(document).on('click','.change_customer',function(e){	
        e.preventDefault();
        $('#change_split_id').val('');
        var change_split = $(this).attr("split");
		var customer_id = $(this).attr("customer_id");
		//console.log(55)
		$("#poscustomer").val(customer_id);
		$("#poscustomer").trigger("change");
		$('#customer-change-Modal').show(); 
		$('#change_split_id').val(change_split);
    });
	$(document).on('click','.change_table',function(e){	
        e.preventDefault();
        $('#change_split_id').val('');
        var change_split = $(this).attr("split");
		$('#table-change-Modal').show();
		$('#change_split_id').val(change_split);
    });	
	$(document).on('click','.merge_bill',function(e){	
        e.preventDefault();
        $('#merge_split_id').val('');
        var current_split = $(this).attr("split");
        var table_id = $(this).attr("table_id");
        $('#merge_split_id').val(current_split);
        $('#merge_table_id').val(table_id);
        $('.merge-group-list').empty();
         $.ajax({
            type: "post",
           url:"<?=base_url('pos/pos/get_splits_for_merge');?>",                
            data: {current_split: current_split,table_id:table_id},
            dataType: "json",
            success: function (res) {
				var check_Box ='';
				$('.merge_table_id').html('');
				$('.merge-group-list').empty();
				if(res.data){
				$.each(res.data, function(i, item) {
					check_Box = "<label>"+"<input type='checkbox' class='merge' name='merge[]' value='" + item.split_id + "'/>" + item.name +"</label>"+ "<br/>";
		             //check_Box = "<input type='checkbox' class='merge'  id='merge_id'"+ table_id + "  name='merge[]' value='" + item.split_id + "'/>" + item.name + "<br/>";

					//alert(check_Box);
					$(check_Box).appendTo('.merge-group-list');
				});
				}else{
					$('.merge-group-list').text("No Order Found.")
				}
            }    
        });
		$('#splits-merge-Modal').show();
    });	
	
$(document).on('click','#Mergesplits',function(){
var checkedNum = $('input[name="merge[]"]:checked').length; 
var current_split = $('#merge_split_id').val();
var merge_table_id = $('#merge_table_id').val();
var merge_splits = [];
var i = 0;
if(checkedNum > 0){
       $('.merge:checked').each(function () {
           merge_splits[i++] = $(this).val();           
       });
       $.ajax({
            type: "POST",
            url:"<?= base_url('pos/pos/multiple_splits_mergeto_singlesplit_for_consolidate');?>",                
            data: {merge_splits: merge_splits, current_split: current_split, merge_table_id:merge_table_id},
            dataType: "json",
            success: function (data) {
                if(data.msg == 'success'){
                         $('#splits-merge-Modal').hide(); 
                        location.reload();
                }else{
                    bootbox.alert('<?=lang('uanble_to_merge');?>');
                    return false;
                }
            }    
        });   
}else{
	bootbox.alert('Please select any one split');
	return false;
}
return false;
});
function send_kot($split_id) {
	        $.ajax({
                        type: "post",
                        url:"<?=base_url('pos/pos/kot_print_copy/');?>"+$split_id,           
                        success: function (data) {
                            bootbox.alert('sent to kot print');
                        }    
                   })
            }
$(document).on('click', '.closemodal', function () {
    $('#changed_table_id').val('');
    $('#change_split_id').val('');
    $('#table-change-Modal').hide();
    $('#customer-change-Modal').hide(); 
});
$(document).on('click','#OrderChangeTable',function(){
     var change_split_id = $('#change_split_id').val();
      var changed_table_id =  $("#changed_table_id option:selected").val();
     if($.trim(changed_table_id) != '' && $.trim(changed_table_id) != 0){
        $.ajax({
            type: "POST",
            url:"<?= base_url('pos/pos/change_table_number_all');?>",                
            data: {change_split_id: change_split_id, changed_table_id: changed_table_id},
            dataType: "json",
            success: function (data) {
                if(data.msg == 'success'){
                         $('#table-change-Modal').hide(); 
                         //location.reload();
						   window.location.href = "<?= base_url('pos/pos/'); ?>";
                }else{
                    bootbox.alert('<?=lang('uanble_to_cancle');?>');
                    return false;
                }
            }    
        })
     }   
     else{
     	bootbox.alert('<?=lang('please_select_changing_table');?>');
        return false;
     }
});
function CancelAllOrderItems( table_id ,$remarks=0, splitid){
                $cancelQty = 'all';
                $('#order_table_id').val(table_id); 
                $('#split_table_id').val(splitid); 
                cancelAllorderPopup(table_id,$remarks,splitid);
            }
function cancelAllorderPopup(table_id,$remarks){
        if($remarks!=0){
                 $('#remarks').val('');
                 $('#CancelAllorderModal').show();
         }else{
            $msg = 'Are you sure want to cancel this order?';
		    bootbox.confirm({
			message: $msg,
			buttons: {
			    confirm: {
				label: 'Yes',
				className: 'btn-success'
			    },
			    cancel: {
				label: 'No',
				className: 'btn-danger'
			    }
			},
			callback: function (result) {
				var split_table_id = $('#split_table_id').val(); 
			    if (result) {				
				$.ajax({
				    type: "get",
				    url:"<?= base_url('pos/pos/cancel_all_order_items');?>",                
				    data: {table_id: table_id,split_table_id: split_table_id},
				    dataType: "json",
				    success: function (data) {
					if(data.msg == 'success'){

						//ajaxDatatimeout = setInterval(ajaxData, 6000);
						location.reload();      	                      	
					}else{
					    alert('not update waiter');
					}
				    }    
				}).done(function () {
				      
				});
				  
			    }else{
					       //requestBill(billid);
			    }
			    
			}
		    });
		}
	    }
		 $(document).ready(function(){
	$(document).on('click','#cancel_allorderitem',function(){
	    $obj =$(this);
            	 var cancel_remarks = $('#cancel-remarks').val();
            	 var table_id = $('#order_table_id').val(); 
            	 var split_table_id = $('#split_table_id').val(); 
            	 if($.trim(cancel_remarks) != ''){
		         $(this).attr('disabled',true);
		         $submit_text = $(this).text();
		         $(this).text('please wait...');
            	 	$.ajax({
                        type: "get",
                        url:"<?= base_url('pos/pos/cancel_all_order_items');?>",                
                        data: {table_id:table_id,cancel_remarks: cancel_remarks,split_table_id:split_table_id},
                        dataType: "json",
                        success: function (data) {
			            $obj.attr('disabled',false);
			            $obj.text($submit_text);			    
                            if(data.msg == 'success'){
                            	     $('#CancelAllorderModal').hide(); 
									 window.location.href = "<?= base_url('pos/pos/'); ?>";               	
                            }else{
				
                                alert('not cancelled');
                            }
                        }    
                    }).done(function () {
				      
					});
            	 } else{
		        $('#cancel-remarks').css('border','1px solid red');
		 }

       });
    })  
				
   function bilGenerator( table_id, split_id, count_id ){    
            	$("#bil_table_type").val(table_id);
				$("#bil_split_type").val(split_id);
				/* if(count_id == 0 || count_id == 1){
					$(".count_div").hide();
				}
				else{
					$(".count_div").show();
				} */
				$(".count_div").show();
            	$('#bilModal').show();

            }
			$(document).on('change','#bils_number_auto',function(){
				if($(this).val()>0){
					 $('#bils_number_auto').css('border', '1px solid #ccc');
				}
			});
			$(document).on('change','#bils_number_manual',function(){
				if($(this).val()>0){
					 $('#bils_number_manual').css('border', '1px solid #ccc');
				}
			});
			$(document).on('click','#updateBil',function(){
                 var table_id = $('#bil_table_type').val(); 
            	 /*var count_item = $('#count_item').val();*/ 
            	 var split_id = $('#bil_split_type').val(); 
            	 var count_item = $('#'+split_id+'_count_item').val();
				 var bil_type = $('input[name=bil_type]:checked').val();
				 if($('input[name=bil_type]:checked').val() == null){
					     bootbox.alert('PLEASE SELECT SPLIT');
				 }
				 var url = '<?php echo  admin_url('pos') ?>';
				 if(bil_type == 1){
					 var bils = 1;
				 }else if(bil_type == 2){
					 var bils = $('#bils_number_auto').val(); 
					 if(bils.length>2){
						  bootbox.alert('PLEASE ENTER THE VALID SPLIT NUMBER');
						  $('#bils_number_auto').val(''); 
						  return false;
					 }
				 }else if(bil_type == 3){
                    var bils = $('#bils_number_manual').val();
					//enter value is equal to 1 automatically  will go to single split
                    if(bils == 1){
                        bil_type = 1;                        
                    }
					
					
                   /*  if(count_item <bils){
                        bootbox.alert('<?=lang('manual_bill');?>');
                        return false;
                    } */
				 }

				 if(bils > 0 && bils !=1){
					 window.location.href= '<?php echo base_url();   ?>pos/pos/billing/?order_type=1&bill_type='+bil_type+'&bils='+bils+'&table='+table_id+'&splits='+split_id;
				 }else{
					 if(bil_type == 2){
				//	  $('#bils_number_auto').css('border-color', 'red');
				       bootbox.alert('PLEASE ENTER THE SPLIT NUMBER');
					 }
					 if(bil_type == 3){
					 //$('#bils_number_manual').css('border-color', 'red');
					  bootbox.alert('PLEASE ENTER THE SPLIT NUMBER');
					 }
            	 	//alert('Please enter 1 or more than 1');
				 }

            });
			
			$('.closebil').click(function () {
            	$("#bil_table_type").val('');
				$("#bil_split_type").val('');
            	$('#bilModal').hide();
            });
/* 			$(document).ready(function(){
    $('.cancel-type').iCheck({
			checkboxClass: 'icheckbox_square-blue',
			radioClass: 'iradio_square-blue',
			increaseArea: '20%'
		});
}); */
		$(document).ready(function() {
    //$('input[type=radio][name=bil_type]').change(function() {
		$('input[type=radio][name=bil_type]').on('ifChanged', function() {
		$("#bils_number_auto").val('');
		$("#bils_number_manual").val('');
         if (this.value == 1) {
            $("#bils_number_auto").hide();
			$("#bils_number_manual").hide();
         }else if (this.value == 2) {
			$("#bils_number_manual").hide();
			$("#bils_number_auto").show();
         }else if (this.value == 3) {
            $("#bils_number_auto").hide();
			$("#bils_number_manual").show();
        }
    });
});	
	</script>
	<script>
 $("input[type='checkbox'], input[type='radio']").iCheck({
                checkboxClass: 'icheckbox_square',
                radioClass: 'iradio_square'
  });
</script>
<script>
var username = '<?=$this->session->userdata('username');?>', order_data = '', bill_data = '';
function widthFunctions(e) {
var wh = $(window).height(),
lth = $('#left-top').height(),
lbh = $('#left-bottom').height();
$('#item-list').css("height", wh - 360);
$('#item-list').css("min-height", 205);
$('#left-middle').css("height", wh - lth - lbh - 102);
$('#left-middle').css("min-height", 278);
$('#recipe-list').css("height", wh - lth - lbh - 107);
$('#recipe-list').css("min-height", 278);
}
$(window).bind("resize", widthFunctions);
$(document).ready(function () {
$('#print_order').click(function () {
if (count == 1) {
bootbox.alert('<?=lang('x_total');?>');
return false;
}
<?php if ($pos_settings->remote_printing != 1) { ?>
printOrder();
<?php } else { ?>
Popup($('#order_tbl').html());
<?php } ?>
});
$('#print_bill').click(function () {
if (count == 1) {
bootbox.alert('<?=lang('x_total');?>');
return false;
}
<?php if ($pos_settings->remote_printing != 1) { ?>
printBill();
<?php } else { ?>
Popup($('#bill_tbl').html());
<?php } ?>
});
});
<?php if ($pos_settings->remote_printing == 1) { ?>
function Popup(data) {
    var mywindow = window.open('', 'sma_pos_print', 'height=500,width=300');
                var is_chrome = Boolean(mywindow.chrome);
                mywindow.document.write('<html><head><title>Print</title>');
                mywindow.document.write("<style type='text/css' media = 'print'>@page {margin: "+$print_header_space+" 5mm "+$print_footer_space+" 5mm;}</style>");
                mywindow.document.write('<link rel="stylesheet" href="<?=$assets?>styles/helpers/bootstrap.min.css" type="text/css" />');
                mywindow.document.write('</head><body >');
                mywindow.document.write(data);
                mywindow.document.write('</body></html>');
               if (is_chrome) {
                 setTimeout(function() { // wait until all resources loaded 
                    mywindow.document.close(); // necessary for IE >= 10
                    mywindow.focus(); // necessary for IE >= 10
                    mywindow.print(); // change window to winPrint
                    mywindow.close(); // change window to winPrint
                 }, 250);
               } else {
                    mywindow.document.close(); // necessary for IE >= 10
                    mywindow.focus(); // necessary for IE >= 10

                    mywindow.print();
                    mywindow.close();
               }
                return true;

/*var mywindow = window.open('', 'sma_pos_print', 'height=500,width=300');
mywindow.document.write('<html><head><title>Print</title>');
mywindow.document.write('<link rel="stylesheet" href="<?=$assets?>styles/helpers/bootstrap.min.css" type="text/css" />');
mywindow.document.write('</head><body >');
mywindow.document.write(data);
mywindow.document.write('</body></html>');
mywindow.print();
mywindow.close();
return true;*/
}
<?php }
?>
</script>

<script type="text/javascript">
var $print_header_space = '<?=$pos_settings->pre_printed_header?>mm';
var $print_footer_space = '<?=$pos_settings->print_footer_space?>mm';
var pre_printed = '<?=$pos_settings->pre_printed_format?>';
$(document).on('click', '.billsave', function () {
$(this).text('<?=lang('loading');?>').attr('disabled', true);
$('#pos-autosplitbill-form').submit();
return false;
});

 function CancelOrderItem( status, id, split_id ,$remarks=0,$quantity){   
                $("#order_item_id").val(id);
                $("#split_order").val(split_id);
              if ($quantity>1) {
				$inputoptions =[];
                $inputoptions[0] ={text: 'Select the cancel quantity',value: ''};
                for (i = 1; i < $quantity; i++) {
                $v = i;
                $inputoptions[i] = {text: $v,value:$v};
                }
                bootbox.prompt({ 
                title: "Enter Quantity to cancel",
                inputType:'select',
                inputOptions :$inputoptions,
                callback: function(qty){
                    if (qty!='') {
                     $cancelQty = qty;
                    if ($quantity==qty) {
                        $cancelQty = 'all';
                    }
                    cancelorderPopup(id ,split_id,$remarks,$cancelQty);
                    $('#cancel_qty').val($cancelQty);
                    }else{
                    bootbox.alert("Please select Quantity");
                    }
                }
                });
            }else{
                $cancelQty = 'all';
                cancelorderPopup(id ,split_id,$remarks,$cancelQty);
                $('#cancel_qty').val($cancelQty);
            } 
            }
          function cancelorderPopup(id,split_id,$remarks,$cancelQty){
                if($remarks!=0){
                    $('#remarks').val('');
                    $('#CancelorderModal').show();
                }else{
                    $msg = ($cancelQty!='all')?'Are you sure want to cancel '+$cancelQty+' Qty?':'Are you sure want to cancel this item?';
             bootbox.confirm({
             message: $msg,
             buttons: {
                confirm: {
                label: 'Yes',
                className: 'btn-success'
                },
                cancel: {
                label: 'No',
                className: 'btn-danger'
                }
            },
            callback: function (result) {
                if (result) {               
                $.ajax({
                    type: "get",
                    url:"<?= base_url('pos/pos/cancel_order_items');?>",                
                    data: {order_item_id: id, split_id: split_id,cancelqty:$cancelQty},
                    dataType: "json",
                    success: function (data) {
                    if(data.msg == 'success'){
                        
                        location.reload();                                  
                    }else{
                        alert('not update waiter');
                    }
                    }    
                }).done(function () {
                      
                });
                }
            }
            });
        }
        }
        $('#remarks').on('focus',function(){
        $('#remarks').css('border','1px solid #ccc');
        });
            $(document).on('click','#cancel_orderitem',function(){
                 var cancel_remarks = $('#remarks').val();
                 var cancel_type = $('.cancel-type:checked').val(); 
                 var order_item_id = $('#order_item_id').val(); 
                 var split_id = $("#split_order").val();
                 var $cancelQty = $('#cancel_qty').val();
                 if($.trim(cancel_remarks) != ''){
				 $(this).text('please wait...');
			     $(this).attr('disabled',true);
                    $.ajax({
                        type: "get",
                        url:"<?= base_url('pos/pos/cancel_order_items');?>",                
                        data: {cancel_type:cancel_type,cancel_remarks: cancel_remarks, order_item_id: order_item_id, split_id: split_id,cancelqty:$cancelQty},
                        dataType: "json",
                        success: function (data) {
                            if(data.msg == 'success'){
                                     $('#CancelorderModal').hide(); 
                                     
                                     location.reload();                                 
                            }else{
                                alert('not update waiter');
                            }
                        }    
                    }).done(function () {
                      
                    });
                 } else{
                $('#remarks').css('border','1px solid red');
         }

            });
            $('.closemodal').click(function () {
                $('#remarks').val('');
                $('#order_item_id').val('');
                $('#split_order').val('');
                $('#cancel_qty').val('');
                $('#CancelorderModal').hide();                 
            });
        $('.cancelclosemodal').click(function () {
                $('#remarks').val('');
                $('#order_table_id').val('');                
                $('#CancelAllorderModal').hide();                 
            });
            

/*$(document).on('click', '#cancel-item', function () {    
    $('#CancelorderModal').show();    
     return false;
});*/

$(document).on('click', '#print-sale', function () {
    $obj = $(this);
    $(this).attr('disabled', true);
    $data = $('#pos-autosplitbill-form').serialize();
	$.ajax({
        type: 'POST',
                url: '<?= base_url('pos/pos/billprint');?>',
                //dataType: "json",
                data: $('#pos-autosplitbill-form').serialize(),
                success: function (data) {
                    $obj.attr('disabled', false);
                    Popup(data);
                }
});

return false;

});

$(document).on('change', '.ptax', function () {

var find_attr = $(this).attr('count');    
var subtotal  = 0;
var tax_amount  = $('#tax_amount_'+ find_attr).val();
var unit_price = 0;
var off_discount = $('#off_discount_'+ find_attr).val();
var discount = $('#item_discounts_'+ find_attr).val();

if(typeof off_discount == "undefined")
{ 
discount = discount;
subtotal  = $('#item_dis_'+ find_attr).text();
unit_price = parseFloat($('#item_dis_'+ find_attr).val());
}
else{
discount = off_discount;
subtotal  = $('#tds1_'+ find_attr).text();
unit_price = parseFloat($('#tds1_'+ find_attr).text());
}

/*var ds = $('#order_discount_input_'+ find_attr).val() ? $('#order_discount_input_'+ find_attr).val() : '0';*/

var pr_tax = $('#ptax_'+find_attr).children(":selected").data("id");

    if (ds.indexOf("%") !== -1) {            
        var pds = ds.split("%");
        if (!isNaN(pds[0])) {
        item_discount = parseFloat(((unit_price) * parseFloat(pds[0])) / 100);
        } else {
        item_discount = parseFloat(ds);
        }
    } else {            
        item_discount = parseFloat(ds);
    }
    var final_discount =  parseFloat(item_discount)  + parseFloat(discount);
    var final_discount_amount = parseFloat(unit_price) - parseFloat(item_discount);

    var pr_tax_val = 0;
    if (pr_tax !== null && pr_tax != 0) {
        $.each(tax_rates, function () {                       
        pr_tax_val = parseFloat(((final_discount_amount) * parseFloat(pr_tax)) / 100);
        pr_tax_rate = (pr_tax) + '%';                
        });
    }
    var final_tax = parseFloat(pr_tax_val);
    $('#tax_amount_'+ find_attr).val(parseFloat(final_tax));
    $('#ttax2_'+ find_attr).text(parseFloat(final_tax));
    $('#ttax2_old_'+ find_attr).text(parseFloat(final_tax));    
});

<?php
if($discount_select['dine'] != 0){
?>
$(document).ready(function(e) {
    $('.order_discount_input').trigger('change');
});
<?php
}
?>

<?php
if($Settings->customer_discount=='customer') { ?>

$(document).ready(function(e) {
    var dis_id = $('.order_discount_input').val();   
    var dis_id = "<?php $this->site->CheckCustomerDiscountAppliedBySplitID($split_id)?>";     
    if(dis_id!=null){
        $('.order_discount_input').trigger('change');
    }    
});
<?php 
}
?>

$(document).on('change', '.order_discount_input', function () {

    $("#order_discount_input_seletedtext").val('');
    $this_obj = $(this);
    var find_attr = $(this).attr('count');
    var subtotal  = 0;
    var tax_amount  = $('#tax_amount_'+ find_attr).val();
    var taxtype  = $('#tax_type_'+ find_attr).val();    
    var unit_price = 0;
    var off_discount = $('#off_discount_'+ find_attr).val() ? $('#off_discount_'+ find_attr).val() : 0;
    var discount = $('#item_discounts_'+ find_attr).val() ? $('#item_discounts_'+ find_attr).val() : 0;
    var manual_discount_amount = 0.00;//$('#manual_discount_amount_'+ find_attr).val();
    // var manual_discount_amount = $('#manual_discount_amount_'+ find_attr).val();
    
    if(typeof off_discount == "undefined" || parseFloat(off_discount) == 0)
    { 
        discount = parseFloat(discount)+parseFloat(manual_discount_amount);
        subtotal  = $('#subtotal_'+ find_attr).val();
        unit_price = parseFloat($('#item_dis_'+ find_attr).val());        
    }
    else{
        discount = parseFloat(discount)+parseFloat(off_discount)+parseFloat(manual_discount_amount);
        subtotal  = $('#subtotal_'+ find_attr).val();
        unit_price = parseFloat($('#offer_discount_'+ find_attr).val());
    }



// alert(manual_discount_amount);

    //if ($('#order_discount_input_'+find_attr).val()!='' && $('#max-allow-discount-percent_'+find_attr).length>0 && $('#max-allow-discount-percent_'+find_attr).val()!='') {
    //    $val = $('#max-allow-discount-percent_'+find_attr).val();
    //    $gtotal = subtotal;//$('#grand_total_'+find_attr).val();
    //    $disval = $('#order_discount_input_'+find_attr).val();
    //
    //    if ($disval.indexOf('%')!=-1) {
    //        if ($val<parseFloat($disval)) { bootbox.alert('Discount should not be Greater than '+$val+'%'); $('#order_discount_input_'+find_attr).val('');return false; }
    //    }else{
    //        $f_val = ($disval*100)/$gtotal;
    //        
    //        if ($val<$f_val) { bootbox.alert('Discount should not be Greater than '+$val+'%');$('#order_discount_input_'+find_attr).val(''); return false; }
    //    }
    //}

    var ds = $('#order_discount_input_'+ find_attr).val() ? $('#order_discount_input_'+ find_attr).val() : '0';

    var pr_tax = $('#ptax_'+find_attr).children(":selected").data("id");


var values = [];
$("input[name='item_offer_dis[]']").each(function() {
    values.push($(this).val());
});

 var off_discounts = []; 
    $('input[name="item_offer_dis[]"]').each(function(){
     
      off_discounts.push($(this).val());
    });

var item_quantity = [];
// split[1][quantity][]
$("input[name='split["+ find_attr+"][quantity][]']").each(function() {
    item_quantity.push($(this).val());
});
item_quantity = item_quantity.join(',');
// alert(quantity);
 var recipeids  = $('#recipeids_'+ find_attr).val();
 var recipeidvariantdataids  = $('#recipeidvariantdataids_'+ find_attr).val();
 var recipevariantids  = $('#recipevariantids_'+ find_attr).val();
var recipeqtys  = item_quantity;
// var recipeqtys  = $('#recipeqtys_'+ find_attr).val();

var manualitemdis = [];
    $('.manual_item_discount').each(function(){
        manualitemdis.push($(this).val());
    });
    manualitemdis = manualitemdis.join(',');

var addonsubtotal = [];
$('.addonsubtotal').each(function(){
    addonsubtotal.push($(this).val());
});
addonsubtotal = addonsubtotal.join(',');



var off_discounts  = off_discounts;
var input_discount = 0;
var divide = "<?php echo $bils;?>";
$split_id ="<?php echo $split_id;?>";
$customer_id ="<?php echo $order_data['customer_id']; ?>";
$table_id ="<?php echo $table_id; ?>";
<?php if($Settings->customer_discount=='customer') : ?>
if(ds !=0){
    
    $("#order_discount_input_seletedtext").val($(this).find('option:selected').text());
        $.ajax({
                type: 'POST',
                url: '<?= base_url('pos/pos/calculate_customerdiscount');?>',
                dataType: "json",
                 async : false,
                data: {
                    recipeids: recipeids,recipevariantids: recipevariantids,recipeqtys: recipeqtys,manualitemdis: manualitemdis,addonsubtotal: addonsubtotal,discountid:$this_obj.val(),divide: divide,split_id:$split_id,customer_id:$customer_id,table_id:$table_id
                },
                success: function (data) {
                   $.each( data, function( index, value ){  
                   $manual_item_discount_per_val = $('#manual_item_discount_per_val'+value.id).val();                   
                    input_discount += value.disamt;
                    if(value.disamt != 0){                        
                        $('#item_cus_dis'+value.id).text(value.discount_val);
                        $('#item_cus_dis-'+value.id).val(value.disamt);
                        $('#manual_and_customer_discount_consolid_percentage_display_option'+value.id).text((parseInt($manual_item_discount_per_val) + parseInt(value.discount_val)));
						$('.recipe-item-discount-'+value.id).text(value.disamt);
                    }else{
                        $('#item_cus_dis'+value.id).text('0');
                        $('#item_cus_dis-'+value.id).val(0);
						$('.recipe-item-discount-'+value.id).text(0);
                        $('#manual_and_customer_discount_consolid_percentage_display_option'+value.id).text((parseInt($manual_item_discount_per_val) + parseInt(0)));
                    }                    
                });                    
                }
           });
       

}else{
    
    $recipeids = recipeids.split(',');
    $recipeidvariantdataids = recipeidvariantdataids.split(',');
    
    $.each($recipeidvariantdataids,function(i,v){console.log(v);
        $('.item_cus_dis').text('0');
        $('[id^=recipe-input-discount-]').val('');
        $manual_item_discount_per_val = $('#manual_item_discount_per_val'+v).val(); 

        $('#manual_and_customer_discount_consolid_percentage_display_option'+v).text((parseInt($manual_item_discount_per_val) ));

        $t_discount = parseFloat($('#recipe-item-discount-'+v).val()) + parseFloat($('#recipe-offer-discount-'+v).val());
        $('#recipe-total-discount-'+v).val($t_discount);
    });
   
} 
<?php elseif($Settings->customer_discount=='manual') : ?>
if (ds.indexOf("%") !== -1) {            
    var pds = ds.split("%");
    if (!isNaN(pds[0])) {
    input_discount = parseFloat(((unit_price) * parseFloat(pds[0])) / 100);
    } else {
    input_discount = parseFloat(ds);
    }
}
else{            
    input_discount = parseFloat(ds);
}
<?php endif;?>
var final_discount =  parseFloat(input_discount)+parseFloat(discount);
var final_amount = parseFloat(subtotal) - parseFloat(final_discount);
var final_discount =  parseFloat(input_discount)+parseFloat(discount);
var birthday = 0;
<?php 
if(!empty($order_data)){
    $custimerid = $order_data['customer_id']; 
  }  
?>     
var check = <?php echo json_encode($this->site->Check_birthday_discount_isavail($custimerid )); ?>;     
var bdydis = <?php echo json_encode($this->pos_settings->birthday_discount); ?>;  
if(check != 0){    
    var disbirthday = parseFloat(subtotal) - (parseFloat(final_discount));
        birthday = parseFloat(((disbirthday) * parseFloat(bdydis)) / 100);        
    var final_amount = parseFloat(subtotal) - parseFloat(final_discount)-parseFloat(birthday);
    var final_amount_before_input =(parseFloat(subtotal)) - parseFloat(final_discount)-parseFloat(birthday);
}else{   
    var final_amount = parseFloat(final_amount);    
    var final_amount_before_input =(parseFloat(subtotal)) - (parseFloat(discount));
} 
/*service charge*/
var service_charge_val = 0;
<?php
if($this->pos_settings->default_service_charge !=0 && $this->pos_settings->service_charge_option !=0){  ?>
var servicecharge = $('#service_charge_'+find_attr).children(":selected").data("id");
    if (servicecharge !== null && servicecharge != 0) {
        $.each(service_charge, function () {                       
        service_charge_val = parseFloat(((final_amount) * parseFloat(servicecharge)) / 100);        
        });
    }
<?php  } ?>
/*service charge*/
var pr_tax_val = 0;
if (pr_tax !== null && pr_tax != 0) {
    $.each(tax_rates, function () {                       
    pr_tax_val = parseFloat(((final_amount) * parseFloat(pr_tax)) / 100);    
    pr_tax_rate = (pr_tax) + '%';                
    });
}
    var final_tax;
    var final_tax_amount;   
    if(taxtype != 0){
        final_tax = parseFloat(pr_tax_val);
        final_tax_amount = parseFloat(final_tax);
        final_amount = parseFloat(final_amount+final_tax+service_charge_val);
        finalamount = parseFloat(final_amount);
        sub_val = parseFloat(finalamount);
    }else{   
        sub_val = final_amount/((pr_tax_val/final_amount)+1);        
        final_tax_amount = sub_val * (pr_tax / 100);
        final_amount = sub_val+final_tax_amount+service_charge_val; 

    }
   
if(final_amount >= 0 ){
    $('#tdis_'+ find_attr).val(formatDecimal(input_discount));
    $('#tds_'+ find_attr).text(formatMoney(input_discount));
    $('.birthday_discount_'+ find_attr).text(formatDecimal(birthday));
    $('.birthday_discount_'+ find_attr).val(formatDecimal(birthday));
    $('#ttax2_'+ find_attr).text(formatMoney(final_tax_amount));
    $('#tax_amount_'+ find_attr).val(formatDecimal(final_tax_amount));
    $('#gtotal_'+ find_attr).text(formatMoney(final_amount));
    $('#grand_total_'+ find_attr).val(formatDecimal(final_amount));
    $('#round_total_'+ find_attr).val(formatDecimal(final_amount));
    $('#service_amount_'+ find_attr).val(formatDecimal(service_charge_val));
    $('#spansericechargeamt_'+ find_attr).text(formatMoney(service_charge_val));
    manualdis1(find_attr);

}else{
    bootbox.alert('Discount should not grater than total', function(){
        location.reload(); 
     });
}
});

    $(document).on('keyup','.comment_price',function(){
        var comment_price = $(this).val();
        var find_attr = $(this).attr('count');                    
        var item_qty = $(this).parent().siblings().find(".quantity").val();            
        var recipe_id = $(this).parent().parent().find(".recipe_id").val();         
        var unit_price = $(this).parent().siblings().find(".unit_price").val();         
        var addonsubtotal = $(this).parent().siblings().find(".addonsubtotal").val(); 
        var manualds = $(this).parent().siblings().find(".manual_item_discount_val").val() ? $(this).parent().siblings().find(".manual_item_discount_val").val() : 0; 
        var item_cus_dis_val = $(this).parent().siblings().find(".item_cus_dis_val").text() ? $(this).parent().siblings().find(".item_cus_dis_val").text() : 0;        
        $sub =parseFloat(comment_price)+parseFloat(unit_price*item_qty)+parseFloat(addonsubtotal);
        var manual_item_ds = 0;
            if(manualds != 0){
            if (manualds.indexOf("%") !== -1) {
                var manualpds = manualds.split("%");
                if (!isNaN(manualpds[0])) {
                   manual_item_ds = formatDecimal((parseFloat(($sub * parseFloat(manualpds[0])) / 100)), 4);
                    
                } else {
                    manual_item_ds = formatDecimal(manualds);
                }
            } else {
                 manual_item_ds = formatDecimal(manualds);
            } } else{
                manual_item_ds = formatDecimal(manualds);
            }  
        $(this).parent().find('.manual_item_discount').val(manual_item_ds); 
        var sub_total = (parseFloat(unit_price*item_qty)+parseFloat(addonsubtotal)+parseFloat(comment_price)-parseFloat(manual_item_ds));
         if(parseFloat(sub_total) <= 0 ){
                 bootbox.alert('Discount should not grater than Subtotal', function(){
                location.reload();
                });
            }
             $(this).parent().find('.manual_item_discount').val(manual_item_ds); 
                var per = 0;
                if (manualds.indexOf('%') !== -1) {
                  per = manualds.replace("%", "");                  
                } else {
                    per = (manualds / unit_price*item_qty) * 100;                                      
                }
                var ds = $('#recipe-item-discount-type-'+recipe_id).val();
                var itemdisperval = $('#recipe-item-discount-val-'+recipe_id).val();
                    if (ds != '') {                        
                        if (ds == 'percentage_discount') {                            
                            recipe_item_discount = parseFloat((( (parseFloat(comment_price)+parseFloat(unit_price*item_qty)+parseFloat(addonsubtotal)) - manual_item_ds) * parseFloat(itemdisperval)) / 100);
                            
                        } else {
                            recipe_item_discount = parseFloat(itemdisperval);
                        }
                    } else {
                        recipe_item_discount = parseFloat(itemdisperval);
                    }

            $(this).parent().parent().find('#recipe-item-discount-'+recipe_id).val(formatDecimal(recipe_item_discount)); 
            $(this).parent().parent().find('.recipe-item-discount-'+recipe_id).text(formatDecimal(recipe_item_discount)); 
            $(this).parent().parent().find('.item_subtotal').text(formatMoney(sub_total)); 
            $(this).parent().parent().find('.discounted_subtotal').val(formatDecimal(sub_total)); 
            $(this).parent().parent().find('.item_subtotal1').val(formatDecimal(unit_price*item_qty+parseFloat(addonsubtotal)+parseFloat(comment_price))); 
            $(this).parent().parent().find('.manual_item_discount_per_val').val(formatDecimal(per));                
            $(this).parent().parent().find('.manual_and_customer_discount_consolid_percentage_display_option').text((parseInt(per) + parseInt(item_cus_dis_val)));  
            $(this).val($(this).val());  
            manualdis(find_attr);         
    });
    $(document).on('click','.minus',function(){
    $original_qty = parseInt($(this).closest('.qty_number').find('.original_quantity').val());    
    $cnt = parseInt($(this).closest('.qty_number').find('.quantity').val()) - parseInt(1);
    $order_item_id = $(this).closest('.qty_number').find('.order_item_id').val();     
    $split_id ="<?php echo $split_id;?>";
    $action = 'minus';
	if($cnt<1){
		return false;
	}

    $msg = 'Are you sure want to Decrease Quantity?';
                bootbox.confirm({
            message: $msg,
            buttons: {
                confirm: {
                label: 'Yes',
                className: 'btn-success'
                },
                cancel: {
                label: 'No',
                className: 'btn-danger'
                }
            },
            callback: function (result) {               
                if (result) {               
                $.ajax({
                    type: "get",
                    url:"<?= base_url('/pos/pos/sale_item_qty_adjustment');?>",                
                    data: {order_item_id: $order_item_id, action: $action, split_id: $split_id},
                    dataType: "json",
                    contentType: false,                           
                    success: function (data) {                        
                    if(data.msg == 'success'){        
                      location.reload(); 
                    }else{
                        alert('Something is wrong please');
                    }
                    }    
                }).done(function () {
                });
                }
            }
            });
});

$(document).on('click','.plus',function(){
     $order_item_id = $(this).closest('.qty_number').find('.order_item_id').val();     
     $split_id ="<?php echo $split_id;?>";
     $action = 'plus';
     $msg = 'Are you sure want to Increase Quantity?';
                bootbox.confirm({
            message: $msg,
            buttons: {
                confirm: {
                label: 'Yes',
                className: 'btn-success'
                },
                cancel: {
                label: 'No',
                className: 'btn-danger'
                }
            },
            callback: function (result) {               
                if (result) {               
                $.ajax({
                    type: "get",
                    url:"<?= base_url('pos/pos/sale_item_qty_adjustment');?>",                
                    data: {order_item_id: $order_item_id, action: $action, split_id: $split_id},
                    dataType: "json",
                    // cache : false, 
                    contentType: false,                           
                    // processData : false,
                    success: function (data) {                        
                    if(data.msg == 'success'){        
                        location.reload();                                         
                    }else{
                        alert('Something is wrong please');
                    }
                    }    
                }).done(function () {
                });
                }
            }
            });
});

    $('.manual_item_discount_val').each(function () {   
    $(this).change(function () {    
    var find_attr = $(this).attr('count');                    
       var item_qty = $(this).parent().siblings().find(".quantity").val();         
       var recipe_id = $(this).parent().siblings().find(".recipe_id").val();         
       var recipe_id = $(this).parent().siblings().find(".recipe_id").val();         
       var unit_price = $(this).parent().siblings().find(".unit_price").val();         
       var addonsubtotal = $(this).parent().siblings().find(".addonsubtotal").val(); 
       var comment_price = $(this).parent().siblings().find(".comment_price").val() ? $(this).parent().siblings().find(".comment_price").val() : 0; 
       var item_cus_dis_val = $(this).parent().siblings().find(".item_cus_dis_val").text() ? $(this).parent().siblings().find(".item_cus_dis_val").text() : 0;  
        var manualds = $(this).val();
        var manual_item_ds = 0;
            if(manualds != 0){
            if (manualds.indexOf("%") !== -1) {
                var manualpds = manualds.split("%");
                if (!isNaN(manualpds[0])) {
                    manual_item_ds = formatDecimal((parseFloat((((unit_price*item_qty)+parseFloat(addonsubtotal)+parseFloat(comment_price)) * parseFloat(manualpds[0])) / 100)), 4);
                    
                } else {
                    manual_item_ds = formatDecimal(manualds);
                }
            } else {
                 manual_item_ds = formatDecimal(manualds);
            } } else{
                manual_item_ds = formatDecimal(manualds);
            }  
            var sub_total = (parseFloat(unit_price*item_qty)+parseFloat(addonsubtotal)+parseFloat(comment_price)-parseFloat(manual_item_ds));
             if(parseFloat(sub_total) <= 0 && unit_price>0){
                 bootbox.alert('Discount should not grater than Subtotal', function(){
                location.reload();
                });
            }
             $(this).parent().find('.manual_item_discount').val(manual_item_ds); 
                var per = 0;
                if (manualds.indexOf('%') !== -1) {
                  per = manualds.replace("%", "");                  
                } else {
                    per = (manualds / unit_price*item_qty) * 100;                                      
                }
                var ds = $('#recipe-item-discount-type-'+recipe_id).val();
                var itemdisperval = $('#recipe-item-discount-val-'+recipe_id).val();
                    if (ds != '') {                        
                        if (ds == 'percentage_discount') {
                            recipe_item_discount = parseFloat(((unit_price*item_qty - manual_item_ds) * parseFloat(itemdisperval)) / 100);
                        } else {
                            recipe_item_discount = parseFloat(itemdisperval);
                        }
                    } else {
                        recipe_item_discount = parseFloat(itemdisperval);
                    }
            $(this).parent().parent().find('#recipe-item-discount-'+recipe_id).val(formatDecimal(recipe_item_discount)); 
            $(this).parent().parent().find('.recipe-item-discount-'+recipe_id).text(formatDecimal(recipe_item_discount)); 
            $(this).parent().parent().find('.item_subtotal').text(formatMoney(sub_total)); 
            $(this).parent().parent().find('.discounted_subtotal').val(formatDecimal(sub_total)); 
            $(this).parent().parent().find('.item_subtotal1').val(formatDecimal(unit_price*item_qty+parseFloat(addonsubtotal)+parseFloat(comment_price))); 
            $(this).parent().parent().find('.manual_item_discount_per_val').val(formatDecimal(per));                
             $(this).parent().parent().find('.manual_and_customer_discount_consolid_percentage_display_option').text((parseInt(per) + parseInt(item_cus_dis_val)));   
            $(this).val($(this).val());  
            manualdis(find_attr);
            
    });    
});

function manualdis(find_attr){
    var sum = 0;
    var item_discount = 0;
    $('.item_discount').each(function(){
        item_discount += parseFloat(this.value);
    });
    $(".itemdiscounts").text(formatMoney(item_discount));
    var itemdiscounts = $(".itemdiscounts").val(formatDecimal(item_discount));

    var item_subtotal1 = 0;
    var all_item_total = 0;
    $('.discounted_subtotal').each(function(){
        all_item_total += (($(this).parent().siblings().find(".quantity").val()) * $(this).parent().siblings().find(".unit_price").val());
        item_subtotal1 += parseFloat(this.value);
    });
     var addonsubtotal = 0;
    $('.addonsubtotal').each(function(){
        addonsubtotal += parseFloat(this.value);
    });

    var commentsubtotal = 0;
    $('.comment_price').each(function(){
        commentsubtotal += parseFloat(this.value);
    });    

    $(".total_manual_discount_amount").val(formatDecimal(sum));
    $(".total_manual_discount_amount").text(formatMoney(sum));
    var total_price_textbox = item_subtotal1;
    
    var after_manual_dis = total_price_textbox - sum;    
    $(".after_manual_dis_textbox").val(formatDecimal(after_manual_dis));

    var after_manual_dis_textbox = after_manual_dis;//$(".after_manual_dis_textbox").val();    
    var itemdiscounts = $(".itemdiscounts").val();
    var after_manual_dis = after_manual_dis_textbox -itemdiscounts; 
    $(".after_item_or_manual_dis").text(formatMoney(item_subtotal1-itemdiscounts));
    $(".after_item_or_manual_dis_textbox").val(formatDecimal(item_subtotal1-itemdiscounts));    
    $(".total_price").text(formatMoney(item_subtotal1));
    $(".total_price").val(formatDecimal(item_subtotal1));
    $(".all_item_total").val(formatDecimal((all_item_total+addonsubtotal+commentsubtotal)));
    
    $total_price = $("#subtotal_"+find_attr).val();
    $item_dis = $("#item_dis_"+find_attr).val();
    var tota_ds = $('.tot_dis_value').val() ? $('.tot_dis_value').val() : '0';
    if (tota_ds.indexOf("%") !== -1) {
        var pds = tota_ds.split("%");
        if (!isNaN(pds[0])) {
            total_discount = parseFloat((($item_dis) * parseFloat(pds[0])) / 100);
        } else {
            total_discount = parseFloat(tota_ds);
        }
    } else {
        total_discount = parseFloat(tota_ds);
    }       
    $("#offer_discount_"+find_attr).val(formatDecimal(parseFloat($item_dis)-parseFloat(total_discount)));
    $(".subtot_"+find_attr).text(formatMoney(parseFloat($item_dis)-parseFloat(total_discount)));
    $(".subtot_"+find_attr).val(formatDecimal(parseFloat($item_dis)-parseFloat(total_discount)));
    $("#tds1_"+find_attr).text(formatMoney(total_discount));
    $("#off_discount_"+find_attr).val(formatDecimal(total_discount));
    $('.order_discount_input').trigger('change');            
}

function manualdis1(find_attr){
    var sum = 0;
    $('.manual_item_discount').each(function(){
        sum += parseFloat(this.value);
    });

    var item_discount = 0;
    $('.item_discount').each(function(){
        item_discount += parseFloat(this.value);
    });

    $(".itemdiscounts").text(formatMoney(item_discount));
    var itemdiscounts = $(".itemdiscounts").val(formatDecimal(item_discount));

    var item_subtotal1 = 0;
    $('.discounted_subtotal').each(function(){
        item_subtotal1 += parseFloat(this.value);
    });

    $(".total_manual_discount_amount").val(formatDecimal(sum));
    $(".total_manual_discount_amount").text(formatMoney(sum));
    var total_price_textbox = item_subtotal1;
    
    var after_manual_dis = total_price_textbox - sum;    
    $(".after_manual_dis_textbox").val(formatDecimal(after_manual_dis));

    var after_manual_dis_textbox = after_manual_dis;//$(".after_manual_dis_textbox").val();    
    var itemdiscounts = $(".itemdiscounts").val();
    var after_manual_dis = after_manual_dis_textbox -itemdiscounts; 
    $(".after_item_or_manual_dis").text(formatMoney(item_subtotal1-itemdiscounts));
    $(".after_item_or_manual_dis_textbox").val(formatDecimal(item_subtotal1-itemdiscounts));    
    $(".total_price").text(formatMoney(item_subtotal1));
    $(".total_price").val(formatDecimal(item_subtotal1));
   
    
    $total_price = $("#subtotal_"+find_attr).val();
    $item_dis = $("#item_dis_"+find_attr).val();

    var tota_ds = $('.tot_dis_value').val() ? $('.tot_dis_value').val() : '0';
    if (tota_ds.indexOf("%") !== -1) {
        var pds = tota_ds.split("%");
        if (!isNaN(pds[0])) {
            total_discount = parseFloat((($item_dis) * parseFloat(pds[0])) / 100);
        } else {
            total_discount = parseFloat(tota_ds);
        }
    } else {
        total_discount = parseFloat(tota_ds);
    }  
     // alert(parseFloat($item_dis)-parseFloat(total_discount)-parseFloat($item_customer_dis));
    $item_customer_dis = $("#tdis_"+find_attr).val();     
    $("#offer_discount_"+find_attr).val(formatDecimal(parseFloat($item_dis)-parseFloat(total_discount)));
    $(".subtot_"+find_attr).text(formatMoney(parseFloat($item_dis)-parseFloat(total_discount)-parseFloat($item_customer_dis)));
    $(".subtot_"+find_attr).val(formatDecimal(parseFloat($item_dis)-parseFloat(total_discount)-parseFloat($item_customer_dis)));
    $("#tds1_"+find_attr).text(formatMoney(total_discount));
    $("#off_discount_"+find_attr).val(formatDecimal(total_discount));
}

$(document).on("focus", '.manual_item_discount_val', function (e) {
  var element = $(this)[0];
    var len = $(this).val().length * 2;
        element.setSelectionRange(len, len);
		if($(this).val() <=0){
			$(this).val('');
		} 
    }).on("click", '.manual_item_discount_val', function (e) {
        $(this).val($(this).val());
        $(this).focus();
    });
function formatDecimal(x, d) {

if (!d) { d = 2; }
return parseFloat(accounting.formatNumber(x, d, '', '.'));

}
</script>
<script type="text/javascript" src="<?=$assets?>js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/perfect-scrollbar.min.js"></script>
<!-- <script type="text/javascript" src="<?=$assets?>pos/js/pos.ajax.js"></script> -->
<script type="text/javascript" src="<?=$assets?>js/dragscrollable.js"></script>
<script type="text/javascript" src="<?=$assets?>js/dragscroll.js"></script>
<script type="text/javascript" src="<?=$assets?>js/jquery.mCustomScrollbar.concat.min.js"></script>  
<script>
$('.kb-pad').keyboard({
restrictInput: true,
preventPaste: true,
autoAccept: true,
alwaysOpen: false,
openOn: 'click',
usePreview: false,
layout: 'custom',
maxLength: 12,
display: {
'b': '\u2190:Backspace',
},
customLayout: {
'default': [
'1 2 3 4',
'5 6 7 8  ',
'9 0 % {b}',
' {accept} {cancel}'
]
}
});
$('.kb-pad-qty').keyboard({
        restrictInput: true,
	     css: {
		 container: 'number-keyboard'
	     },
        preventPaste: true,
        autoAccept: true,
        alwaysOpen: false,
        openOn: 'click',
        usePreview: false,
        layout: 'custom',
        maxlength:4,
        display: {
            'b': '\u2190:Backspace',
        },
      customLayout: {
            'default': [
            '1 2 3 {b}',
            '4 5 6 . {clear}',
            '7 8 9 0 %',
            '{accept} {cancel}'
            ]
        }
    });
	$('.kb_pad_length').keyboard({
        restrictInput: true,
        preventPaste: true,
        autoAccept: true,
        alwaysOpen: false,
        openOn: 'click',
        usePreview: false,
        layout: 'custom',
        maxLength: 10,
        display: {
            'b': '\u2190:Backspace',
        }, 
        maxLength : 20,
        customLayout: {
            'default': [
            '1 2 3 4',
            '5 6 7 8  ',
            ' 9 0 - {b}',
            ' {accept} {cancel}'
            ]
        },
        
    });
$('.kb_pad_exp').keyboard({
        restrictInput: true,
        preventPaste: true,
        autoAccept: true,
        alwaysOpen: false,
        openOn: 'click',
        usePreview: false,
        layout: 'custom',
        maxLength: 10,
        display: {
            'b': '\u2190:Backspace',
        }, 
        maxLength : 6,
        customLayout: {
            'default': [
            '1 2 3 4',
            '5 6 7 8  ',
            ' 9 0 / - ',
            ' {accept} {cancel}'
            ]
        },
        
    });
	  $('.kb-text').keyboard({
        autoAccept: true,
        alwaysOpen: false,
        openOn: 'focus',
        usePreview: false,
        layout: 'custom',
        //layout: 'qwerty',
        display: {
            'bksp': "\u2190",
            'accept': 'return',
            'default': 'ABC',
            'meta1': '123',
            'meta2': '#+='
        },
        customLayout: {
            'default': [
            'q w e r t y u i o p {bksp}',
            'a s d f g h j k l {enter}',
            '{s} z x c v b n m , . {s}',
            '{meta1} {space} {cancel} {accept}'
            ],
            'shift': [
            'Q W E R T Y U I O P {bksp}',
            'A S D F G H J K L {enter}',
            '{s} Z X C V B N M / ? {s}',
            '{meta1} {space} {meta1} {accept}'
            ],
            'meta1': [
            '1 2 3 4 5 6 7 8 9 0 {bksp}',
            '- / : ; ( ) \u20ac & @ {enter}',
            '{meta2} . , ? ! \' " {meta2}',
            '{default} {space} {default} {accept}'
            ],
            'meta2': [
            '[ ] { } # % ^ * + = {bksp}',
            '_ \\ | &lt; &gt; $ \u00a3 \u00a5 {enter}',
            '{meta1} ~ . , ? ! \' " {meta1}',
            '{default} {space} {default} {accept}'
            ]}
        });
$('.kb-pad1').keyboard({
        restrictInput: true,
    css: {
        container: 'number-keyboard'
    },
        preventPaste: true,
        autoAccept: true,
        alwaysOpen: false,
        openOn: 'click',
        usePreview: false,
        layout: 'custom',
        display: {
            'b': '\u2190:Backspace',
        },
        customLayout: {
            'default': [
            '1 2 3 {b}',
            '4 5 6 . {clear}',
            '7 8 9 0 %',
            '{accept} {cancel}'
            ]
        }
    });

$("#delivery_person_id").select2();
</script>
<?php
if ( ! $pos_settings->remote_printing) {
?>
<script type="text/javascript">
var order_printers = <?= json_encode($order_printers); ?>;
function printOrder() {
$.each(order_printers, function() {
var socket_data = { 'printer': this,
'logo': (biller && biller.logo ? biller.logo : ''),
'text': order_data };
$.get('<?= admin_url('pos/p/order'); ?>', {data: JSON.stringify(socket_data)});
});
return false;
}

function printBill() {
var socket_data = {
'printer': <?= json_encode($printer); ?>,
'logo': (biller && biller.logo ? biller.logo : ''),
'text': bill_data
};
$.get('<?= admin_url('pos/p'); ?>', {data: JSON.stringify(socket_data)});
return false;
}
</script>
<?php
} elseif ($pos_settings->remote_printing == 2) {
?>
<script src="<?= $assets ?>js/socket.io.min.js" type="text/javascript"></script>
<script type="text/javascript">
socket = io.connect('http://localhost:6440', {'reconnection': false});

function printBill() {
if (socket.connected) {
var socket_data = {'printer': <?= json_encode($printer); ?>, 'text': bill_data};
socket.emit('print-now', socket_data);
return false;
} else {
bootbox.alert('<?= lang('pos_print_error'); ?>');
return false;
}
}

var order_printers = <?= json_encode($order_printers); ?>;
function printOrder() {
if (socket.connected) {
$.each(order_printers, function() {
var socket_data = {'printer': this, 'text': order_data};
socket.emit('print-now', socket_data);
});
return false;
} else {
bootbox.alert('<?= lang('pos_print_error'); ?>');
return false;
}
}
</script>
<?php

} elseif ($pos_settings->remote_printing == 3) {

?>
<script type="text/javascript">
try {
socket = new WebSocket('<?php echo PRINTER_SOCKET; ?>');
socket.onopen = function () {
console.log('Connected');
return;
};
socket.onclose = function () {
console.log('Not Connected');
return;
};
} catch (e) {
console.log(e);
}

var order_printers = <?= $pos_settings->local_printers ? "''" : json_encode($order_printers); ?>;
function printOrder() {
if (socket.readyState == 1) {

if (order_printers == '') {

var socket_data = { 'printer': false, 'order': true,
'logo': (biller && biller.logo ? site.base_url+'assets/uploads/logos/'+biller.logo : ''),
'text': order_data };
socket.send(JSON.stringify({type: 'print-receipt', data: socket_data}));

} else {

$.each(order_printers, function() {
var socket_data = { 'printer': this,
'logo': (biller && biller.logo ? site.base_url+'assets/uploads/logos/'+biller.logo : ''),
'text': order_data };
socket.send(JSON.stringify({type: 'print-receipt', data: socket_data}));
});

}
return false;
} else {
bootbox.alert('<?= lang('pos_print_error'); ?>');
return false;
}
}

function printBill() {
if (socket.readyState == 1) {
var socket_data = {
'printer': <?= $pos_settings->local_printers ? "''" : json_encode($printer); ?>,
'logo': (biller && biller.logo ? site.base_url+'assets/uploads/logos/'+biller.logo : ''),
'text': bill_data
};
socket.send(JSON.stringify({type: 'print-receipt', data: socket_data}));
return false;
} else {
bootbox.alert('<?= lang('pos_print_error'); ?>');
return false;
}
}
</script>
<?php
}
?>
<script>
	<?php   if(count($sales_row->bils)==1){  ?>
				$('.taxation_settings').show();
                $('.post-rough-tender').remove();
			       <?php if($pos_settings->discount_popup_screen_in_payment == 0) {?>
				   
				     $thisObj = $('.request_bil_new');    
                    payment_popup($thisObj);
					calculateTotalsbill();
					$(".amounts").trigger("keydown");
				   <?php   }else{   ?>
		$('document').ready(function(){
		    $thisObj = $('.request_bil_new');    
			var billid = $('.billid').val();
			var ordersplit = $(this).parents('.payment-list-container').find('.order_split').val();
			var salesid = $(this).parents('.payment-list-container').find('.salesid').val(); 
            var grandtotal = $(this).parents('.payment-list-container').find('.grandtotal').val(); 
            var customer_id = $(this).parents('.payment-list-container').find('.customer-id').val();
			var loyalty_available = $(this).parents('.payment-list-container').find('.loyalty_available').val();
            $('.customer_id').val(customer_id);            
            $('.loyaltyavailable').val(loyalty_available);            
			var count = $(this).parents('.payment-list-container').find('.totalitems').val();
			$url = '<?= base_url().'pos/pos/DINEINcheckCustomerDiscount'?>';
			$.ajax({
			url: $url,
			type: "POST",
			data: {bill_id:billid},
			dataType: "json",
			success:function(data){
			    if (data.unique_discount == 0) {
                 if("<?php echo $Settings->customer_discount ?>" == "customer" ) {            
    			 $dropdown = '<select id="choose-discount">';
    			 $dropdown +='<option value="0">No Discount</option>';
				$.each( data.all_dis, function( index, value ){
				    $selected = (data.cus_dis.customer_discount_id == value.id)?"selected='selected'":"";
				    $dropdown +='<option value="'+value.id+'" '+$selected+'>'+value.name+'</option>';
				});
				$dropdown +='</select>';
				if (data.cus_dis.customer_discount_id != 0) {
					$msg = "<div id='discount-confirm-msg'>Do You want to apply any discount?</div>";
				} else {
					$msg = "<div id='discount-confirm-msg'>Do You want to apply any discount?</div>";
				}
				    bootbox.confirm({
					message: $dropdown+$msg,					
					 buttons: {
					     confirm: {
						 label: 'Apply',
						 className: 'btn-success'
                         
					     },
					     cancel: {
						 label: 'Cancel',
						 className: 'btn-danger'
					     }
					 },
					 callback: function (result) {					   
					     if (result) {
						dis_id  = $('#choose-discount').val();
						 $.ajax({
						     url: '<?=admin_url().'pos/DINEINupdateBillDetails'?>',
						     type: "GET",
						     data: {bill_id:billid,dis_id:dis_id},
						     dataType: "json",
						     success:function(data){
								 if (!data.no_discount) {
									 $thisObj.parents('.payment-list-container').find('.grandtotal').val(data.amount);
									 $thisObj.siblings('.grandtotal_req').val(data.amount);
									 payment_popup($thisObj);
								 }else{
									payment_popup($thisObj);
								 }
						     }
						 });
					     }else{  
						 	payment_popup($thisObj);
						}
					     
					 }
				     });
				    return false;
			       
			    }else{ 
                    payment_popup($thisObj);
                }
                }else{ 
                    payment_popup($thisObj);
                }
			}
		    });
			
			});

	<?php  } } ?>
  $('#paymentModal').on('shown.bs.modal', function(e) {
	//  console.log('popuptrigger');
        $('#userd_tender_list').html('');
        var loyalty_available = $('.loyaltyavailable').val();          
       
        $('#payment-cash').val('cash');

	if (rt_loyalty!='' && rt_loyalty!=undefined) {
	    $('#payment-loyalty').trigger('click');                  
            $('#payment-loyalty').addClass('active');
	    
	}
	if (rt_credit!='' && rt_credit!=undefined) {
	    $('#payment-credit').trigger('click');                  
            $('#payment-credit').addClass('active');   
	}
	if($('#payment-cash').val() == 'cash'){
            $('#payment-cash').trigger('click');                  
            $('#payment-cash').addClass('active');   
        }
 	if (rt_cc!='' && rt_cc!=undefined) {
	    $('#payment-CC').trigger('click');                  
            $('#payment-CC').addClass('active');   
	} 
        });
	
 $(document).on('click', '#reset_payment', function () {    
      $('#userd_tender_list').html('');
      $('.crd_exp,.cc_no').val('');      
      $(".amounts").val('');
      $('.amounts').trigger('blur');
      calculateTotalsbill();
});
$('.crd_exp').datetimepicker({format: 'mm/yyyy', fontAwesome: true, todayBtn: 1, autoclose: 1, minView: 3,startDate: new Date(),viewMode : 'months',startView: "year", 
    minViewMode: "months" });
 $(document).on('click', '.payment_type', function () {  
                $index = $( this ).attr('data-index');                
                $data_id = $( this ).attr('data_id');                
                <?php
                foreach($currency as $currency_row){
                    $default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
                    if($currency_row->code == $default_currency_data->code){
                    ?>
                    // $('#amount_<?php echo $currency_row->code; ?>_'+$index).val('');
                    $('#amount_<?php echo $currency_row->code; ?>_'+$index).attr('readonly', false);
                    <?php
                    }else{
                    ?>
                    // $('#amount_<?php echo $currency_row->code; ?>_'+$index).val('');
                    $('#amount_<?php echo $currency_row->code; ?>_'+$index).attr('readonly', false);            
                    <?php
                    }
                } 
               ?>  
        
                var p_val = $(this).val(),
                id = $(this).attr('id'),
                pa_no = id.substr(id.length - 1);            
                $('#rpaidby').val(p_val);
            if (p_val == 'cash') {    
                $('.payment_type.active').removeClass('active');
                $('#payment-cash').addClass('active');  
                $('#amount_<?php echo $currency_row->code; ?>_'+$index).attr('readonly', false);  
				
                $('.cash').show();   
				$('.wallets').hide(); 				
                $('#base_currency_'+ $index).show();   
                $('#multi_currency_'+ $index).show();
                $('#lc_'+ $index).hide();                 
                $('#CC_'+ $index).hide(); 
				$('#ws_'+ $index).hide();
                $('.credit').hide();   
                $('.loyalty').hide();  
                $('.CC').hide();    
				$('#nk_'+ $index).hide();		
				$('#nk_nc_kot').hide();
                <?php
                foreach($currency as $currency_row){
                $default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
                if($currency_row->code == $default_currency_data->code){
                ?>
                $('#amount_<?php echo $currency_row->code ?>_'+$index).focus();
                <?php
                }else{ ?>
                <?php } }
                ?>     
            } else if (p_val == 'credit') {       
                $('.payment_type.active').removeClass('active');
                $('#payment-'+$index).addClass('active');  
                $('#amount_<?php echo $currency_row->code; ?>_'+$index).attr('readonly', false);    
                $('.credit').show();    
				$('.wallets').hide(); 
				 $('#nk_'+ $index).hide();		
                $('#base_currency_'+ $index).show();           
                $('#multi_currency_'+ $index).hide();
                $('#lc_'+ $index).hide();                 
                $('#CC_'+ $index).hide();
				$('#ws_'+ $index).hide();
                $('.CC').hide();    
                $('.loyalty').hide(); 
                $('.cash').hide(); 
				$('#nk_nc_kot').hide();
                <?php
                foreach($currency as $currency_row){
                $default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
                if($currency_row->code == $default_currency_data->code){
                ?>
                $('#amount_<?php echo $currency_row->code ?>_' + $index).focus();
                <?php
                }else{
                ?>
                <?php
                }
                }
                ?>                              
            }else if (p_val == 'wallets') {       
                $('.payment_type.active').removeClass('active');
                $('#payment-'+$index).addClass('active');  
                $('#amount_<?php echo $currency_row->code; ?>_'+$index).attr('readonly', false);   
                $('.wallets').show(); 
                $('#ws_'+ $index).show();	
				$('.credit').hide();		
				$('#nk_'+ $index).hide();						
                $('#base_currency_'+ $index).show();           
                $('#multi_currency_'+ $index).hide();
                $('#lc_'+ $index).hide();                 
                $('#CC_'+ $index).hide();
                $('.CC').hide();    
                $('.loyalty').hide(); 
                $('.cash').hide(); 
				$('#nk_nc_kot').hide();
                <?php
                foreach($currency as $currency_row){
                $default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
                if($currency_row->code == $default_currency_data->code){
                ?>
                $('#amount_<?php echo $currency_row->code ?>_' + $index).focus();
                <?php
                }else{
                ?>
                <?php
                }
                }
                ?>                              
            } else if (p_val == 'CC') {  
                $('.payment_type.active').removeClass('active');
                $('#payment-'+$index).addClass('active');
                 $('.CC').show();     
                 $('#CC_'+ $index).show();   
				 $('.wallets').hide(); 		
				 $('#nk_'+ $index).hide();						 
                 $('#base_currency_'+ $index).show();                      
                 $('#multi_currency_'+ $index).hide();
                 $('#lc_'+ $index).hide();
				 $('#ws_'+ $index).hide();
                 $('.credit').hide(); 
                 $('.loyalty').hide(); 
                 $('.cash').hide();
				$('#nk_nc_kot').hide();				 
				 
                <?php
                    foreach($currency as $currency_row){
                        $default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
                        if($currency_row->code == $default_currency_data->code){
                        ?>
                        $('#amount_<?php echo $currency_row->code ?>_' + $index).focus();
                        <?php
                        }else{
                        ?>                    
                        <?php
                        }
                    }
                ?>
            }else if (p_val == 'nc_kot') {   
			    $("#reset_payment").trigger("click");
                $('.payment_type.active').removeClass('active');
                $('#payment-'+$index).addClass('active');  
                $('#amount_<?php echo $currency_row->code; ?>_'+$index).attr('readonly', false);
				$('#amount_<?php echo $currency_row->code; ?>_'+$index).val($(".total").val());
				$('#amount_<?php echo $currency_row->code; ?>_'+$index).trigger("blur");				
                $('#ws_'+ $index).hide();
				$('.wallets').hide(); 
                $('#nk_'+ $index).show();
				
				
				$('.credit').hide();				
                $('#base_currency_'+ $index).show();           
                $('#multi_currency_'+ $index).hide();
                $('#lc_'+ $index).hide();                 
                $('#CC_'+ $index).hide();
                $('.CC').hide();    
                $('.loyalty').hide(); 
                $('.cash').hide(); 
                <?php
                foreach($currency as $currency_row){
                $default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
                if($currency_row->code == $default_currency_data->code){
                ?>
                $('#amount_<?php echo $currency_row->code ?>_' + $index).focus();
                <?php
                }else{
                ?>
                <?php
                }
                }
                ?>                              
            } else if (p_val == 'loyalty') {
                $('.payment_type.active').removeClass('active');
                $('#payment-'+$index).addClass('active');
                 $('.loyalty').show();               
                 $('#lc_'+ $index).show();   
				 $('#nk_'+ $index).hide();					 
                 $('#base_currency_'+ $index).show();
                 $('#multi_currency_'+ $index).hide();
                 $('#CC_'+ $index).hide();                 
                 $('.credit').hide(); 
                 $('.CC').hide(); 
                 $('.cash').hide(); 
$('#nk_nc_kot').hide();
                $('#amount_<?php echo $currency_row->code; ?>_'+$index).focus();
                $('#amount_<?php echo $currency_row->code; ?>_'+$index).attr('readonly', true);
                $('#loyalty_points_' + $index).focus();
                $('#loyaltypoints').val(0);                
                $('#lc_details_' + $index).html(''); 
                $('#lc_reduem_' + $index).html('');

                var loyalty_customer_id = $('#loyalty_customer').val();
                // alert(loyalty_customer_id);

                var customer_id ='';
                var bill_customer_id = $('.customer_id').val(); 
                if(loyalty_customer_id){
                    customer_id = loyalty_customer_id;
                }else{
                    customer_id =  bill_customer_id;
                }
                // alert(bill_customer_id);
                var payid = $(this).attr('id'),
                    id = payid.substr(payid.length - 1);
                if (customer_id != '') {
                    $.ajax({
                        type: "get", async: false,
                        url: site.base_url + "pos/get_loyalty_points/" + customer_id,
                        dataType: "json",
                        success: function (data) { 

                            if (data.points === false && data.redemption === 0) {     
                            
                                $('#loyalty_points_' + $index).parent('.form-group').addClass('has-error');
                                bootbox.alert('Not Eligible To use Loyalty Card.');
                            } else if ((data.points.total_points == 0) || (data.points.loyalty_card_no == '')) { 
                                
                                bootbox.alert('Right Now Not Eligible to Loyalty,Please try after some visit.');
                                ('#lc_details_' + $index).html(''); 
                                $('#lc_reduem_' + $index).html(''); 
                            } else {          
                                                          
                                $('#loyaltypoints').val(data.points.total_points);
                                $('#lc_details_' + $index).html('<small>Card No: ' + data.points.loyalty_card_no + '&nbsp;&nbsp;Value: ' + data.points.total_points +'</small>'); 

                                $('#lc_reduem_' + $index).html('<small>Redemption: ' + parseFloat(data.redemption.redempoint) + '&nbsp;&nbsp;Amount: ' + parseFloat(data.redemption.amount) +'</small>'); 

                                $('#loyalty_points_' + $index).parent('.form-group').removeClass('has-error');                                 
                            }
                        }
                    });
                }               
             }        
            
             var currentYear = new Date().getFullYear();  
                for (var i = 1; i <= 20; i++ ) {
                    $(".pcc_year").append(

                        $("<option></option>")
                            .attr("value", currentYear)
                            .text(currentYear)

                    );
                    currentYear++;
                }
           $(this).parents('.payment').find('input').val('');
            $('#pcc_month_'+$index).prepend('<option value="">Month</option>');
            $('#pcc_year_'+$index).prepend('<option value="">Year</option>');
            $('#pcc_month_'+$index).val('');
            $('#pcc_year_'+$index).val('');            
            $amount = $('#balance_amt_<?=$default_currency_data->code?>').val();              
            $amount = parseFloat($amount.match(/([0-9]*\.[0-9]+|[0-9]+)/));
            $('#amount_<?=$default_currency_data->code?>_'+$index).removeClass('credit-max');
            $(this).parent('.form-group').find('.available-c-limit').remove();
            if ($( this ).val()=='credit') {                
                $('#amount_<?=$default_currency_data->code?>_'+$index).addClass('credit-max');
                $inputCredit = 0;
                $('.credit-max').each(function(n,v){
                if($(this).attr('id')!="amount_<?=$default_currency_data->code?>_"+$index){
                    $inputCredit += ($(this).val()=='')?0:parseFloat($(this).val());
                }
                });
                $creditlimit = parseFloat($('#credit_limit').val())-parseFloat($inputCredit);                
             if ($('#customer_type').val()=='none'){
                bootbox.alert("Not allowed to use Credit option");
                $(this).parent('.form-group > .available-c-limit').empty();
                return false;
                }

                if($('#customer_type').val()=='prepaid' && $amount>$creditlimit){
                $amount = $creditlimit;
                }
                $amount = ($amount!=0)?$amount:'';
                $(this).parent('.form-group').append('<span class="available-c-limit">Available Customer Credit Limit $'+$('#credit_limit').val()+'</span>');
            }

            $('#amount_<?=$default_currency_data->code?>_'+$index).removeClass('creditcard-max');            
            if ($( this ).val()=='CC') {                
                    $('#amount_<?=$default_currency_data->code?>_'+$index).addClass('creditcard-max');
                    $inputCreditcard = 0;

                    $('.creditcard-max').each(function(n,v){
                        if($(this).attr('id')!="amount_<?=$default_currency_data->code?>_"+$index){
                            $inputCreditcard += ($(this).val()=='')?0:parseFloat($(this).val());
                        }
                    });                
            }

                // $('#amount_USD_cash').val('');
		
                if ((p_val != 'loyalty') && (p_val == 'cash')) {                    
                  if($amount>0){                    
                     if($('#amount_<?=$default_currency_data->code?>_cash').val() == ''){
			if (rt_cash!='' && rt_cash!=undefined) {
			    $amount = rt_cash;
                      }
		          $('#amount_<?=$default_currency_data->code?>_cash').val($amount);
                    }
		  }
                }else if ((p_val != 'loyalty') && (p_val == 'CC')) {                    
                  if($amount>0){
		        if (rt_cc!='' && rt_cc!=undefined) {
			    $amount = rt_cc;
		        }
               if($('#amount_<?=$default_currency_data->code?>_CC').val() == ''){
			   if (rt_cc!='' && rt_cc!=undefined) {
			    $amount = rt_cc;
			   }
                 $('#amount_<?=$default_currency_data->code?>_CC').val($amount);
			
                      }
                    }
                }else if (p_val == 'loyalty'){
                    $('#loyalty_points_cash').focus();
		    if (rt_loyalty!='' && rt_loyalty!=undefined) {
			    $('#loyalty_points_loyalty').val(rt_loyalty);
			    $('.loyalty_points').trigger('change');
		    }
		    
                } else{
		    if (rt_credit!='' && rt_credit!=undefined) {
			    $('#amount_<?=$default_currency_data->code?>_credit').val(rt_credit);
			    
		    }
		}
		$('.amount').trigger('blur');
        });
		
		
		
		
$('#subcategory-list, #scroller').dragscrollable({
dragSelector: 'button', 
acceptPropagatedEvent: false
});

 $(document).on('change', '.loyalty_points', function () {
            var loyaltypoints = $("#loyaltypoints").val();             
            var redemption = $(this).val() ? $(this).val() : 0;
            var customer_id = $("#customer_id").val();    
            $('#loyalty_used_points').val(0);             
            var payid = $(this).attr('idd'); 
            if(parseFloat(loyaltypoints) == 0){    
                 bootbox.alert('Gift card number is incorrect or expired.');    
                 $('#loyalty_points_' + payid).focus().val('');            
             }else if (parseFloat(redemption) <= parseFloat(loyaltypoints)) {
                $bal_amount = $('#balance_amt_<?=$default_currency_data->code?>').val();
                 $bal_amount = parseFloat($bal_amount.match(/([0-9]*\.[0-9]+|[0-9]+)/));
                    $.ajax({
                        type: "get", async: false,
                        url: site.base_url + "pos/validate_loyalty_card/",
                        dataType: "json",
                         data: {
                            redemption: redemption,                        
                            customer_id: customer_id,
                            bal_amount: $bal_amount,
                        }, 
                        success: function (data) {
                            if (data === false) {          
                                 bootbox.alert('Right Now Not Eligible to use this card number,Please try after some visit.');
                                 $('#loyalty_points_' + payid).focus().val('');                                  
                                 $('#amount_<?php echo $currency_row->code; ?>_'+payid).val('');
                            } else if(parseFloat(data.total_redemamount) > parseFloat($bal_amount)) {      
                                    bootbox.alert('Already seleted in other payment method Plz check it (OR) use only Blance amount only.');
                                    $('#lc_reduem_' + payid).html('<small>Redemption: ' + parseFloat(data.redemption) + '&nbsp;&nbsp;Amount: ' + parseFloat(data.amount) +'</small>');
                                    $('#loyalty_points_' + payid).focus().val('');
                                    $('#amount_<?php echo $currency_row->code; ?>_'+payid).val('');
                               }else{                                                                                  
                                    // $('#loyalty_points_' + id).parent('.form-group').removeClass('has-error');
                                    $('#lc_reduem_' + payid).html('<small>Redemption: ' + parseFloat(data.redemption) + '&nbsp;&nbsp;Amount: ' + parseFloat(data.amount) +'</small>'); 
                                    $('#loyalty_used_points').val(redemption); 
                                    $('#amount_<?php echo $currency_row->code; ?>_'+payid).focus().val(data.total_redemamount);
                                    $('#amount_<?php echo $currency_row->code; ?>_'+payid).attr('readonly', true);
                              }
                        }
                    });
                }else{
                    
                    bootbox.alert('Please Enter less than your points or equal.');  
                     $('#loyalty_points_' + payid).focus().val('');
                     $('#amount_<?php echo $currency_row->code; ?>_'+payid).val('');
                    
                }           
        });

        
        var customer_id = $('.customer_id').val();  
        $('#loyalty_customer').val(customer_id).select2({
            
            minimumInputLength: 1,
            data: [],
            initSelection: function (element, callback) {
                $.ajax({
                    type: "get", async: false,
                    url: "<?=admin_url('customers/getCustomer')?>/" + $(element).val(),
                    dataType: "json",
                    success: function (data) {
                        callback(data[0]);
                    }
                });
            },
            ajax: {
                url: site.base_url + "pos/loyalty_customer",
                dataType: 'json',
                quietMillis: 15,
                data: function (term, page) {
                    return {
                        term: term,
                        limit: 1
                    };
                },
                results: function (data, page) {
                    if (data.results != null) {                        
                        return {results: data.results};
                    } else {
                        return {results: [{id: '', text: 'No Match Found'}]};
                    }
                }
            }
        });
		
if (KB) {
     display_keyboards();
            var result = false, sct = '';
            $('#loyalty_customer').on('select2-opening', function () {
                sct = '';
                $('.select2-input').addClass('kb-pad-all');  
                display_keyboards();       
                $('select, .select').select2('destroy');    
                // alert($(this).next().parent().parent().html());                    
               /*   $('input[name="default"]').addClass('kbtext');*/
                $('.select2-input').bind('change.keyboard', function (e, keyboard, el) {
                    
                    if (el && el.value != '' && el.value.length > 0 && sct != el.value) {
                        sct = el.value;
                    }
                    if(!el && sct.length > 0) {
                        $('.select2-input').addClass('select2-active');
                         // setTimeout(function() {
                            $.ajax({
                                type: "get",
                                async: false,
                                url: "<?=admin_url('pos/loyalty_customer')?>/?term=" + sct,
                                dataType: "json",
                                success: function (res) {
                                    if (res.results != null) {                                                          
                                        // $('#loyalty_customer').select2({data: res}).select2('open');
                                        $('.select2-input').removeClass('select2-active');
                                    } else {
                                         bootbox.alert('no_match_found');
                                        $('#loyalty_customer').select2('close');
                                        // $('#test').click();
                                    }
                                }
                            });
                         // }, 500);
                    }
                });
                

            });

            $('#loyalty_customer').on('select2-close', function () {
                $('.select2-input').removeClass('kb-pad-all');                
                $('#test').click();
                $('select, .select').select2('destroy');
                $('select, .select').select2({minimumResultsForSearch: 7});
            });
            $(document).bind('click', '#test', function () {
                var kb = $('#test').keyboard().getkeyboard();
                kb.close();
            });
        }    


        /*if($('.ui-keyboard-keyset').is(':visible')) {
            alert();
        }*/


        /*$(document).on('.ui-keyboard-keyset:visible', function () {
            alert();
        });*/
        
        // $("#loyalty_customer").on("change", function (e) {
            // $(document).on('change', '#loyalty_customer', function () {
        $(document).on('change',"#loyalty_customer", function () {
            var loyalty_customer_id = $(this).val();
            var myVar = $('.payment_type.active').val();               
            if(myVar =='loyalty'){
            var customer_id = loyalty_customer_id;  
                var payid = $(this).attr('id'),
                    id = payid.substr(payid.length - 1);
                if (customer_id != '') {
                    $.ajax({
                        type: "get", async: false,
                        url: site.base_url + "pos/get_loyalty_points/" + customer_id,
                        dataType: "json",
                        success: function (data) {                             
                            if (data === false) {
                                $('#loyalty_points_' + $index).parent('.form-group').addClass('has-error');
                                bootbox.alert('Not Eligible To use Loyalty Points.');
                            } else if ((data.points.total_points == 0) || (data.points.loyalty_card_no == '')) {
                                bootbox.alert('Right Now Not Eligible to Loyalty,Please try after some visit.');
                                ('#lc_details_' + $index).html(''); 
                                $('#lc_reduem_' + $index).html('');
                            } else {
                                
                                $('#loyaltypoints').val(data.points.total_points);
                                $('#lc_details_' + $index).html('<small>Card No: ' + data.points.loyalty_card_no + '&nbsp;&nbsp;Value: ' + data.points.total_points +'</small>'); 
                                $('#lc_reduem_' + $index).html('<small>Redemption: ' + parseFloat(data.redemption.redempoint) + '&nbsp;&nbsp;Amount: ' + parseFloat(data.redemption.amount) +'</small>'); 
                                $('#loyalty_points_' + $index).parent('.form-group').removeClass('has-error');                                 
                            }
                        }
                    });               
               }
            }
        });    

		
        function payment_popup($thisObj){
            $('#paymentModal').css('overflow-y', 'scroll');
	        $('#paymentModal').appendTo("body").modal('show', {backdrop: 'static', keyboard: false});  
            $('.balance_amount').val('');
            $(".amounts").val('');
            var billid = $thisObj.siblings('.billid').val(); 
          //  console.log(billid)			;
            var customer_type = $thisObj.siblings('.customer-type').val(); 
            var company_id = $thisObj.siblings('.company-id').val();
	        var allow_loyalty = $thisObj.siblings('.customer-allow-loyalty').val(); 
            var ordersplit = $thisObj.siblings('.order_split').val();
            var salesid = $thisObj.siblings('.salesid').val(); 
            var grandtotal = $thisObj.siblings('.grandtotal').val();
            var credit_limit = $thisObj.siblings('.credit-limit').val();
			//console.log(grandtotal)			;
	       // rough tender start//
	        rt_cash = '';rt_credit='';rt_cc='';rt_loyalty='';
	        rt_cash = $thisObj.siblings('.rt-cash').val(); 
            rt_credit = $thisObj.siblings('.rt-credit').val();
            rt_cc = $thisObj.siblings('.rt-CC').val();
	        rt_loyalty = $thisObj.siblings('.rt-loyalty').val();
	        // rough tender - end //
			var customer_name = $thisObj.siblings('.customer-name').val();
	        $('#new_customer_name').text(customer_name);
			
	        //console.log(credit_limit)
            var count = $thisObj.siblings('.totalitems').val(); 
            $('.bill_id').val(billid);
            $('.sales_id').val(salesid);
            $('.total').val(grandtotal);
            $('.order_split_id').val(ordersplit);
            $('.credit_limit').val(credit_limit);

            if(allow_loyalty==0){
                 $('#payment-loyalty').hide();
            }
            if (customer_type =='none' || customer_type==undefined || customer_type==0 ) {                
                $('#payment-credit').hide();
            }
			
            // $('.loyalty_available').val(loyalty_available);
            $('.customer_type').val(customer_type);
	        $('.company_id').val(company_id);

            var twt = formatDecimal(grandtotal);
            $('#bill_amount').text(formatMoney(grandtotal));
	      //  console.log('grandtotal-'+grandtotal)
          //  console.log('bil-'+billid);
            if (count == 0) {
                bootbox.alert('<?=lang('x_total');?>');
                return false;
            }
			
			<?php
            $exchange_curr_code = $this->site->getExchangeCurrency($this->Settings->default_currency);
            $exchange_rate = $this->site->getExchangeRatey($this->Settings->default_currency);
			foreach($currency as $currency_row){
		      	$default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
        			if($currency_row->code == $default_currency_data->code){
        			?>
					
        			 gtotal_<?php echo $currency_row->code; ?> = (parseFloat(twt) / parseFloat(<?php echo $currency_row->rate; ?>)) * <?php echo $default_currency_data->rate; ?>;
					 
                    var decimals = twt - Math.floor(twt);
                    decimals = decimals.toFixed(2);
                    var currency_id = <?php echo $currency_row->id;?>;
                    var  $exchange_curr_code = "<?php echo $exchange_curr_code;?>"; 
                    var  $exchange_rate = "<?php echo $exchange_rate;?>";                    
                    if(currency_id == "<?php echo $this->Settings->default_currency;?>" && $exchange_rate != '' ){
                        $decimals = decimals/ $exchange_rate;
                        $decimals =  Math.round($decimals / 100) * 100;
                         var $riel = '('+$exchange_curr_code+($decimals)+')';
                    }
                    else{
                        var $riel = '';
                    }
                     $space=" ";
                    $('#twt_<?php echo $currency_row->code; ?>').text(formatMoney(gtotal_<?php echo $currency_row->code; ?>)+$space+$riel);

        			 $('#paid_amt_<?php echo $currency_row->code; ?>').val(formatDecimal(gtotal_<?php echo $currency_row->code; ?>));
        			<?php
        			}else{ ?>
                var  $exchange_curr_code = "<?php echo $exchange_curr_code;?>"; 
    			gtotal_<?php echo $currency_row->code; ?> = (parseFloat(twt) / parseFloat(<?php echo $currency_row->rate; ?>)) * <?php echo $default_currency_data->rate; ?>;
				
                     $final_amt = twt/ <?php echo $currency_row->rate ?>;
                     $final_amt =  Math.round($final_amt / 100) * 100;
                    $amt =$exchange_curr_code+$final_amt;
                    
    			$('#twt_<?php echo $currency_row->code; ?>').text($amt);
                $('#paid_amt_<?php echo $currency_row->code; ?>').val(formatDecimal(gtotal_<?php echo $currency_row->code; ?>));
    			<?php } ?>
			<?php } ?>
			
            $('#item_count').text(count);
            //$('#paymentModal').appendTo("body").modal('show');
			<?php
			foreach($currency as $currency_row){
			$default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
			if($currency_row->code == $default_currency_data->code){	
			?>
			$('#amount_<?php echo $currency_row->code; ?>_1').focus().val('');
			
			<?php
			}else{
			?>

            $('#amount_<?php echo $currency_row->code; ?>_1').val('');
			<?php
			}
			}
			?>

	    }
        
    $(document).on('click', '.request_bil', function(){
	  $("button").attr("disabled", "disabled");
	  setTimeout(function() {
           $("button").removeAttr("disabled");      
       }, 1000);
		$thisObj = $(this);
		//var billid = $(this).parents('.payment-list-container').find('.billid').val();
		var billid=$(this).attr('data-billid');
        <?php if($pos_settings->discount_popup_screen_in_bill_print == 0) :?>
            requestBill(billid);
            return false;
        <?php endif; ?>  
	    	var ordersplit = $(this).parents('.payment-list-container').find('.order_split').val();
	    	var salesid = $(this).parents('.payment-list-container').find('.salesid').val(); 
		    var grandtotal = $(this).parents('.payment-list-container').find('.grandtotal').val(); 
		    var count = $(this).parents('.payment-list-container').find('.totalitems').val();        
		   $url = '<?= base_url().'pos/pos/checkCustomerDiscount'?>';
		   $.ajax({
			url: $url,
			type: "get",
			data: {bill_id:billid},
			dataType: "json",
			success:function(data){
			    if (data.unique_discount == 0) {			       
			    var discounttype =  "<?php echo $Settings->customer_discount ?>";                
				if("<?php echo $Settings->customer_discount ?>" == "customer" ) {                 
				$dropdown = '<select id="choose-discount">';
				$dropdown +='<option value="0">No Discount</option>';
				$.each( data.all_dis, function( index, value ){
				    $selected = (data.cus_dis.customer_discount_id == value.id)?"selected='selected'":"";
				    $dropdown +='<option value="'+value.id+'" '+$selected+'>'+value.name+'</option>';
				});
				$dropdown +='</select>';
				if (data.cus_dis.customer_discount_id != 0) {
					$msg = "<div id='discount-confirm-msg'>Do You want to apply any discount?</div>";
				} else {
					$msg = "<div id='discount-confirm-msg'>Do You want to apply any discount?</div>";
				}				
				    bootbox.confirm({
					closeButton: true,
					message: $dropdown+$msg,					
					 buttons: {
					     confirm: {
						 label: 'Apply',
						 className: 'btn-success'
                         
					     },
					     cancel: {
						 label: 'Cancel',
						 className: 'btn-danger'
					     }
					 },
					 callback: function (result) {
					   bootbox.hideAll();$('.modal-backdrop').remove();
					     if (result) {
						dis_id  = $('#choose-discount').val();
						 $.ajax({
						     url: '<?=admin_url().'pos/DINEINupdateBillDetails'?>',//updateBillDetails
						     type: "GET",
						     data: {bill_id:billid,dis_id:dis_id},
						     dataType: "json",
						     success:function(data){
							 if (!data.no_discount) {
							     $thisObj.parents('.payment-list-container').find('.grandtotal').val(data.amount);
							     $thisObj.siblings('.grandtotal_req').val(data.amount);
							 }
							       requestBill(billid);
								   //payment_popup($thisObj);
						     }
						 });
					     }else{  requestBill(billid);}
					     
					 }
				     });
				    return false;
			       }else{ requestBill(billid)}
			    }else{ requestBill(billid)}
			    
			}
		    });
			$(this).prop('disabled', true);
	    });
		
			$(document).on('click', '.request_bil_new,.rough-tender-payment', function(){
			$thisObj = $(this);    
		//	console.log($thisObj);
			if ($thisObj.attr('data-item')=="rough-tender") {
			    $('#pos-payment-form').prepend('<input type="hidden" name="rough_tender" value=1 class="post-rough-tender">');
			   // $('.taxation_settings').hide();                
                  <?php if($pos_settings->discount_popup_screen_in_rough_payment == 0) :?>
                    payment_popup($thisObj);
                    return false;
                <?php endif; ?>  

			}else{       
                $('.taxation_settings').show();
                $('.post-rough-tender').remove();
                <?php if($pos_settings->discount_popup_screen_in_payment == 0) :?>
                    payment_popup($thisObj);
                   return false;
               <?php endif; ?>  
			}
		
			var billid = $(this).parents('.payment-list-container').find('.billid').val();
			var ordersplit = $(this).parents('.payment-list-container').find('.order_split').val();
			var salesid = $(this).parents('.payment-list-container').find('.salesid').val(); 
            var grandtotal = $(this).parents('.payment-list-container').find('.grandtotal').val(); 
            var customer_id = $(this).parents('.payment-list-container').find('.customer-id').val();
			var loyalty_available = $(this).parents('.payment-list-container').find('.loyalty_available').val();
            $('.customer_id').val(customer_id);            
            $('.loyaltyavailable').val(loyalty_available);            
			var count = $(this).parents('.payment-list-container').find('.totalitems').val();
			$url = '<?= base_url().'pos/pos/DINEINcheckCustomerDiscount'?>';
			$.ajax({
			url: $url,
			type: "POST",
			data: {bill_id:billid},
			dataType: "json",
			success:function(data){
			    if (data.unique_discount == 0) {
                 if("<?php echo $Settings->customer_discount ?>" == "customer" ) {            
    			 $dropdown = '<select id="choose-discount">';
    			 $dropdown +='<option value="0">No Discount</option>';
				$.each( data.all_dis, function( index, value ){
				    $selected = (data.cus_dis.customer_discount_id == value.id)?"selected='selected'":"";
				    $dropdown +='<option value="'+value.id+'" '+$selected+'>'+value.name+'</option>';
				});
				$dropdown +='</select>';
				if (data.cus_dis.customer_discount_id != 0) {
					$msg = "<div id='discount-confirm-msg'>Do You want to apply \"<span id='discount-name'>"+data.cus_dis.name+"</span>\"?</div>";
				} else {
					$msg = "<div id='discount-confirm-msg'>Do You want to apply any discount?</div>";
				}
				    bootbox.confirm({
					message: $dropdown+$msg,					
					 buttons: {
					     confirm: {
						 label: 'Apply',
						 className: 'btn-success'
                         
					     },
					     cancel: {
						 label: 'Cancel',
						 className: 'btn-danger'
					     }
					 },
					 callback: function (result) {					   
					     if (result) {
						dis_id  = $('#choose-discount').val();
						 $.ajax({
						     url: '<?=admin_url().'pos/DINEINupdateBillDetails'?>',
						     type: "GET",
						     data: {bill_id:billid,dis_id:dis_id},
						     dataType: "json",
						     success:function(data){
								 if (!data.no_discount) {
									 $thisObj.parents('.payment-list-container').find('.grandtotal').val(data.amount);
									 $thisObj.siblings('.grandtotal_req').val(data.amount);
									 payment_popup($thisObj);
								 }else{
									payment_popup($thisObj);
								 }
						     }
						 });
					     }else{  
						 	payment_popup($thisObj);
						}
					     
					 }
				     });
				    return false;
			       
			    }else{ 
                    payment_popup($thisObj);
                }
                }else{ 
                    payment_popup($thisObj);
                }
			}
		    });
		});
		
	    
        $('#paymentModal').on('show.bs.modal', function(e) {
        $('#submit-sale').text('<?=lang('submit');?>').attr('disabled', false);
        });
        $('#paymentModal').on('shown.bs.modal', function(e) {
        $("#loyalty_customer").val(null).trigger("change"); 
	    $("select.paid_by").val("cash").change();
			<?php
			foreach($currency as $currency_row){
    			$default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
    			if($currency_row->code == $default_currency_data->code){	
    			?>
    			<?php  }else{  ?>  $('#amount_<?php echo $currency_row->code; ?>_cash').val('');
    			<?php
    			}
			}
			?>
        });
		
		<?php
		foreach($currency as $currency_row){
		$default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
		if($currency_row->code == $default_currency_data->code){	
		?>
		var pi_<?php echo $currency_row->code; ?> = 'amount_<?php echo $currency_row->code; ?>_1';
		<?php
		}else{
		?>
		var pi_<?php echo $currency_row->code; ?> = 'amount_<?php echo $currency_row->code; ?>_1';
		<?php
		}
		}
		?>
        
        $(document).on('focus', '.amounts', function () {     

			<?php $default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency); ?>
            pi_<?php echo $default_currency_data->code; ?> = $(this).attr('id');
            calculateTotalsbill();
        }).on('blur', '.amounts', function () {
             var inputs = $(".amount_base");
             var arr = $('input[name="pname[]"]').map(function () {
                return this.value; 
            }).get();
            var paid_tenders = '';
             for(var i = 0; i < inputs.length; i++){  
                    if(($.inArray($(inputs[i]).attr('payment-type'),arr)) !== -1){                        
                        $('#userd_tender_'+$(inputs[i]).attr('payment-type')).text($(inputs[i]).attr('payment-type')+' - '+$(inputs[i]).val());
                    }else if($(inputs[i]).val() != 0 && ($.inArray($(inputs[i]).attr('payment-type'),arr)) === -1){                        
                     paid_tenders += '<div type="button" class="btn-prni paid_payments" data-index="cash" data_id="1"><input type="hidden"  name="pname[]" value="'+$(inputs[i]).attr('payment-type')+'"><span style="padding-top:5px" id="userd_tender_'+$(inputs[i]).attr('payment-type')+'">'+$(inputs[i]).attr('payment-type')+' - '+$(inputs[i]).val()+'</span></div>';
                    } else if($(inputs[i]).val() === 0){      
                           $('#userd_tender_'+$(inputs[i]).attr('payment-type')).remove();
                    }
            } 
            $('#userd_tender_list').append(paid_tenders);
            calculateTotalsbill();

        });

/*var arr = $('input[name="pname[]"]').map(function () {
                return this.value; 
            }).get();
var paid_tenders = '';

$(".amount_base").on("blur", function(){
    var sum=0;
    $(".amount_base").each(function(){
        if($(this).val() == 0){
            $('#used_tender_type_'+$(this).attr('payment-type')).remove();
        }else if(($.inArray($(this).attr('payment-type'),arr)) !== -1 ){ 
            $('#userd_tender_'+$(this).attr('payment-type')).text($(this).attr('payment-type')+' - '+$(this).val());
        }else if($(this).val() != 0 && ($.inArray($(this).attr('payment-type'),arr)) === -1){
            paid_tenders += '<div type="button" class="btn-prni used_tender_type" id="used_tender_type_'+$(this).attr('payment-type')+'" data-index="cash" data_id="1"><input type="hidden"  name="pname[]" value="'+$(this).attr('payment-type')+'"><span style="padding-top:5px" id="userd_tender_'+$(this).attr('payment-type')+'">'+$(this).attr('payment-type')+' - '+$(this).val()+'</span></div>';
        }

    });
$('#userd_tender_list').append(paid_tenders);
    
});*/
<?php
 $default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
		?>
		var currency_json = <?php echo json_encode($currency); ?>;
		var default_currency_rate = <?php echo $default_currency_data->rate; ?>;
		var default_currency_code = '<?php echo $default_currency_data->code; ?>'; 
	function calculateTotalsbill() {
	 	var value_amount = 0;
	 	var total_paying = 0;
		var ia = $(".amounts");
		$.each(ia, function (i) {
			var code = $(this).attr('data-code');
			var rate = $(this).attr('data-rate');
			var cost_v = $(this).val();
			var a  = default_currency_code;
			var c  = default_currency_rate;
          // console.log('cost_v'+cost_v);
			//console.log('rate'+rate);
			if(code == default_currency_code){
				value_amount = cost_v;
			}else{
				value_amount = cost_v * rate;
			}
			var this_amount = formatCNum(value_amount ? value_amount : 0);
			total_paying += parseFloat(this_amount);
		});
		
		$('#total_paying').text(formatMoney(total_paying));		
		
		<?php
		foreach($currency as $currency_row){
    		$default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
            $exchange_curr_code = $this->site->getExchangeCurrency($this->Settings->default_currency);
            $exchange_rate = $this->site->getExchangeRatey($this->Settings->default_currency);
            $getExchangeRatecode = $this->site->getExchangeRatecode($this->Settings->default_currency);
    		if($currency_row->code == $default_currency_data->code){ ?>
                var currency_id = <?php echo $currency_row->id;?>;
                var  $exchange_curr_code = "<?php echo $exchange_curr_code;?>"; 
                var  $exchange_rate = "<?php echo $exchange_rate;?>";

                var decimals = formatDecimal((total_paying - (gtotal_<?php echo $currency_row->code; ?> * <?php echo $currency_row->rate; ?>)));
                  decimals = Math.abs(decimals);
                    // decimals = decimals.toFixed(2);
                    decimals = decimals - Math.floor(decimals);
                    decimals = decimals.toFixed(2);

                    /*console.log((decimals));
                    console.log(decimals);*/
             if(currency_id == "<?php echo $this->Settings->default_currency;?>" && $exchange_rate != '' ){
                    $decimals = decimals/ $exchange_rate;
                    $decimals =  Math.round($decimals / 100) * 100;
                     var $riel = '('+$exchange_curr_code+($decimals)+')';
                }
                else{
                    var $riel = '';
                }       

                 $exchange_amt = (total_paying - (gtotal_<?php echo $getExchangeRatecode; ?> * <?php echo $exchange_rate; ?>))/ <?php echo $exchange_rate ?>;
                /* if($exchange_amt < 0){                     
                   total_paying = total_paying+0.01;
                }    */       
			 $space=' ';
			  $bal_usd_bottom=formatDecimal(total_paying - (gtotal_<?php echo $currency_row->code; ?> * <?php echo $currency_row->rate; ?>));
            $('#balance_<?php echo $currency_row->code; ?>').text(formatMoney($bal_usd_bottom) +$space+ $riel);
            $('#balance_amt_<?php echo $currency_row->code; ?>').val(formatDecimal(total_paying - (gtotal_<?php echo $currency_row->code; ?> * <?php echo $currency_row->rate; ?>) ),'<?php echo $currency_row->symbol; ?>');
    		$('.balance_amount').val(formatDecimal(total_paying - (gtotal_<?php echo $currency_row->code; ?> * <?php echo $currency_row->rate; ?>)),'<?php echo $currency_row->symbol; ?>');
    		<?php
    		}else{
                $getExchangesymbol = $this->site->getExchangeCurrency($this->Settings->default_currency);
    		?>
                var $exchange_curr_code = "<?php echo $exchange_curr_code;?>"; 
                $bal_final_amt = (total_paying - (gtotal_<?php echo $currency_row->code; ?> * <?php echo $currency_row->rate; ?>))/ <?php echo $currency_row->rate ?>;
                $bal_final_amt =  Math.round($bal_final_amt / 100) * 100;
                $bal_amt =$exchange_curr_code+$bal_final_amt;
                $('#balance_<?php echo $currency_row->code; ?>').text($bal_amt);

    		// $('#balance_<?php echo $currency_row->code; ?>').text(formatMoney((total_paying - (gtotal_<?php echo $currency_row->code; ?> * <?php echo $currency_row->rate; ?>) ) / <?php echo $currency_row->rate; ?>,'<?php echo $getExchangesymbol; ?>'));
    		
    		<?php }
    		if($currency_row->code == $default_currency_data->code){
    		?>
    		var balance_usd_total_amount = Math.abs((total_paying -  gtotal_<?php echo $currency_row->code; ?> * <?php echo $currency_row->rate; ?>));
    		var balance_usd_remaing_float = balance_usd_total_amount.toString().split(".")[1];
    		//var balance_usd_remaing_float = Math.abs((balance_usd_total_amount - Math.round(balance_usd_total_amount)) );
    		var balance_usd_remaing_float = parseFloat('0.'+balance_usd_remaing_float) / parseFloat($exchange_rate);
    		var balance_USD_KHR = parseFloat(balance_usd_remaing_float);
    		$('#balance_<?=$default_currency_data->code?>_KHR').text(formatMoney(balance_USD_KHR));
    		<?php
    		}	
    	}
		?>
		total_paid = formatDecimal(total_paying);
		grand_total = gtotal_<?php echo $default_currency_data->code; ?>;
		//console.log(formatDecimal(grand_total))
    }      

    <?php if ($pos_settings->tooltips) {echo '$(".pos-tip").tooltip();';} ?>
$(document).on('change', '.credit-max', function () {
    if ($('#customer_type').val()=='prepaid') {	
	$inputCredit = 0;
	$index = $(this).parents('.payment-row').find('select').attr('data-index');	
	$('.credit-max').each(function(n,v){
	    console.log($(this).attr('id')+'=='+"amount_<?=$default_currency_data->code?>_"+$index);
	    if($(this).attr('id')!="amount_<?=$default_currency_data->code?>_"+$index){
		$inputCredit += ($(this).val()=='')?0:parseFloat($(this).val());
	    }
	});	
	$creditlimit = parseFloat($('#credit_limit').val())-parseFloat($inputCredit);	
	if(parseFloat($(this).val())>parseFloat($creditlimit)){$(this).val('');alert('Amount Exceeds credit limit');}
    }    
});		

$(document).on('change', '.creditcard-max', function () {
var balance = $('.balance_amount').val();
var total_check = $('#total').val();
    $inputCreditcard = 0;
    $index = $( this ).attr('payment-type');   
    $('.creditcard-max').each(function(n,v){        
        if($(this).attr('id') =="amount_<?=$default_currency_data->code?>_"+$index){
            $inputCreditcard += ($(this).val()=='')?0:parseFloat($(this).val());
        }
    });     
    var total_check = $('#total').val();
    if(parseFloat(balance)>0){$(this).val('');alert('Amount Exceeds Payable Total');}    
});     
        $(document).on('click', '#submit-sale', function () {
            if (total_paid == 0 || total_paid < grand_total) {
				 bootbox.alert("PAID AMOUNT IS LESS THAN THE PAYABLE AMOUNT.");
                return false;
            } else {
                $('#pos_note').val(localStorage.getItem('posnote'));
                $('#staff_note').val(localStorage.getItem('staffnote'));
                $(this).text('<?=lang('loading');?>').attr('disabled', true);
               $('#pos-payment-form').submit();
            }
        });
    $(document).on('click', '.cancel_bill', function(e) {
        e.preventDefault();
        var cancel_id = $(this).siblings('.cancel_bill_id').val();
        bootbox.confirm(lang.r_u_sure, function(result) {
        if(result == true) {
                $("#sale_id").val('');
                $("#sale_id").val(cancel_id);
                $('#salecancelremarks').val(''); 
                $('#salesCancelorderModal').show();
            }
        });
        return false;
    });
$(document).on('click','#salescancel_orderitem',function(){
     var cancel_remarks = $('#salecancelremarks').val(); 
     var sale_id = $('#sale_id').val(); 
     if($.trim(cancel_remarks) != ''){
        
        $.ajax({
            type: "get",
            url:"<?= base_url('pos/pos/cancel_sale');?>",                
            data: {cancel_remarks: cancel_remarks, sale_id: sale_id},
            dataType: "json",
            success: function (data) {
                if(data.msg == 'success'){
                         $('#salesCancelorderModal').hide(); 
                         window.location.href = "<?= base_url('pos/pos/'); ?>";
                }else{
                    bootbox.alert('<?=lang('uanble_to_cancle');?>');
                    return false;
                }
            }    
        }).done(function () {
          
        });
     }   
});
function exchangeformatMoney(x, symbol) {
    if(!symbol) { symbol = ""; }
    if(site.settings.sac == 1) {
        return (site.settings.display_symbol == 1 ? site.settings.symbol : '') +
            ''+formatSA(parseFloat(x).toFixed(site.settings.exchange_decimals)) +
            (site.settings.display_symbol == 2 ? site.settings.symbol : '');
    }
    var fmoney = accounting.formatMoney(x, symbol, site.settings.exchange_decimals, site.settings.thousands_sep == 0 ? ' ' : site.settings.thousands_sep, site.settings.decimals_sep, "%s%v");
    if(symbol){
       return fmoney; 
    }
    return (site.settings.display_symbol == 1 ? site.settings.symbol : '') +
        fmoney +
        (site.settings.display_symbol == 2 ? site.settings.symbol : '');
}
$(document).on('click', '.closemodal', function () {
    $('#remarks').val('');
    $('#sale_id').val('');
    $('#CancelorderModal').hide(); 
});
var $print_header_space = '<?=$pos_settings->pre_printed_header?>mm';
var $print_footer_space = '<?=$pos_settings->print_footer_space?>mm';
var pre_printed = '<?=$pos_settings->pre_printed_format?>';
		 function requestBill(billid){
            var base_url = '<?php echo base_url(); ?>';            
            if (billid != '') {
                $.ajax({
                    type: 'get',
                    async: false,                    
                    ContentType: "application/json",
                    url: '<?= base_url('pos/pos/gatdata_print_billing');?>',
                    dataType: "json",
                    data: {
                        billid: billid
                    },
                    success: function (data) {
                    if (data != '') {      
                     var bill_totals = '';
                       var bill_head ='' ;
			          if (pre_printed==0) {
                         bill_head += (data.biller.logo != "test") ? '<div id="wrapper1"><div id="receiptData"><div id="receipt-datareceipt-data"><div class="text-center"><img  src='+base_url+'assets/uploads/logos/'+data.biller.logo +' alt="" >': "";

                         <?php if($pos_settings->print_local_language == 1) :?>
                            bill_head += '<h3 style="text-transform:uppercase;">'+data.biller.local_lang_name+'</h3>';
                         <?php endif; ?>   

                         bill_head += '<h3 style="text-transform:uppercase;">'+data.biller.company+'</h3>';
						
                        <?php if($pos_settings->print_local_language == 1) :?>
                            bill_head += '<p>'+data.biller.local_lang_address+'</p>';
                        <?php endif; ?>  

                         bill_head += '<h4 style="font-weight: bold;">'+data.biller.address+"  "+data.biller.city+" "+data.biller.postal_code+"  "+data.biller.state+"  "+data.biller.country+'<br>'+'<?= lang('tel'); ?>'+': '+data.biller.phone+'</h4></div>';
			        }
						 bill_head += '<h3 class="text-center" style="margin-top: 10px">INVOICE</h3>';
                          
                        <?php
                        if($this->Settings->time_format == 12){ ?>
                            var created_on = formatDate(data.inv.created_on);
                        <?php }else {?>
                            var created_on = data.inv.created_on;
                        <?php }
                        ?>
                        function formatDate(date) {
                            var d = new Date(date);
                            var hh = d.getHours();
                            var m = d.getMinutes();
                            var s = d.getSeconds();
                            var dd = "AM";
                            var h = hh;
                            if (h >= 12) {
                                h = hh-12;
                                dd = "PM";
                            }
                            if (h == 0) {
                                h = 12;
                            }
                            m = m<10?"0"+m:m;

                            s = s<10?"0"+s:s;

                            /* if you want 2 digit hours:
                            h = h<10?"0"+h:h; */

                            var pattern = new RegExp("0?"+hh+":"+m+":"+s);

                            var replacement = h+":"+m;
                            /* if you want to add seconds
                            replacement += ":"+s;  */
                            replacement += " "+dd;  
                           // return date.replace(pattern,replacement);
							 return date;
                        }
                        bill_head +='<?= lang('sale_no_ref'); ?>'+': '+data.inv.reference_no+'<br>';
                         bill_head += '<?= lang('sales_person'); ?>'+': '+data.created_by.first_name+' '+data.created_by.last_name+'<br>'+'<?= lang('cashier'); ?>'+': '+data.cashier.first_name+' '+data.cashier.last_name;			 
            			 if(data.billdata.order_type==1){
            			    bill_head +='<br>'+'<?= lang('Table'); ?>'+': '+data.billdata.table_name;
            			 }else{
            			 
            			 }
			             bill_head += '</p>';
                         bill_head += '<p style="margin-top: -10px">'+'<?= lang('customer'); ?>'+': '+data.customer.name+'</p>';
						 
                         if (site.pos_settings.total_covers==1) {
                         bill_head += '<p style="margin-top: -10px">'+'<?= lang('No of Covers'); ?>'+': '+data.billdata.seats+'</p>';
                     }
					 
					    if (site.pos_settings.total_covers==1) {
                         bill_head += '<p style="margin-top: -10px">'+'<?= lang('No of Covers'); ?>'+': '+data.billdata.seats+'</p>';
                     }
					  if (data.floor_print != '' && data.floor_print!=undefined){
						   bill_head += '<p style="margin-top: -10px">'+data.floor_print+'</p>';
					  }
					   if (data.people_print != '' && data.people_print != undefined){
						   bill_head += '<p style="margin-top: -10px">'+data.people_print+'</p>';
					  }
					   if (data.vat_print != '' && data.vat_print != undefined){
						   bill_head += '<p style="margin-top: -10px">'+data.vat_print+'</p>';
					  }
					 
                            if(typeof data.delivery_person  != "undefined"){
							 bill_head += '<p>'+'<?= lang('phone'); ?>'+': '+data.customer.phone+'</p>';
							  bill_head += '<p>'+'<?= lang('delivery_address'); ?>'+': </p>';
							  bill_head += '<address>'+data.customer.address+' <br>'+data.customer.city+' '+data.customer.state+' '+data.customer.country+'<br>Pincode : '+data.customer.postal_code+'</address>';							  
							  bill_head += '<p>'+'<?= lang('delivery_person'); ?>'+': '+data.delivery_person.first_name+' '+data.delivery_person.last_name+' ('+data.delivery_person.user_number+')</p>';
							  bill_head += '<p>'+'<?= lang('phone'); ?>'+': '+data.delivery_person.phone+'</p>';
							 }

                        bill_totals += '<table class="table table-striped table-condensed" style="margin-top: -10px;font-size:14px!important;"><th colspan="2">'+'<?=lang("description");?>'+'</th><th>'+'<?=lang("price");?>'+'</th><th class="text-center">'+'<?=lang("qty");?>'+'</th>';
			    if (site.pos_settings.bill_print_format==2) {
				bill_totals += '<th class="no-border text-right" style="margin-top: -10px">'+'<?=lang("dis(%)");?>'+'</th>';
			    }else{
				if(data.billdata.manual_item_discount != 0){
				    if(site.pos_settings.manual_item_discount_display_option == 1){
				    bill_totals += '<th class="no-border text-rigth" style="margin-top: -10px">'+'<?=lang("dis(%)");?>'+'</th>';
				}else{
				    bill_totals += '<th class="no-border text-right" style="margin-top: -10px">'+'<?=lang("dis");?>'+'</th>';
				}
				}
			    }

                        bill_totals += '<th class="text-right">'+'<?=lang("sub_total");?>'+'</th>';

                            var r =0;
							var get_free_item=0;
							var disc_item=0;
                           $.each(data.billitemdata, function(a,b) {
                            r++;
							var recipe_name;
							<?php
							if($this->Settings->user_language == 'khmer'){
								?>
							if(b.khmer_name != ''){
								recipe_name = b.khmer_name;
							}else{
								recipe_name = b.recipe_name;
							}
							<?php
							}else{
							?>
							recipe_name = b.recipe_name;
							<?php } ?>
							var recipe_variant='';
                            if(b.recipe_variant!='' && b.recipe_variant!= 0){                                
                                recipe_variant = ' - ['+b.recipe_variant+']';
                            }else{                                
                                recipe_variant='';
                            }
                            $underline='';
							if(b.net_unit_price ==0){
							$underline ='underline';
							get_free_item++;
					       	}
							
                            var star ='';                            
                            if(b.star == '' || b.manual_item_discount != 0){
                              star ="";
                            }
                             if(b.manual_item_discount != 0){
                                star ='*';
								disc_item++;
                            }else{
                              star ='';
                            }
                            // bill_totals += '<tr><td colspan="2" class="no-border"><span style="display: inherit;">'+ star+ '</span><span style="display: table-cell;text-align: -webkit-match-parent;text-decoration:'+$underline+'">'+ recipe_name+ ''+ recipe_variant+ ' </span></td><td class="no-border">'+ formatMoney(b.net_unit_price) +'</td><td class="no-border text-center">'+ formatDecimal(b.quantity) +'</td>';

                            bill_totals += '<tr><td colspan="2" class="no-border"><span style="display: inherit;">'+ star+ '</span><span style="display: table-cell;text-align: -webkit-match-parent;text-decoration:'+$underline+'">'+ recipe_name+ ''+ recipe_variant+ ' </span>';

                            if(typeof b.addondetails.length != 'undefined'){ 
                                $addon_i =1;    
                                $itemaddonamt =0;                           
                                $br ='';                               
                                $.each(b.addondetails, function(s,p) {
                                    $itemaddonamt =p.price*p.qty;
                                    $addon_i =1;
                                    if($addon_i > 1) {
                                        $br ='<br>';
                                    }                                    
                                    bill_totals += '<span style="color: red;font-weight: bold;"> '+p.addon_name+'('+parseInt(p.qty)+'X'+p.price+') = '+ formatMoney(p.price*p.qty)+'</span>'+$br+'';
                                    
                                }); 
                             }

                            bill_totals += '</td>';
                            bill_totals += '<td class="no-border">'+ formatMoney(b.net_unit_price) +'</td><td class="no-border text-center">'+ formatDecimal(b.quantity) +'</td>';
                            $cols = "4";
                            $cols1 = "5";
        				    if (site.pos_settings.bill_print_format==2) {
        				    bill_totals += '<td class="no-border text-right">'+ b.customer_discount_val +'</td>';
        				    }else{
        				     if(data.billdata.manual_item_discount != 0){
        					$cols = "5";
        					$cols1 = "6";
        					if(site.pos_settings.manual_item_discount_display_option == 1){
        					bill_totals += '<td class="no-border text-right">'+ Math.floor(b.manual_item_discount_per_val) +'</td>';
        					}else{
        					    bill_totals += '<td class="no-border text-right">'+ formatMoney(b.manual_item_discount) +'</td>';
        					}
        				    }
        				 }
                        if (site.pos_settings.bill_print_format==2) {
						bill_totals += '<td class="no-border text-right">'+ formatMoney(b.subtotal-b.manual_item_discount-b.input_discount) +'</td></tr>';
						}else{
                            bill_totals += '<td class="no-border text-right">'+ formatMoney(b.subtotal-b.manual_item_discount) +'</td></tr>';
							}
                           
                            });
                                $cols = "4";
                                $cols1 = "5";
                               if(data.billdata.manual_item_discount != 0){
                                $cols = "5";
                                $cols1 = "6";
                                }
								if (site.pos_settings.bill_print_format==2) {
								$cols = "5";
                                $cols1 = "6";
								}
							 bill_totals += '<tr class="bold"><th colspan="'+$cols+'" class="text-right">'+'<?=lang("items");?>'+'</th><th  class="text-right">'+formatDecimal(r)+'</th></tr>';
							if (site.pos_settings.bill_print_format==1) {			 
							bill_totals += '<tr class="bold"><th colspan="'+$cols+'"class="text-right">'+'<?=lang("total");?>'+'</th><th   class="text-right">'+formatMoney(data.billdata.total )+'</th></tr>';
							}else{
							bill_totals += '<tr class="bold"><th colspan="'+$cols+'"class="text-right">'+'<?=lang("total");?>'+'</th><th   class="text-right">'+formatMoney(data.billdata.total )+'</th></tr>';
							}
							
							$total_dis_without_manual =  formatDecimal(data.billdata.total_discount - data.billdata.manual_item_discount);
                            if($total_dis_without_manual > 0) {
									if(data.billdata.discount_type == 'manual'){
                                        if(data.discount.discount_val != ''){
                                            var disname = data.billdata.discount_val;
                                        }else{
                                            var disname = '';
                                        } 
										bill_totals += '<tr class="bold"><th class="text-right" colspan="'+$cols+'">'+lang.discount+'('+disname+')</th><th   class="text-right">'+formatMoney($total_dis_without_manual)+'</th></tr>';
										bill_totals += '<tr class="bold"><th class="text-right" colspan="'+$cols+'">Sub Total</th><th   class="text-right">'+formatMoney(data.billdata.total - data.billdata.manual_item_discount - data.billdata.order_discount)+'</th></tr>';
									} else {
                                        if(data.discount){
                                            var disname = data.discount;
                                        }else{
                                            var disname = '';
                                        }
                                    bill_totals += '<tr class="bold"><th class="text-right" colspan="'+$cols+'">'+disname+'</th><th   class="text-right">'+formatMoney($total_dis_without_manual)+'</th></tr>';
									bill_totals += '<tr class="bold"><th class="text-right" colspan="'+$cols+'">Sub Total</th><th   class="text-right">'+formatMoney(data.billdata.total - data.billdata.manual_item_discount - data.billdata.order_discount-$total_dis_without_manual)+'</th></tr>';
									}
                                }
                            if(data.billdata.service_charge_id != 0 && data.billdata.service_charge_amount != 0){
                                bill_totals += '<tr class="bold">';
                                bill_totals += '<th colspan="'+$cols+'" class="text-right" >'+data.billdata.service_charge_display_value+' </th>';
                                    bill_totals += '<th colspan="1" class="text-right" >'+data.billdata.service_charge_amount+' </th>';
                                bill_totals += '</tr>';
                            }                                    
                           <?php if($pos_settings->display_tax==1) : ?>
                            if (data.billdata.tax_rate != 0) {
                                    $taxtype = '<?=lang('tax_exclusive')?> '+ data.billdata.tax_name;
                                if(data.billdata.tax_type==0){
                                       $taxtype = '<?=lang('tax_inclusive')?> '+data.billdata.tax_name;
                                }
                                bill_totals += '<tr class="bold">';
                                bill_totals += '<th colspan="'+$cols+'" class="text-right" ><?php echo $pos_settings->tax_caption; ?>  </th>';
                                <?php if($pos_settings->display_tax_amt==1) : ?>
                                    bill_totals += '<th colspan="1" class="text-right" >'+formatMoney(data.billdata.total_tax)+'  </th>';
                                <?php endif; ?>
                                bill_totals += '</tr>';
                            }
                        <?php endif; ?>      
                       if(data.billdata.tax_type==0){
                        $grandTotal = parseFloat(data.billdata.total) -parseFloat(data.billdata.total_discount) -parseFloat(data.billdata.birthday_discount)  + parseFloat(data.billdata.service_charge_amount);
                       }else{
                        $grandTotal = parseFloat(data.billdata.total) -parseFloat(data.billdata.total_discount) -parseFloat(data.billdata.birthday_discount)  + parseFloat(data.billdata.total_tax) + parseFloat(data.billdata.service_charge_amount);
                       }    
                        $grandTotal =data.billdata.grand_total;
                        $grand_Total =formatMoney(data.billdata.grand_total);
                        var substr = $grand_Total.split('.');
                        $riel =  substr[1]; 
                        var decimals = $grandTotal - Math.floor($grandTotal);
                        decimals = decimals.toFixed(2);
                        <?php
                        $exchange_rate = $this->site->getExchangeRatey($this->Settings->default_currency);
                        if ($pos_settings->print_option == 1) {  
                        $exchange_curr_code = $this->site->getExchangeCurrency($this->Settings->default_currency);    
                        foreach($currency as $currency_row):?>
                        var currency_rate = <?php echo $currency_row->rate;?>;
                        var currency_id = <?php echo $currency_row->id;?>;
                        var  $exchange_rate = "<?php echo $exchange_rate;?>";                    
                        var  $exchange_curr_code = "<?php echo $exchange_curr_code;?>";  
                        var currency_symbol = '<?php echo $currency_row->symbol;?>';                    
                        var grandTotal = formatMoney($grandTotal/currency_rate, currency_symbol);          
                        if(currency_id == "<?php echo $this->Settings->default_currency;?>"){
                            $decimals = decimals/ $exchange_rate;
                            $decimals =  Math.round($decimals / 100) * 100;
                             var $riel = '<br>('+$exchange_curr_code+($decimals)+')';
                        }
                        else{
                            var $riel = '';
                        }
                       bill_totals += '<tr class="bold test_tr"><th colspan="'+$cols+'" class="text-right" style="color:#fff!important;;background-color:#000!important;">'+lang.grand_total;
                       <?php 
                           if($this->Settings->default_currency != $currency_row->id){ ?>
                            // exchange amount 
                                    $final_amt = $grandTotal/ currency_rate;
                                    $final_amt =  Math.round($final_amt / 100) * 100;
                                    bill_totals += '</th><th colspan="2"  class="text-right" style="color:#fff!important;;background-color:#000!important;">'+exchangeformatMoney($final_amt, $exchange_curr_code)+'</th>';

                               <?php  }else{ ?>
                                    $final_amt = $grandTotal/ currency_rate;
                                    bill_totals += '</th><th colspan="2"  class="text-right" style="color:#fff!important;;background-color:#000!important;">'+formatMoney($final_amt, currency_symbol)+''+$riel+'</th>';
                      <?php  } ?>

                       bill_totals += '</tr>';
               
               <?php endforeach; }else{ ?>
                    <?php $default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
                    $exchange_curr_code = $this->site->getExchangeCurrency($this->Settings->default_currency); 

                     ?>
                    var default_currency_rate = <?php echo $default_currency_data->rate; ?>;
                    var currency_symbol = '<?php echo $currency_row->symbol;?>';                    
                    var grandTotal = formatMoney($grandTotal/default_currency_rate, currency_symbol); 
                    var  $exchange_rate = "<?php echo $exchange_rate;?>";                    
                    var  $exchange_curr_code = "<?php echo $exchange_curr_code;?>";

                    bill_totals += '<tr class="bold test_tr" style="background-color:#000!important;color:#fff!important;color-adjust: exact !important;  "><th colspan="'+$cols+'" class="text-right" style="color:#fff!important;background-color:#000!important;color-adjust: exact !important;  ">'+lang.grand_total+'(<?php echo $default_currency_data->code;?>)</th><th colspan="2"  class="text-right" style="color:#fff!important;background-color:#000!important;color-adjust: exact !important;  ">'+formatMoney($grandTotal)+'</th></tr>';
                <?php }?>
                if (data.custom_print != '' && data.custom_print!=undefined){
						   bill_totals += '<p style="margin-top: -10px">'+data.custom_print+'</p>';
					  }
               <?php if($pos_settings->discount_note_display_option == 1){?>
			    console.log(disc_item);
                    if(data.billdata.manual_item_discount != 0 || disc_item !=0){
                       bill_totals += '<tr><th colspan="'+$cols1+'" class="text-left"><small>Marked items are discounted items.</small></th></tr>';
                     }  
					 if(get_free_item !=0){
					 bill_totals += '<tr><th colspan="'+$cols1+'" class="text-left"><small>Underlined Items are complimentary Items.</small></th></tr>';	
					 }	
					if(data.billdata.member_dscount !=0.00){
					 bill_totals += '<tr><th colspan="'+$cols1+'" class="text-left"><small>Membership discount applied.</small></th></tr>';	
					 }						 
                <?php } ?> 
                          if(data.biller.invoice_footer != ''){
                          bill_totals += '<tr><th colspan="'+$cols1+'" class="text-center"><small>'+data.biller.invoice_footer +'</small></th></tr>';
                          }
								bill_totals += '</table>';
                                $('#bill_header').empty();
								$('#bill_header').append(bill_head);
                                $('#bill-total-table').empty();                                
                                $('#bill-total-table').append(bill_totals);
								<?php if($pos_settings->remote_printing == 1){?>
                                PrintDiv($('#bill_tbl').html());  
								<?php }else{?>
									printOrder(data);						
								<?php }?>
                            }
                    } 
                });
            }
		 }
		  <?php
    
    if ($pos_settings->remote_printing == 1) { ?>
        function PrintDiv(data) {
                var mywindow = window.open('', 'sma_pos_print', 'height=500,width=300');
                var is_chrome = Boolean(mywindow.chrome);
                mywindow.document.write('<html><head><title>Print</title>');
                mywindow.document.write("<style type='text/css' media = 'print'>@page {margin: "+$print_header_space+" 5mm "+$print_footer_space+" 5mm;}</style>");
                mywindow.document.write('<link rel="stylesheet" href="<?=$assets?>styles/helpers/bootstrap.min.css" type="text/css" />');
                mywindow.document.write('</head><body >');
                mywindow.document.write(data);
                mywindow.document.write('</body></html>');
               if (is_chrome) {
                 setTimeout(function() { // wait until all resources loaded 
                    mywindow.document.close(); // necessary for IE >= 10
                    mywindow.focus(); // necessary for IE >= 10
                    mywindow.print(); // change window to winPrint
                    mywindow.close(); // change window to winPrint 
                 }, 250);
               } else {
                    mywindow.document.close(); // necessary for IE >= 10
                    mywindow.focus(); // necessary for IE >= 10

                    mywindow.print();
                    mywindow.close(); 
               }

                return true;
            }

    /*function Popup(data) {
	
        var mywindow = window.open('', 'sma_pos_print', 'height=500,width=300');
	
        mywindow.document.write('<html><head><title>Print</title>');
        mywindow.document.write('<link rel="stylesheet" href="<?=$assets?>styles/helpers/bootstrap.min.css" type="text/css" />');
        mywindow.document.write('</head><body >');
        mywindow.document.write(data);
        mywindow.document.write('</body></html>');
        mywindow.print();
        mywindow.close();
        return true;
    }*/
    <?php }
    ?>
	    
		/* $(document).on('click', '#submit-sale', function () {
            var balance = $('.balance_amount').val();
            if (balance >= 0 && balance !='') {   
		      $(this).attr('disabled',true);
                $('#pos-payment-form').submit();
            }
            else{
                
                bootbox.alert("Paid amount is less than the payable amount.");
                return false;
            }  
        }); */
		$(document).on('click', "#new_customer_submit", function(e) {
	var form = $(this);
	var total_check = $('#total').val();
	var eligibity_point  = $('#eligibity_point').val();
	$.ajax({
		type: "POST",
		url: site.base_url + "customers/new_customer",
          //url: "<?=admin_url('customers/new_customer')?>",
		data: $('#new-customer-form').serialize(), // serializes the form's elements.
		dataType: 'json',
		success: function(data){
			if(data.msg == 'error'){
				$('#msg_error').html(data.msg_error);
			}else{
			   $('#new_customer_id').val(data.new_customer_id ? data.new_customer_id : 0);
			   $('#new_customer_name').text(data.name ? data.name : '');
			}
		}
	});
	e.preventDefault(); // avoid to execute the actual submit of the form.
	return false;
});
 $('.mergeclose').click(function () {
               /*  $('#merge_split_id').val('');
                $('#merge_table_id').val('');        */         
                $('#splits-merge-Modal').hide();                 
            });
$(document).on('click','#OrderChangeCustomer',function(){
     var change_split_id = $('#change_split_id').val(); 
      var changed_customer_id =  $("#poscustomer").val();
     if($.trim(changed_customer_id) != '' && $.trim(changed_customer_id) != 0){
        $.ajax({
            type: "POST",
            url:"<?= base_url('pos/pos/change_customer_number');?>",                
            data: {change_split_id: change_split_id, changed_customer_id: changed_customer_id},
            dataType: "json",
            success: function (data) {
                if(data.msg == 'success'){
                         $('#customer-change-Modal').hide(); 
                         location.reload();
                }else{
                    bootbox.alert('<?=lang('uanble_to_cancle');?>');
                    return false;
                }
            }    
        })
     }   
     else{
     	bootbox.alert('<?=lang('please_select_changing_customer');?>');
        return false;
     }
});
</script>
<script>
$(function(){
		$(".table_bill_list_roll").mCustomScrollbar({
			 theme:"dark-3" ,
		});
        });
		$(function(){
		$(".nc_kot_sec").mCustomScrollbar({
			 theme:"dark-3" ,
		});
        });
		<?php   if(count($sales_row->bils)==1){  ?>
$('#userd_tender_list').html('');
 var loyalty_available = $('.loyaltyavailable').val();          
        $('#payment-cash').val('cash');
	if (rt_loyalty!='' && rt_loyalty!=undefined) {
	    $('#payment-loyalty').trigger('click');                  
            $('#payment-loyalty').addClass('active');
	}
	if (rt_credit!='' && rt_credit!=undefined) {
	    $('#payment-credit').trigger('click');                  
            $('#payment-credit').addClass('active');   
	}
	if($('#payment-cash').val() == 'cash'){
            $('#payment-cash').trigger('click');                  
            $('#payment-cash').addClass('active');   
        }
 	if (rt_cc!='' && rt_cc!=undefined) {
	    $('#payment-CC').trigger('click');                  
            $('#payment-CC').addClass('active');   
	} 

		<?php  }  ?>
		<?php  if(!empty($member_dis)){  ?>
		$(".manual-discount").trigger("change");
		<?php  }elseif(!empty($order_data)){ ?>
		$(".manual_item_discount_val").trigger("change");
		<?php   }  ?>
	</script>
	<?php 
echo $this->load->view($this->theme.'pos_v2/shift/shift_popup'); 
?>

</body>
</html>
