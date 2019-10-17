var disabled_weekdays = [];
$(document).ready(function(){
    $dis_applied = {};
    
    /**** recipe group hide.show - both add/edit *****/ 
    
    $(document).on('click','.list-group',function(e){
            e.preventDefault();
            $obj = $(this);
            //console.log(55)
            $thislist = $obj.parents('.discount-container').find('.recipe-group-list').toggle();
            $('.recipe-group-list').not($obj.parents('.discount-container').find('.recipe-group-list')).hide();
            //if ($obj.parent('div').find('.recipe-group-list')) {
            //    //code
            //}
            console.log($dis_applied)
            $obj.parents('.discount-container').find('.recipe-group-list input[type="checkbox"].recipe-item-days').each(function(){
                $thisobj = $(this);
                $v = $thisobj.val();
                
                $d_val = $thisobj.closest('li').find('.recipe-item').val();
                $day_val = $thisobj.val();
                $v= $d_val+'-'+$day_val;
                $i = $thisobj.attr('data-index');
                $thisobj.attr('disabled',false);
                $thisobj.parent('div').removeAttr('title');
                //console.log($dis_applied[$v])
                if($dis_applied[$v] != undefined && $dis_applied[$v]!=$i){
                 $index_v = $dis_applied[$v].split('-');
                 //console.log('$i'+$i)
                 //console.log('$v'+$dis_applied[$v])
                 console.log($dis_applied[$v])
                 
                 $thisobj.parent('div').css('background-position','-97px 0');
                 $dis_val = $('input[name="group['+$index_v[0]+'][discount]"]').val();
                 console.log($thisobj)
                 //$thisobj.parent('div').attr('title','Applied '+$dis_val+'%');
                 $thisobj.next('label').attr('title','Applied '+$dis_val+'%');
                 $thisobj.next('label').attr('data-title','Applied '+$dis_val+'%');  
                 $thisobj.attr('disabled','disabled');
                 $thisobj.addClass('disabled-day');
                 //alert(5)
                 //$thisobj.attr('checked',true);
                 //  $(this).iCheck('disable')
                }else if($dis_applied[$v] == undefined){//!$.inArray($v, $dis_applied )
                    //$thisobj.iCheck('uncheck');
                    console.log('am here')
                    //$thisobj.attr('disabled',false);
                    $thisobj.next('label').removeAttr('title');
                    $thisobj.next('label').attr('data-title','');  
                    $thisobj.attr('checked',false);
                    $thisobj.removeClass('disabled-day');
                    if (!$thisobj.hasClass('weekday-disabled')) {
                           $thisobj.attr('disabled',false);
                    }
                    
                }
                if ($day_val) {
                    if(disabled_weekdays.indexOf($day_val)>-1){
                        $thisobj.attr('disabled',true);
                        $thisobj.addClass('weekday-disabled');
                        $thisobj.next('label').attr('title','Not Applicable');
                    }else{
                        $thisobj.removeClass('weekday-disabled');
                        if (!$thisobj.hasClass('disabled-day')) {
                           $thisobj.attr('disabled',false);
                        }
                    }
                }
            });
            
            $obj.parents('.discount-container').find('.recipe-group-list input[type="checkbox"].subgroup-item-excluded:checked').each(function(n,v){
                $obj1 = $(this);
                if ($obj1.is(':checked')) {
                    $obj1.closest('li.level-2-menu-li').find('input.recipe-item-days').attr('checked',false);
                    $obj1.closest('li.level-2-menu-li').find('input.recipe-item-days').attr('disabled',true);
                }else{
                    $obj1.closest('li.level-2-menu-li').find('input.recipe-item-days').attr('disabled',false);
                }
        });
    });
    /****** Recipe group select/unselect *****/
    $(document).on('ifChanged','.icheckbox_square-blue input.recipe-item-days:not(.disabled-day)', function (e) {
    //$(".icheckbox_square-blue input").on('ifChanged', function (e) {
      $this = $(this);
      $d_val = $this.closest('li').find('.recipe-item').val();
      $day_val = $this.val();
      $val = $d_val+'-'+$day_val;
      if (($this).is(':checked')) {
          //$val = $this.val();
          $index = $this.attr('data-index');
          $dis_applied[$val] = $index;
      }else{
          //$val = $this.val();
          delete $dis_applied[$val];
      }
      //if ($(".discount-container:first .recipe-group").length==$(".discount-container:first .recipe-group:checked").length) {
      //    $('#apply-all').iCheck('check');
      //}else{
      //    $('#apply-all').iCheck('uncheck');
      //}
      console.log('not working')
      console.log($dis_applied)
    });
    $(document).on('click','input.recipe-item-days',function (e) {
    //$(".icheckbox_square-blue input").on('ifChanged', function (e) {
      $this = $(this);
       $d_val = $this.closest('li').find('.recipe-item').val();
      $day_val = $this.val();
      $val = $d_val+'-'+$day_val;
      if (($this).is(':checked')) {
          //$val = $this.val();
          $this.next('label').css({background: '#2AD705',color: '#ffffff'});
          $index = $this.attr('data-index');
          $dis_applied[$val] = $index;
      }else{
          //$val = $this.val();
          $this.next('label').css({background: '#dddddd',color: '#000000'});
          delete $dis_applied[$val];
      }
      //if ($(".discount-container:first .recipe-group").length==$(".discount-container:first .recipe-group:checked").length) {
      //    $('#apply-all').iCheck('check');
      //}else{
      //    $('#apply-all').iCheck('uncheck');
      //}
      console.log('working')
      console.log($dis_applied)
    });
    
    $(document).on('click','.subgroup-item-excluded',function(){
        $obj = $(this);
        if ($obj.is(':checked')) {
            $obj.closest('li.level-2-menu-li').find('input.recipe-item-days').attr('checked',false);
            $obj.closest('li.level-2-menu-li').find('input.recipe-item-days').attr('disabled',true);
        }else{
            $obj.closest('li.level-2-menu-li').find('input.recipe-item-days').attr('disabled',false);
            $obj.closest('li.level-2-menu-li').find('input.recipe-item:checked').each(function(){
                $obj1 =$(this);
                $obj1.closest('li').find('input.recipe-item-days').attr('checked','checked');
            });
        }
    })
    /****** Remove discount *****/
    $(document).on('click','.remove-discount',function(e){
        e.preventDefault();
        $obj = $(this);//console.log(22);
        $('.recipe-group-list').not($obj.parents('.discount-container').find('.recipe-group-list')).hide();
        $index = $obj.attr('data-index');
        bootbox.confirm({
            message: "Do You want to remove this discount?",
            buttons: {
                confirm: {
                    label: 'Yes',
                    className: 'btn-success'
                },
                cancel: {
                    label: 'No',
                    className: 'btn-danger'
                }
            },
                    callback: function (result) {
                        $existing = false;
                       if($obj.parents('.discount-container').hasClass('existing-discount')){
                        $existing = true;
                       }
                       $obj.parents('.discount-container').remove();
                       //console.log($dis_applied);
                        for(var i in $dis_applied){
                            //console.log($dis_applied[i]);
                            $remove_index = $dis_applied[i].split('-');
                            if($remove_index[0]==$index){
                                $('input[data-index="'+$dis_applied[i]+'"]').attr('checked',false);
                                delete $dis_applied[i];
                            }
                        }
                       //console.log($dis_applied);
                       if ($existing) {
                        bootbox.alert('Discount has been removed successfully.Please submit form');
                       }else{
                        bootbox.alert('Discount has been removed successfully.');
                       }
                      
                    }
        });
    });
    //$(document).bind('submit','#edit-cus-dis-form',function(e){
    //    $index = 1;
    //    $dis_error = [];
    //    $group_error = [];
    //    $('.discount-container:visible').each(function(i,v){
    //        if($(this).find('input[type="text"].discount').val()==''){
    //            $dis_error.push($index);
    //        }
    //        if ($(this).find('.recipe-group:checked').length==0) {
    //            $group_error.push($index);
    //        }
    //        $index++;
    //    });
    //    if ($dis_error.length>0 || $group_error.length>0) {
    //        e.preventDefault(); e.stopImmediatePropagation();
    //        bootbox.alert('Discount field should not be empty. Please select atleast one group or else remove');
    //        $('#edit-cus-dis-form input[type="submit"]').attr('disabled',false);
    //        return false;
    //    }else{
    //        //console.log(55)
    //        //$('#edit-cus-dis-form').submit();
    //        return true;
    //    }
    //    return false;
    //    
    //});
    $(".numberonly").keypress(function (event){
    
    if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
        event.preventDefault();
    }
  
    });
    $('#from_date,#to_date').datetimepicker({format: 'yyyy-mm-dd', fontAwesome: true, todayBtn: 1, autoclose: 1, minView: 2,startDate: new Date() });
    //$('#from_time,#to_time').datetimepicker({
    //                
    // pickDate: false,
    ////minuteStep: 15,
    //pickerPosition: 'bottom-right',
    //format: 'hh:ii',
    //autoclose: true,
    //defaultViewDate	:'today',
    //calendarWeeks:false,
    //////showMeridian: true,
    //startView: 1,
    //maxView: 1,
    //            });
    $(document).on('focus','#from_time,#to_time', function() {
        date = new Date();
         today = new Date(date.getFullYear(), date.getMonth(), date.getDate());
        //$('.datetimepicker-hours .table-condensed thead .switch,').css('visibility','hidden !important')
        $(this).datetimepicker({format: "hh:ii", fontAwesome: true,  autoclose: 1,  forceParse: 0,
                startView: 1,
                maxView: 1,
                startDate: today});
       
    });
    $(document).on('click','.category-name-container',function(e){
        console.log(e.target.className)
        if (e.target.className=="category-name-container" || e.target.className=="category-name") {
            if ($(this).closest('.level-1-menu-li').find('.level-2-menu').is(':visible')) {
                $(this).closest('.level-1-menu-li').find('.level-2-menu').hide();
                $(this).find('.subgroup_hide_show i').removeClass('fa-minus-circle');
            }else{
                 $(this).closest('.level-1-menu-li').find('.level-2-menu').show();
                 $(this).find('.subgroup_hide_show i').addClass('fa-minus-circle');
            }
        }       
        
    });
    $(document).on('click','.subgroup-strip',function(e){
        console.log(e)
        
        $class = e.target.className.split(' ');console.log($class);
        if ($class[0]=="subgroup-strip" || $class[0]=="subgroup-name") {
            if ($(this).closest('.level-2-menu-li').find('.level-3-menu').is(':visible')) {
                $(this).closest('.level-2-menu-li').find('.level-3-menu').hide();
                $(this).find('.recipe_hide_show i').removeClass('fa-minus-circle');
            }else{
                $(this).closest('.level-2-menu-li').find('.level-3-menu').show();
                $(this).find('.recipe_hide_show i').addClass('fa-minus-circle');
            }
        }       
        
    });
   
    $(document).on('ifChanged','.icheckbox_square-blue input.r_weekdays', function (e) {
        $obj = $(this);
        console.log($obj);
        $w_code = $obj.attr('data-code');
        var isChecked = e.currentTarget.checked;
                            
        if (isChecked == true) {
            var index = disabled_weekdays.indexOf($w_code);
            if (index > -1) {
              disabled_weekdays.splice(index, 1);
            }
              $('input[value="'+$w_code+'"].recipe-item-days').removeClass('weekday-disabled');
              if (!$('input[value="'+$w_code+'"].recipe-item-days').hasClass('disabled-day')) {
                //$('input[value="'+$w_code+'"].recipe-item-days').attr(y'checked',true);
                $('input[value="'+$w_code+'"].recipe-item-days').attr('disabled',false);
                $('input[value="'+$w_code+'"].recipe-item-days').next('label').removeAttr('title');
              }else{
                $title = $('input[value="'+$w_code+'"].recipe-item-days').next('label').attr('data-title');
                console.log($title)
                $('input[value="'+$w_code+'"].recipe-item-days').next('label').attr('title',$title);
              }
            
        }else{
            $('input[value="'+$w_code+'"].recipe-item-days').addClass('weekday-disabled');
            $('input[value="'+$w_code+'"].recipe-item-days').next('label').attr('title','Not Applicable');
            $('input[value="'+$w_code+'"].recipe-item-days').attr('disabled',true);
            disabled_weekdays.push($w_code);
        }
        console.log(disabled_weekdays);
    });
    $('.r_weekdays').each(function(){
        $('.recipe-group-list').hide('slow');
        if (!$(this).is(':checked')) {
            $w_code = $(this).attr('data-code');
            disabled_weekdays.push($w_code);
        }
    })
    
})