<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';
class App_version_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'app_versions';
        $this->primary_key = 'id';
    }

    function getLastVersion($app_id, $os_type){
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where('app_id', $app_id);
        $this->db->where('os_type', $os_type);
        $this->db->order_by('update_date', 'desc');
        $query = $this->db->get();
        return $query->row_array();
    }
}

  