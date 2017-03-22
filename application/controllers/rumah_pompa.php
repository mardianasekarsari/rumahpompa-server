<?php

require APPPATH.'/libraries/REST_Controller.php';

/**
 * Created by PhpStorm.
 * User: Mardiana
 * Date: 2/27/2017
 * Time: 12:24 PM
 */
class Rumah_pompa extends REST_Controller
{
    function __construct()
    {
        parent::__construct();
        /*$this->data = array("rc" => 'UR', "desc" => "Response belum didefinisikan");

        return $this->response($this->data, 200);*/
        /*$this->data = array("rc" => 'UR', "desc" => "Response belum didefinisikan");
        $this->response($this->data, 200);*/
    }

    function rumahpompa_get(){

        $this->load->model('rumahpompa_model');
        $rumah_pompa["result"] = $this->rumahpompa_model->getAll();

        $this->response($rumah_pompa, 200);
    }

    function getrumahpompabyId_post(){
        $id = $this->post('id');
        $this->load->model('rumahpompa_model');
        $rumah_pompa["result"] = $this->rumahpompa_model->getbyId($id);
        $this->response($rumah_pompa, 200);
    }

    function edit_post(){
        $this->load->model('rumahpompa_model');
        date_default_timezone_set('Asia/Jakarta');
        $date_update = date('Y-m-d h:i:s', time());
        $id = $this->post('id');
        $data = array(
            'nama_' => $this->post('name'),
            'jalan' => $this->post('address'),
            'no_telp_rumah_pompa' => $this->post('phone'),
            'threshold_tinggi_air' => (int)$this->post('threshold'),
            'updated_at' => $date_update);

        $update = $this->rumahpompa_model->edit($id, $data);
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