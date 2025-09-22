<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class Notification_text_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'notification_texts';
        $this->primary_key = 'id';
    }

    public function getRecordByCond($cond){
        $this->db->from($this->table);
        if(!empty($cond['company_id'])){
            $this->db->where('company_id', $cond['company_id']);
        }
        if(!empty($cond['mail_type'])){
            $this->db->where('mail_type', $cond['mail_type']);
        }

        $query = $this->db->get();

        return $query->row_array();
    }
}