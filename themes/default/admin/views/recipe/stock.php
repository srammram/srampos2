<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<style type="text/css">
    .modal-lg {
    width: 1323px !important;
}
</style>
<script>
 var oTable;
    $(document).ready(function () {
        oTable = $('#StockData').dataTable({
			 "aaSorting": [[3, "asc"], [4, "asc"]],
			 "aLengthMenu": [[1,10, 25, 50, 100, -1], [1,10, 25, 50, 100, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= admin_url('recipe/stock_details/'.$id) ?>',
			'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                var oSettings = oTable.fnSettings();
                nRow.id = aData[1];
                nRow.className = "recipe_link";
		var oSettings = oTable.fnSettings();
                $index = oSettings._iDisplayStart+parseInt(iDisplayIndex) +parseInt(1) ;
                $("td:first", nRow).html($index);
                //if(aData[7] > aData[9]){ nRow.className = "recipe_link warning"; } else { nRow.className = "recipe_link"; }
                return nRow;
            },
			 "aoColumns": [
			 	null,null, null,null,null, null,null,null,null,null,null,null,null
			 ],
		}).fnSetFilteringDelay().dtFilter([
            
			],"footer");
	});
</script>
<style>
    #StockData_filter{
	display: none;
    }
</style>

<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?=$recipe->code.' - '.$recipe->name?> - <?php echo lang('stock_details'); ?>
            <label style="float:right;margin-right:13px;"> <span>Total Stock : </span><?=@$total_stock?></label></h4>
        </div>
      
      
        <div class="modal-body">
          <div class="table-responsive">
                    <table id="StockData" class="table table-bordered table-condensed table-hover table-striped">
                        <thead>
                        <tr class="primary">
							<th><?= lang("s.no") ?></th>
                            <th><?= lang("store") ?></th>
							<th><?= lang("category") ?></th>
							<th><?= lang("subcategory") ?></th>
							<th><?= lang("brand") ?></th>
                            <th><?= lang("stock_in") ?></th>
                            <th><?= lang("stock_out") ?></th>
                            <th><?= lang("current_stock") ?></th>
							<th><?= lang("stock_piece") ?></th>
                            <!-- <th><?= lang("stock_out") ?></th>
							<th><?= lang("stock_out_piece") ?></th> -->
                            <th><?= lang("batch") ?></th>
                            <th><?= lang("cost_price") ?></th>
                            <th><?= lang("selling_price") ?></th>
                            <th><?= lang("expiry") ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td colspan="13" class="dataTables_empty"><?= lang('loading_data_from_server'); ?></td>
                        </tr>
                        </tbody>

                        <tfoot class="dtFilter">
                        <tr class="active">
							<th></th>
                            <th></th>
							<th></th>
                            <th></th>
                            <th></th>
							<th></th>
							<th></th>
                            <th></th>
							<th></th>
                            <!-- <th></th>
                            <th></th> -->
							<th></th>
                            <th></th>
                            <th></th>
							<th></th>
                        </tr>
                        </tfoot>
                    </table>
                </div>

        </div>
        <div class="modal-footer">
            
        </div>
    </div>
    
</div>
<?= $modal_js ?>