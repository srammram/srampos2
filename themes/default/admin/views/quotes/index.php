<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<script>
    $(document).ready(function () {
        oTable = $('#QuotationData').dataTable({
            "aaSorting": [[3, "desc"], [4, "desc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?=lang('all')?>"]],
            "iDisplayLength": <?=$Settings->rows_per_page?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?=admin_url('quotes/getQuotation' . ($warehouse_id ? '/' . $warehouse_id : ''))?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?=$this->security->get_csrf_token_name()?>",
                    "value": "<?=$this->security->get_csrf_hash()?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            "aoColumns": [null,{"bSortable": false,"mRender": checkbox},null, {"mRender": fld}, null,null, {"mRender": currencyFormat},{"mRender": currencyFormat}, {"mRender": currencyFormat}, {"mRender": currencyFormat},   {"bSortable": false}],
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                var oSettings = oTable.fnSettings();
                nRow.id = aData[1];
                nRow.className = "quote_link";
		$index = oSettings._iDisplayStart+parseInt(iDisplayIndex) +parseInt(1) ;
                $("td:first", nRow).html($index);
                return nRow;
            },
            // id,date,reference_no,customer,total,total_discount,total_tax,grand_total,Actions
            "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
                var total= 0;
                var total_tax= 0;
                var total_discount= 0;
                var grand_total= 0;
                for (var i = 0; i < aaData.length; i++) {                    
                    total += parseFloat(aaData[aiDisplay[i]][6]);
                    total_discount += parseFloat(aaData[aiDisplay[i]][7]);
                    total_discount += parseFloat(aaData[aiDisplay[i]][8]);
                    grand_total += parseFloat(aaData[aiDisplay[i]][9]);
                }
                var nCells = nRow.getElementsByTagName('th');
                nCells[6].innerHTML = currencyFormat(total);
                nCells[7].innerHTML = currencyFormat(total_discount);
                nCells[8].innerHTML = currencyFormat(total_tax);
                nCells[9].innerHTML = currencyFormat(grand_total);
            }
        }).fnSetFilteringDelay().dtFilter([
            {column_number: 1, filter_default_label: "[<?=lang('date');?> (yyyy-mm-dd)]", filter_type: "text", data: []},
            {column_number: 2, filter_default_label: "[<?=lang('reference_no');?>]", filter_type: "text", data: []},
            {column_number: 3, filter_default_label: "[<?=lang('customer');?>]", filter_type: "text", data: []},
        ], "footer");

        <?php if ($this->session->userdata('remove_pols')) {?>
        if (localStorage.getItem('quoteitems')) {
            localStorage.removeItem('quoteitems');
        }
        if (localStorage.getItem('quotediscount')) {
            localStorage.removeItem('quotediscount');
        }
        if (localStorage.getItem('quotetax2')) {
            localStorage.removeItem('quotetax2');
        }
        if (localStorage.getItem('quoteshipping')) {
            localStorage.removeItem('quoteshipping');
        }
        if (localStorage.getItem('quoteref')) {
            localStorage.removeItem('quoteref');
        }
        if (localStorage.getItem('quotewarehouse')) {
            localStorage.removeItem('quotewarehouse');
        }
        if (localStorage.getItem('quotenote')) {
            localStorage.removeItem('quotenote');
        }
        if (localStorage.getItem('quotesupplier')) {
            localStorage.removeItem('quotesupplier');
        }
        if (localStorage.getItem('quotecurrency')) {
            localStorage.removeItem('pocurrency');
        }
        if (localStorage.getItem('quoteextras')) {
            localStorage.removeItem('quoteextras');
        }
        if (localStorage.getItem('quotedate')) {
            localStorage.removeItem('quotedate');
        }
        if (localStorage.getItem('quotestatus')) {
            localStorage.removeItem('quotestatus');
        }
        if (localStorage.getItem('quotepayment_term')) {
            localStorage.removeItem('quotepayment_term');
        }
        <?php $this->sma->unset_data('remove_pols');}
        ?>
    });

