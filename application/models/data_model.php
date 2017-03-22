<?php

/**
 * Created by PhpStorm.
 * User: Mardiana
 * Date: 3/20/2017
 * Time: 11:20 PM
 */
class Data_model extends CI_Model
{
    private $table_name = "data";
    private $idrumahpompa = "id_rumah_pompa";

    function check_existing_data($id){
        $this->db->where($this->idrumahpompa, $id);
        $this->db->from($this->table_name);
        return $this->db->count_all_results();
    }

    function generateid(){
        $query = "SELECT nextval('id_data_seq')";
        $id_data = $this->db->query($query);
        return $id_data->row()->nextval;
    }

    function store($data){
        $query = $this->db->insert($this->table_name, $data);
        return $query ? true : false;
    }

    function edit($id, $data){
        $this->db->where($this->idrumahpompa, $id);
        $update = $this->db->update($this->table_name, $data);
        return $update ? true : false;
    }

    function getbyIdrumahpompa($id){
        $this->db->where($this->idrumahpompa, $id);
        $query = $this->db->get($this->table_name);
        return $query->row();
    }
}