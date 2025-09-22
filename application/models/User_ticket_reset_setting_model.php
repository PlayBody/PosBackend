<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class User_ticket_reset_setting_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'user_ticket_reset_settings';
        $this->primary_key = 'id';
    }

    public function getResetSettingList($days, $week){
        $strSql = "select * from user_ticket_reset_settings where 
            ((time_type=2 and time_value=$week) || (time_type=1 and time_value in ($days))) and is_enable=1";

        $query = $this->db->query($strSql);

        return $query->result_array();
    }

    public function getResetSetting($cond){
        $this->db->from($this->table);

        if (!empty($cond['user_id'])){
            $this->db->where('user_id', $cond['user_id']);
        }

        $query = $this->db->get();

        return $query->row_array();

    }


}