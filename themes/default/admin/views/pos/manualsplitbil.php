<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?=lang('pos_module') . " | " . $Settings->site_name;?></title>
    <script type="text/javascript">if(parent.frames.length !== 0){top.location = '<?=admin_url('pos')?>';}</script>
    <base href="<?=base_url()?>"/>
    <meta http-equiv="cache-control" content="max-age=0"/>
    <meta http-equiv="cache-control" content="no-cache"/>
    <meta http-equiv="expires" content="0"/>
    <meta http-equiv="pragma" content="no-cache"/>
    <link rel="shortcut icon" href="<?=$assets?>images/favicon.ico"/>
    <link rel="stylesheet" href="<?=$assets?>styles/theme.css" type="text/css"/>
    <link rel="stylesheet" href="<?=$assets?>styles/style.css" type="text/css"/>
    <link rel="stylesheet" href="<?=$assets?>pos/css/posajax.css" type="text/css"/>
    <link rel="stylesheet" href="<?=$assets?>pos/css/print.css" type="text/css" media="print"/>
    <script type="text/javascript" src="<?=$assets?>js/jquery-2.0.3.min.js"></script>
    <script type="text/javascript" src="<?=$assets?>js/jquery-migrate-1.2.1.min.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
     <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.js"></script>
    <!--[if lt IE 9]>
    <script src="<?=$assets?>js/jquery.js"></script>
    <![endif]-->
    <?php if ($Settings->user_rtl) {?>
        <link href="<?=$assets?>styles/helpers/bootstrap-rtl.min.css" rel="stylesheet"/>
        <link href="<?=$assets?>styles/style-rtl.css" rel="stylesheet"/>
        <script type="text/javascript">
            $(document).ready(function () {
                $('.pull-right, .pull-left').addClass('flip');
            });
            
            
        </script>
    <?php }
    ?><script>var unique_discount=0;var only_offer_dis = 0;</script>
</head>
<body>

<noscript>
    <div class="global-site-notice noscript">
        <div class="notice-inner">
            <p><strong>JavaScript seems to be disabled in your browser.</strong><br>You must have JavaScript enabled in
                your browser to utilize the functionality of this website.</p>
        </div>
    </div>
</noscript>



