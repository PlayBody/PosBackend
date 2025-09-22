<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . 'core/WebController.php';

/*
 *
 */

class Apigroups extends WebController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('group_model');
        $this->load->model('user_model');
        $this->load->model('group_user_model');
    }

    public function loadGroupList(){
        $company_id = $this->input->post('company_id');

        $groups = $this->group_model->getListByCond(['company_id'=>$company_id]);

        $results['isLoad'] = true;
        $results['groups'] = $groups;

        echo json_encode($results);
    }

    public function loadGroupInfo(){
        $group_id = $this->input->post('group_id');

        $group = $this->group_model->getFromId($group_id);

        $results['isLoad'] = true;
        $results['group'] = $group;

        echo json_encode($results);
    }

    public function saveGroupName(){
        $group_id = $this->input->post('group_id');
        $company_id = $this->input->post('company_id');
        $creator_id = $this->input->post('staff_id');
        $group_users = $this->input->post('group_users');

        $group_name = $this->input->post('group_name');

        if (empty($company_id) || empty($creator_id)){
            $results['isSave'] = false;
            echo json_encode($results);
            return;
        }

        if (empty($group_id)){
            $group = array(
                'company_id' => $company_id,
                'creator_id' => $creator_id,
                'group_name' => $group_name,
                'visible' => 1,
            );
            $group_id = $this->group_model->insertRecord($group);
        }else{
            $group = $this->group_model->getFromId($group_id);
            $group['group_name'] = $group_name;
            $this->group_model->updateRecord($group, 'group_id');
        }

        $old_users = $this->group_user_model->getUsersByGroupGroup($group_id);
        foreach ($old_users as $user){
            $this->group_user_model->delete_force($user['id'],'id');
        }

        $users = json_decode($group_users);

        foreach ($users as $user){
            $data = array(
                'group_id' =>$group_id,
                'user_id' => $user,
            );
            $this->group_user_model->insertRecord($data);
        }

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

    public function updateUserGroup(){
        $user_id = $this->input->post('user_id');
        $group_id = $this->input->post('group_id');
        $update_value = $this->input->post('update_value');

        $group = $this->group_user_model->getUserGroup($user_id, $group_id);

        if ($update_value=='1'){
            if (empty($group)){
                $group['user_id'] = $user_id;
                $group['group_id'] = $group_id;

                $this->group_user_model->insertRecord($group);
            }
        }else{
            if (!empty($group)){
                $this->group_user_model->delete_force($group['id']);
            }
        }

        $results['isUpdate'] = true;

        echo json_encode($results);
    }

    public function deleteGroup(){
        $group_id = $this->input->post('group_id');
        $this->group_model->delete_force($group_id, 'group_id');

        $user_groups = $this->group_user_model->getUsersByGroupGroup($group_id);

        foreach ($user_groups as $item){
            $this->group_user_model->delete_force($item['id'], 'id');
        }

        $results['isDelete'] = true;
        echo json_encode($results);
    }
}
?>