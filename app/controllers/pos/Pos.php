<?php defined('BASEPATH') or exit('No direct script access allowed');

class Pos extends MY_Controller{
    public function __construct(){
        parent::__construct();
        $this->pos_report_view_access = $this->session->userdata('pos_report_view_access') ? $this->session->userdata('pos_report_view_access') : 0;
        $this->pos_report_show = 0;
		
        if ($this->pos_report_view_access == 2) {
            $this->pos_report_show = 0;
        } elseif ($this->pos_report_view_access == 3) {
            $this->pos_report_show = 1;
        } else {
            $this->pos_report_show = 0;
        }
        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }
        if ($this->Customer || $this->Supplier) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER["HTTP_REFERER"]);
        }
        $this->lang->admin_load('posnew', $this->Settings->user_language);
        $this->load->model('pos/pos_model');
        $this->load->admin_model('settings_model');
        $this->load->helper('text');
        $this->load->helper('shop');
        $this->pos_settings = $this->pos_model->getSetting();
        $this->settings = $this->pos_model->getSettings();
        $this->pos_settings->pin_code = $this->pos_settings->pin_code ? md5($this->pos_settings->pin_code) : null;
        $this->data['pos_settings'] = $this->pos_settings;
        $this->data['settings'] = $this->settings;
        $this->session->set_userdata('last_activity', now());
        $this->lang->admin_load('pos', $this->Settings->user_language);
        $this->load->library('form_validation');
        $params = array(
            'host' => PRINTER_HOST,
            'port' => PRINTER_PORT,
            'path' => '',
        );
        $this->load->library('ws', $params);
        $this->load->library('firebase');
        $this->load->library('push');
        $this->data['multi_uniq_discounts'] = $this->site->is_uniqueDiscountExist('checkformulti');
		
    }
    public function index(){
                $t = $this->sma->checkPermissions('index');
				$this->data['till']=$this->site->get_till($this->till_id);
				$shift_id=!empty($this->ShiftID)?$this->ShiftID:0;
				$this->data['sales_details']=$this->pos_model->get_sales_details($shift_id,$this->till_id,$this->store_id);
				$this->data['KHR_sales_details']=$this->pos_model->get_sales_details_khr($shift_id,$this->till_id,$this->store_id);
				$this->data['USD_sales_details']=$this->pos_model->get_sales_details_usd($shift_id,$this->till_id,$this->store_id);
			    $this->data['user']=$user=$this->site->getUser_details($this->session->userdata('user_id'));
				$this->data['openning_cash']=$this->pos_model->get_openning_cash();
				$this->data['areas'] = $this->pos_model->getTablelist($this->session->userdata('warehouse_id'),$user->area_id);
				$this->data['active_area'] = $this->pos_model->active_area_name($user->area_id);
                $this->data['pos_settings'] = $this->pos_settings;
			    $this->data['avil_tables'] = $this->site->getAvilAbleTables_dineIn();
				$this->data['customer']        = $this->pos_model->getCompanyByID($this->pos_settings->default_customer);
				$this->data['usere_sales_details']=$this->pos_model->get_sales_details_byUserWise($shift_id,$this->till_id,$this->store_id,$this->session->userdata('user_id'));
                $this->load->view($this->theme . 'pos_v2/tables', $this->data);
    }
	public function home(){
		    $this->data['home']='Home';
	     	$this->load->view($this->theme . 'pos_v2/home', $this->data);
	}
   public function order($type = null){
	            $this->data['order_type']      = $order= !empty($_GET['type']) ? $_GET['type'] : '';
				$this->data['table_id']        = $table_id= !empty($_GET['table']) ? $_GET['table'] : '';
				 if(empty($table_id) && $order ==1){ redirect('pos/pos'); }
				$this->data['sprequest']       = !empty($_GET['spr']) ? $_GET['spr'] : '';
				$this->data['get_split']       = $split = !empty($_GET['split']) ? $_GET['split'] : '';
				$this->data['same_customer']   = $same_customer = !empty($_GET['same_customer']) ? $_GET['same_customer'] : ''; 
                $this->data['areas']           = $this->pos_model->getTablelist($this->session->userdata('warehouse_id'));
				$this->data['customer']        = $this->pos_model->getCompanyByID($this->pos_settings->default_customer);
                $this->data['billers']         = $this->site->getAllCompanies('biller');
                $this->data['sales_types']     = $this->site->getAllSalestype();
                $this->data['tables']          = $this->site->getTablesByID($table_id);
                $this->data['warehouses']      = $this->site->getAllWarehouses();
                $this->data['tax_rates']       = $this->site->getAllTaxRates();
                $this->data['user']            = $this->site->getUser();
                $this->data["tcp"]              = $this->pos_model->recipe_count($this->pos_settings->default_category, $this->session->userdata('warehouse_id'));
				if ($this->pos_settings->sales_item_in_pos == 1) {
                    $this->data['categories'] = $this->site->getAllrecipeCategories();
                } else { //by day wise item mappings
                    $this->data['categories'] = $this->site->getAllrecipeCategories_withdays($order);
                }
				if ($this->pos_settings->sales_item_in_pos == 1) {
                        $this->data['subcategories'] = $this->site->getrecipeSubCategories($this->pos_settings->default_category);
                    } else { // sub category list from mapping table with active items in recipe table
                        $this->data['subcategories'] = $this->site->getrecipeSubCategories_withdays($this->pos_settings->default_category, $order);
                    }
                $this->data['recipe'] = $this->ajaxrecipe_consolidate($this->pos_settings->default_category, $this->session->userdata('warehouse_id'));
                $this->load->view($this->theme . 'pos_v2/order', $this->data);

    }
