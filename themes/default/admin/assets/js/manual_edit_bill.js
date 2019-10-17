$(document).ready(function(){
   localStorage.removeItem('selected_items');
   //$('#LoadModal').modal({
   //        backdrop: 'static',
   //        keyboard: false
   // })
    $('.subcategory-list:visible button:eq(0)').trigger('click');
    $('#add-item').click(function(){
	$('.table.items tbody').html('');
	    $('#LoadModal').modal({
	    backdrop: 'static',
	    keyboard: false
	})
	$('.subcategory-list:visible button:eq(0)').trigger('click');
    });
    
    $('.category').click(function(){
	$this = $(this);
	$('.category').removeClass('active');
	$this.addClass('active');
	$id = $this.attr('data-id');
	$('.carousel.subcategory-list').hide();
	$('.subcat-'+$id).show();
	
	$('.subcat-'+$id+' button:eq(0)').trigger('click');
    });
    
    $('.subcategory').click(function(){
	$this = $(this);
	$category_id = $this.attr('data-cat');
	$subcategory_id = $this.attr('data-sub');
	render_recipes($category_id,$subcategory_id);
	
    });
    //console.log(6666666)
    //console.log(localStorage.getItem('selected_items'));
    var $select_recipes ={};
    $(document).on('click','.recipe',function(){
	$itemarray={};
	
	if (localStorage.getItem('selected_items')!=null) {
	    $select_recipes = JSON.parse(localStorage.getItem('selected_items'));
	}
	$code = $(this).attr('data-code');
	$name = $(this).attr('data-name');
	$price = $(this).attr('data-price');
	$type = $(this).attr('data-type');
	$id = $(this).attr('data-id');
	if ($select_recipes[$code]==undefined) {  	
	    
	    $itemarray['name'] =$name;
	    $itemarray['price'] =$price;
	    $itemarray['subtotal'] =$price;
	    $itemarray['qty'] ='1';
	    $itemarray['type'] =$type;
	    $itemarray['code'] =$code;
	    $itemarray['id'] = $id;
	    $itemarray['order'] = new Date().getTime();
	}else{
	    $itemarray = $select_recipes[$code];
	    $itemarray['qty'] =parseInt($select_recipes[$code]['qty'])+parseInt(1);
	    $itemarray['subtotal'] = parseFloat($price) *  parseFloat($itemarray['qty']);
	    $itemarray['order'] = new Date().getTime();
	}
	$select_recipes[$code] =$itemarray;
	console.log($select_recipes);
	
	localStorage.setItem('selected_items',JSON.stringify($select_recipes));
	loadItems();
	
    });
   
    $(document).on('click','.remove-added-item',function(){
	 $code = $(this).attr('id');
	 $(this).closest('tr').remove();
	 
	 $select_recipes = JSON.parse(localStorage.getItem('selected_items'));
	 delete $select_recipes[$code];
	 localStorage.setItem('selected_items',JSON.stringify($select_recipes));
     
    });
    var new_itemcnt = 1;
    $(document).on('click','#add-items-to-bill',function(){
	$new_items = JSON.parse(localStorage.getItem('selected_items'));
	$bill_id = $('.bill-id').val();
	$item_tax_type = $('.bill-tax-type').val();
	$item_warehouse = $('.bill-warehouse-id').val();
	$order_item_id="";
	$order_id ="";
	var codearray = []; 
	$.each($new_items,function(n,v){
		codearray.push(v.code);
	    $item_id = v.id;
	    $bill_item_id = 'new-'+new_itemcnt;
	    $recipe_name = v.name;	    
	    $recipe_code = v.code;
	    $recipe_type=v.type;	   
	    $unit_price = v.price;
	    $ordered_quantity = 0;
	    $quantity = v.qty;
	    $net_unit_price =v.subtotal;
	    new_itemcnt++;
	    
	    $new_item = '<tr class="newly-added-item item-details item-'+$bill_item_id+' recipeid-'+$item_id+'" data-item="'+$bill_item_id+'">'+
		    '<td style="text-align: center;">'+
			'<a href="" class="remove-item"><i class="fa fa-trash" aria-hidden="true"></i></a>'+
		    '</td>'+
		    '<td>'+
			$recipe_name+
			'<input type="hidden" name="item_bill_id[]" class="item-bill-id" value="'+$bill_id+'" autocomplete="off">'+
			'<input type="hidden" name="item_tax_type[]" class="item-tax-type" value="'+$item_tax_type+'" autocomplete="off">'+
			'<input type="hidden" name="item_warehouse[]" class="item-warehouse" value="'+$item_warehouse+'" autocomplete="off">'+
			'<input type="hidden" name="recipe_id[]" class="recipe-id" value="'+$item_id+'" autocomplete="off">'+
			'<input type="hidden" name="recipe_code[]" class="recipe-code" value="'+$recipe_code+'" autocomplete="off">'+
			'<input type="hidden" name="recipe_name[]" class="recipe-name" value="'+$recipe_name+'" autocomplete="off">'+
			'<input type="hidden" name="recipe_type[]" class="recipe-type" value="'+$recipe_type+'" autocomplete="off">'+
			'<input type="hidden" name="order_item_id[]" class="sale-item-id" value="" autocomplete="off">'+
			'<input type="hidden" name="order_id[]" class="sale-item-order-id" value="" autocomplete="off">'+
		    '</td>'+
		    '<td>'+$ordered_quantity+'<input type="hidden" name="ordered_quantity[]" value="'+$ordered_quantity+'" autocomplete="off"></td>'+
		    '<td><div class="qty_number"><span class="minus ">-</span><input class="form-control change-qty input-sm text-center item-qty" data-id="'+$bill_item_id+'" name="quantity[]" type="text" value="'+$quantity+'" autocomplete="off"><span class="plus ">+</span></div></td>'+
		    '<td class="unit-price-container">'+
			'<span class="unit-price-label">'+$unit_price+'</span>'+
			'<input type="hidden" name="unit_price[]" class="unit-price" value="'+$unit_price+'" autocomplete="off">'+
		    '</td>'+
		    '<td class="net-unit-price-container">'+
			'<span class="net-unit-price-label">'+$net_unit_price+'</span>'+
			'<input type="hidden" name="net_unit_price[]" class="net-unit-price" value="'+$net_unit_price+'" autocomplete="off">'+
		    '</td>'+
		    '<td><span class="manual-discount-label">0.00</span></td>'+
		    '<td>'+
			'<input type="text" name="manual_discount_val[]" class="manual-dis-val" value="" autocomplete="off">'+
			'<input type="hidden" name="manual_discount[]" class="manual-discount" value="0" autocomplete="off">'+
		    '</td>'+
		    '<td class="item-discount-container">'+
			'<span class="item-discount-label">0</span>'+
			'<input type="hidden" class="item-dis-val" value="0" autocomplete="off">'+
			'<input type="hidden" name="item_discount_id[]" class="" value="" autocomplete="off">'+                
			'<input type="hidden" name="item_discount[]" class="item-discount" value="0" autocomplete="off">'+
		    '</td>'+
		    '<td class="offer-discount-container">'+
			'<span class="offer-discount-label">0</span>'+
			
			'<input type="hidden" name="offer_discount[]" class="offer-discount" value="0" autocomplete="off">'+
		    '</td>'+
		    '<td class="customer-discount-container">'+
			'<span class="customer-discount-label"></span>'+
			'<input type="hidden" class="customer-dis-val" value="" autocomplete="off">'+
			'<input type="hidden" name="customer_discount[]" class="customer-discount" value="" autocomplete="off">'+
		    '</td>'+
		    '<td class="total-discount-container">'+
			'<span class="total-discount-label"></span>'+
			'<input type="hidden" name="total_discount[]" class="total-discount" value="" autocomplete="off">'+
		    '</td>'+
		    '<td class="item-tax-container">'+
			'<span class="item-tax-label"></span>'+
			'<input type="hidden" name="item_tax[]" class="item-tax" tax-type="tax-exclusive" value="" autocomplete="off">'+
		    '</td>'+
		    '<td class="item-subtotal-container">'+				    
			'<span class="item-subtotal-label"></span>'+
			'<input type="hidden" name="item_subtotal[]" class="item-subtotal" value="" autocomplete="off">'+
		    '</td>'+				
		'</tr>';
		
		$('#BillEdit tbody').append($new_item);
		calculateITEM($bill_item_id);
	    });
	
	    $('#LoadModal').modal('hide');
	    if ($('select.all-customer-discounts').val()!=0) {
		    getcustomerDis($('select.all-customer-discounts'));
	    }	    
    	$.each(codearray, function(s, p){                    
               delete $new_items[p];
        });	  	    
	    var $empty_items ={};
		localStorage.setItem('selected_items',JSON.stringify($empty_items));
		console.log(JSON.parse(localStorage.getItem('selected_items')));return false;
	    $('html, body').animate({scrollTop:$('#add-item').position().top}, 'slow');
		localStorage.removeItem('selected_items');
    });
    
});

