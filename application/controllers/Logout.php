<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

defined('BASEPATH') or exit('No direct script access allowed');
require_once APPPATH . 'core/UpdateController.php';

class Logout extends UpdateController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Index Page for this controller.
     */
    public function index()
    {
        $this->logout(ROLE_STAFF);

        $this->logout();
    }

}