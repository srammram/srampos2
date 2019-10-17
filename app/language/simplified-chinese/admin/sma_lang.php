<?php defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * Module: General Language File for common lang keys
 * Language: Simplified Chinese
 * Translator: Wei Long Ueng (TAIWAN)
 *
 * Last edited:
 * 1st September 2016
 *
 * Package:
 * SRAM POS v1.0
 *
 * You can translate this file to your language.
 * For instruction on new language setup, please visit the documentations.
 * You also can share your language files by emailing to info@srampos.com
 * Thank you
 */

/* --------------------- CUSTOM FIELDS ------------------------ */
/*
* Below are custome field labels
* Please only change the part after = and make sure you change the the words in between "";
* $lang['bcf1']                         = "Biller Custom Field 1";
* Don't change this                     = "You can change this part";
* For support email info@srampos.com Thank you!
*/

$lang['bcf1']                           = "公司自订栏位 1";
$lang['bcf2']                           = "公司自订栏位 2";
$lang['bcf3']                           = "公司自订栏位 3";
$lang['bcf4']                           = "公司自订栏位 4";
$lang['bcf5']                           = "公司自订栏位 5";
$lang['bcf6']                           = "公司自订栏位 6";
$lang['pcf1']                           = "产品自订栏位 1";
$lang['pcf2']                           = "产品自订栏位 2";
$lang['pcf3']                           = "产品自订栏位 3";
$lang['pcf4']                           = "产品自订栏位 4";
$lang['pcf5']                           = "产品自订栏位 5";
$lang['pcf6']                           = "产品自订栏位 6";
$lang['ccf1']                           = "顾客自订栏位 1";
$lang['ccf2']                           = "顾客自订栏位 2";
$lang['ccf3']                           = "顾客自订栏位 3";
$lang['ccf4']                           = "顾客自订栏位 4";
$lang['ccf5']                           = "顾客自订栏位 5";
$lang['ccf6']                           = "顾客自订栏位 6";
$lang['scf1']                           = "供应商自订栏位 1";
$lang['scf2']                           = "供应商自订栏位 2";
$lang['scf3']                           = "供应商自订栏位 3";
$lang['scf4']                           = "供应商自订栏位 4";
$lang['scf5']                           = "供应商自订栏位 5";
$lang['scf6']                           = "供应商自订栏位 6";

/* ----------------- DATATABLES LANGUAGE ---------------------- */
/*
* Below are datatables language entries
* Please only change the part after = and make sure you change the the words in between "";
* 'sEmptyTable'                     => "No data available in table",
* Don't change this                 => "You can change this part but not the word between and ending with _ like _START_;
* For support email info@srampos.com Thank you!
*/

$lang['datatables_lang']        = array(
    'sEmptyTable'                   => "无资料",
    'sInfo'                         => "显示第 _START_ 笔到第 _END_ 笔，共 _TOTAL_ 笔资料",
    'sInfoEmpty'                    => "显示第 0 笔到第 0 笔，共 0 笔资料",
    'sInfoFiltered'                 => "(筛选自 _MAX_ 笔资料)",
    'sInfoPostFix'                  => "",
    'sInfoThousands'                => ",",
    'sLengthMenu'                   => "显示 _MENU_ 笔",
    'sLoadingRecords'               => "资料载入中...",
    'sProcessing'                   => "处理中...",
    'sSearch'                       => "搜寻",
    'sZeroRecords'                  => "没有找到相符的资料",
    'oAria'                                     => array(
      'sSortAscending'                => ": 启动升幂排序",
      'sSortDescending'               => ": 启动降幂排序"
      ),
    'oPaginate'                                 => array(
      'sFirst'                        => "<< 第一页",
      'sLast'                         => "最後页 >>",
      'sNext'                         => "下一页 >",
      'sPrevious'                     => "< 上一页",
      )
    );

/* ----------------- Select2 LANGUAGE ---------------------- */
/*
* Below are select2 lib language entries
* Please only change the part after = and make sure you change the the words in between "";
* 's2_errorLoading'                 => "The results could not be loaded",
* Don't change this                 => "You can change this part but not the word between {} like {t};
* For support email info@srampos.com Thank you!
*/

