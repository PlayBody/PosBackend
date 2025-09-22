<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class Organ_special_shift_time_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'organ_special_shift_times';
        $this->primary_key = 'id';
    }

    public function getTimes($organ_id, $from_time, $to_time){

        $this->db->from($this->table);

        $this->db->where('organ_id', $organ_id);
        $this->db->where("from_time >= '".$from_time."'");
        $this->db->where("to_time < '".$to_time."'");
        $this->db->order_by('from_time');

        $query = $this->db->get();

        return $query->result_array();
    }

    public function getTimeList($organ_id, $from_time='', $to_time=''){

        $this->db->from($this->table);

        $this->db->where('organ_id', $organ_id);
        if(!empty($from_time)) $this->db->where("from_time >= '".$from_time."'");
        if(!empty($to_time)) $this->db->where("to_time <= '".$to_time."'");
        $this->db->order_by('from_time');

        $query = $this->db->get();

        return $query->result_array();
    }

    public function getFindShiftTimeOfSpecialBusiness($special_business_id){

        $this->db->from($this->table);

        $this->db->where('special_business_id', $special_business_id);

        $query = $this->db->get();

        return $query->row_array();
    }

}
