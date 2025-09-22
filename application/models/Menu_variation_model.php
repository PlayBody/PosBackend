<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class Menu_variation_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'menu_variations';
        $this->primary_key = 'variation_id';
    }

    public function getVariationList($cond){
        $this->db->select($this->table.".*, IF(staff_nick is NULL, CONCAT(staff_first_name,'　', staff_last_name), staff_nick) as staff_name");
        $this->db->from($this->table);
        $this->db->join('staffs', 'staffs.staff_id = menu_variations.variation_back_staff', 'left');
        $this->db->where($this->table.'.visible', '1');
        if (!empty($cond['menu_id'])){
            $this->db->where('menu_id', $cond['menu_id']);
        }

        $query = $this->db->get();

        return $query->result_array();

    }
}