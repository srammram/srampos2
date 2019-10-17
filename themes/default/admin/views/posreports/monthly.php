<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<style type="text/css">
    .dfTable th, .dfTable td {
        text-align: center;
        vertical-align: middle;
    }

    .dfTable td {
        padding: 2px;
    }

    .data tr:nth-child(odd) td {
        color: #2FA4E7;
    }

    .data tr:nth-child(even) td {
        text-align: right;
    }
</style>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-calendar"></i><?= lang('monthly_sales').' ('.(isset($sel_warehouse) ? $sel_warehouse->name : lang('all_warehouses')).')'; ?></h2>

        <div class="box-icon">
            <ul class="btn-tasks">
                <?php if (!empty($warehouses) && !$this->session->userdata('warehouse_id')) { ?>
                    <li class="dropdown">
                        <a data-toggle="dropdown" class="dropdown-toggle" href="#"><i class="icon fa fa-building-o tip" data-placement="left" title="<?=lang("warehouses")?>"></i></a>
                        <ul class="dropdown-menu pull-right tasks-menus" role="menu" aria-labelledby="dLabel">
                            <li><a href="<?=site_url('api/v1/posreports/monthly_sales/0/'.$year).'?api-key='.$_GET['api-key']?>"><i class="fa fa-building-o"></i> <?=lang('all_warehouses')?></a></li>
                            <li class="divider"></li>
                            <?php
                                foreach ($warehouses as $warehouse) {
                                        echo '<li><a href="' . site_url('api/v1/posreports/monthly_sales/'.$warehouse->id.'/'.$year) .'?api-key='.$_GET['api-key'].'"><i class="fa fa-building"></i>' . $warehouse->name . '</a></li>';
                                    }
                                ?>
                        </ul>
                    </li>
                <?php } ?>
                <!-- <li class="dropdown">
                    <a href="#" id="image" class="tip" title="<?= lang('save_image') ?>">
                        <i class="icon fa fa-file-picture-o"></i>
                    </a>
                </li> -->
            </ul>
        </div>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <p class="introtext"><?= lang("reports_calendar_text") ?></p>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped dfTable reports-table">
                        <thead>
                        <tr class="year_roller">
                            <th><a class="white" href="<?= site_url('api/v1/posreports/monthly_sales/'.(isset($warehouse_id) ? $warehouse_id : 0).'/'.($year-1)).'?api-key='.$_GET['api-key']; ?>">&lt;&lt;</a></th>
                            <th colspan="10"> <?php echo $year; ?></th>
                            <th><a class="white" href="<?= site_url('api/v1/posreports/monthly_sales/'.(isset($warehouse_id) ? $warehouse_id : 0).'/'.($year+1)).'?api-key='.$_GET['api-key']; ?>">&gt;&gt;</a></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td class="bold text-center">
                                <a href="<?= site_url('api/v1/posreports/monthly_profit/'.$year.'/01').'?api-key='.$_GET['api-key']; ?>" data-toggle="modal" data-target="#myModal">
                                    <?= lang("cal_january"); ?>
                                </a>
                            </td>
                            <td class="bold text-center">
                                <a href="<?= site_url('api/v1/posreports/monthly_profit/'.$year.'/02').'?api-key='.$_GET['api-key']; ?>" data-toggle="modal" data-target="#myModal">
                                    <?= lang("cal_february"); ?>
                                </a>
                            </td>
                            <td class="bold text-center">
                                <a href="<?= site_url('api/v1/posreports/monthly_profit/'.$year.'/03').'?api-key='.$_GET['api-key']; ?>" data-toggle="modal" data-target="#myModal">
                                    <?= lang("cal_march"); ?>
                                </a>
                            </td>
                            <td class="bold text-center">
                                <a href="<?= site_url('api/v1/posreports/monthly_profit/'.$year.'/04').'?api-key='.$_GET['api-key']; ?>" data-toggle="modal" data-target="#myModal">
                                    <?= lang("cal_april"); ?>
                                </a>
                            </td>
                            <td class="bold text-center">
                                <a href="<?= site_url('api/v1/posreports/monthly_profit/'.$year.'/05').'?api-key='.$_GET['api-key']; ?>" data-toggle="modal" data-target="#myModal">
                                    <?= lang("cal_may"); ?>
                                </a>
                            </td>
                            <td class="bold text-center">
                                <a href="<?= site_url('api/v1/posreports/monthly_profit/'.$year.'/06').'?api-key='.$_GET['api-key']; ?>" data-toggle="modal" data-target="#myModal">
                                    <?= lang("cal_june"); ?>
                                </a>
                            </td>
                            <td class="bold text-center">
                                <a href="<?= site_url('api/v1/posreports/monthly_profit/'.$year.'/07').'?api-key='.$_GET['api-key']; ?>" data-toggle="modal" data-target="#myModal">
                                    <?= lang("cal_july"); ?>
                                </a>
                            </td>
                            <td class="bold text-center">
                                <a href="<?= site_url('api/v1/posreports/monthly_profit/'.$year.'/08').'?api-key='.$_GET['api-key']; ?>" data-toggle="modal" data-target="#myModal">
                                    <?= lang("cal_august"); ?>
                                </a>
                            </td>
                            <td class="bold text-center">
                                <a href="<?= site_url('api/v1/posreports/monthly_profit/'.$year.'/09').'?api-key='.$_GET['api-key']; ?>" data-toggle="modal" data-target="#myModal">
                                    <?= lang("cal_september"); ?>
                                </a>
                            </td>
                            <td class="bold text-center">
                                <a href="<?= site_url('api/v1/posreports/monthly_profit/'.$year.'/10').'?api-key='.$_GET['api-key']; ?>" data-toggle="modal" data-target="#myModal">
                                    <?= lang("cal_october"); ?>
                                </a>
                            </td>
                            <td class="bold text-center">
                                <a href="<?= site_url('api/v1/posreports/monthly_profit/'.$year.'/11').'?api-key='.$_GET['api-key']; ?>" data-toggle="modal" data-target="#myModal">
                                    <?= lang("cal_november"); ?>
                                </a>
                            </td>
                            <td class="bold text-center">
                                <a href="<?= site_url('api/v1/posreports/monthly_profit/'.$year.'/12').'?api-key='.$_GET['api-key']; ?>" data-toggle="modal" data-target="#myModal">
                                    <?= lang("cal_december"); ?>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <?php
                            if (!empty($sales)) {
                                foreach ($sales as $value) {
                                    $array[$value->date] = "<table class='table table-bordered table-hover table-striped table-condensed data' style='margin:0;'><tbody><tr><td>" . $this->lang->line("total") . "</td></tr><tr><td>" . $this->sma->formatMoney($value->total) . "</td></tr><tr><td>" . $this->lang->line("discount") . "</td></tr><tr><td>" . $this->sma->formatMoney($value->discount) . "</td></tr><tr><td>" . $this->lang->line("tax") . "</td></tr><tr><td>" . $this->sma->formatMoney($value->tax) . "</td></tr><tr><td>" . $this->lang->line("grand_total") . "</td></tr><tr><td>" . $this->sma->formatMoney($value->grand_total) . "</td></tr></tbody></table>";
                                }
                                for ($i = 1; $i <= 12; $i++) {
                                    echo '<td width="8.3%">';
                                    if (isset($array[$i])) {
                                        echo $array[$i];
                                    } else {
                                        echo '<strong>0</strong>';
                                    }
                                    echo '</td>';
                                }
                            } else {
                                for ($i = 1; $i <= 12; $i++) {
                                    echo '<td width="8.3%"><strong>0</strong></td>';
                                }
                            }
                            ?>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="<?= $assets ?>js/html2canvas.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('#pdf').click(function (event) {
            event.preventDefault();
            window.location.href = "<?=site_url('api/v1/posreports/monthly_sales/'.($warehouse_id ? $warehouse_id : 0).'/'.$year.'/pdf')?>";
            return false;
        });
        $('#image').click(function (event) {
            event.preventDefault();
            html2canvas($('.box'), {
                onrendered: function (canvas) {
                    openImg(canvas.toDataURL());
                }
            });
            return false;
        });
    });
</script>
