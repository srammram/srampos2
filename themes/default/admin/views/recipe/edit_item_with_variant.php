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

           /*  var canvasHeight = 60;
            var canvas = document.getElementById('myCanvas'+val);
            var context = canvas.getContext('2d');
            var maxWidth = 360;
            var lineHeight = 42;
           //var x = (canvas.width - maxWidth) / 2;
            var x = 5;
            var y = 40;                        
            var variant_native_name = $('#item_native_name').val();
            var qty = $('.rquantity'+val).val();

            var text = variant_native_name;            
            $arrayWords = [];
            $stringLength = text.length;
            $wordsCnt = Math.ceil($stringLength/20);
            canvasHeight = (($wordsCnt-1)*40)+60;
            var $start = 0;var $end =25;
            for(var $n = 0; $n < $wordsCnt; $n++) {
                $str = text.substring($start, $end);
                $start = $end;$end =$start+25;
                // alert($str.length);
                if($str.length !=0){
                  $arrayWords.push($str+' ');
                }
                    
            }
        
        /// set height ///
        $('#myCanvas').attr('height',canvasHeight);   
        ///// end-set height //////////
        text =  $arrayWords.join('');
        // context.font = '23px KHMEROSBATTAMBANG-REGULAR';        
        context.font = '28px AKbalthom Kbach';
        context.fillStyle = '#000';
        wrapText(context, text, x, y, maxWidth, lineHeight,canvasHeight,qty);
        $('#recipe-name-img').val(canvas.toDataURL());*/

            var canvasHeight = 60;
            var canvas = document.getElementById('myCanvas');
            var context = canvas.getContext('2d');
            var maxWidth = 350;
            var lineHeight = 35;
           //var x = (canvas.width - maxWidth) / 2;
            var x = 5;
            var y = 60;
            var item_native_name = $('#item_native_name').val();
            var variant_native_name = $('#variant_native_name').val();
            var text = item_native_name+variant_native_name;            
            var text = item_native_name;            
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
        // context.font = '23px KHMEROSBATTAMBANG-REGULAR';
        context.font = 'bold 28px AKbalthom Kbach';
        context.fillStyle = '#000';
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

     String.prototype.rtrim = function () {
        return this.replace(/((\s*\S+)*)\s*/, "$1");
     }

    function wrapText(context, text, x, y, maxWidth, lineHeight,canvasHeight,qty) {
        $txt = text.rtrim();
        var words = $txt.split(' ');
        var line = ''; 
        for(var n = 0; n < words.length; n++) {
          var testLine = line + words[n];          
        if(n==0){testLine =testLine.trim()}
          var metrics = context.measureText(testLine);          
          var testWidth = metrics.width;
          var testHeight = metrics.height;          
              if (testWidth > maxWidth && n > 0) {
                context.fillText(line, x, y);
                line = words[n] + ' ';                        
                y += lineHeight;            
              }else {             
                line = testLine;
          }
        }       
         context.fillText(line, x, y);        
         if (y > 41) {
             context.fillText(qty,maxWidth * 1.2, y/2);             
         }else{
             context.fillText(qty,maxWidth * 1.2, y);             
         }
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
	<form  data-toggle="validator" data-url="<?=admin_url('recipe/edit_item_with_varient/'.$variant->id)?>" role="form" enctype="multipart/form-data" method="post" accept-charset="utf-8" novalidate class="bv-form" id="add-varient">
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>

         
            <div class="form-group">
                <?php $con =$variant->item_native_name.$variant->variant_native_name ?>
                <?= lang('item_native_name', 'item_native_name'); ?>
                <?= form_input('item_native_name', set_value('item_native_name',$con), 'class="form-control gen_slug" id="item_native_name" readonly  required="required"'); ?>
            </div>
		
        	<div class="form-group">
                <?= lang('variant_native_name', 'variant_native_name'); ?>
                <?= form_input('variant_native_name', set_value('variant_native_name',$variant->variant_native_name), 'class="form-control" id="variant_native_name" readonly required="required"'); ?>
            </div>

            <div class="form-group">
                <?= lang('item_name', 'item_name'); ?>
                <?= form_input('item_name', set_value('item_name',$variant->item_name), 'class="form-control" id="item_name" readonly required="required"'); ?>
                
            </div>

            <div class="form-group">
                <?= lang('variant_name', 'variant_name'); ?>
                <?= form_input('variant_name', set_value('variant_name',$variant->variant_name), 'class="form-control" id="variant_name" readonly required="required"'); ?>
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
