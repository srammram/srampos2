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
    <link rel="stylesheet" href="<?= $assets ?>styles/helpers/jquery-ui.css">
    <script src="<?= $assets ?>js/jquery-ui.js"></script>

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
<script type="text/javascript" src="<?=$assets?>pos/js/pos.ajax.js?v=2"></script>
<script type="text/javascript" src="<?=$assets?>js/dragscrollable.js"></script>
<script type="text/javascript" src="<?=$assets?>js/dragscroll.js"></script>

<script type="text/javascript">
    
var site = <?=json_encode(array('url' => base_url(), 'base_url' => admin_url('/'), 'assets' => $assets, 'settings' => $Settings, 'dateFormats' => $dateFormats))?>, pos_settings = <?=json_encode($pos_settings);?>;

</script>
    
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
            #pass_code{
                    border: 1px solid #ccc !important;
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
    $this->load->view($this->theme . 'pos/pos_header');
    ?>
     
    <div id="content">
                 <div id="printdiv">
        <div id="receiptData">
            <div class="no-print">
                
            <h4 class="modal-title" id="myModalLabel"><?= lang('report_view_access'); ?></h4>        
        
        <div class="modal-body">          

            <div class="form-group">
                <?= lang('Pass code', 'pass_code'); ?>
                <input type="password" class="form-control kb-pad" id="pass_code" name="pass_code" value="">                
            </div>

                    
        </div>
        <div class="modal-footer">
            <?= form_submit('submit', lang('submit'), 'class="btn btn-primary submit"'); ?>
        </div>
    </div>
    </div>
</div>
    </div>
</div>

<?php
$this->load->view($this->theme . 'pos/pos_footer');
?>

<script type="text/javascript">
   $(document).on('click', '.submit', function () {
    <?php $type = $this->input->get('type'); ?>
           var pass_code = $('#pass_code').val();            
              $url = '<?=admin_url('pos/report_view_access');?>';
            if (pass_code !='' ) {
                $('#pass_code').css('border-color', '#ccc'); 
                  $.ajax({
                        type: 'POST',
                        url: $url,                    
                        data: {pass_code: pass_code},
                        dataType: "json",
                        success: function (data) {
                            if(data != 0){                
                             var url = '<?php echo  admin_url('pos/reports') ?>';
                              window.location.href= url +'/?type=<?php echo $type; ?>'; 
                                 // window.location.href = window.location.href;
                            }
                        } 
                   });    
            } 
            else{
                $('#pass_code').css('border-color', 'red'); 
            }
        });
display_keyboards();
//$('.kb-pad').keyboard({
//         restrictInput: true,
//        preventPaste: true,
//        autoAccept: true,
//        alwaysOpen: false,
//        openOn: 'click',
//        usePreview: false,
//        layout: 'custom',
//        display: {
//            'b': '\u2190:Backspace',
//        },
//        customLayout: {
//            'default': [
//            '1 2 3 ',
//            '4 5 6 ',
//            '7 8 9 0',
//            '{accept} {cancel}'
//            ]
//        }
//    });   
</script>
</body>
</html>
