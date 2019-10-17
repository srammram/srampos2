 
    <?php
    if(!empty($areas)){
        foreach($areas as $areas_row){
    ?>
       
        <?php
        if(!empty($areas_row->tables)){
            foreach($areas_row->tables as $tables){
                
				if($this->sma->actionPermissions('table_add')){ echo ''; 
				
                $table_status = $this->site->orderTablecheck($tables->table_id);

                if($table_status == 'Available'){
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
				}else{   $disabled = ''; }  

                if($this->pos_settings->table_size == 0){
                $table_class="table_id_small";
                }else{
                    $table_class="table_id_big";
                }

        ?>
        
               
                    <button type="button" <?php echo $disabled; ?>  class="table_id <?=$table_class;?> <?php echo $main_class; ?>" value="<?php echo $tables->table_id ?>">
                         <img src="<?=$assets?>images/table_hun.png" alt="table select">
                         <p><?php echo $tables->table_name; ?></p>
                         <div class="ribbon <?php echo $class; ?>"><span><?php echo $table_status;  ?></span></div>
                    </button>
                
                
        <?php
                
            }
        }
        ?>
       
    <?php
        }
    }
    ?>
    
