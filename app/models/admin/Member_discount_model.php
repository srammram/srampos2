<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Member_discount_model extends CI_Model{
    public function __construct(){
        parent::__construct();
    }
  function add_discounts($data){
	  $this->db->insert("member_discounts",$data);
	  return  $this->db->insert_id();
  }
  function update($data,$id){
	  $this->db->where("id",$id);
	  $this->db->update("member_discounts",$data);
	  return true;
  }
  function delete($id){
	    $this->db->where("id",$id);
	    $this->db->delete("member_discounts");
  }
  function get_discount_details($id){
	  $this->db->select("*");
	  $this->db->where("id",$id);
	  $q=$this->db->get("member_discounts");
	  if($q->num_rows()>0){
		return   $q->row();
	  }
	  return false;
  }
  function add_discounts_card($data){
	  $this->db->insert("memeber_discount_card_details",$data);
	  $discount_card_id=$this->db->insert_id();
	  for($i=0;$i<$data['no_of_vouchers'];$i++){
		   $card_no= ($i==0)?($data['prefix'].($data['serial_no'])):++$card_no;
		  $this->db->insert("memberdiscountcards",array("card_details_id"=>$discount_card_id,"card_no"=>$card_no,"created_on"=>date("Y-m-d")));
		  $card_id=$this->db->insert_id();
		  $this->db->insert("memeberDiscountcard_status",array("card_id"=>$card_id,"status"=>1,"discount_card_id"=>$discount_card_id));
	  }
	  return true;
  }
  function update_card($data,$id){
	  $this->db->where("id",$id);
	  $this->db->update("memeber_discount_card_details",$data);
	  $this->db->where("card_details_id",$id);
	  $this->db->delete("memberdiscountcards");
	  $this->db->where("discount_card_id",$id);
	  $this->db->delete("memeberDiscountcard_status");
	  $card_no=$data['prefix'].$data['serial_no'];
	  for($i=0;$i<$data['no_of_vouchers'];$i++){
		  $card_no=$data['prefix'].($data['serial_no']+$i);
		  $this->db->insert("memberdiscountcards",array("card_details_id"=>$id,"card_no"=>$card_no,"created_on"=>date("Y-m-d")));
		  $card_id=$this->db->insert_id();
		  $this->db->insert("memeberDiscountcard_status",array("card_id"=>$card_id,"status"=>1,"discount_card_id"=>$id));
	  }
	  return true;
  }
  function delete_card($id){
	    $this->db->where("id",$id);
	    $this->db->delete("memeber_discount_card_details");
  }
  function get_discount_card_details($id){
	  $this->db->select("memeber_discount_card_details.*,member_discounts.name");
	  $this->db ->join("member_discounts","member_discounts.id=memeber_discount_card_details.member_discount_id","left");
	  $this->db->where("memeber_discount_card_details.id",$id);
	  $q=$this->db->get("memeber_discount_card_details");
	  if($q->num_rows()>0){
		return   $q->row();
	  }
	  return false;
  }
    function get_discount(){
	  $this->db->select("*");
	  $q=$this->db->get("member_discounts");
	  if($q->num_rows()>0){
		foreach($q->result() as $row){
		$data[]=$row;
	   }
	   return $data;
	  }
	  return false;
  }
  
  function get_card($discount_card_id){
	  $this->db->select("memberDiscountcards.id,card_no");
	  $this->db->join("memberDiscountcards","memberDiscountcards.card_details_id=memeber_discount_card_details.id","left");
	  $this->db->join("memeberDiscountcard_status","memeberDiscountcard_status.card_id=memberDiscountcards.id","left");
	  $this->db->where("memeber_discount_card_details.member_discount_id",$discount_card_id);
	  $this->db->where("memeberDiscountcard_status.status",1);
	  $q=$this->db->get("memeber_discount_card_details");
	  if($q->num_rows()>0){
			foreach($q->result() as $row){
				$data[]=$row;
			}
			return $data;
		}
		return false;
	  
  }
  
  function get_card_discount_details($discount_card_id){
	  $this->db->select("memeber_discount_card_details.to_date,selling_price,discount , discount_type  ");
	  $this->db->join("memeber_discount_card_details","memeber_discount_card_details.id=memberDiscountcards.card_details_id","left");
	  $this->db->join("member_discounts","member_discounts.id=memeber_discount_card_details.member_discount_id","left");
	  $this->db->where("memberDiscountcards.id",$discount_card_id);
	  $q=$this->db->get("memberDiscountcards");
	  if($q->num_rows()>0){
		  return $q->row();
	  }
	return false;	  
  }
  function get_customer(){
	  $this->db->select("id,name");
	  $this->db->where("group_id",3);
	  $q=$this->db->get("companies");
	  if($q->num_rows()>0){
		  foreach($q->result() as $row){
			$data[]=$row;  
		  }
		  return $data;
	  }
	  return false;
  }
  function get_discount_issue_card_details($issue_card_id){
	  $this->db->select("memberDisountcard_issued.*,memberdiscountcards.card_no,member_discounts.name,companies.name customer");
	  $this->db->join("memberdiscountcards","memberdiscountcards.id=memberDisountcard_issued.card_id","left");
	  $this->db->join("member_discounts","member_discounts.id=memberDisountcard_issued.member_discount_id","left");
	  $this->db->join("companies","companies.id=memberDisountcard_issued.customer_id","left");
	  $this->db->where("memberDisountcard_issued.id",$issue_card_id);
	  $q=$this->db->get("memberDisountcard_issued");
	
	  if($q->num_rows()>0){
		  return $q->row();
	  }
	  return false;
  }
  function get_cardlistById($discount_id,$current_card_id){
	  $this->db->select("memberDiscountcards.id id, card_no");
	  $this->db->join("memeber_discount_card_details","memeber_discount_card_details.id=memberDiscountcards.card_details_id","left");
	  $this->db->join("memeberDiscountcard_status","memeberDiscountcard_status.card_id=memberDiscountcards.id","left");
	  $this->db->where("memeberDiscountcard_status.status",1);
	  $this->db->or_where_in("memberDiscountcards.id",$current_card_id);
	  $this->db->where("memeber_discount_card_details.member_discount_id",$discount_id);
	  $q=$this->db->get("memberDiscountcards");
	  if($q->num_rows()>0){
		  foreach($q->result() as $row){
			$data[]=$row;  
		  }
		  return $data;
	  }
	  return false;
  }
  function add_discounts_issue_card($data){
	  $card_details=$this->db->get_where("memberDiscountcards",array("id"=>$data['card_id']))->row();
	  $data['card_details_id']=$card_details->card_details_id;
	  if($this->db->insert("memberDisountcard_issued",$data)){
		  $this->db->where("card_id",$data['card_id']);
		  $this->db->update("memeberDiscountcard_status",array("status"=>2,"issued_on"=>date("Y-m-d")));
		  
	  }
	  return true;
  }
   function update_discounts_issue_card($data,$id){
	    $issue_old_card_data=$this->db->get_where("memberDisountcard_issued",array("id"=>$id))->row();
	    $this->db->where("card_id",$issue_old_card_data->card_id);
		$this->db->update("memeberDiscountcard_status",array("status"=>1));
	    $card_details=$this->db->get_where("memberDiscountcards",array("id"=>$data['card_id']))->row();
	    $data['card_details_id']=$card_details->card_details_id;
		$this->db->where("id",$id);
	   if($this->db->update("memberDisountcard_issued",$data)){
		  $this->db->where("card_id",$data['card_id']);
		  $this->db->update("memeberDiscountcard_status",array("status"=>2,"issued_on"=>date("Y-m-d")));
		  
	  }
	  return true;
  }
  function delete_issue_card($id){
	   $issue_old_card_data=$this->db->get_where("memberDisountcard_issued",array("id"=>$id))->row();
	   $this->db->where("card_id",$issue_old_card_data->card_id);
	   if($this->db->update("",array("status"=>1))){
		   $this->db->where("id",$id);
		   $this->db->delete("memberDisountcard_issued");
	   }
	  return true;
  }
  function block_issued_card($id){
	   $issue_old_card_data=$this->db->get_where("memberDisountcard_issued",array("id"=>$id))->row();
	   switch ($issue_old_card_data->status){
		   case 4:
		     $staus=array("status"=>2);
		   break;
		   case 2:
		     $staus=array("status"=>4);
		   break;
	   }
	   $this->db->where("card_id",$issue_old_card_data->card_id);
	   $this->db->update("memberDisountcard_issued",$staus);
	   $this->db->where("card_id",$issue_old_card_data->card_id);
	   $this->db->update("memeberDiscountcard_status",$staus);
	   return true;
  }
  function get_discount_card_status($discount_card_id){
	  $this->db->select("memberDiscountcards.id,card_no,memeberDiscountcard_status.status, issued_on ,           blocked_on");
	  $this->db->join("memberDiscountcards","memberDiscountcards.card_details_id=memeber_discount_card_details.id","left");
	  $this->db->join("memeberDiscountcard_status","memeberDiscountcard_status.card_id=memberDiscountcards.id","left");
	  $this->db->where("memeber_discount_card_details.member_discount_id",$discount_card_id);
	  $q=$this->db->get("memeber_discount_card_details");
	  if($q->num_rows()>0){
			foreach($q->result() as $row){
				$data[]=$row;
			}
			return $data;
		}
		return false;
	  
  }
}