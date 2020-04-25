<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Shiftmaster extends MY_Controller
{

    function __construct()
    {
        parent::__construct();

        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }

        if (!$this->Owner) {
            //$this->session->set_flashdata('warning', lang('access_denied'));
            //redirect('admin');
        }
        $this->lang->admin_load('settings', $this->Settings->user_language);
        $this->load->library('form_validation');
        $this->load->admin_model('shiftmaster_model');
        $this->upload_path = 'assets/uploads/';
        $this->thumbs_path = 'assets/uploads/thumbs/';
        $this->image_types = 'gif|jpg|jpeg|png|tif';
        $this->digital_file_types = 'zip|psd|ai|rar|pdf|doc|docx|xls|xlsx|ppt|pptx|gif|jpg|jpeg|png|tif';
        $this->allowed_file_size = '1024';
    }

    function index($warehouse_id = NULL)
    {
		$this->sma->checkPermissions('index',true);
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('shiftmaster')));
        $meta = array('page_title' => lang('shiftmaster'), 'bc' => $bc);            
        $this->page_construct('shiftmaster/index', $meta, $this->data);

        
    }
    function getShiftmaster()
    {

        $this->load->library('datatables');
        $this->datatables
          
        ->select("'sno',id, code, name, from_time, to_time, status")
            ->from("shiftmaster")
            ->add_column("Actions", "<div class=\"text-center\"><a class=\"tip\" title='" . $this->lang->line("edit_shiftmaster") . "' href='" . admin_url('shiftmaster/edit/$1') . "'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . $this->lang->line("delete_shiftmaster") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger' href='" . admin_url('shiftmaster/delete/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", "id")
        ->edit_column('status', '$1__$2', 'status, id');
    echo $this->datatables->generate();
    }

    public function add()
    {
		$this->sma->checkPermissions('add',true);
        $this->form_validation->set_rules('code', $this->lang->line("code"), 'required');   
		$this->form_validation->set_rules('name', $this->lang->line("name"), 'required');   
		         
        if ($this->form_validation->run() == true) {  
			$from_time =  $this->input->post('from_hours').':'.$this->input->post('from_minutes').':00';
			$to_time =  $this->input->post('to_hours').':'.$this->input->post('to_minutes').':00';      
           $check = $this->shiftmaster_model->checkShiftmaster($from_time, $to_time);
		   if($check == TRUE){
			  $this->session->set_flashdata('error', $this->lang->line("shiftmaster_already_exit"));
              admin_redirect('shiftmaster'); 
		   }
            $date = date('Y-m-d H:i:s');
            
            $data = array('code' => $this->input->post('code'),
                'name' => $this->input->post('name'),
                'from_time' => $from_time,
                'to_time' => $to_time,
                'status' => 1,
                'created_on' => $date,
            );

          
		  
        }
        if ($this->form_validation->run() == true && $this->shiftmaster_model->addShiftmaster($data)) {
            $this->session->set_flashdata('message', $this->lang->line("shiftmaster_added"));
            admin_redirect('shiftmaster');
        } else {            

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));                    
            $this->load->helper('string');
            $value = random_string('alnum', 20);
           
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('shiftmaster'), 'page' => lang('shiftmaster')), array('link' => '#', 'page' => lang('add_shiftmaster')));
            $meta = array('page_title' => lang('add_shiftmaster'), 'bc' => $bc);
            $this->page_construct('shiftmaster/add', $meta, $this->data);
        }
    }

    public function edit($id = NULL)
    {
        $this->sma->checkPermissions('edit',true);
       
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
		$shiftmaster = $this->shiftmaster_model->getShiftmasterByID($id);
        $this->form_validation->set_rules('code', $this->lang->line("code"), 'required');   
		$this->form_validation->set_rules('name', $this->lang->line("name"), 'required');   

        if ($this->form_validation->run() == true) {
           
            $from_time =  $this->input->post('from_hours').':'.$this->input->post('from_minutes').':00';
			$to_time =  $this->input->post('to_hours').':'.$this->input->post('to_minutes').':00';   
			if($shiftmaster->from_time != $from_time || $shiftmaster->to_time != $to_time){   
			   $check = $this->shiftmaster_model->checkShiftmaster($from_time, $to_time, $id);
			   if($check == TRUE){
				  $this->session->set_flashdata('error', $this->lang->line("shiftmaster_already_exit"));
				  admin_redirect('shiftmaster'); 
			   }
			}
            $date = date('Y-m-d H:i:s');
            
            $data = array(
                'name' => $this->input->post('name'),
                'from_time' => $from_time,
                'to_time' => $to_time,
            );
            
                
        }
        if ($this->form_validation->run() == true && $this->shiftmaster_model->updateShiftmaster($id,$data)) {
            
            $this->session->set_flashdata('message', $this->lang->line("shiftmaster_updated"));
            admin_redirect('shiftmaster');
        } else {
            
            $this->data['id'] = $id;            
            $this->data['shiftmaster'] = $shiftmaster;
            if(empty($this->data['shiftmaster'])){
                admin_redirect('shiftmaster');
            }
            $this->data['shiftmaster_id'] = $id;                                    
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('shiftmaster'), 'page' => lang('shiftmaster')), array('link' => '#', 'page' => lang('edit_shiftmaster')));
            $meta = array('page_title' => lang('edit_shiftmaster'), 'bc' => $bc);
            $this->page_construct('shiftmaster/edit', $meta, $this->data);
        }
    }  
	 
    function delete($id = NULL)
    {
		$this->sma->checkPermissions('delete',true);
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        //$delete_check = $this->shiftmaster_model->checkLoyalused($id);
        //if($delete_check == FALSE){            
            
			if ($this->shiftmaster_model->deleteShiftmaster($id)) {
			   
				$this->session->set_flashdata('message', lang('shiftmaster_deleted'));
				admin_redirect('shiftmaster');
			}
       // }else{            
           // $this->sma->send_json(array('error' => 1, 'msg' => lang("could_not_be_delete_shiftmaster_issued_to_user")));  
        //}

    }
}
    
   