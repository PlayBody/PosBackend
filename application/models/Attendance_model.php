<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class Attendance_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'attendances';
        $this->primary_key = 'attendance_id';
    }

    function getLastAttendance($staff_id){
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where('staff_id', $staff_id);
        $this->db->where('visible','1');
        $this->db->order_by('attendance_time', 'desc');
        $query = $this->db->get();
        return $query->row_array();
    }

    function getAttendanceList($staff_id, $month){
        $this->db->select("*, DATE_FORMAT(attendance_time, '%Y-%m-%d') as attendance_date");
        $this->db->from($this->table);
        $this->db->where('staff_id', $staff_id);
        $this->db->where('visible','1');
        $this->db->where("attendance_time like '" . $month . "-%'");

        $this->db->order_by('attendance_time', 'asc');

        $query = $this->db->get();

        return $query->result_array();
    }


}