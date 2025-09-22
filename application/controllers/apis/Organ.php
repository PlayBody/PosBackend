<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

header('Access-Control-Allow-Origin: *');
Header('Access-Control-Allow-Headers: *'); //for allow any headers, insecure
Header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');

require_once APPPATH . 'core/WebController.php';

/*
 *
 */

class Organ extends WebController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('organ_model');
        $this->load->model('organ_time_model');
        $this->load->model('organ_shift_time_model');
        $this->load->model('organ_special_shift_time_model');
        $this->load->model('staff_organ_model');
        $this->load->model('staff_model');
    }

    public function list()
    {
        $company_id = $this->input->post('company_id');
        $staff_id = $this->input->post('staff_id');

        $cond = [];

        // if (!empty($company_id)){
        //     $cond['company_id'] = $company_id;
        // } 
        
        if (!empty($staff_id)){

            $staff = $this->staff_model->get($staff_id);

            // if ($staff['staff_auth'] = STAFF_AUTH_ADMIN){
			// 	$cond['company_id'] = '';
            // }

            if ($staff['staff_auth'] == STAFF_AUTH_OWNER){
                $cond['company_id'] = $staff['company_id'];
            }
        
            if ($staff['staff_auth'] < STAFF_AUTH_OWNER){
                $cond['staff_id'] = $staff_id;
            }
		
        }
        $organ_list = $this->organ_model->getListByCond($cond);
        
        $results['is_load'] = true;
        $results['data'] = $organ_list;

        echo json_encode($results);
    }

    public function loadBussinessTime(){
        $organ_id = $this->input->post('organ_id');
        $from_date  = $this->input->post('from_date');
        $to_date = $this->input->post('to_date');

        $dt_from = new DateTime($from_date);
        $dt_to = new DateTime($to_date);

        $dt_to->add(new DateInterval('P1D'));
        
        $from = $dt_from->format('Y-m-d 00:00:00');
        $to = $dt_to->format('Y-m-d 00:00:00');

        if (empty($organ_id)){
            echo json_encode(['is_load' => false]);
            return;
        }

        $times = $this->organ_shift_time_model->getDataByParam(['organ_id'=>$organ_id]);

        $data = [];
        foreach($times as $time){
            $tmp = [];
            $addDay = new DateInterval('P'.($time['weekday']-1).'D');
            $to_time = $time['to_time']=="24:00" ? '23:59:59' : ($time['to_time'] . ':00');
            
            $f = new DateTime($from_date);
            $f->add($addDay);
            $f_date = $f->format('Y-m-d');
            $tmp['from_time'] = $f_date . ' ' . $time['from_time'] . ':00';
            $tmp['to_time'] = $f_date . ' ' . $to_time;
            $data[] = $tmp;
        }

        $times = $this->organ_special_shift_time_model->getTimes($organ_id, $from, $to);

        foreach($times as $time){
            $tmp = [];
            $tmp['from_time'] = $time['from_time'];
            $tmp['to_time'] = $time['to_time'];
            $data[] = $tmp;
        }


        $results['is_load'] = true;
        $results['data'] = $data;

        echo(json_encode($results));
    }

}
?>
