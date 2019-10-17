<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<script>
    $(document).ready(function () {
        oTable = $('#GPData').dataTable({
            "aaSorting": [[1, "asc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?=lang('all')?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            "oTableTools": {
                "sSwfPath": "assets/media/swf/copy_csv_xls_pdf.swf",
                "aButtons": ["csv", {"sExtends": "pdf", "sPdfOrientation": "landscape", "sPdfMessage": ""}, "print"]
            },
            "aoColumns": [null,{"bSortable": false}, null, null, null, {"bSortable": false}
            ]

        });
    });
</script>

<div class="box">
    <div class="box-header">
        <h2 class="blue"><?= lang('category_subcategory_sale_items_mapping'); ?></h2>
        <?php $sale_type =$this->uri->segment(4); 
        if($sale_type){

        ?>   

        <div class="box-icon">
            <ul class="btn-tasks">
                <li class="dropdown">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#"><i class="icon fa fa-tasks tip" data-placement="left" title="<?= lang("actions") ?>"></i></a>
                    <ul class="dropdown-menu pull-right tasks-menus" role="menu" aria-labelledby="dLabel">
                        <li><a href="<?= admin_url('system_settings/create_sale_item_mapping/'.$sale_type.''); ?>" data-toggle="modal" data-target="#myModal"><i class="fa fa-plus"></i> <?= lang('create_sale_item_mapping') ?></a></li>
                    </ul>
                </li>
            </ul>
        </div>
    <?php } ?>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <p class="introtext"><?php echo $this->lang->line("list_results"); ?></p>
                <div class="table-responsive">
                    <table id="GPData" class="table table-bordered table-hover table-striped reports-table">
                        <thead>
                        <tr>
                            <th><?=lang('s.no')?></th>
                            <th style="min-width:30px; width: 30px; text-align: center;">
                                <input class="checkbox checkth" type="checkbox" name="check"/>
                            </th>
                            <th><?php echo $this->lang->line("group_id"); ?></th>
                            <th><?php echo $this->lang->line("group_name"); ?></th>
                            <th><?php echo $this->lang->line("Sale_type"); ?></th>
                            <th style="width:45px;"><?php echo $this->lang->line("actions"); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach ($saletypes as $k => $group) {
                            ?>
                            <tr>
                                <td><?=$k+1?></td>
                                <td>
                                 <div class="text-center"><input class="checkbox multi-select" type="checkbox" name="val[]"
                                                   value="<?= $group->id ?>"/></div>
                                </td>
                                <td><?php echo $group->id; ?></td>
                                <td><?php echo $group->days; ?></td>
                                <td><?php echo $group->sale_type; ?></td>
                                <td style="text-align:center;">
                                    <?php 
                                        $check = $this->site->checksaleitemsmapped_byid($group->id);
                                       if($check){
                                            echo '<a class="tip" title="' . $this->lang->line("edit_sale_item_mapping_by_saletype_and_day") . '" href="' . admin_url('system_settings/edit_sale_item_mapping_by_saletype_and_day/' . $group->id) . '"><i class="fa fa-tasks"></i></a>'; 
                                        }else{
                                        echo '<a class="tip" title="' . $this->lang->line("sale_item_mapping_by_saletype_and_day") . '" href="' . admin_url('system_settings/sale_item_mapping_by_saletype_and_day/' . $group->id) . '"><i class="fa fa-tasks"></i></a>'; 
                                        }    
                                        ?>

                                     <?php echo '&nbsp; <a href="#" class="tip po" title="' . $this->lang->line("delete_sale_mapping") . '" data-content="<p>' . lang('r_u_sure') . '</p><a class=\'btn btn-danger\' href=\'' . admin_url('system_settings/delete_sale_mapping/' . $group->id) . '\'>' . lang('i_m_sure') . '</a> <button class=\'btn po-close\'>' . lang('no') . '</button>"><i class="fa fa-trash-o"></i></a>'; 
                                     ?>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
