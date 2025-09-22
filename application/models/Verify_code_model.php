<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';
class Verify_code_model extends Base_model
{

    public function __construct()
    {
        parent::__construct();
        $this->table = 'verify_codes';
        $this->primary_key = 'id';
    }

}

