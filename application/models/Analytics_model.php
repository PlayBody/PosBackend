<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class Analytics_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'tbl_analytics';
        $this->primary_key = 'analyticsid';
    }

    function getList($select,$where_data, $count_flag=false,$page=10, $offset=0,$order_by='')
    {
        if(empty($select)){
            $select = $this->table. '.*,tbl_analytics.*';
        }
        $this->db->select($select);
        $this->db->from($this->table);
        $this->db->join('tbl_faq','tbl_faq.id=tbl_analytics.faq_id','left');
//        if(is_array($where_data)){
//            foreach ($where_data as $key => $value){
//                if($key=='searchText'){
//                    $this->db->like('title',$value);
//                }else{
//                    $this->db->where($key,$value);
//                }
//            }
//        }
        if($order_by && is_array($order_by)){
            foreach ($order_by as $key => $value) {
                $this->db->order_by($key, $value);
            }
        }
        if(!$count_flag){
            $this->db->limit($page, $offset);
            $query = $this->db->get();
            $result = $query->result();
            return $result;
        }else{
            return  $this->db->count_all_results();
        }
    }

}