public function ajaxrecipe_consolidate($category_id = null, $warehouse_id = null, $subcategory_id = null, $brand_id = null, $order_type = null){
        $this->sma->checkPermissions('index');
        if ($this->input->get('brand_id')) {
            $brand_id = $this->input->get('brand_id');
        }
        if ($this->input->get('category_id')) {
            $category_id = $this->input->get('category_id');
        } else {
            $category_id = $this->pos_settings->default_category;
        }
        if ($this->input->get('subcategory_id')) {
            $subcategory_id = $this->input->get('subcategory_id');
        } else {
            $subcategory_id = null;
        }
        if ($this->input->get('warehouse_id')) {
            $warehouse_id = $this->input->get('warehouse_id');
        } else {
            $warehouse_id = $warehouse_id;
        }
        if ($this->input->get('per_page') == 'n' || $this->input->get('per_page') == '0') {
            $page = 0;
        } else {
            $page = $this->input->get('per_page');
        }
        if ($this->input->get('order_type')) {
            $order_type = $this->input->get('order_type');
        }
        $this->load->library("pagination");
        $config = array();
        $config["base_url"] = base_url() . "pos/ajaxrecipe";
        if ($this->pos_settings->sales_item_in_pos == 1) {
            $config["total_rows"] = $this->pos_model->recipe_count($category_id, $warehouse_id, $subcategory_id, $brand_id);
        } else {
            $config["total_rows"] = $this->pos_model->recipe_count_withdays($category_id, $warehouse_id, $subcategory_id, $brand_id, $order_type);
        }
        $config["per_page"] =15;
        $config['prev_link'] = false;
        $config['next_link'] = false;
        $config['display_pages'] = false;
        $config['first_link'] = false;
        $config['last_link'] = false;
        $this->pagination->initialize($config);
        if ($this->pos_settings->sales_item_in_pos == 1) {
            $recipe = $this->pos_model->fetch_recipe($category_id, $warehouse_id, $config["per_page"], $page, $subcategory_id, $brand_id);
        } else {
            $recipe = $this->pos_model->fetch_recipe_withdays($category_id, $warehouse_id, $config["per_page"], $page, $subcategory_id, $brand_id, $order_type);
        }
        $pro = 1;
        $prods='';
        if (!empty($recipe)) {
			 $prods = '<div>';
            foreach ($recipe as $recipe) {
                $count = $recipe->id;
                $buy = $this->site->checkBuyget($recipe->id);
                $default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
                $default_currency_rate = $default_currency_data->rate;
                $default_currency_symbol = $default_currency_data->symbol;
                if (!empty($buy)) {
                    if ($buy->buy_method == 'buy_x_get_x') {
                        $buyvalue = "<div class='rippon'><div class='offer_list'><span>Buy " . $buy->buy_quantity . "  Get " . $buy->get_quantity . "  </span></div></div>";
                    } elseif ($buy->buy_method == 'buy_x_get_y') {
                        $buyvalue = "<div class='rippon'><div class='offer_list'><span>Buy " . $buy->buy_quantity . "  Get ( " . $buy->get_quantity . ")</span></div></div>";
                    }

                } else {
                    $buyvalue = '';
                }

                if ($count < 10) {
                    $count = "0" . ($count / 100) * 100;
                }
                if ($category_id < 10) {
                    $category_id = "0" . ($category_id / 100) * 100;
                }

                if ($this->Settings->user_language == 'khmer') {

                    if (!empty($recipe->khmer_name)) {

                        $recipe_name = $recipe->khmer_name;
                    } else {
                        $recipe_name = $recipe->name;
                    }
                } else {
                    $recipe_name = $recipe->name;
                }

                $class = '';
                $vari = '';
                $varients = false;

                $varients = $this->pos_model->isVarientExist($recipe->id);

                if (!empty($varients)) {
                    $class = "has-varients";

                    if ($this->pos_settings->variant_display_option == 0) 
					{
                        $vari = '<div class="variant-popup" style="display: none;">';
                        foreach ($varients as $k => $varient) {
                            if ($this->Settings->user_language == 'khmer') {
                                if (!empty($varient->native_name)) {
                                    $varient_name = $varient->native_name;
                                } else {
                                    $varient_name = $varient->name;
                                }
                            } else {
                                $varient_name = $varient->name;
                            }
							 
                            $vari .= '<button data-id="' . $varient->variant_id . '" id="recipe-' . $category_id . $count . '" type="button" value="' . $recipe->code . '" title="" class="btn btn_item  recipe-varient pos-tip" data-container="body" data-original-title="' . $varient_name . '" tabindex="-1">';
                            if (strlen($varient_name) < 15) {
                                $vari .= "<span class='name_strong'>" . $varient_name . "</span>";
                            } else {
                               // $vari .= '<marquee class="name_strong" behavior="alternate" direction="left" scrollamount="1">
									//&nbsp;&nbsp;' . $varient_name . '&nbsp;&nbsp;</marquee>';
									$vari .= "<span class='name_strong'>" .wordwrap($varient_name,15,"\n") . "</span>";
									
                            }
                            $vari .= '
								<span class="price_strong"> ' . $default_currency_symbol . $this->sma->formatDecimal($varient->price) . '</span> </button>';
                        }
                        $vari .= '</div>';
                    } else { //varaint list by Table for Kimmo client requriment

                        $vari = '<div class="variant-popup" style="display: none;">';
						$vari .= '<h2 style="margin-top:10px;"><tr><td>' . lang('VARIANTS') . '</td><td> (' .$recipe_name.' ['.$recipe->code.']' . ')</td></tr></h2>';

						
                        $vari .= '<table class="table table-bordered table-hover table-striped reports-table dataTable n1" >';
						// $vari .= ' <input $varient_name = $varient->name; >';
                        $vari .= '<thead>';
						 // $vari .= '<span class="name_strong">' . $varient_name = $varient->name. '</span>';
						  
						//$vari .= '<td colspan="2"> . $varient->variant_code</td>';
						$vari .= '<h1><tr><th align="center">' . lang('code') . '</th><th align="center">' . lang('Variants name') . '</th></th><th align="center">' . lang('Price') . '</th></tr></h1>';
						
						//$vari .= '<td colspan="1"><button type="button" class="btn btn-primary pull-right AddonItem" id="AddonItem">Submit</button></td></tfoot>';
						$vari .= '</thead>';
                        foreach ($varients as $k => $varient) {
                            if ($this->Settings->user_language == 'khmer') {
                                if (!empty($varient->native_name)) {
                                    $varient_name = $varient->native_name;
                                } else {
                                    $varient_name = $varient->name;
                                }
                            } else {
                                $varient_name = $varient->name;
                            }
							$variant_offer=($varient->variant_id== $buy->buy_variant_id)?$buyvalue:'';
							$vari .= '<tbody id="myTable">';
							//$vari .= '<div id="myTable">';
                            $vari .= '<tr data-id="' . $varient->variant_id . '" id="recipe-' . $category_id . $count . '" code="' . $recipe->code . '" title="" class=" recipe-varient pos-tip recipe-11155" data-container="body" data-original-title="' . $varient_name . '" tabindex="-1">';
							
                            $vari .= "<td >".$variant_offer."<span class='name_strong'>" . $varient->variant_code . "</span></td>";
							
                            $vari .= '<td><span class="price_strong"> ' . $varient_name . '</span></td>';
							 $vari .= '<td><span class="price_strong"> ' . $this->sma->formatMoney($varient->variant_price) . '</span></td>';
                            $vari .= '</tr >';
							$vari .= '</tbody>';
							
							//$vari .= '</div>';
							
                            /*$vari .= '<button data-id="'.$varient->variant_id.'" id="recipe-'.$category_id . $count.'" type="button" value="'.$recipe->code .'" title="" class="btn-default  recipe-varient pos-tip" data-container="body" data-original-title="'.$varient_name.'" tabindex="-1">';
                        if(strlen($varient_name) < 15){
                        $vari .= "<span class='name_strong'>" .$varient_name. "</span>";
                        }else{
                        $vari .='<marquee class="name_strong" behavior="alternate" direction="left" scrollamount="1">
                        &nbsp;&nbsp;'.$varient_name.'&nbsp;&nbsp;</marquee>';
                        }
                        $vari .='<br>
                        <span class="price_strong"> '.$default_currency_symbol.$this->sma->formatDecimal($varient->price).'</span> </button>';*/
                        }
						$vari .= '<td colspan="3"><button type="button" class="btn btn-primary pull-right vritem" id="vritem">Submit</button><span class="payment_status pull-left label label-danger iclose" style="padding:10px 12px;font-size:14px;margin-right:5px;border-radius:0px;font-weight:normal;cursor:pointer;">void</span> </td>';
						/* <span class="payment_status pull-left label label-danger iclose" style="padding:10px 12px;font-size:14px;margin-right:5px;border-radius:0px;font-weight:normal;cursor:pointer;" id="" + row_no + "" title="Remove" style="cursor:pointer;">void</span> */
                        $vari .= '</table>';
                        $vari .= '</div>';
                    }
                }
				$activemode=($recipe->active ==2)?'style="background-color:#E1A87C !important"':'';
				$activemode_class=($recipe->active ==2)?'non_transaction':'';
                if ($this->pos_settings->sale_item_display == 0) {

                    $prods .= "<span><button ".$activemode." id=\"recipe-" . $category_id . $count . "\" type=\"button\" value='" . $recipe->id . "' title=\"" . $recipe->name . "\" class=\"btn-prni btn_item btn-" . $this->pos_settings->recipe_button_color . " " . $class . " recipe pos-tip\" data-container=\"body\"><img src=\"" . base_url() . "assets/uploads/thumbs/" . $recipe->image . "\" alt=\"" . $recipe_name . "\" class='img-rounded  ".$activemode_class."' />";
                } else {
                    $prods .= "<span><button ".$activemode." id=\"recipe-" . $category_id . $count . "\" type=\"button\" value='" . $recipe->id . "' title=\"" . $recipe->name . "\" class=\"btn-img btn_item btn-" . $this->pos_settings->recipe_button_color . " " . $class . " recipe pos-tip ".$activemode_class."\" data-container=\"body\">";
                }

                if (strlen($recipe->name) < 15) {

                    $prods .= "<span class='name_strong'>" . $recipe_name . "</span>";
                } else {
                   
					$prods .= "<span class='name_strong'>" . wordwrap($recipe_name,15,"\n"). "</span>";
                }
    
                $prods .= "<span class='price_strong'> ";
                if ($recipe->price != 0) {
                    $prods .= $default_currency_symbol . "" . $this->sma->formatDecimal($recipe->price);
                }

                $prods .= "</span>" . $buyvalue . "";

                $prods .= "</button>";
                $prods .= $vari . '</span>';

                $pro++;
            }
			  $prods .= "</div>";
        }
      

        // if ($this->input->get('per_page')) {
        if ($this->input->get('per_page') != null) {
            echo $prods;
        } else {
            return $prods ;
        }
    }
	public function ajaxcategorydata($category_id = null)
    {
        $this->sma->checkPermissions('index');
        $recipe_standard = $this->input->get('recipe_standard');
        if ($this->input->get('category_id')) {
            $category_id = $this->input->get('category_id');
        } else {
            $category_id = $this->pos_settings->default_category;
        }
        if ($this->input->get('split')) {
            $split_id = $this->input->get('split');
            $sales_type = $this->pos_model->getBBQLobsterSaletype($split_id);
            if (!empty($sales_type)) {
                $sales_type = $sales_type;
            }
        }
        $order_type = $this->input->get('order_type');
        if ($this->pos_settings->sales_item_in_pos == 1) {
            $subcategories = $this->site->getrecipeSubCategories($category_id);
        } else { // sub category list from mapping table with active items in recipe table
            $subcategories = $this->site->getrecipeSubCategories_withdays($category_id, $sales_type);
        }

        // $subcategories = $this->site->getrecipeSubCategories_withdays($category_id,$order_type);
        $scats = '';
        if ($subcategories) {
            foreach ($subcategories as $category) {

                if ($this->Settings->user_language == 'khmer') {

                    if (!empty($category->khmer_name)) {

                        $subcategory_name = $category->khmer_name;
                    } else {
                        $subcategory_name = $category->name;
                    }
                } else {
                    $subcategory_name = $category->name;
                }

                if ($this->pos_settings->subcategory_display == 0) {
                    $scats .= "<button id=\"subcategory-" . $category->id . "\" type=\"button\" value='" . $category->id . "' class=\"btn-prni btn_lightred subcategory slide\" >";
					//<img src=\"assets/uploads/thumbs/" . ($category->image ? $category->image : 'no_image.png') . "\" class='img-rounded' />";
                } else {
                    $scats .= "<button id=\"subcategory-" . $category->id . "\" type=\"button\" value='" . $category->id . "' class=\"btn-img subcategory slide\" >";
                }

                // $scats .= "<button id=\"subcategory-" . $category->id . "\" type=\"button\" value='" . $category->id . "' class=\"btn-prni subcategory\" ><img src=\"" . base_url() ."assets/uploads/thumbs/" . ($category->image ? $category->image : 'no_image.png') . "\" class='img-rounded img-thumbnail' />";

               if (strlen($subcategory_name) < 15) {

                    $scats .= "<span class='name_strong'>" . $subcategory_name . "</span>";
                } else {
                  //  $scats .= "<marquee class='sub_strong' behavior='alternate' direction='left' scrollamount='1'>&nbsp;&nbsp;&nbsp;&nbsp;" . $subcategory_name . "&nbsp;&nbsp;&nbsp;&nbsp;</marquee>";
				   $scats .= "<span class='name_strong'>" .wordwrap($subcategory_name,15,"\n")  . "</span>";
                } 
				// $scats .= "<span class='name_strong'>" . $subcategory_name . "</span>";
                $scats .= "</button>";

            }
        }
        if ($recipe_standard == 1) {

            $recipe = $this->ajaxrecipe_consolidate($category_id, $this->session->userdata('warehouse_id'), $order_type);
            if (!($tcp = $this->pos_model->recipe_count($category_id, $this->session->userdata('warehouse_id')))) {
                $tcp = 0;
            }
        } else {
            $recipe = $this->ajaxrecipebbq_consolidate($category_id, $this->session->userdata('warehouse_id'), $subcategory_id = null, $brand_id = null, $sales_type);
            if (!($tcp = $this->pos_model->bbqrecipe_count($category_id, $this->session->userdata('warehouse_id')))) {
                $tcp = 0;
            }
        }

        $this->sma->send_json(array('recipe' => $recipe, 'subcategories' => $scats, 'tcp' => $tcp));
    }
	public function sent_to_kitchen($sid = null){
       /*  echo "<pre>";
        print_r($_POST);exit;*/
        $this->form_validation->set_rules('customer', $this->lang->line("customer"), 'trim|required');
        $this->form_validation->set_rules('warehouse', $this->lang->line("warehouse"), 'required');
        $this->form_validation->set_rules('biller', $this->lang->line("biller"), 'required');
         $table=$this->input->post('table_list_id');
        if ($this->form_validation->run() == true) {
          /*   echo "<pre>";
            print_r($this->input->post());die;  */
            
            $date = date('Y-m-d H:i:s');
            $warehouse_id = $this->input->post('warehouse');
            $customer_id = $this->input->post('customer');
            $biller_id = $this->input->post('biller');
            $total_items = $this->input->post('total_items');
            $payment_term = 0;
            $due_date = date('Y-m-d', strtotime('+' . $payment_term . ' days'));
            $shipping = $this->input->post('shipping') ? $this->input->post('shipping') : 0;
            $customer_details = $this->site->getCompanyByID($customer_id);
            $customer = !empty($customer_details->name)?$customer_details->name:'nil';
            $biller_details = $this->site->getCompanyByID($biller_id);
            $biller = $biller_details->name;
            $note = $this->sma->clear_tags($this->input->post('pos_note'));
            $staff_note = $this->sma->clear_tags($this->input->post('staff_note'));
            $reference = 'ORDER' . date('YmdHis');
            $split_id = $this->input->post('split_id') ? $this->input->post('split_id') : $this->site->CreateSplitID($this->session->userdata('user_id'));
            $total = 0;
            $recipe_tax = 0;
            $recipe_discount = 0;
            $digital = false;
            $gst_data = [];
            $total_cgst = $total_sgst = $total_igst = 0;
            $i = isset($_POST['recipe_code']) ? sizeof($_POST['recipe_code']) : 0;
            for ($r = 0; $r < $i; $r++) {
                if ($_POST['recipe_type'][$r] == 'manual') {
                    $manual_recipe = $this->site->create_or_get_manual_recipe_details($_POST['recipe_name'][$r], $_POST['unit_price'][$r]);
                    $item_id = $manual_recipe;
                    $kitchen_type_id = $this->site->getAllDefalutKitchen();
                } else {
                    $item_id = $_POST['recipe_id'][$r];
                    $kitchen_type_id = $_POST['kitchen_type_id'][$r];
                }
                $item_type = $_POST['recipe_type'][$r];
                $item_code = $_POST['recipe_code'][$r];
                $item_name = $_POST['recipe_name'][$r];
                $buy_id = $_POST['buy_id'][$r];
                $buy_quantity = $_POST['buy_quantity'][$r];
                $kitchen_type_id = $_POST['kitchen_type_id'][$r];
                $get_item = $_POST['get_item'][$r];
                $get_quantity = $_POST['get_quantity'][$r];
                $total_get_quantity = $_POST['total_get_quantity'][$r];
                $item_comment = $_POST['recipe_comment'][$r];
                //$item_addon = isset($_POST['recipe_addon'][$r]) && $_POST['recipe_addon'][$r] != 'false' ? $_POST['recipe_addon'][$r] : NULL;
                $item_addon = (!is_object($_POST['recipe_addon'][$r])) ? $_POST['recipe_addon'][$r] : null;
                $item_addon_qty = (!is_object($_POST['recipe_addon_qty'][$r])) ? $_POST['recipe_addon_qty'][$r] : null;
                $item_option = isset($_POST['recipe_option'][$r]) && $_POST['recipe_option'][$r] != 'false' ? $_POST['recipe_option'][$r] : null;
                $real_unit_price = $this->sma->formatDecimal($_POST['real_unit_price'][$r]);
                $net_price = $this->sma->formatDecimal($_POST['net_price'][$r]);
                $unit_price = $this->sma->formatDecimal($_POST['unit_price'][$r]);
                $item_unit_quantity = $_POST['quantity'][$r];
                $item_serial = isset($_POST['serial'][$r]) ? $_POST['serial'][$r] : '';
                $item_tax_rate = isset($_POST['recipe_tax'][$r]) ? $_POST['recipe_tax'][$r] : null;
                $item_discount = isset($_POST['recipe_discount'][$r]) ? $_POST['recipe_discount'][$r] : null;
                $item_unit = $_POST['recipe_unit'][$r];
                $item_quantity = $_POST['recipe_base_quantity'][$r];

                if (isset($item_code) && isset($real_unit_price) && isset($unit_price) && isset($item_quantity)) {
                    $recipe_details = $item_type != 'manual' ? $this->pos_model->getrecipeByCode($item_code) : null;
                    // $unit_price = $real_unit_price;
                    if ($item_type == 'digital') {
                        $digital = true;
                    }
                    $pr_discount = $this->site->calculateDiscount($item_discount, $real_unit_price);
                    $unit_price = $this->sma->formatDecimal($real_unit_price - $pr_discount);
                    $item_net_price = $unit_price;
                    $pr_item_discount = $this->sma->formatDecimal($pr_discount * $item_unit_quantity);
                    $recipe_discount += $pr_item_discount;
                    $pr_item_tax = $item_tax = 0;
                    $tax = "";
                    if (isset($item_tax_rate) && $item_tax_rate != 0) {
                        $tax_details = $this->site->getTaxRateByID($item_tax_rate);
                        $ctax = $this->site->calculateTax($recipe_details, $tax_details, $unit_price);
                        $item_tax = $ctax['amount'];
                        $tax = $ctax['tax'];
                        if (!$recipe_details || (!empty($recipe_details) && $recipe_details->tax_method != 1)) {
                            $item_net_price = $unit_price - $item_tax;
                        }
                        $pr_item_tax = $this->sma->formatDecimal(($item_tax * $item_unit_quantity), 4);
                        if ($this->Settings->indian_gst && $gst_data = $this->gst->calculteIndianGST($pr_item_tax, ($biller_details->state == $customer_details->state), $tax_details)) {
                            $total_cgst += $gst_data['cgst'];
                            $total_sgst += $gst_data['sgst'];
                            $total_igst += $gst_data['igst'];
                        }
                    }
                    $recipe_tax += $pr_item_tax;
                    $subtotal = (($item_net_price * $item_unit_quantity) + $pr_item_tax);
                    $unit = $this->site->getUnitByID($item_unit);
                    $variant = explode("|", $_POST['variant'][$r]);
                    $recipe_item = array(
                        'recipe_id' => $item_id,
                        'item_status' => 'Inprocess',
                        'kitchen_type_id' => $kitchen_type_id ? $kitchen_type_id : 0,
                        'recipe_code' => $item_code,
                        'recipe_name' => $item_name,
                        'recipe_name_img' => $_POST['recipe_name_img'][$r] ? $_POST['recipe_name_img'][$r] : '',
                        'addon_name_img' => $_POST['addon_name_img'][$r] ? $_POST['addon_name_img'][$r] : '',
                        'buy_id' => $buy_id ? $buy_id : 0,
                        'buy_quantity' => $buy_quantity ? $buy_quantity : 0,
                        'get_item' => $get_item ? $get_item : 0,
                        'get_quantity' => $get_quantity ? $get_quantity : 0,
                        'total_get_quantity' => $total_get_quantity ? $total_get_quantity : 0,
                        'recipe_type' => $item_type,
                        'option_id' => $item_option,
                        'addon_id' => $item_addon,
                        'addon_qty' => $item_addon_qty ? $item_addon_qty : 0,
                        'net_unit_price' => $item_net_price,
                        'unit_price' => $this->sma->formatDecimal($item_net_price + $item_tax),
                        'quantity' => $item_quantity,
                        'recipe_unit_id' => $unit ? $unit->id : null,
                        'recipe_unit_code' => $unit ? $unit->code : null,
                        'unit_quantity' => $item_unit_quantity,
                        'warehouse_id' => $warehouse_id,
                        'item_tax' => $pr_item_tax,
                        'tax_rate_id' => $item_tax_rate,
                        'tax' => $tax,
                        'discount' => $item_discount,
                        'item_discount' => $pr_item_discount,
                        'subtotal' => $this->sma->formatDecimal($net_price),
                        'serial_no' => $item_serial,
                        'real_unit_price' => $real_unit_price,
                        'comment' => $item_comment,
                        'time_started' => date('Y-m-d H:i:s'),
                        'created_on' => date('Y-m-d H:i:s'),
                        'variant' => $variant[1] ? $variant[1] : '',
                        'recipe_variant_id' => $variant[0] ? $variant[0] : 0,
                        'manual_item_discount' => $_POST['manual_item_discount'][$r] ? $_POST['manual_item_discount'][$r] : 0,
                        'manual_item_discount_val' => $_POST['manual_item_discount_val'][$r] ? $_POST['manual_item_discount_val'][$r] : 0,
                        'unwanted_ingredients' => $_POST['unwanted_ingredients'][$r] ? $_POST['unwanted_ingredients'][$r] : 0,
                    );
                    $recipe[] = ($recipe_item + $gst_data);
					//buy x and get x & buy x get  block start//
				
					if(!empty($get_quantity)&& !empty($get_item)){
						$item_details=$this->site->getRecipeDetails($get_item);
						  $recipe_item = array(
                        'recipe_id' => $item_details->id,
                        'item_status' => 'Inprocess',
                        'kitchen_type_id' => $kitchen_type_id ? $kitchen_type_id : 0,
                        'recipe_code' => $item_details->code,
                        'recipe_name' => $item_details->name,
                        'recipe_name_img' => $_POST['recipe_name_img'][$r] ? $_POST['recipe_name_img'][$r] : '',
                        'addon_name_img' => $_POST['addon_name_img'][$r] ? $_POST['addon_name_img'][$r] : '',
                        'buy_id' => 0,
                        'buy_quantity' =>0,
                        'get_item' =>  0,
                        'get_quantity' =>  0,
                        'total_get_quantity' => $get_quantity ? $get_quantity : 0,
                        'recipe_type' => $item_details->recipe_details,
                        'option_id' => 0,
                        'addon_id' => 0,
                        'addon_qty' =>  0,
                        'net_unit_price' => 0,
                        'unit_price' => $this->sma->formatDecimal(0),
                        'quantity' => $get_quantity,
                        'recipe_unit_id' =>  null,
                        'recipe_unit_code' =>  null,
                        'unit_quantity' => $get_quantity,
                        'warehouse_id' => $warehouse_id,
                        'item_tax' => 0,
                        'tax_rate_id' => 0,
                        'tax' =>0,
                        'discount' =>0,
                        'item_discount' => 0,
                        'subtotal' => $this->sma->formatDecimal(0),
                        'serial_no' => $item_serial,
                        'real_unit_price' => 0,
                        'comment' => '',
                        'time_started' => date('Y-m-d H:i:s'),
                        'created_on' => date('Y-m-d H:i:s'),
                        'variant' => $_POST['get_item_variant_name'][$r] ? $_POST['get_item_variant_name'][$r] : '',
                        'recipe_variant_id' => $_POST['get_item_variant_id'][$r] ? $_POST['get_item_variant_id'][$r] : '',
                        'manual_item_discount' =>0,
                        'manual_item_discount_val' =>  0,
                        'unwanted_ingredients' =>  0,
						'parent_order_item_id'=>$item_id
                    );
					  $recipe[] =$recipe_item ;
					}
					//buy x and get x & buy x get  block end//
                    $total += $this->sma->formatDecimal(($item_net_price * $item_unit_quantity), 4);
                }
            }
            if (empty($recipe)) {
                $this->form_validation->set_rules('recipe', lang("order_items"), 'required');
            } elseif ($this->pos_settings->item_order == 1) {
                krsort($recipe);
            }

            $order_discount = $this->site->calculateDiscount($this->input->post('discount'), ($total + $recipe_tax));
            $total_discount = $this->sma->formatDecimal(($order_discount + $recipe_discount), 4);
            $order_tax = $this->site->calculateOrderTax($this->input->post('order_tax'), ($total + $recipe_tax - $total_discount));
            $total_tax = $this->sma->formatDecimal(($recipe_tax + $order_tax), 4);
            $grand_total = $this->sma->formatDecimal(($total + $total_tax + $this->sma->formatDecimal($shipping) - $order_discount), 4);
            $rounding = 0;
            if ($this->pos_settings->rounding) {
                $round_total = $this->sma->roundNumber($grand_total, $this->pos_settings->rounding);
                $rounding = $this->sma->formatMoney($round_total - $grand_total);
            }
            $data = array('date' => $this->site->getTransactionDate(),
                'created_on' => $date,
                'reference_no' => $reference,
                'table_id' => !empty($this->input->post('table_list_id')) ? $this->input->post('table_list_id') : 0,
                'seats_id' => !empty($this->input->post('no_peoples')) ? $this->input->post('no_peoples') : 0,
                'split_id' => $split_id,
                'order_type' => $this->input->post('order_type_id'),
                'order_status' => 'Open',
                'customer_id' => $customer_id,
                'customer' => $customer,
                'biller_id' => $biller_id,
                'biller' => $biller,
                'note' => $note,
                'staff_note' => $staff_note,
                'total' => $total,
                'recipe_discount' => $recipe_discount,
                'order_discount_id' => $this->input->post('discount'),
                'order_discount' => $order_discount,
                'total_discount' => $total_discount,
                'recipe_tax' => $recipe_tax,
                'order_tax_id' => $this->input->post('order_tax'),
                'order_tax' => $order_tax,
                'total_tax' => $total_tax,
                'shipping' => $this->sma->formatDecimal($shipping),
                'grand_total' => $grand_total,
                'total_items' => $total_items,
                /*'sale_status'       => $sale_status,
                'payment_status'    => $payment_status,*/
                'payment_term' => $payment_term,
                'rounding' => $rounding,
                'suspend_note' => $this->input->post('suspend_note'),
                'pos' => 1,
                'paid' => $this->input->post('amount-paid') ? $this->input->post('amount-paid') : 0,
                'created_by' => $this->session->userdata('user_id'),
                'ordered_by' => 'steward',
                'order_from' => 'web',
                'hash' => hash('sha256', microtime() . mt_rand()),
                'waiter_id' => $this->session->userdata('user_id'),
				'warehouse_id' => $warehouse_id,
				//'warehouse_id' => $this->isWarehouse,
				'store_id' => $this->store_id,
				'till_id' => $this->till_id,
				'shift_id' =>!empty($this->ShiftID)?$this->ShiftID:0,
            );
            if ($this->Settings->indian_gst) {
                $data['cgst'] = $total_cgst;
                $data['sgst'] = $total_sgst;
                $data['igst'] = $total_igst;
            }
            if ($data['table_id'] != 0) {
                $data['table_whitelisted'] = $this->pos_model->isTableWhitelisted($data['table_id']);
            }
            $kitchen = array(
                'waiter_id' => $this->session->userdata('user_id'),
                'status' => 'Inprocess',
            );
               $role='';
            if ($this->session->userdata('group_id') == 5) {
                $role = ' (Sale) ';
            } elseif ($this->session->userdata('group_id') == 7) {
                $role = ' (Waiter) ';
            }
            if ($this->input->post('order_type_id') == 1) {
                $notification_message = $this->session->userdata('username') . $role . '  has been create new dine in order. it will be process sent to kitchen';
            } elseif ($this->input->post('order_type_id') == 2) {
                $notification_message = $this->session->userdata('username') . $role . '  has been create new takeaway order. it will be process sent to kitchen';
            } elseif ($this->input->post('order_type_id') == 3) {
                $notification_message = $this->session->userdata('username') . $role . ' has been create new door delivery order. it will be process sent to kitchen';
            } elseif ($this->input->post('order_type_id') == 4) {
                $notification_message = $this->session->userdata('username') . $role . ' has been create new BBQ order. it will be process sent to kitchen';
            }

            $notification_array['from_role'] = $this->session->userdata('group_id');
            $notification_array['insert_array'] = array(
                'msg' => $notification_message,
                'type' => 'Send to kitchen',
                'table_id' => !empty($this->input->post('table_list_id')) ? $this->input->post('table_list_id') : 0,
                'user_id' => $this->session->userdata('user_id'),
                'role_id' => KITCHEN,
                'warehouse_id' => $warehouse_id,
                'created_on' => date('Y-m-d H:m:s'),
                'is_read' => 0,
                'respective_steward' => 0,
                'split_id' => $split_id,
                'tag' => 'send-to-kitchen',
                'status' => 1,
            );
            // $this->sma->print_arrays($data, $recipe, $kitchen);

        }

        if (in_array(1, $this->input->post('special_item'))) {
            $spl_res = $this->applySpecialItem($data, $recipe, $kitchen, $this->session->userdata('warehouse_id'), $this->session->userdata('user_id'));
            if ($spl_res == 1) {

                redirect("pos/pos?msg=special_item");
            } else {
                redirect("pos/pos");
            }
        }
        if ($this->form_validation->run() == true && !empty($recipe) && !empty($data) && !empty($kitchen)) {
            if ($sale = $this->pos_model->addKitchen_all($data, $recipe, $kitchen, $notification_array, $this->session->userdata('warehouse_id'), $this->session->userdata('user_id'))) {
                $kot_print_data['kot_print_option'] = $this->pos_settings->kot_print_option;
                $kot_print_data['con_kot_print_option'] = $this->pos_settings->consolidated_kot_print;
                $kot_print_data['kot_area_print'] = $sale['kitchen_data'];
                if ($this->pos_settings->consolidated_kot_print != 0) {
                    $kot_print_data['kot_con_print'] = $sale['consolid_kitchen_data'];
                    $kot_print_data['consolidate_kitchens_kot'] = $sale['consolidate_kitchens_kot'];
                }
                $kot_print_lang_option = $this->pos_settings->kot_print_lang_option;
                if ($this->pos_settings->kot_enable_disable == 1) {
                    $this->send_to_kot_print($kot_print_data);
                }
                $this->session->set_userdata('remove_posls', 1);
				$msg='';
                $this->session->set_flashdata('message', $msg);
                $tableid = $this->input->post('table_list_id');
                if ($_POST['order_type_id'] == 1 && substr($_POST['split_id'], 0, 3) !== "BBQ") {
                    redirect("pos/pos/");
                } else if ($_POST['order_type_id'] == 2) {
                   redirect("pos/pos/home/");
                } elseif($_POST['order_type_id'] == 3) {
                   redirect("pos/pos/home/");
                }

            }
        } else {
            redirect("pos/pos/");
        }

    }
	
    public function kot_consolidated_curl($kotconsoildprint){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, site_url('kot_print/kot_consolidated'));
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        $kitchendata = json_encode($kotconsoildprint);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $kitchendata);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        $result = curl_exec($ch);
        curl_close($ch);
    }

    public function kot_print_copy($split_id, $kitchen_id = false){
        $sale = $this->pos_model->kot_print_copy($split_id, $kitchen_id);
        $kot_print_data['kot_print_option'] = $this->pos_settings->kot_print_option;
        $kot_print_data['con_kot_print_option'] = $this->pos_settings->consolidated_kot_print;
        $kot_print_data['kot_area_print'] = $sale['kitchen_data'];
        $kot_print_data['kot_con_print'] = $sale['consolid_kitchen_data'];
          /*   echo "<pre>";
        print_r($kot_print_data);die;    */
        if ($this->pos_settings->kot_enable_disable == 1) {
            $this->send_to_kot_print($kot_print_data);
        }
    }
    public function kitchen_kot_print_copy($order_id, $kitchen_id){
        $orderItemIDs = $this->input->post('order_item_ids');
        $sale = $this->pos_model->kitchen_kot_print_copy($order_id, $orderItemIDs, $kitchen_id);
        $kot_print_data['kot_print_option'] = $this->pos_settings->kot_print_option;
        $kot_print_data['con_kot_print_option'] = 0;
        $kot_print_data['kot_area_print'] = $sale['kitchen_data'];
        $this->send_to_kot_print($kot_print_data);

    }
    public function send_to_kot_print($kot_print_data){
		
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, site_url('kot_print/send_to_kot_print'));
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        $kot_print_data = json_encode($kot_print_data);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $kot_print_data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        $result = curl_exec($ch);
        curl_close($ch);
    }
	
	 public function cancelItem_send_to_kot_print($kot_print_data){
		
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, site_url('kot_print/send_to_kot_print_cancelItem'));
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        $kot_print_data = json_encode($kot_print_data);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $kot_print_data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        $result = curl_exec($ch);
        curl_close($ch);
    }
	 public function get_splits_for_merge($current_split = null){
        $current_split = $this->input->post('current_split');
		$table_id = $this->input->post('table_id');
        $data = $this->site->getsplitsformerge($current_split);
        if ($data) {
            $msg = 'success';
        } else {
            $msg = 'error';
        }
        $this->sma->send_json(array('msg' => $msg, 'data' => $data));
    }
	 public function multiple_splits_mergeto_singlesplit_for_consolidate(){
        $merge_splits = $this->input->post('merge_splits');
        $current_split = $this->input->post('current_split');
        $merge_table_id = $this->input->post('merge_table_id');
		$this->data['bils'] = 1;
        $result = $this->pos_model->merger_multiple_to_single_split_consolidate($merge_splits, $current_split, $merge_table_id);
        if ($result == true) {
            $msg = 'success';
        } else {
            $msg = 'error';
            $status = 'error';
        }
        $this->sma->send_json(array('msg' => $msg, 'status' => $msg));
    }
	function payment(){
		  $this->data['order_type']=$order_type= !empty($_GET['type']) ? $_GET['type'] : '';
		  $this->data['table_id']=$table_id= !empty($_GET['table']) ? $_GET['table'] : '';
		  $this->data['split_id']=$split_id= !empty($_GET['split_id']) ? $_GET['split_id'] : '';
		   $requestType=!empty($_GET['req']) ? $_GET['req'] : '';
		  if(!empty($requestType)){
			  switch ($requestType){
				case "Invoice"  :
				 $order_details=$this->pos_model->get_table_order_count($table_id,$split_id);
		         if(count($order_details)>1 && empty($split_id)){
			     redirect("pos/pos/split_list/".$table_id);
		         }else{
					  $this->data['split_id'] =$split_id= $order_details->split_id;
				 }
				break;
				  case "Payment":
				  $avl_bill=$this->site->avl_bill($table_id);
				  if(count($avl_bill)>1 ){
			            redirect("pos/pos/invoice_list/".$table_id);
		         }else{
					$this->data['split_id'] =$split_id= $avl_bill->sales_split_id; 
				 }
				  break;
			  }
		  }
		$this->data['split_order']=$order_details;
		$this->data['warehouse'] = $this->session->userdata('warehouse_id') ? $this->site->getWarehouseByID($this->session->userdata('warehouse_id')) : null;
        $waiter_id = $this->session->userdata('user_id');
		$this->data['bils'] = 1;
		$this->data['bill_type'] = 1;
        $this->data['table_id']          = $table_id;
        $this->data['tax_rates']         = $this->site->getAllTaxRates();
        $this->data['service_charge']    = $this->site->getAllSericeCharges();
        $this->data['customer_discount'] = $this->site->GetAllcostomerDiscounts();
        $this->data['current_user']      = $this->pos_model->getUserByID($this->session->userdata('user_id'));
		$this->data["tcp"]               = $this->pos_model->recipe_count($this->pos_settings->default_category, $this->session->userdata('warehouse_id'));
		$this->data['sales']             =$sales= $this->pos_model->getAllSalesWithbiller_based_split($this->data['order_type'], $table_id,$split_id);
		 if(empty($sales)){
        if (!empty($table_id)) {
            $item_data = $this->pos_model->getBill_all($table_id, $split_id, $this->session->userdata('user_id'),1);
        } else {
            $item_data = $this->pos_model->getBill_all($table_id, $split_id, $this->session->userdata('user_id'),1);
        }
        foreach ($item_data['items'] as $item_row) {
            foreach ($item_row as $item) {
                $order_item_id[] = $item->id;
            }
        }
        foreach ($item_data['items'] as $item_row) {
            foreach ($item_row as $item) {
                $order_item[] = $item;
            }
        }
        foreach ($item_data['items'] as $orderitems) {
            foreach ($orderitems as $items) {
                $timelog_array[] = array(
                    'status' => 'Closed',
                    'created_on' => date('Y-m-d H:m:s'),
                    'item_id' => $items->id,
                    'user_id' => $this->session->userdata('user_id'),
                    'warehouse_id' => $this->session->userdata('warehouse_id'));
            }
        }
        $this->data['order_item'] = $order_item;
     /*   echo "<pre>";
        print_r($order_item);
		die; */
        foreach ($item_data['order'] as $order) {
            $order_data = array('sales_type_id' => $order->order_type,
                'sales_split_id' => $order->split_id,
                'sales_table_id' => $order->table_id,
                'date' => $this->site->getTransactionDate(),
                'created_on' => date('Y-m-d H:i:s'),
                'reference_no' => 'SALES-' . date('YmdHis'),
                'customer_id' => $order->customer_id,
                'customer' => $order->customer,
                'biller_id' => $order->biller_id,
                'biller' => $order->biller,
                'warehouse_id' => $order->warehouse_id,
                'note' => $order->note,
                'staff_note' => $order->staff_note,
                'sale_status' => 'Process',
                'hash' => hash('sha256', microtime() . mt_rand()),
            );
            $customer_id = $order->customer_id;
            $notification_array['customer_id'] = $order->customer_id;
        }
		 $this->data['member_discount']   = $this->pos_model->getMember_discount( $order->customer_id);
		 }
	
        $this->data['order_data'] = $order_data;
        $postData = $this->input->post();
        $delivery_person = $this->input->post('delivery_person_id') ? $this->input->post('delivery_person_id') : 0;
        $split_status = $this->site->check_splitid_is_bill_generated($split_id);
		$this->data['bills_class']=empty($sales)?"disabled":"";
		$this->data['order_class']=!empty($sales)?"disabled":"";
		$this->data['order_type_disabled']=($order_type!=1)?"disabled":"";
		$this->data['avil_tables'] = $this->site->getAvilAbleTables_dineIn($table_id);
	    $this->load->view($this->theme . 'pos_v2/invoice', $this->data);
	}
	public function change_table_number_all($cancel_remarks = null, $sale_id = null){
        $change_split_id = $this->input->post('change_split_id');
        $changed_table_id = $this->input->post('changed_table_id');
        $result = $this->pos_model->change_table_consolidate($change_split_id, $changed_table_id);
        if ($result == true) {
            $msg = 'success';
        } else {
            $msg = 'error';
            $status = 'error';
        }
        $this->sma->send_json(array('msg' => $msg, 'status' => $msg));
    }
	public function cancel_all_order_items($cancel_remarks = null, $split_table_id = null){
        $cancel_remarks = $this->input->get('cancel_remarks');
        $split_table_id = $this->input->get('split_table_id');
        $notification_array['from_role'] = $this->session->userdata('group_id');
        $notification_array['insert_array'] = array(
            'user_id' => $this->session->userdata('user_id'),
            'warehouse_id' => $this->session->userdata('warehouse_id'),
            'created_on' => date('Y-m-d H:m:s'),
            'is_read' => 0,
        );
        $result = $this->pos_model->ALLCancelOrdersItem($cancel_remarks, $split_table_id, $this->session->userdata('user_id'), $notification_array);
        if ($result == true) {
            $msg = 'success';

        } else {
            $msg = 'error';
            $status = 'error';
        }
        $this->sma->send_json(array('msg' => $msg, 'status' => $msg));
    }
	
