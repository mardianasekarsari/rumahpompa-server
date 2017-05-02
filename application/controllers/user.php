<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';
require APPPATH.'/libraries/JWT.php';
use \Firebase\JWT\SignatureInvalidException;
require APPPATH.'/libraries/SignatureInvalidException.php';
use \Firebase\JWT\JWT;

//require 'apikey.php';

class User extends REST_Controller
{

	function __construct()
	{
		parent::__construct();
        $this->load->helper('url');
        $this->load->model('user_model');
        $this->load->model('roleuser_model');
        $this->load->model('user_rumahpompa_model');
        $this->load->model('role_model');
        $this->load->model('rumahpompa_model');

		/*$this->data = array("rc" => 'UR', "desc" => "Response belum didefinisikan");

		return $this->response($this->data, 200);*/
		/*$this->data = array("rc" => 'UR', "desc" => "Response belum didefinisikan");
		$this->response($this->data, 200);*/
	}

    function validateToken($apikey){
        $algorithm = 'HS512';
        $secretKey = base64_decode($this->config->item('jwt_key')); 

        $res = array(false, '');
        // using a try and catch to verify
        try {
            $token = JWT::decode($apikey, $secretKey, array($algorithm));
            $res['0'] = true;
            $res['1'] = (array) $token;
        } catch (Exception $e) {
            $res['0'] = false;
            $res['1'] = '';
        }
    
        return $res;
    }

	function user_get(){
        
        $headers=array();
        foreach (getallheaders() as $name => $value) {
            $headers[$name] = $value;
        }

        $tokenVal = $this->validateToken($headers["Api-key"]);
        if ($tokenVal['0']) {
            $username = $token->data->userName;
            $user["result"] = $this->user_model->getAllUser();
            $this->response($user, 200);
        } else{
            $invalid = "Token Tidak Valid";
            $this->response($invalid, 200);
        } 
        
	}

    function getbyUsername_get($username){
        //$username = $this->uri->segment(2);
        $user["result"] = $this->user_model->getbyUsername($username);
        $this->response($user, 200);
    }

    function user_post(){
        //var_dump($this->input->post());

        date_default_timezone_set('Asia/Jakarta');
        $date_insert = date('Y-m-d h:i:s', time());

       $data_user = array(
            'username' => $this->post('username'),
            'nama_user' => $this->post('name'),
            'alamat_user' => $this->post('address'),
            'no_telp_user' => $this->post('phone'),
            'password' => md5($this->post('password')),
            'created_at' => $date_insert,
            'updated_at' => $date_insert,
            'soft_delete' => 'false');

        $data_role = array(
            'username' => $this->post('username'),
            'id_role' => (int)$this->role_model->getbyName($this->post('role'))->id_role,
            'isactive' => 'TRUE',
            'created_at' => $date_insert,
            'updated_at' => $date_insert,
            'soft_delete' => 'false'
        );

        $data_user_rumahpompa = array(
            'id_rumah_pompa' => $this->rumahpompa_model->getbyName($this->post('rumah_pompa'))->id_rumah_pompa,
            'username' => $this->post('username'),
            'isactive' => 'TRUE',
            'created_at' => $date_insert,
            'updated_at' => $date_insert,
            'soft_delete' => 'false'
        );

        $username = $this->post('username');
        $name = $this->post('name');
        $address = $this->post('address');
        $phone = $this->post('phone');
        $role = $this->post('role');
        $rumah_pompa = $this->post('rumah_pompa');
        $password = $this->post('password');

        if(isset($username) && isset($name) && isset($address) && isset($phone) && isset($role) && isset($password) && isset($rumah_pompa)){
            /*$respon["msg2"]=$this->user_model->check_existing_user($username);
            $this->response($respon, 200);*/
            if ($this->user_model->check_existing_user($username)==0){
                $query = $this->user_model->store($data_user, $data_role, $data_user_rumahpompa);
                 if($query){
                     $respon["status"]= true;
                     $respon["msg"]= "Register Berhasil";
                 }else{
                     $respon["status"]= false;
                     $respon["msg"]= "Register Gagal";
                 }
            }
            else{
                $respon["status"]= false;
                $respon["msg"]= "Username sudah terpakai";
            }
            $this->response($respon, 200);
            //echo $this->role_model->getbyName($this->post('role'))->id_role;
        }
    }

	/*function user_post(){
        date_default_timezone_set('Asia/Jakarta');
        $date_insert = date('Y-m-d h:i:s', time());
		$data = array(
			'id_role' => $this->post('id_role'),
			'nama_role' => $this->post('nama_role'),
			'created_at' => $date_insert, 
			'updated_at' => $date_insert,
			'soft_delete' => 'false' );
		$insert = $this->db->insert('role', $data);
		if ($insert){
			$this->response($data, 200);
		}else{
			$this->response(array('status' => 'fail', 502));
		}
		

	}*/

