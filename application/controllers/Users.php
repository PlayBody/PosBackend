<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . 'core/WebController.php';

/*
 *
 */

class Users extends WebController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('user_model');
    }

    public function loadUserList(){
//        $user_no = $this->input->post('user_no');

        $users = $this->user_model->getUsersByCond([]);

        $results['isLoad'] = true;
        $results['users'] = $users;

        echo json_encode($results);
    }
}
?>