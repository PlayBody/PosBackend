<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class Table_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'tables';
        $this->primary_key = 'table_id';
    }

    public function getTableList($cond){

        $this->db->from($this->table);

        $this->db->where('visible', '1');

        if (!empty($cond['organ_id'])){
            $this->db->where('organ_id', $cond['organ_id']);
        }

        if (!empty($cond['count'])){
            $this->db->where('position <='. $cond['count']);
        }

        $this->db->order_by('position');

        $query = $this->db->get();
        return $query->result_array();
    }


    public function getTableRecord($cond){

        $this->db->from($this->table);

        if (!empty($cond['organ_id'])){
            $this->db->where('organ_id', $cond['organ_id']);
        }

        if (!empty($cond['position'])){
            $this->db->where('position', $cond['position']);
        }


        $query = $this->db->get();
        return $query->row_array();
    }


}