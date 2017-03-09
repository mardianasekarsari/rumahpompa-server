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
    }

    function role_get(){
        $this->load->model('role_model');
        $role["result"] = $this->role_model->getAll();
        //$petugas = $this->db->get('role')->result();
        $this->response($role, 200);

        /*$respon["responCode"] = "01";
        $respon["responData"] = "Nomor Referensi Tidak Ditemukan";
        $this->response($respon, 200);*/
    }
}