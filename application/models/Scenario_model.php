<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class Scenario_model extends Base_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'tbl_scenario';
        $this->primary_key = 'scenario_id';
    }

    public function counter($id){

        $sql = 'update '.$this->table.' set select_cnt=select_cnt+1 where '.$this->primary_key.'=?';
        $this->db->query($sql, array($id));
        return true;
    }

    public function getDetail($id,$company_id)
    {
        $key = $this->primary_key;
        $this->db->select('A.scenario_id,A.level,A.title,A.content,B.title as parent_title');
        $this->db->from($this->table.' as A');
        $this->db->join($this->table.' as B','A.parent_id =B.scenario_id','left');
        $this->db->where('A.'.$key, $id);
        $this->db->where('A.company_id', $company_id);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row;
        }
        return array();
    }

    public function getChild($param = null)
    {
        $this->db->select('scenario_id as value,title as content');
        if ($param) {
            $this->db->where($param);
        }
        $this->db->order_by("tree_code", "ASC");
        $result = $this->db->get($this->table)->result_array();
        return $result;
    }

    function getScenarioList($company_id,$count=false)
    {
        $this->db->select("*");
        $this->db->from($this->table);
        $this->db->where('company_id',$company_id);
        if($count==true){
            return $this->db->count_all_results();
        }
        $this->db->order_by("tree_code", "ASC");
        $query = $this->db->get();
        $result = $query->result();

        return $result;
    }

    function getGroupMaxID($parent_id)
    {
        $this->db->select("group_order");
        $this->db->from($this->table);
        $this->db->where("parent_id", $parent_id);
        $this->db->order_by("group_order", "DESC");
        $this->db->limit(1);
        $query = $this->db->get();
        return $query->row_array();
    }

    function delete_tree($code)
    {
        $this->db->like('tree_code', $code, 'after');
        $this->db->delete($this->table);
        return true;
    }

    function get_tree($code, $count_flag = false)
    {
        $this->db->from($this->table);
        $this->db->like('tree_code', $code, 'after');
        if ($count_flag) {
            return $this->db->count_all_results();
        } else {
            return $this->db->get()->result_array();
        }
    }
}

?>