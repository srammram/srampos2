<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>


<div id="viewkitchen_<?php echo $orders_list->id; ?>">
    <?php
    //$biller = $this->site->getCompanyOrderByID($orders_list->biller_id);
    
    ?>
   
    <table class="table" >
        <tbody>
			<tr>
				<td ><img src="<?php echo base_url().'assets/uploads/logos/'.$biller->logo; ?>"></td>
			</tr>
			<tr>
				<td ><h2><?=lang('table')?> : <?php echo $store_name; ?></h2></td>
			</tr>
			<tr>
				<td align="center"><h2>KOT ORDER</h2></td>
			</tr>
			<tr>
				<td><p style="font-size:20px !important;"><?=lang('order_number')?> : <?php echo $reference_no; ?></p></td>
			</tr>
			<tr>
				<td><p style="font-size:20px !important;"><?=lang('date')?> : <?php echo $orders_date; ?></p></td>
			</tr>
			<tr>
				<td><p style="font-size:20px !important;"><?=lang('order_person')?> : <?php echo $user;?></p></td>
			</tr>
			<tr>
				<td><p style="font-size:20px !important;"><?=lang('kitchen_type')?> : <?php echo $kitchen_orders_data->name; ?></p></td>
			</tr>
		
		
        
            <tr>
                <th align="left"><p style="font-size:20px !important;"><?=lang('sale_item')?></p></th>
                <th align="left"><p style="font-size:20px !important;"><?=lang('quantity')?></p></th>
            </tr>
			<tr>&nbsp;
			</tr>
       
            <?php 
             foreach($kitchen_orders_data->kit_o as $item){
                
                //$addons = $this->site->getAddonByRecipe($item->recipe_id, $item->addon_id);
                //$get_item =  $this->site->getrecipeByID($item->id);
            ?>
            <tr >
            <?php
                       /* if($this->Settings->user_language == 'khmer'){
                            if(!empty($item->khmer_name)){
                                $recipe_name = $item->khmer_name;
                            }else{
                                $recipe_name = $item->recipe_name;
                            }
                        }else{
                            $recipe_name = $item->recipe_name;
                        }*/
                        ?>
                        <?php // echo $recipe_name; ?>
                <td align="left"><p style="font-size:30px !important;"><?php echo $item['recipe_name']; ?> <br> (<?=$item['en_recipe_name']?>) </p></td>
                <td align="left"><p style="font-size:30px !important;"><?php echo $item['quantity']; ?></p></td>
                
            </tr>
            <?php
             }
            ?>
        </tbody>
     </table>
</div>