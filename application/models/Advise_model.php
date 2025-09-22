<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class Advise_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'advises';
        $this->primary_key = 'advise_id';
    }

    public function getListByCond($cond){

        $this->db->select($this->table.'.*, users.user_first_name, users.user_last_name, users.user_nick, staffs.staff_first_name, staffs.staff_last_name');
        $this->db->from($this->table);
        $this->db->join('users', 'users.user_id=advises.user_id', 'left');
        $this->db->join('staffs', 'staffs.staff_id=advises.teacher_id', 'left');

        if (!empty($cond['company_id'])){
            $this->db->where('users.company_id', $cond['company_id']);
        }
        if (!empty($cond['user_id'])){
            $this->db->where($this->table.'.user_id', $cond['user_id']);
        }

        $this->db->order_by($this->table.'.update_date', 'desc');

        $query = $this->db->get();

        return $query->result_array();
    }


}