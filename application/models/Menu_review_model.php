<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class Menu_review_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'menu_reviews';
        $this->primary_key = 'menu_review_id';
    }

    public function getRecordByCond($cond){

        $this->db->from($this->table);
        if (!empty($cond['menu_id'])){
            $this->db->where('menu_id', $cond['menu_id']);
        }
        if (!empty($cond['user_id'])){
            $this->db->where('user_id', $cond['user_id']);
        }

        $query = $this->db->get();

        return $query->row_array();

    }

}