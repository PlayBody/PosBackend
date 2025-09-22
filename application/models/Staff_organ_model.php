<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class Staff_organ_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'staff_organs';
        $this->primary_key = 'id';
    }

    private function getWhere($cond){
        if (!empty($cond['staff_id'])){
            $this->db->where('staff_organs.staff_id', $cond['staff_id']);
        }
        if (!empty($cond['company_id'])){
            $this->db->where('organs.company_id' , $cond['company_id']);
        }
        if (!empty($cond['is_no_reserve_type']) || $cond['is_no_reserve_type'] == '0'){
            $this->db->where('organs.is_no_reserve_type' , $cond['is_no_reserve_type']);
        }

    }
    public function getListData($cond){
        $this->db->select('organs.*');
        $this->db->from($this->table);
        $this->db->join('organs', 'organs.organ_id = staff_organs.organ_id', 'left');
        $this->getWhere($cond);
        $this->db->order_by('organs.organ_id');

        $query = $this->db->get();
        return $query->result_array();
    }

    public function getStaffOrganList($cond){
        $this->db->from($this->table);

        if (!empty($cond['staff_id']))
            $this->db->where('staff_id', $cond['staff_id']);
        $query = $this->db->get();


        return $query->result_array();
    }

    public function getOrgansByStaff($staff_id){
        $this->db->select('organs.*');
        $this->db->from($this->table);

        $this->db->join('organs', 'organs.organ_id = staff_organs.organ_id', 'inner');

        $this->db->where('staff_id', $staff_id);

//        $this->db->where('organs.visible', '1');

        $query = $this->db->get();

        return $query->result_array();
    }

    public function getStaffsByOrgan($organ_id, $auth, $isGetGuest = true, $isStaff = true){
        $this->db->select("staffs.*, ".$this->table.".auth, IF(staff_nick is NULL, CONCAT(staff_first_name,' ', staff_last_name), staff_nick) as sort_name");
        $this->db->from($this->table);

        $this->db->join('staffs', 'staffs.staff_id = staff_organs.staff_id', 'left');
        $this->db->where('staffs.visible', '1');
        $this->db->where('organ_id', $organ_id);
        $this->db->where('staff_auth<'.$auth);
        if (!$isGetGuest)
            $this->db->where('staff_auth>0');
        if (!$isStaff)
            $this->db->where('staff_auth>1');

        $this->db->order_by('staffs.sort_no', 'asc');
        $query = $this->db->get();

        return $query->result_array();
    }

    public function getAuthRecord($staff_id, $organ_id){
        $this->db->from($this->table);
        $this->db->where('staff_id', $staff_id);
        $this->db->where('organ_id', $organ_id);
        $query = $this->db->get();

        return $query->row_array();
    }

    public function getStaffs($cond){
        $this->db->select('staffs.*');
        $this->db->from($this->table);
        $this->db->join('staffs', 'staffs.staff_id = staff_organs.staff_id', 'left');

        if (!empty($cond['organ_id'])){
            $this->db->where('organ_id', $cond['organ_id']);
        }
        if (!empty($cond['staff_id'])){
            $this->db->where('staffs.staff_id', $cond['staff_id']);
        }
        if (!empty($cond['staff_sex'])){
            $this->db->where('staff_sex', $cond['staff_sex']);
        }

        $this->db->where('staff_auth > 0');
        $this->db->where('staff_auth < ' . STAFF_AUTH_ADMIN);

        $query = $this->db->get();

        return $query->result_array();
    }

	public function getStaffsByPermission($organ_id, $min_permission)
	{
		$this->db->select('staffs.*');
		$this->db->from($this->table);
		$this->db->join('staffs', 'staff_organs.staff_id = staffs.staff_id');
		$this->db->where('staff_organs.organ_id', $organ_id);
		$this->db->where('staffs.staff_auth >=' . $min_permission);

		$query = $this->db->get();
		
		return $query->result_array();

		
	}

}