<div id="wrapper">
   

    <?php
    $this->load->view($this->theme . 'pos/pos_header');
    ?>
     
    <div id="content">
        <div class="c1">
            <div class="pos">
                <?php
                    if ($error) {
                        echo "<div class=\"alert alert-danger\"><button type=\"button\" class=\"close fa-2x\" data-dismiss=\"alert\">&times;</button>" . $error . "</div>";
                    }
                ?>
                <?php
                    if (!empty($message)) {
                        echo "<div class=\"alert alert-success\"><button type=\"button\" class=\"close fa-2x\" data-dismiss=\"alert\">&times;</button>" . $message . "</div>";
                    }
                ?>
                
              
               
                <div id="pos">
                
                    <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'id' => 'pos-manualsplitbill-form');
                    echo admin_form_open("pos/billing?order_type=".$order_type."&bill_type=3&bils=".$bils."&table=".$table_id."&splits=".$split_id, $attrib);?>

                         <input type="hidden" name="order_type" value="<?php echo $order_type; ?>">
                        <input type="hidden" name="bill_type" value="<?php echo $bill_type;?>" />
                        <input type="hidden" name="bils" value="<?php echo $bils;?>" />
                        <input type="hidden" name="table" value="<?php echo $table_id;?>" />
                        <input type="hidden" name="splits" value="<?php echo $split_id;?>" />
                        
                        <?php
                        if($order_type == 3){
                        ?>
                        <div class="col-lg-12">
                        <label>Delivery Person</label>
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
                        <div class="clearfix"></div>
                    <div class="tableleft col-sm-3 col-xs-3">
                        <div id="left-middle">
                                
                                <div class="col-sm-12 col-xs-12 left_dragtable example_manual_single">
                                        <div class="rec_price1"><?=lang("recipe");?></div>
                                        <div class="rec_price2 rec_price_che"><?=lang("price");?></div>
                                        <div class="rec_price3"><?=lang("qty");?></div>
                                        <div class="rec_price4 rec_price_che"><?=lang("discount");?></div>
                                        <div class="rec_price5 rec_price_che"><?=lang("discount_amount");?></div>
                                        <div class="rec_price6 rec_price_che"><?=lang("tax_amount");?></div>
                                        <div class="rec_price7"><?=lang("subtotal");?></div>
                                </div>
                                <?php
                                     if(!empty($order_item)){
                                    ?>
                                <div class="col-lg-12 col-sm-12 col-xs-12 orders draggable " id="origin" style="min-height:200px; background:#fff;">
                                    
                                    <?php
                                        foreach($order_item as $k =>$item) 
                                        {   $dis= 0;
                                            $discount = $this->site->discountMultiple($item->recipe_id);
                                            if(isset($discount['unique_discount'])){ ?>
                                             <script> unique_discount=1; </script>
                                           <?php } if(isset($discount['only_offer_dis'])){ ?>
                                             <script> only_offer_dis=1; </script>
                                           <?php }
                                            if(!empty($discount)){
                                                if($discount[2]== 'percentage_discount'){
                                                    $discount_value = $discount[1].'%';
                                                }else{
                                                    $discount_value = $discount[1];
                                                }
                                                 $total_p = $item->subtotal;
                                                 $price_value = $this->site->calculateDiscount($discount_value, $total_p);
                                                 $dis = $price_value;
                                                 $price_total = $total_p;
                                            }else{
                                                 $price_total = $item->subtotal;
                                            }


                                              
                                        $variant ='';$variant_name=''; $variant_id='';
                                            if($item->variant!=''){  
                                                $recipe_variantid[] = $item->recipe_variant_id;
                                                $vari = explode('|',$item->variant);
                                                $variant = $item->variant;
                                                $variant_id = $item->recipe_variant_id;
                                                $variant_name='[<span class="pos-variant-name">'.$variant.'</span>]';
                                            }else{
                                                $recipe_variantid[] = '';
                                            }
                                            

                                        $addondetails = $this->site->getAddonByRecipeidAndOrderitemid($item->recipe_id,$item->id);
                                              $itemaddonamt =0;
                                        if(!empty($addondetails)) :
                                             foreach ($addondetails as $key => $addons) {                                     
                                                $itemaddonamt +=$addons->price*$addons->qty;
                                            }                                 
                                        endif;

                                        ?>
                                    <div class="col-lg-12 col-sm-12 col-xs-12 draggable orders_item">
                                        
                                        <div class="rec_price1 item_recipe"><?php echo $item->recipe_name.$variant_name;?>
                                        <?php 
                                       /* $addondetails = $this->site->getAddonByRecipeidAndOrderitemid($item->recipe_id,$item->id);
                                         if(!empty($addondetails)) :
                                             foreach ($addondetails as $key => $addons) { ?>
                                                <br> <span style="color: #0e34ef;font-weight: bold;"> <?= $addons->addon_name ?> (<?= $addons->qty ?> X  <?=  $addons->price ?> ) &nbsp;= <?=  $this->sma->formatMoney($addons->price*$addons->qty) ?></span>
                                                
                                             <?php }
                                         endif;*/
                                         ?>
                                        <input type="hidden" name="split[<?=$k?>][recipe_name][]"  value="<?php echo $item->recipe_name; ?>">
                                        </div>

                                        <div class="item_id no_pad"><input type="hidden" name="split[<?=$k?>][recipe_id][]"  value="<?php echo $item->recipe_id; ?>" class="recipe-id"></div>

                                        <div class=" item_code no_pad"><input type="hidden" name="split[<?=$k?>][recipe_code][]"  value="<?php echo $item->recipe_code; ?>"></div>

                                        <div class=" item_type no_pad"><input type="hidden" name="split[<?=$k?>][recipe_type][]"  value="<?php echo $item->recipe_type; ?>"></div>


                                        <div class="rec_price2 item_price rec_price_che"><?php echo $this->sma->formatDecimal($price_total-$item->manual_item_discount);?><input type="hidden" name="split[<?=$k?>][unit_price][]"  value="<?php echo $item->unit_price-$item->manual_item_discount; ?>"></div>

                                        <div class="rec_price10 manual_item_discount rec_price_che" > 
                                        <input type="hidden" name="split[<?=$k?>][manual_item_discount][]"  value="<?php echo $item->manual_item_discount;?>" class ="form-control pos-input-tip manual_item_discount text-right" readonly="readonly">
                                        </div>

                                        <div class="rec_price3 item_quantity"><?php echo $item->quantity;?><input type="hidden" name="split[<?=$k?>][quantity][]"  value="<?php echo $item->quantity; ?>" class="recipe-quantity"></div>                                        
                                        
                                        <div class="rec_price4 discount_name rec_price_che">
                                            <?php echo $discount_value; ?>
                                        </div>
                                        
                                        <div class="rec_price5 item_discount rec_price_che">
                                       <?php echo $dis; ?>
                                        <input type="hidden" class="all-item-discounts" name="split[<?=$k?>][item_discount][]"  value="<?php echo $dis; ?>">           
                                        </div>

                                        <div class="rec_price5 other_item_discount rec_price_che">
                                       <?php echo $dis; ?>
                                        <input type="hidden" class="all-other-item-discounts" name="split[<?=$k?>][other_item_discount][]"  value="" id="other-dis-recipe-<?=$item->recipe_id?>">           
                                        </div>

                                        <div class="rec_price6 item_tax rec_price_che">
                                           <?php echo $item->item_tax; ?>
                                         <input type="hidden" name="split[<?=$k?>][item_tax][]"  value="<?php echo $item->item_tax; ?>">
                                        </div>

                                        <div class=" item_discount_id no_pad">
                                        <input type="hidden" name="split[<?php echo $k;?>][item_discount_id][]"  value="<?php echo $discount[0]; ?>">
                                        </div>
                                        
                                        <div class="rec_price7 item_subtotal"><?php echo $this->sma->formatDecimal($price_total-$item->manual_item_discount);?><input type="hidden" name="split[<?=$k?>][subtotal][]"  value="<?php echo $price_total-$item->manual_item_discount; ?>"></div>

                                        <div class="rec_price8 item_variant_id"><input type="hidden" value="<?php echo $item->recipe_variant_id; ?>" class="item_variant_id"></div>


                                        <div class="rec_price9 addon_subtotal"><input type="hidden"  value="<?php echo $itemaddonamt; ?>" class="addon_subtotal"></div>

                                       
                                    </div>  
                                     <?php
                                        }
                                        ?>
                                </div>
                                <?php
                                     }
                                    ?> 
                                <div style="clear:both;"></div>
                          
                        </div>
                    </div>
                    
                    <div class="col-lg-9">
                    
                    <?php
                    $split_count = $bils;
                    for($i=1;$i<=$split_count;$i++){
                    ?>
                    <div class="tableright col-sm-4 col-xs-12 manual_click_btn split-form-<?=$i?>">
                        
                             <a href="javascript:void(0);" data-toggle="tooltip" data-placement="bottom" data-original-title="" aria-describedby="tooltip" title="Click here to full details" class="checkmark" id="<?php echo $i; ?>" ></a>
                            <div class="col-xs-12 rightdrag_table drag_table_head">
                            
                                    <div class="col-lg-3 col-sm-3 col-xs-12"><?=lang("recipe");?></div>
                                    <div class="col-lg-3 col-sm-3 col-xs-12 rec_price_che"><?=lang("price");?></div>
                                    <div class="col-lg-3 col-sm-3 col-xs-12"><?=lang("qty");?></div>
                                    <div class="col-lg-3 col-sm-3 col-xs-12 rec_price_che"><?=lang("discount");?></div>
                                        <div class="col-lg-3 col-sm-3 col-xs-12 rec_price_che"><?=lang("discount_amount");?></div>
                                        
                                    <div class="col-lg-3 col-sm-3 col-xs-12"><?=lang("subtotal");?></div>
                            </div>
                            
                            <div class="col-lg-12 col-sm-12 col-xs-12 orders droporder" id="drop_<?php echo $i; ?>" style="min-height:150px;">
                                
                            </div>  
                            
                            <div class="col-sm-12 col-xs-12 drag_items" id="drop_<?php echo $i; ?>_total">
                            <?php if(isset($discount['unique_discount'])) : ?>
                                <input type="hidden" name="unique_discount" value="1">
                                    <?php endif; ?>
                                <div class="col-sm-9 col-xs-12">Total Items</div>
                                <div class="total_item col-sm-3 col-xs-12 text-right">0</div>
                                <input type="hidden" name="split[<?php echo $i;?>][total_item]" value="" class="total_items_hidden">
                                
                            </div>
                            <div class="col-sm-12 col-xs-12 drag_items" id="drop_<?php echo $i; ?>_price">
                                <div class="col-sm-9 col-xs-12">Total Price</div>
                                <div class="col-sm-3 col-xs-12 total_price text-right">0.00</div>
                               
                                    <input type="hidden" name="split[<?php echo $i;?>][total_price]" value="" class="total_hidden" id="subtotal_<?php echo $i; ?>">
                            </div>

                            <div id="drop_<?php echo $i; ?>_discount" class="col-sm-12 col-xs-12 drag_items">
                                <div class="col-sm-9 col-xs-12">Total Discount
                                     <span class="discount_total pull-right"  >0</span>
                                      <input class="discount_total_value" type="hidden" name="split[<?php echo $i;?>][itemdiscounts]" id="tdis_old_<?php  echo $i; ?>" >
                                </div>
                               
                                 <div class="col-sm-3 col-xs-12">
                                      <span class="discount pull-right" id="tds_old_<?php echo $i; ?>">0.00</span>
                                 <input class="item_dis_<?php   echo $i; ?>" type="hidden" name="split[<?php echo $i;?>][item_dis]" id="item_dis_<?php   echo $i; ?>">
                                 </div>
                            </div>

                            <div id="drop_<?php echo $i; ?>_discount_offer" class="col-sm-12 col-xs-12 drag_items" style="display: none">
                                <div class="col-sm-6 col-xs-12" >Offer Discount</div>
                                <div  class="col-sm-3 col-xs-12">
                                 <span class="pull-right" id="offer_<?php echo $i; ?>">0.00</span>                                 
                                  <input class="tot_dis_id<?php   echo $i; ?>" type="hidden" name="split[<?php echo $i;?>][tot_dis_value]" id="tot_dis_<?php   echo $i; ?>">
                                   
                                  
                                  </div>

                                 <div  class="col-sm-3 col-xs-12">
                                 <span class="offer_discount pull-right" id="tds1_<?php echo $i; ?>">0.00</span>                                 
                                  <input class="off_tot_dis_<?php   echo $i; ?>" type="hidden" name="split[<?php echo $i;?>][offer_dis]" id="off_tot_dis_<?php   echo $i; ?>">
                                  <input type="hidden" id="offer_discount_<?php   echo $i; ?>" name="split[<?php   echo $i; ?>][tot_dis1]" value="">
                                  </div>
                            </div>

                            <?php if(!isset($discount['unique_discount'])) : ?>
                            <div id="drop_<?php echo $i; ?>_discount_other" class="col-sm-12 col-xs-12 drag_items">
                                <div class="col-sm-6 col-xs-12">Customer Discount</div>
                                <div class="col-sm-3 col-xs-12">
                                   <?php if($Settings->customer_discount=='customer') : //customer dis?>
                                    <select style="display: "  name="split[<?=$i?>][order_discount_input]" class="form-control pos-input-tip order_discount_input" autocomplete="off" id="order_discount_input_<?php echo $i; ?>" count="<?php echo $i; ?>">
                                    <option value="0">No</option>
                                        <?php
                                        foreach ($customer_discount as $cusdis) {
                                            
                                        ?>
                                        <option value="<?php echo $cusdis->id; ?>" data-id="<?php echo $cusdis->id; ?>"><?php echo $cusdis->name; ?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                <?php elseif($Settings->customer_discount=='manual') : //manual?>
                                    <?php echo form_input('split['.$i.'][order_discount_input]', '', 'class="kb-pad order_discount_input text-right"  autocomplete="off" id="order_discount_input_'.$i.'" count="'. $i.'"'); ?>
                                <?php endif; ?>
                                </div>
                               
                                 <div  class="col-sm-3 col-xs-12">
                                 <span class="discount_other pull-right" id="tds_<?php echo $i; ?>">0.00</span>                                 
                                  <input class="discount_other_total" type="hidden" name="split[<?php echo $i;?>][discount_amount]" id="tdis_<?php   echo $i; ?>">
                                  </div>
                            </div>
                        <?php      endif; 
                        $getTaxType = $this->pos_settings->tax_type;
                       
                        $getTax = $this->site->getTaxRateByID($this->pos_settings->default_tax);//print_R($getTax);
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
                        ?><!--style="visibility: <?php echo $HideShow;?>;display:<?php echo $display; ?>"-->
                            <input type="hidden" name="split[<?php echo $i;?>][tax_rate]" id="tax_rate_<?php echo $i; ?>" class="tax-rate" value="<?php echo $getTax->rate; ?>">
                            <input type="hidden" name="split[<?php echo $i;?>][tax_type]" id="tax_type_<?php echo $i; ?>" class="tax-type" value="<?php echo $getTaxType; ?>">
                            
                            <div class="col-sm-12 col-xs-12  drag_items" >
                                <div class="col-sm-6 col-xs-12" id="drop_<?php echo $i; ?>_tax_dropdown">Tax 
                                
                               <?php
                                                        //$getTax = $this->site->getTaxRateByID($this->pos_settings->default_tax);print_R($getTax);
                                                        
                                                        ?>
                                                        <select name="split[<?php echo $i;?>][ptax]" class="form-control pos-input-tip ptax" id="ptax_<?php echo $i; ?>" count="<?php echo $i; ?>" style="display: none;">
                                                            <?php
                                                            foreach ($tax_rates as $tax) {
                                                            ?>
                                                            <option value="<?php echo $tax->id; ?>" <?php if($getTax->id == $tax->id){ echo 'selected'; }else{ echo ''; } ?> data-id="<?php echo $tax->rate; ?>"><?php echo $tax->name; ?></option>
                                                            <?php
                                                            }
                                                            ?>
                                                        </select>                           </div>
                               
                                <div class="col-sm-3 col-xs-12" id="drop_<?php echo $i; ?>_tax_select">
                                    <?php  if($getTaxType != 0){
                                    $taxtype = lang("exclusive");
                                }else {
                                   $taxtype = lang("inclusive");
                                 }?>
                                    <span style="font-size: 9px;"class="ttax2_old pull-right" id="ttax2_old_<?php echo $i; ?>"><?php echo '('.$taxtype.' - '.$getTax->name.')' ?></span>
                                </div>
                                
                                 <div id="drop_<?php echo $i; ?>_tax" class="col-sm-3 col-xs-12">
                                    <span class="tax pull-right" id="ttax2_<?php echo $i; ?>">0.00</span>
                                  <input class="tax_total" type="hidden" name="split[<?php echo $i;?>][tax_amount]" id="tax2_<?php echo $i; ?>">
                                 </div>
                            </div>
                            
                            
                            
                           
                            <div class="col-sm-12 col-xs-12 drag_items">
                                <div class="col-sm-9 col-xs-12">Grand Total</div>
                                <div id="drop_<?php echo $i; ?>_grand" class="col-sm-3 col-xs-12">
                                <input type="hidden" name="split[<?php echo $i;?>][grand_total]" id="grand_total_<?php echo $i;?>" class="grand_total">
                                 <input type="hidden" name="split[<?php echo $i;?>][round_total]" id="round_total_<?php echo $i;?>" class="round_total">
                                <span class="grand pull-right" id="gtotal_<?php echo $i; ?>">0.00</span></div>
                            </div>
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
                    </div>
                    
                    <?php
                    }
                    ?>
                    
<div class="modal-footer">
       <button class="btn btn-block btn-lg btn-primary" id="submit-sale"><?=lang('submit');?></button>
</div>
                     </div>
                     <?php
                        echo form_hidden('remove_image','No');
                        echo form_hidden('action', 'MANUALSPLITBILL-SUBMIT');
                    ?>
                    
                    <?php
                        echo form_close();
                     ?>
                     
                    <div style="clear:both;"></div>
                </div>
                <div style="clear:both;"></div>
            </div>
        </div>
    </div>
</div>


<?php
$this->load->view($this->theme . 'pos/pos_footer');
?>
<div class="modal" id="RecipeviewModal" tabindex="-1" role="dialog" aria-labelledby="CancelorderModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                Recipe View
                <button type="button" class="close closemodal" data-dismiss="modal"><span aria-hidden="true">
                    <i class="fa fa-2x">&times;</i></span>
                    <span class="sr-only"></span>
                </button>
                <h4 class="modal-title" id="cmModalLabel"></h4>
            </div>
            <div class="modal-body" id="pr_popover_content">
                <div class="col-xs-12 rightdrag_table">
                            
                            <div class="rec_price1"><?=lang("recipe");?></div>
                            <div class="rec_price2 "><?=lang("price");?></div>
                            <div class="rec_price3"><?=lang("qty");?></div>
                            <div class="rec_price4 "><?=lang("discount");?></div>
                                <div class="rec_price5 "><?=lang("discount amount");?></div>
                               
                            <div class="rec_price7 "><?=lang("subtotal");?></div>
                    </div>
                 <div id="Recipeview_row">
                    
                 </div>
                 <div class="clearfix"></div>
            </div>
            <div class="clearfix"></div>
            
        </div>
    </div>
</div>

<?php unset($Settings->setting_id, $Settings->smtp_user, $Settings->smtp_pass, $Settings->smtp_port, $Settings->update, $Settings->reg_ver, $Settings->allow_reg, $Settings->default_email, $Settings->mmode, $Settings->timezone, $Settings->restrict_calendar, $Settings->restrict_user, $Settings->auto_reg, $Settings->reg_notification, $Settings->protocol, $Settings->mailpath, $Settings->smtp_crypto, $Settings->corn, $Settings->customer_group, $Settings->srampos_username, $Settings->purchase_code);?>


<script>
var recipe_variant = 0, shipping = 0, p_page = 0, per_page = 0, pro_limit = <?= $pos_settings->pro_limit; ?>,
        brand_id = 0, obrand_id = 0, cat_id = "<?=$pos_settings->default_category?>", ocat_id = "<?=$pos_settings->default_category?>", sub_cat_id = 0, osub_cat_id,
        count = 1, an = 1, DT = <?=$Settings->default_tax_rate?>,
        recipe_tax = 0, invoice_tax = 0, recipe_discount = 0, order_discount = 0, total_discount = 0, total = 0, total_paid = 0, grand_total = 0,round_total = 0,
        KB = <?=$pos_settings->keyboard?>, tax_rates =<?php echo json_encode($tax_rates); ?>;
    var protect_delete = <?php if (!$Owner && !$Admin) {echo $pos_settings->pin_code ? '1' : '0';} else {echo '0';} ?> /*billers = <?= json_encode($posbillers); ?>, biller = <?= json_encode($posbiller); ?>;*/
    
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
        mywindow.document.write('<html><head><title>Print</title>');
        mywindow.document.write('<link rel="stylesheet" href="<?=$assets?>styles/helpers/bootstrap.min.css" type="text/css" />');
        mywindow.document.write('</head><body >');
        mywindow.document.write(data);
        mywindow.document.write('</body></html>');
        mywindow.print();
        mywindow.close();
        return true;
    }
    <?php }
    ?>
