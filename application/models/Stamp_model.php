<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class Stamp_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'stamps';
        $this->primary_key = 'stamp_id';
    }

    public function getStampList($cond){
        $this->db->from($this->table);

        if (!empty($cond['user_id'])){
            $this->db->where('user_id', $cond['user_id']);
        }
        if (!empty($cond['company_id'])){
            $this->db->where('company_id', $cond['company_id']);
        }

        if ($cond['use_flag']=='0' || !empty($cond['use_flag'])){
            $this->db->where('use_flag', $cond['use_flag']);
        }

        if (!empty($cond['from_time'])){
            $this->db->where("create_date>='".$cond['from_time']."'");
        }

        $query = $this->db->get();

        return $query->result_array();

    }


}
