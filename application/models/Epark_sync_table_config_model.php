<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';
class Epark_sync_table_config_model extends Base_model
{

    public function __construct()
    {
        parent::__construct();
        $this->table = 'epark_sync_table_config';
        $this->primary_key = 'id';
    }


}

