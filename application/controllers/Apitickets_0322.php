<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . 'core/WebController.php';

/*
 *
 */

class Apitickets extends WebController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('mst_ticket_model');
        $this->load->model('ticket_model');
        $this->load->model('user_ticket_model');
        $this->load->model('ticket_push_setting_model');
    }

    public function loadMasterTicket(){
        $results['ticket_master'] = $this->mst_ticket_model->getListByCond([]);

        echo json_encode($results);
    }
    public function loadTicketList(){
        $company_id = $this->input->post('company_id');

        if (empty($company_id)){
            $results['isLoad'] = false;
            echo json_encode($results);
            return;
        }

        $tickets = $this->ticket_model->getListByCond(['company_id'=>$company_id]);

        $results['isLoad'] = true;
        $results['tickets'] = $tickets;

        echo json_encode($results);
    }

    public function loadTicket(){
        $id = $this->input->post('id');

        if (empty($id)){
            $results['isLoad'] = false;
            echo json_encode($results);
            return;
        }

        $ticket = $this->ticket_model->getFromId($id);

        $results['isLoad'] = true;
        $results['ticket'] = $ticket;

        echo json_encode($results);
    }

    public function saveTicket(){
        $id = $this->input->post('id');
        $ticket_id = $this->input->post('ticket_id');
        $company_id = $this->input->post('company_id');

        $ticket_title = $this->input->post('ticket_title');
        $ticket_detail = $this->input->post('ticket_detail');
        $ticket_price = $this->input->post('price');
        $ticket_cost = $this->input->post('cost');
        $ticket_tax = $this->input->post('tax');
        $ticket_count = $this->input->post('ticket_count');
        $ticket_image_url = $this->input->post('ticket_image');
        $ticket_disamount = $this->input->post('disamount');

        $is_period  = $this->input->post('is_period');
        $period_month = $this->input->post('period_month');
        if (empty($period_month))  $period_month = null;

        $price02 = 0;
        if (intval($ticket_count)>0){
            $tax_rate = 1;
            $one_price = $ticket_price/$ticket_count;
            if (!empty($ticket_tax)) $tax_rate+= $ticket_tax/100;
            $price02 = intval($one_price*$tax_rate);
        }

        if (empty($id)){
            $ticket = array(
                'ticket_id' => $ticket_id,
//                'ticket_title' => $ticket_title,
                'company_id' => $company_id,
                'ticket_title' => $ticket_title,
                'ticket_detail' => $ticket_detail,
                'ticket_image' => empty($ticket_image_url) ? null : $ticket_image_url,
                'ticket_price' => $ticket_price,
                'ticket_price02' => $price02,
                'ticket_cost' => $ticket_cost,
                'ticket_tax' => $ticket_tax,
                'ticket_disamount' => $ticket_disamount,
                'ticket_count' => $ticket_count,
                'is_period' => $is_period,
                'period_month' => $period_month,
            );

            $this->ticket_model->insertRecord($ticket);
        }else{
            $ticket = $this->ticket_model->getFromId($id);
            $ticket['ticket_id'] = $ticket_id;
            $ticket['ticket_title'] = $ticket_title;
            $ticket['ticket_detail'] = $ticket_detail;
            if (!empty($ticket_image_url))  $ticket['ticket_image']= $ticket_image_url;
            $ticket['ticket_price'] = $ticket_price;
            $ticket['ticket_price02'] = $price02;
            $ticket['ticket_cost'] = $ticket_cost;
            $ticket['ticket_tax'] = $ticket_tax;
            $ticket['ticket_disamount'] = $ticket_disamount;
            $ticket['ticket_count'] = $ticket_count;
            $ticket['is_period'] = $is_period;
            $ticket['period_month'] = $period_month;

            $this->ticket_model->updateRecord($ticket, 'id');
        }

        $results['isSave'] = true;

        echo json_encode($results);
    }

    public function deleteTicket(){
        $ticket_id = $this->input->post('ticket_id');

        if (empty($ticket_id)){
            $results['isDelete'] = false;
            echo json_encode($results);
            return;
        }
        $this->ticket_model->delete_force($ticket_id, 'id');
        $results['isDelete'] = true;

        echo json_encode($results);
    }

    public function loadUserTickets(){
        $company_id = $this->input->post('company_id');
        $user_id = $this->input->post('user_id');

        $tickets = $this->ticket_model->getListByCond(['company_id'=>$company_id]);

        $user_tickets = [];
        foreach ($tickets as $ticket) {
            $tmp = [];
            $tmp['ticket_id'] = $ticket['id'];
            $tmp['ticket_name'] = $ticket['ticket_name'];
            $tmp['ticket_title'] = $ticket['ticket_title'];
            $tmp['ticket_price'] = $ticket['ticket_price'];
            $tmp['ticket_price02'] = $ticket['ticket_price02'];
            $tmp['ticket_cost'] = $ticket['ticket_cost'];
            $tmp['ticket_tax'] = $ticket['ticket_tax'];
            $tmp['add_count'] = $ticket['ticket_count'];
            $tmp['user_id'] = $user_id;

            $uTicket = $this->user_ticket_model->getUserTicket(['ticket_id'=>$ticket['id'], 'user_id'=>$user_id]);
            $tmp['id'] = empty($uTicket['id']) ? '' : $uTicket['id'];
            $tmp['count'] = empty($uTicket['count']) ? 0 : $uTicket['count'];
            $tmp['is_reset'] = empty($uTicket['is_reset']) ? 0 : $uTicket['is_reset'];
            $tmp['reset_time_type'] = empty($uTicket['reset_time_type']) ? 0 : $uTicket['reset_time_type'];
            $tmp['reset_time_value'] = empty($uTicket['reset_time_value']) ? 0 : $uTicket['reset_time_value'];
            $tmp['reset_count'] = empty($uTicket['reset_count']) ? 0 : $uTicket['reset_count'];

            $user_tickets[] = $tmp;
        }

        $results['tickets'] = $user_tickets;

        echo json_encode($results);
    }


    function uploadPhoto() {

        $results = array();

        // user photo
        $image_path = "assets/images/tickets/";
        if(!is_dir($image_path)) {
            mkdir($image_path);
        }
        $image_url  = base_url().$image_path;
        $fileName = $_FILES['picture']['name'];
        $config = array(
            'upload_path'   => $image_path,
            'allowed_types' => '*',
            'overwrite'     => 1,
            'file_name' 	=> $fileName
        );
        $this->load->library('upload', $config);
        $this->upload->initialize($config);

        $results['isUpload'] = false;
        if (!empty($_FILES['picture']['name'])) {
            if ($this->upload->do_upload('picture')) {
                $file_url = $image_url.$this->upload->file_name;
                $results['isUpload'] = true;
                $results['picture'] = $file_url;
            }
        }

        echo json_encode($results);

    }


    public function updateMaster(){
        $master_id = $this->input->post('master_id');
        $title = $this->input->post('title');
        if(empty($master_id)){
            $data = array('ticket_name'=>$title);
            $this->mst_ticket_model->insertRecord($data);
        }else{
            $data = $this->mst_ticket_model->getFromId($master_id);
            $data['ticket_name'] = $title;
            $this->mst_ticket_model->updateRecord($data, 'id');
        }
        $results['isUpdate'] = true;
        echo json_encode($results);

    }
    public function deleteMaster(){
        $master_id = $this->input->post('master_id');
        if(empty($master_id)){
            $results['isDelete'] = false;
        }else{
            $this->mst_ticket_model->delete_force($master_id, 'id');
            $results['isDelete'] = true;
        }

        echo json_encode($results);

    }

    public function savePushSetting(){
        $ticket_id = $this->input->post('ticket_id');
        $before_day = $this->input->post('before_day');
        $push_time = $this->input->post('push_time');

        $data = array(
            'ticket_id' => $ticket_id,
            'before_day'=>$before_day,
            'push_time'=> $push_time
        );
        $this->ticket_push_setting_model->insertRecord($data);

        $results['isSave'] = true;
        echo json_encode($results);

    }

    public function loadTicketResetPushSettings(){
        $ticket_id = $this->input->post('ticket_id');
        $settings = $this->ticket_push_setting_model->getSettingList($ticket_id);
        $results['isLoad'] = true;
        $results['settings'] = $settings;

        echo json_encode($results);
    }

    public function deleteTicketResetPushSettings(){
        $setting_id = $this->input->post('setting_id');
        $this->ticket_push_setting_model->delete_force($setting_id, 'id');
        $results['isDelete'] = true;

        echo json_encode($results);
    }
}
?>