function render_recipes($category_id,$subcategory_id){
    $('.item-list').html('');
    $.ajax({
	    url : base_url+'reports/getRecipes',
	    type:'get',
	    dataType:'json',
	    data:{category_id:$category_id,subcategory_id:$subcategory_id},
	    success:function(data){
		//console.log(data);
		$.each(data,function(n,v){
		    $itemid = v.id;
		    $itemname = v.name;
		    $itemprice = v.price;
		    $itemcode = v.code;
		    $itemtype = v.type;
		    $item = '<button id="recipe-'+$itemid+'" type="button" value="'+$itemcode+'" title="'+$itemname+'" class="btn-img btn-default  recipe pos-tip" data-container="body" data-id="'+$itemid+'" data-name="'+$itemname+'" data-code="'+$itemcode+'" data-type="'+$itemtype+'"  data-price="'+$itemprice+'">';
		    if ($itemname.length>15) {
			$item += '<marquee class="name_strong" behavior="alternate" direction="left" scrollamount="1">&nbsp;&nbsp;'+$itemname+'&nbsp;&nbsp;</marquee>';
		    }else{
			$item +='<span class="name_strong">'+$itemname+'</span>';
		    }
		    $item +='<br><span class="price_strong"> '+formatMoney($itemprice)+'</span> </button>';
		    $('.item-list').append($item);
		})
		
	    }
	})
}
function loadItems() {
    $('.table.items tbody').empty();
    $data  = JSON.parse(localStorage.getItem('selected_items'));
    sortedItems = _.sortBy($data, function(o) { return [parseInt(o.order)]; });
    //console.log($data);
    $.each(sortedItems, function (n,v) {					
            var item = this;
	    $tr = '<tr class="add-item-row" data-code="'+item.code+'">';
	    $tr+='<td>'+item.name+'</td>';
	    $tr+='<td>'+formatMoney(item.price)+'<input type="hidden" name="" class="item-price" value="'+item.price+'"></td>';
	    $tr+='<td><div class="item_qty_number"><span class="minus ">-</span><input class="form-control input-sm text-center kb-pad-qty rquantity input-item-qty ui-keyboard-input ui-widget-content ui-corner-all" data-stock="0" tabindex="2" name="quantity[]" type="text" value="'+item.qty+'"  data-item="'+item.id+'" onclick="this.select();" aria-haspopup="true" role="textbox"><span class="plus ">+</span></div></td>';
	    $tr+='<td><span class="subtotal">'+formatMoney(item.subtotal)+'</span</td>';
	    $tr+='<td><div class="text-center"><span class="remove-added-item label label-danger posdel" id="'+item.code+'" title="Remove" style="cursor:pointer;">void</span></div></td>';
	    $tr+='</tr>';
	    $('.table.items tbody').prepend($tr);
    });
}
function calculatenewSubtotal($parent){
    $qty =  $parent.find('.input-item-qty').val();
    $unit_price =  $parent.find('.item-price').val();
    $s_total = $qty*$unit_price;	   
    $parent.find('.subtotal').text(formatMoney($s_total));
    $data  = JSON.parse(localStorage.getItem('selected_items'));
    $code = $parent.attr('data-code');
    //console.log($data[$code])
    $data[$code]['qty'] = $qty;
    $data[$code]['subtotal'] = $s_total;
    localStorage.setItem('selected_items',{});
    localStorage.setItem('selected_items',JSON.stringify($data));
    //console.log( JSON.parse(localStorage.getItem('selected_items')));
    
    
}