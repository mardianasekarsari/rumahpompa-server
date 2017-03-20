<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * user_model
 *
 *
 * 
 * <===== LOG =====>
 *
*/

class User_model extends CI_Model
{
    private $table_name = "user_";
    private $username = "username";
    private $password = "password";

	function __construct()
  	{
    	parent::__construct(); // construct the Model class
  	}

  	function check_existing_user($username){
  		$this->db->where($this->username, $username);
		$this->db->from($this->table_name);
        return $this->db->count_all_results();
        //return $this->db->last_query();
        //return $this->db->get();
  	}

  	function check_login($username, $password)
	{
		$this->db->where($this->username, $username);
		$this->db->where($this->password, $password);
        $this->db->from($this->table_name);
        return $this->db->count_all_results();
		//return $this->db->get('user_');
        //return $query->row();
        //return $this->db->last_query();
	}

    function store($data_user, $data_role, $data_user_rumahpompa){
        $this->load->model('roleuser_model');
        $this->load->model('user_rumahpompa_model');
        $query = $this->db->insert($this->table_name, $data_user);
        $query2 = $this->roleuser_model->store($data_role);
        $query3 = $this->user_rumahpompa_model->store($data_user_rumahpompa);
        return $query && $query2 && $query3 ? true : false;
    }

    function getAllUser(){
        return $this->db->get('role')->result();
    }

	function getbyUsername($username){
		$this->db->where($this->username, $username);
        $query = $this->db->get($this->table_name);
		return $query->row();
	}

	function get_role_user($username){
        $this->db->select('id_role');
        $this->db->where($this->username, $username);
        $this->db->from('roleuser');
        $role_id = $this->db->get_compiled_select();


        $this->db->where("id_role in ($role_id)", NULL, FALSE);
        $query = $this->db->get('role');
        return $query->row();
    }

    function editUser($username, $data){
        $this->db->where($this->username, $username);
        $update = $this->db->update($this->table_name, $data);
        return $update ? true : false;
    }
	// =========================================================================
}

/* End of file user_model.php */
/* Location: ./application/models/user_model.php */
