<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';
class User_model extends Base_model
{

    public function __construct()
    {
        parent::__construct();
        $this->table = 'users';
        $this->primary_key = 'user_id';
    }

    private function getWhere($cond){
        if (!empty($cond['company_id'])){
            $this->db->where('company_id', $cond['company_id']);
        }
        if (!empty($cond['user_email'])){
            $this->db->where('user_email', $cond['user_email']);
        }
        if (!empty($cond['is_enable_user'])){
            $this->db->where('visible', 1);
        }
    }

    public function getUserRow($cond){
        $this->db->from($this->table);
        $this->getWhere($cond);

        $query = $this->db->get();
        return $query->row_array();
    }

    public function getListByCond($cond){
        $this->db->from($this->table);
        $this->db->where('visible', '1');

        if (!empty($cond['company_id'])){
            $this->db->where('company_id', $cond['company_id']);
        }

        if (!empty($cond['user_name'])){
            $namestr = '%'.$cond['user_name'].'%';
            $this->db->where("(user_first_name like '$namestr' OR user_last_name like '$namestr' OR user_nick like '$namestr')");
        }

        $query = $this->db->get();
        return $query->result_array();

    }

    public function getRecordByCond($cond){
        $this->db->from($this->table);
        $this->db->where('visible', '1');

        if (!empty($cond['user_no'])){
            $this->db->where('user_no', $cond['user_no']);
        }

        $query = $this->db->get();
        return $query->row_array();

    }

    public function getUsersByCond($cond, $is_count=false, $limit='', $start_index=''){
        $this->db->from($this->table);

        if (!empty($cond['company_id'])){
            $this->db->where('company_id', $cond['company_id']);
        }

        if (!empty($cond['user_name'])){
            $search = $cond['user_name'];
            $this->db->where("(user_first_name like '%$search%' or user_last_name like '%$search%' or user_nick like '%$search%' or user_email like '%$search%')");
        }

        if (!empty($cond['user_search_birthday_from'])){
            $search = $cond['user_search_birthday_from'];
            $this->db->where("DATE_FORMAT(user_birthday, '%m-%d') >= '$search'");
        }
        if (!empty($cond['user_search_birthday_to'])){
            $search = $cond['user_search_birthday_to'];
            $this->db->where("DATE_FORMAT(user_birthday, '%m-%d') <= '$search'");
        }

        if (!empty($cond['user_sex'])){
            $this->db->where('user_sex', $cond['user_sex']);
        }

        if (!empty($cond['last_visit_date'])){
            //$this->db->where('user_sex', $cond['user_sex']);
        }

        $this->db->where('user_id != 1');
        $this->db->where('visible', 1);

        if (!empty($limit)){
            $this->db->limit($limit, $start_index);
        }

        $query = $this->db->get();
        if ($is_count) return $query->num_rows();
        return $query->result_array();

    }

    public function getUserListWithSelectGroup($company_id, $group_id){

        $sql = "select users.*, tmp.group_id from 
                                  users left join 
                                      (select * from group_users where group_users.group_id=$group_id) tmp on users.user_id = tmp.user_id 
                where users.company_id = $company_id order by users.user_nick";

        $query = $this->db->query($sql);

        return $query->result_array();

    }

    public function getUserListInSelectGroup($company_id, $group_id){

        $sql = "select users.* from 
                                  users left join group_users on users.user_id = group_users.user_id 
                where users.company_id = $company_id and group_users.group_id = $group_id order by users.user_nick";

        $query = $this->db->query($sql);

        return $query->result_array();

    }

    public function checkEmailExists($email, $company_id, $userId=''){
        $this->db->from($this->table);
        $this->db->where('user_email', $email);
        $this->db->where('company_id', $company_id);
        if (!empty($userId)){
            $this->db->where('user_id <> '. $userId);
        }

        $query = $this->db->get();

        return !empty($query->result_array());
    }

    public function getUserByToken($token, $company_id){
        $this->db->from($this->table);
        $this->db->where('user_device_token', $token);
        $this->db->where('company_id', $company_id);
        $query = $this->db->get();
        return $query->row_array();
    }

}

