 	
   <div class="tableright_s col-sm-12 col-xs-12">
    <ul  class="nav nav-pills">
	  <?php if(!empty($areas)){ $i=1;     foreach($areas as $areas_row){  ?>
											<li class="<?php if($i==1){ echo 'active';}  ?>"><a  href="#<?php echo $i;  ?>a" data-toggle="tab"><?php echo $areas_row->areas_name; ?></a></li>
											
	  <?php  $i++; }  }   ?>
										</ul>
<div class="tab-content clearfix">
    <?php    
    if(!empty($areas)){ $i=1;
        foreach($areas as $areas_row){
    ?>
       <div class="tab-pane <?php if($i==1){ echo 'active';}  ?>" id="<?php echo $i;  ?>a">
      
       <div class="tableright">
        <?php
        if(!empty($areas_row->tables)){
            foreach($areas_row->tables as $tables){
                
				if($this->sma->actionPermissions('table_add')){
				
                // $table_status = $this->site->orderTablecheck($tables->table_id);

                /*if($table_status == 'Available'){
                    $disabled = '';
                    $class = 'green_ribbon';
                    $main_class = 'green_class';
                }elseif($table_status == 'In_Kitchen' || $table_status == 'READY'){
                    $disabled = '';
                    $class = 'blue_ribbon';
                    $main_class = 'blue_class';
                }elseif($table_status == 'SERVED'){
                    $disabled = '';
                    $class = 'orange_ribbon';
                    $main_class = 'orange_class';
                }elseif($table_status == 'PENDING'){
                    $disabled = '';
                    $class = 'red_ribbon';
                    $main_class = 'red_class';
                }elseif($table_status == 'Ongoingothers'){
                    $disabled = 'disabled';
                    $class = 'red_ribbon';
                    $main_class = 'red_class';
                }else{
                    $disabled = '';
                    $class = '';
                }
				}else{   $disabled = ''; } */ 

                $table_status = $tables->current_order_status;
                $current_order_user = $tables->current_order_user;
                $user_id = $this->session->userdata('user_id');
               $Alluseraccess = $this->site->getGroupPermissionsAlluseraccess($this->session->userdata('group_id'));  
               // var_dump($Alluseraccess);
               // if($Alluseraccess != 0)   {          
                if($table_status == 0){
                    $available_color = $this->pos_settings->table_available_color;
                    $available_color = (explode("/",$available_color));

                        $table_status = 'Available';
                        $disabled = '';
                        $class = $available_color[0];
                        $main_class = $available_color[1];
                    }elseif($table_status == 1 ){
                        $kitchen_color = $this->pos_settings->table_kitchen_color;
                        $kitchen_color = (explode("/",$kitchen_color));
                        $table_status = 'In_Kitchen';
                        $disabled = '';
                        $class = $kitchen_color[0];
                        $main_class = $kitchen_color[1];
                    }elseif($table_status == 2){
                        $table_status = 'READY';
                        $disabled = '';
                        $class = 'blue_ribbon';
                        $main_class = 'blue_class';
                    }elseif($table_status == 3){
                        $table_status = 'SERVED';
                        $disabled = '';
                        $class = 'orange_ribbon';
                        $main_class = 'orange_class';
                    }elseif($table_status == 4){
                        $pending_color = $this->pos_settings->table_pending_color;
                        $pending_color = (explode("/",$pending_color));
                        $table_status = 'PENDING';
                        $disabled = '';
                        $class = $pending_color[0];
                        $main_class = $pending_color[1];
                    }else{
                        $disabled = '';
                        $class = '';
                    }                
                 // }else{   $disabled = ''; } 
            }else{
                $table_status = 'Ongoingothers';
                        $disabled = 'disabled';
                        $class = 'red_ribbon';
                        $main_class = 'red_class';

            }
            if($this->pos_settings->table_size == 0){
                $table_class="table_id_small";
            }else{
                $table_class="table_id_big";
            }            
           ?>         
                <div class="item" id="table-<?=$tables->table_id?>">
                    <button type="button" <?php echo $disabled; ?>  class="table_id <?=$table_class;?> <?php echo $main_class; ?>" value="<?php echo $tables->table_id ?>">
                         <img src="<?=$assets?>images/table_hun.png" alt="table select">
                         <!-- <?php if($this->Settings->user_language == 'english'){ ?>
                         <p><?php echo $tables->table_name; ?></p>
                        <?php } ?> -->
                        <?php if($this->Settings->user_language != 'english' && $tables->native_name !=''){ ?>
                            <p><?php echo $tables->native_name; ?></p>
                        <?php }else{ ?>
                            <p><?php echo $tables->table_name; ?></p>
                        <?php } ?>
                         <div class="ribbon <?php echo $class; ?>"><span><?php echo $table_status;  ?></span></div>
                    </button>
                </div>
           <?php  

            }
        }
        ?>
       </div>
       
       </div>
    <?php
	$i++;
        }
    }
    ?>
    <div class="left">
		  <button id="left-button">
			<i class="fa fa-chevron-circle-left fa-2x" aria-hidden="true"></i>
		  </button>
		</div>
		<div class="right">
			<button id="right-button">
				<i class="fa fa-chevron-circle-right fa-2x" aria-hidden="true"></i>
			</button>
		</div>
    </div>
  
</div>

<script>
const slider = document.querySelector('.tableright');
let isDown = false;
let startX;
let scrollLeft;

slider.addEventListener('mousedown', (e) => {
  isDown = true;
  slider.classList.add('active');
  startX = e.pageX - slider.offsetLeft;
  scrollLeft = slider.scrollLeft;
});
slider.addEventListener('mouseleave', () => {
  isDown = false;
  slider.classList.remove('active');
});
slider.addEventListener('mouseup', () => {
  isDown = false;
  slider.classList.remove('active');
});
slider.addEventListener('mousemove', (e) => {
  if(!isDown) return;
  e.preventDefault();
  const x = e.pageX - slider.offsetLeft;
  const walk = (x - startX) * 3; //scroll-fast
  slider.scrollLeft = scrollLeft - walk;
  console.log(walk);
});
</script>
<style>
	#left-button,#right-button{width: 60px;height: 80px;border-radius: 4px;border-color: transparent;}
	#left-button:hover,#left-button:focus,#right-button:hover,#right-button:focus{background-color: #543816;transition: 0.2s all ease-in;}
	#left-button:hover .fa,#left-button:focus .fa,#right-button:hover .fa,#right-button:focus .fa{color: #fff;}
	#left-button:hover,#left-button:focus,#right-button:hover,#right-button:focus{outline: none;box-shadow: none;}
.left{
	position: absolute;right: 5%; bottom: 7.4%;
}
.right{
 position: absolute;right: 0px;bottom: 7.4%;
}
	.tableright_s{margin-bottom: 2px;padding: 0px;}
	.table_id_small p{white-space: pre-line;margin-top: 0px;}
</style>
 <!--<script type="text/javascript" src="<?=$assets?>js/jquery-2.0.3.min.js"></script> --->
<script>
   $('#right-button').click(function() {
      event.preventDefault();
      $('.tableright').animate({
        scrollLeft: "+=300px"
      }, "slow");
   });
   
     $('#left-button').click(function() {
      event.preventDefault();
      $('.tableright').animate({
        scrollLeft: "-=300px"
      }, "slow");
   });
</script>