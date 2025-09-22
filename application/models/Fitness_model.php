<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class Fitness_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'fitnesses';
        $this->primary_key = 'fitness_id';
    }

    public function getListByCond($cond){
        $this->db->from($this->table);
        if (!empty($cond['company_id'])){
            $this->db->where('company_id', $cond['company_id']);
        }
        if (!empty($cond['group_id'])){
            $this->db->where('group_id', $cond['group_id']);
        }else{
            $this->db->where('group_id is null');
        }

        $this->db->order_by('create_date', 'desc');
        $this->db->limit(10);

        $query = $this->db->get();

        return $query->result_array();
    }



}