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
    private $alert = "alert";

    function getbyName($name){
        $this->db->where($this->nama, $name);
        $query = $this->db->get('rumah_pompa');
        return $query->row();
    }

    function generateid(){
        $query = "SELECT nextval('id_rumahpompa_seq')";
        $id_data = $this->db->query($query);
        return $id_data->row()->nextval;
    }

    function store($data){
        $query = $this->db->insert($this->table_name, $data);
        return $query ? true : false;
    }

    function edit($id, $data){
        $this->db->where($this->id_rumahpompa, $id);
        $update = $this->db->update($this->table_name, $data);
        return $update ? true : false;
    }

    function getAll(){
        $this->db->where('soft_delete', false);
        $this->db->order_by('id_rumah_pompa', 'asc');
        $query = $this->db->get($this->table_name);
        return $query->result();
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

    function getbyStatus($status){
        $this->db->where('soft_delete', false);
        if ($status=="Berpotensi Banjir") {
            $this->db->where($this->alert, TRUE);
        }
        else{
             $this->db->where($this->alert, FALSE);
        }
        $query = $this->db->get($this->table_name);
        return $query->result();
    }

    
}