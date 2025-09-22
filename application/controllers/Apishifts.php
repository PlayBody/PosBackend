<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . 'core/WebController.php';

/*
 *
 */

class Apishifts extends WebController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('organ_model');
        $this->load->model('shift_model');
        $this->load->model('setting_init_shift_model');
        $this->load->model('shift_frame_model');
        $this->load->model('staff_model');
        $this->load->model('staff_organ_model');
        $this->load->model('organ_shift_time_model');
        $this->load->model('shift_lock_model');
        $this->load->model('reserve_model');
        $this->load->model('reserve_menu_model');
		$this->load->model('notification_text_model');
    }

    public function getShiftCounts(){
        $organ_id = $this->input->post('organ_id');
        $from_time = $this->input->post('from_time');
        $to_time = $this->input->post('to_time');

        $cond = [];
        $cond['organ_id'] = $organ_id;
        $cond['from_time'] = $from_time;
        $cond['to_time'] = $to_time;
        $shift_counts = $this->shift_frame_model->getListByCond($cond);

        $results['counts'] = $shift_counts;

        echo json_encode($results);
    }

    public function getStaffShifts(){
        $staff_id = $this->input->post('staff_id');
        $organ_id = $this->input->post('organ_id');
        $from_time = $this->input->post('from_time');
        $to_time = $this->input->post('to_time');
        $mode =  $this->input->post('mode');
        $pattern = $this->input->post('pattern');


        $cond['organ_id'] = $organ_id;
        $cond['staff_id'] = $staff_id;
        $cond['from_time'] = $from_time;
        $cond['to_time'] = $to_time;

        $shifts = $this->shift_model->getListByCond($cond);

        if ($mode == 'init'){
            $pattern = empty($pattern) ? 1 : $pattern;
            foreach ($shifts as $item) {
                $this->shift_model->delete_force($item['shift_id'], 'shift_id');
            }

            $condInit['staff_id'] = $staff_id;
            $condInit['organ_id'] = $organ_id;
            $condInit['pattern'] = $pattern;
            $initData = $this->setting_init_shift_model->getListByCond($condInit);
            foreach ($initData as $item){
                $diff1Day = new DateInterval('P'.($item['weekday']-1).'D');
                $curDateTime = new DateTime($from_time);
                $curDateTime->add($diff1Day);
                $sel_date = $curDateTime->format("Y-m-d");

                $condR = [];
                $condR['organ_id'] = $organ_id;
                $condR['from_time'] = $sel_date . ' 00:00:00';
                $condR['to_time'] = $sel_date . ' 23:59:59';

                $shift_counts = $this->shift_frame_model->getListByCond($condR);
                $item_from = $sel_date . ' ' . $item['from_time'];
                $item_to = $sel_date . ' ' . $item['to_time'];
                foreach ($shift_counts as $count){
                    if ($item_from<$count['to_time'] && $item_to>$count['from_time']){

                        $_from  = $item_from>=$count['from_time'] ? $item_from : $count['from_time'];
                        $_to  = $item_to<=$count['to_time'] ? $item_to : $count['to_time'];
                        $add_shift = array(
                            'from_time'=> $_from,// $input_from,
                            'to_time' => $_to, //$input_to,
                            'staff_id' => $staff_id,
                            'organ_id' => $organ_id,
                            'visible' => 1,
                            'shift_type' => empty($item['shift_type']) ? 1 : $item['shift_type'],
                        );

                        $this->shift_model->insertRecord($add_shift);
                    }
                }

            }

            $shifts = $this->shift_model->getListByCond($cond);
        }

        $results['shifts'] = $shifts;

        echo json_encode($results);
    }

    public function getStaffReserves(){
        $staff_id = $this->input->post('staff_id');
        $organ_id = $this->input->post('organ_id');
        $from_time = $this->input->post('from_time');
        $to_time = $this->input->post('to_time');

        $staff = $this->staff_model->getFromId($staff_id);

        $reserves = $this->reserve_model->getReserveList(['organ_id'=>$organ_id, 'staff_id'=>$staff_id, 'from_time'=>$from_time, 'to_time'=>$to_time, 'max_status'=>'2']);

        $data = [];
        foreach ($reserves as $reserve){
            $tmp = $reserve;
            $tmp['menus'] = $this->reserve_menu_model->getReserveMenuList($reserve['reserve_id']);
            $data[] = $tmp;
        }

        $results['isLoad'] = true;

        $results['reserves'] = $data;

        echo(json_encode($results));
    }


    public function getActiveShifts(){
        $organ_id = $this->input->post('organ_id');
        $shift_times = $this->organ_shift_time_model->getListByCond(['organ_id'=>$organ_id]);
        $results['shift_times'] = $shift_times;
        echo(json_encode($results));
    }

    public function loadShiftStatus(){
        $staff_id = $this->input->post('staff_id');
        $organ_id = $this->input->post('organ_id');
        $select_datetime = $this->input->post('select_datetime');

        $cond = array();
        $cond['staff_id'] = $staff_id;
        $cond['organ_id'] = $organ_id;
        $cond['select_datetime'] = $select_datetime;
        $shifts = $this->shift_model->getListByCond($cond);

        $results = [];
        $results['isLoad'] = true;

        if (empty($shifts)){
            $results['status'] = '0';
            $shift_count = $this->shift_frame_model->getListByCond(['organ_id'=>$organ_id, 'select_time'=>$select_datetime]);
            if (!empty($shift_count)){
                $results['count_shift'] = $shift_count[0];
            }

        }else{
            $results['shift'] = $shifts[0];
        }

        echo json_encode($results);
        return;
    }

    public function submitShift(){

        $shift_id = $this->input->post('shift_id');
        $staff_id = $this->input->post('staff_id');
        $organ_id = $this->input->post('organ_id');
        $from_time = $this->input->post('from_time');
        $to_time = $this->input->post('to_time');
        $shift_type = $this->input->post('shift_type');

        if($shift_type=='6'){
            $this->shift_model->deleteDayShift(substr($from_time, 0,10), $staff_id, $organ_id);
            $shift = array(
                'staff_id' => $staff_id,
                'organ_id' => $organ_id,
                'from_time' => $from_time,
                'to_time' => $to_time,
                'shift_type' => $shift_type,
                'visible' => 1,
            );
            $this->shift_model->insertRecord($shift);
            $results['isUpdate'] = true;
            echo json_encode($results);
            return;

        }else{
            $this->shift_model->deleteDayShift(substr($from_time, 0,10), $staff_id, $organ_id, 6);
        }

        $shift_exist = $this->shift_model->isExist($organ_id, $staff_id, $shift_id, $from_time, $to_time);
        if ($shift_exist){
            $results['isUpdate'] = false;
            $results['msg'] = 'exist_error';
            echo json_encode($results);
            return;
        }

        $shift_area = $this->shift_frame_model->getListByCond(['organ_id'=>$organ_id,
            'select_date'=>date('Y-m-d', strtotime($from_time))]);


        if (empty($shift_area)){
            $results['isUpdate'] = false;
            $results['msg'] = 'area_error';
            echo json_encode($results);
            return;
        }

        $is_add = false;
        foreach ($shift_area as $record){
            $_start = $record['from_time'];
            $_end = $record['to_time'];
            $from = $from_time;
            $to = $to_time;

            if ($from>=$_end || $to<=$_start) continue;
            if ($from>$_start) $input_from = $from; else $input_from = $_start;
            if ($to>$_end) $input_to = $_end; else $input_to = $to;

            $shift = array(
                'staff_id' => $staff_id,
                'organ_id' => $organ_id,
                'from_time' => $input_from,
                'to_time' => $input_to,
                'shift_type' => $shift_type,
                'visible' => 1,
            );
            $is_add = true;
            $this->shift_model->insertRecord($shift);
        }

        if (!$is_add){
            $results['isUpdate'] = false;
            $results['msg'] = 'area_error';
            echo json_encode($results);
            return;
        }

        if (!empty($shift_id)){
            $this->shift_model->delete_force($shift_id, 'shift_id');
        }

//
//        if (empty($shift_id)){
//            $shift = array(
//                'staff_id' => $staff_id,
//                'organ_id' => $organ_id,
//                'from_time' => $from_time,
//                'to_time' => $to_time,
//                'shift_type' => 1,
//                'visible' => 1,
//            );
//            $shift_id = $this->shift_model->insertRecord($shift);
//        }else{
//            $shift = $this->shift_model->getFromId($shift_id);
//
//            $shift['from_time'] = $from_time;
//            $shift['to_time'] = $to_time;
//            $shift['shift_type'] = 1;
//
//            $this->shift_model->updateRecord($shift, 'shift_id');
//        }

//        $prev_shift = $this->shift_model->getReleationShift($organ_id, $staff_id, $from_time, 'prev');
//        if (!empty($prev_shift)){
//            $shift['shift_id'] = $shift_id;
//            $shift['from_time'] = $prev_shift['from_time'];
//            $this->shift_model->updateRecord($shift, 'shift_id');
//            $this->shift_model->delete_force($prev_shift['shift_id'], 'shift_id');
//        }
//
//        $next_shift = $this->shift_model->getReleationShift($organ_id, $staff_id, $to_time, 'next');
//        if (!empty($next_shift)){
//            $shift['shift_id'] = $shift_id;
//            $shift['to_time'] = $next_shift['to_time'];
//            $this->shift_model->updateRecord($shift, 'shift_id');
//            $this->shift_model->delete_force($next_shift['shift_id'], 'shift_id');
//        }

        $results=[];
//        if ($shift['shift_type']=='1'){
//            $results['isSend'] = $this->sendNotificationToStaffShiftRequest($organ_id, $staff_id,  $shift['from_time'], $shift['to_time']);
//        }
        $results['isUpdate'] = true;
        echo json_encode($results);
        return;
    }


    public function actionStaffShift(){
        $shift_id = $this->input->post('shift_id');
        $status = $this->input->post('status');

        $results=[];

        if (empty($shift_id)){
            $results['isUpdate'] = false;
            echo json_encode($results);
            return;
        }

        $shift = $this->shift_model->getFromId($shift_id);

        if (empty($shift)){
            $results['isUpdate'] = false;
            echo json_encode($results);
            return;
        }

        $shift['shift_type'] = $status;

        $this->shift_model->updateRecord($shift, 'shift_id');


        $results['isUpdate'] = true;
        echo json_encode($results);
        return;

    }

    public function loadShiftManage(){
        $organ_id = $this->input->post('organ_id');
        $from_time = $this->input->post('from_time');
        $to_time = $this->input->post('to_time');

        $counts = $this->shift_frame_model->getListByCond(['organ_id' => $organ_id, 'from_time' => $from_time, 'to_time' => $to_time]);
        $times = [];

        foreach($counts as $count){
            if(!array_search($count['from_time'], $times) && (empty($times) || $times[0]!=$count['from_time'])) $times[] = $count['from_time'];
            if(!array_search($count['to_time'], $times) && (empty($times) || $times[0]!=$count['to_time'])) $times[] = $count['to_time'];
        }

        $shifts = $this->shift_model->getListByCond(['organ_id' => $organ_id, 'from_time' => $from_time, 'to_time' => $to_time, 'is_apply_enable' => '1']);

        foreach($shifts as $shift){
            if(!array_search($shift['from_time'], $times) && (empty($times) || $times[0]!=$shift['from_time'])) $times[] = $shift['from_time'];
            if(!array_search($shift['to_time'], $times) && (empty($times) || $times[0]!=$shift['to_time'])) $times[] = $shift['to_time'];
        }

        sort($times);

        $data = [];
        for($i=0; $i<count($times)-1; $i++){
            $tmp = [];
            $tmp['from_time'] = $times[$i];
            $tmp['to_time'] = $times[$i+1];
            $_counts = $this->shift_frame_model->getListByCond(['organ_id' => $organ_id, 'in_from_time' => $times[$i], 'in_to_time' => $times[$i+1]]);
            $tmp['count'] = empty($_counts) ? 0 : $_counts[0]['count'];

            $_applies = $this->shift_model->getListByCond(['organ_id' => $organ_id, 'in_from_time' => $times[$i], 'in_to_time' => $times[$i+1], 'is_apply' => '1']);
            $tmp['apply'] = empty($_applies) ? 0 : count($_applies);

            $_shifts = $this->shift_model->getListByCond(['organ_id' => $organ_id, 'in_from_time' => $times[$i], 'in_to_time' => $times[$i+1], 'is_apply_enable' => '1']);
            $tmp['shift'] = empty($_shifts) ? 0 : count($_shifts);

            if(!empty($_shifts))  $tmp['shifts'] = $_shifts;
            if($tmp['count']==0 && $tmp['shift']==0) continue;

            $data[] = $tmp;
        }

        $results['data'] = $data;

        echo json_encode($results);

//
//        $staff = $this->staff_model->getFromId($staff_id);
//
//        if ($staff['staff_auth']>3){
//            $cond = [];
//            if ($staff['staff_auth']<5) $cond['company_id'] = $staff['company_id'];
//            $organ_list = $this->organ_model->getListByCond($cond);
//        }else{
//            $organ_list = $this->staff_organ_model->getOrgansByStaff($staff_id);
//        }
//
//        if (empty($organ_id)) $organ_id=$organ_list[0]['organ_id'];
//
//        $organ = $this->organ_model->getFromId($organ_id);
//
//        $shift_times = $this->organ_shift_time_model->getListByCond(['organ_id'=>$organ_id]);
//        $organ_active_start = empty($organ['active_start_time']) ? '00:00' : $organ['active_start_time'];
//        $organ_active_end = empty($organ['active_end_time']) ? '23:59' : $organ['active_end_time'];
//
//        $shift_inactive = [];
//        $cur_date = $from_date;
//
//        $divide_shift = [];
//        while($cur_date<=$to_date){
//            $area_list = $this->shift_frame_model->getListByCond(['organ_id'=>$organ_id, 'from_time'=>$cur_date. ' 00:00:00', 'to_time'=>$cur_date. ' 23:59:59']);
//
//            foreach ($area_list as $area){
//                $divide_list = $this->shift_model->getDivideShifts($area['from_time'], $area['to_time'], $organ_id);
//
//                $old_time ='';
//                foreach ($divide_list as $item){
//                    if ($old_time==''){
//                        $old_time = $item['time'];
//                    }else if($item['time']==$old_time){
//                        continue;
//                    }else{
//                        $tmp = [];
//                        $tmp['from'] = $old_time;
//                        $tmp['to'] = $item['time'];
//                        $tmp['count'] = $area['count'];
//
//                        $tmp['exist_count'] = $this->shift_model->getExistCount(['organ_id'=>$organ_id, 'from_time'=>$tmp['from'], 'to_time'=>$tmp['to']]);
//
//                        $divide_shift[] = $tmp;
//                        $old_time = $item['time'];
//                    }
//
//                }
//
//            }
//
//
//            $diff1Day = new DateInterval('P1D');
//
//            $curDateTime = new DateTime($cur_date);
//
//            $curDateTime->add($diff1Day);
//            $cur_date = $curDateTime->format("Y-m-d");
//        }
//
//        $shift_counts = $this->shift_frame_model->getListByCond(['organ_id'=>$organ_id, 'from_time'=>$from_date. ' 00:00:00', 'to_time'=>$to_date. ' 23:59:59']);
//        $shifts = $this->shift_model->getListByCond(['organ_id'=>$organ_id, 'from_time'=>$from_date. ' 00:00:00', 'to_time'=>$to_date. ' 23:59:59']);
//
//        $results['isLoad'] = true;
//        $results['active_time']['from'] = $organ_active_start;
//        $results['active_time']['to'] = $organ_active_end;
//
//        $results['shift_counts'] = $shift_counts;
//        $results['shifts'] = $shifts;
//        $results['divide_shift'] = $divide_shift;
//        $results['shift_times'] = $shift_times;
//        $results['organ_list'] = $organ_list;
//        $results['shift_inactive'] = $shift_inactive;
//
//        echo json_encode($results);

    }

    public function loadStaffManageStatus(){
        $organ_id = $this->input->post('organ_id');

        $staffs = $this->staff_organ_model->getStaffsByOrgan($organ_id, 5);

        $results['isLoad'] = true;
        $results['staffs'] = $staffs;

        echo json_encode($results);

    }

    public function saveShiftComplete(){
        $cur_staff_id = $this->input->post('cur_staff_id');
        $organ_id = $this->input->post('organ_id');
        $staff_id = $this->input->post('staff_id');
        $shift_id = $this->input->post('shift_id');
        $shift_type = $this->input->post('shift_type');
        $from_time = $this->input->post('from_time');
        $to_time = $this->input->post('to_time');

        $update_value = '4';
        if ($shift_type=='1') $update_value = '2';
        if ($shift_type=='3') $update_value = '2';
        if ($shift_type=='-4') $update_value = '-3';

        if (empty($shift_id)){
            $shift = array(
                'organ_id' => $organ_id,
                'staff_id' => $staff_id,
                'shift_type' => $update_value,
                'from_time' => $from_time,
                'to_time' => $to_time,
                'visible' => '1'
            );
            $this->shift_model->insertRecord($shift);
        }else{
            $shift = $this->shift_model->getFromId($shift_id);
            $shift['shift_type'] = $update_value;
            $this->shift_model->updateRecord($shift, 'shift_id');
        }

        if ($staff_id!=$cur_staff_id){
            if ($update_value==2){
                $this->sendNotificationToStaffShiftRequest($organ_id, $cur_staff_id, $staff_id, $from_time, $to_time, $update_value);
            }
        }

        $results['isSave'] = true;
        echo json_encode($results);

    }

    private function sendNotificationToStaffShiftRequest($organ_id, $sender_id, $receiver_id, $from_time, $to_time, $update_value){
        $strMsg = $update_value==4 ? '出勤要請が入りました' : '出勤が承認されました。';
        $curstaff = $this->staff_model->getFromId($sender_id);
        $title = ($curstaff['staff_nick'] == null ? ($curstaff['staff_first_name'] . ' ' . $curstaff['staff_last_name']) : $curstaff['staff_nick']) .  '様から'.$strMsg;


        $fdate = new DateTime($from_time);
        $tdate = new DateTime($to_time);

        $content = $fdate->format('n月j日 H時i分').'から'. $tdate->format('H時i分') . 'まで'.$strMsg;

        $is_fcm = $this->sendNotifications($update_value==4 ? 'shift_request' : 'shift_accept', $title, $content, $receiver_id, $sender_id, '1');

        return true;

    }

    private function isCountTime($count_times, $sel_date, $from_time, $to_time){

        $isActive = false;
        foreach ($count_times as $record){
            $_start = $record['from_time'];
            $_end = $record['to_time'];
            $from = $sel_date . ' ' . $from_time;
            $to = $sel_date . ' ' . $to_time;
            if ($_start<=$from && $_end>=$to){
                $isActive = true;
                break;
            }
        }

        return $isActive;
    }

    public function loadOtherOrganExist(){
        $staff_id = $this->input->get('staff_id');
        $cur_organ_id = $this->input->get('cur_organ_id');
        $from_time = $this->input->get('from_time');
        $to_time = $this->input->get('to_time');

        $cond['staff_id'] = $staff_id;
        $cond['cur_organ_id'] = $cur_organ_id;
        $cond['from_time'] = $from_time;
        $cond['to_time'] = $to_time;

        $all_time = $this->shift_model->getOtherOrgansShift($cond);

        $results['all_time'] = $all_time;

        echo json_encode($results);

    }

    public function loadLockStatus(){
        $organ_id = $this->input->post('organ_id');
        $from_time = $this->input->post('from_time');
        $to_time = $this->input->post('to_time');

        $cond['organ_id'] = $organ_id;
        $cond['from_time'] = $from_time;
        $cond['to_time'] = $to_time;

        $lock = $this->shift_lock_model->getLockRecord($cond);
        $is_lock = false;
        if (!empty($lock)){
            $is_lock = $lock['lock_status'] == 1 ? true : false;
        }

        $results['isLoad'] = true;
        $results['is_lock'] = $is_lock;
        echo json_encode($results);
    }
    public function updateLockStatus(){
        $organ_id = $this->input->post('organ_id');
        $from_time = $this->input->post('from_time');
        $to_time = $this->input->post('to_time');
        $lock_status = $this->input->post('lock_status');

        $cond['organ_id'] = $organ_id;
        $cond['from_time'] = $from_time;
        $cond['to_time'] = $to_time;

        $lock = $this->shift_lock_model->getLockRecord($cond);

        if (empty($lock)){
            $lock = array(
                'organ_id' => $organ_id,
                'from_time' => $from_time,
                'to_time' => $to_time,
                'lock_status' => $lock_status
            );
            $this->shift_lock_model->insertRecord($lock);
        }else{
            $lock['lock_status'] = $lock_status;
            $this->shift_lock_model->updateRecord($lock, 'shift_lock_id');
        }

        $results['isSave'] = true;
        echo json_encode($results);
    }

    public function sendNotificationToStaffInputRequest(){
        $organ_id = $this->input->post('organ_id');
        $staff_id = $this->input->post('staff_id');
        $from_time = $this->input->post('from_time');
        $to_time = $this->input->post('to_time');

        $fdate = new DateTime($from_time);
        $tdate = new DateTime($to_time);
        $sender = $this->staff_model->getFromId($staff_id);

        $this->load->model('notification_text_model');
        $text_data = $this->notification_text_model->getRecordByCond(['company_id'=>$sender['company_id'], 'mail_type'=>'12']);
        $title = empty($text_data['title']) ? 'タイトルなし' : $text_data['title'];
        $content = empty($text_data['content']) ? '' : $text_data['content'];
        $content = str_replace('$from_month', $fdate->format('n'), $content);
        $content = str_replace('$from_day', $fdate->format('j'), $content);
        $content = str_replace('$to_month', $tdate->format('n'), $content);
        $content = str_replace('$to_day', $tdate->format('j'), $content);

        //$staffs = $this->staff_organ_model->getStaffsByOrgan($organ_id, STAFF_AUTH_ADMIN, false, true);
        $datas = $this->shift_model->getRequestStaffs($organ_id, $from_time. ' 00:00:00', $to_time. ' 23:59:59');

        foreach ($datas as $data){
            if ($staff_id == $data['staff_id']) continue;
            $content = str_replace('$hope_time', $data['time'], $content);
            $is_fcm = $this->sendNotifications('12', $title, $content, $staff_id, $data['staff_id'], '1');
        }

        echo json_encode(['isSend'=>true]);

    }

    public function loadDailyDetail(){
        $organ_id = $this->input->post('organ_id');
        $select_date = $this->input->post('select_date');
        $shifts = $this->shift_model->getDayShift($organ_id, $select_date);

        $results['shifts'] = $shifts;

        echo json_encode($results);
    }

    public function applyOrRejectRequestShift   (){
        $shift_id = $this->input->post('shift_id');
        $from_time = $this->input->post('from_time');
        $to_time = $this->input->post('to_time');
        $update_shift_type = $this->input->post('update_shift_type');

        $shift = $this->shift_model->getFromId($shift_id);

        if (empty($shift)){
            $results['isUpdate'] = false;
            echo json_encode($results);
            return;
        }

        $shift['shift_type'] = $update_shift_type;
        $shift['from_time'] = $from_time;
        $shift['to_time'] = $to_time;

        $this->shift_model->updateRecord($shift, 'shift_id');

        $results['isUpdate'] = true;

        echo json_encode($results);
    }

    public function updateShiftStatus(){
        $shift_id = $this->input->post('shift_id');
        $status = $this->input->post('status');

        $shift = $this->shift_model->getFromId($shift_id);

        if (empty($shift)){
            $results['isUpdate'] = false;
            echo json_encode($results);
            return;
        }

        $shift['shift_type'] = $status;

        $this->shift_model->updateRecord($shift, 'shift_id');

        $results['isUpdate'] = true;

        echo json_encode($results);
    }

    public function updateShiftTime(){
        $shift_id = $this->input->post('shift_id');
        $from_time = $this->input->post('from_time');
        $to_time = $this->input->post('to_time');

        $shift = $this->shift_model->getFromId($shift_id);

        if (empty($shift)){
            $results['isUpdate'] = false;
            echo json_encode($results);
            return;
        }

        $shift['from_time'] = $from_time;
        $shift['to_time'] = $to_time;

        $this->shift_model->updateRecord($shift, 'shift_id');

        $results['isUpdate'] = true;

        echo json_encode($results);
    }
    public function updateReserveStaff(){
        $reserve_id = $this->input->post('reserve_id');
        $staff_id = $this->input->post('staff_id');

        $reserve = $this->reserve_model->getFromId($reserve_id);

        if (empty($reserve)){
            $results['isUpdate'] = false;
            echo json_encode($results);
            return;
        }

        $reserve['staff_id'] = $staff_id;

        $this->reserve_model->updateRecord($reserve, 'reserve_id');

        $results['isUpdate'] = true;

        echo json_encode($results);

    }

    /*--------------------common------------------------------*/

    public function loadShiftDataByParam(){
        $condition = $this->input->post('condition');
        $cond = json_decode($condition, true);

        $shifts = $this->shift_model->getShiftDataByParam($cond);

        $results['isLoad'] = true;
        $results['shifts'] = $shifts;
        echo json_encode($results);

    }

    public function loadShiftCounts(){
        $condition = $this->input->post('condition');
        $cond = json_decode($condition, true);

        $shift_counts = $this->shift_frame_model->getListByCond($cond);

        $results['counts'] = $shift_counts;

        echo json_encode($results);
    }

    public function loadShifts(){
        $condition = $this->input->post('condition');
        $cond = json_decode($condition, true);

        // if (!empty($cond['from_time']) && !empty($cond['to_time'])){
        //     $company_id = 2;
        //     $this->load->helper('epark');
        //     $epark_helper = new Epark_helper($company_id);
        //     $_from = new DateTime($cond['from_time']);
        //     $_to = new DateTime($cond['to_time']);
        //     while($_from->format('Y-m-d 00:00:00') <= $_to->format('Y-m-d 00:00:00')){
        //         $r = $epark_helper->loadEparkReceipt($cond['organ_id'], $_from->format('Y-m-d'));
        //         $_from->add(new DateInterval('P1D'));
        //     }
        // }

        $shifts = $this->shift_model->getListByCond($cond);
        // echo $condition;

        // return;

        $results['shifts'] = $shifts;
        echo json_encode($results);
    }

    public function saveShift(){
        $shift_id = $this->input->post('shift_id');
        $organ_id = $this->input->post('organ_id');
        $staff_id = $this->input->post('staff_id');
        $from_time = $this->input->post('from_time');
        $to_time = $this->input->post('to_time');
        $shift_type = $this->input->post('shift_type');

        if($from_time >= $to_time){
            $results['isSave'] = false;
            $results['message'] = 'シフト時間を正確に入力してください。';
            echo json_encode($results);
            return;
        }

        if ($shift_type==SHIFT_STATUS_ME_REJECT){
            $shift = $this->shift_model->getFromId($shift_id);

            if (empty($shift['old_shift'])){
                $this->shift_model->delete_force($shift_id, 'shift_id');
            }else{
                $shift['shift_type'] = $shift['old_shift'];
                $shift['old_shift'] = null;
                $this->shift_model->updateRecord($shift, 'shift_id');
            }
            $results['isSave'] = true;
            echo json_encode($results);
            return;
        }
        if ($shift_type==SHIFT_STATUS_ME_REPLY){
            $shift = $this->shift_model->getFromId($shift_id);

            $shift['shift_type'] = $shift_type;
            $this->shift_model->updateRecord($shift, 'shift_id');

            $results['isSave'] = true;
            echo json_encode($results);
            return;
        }

        $shift = [];
        $old_shift_type = 0;
        if (!empty($shift_id)){
            $shift = $this->shift_model->getFromId($shift_id);
            $old_shift_type = $shift['shift_type'];
        }


        if (!empty($organ_id)) $shift['organ_id'] = $organ_id;
        if (!empty($staff_id)) $shift['staff_id'] = $staff_id;
        if (!empty($from_time)) $shift['from_time'] = $from_time;
        if (!empty($to_time)) $shift['to_time'] = $to_time;
        if (!empty($shift_type)) $shift['shift_type'] = $shift_type;
        $shift['visible'] = 1;

        if($shift_type==SHIFT_STATUS_REST){
            $shifts = $this->shift_model->getListByCond([
                'staff_id'=>$staff_id,
                'organ_id' => $organ_id,
                'select_date' => substr($from_time, 0,10),
            ]);

            if(!empty($shifts)){
                $results['isSave'] = false;
                $results['message'] = 'シフトが存在します。 休みを設定するには、シフトを削除してください。';
                echo json_encode($results);
                return;
            }

            $shift = array(
                'staff_id' => $staff_id,
                'organ_id' => $organ_id,
                'from_time' => substr($from_time, 0,10). ' 00:00:00',
                'to_time' => substr($from_time, 0,10). ' 23:59:59',
                'shift_type' => $shift_type,
                'visible' => 1,
            );
            $this->shift_model->insertRecord($shift);
            $results['isSave'] = true;
            echo json_encode($results);
            return;

        }else{
            $this->shift_model->deleteDayShift(substr($from_time, 0,10), $staff_id, $organ_id, SHIFT_STATUS_REST);
        }

        $exist_other_shifts = $this->shift_model->getListByCond([
            'staff_id'=>$staff_id,
            'organ_id' => $organ_id,
            'in_from_time' => $from_time,
            'in_to_time' => $to_time,
            'no_shift' => $shift_id,
        ]);

        if (!empty($exist_other_shifts)){
            $results['isSave'] = false;
            $results['message'] = '入力したシフトが重複しました。時間を確認してください。';
            echo json_encode($results);
            return;
        }

        $counts = $this->shift_frame_model->getListByCond([
            'organ_id'=>$organ_id,
            'in_from_time'=>$from_time,
            'in_to_time'=>$to_time,
        ]);

        if (empty($counts)){
            $results['isSave'] = false;
            $results['message'] = '勤務計画が準備されていません。';
            echo json_encode($results);
            return;
        }

        if (!empty($shift_id)){
            $this->shift_model->delete_force($shift_id, 'shift_id');
        }

        foreach ($counts as $count){
            $_start = $count['from_time'];
            $_end = $count['to_time'];
            $from = $from_time;
            $to = $to_time;

            if ($from>=$_end || $to<=$_start) continue;
            if ($from>$_start) $input_from = $from; else $input_from = $_start;
            if ($to>$_end) $input_to = $_end; else $input_to = $to;

            $shift = array(
                'staff_id' => $staff_id,
                'organ_id' => $organ_id,
                'from_time' => $input_from,
                'to_time' => $input_to,
                'shift_type' => $shift_type,
                'visible' => 1,
            );
            $this->shift_model->insertRecord($shift);
        }

        $this->syncEparkShift($organ_id, $staff_id, substr($from_time, 0, 10));
        $result['isSave'] = true;
        echo json_encode($result);
    }

    public function initShift(){
        $from_date = $this->input->post('from_date');
        $to_date = $this->input->post('to_date');
        $organ_id = $this->input->post('organ_id');
        $staff_id = $this->input->post('staff_id');
        $pattern = $this->input->post('pattern');

        $from_time = $from_date . ' 00:00:00';
        $to_time = $to_date . ' 23:59:59';

        $counts = $this->shift_frame_model->getListByCond([
            'organ_id'=>$organ_id,
            'from_time'=>$from_time,
            'to_time'=>$to_time,
        ]);

        if (empty($counts)){
            $results['isSave'] = false;
            $results['message'] = '勤務計画が準備されていません。';
            echo json_encode($results);
            return;
        }

        $apply_shifts = $this->shift_model->getListByCond([
            'staff_id'=>$staff_id,
            'organ_id' => $organ_id,
            'from_time' => $from_time,
            'to_time' => $to_time,
            'no_edit' => 1
        ]);

        if (!empty($apply_shifts)){
            $results['isSave'] = false;
            $results['message'] = '確定したシフトが存在するため、初期設定を適用できません。';
            echo json_encode($results);
            return;
        }
        $pattern = empty($pattern) ? 1 : $pattern;

        $shifts = $this->shift_model->getListByCond([
            'staff_id'=>$staff_id,
            'organ_id' => $organ_id,
            'from_time' => $from_time,
            'to_time' => $to_time,
        ]);

        foreach ($shifts as $shift) {
            $this->shift_model->delete_force($shift['shift_id'], 'shift_id');
        }

        $condInit['staff_id'] = $staff_id;
        $condInit['organ_id'] = $organ_id;
        $condInit['pattern'] = $pattern;
        $initData = $this->setting_init_shift_model->getListByCond([
            'staff_id' => $staff_id,
            'organ_id' => $organ_id,
            'pattern' => $pattern
        ]);

        foreach ($initData as $item){
            $diff1Day = new DateInterval('P'.($item['weekday']-1).'D');
            $curDateTime = new DateTime($from_time);
            $curDateTime->add($diff1Day);
            $sel_date = $curDateTime->format("Y-m-d");

            if($item['shift_type']==SHIFT_STATUS_REST){
                $add_shift = array(
                    'from_time'=> $sel_date . ' 00:00:00',// $input_from,
                    'to_time' => $sel_date . ' 23:59:59', //$input_to,
                    'staff_id' => $staff_id,
                    'organ_id' => $organ_id,
                    'visible' => 1,
                    'shift_type' => empty($item['shift_type']) ? SHIFT_STATUS_SUBMIT : $item['shift_type'],
                );
                $this->shift_model->insertRecord($add_shift);


                continue;
            }

            $item_from = $sel_date . ' ' . $item['from_time'];
            $item_to = $sel_date . ' ' . $item['to_time'];

            $inner_counts = $this->shift_frame_model->getListByCond([
                'organ_id'=>$organ_id,
                'in_from_time'=>$item_from,
                'in_to_time'=>$item_to,
            ]);
            if(empty($inner_counts)) continue;

            foreach ($inner_counts as $count){
                $_from  = $item_from>=$count['from_time'] ? $item_from : $count['from_time'];
                $_to  = $item_to<=$count['to_time'] ? $item_to : $count['to_time'];
                $add_shift = array(
                    'from_time'=> $_from,// $input_from,
                    'to_time' => $_to, //$input_to,
                    'staff_id' => $staff_id,
                    'organ_id' => $organ_id,
                    'visible' => 1,
                    'shift_type' => empty($item['shift_type']) ? SHIFT_STATUS_SUBMIT : $item['shift_type'],
                );

                $this->shift_model->insertRecord($add_shift);
            }

        }

        $result['isSave'] = true;
        echo json_encode($result);
    }

    public function deleteShift(){
        $shift_id = $this->input->post('shift_id');
        $shift = $this->shift_model->getFromId($shift_id);
        if (!empty($shift_id)){
            $this->shift_model->delete_force($shift_id, 'shift_id');
        }

        //if (!empty($shift)){
        //    $this->syncEparkShift($shift['organ_id'], $shift['staff_id'], substr($shift['from_time'], 0, 10));
        //}

        $results['isDelete'] = true;
        echo json_encode($results);
        return;
    }

    public function updateShiftChange(){
        $organ_id = $this->input->post('organ_id');
		$organ = $this->organ_model->getFromId($organ_id);
		$company_id = $organ['company_id'];
        $json = $this->input->post('data');
        $datas = json_decode($json, true);
        foreach ($datas as $data){
            $staff_id = $data['staff_id'];
            $from_time = $data['from_time'];
            $to_time = $data['to_time'];
            $shift_type = $data['shift_type'];

            $old_shift = $this->shift_model->getRecordByCond(['staff_id'=>$staff_id, 'organ_id'=>$organ_id, 'in_from_time'=>$from_time, 'in_to_time'=>$to_time], true);

            if ($shift_type == SHIFT_STATUS_ME_REJECT){
                if (empty($old_shift['old_shift'])){
                    $this->shift_model->delete_force($old_shift['shift_id'], 'shift_id');
                }else{
                    $old_shift['shift_type'] = $old_shift['old_shift'];
                    $old_shift['old_shift'] = null;

                    $this->shift_model->updateRecord($old_shift, 'shift_id');
                }
                continue;
            }

            if(empty($old_shift)){
                $shift = array(
                    'staff_id' => $staff_id,
                    'organ_id' => $organ_id,
                    'from_time' => $from_time,
                    'to_time' => $to_time,
                    'shift_type' => $shift_type,
                    'visible' => 1
                );
                $this->shift_model->insertRecord($shift);
            }else{
                $old_type = SHIFT_STATUS_REQUEST == $shift_type ? $old_shift['shift_type'] : $old_shift['old_shift'];
                if ($old_shift['from_time']==$from_time && $old_shift['to_time']==$to_time){
                    $old_shift['old_shift'] = $old_type;
                    $old_shift['shift_type'] = $shift_type;

                    $this->shift_model->updateRecord($old_shift, 'shift_id');
                }else if($old_shift['from_time']==$from_time || $old_shift['to_time']==$to_time) {
                    if($old_shift['from_time']==$from_time){
                        $old_shift['from_time'] = $to_time;
                    }
                    if($old_shift['to_time']==$to_time){
                        $old_shift['to_time'] = $from_time;
                    }

                    $this->shift_model->updateRecord($old_shift, 'shift_id');

                    $shift = array(
                        'staff_id' => $staff_id,
                        'organ_id' => $organ_id,
                        'from_time' => $from_time,
                        'to_time' => $to_time,
                        'old_shift' => $old_type,
                        'shift_type' => $shift_type,
                        'visible' => 1
                    );
                    $this->shift_model->insertRecord($shift);
                }else{
                    $tmp = $old_shift;
                    $old_shift['to_time'] = $from_time;
                    $this->shift_model->updateRecord($old_shift, 'shift_id');

                    $shift = array(
                        'staff_id' => $staff_id,
                        'organ_id' => $organ_id,
                        'from_time' => $from_time,
                        'to_time' => $to_time,
                        'old_shift' => $old_type,
                        'shift_type' => $shift_type,
                        'visible' => 1
                    );
                    $this->shift_model->insertRecord($shift);

                    $shift = array(
                        'staff_id' => $staff_id,
                        'organ_id' => $organ_id,
                        'from_time' => $to_time,
                        'to_time' => $tmp['to_time'],
                        'shift_type' => $tmp['shift_type'],
                        'visible' => 1
                    );
                    $this->shift_model->insertRecord($shift);
                }
            }

			$notify_type = '';
			//if($shift_type == SHIFT_STATUS_APPLY)
            //    $notify_type = '17';
			//if($shift_type == SHIFT_STATUS_REJECT)
            //    $notify_type = '18';
			if($shift_type == SHIFT_STATUS_REQUEST)
                $notify_type = '19';
			if (!empty($notify_type)){


                $text_data = $this->notification_text_model->getRecordByCond(['company_id'=>$company_id, 'mail_type'=> $notify_type]);

                $title = empty($text_data['title']) ? 'タイトルなし' : $text_data['title'];
                $content = empty($text_data['content']) ? 'タイトルなし' : $text_data['content'];
                $content = str_replace('$select_month', substr($from_time, 5,2), $content);
                $content = str_replace('$select_day', substr($from_time, 8,2), $content);
                $content = str_replace('$from_time', substr($from_time, 11,5), $content);
                $content = str_replace('$to_time', substr($to_time, 11,5), $content);
				$is_fcm = $this->sendNotifications($notify_type, $title, $content, '', $staff_id, '1');
			}
        }

        $this->shift_model->delete_force('0', 'shift_type');

        echo json_encode(['isUpdate' => true]);
    }

    public function autoControlShift(){
        $organ_id = $this->input->get_post('organ_id');
        $from_time = $this->input->get_post('from_time');
        $to_time = $this->input->get_post('to_time');
        $in_from_time = $this->input->get_post('in_from_time');
        $in_to_time = $this->input->get_post('in_to_time');
        $type = $this->input->get_post('type');

        $data = $this->shift_model->getStaffOrderForAuto($organ_id, $from_time, $to_time, $in_from_time, $in_to_time, $type);

        for($i = 0; $i < count($data); $i++) {
            $otherShopShiftData = $this->shift_model->getStaffOtherShopShiftTime($organ_id, $data[$i]['staff_id']);
            if (!empty($otherShopShiftData)) {
                $data[$i]['time'] = $data[$i]['time'] + $otherShopShiftData['all_shift'];
            }
        }

        $results['data'] = $data;

        echo json_encode($results);

    }

    public function getStaffShiftTime() {
        $staff_id = $this->input->get_post('staff_id');
        $organ_id = $this->input->get_post('organ_id');
        $shiftData = $this->shift_model->getStaffShiftTime($staff_id, $organ_id);

        $results['isLoad'] = true;
        $results['staff_times'] = isset($shiftData['staff_times']) ? $shiftData['staff_times'] : "0";
        // $results['shift_apply_time'] = isset($shiftData['shift_apply_time']) ? abs($shiftData['shift_apply_time']) : 0;
        // $results['shift_application_time'] = isset($shiftData['shift_application_time']) ? abs($shiftData['shift_application_time']) : 0;

        echo json_encode($results);
    }
}
?>
