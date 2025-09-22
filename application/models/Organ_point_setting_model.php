<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class Organ_point_setting_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'organ_point_settings';
        $this->primary_key = 'organ_point_id';
    }


}