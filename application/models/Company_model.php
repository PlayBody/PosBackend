<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class Company_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'companies';
        $this->primary_key = 'company_id';
    }

    public function getListByCond($cond){
        $this->db->from($this->table);
        if(!empty($cond['company_id'])){
            $this->db->where('company_id', $cond['company_id']);
        }

        $query = $this->db->get();

        return $query->result_array();
    }
}