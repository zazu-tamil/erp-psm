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
 
$route['gst-list'] = 'master/gst_list';
$route['gst-list/(:num)'] = 'master/gst_list/$1';

$route['user-list'] = 'master/user_list';
$route['user-list/(:num)'] = 'master/user_list/$1';

$route['vendor-list'] = 'master/vendor_list';
$route['vendor-list/(:num)'] = 'master/vendor_list/$1';

$route['customer-list'] = 'master/customer_list';
$route['customer-list/(:num)'] = 'master/customer_list/$1';