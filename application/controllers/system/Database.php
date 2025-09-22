<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/core/AdminController.php';

class Database extends AdminController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct(ROLE_STAFF);
        if ($this->staff['staff_auth'] < 4) {
            redirect('login');
        }

        $this->header['page'] = 'epark';
        $this->header['sub_page'] = 'shift';
        $this->header['title'] = 'システムメンテナンス';


        $this->load->model('company_model');
        $this->load->model('organ_model');
        $this->load->model('shift_model');
    }

    /**
     * This function used to load the first screen of the user
     */
    public function shift()
    {
        $this->load_view_with_menu("system/shift_check.php");
    }
}
?>
