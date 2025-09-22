<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . 'core/WebController.php';

/*
 *
 */

class Apinotifications extends WebController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('notification_model');
    }

    public function getBadge(){
        $receiver_id = $this->input->post('receiver_id');
        $receiver_type = $this->input->post('receiver_type');


        $badge = $this->notification_model->getBageCount($receiver_id, $receiver_type);

        $results['count'] = $badge;

        echo json_encode($results);
    }

    public function removeBadge(){
        $receiver_id = $this->input->post('receiver_id');
        $receiver_type = $this->input->post('receiver_type');
        $notification_type = $this->input->post('notification_type');
        $badge_count = $this->input->post('badge_count');

        $cond = [];
        $cond['receiver_type'] = $receiver_type;
        $cond['receiver_id'] = $receiver_id;
        $cond['notification_type'] = $notification_type;

        $data = $this->notification_model->getRecordByCond($cond);

        if (!empty($data)){
            $data['badge_count']=$badge_count;
            $this->notification_model->updateRecord($data, 'id');
        }

        $results['isRemove'] = true;

        echo json_encode($results);
    }
}
?>