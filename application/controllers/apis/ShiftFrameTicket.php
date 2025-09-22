<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

header('Access-Control-Allow-Origin: *');
Header('Access-Control-Allow-Headers: *'); //for allow any headers, insecure
Header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');

require_once APPPATH . 'core/WebController.php';

/*
 *
 */

class ShiftFrameTicket extends WebController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('shift_frame_ticket_model');
    }

    public function loadShiftFrameTickets(){
        $shift_frame_id = $this->input->post('shift_frame_id');

		$tickets = $this->shift_frame_ticket_model->getListData(['shift_frame_id' => $shift_frame_id]);

        $results['is_result'] = true;
        $results['data'] = $tickets;
        echo json_encode($results);
    }

}
?>
