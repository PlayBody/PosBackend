<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class Staff_point_setting_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'staff_point_settings';
        $this->primary_key = 'id';
    }

    public function getSettingData($cond){
        $this->db->from($this->table);

        if (!empty($cond['staff_id'])){
            $this->db->where('staff_id', $cond['staff_id']);
        }

        if (!empty($cond['setting_year'])){
            $this->db->where('setting_year', $cond['setting_year']);
        }
        if (!empty($cond['setting_month'])){
            $this->db->where('setting_month', $cond['setting_month']);
        }

        $query = $this->db->get();

        return $query->row_array();

    }

    public function getLastSetting($staff_id, $cur_date){
        $this->db->from($this->table);

        $this->db->where('staff_id', $staff_id);
        $this->db->where("CONCAT(setting_year, '-' , setting_month) < '$cur_date'");

        $this->db->order_by('setting_year', 'desc');
        $this->db->order_by('setting_month', 'desc');
        $query = $this->db->get();

        return $query->row_array();

    }


    public function getPAList($company_id, $menu_response, $cur_month){
        $this->db->from($this->table);
        $this->db->join('staffs', 'staffs.staff_id=staff_point_settings.staff_id', 'left');


        $this->db->where('company_id', $company_id);
        $this->db->where('menu_response', $menu_response);
        $this->db->where('staff_auth', 1);
        $this->db->where("CONCAT(setting_year, '-' , setting_month) = '$cur_month'");

        $query = $this->db->get();

        $results = $query->result_array();

        return join(',', array_column($results, 'staff_id'));

    }


}