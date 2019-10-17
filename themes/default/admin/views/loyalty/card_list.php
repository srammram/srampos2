<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!-- <script type="text/javascript" src="<?=$assets ?>js/customer_discount.js"></script> -->
<script>
var oTable;
    $(document).ready(function () {
        oTable = $('#SupData').dataTable({
            "aaSorting": [[2, "asc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= admin_url('loyalty_settings/getLoyaltyCards') ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
             'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                var oSettings = oTable.fnSettings();
               
                $index = oSettings._iDisplayStart+parseInt(iDisplayIndex) +parseInt(1) ;
                $("td:first", nRow).html($index);
                return nRow;
            },
            "aoColumns": [null,{
                "bSortable": false,
                "mRender": checkbox
            },  null, null,{"mRender": loyalty_card_status}]
        }).dtFilter([
            {column_number: 2, filter_default_label: "[<?=lang('loyalty_name');?>]", filter_type: "text", data: []},
            {column_number: 3, filter_default_label: "[<?=lang('strat_date');?>]", filter_type: "text", data: []},
        ], "footer");
    });
     $(document).on('click',".modal .close",function(){
        $("#myModal").html("");
        $("#myModal2").html("");
    });
</script>
<?php if ($Owner || $GP['bulk_actions']) {
    echo admin_form_open('loyalty_settings/loyalty_settings_actions', 'id="action-form"');
} ?>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-users"></i><?= lang('loyalty_cards'); ?></h2>
        <div class="box-icon">
            <ul class="btn-tasks">
                <li class="dropdown">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#"><i class="icon fa fa-tasks tip" data-placement="left" title="<?= lang("actions") ?>"></i></a>
                    <ul class="dropdown-menu pull-right tasks-menus" role="menu" aria-labelledby="dLabel">
                        <li><a href="<?= admin_url('loyalty_settings/loyalty_card_add'); ?>" data-toggle="modal"><i class="fa fa-plus-circle"></i> <?= lang("add_loyalty"); ?></a></li>                        
                    </ul>
                </li>
            </ul>
        </div>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?= lang('list_results'); ?></p>

                <div class="table-responsive">
                    <table id="SupData" cellpadding="0" cellspacing="0" border="0"
                           class="table table-bordered table-condensed table-hover table-striped">
                        <thead>
                        <tr class="primary">
                            <th><?=lang('s.no')?></th>
                            <th class="col-lg-2" style="min-width:30px; width: 30px; text-align: center;">
                                <input class="checkbox checkth" type="checkbox" name="check"/>
                            </th>
                            <th><?= lang("loyalty_name"); ?></th>
                            <th><?= lang("card_no"); ?></th>
                            <th><?= lang("status"); ?></th>                            
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td colspan="6" class="dataTables_empty"><?= lang('loading_data_from_server') ?></td>
                        </tr>
                        </tbody>
                        
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

