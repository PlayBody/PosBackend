<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class Shift_init_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'shift_init';
        $this->primary_key = 'id';
    }

    public function getRecordByCond($cond){

        $this->db->from($this->table);

        if (!empty($cond['staff_id'])){
            $this->db->where('staff_id', $cond['staff_id']);
        }

        if (!empty($cond['organ_id'])){
            $this->db->where('organ_id', $cond['organ_id']);
        }

        if (!empty($cond['organ_id'])){
            $this->db->where('organ_id', $cond['organ_id']);
        }

        if (!empty($cond['week_num'])){
            $this->db->where('week_num', $cond['week_num']);
        }

        if (!empty($cond['selected_time'])){
            $this->db->where("from_time <= '" . $cond['selected_time'] . "'");
            $this->db->where("to_time > '" . $cond['selected_time'] . "'");
        }

        $query = $this->db->get();

        return $query->row_array();
    }
}
