$(document).ready(function () {
$('body a, body button').attr('tabindex', -1);
check_add_item_val();
if (site.settings.set_focus != 1) {
    $('#add_item').focus();
}

////////////////// store _select  /////////////////
$('#store_transfrom_store_id').change(function (e) {
    localStorage.setItem('store_transfrom_store_id', $(this).val());
	$('#store_transto_store_id option').attr('disabled',false)
	$('#store_transto_store_id option[value="'+$(this).val()+'"]').attr('disabled',true)
});
$('#store_transto_store_id').change(function (e) {
        localStorage.setItem('store_transto_store_id', $(this).val());
});
//*********************** store select - end *************************//

//****************** add items ****************//
$("#add_item").autocomplete({
            // source: '<?= admin_url('procurment/store_transfers/suggestions'); ?>',
            source: function (request, response) {
                $.ajax({
                    type: 'get',
                    url: site.base_url+'procurment/store_transfers/suggestions',
                    dataType: "json",
                    data: {
                        term: request.term,
                        supplier_id: $("#store_transsupplier").val()
                    },
                    success: function (data) {
                        $(this).removeClass('ui-autocomplete-loading');
                        response(data);
                    }
                });
            },
            minLength: 1,
            autoFocus: false,
            delay: 250,
            response: function (event, ui) {
                if ($(this).val().length >= 16 && ui.content[0].id == 0) {
                    //audio_error.play();
                    bootbox.alert('no match found', function () {
                        $('#add_item').focus();
                    });
                    $(this).removeClass('ui-autocomplete-loading');
                    $(this).val('');
                }
               /* else if (ui.content.length == 1 && ui.content[0].id != 0) {
                    ui.item = ui.content[0];
                    $(this).data('ui-autocomplete')._trigger('select', 'autocompleteselect', ui);
                    $(this).autocomplete('close');
                    $(this).removeClass('ui-autocomplete-loading');
                }*/
                else if (ui.content.length == 1 && ui.content[0].id == 0) {
                    //audio_error.play();
                    bootbox.alert('no match found', function () {
                        $('#add_item').focus();
                    });
                    $(this).removeClass('ui-autocomplete-loading');
                    $(this).val('');
                }
            },
            select: function (event, ui) {
                event.preventDefault();
                if (ui.item.id !== 0) {                    
                    var row = add_store_transfers_item(ui.item);
                    if (row)
                        $(this).val('');
                } else {
                    //audio_error.play();
                    bootbox.alert('no match found');
                }
            }
        });
////******************* add items end ************************************//

// Order level shipping and discoutn localStorage
if (store_transdiscount = localStorage.getItem('store_transdiscount')) {
    $('#store_transdiscount').val(store_transdiscount);
}
$('#ptax2').change(function (e) {
    localStorage.setItem('p_tax2', $(this).val());
});
if (ptax1= localStorage.getItem('p_tax1')) {
    $('#ptax1').select2("val", ptax1);
}
$('#store_transstatus').change(function (e) {
    localStorage.setItem('store_transstatus', $(this).val());
});
if (postatus = localStorage.getItem('store_transstatus')) {
    $('#store_transstatus').select2("val", postatus);
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
    localStorage.setItem('store_transshipping', shipping);
    var gtotal = ((total + invoice_tax) - order_discount) + shipping;
    $('#gtotal').text(formatMoney(gtotal));
    $('#tship').text(formatMoney(shipping));
});
if (poshipping = localStorage.getItem('store_transshipping')) {
    shipping = parseFloat(poshipping);
    $('#poshipping').val(shipping);
}

$('#popayment_term').change(function (e) {
    localStorage.setItem('store_transpayment_term', $(this).val());
});
if (popayment_term = localStorage.getItem('store_transpayment_term')) {
    $('#popayment_term').val(popayment_term);
}

// If there is any item in localStorage
if (localStorage.getItem('store_transitems')) {
    loadItems();
}

    // clear localStorage and reload
    $('#reset').click(function (e) {
        bootbox.confirm(lang.r_u_sure, function (result) {
            if (result) {
                if (localStorage.getItem('store_transitems')) {
                    localStorage.removeItem('store_transitems');
                }
                if (localStorage.getItem('store_transdiscount')) {
                    localStorage.removeItem('store_transdiscount');
                }
                if (localStorage.getItem('store_transtax2')) {
                    localStorage.removeItem('store_transtax2');
                }
                if (localStorage.getItem('store_transshipping')) {
                    localStorage.removeItem('store_transshipping');
                }
                if (localStorage.getItem('store_transref')) {
                    localStorage.removeItem('store_transref');
                }
                if (localStorage.getItem('store_transwarehouse')) {
                    localStorage.removeItem('store_transwarehouse');
                }
                if (localStorage.getItem('store_transnote')) {
                    localStorage.removeItem('store_transnote');
                }
                if (localStorage.getItem('store_transsupplier')) {
                    localStorage.removeItem('store_transsupplier');
                }
                if (localStorage.getItem('store_transcurrency')) {
                    localStorage.removeItem('store_transcurrency');
                }
                if (localStorage.getItem('store_transextras')) {
                    localStorage.removeItem('store_transextras');
                }
                if (localStorage.getItem('store_transdate')) {
                    localStorage.removeItem('store_transdate');
                }
                if (localStorage.getItem('store_transstatus')) {
                    localStorage.removeItem('store_transstatus');
                }
				
				if (localStorage.getItem('store_transfrom_store_id')) {
                    localStorage.removeItem('store_transfrom_store_id');
                }
				if (localStorage.getItem('store_transto_store_id')) {
                    localStorage.removeItem('store_transto_store_id');
                }
				
                if (localStorage.getItem('store_transpayment_term')) {
                    localStorage.removeItem('store_transpayment_term');
                }
				if (localStorage.getItem('store_transrequestnumber')) {
					localStorage.removeItem('store_transrequestnumber');
				}
                $('#modal-loading').show();
                location.reload();
            }
        });
    });

// save and load the fields in and/or from localStorage
var $supplier = $('#store_transsupplier'), $currency = $('#pocurrency');

$('#poref').change(function (e) {
    localStorage.setItem('store_transref', $(this).val());
});

if (poref = localStorage.getItem('store_transref')) {
    $('#poref').val(poref);
}

$('#invoice_no').change(function (e) {
    localStorage.setItem('store_transinvoice_no', $(this).val());
});

if (store_transinvoice_no = localStorage.getItem('store_transinvoice_no')) {
    $('#invoice_no').val(store_transinvoice_no);
}
$('#powarehouse').change(function (e) {
    localStorage.setItem('store_transwarehouse', $(this).val());
});
if (powarehouse = localStorage.getItem('store_transwarehouse')) {
    $('#powarehouse').select2("val", powarehouse);
}
        $('#ponote').redactor('destroy');
        $('#ponote').redactor({
            buttons: ['formatting', '|', 'alignleft', 'aligncenter', 'alignright', 'justify', '|', 'bold', 'italic', 'underline', '|', 'unorderedlist', 'orderedlist', '|', 'link', '|', 'html'],
            formattingTags: ['p', 'pre', 'h3', 'h4'],
            minHeight: 100,
            changeCallback: function (e) {
                var v = this.get();
                localStorage.setItem('store_transnote', v);
            }
        });
        if (ponote = localStorage.getItem('store_transnote')) {
            $('#ponote').redactor('set', ponote);
        }
        $supplier.change(function (e) {
            localStorage.setItem('store_transsupplier', $(this).val());
            $('#supplier_id').val($(this).val());
        });
        if (store_transsupplier = localStorage.getItem('store_transsupplier')) {
            $supplier.val(store_transsupplier).select2({
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

if (localStorage.getItem('store_transextras')) {
    $('#extras').iCheck('check');
    $('#extras-con').show();
}
$('#extras').on('ifChecked', function () {
    localStorage.setItem('store_transextras', 1);
    $('#extras-con').slideDown();
});
$('#extras').on('ifUnchecked', function () {
    localStorage.removeItem("store_transextras");
    $('#extras-con').slideUp();
});

$(document).on('change', '.rtransfer_qty', function () {
    var item_id = $(this).closest('tr').attr('data-item-id');
	var row = $(this).closest('tr').attr('id');
	var row_id = row.slice(4);
	var tquantity = 0;
	var pquantity = $('#pending_qty_'+row_id).val();
	var aquantity = $('#available_qty_'+row_id).val();
	if(pquantity < $(this).val() && aquantity < $(this).val()){
		tquantity = 0;
		bootbox.alert('Transfer quantity is greater than availabel quantity!', function(){
			$('#transfer_qty_'+row_id).val(tquantity);
			store_transitems[item_id].row.transfer_quantity = tquantity;
    		localStorage.setItem('store_transitems', JSON.stringify(store_transitems));
		});
	}else{
	    tquantity = $(this).val();
	    $p_qty = aquantity - tquantity;
	    $('#pending_qty_'+row_id).val($p_qty);
	    store_transitems[item_id].row.transfer_quantity = tquantity;
	    store_transitems[item_id].row.pending_quantity = $p_qty;
    	localStorage.setItem('store_transitems', JSON.stringify(store_transitems));
	    
       // return;	
	}
    
});
$(document).on('change', '.ravailable_qty', function () {
    var item_id = $(this).closest('tr').attr('data-item-id');
	var row = $(this).closest('tr').attr('id');
	var row_id = row.slice(4);
	var tquantity =  $(this).closest('tr').find('.rtransfer_qty').val();
	var aquantity = $(this).val();
	$p_qty = aquantity - tquantity;
	$('#pending_qty_'+row_id).val($p_qty);
	store_transitems[item_id].row.pending_quantity = $p_qty;
	localStorage.setItem('store_transitems', JSON.stringify(store_transitems));
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
        localStorage.setItem('store_transtax2', $(this).val());
        loadItems();
        return;
    });
}

// Order discount calcuation
var old_store_transdiscount;
$('#store_transdiscount').focus(function () {
    old_store_transdiscount = $(this).val();
}).change(function () {
    var pod = $(this).val() ? $(this).val() : 0;
    if (is_valid_discount(pod)) {
        localStorage.removeItem('store_transdiscount');
        localStorage.setItem('store_transdiscount', pod);
        loadItems();
        return;
    } else {
        $(this).val(old_store_transdiscount);
        bootbox.alert(lang.unexpected_value);
        return;
    }

});


    /* ----------------------
     * Delete Row Method
     * ---------------------- */

     $(document).on('click', '.podel', function () {
        var row = $(this).closest('tr');
        var item_id = row.attr('data-item');
        delete store_transitems[item_id];
        row.remove();
        if(store_transitems.hasOwnProperty(item_id)) { } else {
            localStorage.setItem('store_transitems', JSON.stringify(store_transitems));
            loadItems();
            return;
        }
    });

    /* -----------------------
     * Edit Row Modal Hanlder
     ----------------------- */
   
   
    $(document).on('change', '#punit', function () {
        var row = $('#' + $('#row_id').val());
        var item_id = row.attr('data-item-id');
        var item = store_transitems[item_id];
        if (!is_numeric($('#pquantity').val()) || parseFloat($('#pquantity').val()) < 0) {
            $(this).val(old_row_qty);
            bootbox.alert(lang.unexpected_value);
            return;
        }
        var unit = $('#punit').val();
        if(unit != store_transitems[item_id].row.base_unit) {
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
        var item = store_transitems[item_id];
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
        if (new_received > store_transitems[item_id].row.qty) {
            $(this).val(old_received);
            bootbox.alert(lang.unexpected_value);
            return;
        }
        unit = formatDecimal(row.children().children('.runit').val()),
        $.each(store_transitems[item_id].units, function(){
            if (this.id == unit) {
                qty_received = formatDecimal(unitToBaseQty(new_received, this), 4);
            }
        });
        store_transitems[item_id].row.unit_received = new_received;
        store_transitems[item_id].row.received = qty_received;
        localStorage.setItem('store_transitems', JSON.stringify(store_transitems));
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
        store_transitems[item_id].row.cost = new_cost;
        localStorage.setItem('store_transitems', JSON.stringify(store_transitems));
        loadItems();
    });

    $(document).on("click", '#removeReadonly', function () {
     $('#store_transsupplier').select2('readonly', false);
     return false;
 });
 

});
/* -----------------------
 * Misc Actions
 ----------------------- */

// hellper function for supplier if no localStorage value
function nsSupplier() {
    $('#store_transsupplier').select2({
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
    if (localStorage.getItem('store_transitems')) {
        total = 0;
        count = 1;
        an = 1;
        product_tax = 0;
        invoice_tax = 0;
        product_discount = 0;
        order_discount = 0;
        total_discount = 0;
        $("#store_transfersTable tbody").empty();
        store_transitems = JSON.parse(localStorage.getItem('store_transitems'));
		sortedItems = store_transitems;
		$row_index = 0;
		$total_no_items = 0;
		$total_no_qty = parseInt(0);$transfer_qty=0;
        var order_no = new Date().getTime();
        $.each(sortedItems, function () {
	        var item = this;
	        $product_grand_total_amt = 0;
	        $product_gross_amt		 = 0;
	        $product_tax			 = 0;
            var item_id 		     = item.item_id;
	        item.order 			     = item.order ? item.order : new Date().getTime();
			console.log(item.row);
			
            $sno 				     = ++$row_index;
            $id 				     = item.row.id;
            $product_name 		     = item.row.name;
            $product_code 		     = item.row.code;
            $product_type 		     = item.row.type;
			item_tax_method 	     = item.row.tax_method;
			$product_tax_per 	     = item.row.tax;
			$product_tax_method      = item.row.tax_method;
            $qty 				     = item.row.qty;
			$ex_qty = (item.row.ex_qty!=undefined)?item.row.ex_qty:0;
			$request_qty =item.row.request_qty ? item.row.request_qty : 0;
			$html = '<tr class="order-item-row warning" data-item="'+item_id+'">';
            $html += '<td>'+$sno+'<input type="hidden" name="product_id[]" value="'+$id+'"></td>';
            $html += '<td>'+$product_code+'<input type="hidden" name="product_code[]" value="'+$product_code+'"></td>';
            $html += '<td>'+$product_name+'<input type="hidden" name="product_name[]" value="'+$product_name+'"><input type="hidden" name="product_type[]" value="'+$product_type+'"><input type="hidden" name="variant_id[]" value="'+item.row.variant_id+'"><input type="hidden" name="catgory_id[]" value="'+item.row.category_id+'"><input type="hidden" name="subcatgory_id[]" value="'+item.row.subcategory_id+'"><input type="hidden" name="brand_id[]" value="'+item.row.brand+'"></td>';
			$html += '<td><input type="text" name="request_qty[]" value="'+formatDecimals($request_qty)+'" class="form-control text-center request-qty" readonly></td>';
			$html +='<td colspan="11">';
			
	    if (item.row.batches) {
	      $.each(item.row.batches,function(n,v){
			var product_unit    = item.row.unit, base_quantity = item.row.base_quantity;
			$recipe_price 		= v.selling_price;
			$recipe_stock_id    = v.unique_id;
			$expiry             =(v.expiry_date!=null)?v.expiry_date:'';
			$landingCost        =v.landing_cost ? v.landing_cost : 0;
			$cost               =v.cost_price;
			$batch_label        = (v.batch=='' || v.batch==null	|| v.batch=='null')?'No batch':v.batch;
			$batch              = (v.batch=='' || v.batch==null	|| v.batch=='null')?'':v.batch_no;
			$available_qty      =v.stock_in ? formatDecimal(v.stock_in) : 0;
			$transfer_qty =v.transfer_qty ? v.transfer_qty : 0;
			$vendor = (v.vendor_id)?v.vendor_id:0;
			$pending_qty =v.pending_qty ? v.pending_qty : '';
			
			$tax=0;
			$tax_per = v.tax;   
				$tax_method = v.tax_method;
				var pr_tax = item.tax_rate;
                var pr_tax_val = pr_tax_rate = 0;
				
				 if( product_unit != item.row.base_unit) {
					$.each(item.units, function(){
                    if (this.id == product_unit) {
                        base_quantity = formatDecimal(unitToBaseQty($transfer_qty, this), 4);
						$available_qty = formatDecimal(baseToUnitQty($available_qty, this), 4);
						$pending_qty = $available_qty-$transfer_qty;
						$cost   = formatDecimal(unitToBaseQty($cost, this), 4);
						$recipe_price= formatDecimal(unitToBaseQty($recipe_price, this), 4);
                    }
                });
            }
				
				$gross_amt = $transfer_qty*$recipe_price;
				
				
                if (site.settings.tax1 == 1 && (ptax = calculateTax(pr_tax, $cost, item_tax_method))) {
                    pr_tax_val = ptax[0];
                    pr_tax_rate = ptax[1];
                    $tax += pr_tax_val * item_qty;
                }
				if(item.row.variant_id !=0){
					if(v.variant_id!=item.row.variant_id){
						return true;
					}
				}
				
			$grand_total_amt = $gross_amt+$tax;
			$batch_html ='<table style="width: 100%;" class="table items  table-bordered table-condensed batch-table"><thead>';
			$batch_html +='<th>batch</th>';
			$batch_html +='<th>a.qty</th>';
			$batch_html +='<th>t.qty</th>';
			$batch_html +='<th>p.qty</th>';
	    
			$batch_html +='<th>expiry</th>';
			$batch_html +='<th>c.price</th>';
			$batch_html +='<th>s.price</th>';
			$batch_html +='<th>tax</th>';
			$batch_html +='<th>tax amt</th>';
			$batch_html +='<th>gross</th>';
	 
			$batch_html +='<th>total</th>';
			$batch_html +='</thead><tbody>';
			$batch_html +='<tr class="batch-row" data-item='+item_id+' data-batch='+n+'>';
			$batch_html += '<td style="width: 78px;font-size: 13px;">'+$batch_label+'<input type="hidden" name="batch['+item_id+']['+n+'][stock_id]" value="'+$recipe_stock_id+'"><input type="hidden" name="batch['+item_id+']['+n+'][catgory_id]" value="'+v.category_id+'"><input type="hidden" name="batch['+item_id+']['+n+'][subcatgory_id]" value="'+v.subcategory_id+'"><input type="hidden" name="batch['+item_id+']['+n+'][brand_id]" value="'+v.brand_id+'"><input type="hidden" name="batch['+item_id+']['+n+'][landing_cost]" value="'+$landingCost+'"><input type="hidden" name="batch['+item_id+']['+n+'][batch_no]" value="'+$batch+'"><input type="hidden" name="batch['+item_id+']['+n+'][vendor_id]" value="'+v.supplier_id+'"><input type="hidden" name="batch['+item_id+']['+n+'][invoice_id]" value="'+v.invoice_id+'"></td>';
			
			
			
			
			
			$batch_html += '<td  style="width: 78px;"><input type="text" name="batch['+item_id+']['+n+'][available_qty]" value="'+$available_qty+'" class="form-control text-center available-qty" readonly></td>';
			$batch_html += '<td  style="width: 78px;"><input type="text" name="batch['+item_id+']['+n+'][transfer_qty]" value="'+$transfer_qty+'" class="numberonly form-control text-center transfer-qty"><input type="hidden" name="batch['+item_id+']['+n+'][product_unit]"  value="'+product_unit+'"><input type="hidden"  name="batch['+item_id+']['+n+'][base_quantity]" value="'+base_quantity+'"></td>';
	    
			$batch_html += '<td  style="width: 78px;"><input type="text" name="batch['+item_id+']['+n+'][pending_qty]" value="'+$pending_qty+'" readonly class="form-control text-center pending-qty"></td>';
			$batch_html += '<td style="width:62px;font-size: 8px;">'+$expiry+'<input type="hidden" name="batch['+item_id+']['+n+'][expiry]" value="'+$expiry+'"></td>';
			$batch_html += '<td><input type="text" name="batch['+item_id+']['+n+'][cost_price]" readonly value="'+$cost+'" class="form-control text-center cost-price"></td>';
			$batch_html += '<td><input type="text" name="batch['+item_id+']['+n+'][selling_price]" readonly value="'+$recipe_price+'" class="form-control text-center selling-price"></td>';
			$batch_html += '<td>'+formatDecimal($tax_per)+'%<input type="hidden" name="batch['+item_id+']['+n+'][tax]" value="'+$tax_per+'" class="form-control text-center recipe-tax-per"><input type="hidden" name="batch['+item_id+']['+n+'][tax_method]" value="'+$tax_method+'"></td>';
			$batch_html += '<td><span class="recipe-tax-amt-label">'+formatDecimal($tax)+'</span><input type="hidden" name="batch['+item_id+']['+n+'][tax_amount]" value="'+$tax+'" class="form-control text-center recipe-tax-amt"></td>';
			$batch_html += '<td><span class="recipe-gross-amt-label">'+formatDecimal($gross_amt)+'</span><input type="hidden" name="batch['+item_id+']['+n+'][gross]" value="'+$gross_amt+'" class="form-control text-center recipe-gross"></td>';
			$batch_html += '<td><span class="recipe-grand-total-label">'+formatDecimal($grand_total_amt)+'</span><input type="hidden" name="batch['+item_id+']['+n+'][grand_total]" value="'+$grand_total_amt+'" class="form-control text-center recipe-grand-total"></td>';
			$batch_html +='</tr>';
			$batch_html +'</tbody>';
			$batch_html +='</table>';
			$html +=$batch_html;
			$total_no_qty+=parseInt($transfer_qty);
	    });
	}
	    $html +='</td>';
	    $html += '<td class="text-center"><i class="fa fa-times tip podel" id="' + item_id + '" title="Remove" style="cursor:pointer;"></i></td>';
	    $html +='</tr>';
        $('#store_transfersTable>tbody').append($html);
	    $total_no_items++;
        });
         $('#total_no_items').val($total_no_items);
	     $('#total_no_qty').val($total_no_qty);
        set_page_focus();
    }
}


/* -----------------------------
 * Add Purchase Item Function
 * @param {json} item
 * @returns {Boolean}
 ---------------------------- */
 function add_store_transfers_item(item) {
	 if ($('#store_transto_store_id').val()=='') {
	bootbox.alert('Select To Store');
	return false;
    }
    if (item == null)
        return;
   var item_id = item.item_id;
    if (store_transitems[item_id]) {
		bootbox.confirm('This item is already added. Do you want to add it again?', function (result) {
			if (result) {
				var new_qty = parseFloat(store_transitems[item_id].row.qty) + 1;
				store_transitems[item_id].row.base_quantity = new_qty;
				if(store_transitems[item_id].row.unit != store_transitems[item_id].row.base_unit) {
					$.each(store_transitems[item_id].units, function(){
						if (this.id == store_transitems[item_id].row.unit) {
							store_transitems[item_id].row.base_quantity = unitToBaseQty(new_qty, this);
						}
					});
				}
				localStorage.setItem('store_transitems', JSON.stringify(store_transitems));
				loadItems();
				return true;
			}
		});
    } else {
        store_transitems[item_id] = item;
		console.log(store_transitems)
		localStorage.setItem('store_transitems', JSON.stringify(store_transitems));
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



$(document).on('change', '.transfer-qty', function () {
	
    var item_id = $(this).closest('tr').attr('data-item');
    $row = $(this).closest('tr');
    $index =  $(this).closest('tr').attr('data-batch');
	var row = $(this).closest('tr').attr('id');
	var tquantity = 0;
	var pquantity = $row.find('.pending-qty').val();
	var aquantity = $row.find('.available-qty').val();
	if(parseInt(aquantity) < parseInt($(this).val())){
		tquantity = 0;
		bootbox.alert('Transfer quantity is greater than available quantity!', function(){
		$row.find('.transfer_qty').val(tquantity);
		store_transitems[item_id].row.batches[$index].transfer_qty = tquantity;
    		localStorage.setItem('store_transitems', JSON.stringify(store_transitems));
		});
	}else{
	    tquantity = $(this).val();
	    $p_qty = aquantity - tquantity;
	    $row.find('.pending-qty').val($p_qty);
		store_transitems[item_id].row.base_quantity = tquantity;
        if(store_transitems[item_id].row.unit != store_transitems[item_id].row.base_unit) {
            $.each(store_transitems[item_id].units, function(){
                if (this.id == store_transitems[item_id].row.unit) {
                    store_transitems[item_id].row.base_quantity = unitToBaseQty(tquantity, this);
                }
            });
        }
	    store_transitems[item_id].row.batches[$index].transfer_qty = tquantity;
	    store_transitems[item_id].row.batches[$index].pending_qty = $p_qty;
    	localStorage.setItem('store_transitems', JSON.stringify(store_transitems));
	    
       // return;	
	}
    loadItems();
});