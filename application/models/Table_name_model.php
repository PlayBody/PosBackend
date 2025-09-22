<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class Table_name_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'table_names';
        $this->primary_key = 'id';
    }

//
//    public function getTableRecord($cond){
//
//        $this->db->from($this->table);
//
//        if (!empty($cond['organ_id'])){
//            $this->db->where('organ_id', $cond['organ_id']);
//        }
//
//        if (!empty($cond['position'])){
//            $this->db->where('position', $cond['position']);
//        }
//
//
//        $query = $this->db->get();
//        return $query->row_array();
//    }

}
