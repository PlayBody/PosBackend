<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class Group_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'groups';
        $this->primary_key = 'group_id';
    }

    public function getListByCond($cond){
        $this->db->select('groups.*, count(group_users.user_id) as user_cnt');
        $this->db->from($this->table);
        $this->db->join('group_users', 'groups.group_id = group_users.group_id', 'left');

        if (!empty($cond['company_id'])){
            $this->db->where('company_id', $cond['company_id']);
        }

        $this->db->group_by('groups.group_id');

        $this->db->order_by('groups.group_name');

        $query = $this->db->get();
        return $query->result_array();

    }
}