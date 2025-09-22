<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class Ticket_push_setting_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'ticket_push_settings';
        $this->primary_key = 'id';
    }

    public function getSettingList($ticket_id){

        $this->db->from($this->table);

        $this->db->where('ticket_id', $ticket_id);
        $this->db->order_by('before_day');

        $query = $this->db->get();

        return $query->result_array();
    }



}