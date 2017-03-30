<?php

/**
 * Created by PhpStorm.
 * User: Mardiana
 * Date: 3/2/2017
 * Time: 11:27 PM
 */
class User_rumahpompa_model extends CI_Model
{
    private $table_name = 'user_rumahpompa';
    function store($data_user_rumahpompa){
        $query = $this->db->insert($this->table_name, $data_user_rumahpompa);
        return $query ? true : false;
    }

    function getbyRumahpompa($idrumahpompa){
        $this->db->where('id_rumah_pompa', $idrumahpompa);
        $this->db->where('isactive', TRUE);
        $this->db->where('soft_delete', FALSE);
        $query = $this->db->get($this->table_name);
        return $query->result();
    }
}