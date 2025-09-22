<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class Question_favorite_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'question_favorites';
        $this->primary_key = 'id';
    }

    public function getListQuestionData($company_id){
        $this->db->from($this->table);
        $this->db->where('company_id', $company_id);
        $this->db->order_by('create_date', 'desc');
        $this->db->limit(10);
        $query = $this->db->get();
        return $query->result_array();

    }




}