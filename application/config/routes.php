<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'login';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

$route['logout'] = 'login/logout'; 
$route['dash'] = 'dashboard'; 

$route['add-tender-enquiry']='tender/add_tender_enquiry';
$route['tender-enquiry-list'] = 'tender/tender_enquiry_list';
$route['tender-enquiry-edit/(:num)'] = 'tender/edit_tender_enquiry/$1'; 
$route['tender-quotation-list'] = 'tender/tender_quotation_list';
$route['tender-quotation-print/(:num)'] = 'tender/tender_quotation_print/$1';

$route['change-password'] = 'login/change_password';
$route['dash'] = 'dashboard'; 
$route['get-data'] = 'general/get_data';
$route['update-data'] = 'general/update_data';
$route['insert-data'] = 'general/insert_data';
$route['delete-record'] = 'general/delete_record';
$route['get-content'] = 'general/get_content';   

$route['company-list'] = 'master/company_list';
$route['company-list/(:num)'] = 'master/company_list/$1';

$route['category-list'] = 'master/category_list';
$route['category-list/(:num)'] = 'master/category_list/$1';

$route['items-list'] = 'master/items_list';
$route['items-list/(:num)'] = 'master/items_list/$1';

$route['brand-list'] = 'master/brand_list';
$route['brand-list/(:num)'] = 'master/brand_list/$1';

$route['uom-list'] = 'master/uom_list';
$route['uom-list/(:num)'] = 'master/uom_list/$1';
 
$route['currency-list'] = 'master/currency_list';
$route['currency-list/(:num)'] = 'master/currency_list/$1';
 
$route['gst-list'] = 'master/gst_list';
$route['gst-list/(:num)'] = 'master/gst_list/$1';

$route['user-list'] = 'master/user_list';
$route['user-list/(:num)'] = 'master/user_list/$1';

$route['vendor-list'] = 'master/vendor_list';
$route['vendor-list/(:num)'] = 'master/vendor_list/$1';

$route['vendor-contact-list'] = 'master/vendor_contact_list';
$route['vendor-contact-list/edit/(:num)'] = 'master/vendor_contact_list/$1';

$route['customer-list'] = 'master/customer_list';
$route['customer-list/(:num)'] = 'master/customer_list/$1';
$route['customer-contact-list'] = 'master/customer_contact_list';
$route['customer-contact-list/edit/(:num)'] = 'master/customer_contact_list/$1';

$route['vendor-rate-enquiry'] = 'vendor/vendor_rate_enquiry';
$route['vendor-rate-enquiry-edit/(:num)'] = 'vendor/vendor_rate_enquiry_edit/$1';
$route['vendor-rate-enquiry-list'] = 'vendor/vendor_rate_enquiry_list';
$route['vendor-rate-enquiry-print/(:num)'] = 'vendor/vendor_rate_enquiry_print/$1';


$route['tender-quotation-add'] = 'tender/tender_quotation_add';
$route['tender-quotation-edit/(:num)'] = 'tender/tender_quotation_edit/$1';

$route['tender-quotation-po'] = 'tender/tender_quotation_po';
$route['tender-quotation-po/(:num)'] = 'tender/tender_quotation_po/$1';

$route['customer-tender-po-add'] = 'tender/customer_tender_po_add';
$route['tender/get_quotations_by_customer'] = 'tender/get_quotations_by_customer';
$route['tender/get_quotation_items'] = 'tender/get_quotation_items';
$route['customer-tender-po-list'] = 'tender/customer_tender_po_list';
$route['customer-tender-po-edit/(:num)'] = 'tender/customer_tender_po_edit/$1';


$route['vendor-quotation-add'] = 'vendor/vendor_quotation_add';
$route['vendor-quotation-list'] = 'vendor/vendor_quotation_list';
$route['vendor-quotation-edit/(:num)'] = 'vendor/vendor_quotation_edit/$1';
$route['vendor-quotation-print/(:num)'] = 'vendor/vendor_quotation_print/$1';

$route['vendor-po-add'] = 'vendor/vendor_po_add'; 
$route['vendor-po-list'] = 'vendor/vendor_po_list';
$route['vendor-po-edit/(:num)'] = 'vendor/vendor_po_edit/$1';
$route['vendor-po-view/(:num)'] = 'vendor/vendor_po_view/$1';

$route['vendor-pur-inward-add'] = 'vendor/vendor_pur_inward_add'; 
$route['vendor-pur-inward-list'] = 'vendor/vendor_pur_inward_list';
$route['vendor-pur-inward-edit/(:num)'] = 'vendor/vendor_pur_inward_edit/$1'; 



/*Accounts Routes*/ 

$route['inward-list'] = 'accounts/cash_inward_list'; 
$route['inward-list/(:num)'] = 'accounts/cash_inward_list/$1'; 

$route['outward-list'] = 'accounts/cash_outward_list'; 
$route['outward-list/(:num)'] = 'accounts/cash_outward_list/$1'; 

$route['opening-balance-list'] = 'accounts/opening_balance_list';
$route['opening-balance-list/(:num)'] = 'accounts/opening_balance_list/$1';

$route['print-voucher/(:num)'] = 'accounts/print_voucher/$1';
$route['print-receipt/(:num)'] = 'accounts/print_receipt/$1';

$route['cash-ledger'] = 'accounts/cash_ledger';

$route['cash-in-statement'] = 'accounts/cash_in_statement'; 
$route['na-cash-in-statement'] = 'accounts/na_cash_in_statement'; 
$route['cash-out-statement'] = 'accounts/cash_out_statement';
$route['na-cash-out-statement'] = 'accounts/na_cash_out_statement';
$route['outward-summary'] = 'accounts/outward_summary';
$route['inward-summary'] = 'accounts/inward_summary';

$route['tds-report'] = 'accounts/tds_report';
$route['bills-report'] = 'accounts/bills_report';

$route['account-head-list'] = 'accounts/account_head_list';
$route['account-head-list/(:num)'] = 'accounts/account_head_list/$1';

$route['sub-account-head-list'] = 'accounts/sub_account_head_list';
$route['sub-account-head-list/(:num)'] = 'accounts/sub_account_head_list/$1';

$route['account-head-for-list'] = 'accounts/account_head_for_list';
$route['account-head-for-list/(:num)'] = 'accounts/account_head_for_list/$1';

$route['company-bank-list'] = 'accounts/company_bank_list';
$route['company-bank-list/(:num)'] = 'accounts/company_bank_list/$1';

$route['voucher-type-list'] = 'accounts/voucher_type_list';
$route['voucher-type-list/(:num)'] = 'accounts/voucher_type_list/$1';


