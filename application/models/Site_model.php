<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class Site_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'company_sites';
        $this->primary_key = 'id';
    }

    public function getSiteInfo($company_id){

        $this->db->from($this->table);

        $this->db->where('company_id', $company_id);

        $query = $this->db->get();
        return $query->row_array();
    }


}