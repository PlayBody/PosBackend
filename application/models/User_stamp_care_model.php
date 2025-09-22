<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class User_stamp_care_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'user_stamp_cares';
        $this->primary_key = 'id';
    }


}
