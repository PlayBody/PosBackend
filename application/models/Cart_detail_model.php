<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class Cart_detail_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'cart_details';
        $this->primary_key = 'cart_detail_id';
    }

    public function getListByCond($cond){
        $this->db->select('cart_details.*, mst_tickets.ticket_name, tickets.ticket_title, tickets.ticket_image, tickets.ticket_price, tickets.ticket_detail, tickets.ticket_tax, tickets.ticket_count');
        $this->db->from($this->table);
        $this->db->join('tickets', 'cart_details.ticket_id = tickets.id', 'left');
        $this->db->join('mst_tickets', 'mst_tickets.id = tickets.ticket_id', 'left');

        if (!empty($cond['cart_id'])){
            $this->db->where('cart_id', $cond['cart_id']);
        }

        $query = $this->db->get();

        return $query->result_array();

    }
    public function getRecordByCond($cond){
        $this->db->from($this->table);

        if (!empty($cond['cart_id'])){
            $this->db->where('cart_id', $cond['cart_id']);
        }

        if (!empty($cond['ticket_id'])){
            $this->db->where('ticket_id', $cond['ticket_id']);
        }

        $query = $this->db->get();

        return $query->row_array();

    }

    public function getAllCartSum($cond){
        $this->db->select('sum(tickets.ticket_price*cart_details.count) as sum_amount');
        $this->db->from($this->table);
        $this->db->join('tickets', 'tickets.id=cart_details.ticket_id','left');

        if (!empty($cond['cart_id'])){
            $this->db->where('cart_id', $cond['cart_id']);
        }

        $query = $this->db->get();

        $result =  $query->row_array();

        return $result['sum_amount'] ==null ? '0' : $result['sum_amount'];
    }

}