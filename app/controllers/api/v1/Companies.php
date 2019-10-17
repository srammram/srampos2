<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class Companies extends REST_Controller {

    function __construct() {
        parent::__construct();

        $this->methods['index_get']['limit'] = 500;
        $this->load->api_model('companies_api');
        $this->load->library('form_validation');
    }

    public function index_get() {
        $name = $this->get('name');

        $filters = [
        'name' => $name,
        'include' => $this->get('include') ? explode(',', $this->get('include')) : NULL,
        'group' => $this->get('group') ? $this->get('group') : 'customer',
        'start' => $this->get('start') && is_numeric($this->get('start')) ? $this->get('start') : 1,
        'limit' => $this->get('limit') && is_numeric($this->get('limit')) ? $this->get('limit') : 10,
        'order_by' => $this->get('order_by') ? explode(',', $this->get('order_by')) : ['company', 'acs'],
        ];

        if ($name === NULL) {

            if ($companies = $this->companies_api->getCompanies($filters)) {
                $pr_data = [];
                foreach ($companies as $company) {
                    if (!empty($filters['include'])) {
                        foreach ($filters['include'] as $include) {
                            if ($include == 'user') {
                                $company->users = $this->companies_api->getCompanyUser($company->id);
                            }
                        }
                    }
                    
                    $pr_data[] = $this->setCompany($company);
                }

                $data =  [
                'data' => $pr_data,
                'limit' => $filters['limit'],
                'start' => $filters['start'],
                'total' => $this->companies_api->countCompanies($filters),
                ];
                $this->response($data, REST_Controller::HTTP_OK);

            } else {
                $this->response([
                    'message' => 'No company were found.',
                    'status' => FALSE,
                    ], REST_Controller::HTTP_NOT_FOUND);
            }

        } else {

            if ($company = $this->companies_api->getCompany($filters)) {

                if (!empty($filters['include'])) {
                    foreach ($filters['include'] as $include) {
                        if ($include == 'user') {
                            $company->users = $this->companies_api->getCompanyUser($company->id);
                        }
                    }
                }

                $company = $this->setCompany($company);
                $this->set_response($company, REST_Controller::HTTP_OK);

            } else {
                $this->set_response([
                    'message' => 'Company could not be found for name '.$name.'.',
                    'status' => FALSE,
                    ], REST_Controller::HTTP_NOT_FOUND);
            }
        }
    }

    protected function setCompany($company) {
        $company->company = !empty($company->company) && $company->company != '-' ? $company->company : NULL;
        $company->person = $company->name;
        if ($company->group_name == 'customer') {
            unset($company->id, $company->group_id, $company->group_name, $company->invoice_footer, $company->logo, $company->name);
        } elseif ($company->group_name == 'supplier') {
            unset($company->id, $company->group_id, $company->group_name, $company->invoice_footer, $company->logo, $company->customer_group_id, $company->customer_group_name, $company->deposit_amount, $company->payment_term, $company->price_group_id, $company->price_group_name, $company->award_points, $company->name);
        } elseif ($company->group_name == 'biller') {
            $company->logo = base_url('assets/uploads/logos/'.$company->logo);
            unset($company->id, $company->group_id, $company->group_name, $company->customer_group_id, $company->customer_group_name, $company->deposit_amount, $company->payment_term, $company->price_group_id, $company->price_group_name, $company->award_points, $company->name);
        }
        $company = (array) $company;
        ksort($company);
        return $company;
    }
    public function addcustomer_post(){
		$api_key = $this->input->post('api-key');
		$devices_key = $this->input->post('devices_key');		
                $order_type = $this->input->post('order_type');
		$this->form_validation->set_rules('devices_key', $this->lang->line("devices_key"), 'required');
		$this->form_validation->set_rules('name', $this->lang->line("name"), 'required');
		$this->form_validation->set_rules('phone', $this->lang->line("phone"), 'required');
                if($order_type==3){
		    $this->form_validation->set_rules('landmark', $this->lang->line("landmark"), 'required');
                    $this->form_validation->set_rules('address', $this->lang->line("address"), 'required');
                }
		
		if ($this->form_validation->run() == true) {
		    if(!$this->companies_api->isphoneExist($this->input->post('phone'))){
		    
		 	$devices_check = $this->site->devicesCheck($api_key);
			if($devices_check == $devices_key){
				$cg = $this->site->getCustomerGroupByID(1);
				$customer['name'] = $this->input->post('name');
				$customer['phone'] = $this->input->post('phone');
				$customer['mobile_number'] = '';
				$customer['email'] = '';
				//$customer['supplier_type'] = $this->input->post('customer_type');
				$customer['created_on'] = date('Y-m-d H:i:s');
				$customer['group_id'] = 3;
				$customer['group_name'] ='customer';
				$customer['ref_id'] ='CUS-'.date('YmdHis');
				$customer['customer_group_name'] = $cg->name;
				if($order_type==3){
				    $customer['address'] = $this->input->post('address');
				    $customer['landmark'] = $this->input->post('landmark');
				}
				$customer['customer_group_id'] = 1;
				$data = $this->companies_api->addCompany($customer);
                $customer['customer_id'] = $data;                
				if($data){
				    
					$result = array( 'status'=> true , 'message'=> lang('customer_added'),'message_khmer'=> html_entity_decode(lang('customer_added')), 'data' => $customer);
				}else{
					$result = array( 'status'=> false , 'message'=> lang('customer_not_added'),'message_khmer'=> html_entity_decode(lang('customer_not_added')));
				}
			}else{
				$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
			}
		    }else{
			$result = array( 'status'=>false , 'message'=> lang('phone_number_already_exist'),'message_khmer'=> html_entity_decode(lang('phone_number_already_exist')));	
		    }
		}else{
			$result = array( 'status'=> false , 'message'=> lang('please_enter_all_fields'),'message_khmer'=> html_entity_decode(lang('please_enter_all_fields_khmer')));	
		}
		$this->response($result);
	}
   
    public function customers_get(){
		$api_key = $this->input->get('api-key');
		$devices_key = $this->input->get('devices_key');
		$user_id = $this->input->get('user_id');
		$group_id = $this->input->get('group_id');
		$warehouse_id = $this->input->get('warehouse_id');
		
		$devices_check = $this->site->devicesCheck($api_key);
		if($devices_check == $devices_key){
                        $customer_groups = $this->companies_api->getAllCustomerGroups();
			$data = $this->companies_api->GetAllcustomers();
			$possetting = $this->companies_api->getPOSSettingsALL();
			if(!empty($data)){
				$result = array( 'status'=> true , 'message'=> lang('customer'),'message_khmer'=> html_entity_decode(lang('customer_khmer')), 'data' => $data, 'customer_groups'=>$customer_groups,'default_customer' => $possetting->default_customer);
			}else{
				$result = array( 'status'=> false , 'message'=> lang('customer_empty'),'message_khmer'=> html_entity_decode(lang('customer_empty_khmer')));
			}
		}else{
			$result = array( 'status'=> false , 'message'=> lang('devices_key_does_not_matche_please_check_your_devices_key'),'message_khmer'=> html_entity_decode(lang('devices_key_does_not_matche_please_check_your_devices_key_khmer')));	
		}
		$this->response($result);
    }

}
