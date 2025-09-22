<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class Organ_shift_time_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'organ_shift_times';
        $this->primary_key = 'id';
    }

    public function getListByCond($cond){
        $this->db->from($this->table);

        if (!empty($cond['organ_id'])){
            $this->db->where('organ_id', $cond['organ_id']);
        }
        if (!empty($cond['weekday'])){
            $this->db->where('weekday', $cond['weekday']);
        }

        $query = $this->db->get();
        return $query->result_array();

    }


    public function getOrganMinMaxShiftTime($organ_id, $from_time, $to_time){
        if (!empty($from_time) && !empty($to_time)) {
            $sql = "select min(from_time) as min_time, max(to_time) as max_time from
            (select from_time, to_time from organ_shift_times where organ_id= " . $organ_id . "
                union 
                select date_format(from_time, '%H:%i') as from_time, date_format(to_time, '%H:%i') as to_time from organ_special_shift_times 
                    where organ_id=" . $organ_id . " 
                        and from_time>='" . $from_time . "' 
                        and to_time <='" . $to_time . "') tmp";
        }else{
            $sql = "select min(from_time) as min_time, max(to_time) as max_time from organ_shift_times where organ_id= " . $organ_id ;
        }
        $query = $this->db->query($sql);

        return $query->row_array();
    }


    public function getMinMaxTimeByCond($cond){
        $this->db->select('min(from_time) as from_time, max(to_time) as to_time');
        $this->db->from($this->table);

        if (!empty($cond['organ_id'])){
            $this->db->where('organ_id', $cond['organ_id']);
        }
        if (!empty($cond['weekday'])){
            $this->db->where('weekday', $cond['weekday']);
        }

        $query = $this->db->get();
        return $query->row_array();

    }
}
