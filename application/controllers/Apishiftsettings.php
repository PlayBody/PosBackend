<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . 'core/WebController.php';

/*
 *
 */

class Apishiftsettings extends WebController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('organ_model');
        $this->load->model('setting_init_shift_model');
        $this->load->model('shift_frame_model');
        $this->load->model('staff_model');
        $this->load->model('staff_organ_model');
        $this->load->model('organ_shift_time_model');
    }

    public function importExeclCount(){
        $organ_id = $this->input->post('organ_id');
        $date_month = $this->input->post('date_month');
        $import_data = $this->input->post('import_data');
        if (empty($organ_id)){
            $results['isImport'] = false;
            echo json_encode($results);
            return;
        }

        $olds = $this->shift_frame_model->getListByCond(['organ_id'=>$organ_id, 'date_month'=>$date_month]);
        foreach ($olds as $old){
            $this->shift_frame_model->delete_force($old['id'], 'id');
        }
        $data = json_decode($import_data);

        foreach ($data as $item){
            $shift_counts = array(
                'organ_id' => $organ_id,
                'from_time' => $item->from_time,
                'to_time' => $item->to_time,
                'count' => $item->count,
            );

            $this->shift_frame_model->insertRecord($shift_counts);
        }

        $results['isImport'] = true;

        echo json_encode($results);
    }

    public function loadInitShifts(){
        $condition = $this->input->post('condition');
        $cond = json_decode($condition, true);

        $shifts = $this->setting_init_shift_model->getListByCond($cond);

        $results['shifts'] = $shifts;

        echo(json_encode($results));
    }

    public function loadStatus(){
        $staff_id = $this->input->post('staff_id');
        $pattern = $this->input->post('pattern');
        $organ_id = $this->input->post('organ_id');
        $weekday = $this->input->post('weekday');
        $select_time = $this->input->post('select_time');

        $cond = array();
        $cond['staff_id'] = $staff_id;
        $cond['organ_id'] = $organ_id;
        $cond['select_time'] = $select_time;
        $cond['weekday'] = $weekday;
        $cond['pattern'] = $pattern;

        $shifts = $this->setting_init_shift_model->getListByCond($cond);

        $results = [];
        $results['isLoad'] = true;

        if (empty($shifts)){
            $results['status'] = '0';
        }else{
            $results['status'] = '1';
            $results['shift'] = $shifts[0];
        }

        echo json_encode($results);
        return;
    }

    public function saveShift(){
        $staff_id = $this->input->post('staff_id');
        $organ_id = $this->input->post('organ_id');
        $setting_id = $this->input->post('setting_id');
        $weekday = $this->input->post('weekday');
        $from_time = $this->input->post('from_time');
        $to_time = $this->input->post('to_time');
        $pattern = $this->input->post('pattern');
        $shift_type = $this->input->post('shift_type');
        if ($to_time=='24:00:00')  $to_time =  '23:59:59';

        $shift_times = $this->organ_shift_time_model->getListByCond(['organ_id'=>$organ_id, 'weekday'=>$weekday]);

        $isactive = $this->isActiveTime($shift_times, $from_time, $to_time);

        if (!$isactive){
            $results['isUpdate'] = false;
            $results['err'] = 'active_err';
            echo json_encode($results);
            return;
        }

        $cond = [];
        $cond['staff_id'] = $staff_id;
        $cond['organ_id'] = $organ_id;
        $cond['weekday'] = $weekday;
        $cond['input_time'] = $from_time;
        $cond['pattern'] = $pattern;
        $shifts_from = $this->setting_init_shift_model->getListByCond($cond, $setting_id);
        $cond['input_time'] = $to_time;
        $shifts_to = $this->setting_init_shift_model->getListByCond($cond, $setting_id);

        $cond['input_time'] = '';
        $cond['from_time'] = $from_time;
        $cond['to_time'] =  $to_time;
        $inner_shift = $this->setting_init_shift_model->getListByCond($cond, $setting_id);

        if (!empty($shifts_from) || !empty($shifts_to) || !empty($inner_shift)){
            $results['isUpdate'] = false;
            $results['err'] = 'duplicate_err';
            echo json_encode($results);
            return;
        }

        if (empty($setting_id)){
            $shift = array(
                'staff_id' => $staff_id,
                'organ_id' => $organ_id,
                'weekday' => $weekday,
                'pattern' => $pattern,
                'shift_type' => $shift_type,
                'from_time' => $from_time,
                'to_time' => $to_time,
            );

            $this->setting_init_shift_model->insertRecord($shift);
        }else{
            $shift = $this->setting_init_shift_model->getFromId($setting_id);
            $shift['from_time'] = $from_time;
            $shift['to_time'] = $to_time;
            $shift['shift_type'] = $shift_type;

            $this->setting_init_shift_model->updateRecord($shift);
        }

        $results['isUpdate'] = true;
        echo json_encode($results);
        return;
    }

    public function deleteShift(){
        $setting_id = $this->input->post('setting_id');

        if (empty($setting_id)){
            echo json_encode(['isDelete'=>false]);
            return;
        }

        $this->setting_init_shift_model->delete_force($setting_id, 'id');

        $results['isDelete'] = true;
        echo json_encode($results);
        return;
    }


    public function loadShiftCount(){
        $staff_id = $this->input->post('staff_id');
        $organ_id = $this->input->post('organ_id');
        $from_date = $this->input->post('from_date');
        $to_date = $this->input->post('to_date');

        $results = [];

        if (empty($staff_id)){
            $results['isLoad'] = false;
            echo json_encode($results);
            return;
        }

        $organ_list = $this->getOrgansByStaffPermission($staff_id);

        if (empty($organ_id)) $organ_id = $organ_list[0]['organ_id'];

        $shift_times = $this->organ_shift_time_model->getListByCond(['organ_id'=> $organ_id]);
        $organ = $this->organ_model->getFromId($organ_id);
//
//        if (empty($organ) || empty($staff)){
//            $results['isLoad'] = false;
//            echo json_encode($results);
//            return;
//        }

        $organ_active_start = empty($organ['active_start_time']) ? '00:00' : $organ['active_start_time'];
        $organ_active_end = empty($organ['active_end_time']) ? '23:59' : $organ['active_end_time'];

        $cond = array();
        $cond['organ_id'] = $organ_id;
        $cond['from_time'] = $from_date. ' 00:00:00';
        $cond['to_time'] = $to_date. ' 23:59:59';
        $shift_list = $this->shift_frame_model->getListByCond($cond);


        $results['isLoad'] = true;
        $results['organ_id'] = $organ_id;
        $results['organ_list'] = $organ_list;
        $results['shift_times'] = $shift_times;
        $results['shifts'] = $shift_list;

        $results['active_time']['from'] = $organ_active_start;
        $results['active_time']['to'] = $organ_active_end;

        echo(json_encode($results));
    }

    public function loadCountShiftStatus(){
        $organ_id = $this->input->post('organ_id');
        $select_time = $this->input->post('select_time');

        $cond = array();
        $cond['organ_id'] = $organ_id;
        $cond['select_time'] = $select_time;

        $shifts = $this->shift_frame_model->getListByCond($cond);

        $results = [];
        $results['isLoad'] = true;

        if (empty($shifts)){
            $results['status'] = '0';
        }else{
            $results['status'] = '1';
            $results['shift'] = $shifts[0];
        }

        echo json_encode($results);
        return;
    }

    public function saveShiftCount(){

        $organ_id = $this->input->post('organ_id');
        $setting_id = $this->input->post('setting_id');
        $select_date = $this->input->post('select_date');
        $from_time = $this->input->post('from_time');
        $to_time = $this->input->post('to_time');
        if ($to_time=='24:00:00') $to_time = '23:59:59';
        $count = $this->input->post('count');

        $time = strtotime($select_date);
        $weekday = date('w',$time);

        $shift_times = $this->organ_shift_time_model->getListByCond(['organ_id'=>$organ_id, 'weekday'=>$weekday]);

        $isactive = $this->isActiveTime($shift_times, $from_time, $to_time);

        if (!$isactive){
            $results['isUpdate'] = false;
            $results['err'] = 'active_err';
            echo json_encode($results);
            return;
        }

        $cond = [];
        $cond['organ_id'] = $organ_id;
        $cond['input_time'] = $select_date. ' '. $from_time;
        $shifts_from = $this->shift_frame_model->getListByCond($cond, $setting_id);
        $cond['input_time'] = $select_date. ' '. $to_time;
        $shifts_to = $this->shift_frame_model->getListByCond($cond, $setting_id);

        if (!empty($shifts_from) || !empty($shifts_to)){
            $results['isUpdate'] = false;
            $results['err'] = 'duplicate_err';
            echo json_encode($results);
            return;
        }

        if (empty($setting_id)){
            $shift = array(
                'organ_id' => $organ_id,
                'from_time' => $select_date. ' '. $from_time,
                'to_time' => $select_date. ' '. $to_time,
                'count' => $count,
            );

            $this->shift_frame_model->insertRecord($shift);
        }else{
            $shift = $this->shift_frame_model->getFromId($setting_id);
            $shift['from_time'] = $select_date. ' '. $from_time;
            $shift['to_time'] = $select_date. ' '. $to_time;
            $shift['count'] = $count;

            $this->shift_frame_model->updateRecord($shift, 'id');
        }

        $results['isUpdate'] = true;
        echo json_encode($results);
        return;
    }

    public function deleteShiftCount(){
        $setting_id = $this->input->post('setting_id');

        if (empty($setting_id)){
            echo json_encode(['isDelete'=>false]);
            return;
        }

        $this->shift_frame_model->delete_force($setting_id, 'id');

        $results['isDelete'] = true;
        echo json_encode($results);
        return;
    }

    private function isActiveTime($shift_times, $from_time, $to_time){

        $isActive = false;
        foreach ($shift_times as $record){
            $_start = $record['from_time'];
            $_end = $record['to_time'];
            if ($_start.":00"<=$from_time && $_end.":00">=$to_time){
                $isActive = true;
                break;
            }
        }

        return $isActive;
    }

    public function copyShiftCounts(){
        $organ_id = $this->input->post('organ_id');
        $from_date = $this->input->post('from_date');
        $to_date = $this->input->post('to_date');

        $old_counts = $this->shift_frame_model->getListByCond(['organ_id'=>$organ_id, 'from_time'=>$from_date." 00:00:00", 'to_time' =>$to_date ." 23:59:59"]);

        foreach ($old_counts as $item){
            $this->shift_frame_model->delete_force($item['id'], 'id');
        }

        $from = new DateTime($from_date);
        $to = new DateTime($to_date);

        $diffDay = new DateInterval('P7D');

        $shift_counts = [];
        $cnt = 0;
        while(empty($shift_counts) && $from->format('Y') >= '2021'){
            $cnt++;
            $from->sub($diffDay);
            $to->sub($diffDay);

            $shift_counts = $this->shift_frame_model->getListByCond(['organ_id'=>$organ_id, 'from_time'=>$from->format("Y-m-d 00:00:00"), 'to_time' =>$to->format("Y-m-d 23:59:59")]);
        }

        $diffDay = new DateInterval('P'.(7*$cnt).'D');

        foreach ($shift_counts as $item){
            $from = new DateTime($item['from_time']);
            $to = new DateTime($item['to_time']);

            $from->add($diffDay);
            $to->add($diffDay);

            $shift_count = array(
                'organ_id' => $organ_id,
                'from_time'=> $from->format('Y-m-d H:i:s'),
                'to_time'=> $to->format('Y-m-d H:i:s'),
                'count' => $item['count']
            );

            $this->shift_frame_model->insertRecord($shift_count);
        }

        $results = [];
        $results['isCopy'] = true;

        echo json_encode($results);


    }


    public function saveInitShift(){
        $staff_id = $this->input->post('staff_id');
        $organ_id = $this->input->post('organ_id');
        $setting_id = $this->input->post('setting_id');
        $weekday = $this->input->post('weekday');
        $from_time = $this->input->post('from_time');
        $to_time = $this->input->post('to_time');
        $pattern = $this->input->post('pattern');
        $shift_type = $this->input->post('shift_type');

        $setting = [];
        if(!empty($setting_id)){
            $setting = $this->setting_init_shift_model->getFromId($setting_id);
            $old_type = $setting['shift_type'];
        }

        if (!empty($organ_id)) $setting['organ_id'] = $organ_id;
        if (!empty($staff_id)) $setting['staff_id'] = $staff_id;
        if (!empty($weekday)) $setting['weekday'] = $weekday;
        if (!empty($from_time)) $setting['from_time'] = $from_time;
        if (!empty($to_time)) $setting['to_time'] = $to_time;
        if (!empty($shift_type)) $setting['shift_type'] = $shift_type;
        if (!empty($pattern)) $setting['pattern'] = $pattern;

        if($shift_type==SHIFT_STATUS_REST){
            $shifts = $this->setting_init_shift_model->getListByCond([
                'staff_id'=>$staff_id,
                'organ_id' => $organ_id,
                'weekday' => $weekday,
                'pattern' => $pattern,
            ]);

            if(!empty($shifts)){
                $results['isSave'] = false;
                $results['message'] = 'シフトが存在します。 休みを設定するには、シフトを削除してください。';
                echo json_encode($results);
                return;
            }

            $setting['from_time'] = '00:00:00';
            $setting['to_time'] = '23:59:59';
        }

        $exist_other_shifts = $this->setting_init_shift_model->getListByCond([
            'staff_id'=>$staff_id,
            'organ_id' => $organ_id,
            'in_from_time' => $from_time,
            'in_to_time' => $to_time,
            'weekday' => $weekday,
			'pattern' => $pattern,
            'no_setting' => $setting_id,
        ]);

        if (!empty($exist_other_shifts)){
            $results['isSave'] = false;
            $results['message'] = '入力したシフトが重複しました。時間を確認してください。';
            echo json_encode($results);
            return;
        }


        if (empty($setting_id) || $old_type == SHIFT_STATUS_REST){
            if (!empty($setting_id)){
                $this->setting_init_shift_model->delete_force($setting_id, 'id');
            }
            $setting['id'] = '';
            $this->setting_init_shift_model->insertRecord($setting);
        }else{
            $this->setting_init_shift_model->updateRecord($setting, 'id');
        }

        $results['isSave'] = true;
        echo json_encode($results);
        return;
    }

}
?>
