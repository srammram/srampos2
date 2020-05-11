<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<script>
	$(window).load(function(e) {
        localStorage.clear();
    });
    $(document).ready(function () {
        oTable = $('#store_receiversTable').dataTable({
            "aaSorting": [[1, "desc"], [2, "desc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?=lang('all')?>"]],
            "iDisplayLength": <?=$Settings->rows_per_page?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?=admin_url('procurment/store_receivers/getStore_receivers' . ($warehouse_id ? '/' . $warehouse_id : ''))?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?=$this->security->get_csrf_token_name()?>",
                    "value": "<?=$this->security->get_csrf_hash()?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            "aoColumns": [{"bSortable": false,"mRender": checkbox}, {"mRender": fld}, null,null, null, null,null,  {"mRender": row_status},  {"bSortable": false}],
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                var oSettings = oTable.fnSettings();
                nRow.id = aData[0];
                nRow.className = "store_receivers_link";
                return nRow;
            },
            // id,date,reference_no,customer,total,total_discount,total_tax,grand_total,Actions
            "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
                var total= 0;
                var total_tax= 0;
                var total_discount= 0;
                var grand_total= 0;
                for (var i = 0; i < aaData.length; i++) {                    
                    total += parseFloat(aaData[aiDisplay[i]][4]);
                    total_discount += parseFloat(aaData[aiDisplay[i]][5]);
                    total_discount += parseFloat(aaData[aiDisplay[i]][6]);
                    grand_total += parseFloat(aaData[aiDisplay[i]][7]);
                }
                var nCells = nRow.getElementsByTagName('th');
                nCells[4].innerHTML = currencyFormat(total);
                nCells[5].innerHTML = currencyFormat(total_discount);
                nCells[6].innerHTML = currencyFormat(total_tax);
                nCells[7].innerHTML = currencyFormat(grand_total);
            }
        }).fnSetFilteringDelay().dtFilter([
            {column_number: 1, filter_default_label: "[<?=lang('date');?> (yyyy-mm-dd)]", filter_type: "text", data: []},
            {column_number: 2, filter_default_label: "[<?=lang('reference_no');?>]", filter_type: "text", data: []},
            {column_number: 3, filter_default_label: "[<?=lang('customer');?>]", filter_type: "text", data: []},
        ], "footer");

       
    });

</script>

<?php if ($Owner || $GP['bulk_actions']) {
	    echo admin_form_open('store_receivers/store_receivers_actions', 'id="action-form"');
	}
?>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i
                class="fa-fw fa fa-star"></i><?=lang('store_receivers') . ' (' . ($warehouse_id ? $warehouse->name : lang('all_warehouses')) . ')';?>
        </h2>

        <div class="box-icon">
            <ul class="btn-tasks">
                <li class="dropdown">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#"><i class="icon fa fa-tasks tip" data-placement="left" title="<?=lang("actions")?>"></i></a>
                    <ul class="dropdown-menu pull-right tasks-menus" role="menu" aria-labelledby="dLabel">
                       
                      
                    </ul>
                </li>
               
            </ul>
        </div>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?=lang('list_results');?></p>

                <div class="table-responsive">
                    <table id="store_receiversTable" cellpadding="0" cellspacing="0" border="0"
                           class="table table-bordered table-hover table-striped">
                        <thead>
                        <tr class="active">
                            <th style="min-width:30px; width: 30px; text-align: center;">
                                <input class="checkbox checkft" type="checkbox" name="check"/>
                            </th>
                            <th><?= lang("date"); ?></th>
                            <th><?= lang("ref_no"); ?></th>
                            <th><?= lang("request_no"); ?></th>
                            <th><?= lang("from_store"); ?></th>
                            <th><?= lang("to_store"); ?></th>
                            <th><?= lang("total_transfer_QTY"); ?></th>
                            <th><?= lang("status")?></th>              
                            <th style="width:100px;"><?= lang("actions"); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td colspan="11" class="dataTables_empty"><?=lang('no data found');?></td>
                        </tr>
                        </tbody>
                        <tfoot class="dtFilter" style="display: none">
                        <tr class="active">
                            <th style="min-width:30px; width: 30px; text-align: center;">
                                <input class="checkbox checkft" type="checkbox" name="check"/>
                            </th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th><?= lang("total"); ?></th>                           
                            <th><?= lang("total_discount"); ?></th>
                            <th><?= lang("total_tax"); ?></th>
                            <th><?= lang("grand_total"); ?></th>
                            <th></th>
                            
                            <th style="width:100px; text-align: center;"><?= lang("actions"); ?></th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php if ($Owner || $GP['bulk_actions']) {?>
    <div style="display: none;">
        <input type="hidden" name="form_action" value="" id="form_action"/>
        <?=form_submit('performAction', 'performAction', 'id="action-form-submit"')?>
    </div>
    <?=form_close()?>
<?php }
?>