public function billing(){
        $order_type = !empty($_GET['order_type']) ? $_GET['order_type'] : '';
        $bill_type = !empty($_GET['bill_type']) ? $_GET['bill_type'] : '';
        $table_id = !empty($_GET['table']) ? $_GET['table'] : '';
        $split_id = !empty($_GET['splits']) ? $_GET['splits'] : '';
        $bils = !empty($_GET['bils']) ? $_GET['bils'] : '';
        $this->data['warehouse'] = $this->session->userdata('warehouse_id') ? $this->site->getWarehouseByID($this->session->userdata('warehouse_id')) : null;
        $waiter_id = $this->session->userdata('user_id');
        $this->data['order_type'] = $order_type;
        $this->data['bill_type'] = $bill_type;
        $this->data['bils'] = $bils;
        $this->data['table_id'] = $table_id;
        $this->data['split_id'] = $split_id;
        $this->data['tax_rates'] = $this->site->getAllTaxRates();
        $this->data['service_charge'] = $this->site->getAllSericeCharges();
        $this->data['customer_discount'] = $this->site->GetAllcostomerDiscounts();
		
       /* echo "<pre>";
        print_r($this->data['customer_discount']);die;*/

        $notification_array['customer_role'] = CUSTOMER;
        $notification_array['customer_msg'] = $this->session->userdata('username') . ' has been bil generator to customer';
        $notification_array['customer_type'] = 'Your bil  generator';
        $notification_array['from_role'] = $this->session->userdata('group_id');
        $notification_array['insert_array'] = array(
            'msg' => $this->session->userdata('username') . ' has been bil generator to ' . $split_id,
            'type' => 'Bil generator (' . $split_id . ')',
            'table_id' => $table_id,
            'role_id' => CASHIER,
            'user_id' => $this->session->userdata('user_id'),
            'warehouse_id' => $this->session->userdata('warehouse_id'),
            'created_on' => date('Y-m-d H:m:s'),
            'is_read' => 0,
            'respective_steward' => 0,
            'split_id' => $split_id,
            'tag' => 'bill-generated',
            'status' => 1,
        );
        $this->data['current_user'] = $this->pos_model->getUserByID($this->session->userdata('user_id'));
        if (!empty($table_id)) {
            $item_data = $this->pos_model->getBill_all($table_id, $split_id, $this->session->userdata('user_id'),$bill_type);
        } else {
            $item_data = $this->pos_model->getBill_all($table_id, $split_id, $this->session->userdata('user_id'),$bill_type);
        }
        foreach ($item_data['items'] as $item_row) {
            foreach ($item_row as $item) {
                $order_item_id[] = $item->id;
            }
        }

        foreach ($item_data['items'] as $item_row) {
            foreach ($item_row as $item) {
                $order_item[] = $item;
            }
        }

        foreach ($item_data['items'] as $orderitems) {
            foreach ($orderitems as $items) {
                $timelog_array[] = array(
                    'status' => 'Closed',
                    'created_on' => date('Y-m-d H:m:s'),
                    'item_id' => $items->id,
                    'user_id' => $this->session->userdata('user_id'),
                    'warehouse_id' => $this->session->userdata('warehouse_id'));
            }
        }

        $this->data['order_item'] = $order_item;
        /*echo "<pre>";
        print_r($item_data['order']);die;*/
        foreach ($item_data['order'] as $order) {
            $order_data = array('sales_type_id' => $order->order_type,
                'sales_split_id' => $order->split_id,
                'sales_table_id' => $order->table_id,
                'date' => $this->site->getTransactionDate(),
                'created_on' => date('Y-m-d H:i:s'),
                'reference_no' => 'SALES-' . date('YmdHis'),
                'customer_id' => $order->customer_id,
                'customer' => $order->customer,
                'biller_id' => $order->biller_id,
                'biller' => $order->biller,
                
                'note' => $order->note,
                'staff_note' => $order->staff_note,
                'sale_status' => 'Process',
                'hash' => hash('sha256', microtime() . mt_rand()),
				'warehouse_id' => $order->warehouse_id,
				//'warehouse_id' => $this->isWarehouse,
				'store_id' => $this->store_id,
				'till_id' => $this->till_id,
				'shift_id' =>!empty($this->ShiftID)?$this->ShiftID:0,
            );

            $customer_id = $order->customer_id;
            $notification_array['customer_id'] = $order->customer_id;
        }

        $this->data['order_data'] = $order_data;
        $postData = $this->input->post();
        $delivery_person = $this->input->post('delivery_person_id') ? $this->input->post('delivery_person_id') : 0;
        $split_status = $this->site->check_splitid_is_bill_generated($split_id);
		$order_customer=$this->pos_model->get_order_customer($split_id);
		$this->data['member_discount']   = $this->pos_model->getMember_discount( $order_customer->customer_id);
        if ($split_status) {
            redirect("pos/pos/");
        }

        if ($bill_type == 1) {
            $this->form_validation->set_rules('splits', $this->lang->line("splits"), 'trim|required');
            if ($this->form_validation->run() == true) {
                if ($this->input->post('action') == "SINGLEBILL-SUBMIT") {
                    /*echo "<pre>";
                    print_r($this->input->post());die;*/
                    if ($this->input->post('bill_type')) {
                        for ($i = 1; $i <= $this->input->post('bils'); $i++) {

                            if (!empty($this->input->post('split[' . $i . '][order_discount_input]'))) {
                                $request_discount[$i] = array(
                                    'customer_id' => $customer_id,
                                    'waiter_id' => $this->session->userdata('user_id'),
                                    'table_id' => $table_id,
                                    'split_id' => $split_id,
                                    'customer_type_val' => $this->Settings->customer_discount ? $this->Settings->customer_discount : '',
                                    'customer_discount_val' => $this->input->post('split[' . $i . '][order_discount_input]') ? $this->input->post('split[' . $i . '][order_discount_input]') : '',
                                    'created_on' => date('Y-m-d H:i:s'),
                                );
                            }
                            $billitem['bills_items'] = array();
                            $bills_item['recipe_name'] = $this->input->post('split[][$i][recipe_name][$i]');
                            $splitData = array();
                            foreach ($postData['split'][$i]['recipe_name'] as $key => $split) {
                                $offer_dis = 0.0000;
                                if ($this->input->post('[split][' . $i . '][tot_dis_value]')) {
                                    $offer_dis = $this->site->calculate_Discount($this->input->post('[split][' . $i . '][tot_dis_value]'), ($postData['split'][$i]['subtotal'][$key] - $postData['split'][$i]['manual_item_discount'][$key] - $postData['split'][$i]['item_discount'][$key]), $this->input->post('[split][' . $i . '][item_dis]'));
                                }

                                $manual_item_discount = $postData['split'][$i]['manual_item_discount'][$key];
                                $subtotal = $postData['split'][$i]['subtotal'][$key];
                                $tot_dis1 = $this->input->post('[split][' . $i . '][tot_dis1]');
                                $item_dis = $postData['split'][$i]['item_dis'][$key];
                                $item_discount = $postData['split'][$i]['item_discount'][$key];
                                $finalAmt = $subtotal - $item_discount - $manual_item_discount - $offer_dis;
                                if ($this->input->post('[split][' . $i . '][order_discount_input]')) {
                                    if ($this->Settings->customer_discount == "customer") {
                                        $recipe_id = $postData['split'][$i]['recipe_id'][$key];
                                        $customer_discount_status = 'applied';
                                        $discountid = $this->input->post('[split][' . $i . '][order_discount_input]');
                                        $recipeDetails = $this->pos_model->getrecipeByID($recipe_id);
                                        $group_id = $recipeDetails->category_id;
                                        $subcategory_id = $recipeDetails->subcategory_id;
                                        if (isset($postData['split'][$i]['item_cus_dis_val'][$key])) {
                                            $item_customer_discount_val = $postData['split'][$i]['item_cus_dis_val'][$key];
                                            $input_dis = $this->site->calculateDiscount($item_customer_discount_val, $finalAmt);
                                        } else {
                                            $input_dis = $this->pos_model->recipe_customer_discount_calculation($recipe_id, $group_id, $subcategory_id, $finalAmt, $discountid);
                                        }

                                    } else if ($this->Settings->customer_discount == "manual") {
                                        if (isset($postData['split'][$i]['item_cus_dis_val'][$key])) {
                                            $item_customer_discount_val = $postData['split'][$i]['item_cus_dis_val'][$key];
                                            $input_dis = $this->site->calculateDiscount($item_customer_discount_val, $finalAmt);

                                        } else {
                                            $input_dis = $this->site->calculate_Discount($this->input->post('[split][' . $i . '][order_discount_input]'), (($postData['split'][$i]['subtotal'][$key] - $postData['split'][$i]['item_discount'][$key]) - $offer_dis), $tot_dis1 ? $tot_dis1 : $item_dis);
                                        }
                                    }
                                } else {
                                    $input_dis = 0;
                                }
                                $item_birday_dis = 0;
                                $birthday_discount = $this->input->post('[split][' . $i . '][birthday_discount]');
                                $total_item = $this->input->post('[split][' . $i . '][total_item]');
                                $item_birday_dis = $birthday_discount / $total_item;
                                /*item service charge */
                                $item_service_charge = 0;
                                if (!empty($this->input->post('[split][' . $i . '][service_charge]'))) {
                                    $item_service_charge = $this->site->calculateServiceCharge($this->input->post('[split][' . $i . '][service_charge]'), ($postData['split'][$i]['subtotal'][$key] - ($manual_item_discount ? $manual_item_discount : 0) - ($offer_dis ? $offer_dis : 0) - ($input_dis ? $input_dis : 0) - ($postData['split'][$i]['item_discount'][$key]) - $item_birday_dis));
                                }
                                /*item service charge */

                                if ($this->input->post('[split][' . $i . '][ptax]')) {
                                    $tax_type = $this->input->post('[split][' . $i . '][tax_type]');
                                    if ($tax_type != 0) {
                                        $itemtax = $this->site->calculateOrderTax($this->input->post('[split][' . $i . '][ptax]'), ($postData['split'][$i]['subtotal'][$key] - ($manual_item_discount ? $manual_item_discount : 0) - ($offer_dis ? $offer_dis : 0) - ($input_dis ? $input_dis : 0) - ($postData['split'][$i]['item_discount'][$key]) - $item_birday_dis));
                                        $sub_val = $postData['split'][$i]['subtotal'][$key];
                                    } else {
                                        $default_tax = $this->site->calculateOrderTax($this->input->post('[split][' . $i . '][ptax]'), ($postData['split'][$i]['subtotal'][$key] - ($offer_dis ? $offer_dis : 0) - ($input_dis ? $input_dis : 0) - ($postData['split'][$i]['item_discount'][$key])));
                                        $final_val = ($postData['split'][$i]['subtotal'][$key] - ($manual_item_discount ? $manual_item_discount : 0) - ($offer_dis ? $offer_dis : 0) - ($input_dis ? $input_dis : 0) - ($postData['split'][$i]['item_discount'][$key]) - $item_birday_dis);
                                        $subval = $final_val / (($default_tax / $final_val) + 1);
                                        $getTax = $this->site->getTaxRateByID($this->input->post('[split][' . $i . '][ptax]'));
                                        $itemtax = ($subval) * ($getTax->rate / 100);
                                        $sub_val = $postData['split'][$i]['subtotal'][$key];
                                    }
                                } else {
                                    $sub_val = $postData['split'][$i]['subtotal'][$key];
                                }

                                $input_dis = $input_dis;
                                $item_net_price = $postData['split'][$i]['unit_price'][$key] * $postData['split'][$i]['quantity'][$key];
                                $manual_discount = $postData['split'][$i]['manual_item_discount'][$key];
                                $item_discount = $postData['split'][$i]['item_discount'][$key];
                                $off_discount = $offer_dis ? $offer_dis : 0;
                                $input_discount = $postData['split'][$i]['item_cus_dis'][$key];
                                $comment_price = $postData['split'][$i]['comment_price'][$key] ? $postData['split'][$i]['comment_price'][$key] : 0;
                                $addonsubtotal = $postData['split'][$i]['addonsubtotal'][$key] ? $postData['split'][$i]['addonsubtotal'][$key] : 0;
                                $item_total_discount = $manual_discount + $item_discount + $off_discount + $input_discount + $item_birday_dis;

                                $splitData[$i][] = array(
                                    'recipe_name' => $split,
                                    'recipe_variant' => $postData['split'][$i]['recipe_variant'][$key],
                                    'recipe_variant_id' => $postData['split'][$i]['recipe_variant_id'][$key],
                                    'unit_price' => $postData['split'][$i]['unit_price'][$key],
                                    'net_unit_price' => $postData['split'][$i]['unit_price'][$key] * $postData['split'][$i]['quantity'][$key],
                                    'warehouse_id' => $this->session->userdata('warehouse_id'),
                                    'recipe_type' => $postData['split'][$i]['recipe_type'][$key],
                                    'quantity' => $postData['split'][$i]['quantity'][$key],
                                    'recipe_id' => $postData['split'][$i]['recipe_id'][$key],
                                    'recipe_code' => $postData['split'][$i]['recipe_code'][$key],
                                    'discount' => $postData['split'][$i]['item_discount_id'][$key],
                                    'item_discount' => $postData['split'][$i]['item_discount'][$key],
                                    'off_discount' => $offer_dis ? $offer_dis : 0,
                                    'customer_discount_val' => @($postData['split'][$i]['item_cus_dis_val'][$key] != '') ? $postData['split'][$i]['item_cus_dis_val'][$key] . '%' : '',
                                    'input_discount' => $postData['split'][$i]['item_cus_dis'][$key],
                                    'birthday_discount' => $item_birday_dis,
                                    'manual_item_discount' => $postData['split'][$i]['manual_item_discount'][$key],
                                    'manual_item_discount_val' => $postData['split'][$i]['manual_item_discount_val'][$key],
                                    'manual_item_discount_per_val' => $postData['split'][$i]['manual_item_discount_per_val'][$key],
                                    'tax_type' => $this->input->post('[split][' . $i . '][tax_type]'),
                                    'tax' => $itemtax,
                                    'subtotal' => $sub_val,
                                    'sale_item_id' => $postData['split'][$i]['order_item_id'][$key],

                                    'service_charge_id' => $postData['split'][$i]['service_charge'][$key] ? $postData['split'][$i]['service_charge'][$key] : 0,
                                    'service_charge_amount' => $item_service_charge,
                                    'grand_total' => $item_net_price + $comment_price + $addonsubtotal + $itemtax + $item_service_charge - $item_total_discount,
                                    'addon_id' => $postData['split'][$i]['addon_id'][$key] ? $postData['split'][$i]['addon_id'][$key] : '',
                                    'addon_qty' => $postData['split'][$i]['addon_qty'][$key] ? $postData['split'][$i]['addon_qty'][$key] : '',
                                    'comment' => $postData['split'][$i]['comment'][$key] ? $postData['split'][$i]['comment'][$key] : '',
                                    'comment_price' => $postData['split'][$i]['comment_price'][$key] ? $postData['split'][$i]['comment_price'][$key] : 0,

                                    /*'subtotal' => $postData['split'][$i]['subtotal'][$key]-(($input_dis ? $input_dis:0)-($offer_dis ? $offer_dis:0)-($postData['split'][$i]['item_discount'][$key]+$itemtax)),*/
                                );
                            }
                            if ($this->input->post('[split][' . $i . '][order_discount_input]')) {
                                $cus_discount_type = $this->Settings->customer_discount;
                                $cus_discount_val = '';
                                if ($this->Settings->customer_discount == "customer") {
                                    $cusdis = $this->input->post('[split][' . $i . '][order_discount_input]');
                                    $cusdis_val = $this->site->getCustomerDiscountval($cusdis);
                                    $cus_discount_val = $cusdis_val;
                                    $customer_discount_id = $this->input->post('[split][' . $i . '][order_discount_input]');
                                    // $cus_discount_val = $this->input->post('[split]['.$i.'][order_discount_input]').'%';
                                } else if ($this->Settings->customer_discount == "manual") {
                                    $cus_discount_val = $this->input->post('[split][' . $i . '][order_discount_input]');
                                }
                            } else {
                                $cus_discount_val = '';
                                $cus_discount_type = '';
                            }

                            $billData[$i] = array(
                                'reference_no' => $this->input->post('[split][' . $i . '][reference_no]'),
                                'date' => $this->site->getTransactionDate(),
                                'created_on' => date('Y-m-d H:i:s'),
                                'customer_id' => $this->input->post('[split][' . $i . '][customer_id]'),
                                'customer' => $this->input->post('[split][' . $i . '][customer]'),
                                'biller' => $this->input->post('[split][' . $i . '][biller]'),
                                'biller_id' => $this->input->post('[split][' . $i . '][biller_id]'),
                                'total_items' => $this->input->post('[split][' . $i . '][total_item]'),
                                'total' => $this->input->post('[split][' . $i . '][all_item_total]'),
                                'total_tax' => $this->input->post('[split][' . $i . '][tax_amount]'),
                                'tax_type' => $this->input->post('[split][' . $i . '][tax_type]'),
                                'tax_id' => $this->input->post('[split][' . $i . '][ptax]'),
                                'total_discount' => (($this->input->post('[split][' . $i . '][itemdiscounts]')) + ($this->input->post('[split][' . $i . '][offer_dis]')) + ($this->input->post('[split][' . $i . '][discount_amount]')) + ($this->input->post('[split][' . $i . '][off_discount]') ? $this->input->post('[split][' . $i . '][off_discount]') : 0) + ($this->input->post('[split][' . $i . '][manual_discount_amount]') ? $this->input->post('[split][' . $i . '][manual_discount_amount]') : 0)),
                                'birthday_discount' => $this->input->post('[split][' . $i . '][birthday_discount]') ? $this->input->post('[split][' . $i . '][birthday_discount]') : 0,
                                'manual_item_discount' => $this->input->post('[split][' . $i . '][manual_discount_amount]') ? $this->input->post('[split][' . $i . '][manual_discount_amount]') : 0,
                                'grand_total' => $this->input->post('[split][' . $i . '][grand_total]'),
                                'round_total' => $this->input->post('[split][' . $i . '][round_total]'),
                                'bill_type' => $bill_type,
                                'order_type' => $order_type,
                                'delivery_person_id' => $delivery_person,
                                'order_discount_id' => $this->input->post('[split][' . $i . '][tot_dis_id]') ? $this->input->post('[split][' . $i . '][tot_dis_id]') : null,

                                'created_by' => $this->session->userdata('user_id'),
                                'customer_discount_id' => $customer_discount_id ? $customer_discount_id : 0,
                                'discount_type' => $cus_discount_type,
                                'discount_val' => $cus_discount_val,
                                'order_discount' => $this->input->post('[split][' . $i . '][discount_amount]') ? $this->input->post('[split][' . $i . '][discount_amount]') : null,
                                'service_charge_id' => $this->input->post('[split][' . $i . '][service_charge]') ? $this->input->post('[split][' . $i . '][service_charge]') : 0,
                                'service_charge_amount' => $this->input->post('[split][' . $i . '][service_amount]') ? $this->input->post('[split][' . $i . '][service_amount]') : 0,
                                'warehouse_id' => $this->session->userdata('warehouse_id'),
								//'warehouse_id' => $this->isWarehouse,
				               'store_id' => $this->store_id,
			               	   'till_id' => $this->till_id,
				               'shift_id' =>!empty($this->ShiftID)?$this->ShiftID:0,
							    'member_dis_cardno' =>!empty($this->input->post('member_dicount_card_number'))?$this->input->post('member_dicount_card_number'):'',
							   'member_dscount' =>!empty($this->input->post('member_discount'))?$this->input->post('member_discount'):'',
							   'member_discount_type' =>!empty($this->input->post('member_discount_type'))?$this->input->post('member_discount_type'):'',
                            );

                            if (isset($_POST['unique_discount'])) {
                                $billData[$i]['unique_discount'] = 1;
                            }
                        }
                        // echo "<pre>";print_r($this->input->post ());die;
                        /*echo "<pre>";
                        print_r($splitData);
                        print_r($billData);
                        die;            */
                        $sales_total = array_column($billData, 'grand_total');
                        $sales_total = array_sum($sales_total);

                        if ($birthday_discount != 0) {
                            $birthday = array(
                                'customer_id' => $customer_id,
                                'birthday_discount' => $birthday_discount,
                                'status' => 1,
                                'issue_date' => date('Y-m-d'),
                                'created_at' => $this->session->userdata('user_id'),
                                'created_on' => date('Y-m-d H:i:s'),
                            );
                        } else {
                            $birthday = array();
                        }

                        $dine_in_discount = $this->input->post('dine_in_discount');
                       // echo '<pre>';print_R($order_item);exit;
                        $response = $this->pos_model->InsertBill($order_data, $order_item, $billData, $splitData, $sales_total, $delivery_person, $timelog_array, $notification_array, $order_item_id, $split_id, $request_discount, $dine_in_discount, $birthday);
                        if ($response == 1) {
                            $update_notifi['split_id'] = $split_id;
                            $update_notifi['tag'] = 'bill-request';
                            $this->site->update_notification_status($update_notifi);
                            if ($order_type == 1) {
                                $tableid = $order_data['sales_table_id'];
								$this->session->set_flashdata('message','Bill Generation Completed.');
                                redirect("pos/pos/payment/?type=1&table=".$table_id."&split_id=".$split_id."");
                            } elseif ($order_type == 2) {
                              redirect("pos/pos/payment/?type=2&split_id=".$split_id."");
                            } elseif ($order_type == 3) {
                              redirect("pos/pos/payment/?type=3&split_id=".$split_id."");
                            }
                        }
                    } else {
                        $customer_id = $this->site->getCustomerDetails($waiter_id, $table_id, $split_id);
                        $this->data['discount_select'] = $this->pos_model->getDiscountdata($customer_id, $waiter_id, $table_id, $split_id);

                        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
                        if ($this->pos_settings->billgeneration_screen == 1) {
                           $this->load->view($this->theme . 'pos_v2/singlebil', $this->data);
                        } else {
                            $this->load->view($this->theme . 'pos_v2/template2/singlebil', $this->data);
                        }
                    }
                }
            } else {
				
                $customer_id = $this->site->getCustomerDetails($waiter_id, $table_id, $split_id);
                $this->data['discount_select'] = $this->pos_model->getDiscountdata($customer_id, $waiter_id, $table_id, $split_id);
                $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
				$this->load->view($this->theme . 'pos_v2/singlebil', $this->data);
            }
        } elseif ($bill_type == 2) {
            $this->form_validation->set_rules('splits', $this->lang->line("splits"), 'trim|required');
            if ($this->form_validation->run() == true) {
                if ($this->input->post('action') == "AUTOSPLITBILL-SUBMIT") {
                    //echo "<pre>";
                    //print_r($this->input->post());die;
                    if ($this->input->post('bill_type')) {
                        $recipe_name = $this->input->post('recipe_name[]');
						$no_of_bills=$this->input->post('bils');
                        $splitData = array();
                        for ($i = 1; $i <= $this->input->post('bils'); $i++) {
                            $tot_item = $this->input->post('[split][' . $i . '][total_item]');
                            $itemdis = $this->input->post('[split][' . $i . '][discount_amount]') / $tot_item;
                            $billitem['bills_items'] = array();
                            $bills_item['recipe_name'] = $this->input->post('split[][$i][recipe_name][$i]');
                            if (!empty($postData['split'][$i]['order_discount_input'])) {
                                $request_discount[$i] = array(
                                    'customer_id' => $customer_id,
                                    'waiter_id' => $this->session->userdata('user_id'),
                                    'table_id' => $table_id,
                                    'split_id' => $split_id,
                                    'customer_type_val' => $this->Settings->customer_discount ? $this->Settings->customer_discount : '',
                                    'customer_discount_val' => $this->input->post('split[' . $i . '][order_discount_input]') ? $this->input->post('split[' . $i . '][order_discount_input]') : '',
                                    'created_on' => date('Y-m-d H:i:s'),
                                );

                            }
                            $tot_runtime_dis = 0;
                            foreach ($postData['split'][$i]['recipe_name'] as $key => $split) {

                                $offer_dis = 0.0000;
                                if ($this->input->post('[split][' . $i . '][tot_dis_value]')) {
                                    $offer_dis = $this->site->calculate_Discount($this->input->post('[split][' . $i . '][tot_dis_value]'), ($postData['split'][$i]['subtotal'][$key] - $postData['split'][$i]['manual_item_discount'][$key] - $postData['split'][$i]['item_discount'][$key]), $this->input->post('[split][' . $i . '][item_dis]'));
                                }
                                $manual_item_discount = $postData['split'][$i]['manual_item_discount'][$key];
                                $tot_runtime_dis = $postData['split'][$i]['manual_item_discount'][$key] ? $postData['split'][$i]['manual_item_discount'][$key] : 0;
                                $customer_discount_id = $this->input->post('[split][' . $i . '][order_discount_input]');
                                $customer_discount_status = '';

                                if ($this->input->post('[split][' . $i . '][order_discount_input]') != 0) {
                                    $recipe_id = $postData['split'][$i]['recipe_id'][$key];
                                    /*echo $recipe_id;die;*/
                                    $customer_discount_status = 'applied';
                                    $recipeDetails = $this->pos_model->getrecipeByID($recipe_id);
                                    $group_id = $recipeDetails->category_id;
                                    $subcategory_id = $recipeDetails->subcategory_id;
                                    $subtotal = $postData['split'][$i]['subtotal'][$key];
                                    $tot_dis1 = $this->input->post('[split][' . $i . '][tot_dis1]');

                                    $item_dis = $postData['split'][$i]['item_dis'][$key];

                                    $item_discount = $postData['split'][$i]['item_discount'][$key];
                                    $discountid = $this->input->post('[split][' . $i . '][order_discount_input]');

                                    $finalAmt = $subtotal - $item_discount - $manual_item_discount - $offer_dis;
                                    if ($this->Settings->customer_discount == "customer") {
                                        $input_dis = $this->pos_model->recipe_customer_discount_calculation($recipe_id, $group_id, $subcategory_id, $finalAmt, $discountid);
                                    } else if ($this->Settings->customer_discount == "manual") {
                                        $input_dis = $this->site->calculate_Discount($this->input->post('[split][' . $i . '][order_discount_input]'), (($postData['split'][$i]['subtotal'][$key] - $postData['split'][$i]['item_discount'][$key]) - $offer_dis), $tot_dis1 ? $tot_dis1 : $item_dis);
                                    }
                                    /*$input_dis = $this->site->calculate_Discount($this->input->post('[split]['.$i.'][order_discount_input]'), (($postData['split'][$i]['subtotal'][$key]-$postData['split'][$i]['item_discount'][$key])-$offer_dis),$tot_dis1 ? $tot_dis1 : $item_dis );*/
                                } else {
                                    $input_dis = 0;
                                }
                                if ($this->input->post('[split][' . $i . '][ptax]')) {
                                    $tax_type = $this->input->post('[split][' . $i . '][tax_type]');
                                    if ($tax_type != 0) {
                                        $itemtax = $this->site->calculateOrderTax($this->input->post('[split][' . $i . '][ptax]'), ($postData['split'][$i]['subtotal'][$key] - ($manual_item_discount ? $manual_item_discount : 0) - ($offer_dis ? $offer_dis : 0) - ($input_dis ? $input_dis : 0) - ($postData['split'][$i]['item_discount'][$key])));

                                        $sub_val = $postData['split'][$i]['subtotal'][$key];
                                    } else {

                                        $default_tax = $this->site->calculateOrderTax($this->input->post('[split][' . $i . '][ptax]'), ($postData['split'][$i]['subtotal'][$key] - ($manual_item_discount ? $manual_item_discount : 0) - ($offer_dis ? $offer_dis : 0) - ($input_dis ? $input_dis : 0) - ($postData['split'][$i]['item_discount'][$key])));

                                        $final_val = ($postData['split'][$i]['subtotal'][$key] - ($manual_item_discount ? $manual_item_discount : 0) - ($offer_dis ? $offer_dis : 0) - ($input_dis ? $input_dis : 0) - ($postData['split'][$i]['item_discount'][$key]));

                                        $subval = $final_val / (($default_tax / $final_val) + 1);

                                        $getTax = $this->site->getTaxRateByID($this->input->post('[split][' . $i . '][ptax]'));

                                        $itemtax = ($subval) * ($getTax->rate / 100);

                                        $sub_val = $postData['split'][$i]['subtotal'][$key];
                                    }

                                }
                                $addonsubtotal = $postData['split'][$i]['addonsubtotal'][$key] ? $postData['split'][$i]['addonsubtotal'][$key] : 0;

                                $splitData[$i][] = array(
                                    'recipe_name' => $split,
									 'recipe_variant' => $postData['split'][$i]['recipe_variant'][$key],
                                    'unit_price' => $postData['split'][$i]['unit_price'][$key],
                                    'net_unit_price' => $postData['split'][$i]['unit_price'][$key] * $postData['split'][$i]['quantity'][$key],
                                    'warehouse_id' => $this->session->userdata('warehouse_id'),
                                    'recipe_type' => $postData['split'][$i]['recipe_type'][$key],
                                    'quantity' => $postData['split'][$i]['quantity'][$key],
                                    'recipe_id' => $postData['split'][$i]['recipe_id'][$key],
                                    'recipe_code' => $postData['split'][$i]['recipe_code'][$key],
                                    'discount' => $postData['split'][$i]['item_discount_id'][$key],
                                    'manual_item_discount' => $postData['split'][$i]['manual_item_discount'][$key],
                                    'item_discount' => $postData['split'][$i]['item_discount'][$key],
                                    'off_discount' => $offer_dis ? $offer_dis : 0,
                                    'input_discount' => $input_dis ? $input_dis : 0,
                                    'tax' => $itemtax,
                                    'subtotal' => $sub_val,
                                    'grand_total' => $sub_val + $addonsubtotal - $item_total_discount,
									 'addon_id' => $postData['split'][$i]['addon_id'][$key] ? $postData['split'][$i]['addon_id'][$key] : '',
                                     'addon_qty' => $postData['split'][$i]['addon_qty'][$key] ? $postData['split'][$i]['addon_qty'][$key] : '',
                                );
                            }
							
                            if ($this->input->post('[split][' . $i . '][order_discount_input]')) {
                                $cus_discount_type = $this->Settings->customer_discount;
                                $cus_discount_val = '';
                                if ($this->Settings->customer_discount == "customer") {
                                    $cus_discount_val = $this->input->post('[split][' . $i . '][order_discount_input]') . '%';
                                } else if ($this->Settings->customer_discount == "manual") {
                                    $cus_discount_val = $this->input->post('[split][' . $i . '][order_discount_input]');
                                }
                            } else {
                                $cus_discount_val = '';
                                $cus_discount_type = '';
                            }
                            $billData[$i] = array(
                                'reference_no' => $this->input->post('[split][' . $i . '][reference_no]'),
                                'date' => $this->site->getTransactionDate(),
                                'created_on' => date('Y-m-d H:i:s'),
                                'customer_id' => $this->input->post('[split][' . $i . '][customer_id]'),
                                'customer' => $this->input->post('[split][' . $i . '][customer]'),
                                'biller' => $this->input->post('[split][' . $i . '][biller]'),
                                'biller_id' => $this->input->post('[split][' . $i . '][biller_id]'),
                                'total_items' => $this->input->post('[split][' . $i . '][total_item]'),
                                'total' => $this->input->post('[split][' . $i . '][total_price]'),
                                'tax_type' => $this->input->post('[split][' . $i . '][tax_type]'),
                                'tax_id' => $this->input->post('[split][' . $i . '][ptax]'),
                                'total_tax' => $this->input->post('[split][' . $i . '][tax_amount]'),
                                'total_discount' => (($this->input->post('[split][' . $i . '][itemdiscounts]')) + ($this->input->post('[split][' . $i . '][offer_dis]')) + ($this->input->post('[split][' . $i . '][discount_amount]')) + ($this->input->post('[split][' . $i . '][off_discount]') ? $this->input->post('[split][' . $i . '][off_discount]') : 0) + ($tot_runtime_dis ? $tot_runtime_dis : 0)),
                                'grand_total' => $this->input->post('[split][' . $i . '][grand_total]'),
                                'round_total' => $this->input->post('[split][' . $i . '][round_total]'),
                                'bill_type' => $bill_type,
                                'delivery_person_id' => $delivery_person,
                                'order_discount_id' => $this->input->post('[split][' . $i . '][tot_dis_id]') ? $this->input->post('[split][' . $i . '][tot_dis_id]') : null,
                                
                                'created_by' => $this->session->userdata('user_id'),
                                'customer_discount_id' => $customer_discount_id,
                                'customer_discount_status' => $customer_discount_status,
                                'discount_type' => $cus_discount_type,
                                'discount_val' => $cus_discount_val,
								'warehouse_id' => $this->session->userdata('warehouse_id'),
								//'warehouse_id' => $this->isWarehouse,
				                'store_id' => $this->store_id,
				                'till_id' => $this->till_id,
				                'shift_id' =>!empty($this->ShiftID)?$this->ShiftID:0
                            );
                            if (isset($_POST['unique_discount'])) {
                                $billData[$i]['unique_discount'] = 1;
                            }
                        }

                        $sales_total = array_column($billData, 'grand_total');
                        $sales_total = array_sum($sales_total);

                        if ($birthday_discount != 0) {
                            $birthday = array(
                                'customer_id' => $customer_id,
                                'birthday_discount' => $birthday_discount,
                                'status' => 1,
                                'issue_date' => date('Y-m-d'),
                                'created_at' => $this->session->userdata('user_id'),
                                'created_on' => date('Y-m-d H:i:s'),
                            );
                        } else {
                            $birthday = array();
                        }
/* print_r($splitData);
							die; 
/*
echo "<pre>";

print_r($splitData);
die;*/
//print_r($billData);die;        

                        $response = $this->pos_model->InsertBill_all($order_data, $order_item, $billData, $splitData, $sales_total, $delivery_person, $timelog_array, $notification_array, $order_item_id, $split_id, $request_discount, $dine_in_discount, $birthday);

                        if ($response == 1) {
                            $update_notifi['split_id'] = $split_id;
                            $update_notifi['tag'] = 'bill-request';
                            $this->site->update_notification_status($update_notifi);
							
                            if ($order_type == 1) {
                                $tableid = $order_data['sales_table_id'];
								$this->session->set_flashdata('message','Bill Generation Completed.');
                                redirect("pos/pos/payment/?type=1&table=".$table_id."&split_id=".$split_id."");
                            } elseif ($order_type == 2) {
                                redirect("pos/pos/home");
                            } elseif ($order_type == 3) {
                               redirect("pos/pos/home");
                            }
                        }

                    } else {
                        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
                        $this->load->view($this->theme . 'pos_v2/autosplitbil', $this->data);
                    }

                }
            } else {
                $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
                $this->load->view($this->theme . 'pos_v2/autosplitbil', $this->data);
            }

        } elseif ($bill_type == 3) {

            $this->form_validation->set_rules('splits', $this->lang->line("splits"), 'trim|required');
			
            if ($this->form_validation->run() == true) {

                if ($this->input->post('action') == "MANUALSPLITBILL-SUBMIT") {
					
                    /*echo "<pre>";
                    print_r($this->input->post());die;*/
                    if ($this->input->post('bill_type')) {
                        $recipe_name = $this->input->post('recipe_name[]');
                        $splitData = array();
                        for ($i = 1; $i <= $this->input->post('bils'); $i++) {
                            $tot_item = $this->input->post('[split][' . $i . '][total_item]');
                            $itemdis = $this->input->post('[split][' . $i . '][discount_amount]') / $tot_item;

                            $billitem['bills_items'] = array();
                            $bills_item['recipe_name'] = $this->input->post('split[][$i][recipe_name][$i]');
                            if (!empty($postData['split'][$i]['order_discount_input'])) {
                                $request_discount[$i] = array(
                                    'customer_id' => $customer_id,
                                    'waiter_id' => $this->session->userdata('user_id'),
                                    'table_id' => $table_id,
                                    'split_id' => $split_id,
                                    'customer_type_val' => $this->Settings->customer_discount ? $this->Settings->customer_discount : '',
                                    'customer_discount_val' => $this->input->post('split[' . $i . '][order_discount_input]') ? $this->input->post('split[' . $i . '][order_discount_input]') : '',
                                    'created_on' => date('Y-m-d H:i:s'),
                                );

                            } else {
                                $request_discount[$i] = array();
                            }
						
                            foreach ($postData['split'][$i]['recipe_name'] as $key => $split) {

                                $offer_dis = 0.0000;
                                if ($this->input->post('[split][' . $i . '][tot_dis_value]')) {
                                    $offer_dis = $this->site->calculate_Discount($this->input->post('[split][' . $i . '][tot_dis_value]'), ($postData['split'][$i]['subtotal'][$key] - $postData['split'][$i]['item_discount'][$key]), $this->input->post('[split][' . $i . '][item_dis]'));
                                }
                                $customer_discount_id = $this->input->post('[split][' . $i . '][order_discount_input]');
                                $customer_discount_status = '';

                                if ($this->input->post('[split][' . $i . '][order_discount_input]') != 0) {
                                    $recipe_id = $postData['split'][$i]['recipe_id'][$key];
                                    /*echo $recipe_id;die;*/
                                    $customer_discount_status = 'applied';

                                    $recipeDetails = $this->pos_model->getrecipeByID($recipe_id);
                                    $group_id = $recipeDetails->category_id;
                                    $subcategory_id = $recipeDetails->subcategory_id;

                                    $subtotal = $postData['split'][$i]['subtotal'][$key];

                                    $tot_dis1 = $this->input->post('[split][' . $i . '][tot_dis1]');

                                    $item_dis = $postData['split'][$i]['item_dis'][$key];

                                    $item_discount = $postData['split'][$i]['item_discount'][$key];

                                    $discountid = $this->input->post('[split][' . $i . '][order_discount_input]');

                                    $finalAmt = $subtotal - $item_discount - $offer_dis;
                                    if ($this->Settings->customer_discount == "customer") {
                                        $input_dis = $this->pos_model->recipe_customer_discount_calculation($recipe_id, $group_id, $subcategory_id, $finalAmt, $discountid);
                                    } else if ($this->Settings->customer_discount == "manual") {
                                        $input_dis = $this->site->calculate_Discount($this->input->post('[split][' . $i . '][order_discount_input]'), (($postData['split'][$i]['subtotal'][$key] - $postData['split'][$i]['item_discount'][$key]) - $offer_dis), $tot_dis1 ? $tot_dis1 : $item_dis);
                                    }
                                    /*$input_dis = $this->site->calculate_Discount($this->input->post('[split]['.$i.'][order_discount_input]'), (($postData['split'][$i]['subtotal'][$key]-$postData['split'][$i]['item_discount'][$key])-$offer_dis),$tot_dis1 ? $tot_dis1 : $item_dis );*/
                                } else {
                                    $input_dis = 0;
                                }
                                if ($this->input->post('[split][' . $i . '][ptax]')) {
                                    $tax_type = $this->input->post('[split][' . $i . '][tax_type]');
                                    if ($tax_type != 0) {
                                        $itemtax = $this->site->calculateOrderTax($this->input->post('[split][' . $i . '][ptax]'), ($postData['split'][$i]['subtotal'][$key] - ($offer_dis ? $offer_dis : 0) - ($input_dis ? $input_dis : 0) - ($postData['split'][$i]['item_discount'][$key])));

                                        $sub_val = $postData['split'][$i]['subtotal'][$key];
                                    } else {

                                        $default_tax = $this->site->calculateOrderTax($this->input->post('[split][' . $i . '][ptax]'), ($postData['split'][$i]['subtotal'][$key] - ($offer_dis ? $offer_dis : 0) - ($input_dis ? $input_dis : 0) - ($postData['split'][$i]['item_discount'][$key])));

                                        $final_val = ($postData['split'][$i]['subtotal'][$key] - ($offer_dis ? $offer_dis : 0) - ($input_dis ? $input_dis : 0) - ($postData['split'][$i]['item_discount'][$key]));

                                        $subval = $final_val / (($default_tax / $final_val) + 1);

                                        $getTax = $this->site->getTaxRateByID($this->input->post('[split][' . $i . '][ptax]'));

                                        $itemtax = ($subval) * ($getTax->rate / 100);

                                        $sub_val = $postData['split'][$i]['subtotal'][$key];
                                    }

                                }

                                $splitData[$i][] = array(
                                    'recipe_name' => $split,
                                    'unit_price' => $postData['split'][$i]['unit_price'][$key],
									 'recipe_variant' => $postData['split'][$i]['recipe_variant'][$key],
                                    'net_unit_price' => $postData['split'][$i]['unit_price'][$key] * $postData['split'][$i]['quantity'][$key],
                                    'warehouse_id' => $this->session->userdata('warehouse_id'),
                                    'recipe_type' => $postData['split'][$i]['recipe_type'][$key],
                                    'quantity' => $postData['split'][$i]['quantity'][$key],
                                    'recipe_id' => $postData['split'][$i]['recipe_id'][$key],
                                    'recipe_code' => $postData['split'][$i]['recipe_code'][$key],
                                    'discount' => $postData['split'][$i]['item_discount_id'][$key],
                                    'item_discount' => $postData['split'][$i]['item_discount'][$key],
                                    'off_discount' => $offer_dis ? $offer_dis : 0,
                                    'input_discount' => $input_dis ? $input_dis : 0,
                                    'tax' => $itemtax,
                                    'subtotal' => $sub_val,
									 'addon_id' => $postData['split'][$i]['addon_id'][$key] ? $postData['split'][$i]['addon_id'][$key] : '',
                                     'addon_qty' => $postData['split'][$i]['addon_qty'][$key] ? $postData['split'][$i]['addon_qty'][$key] : '',
                                );
                            }
                            if ($this->input->post('[split][' . $i . '][order_discount_input]')) {
                                $cus_discount_type = $this->Settings->customer_discount;
                                $cus_discount_val = '';
                                if ($this->Settings->customer_discount == "customer") {
                                    $cus_discount_val = $this->input->post('[split][' . $i . '][order_discount_input]') . '%';
                                } else if ($this->Settings->customer_discount == "manual") {
                                    $cus_discount_val = $this->input->post('[split][' . $i . '][order_discount_input]');
                                }
                            } else {
                                $cus_discount_val = '';
                                $cus_discount_type = '';
                            }
                            $billData[$i] = array(
                                'reference_no' => $this->input->post('[split][' . $i . '][reference_no]'),
                                'date' => $this->site->getTransactionDate(),
                                'created_on' => date('Y-m-d H:i:s'),
                                'customer_id' => $this->input->post('[split][' . $i . '][customer_id]'),
                                'customer' => $this->input->post('[split][' . $i . '][customer]'),
                                'biller' => $this->input->post('[split][' . $i . '][biller]'),
                                'biller_id' => $this->input->post('[split][' . $i . '][biller_id]'),
                                'total_items' => $this->input->post('[split][' . $i . '][total_item]'),
                                'total' => $this->input->post('[split][' . $i . '][total_price]'),
                                'tax_type' => $this->input->post('[split][' . $i . '][tax_type]'),
                                'tax_id' => $this->input->post('[split][' . $i . '][ptax]'),
                                'total_tax' => $this->input->post('[split][' . $i . '][tax_amount]'),
                                'total_discount' => (($this->input->post('[split][' . $i . '][itemdiscounts]')) + ($this->input->post('[split][' . $i . '][offer_dis]')) + ($this->input->post('[split][' . $i . '][discount_amount]')) + ($this->input->post('[split][' . $i . '][off_discount]') ? $this->input->post('[split][' . $i . '][off_discount]') : 0)),
                                'grand_total' => $this->input->post('[split][' . $i . '][grand_total]'),
                                'round_total' => $this->input->post('[split][' . $i . '][round_total]'),
                                'bill_type' => $bill_type,
                                'delivery_person_id' => $delivery_person,
                                'order_discount_id' => $this->input->post('[split][' . $i . '][tot_dis_id]') ? $this->input->post('[split][' . $i . '][tot_dis_id]') : null,
                               
                                'created_by' => $this->session->userdata('user_id'),
                                'customer_discount_id' => $customer_discount_id,
                                'customer_discount_status' => $customer_discount_status,
                                'discount_type' => $cus_discount_type,
                                'discount_val' => $cus_discount_val,
								 'warehouse_id' => $this->session->userdata('warehouse_id'),
								 //'warehouse_id' => $this->isWarehouse,
								'store_id' => $this->store_id,
								'till_id' => $this->till_id,
								'shift_id' =>!empty($this->ShiftID)?$this->ShiftID:0,
                            );
                            if (isset($_POST['unique_discount'])) {
                                $billData[$i]['unique_discount'] = 1;
                            }
                        }

                        $sales_total = array_column($billData, 'grand_total');
                        $sales_total = array_sum($sales_total);
                        $dine_in_discount = $this->input->post('dine_in_discount') ? $this->input->post('dine_in_discount') : 0;
			

                        $birthday = array();
                        /*echo "<pre>";

                        var_dump($dine_in_discount);
                        print_r($billData);die;    */

                        $response = $this->pos_model->InsertBill($order_data, $order_item, $billData, $splitData, $sales_total, $delivery_person, $timelog_array, $notification_array, $order_item_id, $split_id, $request_discount, $dine_in_discount, $birthday);
                        if ($response == 1) {
                            $update_notifi['split_id'] = $split_id;
                            $update_notifi['tag'] = 'bill-request';
                            $this->site->update_notification_status($update_notifi);
                            if ($order_type == 1) {
                                $tableid = $order_data['sales_table_id'];
								$this->session->set_flashdata('message','Bill Generation Completed.');
                                redirect("pos/pos/payment/?type=1&table=".$table_id."&split_id=".$split_id."");
                            } elseif ($order_type == 2) {
                                redirect("pos/pos/home");
                            } elseif ($order_type == 3) {
                               redirect("pos/pos/home");
                            }
                        }

                    } else {
                        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
                        $this->load->view($this->theme . 'pos_v2/manualsplitbil', $this->data);
                    }

                }

            } else {
                $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
                $this->load->view($this->theme . 'pos_v2/manualsplitbil', $this->data);
            }
        }

    } 
	public function paymant_all(){
        $this->data['warehouse'] = $this->session->userdata('warehouse_id') ? $this->site->getWarehouseByID($this->session->userdata('warehouse_id')) : null;
        $this->data['tax_rates'] = $this->site->getAllTaxRates();
        $currency = $this->site->getAllCurrencies();
        $postData = $this->input->post();
        if ($this->input->post('action') == "PAYMENT-SUBMIT") {
           /*  echo "<pre>";
            print_r($this->input->post());die; */
            $balance = $this->input->post('balance_amount');
            $dueamount = $this->input->post('due_amount');
            $total_pay = $this->input->post('total') + $this->input->post('balance_amount');
            $total = $this->input->post('total');
            $customer_changed = 0;
            $loyalty_customer = $this->input->post('loyalty_customer');
            $new_customer_id = $this->input->post('new_customer_id');
            if ($loyalty_customer) {
                $customer_changed = 1;
                $customer_id = $loyalty_customer;
            } elseif ($new_customer_id) {
                $customer_id = $this->input->post('new_customer_id');
                $customer_changed = 1;
            } else {
                $customer_id = $this->input->post('customer_id');
            }
            $order_split_id = $this->input->post('order_split_id');
            $paid = !empty($dueamount) ? ($total - $dueamount) : $total;
            $p = isset($_POST['paid_by']) ? sizeof($_POST['paid_by']) : 0;
            $default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);

            /*foreach($currency as $currency_row){
            if($default_currency_data->code == $currency_row->code){
            $p = isset($_POST['paid_by'.$currency_row->code.'']) ? sizeof($_POST['paid_by'.$currency_row->code.'']) : 0;
            $amount_.$currency_row->code = array_sum($_POST['amount_'.$currency_row->code.'']);
            }else{
            $amount_.$currency_row->code = array_sum($_POST['amount_'.$currency_row->code]);
            }
            }*/
            /*print_r($amount_);
            echo "string";die;*/
            //$amount_USD = array_sum($_POST['amount_USD']);
            $getExchangecode = $this->site->getExchangeRatecode($this->Settings->default_currency);
            // var_dump($getExchangecode);die;
            for ($r = 0; $r < $p; $r++) {
                if ($_POST['amount_' . $getExchangecode . ''][$r] == '' && $_POST['amount_' . $default_currency_data->code . ''][$r] == '') {
                    unset($link);
                } else {
                    foreach ($currency as $currency_row) {
                        if ($default_currency_data->code == $currency_row->code) {
                            $multi_currency[] = array(
                                'sale_id' => $this->input->post('sales_id'),
                                'bil_id' => $this->input->post('bill_id'),
                                'currency_id' => $currency_row->id,
                                'currency_rate' => $currency_row->rate,
                                'amount' => $_POST['amount_' . $currency_row->code][$r],
                            );
                        } else {
                            $multi_currency[] = array(
                                'sale_id' => $this->input->post('sales_id'),
                                'bil_id' => $this->input->post('bill_id'),
                                'currency_id' => $currency_row->id,
                                'currency_rate' => $currency_row->rate,
                                'amount' => $_POST['amount_' . $currency_row->code][$r],
                            );
                        }
                    }
                }
            }

            for ($r = 0; $r < $p; $r++) {
                if ($_POST['amount_' . $getExchangecode . ''][$r] == '' && $_POST['amount_' . $default_currency_data->code . ''][$r] == '') {
                    unset($link);
                } else {
                    foreach ($currency as $currency_row) {
                        if ($currency_row->rate == $default_currency_data->rate) {
                            $p = isset($_POST['amount_' . $currency_row->code][$r]) ? sizeof($_POST['amount_' . $currency_row->code]) : 0;
                            /*$amount_.$currency_row->code = array_sum($_POST['amount_'.$currency_row->code]);*/
                            $amount = $_POST['amount_' . $currency_row->code][$r];
                        } else {
                            /*$amount_.$currency_row->code = array_sum($_POST['amount_'.$currency_row->code]);*/
                            $amount_exchange = $_POST['amount_' . $currency_row->code][$r];

                        }
                    }
                    $crd_exp_date = explode('/', $this->input->post('card_exp_date[1]'));
					if(!empty($_POST['wallet_type'])){
					$wallets=$this->site->getWalletsById($_POST['wallet_type']);
					}
					
					
					
		/*********************  nc kot start block   ***************************************/
					$nc_kot_details=array();
					if($_POST['paid_by'][$r] =="nc_kot" && !empty($_POST['master_active'])){
						$nc_kot_type=$_POST['master_active'];
						$nc_kot_type_name=$this->site->get_ncKotMastersByid($_POST['master_active']);
						if(!empty($_POST['master_input'])){
						foreach(array_filter($_POST['master_input'][$_POST['master_active']]) as $t){
							if(!empty($t)){
							$nc_kot_details[]=array(
							"type"=>"input",
						    "type_name"=>"Comments",
						    "type_id"=>'',
						    "details"=>$t);  
							} } }
						if(!empty($_POST['master_select'])){
							foreach(json_decode($nc_kot_type_name->select_box_master) as $k=>$sm){
						    foreach(array_filter($_POST['master_select'][$_POST['master_active']][$sm]) as $key=>$s){
							$details=$this->pos_model->get_nkm_details($sm,$s);
							$nc_kot_details[]=array(
							"type"=>"Select",
						    "type_name"=>$details["type"],
						    "type_id"=>$s,
						    "details"=>$details["name"]);
						}
							}
						}
					}
		/******************  nc kot block  end  ******************************************/
		
		
                    $payment[$r] = array(
                        'date' => $this->site->getTransactionDate(),
                        'paid_on' => date('Y-m-d H:i:s'),
                        'sale_id' => $_POST['bill_id'],
                        'bill_id' => $_POST['bill_id'],
                        //'reference_no' => $this->input->post('reference_no'),
                        'amount' => $amount ? $amount : 0,
                        'amount_exchange' => $amount_exchange ? $amount_exchange : 0,
                        'pos_paid' => $_POST['amount_' . $default_currency_data->code . ''][$r],
                        'pos_balance' => round($balance, 3),
                        'paid_by' => $_POST['paid_by'][$r],
                        'cc_no' => $_POST['cc_no'][$r],
                        'cc_month' => $crd_exp_date[0],
                        'cc_year' => $crd_exp_date[1],
						'wallet_id' => $_POST['wallet_type'],
						'wallet_name' => !empty($wallets->name)?$wallets->name:'',
                        'created_by' => $this->session->userdata('user_id'),
                        'type' => 'received',
						'nc_kot_type'=>!empty($nc_kot_type)?$nc_kot_type:0,
						'nc_kot_details'=>!empty($nc_kot_details)?json_encode($nc_kot_details):0,
						'nc_kot_type_name'=>!empty($nc_kot_type_name)?$nc_kot_type_name->name:0,
                    );
                    if (isset($_POST['rough_tender'])) {
                        $payment[$r]['loyalty_points'] = $_POST['paying_loyalty_points'][$r];
                    }

                }
            }
            $loyalty_used_points = $this->input->post('loyalty_used_points') ? $this->input->post('loyalty_used_points') : 0;
            $billid = $this->input->post('bill_id');
            $salesid = $this->input->post('sales_id');
            $taxation = $this->input->post('taxation') ? $this->input->post('taxation') : 0;
            $update_bill = array(
                'updated_at' => date('Y-m-d H:i:s'),
                'paid_by' => $this->session->userdata('user_id'),
                'total_pay' => $total_pay,
                'balance' => $balance,
                'paid' => $paid,
                'payment_status' => 'Completed',
                'default_currency_code' => $default_currency_data->code,
                'default_currency_rate' => $default_currency_data->rate,
                'table_whitelisted' => $taxation,
            );
            $sales_bill = array(
                'grand_total' => $total,
                'paid' => $paid,
                'payment_status' => 'Paid',
                'default_currency_code' => $default_currency_data->code,
                'default_currency_rate' => $default_currency_data->rate,
            );
            $notification_array['from_role'] = $this->session->userdata('group_id');
            $notification_array['insert_array'] = array(
                'user_id' => $this->session->userdata('user_id'),
                'warehouse_id' => $this->session->userdata('warehouse_id'),
                'created_on' => date('Y-m-d H:m:s'),
                'is_read' => 0,
                'respective_steward' => 0,
                'split_id' => $order_split_id,
                'tag' => 'payment-done',
                'status' => 1,
            );
            $q = $this->db->select('*')->where('bill_id', $billid)->get('payments');
            if (isset($_POST['rough_tender'])) {
                $q = $this->db->select('*')->where('bill_id', $billid)->get('rough_tender_payments');
            }
            if ($q->num_rows() > 0) {
                $response = 1;
            } else {
                $updateCreditLimit['company_id'] = $postData['company_id'];
                $updateCreditLimit['customer_type'] = $postData['customer_type'];
                $new_payment = true;
                if (isset($_POST['rough_tender'])) {
                    $response = $this->pos_model->addRoughTender($billid, $payment, $multi_currency,  $updateCreditLimit);
                } else {
                    // echo "<pre>";
                    //print_r($payment);
                   //  print_r($multi_currency);die;
				   //die;
                    $response = $this->pos_model->Payment_for_consolidate($update_bill, $billid, $payment, $multi_currency, $salesid, $sales_bill, $order_split_id, $notification_array, $updateCreditLimit, $total, $customer_id, $loyalty_used_points, $taxation, $customer_changed);
                }
            }
            if ($response == 1) {
                //$this->send_to_bill_print($billid);
                $update_notifi['split_id'] = $order_split_id;
                $update_notifi['tag'] = 'bill-request';
                $this->site->update_notification_status($update_notifi);
                if ($taxation == 1) {
                      redirect("pos/pos/");
                }
                $this->data['order_item'] = $this->pos_model->getAllBillitems($billid);
                $this->data['message'] = $this->session->flashdata('message');
                $inv = $this->pos_model->getInvoiceByID($billid);
                $tableno = $this->pos_model->getTableNumber($billid);
                $this->load->helper('pos');
                if (!$this->session->userdata('view_right')) {
                    $this->sma->view_rights($inv->created_by, true);
                }
                /*$this->data['rows'] = $this->pos_model->getAllInvoiceItems($billid);*/
                $this->data['billi_tems'] = $this->pos_model->getAllBillitems($billid);
                $this->data['discounnames'] = $this->pos_model->getBillDiscountNames($billid);
                $biller_id = $inv->biller_id;
                $bill_id = $inv->sales_id;
                $customer_id = $inv->customer_id;
                $delivery_person_id = $inv->delivery_person_id;
                $this->data['inv'] = $inv;
                $this->data['tableno'] = $tableno;
                $this->data['customer'] = $this->pos_model->getCompanyByID($customer_id);
                if ($delivery_person_id != 0) {
                    $this->data['delivery_person'] = $this->pos_model->getUserByID($delivery_person_id);
                }
                $this->data['created_by'] = $this->site->getUser($inv->created_by);
                $this->data['cashier'] = $this->pos_model->getCashierInfo($billid);
                $this->data['printer'] = $this->pos_model->getPrinterByID($this->pos_settings->printer);
                $this->data['biller'] = $this->pos_model->getCompanyByID($biller_id);
                if (isset($_POST['rough_tender'])) {
                    $this->data['inv']->balance = $update_bill['balance'];
                    $this->data['payments'] = $this->pos_model->getInvoiceRoughTenderPayments($this->input->post('bill_id'));
                } else {
                    $this->data['payments'] = $this->pos_model->getInvoicePayments($this->input->post('bill_id'));
                }
                /*echo "<pre>";
                var_du($this->data['payments']);die;*/
                $this->data['return_sale'] = $inv->return_id ? $this->pos_model->getInvoiceByID($inv->return_id) : null;
                $this->data['return_rows'] = $inv->return_id ? $this->pos_model->getAllInvoiceItems($inv->return_id) : null;
                $this->data['return_payments'] = $this->data['return_sale'] ? $this->pos_model->getInvoicePayments($this->data['return_sale']->id) : null;
                $this->data['type'] = $this->input->post('type');
				$this->data['tableno']= $this->pos_model->getTableNumber($inv->id);
				$this->data['store'] = $this->site->getWarehouseByID($inv->store_id);
/*echo "<pre>";
print_r($inv);die;*/
                $tableid = $this->pos_model->getTableID($billid);
				$this->data['tableid'] = $tableid;
                if (!empty($inv)) {
                    if (@$new_payment) {
                        $this->data['socket_tableid'] = $tableid;
                    }
                    if (isset($_POST['rough_tender'])) {
                        $this->data['rough_tender'] = true;
                    }
                   /*  if ($this->pos_settings->bill_print_format == 1) {
                        $this->load->view($this->theme . 'pos/consolidate/view_bill', $this->data);
                    } elseif ($this->pos_settings->bill_print_format == 3) {
                        $this->load->view($this->theme . 'pos/indai_bill/view_bill', $this->data, false);
                    } elseif ($this->pos_settings->bill_print_format == 4) {
                        $this->load->view($this->theme . 'pos/local_bill/view_bill', $this->data, false);
                    } else {
                        $this->load->view($this->theme . 'pos/row_discount/view_bill', $this->data);
                    } */
					   $this->load->view($this->theme . 'pos_v2/view_bill', $this->data);
                } else {
                    redirect("pos/pos/");
                }
            }
        } else {
            redirect("pos/pos/");
        }
    }

    public function gatdata_print_billing(){
        $id = $this->input->get('billid');
        $row['billdata'] = $this->pos_model->get_BillData($id);
        $row['billitemdata'] = $this->pos_model->getAllBillitems($id);

        $row['billdata']->service_charge_display_value = '';
        if ($row['billdata']->service_charge_id != 0) {
            $ServiceCharge = $this->site->getServiceChargeByID($row['billdata']->service_charge_id);
            $row['billdata']->service_charge_display_value = $ServiceCharge->name;
        }
        $row['discount'] = $this->pos_model->getBillDiscountNames($id);
        $inv = $this->pos_model->getInvoiceByID($id);
        $row['biller'] = $this->pos_model->getCompanyByID($inv->biller_id);
        $row['inv'] = $inv;
        $row['created_by'] = $this->site->getUser($inv->created_by);
        $row['cashier'] = $this->site->getUser($this->session->userdata('user_id'));
        $customer_id = $inv->customer_id;
        $delivery_person = $inv->delivery_person_id;
        $row['customer'] = $this->pos_model->getCompanyByID($customer_id);
		$store = $this->site->getWarehouseByID($inv->store_id);
		$tableno = $this->pos_model->getTableNumber($inv->id);
			if(in_array(3,json_decode( $this->pos_settings->floor_print))&&  $this->pos_settings->floor_area_print ==1){ 
			$row['floor_print']= "Floor :". $tableno->floor . "<br>";
			}
			if(in_array(3,json_decode( $this->pos_settings->nop_print))&&  $this->pos_settings->number_of_people_print ==1){
			 $row['people_print']=  !empty($inv->seats)? "People :". $inv->seats: "People :".$tableno->seats ;
		
			}
			if(in_array(3,json_decode( $this->pos_settings->vat_print))&&  $this->pos_settings->vat_number_print ==1 && !empty($store->vat_number)){
			  $row['vat_print']= !empty($store->vat_number)? "VAT Number :" . $store->vat_number:"";
			}
			if(in_array(3,json_decode( $this->pos_settings->cus_print))&&  $this->pos_settings->customer_name ==1 && !empty($store->vat_number)){
			 $row['custom_print'].=' <table class="table table-striped table-condensed">        
				 <tr><td style="border:solid 1px #fff ! important;">Name :</td><td style="border:solid 1px #fff ! important;">';
			 	$row['custom_print'].= ($inv->customer_id != $this->pos_settings->default_customer)? $inv->customer:"___________________";
				$row['custom_print'].= '</td></tr><tr><td width="20%">Signature</td><td>___________________</td></tr>
				 </table>';
			}
        if ($delivery_person != 0) {
            $row['delivery_person'] = $this->pos_model->getUserByID($delivery_person);
        }
        if ($this->pos_settings->bill_print_format == 3) {
            $row['tax_splits'] = $this->site->get_tax_splits($this->pos_settings->default_tax);
            $row['tax_rate'] = $this->site->getTaxRateByID($this->pos_settings->default_tax);
        }
        $this->sma->send_json($row);
    }
