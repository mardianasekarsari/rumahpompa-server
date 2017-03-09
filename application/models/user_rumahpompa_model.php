<?php

/**
 * Created by PhpStorm.
 * User: Mardiana
 * Date: 3/2/2017
 * Time: 11:27 PM
 */
class User_rumahpompa_model extends CI_Model
{
    function store($data_user_rumahpompa){
        $query = $this->db->insert('user_rumahpompa', $data_user_rumahpompa);
        return $query ? true : false;
    }
}