	function login_post(){
        $this->load->model('rumahpompa_model');

		$data = array(
			'username' => $this->post('username'),
			'password' => $this->post('password'));

		$username = $this->post('username');
		$password = $this->post('password');

		$user = $this->user_model->getbyUsername($username);
        $role_user = $this->user_model->get_role_user($username);

        if($this->user_model->check_existing_user($username)==0)
        {
            $respon["status"]= false;
            $respon["msg"]= "User tidak terdaftar";
        }
        else{
            $check_login = $this->user_model->check_login($username, md5($password));
            if ($check_login==0){
                $respon["status"]= false;
                $respon["msg"]= "Username dan password tidak valid";
                //$this->response($respon, 200);
            }
            else{
                //$user_rumahpompa = $this->rumahpompa_model->getbyUser($username);
                $user_rumahpompa = $this->user_rumahpompa_model->getbyUsername($username);


                $respon["status"]= true;
                //$respon["user"] = $user;
                $respon["user"]["username"] = $username;
                $respon["user"]["address"] = $user->alamat_user;
                $respon["user"]["name"] = $user->nama_user;
                $respon["user"]["phone"] = $user->no_telp_user;
                $respon["user"]["role"] = $role_user->nama_role;
                $respon["user"]["rumah_pompa"] = $user_rumahpompa->id_rumah_pompa;
                $respon["msg"]= "Sukses Login";
                //$this->response($respon, 200);
            }
        }
        $this->response($respon, 200);
	}

    function user_put(){

        date_default_timezone_set('Asia/Jakarta');
        $date_insert = date('Y-m-d h:i:s', time());
        $username = $this->put('username');
        $data_user = array(
            'username' => $this->put('username'),
            'nama_user' => $this->put('name'),
            'alamat_user' => $this->put('address'),
            'no_telp_user' => $this->put('phone'),
            'updated_at' => $date_insert,);

        $data_role = array(
            'username' => $this->put('username'),
            'id_role' => (int)$this->role_model->getbyName($this->put('role'))->id_role,
            'isactive' => 'TRUE',
            'created_at' => $date_insert,
            'updated_at' => $date_insert,
            'soft_delete' => 'false'
        );

        $data_user_rumahpompa = array(
            'id_rumah_pompa' => $this->rumahpompa_model->getbyName($this->put('rumah_pompa'))->id_rumah_pompa,
            'username' => $this->put('username'),
            'isactive' => 'TRUE',
            'created_at' => $date_insert,
            'updated_at' => $date_insert,
            'soft_delete' => 'false'
        );

        $query = $this->user_model->edit($data_user, $data_role, $data_user_rumahpompa);
        if($query){
            $respon["status"]= true;
            $respon["msg"]= "Edit Berhasil";
        }else{
            $respon["status"]= false;
            $respon["msg"]= "Edit Gagal";
        }
        $this->response($respon, 200);
    }

    function user_delete($username){
        date_default_timezone_set('Asia/Jakarta');
        $date_update = date('Y-m-d h:i:s', time());
        //$username = $this->delete('username');
        //$username = $this->uri->segment(2);

        //Menghapus Table rumahpompauser
        $data_userrumahpompa = array(
            'isactive' => FALSE,
            'soft_delete' => TRUE,
            'updated_at' => $date_update);
        $update1 = $this->user_rumahpompa_model->edit($username, $data_userrumahpompa);

        //Menghapus Table Roleuser, isactive false
        $data_roleuser = array(
            'isactive' => FALSE,
            'soft_delete' => TRUE,
            'updated_at' => $date_update);
        $update2 = $this->roleuser_model->edit($username, $data_roleuser );

        //Menghapus di table user_
        $data_user = array(
            'soft_delete' => TRUE,
            'updated_at' => $date_update);
        $update3 = $this->user_model->editUser($username, $data_user );

        if ($update1 && $update2 && $update3) {
            $respon["status"]= true;
            $respon["msg"]= "Delete Berhasil";
        }
        else{
            $respon["status"]= false;
            $respon["msg"]= "Delete Gagal";
        }
        $this->response($respon, 200);
	}

    function deleteToken_delete(){
        date_default_timezone_set('Asia/Jakarta');
        $date_update = date('Y-m-d h:i:s', time());
        $username = $this->uri->segment(2);
        //$username = $this->post('username');
        $data = array(
            'token' => NULL,
            'updated_at' => $date_update);
        $update = $this->user_model->editUser($username, $data);
        if($update){
            $respon["status"]= true;
            $respon["msg"]= "Edit Berhasil";
        }else{
            $respon["status"]= false;
            $respon["msg"]= "Edit Gagal";
        }
        $this->response($respon, 200);
    }

