$(document).ready(function () {
$('body a, body button').attr('tabindex', -1);
check_add_item_val();
if (site.settings.set_focus != 1) {
    $('#add_item').focus();
}
// Order level shipping and discoutn localStorage
if (store_rec_discount = localStorage.getItem('store_rec_discount')) {
    $('#store_rec_discount').val(store_rec_discount);
}
$('#ptax2').change(function (e) {
    localStorage.setItem('p_tax2', $(this).val());
});
if (ptax1= localStorage.getItem('p_tax1')) {
    $('#ptax1').select2("val", ptax1);
}
$('#postatus').change(function (e) {
    localStorage.setItem('store_rec_status', $(this).val());
});
if (postatus = localStorage.getItem('store_rec_status')) {
    $('#postatus').select2("val", postatus);
}
var old_shipping;
$('#poshipping').focus(function () {
    old_shipping = $(this).val();
}).change(function () {
    var posh = $(this).val() ? $(this).val() : 0;
    if (!is_numeric(posh)) {
        $(this).val(old_shipping);
        bootbox.alert(lang.unexpected_value);
        return;
    }
    shipping = parseFloat(posh);
    localStorage.setItem('store_rec_shipping', shipping);
    var gtotal = ((total + invoice_tax) - order_discount) + shipping;
    $('#gtotal').text(formatMoney(gtotal));
    $('#tship').text(formatMoney(shipping));
});
if (poshipping = localStorage.getItem('store_rec_shipping')) {
    shipping = parseFloat(poshipping);
    $('#poshipping').val(shipping);
}

$('#popayment_term').change(function (e) {
    localStorage.setItem('store_rec_payment_term', $(this).val());
});
if (popayment_term = localStorage.getItem('store_rec_payment_term')) {
    $('#popayment_term').val(popayment_term);
}

// If there is any item in localStorage
if (localStorage.getItem('store_rec_items')) {
    loadItems();
}
    // clear localStorage and reload
    $('#reset').click(function (e) {
        bootbox.confirm(lang.r_u_sure, function (result) {
            if (result) {
                if (localStorage.getItem('store_rec_items')) {
                    localStorage.removeItem('store_rec_items');
                }
                if (localStorage.getItem('store_rec_discount')) {
                    localStorage.removeItem('store_rec_discount');
                }
                if (localStorage.getItem('store_rec_tax2')) {
                    localStorage.removeItem('store_rec_tax2');
                }
                if (localStorage.getItem('store_rec_shipping')) {
                    localStorage.removeItem('store_rec_shipping');
                }
                if (localStorage.getItem('store_rec_ref')) {
                    localStorage.removeItem('store_rec_ref');
                }
                if (localStorage.getItem('store_rec_warehouse')) {
                    localStorage.removeItem('store_rec_warehouse');
                }
                if (localStorage.getItem('store_rec_note')) {
                    localStorage.removeItem('store_rec_note');
                }
                if (localStorage.getItem('store_rec_supplier')) {
                    localStorage.removeItem('store_rec_supplier');
                }
                if (localStorage.getItem('store_rec_currency')) {
                    localStorage.removeItem('store_rec_currency');
                }
                if (localStorage.getItem('store_rec_extras')) {
                    localStorage.removeItem('store_rec_extras');
                }
                if (localStorage.getItem('store_rec_date')) {
                    localStorage.removeItem('store_rec_date');
                }
                if (localStorage.getItem('store_rec_status')) {
                    localStorage.removeItem('store_rec_status');
                }
				
				if (localStorage.getItem('store_rec_from_store_id')) {
                    localStorage.removeItem('store_rec_from_store_id');
                }
				if (localStorage.getItem('store_rec_to_store_id')) {
                    localStorage.removeItem('store_rec_to_store_id');
                }
				
                if (localStorage.getItem('store_rec_payment_term')) {
                    localStorage.removeItem('store_rec_payment_term');
                }

                $('#modal-loading').show();
                location.reload();
            }
        });
    });

// save and load the fields in and/or from localStorage
var $supplier = $('#store_rec_supplier'), $currency = $('#pocurrency');

$('#poref').change(function (e) {
    localStorage.setItem('store_rec_ref', $(this).val());
});

if (poref = localStorage.getItem('store_rec_ref')) {
    $('#poref').val(poref);
}

$('#invoice_no').change(function (e) {
    localStorage.setItem('store_rec_invoice_no', $(this).val());
});

if (store_rec_invoice_no = localStorage.getItem('store_rec_invoice_no')) {
    $('#invoice_no').val(store_rec_invoice_no);
}


$('#powarehouse').change(function (e) {
    localStorage.setItem('store_rec_warehouse', $(this).val());
});
if (powarehouse = localStorage.getItem('store_rec_warehouse')) {
    $('#powarehouse').select2("val", powarehouse);
}

        $('#ponote').redactor('destroy');
        $('#ponote').redactor({
            buttons: ['formatting', '|', 'alignleft', 'aligncenter', 'alignright', 'justify', '|', 'bold', 'italic', 'underline', '|', 'unorderedlist', 'orderedlist', '|', 'link', '|', 'html'],
            formattingTags: ['p', 'pre', 'h3', 'h4'],
            minHeight: 100,
            changeCallback: function (e) {
                var v = this.get();
                localStorage.setItem('store_rec_note', v);
            }
        });
        if (ponote = localStorage.getItem('store_rec_note')) {
            $('#ponote').redactor('set', ponote);
        }
        $supplier.change(function (e) {
            localStorage.setItem('store_rec_supplier', $(this).val());
            $('#supplier_id').val($(this).val());
        });
        if (store_rec_supplier = localStorage.getItem('store_rec_supplier')) {
            $supplier.val(store_rec_supplier).select2({
                minimumInputLength: 1,
                data: [],
                initSelection: function (element, callback) {
                    $.ajax({
                        type: "get", async: false,
                        url: site.base_url+"suppliers/getSupplier/" + $(element).val(),
                        dataType: "json",
                        success: function (data) {
                            callback(data[0]);
                        }
                    });
                },
                ajax: {
                    url: site.base_url + "suppliers/suggestions",
                    dataType: 'json',
                    quietMillis: 15,
                    data: function (term, page) {
                        return {
                            term: term,
                            limit: 10
                        };
                    },
                    results: function (data, page) {
                        if (data.results != null) {
                            return {results: data.results};
                        } else {
                            return {results: [{id: '', text: 'No Match Found'}]};
                        }
                    }
                }
            });

} else {
    nsSupplier();
}

    /*$('.rexpiry').change(function (e) {
        var item_id = $(this).closest('tr').attr('data-item-id');
        store_rec_items[item_id].row.expiry = $(this).val();
        localStorage.setItem('store_rec_items', JSON.stringify(store_rec_items));
    });*/
if (localStorage.getItem('store_rec_extras')) {
    $('#extras').iCheck('check');
    $('#extras-con').show();
}
$('#extras').on('ifChecked', function () {
    localStorage.setItem('store_rec_extras', 1);
    $('#extras-con').slideDown();
});
$('#extras').on('ifUnchecked', function () {
    localStorage.removeItem("store_rec_extras");
    $('#extras-con').slideUp();
});
/*$(document).on('change', '.rexpiry', function () {
    var item_id = $(this).closest('tr').attr('data-item-id');
    store_rec_items[item_id].row.expiry = $(this).val();
    localStorage.setItem('store_rec_items', JSON.stringify(store_rec_items));
});
$(document).on('change', '.rmfg', function () {
    var item_id = $(this).closest('tr').attr('data-item-id');
    store_rec_items[item_id].row.mfg = $(this).val();
    localStorage.setItem('store_rec_items', JSON.stringify(store_rec_items));
});
*/


$(document).on('change', '.rbatch_no', function () {
    var item_id = $(this).closest('tr').attr('data-item-id');
    store_rec_items[item_id].row.batch_no = $(this).val();
    localStorage.setItem('store_rec_items', JSON.stringify(store_rec_items));
});





// prevent default action upon enter
$('body').bind('keypress', function (e) {
    if ($(e.target).hasClass('redactor_editor')) {
        return true;
    }
    if (e.keyCode == 13) {
        e.preventDefault();
        return false;
    }
});

// Order tax calcuation
if (site.settings.tax2 != 0) {
    $('#potax2').change(function () {
        localStorage.setItem('store_rec_tax2', $(this).val());
        loadItems();
        return;
    });
}

// Order discount calcuation
var old_store_rec_discount;
$('#store_rec_discount').focus(function () {
    old_store_rec_discount = $(this).val();
}).change(function () {
    var pod = $(this).val() ? $(this).val() : 0;
    if (is_valid_discount(pod)) {
        localStorage.removeItem('store_rec_discount');
        localStorage.setItem('store_rec_discount', pod);
        loadItems();
        return;
    } else {
        $(this).val(old_store_rec_discount);
        bootbox.alert(lang.unexpected_value);
        return;
    }

});


    /* ----------------------
     * Delete Row Method
     * ---------------------- */

     $(document).on('click', '.podel', function () {
        var row = $(this).closest('tr');
        var item_id = row.attr('data-item-id');
        delete store_rec_items[item_id];
        row.remove();
        if(store_rec_items.hasOwnProperty(item_id)) { } else {
            localStorage.setItem('store_rec_items', JSON.stringify(store_rec_items));
            loadItems();
            return;
        }
    });

    /* -----------------------
     * Edit Row Modal Hanlder
     ----------------------- */
     $(document).on('click', '.edit', function () {

        var row = $(this).closest('tr');
        var row_id = row.attr('id');
        item_id = row.attr('data-item-id');
        item = store_rec_items[item_id];
        var qty = row.children().children('.rquantity').val(),
        product_option = row.children().children('.roption').val(),
        unit_cost = formatDecimal(row.children().children('.rucost').val()),
        discount = row.children().children('.rdiscount').val();
        $('#prModalLabel').text(item.row.name + ' (' + item.row.code + ')');
        var real_unit_cost = item.row.real_unit_cost;
        var net_cost = real_unit_cost;
        if (site.settings.tax1) {
            $('#ptax').select2('val', item.row.tax_rate);
			$('#ptax_method').select2('val', item.row.tax_method);
            $('#old_tax').val(item.row.tax_rate);
            var item_discount = 0, ds = discount ? discount : '0';
            if (ds.indexOf("%") !== -1) {
                var pds = ds.split("%");
                if (!isNaN(pds[0])) {
                    item_discount = parseFloat(((real_unit_cost) * parseFloat(pds[0])) / 100);
                } else {
                    item_discount = parseFloat(ds);
                }
            } else {
                item_discount = parseFloat(ds);
            }
            net_cost -= item_discount;
            var pr_tax = item.row.tax_rate, pr_tax_val = 0;
            if (pr_tax !== null && pr_tax != 0) {
                $.each(tax_rates, function () {
                    if(this.id == pr_tax){
                        if (this.type == 1) {

                            if (store_rec_items[item_id].row.tax_method == 0) {
                                pr_tax_val = formatDecimal((((real_unit_cost-item_discount) * parseFloat(this.rate)) / (100 + parseFloat(this.rate))), 4);
                                pr_tax_rate = formatDecimal(this.rate) + '%';
                                net_cost -= pr_tax_val;
                            } else {
                                pr_tax_val = formatDecimal((((real_unit_cost-item_discount) * parseFloat(this.rate)) / 100), 4);
                                pr_tax_rate = formatDecimal(this.rate) + '%';
                            }

                        } else if (this.type == 2) {

                            pr_tax_val = parseFloat(this.rate);
                            pr_tax_rate = this.rate;

                        }
                    }
                });
            }
        }
        if (site.settings.product_serial !== 0) {
            $('#pserial').val(row.children().children('.rserial').val());
        }
        var opt = '<p style="margin: 12px 0 0 0;">n/a</p>';
        if(item.options !== false) {
            var o = 1;
            opt = $("<select id=\"poption\" name=\"poption\" class=\"form-control select\" />");
            $.each(item.options, function () {
                if(o == 1) {
                    if(product_option == '') { product_variant = this.id; } else { product_variant = product_option; }
                }
                $("<option />", {value: this.id, text: this.name}).appendTo(opt);
                o++;
            });
        }

        uopt = $("<select id=\"punit\" name=\"punit\" class=\"form-control select\" />");
        $.each(item.units, function () {
            if(this.id == item.row.unit) {
                $("<option />", {value: this.id, text: this.name, selected:true}).appendTo(uopt);
            } else {
                $("<option />", {value: this.id, text: this.name}).appendTo(uopt);
            }
        });

        $('#poptions-div').html(opt);
        $('#punits-div').html(uopt);
        $('select.select').select2({minimumResultsForSearch: 7});
        $('#pquantity').val(qty);
        $('#old_qty').val(qty);
        $('#pcost').val(unit_cost);
        $('#punit_cost').val(formatDecimal(parseFloat(unit_cost)+parseFloat(pr_tax_val)));
        $('#poption').select2('val', item.row.option);
        $('#old_cost').val(unit_cost);
        $('#row_id').val(row_id);
        $('#item_id').val(item_id);
        $('#pmfg').val(row.children().children('.rmfg').val());
		$('#pexpiry').val(row.children().children('.rexpiry').val());
		$('#pbatch_no').val(row.children().children('.rbatch_no').val());
        $('#pdiscount').val(discount);
        $('#net_cost').text(formatMoney(net_cost));
        $('#pro_tax').text(formatMoney(pr_tax_val));
        $('#psubtotal').val('');
        $('#prModal').appendTo("body").modal('show');

    });

    $('#prModal').on('shown.bs.modal', function (e) {
        if($('#poption').select2('val') != '') {
            $('#poption').select2('val', product_variant);
            product_variant = 0;
        }
    });

    $(document).on('change', '#pcost, #ptax, #ptax_method, #pdiscount', function () {
        var row = $('#' + $('#row_id').val());
        var item_id = row.attr('data-item-id');
        var unit_cost = parseFloat($('#pcost').val());
        var item = store_rec_items[item_id];
        var ds = $('#pdiscount').val() ? $('#pdiscount').val() : '0';
        if (ds.indexOf("%") !== -1) {
            var pds = ds.split("%");
            if (!isNaN(pds[0])) {
                item_discount = parseFloat(((unit_cost) * parseFloat(pds[0])) / 100);
            } else {
                item_discount = parseFloat(ds);
            }
        } else {
            item_discount = parseFloat(ds);
        }
        unit_cost -= item_discount;
        var pr_tax = $('#ptax').val(), item_tax_method = ($('#ptax_method').val()) ? $('#ptax_method').val() : item.row.tax_method;
        var pr_tax_val = 0, pr_tax_rate = 0;
        if (pr_tax !== null && pr_tax != 0) {
            $.each(tax_rates, function () {
                if(this.id == pr_tax){
                    if (this.type == 1) {
                        if (item_tax_method == 0) {
                            pr_tax_val = formatDecimal((((unit_cost) * parseFloat(this.rate)) / (100 + parseFloat(this.rate))), 4);
                            pr_tax_rate = formatDecimal(this.rate) + '%';
                            unit_cost -= pr_tax_val;
                        } else {
                            pr_tax_val = formatDecimal((((unit_cost) * parseFloat(this.rate)) / 100), 4);
                            pr_tax_rate = formatDecimal(this.rate) + '%';
                        }
                    } else if (this.type == 2) {
                        pr_tax_val = parseFloat(this.rate);
                        pr_tax_rate = this.rate;

                    }
                }
            });
        }

        $('#net_cost').text(formatMoney(unit_cost));
        $('#pro_tax').text(formatMoney(pr_tax_val));
    });

    $(document).on('change', '#punit', function () {
        var row = $('#' + $('#row_id').val());
        var item_id = row.attr('data-item-id');
        var item = store_rec_items[item_id];
        if (!is_numeric($('#pquantity').val()) || parseFloat($('#pquantity').val()) < 0) {
            $(this).val(old_row_qty);
            bootbox.alert(lang.unexpected_value);
            return;
        }
        var unit = $('#punit').val();
        if(unit != store_rec_items[item_id].row.base_unit) {
            $.each(item.units, function() {
                if (this.id == unit) {
                    $('#pcost').val(formatDecimal((parseFloat(item.row.base_unit_cost)*(unitToBaseQty(1, this))), 4)).change();
                }
            });
        } else {
            $('#pcost').val(formatDecimal(item.row.base_unit_cost)).change();
        }
    });

    $(document).on('click', '#calculate_unit_price', function () {
        var row = $('#' + $('#row_id').val());
        var item_id = row.attr('data-item-id');
        var item = store_rec_items[item_id];
        if (!is_numeric($('#pquantity').val()) || parseFloat($('#pquantity').val()) < 0) {
            $(this).val(old_row_qty);
            bootbox.alert(lang.unexpected_value);
            return;
        }
        var subtotal = parseFloat($('#psubtotal').val()),
        qty = parseFloat($('#pquantity').val());
        $('#pcost').val(formatDecimal((subtotal/qty), 4)).change();
        return false;
    });

    /* -----------------------
     * Edit Row Method
     ----------------------- */
     $(document).on('click', '#editItem', function () {
        var row = $('#' + $('#row_id').val());
        var item_id = row.attr('data-item-id'), new_pr_tax = $('#ptax').val(), new_pr_tax_rate = {}, new_pr_tax_method = $('#ptax_method').val();
        if (new_pr_tax) {
            $.each(tax_rates, function () {
                if (this.id == new_pr_tax) {
                    new_pr_tax_rate = this;
                }
            });
        }

        if (!is_numeric($('#pquantity').val()) || parseFloat($('#pquantity').val()) < 0) {
            $(this).val(old_row_qty);
            bootbox.alert(lang.unexpected_value);
            return;
        }

        var unit = $('#punit').val();
        var base_quantity = parseFloat($('#pquantity').val());
        if(unit != store_rec_items[item_id].row.base_unit) {
            $.each(store_rec_items[item_id].units, function(){
                if (this.id == unit) {
                    base_quantity = unitToBaseQty($('#pquantity').val(), this);
                }
            });
        }

        store_rec_items[item_id].row.fup = 1,
        store_rec_items[item_id].row.qty = parseFloat($('#pquantity').val()),
        store_rec_items[item_id].row.base_quantity = parseFloat(base_quantity),
        store_rec_items[item_id].row.unit = unit,
        store_rec_items[item_id].row.real_unit_cost = parseFloat($('#pcost').val()),
        store_rec_items[item_id].row.tax_rate = new_pr_tax,
		store_rec_items[item_id].row.tax_method = new_pr_tax_method,
        store_rec_items[item_id].tax_rate = new_pr_tax_rate,
        store_rec_items[item_id].row.discount = $('#pdiscount').val() ? $('#pdiscount').val() : '0',
        store_rec_items[item_id].row.option = $('#poption').val(),		
		store_rec_items[item_id].row.tax1 = $('#ptax1').val(),
		store_rec_items[item_id].row.tax2 = $('#ptax2').val(),		
        localStorage.setItem('store_rec_items', JSON.stringify(store_rec_items));
        $('#prModal').modal('hide');               
        loadItems();
        return;
    });

    /* ------------------------------
     * Show manual item addition modal
     ------------------------------- */
     $(document).on('click', '#addManually', function (e) {
        $('#mModal').appendTo("body").modal('show');
        return false;
    });

    /* --------------------------
     * Edit Row Quantity Method
     -------------------------- */
     var old_row_qty;
     $(document).on("focus", '.rquantity', function () {
        old_row_qty = $(this).val();
    }).on("change", '.rquantity', function () {
        var row = $(this).closest('tr');
        if (!is_numeric($(this).val()) || parseFloat($(this).val()) < 0) {
            $(this).val(old_row_qty);
            bootbox.alert(lang.unexpected_value);
            return;
        }
        var new_qty = parseFloat($(this).val()),
        item_id = row.attr('data-item-id');
        store_rec_items[item_id].row.base_quantity = new_qty;
        if(store_rec_items[item_id].row.unit != store_rec_items[item_id].row.base_unit) {
            $.each(store_rec_items[item_id].units, function(){
                if (this.id == store_rec_items[item_id].row.unit) {
                    store_rec_items[item_id].row.base_quantity = unitToBaseQty(new_qty, this);
                }
            });
        }
        store_rec_items[item_id].row.qty = new_qty;
        store_rec_items[item_id].row.received = new_qty;
        localStorage.setItem('store_rec_items', JSON.stringify(store_rec_items));
        loadItems();
    });
	
	$(document).on("change", '.rtax1', function () {
		var row = $(this).closest('tr');
		current_tax_method = $(this).val();
		item_id = row.attr('data-item-id');
		store_rec_items[item_id].row.tax_method = current_tax_method;
		localStorage.setItem('store_rec_items', JSON.stringify(store_rec_items));
		loadItems();
	});
	
	$(document).on("change", '.rtax2', function () {
		var row = $(this).closest('tr');
		current_tax = $(this).val();
		item_id = row.attr('data-item-id');
		store_rec_items[item_id].tax_rate = current_tax;
		localStorage.setItem('store_rec_items', JSON.stringify(store_rec_items));
		loadItems();
	});

    var old_received;
     $(document).on("focus", '.received', function () {
        old_received = $(this).val();
    }).on("change", '.received', function () {
        var row = $(this).closest('tr');
        new_received = $(this).val() ? $(this).val() : 0;
        if (!is_numeric(new_received)) {
            $(this).val(old_received);
            bootbox.alert(lang.unexpected_value);
            return;
        }
        var new_received = parseFloat($(this).val()),
        item_id = row.attr('data-item-id');
        if (new_received > store_rec_items[item_id].row.qty) {
            $(this).val(old_received);
            bootbox.alert(lang.unexpected_value);
            return;
        }
        unit = formatDecimal(row.children().children('.runit').val()),
        $.each(store_rec_items[item_id].units, function(){
            if (this.id == unit) {
                qty_received = formatDecimal(unitToBaseQty(new_received, this), 4);
            }
        });
        store_rec_items[item_id].row.unit_received = new_received;
        store_rec_items[item_id].row.received = qty_received;
        localStorage.setItem('store_rec_items', JSON.stringify(store_rec_items));
        loadItems();
    });

    $(document).on("change", '.rucost', function () {
        var row = $(this).closest('tr');
        current_ucost = $(this).val();
        item_id = row.attr('data-item-id');
        store_rec_items[item_id].row.real_unit_cost = current_ucost;
        localStorage.setItem('store_rec_items', JSON.stringify(store_rec_items));
        loadItems();
    });

    $(document).on("change", '.rdiscount', function () {
        var row = $(this).closest('tr');
        current_discount = $(this).val();
        item_id = row.attr('data-item-id');
        store_rec_items[item_id].row.discount = current_discount;
        localStorage.setItem('store_rec_items', JSON.stringify(store_rec_items));
        loadItems();
    });       
    /* --------------------------
     * Edit Row Cost Method
     -------------------------- */
     var old_cost;
     $(document).on("focus", '.rcost', function () {
        old_cost = $(this).val();
    }).on("change", '.rcost', function () {
        var row = $(this).closest('tr');
        if (!is_numeric($(this).val())) {
            $(this).val(old_cost);
            bootbox.alert(lang.unexpected_value);
            return;
        }
        var new_cost = parseFloat($(this).val()),
        item_id = row.attr('data-item-id');
        store_rec_items[item_id].row.cost = new_cost;
        localStorage.setItem('store_rec_items', JSON.stringify(store_rec_items));
        loadItems();
    });

    $(document).on("click", '#removeReadonly', function () {
     $('#store_rec_supplier').select2('readonly', false);
     return false;
 });

    if (store_receivers_edit) {
        $('#store_rec_supplier').select2("readonly", true);
    }

});
/* -----------------------
 * Misc Actions
 ----------------------- */

