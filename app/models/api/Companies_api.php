<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Companies_api extends CI_Model
{

    public function __construct() {
        parent::__construct();
    }

    public function countCompanies($filters = []) {
        if ($filters['group']) {
            $this->db->where('group_name', $filters['group']);
        }
        $this->db->from('companies');
        return $this->db->count_all_results();
    }

    public function getCompanies($filters = []) {

        // $uploads_url = base_url('assets/uploads/');
        // $this->db->select("id, code, name, type, slug, price, CONCAT('{$uploads_url}', image) as image_url, tax_method, tax_rate, unit");

        if ($filters['group']) {
            $this->db->where('group_name', $filters['group']);
        }

        if ($filters['name']) {
            $this->db->where('name', $filters['name']);
        } else {
            $this->db->order_by($filters['order_by'][0], $filters['order_by'][1] ? $filters['order_by'][1] : 'asc');
            $this->db->limit($filters['limit'], ($filters['start']-1));
        }

        return $this->db->get("companies")->result();
    }

    public function getCompany($filters) {
        if (!empty($companies = $this->getCompanies($filters))) {
            return array_values($companies)[0];
        }
        return FALSE;
    }

    public function getCompanyUsers($company_id) {
        return $this->db->get_where('users', ['company_id' => $company_id])->result();
    }
    public function addCompany($data)
    {
        if ($this->db->insert('companies', $data)) {
            $cid = $this->db->insert_id();
            return $cid;
        }
        return false;
    }
     public function GetAllcustomers() {
	$this->db->select('id,name,group_id,email,phone,address,city,state,postal_code,customer_group_id,IFNULL(landmark, "") AS landmark, ');
	$this->db->where('group_name', 'customer');
        $q = $this->db->get("companies");        
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
		$row->address		=	($row->address==null)?"":$row->address;
		$row->city		=	($row->city==null)?"":$row->city;
		$row->state		=	($row->state==null)?"":$row->state;
		$row->email		=	($row->email==null)?"":$row->email;
		$row->postal_code	=	($row->postal_code==null)?"":$row->postal_code;
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
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
    function isphoneExist($phone){
	$this->db->select('*');
	$this->db->where('phone',$phone);
        $q = $this->db->get("companies");
        if ($q->num_rows() > 0) {
	    return true;
	}
	return false;
    }
}
