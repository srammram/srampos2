<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Promotions_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }
     public function addBillDiscount($data) {
        ///p($data,1);
	$dis['name'] = $data['name'];
	$dis['status'] = 1;
	$dis['from_date'] = $data['from_date'];
	$dis['to_date'] = $data['to_date'];
	$dis['from_time'] = $data['from_time'];
	$dis['to_time'] = $data['to_time'];
	$dis['created_dt'] =  date("Y-m-d-H-i-s");
	$dis['week_days'] = implode(',', $data['weekdays']) ? implode(',', $data['weekdays']) : '';   
	if(isset($data['apply_all'])) $dis['apply_all'] = 1;
        $this->db->insert('bill_discounts', $dis);
        //p($this->db->error());
        $id = $this->db->insert_id();
        if($id) 
        {
            $assigned_stores = $data['stores'];
            $assignedstores = array();
            p($assigned_stores);
            foreach($assigned_stores as $k => $store_id){
                 $a_stores['store_id'] = $store_id;
                 $a_stores['discount_id'] = $id;
                 array_push($assignedstores,$store_id);
                 $this->db->insert('bill_discount_stores', $a_stores);
                 $this->db->insert_id();
            }
            if(!empty($assignedstores)){
                $this->db->where('discount_id',$id);
                $this->db->where_not_in('store_id',$assignedstores);
                $this->db->delete('bill_discount_stores');
            }
            $g_dis['discount_id'] = $id;
	//    foreach($data['condition'] as $condition){
	//	    $condition['discount_id'] = $id;
	//	    $condition['days'] = (isset($condition['condition_days']))?implode(',',$condition['condition_days']):'';
	//	    $this->db->insert('group_discount_conditions', $condition);
	//    }
	//    foreach($data['group'] as $k => $row){
	//	$g_dis['discount_val'] = $row['discount'];
	//	foreach($row['category_id'] as $k_1 => $row_1){
	//	    $g_dis['category_id'] = $row_1;
	//	    $this->db->insert('bill_discount_group', $g_dis);
	//	}
	//    }
      //  p($data['group'],1);
	foreach($data['group'] as $k => $row){
		$g_dis['discount_val'] = $row['discount'];
		$g_dis['discount_type'] = $row['discount_type'];
		$g_dis['status'] = 1;
		foreach($row['recipe_department_id'] as $k_0 => $row_0){
                    $g_dis['department_id'] = $row_0['id'];
		foreach($row_0['recipe_group_id'] as $k_1 => $row_1){
		    $g_dis['category_id'] = $row_1['id'];
		//    foreach($row_1['sub_category'] as $kk => $row_2){
		//	$g_dis['subcategory_id'] = $row_2['id'];
		//	$g_dis['status'] = 1;
		//	$g_dis['type'] = (isset($row_2['type']))?$row_2['type']:'included';
		//	$insert = false;
		//	if(isset($row_2['all'])) :
		//	    $insert = true;
		//	    $g_dis['product_id'] = 0;
		//	elseif(isset($row_2['recipes'])) :
		//	    $insert = true;
		//	    $g_dis['product_id'] = implode(',',$row_2['recipes']);
		//	endif;
		//	if($insert) : 
		//	   // echo '<pre>';print_R($g_dis);
		//	    $this->db->insert('bill_discount_group', $g_dis); //print_r($this->db->error());exit;
		//	endif;
		//    }
		foreach($row_1['sub_category'] as $kk => $row_2){
			$g_dis['subcategory_id'] = $row_2['id'];
			$g_dis['type'] = (isset($row_2['type']))?$row_2['type']:'included';
			$insert = false;
			//if(isset($row_2['all'])) :
			//    $insert = true;
			//    $g_dis['product_id'] = 0;
			//else
                        p($row_2['brands']);
                        foreach($row_2['brands'] as $bk => $row_3){
                            $g_dis['brand_id'] = $row_3['id'];
                           
                            $insert = false;
                            //////// Recipes - Start ///
                            if(isset($row_3['recipes'])) :
                            //print_R($row_2['recipes']);
                               $insert = true;
                                $id_days = array();
                                foreach($row_3['recipes'] as $r => $r_row){
                                    $days = '';
                                    $id_days[$r_row]['id'] = $r_row; 
                                    if(isset($row_3['days'][$r_row])){
                                      $id_days[$r_row]['days'] = serialize(array_flip($row_3['days'][$r_row]));
                                       //$insert = true;
                                    }
                                    //echo '<pre>';print_R($id_days);
                                }
                                //echo '<pre>';print_R($id_days);
                                $g_dis['product_id'] = serialize($id_days);
                               // $g_dis['product_id'] = implode(',',$row_2['recipes']);
                            endif;
                            ////// Recipes -END /////
                            if($insert) : 
                                //echo '<pre>';print_R($g_dis);
                                $this->db->insert('bill_discount_group', $g_dis); p($this->db->error());
                            endif;
                        }
		    }
		    
		}
                }
              // p($g_dis);
		//if(isset($_POST['apply_all'])) break;
	}
	  // exit;
            return true;
        }
        else
        {        
            return false;
        }
    }

    function updateCusDiscount_status($id,$status){
	$data['status'] = $status;
	$this->db->update("bill_discounts", $data, array('id' => $id));
	return true;
    }

     public function updateBillDiscount($id, $data = array()) {//echo '<pre>';print_R($data);exit;
	$dis['name'] = $data['name'];
	$dis['from_date'] = $data['from_date'];
	$dis['to_date'] = $data['to_date'];
	$dis['from_time'] = $data['from_time'];
	$dis['to_time'] = $data['to_time'];
	$dis['apply_all'] = 0;
	$dis['week_days'] = implode(',', $data['weekdays']) ? implode(',', $data['weekdays']) : '';   
	if(isset($data['apply_all'])) $dis['apply_all'] = 1; 
	$this->db->update("bill_discounts", $dis, array('id' => $data['id']));
	//$this->db->delete('group_discount_conditions', array('discount_id' => $data['id']));
	$this->db->delete('bill_discount_group', array('discount_id'=>$data['id']));
	
        /************* Assign Stores ************/
        $assigned_stores = $data['stores'];
        $assignedstores = array();
        foreach($assigned_stores as $k => $store_id){
             $a_stores['store_id'] = $store_id;
             $a_stores['discount_id'] = $id;
             array_push($assignedstores,$store_id);
             $this->db->insert('bill_discount_stores', $a_stores);//p($this->db->error());
             $this->db->insert_id();
        }
        if(!empty($assignedstores)){
            $this->db->where('discount_id',$id);
            $this->db->where_not_in('store_id',$assignedstores);
            $this->db->delete('bill_discount_stores');
        }
        /************* Assign Stores -END ************/
	/************* conditons ****************/
	//foreach($data['condition'] as $condition){
	//	    $condition['discount_id'] = $data['id'];
	//	    $condition['days'] = (isset($condition['condition_days']))?implode(',',$condition['condition_days']):'';
	//	    $this->db->insert('group_discount_conditions', $condition);
	//}
	/************* discounts ****************/
        //p($data['group']);
	$g_dis['discount_id'] = $data['id'];
	    foreach($data['group'] as $k => $row){
		$g_dis['discount_val'] = $row['discount'];
		$g_dis['discount_type'] = $row['discount_type'];
		if(isset($row['status'])) $g_dis['status'] = 1;
		    //echo '<pre>';print_r($row['category_id']);
                foreach($row['recipe_department_id'] as $k_0 => $row_0){
                    $g_dis['department_id'] = $row_0['id'];
		foreach($row_0['recipe_group_id'] as $k_1 => $row_1){	
		    $g_dis['category_id'] = $row_1['id'];
		    foreach($row_1['sub_category'] as $kk => $row_2){
			$g_dis['subcategory_id'] = $row_2['id'];
			$g_dis['type'] = (isset($row_2['type']))?$row_2['type']:'included';
			$insert = false;
			//if(isset($row_2['all'])) :
			//    $insert = true;
			//    $g_dis['product_id'] = 0;
			//else
                       // p($row_2['brands']);
                         foreach($row_2['brands'] as $bk => $row_3){
                            $g_dis['brand_id'] = $row_3['id'];
                           
                            $insert = false;
                        /************ receipe start *********/
                            if(isset($row_3['recipes'])) :
                            //echo '<pre>';print_r($row_2);
                            //print_R($row_2['recipes']);
                                $insert = true;
                                $id_days = array();
                                foreach($row_3['recipes'] as $r => $r_row){
                                    $days = '';
                                    $id_days[$r_row]['id'] = $r_row; 
                                    if(isset($row_3['days'][$r_row])){
                                      $id_days[$r_row]['days'] = serialize(array_flip($row_3['days'][$r_row]));
                                      
                                    }
                                    //echo '<pre>';print_R($id_days);
                                }
                                //echo '<pre>';print_R($id_days);
                                $g_dis['product_id'] = serialize($id_days);
                               // $g_dis['product_id'] = implode(',',$row_3['recipes']);
                            endif;
                            if($insert) : 
                                //echo '<pre>';print_R($g_dis);
                               $this->db->insert('bill_discount_group', $g_dis);//p($this->db->error()); //print_r($this->db->error());exit;
                            endif;
                        /********** recipe - end ****************/
                         }
		    }
		    
		}
                    }//exit;
		//if(isset($_POST['apply_all'])) break;
	}
	//exit;
	return true;
        //if ($this->db->update("bill_discounts", $dis, array('id' => $data['id']))) {
        //    return true;
        //}
        //return false;
    }
    
    function getBillDiscount($id){
	$q = $this->db->get_where("bill_discounts", array('id' => $id), 1);
	
	if ($q->num_rows() > 0) {
	    $q = $q->row();//print_R($q);
	//     $this->db
	//	->select('*')
	//	->from("group_discount_conditions")
	//	->where(array('discount_id' => $q->id));		
	    $q->conditions = array();//$this->db->get()->result();
	    
            /******* get assigned stores *********/
            $s = $this->db->get_where("bill_discount_stores", array('discount_id' => $id));
            if($s->num_rows()>0){
                $a_stores = array();
                foreach($s->result() as $k => $store_id){
                    array_push($a_stores,$store_id->store_id);
                }
                $q->a_stores = $a_stores;
            }
	    $this->db
		->select('id,discount_id,discount_val,discount_type,status')
		->from("bill_discount_group")
		->where(array('discount_id' => $q->id))
                ->order_by('id')
		->group_by('discount_id,discount_val');
		//->limit(1);
	    $group = $this->db->get()->result();
	    $q->group = $group;
	    //print_R($group);
	    foreach($group as $k => $row){
		 $this->db
		->select()
		->from("bill_discount_group")
		->where(array('discount_id' =>$row->discount_id,'discount_val'=>$row->discount_val));
		$recipe_groups = $this->db->get()->result();
		//p($recipe_groups);exit;
		$recipe_subgroups = array();
		foreach($recipe_groups as $kk => $val) {
		    $recipe_subgroups[$val->subcategory_id][$val->brand_id] = $val;
		}
		$q->group[$k]->recipe_groups = $recipe_subgroups;
                
                
	    }
	   
	    
	    
	    //$q->r_group = $r_group;
	    //p($q);exit;
            return $q;
        }
        return FALSE;
    }
    
    public function getAllCategories_items() {
        $this->db->select('id,name')
	->where('parent_id',0)->order_by('id');
        //echo $this->db->get_compiled_select();exit;
        $q = $this->db->get("categories");
        
        if ($q->num_rows() > 0) {
            if(!empty($q->result())){
                foreach (($q->result()) as $k => $row) {
                    $data[$k] = $row;
                    
                    $data[$k]->category = $this->getCategories($row->id);
                    if(!empty($data[$k]->category)){
                        foreach($data[$k]->category as $kk => $row1){
                            $data[$k]->category[$kk]->sub_category = $this->getSubCategories($row1->id);
                            if(!empty($data[$k]->category[$kk]->sub_category)){
                                foreach($data[$k]->category[$kk]->sub_category as $k2 => $row2){
                                     $data[$k]->category[$kk]->sub_category[$k2]->brands = $this->getProductBySubCategories($row2->id);
                                }
                            }
                        }
                    }
                    
                  
                    
                }
            }            
	    //p($data);exit;
            return $data;
        }
        return FALSE;
    }
    function getCategories($depID){
         $this->db->select('id,name');
         $this->db->where('parent_id', $depID)->order_by('name');
        $q = $this->db->get("categories");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    function getSubCategories($catID){
         $this->db->select('id,name');
         $this->db->where('parent_id', $catID)->order_by('name');
        $q = $this->db->get("categories");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    function getProductBySubCategories($sub_id){
        $this->db->select('p.id,p.name,p.brand,b.name as brand_name');
        $this->db->from('products p');
	$this->db->where('subcategory_id', $sub_id)->order_by('p.name');
        $this->db->join('brands b','b.id=p.brand','left');
        //echo $this->db->get_compiled_select();
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[$row->brand]->id = $row->brand;
                $data[$row->brand]->name = $row->brand_name;
                $data[$row->brand]->recipes[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
}