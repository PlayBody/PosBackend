<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class Staff_attend_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'staff_attends';
        $this->primary_key = 'id';
    }

    public function getAttendRecord($staff_id){
        $this->db->from($this->table);
        $this->db->where('to_time is null');
        $query = $this->db->get();
        return $query->row_array();
    }

    public function getAttendData($staff_id, $organ_id, $from_time, $to_time){
        $this->db->from($this->table);
        $this->db->where('staff_id', $staff_id);
        $this->db->where('organ_id', $organ_id);
        $this->db->where("from_time < '$to_time'");
        $this->db->where("(to_time > '$from_time' or to_time is null)");
        $query = $this->db->get();
        return $query->result_array();
    }
}
