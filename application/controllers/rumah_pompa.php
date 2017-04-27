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
        $this->load->model('rumahpompa_model');
        $this->load->model('user_rumahpompa_model');
        /*$this->data = array("rc" => 'UR', "desc" => "Response belum didefinisikan");

        return $this->response($this->data, 200);*/
        /*$this->data = array("rc" => 'UR', "desc" => "Response belum didefinisikan");
        $this->response($this->data, 200);*/
    }

    function rumahpompa_get(){
        $rumah_pompa["result"] = $this->rumahpompa_model->getAll();

        $this->response($rumah_pompa, 200);
    }

    function getrumahpompabyId_post(){
        $id = $this->post('id');
        $rumah_pompa["result"] = $this->rumahpompa_model->getbyId($id);
        $this->response($rumah_pompa, 200);
    }

    function edit_post(){
        date_default_timezone_set('Asia/Jakarta');
        $date_update = date('Y-m-d h:i:s', time());
        $id = $this->post('id');
        $data = array(
            'nama_' => $this->post('name'),
            'jalan' => $this->post('address'),
            'no_telp_rumah_pompa' => $this->post('phone'),
            'threshold_tinggi_air' => (int)$this->post('threshold'),
            'latitude' => (double)$this->post('latitude'),
            'longitude' => (double)$this->post('longitude'),
            'ketinggian_sungai' => (double)$this->post('depthofinlet'),
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

    function store_post(){
        date_default_timezone_set('Asia/Jakarta');
        $date_update = date('Y-m-d h:i:s', time());
        $id = $this->post('id');
        $data = array(
            'id_rumah_pompa' => $this->rumahpompa_model->generateid(),
            'nama_' => $this->post('name'),
            'jalan' => $this->post('address'),
            'no_telp_rumah_pompa' => $this->post('phone'),
            'threshold_tinggi_air' => (int)$this->post('threshold'),
            'latitude' => (double)$this->post('latitude'),
            'longitude' => (double)$this->post('longitude'),
            'created_at' => $date_update,
            'updated_at' => $date_update,
            'soft_delete' => false,
            'ketinggian_sungai' => (int)$this->post('depthofriver'),
            'alert' => false);

        $update = $this->rumahpompa_model->store($data);
        if($update){
            $respon["status"]= true;
            $respon["msg"]= "Store Berhasil";
        }else{
            $respon["status"]= false;
            $respon["msg"]= "Store Gagal";
        }
        $respon["status"]=true;
        $this->response($respon, 200);
    }

    function delete_post(){
        date_default_timezone_set('Asia/Jakarta');
        $date_update = date('Y-m-d h:i:s', time());
        $id = $this->post('id');

        $data = array(
            'soft_delete' => true,
            'updated_at' => $date_update);
        $update = $this->rumahpompa_model->edit($id, $data);
        if($update){
            $respon["status"]= true;
            $respon["msg"]= "Delete Berhasil";
        }else{
            $respon["status"]= false;
            $respon["msg"]= "Delete Gagal";
        }
        $this->response($respon, 200);
        
    }

    function getrumahpompabyName_post(){
        $name = $this->post('rumah_pompa');
        $rumah_pompa["result"] = $this->rumahpompa_model->getbyName($name);
        $this->response($rumah_pompa, 200);
    }

    function getUserRumahpompa_post(){
        $username = $this->post('username');
        $result = $this->user_rumahpompa_model->getbyUsername($username);
        $id_rumah_pompa = $result->id_rumah_pompa;

        $rumah_pompa["result"] = $this->rumahpompa_model->getbyId($id_rumah_pompa);
        $this->response($rumah_pompa, 200);
    }

    function getUserRumahpompa_get(){
        $result = $this->user_rumahpompa_model->getAll();
        $i = 0;
        foreach ($result as $key) {
            $rumah_pompa[$i]["username"] = $key->username;

            $id_rumah_pompa = $key->id_rumah_pompa;
            $rumah_pompa[$i]["rumahpompa"] = $this->rumahpompa_model->getbyId($id_rumah_pompa)->nama_;
            $i++;
            
        }
       $this->response($rumah_pompa, 200);
    }

    function getrumahpompabyStatus_post(){
        $status = $this->post('status');
        $rumah_pompa["result"] = $this->rumahpompa_model->getbyStatus($status);
        $this->response($rumah_pompa, 200);
    }

}