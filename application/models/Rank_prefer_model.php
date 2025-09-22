<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class Rank_prefer_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'rank_prefers';
        $this->primary_key = 'rank_prefer_id';
    }

	private function where($cond)
	{
		if (!empty($cond['rank_id'])) {
			$this->db->where('rank_id', $cond['rank_id']);
		}
		if (!empty($cond['max_stamp_cnt'])) {
			$this->db->where('stamp_count <=' . $cond['max_stamp_cnt']);
		}
	}

	public function getListByCond($cond)
	{
		$this->db->select('*');
		$this->db->from($this->table);
		$this->where($cond);

		$query = $this->db->get();
        return $query->result_array();
	}

    public function getPreferList($cond){
        $this->db->select($this->table.'.*, menus.menu_title as menu_name, coupons.coupon_name as coupon_name');
        $this->db->from($this->table);
        $this->db->join('menus', 'menus.menu_id=rank_prefers.menu_id', 'left');
        $this->db->join('coupons', 'coupons.coupon_id=rank_prefers.coupon_id', 'left');
        $this->db->join('ranks', 'ranks.rank_id=rank_prefers.rank_id', 'left');

        if(!empty($cond['company_id'])){
            $this->db->where('ranks.company_id', $cond['company_id']);
        }

        if(!empty($cond['rank_id'])){
            $this->db->where('rank_prefers.rank_id', $cond['rank_id']);
        }

        $query = $this->db->get();
        return $query->result_array();
    }

	public function getChangeGradePrefers($company_id, $old_grade, $update_grade, $old_stamp, $update_stamp)
	{
        $this->db->select('rank_prefers.*');
        $this->db->from($this->table);
		$this->db->join('ranks', 'rank_prefers.rank_id = ranks.rank_id');
		$this->db->where('ranks.company_id = '.$company_id.' and 
			((ranks.rank_level = '.$old_grade.' and rank_prefers.stamp_count>='.$old_stamp.') or 
			(ranks.rank_level > '.$old_grade.' and ranks.rank_level<'.$update_grade.') or 
			(ranks.rank_level = '.$update_grade.' and rank_prefers.stamp_count<='.$update_stamp.'))');

        $query = $this->db->get();
        $result = $query->result_array();

        return $result;
	}


	public function getAvailablePrefers($company_id, $rank, $stamp_cnt)
	{
		$this->db->select('rank_prefers.*');
		$this->db->from($this->table);
		$this->db->join('ranks', 'rank_prefers.rank_id = ranks.rank_id');

		$this->db->where('ranks.company_id = '.$company_id.' and 
			((ranks.rank_level = '.$rank.' and rank_prefers.stamp_count<='.$stamp_cnt.') or 
			ranks.rank_level<'.$rank.')');


		$query = $this->db->get();
        return $query->result_array();
	}

	public function getAllPrefers($company_id)
	{
		$this->db->select('rank_prefers.*');
		$this->db->from($this->table);
		$this->db->join('ranks', 'rank_prefers.rank_id = ranks.rank_id');

		$query = $this->db->get();
        return $query->result_array();
	}

}