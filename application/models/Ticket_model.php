<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class Ticket_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'tickets';
        $this->primary_key = 'id';
    }

    public function getListByCond($cond){
        $this->db->select($this->table.'.*, mst_tickets.ticket_name');
        $this->db->from($this->table);
        $this->db->join('mst_tickets', 'tickets.ticket_id=mst_tickets.id', 'inner');

        if (!empty($cond['company_id'])){
            $this->db->where('tickets.company_id', $cond['company_id']);
        }

        $this->db->order_by('mst_tickets.id', 'asc');
        $query = $this->db->get();

        return $query->result_array();

    }
}