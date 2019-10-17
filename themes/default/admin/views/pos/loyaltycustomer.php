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
    <script type="text/javascript" src="<?=$assets?>js/select2.min.js"></script>
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
    ?>
<style type="text/css" media="all">
            body { color: #000; }
            #wrapper1 { max-width: 480px; margin: 0 auto; padding-top: 20px; }
            .btn { border-radius: 0; margin-bottom: 5px; }
            .bootbox .modal-footer { border-top: 0; text-align: center; }
            h3 { margin: 5px 0; }
            .order_barcodes img { float: none !important; margin-top: 5px; }
            @media print {
                .no-print { display: none; }
                #wrapper1 { max-width: 480px; width: 100%; min-width: 250px; margin: 0 auto; }
                .no-border { border: none !important; }
                .border-bottom { border-bottom: 1px solid #ddd !important; }
                table tfoot { display: table-row-group; }
            }
        .bootbox.modal{
        background: none !important;
        }
        .available-c-limit{
        color:#f00;
        }
        #payment-customer-name{
        float: right;
        }
        </style>
    <script>var curr_page="order_biller";</script>
    <?php if(@$_GET['tid'] && isset($_SERVER['HTTP_REFERER'])): ?>
        <script>var curr_func="update_tables";var tableid = '<?=$_GET['tid']?>';</script>   
    <?php endif; ?>
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
              <!--   <?php
                    if ($error) {
                        echo "<div class=\"alert alert-danger\"><button type=\"button\" class=\"close fa-2x\" data-dismiss=\"alert\">&times;</button>" . $error . "</div>";
                    }
                ?>
                <?php
                    if (!empty($message)) {
                        echo "<div class=\"alert alert-success\"><button type=\"button\" class=\"close fa-2x\" data-dismiss=\"alert\">&times;</button>" . $message . "</div>";
                    }
                ?> -->
                
                <div id="pos">
                
                    <div class="current_table_order">
                                <div class="container custom_container">
                                    <div class="row">
                            
                                    <div id="loyaltycustomer">
                                        <h2>Loyalty Customer</h2>
                                        <table id="example" class="table table-striped table-bordered" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Phone</th>
                                                    <th>Loyalt card</th>
                                                    <th>Expiry Date</th>
                                                    <th>Total Points</th>
                                                    <th>status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                if(!empty($loyaltycustomer)){
                                                    foreach($loyaltycustomer as $loyalty){
                                                        if(!empty($loyalty->loyalty_card_no)){
                                                            $disabled = 'disabled';
                                                            $txt = 'Card Issued';
                                                            $class = 'btn btn-success';
                                                        }else{
                                                            if($loyalty->total_points >= $loyalty->eligibity_point){
                                                                $disabled = '';
                                                                $txt = 'Issue Loyalty Card';
                                                                $class = 'btn-primary';
                                                            }else{
                                                                $disabled = 'disabled';
                                                                $txt = 'User Not Eligible';
                                                                $class = 'btn-warning';
                                                            }
                                                        }
                                                ?>
                                                <tr>
                                                    <td><?=$loyalty->name?></td>
                                                    <td><?=$loyalty->phone?></td>
                                                    <td><?=$loyalty->loyalty_card_no?></td>
                                                    <td><?=$loyalty->expiry_date?></td>
                                                    <td><?=$loyalty->total_points?></td>
                                                    <td>
                                                        <button data-customer="<?=$loyalty->customer_id?>" data-expiry="<?=$loyalty->expiry_date?>" class="btn btn-block <?=$class?> voucher" <?=$disabled?>><?=$txt?> </button>
                                                    </td>
                                                </tr>
                                                <?php
                                                    }
                                                }
                                                ?>
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
</div>

<?php
$this->load->view($this->theme . 'pos/pos_footer');
?>

<div class="modal fade in" id="voucherModal" tabindex="-1" role="dialog" aria-labelledby="voucherModalLabel"
     aria-hidden="true" style="z-index:9999" data-backdrop="static" data-keyboard="false" >
    <div class="modal-dialog modal-md">
       <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="voucherModalLabel"><?php echo lang('loyalty_card_issue'); ?></h4>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'id' => 'add-customer-form');
        echo admin_form_open_multipart("pos/addcustomervoucher", $attrib); ?>
        <div class="modal-body">
            <div class="row">
                <div class="col-sm-6">                
                    <?=lang("customer_name", "customer_name");?>
                    <input type="text" readonly="" name="customername"  id="customername" ></span>                
                </div>
            
            <div class="col-sm-6">
                <?=lang("customer_points", "customer_points");?>
                    <input type="text" readonly="" name="customerpoints" id="customerpoints"></span>
            </div>

            <div class="col-sm-6">
                <div id="voucherdetails">
                    <?=lang("loyalty_card_list", "loyalty_card_list");?>
                    <input type="hidden" readonly="" name="loyalty_card_name" id="loyalty_card_name"></span>
                    <input type="hidden" readonly="" name="customer_id" id="customer_id"></span>
                    <select id="loyalty_card_list" name="loyalty_card" class="form-control loyalty_card_list" required="">
                        <option></option>
                    </select>
                
                </div>
            </div>
            </div>


        </div>
        <div class="modal-footer">
            <input type="submit" name="create_voucher" value="<?=lang('submit');?>" class="btn btn-primary" autocomplete="off">
            <!-- <?php echo form_submit('create_voucher', lang('submit'), 'class="btn btn-primary"'); ?> -->
        </div>
        <?php echo form_close(); ?>
    </div>
    </div>
