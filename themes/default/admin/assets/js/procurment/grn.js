$(document).ready(function () {
$('body a, body button').attr('tabindex', -1);
check_add_item_val();
if (site.settings.set_focus != 1) {
    $('#add_item').focus();
}
    // If there is any item in localStorage
    if (localStorage.getItem('grn_items')) {
        loadItems();
    }

    // clear localStorage and reload
    $('#reset').click(function (e) {
            bootbox.confirm(lang.r_u_sure, function (result) {
                if (result) {
                   if (localStorage.getItem('grn_items')) {
						localStorage.removeItem('grn_items');
					}
					if (localStorage.getItem('pi_number')) {
						localStorage.removeItem('pi_number');
					}
					if (localStorage.getItem('pi_invoiceno')) {
						localStorage.removeItem('pi_invoiceno');
					}
					if (localStorage.getItem('pi_warehouse')) {
						localStorage.removeItem('pi_warehouse');
					}
					if (localStorage.getItem('pi_supplier')) {
						localStorage.removeItem('pi_supplier');
					}
					if (localStorage.getItem('pi_currency')) {
						localStorage.removeItem('pi_currency');
					}
					if (localStorage.getItem('pi_date')) {
					localStorage.removeItem('pi_date');
					}
					if (localStorage.getItem('delivery_address')) {
						localStorage.removeItem('delivery_address');
					}
					if (localStorage.getItem('supplier_address')) {
					localStorage.removeItem('supplier_address');
					}
					if (localStorage.getItem('invoice_amt')) {
						localStorage.removeItem('invoice_amt');
					}

                    $('#modal-loading').show();
                    location.reload();
                }
            });
    });

// save and load the fields in and/or from localStorage

   



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



/* ----------------------


    /* -----------------------
     * Edit Row Modal Hanlder
     ----------------------- */
     $(document).on('click', '.edit', function () {
        var row = $(this).closest('tr');
        var row_id = row.attr('id');
        item_id = row.attr('data-item-id');
        item = store_reqitems[item_id];
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
                /*$.each(tax_rates, function () {
                    if(this.id == pr_tax){
                        if (this.type == 1) {

                            if (store_reqitems[item_id].row.tax_method == 0) {
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
                });*/
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



    $(document).on('change', '#punit', function () {
        var row = $('#' + $('#row_id').val());
        var item_id = row.attr('data-item-id');
        var item = store_reqitems[item_id];
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
        if(item.units && unit != store_reqitems[item_id].row.base_unit) {
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
 

    /* -----------------------
     * Product option change
     ----------------------- */
     $(document).on('change', '#poption', function () {
        var row = $('#' + $('#row_id').val()), opt = $(this).val();
        var item_id = row.attr('data-item-id');
        var item = store_reqitems[item_id];
        var unit = $('#punit').val(), base_quantity = parseFloat($('#pquantity').val()), base_unit_price = item.row.base_unit_price;
        if(unit != store_reqitems[item_id].row.base_unit) {
            $.each(store_reqitems[item_id].units, function(){
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
            /*$.each(tax_rates, function () {
                if (this.id == mtax) {
                    mtax_rate = this;
                }
            });*/

            store_reqitems[mid] = {"id": mid, "item_id": mid, "label": mname + ' (' + mcode + ')', "row": {"id": mid, "code": mcode, "name": mname, "quantity": mqty, "price": unit_price, "unit_price": unit_price, "real_unit_price": unit_price, "tax_rate": mtax, "tax_method": 0, "qty": mqty, "type": "manual", "discount": mdiscount, "serial": "", "option":""}, "tax_rate": mtax_rate, "options":false};
            localStorage.setItem('store_reqitems', JSON.stringify(store_reqitems));
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
         
        }

        $('#mnet_price').text(formatMoney(unit_price));
        $('#mpro_tax').text(formatMoney(pr_tax_val));
    });

    /* --------------------------
     * Edit Row quantity Method
     -------------------------- */
    

   
 


});
/* -----------------------
 * Misc Actions
 ----------------------- */

// hellper function for customer if no localStorage value
function nsCustomer() {
    $('#store_reqcustomer').select2({
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
    $('#store_reqsupplier').select2({
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
    if (localStorage.getItem('grn_items')) {  
	
        total = 0;
        count = 1;
        an = 1;
        product_tax = 0;
        invoice_tax = 0;
        product_discount = 0;
        order_discount = 0;
        total_discount = 0;
        $("#grntable tbody").empty();
        grn_items = JSON.parse(localStorage.getItem('grn_items'));
        sortedItems = grn_items;
        $('#add_sale, #edit_sale').attr('disabled', false);
        var c = 1;
		
        $.each(sortedItems, function () {
									
            var item = this;
            var item_id = item.item_id;
            item.order = item.order ? item.order : new Date().getTime();
            var product_id = item.row.id, item_type = item.row.type, combo_items = item.combo_items, item_price = item.row.price, item_qty = item.row.qty, item_aqty = item.row.quantity, item_tax_method = item.row.tax_method, item_ds = item.row.discount, item_discount = 0, item_option = item.row.option, item_code = item.row.code, item_serial = item.row.serial, item_name = item.row.name.replace(/"/g, "&#034;").replace(/'/g, "&#039;");
	        var category_id  = item.row.category_id,
		    category_name    = item.row.category_name,
		    subcategory_id   = item.row.subcategory_id,
		    subcategory_name = item.row.subcategory_name,
		    brand_id         = item.row.brand_id,
		    brand_name       = item.row.brand_name;
            var unit_price   = item.row.real_unit_price;
            var product_unit = item.row.unit, base_quantity = item.row.base_quantity;
			var product_base_cost =item.row.cost_price;
			var product_base_price =item.row.selling_price;

            if(item.units && item.row.fup != 1 && product_unit != item.row.base_unit) {
                $.each(item.units, function(){
                    if (this.id == product_unit) {
                        base_quantity = formatDecimal(unitToBaseQty(item.row.qty, this), 4);
						//price calculation be reverse calculation
                         product_base_cost = formatDecimal((baseToUnitQty(item.row.cost_price, this)), 4);
						 product_base_price = formatDecimal((baseToUnitQty(item.row.selling_price, this)), 4);
                    }
                });
            }

            if(item.options !== false) {
                $.each(item.options, function () {
                    if(this.id == item.row.option && this.price != 0 && this.price != '' && this.price != null) {
                        item_price = unit_price+(parseFloat(this.price));
                        unit_price = item_price;
                    }
                });
            }
          
            var sel_opt = '';
            $.each(item.options, function () {
                if(this.id == item_option) {
                    sel_opt = this.name;
                }
            });
            var row_no = (new Date).getTime();
            var newTr = $('<tr id="row_' + row_no + '" class="row_' + item_id + '" data-item-id="' + item_id +'_'+item.store_id+'_'+item.row.category_id+'_'+item.row.subcategory_id+'_'+item.row.brand_id +'_'+item.row.option_id+'"></tr>');
            tr_html = '<td><input name="product_id[]" type="hidden" class="rid" value="' + product_id + '"><input name="product_type[]" type="hidden" class="rtype" value="' + item_type + '"><input name="product_code[]" type="hidden" class="rcode" value="' + item_code + '"><input name="product_name[]" type="hidden" class="rname" value="' + item_name + '"><input name="product_option[]" type="hidden" class="roption" value="' + item.row.option_id + '"><input type="hidden" name="store_id[]" value="'+item.row.store_id+'"><input type="hidden" name="cost_price[]" value="'+item.row.cost_price+'"><input type="hidden" name="selling_price[]" value="'+item.row.selling_price+'"><input type="hidden" name="landing_cost[]" value="'+item.row.landing_cost+'"><input type="hidden" name="tax_rate[]" value="'+item.row.tax_rate+'"><input type="hidden" name="invoice_id[]" value="'+item.row.invoice_id+'"><input type="hidden" name="batch[]" value="'+item.row.batch+'"><input type="hidden" name="expiry[]" value="'+item.row.expiry+'"><input type="hidden" name="expiry_type[]" value="'+item.row.expiry_type+'"><input type="hidden" name="invoice_date[]" value="'+item.row.invoice_date+'"><input type="hidden" name="tax_rate_id[]" value="'+item.row.tax_rate_id+'"><input type="hidden" name="unique_id[]" value="'+item.row.uniqueid+'"><span class="sname" id="name_' + row_no + '">' + item_code +' - '+ item_name +(sel_opt != '' ? ' ('+sel_opt+')' : '')+'</span> </td>';
	    
			if (category_name==null) {category_name ='';}if (category_id==null) {category_id =0;}
			 tr_html +='<td>'+
		     '<input name="category_id[]" type="hidden" class="cid" value="' + category_id + '">'+
		     '<input name="category_name[]" type="hidden" class="cname" readonly value="' + category_name + '">'+
		     '<span class="sname" id="name_' + row_no + '">' + category_name +'</span>'+
		     '</td>';
		     if (subcategory_name==null) {subcategory_name ='';}if (subcategory_id==null) {subcategory_id =0;}
	         tr_html +='<td>'+
		     '<input name="subcategory_id[]" type="hidden" class="scid" value="' + subcategory_id + '">'+
		     '<input name="subcategory_name[]" type="hidden" class="scname" readonly value="' + subcategory_name + '">'+
		     '<span class="sname" id="name_' + row_no + '">' + subcategory_name +'</span>'+
		     '</td>';
		    if (brand_name==null) {brand_name ='';}if (brand_id==null) {brand_id =0;}
	         tr_html +='<td>'+
		     '<input name="brand_id[]" type="hidden" class="bid" value="' + brand_id + '">'+
		     '<input name="brand_name[]" type="hidden" class="bname" readonly value="' + brand_name + '">'+
		     '<span class="sname" id="name_' + row_no + '">' + brand_name +'</span>'+
		     '</td>';
         
            tr_html += '<td><input class="form-control text-center "   type="hidden" name="pi_qty[]" value="' + formatQuantity2(item.row.pi_qty) + '" readonly data-id="' +row_no+ '" data-item="' +item_id+ '" id="quantity_' +row_no+ '" ><span>'+formatQuantity2(item.row.pi_qty)+'</span></td>';
			tr_html += '<td><input class="form-control text-center rquantity" tabindex="'+((site.settings.set_focus == 1) ? an : (an+1))+'" name="quantity[]" type="text"   value="' + formatQuantity2(item_qty) + '" data-id="' + row_no + '" data-item="' + item_id + '" id="quantity_' + row_no + '" onClick="this.select();"><input name="product_unit[]" type="hidden" class="runit" value="' + product_unit + '"><input name="product_base_quantity[]" type="hidden" class="rbase_quantity" value="' + base_quantity + '"><input name="quantity_balance[]" type="hidden"  value="' + item.row.quantity_balance + '"><input type="hidden" name="product_base_cost[]" value="'+product_base_cost+'"><input type="hidden" name="product_base_price[]" value="'+product_base_price+'"></td>';
            tr_html += '<td class="text-center"><i class="fa fa-trash-o tip pointer grn_itemDel" id="' + row_no + '" title="Remove" style="cursor:pointer; color:red;"></i></td>';
            newTr.html(tr_html);
            newTr.appendTo("#grntable");
            count += parseFloat(item_qty);
            an++;
			
            if (item_type == 'standard' && item.options !== false) {
                $.each(item.options, function () {
                    if(this.id == item_option && base_quantity > this.quantity) {
                        $('#row_' + row_no).addClass('danger');
                        if(site.settings.overselling != 1) { $('#add_sale, #edit_sale').attr('disabled', true); }
                    }
                });
            } else if(item_type == 'standard' && base_quantity > item_aqty) {
                $('#row_' + row_no).addClass('danger');
            } else if (item_type == 'combo') {
                if(combo_items === false) {
                    $('#row_' + row_no).addClass('danger');
                } else {
                    $.each(combo_items, function() {
                       if(parseFloat(this.quantity) < (parseFloat(this.qty)*base_quantity) && this.type == 'standard') {
                           $('#row_' + row_no).addClass('danger');
                       }
                   });
                }
            }
        });

        var col = 2;
		var tfoot = '';
     
        $('#grntable tfoot').html(tfoot);
        $('.titems').val((an - 1));
        $('.total_items').val((parseFloat(count)- 1));
    
        if (an > parseInt(site.settings.bc_fix) && parseInt(site.settings.bc_fix) > 0) {
            $("html, body").animate({scrollTop: $('#sticker').offset().top}, 500);
            $(window).scrollTop($(window).scrollTop() + 1);
        }
        set_page_focus();
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
		console.log(grn_items);
        grn_items[item_id].row.base_quantity = new_qty;
        if(grn_items[item_id].row.unit != grn_items[item_id].row.base_unit) {
            $.each(grn_items[item_id].units, function(){
                if (this.id == grn_items[item_id].row.unit) {
                    grn_items[item_id].row.base_quantity = unitToBaseQty(new_qty, this);  
                }
            });
        }
		var pi_qty = grn_items[item_id].row.pi_qty;
		var quantity_balance=0;
		quantity_balance=parseFloat(pi_qty)-parseFloat(new_qty);
        grn_items[item_id].row.qty = new_qty;
		grn_items[item_id].row.quantity_balance = quantity_balance;
        localStorage.setItem('grn_items', JSON.stringify(grn_items));
        loadItems();
    });
	
	if (typeof (Storage) === "undefined") {
    $(window).bind('beforeunload', function (e) {
        if (count > 1) {
            var message = "You will loss data!";
            return message;
        }
    });
}
$(document).ready(function(){
    $('#add_grn input[type="submit"]').click(function(e){
    $(window).unbind('beforeunload');
    $error = false;
  if($('#grntable tbody tr').length==0){
    bootbox.alert('Add Items');
    return false;
    }else{
    $(".rquantity").each(function(n,v){
	    if($(this).val()=='' || $(this).val()==0){
		$error=true;
		$(this).addClass('procurment-input-error');
	    }
    });
    }
    if ($error) {      
        e.preventDefault();
        $("html, body").animate({ scrollTop: $('.procurment-input-error:eq(0)').offset().top }, 1000);
        return false;   
    }else{
    $('#add_grn').submit();
    }
   });
})