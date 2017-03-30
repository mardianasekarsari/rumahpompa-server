<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * User
 *
 * Service untuk kelola data user
 * 	- register
 * 	- login
 * 	- view_profile
 *	- update_profile
 *
 * 
 * <===== LOG =====>
 *
*/

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH.'/libraries/REST_Controller.php';

class User extends REST_Controller
{

	function __construct()
	{
		parent::__construct();
		/*$this->data = array("rc" => 'UR', "desc" => "Response belum didefinisikan");

		return $this->response($this->data, 200);*/
		/*$this->data = array("rc" => 'UR', "desc" => "Response belum didefinisikan");
		$this->response($this->data, 200);*/
	}

	function user_get(){
		$this->load->model('user_model');
		$petugas = $this->user_model->getAllUser();
		//$petugas = $this->db->get('role')->result();
		$this->response($petugas, 200);
		
		/*$respon["responCode"] = "01";
		$respon["responData"] = "Nomor Referensi Tidak Ditemukan";
		$this->response($respon, 200);*/

	}

	function user_post(){
		$data = array(
			'id_role' => $this->post('id_role'),
			'nama_role' => $this->post('nama_role'),
			'created_at' => $this->post('created_at'), 
			'updated_at' => $this->post('updated_at'),
			'soft_delete' => $this->post('soft_delete') );
		$insert = $this->db->insert('role', $data);
		if ($insert){
			$this->response($data, 200);
		}else{
			$this->response(array('status' => 'fail', 502));
		}
		/*$respon["responCode"] = "01";
		$respon["responData"] = "Nomor Referensi Tidak Ditemukan";
		$this->response($respon, 200);*/

	}

	function login_post(){
		$this->load->model('user_model');
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
                $user_rumahpompa = $this->rumahpompa_model->getbyUser($username);


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

	function store_post(){
        //var_dump($this->input->post());
        $this->load->model('user_model');
        $this->load->model('role_model');
        $this->load->model('rumahpompa_model');

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

    function edit_post(){
        $this->load->model('user_model');
        date_default_timezone_set('Asia/Jakarta');
        $date_insert = date('Y-m-d h:i:s', time());
        $username = $this->post('username');
        $data = array(
            'nama_user' => $this->post('name'),
            'alamat_user' => $this->post('address'),
            'no_telp_user' => $this->post('phone'),
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
    }

    function editToken_post(){
        $this->load->model('user_model');
        date_default_timezone_set('Asia/Jakarta');
        $date_update = date('Y-m-d h:i:s', time());

        $username = $this->post('username');
        $token = $this->post('token');
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

    function deleteToken_post(){
        $this->load->model('user_model');
        date_default_timezone_set('Asia/Jakarta');
        $date_update = date('Y-m-d h:i:s', time());

        $username = $this->post('username');
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

}
	