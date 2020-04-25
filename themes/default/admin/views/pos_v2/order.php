<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?><!DOCTYPE html>
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
   	<link rel="stylesheet" href="<?=$assets?>styles/helpers/select2.min.css" type="text/css">
   	<link rel="stylesheet" href="<?=$assets?>styles/theme.css" type="text/css"/>
   	<link rel="stylesheet" href="<?=$assets?>styles/palered_theme.css" type="text/css">

<style>
	.tab_sr_s .ui-widget-content{border: none!important;}
	.table_fixed_ls thead tr th:last-child{border-right: none;}
	.btn-group-justified .btn:focus{outline: none;}
	.btn-group-justified .btn-danger,.btn-group-justified .btn-danger:hover, .btn-group-justified .btn-danger:focus,.btn-group-justified .btn-danger:active,.btn-group-justified .btn-danger.active{    background-color: #c9302c;border-color: #c9302c;}
	.tab_sr_s tbody tr td{ font-size:16px;  }
	.table_pay_s_foot tr td{ font-size:18px !important;}
	#posTable{border-top: none!important;}
	.table_item_ls thead tr th{font-size: 14px;}
	.total_payable{font-size: 24px;color: #fff;}
</style>
</head>
<body>

	<section class="pos_bottom_s">
		<div class="container-fluid">
			<div class="row">
			<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12 logo_sec">
				<?php if ($this->Settings->logo3) { ?>
					<a href="<?php echo base_url('pos/pos/'); ?>"><img src="<?=base_url()?>assets/uploads/logos/<?=$this->Settings->logo3?>" alt="<?=$this->Settings->site_name?>" class="sram_table_logo" width="100%" /></a>
			   <?php   } ?>
			</div>
				<div class="col-lg-10 col-md-10 col-sm-10 col-xs-12 text-left header_sec_me">
					<ul class="menu_nav">
						<li>
							<button type="button" class="btn center-block" id="sent_to_kitchen">
								<div class="img_block">
									<img src="<?=$assets?>images/sprite/save_order.png">
									<figcaption>Save order</figcaption>
									<figcaption>រក្សាទុកការកម្ម៉ង</figcaption>
								</div>
							</button>
						</li>
						<li>
						<button class="reset btn center-block" id="reset">
									<div class="img_block">
										<img src="<?=$assets?>images/sprite/invoice.png">
										<figcaption>Cancel Order</figcaption>
										<figcaption>លុបការកម្ម៉ង</figcaption>
									</div>
							</button>
						</li>
						<li>
								<table class="table tab_sr_s">
									<tbody>
										<tr>
										<td>
											<table>
											<?php  if($order_type ==1){  ?>
												<tr>
													<td>Table/តុ</td>
													<td><input type="text" class="form-control" readonly value="<?php echo !empty($tables->name)?$tables->name:'no Table Select';   ?>"></td>
													
													<td>Persons/មនុស្ស</td>
													<td><input type="text" class="form-control kb-pad-qty numberonly persons" placeholder="How Many/ចំនួន"></td>
												</tr>
											<?php   }  ?>
												<tr>
													<td>Customer/ភ្ញៀវ</td>
													<td colspan="3">
                                                      <div class="input-group">
														 <?php
															echo form_input('customer', (isset($_POST['customer']) ? $_POST['customer'] : ""), 'id="poscustomer" data-placeholder="' . $this->lang->line("select") . ' ' . $this->lang->line("customer") . '" required="required" class="form-control kb-text-click pos-input-tip poscustomer" style="width:83%;display:block;"');
														?>					
														<div class="input-group-addon">
													  	<a href="#" id="toogle-customer-read-attr" class="external">
                                            				<i class="fa fa-pencil" id="addIcon" ></i>
                                        				</a>
														</div>
														
														<div class="input-group-addon">
														 <a href="#" id="view-customer" class="external" data-toggle="modal" data-target="#myModal">
                                                                <i class="fa fa-eye" id="addIcon" ></i>
                                                                          </a>
														</div>
														<?php if ($Owner || $Admin || $GP['customers-add']) { ?>
														<div class="input-group-addon">
															  <a href="#" id="add-customer" class="external" data-toggle="modal" data-target="#customerModal">
                                                           <i class="fa fa-plus-circle" id="addIcon" ></i>
                                                                </a>
														</div>
														<?php   }  ?>
													  </div>
													</td>
												</tr>
											</table>
										</td>
									</tr>
									</tbody>
								</table>
						</li>
						<?php  if($order_type !=1){ $current_order_type=!empty($order_type)?$order_type:'1'; ?>
						<li>
							<a href="<?php echo base_url('/pos/pos/split_list/?type='.$current_order_type);?>">
								<figure class="text-center" style="width: 10.9%;">
									<img src="<?=$assets?>images/sprite/order.png">
									<figcaption>Order</figcaption>
									<figcaption>កម្ម៉ង់</figcaption>
								</figure>
							</a>
						</li>
						<li>
							<a href="<?php echo base_url('/pos/pos/invoice_list/?type='.$current_order_type);?>">
								<figure class="text-center"  style="width: 10.9%;">
									<img src="<?=$assets?>images/sprite/invoice.png">
									<figcaption>Invoice</figcaption>
									<figcaption>វិក័យបត្រ័</figcaption>
								</figure>
							</a>
						</li>
						<?php   }   ?>
						<li>
						
						<span class="total_payable">Total Payable : <small class="totalPayable" style="font-size:25px;"> 0.00</small></span>
							
						</li>
						<li>
							<a href="<?php echo base_url('pos/pos');  ?>">
								<figure class="text-center pull-right">
									<div class="img_block">
										<img src="<?=$assets?>images/sprite/back.png">
										<figcaption>Back</figcaption>
										<figcaption>ត្រលប់</figcaption>
									</div>
								</figure>
							</a>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</section>
	<section>
	  <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'id' => 'pos-sale-form');
                    echo form_open("pos/pos/sent_to_kitchen", $attrib);?>
		<div class="container-fluid">
			<div class="row">
				<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
					<div class="head_left_order">
						<table class="table top_left_order">
							<tbody>
								<tr>
									<td>
									     <input type="hidden" name="customer"  id="poscustomer1" >   
                              <div class="no-print">
                                <?php if ($Owner || $Admin || !empty($this->session->userdata('warehouse_id'))) {
                                    ?>
                                    <div class="form-group" style="display:none;">
                                        <?php
                                            $wh[''] = '';
                                                foreach ($warehouses as $warehouse) {
													if($this->session->userdata('warehouse_id') == $warehouse->id){
                                                    	$wh[$warehouse->id] = $warehouse->name;
													}
                                                }
                                                echo form_dropdown('warehouse', $wh, (isset($_POST['warehouse']) ? $_POST['warehouse'] : $this->session->userdata('warehouse_id')), 'id="poswarehouse" class="form-control pos-input-tip" data-placeholder="' . $this->lang->line("select") . ' ' . $this->lang->line("warehouse") . '" required="required" style="width:100%;" ');
                                            ?>
                                    </div>
                                <?php } else {
                                        $warehouse_input = array(
                                            'type' => 'hidden',
                                            'name' => 'warehouse',
                                            'id' => 'poswarehouse',
                                            'value' => $this->session->userdata('warehouse_id'),
                                        );

                                        echo form_input($warehouse_input);
                                    }
                                ?>
								     <?php echo form_input('add_item', '', 'class="form-control pos-tip kb-text-click" id="add_item"  data-trigger="focus" placeholder="' . $this->lang->line("search_recipe_by_name_code") . '" title="' . $this->lang->line("au_pr_name_tip") . '"'); ?>
									 <input type="hidden" value="<?php echo $table_id; ?>" name="table_list_id">
									  <input type="hidden" value="<?php echo $order_type; ?>" name="order_type_id">
                                    <input type="hidden" value="<?php echo !empty($get_split) ? $get_split : ''; ?>" name="split_id">
									  <input type="hidden" name="biller" id="biller" value="<?= ($Owner || $Admin || !$this->session->userdata('biller_id')) ? $pos_settings->default_biller : $this->session->userdata('biller_id')?>"/>
									   <input type="hidden" name="no_peoples" id="no_peoples" class="form-control  kb-pad text-center " placeholder="<?=lang('how_many_people')?>" >
									</td>
								</tr>
							</tbody>
						</table>
						<!--- addon start   --->
						<div class="ad12" style="display:none;">
						  <div class="modal-body" id="pr_popover_content">
                         <form class="form-horizontal" role="form">
                           <div class="form-group text-center">
							<div class="col-sm-12">
								<table class="table table-bordered table-striped">
									 <thead>
										 <tr>
										   <!--  <th><?= lang('check'); ?></th>-->
											 <th><?= lang('recipe_addon'); ?></th>
											 <th><?= lang('quantity'); ?></th>
											 <th><?= lang('price'); ?></th>
										 </tr>
									 </thead>
									 <tbody id="poaddon-div"></tbody>
									 <tfoot>
									 	<tr>
									 		<td colspan="3">
									 			<span class='payment_status pull-left label label-danger iclose addclose' style='padding:10px 12px;font-size:14px;margin-right:5px;border-radius:0px;font-weight:normal;cursor:pointer;'  title='Remove' style='cursor:pointer;'>void</span>
									 			<button type="button" class="btn btn-primary pull-right" id="AddonItem"><?=lang('submit')?></button>
									 		</td>
									 		
									 	</tr>
									 </tfoot>
								</table>                            
							</div>
						</div>  
                     <div class="form-group" style="display: none">
                        <label for="addonamount" class="col-sm-4 control-label"><?=lang('addonamount')?></label>
                        <div class="col-sm-8">
                            <input type="hidden" class="form-control kb-pad" name="addonamount[]" id="addonamount">
                        </div>
                    </div>
                    <input type="hidden" id="punit_price" value=""/>
                    <input type="hidden" id="old_tax" value=""/>
                    <input type="hidden" id="old_qty" value=""/>
                    <input type="hidden" id="old_price" value=""/>
                    <input type="hidden" id="row_id" value=""/>
					
					  
                       </form>
                     </div>
						</div>
						<!----     addons_end    -->
						<div class="v12" style="display:none;"></div>
						<div class="customized_ingredients" style="display:none;">
                <h4 class="modal-title" id="cuModalLaebel"></h4>
						 <div class="modal-body" id="pr_popover_content1">
                <form class="form-horizontal" role="form">
                    <div class="form-group text-center">
                        <div class="col-sm-12">
                            <table class="table table-bordered table-striped table_se_q">
                                 <thead>
                                     <tr>
                                        <th><?= lang('check'); ?></th>
                                        <th><?= lang('name'); ?></th>  
                                      <!--  <th><?= lang('qty'); ?></th>
                                        <th><?= lang('uom'); ?></th>-->
                                     </tr>
                                 </thead>
                                 <tbody id="pocustomize-div"></tbody>
                            </table>                            
                        </div>
                    </div>                               
                </form>
            </div>
            <input type="hidden" id="row_id1" value=""/>
            <div class="modal-footer mod_foot">
			<span class="payment_status pull-left label label-danger iclose_customized" style="padding:10px 12px;font-size:14px;margin-right:5px;border-radius:0px;font-weight:normal;cursor:pointer;">void</span>
                <button type="button" class="btn btn-primary" id="CustomizeItem"><?=lang('submit')?></button>
            </div>	
						</div>
						<div class="category_se">
							<h4 class="text-center category_title_name">Category/ប្រភេទ</h4>
							<table class="table table_middle_s categorieslist" id="categorieslist">
								<tbody>

									<tr>
										<td>
											<ul>
							<?php
								foreach ($categories as $category) {
									if($this->Settings->user_language == 'khmer'){
										if(!empty($category->khmer_name)){
											$category_name = $category->khmer_name;
										}else{
											$category_name = $category->name;
										}
									}else{
										$category_name = $category->name;
									}	
												echo "<li>
													<div class='item'><button id=\"category-" . $category->id . "\" type=\"button\" value='" . $category->id . "' class=\"btn btn_default category\" ><span>" . $category_name . "</span></button></div>
												</li>";
											}
											?>
											</ul>

										</td>
									</tr>
								</tbody>
								<div class="btn-group btn-group-justified" style="position: absolute;">
									<div class="btn">
										<button style="z-index:99;position: absolute;left: -10px;top: 0px;border-radius: 10px;    padding: 15% 10%;" class="btn btn-danger " title="" type="button" id="previous3" data-original-title="Previous" tabindex="-1">
											<i class="fa fa-chevron-left"></i>
										</button>
									</div>
									<div class="btn">
										<button style="z-index:99;position: absolute;right:20px;top: 0px;border-radius: 10px;    padding: 15% 10%;" class="btn btn-danger " title="" type="button" id="next3" data-original-title="Next" tabindex="-1">
											<i class="fa fa-chevron-right"></i>
										</button>
									</div>
								</div>  
							</table>
						</div>
						<div class="sub_category_gr">
						<h4 class="text-center">Sub Category/ប្រភេទថ្នាក់ក្រោម</h4>
						<div id="subcategory-list">
							
						</div>
						<table class="table table_bottom_s subcategories_table">
							<tbody class="sub_category_list">
								<tr>
									<td>
										<ul>
								<?php
                            if (!empty($subcategories)) {
                                foreach ($subcategories as $category) {
										if($this->Settings->user_language == 'khmer'){
											if(!empty($category->khmer_name)){
												$subcategory_name = $category->khmer_name;
											}else{
												$subcategory_name = $category->name;
											}
										}else{
											$subcategory_name = $category->name;
										}
									   if($this->pos_settings->subcategory_display == 0){
                                   		 $subhtml = "<button id=\"subcategory-" . $category->id . "\" type=\"button\" value='" . $category->id . "' class=\"btn-prni btn_lightred subcategory slide\" >";
                                        }else{
                                            $subhtml = "<button id=\"subcategory-" . $category->id . "\" type=\"button\" value='" . $category->id . "' class=\"btn-img subcategory slide\" >";
                                        } 
										 if(strlen($subcategory_name) < 20){		
											$subhtml .= "<span class='name_strong'>" .$subcategory_name. "</span>";
										}else{
											$subhtml .= "<marquee class='name_strong' behavior='alternate' direction='left' scrollamount='1'>&nbsp;&nbsp;" .$subcategory_name. "&nbsp;&nbsp;</marquee>";
										}
										  $subhtml .=  "</button>";
										 echo $subhtml;				
                                }
                            }
                        ?>
                        				
										</ul>
										
									</td>
								</tr>
							</tbody>
							<div class="btn-group btn-group-justified" style="position: absolute;">
                                            <div class="btn">
                                                <button style="z-index:99;position: absolute;left: -20px;top: -23px;border-radius: 10px;    padding: 15% 10%;" class="btn btn-danger " title="" type="button" id="previous4" data-original-title="Previous" tabindex="-1">
                                                    <i class="fa fa-chevron-left"></i>
                                                </button>
                                            </div>
											<div class="btn">
                                                <button style="z-index:99;position: absolute;right: 0px;top: -23px;border-radius: 10px;    padding: 15% 10%;" class="btn btn-danger " title="" type="button" id="next4" data-original-title="Next" tabindex="-1">
                                                    <i class="fa fa-chevron-right"></i>
                                                </button>
                                            </div>
                                        </div> 
						</table>
						</div>
					</div>
				</div>
				<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
					<div class="tcb-simple-carousel">
						<div id="myCarousel" class="carousel slide" data-interval="false">
							<div class="btn-group btn-group-justified" style="position: absolute;">
								<div class="btn">
									<button style="z-index:99;position: absolute;left: -25px;top: -5px;height: 54vh; border-radius: 10px;   padding: 8% 5%;" class="btn btn-danger " title="" type="button" id="previous" data-original-title="Previous" tabindex="-1">
										<i class="fa fa-chevron-left"></i>
									</button>
								</div>
																			<div class="btn">
									<button style="z-index:99;position: absolute;right: -10px;top:-5px;height: 54vh;border-radius: 10px; padding: 8% 5%;" class="btn btn-danger " title="" type="button" id="next" data-original-title="Next" tabindex="-1">
										<i class="fa fa-chevron-right"></i>
									</button>
								</div>
							</div>
								<div class="carousel-inner">           
									 <div id="item-list">
                                            <?php echo $recipe; ?>
										</div>
								</div> 
								
							   			              
							</div>
					</div>
					<div class="clearfix"></div>
				<!--table-->
			<table class="table table_item_ls table_fixed_ls">
			<colgroup>
				<col width="28%">
				<col width="10%">
				<?php if($Settings->manual_item_discount ==1){  ?>
				<col width="10%">
				<col width="17%">
				<?php  }   ?>
				
				<col width="15%">
				<col width="13%">
				<col width="13%">
			</colgroup>
				<thead>
					<tr>
						<th>Name /<br>ឈ្មោះ</th>
						<th>Price /<br>តម្លៃ</th>
						<?php if($Settings->manual_item_discount ==1){  ?>
						<th>Dis % /<br>បញ្ចុះតម្លៃ%</th>
						<th>Dis Amt /<br>ចំនួនទឹកប្រាក់បញ្ចុះ</th>
						<?php   }   ?>
						<th>Qty /<br>ចំនួន</th>
						<th>Amount /<br>ចំនួនទឹកប្រាក់</th>
						<th>Delete /<br>លុប</th>
					</tr>
				</thead>
			</table>
		<div class="" id="item_list_m">
			<table class="table table_item_ls" id="posTable">
				<colgroup>
					<col width="28%">
					<col width="10%">
					<?php if($Settings->manual_item_discount ==1){  ?>
					<col width="10%">
					<col width="17%">
					<?php  }   ?>
					<col width="15%">
					<col width="13%">
					<col width="13%">
				</colgroup>

				<tbody>
				</tbody>
			</table>
		</div>
		<!--<table class="table table_item_ls table_pay_s_foot">
			<tbody>
				<tr>
					<td style="text-align: right;">Total Item/សរុបម្ហូប :<span id="total_items">0</span></td>
				 	<td style="text-align: right;">Total Qty/សរុបចំនួន : <span id="titems">0</span> </td>
				</tr>
			</tbody>
		</table>-->
		
		<table class="table table_item_ls table_pay_s_foot">
				<tbody>
				<tr>					<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
					<td >Item : <span id="total_items">0</span></td>
					<td>Discount : <span id="tds">0.00</span></td>
					<td>               <?php 
                                            if ($pos_settings->tax_type == 0){
                                                $taxname = 'Inclusive';
                                            }else{
                                                $taxname = 'Exclusive';
                                            }
                                            $getTax = $this->site->getTaxRateByID($this->pos_settings->default_tax);
                                            ?>
                                        <?=lang("Tax ".'('.$taxname.' '.$getTax->name.')');?> : <span id="ttax2">0.00</span> </td>
					 <td style="font-weight: bold;">Total : <span id="total">0.00</span>  <input type="hidden" name="sub_total" id="sub_total" value="0"></td>
					
				</tr>
				<tr>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td style="font-weight: bold;text-align: left;">Total Payable : <span style="text-align: right" id="gtotal">0.00</span></td>
				</tr>
			</tbody>
		</table>
		
		
		<!--<div id="bill_tbl"><span id="bill_span"></span>
    <table id="bill-table" width="100%" class="prT table table-striped" style="margin-bottom:0;"></table>-->
</div>
				</div>
			</div>
    	</div>
	       <?php   echo form_close();  ?>
	</section>
	<div class="modal fade in add_cust_sec" id="myModal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true"></div>
	 <div class="modal" id="cmModal" tabindex="-1" role="dialog" aria-labelledby="cmModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">
                    <i class="fa fa-2x">&times;</i></span>
                    <span class="sr-only"><?=lang('close');?></span>
                </button>
                <h4 class="modal-title" id="cmModalLabel"></h4>
            </div>
            <div class="modal-body" id="pr_popover_content">
                <div class="form-group">
                    <?= lang('comment', 'icomment'); ?>
                    <?= form_textarea('comment', '', 'class="form-control kb-text" id="icomment" style="height:80px;"'); ?>
                </div>
                <div class="form-group" style="display: none">
                    <?= lang('ordered', 'iordered'); ?>
                    <?php
                    $opts = array(0 => lang('no'), 1 => lang('yes'));
                    ?>
                    <?= form_dropdown('ordered', $opts, '', 'class="form-control" id="iordered" style="width:100%;"'); ?>
                </div>
                <input type="hidden" id="irow_id" value=""/>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="editComment"><?=lang('submit')?></button>
            </div>
        </div>
    </div>
</div>
<!--
<div class="modal" id="prModal" tabindex="-1" role="dialog" aria-labelledby="prModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true"><i
                            class="fa fa-2x">&times;</i></span><span class="sr-only"><?=lang('close');?></span></button>
                <h4 class="modal-title" id="prModalLabel"></h4>
            </div>
            <div class="modal-body" id="pr_popover_content">
                <form class="form-horizontal" role="form">
                    <div class="form-group text-center">
                       
                        <div class="col-sm-10">
                            <table class="table table-bordered table-striped">
                                 <thead>
                                     <tr>
                                         <th><?= lang('check'); ?></th>
                                         <th><?= lang('recipe_addon'); ?></th>
                                         <th><?= lang('quantity'); ?></th>
                                         <th><?= lang('price'); ?></th>
                                     </tr>
                                 </thead>
                                 <tbody id="poaddon-div"></tbody>
                            </table>                            
                        </div>
                    </div>  
                                     
                     <div class="form-group" style="display: none">
                        <label for="addonamount" class="col-sm-4 control-label"><?=lang('addonamount')?></label>
                        <div class="col-sm-8">
                            <input type="hidden" class="form-control kb-pad" name="addonamount[]" id="addonamount">
                        </div>
                    </div>

                    <input type="hidden" id="punit_price" value=""/>
                    <input type="hidden" id="old_tax" value=""/>
                    <input type="hidden" id="old_qty" value=""/>
                    <input type="hidden" id="old_price" value=""/>
                    <input type="hidden" id="row_id" value=""/>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="AddonItem"><?=lang('submit')?></button>
            </div>
        </div>
    </div>
</div>--->
<!--scripts-->
<div class="modal" id="customerModal" tabindex="-1" role="dialog" aria-labelledby="prModalLabel" aria-hidden="true">
<div class="modal-dialog modal-lg">
    <div class="modal-content close_pop">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('add_customer'); ?></h4>
        </div>
        <?php //$attrib = array('data-toggle' => 'validator', 'role' => 'form', class='bv-form' 'id' => 'add-customer-form');
       // echo admin_form_open_multipart("customers/add_pos", $attrib); ?>
        <form data-url="<?=admin_url('customers/add_pos')?>" data-toggle="validator" role="form"  method="post" accept-charset="utf-8" novalidate class="bv-form" id="add-customer-form">
        <div class="modal-body1">
            <span><?= lang('enter_info'); ?></span>
            <div class="row"> 
            <div class="col-md-12">
                <div class="col-md-6">
                <div class="form-group">
                    <label><?= lang("customer_type"); ?></label>
                    <?php $types = array('0' => lang('select_type'), '1' => lang('local'), '2' => lang('foreign'));
            echo form_dropdown('supplier_type', $types, '1', 'class="form-control select" id="supplier_type" required="required"'); ?>
                </div>
                	<div class="form-group">
                    <label class="control-label" for="customer_group"><?php echo $this->lang->line("customer_group"); ?></label>
                        <?php
                        foreach ($customer_groups as $customer_group) {
                            $cgs[$customer_group->id] = $customer_group->name;
                        }
                        echo form_dropdown('customer_group', $cgs, $Settings->customer_group, 'class="form-control select" id="customer_group" style="width:100%;" required="required"');
                        ?>
                    </div>
                    
						<div class="form-group">
                        <?= lang("address", "address"); ?>
                        <?php echo form_input('address', '', 'class="form-control kb-text-click" autocomplete="off" id="address" required="required"'); ?>
                    </div>
                    
                    <div class="form-group">
                        <?= lang("email_address", "email_address"); ?>
                        <input type="text" name="email" class="form-control kb-text-click" id="email_address" autocomplete="off" />
                    </div>

                </div>
                <div class="col-md-6">
                    
		    <div class="form-group person">
                        <?= lang("name", "name"); ?>
                        <?php echo form_input('name', '', 'class="form-control tip alphabetsOnly kb-text-click" id="name"   autocomplete="off" data-bv-notempty="true"'); ?>
                        <span id='alp' style="color: #a94442;">Alphabets Only Type</span>
                    </div>
                    <div class="form-group">
                        <?= lang("phone", "phone"); ?>
                        <input type="tel" name="phone" class="form-control kb-pad-qty numberonly"  maxlength="10"  autocomplete="off" id="phone"/>
                    </div>
                    <div class="form-group">
                        <?= lang("mobile_number"); ?>
                        <input type="tel" name="mobile_number" class="form-control kb-pad-qty numberonly"  autocomplete="off" maxlength="10"  id="mobile_number"
                               value=""/>
                    </div>
                    <div class="form-group" style="margin-top: 53px !important;">
                    <label class="control-label" for="allow_loyalty"><?php echo $this->lang->line("Allow_Loyalty"); ?></label>
                     <select name="allow_loyalty" class="select" id="allow_loyalty">
			           <option value="0"><?=lang('No')?></option>
			         <option value="1"><?=lang('Yes')?></option>
		               </select>
                    </div>
                </div>
            </div>
	    </div>


        </div>
        <div class="modal-footer">
            <input type="hidden" id='cids' value="">
            <input type="hidden" id='cname' value="">
            <input type="hidden" name="add_customer" value="<?=lang('add_customer')?>">
            <?php echo form_submit('add_customer', lang('add_customer'), 'class="btn btn-primary"'); ?>
            <button type="button"  class="btn btn-default pop_close" data-dismiss="modal">Close</button>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
</div>




<?php   $default_tax=$this->site->getTaxRateByID($pos_settings->default_tax);
	    !empty($default_tax)?$default_tax:0;    ?>
<script>
var site = <?=json_encode(array('url' => base_url(), 'base_url' => admin_url('/'), 'assets' => $assets, 'settings' => $Settings, 'dateFormats' => $dateFormats))?>, pos_settings = <?=json_encode($pos_settings);?>,default_tax=<?php echo  json_encode($default_tax);  ?>;
</script>
<script type="text/javascript" src="<?=$assets?>js/jquery-2.0.3.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/jquery.mCustomScrollbar.concat.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/jquery-ui.js"></script>
<script type="text/javascript" src="<?=$assets?>js/perfect-scrollbar.min.js"></script>
<!--<script type="text/javascript" src="<?=$assets?>js/select2.full.min.js"></script>-->
<script type="text/javascript" src="<?=$assets?>js/select2.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/custom.js"></script>
<script type="text/javascript" src="<?=$assets?>js/jquery.calculator.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/bootstrapValidator.min.js"></script>
<script type="text/javascript" src="<?=$assets?>pos/js/plugins.min.js"></script>
<script type="text/javascript" src="<?=$assets?>pos/js/parse-track-data.js"></script>
<script type="text/javascript" src="<?=$assets?>pos/js/pos_v2.ajax.js?v=1"></script>
<?php unset($Settings->setting_id, $Settings->smtp_user, $Settings->smtp_pass, $Settings->smtp_port, $Settings->update, $Settings->reg_ver, $Settings->allow_reg, $Settings->default_email, $Settings->mmode, $Settings->timezone, $Settings->restrict_calendar, $Settings->restrict_user, $Settings->auto_reg, $Settings->reg_notification, $Settings->protocol, $Settings->mailpath, $Settings->smtp_crypto, $Settings->corn, $Settings->customer_group, $Settings->srampos_username, $Settings->purchase_code);?>
<script type="text/javascript">
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
	<script>
	
		var recipe_variant = 0, shipping = 0, p_page = 0, per_page = 0, tcp = "<?=$tcp?>", pro_limit =15,pro_limt_cat=15,pro_limt_sub_cat=15,
			brand_id = 0, obrand_id = 0, cat_id = "<?=$pos_settings->default_category?>", ocat_id = "<?=$pos_settings->default_category?>", sub_cat_id = 0, osub_cat_id,
			count = 1, an = 1, DT = <?=$Settings->default_tax_rate?>,
			recipe_tax = 0, invoice_tax = 0, recipe_discount = 0, order_discount = 0, total_discount = 0, total = 0, total_paid = 0, grand_total = 0,
			KB = <?=$pos_settings->keyboard?>, tax_rates =<?php echo json_encode($tax_rates); ?>;
		var protect_delete = <?php if (!$Owner && !$Admin) {echo $pos_settings->pin_code ? '1' : '0';} else {echo '0';} ?>, billers = <?= json_encode($posbillers); ?>, biller = <?= json_encode($posbiller); ?>;
		var username = '<?=$this->session->userdata('username');?>', order_data = '', bill_data = '';
			$(document).ready(function(){
				$('#myCarousel,#myCarousel1,#myCarousel2').carousel();
			});
			
	<?php
		if($get_order_type == 3 && !empty($same_customer) && !empty($this->input->get('customer'))){
			$customer = $this->input->get('customer');
		}elseif($get_order_type == 3 && empty($same_customer) && !empty($this->input->get('customer'))){
			$customer = $this->input->get('customer');
		}elseif($get_order_type == 3 && !empty($same_customer) && empty($this->input->get('customer'))){
			$customer = $same_customer;
		}elseif($get_order_type == 3 && empty($same_customer) && empty($this->input->get('customer'))){
			$customer = '';
		}elseif($get_order_type != 3 && !empty($same_customer) && !empty($this->input->get('customer'))){
			$customer = $this->input->get('customer');
		}elseif($get_order_type != 3 && empty($same_customer) && !empty($this->input->get('customer'))){
			$customer = $this->input->get('customer');
		}elseif($get_order_type != 3 && !empty($same_customer) && empty($this->input->get('customer'))){
			$customer = $same_customer;
		}elseif($get_order_type != 3 && empty($same_customer) && empty($this->input->get('customer'))){
			$customer = $customer->id;	
		}else{
			$customer = '';
		}
		
		?>
		<?php
		if(!empty($customer)){
		?>
		 if (localStorage.getItem('poscustomer')) {
			localStorage.removeItem('poscustomer');
		 }
		if (!localStorage.getItem('poscustomer')) {
            localStorage.setItem('poscustomer', <?=$customer;?>);
        }
		<?php
		}
		?>
		$(document).ready(function() {
			$('.minus').click(function () {
				var $input = $(this).parent().find('input');
				var count = parseInt($input.val()) - 1;
				count = count < 0 ? 0 : count;
				$input.val(count);
				$input.change();
				return false;
			});
			$('.plus').click(function () {
				var $input = $(this).parent().find('input');
				$input.val(parseInt($input.val()) + 1);
				$input.change();
				return false;
			});
		});
	</script>
	<script>
	$(function(){
//		$("#item-list").mCustomScrollbar({
//			 theme:"dark-3" ,
//		});
		$("#item_list_m").mCustomScrollbar({
			 theme:"dark-3" ,
		});
        });
	</script>
	<script>
	        $(document).on('click', '.category', function () {
            if (cat_id != $(this).val()) {
                $('#open-category').click();
                $('#modal-loading').show();
                var order_type = "<?php echo $get_order_type; ?>";
                cat_id = $(this).val();
				var warehouse_id = '<?php echo $this->session->userdata('warehouse_id'); ?>';
                var order_type = "<?php echo $get_order_type; ?>";
                $.ajax({
                    type: "get",
                    url: "pos/pos/ajaxcategorydata",
                    data: {category_id: cat_id, warehouse_id: warehouse_id, recipe_standard: 1,order_type:order_type},
                    dataType: "json",
                    success: function (data) {
                        $('#item-list').empty();
                        var newPrs = $('<div></div>');
                        newPrs.html(data.recipe);
                        newPrs.appendTo("#item-list");
                        $('#subcategory-list').empty();
						$(".subcategories_table tr").detach();
                        var newScs = $('<div></div>');
                        newScs.html(data.subcategories);
                        newScs.appendTo("#subcategory-list");
                        tcp = data.tcp;
                        nav_pointer();
                    }
                }).done(function () {
                    p_page = 'n';
                    $('#category-' + cat_id).addClass('active');
                    $('#category-' + ocat_id).removeClass('active');
                    ocat_id = cat_id;
                    $('#modal-loading').hide();
                    nav_pointer();
                });
            }
        });
		
        $('#category-' + cat_id).addClass('active');
		$(document).on('click', '.brand', function () {
            if (brand_id != $(this).val()) {
                $('#open-brands').click();
                $('#modal-loading').show();
                brand_id = $(this).val();
                $.ajax({
                    type: "get",
                    url: "<?=admin_url('pos/ajaxbranddata');?>",
                    data: {brand_id: brand_id},
                    dataType: "json",
                    success: function (data) {
                        $('#item-list').empty();
                        var newPrs = $('<div></div>');
                        newPrs.html(data.recipe);
                        newPrs.appendTo("#item-list");
                        tcp = data.tcp;
                        nav_pointer();
                    }
                }).done(function () {
                    p_page = 'n';
                    $('#brand-' + brand_id).addClass('active');
                    $('#brand-' + obrand_id).removeClass('active');
                    obrand_id = brand_id;
                    $('#category-' + cat_id).removeClass('active');
                    $('#subcategory-' + sub_cat_id).removeClass('active');
                    cat_id = 0; sub_cat_id = 0;
                    $('#modal-loading').hide();
                    nav_pointer();
                });
            }
        });

        $(document).on('click', '.subcategory', function () {
            if (sub_cat_id != $(this).val()) {
                $('#open-subcategory').click();
                $('#modal-loading').show();
                sub_cat_id = $(this).val();
				var warehouse_id = '<?php echo $this->session->userdata('warehouse_id'); ?>';
                var order_type = "<?php echo $order_type; ?>";				
                $.ajax({
                    type: "get",
                    url: "<?= base_url('pos/pos/ajaxrecipe_consolidate');?>",
                    data: {category_id: cat_id, warehouse_id: warehouse_id, subcategory_id: sub_cat_id, per_page: p_page,order_type:order_type},
                    dataType: "html",
                    success: function (data) {
                        $('#item-list').empty();
                        var newPrs = $('<div></div>');
                        newPrs.html(data);
                        newPrs.appendTo("#item-list");
                    }
                }).done(function () {
                    p_page = 'n';
                    $('#subcategory-' + sub_cat_id).addClass('active');
                    $('#subcategory-' + osub_cat_id).removeClass('active');
                    $('#modal-loading').hide();
                });
            }
        });
		  $("#add_item").autocomplete({
            source: function (request, response) {
                $.ajax({
                    type: 'get',
                    url: '<?=admin_url('sales/suggestions');?>',
                    dataType: "json",
                    data: {
                        term: request.term,
                        warehouse_id: $("#poswarehouse").val(),
                        customer_id: $("#poscustomer").val(),
                        recipe_standard: 1
                    },
                    success: function (data) {
                        $(this).removeClass('ui-autocomplete-loading');
                        response(data);
                    }
                });
            },
            minLength: 1,
            autoFocus: true,
            delay: 250,
            response: function (event, ui) {
                if ($(this).val().length >= 16 && ui.content[0].id == 0) {
                    bootbox.alert('<?=lang('no_match_found')?>', function () {
                        $('#add_item').focus();
                    });
                    $(this).val('');
                }
                else if (ui.content.length == 1 && ui.content[0].id == 0) {
                    bootbox.alert('<?=lang('no_match_found')?>', function () {
                        $('#add_item').focus();
                    });
                    $(this).val('');
                }
            },
	    focus: function( event, ui ) {
		event.preventDefault();
		if (item_scanned) {
		    $('.ui-menu-item:eq(0)').trigger('click');
		}
	    },
                select: function (event, ui) {
                event.preventDefault();
	         	item_scanned = false;
                if (ui.item.id !== 0) {
                    var row = add_invoice_item(ui.item);
                    if (row)
                        $(this).val('');
                } else {
                    bootbox.alert('<?=lang('no_match_found')?>');
                }
		        $('#add_item').val('');
            }
        });
		   $(document).bind( "change.autocomplete", '#add_item', function( event ) {               
                var add_item = $('#add_item').val();
                 $("#add_item").autocomplete('search', add_item);
                
            });
		        $(document).on('click', '.recipe:not(".has-varients")', function (e) {
				$(".table_bottom_s").show();
				$(".table_middle_s").show();
				$(".category_se").show();
				$(".sub_category_gr").show();
				$(".customized_ingredients").hide();
				$(".ad12").hide();
				$(".v12").empty();
                $('#modal-loading').show();
                code = $(this).val(),
                wh = $('#poswarehouse').val(),
                cu = $('#poscustomer').val();
				var tb = $('#postable_list').val();
                $.ajax({
                type: "get",
                url: "<?= base_url('pos/pos/getrecipeDataByCode_all')?>",
                data: {code: code, warehouse_id: wh, customer_id: cu},
                dataType: "json",
                success: function (data) {
                    e.preventDefault();
                    if (data !== null) {
                        add_invoice_item(data);
                        $('#modal-loading').hide();
                    } else {
                        bootbox.alert('<?=lang('no_match_found')?>');
                        $('#modal-loading').hide();
                    }
                }
            });
				
        });
		 $(document).on('click', '.recipe-varient', function (e) {  
		var code = $(this).attr('code');        
        $('#myVaraintModal').modal('hide');        
        $('#modal-loading').hide();        
        var wh = $('#poswarehouse').val();
        var cu = $('#poscustomer').val();
		var tb = $('#postable_list').val();
        $vid = $(this).attr('data-id');
            $.ajax({
                type: "get",
                url: "<?=base_url('pos/pos/getrecipeVarientDataByCode')?>",
                data: {code: code, warehouse_id: wh, customer_id: cu,variant:$vid},
                dataType: "json",
                success: function (data) {                    
                    e.preventDefault();
                    if (data !== null) {                        
                        add_invoice_item(data);
                        $('#modal-loading').hide();
                    } else {
                        bootbox.alert('<?=lang('no_match_found')?>');
                        $('#modal-loading').hide();
                    }
                }
            });
        }); 
		 $(document).on('click','.has-varients',function(){
		  var tb = $('#postable_list').val();
          $obj = $(this);
          $v = $obj.attr('value');
          $popcon = $obj.closest('span').find('.variant-popup').html();
		  $('.table_middle_s').hide();
		   $('.category_se').hide();
	      $('.table_bottom_s').hide();
		  $('.sub_category_gr').hide();
	      $('#cpinner').hide();
		  $('.v12').html($popcon);
		  $('.v12').show();
          });
		$(document).ready(function () {
		  $('#sent_to_kitchen').click(function () {
          	if (count == 1 ) {
                bootbox.alert('Select recipe');
			  return false;
            }else{
				$('#pos-sale-form').submit();
			}
		   });
		});
	 $(document).ready(function () {
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
		});
	    $('#view-customer').click(function(){
        $('#myModal').modal({remote: site.base_url + 'customers/view/' + $("input[name=customer1]").val()});
        $('#myModal').modal('show');
    });
    var default_customer_id=localStorage.getItem('poscustomer');
    $("#poscustomer1").val(default_customer_id);
    $(document).ready(function(){
    $(".poscustomer").change(function(){
    $("#poscustomer1").val($("input[name=customer]").val());
    });
    });
	  $('#next').click(function () {
            if (p_page == 'n') {
                p_page = 0
            }
            p_page = p_page + pro_limit;
            if (tcp >= pro_limit && p_page < tcp) {
                $('#modal-loading').show();
				var warehouse_id = '<?php echo $this->session->userdata('warehouse_id'); ?>';
                var order_type = "<?php echo $get_order_type; ?>";  
                $.ajax({
                    type: "get",
                    url: "<?= base_url('pos/pos/ajaxrecipe_consolidate');?>",
                    data: {category_id: cat_id, warehouse_id: warehouse_id, subcategory_id: sub_cat_id, per_page: p_page,order_type: order_type},
                    dataType: "html",
                    success: function (data) {
						if (!$.trim(data)){   
							 $('#next').prop('disabled', true);
							return false;
						}
                        $('#item-list').empty();
                        var newPrs = $('<div></div>');
                        newPrs.html(data);
                        newPrs.appendTo("#item-list");
                        nav_pointer();
                    }
                }).done(function () {
                    $('#modal-loading').hide();
                });
            } else {
                p_page = p_page - pro_limit;
            }
        });

        $('#previous').click(function () {
			 $('#next').prop('disabled', false);
            if (p_page == 'n') {
                p_page = 0;
            }
            if (p_page != 0) {
                $('#modal-loading').show();
                p_page = p_page - pro_limit;
                if (p_page == 0) {
                    p_page = 'n'
                }
				var warehouse_id = '<?php echo $this->session->userdata('warehouse_id'); ?>';
                var order_type = "<?php echo $get_order_type; ?>";
                $.ajax({
                    type: "get",
                    url: "<?= base_url('pos/pos/ajaxrecipe_consolidate');?>",
                    data: {category_id: cat_id, warehouse_id: warehouse_id, subcategory_id: sub_cat_id, per_page: p_page,order_type:order_type},
                    dataType: "html",
                    success: function (data) {
                        $('#item-list').empty();
                        var newPrs = $('<div></div>');
                        newPrs.html(data);
                        newPrs.appendTo("#item-list");
                        nav_pointer();
                    }

                }).done(function () {
                    $('#modal-loading').hide();
                });
            }
        });
				
		 $('#next3').click(function () {
            if (p_page == 'n') {
                p_page = 0
            }
            p_page = p_page + pro_limt_cat;
            if (tcp >= pro_limt_cat && p_page < tcp) {
                $('#modal-loading').show();
				var warehouse_id = '<?php echo $this->session->userdata('warehouse_id'); ?>';
                var order_type = "<?php echo $get_order_type; ?>";  
                $.ajax({
                    type: "get",
                    url: "<?= base_url('pos/pos/get_categories');?>",
                    data: { per_page: p_page,order_type: order_type},
                    dataType: "html",
                    success: function (data) {
						if (!$.trim(data)){   
							 $('#next3').prop('disabled', true);
							return false;
						}
                        $('.categorieslist').empty();
                        var newPrs = $('<div></div>');
                        newPrs.html(data);
                        newPrs.appendTo(".categorieslist");
                      //  nav_pointer();
                    }
                }).done(function () {
                    $('#modal-loading').hide();
                });
            } else {
                p_page = p_page - pro_limt_cat;
            }
        });

        $('#previous3').click(function () {
			 $('#next3').prop('disabled', false);
            if (p_page == 'n') {
                p_page = 0;
            }
            if (p_page != 0) {
                $('#modal-loading').show();
                p_page = p_page - pro_limt_cat;
                if (p_page == 0) {
                    p_page = 'n'
                }
				var warehouse_id = '<?php echo $this->session->userdata('warehouse_id'); ?>';
                var order_type = "<?php echo $get_order_type; ?>";
                $.ajax({
                    type: "get",
                    url: "<?= base_url('pos/pos/get_categories');?>",
                    data: { per_page: p_page,order_type:order_type},
                    dataType: "html",
                    success: function (data) {
                          $('.categorieslist').empty();
                        var newPrs = $('<div></div>');
                        newPrs.html(data);
                        newPrs.appendTo(".categorieslist");
                        nav_pointer();
                    }

                }).done(function () {
                    $('#modal-loading').hide();
                });
            }
        });
		
		
				 $('#next4').click(function () {
            if (p_page == 'n') {
                p_page = 0
            }
            p_page = p_page + pro_limt_sub_cat;
            if (tcp >= pro_limt_sub_cat && p_page < tcp) {
                $('#modal-loading').show();
				var warehouse_id = '<?php echo $this->session->userdata('warehouse_id'); ?>';
                var order_type = "<?php echo $get_order_type; ?>";  
                $.ajax({
                    type: "get",
                    url: "<?= base_url('pos/pos/get_subcategories');?>",
                    data: {category_id: cat_id, per_page: p_page,order_type: order_type},
                    dataType: "html",
                    success: function (data) {
						if (!$.trim(data)){   
							 $('#next4').prop('disabled', true);
							return false;
						}
                        $('.sub_category_list').empty();
                        var newPrs = $('<div></div>');
                        newPrs.html(data);
                        newPrs.appendTo(".sub_category_list");
                        nav_pointer();
                    }
                }).done(function () {
                    $('#modal-loading').hide();
                });
            } else {
                p_page = p_page - pro_limt_sub_cat;
            }
        });

        $('#previous4').click(function () {
			 $('#next4').prop('disabled', false);
            if (p_page == 'n') {
                p_page = 0;
            }
            if (p_page != 0) {
                $('#modal-loading').show();
                p_page = p_page - pro_limt_sub_cat;
                if (p_page == 0) {
                    p_page = 'n'
                }
				var warehouse_id = '<?php echo $this->session->userdata('warehouse_id'); ?>';
                var order_type = "<?php echo $get_order_type; ?>";
                $.ajax({
                    type: "get",
                    url: "<?= base_url('pos/pos/get_subcategories');?>",
                    data: {category_id: cat_id,   per_page: p_page,order_type:order_type},
                    dataType: "html",
                    success: function (data) {
                        $('.sub_category_list').empty();
                        var newPrs = $('<div></div>');
                        newPrs.html(data);
                        newPrs.appendTo(".sub_category_list");
                        nav_pointer();
                    }
                }).done(function () {
                    $('#modal-loading').hide();
                });
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
		 function display_keyboards() {
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
        $('.kb-text-click').keyboard({
        autoAccept: true,
        alwaysOpen: false,
        openOn: 'click',
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
 }
	
	</script>
	<script>
$(".numberonly").keypress(function (event){
	
	if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
		event.preventDefault();
	}
  
	});
$(document).ready(function(){
    $('#customer_type').change(function(){
	if($(this).val()=="prepaid"){
	    $('.credit_days').hide().find('input').val('');
	    $('.credit_limit').hide().find('input').val('');
	    
	}else{
	    $('.credit_days').show();
	    $('.credit_limit').show();
	}
    });
      $('#alp').hide();
      $('.alphabetsOnly').keyup(function (e) {
        var regex = new RegExp(/^[a-zA-Z\s]+$/);
       if($('#name').val().match(regex)){
        $('#alp').hide();
            return true;
        }
        else {
    $('#alp').show();
            return false;
        }
    });
})

</script>
<script type="text/javascript">
    //    $page = '<?=$_GET['type']?>';
	
	$("#add-customer").on("click",function(){
		$('#add-customer-form')[0].reset();
	});
    $(document).ready(function(){
      $('#add-customer-form')
        .bootstrapValidator(
                            {
                                message: 'Please enter/select a value',
                                //submitButtons: 'input[type="submit"]',
                                
                            }
                        )
        .on('success.form.bv', function(e) {
            // Prevent form submission
           e.preventDefault();
        $('.counter-form-error').remove();
            $obj = $(this);
            $url = $obj.attr('data-url');
            var form = $('#add-customer-form')[0];
            var data = new FormData(form);
        $.ajax({
                    url: $url,
                    type: "POST",
                    data: data,//$formData+'&userfile='+data+'&add_brand=Add Brand',
                    cache: false,
                    dataType: 'json',
                    processData: false, // Don't process the files
                  contentType: false,
                    success:function(data){
                        if (data.error) {
                            $('<div class="counter-form-error">'+data.error+'</div>').insertAfter($('.modal-body1 span:eq(0)'));
                            $obj.find('input[type="submit"]').attr('disabled',false);//$('#add-counter').live('submit');
                        } else if (data.success) {
							$("#poscustomer").val(data.success.id);
							$("#poscustomer").trigger("change");;
                             $(".pop_close").trigger("click");
							var ids = $('#cids').val(data.success.id);
							var iname = $(this).attr('name');
							
							var iid = '#' + id;
							if (!!$(this).attr('data-bv-notempty') || !!$(this).attr('required')) {
								$("label[for='" + id + "']").append(' *');
								$(document).on('change', iid, function () {
								$('form[data-toggle="validator"]').bootstrapValidator('revalidateField', iname);
								});
							 }
                        }
                    },
                   
                });
                      
        });
        
    });
	
	$('.persons').on('blur', function() {
   $("#no_peoples").val($(this).val());
});
	
</script>

	<?php 
echo $this->load->view($this->theme.'pos_v2/shift/shift_popup'); 
?>
</body>
</html>
