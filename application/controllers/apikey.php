<?php

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH.'/libraries/REST_Controller.php';
require APPPATH.'/libraries/JWT.php';
use \Firebase\JWT\JWT;

class Apikey extends REST_Controller
{
	
	function __construct()
	{
		parent::__construct();
		$this->load->model('user_model');
	}

	function apikey_post(){
		
		$key = $this->config->item('jwt_key');
		$server = "http://rumahpompa-server.com";

		/*$keypost = $this->post('key');
		$server = $this->post('domain');*/
		$user = $this->post('username');
		$password = $this->post('password');
		$userpasspost = $this->post('key');
		$userpass = $user.$password;

		$username = $this->user_model->getbyUsername($user);
		if ($username!=null && $username->username==$user && $username->password==md5($password) && $userpasspost==$userpass) {
			$date = new DateTime();
			$token = array(
			    "iss" => $server,
			    'iat' => $date->getTimestamp(),
            	/*'exp' = $date->getTimestamp() + 60*60*5;*/
			    'data' => [ 
		            'userName' => $user
		        ]
			);
			$output['result']['status'] = 'valid';
			$output['result']['token'] = JWT::encode($token, $key);
		}
		else{
			$output['result']['status'] = 'invalid';
		}

		$this->response($output, 200);

	}

}