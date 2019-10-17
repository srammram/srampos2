<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
  <link rel="stylesheet" href="<?= $assets ?>styles/jquery-ui.css">
  <!-- <link rel="stylesheet" href="<?= $assets ?>styles/jquery.fancybox.min.css"> -->
  <script src="<?= $assets ?>js/jquery-ui.js"></script>
  <!-- <script src="<?= $assets ?>js/jquery.fancybox.min.js"></script> -->
  <script src="<?= $assets ?>js/audioPlayer.js"></script>
<?php

$v = "";

if ($this->input->post('start_date')) {
    $v .= "&start_date=" . $this->input->post('start_date');
}
if ($this->input->post('end_date')) {
    $v .= "&end_date=" . $this->input->post('end_date');
}

?>

<style type="text/css">
    .gallery {
  display: inline-block;
  margin-top: 20px;
}

.fancybox-opened .fancybox-title {
  background: #fff;
  color: #000;
  border: 18px solid #000;
  width: 100%;
  margin-bottom: 98px;
}

.audiofile {
  border: 10px solid #000;
  padding: 14px;
  position: relative;;
  top: -98px;
}
.fancybox-next { right: -45px !important; }
.fancybox-prev { left: -45px !important; }
</style>

<script type="text/javascript">
    $(document).ready(function () {
        $('#form').hide();
        <?php if ($this->input->post('customer')) { ?>
        $('#customer').val(<?= $this->input->post('customer') ?>).select2({
            minimumInputLength: 1,
            data: [],
            initSelection: function (element, callback) {
                $.ajax({
                    type: "get", async: false,
                    url: site.base_url + "customers/suggestions/" + $(element).val(),
                    dataType: "json",
                    success: function (data) {
                        callback(data.results[0]);
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

        $('#customer').val(<?= $this->input->post('customer') ?>);
        <?php } ?>
        $('.toggle_down').click(function () {
            $("#form").slideDown();
            return false;
        });
        $('.toggle_up').click(function () {
            $("#form").slideUp();
            return false;
        });
    });
</script>


<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-heart"></i><?= lang('feedback_details'); ?> <?php
            if ($this->input->post('start_date')) {
                echo "From " . $this->input->post('start_date') . " to " . $this->input->post('end_date');
            }
            ?>
        </h2>

        <div class="box-icon">
            <ul class="btn-tasks">
                <li class="dropdown">
                    <a href="#" class="toggle_up tip" title="<?= lang('hide_form') ?>">
                        <i class="icon fa fa-toggle-up"></i>
                    </a>
                </li>
                <li class="dropdown">
                    <a href="#" class="toggle_down tip" title="<?= lang('show_form') ?>">
                        <i class="icon fa fa-toggle-down"></i>
                    </a>
                </li>
            </ul>
        </div>
        <div class="box-icon">
            <ul class="btn-tasks">
                <li class="dropdown">
                    <a href="#" id="xls" class="tip" title="<?= lang('download_xls') ?>">
                        <i class="icon fa fa-file-excel-o"></i>
                    </a>
                </li>
                <li class="dropdown">
                    <a href="#" id="image" class="tip" title="<?= lang('save_image') ?>">
                        <i class="icon fa fa-file-picture-o"></i>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?= lang('customize_report'); ?></p>
                <div id="form">
                <!-- <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'id' => 'pos-item-sale-report');
             echo admin_form_open("reports/recipe", $attrib);?> -->

                    <div class="row">  
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang("warehouse", "warehouse"); ?>
                                <?php
                                $wh['0'] = lang('all');
                                foreach ($warehouses as $warehouse) {
                                    $wh[$warehouse->id] = $warehouse->name;
                                }
                                echo form_dropdown('warehouse_id', $wh, '', 'class="form-control" data-placeholder="' . lang("select") . ' ' . lang("warehouse") . '" id="warehouse_id" style="width:100%;" ');
                                ?>
                                
                            </div>
                        </div>
                        <!-- <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label" for="user"><?= lang("customer"); ?></label>
                                <?php
                                $cs['0'] = lang('select').' '.lang('customer');
                                foreach ($customers as $customer) {
                                    $cs[$customer->id] = $customer->name . " " . $customer->last_name;
                                }
                                echo form_dropdown('customer', $cs, (isset($_POST['customer']) ? $_POST['customer'] : ""), 'class="form-control" id="customer" data-placeholder="' . $this->lang->line("select") . " " . $this->lang->line("customer") . '"');
                                ?>
                            </div>
                        </div> -->

                        <div class="col-sm-3">
                            <div class="form-group">
                                <?= lang("start_date", "start_date"); ?>
                                <?php echo form_input('start_date', ($this->session->userdata('start_date')), 'class="form-control " autocomplete="off"  id="start_date"'); ?>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <?= lang("end_date", "end_date"); ?>
                                <?php echo form_input('end_date', ($this->session->userdata('end_date')), 'class="form-control "  autocomplete="off" id="end_date"'); ?>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="category">Show</label>
                                <select name="pagelimit" class="form-control select" id="pagelimit" style="width:100px">
                                <option value=""></option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="4">4</option>
                                <option value="10" selected="selected">10</option>
                                <option value="15">15</option>
                                <option value="30">30</option>
                                <option value="100">100</option>
                                <option value="0">All</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div
                            class="controls"> <?php echo form_submit('submit_report', $this->lang->line("submit"), 'class="btn btn-primary home_delivery"'); ?> 
                        </div>
                    </div>
                </div>
                    <!-- <?php echo form_close(); ?> -->
                <div class="clearfix"></div>
                <div class="table-responsive">
                    <table id="FeedBackData"
                           class="table table-bordered table-hover table-striped table-condensed reports-table">
                       <thead>
                          <tr>
                              <th><?= lang("s.no"); ?></th>
                              <th><?= lang("bill_no"); ?></th>
                              <th><?= lang("bill_date"); ?></th>
                              <th><?= lang("table_name")?></th>
                              <th><?= lang("customer_name")?></th>
                              <th><?= lang("remarks")?></th>
                              <th><?= lang("photo"); ?></th>
                              <th><?= lang("media"); ?></th>  
                              <th style="width:85px;"><?= lang("actions"); ?></th>
                              <th style="width:85px;"><?= lang("FB_post"); ?></th> 
                          </tr>
                        </thead>
                        <tbody>                       
                        </tbody>                   
                    </table>
                    <div class="col-md-6 text-right" style="float:right">
                        <div class="dataTables_paginate paging_bootstrap"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="<?= $assets ?>js/html2canvas.min.js"></script>
<script type="text/javascript">
    var $offset = false;
    $(document).ready(function () {

        $('#pdf').click(function (event) {
            event.preventDefault();
            window.location.href = "<?=admin_url('reports/getSalesReport/pdf/?v=1'.$v)?>";
            return false;
        });
        $('#xls').click(function (event) {
            event.preventDefault();
            window.location.href = "<?=admin_url('reports/getSalesReport/0/xls/?v=1'.$v)?>";
            return false;
        });
        $('#image').click(function (event) {
            event.preventDefault();
            html2canvas($('.box'), {
                onrendered: function (canvas) {
                    openImg(canvas.toDataURL());
                }
            });
            return false;
        });
        $(document).on('click', '.pagination a',function(e){
            e.preventDefault();
            $url = $(this).attr("href");
            $url_seg = $url.split('/');
            $count = $url_seg.length-1;           
            $offset = (isNaN($url_seg[$count]))?false:$url_seg[$count];
            GetData($url);
            return false;
        });
        $(document).on('click', '.home_delivery', function () {
            $offset = false;
            $url = '<?=admin_url('reports/get_feedback');?>';
            GetData($url);
        });

        $(document).on('change', '#pagelimit', function () {
            $offset = false;
            $url = '<?=admin_url('reports/get_feedback');?>';
            GetData($url);
        });
           $url = '<?=admin_url('reports/get_feedback');?>';            
           GetData($url);
    });
function GetData($url){              
    var start = $('#start_date').val();
    var end = $('#end_date').val();
    var warehouse_id = $('#warehouse_id').val(); 
    var customer = $('#customer').val();

    var pagelimit = $('#pagelimit').val();
    if (start !='' && end !='') {                  
        $('#start_date,#end_date').css('border-color', '#ccc'); 
        $('#kot').siblings(".select2-container").find('.select2-choice').css('border-color', '#ccc');
                  $.ajax({
                        type: 'POST',
                        url: $url,
                        data: {start: start, end: end, warehouse_id: warehouse_id,customer:customer,pagelimit:pagelimit},
                        dataType: "json",
                        success: function (data) {
                            $('#FeedBackData > tbody').empty();                        

                            if(data.feedback =='empty' || data.feedback == 'error'){
                             
                                  $('#FeedBackData > tbody').append('<tr><td colspan="7" class="dataTables_empty"><?= lang('sEmptyTable') ?></td></tr>');    
                            }
                            else{

                               $('.dataTables_paginate').html(data.pagination);                                    
                                var $row_index = ($offset) ?parseInt($offset)+parseInt(1):1;
                                var image ='';
                                var audio ='';
                                $.each(data.feedback, function (a,b) 
                                {  
                                    var image_link = (b.photo == null || b.photo == '') ? 'no_image.png' : b.photo;
                                    var audio_link = (b.audio == null || b.audio == '') ? 'no_image.png' : b.audio;
                                    /*if(b.photo !=''){*/
                                        image =  '<div class="text-center"><a href="'+site.url+'assets/uploads/testimonial/' + image_link + '" data-toggle="lightbox"><img src="'+site.url+'assets/uploads/testimonial/' + image_link + '" alt="" style="width:30px!important; height:30px!important;" /></a></div>';
                                   /* }
                                    else{
                                         image = '<div class="text-center"><a href="'+site.url+'assets/uploads/testimonial/' + image_link + '" data-toggle="lightbox"><img src="'+site.url+'assets/uploads/testimonial/' + image_link + '" alt="" style="width:30px; height:30px;" /></a></div>';
                                    }*/
                                  /*  if(b.audio !=''){                                       

                                        audio =  '<div class="col-sm-4 col-xs-6 col-md-3 col-lg-3"><a class="thumbnail fancybox" rel="ligthbox" href="'+site.url+'assets/uploads/testimonial/' + audio_link + '"  title=" dolor. " audio-html="<audio autoplay="autoplay" controls="controls"  style="width:30px; height:30px;" ><source src="'+site.url+'assets/uploads/testimonial/' + audio_link + '"  /><div class="text-right"><small class="text-muted">Image Title</small></div></a></div>';
                                    }
                                    else{*/
                                         /*audio =  '<div class="col-sm-4 col-xs-6 col-md-3 col-lg-3"><a class="thumbnail fancybox" rel="ligthbox" href="'+site.url+'assets/uploads/testimonial/' + audio_link + '"  title=" dolor. " audio-html="<audio autoplay="autoplay" controls="controls"  style="width:30px; height:30px;" ><source src="'+site.url+'assets/uploads/testimonial/' + audio_link + '"  /><div class="text-right"><small class="text-muted">Image Title</small></div></a></div>';*/
                                    /*}      */                            
                                    if(b.audio !=''){   
                                        audio = '<audio preload="auto" controls><source src="'+site.url+'assets/uploads/testimonial/' + audio_link + '"></audio>';

                                         /*audio = '<audio id="audio-player" class="d-none" src="'+site.url+'assets/uploads/testimonial/' + audio_link + '" type="audio/mp3" controls="controls"></audio>';*/
                                     

                                       /*audio = '<audio controls><source src="'+site.url+'assets/uploads/testimonial/' + audio_link + '"   type="audio/ogg"><source src="'+site.url+'assets/uploads/testimonial/' + audio_link + '"  type="audio/mpeg">Your browser does not support the audio element.</audio>';*/
                                    }
                                    else{
                                        audio = 'No Media';
                                    }

/*<td class=""><div class="text-center"><a class="tip" title="" href="http://localhost/suki/admin/reports/customer_report/13" data-original-title="View Report"><span class="label label-primary">View Report</span></a></div></td>*/
                                    $post_label = (b.fb_postid!='')?'posted':'post';
                                    $post_class = (b.fb_postid!='')?'posted-feedback':'post-feedback';
                                    $('#FeedBackData > tbody').append('<tr class="text-center"><td >'+$row_index+'</td><td >'+b.bill_number+'</td><td >'+b.bill_date+'</td><td >'+b.table_name+'</td><td >'+b.name+'</td><td style="word-break:break-all;width:50%">'+b.comment+'</td><td>'+image+'</td><td>'+audio+'</td><td class="feedback_link"  split_id="'+b.split_id+'" style="cursor: pointer;"><div class="text-center " ><span class="label label-primary">View Details</span></div></td><td class="'+$post_class+'"  split_id="'+b.split_id+'" style="cursor: pointer;"><div class="text-center " ><span class="label label-primary">'+$post_label+'</span></div></td></tr>');
                                    $row_index++;
                                });
                           } 
                        }
                    });
    }
    else{
        if (start == '') {                    
            $('#start_date').css('border-color', 'red');
        }else{
           $('#start_date').css('border-color', '#ccc'); 
        }
        if (end == '') {                    
            $('#end_date').css('border-color', 'red');
        }else{
            $('#end_date').css('border-color', '#ccc'); 
        }
       /* if (user == '') {  
            $('#user').siblings(".select2-container").find('.select2-choice').css('border-color', 'red');
        } 
        else
        {
            $('#user').siblings(".select2-container").find('.select2-choice').css('border-color', '#ccc');
        }*/
        return false;    
    }  
}   

$(document).ready(function(){
    $("#form").slideDown();
        $('#end_date').datepicker({
        dateFormat: "yy-mm-dd" ,
        maxDate:  0,      
    });
    $("#start_date").datepicker({
        dateFormat: "yy-mm-dd" ,  
        maxDate:  0,      
        onSelect: function(date){            
            var date1 = $('#start_date').datepicker('getDate');           
            var date = new Date( Date.parse( date1 ) ); 
            date.setDate( date.getDate());        
            var newDate = date.toDateString(); 
            newDate = new Date( Date.parse( newDate ) );                      
            $('#end_date').datepicker("option","minDate",newDate);            
        }
    });
});

$(document).ready(function() {
 /* $('.fancybox').fancybox({
    helpers: {
      title: {
        type: 'over'
      }
    },
    afterShow: function(index) {
      var currentItem = $('.thumbnail').eq(this.index);
      var audioHtml = currentItem.attr('audio-html');
      $(".fancybox-title").hide();

      $(".fancybox-title").stop(true, true).slideDown(200);
      var toolbar = $("<div/>").addClass("audiofile");

      toolbar.html(audioHtml);
      $(".fancybox-title").after(toolbar);
    }
  });*/

$('[data-target=#myModal]').attr('data-backdrop',"static");

     $('#myModal').on('hidden.bs.modal', function() {
        $(this).find('.modal-dialog').empty();       
        $(this).removeData('bs.modal');
    });


}); 
 $(document).on('click', '.feedback_link',function(e){
        $('#myModal').modal({remote: site.base_url + 'reports/feedback_view/' + $(this).attr("split_id")});
        $('#myModal').modal('show');        
    });
$(document).on('click', '.post-feedback',function(e){
    e.preventDefault();
    $obj = $(this);
    $.ajax({
        url:site.base_url + 'reports/post_feedback/'+$(this).attr("split_id"),
        dataType:'json',
        success:function(response){
            if (response.status=="success") {
                console.log(5)
                $obj.find('span').text('posted');
                $obj.removeClass('post-feedback');
                $obj.addClass('posted-feedback');
            }else{
                alert(response.msg)
            }
        }
    });
 });
</script>
<script>
            $(function() {
                //$('audio').audioPlayer();
            });
        </script>