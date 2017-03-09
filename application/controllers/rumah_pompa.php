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
        $id = $this->get('id');
        $this->load->model('rumahpompa_model');
        if ($id == ''){
            $rumah_pompa["result"] = $this->rumahpompa_model->getAll();
        }
        else{
            $rumah_pompa["result"] = $this->rumahpompa_model->getbyId($id);
        }

        $this->response($rumah_pompa, 200);
    }
}