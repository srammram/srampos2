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
<!--
    <link rel="stylesheet" href="<?=$assets?>styles/style.css" type="text/css"/>
    <link rel="stylesheet" href="<?=$assets?>pos/css/posajax.css" type="text/css"/>
-->
    <link rel="stylesheet" href="<?=$assets?>pos/css/print.css" type="text/css" media="print"/>
    <link rel="stylesheet" href="<?=$assets?>styles/helpers/bootstrap.min.css" type="text/css">
   	<link rel="stylesheet" href="<?=$assets?>styles/helpers/font-awesome.min.css" type="text/css">
    <link rel="stylesheet" href="<?=$assets?>styles/home_frontend.css" type="text/css">
    <script type="text/javascript" src="<?=$assets?>js/jquery-2.0.3.min.js"></script>
    <script type="text/javascript" src="<?=$assets?>js/jquery-migrate-1.2.1.min.js"></script>
    <link rel="stylesheet" href="<?= $assets ?>styles/jquery-ui.css">
    <script src="<?= $assets ?>js/jquery-ui.js"></script>
    <?php if ($Settings->user_rtl) {?>
<!--        <link href="<?=$assets?>styles/helpers/bootstrap-rtl.min.css" rel="stylesheet"/>-->
<!--        <link href="<?=$assets?>styles/style-rtl.css" rel="stylesheet"/>-->
        <script type="text/javascript">
            $(document).ready(function () {
                $('.pull-right, .pull-left').addClass('flip');
            });
        </script>
    <?php }
    ?>

    <style type="text/css" media="all">
        
            #printdiv { max-width: 480px; margin: 0 auto; padding-top: 20px; }
            .btn { border-radius: 0; margin-bottom: 5px; }
            .bootbox .modal-footer { border-top: 0; text-align: center; }
            h3 { margin: 5px 0; }
            .order_barcodes img { float: none !important; margin-top: 5px; }
            @media print {
                .no-print { display: none; }
                #printdiv { max-width: 480px; width: 100%; min-width: 250px; margin: 0 auto; }
                .no-border { border: none !important; }
                .border-bottom { border-bottom: 1px solid #ddd !important; }
                table tfoot { display: table-row-group; }
            }
            #hide{
                display: none;
            }
        </style>

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
						<a href="<?php echo base_url('/pos/pos/split_list')  ?>">
							<button>
								<img src="<?=$assets?>images/sprite/order.png">
								<figcaption>Order</figcaption>
							</button>
							</a>
						</li>
						<li>
							<a href="<?php echo base_url('/pos/pos/invoice_list')  ?>">
							<button>
								<img src="<?=$assets?>images/sprite/payment_y.png">
								<figcaption>Payment</figcaption>
							
							</button>
								</a>
						</li>
						<li>
							<a href="<?php echo base_url('/pos/pos/reprint')  ?>">
							<button>
								<img src="<?=$assets?>images/sprite/print.png">
								<figcaption>Re Print</figcaption>
								
							</button>
							</a>
						</li>
						<li>
							<a href="<?php echo base_url('/pos/pos/report')  ?>">
								<figure class="text-center">
									<div class="img_block">
										<img src="<?=$assets?>images/sprite/report_icon.png">
										<figcaption>Reports</figcaption>
									</div>
								</figure>
							</a>
						</li>
						<li>
						<a href="<?php echo base_url('/pos/pos/')  ?>">
							<button class="pull-right">
								<img src="<?=$assets?>images/sprite/back.png">
								<figcaption>Back</figcaption>
							</button>
							</a>
						</li>
					</ul>
				</div>
			</div>
			
		</div>
	</section>
    <div id="content">
          <div id="printdiv">
            <?php    
            

              $fromdate = $this->input->get('fromdate');
              $todate = $this->input->get('todate');
              
                if($fromdate)
                {
                  $from = strtotime($fromdate);
                  $from = date("d-m-Y", $from);
                }
                else{
                  $from = date('Y-m-d'); 
                }
                
                if($todate)
                {
                  $to = strtotime($todate);
                  $to = date("d-m-Y", $to);
                }
                else{
                    $to = date('Y-m-d');
                }

                // echo "<strong><p>Item Wise Sales</p></strong>";
                // echo "<strong><p>" .lang("from") . "&nbsp;&nbsp;: " . $from . "&nbsp;&nbsp;&nbsp;&nbsp; " .lang("to") . "&nbsp;&nbsp;: " . $to . "</strong></p></br>";
                ?>
            <!-- <h2><?= lang("pos_settlement_report"); ?></h2> -->
             <strong style="text-align: center;"><h2><?=lang('shifttime_report')?></h2></strong>
        <div id="receiptData">

            <div class="no-print">
             <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'id' => 'pos-payment-form');
             echo form_open("pos/pos/paymant", $attrib);?>

             <div class="form-group  col-lg-6 date_div">
                <label for="method"><?php echo $this->lang->line("from_date"); ?></label>
                  <div class="controls ">
                    <input type="text" name="from_date" class="form-control datetime" placeholder="From Date " id="from_date" required="required" autocomplete="off">
                  </div>
                </div>
                <div class="form-group  col-lg-6 date_div">
                <label for="method"><?php echo $this->lang->line("to_date"); ?></label>
                  <div class="controls">
                    <input type="text" name="to_date" class="form-control datetime" placeholder="To Date " id="to_date" required="required" autocomplete="off">
                  </div>
                </div>
             <?php           

              $shift_id =  $_GET['shift_id']?$_GET['shift_id']:0;    
              $shift_id =  $_GET['shift_id'];

             echo form_hidden('action', 'PAYMENT-SUBMIT');
             echo form_close();
             ?>

               <div class="">   
                 <label for="method" style="margin-left: 15px;color: #fff;font-weight: normal;"><?php echo $this->lang->line("Shift_Time"); ?></label>
                  <select name="shift_id" style="width: 450px;margin-left: 14px;" class="form-control pos-input-tip shift_id select2" id="shift_id" >
                     <option value="0">ALL</option>
                      <?php

                       if(!empty($shifttime)){

                          foreach ($shifttime as $shift) {                              
                          ?>
                          <option value="<?php echo $shift->id; ?>" <?php if( $shift_id ==  $shift->id ) echo 'selected ="selected"';?>  data-id="<?php echo $shift->id; ?>"><?php echo $shift->name; ?></option>
                          <?php
                          }
                      }
                      ?>
                  </select><br>
                  </div>

             <div class="modal-footer">
                <button class="btn btn-block btn-primary brn_reports_s" id="submit-sale"><?=lang('submit');?></button>
            </div>

            </div>
            <div id="receipt-datareceipt-data" >
                <!-- <h3 id="hide"><b><?= lang("pos_settlement_report"); ?></b></h3> -->
            <div id="printdata">              
          
          <table class="table table-striped table-condensed" >
            <tbody>
                <colgroup>
                    <col width="50%">
                    <col width="50%">
                </colgroup>
          <!--   <h3 ><b><?= lang("shift_reports"); ?></b></h3> -->
            <?php 
                   if($from != $to){
                        echo "<strong><p>" .lang("date") . "&nbsp;&nbsp;: " . $from . "&nbsp;&nbsp;&nbsp;&nbsp; - &nbsp;&nbsp; " . $to . "</strong></p></br>";
                    } 
                    else{
                     echo "<strong><p>" .lang("date") . "&nbsp;&nbsp;: " . $from."</strong></p></br>";   
                    }
                    if(@$_GET['fromdate']!='' && @$_GET['todate']!='' ){
                        $daterange = date('d/m/y',strtotime($_GET['fromdate'])).' - '.date('d/m/y',strtotime($_GET['todate']));
                    }else if(@$_GET['fromdate']!=''){
                        $daterange = date('d/m/y',strtotime($_GET['fromdate']));
                    }else if(@$_GET['todate']!=''){
                        $daterange = date('d/m/y',strtotime($_GET['todate']));
                    }else{
                        $daterange = date('d/m/Y');
                    } 
              ?>

        <?php 
        if(!empty($shiftreport)){   
             
                    ?>
                    <table class="table table-striped table-condensed">                    
                     <?php            
                     $grand_total =0;                     
                      echo '<tr ><th ></th><th ></th><th></th><th>FROM</th><th>TO</th></tr>';
                     
                     foreach($shiftreport as $table){
                      // echo "<pre>";
                     // print_r($table->name);die;
                        echo '<tr><td>Shift No:</td><td></td><td>'.$table->name.'</td><td>'.$table->start_time.'</td><td>'.$table->end_time.'</td></tr>';
                        ?> <tr>                       
								<th><?=lang("user");?></th>
								<th><?=lang("cash");?></th>
								<th><?=lang("CC");?></th>
								<th><?=lang("Credit");?></th>
								<th class="text-right"><?=lang("Total");?></th>
							</tr>
                    </thead>
                    <?php
                         $shift_total = 0;
                         echo '<tbody>';
                         if(!empty($table->user)){

                     foreach($table->user as $users){ 
                         $shift_total += $users->Bill_amt;
                         $grand_total += $users->Bill_amt;                        
                        echo '<tr><td>'.$users->username.'</td>';
                        echo '<td>'.$this->sma->formatMoney($users->Cash+$users->For_Exto_usd).'</td>';
                        echo '<td>'.$this->sma->formatMoney($users->Credit_Card).'</td>';
                        echo '<td>'.$this->sma->formatMoney($users->credit).'</td>';
                        echo '<td class="text-right">'.$this->sma->formatMoney($users->Bill_amt).'</td>';                        
                        echo '</tr></tbody>';
                        ?>
                           <?php 
                        } ?>
                        <tr>
                           <td></td><td></td>
                            <td colspan="2"><strong><?= lang("shift total:"); ?></strong></td>                            
                            <td class="text-right"><?php echo $this->sma->formatMoney($shift_total); ?></td>
                        </tr>

                    <?php }
                    ?><?php  if($grand_total != 0){ ?>
                       <tr>
                           <td></td><td></td>
                            <td colspan="2"><strong><?= lang("total_sales:"); ?></strong></td>                            
                            <td class="text-right"><?php echo $this->sma->formatMoney($grand_total); ?></td>
                        </tr>  
                      <?php } ?>
                     <?php } 
                     ?>
                
           </tbody>
                     <?php 
                      }                                                               

                
                ?> 

             </table>

            <div style="clear:both;"></div>
        </div>
        </div>

        <div id="buttons" style="padding-top:10px; text-transform:uppercase;" class="no-print">
          <hr>
               <span class="pull-right col-xs-12">
                    <?php
                     echo '<button id="print_days" class="btn btn-block btn-primary brn_reports_s">'.lang("print").'</button>';
                    ?>
                </span>
            <div style="clear:both;"></div>
        </div>
    </div>

