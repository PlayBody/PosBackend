<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';
class Oauth_info_model extends Base_model
{

    public function __construct()
    {
        parent::__construct();
        $this->table = 'oauth_infos';
        $this->primary_key = 'id';
    }

}

  