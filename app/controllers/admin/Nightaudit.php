<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Nightaudit extends MY_Controller
{

    function __construct()
    {
        parent::__construct();

        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }

        if (!$this->Owner) {
           // $this->session->set_flashdata('warning', lang('access_denied'));
          //  redirect('admin');
        }
		$this->lang->admin_load('sma', $this->Settings->user_language);
        $this->load->library('form_validation');
        $this->load->admin_model('nightaudit_model');
        
    }

	/* Tables*/
	
	function index($dates = NULL, $warehouses_id = NULL)
    {
    	if($this->Settings->night_audit_rights == 0){
    		admin_redirect('welcome');
    	}
	$this->sma->checkPermissions();
		$this->session->userdata('user_id');		
		$id = $this->session->userdata('user_id');
     	$dates = $this->input->get('dates');  
		$warehouses_id = $this->input->get('warehouses_id');
		$warehouses              = $this->site->getAllWarehouses();
		$this->data['sales']     = $this->nightaudit_model->getDataviewSales($dates, $warehouses_id); 
		$this->data['status']    = $this->nightaudit_model->checkNightaudit($dates, $warehouses_id);
		$this->data['dates']     = $this->nightaudit_model->Check_Not_Closed_Nightaudit();
		$this->data['last_date'] = $this->nightaudit_model->Last_Nightaudit();
		$this->data['negative_stock'] = $this->nightaudit_model->getNegativeStock();
		$group_id 				 = $this->nightaudit_model->getUserGroupid($id);
		$this->data['p'] = $this->nightaudit_model->getGroupPermissions($group_id->group_id);
		$this->data['warehouses'] = $warehouses;
		$bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('nightaudit'), 'page' => lang('night_audit')), array('link' => '#', 'page' => lang('night_audit')));
        $meta = array('page_title' => lang('night_audit'), 'bc' => $bc);
        $this->page_construct('nightaudit', $meta, $this->data);
    }
	
	function getNightauditData($dates = NULL, $warehouses_id = NULL){
		$dates = $this->input->get('dates');
		$warehouses_id = $this->input->get('warehouses_id');
		$Last_Nightaudit =  $this->nightaudit_model->Last_Nightaudit();
		$before_date = date('Y-m-d', strtotime($dates . ' -1 day'));
		$before_status = $this->nightaudit_model->checkbeforedate($before_date, $warehouses_id);
		$sales = $this->nightaudit_model->getDataviewSales($dates, $warehouses_id); 
		$status = $this->nightaudit_model->checkNightaudit($dates, $warehouses_id);
		$total_sales = 0;
		$complete_sales = 0;
		$pending_sales = 0;
		foreach($sales as $sales_row){
			$total_sales++;
			if($sales_row->sale_status == 'Closed'){
				$complete[] = $sales_row->grand_total;
				$complete_sales++;
			}elseif($sales_row->sale_status == 'Process'){
				$pending[] = $sales_row->grand_total;
				$pending_sales++;
			}
			$total[] = $sales_row->grand_total;
		}
		$complete_sales;
		$pending_sales;
		$total_amount = array_sum($total);
		$complete_amount = array_sum($complete);
		$pending_amount = array_sum($pending);
		
		$row['total_sales'] = $total_sales;	
		$row['complete_sales'] = $complete_sales;	
		$row['pending_sales'] = $pending_sales;	
		$row['total_amount'] = $total_amount ? $total_amount : 0;	
		$row['complete_amount'] = $complete_amount ? $complete_amount : 0;	
		$row['pending_amount'] = $pending_amount ? $pending_amount : 0;	
		$row['status'] = $status;	
		$row['before_status'] = $before_status;	
		
		echo json_encode($row);
		exit;
	}
	
	public function actions(){
		$this->sma->checkPermissions('index');
		$data = array(
			'nightaudit_date' => $this->input->post('nightaudit_date'),
			'warehouse_id' => $this->input->post('warehouses_id'),
			'total_sales' => $this->input->post('total_sales'),
			'total_amount' => $this->input->post('total_amount'),
			'complete_sales' => $this->input->post('complete_sales'),
			'complete_amount' => $this->input->post('complete_amount'),
			'pending_sales' => $this->input->post('pending_sales'),
			'pending_amount' => $this->input->post('pending_amount'),
			'nightaudit' => $this->input->post('nightaudit'),
			'created' => date('Y-m-d H:m:s'),
			'created_by' => $this->session->userdata('user_id'),
		);	
		
		if ($this->nightaudit_model->addNightaudit($data)) {
			
            $this->session->set_flashdata('message', lang("Night Audit process complete"));
            admin_redirect('nightaudit');
        } else {
			$this->session->set_flashdata('error', lang("Unable to Do Night Audit Now"));
            admin_redirect('nightaudit');
		}
	}
	public function carry_forward($stock_id){
		if ($this->nightaudit_model->carryForward($stock_id)) {
            $this->session->set_flashdata('message', lang("Stock Carry Forward Done"));
            admin_redirect('nightaudit');
        } else {
			$this->session->set_flashdata('error', lang("Unable to Do Carry Forward"));
            admin_redirect('nightaudit');
		}
	}
   public function stock_request($stockId){
	      $n               = $this->siteprocurment->lastidStoreRequest();
          $reference       = 'SR'.str_pad($n + 1, 5, 0, STR_PAD_LEFT);
		  $date            = date('Y-m-d H:i:s');
          $stock=$this->nightaudit_model->getStockDetails($stockId);
		  
		  $data = array(
				'reference_no' 	=> $reference,
				'date' 			=> $date,
				'request_type' 	=> "Negative Stock",
				'from_store_id' => $this->store_id,
                'warehouse_id' 	=> $warehouse_id,
				'store_id' 	    => $this->store_id,
                'note' 			=> "Negative Stock Request",
                'status' 		=> "Process",
                'created_by' 	=> $this->session->userdata('user_id'),
				'created_on' 	=> date('Y-m-d H:i:s'),
				'total_no_items' =>1,
            );
			$row = $this->siteprocurment->getItemByID($stock->product_id);
            $unit = $this->siteprocurment->getUnitByID($row->unit);  
			$variant=$this->site->getRecipeVariantById($stock->variant_id);
			 $items= array(
                        'product_id'      => $row->id,
                        'product_code'    => $row->code,
                        'product_name'    => $row->name,
                        'product_type'    => $row->type,
                        'quantity'        => $this->site->baseToUnitQty(abs($stock->stock_in),$unit->operator,$unit->operation_value),
						'unit_quantity'   => $stock->stock_in,
                        'product_unit_id' => $row->purchase_unit,
						'product_unit_code'=>$unit->name,
						'option_id'=>$stock->variant_id,
						'option_name'=>($variant->name)?$variant->name:""
                    );
	    if ($this->nightaudit_model->generateStockRequest($data,$items,$stockId)) {
            $this->session->set_flashdata('message', lang("Stock Request Generated"));
            admin_redirect('nightaudit');
        } else {
			$this->session->set_flashdata('error', lang("Unable to Do Stock Request"));
            admin_redirect('nightaudit');
		}
	   
   }
   
   
   public function purchase_invoices_generate($stock_id){
	   $stock=$this->nightaudit_model->getStockDetails($stock_id);
	    $n = $this->siteprocurment->lastidPurchaseInv();
	    $n=($n !=0)?$n+1:$this->store_id .'1';
	    $reference = 'PI'.str_pad($n , 8, 0, STR_PAD_LEFT);
		$date=date('Y-m-d H:i:s');
	   $data = array(
            'reference_no' => $reference,
            'date' => $date,
			'store_id'=>$this->store_id,
            'invoice_no' =>strtotime() ,
            'invoice_date' =>  $date,
            'note' => "Invoice Generate from night audit",
            'tax_method' =>1,
            'status' => "process",
            'no_of_items' =>1,
            'no_of_qty' => 1,
            'created_by' => $this->session->userdata('user_id'),
            'created_on' => date('Y-m-d H:i:s'),
            'processed_by' => $this->session->userdata('user_id'),
            'processed_on' => date('Y-m-d H:i:s'),
            );
			$row = $this->siteprocurment->getItemByID($stock->product_id);
            $unit = $this->siteprocurment->getUnitByID($row->unit);   
            if($unit->name){
                $row->unit_name = $unit->name;
            }else{
                $row->unit_name = '';
            }
               if($row->type=="raw"|| $row->type=="semi_finished"){
				   $cm=$this->site->getSaleCategory_mapping($row->id);
				   $row->brand=$cm->brand_id;
				   $row->category_id=$cm->category_id;
				   $row->subcategory_id=$cm->subcategory_id;
			   }
				$brand=$this->site->getBrandByID($row->brand);
				$category=$this->site->getrecipeCategoryByID($row->category_id);
				$subcategory=$this->site->getrecipeCategoryByID($row->subcategory_id);
				$unit = $this->site->getUnitByID($row->purchase_unit);
				$product_unit_code=$unit->code;
		        $items['store_id']     = $this->store_id;
                $items['product_id']   = $row->id;
				$items['variant_id']   = $stock->variant_id;
				$items['product_code'] = $row->code;
				$items['product_name'] = $row->name;
				$items['quantity']     = $this->site->baseToUnitQty(abs($stock->stock_in),$unit->operator,$unit->operation_value);
				$items['po_qty']       = 0;
				$items['batch_no']     ="";
				$items['expiry']       = $row->expiry;
				$items['expiry_type']  = $row->type_expiry;
				$items['cost']         = $row->cost;
				$items['category_id'] = $row->category_id;
				$items['category_name'] = $category->name;
				$items['subcategory_id'] =$row->subcategory_id;
				$items['subcategory_name'] = $subcategory->name;
                $items['brand_id'] = $row->brand;
				$items['brand_name'] = $brand->name;
				$items['product_unit_code'] = $product_unit_code;
				$items['unit_quantity'] = abs($stock->stock_in);
				$items['product_unit_id'] = $row->purchase_unit;
				$items['parent_stock_unique_id'] = $stock_id;
				
	   if ($this->nightaudit_model->generateInvoice($data,$items,$stock_id)) {
            $this->session->set_flashdata('message', lang("Purchase Invoice Added"));
            admin_redirect('nightaudit');
        } else {
			$this->session->set_flashdata('error', lang("Unable to Do Purchase Invoice"));
            admin_redirect('nightaudit');
		}
	   
   }
}
