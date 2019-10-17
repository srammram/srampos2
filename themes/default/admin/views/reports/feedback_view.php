<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="modal-dialog modal-lg no-modal-header">
    <div class="modal-content">
        <div class="modal-body">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                <i class="fa fa-2x">&times;</i>
            </button>
            <div style="align-self: center;text-align: center;"> <h1 style="margin-top:10px;"> <?= lang("customer_feedback_details"); ?></h1></div>

           <!--  <button type="button" class="btn btn-xs btn-default no-print pull-right" style="margin-right:15px;" onclick="window.print();">
                <i class="fa fa-print"></i> <?= lang('print'); ?>
            </button>  -->   

             <?php if ($logo) { ?>
                <div class="text-center" style="margin-bottom:20px;">
                    <img src="<?= base_url() . 'assets/uploads/logos/' . $Settings->logo; ?>"
                         alt="<?= $Settings->site_name; ?>">
                </div>
            <?php } ?>

            <div class="clearfix"></div>
            <div class="clearfix"></div>

            <!-- <div> <h2 style="margin-top:10px;"> <?= lang("customer_details"); ?></h2></div> -->
            <div class="clearfix"></div>

            <div class="row" style="margin-bottom:15px;">
                <div class="col-xs-6">

                    <h2 style="margin-top:10px;"><?= $supplier->company ? $supplier->company : $supplier->name; ?></h2>

                     <?= lang("date"); ?>: <?= $supplier->bill_date; ?><br>
                     <?= lang("bill_no"); ?>: <?= $supplier->bill_number; ?><br>
                     <?= lang("sales_associate"); ?>: <?= $supplier->sales_associate; ?><br>
                     <?= lang("cashier"); ?>: <?= $supplier->cashier; ?><br>

                     <?= $supplier->name ? "" : "Customer Name: " . $supplier->name. "<br />"  ?>

                    <?php
                     echo $supplier->address . "<br />" . $supplier->city . " " . $supplier->postal_code . " " . $supplier->state . "<br />" . $supplier->country;

                    if($supplier->phone){
                        echo lang("tel") . ": " . $supplier->phone . "<br />" ;
                    }
                    if($supplier->email){
                       echo  lang("email") . ": " . $supplier->email;
                    }
                    ?>
                </div>               
            </div>
            <div> <h2 style="margin-top:10px;"> <?= lang("item_feedback"); ?></h2></div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped print-table order-table">

                    <thead>
                    <tr>
                        <th><?= lang("s_no"); ?></th>
                        <th><?= lang("item_name"); ?></th>                       
                        <th><?= lang("remark"); ?></th>
                        <th><?= lang("comments"); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                  <?php $r = 1;                    
                    foreach ($item_feedback as $row):                        
                    ?>
                        <tr>
                            <td style="text-align:center; width:40px; vertical-align:middle;"><?= $r; ?></td>                           
                            <td style="text-align:left; width:100px;"><?= $row->item_name; ?></td>                           
                            <td style="text-align:right; width:120px;"><?= $row->status; ?></td>
                            <td style="text-align:right; width:120px;"><?= $row->message; ?></td>
                        </tr>
                        <?php                       
                        $r++;
                        endforeach;
                     ?>
                    </tbody>
                  
                </table>
            </div>
        <div> <h2 style="margin-top:10px;"> <?= lang("company_feedback"); ?></h2></div>
             <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped print-table order-table">

                    <thead>
                    <tr>
                        <th><?= lang("s_no"); ?></th>
                        <th><?= lang("question"); ?></th>                       
                        <th><?= lang("answer"); ?></th>
                        <!-- <th><?= lang("commnts"); ?></th> -->
                    </tr>
                    </thead>
                    <tbody>
                  <?php $k = 1;                    
                    foreach ($company_feedback as $com):                        
                    ?>
                        <tr>
                            <td style="text-align:center; width:40px; vertical-align:middle;"><?= $k; ?></td>                           
                            <td style="text-align:left; width:100px;"><?= $com->question_id; ?></td>                           
                            <td style="text-align:right; width:120px;"><?= $com->answer; ?></td>
                        </tr>
                        <?php                       
                        $k++;
                        endforeach;
                     ?>
                    </tbody>
                  
                </table>
            </div>
           <!--  <?php if($overallcomment){ ?>
               <div> <h2><?php  echo  lang("overallcomment") . ": " . $overallcomment; ?></h2></div>
            <?php } ?> -->
        </div>
    </div>
</div>
