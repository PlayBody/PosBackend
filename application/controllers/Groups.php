<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . 'core/WebController.php';

/*
 *
 */

class Groups extends WebController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('group_model');
        $this->load->model('user_model');
    }

    public function makeGroup(){
        $company_id = $this->input->post('company_id');
        $creator_id = $this->input->post('staff_id');

        $group_name = $this->input->post('group_name');

        if (empty($company_id) || empty($creator_id)){
            $results['isSave'] = false;
            echo json_encode($results);
            return;
        }

        $group = array(
            'company_id' => $company_id,
            'creator_id' => $creator_id,
            'group_name' => $group_name,
            'visible' => 1,
        );

        $group_id = $this->group_model->insertReocord($group);

        $results['isSave'] = true;
        $results['group_id'] = $group_id;

        echo json_encode($results);
    }

    public function loadUsersWithGroupStatus(){
        $group_id = $this->input->post('group_id');
        $company_id = $this->input->post('company_id');

        if (empty($company_id) || empty($group_id)){
            $results['isLoad'] = false;
            echo json_encode($results);
            return;
        }

        $users = $this->user_model->getUsersWithIsGroup($company_id, $group_id);

        $results['isLoad'] = true;
        $results['users'] = $users;

        echo json_encode($results);
    }
}
?>