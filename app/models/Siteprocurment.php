<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Siteprocurment extends CI_Model{
    public function __construct() {
        parent::__construct();
    }

    public function get_total_qty_alerts() {
        $this->db->where('quantity < alert_quantity', NULL, FALSE)->where('track_quantity', 1);
        return $this->db->count_all_results('products');
    }
	

    public function get_expiring_qty_alerts() {
        $date = date('Y-m-d', strtotime('+3 months'));
        $this->db->select('SUM(quantity_balance) as alert_num')
        ->where('expiry !=', NULL)->where('expiry !=', '0000-00-00')
        ->where('expiry <', $date);
        $q = $this->db->get('pro_purchase_items');
        if ($q->num_rows() > 0) {
            $res = $q->row();   
            return (INT) $res->alert_num;
        }
        return FALSE;
    }

    public function get_shop_sale_alerts() {
        $this->db->join('deliveries', 'deliveries.sale_id=sales.id', 'left')
        ->where('sales.shop', 1)->where('sales.sale_status', 'completed')->where('sales.payment_status', 'paid')
        ->group_start()->where('deliveries.status !=', 'delivered')->or_where('deliveries.status IS NULL', NULL)->group_end();
        return $this->db->count_all_results('sales');
    }

    public function get_shop_payment_alerts() {
        $this->db->where('shop', 1)->where('attachment !=', NULL)->where('payment_status !=', 'paid');
        return $this->db->count_all_results('sales');
    }

    public function get_setting() {
        $q = $this->db->get('settings');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getDateFormat($id) {
        $q = $this->db->get_where('date_format', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	
	public function getRoomdetail($room_id){
		$this->db->select('rooms.*, reservation_rooms.reservation_id');
		$this->db->join('reservation_rooms', 'reservation_rooms.room_id = rooms.id');
		$this->db->where('rooms.id', $room_id);
		$q = $this->db->get('rooms');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
	}
	
	public function checkHousekeepingrequest($room_id){
		
		$q = $this->db->get_where('housekeeping_request', array('room_id' => $room_id), 1);
        if ($q->num_rows() > 0) {
            return TRUE;
        }
        return FALSE;
	}
	
	public function GETaccessModules($modules){
		
		$q = $this->db->get_where('pro_access_permission', array('user_id' => $this->session->userdata('user_id')), 1);
		
        if ($q->num_rows() > 0) {
			if(in_array($modules, json_decode($q->row('modules')))){ 
				return TRUE;
			}
			return FALSE;
        }
        return FALSE;
	}
	
	public function getUsersnotificationWithoutSales(){
		$this->db->where_in('group_id', array(1, 2, 10, 11));
		$q = $this->db->get('users');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
	}
	
	public function getUsersnotificationWithSales(){
		$this->db->where_in('group_id', array(1, 2, 5, 10, 11));
		$q = $this->db->get('users');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
	}
	
	
	
	public function getAllAccess() {
        $q = $this->db->get('pro_access');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getAllCompanies($group_name) {
        $q = $this->db->get_where('companies', array('group_name' => $group_name));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
	public function getAllgroupUsers($group_id) {
        $q = $this->db->get_where('users', array('group_id' => $group_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
	

    public function getCompanyByID($id) {
        $q = $this->db->get_where('companies', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getCustomerGroupByID($id) {
        $q = $this->db->get_where('customer_groups', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getUser($id = NULL) {
        if (!$id) {
            $id = $this->session->userdata('user_id');
        }
        $q = $this->db->get_where('users', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getProductByID($id) {
        $q = $this->db->get_where('products', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	
	public function getRecipeByID($id) {
	$this->db->select('r.*,t.rate as purchase_tax_rate,b.name as brand_name,rc.name as category_name,rsc.name as subcategory_name,cm.category_id,cm.subcategory_id,cm.brand_id,cm.purchase_cost,cm.selling_price as cost');
	$this->db->from('recipe r');
	$this->db->join('category_mapping as cm','cm.product_id=r.id','left');
	$this->db->join('recipe_categories as rc','rc.id=cm.category_id','left');
	$this->db->join('recipe_categories rsc','rsc.id=cm.subcategory_id','left');	
	$this->db->join('brands b','b.id=cm.brand_id','left');
	$this->db->join('tax_rates t','r.purchase_tax=t.id','left');
	$this->db->where(array('r.id' => $id));
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	public function getVariantByID($id) {
    $this->db->select('name');
    $this->db->from('recipe_variants');
    $this->db->where('id',$id);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	
	public function getALLCustomer() {
        $q = $this->db->get('customer');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
	public function getAllCurrencies() {
        $q = $this->db->get('currencies');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
    public function getAllCountries() {
        $q = $this->db->get('countries');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getCurrencyByCode($code) {
        $q = $this->db->get_where('currencies', array('code' => $code), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getAllTaxRates() {
        $q = $this->db->get('tax_rates');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getTaxRateByID($id) {
        $q = $this->db->get_where('tax_rates', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getAllWarehouses() {
	$this->db->where('type', 0);
        $q = $this->db->get('warehouses');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    public function getAllWarehouses_Stores() {
        $q = $this->db->get('warehouses');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	 public function getAllWarehouses_Storeslist() {
		 $this->db->where_not_in('id',$this->store_id);
        $q = $this->db->get('warehouses');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	function  get_all_warehouse(){
		$this->db->select("");
		$this->db->get_where("store_request");
		$this->db->get();
		
	}
	public function getAllStores() {
        $this->db->where('type', 1);
        $q = $this->db->get('warehouses');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

	public function getStoreIDBY($id) {
        $q = $this->db->get_where('warehouses', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	
    public function getWarehouseByID($id) {
        $q = $this->db->get_where('warehouses', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getAllCategories() {
        $this->db->where('parent_id', NULL)->or_where('parent_id', 0)->order_by('name');
        $q = $this->db->get("categories");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getSubCategories($parent_id) {
        $this->db->where('parent_id', $parent_id)->order_by('name');
        $q = $this->db->get("categories");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getCategoryByID($id) {
        $q = $this->db->get_where('categories', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	
	public function getAllRecipeCategories() {
        $this->db->where('parent_id', NULL)->or_where('parent_id', 0)->order_by('name');
        $q = $this->db->get("recipe_categories");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
	public function getAllRecipeRestaurantCategories() {
        $this->db->where('services_type', 'restaurant')->where('parent_id', 0)->order_by('name');
        $q = $this->db->get("recipe_categories");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
	public function getAllRecipeBarCategories() {
        $this->db->where('services_type', 'bar')->where('parent_id', 0)->order_by('name');
        $q = $this->db->get("recipe_categories");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getRecipeSubCategories($parent_id) {
        $this->db->where('parent_id', $parent_id)->order_by('name');
        $q = $this->db->get("recipe_categories");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getRecipeCategoryByID($id) {
        $q = $this->db->get_where('recipe_categories', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getGiftCardByID($id) {
        $q = $this->db->get_where('gift_cards', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getGiftCardByNO($no) {
        $q = $this->db->get_where('gift_cards', array('card_no' => $no), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function updateInvoiceStatus() {
        $date = date('Y-m-d');
        $q = $this->db->get_where('invoices', array('status' => 'unpaid'));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                if ($row->due_date < $date) {
                    $this->db->update('invoices', array('status' => 'due'), array('id' => $row->id));
                }
            }
            $this->db->update('settings', array('update' => $date), array('setting_id' => '1'));
            return true;
        }
    }

    public function modal_js() {
        return '<script type="text/javascript">' . file_get_contents($this->data['assets'] . 'js/modal.js') . '</script>';
    }

    public function getReference($field) {
        $q = $this->db->get_where('order_ref', array('ref_id' => '1'), 1);
        if ($q->num_rows() > 0) {
            $ref = $q->row();
            switch ($field) {
                case 'so':
                    $prefix = $this->Settings->sales_prefix;
                    break;
                case 'pos':
                    $prefix = isset($this->Settings->sales_prefix) ? $this->Settings->sales_prefix . '/POS' : '';
                    break;
                case 'qu':
                    $prefix = $this->Settings->quote_prefix;
                    break;
                case 'po':
                    $prefix = $this->Settings->purchase_prefix;
                    break;
                case 'to':
                    $prefix = $this->Settings->transfer_prefix;
                    break;
                case 'do':
                    $prefix = $this->Settings->delivery_prefix;
                    break;
                case 'pay':
                    $prefix = $this->Settings->payment_prefix;
                    break;
                case 'ppay':
                    $prefix = $this->Settings->ppayment_prefix;
                    break;
                case 'ex':
                    $prefix = $this->Settings->expense_prefix;
                    break;
                case 're':
                    $prefix = $this->Settings->return_prefix;
                    break;
                case 'rep':
                    $prefix = $this->Settings->returnp_prefix;
                    break;
                case 'qa':
                    $prefix = $this->Settings->qa_prefix;
                    break;
                default:
                    $prefix = '';
            }

            // $ref_no = (!empty($prefix)) ? $prefix . '/' : '';
            $ref_no = $prefix;

            if ($this->Settings->reference_format == 1) {
                $ref_no .= date('Y') . "/" . sprintf("%04s", $ref->{$field});
            } elseif ($this->Settings->reference_format == 2) {
                $ref_no .= date('Y') . "/" . date('m') . "/" . sprintf("%04s", $ref->{$field});
            } elseif ($this->Settings->reference_format == 3) {
                $ref_no .= sprintf("%04s", $ref->{$field});
            } else {
                $ref_no .= $this->getRandomReference();
            }

            return $ref_no;
        }
        return FALSE;
    }

    public function getRandomReference($len = 12) {
        $result = '';
        for ($i = 0; $i < $len; $i++) {
            $result .= mt_rand(0, 9);
        }

        if ($this->getSaleByReference($result)) {
            $this->getRandomReference();
        }

        return $result;
    }

    public function getSaleByReference($ref) {
        $this->db->like('reference_no', $ref, 'before');
        $q = $this->db->get('sales', 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function updateReference($field) {
        $q = $this->db->get_where('order_ref', array('ref_id' => '1'), 1);
        if ($q->num_rows() > 0) {
            $ref = $q->row();
            $this->db->update('order_ref', array($field => $ref->{$field} + 1), array('ref_id' => '1'));
            return TRUE;
        }
        return FALSE;
    }

    public function checkPermissions() {
        $q = $this->db->get_where('permissions', array('group_id' => $this->session->userdata('group_id')), 1);
        if ($q->num_rows() > 0) {
            return $q->result_array();
        }
        return FALSE;
    }
	
	public function getAccessNotifications($id) {
        $date = date('Y-m-d');
        $this->db->where("DATE(created_on) =", $date);
        $this->db->where("is_read", 0);
        $this->db->where('user_id', $id);
        $q = $this->db->get("pro_access_notification");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }
	
	public function insertNotification($notification_array = array()){
	    $this->load->library('socketemitter');
		if(!empty($notification_array)){	
			$this->db->insert('pro_access_notification ', $notification_array);	
			$insert_id = $this->db->insert_id();
			$q = $this->db->where('id', $insert_id)->get('pro_access_notification');
			if ($q->num_rows() > 0) {
				$this->socketemitter->setEmit('pro_notification', $q->row());
			}
   
			return true;
		}
		return false;
	}
	
	public function clearNotitfication($notification_id){
		
		if(!empty($notification_id)){	
			
			$this->db->where_in('id', explode(',',$notification_id));
			$this->db->update('pro_access_notification ', array('is_read' => 1));			
			
			return true;
		}
		return false;
	}

    public function getNotifications() {
        $date = date('Y-m-d H:i:s', time());
        $this->db->where("from_date <=", $date);
        $this->db->where("till_date >=", $date);
        if (!$this->Owner) {
            if ($this->Supplier) {
                $this->db->where('scope', 4);
            } elseif ($this->Customer) {
                $this->db->where('scope', 1)->or_where('scope', 3);
            } elseif (!$this->Customer && !$this->Supplier) {
                $this->db->where('scope', 2)->or_where('scope', 3);
            }
        }
        $q = $this->db->get("notifications");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    public function getUpcomingEvents() {
        $dt = date('Y-m-d');
        $this->db->where('start >=', $dt)->order_by('start')->limit(5);
        if ($this->Settings->restrict_calendar) {
            $this->db->where('user_id', $this->session->userdata('user_id'));
        }

        $q = $this->db->get('calendar');

        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getUserGroup($user_id = false) {
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        $group_id = $this->getUserGroupID($user_id);
        $q = $this->db->get_where('groups', array('id' => $group_id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	
	public function getUserAccesscheck($user_id = false) {
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
		
        $access_id = $this->getUserAccessID($user_id);
		$this->db->select('id, name');
		$this->db->where_in('id', $access_id);
        $q = $this->db->get('pro_access');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row->name;
            }
			
            return $data;
        }
        return FALSE;
    }
	
	public function getUserAccessID($user_id = false) {
        $q = $this->db->get_where('pro_access_permission', array('user_id' => $user_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row->access_id;
            }
            return $data;
        }
		return FALSE;
    }

    public function getUserGroupID($user_id = false) {
        $user = $this->getUser($user_id);
        return $user->group_id;
    }

    public function getWarehouseProductsVariants($option_id, $warehouse_id = NULL) {
        if ($warehouse_id) {
            $this->db->where('warehouse_id', $warehouse_id);
        }
        $q = $this->db->get_where('warehouses_products_variants', array('option_id' => $option_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
	public function getWarehouseRecipesVariants($option_id, $warehouse_id = NULL) {
        if ($warehouse_id) {
            $this->db->where('warehouse_id', $warehouse_id);
        }
        $q = $this->db->get_where('warehouses_recipes_variants', array('option_id' => $option_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getPurchasedItem($clause) {
        $orderby = ($this->Settings->accounting_method == 1) ? 'asc' : 'desc';
        $this->db->order_by('date', $orderby);
        $this->db->order_by('purchase_id', $orderby);
        if (!isset($clause['option_id']) || empty($clause['option_id'])) {
            $this->db->group_start()->where('option_id', NULL)->or_where('option_id', 0)->group_end();
        }
        $q = $this->db->get_where('pro_purchase_items', $clause);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function setPurchaseItem($clause, $qty) {
        if ($product = $this->getProductByID($clause['product_id'])) {
            if ($pi = $this->getPurchasedItem($clause)) {
                $quantity_balance = $pi->quantity_balance+$qty;
                return $this->db->update('pro_purchase_items', array('quantity_balance' => $quantity_balance), array('id' => $pi->id));
            } else {
                $unit = $this->getUnitByID($product->unit);
                $clause['product_unit_id'] = $product->unit;
                $clause['product_unit_code'] = $unit->code;
                $clause['product_code'] = $product->code;
                $clause['product_name'] = $product->name;
                $clause['purchase_id'] = $clause['transfer_id'] = $clause['item_tax'] = NULL;
                $clause['net_unit_cost'] = $clause['real_unit_cost'] = $clause['unit_cost'] = $product->cost;
                $clause['quantity_balance'] = $clause['quantity'] = $clause['unit_quantity'] = $clause['quantity_received'] = $qty;
                $clause['subtotal'] = ($product->cost * $qty);
                if (isset($product->tax_rate) && $product->tax_rate != 0) {
                    $tax_details = $this->siteprocurment->getTaxRateByID($product->tax_rate);
                    $ctax = $this->calculateTax($product, $tax_details, $product->cost);
                    $item_tax = $clause['item_tax'] = $ctax['amount'];
                    $tax = $clause['tax'] = $ctax['tax'];
                    $clause['tax_rate_id'] = $tax_details->id;
                    if ($product->tax_method != 1) {
                        $clause['net_unit_cost'] = $product->cost - $item_tax;
                        $clause['unit_cost'] = $product->cost;
                    } else {
                        $clause['net_unit_cost'] = $product->cost;
                        $clause['unit_cost'] = $product->cost + $item_tax;
                    }
                    $pr_item_tax = $this->sma->formatDecimal($item_tax * $clause['unit_quantity'], 4);
                    if ($this->Settings->indian_gst && $gst_data = $this->gst->calculteIndianGST($pr_item_tax, ($this->Settings->state == $supplier_details->state), $tax_details)) {
                        $clause['gst'] = $gst_data['gst'];
                        $clause['cgst'] = $gst_data['cgst'];
                        $clause['sgst'] = $gst_data['sgst'];
                        $clause['igst'] = $gst_data['igst'];
                    }
                    $clause['subtotal'] = (($clause['net_unit_cost'] * $clause['unit_quantity']) + $pr_item_tax);
                }
                $clause['status'] = 'received';
                $clause['date'] = date('Y-m-d');
                $clause['option_id'] = !empty($clause['option_id']) && is_numeric($clause['option_id']) ? $clause['option_id'] : NULL;
                return $this->db->insert('pro_purchase_items', $clause);
            }
        }
        return FALSE;
    }

    public function syncVariantQty($variant_id, $warehouse_id, $product_id = NULL) {
        $balance_qty = $this->getBalanceVariantQuantity($variant_id);
        $wh_balance_qty = $this->getBalanceVariantQuantity($variant_id, $warehouse_id);
        if ($this->db->update('product_variants', array('quantity' => $balance_qty), array('id' => $variant_id))) {
            if ($this->getWarehouseProductsVariants($variant_id, $warehouse_id)) {
                $this->db->update('warehouses_products_variants', array('quantity' => $wh_balance_qty), array('option_id' => $variant_id, 'warehouse_id' => $warehouse_id));
            } else {
                if($wh_balance_qty) {
                    $this->db->insert('warehouses_products_variants', array('quantity' => $wh_balance_qty, 'option_id' => $variant_id, 'warehouse_id' => $warehouse_id, 'product_id' => $product_id));
                }
            }
            return TRUE;
        }
        return FALSE;
    }

    public function getWarehouseProducts($product_id, $warehouse_id = NULL) {
        if ($warehouse_id) {
            $this->db->where('warehouse_id', $warehouse_id);
        }
        $q = $this->db->get_where('warehouses_products', array('product_id' => $product_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
	public function getWarehouseRecipes($recipe_id, $warehouse_id = NULL) {
        if ($warehouse_id) {
            $this->db->where('warehouse_id', $warehouse_id);
        }
        $q = $this->db->get_where('warehouses_recipes', array('recipe_id' => $recipe_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function syncProductQty($product_id, $warehouse_id) {
        $balance_qty = $this->getBalanceQuantity($product_id);
        $wh_balance_qty = $this->getBalanceQuantity($product_id, $warehouse_id);
        if ($this->db->update('products', array('quantity' => $balance_qty), array('id' => $product_id))) {
            if ($this->getWarehouseProducts($product_id, $warehouse_id)) {
                $this->db->update('warehouses_products', array('quantity' => $wh_balance_qty), array('product_id' => $product_id, 'warehouse_id' => $warehouse_id));
            } else {
                if( ! $wh_balance_qty) { $wh_balance_qty = 0; }
                $product = $this->siteprocurment->getProductByID($product_id);
                $this->db->insert('warehouses_products', array('quantity' => $wh_balance_qty, 'product_id' => $product_id, 'warehouse_id' => $warehouse_id, 'avg_cost' => $product->cost));
            }
            return TRUE;
        }
        return FALSE;
    }
	

    public function getSaleByID($id) {
        $q = $this->db->get_where('sales', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getSalePayments($sale_id) {
        $q = $this->db->get_where('payments', array('sale_id' => $sale_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function syncSalePayments($id) {
        $sale = $this->getSaleByID($id);
        if ($payments = $this->getSalePayments($id)) {
            $paid = 0;
            $grand_total = $sale->grand_total+$sale->rounding;
            foreach ($payments as $payment) {
                $paid += $payment->amount;
            }

            $payment_status = $paid == 0 ? 'pending' : $sale->payment_status;
            if ($this->sma->formatDecimal($grand_total) == $this->sma->formatDecimal($paid)) {
                $payment_status = 'paid';
            } elseif ($sale->due_date <= date('Y-m-d') && !$sale->sale_id) {
                $payment_status = 'due';
            } elseif ($paid != 0) {
                $payment_status = 'partial';
            }

            if ($this->db->update('sales', array('paid' => $paid, 'payment_status' => $payment_status), array('id' => $id))) {
                return true;
            }
        } else {
            $payment_status = ($sale->due_date <= date('Y-m-d')) ? 'due' : 'pending';
            if ($this->db->update('sales', array('paid' => 0, 'payment_status' => $payment_status), array('id' => $id))) {
                return true;
            }
        }

        return FALSE;
    }

    public function getPurchaseByID($id) {
        $q = $this->db->get_where('pro_purchases', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getPurchasePayments($purchase_id) {
        $q = $this->db->get_where('payments', array('purchase_id' => $purchase_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function syncPurchasePayments($id) {
        $purchase = $this->getPurchaseByID($id);
        $paid = 0;
        if ($payments = $this->getPurchasePayments($id)) {
            foreach ($payments as $payment) {
                $paid += $payment->amount;
            }
        }

        $payment_status = $paid <= 0 ? 'pending' : $purchase->payment_status;
        if ($this->sma->formatDecimal($purchase->grand_total) > $this->sma->formatDecimal($paid) && $paid > 0) {
            $payment_status = 'partial';
        } elseif ($this->sma->formatDecimal($purchase->grand_total) <= $this->sma->formatDecimal($paid)) {
            $payment_status = 'paid';
        }

        if ($this->db->update('pro_purchases', array('paid' => $paid, 'payment_status' => $payment_status), array('id' => $id))) {
            return true;
        }

        return FALSE;
    }

    private function getBalanceQuantity($product_id, $warehouse_id = NULL) {
        $this->db->select('SUM(COALESCE(quantity_balance, 0)) as stock', False);
        $this->db->where('product_id', $product_id)->where('quantity_balance !=', 0);
        if ($warehouse_id) {
            $this->db->where('warehouse_id', $warehouse_id);
        }
        $this->db->group_start()->where('status', 'received')->or_where('status', 'partial')->group_end();
        $q = $this->db->get('pro_purchase_items');
        if ($q->num_rows() > 0) {
            $data = $q->row();
            return $data->stock;
        }
        return 0;
    }

    private function getBalanceVariantQuantity($variant_id, $warehouse_id = NULL) {
        $this->db->select('SUM(COALESCE(quantity_balance, 0)) as stock', False);
        $this->db->where('option_id', $variant_id)->where('quantity_balance !=', 0);
        if ($warehouse_id) {
            $this->db->where('warehouse_id', $warehouse_id);
        }
        $this->db->group_start()->where('status', 'received')->or_where('status', 'partial')->group_end();
        $q = $this->db->get('pro_purchase_items');
        if ($q->num_rows() > 0) {
            $data = $q->row();
            return $data->stock;
        }
        return 0;
    }

    public function calculateAVCost($product_id, $warehouse_id, $net_unit_price, $unit_price, $quantity, $product_name, $option_id, $item_quantity) {
        $product = $this->getProductByID($product_id);
        $real_item_qty = $quantity;
        $wp_details = $this->getWarehouseProduct($warehouse_id, $product_id);
        $con = $wp_details ? $wp_details->avg_cost : $product->cost;
        $tax_rate = $this->getTaxRateByID($product->tax_rate);
        $ctax = $this->calculateTax($product, $tax_rate, $con);
        if ($product->tax_method) {
            $avg_net_unit_cost = $con;
            $avg_unit_cost = ($con + $ctax['amount']);
        } else {
            $avg_unit_cost = $con;
            $avg_net_unit_cost = ($con - $ctax['amount']);
        }

        if ($pis = $this->getPurchasedItems($product_id, $warehouse_id, $option_id)) {
            $cost_row = array();
            $quantity = $item_quantity;
            $balance_qty = $quantity;
            foreach ($pis as $pi) {
                if (!empty($pi) && $pi->quantity > 0 && $balance_qty <= $quantity && $quantity > 0) {
                    if ($pi->quantity_balance >= $quantity && $quantity > 0) {
                        $balance_qty = $pi->quantity_balance - $quantity;
                        $cost_row = array('date' => date('Y-m-d'), 'product_id' => $product_id, 'sale_item_id' => 'sale_items.id', 'purchase_item_id' => $pi->id, 'quantity' => $quantity, 'purchase_net_unit_cost' => $avg_net_unit_cost, 'purchase_unit_cost' => $avg_unit_cost, 'sale_net_unit_price' => $net_unit_price, 'sale_unit_price' => $unit_price, 'quantity_balance' => $balance_qty, 'inventory' => 1, 'option_id' => $option_id);
                        $quantity = 0;
                    } elseif ($quantity > 0) {
                        $quantity = $quantity - $pi->quantity_balance;
                        $balance_qty = $quantity;
                        $cost_row = array('date' => date('Y-m-d'), 'product_id' => $product_id, 'sale_item_id' => 'sale_items.id', 'purchase_item_id' => $pi->id, 'quantity' => $pi->quantity_balance, 'purchase_net_unit_cost' => $avg_net_unit_cost, 'purchase_unit_cost' => $avg_unit_cost, 'sale_net_unit_price' => $net_unit_price, 'sale_unit_price' => $unit_price, 'quantity_balance' => 0, 'inventory' => 1, 'option_id' => $option_id);
                    }
                }
                if (empty($cost_row)) {
                    break;
                }
                $cost[] = $cost_row;
                if ($quantity == 0) {
                    break;
                }
            }
        }
        if ($quantity > 0 && !$this->Settings->overselling) {
            $this->session->set_flashdata('error', sprintf(lang("quantity_out_of_stock_for_%s"), ($pi->product_name ? $pi->product_name : $product_name)));
            redirect($_SERVER["HTTP_REFERER"]);
        } elseif ($quantity > 0) {
            $cost[] = array('date' => date('Y-m-d'), 'product_id' => $product_id, 'sale_item_id' => 'sale_items.id', 'purchase_item_id' => NULL, 'quantity' => $real_item_qty, 'purchase_net_unit_cost' => $avg_net_unit_cost, 'purchase_unit_cost' => $avg_unit_cost, 'sale_net_unit_price' => $net_unit_price, 'sale_unit_price' => $unit_price, 'quantity_balance' => NULL, 'overselling' => 1, 'inventory' => 1);
            $cost[] = array('pi_overselling' => 1, 'product_id' => $product_id, 'quantity_balance' => (0 - $quantity), 'warehouse_id' => $warehouse_id, 'option_id' => $option_id);
        }
        return $cost;
    }

    public function calculateCost($product_id, $warehouse_id, $net_unit_price, $unit_price, $quantity, $product_name, $option_id, $item_quantity) {
        $pis = $this->getPurchasedItems($product_id, $warehouse_id, $option_id);
        $real_item_qty = $quantity;
        $quantity = $item_quantity;
        $balance_qty = $quantity;
        foreach ($pis as $pi) {
            $cost_row = NULL;
            if (!empty($pi) && $balance_qty <= $quantity && $quantity > 0) {
                $purchase_unit_cost = $pi->unit_cost ? $pi->unit_cost : ($pi->net_unit_cost + ($pi->item_tax / $pi->quantity));
                if ($pi->quantity_balance >= $quantity && $quantity > 0) {
                    $balance_qty = $pi->quantity_balance - $quantity;
                    $cost_row = array('date' => date('Y-m-d'), 'product_id' => $product_id, 'sale_item_id' => 'sale_items.id', 'purchase_item_id' => $pi->id, 'quantity' => $quantity, 'purchase_net_unit_cost' => $pi->net_unit_cost, 'purchase_unit_cost' => $purchase_unit_cost, 'sale_net_unit_price' => $net_unit_price, 'sale_unit_price' => $unit_price, 'quantity_balance' => $balance_qty, 'inventory' => 1, 'option_id' => $option_id);
                    $quantity = 0;
                } elseif ($quantity > 0) {
                    $quantity = $quantity - $pi->quantity_balance;
                    $balance_qty = $quantity;
                    $cost_row = array('date' => date('Y-m-d'), 'product_id' => $product_id, 'sale_item_id' => 'sale_items.id', 'purchase_item_id' => $pi->id, 'quantity' => $pi->quantity_balance, 'purchase_net_unit_cost' => $pi->net_unit_cost, 'purchase_unit_cost' => $purchase_unit_cost, 'sale_net_unit_price' => $net_unit_price, 'sale_unit_price' => $unit_price, 'quantity_balance' => 0, 'inventory' => 1, 'option_id' => $option_id);
                }
            }
            $cost[] = $cost_row;
            if ($quantity == 0) {
                break;
            }
        }
        if ($quantity > 0) {
            $this->session->set_flashdata('error', sprintf(lang("quantity_out_of_stock_for_%s"), (isset($pi->product_name) ? $pi->product_name : $product_name)));
            redirect($_SERVER["HTTP_REFERER"]);
        }
        return $cost;
    }

    public function getPurchasedItems($product_id, $warehouse_id, $option_id = NULL) {
        $orderby = ($this->Settings->accounting_method == 1) ? 'asc' : 'desc';
        $this->db->select('id, quantity, quantity_balance, net_unit_cost, unit_cost, item_tax');
        $this->db->where('product_id', $product_id)->where('warehouse_id', $warehouse_id)->where('quantity_balance !=', 0);
        if (!isset($option_id) || empty($option_id)) {
            $this->db->group_start()->where('option_id', NULL)->or_where('option_id', 0)->group_end();
        } else {
            $this->db->where('option_id', $option_id);
        }
        $this->db->group_start()->where('status', 'received')->or_where('status', 'partial')->group_end();
        $this->db->group_by('id');
        $this->db->order_by('date', $orderby);
        $this->db->order_by('purchase_id', $orderby);
        $q = $this->db->get('pro_purchase_items');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getProductComboItems($pid, $warehouse_id = NULL) {
        $this->db->select('products.id as id, combo_items.item_code as code, combo_items.quantity as qty, products.name as name, products.type as type, combo_items.unit_price as unit_price, warehouses_products.quantity as quantity')
            ->join('products', 'products.code=combo_items.item_code', 'left')
            ->join('warehouses_products', 'warehouses_products.product_id=products.id', 'left')
            ->group_by('combo_items.id');
        if($warehouse_id) {
            $this->db->where('warehouses_products.warehouse_id', $warehouse_id);
        }
        $q = $this->db->get_where('combo_items', array('combo_items.product_id' => $pid));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
        return FALSE;
    }
	
	public function getRecipeComboItems($pid, $warehouse_id = NULL) {
        $this->db->select('recipes.id as id, combo_items.item_code as code, combo_items.quantity as qty, recipes.name as name, recipes.type as type, combo_items.unit_price as unit_price, warehouses_recipes.quantity as quantity')
            ->join('recipes', 'recipes.code=combo_items.item_code', 'left')
            ->join('warehouses_recipes', 'warehouses_recipes.recipe_id=recipes.id', 'left')
            ->group_by('combo_items.id');
        if($warehouse_id) {
            $this->db->where('warehouses_recipes.warehouse_id', $warehouse_id);
        }
        $q = $this->db->get_where('combo_items', array('combo_items.recipe_id' => $pid));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
        return FALSE;
    }

    public function item_costing($item, $pi = NULL) {
        $item_quantity = $pi ? $item['aquantity'] : $item['quantity'];
        if (!isset($item['option_id']) || empty($item['option_id']) || $item['option_id'] == 'null') {
            $item['option_id'] = NULL;
        }

        if ($this->Settings->accounting_method != 2 && !$this->Settings->overselling) {

            if ($this->getProductByID($item['product_id'])) {
                if ($item['product_type'] == 'standard') {
                    $unit = $this->getUnitByID($item['product_unit_id']);
                    $item['net_unit_price'] = $this->convertToBase($unit, $item['net_unit_price']);
                    $item['unit_price'] = $this->convertToBase($unit, $item['unit_price']);
                    $cost = $this->calculateCost($item['product_id'], $item['warehouse_id'], $item['net_unit_price'], $item['unit_price'], $item['quantity'], $item['product_name'], $item['option_id'], $item_quantity);
                } elseif ($item['product_type'] == 'combo') {
                    $combo_items = $this->getProductComboItems($item['product_id'], $item['warehouse_id']);
                    foreach ($combo_items as $combo_item) {
                        $pr = $this->getProductByCode($combo_item->code);
                        if ($pr->tax_rate) {
                            $pr_tax = $this->getTaxRateByID($pr->tax_rate);
                            if ($pr->tax_method) {
                                $item_tax = $this->sma->formatDecimal((($combo_item->unit_price) * $pr_tax->rate) / (100 + $pr_tax->rate));
                                $net_unit_price = $combo_item->unit_price - $item_tax;
                                $unit_price = $combo_item->unit_price;
                            } else {
                                $item_tax = $this->sma->formatDecimal((($combo_item->unit_price) * $pr_tax->rate) / 100);
                                $net_unit_price = $combo_item->unit_price;
                                $unit_price = $combo_item->unit_price + $item_tax;
                            }
                        } else {
                            $net_unit_price = $combo_item->unit_price;
                            $unit_price = $combo_item->unit_price;
                        }
                        if ($pr->type == 'standard') {
                            $cost[] = $this->calculateCost($pr->id, $item['warehouse_id'], $net_unit_price, $unit_price, ($combo_item->qty * $item['quantity']), $pr->name, NULL, $item_quantity);
                        } else {
                            $cost[] = array(array('date' => date('Y-m-d'), 'product_id' => $pr->id, 'sale_item_id' => 'sale_items.id', 'purchase_item_id' => NULL, 'quantity' => ($combo_item->qty * $item['quantity']), 'purchase_net_unit_cost' => 0, 'purchase_unit_cost' => 0, 'sale_net_unit_price' => $combo_item->unit_price, 'sale_unit_price' => $combo_item->unit_price, 'quantity_balance' => NULL, 'inventory' => NULL));
                        }
                    }
                } else {
                    $cost = array(array('date' => date('Y-m-d'), 'product_id' => $item['product_id'], 'sale_item_id' => 'sale_items.id', 'purchase_item_id' => NULL, 'quantity' => $item['quantity'], 'purchase_net_unit_cost' => 0, 'purchase_unit_cost' => 0, 'sale_net_unit_price' => $item['net_unit_price'], 'sale_unit_price' => $item['unit_price'], 'quantity_balance' => NULL, 'inventory' => NULL));
                }
            } elseif ($item['product_type'] == 'manual') {
                $cost = array(array('date' => date('Y-m-d'), 'product_id' => $item['product_id'], 'sale_item_id' => 'sale_items.id', 'purchase_item_id' => NULL, 'quantity' => $item['quantity'], 'purchase_net_unit_cost' => 0, 'purchase_unit_cost' => 0, 'sale_net_unit_price' => $item['net_unit_price'], 'sale_unit_price' => $item['unit_price'], 'quantity_balance' => NULL, 'inventory' => NULL));
            }

        } else {

            if ($this->getProductByID($item['product_id'])) {
                if ($item['product_type'] == 'standard') {
                    $cost = $this->calculateAVCost($item['product_id'], $item['warehouse_id'], $item['net_unit_price'], $item['unit_price'], $item['quantity'], $item['product_name'], $item['option_id'], $item_quantity);
                } elseif ($item['product_type'] == 'combo') {
                    $combo_items = $this->getProductComboItems($item['product_id'], $item['warehouse_id']);
                    foreach ($combo_items as $combo_item) {
                        $pr = $this->getProductByCode($combo_item->code);
                        if ($pr->tax_rate) {
                            $pr_tax = $this->getTaxRateByID($pr->tax_rate);
                            if ($pr->tax_method) {
                                $item_tax = $this->sma->formatDecimal((($combo_item->unit_price) * $pr_tax->rate) / (100 + $pr_tax->rate));
                                $net_unit_price = $combo_item->unit_price - $item_tax;
                                $unit_price = $combo_item->unit_price;
                            } else {
                                $item_tax = $this->sma->formatDecimal((($combo_item->unit_price) * $pr_tax->rate) / 100);
                                $net_unit_price = $combo_item->unit_price;
                                $unit_price = $combo_item->unit_price + $item_tax;
                            }
                        } else {
                            $net_unit_price = $combo_item->unit_price;
                            $unit_price = $combo_item->unit_price;
                        }
                        $cost[] = $this->calculateAVCost($combo_item->id, $item['warehouse_id'], $net_unit_price, $unit_price, ($combo_item->qty * $item['quantity']), $item['product_name'], $item['option_id'], $item_quantity);
                    }
                } else {
                    $cost = array(array('date' => date('Y-m-d'), 'product_id' => $item['product_id'], 'sale_item_id' => 'sale_items.id', 'purchase_item_id' => NULL, 'quantity' => $item['quantity'], 'purchase_net_unit_cost' => 0, 'purchase_unit_cost' => 0, 'sale_net_unit_price' => $item['net_unit_price'], 'sale_unit_price' => $item['unit_price'], 'quantity_balance' => NULL, 'inventory' => NULL));
                }
            } elseif ($item['product_type'] == 'manual') {
                $cost = array(array('date' => date('Y-m-d'), 'product_id' => $item['product_id'], 'sale_item_id' => 'sale_items.id', 'purchase_item_id' => NULL, 'quantity' => $item['quantity'], 'purchase_net_unit_cost' => 0, 'purchase_unit_cost' => 0, 'sale_net_unit_price' => $item['net_unit_price'], 'sale_unit_price' => $item['unit_price'], 'quantity_balance' => NULL, 'inventory' => NULL));
            }

        }
        return $cost;
    }

    public function costing($items) {
        $citems = array();
        foreach ($items as $item) {
            $option = (isset($item['option_id']) && !empty($item['option_id']) && $item['option_id'] != 'null' && $item['option_id'] != 'false') ? $item['option_id'] : '';
            $pr = $this->getProductByID($item['product_id']);
            $item['option_id'] = $option;
            if ($pr && $pr->type == 'standard') {
                if (isset($citems['p' . $item['product_id'] . 'o' . $item['option_id']])) {
                    $citems['p' . $item['product_id'] . 'o' . $item['option_id']]['aquantity'] += $item['quantity'];
                } else {
                    $citems['p' . $item['product_id'] . 'o' . $item['option_id']] = $item;
                    $citems['p' . $item['product_id'] . 'o' . $item['option_id']]['aquantity'] = $item['quantity'];
                }
            } elseif ($pr && $pr->type == 'combo') {
                $wh = $this->Settings->overselling ? NULL : $item['warehouse_id'];
                $combo_items = $this->getProductComboItems($item['product_id'], $wh);
                foreach ($combo_items as $combo_item) {
                    if ($combo_item->type == 'standard') {
                        if (isset($citems['p' . $combo_item->id . 'o' . $item['option_id']])) {
                            $citems['p' . $combo_item->id . 'o' . $item['option_id']]['aquantity'] += ($combo_item->qty*$item['quantity']);
                        } else {
                            $cpr = $this->getProductByID($combo_item->id);
                            if ($cpr->tax_rate) {
                                $cpr_tax = $this->getTaxRateByID($cpr->tax_rate);
                                if ($cpr->tax_method) {
                                    $item_tax = $this->sma->formatDecimal((($combo_item->unit_price) * $cpr_tax->rate) / (100 + $cpr_tax->rate));
                                    $net_unit_price = $combo_item->unit_price - $item_tax;
                                    $unit_price = $combo_item->unit_price;
                                } else {
                                    $item_tax = $this->sma->formatDecimal((($combo_item->unit_price) * $cpr_tax->rate) / 100);
                                    $net_unit_price = $combo_item->unit_price;
                                    $unit_price = $combo_item->unit_price + $item_tax;
                                }
                            } else {
                                $net_unit_price = $combo_item->unit_price;
                                $unit_price = $combo_item->unit_price;
                            }
                            $cproduct = array('product_id' => $combo_item->id, 'product_name' => $cpr->name, 'product_type' => $combo_item->type, 'quantity' => ($combo_item->qty*$item['quantity']), 'net_unit_price' => $net_unit_price, 'unit_price' => $unit_price, 'warehouse_id' => $item['warehouse_id'], 'item_tax' => $item_tax, 'tax_rate_id' => $cpr->tax_rate, 'tax' => ($cpr_tax->type == 1 ? $cpr_tax->rate.'%' : $cpr_tax->rate), 'option_id' => NULL, 'product_unit_id' => $cpr->unit);
                            $citems['p' . $combo_item->id . 'o' . $item['option_id']] = $cproduct;
                            $citems['p' . $combo_item->id . 'o' . $item['option_id']]['aquantity'] = ($combo_item->qty*$item['quantity']);
                        }
                    }
                }
            }
        }
        // $this->sma->print_arrays($combo_items, $citems);
        $cost = array();
        foreach ($citems as $item) {
            $item['aquantity'] = $citems['p' . $item['product_id'] . 'o' . $item['option_id']]['aquantity'];
            $cost[] = $this->item_costing($item, TRUE);
        }
        return $cost;
    }

    public function syncQuantity($sale_id = NULL, $purchase_id = NULL, $oitems = NULL, $product_id = NULL) {
        if ($sale_id) {

            $sale_items = $this->getAllSaleItems($sale_id);
            foreach ($sale_items as $item) {
                if ($item->product_type == 'standard') {
                    $this->syncProductQty($item->product_id, $item->warehouse_id);
                    if (isset($item->option_id) && !empty($item->option_id)) {
                        $this->syncVariantQty($item->option_id, $item->warehouse_id, $item->product_id);
                    }
                } elseif ($item->product_type == 'combo') {
                    $wh = $this->Settings->overselling ? NULL : $item->warehouse_id;
                    $combo_items = $this->getProductComboItems($item->product_id, $wh);
                    foreach ($combo_items as $combo_item) {
                        if($combo_item->type == 'standard') {
                            $this->syncProductQty($combo_item->id, $item->warehouse_id);
                        }
                    }
                }
            }

        } elseif ($purchase_id) {

            $purchase_items = $this->getAllPurchaseItems($purchase_id);
            foreach ($purchase_items as $item) {
                $this->syncProductQty($item->product_id, $item->warehouse_id);
                if (isset($item->option_id) && !empty($item->option_id)) {
                    $this->syncVariantQty($item->option_id, $item->warehouse_id, $item->product_id);
                }
            }

        } elseif ($oitems) {

            foreach ($oitems as $item) {
                if (isset($item->product_type)) {
                    if ($item->product_type == 'standard') {
                        $this->syncProductQty($item->product_id, $item->warehouse_id);
                        if (isset($item->option_id) && !empty($item->option_id)) {
                            $this->syncVariantQty($item->option_id, $item->warehouse_id, $item->product_id);
                        }
                    } elseif ($item->product_type == 'combo') {
                        $combo_items = $this->getProductComboItems($item->product_id, $item->warehouse_id);
                        foreach ($combo_items as $combo_item) {
                            if($combo_item->type == 'standard') {
                                $this->syncProductQty($combo_item->id, $item->warehouse_id);
                            }
                        }
                    }
                } else {
                    $this->syncProductQty($item->product_id, $item->warehouse_id);
                    if (isset($item->option_id) && !empty($item->option_id)) {
                        $this->syncVariantQty($item->option_id, $item->warehouse_id, $item->product_id);
                    }
                }
            }

        } elseif ($product_id) {
            $warehouses = $this->getAllWarehouses();
            foreach ($warehouses as $warehouse) {
                $this->syncProductQty($product_id, $warehouse->id);
                if ($product_variants = $this->getProductVariants($product_id)) {
                    foreach ($product_variants as $pv) {
                        $this->syncVariantQty($pv->id, $warehouse->id, $product_id);
                    }
                }
            }
        }
    }

    public function getProductVariants($product_id) {
        $q = $this->db->get_where('product_variants', array('product_id' => $product_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
	public function getRecipeVariants($recipe_id) {
        $q = $this->db->get_where('recipe_variants', array('recipe_id' => $recipe_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
	
	public function getCurrencyByID($id) {
        $q = $this->db->get_where('currencies', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getAllSaleItems($sale_id) {
        $q = $this->db->get_where('sale_items', array('sale_id' => $sale_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getAllPurchaseItems($purchase_id) {
        $q = $this->db->get_where('pro_purchase_items', array('purchase_id' => $purchase_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function syncPurchaseItems($data = array()) {
        if (!empty($data)) {
            foreach ($data as $items) {
                foreach ($items as $item) {
                    if (isset($item['pi_overselling'])) {
                        unset($item['pi_overselling']);
                        $option_id = (isset($item['option_id']) && !empty($item['option_id'])) ? $item['option_id'] : NULL;
                        $clause = array('purchase_id' => NULL, 'transfer_id' => NULL, 'product_id' => $item['product_id'], 'warehouse_id' => $item['warehouse_id'], 'option_id' => $option_id);
                        if ($pi = $this->getPurchasedItem($clause)) {
                            $quantity_balance = $pi->quantity_balance + $item['quantity_balance'];
                            $this->db->update('pro_purchase_items', array('quantity_balance' => $quantity_balance), array('id' => $pi->id));
                        } else {
                            $clause['quantity'] = 0;
                            $clause['item_tax'] = 0;
                            $clause['quantity_balance'] = $item['quantity_balance'];
                            $clause['status'] = 'received';
                            $clause['option_id'] = !empty($clause['option_id']) && is_numeric($clause['option_id']) ? $clause['option_id'] : NULL;
                            $this->db->insert('pro_purchase_items', $clause);
                        }
                    } else {
                        if ($item['inventory']) {
                            $this->db->update('pro_purchase_items', array('quantity_balance' => $item['quantity_balance']), array('id' => $item['purchase_item_id']));
                        }
                    }
                }
            }
            return TRUE;
        }
        return FALSE;
    }
	
	

    public function getProductByCode($code) {
        $q = $this->db->get_where('products', array('code' => $code), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	
	public function getRecipeByCode($code) {
        $q = $this->db->get_where('recipes', array('code' => $code), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function check_customer_deposit($customer_id, $amount) {
        $customer = $this->getCompanyByID($customer_id);
        return $customer->deposit_amount >= $amount;
    }

    public function getWarehouseProduct($warehouse_id, $product_id) {
        $q = $this->db->get_where('warehouses_products', array('product_id' => $product_id, 'warehouse_id' => $warehouse_id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getAllBaseUnits() {
        $q = $this->db->get_where("units", array('base_unit' => NULL));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getUnitsByBUID($base_unit) {
        $this->db->where('id', $base_unit)->or_where('base_unit', $base_unit)
        ->group_by('id')->order_by('id asc');
        $q = $this->db->get("units");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
			
            return $data;
        }
        return FALSE;
    }

    public function getUnitByID($id) {
        $q = $this->db->get_where("units", array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getPriceGroupByID($id) {
        $q = $this->db->get_where('price_groups', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getProductGroupPrice($product_id, $group_id) {
        $q = $this->db->get_where('product_prices', array('price_group_id' => $group_id, 'product_id' => $product_id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	
	public function getRecipeGroupPrice($recipe_id, $group_id) {
        $q = $this->db->get_where('recipe_prices', array('price_group_id' => $group_id, 'recipe_id' => $recipe_id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getAllBrands() {
        $q = $this->db->get("brands");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getBrandByID($id) {
        $q = $this->db->get_where('brands', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	
	
	
	public function getAllTables() {
        $q = $this->db->get("tables");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getTableByID($id) {
        $q = $this->db->get_where('tables', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	
	public function getAllKitchentypes() {
        $q = $this->db->get("kitchen_type");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getKitchentypeByID($id) {
        $q = $this->db->get_where('kitchen_type', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	public function getAllTabletypes() {
        $q = $this->db->get("table_type");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getTabletypeByID($id) {
        $q = $this->db->get_where('table_type', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	
	
	public function getAllBulidingtypes() {
        $q = $this->db->get("buliding_type");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getBulidingtypeByID($id) {
        $q = $this->db->get_where('buliding_type', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	
	public function  availableRooms(){
		$this->db->select('rooms.id, reservation_rooms.room_id');
		$this->db->join('reservation_rooms', 'reservation_rooms.room_id = rooms.id', 'left');
		$q = $this->db->get('rooms');
		if ($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
                $data['room_count'][] = $row-id;
				if(!empty($row->room_id)){
					$data['reservation_count'][] = $row-room_id;
				}
            }
			$available =  count($data['room_count']) - count($data['reservation_count']);

            return $available;
        }
        return FALSE;	
	}
	
	public function getAllTypewiseRoom($id) {
        $q = $this->db->get_where('rooms', array('room_type_id' => $id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
	public function getAllTypewiseExtra($id) {
        $q = $this->db->get_where('extrabeds', array('room_type_id' => $id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
	public function checkRoomRequestorders($room_id){
		
		$this->db->where('restaurant_request.room_id', $room_id);
		$this->db->order_by('restaurant_request.id', 'DESC');
		$this->db->group_by('restaurant_request.room_id');
		$q = $this->db->get('restaurant_request');
        if ($q->num_rows() > 0) {
			
            return TRUE;
        }
        return FALSE;	
	}
	
	public function checkBarRoomRequestorders($room_id){
		
		$this->db->where('bar_request.room_id', $room_id);
		$this->db->order_by('bar_request.id', 'DESC');
		$this->db->group_by('bar_request.room_id');
		$q = $this->db->get('bar_request');
        if ($q->num_rows() > 0) {
			
            return TRUE;
        }
        return FALSE;	
	}
	
	public function getAllRooms() {
        $q = $this->db->get("rooms");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getRoomByID($id) {
        $q = $this->db->get_where('rooms', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	
	public function getAllExtrabeds() {
        $q = $this->db->get("extrabeds");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getExtrabedByID($id) {
        $q = $this->db->get_where('extrabeds', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	
	public function getAllRoomtypes() {
        $q = $this->db->get("room_type");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
	public function getAllRoomamenitys(){
		$this->db->or_where('services_type', 'room_services');
		$this->db->or_where('services_type', 'housekeeping');
		$q = $this->db->get("products");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
	}
	
	public function getAllExtraproduct(){
		$this->db->or_where('services_type', 'room_services');
		$this->db->or_where('services_type', 'housekeeping');
		$q = $this->db->get("products");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
	}

    public function getRoomtypeByID($id) {
        $q = $this->db->get_where('room_type', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	
	public function getAllVehicles() {
        $q = $this->db->get("vehicles");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getVehicleByID($id) {
        $q = $this->db->get_where('vehicles', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	public function getAllVehiclecategorys() {
        $q = $this->db->get("vehicle_category");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getVehiclecategotyByID($id) {
        $q = $this->db->get_where('vehicle_category', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	public function getAllVehiclefeatures() {
        $q = $this->db->get("vehicle_feature");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getVehiclefeatureByID($id) {
        $q = $this->db->get_where('vehicle_feature', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function convertToBase($unit, $value) {
        switch($unit->operator) {
            case '*':
                return $value / $unit->operation_value;
                break;
            case '/':
                return $value * $unit->operation_value;
                break;
            case '+':
                return $value - $unit->operation_value;
                break;
            case '-':
                return $value + $unit->operation_value;
                break;
            default:
                return $value;
        }
    }

    function calculateTax($product_details = NULL, $tax_details, $custom_value = NULL, $c_on = NULL) {
        $value = $custom_value ? $custom_value : (($c_on == 'cost') ? $product_details->cost : $product_details->price);
        $tax_amount = 0; $tax = 0;
        if ($tax_details && $tax_details->type == 1 && $tax_details->rate != 0) {
            if ($product_details && $product_details->tax_method == 1) {
                $tax_amount = $this->sma->formatDecimal((($value) * $tax_details->rate) / 100, 4);
                $tax = $this->sma->formatDecimal($tax_details->rate, 0) . "%";
            } else {
                $tax_amount = $this->sma->formatDecimal((($value) * $tax_details->rate) / (100 + $tax_details->rate), 4);
                $tax = $this->sma->formatDecimal($tax_details->rate, 0) . "%";
            }
        } elseif ($tax_details && $tax_details->type == 2) {
            $tax_amount = $this->sma->formatDecimal($tax_details->rate);
            $tax = $this->sma->formatDecimal($tax_details->rate, 0);
        }
        return array('id' => $tax_details->id, 'tax' => $tax, 'amount' => $tax_amount);
    }
	
	function calculateTaxRecipe($recipe_details = NULL, $tax_details, $custom_value = NULL, $c_on = NULL) {
        $value = $custom_value ? $custom_value : (($c_on == 'cost') ? $recipe_details->cost : $recipe_details->price);
        $tax_amount = 0; $tax = 0;
        if ($tax_details && $tax_details->type == 1 && $tax_details->rate != 0) {
            if ($recipe_details && $recipe_details->tax_method == 1) {
                $tax_amount = $this->sma->formatDecimal((($value) * $tax_details->rate) / 100, 4);
                $tax = $this->sma->formatDecimal($tax_details->rate, 0) . "%";
            } else {
                $tax_amount = $this->sma->formatDecimal((($value) * $tax_details->rate) / (100 + $tax_details->rate), 4);
                $tax = $this->sma->formatDecimal($tax_details->rate, 0) . "%";
            }
        } elseif ($tax_details && $tax_details->type == 2) {
            $tax_amount = $this->sma->formatDecimal($tax_details->rate);
            $tax = $this->sma->formatDecimal($tax_details->rate, 0);
        }
        return array('id' => $tax_details->id, 'tax' => $tax, 'amount' => $tax_amount);
    }

    public function getAddressByID($id) {
        return $this->db->get_where('addresses', ['id' => $id], 1)->row();
    }

    public function checkSlug($slug, $type = NULL) {
        if (!$type) {
            return $this->db->get_where('products', ['slug' => $slug], 1)->row();
        } elseif ($type == 'category') {
            return $this->db->get_where('categories', ['slug' => $slug], 1)->row();
        } elseif ($type == 'brand') {
            return $this->db->get_where('brands', ['slug' => $slug], 1)->row();
        }
        return FALSE;
    }

    public function calculateDiscount($discount = NULL, $amount) {
        if ($discount && $this->Settings->product_discount) {
            $dpos = strpos($discount, '%');
            if ($dpos !== false) {
                $pds = explode("%", $discount);
                return $this->sma->formatDecimal(((($this->sma->formatDecimal($amount)) * (Float) ($pds[0])) / 100), 4);
            } else {
                return $this->sma->formatDecimal($discount, 4);
            }
        }
        return 0;
    }

    public function calculateOrderTax($order_tax_id = NULL, $amount) {
        if ($this->Settings->tax2 != 0 && $order_tax_id) {
            if ($order_tax_details = $this->siteprocurment->getTaxRateByID($order_tax_id)) {
                if ($order_tax_details->type == 1) {
                    return $this->sma->formatDecimal((($amount * $order_tax_details->rate) / 100), 4);
                } else {
                    return $this->sma->formatDecimal($order_tax_details->rate, 4);
                }
            }
        }
        return 0;
    }

    public function getSmsSettings() {
        $q = $this->db->get('sms_settings');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	
	public function getAllREQUESTNUMBER(){
		$q = $this->db->get_where('pro_request', array('status' => 'approved'));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
	}

	public function getAllREQUESTNUMBERedit(){
		$q = $this->db->get_where('pro_request', array('status' => 'completed'));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
	}
	
	public function getAllSTOREREQUESTNUMBER(){
		$this->db->where('status', 'approved');
		$this->db->or_where('status', 'partial_complete');
		$q = $this->db->get('pro_store_request');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
	}

	public function getAllSTOREREQUESTNUMBERedit(){
		$q = $this->db->get_where('pro_store_request', array('status !=' => 'approved'));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
	}
	
	public function getAllSTORETRANSNUMBER(){
		$q = $this->db->get_where('pro_store_transfers'); //array('status' => 'approved')
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
			/* print_r($data);
			die; */
            return $data;
        }
        return FALSE;
	}

	public function getAllSTORETRANSNUMBERedit(){
		$q = $this->db->get_where('pro_store_transfers', array('status' => 'completed'));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
	}
	
	
	public function getAllSTORE_RETURN_NO(){
		$q = $this->db->get_where('pro_store_returns', array('status' => 'approved'));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
	}

	public function getAllSTORE_RETURN_NOedit(){
		$q = $this->db->get_where('pro_store_returns', array('status' => 'completed'));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
	}
	
	public function getAllQUOTESNUMBER(){
		$q = $this->db->get_where('pro_quotes', array('status' => 'approved'));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
	}

	public function getAllQUOTESNUMBERedit(){
		$q = $this->db->get_where('pro_quotes', array('status' => 'completed'));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
	}
	
	public function getAllPRETURN_NUMBER(){
		$this->db->select('stock_master.id as stock_id, stock_master.return_status, stock_master.purchase_invoice_id as id, P.reference_no');
		$this->db->join('purchase_invoices P', 'P.id = stock_master.purchase_invoice_id');
		$this->db->where('return_status', 1);
		
		$q = $this->db->get('pro_stock_master');
		
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
			
            return $data;
        }
        return FALSE;
	}
	
	public function getAllPONUMBER(){
		$q = $this->db->get_where('pro_purchase_orders', array('status' => 'approved'));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
	}

	public function getAllPONUMBERedit(){
		$q = $this->db->get_where('pro_purchase_orders', array('status' => 'completed'));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
	}
	public function getAll_Approved_invoice(){
		$q = $this->db->get_where('pro_purchase_invoices', array('status' => 'approved'));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
	}
	public function defaultStores() {
		// $this->db->where('is_default_store' , 1);
        $q = $this->db->get('warehouses');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	public function lastidRequest(){
		$this->db->order_by('id' , 'DESC');
        $q = $this->db->get('pro_request');
        if ($q->num_rows() > 0) {
            return $q->row('id');
        }
        return 0;
	}
	public function lastidStoreRequest(){
		$this->db->order_by('s_no' , 'DESC');
        $q = $this->db->get('pro_store_request');
        if ($q->num_rows() > 0) {
            return $q->row('id');
        }
        return 0;
	}
	public function lastidQuotation(){
		$this->db->order_by('id' , 'DESC');
        $q = $this->db->get('pro_quotes');
        if ($q->num_rows() > 0) {
            return $q->row('id');
        }
        return 0;
	}
    public function lastidPurchaseInv(){
        $this->db->order_by('id' , 'DESC');
		$this->db->where('store_id' ,$this->store_id);
        $q = $this->db->get('pro_purchase_invoices');
        if ($q->num_rows() > 0) {
            return $q->row('id');
        }
        return 0;
    }
    public function lastidPurchaseReturn(){
        $this->db->order_by('id' , 'DESC');
        $q = $this->db->get('pro_purchase_returns');
        if ($q->num_rows() > 0) {
            return $q->row('id');
        }
        return 0;
    }
    public function lastidPurchase(){
        $this->db->order_by('id' , 'DESC');
        $q = $this->db->get('pro_purchase_orders');
        if ($q->num_rows() > 0) {
            return $q->row('id');
        }
        return 0;
    }
    public function lastidStoreTransafer(){
		$this->db->order_by('id' , 'DESC');
        $q = $this->db->get('pro_store_transfers');
        if ($q->num_rows() > 0) {
            return $q->row('id');
        }
        return 0;
    }
	 public function lastidpro_store_return_receivers(){
	     $this->db->order_by('id' , 'DESC');
		 $q = $this->db->get('pro_store_return_receivers');
		 if ($q->num_rows() > 0) {
            return $q->row('id');
         }
        return 0;
    }
	public function lastidpro_store_return(){
	     $this->db->order_by('id' , 'DESC');
		 $q = $this->db->get('pro_store_returns');
		 if ($q->num_rows() > 0) {
            return $q->row('id');
         }
        return 0;
    }
	
    public function currencyName($id){
	    $this->db->where('id', $id);
	    $q = $this->db->get('currencies');
    if ($q->num_rows() > 0) {
	return $q->row('name').'('.$q->row('symbol').')';
    }
    }
    function getItemByID($id){
	$q = $this->db->get_where('recipe', array('id' => $id), 1);
    if ($q->num_rows() > 0) {
	return $q->row();
    }
    return FALSE;
    }
    function stock_update($id,$qty){
	
	$this->db->set('stock_quantity','stock_quantity + '.$qty,false);
	$this->db->where('id',$id);
	$this->db->update('recipe');
    }
    function default_warehouse_id(){
    $this->db->limit(1);
    $q = $this->db->get('warehouses');
    if ($q->num_rows() > 0) {
	return $q->row('id');
    }
    return FALSE;
    }
	function getAllPONumbers(){
     $q = $this->db->get_where('pro_purchase_orders', array('status' => 'approved'));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
	}
	function getAllInvoiceNumbers(){
		
	  $q = $this->db->get_where('pro_purchase_invoices', array('status' => 'completed','store_id'=>$this->store_id));
      if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
      }
     return FALSE;
	}
	function getAllInvoiceNumbers_edit($invid){
    $this->db->select();
    $this->db->from('pro_purchase_invoices');
    $this->db->where('status','approved');
    $this->db->or_where('id',$invid);    
    $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
	}
    
    public function getProductNames($term, $limit = 10){
		$type = array('standard','raw');
		$this->db->select('r.*,t.rate as purchase_tax_rate,b.name as brand_name,rc.name as category_name,rsc.name as subcategory_name,cm.category_id,cm.subcategory_id,cm.id as cm_id,cm.brand_id,cm.purchase_cost as cost,cm.selling_price as price,u.name as unit_name,us.name as purchase_unitName,COALESCE(rv.id,0) as variant_id,(CASE WHEN r.variants = 1 THEN CONCAT(r.name,"-",rv.name) ELSE r.name END) AS name,cm.selling_price AS price,cm.purchase_cost AS cost,rvv.attr_id as option_id');
		$this->db->from('recipe r');
		$this->db->join('category_mapping as cm','cm.product_id=r.id','left'); // 
		$this->db->join('recipe_categories as rc','rc.id=cm.category_id','left');
		$this->db->join('recipe_categories rsc','rsc.id=cm.subcategory_id','left');	
		$this->db->join('brands b','b.id=cm.brand_id','left');
		$this->db->join('tax_rates t','r.purchase_tax=t.id','left');
		$this->db->join('units u','u.id=r.unit','left');
		$this->db->join('units us','us.id=r.purchase_unit','left');
		$this->db->join('recipe_variants_values rvv','rvv.recipe_id=r.id','left');
		$this->db->join('recipe_variants rv','rv.id=rvv.attr_id','left');
		if($this->Settings->item_search ==0){
		$this->db->where("(r.name LIKE '" . $term . "%' OR r.code LIKE '" . $term . "%' OR  concat(r.name, ' (', r.code, ')') LIKE '" . $term . "%')");
		}else{
		$this->db->where("(r.name LIKE '%" . $term . "%' OR r.code LIKE '%" . $term . "%' OR  concat(r.name, ' (', r.code, ')') LIKE '%" . $term . "%')"); 
		}
		$this->db->where_in('r.type',$type);
		$this->db->group_by('r.id,rv.id,rc.id,rsc.id,b.id');
		$this->db->limit($limit);
        $q = $this->db->get();
		/* 	echo $this->db->last_query();
		die; */
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {		
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
	 public function getProductNames_new($term, $limit = 10){
		$type = array('standard','raw');
		$this->db->select('r.*,t.rate as purchase_tax_rate,b.name as brand_name,rc.name as category_name,rsc.name as subcategory_name,cm.category_id,cm.subcategory_id,cm.id as cm_id,cm.brand_id,cm.purchase_cost as cost,cm.selling_price as price,u.name as unit_name,us.name as purchase_unitName,COALESCE(rv.id,0) as variant_id,(CASE WHEN r.variants = 1 THEN CONCAT(r.name,"-",rv.name) ELSE r.name END) AS name,cm.selling_price AS price,cm.purchase_cost AS cost,rvv.attr_id as option_id');
		$this->db->from('recipe r');
		$this->db->join('category_mapping as cm','cm.product_id=r.id','left'); // 
		$this->db->join('recipe_categories as rc','rc.id=cm.category_id','left');
		$this->db->join('recipe_categories rsc','rsc.id=cm.subcategory_id','left');	
		$this->db->join('brands b','b.id=cm.brand_id','left');
		$this->db->join('tax_rates t','r.purchase_tax=t.id','left');
		$this->db->join('units u','u.id=r.unit','left');
		$this->db->join('units us','us.id=r.purchase_unit','left');
		$this->db->join('recipe_variants_values rvv','rvv.recipe_id=r.id','left');
		$this->db->join('recipe_variants rv','rv.id=rvv.attr_id','left');
		$this->db->join('pro_stock_master','pro_stock_master.product_id=r.id AND pro_stock_master.store_id='.$this->store_id);
		if($this->Settings->item_search ==0){
		$this->db->where("(r.name LIKE '" . $term . "%' OR r.code LIKE '" . $term . "%' OR  concat(r.name, ' (', r.code, ')') LIKE '" . $term . "%')");
		}else{
		$this->db->where("(r.name LIKE '%" . $term . "%' OR r.code LIKE '%" . $term . "%' OR  concat(r.name, ' (', r.code, ')') LIKE '%" . $term . "%')"); 
		}
		$this->db->where_in('r.type',$type);
		$this->db->group_by('r.id,rv.id,rc.id,rsc.id,b.id');
		$this->db->limit($limit);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {		
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
    function item_cost_update($product_id,$cost,$selling_price,$tax_id,$cate){
		$data['cost'] = $selling_price;
		$data['price'] = $selling_price;
		$data['purchase_cost'] = $cost;
		$data['purchase_tax'] = $tax_id;
		$r = $this->getRecipeByID($product_id);
		$saleITemTypes = array('standard','prodcution','quick_service','combo');
		if(in_array($r->type,$saleITemTypes)){
			$this->db->where(array('id'=>$product_id,'category_id'=>$cate['category_id'],'subcategory_id'=>$cate['subcategory_id'],'brand_id'=>$cate['brand_id']));
			$this->db->update('recipe',$data);
		}
		$cate_mapp_data['purchase_cost'] = $cost;
		$cate_mapp_data['selling_price'] = $selling_price;
		$this->db->where(array('product_id'=>$product_id,'category_id'=>$cate['category_id'],'subcategory_id'=>$cate['subcategory_id'],'brand_id'=>$cate['brand_id']));
		$this->db->update('category_mapping',$cate_mapp_data);
	
    }
	
	 function item_cost_update_new($cate_map_data){
		$cate_mapp_data['store_id']       = $cate_map_data['store_id'];
		$cate_mapp_data['product_id']     = $cate_map_data['product_id'];
		$cate_mapp_data['variant_id']     = $cate_map_data['variant_id'];
		$cate_mapp_data['category_id']    = $cate_map_data['category_id'];
		$cate_mapp_data['subcategory_id'] = $cate_map_data['subcategory_id'];
		$cate_mapp_data['brand_id']       = $cate_map_data['brand_id'];
		$cate_mapp_data['batch_no']       = !empty($cate_map_data['batch'])?$cate_map_data['batch']:'';
		$cate_mapp_data['purchase_cost']  = $cate_map_data['cost_price'];
		$cate_mapp_data['vendor_id']      = $cate_map_data['supplier_id'];
		$cate_mapp_data['invoice_id']     = $cate_map_data['invoice_id'];
		$cate_mapp_data['selling_price']  = $cate_map_data['selling_price'];
		$cate_mapp_data['unique_id']      = $cate_map_data['unique_id'];
		$cate_mapp_data['status']         = 1;
		$cate_mapping=$this->db->get_where("category_mapping",array("unique_id"=>$cate_mapp_data['unique_id']));
	   if($cate_mapping->num_rows()>0){
		    $cate_mapping=$cate_mapping->row();
		   $this->db->where("unique_id",$cate_mapp_data['unique_id']);
		   $this->db->update("category_mapping",$cate_mapp_data);
		   return $cate_mapping->id;
	   }else{
		   $this->db->insert("category_mapping",$cate_mapp_data);
			$insertID                  = $this->db->insert_id();
			$UniqueID                  = $this->site->generateUniqueTableID($insertID);
			$this->site->updateUniqueTableId($insertID,$UniqueID,'category_mapping');
			return $UniqueID;
	   }
		return false;
    }
    public function getAll_respectiveSTOREREQUESTNUMBER(){
		$this->db->where('status', 'approved');
		$this->db->or_where('status', 'partial_complete');
		$this->db->where('to_store_id',$this->Settings->default_store);
		$q = $this->db->get('pro_store_request');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return array();
    }
    function getAvailableQty($product_id){
	$this->db->select('SUM(stock_in) as avail_qty');
	$this->db->from('pro_stock_master');
	$this->db->where('store_id',$this->Settings->default_store);
	$this->db->where('product_id',$product_id);
	//echo $this->db->get_compiled_select();
	$q = $this->db->get();
	
	return ($q->row('avail_qty')!=null)?$q->row('avail_qty'):0;
    }
    function hasApprovedPermission(){
	$module = $this->m;
	$action = 'approved';
	if ($this->Owner || $this->Admin) {
	    return true;
	}
	else if ($this->GP[$module . '-' . $action] == 1) {
	    return true;
	} else {
	    return false;
	}
    }
    function product_stockOut($product_id,$stockout,$cate){
        
	$this->db->set('stock','stock - '.$stockout,false);
	$this->db->where(array('product_id'=>$product_id,'category_id'=>$cate['category_id'],'subcategory_id'=>$cate['subcategory_id'],'brand_id'=>$cate['brand_id']));
	$this->db->update('category_mapping');    

    }
    
    function product_stockIn($product_id,$stockin,$cate){
		$this->db->set('stock','stock + '.$stockin,false);
		$this->db->where(array('product_id'=>$product_id,'category_id'=>$cate['category_id'],'subcategory_id'=>$cate['subcategory_id'],'brand_id'=>$cate['brand_id']));
		$this->db->update('category_mapping');
    }
    function get_store_requestNo($store_req_ids){
	$this->db->select('reference_no')
	->from('pro_store_request')
	->where_in('id',$store_req_ids);
	$q = $this->db->get();
	if ($q->num_rows() > 0) {
	    $storeReqNo = array();
            foreach (($q->result()) as $row) {
                array_push($storeReqNo,$row->reference_no);
            }
            return implode(',',$storeReqNo);
        }
        return false;
    }
    public function lastidProduction(){
	$this->db->order_by('id' , 'DESC');
        $q = $this->db->get('pro_production');
        if ($q->num_rows() > 0) {
            return $q->row('id');
        }
        return 0;
    }
    function getCategoryMappingID($product_id,$category_id,$subcategory_id,$brand){
	$q = $this->db->get_where('category_mapping', array('product_id'=>$product_id,'category_id' => $category_id,'subcategory_id' => $subcategory_id,'brand_id' => $brand));
    // print_r($this->db->last_query());die;
        return @$q->row('id');

    }

   

    function production_salestock_out($product_id,$stock_out_qty,$variant_id){
		// ingredient stock
        $item = $this->getrecipedeatilsByID($product_id);  
        if($item->type=="production" || $item->type=="quick_service" || $item->type=="semi_finished"){
           $q = $this->get_recipe_products($product_id,$variant_id); 
            if($q->num_rows()>0){
                foreach($q->result() as $k => $row){
                    $cate['category_id'] = $row->category_id;
                    $cate['subcategory_id'] = $row->subcategory_id;
                    $cate['brand_id'] = $row->brand_id;
                    $cate['cm_id'] = $row->cm_id;
					$ingredi_stock_out=$this->site->unitToBaseQty($row->quantity,$row->operator,$row->operation_value);
                    $updated_stock = $this->productionupdateStockMaster($row->product_id,$variant_id,$ingredi_stock_out,$cate);
                }
            }
        }   // die;    
    }

    function productionupdateStockMaster($product_id,$variant_id,$stock_out,$cate){   
        $store_id = $this->data['pos_store'];
        $rawstock =$this->getrawstock($product_id,$variant_id,$cate['category_id'],$cate['subcategory_id'],$cate['brand_id']); 
        $stock_overflow =0;
        if(!empty($rawstock)){
            foreach($rawstock as $row){     
                if($stock_overflow == 0)     {
                    $tobedetect = $stock_out; 
                }else{
                    $tobedetect =$stock_overflow; 
                }                 
                 $stock = $row->stock_in - $row->stock_out;                 
                if ($stock > $tobedetect){
                    $stock_overflow = $stock-$tobedetect;  
                    $stock_qty_taken = $tobedetect-$stock;
                    if($stock_overflow >= 0){                        
                       $query = 'update srampos_pro_stock_master set stock_in=stock_id +'.$tobedetect.', stock_out = stock_out + '.$tobedetect.' where id='.$row->id;
                      // echo $query;
                      $this->db->query($query);  
                      $stock_id = $row->id;
                     /*  $date =date('Y-m-d h:m:s');
                      $ledger_query ='insert into srampos_pro_stock_ledger(stock_id,store_id, product_id,variant_id, cm_id, category_id, subcategory_id, brand_id, transaction_identify,transaction_type,transaction_qty,date)values('.$stock_id.','.$store_id.','.$product_id.','.$variant_id.', '.$cate['cm_id'].', '.$cate['category_id'].', '.$cate['subcategory_id'].', '.$cate['brand_id'].', "Sales","O",'.$tobedetect.',"'.$date.'")';
                       $this->db->query($ledger_query);    */
                    }   
                      if($stock_qty_taken <= 0){                        
                        break;
                    }
                }else{                    
                    $stock = $row->stock_in - $row->stock_out;
                    $stock_overflow = $tobedetect -$stock;
                    $out = $stock - $tobedetect;                    
                    $closed='';
                    if($out <= 0){
                        $cloased=', stock_status =  "closed"';
                    }                    
                    $query = 'update srampos_pro_stock_master set stock_in=stock_id +'.$stock.', stock_out = stock_out + '.$stock.'  '.$closed.'  where id='.$row->id;
                    //echo $query;
                    $this->db->query($query);   
                    $stock_id = $row->id;
                    /* $date =date('Y-m-d h:m:s');
                    $ledger_query ='insert into srampos_pro_stock_ledger(stock_id,store_id, product_id,variant_id, cm_id, category_id, subcategory_id, brand_id, transaction_identify,transaction_type,transaction_qty,date)values('.$stock_id.','.$store_id.','.$product_id.','.$variant_id.', '.$cate['cm_id'].', '.$cate['category_id'].', '.$cate['subcategory_id'].', '.$cate['brand_id'].', "Sales","O",'.$stock.',"'.$date.'")';   
                     $this->db->query($ledger_query); */
					
                    if($stock_overflow <= 0){
                        break;
                    }
                }
            }
        }
            
    }

function production_salestock_in($product_id,$stock_out_qty,$variant_id){
		// ingredient stock
        $item = $this->getrecipedeatilsByID($product_id);  
        if($item->type=="production" || $item->type=="quick_service" || $item->type=="semi_finished"){
           $q = $this->get_recipe_products($product_id,$variant_id); 
            if($q->num_rows()>0){
                foreach($q->result() as $k => $row){
                    $cate['category_id'] = $row->category_id;
                    $cate['subcategory_id'] = $row->subcategory_id;
                    $cate['brand_id'] = $row->brand_id;
                    $cate['cm_id'] = $row->cm_id;
					$ingredi_stock_out=$this->site->unitToBaseQty($row->quantity,$row->operator,$row->operation_value);
                    $updated_stock = $this->productionStockMaster_in($row->product_id,$variant_id,$ingredi_stock_out,$cate);
                }
            }
        } elseif($item->type=="standard"){
			 $rawstock =$this->getrawstock($product_id,$variant_id,$item->category_id,$item->subcategory_id,$item->brand); 
			 if(!empty($rawstock)){
				 foreach($rawstock as $batch){
				   $query = 'update srampos_pro_stock_master set stock_in=stock_in + '.$stock_out_qty.', stock_out = stock_out - '.$stock_out_qty.'    where id='.$batch->id;
                    $this->db->query($query); 
                    $stock_id = $batch->id;
					break;
				  
			  }
			 }else{
			   $batches =$this->getrawstock_empty($product_id,$variant_id,$item->category_id,$item->subcategory_id,$item->brand);
			  foreach($batches as $batch){
				   $query = 'update srampos_pro_stock_master set stock_in=stock_in + '.$stock_out_qty.', stock_out = stock_out - '.$stock_out_qty.'    where id='.$batch->id;
                    $this->db->query($query); 
                    $stock_id = $batch->id;
					break;
				  
			  }
			 }

		}			// die;    
    }

    function productionupdateStockMaster_old($product_id,$stock_out,$cm_id,$cate){     
        $piece = $this->db->get_where('recipe', array('id' =>$product_id))->row('piece');
        $store_id = $this->data['pos_store'];
        $rawstock =$this->getrawstock($product_id,$cate['category_id'],$cate['subcategory_id'],$cate['brand_id']);          
        $stock_overflow =0;
        if(!empty($rawstock)){
            foreach($rawstock as $row){     
                if($stock_overflow == 0)     {
                    $tobedetect =$stock_out; 
                }else{
                    $tobedetect =$stock_overflow; 
                }                       
                                          
                if ($row->stock_in < $tobedetect)
                {      
                    $stock_overflow = $tobedetect -$row->stock_in;                      
                    if($stock_overflow >= 0){
                       $query = 'update srampos_pro_stock_master set  stock_out = stock_out + '.$row->stock_in.',stock_status =  "closed" where id='.$row->id;
                        $this->db->query($query);                         
                    }                 
                }else{
                    $out = $row->stock_in - $tobedetect;                    
                    $cloased='';
                    if($out == 0){
                        $cloased=', stock_status =  "closed"';
                    }
                    $query = 'update srampos_pro_stock_master set  stock_out = stock_out + '.$tobedetect.'  '.$cloased.'  where id='.$row->id;
                    $this->db->query($query);                       
                    break;
                }
            }
        }    
    }
public function getrawstock($product_id,$variant_id,$category_id,$subcategory_id,$brand_id){
        $this->db->select('pro_stock_master.*');
        $this->db->from('pro_stock_master');
        if($category_id !=''){
            $this->db->where('category_id',$category_id);
        }
        if($subcategory_id !=''){
            $this->db->where('subcategory_id',$subcategory_id);
        }
        if($brand_id !=''){
            $this->db->where('brand_id',$brand_id);
        }
        $this->db->where('product_id',$product_id);
		//if($variant_id !='0'){
            //$this->db->where('variant_id',$variant_id);
        //}
        //$this->db->where('variant_id',$variant_id);
        $this->db->where_not_in('stock_status','closed');
		$this->db->where('stock_in>0');
        $this->db->group_by('id');
        $this->db->order_by('id', 'asc');
        $q = $this->db->get(); 
        // print_r($this->db->last_query());die;
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return array();

     /*   if ($q->num_rows() > 0) {
            return $q->result_array();
        }
        return FALSE;*/
}

     public function getrecipedeatilsByID($id) {
        $q = $this->db->get_where('recipe', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    function get_recipe_products($product_id,$variant_id){
        $this->db->select('recipe_products.product_id,recipe_products.quantity,recipe.type,cm.id as cm_id,cm.category_id,cm.subcategory_id,cm.brand_id,IFNULL(u.operator,"") as operator,IFNULL(u.operation_value,"1") as operation_value,u.name');
        $this->db->from('recipe_products');
        $this->db->join('recipe','recipe.id=recipe_products.product_id');
        $this->db->join('category_mapping as cm','cm.id=recipe_products.cm_id','left');
        $this->db->join('units as u','u.id=recipe_products.unit_id','left');
        $this->db->where('recipe_id',$product_id);
        if($variant_id != 0){
            $this->db->where('recipe_products.variant_id',$variant_id);
        }
        $q = $this->db->get();            
        return $q;
    }        




   function productionStockMaster_in($product_id,$variant_id,$stock_out,$cate){   
        $store_id = $this->data['pos_store'];
        $rawstock =$this->getrawstock($product_id,$variant_id,$cate['category_id'],$cate['subcategory_id'],$cate['brand_id']); 
        $stock_overflow =0;
        if(!empty($rawstock)){
            foreach($rawstock as $row){     
                            $query = 'update srampos_pro_stock_master set stock_in=stock_in + '.$stock_out.', stock_out = stock_out - '.$stock_out.'    where id='.$row->id;
                    $this->db->query($query); 
                    $stock_id = $row->id;  
               break;
            }
        }else{
			$rawstock =$this->getrawstock_empty($product_id,$variant_id,$cate['category_id'],$cate['subcategory_id'],$cate['brand_id']); 
			 foreach($rawstock as $row){
				 $query = 'update srampos_pro_stock_master set stock_in=stock_in + '.$stock_out.', stock_out = stock_out - '.$stock_out.'    where id='.$row->id;
                    $this->db->query($query); 
                    $stock_id = $row->id;
					break;
			 }
			
		}
            
    }
     function getWarehouse($id=false){
	$this->db->where('type' , 0);
	if($id) $this->db->where('id' , $id);
        $q = $this->db->get('warehouses');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return 0;
    }
		public function lastidStoreStockRequest(){
		$this->db->order_by('s_no' , 'DESC');
        $q = $this->db->get('pro_stock_request');
        if ($q->num_rows() > 0) {
            return $q->row('s_no');
        }
        return 0;
	}
	function lastidStockRequest(){
	$this->db->order_by('s_no' , 'DESC');
        $q = $this->db->get('pro_stock_request');
        if ($q->num_rows() > 0) {
            return $q->row('s_no');
        }
        return 0;
    }
	
	  public function lastidGrn(){
        $this->db->order_by('id' , 'DESC');
		$this->db->where('store_id' ,$this->store_id);
        $q = $this->db->get('pro_grn');
        if ($q->num_rows() > 0) {
            return $q->row('id');
        }
        return 0;
    }
		 public function getreceive_items_details($store_receiver_id,$store_receiver_item_id){
		$this->db->where('store_receiver_id', $store_receiver_id);
		$this->db->where('store_receiver_item_id', $store_receiver_item_id);
		$q = $this->db->get('pro_store_receiver_item_details');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return array();
    }
	 public function getreturn_receive_items_details($store_return_receiver_id,$store_return_receiver_item_id){
		$this->db->where('store_return_receiver_id', $store_return_receiver_id);
		$this->db->where('store_return_receiver_item_id', $store_return_receiver_item_id);
		$q = $this->db->get('pro_store_return_receiver_item_details');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return array();
    }
	public function getstore_transfer_items_details($store_transfer_id,$store_transfer_item_id){
		$this->db->where('store_transfer_id', $store_transfer_id);
		$this->db->where('store_transfer_item_id', $store_transfer_item_id);
		$q = $this->db->get('pro_store_transfer_item_details');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return array();
    }
	public function getstorereturn_items_details($store_return_id,$store_return_item_id){
		$this->db->where('store_return_id', $store_return_id);
		$this->db->where('store_return_item_id', $store_return_item_id);
		$q = $this->db->get('pro_store_return_item_details');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return array();
    }
		  public function lastWastageId(){
        $this->db->order_by('id' , 'DESC');
		$this->db->where('store_id' ,$this->store_id);
        $q = $this->db->get('wastage');
        if ($q->num_rows() > 0) {
            return $q->row('id');
        }
        return 0;
    }
	
public function getrawstock_empty($product_id,$variant_id,$category_id,$subcategory_id,$brand_id){
       $this->db->select('pro_stock_master.*');
        $this->db->from('pro_stock_master');
        if($category_id !=''){
            $this->db->where('category_id',$category_id);
        }
        if($subcategory_id !=''){
            $this->db->where('subcategory_id',$subcategory_id);
        }
        if($brand_id !=''){
            $this->db->where('brand_id',$brand_id);
        }
		if($variant_id !='' && $variant_id !=0){
        $this->db->where('variant_id',$variant_id);   
		}	
        //$this->db->where('variant_id',$variant_id);        
        $this->db->where('product_id',$product_id);
      //  $this->db->where_not_in('stock_status','closed');
		$this->db->where('store_id',$this->store_id);
		$this->db->limit(1);
        $this->db->group_by('id');
        $this->db->order_by('id', 'asc');
        $q = $this->db->get(); 
        // print_r($this->db->last_query());die; 
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return array();

     /*   if ($q->num_rows() > 0) {
            return $q->result_array();
        }
        return FALSE;*/
}
	
}
