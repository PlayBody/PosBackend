<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . 'core/WebController.php';

/*
 *
 */

class InitShift extends WebController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('shift_init_model');

    }

    public function load(){
        $organ_id = $this->input->post('organ_id');
        $staff_id = $this->input->post('staff_id');
        $pattern = $this->input->post('pattern');

        $shifts = $this->shift_init_model->getDataByParam([
            'organ_id' => $organ_id,
            'staff_id' => $staff_id,
            'pattern' => $pattern
        ]);

        if(empty($shifts)){
            echo json_encode(['is_load' => false]);
            return;
        }

        echo json_encode(['is_load' => true, 'shifts' => $shifts]);
    }


    public function getFromSelDate(){
        $select_date = $this->input->post('select_date');
        $organ_id = $this->input->post('organ_id');
        $staff_id = $this->input->post('staff_id');
        $pattern = $this->input->post('pattern');
        $cond = [
            'organ_id' => $organ_id,
            'staff_id' => $staff_id,
            'pattern' => $pattern,
            'week_num' => date('w', strtotime($select_date)),
            'selected_date' => date('H:i:s', strtotime($select_date)),
        ];

        $shift = $this->shift_init_model->getRecordByCond($cond);

        if(empty($shift)){
            echo json_encode(['is_load' => false]);
            return;
        }

        echo json_encode(['is_load' => true, 'shift' => $shift]);
    }

}
?>
