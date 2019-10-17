<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('add_payment'); ?></h4>
        </div>
        <?php $bill = $bills['bill']; ?>
        <?php if($bill_id) : ?>
        <form action="<?=admin_url('reports/postpaid_payment/'.$bill->customer_id.'/'.$bill_id)?>" data-toggle="validator" role="form" enctype="multipart/form-data" method="post" accept-charset="utf-8">
        <?php else :?>
        <form action="<?=admin_url('reports/postpaid_payment/'.$bill->customer_id)?>" data-toggle="validator" role="form" enctype="multipart/form-data" method="post" accept-charset="utf-8">
        <?php endif; ?>
        
           
            <div class="modal-body">
                <p> Please Fill In The Information Below. The Field Labels Marked With * Are Required Input Fields.</p>

                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label For="date"> Date</label>
                            <input type="text" name="date" value=""  class="form-control datetime" id="date" required="required" />
                        </div>
                    </div>  
                    <input type="hidden" value="<?=$bill->customer_id?>" name="customer_id"/>
                </div>
                <div class="clearfix"></div>
                <div id="postpaid-payments">
                <div class="well well-sm well_1">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="payment">
                                    <div class="form-group">
                                        <label For="amount_1"> Amount</label>
                                        <input name="amount-paid" type="text" id="amount_1" value="<?=$bill->amount?>" class="form-control kb-pad amount" required="required"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label For="paid_by_1"> Paying By</label>
                                    <select name="paid_by" id="paid_by_1" class="form-control paid_by" required="required">
                                        
                                        <option value="cash"> Cash</option>
                                        <option value="gift_card"> Gift Card</option>
                                        <option value="CC"> Credit Card</option>
                                        <option value="Cheque"> Cheque</option>
                                        <option value="other"> Other</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="pcc_1" style="display:none;">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input name="pcc_no" type="text" id="pcc_no_1" class="form-control"
                                               placeholder=" Credit Card No"/>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">

                                        <input name="pcc_holder" type="text" id="pcc_holder_1" class="form-control"
                                               placeholder=" Holder Name"/>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <select name="pcc_type" id="pcc_type_1" class="form-control pcc_type"
                                                placeholder=" Card Type">
                                            <option value="Visa"> Visa</option>
                                            <option value="MasterCard"> MasterCard</option>
                                            <option value="Amex"> Amex</option>
                                            <option value="Discover"> Discover</option>
                                        </select>
                                        <!-- <input type="text" id="pcc_type_1" class="form-control" placeholder=" Card Type" />-->
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <input name="pcc_month" type="text" id="pcc_month_1" class="form-control"
                                               placeholder=" Month"/>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">

                                        <input name="pcc_year" type="text" id="pcc_year_1" class="form-control"
                                               placeholder=" Year"/>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">

                                        <input name="pcc_ccv" type="text" id="pcc_cvv2_1" class="form-control"
                                               placeholder=" CVV2"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="pcheque_1" style="display:none;">
                            <div class="form-group"><label For="cheque_no_1"> Cheque No</label>                                <input name="cheque_no" type="text" id="cheque_no_1" class="form-control cheque_no"/>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>

            </div>

           

            <div class="form-group">
                <label For="note"> Note</label>                <textarea name="note" cols="40" rows="10"  class="form-control" id="note"></textarea>
            </div>

        </div>
        <div class="modal-footer">
            <input type="submit" name="add_payment" value="Add Payment"  class="btn btn-primary" />
        </div>
    </div>
    </form>
</div>


<script type="text/javascript" charset="UTF-8">
    $(document).ready(function () {
        
        $(document).on('change', '.paid_by', function () {
            var p_val = $(this).val();
            localStorage.setItem('paid_by', p_val);
            $('#rpaidby').val(p_val);
            if (p_val == 'cash') {
                $('.pcheque_1').hide();
                $('.pcc_1').hide();
                $('.pcash_1').show();
                $('#amount_1').focus();
            } else if (p_val == 'CC') {
                $('.pcheque_1').hide();
                $('.pcash_1').hide();
                $('.pcc_1').show();
                $('#pcc_no_1').focus();
            } else if (p_val == 'Cheque') {
                $('.pcc_1').hide();
                $('.pcash_1').hide();
                $('.pcheque_1').show();
                $('#cheque_no_1').focus();
            } else {
                $('.pcheque_1').hide();
                $('.pcc_1').hide();
                $('.pcash_1').hide();
            }
        });
        $('#pcc_no_1').change(function (e) {
            var pcc_no = $(this).val();
            localStorage.setItem('pcc_no_1', pcc_no);
            var CardType = null;
            var ccn1 = pcc_no.charAt(0);
            if (ccn1 == 4)
                CardType = 'Visa';
            else if (ccn1 == 5)
                CardType = 'MasterCard';
            else if (ccn1 == 3)
                CardType = 'Amex';
            else if (ccn1 == 6)
                CardType = 'Discover';
            else
                CardType = 'Visa';
        
            $('#pcc_type_1').select2("val", CardType);
        });
        $("#date").datetimepicker({
            format: site.dateFormats.js_ldate,
            fontAwesome: true,
            language: 'sma',
            weekStart: 1,
            todayBtn: 1,
            autoclose: 1,
            todayHighlight: 1,
            startView: 2,
            forceParse: 0
        }).datetimepicker('update', new Date());
    });
</script>




