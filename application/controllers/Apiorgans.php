<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . 'core/WebController.php';

class Apiorgans extends WebController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('staff_model');
        $this->load->model('table_model');
        $this->load->model('organ_model');
        $this->load->model('staff_organ_model');
        $this->load->model('organ_setting_model');
        $this->load->model('organ_time_model');
        $this->load->model('organ_special_time_model');
        $this->load->model('organ_special_shift_time_model');
        $this->load->model('organ_shift_time_model');
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

    public function getOrgans(){
        $company_id = $this->input->post('company_id');
        $cond = [];
        if (empty($comnpany_id)) $cond['company_id'] = $company_id;
        $organs = $this->organ_model->getListByCond($cond);

        $results['organs'] = $organs;
        echo json_encode($results);
    }

    public function getOrganInfoByOrganNumber(){
        $company_id = $this->input->post('company_id');
        $organ_number = $this->input->post('organ_number');
        $cond = [];
        $cond['company_id'] = $company_id;
        $cond['organ_number'] = $organ_number;
        $organ = $this->organ_model->getRecordByCond($cond);

        $results['organ'] = $organ;
        echo json_encode($results);
    }

    public function loadOrganList(){
        $staff_id = $this->input->post('staff_id');
        $company_id = $this->input->post('company_id');

        if (empty($staff_id)){
            $cond['company_id'] = $company_id;
            $organs = $this->organ_model->getListByCond($cond);
        }else{
            $organs = $this->getOrgansByStaffPermission($staff_id);
        }

        $results['isLoad'] = true;
        $results['organs'] = $organs;
        echo(json_encode($results));

    }

    public function loadOrganInfo(){
        $organ_id = $this->input->post('organ_id');
        $organ = $this->organ_model->getFromId($organ_id);

        //$bosses = $this->staff_organ_model->getBossessByOrgan($organ_id);

        $results['isLoad'] = true;
        $results['organ'] = $organ;
        //$results['staffs'] = $bosses;
        echo(json_encode($results));

    }
    public function deleteOrgan(){
        $organ_id = $this->input->post('organ_id');

        if (empty($organ_id)){
            $results['isDelete'] = false;
            echo json_encode($results);
            return;
        }

        $this->organ_model->delete_force($organ_id, 'organ_id');
        $this->staff_organ_model->delete_force($organ_id, 'organ_id');

        $results['isDelete'] = true;
        echo(json_encode($results));

    }

    public function saveOrgan(){
        $company_id = $this->input->post('company_id');
        if (empty($company_id)){
            $results['isSave'] = false;
            echo(json_encode($results));
            return;
        }
        $organ_id = $this->input->post('organ_id');
        $organ_name = $this->input->post('organ_name');

        if (empty($organ_id)){
            $organ['organ_name'] = $organ_name;
            $organ['company_id'] = $company_id;
            $organ['zip_code'] = empty($this->input->post('zip_code')) ? null : $this->input->post('zip_code');
            $organ['address'] = empty($this->input->post('address')) ? null : $this->input->post('address');
            $organ['phone'] = empty($this->input->post('phone')) ? null : $this->input->post('phone');
            $organ['comment'] = empty($this->input->post('comment')) ? null : $this->input->post('comment');
            $organ['image'] = empty($this->input->post('image')) ? null : $this->input->post('image');
            $organ['visible'] = 1;

            $organ_id = $this->organ_model->insertRecord($organ);
        }else{
            $organ = $this->organ_model->getFromId($organ_id);
            $organ['organ_name'] = $organ_name;
            $organ['zip_code'] = empty($this->input->post('zip_code')) ? null : $this->input->post('zip_code');
            $organ['address'] = empty($this->input->post('address')) ? null : $this->input->post('address');
            $organ['phone'] = empty($this->input->post('phone')) ? null : $this->input->post('phone');
            $organ['comment'] = empty($this->input->post('comment')) ? null : $this->input->post('comment');
            $organ['image'] = empty($this->input->post('image')) ? null : $this->input->post('image');

            $this->organ_model->updateRecord($organ, 'organ_id');
        }

        $results['isSave'] = true;
        $results['organ_id'] = $organ_id;

        echo(json_encode($results));

    }


    function uploadPicture() {

        $results = array();

        // user photo
        $image_path = "assets/images/organs/";
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

    public function loadOrganListByStaff(){
        $staff_id = $this->input->post('staff_id');
        if (empty($staff_id)){
            $results['isLoad'] = false;
            echo json_encode($results);
            return;
        }

        $organs = $this->getOrgansByStaffPermission($staff_id);

        $results['isLoad'] = true;
        $results['organs'] = $organs;

        echo json_encode($results);
    }

    public function loadOrganTimes(){
        $organ_id = $this->input->post('organ_id');

        if (empty($organ_id)){
            $results['isLoad'] = false;
            echo json_encode($results);
            return;
        }

        $cond['organ_id'] = $organ_id;
        $data = $this->organ_time_model->getListByCond($cond);

        $results = [];
        $results['isLoad'] = true;
        $results['data'] = $data;

        echo json_encode($results);
    }

    public function loadOrganBusinessTime(){
        $organ_id = $this->input->post('organ_id');

        if (empty($organ_id)){
            $results['isLoad'] = false;
            echo json_encode($results);
            return;
        }

        $data = $this->organ_time_model->getMinMaxTimeByCond(['organ_id'=>$organ_id]);

        $results = [];
        $results['isLoad'] = true;
        $results['data'] = $data;

        echo json_encode($results);
    }


    public function saveOrganTime(){
        $time_id = $this->input->post('time_id');
        $organ_id = $this->input->post('organ_id');
        $week_day = $this->input->post('weekday');
        $from_time  = $this->input->post('from_time');
        $to_time  = $this->input->post('to_time');

        if (empty($time_id)){
            $data = array(
                'organ_id'=>$organ_id,
                'weekday' => $week_day,
                'from_time' => $from_time,
                'to_time'=>$to_time
            );

            $time_id = $this->organ_time_model->insertRecord($data);

        }else{
            $data  = $this->organ_time_model->getFromId($time_id);
            $data['from_time'] = $from_time;
            $data['to_time'] = $to_time;

            $this->organ_time_model->updateRecord($data);
        }

        $results = [];
        $results['isSave'] = true;
        echo json_encode($results);
    }

    public function deleteOrganTime(){
        $time_id = $this->input->post('time_id');

        if (empty($time_id)){
            $results['isDelete'] = false;
            echo json_encode($results);
            return;
        }

        $this->organ_time_model->delete_force($time_id, 'id');

        $results = [];
        $results['isDelete'] = true;
        echo json_encode($results);
        return;
    }

    public function loadOrganShiftTimes(){
        $organ_id = $this->input->post('organ_id');

        if (empty($organ_id)){
            $results['isLoad'] = false;
            echo json_encode($results);
            return;
        }

        $cond['organ_id'] = $organ_id;
        $data = $this->organ_shift_time_model->getListByCond($cond);

        $results = [];
        $results['isLoad'] = true;
        $results['data'] = $data;

        echo json_encode($results);
    }


    public function saveOrganShiftTime(){
        $time_id = $this->input->post('time_id');
        $organ_id = $this->input->post('organ_id');
        $week_day = $this->input->post('weekday');
        $from_time  = $this->input->post('from_time');
        $to_time  = $this->input->post('to_time');

        if (empty($time_id)){
            $data = array(
                'organ_id'=>$organ_id,
                'weekday' => $week_day,
                'from_time' => $from_time,
                'to_time'=>$to_time
            );

            $time_id = $this->organ_shift_time_model->insertRecord($data);
        }else{
            $data  = $this->organ_shift_time_model->getFromId($time_id);
            $data['from_time'] = $from_time;
            $data['to_time'] = $to_time;

            $this->organ_shift_time_model->updateRecord($data);
        }

        $results = [];
        $results['isSave'] = true;
        echo json_encode($results);
    }

    public function deleteOrganShiftTime(){
        $time_id = $this->input->post('time_id');

        if (empty($time_id)){
            $results['isDelete'] = false;
            echo json_encode($results);
            return;
        }

        $this->organ_shift_time_model->delete_force($time_id, 'id');

        $results = [];
        $results['isDelete'] = true;
        echo json_encode($results);
        return;
    }

    public function loadOrganSetTableData(){
        $organ_id = $this->input->post('organ_id');
        $set_number = $this->input->post('set_number');

        $this->load->model('organ_set_table_model');

        $set_data = $this->organ_set_table_model->getRecordTable($organ_id, $set_number);

        $results = [];
        $results['isLoad'] = true;
        $results['set_data'] = $set_data;
        echo json_encode($results);
        return;
    }

    public function loadOrganShiftTime(){
        $organ_id = $this->input->post('organ_id');
        $select_date = $this->input->post('select_date');

        $cond['organ_id'] = $organ_id;
        if (!empty($select_date)){
            $sdate = new DateTime($select_date);
            $cond['weekday'] = $sdate->format('N');
        }
        $data = $this->organ_shift_time_model->getListByCond($cond);

        $results = [];
        $results['isLoad'] = true;
        $results['data'] = $data;

        echo json_encode($results);
    }

    public function loadBusinessTimes(){
        $organ_id = $this->input->post('organ_id');

        if (empty($organ_id)){
            $results['isLoad'] = false;
            echo json_encode($results);
            return;
        }

        $data = $this->organ_time_model->getListByCond(['organ_id'=>$organ_id]);

        $results = [];
        $results['isLoad'] = true;
        $results['times'] = $data;

        echo json_encode($results);
    }

    public function loadShiftTimes(){
        $organ_id = $this->input->post('organ_id');

        if (empty($organ_id)){
            $results['isLoad'] = false;
            echo json_encode($results);
            return;
        }

        $data = $this->organ_shift_time_model->getListByCond(['organ_id'=>$organ_id]);

        $results = [];
        $results['isLoad'] = true;
        $results['times'] = $data;

        echo json_encode($results);
    }


    public function isUseSetInTable(){
        $organ_id = $this->input->post('organ_id');
//
//        $table = $this->table_model->getFromId($table_id);
//
//        if (empty($table_id) || empty($table['organ_id'])){
//            $results['isLoad'] = false;
//            echo json_encode($results);
//            return;
//        }

        $organ = $this->organ_model->getFromId($organ_id);

        $isUse = true;
        if(empty($organ) || empty($organ['is_use_set']) || $organ['is_use_set']=='0') $isUse = false;

        $results = [];
        $results['isLoad'] = true;
        $results['isUse'] = $isUse;

        echo json_encode($results);
    }

    public function loadOrganSpecialTimes(){
        $organ_id = $this->input->post('organ_id');
        $from_time = $this->input->post('from_time');
        $to_time = $this->input->post('to_time');
        $times = $this->organ_special_time_model->getTimeList($organ_id, $from_time, $to_time);

        $results['isLoad'] = true;
        $results['times'] = $times;

        echo json_encode($results);
    }
    public function loadOrganSpecialShiftTimes(){
        $organ_id = $this->input->post('organ_id');
        $from_time = $this->input->post('from_time');
        $to_time = $this->input->post('to_time');
        $times = $this->organ_special_shift_time_model->getTimeList($organ_id, $from_time, $to_time);

        $results['isLoad'] = true;
        $results['times'] = $times;

        echo json_encode($results);
    }

    public function saveOrganSpecialTime(){
        $organ_id = $this->input->post('organ_id');
        $from_time  = $this->input->post('from_time');
        $to_time  = $this->input->post('to_time');

        $data = array(
            'organ_id'=>$organ_id,
            'from_time' => $from_time,
            'to_time'=>$to_time
        );

        $_id = $this->organ_special_time_model->insertRecord($data);

        $shift_time = $this->organ_special_shift_time_model->getFindShiftTimeOfSpecialBusiness($_id);
        if(empty($shift_time)){
            $_calc_from_time = new DateTime($from_time);
            $_calc_from_time->sub(new DateInterval('PT30M'));
            $input_from_time = $_calc_from_time->format('Y-m-d H:i:s');
            if($input_from_time<(substr($from_time, 0, 10).' 00:00:00'))
                $input_from_time = (substr($from_time, 0, 10).' 00:00:00');

			$_calc_to_time = new DateTime($to_time);
            $_calc_to_time->add(new DateInterval('PT30M'));
            $input_to_time = $_calc_to_time->format('Y-m-d H:i:s');
            if($input_to_time>(substr($to_time, 0, 10).' 23:59:59'))
                $input_to_time = (substr($to_time, 0, 10).' 23:59:59');

            $shift_time = array(
                'organ_id' => $organ_id,
                'special_business_id' => $_id,
                'from_time' => $input_from_time,
                'to_time' => $input_to_time
            );
            $this->organ_special_shift_time_model->insertRecord($shift_time);
        }

        $results = [];
        $results['isSave'] = true;
        echo json_encode($results);
    }

    public function saveOrganSpecialShiftTime(){
        $time_id = $this->input->post('time_id');
        $from_time  = $this->input->post('from_time');
        $to_time  = $this->input->post('to_time');

        $shift_time = $this->organ_special_shift_time_model->getFromId($time_id);
        if(!empty($shift_time)){
            $shift_time['from_time'] = $from_time;
            $shift_time['to_time'] = $to_time;

            $this->organ_special_shift_time_model->updateRecord($shift_time, 'id');
        }

        $results = [];
        $results['isSave'] = true;
        echo json_encode($results);
    }
    public function deleteOrganSpecialTime(){
        $time_id = $this->input->post('time_id');

        if (empty($time_id)){
            $results['isDelete'] = false;
            echo json_encode($results);
            return;
        }

        $this->organ_special_time_model->delete_force($time_id, 'organ_special_time_id');
        $this->organ_special_shift_time_model->delete_force($time_id, 'special_business_id');

        $results = [];
        $results['isDelete'] = true;
        echo json_encode($results);
        return;
    }


    public function loadOrganMinAndMaxShiftTime(){
        $organ_id = $this->input->post('organ_id');
        $from_time = $this->input->post('from_time');
        $to_time = $this->input->post('to_time');

        $row = $this->organ_shift_time_model->getOrganMinMaxShiftTime($organ_id, $from_time, $to_time);

        $results = [];
        if (empty($row['min_time']) || empty($row['max_time'])){
            $results['start'] = 0;
            $results['end'] = 24;
        }else{
            $results['start'] = explode(':', $row['min_time'])[0];
            $results['end'] = explode(':', $row['max_time'])[0] + 1 ;
            if ($results['end'] >24) $results['end'] = 24;
        }

        echo json_encode($results);

    }

    public function renderPrintLogo(){

        $organ_id = $this->input->get('organ_id');
        if($organ_id == null)
        {
            $file = 'noImage.jpg';
        }else{
            $organ = $this->organ_model->getFromId($organ_id);
            if (empty($organ) || empty($organ['print_logo_file'])){
                $file = 'noImage.jpg';
            }else{
                $file = $organ['print_logo_file'];
            }
        }
        $file = './assets/images/prints/'.$file;
        if (!is_file($file)) $file = './assets/images/prints/no_image.png';

        header("Content-Type: image/png");
        header("Content-Length: " . filesize($file));
        echo file_get_contents($file);
        exit;
    }

    public function loadOrganOpenCloseHour(){
        $organ_id = $this->input->post('organ_id');
        $select_date = $this->input->post('select_date');
        $timestamp = strtotime($select_date);
        $week_num = date('N', $timestamp);
        $organ_time_row = $this->organ_shift_time_model->getMinMaxTimeByCond([
            'organ_id' => $organ_id,
            'weekday' => $week_num
        ]);

        $open_hour = 8;
        $open_min = 0;
        $close_hour = 22;
        $close_min = 0;
        if (!empty($organ_time_row) && !empty($organ_time_row['from_time'])){
            $open_hour = intval(mb_split(':', $organ_time_row['from_time'])[0]);
            $open_min = intval(mb_split(':', $organ_time_row['from_time'])[1]);
            $close_hour = intval(mb_split(':', $organ_time_row['to_time'])[0]);
            $close_min = intval(mb_split(':', $organ_time_row['to_time'])[1]);
            if ($close_min>0) $close_hour++;
        }
        $result['open_hour'] = $open_hour;
        $result['open_min'] = $open_min;
        $result['close_hour'] = $close_hour;
        $result['close_min'] = $close_min;

        echo json_encode($result);
    }
}
?>
