<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class User_read_news_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'user_read_news';
        $this->primary_key = 'id';
    }

}
