<?php

/**
 * Created by PhpStorm.
 * User: Mardiana
 * Date: 2/24/2017
 * Time: 12:46 AM
 */
class Rumahpompa_model extends CI_Model
{
    private $table_name = "rumah_pompa";
    private $nama = "nama_";
    private $id_rumahpompa = "id_rumah_pompa";
    private $latitude = "latitude";
    private $longitude = "longitude";

    function getbyName($name){
        $this->db->where($this->nama, $name);
        $query = $this->db->get('rumah_pompa');
        return $query->row();
    }

    function store($data){

    }

    function edit($id, $data){
        $this->db->where($this->id_rumahpompa, $id);
        $update = $this->db->update($this->table_name, $data);
        return $update ? true : false;
    }

    function getAll(){
        $this->db->order_by('id_rumah_pompa', 'asc');
        $query = $this->db->get($this->table_name);
        return $query->result();
    }

    function getbyUser($user){
        $this->db->where('username', $user);
        $this->db->where('isactive', 'TRUE');
        $query = $this->db->get('user_rumahpompa');
        return $query->row();
        //return $this->db->last_query();
    }

    function getbyId($id){
        $this->db->where($this->id_rumahpompa, $id);
        $query = $this->db->get($this->table_name);
        return $query->row();
    }

    function getbyLocation($lat, $long){
        $this->db->where($this->latitude, $lat);
        $this->db->where($this->longitude, $long);
        $query = $this->db->get($this->table_name);
        return $query->row();

    }
}