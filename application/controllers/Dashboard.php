<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/core/UpdateController.php';

class Dashboard extends UpdateController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct(ROLE_STAFF);

        //チャットボット
        $this->header['page'] = 'dashboard';
        $this->header['sub_page'] = 'dashboard';
        $this->header['title'] = '';
        $this->header['staff'] = $this->staff;

        $this->load->model('scenario_model');
        $this->load->model('bot_model');
        $this->load->model('user_model');
    }

    /**
     * This function used to load the first screen of the user
     */
    public function index()
    {

        $this->load_view_with_menu("dashboard");
        //$this->_load_view("dashboard");
    }

    /**
     * Page not found : error 404
     */
    function pageNotFound()
    {
        $this->global['pageTitle'] = '404エラー';

        $this->loadViews("404", $this->global, NULL, NULL);
    }
}

?>
