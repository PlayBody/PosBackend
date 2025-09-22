<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class Company_site_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'company_sites';
        $this->primary_key = 'site_id';
    }

    public function getListByCond($cond){
        $this->db->from($this->table);

        if (!empty($cond['company_id'])){
            $this->db->where('company_id', $cond['company_id']);
        }

        if (!empty($cond['visible'])){
            $this->db->where('visible', $cond['visible']);
        }

        $query = $this->db->get();

        return $query->result_array();
    }
}