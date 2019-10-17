<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<link rel="stylesheet" href="<?= $assets ?>styles/jquery-ui.css">
  <script src="<?= $assets ?>js/jquery-ui.js"></script>

<script type="text/javascript">
    $(document).ready(function () {
        $('#form').hide();        
        $('.toggle_down').click(function () {
            $("#form").slideDown();
            return false;
        });
        $('.toggle_up').click(function () {
            $("#form").slideUp();
            return false;
        });
    });
</script>
<?php
$v = "";
if ($this->input->post('warehouse_id')) {
    $v .= "&warehouse_id=" . $this->input->post('warehouse_id');
}
if ($this->input->post('type')) {
    $v .= "&type=" . $this->input->post('type');
}

?>
<script>
    $(document).ready(function () {        
        oTable = $('#PrRData').dataTable({
            "aaSorting": [[1, "asc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= admin_url('reports/item_stock_details/?v=1'.$v) ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            "fnRowCallback" : function(nRow, aData, iDisplayIndex){
                var oSettings = oTable.fnSettings();
                $index = oSettings._iDisplayStart+parseInt(iDisplayIndex) +parseInt(1) ;
                $("td:first", nRow).html($index);
                return nRow;
            },
            "aoColumns": [null,null, null,null,null,null,null,null,null,null,null,null],
            "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
               
            }
        }).fnSetFilteringDelay().dtFilter([
          //  {column_number: 1, filter_default_label: "[<?=lang('brand');?>]", filter_type: "text", data: []},
        ], "footer");
    });
</script>



<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-heart"></i><?= lang('stock_report'); ?> <?php
            if ($this->input->post('start_date')) {
                echo "From " . $this->input->post('start_date') . " to " . $this->input->post('end_date');
            }
            ?>
        </h2>
          <div class="box-icon">
            <ul class="btn-tasks">
                <li class="dropdown">
                    <a href="#" class="toggle_up tip" title="<?= lang('hide_form') ?>">
                        <i class="icon fa fa-toggle-up"></i>
                    </a>
                </li>
                <li class="dropdown">
                    <a href="#" class="toggle_down tip" title="<?= lang('show_form') ?>">
                        <i class="icon fa fa-toggle-down"></i>
                    </a>
                </li>
            </ul>
        </div>
        <div class="box-icon">
            <ul class="btn-tasks">
                <li class="dropdown">
                    <a href="javascript:void(0);" id="excel_report" class="excel_report" title="<?= lang('download_xls') ?>">
                        <i class="icon fa fa-file-excel-o"></i>
                    </a>
                </li>
               
            </ul>
        </div>
    </div>
    <div class="box-content">

        <div class="row">
            <div class="col-lg-12">

                 <div id="form">
                    <?php echo admin_form_open("reports/item_stock"); ?>

                <!-- <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'id' => 'item_stock');
             echo admin_form_open("reports/item_stock", $attrib);?> -->

                    <div class="row">   

                      <div class="col-md-4">
                            <div class="form-group">
                                <?= lang("warehouse", "warehouse"); ?>
                                <?php
                                $wh['0'] = lang('all');
                                foreach ($warehouses as $warehouse) {
                                    $wh[$warehouse->id] = $warehouse->name;
                                }
                                echo form_dropdown('warehouse_id', $wh, '', 'class="form-control" data-placeholder="' . lang("select") . ' ' . lang("warehouse") . '" id="warehouse_id" style="width:100%;" ');
                                ?>
                            </div>
                        </div>  

                       <div class="col-md-4">
                            <div class="form-group">
                                <div class="form-group">
                                    <?= lang("item_type", "type") ?>
                                    <?php                                    
                                    $opts = array('standard' => lang('standard'), 'production' => lang('production'),'quick_service' => lang('quick_service'),'combo' => lang('combo'), 'addon' => lang('addon'),'semi_finished' => lang('semi_finished'),'raw' => lang('raw'),'service' => lang('service'));
                                    echo form_dropdown('type', $opts, '', 'class="form-control" id="type" required="required"');
                                    ?>
                                </div>
                            </div>
                        </div>                                                 
                    </div>
                    <div class="form-group">
                        <div
                            class="controls"> <?php echo form_submit('submit_report', $this->lang->line("submit"), 'class="btn btn-primary stock_reports"'); ?> </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="PrRData"
                           class="table table-bordered table-hover table-striped table-condensed reports-table">
                        <thead>
                        <tr>
                            <th><?= lang("s.no") ?></th>
                            <th><?= lang("product") ?></th>
                            <th><?= lang("type") ?></th>
                            <th><?= lang("category") ?></th>
                            <th><?= lang("subcategory") ?></th>
                            <th><?= lang("brand") ?></th>
                            <th><?= lang("batch") ?></th>
                            <th><?= lang("stock_in") ?></th>
                            <th><?= lang("stock_out") ?></th>
                            <th><?= lang("current_stock") ?></th>                            
                            <th><?= lang("cost_price") ?></th>
                            <th><?= lang("selling_price") ?></th>
                            <th><?= lang("expiry") ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td colspan="7" class="dataTables_empty"><?= lang('loading_data_from_server') ?></td>
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
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            <div class="form-group">
                <div
                    class="controls"> <?php echo form_submit('submit_report', $this->lang->line("submit"), 'class="btn btn-primary"'); ?> 
                </div>
            </div>
              <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="<?= $assets ?>js/html2canvas.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
     $(".excel_report").click(function(){

      $("#SlRData").table2excel({       

        exclude: ".noExl",

        name: "Worksheet Name",

        filename: "Customer Loyalty Point Report " //do not include extension

      });

    });
    });

    /*$(document).on('click', '.stock_reports', function (e) {        
         e.preventDefault();
        $("#testForm").submit();
    });*/
</script>