public function cancel_sale($cancel_remarks = null, $sale_id = null){
        $cancel_remarks = $this->input->get('cancel_remarks');
        $sale_id = $this->input->get('sale_id');
        $notification_array['from_role'] = $this->session->userdata('group_id');
        $notification_array['insert_array'] = array(
            'user_id' => $this->session->userdata('user_id'),
            'warehouse_id' => $this->session->userdata('warehouse_id'),
            'created_on' => date('Y-m-d H:m:s'),
            'is_read' => 0,
        );
        $result = $this->pos_model->CancelSale_consolidate($cancel_remarks, $sale_id, $this->session->userdata('user_id'), $notification_array);
        if ($result == true) {
            $msg = 'success';
        } else {
            $msg = 'error';
            $status = 'error';
        }
        $this->sma->send_json(array('msg' => $msg, 'status' => $msg));
    }
	public function sale_item_qty_adjustment($order_item_id = null, $action = null, $split_id = null){
        $action = $this->input->get('action');
        $order_item_id = $this->input->get('order_item_id');
        $split_id = $this->input->get('split_id');
        if ($action == 'plus') {
            $result = $this->pos_model->SaleItemQtyIncrease($order_item_id, $this->session->userdata('user_id'), $split_id);
        } else {
            $result = $this->pos_model->Saleitemqtyadjustment($order_item_id, $this->session->userdata('user_id'), $split_id);
        }
        if ($result == true) {
            $msg = 'success';
        } else {
            $msg = 'error';
            $status = 'error';
        }
        $this->sma->send_json(array('msg' => $msg, 'status' => $msg));
    }
  public function calculate_customerdiscount(){
        $recipeids = $this->input->post('recipeids');
        $recipevariantids = $this->input->post('recipevariantids');
        $split_id = $this->input->post('split_id');
        $customer_id = $this->input->post('customer_id');
        $table_id = $this->input->post('table_id');
        $recipeqtys = $this->input->post('recipeqtys');
        $manualitemdis = $this->input->post('manualitemdis');
        $addonsubtotal = $this->input->post('addonsubtotal');
        $discountid = $this->input->post('discountid');
        $divide = $this->input->post('divide');
        $discounttype = $this->input->post('discounttype');
        $reciepe_ids = explode(",", $recipeids);
        $reciepe_qtys = explode(",", $recipeqtys);
        $recipeva_riantids = explode(",", $recipevariantids);
        $manualitem_dis = explode(",", $manualitemdis);
        $addon_subtotal = explode(",", $addonsubtotal);
        $recipe = array();
        $amt = '';
        if ($reciepe_ids) {
            $disamt = 0;
            $variant_id = '';
            foreach ($reciepe_ids as $key => $recipe_id) {
                $recipeDetails = $this->pos_model->getrecipeByID($recipe_id);
                $discount = $this->site->discountMultiple($recipe_id);
                $current_qty = $reciepe_qtys[$key];
                $variant_id = $recipeva_riantids[$key];
                $manual_item_dis = $manualitem_dis[$key];
                $addon_sub_total = $addon_subtotal[$key];
                $recipe_Variant_Details = $this->pos_model->getrecipeVarient($recipe_id, $variant_id);
                if (!empty($recipe_Variant_Details)) {
                     $price_total = (($recipe_Variant_Details->price * $current_qty) + $addon_sub_total);
                     $price_total = $price_total - $manual_item_dis;
                } else {
                     $price_total = (($recipeDetails->price * $current_qty) + $addon_sub_total);
                     $price_total = $price_total - $manual_item_dis;
                }
                // $price_total = $recipeDetails->cost;

               $finalAmt = $price_total;
			  
                /*var_dump($finalAmt);*/
                $dis = 0;
                if (!empty($discount)) {
                    if ($discount[2] == 'percentage_discount') {
                        $discount_value = $discount[1] . '%';
                    } else {
                        $discount_value = $discount[1];
                    }
                    $dis = $this->site->calculateDiscount($discount_value, $price_total);
                    $finalAmt = $price_total - $dis;
                }
                /********* offer discount *****************/
                $TotalDiscount = $this->site->TotalDiscount();
                $offer_dis = 0;
                if (!empty($TotalDiscount) && $TotalDiscount[0] != 0) {
                    if ($TotalDiscount[3] == 'percentage_discount') {
                        $totdiscount = $TotalDiscount[1] . '%';
                    } else {
                        $totdiscount = $TotalDiscount[1];
                    }
                    $offerdiscount = $this->site->calculateDiscount($totdiscount, $finalAmt);
                    $offer_dis = $offerdiscount;
                    $finalAmt = $finalAmt - $offer_dis;
                }
                /****************          ***************/

                /*************** Customer Discount Apply ****************/
                if (!empty($discountid)) {
                    $request_discount = array(
                        'customer_id' => $customer_id,
                        'waiter_id' => $this->session->userdata('user_id') ? $this->session->userdata('user_id') : 0,
                        'table_id' => $table_id,
                        'split_id' => $split_id,
                        'customer_type_val' => 'customer',
                        'customer_discount_val' => $discountid,
                        'created_on' => date('Y-m-d H:i:s'),
                    );
                    $this->site->customerRequest($request_discount, $split_id);
                }

                /**************  Customer Discount Apply   ***************/
                $recipe[$key]['id'] = $recipe_id . $variant_id;
                $subgroup_id = $recipeDetails->subcategory_id;
                $finalAmt = $finalAmt;
                $discount = $this->pos_model->getCategory_GroupDiscount($recipeDetails->category_id, $subgroup_id, $recipe_id, $discountid);
                $recipe[$key]['discount_val'] = $discount['discount_val'];
                $recipe[$key]['disamt'] = $this->pos_model->recipe_customer_discount_calculation($recipe_id, $recipeDetails->category_id, $subgroup_id, $finalAmt, $discountid, $discounttype);
                $amt = $recipe;
            }
        }
        /*echo "<pre>";
        print_r($amt);die;*/
        // echo json_encode(array('amt'=>$amt));exit;
        echo json_encode($amt);exit;
    }
	 public function cancel_order_items($cancel_remarks = null, $order_item_id = null, $split_id = null){
        /*echo "<pre>";
        print_r($this->input->get());die;*/
        $cancel_remarks = $this->input->get('cancel_remarks');
        $order_item_id = $this->input->get('order_item_id');
        $split_id = $this->input->get('split_id');
        $cancelQty = $this->input->get('cancelqty'); //if 0    cancel all qty of tis item
        // var_dump($cancelQty);die;
        $cancel_type = $this->input->get('cancel_type');
        $item_data = $this->site->getOrderItem($order_item_id);
        $customer_id = $this->site->getOrderItemCustomer($order_item_id);
        if (!empty($split_id)) {
            $notification_msg = 'The item has been cancel to waiter';
            $type = 'Waiter Cancel';
            $notification_customer = 'The ' . $item_data->recipe_name . ' has been cancel to waiter';
        } else {
            $type = 'Chef Cancel';
            $notification_msg = 'The item has been cancel to chef';
            $notification_customer = 'The ' . $item_data->recipe_name . ' has been cancel to chef';
        }
        $notification_array['from_role'] = $this->session->userdata('group_id');
        $notification_array['customer_role'] = $this->session->userdata('group_id');
        $notification_array['customer_msg'] = $notification_customer;
        $notification_array['customer_type'] = $type;
        $notification_array['customer_id'] = $customer_id;
        $notification_array['insert_array'] = array(
            'msg' => $notification_msg,
            'type' => $type,
            'table_id' => 0,
            'user_id' => $this->session->userdata('user_id'),
            'warehouse_id' => $this->session->userdata('warehouse_id'),
            'created_on' => date('Y-m-d H:m:s'),
            'is_read' => 0,
        );
        $timelog_array = array(
            'status' => 'Cancel',
            'created_on' => date('Y-m-d H:m:s'),
            'item_id' => $order_item_id,
            'user_id' => $this->session->userdata('user_id'),
            'warehouse_id' => $this->session->userdata('warehouse_id'),
        );
        $sale  = $this->pos_model->CancelOrdersItem($notification_array, $cancel_remarks, $order_item_id, $this->session->userdata('user_id'), $split_id, $timelog_array, $cancelQty, $cancel_type);
        if ($sale) {
			 $kot_print_data['kot_print_option'] = $this->pos_settings->kot_print_option;
                $kot_print_data['con_kot_print_option'] = $this->pos_settings->consolidated_kot_print;
                $kot_print_data['kot_area_print'] = $sale['kitchen_data'];

                if ($this->pos_settings->consolidated_kot_print != 0) {
                    $kot_print_data['kot_con_print'] = $sale['consolid_kitchen_data'];
                    $kot_print_data['consolidate_kitchens_kot'] = $sale['consolidate_kitchens_kot'];
                }
              
                $kot_print_lang_option = $this->pos_settings->kot_print_lang_option;
                if ($this->pos_settings->kot_enable_disable == 1) {
                    $this->cancelItem_send_to_kot_print($kot_print_data);
                }
                $msg = 'success';

        } else {
            $msg = 'error';
            $status = 'error';
        }
        $this->sma->send_json(array('msg' => $msg, 'status' => $msg));
        /*$this->sma->send_json(array('status' => $msg));*/

    }
	function split_list($table_id = NULL){
		 $this->data['order_type']=$order_type=!empty($this->input->get('type'))?$this->input->get('type'):1;
		 $this->data['table_id']=!empty($this->input->post('table_id'))?$this->input->post('table_id'):$table_id;
		 $this->data['steward_id']=!empty($this->input->post('steward_id'))?$this->input->post('steward_id'):$table_id;
		 $this->data['tables']=$this->pos_model->ordered_table();
		 $this->data['steward']=$this->pos_model->ordered_steward();
		 $this->load->view($this->theme . 'pos_v2/split_list', $this->data);
	}
	function invoice_list($table_id = NULL){
		$this->data['order_type']=$order_type=!empty($this->input->get('type'))?$this->input->get('type'):1;
		$new_customer = $this->input->get('customer');
        $sales_type_id = !empty($this->input->get('type'))?$this->input->get('type'):1;
        $this->data['type'] = $this->input->get('type');
		$this->data['tables']=$this->pos_model->billing_table();
		$this->data['steward']=$this->pos_model->ordered_steward();
		$steward = !empty($this->input->post('steward'))?$this->input->post('steward'):'';
		$table_id = !empty($this->input->post('table_id'))?$this->input->post('table_id'):$table_id;
        if ($sales_type_id == 1) {
            $this->data['sales_type'] = 'Dine In';
        } elseif ($sales_type_id == 2) {
            $this->data['sales_type'] = 'Take Away';
        } elseif ($sales_type_id == 3) {
            $this->data['sales_type'] = 'Door Delivery';
        }
        $this->data['new_customer'] = $new_customer;
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['sales_types'] = $this->site->getAllSalestype();
        $this->data['billers'] = $this->site->getAllCompanies('biller');
        /*$this->data['get_order_type'] = $order;*/
        $this->data['warehouse_id'] = $this->session->userdata('warehouse_id');
        $this->data['warehouse'] = $this->session->userdata('warehouse_id') ? $this->site->getWarehouseByID($this->session->userdata('warehouse_id')) : null;
        $this->data['sid'] = $this->input->get('suspend_id') ? $this->input->get('suspend_id') : $sid;
        $order_printers = json_decode($this->pos_settings->order_printers);
        $printers = array();
        if (!empty($order_printers)) {
            foreach ($order_printers as $printer_id) {
                $printers[] = $this->pos_model->getPrinterByID($printer_id);
            }
        }
        $this->data['order_printers'] = $printers;
        if ($sales_type_id) {
            $this->data['sales'] = $this->pos_model->getAllSalesWithbiller($sales_type_id,$table_id,$steward);
        }
		$this->load->view($this->theme . 'pos_v2/invoice_list', $this->data);
	}
	function reprint($ordertype= null){
		 $start = $this->input->post('from_date');
         if ($start) {
            $start = $start;
         } else {
            $start = date('Y-m-d');
         }
		 if($this->pos_model->check_bill_exists($start)){
			 $this->data['sales'] = $this->pos_model->getAllBillingforReprint($start);
			 //not archival data so 1
			 $this->data['reprint_type']=1;
		 }else{
			  $this->data['sales'] = $this->pos_model->getAllBillingforReprint_archival($start);
			   //archival data so  2
			  $this->data['reprint_type']=2;
		 }
		 
		
		 $this->data['date']=$start;
		 $this->load->view($this->theme . 'pos_v2/reprint_list', $this->data);
	}
	  public function tablecheck($order_type = null, $table_id = null){
        $order_type = $this->input->get('order_type');
        $table_id = $this->input->get('table_id');
        $bbqcount = $this->site->getBBQmenuListCount();
        $table = $this->pos_model->checkTables($table_id, $order_type);
        if ($bbqcount <= 1) {
            $menuprice = $this->site->getbbqmenucoverprice();
            $table['adult_price'] = $menuprice->adult_price;
            $table['child_price'] = $menuprice->child_price;
            $table['kids_price'] = $menuprice->kids_price;
            $table['bbq_menu_id'] = $menuprice->bbq_menu_id;
        }
        $this->sma->send_json($table);
    }
	public function getrecipeVarientDataByCode($code = null, $warehouse_id = null){
        $this->sma->checkPermissions('index');
        if ($this->input->get('code')) {
            $code = $this->input->get('code', true);
        }
        if ($this->input->get('warehouse_id')) {
            $warehouse_id = $this->input->get('warehouse_id', true);
        }
        if ($this->input->get('customer_id')) {
            $customer_id = $this->input->get('customer_id', true);
        }
        $variant = $this->input->get('variant', true);
        if (!$code) {
            echo null;
            die();
        }
        $warehouse = $this->site->getWarehouseByID($warehouse_id);
        $customer = $this->site->getCompanyByID($customer_id);
        $customer_group = $this->site->getCustomerGroupByID($customer->customer_group_id);
        /*$discount_recipe = $this->site->getDiscounts($code);*/
        $row = $this->pos_model->getWHrecipe($code, $warehouse_id);
        /*echo "<pre>";
        print_r($row);die;*/
        $option = false;
        if ($row) {
            unset($row->cost, $row->details, $row->recipe_details, $row->image, $row->barcode_symbology, $row->cf1, $row->cf2, $row->cf3, $row->cf4, $row->cf5, $row->cf6, $row->supplier1price, $row->supplier2price, $row->cfsupplier3price, $row->supplier4price, $row->supplier5price, $row->supplier1, $row->supplier2, $row->supplier3, $row->supplier4, $row->supplier5, $row->supplier1_part_no, $row->supplier2_part_no, $row->supplier3_part_no, $row->supplier4_part_no, $row->supplier5_part_no);
            $row->item_tax_method = $row->tax_method;
            $row->qty = 1;
            $row->discount = '0';
            $row->serial = '';
            $options = $this->pos_model->getrecipeOptions($row->id, $warehouse_id);
            if (!empty($variant)) {
                $addons = $this->pos_model->getrecipeVariantAddons($variant, $row->id);
                $customizable = $this->pos_model->getrecipeVariantCustomizable($variant, $row->id);
            } else {
                $addons = $this->pos_model->getrecipeAddons($row->id);
                $customizable = $this->pos_model->getrecipeCustomizable($row->id);
            }
            if ($options) {
                $opt = current($options);
                if (!$option) {
                    $option = $opt->id;
                }
            } else {
                $opt = json_decode('{}');
                $opt->price = 0;
            }
            $row->option = $option;
            if ($addons) {
                $aon = current($addons);
                if (!$option) {
                    $option = $aon->id;
                }
            } else {
                $aon = json_decode('{}');
                $aon->price = 0;
            }
            $row->addon = !empty($addon) ? $addon : null;
            $buy = $this->site->checkBuyget($row->id);
			
            if (!empty($buy)) {
                $row->buy_id = $buy->id;
                $row->get_item = $buy->get_item;
                $row->buy_quantity =$x_quantity= $buy->buy_quantity;
                $row->get_quantity =$y_quantity= $buy->get_quantity;
                $total_quantity = $x_quantity % $y_quantity;
                $x_quantity = ($x_quantity - $total_quantity) / $y_quantity;
                $total_get_quantity = $x_quantity * $b_quantity;
                $row->total_get_quantity = $total_get_quantity;
                $row->free_recipe = $buy->free_recipe;
			    $row->get_variant_name= !empty($buy->variant_Name)?$buy->variant_Name:'';
				$row->get_variant_id=$buy->get_variant_id;
            } else {
                 $row->buy_id = 0;
                $row->get_item = 0;
                $row->buy_quantity = 0;
                $row->get_quantity = 0;
                $row->total_get_quantity = 0;
                $row->free_recipe = '';
				$row->get_variant_name='';
				$row->get_variant_id='';
            }
            $row->quantity = 0;
            $pis = $this->site->getPurchasedItems($row->id, $warehouse_id, $row->option);
            if ($pis) {
                foreach ($pis as $pi) {
                    $row->quantity += $pi->quantity_balance;
                }
            }
            if ($row->type == 'standard' && (!$this->Settings->overselling && $row->quantity < 1)) {
                echo null;die();
            }
            if ($options) {
                $option_quantity = 0;
                foreach ($options as $option) {
                    $pis = $this->site->getPurchasedItems($row->id, $warehouse_id, $row->option);
                    if ($pis) {
                        foreach ($pis as $pi) {
                            $option_quantity += $pi->quantity_balance;
                        }
                    }
                    if ($option->quantity > $option_quantity) {
                        $option->quantity = $option_quantity;
                    }
                }
            }
            if ($row->promotion) {
                $row->price = $row->promo_price;
            } elseif ($customer->price_group_id) {
                if ($pr_group_price = $this->site->getrecipeGroupPrice($row->id, $customer->price_group_id)) {
                    $row->price = $pr_group_price->price;
                }
            } elseif ($warehouse->price_group_id) {
                if ($pr_group_price = $this->site->getrecipeGroupPrice($row->id, $warehouse->price_group_id)) {
                    $row->price = $pr_group_price->price;
                }
            }
            $variant_id = '';
            $variantData = $this->pos_model->getVariantData($variant, $row->id);
            $row->price = $variantData->price; //$row->price;
            $row->variant = $variantData->name;
            $row->variant_khmer_name = $variantData->native_name;
            $row->variant_id = $variantData->attr_id;
            $row->real_unit_price = $row->price;
            $row->base_quantity = 1;
            $row->base_unit = $row->price;
            $row->base_unit_price = $row->price;
            $row->unit = $row->sale_unit ? $row->sale_unit : $row->unit;
            $row->comment = '';
            $combo_items = false;
            if ($row->type == 'combo') {
                $combo_items = $this->pos_model->getrecipeComboItems($row->id, $warehouse_id);
            }
            if (!empty($variantData->attr_id)) {
                $variant_id = $variantData->attr_id;
            }
            $units = $this->site->getUnitsByBUID($row->base_unit);
            $tax_rate = $this->site->getTaxRateByID($row->tax_rate);
            $pr = array('id' => str_replace(".", "", microtime(true)), 'item_id' => $row->id . $variant_id, 'label' => $row->name . " (" . $row->code . ")", 'category' => $row->category_id, 'row' => $row, 'combo_items' => $combo_items, 'tax_rate' => $tax_rate, 'units' => $units, 'options' => $options, 'addons' => $addons, 'customizable_ingrediends' => $customizable);
            $this->sma->send_json($pr);
        } else {
            echo null;
        }
    }
	public function getrecipeDataByCode_all($code = null, $warehouse_id = null){
        $this->sma->checkPermissions('index');
        if ($this->input->get('code')) {
            $code = $this->input->get('code', true);
        }
        if ($this->input->get('warehouse_id')) {
            $warehouse_id = $this->input->get('warehouse_id', true);
        }
        if ($this->input->get('customer_id')) {
            $customer_id = $this->input->get('customer_id', true);
        }
        if (!$code) {
            echo null;
            die();
        }

        $warehouse = $this->site->getWarehouseByID($warehouse_id);
        $customer = $this->site->getCompanyByID($customer_id);
        $customer_group = $this->site->getCustomerGroupByID($customer->customer_group_id);
        /*$discount_recipe = $this->site->getDiscounts($code);*/
        $row = $this->pos_model->getWHrecipebyid($code, $warehouse_id);
        $option = false;

        /*$check_stock_ava_qty = $this->pos_model->checkStockavaQTY($row->id, $row->type);
        if($check_stock_ava_qty == 0){
        echo NULL;
        die;
        }*/

        if ($row) {
            unset($row->cost, $row->details, $row->recipe_details, $row->image, $row->barcode_symbology, $row->cf1, $row->cf2, $row->cf3, $row->cf4, $row->cf5, $row->cf6, $row->supplier1price, $row->supplier2price, $row->cfsupplier3price, $row->supplier4price, $row->supplier5price, $row->supplier1, $row->supplier2, $row->supplier3, $row->supplier4, $row->supplier5, $row->supplier1_part_no, $row->supplier2_part_no, $row->supplier3_part_no, $row->supplier4_part_no, $row->supplier5_part_no);
            $row->item_tax_method = $row->tax_method;
            $row->qty = 1;
            $row->discount = '0';
            $row->serial = '';
            $options = $this->pos_model->getrecipeOptions($row->id, $warehouse_id);
            $addons = $this->pos_model->getrecipeAddons($row->id);
            $customizable = $this->pos_model->getrecipeCustomizable($row->id);
            /*echo "<pre>";
            print_r($addons);die;*/
            if ($options) {
                $opt = current($options);
                if (!$option) {
                    $option = $opt->id;
                }
            } else {
                $opt = json_decode('{}');
                $opt->price = 0;
            }
            $row->option = $option;

            if ($addons) {
                $aon = current($addons);
                if (!$option) {
                    $option = $aon->id;
                }
            } else {
                $aon = json_decode('{}');
                $aon->price = 0;
            }
            $row->addon = !empty($addon) ? $addon : null;
            $buy = $this->site->checkBuyget($row->id);
			
            if (!empty($buy)) {
                $row->buy_id = $buy->id;
                $row->get_item = $buy->get_item;
				$x_quantity=$buy->buy_quantity;
				$y_quantity=$buy->get_quantity;
                $row->buy_quantity = $buy->buy_quantity;
                $row->get_quantity = $buy->get_quantity;
                $total_quantity = $x_quantity % $y_quantity;
                $x_quantity = ($x_quantity - $total_quantity) / $y_quantity;
                $total_get_quantity = $x_quantity * $b_quantity;
                $row->total_get_quantity = $total_get_quantity;
			    $row->free_recipe = $buy->free_recipe ;
				$row->get_variant_name= !empty($buy->variant_Name)?$buy->variant_Name:'';
				$row->get_variant_id=$buy->get_variant_id;
            } else {
                $row->buy_id = 0;
                $row->get_item = 0;
                $row->buy_quantity = 0;
                $row->get_quantity = 0;
                $row->total_get_quantity = 0;
                $row->free_recipe = '';
				$row->get_variant_name='';
				$row->get_variant_id='';
            }

            $row->quantity = 0;
            $pis = $this->site->getPurchasedItems($row->id, $warehouse_id, $row->option);
            if ($pis) {
                foreach ($pis as $pi) {
                    $row->quantity += $pi->quantity_balance;
                }
            }
            if ($row->type == 'standard' && (!$this->Settings->overselling && $row->quantity < 1)) {
                echo null;die();
            }
            if ($options) {
                $option_quantity = 0;
                foreach ($options as $option) {
                    $pis = $this->site->getPurchasedItems($row->id, $warehouse_id, $row->option);
                    if ($pis) {
                        foreach ($pis as $pi) {
                            $option_quantity += $pi->quantity_balance;
                        }
                    }
                    if ($option->quantity > $option_quantity) {
                        $option->quantity = $option_quantity;
                    }
                }
            }
            if ($row->promotion) {
                $row->price = $row->promo_price;
            } elseif ($customer->price_group_id) {
                if ($pr_group_price = $this->site->getrecipeGroupPrice($row->id, $customer->price_group_id)) {
                    $row->price = $pr_group_price->price;
                }
            } elseif ($warehouse->price_group_id) {
                if ($pr_group_price = $this->site->getrecipeGroupPrice($row->id, $warehouse->price_group_id)) {
                    $row->price = $pr_group_price->price;
                }
            }
            $row->price = $row->price;
            $row->real_unit_price = $row->price;
            $row->base_quantity = 1;
            $row->base_unit = $row->price;
            $row->base_unit_price = $row->price;
            $row->unit = $row->sale_unit ? $row->sale_unit : $row->unit;
            $row->comment = '';
            $combo_items = false;
            if ($row->type == 'combo') {
                $combo_items = $this->pos_model->getrecipeComboItems($row->id, $warehouse_id);

            }
            $units = $this->site->getUnitsByBUID($row->base_unit);
            $tax_rate = $this->site->getTaxRateByID($row->tax_rate);

            $pr = array('id' => str_replace(".", "", microtime(true)), 'item_id' => $row->id, 'stock_ava_qty' => $check_stock_ava_qty ? $check_stock_ava_qty : 0, 'label' => $row->name . " (" . $row->code . ")", 'category' => $row->category_id, 'row' => $row, 'combo_items' => $combo_items, 'tax_rate' => $tax_rate, 'units' => $units, 'options' => $options, 'addons' => $addons, 'customizable_ingrediends' => $customizable);

            $this->sma->send_json($pr);
        } else {
            echo null;
        }
    }
	public function report(){
		    $this->data['report']='report';
	     	$this->load->view($this->theme . 'pos_v2/report', $this->data);
	}
  public function reports(){
           if (($this->pos_settings->taxation_report_settings == 1) && ($this->pos_report_view_access == 0)) {
             $this->load->view($this->theme . 'pos_v2/reports_passcode', $this->data);
            } else {
            $reports_type = $this->input->get('type');
            $start = $this->input->get('fromdate');
            $end = $this->input->get('todate');
            if (isset($start) == true) {
                $start = date("Y-m-d", strtotime($start));
            } else {
                $start = date('Y-m-d');
            }
            if (isset($end) == true) {
                $end = date('Y-m-d', strtotime($end));
            } else {
                $end = date('Y-m-d');
            }
            $dates = array(
                'fromdate' => $start,
                'todate' => $end,
            );
            $type = !empty($this->input->get('type')) ? $this->input->get('type') : '';
            $date = date('Y-m-d');
            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $this->data['sales_types'] = $this->site->getAllSalestype();
            $this->data['billers'] = $this->site->getAllCompanies('biller');
            $category_id = $this->input->get('category_id');
            $subcategory_id = $this->input->get('subcategory_id');
            if (!empty($category_id)) {
                $group = $category_id;
            } else {
                $group = "";
            }
            if (!empty($subcategory_id)) {
                $subgroup = $subcategory_id;
            } else {
                $subgroup = "";
            }

         /*   var_dump($category_id)."<br>";
            var_dump($subcategory_id);exit;
            /*echo "<pre>";
            print_r($_GET);exit;*/
            if ($reports_type == 1) {
                $this->data['categories'] = $this->site->getAllrecipeCategories();
                $this->data['sub_categories'] = $this->site->getAllrecipe_subCategories();
                $this->data['recipes'] = $this->pos_model->getItemSaleReports($start, $end, $this->pos_report_view_access, $this->pos_report_show, $group, $subgroup);
				$this->data['round'] = $this->pos_model->getRoundamount($start, $end);
				if($this->Settings->archival_report){
					$data1=$this->pos_model->getItemSaleReports_archival($start, $end, $this->pos_report_view_access, $this->pos_report_show, $group, $subgroup);
					$data2 = $this->pos_model->getRoundamount_archival($start, $end);
					foreach($data1 as $row){
				    $this->data['recipes'][]=$row;
					
	     		}
				foreach($data2 as $row){
				     $this->data['round'][]=$row;
	     		}
				}
              
                $this->load->view($this->theme . '/pos_v2/reports//item_reports', $this->data);

            } elseif ($reports_type == 2) {
                $vale = $this->settings->default_currency;
                $this->data['row'] = $this->pos_model->getdaysummary($start, $end, $this->pos_report_view_access, $this->pos_report_show);
                // $this->data['collection'] = $this->pos_model->getCollection($start,$end,$this->pos_report_view_access,$this->pos_report_show);
                $this->data['tendersales'] =$tender_data= $this->pos_model->getTendertypes($start, $end, $this->pos_report_view_access, $this->pos_report_show);
                // echo "<pre>";/* 
                //print_r($this->data['tendersales']);die; //
				if($this->Settings->archival_report){
					$data = $this->pos_model->getdaysummary_archival($start, $end, $this->pos_report_view_access, $this->pos_report_show);
					$data2 = $this->pos_model->getTendertypes_archival($start, $end, $this->pos_report_view_access, $this->pos_report_show);
				  // $this->data['row'][]=$data;
				$this->data['row']->totalbill +=!empty($data->totalbill)?$data->totalbill:0;
	     	    $this->data['row']->total +=!empty($data->total)?$data->total:0;
				$this->data['row']->total_amount1 +=!empty($data->total_amount1)?$data->total_amount1:0;
				$this->data['row']->total_tax +=!empty($data->total_tax)?$data->total_tax:0;
				$this->data['row']->service_charge_amount +=!empty($data->service_charge_amount)?$data->service_charge_amount:0;
				$this->data['row']->total_discount +=!empty($data->total_discount)?$data->total_discount:0;
				$this->data['row']->total_amount +=!empty($data->total_amount)?$data->total_amount:0;
				$this->data['row']->net_amt +=!empty($data->net_amt)?$data->net_amt:0;
				$this->data['row']->gross_amt +=!empty($data->gross_amt)?$data->gross_amt:0;
				$this->data['row']->netamt +=!empty($data->netamt)?$data->netamt:0;
				foreach($this->data['tendersales']['Tender_Type'] as $tt){
				foreach($data2['Tender_Type'] as $key => $tt2){
				if($tt2->tender_type == $tt->tender_type){
					$this->data['tendersales']['Tender_Type'][$key]->tender_type_total +=  $tt2->tender_type_total;
						}
					}
				}
				}
                $this->load->view($this->theme . '/pos_v2/reports//day_reports', $this->data);

            }elseif ($reports_type == 3) {
                $this->data['cashier'] = $this->pos_model->getCashierReport($start, $end, $this->pos_report_view_access, $this->pos_report_show);
				$data = $this->pos_model->getCashierReport_archival($start, $end, $this->pos_report_view_access, $this->pos_report_show);

				if($this->Settings->archival_report  && !empty($this->data['cashier'])){
										foreach($data as $row){
						foreach($this->data['cashier'] as $key => $row1){
							if($row1->id == $row->id){
								$this->data['cashier'][$key]->grand_total +=$row1->grand_total;
							}
						}
	     		}
				}else{
				$this->data['cashier']=$data;	
				}
                /*$this->data['collection'] = $this->pos_model->getCollection($start,$end);*/
                $this->data['dates'] = $dates;
                $this->load->view($this->theme . '/pos_v2/reports//cashier_reports', $this->data);

            } elseif ($reports_type == 4) {
                $this->data['settlement'] = $this->pos_model->getSettlementReport($start, $end, $this->pos_report_view_access, $this->pos_report_show);
			/* 	 print_r($this->data['settlement']);
				die;   */
				//payments
				//tender_type
				//sale_type
				//exchange_amt
				$data = $this->pos_model->getSettlementReport_archival($start, $end, $this->pos_report_view_access, $this->pos_report_show);
				if($this->Settings->archival_report && !empty($this->data['settlement'])){
					
					foreach($data['payments'] as $row){
						foreach($this->data['settlement']['payments'] as $key => $row1){
						$this->data['settlement']['payments'][$key]->total_transaction +=!empty($row->total_transaction)?$row->total_transaction:0;
						$this->data['settlement']['payments'][$key]->gross_total1 +=!empty($row->gross_total1)?$row->gross_total1:0;
						$this->data['settlement']['payments'][$key]->gross_total +=!empty($row->gross_total)?$row->gross_total:0;
						$this->data['settlement']['payments'][$key]->net_total1 +=!empty($row->net_total1)?$row->net_total1:0;
						$this->data['settlement']['payments'][$key]->net_total +=!empty($row->net_total)?$row->net_total:0;
	     		}
				}
				foreach($data['tender_type'] as $row){
						foreach($this->data['settlement']['tender_type'] as $key => $row1){
					    if($this->data['settlement']['tender_type'][$key]['tender_type'] == $row['tender_type']){
						 $this->data['settlement']['tender_type'][$key]['tender_type_total'] +=!empty($row['tender_type_total'])?$row['tender_type_total']:0;
						
	     		}
				}
				}
				//sale_type_total1  sale_type  sale_type_total
				
				foreach($data['sale_type'] as $row){
						foreach($this->data['settlement']['sale_type'] as $key => $row1){
					    if($this->data['settlement']['sale_type'][$key]->sale_type == $row->sale_type){
						 $this->data['settlement']['sale_type'][$key]->sale_type_total1 +=!empty($row->sale_type_total1)?$row->sale_type_total1:0;
						 $this->data['settlement']['sale_type'][$key]->sale_type_total +=!empty($row->sale_type_total)?$row->sale_type_total:0;
						
	     		}
				}
				}
				}else{
					$this->data['settlement']=$data;
					
				}
             //  print_r($this->data['settlement']);die;
                /*$this->data['collection'] = $this->pos_model->getCollection($start,$end);*/
                $this->data['dates'] = $dates;
                $this->load->view($this->theme . '/pos_v2/reports//settlement_reports', $this->data);

            }elseif ($reports_type == 5) {
                $this->data['shifttime'] = $this->pos_model->getshifttime();
                $shift_id = $this->input->get('shift_id') ? $this->input->get('shift_id') : 0;
                $this->data['shiftreport'] = $this->pos_model->getShiftWiseReport($start, $end, $shift_id, $this->pos_report_view_access, $this->pos_report_show);
				if($this->Settings->archival_report){
					$data =  $this->pos_model->getShiftWiseReport_archival($start, $end, $shift_id, $this->pos_report_view_access, $this->pos_report_show);
					foreach($data as $row){
				    $this->data['shiftreport'][]=$row;
	     		}
				}
                $this->data['dates'] = $dates;
                $this->load->view($this->theme . '/pos_v2/reports//shift_report', $this->data);
            } else {
                $this->data['recipes'] = $this->pos_model->getItemSaleReports($start, $end, $this->pos_report_view_access, $this->pos_report_show);
                $this->load->view($this->theme . '/pos_v2/reports/item_reports', $this->data);

            }
        }
    }
	public function change_customer_number($cancel_remarks = null, $sale_id = null){
        $change_split_id = $this->input->post('change_split_id');
        $changed_customer_id = $this->input->post('changed_customer_id');
        $result = $this->pos_model->change_customer($change_split_id, $changed_customer_id);
        if ($result == true) {
            $msg = 'success';
        } else {
            $msg = 'error';
            $status = 'error';
        }
        $this->sma->send_json(array('msg' => $msg, 'status' => $msg));
    }
  public function billprint(){
        $data = $this->input->post();
        /*echo '<pre>';
        print_r($data);die;*/
        $split_id = $this->input->post('splits');
        $order_discount_input_seletedtext = $this->input->post('order_discount_input_seletedtext');
        $manual_discount_amount = $this->input->post('manual_discount_amount');
        for ($i = 1; $i <= $this->input->post('bils'); $i++) {
            foreach ($this->input->post('split[' . $i . '][recipe_name]') as $k => $row) {
                $bill_items['item'][$k]['recipe_id'] = $this->input->post('split[' . $i . '][recipe_id][' . $k . ']');
                $bill_items['item'][$k]['recipe_name'] = $this->input->post('split[' . $i . '][recipe_name][' . $k . ']');
                $bill_items['item'][$k]['recipe_native_name'] = $this->site->getrecipeKhmer($this->input->post('split[' . $i . '][recipe_id][' . $k . ']'));
                $bill_items['item'][$k]['order_item_id'] = $this->input->post('split[' . $i . '][order_item_id][' . $k . ']');
                $bill_items['item'][$k]['variant_native_name'] = $this->site->getrecipevariantKhmer($this->input->post('split[' . $i . '][recipe_variant_id][' . $k . ']'));
                $bill_items['item'][$k]['recipe_variant'] = $this->input->post('split[' . $i . '][recipe_variant][' . $k . ']');
                $bill_items['item'][$k]['recipe_price'] = $this->input->post('split[' . $i . '][unit_price][' . $k . ']');
                $bill_items['item'][$k]['recipe_qty'] = $this->input->post('split[' . $i . '][quantity][' . $k . ']');
                $bill_items['item'][$k]['recipe_subtotal'] = $this->input->post('split[' . $i . '][subtotal][' . $k . ']');
                $bill_items['item'][$k]['manual_item_discount'] = $this->input->post('split[' . $i . '][manual_item_discount][' . $k . ']');
                $bill_items['item'][$k]['manual_item_discount_val'] = $this->input->post('split[' . $i . '][manual_item_discount_val][' . $k . ']');
                $bill_items['item'][$k]['manual_item_discount_per_val'] = $this->input->post('split[' . $i . '][manual_item_discount_per_val][' . $k . ']');
                $bill_items['item'][$k]['item_cus_dis_val'] = $this->input->post('split[' . $i . '][item_cus_dis_val][' . $k . ']');
                $bill_items['item'][$k]['item_cus_dis'] = $this->input->post('split[' . $i . '][item_cus_dis][' . $k . ']');
                //$bill_items['item'][$k]['total_after_dis'] = $this->input->post('split['.$i.'][total_price]['.$k.']');
            }
            $bill_items['item_cnt'] = $this->input->post('split[' . $i . '][total_item]');
            $bill_items['total'] = $this->input->post('split[' . $i . '][total_price]');
            $bill_items['discount'] = $this->input->post('split[' . $i . '][discount_amount]') + $this->input->post('split[' . $i . '][itemdiscounts]');
            $bill_items['tax_type'] = $this->input->post('split[' . $i . '][tax_type]');
            $bill_items['service_amount'] = $this->input->post('split[' . $i . '][service_amount]') ? $this->input->post('split[' . $i . '][service_amount]') : 0;

            if ($bill_items['tax_type'] == 1) {
                $bill_items['grand_total'] = $this->input->post('split[' . $i . '][grand_total]');
            } else {
                $bill_items['grand_total'] = $this->input->post('split[' . $i . '][grand_total]');
            }

            $bill_items['biller_id'] = $this->input->post('split[' . $i . '][biller_id]');

            $bill_items['tax_type'] = $this->input->post('split[' . $i . '][tax_type]');
            if ($bill_items['tax_type'] == 0) {
                $taxtype = 'Tax Inclusive';
            } else if ($bill_items['tax_type'] == 1) {
                $taxtype = 'Tax Exclusive';
            }
            $tax_details = $this->site->getTaxRateByID($this->input->post('split[' . $i . '][ptax]'));
            $service_charge = $this->site->getServiceChargeByID($this->input->post('split[' . $i . '][service_charge]') ? $this->input->post('split[' . $i . '][service_charge]') : 0);
            $bill_items['tax_type'] = $taxtype . $tax_details->name;
            $bill_items['tax_rate'] = $tax_details->rate;
            $bill_items['tax_name'] = $tax_details->name;
            $bill_items['tax'] = $this->input->post('split[' . $i . '][tax_amount]');

            $bill_items['service_charge_name'] = $service_charge->display_value;

            $manual_discount_amount = $this->input->post('split[' . $i . '][manual_discount_amount]');
            $order_discount_input = $this->input->post('split[' . $i . '][order_discount_input]');
        }
        $bill_items['date'] = date('Y-m-d H:i:s');
        $this->db->
            select('r.name table,o.reference_no,c.name customer,u.first_name,o.seats_id,o.store_id,o.table_id')
            ->from('orders o')
            ->join('restaurant_tables r', 'o.table_id = r.id')
            ->join('companies c', 'c.id = o.customer_id')
            ->join('users u', 'u.id = o.created_by')
            ->where(array('o.split_id' => $split_id));

        $orders = $this->db->get()->row_array();
        $bill_items['table_name'] = $orders['table'];
        $bill_items['reference_no'] = $orders['reference_no'];
        $bill_items['customer_name'] = $orders['customer'];
        $bill_items['created_by'] = $orders['first_name'];
       
        $this->data['bill_items'] = $bill_items;
        // $this->data['discounnames'] = $this->pos_model->getBillDiscountNamesbysplitname($split_id);
		$this->data['seats_id']               = $orders['seats_id'];
        $this->data['discounnames']           = $order_discount_input_seletedtext;
        $this->data['order_discount_input']   = $order_discount_input;
        $this->data['manual_discount_amount'] = $manual_discount_amount;
        $this->data['splits']                 = $split_id;
	    $this->data['store']                  = $this->site->getWarehouseByID($orders['store_id']);
	    $this->data['tableno']                 = $this->site->getAreasByIDWithArea($orders['table_id']);
/* 
        if ($this->pos_settings->bill_print_format == 1) {
            $this->load->view($this->theme . 'pos/print_bill', $this->data, false);
        } elseif ($this->pos_settings->bill_print_format == 3) {
            $this->load->view($this->theme . 'pos/indai_bill/print_bill', $this->data, false);
        } elseif ($this->pos_settings->bill_print_format == 4) {
            $this->load->view($this->theme . 'pos/local_bill/print_bill', $this->data, false);
        } else {
            $this->load->view($this->theme . 'pos/row_discount/print_bill', $this->data, false);
        } */
		 $this->load->view($this->theme . 'pos_v2/print_bill', $this->data, false);
    }
	    public function reprint_view($type){
        $billid = $this->input->get('bill_id');
		$type   = $this->input->get('type');
		if($type ==1){
			$inv = $this->pos_model->getInvoiceByID($billid);
			$this->data['billi_tems'] = $this->pos_model->getAllBillitems($billid);
			$this->data['discounnames'] = $this->pos_model->getBillDiscountNames($billid);
			$this->data['payments'] = $this->pos_model->getInvoicePayments($billid);
			$this->data['return_sale'] = $inv->return_id ? $this->pos_model->getInvoiceByID($inv->return_id) : null;
		    $this->data['return_rows'] = $inv->return_id ? $this->pos_model->getAllInvoiceItems($inv->return_id) : null;
			$this->data['return_payments'] = $this->data['return_sale'] ? $this->pos_model->getInvoicePayments($this->data['return_sale']->id) : null;
			$tableno = $this->pos_model->getTableNumber($billid);
		}else{
			// get the data from archival table
			$inv = $this->pos_model->getInvoiceByID_archival($billid);
		    $this->data['billi_tems'] = $this->pos_model->getAllBillitems_archival($billid);
			$this->data['discounnames'] = $this->pos_model->getBillDiscountNames_archival($billid);
			$this->data['payments'] = $this->pos_model->getInvoicePayments_archival($billid);
			$this->data['return_sale'] = $inv->return_id ? $this->pos_model->getInvoiceByID_archival($inv->return_id) : null;
			$this->data['return_rows'] = $inv->return_id ? $this->pos_model->getAllInvoiceItems_archival($inv->return_id) : null;
		    $this->data['return_payments'] = $this->data['return_sale'] ? $this->pos_model->getInvoicePayments_archival($this->data['return_sale']->id) : null;
			$tableno = $this->pos_model->getTableNumber_archival($billid);
		}
        
        $this->data['message'] = $this->session->flashdata('message');
      
        $this->load->helper('pos');
        if (!$this->session->userdata('view_right')) {
            $this->sma->view_rights($inv->created_by, true);
        }
        /*$this->data['rows'] = $this->pos_model->getAllInvoiceItems($billid);*/
       
        $biller_id = $inv->biller_id;
        $bill_id = $inv->sales_id;
        $customer_id = $inv->customer_id;
        $delivery_person_id = $inv->delivery_person_id;
        $this->data['inv'] = $inv;
        $this->data['tableno'] = $tableno;
        $this->data['customer'] = $this->pos_model->getCompanyByID($customer_id);
        if ($delivery_person_id != 0) {
            $this->data['delivery_person'] = $this->pos_model->getUserByID($delivery_person_id);
        }
		$this->data['store'] = $this->site->getWarehouseByID($inv->store_id);
        $this->data['created_by'] = $this->site->getUser($inv->created_by);
        $this->data['cashier'] = $this->pos_model->getCashierInfo($billid);
        $this->data['printer'] = $this->pos_model->getPrinterByID($this->pos_settings->printer);
        $this->data['biller'] = $this->pos_model->getCompanyByID($biller_id);
        $this->data['type'] = $this->input->post('type');
        $this->site->send_to_bill_print($billid);
        $this->load->view($this->theme . 'pos_v2/reprint_viewbill', $this->data);
       
    }
	  public function report_view_access(){
        $pass_code = $this->input->post('pass_code');
        $data = $this->pos_model->check_reportview_access($pass_code);
        if ($data != 0) {
            $this->session->set_userdata('pos_report_view_access', $data);
            $this->sma->send_json($data);
        } else {
            $this->sma->send_json(0);
        }
    }
	
	public function get_categories(){
        if ($this->input->get('per_page') == 'n' || $this->input->get('per_page') == '0') {
            $page = 0;
        } else {
            $page = $this->input->get('per_page');
        }
        if ($this->input->get('order_type')) {
            $order_type = $this->input->get('order_type');
        }
        $this->load->library("pagination");
        $config = array();
        $config["base_url"] = base_url() . "pos/ajaxrecipe";
        $config["total_rows"] = $this->pos_model->get_count_category();
        $config["per_page"] =15;
        $config['prev_link'] = false;
        $config['next_link'] = false;
        $config['display_pages'] = false;
        $config['first_link'] = false;
        $config['last_link'] = false;
        $this->pagination->initialize($config);
        $categories = $this->pos_model->getAllrecipeCategories( $config["per_page"], $page);
		$cat_html='';
	    if($categories){
			$cat_html .='<tr><td><ul>';
                           foreach ($categories as $category) {
							 if($this->Settings->user_language == 'khmer'){
							 if(!empty($category->khmer_name)){
								 	        $category_name = $category->khmer_name;
							  }else{    	$category_name = $category->name; 
							  }
							  }else{
							                $category_name = $category->name;
							}	
							  $cat_html .= "<li>
												<div class='item'><button id=\"category-" . $category->id . "\" type=\"button\" value='" . $category->id . "' class=\"btn btn_default category\" ><span>" . $category_name . "</span></button></div>
											</li>";
							}
						$cat_html .="</ul></td></tr>";
		            }
		echo  $cat_html;
	}
	public function get_subcategories(){
        if ($this->input->get('per_page') == 'n' || $this->input->get('per_page') == '0') {
            $page = 0;
        } else {
            $page = $this->input->get('per_page');
        }
        if ($this->input->get('order_type')) {
            $order_type = $this->input->get('order_type');
        }
		$category_id = $this->input->get('category_id');
        $this->load->library("pagination");
        $config = array();
        $config["base_url"] = base_url() . "pos/ajaxrecipe";
        $config["total_rows"] = $this->pos_model->get_count_category();
        $config["per_page"] =15;
        $config['prev_link'] = false;
        $config['next_link'] = false;
        $config['display_pages'] = false;
        $config['first_link'] = false;
        $config['last_link'] = false;
        $this->pagination->initialize($config);
        $subcategories = $this->pos_model->getrecipeSubCategories($category_id, $config["per_page"], $page);
		$subhtml='';
	                          if($subcategories){
                                       foreach ($subcategories as $category) {
										  if($this->Settings->user_language == 'khmer'){
											if(!empty($category->khmer_name)){
												$subcategory_name = $category->khmer_name;
											}else{
												$subcategory_name = $category->name;
											}
									   	}else{
											$subcategory_name = $category->name;
										}
									    if($this->pos_settings->subcategory_display == 0){
                                   		 $subhtml = "<button id=\"subcategory-" . $category->id . "\" type=\"button\" value='" . $category->id . "' class=\"btn-prni btn_lightred subcategory slide\" >";
                                        }else{
                                            $subhtml = "<button id=\"subcategory-" . $category->id . "\" type=\"button\" value='" . $category->id . "' class=\"btn-img subcategory slide\" >";
                                        } 
										 if(strlen($subcategory_name) < 20){		
											$subhtml .= "<span class='name_strong'>" .$subcategory_name. "</span>";
										}else{
											$subhtml .= "<marquee class='name_strong' behavior='alternate' direction='left' scrollamount='1'>&nbsp;&nbsp;" .$subcategory_name. "&nbsp;&nbsp;</marquee>";
										}
										  $subhtml .=  "</button>";
                                }
                            }
		           echo  $subhtml;
	}
	
	 

	function get_tables(){
		if ($this->input->get('per_page') == 'n' || $this->input->get('per_page') == '0') {
            $page = 0;
        } else {
            $page = $this->input->get('per_page');
        }
        if ($this->input->get('order_type')) {
            $order_type = $this->input->get('order_type');
        }
		if ($this->input->get('area_id')) {
            $area_id = $this->input->get('area_id');
        }
		if ($this->input->get('warehouse_id')) {
            $warehouse_id = $this->input->get('warehouse_id');
        }
	
		$category_id = $this->input->get('category_id');
        $this->load->library("pagination");
        $config = array();
        $config["base_url"] = base_url() . "pos/ajaxrecipe";
        $config["total_rows"] = $this->pos_model->get_area_tables_count($area_id,$warehouse_id);
        $config["per_page"] =35;
        $config['prev_link'] = false;
        $config['next_link'] = false;
        $config['display_pages'] = false;
        $config['first_link'] = false;
        $config['last_link'] = false;
        $this->pagination->initialize($config);
		$areas_Tables = $this->pos_model->get_area_tables($area_id,$warehouse_id,$config["per_page"],$page);
		$subhtml='';
	     if($areas_Tables){
			 	$table_data='';
			foreach($areas_Tables as $tables){
				 $table_status[]=$tables->current_order_status;
								   $table_class='';
							       $link='';
								    switch($tables->current_order_status){
									case 0:
									$table_class='btn_violet';
									$link=base_url('pos/pos/order/?type=1&table='.$tables->id);
									break;
									case 1:
									$table_class='btn_orange';
									$link=base_url('pos/pos/payment/?type=1&table='.$tables->id);
									break;
									case 4:
									$table_class='btn_gray';
									$link=base_url('pos/pos/payment/?type=1&table='.$tables->id);
									break;
								}
								$avl_order=$this->site->avl_order($tables->id);
								$avl_bill=$this->site->avl_bill($tables->table_id);								
								$order_count=count($avl_order); 
								//clock part
							 $current_time = date('Y-m-d H:i:s');
						      $created_time = $tables->last_order_placed_time;
						     // $diff = strtotime($current_time) -  strtotime($created_time);
						     $diff1 = (strtotime($current_time) -  strtotime($created_time));
						     $limit_time = $this->Settings->default_preparation_time;
						     if($diff >= $limit_time){
						     $diff = 0; 
					         }else{
						      $diff = $limit_time - $diff; 
					          } 
							  /// clock part end
					         $table=''; $main_class='';
			                     	$table_data.='<div class="btn btn-default '.$table_class.' '.$main_class.' '.$table.' table_id ">
								<a href="'.$link.'">
									<button type="button" value="'.$tables->id.'">
											<span class="number_s">'.$tables->name.'</span>';
											if($tables->current_order_status ==1){
	                                    $table_data.='  <script>            
					                    $(document).ready(function () {
					                     var clock;
					                     clock = $(".clock_'.$tables->id.'").FlipClock('.$diff1.',{  
						                 clockFace: "HourlyCounter", 
						                 autoStart: true,
				     	                  }); 
				                         });
			           </script>';
								$table_data.='	<span class="clock_'.$tables->id.' flip-clock-wrapper" start_time="'.$tables->last_order_placed_time.'">';
                    }
				  $hide_menu="hide('setting_table_".$tables->id."')";
				  $active_new_split=($tables->current_order_status ==0 || $tables->current_order_status==1)?"":"class='disable_list'";
				  $new_order_item=!empty($avl_order)?"":"class='disable_list'";
				  $invoice=!empty($avl_order)?"":"class='disable_list'";
				  $payment=!empty($avl_bill)?"":"class='disable_list'";
				  $change_table= !($order_count ==1 && $tables->current_order_status !=0)?"":"disable_list";
				  $change_customer= ($order_count ==1 && $tables->current_order_status !=0)?"":"disable_list";
				  $copy_kot=($order_count ==1 && $tables->current_order_status !=0)?"k":"disable_list";
				  $cancel_all=($order_count ==1 && $tables->current_order_status !=0)?"":"disable_list";
				  $new_split_gen=($order_count ==1 && $tables->current_order_status !=0)?"":"disable_list";
		          $table_data.='</button></a><label class="setting" data-title="setting_table_'.$tables->id.'"><i class="fa fa-cog" aria-hidden="true"></i></label>
	              <div id="setting_table_'.$tables->id.'" class="setting_list_menu" style="display:none">
			       <a href="javascript:void(0);" onclick="'.$hide_menu.'"><i class="fa fa-times" aria-hidden="true"></i></a>
			       <ul>
			       <li><a '.$active_new_split.' href="'.base_url('pos/pos/order/?type=1&table='.$tables->id).'">1.New Split/បំបែកថ្មី</a></li>
			       <li><a '.$new_order_item.' href="'.base_url('pos/pos/order/?type=1&table='.$tables->id.'&split='.$avl_order->split_id.'&same_customer='.$avl_order->customer_id).'">2.New Order Item/កម៉្មងម្ហូបថ្មី</a></li>
				   
			       <li><a '.$invoice.' href="'.base_url('pos/pos/payment/?type=1&table='.$tables->id.'&req=Invoice').'">3.Invoice/វិក័យបត្រ័</a></li>
			       <li class="'.$payment.'"><a href="'. base_url('pos/pos/payment/?type=1&table='.$tables->table_id.'&req=Payment').'">4.Payment/ទូទាត់</a></li>
				   
			       <li class="merge_bill " table_id="'.$tables->id.'" data-split_id="'.$avl_order->split_id.'"><a href="#">5.Merge Table/បញ្ចូលទុក</a></li>
			       <li class="change_table '.$change_table.'" data-split_id="'.$avl_order->split_id.'"><a href="javascript:void(0)">6.Change Table/ប្ដូរតុ</a></li>
				   
		           <li class="change_customer '.$change_customer.'   " data-split_id="'.$avl_order->split_id.'"><a href="javascript:void(0)">7.Change Customer /ប្ដូរភ្ញៀវ</a></li>
			      
				  <li class="'.$copy_kot.'" onclick="send_kot("'.$avl_order->split_id.'");"><a href="javascript:void(0)">8.Copy Kot/ចំលងKOT</a></li>
				  
			       <li class="'.$cancel_all.'" onclick="CancelAllOrderItems("'.$tables->id.'",1,"'.$avl_order->split_id.'");"><a href="javascript:void(0)">9.Cancel All/លុបទាំងអស់</a></li>
			       <li class="'.$new_split_gen.'" onclick="bilGenerator("'.$tables->id.'","'.$avl_order->split_id.'");"><a href="javascript:void(0)">10.New Split generator/បំបែកតុថ្មី</a></li>
		               </ul>
	            </div>
	            <div class="split_s">';
	                $bill_count=!empty($order_count)? $order_count:0;
	                $order_count=!empty(count($avl_bill))? count($avl_bill):0;
		            $table_data .='<div class="btn-group-vertical" role="group">
		            <button type="button" class="btn btn-danger">'.$order_count.'</button>
		             <button type="button" class="btn btn-danger">'.$bill_count.'</button>
		            </div>
		            </div>
		           </div>';
		}
		echo $table_data;
	}
	}
	
	  function customer_suggestions($term = NULL, $limit = NULL){
        // $this->sma->checkPermissions('index');
        if ($this->input->get('term')) {
            $term = $this->input->get('term', TRUE);
        }
        if (strlen($term) < 1) {
            return FALSE;
        }
        $limit = $this->input->get('limit', TRUE);
        $rows['results'] = $this->pos_model->getCustomerSuggestions($term, $limit);
        $this->sma->send_json($rows);
    }