</script>

<script>
function myFunction(id) {
    var id = id;
    var splitid = id.substring(5);
    
    $('#drop_'+splitid+' .item_recipe input').attr('name',$('.item_recipe input').attr('name').replace(/\[\d+](?!.*\[\d)/, '['+splitid+']'));

            $('#drop_'+splitid+' .item_id input').attr('name',$('.item_id input').attr('name').replace(/\[\d+](?!.*\[\d)/, '['+splitid+']'));

             $('#drop_'+splitid+' .item_code input').attr('name',$('.item_code input').attr('name').replace(/\[\d+](?!.*\[\d)/, '['+splitid+']'));

            $('#drop_'+splitid+' .item_type input').attr('name',$('.item_type input').attr('name').replace(/\[\d+](?!.*\[\d)/, '['+splitid+']'));

            $('#drop_'+splitid+' .item_price input').attr('name',$('.item_price input').attr('name').replace(/\[\d+](?!.*\[\d)/, '['+splitid+']'));

            $('#drop_'+splitid+' .item_quantity input').attr('name',$('.item_quantity input').attr('name').replace(/\[\d+](?!.*\[\d)/, '['+splitid+']'));
            
            $('#drop_'+splitid+' .item_discount input').attr('name',$('.item_discount input').attr('name').replace(/\[\d+](?!.*\[\d)/, '['+splitid+']')); 

            $('#drop_'+splitid+' .item_tax input').attr('name',$('.item_tax input').attr('name').replace(/\[\d+](?!.*\[\d)/, '['+splitid+']'));           
            
            $('#drop_'+splitid+' .item_discount_id input').attr('name',$('.item_discount_id input').attr('name').replace(/\[\d+](?!.*\[\d)/, '['+splitid+']'));
            
            $('#drop_'+splitid+' .item_subtotal input').attr('name',$('.item_subtotal input').attr('name').replace(/\[\d+](?!.*\[\d)/, '['+splitid+']'));
            $('#drop_'+splitid+' .other_item_discount input').attr('name',$('.other_item_discount input').attr('name').replace(/\[\d+](?!.*\[\d)/, '['+splitid+']'));
            
            var count = 0;
            count = $('#'+id+' .item_recipe').length;
            $('#'+id+'_total .total_item').text(count);
            $('#'+id+'_total .total_items_hidden').val(count);

            var sum = 0;
            var dis = 0;
            //var Item_dis = 0;
            
            $('#'+id+' .item_subtotal').each(function(){
                sum += parseFloat(this.innerHTML)
            });
            $('#'+id+'_price .total_price').text(formatDecimal(sum));
            $('#'+id+'_price .total_hidden').val(formatDecimal(sum));
            //$('#'+id+'_discount .discount').text(sum);
            //console.log('sum:'+sum)

            $('#'+id+' .all-item-discounts').each(function(){
                //console.log($(this).val())
                dis += parseFloat($(this).val());
                //Item_dis +=parseFloat($(this).val());
            });
console.log('dis'+unique_discount)
            var str = <?php echo json_encode($this->site->TotalDiscount()); ?>;
            
            var res = str.toString().split(',');     

            var value = 0;
             value =  parseFloat(sum) - parseFloat(dis);
            var totdiscount = '0';
            var totdiscount1 = 0;
            if((!unique_discount || only_offer_dis==1) && res[0] != 0)
            {
                if(res[3] == 'percentage_discount'){
                    totdiscount = res[1]+'%';
                }else{
                    totdiscount =res[1];
                }
          }//console.log(totdiscount)
          if (totdiscount.indexOf("%") !== -1) {
             var pds = res[1];             
              if (!isNaN(pds)) {
                    totdiscount1 = formatDecimal(parseFloat(((value) * parseFloat(pds)) / 100), 4);
                }else {
                    totdiscount1 = parseFloat(totdiscount);
                }
          }
          else
          {
                totdiscount1 = parseFloat(totdiscount);
          }
          val = false;
         
           if(totdiscount1 < value)
           { 
            /*D.id,',',D.discount, ',',D.amount,',', D.discount_type*/
            val = value - totdiscount1;
            if (totdiscount1!=0) {
           
             $('#'+id+'_discount_offer').css('display','block');
            
             }
             
            //$('#tds_old_'+splitid).text(dis);
            //$('#item_dis_'+splitid).val(dis);
            $('#tds_old_'+splitid).text(parseFloat(dis));
            $('#item_dis_'+splitid).val(parseFloat($('#subtotal_'+splitid).val())-parseFloat(dis));
            $('#tds_'+splitid).text();
            $('#offer_'+splitid).text(totdiscount);
            $('#tot_dis_'+splitid).val(totdiscount1);
            $('#off_tot_dis_'+splitid).val(totdiscount1);
            $('#offer_discount_'+splitid).val(parseFloat($('#subtotal_'+splitid).val())-parseFloat(dis)-totdiscount1);
            $('#tds1_'+splitid).text(totdiscount1);
            /*$('#'+id+'_price .total_hidden').val(sum);*/
           } 
           else
           {
             $('#'+id+'_discount_offer').css('display','none');
             $('#tds1_'+splitid).text('');
             $('#tds_old_'+splitid).text(0)
             $('#item_dis_'+splitid).val(0);
             $('#offer_'+splitid).text('');
             $('#tds_'+splitid).text(sum);
             $('#offer_discount_'+splitid).val(0);
           }
           var final_val = 0;
           if(val)
            { //console.log('val:'+val)
                final_val = val;
            }
            else
            {
                //console.log('value:'+value)
                final_val = value;
            }
            

            $('#'+id+'_discount .discount_total').text(dis);
            $('#'+id+'_discount .discount_total_value').val(dis);
            //$('#'+id+'_discount_other .order_discount_input').val(0);
           // $('#'+id+'_discount_other .discount_other_total').val(dis);

            var pr_tax = $('#'+id+'_tax_dropdown .ptax').children(":selected").attr("data-id");
            var pr_tax = $('#tax_rate_'+splitid).val();
            var pr_tax_val = 0;
            //console.log('fv:'+final_val)
            if (pr_tax !== null && pr_tax != 0) {
                $.each(tax_rates, function () {                       
                    pr_tax_val = parseFloat(((final_val) * parseFloat(pr_tax)) / 100);
                    pr_tax_rate = (pr_tax) + '%';                
                });
            }

//            var final_tax = parseFloat(pr_tax_val);     
//            console.log('final_tax:'+formatMoney(final_tax))
//            $('#'+id+'_tax_select .ttax2_old').text(formatMoney(final_tax));         
//            $('#'+id+'_tax .tax').text(formatMoney(final_tax));
//            $('#'+id+'_tax .tax_total').val(final_tax);
//           
//            var final_total =  parseFloat(final_val) + parseFloat(final_tax);     
//console.log(final_total)
//            //$('#'+id+'_discount_other .discount_other').text();
//            $('#'+id+'_grand .grand').text(formatMoney(final_total));
//            $('#'+id+'_grand .grand_total').val(final_total);
//            if ($('.split-form-'+splitid).find('.item_quantity').length) {
//                $('#order_discount_input_'+splitid).trigger('change');
//            }


            itemdiscount = $('#tdis_old_'+splitid).val();
            offerdiscount = $('#off_tot_dis_'+splitid).val();
            discount = parseFloat(itemdiscount)+parseFloat(offerdiscount);
            subtotal = $('#subtotal_'+splitid).val();
            var final_discount =  parseFloat(discount);
            var final_amount = parseFloat(subtotal) - parseFloat(final_discount);
            
            var final_tax;
            var final_tax_amount;
            var taxtype = $('#tax_type_'+splitid).val();
            if(taxtype != 0)
            {
                final_tax = parseFloat(pr_tax_val);
                final_tax_amount = parseFloat(final_tax);
                final_amount = parseFloat(final_amount+final_tax);
                finalamount = parseFloat(final_amount);
                sub_val = parseFloat(finalamount);
            }
            else
            {  
                
                sub_val = final_amount/((pr_tax_val/final_amount)+1);
                final_tax_amount = sub_val * (pr_tax / 100);
                final_amount = sub_val+final_tax_amount; 
            }
       
            //alert(final_amount);return false;
            //if(final_amount >= 0 ){
                //$('#tdis_'+ splitid).val(formatDecimal(input_discount));
                //$('#tds_'+ splitid).text(formatMoney(input_discount));
                $('#ttax2_'+ splitid).text(formatMoney(final_tax_amount));
                //$('#'+id+'_tax_select .ttax2_old').text(formatMoney(final_tax_amount)); 
                $('#'+id+'_tax .tax_total').val(formatDecimal(final_tax_amount));
                $('#tax_amount_'+ splitid).val(formatDecimal(final_tax_amount));
            
                $('#gtotal_'+ splitid).text(formatMoney(final_amount));
                $('#grand_total_'+ splitid).val(formatDecimal(sub_val));
                $('#round_total_'+ splitid).val(formatDecimal(sub_val));
           // }
           if ($('.split-form-'+splitid).find('.item_quantity').length) {
               $('#order_discount_input_'+splitid).trigger('change');
           }
            
}
  
$(".draggable").draggable({ cursor: "crosshair", revert: "invalid"});

$(".orders").droppable({ accept: ".draggable", 
   drop: function(event, ui) {
            console.log("drop");
            $(this).removeClass("border").removeClass("over");
            var dropped = ui.draggable;
            var droppedOn = $(this);
            $(dropped).detach().css({top: 0,left: 0}).appendTo(droppedOn); 
            var id = $(this).attr('id');
             setTimeout(function(){
                 myFunction(id);
              }, 600)
            
        }, 
  over: function(event, elem) {
        var id = $(this).attr('id');
        myFunction(id);
        setTimeout(function(){ 
            //myFunction(id);
        }, 600)
            /*var id = $(this).attr('id'); */    
            /* var count = $('#'+id+' .item_recipe').length;
            $('#'+id+'_total .total_item').text(count);
            $('#'+id+'_total .total_items_hidden').val(count);
            $(this).addClass("over");*/
            console.log("over");            
        },
   out: function(event, elem) {
            $(this).removeClass("over");
            var id = $(this).attr('id');
            myFunction(id);
            setTimeout(function(){
                //myFunction(id);
            }, 500)
            setTimeout(function(){
                myFunction(id);
            }, 1000)
            //setTimeout(function(){
            //    myFunction(id);
            //}, 1500)
            setTimeout(function(){
                myFunction(id);
            }, 2000)
            //setTimeout(function(){
            //    myFunction(id);
            //}, 3000)
            //setTimeout(function(){
            //    myFunction(id);
            //}, 4000)
            /*var id = $(this).attr('id');     
            var count = $('#'+id+' .item_recipe').length;
            $('#'+id+'_total .total_item').text(count);
            $('#'+id+'_total .total_items_hidden').val(count);*/
        }
    });
$(".orders").sortable();
</script>


<script type="text/javascript">

$(".rec_price_che").hide();
$(".checkmark").click(function(e) {
    var id = $(this).attr('id');
    var recipe = $("#drop_"+id).html();
    $("#Recipeview_row").html(recipe);
    $("#Recipeview_row .rec_price_che").show();
    $('#RecipeviewModal').show();
});     

$('.closemodal').click(function () {
    $("#Recipeview_row").html('');
    $('#RecipeviewModal').hide(); 
});

$(document).on('click', '#submit-sale', function () {
    $('#pos-manualsplitbill-form').submit();
    return false;
   
});


//$(document).on('change', '.order_discount_input, .ptax', function () {
//    
//    var find_attr = $(this).attr('count');
//    var subtotal  = $('#tds_old_'+ find_attr).text();
//    var tax_amount  = $('#tax2_'+ find_attr).val();
//    var unit_price = parseFloat($('#tds_'+ find_attr).text());
//    var old_discount = $('#tdis_old_'+ find_attr).val();
//    var ds = $('#order_discount_input_'+ find_attr).val() ? $('#order_discount_input_'+ find_attr).val() : '0';
//    var pr_tax = $('#ptax_'+find_attr).children(":selected").data("id");
//    
//    if (ds.indexOf("%") !== -1) {            
//        var pds = ds.split("%");
//        if (!isNaN(pds[0])) {
//            item_discount = parseFloat(((unit_price) * parseFloat(pds[0])) / 100);
//        } else {
//            item_discount = parseFloat(ds);
//        }
//    } else {            
//        item_discount = parseFloat(ds);
//    }
//    
//    
//    var final_discount =  parseFloat(item_discount)  + parseFloat(old_discount);
//    var final_discount_amount = parseFloat(subtotal) - parseFloat(item_discount);        
//    var pr_tax_val = 0;
//    if (pr_tax !== null && pr_tax != 0) {
//        $.each(tax_rates, function () {                       
//            pr_tax_val = parseFloat(((final_discount_amount) * parseFloat(pr_tax)) / 100);
//            pr_tax_rate = (pr_tax) + '%';                
//        });
//    }
//
//    var final_tax = parseFloat(pr_tax_val);
//    var final_tax_amount = parseFloat(final_discount_amount) + parseFloat(final_tax);
//    var checkamount = (final_tax_amount) % 100;
//    if(checkamount < 50){
//        var final_total_amount = parseFloat(final_tax_amount) - checkamount;
//    }else{
//        var final_total_amount = (100 - checkamount) + (parseFloat(final_tax_amount));
//    }
//    
//    if(final_total_amount >= 0 ){
//        $('#tdis_'+ find_attr).val(parseFloat(final_discount));
//        $('#tds_'+ find_attr).text(parseFloat(final_discount_amount));
//        $('#tax2_'+ find_attr).val(parseFloat(final_tax));
//        $('#ttax2_'+ find_attr).text(parseFloat(final_tax_amount));
//        $('#ttax2_old_'+ find_attr).text(parseFloat(final_tax));
//        $('#gtotal_'+ find_attr).text(parseFloat(final_total_amount));
//        $('#grand_total_'+ find_attr).val(parseFloat(final_total_amount));
//    }else{
//        bootbox.alert('Discount should not grater than total');
//        $('#order_discount_input_'+ find_attr).val(0);
//        return false;   
//    }
//    
//    
//});

$(document).on('change', '.order_discount_input', function () {
    $this_obj = $(this);
    ds  = $this_obj.val();
    if(ds == ''){
        ds = 0;
    }
    else{
        ds = ds;
    }
    recipeids = [];recipeqtys = [];recipevariantids = [];addonsubtotal = [];manual_item_discount = [];
    $split = $this_obj.attr('count');
    $('.split-form-'+$split+' .recipe-id').each(function(n,v){
        recipeids.push($(this).val());
    });
    recipeids = recipeids.toString();
    $('.split-form-'+$split+' .recipe-quantity').each(function(n,v){
        recipeqtys.push($(this).val());
    });
    recipeqtys = recipeqtys.toString();

    $('.split-form-'+$split+' .item_variant_id').each(function(n,v){
        if($(this).val() !=''){
            recipevariantids.push($(this).val());
        }  
    });
    recipevariantids = recipevariantids.toString();

    $('.split-form-'+$split+' .addon_subtotal').each(function(n,v){
        if($(this).val() !=''){
            addonsubtotal.push($(this).val());      
        }  
    });
    addonsubtotal = addonsubtotal.toString();

   $('.split-form-'+$split+' .manual_item_discount').each(function(n,v){
        if($(this).val() !=''){
            manual_item_discount.push($(this).val());
        }  
    });
    manual_item_discount = manual_item_discount.toString();

    // console.log(addonsubtotal);return false;
    input_discount = 0;
    itemdiscount = $('#tdis_old_'+$split).val();
    offerdiscount = $('#off_tot_dis_'+$split).val();
    discount = parseFloat(itemdiscount)+parseFloat(offerdiscount);
    subtotal = $('#subtotal_'+$split).val();
    if(recipeids!=''){
        <?php if($Settings->customer_discount=='customer') : ?>
        if(ds !=0){
            $.ajax({
                    type: 'POST',
                    url: '<?=admin_url('pos/manualsplit_calculate_customerdiscount');?>',                    
                    dataType: "json",
                     async : false,
                    data: {
                        recipeids: recipeids,recipeqtys: recipeqtys,discountid:$this_obj.val(),recipevariantids:recipevariantids,addonsubtotal:addonsubtotal,manual_item_discount:manual_item_discount
                    },
                    success: function (data) {
                        /*$(this).removeClass('ui-autocomplete-loading');*/
                        console.log(data);
                        input_discount += data;
                    }
               });
        }
        <?php elseif($Settings->customer_discount=='manual') : ?>
        if (ds.indexOf("%") !== -1) {            
            var pds = ds.split("%");
            if (!isNaN(pds[0])) {
            input_discount = parseFloat(((subtotal) * parseFloat(pds[0])) / 100);
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
        var pr_tax = $('#tax_rate_'+$split).val();
        var taxtype = $('#tax_type_'+$split).val();
        var pr_tax_val = 0;
        if (pr_tax !== null && pr_tax != 0) {
            $.each(tax_rates, function () {                       
            pr_tax_val = parseFloat(((final_amount) * parseFloat(pr_tax)) / 100);
            pr_tax_rate = (pr_tax) + '%';                
            });
        }
        var final_tax;
        var final_tax_amount;
    
        if(taxtype != 0)
        {
            final_tax = parseFloat(pr_tax_val);
            final_tax_amount = parseFloat(final_tax);
            final_amount = parseFloat(final_amount+final_tax);
            finalamount = parseFloat(final_amount);
            sub_val = parseFloat(finalamount);
        }
        else
        {  
            
            sub_val = final_amount/((pr_tax_val/final_amount)+1);
            final_tax_amount = sub_val * (pr_tax / 100);
            final_amount = sub_val+final_tax_amount; 
        }
       
        //alert(final_amount);return false;
        if(final_amount >= 0 ){
            $('#tdis_'+ $split).val(formatDecimal(input_discount));
            $('#tds_'+ $split).text(formatMoney(input_discount));
            $('#ttax2_'+ $split).text(formatMoney(final_tax_amount));
            $('#tax_amount_'+ $split).val(formatDecimal(final_tax_amount));
        
            $('#gtotal_'+ $split).text(formatMoney(final_amount));
            $('#grand_total_'+ $split).val(formatDecimal(final_amount));
            $('#round_total_'+ $split).val(formatDecimal(final_amount));
            
            //$('.split-form-'+$split+' .ttax2_old').text(formatMoney(final_tax_amount)); 
            $('.split-form-'+$split+' .tax_total').val(formatDecimal(final_tax_amount));
        }else{
            bootbox.alert('Discount should not grater than total');
            $('#order_discount_input_'+ $split).val('');
            final_amount = formatDecimal(unit_price);        
            $('#tdis_'+ $split).val(formatDecimal(input_discount));
            $('#tds_'+ $split).text(formatMoney(input_discount));
            $('#ttax2_'+ $split).text(formatMoney(final_tax_amount));
        
            $('#gtotal_'+ $split).text(formatMoney(final_amount));
            $('#grand_total_'+ $split).val(formatDecimal(final_amount));
            $('#round_total_'+ $split).val(formatDecimal(final_amount));
            return false;   
        }
    }
});


function formatDecimal(x, d) {
    if (!d) { d = site.settings.decimals; }
    return parseFloat(accounting.formatNumber(x, d, '', '.'));
}
$('.table_id').click(function () {
    var order_type = $('#order_type').val();        
    var table_id = $(this).val();
    var url = '<?php echo  admin_url('pos') ?>';
    //window.location.href= url +'/?order='+order_type+'&table='+table_id;  
    $('#modal-loading').show();
        
        $.ajax({
            type: "get",
            url: "<?=admin_url('pos/tablecheck');?>",
            data: {table_id: table_id, order_type: order_type},
            dataType: "json",
            success: function (data) {
                
                if(data.status == 'success'){
                    window.location.href= url +'/order_table/?table='+table_id; 
                }else{
                    window.location.href= url +'/?order='+order_type+'&table='+table_id;    
                }
            }

        }).done(function () {
            $('#modal-loading').hide();
        });
    
});
var site = <?=json_encode(array('url' => base_url(), 'base_url' => admin_url('/'), 'assets' => $assets, 'settings' => $Settings, 'dateFormats' => $dateFormats))?>, pos_settings = <?=json_encode($pos_settings);?>;
function formatMoney(x, symbol) {
    if(!symbol) { symbol = ""; }
    if(site.settings.sac == 1) {
        return (site.settings.display_symbol == 1 ? site.settings.symbol : '') +
            ''+formatSA(parseFloat(x).toFixed(site.settings.decimals)) +
            (site.settings.display_symbol == 2 ? site.settings.symbol : '');
    }
    var fmoney = accounting.formatMoney(x, symbol, site.settings.decimals, site.settings.thousands_sep == 0 ? ' ' : site.settings.thousands_sep, site.settings.decimals_sep, "%s%v");
    return (site.settings.display_symbol == 1 ? site.settings.symbol : '') +
        fmoney +
        (site.settings.display_symbol == 2 ? site.settings.symbol : '');
}
</script>
<script type="text/javascript" src="<?=$assets?>js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/perfect-scrollbar.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/select2.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/custom.js"></script>
<script type="text/javascript" src="<?=$assets?>js/jquery.calculator.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/bootstrapValidator.min.js"></script>
<script type="text/javascript" src="<?=$assets?>pos/js/plugins.min.js"></script>
<script type="text/javascript" src="<?=$assets?>pos/js/parse-track-data.js"></script>
<!-- <script type="text/javascript" src="<?=$assets?>pos/js/pos.ajax.js"></script> -->
<script type="text/javascript" src="<?=$assets?>js/dragscrollable.js"></script>
<script type="text/javascript" src="<?=$assets?>js/dragscroll.js"></script>
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
</script>
</body>
</html>
