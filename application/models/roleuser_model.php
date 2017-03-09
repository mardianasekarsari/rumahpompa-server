<?php

/**
 * Created by PhpStorm.
 * User: Mardiana
 * Date: 2/24/2017
 * Time: 12:27 AM
 */
class Roleuser_model extends CI_Model
{
    function store($data_role){
        $query = $this->db->insert('roleuser', $data_role);
        return $query ? true : false;
    }
}