</div>

    </div>
  </div>
  

<?php
$this->load->view($this->theme . 'pos/pos_footer');
?>

<div class="modal fade in" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2"
     aria-hidden="true">
</div>

<div id="modal-loading" style="display: none;">
    <div class="blackbg"></div>
    <div class="loader"></div>
</div>


<?php unset($Settings->setting_id, $Settings->smtp_user, $Settings->smtp_pass, $Settings->smtp_port, $Settings->update, $Settings->reg_ver, $Settings->allow_reg, $Settings->default_email, $Settings->mmode, $Settings->timezone, $Settings->restrict_calendar, $Settings->restrict_user, $Settings->auto_reg, $Settings->reg_notification, $Settings->protocol, $Settings->mailpath, $Settings->smtp_crypto, $Settings->corn, $Settings->customer_group, $Settings->srampos_username, $Settings->purchase_code);?>
<script type="text/javascript">


var site = <?=json_encode(array('url' => base_url(), 'base_url' => admin_url('/'), 'assets' => $assets, 'settings' => $Settings, 'dateFormats' => $dateFormats))?>, pos_settings = <?=json_encode($pos_settings);?>;
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


    $(document).ready(function () {
        $(document).on('click', '#print_days', function () {
            Popup($('#receipt-datareceipt-data').html());    
        });
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
});
</script>


