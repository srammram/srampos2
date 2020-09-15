$(document).ready(function () {
$('body a, body button').attr('tabindex', -1);
check_add_item_val();
if (site.settings.set_focus != 1) {
    $('#add_item').focus();
}


//****************** add items ****************//
$("#add_item").autocomplete({
            
            source: function (request, response) {
                $.ajax({
                    type: 'get',
                    url: site.base_url+'wastage/suggestions',
                    dataType: "json",
                    data: {
                        term: request.term,
                       
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
                    var row = add_wastage_item(ui.item);
                    if (row)
                        $(this).val('');
                } else {
                    //audio_error.play();
                    bootbox.alert('no match found');
                }
            }
        });
////******************* add items end ************************************//


    // clear localStorage and reload
    $('#reset').click(function (e) {
        bootbox.confirm(lang.r_u_sure, function (result) {
            if (result) {
                if (localStorage.getItem('wastage_items')) {
                    localStorage.removeItem('wastage_items');
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
     * Delete Row Method
     * ---------------------- */

     $(document).on('click', '.podel', function () {
        var row = $(this).closest('tr');
        var item_id = row.attr('data-item');
        delete wastage_items[item_id];
        row.remove();
        if(wastage_items.hasOwnProperty(item_id)) { } else {
            localStorage.setItem('wastage_items', JSON.stringify(wastage_items));
            loadItems();
            return;
        }
    });

 if (localStorage.getItem('wastage_items')) {
    loadItems();
}
function loadItems() {
    if (localStorage.getItem('wastage_items')) {
        total = 0;
        count = 1;
        an = 1;
        product_tax = 0;
        invoice_tax = 0;
        product_discount = 0;
        order_discount = 0;
        total_discount = 0;
        $("#wastageitemtables tbody").empty();
        wastage_items = JSON.parse(localStorage.getItem('wastage_items'));
		sortedItems = wastage_items;
		$row_index = 0;
		$total_no_items = 0;
		$total_no_qty = parseInt(0);$wastage_qty=0;
        var order_no = new Date().getTime();
        $.each(sortedItems, function () {
	        var item = this;	       
			$product_grand_total_amt = 0;
	        $product_gross_amt=0;
	        $product_tax=0;
            var item_id = item.item_id;
	        item.order = item.order ? item.order : new Date().getTime();
            $sno = ++$row_index;
            $id = item.row.id;
            $product_name = item.row.name;
            $product_code = item.row.code;
            $product_type = item.row.type;
			item_tax_method = item.row.tax_method;
			$product_tax_per = item.row.tax;
			$product_tax_method = item.row.tax_method;
            $qty = item.row.qty;
			$ex_qty = (item.row.ex_qty!=undefined)?item.row.ex_qty:0;
			$request_qty =item.row.request_qty ? item.row.request_qty : 0;
			$html = '<tr class="order-item-row warning" data-item="'+item_id+'">';
            $html += '<td>'+$sno+'<input type="hidden" name="product_id[]" value="'+$id+'"></td>';
            $html += '<td>'+$product_code+'<input type="hidden" name="product_code[]" value="'+$product_code+'"></td>';
            $html += '<td>'+$product_name+'<input type="hidden" name="product_name[]" value="'+$product_name+'"><input type="hidden" name="product_type[]" value="'+$product_type+'"><input type="hidden" name="variant_id[]" value="'+item.row.variant_id+'"><input type="hidden" name="catgory_id[]" value="'+item.row.category_id+'"><input type="hidden" name="subcatgory_id[]" value="'+item.row.subcategory_id+'"><input type="hidden" name="brand_id[]" value="'+item.row.brand+'"></td>';
		//	$html += '<td><input type="text" name="request_qty[]" value="'+formatDecimals($request_qty)+'" class="form-control text-center request-qty" readonly></td>';
			$html +='<td colspan="11">';
			
	    if (item.row.batches) {
	      $.each(item.row.batches,function(n,v){
			var product_unit = item.row.unit, base_quantity = item.row.base_quantity;
			$recipe_price = v.selling_price;
			$recipe_stock_id = v.unique_id;
			$expiry =(v.expiry_date!=null)?v.expiry_date:'';
			$landingCost =v.landing_cost ? v.landing_cost : 0;
			$cost =v.cost_price;
			$batch_label = (v.batch=='' || v.batch==null	|| v.batch=='null')?'No batch':v.batch;
			$batch  = (v.batch=='' || v.batch==null	|| v.batch=='null')?'':v.batch_no;
			$available_qty =v.stock_in ? formatDecimal(v.stock_in) : 0;
			$wastage_qty =v.wastage_qty ? v.wastage_qty : 0;
			$vendor = (v.vendor_id)?v.vendor_id:0;
			$pending_qty =v.pending_qty ? v.pending_qty : '';
			$gross_amt = $wastage_qty*$recipe_price;
			$tax=0;
			$tax_per = v.tax;   
				$tax_method = v.tax_method;
				var pr_tax = item.tax_rate;
                var pr_tax_val = pr_tax_rate = 0;
                if (site.settings.tax1 == 1 && (ptax = calculateTax(pr_tax, $cost, item_tax_method))) {
                    pr_tax_val = ptax[0];
                    pr_tax_rate = ptax[1];
                    $tax += pr_tax_val * item_qty;
                }
				console.log(v.variant_id+"||"+item.row.variant_id);
				if(item.row.variant_id !=0){
					if(v.variant_id!=item.row.variant_id){
						return true;
					}
				}
				
				 if( product_unit != item.row.base_unit) {
					$.each(item.units, function(){
                    if (this.id == product_unit) {
                        base_quantity = formatDecimal(unitToBaseQty($wastage_qty, this), 4);
						$available_qty=formatDecimal(baseToUnitQty($available_qty, this), 4);
						$pending_qty=$available_qty-$wastage_qty;
                    }
                });
            }
			
			$grand_total_amt = $gross_amt+$tax;
			$batch_html ='<table style="width: 100%;" class="table items  table-bordered table-condensed batch-table"><thead>';
			$batch_html +='<th>batch</th>';
			$batch_html +='<th>a.qty</th>';
			$batch_html +='<th>w.qty</th>';
			$batch_html +='<th>p.qty</th>';
	    
			$batch_html +='<th>expiry</th>';
			$batch_html +='<th>c.price</th>';
			$batch_html +='<th>s.price</th>';
		//	$batch_html +='<th>tax</th>';
		//	$batch_html +='<th>tax amt</th>';
			$batch_html +='<th>gross</th>';
	 
			$batch_html +='<th>total</th>';
			$batch_html +='</thead><tbody>';
			$batch_html +='<tr class="batch-row" data-item='+item_id+' data-batch='+n+'>';
			$batch_html += '<td style="width: 78px;font-size: 13px;">'+$batch_label+'<input type="hidden" name="batch['+item_id+']['+n+'][stock_id]" value="'+$recipe_stock_id+'"><input type="hidden" name="batch['+item_id+']['+n+'][landing_cost]" value="'+$landingCost+'"><input type="hidden" name="batch['+item_id+']['+n+'][batch_no]" value="'+$batch+'"><input type="hidden" name="batch['+item_id+']['+n+'][vendor_id]" value="'+v.supplier_id+'"><input type="hidden" name="batch['+item_id+']['+n+'][invoice_id]" value="'+v.invoice_id+'"></td>';
			$batch_html += '<td  style="width: 78px;"><input type="text" name="batch['+item_id+']['+n+'][available_qty]" value="'+$available_qty+'" class="form-control text-center available-qty" readonly></td>';
			$batch_html += '<td  style="width: 78px;"><input type="text" name="batch['+item_id+']['+n+'][wastage_qty]" value="'+$wastage_qty+'" class="numberonly form-control text-center wastage-qty"><input type="hidden" name="batch['+item_id+']['+n+'][product_unit]"  value="'+product_unit+'"><input type="hidden"  name="batch['+item_id+']['+n+'][base_quantity]" value="'+base_quantity+'"></td>';

	    
			$batch_html += '<td  style="width: 78px;"><input type="text" name="batch['+item_id+']['+n+'][pending_qty]" value="'+$pending_qty+'" readonly class="form-control text-center pending-qty"></td>';
	    
			$batch_html += '<td style="width:62px;font-size: 8px;">'+$expiry+'<input type="hidden" name="batch['+item_id+']['+n+'][expiry]" value="'+$expiry+'"></td>';
	    
			$batch_html += '<td><input type="text" name="batch['+item_id+']['+n+'][cost_price]" readonly value="'+$cost+'" class="form-control text-center cost-price"></td>';
			$batch_html += '<td><input type="text" name="batch['+item_id+']['+n+'][selling_price]" readonly value="'+$recipe_price+'" class="form-control text-center selling-price"></td>';
			//$batch_html += '<td>'+formatDecimal($tax_per)+'%<input type="hidden" name="batch['+item_id+']['+n+'][tax]" value="'+$tax_per+'" class="form-control text-center recipe-tax-per"><input type="hidden" name="batch['+item_id+']['+n+'][tax_method]" value="'+$tax_method+'"></td>';
		//	$batch_html += '<td><span class="recipe-tax-amt-label">'+formatDecimal($tax)+'</span><input type="hidden" name="batch['+item_id+']['+n+'][tax_amount]" value="'+$tax+'" class="form-control text-center recipe-tax-amt"></td>';
			$batch_html += '<td><span class="recipe-gross-amt-label">'+formatDecimal($gross_amt)+'</span><input type="hidden" name="batch['+item_id+']['+n+'][gross]" value="'+$gross_amt+'" class="form-control text-center recipe-gross"></td>';
	    
			$batch_html += '<td><span class="recipe-grand-total-label">'+formatDecimal($grand_total_amt)+'</span><input type="hidden" name="batch['+item_id+']['+n+'][grand_total]" value="'+$grand_total_amt+'" class="form-control text-center recipe-grand-total"></td>';
	    
			$batch_html +='</tr>';
	    
			$batch_html +'</tbody>';
			$batch_html +='</table>';
			$html +=$batch_html;
			$total_no_qty+=parseInt($wastage_qty);
	    });
	}
	    $html +='</td>';
	    $html += '<td class="text-center"><i class="fa fa-times tip podel" id="' + item_id + '" title="Remove" style="cursor:pointer;"></i></td>';
	    $html +='</tr>';
	    
        $('#wastageitemtables>tbody').append($html);
	    $total_no_items++;
	    
        });
		
         $('.total_no_items').val($total_no_items);
	     $('.total_no_qty').val($total_no_qty);
        set_page_focus();
    }
}


/* -----------------------------
 * Add Purchase Item Function
 * @param {json} item
 * @returns {Boolean}
 ---------------------------- */
 function add_wastage_item(item) {
	 
    if (item == null)
        return;
   var item_id = item.item_id;
    if (wastage_items[item_id]) {
		bootbox.confirm('This item is already added. Do you want to add it again?', function (result) {
			if (result) {
				var new_qty = parseFloat(wastage_items[item_id].row.qty) + 1;
				wastage_items[item_id].row.base_quantity = new_qty;
				if(wastage_items[item_id].row.unit != wastage_items[item_id].row.base_unit) {
					$.each(wastage_items[item_id].units, function(){
						if (this.id == wastage_items[item_id].row.unit) {
							wastage_items[item_id].row.base_quantity = unitToBaseQty(new_qty, this);
						}
					});
				}
				localStorage.setItem('wastage_items', JSON.stringify(wastage_items));
				loadItems();
				return true;
			}
		});
    } else {
        wastage_items[item_id] = item;
		localStorage.setItem('wastage_items', JSON.stringify(wastage_items));
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



$(document).on('change', '.wastage-qty', function () {
    var item_id = $(this).closest('tr').attr('data-item');
    $row = $(this).closest('tr');
    $index =  $(this).closest('tr').attr('data-batch');
	var row = $(this).closest('tr').attr('id');
	var wquantity = 0;
	var pquantity = $row.find('.pending-qty').val();
	var aquantity = $row.find('.available-qty').val();
	if(parseInt(aquantity) < parseInt($(this).val())){
		wquantity = 0;
		bootbox.alert('Wasatage quantity is greater than available quantity!', function(){
		$row.find('.wastage_qty').val(wquantity);
		wastage_items[item_id].row.batches[$index].wastage_qty = wquantity;
    		localStorage.setItem('wastage_items', JSON.stringify(wastage_items));
		});
	}else{
	    wquantity = $(this).val();
	    $p_qty = aquantity - wquantity;
	    $row.find('.pending-qty').val($p_qty);
		wastage_items[item_id].row.base_quantity = wquantity;
        if(wastage_items[item_id].row.unit != wastage_items[item_id].row.base_unit) {
            $.each(wastage_items[item_id].units, function(){
                if (this.id == wastage_items[item_id].row.unit) {
			
                    wastage_items[item_id].row.base_quantity = unitToBaseQty(wquantity, this);
                }
            });
        }
	    wastage_items[item_id].row.batches[$index].wastage_qty = wquantity;
	    wastage_items[item_id].row.batches[$index].pending_qty = $p_qty;
    	localStorage.setItem('wastage_items', JSON.stringify(wastage_items));
	    
		}
    loadItems();
});

});