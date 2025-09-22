<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class Organ_menu_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'organ_menus';
        $this->primary_key = 'id';
    }

    public function getListByCond($cond){
        $this->db->select('*');
        $this->db->from($this->table);

        if (!empty($cond['organ_id'])){
            $this->db->where('organ_id', $cond['organ_id']);
        }
        if (!empty($cond['order_id'])){
            $this->db->where('order_id', $cond['order_id']);
        }

        if (!empty($cond['menu_id'])){
            $this->db->where('menu_id', $cond['menu_id']);
        }

        $query = $this->db->get();
        return $query->result_array();
    }

}
