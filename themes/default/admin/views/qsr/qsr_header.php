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
                <a href="<?php echo site_url().'admin/qsr'; ?>"><img src="<?=base_url()?>assets/uploads/logos/<?=$this->Settings->logo3?>" alt="<?=$this->Settings->site_name?>" class="sram_table_logo" /></a>
           <?php   } ?>


                <!-- <a href="<?php echo site_url().'admin/qsr'; ?>"><img src="<?=$assets?>images/front_logo.png" alt="Pos_logo" title="Pos-logo"></a> -->
            </div>
            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 custom_details">
                <ul>
                  
					<button onClick="openFullScreen()" class="btn btn-sm btn-danger btn_fullscreen hide">
                		<i class="fa fa-arrows-alt fa-2x" aria-hidden="true"></i>           
                	</button>
                  <li></li>
                  <li></li>
                  <li></li>
                  <li></li>
                   <?php if($this->sma->actionPermissions('reprint') ){?>
                  <li><a href="<?= admin_url('qsr/reprinter'); ?>"><img src="<?=$assets?>images/loyalty.png"> <br><?=lang('reprint')?></a></li>
                  <?php } ?>

                  <?php if($this->sma->actionPermissions('report_view') ){?>
                      <li class="dropdown"><a data-toggle="dropdown"  href="javascript:void(0);"><img src="<?=$assets?>images/dine.png"> <br><?=lang('reports')?> <img src="<?=$assets?>images/angle_down.png" class="down_img"></a>
                      <ul class="dropdown-menu">
                        <?php
                        if($this->sma->actionPermissions('today_item_report') ){
                        ?>
                          <li><a href="<?= admin_url('qsr/reports/?type=1'); ?>"><?=lang('today_itemsale_report')?></a></li>
                        <?php
                        }
                        ?>
                        <?php
                        if($this->sma->actionPermissions('daywise_report') ){
                        ?>
                         <li><a href="<?= admin_url('qsr/reports/?type=2'); ?>"><?=lang('daywise_report')?></a></li>
                        <?php
                        }
                        ?>
                        <?php
                        if($this->sma->actionPermissions('cashierwise_report') ){
                        ?>
                          <li><a href="<?= admin_url('qsr/reports/?type=3'); ?>"><?=lang('cashierwise')?></a></li>
                        <?php
                        }
                        ?>
                           <li><a href="<?= admin_url('qsr/reports/?type=4'); ?>"><?=lang('POS Settlement')?></a></li>
                      </ul>
                    </li>
                  <?php }?>
                  <!-- <li><a href="<?= admin_url('qsr/reports/?type=4'); ?>"><img src="<?=$assets?>images/dine.png"> <br><?=lang('POS Settlement')?></a></li> -->
                    <?php 
                         $url = $this->uri->segment(3);                        
                       if($url == null && $this->sma->actionPermissions('hold_sales') ){ ?>
                         <li><a id="opened_bills"   href="<?= admin_url('qsr/opened_bills'); ?>" data-toggle="ajax" >
                            <img src="<?=$assets?>images/dine.png"> <br><?=lang('hold_sales')?></a>
                             </li>
                    <?php }
                     ?>

                        <?php 
                         $url = $this->uri->segment(3);                        
                       if($this->sma->actionPermissions('cancel_sales') || $this->sma->actionPermissions('resettle_sales') ){ ?>
              		      <li class="dropdown"><a data-toggle="dropdown"  href="javascript:void(0);"><img src="<?=$assets?>images/dine.png"> <br><?=lang('more')?> <img src="<?=$assets?>images/angle_down.png" class="down_img"></a>
                        <ul class="dropdown-menu">
                          <?php if($this->sma->actionPermissions('cancel_sales') ){ ?>
                          <li style="border-right: none"><a  href="<?= admin_url('qsr/cancel_bill'); ?>" ><?=lang('cancel_sales')?></a>
                          </li>
                          <?php } ?>
                           <?php if($this->sma->actionPermissions('resettle_sales') ){ ?>
                          <li style="border-right: none"><a  href="<?= admin_url('qsr/resettle_bill?date='.date('Y-m-d')); ?>" ><?=lang('resettle_sales')?></a>
                          </li>
                        <?php } ?>
                        </ul>
              		      </li>
                    <?php }
                     ?>


                    <li class="dropdown pull-right notification"><a href="javascript:void(0)"><i class="fa fa-bell-o" aria-hidden="true"><span id="notification_area" class="badge">0</span></i>
                    <input type="hidden" name="notification_key" id="notification_key">
                    </a>
                        <ul class="dropdown-menu content_notification" id="content_notification">
							<div class="notify_titile">
                           		<h4>Notification</h4>
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
                   <!--  <li class="dropdown pull-right">
                   <a class="btn blightOrange pos-tip" id="opened_bills" title="<span><?=lang('suspended_sales')?></span>" data-placement="bottom" data-html="true" href="<?=admin_url('qsr/opened_bills')?>" data-toggle="ajax">
                            <i class="fa fa-th"></i>
                        </a>
                        
                       </li> -->
                        <div class="welcome_user" style="position: absolute;;right: 0px;top:3px;padding: 0px 0px;">
                        <span class="user btn-warning" style="padding: 0px 3px;"> Welcome :<?php                        
                          echo $this->session->userdata('first_name');?></span>
                        </div>

                </ul>
            </div>
            
            <div class="col-lg-1 col-md-2 col-sm-2 col-xs-12 log_out">
                 <a href="<?php echo site_url('frontend/logout'); ?>" onClick=" localStorage.clear(); "><button type="button" class="btn btn-default pull-left" title="Log Out"><i class="fa fa-sign-out" aria-hidden="true"></i> Logout</button></a>
            </div>
        </div>
    </div>
 </div>
 
