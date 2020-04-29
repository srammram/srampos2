<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Store_indent_receive_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    


   function getindentReceive_requestByID($id){
     $q = $this->db->get_where('pro_store_indent_receive', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
   }
   function getAllIndentRecevie_requestItems($id){
     $this->db->select('pro_store_indent_receive_items.*')
           
            ->group_by('pro_store_indent_receive_items.id')
            ->order_by('id', 'asc');
        $q = $this->db->get_where('pro_store_indent_receive_items', array('store_request_id' => $id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
   }
}