// hellper function for supplier if no localStorage value
function nsSupplier() {
	
    $('#store_rec_supplier').select2({
        minimumInputLength: 1,
        ajax: {
            url: site.base_url + "suppliers/suggestions",
            dataType: 'json',
            quietMillis: 15,
            data: function (term, page) {
				
                return {
                    term: term,
                    limit: 10
                };
            },
            results: function (data, page) {
                if (data.results != null) {
                    return {results: data.results};
                } else {
                    return {results: [{id: '', text: 'No Match Found'}]};
                }
            }
        }
    });
}



function loadItems() {
	 if (localStorage.getItem('store_rec_items')) {
        total = 0;
        count = 1;
        an = 1;
        product_tax = 0;
        invoice_tax = 0;
        product_discount = 0;
        order_discount = 0;
        total_discount = 0;
        $("#store_receiversTable tbody").empty();
        store_rec_items = JSON.parse(localStorage.getItem('store_rec_items'));
	    sortedItems = store_rec_items;
        var order_no = new Date().getTime();
		$row_index = 0;
		$total_no_items = 0;
		$total_no_qty = 0;
        $.each(sortedItems, function () {
	    var item = this;
	    $product_grand_total_amt = 0;
	    $product_gross_amt=0;
	    $product_tax=0;
            var item_id = item.unique_id;
			item.order = item.order ? item.order : new Date().getTime();
            $sno = ++$row_index;
            $id = item.row.id;
            $product_name = item.row.name;
            $product_code = item.row.code;
            $product_type = item.row.type;
			console.log(item.row.unit)
	        	var product_unit    = item.row.unit;
			$product_tax_per = item.row.tax;
			$product_tax_method = item.row.tax_method;
            $qty = item.row.qty;
			$ex_qty = (item.row.ex_qty!=undefined)?item.row.ex_qty:0;
			$request_qty =item.row.request_qty ? item.row.request_qty : 0;
			$html = '<tr class="order-item-row warning" data-item="'+item_id+'"><input type="hidden" name="store_receive_itemid[]" value="'+item.store_receiveItemid+'">';
            $html += '<td>'+$sno+'<input type="hidden" name="product_id[]" value="'+$id+'"></td>';
            $html += '<td>'+$product_code+'<input type="hidden" name="product_code[]" value="'+$product_code+'"></td>';
            $html += '<td>'+$product_name+'<input type="hidden" name="product_name[]" value="'+$product_name+'"><input type="hidden" name="product_type[]" value="'+$product_type+'"><input type="hidden" name="variant_id[]" value="'+item.row.variant_id+'"><input type="hidden" name="category_id[]" value="'+item.row.category_id+'"><input type="hidden" name="subcategory_id[]" value="'+item.row.subcategory_id+'"><input type="hidden" name="brand_id[]" value="'+item.row.brand_id+'"></td>';
	   
			$html += '<td><input type="text" name="request_qty[]" value="'+$request_qty+'" class="form-control text-center request-qty" readonly></td>';
			$html +='<td colspan="11">';
			$transfer_qty = 0;
			$.each(item.row.batches,function(n,v){
			$cost =v.cost_price;
			$product_price = v.price;
			$product_stock_id = v.stock_id;
			$product_expiry =(v.expiry!=null)?v.expiry:'';
			$product_cost =v.cost_price;
			$product_batch_label = (v.batch_no=='')?'No batch':v.batch_no;
			$product_batch  = (v.batch_no=='')?'':v.batch_no;
			$transfer_qty =v.transfer_qty ? v.transfer_qty : 0;
			$landingCost =v.landing_cost ? v.landing_cost : 0;
			$received_qty =v.received_qty ? v.received_qty : '';
			$product_gross_amt = $transfer_qty*$product_price;
			$product_tax_per = v.tax;
			$product_tax_method =v.tax_method;
			$product_vendor = (v.vendor_id)?v.vendor_id:0;
	        $product_tax=0;
			$tax_per = v.tax;
		
			 $base_received_qty = $received_qty;
				$tax_method = v.tax_method;
				var pr_tax = item.tax_rate;
                var pr_tax_val = pr_tax_rate = 0;
                if (site.settings.tax1 == 1 && (ptax = calculateTax(pr_tax, $cost, $product_tax_method))) {
                    pr_tax_val = ptax[0];
                    pr_tax_rate = ptax[1];
                    $product_tax += pr_tax_val * item_qty;
                }
				
				
					 if( product_unit != item.row.base_unit) {
					$.each(item.units, function(){
                    if (this.id == product_unit) {
                        $base_received_qty = formatDecimal(unitToBaseQty($received_qty, this), 4);
						
                    }
                });
            }
				
				
				
				
				
				
			$product_grand_total_amt = $product_gross_amt+$product_tax;
			$batch_html ='<table style="width: 100%;" class="table items  table-bordered table-condensed batch-table"><thead>';
			$batch_html +='<th>batch</th>';
			$batch_html +='<th>t.qty</th>';
			$batch_html +='<th>received qty</th>';
			$batch_html +='<th>expiry</th>';
			$batch_html +='<th>c.price</th>';
			$batch_html +='<th>s.price</th>';
			$batch_html +='<th>tax</th>';
			$batch_html +='<th>gross</th>';
			$batch_html +='<th>tax amt</th>';
			$batch_html +='<th>total</th>';
			$batch_html +='</thead><tbody>';
			$batch_html +='<tr class="batch-row" data-item='+item_id+' data-batch='+n+'>';
			$batch_html += '<td style="width: 78px;font-size: 13px;">'+$product_batch_label+'<input type="hidden" name="batch['+item_id+']['+n+'][stock_id]" value="'+$product_stock_id+'"><input type="hidden" name="batch['+item_id+']['+n+'][batch_no]" value="'+$product_batch+'"><input type="hidden" name="batch['+item_id+']['+n+'][vendor_id]" value="'+$product_vendor+'"><input type="hidden" name="batch['+item_id+']['+n+'][invoice_id]" value="'+v.invoice_id+'"><input type="hidden" name="batch['+item_id+']['+n+'][itemid]" value="'+v.itemid+'"><input type="hidden" name="batch['+item_id+']['+n+'][storereceiverid]" value="'+v.stri+'"><input type="hidden" name="batch['+item_id+']['+n+'][storereceiveritemid]" value="'+v.strii+'"></td>';
	    
			$batch_html += '<td><input type="hidden" name="batch['+item_id+']['+n+'][landing_cost]" value="'+$landingCost+'"><input type="text" name="batch['+item_id+']['+n+'][transfer_qty]" value="'+$transfer_qty+'" class="form-control text-center transfer-qty"></td>';	    
		if($received_qty ==0 || isNaN($received_qty)){
			$total_no_qty +=parseInt($transfer_qty);
	    $batch_html += '<td><input type="text" name="batch['+item_id+']['+n+'][received_qty]" value="'+$transfer_qty+'"  class="numberonly form-control text-center received-qty"><input type="hidden" name="batch['+item_id+']['+n+'][base_received_qty]" value="'+$base_received_qty+'"  ></td>';
	    }else{
			$batch_html += '<td><input type="text" name="batch['+item_id+']['+n+'][received_qty]" value="'+$received_qty+'"  class="numberonly form-control text-center received-qty"><input type="hidden" name="batch['+item_id+']['+n+'][base_received_qty]" value="'+$base_received_qty+'"  ></td>';
		}
	    $batch_html += '<td style="width: 78px;font-size: 13px;">'+$product_expiry+'<input type="hidden" name="batch['+item_id+']['+n+'][expiry]" value="'+$product_expiry+'"></td>';
	    
	    $batch_html += '<td><input type="text" name="batch['+item_id+']['+n+'][cost_price]" readonly value="'+$product_cost+'" class="form-control text-center cost-price"></td>';
	    $batch_html += '<td><input type="text" name="batch['+item_id+']['+n+'][selling_price]" value="'+$product_price+'" class="form-control text-center selling-price"></td>';
	    $batch_html += '<td>'+formatDecimal($product_tax_per)+'%<input type="hidden" name="batch['+item_id+']['+n+'][tax]" value="'+$product_tax_per+'" class="form-control text-center product-tax-per"><input type="hidden" name="batch['+item_id+']['+n+'][tax_method]" value="'+$product_tax_method+'"></td>';
	    $batch_html += '<td><span class="product-gross-amt-label">'+formatDecimal($product_gross_amt)+'</span><input type="hidden" name="batch['+item_id+']['+n+'][gross]" value="'+$product_gross_amt+'" class="form-control text-center product-gross"></td>';
	    $batch_html += '<td><span class="product-tax-amt-label">'+formatDecimal($product_tax)+'</span><input type="hidden" name="batch['+item_id+']['+n+'][tax_amount]" value="'+$product_tax+'" class="form-control text-center product-tax-amt"></td>';
	    $batch_html += '<td><span class="product-grand-total-label">'+formatDecimal($product_grand_total_amt)+'</span><input type="hidden" name="batch['+item_id+']['+n+'][product_grand_total]" value="'+$product_grand_total_amt+'" class="form-control text-center product-grand-total"></td>';
	    
	    $batch_html +='</tr>';
	    
	    $batch_html +'</tbody>';
	    $batch_html +='</table>';
	    $html +=$batch_html;
	    $total_no_qty+=parseInt($received_qty);
	
	    });
	    $html +='</td>';
	  // $html += '<td class="text-center"><i class="fa fa-times tip podel" id="' + item_id + '" title="Remove" style="cursor:pointer;"></i></td>';
	    $html +='</tr>';
	    
            $('#store_receiversTable>tbody').append($html);
	    
	    $total_no_items++;
        });
       
        $('#total_no_items').val($total_no_items);
		$('#total_no_qty').val($total_no_qty);
        set_page_focus();
    }
}


