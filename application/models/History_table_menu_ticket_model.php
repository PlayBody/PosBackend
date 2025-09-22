<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class History_table_menu_ticket_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'history_table_menu_tickets';
        $this->primary_key = 'id';
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

    public function getTicketCount($cond, $is_get_amount=''){
        if (empty($is_get_amount)){
            $this->db->select("sum(history_table_menu_tickets.count) as ticket_count");
        }else{
            $this->db->select("sum(history_table_menu_tickets.count*tickets.ticket_price02) as sum_amount");
        }
        $this->db->from($this->table);
        $this->db->join('tickets', 'tickets.id=history_table_menu_tickets.ticket_id', 'left');
        $this->db->join('history_table_menus', 'history_table_menus.history_table_menu_id=history_table_menu_tickets.history_table_menu_id', 'left');
        $this->db->join('history_tables', 'history_tables.order_table_history_id=history_table_menus.history_table_id', 'left');
        if (!empty($cond['sel_date'])){
            $this->db->where("history_tables.end_time like '".$cond['sel_date']."%'");
        }

        if (!empty($cond['ticket_master_id'])){
            $this->db->where('tickets.ticket_id', $cond['ticket_master_id']);
        }

        if (!empty($cond['ticket_use_am'])){
            $this->db->where("history_tables.start_time <= '".$cond['sel_date']." 14:00:00'");
        }
        if (!empty($cond['ticket_use_pm'])){
            $this->db->where("history_tables.start_time > '".$cond['sel_date']." 14:00:00'");
        }

        $query = $this->db->get();

        $result = $query->row_array();

        if (empty($is_get_amount)){
            return $result['ticket_count']==null ? '0' : $result['ticket_count'];
        }
        return $result['sum_amount']==null ? '0' : $result['sum_amount'];
    }

}