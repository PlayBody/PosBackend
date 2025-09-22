<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class Shift_frame_ticket_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'shift_frame_tickets';
        $this->primary_key = 'id';
    }

    private function getWhere($cond){
		if (!empty($cond['shift_frame_id'])) {
			$this->db->where('shift_frame_id', $cond['shift_frame_id']);
		}
		if (!empty($cond['ticket_id'])) {
			$this->db->where('ticket_id', $cond['ticket_id']);
		}
        
    }

    public function getListData($cond){
        $this->db->from($this->table);

        $this->getWhere($cond);
        
        $query = $this->db->get();

        return $query->result_array();

    }


	public function getRowByCond($cond)
	{
        $this->db->from($this->table);

        $this->getWhere($cond);
        
        $query = $this->db->get();

        return $query->row_array();
	}

}
