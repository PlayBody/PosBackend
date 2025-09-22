<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . 'core/WebController.php';

/*
 *
 */

class Apievents extends WebController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('event_model');
    }


    public function loadEventDetail(){
        $event_id = $this->input->post('event_id');

        $event = $this->event_model->getFromId($event_id);
        $results['event'] = $event;
        echo json_encode($results);
    }

    public function loadEvents(){
        $condition = $this->input->post('condition');
        $cond = json_decode($condition, true);

        $events = $this->event_model->getEventList($cond);
        $results['events'] = $events;
        echo json_encode($results);
    }

    public function saveEvent(){
        $event_id = $this->input->post('event_id');
        $company_id = $this->input->post('company_id');
        $from_time = $this->input->post('from_time');
        $to_time = $this->input->post('to_time');
        $organ_id = $this->input->post('organ_id');
        $comment = $this->input->post('comment');
        $url = $this->input->post('url');
        $reg_staff_id = $this->input->post('reg_staff_id');

        if (empty($event_id)){
            $data = array(
                'company_id' => $company_id,
                'organ_id' => $organ_id,
                'from_time' => $from_time,
                'to_time' => $to_time,
                'comment' => $comment,
                'event_url' => $url,
                'reg_staff_id' => $reg_staff_id
            );

            $this->event_model->insertRecord($data);
        }else{
            $event = $this->event_model->getFromId($event_id);
            $event['organ_id'] = $organ_id;
            $event['from_time'] = $from_time;
            $event['to_time'] = $to_time;
            $event['comment'] = $comment;
            $event['event_url'] = $url;
            $event['reg_staff_id'] = $reg_staff_id;
            $this->event_model->updateRecord($event);
        }

        $results['isSave'] = true;
        echo json_encode($results);
    }

    public function deleteEvent(){
        $event_id = $this->input->post('event_id');
        $this->event_model->delete_force($event_id, 'id');
        $results['isDelete'] = true;
        echo json_encode($results);
    }
}
?>