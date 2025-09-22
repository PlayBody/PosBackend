<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class Reserve_ticket_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'reserve_tickets';
        $this->primary_key = 'reserve_ticket_id';
    }

    public function getReserveTicketList($reserve_id){
        $this->db->select('tickets.*, reserve_tickets.use_count');
        $this->db->from($this->table);
        $this->db->join('tickets', 'tickets.id=reserve_tickets.ticket_id', 'left');

        $this->db->where('reserve_id', $reserve_id);

        $this->db->order_by('tickets.ticket_id');

        $query = $this->db->get();

        return $query->result_array();
    }


}