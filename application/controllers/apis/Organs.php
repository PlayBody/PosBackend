<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

header('Access-Control-Allow-Origin: *');
Header('Access-Control-Allow-Headers: *'); //for allow any headers, insecure
Header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');

require_once APPPATH . 'core/WebController.php';

/*
 *
 */

class Organs extends WebController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('organ_model');
        $this->load->model('organ_time_model');
        $this->load->model('organ_special_time_model');
        $this->load->model('staff_organ_model');
        $this->load->model('staff_model');
    }

    public function loadOrganInfo(){
        $organ_id = $this->input->post('organ_id');
        if (empty($organ_id)){
            $results['is_result'] = false;
            $results['err_message'] = "organ_id is required";
            echo json_encode($results);
            return;
        }

        $organ = $this->organ_model->getFromId($organ_id);
        if (empty($organ)){            
            $results['is_result'] = false;
            $results['err_message'] = "organ don't exist";
            echo json_encode($results);
            return;
        }

        $results['is_result'] = true;
        $results['data'] = $organ;
        echo json_encode($results);
    }

    public function getOrganList()
    {
        $company_id = $this->input->post('company_id');
        $staff_id = $this->input->post('staff_id');

        $cond = [];
        if (!empty($company_id)) $cond['company_id'] = $company_id;
        if (!empty($staff_id)){
            $staff = $this->staff_model->get($staff_id);
            if ($staff['staff_auth'] == STAFF_AUTH_OWNER && !empty($staff['company_id'])){
                $cond['company_id'] = $staff['company_id'];
            }
            if ($staff['staff_auth'] < STAFF_AUTH_OWNER){
                $cond['staff_id'] = $staff_id;
            }
			if ($staff['staff_auth'] == STAFF_AUTH_ADMIN){
				$cond['company_id'] = '';
            }

        }
        $organ_list = $this->organ_model->getListByCond($cond);
        
        $results['is_result'] = true;
        $results['data'] = $organ_list;

        echo json_encode($results);
    }

    public function isOpen()
    {
        $organ_id = $this->input->post('organ_id');

        $time = date('H:i');
        $date_time = date('H:i');

        $weekday = date('N');

        $organ_times = $this->organ_time_model->getDataByParam(['organ_id' => $organ_id, 'weekday' => $weekday]);

        $is_open = false;
        foreach ($organ_times as $organ_time){
            if ($time>=$organ_time['from_time'] && $time<$organ_time['to_time']){
                $is_open = true;
                break;
            }
        }

        if (!$is_open){
            $specical_times = $this->organ_special_time_model->getDataByParam(['organ_id'=>$organ_id]);
            foreach ($specical_times as $specical_time){
                if ($date_time>=$specical_time['from_time'] && $date_time<$specical_time['to_time']){
                    $is_open = true;
                    break;
                }
            }
        }

        $results['is_open'] = $is_open;

        echo json_encode($results);
    }

    public function organsHavingShiftMode(){
        $company_id = $this->input->post('company_id');
        $staff_id = $this->input->post('staff_id');

        $organs = $this->staff_organ_model->getListData([
            'company_id' => $company_id, 
            'staff_id' => $staff_id, 
            'is_no_reserve_type' => ORGAN_RESERVE_MODE_TYPE_SHIFT
        ]);

        $results['is_result'] = true;
        $results['data'] = $organs;

        echo json_encode($results);
    }

}
?>
