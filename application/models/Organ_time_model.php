<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class Organ_time_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'organ_times';
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
        if (!empty($cond['select_time'])){
            $this->db->where("from_time<= '". $cond['select_time'] . "'");
            $this->db->where("to_time>'". $cond['select_time'] . "'");
        }

        $this->db->order_by('weekday');
        $this->db->order_by('from_time');

        $query = $this->db->get();
        return $query->result_array();

    }

    public function isActiveTime($organ_id, $weekday, $from_time){
        $this->db->from($this->table);
        $this->db->where('organ_id', $organ_id);
        $this->db->where('weekday', $weekday);
        $this->db->where("from_time<='".$from_time."' and to_time>'".$from_time."'");
        $query = $this->db->get();

        return !empty($query->row_array());
    }

    public function isPeriodActiveTime($organ_id, $weekday, $from_time, $to_time){
        $this->db->from($this->table);
        $this->db->where('organ_id', $organ_id);
        $this->db->where('weekday', $weekday);
        $this->db->where("from_time<='".$from_time."' and to_time>'".$to_time."'");
        $query = $this->db->get();

        return !empty($query->row_array());
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

    public function isInOpenTime($organ_id, $week, $from_time, $to_time){
        $this->db->from($this->table);
        $this->db->where('organ_id', $organ_id);
        $this->db->where('weekday', $week);
        $this->db->where("from_time <= '".$from_time."'");
        $this->db->where("to_time >= '".$to_time."'");

        $query = $this->db->get();

        return !empty($query->row_array());
    }
}