</div>



<script>



$(document).on('click', '.voucher', function(){
    var customer_name = $(this).closest('tr').find('td:eq(0)').text();
    var total_points = $(this).closest('tr').find('td:eq(4)').text();
    var customer_id = $(this).attr('data-customer');      
    $("#customername").val(customer_name);
    $("#customerpoints").val(total_points);    
    $("#customer_id").val(customer_id);         
    $.ajax({
        type: "get",
        url: "<?=admin_url('pos/getLoyaltyCardNo');?>",
        data: {customer_id: customer_id},
        dataType: "json",
        success: function (data) {            
            // $('#voucherModal').show();
            $html='';
            $.each(data.cus_dis, function( index, value ){                            
                $html +='<option value="'+value.id+'">'+value.card_no+'</option>';
            });
            $("#loyalty_card_list").append($html);
            $('#voucherModal').appendTo("body").modal('show', {backdrop: 'static', keyboard: false});                 
        }    
    });
});

$(document).on('change', '.loyalty_card_list', function(){
        $('#loyalty_card_name').val($('#loyalty_card_list option:selected').text());
});

</script>


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
</script>

<script type="text/javascript">
    var username = '<?=$this->session->userdata('username');?>', order_data = '', bill_data = '';

    
        $(document).on('click', '.closemodal', function () {
            $('#remarks').val('');
            $('#sale_id').val('');
            $('#CancelorderModal').hide(); 
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
<script>
$('.kb-pad').keyboard({
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
        customLayout: {
            'default': [
            '1 2 3 4',
            '5 6 7 8  ',
            ' 9 0 . {b}',

            ' {accept} {cancel}'
            ]
        }
    });
</script>
<script>
$('.kb-text').keyboard({
        autoAccept: true,
        alwaysOpen: false,
        openOn: 'focus',
        usePreview: false,
        layout: 'custom',
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
</script>


 
<script type="text/javascript">
$('.sortable_table tbody').sortable({
    containerSelector: 'tr'
});

function formatMoney(x, symbol) {
    if(!symbol) { symbol = ""; }
    if(site.settings.sac == 1) {
        return (site.settings.display_symbol == 1 ? site.settings.symbol : '') +
            ''+formatSA(parseFloat(x).toFixed(site.settings.decimals)) +
            (site.settings.display_symbol == 2 ? site.settings.symbol : '');
    }
    var fmoney = accounting.formatMoney(x, symbol, site.settings.decimals, site.settings.thousands_sep == 0 ? ' ' : site.settings.thousands_sep, site.settings.decimals_sep, "%s%v");
    if(symbol){
       return fmoney; 
    }
    return (site.settings.display_symbol == 1 ? site.settings.symbol : '') +
        fmoney +
        (site.settings.display_symbol == 2 ? site.settings.symbol : '');
}
</script>

<script type="text/javascript" charset="UTF-8"><?=$s2_file_date?></script>
<?php
if (isset($print) && !empty($print)) {
    /* include FCPATH.'themes'.DIRECTORY_SEPARATOR.$Settings->theme.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'pos'.DIRECTORY_SEPARATOR.'remote_printing.php'; */
    include 'remote_printing.php';
}
?>




</body>
</html>
