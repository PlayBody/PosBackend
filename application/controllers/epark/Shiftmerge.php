<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/core/AdminController.php';

class Shiftmerge extends AdminController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct(ROLE_STAFF);
        if ($this->staff['staff_auth'] < STAFF_AUTH_ADMIN) {
            redirect('login');
        }
    }

    /**
     * This function used to load the first screen of the user
     */
    public function index()
    {
       // $shifts = $this->shift_model->get
    }
}
?>
