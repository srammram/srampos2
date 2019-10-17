<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<script>
    
</script>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-cog"></i><?= lang('select_tables_to_sync'); ?></h2>

       
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?= lang('update_info'); ?></p>

              
                <div class="row">
                    <div class="col-lg-12">
                        <fieldset class="scheduler-border">
                            <legend class="scheduler-border"><?= lang('sync') ?></legend>
                           
                            <div class="col-md-4">
                                <div class="form-group">
                                    <?php if($this->centerdb_connected) : ?>
                                    <a href="<?=admin_url('system_settings/sync?sync')?>" class="btn btn-primary">Sync Now</a>
                                    <?php else :?>
                                    <p class="introtext"><?= lang('No internet connection or center server might be down'); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                    </fieldset>


                </div>
            </div>
            
        </div>
    </div>

</div>
</div>
