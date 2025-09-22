<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class Rank_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'ranks';
        $this->primary_key = 'rank_id';
    }

    public function getRankList($condition){
        $this->db->select();
        $this->db->from($this->table);

        if(!empty($condition['company_id'])){
            $this->db->where('company_id', $condition['company_id']);
        }

        if(!empty($condition['max_rank'])){
           $this->db->where('rank_level <= ' . $condition['max_rank']);
        }

        if(!empty($condition['after_rank'])){
            $this->db->where('rank_level > ' . $condition['after_rank']);
        }

        if(!empty($condition['prev_rank'])){
            $this->db->where('rank_level < ' . $condition['prev_rank']);
        }

        $this->db->order_by('rank_level');

        $query = $this->db->get();
        return $query->result_array();

    }

    public function getFromMaxLevel($company_id){
        $this->db->select("max(rank_level) as max_level");
        $this->db->from($this->table);

        $this->db->where('company_id', $company_id);

        $results = $this->db->get()->row_array();

        if (empty($results) || empty($results['max_level'])) return 1;
        return $results['max_level'] + 1;

    }

    public function getMaxRank($company_id){
        $this->db->select("max(rank_level) as max_level");
        $this->db->from($this->table);

        $this->db->where('company_id', $company_id);

        $results = $this->db->get()->row_array();

        if (empty($results) || empty($results['max_level'])) return 0;
        return $results['max_level'];

    }

    public function getRankRecord($condition){
        $this->db->select();
        $this->db->from($this->table);

        if(!empty($condition['company_id'])){
            $this->db->where('company_id', $condition['company_id']);
        }

        if(!empty($condition['rank_level'])){
            $this->db->where('rank_level', $condition['rank_level']);
        }

        $query = $this->db->get();
        return $query->row_array();
    }
}
