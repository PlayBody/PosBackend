<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class Group_user_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'group_users';
        $this->primary_key = 'group_id';
    }

    private function getWhere($cond){
        if (!empty($cond['group_id'])){
            $this->db->where('group_users.group_id', $cond['group_id']);
        }

        $this->db->where('users.user_id is not null');

    }

    public function getGroupsByUser($user_id){
        $this->db->select('groups.*');
        $this->db->from($this->table);
        $this->db->join('groups', 'groups.group_id=group_users.group_id', 'left');
        $this->db->where($this->table.'.user_id', $user_id);

        $query = $this->db->get();
        return $query->result_array();
    }

    public function getUserGroup($user_id, $group_id){
        $this->db->from($this->table);
        $this->db->where('user_id', $user_id);
        $this->db->where('group_id', $group_id);

        $query = $this->db->get();
        return $query->row_array();
    }

    public function getUsersByGroupGroup($group_id){
        $this->db->from($this->table);
        $this->db->where('group_id', $group_id);

        $query = $this->db->get();
        return $query->result_array();
    }

    public function getUsers($cond){
        $this->db->select('users.*, groups.group_id, groups.group_name');
        $this->db->from($this->table);
        $this->db->join('users', 'group_users.user_id = users.user_id', 'left');
        $this->db->join('groups', 'group_users.group_id = groups.group_id', 'left');

        $this->getWhere($cond);
        
        $query = $this->db->get();

        return $query->result_array();
    }
}