/* -----------------------------
 * Add Purchase Iten Function
 * @param {json} item
 * @returns {Boolean}
 ---------------------------- */
 function add_store_receivers_item(item) {
    if (count == 1) {
        store_rec_items = {};
        if ($('#store_rec_supplier').val()) {
             
            $('#store_rec_supplier').select2("readonly", true);
        } else {
            bootbox.alert(lang.select_above);
            item = null;
            return;
        }
    }
    if (item == null)
        return;

   // var item_id = site.settings.item_addition == 1 ? item.item_id : item.id;
   var item_id = item.item_id;
   
    if (store_rec_items[item_id]) {
		
		bootbox.confirm('This item is already added. Do you want to add it again?', function (result) {
			
			if (result) {
				var new_qty = parseFloat(store_rec_items[item_id].row.qty) + 1;
				store_rec_items[item_id].row.base_quantity = new_qty;
				if(store_rec_items[item_id].row.unit != store_rec_items[item_id].row.base_unit) {
					$.each(store_rec_items[item_id].units, function(){
						if (this.id == store_rec_items[item_id].row.unit) {
							store_rec_items[item_id].row.base_quantity = unitToBaseQty(new_qty, this);
						}
					});
				}
				store_rec_items[item_id].row.qty = new_qty;
				store_rec_items[item_id].row.batch_no = '';
				store_rec_items[item_id].row.mfg = '';
				store_rec_items[item_id].order = new Date().getTime();
				localStorage.setItem('store_rec_items', JSON.stringify(store_rec_items));
				loadItems();
				return true;
				
			}
		});
		
		

    } else {
        store_rec_items[item_id] = item;
		store_rec_items[item_id].row.batch_no = '';
		store_rec_items[item_id].row.mfg = '';
		store_rec_items[item_id].order = new Date().getTime();
		localStorage.setItem('store_rec_items', JSON.stringify(store_rec_items));
		loadItems();
		return true;
    }
	

}

if (typeof (Storage) === "undefined") {
    $(window).bind('beforeunload', function (e) {
        if (count > 1) {
            var message = "You will loss data!";
            return message;
        }
    });
}
