<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class Menu_variation_back_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'menu_variation_backs';
        $this->primary_key = 'id';
    }

    public function getVariationBacks($variation_id){
        $this->db->select($this->table.".*, IF(staff_nick is NULL, CONCAT(staff_first_name,'　', staff_last_name), staff_nick) as staff_name");
        $this->db->from($this->table);
        $this->db->join('staffs', $this->table.'.staff_id=staffs.staff_id');

        $this->db->where('variation_id', $variation_id);

        $query = $this->db->get();

        return $query->result_array();

    }

}