<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('edit_deposit') . " (" . $company->name . ")"; ?></h4>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form');
        echo admin_form_open("customers/edit_deposit/" . $deposit->id, $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>

            <div class="row">
                <div class="col-sm-12">
                    <?php if ($Owner || $Admin) { ?>
                    <div class="form-group">
                        <?php echo lang('date', 'date'); ?>
                        <div class="controls">
                            <?php echo form_input('date', set_value('date', $this->sma->hrld($deposit->date)), 'class="form-control datetime" id="date" required="required"'); ?>
                        </div>
                    </div>
                    <?php } ?>

                    

                    <div class="form-group">
                        <?php echo lang('paid_by', 'paid_by'); ?>
                        <div class="controls">
                            <input type="text" name="paid_by" class="form-control" id="paid_by"
                               value="<?=$company->customer_type?>" readonly/>
                        </div>
                    </div>
                    <div class="form-group">
                        <?php echo lang('credit_amount', 'credit_amount'); ?>
                        <div class="controls">
                            <?php echo form_input('credit_amount', set_value('credit_amount', $deposit->credit_amount), 'class="form-control" id="credit_amount" required="required" readonly="readonly"'); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <?php echo lang('credit_used', 'credit_used'); ?>
                        <div class="controls">
                            <?php echo form_input('credit_used',$deposit->credit_used, 'class="form-control" id="credit_used" readonly="readonly"'); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <?php echo lang('credit_balance', 'credit_balance'); ?>
                        <div class="controls">
                            <?php echo form_input('credit_balance', $deposit->credit_balance, 'class="form-control" id="credit_balance" readonly="readonly"'); ?>
                        </div>
                    </div>
                    <?php if($company->customer_type=="prepaid") : ?>
                        <?php echo lang('Return_Amount', 'return_amount'); ?>
                        <div class="controls">
                            <input type="text" name="return_amount" value="" class="form-control" data-balance="<?=$deposit->credit_balance?>" id="return_amount">
                        </div>
                    <?php endif;?>
                    <div class="form-group">
                        <?php echo lang('note', 'note'); ?>
                        <div class="controls">
                            <?php echo form_textarea('note', $deposit->note, 'class="form-control" id="note"'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <?php echo form_submit('edit_deposit', lang('edit_deposit'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<?= $modal_js ?>
<script>
    $(document).ready(function(){
        $('#return_amount').change(function(){
            if(parseFloat($(this).val())>parseFloat($(this).attr('data-balance'))){$(this).val('');alert('Return Amount should not be greater than available Credit balance')};
        });
    })
</script>

