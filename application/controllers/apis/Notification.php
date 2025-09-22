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
        $this->load->model('order_model');
    }

    public function loadBadgeCounts(){
        $receiver_id = $this->input->get_post('receiver_id');
        $receiver_type = $this->input->get_post('receiver_type');
        $in_type = $this->input->get_post('in_type');

        // $count = $this->notification_model->getBageCountByCond([
        //     'receiver_id' => $receiver_id,
        //     'receiver_type' => $receiver_type,
        //     'in_type' => $in_type,
        // ]);

        $notifications = $this->notification_model->getBageCountByCondArray([
            'receiver_id' => $receiver_id,
            'receiver_type' => $receiver_type,
            'in_type' => $in_type,
        ]);

        if (empty($notifications)){
            echo json_encode(['count' => 0]);
            return;
        }

        $badge = 0;
        $userIds = array();
        foreach ($notifications as $notification){
            $badge = $badge + $notification['badge_count'];
            if (!empty($notification['order_id'])) {
                $orderData = $this->order_model->getFromId($notification['order_id']);
                if (!empty($orderData) && $orderData['to_time'] > date('Y-m-d H:i:s')) {                    
                    $userIds[] = $orderData['user_id'];
                }
            }
        }

        echo json_encode(['count' => $badge, 'userIds' => $userIds]);
    }

}
?>
