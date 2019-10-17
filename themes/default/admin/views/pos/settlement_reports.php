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
    <link rel="stylesheet" href="<?= $assets ?>styles/jquery-ui.css">
    <script src="<?= $assets ?>js/jquery-ui.js"></script>
    <?php if ($Settings->user_rtl) {?>
        <link href="<?=$assets?>styles/helpers/bootstrap-rtl.min.css" rel="stylesheet"/>
        <link href="<?=$assets?>styles/style-rtl.css" rel="stylesheet"/>
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

     <?php
    if($this->Settings->user_language == 'english' ) { 
         $this->load->view($this->theme . 'pos/pos_header');   
         }else{// for kimmo 
            $this->load->view($this->theme . 'pos/pos_header_kimmo'); 
         }
    ?>
     
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
              <strong style="text-align: center;"><h2><?=lang('pos_settle_report')?></h2></strong>  
        <div id="receiptData">

            <div class="no-print">
             <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'id' => 'pos-payment-form');
             echo admin_form_open("pos/paymant", $attrib);?>

             <div class="form-group  col-lg-6 date_div">
                <label for="method"><?php echo $this->lang->line("from_date"); ?></label>
                  <div class="controls ">
                    <input type="text" name="from_date" class="form-control datetime" placeholder="From Date " id="from_date" required="required">
                  </div>
                </div>
                <div class="form-group  col-lg-6 date_div">
                <label for="method"><?php echo $this->lang->line("to_date"); ?></label>
                  <div class="controls">
                    <input type="text" name="to_date" class="form-control datetime" placeholder="To Date " id="to_date" required="required">
                  </div>
                </div>
             <?php                             
             echo form_hidden('action', 'PAYMENT-SUBMIT');
             echo form_close();
             ?>

             <div class="modal-footer">
                <button class="btn btn-block btn-lg btn-primary" id="submit-sale"><?=lang('submit');?></button>
            </div>

            </div>
            <div id="receipt-datareceipt-data" >
                <h3 id="hide"><b><?= lang("pos_settlement_report"); ?></b></h3>
            <div id="printdata">              
          
          <table class="table table-striped table-condensed" >
            <tbody>
                <colgroup>
                <col width="50%">
                 <col width="50%">
            </colgroup>
             <?php echo "<strong><p>" .lang("from") . "&nbsp;&nbsp;: " . $from . "&nbsp;&nbsp;&nbsp;&nbsp; " .lang("to") . "&nbsp;&nbsp;: " . $to . "</strong></p></br>";?>
            <h3 ><b><?= lang("order_wise"); ?></b></h3>
        <?php
         $buffet_total = 0;
         $buffet_transaction = 0;
         $net_total = 0;
         $gross_total = 0;
         $total_transaction = 0;
         if(isset($settlement['sale_type'])) : ?>
        <?php foreach($settlement['sale_type'] as $k => $row) : ?>
        <tr>
            <td><?=$row->sale_type?></td>
            <td><?=$this->sma->formatMoney($row->sale_type_total); ?></td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>

        <?php if(isset($settlement['payments'])) : ?>
            <?php foreach($settlement['payments'] as $k => $row) : ?>
            <?php if(isset($row->total_transaction)) :
                $total_transaction =$row->total_transaction;
             ?>
            <!-- <tr style="font-weight:bold">
                <td class="no-border"><?=lang('total_transaction')?></td>
                <td><?=$row->total_transaction?></td>
            </tr> -->
            <?php endif; ?>
            <?php if(isset($row->gross_total)) : 
                $gross_total = $row->gross_total;
                $net_total = $row->net_total;
            ?>
            <!-- <tr style="font-weight:bold">
                <td class="no-border"><?=lang('gross_total')?></td>
                <td><?=$this->sma->formatMoney($row->gross_total)?></td>
            </tr> -->
            <?php endif; ?>
            <?php if(isset($row->net_total)) : ?>
            <!-- <tr style="font-weight:bold">
                <td class="no-border"><?=lang('net_total')?></td>
                <td><?=$this->sma->formatMoney($row->net_total)?></td>
            </tr> -->
            <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>

         <?php if(isset($settlement['buffet_sale'])) : ?>
        <?php foreach($settlement['buffet_sale'] as $k => $row) :
            $buffet_total =$row->sale_type_total;
            $buffet_transaction =$row->total_transaction; ?>
        <tr>
            <td><?=$row->sale_type?></td>
            <td><?=$this->sma->formatMoney($row->sale_type_total)?></td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>

         <tr style="font-weight:bold">
                <td class="no-border"><?=lang('total_transaction')?></td>
                <td><?=$total_transaction+$buffet_transaction?></td>
         </tr>
       <!--   <tr style="font-weight:bold">
                <td class="no-border"><?=lang('gross_total')?></td>
                <td><?=$this->sma->formatMoney($gross_total+$buffet_total)?></td>
         </tr>  -->

         <tr style="font-weight:bold">
                <td class="no-border"><?=lang('net_total')?></td>
                <td><?=$this->sma->formatMoney($net_total+$buffet_total)?></td>
         </tr> 

        </tbody>
        </table>    

            <h3 ><b><?= lang("payment_mode_reports"); ?></b></h3>
    <table class="table table-striped table-condensed" style="position: relative;width: 100%;float: left;">   
         <tbody>
            <colgroup>
                <col width="50%">
                 <col width="50%">
            </colgroup>
        <?php 

        /*echo "string";
        print_r($settlement['tender_type']);die;*/

        if(isset($settlement['tender_type'])) : ?>
        <?php foreach($settlement['tender_type'] as $row) : ?>
        <tr style="font-weight:bold">
            <td><?=$row['tender_type']?></td>
            <td><?=$this->sma->formatMoney($row['tender_type_total'])?></td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>

        <?php
            $usd = 0;
            $exchange = 0;

            $usd1 = 0;
            $exchange1 = 0;
            $default_currency = $this->Settings->default_currency;
            $exchane_currency  =  $this->site->getExchangeCurrency($default_currency);
        ?>

        <?php if(isset($settlement['exchange_amt'])) : ?>
        <?php foreach($settlement['exchange_amt'] as $k => $row) : 
              $usd =$row->usd;
              $exchange =$row->For_Ex; ?>

            <?php endforeach; ?>
        <?php endif; ?>

         <?php if(isset($settlement['buffet_exchange'])) : ?>
        <?php foreach($settlement['buffet_exchange'] as $k => $row) : 
              $usd1 =$row->usd;
              $exchange1 =$row->For_Ex; ?>

            <?php endforeach; ?>
        <?php endif; ?>

        <tr style="font-weight:bold">
            <td class="no-border"><?=lang('exchange')?></td>
            <td><?php echo $this->sma->formatMoney($usd+$usd1).'('.$exchane_currency.($exchange+$exchange1).')';?></td>
        </tr>
        

        <?php if(isset($settlement['open_sale'])) : ?>
        <?php foreach($settlement['open_sale'] as $k => $row) :
        if($row->opensale != 0):
            ?>
        <tr style="font-weight:bold">
            <td class="no-border"><?=lang('open_register')?></td>
            <td><?php echo $this->sma->formatMoney($row->opensale);?></td>
        </tr>
        <?php endif; ?>
        <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>          
            <div style="clear:both;"></div>
        </div>
        </div>

        <div id="buttons" style="padding-top:10px; text-transform:uppercase;" class="no-print">
          <hr>
               <span class="pull-right col-xs-12">
                    <?php
                     echo '<button id="print_days" class="btn btn-block btn-primary">'.lang("print").'</button>';
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
            var url = '<?php echo  admin_url('pos/reports') ?>';
            window.location.href= url +'/?type=4'+'&fromdate='+from_date+'&todate='+to_date;  
    });

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




</body>
</html>
