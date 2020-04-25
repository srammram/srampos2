<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Pos_api extends CI_Model
{     
 	 
    public function __construct() {
        parent::__construct();
        $mydate=getdate(date("U"));
        $this->today = "$mydate[weekday]";
    }

	public function getPOSSettingsALL(){
		$this->db->select('*');
		$q = $this->db->get('pos_settings');
		if ($q->num_rows() == 1) {
			$data = $q->row();
			return $data;
		}
		return FALSE;
	}
	public function getSettingsALL(){
		$this->db->select('*');
		$q = $this->db->get('settings');
		if ($q->num_rows() == 1) {
			$data = $q->row();
			return $data;
		}
		return FALSE;
	}	
	
	public function GetuserByID($user_number){
		
		$query = $this->db->select('*')
            ->where('user_number', $user_number)
            ->limit(1)
            ->get('users');
			
		if ($query->num_rows() === 1) {
            $user = $query->row();
			$ldata = array('user_id' => $user->id, 'ip_address' => $user->ip_address, 'login' => $user->id);
			$ldata['group_id'] = $user->group_id;
			$this->db->insert('user_logins', $ldata);
			$data = $user;
			return $data;
        }
		return FALSE;
	}
	
	public function GetRecipedetails($recipe_id){
		$default_image = site_url('assets/uploads/no_image.png');
		$default_url = site_url('assets/uploads/');
		$default_thumb_url = site_url('assets/uploads/thumbs/');
		
		$q = $this->db->query("SELECT r.id, r.code, r.recipe_details, r.type,  r.name, CASE WHEN r.khmer_name !='' THEN  r.khmer_name ELSE r.name END AS khmer_name, r.price, r.slug, r.category_id, c.name AS category_name, r.subcategory_id, s.name AS subcategory_name, r.kitchens_id, CASE WHEN r.image !='' THEN CONCAT('".$default_url."', r.image) ELSE '$default_image' END AS image, CASE WHEN r.image !='' THEN CONCAT('".$default_thumb_url."', r.image) ELSE '$default_image' END AS thumbnail FROM " . $this->db->dbprefix('recipe') . " AS r
		LEFT JOIN ".$this->db->dbprefix('recipe_categories')." AS c ON c.id = r.category_id
		LEFT JOIN ".$this->db->dbprefix('recipe_categories')." AS s ON s.id = r.subcategory_id
		WHERE r.id = ".$recipe_id." ");
		
		
		 if ($q->num_rows() == 1) {
			
			
			
			$data = $q->row();
			
			$this->db->select('recipe_addon.*, recipe.name, recipe.price');
			$this->db->join('recipe', 'recipe.id = recipe_addon.recipe_id');
			$this->db->where('recipe_addon.recipe_id', $recipe_id);
			
			$addon_query = $this->db->get('recipe_addon');
			
			if($addon_query->num_rows() > 0){
				foreach ($addon_query->result() as $addon_row) {
					$add[$recipe_id][] = $addon_row;
				}
				$data->addon_list = $add[$recipe_id];
			}else{
				$data->addon_list = array();	
			}
		
			
            return $data;
        }
	
		
		
		return FALSE;
	}
	
	public function getPrinterByID($id) {
        $q = $this->db->get_where('printers', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	
	public function notification_clear($notification_id){
		
		if(!empty($notification_id)){	
			
			$this->db->where_in('id', $notification_id);
			$this->db->update('notiy', array('is_read' => 1));			
			
			return true;
		}
		return false;
	}
	

	public function GetnotificationTags($group_id, $user_id, $warehouse_id,$tags){
		$current_date = date('Y-m-d');
		
		if($tags=="bbq-cover-validation"){
		    $this->db->select('type,type title,msg,type,split_id as bbq_code,table_id,id as notify_id');
		}else if($tags=='bbq-return'){
		    $this->db->select('type as title,msg,split_id as bbq_code,table_id');
		}else if($tags=='bill-request'){
		    $this->db->select('type as title,msg,split_id,table_id,id as notify_id');
		}else{
		    $this->db->select('n.*');
		}
		
		$this->db->from('notiy n')
		->where('n.to_user_id', $user_id)
		->where('n.warehouse_id', $warehouse_id)
		->where('n.is_read', 0)
		->where('DATE(n.created_on)', $current_date);
		if($tags!=''){
		   $this->db->where('n.tag',$tags);
		}
		//echo $this->db->get_compiled_select();
		$u = $this->db->get();
		if ($u->num_rows() > 0) {
			foreach($u->result() as $uow){
				$user[] = $uow;

			}
		}
		
		
		if(!empty($user)){
			$data = $user;
		}

		
		
		if(!empty($data)){
			
			return $data;
		}else{
			return false;
		}
				
		
	}
	

	public function Getnotification($group_id, $user_id, $warehouse_id,$status=null){
		
		$current_date = date('Y-m-d');
		
		$this->db->select('*')
		->where('to_user_id', $user_id)
		->where('warehouse_id', $warehouse_id)
		->where('DATE(created_on)', $current_date);
		
		if($status!=null){
		    $status = ($status=="pending")?0:1;
		    $this->db->where('status',$status);
		}
		
		$u = $this->db->get('notiy');
		if ($u->num_rows() > 0) {
			foreach($u->result() as $uow){
				$user[] = $uow;
			}
		}
		
		/*$r =$this->db->select('*')->where('role_id', $group_id)->where('to_user_id', 0)->where('warehouse_id', $warehouse_id)->where('is_read', 0)->where('DATE(created_on)', $current_date)->get('notiy');
		if ($r->num_rows() > 0) {
			foreach($r->result() as $row){
				$group[] = $row;
			}
		}*/
		
		/*if(!empty($user) && empty($group)){
			$data['notification_list'] = $user;
		}elseif(empty($user) && !empty($group)){
			$data['notification_list'] = $group;
		}elseif(!empty($user) && !empty($group)){
			$data['notification_list'] = array_merge($user, $group);
		}*/
		
		if(!empty($user)){
			$data['notification_list'] = $user;
		}

		$data['notification_count'] = count($data['notification_list']);
		
		if(!empty($data['notification_list'])){
			
			return $data;
		}else{
			return false;
		}
	}
	
	public function GetAllmaincategory_withdays($order_type){

		$default_image = site_url('assets/uploads/no_image.png');
		$default_url = site_url('assets/uploads/');
		$default_thumb_url = site_url('assets/uploads/thumbs/');
		$today =$this->today; 		

		$order_by ='ORDER BY RC.name';	
		if($this->pos_settings->categories_list_by ==0) {
			$order_by ='ORDER BY RC.id';
		}	

		$query = $this->db->query("SELECT RC.id, RC.name, RC.khmer_name, CASE WHEN RC.image !='' THEN CONCAT('".$default_url."', RC.image) ELSE '$default_image' END AS image, CASE WHEN RC.image !='' THEN CONCAT('".$default_thumb_url."', RC.image) ELSE '$default_image' END AS thumbnail  FROM " . $this->db->dbprefix('recipe_categories') . "  AS RC
			JOIN " . $this->db->dbprefix('sale_items_mapping_details') . " AS IMDT ON IMDT.recipe_group_id = RC.id 
			JOIN " . $this->db->dbprefix('sale_items_mapping_head') . " AS IMHD ON IMHD.id = IMDT.sales_map_hd_id 
			WHERE RC.parent_id is NULL or RC.parent_id = 0 AND IMHD.days= '".$today."' AND  IMHD.sale_type= ".$order_type." AND RC.status=1  GROUP BY RC.id ".$order_by." ");			
        	if ($query->num_rows() > 0) {
			$all = array('id' => "0", 'name' => 'ALL', 'khmer_name' => 'ទាំងអស់','image' => $default_image,'thumbnail' => $default_image);
			$data[] = $all;
           foreach ($query->result() as $row) {
			   if(!empty($row->khmer_name)){
				   $row->khmer_name = $row->khmer_name;
			   }else{
				   $row->khmer_name = $row->name;
			   }
                $data[] = $row;
            }

            return $data;
        }
        return FALSE;
	}

	public function GetAllmaincategory($order_type){

		$default_image = site_url('assets/uploads/no_image.png');
		$default_url = site_url('assets/uploads/');
		$default_thumb_url = site_url('assets/uploads/thumbs/');

		$order_by ='ORDER BY RC.name';	
		if($this->pos_settings->categories_list_by ==0) {
			$order_by ='ORDER BY RC.id';
		}		
		$query = $this->db->query("SELECT RC.id, RC.name, RC.khmer_name, CASE WHEN RC.image !='' THEN CONCAT('".$default_url."', RC.image) ELSE '$default_image' END AS image, CASE WHEN RC.image !='' THEN CONCAT('".$default_thumb_url."', RC.image) ELSE '$default_image' END AS thumbnail  FROM " . $this->db->dbprefix('recipe_categories') . "  AS RC			
			WHERE RC.parent_id is NULL or RC.parent_id = 0  AND RC.status=1 GROUP BY RC.id ".$order_by." ");
        	if ($query->num_rows() > 0) {
			$all = array('id' => "0", 'name' => 'ALL', 'khmer_name' => 'ទាំងអស់','image' => $default_image,'thumbnail' => $default_image);
			$data[] = $all;
           foreach ($query->result() as $row) {
			   if(!empty($row->khmer_name)){
				   $row->khmer_name = $row->khmer_name;
			   }else{
				   $row->khmer_name = $row->name;
			   }
                $data[] = $row;
            }

            return $data;
        }
        return FALSE;
	}	
	public function GetAllsubcategory_withdays($category_id,$order_type){
		$default_image = site_url('assets/uploads/no_image.png');
		$default_url = site_url('assets/uploads/');
		$default_thumb_url = site_url('assets/uploads/thumbs/');
		$today =$this->today; 

		if($category_id == 0){
			$where = " WHERE parent_id != ".$category_id."";
			
		}else{
			$where = " WHERE parent_id = ".$category_id."";
		}
		$query = $this->db->query("SELECT RC.id, RC.name, RC.khmer_name, CASE WHEN RC.image !='' THEN CONCAT('".$default_url."', RC.image) ELSE '$default_image' END AS image, CASE WHEN RC.image !='' THEN CONCAT('".$default_thumb_url."', RC.image) ELSE '$default_image' END AS thumbnail  FROM 
			" . $this->db->dbprefix('recipe_categories') . " AS RC
			JOIN " . $this->db->dbprefix('sale_items_mapping_details') . " AS IMDT ON IMDT.recipe_subgroup_id = RC.id 
			JOIN " . $this->db->dbprefix('sale_items_mapping_head') . " AS IMHD ON IMHD.id = IMDT.sales_map_hd_id 
			  ".$where."  AND IMHD.days= '".$today."' AND  IMHD.sale_type= ".$order_type."  AND RC.status=1  order by RC.name  asc");
			
        if ($query->num_rows() > 0) {
           foreach ($query->result() as $row) {
			   if(!empty($row->khmer_name)){
				   $row->khmer_name = $row->khmer_name;
			   }else{
				   $row->khmer_name = $row->name;
			   }
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
	}
public function GetAllsubcategory($category_id,$order_type){
		$default_image = site_url('assets/uploads/no_image.png');
		$default_url = site_url('assets/uploads/');
		$default_thumb_url = site_url('assets/uploads/thumbs/');
		
		if($category_id == 0){
			$where = " WHERE parent_id != ".$category_id." AND status=1";		
		}else{
			$where = " WHERE parent_id = ".$category_id." AND status=1";
		}
		$query = $this->db->query("SELECT RC.id, RC.name, RC.khmer_name, CASE WHEN RC.image !='' THEN CONCAT('".$default_url."', RC.image) ELSE '$default_image' END AS image, CASE WHEN RC.image !='' THEN CONCAT('".$default_thumb_url."', RC.image) ELSE '$default_image' END AS thumbnail  FROM 
			" . $this->db->dbprefix('recipe_categories') . " AS RC ".$where." order by RC.name  asc ");
		// print_r($this->db->last_query());die;
        if ($query->num_rows() > 0) {
           foreach ($query->result() as $row) {
			   if(!empty($row->khmer_name)){
				   $row->khmer_name = $row->khmer_name;
			   }else{
				   $row->khmer_name = $row->name;
			   }
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
	}
	public function GetAllrecipe_withdays($subcategory_id, $warehouse_id,$order_type){
		$default_image = site_url('assets/uploads/no_image.png');
		$default_url = site_url('assets/uploads/');
		$default_thumb_url = site_url('assets/uploads/thumbs/');
		$today =$this->today; 
		$where_in = ("'standard'".','."'production'".','."'quick_service'".','."'combo'");
		$query = $this->db->query("SELECT r.id, r.code,r.active, r.type,  r.name, r.khmer_name, r.price, r.currency_type, r.slug, r.category_id, r.subcategory_id, r.kitchens_id, CASE WHEN r.image !='' THEN CONCAT('".$default_url."', r.image) ELSE '$default_image' END AS image, CASE WHEN r.image !='' THEN CONCAT('".$default_thumb_url."', r.image) ELSE '$default_image' END AS thumbnail, w.warehouse_id  FROM " . $this->db->dbprefix('recipe') . " AS r 
			LEFT JOIN " . $this->db->dbprefix('warehouses_recipe') . " AS w ON w.recipe_id = r.id 
			JOIN " . $this->db->dbprefix('recipe_categories') . " AS RC ON RC.id = r.category_id 
			JOIN " . $this->db->dbprefix('sale_items_mapping_details') . " AS IMDT ON IMDT.recipe_subgroup_id = r.subcategory_id
			JOIN " . $this->db->dbprefix('sale_items_mapping_head') . " AS IMHD ON IMHD.id = IMDT.sales_map_hd_id 
			 WHERE r.subcategory_id = ".$subcategory_id." AND r.active in (1,2) AND w.warehouse_id = ".$warehouse_id."  AND IMHD.days= '".$today."' AND  IMHD.sale_type= ".$order_type." AND FIND_IN_SET(r.id,IMDT.recipe_id) !=0 AND r.type in (".$where_in.") order by r.name asc");
			 
        if ($query->num_rows() > 0) {
           foreach ($query->result() as $row) {	    
			//// variants ///
			$this->db->select('recipe_variants.id as variant_id,recipe_variants.name,recipe_variants_values.price');
			$this->db->join('recipe_variants', 'recipe_variants.id=recipe_variants_values.attr_id');
			$this->db->where('recipe_variants_values.recipe_id',$row->id);
			$variant_query = $this->db->get('recipe_variants_values');
			$row->variants = 0;
			$row->addon = 0;
			$row->customizable = 0; 
			($row->active ==2)?$row->non_transaction=1:$row->non_transaction=0;
		
			if($variant_query->num_rows()>0){
			    $row->variants = 1;
			}
			   
			  if(!empty($row->khmer_name)){
				   $row->khmer_name = $row->khmer_name;
			   }else{
				   $row->khmer_name = $row->name;
			   }
			   
			  $row->comment_active = 0;
			  
			  if($row->variants == 1){

			  	$this->db->select("recipe_addon_details.*, recipe.name AS addon, recipe.price")
		      	->join('recipe_addon_details', 'recipe_addon_details.addon_head_id = recipe_addon.id')
		      	->join('recipe', 'recipe.id = recipe_addon_details.addon_item_id','left')
		      	->where('recipe_addon.recipe_id', $row->id)
		      	->where('recipe_addon.variant_id', $row->variants);      	
		        $variant_addon_query = $this->db->get('recipe_addon');
			      if($variant_addon_query->num_rows()>0){
				        $row->addon = 1;
				   }

				$this->db->select("r.id,r.name AS customize_name, IFNULL(r.price,0) price,r.khmer_name AS customize_native_name,recipe_products.quantity,units.name as unit_name")      	
		      	->join('recipe r', 'r.id = recipe_products.product_id','left')
		      	->join('recipe_variants', 'recipe_variants.id = recipe_products.product_id','left')
		      	->join('units', 'units.id = recipe_products.unit_id')
		      	->where('recipe_products.recipe_id', $row->id)
		      	->where('recipe_products.variant_id', $row->variants)    	
		      	->where('recipe_products.item_customizable', 1);   
		        $item_customizable = $this->db->get('recipe_products');                         
		        if ($item_customizable->num_rows() > 0) {
		            $row->customizable = 1;
		        } 

			  }else{
			  		$this->db->select("recipe_addon_details.*, recipe.name AS addon, recipe.price")
			      	->join('recipe_addon_details', 'recipe_addon_details.addon_head_id = recipe_addon.id')
			      	->join('recipe', 'recipe.id = recipe_addon_details.addon_item_id','left')
			      	->where('recipe_addon.recipe_id', $row->id);      	
			        $recipe_addon_query = $this->db->get('recipe_addon');
			        if($recipe_addon_query->num_rows()>0){
				          $row->addon = 1;
				   }

				$this->db->select("r.id,r.name AS customize_name, IFNULL(r.price,0) price,r.khmer_name AS customize_native_name,recipe_products.quantity,units.name as unit_name")      	
		      	->join('recipe r', 'r.id = recipe_products.product_id','left')
		      	->join('recipe_variants', 'recipe_variants.id = recipe_products.product_id','left')
		      	->join('units', 'units.id = recipe_products.unit_id')
		      	->where('recipe_products.recipe_id', $row->id)		      	  	
		      	->where('recipe_products.item_customizable', 1);   
		        $item_customizable = $this->db->get('recipe_products');                         
		        if ($item_customizable->num_rows() > 0) {
		            $row->customizable = 1;
		        } 

			  }
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
	}
	public function GetAllrecipe($subcategory_id, $warehouse_id,$order_type){
		$default_image = site_url('assets/uploads/no_image.png');
		$default_url = site_url('assets/uploads/');
		$default_thumb_url = site_url('assets/uploads/thumbs/');
		$today =$this->today; 
		$where_in = ("'standard'".','."'production'".','."'quick_service'".','."'combo'");
		$query = $this->db->query("SELECT r.id, r.code, r.type,  r.name, r.khmer_name, r.price, r.currency_type, r.slug, r.category_id, r.subcategory_id, r.kitchens_id, CASE WHEN r.image !='' THEN CONCAT('".$default_url."', r.image) ELSE '$default_image' END AS image, CASE WHEN r.image !='' THEN CONCAT('".$default_thumb_url."', r.image) ELSE '$default_image' END AS thumbnail, w.warehouse_id,r.active  FROM " . $this->db->dbprefix('recipe') . " AS r 
			LEFT JOIN " . $this->db->dbprefix('warehouses_recipe') . " AS w ON w.recipe_id = r.id 
			JOIN " . $this->db->dbprefix('recipe_categories') . " AS RC ON RC.id = r.category_id 
			
			 WHERE r.recipe_standard != 2 AND r.subcategory_id = ".$subcategory_id." AND  w.warehouse_id = ".//$warehouse_id." AND r.type in (".$where_in.") order by RC.id asc");
			 $warehouse_id." AND r.type in (".$where_in.") and r.active in (1,2)order by r.name asc");
		//print_r($this->db->last_query());die;
		
        if ($query->num_rows() > 0) {
           foreach ($query->result() as $row) {
	    
			//// variants ///
			$this->db->select('recipe_variants.id as variant_id,recipe_variants.name,recipe_variants_values.price');
			$this->db->join('recipe_variants', 'recipe_variants.id=recipe_variants_values.attr_id');
			$this->db->where('recipe_variants_values.recipe_id',$row->id);
			$variant_query = $this->db->get('recipe_variants_values');
			$row->variants = 0;
			$row->addon = 0;
			if($variant_query->num_rows()>0){
			    $row->variants = 1;
			}
			   
			    if(!empty($row->khmer_name)){
				   $row->khmer_name = $row->khmer_name;
			   }else{
				   $row->khmer_name = $row->name;
			   }
			   
			  $row->comment_active = 0;
			 ($row->active ==2)?$row->non_transaction=1:$row->non_transaction=0;
				  
			
				  
			  if($row->variants == 1){
			  	$this->db->select("recipe_addon_details.*, recipe.name AS addon, recipe.price")
		      	->join('recipe_addon_details', 'recipe_addon_details.addon_head_id = recipe_addon.id')
		      	->join('recipe', 'recipe.id = recipe_addon_details.addon_item_id','left')
		      	->where('recipe_addon.recipe_id', $row->id)
		      	->where('recipe_addon.variant_id', $row->variants);      	
		        $variant_addon_query = $this->db->get('recipe_addon');
			      if($variant_addon_query->num_rows()>0){
				        $row->addon = 1;
				   }
			  }else{
			  		$this->db->select("recipe_addon_details.*, recipe.name AS addon, recipe.price")
			      	->join('recipe_addon_details', 'recipe_addon_details.addon_head_id = recipe_addon.id')
			      	->join('recipe', 'recipe.id = recipe_addon_details.addon_item_id','left')
			      	->where('recipe_addon.recipe_id', $row->id);      	
			        $recipe_addon_query = $this->db->get('recipe_addon');
			        if($recipe_addon_query->num_rows()>0){
				          $row->addon = 1;
				   }
			  }
			   
			   /*$this->db->select('recipe_addon.*, recipe.name AS addon, recipe.price');
			   $this->db->join('recipe', 'recipe.id = recipe_addon.addon_id');
			   $this->db->where('recipe_addon.recipe_id', $row->id);
			   $this->db->group_by('recipe_addon.recipe_id');
			   
			    $addon_query = $this->db->get('recipe_addon');
				if($addon_query->num_rows() > 0){
					foreach ($addon_query->result() as $addon_row) {
						$add[$row->id][] = $addon_row;
					}
					$row->addon_list = $add[$row->id];
				}else{
					$row->addon_list = array();	
				}*/
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
	}	
/*21-04-2019 addon for recipe or variant*/

	public function getrecipeAddons($recipe_id)
    {
      	$this->db->select("RA.id,RA.addon_head_id,RA.addon_item_id,CAST(srampos_recipe.price AS DECIMAL(10,2)) as price,recipe.name AS addon")
      	->join('recipe_addon_details RA', 'RA.addon_head_id = recipe_addon.id')
      	->join('recipe', 'recipe.id = RA.addon_item_id','left')
      	->where('recipe_addon.recipe_id', $recipe_id);      	
        $q = $this->db->get('recipe_addon');        
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	public function getrecipeVariantAddons($variant, $recipe_id)
    {
      	$this->db->select("RA.id,RA.addon_head_id,RA.addon_item_id,CAST(srampos_recipe.price AS DECIMAL(6,2)) as price,recipe.name AS addon")
      	->join('recipe_addon_details RA', 'RA.addon_head_id = recipe_addon.id')
      	->join('recipe', 'recipe.id = RA.addon_item_id','left')
      	->where('recipe_addon.recipe_id', $recipe_id)
      	->where('recipe_addon.variant_id', $variant);      	
        $q = $this->db->get('recipe_addon');           
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }	

    function GetVaraintDetails($recipe_id){
	$this->db->select('r.id,r.recipe_id,CAST(r.price AS DECIMAL(10,2)) as price,r.preferred,v.name,v.native_name,v.variant_code,v.id variant_id,v.image');
	$this->db->from('recipe_variants_values r');
	$this->db->join('recipe_variants v','v.id=r.attr_id');
	$this->db->where('r.recipe_id',$recipe_id);
	$q = $this->db->get();			
	if($q->num_rows()>0){
		foreach ($q->result() as $row) {
		$this->db->select("recipe_addon_details.*, recipe.name AS addon, recipe.price")
		      	->join('recipe_addon_details', 'recipe_addon_details.addon_head_id = recipe_addon.id')
		      	->join('recipe', 'recipe.id = recipe_addon_details.addon_item_id','left')
		      	->where('recipe_addon.recipe_id', $row->recipe_id)	      	
		      	->where('recipe_addon.variant_id', $row->variant_id);		      	
		        $variant_addon_query = $this->db->get('recipe_addon');		        
		        $row->addon = 0;
			      if($variant_addon_query->num_rows()>0){
				        $row->addon = 1;
				   }
				   $data[] = $row;
		}
	    return $q->result();
	}
	return false;
	
    }

/*09-09-2019 Customzec Item for recipe or variant*/
	public function getrecipeVariantCustomizable($variant, $recipe_id)
    {
      	$this->db->select("r.id,r.name AS customize_name, IFNULL(r.price,0) price,r.khmer_name AS customize_native_name,recipe_products.quantity,units.name as unit_name")      	
      	->join('recipe r', 'r.id = recipe_products.product_id','left')
      	->join('recipe_variants', 'recipe_variants.id = recipe_products.product_id','left')
      	->join('units', 'units.id = recipe_products.unit_id')
      	->where('recipe_products.recipe_id', $recipe_id)
      	->where('recipe_products.variant_id', $variant)    	
      	->where('recipe_products.item_customizable', 1);   
        $q = $this->db->get('recipe_products');                         
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	public function getrecipeCustomizable($recipe_id)
    {
      	$this->db->select("r.id,r.name AS customize_name, IFNULL(r.price,0) price,r.khmer_name AS customize_native_name,recipe_products.quantity,units.name as unit_name")      	
      	->join('recipe r', 'r.id = recipe_products.product_id','left')   
      	->join('units', 'units.id = recipe_products.unit_id')   	
      	->where('recipe_products.recipe_id', $recipe_id)      	   	
      	->where('recipe_products.item_customizable', 1);   
        $q = $this->db->get('recipe_products');                 
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
/*09-09-2019 Customzec Item for recipe or variant*/
/*21-04-2019 end  addon  for recipe or variant*/	
	public function Alltablecategory($warehouse_id){
		
		$this->db->select('restaurant_areas.*');
		$this->db->join('restaurant_tables', 'restaurant_tables.warehouse_id = '.$warehouse_id.' AND restaurant_tables.area_id = restaurant_areas.id ');
		$this->db->group_by('restaurant_areas.id');
		$query = $this->db->get('restaurant_areas');
		if ($query->num_rows() > 0) {
			 foreach ($query->result() as $row) {
				 $data[] = $row;
			 }
			  return $data;
		}
		 return FALSE;
	}
	
	public function GetAlltablecategory($warehouse_id, $bbq_id=NULL){
		/*if($bbq_id == 1){
			$bbq = 'bbq';
		}else{
			$bbq = 'suki';
		}*/
		$this->db->select('restaurant_areas.id, restaurant_areas.name');
		$this->db->join('restaurant_tables', 'restaurant_tables.warehouse_id = '.$warehouse_id.' AND restaurant_tables.area_id = restaurant_areas.id ');
		// $this->db->where('restaurant_areas.type', $bbq);
		$this->db->where("restaurant_tables.sale_type", 'alacarte');
		$this->db->having('COUNT(srampos_restaurant_tables.sale_type) >= 1'); 
		$this->db->group_by('restaurant_areas.id');
		$query = $this->db->get('restaurant_areas');
		if ($query->num_rows() > 0) {
			 foreach ($query->result() as $row) {
				 $data[] = $row;
			 }
			  return $data;
		}
		 return FALSE;
	}
	
	public function GetAlltables($area_id, $warehouse_id, $user_id){
		$this->db->select('id, name, area_id, warehouse_id');
		$query = $this->db->get_where('restaurant_tables', array('area_id' => $area_id, 'warehouse_id' => $warehouse_id));
		if ($query->num_rows() > 0) {
			 foreach ($query->result() as $row) {
				 $table_status = $this->site->orderTablecheckapi($row->id, $user_id);
               	 $row->status = $table_status;
				 $data[] = $row;
			 }
			  return $data;
		}
		return FALSE;
	}
	
	public function DineGetAlltables($area_id, $warehouse_id, $user_id){
		$this->db->select('id, name, area_id, warehouse_id,sale_type AS type');
		$this->db->order_by('id', 'ASC');
		$query = $this->db->get_where('restaurant_tables', array('area_id' => $area_id,'warehouse_id' => $warehouse_id));

		// $query = $this->db->get_where('restaurant_tables', array('area_id' => $area_id,'warehouse_id' => $warehouse_id,'sale_type' => 'alacarte'));
		if ($query->num_rows() > 0) {
			 foreach ($query->result() as $row) {
				 $table_status = $this->site->orderTablecheckapi($row->id, $user_id);
               	 $row->status = $table_status;
				 $data[] = $row;
			 }
			  return $data;
		}
		return FALSE;
	}
	
	public function BBQGetAlltables($area_id, $warehouse_id, $user_id){
		$this->db->select('id, name, area_id, warehouse_id');
		$query = $this->db->get_where('restaurant_tables', array('area_id' => $area_id, 'warehouse_id' => $warehouse_id));
		if ($query->num_rows() > 0) {
			 foreach ($query->result() as $row) {
				 $table_status = $this->site->orderBBQTablecheckapi($row->id, $user_id);
               	 $row->status = $table_status;
				 $data[] = $row;
			 }
			  return $data;
		}
		return FALSE;
	}
	
	public function GetAllcurrencies() {
        $q = $this->db->get('currencies');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
	public function GetAlltaxs() {
        $q = $this->db->get('tax_rates');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
	public function GetAllwarehouses() {
        $q = $this->db->get('warehouses');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
	 public function GetAllgroups() {
		
		$q = $this->db->get('groups');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
	public function GetAllcustomer_groups() {
        $q = $this->db->get('customer_groups');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
	public function GetAllsuppliers() {
		$this->db->where('group_name', 'supplier');
        $q = $this->db->get("companies");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
	public function GetAllcustomers() {
		$this->db->where('group_name', 'customer');
        $q = $this->db->get("companies");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
	public function GetAlldeliveryusers($warehouse_id){
		$this->db->select("users.id, users.first_name, users.last_name, users.email, groups.description");
		$this->db->join('groups', 'groups.id = users.group_id');
		$this->db->where('users.warehouse_id', $warehouse_id);
		$this->db->where('users.active', 1);
		$this->db->order_by('users.group_id', 'DESC');
		 $q = $this->db->get('users');
		if ($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
                $data[] = $row;
            }
			
			
            return $data;
		}
		return FALSE;
	}
	
	public function Insertcustomer($data = array())
    {
        if ($this->db->insert('companies', $data)) {
            $cid = $this->db->insert_id();
			
			$this->db->where('group_name', 'customer');
			$this->db->where('id', $cid);
			$q = $this->db->get("companies");
			if ($q->num_rows() > 0) {
				foreach (($q->result()) as $row) {
					$row->customer_id = $row->id;
					$data = $row;
				}
				return $data;
			}
			
            return false;
        }
        return false;
    }
    public function GetAllcostomerDiscounts() {
    	$this->db->where('status', 1);
        $q = $this->db->get('diccounts_for_customer');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    public function getAllBillingforReprint($date,$type,$bill_no,$warehouse_id){
    $current_date = date('Y-m-d');
		$this->db->select("sales.id as id,sales.reference_no, ,restaurant_tables.name as tablename,restaurant_areas.name as areaname,bils.bill_number,bils.id as bill_id");
		$this->db->join("restaurant_tables", "restaurant_tables.id = sales.sales_table_id",'left');
		$this->db->join("restaurant_areas", "restaurant_areas.id = restaurant_tables.area_id",'left');		
		$this->db->join("bils", "bils.sales_id = sales.id",'left');		
		$this->db->where('sales.warehouse_id', $warehouse_id);
		$this->db->where('sales.sale_status', 'Closed');
		$this->db->where('sales.cancel_status', 0);
		$this->db->where('sales.consolidated', 0);
		if($date==''){$date = date('Y-m-d');}
		$this->db->where('bils.date', $date);
		if(isset($bill_no) && $bill_no!=''){
		    $this->db->where("(bils.bill_number like '%" . $bill_no . "%')");
		}
		if(isset($type) && $type!='all'){
		    $this->db->where('bils.table_whitelisted', $type);
		}
		$this->db->where_in('sales_type_id',array(1,2,3));
		$this->db->order_by("bils.bill_number", "desc");

		$s = $this->db->get('sales');

		if ($s->num_rows() > 0) {
            foreach ($s->result() as $row) {
				
				$this->db->select("bils.*");
				$this->db->where('bils.sales_id', $row->id);
				// $this->db->order_by("bils.bill_number", "desc");
				$b = $this->db->get('bils');
				

				if ($b->num_rows() > 0) {
					foreach ($b->result() as $bil_row) {
						
						$bils[$row->id][] = $bil_row;
					}
					//$row->bils = $bils[$row->id];
					$data[] = $row;	
				}
				
			}
			
			return $data;
		}

		return FALSE;
}
 public function getBillDetails($bill_id,$warehouse_id){
    $this->db
	->select('id,bill_number,total_items,total,total_discount,total_tax,grand_total,SUM(total-total_discount) as subtotal,tax_type,customer_discount_id,order_discount')
	->from('bils')
	->where('id',$bill_id);
	//echo $this->db->get_compiled_select();
	$q = $this->db->get();
	$data = array();
	if($q->num_rows()>0){
	    $data['bill'] = $q->row();
	    $this->db->select('recipe_name,unit_price,quantity,discount,subtotal');
	    //echo $this->db->get_compiled_select();exit;
	    $b = $this->db->get_where('bil_items',array('bil_id'=>$bill_id));
	    if($b->num_rows()>0){
		$data['bill']->bill_items = $b->result();
	    }
	    return $data;
	}
	return false;
 }
}
