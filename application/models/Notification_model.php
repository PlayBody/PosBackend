<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class Notification_model extends Base_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'notifications';
        $this->primary_key = 'id';
    }

    public function getListByCond($cond){
        $this->db->from($this->table);

        if (!empty($cond['receiver_type'])){
            $this->db->where('receiver_type', $cond['receiver_type']);
        }
        if (!empty($cond['receiver_id'])){
            $this->db->where('receiver_id', $cond['receiver_id']);
        }

        $query = $this->db->get();

        return $query->result_array();
    }

    public function getRecordByCond($cond){
        $this->db->from($this->table);

        if (!empty($cond['receiver_type'])){
            $this->db->where('receiver_type', $cond['receiver_type']);
        }
        if (!empty($cond['receiver_id'])){
            $this->db->where('receiver_id', $cond['receiver_id']);
        }
        if (!empty($cond['notification_type'])){
            $this->db->where('notification_type', $cond['notification_type']);
        }

        $query = $this->db->get();

        return $query->row_array();
    }

    public function getBageCount($receiver_id, $receiver_type){
        $this->db->select('sum(badge_count) as badge_count');
        $this->db->from($this->table);
        $this->db->where('receiver_id', $receiver_id);
        $this->db->where('receiver_type', $receiver_type);

        $query = $this->db->get();

        $result = $query->row_array();
        return $result['badge_count'];
    }
    public function getBageCountByCond($cond){
        $this->db->select('sum(badge_count) as badge_count');
        $this->db->from($this->table);
        if (!empty($cond['receiver_id'])){
            $this->db->where('receiver_id', $cond['receiver_id']);
        }
        if (!empty($cond['receiver_type'])){
            $this->db->where('receiver_type', $cond['receiver_type']);
        }
        if (!empty($cond['in_type'])){
            $this->db->where("notification_type in (" . $cond['in_type'].")");
        }

        $query = $this->db->get();

        $result = $query->row_array();
        return $result['badge_count'];
    }

    public function getBageCountByCondArray($cond){

        $this->db->select('*');
        $this->db->from($this->table);
        if (!empty($cond['receiver_id'])){
            $this->db->where('receiver_id', $cond['receiver_id']);
        }
        if (!empty($cond['receiver_type'])){
            $this->db->where('receiver_type', $cond['receiver_type']);
        }
        if (!empty($cond['in_type'])){
            $this->db->where("notification_type in (" . $cond['in_type'].")");
        }

        $query = $this->db->get();

        $result = $query->result_array();

        return $result;

    }
}