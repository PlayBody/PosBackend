<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class Organ_special_time_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'organ_special_times';
        $this->primary_key = 'organ_special_time_id';
    }

    public function getListByCond($cond){

        $this->db->from($this->table);

        if(!empty($cond['organ_id'])){
            $this->db->where('organ_id', $cond['organ_id']);
        }
        if(!empty($cond['select_date'])){
            $this->db->where("from_time like '" . $cond['select_date'] . " %'");
        }
        if(!empty($cond['select_time'])){
            $this->db->where("from_time<= '". $cond['select_time'] . "'");
            $this->db->where("to_time>'". $cond['select_time'] . "'");
        }
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

    public function isInOpenTime($organ_id, $from_time, $to_time){
        $this->db->from($this->table);
        $this->db->where('organ_id', $organ_id);
        $this->db->where("from_time <= '".$from_time."'");
        $this->db->where("to_time >= '".$to_time."'");

        $query = $this->db->get();

        return !empty($query->row_array());
    }
}
