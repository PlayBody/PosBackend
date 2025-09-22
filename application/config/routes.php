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

$route['default_controller'] = "dashboard";
$route['404_override'] = 'error_404';
$route['translate_uri_dashes'] = FALSE;


/*********** USER DEFINED ROUTES *******************/

$route['loginMe'] = 'login/loginMe';
$route['dashboard'] = 'dashboard';
$route['userList'] = 'user/userList';
$route['userList/(:num)'] = "user/userList/$1";
$route['addNew'] = "user/addNew";
$route['addNewUser'] = "user/addNewUser";
$route['editOld'] = "user/editOld";
$route['editOld/(:num)'] = "user/editOld/$1";
$route['editUser'] = "user/editUser";
$route['deleteUser'] = "user/deleteUser";
//$route['profile'] = "user/profile";
//$route['profile/(:any)'] = "user/profile/$1";
//$route['profileUpdate'] = "user/profileUpdate";
//$route['profileUpdate/(:any)'] = "user/profileUpdate/$1";

$route['loadChangePass'] = "user/loadChangePass";
$route['changePassword'] = "user/changePassword";
$route['changePassword/(:any)'] = "user/changePassword/$1";
$route['pageNotFound'] = "user/pageNotFound";
$route['checkEmailExists'] = "user/checkEmailExists";
$route['login-history'] = "user/loginHistoy";
$route['login-history/(:num)'] = "user/loginHistoy/$1";
$route['login-history/(:num)/(:num)'] = "user/loginHistoy/$1/$2";

$route['forgotPassword'] = "login/forgotPassword";
$route['resetPasswordUser'] = "login/resetPasswordUser";
$route['resetPasswordConfirmUser'] = "login/resetPasswordConfirmUser";
$route['resetPasswordConfirmUser/(:any)'] = "login/resetPasswordConfirmUser/$1";
$route['resetPasswordConfirmUser/(:any)/(:any)'] = "login/resetPasswordConfirmUser/$1/$2";
$route['createPasswordUser'] = "login/createPasswordUser";
$route['chat/(:any)'] = "chat/index/$1";
$route['scenario'] = "scenario/index";


$route['admin/shift_status'] = "web/admin/shiftStatus/index";
$route['admin/shift_status_save'] = "web/admin/shiftStatus/save";
$route['admin/shift_status_delete'] = "web/admin/shiftStatus/delete";

$route['admin/company'] = "web/admin/company/index";
$route['admin/company_edit'] = "web/admin/company/edit";
$route['admin/company_epark'] = "web/admin/company/epark";
$route['admin/company_epark_update'] = "web/admin/company/epark_update";

$route['shift/shift'] = "web/shift/shift/index";
$route['shift/ajax_load_main'] = "web/shift/shift/ajaxLoadMain";

// ---------------- api routes ---------------
$route ['api/company-issync'] = "apis/company/isSyncEpark";

$route ['api/staff-login'] = "apis/staff/login";
$route ['api/staff-list'] = "apis/staff/list";
$route ['api/staff-detail'] = "apis/staff/detail";
$route ['api/staff-save'] = "apis/staff/save";

$route ['api/organ-list'] = "apis/organ/list";
$route ['api/organ-bussiness_time'] = "apis/organ/loadBussinessTime";

$route ['api/shift_frame-list'] = "apis/shiftFrame/list";

$route ['api/epark-sync'] = "apis/EparkSync/sendToEpark";

/* End of file routes.php */
/* Location: ./application/config/routes.php */
