<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class Staff_setting_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'staff_settings';
        $this->primary_key = 'setting_id';
    }

    public function getStaffSetting($staff_id){
        $this->db->select('*');
        $this->db->from($this->table);

        $this->db->where('staff_id', $staff_id);

        $query = $this->db->get();

        return $query->row_array();
    }
}