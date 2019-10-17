 <script type="text/javascript">
	function openFullScreen() {
  var doc = window.document;
  var docEl = doc.documentElement;

  var requestFullScreen = docEl.requestFullscreen || docEl.mozRequestFullScreen || docEl.webkitRequestFullScreen || docEl.msRequestFullscreen;
  var cancelFullScreen = doc.exitFullscreen || doc.mozCancelFullScreen || doc.webkitExitFullscreen || doc.msExitFullscreen;

  if(!doc.fullscreenElement && !doc.mozFullScreenElement && !doc.webkitFullscreenElement && !doc.msFullscreenElement) {
    requestFullScreen.call(docEl);
  }
  else {
    cancelFullScreen.call(doc);
  }
}
function isFullScreen() {
  var doc = window.document;
  var docEl = doc.documentElement;

  var requestFullScreen = docEl.requestFullscreen || docEl.mozRequestFullScreen || docEl.webkitRequestFullScreen || docEl.msRequestFullscreen;
  var cancelFullScreen = doc.exitFullscreen || doc.mozCancelFullScreen || doc.webkitExitFullscreen || doc.msExitFullscreen;

  if(!doc.fullscreenElement && !doc.mozFullScreenElement && !doc.webkitFullscreenElement && !doc.msFullscreenElement) {
    return false;
  }
  else {
    return true;
  }
}
</script>


