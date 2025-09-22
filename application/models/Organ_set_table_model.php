<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class Organ_set_table_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'organ_set_tables';
        $this->primary_key = 'organ_set_table_id';
    }

    public function getRecordTable($organ_id, $num){

        $this->db->from($this->table);
        $this->db->where('organ_id', $organ_id);
        $this->db->where('set_number', $num);

        $query = $this->db->get();

        return $query->row_array();
    }
}