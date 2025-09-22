<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class Staff_shift_sort_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'staff_shift_sorts';
        $this->primary_key = 'id';
    }

    public function getSortList($staff_id){
        $this->db->from($this->table);

        $this->db->where('staff_id', $staff_id);

        $this->db->order_by('sort', 'asc');

        $query = $this->db->get();

        return $query->result_array();

    }

    public function getSortMax($staff_id){
        $this->db->select('max(sort) as sort');
        $this->db->from($this->table);

        $this->db->where('staff_id', $staff_id);

        $query = $this->db->get();

        $result = $query->row_array();

        $sort = empty($result['sort']) ? 0 : $result['sort'];
        return $sort+1;
    }

}