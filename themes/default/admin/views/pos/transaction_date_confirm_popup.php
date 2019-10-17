<div class="modal" id="transaction-date-popup" tabindex="-1" role="dialog" aria-labelledby="transaction-date-popupLabel" aria-hidden="true" style="background: rgba(0,0,0,0.5);">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <!--<button type="button" class="close closebil" data-dismiss="modal" aria-hidden="true"><i
                        class="fa fa-2x">&times;</i></button>-->
                <h4 class="modal-title" id="transaction-date-popupLabel">Set Transaction date </h4>
            </div>
            <div class="modal-body">              
                <div class="form-group date-alert-text"></div>
                <div class="modal-footer">
                    <div style="float: left;" class="user-num-container form-group"><form><input type="password" name="u_num" class='user-num kb-pad-usernum' placeholder='Enter Your Password' required='required' maxLength='4' autocomplete="off"></form></div>
                    <button type="button" id="set-today" data-item="today" class="set-transaction-date btn btn-primary">No, Today</button>
                    <button type="button" id="set-lasttransaction-day" data-item="lastday" class="set-transaction-date btn btn-primary">Yes, Continue</button>
                </div>
            </div>
        </div>
    </div>
</div>

