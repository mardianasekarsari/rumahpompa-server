<?php

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH.'/libraries/REST_Controller.php';
require APPPATH.'/libraries/JWT.php';
use \Firebase\JWT\SignatureInvalidException;
require APPPATH.'/libraries/SignatureInvalidException.php';
use \Firebase\JWT\JWT;

class Apikey extends REST_Controller
{
	
	function __construct()
	{
		parent::__construct();
		$this->load->model('user_model');
	}

	function apikey_post(){
		$server = "http://rumahpompa-server.com";
		$secret_key = $this->config->item('jwt_key');
		$algorithm = 'HS512';
		$key = base64_decode($secret_key);

		$user = $this->post('username');
		$password = $this->post('password');
		$userpasspost = $this->post('key');
		$userpass = $user.$password;

		if ($user!="" && $password!= "" && $userpasspost!="") {

			$username = $this->user_model->getbyUsername($user);
		//print_r($user);

			if ($username!=null && $username->username==$user && $username->password==md5($password) && $userpasspost==$userpass) {
				$date = new DateTime();
				$token = array(
				    "iss" => $server,
				    'iat' => $date->getTimestamp(),
	            	//'exp' = $date->getTimestamp() + 60*60*5
				    'data' => [ 
			            'userName' => $user
			        ]
				);
				$output['result']['status'] = 'valid';
				$output['result']['token'] = JWT::encode($token, $key, $algorithm);
			}
			else{
				$output['result']['status'] = 'invalid';
				$output['result']['msg'] = 'Token Invalid';
			}
		}
		else{
			$output['result']['status'] = 'invalid';;
		}


		$this->response($output, 200);

	}

	function validateToken($apikey){
		$algorithm = 'HS512';
		$secretKey = base64_decode($this->config->item('jwt_key')); 
		$res = array(false, '');
	    // using a try and catch to verify
	    try {
	    	$token = JWT::decode($apikey, $secretKey, array($algorithm));
	    } catch (Exception $e) {
	      return $res;
	    }
	    $res['0'] = true;
	    $res['1'] = (array) $token;
	 
	    return $res;
	}

}