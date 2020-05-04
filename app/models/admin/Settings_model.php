<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Settings_model extends CI_Model{
    public function __construct()
    {
        parent::__construct();
    }
    public function updateLogo($photo)
    {
        $logo = array('logo' => $photo);
        if ($this->db->update('settings', $logo)) {
            return true;
        }
        return false;
    }

    public function updateLoginLogo($photo)
    {
        $logo = array('logo2' => $photo);
        if ($this->db->update('settings', $logo)) {
            return true;
        }
        return false;
    }
	
	/*BBQ*/
	public function getBBQbuyxgetx(){
		$this->db->select('*');
		$q = $this->db->get('bbq_buyx_getx');
		if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
	}
	
	public function addBBQDiscount($data)
    {	
        if($this->db->insert('diccounts_for_bbq', $data)) 
        {
	   		$id = $this->db->insert_id();
            return true;
        }
		return false;
    }
	function getBBQDiscount($id){
		$q = $this->db->get_where("diccounts_for_bbq", array('id' => $id), 1);
		if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	function updateBBQDiscount($id, $data){
		$this->db->where('id', $id);
		if($this->db->update('diccounts_for_bbq', $data)) 
        {
            return true;
        }
		return false;
	}
	
	public function deleteBBQDiscount($id){
        if ($this->db->delete("diccounts_for_bbq", array('id' => $id))) {
	    // $this->db->delete("group_discount", array('cus_discount_id' => $id));           
            return true;
        }
        return FALSE;
    }

	public function getCategoryItems(){
		$this->db->select('recipe_categories.id, recipe_categories.name');
		$this->db->join('recipe', 'recipe.category_id = recipe_categories.id');
		$this->db->where('recipe_categories.parent_id', 0);
		$this->db->group_by('recipe_categories.id');
		$c = $this->db->get('recipe_categories');
		if ($c->num_rows() > 0) {
            foreach (($c->result()) as $row) {
				
				$this->db->select('recipe_categories.id, recipe_categories.name');
				$this->db->join('recipe', 'recipe.subcategory_id = recipe_categories.id');
				$this->db->where('recipe_categories.parent_id', $row->id);
				$this->db->group_by('recipe_categories.id');
				$s = $this->db->get('recipe_categories');
				if ($s->num_rows() > 0) {
            		foreach (($s->result()) as $sow) {
						$this->db->select('recipe.id, recipe.name, recipe.code');
						$this->db->where('recipe.subcategory_id', $sow->id);
						
						$r = $this->db->get('recipe');
						if ($r->num_rows() > 0) {
							foreach (($r->result()) as $rrow) {
								$sow->recipe[] = $rrow;
							}
						}
						$row->subcategory[] = $sow;
					}
				}
				/*$this->db->select('recipe.id, recipe.name, recipe.code');
				$this->db->where('recipe.category_id', $row->id);
				
				$r = $this->db->get('recipe');
				if ($r->num_rows() > 0) {
           			foreach (($r->result()) as $rrow) {
						$row->recipe[] = $rrow;
					}
				}*/
                $data[] = $row;
            }
			
			
            return $data;
        }
		
		return false;	
	}
	
	public function getCategoryItemsSearch(){
		$this->db->select('recipe_categories.id, recipe_categories.name');
		$this->db->join('recipe', 'recipe.category_id = recipe_categories.id');
		$this->db->where('recipe_categories.parent_id', 0);
		$this->db->group_by('recipe_categories.id');
		$q = $this->db->get('recipe_categories');
		if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
				$this->db->select('recipe.id, recipe.name, recipe.code');
				$this->db->where('recipe.category_id', $row->id);
				
				$r = $this->db->get('recipe');
				if ($r->num_rows() > 0) {
           			foreach (($r->result()) as $rrow) {
						$row->recipe[] = $rrow;
					}
				}
                $data[] = $row;
            }
			
			
            return $data;
        }
		
		return false;	
	}
	
	public function getbbqCategoryDay($id){
		$this->db->where('bbq_category_id', $id);
        $q = $this->db->get("bbq_day_discount");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
	}
	public function getbbqCategoryByID($id)
    {
        $q = $this->db->get_where("bbq_categories", array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getbbqCategoryByCode($code)
    {
        $q = $this->db->get_where('bbq_categories', array('code' => $code), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function addbbqCategory($data, $active_data)
    {
        if ($this->db->insert("bbq_categories", $data)) {
            $cid = $this->db->insert_id();
			foreach($active_data as $active){
				$active['bbq_category_id'] = $cid;
				$this->db->insert("bbq_day_discount", $active);
			}
            return $cid;
        }
        return false;
    }

    public function addbbqCategories($data)
    {
        if ($this->db->insert_batch('bbq_categories', $data)) {
            return true;
        }
        return false;
    }

    public function updatebbqCategory($id, $data = array(), $active_data)
    {
        if ($this->db->update("bbq_categories", $data, array('id' => $id))) {
			if(!empty($active_data)){
				$this->db->delete('bbq_day_discount', array('bbq_category_id' => $id));
				foreach($active_data as $active){
					$active['bbq_category_id'] = $id;
					$this->db->insert("bbq_day_discount", $active);
				}
			}
            return true;
        }
        return false;
    }
	
	public function updateBBQItems($id, $data = array())
    {
        if ($this->db->update("bbq_categories", $data, array('id' => $id))) {
			 return true;
		}
		 return false;
	}
	
	public function updateBBQBUY($array_update = array())
    {
		$this->db->delete('bbq_buyx_getx', array('status', 1));
        if ($this->db->insert_batch("bbq_buyx_getx", $array_update)) {
			 return true;
		}
		 return false;
	}

        public function deleteBBQBUYGET($id)
    {
        if ($this->db->delete("bbq_buyx_getx", array('id' => $id))) {
            return true;
        }
        return FALSE;
    }


	public function updateBBQLobsterDiscount($array_update = array())
    {
		$this->db->delete('bbq_lobster_discount', array('status', 1));
        if ($this->db->insert_batch("bbq_lobster_discount", $array_update)) {
			 return true;
		}
		 return false;
	}
    public function deleteLobster($id)
    {
        if ($this->db->delete("bbq_lobster_discount", array('id' => $id))) {
            return true;
        }
        return FALSE;
    }
/*30-08-2019 BBQ daywise Discount Sivan */

public function getBBQdaywisediscount($id)
    {
        $q = $this->db->get_where('bbq_daywise_discount_hd', array('id' => $id), 1);        
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    } 

public function getBBQDaywiseDiscountByID($id){         
        $this->db->select("srampos_bbq_daywise_discount.*");     
        $this->db->join('srampos_bbq_daywise_discount', 'srampos_bbq_daywise_discount.bbq_daywise_discount_hd_id = bbq_daywise_discount_hd.id');
        $this->db->where('srampos_bbq_daywise_discount.bbq_daywise_discount_hd_id', $id);                  
        $q = $this->db->get('bbq_daywise_discount_hd');                          
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
}

    public function AddBBQDaywiseDiscount($data, $bbq_daywise_discount= array())
    {        
        if ($this->db->insert("bbq_daywise_discount_hd", $data)) {
            $bbq_daywise_discount_hd_id = $this->db->insert_id();
            foreach($bbq_daywise_discount as $daywise_discount){
                $daywise_discount['bbq_daywise_discount_hd_id'] = $bbq_daywise_discount_hd_id;
                $this->db->insert("bbq_daywise_discount", $daywise_discount);
            }            
            return $bbq_daywise_discount_hd_id;
        }        
        return false;
    }    

public function updateBBQDaywiseDiscount($id, $data = array(),$bbq_daywise_discount)
    {
        /*echo "<pre>";
        print_r($bbq_daywise_discount);die;*/
        $this->db->where('id', $id);
        if ($this->db->update("bbq_daywise_discount_hd", $data)) {
            $this->db->delete("bbq_daywise_discount", array('bbq_daywise_discount_hd_id' => $id));
            foreach($bbq_daywise_discount as $daywise_discount){
                $daywise_discount['bbq_daywise_discount_hd_id'] = $id;
                $this->db->insert("bbq_daywise_discount", $daywise_discount);
            }   
            // print_r($this->db->error());die;           
            return true;
        }
        // print_r($this->db->error());die;
        return false;
    }

    public function delete_bbq_daywise_discount($id)
    {
        if ($this->db->delete("bbq_daywise_discount_hd", array('id' => $id))) {
            $this->db->delete("bbq_daywise_discount", array('bbq_daywise_discount_hd_id' => $id));
            // print_r($this->db->last_query());die;
            return true;
        }
        // print_r($this->db->last_query());die;
        return FALSE;
    }

    public function change_status_daywisediscount($id,$status)
    {
        $updatestatus =1;
         if($status ==1){
            $updatestatus =0;
         }
        if ($this->db->update("bbq_daywise_discount", array('status' => $updatestatus),array('id' => $id))) {
        // print_r($this->db->last_query());die;        
            return true;
        }        
        // print_r($this->db->last_query());die;
        return FALSE;
    }

/*30-08-2019 BBQ daywise Discount Sivan*/

    public function deletebbqCategory($id)
    {
        if ($this->db->delete("bbq_categories", array('id' => $id))) {
            return true;
        }
        return FALSE;
    }
	/*BBQ END*/
	
	public function getrecipeCategorySuggestions($term, $limit = 10)
    {
		
        $this->db->select("id, (CASE WHEN code = '-' THEN name ELSE CONCAT(code, ' - ', name, ' ') END) as text", FALSE);
        $this->db->where(" (id LIKE '%" . $term . "%' OR name LIKE '%" . $term . "%' OR code LIKE '%" . $term . "%') ");
        $q = $this->db->get('recipe_categories', '', $limit);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
    }
	
	public function getrecipeItemSuggestions($term, $limit = 10)
    {
		
        $this->db->select("id, (CASE WHEN code = '-' THEN name ELSE CONCAT(code, ' - ', name, ' ') END) as text", FALSE);
        $this->db->where(" (id LIKE '%" . $term . "%' OR name LIKE '%" . $term . "%' OR code LIKE '%" . $term . "%') ");
        $q = $this->db->get('recipe', '', $limit);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
    }

    public function getSettings()
    {
        $q = $this->db->get('settings');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getDateFormats()
    {
        $q = $this->db->get('date_format');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function updateSetting($data)
    {
        $this->db->where('setting_id', '1');
        if ($this->db->update('settings', $data)) {
            return true;
        }
        return false;
    }

    public function addTaxRate($data)
    {
        if ($this->db->insert('tax_rates', $data)) {
            return true;
        }
        return false;
    }
	
    public function updateTaxRate($id, $data = array())
    {
        $this->db->where('id', $id);
        if ($this->db->update('tax_rates', $data)) {
            return true;
        }
        return false;
    }
    public function getTaxRateByID($id)
    {
        $q = $this->db->get_where('tax_rates', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    public function getTaxSplitsByTaxid($id)
    {
        $this->db->select('tax_splits.*');
        $this->db->join('tax_rates','tax_rates.id = tax_splits.tax_id');
        $this->db->where('tax_rates.id',$id);
        $q = $this->db->get('tax_splits');    
        // print_r($this->db->last_query());die;    
        if($q->num_rows() > 0)
        {
            foreach (($q->result()) as $row) {
                $data = $row;
            }
            return $data;
        }
        return FALSE;
    }
public function deleteTaxRate($id)
    {
        if ($this->db->delete('tax_rates', array('id' => $id))) {
            return true;
        }
        return FALSE;
    }

    public function addServiceCharge($data)
    {
        if ($this->db->insert('service_charge', $data)) {
            return true;
        }
        return false;
    }
	
    public function updateServiceCharge($id, $data = array())
    {
        $this->db->where('id', $id);
        if ($this->db->update('service_charge', $data)) {
            return true;
        }
        return false;
    }
    
    public function getSercideChargeByID($id)
    {
        $q = $this->db->get_where('service_charge', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    public function deleteSercideCharge($id)
    {
        if ($this->db->delete('service_charge', array('id' => $id))) {
            return true;
        }
        return FALSE;
    }

    public function addDiscount($discount_array = array(), $item_array = array(), $item_type_id1 = array(), $condition_array = array())
    {
        $this->db->insert('discounts', $discount_array);
        $discount_id = $this->db->insert_id();
        if ($discount_id) {
            $i=0;
            foreach($item_array as $item){
                $item['discount_id'] = $discount_id;
                $this->db->insert('discount_items', $item);
                $discount_item_id = $this->db->insert_id();
                
                foreach(explode(',' ,$item_type_id1[$i]) as $item_row){
                    
                    $this->db->insert('discount_item_list', array('discount_item_id' => $discount_item_id, 'item_id' => $item_row));
                }
                $i++;
            }

            foreach($condition_array as $condition){
                $condition['discount_id'] = $discount_id;
                $condition['created'] = date("Y-m-d-H-i-s");
                $this->db->insert('discount_conditions', $condition);
            }
            return true;
        }
        return false;
    }
    public function updateCustomerDiscount($id, $data = array()) {//echo '<pre>';print_R($data);exit;
	$dis['name'] = $data['name'];
	$dis['from_date'] = $data['from_date'];
	$dis['to_date'] = $data['to_date'];
	$dis['from_time'] = $data['from_time'];
	$dis['to_time'] = $data['to_time'];
	$dis['apply_all'] = 0;
	$dis['week_days'] = implode(',', $data['weekdays']) ? implode(',', $data['weekdays']) : '';   
	if(isset($data['apply_all'])) $dis['apply_all'] = 1; 
	$this->db->update("diccounts_for_customer", $dis, array('id' => $data['id']));
	//$this->db->delete('group_discount_conditions', array('discount_id' => $data['id']));
	$this->db->delete('group_discount', array('cus_discount_id'=>$data['id']));
	
	/************* conditons ****************/
	//foreach($data['condition'] as $condition){
	//	    $condition['discount_id'] = $data['id'];
	//	    $condition['days'] = (isset($condition['condition_days']))?implode(',',$condition['condition_days']):'';
	//	    $this->db->insert('group_discount_conditions', $condition);
	//}
	/************* discounts ****************/
	$g_dis['cus_discount_id'] = $data['id'];
	    foreach($data['group'] as $k => $row){
		$g_dis['discount_val'] = $row['discount'];
		$g_dis['discount_type'] = $row['discount_type'];
		if(isset($row['status'])) $g_dis['status'] = 1;
		    //echo '<pre>';print_r($row['recipe_group_id']);
		foreach($row['recipe_group_id'] as $k_1 => $row_1){	
		    $g_dis['recipe_group_id'] = $row_1['id'];
		    foreach($row_1['sub_category'] as $kk => $row_2){
			$g_dis['recipe_subgroup_id'] = $row_2['id'];
			$g_dis['type'] = (isset($row_2['type']))?$row_2['type']:'included';
			$insert = false;
			//if(isset($row_2['all'])) :
			//    $insert = true;
			//    $g_dis['recipe_id'] = 0;
			//else
			if(isset($row_2['recipes'])) :
			//echo '<pre>';print_r($row_2);
			//print_R($row_2['recipes']);
			    $insert = true;
			    $id_days = array();
			    foreach($row_2['recipes'] as $r => $r_row){
				$days = '';
				$id_days[$r_row]['id'] = $r_row; 
				if(isset($row_2['days'][$r_row])){
				  $id_days[$r_row]['days'] = serialize(array_flip($row_2['days'][$r_row]));
				  
				}
				//echo '<pre>';print_R($id_days);
			    }
			    //echo '<pre>';print_R($id_days);
			    $g_dis['recipe_id'] = serialize($id_days);
			   // $g_dis['recipe_id'] = implode(',',$row_2['recipes']);
			endif;
			if($insert) : 
			    //echo '<pre>';print_R($g_dis);
			   $this->db->insert('group_discount', $g_dis); //print_r($this->db->error());exit;
			endif;
		    }
		    
		}//exit;
		//if(isset($_POST['apply_all'])) break;
	}
	//exit;
	return true;
        //if ($this->db->update("diccounts_for_customer", $dis, array('id' => $data['id']))) {
        //    return true;
        //}
        //return false;
    }
    
    public function addCustomerDiscount($data) {	
	$dis['name'] = $data['name'];
	$dis['status'] = 1;
	$dis['from_date'] = $data['from_date'];
	$dis['to_date'] = $data['to_date'];
	$dis['from_time'] = $data['from_time'];
	$dis['to_time'] = $data['to_time'];
	$dis['created_dt'] =  date("Y-m-d-H-i-s");
	$dis['week_days'] = implode(',', $data['weekdays']) ? implode(',', $data['weekdays']) : '';   
	if(isset($data['apply_all'])) $dis['apply_all'] = 1; 
        if($this->db->insert('diccounts_for_customer', $dis)) 
        {
	    $id = $this->db->insert_id();
	    $g_dis['cus_discount_id'] = $id;
	//    foreach($data['condition'] as $condition){
	//	    $condition['discount_id'] = $id;
	//	    $condition['days'] = (isset($condition['condition_days']))?implode(',',$condition['condition_days']):'';
	//	    $this->db->insert('group_discount_conditions', $condition);
	//    }
	//    foreach($data['group'] as $k => $row){
	//	$g_dis['discount_val'] = $row['discount'];
	//	foreach($row['recipe_group_id'] as $k_1 => $row_1){
	//	    $g_dis['recipe_group_id'] = $row_1;
	//	    $this->db->insert('group_discount', $g_dis);
	//	}
	//    }
	foreach($data['group'] as $k => $row){
		$g_dis['discount_val'] = $row['discount'];
		$g_dis['discount_type'] = $row['discount_type'];
		$g_dis['status'] = 1;
		
		foreach($row['recipe_group_id'] as $k_1 => $row_1){
		    $g_dis['recipe_group_id'] = $row_1['id'];
		//    foreach($row_1['sub_category'] as $kk => $row_2){
		//	$g_dis['recipe_subgroup_id'] = $row_2['id'];
		//	$g_dis['status'] = 1;
		//	$g_dis['type'] = (isset($row_2['type']))?$row_2['type']:'included';
		//	$insert = false;
		//	if(isset($row_2['all'])) :
		//	    $insert = true;
		//	    $g_dis['recipe_id'] = 0;
		//	elseif(isset($row_2['recipes'])) :
		//	    $insert = true;
		//	    $g_dis['recipe_id'] = implode(',',$row_2['recipes']);
		//	endif;
		//	if($insert) : 
		//	   // echo '<pre>';print_R($g_dis);
		//	    $this->db->insert('group_discount', $g_dis); //print_r($this->db->error());exit;
		//	endif;
		//    }
		foreach($row_1['sub_category'] as $kk => $row_2){
			$g_dis['recipe_subgroup_id'] = $row_2['id'];
			$g_dis['type'] = (isset($row_2['type']))?$row_2['type']:'included';
			$insert = false;
			//if(isset($row_2['all'])) :
			//    $insert = true;
			//    $g_dis['recipe_id'] = 0;
			//else
			if(isset($row_2['recipes'])) :
			//print_R($row_2['recipes']);
			   $insert = true;
			    $id_days = array();
			    foreach($row_2['recipes'] as $r => $r_row){
				$days = '';
				$id_days[$r_row]['id'] = $r_row; 
				if(isset($row_2['days'][$r_row])){
				  $id_days[$r_row]['days'] = serialize(array_flip($row_2['days'][$r_row]));
				   //$insert = true;
				}
				//echo '<pre>';print_R($id_days);
			    }
			    //echo '<pre>';print_R($id_days);
			    $g_dis['recipe_id'] = serialize($id_days);
			   // $g_dis['recipe_id'] = implode(',',$row_2['recipes']);
			endif;
			if($insert) : 
			    //echo '<pre>';print_R($g_dis);
			    $this->db->insert('group_discount', $g_dis); //print_r($this->db->error());exit;
			endif;
		    }
		    
		}
		//if(isset($_POST['apply_all'])) break;
	}
	    
            return true;
        }
        else
        {        
            return false;
        }
    }

    function updateCusDiscount_status($id,$status){
	$data['status'] = $status;
	$this->db->update("diccounts_for_customer", $data, array('id' => $id));
	return true;
    }

    function updateBBQDiscount_status($id,$status){
	$data['status'] = $status;
	$this->db->update("diccounts_for_bbq", $data, array('id' => $id));
	return true;
    }

    public function deleteCustomerDiscount($id){
        if ($this->db->delete("diccounts_for_customer", array('id' => $id))) {
	    $this->db->delete("group_discount", array('cus_discount_id' => $id));
           /* $this->db->delete("units", array('base_unit' => $id));*/
            return true;
        }
        return FALSE;
    }
	public function addBuy($buy_array = array(), $item_array = array())
    {
    	$this->db->insert('buy_get', $buy_array);
		$buy_get_id = $this->db->insert_id();
        if ($buy_get_id) {
                $item_array['buy_get_id'] = $buy_get_id;
				$this->db->insert('buy_get_items', $item_array);
			
            return true;
        }
        return false;
    }
		public function editBuy($buy_array = array(), $item_array = array(),$id){
		$this->db->where("id",$id);
    	if($this->db->update('buy_get', $buy_array)){
			$this->db->where('buy_get_id',$id);
			$this->db->delete("buy_get_items");
                $item_array['buy_get_id'] = $id;
				$this->db->insert('buy_get_items', $item_array);
			
            return true;
        }
        return false;
    }
	
	public function editDiscount($discount_array = array(), $item_array = array(), $condition_array = array(), $id = NULL){
        $this->db->where(array('id' => $id));
		$this->db->update('discounts',$discount_array);
		$this->db->delete('discount_items', array('discount_id' => $id));
		$this->db->delete('discount_conditions', array('discount_id' => $id));
		$discount_id = $id;
//        if ($discount_id) {
//
//			foreach($item_array as $item){
//				$item['discount_id'] = $discount_id;
//				$this->db->insert('discount_items', $item);
//			}
//			foreach($condition_array as $condition){
//				$condition['discount_id'] = $discount_id;
//				$this->db->insert('discount_conditions', $condition);
//			}			
//            return true;
//        }
	 if ($discount_id) {
            $i=0;
            foreach($item_array as $item){
                $item['discount_id'] = $discount_id;
                $this->db->insert('discount_items', $item);
                $discount_item_id = $this->db->insert_id();
                
                foreach(explode(',' ,$item['item_type_id']) as $item_row){
                    
                    $this->db->insert('discount_item_list', array('discount_item_id' => $discount_item_id, 'item_id' => $item_row));
                }
                $i++;
            }

            foreach($condition_array as $condition){
                $condition['discount_id'] = $discount_id;
                $condition['created'] = date("Y-m-d-H-i-s");
                $this->db->insert('discount_conditions', $condition);
            }
            return true;
        }
        return false;
    }


	
	public function updateDiscount($id, $data = array())
    {
        $this->db->where('id', $id);
        if ($this->db->update('discounts', $data)) {
            return true;
        }
        return false;
    }

    public function getAllTaxRates()
    {
        $q = $this->db->get('tax_rates');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
	public function getAllDiscounts()
    {
        $q = $this->db->get('discounts');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }


	 public function getDiscountByID($id)
    {
        $q = $this->db->get_where('discounts', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
	    $data = $q->row();
	    $data->items_type = $this->db->get_where('discount_items', array('discount_id' => $id))->result();
	    foreach( $data->items_type as $k => $row){
		$data->items_type[$k]->items = $this->db->get_where('discount_item_list', array('discount_item_id' => $row->id))->result();
	    }
	    $data->conditions = $this->db->get_where('discount_conditions', array('discount_id' => $id))->result();
	    //
	    //echo '<pre>';print_R($data);exit;
            return $data;
        }
        return FALSE;
    }
	
 public function getBuyByID($id){
        $q = $this->db->get_where('buy_get', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function addWarehouse($data){
        $this->db->insert('warehouses', $data);//file_put_contents('tttt.txt',json_encode($this->db->error()),FILE_APPEND);
        $insert_id =  $this->db->insert_id();
       return $insert_id;
    }

    public function updateWarehouse($id, $data = array()){
        $this->db->where('id', $id);
        if ($this->db->update('warehouses', $data)) {
            return true;
        }
        return false;
    }
	
	public function getBuyItems($id){
		$this->db->where('buy_get_id', $id);
		$q = $this->db->get('buy_get_items');
        if ($q->num_rows() > 0) {
            /* foreach (($q->result()) as $row) {
                $data[] = $row;
            } */
            return $q->row();
        }
        return FALSE;
	}
    public function getAllWarehouses()
    {
    	$q = $this->db->get('warehouses');
        // $q = $this->db->get_where('warehouses',array('type'=>0));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
     public function getAllStores()
    {
        $q = $this->db->get_where('warehouses',array('type'=>1));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getWarehouseByID($id)
    {
        $q = $this->db->get_where('warehouses', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	
	public function deleteDiscount($id){
        $this->db->delete('discounts', array('id' => $id));
	    $this->db->delete('discount_conditions', array('discount_id' => $id));
	    $itemsType = $this->db->get_where('discount_items', array('discount_id' => $id))->result();
	    $this->db->delete('discount_items', array('discount_id' => $id));
	    foreach($itemsType as $k => $row){
		$this->db->delete('discount_item_list', array('discount_item_id' => $row->id));
	    }
            return true;
        return FALSE;
    }

    public function deleteInvoiceType($id)
    {
        if ($this->db->delete('invoice_types', array('id' => $id))) {
            return true;
        }
        return FALSE;
    }

    public function deleteWarehouse($id)
    {
        if ($this->db->delete('warehouses', array('id' => $id)) && $this->db->delete('warehouses_products', array('warehouse_id' => $id))) {
            return true;
        }
        return FALSE;
    }

    public function addCustomerGroup($data)
    {
        if ($this->db->insert('customer_groups', $data)) {
            return true;
        }
        return false;
    }

    public function updateCustomerGroup($id, $data = array())
    {
        $this->db->where('id', $id);
        if ($this->db->update('customer_groups', $data)) {
            return true;
        }
        return false;
    }

    public function getAllCustomerGroups()
    {
        $q = $this->db->get('customer_groups');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
	public function getAllGroups()
    {
        $q = $this->db->get('groups');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getCustomerGroupByID($id)
    {
        $q = $this->db->get_where('customer_groups', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function deleteCustomerGroup($id)
    {
        if ($this->db->delete('customer_groups', array('id' => $id))) {
            return true;
        }
        return FALSE;
    }

    public function getGroups()
    {
        $this->db->where('id >', 4);
        $q = $this->db->get('groups');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getGroupByID($id)
    {
        $q = $this->db->get_where('groups', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getGroupPermissions($id)
    {
        $q = $this->db->get_where('permissions', array('group_id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    public function getUserByID($id)
    {
        $q = $this->db->get_where('users', array('id' => $id), 1);        
        if ($q->num_rows() > 0) {        	
            return $q->row();
        }
        return FALSE;
    }  
    public function getUserPermissions($id)
    {
        $q = $this->db->get_where('user_permissions', array('user_id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }else{
	    $data['user_id'] = $id;
	    $this->db->insert('user_permissions',$data);
	    $q = $this->db->get_where('user_permissions', array('user_id' => $id), 1);
	    return $q->row();
	}
        return FALSE;
    }
    public function GroupPermissions($id)
    {
        $q = $this->db->get_where('permissions', array('group_id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->result_array();
        }
        return FALSE;
    }

    public function updatePermissions($id, $data = array())
    {
		
        if ($this->db->update('permissions', $data, array('group_id' => $id)) && $this->db->update('users', array('show_price' => $data['products-price'], 'show_cost' => $data['products-cost']), array('group_id' => $id))) {
			
            return true;
        }
        return false;
    }
    public function updateUserPermissions($id, $data = array())
    {
	
	if(!$this->getUserByID($id)){
	    $data['user_id'] = $id;
	    $this->db->insert('user_permissions',$data);
		
	    return $this->db->insert_id();
	}else{
	  if ($this->db->update('user_permissions', $data, array('user_id' => $id))) {
            return true;
	    }  
	}	
        
        return false;
    }

    public function addGroup($data)
    {
        if ($this->db->insert("groups", $data)) {
            $gid = $this->db->insert_id();
            $this->db->insert('permissions', array('group_id' => $gid));
            return $gid;
        }
        return false;
    }

    public function updateGroup($id, $data = array())
    {
        $this->db->where('id', $id);
        if ($this->db->update("groups", $data)) {
            return true;
        }
        return false;
    }


    public function getAllCurrencies()
    {
        $q = $this->db->get('currencies');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getCurrencyByID($id)
    {
        $q = $this->db->get_where('currencies', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function addCurrency($data)
    {
        if ($this->db->insert("currencies", $data)) {
            return true;
        }
        return false;
    }

    public function updateCurrency($id, $data = array())
    {
        $this->db->where('id', $id);
        if ($this->db->update("currencies", $data)) {
            return true;
        }
        return false;
    }

    public function deleteCurrency($id)
    {
        if ($this->db->delete("currencies", array('id' => $id))) {
            return true;
        }
        return FALSE;
    }

public function getShifttimeByID($id)
    {
        $q = $this->db->get_where('shift_time', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }


    public function addShifttime($data)
    {
        if ($this->db->insert("shift_time", $data)) {        	
            return true;
        }        
        return false;
    }

    public function updateShifttime($id, $data = array())
    {
        $this->db->where('id', $id);
        if ($this->db->update("shift_time", $data)) {
            return true;
        }
        return false;
    }
    public function deleteShift($id){
    	 if ($this->db->delete("shift_time", array('id' => $id))) {
            return true;
        }
        return FALSE;
    }


public function getBBQDaywisepriceByID($id){         
        $this->db->select("bbq_menu_day_wise_price.*");     
        $this->db->join('bbq_menu_day_wise_price', 'bbq_menu_day_wise_price.bbq_menu_id = bbq_menu.bbq_menu_id');
        $this->db->where('bbq_menu_day_wise_price.bbq_menu_id', $id);                  
        $q = $this->db->get('bbq_menu');                          
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
}   

public function getBBQProcessByID($id)
    {
        $q = $this->db->get_where('bbq_menu', array('bbq_menu_id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    } 

    public function deleteBBQmenu($id)
    {
         if ($this->db->delete('bbq_menu', array('bbq_menu_id' => $id))) {
            $this->db->delete('bbq_menu_day_wise_price', array('bbq_menu_id' => $id));
            return true;
        }
        return FALSE;
       
    }
    
public function getBBQSalestypes()
    {
        $q = $this->db->get_where('sales_type', array('type' => 'bbq'));
        if ($q->num_rows() > 0) {
            return $q->result();
        }
        return FALSE;
    } 

public function getBBQMenus()
    {
        $q = $this->db->get('bbq_menu');
        if ($q->num_rows() > 0) {
            return $q->result();
        }
        return FALSE;
    } 


    public function addBBQProcess($data, $bbq_days_price)
    {        
        if ($this->db->insert("bbq_menu", $data)) {
            $bbq_menu_id = $this->db->insert_id();
            foreach($bbq_days_price as $days_price){
                $days_price['bbq_menu_id'] = $bbq_menu_id;
                $this->db->insert("bbq_menu_day_wise_price", $days_price);
            }            
            return $bbq_menu_id;
        }        
        return false;
    }

    public function addBBQProcess_old($data) //hide date 28-08-2019 Sivan
    {
        if ($this->db->insert("bbq_menu", $data)) {        	        	
            return true;
        }        
        return false;
    }
        public function updateBBQProcess($id, $data = array(),$bbq_days_price)
    {
        $this->db->where('bbq_menu_id', $id);
        if ($this->db->update("bbq_menu", $data)) {
            $this->db->delete("bbq_menu_day_wise_price", array('bbq_menu_id' => $id));
            foreach($bbq_days_price as $days_price){
                $days_price['bbq_menu_id'] = $id;
                $this->db->insert("bbq_menu_day_wise_price", $days_price);
            }              
            return true;
        }
        return false;
    }

    public function getParentCategories()
    {
        $this->db->where('type',1)
	->where('(parent_id="null" or parent_id=0)');
	//echo $this->db->get_compiled_select();
        $q = $this->db->get("recipe_categories");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getCategoryByID($id)
    {
        $q = $this->db->get_where("recipe_categories", array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getCategoryByCode($code)
    {
        $q = $this->db->get_where('categories', array('code' => $code), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function addCategory($data)
    {
        if ($this->db->insert("recipe_categories", $data)) {
            $cid = $this->db->insert_id();
            return $cid;
        }
        return false;
    }

    public function addCategories($data)
    {
        if ($this->db->insert_batch('categories', $data)) {
            return true;
        }
        return false;
    }

    public function updateCategory($id, $data = array())
    {
        if ($this->db->update("recipe_categories", $data, array('id' => $id))) {
            return true;
        }
        return false;
    }

    public function deleteCategory($id)
    {
        if ($this->db->delete("recipe_categories", array('id' => $id))) {
            return true;
        }
        return FALSE;
    }
	public function getParentrecipeCategories()
    {
        $this->db->where('type',0)
	->where('(parent_id="null" or parent_id=0)');
        $q = $this->db->get("recipe_categories");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getrecipeCategoryByID($id)
    {
        $q = $this->db->get_where("recipe_categories", array('id' => $id,'type'=>0), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getrecipeCategoryByCode($code)
    {
        $q = $this->db->get_where('recipe_categories', array('code' => $code), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function addrecipeCategory($data)
    {
        if ($this->db->insert("recipe_categories", $data)) {
            $cid = $this->db->insert_id();
            return $cid;
        }
        return false;
    }

    public function addrecipeCategories($data)
    {
        if ($this->db->insert_batch('recipe_categories', $data)) {
            return true;
        }
        return false;
    }

    public function updaterecipeCategory($id, $data = array())
    {
        if ($this->db->update("recipe_categories", $data, array('id' => $id))) {
            return true;
        }
        return false;
    }

    public function deleterecipeCategory($id)
    {
        if ($this->db->delete("recipe_categories", array('id' => $id))) {
            return true;
        }
        return FALSE;
    }
    public function getPaypalSettings()
    {
        $q = $this->db->get('paypal');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function updatePaypal($data)
    {
        $this->db->where('id', '1');
        if ($this->db->update('paypal', $data)) {
            return true;
        }
        return FALSE;
    }

    public function getSkrillSettings()
    {
        $q = $this->db->get('skrill');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function updateSkrill($data)
    {
        $this->db->where('id', '1');
        if ($this->db->update('skrill', $data)) {
            return true;
        }
        return FALSE;
    }

    public function checkGroupUsers($id)
    {
        $q = $this->db->get_where("users", array('group_id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function deleteGroup($id)
    {
        if ($this->db->delete('groups', array('id' => $id))) {
            return true;
        }
        return FALSE;
    }

    public function addVariant($data)
    {
        if ($this->db->insert('variants', $data)) {
            return true;
        }
        return false;
    }

    public function updateVariant($id, $data = array())
    {
        $this->db->where('id', $id);
        if ($this->db->update('variants', $data)) {
            return true;
        }
        return false;
    }

    public function getAllVariants()
    {
        $q = $this->db->get('variants');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getVariantByID($id)
    {
        $q = $this->db->get_where('variants', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function deleteVariant($id)
    {
        if ($this->db->delete('variants', array('id' => $id))) {
            return true;
        }
        return FALSE;
    }
	
	public function addSales_type($data)
    {
        if ($this->db->insert('sales_type', $data)) {
            return true;
        }
        return false;
    }

    public function updateSales_type($id, $data = array())
    {
        $this->db->where('id', $id);
        if ($this->db->update('sales_type', $data)) {
            return true;
        }
        return false;
    }

    public function getAllSales_type()
    {
        $q = $this->db->get('sales_type');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getSales_typeByID($id)
    {
        $q = $this->db->get_where('sales_type', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function deleteSales_type($id)
    {
        if ($this->db->delete('sales_type', array('id' => $id))) {
            return true;
        }
        return FALSE;
    }
	
	public function addTills($data)
    {
        if ($this->db->insert('tills', $data)) {
			 $till_id = $this->db->insert_id();
        if ($till_id) {            
            $unique_id = $this->site->generateUniqueTableID($till_id);
            if ($till_id) {
                $this->site->updateUniqueTableId($till_id,$unique_id,'tills');
            }
		}
            return true;
        }
        return false;
    }

    public function updateTills($id, $data = array()){
        $this->db->where('id', $id);
        if ($this->db->update('tills', $data)) {
            return true;
        }
        return false;
    }

    public function getAllTills()
    {
        $q = $this->db->get('tills');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getTillsByID($id)
    {
        $q = $this->db->get_where('tills', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function deleteTills($id)
    {
        if ($this->db->delete('tills', array('id' => $id))) {
            return true;
        }
        return FALSE;
    }

    public function getExpenseCategoryByID($id)
    {
        $q = $this->db->get_where("expense_categories", array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getExpenseCategoryByCode($code)
    {
        $q = $this->db->get_where("expense_categories", array('code' => $code), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function addExpenseCategory($data)
    {
        if ($this->db->insert("expense_categories", $data)) {
            return true;
        }
        return false;
    }

    public function addExpenseCategories($data)
    {
        if ($this->db->insert_batch("expense_categories", $data)) {
            return true;
        }
        return false;
    }

    public function updateExpenseCategory($id, $data = array())
    {
        if ($this->db->update("expense_categories", $data, array('id' => $id))) {
            return true;
        }
        return false;
    }

    public function hasExpenseCategoryRecord($id)
    {
        $this->db->where('category_id', $id);
        return $this->db->count_all_results('expenses');
    }

    public function deleteExpenseCategory($id)
    {
        if ($this->db->delete("expense_categories", array('id' => $id))) {
            return true;
        }
        return FALSE;
    }

    public function addUnit($data)
    {
        if ($this->db->insert("units", $data)) {
            return true;
        }
        return false;
    }

    public function updateUnit($id, $data = array())
    {
        if ($this->db->update("units", $data, array('id' => $id))) {
            return true;
        }
        return false;
    }

    public function getUnitChildren($base_unit)
    {
        $this->db->where('base_unit', $base_unit);
        $q = $this->db->get("units");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function deleteUnit($id)
    {
        if ($this->db->delete("units", array('id' => $id))) {
            $this->db->delete("units", array('base_unit' => $id));
            return true;
        }
        return FALSE;
    }

    public function addPriceGroup($data)
    {
        if ($this->db->insert('price_groups', $data)) {
            return true;
        }
        return false;
    }

    public function updatePriceGroup($id, $data = array())
    {
        $this->db->where('id', $id);
        if ($this->db->update('price_groups', $data)) {
            return true;
        }
        return false;
    }

    public function getAllPriceGroups()
    {
        $q = $this->db->get('price_groups');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getPriceGroupByID($id)
    {
        $q = $this->db->get_where('price_groups', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function deletePriceGroup($id)
    {
        if ($this->db->delete('price_groups', array('id' => $id)) && $this->db->delete('product_prices', array('price_group_id' => $id))) {
            return true;
        }
        return FALSE;
    }

    public function setProductPriceForPriceGroup($product_id, $group_id, $price)
    {
        if ($this->getGroupPrice($group_id, $product_id)) {
            if ($this->db->update('product_prices', array('price' => $price), array('price_group_id' => $group_id, 'product_id' => $product_id))) {
                return true;
            }
        } else {
            if ($this->db->insert('product_prices', array('price' => $price, 'price_group_id' => $group_id, 'product_id' => $product_id))) {
                return true;
            }
        }
        return FALSE;
    }

    public function getGroupPrice($group_id, $product_id)
    {
        $q = $this->db->get_where('product_prices', array('price_group_id' => $group_id, 'product_id' => $product_id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getProductGroupPriceByPID($product_id, $group_id)
    {
        $pg = "(SELECT {$this->db->dbprefix('product_prices')}.price as price, {$this->db->dbprefix('product_prices')}.product_id as product_id FROM {$this->db->dbprefix('product_prices')} WHERE {$this->db->dbprefix('product_prices')}.product_id = {$product_id} AND {$this->db->dbprefix('product_prices')}.price_group_id = {$group_id}) GP";

        $this->db->select("{$this->db->dbprefix('products')}.id as id, {$this->db->dbprefix('products')}.code as code, {$this->db->dbprefix('products')}.name as name, GP.price", FALSE)
        // ->join('products', 'products.id=product_prices.product_id', 'left')
        ->join($pg, 'GP.product_id=products.id', 'left');
        $q = $this->db->get_where('products', array('products.id' => $product_id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function updateGroupPrices($data = array())
    {
        foreach ($data as $row) {
            if ($this->getGroupPrice($row['price_group_id'], $row['product_id'])) {
                $this->db->update('product_prices', array('price' => $row['price']), array('product_id' => $row['product_id'], 'price_group_id' => $row['price_group_id']));
            } else {
                $this->db->insert('product_prices', $row);
            }
        }
        return true;
    }

    public function deleteProductGroupPrice($product_id, $group_id)
    {
        if ($this->db->delete('product_prices', array('price_group_id' => $group_id, 'product_id' => $product_id))) {
            return TRUE;
        }
        return FALSE;
    }

    public function getBrandByName($name)
    {
        $q = $this->db->get_where('brands', array('name' => $name), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function addBrand($data)
    {
        if ($this->db->insert("brands", $data)) {
            $cid = $this->db->insert_id();
            return $cid;
        }
        return false;
    }

    public function addBrands($data)
    {
        if ($this->db->insert_batch('brands', $data)) {
            return true;
        }
        return false;
    }

    public function updateBrand($id, $data = array())
    {
        if ($this->db->update("brands", $data, array('id' => $id))) {
            return true;
        }
        return false;
    }

    public function brandHasProducts($brand_id)
    {
        $q = $this->db->get_where('products', array('brand' => $brand_id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function deleteBrand($id)
    {
        if ($this->db->delete("brands", array('id' => $id))) {
            return true;
        }
        return FALSE;
    }
	
	public function getAllCustomFeedback()
    {
        $q = $this->db->get('customfeedback');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

	public function getCustomFeedbackAnswer($id)
    {
		$this->db->where('question_id', $id);
        $q = $this->db->get('customfeedback_answer');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
    public function getCustomFeedbackByID($id)
    {
        $q = $this->db->get_where('customfeedback', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function addCustomFeedback($data, $data_answer)
    {
		$this->db->insert("customfeedback", $data);
		$question_id = $this->db->insert_id();
        if ($question_id) {
			foreach($data_answer as $data_answer_row){
				$data_answer_row['question_id'] = $question_id;
				$this->db->insert("customfeedback_answer", $data_answer_row);
			}
            return true;
        }
        return false;
    }

    public function updateCustomFeedback($id, $data = array(), $data_answer)
    {
		$this->db->delete("customfeedback_answer", array('question_id' => $id));
        $this->db->where('id', $id);
        if ($this->db->update("customfeedback", $data)) {
			foreach($data_answer as $data_answer_row){
				$data_answer_row['question_id'] = $id;
				$this->db->insert("customfeedback_answer", $data_answer_row);
			}
            return true;
        }
        return false;
    }

    public function deleteCustomFeedback($id)
    {
        if ($this->db->delete("customfeedback", array('id' => $id))) {
            return true;
        }
        return FALSE;
    }
    function getCustomerDiscount($id){
	$q = $this->db->get_where("diccounts_for_customer", array('id' => $id), 1);
	
	if ($q->num_rows() > 0) {
	    $q = $q->row();//print_R($q);
	//     $this->db
	//	->select('*')
	//	->from("group_discount_conditions")
	//	->where(array('discount_id' => $q->id));		
	    $q->conditions = array();//$this->db->get()->result();
	    
	    $this->db
		->select('id,cus_discount_id,discount_val,discount_type,status')
		->from("group_discount")
		->where(array('cus_discount_id' => $q->id))
		->group_by('cus_discount_id,discount_val');
		//->limit(1);
	    $group = $this->db->get()->result();
	    $q->group = $group;
	    //print_R($group);
	    foreach($group as $k => $row){
		 $this->db
		->select()
		->from("group_discount")
		->where(array('cus_discount_id' =>$row->cus_discount_id,'discount_val'=>$row->discount_val));
		$recipe_groups = $this->db->get()->result();
		//print_R($recipe_groups);exit;
		$recipe_subgroups = array();
		foreach($recipe_groups as $kk => $val) {
		    $recipe_subgroups[$val->recipe_subgroup_id] = $val;
		}
		$q->group[$k]->recipe_groups = $recipe_subgroups; 
	    }
	   
	    
	    
	    //$q->r_group = $r_group;
	    //print_R($q);exit;
            return $q;
        }
        return FALSE;
    }
    
    
     public function addPayment_method($data)
    {
        if ($this->db->insert("payment_methods", $data)) {
            $cid = $this->db->insert_id();
            return $cid;
        }
        return false;
    }
    function updatePayment_method_status($id,$status){
	$data['status'] = $status;
	$this->db->update("payment_methods", $data, array('id' => $id));
	return true;
    }
    function UniqueDisExist($d1,$d2,$day,$fromtime,$totime,$id=false){
	$uniqueQuery = "SELECT *,D.id as discount_id,DC.from_date,DC.to_date,DC2.from_time,DC2.to_time,DC1.days from " . $this->db->dbprefix('discounts') . " D";
	$uniqueQuery .= " left JOIN " . $this->db->dbprefix('discount_conditions') . " DC2 ON D.id = DC2.discount_id AND DC2.condition_method ='condition_time'
		left JOIN " . $this->db->dbprefix('discount_conditions') . " DC ON D.id = DC.discount_id AND DC.condition_method ='condition_date'
		left JOIN " . $this->db->dbprefix('discount_conditions') . " DC1 ON D.id = DC1.discount_id AND DC1.condition_method ='condition_days'";
	if($fromtime!='' && $totime!='' && $d1!='' && $d2!='' && $day!=''){
	   $uniqueQuery .= " WHERE  ((DC2.from_time IS NOT NULL AND CAST('".$fromtime."' AS time) BETWEEN DC2.from_time AND DC2.to_time ) OR
		(DC2.to_time IS NOT NULL AND CAST('".$totime."' AS time) BETWEEN DC2.from_time AND DC2.to_time )) AND ((  DC.from_date IS NOT NULL AND DATE(DC.from_date) <=DATE('$d1')  and DATE(DC.to_date) >= DATE('$d1'))
		OR (  DC.from_date IS NOT NULL AND DATE(DC.from_date) <= DATE('$d2') and  DATE(DC.to_date)>= DATE('$d2'))
		) AND FIND_IN_SET('".$day."' ,DC1.days))";
		
	}
	else if($d1!='' && $d2!='' && $day!=''){
	    $uniqueQuery .= " WHERE ((DATE(DC.from_date) <=DATE('$d1')  and DATE(DC.to_date) >= DATE('$d1'))
		OR (DATE(DC.from_date) <= DATE('$d2') and  DATE(DC.to_date)>= DATE('$d2')))
		AND FIND_IN_SET('".$day."' ,DC1.days) AND DC2.from_time IS NULL";
		//OR
		//(DC.from_date IS NULL AND FIND_IN_SET('".$day."' ,DC1.days))";
		
	}else if($fromtime!='' && $totime!='' && $d1!='' && $d2!=''){
	    $uniqueQuery .= " WHERE  ((DC2.from_time IS NOT NULL AND CAST('".$fromtime."' AS time) BETWEEN DC2.from_time AND DC2.to_time ) OR
		(DC2.to_time IS NOT NULL AND CAST('".$totime."' AS time) BETWEEN DC2.from_time AND DC2.to_time )) AND ((  DC.from_date IS NOT NULL AND DATE(DC.from_date) <=DATE('$d1')  and DATE(DC.to_date) >= DATE('$d1'))
		OR (  DC.from_date IS NOT NULL AND DATE(DC.from_date) <= DATE('$d2') and  DATE(DC.to_date)>= DATE('$d2'))
		) AND DC1.days IS NULL";
		
	}else if($fromtime!='' && $totime!='' && $day!=''){
	    $uniqueQuery .= " WHERE  ((DC2.from_time IS NOT NULL AND CAST('".$fromtime."' AS time) BETWEEN DC2.from_time AND DC2.to_time ) OR
		(DC2.to_time IS NOT NULL AND CAST('".$totime."' AS time) BETWEEN DC2.from_time AND DC2.to_time )) AND FIND_IN_SET('".$day."' ,DC1.days) AND DC.from_date IS NULL";
		
	}else if($day!=''){
	    $uniqueQuery .= " WHERE  DC.from_date IS NULL AND  DC2.from_time IS NULL AND FIND_IN_SET('".$day."' ,DC1.days)";
		
	}else if($d1!='' && $d2!=''){
	    $uniqueQuery .= " WHERE ((  DC.from_date IS NOT NULL AND DATE(DC.from_date) <=DATE('$d1')  and DATE(DC.to_date) >= DATE('$d1'))
		OR (  DC.from_date IS NOT NULL AND DATE(DC.from_date) <= DATE('$d2') and  DATE(DC.to_date)>= DATE('$d2'))
		) AND DC1.days IS NULL AND DC2.from_time IS NULL";
	}else if($fromtime!='' && $totime!=''){
	    $uniqueQuery .= " WHERE  ((DC2.from_time IS NOT NULL AND CAST('".$fromtime."' AS time) BETWEEN DC2.from_time AND DC2.to_time ) OR
		(DC2.to_time IS NOT NULL AND CAST('".$totime."' AS time) BETWEEN DC2.from_time AND DC2.to_time ))AND DC1.days IS NULL AND DC.from_date IS NULL";
		
	}
	
	
	
	$uniqueQuery .=" AND D.unique_discount=1";
	if($id){
	    $uniqueQuery .= " AND D.id!=".$id;
	}
	$uniqueQuery .= " order by D.id DESC LIMIT 1";
	$uniqueDaysDis = $this->db->query($uniqueQuery);
	//echo $uniqueQuery;
	//echo '<pre>';print_R($uniqueDaysDis->result());exit;
	if($uniqueDaysDis->num_rows()>0){
	    return $uniqueDaysDis->row();
	}
	return false;
    }
    function updateDiscount_status($id,$status){
    $data['discount_status'] = $status;
    //echo $id;print_R($data);exit;
    $this->db->update("discounts", $data, array('id' => $id));//print_R($this->db->error());exit;
    return true;
    }
    function updateShiftTime_status($id,$status){
    $data['status'] = $status;    
    $this->db->update("srampos_shift_time", $data, array('id' => $id));
    return true;
    }
    
    function updateRecipeCategory_status($id,$status){
    $data['status'] = $status;    
    $this->db->update("recipe_categories", $data, array('id' => $id));
    return true;
    }    

   
    function updateRecipe_feedbackMapping($data){
	$this->db->truncate('recipe_feedback_mapping');
	$this->db->query("ALTER TABLE ".$table." AUTO_INCREMENT = 1");
	$this->db->insert_batch('recipe_feedback_mapping',$data);
	return true;
    }
    function getRecipe_feedbackMapping(){
	$q = $this->db->get('recipe_feedback_mapping')->result();
	$recipeIds = [];
        foreach($q as $k => $row){
          $recipeIds[] = $row->recipe_id;
        }
	return $recipeIds;
    }

/*Store*/

    public function getStoreByID($id)
    {
        $q = $this->db->get_where('pro_stores', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function addStore($data)
    {
        if ($this->db->insert("pro_stores", $data)) {
            return true;
        }
        return false;
    }

    public function updateStore($id, $data = array())
    {
        $this->db->where('id', $id);
        if ($this->db->update("pro_stores", $data)) {
            return true;
        }
        return false;
    }

    public function deleteStore($id)
    {
        if ($this->db->delete("pro_stores", array('id' => $id))) {
            return true;
        }
        return FALSE;
    }
    function getTransactionDate(){
	$curdate = date('Y-m-d');
	$q = $this->db->get_where('transaction_date',array('date(currentdate)'=>$curdate));
	if($q->num_rows()>0){	    
	    return date('Y-m-d', strtotime($q->row('transaction_date')));
	}
	return false;
	
    }
    /*Store*/


    public function addSalesitems_mapping_head($data)
    {
        if ($this->db->insert("sale_items_mapping_head", $data)) {
            $gid = $this->db->insert_id();
            // $this->db->insert('permissions', array('group_id' => $gid));
            return $gid;
        }
        return false;
    }
    public function getALLSaleTypeSaleitemMapping(){
		// $this->db->where('sale_type', $id);
		$this->db->select('sale_items_mapping_head.id,sale_items_mapping_head.days,sales_type.name as sale_type');
    	$this->db->join('sales_type', 'sales_type.id = sale_items_mapping_head.sale_type');
        $q = $this->db->get("sale_items_mapping_head");
        // print_r($this->db->last_query());die;
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
	}

    public function getSaleTypeSaleitemMapping($id){
        $this->db->where('sale_type', $id);
        $q = $this->db->get("sale_items_mapping_head");
        // print_r($this->db->last_query());die;
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getSaleTypefromsalesmapping($id){

        $this->db->select('sales_type.type as sale_type');
        $this->db->join('sales_type', 'sales_type.id = sale_items_mapping_head.sale_type');
        $this->db->where('sale_items_mapping_head.id', $id);
        $q = $this->db->get("sale_items_mapping_head");
        // print_r($this->db->last_query());die;
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }    

    public function getSaleTypeSaleitemMappinghead($id){
    	$this->db->select('sale_items_mapping_head.id,sale_items_mapping_head.days,sales_type.name');
        $this->db->join('sales_type', 'sales_type.id = sale_items_mapping_head.sale_type');    	
		$this->db->where('sale_items_mapping_head.id', $id);
        $q = $this->db->get("sale_items_mapping_head");
        // print_r($this->db->last_query());die;
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data = $row;
            }
            return $data;
        }
        return FALSE;
	}


    function getSalesmapping($id){
	$q = $this->db->get_where("sale_items_mapping_head", array('id' => $id), 1);
	
	if ($q->num_rows() > 0) {		
	    $q = $q->row(); 	    
	    $this->db
		->select('id')
		->from("sale_items_mapping_details")
		->where(array('sales_map_hd_id' => $q->id))
		->group_by('sales_map_hd_id');		
	    $group = $this->db->get()->result();	    
	    $q->group = $group;	 
	    
	    foreach($group as $k => $row){
		 $this->db
		->select()
		->from("sale_items_mapping_details")
		->where(array('sales_map_hd_id' =>$row->id));
		$recipe_groups = $this->db->get()->result();
		
		$recipe_subgroups = array();
		foreach($recipe_groups as $kk => $val) {
		    $recipe_subgroups[$val->recipe_subgroup_id] = $val;
		}
		$q->group[$k]->recipe_groups = $recipe_subgroups; 
	    }
            return $q;
        }
        return FALSE;
    }

    public function addsaleitemsmapping($id,$data) {	    	
    	$q = $this->db->get_where("sale_items_mapping_head", array('id' => $data['sales_map_hd_id']));
    	$add_item_map['sales_map_hd_id'] = $data['sales_map_hd_id'];
    	$add_item_map['day'] = $q->row('days');
		foreach($data['group'] as $k => $row){							
				foreach($row['recipe_group_id'] as $k_1 => $row_1){
					$add_item_map['recipe_group_id'] = $row_1['id'];		
					foreach($row_1['sub_category'] as $k_2 => $row_2){		
					$insert = false;				
						if(!empty($row_2['recipes'])){		
						$insert = true;						
	    					$string_version = implode(',', $row_2['recipes']);
							$add_item_map['recipe_subgroup_id'] = $row_2['id'];
							$add_item_map['recipe_id'] = $string_version;
						}  
						if($insert) : 			    
						$this->db->insert('sale_items_mapping_details', $add_item_map); 
						endif;

					}
                }	return TRUE;
	     }
	}
    public function editsaleitemsmapping($id,$data) {	    	

    	$edit_item_map['sales_map_hd_id'] = $data['sales_map_hd_id'];
        
        $q = $this->db->get_where("sale_items_mapping_head", array('id' => $data['sales_map_hd_id']));
        $edit_item_map['day'] = $q->row('days');
    	if ($this->db->delete("sale_items_mapping_details", array('sales_map_hd_id' => $id))) {
		foreach($data['group'] as $k => $row){							
				foreach($row['recipe_group_id'] as $k_1 => $row_1){
					$edit_item_map['recipe_group_id'] = $row_1['id'];		
					foreach($row_1['sub_category'] as $k_2 => $row_2){		
					$insert = false;				
						if(!empty($row_2['recipes'])){		
						$insert = true;						
	    					$string_version = implode(',', $row_2['recipes']);
							$edit_item_map['recipe_subgroup_id'] = $row_2['id'];
							$edit_item_map['recipe_id'] = $string_version;
						}  
						if($insert) : 			    
						$this->db->insert('sale_items_mapping_details', $edit_item_map); 
						endif;
					}
                }
	     } return TRUE;
	    }
	    return false;
	}	
	public function deletesalemapping($id)
    {
        if ($this->db->delete('sale_items_mapping_details', array('sales_map_hd_id' => $id))) {
        	$this->db->delete('sale_items_mapping_head', array('id' => $id));
            return true;
        }
        return FALSE;
    }
	function delete_buy_x_get_x($id){
		$this->db->where("id",$id);
		if($this->db->delete("buy_get")){
			$this->db->where("buy_get_id",$id);
			$this->db->delete("buy_get_items");
			return true;
		}
		return false;
	}
	function update_buy_x_status($id,$status){
	$data['status'] = $status;
	$this->db->update("buy_get", $data, array('id' => $id));
	return true;
    }
	function get_recipe_variant($recipeid){
		$this->db->select("recipe_variants.*");
		$this->db->join("recipe_variants","recipe_variants.id=recipe_variants_values.attr_id");
		$this->db->where("recipe_variants_values.recipe_id",$recipeid);
		$q=$this->db->get("recipe_variants_values");
		if($q->num_rows()>0){
			foreach($q->result() as $row){
				$data[]=$row;
			}
			return $data;
		}
		return false;
	}
	
	public function addWallets($data)    {
        if ($this->db->insert('wallet_master', $data)) {
            return true;
        }
        return false;
    }

    public function updateWallets($id, $data = array()){
        $this->db->where('id', $id);
        if ($this->db->update('wallet_master', $data)) {
            return true;
        }
        return false;
    }
    public function getWallets(){
        $q = $this->db->get('wallet_master');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getWalletsByID($id){
        $q = $this->db->get_where('wallet_master', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function deleteWallets($id){
		  $this->db->where("id",$id);
        if ($this->db->update('wallet_master', array('active' => 0))) {
            return true;
        }
        return FALSE;
    }
	
	public function addNCKotMater($data)    {
        if ($this->db->insert('nc_kot_type_master', $data)) {
            return true;
        }
        return false;
    }
    public function updateNCKotMaster($id, $data = array()){
        $this->db->where('id', $id);
        if ($this->db->update('nc_kot_type_master', $data)) {
            return true;
        }
        return false;
    }
    public function getNCKotMaster(){
        $q = $this->db->get('nc_kot_type_master');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getNCKotMasterByID($id){
        $q = $this->db->get_where('nc_kot_type_master', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function deleteNCKotMaster($id){
		  $this->db->where("id",$id);
        if ($this->db->update('nc_kot_type_master', array('active' => 0))) {
            return true;
        }
        return FALSE;
    }
}