</script>
<?php
    $s2_lang_file = read_file('./assets/config_dumps/s2_lang.js');
    foreach (lang('select2_lang') as $s2_key => $s2_line) {
        $s2_data[$s2_key] = str_replace(array('{', '}'), array('"+', '+"'), $s2_line);
    }
    $s2_file_date = $this->parser->parse_string($s2_lang_file, $s2_data, true);
?>
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
<script type="text/javascript" src="<?=$assets?>pos/js/pos.bills.js"></script>
<script type="text/javascript" src="<?=$assets?>js/dragscrollable.js"></script>
<script type="text/javascript" src="<?=$assets?>js/dragscroll.js"></script>
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
<script type="text/javascript">

   $('#submit-sale').click(function () {
            var from_date = $('#from_date').val(); 
            var to_date = $('#to_date').val(); 
            var shift_id = $('#shift_id').val(); 
            var url = '<?php echo  base_url('pos/pos/reports') ?>';
            window.location.href= url +'/?type=5'+'&fromdate='+from_date+'&todate='+to_date+'&shift_id='+shift_id; 
    });

    /*$('#submit-sale').click(function () {
            var from_date = $('#from_date').val(); 
            var to_date = $('#to_date').val(); 
            var url = '<?php echo  admin_url('pos/reports') ?>';
            window.location.href= url +'/?type=5'+'&fromdate='+from_date+'&todate='+to_date;  
    });*/

$('.sortable_table tbody').sortable({
    containerSelector: 'tr'
});
        
$('#to_date').datepicker({
        dateFormat: "yy-mm-dd" ,
        maxDate:  0,      
    });
    $("#from_date").datepicker({
        dateFormat: "yy-mm-dd" ,  
        maxDate:  0,      
        onSelect: function(date){            
            var date1 = $('#from_date').datepicker('getDate');           
            var date = new Date( Date.parse( date1 ) );
            date.setDate( date.getDate());        
            var newDate = date.toDateString(); 
            newDate = new Date( Date.parse( newDate ) );                      
            $('#to_date').datepicker("option","minDate",newDate);            
        }
    });

</script>

<script type="text/javascript" charset="UTF-8"><?=$s2_file_date?></script>
<?php
if (isset($print) && !empty($print)) {
   
    include 'remote_printing.php';
}
?>

<?php 
echo $this->load->view($this->theme.'pos_v2/shift/shift_popup'); 
?>


</body>
</html>
