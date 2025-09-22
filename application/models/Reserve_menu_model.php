<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class Reserve_menu_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'reserve_menus';
        $this->primary_key = 'id';
    }

    public function getReserveMenuList($reserve_id){
        $this->db->select('menus.*');
        $this->db->from($this->table);
        $this->db->join('menus', 'menus.menu_id=reserve_menus.menu_id', 'left');

        $this->db->where('reserve_id', $reserve_id);

        $this->db->order_by('menus.menu_price', 'desc');

        $query = $this->db->get();

        return $query->result_array();
    }


}