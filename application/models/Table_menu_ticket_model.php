<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class Table_menu_ticket_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'table_menu_tickets';
        $this->primary_key = 'id';
    }

    function getListByCond($cond)
    {
        $this->db->select('*')->from($this->table);

        if (!empty($cond['table_menu_id'])){
            $this->db->where('table_menu_id', $cond['table_menu_id']);
        }

        $query = $this->db->get();

        return $query->result_array();
    }

    function getTicketAmountByCond($cond)
    {
        $this->db->select('sum(tickets.ticket_price02*table_menu_tickets.count) as amount');

        $this->db->from($this->table);
        $this->db->join('tickets', 'table_menu_tickets.ticket_id=tickets.id', 'left');
        $this->db->join('table_menus', 'table_menus.table_menu_id=table_menu_tickets.table_menu_id', 'left');
        $this->db->join('tables', 'tables.table_id=table_menus.table_id', 'left');

       // $this->db->where('visible','1');

        if (!empty($cond['table_id'])){
            $this->db->where('tables.table_id', $cond['table_id']);
        }

        $query = $this->db->get();

        $result = $query->row_array();

        return $result['amount'];
    }


}