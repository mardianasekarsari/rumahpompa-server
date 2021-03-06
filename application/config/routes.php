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
$route['default_controller'] = 'welcome';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;


//Route user
$route['login']['POST'] = 'user/login';
$route['user']['GET'] = 'user/user';
$route['user/(:any)']['GET'] = 'user/getbyUsername/$1';
$route['user']['POST'] = 'user/user';
$route['user']['PUT'] = 'user/user';
$route['user/(:any)']['DELETE'] = 'user/user/$1';
//$route['token/(:any)']['GET'] = 'user/deleteToken/$1';
$route['user/(:any)/token']['DELETE'] = 'user/deleteToken/$1';
$route['user/(:any)/token']['PUT'] = 'user/editToken';
$route['user/(:any)/password']['PUT'] = 'user/changePassword';

//Route rumah pompa
$route['rumah-pompa']['GET'] = 'rumah_pompa/rumahpompa';
$route['rumah-pompa/(:any)']['GET'] = 'rumah_pompa/getrumahpompabyId/$1';
/*$route['rumah-pompa/(:any)/name']['GET'] = 'rumah_pompa/getrumahpompabyName';
$route['rumah-pompa/(:any)/status']['GET'] = 'rumah_pompa/getrumahpompabyStatus';*/
$route['rumah-pompa']['POST'] = 'rumah_pompa/rumahpompa';
$route['rumah-pompa']['PUT'] = 'rumah_pompa/rumahpompa';
$route['rumah-pompa/(:any)']['DELETE'] = 'rumah_pompa/rumah_pompa/$1';

//Route role
$route['role']['GET'] = 'role/role';
$route['role/(:any)']['GET'] = 'role/getByUsername/$1';

//Route Data
$route['data']['GET'] = 'data/getalllastdata';
$route['data/(:any)']['GET'] = 'data/getbyId/$1';

//Route Apikey
$route['apikey']['POST'] = 'apikey/apikey';

