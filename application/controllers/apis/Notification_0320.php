<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . 'core/WebController.php';

/*
 *
 */

class Notification extends WebController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('notification_model');
    }

    public function loadBadgeCounts(){
        $receiver_id = $this->input->post('receiver_id');
        $receiver_type = $this->input->post('receiver_type');
        $in_type = $this->input->post('in_type');

        $count = $this->notification_model->getBageCountByCond([
            'receiver_id' => $receiver_id,
            'receiver_type' => $receiver_type,
            'in_type' => $in_type,
        ]);

        if (empty($count)){
            echo json_encode(['count' => 0]);
            return;
        }

        echo json_encode(['count' => $count]);
    }

}
?>
