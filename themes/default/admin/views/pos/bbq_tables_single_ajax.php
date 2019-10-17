
    
    <?php
    if(!empty($areas)){
        foreach($areas as $areas_row){
    ?>
        
        <?php
        if(!empty($areas_row->tables)){
            foreach($areas_row->tables as $tables){
                
				if($this->sma->actionPermissions('table_add')){ echo ''; 
				
                $table_status = $this->site->orderBBQTablecheck($tables->table_id);
				$bbq_check = $this->site->BBQcheckTable($tables->table_id);
				
                if($table_status == 'Available'){
					
					if(!empty($bbq_check)){
						$disabled = '';
						$class = 'perl_ribbon';
						$main_class = 'perl_class';
						$table_status = 'BBQ';
						$bbq_code = $bbq_check->reference_no;
						$bbq_data = $bbq_check->reference_no;
						$bbq_cancel = 1;
					}else{
						$disabled = '';
						$class = 'green_ribbon';
						$main_class = 'green_class';
						$bbq_code = '';
						$bbq_data = '';
						$bbq_cancel = 0;
					}
                }elseif($table_status == 'In_Kitchen' || $table_status == 'READY'){
                    $disabled = '';
                    $class = 'blue_ribbon';
                    $main_class = 'blue_class';
					$bbq_code = $bbq_check->reference_no;
					$bbq_data = $bbq_check->reference_no;
					$bbq_cancel = 0;
                }elseif($table_status == 'SERVED'){
                    $disabled = '';
                    $class = 'orange_ribbon';
                    $main_class = 'orange_class';
					$bbq_code = $bbq_check->reference_no;
					$bbq_data = $bbq_check->reference_no;
					$bbq_cancel = 0;
                }elseif($table_status == 'PENDING'){
                    $disabled = '';
                    $class = 'red_ribbon';
                    $main_class = 'red_class';
					$bbq_code = $bbq_check->reference_no;
					$bbq_cancel = 0;
					$bbq_data = '';
                }elseif($table_status == 'Ongoingothers'){
                    $disabled = 'disabled';
                    $class = 'red_ribbon';
                    $main_class = 'red_class';
					$bbq_code = $bbq_check->reference_no;
					$bbq_cancel = 0;
					$bbq_data = '';
                }else{
                    $disabled = '';
                    $class = '';
					$bbq_code = '';
					$bbq_data = '';
					$bbq_cancel = 0;
                }
				}else{   $disabled = ''; }  
                 if($this->pos_settings->table_size == 0){
                $table_class="table_id_small";
                }else{
                    $table_class="table_id_big";
                }
        ?>
        
                
                    <button type="button" <?php echo $disabled; ?> style="width:120px; height:120px;"  class="<?=$table_class;?> table_id <?php echo $main_class; ?>" value="<?php echo $tables->table_id ?>" data-split="<?php echo $bbq_code; ?>" dataCustomer="<?= $bbq_check->customer_id  ? $bbq_check->customer_id  : '' ?>">
                         <img src="<?=$assets?>images/table_hun.png" alt="table select">
                         <p><?php echo $tables->table_name; ?><br><?= $bbq_code ? $bbq_code : '' ?></p>
                         <div class="ribbon <?php echo $class; ?>"><span><?php echo $table_status;  ?></span></div>
                         
                          
                    </button>
                    <?php
					if(!empty($bbq_data)){
					?>
                   <a href="javascript:void(0);" data-bbq="<?=$bbq_data?>" class="text-warning cover_edit pull-left"><span class="pull-left" style="width:auto; text-align:center; font-size:12px; padding-left:10px; font-weight:bold;"><i class="fa fa-edit"></i> Edit</span></a>
                   
                   
                   <?php
				   if($bbq_cancel == 1){
				   ?>
                   <a href="javascript:void(0);" data-bbq="<?=$bbq_data?>" class="text-warning cover_cancel pull-right"><span class="pull-left" style="width:auto; text-align:center; font-size:12px; font-weight:bold; padding-right:20px; "><i class="fa fa-trash"></i> Cancel</span></a>
                   <?php
				   }else{
				   ?>
                   <a href="javascript:void(0);" class="text-warning"><span class="pull-right" style="width:auto; text-align:center; font-size:12px; font-weight:bold; padding-right:20px; opacity:0.6"><i class="fa fa-trash"></i> Cancel</span></a>
                   <?php
				   }
				   ?>
                   <?php
					}
				   ?>
        <?php
                
            }
        }
        ?>
       
    <?php
        }
    }
    ?>
