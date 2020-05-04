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
        $this->load->admin_model('pos_model');
        $this->load->admin_model('settings_model');
        $this->load->helper('text');
        $this->load->helper('shop');
        $this->pos_settings = $this->pos_model->getSetting();
        $this->settings = $this->pos_model->getSettings();
        /*echo "<pre>";
        print_r($this->settings);die;*/
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

    /*BBQ*/

    public function getCustomerBYID($customer_id){
        $data = $this->pos_model->getCustomerBYID($customer_id);
        $this->sma->send_json($data);
    }

    public function cancelBBQ($bbqcode){
        $data = $this->pos_model->cancelBBQ($bbqcode);
        $this->sma->send_json($data);
    }

    public function bbq($sid = null)
    {
        $t = $this->sma->checkPermissions('index');

        $order = !empty($_GET['order']) ? $_GET['order'] : '';
        $table = !empty($_GET['table']) ? $_GET['table'] : '';
        $split = !empty($_GET['split']) ? $_GET['split'] : '';
        $bbq_set_id = !empty($_GET['set']) ? $_GET['set'] : '';
        $same_customer = !empty($_GET['same_customer']) ? $_GET['same_customer'] : '';

        if (!$this->pos_settings->default_biller || !$this->pos_settings->default_customer || !$this->pos_settings->default_category) {
            $this->session->set_flashdata('warning', lang('please_update_settings'));
            admin_redirect('pos/settings');
        }

        $user_group = $this->pos_model->getUserByID($this->session->userdata('user_id'));

        $gp = $this->settings_model->getGroupPermissions($user_group->group_id);

        if (($this->pos_settings->open_sale_register == 1) && (($gp->{'pos-open_sale_register'} == 1))) {

            $register = $this->pos_model->registerData($this->session->userdata('user_id'));
            $register_data = array('register_id' => $register->id, 'cash_in_hand' => $register->cash_in_hand, 'register_open_time' => $register->date);
            $this->session->set_userdata($register_data);

            if ($register) {
                $register_data = 'open';
            } else {
                $register_data = 'none';

            }
        } else {
            $register_data = 'disable';
        }

        $this->data['register_data'] = $register_data;

        /*if ($register = $this->pos_model->registerData($this->session->userdata('user_id'))) {
        $register_data = array('register_id' => $register->id, 'cash_in_hand' => $register->cash_in_hand, 'register_open_time' => $register->date);

        $this->session->set_userdata($register_data);
        } else {
        $this->session->set_flashdata('error', lang('register_not_open'));
        admin_redirect('pos/open_register');
        }*/

        $this->data['sid'] = $this->input->get('suspend_id') ? $this->input->get('suspend_id') : $sid;

        $did = $this->input->post('delete_id') ? $this->input->post('delete_id') : null;
        $suspend = $this->input->post('suspend') ? true : false;
        $count = $this->input->post('count') ? $this->input->post('count') : null;

        $duplicate_sale = $this->input->get('duplicate') ? $this->input->get('duplicate') : null;

        //validate form input
        $this->form_validation->set_rules('customer', $this->lang->line("customer"), 'trim|required');
        $this->form_validation->set_rules('warehouse', $this->lang->line("warehouse"), 'required');
        $this->form_validation->set_rules('biller', $this->lang->line("biller"), 'required');

        if (!empty($order)) {

            if ($this->form_validation->run() == true) {

                $date = date('Y-m-d H:i:s');
                $warehouse_id = $this->input->post('warehouse');
                $customer_id = $this->input->post('customer');
                $biller_id = $this->input->post('biller');
                $total_items = $this->input->post('total_items');

                $payment_term = 0;
                $due_date = date('Y-m-d', strtotime('+' . $payment_term . ' days'));
                $shipping = $this->input->post('shipping') ? $this->input->post('shipping') : 0;
                $customer_details = $this->site->getCompanyByID($customer_id);
                $customer = $customer_details->company != '-' ? $customer_details->company : $customer_details->name;
                $biller_details = $this->site->getCompanyByID($biller_id);
                $biller = $biller_details->company != '-' ? $biller_details->company : $biller_details->name;
                $note = $this->sma->clear_tags($this->input->post('pos_note'));
                $staff_note = $this->sma->clear_tags($this->input->post('staff_note'));
                $reference = $this->site->getReference('pos');

                $total = 0;
                $recipe_tax = 0;
                $recipe_discount = 0;
                $digital = false;
                $gst_data = [];
                $total_cgst = $total_sgst = $total_igst = 0;
                $i = isset($_POST['recipe_code']) ? sizeof($_POST['recipe_code']) : 0;
                for ($r = 0; $r < $i; $r++) {

                    $item_addon = isset($_POST['recipe_addon'][$r]) && $_POST['recipe_addon'][$r] != 'false' ? $_POST['recipe_addon'][$r] : null;

                    $item_id = $_POST['recipe_id'][$r];
                    $item_type = $_POST['recipe_type'][$r];
                    $item_code = $_POST['recipe_code'][$r];

                    $buy_id = $_POST['buy_id'][$r];
                    $buy_quantity = $_POST['buy_quantity'][$r];
                    $get_item = $_POST['get_item'][$r];
                    $get_quantity = $_POST['get_quantity'][$r];
                    $total_get_quantity = $_POST['total_get_quantity'][$r];

                    $item_name = $_POST['recipe_name'][$r];
                    $item_comment = $_POST['recipe_comment'][$r];
                    $item_option = isset($_POST['recipe_option'][$r]) && $_POST['recipe_option'][$r] != 'false' ? $_POST['recipe_option'][$r] : null;
                    $real_unit_price = $this->sma->formatDecimal($_POST['real_unit_price'][$r]);
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
                        $pr_discount = $this->site->calculateDiscount($item_discount, $unit_price);
                        $unit_price = $this->sma->formatDecimal($unit_price - $pr_discount);
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

                        $recipe = array(
                            'recipe_id' => $item_id,
                            'recipe_code' => $item_code,
                            'recipe_name' => $item_name,
                            'recipe_type' => $item_type,
                            'option_id' => $item_option,
                            'addon_id' => $item_addon,
                            'buy_id' => $buy_id,
                            'buy_quantity' => $buy_quantity,
                            'get_item' => $get_item,
                            'get_quantity' => $get_quantity,
                            'total_get_quantity' => $total_get_quantity,
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
                            'subtotal' => $this->sma->formatDecimal($subtotal),
                            'serial_no' => $item_serial,
                            'real_unit_price' => $real_unit_price,
                            'comment' => $item_comment,
                        );

                        $recipe[] = ($recipe + $gst_data);
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
                $data = array('date' => $date,
                    'reference_no' => $reference,
                    'customer_id' => $customer_id,
                    'customer' => $customer,
                    'biller_id' => $biller_id,
                    'biller' => $biller,
                    'warehouse_id' => $warehouse_id,
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
                    'sale_status' => 'Process',
                    'payment_status' => $payment_status,
                    'payment_term' => $payment_term,
                    'rounding' => $rounding,
                    'suspend_note' => $this->input->post('suspend_note'),
                    'pos' => 1,
                    'paid' => $this->input->post('amount-paid') ? $this->input->post('amount-paid') : 0,
                    'created_by' => $this->session->userdata('user_id'),
                    'hash' => hash('sha256', microtime() . mt_rand()),
                );
                if ($this->Settings->indian_gst) {
                    $data['cgst'] = $total_cgst;
                    $data['sgst'] = $total_sgst;
                    $data['igst'] = $total_igst;
                }

                if (!$suspend) {
                    $p = isset($_POST['amount']) ? sizeof($_POST['amount']) : 0;
                    $paid = 0;
                    for ($r = 0; $r < $p; $r++) {
                        if (isset($_POST['amount'][$r]) && !empty($_POST['amount'][$r]) && isset($_POST['paid_by'][$r]) && !empty($_POST['paid_by'][$r])) {
                            $amount = $this->sma->formatDecimal($_POST['balance_amount'][$r] > 0 ? $_POST['amount'][$r] - $_POST['balance_amount'][$r] : $_POST['amount'][$r]);
                            if ($_POST['paid_by'][$r] == 'deposit') {
                                if (!$this->site->check_customer_deposit($customer_id, $amount)) {
                                    $this->session->set_flashdata('error', lang("amount_greater_than_deposit"));
                                    redirect($_SERVER["HTTP_REFERER"]);
                                }
                            }
                            if ($_POST['paid_by'][$r] == 'gift_card') {
                                $gc = $this->site->getGiftCardByNO($_POST['paying_gift_card_no'][$r]);
                                $amount_paying = $_POST['amount'][$r] >= $gc->balance ? $gc->balance : $_POST['amount'][$r];
                                $gc_balance = $gc->balance - $amount_paying;
                                $payment[] = array(
                                    'date' => $date,
                                    // 'reference_no' => $this->site->getReference('pay'),
                                    'amount' => $amount,
                                    'paid_by' => $_POST['paid_by'][$r],
                                    'cheque_no' => $_POST['cheque_no'][$r],
                                    'cc_no' => $_POST['paying_gift_card_no'][$r],
                                    'cc_holder' => $_POST['cc_holder'][$r],
                                    'cc_month' => $_POST['cc_month'][$r],
                                    'cc_year' => $_POST['cc_year'][$r],
                                    'cc_type' => $_POST['cc_type'][$r],
                                    'cc_cvv2' => $_POST['cc_cvv2'][$r],
                                    'created_by' => $this->session->userdata('user_id'),
                                    'type' => 'received',
                                    'note' => $_POST['payment_note'][$r],
                                    'pos_paid' => $_POST['amount'][$r],
                                    'pos_balance' => $_POST['balance_amount'][$r],
                                    'gc_balance' => $gc_balance,
                                );

                            } else {
                                $payment[] = array(
                                    'date' => $date,
                                    // 'reference_no' => $this->site->getReference('pay'),
                                    'amount' => $amount,
                                    'paid_by' => $_POST['paid_by'][$r],
                                    'cheque_no' => $_POST['cheque_no'][$r],
                                    'cc_no' => $_POST['cc_no'][$r],
                                    'cc_holder' => $_POST['cc_holder'][$r],
                                    'cc_month' => $_POST['cc_month'][$r],
                                    'cc_year' => $_POST['cc_year'][$r],
                                    'cc_type' => $_POST['cc_type'][$r],
                                    'cc_cvv2' => $_POST['cc_cvv2'][$r],
                                    'created_by' => $this->session->userdata('user_id'),
                                    'type' => 'received',
                                    'note' => $_POST['payment_note'][$r],
                                    'pos_paid' => $_POST['amount'][$r],
                                    'pos_balance' => $_POST['balance_amount'][$r],
                                );

                            }

                        }
                    }
                }
                if (!isset($payment) || empty($payment)) {
                    $payment = array();
                }

                // $this->sma->print_arrays($data, $recipe, $payment);
            }

            if ($this->form_validation->run() == true && !empty($recipe) && !empty($data)) {
                if ($suspend) {
                    if ($this->pos_model->suspendSale($data, $recipe, $did)) {
                        $this->session->set_userdata('remove_posls', 1);
                        $this->session->set_flashdata('message', $this->lang->line("sale_suspended"));
                        admin_redirect("pos");
                    }
                } else {
                    if ($sale = $this->pos_model->addSale($data, $recipe, $payment, $did)) {
                        $this->session->set_userdata('remove_posls', 1);
                        $msg = $this->lang->line("sale_added");
                        if (!empty($sale['message'])) {
                            foreach ($sale['message'] as $m) {
                                $msg .= '<br>' . $m;
                            }
                        }
                        $this->session->set_flashdata('message', $msg);
                        $redirect_to = $this->pos_settings->after_sale_page ? "pos" : "pos/view/" . $sale['sale_id'];
                        if ($this->pos_settings->auto_print) {
                            if ($this->Settings->remote_printing != 1) {
                                $redirect_to .= '?print=' . $sale['sale_id'];
                            }
                        }
                        admin_redirect($redirect_to);
                    }
                }
            } else {
                $this->data['old_sale'] = null;
                $this->data['oid'] = null;
                if ($duplicate_sale) {
                    if ($old_sale = $this->pos_model->getInvoiceByID($duplicate_sale)) {
                        $inv_items = $this->pos_model->getSaleItems($duplicate_sale);
                        $this->data['oid'] = $duplicate_sale;
                        $this->data['old_sale'] = $old_sale;
                        $this->data['message'] = lang('old_sale_loaded');
                        $this->data['customer'] = $this->pos_model->getCompanyByID($old_sale->customer_id);
                    } else {
                        $this->session->set_flashdata('error', lang("bill_x_found"));
                        admin_redirect("pos");
                    }
                }
                $this->data['suspend_sale'] = null;
                if ($sid) {
                    if ($suspended_sale = $this->pos_model->getOpenBillByID($sid)) {
                        $inv_items = $this->pos_model->getSuspendedSaleItems($sid);
                        $this->data['sid'] = $sid;
                        $this->data['suspend_sale'] = $suspended_sale;
                        $this->data['message'] = lang('suspended_sale_loaded');
                        $this->data['customer'] = $this->pos_model->getCompanyByID($suspended_sale->customer_id);
                        $this->data['reference_note'] = $suspended_sale->suspend_note;
                    } else {
                        $this->session->set_flashdata('error', lang("bill_x_found"));
                        admin_redirect("pos");
                    }
                }

                if (($sid || $duplicate_sale) && $inv_items) {
                    // krsort($inv_items);
                    $c = rand(100000, 9999999);
                    foreach ($inv_items as $item) {
                        $row = $this->site->getrecipeByID($item->recipe_id);

                        $buy = $this->site->checkBuyget($row->id);
                        if (!empty($buy)) {
                            $row->buy_id = $buy->id;
                            $row->buy_quantity = $buy->buy_quantity;
                            $row->get_item = $buy->get_item;
                            $row->get_quantity = $buy->get_quantity;
                            $row->total_get_quantity = $buy->get_quantity;
                            $total_quantity = $x_quantity % $y_quantity;
                            $x_quantity = ($x_quantity - $total_quantity) / $y_quantity;
                            $total_get_quantity = $x_quantity * $b_quantity;
                            $row->total_get_quantity = $total_get_quantity;

                            $row->free_recipe = $buy->free_recipe;
                        } else {
                            $row->buy_id = 0;
                            $row->get_item = 0;
                            $row->buy_quantity = 0;
                            $row->get_quantity = 0;
                            $row->total_get_quantity = 0;
                            $row->free_recipe = '';
                        }

                        if (!$row) {
                            $row = json_decode('{}');
                            $row->tax_method = 0;
                            $row->quantity = 0;
                        } else {
                            $category = $this->site->getCategoryByID($row->category_id);
                            $row->category_name = $category->name;
                            unset($row->cost, $row->details, $row->recipe_details, $row->image, $row->barcode_symbology, $row->cf1, $row->cf2, $row->cf3, $row->cf4, $row->cf5, $row->cf6, $row->supplier1price, $row->supplier2price, $row->cfsupplier3price, $row->supplier4price, $row->supplier5price, $row->supplier1, $row->supplier2, $row->supplier3, $row->supplier4, $row->supplier5, $row->supplier1_part_no, $row->supplier2_part_no, $row->supplier3_part_no, $row->supplier4_part_no, $row->supplier5_part_no);
                        }
                        $pis = $this->site->getPurchasedItems($item->recipe_id, $item->warehouse_id, $item->option_id);
                        if ($pis) {
                            foreach ($pis as $pi) {
                                $row->quantity += $pi->quantity_balance;
                            }
                        }
                        $row->id = $item->recipe_id;
                        $row->code = $item->recipe_code;
                        $row->name = $item->recipe_name;
                        $row->type = $item->recipe_type;
                        $row->quantity += $item->quantity;
                        $row->discount = $item->discount ? $item->discount : '0';
                        $row->price = $this->sma->formatDecimal($item->net_unit_price + $this->sma->formatDecimal($item->item_discount / $item->quantity));
                        $row->unit_price = $row->tax_method ? $item->unit_price + $this->sma->formatDecimal($item->item_discount / $item->quantity) + $this->sma->formatDecimal($item->item_tax / $item->quantity) : $item->unit_price + ($item->item_discount / $item->quantity);
                        $row->real_unit_price = $item->real_unit_price;
                        $row->base_quantity = $item->quantity;
                        $row->base_unit = isset($row->unit) ? $row->unit : $item->recipe_unit_id;
                        $row->base_unit_price = $row->price ? $row->price : $item->unit_price;
                        $row->unit = $item->recipe_unit_id;
                        $row->qty = $item->unit_quantity;
                        $row->tax_rate = $item->tax_rate_id;
                        $row->serial = $item->serial_no;
                        $row->option = $item->option_id;
                        $row->addon = $item->addon_id;
                        $options = $this->pos_model->getrecipeOptions($row->id, $item->warehouse_id);
                        $addons = $this->pos_model->getrecipeAddons($row->id);
						

                        if ($options) {
                            $option_quantity = 0;
                            foreach ($options as $option) {
                                $pis = $this->site->getPurchasedItems($row->id, $item->warehouse_id, $item->option_id);
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

                        $row->comment = isset($item->comment) ? $item->comment : '';
                        $row->ordered = 1;
                        $combo_items = false;
                        if ($row->type == 'combo') {
                            $combo_items = $this->pos_model->getrecipeComboItems($row->id, $item->warehouse_id);
                        }
                        $units = $this->site->getUnitsByBUID($row->base_unit);
                        $tax_rate = $this->site->getTaxRateByID($row->tax_rate);
                        $ri = $this->Settings->item_addition ? $row->id : $c;

                        $pr[$ri] = array('id' => $c, 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")",
                            'row' => $row, 'combo_items' => $combo_items, 'tax_rate' => $tax_rate, 'units' => $units, 'options' => $options, 'addons' => $addons);
                        $c++;
                    }

                    $this->data['items'] = json_encode($pr);

                } else {
                    $this->data['customer'] = $this->pos_model->getCompanyByID($this->pos_settings->default_customer);
                    $this->data['reference_note'] = null;
                }

                $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
                $this->data['message'] = isset($this->data['message']) ? $this->data['message'] : $this->session->flashdata('message');

                // $this->data['biller'] = $this->site->getCompanyByID($this->pos_settings->default_biller);
                $this->data['billers'] = $this->site->getAllCompanies('biller');
                $this->data['sales_types'] = $this->site->getAllSalestype();
                $this->data['warehouses'] = $this->site->getAllWarehouses();
                $this->data['tax_rates'] = $this->site->getAllTaxRates();
                $this->data['user'] = $this->site->getUser();
                $this->data["tcp"] = $this->pos_model->bbqrecipe_count($bbq_set_id, $this->session->userdata('warehouse_id'), $this->pos_settings->default_category);
                $sales_type = $order;
                $sales_type = $this->pos_model->getBBQLobsterSaletype($split);
                if (!empty($sales_type)) {
                    $sales_type = $sales_type;
                }
                $this->data["sub_cat"] = $this->site->getrecipeSubCategories($this->pos_settings->default_category);
                $this->data['recipe'] = $this->ajaxbbqrecipe($this->pos_settings->default_category, $this->session->userdata('warehouse_id'), $this->data["sub_cat"][0]->id, $brand_id = null, $sales_type);

                // $this->data['recipe'] = $this->ajaxbbqrecipe($bbq_set_id, $this->session->userdata('warehouse_id'),$this->pos_settings->default_category);
                // $this->data['categories'] = $this->site->getAllrecipeCategories_withdays($sales_type);
                if ($this->pos_settings->sales_item_in_pos == 1) {
                    $this->data['categories'] = $this->site->getAllrecipeCategories();
                } else { //by day wise item mappings
                    $this->data['categories'] = $this->site->getAllrecipeCategories_withdays($sales_type);
                    $category_id = $this->data['categories'][0]->id ? $this->data['categories'][0]->id : null;
                }
                $this->data['brands'] = $this->site->getAllBrands();
                if ($this->pos_settings->sales_item_in_pos == 1) {
                    $this->data['subcategories'] = $this->site->getrecipeSubCategories($this->pos_settings->default_category);
                } else { // sub category list from mapping table with active items in recipe table
                    $this->data['subcategories'] = $this->site->getrecipeSubCategories_withdays($category_id, $sales_type);
                }
                // $this->data['subcategories'] = $this->site->getrecipeSubCategories_withdays($this->pos_settings->default_category,$sales_type);
                $this->data['printer'] = $this->pos_model->getPrinterByID($this->pos_settings->printer);
                $order_printers = json_decode($this->pos_settings->order_printers);
                $printers = array();
                if (!empty($order_printers)) {
                    foreach ($order_printers as $printer_id) {
                        $printers[] = $this->pos_model->getPrinterByID($printer_id);
                    }
                }
                $this->data['order_printers'] = $printers;
                $this->data['pos_settings'] = $this->pos_settings;
                $table_area = 2;
                $this->data['areas'] = $this->pos_model->getBBQTablelist($this->session->userdata('warehouse_id'));

                $this->data['get_table'] = $table;
                $this->data['get_order_type'] = $order;
                $this->data['get_split'] = $split;
                $this->data['bbq_set_id'] = $bbq_set_id;
                $this->data['same_customer'] = $same_customer;

                if ($this->pos_settings->after_sale_page && $saleid = $this->input->get('print', true)) {
                    if ($inv = $this->pos_model->getInvoiceByID($saleid)) {
                        $this->load->helper('pos');
                        if (!$this->session->userdata('view_right')) {
                            $this->sma->view_rights($inv->created_by, true);
                        }
                        $this->data['rows'] = $this->pos_model->getAllInvoiceItems($inv->id);
                        $this->data['biller'] = $this->pos_model->getCompanyByID($inv->biller_id);
                        $this->data['customer'] = $this->pos_model->getCompanyByID($inv->customer_id);
                        $this->data['payments'] = $this->pos_model->getInvoicePayments($inv->id);
                        $this->data['return_sale'] = $inv->return_id ? $this->pos_model->getInvoiceByID($inv->return_id) : null;
                        $this->data['return_rows'] = $inv->return_id ? $this->pos_model->getAllInvoiceItems($inv->return_id) : null;
                        $this->data['return_payments'] = $this->data['return_sale'] ? $this->pos_model->getInvoicePayments($this->data['return_sale']->id) : null;
                        $this->data['inv'] = $inv;
                        $this->data['print'] = $inv->id;

                        $this->data['created_by'] = $this->site->getUser($inv->created_by);
                    }
                }

                $this->data['rcustomer'] = $this->site->getAllCompanies('customer');
                $this->load->view($this->theme . 'pos/bbq', $this->data);
            }

        } else {
            admin_redirect("pos/bbq_tables/?order=4");
        }

    }

    // public function ajaxbbqrecipe($bbq_set_id = NULL, $warehouse_id = NULL)
    public function ajaxbbqrecipe($category_id = null, $warehouse_id = null, $subcategory_id = null, $brand_id = null, $order_type = null)
    {
        $this->sma->checkPermissions('index');

        if ($this->input->get('category_id')) {
            $category_id = $this->input->get('category_id');
        } else {
            $category_id = $this->pos_settings->default_category;
        }
        if ($this->input->get('subcategory_id')) {
            $subcategory_id = $this->input->get('subcategory_id');
        } else {
            $subcategory_id = $subcategory_id;
        }

        if ($this->input->get('warehouse_id')) {
            $warehouse_id = $this->input->get('warehouse_id');
        } else {
            $warehouse_id = $warehouse_id;
        }
        if ($this->input->get('order_type')) {
            $order_type = $this->input->get('order_type');
        }

        if ($this->input->get('split')) {
            $split_id = $this->input->get('split');
            $sales_type = $this->pos_model->getBBQLobsterSaletype($split_id);
            if (!empty($sales_type)) {
                $order_type = $sales_type;
            }
        }

        /*if ($this->input->get('bbq_set_id')) {
        $bbq_set_id = $this->input->get('bbq_set_id');
        } else {
        $bbq_set_id = $bbq_set_id;
        }*/

        // if ($this->input->get('per_page') == 'n') {
        if ($this->input->get('per_page') == 'n' || $this->input->get('per_page') == '0') {
            $page = 0;
        } else {
            $page = $this->input->get('per_page');
        }

        /* if ($this->input->get('per_page') == 'n' || $this->input->get('per_page') == '0' ) {
        $page = 0;
        } else {
        $page = $this->input->get('per_page');
        }*/

        $this->load->library("pagination");

        $config = array();
        $config["base_url"] = base_url() . "pos/ajaxbbqrecipe";

        // $config["total_rows"] = $this->pos_model->bbqrecipe_count($category_id, $warehouse_id,$category_id);
        if ($this->pos_settings->sales_item_in_pos == 1) {
            $config["total_rows"] = $this->pos_model->recipe_count($category_id, $warehouse_id, $subcategory_id, $brand_id);
        } else {
            $config["total_rows"] = $this->pos_model->recipe_count_withdays($category_id, $warehouse_id, $subcategory_id, $brand_id, $order_type);
        }
        // $config["total_rows"] = $this->pos_model->recipe_count_withdays($category_id, $warehouse_id, $subcategory_id, $brand_id,$order_type);
        // $config["per_page"] = $this->Settings->bbq_display_items;
        $config["per_page"] = $this->pos_settings->pro_limit;

        $config['prev_link'] = false;
        $config['next_link'] = false;
        $config['display_pages'] = false;
        $config['first_link'] = false;
        $config['last_link'] = false;

        $this->pagination->initialize($config);

        if ($this->pos_settings->sales_item_in_pos == 1) {
            $recipe = $this->pos_model->fetch_recipe($category_id, $warehouse_id, $config["per_page"], $page, $subcategory_id, $brand_id);
        } else {
            $recipe = $this->pos_model->fetch_recipe_withdays($category_id = null, $warehouse_id, $config["per_page"], $page, $subcategory_id = null, $brand_id = null, $order_type);
        }

        //$recipe = $this->pos_model->fetch_recipe_withdays($category_id, $warehouse_id, $config["per_page"], $page, $subcategory_id, $brand_id,$order_type);
        // $recipe = $this->pos_model->bbqfetch_recipe($category_id, $warehouse_id, $config["per_page"], $page, $subcategory_id);

        $pro = 1;
        $prods = '<div>';
        if (!empty($recipe)) {
            foreach ($recipe as $recipe) {

                $count = $recipe->id;
                $buy = $this->site->checkBuyget($recipe->id);

                $default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
                $default_currency_rate = $default_currency_data->rate;
                $default_currency_symbol = $default_currency_data->symbol;

                if (!empty($buy)) {

                    if ($buy->buy_method == 'buy_x_get_x') {
                        $buyvalue = "<div class='rippon'><div class='offer_list'><span>Buy " . $buy->buy_quantity . "  Get " . $buy->get_quantity . " " . $buy->free_recipe . " </span></div></div>";
                    } elseif ($buy->buy_method == 'buy_x_get_y') {
                        $buyvalue = "<div class='rippon'><div class='offer_list'><span>Buy " . $buy->buy_quantity . "  Get " . $buy->free_recipe . " ( " . $buy->get_quantity . ")</span></div></div>";
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

                // $varients = $this->pos_model->isVarientExist($recipe->id);

                /*$prods .= "<button id=\"recipe-" . $category_id . $count . "\" type=\"button\" value='" . $recipe->code . "' title=\"" . $recipe->name . "\" class=\"btn-prni btn-" . $this->pos_settings->recipe_button_color . " recipe pos-tip\" data-container=\"body\"><img src=\"" . base_url() . "assets/uploads/thumbs/" . $recipe->image . "\" alt=\"" . $recipe_name . "\" class='img-rounded' />";

                $prods .=  "<br><span class='price_strong'> ".$default_currency_symbol ."" . $this->sma->formatDecimal($recipe->price). "</span>".$buyvalue." </button>";*/

                /* $prods .= "<button id=\"recipe-" . $category_id . $count . "\" type=\"button\" value='" . $recipe->code . "' title=\"" . $recipe->name . "\" class=\"btn-prni btn-" . $this->pos_settings->recipe_button_color . " recipe pos-tip\" data-container=\"body\">";

                if(strlen($recipe->name) < 20){

                $prods .= "<span class='name_strong'>" .$recipe_name. "</span>";
                }else{
                $prods .= "<marquee class='name_strong' behavior='alternate' direction='left' scrollamount='1'>&nbsp;&nbsp;" .$recipe_name. "&nbsp;&nbsp;</marquee>";
                }
                 */

                if (!empty($varients)) {
                    $class = "has-varients";
                    $vari = '<div class="variant-popup" style="display: none;">';
                    foreach ($varients as $k => $varient) {
                        $vari .= '<button data-id="' . $varient->variant_id . '" id="recipe-' . $category_id . $count . '" type="button" value="' . $recipe->id . '" title="" class="btn-default  recipe-varient pos-tip" data-container="body" data-original-title="' . $varient->name . '" tabindex="-1">';
                        if (strlen($varient->name) < 10) {
                            $vari .= "<span class='name_strong'>" . $varient->name . "</span>";
                        } else {
                            $vari .= '<marquee class="name_strong" behavior="alternate" direction="left" scrollamount="1">
								&nbsp;&nbsp;' . $varient->name . '&nbsp;&nbsp;</marquee>';
                        }
                        $vari .= '<br>
							<span class="price_strong"> ' . $default_currency_symbol . $this->sma->formatDecimal($varient->price) . '</span> </button>';
                    }
                    $vari .= '</div>';
                }$activemode=($recipe->active ==2)?'style="background-color:#E1A87C !important"':'';
				$activemode_class=($recipe->active ==2)?'non_transaction':'';

                $prods .= "<span><button id=\"recipe-" . $category_id . $count . "\" type=\"button\" value='" . $recipe->id . "' title=\"" . $recipe->name . "\" class=\"btn-prni btn-" . $this->pos_settings->recipe_button_color . " " . $class . " recipe pos-tip ".$activemode_class."\" data-container=\"body\"><img src=\"" . base_url() . "assets/uploads/thumbs/" . $recipe->image . "\" alt=\"" . $recipe_name . "\" class='img-rounded' />";

                if (strlen($recipe->name) < 10) {

                    $prods .= "<span class='name_strong'>" . $recipe_name . "</span>";
                } else {
                    $prods .= "<marquee class='name_strong' behavior='alternate' direction='left' scrollamount='1'>&nbsp;&nbsp;" . $recipe_name . "&nbsp;&nbsp;</marquee>";
                }

                // $prods .=  "<br><span class='price_strong'> ".$default_currency_symbol ."" . $this->sma->formatDecimal($recipe->price). "</span>".$buyvalue." </button>";
                $prods .= "<br></button>";
                $prods .= $vari . '</span>';
                $pro++;
            }
        }
        $prods .= "</div>";

        if ($this->input->get('per_page') != null) {
            echo $prods;
        } else {
            return $prods;
        }
    }
    public function BBQcode()
    {
        $coversLimit = $this->data['settings']->bbq_covers_limit;
        $reference_no = $_GET['bbqcode'];
        $bbq = $this->site->getBBQdataCode($reference_no);

        $html = '<div class="col-md-12">

                    <input type="hidden" name="reference_no" id="reference_no" value="">

                    <div class="form-group kids_price col-lg-6">
                        ' . lang("reference_no", "reference_no") . form_input('reference_no', $bbq->reference_no, 'class="form-control tip" readonly id="reference_no" ') . '
                    </div>
                     <div class="form-group kids_price col-lg-6">
                        ' . lang("table_name", "table_name") . form_input('table_name', $bbq->table_name, 'class="form-control tip" readonly id="table_name" ') . '
                    </div>

                    <div class="form-group kids_price col-lg-4">
                        ' . lang("number_of_adult", "number_of_adult") . '
                        <select name="number_of_adult" class="form-control" id="number_of_adult" required>';
        for ($j = 1; $j <= $coversLimit; $j++) {
            if ($bbq->number_of_adult == $j) {
                $selected = "selected";
            } else {
                $selected = "";
            }
            $html .= '<option value="' . $j . '" ' . $selected . '>' . $j . '</option>';
        }
        $html .= '</select>
                    </div>

                    <div class="form-group kids_price col-lg-4">
                        ' . lang("number_of_child", "number_of_child") . '
						 <select name="number_of_child" class="form-control" id="number_of_child">';
        for ($j = 0; $j <= $coversLimit; $j++) {
            if ($bbq->number_of_child == $j) {
                $selected = "selected";
            } else {
                $selected = "";
            }
            $html .= '<option value="' . $j . '" ' . $selected . '>' . $j . '</option>';
        }
        $html .= '</select>

                    </div>

                    <div class="form-group kids_price col-lg-4">
                        ' . lang("number_of_kids", "number_of_kids") . '
						 <select name="number_of_kids" class="form-control" id="number_of_kids">';
        for ($j = 0; $j <= $coversLimit; $j++) {
            if ($bbq->number_of_kids == $j) {
                $selected = "selected";
            } else {
                $selected = "";
            }
            $html .= '<option value="' . $j . '" ' . $selected . '>' . $j . '</option>';
        }
        $html .= '</select>

                    </div>



                </div>';

        echo $html;
    }

    public function edit_bbq()
    {
        $reference_no = $this->input->post('reference_no');
        $bbq_array = array(
            'number_of_adult' => $this->input->post('number_of_adult') ? $this->input->post('number_of_adult') : 1,
            'number_of_child' => $this->input->post('number_of_child') ? $this->input->post('number_of_child') : 0,
            'number_of_kids' => $this->input->post('number_of_kids') ? $this->input->post('number_of_kids') : 0,
        );
        $response = $this->pos_model->updateBBQ($bbq_array, $reference_no);
        if ($response) {
            redirect($_SERVER["HTTP_REFERER"]);
        }
        return false;
    }

    public function add_bbq()
    {
        $this->form_validation->set_rules('warehouse_id', $this->lang->line("warehouse"), 'required');
        $bbq = $this->site->CreateBBQSplitID($this->session->userdata('user_id'));
        if ($this->form_validation->run() == true) {
            $array_bbq = array(
                'reference_no' => $bbq,
                'warehouse_id' => $this->input->post('warehouse_id'),
                'bbq_menu_id' => $this->input->post('bbq_menu_id'),
                'table_id' => $this->input->post('table_id'),
                'name' => $this->input->post('name') ? $this->input->post('name') : null,
                'phone' => $this->input->post('phone') ? $this->input->post('phone') : null,
                'email' => $this->input->post('email_address') ? $this->input->post('email_address') : '',
                'number_of_adult' => $this->input->post('number_of_adult'),
                'number_of_child' => $this->input->post('number_of_child'),
                'number_of_kids' => $this->input->post('number_of_kids'),
                'bbq_set_id' => $this->input->post('bbq_set_id'),
                'adult_price' => $this->input->post('adult_price'),
                'child_price' => $this->input->post('child_price'),
                'kids_price' => $this->input->post('kids_price'),
                'status' => 'Open',
                'payment_status' => '',
                'created_by' => $this->session->userdata('user_id'),
                'confirmed_by' => $this->session->userdata('user_id'),
                'created_on' => date('Y-m-d H:i:s'),
            );

            $array_customer = array(
                'ref_id' => 'CUS-' . date('YmdHis'),
                //'company' => $this->input->post('company') ? $this->input->post('company') : NULL,
                'name' => $this->input->post('name') ? $this->input->post('name') : 'New Customer',
                'email' => $this->input->post('email_address') ? $this->input->post('email_address') : '',
                'phone' => $this->input->post('phone') ? $this->input->post('phone') : null,
                //'address' => $this->input->post('address') ? $this->input->post('address') : NULL,
                //'city' => $this->input->post('city') ? $this->input->post('city') : NULL,
                //'state' => $this->input->post('state') ? $this->input->post('state') : NULL,
                //'postal_code' => $this->input->post('postal_code') ? $this->input->post('postal_code') : NULL,
                //'country' => $this->input->post('country') ? $this->input->post('country') : NULL,
                'group_id' => 3,
                'group_name' => 'customer',
            );

            $customer_id = $this->input->post('customer_id') ? $this->input->post('customer_id') : null;

            //$this->sma->print_arrays($array_bbq, $table, $array_customer, $customer_id);

        }
        $result = $this->pos_model->addBBQ($array_bbq, $array_customer, $customer_id);
        if ($this->form_validation->run() == true && !empty($result)) {

            $this->session->set_flashdata('message', 'BBQ Added Success');
            admin_redirect("pos/bbq?order=4&table=" . $result->table_id . "&split=" . $result->reference_no . "&same_customer=" . $result->customer_id . "&set=" . $result->bbq_set_id . "&bbqtid=" . $result->table_id);

        } else {
            $this->session->set_flashdata('error', 'BBQ Not Added');
            admin_redirect("pos/bbq_tables/?order=4");
        }

    }

    public function demo_tables()
    {
        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['message'] = isset($this->data['message']) ? $this->data['message'] : $this->session->flashdata('message');

        // $this->data['biller'] = $this->site->getCompanyByID($this->pos_settings->default_biller);
        $this->data['billers'] = $this->site->getAllCompanies('biller');
        $this->data['sales_types'] = $this->site->getAllSalestype();
        $this->data['tables'] = $this->site->getAllTables();
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['tax_rates'] = $this->site->getAllTaxRates();
        $this->data['user'] = $this->site->getUser();
        $this->data["tcp"] = $this->pos_model->recipe_count($this->pos_settings->default_category, $this->session->userdata('warehouse_id'));
        $this->data['recipe'] = $this->ajaxrecipe($this->pos_settings->default_category, $this->session->userdata('warehouse_id'));
        $this->data['categories'] = $this->site->getAllrecipeCategories();
        $this->data['brands'] = $this->site->getAllBrands();
        $this->data['subcategories'] = $this->site->getrecipeSubCategories($this->pos_settings->default_category);

        $this->data['rcustomer'] = $this->site->getAllCompanies('customer');
        $this->data['bbq_category'] = $this->pos_model->getAllbbqCategories();

        $this->data['pos_settings'] = $this->pos_settings;
        $order = $_GET['order'] ? $_GET['order'] : 4;
        $this->data['order_type'] = $order;
        $this->data['areas'] = $this->pos_model->getBBQTablelist($this->session->userdata('warehouse_id'));
        $this->load->view($this->theme . 'pos/bbq_tables', $this->data);
    }

    public function bbq_tables()
    {
        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['message'] = isset($this->data['message']) ? $this->data['message'] : $this->session->flashdata('message');

        // $this->data['biller'] = $this->site->getCompanyByID($this->pos_settings->default_biller);
        $this->data['billers'] = $this->site->getAllCompanies('biller');
        $this->data['sales_types'] = $this->site->getAllSalestype();
        $this->data['tables'] = $this->site->getAllTables();
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['tax_rates'] = $this->site->getAllTaxRates();
        $this->data['user'] = $this->site->getUser();
        $this->data["tcp"] = $this->pos_model->recipe_count($this->pos_settings->default_category, $this->session->userdata('warehouse_id'));
        $this->data['recipe'] = $this->ajaxrecipe($this->pos_settings->default_category, $this->session->userdata('warehouse_id'));
        $this->data['categories'] = $this->site->getAllrecipeCategories();
        $this->data['brands'] = $this->site->getAllBrands();
        $this->data['subcategories'] = $this->site->getrecipeSubCategories($this->pos_settings->default_category);

        $this->data['rcustomer'] = $this->site->getAllCompanies('customer');
        $this->data['bbq_category'] = $this->pos_model->getAllbbqCategories();

        $this->data['pos_settings'] = $this->pos_settings;
        $order = $_GET['order'] ? $_GET['order'] : 4;
        $this->data['order_type'] = $order;
        $this->load->view($this->theme . 'pos/bbq_tables', $this->data);
    }

    public function ajax_bbq_tables()
    {

        $this->data['areas'] = $this->pos_model->getBBQTablelist($this->session->userdata('warehouse_id'));
        $this->load->view($this->theme . 'pos/bbq_tables_ajax', $this->data);
    }
    public function ajax_bbq_tables_byID()
    {
        $id = $this->input->post('id');
        $this->data['areas'] = $this->pos_model->getBBQTablelist_byID($id, $this->session->userdata('warehouse_id'));
        /*echo "<pre>";
        print_r($this->data['areas']);*/
        $this->load->view($this->theme . 'pos/bbq_tables_single_ajax', $this->data);
    }
    public function bbqconsolidated()
    {

        $bbq_order_type = !empty($_GET['bbq_order_type']) ? $_GET['bbq_order_type'] : '';
        $dine_order_type = !empty($_GET['dine_order_type']) ? $_GET['dine_order_type'] : '';
        $bill_type = !empty($_GET['bill_type']) ? $_GET['bill_type'] : '';
        $table_id = !empty($_GET['table']) ? $_GET['table'] : '';
        $split_id = !empty($_GET['splits']) ? $_GET['splits'] : '';
        $bils = !empty($_GET['bils']) ? $_GET['bils'] : '';
        $waiter_id = $this->session->userdata('user_id');
        $this->data['warehouse'] = $this->session->userdata('warehouse_id') ? $this->site->getWarehouseByID($this->session->userdata('warehouse_id')) : null;
        $this->data['bill_type'] = $bill_type;
        $this->data['bbq_order_type'] = $bbq_order_type;
        $this->data['dine_order_type'] = $dine_order_type;
        $this->data['bils'] = $bils;
        $this->data['table_id'] = $table_id;
        $this->data['split_id'] = $split_id;
        $this->data['tax_rates'] = $this->site->getAllTaxRates();
        $this->data['service_charge'] = $this->pos_model->getAllSericeCharges();

        if (!empty($dine_order_type)) {
            $this->data['customer_discount'] = $this->site->GetAllcostomerDiscounts();
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
            );
            $this->data['current_user'] = $this->pos_model->getUserByID($this->session->userdata('user_id'));
            if (!empty($table_id)) {
                $item_data = $this->pos_model->dinegetBil($table_id, $split_id, $this->session->userdata('user_id'));
            } else {
                $item_data = $this->pos_model->dinegetBil($table_id, $split_id, $this->session->userdata('user_id'));
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
            foreach ($item_data['order'] as $order) {
                $order_data = array('sales_type_id' => $order->order_type,
                    'sales_split_id' => $order->split_id,
                    'sales_table_id' => $order->table_id,
                    'date' => date('Y-m-d H:i:s'),
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
                    'consolidated' => 1,
                );

                $notification_array['customer_id'] = $order->customer_id;
            }

            $this->data['order_data'] = $order_data;
            $postData = $this->input->post();
            $delivery_person = $this->input->post('delivery_person_id') ? $this->input->post('delivery_person_id') : 0;
        }
        if ($bbq_order_type) {
            $this->data['bbq_discount'] = $this->site->GetAllBBQDiscounts();
        }
        $bbq_order_id = $this->pos_model->getBBQorderID($split_id);
        $split_status = $this->site->check_splitid_is_bill_generated($split_id);
        if ($split_status) {
            admin_redirect("pos/order_bbqtable");
        }
        if ($bill_type == 4) { // bbq

            if ($this->input->post('action') == "CONSOLIDATED-SUBMIT") {
                if (!empty($dine_order_type)) {
                    for ($i = 1; $i <= $this->input->post('bils'); $i++) {
                        $check_discount_amount_old = $this->input->post('split[' . $i . '][itemdiscounts]');
                        $check_order_discount_input = $this->input->post('split[' . $i . '][order_discount_input]');
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

                        if (!empty($check_discount_amount_old) || !empty($check_order_discount_input)) {
                            $check_discount = 'YES';
                        } else {
                            $check_discount = '';
                        }

                        $tot_item = $this->input->post('[split][' . $i . '][total_item]');
                        $itemdis = $this->input->post('[split][' . $i . '][discount_amount]') / $tot_item;
                        $billitem['bills_items'] = array();
                        $bills_item['recipe_name'] = $this->input->post('split[][$i][recipe_name][$i]');
                        $splitData = array();
                        foreach ($postData['split'][$i]['recipe_name'] as $key => $split) {

                            $offer_dis = 0.0000;
                            if ($this->input->post('[split][' . $i . '][tot_dis_value]')) {
                                $offer_dis = $this->site->calculate_Discount($this->input->post('[split][' . $i . '][tot_dis_value]'), ($postData['split'][$i]['subtotal'][$key] - $postData['split'][$i]['item_discount'][$key]), $this->input->post('[split][' . $i . '][item_dis]'));
                            }
                            /*314500*/

                            if ($this->input->post('[split][' . $i . '][order_discount_input]')) {
                                $subtotal = $postData['split'][$i]['subtotal'][$key];
                                $tot_dis1 = $this->input->post('[split][' . $i . '][tot_dis1]');
                                $item_dis = $postData['split'][$i]['item_dis'][$key];
                                $item_discount = $postData['split'][$i]['item_discount'][$key];

                                if ($this->Settings->customer_discount == "customer") {
                                    $recipe_id = $postData['split'][$i]['recipe_id'][$key];
                                    $finalAmt = $subtotal - $item_discount - $offer_dis;
                                    $customer_discount_status = 'applied';
                                    $discountid = $this->input->post('[split][' . $i . '][order_discount_input]');
                                    $recipeDetails = $this->pos_model->getrecipeByID($recipe_id);
                                    $group_id = $recipeDetails->category_id;
                                    $subcategory_id = $recipeDetails->subcategory_id;
                                    $input_dis = $this->pos_model->recipe_customer_discount_calculation($recipe_id, $group_id, $subcategory_id, $finalAmt, $discountid);
                                } else if ($this->Settings->customer_discount == "manual") {

                                    $input_dis = $this->site->calculate_Discount($this->input->post('[split][' . $i . '][order_discount_input]'), (($postData['split'][$i]['subtotal'][$key] - $postData['split'][$i]['item_discount'][$key]) - $offer_dis), $tot_dis1 ? $tot_dis1 : $item_dis);
                                }
                            } else {
                                $input_dis = 0;
                            }

                            $item_birday_dis = 0;
                            $birthday_discount = $this->input->post('[split][' . $i . '][birthday_discount]');
                            $total_item = $this->input->post('[split][' . $i . '][total_item]');
                            $item_birday_dis = $birthday_discount / $total_item;

                            /*item service charge */
                            $dine_item_service_charge = 0;
                            if (!empty($this->input->post('[split][' . $i . '][dine_service_charge]'))) {
                                $dine_item_service_charge = $this->site->calculateServiceCharge($this->input->post('[split][' . $i . '][dine_service_charge]'), ($postData['split'][$i]['subtotal'][$key] - ($manual_item_discount ? $manual_item_discount : 0) - ($offer_dis ? $offer_dis : 0) - ($input_dis ? $input_dis : 0) - ($postData['split'][$i]['item_discount'][$key]) - $item_birday_dis));
                            }
                            /*item service charge */

                            if ($this->input->post('[split][' . $i . '][ptax]')) {
                                $tax_type = $this->input->post('[split][' . $i . '][tax_type]');

                                if ($tax_type != 0) {

                                    $itemtax = $this->site->calculateOrderTax($this->input->post('[split][' . $i . '][ptax]'), ($postData['split'][$i]['subtotal'][$key] - ($offer_dis ? $offer_dis : 0) - ($input_dis ? $input_dis : 0) - ($postData['split'][$i]['item_discount'][$key]) - $item_birday_dis));

                                    $sub_val = $postData['split'][$i]['subtotal'][$key];

                                } else {
                                    $default_tax = $this->site->calculateOrderTax($this->input->post('[split][' . $i . '][ptax]'), ($postData['split'][$i]['subtotal'][$key] - ($offer_dis ? $offer_dis : 0) - ($input_dis ? $input_dis : 0) - ($postData['split'][$i]['item_discount'][$key])));

                                    $final_val = ($postData['split'][$i]['subtotal'][$key] - ($offer_dis ? $offer_dis : 0) - ($input_dis ? $input_dis : 0) - ($postData['split'][$i]['item_discount'][$key]) - $item_birday_dis);

                                    $subval = $final_val / (($default_tax / $final_val) + 1);

                                    $getTax = $this->site->getTaxRateByID($this->input->post('[split][' . $i . '][ptax]'));

                                    $itemtax = ($subval) * ($getTax->rate / 100);

                                    $sub_val = $postData['split'][$i]['subtotal'][$key];
                                }
                            } else {
                                $sub_val = $postData['split'][$i]['subtotal'][$key];
                            }

                            // $input_dis =  $input_dis + $item_birday_dis;
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
                                'input_discount' => $input_dis ? $input_dis : 0,
                                'birthday_discount' => $item_birday_dis,
                                'tax_type' => $this->input->post('[split][' . $i . '][tax_type]'),
                                'tax' => $itemtax,
                                'subtotal' => $sub_val,

                                'service_charge_id' => $postData['split'][$i]['dine_service_charge'][$key] ? $postData['split'][$i]['dine_service_charge'][$key] : 0,
                                'service_charge_amount' => $dine_item_service_charge,

                            );
                        }
                        if ($this->input->post('[split][' . $i . '][order_discount_input]')) {
                            $cus_discount_type = $this->Settings->customer_discount;
                            $cus_discount_val = '';
                            if ($this->Settings->customer_discount == "customer") {
                                $cus_discount_val = $this->input->post('[split][' . $i . '][order_discount_input]') . '%';
                                $customer_discount_id = $this->input->post('[split][' . $i . '][order_discount_input]');
                            } else if ($this->Settings->customer_discount == "manual") {
                                $cus_discount_val = $this->input->post('[split][' . $i . '][order_discount_input]');
                            }
                        } else {
                            $cus_discount_val = '';
                            $cus_discount_type = '';
                        }

                        $billData[$i] = array(
                            'reference_no' => $this->input->post('[split][' . $i . '][reference_no]'),
                            'date' => date('Y-m-d H:i:s'),
                            'customer_id' => $this->input->post('[split][' . $i . '][customer_id]'),
                            'customer' => $this->input->post('[split][' . $i . '][customer]'),
                            'biller' => $this->input->post('[split][' . $i . '][biller]'),
                            'biller_id' => $this->input->post('[split][' . $i . '][biller_id]'),
                            'total_items' => $this->input->post('[split][' . $i . '][total_item]'),
                            'total' => $this->input->post('[split][' . $i . '][total_price]'),
                            'total_tax' => $this->input->post('[split][' . $i . '][tax_amount]'),
                            'tax_type' => $this->input->post('[split][' . $i . '][tax_type]'),
                            'tax_id' => $this->input->post('[split][' . $i . '][ptax]'),
                            'total_discount' => (($this->input->post('[split][' . $i . '][itemdiscounts]')) + ($this->input->post('[split][' . $i . '][offer_dis]')) + ($this->input->post('[split][' . $i . '][discount_amount]')) + ($this->input->post('[split][' . $i . '][off_discount]') ? $this->input->post('[split][' . $i . '][off_discount]') : 0)),
                            'birthday_discount' => $this->input->post('[split][' . $i . '][birthday_discount]') ? $this->input->post('[split][' . $i . '][birthday_discount]') : 0,
                            'grand_total' => $this->input->post('[split][' . $i . '][grand_total]'),
                            'round_total' => $this->input->post('[split][' . $i . '][round_total]'),
                            'bill_type' => $bill_type,
                            'delivery_person_id' => $delivery_person,
                            'order_discount_id' => $this->input->post('[split][' . $i . '][tot_dis_id]') ? $this->input->post('[split][' . $i . '][tot_dis_id]') : null,
                            'warehouse_id' => $this->session->userdata('warehouse_id'),
                            'created_by' => $this->session->userdata('user_id'),
                            'customer_discount_id' => $customer_discount_id ? $customer_discount_id : 0,
                            'discount_type' => $cus_discount_type,
                            'discount_val' => $cus_discount_val,
                            'consolidated' => 1,
                            'order_type' => 1,
                            'service_charge_id' => $this->input->post('[split][' . $i . '][dine_service_charge]') ? $this->input->post('[split][' . $i . '][dine_service_charge]') : 0,
                            'service_charge_amount' => $this->input->post('[split][' . $i . '][dine_service_amount]') ? $this->input->post('[split][' . $i . '][dine_service_amount]') : 0,

                        );
                        if (isset($_POST['unique_discount'])) {
                            $billData[$i]['unique_discount'] = 1;
                        }

                    }
                    $sales_total = array_column($billData, 'grand_total');
                    $sales_total = array_sum($sales_total);

                    $dine_in_discount = $this->input->post('dine_in_discount');
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
                    /*echo "<pre>";
                    // print_r($this->input->post());
                    print_r($billData);
                    print_r($splitData);die;*/
                    $dine_response = $this->pos_model->InsertBill($order_data, $order_item, $billData, $splitData, $sales_total, $delivery_person, $timelog_array, $notification_array, $order_item_id, $split_id, $request_discount, $dine_in_discount, $birthday);
                }
                if (!empty($bbq_order_type)) {
                    for ($i = 0; $i < $this->input->post('bils'); $i++) {
                        if ($_POST['number_of_covers'][$i] != 0) {
                            $bbq_discount_amount[] = $_POST['bbq_discount_amount'][$i];
                            $tax_amount[] = $_POST['tax_amount'][$i];
                            $total_amount[] = $_POST['total_amount'][$i];
                            $gtotal[] = $_POST['gtotal'][$i];

                            $adult_price[] = $_POST['adult_price'][$i];
                            $number_of_adult[] = $_POST['number_of_adult'][$i];
                            $adult_subprice[] = $_POST['adult_subprice'][$i];

                            $child_price[] = $_POST['child_price'][$i];
                            $number_of_child[] = $_POST['number_of_child'][$i];
                            $child_subprice[] = $_POST['child_subprice'][$i];

                            $kids_price[] = $_POST['kids_price'][$i];
                            $number_of_kids[] = $_POST['number_of_kids'][$i];
                            $kids_subprice[] = $_POST['kids_subprice'][$i];

                            $number_of_covers[] = $_POST['number_of_covers'][$i];

                            $adult_discount_cover[] = $_POST['adult_discount_cover'][$i];
                            $child_discount_cover[] = $_POST['child_discount_cover'][$i];
                            $kids_discount_cover[] = $_POST['kids_discount_cover'][$i];

                        }
                    }

                    $bbq_discount_amount = array_sum($bbq_discount_amount);
                    $tax_amount = array_sum($tax_amount);
                    $total_amount = array_sum($total_amount);
                    $gtotal = array_sum($gtotal);

                    $adult_price = array_sum($adult_price);
                    $number_of_adult = array_sum($number_of_adult);
                    $adult_subprice = array_sum($adult_subprice);

                    $child_price = array_sum($child_price);
                    $number_of_child = array_sum($number_of_child);
                    $child_subprice = array_sum($child_subprice);

                    $kids_price = array_sum($kids_price);
                    $number_of_kids = array_sum($number_of_kids);
                    $kids_subprice = array_sum($kids_subprice);

                    $number_of_covers = array_sum($number_of_covers);

                    $adult_discount_cover = array_sum($adult_discount_cover);
                    $child_discount_cover = array_sum($child_discount_cover);
                    $kids_discount_cover = array_sum($kids_discount_cover);

                    $bbq_array = array(
                        'number_of_adult' => $number_of_adult,
                        'number_of_child' => $number_of_child,
                        'number_of_kids' => $number_of_kids,
                    );

                    $item_data = $this->pos_model->BBQgetBil($table_id, $split_id, $this->session->userdata('user_id'));

                    foreach ($item_data['items'] as $row_order) {
                        foreach ($row_order as $item) {

                            $saleorder_item[] = $item;
                            $bil_total[] = $item->subtotal;

                            $discount = $this->site->discountMultiple($item->recipe_id);

                            if (!empty($discount)) {

                                if ($discount[2] == 'percentage_discount') {
                                    $discount_value = $discount[1] . '%';
                                } else {
                                    $discount_value = $discount[1];
                                }
                                $item_discount1 = $this->site->calculateDiscount($discount_value, $item->subtotal);
                                $total_dis[] = $item_discount1;
                            } else {
                                $item_discount1 = 0;
                                $total_dis[] = 0;
                            }
                        }

                    }
                    $TotalDiscount = $this->site->TotalDiscount();
                    if (!empty($TotalDiscount)) {
                        $offer_discount = $TotalDiscount[1];
                        $offer_discount_id = $TotalDiscount[0];
                    } else {
                        $offer_discount = 0;
                        $offer_discount_id = 0;
                    }
                    $final_bil = array_sum($bil_total) - array_sum($total_dis);
                    $step_bil_1 = array_sum($bil_total) - array_sum($total_dis);

                    $other_discount = $this->input->post('bbq_discount');
                    $final_bil = $final_bil - $TotalDiscount[1];
                    $step_bil_2 = $step_bil_1 - $TotalDiscount[1];

                    $other_discount_total = $this->site->calculateDiscount($other_discount, $step_bil_2);
                    $total_discount = $other_discount_total + array_sum($total_dis) + $offer_discount;
                    $final_bil = $final_bil - $other_discount_total;

                    $step_bil_3 = $step_bil_2 - $other_discount_total;

                    $total_tax = $this->site->calculateOrderTax($this->Settings->default_tax, $final_bil);
                    $final_bil = $final_bil;
                    // var_dump($final_bil);die;
                    $step_bil_4 = $step_bil_3;
                    foreach ($item_data['order'] as $order) {
                        $order_data = array('sales_type_id' => $order->order_type,
                            'sales_split_id' => $order->split_id,
                            'sales_table_id' => $order->table_id,
                            'date' => date('Y-m-d H:i:s'),
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
                            'consolidated' => 1,
                        );
                    }

                    $sale = array(
                        'bilgenerator_type' => 0,
                        'sales_type_id' => 4,
                        'sales_split_id' => $this->input->post('splits'),
                        'sales_table_id' => $this->input->post('table'),
                        'date' => date('Y-m-d H:i:s'),
                        'reference_no' => 'SALE' . date('YmdHis'),
                        'customer_id' => $this->input->post('customer_id'),
                        'customer' => $this->input->post('customer'),
                        'biller_id' => $this->input->post('biller_id'),
                        'biller' => $this->input->post('biller'),
                        'warehouse_id' => $this->input->post('warehouse_id'),
                        'total' => $this->input->post('total_amount'),
                        'order_discount_id' => $this->input->post('bbq_discount'),
                        'total_discount' => $this->input->post('bbq_discount_amount'),
                        'order_tax_id' => $this->input->post('ptax'),
                        'total_tax' => $this->input->post('tax_amount'),
                        'grand_total' => $this->input->post('gtotal'),
                        'sale_status' => 'Process',
                        'total_items' => $this->input->post('number_of_covers'),
                        'consolidated' => 1,
                    );

                    $sale_items[] = array();
                    /*$sale_items[] = array(
                    'type' => 'adult',
                    'cover' => $this->input->post('number_of_adult'),
                    'price' => $this->input->post('adult_price'),
                    'days' => $this->input->post('adult_days'),
                    'buyx' => $this->input->post('adult_buyx'),
                    'getx' => $this->input->post('adult_getx'),
                    'discount_cover' => $this->input->post('adult_discount_cover'),
                    'subtotal' => $this->input->post('adult_subprice'),
                    'created' => date('Y-m-d H:i:s'),
                    );

                    $sale_items[] = array(
                    'type' => 'child',
                    'cover' => $this->input->post('number_of_child'),
                    'price' => $this->input->post('child_price'),
                    'days' => $this->input->post('child_days'),
                    'buyx' => $this->input->post('child_buyx'),
                    'getx' => $this->input->post('child_getx'),
                    'discount_cover' => $this->input->post('child_discount_cover'),
                    'subtotal' => $this->input->post('child_subprice'),
                    'created' => date('Y-m-d H:i:s'),
                    );

                    $sale_items[] = array(
                    'type' => 'kids',
                    'cover' => $this->input->post('number_of_kids'),
                    'price' => $this->input->post('kids_price'),
                    'days' => $this->input->post('kids_days'),
                    'buyx' => $this->input->post('kids_buyx'),
                    'getx' => $this->input->post('kids_getx'),
                    'discount_cover' => $this->input->post('kids_discount_cover'),
                    'subtotal' => $this->input->post('kids_subprice'),
                    'created' => date('Y-m-d H:i:s'),
                    );*/

                    $bil_value = $this->input->post('bils');

                    for ($i = 0; $i < $this->input->post('bils'); $i++) {

                        $total = array_sum($bil_total);
                        $bil_total_count = count($item_data['items']);

                        foreach ($item_data['order'] as $order) {
                            $billData[$i] = array(
                                'date' => date('Y-m-d H:i:s'),
                                'customer_id' => $order->customer_id,
                                'customer' => $order->customer,
                                'biller_id' => $order->biller_id,
                                'biller' => $order->biller,
                                'reference_no' => 'SALES-' . date('YmdHis'),
                                'total_items' => $bil_total_count,
                                'total' => $total / $bil_value,
                                'total_tax' => $total_tax / $bil_value,
                                'tax_id' => $this->Settings->default_tax,
                                'total_discount' => $total_discount / $bil_value,
                                'grand_total' => $final_bil / $bil_value,
                                'round_total' => $final_bil / $bil_value,
                                'order_discount_id' => $offer_discount_id,
                                'bill_type' => $bill_type,
                                'delivery_person_id' => $delivery_person,
                                'warehouse_id' => $warehouse_id,
                                'consolidated' => 1,
                            );
                        }

                        foreach ($item_data['items'][$i] as $item) {
                            // print_r(count($item_data['items'][$i]));
                            $discount = $this->site->discountMultiple($item->recipe_id);
                            if (!empty($discount)) {

                                if ($discount[2] == '1') {
                                    $discount_value = $discount[1] . '%';
                                } else {
                                    $discount_value = $discount[1];
                                }
                                $item_discount = $this->site->calculateDiscount($discount_value, $item->subtotal);
                            } else {
                                $item_discount = 0;
                            }

                            $off_discount = $this->site->calculate_Discount($offer_discount, ($item->subtotal - $item_discount), $step_bil_1);
                            $input_discount = $this->site->calculate_Discount($other_discount_total, ($item->subtotal - $item_discount - $off_discount), $step_bil_2);

                            $bbq_item_birday_dis = 0;
                            $bbq_birthday_discount = $this->input->post('birthday_discount_for_bbq');
                            $bbq_item_birday_dis = $bbq_birthday_discount;

                            $itemtax = $this->site->calculateOrderTax($this->input->post('ptax'), ($item->subtotal - $off_discount - $input_discount - $item_discount - $bbq_item_birday_dis));

                            $input_discount = $input_discount ? $input_discount : 0;

                            $splitData[$i][] = array(
                                'recipe_name' => $item->recipe_name,
                                'unit_price' => $item->unit_price / $bil_value,
                                'net_unit_price' => $item->net_unit_price / $bil_value,
                                'warehouse_id' => $warehouse_id,
                                'recipe_type' => $item->recipe_type,
                                'quantity' => $item->quantity,
                                'recipe_id' => $item->recipe_id,
                                'recipe_code' => $item->recipe_code,
                                'discount' => $discount[0],
                                'item_discount' => $item_discount / $bil_value,
                                'off_discount' => $off_discount ? $off_discount / $bil_value : 0,
                                'input_discount' => (($input_discount) / $bil_value),
                                'birthday_discount' => $bbq_birthday_discount / count($item_data['items'][$i]),
                                'tax' => $itemtax ? $itemtax / $bil_value : 0,
                                'subtotal' => ($item->subtotal / $bil_value - $input_discount / $bil_value - $bbq_birthday_discount / $bil_value) + $itemtax / $bil_value,
                            );
                            $j++;
                        }

                    }

                    $sales_total = array_column($billData, 'grand_total');
                    $sales_total = array_sum($sales_total);

                    $notification_array['from_role'] = $group_id;
                    $notification_array['insert_array'] = array(
                        'msg' => 'Waiter has been bil generator to ' . $split_id,
                        'type' => 'Bil generator (' . $split_id . ')',
                        'table_id' => $table_id,
                        'role_id' => 8,
                        'user_id' => $user_id,
                        'warehouse_id' => $warehouse_id,
                        'created_on' => date('Y-m-d H:m:s'),
                        'is_read' => 0,
                    );

                    for ($i = 0; $i < $this->input->post('bils'); $i++) {
                        if (!empty($this->input->post('bbq_discount'))) {

                            $request_discount[$i] = array(
                                'customer_id' => $customer_id,
                                'waiter_id' => $this->session->userdata('user_id'),
                                'table_id' => $table_id,
                                'split_id' => $split_id,
                                'bbq_type_val' => $this->Settings->bbq_discount ? $this->Settings->bbq_discount : '',
                                'bbq_discount_val' => $this->input->post('bbq_discount_id') ? $this->input->post('bbq_discount_id') : '',
                                'created_on' => date('Y-m-d H:i:s'),
                            );
                        }

                        $bilsdata[$i] = array(
                            'bilgenerator_type' => 0,
                            'date' => date('Y-m-d H:i:s'),
                            'reference_no' => 'SALE' . date('YmdHis'),
                            'customer_id' => $this->input->post('customer_id'),
                            'customer' => $this->input->post('customer'),
                            'biller_id' => $this->input->post('biller_id'),
                            'biller' => $this->input->post('biller'),
                            'bill_type' => $bill_type,
                            'warehouse_id' => $this->input->post('warehouse_id'),
                            'created_by' => $this->session->userdata('user_id'),
                            'total' => $this->input->post('total_amount'),
                            'order_discount_id' => $this->input->post('bbq_discount'),
                            'total_discount' => $this->input->post('bbq_discount_amount'),
                            'birthday_discount' => $this->input->post('birthday_discount_for_bbq') ? $this->input->post('birthday_discount_for_bbq') : 0,
                            'tax_id' => $this->input->post('ptax'),
                            'total_tax' => $this->input->post('tax_amount'),
                            'tax_type' => $this->input->post('tax_type'),
                            'grand_total' => $this->input->post('gtotal'),
                            'total_items' => $this->input->post('number_of_covers'),
                            'customer_discount_id' => $this->input->post('bbq_discount_id') ? $this->input->post('bbq_discount_id') : 0,
                            'consolidated' => 1,
                            'order_type' => 4,
                            'bbq_cover_discount' => $this->input->post('bbq_cover_discount') ? $this->input->post('bbq_cover_discount') : 0,

                            'bbq_daywise_discount_id' => $this->input->post('bbq_daywise_discount_id') ? $this->input->post('bbq_daywise_discount_id') : 0,
                            'bbq_daywise_discount' => $this->input->post('bbq_daywise_discount') ? $this->input->post('bbq_daywise_discount') : 0,

                            'service_charge_id' => $this->input->post('bbq_service_charge') ? $this->input->post('bbq_service_charge') : 0,
                            'service_charge_amount' => $this->input->post('bbq_service_amount') ? $this->input->post('bbq_service_amount') : 0,

                        );

                        $adult_discount = $this->site->calculateDiscount($this->input->post('bbq_discount'), $this->input->post('adult_subprice'));
                        $adult_disfinal = $this->input->post('adult_subprice') - $adult_discount;
                        $adult_tax_id = $this->pos_settings->default_tax;
                        $adult_tax_type = $this->pos_settings->tax_type;
                        $adult_tax = $this->site->calculateOrderTax($this->pos_settings->default_tax, $adult_disfinal);

                        $bil_items[$i][] = array(
                            'type' => 'adult',
                            'cover' => $this->input->post('number_of_adult'),
                            'price' => $this->input->post('adult_price'),
                            'days' => $this->input->post('adult_days'),
                            'buyx' => $this->input->post('adult_buyx'),
                            'getx' => $this->input->post('adult_getx'),
                            'discount_cover' => $this->input->post('adult_discount_cover'),
                            'discount' => $adult_discount,
                            'tax_id' => $adult_tax_id,
                            'tax_type' => $adult_tax_type,
                            'tax' => $adult_tax,
                            'subtotal' => $this->input->post('adult_subprice'),
                            'created' => date('Y-m-d H:i:s'),
                        );

                        $child_discount = $this->site->calculateDiscount($this->input->post('bbq_discount'), $this->input->post('child_subprice'));
                        $child_disfinal = $this->input->post('child_subprice') - $child_discount;
                        $child_tax_id = $this->pos_settings->default_tax;
                        $child_tax_type = $this->pos_settings->tax_type;
                        $child_tax = $this->site->calculateOrderTax($this->pos_settings->default_tax, $child_disfinal);

                        $bil_items[$i][] = array(
                            'type' => 'child',
                            'cover' => $this->input->post('number_of_child'),
                            'price' => $this->input->post('child_price'),
                            'days' => $this->input->post('child_days'),
                            'buyx' => $this->input->post('child_buyx'),
                            'getx' => $this->input->post('child_getx'),
                            'discount_cover' => $this->input->post('child_discount_cover'),
                            'discount' => $child_discount,
                            'tax_id' => $child_tax_id,
                            'tax_type' => $child_tax_type,
                            'tax' => $child_tax,
                            'subtotal' => $this->input->post('child_subprice'),
                            'created' => date('Y-m-d H:i:s'),
                        );

                        $kids_discount = $this->site->calculateDiscount($this->input->post('bbq_discount'), $this->input->post('kids_subprice'));
                        $kids_disfinal = $this->input->post('kids_subprice') - $kids_discount;
                        $kids_tax_id = $this->pos_settings->default_tax;
                        $kids_tax_type = $this->pos_settings->tax_type;
                        $kids_tax = $this->site->calculateOrderTax($this->pos_settings->default_tax, $kids_disfinal);

                        $bil_items[$i][] = array(
                            'type' => 'kids',
                            'cover' => $this->input->post('number_of_kids'),
                            'price' => $this->input->post('kids_price'),
                            'days' => $this->input->post('kids_days'),
                            'buyx' => $this->input->post('kids_buyx'),
                            'getx' => $this->input->post('kids_getx'),
                            'discount_cover' => $this->input->post('kids_discount_cover'),
                            'discount' => $kids_discount,
                            'tax_id' => $kids_tax_id,
                            'tax_type' => $kids_tax_type,
                            'tax' => $kids_tax,
                            'subtotal' => $this->input->post('kids_subprice'),
                            'created' => date('Y-m-d H:i:s'),
                        );
                    }

                    $splits = $this->input->post('splits');
                    $bbq_in_discount = $this->input->post('bbq_in_discount');
                    // echo "<pre>";
                    /*print_r($this->input->post());
                    print_r($splitData);*/
                    // print_r($bilsdata);
                    // die;
                    if ($bbq_item_birday_dis != 0) {
                        $birthday = array(
                            'customer_id' => $customer_id,
                            'birthday_discount' => $bbq_item_birday_dis,
                            'status' => 1,
                            'issue_date' => date('Y-m-d'),
                            'created_at' => $this->session->userdata('user_id'),
                            'created_on' => date('Y-m-d H:i:s'),
                        );
                    } else {
                        $birthday = array();
                    }
                    /*echo "<pre>";
                    print_r($bilsdata);
                    print_r($splitData);*/
                    $bbq_response = $this->pos_model->BBQaddSale($notification_array, $timelog_array, $order_data, $splitData, $saleorder_item, $sale, $sale_items, $bilsdata, $bil_items, $order_id, $bbq_array, $splits, $request_discount, $bbq_in_discount, $birthday);

                }

                if ($bbq_response == true && $dine_response) {
                    $tableid = $order_data['sales_table_id'];
                    admin_redirect("pos/order_bbqtable?bbqtid=" . $tableid);
                } else {

                    $customer_id = $this->site->getCustomerDetails($waiter_id, $table_id, $split_id);
                    $this->data['discount_select'] = $this->pos_model->getDiscountdata($customer_id, $waiter_id, $table_id, $split_id);

                    $this->data['order_bbq'] = $this->pos_model->BBQtablesplit($table_id, $split_id);
                    $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
                    $this->load->view($this->theme . 'pos/consolidated', $this->data);
                }
            }
            $customer_id = $this->site->getCustomerDetails($waiter_id, $table_id, $split_id);
            $this->data['discount_select'] = $this->pos_model->getDiscountdata($customer_id, $waiter_id, $table_id, $split_id);

            $this->data['order_bbq'] = $this->pos_model->BBQtablesplit($table_id, $split_id);
            $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
            $this->load->view($this->theme . 'pos/consolidated', $this->data);
        }

    }

    public function bbqbilling(){
        $order_type = !empty($_GET['order_type']) ? $_GET['order_type'] : '';
        $bill_type = !empty($_GET['bill_type']) ? $_GET['bill_type'] : '';
        $table_id = !empty($_GET['table']) ? $_GET['table'] : '';
        $split_id = !empty($_GET['splits']) ? $_GET['splits'] : '';
        $bils = !empty($_GET['bils']) ? $_GET['bils'] : '';
        $waiter_id = $this->session->userdata('user_id');
        $this->data['warehouse'] = $this->session->userdata('warehouse_id') ? $this->site->getWarehouseByID($this->session->userdata('warehouse_id')) : null;
        $this->data['order_type'] = $order_type;
        $this->data['bill_type'] = $bill_type;
        $this->data['bils'] = $bils;
        $this->data['table_id'] = $table_id;
        $this->data['split_id'] = $split_id;
        $this->data['tax_rates'] = $this->site->getAllTaxRates();
        $this->data['service_charge'] = $this->pos_model->getAllSericeCharges();
        $this->data['bbq_discount'] = $this->site->GetAllBBQDiscounts();
        $order_id = $this->pos_model->getBBQorderID($split_id);
        $split_status = $this->site->check_splitid_is_bill_generated($split_id);if ($split_status) {
            admin_redirect("pos/order_bbqtable");
        }
        if ($bill_type == 1) {
            $this->form_validation->set_rules('splits', $this->lang->line("splits"), 'trim|required');
            if ($this->form_validation->run() == true) {
                // echo "<pre>";print_r($_POST);exit;
                if ($this->input->post('action') == "SINGLEBILL-SUBMIT") {
                    for ($i = 0; $i < $this->input->post('bils'); $i++) {
                        if ($_POST['number_of_covers'][$i] != 0) {
                            $bbq_discount_amount[] = $_POST['bbq_discount_amount'][$i];
                            $tax_amount[] = $_POST['tax_amount'][$i];
                            $total_amount[] = $_POST['total_amount'][$i];
                            $gtotal[] = $_POST['gtotal'][$i];
                            $adult_price[] = $_POST['adult_price'][$i];
                            $number_of_adult[] = $_POST['number_of_adult'][$i];
                            $adult_subprice[] = $_POST['adult_subprice'][$i];
                            $child_price[] = $_POST['child_price'][$i];
                            $number_of_child[] = $_POST['number_of_child'][$i];
                            $child_subprice[] = $_POST['child_subprice'][$i];
                            $kids_price[] = $_POST['kids_price'][$i];
                            $number_of_kids[] = $_POST['number_of_kids'][$i];
                            $kids_subprice[] = $_POST['kids_subprice'][$i];
                            $number_of_covers[] = $_POST['number_of_covers'][$i];
                            $adult_discount_cover[] = $_POST['adult_discount_cover'][$i];
                            $child_discount_cover[] = $_POST['child_discount_cover'][$i];
                            $kids_discount_cover[] = $_POST['kids_discount_cover'][$i];
                        }
                    }
                    $bbq_discount_amount = array_sum($bbq_discount_amount);
                    $tax_amount = array_sum($tax_amount);
                    $total_amount = array_sum($total_amount);
                    $gtotal = array_sum($gtotal);
                    $adult_price = array_sum($adult_price);
                    $number_of_adult = array_sum($number_of_adult);
                    $adult_subprice = array_sum($adult_subprice);
                    $child_price = array_sum($child_price);
                    $number_of_child = array_sum($number_of_child);
                    $child_subprice = array_sum($child_subprice);
                    $kids_price = array_sum($kids_price);
                    $number_of_kids = array_sum($number_of_kids);
                    $kids_subprice = array_sum($kids_subprice);
                    $number_of_covers = array_sum($number_of_covers);
                    $adult_discount_cover = array_sum($adult_discount_cover);
                    $child_discount_cover = array_sum($child_discount_cover);
                    $kids_discount_cover = array_sum($kids_discount_cover);
                    $bbq_array = array(
                        'number_of_adult' => $number_of_adult,
                        'number_of_child' => $number_of_child,
                        'number_of_kids' => $number_of_kids,
                    );
                    $item_data = $this->pos_model->getBil($table_id, $split_id, $this->session->userdata('user_id'));
                    foreach ($item_data['order'] as $order) {
                        $customer_id = $order->customer_id;
                        $notification_array['customer_id'] = $order->customer_id;
                    }

                    foreach ($item_data['items'] as $row_order) {
                        foreach ($row_order as $item) {

                            $saleorder_item[] = $item;
                            $bil_total[] = $item->subtotal;
                            $discount = $this->site->discountMultiple($item->recipe_id);
                            if (!empty($discount)) {
                                if ($discount[2] == 'percentage_discount') {
                                    $discount_value = $discount[1] . '%';
                                } else {
                                    $discount_value = $discount[1];
                                }
                                $item_discount1 = $this->site->calculateDiscount($discount_value, $item->subtotal);
                                $total_dis[] = $item_discount1;
                            } else {
                                $item_discount1 = 0;
                                $total_dis[] = 0;
                            }
                        }

                    }
                    $TotalDiscount = $this->site->TotalDiscount();
                    if (!empty($TotalDiscount)) {
                        $offer_discount = $TotalDiscount[1];
                        $offer_discount_id = $TotalDiscount[0];
                    } else {
                        $offer_discount = 0;
                        $offer_discount_id = 0;
                    }
                    $final_bil = array_sum($bil_total) - array_sum($total_dis);
                    $step_bil_1 = array_sum($bil_total) - array_sum($total_dis);
                    $other_discount = $this->input->post('bbq_discount');
                    $final_bil = $final_bil - $TotalDiscount[1];
                    $step_bil_2 = $step_bil_1 - $TotalDiscount[1];

                    $other_discount_total = $this->site->calculateDiscount($other_discount, $step_bil_2);
                    $total_discount = $other_discount_total + array_sum($total_dis) + $offer_discount;
                    $final_bil = $final_bil - $other_discount_total;
                    $step_bil_3 = $step_bil_2 - $other_discount_total;

                    $total_tax = $this->site->calculateOrderTax($this->Settings->default_tax, $final_bil);
                    $final_bil = $final_bil;
                    $step_bil_4 = $step_bil_3;
                    foreach ($item_data['order'] as $order) {
                        $order_data = array('sales_type_id' => $order->order_type,
                            'sales_split_id' => $order->split_id,
                            'sales_table_id' => $order->table_id,
                            'date' => date('Y-m-d H:i:s'),
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
                    }

                    $sale = array(
                        'bilgenerator_type' => 0,
                        'sales_type_id' => 4,
                        'sales_split_id' => $this->input->post('splits'),
                        'sales_table_id' => $this->input->post('table'),
                        'date' => date('Y-m-d H:i:s'),
                        'reference_no' => 'SALE' . date('YmdHis'),
                        'customer_id' => $this->input->post('customer_id'),
                        'customer' => $this->input->post('customer'),
                        'biller_id' => $this->input->post('biller_id'),
                        'biller' => $this->input->post('biller'),
                        'warehouse_id' => $this->input->post('warehouse_id'),
                        'total' => $this->input->post('total_amount'),
                        'order_discount_id' => $this->input->post('bbq_discount'),
                        'total_discount' => $this->input->post('bbq_discount_amount'),
                        'order_tax_id' => $this->input->post('ptax'),
                        'total_tax' => $this->input->post('tax_amount'),
                        'grand_total' => $this->input->post('gtotal'),
                        'sale_status' => 'Process',
                        'total_items' => $this->input->post('number_of_covers'),
                    );

                    /*$sale_items[] = array(
                    'type' => 'adult',
                    'cover' => $this->input->post('number_of_adult'),
                    'price' => $this->input->post('adult_price'),
                    'days' => $this->input->post('adult_days'),
                    'buyx' => $this->input->post('adult_buyx'),
                    'getx' => $this->input->post('adult_getx'),
                    'discount_cover' => $this->input->post('adult_discount_cover'),
                    'subtotal' => $this->input->post('adult_subprice'),
                    'created' => date('Y-m-d H:i:s'),
                    );
                    $sale_items[] = array(
                    'type' => 'child',
                    'cover' => $this->input->post('number_of_child'),
                    'price' => $this->input->post('child_price'),
                    'days' => $this->input->post('child_days'),
                    'buyx' => $this->input->post('child_buyx'),
                    'getx' => $this->input->post('child_getx'),
                    'discount_cover' => $this->input->post('child_discount_cover'),
                    'subtotal' => $this->input->post('child_subprice'),
                    'created' => date('Y-m-d H:i:s'),
                    );

                    $sale_items[] = array(
                    'type' => 'kids',
                    'cover' => $this->input->post('number_of_kids'),
                    'price' => $this->input->post('kids_price'),
                    'days' => $this->input->post('kids_days'),
                    'buyx' => $this->input->post('kids_buyx'),
                    'getx' => $this->input->post('kids_getx'),
                    'discount_cover' => $this->input->post('kids_discount_cover'),
                    'subtotal' => $this->input->post('kids_subprice'),
                    'created' => date('Y-m-d H:i:s'),
                    );*/

                    $bil_value = $this->input->post('bils');

                    for ($i = 0; $i < $this->input->post('bils'); $i++) {

                        if (!empty($this->input->post('bbq_discount'))) {

                            $request_discount[$i] = array(
                                'customer_id' => $customer_id,
                                'waiter_id' => $this->session->userdata('user_id'),
                                'table_id' => $table_id,
                                'split_id' => $split_id,
                                'bbq_type_val' => $this->Settings->bbq_discount ? $this->Settings->bbq_discount : '',
                                'bbq_discount_val' => $this->input->post('bbq_discount_id') ? $this->input->post('bbq_discount_id') : '',
                                'created_on' => date('Y-m-d H:i:s'),
                            );
                        }

                        $total = array_sum($bil_total);
                        $bil_total_count = count($item_data['items']);

                        foreach ($item_data['order'] as $order) {
                            $billData[$i] = array(
                                'date' => date('Y-m-d H:i:s'),
                                'created_on' => date('Y-m-d H:i:s'),
                                'customer_id' => $order->customer_id,
                                'customer' => $order->customer,
                                'biller_id' => $order->biller_id,
                                'biller' => $order->biller,
                                'bil_type' => $bill_type,
                                'reference_no' => 'SALES-' . date('YmdHis'),
                                'total_items' => $bil_total_count,
                                'total' => $total / $bil_value,
                                'total_tax' => $total_tax / $bil_value,
                                'tax_id' => $this->Settings->default_tax,
                                'total_discount' => $total_discount / $bil_value,
                                'grand_total' => $final_bil / $bil_value,
                                'round_total' => $final_bil / $bil_value,
                                'order_discount_id' => $offer_discount_id,
                                'bill_type' => $bill_type,
                                'delivery_person_id' => $delivery_person,
                                'warehouse_id' => $warehouse_id,
                            );
                        }

                        foreach ($item_data['items'][$i] as $item) {

                            $discount = $this->site->discountMultiple($item->recipe_id);

                            if (!empty($discount)) {

                                if ($discount[2] == '1') {
                                    $discount_value = $discount[1] . '%';
                                } else {
                                    $discount_value = $discount[1];
                                }
                                $item_discount = $this->site->calculateDiscount($discount_value, $item->subtotal);
                            } else {
                                $item_discount = 0;
                            }

                            $off_discount = $this->site->calculate_Discount($offer_discount, ($item->subtotal - $item_discount), $step_bil_1);
                            $input_discount = $this->site->calculate_Discount($other_discount_total, ($item->subtotal - $item_discount - $off_discount), $step_bil_2);

                            $bbq_item_birday_dis = 0;
                            $bbq_birthday_discount = $this->input->post('birthday_discount_for_bbq');
                            $bbq_item_birday_dis = $bbq_birthday_discount;

                            $itemtax = $this->site->calculateOrderTax($this->input->post('ptax'), ($item->subtotal - $off_discount - $input_discount - $item_discount));

                            /*item service charge */
                            $item_service_charge = 0;
                            if (!empty($this->input->post('service_charge'))) {
                                $item_service_charge = $this->site->calculateServiceCharge($this->input->post('service_charge'), ($item->subtotal - $off_discount - $input_discount - $item_discount));
                            }
                            /*item service charge */

                            $splitData[$i][] = array(
                                'recipe_name' => $item->recipe_name,
                                'recipe_variant' => $item->variant,
                                'recipe_variant_id' => $item->recipe_variant_id,
                                'unit_price' => $item->unit_price / $bil_value,
                                'net_unit_price' => $item->net_unit_price / $bil_value,
                                'warehouse_id' => $warehouse_id,
                                'recipe_type' => $item->recipe_type,
                                'quantity' => $item->quantity,
                                'recipe_id' => $item->recipe_id,
                                'recipe_code' => $item->recipe_code,
                                'discount' => $discount[0],
                                'item_discount' => $item_discount / $bil_value,
                                'off_discount' => $off_discount ? $off_discount / $bil_value : 0,
                                'input_discount' => $input_discount ? $input_discount / $bil_value : 0,
                                'birthday_discount' => $bbq_item_birday_dis / count($item_data['items'][$i]),
                                'tax' => $itemtax ? $itemtax / $bil_value : 0,
                                'subtotal' => ($item->subtotal / $bil_value - $input_discount / $bil_value) + $itemtax / $bil_value,
                            );

                            $j++;
                        }

                    }

                    $sales_total = array_column($billData, 'grand_total');
                    $sales_total = array_sum($sales_total);

                    $notification_array['from_role'] = $group_id;
                    $notification_array['insert_array'] = array(
                        'msg' => 'Waiter has been bil generator to ' . $split_id,
                        'type' => 'Bil generator (' . $split_id . ')',
                        'table_id' => $table_id,
                        'role_id' => 8,
                        'user_id' => $this->session->userdata('user_id'),
                        'warehouse_id' => $this->session->userdata('warehouse_id'),
                        'created_on' => date('Y-m-d H:m:s'),
                        'is_read' => 0,
                        'respective_steward' => 0,
                        'split_id' => $split_id,
                        'tag' => 'bill-generated',
                        'status' => 1,
                    );

                    for ($i = 0; $i < $this->input->post('bils'); $i++) {
                        $bilsdata[$i] = array(
                            'bilgenerator_type' => 0,
                            'date' => $this->site->getTransactionDate(),
                            'created_on' => date('Y-m-d H:i:s'),
                            'reference_no' => 'SALE' . date('YmdHis'),
                            'customer_id' => $this->input->post('customer_id'),
                            'customer' => $this->input->post('customer'),
                            'biller_id' => $this->input->post('biller_id'),
                            'biller' => $this->input->post('biller'),
                            'warehouse_id' => $this->input->post('warehouse_id'),
                            'created_by' => $this->session->userdata('user_id'),
                            'total' => $this->input->post('total_amount'),
                            'order_discount_id' => $this->input->post('bbq_discount'),
                            'total_discount' => $this->input->post('bbq_discount_amount'),
                            'birthday_discount' => $this->input->post('birthday_discount_for_bbq') ? $this->input->post('birthday_discount_for_bbq') : 0,
                            'tax_id' => $this->input->post('ptax'),
                            'total_tax' => $this->input->post('tax_amount'),
                            'tax_type' => $this->input->post('tax_type'),
                            'grand_total' => $this->input->post('gtotal'),
                            'total_items' => $this->input->post('number_of_covers'),
                            'customer_discount_id' => $this->input->post('bbq_discount_id') ? $this->input->post('bbq_discount_id') : 0,
                            'order_type' => 4,
                            'bbq_cover_discount' => $this->input->post('bbq_cover_discount') ? $this->input->post('bbq_cover_discount') : 0,

                            'bbq_daywise_discount_id' => $this->input->post('bbq_daywise_discount_id') ? $this->input->post('bbq_daywise_discount_id') : 0,
                            'bbq_daywise_discount' => $this->input->post('bbq_daywise_discount') ? $this->input->post('bbq_daywise_discount') : 0,

                            'service_charge_id' => $this->input->post('service_charge') ? $this->input->post('service_charge') : 0,
                            'service_charge_amount' => $this->input->post('service_amount') ? $this->input->post('service_amount') : 0,
                        );

                        $adult_discount = $this->site->calculateDiscount($this->input->post('bbq_discount'), $this->input->post('adult_subprice'));
                        $adult_disfinal = $this->input->post('adult_subprice') - $adult_discount;
                        $adult_tax_id = $this->pos_settings->default_tax;
                        $adult_tax_type = $this->pos_settings->tax_type;
                        $adult_tax = $this->site->calculateOrderTax($this->pos_settings->default_tax, $adult_disfinal);

                        $bil_items[$i][] = array(
                            'type' => 'adult',
                            'cover' => $this->input->post('number_of_adult'),
                            'price' => $this->input->post('adult_price'),
                            'days' => $this->input->post('adult_days'),
                            'buyx' => $this->input->post('adult_buyx'),
                            'getx' => $this->input->post('adult_getx'),
                            'discount_cover' => $this->input->post('adult_discount_cover'),
                            'daywise_discount' => $this->input->post('adult_daywise_discount') ? $this->input->post('adult_daywise_discount') : 0,
                            'discount' => $adult_discount,
                            'tax_id' => $adult_tax_id,
                            'tax_type' => $adult_tax_type,
                            'tax' => $adult_tax,
                            'subtotal' => $this->input->post('adult_subprice'),
                            'created' => date('Y-m-d H:i:s'),
                        );

                        $child_discount = $this->site->calculateDiscount($this->input->post('bbq_discount'), $this->input->post('child_subprice'));
                        $child_disfinal = $this->input->post('child_subprice') - $child_discount;
                        $child_tax_id = $this->pos_settings->default_tax;
                        $child_tax_type = $this->pos_settings->tax_type;
                        $child_tax = $this->site->calculateOrderTax($this->pos_settings->default_tax, $child_disfinal);

                        $bil_items[$i][] = array(
                            'type' => 'child',
                            'cover' => $this->input->post('number_of_child'),
                            'price' => $this->input->post('child_price'),
                            'days' => $this->input->post('child_days'),
                            'buyx' => $this->input->post('child_buyx'),
                            'getx' => $this->input->post('child_getx'),
                            'discount_cover' => $this->input->post('child_discount_cover'),
                            'daywise_discount' => $this->input->post('child_daywise_discount') ? $this->input->post('child_daywise_discount') : 0,
                            'discount' => $child_discount,
                            'tax_id' => $child_tax_id,
                            'tax_type' => $child_tax_type,
                            'tax' => $child_tax,
                            'subtotal' => $this->input->post('child_subprice'),
                            'created' => date('Y-m-d H:i:s'),
                        );

                        $kids_discount = $this->site->calculateDiscount($this->input->post('bbq_discount'), $this->input->post('kids_subprice'));
                        $kids_disfinal = $this->input->post('kids_subprice') - $kids_discount;
                        $kids_tax_id = $this->pos_settings->default_tax;
                        $kids_tax_type = $this->pos_settings->tax_type;
                        $kids_tax = $this->site->calculateOrderTax($this->pos_settings->default_tax, $kids_disfinal);

                        $bil_items[$i][] = array(
                            'type' => 'kids',
                            'cover' => $this->input->post('number_of_kids'),
                            'price' => $this->input->post('kids_price'),
                            'days' => $this->input->post('kids_days'),
                            'buyx' => $this->input->post('kids_buyx'),
                            'getx' => $this->input->post('kids_getx'),
                            'discount_cover' => $this->input->post('kids_discount_cover'),
                            'daywise_discount' => $this->input->post('kids_daywise_discount') ? $this->input->post('kids_daywise_discount') : 0,
                            'discount' => $kids_discount,
                            'tax_id' => $kids_tax_id,
                            'tax_type' => $kids_tax_type,
                            'tax' => $kids_tax,
                            'subtotal' => $this->input->post('kids_subprice'),
                            'created' => date('Y-m-d H:i:s'),
                        );
                    }

                    $splits = $this->input->get('splits');
                    $bbq_in_discount = $this->input->post('bbq_in_discount');
                    /*echo "<pre>";
                    // print_r($this->input->post());
                    print_r($bilsdata);
                    print_r($splitData);die;*/
                    if ($bbq_item_birday_dis != 0) {
                        $birthday = array(
                            'customer_id' => $customer_id,
                            'birthday_discount' => $bbq_item_birday_dis,
                            'status' => 1,
                            'issue_date' => date('Y-m-d'),
                            'created_at' => $this->session->userdata('user_id'),
                            'created_on' => date('Y-m-d H:i:s'),
                        );
                    } else {
                        $birthday = array();
                    }
/*echo "<pre>";
print_r($birthday);die;*/
                    $response = $this->pos_model->BBQaddSale($notification_array, $timelog_array, $order_data, $splitData, $saleorder_item, $sale, $sale_items, $bilsdata, $bil_items, $order_id, $bbq_array, $splits, $request_discount, $bbq_in_discount, $birthday);

                    if ($response == true) {
                        $update_notifi['split_id'] = $split_id;
                        $update_notifi['tag'] = 'bill-request';
                        $this->site->update_notification_status($update_notifi);
                        $tableid = $order_data['sales_table_id'];
                        admin_redirect("pos/order_bbqtable?bbqtid=" . $tableid);
                    } else {
                        $customer_id = $this->site->getCustomerDetails($waiter_id, $table_id, $split_id);
                        $this->data['discount_select'] = $this->pos_model->getDiscountdata($customer_id, $waiter_id, $table_id, $split_id);

                        $this->data['order_bbq'] = $this->pos_model->BBQtablesplit($table_id, $split_id);
                        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
                        $this->load->view($this->theme . 'pos/bbqsinglebil', $this->data);
                    }

                }

            } else {
                $customer_id = $this->site->getCustomerDetails($waiter_id, $table_id, $split_id);
                $this->data['discount_select'] = $this->pos_model->getDiscountdata($customer_id, $waiter_id, $table_id, $split_id);

                $this->data['order_bbq'] = $this->pos_model->BBQtablesplit($table_id, $split_id);
                $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

                $this->load->view($this->theme . 'pos/bbqsinglebil', $this->data);
            }
        } elseif ($bill_type == 2) {

            $this->form_validation->set_rules('splits', $this->lang->line("splits"), 'trim|required');
            if ($this->form_validation->run() == true) {
                if ($this->input->post('action') == "AUTOSPLITBILL-SUBMIT") {

                    $bbq_discount = implode(',', $this->input->post('bbq_discount'));
                    $ptax = implode(',', $this->input->post('ptax'));

                    $notification_array['from_role'] = $group_id;
                    $notification_array['insert_array'] = array(
                        'msg' => 'Waiter has been bil generator to ' . $split_id,
                        'type' => 'Bil generator (' . $split_id . ')',
                        'table_id' => $table_id,
                        'role_id' => 8,
                        'user_id' => $user_id,
                        'warehouse_id' => $warehouse_id,
                        'created_on' => date('Y-m-d H:m:s'),
                        'is_read' => 0,
                    );

                    for ($i = 0; $i < $this->input->post('bils'); $i++) {
                        $bbq_discount_amount[] = $_POST['bbq_discount_amount'][$i];
                        $tax_amount[] = $_POST['tax_amount'][$i];
                        $total_amount[] = $_POST['total_amount'][$i];
                        $gtotal[] = $_POST['gtotal'][$i];

                        $adult_price[] = $_POST['adult_price'][$i];
                        $adult_subprice[] = $_POST['adult_subprice'][$i];

                        $child_price[] = $_POST['child_price'][$i];
                        $child_subprice[] = $_POST['child_subprice'][$i];

                        $kids_price[] = $_POST['kids_price'][$i];
                        $kids_subprice[] = $_POST['kids_subprice'][$i];

                    }
                    $bbq_discount_amount = array_sum($bbq_discount_amount);
                    $tax_amount = array_sum($tax_amount);
                    $total_amount = array_sum($total_amount);
                    $gtotal = array_sum($gtotal);

                    $adult_price = array_sum($adult_price);
                    $adult_subprice = array_sum($adult_subprice);

                    $child_price = array_sum($child_price);
                    $child_subprice = array_sum($child_subprice);

                    $kids_price = array_sum($kids_price);
                    $kids_subprice = array_sum($kids_subprice);

                    $sale = array(
                        'bilgenerator_type' => 0,
                        'sales_type_id' => 4,
                        'sales_split_id' => $this->input->post('splits'),
                        'sales_table_id' => $this->input->post('table'),
                        'date' => date('Y-m-d H:i:s'),
                        'reference_no' => 'SALE' . date('YmdHis'),
                        'customer_id' => $this->input->post('customer_id'),
                        'customer' => $this->input->post('customer'),
                        'biller_id' => $this->input->post('biller_id'),
                        'biller' => $this->input->post('biller'),
                        'warehouse_id' => $this->input->post('warehouse_id'),
                        'total' => $total_amount,
                        'order_discount_id' => $bbq_discount,
                        'total_discount' => $bbq_discount_amount,
                        'order_tax_id' => $ptax,
                        'total_tax' => $tax_amount,
                        'grand_total' => $gtotal,
                        'total_items' => $_POST['number_of_covers'][0],
                    );

                    $sale_items[] = array(
                        'type' => 'adult',
                        'cover' => $_POST['number_of_adult'][0],
                        'days' => $_POST['adult_days'][0],
                        'buyx' => $_POST['adult_buyx'][0],
                        'getx' => $_POST['adult_getx'][0],
                        'discount_cover' => $_POST['adult_discount_cover'][0],
                        'price' => $adult_price,
                        'subtotal' => $adult_subprice,
                        'created' => date('Y-m-d H:i:s'),
                    );

                    $sale_items[] = array(
                        'type' => 'child',
                        'cover' => $_POST['number_of_child'][0],
                        'days' => $_POST['child_days'][0],
                        'buyx' => $_POST['child_buyx'][0],
                        'getx' => $_POST['child_getx'][0],
                        'discount_cover' => $_POST['child_discount_cover'][0],
                        'price' => $child_price,
                        'subtotal' => $child_subprice,
                        'created' => date('Y-m-d H:i:s'),
                    );

                    $sale_items[] = array(
                        'type' => 'kids',
                        'cover' => $_POST['number_of_kids'][0],
                        'days' => $_POST['kids_days'][0],
                        'buyx' => $_POST['kids_buyx'][0],
                        'getx' => $_POST['kids_getx'][0],
                        'discount_cover' => $_POST['kids_discount_cover'][0],
                        'price' => $kids_price,
                        'subtotal' => $kids_subprice,
                        'created' => date('Y-m-d H:i:s'),
                    );

                    for ($i = 0; $i < $this->input->post('bils'); $i++) {

                        if (!empty($this->input->post('bbq_discount'))) {

                            $request_discount[$i] = array(
                                'customer_id' => $customer_id,
                                'waiter_id' => $this->session->userdata('user_id'),
                                'table_id' => $table_id,
                                'split_id' => $split_id,
                                'bbq_type_val' => $this->Settings->bbq_discount ? $this->Settings->bbq_discount : '',
                                'bbq_discount_val' => $this->input->post('bbq_discount_id') ? $this->input->post('bbq_discount_id') : '',
                                'created_on' => date('Y-m-d H:i:s'),
                            );
                        }

                        $bilsdata[$i] = array(
                            'bilgenerator_type' => 0,
                            'date' => date('Y-m-d H:i:s'),
                            'reference_no' => 'SALE' . date('YmdHis') . $i,
                            'customer_id' => $this->input->post('customer_id'),
                            'customer' => $this->input->post('customer'),
                            'biller_id' => $this->input->post('biller_id'),
                            'biller' => $this->input->post('biller'),
                            'warehouse_id' => $this->input->post('warehouse_id'),
                            'created_by' => $this->session->userdata('user_id'),
                            'total' => $_POST['total_amount'][$i],
                            'order_discount_id' => $_POST['bbq_discount'][$i],
                            'total_discount' => $_POST['bbq_discount_amount'][$i],
                            'tax_id' => $_POST['ptax'][$i],
                            'total_tax' => $_POST['tax_amount'][$i],
                            'tax_type' => $_POST['tax_type'][$i],
                            'grand_total' => $_POST['gtotal'][$i],
                            'total_items' => $_POST['number_of_covers'][$i],
                        );

                        $adult_discount[$i] = $this->site->calculateDiscount($_POST['bbq_discount'][$i], $_POST['adult_subprice'][$i]);
                        $adult_disfinal[$i] = $_POST['adult_subprice'][$i] - $adult_discount[$i];
                        $adult_tax_id[$i] = $this->pos_settings->default_tax;
                        $adult_tax_type[$i] = $this->pos_settings->tax_type;
                        $adult_tax[$i] = $this->site->calculateOrderTax($this->pos_settings->default_tax, $adult_disfinal[$i]);

                        $bil_items[$i][] = array(
                            'type' => 'adult',
                            'cover' => $_POST['number_of_adult'][$i],
                            'price' => $_POST['adult_price'][$i],
                            'days' => $_POST['adult_days'][$i],
                            'buyx' => $_POST['adult_buyx'][$i],
                            'getx' => $_POST['adult_getx'][$i],
                            'discount_cover' => $_POST['adult_discount_cover'][$i],
                            'discount' => $adult_discount[$i],
                            'tax_id' => $adult_tax_id[$i],
                            'tax_type' => $adult_tax_type[$i],
                            'tax' => $adult_tax[$i],
                            'subtotal' => $_POST['adult_subprice'][$i],
                            'created' => date('Y-m-d H:i:s'),
                        );

                        $child_discount[$i] = $this->site->calculateDiscount($_POST['bbq_discount'][$i], $_POST['child_subprice'][$i]);
                        $child_disfinal[$i] = $_POST['child_subprice'][$i] - $child_discount[$i];
                        $child_tax_id[$i] = $this->pos_settings->default_tax;
                        $child_tax_type[$i] = $this->pos_settings->tax_type;
                        $child_tax[$i] = $this->site->calculateOrderTax($this->pos_settings->default_tax, $child_disfinal[$i]);

                        $bil_items[$i][] = array(
                            'type' => 'child',
                            'cover' => $_POST['number_of_child'][$i],
                            'days' => $_POST['child_days'][$i],
                            'buyx' => $_POST['child_buyx'][$i],
                            'getx' => $_POST['child_getx'][$i],
                            'discount_cover' => $_POST['child_discount_cover'][$i],
                            'discount' => $child_discount[$i],
                            'tax_id' => $child_tax_id[$i],
                            'tax_type' => $child_tax_type[$i],
                            'tax' => $child_tax[$i],
                            'price' => $_POST['child_price'][$i],
                            'subtotal' => $_POST['child_subprice'][$i],
                            'created' => date('Y-m-d H:i:s'),
                        );

                        $kids_discount[$i] = $this->site->calculateDiscount($_POST['bbq_discount'][$i], $_POST['kids_subprice'][$i]);
                        $kids_disfinal[$i] = $_POST['kids_subprice'][$i] - $kids_discount[$i];
                        $kids_tax_id[$i] = $this->pos_settings->default_tax;
                        $kids_tax_type[$i] = $this->pos_settings->tax_type;
                        $kids_tax[$i] = $this->site->calculateOrderTax($this->pos_settings->default_tax, $kids_disfinal[$i]);

                        $bil_items[$i][] = array(
                            'type' => 'kids',
                            'cover' => $_POST['number_of_kids'][$i],
                            'days' => $_POST['kids_days'][$i],
                            'buyx' => $_POST['kids_buyx'][$i],
                            'getx' => $_POST['kids_getx'][$i],
                            'discount_cover' => $_POST['kids_discount_cover'][$i],
                            'discount' => $kids_discount[$i],
                            'tax_id' => $kids_tax_id[$i],
                            'tax_type' => $kids_tax_type[$i],
                            'tax' => $kids_tax[$i],
                            'price' => $_POST['kids_price'][$i],
                            'subtotal' => $_POST['kids_subprice'][$i],
                            'created' => date('Y-m-d H:i:s'),
                        );
                    }

                    $splits = $this->input->get('splits');
                    $bbq_in_discount = $this->input->post('bbq_in_discount');

                    $response = $this->pos_model->BBQaddSale($notification_array, $timelog_array, $order_data, $splitData, $saleorder_item, $sale, $sale_items, $bilsdata, $bil_items, $order_id, $bbq_array, $splits, $request_discount, $bbq_in_discount);

                    //$response = $this->pos_model->BBQaddSale($sale, $sale_items, $bilsdata, $bil_items, $order_id);

                    if ($response == true) {$update_notifi['split_id'] = $split_id;
                        $update_notifi['tag'] = 'bill-request';
                        $this->site->update_notification_status($update_notifi);
                        admin_redirect("pos/order_bbqtable");
                    } else {
                        $this->data['order_bbq'] = $this->pos_model->BBQtablesplit($table_id, $split_id);
                        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
                        $this->load->view($this->theme . 'pos/bbqautosplitbil', $this->data);
                    }

                }
            } else {
                $this->data['order_bbq'] = $this->pos_model->BBQtablesplit($table_id, $split_id);
                $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
                $this->load->view($this->theme . 'pos/bbqautosplitbil', $this->data);
            }

        } elseif ($bill_type == 3) {

            $this->form_validation->set_rules('splits', $this->lang->line("splits"), 'trim|required');
            if ($this->form_validation->run() == true) {

                if ($this->input->post('action') == "MANUALSPLITBILL-SUBMIT") {

                    $splits = $this->input->post('splits');

                    $bbq_discount = implode(',', $this->input->post('bbq_discount'));
                    $ptax = implode(',', $this->input->post('ptax'));

                    $notification_array['from_role'] = $group_id;
                    $notification_array['insert_array'] = array(
                        'msg' => 'Waiter has been bil generator to ' . $split_id,
                        'type' => 'Bil generator (' . $split_id . ')',
                        'table_id' => $table_id,
                        'role_id' => 8,
                        'user_id' => $user_id,
                        'warehouse_id' => $warehouse_id,
                        'created_on' => date('Y-m-d H:m:s'),
                        'is_read' => 0,
                    );

                    for ($i = 0; $i < $this->input->post('bils'); $i++) {

                        if ($_POST['number_of_covers'][$i] != 0) {
                            $bbq_discount_amount[] = $_POST['bbq_discount_amount'][$i];
                            $tax_amount[] = $_POST['tax_amount'][$i];
                            $total_amount[] = $_POST['total_amount'][$i];
                            $gtotal[] = $_POST['gtotal'][$i];

                            $adult_price[] = $_POST['adult_price'][$i];
                            $number_of_adult[] = $_POST['number_of_adult'][$i];
                            $adult_subprice[] = $_POST['adult_subprice'][$i];

                            $child_price[] = $_POST['child_price'][$i];
                            $number_of_child[] = $_POST['number_of_child'][$i];
                            $child_subprice[] = $_POST['child_subprice'][$i];

                            $kids_price[] = $_POST['kids_price'][$i];
                            $number_of_kids[] = $_POST['number_of_kids'][$i];
                            $kids_subprice[] = $_POST['kids_subprice'][$i];

                            $number_of_covers[] = $_POST['number_of_covers'][$i];

                            $adult_discount_cover[] = $_POST['adult_discount_cover'][$i];
                            $child_discount_cover[] = $_POST['child_discount_cover'][$i];
                            $kids_discount_cover[] = $_POST['kids_discount_cover'][$i];

                        }

                    }

                    $bbq_discount_amount = array_sum($bbq_discount_amount);
                    $tax_amount = array_sum($tax_amount);
                    $total_amount = array_sum($total_amount);
                    $gtotal = array_sum($gtotal);

                    $adult_price = array_sum($adult_price);
                    $number_of_adult = array_sum($number_of_adult);
                    $adult_subprice = array_sum($adult_subprice);

                    $child_price = array_sum($child_price);
                    $number_of_child = array_sum($number_of_child);
                    $child_subprice = array_sum($child_subprice);

                    $kids_price = array_sum($kids_price);
                    $number_of_kids = array_sum($number_of_kids);
                    $kids_subprice = array_sum($kids_subprice);

                    $number_of_covers = array_sum($number_of_covers);

                    $adult_discount_cover = array_sum($adult_discount_cover);
                    $child_discount_cover = array_sum($child_discount_cover);
                    $kids_discount_cover = array_sum($kids_discount_cover);

                    $bbq_array = array(
                        'number_of_adult' => $number_of_adult,
                        'number_of_child' => $number_of_child,
                        'number_of_kids' => $number_of_kids,
                    );

                    $sale = array(
                        'bilgenerator_type' => 0,
                        'sales_type_id' => 4,
                        'sales_split_id' => $this->input->post('splits'),
                        'sales_table_id' => $this->input->post('table'),
                        'date' => date('Y-m-d H:i:s'),
                        'reference_no' => 'SALE' . date('YmdHis'),
                        'customer_id' => $this->input->post('customer_id'),
                        'customer' => $this->input->post('customer'),
                        'biller_id' => $this->input->post('biller_id'),
                        'biller' => $this->input->post('biller'),
                        'warehouse_id' => $this->input->post('warehouse_id'),
                        'total' => $total_amount,
                        'order_discount_id' => $bbq_discount,
                        'total_discount' => $bbq_discount_amount,
                        'order_tax_id' => $ptax,
                        'total_tax' => $tax_amount,
                        'grand_total' => $gtotal,
                        'total_items' => $number_of_covers,
                    );

                    $sale_items[] = array(
                        'type' => 'adult',
                        'cover' => $number_of_adult,
                        'days' => $_POST['adult_days'][0],
                        'buyx' => $_POST['adult_buyx'][0],
                        'getx' => $_POST['adult_getx'][0],
                        'discount_cover' => $adult_discount_cover,
                        'price' => $adult_price,
                        'subtotal' => $adult_subprice,
                        'created' => date('Y-m-d H:i:s'),
                    );

                    $sale_items[] = array(
                        'type' => 'child',
                        'cover' => $number_of_child,
                        'days' => $_POST['child_days'][0],
                        'buyx' => $_POST['child_buyx'][0],
                        'getx' => $_POST['child_getx'][0],
                        'discount_cover' => $child_discount_cover,
                        'price' => $child_price,
                        'subtotal' => $child_subprice,
                        'created' => date('Y-m-d H:i:s'),
                    );

                    $sale_items[] = array(
                        'type' => 'kids',
                        'cover' => $number_of_kids,
                        'days' => $_POST['kids_days'][0],
                        'buyx' => $_POST['kids_buyx'][0],
                        'getx' => $_POST['kids_getx'][0],
                        'discount_cover' => $kids_discount_cover,
                        'price' => $kids_price,
                        'subtotal' => $kids_subprice,
                        'created' => date('Y-m-d H:i:s'),
                    );

                    for ($i = 0; $i < $this->input->post('bils'); $i++) {

                        if (!empty($this->input->post('bbq_discount'))) {

                            $request_discount[$i] = array(
                                'customer_id' => $customer_id,
                                'waiter_id' => $this->session->userdata('user_id'),
                                'table_id' => $table_id,
                                'split_id' => $split_id,
                                'bbq_type_val' => $this->Settings->bbq_discount ? $this->Settings->bbq_discount : '',
                                'bbq_discount_val' => $this->input->post('bbq_discount_id') ? $this->input->post('bbq_discount_id') : '',
                                'created_on' => date('Y-m-d H:i:s'),
                            );
                        }

                        if ($_POST['number_of_covers'][$i] != 0) {
                            $bilsdata[$i] = array(
                                'bilgenerator_type' => 0,
                                'date' => date('Y-m-d H:i:s'),
                                'reference_no' => 'SALE' . date('YmdHis') . $i,
                                'customer_id' => $this->input->post('customer_id'),
                                'customer' => $this->input->post('customer'),
                                'biller_id' => $this->input->post('biller_id'),
                                'biller' => $this->input->post('biller'),
                                'warehouse_id' => $this->input->post('warehouse_id'),
                                'created_by' => $this->session->userdata('user_id'),
                                'total' => $_POST['total_amount'][$i],
                                'order_discount_id' => $_POST['bbq_discount'][$i],
                                'total_discount' => $_POST['bbq_discount_amount'][$i],
                                'tax_id' => $_POST['ptax'][$i],
                                'total_tax' => $_POST['tax_amount'][$i],
                                'tax_type' => $_POST['tax_type'][$i],
                                'grand_total' => $_POST['gtotal'][$i],
                                'total_items' => $_POST['number_of_covers'][$i],
                            );

                            $adult_discount[$i] = $this->site->calculateDiscount($_POST['bbq_discount'][$i], $_POST['adult_subprice'][$i]);
                            $adult_disfinal[$i] = $_POST['adult_subprice'][$i] - $adult_discount[$i];
                            $adult_tax_id[$i] = $this->pos_settings->default_tax;
                            $adult_tax_type[$i] = $this->pos_settings->tax_type;
                            $adult_tax[$i] = $this->site->calculateOrderTax($this->pos_settings->default_tax, $adult_disfinal[$i]);

                            $bil_items[$i][] = array(
                                'type' => 'adult',
                                'cover' => $_POST['number_of_adult'][$i],
                                'price' => $_POST['adult_price'][$i],
                                'days' => $_POST['adult_days'][$i],
                                'buyx' => $_POST['adult_buyx'][$i],
                                'getx' => $_POST['adult_getx'][$i],
                                'discount_cover' => $_POST['adult_discount_cover'][$i],
                                'discount' => $adult_discount[$i],
                                'tax_id' => $adult_tax_id[$i],
                                'tax_type' => $adult_tax_type[$i],
                                'tax' => $adult_tax[$i],
                                'subtotal' => $_POST['adult_subprice'][$i],
                                'created' => date('Y-m-d H:i:s'),
                            );

                            $child_discount[$i] = $this->site->calculateDiscount($_POST['bbq_discount'][$i], $_POST['child_subprice'][$i]);
                            $child_disfinal[$i] = $_POST['child_subprice'][$i] - $child_discount[$i];
                            $child_tax_id[$i] = $this->pos_settings->default_tax;
                            $child_tax_type[$i] = $this->pos_settings->tax_type;
                            $child_tax[$i] = $this->site->calculateOrderTax($this->pos_settings->default_tax, $child_disfinal[$i]);

                            $bil_items[$i][] = array(
                                'type' => 'child',
                                'cover' => $_POST['number_of_child'][$i],
                                'days' => $_POST['child_days'][$i],
                                'buyx' => $_POST['child_buyx'][$i],
                                'getx' => $_POST['child_getx'][$i],
                                'discount_cover' => $_POST['child_discount_cover'][$i],
                                'discount' => $child_discount[$i],
                                'tax_id' => $child_tax_id[$i],
                                'tax_type' => $child_tax_type[$i],
                                'tax' => $child_tax[$i],
                                'price' => $_POST['child_price'][$i],
                                'subtotal' => $_POST['child_subprice'][$i],
                                'created' => date('Y-m-d H:i:s'),
                            );

                            $kids_discount[$i] = $this->site->calculateDiscount($_POST['bbq_discount'][$i], $_POST['kids_subprice'][$i]);
                            $kids_disfinal[$i] = $_POST['kids_subprice'][$i] - $kids_discount[$i];
                            $kids_tax_id[$i] = $this->pos_settings->default_tax;
                            $kids_tax_type[$i] = $this->pos_settings->tax_type;
                            $kids_tax[$i] = $this->site->calculateOrderTax($this->pos_settings->default_tax, $kids_disfinal[$i]);

                            $bil_items[$i][] = array(
                                'type' => 'kids',
                                'cover' => $_POST['number_of_kids'][$i],
                                'days' => $_POST['kids_days'][$i],
                                'buyx' => $_POST['kids_buyx'][$i],
                                'getx' => $_POST['kids_getx'][$i],
                                'discount_cover' => $_POST['kids_discount_cover'][$i],
                                'discount' => $kids_discount[$i],
                                'tax_id' => $kids_tax_id[$i],
                                'tax_type' => $kids_tax_type[$i],
                                'tax' => $kids_tax[$i],
                                'price' => $_POST['kids_price'][$i],
                                'subtotal' => $_POST['kids_subprice'][$i],
                                'created' => date('Y-m-d H:i:s'),
                            );

                        }
                    }

                    $splits = $this->input->get('splits');
                    $bbq_in_discount = $this->input->post('bbq_in_discount');

                    $response = $this->pos_model->BBQaddSale($notification_array, $timelog_array, $order_data, $splitData, $saleorder_item, $sale, $sale_items, $bilsdata, $bil_items, $order_id, $bbq_array, $splits, $request_discount, $bbq_in_discount);

                    if ($response == true) {$update_notifi['split_id'] = $split_id;
                        $update_notifi['tag'] = 'bill-request';
                        $this->site->update_notification_status($update_notifi);
                        admin_redirect("pos/order_bbqtable");
                    } else {
                        $this->data['order_bbq'] = $this->pos_model->BBQtablesplit($table_id, $split_id);
                        $this->data['error'] = 'Buffet Manual covers is empty. Please added covers';
                        $this->load->view($this->theme . 'pos/bbqmanualsplitbil', $this->data);
                    }

                }

            } else {
                $this->data['order_bbq'] = $this->pos_model->BBQtablesplit($table_id, $split_id);
                $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error', 'Buffet Manual covers is empty. Please added covers');
                $this->load->view($this->theme . 'pos/bbqmanualsplitbil', $this->data);
            }
        }

    }

    public function bbqconsolidatedpaymant()
    {
        $this->data['warehouse'] = $this->session->userdata('warehouse_id') ? $this->site->getWarehouseByID($this->session->userdata('warehouse_id')) : null;

        $this->data['tax_rates'] = $this->site->getAllTaxRates();
        $currency = $this->site->getAllCurrencies();

        $postData = $this->input->post(); //echo "<pre>";print_R($postData);exit;
        if ($this->input->post('action') == "PAYMENT-SUBMIT") {

            //echo '<pre>';
            $pData = $this->input->post();

            //print_r($pData);
            $balance = $this->input->post('balance_amount');
            $dueamount = $this->input->post('due_amount');
            $total_pay = $this->input->post('total') + $this->input->post('balance_amount');
            $total = $this->input->post('total');
            $customer_id = $this->input->post('customer_id');
            $order_split_id = $this->input->post('order_split_id');
            $billid = $this->pos_model->getBilID($order_split_id);
            $salesid = $this->pos_model->getsalesID($order_split_id);

            foreach ($billid as $billid_row) {
                $billid_val[] = $billid_row->id;
                $salesid_val[] = $billid_row->sales_id;
            }

            $paid = !empty($dueamount) ? ($total - $dueamount) : $total;
            $p = isset($_POST['paid_by']) ? sizeof($_POST['paid_by']) : 0;
            $getExchangecode = $this->site->getExchangeRatecode($this->Settings->default_currency);
            $getExchangerate = $this->site->getExchangeRatey($this->Settings->default_currency);
            $default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
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
                    if ($amount) {
                        $payment[] = array(
                            'date' => date('Y-m-d H:i:s'),
                            'paid_on' => date('Y-m-d H:i:s'),
                            'amount' => $amount ? $amount : 0,
                            'amount_exchange' => 0,
                            'exchange_enable' => 0,
                            'pos_paid' => $_POST['amount_' . $default_currency_data->code . ''][$r],
                            'pos_balance' => round($balance, 3),
                            'paid_by' => $_POST['paid_by'][$r],
                            'cc_no' => $_POST['cc_no'][$r],
                            /* 'cheque_no'   => $_POST['cheque_no'][$r],
                            'cc_holder'   => $_POST['cc_holer'][$r],
                            'cc_month'    => $_POST['cc_month'][$r],
                            'cc_year'     => $_POST['cc_year'][$r],
                            'cc_type'     => $_POST['cc_type'][$r],
                            'sale_note'   => $_POST['sale_note'],
                            'staff_note'   => $_POST['staffnote'],
                            'payment_note' => $_POST['payment_note'][$r],*/
                            'created_by' => $this->session->userdata('user_id'),
                            'type' => 'received',
                        );
                    }
                    if (!empty($amount_exchange)) {
                        $amount_ex = $amount_exchange * $getExchangerate;
                        $payment[] = array(
                            'date' => date('Y-m-d H:i:s'),
                            'paid_on' => date('Y-m-d H:i:s'),
                            'amount' => $amount_ex ? $amount_ex : 0,
                            'amount_exchange' => $amount_exchange ? $amount_exchange : 0,
                            'exchange_enable' => 1,
                            'pos_paid' => $_POST['amount_' . $default_currency_data->code . ''][$r],
                            'pos_balance' => round($balance, 3),
                            'paid_by' => $_POST['paid_by'][$r],
                            'cc_no' => $_POST['cc_no'][$r],
                            /* 'cheque_no'   => $_POST['cheque_no'][$r],
                            'cc_holder'   => $_POST['cc_holer'][$r],
                            'cc_month'    => $_POST['cc_month'][$r],
                            'cc_year'     => $_POST['cc_year'][$r],
                            'cc_type'     => $_POST['cc_type'][$r],
                            'sale_note'   => $_POST['sale_note'],
                            'staff_note'   => $_POST['staffnote'],
                            'payment_note' => $_POST['payment_note'][$r],*/
                            'created_by' => $this->session->userdata('user_id'),
                            'type' => 'received',
                        );
                    }
                }
            }

            $alacat = 0;
            //echo '<pre>';print_R($payment);
            foreach ($payment as $key => $pay) {
                if ($alacat <= 0) {
                    if ($billid[0]->grand_total <= $pay['amount']) {
                        $bil_id = $billid[1]->id;
                        $sale_id = $billid[1]->sales_id;

                        $alacat -= $this->sma->formatDecimal($billid[0]->grand_total) - $this->sma->formatDecimal($pay['amount']);
                    } elseif ($billid[0]->grand_total > $pay['amount']) {
                        $alacat += $this->sma->formatDecimal($billid[0]->grand_total) - $this->sma->formatDecimal($pay['amount']);
                        $bil_id = $billid[1]->id;
                        $sale_id = $billid[1]->sales_id;
                    }

                    $consolidatedpayment[$key] = array(
                        'date' => $pay['date'],
                        'paid_on' => date('Y-m-d H:i:s'),
                        'amount' => $this->sma->formatDecimal($billid[0]->grand_total),
                        'amount_exchange' => 0,
                        'bill_id' => $billid[0]->id,
                        'sale_id' => $billid[0]->sales_id,
                        'exchange_enable' => $pay['exchange_enable'],
                        'paid_by' => $pay['paid_by'],
                        'cc_no' => $pay['cc_no'],
                        /* 'cheque_no'   => $pay['cheque_no'],
                        'cc_holder'   => $pay['cc_holer'],
                        'cc_month'    => $pay['cc_month'],
                        'cc_year'     => $pay['cc_year'],
                        'cc_type'     => $pay['cc_type'],
                        'sale_note'   => $pay['sale_note'] != NULL ? $pay['sale_note'] : '',
                        'staff_note'   => $pay['staffnote'] != NULL ? $pay['staffnote'] : '',
                        'payment_note' => $pay['payment_note'] != NULL ? $pay['payment_note'] : '',*/
                        'created_by' => $this->session->userdata('user_id'),
                        'type' => 'received',
                    );
                    if ($pay['exchange_enable'] == 1) {

                        $amount_exchange = $alacat / $getExchangerate;
                        $alacat = 0;
                    } else {
                        $amount_exchange = 0;
                        $alacat = $this->sma->formatDecimal($alacat);
                    }

                    $consolidatedpayment[$key + 1] = array(
                        'date' => $pay['date'],
                        'paid_on' => date('Y-m-d H:i:s'),
                        'amount' => $this->sma->formatDecimal($alacat),
                        'amount_exchange' => $amount_exchange,
                        'bill_id' => $bil_id,
                        'sale_id' => $sale_id,
                        'exchange_enable' => $pay['exchange_enable'],
                        'paid_by' => $pay['paid_by'],
                        'cc_no' => $pay['cc_no'],
                        /*'cheque_no'   => $pay['cheque_no'],
                        'cc_holder'   => $pay['cc_holer'],
                        'cc_month'    => $pay['cc_month'],
                        'cc_year'     => $pay['cc_year'],
                        'cc_type'     => $pay['cc_type'],
                        'sale_note'   => $pay['sale_note'] != NULL ? $pay['sale_note'] : '',
                        'staff_note'   => $pay['staffnote'] != NULL ? $pay['staffnote'] : '',
                        'payment_note' => $pay['payment_note'] != NULL ? $pay['payment_note'] : '',*/
                        'created_by' => $this->session->userdata('user_id'),
                        'type' => 'received',
                    );
                    if (isset($_POST['rough_tender'])) {
                        $consolidatedpayment[$key]['loyalty_points'] = $_POST['paying_loyalty_points'][$r];
                    }

                } else {
                    if ($pay['exchange_enable'] == 1) {
                        $amount_exchange = $pay['amount'] / $getExchangerate;
                        $pay_amount = 0;
                    } else {
                        $amount_exchange = 0;
                        $pay_amount = $this->sma->formatDecimal($pay['amount']);
                    }
                    $consolidatedpayment[$key] = array(
                        'date' => $pay['date'],
                        'paid_on' => date('Y-m-d H:i:s'),
                        'amount' => $pay_amount,
                        'amount_exchange' => $amount_exchange,
                        'bill_id' => $billid[1]->id,
                        'sale_id' => $billid[1]->sales_id,
                        'exchange_enable' => $pay['exchange_enable'],
                        'paid_by' => $pay['paid_by'],
                        'cc_no' => $pay['cc_no'],
                        /*'cheque_no'   => $pay['cheque_no'],
                        'cc_holder'   => $pay['cc_holer'],
                        'cc_month'    => $pay['cc_month'],
                        'cc_year'     => $pay['cc_year'],
                        'cc_type'     => $pay['cc_type'],
                        'sale_note'   => $pay['sale_note'] != NULL ? $pay['sale_note'] : '',
                        'staff_note'   => $pay['staffnote'] != NULL ? $pay['staffnote'] : '',
                        'payment_note' => $pay['payment_note'] != NULL ? $pay['payment_note'] : '',*/
                        'created_by' => $this->session->userdata('user_id'),
                        'type' => 'received',
                    );
                    if (isset($_POST['rough_tender'])) {
                        $consolidatedpayment[$key]['loyalty_points'] = $_POST['paying_loyalty_points'][$r];
                    }
                }
            }

            foreach ($consolidatedpayment as $key => $consolidated) {

                foreach ($currency as $currency_row) {
                    if ($default_currency_data->code == $currency_row->code) {

                        if ($consolidatedpayment[$key]['amount_exchange'] == 0) {
                            $amount_val = $consolidatedpayment[$key]['amount'];
                            $multi_currency[$key] = array(
                                'sale_id' => $consolidatedpayment[$key]['sale_id'],
                                'bil_id' => $consolidatedpayment[$key]['bill_id'],
                                'currency_id' => $currency_row->id,
                                'currency_rate' => $currency_row->rate,
                                'amount' => $amount_val,
                            );
                        } else {
                            $amount_val = $consolidatedpayment[$key]['amount_exchange'];
                        }
                    } else {

                        if ($consolidatedpayment[$key]['amount_exchange'] == 0) {
                            $amount_val = $consolidatedpayment[$key]['amount'];

                        } else {
                            $amount_val = $consolidatedpayment[$key]['amount_exchange'];
                            $multi_currency[$key] = array(
                                'sale_id' => $consolidatedpayment[$key]['sale_id'],
                                'bil_id' => $consolidatedpayment[$key]['bill_id'],
                                'currency_id' => $currency_row->id,
                                'currency_rate' => $currency_row->rate,
                                'amount' => $amount_val,
                            );
                        }
                    }
                }
            }

            $taxation = $this->input->post('taxation') ? $this->input->post('taxation') : 0;
            $update_bill[$billid[0]->id] = array(
                'updated_at' => date('Y-m-d H:i:s'),
                'paid_by' => $this->session->userdata('user_id'),
                'total_pay' => $billid[0]->grand_total,
                'balance' => 0.00,
                'paid' => $billid[0]->grand_total,
                'payment_status' => 'Completed',
                'default_currency_code' => $default_currency_data->code,
                'default_currency_rate' => $default_currency_data->rate,
                'table_whitelisted' => $taxation,
            );
            $update_bill[$billid[1]->id] = array(
                'updated_at' => date('Y-m-d H:i:s'),
                'paid_by' => $this->session->userdata('user_id'),
                'total_pay' => $total_pay - $billid[0]->grand_total,
                'balance' => $balance,
                'paid' => $paid - $billid[0]->grand_total,
                'payment_status' => 'Completed',
                'default_currency_code' => $default_currency_data->code,
                'default_currency_rate' => $default_currency_data->rate,
                'table_whitelisted' => $taxation,
            );

            $sales_bill[$billid[0]->sales_id] = array(
                'grand_total' => $billid[0]->grand_total,
                'paid' => $billid[0]->grand_total,
                'payment_status' => 'Paid',
                'sale_status' => 'Closed',
                'default_currency_code' => $default_currency_data->code,
                'default_currency_rate' => $default_currency_data->rate,
            );

            $sales_bill[$billid[1]->sales_id] = array(
                'grand_total' => $billid[1]->grand_total,
                'paid' => $billid[1]->grand_total,
                'payment_status' => 'Paid',
                'sale_status' => 'Closed',
                'default_currency_code' => $default_currency_data->code,
                'default_currency_rate' => $default_currency_data->rate,
            );

            $updateCreditLimit['company_id'] = $postData['company_id'];
            $updateCreditLimit['customer_type'] = $postData['customer_type'];

            $q = $this->db->select('*')->where_in('bill_id', $billid_val)->get('payments');
            // print_r($this->db->last_query());die;
            if (isset($_POST['rough_tender'])) {
                $q = $this->db->select('*')->where_in('bill_id', $billid_val)->get('rough_tender_payments');
            }
            if ($q->num_rows() > 1) {
                $response = 1;
            } else {


                $waiter_id = $this->pos_model->splitWaiterid($order_split_id);
                //$device_token = $this->pos_model->deviceGET($waiter_id);
                $deviceDetails = $this->pos_model->deviceDetails($waiter_id);
                $device_token = @$deviceDetails->device_token;
                $title = 'BBQ Return (' . $order_split_id . ')';
                $message = 'The cashier check payment status has been done. please check bbq return process -  ' . $order_split_id;
                $push_data = $this->push->setPush($title, $message);
                if (!isset($_POST['rough_tender'])) {
                    if ($this->site->isSocketEnabled() && $push_data == true && isset($deviceDetails->socket_id) && $deviceDetails->socket_id != '') {
                        $json_data = '';
                        $response_data = '';
                        $json_data = $this->push->getPush();
                        $regId_data = $device_token;
                        //$response_data = $this->firebase->send($regId_data, $json_data);
                        $socket_id = $deviceDetails->socket_id;
                        $this->site->send_pushNotification($title, $message, $socket_id);
                    }
                }

                $notification_array['from_role'] = $this->session->userdata('group_id');
                $notification_array['insert_array'] = array(
                    'user_id' => $this->session->userdata('user_id'),
                    'warehouse_id' => $this->session->userdata('warehouse_id'),
                    'created_on' => date('Y-m-d H:m:s'),
                    'is_read' => 0,
                );
                $loyalty_used_points = $this->input->post('loyalty_used_points') ? $this->input->post('loyalty_used_points') : 0;
                $new_payment = true;
                $bill_id = $billid[1]->id;
                if (isset($_POST['rough_tender'])) {
                    //echo '<pre>';
                    //print_R($billid_val);
                    //print_R($consolidatedpayment);
                    //print_R($multi_currency);
                    //exit;

                    $response = $this->pos_model->addRoughTender($billid_val, $consolidatedpayment, $multi_currency, $updateCreditLimit, 'BBQCON');
                } else {
                    $response = $this->pos_model->BBQCONPayment($update_bill, $billid_val, $consolidatedpayment, $multi_currency, $salesid_val, $sales_bill, $order_split_id, $notification_array, $updateCreditLimit, $total, $customer_id, $loyalty_used_points, $bill_id, $taxation);
                }
            }

            if ($response == 1) {
                $update_notifi['split_id'] = $order_split_id;
                $update_notifi['tag'] = 'bill-request';
                $this->site->update_notification_status($update_notifi);
                if ($taxation == 1) {
                    admin_redirect("pos/biller_bbqconsolidated");
                }

                foreach ($billid as $billid_row) {

                    if ($billid_row->sales_type_id == 1) {

                        $this->data['dine']['order_item'] = $this->pos_model->getAllBillitems($billid_row->id);

                        $dine_inv = $this->pos_model->getInvoiceByID($billid_row->id);
                        $dine_tableno = $this->pos_model->getTableNumber($billid_row->id);

                        /*$this->data['rows'] = $this->pos_model->getAllInvoiceItems($billid_row->id);*/
                        $this->data['dine']['billi_tems'] = $this->pos_model->getAllBillitems($billid_row->id);
                        $this->data['dine']['discounnames'] = $this->pos_model->getBillDiscountNames($billid_row->id);

                        $dine_biller_id = $dine_inv->biller_id;
                        $dine_bill_id = $dine_inv->sales_id;

                        $dine_customer_id = $dine_inv->customer_id;
                        $dine_delivery_person_id = $dine_inv->delivery_person_id;

                        $this->data['dine']['inv'] = $dine_inv;
                        $this->data['dine']['tableno'] = $dine_tableno;
                        $this->data['dine']['customer'] = $this->pos_model->getCompanyByID($dine_customer_id);

                        if ($dine_delivery_person_id != 0) {
                            $this->data['dine']['delivery_person'] = $this->pos_model->getUserByID($dine_delivery_person_id);
                        }
                        $this->data['dine']['created_by'] = $this->site->getUser($dine_inv->created_by);
                        $this->data['dine']['cashier'] = $this->pos_model->getCashierInfo($billid_row->id);
                        $this->data['printer'] = $this->pos_model->getPrinterByID($this->pos_settings->printer);
                        $this->data['biller'] = $this->pos_model->getCompanyByID($dine_biller_id);
                        if (isset($_POST['rough_tender'])) {
                            $this->data['dine']['inv']->balance = $update_bill[$billid[0]->id]['balance'];
                            $this->data['dine']['payments'] = $this->pos_model->getInvoiceRoughTenderPayments($billid_row->id);
                        } else {
                            $this->data['dine']['payments'] = $this->pos_model->getInvoicePayments($billid_row->id);
                        } /*echo "<pre>";
                        var_du($this->data['payments']);die;*/
                        $this->data['dine']['return_sale'] = $dine_inv->return_id ? $this->pos_model->getInvoiceByID($dine_inv->return_id) : null;
                        $this->data['dine']['return_rows'] = $dine_inv->return_id ? $this->pos_model->getAllInvoiceItems($dine_inv->return_id) : null;
                        $this->data['dine']['return_payments'] = $this->data['return_sale'] ? $this->pos_model->getInvoicePayments($this->data['return_sale']->id) : null;

                    } elseif ($billid_row->sales_type_id == 4) {

                        /*#################################*/
                        $this->data['bbq']['order_item'] = $this->pos_model->getBBQAllBillitems($billid_row->id);
                        $bbq_inv = $this->pos_model->getBBQInvoiceByID($billid_row->id);
                        $this->data['bbq']['inv'] = $bbq_inv;
                        $bbq_tableno = $this->pos_model->getBBQTableNumber($billid_row->id);
                        $this->data['bbq_tableno'] = $bbq_tableno;
                        //$this->data['billi_tems'] = $this->pos_model->getAllBillitems($billid);
                        $bbq_biller_id = $bbq_inv->biller_id;
                        $bbq_bill_id = $bbq_inv->sales_id;
                        $bbq_customer_id = $bbq_inv->customer_id;
                        $this->data['bbq']['billi_tems'] = $this->pos_model->getBBQAllBillitems($billid_row->id);
                        $this->data['bbq']['discount'] = $this->pos_model->BBQgetBillDiscountNames($billid_row->id);
                        $this->data['bbq']['DiscountCovers'] = $this->pos_model->getBBQBillDiscountCovers($billid_row->id);
                        $this->data['customer'] = $this->pos_model->getCompanyByID($bbq_customer_id);
                        $this->data['bbq']['created_by'] = $this->site->getUser($bbq_inv->created_by);
                        $this->data['printer'] = $this->pos_model->getPrinterByID($this->pos_settings->printer);
                        $this->data['bbq']['biller'] = $this->pos_model->getCompanyByID($bbq_biller_id);
                        if (isset($_POST['rough_tender'])) {
                            $this->data['bbq']['inv']->balance = $update_bill[$billid[1]->id]['balance'];
                            $this->data['bbq']['payments'] = $this->pos_model->getInvoiceRoughTenderPayments($billid_row->id);
                        } else {
                            $this->data['bbq']['payments'] = $this->pos_model->getBBQInvoicePayments($billid_row->id);
                        }

                    }
                }

                $tableid = $this->pos_model->getBBQTableID($billid_row->id);
                if (!empty($dine_inv) || !empty($bbq_inv)) {

                    if (!isset($_POST['rough_tender'])) {
                        if (@$new_payment) {
                            if ($this->site->isSocketEnabled()) {
                                $this->load->library('socketemitter');
                                $this->data['socket_tableid'] = $tableid;
                                $socketEmit['user_id'] = $this->session->userdata('user_id');
                                $socketEmit['group_id'] = $this->session->userdata('group_id');
                                $socketEmit['table_id'] = $tableid;
                                $socketEmit['warehouse_id'] = $this->session->userdata('warehouse_id');
                                $socketEmit['bbq_code'] = $order_split_id;
                                $event = 'bbq_return_request';
                                $edata = $socketEmit;
                                $this->socketemitter->setEmit($event, $edata);
                            }
                        }
                    }
                    if (isset($_POST['rough_tender'])) {
                        $this->data['rough_tender'] = true;
                    }
                    $this->load->view($this->theme . 'pos/bbqconsolidated_view_bill', $this->data);
                } else {
                    admin_redirect("pos/biller_bbqconsolidated?bbqtid=" . $tableid);
                }
            } else {
                admin_redirect("pos/biller_bbqconsolidated");
            }

        } else {
            admin_redirect("pos/biller_bbqconsolidated");
        }
    }

    public function consolidated_reprint_view()
    {
        $bill_no = $this->input->get('bill_no');
        $dinein_id = $this->pos_model->getDineFromConsolidatebill($bill_no);
        $this->data['dine']['order_item'] = $this->pos_model->getAllBillitems($dinein_id->id);
        $dine_inv = $this->pos_model->getInvoiceByID($dinein_id->id);
        $dine_tableno = $this->pos_model->getTableNumber($dinein_id->id);

        $this->data['dine']['billi_tems'] = $this->pos_model->getAllBillitems($dinein_id->id);
        $this->data['discounnames'] = $this->pos_model->getBillDiscountNames($dinein_id->id);

        $dine_biller_id = $dine_inv->biller_id;
        $dine_bill_id = $dine_inv->sales_id;

        $dine_customer_id = $dine_inv->customer_id;
        $dine_delivery_person_id = $dine_inv->delivery_person_id;

        $this->data['dine']['inv'] = $dine_inv;
        $this->data['dine']['tableno'] = $dine_tableno;
        $this->data['dine']['customer'] = $this->pos_model->getCompanyByID($dine_customer_id);

        if ($dine_delivery_person_id != 0) {
            $this->data['dine']['delivery_person'] = $this->pos_model->getUserByID($dine_delivery_person_id);
        }
        $this->data['dine']['created_by'] = $this->site->getUser($dine_inv->created_by);
        $this->data['dine']['cashier'] = $this->pos_model->getCashierInfo($dinein_id->id);
        $this->data['printer'] = $this->pos_model->getPrinterByID($this->pos_settings->printer);
        $this->data['biller'] = $this->pos_model->getCompanyByID($dine_biller_id);

        $this->data['dine']['payments'] = $this->pos_model->getInvoicePayments($dinein_id->id);

        $this->data['dine']['return_sale'] = $dine_inv->return_id ? $this->pos_model->getInvoiceByID($dine_inv->return_id) : null;
        $this->data['dine']['return_rows'] = $dine_inv->return_id ? $this->pos_model->getAllInvoiceItems($dine_inv->return_id) : null;
        $this->data['dine']['return_payments'] = $this->data['return_sale'] ? $this->pos_model->getInvoicePayments($this->data['return_sale']->id) : null;
/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
        $bbq_id = $this->pos_model->getBBQFromConsolidatebill($bill_no);

        $this->data['bbq']['order_item'] = $this->pos_model->getBBQAllBillitems($bbq_id->id);
        $bbq_inv = $this->pos_model->getBBQInvoiceByID($bbq_id->id);
        $this->data['bbq']['inv'] = $bbq_inv;
        $bbq_tableno = $this->pos_model->getBBQTableNumber($bbq_id->id);
        $this->data['bbq_tableno'] = $bbq_tableno;

        $bbq_biller_id = $bbq_inv->biller_id;
        $bbq_bill_id = $bbq_inv->sales_id;
        $bbq_customer_id = $bbq_inv->customer_id;
        $this->data['bbq']['billi_tems'] = $this->pos_model->getBBQAllBillitems($bbq_id->id);
        $this->data['bbq']['DiscountCovers'] = $this->pos_model->getBBQBillDiscountCovers($bbq_id->id);
        $this->data['bbq']['discount'] = $this->pos_model->BBQgetBillDiscountNames($bbq_id->id);
        $this->data['customer'] = $this->pos_model->getCompanyByID($bbq_customer_id);
        $this->data['bbq']['created_by'] = $this->site->getUser($bbq_inv->created_by);
        $this->data['printer'] = $this->pos_model->getPrinterByID($this->pos_settings->printer);
        $this->data['bbq']['biller'] = $this->pos_model->getCompanyByID($bbq_biller_id);

        $this->data['bbq']['payments'] = $this->pos_model->getBBQInvoicePayments($bbq_id->id);
/*----------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
        $this->load->view($this->theme . 'pos/bbqconsolidated_reprint_viewbill', $this->data);

    }

    public function bbqpaymant()
    {

        $this->data['warehouse'] = $this->session->userdata('warehouse_id') ? $this->site->getWarehouseByID($this->session->userdata('warehouse_id')) : null;

        $this->data['tax_rates'] = $this->site->getAllTaxRates();
        $currency = $this->site->getAllCurrencies();

        $postData = $this->input->post(); //echo "<pre>";print_R($postData);//exit;
        if ($this->input->post('action') == "PAYMENT-SUBMIT") {

            $balance = $this->input->post('balance_amount');
            $dueamount = $this->input->post('due_amount');
            $total_pay = $this->input->post('total') + $this->input->post('balance_amount');
            $total = $this->input->post('total');
            $customer_id = $this->input->post('customer_id');
            $order_split_id = $this->input->post('order_split_id');
            $paid = !empty($dueamount) ? ($total - $dueamount) : $total;
            $p = isset($_POST['paid_by']) ? sizeof($_POST['paid_by']) : 0;

            $getExchangecode = $this->site->getExchangeRatecode($this->Settings->default_currency);
            $default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);

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
                    $payment[$r] = array(
                        'date' => date('Y-m-d H:i:s'),
                        'paid_on' => date('Y-m-d H:i:s'),
                        'sale_id' => $_POST['sales_id'],
                        'bill_id' => $_POST['bill_id'],
                        'amount' => $amount ? $amount : 0,
                        'amount_exchange' => $amount_exchange ? $amount_exchange : 0,
                        'pos_paid' => $_POST['amount_' . $default_currency_data->code . ''][$r],
                        'pos_balance' => round($balance, 3),
                        'paid_by' => $_POST['paid_by'][$r],
                        'cc_no' => $_POST['cc_no'][$r],
                        /* 'cheque_no'   => $_POST['cheque_no'][$r],
                        'cc_no'       => $_POST['cc_no'][$r],
                        'cc_holder'   => $_POST['cc_holer'][$r],
                        'cc_month'    => $_POST['cc_month'][$r],
                        'cc_year'     => $_POST['cc_year'][$r],
                        'cc_type'     => $_POST['cc_type'][$r],
                        'sale_note'   => $_POST['sale_note'],
                        'staff_note'   => $_POST['staffnote'],
                        'payment_note' => $_POST['payment_note'][$r],*/
                        'created_by' => $this->session->userdata('user_id'),
                        'type' => 'received',
                    );
                    if (isset($_POST['rough_tender'])) {
                        $payment[$r]['loyalty_points'] = $_POST['paying_loyalty_points'][$r];
                    }
                }
            }
            /*echo "<pre>";
            print_r($payment);die;*/
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

            $q = $this->db->select('*')->where('bill_id', $billid)->get('payments');
            if (isset($_POST['rough_tender'])) {
                $q = $this->db->select('*')->where('bill_id', $billid)->get('rough_tender_payments');
            }
            if ($q->num_rows() > 0) {
                $response = 1;
            } else {
                $waiter_id = $this->pos_model->splitWaiterid($order_split_id);
                //$device_token = $this->pos_model->deviceGET($waiter_id);
                $deviceDetails = $this->pos_model->deviceDetails($waiter_id);
                $device_token = @$deviceDetails->device_token;
                $title = 'BBQ Return (' . $order_split_id . ')';
                $message = 'The cashier check payment status has been done. please check bbq return process -  ' . $order_split_id;
                if (!isset($_POST['rough_tender'])) {
                    $push_data = $this->push->setPush($title, $message);
                    if ($this->site->isSocketEnabled() && $push_data == true && isset($deviceDetails->socket_id) && $deviceDetails->socket_id != '') {
                        $json_data = '';
                        $response_data = '';
                        $json_data = $this->push->getPush();
                        $regId_data = $device_token;
                        //$response_data = $this->firebase->send($regId_data, $json_data);
                        $socket_id = $deviceDetails->socket_id;
                        $this->site->send_pushNotification($title, $message, $socket_id);
                    }
                }

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

                $new_payment = true;
                if (isset($_POST['rough_tender'])) {
                    $response = $this->pos_model->addRoughTender($billid, $payment, $multi_currency, $updateCreditLimit);
                } else {
                    $response = $this->pos_model->BBQPayment($update_bill, $billid, $payment, $multi_currency, $salesid, $sales_bill, $order_split_id, $notification_array, $updateCreditLimit, $total, $customer_id, $loyalty_used_points, $taxation);
                }
            }

            if ($response == 1) {
                $update_notifi['split_id'] = $order_split_id;
                $update_notifi['tag'] = 'bill-request';
                $this->site->update_notification_status($update_notifi);
                if ($taxation == 1) {
                    admin_redirect("pos/biller_bbqtable");
                }

                $this->data['order_item'] = $this->pos_model->getBBQAllBillitems($billid);
                $inv = $this->pos_model->getBBQInvoiceByID($billid);
                $this->data['inv'] = $inv;
                $tableno = $this->pos_model->getBBQTableNumber($billid);
                $this->data['tableno'] = $tableno;
                //$this->data['billi_tems'] = $this->pos_model->getAllBillitems($billid);
                $biller_id = $inv->biller_id;
                $bill_id = $inv->sales_id;
                $customer_id = $inv->customer_id;
                $this->data['billi_tems'] = $this->pos_model->getBBQAllBillitems($billid);
                $this->data['DiscountCovers'] = $this->pos_model->getBBQBillDiscountCovers($billid);
                $this->data['discount'] = $this->pos_model->BBQgetBillDiscountNames($billid);
                $this->data['customer'] = $this->pos_model->getCompanyByID($customer_id);
                $this->data['created_by'] = $this->site->getUser($inv->created_by);
                $this->data['cashier'] = $this->pos_model->getCashierInfo($billid);
                $this->data['printer'] = $this->pos_model->getPrinterByID($this->pos_settings->printer);
                $this->data['biller'] = $this->pos_model->getCompanyByID($biller_id);
                if (isset($_POST['rough_tender'])) {
                    $this->data['inv']->balance = $update_bill['balance'];
                    $this->data['payments'] = $this->pos_model->getInvoiceRoughTenderPayments($this->input->post('bill_id'));
                } else {
                    $this->data['payments'] = $this->pos_model->getBBQInvoicePayments($this->input->post('bill_id'));
                    $this->data['type'] = $this->input->post('type');
                }

                $tableid = $this->pos_model->getBBQTableID($billid);
                if (!empty($inv)) {
                    if (!isset($_POST['rough_tender'])) {
                        if (@$new_payment) {
                            if ($this->site->isSocketEnabled()) {
                                $this->load->library('socketemitter');
                                $this->data['socket_tableid'] = $tableid;
                                $socketEmit['user_id'] = $this->session->userdata('user_id');
                                $socketEmit['group_id'] = $this->session->userdata('group_id');
                                $socketEmit['table_id'] = $tableid;
                                $socketEmit['warehouse_id'] = $this->session->userdata('warehouse_id');
                                $socketEmit['bbq_code'] = $order_split_id;
                                $event = 'bbq_return_request';
                                $edata = $socketEmit;
                                $this->socketemitter->setEmit($event, $edata);
                            }
                        }
                    }
                    if (isset($_POST['rough_tender'])) {
                        $this->data['rough_tender'] = true;
                    }
                    $this->load->view($this->theme . 'pos/bbq_view_bill', $this->data);
                } else {
                    admin_redirect("pos/biller_bbqtable?bbqtid=" . $tableid);
                }
            } else {
                admin_redirect("pos/biller_bbqtable");
            }

        } else {
            admin_redirect("pos/biller_bbqtable");
        }

    }

    public function bbq_reprint_view()
    {

        $billid = $this->input->get('bill_id');
        $this->data['order_item'] = $this->pos_model->getBBQAllBillitems($billid);
        $inv = $this->pos_model->getBBQInvoiceByID($billid);
        $this->data['inv'] = $inv;
        $tableno = $this->pos_model->getBBQTableNumber($billid);
        $this->data['tableno'] = $tableno;
        //$this->data['billi_tems'] = $this->pos_model->getAllBillitems($billid);
        $biller_id = $inv->biller_id;
        $bill_id = $inv->sales_id;
        $customer_id = $inv->customer_id;
        $this->data['billi_tems'] = $this->pos_model->getBBQAllBillitems($billid);
        $this->data['DiscountCovers'] = $this->pos_model->getBBQBillDiscountCovers($billid);
        $this->data['discount'] = $this->pos_model->BBQgetBillDiscountNames($billid);
        $this->data['customer'] = $this->pos_model->getCompanyByID($customer_id);
        $this->data['created_by'] = $this->site->getUser($inv->created_by);
        $this->data['cashier'] = $this->pos_model->getCashierInfo($billid);
        $this->data['printer'] = $this->pos_model->getPrinterByID($this->pos_settings->printer);
        $this->data['biller'] = $this->pos_model->getCompanyByID($biller_id);
        $this->data['payments'] = $this->pos_model->getBBQInvoicePayments($this->input->post('bill_id'));
        $this->data['type'] = $this->input->post('type');

        $this->load->view($this->theme . 'pos/bbq_reprint_viewbill', $this->data);

    }
    public function order_bbqtable()
    {

        $this->sma->checkPermissions('index');
        $user = $this->site->getUser();
        $this->data['warehouses'] = null;
        $this->data['warehouse_id'] = $this->session->userdata('warehouse_id');
        $this->data['warehouse'] = $this->session->userdata('warehouse_id') ? $this->site->getWarehouseByID($this->session->userdata('warehouse_id')) : null;
        $this->data['tableid'] = !empty($this->input->get('table')) ? $this->input->get('table') : '';
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->load->view($this->theme . 'pos/orderbbqtable', $this->data);
    }

    public function ajaxorder_bbqtable()
    {

        $table_id = !empty($this->input->get('table')) ? $this->input->get('table') : '';
        $this->data['tables'] = $this->pos_model->getAllBBQTablesorder($table_id);
        $this->data['avil_tables'] = $this->site->getAvilAbleTables($table_id);
        $this->data['avil_customers'] = $this->site->getAvilAbleCustomers();
        $this->load->view($this->theme . 'pos/orderbbqtable_ajax', $this->data);

    }

    public function biller_bbqtable($split_id = null, $bill_type = null, $sid = null)
    {

        $split_id = $this->input->get('split_id');
        $bill_type = $this->input->get('bill_type');

        $this->data['type'] = 4;
        $sales_type_id = 4;
        $this->data['sales_type'] = 'BBQ';

        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['sales_types'] = $this->site->getAllSalestype();
        $this->data['billers'] = $this->site->getAllCompanies('biller');
        /*$this->data['get_order_type'] = $order;*/
        $this->data['warehouse_id'] = $this->session->userdata('warehouse_id');
        $this->data['warehouse'] = $this->session->userdata('warehouse_id') ? $this->site->getWarehouseByID($this->session->userdata('warehouse_id')) : null;
        $this->data['sid'] = $this->input->get('suspend_id') ? $this->input->get('suspend_id') : $sid;

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $order_printers = json_decode($this->pos_settings->order_printers);
        $printers = array();
        if (!empty($order_printers)) {
            foreach ($order_printers as $printer_id) {
                $printers[] = $this->pos_model->getPrinterByID($printer_id);
            }
        }
        $this->data['order_printers'] = $printers;
        if ($sales_type_id) {
            $this->data['sales'] = $this->pos_model->getAllSalesWithbiller($sales_type_id);
            /*echo "<pre>";
        print_r($this->data['sales']);die;*/
        }
        $this->load->view($this->theme . 'pos/bbqorderbiller_newscreen', $this->data);
    }

    public function ajaxbbq_billing()
    {
        $sales_type_id = 4;
        $this->data['sales_type'] = 'BBQ';
        if ($sales_type_id) {
            $this->data['sales'] = $this->pos_model->getBBQAllSalesWithbiller($sales_type_id);
        }

        $this->load->view($this->theme . 'pos/bbqorderbiller_ajax', $this->data);
    }

    public function biller_bbqconsolidated($split_id = null, $bill_type = null, $sid = null)
    {

        $split_id = $this->input->get('split_id');
        $bill_type = $this->input->get('bill_type');

        $this->data['type'] = 4;
        $sales_type_id = 4;
        $this->data['sales_type'] = 'BBQ WITH DINE IN';

        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['sales_types'] = $this->site->getAllSalestype();
        $this->data['billers'] = $this->site->getAllCompanies('biller');
        /*$this->data['get_order_type'] = $order;*/
        $this->data['warehouse_id'] = $this->session->userdata('warehouse_id');
        $this->data['warehouse'] = $this->session->userdata('warehouse_id') ? $this->site->getWarehouseByID($this->session->userdata('warehouse_id')) : null;
        $this->data['sid'] = $this->input->get('suspend_id') ? $this->input->get('suspend_id') : $sid;

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $order_printers = json_decode($this->pos_settings->order_printers);
        $printers = array();
        if (!empty($order_printers)) {
            foreach ($order_printers as $printer_id) {
                $printers[] = $this->pos_model->getPrinterByID($printer_id);
            }
        }
        $this->data['order_printers'] = $printers;
        if ($sales_type_id) {
            $this->data['sales'] = $this->pos_model->getCONAllSalesWithbiller($sales_type_id);
            /*echo "<pre>";
        print_r($this->data['sales']);die;*/
        }
        $this->load->view($this->theme . 'pos/bbqconsolidatedbiller_newscreen', $this->data);
    }

    public function ajaxbbqconsolidated()
    {
        $sales_type_id = 4;
        $this->data['sales_type'] = 'BBQ WITH DINE IN';
        if ($sales_type_id) {
            $this->data['sales'] = $this->pos_model->getCONBBQAllSalesWithbiller($sales_type_id);
            /*echo "<pre>";
        print_r($this->data['sales']);*/
        }

        $this->load->view($this->theme . 'pos/bbqconsolidatedbiller_ajax', $this->data);
    }

    public function bbqreprinter()
    {

        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['sales_types'] = $this->site->getAllSalestype();
        $this->data['billers'] = $this->site->getAllCompanies('biller');

        $this->data['warehouse_id'] = $this->session->userdata('warehouse_id');
        $this->data['warehouse'] = $this->session->userdata('warehouse_id') ? $this->site->getWarehouseByID($this->session->userdata('warehouse_id')) : null;
        $this->data['sid'] = $this->input->get('suspend_id') ? $this->input->get('suspend_id') : $sid;

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

        $start = $this->input->get('date');
        if ($start) {
            $start = $start;

        } else {
            $start = date('Y-m-d');
        }

        $this->data['sales'] = $this->pos_model->getBBQAllBillingDatas($start);

        $this->load->view($this->theme . 'pos/bbqbill_reprint', $this->data);
    }

    public function consolidatedreprinter()
    {

        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['sales_types'] = $this->site->getAllSalestype();
        $this->data['billers'] = $this->site->getAllCompanies('biller');

        $this->data['warehouse_id'] = $this->session->userdata('warehouse_id');
        $this->data['warehouse'] = $this->session->userdata('warehouse_id') ? $this->site->getWarehouseByID($this->session->userdata('warehouse_id')) : null;
        $this->data['sid'] = $this->input->get('suspend_id') ? $this->input->get('suspend_id') : $sid;

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        //$this->data['sales'] = $this->pos_model->getBBQAllBillingDatas();
        $sales_type_id = 4;
        $this->data['sales_type'] = 'BBQ WITH DINE IN';

        $start = $this->input->get('date');
        if ($start) {
            $start = $start;

        } else {
            $start = date('Y-m-d');
        }

        $this->data['sales'] = $this->pos_model->getCONBBQAllSalesWithbillerreprint($sales_type_id, $start);

        $this->load->view($this->theme . 'pos/consolidatedbil_reprint', $this->data);
    }

    public function bbqitem_return()
    {

        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['sales_types'] = $this->site->getAllSalestype();
        $this->data['billers'] = $this->site->getAllCompanies('biller');

        $this->data['warehouse_id'] = $this->session->userdata('warehouse_id');
        $this->data['warehouse'] = $this->session->userdata('warehouse_id') ? $this->site->getWarehouseByID($this->session->userdata('warehouse_id')) : null;
        $this->data['sid'] = $this->input->get('suspend_id') ? $this->input->get('suspend_id') : $sid;

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        //$this->data['sales'] = $this->pos_model->getBBQAllBillingDatasreturn();
        $this->data['sales'] = $this->pos_model->getBBQReturn();

        $this->load->view($this->theme . 'pos/bbqitem_return', $this->data);
    }

    public function itemreturnBBQ()
    {
        $split_id = $this->input->post('split_id');
        $result = $this->pos_model->BBQsalesordersGET($split_id);
        $html = '<div class="col-lg-12"><table class="table table-bordered col-lg-12"><thead>';
        $html .= '<tr><th>Sale Item</th><th>Quantity</th><th>Total Piece</th><th>Return Piece</th><th>Return UOM</th></tr>';
        $html .= '</thead>';
        $html .= '<tbody>';

        /* echo '<pre>';
        print_r($result); */

        foreach ($result as $data) {
            //echo $data->recipe_name;
            $html .= '<tr><td>' . $data->recipe_name . '</td><td>' . $data->quantity . '</td><td>' . ($data->quantity * $data->piece) . '</td><td>
			<input type="number" name="return_piece[]" value="0"  min="0" max="' . ($data->quantity * $data->piece) . '" class="kb-pad form-control" required></td>
			<td><input type="text" name="return_uom[]" value="0"  min="0" max="' . ($data->quantity) . '" class="kb-pad form-control" required>
			<input type="hidden" name="order_id[]" value="' . $data->order_id . '">
			<input type="hidden" name="item_id[]" value="' . $data->item_id . '">
			<input type="hidden" name="recipe_id[]" value="' . $data->recipe_id . '">
			<input type="hidden" name="recipe_code[]" value="' . $data->recipe_code . '">
			<input type="hidden" name="recipe_name[]" value="' . $data->recipe_name . '">
			<input type="hidden" name="recipe_type[]" value="' . $data->recipe_type . '">
			<input type="hidden" name="total_piece[]" value="' . ($data->quantity * $data->piece) . '">
			<input type="hidden" name="quantity[]" value="' . $data->quantity . '">
			<input type="hidden" name="piece[]" value="' . $data->piece . '">

			</td></tr>';
        }
        $html .= '</tbody>';
        $html .= '</table>';
        $html .= '<input type="hidden" name="sale_id"  value="' . $result[0]->sale . '"> <input type="hidden" name="order"  value="' . $result[0]->order_id . '"> <input type="hidden" name="split_id"  value="' . $result[0]->split_id . '"> <input type="hidden" name="order_type"  value="' . $result[0]->order_type . '">';

        echo $html;
    }

    public function itemreturnBBQCode()
    {
        $split_id = $this->input->post('split_id');
        $result = $this->pos_model->BBQsalesordersGET($split_id);
        $html = '<div class="col-lg-12"><table class="table table-bordered col-lg-12"><thead>';
        $html .= '<tr><th>Sale Item</th><th>Quantity</th><th>Total Piece</th><th>Return Piece</th></tr>';
        $html .= '</thead>';
        $html .= '<tbody>';

        foreach ($result as $data) {
            //echo $data->recipe_name;
            $html .= '<tr><td>' . $data->recipe_name . '</td><td>' . $data->quantity . '</td><td>' . ($data->quantity * $data->piece) . '</td><td>
			<input type="number" name="return_piece[]" value="0"  min="0" max="' . ($data->quantity * $data->piece) . '" class="kb-pad form-control" required>
			<input type="hidden" name="order_id[]" value="' . $data->order_id . '">
			<input type="hidden" name="item_id[]" value="' . $data->item_id . '">
			<input type="hidden" name="recipe_id[]" value="' . $data->recipe_id . '">
			<input type="hidden" name="recipe_code[]" value="' . $data->recipe_code . '">
			<input type="hidden" name="recipe_name[]" value="' . $data->recipe_name . '">
			<input type="hidden" name="recipe_type[]" value="' . $data->recipe_type . '">
			<input type="hidden" name="total_piece[]" value="' . ($data->quantity * $data->piece) . '">

			</td></tr>';
        }
        $html .= '</tbody>';
        $html .= '</table>';
        $html .= '<input type="hidden" name="sale_id"  value="' . $result[0]->sale . '"> <input type="hidden" name="order"  value="' . $result[0]->order_id . '"> <input type="hidden" name="split_id"  value="' . $result[0]->split_id . '"> <input type="hidden" name="order_type"  value="' . $result[0]->order_type . '">';

        echo $html;
    }

    public function salereturnUpdate()
    {

        $this->form_validation->set_rules('split_id', $this->lang->line("split_id"), 'trim|required');

        if ($this->form_validation->run() == true) {

            $return_array = array(

                'order_id' => $this->input->post('order'),
                'split_id' => $this->input->post('split_id'),
                'order_type' => $this->input->post('order_type'),
                'created_at' => date('Y-m-d H:i:s'),
                'confirmed_by' => $this->session->userdata('user_id'),
            );

            for ($i = 0; $i < count($this->input->post('item_id')); $i++) {
                $returnitem_array[$i] = array(

                    'order_id' => $_POST['order_id'][$i],
                    'item_id' => $_POST['item_id'][$i],
                    'recipe_id' => $_POST['recipe_id'][$i],
                    'recipe_code' => $_POST['recipe_code'][$i],
                    'recipe_name' => $_POST['recipe_name'][$i],
                    'recipe_type' => $_POST['recipe_type'][$i],
                    'total_piece' => $_POST['total_piece'][$i],
                    'return_piece' => $_POST['return_piece'][$i],
                    'return_uom' => $_POST['return_uom'][$i],
                    'created_at' => date('Y-m-d H:i:s'),

                );

                $piece = $_POST['piece'][$i];
                $return_uom = $_POST['return_uom'][$i];
                $stock_piece = $return_uom * $piece;

                //echo 'quantity ';
                // $quantity = $_POST['quantity'][$i];
                //echo '<br />';

                //echo 'piece ';

                //echo '<br />';

                //echo 'return_piece ';
                // $return_piece = $_POST['return_piece'][$i];
                //echo '<br />';

                //echo 'return_uom ';
                //echo '<br />';

                /* echo 'remaining_piece ';
                //echo fmod($return_piece, $quantity);
                //    echo $remaining_piece = $return_piece % $piece; // 3 = 8 % 5
                echo '<br />';
                echo 'remaining_qty ';
                echo $remaining_qty =  $return_piece / $piece; // 1.6 = 8 / 5
                echo '<br />';
                echo $remaining_qty_int = (int)$remaining_qty;  // 1
                echo '<br />';
                echo $remaining_qty_round = round($remaining_qty,0); // 2

                echo $stock_out_piece = $quantity * $piece - $return_piece; */

                $query = 'update srampos_pro_stock_master set stock_in = stock_in + ' . $return_uom . ', stock_in_piece = stock_in_piece + ' . $stock_piece . ', stock_out = stock_out - ' . $return_uom . ', stock_out_piece = stock_out_piece - ' . $stock_piece . ' where product_id=' . $_POST['recipe_id'][$i];
                //    echo $query = 'update srampos_pro_stock_master set stock_in = stock_in + '.$remaining_qty_int.', stock_in_piece = stock_in_piece + '.$remaining_piece.', stock_out = stock_out - '.$remaining_qty_round.', stock_out_piece = stock_out_piece + '.$stock_out_piece.' where product_id='.$_POST['recipe_id'][$i];
                $this->db->query($query);

            }

            //die;

            /* echo '<pre>';print_R($return_array);
            echo '<pre>';print_R($returnitem_array);
            exit; */

            $response = $this->pos_model->salereturnUpdate($return_array, $returnitem_array);

            if ($response == true) {
                $update_notifi['split_id'] = $return_array['split_id'];
                $update_notifi['tag'] = 'bbq-return';
                $this->site->update_notification_status($update_notifi);
                admin_redirect("pos/bbqitem_return");
            } else {

                admin_redirect("pos/bbqitem_return");
            }

        } else {

            admin_redirect("pos/bbqitem_return");
        }

    }

    public function bbqgatdata_print_billing()
    {
        $billid = $this->input->get('billid');
        $this->data['order_item'] = $this->pos_model->getBBQAllBillitems($billid);
        $this->data['discount'] = $this->pos_model->BBQgetBillDiscountNames($billid);
        $inv = $this->pos_model->getBBQInvoiceByID($billid);
        $this->data['inv'] = $inv;
        $this->data['inv']->service_charge_display_value = '';
        if ($inv->service_charge_id != 0) {
            $ServiceCharge = $this->site->getServiceChargeByID($inv->service_charge_id);
            $this->data['inv']->service_charge_display_value = $ServiceCharge->name;
        }
        $tableno = $this->pos_model->getBBQTableNumber($billid);
        $this->data['tableno'] = $tableno;
        $this->data['billi_tems'] = $this->pos_model->getAllBillitems($billid);
        $biller_id = $inv->biller_id;
        $bill_id = $inv->sales_id;
        $customer_id = $inv->customer_id;
        $this->data['billi_tems'] = $this->pos_model->getBBQAllBillitems($billid);
        $this->data['DiscountCovers'] = $this->pos_model->getBBQBillDiscountCovers($billid);
        $this->data['customer'] = $this->pos_model->getCompanyByID($customer_id);
        $this->data['created_by'] = $this->site->getUser($inv->created_by);
        $this->data['cashier'] = $this->pos_model->getCashierInfo($billid);
        $this->data['printer'] = $this->pos_model->getPrinterByID($this->pos_settings->printer);
        $this->data['biller'] = $this->pos_model->getCompanyByID($biller_id);
        $this->data['payments'] = $this->pos_model->getBBQInvoicePayments($this->input->post('bill_id'));
        $this->data['type'] = $this->input->post('type');
        if (!empty($inv)) {
            $this->load->view($this->theme . 'pos/bbq_view_print_bill', $this->data);
        }

    }

    public function bbqcondata_print_billing()
    {
        $order_split_id = $this->input->get('ordersplit');
        $billid = $this->pos_model->getBilID($order_split_id);

        /*echo "<pre>";
        print_r($billid->);die*/
        foreach ($billid as $billid_row) {

            if ($billid_row->sales_type_id == 1) {

                $this->data['dine']['order_item'] = $this->pos_model->getAllBillitems($billid_row->id);
                $this->data['dine']['reference_no'] = $billid_row->reference_no;

                $dine_inv = $this->pos_model->getInvoiceByID($billid_row->id);
                $dine_tableno = $this->pos_model->getTableNumber($billid_row->id);

                /*$this->data['rows'] = $this->pos_model->getAllInvoiceItems($billid_row->id);*/
                $this->data['dine']['billi_tems'] = $this->pos_model->getAllBillitems($billid_row->id);
                $this->data['dine']['discounnames'] = $this->pos_model->getBillDiscountNames($billid_row->id);

                $dine_biller_id = $dine_inv->biller_id;
                $dine_bill_id = $dine_inv->sales_id;

                $dine_customer_id = $dine_inv->customer_id;
                $dine_delivery_person_id = $dine_inv->delivery_person_id;

                $this->data['dine']['inv'] = $dine_inv;
                $this->data['dine']['tableno'] = $dine_tableno;
                $this->data['dine']['customer'] = $this->pos_model->getCompanyByID($dine_customer_id);

                if ($dine_delivery_person_id != 0) {
                    $this->data['dine']['delivery_person'] = $this->pos_model->getUserByID($dine_delivery_person_id);
                }
                $this->data['dine']['created_by'] = $this->site->getUser($dine_inv->created_by);
                $this->data['dine']['cashier'] = $this->site->getUser($this->session->userdata('user_id'));
                $this->data['printer'] = $this->pos_model->getPrinterByID($this->pos_settings->printer);
                $this->data['biller'] = $this->pos_model->getCompanyByID($dine_biller_id);

                $this->data['dine']['payments'] = $this->pos_model->getInvoicePayments($billid_row->id);
                /*echo "<pre>";
                var_du($this->data['payments']);die;*/
                $this->data['dine']['return_sale'] = $dine_inv->return_id ? $this->pos_model->getInvoiceByID($dine_inv->return_id) : null;
                $this->data['dine']['return_rows'] = $dine_inv->return_id ? $this->pos_model->getAllInvoiceItems($dine_inv->return_id) : null;
                $this->data['dine']['return_payments'] = $this->data['return_sale'] ? $this->pos_model->getInvoicePayments($this->data['return_sale']->id) : null;

            } elseif ($billid_row->sales_type_id == 4) {

                /*#################################*/
                $this->data['bbq']['order_item'] = $this->pos_model->getBBQAllBillitems($billid_row->id);
                $this->data['bbq']['reference_no'] = $billid_row->reference_no;
                $bbq_inv = $this->pos_model->getBBQInvoiceByID($billid_row->id);
                $this->data['bbq']['inv'] = $bbq_inv;
                $bbq_tableno = $this->pos_model->getBBQTableNumber($billid_row->id);
                $this->data['bbq_tableno'] = $bbq_tableno;
                //$this->data['billi_tems'] = $this->pos_model->getAllBillitems($billid);
                $bbq_biller_id = $bbq_inv->biller_id;
                $bbq_bill_id = $bbq_inv->sales_id;
                $bbq_customer_id = $bbq_inv->customer_id;
                $this->data['bbq']['billi_tems'] = $this->pos_model->getBBQAllBillitems($billid_row->id);
                $this->data['bbq']['discount'] = $this->pos_model->BBQgetBillDiscountNames($billid_row->id);
                $this->data['bbq']['DiscountCovers'] = $this->pos_model->getBBQBillDiscountCovers($billid_row->id);
                $this->data['customer'] = $this->pos_model->getCompanyByID($bbq_customer_id);
                $this->data['bbq']['created_by'] = $this->site->getUser($bbq_inv->created_by);
                $this->data['printer'] = $this->pos_model->getPrinterByID($this->pos_settings->printer);
                $this->data['bbq']['biller'] = $this->pos_model->getCompanyByID($bbq_biller_id);

                $this->data['bbq']['payments'] = $this->pos_model->getBBQInvoicePayments($billid_row->id);

            }
        }

        if (!empty($dine_inv) && !empty($bbq_inv)) {

            $this->load->view($this->theme . 'pos/bbqconsolidated_view_print_bill', $this->data);
        }

    }

    public function bbqpaidgatdata_print_billing()
    {
        $billid = $this->input->get('billid');
        $this->data['order_item'] = $this->pos_model->getBBQAllBillitems($billid);
        $inv = $this->pos_model->getBBQInvoiceByID($billid);
        $this->data['inv'] = $inv;
        $tableno = $this->pos_model->getBBQTableNumber($billid);
        $this->data['tableno'] = $tableno;
        $this->data['billi_tems'] = $this->pos_model->getAllBillitems($billid);
        $biller_id = $inv->biller_id;
        $bill_id = $inv->sales_id;
        $customer_id = $inv->customer_id;
        $this->data['billi_tems'] = $this->pos_model->getBBQAllBillitems($billid);
        $this->data['customer'] = $this->pos_model->getCompanyByID($customer_id);
        $this->data['created_by'] = $this->site->getUser($inv->created_by);
        $this->data['cashier'] = $this->pos_model->getCashierInfo($billid);
        // var_dump($this->data['cashier']);die;
        $this->data['printer'] = $this->pos_model->getPrinterByID($this->pos_settings->printer);
        $this->data['biller'] = $this->pos_model->getCompanyByID($biller_id);
        $this->data['payments'] = $this->pos_model->getBBQInvoicePayments($this->input->post('bill_id'));
        $this->data['type'] = $this->input->post('type');

        if (!empty($inv)) {
            $this->load->view($this->theme . 'pos/bbq_paid_view_print_bill', $this->data);
        }

    }

    /*BBQ END*/

    public function notification()
    {
        $response = $this->site->notification_count($this->session->userdata('group_id'), $this->session->userdata('user_id'), $this->session->userdata('warehouse_id'));
        echo json_encode($response);
        exit;
    }

    public function request_bil()
    {

        $response = $this->site->request_count($this->session->userdata('group_id'), $this->session->userdata('user_id'), $this->session->userdata('warehouse_id'));
        echo json_encode($response);
        exit;
    }

    public function nitification_clear()
    {
        $notification_id = $this->input->post('notification_id');
        $response = $this->site->notification_clear($notification_id);
        echo json_encode($response);
        exit;
    }

    public function sales($warehouse_id = null)
    {
        $this->sma->checkPermissions('index');

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        if ($this->Owner) {
            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $this->data['warehouse_id'] = $warehouse_id;
            $this->data['warehouse'] = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : null;
        } else {
            $user = $this->site->getUser();
            $this->data['warehouses'] = null;
            $this->data['warehouse_id'] = $this->session->userdata('warehouse_id');
            $this->data['warehouse'] = $this->session->userdata('warehouse_id') ? $this->site->getWarehouseByID($this->session->userdata('warehouse_id')) : null;
        }

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('pos'), 'page' => lang('pos')), array('link' => '#', 'page' => lang('pos_sales')));
        $meta = array('page_title' => lang('pos_sales'), 'bc' => $bc);
        $this->page_construct('pos/sales', $meta, $this->data);
    }

    public function getSales($warehouse_id = null)
    {
        $this->sma->checkPermissions('index');

        if ((!$this->Owner || !$this->Admin) && !$warehouse_id) {
            $user = $this->site->getUser();
            $warehouse_id = $user->warehouse_id;
        }
        $duplicate_link = anchor('admin/pos/?duplicate=$1', '<i class="fa fa-plus-square"></i> ' . lang('duplicate_sale'), 'class="duplicate_pos"');
        $detail_link = anchor('admin/pos/view/$1', '<i class="fa fa-file-text-o"></i> ' . lang('view_receipt'));
        $detail_link2 = anchor('admin/sales/modal_view/$1', '<i class="fa fa-file-text-o"></i> ' . lang('sale_details_modal'), 'data-toggle="modal" data-target="#myModal"');
        $detail_link3 = anchor('admin/sales/view/$1', '<i class="fa fa-file-text-o"></i> ' . lang('sale_details'));
        $payments_link = anchor('admin/sales/payments/$1', '<i class="fa fa-money"></i> ' . lang('view_payments'), 'data-toggle="modal" data-target="#myModal"');
        $add_payment_link = anchor('admin/pos/add_payment/$1', '<i class="fa fa-money"></i> ' . lang('add_payment'), 'data-toggle="modal" data-target="#myModal"');
        $packagink_link = anchor('admin/sales/packaging/$1', '<i class="fa fa-archive"></i> ' . lang('packaging'), 'data-toggle="modal" data-target="#myModal"');
        $add_delivery_link = anchor('admin/sales/add_delivery/$1', '<i class="fa fa-truck"></i> ' . lang('add_delivery'), 'data-toggle="modal" data-target="#myModal"');
        $email_link = anchor('admin/#', '<i class="fa fa-envelope"></i> ' . lang('email_sale'), 'class="email_receipt" data-id="$1" data-email-address="$2"');
        $edit_link = anchor('admin/sales/edit/$1', '<i class="fa fa-edit"></i> ' . lang('edit_sale'), 'class="sledit"');
        $return_link = anchor('admin/sales/return_sale/$1', '<i class="fa fa-angle-double-left"></i> ' . lang('return_sale'));
        $delete_link = "<a href='#' class='po' title='<b>" . lang("delete_sale") . "</b>' data-content=\"<p>"
        . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('sales/delete/$1') . "'>"
        . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i> "
        . lang('delete_sale') . "</a>";
        $action = '<div class="text-center"><div class="btn-group text-left">'
        . '<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">'
        . lang('actions') . ' <span class="caret"></span></button>
            <ul class="dropdown-menu pull-right" role="menu">
                <li>' . $duplicate_link . '</li>
                <li>' . $detail_link . '</li>
                <li>' . $detail_link2 . '</li>
                <li>' . $detail_link3 . '</li>
                <li>' . $payments_link . '</li>
                <li>' . $add_payment_link . '</li>
                <li>' . $packagink_link . '</li>
                <li>' . $add_delivery_link . '</li>
                <li>' . $edit_link . '</li>
                <li>' . $email_link . '</li>
                <li>' . $return_link . '</li>
                <li>' . $delete_link . '</li>
            </ul>
        </div></div>';

        $this->load->library('datatables');
        if ($warehouse_id) {
            $this->datatables
                ->select($this->db->dbprefix('sales') . ".id as id, DATE_FORMAT(date, '%Y-%m-%d %T') as date, reference_no, biller, customer, (grand_total+COALESCE(rounding, 0)), paid, (grand_total-paid) as balance, sale_status, payment_status, companies.email as cemail")
                ->from('sales')
                ->join('companies', 'companies.id=sales.customer_id', 'left')
                ->where('warehouse_id', $warehouse_id)
                ->group_by('sales.id');
        } else {
            $this->datatables
                ->select($this->db->dbprefix('sales') . ".id as id, DATE_FORMAT(date, '%Y-%m-%d %T') as date, reference_no, biller, customer, (grand_total+COALESCE(rounding, 0)), paid, (grand_total+rounding-paid) as balance, sale_status, payment_status, companies.email as cemail")
                ->from('sales')
                ->join('companies', 'companies.id=sales.customer_id', 'left')
                ->group_by('sales.id');
        }
        $this->datatables->where('pos', 1);
        if (!$this->Customer && !$this->Supplier && !$this->Owner && !$this->Admin && !$this->session->userdata('view_right')) {
            $this->datatables->where('created_by', $this->session->userdata('user_id'));
        } elseif ($this->Customer) {
            $this->datatables->where('customer_id', $this->session->userdata('user_id'));
        }
        $this->datatables->add_column("Actions", $action, "id, cemail")->unset_column('cemail');
        echo $this->datatables->generate();
    }

    /* ---------------------------------------------------------------------------------------------------- */

    public function index($sid = null){
        $t = $this->sma->checkPermissions('index');
        $order = !empty($_GET['order']) ? $_GET['order'] : '';
        $table = !empty($_GET['table']) ? $_GET['table'] : '';
        $split = !empty($_GET['split']) ? $_GET['split'] : '';
        $same_customer = !empty($_GET['same_customer']) ? $_GET['same_customer'] : '';
        if (!$this->pos_settings->default_biller || !$this->pos_settings->default_customer || !$this->pos_settings->default_category) {
            $this->session->set_flashdata('warning', lang('please_update_settings'));
            admin_redirect('pos/settings');
        }
        $user_group = $this->pos_model->getUserByID($this->session->userdata('user_id'));
        $gp = $this->settings_model->getGroupPermissions($user_group->group_id);
        /* if(($this->pos_settings->open_sale_register == 1) && ( ($gp->{'pos-open_sale_register'} == 1)) ){

        $register = $this->pos_model->registerData($this->session->userdata('user_id'));
        $register_data = array('register_id' => $register->id, 'cash_in_hand' => $register->cash_in_hand, 'register_open_time' => $register->date);
        $this->session->set_userdata($register_data);

        if($register){
        $register_data = 'open';
        }
        else{
        $register_data = 'none';

        }
        }else{
        $register_data = 'disable';
        }

        $this->data['register_data'] = $register_data;*/

        /*if ($register = $this->pos_model->registerData($this->session->userdata('user_id'))) {
        $register_data = array('register_id' => $register->id, 'cash_in_hand' => $register->cash_in_hand, 'register_open_time' => $register->date);

        $this->session->set_userdata($register_data);
        } else {
        $this->session->set_flashdata('error', lang('register_not_open'));
        admin_redirect('pos/open_register');
        }*/

        $this->data['sid'] = $this->input->get('suspend_id') ? $this->input->get('suspend_id') : $sid;

        $did = $this->input->post('delete_id') ? $this->input->post('delete_id') : null;
        $suspend = $this->input->post('suspend') ? true : false;
        $count = $this->input->post('count') ? $this->input->post('count') : null;
        $duplicate_sale = $this->input->get('duplicate') ? $this->input->get('duplicate') : null;
        //validate form input
        $this->form_validation->set_rules('customer', $this->lang->line("customer"), 'trim|required');
        $this->form_validation->set_rules('warehouse', $this->lang->line("warehouse"), 'required');
        $this->form_validation->set_rules('biller', $this->lang->line("biller"), 'required');

        if (!empty($order)) {
            if ($order == 1 && !empty($table)) {
                $table_view = 'table';
            } elseif ($order == 2) {
                $table_view = 'pos';
            } elseif ($order == 3) {
                $table_view = 'pos';
            }

            if (isset($table_view) == 'pos') {
                if ($this->form_validation->run() == true) {
                    $date = date('Y-m-d H:i:s');
                    $warehouse_id = $this->input->post('warehouse');
                    $customer_id = $this->input->post('customer');
                    $biller_id = $this->input->post('biller');
                    $total_items = $this->input->post('total_items');
                    $payment_term = 0;
                    $due_date = date('Y-m-d', strtotime('+' . $payment_term . ' days'));
                    $shipping = $this->input->post('shipping') ? $this->input->post('shipping') : 0;
                    $customer_details = $this->site->getCompanyByID($customer_id);
                    $customer = $customer_details->company != '-' ? $customer_details->company : $customer_details->name;
                    $biller_details = $this->site->getCompanyByID($biller_id);
                    $biller = $biller_details->company != '-' ? $biller_details->company : $biller_details->name;
                    $note = $this->sma->clear_tags($this->input->post('pos_note'));
                    $staff_note = $this->sma->clear_tags($this->input->post('staff_note'));
                    $reference = $this->site->getReference('pos');

                    $total = 0;
                    $recipe_tax = 0;
                    $recipe_discount = 0;
                    $digital = false;
                    $gst_data = [];
                    $total_cgst = $total_sgst = $total_igst = 0;
                    $i = isset($_POST['recipe_code']) ? sizeof($_POST['recipe_code']) : 0;
                    for ($r = 0; $r < $i; $r++) {

                        $item_addon = isset($_POST['recipe_addon'][$r]) && $_POST['recipe_addon'][$r] != 'false' ? $_POST['recipe_addon'][$r] : null;

                        $item_id = $_POST['recipe_id'][$r];
                        $item_type = $_POST['recipe_type'][$r];
                        $item_code = $_POST['recipe_code'][$r];

                        $buy_id = $_POST['buy_id'][$r];
                        $buy_quantity = $_POST['buy_quantity'][$r];
                        $get_item = $_POST['get_item'][$r];
                        $get_quantity = $_POST['get_quantity'][$r];
                        $total_get_quantity = $_POST['total_get_quantity'][$r];

                        $item_name = $_POST['recipe_name'][$r];
                        $item_comment = $_POST['recipe_comment'][$r];
                        $item_option = isset($_POST['recipe_option'][$r]) && $_POST['recipe_option'][$r] != 'false' ? $_POST['recipe_option'][$r] : null;
                        $real_unit_price = $this->sma->formatDecimal($_POST['real_unit_price'][$r]);
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
                            $pr_discount = $this->site->calculateDiscount($item_discount, $unit_price);
                            $unit_price = $this->sma->formatDecimal($unit_price - $pr_discount);
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

                            $recipe = array(
                                'recipe_id' => $item_id,
                                'recipe_code' => $item_code,
                                'recipe_name' => $item_name,
                                'recipe_type' => $item_type,
                                'option_id' => $item_option,
                                'addon_id' => $item_addon,
                                'buy_id' => $buy_id,
                                'buy_quantity' => $buy_quantity,
                                'get_item' => $get_item,
                                'get_quantity' => $get_quantity,
                                'total_get_quantity' => $total_get_quantity,
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
                                'subtotal' => $this->sma->formatDecimal($subtotal),
                                'serial_no' => $item_serial,
                                'real_unit_price' => $real_unit_price,
                                'comment' => $item_comment,
                            );

                            $recipe[] = ($recipe + $gst_data);
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
                    $data = array('date' => $date,
                        'reference_no' => $reference,
                        'customer_id' => $customer_id,
                        'customer' => $customer,
                        'biller_id' => $biller_id,
                        'biller' => $biller,
                        'warehouse_id' => $warehouse_id,
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
                        'sale_status' => 'Process',
                        'payment_status' => $payment_status,
                        'payment_term' => $payment_term,
                        'rounding' => $rounding,
                        'suspend_note' => $this->input->post('suspend_note'),
                        'pos' => 1,
                        'paid' => $this->input->post('amount-paid') ? $this->input->post('amount-paid') : 0,
                        'created_by' => $this->session->userdata('user_id'),
                        'hash' => hash('sha256', microtime() . mt_rand()),
                    );
                    if ($this->Settings->indian_gst) {
                        $data['cgst'] = $total_cgst;
                        $data['sgst'] = $total_sgst;
                        $data['igst'] = $total_igst;
                    }

                    if (!$suspend) {
                        $p = isset($_POST['amount']) ? sizeof($_POST['amount']) : 0;
                        $paid = 0;
                        for ($r = 0; $r < $p; $r++) {
                            if (isset($_POST['amount'][$r]) && !empty($_POST['amount'][$r]) && isset($_POST['paid_by'][$r]) && !empty($_POST['paid_by'][$r])) {
                                $amount = $this->sma->formatDecimal($_POST['balance_amount'][$r] > 0 ? $_POST['amount'][$r] - $_POST['balance_amount'][$r] : $_POST['amount'][$r]);
                                if ($_POST['paid_by'][$r] == 'deposit') {
                                    if (!$this->site->check_customer_deposit($customer_id, $amount)) {
                                        $this->session->set_flashdata('error', lang("amount_greater_than_deposit"));
                                        redirect($_SERVER["HTTP_REFERER"]);
                                    }
                                }
                                if ($_POST['paid_by'][$r] == 'gift_card') {
                                    $gc = $this->site->getGiftCardByNO($_POST['paying_gift_card_no'][$r]);
                                    $amount_paying = $_POST['amount'][$r] >= $gc->balance ? $gc->balance : $_POST['amount'][$r];
                                    $gc_balance = $gc->balance - $amount_paying;
                                    $payment[] = array(
                                        'date' => $date,
                                        // 'reference_no' => $this->site->getReference('pay'),
                                        'amount' => $amount,
                                        'paid_by' => $_POST['paid_by'][$r],
                                        'cheque_no' => $_POST['cheque_no'][$r],
                                        'cc_no' => $_POST['paying_gift_card_no'][$r],
                                        'cc_holder' => $_POST['cc_holder'][$r],
                                        'cc_month' => $_POST['cc_month'][$r],
                                        'cc_year' => $_POST['cc_year'][$r],
                                        'cc_type' => $_POST['cc_type'][$r],
                                        'cc_cvv2' => $_POST['cc_cvv2'][$r],
                                        'created_by' => $this->session->userdata('user_id'),
                                        'type' => 'received',
                                        'note' => $_POST['payment_note'][$r],
                                        'pos_paid' => $_POST['amount'][$r],
                                        'pos_balance' => $_POST['balance_amount'][$r],
                                        'gc_balance' => $gc_balance,
                                    );

                                } else {
                                    $payment[] = array(
                                        'date' => $date,
                                        // 'reference_no' => $this->site->getReference('pay'),
                                        'amount' => $amount,
                                        'paid_by' => $_POST['paid_by'][$r],
                                        'cheque_no' => $_POST['cheque_no'][$r],
                                        'cc_no' => $_POST['cc_no'][$r],
                                        'cc_holder' => $_POST['cc_holder'][$r],
                                        'cc_month' => $_POST['cc_month'][$r],
                                        'cc_year' => $_POST['cc_year'][$r],
                                        'cc_type' => $_POST['cc_type'][$r],
                                        'cc_cvv2' => $_POST['cc_cvv2'][$r],
                                        'created_by' => $this->session->userdata('user_id'),
                                        'type' => 'received',
                                        'note' => $_POST['payment_note'][$r],
                                        'pos_paid' => $_POST['amount'][$r],
                                        'pos_balance' => $_POST['balance_amount'][$r],
                                    );

                                }

                            }
                        }
                    }
                    if (!isset($payment) || empty($payment)) {
                        $payment = array();
                    }

                    // $this->sma->print_arrays($data, $recipe, $payment);
                }

                if ($this->form_validation->run() == true && !empty($recipe) && !empty($data)) {
                    if ($suspend) {
                        if ($this->pos_model->suspendSale($data, $recipe, $did)) {
                            $this->session->set_userdata('remove_posls', 1);
                            $this->session->set_flashdata('message', $this->lang->line("sale_suspended"));
                            admin_redirect("pos");
                        }
                    } else {
                        if ($sale = $this->pos_model->addSale($data, $recipe, $payment, $did)) {
                            $this->session->set_userdata('remove_posls', 1);
                            $msg = $this->lang->line("sale_added");
                            if (!empty($sale['message'])) {
                                foreach ($sale['message'] as $m) {
                                    $msg .= '<br>' . $m;
                                }
                            }
                            $this->session->set_flashdata('message', $msg);
                            $redirect_to = $this->pos_settings->after_sale_page ? "pos" : "pos/view/" . $sale['sale_id'];
                            if ($this->pos_settings->auto_print) {
                                if ($this->Settings->remote_printing != 1) {
                                    $redirect_to .= '?print=' . $sale['sale_id'];
                                }
                            }
                            admin_redirect($redirect_to);
                        }
                    }
                } else {
                    $this->data['old_sale'] = null;
                    $this->data['oid'] = null;
                    if ($duplicate_sale) {
                        if ($old_sale = $this->pos_model->getInvoiceByID($duplicate_sale)) {
                            $inv_items = $this->pos_model->getSaleItems($duplicate_sale);
                            $this->data['oid'] = $duplicate_sale;
                            $this->data['old_sale'] = $old_sale;
                            $this->data['message'] = lang('old_sale_loaded');
                            $this->data['customer'] = $this->pos_model->getCompanyByID($old_sale->customer_id);
                        } else {
                            $this->session->set_flashdata('error', lang("bill_x_found"));
                            admin_redirect("pos");
                        }
                    }
                    $this->data['suspend_sale'] = null;
                    if ($sid) {
                        if ($suspended_sale = $this->pos_model->getOpenBillByID($sid)) {
                            $inv_items = $this->pos_model->getSuspendedSaleItems($sid);
                            $this->data['sid'] = $sid;
                            $this->data['suspend_sale'] = $suspended_sale;
                            $this->data['message'] = lang('suspended_sale_loaded');
                            $this->data['customer'] = $this->pos_model->getCompanyByID($suspended_sale->customer_id);
                            $this->data['reference_note'] = $suspended_sale->suspend_note;
                        } else {
                            $this->session->set_flashdata('error', lang("bill_x_found"));
                            admin_redirect("pos");
                        }
                    }

                    if (($sid || $duplicate_sale) && $inv_items) {
                        // krsort($inv_items);
                        $c = rand(100000, 9999999);
                        foreach ($inv_items as $item) {
                            $row = $this->site->getrecipeByID($item->recipe_id);

                            $buy = $this->site->checkBuyget($row->id);
                            if (!empty($buy)) {
                                $row->buy_id = $buy->id;
                                $row->buy_quantity = $buy->buy_quantity;
                                $row->get_item = $buy->get_item;
                                $row->get_quantity = $buy->get_quantity;
                                $row->total_get_quantity = $buy->get_quantity;
                                $total_quantity = $x_quantity % $y_quantity;
                                $x_quantity = ($x_quantity - $total_quantity) / $y_quantity;
                                $total_get_quantity = $x_quantity * $b_quantity;
                                $row->total_get_quantity = $total_get_quantity;

                                $row->free_recipe = $buy->free_recipe;
                            } else {
                                $row->buy_id = 0;
                                $row->get_item = 0;
                                $row->buy_quantity = 0;
                                $row->get_quantity = 0;
                                $row->total_get_quantity = 0;
                                $row->free_recipe = '';
                            }

                            if (!$row) {
                                $row = json_decode('{}');
                                $row->tax_method = 0;
                                $row->quantity = 0;
                            } else {
                                $category = $this->site->getCategoryByID($row->category_id);
                                $row->category_name = $category->name;
                                unset($row->cost, $row->details, $row->recipe_details, $row->image, $row->barcode_symbology, $row->cf1, $row->cf2, $row->cf3, $row->cf4, $row->cf5, $row->cf6, $row->supplier1price, $row->supplier2price, $row->cfsupplier3price, $row->supplier4price, $row->supplier5price, $row->supplier1, $row->supplier2, $row->supplier3, $row->supplier4, $row->supplier5, $row->supplier1_part_no, $row->supplier2_part_no, $row->supplier3_part_no, $row->supplier4_part_no, $row->supplier5_part_no);
                            }
                            $pis = $this->site->getPurchasedItems($item->recipe_id, $item->warehouse_id, $item->option_id);
                            if ($pis) {
                                foreach ($pis as $pi) {
                                    $row->quantity += $pi->quantity_balance;
                                }
                            }
                            $row->id = $item->recipe_id;
                            $row->code = $item->recipe_code;
                            $row->name = $item->recipe_name;
                            $row->type = $item->recipe_type;
                            $row->quantity += $item->quantity;
                            $row->discount = $item->discount ? $item->discount : '0';
                            $row->price = $this->sma->formatDecimal($item->net_unit_price + $this->sma->formatDecimal($item->item_discount / $item->quantity));
                            $row->unit_price = $row->tax_method ? $item->unit_price + $this->sma->formatDecimal($item->item_discount / $item->quantity) + $this->sma->formatDecimal($item->item_tax / $item->quantity) : $item->unit_price + ($item->item_discount / $item->quantity);
                            $row->real_unit_price = $item->real_unit_price;
                            $row->base_quantity = $item->quantity;
                            $row->base_unit = isset($row->unit) ? $row->unit : $item->recipe_unit_id;
                            $row->base_unit_price = $row->price ? $row->price : $item->unit_price;
                            $row->unit = $item->recipe_unit_id;
                            $row->qty = $item->unit_quantity;
                            $row->tax_rate = $item->tax_rate_id;
                            $row->serial = $item->serial_no;
                            $row->option = $item->option_id;
                            $row->addon = $item->addon_id;
                            $options = $this->pos_model->getrecipeOptions($row->id, $item->warehouse_id);
                            $addons = $this->pos_model->getrecipeAddons($row->id);

                            if ($options) {
                                $option_quantity = 0;
                                foreach ($options as $option) {
                                    $pis = $this->site->getPurchasedItems($row->id, $item->warehouse_id, $item->option_id);
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

                            $row->comment = isset($item->comment) ? $item->comment : '';
                            $row->ordered = 1;
                            $combo_items = false;
                            if ($row->type == 'combo') {
                                $combo_items = $this->pos_model->getrecipeComboItems($row->id, $item->warehouse_id);
                            }
                            $units = $this->site->getUnitsByBUID($row->base_unit);
                            $tax_rate = $this->site->getTaxRateByID($row->tax_rate);
                            $ri = $this->Settings->item_addition ? $row->id : $c;

                            $pr[$ri] = array('id' => $c, 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")",
                                'row' => $row, 'combo_items' => $combo_items, 'tax_rate' => $tax_rate, 'units' => $units, 'options' => $options, 'addons' => $addons);
                            $c++;
                        }

                        $this->data['items'] = json_encode($pr);

                    } else {
                        $this->data['customer'] = $this->pos_model->getCompanyByID($this->pos_settings->default_customer);
                        $this->data['reference_note'] = null;
                    }

                    $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
                    $this->data['message'] = isset($this->data['message']) ? $this->data['message'] : $this->session->flashdata('message');

                    // $this->data['biller'] = $this->site->getCompanyByID($this->pos_settings->default_biller);
                    $this->data['billers'] = $this->site->getAllCompanies('biller');
                    $this->data['sales_types'] = $this->site->getAllSalestype();
                    $this->data['warehouses'] = $this->site->getAllWarehouses();
                    $this->data['tax_rates'] = $this->site->getAllTaxRates();
                    $this->data['user'] = $this->site->getUser();
                    $this->data["tcp"] = $this->pos_model->recipe_count($this->pos_settings->default_category, $this->session->userdata('warehouse_id'));
                    $this->data["sub_cat"] = $this->site->getrecipeSubCategories($this->pos_settings->default_category);
                    $this->data['recipe'] = $this->ajaxrecipe($this->pos_settings->default_category, $this->session->userdata('warehouse_id'), $this->data["sub_cat"][0]->id, $brand_id = null, $order);
                    if ($this->pos_settings->sales_item_in_pos == 1) {
                        $this->data['categories'] = $this->site->getAllrecipeCategories();
                    } else { //by day wise item mappings
                        $this->data['categories'] = $this->site->getAllrecipeCategories_withdays($order);
                    }
                    $this->data['brands'] = $this->site->getAllBrands();
                    // sub category list from recipe table with active items in recipe table
                    if ($this->pos_settings->sales_item_in_pos == 1) {
                        $this->data['subcategories'] = $this->site->getrecipeSubCategories($this->pos_settings->default_category);
                    } else { // sub category list from mapping table with active items in recipe table
                        $this->data['subcategories'] = $this->site->getrecipeSubCategories_withdays($this->pos_settings->default_category, $order);
                    }

                    $this->data['printer'] = $this->pos_model->getPrinterByID($this->pos_settings->printer);
                    $order_printers = json_decode($this->pos_settings->order_printers);
                    $printers = array();
                    if (!empty($order_printers)) {
                        foreach ($order_printers as $printer_id) {
                            $printers[] = $this->pos_model->getPrinterByID($printer_id);
                        }
                    }
                    $this->data['order_printers'] = $printers;
                    $this->data['pos_settings'] = $this->pos_settings;

                    $this->data['areas'] = $this->pos_model->getTablelist($this->session->userdata('warehouse_id'));

                    $this->data['get_table'] = $table;
                    $this->data['get_order_type'] = $order;
                    $this->data['get_split'] = $split;
                    $this->data['same_customer'] = $same_customer;

                    if ($this->pos_settings->after_sale_page && $saleid = $this->input->get('print', true)) {
                        if ($inv = $this->pos_model->getInvoiceByID($saleid)) {
                            $this->load->helper('pos');
                            if (!$this->session->userdata('view_right')) {
                                $this->sma->view_rights($inv->created_by, true);
                            }
                            $this->data['rows'] = $this->pos_model->getAllInvoiceItems($inv->id);
                            $this->data['biller'] = $this->pos_model->getCompanyByID($inv->biller_id);
                            $this->data['customer'] = $this->pos_model->getCompanyByID($inv->customer_id);
                            $this->data['payments'] = $this->pos_model->getInvoicePayments($inv->id);
                            $this->data['return_sale'] = $inv->return_id ? $this->pos_model->getInvoiceByID($inv->return_id) : null;
                            $this->data['return_rows'] = $inv->return_id ? $this->pos_model->getAllInvoiceItems($inv->return_id) : null;
                            $this->data['return_payments'] = $this->data['return_sale'] ? $this->pos_model->getInvoicePayments($this->data['return_sale']->id) : null;
                            $this->data['inv'] = $inv;
                            $this->data['print'] = $inv->id;

                            $this->data['created_by'] = $this->site->getUser($inv->created_by);
                        }
                    }
                    /*echo "<pre>";
                    print_r($this->data);
                    die;*/
                    if ($this->pos_settings->variant_display_option == 0) {
                        $this->load->view($this->theme . 'pos/add', $this->data);
                    } else {
                        $this->load->view($this->theme . 'pos/kimmo/add', $this->data);
                    }
                }

            } else {

                $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
                $this->data['message'] = isset($this->data['message']) ? $this->data['message'] : $this->session->flashdata('message');

                // $this->data['biller'] = $this->site->getCompanyByID($this->pos_settings->default_biller);
                $this->data['billers'] = $this->site->getAllCompanies('biller');
                $this->data['sales_types'] = $this->site->getAllSalestype();
                $this->data['tables'] = $this->site->getAllTables();
                $this->data['warehouses'] = $this->site->getAllWarehouses();
                $this->data['tax_rates'] = $this->site->getAllTaxRates();
                $this->data['user'] = $this->site->getUser();
                $this->data["tcp"] = $this->pos_model->recipe_count($this->pos_settings->default_category, $this->session->userdata('warehouse_id'));
                $this->data['recipe'] = $this->ajaxrecipe($this->pos_settings->default_category, $this->session->userdata('warehouse_id'));
                if ($this->pos_settings->sales_item_in_pos == 1) {
                    $this->data['categories'] = $this->site->getAllrecipeCategories();
                } else { //by day wise item mappings
                    $this->data['categories'] = $this->site->getAllrecipeCategories_withdays($order);
                }

                $this->data['brands'] = $this->site->getAllBrands();
                $this->data['subcategories'] = $this->site->getrecipeSubCategories($this->pos_settings->default_category);

                $this->data['pos_settings'] = $this->pos_settings;

                $this->data['order_type'] = $order;
                $this->load->view($this->theme . 'pos/tables', $this->data);

            }

        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['message'] = isset($this->data['message']) ? $this->data['message'] : $this->session->flashdata('message');

            // $this->data['biller'] = $this->site->getCompanyByID($this->pos_settings->default_biller);
            $this->data['billers'] = $this->site->getAllCompanies('biller');
            $this->data['sales_types'] = $this->site->getAllSalestype();
            $this->data['tables'] = $this->site->getAllTables();
            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $this->data['tax_rates'] = $this->site->getAllTaxRates();
            $this->data['user'] = $this->site->getUser();
            $this->data["tcp"] = $this->pos_model->recipe_count($this->pos_settings->default_category, $this->session->userdata('warehouse_id'));
            $this->data['recipe'] = $this->ajaxrecipe($this->pos_settings->default_category, $this->session->userdata('warehouse_id'));
            $this->data['categories'] = $this->site->getAllrecipeCategories_withdays($order);
            $this->data['brands'] = $this->site->getAllBrands();
            $this->data['subcategories'] = $this->site->getrecipeSubCategories($this->pos_settings->default_category);
            $this->data['bbq_category'] = $this->pos_model->getAllbbqCategories();
            $this->data['pos_settings'] = $this->pos_settings;
            $this->data['group'] = $this->session->userdata('group_id');
            if ($this->pos_settings->pos_types_display_option == 0) {
                $this->load->view($this->theme . 'pos/pos_type', $this->data);
            } else {
                $this->load->view($this->theme . 'pos/pos_type_without_icon', $this->data);
            }

        }

    }

    public function ajax_tables(){

        $this->data['areas'] = $this->pos_model->getTablelist($this->session->userdata('warehouse_id'));
        if ($this->pos_settings->table_display_option == 0) {
            $this->load->view($this->theme . 'pos/tables_ajax', $this->data);
        } else {
            $this->load->view($this->theme . 'pos/tables_ajax_without_icon', $this->data);
        }
    }
    public function ajax_table_byID()
    {
        $id = $this->input->post('id');
        $this->data['areas'] = $this->pos_model->getTable_byID($id, $this->session->userdata('warehouse_id'));
        $this->load->view($this->theme . 'pos/tables_single_ajax', $this->data);
    }

    public function sent_to_kitchen($sid = null){
        /*echo "<pre>";
        print_r($_POST);exit;    */
        $this->form_validation->set_rules('customer', $this->lang->line("customer"), 'trim|required');
        $this->form_validation->set_rules('warehouse', $this->lang->line("warehouse"), 'required');
        $this->form_validation->set_rules('biller', $this->lang->line("biller"), 'required');

        if ($this->form_validation->run() == true) {
            /*echo "<pre>";
            print_r($this->input->post());die; */

            $date = date('Y-m-d H:i:s');
            $warehouse_id = $this->input->post('warehouse');
            $customer_id = $this->input->post('customer');
            $biller_id = $this->input->post('biller');
            $total_items = $this->input->post('total_items');

            $payment_term = 0;
            $due_date = date('Y-m-d', strtotime('+' . $payment_term . ' days'));
            $shipping = $this->input->post('shipping') ? $this->input->post('shipping') : 0;
            $customer_details = $this->site->getCompanyByID($customer_id);
            $customer = $customer_details->name;
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
                    $total += $this->sma->formatDecimal(($item_net_price * $item_unit_quantity), 4);
                }
            }
            /*echo "<pre>";
            print_r($recipe);die;   */

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
                'seats_id' => !empty($this->input->post('table_list_id')) ? $this->input->post('seats_id') : 0,
                'split_id' => $split_id,
                'order_type' => $this->input->post('order_type_id'),
                'order_status' => 'Open',
                'customer_id' => $customer_id,
                'customer' => $customer,
                'biller_id' => $biller_id,
                'biller' => $biller,
                'warehouse_id' => $warehouse_id,
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

                admin_redirect("pos?msg=special_item");
            } else {
                admin_redirect("pos");
            }
        }
        /*echo "<pre>";
        print_r($recipe);
        die;*/
        if ($this->form_validation->run() == true && !empty($recipe) && !empty($data) && !empty($kitchen)) {

            if ($sale = $this->pos_model->addKitchen($data, $recipe, $kitchen, $notification_array, $this->session->userdata('warehouse_id'), $this->session->userdata('user_id'))) {

                $kot_print_data['kot_print_option'] = $this->pos_settings->kot_print_option;
                $kot_print_data['con_kot_print_option'] = $this->pos_settings->consolidated_kot_print;
                $kot_print_data['kot_area_print'] = $sale['kitchen_data'];

                if ($this->pos_settings->consolidated_kot_print != 0) {
                    $kot_print_data['kot_con_print'] = $sale['consolid_kitchen_data'];
                    $kot_print_data['consolidate_kitchens_kot'] = $sale['consolidate_kitchens_kot'];
                }
                /*echo "<pre>";
                print_r($kot_print_data);*/

                $kot_print_lang_option = $this->pos_settings->kot_print_lang_option;
                //print_r($kot_print_lang_option);
                // var_dump($this->pos_settings->kot_enable_disable);die;
                if ($this->pos_settings->kot_enable_disable == 1) {
                    $this->send_to_kot_print($kot_print_data);
                }
                // die;
                //if($this->pos_settings->kot_print_option == 1){
                //    $this->remotePrintingKOT_single($sale['kitchen_data']);
                //}else{
                //    $this->remotePrintingKOT($sale['kitchen_data']);
                //}
                //if($this->pos_settings->consolidated_kot_print != 0){
                //
                //
                //    $kotconsoildprint = $sale['consolid_kitchen_data'];
                //    $this->kot_consolidated_curl($kotconsoildprint);

                //if(!empty($kotconsoildprint['consolid_kot_print_details'])){
                //
                //
                //    foreach($kotconsoildprint['consolid_kot_print_details'] as $order_data){
                //
                //
                //        if(!empty($kotconsoildprint['consolid_kot_print_details']) && !empty($kotconsoildprint['consolid_kitchens'])){
                //
                //            $this->remotePrintingCONSOLIDKOT($sale['consolid_kitchen_data']);
                //        }
                //    }
                //}

                //}

                $this->session->set_userdata('remove_posls', 1);
                $msg = $this->lang->line("sale_added");
                if (!empty($sale['message'])) {
                    foreach ($sale['message'] as $m) {
                        $msg .= '<br>' . $m;
                    }
                }
                $this->session->set_flashdata('message', $msg);
                $tableid = $this->input->post('table_list_id');
                if ($_POST['order_type_id'] == 1 && substr($_POST['split_id'], 0, 3) !== "BBQ") {
                    admin_redirect("pos?tid=" . $tableid);
                } else if ($_POST['order_type_id'] == 4) {
                    admin_redirect("pos?bbqtid=" . $tableid);
                } else {
                    admin_redirect("pos");
                }

            }
        } else {
            admin_redirect("pos");
        }

    }

    public function kot_consolidated_curl($kotconsoildprint)
    {
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

    public function kot_print_copy($split_id, $kitchen_id = false)
    {
        $sale = $this->pos_model->kot_print_copy($split_id, $kitchen_id);
        $kot_print_data['kot_print_option'] = $this->pos_settings->kot_print_option;
        $kot_print_data['con_kot_print_option'] = $this->pos_settings->consolidated_kot_print;
        $kot_print_data['kot_area_print'] = $sale['kitchen_data'];
        $kot_print_data['kot_con_print'] = $sale['consolid_kitchen_data'];
        /*echo "<pre>";
        print_r($sale);die;*/
        if ($this->pos_settings->kot_enable_disable == 1) {
            $this->send_to_kot_print($kot_print_data);
        }
    }
    public function kitchen_kot_print_copy($order_id, $kitchen_id)
    {
        $orderItemIDs = $this->input->post('order_item_ids');
        $sale = $this->pos_model->kitchen_kot_print_copy($order_id, $orderItemIDs, $kitchen_id);
        $kot_print_data['kot_print_option'] = $this->pos_settings->kot_print_option;
        $kot_print_data['con_kot_print_option'] = 0;
        $kot_print_data['kot_area_print'] = $sale['kitchen_data'];
        $this->send_to_kot_print($kot_print_data);

    }
    public function send_to_kot_print($kot_print_data)
    {
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

    public function applySpecialItem($data, $recipe, $kitchen, $warehouse_id, $user_id)
    {

        if (!empty($data)) {
            $order_data = $data;
            $order_data['total'] = 0;
            $order_data['recipe_discount'] = 0;
            $order_data['order_discount_id'] = '';
            $order_data['order_discount'] = 0;
            $order_data['total_discount'] = 0;
            $order_data['recipe_tax'] = 0;
            $order_data['order_tax_id'] = 0;
            $order_data['order_tax'] = 0;
            $order_data['total_tax'] = 0;
            $order_data['shipping'] = 0;
            $order_data['grand_total'] = 0;
            $order_data['total_items'] = 0;
            $order_data['payment_term'] = 0;
            $order_data['rounding'] = 0;
            $order_data['order_status'] = 'Closed';
            $order_data['sale_status'] = 'Closed';
            $order_data['payment_status'] = 'Paid';
            $order_data['special_order'] = 1;
        }

        if (!empty($recipe)) {
            $order_item = $recipe;
            $i = 0;
            foreach ($recipe as $key => $value) {
                $order_item[$i]['unit_price'] = 0;
                $order_item[$i]['net_unit_price'] = 0;
                $order_item[$i]['quantity'] = 0;
                $order_item[$i]['unit_quantity'] = 0;
                $order_item[$i]['item_tax'] = 0;
                $order_item[$i]['tax_rate_id'] = '';
                $order_item[$i]['tax'] = 0;
                $order_item[$i]['discount'] = 0;
                $order_item[$i]['item_discount'] = 0;
                $order_item[$i]['subtotal'] = 0;
                $order_item[$i]['real_unit_price'] = 0;
                $order_item[$i]['item_status'] = 'Closed';
                $order_item[$i]['special_order'] = 1;

                $i++;
            }

        }

        $result = $this->pos_model->addKitchen_Special($order_data, $order_item, $kitchen, $warehouse_id, $user_id);

        if (!empty($result)) {
            $order_type = $result['order_type'];
            $bill_type = $result['bill_type'];
            $table_id = $result['table'];
            $split_id = $result['splits'];
            $bils = $result['bils'];

            $item_data = $this->pos_model->getBil_Special($table_id, $split_id, $user_id);

            foreach ($item_data['order'] as $order) {

                $sale_data = array(
                    'sales_type_id' => $order->order_type,
                    'sales_split_id' => $order->split_id,
                    'sales_table_id' => $order->table_id,
                    'date' => $this->site->getTransactionDate(),
                    'created_on' => date('Y-m-d H:i:s'),
                    'reference_no' => 'SALES-' . date('YmdHis'),
                    'customer_id' => $order->customer_id,
                    'customer' => $order->customer,
                    'biller_id' => $order->biller_id,
                    'biller' => $order->biller,
                    'warehouse_id' => $warehouse_id,
                    'note' => '',
                    'staff_note' => '',
                    'sale_status' => 'Closed',
                    'payment_status' => 'Paid',
                    'hash' => hash('sha256', microtime() . mt_rand()),
                    'special_order' => 1,
                    'total' => 0,

                );

                $bil_data = array(

                    'order_type' => $order->order_type,
                    'date' => $this->site->getTransactionDate(),
                    'created_on' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'reference_no' => 'SALES-' . date('YmdHis'),
                    'customer_id' => $order->customer_id,
                    'customer' => $order->customer,
                    'biller_id' => $order->biller_id,
                    'biller' => $order->biller,
                    'warehouse_id' => $warehouse_id,
                    'note' => '',
                    'staff_note' => '',
                    'payment_status' => 'Completed',
                    'created_by' => $user_id,
                    'paid_by' => $user_id,
                    'special_order' => 1,
                    'total' => 0,

                );

            }

            foreach ($item_data['items'] as $item) {
                $item->special_order = 1;
                unset($item->id);
                $sale_item[] = $item;
                $bil_item[] = $item;
            }
            $response = $this->pos_model->SpecialSaleandBils($sale_data, $sale_item, $bil_data, $bil_item);
            if ($response) {
                return 1;
            } else {
                return 0;
            }
        } else {
            return 0;
        }

    }

    public function remotePrintingKOT_new($kitchen_data = array())
    {
        if (!empty($kitchen_data)) {

            $this->data['user'] = $this->site->getUser($kitchen_data['orders_details']->created_by);
            $this->data['biller'] = $this->site->getCompanyOrderByID($kitchen_data['orders_details']->biller_id);
            if ($kitchen_data['orders_details']->order_type == 1) {
                $this->data['store_name'] = "Table : #" . $kitchen_data['orders_details']->table_name;
            } elseif ($kitchen_data['orders_details']->order_type == 2) {
                $this->data['store_name'] = "Takeaway : #" . $kitchen_data['orders_details']->reference_no;
            } else {
                $this->data['store_name'] = "Delivery : #" . $kitchen_data['orders_details']->reference_no;
            }

            $this->data['reference_no'] = $kitchen_data['orders_details']->reference_no;
            $this->data['orders_date'] = $kitchen_data['orders_details']->date;
            $this->data['ordered_by'] = $ordered_by;

            $this->data['kitchens'] = $kitchen_data['kitchens'];

            if (!empty($kitchen_data['kitchens'])) {

                foreach ($kitchen_data['kitchens'] as $order_data) {

                    $this->data['orders'] = $this->pos_model->getorderKitchenprint($order_data->id, $kitchen_data['orders_details']->id);
                    $this->data['kitchen_value'] = !empty($order_data->id) ? $order_data->id : 1;
                    $this->data['reskitchen'] = $this->site->getAllResKitchen();

                    $html = $this->load->view($this->theme . 'pos/orderkitchenprint', $this->data);
                    echo $html;
                }
            }

        }
    }

    public function remotePrintingKOT_single($kitchen_data = array())
    {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, site_url('kot_print/single_item'));
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        $kitchendata = json_encode(array('k_data' => $kitchen_data));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $kitchendata);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        $result = curl_exec($ch);
        curl_close($ch);

        //if(!empty($kitchen_data)){
        //    $ordered_by = 'N/A';
        //    $user = $this->site->getUser($kitchen_data['orders_details']->created_by);
        //    if($user){
        //    $ordered_by = $user->first_name.' '.$user->last_name;
        //    }
        //    $biller = $this->site->getCompanyOrderByID($kitchen_data['orders_details']->biller_id);
        //    $print_header = "";
        //    if($kitchen_data['orders_details']->order_type == 1){
        //        $store_name = "Table : #".$kitchen_data['orders_details']->table_name;
        //    } elseif($kitchen_data['orders_details']->order_type == 2){
        //        $store_name = "Takeaway : #".$kitchen_data['orders_details']->reference_no;
        //    } else{
        //        $store_name = "Delivery : #".$kitchen_data['orders_details']->reference_no;
        //    }
        //    //$print_header .= $biller[$billid]->company;
        //    //$print_header .= ', ';
        //    //$print_header .= $biller[$billid]->address;
        //
        //    if($this->Settings->time_format == 12){
        //    $date = new DateTime($kitchen_data['orders_details']->created_on);
        //    $created_on = $date->format('Y-m-d h:iA');
        //    }else{
        //        $created_on =  $kitchen_data['orders_details']->created_on;
        //    }
        //
        //    //$print_header .= "\n";
        //    $print_header .= "KOT ORDER";
        //    $print_header .= "\n";
        //    $print_info_common = "";
        //    $print_info_common .= 'Order Number';
        //    $print_info_common .= ' : ';
        //    $print_info_common .= $kitchen_data['orders_details']->reference_no;
        //    $print_info_common .= "\n";
        //    $print_info_common .= 'Date';
        //    $print_info_common .= ' : ';
        //    // $print_info_common .= $kitchen_data['orders_details']->created_on;
        //    $print_info_common .= $created_on;
        //    $print_info_common .= "\n";
        //    $print_info_common .= 'Order Person';
        //    $print_info_common .= ' : ';
        //    $print_info_common .= $ordered_by;
        //    $print_info_common .= "\n";
        //
        //
        //
        //    if(!empty($kitchen_data['kitchens'])){
        //        foreach($kitchen_data['kitchens'] as $order_data){
        //            $print_info = '';
        //            $print_info .= $print_info_common;
        //            $print_info .= 'Kitchen Type';
        //            $print_info .= ' : ';
        //            $print_info .= $order_data->name;
        //            $print_info .= "\n-----------------------------------------------\n";
        //
        //            if(!empty($order_data->kit_o) && !empty($order_data->printers_details)){
        //                $i =1;
        //
        //
        //                foreach($order_data->kit_o as $item_data){
        //                    $print_items = "";
        //                    $list = array();
        //                    $print_items .= '';
        //                    $print_items .= $i;
        //                    $print_items .= ' ';
        //                    if(!empty($item_data['khmer_recipe_image'])){
        //                        $print_items .= $item_data['khmer_recipe_image'];
        //                    }else{
        //                        $print_items .= $item_data['recipe_name'];
        //                    }
        //                    $print_items .= "";
        //                    $print_items .= '   X ';
        //                    $print_items .= $item_data['quantity'];
        //                    $print_items .= "\n";
        //
        //                    $list[] = array(
        //                        'sno' => $i,
        //                        'en_recipe_name' => $item_data['en_recipe_name'],
        //                        'quantity' => $item_data['quantity'],
        //                        'comment' => $item_data['comment'],
        //                        'khmer_image' => $item_data['khmer_recipe_image']
        //                    );
        //                    $i++;
        //                    //Remote printing KOT
        //                $receipt = array(
        //                    'store_name' => $store_name,
        //                    'header' => $print_header,
        //                    'info' => $print_info,
        //                    'items' => $print_items,
        //                    'itemlists' => $list
        //                );
        //                $data = array(
        //                'type'=>'print-receipt',
        //                'data'=>array(
        //                    'printer' => $order_data->printers_details,
        //                    // 'logo'=> base_url().'assets/uploads/logos/'.$biller[$billid]->logo,
        //                    'text' => $receipt,
        //                    'cash_drawer' => ''
        //                )
        //                );
        //                /*echo "<pre>";
        //                print_r($data);*/
        //                if(!empty($this->ws->checkConnection())){
        //                    $result = $this->ws->send(json_encode($data));
        //                    $this->ws->close();
        //                }
        //
        //                }
        //
        //            }
        //        }
        //    }//die;
        //}
    }
    public function remotePrintingKOT($kitchen_data = array())
    {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, site_url('kot_print/all_items'));
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        $kitchendata = json_encode(array('k_data' => $kitchen_data));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $kitchendata);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        $result = curl_exec($ch);
        curl_close($ch);

        //if(!empty($kitchen_data)){
        //    $ordered_by = 'N/A';
        //    $user = $this->site->getUser($kitchen_data['orders_details']->created_by);
        //    if($user){
        //    $ordered_by = $user->first_name.' '.$user->last_name;
        //    }
        //    $biller = $this->site->getCompanyOrderByID($kitchen_data['orders_details']->biller_id);
        //    $print_header = "";
        //    if($kitchen_data['orders_details']->order_type == 1 || $kitchen_data['orders_details']->order_type == 4 ){
        //        $store_name = "Table : #".$kitchen_data['orders_details']->table_name;
        //    } elseif($kitchen_data['orders_details']->order_type == 2){
        //        $store_name = "Takeaway : #".$kitchen_data['orders_details']->reference_no;
        //    } else{
        //        $store_name = "Delivery : #".$kitchen_data['orders_details']->reference_no;
        //    }
        //    //$print_header .= $biller[$billid]->company;
        //    //$print_header .= ', ';
        //    //$print_header .= $biller[$billid]->address;
        //    if($this->Settings->time_format == 12){
        //    $date = new DateTime($kitchen_data['orders_details']->created_on);
        //    $created_on = $date->format('Y-m-d h:iA');
        //    }else{
        //        $created_on =  $kitchen_data['orders_details']->created_on;
        //    }
        //
        //    $print_header .= "\n";
        //    $print_header .= "KOT ORDER";
        //    $print_header .= "\n";
        //    $print_info_common = "";
        //    $print_info_common .= 'Order Number';
        //    $print_info_common .= ' : ';
        //    $print_info_common .= $kitchen_data['orders_details']->reference_no;
        //    $print_info_common .= "\n";
        //    $print_info_common .= 'Date';
        //    $print_info_common .= ' : ';
        //    $print_info_common .= $created_on;
        //    $print_info_common .= "\n";
        //    $print_info_common .= 'Order Person';
        //    $print_info_common .= ' : ';
        //    $print_info_common .= $ordered_by;
        //    $print_info_common .= "\n";
        //
        //
        //
        //    if(!empty($kitchen_data['kitchens'])){
        //        foreach($kitchen_data['kitchens'] as $order_data){
        //            $print_info = '';
        //            $print_info .= $print_info_common;
        //            $print_info .= 'Kitchen Type';
        //            $print_info .= ' : ';
        //            $print_info .= $order_data->name;
        //            $print_info .= "\n-----------------------------------------------\n";
        //            $print_items = "";
        //            if(!empty($order_data->kit_o) && !empty($order_data->printers_details)){
        //                $i =1;
        //                $list = array();
        //                foreach($order_data->kit_o as $item_data){
        //
        //
        //                    $print_items .= '';
        //                    $print_items .= $i;
        //                    $print_items .= ' ';
        //                    if(!empty($item_data['khmer_recipe_image'])){
        //                        $print_items .= $item_data['khmer_recipe_image'];
        //                    }else{
        //                        $print_items .= $item_data['recipe_name'];
        //                    }
        //                    $print_items .= "";
        //                    $print_items .= '   X ';
        //                    $print_items .= $item_data['quantity'];
        //                    $print_items .= "\n";
        //
        //                    $list[] = array(
        //                        'sno' => $i,
        //                        'en_recipe_name' => $item_data['en_recipe_name'],
        //                        'quantity' => $item_data['quantity'],
        //                        'khmer_image' => $item_data['khmer_recipe_image']
        //                    );
        //                    $i++;
        //                }
        //                //Remote printing KOT
        //                $receipt = array(
        //                    'store_name' => $store_name,
        //                    'header' => $print_header,
        //                    'info' => $print_info,
        //                    'items' => $print_items,
        //                    'itemlists' => $list
        //                );
        //                $data = array(
        //                'type'=>'print-receipt',
        //                'data'=>array(
        //                    'printer' => $order_data->printers_details,
        //                    'logo'=> base_url().'assets/uploads/logos/'.$biller[$billid]->logo,
        //                    'text' => $receipt,
        //                    'cash_drawer' => ''
        //                )
        //                );
        //                if(!empty($this->ws->checkConnection())){//echo '<pre>';print_R($data);
        //                $result = $this->ws->send(json_encode($data));
        //                $this->ws->close();
        //                }
        //            }
        //        }
        //    }//die;
        //}
    }
    public function remotePrintingCONSOLIDKOT_bk($kitchen_data = array())
    {

        /*echo "<pre>";
        print_r($kitchen_data);die;*/
        if (!empty($kitchen_data)) {
            $ordered_by = 'N/A';
            $user = $this->site->getUser($kitchen_data['orders_details']->created_by);
            if ($user) {
                $ordered_by = $user->first_name . ' ' . $user->last_name;
            }
            $biller = $this->site->getCompanyOrderByID($kitchen_data['orders_details']->biller_id);
            $print_header = "";
            if ($kitchen_data['orders_details']->order_type == 1 || $kitchen_data['orders_details']->order_type == 4) {
                $store_name = "Table : #" . $kitchen_data['orders_details']->table_name;
            } elseif ($kitchen_data['orders_details']->order_type == 2) {
                $store_name = "Takeaway : #" . $kitchen_data['orders_details']->reference_no;
            } else {
                $store_name = "Delivery : #" . $kitchen_data['orders_details']->reference_no;
            }
            //$print_header .= $biller[$billid]->company;
            //$print_header .= ', ';
            //$print_header .= $biller[$billid]->address;
            if ($this->Settings->time_format == 12) {
                $date = new DateTime($kitchen_data['orders_details']->created_on);
                $created_on = $date->format('Y-m-d h:iA');
            } else {
                $created_on = $kitchen_data['orders_details']->created_on;
            }

            $print_header .= "\n";
            $print_header .= "CONSOLID KOT ORDER";
            $print_header .= "\n";
            $print_info_common = "";
            $print_info_common .= 'Order Number';
            $print_info_common .= ' : ';
            $print_info_common .= $kitchen_data['orders_details']->reference_no;
            $print_info_common .= "\n";
            $print_info_common .= 'Date';
            $print_info_common .= ' : ';
            $print_info_common .= $created_on;
            $print_info_common .= "\n";
            $print_info_common .= 'Order Person';
            $print_info_common .= ' : ';
            $print_info_common .= $ordered_by;
            $print_info_common .= "\n";

            if (!empty($kitchen_data['consolid_kot_print_details'])) {

                foreach ($kitchen_data['consolid_kot_print_details'] as $order_data) {

                    /*echo "<pre>";
                    print_r($order_data);        die;    */
                    $print_info = '';
                    $print_info .= $print_info_common;
                    // $print_info .= 'Kitchen Type';
                    // $print_info .= ' : ';
                    // $print_info .= $order_data->name;
                    $print_info .= "\n-----------------------------------------------\n";
                    $print_items = "";
                    if (!empty($kitchen_data['consolid_kot_print_details']) && !empty($kitchen_data['consolid_kitchens'])) {
                        $i = 1;
                        $list = array();
                        /*echo "<pre>";
                        print_r($order_data->kit_o);die;*/
                        foreach ($kitchen_data['consolid_kitchens'] as $item_data) {

                            $print_items .= '';
                            $print_items .= $i;
                            $print_items .= ' ';
                            if (!empty($item_data['khmer_recipe_image'])) {
                                $print_items .= $item_data['khmer_recipe_image'];
                            } else {
                                $print_items .= $item_data['recipe_name'];
                            }
                            $print_items .= "";
                            $print_items .= '   X ';
                            $print_items .= $item_data['quantity'];
                            $print_items .= "\n";

                            $list[] = array(
                                'sno' => $i,
                                'en_recipe_name' => $item_data['en_recipe_name'],
                                'quantity' => $item_data['quantity'],
                                'khmer_image' => $item_data['khmer_recipe_image'],
                            );
                            $i++;
                        }
                        //Remote printing KOT
                        $receipt = array(
                            'store_name' => $store_name,
                            'header' => $print_header,
                            'info' => $print_info,
                            'items' => $print_items,
                            'itemlists' => $list,
                        );
                        $data = array(
                            'type' => 'print-receipt',
                            'data' => array(
                                'printer' => $order_data,
                                'logo' => base_url() . 'assets/uploads/logos/' . $biller[$billid]->logo,
                                'text' => $receipt,
                                'cash_drawer' => '',
                            ),
                        );

                        if (!empty($this->ws->checkConnection())) { //echo '<pre>';print_R($data);
                            $result = $this->ws->send(json_encode($data));
                            $this->ws->close();
                        }
                        /*echo "<pre>";
                    print_r($data);die;*/

                    }
                }
                /*echo "<pre>";
            print_r($data);die;*/
            } //die;
        }
    }
    public function billing()
    {
        //echo "<pre>";print_r($_POST);exit;
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
        /*echo "<pre>";
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
            $item_data = $this->pos_model->getBil($table_id, $split_id, $this->session->userdata('user_id'));
        } else {
            $item_data = $this->pos_model->getBil($table_id, $split_id, $this->session->userdata('user_id'));
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
                'warehouse_id' => $order->warehouse_id,
                'note' => $order->note,
                'staff_note' => $order->staff_note,
                'sale_status' => 'Process',
                'hash' => hash('sha256', microtime() . mt_rand()),
            );

            $customer_id = $order->customer_id;
            $notification_array['customer_id'] = $order->customer_id;
        }

        $this->data['order_data'] = $order_data;
        $postData = $this->input->post();
        $delivery_person = $this->input->post('delivery_person_id') ? $this->input->post('delivery_person_id') : 0;

        $split_status = $this->site->check_splitid_is_bill_generated($split_id);
        if ($split_status) {
            admin_redirect("pos/order_table");
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
                            /*$tot_item =    $this->input->post('[split]['.$i.'][total_item]');
                            $itemdis = $this->input->post('[split]['.$i.'][discount_amount]')/$tot_item;*/

                            $billitem['bills_items'] = array();
                            $bills_item['recipe_name'] = $this->input->post('split[][$i][recipe_name][$i]');
                            $splitData = array();
                            foreach ($postData['split'][$i]['recipe_name'] as $key => $split) {

                                // $comment_price = $postData['split'][$i]['comment_price'][$key];

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

                                    // $input_dis = $this->input->post('[split]['.$i.'][item_input_dis]['.$key.']');
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
                                'warehouse_id' => $this->session->userdata('warehouse_id'),
                                'created_by' => $this->session->userdata('user_id'),
                                'customer_discount_id' => $customer_discount_id ? $customer_discount_id : 0,
                                'discount_type' => $cus_discount_type,
                                'discount_val' => $cus_discount_val,
                                'order_discount' => $this->input->post('[split][' . $i . '][discount_amount]') ? $this->input->post('[split][' . $i . '][discount_amount]') : null,
                                'service_charge_id' => $this->input->post('[split][' . $i . '][service_charge]') ? $this->input->post('[split][' . $i . '][service_charge]') : 0,
                                'service_charge_amount' => $this->input->post('[split][' . $i . '][service_amount]') ? $this->input->post('[split][' . $i . '][service_amount]') : 0,

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
                                admin_redirect("pos/order_table?tid=" . $tableid);
                            } elseif ($order_type == 2) {
                                admin_redirect("pos/order_takeaway");
                            } elseif ($order_type == 3) {
                                admin_redirect("pos/order_doordelivery");
                            }
                        }
                    } else {
                        $customer_id = $this->site->getCustomerDetails($waiter_id, $table_id, $split_id);
                        $this->data['discount_select'] = $this->pos_model->getDiscountdata($customer_id, $waiter_id, $table_id, $split_id);

                        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
                        if ($this->pos_settings->billgeneration_screen == 1) {
                            $this->load->view($this->theme . 'pos/singlebil', $this->data);
                        } else {
                            $this->load->view($this->theme . 'pos/bill_generation/template2/singlebil', $this->data);
                        }
                    }
                }
            } else {
                $customer_id = $this->site->getCustomerDetails($waiter_id, $table_id, $split_id);
                $this->data['discount_select'] = $this->pos_model->getDiscountdata($customer_id, $waiter_id, $table_id, $split_id);

                $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
                // echo '<pre>';print_R($this->data);exit;

                if ($this->pos_settings->billgeneration_screen == 1) {
                    $this->load->view($this->theme . 'pos/singlebil', $this->data);
                } else {
                    $this->load->view($this->theme . 'pos/bill_generation/template2/singlebil', $this->data);
                }
            }
        } elseif ($bill_type == 2) {

            $this->form_validation->set_rules('splits', $this->lang->line("splits"), 'trim|required');
            if ($this->form_validation->run() == true) {
                if ($this->input->post('action') == "AUTOSPLITBILL-SUBMIT") {
                    //echo "<pre>";
                    //print_r($this->input->post());die;
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
/* echo "<pre>";
print_r($postData);die; */
                                $splitData[$i][] = array(
                                    'recipe_name' => $split,
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
                                    'recipe_variant' => $postData['split'][$i]['recipe_variant'][$key],
                                    'recipe_variant_id' => $postData['split'][$i]['recipe_variant_id'][$key],
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
                                'warehouse_id' => $this->session->userdata('warehouse_id'),
                                'created_by' => $this->session->userdata('user_id'),
                                'customer_discount_id' => $customer_discount_id,
                                'customer_discount_status' => $customer_discount_status,
                                'discount_type' => $cus_discount_type,
                                'discount_val' => $cus_discount_val,
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
/*

echo "<pre>";

print_r($splitData);
print_r($billData);die;        */

                        $response = $this->pos_model->InsertBill($order_data, $order_item, $billData, $splitData, $sales_total, $delivery_person, $timelog_array, $notification_array, $order_item_id, $split_id, $request_discount, $dine_in_discount, $birthday);

                        if ($response == 1) {
                            $update_notifi['split_id'] = $split_id;
                            $update_notifi['tag'] = 'bill-request';
                            $this->site->update_notification_status($update_notifi);
                            if ($order_type == 1) {
                                $tableid = $order_data['sales_table_id'];
                                admin_redirect("pos/order_table?tid=" . $tableid);
                            } elseif ($order_type == 2) {
                                admin_redirect("pos/order_takeaway");
                            } elseif ($order_type == 3) {
                                admin_redirect("pos/order_doordelivery");
                            }
                        }

                    } else {
                        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
                        $this->load->view($this->theme . 'pos/autosplitbil', $this->data);
                    }

                }
            } else {
                $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
                $this->load->view($this->theme . 'pos/autosplitbil', $this->data);
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
                                'warehouse_id' => $this->session->userdata('warehouse_id'),
                                'created_by' => $this->session->userdata('user_id'),
                                'customer_discount_id' => $customer_discount_id,
                                'customer_discount_status' => $customer_discount_status,
                                'discount_type' => $cus_discount_type,
                                'discount_val' => $cus_discount_val,
                            );
                            if (isset($_POST['unique_discount'])) {
                                $billData[$i]['unique_discount'] = 1;
                            }
                        }

                        $sales_total = array_column($billData, 'grand_total');
                        $sales_total = array_sum($sales_total);
                        $dine_in_discount = $this->input->post('dine_in_discount') ? $this->input->post('dine_in_discount') : 0;

/*                    if($birthday_discount != 0){
$birthday = array(
'customer_id' => $customer_id,
'birthday_discount' => $birthday_discount,
'status' => 1,
'issue_date' => date('Y-m-d'),
'created_at' => $this->session->userdata('user_id'),
'created_on' => date('Y-m-d H:i:s')
);
}else{
$birthday = array();
}*/
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
                                admin_redirect("pos/order_table?tid=" . $tableid);
                            } elseif ($order_type == 2) {
                                admin_redirect("pos/order_takeaway");
                            } elseif ($order_type == 3) {
                                admin_redirect("pos/order_doordelivery");
                            }
                        }

                    } else {
                        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
                        $this->load->view($this->theme . 'pos/manualsplitbil', $this->data);
                    }

                }

            } else {
                $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
                $this->load->view($this->theme . 'pos/manualsplitbil', $this->data);
            }
        }

    }

    public function reports(){
        if (($this->pos_settings->taxation_report_settings == 1) && ($this->pos_report_view_access == 0)) {
            $this->load->view($this->theme . 'pos/reports_passcode', $this->data);
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
            if (isset($category_id)) {
                $group = $category_id;
            } else {
                $group = "";
            }
            if (isset($subcategory_id)) {
                $subgroup = $subcategory_id;
            } else {
                $subgroup = "";
            }

            /*var_dump($category_id)."<br>";
            var_dump($subcategory_id);exit;*/
            /*echo "<pre>";
            print_r($_GET);exit;*/
            if ($reports_type == 1) {
                $this->data['categories'] = $this->site->getAllrecipeCategories();
                $this->data['sub_categories'] = $this->site->getAllrecipe_subCategories();
                $this->data['recipes'] = $this->pos_model->getItemSaleReports($start, $end, $this->pos_report_view_access, $this->pos_report_show, $group, $subgroup);
				/* 
				if($this->Settings->archival_report){
					 $this->data1['recipes'] = $this->pos_model->getItemSaleReports_archival($start, $end, $this->pos_report_view_access, $this->pos_report_show, $group, $subgroup);
					
					 foreach($this->data1['recipes'] as $row){
				     $data['recipes'][]=$row;
			       }
				}
				 print_r( $this->data['recipes']);
					 die; */
                $this->data['round'] = $this->pos_model->getRoundamount($start, $end);
                $this->load->view($this->theme . 'pos/item_reports', $this->data);

            } elseif ($reports_type == 2) {
                $vale = $this->settings->default_currency;
                $this->data['row'] = $this->pos_model->getdaysummary($start, $end, $this->pos_report_view_access, $this->pos_report_show);
                // $this->data['collection'] = $this->pos_model->getCollection($start,$end,$this->pos_report_view_access,$this->pos_report_show);
                $this->data['tendersales'] = $this->pos_model->getTendertypes($start, $end, $this->pos_report_view_access, $this->pos_report_show);
                // echo "<pre>";
                // print_r($this->data['tendersales']['Exchange'][0]->usd);die;
				if($this->Settings->archival_report){
					 $this->data1['row']= $this->pos_model->getdaysummary_archival($start, $end, $this->pos_report_view_access, $this->pos_report_show);
					 $this->data1['tendersales']            = $this->pos_model->getTendertypes_archival($start, $end, $this->pos_report_view_access, $this->pos_report_show);
					 $this->data['row']->total             +=!empty($this->data1['row']->total)?$this->data1['row']->total:0;
					 $this->data['row']->total_amount1  +=!empty($this->data1['row']->total_amount1)?$this->data1['row']->total_amount1:0;
					 $this->data['row']->total_tax      +=!empty($this->data1['row']->total_tax)?$this->data1['row']->total_tax:0;
					 $this->data['row']->service_charge_amount +=!empty($this->data1['row']->service_charge_amount)?$this->data1['row']->service_charge_amount:0;
					 $this->data['row']->total_discount   +=!empty($this->data1['row']->total_discount)?$this->data1['row']->total_discount:0;
					 $this->data['row']->total_amount     +=!empty($this->data1['row']->total_amount)?$this->data1['row']->total_amount:0;
					 $this->data['row']->totalbill        +=!empty($this->data1['row']->totalbill)?$this->data1['row']->totalbill:0;
					 $this->data['row']->net_amt          +=!empty($this->data1['row']->net_amt)?$this->data1['row']->net_amt:0;
					 $this->data['row']->gross_amt        +=!empty($this->data1['row']->gross_amt)?$this->data1['row']->gross_amt:0;
					 $this->data['row']->netamt           +=!empty($this->data1['row']->netamt)?$this->data1['row']->netamt:0;
					 $this->data['tendersales']['Exchange'][]=$this->data1['tendersales']['Exchange'];
					 $this->data['tendersales']['Tender_Type'][]=$this->data1['tendersales']['Tender_Type'];
					
				}
				
                $this->load->view($this->theme . 'pos/day_reports', $this->data);

            } elseif ($reports_type == 3) {
                $this->data['cashier'] = $this->pos_model->getCashierReport($start, $end, $this->pos_report_view_access, $this->pos_report_show);
                /*$this->data['collection'] = $this->pos_model->getCollection($start,$end);*/
				//archival_data report part start    
				if($this->Settings->archival_report){
					$this->data1['cashier'] = $this->pos_model->getCashierReport_archival($start, $end, $this->pos_report_view_access, $this->pos_report_show);
					foreach( $this->data['cashier'] as $k => $v) {
					 foreach( $this->data1['cashier'] as $row){
					     if($row->id ==$v->id){
						       $this->data['cashier'][$k]->grand_total += !empty($row->grand_total)?$row->grand_total:0;
					           }else{  $this->data['cashier'][]=$row;  }
				        } 
				    } 
				 }
			//archival_data report part End
                $this->data['dates'] = $dates;
                $this->load->view($this->theme . 'pos/cashier_reports', $this->data);

            } elseif ($reports_type == 4) {
                $this->data['settlement'] = $this->pos_model->getSettlementReport($start, $end, $this->pos_report_view_access, $this->pos_report_show);
                // echo $this->data['settlement'];die;
                /*$this->data['collection'] = $this->pos_model->getCollection($start,$end);*/
				if($this->Settings->archival_report){
					$this->data1['settlement'] = $this->pos_model->getSettlementReport_archival($start, $end, $this->pos_report_view_access, $this->pos_report_show);
			
					foreach( $this->data['settlement']['payments'] as $k => $v) {
					 foreach( $this->data1['settlement']['payments'] as $row){
						       $this->data['settlement']['payments'][$k]->total_transaction += !empty($row->total_transaction)?$row->total_transaction:0;
							   $this->data['settlement']['payments'][$k]->gross_total1 += !empty($row->gross_total1)?$row->gross_total1:0;
							   $this->data['settlement']['payments'][$k]->gross_total += !empty($row->gross_total)?$row->gross_total:0;
							   $this->data['settlement']['payments'][$k]->net_total1 += !empty($row->net_total1)?$row->net_total1:0;
							   $this->data['settlement']['payments'][$k]->net_total += !empty($row->net_total)?$row->net_total:0;
				       } 
				    }
					
					foreach( $this->data['settlement']['tender_type'] as $k => $v) {
					 foreach( $this->data1['settlement']['tender_type'] as $row){
						    if($row['tender_type'] ==$v['tender_type']){
						      $this->data['settlement']['tender_type'][$k]['tender_type_total'] += !empty($row['tender_type_total'])?$row['tender_type_total']:0;
				       } else{
						   $this->data['settlement']['tender_type'][]=$row;
					   }
				    }
					}
					
					foreach( $this->data['settlement']['sale_type'] as $k => $v) {
					 foreach( $this->data1['settlement']['sale_type'] as $row){
						    if($row->sale_type ==$v->sale_type){
						       $this->data['settlement']['sale_type'][$k]->sale_type_total1 += !empty($row->sale_type_total1)?$row->sale_type_total1:0;
							   $this->data['settlement']['sale_type'][$k]->sale_type_total += !empty($row->sale_type_total)?$row->sale_type_total:0;
				       } else{
						   $this->data['settlement']['sale_type'][]=$row;
					   }
				    }
					}
					foreach( $this->data['settlement']['exchange_amt'] as $k => $v) {
					 foreach( $this->data1['settlement']['exchange_amt'] as $row){
						    if($row->usd ==$v->usd){
						       $this->data['settlement']['exchange_amt'][$k]->For_Ex += !empty($row->For_Ex)?$row->For_Ex:0;
				       } else{
						   $this->data['settlement']['exchange_amt']['exchange_amt'][]=$row;
					   }
				    }
					}
					foreach( $this->data['settlement']['open_sale'] as $k => $v) {
					 foreach( $this->data1['settlement']['open_sale'] as $row){
						    if($row->id ==$v->id){
						       $this->data['settlement']['open_sale'][$k]->opensale += !empty($row->opensale)?$row->opensale:0;
				       } else{
						       $this->data['settlement']['open_sale'][]=$row;
					   }
				    }
					}
				}
                $this->data['dates'] = $dates;
                $this->load->view($this->theme . 'pos/settlement_reports', $this->data);

            } elseif ($reports_type == 5) {
                $this->data['shifttime'] = $this->pos_model->getshifttime();
                $shift_id = $this->input->get('shift_id') ? $this->input->get('shift_id') : 0;
                $this->data['shiftreport'] = $this->pos_model->getShiftWiseReport($start, $end, $shift_id, $this->pos_report_view_access, $this->pos_report_show);
				if($this->Settings->archival_report){
					  $this->data1['shiftreport'] = $this->pos_model->getShiftWiseReport_archival($start, $end, $shift_id, $this->pos_report_view_access, $this->pos_report_show);
				
				foreach( $this->data['shiftreport'] as $k => $v) {
					 foreach( $this->data1['shiftreport'] as $row){
				       if($row->cate_id==$v->cate_id ){
					     foreach($v->user as $j =>$w){
							  foreach($row->user as $row1){
							  if($row1->id ==$w->id){
								   $this->data['shiftreport'][$k]->user[$j]->Cash +=!empty($row1->Cash)?$row1->Cash:0 ;
								   $this->data['shiftreport'][$k]->user[$j]->For_Exto_usd +=!empty($row1->id)?$row1->For_Exto_usd:0 ;
								   $this->data['shiftreport'][$k]->user[$j]->Credit_Card +=!empty($row1->id)?$row1->Credit_Card:0 ;
								   $this->data['shiftreport'][$k]->user[$j]->credit +=!empty($row1->id)?$row1->credit:0 ;
								    $this->data['shiftreport'][$k]->user[$j]->Bill_amt +=!empty($row1->id)?$row1->Bill_amt:0 ;
							  }else{
								  $this->data['shiftreport'][$k]['user'][$j]=$row1;
							  }
							  } 
				   }
					 }else{
						 $this->data['shiftreport'][]=$row;
					 }
				}
				
			}
				}
				
				//print_r($this->data1['shiftreport']);
			
                $this->data['dates'] = $dates;
                $this->load->view($this->theme . 'pos/shift_report', $this->data);
            } else {
                $this->data['recipes'] = $this->pos_model->getItemSaleReports($start, $end, $this->pos_report_view_access, $this->pos_report_show);
                $this->load->view($this->theme . 'pos/item_reports', $this->data);

            }
        }
    }
    public function report_view_access()
    {
        $pass_code = $this->input->post('pass_code');
        $data = $this->pos_model->check_reportview_access($pass_code);

        if ($data != 0) {
            $this->session->set_userdata('pos_report_view_access', $data);
            $this->sma->send_json($data);
        } else {
            $this->sma->send_json(0);
        }
    }

    public function paymant(){
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
                            /*$amount_.$currency_row->code = array_sum($_POST['amount_'.$currency_row->code]);*/
                            $amount = $_POST['amount_' . $currency_row->code][$r];
                        } else {
                            /*$amount_.$currency_row->code = array_sum($_POST['amount_'.$currency_row->code]);*/
                            $amount_exchange = $_POST['amount_' . $currency_row->code][$r];

                        }
                    }
                    $crd_exp_date = explode('/', $this->input->post('card_exp_date[1]'));
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
                        // 'cheque_no'   => $_POST['cheque_no'][$r],
                        'cc_no' => $_POST['cc_no'][$r],
                        'cc_month' => $crd_exp_date[0],
                        'cc_year' => $crd_exp_date[1],
                        /*'cc_holder'   => $_POST['cc_holer'][$r],
                        'cc_month'    => $_POST['cc_month'][$r],
                        'cc_year'     => $_POST['cc_year'][$r],
                        'cc_type'     => $_POST['cc_type'][$r],*/
                        // 'cc_cvv2'   => $this->input->post('cc_cvv2'),
                        /*'sale_note'   => $_POST['sale_note'],
                        'staff_note'   => $_POST['staffnote'],
                        'payment_note' => $_POST['payment_note'][$r],*/
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
                    $response = $this->pos_model->addRoughTender($billid, $payment, $multi_currency, $updateCreditLimit);
                } else {
                    // echo "<pre>";
                    // print_r($payment);
                    // print_r($multi_currency);die;
                    $response = $this->pos_model->Payment($update_bill, $billid, $payment, $multi_currency, $salesid, $sales_bill, $order_split_id, $notification_array, $updateCreditLimit, $total, $customer_id, $loyalty_used_points, $taxation, $customer_changed);
                }
            }

            if ($response == 1) {

                //$this->send_to_bill_print($billid);
                $update_notifi['split_id'] = $order_split_id;
                $update_notifi['tag'] = 'bill-request';
                $this->site->update_notification_status($update_notifi);
                if ($taxation == 1) {
                    admin_redirect("pos/order_biller");
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
/*echo "<pre>";
print_r($inv);die;*/
                $tableid = $this->pos_model->getTableID($billid);
                if (!empty($inv)) {
                    if (@$new_payment) {
                        $this->data['socket_tableid'] = $tableid;
                    }
                    if (isset($_POST['rough_tender'])) {
                        $this->data['rough_tender'] = true;
                    }
                    if ($this->pos_settings->bill_print_format == 1) {
                        $this->load->view($this->theme . 'pos/view_bill', $this->data);
                    } elseif ($this->pos_settings->bill_print_format == 3) {
                        $this->load->view($this->theme . 'pos/indai_bill/view_bill', $this->data, false);
                    } elseif ($this->pos_settings->bill_print_format == 4) {
                        $this->load->view($this->theme . 'pos/local_bill/view_bill', $this->data, false);
                    } else {
                        $this->load->view($this->theme . 'pos/row_discount/view_bill', $this->data);
                    }
                } else {
                    admin_redirect("pos/order_biller?tid=" . $tableid);
                }
            }
        } else {
            admin_redirect("pos/order_biller");
        }

    }

    public function reprint_view()
    {
        // admin_redirect("pos/paymat");
        $billid = $this->input->get('bill_id');
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
        $this->data['payments'] = $this->pos_model->getInvoicePayments($billid);
        $this->data['return_sale'] = $inv->return_id ? $this->pos_model->getInvoiceByID($inv->return_id) : null;
        $this->data['return_rows'] = $inv->return_id ? $this->pos_model->getAllInvoiceItems($inv->return_id) : null;
        $this->data['return_payments'] = $this->data['return_sale'] ? $this->pos_model->getInvoicePayments($this->data['return_sale']->id) : null;
        $this->data['type'] = $this->input->post('type');
        /*var_dump($this->pos_settings->bill_print_format);die;*/

        $this->site->send_to_bill_print($billid);

        if ($this->pos_settings->bill_print_format == 1) {
            $this->load->view($this->theme . 'pos/reprint_viewbill', $this->data);
        } elseif ($this->pos_settings->bill_print_format == 3) {
            $this->load->view($this->theme . 'pos/indai_bill/reprint_viewbill', $this->data);
        } elseif ($this->pos_settings->bill_print_format == 4) {
            $this->load->view($this->theme . 'pos/local_bill/reprint_viewbill', $this->data);
        } else {
            $this->load->view($this->theme . 'pos/row_discount/reprint_viewbill', $this->data);
        }
    }
    public function multiple_reprint_view()
    {
        $bill_id = $this->input->get('bill_id');
        $billids = (explode(",", $bill_id));
        /*echo "<pre>";
        print_r($billids);die;*/
        $billdata = array();
        foreach ($billids as $key => $billid) {
            // echo $billid;
            // $this->data['order_item'] = $this->pos_model->getAllBillitems($billid);
            $billdata['message'][$key] = $this->session->flashdata('message');

            $inv = $this->pos_model->getInvoiceByID($billid);
            $tableno = $this->pos_model->getTableNumber($billid);
            $billdata['bill_id'][$key] = $key;

            $this->load->helper('pos');
            if (!$this->session->userdata('view_right')) {
                $this->sma->view_rights($inv->created_by, true);
            }
            $billdata['billi_tems'][$key] = $this->pos_model->getAllBillitems($billid);
            $billdata['discounnames'][$key] = $this->pos_model->getBillDiscountNames($billid);
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
            $billdata['payments'][$key] = $this->pos_model->getInvoicePayments($billid);
            // $this->data['return_sale'][$key] = $inv->return_id ? $this->pos_model->getInvoiceByID($inv->return_id) : NULL;
            // $this->data['return_rows'][$key] = $inv->return_id ? $this->pos_model->getAllInvoiceItems($inv->return_id) : NULL;
            // $this->data['return_payments'][$key] = $this->data['return_sale'] ? $this->pos_model->getInvoicePayments($this->data['return_sale']->id) : NULL;
            $billdata['type'][$key] = $this->input->post('type');
            $billdata['pos_settings'][$key] = $this->pos_settings;
            $billdata['Settings'] = $this->Settings;
            $billdata['assets'] = $this->data['assets'];
        }
        $this->load->view($this->theme . 'pos/multiple_bill_reprint', $billdata);

    }

    public function order_table(){
        $this->sma->checkPermissions('index');
        $table_id = !empty($this->input->get('table')) ? $this->input->get('table') : '';
        $user = $this->site->getUser();
        $this->data['avil_customers'] = $this->site->getAvilAbleCustomers();
        $this->data['avil_tables'] = $this->site->getAvilAbleTables($table_id);
        $this->data['warehouses'] = null;
        $this->data['warehouse_id'] = $this->session->userdata('warehouse_id');
        $this->data['warehouse'] = $this->session->userdata('warehouse_id') ? $this->site->getWarehouseByID($this->session->userdata('warehouse_id')) : null;
        $this->data['tableid'] = !empty($this->input->get('table')) ? $this->input->get('table') : '';
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->load->view($this->theme . 'pos/ordertable', $this->data);
    }

    public function ajaxorder_table(){
        $table_id = !empty($this->input->get('table')) ? $this->input->get('table') : '';

        // $this->data['tables'] = $this->pos_model->getAllTablesorder_new($table_id);
        /*echo "<pre>";
        print_r($this->data['tables']);die;*/
        // $this->data['avil_tables'] = $this->site->getAvilAbleTables($table_id);
        // $this->data['avil_customers'] = $this->site->getAvilAbleCustomers();
        $this->load->view($this->theme . 'pos/ordertable_ajax', $this->data);

    }

    public function tablecheck($order_type = null, $table_id = null)
    {
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
    public function tablecheckdine($order_type = null, $table_id = null)
    {
        $order_type = $this->input->get('order_type');
        $table_id = $this->input->get('table_id');
        $table = $this->pos_model->checkTables($table_id, $order_type);
        $this->sma->send_json($table);
    }

    public function tablecheckwithbbq($order_type = null, $table_id = null, $menu_id = null)
    {
        $order_type = $this->input->get('order_type');
        $table_id = $this->input->get('table_id');
        $menu_id = $this->input->get('menu_id');
        $menuprice = $this->pos_model->getbbqmenucoverpricebyid($menu_id);
        $price['adult_price'] = $menuprice->adult_price;
        $price['child_price'] = $menuprice->child_price;
        $price['kids_price'] = $menuprice->kids_price;
        $this->sma->send_json($menuprice);
    }

    public function ajaxBildata($table_id = null, $split_id = null)
    {
        $table_id = $this->input->get('table_id');
        $split_id = $this->input->get('split_id');
        $bil = $this->pos_model->getBil($table_id, $split_id, $this->session->userdata('user_id'));

        foreach ($bil['items'] as $bil_item) {
            foreach ($bil_item as $item) {
                $item_data[] = $item;
                $total_subtotal[] = $item->subtotal;
            }
        }

        $total_items = count($item_data);
        foreach ($bil['order'] as $bil_order) {
            $order_data = array('sales_type_id' => $bil_order->order_type,
                'sales_split_id' => $bil_order->split_id,
                'sales_table_id' => $bil_order->table_id,
                'date' => date('Y-m-d H:i:s'),
                'reference_no' => 'SALES-' . date('YmdHis'),
                'customer_id' => $bil_order->customer_id,
                'customer' => $bil_order->customer,
                'biller_id' => $bil_order->biller_id,
                'biller' => $bil_order->biller,
                'warehouse_id' => $bil_order->warehouse_id,
                'note' => $bil_order->note,
                'staff_note' => $bil_order->staff_note,
                'total' => array_sum($total_subtotal),
                'sale_status' => 'Process',
                'total_items' => $total_items,
                'grand_total' => array_sum($total_subtotal),
                'hash' => hash('sha256', microtime() . mt_rand()),
            );
        }
        $sales = $this->pos_model->updateNewSales($order_data, $item_data);
        $this->sma->send_json(array('status' => $sales));

    }

    public function ajaxOrderitemdata($order_id = null, $table_id = null, $split_id = null)
    {
        $order_id = $this->input->get('order_id');
        $table_id = $this->input->get('table_id');
        $split_id = $this->input->get('split_id');

        $order_item = $this->pos_model->getOrderitemlist($order_id, $table_id, $split_id, $this->session->userdata('user_id'));

        $item = '';
        if (!empty($order_item)) {
            $html = '<div id="recipe-list" class="dragscroll" style="height: 0px; min-height: 278px;">
					<table class="table items table-striped table-fixed table-bordered table-condensed table-hover sortable_table" id="posTable" style="margin-bottom: 0;">
						<thead>
						<tr>
							<th width="40%">recipe</th>
							<th width="15%">Price</th>
							<th width="15%">Qty</th>
							<th width="20%">Subtotal</th>

						</tr>
						</thead>';

            $html .= '<tbody class="ui-sortable">';
            foreach ($order_item as $item) {
                $html .= '<tr><td>' . $item->recipe_name . '</td><td>' . $item->unit_price . '</td><td>' . $item->quantity . '</td><td>' . $item->subtotal . '</td></tr>';
            }
            $html .= '</tbody>';

            $html .= '</table>
					<div style="clear:both;"></div>
				</div>';
            $item = $html;
        }
        $this->sma->send_json(array('order_item' => $item));
    }

    public function order_biller($split_id = null, $bill_type = null, $sid = null, $new_customer = null){

        $new_customer = $this->input->get('customer');
        $split_id = $this->input->get('split_id');
        $bill_type = $this->input->get('bill_type');
        $sales_type_id = $this->input->get('type');
        $this->data['type'] = $this->input->get('type');
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

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $order_printers = json_decode($this->pos_settings->order_printers);
        $printers = array();
        if (!empty($order_printers)) {
            foreach ($order_printers as $printer_id) {
                $printers[] = $this->pos_model->getPrinterByID($printer_id);
            }
        }
        $this->data['order_printers'] = $printers;
        if ($sales_type_id) {
            $this->data['sales'] = $this->pos_model->getAllSalesWithbiller($sales_type_id);
            //echo "<pre>";
            //print_r($this->data['sales']);die;
        }
		
        if ($this->pos_settings->bill_print_format == 3) {
            $this->load->view($this->theme . 'pos/indai_bill/orderbiller', $this->data, false);
        } elseif ($this->pos_settings->bill_print_format == 4) {
            $this->load->view($this->theme . 'pos/local_bill/orderbiller_newscreen', $this->data, false);
        } else {
            $this->load->view($this->theme . 'pos/orderbiller_newscreen', $this->data);
        }
    }

    public function ajaxorder_billing()
    {
        $table_id = (isset($_GET['table']) && $_GET['table'] != '') ? $this->input->get('table') : null;
        $sales_type_id = !empty($this->input->get('type')) ? $this->input->get('type') : '';
        if ($sales_type_id == 1) {
            $this->data['sales_type'] = 'Dine In';
        } elseif ($sales_type_id == 2) {
            $this->data['sales_type'] = 'Take Away';
        } elseif ($sales_type_id == 3) {
            $this->data['sales_type'] = 'Door Delivery';
        }

        if ($sales_type_id) {
            $this->data['sales'] = $this->pos_model->getAllSalesWithbiller($sales_type_id, $table_id);
        }
        $this->load->view($this->theme . 'pos/orderbiller_ajax', $this->data);
    }

    public function reprinter(){
        $start = $this->input->get('date');
        if ($start) {
            $start = $start;

        } else {
            $start = date('Y-m-d');
        }
        /*$this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['sales_types'] = $this->site->getAllSalestype();
        $this->data['billers'] = $this->site->getAllCompanies('biller');
        $this->data['warehouse_id'] = $this->session->userdata('warehouse_id');
        $this->data['warehouse'] = $this->session->userdata('warehouse_id') ? $this->site->getWarehouseByID($this->session->userdata('warehouse_id')) : NULL;
        $this->data['sid'] = $this->input->get('suspend_id') ? $this->input->get('suspend_id') : $sid;
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');        */

        $this->data['sales'] = $this->pos_model->getAllBillingforReprint($start);
        $this->load->view($this->theme . 'pos/bill_reprint', $this->data);

    }
    public function ajaxBilleritemdata($order_id = null, $table_id = null, $split_id = null){
        $order_id = $this->input->get('order_id');
        $table_id = $this->input->get('table_id');
        $split_id = $this->input->get('split_id');
        $order_item = $this->pos_model->getBilleritemlist($order_id, $table_id, $split_id, $this->session->userdata('user_id'));
        $item = '';
        if (!empty($order_item)) {
            $html = '<div id="recipe-list" class="dragscroll" style="height: 0px; min-height: 278px;">
                    <table class="table items table-striped table-fixed table-bordered table-condensed table-hover sortable_table" id="posTable" style="margin-bottom: 0;">
                        <thead>
                        <tr>
                            <th width="40%">recipe</th>
                            <th width="15%">Price</th>
                            <th width="15%">Qty</th>
                            <th width="20%">Subtotal</th>

                        </tr>
                        </thead>';

            $html .= '<tbody class="ui-sortable">';
            foreach ($order_item as $item) {
                $html .= '<tr><td>' . $item->recipe_name . '</td><td>' . $item->unit_price . '</td><td>' . $item->quantity . '</td><td>' . $item->subtotal . '</td></tr>';
            }
            $html .= '</tbody>';

            $html .= '</table>
                    <div style="clear:both;"></div>
                </div>';
            $item = $html;
        }
        $this->sma->send_json(array('order_item' => $item));
    }

    public function ajaxBillCashierPrintdata($split_id = null)
    {
        $split_id = $this->input->get('split_id');

        $order_item = $this->pos_model->getBillCashierPrintdata($split_id);

        $item = '';
        if (!empty($order_item)) {
            $html = '<div id="recipe-list" class="dragscroll" style="height: 0px; min-height: 278px;">
                    <table class="table items table-striped table-fixed table-bordered table-condensed table-hover sortable_table" id="posTable" style="margin-bottom: 0;">
                        <thead>
                        <tr>
                            <th width="40%">Recipe</th>
                            <th width="15%">Price</th>
                            <th width="15%">Qty</th>
                            <th width="15%">Sub Total</th>
                        </tr>
                        </thead>';

            $html .= '<tbody class="ui-sortable">';
            foreach ($order_item as $item) {
                $html .= '<tr><td>' . $item->recipe_name . '</td><td>' . $item->quantity . '</td><td>' . $item->unit_price . '</td><td class="text-right">' . $item->subtotal . '</td></tr>';
            }
            /*$html .='<tr><td colspan="4" class="text-right">Shipping</td><td class="text-right">'.$item->shipping.'</td></tr>';
            $html .='<tr><td colspan="4" class="text-right">Grand Total</td><td class="text-right">'.$item->grand_total.'</td></tr>';*/

            $html .= '<tr>
                                        <td class="left_td text-right" colspan="2" style="padding: 5px 10px;">' . lang("order_tax") . '
                                            <a href="#" id="pptax2">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                        </td>

                                        <td class="center_td">:</td>

                                        <td class="right_td text-right" style="padding: 5px 10px;font-size: 14px; font-weight:bold;">
                                            <span id="ttax2">0.00</span>
                                        </td>
                     </tr>
                     <tr>
                                    <td class="left_td text-right" colspan="2" style="padding: 5px 10px;">' . lang("discount") . '

                                            <a href="#" id="ppdiscount">
                                                <i class="fa fa-edit"></i>
                                            </a>

                                    </td>

                                    <td class="center_td">:</td>

                                        <td class="text-right" style="padding: 5px 10px;font-weight:bold;" >
                                            <span id="tds">0.00</span>
                                        </td>
                    </tr> <tr style="border-top: 1px solid #e2e2e2;">
                        <td class="left_td text-right" colspan="2" style="padding: 5px 10px; border-top: 1px solid #666; border-bottom: 1px solid #333; font-weight:bold; background:#fff; color:#4b2d0a;">
                            ' . lang("total_payable") . '
                            <a href="#" id="pshipping">
                                <i class="fa fa-plus-square"></i>
                            </a>
                            <span id="tship"></span>
                        </td>

                        <td class="center_td">:</td>

                        <td class="right_td text-right" style="padding:5px 10px 5px 10px; font-size: 14px; border-bottom: 1px solid #333; font-weight:bold; background:#fff; color:#a76821;" >
                            <span id="gtotal">0.00</span>
                        </td>
                </tr>';

            $html .= '</tbody>';

            $html .= '</table>
                    <div style="clear:both;"></div>
                </div>';
            $item = $html;
        }
        $this->sma->send_json(array('order_item' => $item));
    }

    public function order_takeaway(){
        $this->sma->checkPermissions('index');
        $user = $this->site->getUser();
        $this->data['warehouses'] = null;
        $this->data['warehouse_id'] = $this->session->userdata('warehouse_id');
        $this->data['warehouse'] = $this->session->userdata('warehouse_id') ? $this->site->getWarehouseByID($this->session->userdata('warehouse_id')) : null;
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->load->view($this->theme . 'pos/ordertakeaway', $this->data);
    }

    public function ajaxorder_takeaway(){
        $this->data['takeaway'] = $this->pos_model->getAllTakeawayorder();
        $this->load->view($this->theme . 'pos/ordertakeaway_ajax', $this->data);
    }

    public function order_doordelivery(){
        $this->sma->checkPermissions('index');
        $user = $this->site->getUser();
        $this->data['warehouses'] = null;
        $this->data['warehouse_id'] = $this->session->userdata('warehouse_id');
        $this->data['warehouse'] = $this->session->userdata('warehouse_id') ? $this->site->getWarehouseByID($this->session->userdata('warehouse_id')) : null;
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->load->view($this->theme . 'pos/orderdoordelivery', $this->data);
    }

    public function ajaxorder_doordelivery(){
        $this->data['doordelivery'] = $this->pos_model->getAllDoordeliveryorder();
        $this->load->view($this->theme . 'pos/orderdoordelivery_ajax', $this->data);
    }

    public function order_kitchen(){
        $this->sma->checkPermissions('index');
        $user = $this->site->getUser();
        $type = $this->input->get('type', true);
        $this->data['kitchen_type'] = $type ? $type : 1;
        $this->data['warehouses'] = null;
        $this->data['warehouse_id'] = $this->session->userdata('warehouse_id');
        $this->data['warehouse'] = $this->session->userdata('warehouse_id') ? $this->site->getWarehouseByID($this->session->userdata('warehouse_id')) : null;
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->load->view($this->theme . 'pos/orderkitchen', $this->data);
    }

    public function ajaxorder_kitchen(){
        $kitchen_type = !empty($this->input->get('kitchen_type')) ? $this->input->get('kitchen_type') : 1;
        $this->data['orders'] = $this->pos_model->getAllTablesWithKitchen($kitchen_type);
        /*echo "<pre>";
        print_r($this->data['orders']);die;*/
        $this->data['kitchen_value'] = !empty($this->input->get('kitchen_type')) ? $this->input->get('kitchen_type') : 1;
        $this->data['reskitchen'] = $this->site->getAllResKitchen();
        $this->load->view($this->theme . 'pos/orderkitchen_ajax', $this->data);
    }

    public function update_order_statusfrom_kitchen($status = null, $order_id = null, $order_item_id = null, $order_type = null){
        $status = $this->input->get('status');
        $order_item_id = $this->input->get('order_item_id');
        $order_id = $this->input->get('order_id');
        $order_type = $this->input->get('order_type');

        if ($status == 'Inprocess') {
            $current_status = 'Preparing';
        } elseif ($status == 'Preparing' && $order_type == 1 || $order_type == 4) {
            $current_status = 'Ready';
        } elseif ($status == 'Preparing' && ($order_type == 2 || $order_type == 3)) {
            $current_status = 'Closed';
        } else {
            $current_status = 'Inprocess';
        }

        $customer_id = $this->site->getOrderCustomer($order_id);

        $notification_array['customer_role'] = CUSTOMER;
        $notification_array['customer_msg'] = 'The item has been ' . $current_status . ' to chef';
        $notification_array['customer_type'] = 'Chef ' . $current_status . ' Status';
        $notification_array['customer_id'] = $customer_id;

        $timelog_array = array(
            'status' => $current_status,
            'created_on' => date('Y-m-d H:m:s'),
            'user_id' => $this->session->userdata('user_id'),
            'warehouse_id' => $this->session->userdata('warehouse_id'),
        );

        $notification_array['from_role'] = $this->session->userdata('group_id');
        $notification_array['insert_array'] = array(
            'type' => 'Chef ' . $current_status . ' Status',
            'table_id' => 0,
            'user_id' => $this->session->userdata('user_id'),
            'role_id' => WAITER,
            'warehouse_id' => $this->session->userdata('warehouse_id'),
            'created_on' => date('Y-m-d H:m:s'),
            'is_read' => 0,
        );

        $notification_array['customer_role'] = CUSTOMER;
        $notification_array['customer_type'] = 'Chef ' . $current_status . ' Status';

        $result = $this->pos_model->updateKitchenstatus($notification_array, $status, $order_id, $order_item_id, $current_status, $this->session->userdata('user_id'), $timelog_array);

        if ($current_status == 'Closed') {
            $orders = $this->pos_model->getTableOrderCount($order_id);
        }
        if ($result == true) {
            $msg = 'success';
        } else {
            $msg = 'error';

        }
        $this->sma->send_json(array('status' => $msg));

    }

    public function update_order_item_status($status = null, $order_item_id = null, $split_id = null)
    {

        $status = $this->input->get('status');
        $order_item_id = $this->input->get('order_item_id');
        $split_id = $this->input->get('split_id');

        if ($status == 'Ready') {
            $current_status = 'Served';
        } elseif ($status == 'Served') {
            $current_status = 'Closed';
        } else {
            $current_status = 'Ready';
        }
        $item_id = explode(',', $order_item_id);
        $customer_id = $this->site->getOrderItemCustomer($item_id[0]);
        $notification_array['customer_role'] = CUSTOMER;
        $notification_array['customer_msg'] = 'The item has been ' . $current_status . ' to waiter';
        $notification_array['customer_type'] = 'Waiter ' . $current_status . ' Status';
        $notification_array['customer_id'] = $customer_id;
        $notification_array['from_role'] = $this->session->userdata('group_id');
        $notification_array['insert_array'] = array(
            'type' => 'Waiter ' . $current_status . ' Status',
            'user_id' => $this->session->userdata('user_id'),
            'role_id' => KITCHEN,
            'warehouse_id' => $this->session->userdata('warehouse_id'),
            'created_on' => date('Y-m-d H:m:s'),
            'is_read' => 0,
        );

        $notification_array['customer_role'] = CUSTOMER;
        $notification_array['customer_type'] = 'Waiter ' . $current_status . ' Status';
        $timelog_array = array(
            'status' => $current_status,
            'created_on' => date('Y-m-d H:m:s'),
            'user_id' => $this->session->userdata('user_id'),
            'warehouse_id' => $this->session->userdata('warehouse_id'),
        );

        $result = $this->pos_model->updateOrderstatus($status, $order_item_id, $current_status, $this->session->userdata('user_id'), $notification_array, $timelog_array);
        $split = $this->pos_model->getTableSplitCount($split_id);
        if ($result == true) {
            $msg = 'success';
            $status = $current_status;

        } else {
            $msg = 'error';
            $status = 'error';
        }
        $this->sma->send_json(array('msg' => $msg, 'status' => $status));
        /*$this->sma->send_json(array('status' => $msg));*/

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
        $result = $this->pos_model->CancelOrdersItem($notification_array, $cancel_remarks, $order_item_id, $this->session->userdata('user_id'), $split_id, $timelog_array, $cancelQty, $cancel_type);
        if ($result == true) {
            $msg = 'success';

        } else {
            $msg = 'error';
            $status = 'error';
        }
        $this->sma->send_json(array('msg' => $msg, 'status' => $msg));
        /*$this->sma->send_json(array('status' => $msg));*/

    }

    public function sale_item_qty_adjustment($order_item_id = null, $action = null, $split_id = null)
    {
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

    public function cancel_all_order_items($cancel_remarks = null, $split_table_id = null)
    {

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

    public function cancel_all_order_items_03022019()
    {
        $cancel_remarks = $this->input->get('cancel_remarks');
        $cancelQty = 'all';
        $table_id = $this->input->get('table_id');
        $cancel_type = 'all-order-cancel';
        if (!empty($table_id)) {
            $data = $this->pos_model->getAllTablesorder($table_id);
            //echo '<pre>';print_R($data);exit;
            foreach ($data as $k => $split) {
                foreach ($split->split_order as $kk => $split_order) {
                    $split_id = $split_order->split_id;
                    foreach ($split_order->order as $ok => $order) {
                        foreach ($order->item as $ik => $item) {
                            $item_data = $item;
                            $order_item_id = $item_data->id;
                            if ($item_data->item_status == "Cancel") {
                                continue;
                            }

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

                            $result = $this->pos_model->CancelOrdersItem($notification_array, $cancel_remarks, $order_item_id, $this->session->userdata('user_id'), $split_id, $timelog_array, $cancelQty, $cancel_type);

                        }
                    }
                }

            }

        }

        if ($result == true) {
            $msg = 'success';

        } else {
            $msg = 'error';
            $status = 'error';
        }
        $this->sma->send_json(array('msg' => $msg, 'status' => $msg));
        /*$this->sma->send_json(array('status' => $msg));*/

    }
    public function cancel_all_order_items_bySplitID()
    {
        $cancel_remarks = $this->input->get('cancel_remarks');
        $cancelQty = 'all';
        $split_id = $this->input->get('split_id');
        $cancel_type = 'all-order-cancel';
        if (!empty($split_id)) {
            $data = $this->pos_model->getAllorders_splitID($split_id);
            //echo '<pre>';print_R($data);exit;
            foreach ($data as $k => $split) {

                foreach ($split->order as $ok => $order) {
                    foreach ($order->item as $ik => $item) {
                        $item_data = $item;
                        $order_item_id = $item_data->id;
                        if ($item_data->item_status == "Cancel") {
                            continue;
                        }

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

                        $result = $this->pos_model->CancelOrdersItem($notification_array, $cancel_remarks, $order_item_id, $this->session->userdata('user_id'), $split_id, $timelog_array, $cancelQty, $cancel_type);

                    }
                }

            }

        }

        if ($result == true) {
            $msg = 'success';

        } else {
            $msg = 'error';
            $status = 'error';
        }
        $this->sma->send_json(array('msg' => $msg, 'status' => $msg));
        /*$this->sma->send_json(array('status' => $msg));*/

    }
    public function cancel_sale($cancel_remarks = null, $sale_id = null)
    {
        $cancel_remarks = $this->input->get('cancel_remarks');
        $sale_id = $this->input->get('sale_id');

        $notification_array['from_role'] = $this->session->userdata('group_id');
        $notification_array['insert_array'] = array(
            'user_id' => $this->session->userdata('user_id'),
            'warehouse_id' => $this->session->userdata('warehouse_id'),
            'created_on' => date('Y-m-d H:m:s'),
            'is_read' => 0,
        );

        $result = $this->pos_model->CancelSale($cancel_remarks, $sale_id, $this->session->userdata('user_id'), $notification_array);
        if ($result == true) {
            $msg = 'success';

        } else {
            $msg = 'error';
            $status = 'error';
        }
        $this->sma->send_json(array('msg' => $msg, 'status' => $msg));
    }
    public function consolidcancel_sale($cancel_remarks = null, $split_id = null)
    {
        $cancel_remarks = $this->input->get('cancel_remarks');
        $split_id = $this->input->get('split_id');

        $notification_array['from_role'] = $this->session->userdata('group_id');
        $notification_array['insert_array'] = array(
            'user_id' => $this->session->userdata('user_id'),
            'warehouse_id' => $this->session->userdata('warehouse_id'),
            'created_on' => date('Y-m-d H:m:s'),
            'is_read' => 0,
        );

        $result = $this->pos_model->BBQCancelSale($cancel_remarks, $split_id, $this->session->userdata('user_id'), $notification_array);
        if ($result == true) {
            $msg = 'success';

        } else {
            $msg = 'error';
            $status = 'error';
        }
        $this->sma->send_json(array('msg' => $msg, 'status' => $msg));
    }
    public function order_item_kitchen()
    {
        $this->sma->checkPermissions('index');

        $user = $this->site->getUser();
        $this->data['warehouses'] = null;
        $this->data['warehouse_id'] = $this->session->userdata('warehouse_id');
        $this->data['warehouse'] = $this->session->userdata('warehouse_id') ? $this->site->getWarehouseByID($this->session->userdata('warehouse_id')) : null;
        //$this->data['tables'] = $this->pos_model->getAllTablesorder();
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('pos'), 'page' => lang('pos')), array('link' => '#', 'page' => lang('pos_sales')));
        $meta = array('page_title' => lang('pos_sales'), 'bc' => $bc);

        $this->load->view($this->theme . 'pos/orderkitchen_item', $this->data);
    }

    public function view_bill()
    {
        $this->sma->checkPermissions('index');
        $this->data['tax_rates'] = $this->site->getAllTaxRates();
        $this->load->view($this->theme . 'pos/view_bill', $this->data);
    }

    public function customer_bill()
    {
        $this->sma->checkPermissions('index');
        $this->data['tax_rates'] = $this->site->getAllTaxRates();
        $this->load->view($this->theme . 'pos/customer_bill', $this->data);
    }

    public function stripe_balance()
    {
        if (!$this->Owner) {
            return false;
        }
        $this->load->admin_model('stripe_payments');

        return $this->stripe_payments->get_balance();
    }

    public function paypal_balance()
    {
        if (!$this->Owner) {
            return false;
        }
        $this->load->admin_model('paypal_payments');

        return $this->paypal_payments->get_balance();
    }

    public function registers()
    {
        $this->sma->checkPermissions();

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['registers'] = $this->pos_model->getOpenRegisters();
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('pos'), 'page' => lang('pos')), array('link' => '#', 'page' => lang('open_registers')));
        $meta = array('page_title' => lang('open_registers'), 'bc' => $bc);
        $this->page_construct('pos/registers', $meta, $this->data);
    }

    public function open_register(){
        $this->sma->checkPermissions('index');
        $this->form_validation->set_rules('cash_in_hand', lang("cash_in_hand"), 'trim|required|numeric');
        if ($this->form_validation->run() == true) {
            $data = array(
                'date' => date('Y-m-d H:i:s'),
                'cash_in_hand' => $this->input->post('cash_in_hand'),
                'user_id' => $this->session->userdata('user_id'),
                'status' => 'open',
            );
        }
        if ($this->form_validation->run() == true && $this->pos_model->openRegister($data)) {
            $this->session->set_flashdata('message', lang("welcome_to_pos"));
            admin_redirect("pos");
        } else {
            $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('open_register')));
            $meta = array('page_title' => lang('open_register'), 'bc' => $bc);
            $this->page_construct('pos/open_register', $meta, $this->data);
        }
    }

    public function user_open_register($cash_in_hand = null){
        $cash_in_hand = $this->input->post('cash_in_hand');
        if ($cash_in_hand) {
            $data = array(
                'date' => date('Y-m-d H:i:s'),
                'cash_in_hand' => $this->input->post('cash_in_hand'),
                'user_id' => $this->session->userdata('user_id'),
                'status' => 'open',
            );
        }
        if ($this->pos_model->openRegister($data)) {
            $msg = 'success';

        } else {
            $msg = 'error';
        }
		$this->sma->send_json(array('msg' => $msg));
    }
    public function close_register($user_id = null){
        $this->sma->checkPermissions('index');
        if (!$this->Owner && !$this->Admin) {
            $user_id = $this->session->userdata('user_id');
        }
        $this->form_validation->set_rules('total_cash', lang("total_cash"), 'trim|required|numeric');
        $this->form_validation->set_rules('total_cheques', lang("total_cheques"), 'trim|required|numeric');
        $this->form_validation->set_rules('total_cc_slips', lang("total_cc_slips"), 'trim|required|numeric');
        if ($this->form_validation->run() == true) {
            if ($this->Owner || $this->Admin) {
                $user_register = $user_id ? $this->pos_model->registerData($user_id) : null;
                $rid = $user_register ? $user_register->id : $this->session->userdata('register_id');
                $user_id = $user_register ? $user_register->user_id : $this->session->userdata('user_id');
            } else {
                $rid = $this->session->userdata('register_id');
                $user_id = $this->session->userdata('user_id');
            }
            $data = array(
                'closed_at' => date('Y-m-d H:i:s'),
                'total_cash' => $this->input->post('total_cash'),
                'total_cheques' => $this->input->post('total_cheques'),
                'total_cc_slips' => $this->input->post('total_cc_slips'),
                'total_cash_submitted' => $this->input->post('total_cash_submitted'),
                'total_cheques_submitted' => $this->input->post('total_cheques_submitted'),
                'total_cc_slips_submitted' => $this->input->post('total_cc_slips_submitted'),
                'note' => $this->input->post('note'),
                'status' => 'close',
                'transfer_opened_bills' => $this->input->post('transfer_opened_bills'),
                'closed_by' => $this->session->userdata('user_id'),
            );
        } elseif ($this->input->post('close_register')) {
            $this->session->set_flashdata('error', (validation_errors() ? validation_errors() : $this->session->flashdata('error')));
            admin_redirect("pos");
        }

        if ($this->form_validation->run() == true && $this->pos_model->closeRegister($rid, $user_id, $data)) {
            $this->session->set_flashdata('message', lang("register_closed"));
            admin_redirect("welcome");
        } else {
            if ($this->Owner || $this->Admin) {
                $user_register = $user_id ? $this->pos_model->registerData($user_id) : null;
                $register_open_time = $user_register ? $user_register->date : null;
                $this->data['cash_in_hand'] = $user_register ? $user_register->cash_in_hand : null;
                $this->data['register_open_time'] = $user_register ? $register_open_time : null;
            } else {
                $register_open_time = $this->session->userdata('register_open_time');
                $this->data['cash_in_hand'] = null;
                $this->data['register_open_time'] = null;
            }
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['ccsales'] = $this->pos_model->getRegisterCCSales($register_open_time, $user_id);
            $this->data['cashsales'] = $this->pos_model->getRegisterCashSales($register_open_time, $user_id);
            $this->data['chsales'] = $this->pos_model->getRegisterChSales($register_open_time, $user_id);
            $this->data['gcsales'] = $this->pos_model->getRegisterGCSales($register_open_time);
            $this->data['pppsales'] = $this->pos_model->getRegisterPPPSales($register_open_time, $user_id);
            $this->data['stripesales'] = $this->pos_model->getRegisterStripeSales($register_open_time, $user_id);
            $this->data['authorizesales'] = $this->pos_model->getRegisterAuthorizeSales($register_open_time, $user_id);
            $this->data['totalsales'] = $this->pos_model->getRegisterSales($register_open_time, $user_id);
            $this->data['refunds'] = $this->pos_model->getRegisterRefunds($register_open_time, $user_id);
            $this->data['cashrefunds'] = $this->pos_model->getRegisterCashRefunds($register_open_time, $user_id);
            $this->data['expenses'] = $this->pos_model->getRegisterExpenses($register_open_time, $user_id);
            $this->data['users'] = $this->pos_model->getUsers($user_id);
            $this->data['suspended_bills'] = $this->pos_model->getSuspendedsales($user_id);
            $this->data['user_id'] = $user_id;
            $this->data['modal_js'] = $this->site->modal_js();
            /* echo "<pre>";
            print_r($this->data);die;*/
            $this->load->view($this->theme . 'pos/close_register', $this->data);
        }
    }

    public function getrecipeDataByCode($code = null, $warehouse_id = null)
    {
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
                $row->buy_quantity = $buy->buy_quantity;
                $row->get_quantity = $buy->get_quantity;
                $total_quantity = $x_quantity % $y_quantity;
                $x_quantity = ($x_quantity - $total_quantity) / $y_quantity;
                $total_get_quantity = $x_quantity * $b_quantity;
                $row->total_get_quantity = $total_get_quantity;
                $row->free_recipe = $buy->free_recipe;
            } else {
                $row->buy_id = 0;
                $row->get_item = 0;
                $row->buy_quantity = 0;
                $row->get_quantity = 0;
                $row->total_get_quantity = 0;
                $row->free_recipe = '';
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

    public function getrecipeVarientDataByCode($code = null, $warehouse_id = null)
    {
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
            } else {
                $row->buy_id = 0;
                $row->get_item = 0;
                $row->buy_quantity = 0;
                $row->get_quantity = 0;
                $row->total_get_quantity = 0;
                $row->free_recipe = '';
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
    public function ajaxrecipe($category_id = null, $warehouse_id = null, $subcategory_id = null, $brand_id = null, $order_type = null)
    {
        // var_dump($order_type);die;

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

        // if ($this->input->get('per_page') == 'n') {
        if ($this->input->get('per_page') == 'n' || $this->input->get('per_page') == '0') {
            $page = 0;
        } else {
            $page = $this->input->get('per_page');
        }
        if ($this->input->get('order_type')) {
            $order_type = $this->input->get('order_type');
        }

        // $order_type = $this->input->get('order_type') ? $this->input->get('order_type') : 0;
        $this->load->library("pagination");
// var_dump($order_type);die;
        $config = array();
        $config["base_url"] = base_url() . "pos/ajaxrecipe";
        if ($this->pos_settings->sales_item_in_pos == 1) {
            $config["total_rows"] = $this->pos_model->recipe_count($category_id, $warehouse_id, $subcategory_id, $brand_id);
        } else {
            $config["total_rows"] = $this->pos_model->recipe_count_withdays($category_id, $warehouse_id, $subcategory_id, $brand_id, $order_type);
        }
        $config["per_page"] = $this->pos_settings->pro_limit;
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
        $prods = '<div>';
        if (!empty($recipe)) {
            foreach ($recipe as $recipe) {
                $count = $recipe->id;
                $buy = $this->site->checkBuyget($recipe->id);
                $default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
                $default_currency_rate = $default_currency_data->rate;
                $default_currency_symbol = $default_currency_data->symbol;
                if (!empty($buy)) {
                    if ($buy->buy_method == 'buy_x_get_x') {
                        $buyvalue = "<div class='rippon'><div class='offer_list'><span>Buy " . $buy->buy_quantity . "  Get " . $buy->get_quantity . " " . $buy->free_recipe . " </span></div></div>";
                    } elseif ($buy->buy_method == 'buy_x_get_y') {
                        $buyvalue = "<div class='rippon'><div class='offer_list'><span>Buy " . $buy->buy_quantity . "  Get " . $buy->free_recipe . " ( " . $buy->get_quantity . ")</span></div></div>";
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
                    if ($this->pos_settings->variant_display_option == 0) {
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
                            $vari .= '<button data-id="' . $varient->variant_id . '" id="recipe-' . $category_id . $count . '" type="button" value="' . $recipe->code . '" title="" class="btn-default  recipe-varient pos-tip" data-container="body" data-original-title="' . $varient_name . '" tabindex="-1">';
                            if (strlen($varient_name) < 15) {
                                $vari .= "<span class='name_strong'>" . $varient_name . "</span>";
                            } else {
                                $vari .= '<marquee class="name_strong" behavior="alternate" direction="left" scrollamount="1">
									&nbsp;&nbsp;' . $varient_name . '&nbsp;&nbsp;</marquee>';
                            }
                            $vari .= '<br>
								<span class="price_strong"> ' . $default_currency_symbol . $this->sma->formatDecimal($varient->price) . '</span> </button>';
                        }
                        $vari .= '</div>';
                    } else { //varaint list by Table for Kimmo client requriment
                        $vari = '<div class="variant-popup" style="display: none;">';
                        $vari .= '<table class="table table-bordered table-hover table-striped reports-table dataTable">';
                        $vari .= '<tr><td>' . lang('code') . '</td><td>' . lang('name') . '</td></tr>';
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
							//buy x get variant based 
						    $variant_offer=($varient->variant_id== $buy->buy_variant_id)?'offer_list':'';
                            $vari .= '<tr data-id="' . $varient->variant_id . '" id="recipe-' . $category_id . $count . '" code="' . $recipe->code . '" title="" class="btn-default  recipe-varient pos-tip" data-container="body" data-original-title="' . $varient_name . '" tabindex="-1">';
                            $vari .= "<td><span class='name_strong ".$variant_offer."'>" . $varient->variant_code . "</span></td>";
                            $vari .= '<td><span class="price_strong"> ' . $varient_name . '</span></td>';
                            $vari .= '</tr>';
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
                        $vari .= '</table>';
                        $vari .= '</div>';
                    }
                }
				//active mode for highlights non transaction recipe
				$activemode=($recipe->active ==2)?'style="background-color:#E1A87C !important"':'';
				$activemode_class=($recipe->active ==2)?'non_transaction':'';
                if ($this->pos_settings->sale_item_display == 0) {
                    $prods .= "<span><button ".$activemode." id=\"recipe-" . $category_id . $count . "\" type=\"button\" value='" . $recipe->id . "' title=\"" . $recipe->name . "\" class=\"btn-prni btn-" . $this->pos_settings->recipe_button_color . " " . $class . " recipe pos-tip ".$activemode_class."\" data-container=\"body\"><img src=\"" . base_url() . "assets/uploads/thumbs/" . $recipe->image . "\" alt=\"" . $recipe_name . "\" class='img-rounded ' />";
                } else {
                    $prods .= "<span><button ".$activemode." id=\"recipe-" . $category_id . $count . "\" type=\"button\" value='" . $recipe->id . "' title=\"" . $recipe->name . "\" class=\"btn-img btn-" . $this->pos_settings->recipe_button_color . " " . $class . " recipe pos-tip " .$activemode_class."\" data-container=\"body\">";
                }
                if (strlen($recipe->name) < 15) {
                    $prods .= "<span class='name_strong'>" . $recipe_name . "</span>";
                } else {
                    $prods .= "<marquee class='name_strong' behavior='alternate' direction='left' scrollamount='1'>&nbsp;&nbsp;" . $recipe_name . "&nbsp;&nbsp;</marquee>";
                }
                $prods .= "<br><span class='price_strong'> ";
                if ($recipe->price != 0) {
                    $prods .= $default_currency_symbol . "" . $this->sma->formatDecimal($recipe->price);
                }
                $prods .= "</span>" . $buyvalue . "";
                $prods .= "</button>";
                $prods .= $vari . '</span>';
                $pro++;
            }
        }
        $prods .= "</div>";
        // if ($this->input->get('per_page')) {
        if ($this->input->get('per_page') != null) {
            echo $prods;
        } else {
            return $prods;
        }
    }

    public function ajaxrecipebbq($category_id = null, $warehouse_id = null, $subcategory_id = null, $brand_id = null, $sales_type = null)
    {

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

        // if ($this->input->get('per_page') == 'n') {
        if ($this->input->get('per_page') == 'n' || $this->input->get('per_page') == '0') {

            $page = 0;
        } else {
            $page = $this->input->get('per_page');
        }

        $this->load->library("pagination");

        $config = array();
        $config["base_url"] = base_url() . "pos/ajaxrecipebbq";
        $config["total_rows"] = $this->pos_model->bbqrecipe_count($category_id, $warehouse_id, $subcategory_id, $brand_id);

        $config["per_page"] = $this->pos_settings->pro_limit;

        $config['prev_link'] = false;
        $config['next_link'] = false;
        $config['display_pages'] = false;
        $config['first_link'] = false;
        $config['last_link'] = false;

        $this->pagination->initialize($config);

        if ($this->pos_settings->sales_item_in_pos == 1) {
            $recipe = $this->pos_model->bbqfetch_recipe($category_id, $warehouse_id, $config["per_page"], $page, $subcategory_id, $brand_id);
        } else {
            $recipe = $this->pos_model->bbqfetch_recipe_withdays($category_id, $warehouse_id, $config["per_page"], $page, $subcategory_id, $brand_id, $sales_type);
        }

        $pro = 1;
        $prods = '<div>';
        if (!empty($recipe)) {
            foreach ($recipe as $recipe) {

                $count = $recipe->id;
                $buy = $this->site->checkBuyget($recipe->id);

                $default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
                $default_currency_rate = $default_currency_data->rate;
                $default_currency_symbol = $default_currency_data->symbol;

                if (!empty($buy)) {

                    if ($buy->buy_method == 'buy_x_get_x') {
                        $buyvalue = "<div class='rippon'><div class='offer_list'><span>Buy " . $buy->buy_quantity . "  Get " . $buy->get_quantity . " " . $buy->free_recipe . " </span></div></div>";
                    } elseif ($buy->buy_method == 'buy_x_get_y') {
                        $buyvalue = "<div class='rippon'><div class='offer_list'><span>Buy " . $buy->buy_quantity . "  Get " . $buy->free_recipe . " ( " . $buy->get_quantity . ")</span></div></div>";
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

                    $vari = '<div class="variant-popup" style="display: none;">';
                    foreach ($varients as $k => $varient) {
                        $vari .= '<button data-id="' . $varient->variant_id . '" id="recipe-' . $category_id . $count . '" type="button" value="' . $recipe->id . '" title="" class="btn-default  recipe-varient pos-tip" data-container="body" data-original-title="' . $varient->name . '" tabindex="-1">';
                        if (strlen($varient->name) < 15) {
                            $vari .= "<span class='name_strong'>" . $varient->name . "</span>";
                        } else {
                            $vari .= '<marquee class="name_strong" behavior="alternate" direction="left" scrollamount="1">
								&nbsp;&nbsp;' . $varient->name . '&nbsp;&nbsp;</marquee>';
                        }
                        $vari .= '<br>
							<span class="price_strong"> ' . $default_currency_symbol . $this->sma->formatDecimal($varient->price) . '</span> </button>';
                    }
                    $vari .= '</div>';

                }
				$activemode=($recipe->active ==2)?'style="background-color:#E1A87C !important"':'';
				$activemode_class=($recipe->active ==2)?' non_transaction':'';

                $prods .= "<span><button id=\"recipe-" . $category_id . $count . "\" type=\"button\" value='" . $recipe->id . "' title=\"" . $recipe->name . "\" class=\"btn-prni btn-" . $this->pos_settings->recipe_button_color . "" . $class . "". $activemode_class." recipe pos-tip\" data-container=\"body\"><img src=\"" . base_url() . "assets/uploads/thumbs/" . $recipe->image . "\" alt=\"" . $recipe_name . "\" class='img-rounded' />";

                if (strlen($recipe->name) < 15) {

                    $prods .= "<span class='name_strong'>" . $recipe_name . "</span>";
                } else {
                    $prods .= "<marquee class='name_strong' behavior='alternate' direction='left' scrollamount='1'>&nbsp;&nbsp;" . $recipe_name . "&nbsp;&nbsp;</marquee>";
                }

                // $prods .=  "<br><span class='price_strong'> ".$default_currency_symbol ."" . $this->sma->formatDecimal($recipe->price). "</span>".$buyvalue." </button>";
                $prods .= $vari . '</span>';
                $pro++;
            }
        }
        $prods .= "</div>";

        if ($this->input->get('per_page')) {
            echo $prods;
        } else {
            return $prods;
        }
    }

    public function getSalesItems($sale_id = null)
    {
        $sale_id = $this->input->get('sale_id');

        $sales_item = $this->pos_model->getBillCashierPrintdata($sale_id);
        $sales = $this->pos_model->getSalesData($sale_id);
        /*echo "<pre>";
        print_r($sales);exit;
        echo "</pre>";*/
        /*$item = '';
        if (!empty($sales_item)) {

        $html ='<tbody class="ui-sortable">';
        foreach($sales_item as $item){
        $html .='<tr><td>'.$item->recipe_name.'</td><td>'.$item->unit_price.'</td><td>'.$item->quantity.'</td><td>'.$item->subtotal.'</td></tr>';
        }
        $html .='</tbody>';

        $html .='</table>
        <div style="clear:both;"></div>
        </div>';
        $item = $html;
        }*/
        /*$table = $this->pos_model->checkTables($table_id,$order_type);*/
        $this->sma->send_json(array('sales_item' => $sales_item, 'sales' => $sales));
        /*$this->sma->send_json($sales_item);*/

        /* $this->sma->send_json(array('sales_item' => $item));*/
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
                    $scats .= "<button id=\"subcategory-" . $category->id . "\" type=\"button\" value='" . $category->id . "' class=\"btn-prni subcategory slide\" ><img src=\"assets/uploads/thumbs/" . ($category->image ? $category->image : 'no_image.png') . "\" class='img-rounded' />";
                } else {
                    $scats .= "<button id=\"subcategory-" . $category->id . "\" type=\"button\" value='" . $category->id . "' class=\"btn-img subcategory slide\" >";
                }

                // $scats .= "<button id=\"subcategory-" . $category->id . "\" type=\"button\" value='" . $category->id . "' class=\"btn-prni subcategory\" ><img src=\"" . base_url() ."assets/uploads/thumbs/" . ($category->image ? $category->image : 'no_image.png') . "\" class='img-rounded img-thumbnail' />";

                if (strlen($subcategory_name) < 15) {

                    $scats .= "<span class='name_strong'>" . $subcategory_name . "</span>";
                } else {
                    $scats .= "<marquee class='sub_strong' behavior='alternate' direction='left' scrollamount='1'>&nbsp;&nbsp;&nbsp;&nbsp;" . $subcategory_name . "&nbsp;&nbsp;&nbsp;&nbsp;</marquee>";
                }
                $scats .= "</button>";

            }
        }
        if ($recipe_standard == 1) {

            $recipe = $this->ajaxrecipe($category_id, $this->session->userdata('warehouse_id'), $order_type);
            if (!($tcp = $this->pos_model->recipe_count($category_id, $this->session->userdata('warehouse_id')))) {
                $tcp = 0;
            }
        } else {
            $recipe = $this->ajaxrecipebbq($category_id, $this->session->userdata('warehouse_id'), $subcategory_id = null, $brand_id = null, $sales_type);
            if (!($tcp = $this->pos_model->bbqrecipe_count($category_id, $this->session->userdata('warehouse_id')))) {
                $tcp = 0;
            }
        }

        $this->sma->send_json(array('recipe' => $recipe, 'subcategories' => $scats, 'tcp' => $tcp));
    }

    public function ajaxbranddata($brand_id = null)
    {
        $this->sma->checkPermissions('index');
        if ($this->input->get('brand_id')) {
            $brand_id = $this->input->get('brand_id');
        }

        $recipe = $this->ajaxrecipe(false, $this->session->userdata('warehouse_id'), $brand_id);

        if (!($tcp = $this->pos_model->recipe_count(false, $this->session->userdata('warehouse_id'), $brand_id))) {
            $tcp = 0;
        }

        $this->sma->send_json(array('recipe' => $recipe, 'tcp' => $tcp));
    }

    /* ------------------------------------------------------------------------------------ */

    public function view($sale_id = null, $modal = null)
    {
        $this->sma->checkPermissions('index');
        if ($this->input->get('id')) {
            $sale_id = $this->input->get('id');
        }
        $this->load->helper('pos');
        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['message'] = $this->session->flashdata('message');
        $inv = $this->pos_model->getInvoiceByID($sale_id);
        if (!$this->session->userdata('view_right')) {
            $this->sma->view_rights($inv->created_by, true);
        }
        $this->data['rows'] = $this->pos_model->getAllInvoiceItems($sale_id);
        $biller_id = $inv->biller_id;
        $customer_id = $inv->customer_id;
        $this->data['biller'] = $this->pos_model->getCompanyByID($biller_id);
        $this->data['customer'] = $this->pos_model->getCompanyByID($customer_id);
        $this->data['payments'] = $this->pos_model->getInvoicePayments($sale_id);
        $this->data['pos'] = $this->pos_model->getSetting();
        $this->data['barcode'] = $this->barcode($inv->reference_no, 'code128', 30);
        $this->data['return_sale'] = $inv->return_id ? $this->pos_model->getInvoiceByID($inv->return_id) : null;
        $this->data['return_rows'] = $inv->return_id ? $this->pos_model->getAllInvoiceItems($inv->return_id) : null;
        $this->data['return_payments'] = $this->data['return_sale'] ? $this->pos_model->getInvoicePayments($this->data['return_sale']->id) : null;
        $this->data['inv'] = $inv;
        $this->data['sid'] = $sale_id;
        $this->data['modal'] = $modal;
        $this->data['created_by'] = $this->site->getUser($inv->created_by);
        $this->data['printer'] = $this->pos_model->getPrinterByID($this->pos_settings->printer);
        $this->data['page_title'] = $this->lang->line("invoice");
        $this->load->view($this->theme . 'pos/view', $this->data);
    }

    public function register_details()
    {
        $this->sma->checkPermissions('index');
        $register_open_time = $this->session->userdata('register_open_time');
        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['ccsales'] = $this->pos_model->getRegisterCCSales($register_open_time);
        $this->data['cashsales'] = $this->pos_model->getRegisterCashSales($register_open_time);
        $this->data['chsales'] = $this->pos_model->getRegisterChSales($register_open_time);
        $this->data['gcsales'] = $this->pos_model->getRegisterGCSales($register_open_time);
        $this->data['pppsales'] = $this->pos_model->getRegisterPPPSales($register_open_time);
        $this->data['stripesales'] = $this->pos_model->getRegisterStripeSales($register_open_time);
        $this->data['authorizesales'] = $this->pos_model->getRegisterAuthorizeSales($register_open_time);
        $this->data['totalsales'] = $this->pos_model->getRegisterSales($register_open_time);
        $this->data['refunds'] = $this->pos_model->getRegisterRefunds($register_open_time);
        $this->data['expenses'] = $this->pos_model->getRegisterExpenses($register_open_time);
        $this->load->view($this->theme . 'pos/register_details', $this->data);
    }

    public function today_sale()
    {
        if (!$this->Owner && !$this->Admin) {
            $this->session->set_flashdata('error', lang('access_denied'));
            $this->sma->md();
        }

        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['ccsales'] = $this->pos_model->getTodayCCSales();
        $this->data['cashsales'] = $this->pos_model->getTodayCashSales();
        $this->data['chsales'] = $this->pos_model->getTodayChSales();
        $this->data['pppsales'] = $this->pos_model->getTodayPPPSales();
        $this->data['stripesales'] = $this->pos_model->getTodayStripeSales();
        $this->data['authorizesales'] = $this->pos_model->getTodayAuthorizeSales();
        $this->data['totalsales'] = $this->pos_model->getTodaySales();
        $this->data['refunds'] = $this->pos_model->getTodayRefunds();
        $this->data['expenses'] = $this->pos_model->getTodayExpenses();
        $this->load->view($this->theme . 'pos/today_sale', $this->data);
    }

    public function check_pin()
    {
        $pin = $this->input->post('pw', true);
        if ($pin == $this->pos_pin) {
            $this->sma->send_json(array('res' => 1));
        }
        $this->sma->send_json(array('res' => 0));
    }

    public function barcode($text = null, $bcs = 'code128', $height = 50)
    {
        return admin_url('recipe/gen_barcode/' . $text . '/' . $bcs . '/' . $height);
    }

    public function settings()
    {
        if (!$this->Owner) {
            $this->session->set_flashdata('error', lang('access_denied'));
            admin_redirect("welcome");
        }
        $this->form_validation->set_message('is_natural_no_zero', $this->lang->line('no_zero_required'));
        $this->form_validation->set_rules('pro_limit', $this->lang->line('pro_limit'), 'required|is_natural_no_zero');
        $this->form_validation->set_rules('pin_code', $this->lang->line('delete_code'), 'numeric');
        $this->form_validation->set_rules('category', $this->lang->line('default_category'), 'required|is_natural_no_zero');
        $this->form_validation->set_rules('customer', $this->lang->line('default_customer'), 'required|is_natural_no_zero');
        $this->form_validation->set_rules('biller', $this->lang->line('default_biller'), 'required|is_natural_no_zero');
        $this->form_validation->set_rules('tax', $this->lang->line('default_tax'), 'required|is_natural_no_zero');
        if ($this->form_validation->run() == true) {

            $data = array(
                'pro_limit' => $this->input->post('pro_limit'),
                'pin_code' => $this->input->post('pin_code') ? $this->input->post('pin_code') : null,
                'default_category' => $this->input->post('category'),
                'default_customer' => $this->input->post('customer'),
                'default_biller' => $this->input->post('biller'),
                'default_billgenerator' => $this->input->post('default_billgenerator'),
                'table_change' => $this->input->post('table_change'),
                'order_screen_font_size' => $this->input->post('order_screen_font_size'),
                'font_family' => $this->input->post('font_family'),

                'table_display_option' => $this->input->post('table_display_option'),
                'pos_types_display_option' => $this->input->post('pos_types_display_option'),
                'variant_display_option' => $this->input->post('variant_display_option'),
                'table_available_color' => $this->input->post('table_available_color'),
                'table_kitchen_color' => $this->input->post('table_kitchen_color'),
                'table_pending_color' => $this->input->post('table_pending_color'),

                'table_size' => $this->input->post('table_size'),
                'merge_bill' => $this->input->post('merge_bill'),
                'default_tax' => $this->input->post('tax'),
                'tax_type' => $this->input->post('tax_type'),
                'display_time' => $this->input->post('display_time'),
                'receipt_printer' => $this->input->post('receipt_printer'),
                'cash_drawer_codes' => $this->input->post('cash_drawer_codes'),
                'cf_title1' => $this->input->post('cf_title1'),
                'cf_title2' => $this->input->post('cf_title2'),
                'cf_value1' => $this->input->post('cf_value1'),
                'cf_value2' => $this->input->post('cf_value2'),
                'focus_add_item' => $this->input->post('focus_add_item'),
                'add_manual_recipe' => $this->input->post('add_manual_recipe'),
                'customer_selection' => $this->input->post('customer_selection'),
                'add_customer' => $this->input->post('add_customer'),
                'toggle_category_slider' => $this->input->post('toggle_category_slider'),
                'toggle_subcategory_slider' => $this->input->post('toggle_subcategory_slider'),
                'toggle_brands_slider' => $this->input->post('toggle_brands_slider'),
                'cancel_sale' => $this->input->post('cancel_sale'),
                'suspend_sale' => $this->input->post('suspend_sale'),
                'print_items_list' => $this->input->post('print_items_list'),
                'finalize_sale' => $this->input->post('finalize_sale'),
                'today_sale' => $this->input->post('today_sale'),
                'open_hold_bills' => $this->input->post('open_hold_bills'),
                'close_register' => $this->input->post('close_register'),
                'tooltips' => $this->input->post('tooltips'),
                'keyboard' => $this->input->post('keyboard'),
                'pos_printers' => $this->input->post('pos_printers'),
                'java_applet' => $this->input->post('enable_java_applet'),
                'recipe_button_color' => $this->input->post('recipe_button_color'),
                'paypal_pro' => $this->input->post('paypal_pro'),
                'stripe' => $this->input->post('stripe'),
                'authorize' => $this->input->post('authorize'),
                'rounding' => $this->input->post('rounding'),
                'item_order' => $this->input->post('item_order'),
                'after_sale_page' => $this->input->post('after_sale_page'),
                'printer' => $this->input->post('receipt_printer'),
                'order_printers' => json_encode($this->input->post('order_printers')),
                'auto_print' => $this->input->post('auto_print'),
                'remote_printing' => DEMO ? 1 : $this->input->post('remote_printing'),
                'print_option' => $this->input->post('print_option'),
                'print_local_language' => $this->input->post('print_local_language'),
                'customer_details' => $this->input->post('customer_details'),
                'local_printers' => $this->input->post('local_printers'),
                'display_tax' => $this->input->post('display_tax'),
                'display_tax_amt' => $this->input->post('display_tax_amt'),
                'open_sale_register' => $this->input->post('open_sale_register'),
                'taxation_report_settings' => $this->input->post('taxation_report_settings'),
                'taxation_all' => $this->input->post('taxation_all'),
                'taxation_include' => $this->input->post('taxation_include'),
                'taxation_exclude' => $this->input->post('taxation_exclude'),
                'taxation_bill_start_from' => $this->input->post('taxation_bill_start_from'),
                'taxation_bill_prefix' => $this->input->post('taxation_bill_prefix'),
                'bill_series_settings' => $this->input->post('bill_series_settings'),
                'consolidated_kot_print' => $this->input->post('consolidated_kot_print'),
                'consolidated_kot_print_option' => $this->input->post('consolidated_kot_print_option'),
                'kot_print_option' => $this->input->post('kot_print_option'),
                'order_no_display' => $this->input->post('order_no_display'),
                'reprint_from_last_day' => $this->input->post('reprint_from_last_day'),
                'category_display' => $this->input->post('category_display'),
                'subcategory_display' => $this->input->post('subcategory_display'),
                'sale_item_display' => $this->input->post('sale_item_display'),
                'birthday_enable' => $this->input->post('birthday_enable'),
                'birthday_discount' => $this->input->post('birthday_discount'),
                'birthday_enable_bbq' => $this->input->post('birthday_enable_bbq'),
                'birthday_discount_for_bbq' => $this->input->post('birthday_discount_for_bbq'),
                'qsr_kot_print' => $this->input->post('qsr_kot_print'),
                'kot_print_logo' => $this->input->post('kot_print_logo'),
                'pre_printed_format' => $this->input->post('pre_printed_format'),
                'pre_printed_header' => $this->input->post('pre_printed_header'),
                'print_footer_space' => $this->input->post('print_footer_space'),
                'order_item_customization' => $this->input->post('order_item_customization'),
                'item_addon' => $this->input->post('item_addon'),
                'item_comment' => $this->input->post('item_comment'),
                'item_comment_price_option' => $this->input->post('item_comment_price_option'),
                'loyalty_option' => $this->input->post('loyalty_option'),
                'sales_item_in_pos' => $this->input->post('sales_item_in_pos'),
                'categories_list_by' => $this->input->post('categories_list_by'),
				
				 'vat_number_print'       => $this->input->post('vat_number_print'),
				 'vat_print'              => json_encode($this->input->post('vat_print')),
				 'number_of_people_print' => $this->input->post('number_of_people_print'),
				 'nop_print'              => json_encode($this->input->post('nop_print')),
				 'floor_area_print'       => $this->input->post('floor_area_print'),
				 'floor_print'            => json_encode($this->input->post('floor_print')),
				 'customer_name'          => $this->input->post('customer_name'),
			     'cus_print'              => json_encode($this->input->post('cus_print')),
				
				
				
                'consolid_kot_print_logo' => $this->input->post('consolid_kot_print_logo'),
                'kot_order_no_print_option' => $this->input->post('kot_order_no_print_option'),
                'reprint_bill_caption' => $this->input->post('reprint_bill_caption'),
                'total_covers' => $this->input->post('total_covers'),
                'manual_item_discount_display_option' => $this->input->post('manual_item_discount_display_option'),
                'manual_and_customer_discount_consolid_percentage_display_option' => $this->input->post('manual_and_customer_discount_consolid_percentage_display_option'),
                'discount_note_display_option' => $this->input->post('discount_note_display_option'),
                'discount_popup_screen_in_payment' => $this->input->post('discount_popup_screen_in_payment'),
                'discount_popup_screen_in_rough_payment' => $this->input->post('discount_popup_screen_in_rough_payment'),
                'discount_popup_screen_in_bill_print' => $this->input->post('discount_popup_screen_in_bill_print'),
                'bill_print_format' => $this->input->post('bill_print_format'),
                'tax_caption' => $this->input->post('tax_caption'),
                'consolidated_reprint_print' => $this->input->post('consolidated_reprint_print'),
                'billgeneration_screen' => $this->input->post('billgeneration_screen'),
                'customer_discount_editable' => $this->input->post('customer_discount_editable'),
                'kot_enable_disable' => $this->input->post('kot_enable_disable'),
                'kot_font_size' => $this->input->post('kot_font_size'),
                'kot_print_lang_option' => $this->input->post('kot_print_lang_option'),
                'default_service_charge' => $this->input->post('default_service_charge'),
                'service_charge_option' => $this->input->post('service_charge_option'),
				 'item_search' => !empty($this->input->post('item_search'))?$this->input->post('item_search'):0,
            );
            $payment_config = array(
                'APIUsername' => $this->input->post('APIUsername'),
                'APIPassword' => $this->input->post('APIPassword'),
                'APISignature' => $this->input->post('APISignature'),
                'stripe_secret_key' => $this->input->post('stripe_secret_key'),
                'stripe_publishable_key' => $this->input->post('stripe_publishable_key'),
                'api_login_id' => $this->input->post('api_login_id'),
                'api_transaction_key' => $this->input->post('api_transaction_key'),
            );
        } elseif ($this->input->post('update_settings')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("pos/settings");
        }

        if ($this->form_validation->run() == true && $this->pos_model->updateSetting($data)) {
            if (DEMO) {
                $this->session->set_flashdata('message', $this->lang->line('pos_setting_updated'));
                admin_redirect("pos/settings");
            }

            if ($this->write_payments_config($payment_config)) {
                $this->session->set_flashdata('message', $this->lang->line('pos_setting_updated'));
                admin_redirect("pos/settings");
            } else {
                //var_dump($this->write_payments_config($payment_config));die;
                //$this->session->set_flashdata('error', $this->lang->line('pos_setting_updated_payment_failed'));
                $this->session->set_flashdata('message', $this->lang->line('pos_setting_updated'));
                admin_redirect("pos/settings");
            }
        } else {

            $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

            $this->data['pos'] = $this->pos_model->getSetting();
            $this->data['categories'] = $this->site->getAllrecipeCategories();
            $this->data['customer'] = $this->pos_model->getCompanyByID($this->pos_settings->default_customer);
            $this->data['billers'] = $this->pos_model->getAllBillerCompanies();
            $this->data['taxs'] = $this->pos_model->getAllTaxRates();
            $this->data['service_charge'] = $this->pos_model->getAllSericeCharges();
            $this->config->load('payment_gateways');
            $this->data['stripe_secret_key'] = $this->config->item('stripe_secret_key');
            $this->data['stripe_publishable_key'] = $this->config->item('stripe_publishable_key');
            $authorize = $this->config->item('authorize');
            $this->data['api_login_id'] = $authorize['api_login_id'];
            $this->data['api_transaction_key'] = $authorize['api_transaction_key'];
            $this->data['APIUsername'] = $this->config->item('APIUsername');
            $this->data['APIPassword'] = $this->config->item('APIPassword');
            $this->data['APISignature'] = $this->config->item('APISignature');
            $this->data['printers'] = $this->pos_model->getAllPrinters();
            $this->data['paypal_balance'] = null; // $this->pos_settings->paypal_pro ? $this->paypal_balance() : NULL;
            $this->data['stripe_balance'] = null; // $this->pos_settings->stripe ? $this->stripe_balance() : NULL;
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('pos_settings')));
            $meta = array('page_title' => lang('pos_settings'), 'bc' => $bc);
            $this->page_construct('pos/settings', $meta, $this->data);
        }
    }

    public function write_payments_config($config)
    {
        if (!$this->Owner) {
            $this->session->set_flashdata('error', lang('access_denied'));
            admin_redirect("welcome");
        }
        if (DEMO) {
            return true;
        }
        $file_contents = file_get_contents('./assets/config_dumps/payment_gateways.php');
        $output_path = APPPATH . 'config/payment_gateways.php';
        $this->load->library('parser');
        $parse_data = array(
            'APIUsername' => $config['APIUsername'],
            'APIPassword' => $config['APIPassword'],
            'APISignature' => $config['APISignature'],
            'stripe_secret_key' => $config['stripe_secret_key'],
            'stripe_publishable_key' => $config['stripe_publishable_key'],
            'api_login_id' => $config['api_login_id'],
            'api_transaction_key' => $config['api_transaction_key'],
        );
        $new_config = $this->parser->parse_string($file_contents, $parse_data);

        $handle = fopen($output_path, 'w+');
        @chmod($output_path, 0777);

        if (is_writable($output_path)) {
            if (fwrite($handle, $new_config)) {
                @chmod($output_path, 0644);
                return true;
            } else {
                @chmod($output_path, 0644);
                return false;
            }
        } else {
            @chmod($output_path, 0644);
            return false;
        }
    }

    public function opened_bills($per_page = 0)
    {
        $this->load->library('pagination');

        //$this->table->set_heading('Id', 'The Title', 'The Content');
        if ($this->input->get('per_page')) {
            $per_page = $this->input->get('per_page');
        }

        $config['base_url'] = admin_url('pos/opened_bills');
        $config['total_rows'] = $this->pos_model->bills_count();
        $config['per_page'] = 6;
        $config['num_links'] = 3;

        $config['full_tag_open'] = '<ul class="pagination pagination-sm">';
        $config['full_tag_close'] = '</ul>';
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a>';
        $config['cur_tag_close'] = '</a></li>';

        $this->pagination->initialize($config);
        $data['r'] = true;
        $bills = $this->pos_model->fetch_bills($config['per_page'], $per_page);
        if (!empty($bills)) {
            $html = "";
            $html .= '<ul class="ob">';
            foreach ($bills as $bill) {
                $html .= '<li><button type="button" class="btn btn-info sus_sale" id="' . $bill->id . '"><p>' . $bill->suspend_note . '</p><strong>' . $bill->customer . '</strong><br>' . lang('date') . ': ' . $bill->date . '<br>' . lang('items') . ': ' . $bill->count . '<br>' . lang('total') . ': ' . $this->sma->formatMoney($bill->total) . '</button></li>';
            }
            $html .= '</ul>';
        } else {
            $html = "<h3>" . lang('no_opeded_bill') . "</h3><p>&nbsp;</p>";
            $data['r'] = false;
        }

        $data['html'] = $html;

        $data['page'] = $this->pagination->create_links();
        echo $this->load->view($this->theme . 'pos/opened', $data, true);

    }

    public function delete($id = null)
    {

        $this->sma->checkPermissions('index');

        if ($this->pos_model->deleteBill($id)) {
            $this->sma->send_json(array('error' => 0, 'msg' => lang("suspended_sale_deleted")));
        }
    }

    public function email_receipt($sale_id = null)
    {
        $this->sma->checkPermissions('index');
        if ($this->input->post('id')) {
            $sale_id = $this->input->post('id');
        }
        if (!$sale_id) {
            die('No sale selected.');
        }
        if ($this->input->post('email')) {
            $to = $this->input->post('email');
        }
        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['message'] = $this->session->flashdata('message');

        $this->data['rows'] = $this->pos_model->getAllInvoiceItems($sale_id);
        $inv = $this->pos_model->getInvoiceByID($sale_id);
        $biller_id = $inv->biller_id;
        $customer_id = $inv->customer_id;
        $this->data['biller'] = $this->pos_model->getCompanyByID($biller_id);
        $this->data['customer'] = $this->pos_model->getCompanyByID($customer_id);

        $this->data['payments'] = $this->pos_model->getInvoicePayments($sale_id);
        $this->data['pos'] = $this->pos_model->getSetting();
        $this->data['barcode'] = $this->barcode($inv->reference_no, 'code128', 30);
        $this->data['inv'] = $inv;
        $this->data['sid'] = $sale_id;
        $this->data['page_title'] = $this->lang->line("invoice");

        if (!$to) {
            $to = $this->data['customer']->email;
        }
        if (!$to) {
            $this->sma->send_json(array('msg' => $this->lang->line("no_meil_provided")));
        }
        $receipt = $this->load->view($this->theme . 'pos/email_receipt', $this->data, true);

        try {
            if ($this->sma->send_email($to, lang('receipt_from') . ' ' . $this->data['biller']->company, $receipt)) {
                $this->sma->send_json(array('msg' => $this->lang->line("email_sent")));
            } else {
                $this->sma->send_json(array('msg' => $this->lang->line("email_failed")));
            }
        } catch (Exception $e) {
            $this->sma->send_json(array('msg' => $e->getMessage()));
        }

    }

    public function active()
    {
        $this->session->set_userdata('last_activity', now());
        if ((now() - $this->session->userdata('last_activity')) <= 20) {
            die('Successfully updated the last activity.');
        } else {
            die('Failed to update last activity.');
        }
    }

    public function add_payment($id = null)
    {
        $this->sma->checkPermissions('payments', true, 'sales');
        $this->load->helper('security');
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $this->form_validation->set_rules('reference_no', lang("reference_no"), 'required');
        $this->form_validation->set_rules('amount-paid', lang("amount"), 'required');
        $this->form_validation->set_rules('paid_by', lang("paid_by"), 'required');
        $this->form_validation->set_rules('userfile', lang("attachment"), 'xss_clean');
        if ($this->form_validation->run() == true) {
            if ($this->input->post('paid_by') == 'deposit') {
                $sale = $this->pos_model->getInvoiceByID($this->input->post('sale_id'));
                $customer_id = $sale->customer_id;
                if (!$this->site->check_customer_deposit($customer_id, $this->input->post('amount-paid'))) {
                    $this->session->set_flashdata('error', lang("amount_greater_than_deposit"));
                    redirect($_SERVER["HTTP_REFERER"]);
                }
            } else {
                $customer_id = null;
            }
            if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld(trim($this->input->post('date')));
            } else {
                $date = date('Y-m-d H:i:s');
            }
            $payment = array(
                'date' => $date,
                'sale_id' => $this->input->post('sale_id'),
                'reference_no' => $this->input->post('reference_no'),
                'amount' => $this->input->post('amount-paid'),
                'paid_by' => $this->input->post('paid_by'),
                'cheque_no' => $this->input->post('cheque_no'),
                'cc_no' => $this->input->post('paid_by') == 'gift_card' ? $this->input->post('gift_card_no') : $this->input->post('pcc_no'),
                'cc_holder' => $this->input->post('pcc_holder'),
                'cc_month' => $this->input->post('pcc_month'),
                'cc_year' => $this->input->post('pcc_year'),
                'cc_type' => $this->input->post('pcc_type'),
                'cc_cvv2' => $this->input->post('pcc_ccv'),
                'note' => $this->input->post('note'),
                'created_by' => $this->session->userdata('user_id'),
                'type' => 'received',
            );

            if ($_FILES['userfile']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path'] = $this->digital_upload_path;
                $config['allowed_types'] = $this->digital_file_types;
                $config['max_size'] = $this->allowed_file_size;
                $config['overwrite'] = false;
                $config['encrypt_name'] = true;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER["HTTP_REFERER"]);
                }
                $photo = $this->upload->file_name;
                $payment['attachment'] = $photo;
            }

            //$this->sma->print_arrays($payment);

        } elseif ($this->input->post('add_payment')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }

        if ($this->form_validation->run() == true && $msg = $this->pos_model->addPayment($payment, $customer_id)) {
            if ($msg) {
                if ($msg['status'] == 0) {
                    unset($msg['status']);
                    $error = '';
                    foreach ($msg as $m) {
                        if (is_array($m)) {
                            foreach ($m as $e) {
                                $error .= '<br>' . $e;
                            }
                        } else {
                            $error .= '<br>' . $m;
                        }
                    }
                    $this->session->set_flashdata('error', '<pre>' . $error . '</pre>');
                } else {
                    $this->session->set_flashdata('message', lang("payment_added"));
                }
            } else {
                $this->session->set_flashdata('error', lang("payment_failed"));
            }
            admin_redirect("pos/sales");
        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $sale = $this->pos_model->getInvoiceByID($id);
            $this->data['inv'] = $sale;
            $this->data['payment_ref'] = $this->site->getReference('pay');
            $this->data['modal_js'] = $this->site->modal_js();

            $this->load->view($this->theme . 'pos/add_payment', $this->data);
        }
    }

    public function updates()
    {
        if (DEMO) {
            $this->session->set_flashdata('warning', lang('disabled_in_demo'));
            redirect($_SERVER["HTTP_REFERER"]);
        }
        if (!$this->Owner) {
            $this->session->set_flashdata('error', lang('access_denied'));
            admin_redirect("welcome");
        }
        $this->form_validation->set_rules('purchase_code', lang("purchase_code"), 'required');
        $this->form_validation->set_rules('srampos_username', lang("srampos_username"), 'required');
        if ($this->form_validation->run() == true) {
            $this->db->update('pos_settings', array('purchase_code' => $this->input->post('purchase_code', true), 'srampos_username' => $this->input->post('srampos_username', true)), array('pos_id' => 1));
            admin_redirect('pos/updates');
        } else {
            $fields = array('version' => $this->pos_settings->version, 'code' => $this->pos_settings->purchase_code, 'username' => $this->pos_settings->srampos_username, 'site' => base_url());
            $this->load->helper('update');
            $protocol = is_https() ? 'https://' : 'http://';
            $updates = get_remote_contents($protocol . 'api.srampos.com/v1/update/', $fields);
            $this->data['updates'] = json_decode($updates);
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('updates')));
            $meta = array('page_title' => lang('updates'), 'bc' => $bc);
            $this->page_construct('pos/updates', $meta, $this->data);
        }
    }

    public function install_update($file, $m_version, $version)
    {
        if (DEMO) {
            $this->session->set_flashdata('warning', lang('disabled_in_demo'));
            redirect($_SERVER["HTTP_REFERER"]);
        }
        if (!$this->Owner) {
            $this->session->set_flashdata('error', lang('access_denied'));
            admin_redirect("welcome");
        }
        $this->load->helper('update');
        save_remote_file($file . '.zip');
        $this->sma->unzip('./files/updates/' . $file . '.zip');
        if ($m_version) {
            $this->load->library('migration');
            if (!$this->migration->latest()) {
                $this->session->set_flashdata('error', $this->migration->error_string());
                admin_redirect("pos/updates");
            }
        }
        $this->db->update('pos_settings', array('version' => $version), array('pos_id' => 1));
        unlink('./files/updates/' . $file . '.zip');
        $this->session->set_flashdata('success', lang('update_done'));
        admin_redirect("pos/updates");
    }

    public function open_drawer()
    {

        $data = json_decode($this->input->get('data'));
        $this->load->library('escpos');
        $this->escpos->load($data->printer);
        $this->escpos->open_drawer();

    }

    public function p()
    {

        $data = json_decode($this->input->get('data'));
        $this->load->library('escpos');
        $this->escpos->load($data->printer);
        $this->escpos->print_receipt($data);

    }

    public function printers()
    {
        $this->sma->checkPermissions();
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['page_title'] = lang('printers');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('pos'), 'page' => lang('pos')), array('link' => '#', 'page' => lang('printers')));
        $meta = array('page_title' => lang('list_printers'), 'bc' => $bc);
        $this->page_construct('pos/printers', $meta, $this->data);
    }

    public function get_printers(){
        $this->sma->checkPermissions('printers');

        $this->load->library('datatables');
        $this->datatables
            ->select("'sno',id, title, type, profile, path, ip_address, port")
            ->from("printers")
            ->add_column("Actions", "<div class='text-center'> <a href='" . admin_url('pos/edit_printer/$1') . "' class='btn-warning btn-xs tip' title='" . lang("edit_printer") . "'><i class='fa fa-edit'></i></a> <a href='#' class='btn-danger btn-xs tip po' title='<b>" . lang("delete_printer") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('pos/delete_printer/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", "id")
            ->unset_column('id');
        echo $this->datatables->generate();

    }

    public function add_printer(){
        $this->sma->checkPermissions();
        $this->form_validation->set_rules('title', $this->lang->line("title"), 'required');
        $this->form_validation->set_rules('type', $this->lang->line("type"), 'required');
        $this->form_validation->set_rules('profile', $this->lang->line("profile"), 'required');
        $this->form_validation->set_rules('char_per_line', $this->lang->line("char_per_line"), 'required');
        if ($this->input->post('type') == 'network') {
            $this->form_validation->set_rules('ip_address', $this->lang->line("ip_address"), 'required|is_unique[printers.ip_address]');
            $this->form_validation->set_rules('port', $this->lang->line("port"), 'required');
        } else {
            $this->form_validation->set_rules('path', $this->lang->line("path"), 'required|is_unique[printers.path]');
        }

        if ($this->form_validation->run() == true) {
            $data = array('title' => $this->input->post('title'),
                'type' => $this->input->post('type'),
                'profile' => $this->input->post('profile'),
                'char_per_line' => $this->input->post('char_per_line'),
                'path' => $this->input->post('path'),
                'ip_address' => $this->input->post('ip_address'),
                'port' => ($this->input->post('type') == 'network') ? $this->input->post('port') : null,
                'print_mirroring' => (isset($_POST['print_mirroring'])) ? implode(',', $this->input->post('print_mirroring')) : '',
				'store_id'=>$this->input->post('warehouse_id')
            );
        }

        if ($this->form_validation->run() == true && $cid = $this->pos_model->addPrinter($data)) {
            $this->session->set_flashdata('message', $this->lang->line("printer_added"));
            admin_redirect("pos/printers");

        } else {
            if ($this->input->is_ajax_request()) {
                echo json_encode(array('status' => 'failed', 'msg' => validation_errors()));die();
            }
			$this->data['warehouses'] = $this->settings_model->getAllWarehouses();
            $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
            $this->data['page_title'] = lang('add_printer');
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('pos'), 'page' => lang('pos')), array('link' => admin_url('pos/printers'), 'page' => lang('printers')), array('link' => '#', 'page' => lang('add_printer')));
            $meta = array('page_title' => lang('add_printer'), 'bc' => $bc);
            $this->data['other_printers'] = $this->pos_model->getOtherPrinters();
            $this->page_construct('pos/add_printer', $meta, $this->data);
        }
    }

    public function edit_printer($id = null){
        $this->sma->checkPermissions();
        if ($this->input->get('id')) {$id = $this->input->get('id', true);}
        $printer = $this->pos_model->getPrinterByID($id);
        $this->form_validation->set_rules('title', $this->lang->line("title"), 'required');
        $this->form_validation->set_rules('type', $this->lang->line("type"), 'required');
        $this->form_validation->set_rules('profile', $this->lang->line("profile"), 'required');
        $this->form_validation->set_rules('char_per_line', $this->lang->line("char_per_line"), 'required');
        if ($this->input->post('type') == 'network') {
            $this->form_validation->set_rules('ip_address', $this->lang->line("ip_address"), 'required');
            if ($this->input->post('ip_address') != $printer->ip_address) {
                $this->form_validation->set_rules('ip_address', $this->lang->line("ip_address"), 'is_unique[printers.ip_address]');
            }
            $this->form_validation->set_rules('port', $this->lang->line("port"), 'required');
        } else {
            $this->form_validation->set_rules('path', $this->lang->line("path"), 'required');
           
        }

        if ($this->form_validation->run() == true) {
            $data = array('title' => $this->input->post('title'),
                'type' => $this->input->post('type'),
                'profile' => $this->input->post('profile'),
                'char_per_line' => $this->input->post('char_per_line'),
                'path' => $this->input->post('path'),
                'ip_address' => $this->input->post('ip_address'),
                'print_mirroring' => (isset($_POST['print_mirroring'])) ? implode(',', $this->input->post('print_mirroring')) : '',
                'port' => ($this->input->post('type') == 'network') ? $this->input->post('port') : null,
				'store_id'=>$this->input->post('warehouse_id')
            );
        }
        if ($this->form_validation->run() == true && $this->pos_model->updatePrinter($id, $data)) {
            $this->session->set_flashdata('message', $this->lang->line("printer_updated"));
            admin_redirect("pos/printers");

        } else {
            $this->data['printer'] = $printer;
			$this->data['warehouses'] = $this->settings_model->getAllWarehouses();
            $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
            $this->data['page_title'] = lang('edit_printer');
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('pos'), 'page' => lang('pos')), array('link' => admin_url('pos/printers'), 'page' => lang('printers')), array('link' => '#', 'page' => lang('edit_printer')));
            $meta = array('page_title' => lang('edit_printer'), 'bc' => $bc);
            $this->data['other_printers'] = $this->pos_model->getOtherPrinters($id);
            $this->page_construct('pos/edit_printer', $meta, $this->data);

        }
    }

    public function delete_printer($id = null){
        if (DEMO) {
            $this->session->set_flashdata('error', $this->lang->line("disabled_in_demo"));
            $this->sma->md();
        }
        $this->sma->checkPermissions();
        if ($this->input->get('id')) {$id = $this->input->get('id', true);}
        if ($this->pos_model->deletePrinter($id)) {
            $this->sma->send_json(array('error' => 0, 'msg' => lang("printer_deleted")));
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
        if ($delivery_person != 0) {
            $row['delivery_person'] = $this->pos_model->getUserByID($delivery_person);
        }
        if ($this->pos_settings->bill_print_format == 3) {

            $row['tax_splits'] = $this->site->get_tax_splits($this->pos_settings->default_tax);
            $row['tax_rate'] = $this->site->getTaxRateByID($this->pos_settings->default_tax);
        }

        $this->sma->send_json($row);
    }

    public function gatdata_sugar_plam_print_billing()
    {
        $id = $this->input->post('billid');
        $row['billdata'] = $this->pos_model->get_BillData($id);

        $row['billitemdata'] = $this->pos_model->getAllBillitems($id);
        /*echo "<pre>";
        print_r($row['billitemdata']);die;*/
        $row['billdata']->service_charge_display_value = '';
        if ($row['billdata']->service_charge_id != 0) {
            $ServiceCharge = $this->site->getServiceChargeByID($row['billdata']->service_charge_id);
            $row['billdata']->service_charge_display_value = $ServiceCharge->name;
        }
        $row['discount'] = $this->pos_model->getBillDiscountNames($id);
        $inv = $this->pos_model->getInvoiceByID($id);
        $row['biller'] = $this->pos_model->getCompanyByID($inv->biller_id);
        //echo "<pre>";
        $row['inv'] = $inv;
        $row['created_by'] = $this->site->getUser($inv->created_by);
        $row['cashier'] = $this->site->getUser($this->session->userdata('user_id'));
        $customer_id = $inv->customer_id;
        $delivery_person = $inv->delivery_person_id;
        $row['customer'] = $this->pos_model->getCompanyByID($customer_id);
        if ($delivery_person != 0) {
            $row['delivery_person'] = $this->pos_model->getUserByID($delivery_person);
        }
        //if($this->pos_settings->bill_print_format ==1){
        $this->load->view($this->theme . 'pos/local_bill/print_bill_for_orderscreen', $row, false);
        //}
    }
/*public function check_timeout_notify($id = NULL,)
{
$order_item_id = $this->input->post('id');

$noti = $this->pos_model->checkTimeoutNotify($order_item_id);

if(!empty($noti)){
$status = 'success';

}else{
$status = 'error';
}
$this->sma->send_json(array('msg' => $msg, 'status' => $msg));
}    */
    public function update_timeout_notify($id = null)
    {
        $order_item_id = $this->input->post('id');

        $notification_array['from_role'] = $this->session->userdata('group_id');

        $orderitem = $this->pos_model->getOrderitemDetsils($order_item_id);

        $noti = $this->pos_model->checkTimeoutNotify($order_item_id);

        if (empty($noti)) {

            if (!empty($orderitem)) {

                foreach ($orderitem as $item) {

                    $notification_array['insert_array'] = array(
                        'msg' => 'The order [' . $item->name . '-' . $item->reference_no . '-' . $item->recipe_name . '] has been Timeout.',
                        'type' => 'Order Timeout Status',
                        'table_id' => $item->table_id,
                        'user_id' => $this->session->userdata('user_id'),
                        'to_user_id' => $item->created_by,
                        'role_id' => WAITER,
                        'warehouse_id' => $this->session->userdata('warehouse_id'),
                        'created_on' => date('Y-m-d H:m:s'),
                        'is_read' => 0,
                        'order_item_id' => $order_item_id,
                    );
                }
            }
            $result = $this->site->create_notification($notification_array);

        }
    }

    public function DINEINcheckCustomerDiscount()
    {
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

    public function BBQcheckCustomerDiscount()
    {
        $billid = $this->input->post('bill_id');
        if ($result = $this->pos_model->getBBQDiscount($billid)) {
            $dis_result = $this->pos_model->getAllBBQDiscount();
            echo json_encode(array('cus_dis' => $result, 'all_dis' => $dis_result));exit;
        }
        // echo 'no_discount';exit;
        echo json_encode(array('no_discount' => 'no_discount'));exit;
    }

    public function CONcheckCustomerDiscount(){
        $split_id = $this->input->post('ordersplit');
        $split_data = $this->pos_model->getSplitBils($split_id);
        foreach ($split_data as $row) { // echo '<pre>';print_r($row);
            if ($row->sales_type_id == 1) {
                $result_cus = $this->pos_model->getDineinCustomerDiscount($row->bil_id);
                $cus_dis_result = (!$row->unique_discount) ? $this->pos_model->getAllCustomerDiscount() : false;
            } elseif ($row->sales_type_id == 4) {
                $result_bbq = $this->pos_model->getBBQDiscount($row->bil_id);
                $bbq_dis_result = $this->pos_model->getAllBBQDiscount();
            }
        }
        if (!empty($result_cus) || !empty($result_bbq)) {
            $return = $this->site->is_uniqueDiscountExist();
            $unique_discount = 0;
            if (!empty($return)) {
                $unique_discount = 1;
            }
            echo json_encode(array('cus_dis' => $result_cus, 'bbq_dis' => $result_bbq, 'all_cus_dis' => $cus_dis_result, 'all_bbq_dis' => $bbq_dis_result, 'unique_discount' => $unique_discount));exit;
        }
        echo json_encode(array('no_discount' => 'no_discount'));exit;

    }

    public function checkCustomerDiscount(){
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

        //echo 'no_discount';exit;
        // echo json_encode(array('no_discount'=>'no_discount'));exit;

    }

    public function DINEINupdateBillDetails()
    {

        $billid = $this->input->get('bill_id');
        $dis_id = $this->input->get('dis_id');
        $cusdis_val = $this->site->getCustomerDiscountval($dis_id);

        $dis_val = $cusdis_val . '%';
        // $result = $this->pos_model->getCustomerDiscount($billid);die;
        if ($result = $this->pos_model->getCustomerDiscount($billid)) {
            if ($dis_id != $result->customer_discount_id) {
                $return = $this->pos_model->update_bill_withcustomer_discount($billid, $dis_id, $dis_val);
                $return = $this->sma->formatDecimal($return);
                echo json_encode(array('amount' => $return));exit;
            } else {
                if ($result->tax_type == 1) {
                    $grand_total = $result->total - $result->total_discount + $result->total_tax + $result->service_charge_amount;
                } else {
                    $grand_total = $result->total - $result->total_discount + $result->service_charge_amount;
                }
                $return = $this->sma->formatDecimal($grand_total);
                echo json_encode(array('amount' => $return));exit;
            }
        }
        echo json_encode(array('no_discount' => 'no_discount'));exit;
    }

    public function DINEINupdateBillDetails_28_09_2018sivan()
    {
        $billid = $this->input->get('bill_id');
        $dis_id = $this->input->get('dis_id');
        $result = $this->pos_model->getDineinCustomerDiscount($billid);
        $bils = $this->pos_model->getDINEINBils($billid);
        $bils_item = $this->pos_model->getDINEINBilitem($billid);
        $discount_data = $this->pos_model->getDINEINCUSDisIDBy($dis_id);

        if ($result->id != $discount_data->id) {

            $customer_request_id = $result->customer_request_id;
            $request_array = array(
                'customer_discount_val' => $discount_data->id,
            );

            $final_total = $bils->total;

            if (!empty($discount_data->discount_val)) {
                $discount_data->discount_val = $discount_data->discount_val . '%';
            } else {
                $discount_data->discount_val = $discount_data->discount_val;
            }

            $total_discount = $this->site->calculateDiscount($discount_data->discount_val, $final_total);
            $total_discount_total = ($final_total - $total_discount);
            $total_tax = $this->site->calculateOrderTax($bils->tax_id, $total_discount_total);
            if ($this->Settings->tax_type == 1) {
                $grand_total = $total_discount_total + $total_tax;
            } else {
                $grand_total = $total_discount_total;
            }
            $round_total = $grand_total;
            $customer_discount_id = $discount_data->id;
            $bils_update = array(
                'total' => $bils->total,
                'total_discount' => $total_discount,
                'customer_discount_id' => $customer_discount_id,
                'tax_id' => $bils->tax_id,
                'total_tax' => $total_tax,
                'grand_total' => $grand_total,
                'round_total' => $round_total,

            );

            foreach ($bils_item as $item) {
                $net = ($item->unit_price * $item->quantity);

                $input_discount = $this->site->calculateDiscount($discount_data->discount_val, $net);
                $net = $net - $input_discount;
                $tax = $this->site->calculateOrderTax($bils->tax_id, $net);

                $item_updates[] = array(
                    'input_discount' => $input_discount,
                    'tax' => $tax,
                    'bil_id' => $item->bil_id,
                    'id' => $item->id,
                );
                $bilitem_ids[] = array(
                    'id' => $item->id,
                );
            }

            $return = $this->pos_model->DINEINupdate_bil($bils_update, $billid, $item_updates, $bilitem_ids, $request_array, $customer_request_id);
            $amount = $this->sma->formatDecimal($grand_total);
            echo json_encode(array('amount' => $amount));exit;
        }

        echo json_encode(array('no_discount' => 'no_discount'));exit;

    }

    public function BBQupdateBillDetails()
    {
        $billid = $this->input->get('bill_id');
        $dis_id = $this->input->get('dis_id');
        $result = $this->pos_model->getBBQDiscount($billid);
        $bils = $this->pos_model->getBBQBils($billid);
        $bils_item = $this->pos_model->getBBQBilitem($billid);
        $bils_cover = $this->pos_model->getBBQBilcover($billid);
        $discount_data = $this->pos_model->getBBQCUSDisIDBy($dis_id);

        if ($result->id != $discount_data->id) {

            $customer_request_id = $result->customer_request_id;
            $request_array = array(
                'bbq_discount_val' => $discount_data->id,
            );

            $final_total = $bils->total;

            if (!empty($discount_data->discount)) {
                $discount_data->discount = $discount_data->discount . '%';
            } else {
                $discount_data->discount = $discount_data->discount;
            }

            $total_discount = $this->site->calculateDiscount($discount_data->discount, $final_total);
            $total_discount_total = ($final_total - $total_discount - $bils->bbq_cover_discount);
            $total_tax = $this->site->calculateOrderTax($bils->tax_id, $total_discount_total);

            if ($this->Settings->tax_type == 1) {
                $grand_total = $total_discount_total + $total_tax;
            } else {
                $grand_total = $total_discount_total;
            }
            $round_total = $grand_total;
            $customer_discount_id = $discount_data->id;
            $bils_update = array(
                'total' => $bils->total,
                'total_discount' => $total_discount,
                'customer_discount_id' => $customer_discount_id,
                'tax_id' => $bils->tax_id,
                'total_tax' => $total_tax,
                'grand_total' => $grand_total,
                'round_total' => $round_total,

            );

            foreach ($bils_item as $item) {
                $net = ($item->unit_price * $item->quantity);

                $input_discount = $this->site->calculateDiscount($discount_data->discount_val, $net);
                $net = $net - $input_discount;
                $tax = $this->site->calculateOrderTax($bils->tax_id, $net);

                $item_updates[] = array(
                    'input_discount' => $input_discount,
                    'tax' => $tax,
                    'bil_id' => $item->bil_id,
                    'id' => $item->id,
                );
                $bilitem_ids[] = array(
                    'id' => $item->id,
                );
            }

            $return = $this->pos_model->BBQupdate_bil($bils_update, $billid, $item_updates, $bilitem_ids, $request_array, $customer_request_id);
            $amount = $this->sma->formatDecimal($grand_total);
            echo json_encode(array('amount' => $amount));exit;
        }

        echo json_encode(array('no_discount' => 'no_discount'));exit;

    }

    public function CONupdateBillDetails()
    {

        $cus_dis_id = $this->input->get('cus_dis_id');
        $bbq_dis_id = $this->input->get('bbq_dis_id');
        // var_dump($return);die;
        $split_id = $this->input->get('ordersplit');
        $split_data = $this->pos_model->getSplitBils($split_id);

        foreach ($split_data as $row) {
            if ($row->sales_type_id == 1) {

                /*
                $billid = $this->input->get('bill_id');
                $dis_id = $this->input->get('dis_id');
                $cusdis_val = $this->site->getCustomerDiscountval($dis_id);
                $dis_val = $cusdis_val.'%';
                if($result = $this->pos_model->getCustomerDiscount($billid)){
                $return =  $this->pos_model->update_bill_withcustomer_discount($billid,$dis_id,$dis_val);
                $return = $this->sma->formatDecimal($return);
                echo json_encode(array('amount'=> $return));exit;
                }
                echo json_encode(array('no_discount'=>'no_discount'));exit;
                 */

                $dis_id = $cus_dis_id;
                $cusdis_val = $this->site->getCustomerDiscountval($dis_id);
                $dis_val = $cusdis_val . '%';
                if ($result = $this->pos_model->getDineinCustomerDiscount($row->bil_id)) {
                    $return = $this->pos_model->update_bill_withcustomer_discount($row->bil_id, $dis_id, $dis_val);
                    $return = $this->sma->formatDecimal($return);
                    $amount[] = $return;

                    // echo json_encode(array('amount'=> $return));exit;
                }
                /*
            $cus_result = $this->pos_model->getDineinCustomerDiscount($row->bil_id);
            $cus_bils = $this->pos_model->getDINEINBils($row->bil_id);
            $cus_bils_item = $this->pos_model->getDINEINBilitem($row->bil_id);
            $cus_discount_data = $this->pos_model->getDINEINCUSDisIDBy($cus_dis_id);

            if($cus_result->id != $cus_discount_data->id) {

            $cus_customer_request_id = $cus_result->customer_request_id;
            $cus_request_array = array(
            'customer_discount_val' => $cus_discount_data->id
            );

            $cus_final_total = $cus_bils->total;

            if(!empty($cus_discount_data->discount_val)){
            $cus_discount_data->discount_val = $cus_discount_data->discount_val.'%';
            }else{
            $cus_discount_data->discount_val = $cus_discount_data->discount_val;
            }
            $cus_total_discount = $this->site->calculateDiscount($cus_discount_data->discount_val, $cus_final_total);

            $cus_total_discount_total = ($cus_final_total - $cus_total_discount);
            $cus_total_tax = $this->site->calculateOrderTax($cus_bils->tax_id, $cus_total_discount_total);
            if($this->Settings->tax_type == 1){
            $cus_grand_total = $cus_total_discount_total + $cus_total_tax;
            }else{
            $cus_grand_total = $cus_total_discount_total;
            }
            $cus_round_total = $cus_grand_total;
            $cus_customer_discount_id = $cus_discount_data->id;
            $cus_bils_update = array(
            'total' => $cus_bils->total,
            'total_discount' => $cus_total_discount,
            'customer_discount_id' => $cus_customer_discount_id,
            'tax_id' => $cus_bils->tax_id,
            'total_tax' => $cus_total_tax,
            'grand_total' => $cus_grand_total,
            'round_total' => $cus_round_total

            );

            foreach($cus_bils_item as $cus_item){
            $cus_net = ($cus_item->unit_price * $cus_item->quantity);

            $cus_input_discount = $this->site->calculateDiscount($cus_discount_data->discount_val, $cus_net);
            $cus_net = $cus_net - $cus_input_discount;
            $cus_tax = $this->site->calculateOrderTax($cus_bils->tax_id, $cus_net);

            $cus_item_updates[] = array(
            'input_discount' => $cus_input_discount,
            'tax' => $cus_tax,
            'bil_id' => $cus_item->bil_id,
            'id' => $cus_item->id
            );
            $cus_bilitem_ids[] = array(
            'id' => $cus_item->id
            );
            }

            $cus_return =  $this->pos_model->DINEINupdate_bil($cus_bils_update, $cus_billid, $cus_item_updates, $cus_bilitem_ids, $cus_request_array, $cus_customer_request_id);
            $amount[] = $this->sma->formatDecimal($cus_grand_total);

            }*/

            } elseif ($row->sales_type_id == 4) {

                $billid = $row->bil_id;
                $bbq_result = $this->pos_model->getBBQDiscount($row->bil_id);
                $bbq_bils = $this->pos_model->getBBQBils($row->bil_id);
                $bbq_bils_item = $this->pos_model->getBBQBilitem($row->bil_id);
                $bbq_bils_cover = $this->pos_model->getBBQBilcover($row->bil_id);
                $bbq_discount_data = $this->pos_model->getBBQCUSDisIDBy($bbq_dis_id);

                // if($bbq_result->id != $bbq_discount_data->id) {

                $bbq_customer_request_id = $bbq_result->customer_request_id;
                $bbq_request_array = array(
                    'bbq_discount_val' => $bbq_discount_data->id,
                );

                $bbq_final_total = $bbq_bils->total - $bbq_bils->bbq_cover_discount;

                if (!empty($bbq_discount_data->discount)) {
                    $bbq_discount_data->discount = $bbq_discount_data->discount . '%';
                } else {
                    $bbq_discount_data->discount = $bbq_discount_data->discount;
                }

                $bbq_total_discount = $this->site->calculateDiscount($bbq_discount_data->discount, $bbq_final_total);
                $bbq_total_discount_total = ($bbq_final_total - $bbq_total_discount);
                $bbq_total_tax = $this->site->calculateOrderTax($bbq_bils->tax_id, $bbq_total_discount_total);

                if ($this->Settings->tax_type == 1) {
                    $bbq_grand_total = $bbq_total_discount_total + $bbq_total_tax;
                } else {
                    $bbq_grand_total = $bbq_total_discount_total;
                }
                $bbq_round_total = $bbq_grand_total;
                $bbq_customer_discount_id = $bbq_discount_data->id;
                $bbq_bils_update = array(
                    'total' => $bbq_bils->total,
                    'total_discount' => $bbq_total_discount,
                    'customer_discount_id' => $bbq_customer_discount_id,
                    'tax_id' => $bbq_bils->tax_id,
                    'total_tax' => $bbq_total_tax,
                    'grand_total' => $bbq_grand_total,
                    'round_total' => $bbq_round_total,

                );

                foreach ($bbq_bils_item as $bbq_item) {
                    $bbq_net = ($bbq_item->unit_price * $bbq_item->quantity);

                    $bbq_input_discount = $this->site->calculateDiscount($bbq_discount_data->discount_val, $bbq_net);
                    $bbq_net = $bbq_net - $bbq_input_discount;
                    $bbq_tax = $this->site->calculateOrderTax($bbq_bils->tax_id, $bbq_net);

                    $bbq_item_updates[] = array(
                        'input_discount' => $bbq_input_discount,
                        'tax' => $bbq_tax,
                        'bil_id' => $bbq_item->bil_id,
                        'id' => $bbq_item->id,
                    );
                    $bbq_bilitem_ids[] = array(
                        'id' => $bbq_item->id,
                    );
                }

                $bbq_return = $this->pos_model->BBQupdate_bil($bbq_bils_update, $billid, $bbq_item_updates, $bbq_bilitem_ids, $bbq_request_array, $bbq_customer_request_id);
                $amount[] = $this->sma->formatDecimal($bbq_grand_total);

                // }

            }

        }
        if (!empty($amount)) {
            $amount = $this->sma->formatDecimal(array_sum($amount));
            echo json_encode(array('amount' => $amount));exit;
        }
        /*################*/

        echo json_encode(array('no_discount' => 'no_discount'));exit;

    }

    public function updateBillDetails()
    {
        $billid = $this->input->post('bill_id');
        $dis_id = $this->input->post('dis_id');
        if ($result = $this->pos_model->getDineinCustomerDiscount($billid)) {
            $return = $this->pos_model->update_bill_withcustomer_discount($billid, $dis_id);
            $return = $this->sma->formatDecimal($return);
            echo json_encode(array('amount' => $return));exit;
            // echo json_encode($return);exit;
        }

        echo json_encode(array('no_discount' => 'no_discount'));exit;

        // $bil_items = $this->pos_model->getBillItemsRecipeID($billid);
        /*if ($bil_items) {
        $disamt = 0;
        foreach ($bil_items as $item){

        $inputdis =  $this->pos_model->group_customer_discount_calculation($item->category_id,$item->amount,$dis_id);  */
        /*$recipeDetails = $this->pos_model->getrecipeByID($recipe_id);
        $discount = $this->site->discountMultiple($recipe_id);
        $price_total = $recipeDetails->cost;
        $finalAmt = $price_total;
        $dis = 0;
        if(!empty($discount)){
        if($discount[2] == 'percentage_discount'){

        $discount_value = $discount[1].'%';

        }else{
        $discount_value =$discount[1];
        }

        $dis = $this->site->calculateDiscount($discount_value, $price_total);
        $finalAmt = $price_total - $dis;
        }*/
        /*}

    echo "<pre>";
    print_r($inputdis);die;
    }*/
    }
    public function updateBillDetails1()
    {
        $billid = $this->input->post('bill_id');
        $dis_id = $this->input->post('dis_id');
        $return = [];
        $this->pos_model->updateCustomerDiscount($billid, $dis_id);
        if ($result = $this->pos_model->getCustomerDiscount($billid)) {

            $getTax = $this->site->getTaxRateByID($result->tax_id);
            $discountVal = ($result->discount_type == "percentage_discount") ? $result->value . '%' : $result->value;

            $discount_amount = $this->site->calculateDiscount($discountVal, $result->total);
            $totalDiscount = $result->total_discount + $discount_amount;
            $totalAmt_afterDiscount = $result->total - $totalDiscount;
            if ($result->tax_type == 0) {
                $grandTotal = $totalAmt_afterDiscount / (($getTax->rate / 100) + 1);
                $totalTax = $totalAmt_afterDiscount - ($totalAmt_afterDiscount / (($getTax->rate / 100) + 1));
                $amountPayable = $grandTotal + $totalTax;

            } else {
                $totalTax = $totalAmt_afterDiscount * ($getTax->rate / 100);
                $grandTotal = $totalAmt_afterDiscount + $totalTax;
                $amountPayable = $grandTotal;
            }
            $update_bil['grand_total'] = $this->sma->formatDecimal($grandTotal);
            $update_bil['total_tax'] = $this->sma->formatDecimal($totalTax);
            $update_bil['total_discount'] = $totalDiscount;
            $update_bil['round_total'] = $this->sma->formatDecimal($grandTotal);
            $return['amount'] = $this->sma->formatDecimal($amountPayable);
            //print_R($update_bil);
            //print_r($return);exit;
            $this->pos_model->update_bil($billid, $update_bil, $discountVal);

            echo json_encode($return);exit;
        }
        echo json_encode(array('no_discount' => 'no_discount'));exit;
    }

    public function customer_bildetails()
    {
        $this->sma->checkPermissions('index');

        $user = $this->site->getUser();

        $this->data['warehouses'] = null;
        $this->data['warehouse_id'] = $this->session->userdata('warehouse_id');
        $this->data['warehouse'] = $this->session->userdata('warehouse_id') ? $this->site->getWarehouseByID($this->session->userdata('warehouse_id')) : null;
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->load->view($this->theme . 'pos/customerbildetails', $this->data);
    }

    public function ajaxcustomer_bildetails()
    {

        $this->data['sales'] = $this->pos_model->getAllTablesWithCustomerRequest($this->session->userdata('warehouse_id'));
        $this->load->view($this->theme . 'pos/customerbildetails_ajax', $this->data);
    }

//  function calculate_customerdiscount(){
    //    $recipeids = $this->input->post('recipeids');
    //    $reciepe_ids = explode(",", $recipeids);
    //
    //    if ($reciepe_ids) {
    //                $disamt = 0;
    //                foreach ($reciepe_ids as $key => $recipe_id)
    //                    $group_id = $this->site->getRecipeGroupId($recipe_id);
    //                    $disamt = $this->site->getCalculateCustomerDiscount($group_id);
    //            }die;
    //    echo json_encode($return);exit;
    //    }

    public function calculate_customerdiscount()
    {

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
                    $price_total = (($recipeDetails->cost * $current_qty) + $addon_sub_total);
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

    public function manualsplit_calculate_customerdiscount()
    {

        $recipeids = $this->input->post('recipeids');
        $recipeqtys = $this->input->post('recipeqtys');
        $manualitemdis = $this->input->post('manualitemdis');
        $recipevariantids = $this->input->post('recipevariantids');
        $addonsubtotal = $this->input->post('addonsubtotal');

        $discountid = $this->input->post('discountid');
        $divide = $this->input->post('divide');
        $discounttype = $this->input->post('discounttype');

        $manualitemdis = $this->input->post('manual_item_discount');

        $reciepe_ids = explode(",", $recipeids);
        $reciepe_qtys = explode(",", $recipeqtys);
        $manualitem_dis = explode(",", $manualitemdis);

        $recipeva_riantids = explode(",", $recipevariantids);
        $addon_subtotal = explode(",", $addonsubtotal);

        $recipe = array();
        $amt = 0;
        if ($reciepe_ids) {
            $disamt = 0;
            foreach ($reciepe_ids as $key => $recipe_id) {
                $recipeDetails = $this->pos_model->getrecipeByID($recipe_id);

                $discount = $this->site->discountMultiple($recipe_id);
                $current_qty = $reciepe_qtys[$key];
                // $manual_item_dis = 0;
                $manual_item_dis = $manualitem_dis[$key];
                // $price_total = $recipeDetails->cost;
                // $price_total = $recipeDetails->cost;
                /**/$finalAmt = (($price_total * $current_qty));

                $variant_id = $recipeva_riantids[$key];
                $addon_sub_total = $addon_subtotal[$key];

                $recipe_Variant_Details = $this->pos_model->getrecipeVarient($recipe_id, $variant_id);
                if (!empty($recipe_Variant_Details)) {
                    $price_total = (($recipe_Variant_Details->price * $current_qty) + $addon_sub_total);
                    $price_total = $price_total - $manual_item_dis;
                } else {
                    $price_total = (($recipeDetails->cost * $current_qty) + $addon_sub_total);
                    $price_total = $price_total - $manual_item_dis;
                }
                $finalAmt = $price_total;
                //print_r($manualitem_dis[$key]);
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

                $recipe[$key]['id'] = $recipe_id;
                $subgroup_id = $recipeDetails->subcategory_id;
                $finalAmt = $finalAmt;
                /*echo "<pre>";
                print_r($finalAmt);echo "<br>";*/
                $recipe[$key]['disamt'] = $this->pos_model->recipe_customer_discount_calculation($recipe_id, $recipeDetails->category_id, $subgroup_id, $finalAmt, $discountid, $discounttype);
                $amt += $recipe[$key]['disamt'];
            }
        }

        echo json_encode($amt);exit;
    }

    public function auto_split_calculate_customerdiscount()
    {
        $recipeids = $this->input->post('recipeids');
        $discountid = $this->input->post('discountid');
        $divide = $this->input->post('divide');
        $reciepe_ids = explode(",", $recipeids);
        $recipe = array();
        $$amt = 0;
        if ($reciepe_ids) {
            $disamt = 0;
            foreach ($reciepe_ids as $key => $recipe_id) {
                $recipeDetails = $this->pos_model->getrecipeByID($recipe_id);

                $discount = $this->site->discountMultiple($recipe_id);

                $price_total = $recipeDetails->cost;
                // $price_total = $recipeDetails->cost;
                $finalAmt = $price_total;
                //var_dump($finalAmt);
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
                if ($TotalDiscount[0] != 0) {
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

                /*$recipe[$key]['id']  = $recipe_id;
                $recipe[$key]['disamt'] = $this->pos_model->recipe_customer_discount_calculation($recipe_id,$recipeDetails->category_id,$finalAmt,$discountid);*/
                $amt += $this->pos_model->recipe_customer_discount_calculation($recipe_id, $recipeDetails->category_id, $recipeDetails->subcategory_id, $finalAmt, $discountid);
            }
        }
        echo json_encode($amt);exit;
    }
    public function autosplit_calculate_customerdiscount()
    {
        $recipeids = $this->input->post('recipeids');
        $recipeqtys = $this->input->post('recipeqtys');

        $discountid = $this->input->post('discountid');
        $divide = $this->input->post('divide');
        $manualitemdis = $this->input->post('manualitemdis');

        $recipevariantids = $this->input->post('recipe_variant_ids');
        $addonsubtotal = $this->input->post('addon_subtotal');
        $discounttype = $this->input->post('discounttype');

        $reciepe_ids = explode(",", $recipeids);
        $reciepe_qtys = explode(",", $recipeqtys);

        $addon_subtotal = explode(",", $addonsubtotal);
        $manualitem_dis = explode(",", $manualitemdis);
        $recipeva_riantids = explode(",", $recipevariantids);
        $recipe = array();
        $amt = 0;
        if ($reciepe_ids) {
            $disamt = 0;
            foreach ($reciepe_ids as $key => $recipe_id) {
                $recipeDetails = $this->pos_model->getrecipeByID($recipe_id);

                $variant_id = $recipeva_riantids[$key];
                $addon_sub_total = $addon_subtotal[$key];
                $manual_item_dis = $manualitem_dis[$key];
                $discount = $this->site->discountMultiple($recipe_id);
                $current_qty = $reciepe_qtys[$key];
                $recipe_Variant_Details = $this->pos_model->getrecipeVarient($recipe_id, $variant_id);
                if (!empty($recipe_Variant_Details)) {

                    $price_total = (($recipe_Variant_Details->price / $divide * $current_qty) + $addon_sub_total);
                    $price_total = ($price_total) - $manual_item_dis;
                } else {

                    $price_total = (($recipeDetails->cost / $divide * $current_qty) + $addon_sub_total);
                    $price_total = ($price_total) - $manual_item_dis;
                }
                $finalAmt = $price_total;
// var_dump($finalAmt);
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
                if (@$TotalDiscount[0] != 0) {
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

                /*$recipe[$key]['id']  = $recipe_id;
                $recipe[$key]['disamt'] = $this->pos_model->recipe_customer_discount_calculation($recipe_id,$recipeDetails->category_id,$finalAmt,$discountid);*/
                $subgroup_id = $recipeDetails->subcategory_id;
                $amt += $this->pos_model->recipe_customer_discount_calculation($recipe_id, $recipeDetails->category_id, $subgroup_id, $finalAmt, $discountid, $discounttype);
            }
        }
        // die;
        echo json_encode($amt);exit;
    }
    public function change_table_number($cancel_remarks = null, $sale_id = null)
    {
        $change_split_id = $this->input->post('change_split_id');
        $changed_table_id = $this->input->post('changed_table_id');

        $result = $this->pos_model->change_table($change_split_id, $changed_table_id);

        if ($result == true) {
            $msg = 'success';

        } else {
            $msg = 'error';
            $status = 'error';
        }
        $this->sma->send_json(array('msg' => $msg, 'status' => $msg));
    }
    public function change_customer_number($cancel_remarks = null, $sale_id = null)
    {
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
    public function get_splits_for_merge($current_split = null)
    {
        $current_split = $this->input->get('current_split');

        $data = $this->site->getsplitsformerge($current_split);
/*echo "<pre>";
print_r($data);die;*/
        if ($data) {
            $msg = 'success';

        } else {
            $msg = 'error';

        }
        $this->sma->send_json(array('msg' => $msg, 'data' => $data));
    }
    public function multiple_splits_mergeto_singlesplit()
    {
        $merge_splits = $this->input->post('merge_splits');
        $current_split = $this->input->post('current_split');
        $merge_table_id = $this->input->post('merge_table_id');

        $result = $this->pos_model->merger_multiple_to_single_split($merge_splits, $current_split, $merge_table_id);

        if ($result == true) {
            $msg = 'success';

        } else {
            $msg = 'error';
            $status = 'error';
        }
        $this->sma->send_json(array('msg' => $msg, 'status' => $msg));
    }
    public function test()
    {
        $d_q = $this->db->get_where('deposits', array('company_id' => 35, 'credit_balance!=' => 0))->result_array();
        $amountpayable = 59.16;
        foreach ($d_q as $dep => $depositRow) {
            if ($amountpayable <= $depositRow['credit_balance']) {
                $payableamt = $amountpayable;
                $this->db->set('credit_balance', 'credit_balance-' . $payableamt, false);
                $this->db->set('credit_used', 'credit_used+' . $payableamt, false);
                $this->db->where('id', $depositRow['id']);
                $this->db->update('deposits'); //echo 'exit';exit;
                break;
            } else {
                $payableamt = $depositRow['credit_balance'];
                $this->db->set('credit_balance', 'credit_balance-' . $payableamt, false);
                $this->db->set('credit_used', 'credit_used+' . $payableamt, false);
                $this->db->where('id', $depositRow['id']);
                $this->db->update('deposits');
                $amountpayable = $amountpayable - $payableamt;
            }
        }
        print_R($this->db->error());
    }
    public function billprint()
    {
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
            select('r.name table,o.reference_no,c.name customer,u.first_name,o.seats_id')
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
        $bill_items['seats_id'] = $orders['seats_id'];
        $this->data['bill_items'] = $bill_items;
        // $this->data['discounnames'] = $this->pos_model->getBillDiscountNamesbysplitname($split_id);
        $this->data['discounnames'] = $order_discount_input_seletedtext;
        $this->data['order_discount_input'] = $order_discount_input;
        $this->data['manual_discount_amount'] = $manual_discount_amount;
        $this->data['splits'] = $split_id;

        if ($this->pos_settings->bill_print_format == 1) {
            $this->load->view($this->theme . 'pos/print_bill', $this->data, false);
        } elseif ($this->pos_settings->bill_print_format == 3) {
            $this->load->view($this->theme . 'pos/indai_bill/print_bill', $this->data, false);
        } elseif ($this->pos_settings->bill_print_format == 4) {
            $this->load->view($this->theme . 'pos/local_bill/print_bill', $this->data, false);
        } else {
            $this->load->view($this->theme . 'pos/row_discount/print_bill', $this->data, false);
        }
    }
    public function set_unique_discount()
    {
        $disid = $this->input->post('disid');
        $this->site->set_unique_discount($disid);
        echo json_encode(array('success' => 1, 'msg' => lang('unique_discount_selected_successfully')));
    }
    /*loyalty start*/
    public function loyalty()
    {

        $this->data['loyaltycustomer'] = $this->pos_model->getLoyaltycustomer();
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->load->view($this->theme . 'pos/loyaltycustomer', $this->data);

    }
    public function getLoyaltyCardNo()
    {
        $customer_id = $this->input->get('customer_id');
        $Loyalty_Cards = $this->pos_model->getLoyaltyCardNo();
        echo json_encode(array('cus_dis' => $Loyalty_Cards));exit;
    }

    public function addcustomervoucher()
    {

        if (!empty($this->input->post())) {

            $customername = $this->input->post('customername');
            $customerpoints = $this->input->post('customerpoints');
            $loyalty_card_name = $this->input->post('loyalty_card_name');
            $loyalty_card = $this->input->post('loyalty_card');
            $customer_id = $this->input->post('customer_id');

            $ExpiryDate = $this->site->getLoyaltyCardByID($loyalty_card);
            // print_r($ExpiryDate->expiry_date);die;
            $cus_loyalty = array(
                'loyalty_card_id' => $this->input->post('loyalty_card'),
                'loyalty_card_no' => $this->input->post('loyalty_card_name'),
                'expiry_date' => $ExpiryDate->expiry_date,
            );

            $response = $this->pos_model->LoyaltyCardIssuetoCustomer($customer_id, $cus_loyalty, $loyalty_card);

            if (!empty($response)) {
                $this->session->set_flashdata('success', lang('issue_loyalty_card_success'));
                admin_redirect("pos/loyalty");
            } else {
                $this->session->set_flashdata('error', lang('loyalty_card_not_issued'));
                admin_redirect("pos/loyalty");
            }
        } else {
            admin_redirect("pos/loyalty");
        }
    }

    public function get_loyalty_points($customer_id)
    {

        $loyaltypoints = $this->pos_model->getLoyaltypointsBycustomer($customer_id);
        $redemption = $this->pos_model->LoyaltyRedemtiondetails($customer_id);
        // $this->sma->send_json($loyaltypoints);

        echo json_encode(array('points' => $loyaltypoints, 'redemption' => $redemption));exit;
    }
    public function getCheckLoyaltyAvailable($customer_id)
    {
        $loyaltycheck = $this->site->getCheckLoyaltyAvailable($customer_id);
        $this->sma->send_json($loyaltycheck);
    }

    public function validate_loyalty_card()
    {
        //$this->sma->checkPermissions();
        $customer_id = $this->input->get('customer_id');
        $redempamt = $this->input->get('redemption');
        $bal_amount = $this->input->get('bal_amount');
        /*echo $customer_id;
        echo $redemption;die;*/
        $redemption = $this->pos_model->LoyaltyRedemtion($customer_id, $redempamt, $bal_amount);
        if ($redemption) {
            $this->sma->send_json($redemption);
        } else {
            $this->sma->send_json(false);
        }
    }

    public function validate_loyalty_card_old()
    {
        //$this->sma->checkPermissions();

        if ($lc = $this->site->getLoyaltyCardByNO($no)) {
            if ($lc->expiry_date) {
                if ($lc->expiry_date >= date('Y-m-d')) {
                    $this->sma->send_json($lc);
                } else {
                    $this->sma->send_json(false);
                }
            } else {
                $this->sma->send_json($lc);
            }
        } else {
            $this->sma->send_json(false);
        }
    }
    public function loyalty_customer($term = null, $limit = null)
    {
        // $this->sma->checkPermissions('index');
        if ($this->input->get('term')) {
            $term = $this->input->get('term', true);
        }
        if (strlen($term) < 1) {
            return false;
        }
        $limit = $this->input->get('limit', true);
        $rows['results'] = $this->pos_model->getLoyaltyCustomerByCardNo($term, $limit);
        $this->sma->send_json($rows);
    }
    /*loyalty end */

    //function getBillItems($biliID){
    // $this->data['bill_items'] =  $this->pos_model->getBillItems($biliID);
    //  $this->load->view($this->theme . 'pos/deleteBills', $this->data);
    //}
    //function delete_bill_item($bi_id){
    //  $this->pos_model->delete_bill_item($bi_id);
    //  redirect($_SERVER['HTTP_REFERER']);
    //}
    //function getBill($biliID){
    // $this->data['bils'] =  $this->pos_model->getNewBillCalculation($biliID);
    // $this->data['bill_id'] = $biliID;
    //  $this->load->view($this->theme . 'pos/bills_details', $this->data);
    //}
    //function update_bill($bill_id){
    //  $this->pos_model->getNewBillCalculation($bill_id,'update');
    //}

    public function bill_report()
    {
        $this->sma->checkPermissions();
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['billers'] = $this->site->getAllCompanies('biller');

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('bill_details_report')));
        $meta = array('page_title' => lang('modify_bills'), 'bc' => $bc);

        $this->page_construct('pos/bill_report', $meta, $this->data);

    }

    public function get_bill_reports($start = null, $end = null, $bill_no = null, $warehouse_id = null, $varient_id = null)
    {
        $this->sma->checkPermissions('bill_details', true);
        $start = $this->input->post('start');
        $end = $this->input->post('end');
        $bill_no = $this->input->post('bill_no');
        $warehouse_id = $this->input->post('warehouse_id');
        $table_whitelisted = $this->input->post('table_whitelisted');
        $limit = $this->input->post('pagelimit');
        $offsetSegment = 4;
        $offset = $this->uri->segment($offsetSegment, 0);

        $this->session->set_userdata('table_whitelisted', $table_whitelisted);
        $this->session->set_userdata('start_date', $this->input->post('start'));
        $this->session->set_userdata('end_date', $this->input->post('end'));

        $data = '';
        if ($start != '' && $end != '') {

            $data = $this->pos_model->getBillReports($start, $end, $bill_no, $warehouse_id, $table_whitelisted, $limit, $offset);
            $p_total = $data['print_total'];
            $dp_total = $data['dontprint_total'];
            if (!empty($data['data'])) {
                $bill = $data['data'];
            } else {
                $bill = 'empty';
            }
        } else {
            $bill = 'error';
        }
        $total = $data['total'];
        $pagination = $this->pagination('pos/get_bill_reports', $limit, $offsetSegment, $total);
        $this->sma->send_json(array('bill_details' => $bill, 'pagination' => $pagination, 'p_total' => $p_total, 'dp_total' => $dp_total));

    }
    public function pagination($url, $per, $segment, $total)
    {
        $config['base_url'] = admin_url($url);
        $config['per_page'] = $per;
        $config['uri_segment'] = $segment;
        $config['total_rows'] = $total;
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['prev_link'] = 'Previous';
        $config['next_link'] = 'Next';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        //$config['num_links'] = 3;
        $config['first_link'] = false;
        $config['last_link'] = false;
        $limit = $config['per_page'];
        $offset = $this->uri->segment($config['uri_segment'], 0);
        $offset = ($offset > 1) ? (($offset - 1) * $limit) : 0;

        $this->pagination->initialize($config);
        return $this->pagination->create_links();
    }
    public function deleteDontPrintBill($bill_no)
    {
        $bill_nos[] = $bill_no;
        $this->pos_model->deleteDontPrintBill($bill_nos);
        echo 1;
    }
    public function change_toPrint($bill_no)
    {

        $this->pos_model->change_toPrintBill($bill_no);
        echo 1;
    }
    public function edit_dontprint($bill_no)
    {
        //$this->sma->checkPermissions();

        if (isset($_POST) && !empty($_POST)) {

            if (isset($_POST['recipe_id'])) {
                $cnt = count($_POST['recipe_id']);
                $bill_items = array();
                for ($i = 0; $i < $cnt; $i++) {
                    $bill_items[$i]['bil_id'] = $_POST['item_bill_id'][$i];
                    $bill_items[$i]['sale_item_id'] = $_POST['order_item_id'][$i];
                    $bill_items[$i]['recipe_id'] = $_POST['recipe_id'][$i];
                    $bill_items[$i]['recipe_code'] = $_POST['recipe_code'][$i];
                    $bill_items[$i]['recipe_name'] = $_POST['recipe_name'][$i];
                    $bill_items[$i]['recipe_type'] = $_POST['recipe_type'][$i];
                    $bill_items[$i]['net_unit_price'] = $_POST['unit_price'][$i];
                    $bill_items[$i]['unit_price'] = $_POST['net_unit_price'][$i];
                    $bill_items[$i]['quantity'] = $_POST['quantity'][$i];
                    $bill_items[$i]['warehouse_id'] = $_POST['warehouse_id'][$i];
                    $bill_items[$i]['tax'] = $_POST['item_tax'][$i];
                    $bill_items[$i]['tax_type'] = $_POST['item_tax_type'][$i];
                    $bill_items[$i]['item_discount'] = $_POST['item_discount'][$i];
                    $bill_items[$i]['off_discount'] = $_POST['offer_discount'][$i];
                    $bill_items[$i]['input_discount'] = $_POST['customer_discount'][$i];
                    $bill_items[$i]['manual_item_discount'] = $_POST['manual_discount'][$i];
                    $bill_items[$i]['manual_item_discount_val'] = $_POST['manual_discount_val'][$i];
                    $bill_items[$i]['manual_item_discount_per_val'] = $_POST['recipe_id'][$i];
                    $bill_items[$i]['subtotal'] = $_POST['item_subtotal'][$i];
                    $bill_items[$i]['recipe_variant'] = ''; //$_POST['recipe_variant'][$i];
                    $bill_items[$i]['recipe_variant_id'] = 0; //$_POST['recipe_variant_id'][$i];

                    //to update order items
                    $order_items[$i]['update']['id'] = $_POST['order_item_id'][$i];
                    $order_items[$i]['update']['quantity'] = $_POST['quantity'][$i];
                    $order_items[$i]['update']['subtotal'] = $_POST['unit_price'][$i] * $_POST['quantity'][$i];

                    if ($_POST['ordered_quantity'][$i] > $_POST['quantity'][$i]) {
                        // to insert cancelled order items
                        $order_items[$i]['insert'] = $this->pos_model->getOrderItemsDetails($_POST['order_item_id'][$i]);
                        $order_items[$i]['insert']['item_status'] = 'cancel';
                        $order_items[$i]['insert']['order_item_cancel_status'] = 1;
                        $order_items[$i]['insert']['order_item_cancel_id'] = 1;
                        $order_items[$i]['insert']['quantity'] = $_POST['ordered_quantity'][$i] - $_POST['quantity'][$i];
                        $order_items[$i]['insert']['subtotal'] = $_POST['unit_price'][$i] * $order_items[$i]['cancel_quantity'];
                    }

                }

                ///update tables : bils,sales,orders

                ///delete tables : order_items,bil_items,payment,sale currency

                // Bills table
                $bill_id = $_POST['bill_id'];
                $bill_data['total'] = $_POST['bill_net_total'];
                $bill_data['total_discount'] = $_POST['bill_total_discount'];
                $bill_data['manual_item_discount'] = $_POST['bill_manual_item_discount'];
                $bill_data['customer_discount_id'] = $_POST['bill_customer_discount_id'];
                $bill_data['customer_discount_status'] = '';
                $bill_data['total_tax'] = $_POST['bill_tax'];
                $bill_data['grand_total'] = $_POST['bill_grandtotal'];
                $bill_data['round_total'] = $_POST['bill_grandtotal'];
                $bill_data['total_pay'] = $_POST['bill_grandtotal'];
                $bill_data['balance'] = 0;
                $bill_data['total_items'] = $_POST['bill_total_items'];
                $bill_data['paid'] = $_POST['bill_grandtotal'];
                $bill_data['discount_type'] = $_POST['bill_customer_discount_type'];
                $bill_data['discount_val'] = $_POST['bill_customer_discount_val'];
                $bill_data['unique_discount'] = 0;
                $bill_data['bbq_cover_discount'] = 0;

                $sale_id = $_POST['sale_id'];
                $sale_data['paid'] = $_POST['bill_grandtotal'];
                $sale_data['grand_total'] = $_POST['bill_grandtotal'];

                //$sale = $this->db->get_where('sales',array('id'=>$sale_id))->row();
                //$split_id = $sale->sales_split_id;
                //
                //$order_data['total'] = $_POST['bill_grandtotal'];
                //$order_data['grand_total'] = $_POST['bill_grandtotal'];
                //$order_data['total_items'] = $_POST['bill_total_items'];
                $p_cnt = count($_POST['payment']);
                $default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
                $currency = $this->site->getAllCurrencies();
                $c_cnt = 0;
                for ($p = 0; $p < $p_cnt; $p++) {
                    $payment[$p]['payment_id'] = $_POST['payment'][$p]['id'];
                    $payment[$p]['amount'] = $_POST['payment'][$p]['amount'];
                    $payment[$p]['pos_paid'] = $_POST['payment'][$p]['amount'];
                    $payment[$p]['pos_balance'] = 0;
                    $payment[$p]['type'] = 'received';
                    $payment[$p]['amount_exchange'] = 0;
                    $payment[$p]['paid_by'] = $_POST['payment'][$p]['paid_by'];
                    //$payment[$p]['bill_id'] =  $bill_id;
                    //$payment[$p]['sale_id'] =  $sale_id;

                    foreach ($currency as $currency_row) {

                        if ($default_currency_data->code == $currency_row->code) {
                            $multi_currency[$c_cnt] = array(

                                'sale_id' => $sale_id,
                                'bil_id' => $bill_id,
                                'currency_id' => $currency_row->id,
                                'currency_rate' => $currency_row->rate,
                                'amount' => $_POST['payment'][$p]['amount'],
                            );
                        } else {
                            $multi_currency[$c_cnt] = array(

                                'sale_id' => $sale_id,
                                'bil_id' => $bill_id,
                                'currency_id' => $currency_row->id,
                                'currency_rate' => $currency_row->rate,
                                'amount' => 0,
                            );
                        }
                        $c_cnt++;
                    }

                } //echo '<pre>';print_R($multi_currency);exit;

            }

            //echo '<pre>';
            //print_R($bill_data);
            //print_R($sale_data);
            //print_R($bill_items);
            //print_R($order_items);
            //exit;
            $this->pos_model->edit_bill($bill_id, $sale_id, $bill_data, $sale_data, $bill_items, $order_items, $payment, $multi_currency);

            $this->session->set_flashdata('message', lang("bill_edited"));
            admin_redirect('pos/bill_report');
        }

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('bill_details_report')));
        $meta = array('page_title' => lang('bill_details_report'), 'bc' => $bc);
        $this->data['bill'] = $this->pos_model->get_dontprintBillData($bill_no);
        $this->data['payments'] = $this->pos_model->get_BillPaymentData($this->data['bill']->id);
        $this->data['off_dis'] = $this->pos_model->getOfferdiscount($this->data['bill']->order_discount_id);
        $this->data['bill_tax'] = $this->pos_model->getBillTax($this->data['bill']->tax_id);
        $this->data['customer_discount'] = $this->site->GetAllcostomerDiscounts();
        $this->page_construct('pos/edit_bill', $meta, $this->data);
    }
    public function getCustomerDiscount()
    {
        $discountid = $this->input->post('discount_id');
        $recipe_ids = $this->input->post('recipe_ids');
        $discount = array();
        foreach ($recipe_ids as $k => $id) {
            $recipe = $this->pos_model->getrecipeByID($id);
            $id = $recipe->id;
            $category_id = $recipe->category_id;
            $subcategory_id = $recipe->subcategory_id;
            $discount[$id] = $this->pos_model->getCategory_GroupDiscount($category_id, $subcategory_id, $id, $discountid);
            //print_R($discount);
        }
        echo json_encode($discount);

    }
    public function archived_bills()
    {
        $this->sma->checkPermissions();
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['billers'] = $this->site->getAllCompanies('biller');

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('archived_bills')));
        $meta = array('page_title' => lang('archived_bills'), 'bc' => $bc);

        $this->page_construct('pos/archived_bills', $meta, $this->data);

    }

    public function get_archived_bills($start = null, $end = null, $bill_no = null, $warehouse_id = null, $varient_id = null)
    {
        $this->sma->checkPermissions('bill_details', true);
        $start = $this->input->post('start');
        $end = $this->input->post('end');
        $bill_no = $this->input->post('bill_no');
        $warehouse_id = $this->input->post('warehouse_id');
        $bill_action = $this->input->post('bill_action');
        $limit = $this->input->post('pagelimit');
        $offsetSegment = 4;
        $offset = $this->uri->segment($offsetSegment, 0);

        $this->session->set_userdata('bill_action', $bill_action);
        $this->session->set_userdata('start_date', $this->input->post('start'));
        $this->session->set_userdata('end_date', $this->input->post('end'));

        $data = '';
        if ($start != '' && $end != '') {

            $data = $this->pos_model->getArchived_BillReports($start, $end, $bill_no, $warehouse_id, $bill_action, $limit, $offset);
            $p_total = $data['print_total'];
            $dp_total = $data['dontprint_total'];
            if (!empty($data['data'])) {
                $bill = $data['data'];
            } else {
                $bill = 'empty';
            }
        } else {
            $bill = 'error';
        }
        $total = $data['total'];
        $pagination = $this->pagination('pos/get_archived_bills', $limit, $offsetSegment, $total);
        $this->sma->send_json(array('bill_details' => $bill, 'pagination' => $pagination));

    }
    public function restore_deleted_bill($billid)
    {

        $this->pos_model->restore_deleted_bill($billid);
        $this->session->set_flashdata('message', lang("bill_restored"));
        admin_redirect('pos/archived_bills');
    }
    public function restore_modified_bill($billid)
    {
        $this->pos_model->restore_modified_bill($billid);
    }

    public function getItem_report_SubCategories()
    {
        $category_id = $this->input->post('category_id');
        //if($type=="standard" || $type=="production" || $type=="combo" || $type=="quick_service"){
        if ($rows = $this->pos_model->getItemSubCategories($category_id)) {
            $data = json_encode($rows);
        } else {
            $data = false;

        }
        echo $data;
        //}else{
        //    if ($rows = $this->recipe_model->getPurchaseSubCategories($category_id)) {
        //
        //    $data = json_encode($rows);
        //    } else {
        //    $data = false;
        //
        //    }
        //    echo $data;
        //}
    }
    public function test1()
    {
        $bill_number = '0001';
        $this->site->latest_bill($bill_number);
    }
	
	//************************************  consolidate start  ******************************//
	 public function ajaxorder_billing_all()
    {
        $table_id = (isset($_GET['table']) && $_GET['table'] != '') ? $this->input->get('table') : null;
		$this->data['table_id']=$table_id;
        $sales_type_id = !empty($this->input->get('type')) ? $this->input->get('type') : '';
		if(!empty($table_id)){
        if ($sales_type_id == 1) {
            $this->data['sales_type'] = 'Dine In';
        } elseif ($sales_type_id == 2) {
            $this->data['sales_type'] = 'Take Away';
        } elseif ($sales_type_id == 3) {
            $this->data['sales_type'] = 'Door Delivery';
        }

        if ($sales_type_id) {
            $this->data['sales'] = $this->pos_model->getAllSalesWithbiller($sales_type_id, $table_id);
        }
		}
        $this->load->view($this->theme . 'pos/consolidate/orderbiller_ajax', $this->data);
    }
    public function ajax_tables_all()
    {
        $this->data['areas'] = $this->pos_model->getTablelist($this->session->userdata('warehouse_id'));
        if ($this->pos_settings->table_display_option == 0) {
            $this->load->view($this->theme . 'pos/consolidate/tables_ajax', $this->data);
        } else {
            $this->load->view($this->theme . 'pos/consolidate/tables_ajax_without_icon', $this->data);
        }
    }
    public function ajax_table_byID_all()
    {
        $id = $this->input->post('id');
        $this->data['areas'] = $this->pos_model->getTable_byID($id, $this->session->userdata('warehouse_id'));
        $this->load->view($this->theme . 'pos/consolidate/tables_single_ajax', $this->data);
    }
    public function consolidate(){
        $t = $this->sma->checkPermissions('index');
        $order = !empty($_GET['order']) ? $_GET['order'] : 1;
        $table = !empty($_GET['table']) ? $_GET['table'] : '';
        $split = !empty($_GET['split']) ? $_GET['split'] : '';
		$this->data['sprequest'] = !empty($_GET['spr']) ? $_GET['spr'] : '';
		$this->data['get_order_type'] = $order;
        $same_customer = !empty($_GET['same_customer']) ? $_GET['same_customer'] : '';
        if (!$this->pos_settings->default_biller || !$this->pos_settings->default_customer || !$this->pos_settings->default_category) {
            $this->session->set_flashdata('warning', lang('please_update_settings'));
            admin_redirect('pos/settings');
        }
        $user_group = $this->pos_model->getUserByID($this->session->userdata('user_id'));
        $gp = $this->settings_model->getGroupPermissions($user_group->group_id);
        $this->data['sid'] = $this->input->get('suspend_id') ? $this->input->get('suspend_id') : $sid;
        $did = $this->input->post('delete_id') ? $this->input->post('delete_id') : null;
        $suspend = $this->input->post('suspend') ? true : false;
        $count = $this->input->post('count') ? $this->input->post('count') : null;
        $duplicate_sale = $this->input->get('duplicate') ? $this->input->get('duplicate') : null;
        //validate form input
        $this->form_validation->set_rules('customer', $this->lang->line("customer"), 'trim|required');
        $this->form_validation->set_rules('warehouse', $this->lang->line("warehouse"), 'required');
        $this->form_validation->set_rules('biller', $this->lang->line("biller"), 'required');
        if (!empty($order)) {
            if ($order == 1 && !empty($table)) {
                $table_view = 'table';
            } elseif ($order == 2) {
                $table_view = 'pos';
            } elseif ($order == 3) {
                $table_view = 'pos';
            }
            if (isset($table_view) == 'pos') {
                if ($this->form_validation->run() == true) {
                    $date = date('Y-m-d H:i:s');
                    $warehouse_id = $this->input->post('warehouse');
                    $customer_id = $this->input->post('customer');
                    $biller_id = $this->input->post('biller');
                    $total_items = $this->input->post('total_items');

                    $payment_term = 0;
                    $due_date = date('Y-m-d', strtotime('+' . $payment_term . ' days'));
                    $shipping = $this->input->post('shipping') ? $this->input->post('shipping') : 0;
                    $customer_details = $this->site->getCompanyByID($customer_id);
                    $customer = $customer_details->company != '-' ? $customer_details->company : $customer_details->name;
                    $biller_details = $this->site->getCompanyByID($biller_id);
                    $biller = $biller_details->company != '-' ? $biller_details->company : $biller_details->name;
                    $note = $this->sma->clear_tags($this->input->post('pos_note'));
                    $staff_note = $this->sma->clear_tags($this->input->post('staff_note'));
                    $reference = $this->site->getReference('pos');

                    $total = 0;
                    $recipe_tax = 0;
                    $recipe_discount = 0;
                    $digital = false;
                    $gst_data = [];
                    $total_cgst = $total_sgst = $total_igst = 0;
                    $i = isset($_POST['recipe_code']) ? sizeof($_POST['recipe_code']) : 0;

                    for ($r = 0; $r < $i; $r++) {

                        $item_addon = isset($_POST['recipe_addon'][$r]) && $_POST['recipe_addon'][$r] != 'false' ? $_POST['recipe_addon'][$r] : null;

                        $item_id = $_POST['recipe_id'][$r];
                        $item_type = $_POST['recipe_type'][$r];
                        $item_code = $_POST['recipe_code'][$r];

                        $buy_id = $_POST['buy_id'][$r];
                        $buy_quantity = $_POST['buy_quantity'][$r];
                        $get_item = $_POST['get_item'][$r];
                        $get_quantity = $_POST['get_quantity'][$r];
                        $total_get_quantity = $_POST['total_get_quantity'][$r];

                        $item_name = $_POST['recipe_name'][$r];
                        $item_comment = $_POST['recipe_comment'][$r];
                        $item_option = isset($_POST['recipe_option'][$r]) && $_POST['recipe_option'][$r] != 'false' ? $_POST['recipe_option'][$r] : null;
                        $real_unit_price = $this->sma->formatDecimal($_POST['real_unit_price'][$r]);
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
                            $pr_discount = $this->site->calculateDiscount($item_discount, $unit_price);
                            $unit_price = $this->sma->formatDecimal($unit_price - $pr_discount);
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

                            $recipe = array(
                                'recipe_id' => $item_id,
                                'recipe_code' => $item_code,
                                'recipe_name' => $item_name,
                                'recipe_type' => $item_type,
                                'option_id' => $item_option,
                                'addon_id' => $item_addon,
                                'buy_id' => $buy_id,
                                'buy_quantity' => $buy_quantity,
                                'get_item' => $get_item,
                                'get_quantity' => $get_quantity,
                                'total_get_quantity' => $total_get_quantity,
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
                                'subtotal' => $this->sma->formatDecimal($subtotal),
                                'serial_no' => $item_serial,
                                'real_unit_price' => $real_unit_price,
                                'comment' => $item_comment,
                            );

                            $recipe[] = ($recipe + $gst_data);
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
                    $data = array('date' => $date,
                        'reference_no' => $reference,
                        'customer_id' => $customer_id,
                        'customer' => $customer,
                        'biller_id' => $biller_id,
                        'biller' => $biller,
                        'warehouse_id' => $warehouse_id,
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
                        'sale_status' => 'Process',
                        'payment_status' => $payment_status,
                        'payment_term' => $payment_term,
                        'rounding' => $rounding,
                        'suspend_note' => $this->input->post('suspend_note'),
                        'pos' => 1,
                        'paid' => $this->input->post('amount-paid') ? $this->input->post('amount-paid') : 0,
                        'created_by' => $this->session->userdata('user_id'),
                        'hash' => hash('sha256', microtime() . mt_rand()),
                    );
                    if ($this->Settings->indian_gst) {
                        $data['cgst'] = $total_cgst;
                        $data['sgst'] = $total_sgst;
                        $data['igst'] = $total_igst;
                    }

                    if (!$suspend) {
                        $p = isset($_POST['amount']) ? sizeof($_POST['amount']) : 0;
                        $paid = 0;
                        for ($r = 0; $r < $p; $r++) {
                            if (isset($_POST['amount'][$r]) && !empty($_POST['amount'][$r]) && isset($_POST['paid_by'][$r]) && !empty($_POST['paid_by'][$r])) {
                                $amount = $this->sma->formatDecimal($_POST['balance_amount'][$r] > 0 ? $_POST['amount'][$r] - $_POST['balance_amount'][$r] : $_POST['amount'][$r]);
                                if ($_POST['paid_by'][$r] == 'deposit') {
                                    if (!$this->site->check_customer_deposit($customer_id, $amount)) {
                                        $this->session->set_flashdata('error', lang("amount_greater_than_deposit"));
                                        redirect($_SERVER["HTTP_REFERER"]);
                                    }
                                }
                                if ($_POST['paid_by'][$r] == 'gift_card') {
                                    $gc = $this->site->getGiftCardByNO($_POST['paying_gift_card_no'][$r]);
                                    $amount_paying = $_POST['amount'][$r] >= $gc->balance ? $gc->balance : $_POST['amount'][$r];
                                    $gc_balance = $gc->balance - $amount_paying;
                                    $payment[] = array(
                                        'date' => $date,
                                        // 'reference_no' => $this->site->getReference('pay'),
                                        'amount' => $amount,
                                        'paid_by' => $_POST['paid_by'][$r],
                                        'cheque_no' => $_POST['cheque_no'][$r],
                                        'cc_no' => $_POST['paying_gift_card_no'][$r],
                                        'cc_holder' => $_POST['cc_holder'][$r],
                                        'cc_month' => $_POST['cc_month'][$r],
                                        'cc_year' => $_POST['cc_year'][$r],
                                        'cc_type' => $_POST['cc_type'][$r],
                                        'cc_cvv2' => $_POST['cc_cvv2'][$r],
                                        'created_by' => $this->session->userdata('user_id'),
                                        'type' => 'received',
                                        'note' => $_POST['payment_note'][$r],
                                        'pos_paid' => $_POST['amount'][$r],
                                        'pos_balance' => $_POST['balance_amount'][$r],
                                        'gc_balance' => $gc_balance,
                                    );

                                } else {
                                    $payment[] = array(
                                        'date' => $date,
                                        // 'reference_no' => $this->site->getReference('pay'),
                                        'amount' => $amount,
                                        'paid_by' => $_POST['paid_by'][$r],
                                        'cheque_no' => $_POST['cheque_no'][$r],
                                        'cc_no' => $_POST['cc_no'][$r],
                                        'cc_holder' => $_POST['cc_holder'][$r],
                                        'cc_month' => $_POST['cc_month'][$r],
                                        'cc_year' => $_POST['cc_year'][$r],
                                        'cc_type' => $_POST['cc_type'][$r],
                                        'cc_cvv2' => $_POST['cc_cvv2'][$r],
                                        'created_by' => $this->session->userdata('user_id'),
                                        'type' => 'received',
                                        'note' => $_POST['payment_note'][$r],
                                        'pos_paid' => $_POST['amount'][$r],
                                        'pos_balance' => $_POST['balance_amount'][$r],
                                    );

                                }

                            }
                        }
                    }
                    if (!isset($payment) || empty($payment)) {
                        $payment = array();
                    }

                    // $this->sma->print_arrays($data, $recipe, $payment);
                }

                if ($this->form_validation->run() == true && !empty($recipe) && !empty($data)) {
                    if ($suspend) {
                        if ($this->pos_model->suspendSale($data, $recipe, $did)) {
                            $this->session->set_userdata('remove_posls', 1);
                            $this->session->set_flashdata('message', $this->lang->line("sale_suspended"));
                            admin_redirect("pos");
                        }
                    } else {
                        if ($sale = $this->pos_model->addSale($data, $recipe, $payment, $did)) {
                            $this->session->set_userdata('remove_posls', 1);
                            $msg = $this->lang->line("sale_added");
                            if (!empty($sale['message'])) {
                                foreach ($sale['message'] as $m) {
                                    $msg .= '<br>' . $m;
                                }
                            }
                            $this->session->set_flashdata('message', $msg);
                            $redirect_to = $this->pos_settings->after_sale_page ? "pos" : "pos/view/" . $sale['sale_id'];
                            if ($this->pos_settings->auto_print) {
                                if ($this->Settings->remote_printing != 1) {
                                    $redirect_to .= '?print=' . $sale['sale_id'];
                                }
                            }
                            admin_redirect($redirect_to);
                        }
                    }
                } else {
                    $this->data['old_sale'] = null;
                    $this->data['oid'] = null;
                    if ($duplicate_sale) {
                        if ($old_sale = $this->pos_model->getInvoiceByID($duplicate_sale)) {
                            $inv_items = $this->pos_model->getSaleItems($duplicate_sale);
                            $this->data['oid'] = $duplicate_sale;
                            $this->data['old_sale'] = $old_sale;
                            $this->data['message'] = lang('old_sale_loaded');
                            $this->data['customer'] = $this->pos_model->getCompanyByID($old_sale->customer_id);
                        } else {
                            $this->session->set_flashdata('error', lang("bill_x_found"));
                            admin_redirect("pos");
                        }
                    }
                    $this->data['suspend_sale'] = null;
                    if ($sid) {
                        if ($suspended_sale = $this->pos_model->getOpenBillByID($sid)) {
                            $inv_items = $this->pos_model->getSuspendedSaleItems($sid);
                            $this->data['sid'] = $sid;
                            $this->data['suspend_sale'] = $suspended_sale;
                            $this->data['message'] = lang('suspended_sale_loaded');
                            $this->data['customer'] = $this->pos_model->getCompanyByID($suspended_sale->customer_id);
                            $this->data['reference_note'] = $suspended_sale->suspend_note;
                        } else {
                            $this->session->set_flashdata('error', lang("bill_x_found"));
                            admin_redirect("pos");
                        }
                    }

                    if (($sid || $duplicate_sale) && $inv_items) {
                        // krsort($inv_items);
                        $c = rand(100000, 9999999);
                        foreach ($inv_items as $item) {
                            $row = $this->site->getrecipeByID($item->recipe_id);

                            $buy = $this->site->checkBuyget($row->id);
                            if (!empty($buy)) {
                                $row->buy_id = $buy->id;
                                $row->buy_quantity = $buy->buy_quantity;
                                $row->get_item = $buy->get_item;
                                $row->get_quantity = $buy->get_quantity;
                                $row->total_get_quantity = $buy->get_quantity;
                                $total_quantity = $x_quantity % $y_quantity;
                                $x_quantity = ($x_quantity - $total_quantity) / $y_quantity;
                                $total_get_quantity = $x_quantity * $b_quantity;
                                $row->total_get_quantity = $total_get_quantity;

                                $row->free_recipe = $buy->free_recipe;
                            } else {
                                $row->buy_id = 0;
                                $row->get_item = 0;
                                $row->buy_quantity = 0;
                                $row->get_quantity = 0;
                                $row->total_get_quantity = 0;
                                $row->free_recipe = '';
                            }

                            if (!$row) {
                                $row = json_decode('{}');
                                $row->tax_method = 0;
                                $row->quantity = 0;
                            } else {
                                $category = $this->site->getCategoryByID($row->category_id);
                                $row->category_name = $category->name;
                                unset($row->cost, $row->details, $row->recipe_details, $row->image, $row->barcode_symbology, $row->cf1, $row->cf2, $row->cf3, $row->cf4, $row->cf5, $row->cf6, $row->supplier1price, $row->supplier2price, $row->cfsupplier3price, $row->supplier4price, $row->supplier5price, $row->supplier1, $row->supplier2, $row->supplier3, $row->supplier4, $row->supplier5, $row->supplier1_part_no, $row->supplier2_part_no, $row->supplier3_part_no, $row->supplier4_part_no, $row->supplier5_part_no);
                            }
                            $pis = $this->site->getPurchasedItems($item->recipe_id, $item->warehouse_id, $item->option_id);
                            if ($pis) {
                                foreach ($pis as $pi) {
                                    $row->quantity += $pi->quantity_balance;
                                }
                            }
                            $row->id = $item->recipe_id;
                            $row->code = $item->recipe_code;
                            $row->name = $item->recipe_name;
                            $row->type = $item->recipe_type;
                            $row->quantity += $item->quantity;
                            $row->discount = $item->discount ? $item->discount : '0';
                            $row->price = $this->sma->formatDecimal($item->net_unit_price + $this->sma->formatDecimal($item->item_discount / $item->quantity));
                            $row->unit_price = $row->tax_method ? $item->unit_price + $this->sma->formatDecimal($item->item_discount / $item->quantity) + $this->sma->formatDecimal($item->item_tax / $item->quantity) : $item->unit_price + ($item->item_discount / $item->quantity);
                            $row->real_unit_price = $item->real_unit_price;
                            $row->base_quantity = $item->quantity;
                            $row->base_unit = isset($row->unit) ? $row->unit : $item->recipe_unit_id;
                            $row->base_unit_price = $row->price ? $row->price : $item->unit_price;
                            $row->unit = $item->recipe_unit_id;
                            $row->qty = $item->unit_quantity;
                            $row->tax_rate = $item->tax_rate_id;
                            $row->serial = $item->serial_no;
                            $row->option = $item->option_id;
                            $row->addon = $item->addon_id;
                            $options = $this->pos_model->getrecipeOptions($row->id, $item->warehouse_id);
                            $addons = $this->pos_model->getrecipeAddons($row->id);

                            if ($options) {
                                $option_quantity = 0;
                                foreach ($options as $option) {
                                    $pis = $this->site->getPurchasedItems($row->id, $item->warehouse_id, $item->option_id);
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

                            $row->comment = isset($item->comment) ? $item->comment : '';
                            $row->ordered = 1;
                            $combo_items = false;
                            if ($row->type == 'combo') {
                                $combo_items = $this->pos_model->getrecipeComboItems($row->id, $item->warehouse_id);
                            }
                            $units = $this->site->getUnitsByBUID($row->base_unit);
                            $tax_rate = $this->site->getTaxRateByID($row->tax_rate);
                            $ri = $this->Settings->item_addition ? $row->id : $c;

                            $pr[$ri] = array('id' => $c, 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")",
                                'row' => $row, 'combo_items' => $combo_items, 'tax_rate' => $tax_rate, 'units' => $units, 'options' => $options, 'addons' => $addons);
                            $c++;
                        }

                        $this->data['items'] = json_encode($pr);

                    } else {
                        $this->data['customer'] = $this->pos_model->getCompanyByID($this->pos_settings->default_customer);
                        $this->data['reference_note'] = null;
                    }

                    $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
                    $this->data['message'] = isset($this->data['message']) ? $this->data['message'] : $this->session->flashdata('message');

                    // $this->data['biller'] = $this->site->getCompanyByID($this->pos_settings->default_biller);
                    $this->data['billers'] = $this->site->getAllCompanies('biller');
                    $this->data['sales_types'] = $this->site->getAllSalestype();
                    $this->data['warehouses'] = $this->site->getAllWarehouses();
                    $this->data['tax_rates'] = $this->site->getAllTaxRates();
                    $this->data['user'] = $this->site->getUser();
                    $this->data["tcp"] = $this->pos_model->recipe_count($this->pos_settings->default_category, $this->session->userdata('warehouse_id'));
                    $this->data["sub_cat"] = $this->site->getrecipeSubCategories($this->pos_settings->default_category);
                    $this->data['recipe'] = $this->ajaxrecipe_consolidate($this->pos_settings->default_category, $this->session->userdata('warehouse_id'), $this->data["sub_cat"][0]->id, $brand_id = null, $order);
                    if ($this->pos_settings->sales_item_in_pos == 1) {
                        $this->data['categories'] = $this->site->getAllrecipeCategories();
                    } else { //by day wise item mappings
                        $this->data['categories'] = $this->site->getAllrecipeCategories_withdays($order);
                    }
                    $this->data['brands'] = $this->site->getAllBrands();
                    // sub category list from recipe table with active items in recipe table
                    if ($this->pos_settings->sales_item_in_pos == 1) {
                        $this->data['subcategories'] = $this->site->getrecipeSubCategories($this->pos_settings->default_category);
                    } else { // sub category list from mapping table with active items in recipe table
                        $this->data['subcategories'] = $this->site->getrecipeSubCategories_withdays($this->pos_settings->default_category, $order);
                    }

                    $this->data['printer'] = $this->pos_model->getPrinterByID($this->pos_settings->printer);
                    $order_printers = json_decode($this->pos_settings->order_printers);
                    $printers = array();
                    if (!empty($order_printers)) {
                        foreach ($order_printers as $printer_id) {
                            $printers[] = $this->pos_model->getPrinterByID($printer_id);
                        }
                    }
                    $this->data['order_printers'] = $printers;
                    $this->data['pos_settings'] = $this->pos_settings;

                    $this->data['areas'] = $this->pos_model->getTablelist($this->session->userdata('warehouse_id'));

                    $this->data['get_table'] = $table;
                    $this->data['get_order_type'] = $order;
                    $this->data['get_split'] = $split;
                    $this->data['same_customer'] = $same_customer;
					$this->data['avil_customers'] = $this->site->getAvilAbleCustomers();
					$this->data['avil_tables'] = $this->site->getAvilAbleTables($table);
                    if ($this->pos_settings->after_sale_page && $saleid = $this->input->get('print', true)) {
                        if ($inv = $this->pos_model->getInvoiceByID($saleid)) {
                            $this->load->helper('pos');
                            if (!$this->session->userdata('view_right')) {
                                $this->sma->view_rights($inv->created_by, true);
                            }
                            $this->data['rows'] = $this->pos_model->getAllInvoiceItems($inv->id);
                            $this->data['biller'] = $this->pos_model->getCompanyByID($inv->biller_id);
                            $this->data['customer'] = $this->pos_model->getCompanyByID($inv->customer_id);
                            $this->data['payments'] = $this->pos_model->getInvoicePayments($inv->id);
                            $this->data['return_sale'] = $inv->return_id ? $this->pos_model->getInvoiceByID($inv->return_id) : null;
                            $this->data['return_rows'] = $inv->return_id ? $this->pos_model->getAllInvoiceItems($inv->return_id) : null;
                            $this->data['return_payments'] = $this->data['return_sale'] ? $this->pos_model->getInvoicePayments($this->data['return_sale']->id) : null;
                            $this->data['inv'] = $inv;
                            $this->data['print'] = $inv->id;

                            $this->data['created_by'] = $this->site->getUser($inv->created_by);
                        }
                    }
					  $table_id = !empty($this->input->get('table')) ? $this->input->get('table') : '';
                    /*echo "<pre>";
                    print_r($this->data);
                    die;*/
                    /* if($this->pos_settings->variant_display_option ==0){
                    $this->load->view($this->theme . 'pos/add', $this->data);
                    }else{
                    $this->load->view($this->theme . 'pos/kimmo/add', $this->data);
                    } */
					$this->data['sales'] = $this->pos_model->getAllSalesWithbiller($order, $table_id);
				
                    $this->load->view($this->theme . 'pos/consolidate/tables', $this->data);
                }

            } else {
               
                $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
                $this->data['message'] = isset($this->data['message']) ? $this->data['message'] : $this->session->flashdata('message');
                $this->data['areas'] = $this->pos_model->getTablelist($this->session->userdata('warehouse_id'));
                // $this->data['biller'] = $this->site->getCompanyByID($this->pos_settings->default_biller);
				$this->data['customer'] = $this->pos_model->getCompanyByID($this->pos_settings->default_customer);
                $this->data['billers'] = $this->site->getAllCompanies('biller');
                $this->data['sales_types'] = $this->site->getAllSalestype();
                $this->data['tables'] = $this->site->getAllTables();
                $this->data['warehouses'] = $this->site->getAllWarehouses();
                $this->data['tax_rates'] = $this->site->getAllTaxRates();
                $this->data['user'] = $this->site->getUser();
                $this->data["tcp"] = $this->pos_model->recipe_count($this->pos_settings->default_category, $this->session->userdata('warehouse_id'));
                $this->data['recipe'] = $this->ajaxrecipe_consolidate($this->pos_settings->default_category, $this->session->userdata('warehouse_id'));
                if ($this->pos_settings->sales_item_in_pos == 1) {
                    $this->data['categories'] = $this->site->getAllrecipeCategories();
                } else { //by day wise item mappings
                    $this->data['categories'] = $this->site->getAllrecipeCategories_withdays($order);
                }

                //******************************   order  *****************////////

                $table_id = !empty($this->input->get('table')) ? $this->input->get('table') : '';
                $user = $this->site->getUser();
                $this->data['avil_customers'] = $this->site->getAvilAbleCustomers();
                $this->data['avil_tables'] = $this->site->getAvilAbleTables($table_id);
                $this->data['warehouse_id'] = $this->session->userdata('warehouse_id');
                $this->data['warehouse'] = $this->session->userdata('warehouse_id') ? $this->site->getWarehouseByID($this->session->userdata('warehouse_id')) : null;
                $this->data['tableid'] = !empty($this->input->get('table')) ? $this->input->get('table') : '';

                //**********************  order ***************************////
                
                $this->data['brands'] = $this->site->getAllBrands();
                $this->data['subcategories'] = $this->site->getrecipeSubCategories($this->pos_settings->default_category);
                $this->data['pos_settings'] = $this->pos_settings;
                $this->data['order_type'] = $order;
				//*************************  bill ******************************//
				 
                $this->load->view($this->theme . 'pos/consolidate/tables', $this->data);
				

            }

        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['message'] = isset($this->data['message']) ? $this->data['message'] : $this->session->flashdata('message');

            // $this->data['biller'] = $this->site->getCompanyByID($this->pos_settings->default_biller);
            $this->data['billers'] = $this->site->getAllCompanies('biller');
            $this->data['sales_types'] = $this->site->getAllSalestype();
            $this->data['tables'] = $this->site->getAllTables();
            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $this->data['tax_rates'] = $this->site->getAllTaxRates();
            $this->data['user'] = $this->site->getUser();
            $this->data["tcp"] = $this->pos_model->recipe_count($this->pos_settings->default_category, $this->session->userdata('warehouse_id'));
            $this->data['recipe'] = $this->ajaxrecipe_consolidate($this->pos_settings->default_category, $this->session->userdata('warehouse_id'));
            $this->data['categories'] = $this->site->getAllrecipeCategories_withdays($order);
            $this->data['brands'] = $this->site->getAllBrands();
            $this->data['subcategories'] = $this->site->getrecipeSubCategories($this->pos_settings->default_category);
            $this->data['bbq_category'] = $this->pos_model->getAllbbqCategories();
            $this->data['pos_settings'] = $this->pos_settings;
            $this->data['group'] = $this->session->userdata('group_id');
			
            if ($this->pos_settings->pos_types_display_option == 0) {
                $this->load->view($this->theme . 'pos/pos_type', $this->data);
            } else {
                $this->load->view($this->theme . 'pos/consolidate/tables', $this->data);
            }

        }

    }
	  public function sent_to_kitchen_all($sid = null){
       /*  echo "<pre>";
        print_r($_POST);exit;*/   
        $this->form_validation->set_rules('customer', $this->lang->line("customer"), 'trim|required');
        $this->form_validation->set_rules('warehouse', $this->lang->line("warehouse"), 'required');
        $this->form_validation->set_rules('biller', $this->lang->line("biller"), 'required');
         $table=$this->input->post('table_list_id');
        if ($this->form_validation->run() == true) {
            /*echo "<pre>";
            print_r($this->input->post());die; */
            
            $date = date('Y-m-d H:i:s');
            $warehouse_id = $this->input->post('warehouse');
            $customer_id = $this->input->post('customer');
            $biller_id = $this->input->post('biller');
            $total_items = $this->input->post('total_items');

            $payment_term = 0;
            $due_date = date('Y-m-d', strtotime('+' . $payment_term . ' days'));
            $shipping = $this->input->post('shipping') ? $this->input->post('shipping') : 0;
            $customer_details = $this->site->getCompanyByID($customer_id);
            $customer = $customer_details->name;
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
				
					if(!empty($get_item)){
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
			//krsort for buy x 
           // krsort($recipe);
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
                'seats_id' => !empty($this->input->post('table_list_id')) ? $this->input->post('seats_id') : 0,
                'split_id' => $split_id,
                'order_type' => $this->input->post('order_type_id'),
                'order_status' => 'Open',
                'customer_id' => $customer_id,
                'customer' => $customer,
                'biller_id' => $biller_id,
                'biller' => $biller,
                'warehouse_id' => $warehouse_id,
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

                admin_redirect("pos?msg=special_item");
            } else {
                admin_redirect("pos");
            }
        }
        /*echo "<pre>";
        print_r($recipe);
        die;*/
        if ($this->form_validation->run() == true && !empty($recipe) && !empty($data) && !empty($kitchen)) {

            if ($sale = $this->pos_model->addKitchen_all($data, $recipe, $kitchen, $notification_array, $this->session->userdata('warehouse_id'), $this->session->userdata('user_id'))) {

                $kot_print_data['kot_print_option'] = $this->pos_settings->kot_print_option;
                $kot_print_data['con_kot_print_option'] = $this->pos_settings->consolidated_kot_print;
                $kot_print_data['kot_area_print'] = $sale['kitchen_data'];

                if ($this->pos_settings->consolidated_kot_print != 0) {
                    $kot_print_data['kot_con_print'] = $sale['consolid_kitchen_data'];
                    $kot_print_data['consolidate_kitchens_kot'] = $sale['consolidate_kitchens_kot'];
                }
                /*echo "<pre>";
                print_r($kot_print_data);*/

                $kot_print_lang_option = $this->pos_settings->kot_print_lang_option;
                //print_r($kot_print_lang_option);
                // var_dump($this->pos_settings->kot_enable_disable);die;
                if ($this->pos_settings->kot_enable_disable == 1) {
                    $this->send_to_kot_print($kot_print_data);
                }
                // die;
                //if($this->pos_settings->kot_print_option == 1){
                //    $this->remotePrintingKOT_single($sale['kitchen_data']);
                //}else{
                //    $this->remotePrintingKOT($sale['kitchen_data']);
                //}
                //if($this->pos_settings->consolidated_kot_print != 0){
                //
                //
                //    $kotconsoildprint = $sale['consolid_kitchen_data'];
                //    $this->kot_consolidated_curl($kotconsoildprint);

                //if(!empty($kotconsoildprint['consolid_kot_print_details'])){
                //
                //
                //    foreach($kotconsoildprint['consolid_kot_print_details'] as $order_data){
                //
                //
                //        if(!empty($kotconsoildprint['consolid_kot_print_details']) && !empty($kotconsoildprint['consolid_kitchens'])){
                //
                //            $this->remotePrintingCONSOLIDKOT($sale['consolid_kitchen_data']);
                //        }
                //    }
                //}

                //}

                $this->session->set_userdata('remove_posls', 1);
                /* $msg = $this->lang->line("sale_added");
                if (!empty($sale['message'])) {
                    foreach ($sale['message'] as $m) {
                        $msg .= '<br>' . $m;
                    }
                } */
				$msg='';
                $this->session->set_flashdata('message', $msg);
                $tableid = $this->input->post('table_list_id');
                if ($_POST['order_type_id'] == 1 && substr($_POST['split_id'], 0, 3) !== "BBQ") {
                    admin_redirect("pos/consolidate/?order=1&table=". $tableid);
                } else if ($_POST['order_type_id'] == 4) {
                    admin_redirect("pos?bbqtid=" . $tableid);
                } else {
                    admin_redirect("pos");
                }

            }
        } else {
            admin_redirect("pos/consolidate/?order=1&table=".$table);
        }

    }
public function billing_all(){
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
        /*echo "<pre>";
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
                'warehouse_id' => $order->warehouse_id,
                'note' => $order->note,
                'staff_note' => $order->staff_note,
                'sale_status' => 'Process',
                'hash' => hash('sha256', microtime() . mt_rand()),
            );

            $customer_id = $order->customer_id;
            $notification_array['customer_id'] = $order->customer_id;
        }

        $this->data['order_data'] = $order_data;
        $postData = $this->input->post();
        $delivery_person = $this->input->post('delivery_person_id') ? $this->input->post('delivery_person_id') : 0;

        $split_status = $this->site->check_splitid_is_bill_generated($split_id);
        if ($split_status) {
            admin_redirect("pos/order_table");
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
                            /*$tot_item =    $this->input->post('[split]['.$i.'][total_item]');
                            $itemdis = $this->input->post('[split]['.$i.'][discount_amount]')/$tot_item;*/

                            $billitem['bills_items'] = array();
                            $bills_item['recipe_name'] = $this->input->post('split[][$i][recipe_name][$i]');
                            $splitData = array();
                            foreach ($postData['split'][$i]['recipe_name'] as $key => $split) {

                                // $comment_price = $postData['split'][$i]['comment_price'][$key];

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

                                    // $input_dis = $this->input->post('[split]['.$i.'][item_input_dis]['.$key.']');
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
                                'warehouse_id' => $this->session->userdata('warehouse_id'),
                                'created_by' => $this->session->userdata('user_id'),
                                'customer_discount_id' => $customer_discount_id ? $customer_discount_id : 0,
                                'discount_type' => $cus_discount_type,
                                'discount_val' => $cus_discount_val,
                                'order_discount' => $this->input->post('[split][' . $i . '][discount_amount]') ? $this->input->post('[split][' . $i . '][discount_amount]') : null,
                                'service_charge_id' => $this->input->post('[split][' . $i . '][service_charge]') ? $this->input->post('[split][' . $i . '][service_charge]') : 0,
                                'service_charge_amount' => $this->input->post('[split][' . $i . '][service_amount]') ? $this->input->post('[split][' . $i . '][service_amount]') : 0,

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
                                admin_redirect("pos/consolidate/?order=1&table=". $tableid);
                            } elseif ($order_type == 2) {
                                admin_redirect("pos/order_takeaway");
                            } elseif ($order_type == 3) {
                                admin_redirect("pos/order_doordelivery");
                            }
                        }
                    } else {
                        $customer_id = $this->site->getCustomerDetails($waiter_id, $table_id, $split_id);
                        $this->data['discount_select'] = $this->pos_model->getDiscountdata($customer_id, $waiter_id, $table_id, $split_id);

                        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
                        if ($this->pos_settings->billgeneration_screen == 1) {
                            $this->load->view($this->theme . 'pos/consolidate/singlebil', $this->data);
                        } else {
                            $this->load->view($this->theme . 'pos/consolidate/template2/singlebil', $this->data);
                        }
                    }
                }
            } else {
				
                $customer_id = $this->site->getCustomerDetails($waiter_id, $table_id, $split_id);
                $this->data['discount_select'] = $this->pos_model->getDiscountdata($customer_id, $waiter_id, $table_id, $split_id);

                $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
                // echo '<pre>';print_R($this->data);exit;

                /* if ($this->pos_settings->billgeneration_screen == 1) {
                    $this->load->view($this->theme . 'pos/consolidate/singlebil', $this->data);
                } else {
                    $this->load->view($this->theme . 'pos/consolidate/template2/singlebil', $this->data);
                } */
				$this->load->view($this->theme . 'pos/consolidate/singlebil', $this->data);
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
                                'warehouse_id' => $this->session->userdata('warehouse_id'),
                                'created_by' => $this->session->userdata('user_id'),
                                'customer_discount_id' => $customer_discount_id,
                                'customer_discount_status' => $customer_discount_status,
                                'discount_type' => $cus_discount_type,
                                'discount_val' => $cus_discount_val,
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
                        $response = $this->pos_model->InsertBill_all($order_data, $order_item, $billData, $splitData, $sales_total, $delivery_person, $timelog_array, $notification_array, $order_item_id, $split_id, $request_discount, $dine_in_discount, $birthday);

                        if ($response == 1) {
                            $update_notifi['split_id'] = $split_id;
                            $update_notifi['tag'] = 'bill-request';
                            $this->site->update_notification_status($update_notifi);
                            if ($order_type == 1) {
                                $tableid = $order_data['sales_table_id'];
								$this->session->set_flashdata('message','Bill Generation Completed.');
                                redirect("pos/pos/");
                            } elseif ($order_type == 2) {
                                admin_redirect("pos/order_takeaway");
                            } elseif ($order_type == 3) {
                                admin_redirect("pos/order_doordelivery");
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
                                'warehouse_id' => $this->session->userdata('warehouse_id'),
                                'created_by' => $this->session->userdata('user_id'),
                                'customer_discount_id' => $customer_discount_id,
                                'customer_discount_status' => $customer_discount_status,
                                'discount_type' => $cus_discount_type,
                                'discount_val' => $cus_discount_val,
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
                                admin_redirect("pos/consolidate/?order=1&table=" . $tableid);
                            } elseif ($order_type == 2) {
                                admin_redirect("pos/order_takeaway");
                            } elseif ($order_type == 3) {
                                admin_redirect("pos/order_doordelivery");
                            }
                        }

                    } else {
                        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
                        $this->load->view($this->theme . 'pos/consolidate/manualsplitbil', $this->data);
                    }

                }

            } else {
                $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
                $this->load->view($this->theme . 'pos/consolidate/manualsplitbil', $this->data);
            }
        }

    }
 public function paymant_all()
    {
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
                        // 'cheque_no'   => $_POST['cheque_no'][$r],
                        'cc_no' => $_POST['cc_no'][$r],
                        'cc_month' => $crd_exp_date[0],
                        'cc_year' => $crd_exp_date[1],
                        /*'cc_holder'   => $_POST['cc_holer'][$r],
                        'cc_month'    => $_POST['cc_month'][$r],
                        'cc_year'     => $_POST['cc_year'][$r],
                        'cc_type'     => $_POST['cc_type'][$r],*/
                        // 'cc_cvv2'   => $this->input->post('cc_cvv2'),
                        /*'sale_note'   => $_POST['sale_note'],
                        'staff_note'   => $_POST['staffnote'],
                        'payment_note' => $_POST['payment_note'][$r],*/
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
                    // print_r($payment);
                    // print_r($multi_currency);die;
                    $response = $this->pos_model->Payment_for_consolidate($update_bill, $billid, $payment, $multi_currency, $salesid, $sales_bill, $order_split_id, $notification_array, $updateCreditLimit, $total, $customer_id, $loyalty_used_points, $taxation, $customer_changed);
                }
            }

            if ($response == 1) {

                //$this->send_to_bill_print($billid);
                $update_notifi['split_id'] = $order_split_id;
                $update_notifi['tag'] = 'bill-request';
                $this->site->update_notification_status($update_notifi);
                if ($taxation == 1) {
                     admin_redirect("pos/consolidate");
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
                    if ($this->pos_settings->bill_print_format == 1) {
                        $this->load->view($this->theme . 'pos/consolidate/view_bill', $this->data);
                    } elseif ($this->pos_settings->bill_print_format == 3) {
                        $this->load->view($this->theme . 'pos/indai_bill/view_bill', $this->data, false);
                    } elseif ($this->pos_settings->bill_print_format == 4) {
                        $this->load->view($this->theme . 'pos/local_bill/view_bill', $this->data, false);
                    } else {
                        $this->load->view($this->theme . 'pos/row_discount/view_bill', $this->data);
                    }
                } else {

                    admin_redirect("pos/ consolidate/?order=1&table=" . $tableid);
                }
            }
        } else {
            admin_redirect("pos/consolidate");
        }

    }
	public function change_table_number_all($cancel_remarks = null, $sale_id = null)
    {
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
	 public function ajaxcategorydata_consolidate($category_id = null)
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
                    $scats .= "<button id=\"subcategory-" . $category->id . "\" type=\"button\" value='" . $category->id . "' class=\"btn-prni subcategory slide\" ><img src=\"assets/uploads/thumbs/" . ($category->image ? $category->image : 'no_image.png') . "\" class='img-rounded' />";
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
	 public function multiple_splits_mergeto_singlesplit_for_consolidate(){
        $merge_splits = $this->input->post('merge_splits');
        $current_split = $this->input->post('current_split');
        $merge_table_id = $this->input->post('merge_table_id');
        $result = $this->pos_model->merger_multiple_to_single_split_consolidate($merge_splits, $current_split, $merge_table_id);
        if ($result == true) {
            $msg = 'success';
        } else {
            $msg = 'error';
            $status = 'error';
        }
        $this->sma->send_json(array('msg' => $msg, 'status' => $msg));
    }
	 public function cancel_all_order_items_consolidate($cancel_remarks = null, $split_table_id = null){
        $cancel_remarks = $this->input->get('cancel_remarks');
        $split_table_id = $this->input->get('split_table_id');
        $notification_array['from_role'] = $this->session->userdata('group_id');
        $notification_array['insert_array'] = array(
            'user_id' => $this->session->userdata('user_id'),
            'warehouse_id' => $this->session->userdata('warehouse_id'),
            'created_on' => date('Y-m-d H:m:s'),
            'is_read' => 0,
        );
        $result = $this->pos_model->ALLCancelOrdersItem_consolidate($cancel_remarks, $split_table_id, $this->session->userdata('user_id'), $notification_array);
        if ($result == true) {
            $msg = 'success';
        } else {
            $msg = 'error';
            $status = 'error';
        }
        $this->sma->send_json(array('msg' => $msg, 'status' => $msg));
    }
	public function ajaxrecipe_consolidate($category_id = null, $warehouse_id = null, $subcategory_id = null, $brand_id = null, $order_type = null)
    {
        // var_dump($order_type);die;

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

        // if ($this->input->get('per_page') == 'n') {
        if ($this->input->get('per_page') == 'n' || $this->input->get('per_page') == '0') {
            $page = 0;
        } else {
            $page = $this->input->get('per_page');
        }
        if ($this->input->get('order_type')) {
            $order_type = $this->input->get('order_type');
        }

        // $order_type = $this->input->get('order_type') ? $this->input->get('order_type') : 0;
        $this->load->library("pagination");
// var_dump($order_type);die;
        $config = array();
        $config["base_url"] = base_url() . "pos/ajaxrecipe";
        if ($this->pos_settings->sales_item_in_pos == 1) {
            $config["total_rows"] = $this->pos_model->recipe_count($category_id, $warehouse_id, $subcategory_id, $brand_id);
        } else {
            $config["total_rows"] = $this->pos_model->recipe_count_withdays($category_id, $warehouse_id, $subcategory_id, $brand_id, $order_type);
        }

        $config["per_page"] = $this->pos_settings->pro_limit;

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
        $prods = '<div>';
        if (!empty($recipe)) {
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
							 
                            $vari .= '<button data-id="' . $varient->variant_id . '" id="recipe-' . $category_id . $count . '" type="button" value="' . $recipe->code . '" title="" class="btn-default  recipe-varient pos-tip" data-container="body" data-original-title="' . $varient_name . '" tabindex="-1">';
                            if (strlen($varient_name) < 15) {
                                $vari .= "<span class='name_strong'>" . $varient_name . "</span>";
                            } else {
                               // $vari .= '<marquee class="name_strong" behavior="alternate" direction="left" scrollamount="1">
									//&nbsp;&nbsp;' . $varient_name . '&nbsp;&nbsp;</marquee>';
									$vari .= "<span class='name_strong'>" .wordwrap($varient_name,15,"\n") . "</span>";
									
                            }
                            $vari .= '<br>
								<span class="price_strong"> ' . $default_currency_symbol . $this->sma->formatDecimal($varient->price) . '</span> </button>';
                        }
                        $vari .= '</div>';
                    } else { //varaint list by Table for Kimmo client requriment

                        $vari = '<div class="variant-popup" style="display: none;">';
						$vari .= '<h2 style="margin-top:10px;"><tr><td>' . lang('VARIANTS') . '</td><td> (' .$recipe_name . ')</td></tr></h2>';

 
$vari .= '<input class="kb-text1" id="myInput"style="width: 50%;height:36px;line-height:36px;padding:8px; border: 1px solid #ccc;"placeholder="Variants Search.." autocomplete ="off" >';
		/* $vari .= "<script> $('.kb-text1').keyboard({
        autoAccept: true,
        alwaysOpen: false,
        openOn: 'focus',
        usePreview: false,
        layout: 'custom',
        //layout: 'qwerty',
        display: {
            'bksp': '\u2190',
            'accept': 'return',
            'default': 'ABC',
            'meta1': '123',
        },
        customLayout: {
            'default': [
            'q w e r t y u i o p {bksp}',
            'a s d f g h j k l {enter}',
            '{s} z x c v b n m , . {s}',
            '{meta1} {space} {cancel} {accept}'
            ],
            'shift': [
            'Q W E R T Y U I O P {bksp}',
            'A S D F G H J K L {enter}',
            '{s} Z X C V B N M / ? {s}',
            '{space} {space} {default} {accept}'
            ],
             'meta1': [
            '1 2 3 4 5 6 7 8 9 0 {bksp}',
            '- / : ; ( ) \u20ac & @ {enter}',
			'{default} {space} {cancel} {accept}'
            ]
            }
        });</script>"; */

	

						
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
                            $vari .= '<tr data-id="' . $varient->variant_id . '" id="recipe-' . $category_id . $count . '" code="' . $recipe->code . '" title="" class="btn-default  recipe-varient pos-tip recipe-11155" data-container="body" data-original-title="' . $varient_name . '" tabindex="-1">';
							
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
						$vari .= '<td colspan="2"><button type="button" class="btn btn-primary pull-right vritem" id="vritem">Submit</button><span class="payment_status pull-left label label-danger iclose" style="padding:10px 12px;font-size:14px;margin-right:5px;border-radius:0px;font-weight:normal;cursor:pointer;">void</span> </td>';
						/* <span class="payment_status pull-left label label-danger iclose" style="padding:10px 12px;font-size:14px;margin-right:5px;border-radius:0px;font-weight:normal;cursor:pointer;" id="" + row_no + "" title="Remove" style="cursor:pointer;">void</span> */
                        $vari .= '</table>';
                        $vari .= '</div>';
                    }
                }
				$activemode=($recipe->active ==2)?'style="background-color:#E1A87C !important"':'';
				$activemode_class=($recipe->active ==2)?'non_transaction':'';
                if ($this->pos_settings->sale_item_display == 0) {

                    $prods .= "<span><button ".$activemode." id=\"recipe-" . $category_id . $count . "\" type=\"button\" value='" . $recipe->id . "' title=\"" . $recipe->name . "\" class=\"btn-prni btn-" . $this->pos_settings->recipe_button_color . " " . $class . " recipe pos-tip\" data-container=\"body\"><img src=\"" . base_url() . "assets/uploads/thumbs/" . $recipe->image . "\" alt=\"" . $recipe_name . "\" class='img-rounded  ".$activemode_class."' />";
                } else {
                    $prods .= "<span><button  ".$activemode." id=\"recipe-" . $category_id . $count . "\" type=\"button\" value='" . $recipe->id . "' title=\"" . $recipe->name . "\" class=\"btn-img btn-" . $this->pos_settings->recipe_button_color . " " . $class . " recipe pos-tip ".$activemode_class."\" data-container=\"body\">";
                }

                if (strlen($recipe->name) < 15) {

                    $prods .= "<span class='name_strong'>" . $recipe_name . "</span>";
                } else {
                   
					$prods .= "<span class='name_strong'>" . wordwrap($recipe_name,15,"\n"). "</span>";
                }
    
                $prods .= "<br><span class='price_strong'> ";
                if ($recipe->price != 0) {
                    $prods .= $default_currency_symbol . "" . $this->sma->formatDecimal($recipe->price);
                }

                $prods .= "</span>" . $buyvalue . "";

                $prods .= "</button>";
                $prods .= $vari . '</span>';

                $pro++;
            }
        }
        $prods .= "</div>";

        // if ($this->input->get('per_page')) {
        if ($this->input->get('per_page') != null) {
            echo $prods;
        } else {
            return $prods;
        }
    }
	  public function ajaxrecipebbq_consolidate($category_id = null, $warehouse_id = null, $subcategory_id = null, $brand_id = null, $sales_type = null)
    {

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

        // if ($this->input->get('per_page') == 'n') {
        if ($this->input->get('per_page') == 'n' || $this->input->get('per_page') == '0') {

            $page = 0;
        } else {
            $page = $this->input->get('per_page');
        }

        $this->load->library("pagination");

        $config = array();
        $config["base_url"] = base_url() . "pos/ajaxrecipebbq";
        $config["total_rows"] = $this->pos_model->bbqrecipe_count($category_id, $warehouse_id, $subcategory_id, $brand_id);

        $config["per_page"] = $this->pos_settings->pro_limit;

        $config['prev_link'] = false;
        $config['next_link'] = false;
        $config['display_pages'] = false;
        $config['first_link'] = false;
        $config['last_link'] = false;

        $this->pagination->initialize($config);

        if ($this->pos_settings->sales_item_in_pos == 1) {
            $recipe = $this->pos_model->bbqfetch_recipe($category_id, $warehouse_id, $config["per_page"], $page, $subcategory_id, $brand_id);
        } else {
            $recipe = $this->pos_model->bbqfetch_recipe_withdays($category_id, $warehouse_id, $config["per_page"], $page, $subcategory_id, $brand_id, $sales_type);
        }

        $pro = 1;
        $prods = '<div>';
        if (!empty($recipe)) {
            foreach ($recipe as $recipe) {
                $count = $recipe->id;
                $buy = $this->site->checkBuyget($recipe->id);
                $default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
                $default_currency_rate = $default_currency_data->rate;
                $default_currency_symbol = $default_currency_data->symbol;
                if (!empty($buy)) {
                    if ($buy->buy_method == 'buy_x_get_x') {
                        $buyvalue = "<div class='rippon'><div class='offer_list'><span>Buy " . $buy->buy_quantity . "  Get " . $buy->get_quantity . " " . $buy->free_recipe . " </span></div></div>";
                    } elseif ($buy->buy_method == 'buy_x_get_y') {
                        $buyvalue = "<div class='rippon'><div class='offer_list'><span>Buy " . $buy->buy_quantity . "  Get " . $buy->free_recipe . " ( " . $buy->get_quantity . ")</span></div></div>";
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

                    $vari = '<div class="variant-popup" style="display: none;">';
                    foreach ($varients as $k => $varient) {
                        $vari .= '<button data-id="' . $varient->variant_id . '" id="recipe-' . $category_id . $count . '" type="button" value="' . $recipe->id . '" title="" class="btn-default  recipe-varient pos-tip" data-container="body" data-original-title="' . $varient->name . '" tabindex="-1">';
                        if (strlen($varient->name) < 15) {
                            $vari .= "<span class='name_strong'>" . $varient->name . "</span>";
                        } else {
                          //  $vari .= '<marquee class="name_strong" behavior="alternate" direction="left" scrollamount="1">
							//	&nbsp;&nbsp;' . $varient->name . '&nbsp;&nbsp;</marquee>';
								$vari .= "<span class='name_strong'>" .wordwrap($varient_name,15,"\n") . "</span>";
								
                        }
                        $vari .= '<br>
							<span class="price_strong"> ' . $default_currency_symbol . $this->sma->formatDecimal($varient->price) . '</span> </button>';
                    }
                    $vari .= '</div>';

                }
                 $activemode=($recipe->active ==2)?'style="background-color:#E1A87C !important"':'';
				$activemode_class=($recipe->active ==2)?'non_transaction':'';
                $prods .= "<span><button id=\"recipe-" . $category_id . $count . "\" type=\"button\" value='" . $recipe->id . "' title=\"" . $recipe->name . "\" class=\"btn-prni btn-" . $this->pos_settings->recipe_button_color . "" . $class . "".$activemode_class." recipe pos-tip\" data-container=\"body\"><img src=\"" . base_url() . "assets/uploads/thumbs/" . $recipe->image . "\" alt=\"" . $recipe_name . "\" class='img-rounded' />";

                if (strlen($recipe->name) < 15) {

                    $prods .= "<span class='name_strong'>" . $recipe_name . "</span>";
                } else {
                  //  $prods .= "<marquee class='name_strong' behavior='alternate' direction='left' scrollamount='1'>&nbsp;&nbsp;" . $recipe_name . "&nbsp;&nbsp;</marquee>";
				  $prods .= "<span class='name_strong'>" . wordwrap($recipe_name,15,"\n"). "</span>";
                }

                // $prods .=  "<br><span class='price_strong'> ".$default_currency_symbol ."" . $this->sma->formatDecimal($recipe->price). "</span>".$buyvalue." </button>";
                $prods .= $vari . '</span>';
                $pro++;
            }
        }
        $prods .= "</div>";

        if ($this->input->get('per_page')) {
            echo $prods;
        } else {
            return $prods;
        }
    }
	public function cancel_sale_consolidate($cancel_remarks = null, $sale_id = null)
    {
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
	   public function getrecipeDataByCode_all($code = null, $warehouse_id = null)
    {
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

}