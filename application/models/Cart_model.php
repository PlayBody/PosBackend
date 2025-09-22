<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class Cart_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'carts';
        $this->primary_key = 'cart_id';
    }

    public function getRecordByCond($cond){
        $this->db->from($this->table);

        if (!empty($cond['user_id'])){
            $this->db->where('user_id', $cond['user_id']);
        }

        if (!empty($cond['company_id'])){
            $this->db->where('company_id', $cond['company_id']);
        }

        if (!empty($cond['visible'])){
            $this->db->where('visible', $cond['visible']);
        }

        if (!empty($cond['cart_type'])){
            $this->db->where('cart_type', $cond['cart_type']);
        }

        $this->db->order_by('cart_id', 'desc');
        $query = $this->db->get();

        return $query->row_array();

    }

    public function getTicketSaleCount($cond){
        $this->db->select('sum(cart_details.count) as all_count');

        $this->db->from($this->table);
        $this->db->join('cart_details', 'carts.cart_id = cart_details.cart_id', 'right');
        $this->db->join('tickets', 'tickets.id = cart_details.ticket_id', 'left');


        if (!empty($cond['company_id'])){
            $this->db->where("carts.company_id ", $cond['company_id']);
        }

        if (!empty($cond['sale_date'])){
            $this->db->where("carts.create_date like '".$cond['sale_date']." %'");
        }

        if (!empty($cond['ticket_master_id'])){
            $this->db->where("tickets.ticket_id ", $cond['ticket_master_id']);
        }

        $query = $this->db->get();
        $result = $query->row_array();

        return empty($result['all_count']) ? 0 : $result['all_count'];
    }
}