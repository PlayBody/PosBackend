<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class Table_menu_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'table_menus';
        $this->primary_key = 'table_menu_id';
    }

    function getMenuListByCond($cond)
    {
        $this->db->select('*')->from($this->table);
        
        $this->db->where('visible','1');
        if (!empty($cond['table_id'])){
            $this->db->where('table_id', $cond['table_id']);
        }

        $query = $this->db->get();

        return $query->result_array();
    }


    function getMenuAmountByCond($cond)
    {
        $this->db->select('sum(menu_price*quantity) as amount')->from($this->table);

        $this->db->where('visible','1');

        if (!empty($cond['table_id'])){
            $this->db->where('table_id', $cond['table_id']);
        }

        $query = $this->db->get();

        $result = $query->row_array();

        return $result['amount'];
    }



}