     function editToken_put(){
        date_default_timezone_set('Asia/Jakarta');
        $date_update = date('Y-m-d h:i:s', time());

        $username = $this->uri->segment(2);
        $token = $this->put('token');
        $data = array(
            'token' => $token,
            'updated_at' => $date_update);
        $update = $this->user_model->editUser($username, $data);
        if($update){
            $respon["status"]= true;
            $respon["msg"]= "Edit Berhasil";
        }else{
            $respon["status"]= false;
            $respon["msg"]= "Edit Gagal";
        }
        $this->response($respon, 200);
    }

    function changePassword_put(){
        //var_dump($this->input->post());

        date_default_timezone_set('Asia/Jakarta');
        $date_insert = date('Y-m-d h:i:s', time());
        $username = $this->uri->segment(2);
        $oldpassword = $this->put('oldpassword');
        $newpassword = $this->put('newpassword');
        $data = array(
            'password' => md5($this->put('newpassword')),
            'updated_at' => $date_insert);

        $userpassword = $this->user_model->getbyUsername($username)->password;
        if ($userpassword==md5($oldpassword)) {
            if ($oldpassword==$newpassword) {
                $respon["status"]= false;
                $respon["kode"]= 3;
                $respon["msg"]= "Password lama sama dengan password baru";
            }
            else{
                $query = $this->user_model->editUser($username, $data);
                if($query){
                    $respon["status"]= true;
                    $respon["msg"]= "Edit Berhasil";
                }else{
                    $respon["status"]= false;
                    $respon["kode"]= 1;
                    $respon["msg"]= "Edit Gagal";
                }
            }
        }
        else{
            $respon["status"]= false;
            $respon["kode"]= 2;
            $respon["msg"]= "Password Lama Salah";
        }
        $this->response($respon, 200);
    }


    /*function edit_post(){

        date_default_timezone_set('Asia/Jakarta');
        $date_insert = date('Y-m-d h:i:s', time());

       $data_user = array(
            'username' => $this->post('username'),
            'nama_user' => $this->post('name'),
            'alamat_user' => $this->post('address'),
            'no_telp_user' => $this->post('phone'),
            'updated_at' => $date_insert,);

        $data_role = array(
            'username' => $this->post('username'),
            'id_role' => (int)$this->role_model->getbyName($this->post('role'))->id_role,
            'isactive' => 'TRUE',
            'created_at' => $date_insert,
            'updated_at' => $date_insert,
            'soft_delete' => 'false'
        );

        $data_user_rumahpompa = array(
            'id_rumah_pompa' => $this->rumahpompa_model->getbyName($this->post('rumah_pompa'))->id_rumah_pompa,
            'username' => $this->post('username'),
            'isactive' => 'TRUE',
            'created_at' => $date_insert,
            'updated_at' => $date_insert,
            'soft_delete' => 'false'
        );

        $query = $this->user_model->edit($data_user, $data_role, $data_user_rumahpompa);

        if($query){
            $respon["status"]= true;
            $respon["msg"]= "Edit Berhasil";
        }else{
            $respon["status"]= false;
            $respon["msg"]= "Edit Gagal";
        }
        $this->response($respon, 200);
    }
*/

    /*function editProfil_put(){
        date_default_timezone_set('Asia/Jakarta');
        $date_insert = date('Y-m-d h:i:s', time());
        $username = $this->put('username');
        $data = array(
            'nama_user' => $this->put('name'),
            'alamat_user' => $this->put('address'),
            'no_telp_user' => $this->put('phone'),
            'updated_at' => $date_insert);

        $update = $this->user_model->editUser($username, $data);
        if($update){
            $respon["status"]= true;
            $respon["msg"]= "Edit Berhasil";
        }else{
            $respon["status"]= false;
            $respon["msg"]= "Edit Gagal";
        }
        $this->response($respon, 200);
    }*/

   
    

   /* function delete_post(){
        date_default_timezone_set('Asia/Jakarta');
        $date_update = date('Y-m-d h:i:s', time());
        $username = $this->post('username');

        //Menghapus Table rumahpompauser
        $data_userrumahpompa = array(
            'isactive' => FALSE,
            'soft_delete' => TRUE,
            'updated_at' => $date_update);
        $update1 = $this->user_rumahpompa_model->edit($username, $data_userrumahpompa);

        //Menghapus Table Roleuser, isactive false
        $data_roleuser = array(
            'isactive' => FALSE,
            'soft_delete' => TRUE,
            'updated_at' => $date_update);
        $update2 = $this->roleuser_model->edit($username, $data_roleuser );

        //Menghapus di table user_
        $data_user = array(
            'soft_delete' => TRUE,
            'updated_at' => $date_update);
        $update3 = $this->user_model->editUser($username, $data_user );

        if ($update1 && $update2 && $update3) {
            $respon["status"]= true;
            $respon["msg"]= "Delete Berhasil";
        }
        else{
            $respon["status"]= false;
            $respon["msg"]= "Delete Gagal";
        }
        $this->response($respon, 200);
        
    }*/

}
	