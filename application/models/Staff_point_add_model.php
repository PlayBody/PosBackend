<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class Staff_point_add_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'staff_point_adds';
        $this->primary_key = 'id';
    }

    public function getPointList($cond){
        $this->db->from($this->table);

        if (!empty($cond['point_setting_id'])){
            $this->db->where('point_setting_id', $cond['point_setting_id']);
        }

        if (!empty($cond['organ_id'])){
            $this->db->where('organ_id', $cond['organ_id']);
        }
//
//        if (!empty($cond['type'])){
//            $this->db->where('type > 0');
//        }

        $query = $this->db->get();

        return $query->result_array();

    }


    public function getPointsSum($cond){
        $this->db->select("sum(value) as sum_point");
        $this->db->from($this->table);

        if (!empty($cond['point_setting_id'])){
            $this->db->where('point_setting_id', $cond['point_setting_id']);
        }

        if (!empty($cond['organ_id'])){
            $this->db->where('organ_id', $cond['organ_id']);
        }
//
//        if (!empty($cond['type'])){
//            $this->db->where('type > 0');
//        }

        $this->db->where('status', '2');

        $results = $this->db->get()->row_array();

        return empty($results['sum_point']) ? 0 : $results['sum_point'];
    }

    public function getStaffAddPoints($cond){
        $this->db->select($this->table.".*, IF(staff_nick is NULL, CONCAT(staff_first_name,' ', staff_last_name), staff_nick) as staff_name");
        $this->db->from($this->table);
        $this->db->join('staffs', 'staff_point_adds.staff_id=staffs.staff_id', 'left');

        if(!empty($cond['staff_id'])){
            $this->db->where($this->table.'.staff_id', $cond['staff_id']);
        }
        if(!empty($cond['point_date'])){
            $this->db->where('point_date', $cond['point_date']);
        }
        if(!empty($cond['point_month'])){
            $this->db->where("point_date like '" . $cond['point_month'] . "-%'");
        }
        if(!empty($cond['organ_id'])){
            $this->db->where('organ_id', $cond['organ_id']);
        }
        if(!empty($cond['status'])){
            $this->db->where('status', $cond['status']);
        }
        if(!empty($cond['point_setting_id'])){
            $this->db->where($this->table.'.point_setting_id', $cond['point_setting_id']);
        }
        if(!empty($cond['from_date'])){
            $this->db->where("point_date >='" . $cond['from_date'] . "'");
        }
        if(!empty($cond['to_date'])){
            $this->db->where("point_date <='" . $cond['to_date'] . "'");
        }


        $this->db->order_by('point_date');
        $this->db->order_by('staff_name');
        $query = $this->db->get();
        return $query->result_array();
    }

}
