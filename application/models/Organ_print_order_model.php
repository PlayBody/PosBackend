<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class Organ_print_order_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'organ_print_orders';
        $this->primary_key = 'id';
    }

    public function getMaxOrder($organ_id, $date){
        $this->db->from($this->table);

        $this->db->where('organ_id', $organ_id);
        $this->db->where('print_date', $date);

        $query = $this->db->get();
        return $query->row_array();
    }

}