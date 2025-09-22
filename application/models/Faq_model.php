<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class Faq_model extends Base_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'tbl_faq';
        $this->primary_key = 'id';
    }

    function getList($select, $where_data, $count_flag = false, $page = 10, $offset = 0, $order_by = '')
    {
        if (empty($select)) {
            $select = 'A.*,sum(B.view_cnt) as view';
        }
        if (!$count_flag) {
            $this->db->select($select);
            $this->db->from('tbl_faq as A');
            $join = 'B.faq_id= A.id';
            if(!empty($where_data['start_date'])){
                $join.=' and B.view_date >="'.$where_data['start_date'].'" ';
            }
            if(!empty($where_data['end_date'])){
                $join.=' and B.view_date <="'.$where_data['end_date'].'" ';
            }
            $this->db->join('tbl_analytics as B', $join, 'left');
        } else {
            $this->db->select('count(*) as rows');
            $this->db->from($this->table . ' as A');
        }

        if (is_array($where_data)) {
            foreach ($where_data as $key => $value) {
                if ($key == 'searchText') {
                    if (!empty($value)) $this->db->where('(A.title like "%' . $value . '%" OR A.content like "%' . $value . '%" )', NULL, false);
                } else if ($key == 'start_date') {
                    //if (!empty($value) && !$count_flag) $this->db->where('B.view_date >=', $value);
                } else if ($key == 'end_date') {
                    //if (!empty($value) && !$count_flag) $this->db->where('B.view_date <=', $value);
                } else {
                    $this->db->where('A.' . $key, $value);
                }
            }
        }
        if (!$count_flag) {
            $this->db->group_by('A.id,A.company_id');
            if ($order_by && is_array($order_by)) {
                foreach ($order_by as $key => $value) {
                    $this->db->order_by($key, $value);
                }
            }
            $this->db->limit($page, $offset);
            $query = $this->db->get();
            $result = $query->result();
            return $result;
        } else {
            $result = $this->db->get()->row();
            return $result->rows;
        }
    }

}