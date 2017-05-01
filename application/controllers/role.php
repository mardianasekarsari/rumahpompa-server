<?php

require APPPATH.'/libraries/REST_Controller.php';

/**
 * Created by PhpStorm.
 * User: Mardiana
 * Date: 3/3/2017
 * Time: 6:26 PM
 */
class Role extends REST_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('role_model');
        $this->load->model('roleuser_model');
    }

    function role_get(){
        
        $role["result"] = $this->role_model->getAll();
        //$petugas = $this->db->get('role')->result();
        $this->response($role, 200);

        /*$respon["responCode"] = "01";
        $respon["responData"] = "Nomor Referensi Tidak Ditemukan";
        $this->response($respon, 200);*/
    }

    function getByUsername_get($username){  
        //$username = $this->post('username');
        $idrole = $this->roleuser_model->getbyUsername($username)->id_role;

        $role["result"]["nama_role"] = ucwords(strtolower($this->role_model->getbyId($idrole)->nama_role));
        $this->response($role, 200);
    }

    /*function getByUsername_post(){  
        $username = $this->post('username');
        $idrole = $this->roleuser_model->getbyUsername($username)->id_role;

        $role["nama_role"] = ucwords(strtolower($this->role_model->getbyId($idrole)->nama_role));
        $this->response($role, 200);
    }*/
}