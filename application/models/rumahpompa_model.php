<?php

/**
 * Created by PhpStorm.
 * User: Mardiana
 * Date: 2/24/2017
 * Time: 12:46 AM
 */
class Rumahpompa_model extends CI_Model
{
    function getbyName($name){
        $this->db->where('nama_', $name);
        $query = $this->db->get('rumah_pompa');
        return $query->row();
    }

    function store($data){

    }

    function getAll(){
        return $this->db->get('rumah_pompa')->result();
    }

    function getbyUser($user){
        $this->db->where('username', $user);
        $this->db->where('isactive', 'TRUE');
        $query = $this->db->get('user_rumahpompa');
        return $query->row();
        //return $this->db->last_query();
    }

    function getbyId($id){
        $this->db->where('id_rumah_pompa', $id);
        $query = $this->db->get('rumah_pompa');
        return $query->row();
    }
}