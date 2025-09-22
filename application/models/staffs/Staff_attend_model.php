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
        $this->db->where('staff_id', $staff_id);
        $this->db->where('to_time is null');
        $query = $this->db->get();
        return $query->row_array();
    }

    public function currentAttendData($organ_id, $cur_date) {
        $curDateTime = new DateTime($cur_date);
        $endDay = $curDateTime->format("Y-m-d 23:59:59");
        $startDay = $curDateTime->format("Y-m-d 00:00:00");


        $this->db->from($this->table);
        $this->db->where('organ_id', $organ_id);
        $this->db->where("from_time < '$cur_date'");
        $this->db->where("from_time > '$startDay'");
        $this->db->where("from_time < '$endDay'");
        $this->db->where('to_time is null');
        $query = $this->db->get();
        return $query->result_array();
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
