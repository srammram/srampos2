<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="modal-dialog modal-lg">
    <div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
            <i class="fa fa-2x">&times;</i>
        </button>
        <button type="button" class="btn btn-xs btn-default no-print pull-right" style="margin-right:15px;" onclick="window.print();">
            <i class="fa fa-print"></i> <?= lang('print'); ?>
        </button>
        <h4 class="modal-title" id="myModalLabel"><?= $recipe->name.(SHOP && $recipe->hide != 1 ? ' ('.lang('shop_views').': '.$recipe->views.')' : ''); ?></h4>
    </div>
        <div class="modal-body">

            <div class="row">
                <div class="col-xs-5">
                    <img id="pr-image" src="<?= base_url() ?>assets/uploads/<?= $recipe->image ?>"
                    alt="<?= $recipe->name ?>" class="img-responsive img-thumbnail"/>

                    <div id="multiimages" class="padding10">
                        <?php if (!empty($images)) {
                            echo '<a class="img-thumbnail change_img" href="' . base_url() . 'assets/uploads/' . $recipe->image . '" style="margin-right:5px;"><img class="img-responsive" src="' . base_url() . 'assets/uploads/thumbs/' . $recipe->image . '" alt="' . $recipe->image . '" style="width:' . $Settings->twidth . 'px; height:' . $Settings->theight . 'px;" /></a>';
                            foreach ($images as $ph) {
                                echo '<div class="gallery-image"><a class="img-thumbnail change_img" href="' . base_url() . 'assets/uploads/' . $ph->photo . '" style="margin-right:5px;"><img class="img-responsive" src="' . base_url() . 'assets/uploads/thumbs/' . $ph->photo . '" alt="' . $ph->photo . '" style="width:' . $Settings->twidth . 'px; height:' . $Settings->theight . 'px;" /></a>';
                                if ($Owner || $Admin || $GP['recipe-edit']) {
                                    echo '<a href="#" class="delimg" data-item-id="'.$ph->id.'"><i class="fa fa-times"></i></a>';
                                }
                                echo '</div>';
                            }
                        }
                        ?>
                        <div class="clearfix"></div>
                    </div>
                </div>
                <div class="col-xs-7">
                    <div class="table-responsive">
                        <table class="table table-borderless table-striped dfTable table-right-left">
                            <tbody>
                                <tr>
                                    <td colspan="2" style="background-color:#FFF;"></td>
                                </tr>
                                <tr>
                                    <td style="width:30%;"><?= lang("barcode_qrcode"); ?></td>
                                    <td style="width:70%;">
                                        <img src="<?= admin_url('misc/barcode/'.$recipe->code.'/'.$recipe->barcode_symbology.'/74/0'); ?>" alt="<?= $recipe->code; ?>" class="bcimg" />
                                        <?= $this->sma->qrcode('link', urlencode(admin_url('recipe/view/' . $recipe->id)), 2); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?= lang("type"); ?></td>
                                    <td><?= lang($recipe->type); ?></td>
                                </tr>
                                <tr>
                                    <td><?= lang("name"); ?></td>
                                    <td><?= $recipe->name; ?></td>
                                </tr>
                                <tr>
                                    <td><?= lang("code"); ?></td>
                                    <td><?= $recipe->code; ?></td>
                                </tr>
                                <tr>
                                    <td><?= lang("brand"); ?></td>
                                    <td><?= $brand ? $brand->name : ''; ?></td>
                                </tr>
                                <tr>
                                    <td><?= lang("category"); ?></td>
                                    <td><?= $category->name; ?></td>
                                </tr>
                                <?php if ($recipe->subcategory_id) { ?>
                                    <tr>
                                        <td><?= lang("subcategory"); ?></td>
                                        <td><?= $subcategory->name; ?></td>
                                    </tr>
                                    <?php } ?>
                                    <tr>
                                        <td><?= lang("unit"); ?></td>
                                        <td><?= $unit ? $unit->name.' ('.$unit->code.')' : ''; ?></td>
                                    </tr>
                                    <?php if ($Owner || $Admin) {
                                        echo '<tr><td>' . lang("cost") . '</td><td>' . $this->sma->formatMoney($recipe->cost) . '</td></tr>';
                                        echo '<tr><td>' . lang("price") . '</td><td>' . $this->sma->formatMoney($recipe->price) . '</td></tr>';
                                        if ($recipe->promotion) {
                                            echo '<tr><td>' . lang("promotion") . '</td><td>' . $this->sma->formatMoney($recipe->promo_price) . ' ('.$this->sma->hrsd($recipe->start_date).' - '.$this->sma->hrsd($recipe->end_date).')</td></tr>';
                                        }
                                    } else {
                                        if ($this->session->userdata('show_cost')) {
                                            echo '<tr><td>' . lang("cost") . '</td><td>' . $this->sma->formatMoney($recipe->cost) . '</td></tr>';
                                        }
                                        if ($this->session->userdata('show_price')) {
                                            echo '<tr><td>' . lang("price") . '</td><td>' . $this->sma->formatMoney($recipe->price) . '</td></tr>';
                                            if ($recipe->promotion) {
                                                echo '<tr><td>' . lang("promotion") . '</td><td>' . $this->sma->formatMoney($recipe->promo_price) . ' ('.$this->sma->hrsd($recipe->start_date).' - '.$this->sma->hrsd($recipe->start_date).')</td></tr>';
                                            }
                                        }
                                    }
                                    ?>

                                    <?php if ($recipe->tax_rate) { ?>
                                    <tr>
                                        <td><?= lang("tax_rate"); ?></td>
                                        <td><?= $tax_rate->name; ?></td>
                                    </tr>
                                    <tr>
                                        <td><?= lang("tax_method"); ?></td>
                                        <td><?= $recipe->tax_method == 0 ? lang('inclusive') : lang('exclusive'); ?></td>
                                    </tr>
                                    <?php } ?>
                                    <?php if ($recipe->alert_quantity != 0) { ?>
                                    <tr>
                                        <td><?= lang("alert_quantity"); ?></td>
                                        <td><?= $this->sma->formatQuantity($recipe->alert_quantity); ?></td>
                                    </tr>
                                    <?php } ?>
                                    <?php if ($variants) { ?>
                                    <tr>
                                        <td><?= lang("recipe_variants"); ?></td>
                                        <td><?php foreach ($variants as $variant) {
                                            echo '<span class="label label-primary">' . $variant->name . '</span> ';
                                        } ?></td>
                                    </tr>
                                    <?php } ?>

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-xs-12">
                        <div class="row">
                            <div class="col-xs-5">
                                <?php if ($recipe->cf1 || $recipe->cf2 || $recipe->cf3 || $recipe->cf4 || $recipe->cf5 || $recipe->cf6) { ?>
                                <h3 class="bold"><?= lang('custom_fields') ?></h3>
                                <div class="table-responsive">
                                    <table
                                    class="table table-bordered table-striped table-condensed dfTable two-columns">
                                    <thead>
                                        <tr>
                                            <th><?= lang('custom_field') ?></th>
                                            <th><?= lang('value') ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if ($recipe->cf1) {
                                            echo '<tr><td>' . lang("pcf1") . '</td><td>' . $recipe->cf1 . '</td></tr>';
                                        }
                                        if ($recipe->cf2) {
                                            echo '<tr><td>' . lang("pcf2") . '</td><td>' . $recipe->cf2 . '</td></tr>';
                                        }
                                        if ($recipe->cf3) {
                                            echo '<tr><td>' . lang("pcf3") . '</td><td>' . $recipe->cf3 . '</td></tr>';
                                        }
                                        if ($recipe->cf4) {
                                            echo '<tr><td>' . lang("pcf4") . '</td><td>' . $recipe->cf4 . '</td></tr>';
                                        }
                                        if ($recipe->cf5) {
                                            echo '<tr><td>' . lang("pcf5") . '</td><td>' . $recipe->cf5 . '</td></tr>';
                                        }
                                        if ($recipe->cf6) {
                                            echo '<tr><td>' . lang("pcf6") . '</td><td>' . $recipe->cf6 . '</td></tr>';
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php } ?>

                            <?php if ((!$Supplier || !$Customer) && !empty($warehouses) && $recipe->type == 'standard') { ?>
                            <h3 class="bold"><?= lang('warehouse_quantity') ?></h3>
                            <div class="table-responsive">
                                <table
                                class="table table-bordered table-striped table-condensed dfTable two-columns">
                                <thead>
                                    <tr>
                                        <th><?= lang('warehouse_name') ?></th>
                                        <th><?= lang('quantity') . ' (' . lang('rack') . ')'; ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($warehouses as $warehouse) {
                                        if ($warehouse->quantity != 0) {
                                            echo '<tr><td>' . $warehouse->name . ' (' . $warehouse->code . ')</td><td><strong>' . $this->sma->formatQuantity($warehouse->quantity) . '</strong>' . ($warehouse->rack ? ' (' . $warehouse->rack . ')' : '') . '</td></tr>';
                                        }
                                    } ?>
                                </tbody>
                            </table>
                        </div>
                        <?php } ?>
                    </div>
                    <div class="col-xs-7">
                        <?php if ($recipe->type == 'combo') { ?>
                        <h3 class="bold"><?= lang('combo_items') ?></h3>
                        <div class="table-responsive">
                            <table
                            class="table table-bordered table-striped table-condensed dfTable two-columns">
                            <thead>
                                <tr>
                                    <th><?= lang('recipe_name') ?></th>
                                    <th><?= lang('quantity') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($combo_items as $combo_item) {
                                    echo '<tr><td>' . $combo_item->name . ' (' . $combo_item->code . ') </td><td>' . $this->sma->formatQuantity($combo_item->qty) . '</td></tr>';
                                } ?>
                            </tbody>
                        </table>
                    </div>
                    <?php } ?>
                    <?php if (!empty($options)) { ?>
                    <h3 class="bold"><?= lang('recipe_variants_quantity'); ?></h3>
                    <div class="table-responsive">
                        <table
                        class="table table-bordered table-striped table-condensed dfTable">
                        <thead>
                            <tr>
                                <th><?= lang('warehouse_name') ?></th>
                                <th><?= lang('recipe_variant'); ?></th>
                                <th><?= lang('quantity') . ' (' . lang('rack') . ')'; ?></th>
                                <?php if ($Owner || $Admin) {
                                    echo '<th>' . lang('price_addition') . '</th>';
                                } ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($options as $option) {
                                if ($option->wh_qty != 0) {
                                    echo '<tr><td>' . $option->wh_name . '</td><td>' . $option->name . '</td><td class="text-center">' . $this->sma->formatQuantity($option->wh_qty) . '</td>';
                                    if ($Owner || $Admin && (!$Customer || $this->session->userdata('show_cost'))) {
                                        echo '<td class="text-right">' . $this->sma->formatMoney($option->price) . '</td>';
                                    }
                                    echo '</tr>';
                                }

                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <div class="col-xs-12">

        <?= $recipe->details ? '<div class="panel panel-success"><div class="panel-heading">' . lang('recipe_details_for_invoice') . '</div><div class="panel-body">' . $recipe->details . '</div></div>' : ''; ?>
        <?= $recipe->recipe_details ? '<div class="panel panel-primary"><div class="panel-heading">' . lang('recipe_details') . '</div><div class="panel-body">' . $recipe->recipe_details . '</div></div>' : ''; ?>

    </div>
</div>
<?php if (!$Supplier || !$Customer) { ?>
    <div class="buttons">
        <div class="btn-group btn-group-justified">
            <div class="btn-group">
                <a href="<?= admin_url('recipe/print_barcodes/' . $recipe->id) ?>" class="tip btn btn-primary" title="<?= lang('print_barcode_label') ?>">
                    <i class="fa fa-print"></i>
                    <span class="hidden-sm hidden-xs"><?= lang('print_barcode_label') ?></span>
                </a>
            </div>
            <div class="btn-group">
                <a href="<?= admin_url('recipe/pdf/' . $recipe->id) ?>" class="tip btn btn-primary" title="<?= lang('pdf') ?>">
                    <i class="fa fa-download"></i>
                    <span class="hidden-sm hidden-xs"><?= lang('pdf') ?></span>
                </a>
            </div>
            <div class="btn-group">
                <a href="<?= admin_url('recipe/edit/' . $recipe->id) ?>" class="tip btn btn-warning tip" title="<?= lang('edit_recipe') ?>">
                    <i class="fa fa-edit"></i>
                    <span class="hidden-sm hidden-xs"><?= lang('edit') ?></span>
                </a>
            </div>
            <div class="btn-group">
                <a href="#" class="tip btn btn-danger bpo" title="<b><?= lang("delete_recipe") ?></b>"
                    data-content="<div style='width:150px;'><p><?= lang('r_u_sure') ?></p><a class='btn btn-danger' href='<?= admin_url('recipe/delete/' . $recipe->id) ?>'><?= lang('i_m_sure') ?></a> <button class='btn bpo-close'><?= lang('no') ?></button></div>"
                    data-html="true" data-placement="top">
                    <i class="fa fa-trash-o"></i>
                    <span class="hidden-sm hidden-xs"><?= lang('delete') ?></span>
                </a>
            </div>
        </div>
    </div>
    <script type="text/javascript">
    $(document).ready(function () {
        $('.tip').tooltip();
    });
    </script>
<?php } ?>
</div>
</div>
</div>
<script type="text/javascript">
$(document).ready(function() {
    $('.change_img').click(function(event) {
        event.preventDefault();
        var img_src = $(this).attr('href');
        $('#pr-image').attr('src', img_src);
        return false;
    });
});
</script>
