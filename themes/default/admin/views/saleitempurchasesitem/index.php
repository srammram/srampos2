<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<script>
$(document).ready(function () {
	myTable = $('#Table1').dataTable({
		"iDisplayLength": 15,
		
	});
	
});

</script>



<?= admin_form_open('tables/actions', 'id="action-form"') ?>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-th-list"></i><?= lang('Bill Of Material'); ?></h2>

            </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <p class="introtext"><?= lang('list_results'); ?></p>
                <div class="table-responsive">
                	
                    <table id="Table1" class="table table-bordered table-hover table-striped reports-table">
                        <thead>
                            <tr>
                               
                               <th><?= lang("s.no"); ?></th>
			       <th><?= lang("branch"); ?></th>
                                <th><?= lang("Sale Code"); ?></th>
                                <th><?= lang("Sale Item"); ?></th>
                                <th><?= lang("Purchases Item Details"); ?></th>
                                
                            </tr>
                        </thead>
                        <tbody>
                        	<?php
							if(!empty($sale_purchase)){
								foreach($sale_purchase as $k => $sale_purchase_row)
								{
							?>
                            <tr>
				<td><?php echo $k+1; ?></td>
				<td><?php echo $sale_purchase_row->branch; ?></td>
                                <td><?php echo $sale_purchase_row->code; ?></td>
                                <td><?php echo $sale_purchase_row->name; ?></td>
                                <td><?php echo $sale_purchase_row->product_details; ?></td>
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
</div>

<div style="display: none;">
    <input type="hidden" name="form_action" value="" id="form_action"/>
    <?= form_submit('submit', 'submit', 'id="action-form-submit"') ?>
</div>
<?= form_close() ?>




