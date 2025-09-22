<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class Question_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'questions';
        $this->primary_key = 'question_id';
    }

    public function getListQuestionData($cond){
        $this->db->select($this->table.'.*, users.user_first_name, users.user_last_name, users.user_nick');
        $this->db->from($this->table);
        $this->db->join('users', 'users.user_id=questions.user_id','left');

        if (!empty($cond['company_id'])){
            $this->db->where('users.company_id', $cond['company_id']);
        }
        $this->db->order_by('update_date', 'desc');
        $this->db->limit(30);
        $query = $this->db->get();
        return $query->result_array();

    }




}