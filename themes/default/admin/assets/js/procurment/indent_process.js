//localStorage.removeItem('store_reqitems');
$(document).ready(function(){
     
     /*----------------------
      *on load localstorage
      *---------------------- */
     
     if(localStorage.getItem('from_store')){
          $("#from_store_id").select2("val", localStorage.getItem('from_store'));
     }
     if (localStorage.getItem('processing_from')) {         
          $('#processing_from').select2('val', JSON.parse(localStorage.getItem('processing_from')));
     }if (localStorage.getItem('indent_date')) {         
          $('#indent_date').val(localStorage.getItem('indent_date'));
     }
     
    /* ----------------------
     * Delete Row Method
     * ---------------------- */

     $(document).on('click', '.remove-item', function () {
        var row = $(this).closest('tr');
        var item_id = row.attr('data-item');
        delete store_reqitems[item_id];
        row.remove();
	console.log(store_reqitems)
        if(store_reqitems.hasOwnProperty(item_id)) { } else {
            localStorage.setItem('store_reqitems', JSON.stringify(store_reqitems));
            loadItems();
            return;
        }
    });
     
    $(document).on('change', '.qty', function () {
	
	var item_id = $(this).closest('tr').attr('data-item');
	$row = $(this).closest('tr');
	$qty = $(this).val();
	store_reqitems[item_id].row.qty = $qty;
	localStorage.setItem('store_reqitems', JSON.stringify(store_reqitems));
	loadItems();
    });
     if($('#from_store_id').val()!=''){
          $val = $('#from_store_id').val();
          if ($val!='') {
              loadIndentNo($val);
          }
     }
     $('#from_store_id').change(function(){
          $val = $(this).val();
          if ($val!='') {
              loadIndentNo($val);
               
          }
          
     });
     $("#select-indent").on("select2-selecting", function(e) {
          $val = e.choice.id;
          if ($val!='') {
              $.ajax({
                    type: 'get',
                    url: site.base_url+'procurment/indent_process/getIndentRequestsData',
                    dataType: "json",
                    data: {
			
                        store_id: $('#from_store_id').val(),
                        'indent_id':$val
                    },
                    success: function (data) {

                        localStorage.setItem('indent_date',data.date);
                        $('#indent_date').val(data.date);
                        $items = JSON.stringify(data.req_items);
                        localStorage.setItem('store_reqitems',$items);
                        loadItems();
                    }
                });
          }
          localStorage.setItem('indent_no',$val);
     });
     var load_stock = false;
     $('#load-stock').click(function(){
          if ($('#store_reqTable tbody tr').length==0) {
               bootbox.alert('Please Select Indent Request');
               return false;
          }
          $select_stores = $('#processing_from').val();
          
          if ($select_stores==null) {
              bootbox.alert('Select Stores');return false;
          }else{
               localStorage.setItem('processing_from',JSON.stringify($select_stores));
          }
          
          $.ajax({
                    type: 'post',
                    url: site.base_url+'procurment/indent_process/LoadStock',
                    dataType: "json",
                    data: $('#indent-processing-form').serialize(),
                    success: function (data) {
                         load_stock = true;
                         localStorage.setItem('load_stock',true);
                    if (!data) {
                        bootbox.alert('No Stock');
                    }
                    
                    $.each(store_reqitems,function(nn,vv){
                         store_reqitems[nn].row.stock ='';
                    })
                    $.each(data,function(n,v){
                         store_reqitems[n].row.stock = v;
                    });
                    localStorage.setItem('store_reqitems',JSON.stringify(store_reqitems));
                    loadItems();
                    }
          });
     });
     
     $('#reset').click(function(){
          load_stock = false;
          localStorage.removeItem('store_reqitems');
          localStorage.removeItem('from_store');
          localStorage.removeItem('indent_no');
          localStorage.removeItem('processing_from');
          localStorage.removeItem('indent_date');
          localStorage.removeItem('load_stock');
          window.location.reload();
     });
     
     $('#indent-processing-form .process-indent').click(function(e){
          $(window).unbind('beforeunload');
          
          $error = false;
          $errors = '';
         /* else*/
     $('.from_store_id').removeClass('procurment-input-error');
     $('.indent-request-dropdown').removeClass('procurment-input-error');
     $('.processing_from').removeClass('procurment-input-error');
     $fromstore = $('#from_store_id').select2().val();
     if ($fromstore=='') {
          $error = true;
          $errors += '<p>Select From Store</p>';
          $('.from_store_id').addClass('procurment-input-error');
     }
     $indent_no = $('#select-indent').select2().val();
     if ($indent_no=='') {
          $error = true;
          $errors += '<p>Select Indent</p>';
          $('.indent-request-dropdown').addClass('procurment-input-error');
     }
     $processing_from = $('#processing_from').select2().val();
     console.log($processing_from)
     if ($processing_from=='' || $processing_from==null) {
          $error = true;
          $errors += '<p>Select Processing Store</p>';
          $('.processing_from').addClass('procurment-input-error');
     }
	 /*code modified on 29.8.19 for validation */
	 //transfer_stock
	 $t_stock=$('.t_stock').val();
	 if ($t_stock=='' || $t_stock==null) {
          $error = true;
          $errors += '<p>Select Transfer Stock</p>';
     }
     /*-------------------*/
     if (!localStorage.getItem('load_stock') && !load_stock) {
          $error = true;
          $errors += '<p>Load Stock</p>';
     }
    if ($('.order-item-row').length==0) {
          $error = true;
          $errors += '<p>Products should not be empty</p>';
     }
     //alert($('.order-item-row').length);
     //alert($fromstore);return false;
     
    
    if ($error) {      
        e.preventDefault();
        //$("html, body").animate({ scrollTop: $('.procurment-input-error:eq(0)').offset().top }, 1000);
        bootbox.alert($errors);
        return false;   
    }else{
    $('#indent-processing-form').submit();
    }
   });
       
})
// If there is any item in localStorage
if (localStorage.getItem('store_reqitems')) {
    loadItems();
}
function loadIndentNo($val) {
          $('#processing_from option').prop('disabled',false);
          $('#processing_from').select2();
          $("#select-indent").select2("val", "");
          $("#select-indent").empty();
          if ($val!='') {
              $.ajax({
                    type: 'get',
                    url: site.base_url+'procurment/indent_process/getStoreIndentRequests',
                    dataType: "json",
                    data: { store_id: $val},
                    success: function (data) {
                        console.log(data);
                         $.each(data,function(n,v){
                              var newOption = new Option(v.reference_no, v.id, false, true);
                              $('#select-indent').append(newOption);
                         });
						$('#select-indent').prepend('<option selected=""></option>').select2({placeholder: "Select  "});
                         if(localStorage.getItem('indent_no')){
                              $indent_id = localStorage.getItem('indent_no');
                              $('#select-indent').select2("val", $indent_id);
                         }
                    }
                });
			   $('#processing_from option[value='+$val+']').remove();
               $('#processing_from').select2();
          }
          localStorage.setItem('from_store',$val);
}
function loadItems() {
	
    if (localStorage.getItem('store_reqitems')) {
        
        total = 0;
        count = 1;
        an = 1;
      
        $("#store_reqTable tbody").empty();
		
        store_reqitems = JSON.parse(localStorage.getItem('store_reqitems'));
	sortedItems = store_reqitems;
       // sortedItems = (site.settings.item_addition == 1) ? _.sortBy(store_transitems, function(o){return [parseInt(o.order)];}) : store_transitems;
        var order_no = new Date().getTime();
	$row_index = 0;
	$total_no_items = 0;
	$total_no_qty = 0;
        $.each(sortedItems, function () {			
	    var item = this;
	    $product_grand_total_amt = 0;
	    $product_gross_amt=0;
	    $product_tax=0;
	    //store_transitems[item_id] = item;
            var item_id = item.unique_id;
	    item.order = item.order ? item.order : new Date().getTime();
            $sno = ++$row_index;
            $id = item.row.id;
            $product_name = item.row.name;
            $product_code = item.row.code;
            $product_type = item.row.type;
            $qty = parseFloat(item.row.qty);
	    $stock = item.row.stock?item.row.stock:{};
	    
	    $html = '<tr class="order-item-row warning" data-item="'+item_id+'">';
            $html += '<td>'+$sno+'<input type="hidden" name="product_id[]" value="'+$id+'"></td>';
            $html += '<td>'+$product_code+'<input type="hidden" name="product_code[]" value="'+$product_code+'"></td>';
            $html += '<td>'+$product_name+'<input type="hidden" name="product_name[]" value="'+$product_name+'"><input type="hidden" name="product_type[]" value="'+$product_type+'"></td>';
	    
	    $html += '<td><input type="text" name="qty[]" value="'+$qty+'" readonly class="numberonly form-control text-center qty"></td>';
            $html += '<td>';
            if ($stock) {
               
            
            $.each($stock,function(n,v){
               $.each(v,function(nn,vv){
              
               $html +='<div>';
               $html +='<label class="stock-store-name">'+vv.store_name+'</label>';
               $html +='<input type="text" name="" readonly value="'+vv.available_stock+'" class="a_stock">';
               $html +='<input type="hidden" name="stock['+item_id+']['+vv.store_id+'][store_id]" value="'+vv.store_id+'"><input type="text" name="stock['+item_id+']['+vv.store_id+'][t_stock]" value="'+$qty+'" class="numberonly t_stock">';
               $html +='</div>';
               });
            });
            }else{
               $html +='<div>';
               $html +='<label class="stock-store-name">No Stock Available</label>';
               $html +='</div>';
            }
            $html += '</td>';
	    $html += '<td class="text-center"><i class="fa fa-times tip podel remove-item" id="' + item_id + '" title="Remove" style="cursor:pointer;"></i></td>';
	    $html +='</tr>';
            $('#store_reqTable tbody').append($html);
	    
	    $total_no_items++;
	    $total_no_qty+=parseInt($qty);
        });
       
        $('#total_no_items').val($total_no_items);
	$('#total_no_qty').val($total_no_qty);
	count++;
        set_page_focus();
    }
}
 function add_invoice_item(item) {

    if (count == 1) {
        store_reqitems = {};
	
        if ($('#store_reqwarehouse').val()) {
            $('#store_reqcustomer').select2("readonly", true);
            $('#store_reqwarehouse').select2("readonly", true);
			$('#store_reqfrom_store_id').select2("readonly", true);
			$('#store_reqto_store_id').select2("readonly", true);
			$('#store_reqstore').select2("readonly", true);
        } else {
            bootbox.alert(lang.select_above);
            item = null;
            return;
        }
    }
    if (item == null)
        return;
    
   // var item_id = site.settings.item_addition == 1 ? item.item_id : item.id;
	
    var item_id = item.unique_id;
    if (store_reqitems[item_id]) {

        var new_qty = parseFloat(store_reqitems[item_id].row.qty) + 1;
        store_reqitems[item_id].row.base_quantity = new_qty;
        if(store_reqitems[item_id].row.unit != store_reqitems[item_id].row.base_unit) {
            $.each(store_reqitems[item_id].units, function(){
                if (this.id == store_reqitems[item_id].row.unit) {
                    store_reqitems[item_id].row.base_quantity = unitToBaseQty(new_qty, this);
                }
            });
        }
        store_reqitems[item_id].row.qty = new_qty;

    } else {
	item.row.qty = 1;
        store_reqitems[item_id] = item;
    }
    store_reqitems[item_id].order = new Date().getTime();
    localStorage.setItem('store_reqitems', JSON.stringify(store_reqitems));

    loadItems();
    return true;
	
}