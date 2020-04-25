<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Shiftmaster_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }


  public function addShiftmaster($data)
    {        
        $this->db->insert('shiftmaster', $data);        
        $shiftmaster_id = $this->db->insert_id();
		
        if ($shiftmaster_id) {            
           return true;
        }        
        return false;
    }

    public function updateShiftmaster($id, $data)
    {          
        $this->db->update('shiftmaster', $data, array('id' => $id));        
        if (!empty($id)) {                         
            return true;
        }            
        return false;
    }

	 public function checkShiftmaster($from_time, $to_time, $id = NULL)
    {
		if($id){
			$this->db->where('id !=', $id);
		}
        $q = $this->db->get('shiftmaster');		
        if($q->num_rows()>0){
            foreach($q->result() as $k => $row){
                
				
                $r_from_time = strtotime($row->from_time);
                $r_to_time = strtotime($row->to_time);
                if($r_from_time<$r_to_time){
                	$from_time = strtotime($from_time);
               		$to_time = strtotime($to_time);
                    if(($r_from_time<=$from_time && $from_time<=$r_to_time) || ($r_from_time<=$to_time && $to_time<=$r_to_time)){
                        return true;
                    }
                }else{
                
                    $n_from_time = strtotime(date('Y-m-d ',strtotime($date .' +1 day')).$from_time);
                    $n_to_time = strtotime(date('Y-m-d ',strtotime($date .' +1 day')).$to_time);
                    $r_from_time = strtotime(date('Y-m-d ').$row->from_time);
                    $r_to_time = strtotime(date('Y-m-d ',strtotime($date .' +1 day')).$row->to_time);
                    $s_from_time = strtotime(date('Y-m-d ').$from_time);
                    $s_to_time = strtotime(date('Y-m-d ',strtotime($date .' +1 day')).$to_time);
                    $r_sameday_to_time = strtotime(date('Y-m-d ').$to_time);
                    
                    if(($r_from_time<=$s_from_time && $s_from_time<=$r_to_time) || ($r_from_time<=$s_to_time && $s_to_time<=$r_to_time)){
                        return true;
                    }else if(($r_from_time<=$n_from_time && $n_from_time<=$r_to_time) || ($r_from_time<=$n_to_time && $n_to_time<=$r_to_time)){
                        return true;
                    }else if(($r_from_time<=$s_from_time && $s_from_time<=$r_to_time) || ($r_from_time<=$r_sameday_to_time && $r_sameday_to_time<=$r_to_time)){
                        return true;
                    }
                }
				
            }
            
            //return true;
        }
        return FALSE;
    }

    public function getShiftmasterByID($id)
    {
        $q = $this->db->get_where('shiftmaster', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }



 	public function deleteShiftmaster($id)
    {
        if ($this->db->delete('shiftmaster', array('id' => $id))) {
            return true;
        }
        return FALSE;
    }
	
	public function deactivate($id = NULL)
    {
        if (($id)) {        
            $data = array(
                'status' => 0
            );
        $return = $this->db->update('shiftmaster', $data, array('id' => $id));
        return $return;
        }
        return FALSE;
    }   

    public function activate($id = NULL)
    {
        if (($id)) {   
            $data = array(
                'status' => 1
            );

        $return = $this->db->update('shiftmaster', $data, array('id' => $id));
        return $return;
        }
        return FALSE;
    }       


}