$lang['select2_lang']               = array(
    'formatMatches_s'               => "一个结果可用, 按enter键选择.",
    'formatMatches_p'               => "多笔结果可用, 请使用上下键选择.",
    'formatNoMatches'               => "没有相符资料",
    'formatInputTooShort'           => "请输入 {n} 个或更多字元",
    'formatInputTooLong_s'          => "请删除 {n} 个字元",
    'formatInputTooLong_p'          => "请删除 {n} 个字元",
    'formatSelectionTooBig_s'       => "你只能选择 {n} 个品项",
    'formatSelectionTooBig_p'       => "你只能选择 {n} 个品项",
    'formatLoadMore'                => "载入更多资料...",
    'formatAjaxError'               => "Ajax请求失败",
    'formatSearching'               => "搜寻中..."
    );


/* ----------------- SMA GENERAL LANGUAGE KEYS -------------------- */

$lang['home']                               = "首页";
$lang['dashboard']                          = "仪表板";
$lang['username']                           = "使用者名称";
$lang['password']                           = "密码";
$lang['first_name']                         = "名";
$lang['last_name']                          = "姓";
$lang['confirm_password']                   = "确认密码";
$lang['email']                              = "电子邮件";
$lang['phone']                              = "电话";
$lang['company']                            = "公司";
$lang['product_code']                       = "产品码";
$lang['product_name']                       = "产品名称";
$lang['cname']                              = "顾客名称";
$lang['barcode_symbology']                  = "条码编码";
$lang['product_unit']                       = "产品单位";
$lang['product_price']                      = "产品售价";
$lang['contact_person']                     = "联络人";
$lang['email_address']                      = "电子邮件地址";
$lang['address']                            = "地址";
$lang['city']                               = "城市";
$lang['today']                              = "今日";
$lang['welcome']                            = "欢迎";
$lang['profile']                            = "基本资料";
$lang['change_password']                    = "变更密码";
$lang['logout']                             = "登出";
$lang['notifications']                      = "通知";
$lang['calendar']                           = "月历";
$lang['messages']                           = "讯息";
$lang['styles']                             = "风格";
$lang['language']                           = "语言";
$lang['alerts']                             = "警告";
$lang['list_products']                      = "产品列表";
$lang['add_product']                        = "新增产品";
$lang['print_barcodes']                     = "列印条码";
$lang['print_labels']                       = "列印标签";
$lang['import_products']                    = "汇入产品";
$lang['update_price']                       = "更新价钱";
$lang['damage_products']                    = "坏品";
$lang['sales']                              = "销售";
$lang['list_sales']                         = "销售列表";
$lang['add_sale']                           = "新增销售";
$lang['deliveries']                         = "交货";
$lang['gift_cards']                         = "礼品卡";
$lang['quotes']                             = "报价";
$lang['list_quotes']                        = "报价列表";
$lang['add_quote']                          = "新增报价";
$lang['purchases']                          = "采购进货";
$lang['list_purchases']                     = "采购进货列表";
$lang['add_purchase']                       = "新增采购进货";
$lang['add_purchase_by_csv']                = "汇入采购进货";
$lang['transfers']                          = "调拨";
$lang['list_transfers']                     = "调拨列表";
$lang['add_transfer']                       = "新增调拨";
$lang['add_transfer_by_csv']                = "汇入调拨";
$lang['people']                             = "人员";
$lang['list_users']                         = "使用者列表";
$lang['new_user']                           = "新增使用者";
$lang['list_billers']                       = "公司列表";
$lang['add_biller']                         = "新增公司";
$lang['list_customers']                     = "客户列表";
$lang['add_customer']                       = "新增客户";
$lang['list_suppliers']                     = "供应商列表";
$lang['add_supplier']                       = "新增供应商";
$lang['settings']                           = "设定";
$lang['system_settings']                    = "系统设定";
$lang['change_logo']                        = "变更Logo";
$lang['currencies']                         = "货币";
$lang['attributes']                         = "产品选项";
$lang['customer_groups']                    = "客户群组";
$lang['categories']                         = "产品类别";
$lang['subcategories']                      = "子类别";
$lang['tax_rates']                          = "税率";
$lang['warehouses']                         = "仓库";
$lang['email_templates']                    = "电子邮件内容模板";
$lang['group_permissions']                  = "使用者群组权限";
$lang['backup_database']                    = "资料库备份";
$lang['reports']                            = "报告（表）";
$lang['overview_chart']                     = "综合图表";
$lang['warehouse_stock']                    = "仓库库存图表";
$lang['product_quantity_alerts']            = "产品库存警告";
$lang['product_expiry_alerts']              = "产品到期警告";
$lang['products_report']                    = "产品报表";
$lang['daily_sales']                        = "日销售";
$lang['monthly_sales']                      = "月销售";
$lang['sales_report']                       = "销售报表";
$lang['payments_report']                    = "付款报表";
$lang['profit_and_loss']                    = "损益";
$lang['purchases_report']                   = "采购进货报表";
$lang['customers_report']                   = "客户报表";
$lang['suppliers_report']                   = "供应商报表";
$lang['staff_report']                       = "员工报表";
$lang['your_ip']                            = "你的IP地址";
$lang['last_login_at']                      = "上次登入时间";
$lang['notification_post_at']               = "通知发布时间";
$lang['quick_links']                        = "快速连结";
$lang['date']                               = "日期";
$lang['reference_no']                       = "参考号";
$lang['products']                           = "产品";
$lang['customers']                          = "客户";
$lang['suppliers']                          = "供应商";
$lang['users']                              = "使用者";
$lang['latest_five']                        = "最新5笔";
$lang['total']                              = "总计";
$lang['payment_status']                     = "付款状态";
$lang['paid']                               = "已付款";
$lang['customer']                           = "客户";
$lang['status']                             = "状态";
$lang['amount']                             = "金额";
$lang['supplier']                           = "供应商";
$lang['from']                               = "从";
$lang['to']                                 = "到";
$lang['name']                               = "名称";
$lang['create_user']                        = "新增使用者";
$lang['gender']                             = "性别";
$lang['biller']                             = "公司";
$lang['select']                             = "选择";
$lang['warehouse']                          = "仓库";
$lang['active']                             = "启动";
$lang['inactive']                           = "不启动";
$lang['all']                                = "全部";
$lang['list_results']                       = "请使用下表来浏览或筛选结果。您也可以下载Excel或PDF文件。";
$lang['actions']                            = "动作";
$lang['pos']                                = "POS";
$lang['access_denied']                      = "无法进入！你没有权利进入所请求的网页。如果你认为这是错误，请联系管理员。";
$lang['add']                                = "新增";
$lang['edit']                               = "编辑";
$lang['delete']                             = "删除";
$lang['view']                               = "检视";
$lang['update']                             = "更新";
$lang['save']                               = "储存";
$lang['login']                              = "登入";
$lang['submit']                             = "送出";
$lang['no']                                 = "No";
$lang['yes']                                = "Yes";
$lang['disable']                            = "关闭";
$lang['enable']                             = "启用";
$lang['enter_info']                         = "请填写以下资料。标有*的是必需输入的资料。";
$lang['update_info']                        = "请更新以下资料。标有*的是必需输入的资料。";
$lang['no_suggestions']                     = "无法获取建议的资料，请检查您的输入";
$lang['i_m_sure']                           = '是的，我确定。';
$lang['r_u_sure']                           = '您确定?';
$lang['export_to_excel']                    = "汇出至Excel档";
$lang['export_to_pdf']                      = "汇出至PDF档";
$lang['image']                              = "图片";
$lang['sale']                               = "销售";
$lang['quote']                              = "估价";
$lang['purchase']                           = "采购";
$lang['transfer']                           = "调控";
$lang['payment']                            = "付款";
$lang['payments']                           = "付款";
$lang['orders']                             = "订单";
$lang['pdf']                                = "PDF";
$lang['vat_no']                             = "税号";
$lang['country']                            = "国家";
$lang['add_user']                           = "新增使用者";
$lang['type']                               = "类型";
$lang['person']                             = "人";
$lang['state']                              = "州";
$lang['postal_code']                        = "邮递区号";
$lang['id']                                 = "ID";
$lang['close']                              = "关闭";
$lang['male']                               = "男";
$lang['female']                             = "女";
$lang['notify_user']                        = "通知使用者";
$lang['notify_user_by_email']               = "以电子邮件通知使用者";
$lang['billers']                            = "公司";
$lang['all_warehouses']                     = "全部仓库";
$lang['category']                           = "类别";
$lang['product_cost']                       = "产品成本";
$lang['quantity']                           = "数量";
$lang['loading_data_from_server']           = "从伺服器载入资料";
$lang['excel']                              = "Excel";
$lang['print']                              = "列印";
$lang['ajax_error']                         = "Ajax载入错误，请重新操作。";
$lang['product_tax']                        = "产品税";
$lang['order_tax']                          = "订单税";
$lang['upload_file']                        = "上传文件";
$lang['download_sample_file']               = "下载范例文件";
$lang['csv1']                               = "请勿变更第一列的栏位名称及顺序.";
$lang['csv2']                               = "正确的栏位顺序是";
$lang['csv3']                               = "&amp; 你必须遵照这个规则。<br>请确认CSV文件是UTF-8编码，并且不是以位元组顺序记号(BOM)作为储存。";
$lang['import']                             = "汇入";
$lang['note']                               = "注记";
$lang['grand_total']                        = "总计";
$lang['download_pdf']                       = "下载为PDF";
$lang['no_zero_required']                   = "栏位『%s』是必须的";
$lang['no_product_found']                   = "没有找到产品";
$lang['pending']                            = "待处理";
$lang['sent']                               = "已送";
$lang['completed']                          = "已完成";
$lang['shipping']                           = "运费";
$lang['add_product_to_order']               = "请新增产品到订单列表";
$lang['order_items']                        = "订单项目";
$lang['net_unit_cost']                      = "净单位成本";
$lang['net_unit_price']                     = "净单价";
$lang['expiry_date']                        = "到期日";
$lang['subtotal']                           = "小计";
$lang['reset']                              = "重置";
$lang['items']                              = "品项";
$lang['au_pr_name_tip']                     = "请开始输入建议的产品码或名称，或者直接扫描条码。";
$lang['no_match_found']                     = "没找到! 产品可能缺货。";
$lang['csv_file']                           = "CSV文件";
$lang['document']                           = "附件文件";
$lang['product']                            = "产品";
$lang['user']                               = "使用者";
$lang['created_by']                         = "建立";
$lang['loading_data']                       = "自伺服器载入资料";
$lang['tel']                                = "Tel";
$lang['ref']                                = "参考";
$lang['description']                        = "说明";
$lang['code']                               = "代码";
$lang['tax']                                = "税";
$lang['unit_price']                         = "单价";
$lang['discount']                           = "折扣";
$lang['order_discount']                     = "订单折扣";
$lang['total_amount']                       = "总价";
$lang['download_excel']                     = "下载为Excel";
$lang['subject']                            = "主题";
$lang['cc']                                 = "副本";
$lang['bcc']                                = "密件副本";
$lang['message']                            = "讯息";
$lang['show_bcc']                           = "显示/隐藏 密件副本";
$lang['price']                              = "价钱";
$lang['add_product_manually']               = "手动新增产品";
$lang['currency']                           = "货币";
$lang['product_discount']                   = "产品折扣";
$lang['email_sent']                         = "电子邮件送出成功";
$lang['add_event']                          = "新增事件";
$lang['add_modify_event']                   = "新增/更新事件";
$lang['adding']                             = "加入中...";
$lang['delete']                             = "删除";
$lang['deleting']                           = "删除中...";
$lang['calendar_line']                      = "请点击新增/更新事件的日期.";
$lang['discount_label']                     = "折扣 (5/5%)";
$lang['product_expiry']                     = "产品期限";
$lang['unit']                               = "单位";
$lang['cost']                               = "成本";
$lang['tax_method']                         = "税率方法";
$lang['inclusive']                          = "包含";
$lang['exclusive']                          = "不包含";
$lang['expiry']                             = "到期";
$lang['customer_group']                     = "客户群组";
$lang['is_required']                        = "是必须的";
$lang['form_action']                        = "表单动作";
$lang['return_sales']                       = "退货";
$lang['list_return_sales']                  = "退货列表";
$lang['no_data_available']                  = "没有资料";
$lang['disabled_in_demo']                   = "我们很抱歉这个功能在Demo时是关闭的。";
$lang['payment_reference_no']               = "付款参考编号";
$lang['gift_card_no']                       = "礼品卡编号";
$lang['paying_by']                          = "付款方式";
$lang['cash']                               = "现金";
$lang['gift_card']                          = "礼品卡";
$lang['CC']                                 = "信用卡";
$lang['cheque']                             = "支票";
$lang['cc_no']                              = "信用卡号码";
$lang['cc_holder']                          = "持有者";
$lang['card_type']                          = "卡片类型";
$lang['Visa']                               = "Visa";
$lang['MasterCard']                         = "MasterCard";
$lang['Amex']                               = "Amex";
$lang['Discover']                           = "Discover";
$lang['month']                              = "月";
$lang['year']                               = "年";
$lang['cvv2']                               = "CVV2";
$lang['cheque_no']                          = "支票号码";
$lang['Visa']                               = "Visa";
$lang['MasterCard']                         = "MasterCard";
$lang['Amex']                               = "Amex";
$lang['Discover']                           = "Discover";
$lang['send_email']                         = "寄送电子邮件";
$lang['order_by']                           = "下单者：";
$lang['updated_by']                         = "更新者：";
$lang['update_at']                          = "更新日期：";
$lang['error_404']                          = "ERROR 404 页面找不到 ";
$lang['default_customer_group']             = "预设客户群组";
$lang['pos_settings']                       = "POS设定";
$lang['pos_sales']                          = "POS销售";
$lang['seller']                             = "销售员";
$lang['ip:']                                = "IP:";
$lang['sp_tax']                             = "产品销售税";
$lang['pp_tax']                             = "产品采购税";
$lang['overview_chart_heading']             = "库存总览图包括成本和价格（圆饼图）与产品税和订单税（列） ，购买（线）和产品的月销量。您可以保存为JPG，PNG和PDF。";
$lang['stock_value']                        = "产品值";
$lang['stock_value_by_price']               = "产品值（售价）";
$lang['stock_value_by_cost']                = "产品值（成本）";
$lang['sold']                               = "已销售";
$lang['purchased']                          = "已采购";
$lang['chart_lable_toggle']                 = "您可以点击图表图例更改图表。点击上面的图例来显示/隐藏图表。";
$lang['register_report']                    = "收银机报表";
$lang['sEmptyTable']                        = "表内无资料";
$lang['upcoming_events']                    = "活动预告";
$lang['clear_ls']                           = "清除本机所有资料";
$lang['clear']                              = "清除";
$lang['edit_order_discount']                = "编辑订单折扣";
$lang['product_variant']                    = "产品选项";
$lang['product_variants']                   = "产品选项";
$lang['prduct_not_found']                   = "产品找不到";
$lang['list_open_registers']                = "列出开启的收银机";
$lang['delivery']                           = "交货";
$lang['serial_no']                          = "序列号";
$lang['logo']                               = "Logo";
$lang['attachment']                         = "附件";
$lang['balance']                            = "结馀";
$lang['nothing_found']                      = "找不到相符的资料";
$lang['db_restored']                        = "资料库恢复成功。";
$lang['backups']                            = "备份";
$lang['best_seller']                        = "最佳销售";
$lang['chart']                              = "图表";
$lang['received']                           = "已接收";
$lang['returned']                           = "已退回";
$lang['award_points']                       = "奖励积分";
$lang['expenses']                           = "支出";
$lang['add_expense']                        = "新增支出";
$lang['other']                              = "其他";
$lang['none']                               = "无";
$lang['calculator']                         = "计算机";
$lang['updates']                            = "更新";
$lang['update_available']                   = "目前已有新的更新，请立即更新。";
$lang['please_select_customer_warehouse']   = "请选择顾客/仓库";
$lang['variants']                           = "资料";
$lang['add_sale_by_csv']                    = "从CSV档汇入销售";
$lang['categories_report']                  = "分类报表";
$lang['adjust_quantity']                    = "调整数量";
$lang['quantity_adjustments']               = "数量调整";
$lang['partial']                            = "部分";
$lang['unexpected_value']                   = "发现溢出字元！";
$lang['select_above']                       = "请先选择上面选项";
$lang['no_user_selected']                   = "没有选择使用者，请至少选择一个使用者。";
$lang['sale_details']                       = "销售说明";
$lang['due'] 								= "到期";
$lang['ordered'] 							= "已订购";
$lang['profit'] 						    = "利润";
$lang['unit_and_net_tip'] 			        = "Calculated on unit (with tax) and net (without tax) i.e <strong>unit(net)</strong> for all sales";
$lang['expiry_alerts'] 				        = "过期警告";
$lang['quantity_alerts'] 				    = "数量警告";
$lang['products_sale']                      = "产品收益";
$lang['products_cost']                      = "产品成本";
$lang['day_profit']                         = "日损益";
$lang['get_day_profit']                     = "你可以点击日期检视当日损益。";
$lang['combine_to_pdf']                     = "结合成pdf";
$lang['print_barcode_label']                = "列印条码/标签";
$lang['list_gift_cards']                    = "礼品卡列表";
$lang['today_profit']                       = "今天收益";
$lang['adjustments']                        = "调整";
$lang['download_xls']                       = "下载为XLS";
$lang['browse']                             = "浏览 ...";
$lang['transferring']                       = "转移中";
$lang['supplier_part_no']                   = "供应商编号";
$lang['deposit']                            = "预收";
$lang['ppp']                                = "Paypal Pro";
$lang['stripe']                             = "Stripe";
$lang['amount_greater_than_deposit']        = "金额大於客户预收, 请输入低於客户预收的金额。";
$lang['stamp_sign']                         = "签名盖章";
$lang['product_option']                     = "产品选项";
$lang['Cheque']                             = "支票";
$lang['sale_reference']                     = "销售参考";
$lang['surcharges']                         = "附加费";
$lang['please_wait']                        = "请稍待...";
$lang['list_expenses']                      = "支出列表";
$lang['deposit']                            = "预收";
$lang['deposit_amount']                     = "预收金额";
$lang['return_purchases']                   = "采购退回";
$lang['list_return_purchases']              = "采购退回列表";
$lang['expense_categories']                 = "支出费用科目";
$lang['authorize']                          = "Authorize.net";
$lang['expenses_report']                    = "支出报表";
$lang['expense_categories']                 = "支出费用科目";
$lang['edit_event']                         = "编辑事件";
$lang['title']                              = "标题";
$lang['event_error']                        = "标题与开始是必须的";
$lang['start']                              = "开始";
$lang['end']                                = "结束";
$lang['event_added']                        = "事件新增成功";
$lang['event_updated']                      = "事件更新成功";
$lang['event_deleted']                      = "事件删除成功";
$lang['event_color']                        = "事件颜色";
$lang['toggle_alignment']                   = "画面左右切换";
$lang['images_location_tip']                = "图片应上传至 <strong>uploads</strong> 资料夹.";
$lang['this_sale']                          = "本次销售";
$lang['return_ref']                         = "退回参考";
$lang['return_total']                       = "全部退回";
$lang['daily_purchases']                    = "日采购";
$lang['monthly_purchases']                  = "月采购";
$lang['reference']                          = "参考";
$lang['no_subcategory']                     = "无子类别";
$lang['returned_items']                     = "已退回品项";
$lang['return_payments']                    = "已退回付款";
$lang['units']                              = "单位";
$lang['price_group']                        = "价钱群组";
$lang['price_groups']                       = "价钱群组";
$lang['no_record_selected']                 = "没有选择, 请至少选择一行项目";
$lang['brand']                              = "品牌";
$lang['brands']                             = "品牌";
$lang['file_x_exist']                       = "系统找不到该文件，它可能被删除或移动。";
$lang['status_updated']                     = "状态已更新成功";
$lang['x_col_required']                     = "前 %d 个栏位是必须的，其馀的是可选的.";
$lang['brands_report']                      = "品牌报表";
$lang['add_adjustment']                     = "新增数量调整";
$lang['best_sellers']                       = "最佳销售";
$lang['adjustments_report']                 = "数量调整报表";
$lang['stock_counts']                       = "库存计算";
$lang['count_stock']                        = "计算库存";
$lang['download']                           = "下载";

$lang['please_select_these_before_adding_product'] = "新增产品前请先选择这些资料";
