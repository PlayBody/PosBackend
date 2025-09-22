<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class History_table_menu_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'history_table_menus';
        $this->primary_key = 'history_table_menu_id';
    }

    public function getListCond($cond){
        $this->db->from($this->table);
        $this->db->where('visible', '1');

        if(!empty($cond['history_table_id'])){
            $this->db->where('history_table_id', $cond['history_table_id']);
        }

        $query = $this->db->get();

        return $query->result_array();
    }

    public function getMenuSumAmount($date, $isUserMenu=''){
        $this->db->select("sum(history_table_menus.menu_price*history_table_menus.quantity) as sum_amount");

        $this->db->from($this->table);
        $this->db->join('history_tables', 'history_tables.order_table_history_id=history_table_menus.history_table_id','left');
        $this->db->join('menus', 'menus.menu_id=history_table_menus.menu_id', 'left');

        $this->db->where("history_tables.end_time like '".$date."%'");
        if(!empty($isUserMenu)){
            $this->db->where('menus.is_user_menu', $isUserMenu);
        }

        $query = $this->db->get();
        $results = $query->row_array();

        return $results['sum_amount']==null ? '0' : $results['sum_amount'];
    }

}