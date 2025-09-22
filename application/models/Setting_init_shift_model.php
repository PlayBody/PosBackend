<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class Setting_init_Shift_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'setting_init_shifts';
        $this->primary_key = 'id';
    }

    public function getListByCond($cond, $setting_id=''){

        $this->db->from($this->table);

        if (!empty($cond['staff_id'])){
            $this->db->where('staff_id', $cond['staff_id']);
        }

        if (!empty($cond['organ_id'])){
            $this->db->where('organ_id', $cond['organ_id']);
        }

        if (!empty($cond['weekday'])){
            $this->db->where('weekday', $cond['weekday']);
        }

        if (!empty($cond['pattern'])){
            $this->db->where('pattern', $cond['pattern']);
        }

        if (!empty($cond['from_time'])){
            $this->db->where('from_time>=', $cond['from_time']);
        }

        if (!empty($cond['to_time'])){
            $this->db->where('to_time<=', $cond['to_time']);
        }

        if (!empty($cond['input_time'])){
            $this->db->where("from_time <'". $cond['input_time'] ."'");
            $this->db->where("to_time >'". $cond['input_time'] ."'");
        }
        if (!empty($cond['select_time'])){
            $this->db->where("from_time <='". $cond['select_time'] ."'");
            $this->db->where("to_time >'". $cond['select_time'] ."'");
        }

        if (!empty($cond['in_from_time']) && !empty($cond['in_to_time'])){
            $this->db->where("((to_time >'". $cond['in_from_time'] ."' and from_time <'". $cond['in_to_time'] ."') || (from_time ='". $cond['in_from_time'] ."' and to_time ='". $cond['in_to_time'] ."'))" );
        }

        if (!empty($cond['no_setting'])){
            $this->db->where("id <>'". $cond['no_setting'] ."'");
        }

        if (!empty($setting_id)){
            $this->db->where("id <> '". $setting_id ."'");
        }
        $query = $this->db->get();

        return $query->result_array();
    }

}