<link rel="stylesheet" href="<?=$assets?>styles/jquery.mCustomScrollbar.css">
  <script src="<?=$assets?>js/jquery.mCustomScrollbar.concat.min.js"></script>
   <div class="pos_header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12 pos_logo_front">
             <?php if ($this->Settings->logo3) { ?>
                <img src="<?=base_url()?>assets/uploads/logos/<?=$this->Settings->logo3?>" alt="<?=$this->Settings->site_name?>" class="sram_table_logo" />
           <?php   } ?>


                <!-- <a href="<?php echo site_url().'admin/pos'; ?>"><img src="<?=$assets?>images/front_logo.png" alt="Pos_logo" title="Pos-logo"></a> -->
            </div>
            <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12 custom_details">
                <ul>
                    
                    <!-- <li class="dropdown"><a data-toggle="dropdown" href="javascript:void(0);"><img src="<?=$assets?>images/table.png"> <br><?=lang('tables')?> <img src="<?=$assets?>images/angle_down.png" class="down_img"></a>
                        <ul class="dropdown-menu">
                        	<?php if($this->sma->actionPermissions('table_view')){ ?>
                            <li><a href="<?php if(!$isNightauditDone) {?> javascript:void(0)<?php } else { echo admin_url('pos/?order=1');} ?>"   <?php if(!$isNightauditDone) {echo 'class="disabled" title="do Nightaudit to enable further orders"';} ?>><?php echo lang("table") ?></a></li>
                            <?php } ?>
                            <?php if($this->Settings->bbq_enable){ ?>
                            <li><a href="<?= admin_url('pos/bbq_tables?order=4'); ?>"><?php echo lang("BBQ_table") ?></a></li>
                            <?php } ?>
                            
                        </ul>
                    </li> -->
                    
                                       
                    <?php
					
					 if($this->sma->actionPermissions('dinein') || $this->sma->actionPermissions('takeaway') || $this->sma->actionPermissions('door_delivery')){
					?>
                    
                    <li class="dropdown"><a data-toggle="dropdown" href="javascript:void(0);"><img src="<?=$assets?>images/dine.png"> <br><?=lang('punch_order')?> <br><p><?php echo lang("native_punch_order") ?></p> <img src="<?=$assets?>images/angle_down.png" class="down_img"></a>
                        <ul class="dropdown-menu">
                        	<?php if($this->sma->actionPermissions('dinein')){ ?>
                            <li><a href="<?php if(!$isNightauditDone) {?> javascript:void(0)<?php } else { echo  admin_url('pos/?order=1'); }?>" <?php if(!$isNightauditDone) {echo 'class="disabled" title="do Nightaudit to enable further orders"';} ?>><?php echo lang("dine_in") ?><p><?php echo lang("native_dine_in​​​") ?></p></a></li>
                            <?php } ?>
                            <?php if($this->sma->actionPermissions('takeaway')){ ?>
                            <li><a href="<?php if(!$isNightauditDone) {?> javascript:void(0)<?php } else { echo  admin_url('pos/?order=2'); }?>" <?php if(!$isNightauditDone) {echo 'class="disabled" title="do Nightaudit to enable further orders"';} ?>><?php echo lang("take_away") ?><p><?php echo lang("native_take_away") ?></p></a></li>
                            <?php } ?>
                            <?php if($this->sma->actionPermissions('door_delivery')){ ?>
                            <li><a href="<?php if(!$isNightauditDone) {?> javascript:void(0)<?php } else { echo  admin_url('pos/?order=3'); }?>" <?php if(!$isNightauditDone) {echo 'class="disabled" title="do Nightaudit to enable further orders"';} ?>><?php echo lang("door_delivery") ?><p><?php echo lang("native_door_delivery") ?></p></a></li>
                            <?php } ?>
                            <?php if($this->Settings->bbq_enable){ ?>
                            <li><a href="<?php if(!$isNightauditDone) {?> javascript:void(0)<?php } else { echo  admin_url('pos/bbq_tables?order=4'); }?>" <?php if(!$isNightauditDone) {echo 'class="disabled" title="do Nightaudit to enable further orders"';} ?>><?php echo lang("bbq") ?></a></li>
                            <?php } ?>
                            
                        </ul>
                    </li>
                    <?php
					 }
					 if($this->sma->actionPermissions('orders')){
					?>
                    <li class="dropdown"><a data-toggle="dropdown" href="javascript:void(0);"><img src="<?=$assets?>images/dine.png"> <br><?=lang('view_orders')?><br><p><?php echo lang("native_view_orders") ?></p> <img src="<?=$assets?>images/angle_down.png" class="down_img"></a>
                        <ul class="dropdown-menu">
                        	<?php if($this->sma->actionPermissions('dinein_orders')){ ?>
                            <li><a href="<?= admin_url('pos/order_table'); ?>"><?php echo lang("dine_in") ?> <p><?php echo lang("native_dine_in​​​") ?></p></a></li>
                             <?php } ?>
                            <?php if($this->sma->actionPermissions('takeaway_orders')){ ?>
                            <li><a href="<?= admin_url('pos/order_takeaway'); ?>"><?php echo lang("take_away") ?><p><?php echo lang("native_take_away") ?></p></a></li>
                             <?php } ?>
                            <?php if($this->sma->actionPermissions('door_delivery_orders')){ ?>
                            <li><a href="<?= admin_url('pos/order_doordelivery'); ?>"><?php echo lang("door_delivery") ?><p><?php echo lang("native_door_delivery") ?></p></a></li>
                            <?php } ?>
                            <?php if($this->Settings->bbq_enable){ ?>
                            <li><a href="<?= admin_url('pos/order_bbqtable'); ?>"><?php echo lang("BBQ_orders") ?></a></li>
                            <?php } ?>
                           

                        </ul>
                    </li>
                    
                    <?php
                    }
                    ?>
                   
                    
                   
                      <?php
                    if($this->sma->actionPermissions('kitchens') ){
                    ?>
                    <li><a href="<?= admin_url('pos/order_kitchen'); ?>"><img src="<?=$assets?>images/loyalty.png"> <br><?=lang('kitchen')?><br><p><?php echo lang("native_kitchen") ?></p></a></li>
                    <?php
                    }
                    ?>
                     <?php
                    if($this->sma->actionPermissions('billing') ){
                    ?>
                     <li class="dropdown"><a data-toggle="dropdown"   href="javascript:void(0);"><img src="<?=$assets?>images/dine.png"> <br><?=lang('billing')?><p><?php echo lang("native_billing") ?></p><img src="<?=$assets?>images/angle_down.png" class="down_img"></a>
                        <ul class="dropdown-menu">
                        	 <?php
							if($this->sma->actionPermissions('dinein_bils') ){
							?>
                            <li><a href="<?= admin_url('pos/order_biller/?type=1'); ?>"><?php echo lang("dine_in") ?><p><?php echo lang("native_dine_in​​​") ?></p></a></li>
                            <?php
							}
							?>
							 <?php
							if($this->sma->actionPermissions('takeaway_bils') ){
							?>
                            <li><a href="<?= admin_url('pos/order_biller/?type=2'); ?>"><?php echo lang("take_away") ?><p><?php echo lang("native_take_away") ?></p></a></li>
                            <?php
							}
							?>
							 <?php
							if($this->sma->actionPermissions('door_delivery_bils') ){
							?>
                            <li><a href="<?= admin_url('pos/order_biller/?type=3'); ?>"><?php echo lang("door_delivery") ?></a><p><?php echo lang("native_door_delivery") ?></p></li>
                            <?php
							}
							?>
                            <?php if($this->Settings->bbq_enable){ ?>
                            <li><a href="<?= admin_url('pos/biller_bbqtable'); ?>"><?php echo lang("BBQ") ?></a></li>
                            <li><a href="<?= admin_url('pos/biller_bbqconsolidated'); ?>"><?php echo lang("bbq_with_dine_in") ?></a></li>
                            <?php } ?>
                          <li>
                             <a href="<?= admin_url('pos/reprinter'); ?>"><?php echo lang("bill_reprint") ?><p><?php echo lang("native_bill_reprint") ?></p>
                             </a>
                          </li>
                          <?php if($this->Settings->bbq_enable){ ?>
              				<li> <a href="<?= admin_url('pos/bbqreprinter'); ?>"><?php echo lang("BBQ_bill_reprint") ?> </a> </li>
                            <li> <a href="<?= admin_url('pos/consolidatedreprinter'); ?>"><?php echo lang("Consolidated_bill_reprint") ?> </a> </li>
                            <li> <a href="<?= admin_url('pos/bbqitem_return'); ?>"><?php echo lang("BBQ_item_return") ?> </a> </li>
						  <?php } ?>

                        </ul>
                    </li>
					<?php if($this->sma->actionPermissions('report_view')){?>
                    <li class="dropdown"><a data-toggle="dropdown"  href="javascript:void(0);"><img src="<?=$assets?>images/dine.png"> <br><?=lang('reports')?><p><?php echo lang("native_reports") ?></p><img src="<?=$assets?>images/angle_down.png" class="down_img"></a>
                        <ul class="dropdown-menu">
                     <?php
        							if($this->sma->actionPermissions('today_item_report') ){ ?>
                          <li><a href="<?= admin_url('pos/reports/?type=1'); ?>"><?=lang('today_itemsale_report')?></a><p><?php echo lang("native_today_itemsale_report") ?></p></li>
                      <?php
        							}
					     		?>
							 <?php if($this->sma->actionPermissions('daywise_report') ) { ?>
                  <li><a href="<?= admin_url('pos/reports/?type=2'); ?>"><?=lang('daywise_report')?></a><p><?php echo lang("native_daywise_report") ?></p></li>
              <?php } ?>

							 <?php if($this->sma->actionPermissions('cashierwise_report') ) { ?>
                    <li><a href="<?= admin_url('pos/reports/?type=3'); ?>"><?=lang('cashierwise')?></a><p><?php echo lang("native_cashierwise") ?></p></li>
              <?php } ?>

						      	<li><a href="<?= admin_url('pos/reports/?type=4'); ?>"><?=lang('pos_settle_report')?></a><p><?php echo lang("native_pos_settle_report") ?></p></li>
                   <?php if($this->sma->actionPermissions('shifttime_report') ) { ?>
                        <li><a href="<?= admin_url('pos/reports/?type=5'); ?>"><?=lang('shifttime_report')?></a><p><?php echo lang("native_shifttime_report") ?></p></li>
                  <?php } ?>

                        </ul>
               </li>
					<?php }  if($pos_settings->loyalty_option == 1) {?>
                    <li class ="<?php if($this->uri->segment(3) == 'order_kitchen'){echo 'active';}?> header_links"><a href="<?= admin_url('pos/loyalty'); ?>"><img src="<?=$assets?>images/loyalty.png"> <br><?=lang('loyalty')?></a></li>

                    <?php }
                    }
                    ?>
           <?php
          if($this->sma->actionPermissions('kitchens') ){
          ?>
        <!--   <li class="dropdown">
                        <a class="btn borange pos-tip" id="close_register" title="" data-placement="bottom" data-html="true" data-backdrop="static"  href="<?= admin_url('pos/close_register'); ?>" data-toggle="modal" data-target="#myModal" data-original-title="<span>Close Register</span>" tabindex="-1">
                            <i class="fa fa-times-circle"></i>
                        </a>
                    </li> -->

         
          <?php
          }
          ?>
					<button onClick="openFullScreen()" class="btn btn-sm btn-danger btn_fullscreen hide">
                		<i class="fa fa-arrows-alt fa-2x" aria-hidden="true"></i>           
                	</button>
                    <li class="dropdown pull-right notification"><a href="javascript:void(0)"><i class="fa fa-bell-o" aria-hidden="true"><span id="notification_area" class="badge">0</span></i>
                    <input type="hidden" name="notification_key" id="notification_key">
                    </a>
                        <ul class="dropdown-menu content_notification" id="content_notification">
							<div class="notify_titile">
                           		<h4><?=lang('notification')?></h4>
							</div>
                           <div class="list_notification">
                            
                           </div> 
                        </ul>
                    </li>	
                    
                  
                    
                    <li class="dropdown lanfuage_str">
                    	<a class="btn tip" title="<?= lang('language') ?>" data-placement="bottom" data-toggle="dropdown"
                           href="javascript:void(0)">
                            <img src="<?= base_url('assets/images/' . $Settings->user_language . '.png'); ?>" alt="">
                        </a>
                        <ul class="dropdown-menu pull-right">
                            <?php $scanned_lang_dir = array_map(function ($path) {
                                return basename($path);
                            }, glob(APPPATH . 'language/*', GLOB_ONLYDIR));
                            foreach ($scanned_lang_dir as $entry) { 
								if($entry == 'english' || $entry == 'khmer'){
							?>
                                <li>
                                    <a href="<?= admin_url('welcome/language/' . $entry); ?>">
                                        <img src="<?= base_url('assets/images/'.$entry.'.png'); ?>" class="language-img">
                                        &nbsp;&nbsp;<?= ucwords($entry); ?>
                                    </a>
                                </li>
                            <?php } } ?>
                          
                           
                        </ul>
                    </li>
                    
                    
                </ul>
            </div>
            
            <div class="col-lg-1 col-md-1 col-sm-1 col-xs-12 log_out">
               <ul>
               	<li> <a class="req_status pos_head_req btn btn-default" href="<?= admin_url('pos/order_biller/?type=1'); ?>" data-notify-type="notice"><?=lang('request_bil')?></a><input type="hidden" name="rep_count" id="rep_count" value=""><span class="req_sound">&nbsp;</span></li>
               	<li><a href="<?php echo site_url('frontend/logout'); ?>" onClick=" localStorage.clear();"><button type="button" class="btn btn-default" title="Log Out"><i class="fa fa-sign-out" aria-hidden="true"></i> <?=lang('logout')?></button></a></li>
               </ul>
            </div>
        </div>
    </div>
 </div>
<script>
       var siteurl = "<?=site_url()?>";
</script>
<?php if(!$isTransactiondateSet) : ?>
<script type="text/javascript" src="<?=$assets?>pos/js/nightaudit_date.js"></script>
<?php endif; ?>