public function DINEINcheckCustomerDiscount(){
        $billid = $this->input->post('bill_id');
        $return = $this->site->is_uniqueDiscountExist();
        $unique_discount = 0;
        if (!empty($return)) {
            $unique_discount = 1;
        }
        /*if($result = $this->pos_model->getDineinCustomerDiscount($billid)){*/
        $result = $this->pos_model->getDineinCustomerDiscount($billid);
        $dis_result = $this->pos_model->getAllCustomerDiscount();
        echo json_encode(array('cus_dis' => $result, 'all_dis' => $dis_result, 'unique_discount' => $unique_discount));exit;
        // }
        // echo json_encode(array('no_discount'=>'no_discount'));exit;
    }
	 public function checkCustomerDiscount(){
        $billid = $this->input->post('bill_id');
        $return = $this->site->is_uniqueDiscountExist();
        $unique_discount = 0;
        if (!empty($return)) {
            $unique_discount = 1;
        }
        $result = $this->pos_model->getDineinCustomerDiscount($billid);
        $dis_result = $this->pos_model->getAllCustomerDiscount();
        echo json_encode(array('cus_dis' => $result, 'all_dis' => $dis_result, 'unique_discount' => $unique_discount));exit;
    }
	public function multiple_reprint_view(){
        $bill_id = $this->input->get('bill_id');
		$type = $this->input->get('type');
        $billids = (explode(",", $bill_id));
        /*echo "<pre>";
        print_r($billids);die;*/
        $billdata = array();
        foreach ($billids as $key => $billid) {
            $billdata['message'][$key] = $this->session->flashdata('message');
            if($type ==1){
            $inv = $this->pos_model->getInvoiceByID($billid);
		    $tableno = $this->pos_model->getTableNumber($billid);
			$billdata['billi_tems'][$key] = $this->pos_model->getAllBillitems($billid);
            $billdata['discounnames'][$key] = $this->pos_model->getBillDiscountNames($billid);
			  $billdata['payments'][$key] = $this->pos_model->getInvoicePayments($billid);
			}else{
		    $inv = $this->pos_model->getInvoiceByID_archival($billid);
		    $tableno = $this->pos_model->getTableNumber_archival($billid);
			$billdata['billi_tems'][$key] = $this->pos_model->getAllBillitems_archival($billid);
            $billdata['discounnames'][$key] = $this->pos_model->getBillDiscountNames_archival($billid);
			$billdata['payments'][$key] = $this->pos_model->getInvoicePayments_archival($billid);
			}
            $billdata['bill_id'][$key] = $key;
            $this->load->helper('pos');
            if (!$this->session->userdata('view_right')) {
                $this->sma->view_rights($inv->created_by, true);
            }
            $biller_id = $inv->biller_id;
            $bill_id = $inv->sales_id;
	
            $customer_id = $inv->customer_id;
            $delivery_person_id = $inv->delivery_person_id;

            $billdata['inv'][$key] = $inv;
            $billdata['tableno'][$key] = $tableno;
            $billdata['customer'][$key] = $this->pos_model->getCompanyByID($customer_id);

            if ($delivery_person_id != 0) {
                $this->data['delivery_person'][$key] = $this->pos_model->getUserByID($delivery_person_id);
            }
            $billdata['created_by'][$key] = $this->site->getUser($inv->created_by);
            $billdata['cashier'][$key] = $this->pos_model->getCashierInfo($billid);
            $billdata['printer'][$key] = $this->pos_model->getPrinterByID($this->pos_settings->printer);
            $billdata['biller'][$key] = $this->pos_model->getCompanyByID($biller_id);
            $billdata['type'][$key] = $this->input->post('type');
            $billdata['pos_settings'][$key] = $this->pos_settings;
            $billdata['Settings'] = $this->Settings;
            $billdata['assets'] = $this->data['assets'];
        }
        $this->load->view($this->theme . 'pos_v2/multiple_bill_reprint', $billdata);

    }
	  public function repaymant(){
        $this->data['warehouse'] = $this->session->userdata('warehouse_id') ? $this->site->getWarehouseByID($this->session->userdata('warehouse_id')) : null;
        $this->data['tax_rates'] = $this->site->getAllTaxRates();
        $currency = $this->site->getAllCurrencies();
        $postData = $this->input->post();
        if ($this->input->post('action') == "PAYMENT-SUBMIT") {
            /*echo "<pre>";
            print_r($this->input->post());die;*/
            $balance = $this->input->post('balance_amount');
            $dueamount = $this->input->post('due_amount');
            $total_pay = $this->input->post('total') + $this->input->post('balance_amount');
            $total = $this->input->post('total');
            $customer_changed = 0;
            $loyalty_customer = $this->input->post('loyalty_customer');
            $new_customer_id = $this->input->post('new_customer_id');
            if ($loyalty_customer) {
                $customer_changed = 1;
                $customer_id = $loyalty_customer;
            } elseif ($new_customer_id) {
                $customer_id = $this->input->post('new_customer_id');
                $customer_changed = 1;
            } else {
                $customer_id = $this->input->post('customer_id');
            }
            $order_split_id = $this->input->post('order_split_id');
            $paid = !empty($dueamount) ? ($total - $dueamount) : $total;
            $p = isset($_POST['paid_by']) ? sizeof($_POST['paid_by']) : 0;
            $default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
            $getExchangecode = $this->site->getExchangeRatecode($this->Settings->default_currency);
            for ($r = 0; $r < $p; $r++) {
                if ($_POST['amount_' . $getExchangecode . ''][$r] == '' && $_POST['amount_' . $default_currency_data->code . ''][$r] == '') {
                    unset($link);
                } else {
                    foreach ($currency as $currency_row) {
                        if ($default_currency_data->code == $currency_row->code) {
                            $multi_currency[] = array(
                                'sale_id' => $this->input->post('sales_id'),
                                'bil_id' => $this->input->post('bill_id'),
                                'currency_id' => $currency_row->id,
                                'currency_rate' => $currency_row->rate,
                                'amount' => $_POST['amount_' . $currency_row->code][$r],
                            );
                        } else {
                            $multi_currency[] = array(
                                'sale_id' => $this->input->post('sales_id'),
                                'bil_id' => $this->input->post('bill_id'),
                                'currency_id' => $currency_row->id,
                                'currency_rate' => $currency_row->rate,
                                'amount' => $_POST['amount_' . $currency_row->code][$r],
                            );
                        }
                    }
                }
            }
            for ($r = 0; $r < $p; $r++) {
                if ($_POST['amount_' . $getExchangecode . ''][$r] == '' && $_POST['amount_' . $default_currency_data->code . ''][$r] == '') {
                    unset($link);
                } else {
                    foreach ($currency as $currency_row) {
                        if ($currency_row->rate == $default_currency_data->rate) {
                            $p = isset($_POST['amount_' . $currency_row->code][$r]) ? sizeof($_POST['amount_' . $currency_row->code]) : 0;
                            $amount = $_POST['amount_' . $currency_row->code][$r];
                        } else {
                            $amount_exchange = $_POST['amount_' . $currency_row->code][$r];
                        }
                    }
                    $crd_exp_date = explode('/', $this->input->post('card_exp_date[1]'));
					if(!empty($_POST['wallet_type'])){
					$wallets=$this->site->getWalletsById($_POST['wallet_type']);
					}
                    $payment[$r] = array(
                        'date' => $this->site->getTransactionDate(),
                        'paid_on' => date('Y-m-d H:i:s'),
                        'sale_id' => $_POST['bill_id'],
                        'bill_id' => $_POST['bill_id'],
                        'amount' => $amount ? $amount : 0,
                        'amount_exchange' => $amount_exchange ? $amount_exchange : 0,
                        'pos_paid' => $_POST['amount_' . $default_currency_data->code . ''][$r],
                        'pos_balance' => round($balance, 3),
                        'paid_by' => $_POST['paid_by'][$r],
                        'cc_no' => $_POST['cc_no'][$r],
                        'cc_month' => $crd_exp_date[0],
                        'cc_year' => $crd_exp_date[1],
						'wallet_id' => $_POST['wallet_type'],
						'wallet_name' => !empty($wallets->name)?$wallets->name:'',
                        'created_by' => $this->session->userdata('user_id'),
                        'type' => 'received',
                    );
                    if (isset($_POST['rough_tender'])) {
                        $payment[$r]['loyalty_points'] = $_POST['paying_loyalty_points'][$r];
                    }
                }
            }
            $loyalty_used_points = $this->input->post('loyalty_used_points') ? $this->input->post('loyalty_used_points') : 0;
            $billid = $this->input->post('bill_id');
            $salesid = $this->input->post('sales_id');
            $taxation = $this->input->post('taxation') ? $this->input->post('taxation') : 0;
            $update_bill = array(
                'updated_at' => date('Y-m-d H:i:s'),
                'paid_by' => $this->session->userdata('user_id'),
                'total_pay' => $total_pay,
                'balance' => $balance,
                'paid' => $paid,
                'payment_status' => 'Completed',
                'default_currency_code' => $default_currency_data->code,
                'default_currency_rate' => $default_currency_data->rate,
                'table_whitelisted' => $taxation,
            );
                $sales_bill = array(
                'grand_total' => $total,
                'paid' => $paid,
                'payment_status' => 'Paid',
                'default_currency_code' => $default_currency_data->code,
                'default_currency_rate' => $default_currency_data->rate,
               );
                $updateCreditLimit['company_id'] = $postData['company_id'];
                $updateCreditLimit['customer_type'] = $postData['customer_type'];
                $new_payment = true;
                $response = $this->pos_model->rePayment($update_bill, $billid, $payment, $multi_currency, $salesid, $sales_bill, $order_split_id, $updateCreditLimit, $total, $customer_id, $loyalty_used_points, $taxation, $customer_changed);
             if ($response) {
                redirect("pos/pos/reprint_view/?bill_id=".$response->id ."&type=".$response->order_type);
               } else {
               redirect("pos/pos/reprint");
            }
       }
	  }
}
