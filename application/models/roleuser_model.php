<?php

/**
 * Created by PhpStorm.
 * User: Mardiana
 * Date: 2/24/2017
 * Time: 12:27 AM
 */
class Roleuser_model extends CI_Model
{
    private $table_name = 'roleuser';
    function store($data_role){
        $query = $this->db->insert($this->table_name, $data_role);
        return $query ? true : false;
    }

    function getbyUsername($username){
        $this->db->where('username', $username);
        $this->db->where('soft_delete', FALSE);
        $this->db->where('isactive', TRUE);
        $query = $this->db->get($this->table_name);
        return $query->row();
    }

    function edit($username, $data){
        $this->db->where('username', $username);
        $this->db->where('isactive', TRUE);

        $query = $this->db->update($this->table_name, $data);

        return $query ? true : false;
    }
}