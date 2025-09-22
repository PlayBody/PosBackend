<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';
class Shift_meta_model extends Base_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'shift_meta';
        $this->primary_key = 'meta_id';
    }

    public function saveData($shift_id, $st, $en)
    {
        $data = ["shift_id"=>$shift_id, "from_time"=>$st, "to_time"=>$en];
        $this->insertRecord($data);
        // $data = $this->get($shift_id, 'shift_id');
        // if(empty($data)){
        // } else {
        //     $this->updateRecord($data);
        // }
    }
}
