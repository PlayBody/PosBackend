<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';
class Device_token_model extends Base_model
{

    public function __construct()
    {
        parent::__construct();
        $this->table = 'device_tokens';
        $this->primary_key = 'id';
    }

    public function getRecordByCondition($cond){

        $this->db->from($this->table);

        if (!empty($cond['staff_id'])){
            $this->db->where('staff_id', $cond['staff_id']);
        }

        if (!empty($cond['device_token'])){
            $this->db->where('device_token', $cond['device_token']);
        }

        $query = $this->db->get();

        return $query->row_array();

    }

    public function getListByCondition($cond){

        $this->db->from($this->table);

        if (!empty($cond['user_id'])){
            $this->db->where('user_id', $cond['user_id']);
        }

        if (!empty($cond['user_type'])){
            $this->db->where('user_type', $cond['user_type']);
        }

        $query = $this->db->get();

        return $query->result_array();

    }


}

  