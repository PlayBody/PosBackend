<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class Message_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'messages';
        $this->primary_key = 'message_id';
    }

    public function getMessageList($cond){
        $this->db->select($this->table.".*, organs.organ_name, IF(staff_nick is NULL, CONCAT(staff_first_name,' ', staff_last_name), staff_nick) as staff_name, CONCAT(user_first_name,' ', user_last_name) as user_name");
        $this->db->from($this->table);
        $this->db->join('organs', 'messages.organ_id=organs.organ_id', 'left');
        $this->db->join('staffs', 'messages.staff_id=staffs.staff_id', 'left');
        $this->db->join('users', 'messages.user_id=users.user_id', 'left');

        if (!empty($cond['user_id'])){
            $this->db->where($this->table.'.user_id', $cond['user_id']);
        }

        if (!empty($cond['group_id'])){
            $this->db->where('group_id', $cond['group_id']);
            $this->db->group_by('group_key');
        }

        if (!empty($cond['company_id'])){
            $this->db->where('messages.company_id', $cond['company_id']);
        }

        if (!empty($cond['organs'])){
            $this->db->where('(messages.organ_id in ('.$cond['organs'].') or type=2)');
        }

        $this->db->order_by('create_date', 'desc');
        $this->db->limit(30, 0);

        $query = $this->db->get();
        return $query->result_array();
    }

    public function getMessageUserLists($company_id, $search_word){
        $sql = "select messages.*, users.user_nick, tmp.unread_message_count, organs.organ_name from
(select max(message_id) as sel_messsage, sum(if (type=2 or read_flag=1, 0, 1)) as unread_message_count 
from messages 
where company_id=$company_id 
GROUP BY user_id) tmp
left join messages on tmp.sel_messsage = messages.message_id
left join users on messages.user_id = users.user_id
left join organs on messages.organ_id = organs.organ_id
where users.company_id=$company_id
and (user_nick like '%$search_word%' or user_first_name like '%$search_word%' or user_last_name like '%$search_word%') 
ORDER BY messages.create_date desc";

        $query = $this->db->query($sql);

        return $query->result_array();
    }

    public function getUnreadMessageCount($user_type, $user_id){
        $this->db->from($this->table);
        if ($user_type=="1"){
            $this->db->where('company_id', $user_id);
        }
        if ($user_type=="2"){
            $this->db->where('user_id', $user_id);
        }
        $this->db->where('type', $user_type);

        $this->db->where('(read_flag is null or read_flag<>1)');

        $query = $this->db->get();
        return $query->num_rows();
    }
}