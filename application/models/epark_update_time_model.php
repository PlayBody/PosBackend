<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class Epark_update_time_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'epark_update_times';
        $this->primary_key = 'id';
    }

}