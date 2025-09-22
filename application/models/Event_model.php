<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class Event_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'events';
        $this->primary_key = 'id';
    }

    public function getEventList($cond){
        $this->db->select($this->table.".*, IF(events.organ_id=0, '全店舗', organs.organ_name) as organ_name");
        $this->db->from($this->table);
        $this->db->join('organs', 'events.organ_id = organs.organ_id', 'left');

        if(!empty($cond['company_id'])){
            $this->db->where('events.company_id', $cond['company_id']);
        }

        if(!empty($cond['organ_id'])){
            $this->db->where('events.organ_id', $cond['organ_id']);
        }

        if(!empty($cond['is_all_organ'])){
            $this->db->where('events.organ_id', 0);
        }else{
            $this->db->where('events.organ_id <> 0');
        }

        if(!empty($cond['from_time'])){
            $this->db->where("events.from_time>='".$cond['from_time']."'");
        }
        if(!empty($cond['to_time'])){
            $this->db->where("events.to_time<='".$cond['to_time']."'");
        }

        $query = $this->db->get();
        return $query->result_array();
    }

}