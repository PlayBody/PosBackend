<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class User_ticket_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'user_tickets';
        $this->primary_key = 'id';
    }

    public function getListByCond($cond){
        $this->db->select($this->table.'.*, tickets.ticket_title, tickets.ticket_price02, tickets.ticket_count');
        $this->db->from($this->table);
        $this->db->join('tickets', 'user_tickets.ticket_id=tickets.id', 'inner');
        // $this->db->join('mst_tickets', 'tickets.ticket_id=mst_tickets.id', 'inner');

        if (!empty($cond['ticket_id'])){
            $this->db->where('ticket_id', $cond['ticket_id']);
        }
        if (!empty($cond['user_id'])){
            $this->db->where('user_id', $cond['user_id']);
        }

        $query = $this->db->get();

        return $query->result_array();
    }

    public function getUserTicket($cond){
        $this->db->from($this->table);

        if (!empty($cond['ticket_id'])){
            $this->db->where('ticket_id', $cond['ticket_id']);
        }
        if (!empty($cond['user_id'])){
            $this->db->where('user_id', $cond['user_id']);
        }

        $query = $this->db->get();
        return $query->row_array();
    }

}