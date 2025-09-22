<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . 'core/WebController.php';

class Api_member extends WebController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('pos_staff_model');
        $this->load->model('pos_member_model');
        // $this->load->model('pos_setting_model');
    }

    public function load_member_setting(){

    	$staff_id = $this->input->post('staff_id');

    	$results = [];
    	if (empty($staff_id)){
    		$results['isLoaded'] = false;
    		echo json_encode($results);
    		return;
    	}

        $staff = $this->pos_staff_model->getSettings($staff_id);

    	if (empty($staff) || empty($staff['member_id'])){
    		$results['isLoaded'] = false;
    		echo json_encode($results);
    		return;
    	}

        $results['isLoaded'] = true;

        $results['set_time'] = $staff['set_time'];
        $results['set_amount'] = $staff['set_amount'];
        $results['table_amount'] = $staff['table_amount'];
        $results['accounting_count'] = $staff['accounting_count'];
        $results['menu_count'] = $staff['menu_count'];
        $results['active_start_time'] = $staff['active_start_time'];
        $results['active_end_time'] = $staff['active_end_time'];
        $results['admin'] = $staff['admin'];

        echo(json_encode($results));
    }



    public function save_member_setting(){

        $staff_id = $this->input->post('staff_id');

        $results = [];
        if (empty($staff_id)){
            $results['isUpdate'] = false;
            echo json_encode($results);
            return;
        }

        $staff = $this->pos_staff_model->getFromId($staff_id);

        if (empty($staff['member_id'])){
            $results['isUpdate'] = false;
            echo json_encode($results);
            return;
        }

        $member = $this->pos_member_model->getFromId($staff['member_id']);

        if (empty($member)){
            $results['isUpdate'] = false;
            echo json_encode($results);
            return;
        }

        $member['accounting_count'] = $this->input->post('accounting_count');
        $member['menu_count'] = $this->input->post('menu_count');
        $member['set_time'] = $this->input->post('set_time');
        $member['set_amount'] = $this->input->post('set_amount');
        $member['table_amount'] = $this->input->post('table_amount');
        $member['active_start_time'] = $this->input->post('active_start_time');
        $member['active_end_time'] = $this->input->post('active_end_time');

        $this->pos_member_model->edit($member, 'id');


        $results['isUpdate'] = true;   
        echo(json_encode($results));
    }


    public function load_staff_list(){
        $staff_id = $this->input->post('staff_id');

        $results = [];
        if (empty($staff_id)){
            $results['isLoaded'] = false;
            echo json_encode($results);
            return;
        }

        $staff = $this->pos_staff_model->getFromId($staff_id);

        if (empty($staff['member_id'])){
            $results['isLoaded'] = false;
            echo json_encode($results);
            return;
        }


        if ($staff['admin'] != 1){
            $results['isLoaded'] = true;
            $results['isAdmin'] = false;
            echo json_encode($results);
            return;
        }

        $condition = [];
        $condition['member_id'] = $staff['member_id'];
        $staff_list = $this->pos_staff_model->getListByCondition($condition);

        $results['isLoaded'] = true;            
        $results['isAdmin'] = true;

        $results['staff_list'] = $staff_list;

        echo json_encode($results);

    }

    public function load_member_list(){

        $members  = $this->pos_member_model->getMemberAll();

        $results = [];
        $results['isLoaded'] = true;

        $results['members'] = $members;
        if (empty($member)){
            $results['member_count'] = 0;
        }else{
            $results['member_count'] = 1;
        }

        echo json_encode($results);
        return;
    }


    public function get_member_image(){ 

        $member_id = $this->input->get('member_id');
        if($member_id == null)
        {
            $file = 'noImage.jpg';
        }else{
            $member = $this->pos_member_model->getFromId($member_id);

            if (empty($member) || empty($member['image'])){

                $file = 'noImage.jpg';
            }else{
                $file = $member['image'];
            }

        }

        // header('Content-Type: image/jpeg');
        // header('Content-Length: ' . filesize(base_url().'assets/images/'.$file));
        // readfile(base_url().'assets/images/'.$file);

        // send the right headers
        $file = './assets/images/member/'.$file;
        // var_dump($file);die();
        header("Content-Type: image/jpg");
        header("Content-Length: " . filesize($file));
        // dump the picture and stop the script
        echo file_get_contents($file);
        exit;

   }    
}
?>