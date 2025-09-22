<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class Shift_status_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'shift_status';
        $this->primary_key = 'id';
    }
}
