<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

header('Access-Control-Allow-Origin: *');
Header('Access-Control-Allow-Headers: *'); //for allow any headers, insecure
Header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');

require_once APPPATH . 'core/WebController.php';

/*
 *
 */

class EparkSync extends WebController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('organ_model');
        $this->load->model('company_model');
        $this->load->model('staff_model');
    }

    public function sendToEpark(){
        $organ_id = $this->input->post('organ_id');
        $from_date = $this->input->post('from_date');
        $to_date = $this->input->post('to_date');

        if (empty($organ_id) || empty($from_date)){
            echo json_encode(['is_load'=>false, 'err' => 'input parameter error!']);
            return;
        }

        if (empty($to_date)) $to_date = $from_date;
        
        $organ = $this->organ_model->getFromId($organ_id);

        $company = $this->company_model->getFromId($organ['company_id']);
        if (!empty($company['is_sync_epark']) && $company['is_sync_epark'] == 1 ){
            $base_url = $company['epark_base_url'];
            $login_id = $company['epark_login_id'];
            $login_pwd = $company['epark_login_pwd'];

            if (empty($base_url) || empty($base_url) || empty($base_url)){
                echo json_encode(['is_load'=>false, 'err' => 'EPARK Parameter Error!']);
                return;
            }

        }else{
            echo json_encode(['is_load'=>false, 'err' => 'Company has not sync flag!']);
            return;
        }


        if (empty($organ['epark_id'])){
            echo json_encode(['is_load'=>false, 'err' => 'Shop SyncID has nothing!']);
            return;
        }

        $this->load->helper('epark');
        $epark_helper = new Epark_helper($organ['company_id']);
        $epark_helper->api_base = $base_url;
        $epark_token = $epark_helper->authenticate($login_id, $login_pwd);
        $epark_helper->loadShopBussinessTime($organ['epark_id']);

        $sel_dateTime = new DateTime($from_date);
        $sel_date = $from_date;
        $add_day_interval = new DateInterval('P1D');

        while($sel_date <= $to_date){
            $shifts = $this->shift_model->getShiftList(['organ_id'=>$organ_id, 'is_enable_apply' => 1,'select_date' => $sel_date]);
            $send_data = [];
            foreach($shifts as $shift){
                $staff_id = $shift['staff_id'];
                $staff = $this->staff_model->getFromId($staff_id);
                if (empty($staff['epark_id'])) continue;
                
                $from = $shift['from_time'];
                $to = $shift['to_time'];
                
                if (empty($send_data[$staff_id])){
                    $tmp = [];
                
                    $tmp['staff_id'] = $staff_id;
                    $tmp['epark_staff_id'] = $staff['epark_id'];
                    $tmp['shift_from'] = $from;
                    $tmp['shift_to'] = $to;
                    $tmp['bookings'] = [];
                
                    $send_data[$staff_id] = $tmp;
                }else{
                    $old_shift_from = $send_data[$staff_id]['shift_from'];
                    $old_shift_to = $send_data[$staff_id]['shift_to'];

                    if ($old_shift_from > $from){
                        $send_data[$staff_id]['shift_from'] = $from;
                    }
                
                    if ($old_shift_to < $to){
                        $send_data[$staff_id]['shift_to'] = $to;
                    }

                    if ($old_shift_from > $to){
                        $tmp_booking = [];
                        $tmp_booking['from_time'] = $to;
                        $tmp_booking['to_time'] = $old_shift_from;
                        $send_data[$staff_id]['bookings'][] = $tmp_booking;
                    }

                    if ($old_shift_to < $from){
                        $tmp_booking = [];
                        $tmp_booking['from_time'] = $old_shift_to;
                        $tmp_booking['to_time'] = $from;
                        $send_data[$staff_id]['bookings'][] = $tmp_booking;
                    }
                }

            }

            $epark_helper->updateEpark($organ['epark_id'], $sel_date, $send_data);
            //print_r($send_data);print_r('<br />');


            
            $sel_dateTime->add($add_day_interval);
            $sel_date = $sel_dateTime->format("Y-m-d");
        }


        echo json_encode(['is_load' => true, 'is_sync' => 'ok']);
        return;
    }

}
?>
