<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . 'core/WebController.php';

/*
 *
 */

class Opentime extends WebController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('organ_time_model');
        $this->load->model('organ_special_time_model');

    }

    public function isOpen(){
        $organ_id = $this->input->post('organ_id');

        $weekday = $date = date('N');
        $time = date('H:i:s');

        $organTimes = $this->organ_time_model->getListByCond([
            'organ_id' => $organ_id,
            'weekday' => date('N'),
            'select_time' => date('H:i:s')
        ]);

        if(!empty($organTimes)){
            echo json_encode(['is_open' => true]);
            return;
        }

        $specialTimes = $this->organ_special_time_model->getListByCond([
            'organ_id' => $organ_id,
            'select_time' => date('Y-m-d H:i:s'),
        ]);

        if(!empty($specialTimes)){
            echo json_encode(['is_open' => true]);
            return;
        }

        echo json_encode(['is_open' => false]);

    }

    public function getTodaySpecialTime(){
        $organ_id = $this->input->post('organ_id');

        $times = $this->organ_special_time_model->getListByCond([
            'organ_id' => $organ_id,
            'select_date' => date('Y-m-d'),
        ]);

        if (empty($times)){
            echo json_encode(['is_load' => false]);
            return;
        }

        echo json_encode(['is_load' => true, 'times' => $times]);
        return;

    }
}
?>
