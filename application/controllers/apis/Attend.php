<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . 'core/WebController.php';

/*
 *
 */

class Attend extends WebController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('staffs/staff_attend_model');
    }

    public function loadAttendOrgan(){
        $staff_id = $this->input->get_post('staff_id');

        $attend = $this->staff_attend_model->getAttendRecord($staff_id);
        
        if (empty($attend)){
            echo json_encode(['is_attend' => false]);
            return;
        }

        echo json_encode(['is_attend' => true, 'organ_id' => $attend['organ_id']]);
    }

    public function updateAttend(){
        $staff_id = $this->input->post('staff_id');
        $organ_id = $this->input->post('organ_id');
        $type = $this->input->post('type');
        if($type == '1'){ //start
            $attend = array(
                'organ_id' => $organ_id,
                'staff_id' => $staff_id,
                'from_time' => date('Y-m-d H:i:s'),
                'to_time' => null
            );

            $this->staff_attend_model->insertRecord($attend);
        }
        if ($type == '2'){
            $attend = $this->staff_attend_model->getAttendRecord($staff_id);
            $attend['to_time'] = date('Y-m-d H:i:s');
            $this->staff_attend_model->updateRecord($attend);
        }

        echo json_encode(['is_update' => true]);
    }

}
?>
