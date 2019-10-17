<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<script>
    $(document).ready(function(){
      $('#add-varient')
        .bootstrapValidator(
                            {
                                message: 'Please enter/select a value',
                                //submitButtons: 'input[type="submit"]',
                                
                            }
                        )
        .on('success.form.bv', function(e) {

            var canvasHeight = 60;
        var canvas = document.getElementById('myCanvas');
        var context = canvas.getContext('2d');
        var maxWidth = 350;
        var lineHeight = 35;
        var x = (canvas.width - maxWidth) / 2;
        var y = 60;
        var text = $('#native_name').val();
        $arrayWords = [];
        $stringLength = text.length;
        $wordsCnt = Math.ceil($stringLength/20);
        canvasHeight = (($wordsCnt-1)*40)+60;
        var $start = 0;var $end =20;
        for(var $n = 0; $n < $wordsCnt; $n++) {
        $str = text.substring($start, $end);
        $start = $end;$end =$start+20;
        $arrayWords.push(' '+$str);
        }
        
        /// set height ///
        $('#myCanvas').attr('height',canvasHeight);
        ///// end-set height //////////
        text =  $arrayWords.join('');
        context.font = '36px KHMEROSBATTAMBANG-REGULAR';
        context.fillStyle = '#333';
        wrapText(context, text, x, y, maxWidth, lineHeight);
        $('#recipe-name-img').val(canvas.toDataURL());

            // Prevent form submission
            e.preventDefault();
	    $('.counter-form-error').remove();
            $obj = $('#add-varient');
            $url = $obj.attr('data-url');
            $formData = $obj.serialize();
	    //console.log($parentCategory);
	    $.ajax({
                    url: $url,
                    type: "POST",
                    data: $formData,//$formData+'&userfile='+data+'&add_brand=Add Brand',
                    //cache: false,
		    dataType: 'json',
		    //processData: false, // Don't process the files
		    //contentType: false,
                    success:function(data){
			 
                         location.reload();
                       
                    },
                   
                });
        });
        
    });
     function wrapText(context, text, x, y, maxWidth, lineHeight) {
        var words = text.split(' ');
        var line = '';

        for(var n = 0; n < words.length; n++) {
          var testLine = line + words[n] + ' ';
      if(n==0){testLine =testLine.trim()}
          var metrics = context.measureText(testLine);
          var testWidth = metrics.width;
          if (testWidth > maxWidth && n > 0) {
            context.fillText(line, x, y);
            line = words[n] + ' ';
            y += lineHeight;
          }
          else {
            line = testLine;
          }
        }
        context.fillText(line, x, y);
    
    }

</script>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('edit_varient'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><!-- <?php echo lang('enter_info'); ?> --></p>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form');
        //echo admin_form_open_multipart("system_settings/add_category", $attrib); ?>
	<form  data-toggle="validator" data-url="<?=admin_url('recipe/edit_varient/'.$variant->id)?>" role="form" enctype="multipart/form-data" method="post" accept-charset="utf-8" novalidate class="bv-form" id="add-varient">
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>

         <!--   
          <div class="form-group">
                <?= lang('code', 'code'); ?>
               
                <div class="input-group col-md-12">
                	<?= form_input('code', '', 'class="form-control numberonly" id="code" required="required" maxlength="9" '); ?>
                     <span class="" id="random_num" style="    padding: 6px 10px;
    background: #efefef;
    position: relative;
    margin-top: -34px;
    border: 1px solid #ccc;
    float: right;
    z-index: 99;
    cursor: pointer;">
                        <i class="fa fa-random"></i>
                    </span>
                   
                </div>
                
                
            </div>-->

            <div class="form-group">
                <?= lang('varient_name', 'name'); ?>
                <?= form_input('name', set_value('name',$variant->name), 'class="form-control gen_slug" id="name" required="required"'); ?>
            </div>
		
        	<div class="form-group">
                <?= lang('native_name', 'native_name'); ?>
                <?= form_input('native_name', set_value('native_name',$variant->native_name), 'class="form-control" id="native_name" required="required"'); ?>
            </div>
           <div class="form-group all">
                        <?= lang("variant_code", "code") ?>
                        <div class="input-group">
                            <?= form_input('variant_code', (isset($_POST['variant_code']) ? $_POST['variant_code'] : ($variant ? $variant->variant_code : '')), 'class="form-control" id="variant_code"  required="required"') ?>
                            <span class="input-group-addon pointer" id="random_num" style="padding: 1px 10px;">
                                <i class="fa fa-random"></i>
                            </span>
                        </div>
                       <!-- <span class="help-block"><?= lang('you_scan_your_barcode_too') ?></span>-->
                        <label for="code" class="text-danger"></label>
                    </div>

            

        </div>
        <div class="modal-footer">
	    <input type="hidden" name="edit_varient" value="<?=lang('edit_varient')?>">

        <canvas id="myCanvas" width="360" height="60" style="display: none;"></canvas>
                <input type="hidden" name="recipe_name_img" id="recipe-name-img" value="">

            <?php echo form_submit('edit_varient', lang('update_varient'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
      </div>

        </div>
    </div>
</div>
<script>
	$(document).ready(function(){
	    $('#random_num').click(function(){
            $(this).parent('.input-group').children('input').val(generateCardNo(8));
        });
	})
    </script>
<script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>
<?= $modal_js ?>
