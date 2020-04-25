$(document).ready(function() {
    $('body a, body button').attr('tabindex', -1);
    check_add_item_val();
    $(document).on('keypress', '.rquantity', function (e) {
        if (e.keyCode == 13) {
            $('#add_item').focus();
        }
    });
    $('#toogle-customer-read-attr').click(function () {
        var nst = $('#poscustomer').is('[readonly]') ? false : true;
        $('#poscustomer').select2("readonly", nst);
        return false;
    });
    $(".open-brands").click(function () {
        $('#brands-slider').toggle('slide', { direction: 'right' }, 700);
    });
    $(".open-category").click(function () {
        $('#category-slider').toggle('slide', { direction: 'right' }, 700);
    });
    $(".open-subcategory").click(function () {
        $('#subcategory-slider').toggle('slide', { direction: 'right' }, 700);
    });
    $(document).on('click', function(e){
        if (!$(e.target).is(".open-brands, .cat-child") && !$(e.target).parents("#brands-slider").size() && $('#brands-slider').is(':visible')) {
            $('#brands-slider').toggle('slide', { direction: 'right' }, 700);
        }
        if (!$(e.target).is(".open-category, .cat-child") && !$(e.target).parents("#category-slider").size() && $('#category-slider').is(':visible')) {
            $('#category-slider').toggle('slide', { direction: 'right' }, 700);
        }
        if (!$(e.target).is(".open-subcategory, .cat-child") && !$(e.target).parents("#subcategory-slider").size() && $('#subcategory-slider').is(':visible')) {
            $('#subcategory-slider').toggle('slide', { direction: 'right' }, 700);
        }
    });
    $('.po').popover({html: true, placement: 'right', trigger: 'click'}).popover();
    $('#inlineCalc').calculator({layout: ['_%+-CABS','_7_8_9_/','_4_5_6_*','_1_2_3_-','_0_._=_+'], showFormula:true});
    $('.calc').click(function(e) { e.stopPropagation();});
    $(document).on('click', '[data-toggle="ajax"]', function(e) {
        e.preventDefault();
        var href = $(this).attr('href');
        $.get(href, function( data ) {
            $("#myModal").html(data).modal();
        });
    });
  /*  $(document).on('click', '.sname', function(e) {
        var row = $(this).closest('tr');
        var itemid = row.find('.rid').val();
        $('#myModal').modal({remote: site.base_url + 'recipes/modal_view/' + itemid});
        $('#myModal').modal('show');
    });*/
});
$(document).ready(function () {

// Order level shipping and discount localStorage
/*sivan*/
/*if (posdiscount = localStorage.getItem('posdiscount')) {
    $('#posdiscount').val(posdiscount);
}*/
$(document).on('change', '#ppostax2', function () {
    localStorage.setItem('postax2', $(this).val());
    $('#postax2').val($(this).val());
});
localStorage.setItem('postax2', pos_settings.default_tax);

if (postax2 = localStorage.getItem('postax2')) {
    $('#postax2').val(postax2);
}

$(document).on('blur', '#sale_note', function () {
    localStorage.setItem('posnote', $(this).val());
    $('#sale_note').val($(this).val());
});

if (posnote = localStorage.getItem('posnote')) {
    $('#sale_note').val(posnote);
}

$(document).on('blur', '#staffnote', function () {
    localStorage.setItem('staffnote', $(this).val());
    $('#staffnote').val($(this).val());
});

if (staffnote = localStorage.getItem('staffnote')) {
    $('#staffnote').val(staffnote);
}

if (posshipping = localStorage.getItem('posshipping')) {
    $('#posshipping').val(posshipping);
    shipping = parseFloat(posshipping);
}
$("#pshipping").click(function(e) {
    e.preventDefault();
    shipping = $('#posshipping').val() ? $('#posshipping').val() : shipping;
    $('#shipping_input').val(shipping);
    $('#sModal').modal();
});
$('#sModal').on('shown.bs.modal', function() {
    $(this).find('#shipping_input').select().focus();
});
$(document).on('click', '#updateShipping', function() {
    var s = parseFloat($('#shipping_input').val() ? $('#shipping_input').val() : '0');
    if (is_numeric(s)) {
        $('#posshipping').val(s);
        localStorage.setItem('posshipping', s);
        shipping = s;
        loadItems();
        $('#sModal').modal('hide');
    } else {
        bootbox.alert(lang.unexpected_value);
    }
});


/* ----------------------
     * Order Discount Handler
     * ---------------------- */
     $("#ppdiscount").click(function(e) {
        e.preventDefault();
        /*var dval = $('#posdiscount').val() ? $('#posdiscount').val() : '0';*/
        var dval = localStorage.getItem('order_discount_input') ? localStorage.getItem('order_discount_input') : '0';
        
        localStorage.setItem("order_discount_input", dval);
        $("#order_discount_input").val(dval).trigger("change");
        
        // $("#order_discount_input").select2(dval)
        /*$('#order_discount_input').val(dval);*/
		
		$(".dcode").val('');
        $('#dsModal').modal();

     });
	/*  $(document).click(function(e){

   if($(e.target).closest('#dsModal').length != 0) return false;
  // $('#ppdiscount').hide();
}); */
	 $(document).ready(function() {
    $("#dsModal").modal({
        show: false,
        backdrop: 'static'
    });
    
  /*   $("#ppdiscount").click(function() {
       $("#dsModal").modal("show");             
    }); */
});
$(document).on('click', '#reset2', function () {    
   
	$("#dcode").val('');
	
   
});

$(document).on('click', '.reset2', function () {    
   
	$("#updateOrderDiscount").val('');
	
   
});


$(document).ready(function() {
    $("#myVaraintModal").modal({
        show: false,
        backdrop: 'static'
    });
    
  /*   $("#ppdiscount").click(function() {
       $("#dsModal").modal("show");             
    }); */
});
     $('#dsModal').on('shown.bs.modal', function() {
		
        $(this).find('#order_discount_input').select().focus();
        $('#order_discount_input').bind('keypress', function(e) {
			//alert('test');
			//var a = $('#order_discount_input').val();
		 //var b = $this.real_unit_price;
		// if (a>b){
			 //return false;
			// alert('Discount should not grater than total');	 
            if (e.keyCode == 13) {
                e.preventDefault();
                var ds = $('#order_discount_input').val();
                if (is_valid_discount(ds)) {
                    $('#posdiscount').val(ds);
                    localStorage.removeItem('posdiscount');
                    localStorage.setItem('posdiscount', ds);
                    loadItems();
                } else {
                    bootbox.alert(lang.unexpected_value);
                }
                $('#dsModal').modal('hide');
            }
			//}
		 //else{
	//alert('Discount should not grater than total');	 
		//return false;
		//}
		
        });
		 
		
		 
     });
     $(document).on('click', '#updateOrderDiscount', function() {
		var a = $('#order_discount_input').val();
		var b = $(this).real_unit_price;
        loadItems();
        var order_discount_input = $('#order_discount_input').val();
        localStorage.setItem('order_discount_input', order_discount_input);
        $('#dsModal').modal('hide');
		
	//alert('Discount should not grater than total');	 
		//return false;
		//}
 
        /*var ds = $('#order_discount_input').val() ? $('#order_discount_input').val() : '0';
        if (is_valid_discount(ds)) {
            $('#posdiscount').val(ds);
            localStorage.removeItem('posdiscount');
            localStorage.setItem('posdiscount', ds);
            loadItems();
        } else {
            bootbox.alert(lang.unexpected_value);
        }
        $('#dsModal').modal('hide');*/
	
     });

     /*$(document).on('click', '#updateOrderDiscount', function() {
        var ds = $('#order_discount_input').val() ? $('#order_discount_input').val() : '0';
        if (is_valid_discount(ds)) {
            $('#posdiscount').val(ds);
            localStorage.removeItem('posdiscount');
            localStorage.setItem('posdiscount', ds);
            loadItems();
        } else {
            bootbox.alert(lang.unexpected_value);
        }
        $('#dsModal').modal('hide');
     });*/
/* ----------------------
     * Order Tax Handler
     * ---------------------- */
     $("#pptax2").click(function(e) {
        e.preventDefault();
        var postax2 = localStorage.getItem('postax2');
        $('#order_tax_input').select2('val', postax2);
        $('#txModal').modal();
     });
     $('#txModal').on('shown.bs.modal', function() {
        $(this).find('#order_tax_input').select2('focus');
     });
     $('#txModal').on('hidden.bs.modal', function() {
        var ts = $('#order_tax_input').val();
        $('#postax2').val(ts);
        localStorage.setItem('postax2', ts);
        loadItems();
     });
     $(document).on('click', '#updateOrderTax', function () {
        var ts = $('#order_tax_input').val();
        $('#postax2').val(ts);
        localStorage.setItem('postax2', ts);
        loadItems();
        $('#txModal').modal('hide');
     });


     $(document).on('change', '.rserial', function () {
        var item_id = $(this).closest('tr').attr('data-item-id');
        positems[item_id].row.serial = $(this).val();
        localStorage.setItem('positems', JSON.stringify(positems));
     });

// If there is any item in localStorage
if (localStorage.getItem('positems')) {
    loadItems();
}

    // clear localStorage and reload
    $('#reset').click(function (e) {
        
        if (protect_delete == 1) {
            var boxd = bootbox.dialog({
                title: "<i class='fa fa-key'></i> Pin Code",
                message: '<input id="pos_pin" name="pos_pin" type="password" placeholder="Pin Code" class="form-control"> ',
                buttons: {
                    success: {
                        label: "<i class='fa fa-tick'></i> OK",
                        className: "btn-success verify_pin",
                        callback: function () {
                            var pos_pin = md5($('#pos_pin').val());
                            if(pos_pin == pos_settings.pin_code) {
							if (localStorage.getItem('order_discount_input')) {
									localStorage.removeItem('order_discount_input');
								}
								if (localStorage.getItem('input_discount')) {
								localStorage.removeItem('input_discount');
							}
							if (localStorage.getItem('offer_total_discount')) {
							localStorage.removeItem('offer_total_discount');
								}
                                if (localStorage.getItem('positems')) {
                                    localStorage.removeItem('positems');
                                }

                                if (localStorage.getItem('posdiscount')) {
                                    localStorage.removeItem('posdiscount');
                                }
                                if (localStorage.getItem('postax2')) {
                                    localStorage.removeItem('postax2');
                                }
                                if (localStorage.getItem('posshipping')) {
                                    localStorage.removeItem('posshipping');
                                }
                                if (localStorage.getItem('posref')) {
                                    localStorage.removeItem('posref');
                                }
                                if (localStorage.getItem('poswarehouse')) {
                                    localStorage.removeItem('poswarehouse');
                                }
                                
                                if (localStorage.getItem('posnote')) {
                                    localStorage.removeItem('posnote');
                                }
                                if (localStorage.getItem('posinnote')) {
                                    localStorage.removeItem('posinnote');
                                }
                                if (localStorage.getItem('poscustomer')) {
                                    localStorage.removeItem('poscustomer');
                                }
                                if (localStorage.getItem('poscurrency')) {
                                    localStorage.removeItem('poscurrency');
                                }
                                if (localStorage.getItem('posdate')) {
                                    localStorage.removeItem('posdate');
                                }
                                if (localStorage.getItem('posstatus')) {
                                    localStorage.removeItem('posstatus');
                                }
                                if (localStorage.getItem('posbiller')) {
                                    localStorage.removeItem('posbiller');
                                }

                                $('#modal-loading').show();
                                suspend_cancel();
                                // return false;
                                window.location.href = site.base_url+"qsr";

                            } else {
                                bootbox.alert('Wrong Pin Code');
                            }
                        }
                    }
                }
            });
        } else {
            bootbox.confirm(lang.r_u_sure, function (result) {
                if (result) {
                    if (localStorage.getItem('positems')) {
                        localStorage.removeItem('positems');
                    }
					if (localStorage.getItem('order_discount_input')) {
						localStorage.removeItem('order_discount_input');
						}
					if (localStorage.getItem('input_discount')) {
						localStorage.removeItem('input_discount');
						}
						if (localStorage.getItem('offer_total_discount')) {
						localStorage.removeItem('offer_total_discount');
						}
                    if (localStorage.getItem('posdiscount')) {
                        localStorage.removeItem('posdiscount');
                    }
                    if (localStorage.getItem('postax2')) {
                        localStorage.removeItem('postax2');
                    }
                    if (localStorage.getItem('posshipping')) {
                        localStorage.removeItem('posshipping');
                    }
                    if (localStorage.getItem('posref')) {
                        localStorage.removeItem('posref');
                    }
                    if (localStorage.getItem('poswarehouse')) {
                        localStorage.removeItem('poswarehouse');
                    }
                    
                    if (localStorage.getItem('posnote')) {
                        localStorage.removeItem('posnote');
                    }
                    if (localStorage.getItem('posinnote')) {
                        localStorage.removeItem('posinnote');
                    }
                    if (localStorage.getItem('poscustomer')) {
                        localStorage.removeItem('poscustomer');
                    }
                    if (localStorage.getItem('poscurrency')) {
                        localStorage.removeItem('poscurrency');
                    }
                    if (localStorage.getItem('posdate')) {
                        localStorage.removeItem('posdate');
                    }
                    if (localStorage.getItem('posstatus')) {
                        localStorage.removeItem('posstatus');
                    }
                    if (localStorage.getItem('posbiller')) {
                        localStorage.removeItem('posbiller');
                    }

                    $('#modal-loading').show();
                    suspend_cancel();
                    // return false;
                    window.location.href = site.base_url+"qsr";
                }
            });
        }
    });
    
    $('.dine_in').click(function (e) {
        if (localStorage.getItem('positems')) {
            localStorage.removeItem('positems');
        }
        if (localStorage.getItem('posdiscount')) {
            localStorage.removeItem('posdiscount');
        }
        if (localStorage.getItem('postax2')) {
            localStorage.removeItem('postax2');
        }
    });
    $('.take_away').click(function (e) {
        if (localStorage.getItem('positems')) {
            localStorage.removeItem('positems');
        }
        if (localStorage.getItem('posdiscount')) {
            localStorage.removeItem('posdiscount');
        }
        if (localStorage.getItem('postax2')) {
            localStorage.removeItem('postax2');
        }
    });
    $('.door_delivery').click(function (e) {
        if (localStorage.getItem('positems')) {
            localStorage.removeItem('positems');
        }
        if (localStorage.getItem('posdiscount')) {
            localStorage.removeItem('posdiscount');
        }
        if (localStorage.getItem('postax2')) {
            localStorage.removeItem('postax2');
        }
        if (localStorage.getItem('poscustomer')) {
            localStorage.removeItem('poscustomer');
        }
    });

// save and load the fields in and/or from localStorage

$('#poswarehouse').change(function (e) {
    localStorage.setItem('poswarehouse', $(this).val());
});



if (poswarehouse = localStorage.getItem('poswarehouse')) {
    $('#poswarehouse').select2('val', poswarehouse);
}
    //$(document).on('change', '#posnote', function (e) {
        $('#posnote').redactor('destroy');
        $('#posnote').redactor({
            buttons: ['formatting', '|', 'alignleft', 'aligncenter', 'alignright', 'justify', '|', 'bold', 'italic', 'underline', '|', 'unorderedlist', 'orderedlist', '|', 'link', '|', 'html'],
            formattingTags: ['p', 'pre', 'h3', 'h4'],
            minHeight: 100,
            changeCallback: function (e) {
                var v = this.get();
                localStorage.setItem('posnote', v);
            }
        });
        if (posnote = localStorage.getItem('posnote')) {
            $('#posnote').redactor('set', posnote);
        }

        $('#poscustomer').change(function (e) {
            localStorage.setItem('poscustomer', $(this).val());
        });


// prevent default action upon enter
$('body').not('textarea').bind('keypress', function (e) {
    if (e.keyCode == 13) {
        e.preventDefault();
        return false;
    }
});

// Order tax calculation
if (site.settings.tax2 != 0) {
    $('#postax2').change(function () {
        localStorage.setItem('postax2', $(this).val());
        loadItems();
        return;
    });
}

// Order discount calculation
var old_posdiscount;
$('#posdiscount').focus(function () {
    old_posdiscount = $(this).val();
}).change(function () {
    var new_discount = $(this).val() ? $(this).val() : '0';
    if (is_valid_discount(new_discount)) {
        localStorage.removeItem('posdiscount');
        localStorage.setItem('posdiscount', new_discount);
        loadItems();
        return;
    } else {
        $(this).val(old_posdiscount);
        bootbox.alert(lang.unexpected_value);
        return;
    }

});

    /* ----------------------
     * Delete Row Method
     * ---------------------- */
     var pwacc = false;
     $(document).on('click', '.posdel', function () {
        var row = $(this).closest('tr');
        var item_id = row.attr('data-item-id');
        if(protect_delete == 1) {
            var boxd = bootbox.dialog({
                title: "<i class='fa fa-key'></i> Pin Code",
                message: '<input id="pos_pin" name="pos_pin" type="password" placeholder="Pin Code" class="form-control"> ',
                buttons: {
                    success: {
                        label: "<i class='fa fa-tick'></i> OK",
                        className: "btn-success verify_pin",
                        callback: function () {
                            var pos_pin = md5($('#pos_pin').val());
                            if(pos_pin == pos_settings.pin_code) {
                                delete positems[item_id];
                                row.remove();
                                if(positems.hasOwnProperty(item_id)) { } else {
                                    localStorage.setItem('positems', JSON.stringify(positems));
                                    loadItems();
                                }
                            } else {
                                bootbox.alert('Wrong Pin Code');
                            }
                        }
                    }
                }
            });
            boxd.on("shown.bs.modal", function() {
                $( "#pos_pin" ).focus().keypress(function(e) {
                    if (e.keyCode == 13) {
                        e.preventDefault();
                        $('.verify_pin').trigger('click');
                        return false;
                    }
                });
            });
        } else {
            delete positems[item_id];
            row.remove();
            if(positems.hasOwnProperty(item_id)) { } else {
                localStorage.setItem('positems', JSON.stringify(positems));
                loadItems();
            }
        }
        return false;
     });

    /* -----------------------
     * Edit Row Modal Hanlder
     ----------------------- */
    $(document).on('click', '.edit', function () {
        
        var row = $(this).closest('tr');
        var row_id = row.attr('id');
        
        item_id = row.attr('data-item-id');
        item = positems[item_id];
        var qty = row.children().children('.rquantity').val(),
        recipe_option = row.children().children('.roption').val(),
        recipe_addon = row.children().children('.raddon').val(),
        unit_price = formatDecimal(row.children().children('.ruprice').val()),
        discount = row.children().children('.rdiscount').val();
        
        if(item.options !== false) {
            $.each(item.options, function () {
                if(this.id == item.row.option && this.price != 0 && this.price != '' && this.price != null) {
                    unit_price = parseFloat(item.row.real_unit_price)+parseFloat(this.price);
                }
            });
        }
        
        
        /*if(item.addons !== false) {
            $.each(item.addons, function () {
                alert(this.price);
                if(this.id == item.row.addon && this.price != 0 && this.price != '' && this.price != null) {
                    unit_price = parseFloat(item.row.real_unit_price)+parseFloat(this.price);
                }
                
            });
        }*/
        /*alert(item.row.addon);
        if(item.row.addon && item.addons !== false) {
            var addonPrice = 0;
            $.each(item.row.addon, function (key, value) {
                alert('a');
                var addonValue = $.grep(item.addons, function(v) {
                    return v.id === value;
                });
                if(addonValue[0] && addonValue[0].price != 0 && addonValue[0].price != '' && addonValue[0].price != null) {
                addonPrice += parseFloat(addonValue[0].price);
                    alert(addonPrice);
                }
                
            });
            alert(item.row.real_unit_price);
            unit_price = parseFloat(item.row.real_unit_price);
        }*/
        
        var real_unit_price = item.row.real_unit_price;
        var net_price = unit_price;
        $('#prModalLabel').text(item.row.code + ' - ' + item.row.name);
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

                            if (positems[item_id].row.tax_method == 0) {
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
        if (site.settings.recipe_serial !== 0) {
            $('#pserial').val(row.children().children('.rserial').val());
        }
        var opt = '<p style="margin: 12px 0 0 0;">n/a</p>';
        if(item.options !== false) {
            var o = 1;
            opt = $("<select id=\"poption\" name=\"poption\" class=\"form-control select\" />");
            $.each(item.options, function () {
                if(o == 1) {
                    if(recipe_option == '') { recipe_variant = this.id; } else { recipe_variant = recipe_option; }
                }
                $("<option />", {value: this.id, text: this.name}).appendTo(opt);
                o++;
            });
        } else {
            recipe_variant = 0;
        }
        
         var aon = '<p style="margin: 12px 0 0 0;">n/a</p>';
        if(item.addons !== false) {
            var o = 1;
            aon = $("<select id=\"poaddon\" name=\"poaddon\" class=\"form-control select\" multiple />");
            
            $.each(item.addons, function () {
                
                if(this.id == recipe_addon) {
                    $("<option />", {value: this.id, text: this.addon, selected:true}).appendTo(aon);
                } else {
                    $("<option />", {value: this.id, text: this.addon}).appendTo(aon);
                }
                
               
            });
        }else{
            recipe_variant_addon = 0
        }
        
        if (item.units !== false) {
            uopt = $("<select id=\"punit\" name=\"punit\" class=\"form-control select\" />");
            $.each(item.units, function () {
                if(this.id == item.row.unit) {
                    $("<option />", {value: this.id, text: this.name, selected:true}).appendTo(uopt);
                } else {
                    $("<option />", {value: this.id, text: this.name}).appendTo(uopt);
                }
            });
        } else {
            uopt = '<p style="margin: 12px 0 0 0;">n/a</p>';
        }

        $('#poptions-div').html(opt);
        $('#poaddon-div').html(aon);
        $('#punits-div').html(uopt);
        $('select.select').select2({minimumResultsForSearch: 7});
        $('#pquantity').val(qty);
        $('#old_qty').val(qty);
        $('#pprice').val(unit_price);
        $('#punit_price').val(formatDecimal(parseFloat(unit_price)));
        $('#poption').select2('val', item.row.option);
        $('#poaddon').select2('val', item.row.addon);
        $('#old_price').val(unit_price);
        $('#row_id').val(row_id);
        $('#item_id').val(item_id);
        $('#pserial').val(row.children().children('.rserial').val());
        $('#pdiscount').val(discount);
        $('#net_price').text(formatMoney(net_price));
        $('#pro_tax').text(formatMoney(pr_tax_val));
        $('#prModal').appendTo("body").modal('show');

    });

    $(document).on('click', '.comment', function () {
        var row = $(this).closest('tr');
        var row_id = row.attr('id');
        item_id = row.attr('data-item-id');
        item = positems[item_id];
        $('#irow_id').val(row_id);
        $('#icomment').val(item.row.comment);
        $('#iordered').val(item.row.ordered);
        $('#iordered').select2('val', item.row.ordered);
        $('#cmModalLabel').text(item.row.code + ' - ' + item.row.name);
        $('#cmModal').appendTo("body").modal('show');
    });

    $(document).on('click', '#editComment', function () {
        var row = $('#' + $('#irow_id').val());
        var item_id = row.attr('data-item-id');
        positems[item_id].row.order = parseFloat($('#iorders').val()),
        positems[item_id].row.comment = $('#icomment').val() ? $('#icomment').val() : '';
        localStorage.setItem('positems', JSON.stringify(positems));
        $('#cmModal').modal('hide');
        loadItems();
        return;
    });

    $('#prModal').on('shown.bs.modal', function (e) {
        if($('#poption').select2('val') != '') {
            $('#poption').select2('val', recipe_variant);
            recipe_variant = 0;
        }
         if($('#poaddon').select2('val') != '') {
            $('#poaddon').select2('val', recipe_variant_addon);
            recipe_variant_addon = 0;
        }
    });

    $(document).on('change', '#pprice, #ptax, #pdiscount', function () {
        var row = $('#' + $('#row_id').val());
        var item_id = row.attr('data-item-id');
        var unit_price = parseFloat($('#pprice').val());
        var item = positems[item_id];
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

        $('#net_price').text(formatMoney(unit_price));
        $('#pro_tax').text(formatMoney(pr_tax_val));
    });

    $(document).on('change', '#punit', function () {
        var row = $('#' + $('#row_id').val());
        var item_id = row.attr('data-item-id');
        var item = positems[item_id];
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
        var aon = $('#poaddon').val();
        if(item.addons !== false) {
            $.each(item.addons, function () {
                if(this.id == aon && this.price != 0 && this.price != '' && this.price != null) {
                    aprice = parseFloat(this.price);
                }
            });
        }
        if(item.units && unit != positems[item_id].row.base_unit) {
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
        if(item.addons !== false) {
            var aon = $('#poaddon').val();
            $.each(item.addons, function () {
                
                if(this.id == aon && this.price != 0 && this.price != '' && this.price != null) {
                    price = price-parseFloat(this.price);
                }
            });
        }
        if (site.settings.recipe_discount == 1 && $('#pdiscount').val()) {
            if(!is_valid_discount($('#pdiscount').val())) {
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
        if(unit != positems[item_id].row.base_unit) {
            $.each(positems[item_id].units, function(){
                if (this.id == unit) {
                    base_quantity = unitToBaseQty($('#pquantity').val(), this);
                }
            });
        }
        positems[item_id].row.fup = 1,
        positems[item_id].row.qty = parseFloat($('#pquantity').val()),
        positems[item_id].row.base_quantity = parseFloat(base_quantity),
        positems[item_id].row.real_unit_price = price,
        positems[item_id].row.unit = unit,
        positems[item_id].row.tax_rate = new_pr_tax,
        positems[item_id].tax_rate = new_pr_tax_rate,
        positems[item_id].row.discount = $('#pdiscount').val() ? $('#pdiscount').val() : '',
        positems[item_id].row.option = $('#poption').val() ? $('#poption').val() : '',
        positems[item_id].row.addon = $('#poaddon').val() ? $('#poaddon').val() : '',
        positems[item_id].row.serial = $('#pserial').val();
        localStorage.setItem('positems', JSON.stringify(positems));
        $('#prModal').modal('hide');

        loadItems();
        return;
    });

    /* -----------------------
     * recipe option change
     ----------------------- */
    $(document).on('change', '#poption', function () {
        var row = $('#' + $('#row_id').val()), opt = $(this).val();
        var item_id = row.attr('data-item-id');
        var item = positems[item_id];
        var unit = $('#punit').val(), base_quantity = parseFloat($('#pquantity').val()), base_unit_price = item.row.base_unit_price;
        if(unit != positems[item_id].row.base_unit) {
            $.each(positems[item_id].units, function(){
                if (this.id == unit) {
                    base_unit_price = formatDecimal((parseFloat(item.row.base_unit_price)*(unitToBaseQty(1, this))), 4)
                    base_quantity = unitToBaseQty($('#pquantity').val(), this);
                }
            });
        }
        $('#pprice').val(parseFloat(base_unit_price)).trigger('change');
        if(item.options !== false) {
            $.each(item.options, function () {
                if(this.id == opt && this.price != 0 && this.price != '' && this.price != null) {
                    $('#pprice').val(parseFloat(base_unit_price)+(parseFloat(this.price))).trigger('change');
                }
            });
        }
    });
    
    $(document).on('change', '#poaddon', function () {
        
        var row = $('#' + $('#row_id').val()), aon = $(this).val();
        
        
        var item_id = row.attr('data-item-id');
        var item = positems[item_id];
        var unit = $('#punit').val(), base_quantity = parseFloat($('#pquantity').val()), base_unit_price = item.row.base_unit_price;
        if(unit != positems[item_id].row.base_unit) {
            $.each(positems[item_id].units, function(){
                if (this.id == unit) {
                    base_unit_price = formatDecimal((parseFloat(item.row.base_unit_price)*(unitToBaseQty(1, this))), 4)
                    base_quantity = unitToBaseQty($('#pquantity').val(), this);
                }
            });
        }
        $('#pprice').val(parseFloat(base_unit_price)).trigger('change');
        if(aon && item.addons !== false) {
            var addonPrice = 0;
            $.each(aon, function (key, value) {
                
                var addonValue = $.grep(item.addons, function(v) {
                    return v.id === value;
                });
                if(addonValue[0] && addonValue[0].price != 0 && addonValue[0].price != '' && addonValue[0].price != null) {
                    addonPrice += parseFloat(addonValue[0].price);
                }
                
            });
            $('#pprice').val(parseFloat(base_unit_price)+parseFloat(addonPrice)).trigger('change');
        }
        
      
    });


     /* ------------------------------
     * Sell Gift Card modal
     ------------------------------- */
     $(document).on('click', '#sellGiftCard', function (e) {
        if (count == 1) {
            positems = {};
            if ($('#poswarehouse').val() && $('#poscustomer').val()) {
                $('#poscustomer').select2("readonly", true);
                $('#poswarehouse').select2("readonly", true);
                $('#postable_list').select2("readonly", true);
                $('#posorder_type').select2("readonly", true);
            } else {
                bootbox.alert(lang.select_above);
                item = null;
                return false;
            }
        }
        $('.gcerror-con').hide();
        $('#gcModal').appendTo("body").modal('show');
        return false;
     });

     $('#gccustomer').select2({
        minimumInputLength: 1,
        ajax: {
            url: site.base_url+"customers/suggestions",
            dataType: 'json',
            quietMillis: 15,
            data: function (term, page) {
                return {
                    term: term,
                    limit: 10
                };
            },
            results: function (data, page) {
                if(data.results != null) {
                    return { results: data.results };
                } else {
                    return { results: [{id: '', text: 'No Match Found'}]};
                }
            }
        }
     });

     $('#genNo').click(function(){
        var no = generateCardNo();
        $(this).parent().parent('.input-group').children('input').val(no);
        return false;
     });
     $('.date').datetimepicker({format: site.dateFormats.js_sdate, fontAwesome: true, language: 'sma', todayBtn: 1, autoclose: 1, minView: 2 });
     $(document).on('click', '#addGiftCard', function (e) {
        var mid = (new Date).getTime(),
        gccode = $('#gccard_no').val(),
        gcname = $('#gcname').val(),
        gcvalue = $('#gcvalue').val(),
        gccustomer = $('#gccustomer').val(),
        gcexpiry = $('#gcexpiry').val() ? $('#gcexpiry').val() : '',
        gcprice = parseFloat($('#gcprice').val());
        if(gccode == '' || gcvalue == '' || gcprice == '' || gcvalue == 0 || gcprice == 0) {
            $('#gcerror').text('Please fill the required fields');
            $('.gcerror-con').show();
            return false;
        }

        var gc_data = new Array();
        gc_data[0] = gccode;
        gc_data[1] = gcvalue;
        gc_data[2] = gccustomer;
        gc_data[3] = gcexpiry;
        //if (typeof positems === "undefined") {
        //    var positems = {};
        //}

        $.ajax({
            type: 'get',
            url: site.base_url+'sales/sell_gift_card',
            dataType: "json",
            data: { gcdata: gc_data },
            success: function (data) {
                if(data.result === 'success') {
                    positems[mid] = {"id": mid, "item_id": mid, "label": gcname + ' (' + gccode + ')', "row": {"id": mid, "code": gccode, "name": gcname, "quantity": 1, "base_quantity": 1, "price": gcprice, "real_unit_price": gcprice, "tax_rate": 0, "qty": 1, "type": "manual", "discount": "0", "serial": "", "option":""}, "tax_rate": false, "options":false, "addons":false, "units":false};
                    localStorage.setItem('positems', JSON.stringify(positems));
                    loadItems();
                    $('#gcModal').modal('hide');
                    $('#gccard_no').val('');
                    $('#gcvalue').val('');
                    $('#gcexpiry').val('');
                    $('#gcprice').val('');
                } else {
                    $('#gcerror').text(data.message);
                    $('.gcerror-con').show();
                }
            }
        });
        return false;
    });

    /* ------------------------------
     * Show manual item addition modal
     ------------------------------- */
     $(document).on('click', '#addManually', function (e) {
        if (count == 1) {
            positems = {};
            if ($('#poswarehouse').val() && $('#poscustomer').val()) {
                $('#poscustomer').select2("readonly", true);
                $('#poswarehouse').select2("readonly", true);
                $('#postable_list').select2("readonly", true);
                $('#posorder_type').select2("readonly", true);
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

            positems[mid] = {"id": mid, "item_id": mid, "label": mname + ' (' + mcode + ')', "row": {"id": mid, "code": mcode, "name": mname, "quantity": mqty, "base_quantity": mqty, "price": unit_price, "unit_price": unit_price, "real_unit_price": unit_price, "tax_rate": mtax, "tax_method": 0, "qty": mqty, "type": "manual", "discount": mdiscount, "serial": "", "option":""}, "tax_rate": mtax_rate, 'units': false, "options":false, "addons":false};
            localStorage.setItem('positems', JSON.stringify(positems));
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

    $(document).on('change', '#mprice, #mtax, #mdiscount,#mquantity', function () {
        var unit_price = parseFloat($('#mprice').val());
        var ds = $('#mdiscount').val() ? $('#mdiscount').val() : '0';
        var dq = $('#mquantity').val() ? $('#mquantity').val() : '0';
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

        $('#mnet_price').text(formatMoney(unit_price*dq));
        // $('#mpro_tax').text(formatMoney(pr_tax_val));
    });

    /* --------------------------
     * Edit Row Quantity Method
    --------------------------- */
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
        var total_quantity = 0;
        var y_quantity = $(this).parent().parent().find('.rbuy_quantity').val();
        var b_quantity = $(this).parent().parent().find('.rget_quantity').val();
        var x_quantity = parseFloat($(this).val());
        
        var new_qty = parseFloat($(this).val()),
        item_id = row.attr('data-item-id');
        positems[item_id].row.base_quantity = new_qty;
        if(positems[item_id].row.unit != positems[item_id].row.base_unit) {
            $.each(positems[item_id].units, function(){
                if (this.id == positems[item_id].row.unit) {
                    positems[item_id].row.base_quantity = unitToBaseQty(new_qty, this);
                }
            });
        }
        
        total_quantity = x_quantity % y_quantity;
        x_quantity = (x_quantity - total_quantity) / y_quantity;
        positems[item_id].row.qty = new_qty;
        positems[item_id].row.total_get_quantity = x_quantity * b_quantity;
        
        localStorage.setItem('positems', JSON.stringify(positems));
        loadItems();
    });

/*manual discount start*/
    var manualdistext = 0;
    $(document).on("focus", '.manual_dis_text', function () {
		
        manualdistext = $(this).val();
    }).on("change", '.manual_dis_text', function () {
		
			 
        var row = $(this).closest('tr');
        manualdistext = manualdistext ? manualdistext : 0;
       item_id = row.attr('data-item-id');
        positems[item_id].row.manual_item_ds_val = manualdistext;        
        localStorage.setItem('positems', JSON.stringify(positems));        
        loadItems();
		 
    });
    

  $(document).on("focus", '.manual_dis_text', function (e) {
    var element = $(this)[0];
    var len = $(this).val().length * 2;
        element.setSelectionRange(len, len);
		if($(this).val() <=0){
			$(this).val('');
		} 
    }).on("click", '.manual_dis_text', function (e) {
        $(this).val($(this).val());
        $(this).focus();
        // element.scrollTop = 9999;
    });
    /*manual discount end*/

// end ready function
});

/* -----------------------
 * Load all items
 ----------------------- */

//localStorage.clear();
function loadItems() {
console.log($("#order_discount_input").val());
    if (localStorage.getItem('positems')) {
        total = 0;
        manu_item_total = 0;
        count = 1;
        an = 1;
        recipe_tax = 0;
        invoice_tax = 0;
        recipe_discount = 0;
        order_discount = 0;
        total_discount = 0;
        order_data = {};
        bill_data = {};
		tax_rate_per=0;

        $("#posTable tbody").empty();
        var time = ((new Date).getTime())/1000;
        if (pos_settings.remote_printing != 1) {
            store_name = (biller && biller.company != '-' ? biller.company : biller.name);
            order_data.store_name = store_name;
            bill_data.store_name = store_name;
            order_data.header = "\n"+lang.order+"\n\n";
            bill_data.header = "\n"+lang.bill+"\n\n";

            var pos_customer = 'C: '+$('#select2-chosen-1').text()+ "\n";
            var hr = 'R: '+$('#reference_note').val()+ "\n";
            var user = 'U: '+username+ "\n";
            var pos_curr_time = 'T: '+date(site.dateFormats.php_ldate, time)+ "\n";
            var ob_info = pos_customer+hr+user+pos_curr_time+ "\n";
            order_data.info = ob_info;
            bill_data.info = ob_info;
            var o_items = '';
            var b_items = '';

        } else {
            $("#order_span").empty(); $("#bill_span").empty();
            var styles = '<style>table, th, td { border-collapse:collapse; border-bottom: 1px solid #CCC; } .no-border { border: 0; } .bold { font-weight: bold; }</style>';
            var pos_head1 = '<span style="text-align:center;"><h3>'+site.settings.site_name+'</h3><h4>';
            var pos_head2 = '</h4><p class="text-left">C: '+$('#select2-chosen-1').text()+'<br>R: '+$('#reference_note').val()+'<br>U: '+username+'<br>T: '+date(site.dateFormats.php_ldate, time)+'</p></span>';
            $("#order_span").prepend(styles + pos_head1+' '+lang.order+' '+pos_head2);
            $("#bill_span").prepend(styles + pos_head1+' '+lang.bill+' '+pos_head2);
            $("#order-table").empty(); $("#bill-table").empty();
        }
        positems = JSON.parse(localStorage.getItem('positems'));
        if (pos_settings.item_order == 1) {
            sortedItems = _.sortBy(positems, function(o) { return [parseInt(o.category), parseInt(o.order)]; });
        } else if (site.settings.item_addition == 1) {
            sortedItems = _.sortBy(positems, function(o) { return [parseInt(o.order)]; });
        } else {
            sortedItems = positems;
        }
        var category = 0, print_cate = false;
        // var itn = parseInt(Object.keys(sortedItems).length);
        $.each(sortedItems, function () {

            var item = this;
            var item_id = site.settings.item_addition == 1 ? item.item_id : item.id;
            var manual_item_ds_val = item.row.manual_item_ds_val ? item.row.manual_item_ds_val : 0; //typinf text
            var manual_item_ds = item.row.manual_item_ds ? item.row.manual_item_ds : 0; //calculated text 

            positems[item_id] = item;
            item.order = item.order ? item.order : new Date().getTime();    

            var recipe_id = item.row.id, item_type = item.row.type, combo_items = item.combo_items, item_price = item.row.price, item_qty = item.row.qty, item_aqty = item.row.quantity, item_tax_method = item.row.tax_method, item_ds = item.row.discount, item_discount = 0, item_option = item.row.option, item_addon = item.row.addon, item_code = item.row.code, kitchen_type_id = item.row.kitchens_id, item_serial = item.row.serial, item_name = item.row.name.replace(/"/g, "&#034;").replace(/'/g, "&#039;");
            var recipe_unit = item.row.unit, base_quantity = item.row.base_quantity;
            var unit_price = item.row.real_unit_price;
            var item_comment = item.row.comment ? item.row.comment : '';
            var item_ordered = item.row.ordered ? item.row.ordered : 0;
            var buy_id = item.row.buy_id ? item.row.buy_id : 0;
            var buy_quantity = item.row.buy_quantity ? item.row.buy_quantity : 0;
            var get_item = item.row.get_item ? item.row.get_item : 0;
            var get_quantity = item.row.get_quantity ? item.row.get_quantity : 0;
            var total_get_quantity = item.row.total_get_quantity ? item.row.total_get_quantity : 0;
            var free_recipe = item.row.free_recipe ? item.row.free_recipe : '';
		
            
            if(item.units && item.row.fup != 1 && recipe_unit != item.row.base_unit) {
                $.each(item.units, function() {
					console.log(this);
                    if (this.id == recipe_unit) {
                        base_quantity = formatDecimal(unitToBaseQty(item.row.qty, this), 4);
                        unit_price = formatDecimal((parseFloat(item.row.base_unit_price)*(unitToBaseQty(1, this.operator))), 4);
						
                    }
                });
            }
            if(item.options !== false) {
                $.each(item.options, function () {
                    if(this.id == item.row.option && this.price != 0 && this.price != '' && this.price != null) {
                        item_price = parseFloat(unit_price)+(parseFloat(this.price));
                        unit_price = item_price;
						
                    }
                });
            }
            if(item.addons !== false) {
                $.each(item.addons, function () {
                    if(this.id == item.row.addon && this.price != 0 && this.price != '' && this.price != null) {
                        item_price = parseFloat(unit_price)+(parseFloat(this.price));
                        unit_price = item_price;
                    }
                });
            }

            var ds = item_ds ? item_ds : '0';
            if (ds.indexOf("%") !== -1) {
                var pds = ds.split("%");
                if (!isNaN(pds[0])) {
                    item_discount = parseFloat((parseFloat(((unit_price) * parseFloat(pds[0])) / 100)), 4);
                } else {
                    item_discount = parseFloat(ds);
                }
            } else {
                 item_discount = parseFloat(ds);
            }

            var manualds = manual_item_ds_val;
            
            if(manualds != 0){
            if (manualds.indexOf("%") !== -1) {
                var manualpds = manualds.split("%");
                if (!isNaN(manualpds[0])) {
                    manual_item_ds = formatDecimal((parseFloat(((unit_price*item_qty) * parseFloat(manualpds[0])) / 100)), 4);
                } else {
                    manual_item_ds = formatDecimal(manualds);
                }
            } else {
                 manual_item_ds = formatDecimal(manualds);
            } } else{
                manual_item_ds = formatDecimal(manualds);
            }

            recipe_discount += parseFloat(item_discount * item_qty);
            unit_price = formatDecimal(unit_price-item_discount);
			
            var pr_tax = item.tax_rate;
            var pr_tax_val = 0, pr_tax_rate = 0;
            if (site.settings.tax1 == 1) {
                if (pr_tax !== false && pr_tax != 0) {
                    if (pr_tax.type == 1) {

                        if (item_tax_method == '0') {
                            pr_tax_val = formatDecimal(((unit_price) * parseFloat(pr_tax.rate)) / (100 + parseFloat(pr_tax.rate)), 4);
                            pr_tax_rate = formatDecimal(pr_tax.rate) + '%';
                        } else {
                            pr_tax_val = formatDecimal(((unit_price) * parseFloat(pr_tax.rate)) / 100, 4);
                            pr_tax_rate = formatDecimal(pr_tax.rate) + '%';
                        }

                    } else if (pr_tax.type == 2) {

                        pr_tax_val = formatDecimal(pr_tax.rate);
                        pr_tax_rate = pr_tax.rate;

                    }
                    recipe_tax += pr_tax_val * item_qty;
                }
            }
            //item_price = item_tax_method == 0 ? formatDecimal((unit_price-pr_tax_val), 4) : formatDecimal(unit_price);
            /*item_price = formatDecimal(unit_price);
            unit_price = formatDecimal((unit_price+item_discount), 4);*/

            item_price = formatDecimal(unit_price);
            unit_price = formatDecimal((unit_price+item_discount+manual_item_ds), 4);
            
            var sel_opt = '';
            $.each(item.options, function () {
                if(this.id == item_option) {
                    sel_opt = this.name;
                }
            });
             var sel_aon = '';
            $.each(item.addons, function () {
                if(this.id == item_addon) {
                    sel_aon = this.addon;
                }
            });

            if (pos_settings.item_order == 1 && category != item.row.category_id) {
                category = item.row.category_id;
                print_cate = true;
                var newTh = $('<tr></tr>');
                newTh.html('<td colspan="100%"><strong>'+item.row.category_name+'</strong></td>');
                newTh.appendTo("#posTable");
            } else {
                print_cate = false;
            }

            var row_no = (new Date).getTime();
            var newTr = $('<tr id="row_' + row_no + '" class="row_' + item_id + '" data-item-id="' + item_id + '"></tr>');
            if(site.settings.user_language == 'khmer'){
                if(item_khmer_name != ''){
                    recipe_name = item_khmer_name;
                }else{
                    recipe_name = item_name;
                }
            }else{
                recipe_name = item_name;
            }

           /* tr_html = '<td><input name="buy_id[]" type="hidden" class="rbuy_id" value="' + buy_id + '"><input name="buy_quantity[]" type="hidden" class="rbuy_quantity" value="' + buy_quantity + '"><input name="get_item[]" type="hidden" class="rget_item" value="' + get_item + '"><input name="get_quantity[]" type="hidden" class="rget_quantity" value="' + get_quantity + '"><input name="total_get_quantity[]" type="hidden" class="rtotal_get_quantity" value="' + total_get_quantity + '"><input name="recipe_id[]" type="hidden" class="rid" value="' + recipe_id + '"><input name="recipe_type[]" type="hidden" class="rtype" value="' + item_type + '"><input name="kitchen_type_id[]" type="hidden" class="rkitchen_type" value="' + kitchen_type_id + '"><input name="recipe_code[]" type="hidden" class="rcode" value="' + item_code + '"><input name="recipe_name[]" type="hidden" class="rname" value="' + item_name + '"><input name="recipe_option[]" type="hidden" class="roption" value="' + item_option + '"><input name="recipe_addon[]" type="hidden" class="raddon" value="' + item_addon + '"><input name="recipe_comment[]" type="hidden" class="rcomment" value="' + item_comment + '"><span class="sname" id="name_' + row_no + '">' + item_name +(free_recipe != '' ? ' ('+free_recipe+':'+get_quantity+')' : '')+'</span><span class="lb"></span><i class="pull-right fa fa-edit fa-bx tip pointer edit" id="' + row_no + '" data-item="' + item_id + '" title="Edit" style="cursor:pointer;"></i><i class="pull-right fa fa-comment fa-bx'+(item_comment != '' ? '' :'-o')+' tip pointer comment" id="' + row_no + '" data-item="' + item_id + '" title="Comment" style="cursor:pointer;margin-right:5px;"></i></td>';*/

            $variant_name ='';$variant_label='';$varint_id=0;
                if (item.row.variant!=null) {
                    $variant_name = item.row.variant_id+'|'+item.row.variant;
                    $varint_id = item.row.variant_id;
                    $variant_label = '<p class="pos-variant-name">['+item.row.variant+']</p>';;
                }

            /*tr_html = '<td><input name="buy_id[]" type="hidden" class="rbuy_id" value="' + buy_id + '"><input name="buy_quantity[]" type="hidden" class="rbuy_quantity" value="' + buy_quantity + '"><input name="get_item[]" type="hidden" class="rget_item" value="' + get_item + '"><input name="get_quantity[]" type="hidden" class="rget_quantity" value="' + get_quantity + '"><input name="total_get_quantity[]" type="hidden" class="rtotal_get_quantity" value="' + total_get_quantity + '"><input name="recipe_id[]" type="hidden" class="rid" value="' + recipe_id + '"><input name="recipe_type[]" type="hidden" class="rtype" value="' + item_type + '"><input name="kitchen_type_id[]" type="hidden" class="rkitchen_type" value="' + kitchen_type_id + '"><input name="recipe_code[]" type="hidden" class="rcode" value="' + item_code + '"><input name="recipe_name[]" type="hidden" class="rname" value="' + item_name + '"><input name="recipe_option[]" type="hidden" class="roption" value="' + item_option + '"><input name="recipe_addon[]" type="hidden" class="raddon" value="' + item_addon + '"><input name="recipe_comment[]" type="hidden" class="rcomment" value="' + item_comment + '"><span class="sname" id="name_' + row_no + '">' + recipe_name +$variant_label +(free_recipe != '' ? ' ('+free_recipe+':'+get_quantity+')' : '')+'</span><span class="lb"></span><div class="row"><div class="col-xs-4"><div class="text-center"><span class="label label-primary fa-bx'+(item_comment != '' ? '' :'-o')+' tip pointer edit" id="' + row_no + '" data-item="' + item_id + '"  title="Edit" style="cursor:pointer;">Addon</span></div></div><div class="col-xs-4"><div class="text-center"><span class="label label-warning fa-bx'+(item_comment != '' ? '' :'-o')+' tip pointer comment" id="' + row_no + '" data-item="' + item_id + '" title="Comment" style="cursor:pointer;margin-right:5px;">Comment</span></div></div></div></td>';*/

            tr_html = '<td><input name="buy_id[]" type="hidden" class="rbuy_id" value="' + buy_id + '"><input name="buy_quantity[]" type="hidden" class="rbuy_quantity" value="' + buy_quantity + '"><input name="get_item[]" type="hidden" class="rget_item" value="' + get_item + '"><input name="get_quantity[]" type="hidden" class="rget_quantity" value="' + get_quantity + '"><input name="total_get_quantity[]" type="hidden" class="rtotal_get_quantity" value="' + total_get_quantity + '"><input name="recipe_id[]" type="hidden" class="rid" value="' + recipe_id + '"><input name="recipe_type[]" type="hidden" class="rtype" value="' + item_type + '"><input name="kitchen_type_id[]" type="hidden" class="rkitchen_type" value="' + kitchen_type_id + '"><input name="recipe_code[]" type="hidden" class="rcode" value="' + item_code + '"><input name="recipe_name[]" type="hidden" class="rname" value="' + item_name + '"><input name="recipe_option[]" type="hidden" class="roption" value="' + item_option + '"><span class="sname" id="name_' + row_no + '">' + recipe_name +$variant_label +(free_recipe != '' ? ' ('+free_recipe+':'+get_quantity+')' : '')+'</span><span class="lb"></span><input name="recipe_addon[]" type="hidden" class="raddon" value="' + item_addon + '"><input name="recipe_comment[]" type="hidden" class="rcomment" value="' + item_comment + '">';
            tr_html +='<div class="row">';
            if (pos_settings.item_addon == 1) {
                tr_html +='<div class="col-xs-4"><div class="text-center"><span class="label label-primary fa-bx'+(item_comment != '' ? '' :'-o')+' tip pointer edit" id="' + row_no + '" data-item="' + item_id + '"  title="Edit" style="cursor:pointer;">Addon</span></div></div>';
            }

            if (pos_settings.item_comment == 1) {

                tr_html +='<div class="col-xs-4"><div class="text-center"><span class="label label-warning fa-bx'+(item_comment != '' ? '' :'-o')+' tip pointer comment" id="' + row_no + '" data-item="' + item_id + '" title="Comment" style="cursor:pointer;margin-right:5px;">Comment</span></div></div>';
            }

            tr_html +='</div></td>';



            tr_html += '<td class="text-right">';

            if (site.settings.recipe_serial == 1) {
                tr_html += '<input class="form-control input-sm rserial" name="serial[]" type="hidden" id="serial_' + row_no + '" value="'+item_serial+'">';
            }
            /*if (site.settings.recipe_discount == 1) {
                tr_html += '<input class="form-control input-sm rdiscount" name="recipe_discount[]" type="hidden" id="discount_' + row_no + '" value="' + item_ds + '">';
            }*/

            tr_html += '<input class="form-control input-sm rdiscount" name="recipe_discount[]" type="hidden" id="discount_' + row_no + '" value="' + item_ds + '">';

            if (site.settings.tax1 == 1) {
                tr_html += '<input class="form-control input-sm text-right rrecipe_tax" name="recipe_tax[]" type="hidden" id="recipe_tax_' + row_no + '" value="' + pr_tax.id + '"><input type="hidden" class="srecipe_tax" id="srecipe_tax_' + row_no + '" value="' + formatMoney(pr_tax_val * item_qty) + '">';
            }            
            tr_html += '<input type="hidden" name="variant[]" value="'+$variant_name+'"><input type="hidden" name="variant_id[]" class="variant_id" id="variant_id_' + row_no + '"   value="'+$varint_id+'"><input class="rprice" name="net_price[]" type="hidden" id="price_' + row_no + '" value="' + item.row.real_unit_price + '"><input class="ruprice" name="unit_price[]" type="hidden" value="' + unit_price + '"><input class="realuprice" name="real_unit_price[]" type="hidden" value="' + item.row.real_unit_price + '"><span class="text-right sprice" id="sprice_' + row_no + '">' + formatMoney(parseFloat(item.row.real_unit_price)) + '</span></td>';


            if (site.settings.manual_item_discount == 1) {
                tr_html += '<td  class="text-right"><input class="form-control input-sm kb-pad text-center manual_dis_text " name="manual_item_discount[]" type="text"  onfocus="this.value = this.value;" value="' + manual_item_ds + '" id="manual_item_discount_' + row_no + '" data-item="' + item_id + '" ><input class="form-control input-sm text-center manualdiscountval" name="manual_item_discount_val[]" type="hidden"  value="' + manual_item_ds_val + '" id="manual_item_discount_val_' + row_no + '" data-item="' + item_id + '" ></td>';
            }else{
                tr_html += '<td style="display:none;" class="text-right"><input class="form-control input-sm  manual_dis_text " name="manual_item_discount[]" type="hidden" value="' + manual_item_ds + '"  id="manual_item_discount_' + row_no + '"  data-item="' + item_id + '" ><input class="form-control input-sm text-center manualdiscountval" name="manual_item_discount_val[]" type="hidden"  value="' + manual_item_ds_val + '" id="manual_item_discount_val_' + row_no + '" data-item="' + item_id + '" ></td>';
            }

             tr_html += '<td><div class="qty_number">'+
                    '<span class="minus ">-</span>'+
                    '<input class="form-control input-sm text-center kb-pad-qty rquantity item-qty"  tabindex="'+((site.settings.set_focus == 1) ? an : (an+1))+'" name="quantity[]" type="text" value="' + formatQuantity2(item_qty) + '" data-id="' + row_no + '" data-item="' + item_id + '" id="quantity_' + row_no + '" onClick="this.select();">'+
                    '<span class="plus ">+</span>'+
                '</div>';

            tr_html += '<input name="recipe_unit[]" type="hidden" class="runit" value="' + recipe_unit + '"><input name="recipe_base_quantity[]" type="hidden" class="rbase_quantity" value="' + base_quantity + '"></td>';

           /* tr_html += '<td><input class="form-control input-sm kb-pad text-center rquantity" tabindex="'+((site.settings.set_focus == 1) ? an : (an+1))+'" name="quantity[]" type="text" value="' + formatQuantity2(item_qty) + '" data-id="' + row_no + '" data-item="' + item_id + '" id="quantity_' + row_no + '" onClick="this.select();"><input name="recipe_unit[]" type="hidden" class="runit" value="' + recipe_unit + '"><input name="recipe_base_quantity[]" type="hidden" class="rbase_quantity" value="' + base_quantity + '"></td>';*/           
           tr_html += '<td class="text-right"><span class="text-right ssubtotal" id="subtotal_' + row_no + '">' + formatMoney(((parseFloat(item_price)) * parseFloat(item_qty)-parseFloat(manual_item_ds))) + '</span></td>';


            /*tr_html += '<td class="text-right"><span class="text-right ssubtotal" id="subtotal_' + row_no + '">' + formatMoney(((parseFloat(item.row.real_unit_price)) * parseFloat(item_qty))) + '</span></td>';*/

            tr_html += '<td class="text-center"><div class="text-center"><span class="payment_status label label-danger posdel" id="' + row_no + '" title="Remove" style="cursor:pointer;">void</span></div></td>';
            
            /*tr_html += '<td class="text-center"><i class="fa fa-times tip pointer posdel" id="' + row_no + '" title="Remove" style="cursor:pointer;"></i></td>';*/
            newTr.html(tr_html);
            if (pos_settings.item_order == 1) {
                newTr.appendTo("#posTable");
            } else {
                newTr.prependTo("#posTable");
            }
            total += formatDecimal(((parseFloat(item.row.real_unit_price)) * parseFloat(item_qty)), 4);
            manu_item_total += formatDecimal(manual_item_ds);
            count += parseFloat(item_qty);
            an++;
            
            if(item.addons !== false) {
            $.each(item.addons, function () {
                    if(this.id == item_addon) {
                        $('#row_' + row_no).addClass('danger');
                    }
                });
            }
            if (item_type == 'standard' && item.options !== false) {
                $.each(item.options, function () {
                    if(this.id == item_option && base_quantity > this.quantity) {
                        $('#row_' + row_no).addClass('danger');
                    }
                });
            } else if(item_type == 'standard' && base_quantity > item_aqty) {
                $('#row_' + row_no).addClass('danger');
            } else if (item_type == 'combo') {
                if(!combo_items ) {
                    $('#row_' + row_no).addClass('danger');
                } else {
                    $.each(combo_items, function(){
                        if(parseFloat(this.quantity) < (parseFloat(this.qty)*base_quantity) && this.type == 'standard') {
                            $('#row_' + row_no).addClass('danger');
                        }
                    });
                }
            }

            var comments = item_comment.split(/\r?\n/g);
            if (pos_settings.remote_printing != 1) {

                b_items += recipe_name("#"+(an-1)+" "+ item_code + " - " + item_name) + "\n";
                for (var i = 0, len = comments.length; i < len; i++) {
                    b_items += (comments[i].length > 0 ? "   * "+comments[i]+"\n" : "");
                }
                b_items += printLine("   "+formatDecimal(item_qty) + " x " + formatMoney(parseFloat(item_price))+": "+ formatMoney(((parseFloat(item_price)) * parseFloat(item_qty)))) + "\n";
                o_items += printLine(recipe_name("#"+(an-1)+" "+ item_code + " - " + item_name) + ": [ "+ (item_ordered != 0 ? 'xxxx' : formatDecimal(item_qty))) + " ]\n";
                for (var i = 0, len = comments.length; i < len; i++) {
                    o_items += (comments[i].length > 0 ? "   * "+comments[i]+"\n" : "");
                }
                o_items += "\n";

            } else {
                if (pos_settings.item_order == 1 && print_cate) {
                    var bprTh = $('<tr></tr>');
                    bprTh.html('<td colspan="100%" class="no-border"><strong>'+item.row.category_name+'</strong></td>');
                    var oprTh = $('<tr></tr>');
                    oprTh.html('<td colspan="100%" class="no-border"><strong>'+item.row.category_name+'</strong></td>');
                    $("#order-table").append(oprTh);
                    $("#bill-table").append(bprTh);
                }
               

                var  bprTr = '<tr class="row_' + item_id + '" data-item-id="' + item_id + '"><td class="no-border">'+(an-1)+"."+ item_name +'['+item.row.variant+']'+'</td>';
              /*  for (var i = 0, len = comments.length; i < len; i++) {
                    bprTr += (comments[i] ? '<br> <b>*</b> <small>'+comments[i]+'</small>' : '');
                }*/
                // bprTr += '</td></tr>';
                bprTr += '<td style="text-align:left;">'+ formatMoney(parseFloat(item_price)) +'</td><td style="text-align:left;">'+ formatDecimal(parseFloat(item_qty)) +'</td><td style="text-align:right;">'+ formatMoney(((parseFloat(item_price) ) * parseFloat(item_qty))) +'</td></tr>';

                var oprTr = '<tr class="row_' + item_id + '" data-item-id="' + item_id + '"><td>#'+(an-1)+' ' + item_code + " - " + item_name + '';
                for (var i = 0, len = comments.length; i < len; i++) {
                    oprTr += (comments[i] ? '<br> <b>*</b> <small>'+comments[i]+'</small>' : '');
                }
                oprTr += '</td><td>[ ' + (item_ordered != 0 ? 'xxxx' : formatDecimal(item_qty)) +' ]</td></tr>';
                $("#order-table").append(oprTr);
                $("#bill-table").append(bprTr);
				
            }
        });
       var bill_table_head = '<tr><th class="text-right" style="text-align:left;width:295px!important;">Description</th><th class="text-right">Price</th><th class="text-right" style="text-align:right;" >Qty</th><th class="text-right">Sub Total</th>';
       $("#bill-table_head").empty();  
       $("#bill-table_head").append(bill_table_head);
        // Order level discount calculations
        if(an ==1){
          localStorage.setItem('input_discount', JSON.stringify(0)); 
          localStorage.setItem('order_discount_input', JSON.stringify(0)); 
        }
        input_discount = localStorage.getItem('input_discount') ? localStorage.getItem('input_discount') : 0;
        offer_total_discount = localStorage.getItem('offer_total_discount') ? localStorage.getItem('offer_total_discount') : 0;
        /*alert('offer'+offer_total_discount);*/
        /*    if (posdiscount = localStorage.getItem('posdiscount')) {
                var ds = posdiscount;

                if (ds.indexOf("%") !== -1) {
                    var pds = ds.split("%");
                    if (!isNaN(pds[0])) {
                        order_discount = formatDecimal((parseFloat(((total) * parseFloat(pds[0])) / 100)), 4);
                    } else {
                        order_discount = parseFloat(ds);
                    }
                } else {
                    order_discount = parseFloat(ds);
                }            
            }
        */

        order_discount = formatDecimal(parseFloat(input_discount));
        // Order level tax calculations
        /*if (site.settings.tax2 != 0) {
            if (postax2 = localStorage.getItem('postax2')) {
                $.each(tax_rates, function () {
                    if (this.id == postax2) {
                        alert(this.type);
                        if (this.type == 2) {
                            invoice_tax = formatDecimal(this.rate);
                        }
                        if (this.type == 1) {
                            invoice_tax = formatDecimal((((total - order_discount) * this.rate) / 100), 4);
                        }
                    }
                });
            }
        }*/
        /*if (site.settings.tax2 != 0) {*/
            if (postax2 = localStorage.getItem('postax2')) {
                $.each(tax_rates, function () {
                    if (this.id == postax2) {
                        if (pos_settings.tax_type == 0) {
                            /*Amount=price*100/(100+taxpercent)
                            Tax=price*taxpercent/(100+taxpercent)*/
                            $amt = formatDecimal(total - order_discount-manu_item_total);
                            $inclusiveamount = $amt*100/(100+formatDecimal(this.rate));
                            //total = $inclusiveamount;
                            invoice_tax=$amt*formatDecimal(this.rate)/(100+formatDecimal(this.rate));
		                    tax_rate_per=this.rate;
                            /*Tax=24.46*10.00/(100+10.00);*/
                            /*alert(Tax);*/
/*alert($inclusiveamount);*/

                            /*alert($inclusiveamount);*/
                            // invoice_tax = formatDecimal(($inclusiveamount * this.rate) /(100 +this.rate));

                            /*invoice_tax = formatDecimal((((total - order_discount-manu_item_total) * this.rate) / 100), 4);*/                            
                        }else{
                            invoice_tax = formatDecimal((((total - order_discount-manu_item_total) * this.rate) / 100), 4);  
					       tax_rate_per=this.rate;
                        }                  
                    }
                });
            }
		
        /*}*/        

        /*if (site.settings.tax2 != 0) {
            if (postax2 = pos_settings.default_tax) {
                
                $.each(tax_rates, function () {
                    if(this.id == postax2){
                        invoice_tax = formatDecimal((((total - order_discount) * this.rate) / 100), 4);
                        
                    }
                });
            }
        }*/
        
  /*alert($amt);
  alert($inclusiveamount);
  alert(invoice_tax);*/

  var tax_per=Math.round(tax_rate_per);
        var total_tax = invoice_tax;
        total = formatDecimal(total-manu_item_total);
        recipe_tax = formatDecimal(recipe_tax);
        total_discount = formatDecimal(order_discount + recipe_discount);        
        if (pos_settings.tax_type == 0) {  
        // inclusive
		var gtotal = parseFloat((total  - recipe_discount-order_discount-offer_total_discount) + parseFloat(shipping) + invoice_tax);
           // var gtotal = parseFloat((total  - recipe_discount-order_discount-offer_total_discount) + parseFloat(shipping));
        }else{	
            // exclusive
		//var gtotal = parseFloat((total  - recipe_discount-order_discount-offer_total_discount) + parseFloat(shipping));
            var gtotal = parseFloat((total  - recipe_discount-order_discount-offer_total_discount) + parseFloat(shipping) + invoice_tax);
        }        
        $('#total').text(formatMoney(total));
        $('#sub_total').val(total);         
        $('#total_tax').val(total_tax);         
        $('#titems').text((an - 1) + ' (' + formatQty(parseFloat(count) - 1) + ')');
        $('#total_items').val((parseFloat(count) - 1));        
        offer_total_discount = parseFloat(offer_total_discount);       

        if (order_discount==0) {
            $('#tds').text('('+formatNumber(order_discount)+') ');
        }else{    
        $('#tds').text('('+(order_discount)+') ');
        }
        $('#posdiscount').val(recipe_discount+order_discount+offer_total_discount);
        $('#item_discount').val(order_discount);
        $('#discount_on_total').val(offer_total_discount);
        if (site.settings.tax2 != 0) {
            $('#ttax2').text(formatMoney(total_tax));
        }
        $('#tship').text(parseFloat(shipping) > 0 ? formatMoney(shipping) : '');
        if (pos_settings.tax_type == 0) {
            
        $('#gtotal').text(formatMoney(gtotal-invoice_tax));
        }else{            
            $('#gtotal').text(formatMoney(gtotal));
        }
        if (pos_settings.remote_printing != 1) {

            order_data.items = o_items;
            bill_data.items = b_items;
            var b_totals = '';
            b_totals += printLine(lang.total+': '+ formatMoney(total)) +"\n";
            if(order_discount > 0 || recipe_discount > 0) {
                b_totals += printLine(lang.discount+': '+ formatMoney(order_discount+recipe_discount)) +"\n";
            }
            if (site.settings.tax2 != 0 && invoice_tax != 0) {
                b_totals += printLine(lang.order_tax+': '+ formatMoney(invoice_tax)) +"\n";
            }
            b_totals += printLine(lang.grand_total+': '+ formatMoney(gtotal)) +"\n";
            if(pos_settings.rounding != 0) {
                round_total = roundNumber(gtotal, parseInt(pos_settings.rounding));
                var rounding = formatDecimal(round_total - gtotal);
                b_totals += printLine(lang.rounding+': '+ formatMoney(rounding)) +"\n";
                b_totals += printLine(lang.total_payable+': '+ formatMoney(round_total)) +"\n";
            }
            b_totals += "\n"+ lang.items+': '+ (an - 1) + ' (' + (parseFloat(count) - 1) + ')' +"\n";
            bill_data.totals = b_totals;
            bill_data.footer = "\n"+ lang.merchant_copy+"\n";

        } else {
            var bill_totals = '';
            bill_totals += '<tr class="bold"><td>'+lang.total+'</td><td style="text-align:right;">'+formatMoney(total)+'</td></tr>';
            var order_discount_input = $("#order_discount_input").val();
            if(order_discount > 0 || recipe_discount > 0) {
                bill_totals += '<tr class="bold"><td>'+lang.discount+'('+order_discount_input+')</td><td style="text-align:right;">'+formatMoney(order_discount+recipe_discount)+'</td></tr>';
            }
          /*   if (site.settings.tax2 != 0 && invoice_tax != 0) {
               // bill_totals += '<tr class="bold"><td>Tax Inclusive 10%</td><td style="text-align:right;"></td></tr>';
                bill_totals += '<tr class="bold"><td>Tax Inclusive 10%</td><td style="text-align:right;">'+formatMoney(invoice_tax)+'</td></tr>';
            } */
			 if (pos_settings.tax_type == 0) {
				  bill_totals += '<tr class="bold"><td>Tax Inclusive '+tax_per+'%</td><td style="text-align:right;">'+formatMoney(invoice_tax)+'</td></tr>';
			 }else{
				  bill_totals += '<tr class="bold"><td>Tax Exclusive '+tax_per+'%</td><td style="text-align:right;">'+formatMoney(invoice_tax)+'</td></tr>';
			 }
				  if (pos_settings.tax_type == 0) {
                    bill_totals += '<tr class="bold"><td>'+lang.grand_total+'</td><td style="text-align:right;">'+formatMoney(gtotal-invoice_tax)+'</td></tr>';
				  }else{
					   bill_totals += '<tr class="bold"><td>'+lang.grand_total+'</td><td style="text-align:right;">'+formatMoney(gtotal)+'</td></tr>';
				  }
					  
            /*if(pos_settings.rounding != 0) {
                round_total = roundNumber(gtotal, parseInt(pos_settings.rounding));
                var rounding = formatDecimal(round_total - gtotal);
                bill_totals += '<tr class="bold"><td>'+lang.rounding+'</td><td style="text-align:right;">'+formatMoney(rounding)+'</td></tr>';
                bill_totals += '<tr class="bold"><td>'+lang.total_payable+'</td><td style="text-align:right;">'+formatMoney(round_total)+'</td></tr>';
            }*/
            bill_totals += '<tr class="bold"><td>'+lang.items+'</td><td style="text-align:right;">'+(an - 1) + ' (' + (parseFloat(count) - 1) + ')</td></tr>';
            $('#bill-total-table').empty();
            $('#bill-total-table').append(bill_totals);
            $('#bill_footer').append('<p class="text-center"><br>'+lang.merchant_copy+'</p>');
        }
        if(count > 1) {
            $('#poscustomer').select2("readonly", true);
            $('#poswarehouse').select2("readonly", true);
            $('#postable_list').select2("readonly", true);
            $('#posorder_type').select2("readonly", true);
        } else {
            $('#poscustomer').select2("readonly", false);
            $('#poswarehouse').select2("readonly", false);
            $('#postable_list').select2("readonly", true);
            $('#posorder_type').select2("readonly", true);
        }
        if (KB) { display_keyboards(); }
        if (site.settings.set_focus == 1) {
            $('#add_item').attr('tabindex', an);
            $('[tabindex='+(an-1)+']').focus().select();
        } else {
            $('#add_item').attr('tabindex', 1);
            //$('#add_item').focus();
        }
        // getTotalDiscount();
    }
}

function printLine(str) {
    var size = pos_settings.char_per_line;
    var len = str.length;
    var res = str.split(":");
    var newd = res[0];
    for(i=1; i<(size-len); i++) {
        newd += " ";
    }
    newd += res[1];
    return newd;
}

/* -----------------------------
 * Add Purchase Iten Function
 * @param {json} item
 * @returns {Boolean}
 ---------------------------- */

 function add_invoice_item(item) {

    if (count == 1) {
        positems = {};
        if ($('#poswarehouse').val() && $('#poscustomer').val()) {
            $('#poscustomer').select2("readonly", true);
            $('#poswarehouse').select2("readonly", true);
            $('#postable_list').select2("readonly", true);
            $('#posorder_type').select2("readonly", true);
        } else {
            bootbox.alert('Please choose customer');
            item = null;
            return;
        }
    }
    if (item == null)
        return;

    var item_id = site.settings.item_addition == 1 ? item.item_id : item.id;
    if (positems[item_id]) {

        var new_qty = parseFloat(positems[item_id].row.qty) + 1;
        positems[item_id].row.base_quantity = new_qty;
        if(positems[item_id].row.unit != positems[item_id].row.base_unit) {
            $.each(positems[item_id].units, function(){
                if (this.id == positems[item_id].row.unit) {
                    positems[item_id].row.base_quantity = unitToBaseQty(new_qty, this);
                }
            });
        }
        positems[item_id].row.qty = new_qty;

    } else {
        positems[item_id] = item;
    }
    positems[item_id].order = new Date().getTime();
    localStorage.setItem('positems', JSON.stringify(positems));    
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

 function display_keyboards() {

    $('.kb-text').keyboard({
        autoAccept: true,
        alwaysOpen: false,
        openOn: 'focus',
        usePreview: false,
        layout: 'custom',
        //layout: 'qwerty',
        display: {
            'bksp': "\u2190",
            'accept': 'return',
            'default': 'ABC',
            'meta1': '123',
            'meta2': '#+='
        },
        customLayout: {
            'default': [
            'q w e r t y u i o p {bksp}',
            'a s d f g h j k l {enter}',
            '{s} z x c v b n m , . {s}',
            '{meta1} {space} {cancel} {accept}'
            ],
            'shift': [
            'Q W E R T Y U I O P {bksp}',
            'A S D F G H J K L {enter}',
            '{s} Z X C V B N M / ? {s}',
            '{meta1} {space} {meta1} {accept}'
            ],
            'meta1': [
            '1 2 3 4 5 6 7 8 9 0 {bksp}',
            '- / : ; ( ) \u20ac & @ {enter}',
            '{meta2} . , ? ! \' " {meta2}',
            '{default} {space} {default} {accept}'
            ],
            'meta2': [
            '[ ] { } # % ^ * + = {bksp}',
            '_ \\ | &lt; &gt; $ \u00a3 \u00a5 {enter}',
            '{meta1} ~ . , ? ! \' " {meta1}',
            '{default} {space} {default} {accept}'
            ]}
        });
    $('.kb-pad').keyboard({
        restrictInput: true,
        preventPaste: true,
        autoAccept: true,
        alwaysOpen: false,
        openOn: 'click',
        usePreview: false,
        layout: 'custom',
        display: {
            'b': '\u2190:Backspace',
        },
        customLayout: {
            'default': [
            '1 2 3 {b}',
            '4 5 6 . {clear}',
            '7 8 9 0 %',
            '{accept} {cancel}'
            ]
        }
    });
    var cc_key = (site.settings.decimals_sep == ',' ? ',' : '{clear}');
    $('.kb-pad1').keyboard({
        restrictInput: true,
        preventPaste: true,
        autoAccept: true,
        alwaysOpen: false,
        openOn: 'click',
        usePreview: false,
        layout: 'custom',
        display: {
            'b': '\u2190:Backspace',
        },
        customLayout: {
            'default': [
            '1 2 3 {b}',
            '4 5 6 . '+cc_key,
            '7 8 9 0 %',
            '{accept} {cancel}'
            ]
        }
    });

 }

/*$(window).bind('beforeunload', function(e) {
    if(count > 1){
    var msg = 'You will loss the sale data.';
        (e || window.event).returnValue = msg;
        return msg;
    }
});
*/
if(site.settings.auto_detect_barcode == 1) {
    $(document).ready(function() {
        var pressed = false;
        var chars = [];
        $(window).keypress(function(e) {
            if(e.key == '%') { pressed = true; }
            chars.push(String.fromCharCode(e.which));
            if (pressed == false) {
                setTimeout(function(){
                    if (chars.length >= 8) {
                        var barcode = chars.join("");
                        $( "#add_item" ).focus().autocomplete( "search", barcode );
                    }
                    chars = [];
                    pressed = false;
                },200);
            }
            pressed = true;
        });
    });
}

$(document).ready(function() {
    read_card();
});

function generateCardNo(x) {
    if(!x) { x = 16; }
    chars = "1234567890";
    no = "";
    for (var i=0; i<x; i++) {
        var rnum = Math.floor(Math.random() * chars.length);
        no += chars.substring(rnum,rnum+1);
    }
    return no;
}
function roundNumber(number, toref) {
    switch(toref) {
        case 1:
            var rn = formatDecimal(Math.round(number * 20)/20);
            break;
        case 2:
            var rn = formatDecimal(Math.round(number * 2)/2);
            break;
        case 3:
            var rn = formatDecimal(Math.round(number));
            break;
        case 4:
            var rn = formatDecimal(Math.ceil(number));
            break;
        default:
            var rn = number;
    }
    return rn;
}
function getNumber(x) {
    return accounting.unformat(x);
}
function formatQuantity(x) {
    return (x != null) ? '<div class="text-center">'+formatNumber(x, site.settings.qty_decimals)+'</div>' : '';
}
function formatQuantity2(x) {
    return (x != null) ? formatQuantityNumber(x, site.settings.qty_decimals) : '';
}
function formatQuantityNumber(x, d) {
    if (!d) { d = site.settings.qty_decimals; }
    return parseFloat(accounting.formatNumber(x, d, '', '.'));
}
function formatQty(x) {
    return (x != null) ? formatNumber(x, site.settings.qty_decimals) : '';
}
function formatNumber(x, d) {
    if(!d && d != 0) { d = site.settings.decimals; }
    if(site.settings.sac == 1) {
        return formatSA(parseFloat(x).toFixed(d));
    }
    return accounting.formatNumber(x, d, site.settings.thousands_sep == 0 ? ' ' : site.settings.thousands_sep, site.settings.decimals_sep);
}
function formatMoney(x, symbol) {
    if(!symbol) { symbol = ""; }
    if(site.settings.sac == 1) {
        return symbol+''+formatSA(parseFloat(x).toFixed(site.settings.decimals));
    }
    return accounting.formatMoney(x, symbol, site.settings.decimals, site.settings.thousands_sep == 0 ? ' ' : site.settings.thousands_sep, site.settings.decimals_sep, "%s%v");
}
function formatCNum(x) {
    if (site.settings.decimals_sep == ',') {
        var x = x.toString();
        var x = x.replace(",", ".");
        return parseFloat(x);
    }
    return x;
}
function formatDecimal(x, d) {
    if (!d) { d = site.settings.decimals; }
    return parseFloat(accounting.formatNumber(x, d, '', '.'));
}
function hrsd(sdate) {
    return moment().format(site.dateFormats.js_sdate.toUpperCase())
}

function hrld(ldate) {
    return moment().format(site.dateFormats.js_sdate.toUpperCase()+' H:mm')
}
function is_valid_discount(mixed_var) {
    return (is_numeric(mixed_var) || (/([0-9]%)/i.test(mixed_var))) ? true : false;
}
function is_numeric(mixed_var) {
    var whitespace =
    " \n\r\t\f\x0b\xa0\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u200b\u2028\u2029\u3000";
    return (typeof mixed_var === 'number' || (typeof mixed_var === 'string' && whitespace.indexOf(mixed_var.slice(-1)) === -
        1)) && mixed_var !== '' && !isNaN(mixed_var);
}
function is_float(mixed_var) {
    return +mixed_var === mixed_var && (!isFinite(mixed_var) || !! (mixed_var % 1));
}
function currencyFormat(x) {
    return formatMoney(x != null ? x : 0);
}
function formatSA (x) {
    x=x.toString();
    var afterPoint = '';
    if(x.indexOf('.') > 0)
       afterPoint = x.substring(x.indexOf('.'),x.length);
    x = Math.floor(x);
    x=x.toString();
    var lastThree = x.substring(x.length-3);
    var otherNumbers = x.substring(0,x.length-3);
    if(otherNumbers != '')
        lastThree = ',' + lastThree;
    var res = otherNumbers.replace(/\B(?=(\d{2})+(?!\d))/g, ",") + lastThree + afterPoint;

    return res;
}

function unitToBaseQty(qty, unitObj) {
    switch(unitObj.operator) {
        case '*':
            return parseFloat(qty)*parseFloat(unitObj.operation_value);
            break;
        case '/':
            return parseFloat(qty)/parseFloat(unitObj.operation_value);
            break;
        case '+':
            return parseFloat(qty)+parseFloat(unitObj.operation_value);
            break;
        case '-':
            return parseFloat(qty)-parseFloat(unitObj.operation_value);
            break;
        default:
            return parseFloat(qty);
    }
}

function baseToUnitQty(qty, unitObj) {
    switch(unitObj.operator) {
        case '*':
            return parseFloat(qty)/parseFloat(unitObj.operation_value);
            break;
        case '/':
            return parseFloat(qty)*parseFloat(unitObj.operation_value);
            break;
        case '+':
            return parseFloat(qty)-parseFloat(unitObj.operation_value);
            break;
        case '-':
            return parseFloat(qty)+parseFloat(unitObj.operation_value);
            break;
        default:
            return parseFloat(qty);
    }
}

function read_card() {
    var typingTimer;

    $('.swipe').keyup(function (e) {
        e.preventDefault();
        var self = $(this);
        clearTimeout(typingTimer);
        typingTimer = setTimeout(function() {
            var payid = self.attr('id');
            var id = payid.substr(payid.length - 1);
            var v = self.val();
            var p = new SwipeParserObj(v);

            if(p.hasTrack1) {
                var CardType = null;
                var ccn1 = p.account.charAt(0);
                if(ccn1 == 4)
                    CardType = 'Visa';
                else if(ccn1 == 5)
                    CardType = 'MasterCard';
                else if(ccn1 == 3)
                    CardType = 'Amex';
                else if(ccn1 == 6)
                    CardType = 'Discover';
                else
                    CardType = 'Visa';

                $('#pcc_no_'+id).val(p.account).change();
                $('#pcc_holder_'+id).val(p.account_name).change();
                $('#pcc_month_'+id).val(p.exp_month).change();
                $('#pcc_year_'+id).val(p.exp_year).change();
                $('#pcc_cvv2_'+id).val('');
                $('#pcc_type_'+id).val(CardType).change();
                self.val('');
                $('#pcc_cvv2_'+id).focus();
            } else {
                $('#pcc_no_'+id).val('');
                $('#pcc_holder_'+id).val('');
                $('#pcc_month_'+id).val('');
                $('#pcc_year_'+id).val('');
                $('#pcc_cvv2_'+id).val('');
                $('#pcc_type_'+id).val('');
            }
        }, 100);
    });

    $('.swipe').keydown(function (e) {
        clearTimeout(typingTimer);
    });
}

function check_add_item_val() {
    $('#add_item').bind('keypress', function (e) {
        if (e.keyCode == 13 || e.keyCode == 9) {
            e.preventDefault();
            $(this).autocomplete("search");
        }
    });
}
function nav_pointer() {
    var pp = p_page == 'n' ? 0 : p_page;
    (pp == 0) ? $('#previous').attr('disabled', true) : $('#previous').attr('disabled', false);
    ((pp+pro_limit) > tcp) ? $('#next').attr('disabled', true) : $('#next').attr('disabled', false);
}

function recipe_name(name, size) {
    if (!size) { size = 42; }
    return name.substring(0, (size-7));
}

$.extend($.keyboard.keyaction, {
    enter : function(base) {
        if (base.$el.is("textarea")){
            base.insertText('\r\n');
        } else {
            base.accept();
        }
    }
});

/*$(document).ajaxStart(function(){
  $('#ajaxCall').show();
}).ajaxStop(function(){
  $('#ajaxCall').hide();
});*/

$(document).ready(function(){
    nav_pointer();
    $('#myModal').on('hidden.bs.modal', function() {
        $(this).find('.modal-dialog').empty();
        $(this).removeData('bs.modal');
    });
    $('#myModal2').on('hidden.bs.modal', function () {
        $(this).find('.modal-dialog').empty();
        $(this).removeData('bs.modal');
        $('#myModal').css('zIndex', '1050');
        $('#myModal').css('overflow-y', 'scroll');
    });
    $('#myModal2').on('show.bs.modal', function () {
        $('#myModal').css('zIndex', '1040');
    });
    $('.modal').on('hidden.bs.modal', function() {
        $(this).removeData('bs.modal');
    });
    $('.modal').on('show.bs.modal', function () {
        $('#modal-loading').show();
        $('.blackbg').css('zIndex', '1041');
        $('.loader').css('zIndex', '1042');
    }).on('hide.bs.modal', function () {
        $('#modal-loading').hide();
        $('.blackbg').css('zIndex', '3');
        $('.loader').css('zIndex', '4');
    });
    $('#clearLS').click(function(event) {
        bootbox.confirm("Are you sure?", function(result) {
        if(result == true) {
            localStorage.clear();
            location.reload();
        }
        });
        return false;
    });

    $(document).on('click','.minus',function(){
    $cnt = parseInt($(this).closest('.qty_number').find('.item-qty').val()) - parseInt(1);
    if ($cnt==0) {
        return false;
    }
    $(this).closest('.qty_number').find('.item-qty').val($cnt);
    $(this).closest('.qty_number').find('.rquantity').trigger('change');
    });
    $(document).on('click','.plus',function(){  
    $cnt = parseInt($(this).closest('.qty_number').find('.item-qty').val()) + parseInt(1);
    $(this).closest('.qty_number').find('.item-qty').val($cnt);
    $(this).closest('.qty_number').find('.rquantity').trigger('change');
    });

});

//$.ajaxSetup ({ cache: false, headers: { "cache-control": "no-cache" } });
if(pos_settings.focus_add_item != '') { shortcut.add(pos_settings.focus_add_item, function() { $("#add_item").focus(); }, { 'type':'keydown', 'propagate':false, 'target':document} ); }
if(pos_settings.add_manual_recipe != '') { shortcut.add(pos_settings.add_manual_recipe, function() { $("#addManually").trigger('click'); }, { 'type':'keydown', 'propagate':false, 'target':document} ); }
if(pos_settings.customer_selection != '') { shortcut.add(pos_settings.customer_selection,/* function() { $("#poscustomer").select2("open"); },*/ { 'type':'keydown', 'propagate':false, 'target':document} ); }
if(pos_settings.add_customer != '') { shortcut.add(pos_settings.add_customer, function() { $("#add-customer").trigger('click'); }, { 'type':'keydown', 'propagate':false, 'target':document} ); }
if(pos_settings.toggle_category_slider != '') { shortcut.add(pos_settings.toggle_category_slider, function() { $("#open-category").trigger('click'); }, { 'type':'keydown', 'propagate':false, 'target':document} ); }
if(pos_settings.toggle_brands_slider != '') { shortcut.add(pos_settings.toggle_brands_slider, function() { $("#open-brands").trigger('click'); }, { 'type':'keydown', 'propagate':false, 'target':document} ); }
if(pos_settings.toggle_subcategory_slider != '') { shortcut.add(pos_settings.toggle_subcategory_slider, function() { $("#open-subcategory").trigger('click'); }, { 'type':'keydown', 'propagate':false, 'target':document} ); }
if(pos_settings.cancel_sale != '') { shortcut.add(pos_settings.cancel_sale, function() { $("#reset").click(); }, { 'type':'keydown', 'propagate':false, 'target':document} ); }
if(pos_settings.suspend_sale != '') { shortcut.add(pos_settings.suspend_sale, function() { $("#suspend").trigger('click'); }, { 'type':'keydown', 'propagate':false, 'target':document} ); }
if(pos_settings.print_items_list != '') { shortcut.add(pos_settings.print_items_list, function() { $("#print_btn").click(); }, { 'type':'keydown', 'propagate':false, 'target':document} ); }
if(pos_settings.finalize_sale != '') { shortcut.add(pos_settings.finalize_sale, function() { if ($('#paymentModal').is(':visible')) { $("#submit-sale").click(); } else { $("#payment").trigger('click'); } }, { 'type':'keydown', 'propagate':false, 'target':document} ); }
if(pos_settings.today_sale != '') { shortcut.add(pos_settings.today_sale, function() { $("#today_sale").click(); }, { 'type':'keydown', 'propagate':false, 'target':document} ); }
if(pos_settings.open_hold_bills != '') { shortcut.add(pos_settings.open_hold_bills, function() { $("#opened_bills").trigger('click'); }, { 'type':'keydown', 'propagate':false, 'target':document} ); }
if(pos_settings.close_register != '') { shortcut.add(pos_settings.close_register, function() { $("#close_register").click(); }, { 'type':'keydown', 'propagate':false, 'target':document} ); }
shortcut.add("ESC", function() { $("#cp").trigger('click'); }, { 'type':'keydown', 'propagate':false, 'target':document} );

if (site.settings.set_focus != 1) {
    //$(document).ready(function(){ $('#add_item').focus(); });
}
var item_scanned =  false;
$(window).scannerDetection();        
$(window).bind('scannerDetectionComplete',function(e,data){
    console.log('complete '+data.string);
    item_scanned = true;
   // $("#add_item").focus();
})
//.bind('scannerDetectionError',function(e,data){
//    console.log('detection error '+data.string);
//})
//.bind('scannerDetectionReceive',function(e,data){	   
//
//})