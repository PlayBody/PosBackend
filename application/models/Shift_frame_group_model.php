<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class Shift_frame_group_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'shift_frame_groups';
        $this->primary_key = 'id';
    }

    private function getWhere($cond){
        if (!empty($cond['shift_frame_id'])){
            $this->db->where("shift_frame_id ", $cond['shift_frame_id']);
        }

        if (!empty($cond['shift_frame_ids'])){
            $this->db->where("shift_frame_id In (". implode(',' , $cond['shift_frame_ids']) .")");
        }

        if (!empty($cond['not_group_ids'])){
            $this->db->where("group_id Not In (". implode(',' , $cond['not_group_ids']) .")");
        }

    }

    public function removeGroup($cond){
        $this->getWhere($cond);
        $this->db->delete($this->table);
    }

    public function getGroupLists($cond){
        $this->db->from($this->table);
        $this->getWhere($cond);
		$query = $this->db->get();
        $result = $query->result_array();
	    return $result;
    }

}
