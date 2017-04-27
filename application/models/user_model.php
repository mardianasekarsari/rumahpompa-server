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

        $this->load->model('roleuser_model');
        $this->load->model('user_rumahpompa_model');
        $this->load->model('rumahpompa_model');
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
        $query = $this->db->insert($this->table_name, $data_user);
        $query2 = $this->roleuser_model->store($data_role);
        $query3 = $this->user_rumahpompa_model->store($data_user_rumahpompa);
        return $query && $query2 && $query3 ? true : false;
    }

    function edit($data_user, $data_role, $data_user_rumahpompa){
        $query_userrumahpompa = true;
        $query_userrole = true;

        $oldUsername = $data_user["username"];
        $user = $this->getbyUsername($oldUsername);
        $user_rumahpompa = $this->user_rumahpompa_model->getbyUsername($oldUsername)->id_rumah_pompa;
        $user_role = $this->roleuser_model->getbyUsername($oldUsername)->id_role;

        $isactive = array(
            'isactive' => FALSE,
            'updated_at' => $data_user["updated_at"]
        );

        $this->db->where('username', $oldUsername);
        $insert_user = $this->db->update($this->table_name, $data_user);

        if ($user_rumahpompa != $data_user_rumahpompa["id_rumah_pompa"]) {
            $update_userrumahpompa = $this->user_rumahpompa_model->edit($oldUsername, $isactive);
            $insert_userrumahpompa = $this->user_rumahpompa_model->store($data_user_rumahpompa);
            if ($update_userrumahpompa && $insert_userrumahpompa) {
                $query_userrumahpompa = true;
            }
            else{
                $query_userrumahpompa = false;
            }
        }
        if ($user_role != $data_role["id_role"]){
            $update_roleuser = $this->roleuser_model->edit($oldUsername, $isactive);
            $insert_roleuser = $this->roleuser_model->store($data_role);

            if ($update_roleuser && $insert_roleuser) {
                $query_userrole = true;
            }
            else{
                $query_userrole = false;
            }
        }
        //print_r($query_userrole);

        if($insert_user && $query_userrole && $query_userrumahpompa){
            return true;
        }
        else{
            return false;
        }
    }

    function getAllUser(){
        $this->db->where('soft_delete', FALSE);
        $this->db->order_by('nama_user', 'asc');
        return $this->db->get('user_')->result();
    }

	function getbyUsername($username){
		$this->db->where($this->username, $username);
        $this->db->where('soft_delete', FALSE);
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
