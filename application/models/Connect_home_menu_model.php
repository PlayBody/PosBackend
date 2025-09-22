<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class Connect_home_menu_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'connect_home_menus';
        $this->primary_key = 'id';
    }

    public function getHomeMenuList($company_id, $is_admin){
        $this->db->from($this->table);
        $this->db->where('company_id', $company_id);
        if (empty($is_admin)){
            $this->db->where('is_use > 0');
        }
        $this->db->order_by('sort');
        $query = $this->db->get();

        return $query->result_array();
    }

    public function getHomePrevMenu($company_id, $sort){
        $this->db->from($this->table);
        $this->db->where('company_id', $company_id);
        $this->db->where('sort < '.$sort);

        $this->db->order_by('sort', 'desc');
        $query = $this->db->get();

        return $query->row_array();
    }
    public function getHomeNextMenu($company_id, $sort){
        $this->db->from($this->table);
        $this->db->where('company_id', $company_id);
        $this->db->where('sort > '.$sort);

        $this->db->order_by('sort', 'asc');
        $query = $this->db->get();

        return $query->row_array();
    }
}