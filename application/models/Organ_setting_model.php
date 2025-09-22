<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class Organ_setting_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'organ_settings';
        $this->primary_key = 'setting_id';
    }

    public function getOrganSetting($organ_id){
        $this->db->select('*');
        $this->db->from($this->table);

        $this->db->where('organ_id', $organ_id);

        $query = $this->db->get();

        return $query->row_array();
    }
}