<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class Mst_point_rate_special_limit_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'mst_point_rate_special_limits';
        $this->primary_key = 'id';
    }

}