</script>

<?php if ($Owner || $GP['bulk_actions']) {
	    echo admin_form_open('quotes/quotes_actions', 'id="action-form"');
	}
?>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i
                class="fa-fw fa fa-star"></i><?=lang('quotation') . ' (' . ($warehouse_id ? $warehouse->name : lang('all_warehouses')) . ')';?>
        </h2>

        <div class="box-icon">
            <ul class="btn-tasks">
                <li class="dropdown">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#"><i class="icon fa fa-tasks tip" data-placement="left" title="<?=lang("actions")?>"></i></a>
                    <ul class="dropdown-menu pull-right tasks-menus" role="menu" aria-labelledby="dLabel">
                        <li>
                            <a href="<?=admin_url('quotes/add')?>">
                                <i class="fa fa-plus-circle"></i> <?=lang('add_quote')?>
                            </a>
                        </li>
                       <!--  <li>
                            <a href="#" id="excel" data-action="export_excel">
                                <i class="fa fa-file-excel-o"></i> <?=lang('export_to_excel')?>
                            </a>
                        </li>
                        <li>
                            <a href="#" id="combine" data-action="combine">
                                <i class="fa fa-file-pdf-o"></i> <?=lang('combine_to_pdf')?>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="#" class="bpo" title="<b><?=lang("delete_quote")?></b>"
                                data-content="<p><?=lang('r_u_sure')?></p><button type='button' class='btn btn-danger' id='delete' data-action='delete'><?=lang('i_m_sure')?></a> <button class='btn bpo-close'><?=lang('no')?></button>"
                                data-html="true" data-placement="left">
                                <i class="fa fa-trash-o"></i> <?=lang('delete_quote')?>
                            </a>
                        </li> -->
                    </ul>
                </li>
                <?php if (!empty($warehouses)) {
                    ?>
                    <li class="dropdown">
                        <a data-toggle="dropdown" class="dropdown-toggle" href="#"><i class="icon fa fa-building-o tip" data-placement="left" title="<?=lang("warehouses")?>"></i></a>
                        <ul class="dropdown-menu pull-right tasks-menus" role="menu" aria-labelledby="dLabel">
                            <li><a href="<?=admin_url('quotes')?>"><i class="fa fa-building-o"></i> <?=lang('all_warehouses')?></a></li>
                            <li class="divider"></li>
                            <?php
                            	foreach ($warehouses as $warehouse) {
                            	        echo '<li ' . ($warehouse_id && $warehouse_id == $warehouse->id ? 'class="active"' : '') . '><a href="' . admin_url('quotes/' . $warehouse->id) . '"><i class="fa fa-building"></i>' . $warehouse->name . '</a></li>';
                            	    }
                                ?>
                        </ul>
                    </li>
                <?php }
                ?>
            </ul>
        </div>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?=lang('list_results');?></p>

                <div class="table-responsive">
                    <table id="QuotationData" cellpadding="0" cellspacing="0" border="0"
                           class="table table-bordered table-hover table-striped">
                        <thead>
                        <tr class="active">
			    <th><?= lang("s.no"); ?></th>
                            <th style="min-width:30px; width: 30px; text-align: center;">
                                <input class="checkbox checkft" type="checkbox" name="check"/>
                            </th>
			    <th><?= lang("branch"); ?></th>
                            <th><?= lang("date"); ?></th>
                            <th><?= lang("ref_no"); ?></th>
                            <th><?= lang("customer"); ?></th>
                            <th><?= lang("total"); ?></th>
                            <th><?= lang("total_discount"); ?></th>
                            <th><?= lang("total_tax"); ?></th>
                            <th><?= lang("grand_total"); ?></th>                      
                            <th style="width:100px;"><?= lang("actions"); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td colspan="11" class="dataTables_empty"><?=lang('loading_data_from_server');?></td>
                        </tr>
                        </tbody>
                        <tfoot class="dtFilter" style="display: none">
                        <tr class="active">
			    <th></th>
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