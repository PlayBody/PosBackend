<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class Category_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'categories';
        $this->primary_key = 'id';
    }

    public function getCategoryList($cond){
        $this->db->from($this->table);
		if (!empty($cond['company_id'])) {
			$this->db->where('company_id', $cond['company_id']);
		}else{
			$this->db->where('company_id', 2);
		}
        if (!empty($cond['word'])){
            $this->db->where("name like '%".$cond['word']."%' or alias like '%".$cond['word']."%'");
        }
        $this->db->order_by('order_no','asc');
        $result = $this->db->get()->result_array();
        return $result;
    }
}