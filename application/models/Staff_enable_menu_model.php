<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class Staff_enable_menu_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'staff_enable_menus';
        $this->primary_key = 'staff_enable_menu_id';
    }

    public function getMenuList($staff_id, $organ_id){
        $this->db->select($this->table.'.*');

        $this->db->from($this->table);
        $this->db->join('menus', 'staff_enable_menus.menu_id=menus.menu_id', 'left');

        $this->db->where($this->table.'.staff_id', $staff_id);
        $this->db->where('menus.organ_id', $organ_id);

        $query = $this->db->get();

        return $query->result_array();
    }

    public function getEnableMenu($staff_id, $menu_id){
        $this->db->from($this->table);

        $this->db->where('staff_id', $staff_id);
        $this->db->where('menu_id', $menu_id);

        $query = $this->db->get();

        return $query->row_array();
    }
}