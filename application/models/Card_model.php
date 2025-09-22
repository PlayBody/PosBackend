<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class Card_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'cards';
        $this->primary_key = '';
    }

}