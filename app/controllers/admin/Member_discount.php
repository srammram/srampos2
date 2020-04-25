<?php defined('BASEPATH') or exit('No direct script access allowed');

class Member_discount extends MY_Controller{
    public function __construct(){
        parent::__construct();
        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }
	$this->lang->admin_load('settings', $this->Settings->user_language);
    $this->load->library('form_validation');
    $this->load->admin_model('member_discount_model');
	
	
    }
    /************************ memeber- discount ***********************/
    
    function index(){
	//$this->sma->checkPermissions();
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('member_discount'), 'page' => lang('member_discount')), array('link' => '#', 'page' => lang('memeber_discount')));
        $meta = array('page_title' => lang('member_discount'), 'bc' => $bc);
        $this->page_construct('member_discount/index', $meta, $this->data);
    }	

    function get_discount(){  
	//$this->sma->checkPermissions();
        $this->load->library('datatables');
		   $this->datatables
           ->select("'sno',id, name, created_dt,status")
            ->from("member_discounts")
            ->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('member_discount/view_discount/$1') . "' class='btn-primary btn-xs tip' title='" . lang("view_discount") . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-eye\"></i></a><a style='margin-left:2px;' href='" . admin_url('member_discount/edit_discount/$1') . "' class='tip' title='" . lang("edit discount") . "' ><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang("delete_discount") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('member_discount/delete_discount/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", "id")
			 ->edit_column('status', '$1__$2', 'status, id');
        //->unset_column('id');
        echo $this->datatables->generate();
	
    }  
	 public function view_discount($id = null){
         $this->data['page_title'] = lang('View_member_discount');        
			$this->data['discount'] =  $this->member_discount_model->get_discount_details($id);	
         $this->data['id'] = $id;
         $this->data['modal_js'] = $this->site->modal_js();
         $this->load->view($this->theme . 'member_discount/view_discount', $this->data);
    }   
	
    function add_discounts(){
		//$this->sma->checkPermissions();
		$this->form_validation->set_rules('name', lang("name"), 'required');
		$this->form_validation->set_rules('start_date', lang("start_date"), 'required');
		$this->form_validation->set_rules('end_date', lang("end_date"), 'required');
		$this->form_validation->set_rules('start_time', lang("start_time"), 'required');
		$this->form_validation->set_rules('end_time', lang("end_time"), 'required');
        if ($this->form_validation->run() == true) {
            $array = array('name' => $this->input->post('name'),
                'from_date' => date('Y-m-d', strtotime($this->input->post('start_date'))),
				'to_date' => date('Y-m-d', strtotime($this->input->post('end_date'))), 
				'from_time' => $this->input->post('start_time'),
				'to_time' => $this->input->post('end_time'),
				'status' => $this->input->post('status'),
                'created_dt' =>date('Y-m-d-H-i-s'),
				'discount' => $this->input->post('discount'),
				'discount_type' =>$this->input->post('discount_type'),
				'week_days' => implode(',', $this->input->post('weekdays')) ? implode(',', $this->input->post('weekdays')) : ''     
            );
			
        } elseif ($this->input->post('add__discounts')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("member_discount/add_discounts");
        }
        if ($this->form_validation->run() == true && $this->member_discount_model->add_discounts($array)) {
            $this->session->set_flashdata('message', lang("Memeber Discount Added"));
             admin_redirect("member_discount/index");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
		$bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('member_discount'), 'page' => lang('member_discount')), array('link' => '#', 'page' => lang('member_discount')));
            $meta = array('page_title' => lang('member_discount'), 'bc' => $bc);
			$this->page_construct('member_discount/add_discount',$meta,$this->data);
        }
    }
   function edit_discount($id = NULL){
        $dis_details = $this->member_discount_model->get_discount_details($id); 
		$this->form_validation->set_rules('name', lang("name"), 'required');
		$this->form_validation->set_rules('start_date', lang("start_date"), 'required');
		$this->form_validation->set_rules('end_date', lang("end_date"), 'required');
		$this->form_validation->set_rules('start_time', lang("start_time"), 'required');
		$this->form_validation->set_rules('end_time', lang("end_time"), 'required');
        if ($this->form_validation->run() == true) {
            $array = array('name' => $this->input->post('name'),
                'from_date' => date('Y-m-d', strtotime($this->input->post('start_date'))),
				'to_date' => date('Y-m-d', strtotime($this->input->post('end_date'))),
				'from_time' => $this->input->post('start_time'),
				'to_time' => $this->input->post('end_time'),
				'status' => $this->input->post('status'),
                'created_dt' =>date('Y-m-d-H-i-s'),
				'discount' => $this->input->post('discount'),
				'discount_type' =>$this->input->post('discount_type'),
				'week_days' => implode(',', $this->input->post('weekdays')) ? implode(',', $this->input->post('weekdays')) : ''     
            );
        } elseif ($this->input->post('edit__discounts')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("member_discount/index");
        }
        if ($this->form_validation->run() == true && $this->member_discount_model->update($array, $id)) {
            $this->session->set_flashdata('message', lang("memeber discount updated"));
            admin_redirect("member_discount/index");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			$bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('member_discount'), 'page' => lang('member_discount')), array('link' => '#', 'page' => lang('member_discount')));
            $meta = array('page_title' => lang('member_discount'), 'bc' => $bc);
			$this->data['id'] = $id;
			$this->data['discount'] = $dis_details;
			$this->page_construct('member_discount/edit_discount', $meta, $this->data);
        }
    }
	
	function delete_discount($id = NULL){
	//$this->sma->checkPermissions();
        if ($this->member_discount_model->delete($id)) {
            $this->sma->send_json(array('error' => 0, 'msg' => lang("discount_deleted")));
        }
    }
	
	
	
	
	
	
	
    /////////////memeber discount card creation ///////////////////////////////////////////////
	
	
	 function member_discount_card(){
	//$this->sma->checkPermissions();
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('member_discount_card'), 'page' => lang('member_discount')), array('link' => '#', 'page' => lang('memeber_discount_card')));
        $meta = array('page_title' => lang('member_discount_card'), 'bc' => $bc);
        $this->page_construct('member_discount/member_discount_card', $meta, $this->data);
    }	

    function get_discount_card(){  
	       //$this->sma->checkPermissions();
           $this->load->library('datatables');
		   $this->datatables
           ->select("'sno',memeber_discount_card_details.id as card_details_id,member_discounts.name,prefix,  serial_no, no_of_vouchers") 
            ->from("memeber_discount_card_details")
			->join("member_discounts","member_discounts.id=memeber_discount_card_details.member_discount_id","left")
            ->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('member_discount/view_card_status/$1') . "' class='btn-primary btn-xs tip' title='" . lang("view_card_status") . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-credit-card\"></i></a><a style='margin-left:2px;'href='" . admin_url('member_discount/view_member_discount_card/$1') . "' class='btn-primary btn-xs tip' title='" . lang("view_member_discount_card") . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-eye\"></i></a><a style='margin-left:2px;' href='" . admin_url('member_discount/edit_member_discount_card/$1') . "' class='tip' title='" . lang("edit_member_discount_card") . "' ><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang("delete_member_discount_card") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('member_discount/delete_member_discount_card/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", "card_details_id");
		//	 ->edit_column('status', '$1__$2', 'status, id');
        //->unset_column('id');
        echo $this->datatables->generate();
	
    }  
	 public function view_member_discount_card($id = null){
         $this->data['page_title'] = lang('View_member_discount_card');        
	     $this->data['discount_card'] =  $this->member_discount_model->get_discount_card_details($id);	
         $this->data['id'] = $id;
         $this->data['modal_js'] = $this->site->modal_js();
         $this->load->view($this->theme . 'member_discount/view_memeber_discount_card', $this->data);
    }   
	public function view_card_status($id){
		 $this->data['page_title'] = lang('View_member_discount_card_status');     
		 $this->data['discount_card'] =  $this->member_discount_model->get_discount_card_details($id);		 
	     $this->data['discount_card_status'] =  $this->member_discount_model->get_discount_card_status($id);	
         $this->data['id'] = $id;
         $this->data['modal_js'] = $this->site->modal_js();
         $this->load->view($this->theme . 'member_discount/view_memeber_discount_card_status', $this->data);
	}
	
    function add_member_discount_card(){
		//$this->sma->checkPermissions();
		$this->form_validation->set_rules('prefix', lang("prefix"), 'required');
		$this->form_validation->set_rules('valid_from', lang("valid_from"), 'required');
		$this->form_validation->set_rules('valid_upto', lang("valid_upto"), 'required');
        if ($this->form_validation->run() == true) {
          $array = array('prefix' => $this->input->post('prefix'),
			   'member_discount_id'=>$this->input->post('discount'),
			   'selling_price'=>$this->input->post('selling_price'),
				'serial_no' => $this->input->post('serial_no'),
				'no_of_vouchers' => $this->input->post('vouchers'),
				'valid_req' => $this->input->post('valid_req'),
				'from_date' => date('Y-m-d', strtotime($this->input->post('valid_from'))),
				'to_date' => date('Y-m-d', strtotime($this->input->post('valid_upto'))),      
            );
	
        } elseif ($this->input->post('add_card')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("member_discount/add_member_discount_card");
        }
        if ($this->form_validation->run() == true && $this->member_discount_model->add_discounts_card($array)) {
            $this->session->set_flashdata('message', lang("Memeber Discount card Added"));
             admin_redirect("member_discount/member_discount_card");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error')); 
			$this->data['discount']=$this->member_discount_model->get_discount();
		    $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('member_discount_card'), 'page' => lang('member_discount')), array('link' => '#', 'page' => lang('add_memeber_discount_card')));
            $meta = array('page_title' => lang('member_discount_card'), 'bc' => $bc);
			$this->page_construct('member_discount/add_memeber_discount_card',$meta,$this->data);
        }
    }
   function edit_member_discount_card($id = NULL){
        $dis_card_details = $this->member_discount_model->get_discount_card_details($id); 
		$this->form_validation->set_rules('prefix', lang("prefix"), 'required');
		$this->form_validation->set_rules('valid_from', lang("valid_from"), 'required');
		$this->form_validation->set_rules('valid_upto', lang("valid_upto"), 'required');
        if ($this->form_validation->run() == true) {
        $array = array('prefix' => $this->input->post('prefix'),
			   'member_discount_id'=>$this->input->post('discount'),
			   'selling_price'=>$this->input->post('selling_price'),
				'serial_no' => $this->input->post('serial_no'),
				'no_of_vouchers' => $this->input->post('vouchers'),
				'valid_req' => $this->input->post('valid_req'),
				'from_date' => date('Y-m-d', strtotime($this->input->post('valid_from'))),
				'to_date' => date('Y-m-d', strtotime($this->input->post('valid_upto'))),      
            );
			
        } elseif ($this->input->post('edit__discounts')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("member_discount/edit_memeber_discount_card");
        }
        if ($this->form_validation->run() == true && $this->member_discount_model->update_card($array, $id)) {
            $this->session->set_flashdata('message', lang("memeber discount updated"));
            admin_redirect("member_discount/member_discount_card");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			$bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('member_discount'), 'page' => lang('member_discount')), array('link' => '#', 'page' => lang('member_discount')));
            $meta = array('page_title' => lang('edit_member_discount_card'), 'bc' => $bc);
			$this->data['id'] = $id;
			$this->data['discount']=$this->member_discount_model->get_discount();
			$this->data['discount_card'] = $dis_card_details;
			$this->page_construct('member_discount/edit_memeber_discount_card', $meta, $this->data);
        }
    }
	
	function delete_member_discount_card($id = NULL){
	//$this->sma->checkPermissions();
        if ($this->member_discount_model->delete_card($id)) {
            $this->sma->send_json(array('error' => 0, 'msg' => lang("discount_deleted")));
        }
    }
	
	
	function member_discount_card_issue(){
		$bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('member_discount_card'), 'page' => lang('member_discount')), array('link' => '#', 'page' => lang('issue_card')));
        $meta = array('page_title' => lang('issue_card'), 'bc' => $bc);
        $this->page_construct('member_discount/issue_card', $meta, $this->data);
		
	}
     function get_issue_card(){  
	//$this->sma->checkPermissions();
        $this->load->library('datatables');
		   $this->datatables
           ->select("'sno',memberDisountcard_issued.id as issued_id,companies.name, card_no,memberDisountcard_issued.status as status ") 
            ->from("memberDisountcard_issued")
			->join("member_discounts","member_discounts.id=memberDisountcard_issued.card_details_id","left")
			->join("memberDiscountcards","memberDiscountcards.id=memberDisountcard_issued.card_id","left")
			->join("companies","companies.id=memberDisountcard_issued.customer_id","left")
            ->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('member_discount/view_issue_card/$1') . "' class='btn-primary btn-xs tip' title='" . lang("view_issue_card") . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-eye\"></i></a><a style='margin-left:2px;' href='" . admin_url('member_discount/edit_issue_card/$1') . "' class='tip' title='" . lang("edit_issue_card") . "' ><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang("delete_member_discount_card") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('member_discount/delete_member_discount_card/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", "issued_id")
		 ->edit_column('status', '$1__$2', 'status, issued_id');
        //->unset_column('id');
        echo $this->datatables->generate(); 
		
	
    }  
	 public function view_issue_card($id = null){
         $this->data['page_title'] = lang('View_issue_card');    
		 $dis_issue_card_details = $this->member_discount_model->get_discount_issue_card_details($id); 		 
	     $this->data['issue_card'] = $dis_issue_card_details;
         $this->data['id'] = $id;
         $this->data['modal_js'] = $this->site->modal_js();
         $this->load->view($this->theme . 'member_discount/view_issue_card', $this->data);
    }   
	
    function add_issue_card(){
		//$this->sma->checkPermissions();
		$this->form_validation->set_rules('discount', lang("discount"), 'required');
		$this->form_validation->set_rules('discount_card', lang("discount_card"), 'required');
		$this->form_validation->set_rules('customer', lang("customer"), 'required');
        if ($this->form_validation->run() == true) {
          $array = array('member_discount_id' => $this->input->post('discount_id'),
			   'card_id'=>$this->input->post('discount_card'),
			   'customer_id'=>$this->input->post('customer'),
				'selling_price' => $this->input->post('selling_price'),
				'discounttype' => $this->input->post('discount_type'),
				'discount' => $this->input->post('discount'),
				'valid_upto' => date('Y-m-d', strtotime($this->input->post('valito'))),
				'created_on'=>date('Y-m-d'),
				'status'=>2
            );
        } elseif ($this->input->post('add_card')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("member_discount/add_issue_card");
        }
        if ($this->form_validation->run() == true && $this->member_discount_model->add_discounts_issue_card($array)) {
            $this->session->set_flashdata('message', lang("issued card Added"));
             admin_redirect("member_discount/member_discount_card_issue");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error')); 
			$this->data['discount']=$this->member_discount_model->get_discount();
			$this->data['customers']=$this->member_discount_model->get_customer();
		    $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('member_discount_card'), 'page' => lang('member_discount')), array('link' => '#', 'page' => lang('add_memeber_discount_card')));
            $meta = array('page_title' => lang('member_discount_card'), 'bc' => $bc);
			$this->page_construct('member_discount/add_issue_card',$meta,$this->data);
        }
    }
   function edit_issue_card($id = NULL){
        $dis_issue_card_details = $this->member_discount_model->get_discount_issue_card_details($id); 
		$this->form_validation->set_rules('discount', lang("discount"), 'required');
		$this->form_validation->set_rules('discount_card', lang("discount_card"), 'required');
		$this->form_validation->set_rules('customer', lang("customer"), 'required');
        if ($this->form_validation->run() == true) {
         $array = array('member_discount_id' => $this->input->post('discount_id'),
			   'card_id'=>$this->input->post('discount_card'),
			   'customer_id'=>$this->input->post('customer'),
				'selling_price' => $this->input->post('selling_price'),
				'discounttype' => $this->input->post('discount_type'),
				'discount' => $this->input->post('discount'),
				'valid_upto' => date('Y-m-d', strtotime($this->input->post('valito'))),
				'created_on'=>date('Y-m-d'),
				'status'=>2
            );
        } elseif ($this->input->post('edit__discounts')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("member_discount/edit_issue_card");
        }
        if ($this->form_validation->run() == true && $this->member_discount_model->update_discounts_issue_card($array, $id)) {
            $this->session->set_flashdata('message', lang("memeber discount updated"));
            admin_redirect("member_discount/member_discount_card_issue");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			$bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('member_discount'), 'page' => lang('member_discount')), array('link' => '#', 'page' => lang('member_discount')));
            $meta = array('page_title' => lang('edit_member_discount_card'), 'bc' => $bc);
			$this->data['id'] = $id;
			$this->data['discount']=$this->member_discount_model->get_discount();
			$this->data['issue_card'] = $dis_issue_card_details;
			$this->data['card_list']=$this->member_discount_model->get_cardlistById($dis_issue_card_details->member_discount_id,$dis_issue_card_details->card_id);
		    $this->data['customers']=$this->member_discount_model->get_customer();
			$this->page_construct('member_discount/edit_issue_card', $meta, $this->data);
        }
    }
	
	  function delete_issue_card($id = NULL){
	//$this->sma->checkPermissions();
        if ($this->member_discount_model->delete_issue_card($id)) {
            $this->sma->send_json(array('error' => 0, 'msg' => lang("discount_deleted")));
        }
    }
	function block_issued_card($id = NULL){
		if ($this->member_discount_model->block_issued_card($id)) {
			 $this->session->set_flashdata('message', lang("card staus Was Changed"));
             admin_redirect("member_discount/member_discount_card_issue");
          
        }
	}
		function get_ajax_card_details(){
		if ($this->input->post('discount_id')) {
            $discount_id = $this->input->post('discount_id', TRUE);
        }
		$rows= $this->member_discount_model->get_card($discount_id);
       echo json_encode($rows);
	}
	function  get_discount_card_details(){
		if ($this->input->post('card_id')) {
            $discount_card_id = $this->input->post('card_id', TRUE);
        }
			$rows= $this->member_discount_model->get_card_discount_details($discount_card_id);
       echo json_encode($rows);
		
	}
}