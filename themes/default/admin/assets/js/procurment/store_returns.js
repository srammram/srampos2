$(document).ready(function () {
$('body a, body button').attr('tabindex', -1);
check_add_item_val();
if (site.settings.set_focus != 1) {
    $('#add_item').focus();
}
// Order level shipping and discoutn localStorage
    if (store_rtn_discount = localStorage.getItem('store_rtn_discount')) {
        $('#store_rtn_discount').val(store_rtn_discount);
    }
    $('#store_rtn_tax2').change(function (e) {
        localStorage.setItem('store_rtn_tax2', $(this).val());
        $('#store_rtn_tax2').val($(this).val());
    });
    if (store_rtn_tax2 = localStorage.getItem('store_rtn_tax2')) {
        $('#store_rtn_tax2').select2("val", store_rtn_tax2);
    }
    $('#store_rtn_status').change(function (e) {
        localStorage.setItem('store_rtn_status', $(this).val());
    });
    if (store_rtn_status = localStorage.getItem('store_rtn_status')) {
        $('#store_rtn_status').select2("val", store_rtn_status);
    }
    var old_shipping;
    $('#store_rtn_shipping').focus(function () {
        old_shipping = $(this).val();
    }).change(function () {
        if (!is_numeric($(this).val())) {
            $(this).val(old_shipping);
            bootbox.alert(lang.unexpected_value);
            return;
        } else {
            shipping = $(this).val() ? parseFloat($(this).val()) : '0';
        }
        localStorage.setItem('store_rtn_shipping', shipping);
        var gtotal = ((total + invoice_tax) - order_discount) + shipping;
        $('#gtotal').text(formatMoney(gtotal));
        $('#tship').text(formatMoney(shipping));
    });
    if (store_rtn_shipping = localStorage.getItem('store_rtn_shipping')) {
        shipping = parseFloat(store_rtn_shipping);
        $('#store_rtn_shipping').val(shipping);
    } else {
        shipping = 0;
    }

    $('#store_rtn_supplier').change(function (e) {
        localStorage.setItem('store_rtn_supplier', $(this).val());
        $('#supplier_id').val($(this).val());
    });
    if (store_rtn_supplier = localStorage.getItem('store_rtn_supplier')) {
        $('#store_rtn_supplier').val(store_rtn_supplier).select2({
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
                reqietMillis: 15,
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

    // If there is any item in localStorage
    if (localStorage.getItem('store_rtn_items')) {
        loadItems();
    }

    // clear localStorage and reload
    $('#reset').click(function (e) {
            bootbox.confirm(lang.r_u_sure, function (result) {
                if (result) {
                    if (localStorage.getItem('store_rtn_items')) {
                        localStorage.removeItem('store_rtn_items');
                    }
                   
					if (localStorage.getItem('store_rtn_from_store_id')) {
                        localStorage.removeItem('store_rtn_from_store_id');
                    }
					if (localStorage.getItem('store_rtn_to_store_id')) {
                        localStorage.removeItem('store_rtn_to_store_id');
                    }
					if (localStorage.getItem('store_rtn_store')) {
                        localStorage.removeItem('store_rtn_store');
                    }
                    if (localStorage.getItem('store_rtn_note')) {
                        localStorage.removeItem('store_rtn_note');
                    }
                    if (localStorage.getItem('store_rtn_innote')) {
                        localStorage.removeItem('store_rtn_innote');
                    }
                   
                    if (localStorage.getItem('store_rtn_date')) {
                        localStorage.removeItem('store_rtn_date');
                    }
                    if (localStorage.getItem('store_rtn_status')) {
                        localStorage.removeItem('store_rtn_status');
                    }
                   

                    $('#modal-loading').show();
                    location.reload();
                }
            });
    });

// save and load the fields in and/or from localStorage

    $('#store_rtn_ref').change(function (e) {
        localStorage.setItem('store_rtn_ref', $(this).val());
    });
    if (store_rtn_ref = localStorage.getItem('store_rtn_ref')) {
        $('#store_rtn_ref').val(store_rtn_ref);
    }
	
    $('#store_rtn_warehouse').change(function (e) {
        localStorage.setItem('store_rtn_warehouse', $(this).val());
    });
    if (store_rtn_warehouse = localStorage.getItem('store_rtn_warehouse')) {
        $('#store_rtn_warehouse').select2("val", store_rtn_warehouse);
    }
	
	 $('#store_rtn_from_store_id').change(function (e) {
        localStorage.setItem('store_rtn_from_store_id', $(this).val());
    });
    if (store_rtn_from_store_id = localStorage.getItem('store_rtn_from_store_id')) {
        $('#store_rtn_from_store_id').select2("val", store_rtn_from_store_id);
    }
	
	 $('#store_rtn_to_store_id').change(function (e) {
        localStorage.setItem('store_rtn_to_store_id', $(this).val());
    });
    if (store_rtn_to_store_id = localStorage.getItem('store_rtn_to_store_id')) {
        $('#store_rtn_to_store_id').select2("val", store_rtn_to_store_id);
    }
	
	$('#store_rtn_store').change(function (e) {
        localStorage.setItem('store_rtn_store', $(this).val());
    });
    if (store_rtn_store = localStorage.getItem('store_rtn_store')) {
        $('#store_rtn_store').select2("val", store_rtn_store);
    }

    $('#store_rtn_note').redactor('destroy');
    $('#store_rtn_note').redactor({
        buttons: ['formatting', '|', 'alignleft', 'aligncenter', 'alignright', 'justify', '|', 'bold', 'italic', 'underline', '|', 'unorderedlist', 'orderedlist', '|', 'link', '|', 'html'],
        formattingTags: ['p', 'pre', 'h3', 'h4'],
        minHeight: 100,
        changeCallback: function (e) {
            var v = this.get();
            localStorage.setItem('store_rtn_note', v);
        }
    });
    if (store_rtn_note = localStorage.getItem('store_rtn_note')) {
        $('#store_rtn_note').redactor('set', store_rtn_note);
    }
    var $customer = $('#store_rtn_customer');
    $customer.change(function (e) {
        localStorage.setItem('store_rtn_customer', $(this).val());
    });
    if (store_rtn_customer = localStorage.getItem('store_rtn_customer')) {
        $customer.val(store_rtn_customer).select2({
            minimumInputLength: 1,
            data: [],
            initSelection: function (element, callback) {
                $.ajax({
                    type: "get", async: false,
                    url: site.base_url+"customers/getCustomer/" + $(element).val(),
                    dataType: "json",
                    success: function (data) {
                        callback(data[0]);
                    }
                });
            },
            ajax: {
                url: site.base_url + "customers/suggestions",
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
        nsCustomer();
    }


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

// Order tax calculation
if (site.settings.tax2 != 0) {
    $('#store_rtn_tax2').change(function () {
        localStorage.setItem('store_rtn_tax2', $(this).val());
        loadItems();
        return;
    });
}

// Order discount calculation
var old_store_rtn_discount;
$('#store_rtn_discount').focus(function () {
    old_store_rtn_discount = $(this).val();
}).change(function () {
    var new_discount = $(this).val() ? $(this).val() : '0';
    if (is_valid_discount(new_discount)) {
        localStorage.removeItem('store_rtn_discount');
        localStorage.setItem('store_rtn_discount', new_discount);
        loadItems();
        return;
    } else {
        $(this).val(old_store_rtn_discount);
        bootbox.alert(lang.unexpected_value);
        return;
    }

});

/* ----------------------
 * Delete Row Method
 * ---------------------- */
$(document).on('click', '.store_rtn_del', function () {
    var row = $(this).closest('tr');
    var item_id = row.attr('data-item-id');
    delete store_rtn_items[item_id];
    row.remove();
    if(store_rtn_items.hasOwnProperty(item_id)) { } else {
        localStorage.setItem('store_rtn_items', JSON.stringify(store_rtn_items));
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
        item = store_rtn_items[item_id];
        var qty = row.children().children('.rquantity').val(),
        product_option = row.children().children('.roption').val(),
        unit_price = formatDecimal(row.children().children('.ruprice').val()),
        discount = row.children().children('.rdiscount').val();
        if(item.options !== false) {
            $.each(item.options, function () {
                if(this.id == item.row.option && this.price != 0 && this.price != '' && this.price != null) {
                    unit_price = parseFloat(item.row.real_unit_price)+parseFloat(this.price);
                }
            });
        }
        var real_unit_price = item.row.real_unit_price;
        var net_price = unit_price;
        $('#prModalLabel').text(item.row.name + ' (' + item.row.code + ')');
        if (site.settings.tax1) {
            $('#ptax').select2('val', item.row.tax_rate);
            $('#old_tax').val(item.row.tax_rate);
            var item_discount = 0, ds = discount ? discount : '0';
            if (ds.indexOf("%") !== -1) {
                var pds = ds.split("%");
                if (!isNaN(pds[0])) {
                    item_discount = formatDecimal(parseFloat(((unit_price) * parseFloat(pds[0])) / 100), 4);
                } else {
                    item_discount = parseFloat(ds);
                }
            } else {
                item_discount = parseFloat(ds);
            }
            net_price -= item_discount;
            var pr_tax = item.row.tax_rate, pr_tax_val = 0;
            if (pr_tax !== null && pr_tax != 0) {
                $.each(tax_rates, function () {
                    if(this.id == pr_tax){
                        if (this.type == 1) {

                            if (store_rtn_items[item_id].row.tax_method == 0) {
                                pr_tax_val = formatDecimal((((net_price) * parseFloat(this.rate)) / (100 + parseFloat(this.rate))), 4);
                                pr_tax_rate = formatDecimal(this.rate) + '%';
                                net_price -= pr_tax_val;
                            } else {
                                pr_tax_val = formatDecimal((((net_price) * parseFloat(this.rate)) / 100), 4);
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
        } else {
            product_variant = 0;
        }

        uopt = '<p style="margin: 12px 0 0 0;">n/a</p>';
        if (item.units) {
            uopt = $("<select id=\"punit\" name=\"punit\" class=\"form-control select\" />");
            $.each(item.units, function () {
                if(this.id == item.row.unit) {
                    $("<option />", {value: this.id, text: this.name, selected:true}).appendTo(uopt);
                } else {
                    $("<option />", {value: this.id, text: this.name}).appendTo(uopt);
                }
            });
        }

        $('#poptions-div').html(opt);
        $('#punits-div').html(uopt);
        $('select.select').select2({minimumResultsForSearch: 7});
        $('#pquantity').val(qty);
        $('#old_qty').val(qty);
        $('#pprice').val(unit_price);
        $('#punit_price').val(formatDecimal(parseFloat(unit_price)+parseFloat(pr_tax_val)));
        $('#poption').select2('val', item.row.option);
        $('#old_price').val(unit_price);
        $('#row_id').val(row_id);
        $('#item_id').val(item_id);
        $('#pserial').val(row.children().children('.rserial').val());
        $('#pdiscount').val(discount);
        $('#net_price').text(formatMoney(net_price-item_discount));
        $('#pro_tax').text(formatMoney(pr_tax_val));
        $('#prModal').appendTo("body").modal('show');

    });

    $('#prModal').on('shown.bs.modal', function (e) {
        if($('#poption').select2('val') != '') {
            $('#poption').select2('val', product_variant);
            product_variant = 0;
        }
    });

    $(document).on('change', '#pprice, #ptax, #pdiscount', function () {
        var row = $('#' + $('#row_id').val());
        var item_id = row.attr('data-item-id');
        var unit_price = parseFloat($('#pprice').val());
        var item = store_rtn_items[item_id];
        var ds = $('#pdiscount').val() ? $('#pdiscount').val() : '0';
        if (ds.indexOf("%") !== -1) {
            var pds = ds.split("%");
            if (!isNaN(pds[0])) {
                item_discount = parseFloat(((unit_price) * parseFloat(pds[0])) / 100);
            } else {
                item_discount = parseFloat(ds);
            }
        } else {
            item_discount = parseFloat(ds);
        }
        unit_price -= item_discount;
        var pr_tax = $('#ptax').val(), item_tax_method = item.row.tax_method;
        var pr_tax_val = 0, pr_tax_rate = 0;
        if (pr_tax !== null && pr_tax != 0) {
            $.each(tax_rates, function () {
                if(this.id == pr_tax){
                    if (this.type == 1) {

                        if (item_tax_method == 0) {
                            pr_tax_val = formatDecimal(((unit_price) * parseFloat(this.rate)) / (100 + parseFloat(this.rate)), 4);
                            pr_tax_rate = formatDecimal(this.rate) + '%';
                            unit_price -= pr_tax_val;
                        } else {
                            pr_tax_val = formatDecimal((((unit_price) * parseFloat(this.rate)) / 100), 4);
                            pr_tax_rate = formatDecimal(this.rate) + '%';
                        }

                    } else if (this.type == 2) {

                        pr_tax_val = parseFloat(this.rate);
                        pr_tax_rate = this.rate;

                    }
                }
            });
        }

        $('#net_price').text(formatMoney(unit_price));
        $('#pro_tax').text(formatMoney(pr_tax_val));
    });

    $(document).on('change', '#punit', function () {
        var row = $('#' + $('#row_id').val());
        var item_id = row.attr('data-item-id');
        var item = store_rtn_items[item_id];
        if (!is_numeric($('#pquantity').val()) || parseFloat($('#pquantity').val()) < 0) {
            $(this).val(old_row_qty);
            bootbox.alert(lang.unexpected_value);
            return;
        }
        var opt = $('#poption').val(), unit = $('#punit').val(), base_quantity = $('#pquantity').val(), aprice = 0;
        if(item.options !== false) {
            $.each(item.options, function () {
                if(this.id == opt && this.price != 0 && this.price != '' && this.price != null) {
                    aprice = parseFloat(this.price);
                }
            });
        }
        if(item.units && unit != store_rtn_items[item_id].row.base_unit) {
            $.each(item.units, function(){
                if (this.id == unit) {
                    base_quantity = unitToBaseQty($('#pquantity').val(), this);
                    $('#pprice').val(formatDecimal(((parseFloat(item.row.base_unit_price+aprice))*unitToBaseQty(1, this)), 4)).change();
                }
            });
        } else {
            $('#pprice').val(formatDecimal(item.row.base_unit_price+aprice)).change();
        }
    });

    /* -----------------------
     * Edit Row Method
     ----------------------- */
     $(document).on('click', '#editItem', function () {
        var row = $('#' + $('#row_id').val());
        var item_id = row.attr('data-item-id'), new_pr_tax = $('#ptax').val(), new_pr_tax_rate = false;
        if (new_pr_tax) {
            $.each(tax_rates, function () {
                if (this.id == new_pr_tax) {
                    new_pr_tax_rate = this;
                }
            });
        }
        var price = parseFloat($('#pprice').val());
        if(item.options !== false) {
            var opt = $('#poption').val();
            $.each(item.options, function () {
                if(this.id == opt && this.price != 0 && this.price != '' && this.price != null) {
                    price = price-parseFloat(this.price);
                }
            });
        }
        if (site.settings.product_discount == 1 && $('#pdiscount').val()) {
            if(!is_valid_discount($('#pdiscount').val()) || $('#pdiscount').val() > price) {
                bootbox.alert(lang.unexpected_value);
                return false;
            }
        }
        if (!is_numeric($('#pquantity').val()) || parseFloat($('#pquantity').val()) < 0) {
            $(this).val(old_row_qty);
            bootbox.alert(lang.unexpected_value);
            return;
        }
        var unit = $('#punit').val();
        var base_quantity = parseFloat($('#pquantity').val());
        if(unit != store_rtn_items[item_id].row.base_unit) {
            $.each(store_rtn_items[item_id].units, function(){
                if (this.id == unit) {
                    base_quantity = unitToBaseQty($('#pquantity').val(), this);
                }
            });
        }

        store_rtn_items[item_id].row.fup = 1,
        store_rtn_items[item_id].row.qty = parseFloat($('#pquantity').val()),
        store_rtn_items[item_id].row.base_quantity = parseFloat(base_quantity),
        store_rtn_items[item_id].row.real_unit_price = price,
        store_rtn_items[item_id].row.unit = unit,
        store_rtn_items[item_id].row.tax_rate = new_pr_tax,
        store_rtn_items[item_id].tax_rate = new_pr_tax_rate,
        store_rtn_items[item_id].row.discount = $('#pdiscount').val() ? $('#pdiscount').val() : '',
        store_rtn_items[item_id].row.option = $('#poption').val() ? $('#poption').val() : '',
        store_rtn_items[item_id].row.serial = $('#pserial').val();
        localStorage.setItem('store_rtn_items', JSON.stringify(store_rtn_items));
        $('#prModal').modal('hide');

        loadItems();
        return;
    });

    /* -----------------------
     * Product option change
     ----------------------- */
     $(document).on('change', '#poption', function () {
        var row = $('#' + $('#row_id').val()), opt = $(this).val();
        var item_id = row.attr('data-item-id');
        var item = store_rtn_items[item_id];
        var unit = $('#punit').val(), base_quantity = parseFloat($('#pquantity').val()), base_unit_price = item.row.base_unit_price;
        if(unit != store_rtn_items[item_id].row.base_unit) {
            $.each(store_rtn_items[item_id].units, function(){
                if (this.id == unit) {
                    base_unit_price = formatDecimal((parseFloat(item.row.base_unit_price)*(unitToBaseQty(1, this))), 4)
                    base_quantity = unitToBaseQty($('#pquantity').val(), this);
                }
            });
        }
        if(item.options !== false) {
            $.each(item.options, function () {
                if(this.id == opt && this.price != 0 && this.price != '' && this.price != null) {
                    $('#pprice').val(parseFloat(base_unit_price)+(parseFloat(this.price))).trigger('change');
                }
            });
        }
    });

    /* ------------------------------
     * Show manual item addition modal
     ------------------------------- */
     $(document).on('click', '#addManually', function (e) {
        if (count == 1) {
            store_rtn_items = {};
            if ($('#store_rtn_warehouse').val()) {
                $('#store_rtn_customer').select2("readonly", true);
                $('#store_rtn_warehouse').select2("readonly", true);
				$('#store_rtn_from_store_id').select2("readonly", true);
				$('#store_rtn_to_store_id').select2("readonly", true);
				$('#store_rtn_store').select2("readonly", true);
            } else {
                bootbox.alert(lang.select_above);
                item = null;
                return false;
            }
        }
        $('#mnet_price').text('0.00');
        $('#mpro_tax').text('0.00');
        $('#mModal').appendTo("body").modal('show');
        return false;
    });

     $(document).on('click', '#addItemManually', function (e) {
        var mid = (new Date).getTime(),
        mcode = $('#mcode').val(),
        mname = $('#mname').val(),
        mtax = parseInt($('#mtax').val()),
        mqty = parseFloat($('#mquantity').val()),
        mdiscount = $('#mdiscount').val() ? $('#mdiscount').val() : '0',
        unit_price = parseFloat($('#mprice').val()),
        mtax_rate = {};
        if (mcode && mname && mqty && unit_price) {
            $.each(tax_rates, function () {
                if (this.id == mtax) {
                    mtax_rate = this;
                }
            });

            store_rtn_items[mid] = {"id": mid, "item_id": mid, "label": mname + ' (' + mcode + ')', "row": {"id": mid, "code": mcode, "name": mname, "quantity": mqty, "price": unit_price, "unit_price": unit_price, "real_unit_price": unit_price, "tax_rate": mtax, "tax_method": 0, "qty": mqty, "type": "manual", "discount": mdiscount, "serial": "", "option":""}, "tax_rate": mtax_rate, "options":false};
            localStorage.setItem('store_rtn_items', JSON.stringify(store_rtn_items));
            loadItems();
        }
        $('#mModal').modal('hide');
        $('#mcode').val('');
        $('#mname').val('');
        $('#mtax').val('');
        $('#mquantity').val('');
        $('#mdiscount').val('');
        $('#mprice').val('');
        return false;
    });

    $(document).on('change', '#mprice, #mtax, #mdiscount', function () {
        var unit_price = parseFloat($('#mprice').val());
        var ds = $('#mdiscount').val() ? $('#mdiscount').val() : '0';
        if (ds.indexOf("%") !== -1) {
            var pds = ds.split("%");
            if (!isNaN(pds[0])) {
                item_discount = parseFloat(((unit_price) * parseFloat(pds[0])) / 100);
            } else {
                item_discount = parseFloat(ds);
            }
        } else {
            item_discount = parseFloat(ds);
        }
        unit_price -= item_discount;
        var pr_tax = $('#mtax').val(), item_tax_method = 0;
        var pr_tax_val = 0, pr_tax_rate = 0;
        if (pr_tax !== null && pr_tax != 0) {
            $.each(tax_rates, function () {
                if(this.id == pr_tax){
                    if (this.type == 1) {

                        if (item_tax_method == 0) {
                            pr_tax_val = formatDecimal(((unit_price) * parseFloat(this.rate)) / (100 + parseFloat(this.rate)));
                            pr_tax_rate = formatDecimal(this.rate) + '%';
                            unit_price -= pr_tax_val;
                        } else {
                            pr_tax_val = formatDecimal(((unit_price) * parseFloat(this.rate)) / 100);
                            pr_tax_rate = formatDecimal(this.rate) + '%';
                        }

                    } else if (this.type == 2) {

                        pr_tax_val = parseFloat(this.rate);
                        pr_tax_rate = this.rate;

                    }
                }
            });
        }

        $('#mnet_price').text(formatMoney(unit_price));
        $('#mpro_tax').text(formatMoney(pr_tax_val));
    });

    /* --------------------------
     * Edit Row quantity Method
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
        store_rtn_items[item_id].row.base_quantity = new_qty;
        if(store_rtn_items[item_id].row.unit != store_rtn_items[item_id].row.base_unit) {
            $.each(store_rtn_items[item_id].units, function(){
                if (this.id == store_rtn_items[item_id].row.unit) {
                    store_rtn_items[item_id].row.base_quantity = unitToBaseQty(new_qty, this);
                }
            });
        }
        store_rtn_items[item_id].row.qty = new_qty;
        localStorage.setItem('store_rtn_items', JSON.stringify(store_rtn_items));
        loadItems();
    });

    /* --------------------------
     * Edit Row Price Method
     -------------------------- */
    var old_price;
    $(document).on("focus", '.rprice', function () {
        old_price = $(this).val();
    }).on("change", '.rprice', function () {
        var row = $(this).closest('tr');
        if (!is_numeric($(this).val())) {
            $(this).val(old_price);
            bootbox.alert(lang.unexpected_value);
            return;
        }
        var new_price = parseFloat($(this).val()),
                item_id = row.attr('data-item-id');
        store_rtn_items[item_id].row.price = new_price;
        localStorage.setItem('store_rtn_items', JSON.stringify(store_rtn_items));
        loadItems();
    });

    $(document).on("click", '#removeReadonly', function () {
       $('#store_rtn_customer').select2('readonly', false);
       //$('#store_rtn_warehouse').select2('readonly', false);
       return false;
    });


});
/* -----------------------
 * Misc Actions
 ----------------------- */

// hellper function for customer if no localStorage value
function nsCustomer() {
    $('#store_rtn_customer').select2({
        minimumInputLength: 1,
        ajax: {
            url: site.base_url + "customers/suggestions",
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

function nsSupplier() {
    $('#store_rtn_supplier').select2({
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

//localStorage.clear();
function loadItems() {
    if (localStorage.getItem('store_rtn_items')) {
			total = 0;
			count = 1;
			an = 1;
			product_tax = 0;
			invoice_tax = 0;
			product_discount = 0;
			order_discount = 0;
			total_discount = 0;
			$("#store_returnTable tbody").empty();
			store_rtn_items = JSON.parse(localStorage.getItem('store_rtn_items'));
			sortedItems = store_rtn_items;
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
			$product_tax_per = item.row.tax;
			$product_tax_method = item.row.tax_method;
            $qty = item.row.qty;
			$ex_qty = (item.row.ex_qty!=undefined)?item.row.ex_qty:0;
			$request_qty =item.row.request_qty ? item.row.request_qty : 0;
			$html = '<tr class="order-item-row warning" data-item="'+item_id+'"><input type="hidden" name="store_return_itemid[]" value="'+item.store_receiveItemid+'">';
            $html += '<td>'+$sno+'<input type="hidden" name="product_id[]" value="'+$id+'"></td>';
            $html += '<td>'+$product_code+'<input type="hidden" name="product_code[]" value="'+$product_code+'"></td>';
            $html += '<td>'+$product_name+'<input type="hidden" name="product_name[]" value="'+$product_name+'"><input type="hidden" name="product_type[]" value="'+$product_type+'"><input type="hidden" name="variant_id[]" value="'+item.row.variant_id+'"><input type="hidden" name="category_id[]" value="'+item.row.category_id+'"><input type="hidden" name="subcategory_id[]" value="'+item.row.subcategory_id+'"><input type="hidden" name="brand_id[]" value="'+item.row.brand_id+'"></td>';
	    
			$html += '<td><input type="text" name="request_qty[]" value="'+$request_qty+'" class="form-control text-center request-qty" readonly></td>';
			$html +='<td colspan="11">';
			$return_qty = 0;
			$.each(item.row.batches,function(n,v){
			$cost =v.cost_price;
			$product_price = v.price;
			$product_stock_id = v.stock_id;
			$product_expiry =(v.expiry!=null)?v.expiry:'';
			$product_cost =v.cost_price;
			$product_batch_label = (v.batch_no=='')?'No batch':v.batch_no;
			$product_batch  = (v.batch_no=='')?'':v.batch_no;
			$return_qty =v.return_qty ? v.return_qty : 0;
			$landingCost =v.landing_cost ? v.landing_cost : 0;
			$received_qty =v.received_qty ? v.received_qty : '';
			$product_gross_amt = $return_qty*$product_price;
			$product_tax_per = v.tax;
			$product_tax_method =v.tax_method;
			$product_vendor = (v.vendor_id)?v.vendor_id:0;
	        $product_tax=0;
			$return_type=v.return_type;
			$tax_per = v.tax;
				$tax_method = v.tax_method;
				var pr_tax = item.tax_rate;
                var pr_tax_val = pr_tax_rate = 0;
                if (site.settings.tax1 == 1 && (ptax = calculateTax(pr_tax, $cost, $product_tax_method))) {
                    pr_tax_val = ptax[0];
                    pr_tax_rate = ptax[1];
                    $product_tax += pr_tax_val * item_qty;
                }
			$product_grand_total_amt = $product_gross_amt+$product_tax;
			$batch_html ='<table style="width: 100%;" class="table items  table-bordered table-condensed batch-table"><thead>';
			$batch_html +='<th>batch</th>';
			$batch_html +='<th>return qty</th>';
			$batch_html +='<th>received qty</th>';
			$batch_html +='<th>expiry</th>';
			$batch_html +='<th>c.price</th>';
			$batch_html +='<th>s.price</th>';
			$batch_html +='<th>tax</th>';
			$batch_html +='<th>gross</th>';
			$batch_html +='<th>tax amt</th>';
			$batch_html +='<th>total</th>';
			$batch_html +='<th>r.type</th>';
			$batch_html +='</thead><tbody>';
			$batch_html +='<tr class="batch-row" data-item='+item_id+' data-batch='+n+'>';
			$batch_html += '<td style="width: 78px;font-size: 13px;">'+$product_batch_label+'<input type="hidden" name="batch['+item_id+']['+n+'][stock_id]" value="'+$product_stock_id+'"><input type="hidden" name="batch['+item_id+']['+n+'][batch_no]" value="'+$product_batch+'"><input type="hidden" name="batch['+item_id+']['+n+'][vendor_id]" value="'+$product_vendor+'"><input type="hidden" name="batch['+item_id+']['+n+'][invoice_id]" value="'+v.invoice_id+'"><input type="hidden" name="batch['+item_id+']['+n+'][itemid]" value="'+v.itemid+'"><input type="hidden" name="batch['+item_id+']['+n+'][storereturnid]" value="'+v.stri+'"><input type="hidden" name="batch['+item_id+']['+n+'][storereturnitemid]" value="'+v.strii+'"></td>';
	    
			$batch_html += '<td><input type="hidden" name="batch['+item_id+']['+n+'][landing_cost]" value="'+$landingCost+'"><input type="text" name="batch['+item_id+']['+n+'][return_qty]" value="'+$return_qty+'" class="form-control text-center transfer-qty"></td>';	    
		    if($received_qty ==0 || isNaN($received_qty)){
			$total_no_qty +=parseInt($return_qty);
	        $batch_html += '<td><input type="text" name="batch['+item_id+']['+n+'][received_qty]" value="'+$return_qty+'"  class="numberonly form-control text-center received-qty"></td>';
	       }else{
			$batch_html += '<td><input type="text" name="batch['+item_id+']['+n+'][received_qty]" value="'+$received_qty+'"  class="numberonly form-control text-center received-qty"></td>';
		 }
	    $batch_html += '<td style="width: 78px;font-size: 13px;">'+$product_expiry+'<input type="hidden" name="batch['+item_id+']['+n+'][expiry]" value="'+$product_expiry+'"></td>';
	    $batch_html += '<td><input type="text" name="batch['+item_id+']['+n+'][cost_price]" readonly value="'+$product_cost+'" class="form-control text-center cost-price"></td>';
	    $batch_html += '<td><input type="text" name="batch['+item_id+']['+n+'][selling_price]" value="'+$product_price+'" class="form-control text-center selling-price"></td>';
	    $batch_html += '<td>'+formatDecimal($product_tax_per)+'%<input type="hidden" name="batch['+item_id+']['+n+'][tax]" value="'+$product_tax_per+'" class="form-control text-center product-tax-per"><input type="hidden" name="batch['+item_id+']['+n+'][tax_method]" value="'+$product_tax_method+'"></td>';
	    $batch_html += '<td><span class="product-gross-amt-label">'+formatDecimal($product_gross_amt)+'</span><input type="hidden" name="batch['+item_id+']['+n+'][gross]" value="'+$product_gross_amt+'" class="form-control text-center product-gross"></td>';
	    $batch_html += '<td><span class="product-tax-amt-label">'+formatDecimal($product_tax)+'</span><input type="hidden" name="batch['+item_id+']['+n+'][tax_amount]" value="'+$product_tax+'" class="form-control text-center product-tax-amt"></td>';
	    $batch_html += '<td><span class="product-grand-total-label">'+formatDecimal($product_grand_total_amt)+'</span><input type="hidden" name="batch['+item_id+']['+n+'][product_grand_total]" value="'+$product_grand_total_amt+'" class="form-control text-center product-grand-total"></td>';
	    $batch_html += '<td><select name="batch['+item_id+']['+n+'][r_type]"><option value="damaged">damaged</option><option value="exist Qty">exist Qty</option><option value="Order">Order</option></select></td>';
	    $batch_html +='</tr>';
	    $batch_html +'</tbody>';
	    $batch_html +='</table>';
	    $html +=$batch_html;
	    $total_no_qty+=parseInt($received_qty);
	    });
	    $html +='</td>';
	  // $html += '<td class="text-center"><i class="fa fa-times tip podel" id="' + item_id + '" title="Remove" style="cursor:pointer;"></i></td>';
	    $html +='</tr>';
	    
            $('#store_returnTable>tbody').append($html);
	    
	    $total_no_items++;
        });
       
        $('#total_no_items').val($total_no_items);
		$('#total_no_qty').val($total_no_qty);
        set_page_focus();
    }
}

/* -----------------------------
 * Add reqotation Item Function
 * @param {json} item
 * @returns {Boolean}
 ---------------------------- */
 function add_invoice_item(item) {

    if (count == 1) {
        store_rtn_items = {};
        if ($('#store_rtn_warehouse').val()) {
            $('#store_rtn_customer').select2("readonly", true);
            $('#store_rtn_warehouse').select2("readonly", true);
			$('#store_rtn_from_store_id').select2("readonly", true);
			$('#store_rtn_to_store_id').select2("readonly", true);
			$('#store_rtn_store').select2("readonly", true);
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
	
	
    if (store_rtn_items[item_id]) {

        var new_qty = parseFloat(store_rtn_items[item_id].row.qty) + 1;
        store_rtn_items[item_id].row.base_quantity = new_qty;
        if(store_rtn_items[item_id].row.unit != store_rtn_items[item_id].row.base_unit) {
            $.each(store_rtn_items[item_id].units, function(){
                if (this.id == store_rtn_items[item_id].row.unit) {
                    store_rtn_items[item_id].row.base_quantity = unitToBaseQty(new_qty, this);
                }
            });
        }
		var available_qty = parseFloat(store_rtn_items[item_id].row.available_qty);
		if(available_qty >= new_qty){
        	store_rtn_items[item_id].row.qty = new_qty;
		}else{
			bootbox.alert('quantity is greater than');
            item = null;
            return;	
		}
    } else {
        store_rtn_items[item_id] = item;
    }
    store_rtn_items[item_id].order = new Date().getTime();
	
    localStorage.setItem('store_rtn_items', JSON.stringify(store_rtn_items));
    loadItems();
    return true;
	
	
}

if (typeof (Storage) === "undefined") {
    $(window).bind('beforeunload', function (e) {
        if (count > 1) {
            var message = "You will loss data!";
            return message;
        }
    });
}
