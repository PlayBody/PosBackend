<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class Shift_frame_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'shift_frames';
        $this->primary_key = 'id';
    }

    private function getWhere($cond){
        if (!empty($cond['organ_id'])){
            $this->db->where('organ_id', $cond['organ_id']);
        }

        if (!empty($cond['input_from_time']) && !empty($cond['input_to_time'])){
            $this->db->where("from_time <'". $cond['input_to_time'] ."'");
            $this->db->where("to_time >'". $cond['input_from_time'] ."'");
        }
        
        if (!empty($cond['from_time'])){
            $this->db->where("from_time >='". $cond['from_time'] ."'");
        }

        if (!empty($cond['to_time'])){
            $this->db->where("to_time <='". $cond['to_time'] ."'");
        }
        
        if (!empty($cond['from_date'])){
            $this->db->where("from_time >='". $cond['from_date'] ."'");
        }

        if (!empty($cond['to_date'])){
            $this->db->where("to_time <'". $cond['to_date'] ."'");
        }

        if (!empty($cond['selected_date'])){
            $this->db->where("from_time like '". $cond['selected_date'] ."%'");
        }
        
    }

    public function getListData($cond){
        $this->db->from($this->table);

        $this->getWhere($cond);
        
        $this->db->order_by("from_time");
        $query = $this->db->get();

        return $query->result_array();

    }

    public function getListByCond($cond, $setting_id=''){

        $this->db->from($this->table);

        if (!empty($cond['organ_id'])){
            $this->db->where('organ_id', $cond['organ_id']);
        }

        if (!empty($cond['input_time'])){
            $this->db->where("from_time <'". $cond['input_time'] ."'");
            $this->db->where("to_time >'". $cond['input_time'] ."'");
        }

        if (!empty($cond['select_time'])){
            $this->db->where("from_time <='". $cond['select_time'] ."'");
            $this->db->where("to_time >'". $cond['select_time'] ."'");
        }

        if (!empty($cond['from_time'])){
            $this->db->where("from_time >='". $cond['from_time'] ."'");
        }

        if (!empty($cond['to_time'])){
            $this->db->where("to_time <='". $cond['to_time'] ."'");
        }

        if (!empty($cond['inner_from_time'])){
            $this->db->where("from_time <='". $cond['inner_from_time'] ."'");
        }

        if (!empty($cond['inner_to_time'])){
            $this->db->where("to_time >='". $cond['inner_to_time'] ."'");
        }

        if (!empty($cond['in_from_time']) && !empty($cond['in_to_time'])){
            $this->db->where("((to_time >'". $cond['in_from_time'] ."' and from_time <'". $cond['in_to_time'] ."') || (from_time ='". $cond['in_from_time'] ."' and to_time ='". $cond['in_to_time'] ."'))" );
        }

        if (!empty($cond['submit_from_time'])){
            $this->db->where("from_time <='". $cond['submit_from_time'] ."'");
        }

        if (!empty($cond['submit_to_time'])){
            $this->db->where("to_time >='". $cond['submit_to_time'] ."'");
        }

        if (!empty($cond['select_date'])){
            $this->db->where("from_time >='". $cond['select_date'] ." 00:00:00'");
            $this->db->where("to_time <='". $cond['select_date'] ." 23:59:59'");
        }

        if (!empty($cond['date_month'])){
            $this->db->where("from_time like '". $cond['date_month'] ."-%'");
        }

        if (!empty($setting_id)){
            $this->db->where("id <> '". $setting_id ."'");
        }

        $this->db->order_by("from_time");
        $query = $this->db->get();

        return $query->result_array();
    }


    public function getPositionCountByPeriod($organ_id, $from_time, $to_time){
        $this->db->select('min(count) as position_count');
        $this->db->from($this->table);
        $this->db->where('organ_id', $organ_id);
        $this->db->where("from_time <= '$to_time'");
        $this->db->where("to_time > '$from_time'");
        $this->db->where("count is not null");

        $this->db->group_by('count');

        $query = $this->db->get();
        $result = $query->row_array();
        if (empty($result)) return 0;
        return $result['position_count'];
    }

    public function getEnableDays($cond){
        $this->db->from($this->table);

        if (!empty($cond['organ_id'])){
            $this->db->where('organ_id', $cond['organ_id']);
        }

        if (!empty($cond['now_start_time'])){
            $this->db->where("to_time >'". $cond['now_start_time'] ."'");
        }
        $query = $this->db->get();
        $result = $query->result_array();

        return $result;
    }

    public function removeShiftFrame($cond){
        $this->getWhere($cond);
        $this->db->delete($this->table);
    }
    
}
