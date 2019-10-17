<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content" style="width:68%!important">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('deactivate'); ?></h4>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form');
        echo admin_form_open("recipe/deactivate/" . $recipe->id, $attrib); ?>
        <div class="modal-body">
          <p style="font-weight: bold;">  <?php if($user_lang == 'english' ){echo $recipe->name;} else{echo $recipe->khmer_name;} ?><br><p>
            <div class="text-center"><a href="<?php echo base_url().$this->upload_path.$recipe->image; ?>" data-toggle="lightbox"><img src="<?php echo base_url().$this->upload_path.$recipe->image; ?>" alt="" style="max-width: 100%;"/></a>
            </div>

            

<?php 
/*echo "<pre>";
print_r($recipe);*/
/*echo $this->upload_path;
echo "<br>";
echo base_url().$this->upload_path;
echo $recipe->image; */
 ?>
            <!-- <div class="text-center"><a href="'+site.url+'assets/uploads/' + image_link + '" data-toggle="lightbox"><img src="'+site.url+'assets/uploads/thumbs/' + image_link + '" alt="" style="width:30px; height:30px;" /></a>
            </div> -->
<!-- no_image.png -->
            <p><?php echo sprintf(lang('deactivate_heading'), $recipe->name); ?></p>

            <div class="form-group">
                <label class="checkbox" for="confirm">
                    <input type="checkbox" name="confirm" value="yes" checked="checked" id="confirm"/> <?= lang('yes') ?>
                </label>

            </div>

            <?php echo form_hidden(array('id' => $recipe->id)); ?>

        </div>
        <div class="modal-footer">
            <input type="submit" name="deactivate" value="<?=lang('deactivate');?>" class="btn btn-primary" autocomplete="off">
            <!-- <?php echo form_submit('deactivate', lang('deactivate'), 'class="btn btn-primary"'); ?> -->
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<?= $modal_js ?>
