<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . 'core/WebController.php';

/*
 *
 */

class Apireserves extends WebController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        $this->reserve_status = array(
            'request' => 1,
            'apply' => 2,
            'reject' => 3,
            'cancel' => 4,
            'entering' => 5,
            'complete' => 6
        );

        parent::__construct();

        $this->load->model('staff_organ_model');
        $this->load->model('organ_model');
        $this->load->model('company_model');
        $this->load->model('staff_model');
        $this->load->model('user_model');
        $this->load->model('menu_model');


        $this->load->model('reserve_model');
        $this->load->model('reserve_menu_model');
        $this->load->model('reserve_ticket_model');

        $this->load->model('shift_model');
        $this->load->model('order_model');
        $this->load->model('order_menu_model');
        $this->load->model('organ_time_model');
        $this->load->model('organ_special_time_model');
        $this->load->model('shift_frame_model');
        //        $this->load->model('pos_staff_shift_model');
    }

    public function loadUserReserveData()
    {

        $user_id = $this->input->post('user_id');
        $organ_id = $this->input->post('organ_id');
        $staff_id = $this->input->post('staff_id');
        $from_date = $this->input->post('from_date');
        $to_date = $this->input->post('to_date');

        $results = [];
        if (empty($organ_id) || empty($user_id)){
            $results['isLoad'] = false;
            echo json_encode($results);
            return;
        }
        $staff_list = $this->staff_organ_model->getStaffsByOrgan($organ_id, 3, false);

        $organ_time = $this->organ_time_model->getMinMaxTimeByCond(['organ_id' =>$organ_id]);

        $organ_from_time = empty($organ_time['from_time']) ? "00:00:00" : ($organ_time['from_time'].":00");
        $organ_to_time = (empty($organ_time['to_time']) || $organ_time['to_time'] == '24:00') ? "23:59:59" : ($organ_time['to_time'].":00");

        $results = [];
        $regions = [];

        $cur_date = $from_date.' '.$organ_from_time;

        while($cur_date<=$to_date.' '. $organ_to_time){
            $curDateTime = new DateTime($cur_date);
            $base = $curDateTime->format("H:i:s");
            if ($base>=$organ_from_time && $base < $organ_to_time){
                $tmp = [];
                $tmp['time'] = $cur_date;
                $tmp['type'] = $this->getReserveTimeStatus($organ_id, $staff_id, $cur_date);
                $regions[] = $tmp;
            }

            $diff1Day = new DateInterval('PT1H');
            $curDateTime->add($diff1Day);
            $cur_date = $curDateTime->format("Y-m-d H:i:s");
        }
        $results['isLoad'] = true;
        $results['regions'] = $regions;
        $results['staffs'] = $staff_list;
        $results['organ_from_time'] = $organ_from_time;
        $results['organ_to_time'] = $organ_to_time;


        $reserve_status = array(
            'request' => 1,
            'apply' => 2,
            'reject' => 3,
            'cancel' => 4,
            'entering' => 5,
            'complete' => 6
        );

        $reserves = $this->reserve_model->getListByCond([
            'from_time' => $from_date." 00:00:00",
            'to_time' => $to_date." 23:59:59",
            'user_id' => $user_id,
            'max_status' => $this->reserve_status['apply']
        ]);

        $results['reserves'] = $reserves;

        echo(json_encode($results));

    }

    public function loadSelectStatus()
    {
        $organ_id = $this->input->post('organ_id');
        $staff_id = $this->input->post('staff_id');
        $select_time = $this->input->post('select_time');
        if (empty($organ_id) || empty($staff_id)){
            echo json_encode(['isLoad'=>false]);
            return;
        }

        $status = $this->getReserveTimeStatus($organ_id, $staff_id, $select_time);
        $results['isLoad'] = true;
        $results['status'] = $status;

        echo(json_encode($results));

    }

    public function saveUserReserve(){
        $organ_id = $this->input->post('organ_id');
        $user_id = $this->input->post('user_id');
        $staff_id = $this->input->post('staff_id');
        $sel_staff_type = $this->input->post('sel_staff_type');
        $reserve_start_time = $this->input->post('reserve_start_time');
        $reserve_end_time = $this->input->post('reserve_end_time');
        $shift_frame_id = $this->input->post('shift_frame_id');
        $reserve_menu = $this->input->post('reserve_menu');
        $reserve_ticket = $this->input->post('use_ticket');
        $coupon_id = $this->input->post('coupon_id');
        $pay_method = $this->input->post('pay_method');
        $coupon_use_amount = $this->input->post('coupon_use_amount');
        $ticket_amount = $this->input->post('ticket_amount');
        $amount = $this->input->post('amount');
        $user_count = $this->input->post('user_count');
        $sum_time = empty($this->input->post('sum_time')) ? 0 : $this->input->post('sum_time');
        $user_2 = empty($this->input->post('user_2')) ? null : $this->input->post('user_2');
        $user_3 = empty($this->input->post('user_3')) ? null : $this->input->post('user_3');
        $user_4 = empty($this->input->post('user_4')) ? null : $this->input->post('user_4');
        $results = [];

        if (empty($organ_id) || empty($user_id)){
            $results['isSave'] = false;
            echo json_encode($results);
            return;
        }
        
        $condition_status = $this->getReserveTimeStatus($organ_id, $staff_id, $reserve_start_time, $user_id, $sum_time, $sel_staff_type);
        if ($condition_status==3){
            if (empty($reserve_id)){
                $results['isSave'] = false;
                echo json_encode($results);
                return;
            }
        }

        $pos =null;
        if($condition_status==1){
            $pos = $this->order_model->emptyMaxPosition([
               'organ_id' => $organ_id,
               'from_time' =>  $reserve_start_time,
                'to_time' => $reserve_end_time,
                'status_array' => [ORDER_STATUS_RESERVE_APPLY, ORDER_STATUS_TABLE_START, ORDER_STATUS_TABLE_END, ORDER_STATUS_TABLE_COMPLETE]
            ]);
        }

        if(empty($staff_id)){
            $shifts = $this->shift_model->getEnableShifts($organ_id, $reserve_start_time, $reserve_end_time);
            foreach ($shifts as $shift){
                if (($sel_staff_type == '1' || $sel_staff_type == '2') && $shift['staff_sex']!=$sel_staff_type) continue;

                $orders = $this->order_model->getListByCond([
                    'staff_id' => $shift['staff_id'],
                    'in_from_time' => $reserve_start_time,
                    'in_to_time' => $reserve_end_time
                ]);

                if (!empty($orders)) continue;

                $staff_id = $shift['staff_id'];
                break;
            }
        }

        $order = array(
            'organ_id' => $organ_id,
            'table_position' => $pos,
            'amount' => $amount,
            'user_id' => $user_id,
            'select_staff_type' => empty($sel_staff_type) ? 0 : $sel_staff_type,
            'select_staff_id' => empty($staff_id) ? null : $staff_id,
            'user_count'=>$user_count,
            'other_name_1' => $user_2,
            'other_name_2' => $user_3,
            'other_name_3' => $user_4,
            'shift_frame_id' => $shift_frame_id,
            'from_time' => $reserve_start_time,
            'to_time' => $reserve_end_time,
            'coupon_id' => empty($coupon_id)?null:$coupon_id,
            'pay_method' => empty($pay_method)?null:($pay_method==1 ? 1: null),
            'coupon_use_amount' => empty($coupon_use_amount)?null:$coupon_use_amount,
            'ticket_amount' => empty($ticket_amount) ? null : $ticket_amount,
            'status'=>$condition_status==1 ? ORDER_STATUS_RESERVE_APPLY : ORDER_STATUS_RESERVE_REQUEST,
            'is_reserve'=>1,
        );

        $order_id = $this->order_model->insertRecord($order);

        if (empty($order_id)){
            $results['isSave'] = false;
            echo json_encode($results);
            return;
        }

        $data = json_decode($reserve_menu);
        $interval = 0;
        foreach ($data as $record) {
            $menu = $this->menu_model->getFromId($record->menu_id);
            $menu_interval = empty($menu['menu_interval']) ? 0 : $menu['menu_interval'];
            if ($interval<$menu_interval) $interval = $menu_interval;
            $insertData = [];
            $insertData = array(
                'order_id' => $order_id,
                'menu_id' => $record->menu_id,
                'menu_title' => $menu['menu_title'],
                'menu_price' => $record->menu_price,
                'quantity' => 11
            );

            $insert = $this->order_menu_model->insertRecord($insertData);
        }

        $reserveData = $this->order_model->getFromId($order_id);
        $reserveData['interval'] = $interval;
        $this->order_model->updateRecord($reserveData);

        $tickets = json_decode($reserve_ticket);
        foreach ($tickets as $record) {
            $insertData = array(
                'reserve_id' => $order_id,
                'ticket_id' => $record->ticket_id,
                'use_count' => $record->use_count,
            );

            $insert = $this->reserve_ticket_model->insertRecord($insertData);
        }

        if (!empty($staff_id)){
            $results['isFCM'] = $this->sendNotificationToStaffReserveRequest($order_id);
        }

        $results['isSave'] = true;
        echo json_encode($results);

    }

    private function sendNotificationToStaffReserveRequest($reserve_id){
        $reserve = $this->reserve_model->getFromId($reserve_id);
        $reserve_menus = $this->reserve_menu_model->getReserveMenuList($reserve_id);
        $str_menus = '';
        foreach ($reserve_menus as $menu){
            if($str_menus!='') $str_menus = $str_menus . ', ';
            $str_menus = $str_menus . $menu['menu_title'];
        }

        $user = $this->user_model->getFromId($reserve['user_id']);
        $organ = $this->organ_model->getFromId($reserve['organ_id']);

        $reserve_time = new DateTime($reserve['reserve_time']);

        $this->load->model('notification_text_model');
        $text_data = $this->notification_text_model->getRecordByCond(['company_id'=>$user['company_id'], 'mail_type'=>'13']);
        $title = empty($text_data['title']) ? 'タイトルなし' : $text_data['title'];
        $content = empty($text_data['content']) ? '' : $text_data['content'];
        $content = str_replace('$organ_name', $organ['organ_name'], $content);
        $content = str_replace('$user_name', $user['user_first_name'].' '.$user['user_last_name'], $content);
        $content = str_replace('$reserve_time', $reserve_time->format('n月j日 H時i分'), $content);
        $content = str_replace('$menus', $str_menus, $content);
        $content = str_replace('$user_comment', '', $content);

        $is_fcm = $this->sendNotifications('13', $title, $content, $reserve['staff_id'], $reserve['user_id'], '1');

        return true;

    }

    public function loadReserveList(){
        $user_id = $this->input->post('user_id');
        $staff_id = $this->input->post('staff_id');
        $company_id = $this->input->post('company_id');

        $cond=[];

        if (!empty($staff_id)){
            $staff = $this->staff_model->getFromId($staff_id);
            if ($staff['staff_auth']<4){
                $organs = $this->staff_organ_model->getOrgansByStaff($staff_id);
                $cond['organ_ids'] = join(',' , array_column($organs,'organ_id'));
            }
            if ($staff['staff_auth']==4){
                $cond['company_id'] = $company_id;
            }
        }

        if (!empty($user_id)) $cond['user_id'] = $user_id;

        $lists = $this->reserve_model->getListByCond( $cond);

        $reserves = [];
        foreach ($lists as $item){
            $menus = $this->reserve_menu_model->getReserveMenuList($item['reserve_id']);

            $item['menus'] = $menus;
            $reserves[] = $item;
        }

        $results['isLoad'] = true;
        $results['reserves'] = $reserves;

        echo json_encode($results);
    }

    public function loadReserveInfo(){
        $reserve_id = $this->input->post('reserve_id');

        $reserve = $this->reserve_model->getFromId($reserve_id);
        $menus = $this->reserve_menu_model->getReserveMenuList($reserve['reserve_id']);
        $reserve['menus'] = $menus;

//        $sum = 0;
//        foreach ($menus as $menu){
//            $price = $menu['menu_price']==null ? 0 : $menu['menu_price'];
//            $sum = $sum + $price;
//        }

//        $reserve_year = substr($reserve['reserve_time'],0,4);
//        $reserve['sum_amount'] = $sum;
//        $reserve['reserve_year'] = $reserve_year;

//        $organ = $this->organ_model->getFromId($reserve['organ_id']);
//
//        $company = $this->company_model->getFromId($organ['company_id']);
//
//        $user = $this->user_model->getFromId($reserve['user_id']);


        $results['isLoad'] = true;
//        $results['company'] = $company;
//        $results['organ'] = $organ;
//        $results['user'] = $user;
        $results['reserve'] = $reserve;

        echo json_encode($results);
    }

    public function deleteReserve(){
        $reserve_id = $this->input->post('reserve_id');
        if (empty($reserve_id)){
            $results['isDelete'] = false;
            echo json_encode($results);
            return;
        }

        $this->reserve_model->delete_force($reserve_id, 'reserve_id');
        $this->reserve_menu_model->delete_force($reserve_id, 'reserve_id');

        $results['isDelete'] = true;

        echo json_encode($results);
    }

    public function loadReserveStaff(){
        $from_time = $this->input->post('from_time');
        $to_time = $this->input->post('to_time');
        $organ_id = $this->input->post('organ_id');

        $reserves = $this->reserve_model->getReserveStaffs($organ_id, $from_time, $to_time);

        $staffs = [];
        foreach ($reserves as $reserve){
            if (empty($reserve['staff_id'])) continue;
            $staff_id = $reserve['staff_id'];
            if (in_array($staff_id, $staffs)) continue;
            $shifts = $this->shift_model->isExist($organ_id, $staff_id, '', $from_time, $to_time);
            if (!empty($shifts)) continue;

            $staffs[] = $staff_id;
        }

        $results['isLoad'] = true;
        $results['staffs'] = $staffs;

        echo json_encode($results);
    }


    public function loadPossibleReserve(){
        $organ_id = $this->input->post('organ_id');
        $user_id = $this->input->post('user_id');
        $staff_id = $this->input->post('staff_id');
        $from_time = $this->input->post('from_time');
        $to_time = $this->input->post('to_time');

        $results['is_possible'] = $this->isPosibleReserve($organ_id, $user_id, $staff_id, $from_time, $to_time);

        echo json_encode($results);
    }

    public function applyReserve(){
        $reserve_id = $this->input->post('reserve_id');//25
        $reserve = $this->reserve_model->getFromId($reserve_id);

        if (empty($reserve)){
            $results['isApply'] = false;
            echo json_encode($results);
            return;
        }

        $reserve['reserve_status'] = $this->reserve_status['apply'];

        $this->reserve_model->updateRecord($reserve);

        $sender = $this->staff_model->getFromId($reserve['staff_id']);

        $this->load->model('notification_text_model');
        $text_data = $this->notification_text_model->getRecordByCond(['company_id'=>$sender['company_id'], 'mail_type'=>'23']);
        $title = empty($text_data['title']) ? 'タイトルなし' : $text_data['title'];
        $content = empty($text_data['content']) ? '' : $text_data['content'];
        $content = str_replace('$from_time', $reserve['reserve_time'], $content);
        $content = str_replace('$to_time', $reserve['reserve_exit_time'], $content);
        $this->sendNotifications('23', $title, $content, $reserve['staff_id'], $reserve['user_id'], '2');

        $results['isApply'] = true;
        echo json_encode($results);
    }

    public function rejectReserve(){
        $reserve_id = $this->input->post('reserve_id');//25

        $reserve = $this->reserve_model->getFromId($reserve_id);

        if (empty($reserve)){
            $results['isReject'] = false;
            echo json_encode($results);
            return;
        }

        $reserve['reserve_status'] = $this->reserve_status['reject'];

        $this->reserve_model->updateRecord($reserve);

        $results['isReject'] = true;
        echo json_encode($results);
    }

    private function isPosibleReserve($organ_id, $user_id, $staff_id, $from_time, $to_time){

        if ($this->reserve_model->isMyPeriodReserve($user_id, $from_time, $to_time)) return false;

        $organ = $this->organ_model->getFromId($organ_id);
        $table_count = $organ['table_count'] == null ? 10 : $organ['table_count'];

        $week = date('N', strtotime($from_time));
        $f_time = date('H:i', strtotime($from_time));
        $t_time = date('H:i', strtotime($to_time));
        $isActive = $this->organ_time_model->isPeriodActiveTime($organ_id, $week, $f_time, $t_time);
        if (!$isActive) return false;

        $reserve_count = $this->reserve_model->getReservePeriodCount($organ_id, $from_time, $to_time);
        if ($reserve_count>=$table_count) return false;

        if (empty($staff_id)) return false;
        $staff_reserve_count = $this->reserve_model->getReservePeriodCount($organ_id, $from_time, $to_time, $staff_id);
        if ($staff_reserve_count > 0) return false;

//        $isStaffActive = $this->shift_model->isStaffActiveReserve($organ_id, $staff_id, $sel_time);
//        if (!$isStaffActive) return '2';

        return true;
    }

    public function getLastReserve(){
        $user_id = $this->input->post('user_id');
        $organ_id = $this->input->post('organ_id');

        $last_reserve = $this->reserve_model->getReserverLastRecord(['organ_id'=>$organ_id, 'user_id'=>$user_id]);

        if (empty($last_reserve)){
            $results['staff_id'] = '';
        }else{
            $results['staff_id'] = empty($last_reserve['staff_id']) ? '' : $last_reserve['staff_id'];
        }

        echo json_encode($results);

    }

    public function updateReserveStatus(){
        $reserve_id = $this->input->post('reserve_id');

        $reserve = $this->reserve_model->getFromID($reserve_id);
        $reserve['reserve_status']=$this->reserve_status['complete'];
        $reserve['visit_time']=date('Y-m-d H:i:s');
        $user = $this->user_model->getFromId($reserve['user_id']);

        $is_first_reserve_visit = $this->reserve_model->getVisitCount($reserve['organ_id'], $reserve['user_id'], $user['company_id']);

        $this->reserve_model->updateRecord($reserve, 'reserve_id');

        $results['isStampAdd'] = false;
        if($is_first_reserve_visit){

            $stamp = array(
                'date' => Date('Y-m-d'),
                'user_id' => $reserve['user_id'],
                'company_id' => '',
                'organ_id' => $reserve['organ_id'],
                'staff_id' => $reserve['staff_id'],
                'use_flag' => '1',
                'stamp_count' => '1'
            );

            $this->load->model('stamp_model');
            $this->stamp_model->insertRecord($stamp);
            $results['isStampAdd'] = true;
        }

        $results['isUpdate'] = true;

        echo json_encode($results);

    }

    public function enteringOrgan(){
        $organ_id = $this->input->post('organ_id');
        $user_id = $this->input->post('user_id');
        $order_id = $this->input->post('order_id');
        $menu_id_string = $this->input->post('menu_ids');

        $user = $this->user_model->getFromId($user_id);

        $cond = [];
        $cond['organ_id'] = $organ_id;
        $cond['user_id'] = $user_id;
        $cond['status_array'] = [ORDER_STATUS_TABLE_START, ORDER_STATUS_TABLE_END, ORDER_STATUS_TABLE_COMPLETE];
        if($user['company_id']==1){
            $now_h = date('G');
            $now_date = date('Y-m-d');
            $dt = new DateTime($now_date);

            if($now_h<12){
                $dt->sub(new DateInterval("P1D")); // 2016-03-02
                $fromDate = $dt->format("Y-m-d");
                $from_time = $fromDate . ' 12:00:00';
                $to_time = $now_date . ' 12:00:00';
            }else{
                $dt->add(new DateInterval("P1D")); // 2016-03-02
                $toDate = $dt->format("Y-m-d");
                $from_time = $now_date . ' 12:00:00';
                $to_time = $toDate . ' 12:00:00';
            }
        }else{
            $from_time = date('Y-m-d') . ' 00:00:00';
            $to_time = date('Y-m-d') . ' 23:59:59';
        }
        $cond['from_time'] = $from_time;
        $cond['to_time'] = $to_time;

        $today_orders = $this->order_model->getListByCond($cond);
//        $is_first_reserve_visit = $this->order_model->getVisitCount($organ_id, $user_id, $user['company_id']);
        if(empty($order_id)){
            $menu_ids = empty($menu_id_string) ? [] : explode(',', $menu_id_string);
            $menus = [];
            $sum_time = 0;
            $interval = 0;
            $amount = 0;

            foreach ($menu_ids as $menu_id){
                $menu = $this->menu_model->getFromId($menu_id);
                $sum_time += $menu['menu_time'];
                $amount += $menu['menu_price'];
                if ($interval<$menu['menu_interval']) $interval = $menu['menu_interval'];
            }
            $sum_time += $interval;

            $now = new DateTime();
            $min = $now->format('i');
            if($min>55){
                $now->add(new DateInterval('PT5M'));
                $from_time = $now->format('Y-m-d H:00:00');
            }else{
                if ($min%5>0){
                    $min = ($min+5) - $min%5;
                    if ($min<10) $min = '0'.$min;
                }
                $now->add(new DateInterval('PT5M'));
                $from_time = $now->format('Y-m-d H:'.$min.':00');
            }
            $now = new DateTime($from_time);
            $now->add(new DateInterval('PT'.$sum_time.'M'));
            $to_time = $now->format('Y-m-d H:i:00');

            $pos = $this->order_model->emptyMaxPosition([
                'organ_id' => $organ_id,
                'from_time' =>  $from_time,
                'to_time' => $to_time,
                'status_array' => [ORDER_STATUS_RESERVE_APPLY, ORDER_STATUS_TABLE_START, ORDER_STATUS_TABLE_END, ORDER_STATUS_TABLE_COMPLETE]
            ]);

            $order = array(
                'user_id' => $user_id,
                'table_position' => $pos,
                'organ_id' => $organ_id,
                'from_time' => $from_time,
                'to_time' => $to_time,
                'interval' => $interval,
                'amount' => $amount,
                'status' => ORDER_STATUS_TABLE_END,
            );
            $this->order_model->insertRecord($order);


        }else{
            $order = $this->order_model->getFromId($order_id);
            $order['status'] = ORDER_STATUS_TABLE_END;

            $this->order_model->updateRecord($order);
        }

        $results['isStampAdd'] = false;
        $results['isUpdateGrade'] = false;
        if(empty($today_orders)){
            $stamp = array(
                'date' => Date('Y-m-d'),
                'user_id' => $user_id ,
                'company_id' => $user['company_id'],
                'organ_id' => $organ_id,
                'use_flag' => '0',
                'stamp_count' => '1'
            );

            $this->load->model('stamp_model');
            $this->stamp_model->insertRecord($stamp);
            $results['isStampAdd'] = true;

            $this->addItemByStamp($user_id);
            $results['isUpdateGrade'] = $this->updateStampRanking($user_id);
        }

        $results['isUpdate'] = true;

        echo json_encode($results);
    }

    public function getReserveNow(){
        $user_id = $this->input->post('user_id');
        $organ_id = $this->input->post('organ_id');

        $cond = [];
        $cond['organ_id'] = $organ_id;
        $cond['user_id'] = $user_id;
        $cond['reserve_status'] = $this->reserve_status['apply'];
        $cond['from_time'] = date('Y-m-d H:i:s', strtotime(' -30 min'));
        $cond['to_time'] = date('Y-m-d H:i:s', strtotime(' +30 min'));
        $reserves = $this->reserve_model->getReserveNowData($cond);

        if (empty($reserves)){
            $results['isExistReserve'] = false;
        }else{
            $results['isExistReserve'] = true;
            $results['reserve'] = $reserves[0];
        }


        echo json_encode($results);

    }

    public function reserveCancel(){
        $reserve_id = $this->input->post('reserve_id');

        $reserve = $this->reserve_model->getFromId($reserve_id);

        $reserve['reserve_status'] = $this->reserve_status['cancel'];
        $this->reserve_model->updateRecord($reserve, 'reserve_id');

        $results['isUpdate'] = true;
        echo json_encode($results);

    }

    public function loadUserReserveList()
    {

        $user_id = $this->input->post('user_id');
        $organ_id = $this->input->post('organ_id');
        $from_date = $this->input->post('from_date');
        $to_date = $this->input->post('to_date');

        $results = [];
        if (empty($organ_id) || empty($user_id)){
            $results['isLoad'] = false;
            echo json_encode($results);
            return;
        }

        $reserves = $this->reserve_model->getListByCond([
            'organ_id'=>$organ_id,
            'user_id'=>$user_id,
            'from_time'=>$from_date,
            'to_time'=>$to_date,
            'max_status'=>$this->reserve_status['apply']
        ]);

        $results['isLoad'] = true;
        $results['reserves'] = $reserves;

        echo(json_encode($results));

    }

    public function loadReserveShiftConditions(){
        $organ_id = $this->input->post('organ_id');
        $from_date = $this->input->post('from_date');
        $to_date = $this->input->post('to_date');
        $user_id = $this->input->post('user_id');
        $duration = $this->input->post('duration');
        
        $results = [];
        $regions = [];

        $cur_date = $from_date;

        $organ = $this->organ_model->getFromId($organ_id);
        
        while($cur_date<=$to_date){
            $curDateTime = new DateTime($cur_date);

            if ($curDateTime > new DateTime()){
                $tmp = [];
                $tmp['time'] = $cur_date;
                $tmp['type'] = $this->getStatusShiftCountForReserve($organ, $cur_date);
                $regions[] = $tmp;
            }else{
                $tmp = [];
                $tmp['time'] = $cur_date;
                $tmp['type'] = 0; // meiyou
                $regions[] = $tmp;
            }

            $diff1Day = new DateInterval('PT5M');
            $curDateTime->add($diff1Day);
            $cur_date = $curDateTime->format("Y-m-d H:i:s");
        }
        $results['isLoad'] = true;
        $results['regions'] = $regions;

        echo json_encode($results);
    }

    public function loadReserveConditions(){

        $organ_id = $this->input->post('organ_id');
        $staff_id = $this->input->post('staff_id');
        $from_date = $this->input->post('from_date');
        $to_date = $this->input->post('to_date');
        $user_id = $this->input->post('user_id');
        $duration = $this->input->post('duration');
        $staff_type = $this->input->post('sel_staff_type');

        $results = [];
        $regions = [];

        $cur_date = $from_date;

        while($cur_date<=$to_date){
            $curDateTime = new DateTime($cur_date);

            if ($curDateTime > new DateTime()){
                $tmp = [];
                $tmp['time'] = $cur_date;
                $tmp['type'] = $this->getReserveTimeStatus($organ_id, $staff_id, $cur_date, $user_id, $duration, $staff_type);
                $regions[] = $tmp;
            }else{
                $tmp = [];
                $tmp['time'] = $cur_date;
                $tmp['type'] = 0;
                $regions[] = $tmp;
            }

            $diff1Day = new DateInterval('PT5M');
            $curDateTime->add($diff1Day);
            $cur_date = $curDateTime->format("Y-m-d H:i:s");
        }
        $results['isLoad'] = true;
        $results['regions'] = $regions;

        echo json_encode($results);

    }

    private function getShiftTimeStatus($organ, $cur_date, $user_id, $duration){
        $organ_id = $organ['organ_id'];
        $curFromTime = new DateTime($cur_date);
        $curToTime = new DateTime($cur_date);
        $from_time = $curFromTime->format("Y-m-d H:i:s");
        if(empty($duration)){
            $duration = 0;
        }
        $curToTime->add(new DateInterval('PT'.$duration.'M'));
        $to_time = $curToTime->format("Y-m-d H:i:s");
        
        $table_count = $organ['table_count'] == null ? 10 : $organ['table_count'];

        /* ------------------is my order check-------------------------- */
        // cannot do double order.
        $my_reserves = $this->order_model->getListByCond([
            'user_id' => $user_id,
            'in_from_time' => $from_time,
            'in_to_time' => $to_time,
            'is_with_interval' => 1,
            'status_array' => [ORDER_STATUS_RESERVE_APPLY, ORDER_STATUS_RESERVE_REQUEST],
        ]);
        if (!empty($my_reserves)) return '3';
        /* endcheck */

        /* ------------------is count check-------------------------- */
        $position_count = $this->order_model->getPositionCountByPeriod($organ_id, $from_time, $to_time);
        if ($position_count >= $table_count) return '3';
        //--------------------------------

        $position_count = 0;

        /* ------------------is shift count check ------------------- */
        $shift_count = $this->shift_frame_model->getPositionCountByPeriod($organ_id, $from_time, $to_time);
        if($position_count < $shift_count){
            return '1'; // 〇: there is a vacancy
        } else {
            return '3';
        }
    }

    private function getStatusShiftCountForReserve($organ, $cur_date){
        $organ_id = $organ['organ_id'];
 
        /* ------------------is my order check-------------------------- */
       
        $shift_count = $this->shift_frame_model->getListByCond([
            'select_time' => $cur_date,
            'organ_id' => $organ_id
        ]);
        if (empty($shift_count)) return '3';
        if (new DateTime($shift_count[0]['from_time'])<new DateTime()) return '3';
        
        
        return '1'; // 〇: there is a vacancy
        
    }
    private function getReserveTimeStatus($organ_id, $staff_id, $cur_date, $user_id, $duration, $staff_type){
        $curFromTime = new DateTime($cur_date);
        $curToTime = new DateTime($cur_date);
        $from_time = $curFromTime->format("Y-m-d H:i:s");
        if(empty($duration)){
            $duration = 0;
        }
        $curToTime->add(new DateInterval('PT'.$duration.'M'));
        $to_time = $curToTime->format("Y-m-d H:i:s");

        $organ = $this->organ_model->getFromId($organ_id);
        $table_count = $organ['table_count'] == null ? 10 : $organ['table_count'];
        $week = date('N', strtotime($from_time));
        $start_time = $curFromTime->format("H:i");
        $end_time = $curToTime->format("H:i");

        /* ------------------open Time check-------------------------- */
        $isInOpenTime = $this->organ_time_model->isInOpenTime($organ_id, $week, $start_time, $end_time);
        if (!$isInOpenTime){
            $isInOpenTime = $this->organ_special_time_model->isInOpenTime($organ_id, $from_time, $to_time);
        }
        if(!$isInOpenTime) return '3';

        /* ------------------is my order check-------------------------- */
        $my_reserves = $this->order_model->getListByCond([
            'user_id' => $user_id,
            'in_from_time' => $from_time,
            'in_to_time' => $to_time,
            'is_with_interval' => 1,
            'status_array' => [ORDER_STATUS_RESERVE_APPLY, ORDER_STATUS_RESERVE_REQUEST],
        ]);
        if (!empty($my_reserves)) return 3;

        /* ------------------is count check-------------------------- */
        $position_count = $this->order_model->getPositionCountByPeriod($organ_id, $from_time, $to_time);
        if ($position_count>=$table_count) return '3';

        //--------------------------------

        if(empty($staff_id)){
            if ($staff_type == '1' || $staff_type == '2'){
               $staffs = $this->staff_model->getStaffs([
                    'organ_id' => $organ_id,
                    'min_auth' => STAFF_AUTH_STAFF,
                    'max_auth' => STAFF_AUTH_OWNER,
                    'staff_sex' => $staff_type,
               ]);   
               $reserve_result = 3;
               foreach($staffs as $staff){
                    $isStaffInReserve = $this->order_model->isStaffInReserve($staff['staff_id'], $from_time, $to_time);
                    if ($isStaffInReserve){
                        $tmp_reserve_result = 3;
                    }else{
                        $isStaffInApply = $this->shift_model->isStaffInApply($staff['staff_id'], $organ_id, $from_time, $to_time);
                        if ($isStaffInApply){
                            $tmp_reserve_result = 1;
                        }else{
                            $isStaffInRequest = $this->shift_model->isStaffInRequest($staff['staff_id'], $organ_id, $from_time, $to_time);
                            if ($isStaffInRequest){
                                $tmp_reserve_result = 2;
                            }
                        }
                    }

                    if ($tmp_reserve_result==1){
                        $reserve_result = 1;
                        break;
                    }

                    if ($tmp_reserve_result==2){
                        $reserve_result = 2;
                    }
               }

               return $reserve_result;

            }

            return '2';
        }else{
            /* is select staff */
            // $isStaffInReserve = $this->order_model->isStaffInReserve($staff_id, $from_time, $to_time);
            // if ($isStaffInReserve) return 3;

            // $isStaffInRequest = $this->shift_model->isStaffInRequest($staff_id, $organ_id, $from_time, $to_time);
            // if ($isStaffInReject) return 2;

            // $isStaffInReject = $this->shift_model->isStaffInReject($staff_id, $organ_id, $from_time, $to_time);
            // if ($isStaffInReject) return 3;

            $isStaffInApply = $this->shift_model->isStaffInApply($staff_id, $organ_id, $from_time, $to_time);
            if ($isStaffInApply) return 1;

            return 3;
        }
    }

    public function updateReceiptUserName(){
        $reserve_id = $this->input->post('reserve_id');
        $update_user_name = $this->input->post('update_user_name');
        $reserve = $this->reserve_model->getFromId($reserve_id);
        $reserve['update_user_name'] = $update_user_name;

        $this->reserve_model->updateRecord($reserve, 'reserve_id');
        $results['isUpdate'] = true;
        echo json_encode($results);
    }
    public function updateFreeReserveAuto(){
        $reserve_date = $this->input->post('reserve_date');
        $organ_id = $this->input->post('organ_id');
        $staff_id = $this->input->post('staff_id');

        $reserves = $this->reserve_model->getFreeReserve($organ_id, $reserve_date);

        foreach ($reserves as $reserve){
            $least_staff = $this->reserve_model->getLeastReserveStaff($organ_id, $reserve_date, $staff_id);
            $reserve['staff_id'] = $least_staff['show_staff_id'];
            $this->reserve_model->updateRecord($reserve, 'reserve_id');
        }
        $results['isUpdate'] = true;
        echo json_encode($results);
    }

    public function updateReserveItem(){
        $reserve_id = $this->input->post('reserve_id');
        $staff_id = $this->input->post('staff_id');
        $reserve_time = $this->input->post('reserve_time');

        $reserve = $this->reserve_model->getFromId($reserve_id);
        $from = $reserve['reserve_time'];
        $to = $reserve['reserve_exit_time'];

        $origin = date_create($from);
        $target = date_create($to);
        $interval = date_diff($origin, $target);
        $d1 = new DateTime($reserve_time);
        $d1->add($interval);

        $reserve['staff_id'] = $staff_id;
        $reserve['reserve_time'] = $reserve_time;
        $reserve['reserve_exit_time'] = $d1->format('Y-m-d H:i:s');

        $this->reserve_model->updateRecord($reserve, 'reserve_id');
        $results['isUpdate'] = true;
        echo json_encode($results);
    }

    public function loadReserves(){
        $condition = $this->input->post('condition');
        $cond = json_decode($condition, true);

        $reserves = $this->reserve_model->getListByCond($cond);

        $results['isLoad'] = true;
        $results['reserves'] = $reserves;

        echo json_encode($results);
    }
}
?>
