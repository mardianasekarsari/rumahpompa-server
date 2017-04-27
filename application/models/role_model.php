<?php

/**
 * Created by PhpStorm.
 * User: Mardiana
 * Date: 2/24/2017
 * Time: 12:31 AM
 */
class Role_model extends CI_Model
{
    function getbyName($role){
        $this->db->where('nama_role', strtoupper($role));
        $query = $this->db->get('role');
        return $query->row();
    }

    function getbyId($id){
        $this->db->where('id_role', $id);
        $query = $this->db->get('role');
        return $query->row();
    }

    function getAll(){
        return $this->db->get('role')->result();
    }
}