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
    <link href="https://fonts.googleapis.com/css?family=Barlow+Condensed&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="<?=$assets?>images/favicon.ico"/>
     <link rel="stylesheet" href="<?=$assets?>styles/style.css" type="text/css"/>
    <link rel="stylesheet" href="<?=$assets?>styles/theme.css" type="text/css"/>
	<link rel="stylesheet" href="<?=$assets?>pos/css/posajax.css" type="text/css">
   <link rel="stylesheet" href="<?=$assets?>styles/frontend_new.css" type="text/css">
    <script type="text/javascript" src="<?=$assets?>js/jquery-2.0.3.min.js"></script>
    <!--[if lt IE 9]>
    <script src="<?=$assets?>js/jquery.js"></script>
    <![endif]-->
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
<div class="outer_screen">		
	<div class="outer_i_screen">		
		<div class="login_screen left">
			<div class="main">
<?php
if ($error) {
	?>
	<div class="alert alert-danger">
		<button data-dismiss="alert" class="close" type="button">×</button>
		<ul class="list-group"><?= $error; ?></ul>
	</div>
	<?php
}
if ($message) {
	?>
	<div class="alert alert-success">
		<button data-dismiss="alert" class="close" type="button">×</button>
		<ul class="list-group"><?= $message; ?></ul>
	</div>
	<?php
}
?>
		<h1>LOGIN</h1>
		<?php echo form_open("pos/login", 'class="login" data-toggle="validator"'); ?>
	
<div class="form-group">
<input type="password" name="user_number" id="user_number" class="form-control kb-pad" placeholder="<?=lang('password')?>"  autocomplete="off" maxLength="4">
</div>
<?php if($this->Settings->login_group_required) : ?>
	<div class="form-group">
	<?php
	$ug[''] = lang('select_groups');
		foreach ($groups as $group) {
			$ug[$group->id] = $group->name;
		}
		echo form_dropdown('user_group', $ug, (isset($_POST['user_group']) ? $_POST['user_group'] : ''), 'id="posuser_group" class="form-control pos-input-tip" data-placeholder="'.lang('select_groups').'" required="required" style="width:100%;" ');
	?>
	</div>
<?php endif; ?>
<?php if($this->Settings->login_warehouse_required) : ?>
	<div class="form-group">
	<?php
	$wh[''] = lang('select_branch');
		foreach ($warehouses as $warehouse) {
			$wh[$warehouse->id] = $warehouse->name;
		}
		echo form_dropdown('user_warehouse', $wh, (isset($_POST['user_warehouse']) ? $_POST['user_warehouse'] : ''), 'id="posuser_warehouse" class="form-control pos-input-tip" data-placeholder="Select Branch" required="required" style="width:100%;" ');
	?>
	</div>
    <?php endif; ?>

<?php /*?><button type="reset" class="btn  btn-danger pull-left"><?= lang('reset') ?> &nbsp; <i class="fa fa-sign-in"></i></button><?php */?>
<button type="submit" class="btn btn-success login_btn_s center-block"><?= lang('login') ?> &nbsp; <i class="fa fa-sign-in"></i></button>
		<?php echo form_close(); ?>
		</div>
	</div>
	</div>
</div>

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
<?php /*<script type="text/javascript" src="<?=$assets?>pos/js/pos.ajax.js"></script>*/?>
<script type="text/javascript" src="<?=$assets?>js/dragscrollable.js"></script>
<script type="text/javascript" src="<?=$assets?>js/dragscroll.js"></script>
<script>
		$('#posuser_group').select2({
		placeholder: 'Select Group'
			});
		$('#posuser_warehouse').select2({
		placeholder: 'Select Warehouse'
			});
		$('.kb-pad').keyboard({
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
		maxLength: 4,
        display: {
            'b': '\u2190:Backspace',
        },
        customLayout: {
            'default': [
            '1 2 3 4',
            '5 6 7 8  ',
            ' 9 0 {b}',

            ' {accept} {cancel}'
            ]
        }
    });
			
		$('.select').select2();
		
		
		$(".login_btn_s").click(function(){
			$error=false;
			if($('#user_number').val().length<1){
				bootbox.alert('Please Enter Password');
				$error=true;
				return false;
			}
			<?php if($this->Settings->login_group_required) : ?>
			if($('#posuser_group').val().length<1){
				bootbox.alert('Please Select Group');
				$error=true;
				return false;
			}
			if($('#posuser_warehouse').val().length<1){
				bootbox.alert('Please Select Branch');
				$error=true;
				return false;
			}
			 <?php endif; ?>
			if($error){
			return false;
			}else{
			$('form.login').submit();
			}
  
});
	</script>

</body>
</html>
