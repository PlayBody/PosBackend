<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . 'core/WebController.php';

/*
 *
 */

class Reserve extends WebController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('staff_organ_model');
        $this->load->model('organ_model');
        $this->load->model('user_model');
        $this->load->model('menu_model');
        $this->load->model('shift_model');
        $this->load->model('order_model');
        $this->load->model('reserve_ticket_model');
        $this->load->model('order_menu_model');
        $this->load->model('staffs/staff_attend_model');
    }

    public function loadReserveConditions()
    {
        
        // $organ_id = 4;$this->input->post('organ_id');
        // $select_staff_type = 0;$this->input->post('select_staff_type');
        // $from_time = '2022-11-13 20:00:00';$this->input->post('from_time');
        // $to_time = '2022-11-13 23:59:59';$this->input->post('to_time');
        // $user_id = 247;$this->input->post('user_id');
        // $staff_id = $this->input->post('staff_id');
        // $duration = 45;$this->input->post('duration');
        // $multi_number = 2;$this->input->post('multi_number');
        
        $organ_id = $this->input->post('organ_id');
        $select_staff_type = $this->input->post('select_staff_type');
        $from_time = $this->input->post('from_time');
        $to_time = $this->input->post('to_time');
        $user_id = $this->input->post('user_id');
        $staff_id = $this->input->post('staff_id');
        $duration = $this->input->post('duration');
        $multi_number = $this->input->post('multi_number');

        $cur_date = $from_time;

        while($cur_date<=$to_time){
            $curDateTime = new DateTime($cur_date);
            if ($curDateTime > new DateTime()){
                $tmp = [];
                $tmp['time'] = $cur_date;
                $tmp['type'] = $this->getReserveTimeStatus($cur_date, $organ_id, $user_id, $select_staff_type, $staff_id, $duration, $multi_number);

                // start add by shengben
                if ($tmp['type'] == RESERVE_CONDITION_DISABLE) {
                    $currentAttendData = $this->staff_attend_model->currentAttendData($organ_id, $curDateTime->format("Y-m-d H:i:s"));
                    if (count($currentAttendData) >= $multi_number) {
                        $tmp['type'] = RESERVE_CONDITION_OK;
                    }
                }
                // end add

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

    public function saveReserveByUser(){
        // $organ_id = 4;$this->input->post('organ_id');
        // $user_id = 247;$this->input->post('user_id');
        // $from_time = '2022-11-19 15:40:00';$this->input->post('reserve_start_time');
        // $to_time = '2022-11-19 16:20:00';$this->input->post('reserve_end_time');
        // $reserve_menu = '{"189":{"no":"1","menu":{"menu_id":"89","menu_price":"4400","multi_number":null}},"268":{"no":"2","menu":{"menu_id":"68","menu_price":"2200","multi_number":null}}}';$this->input->post('reserve_menu');
        // $reserve_ticket = $this->input->post('use_ticket');
        // $coupon_id = $this->input->post('coupon_id');
        // $pay_method = 2;$this->input->post('pay_method');
        // $coupon_use_amount = $this->input->post('coupon_use_amount');
        // $ticket_amount = $this->input->post('ticket_amount');
        // $amount = 6600;$this->input->post('amount');
        // $user_count = 2;$this->input->post('user_count');
        // $sum_time = 0;empty($this->input->post('sum_time')) ? 0 : $this->input->post('sum_time');
        // $user_2 = 'test';empty($this->input->post('user_2')) ? null : $this->input->post('user_2');
        // $user_3 = empty($this->input->post('user_3')) ? null : $this->input->post('user_3');
        // $user_4 = empty($this->input->post('user_4')) ? null : $this->input->post('user_4');
        // $staff_id = $this->input->post('staff_id');
        // $sel_staff_type = $this->input->post('sel_staff_type');
        // $reserve_status = 1;$this->input->post('reserve_status');

       $organ_id = $this->input->post('organ_id');
       $user_id = $this->input->post('user_id');
       $from_time = $this->input->post('reserve_start_time');
       $to_time = $this->input->post('reserve_end_time');
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
       $staff_id = $this->input->post('staff_id');
       $sel_staff_type = $this->input->post('sel_staff_type');
       $reserve_status = $this->input->post('reserve_status');

        $shift_status = $this->getShiftStatus($organ_id, $sel_staff_type, $staff_id, $from_time, $to_time, $user_count);
        // if ($shift_status['status'] == RESERVE_CONDITION_DISABLE || $shift_status['status'] != $reserve_status){
        //     echo json_encode(['isSave' => false]);
        //     return;
        // }

        if (isset($shift_status['staffs'])) {
            $staffs = $shift_status['staffs'];
        }else {
            $staffs = [$staff_id];
        }

        $order_ids = [];

        $staff_id = $staffs[0];
        $pos = $this->order_model->emptyMaxPosition([
            'organ_id' => $organ_id,
            'from_time' =>  $from_time,
            'to_time' => $to_time,
            'status_array' => [ORDER_STATUS_RESERVE_APPLY, ORDER_STATUS_RESERVE_REQUEST, ORDER_STATUS_TABLE_START, ORDER_STATUS_TABLE_END, ORDER_STATUS_TABLE_COMPLETE]
        ]);
        $user = $this->user_model->getFromId($user_id);
        $user_name = $user['user_first_name'] . ' ' . $user['user_last_name'];

        $order = array(
            'organ_id' => $organ_id,
            'table_position' => $pos,
            'amount' => $amount,
            'user_id' => $user_id,
            'select_staff_type' => empty($sel_staff_type) ? 0 : $sel_staff_type,
            'select_staff_id' => $staffs[0],
            'user_count'=>$user_count,
            'user_input_name' => $user_name,
            'other_name_1' => $user_2,
            'other_name_2' => $user_3,
            'other_name_3' => $user_4,
            'from_time' => $from_time,
            'to_time' => $to_time,
            'coupon_id' => empty($coupon_id)?null:$coupon_id,
            'pay_method' => empty($pay_method)?null:($pay_method==1 ? 1: null),
            'coupon_use_amount' => empty($coupon_use_amount)?null:$coupon_use_amount,
            'ticket_amount' => empty($ticket_amount) ? null : $ticket_amount,
            'status'=>$reserve_status == RESERVE_CONDITION_OK ? ORDER_STATUS_RESERVE_APPLY : ORDER_STATUS_RESERVE_REQUEST,
            'is_reserve'=>1,
        );

        $order_id = $this->order_model->insertRecord($order);
        $order_ids[1] = $order_id;

        if (empty($order_id)){
            $results['isSave'] = false;
            echo json_encode($results);
            return;
        }

        for ($i=1;$i<count($staffs);$i++){
            $staff_id = $staffs[$i];
            $pos = $this->order_model->emptyMaxPosition([
                'organ_id' => $organ_id,
                'from_time' =>  $from_time,
                'to_time' => $to_time,
                'status_array' => [ORDER_STATUS_RESERVE_APPLY, ORDER_STATUS_RESERVE_REQUEST, ORDER_STATUS_TABLE_START, ORDER_STATUS_TABLE_END, ORDER_STATUS_TABLE_COMPLETE]
            ]);
            $user_name = '';
            switch ($i){
                case 1:
                    $user_name = $user_2; break;
                case 2:
                    $user_name = $user_3; break;
                case 3:
                    $user_name = $user_4; break;
            }
            $order = array(
                'root_id' => $order_id,
                'number' => $i+1,
                'organ_id' => $organ_id,
                'table_position' => $pos,
                'user_id' => $user_id,
                'select_staff_type' => 0,
                'select_staff_id' => $staffs[$i],
                'user_count'=>$user_count,
                'user_input_name' => $user_name,
                'other_name_1' => $user_2,
                'other_name_2' => $user_3,
                'other_name_3' => $user_4,
                'from_time' => $from_time,
                'to_time' => $to_time,
                'coupon_id' => empty($coupon_id)?null:$coupon_id,
                'pay_method' => empty($pay_method)?null:($pay_method==1 ? 1: null),
                'coupon_use_amount' => empty($coupon_use_amount)?null:$coupon_use_amount,
                'ticket_amount' => empty($ticket_amount) ? null : $ticket_amount,
                'status'=>$reserve_status == RESERVE_CONDITION_OK ? ORDER_STATUS_RESERVE_APPLY : ORDER_STATUS_RESERVE_REQUEST,
                'is_reserve'=>1,
            );

            $order_ids[$i+1] = $this->order_model->insertRecord($order);
        }

        $data = json_decode($reserve_menu, true);
        if (!empty($data)){
            $interval = [];
            for($i=1;$i<=$user_count; $i++){
                $interval[$i] = 0;
            }

            foreach ($data as $item) {
                $no = $item['no'];
                $record = $item['menu'];

                $menu = $this->menu_model->getFromId($record['menu_id']);
                $menu_interval[$no] = empty($menu['menu_interval']) ? 0 : $menu['menu_interval'];

                if ($interval[$no]<$menu_interval[$no]) $interval[$no] = $menu_interval[$no];
                $insertData = [];
                $insertData = array(
                    'order_id' => $order_ids[$no],
                    'menu_id' => $record['menu_id'],
                    'menu_title' => $menu['menu_title'],
                    'menu_price' => $record['menu_price'],
                    'quantity' => 1
                );

                $insert = $this->order_menu_model->insertRecord($insertData);
            }

            foreach ($interval as $no=>$item){
                $reserveData = $this->order_model->getFromId($order_ids[$no]);
                $reserveData['interval'] = $interval[$no];
                $this->order_model->updateRecord($reserveData);
            }
        }

        $tickets = json_decode($reserve_ticket);
        if (!empty($tickets)){
            foreach ($tickets as $record) {
                $insertData = array(
                    'reserve_id' => $order_id,
                    'ticket_id' => $record->ticket_id,
                    'use_count' => $record->use_count,
                );

                $insert = $this->reserve_ticket_model->insertRecord($insertData);
            }
        }
        $results['isFCM'] = $this->sendNotificationToStaffReserveRequest($order_id);

        $results['isSave'] = true;
        echo json_encode($results);
    }

    private function getReserveTimeStatus($sel_time, $organ_id, $user_id, $staff_type, $staff_id, $duration, $multi_number=1){
        $curFromTime = new DateTime($sel_time);
        $curToTime = new DateTime($sel_time);
        $curToTime->add(new DateInterval('PT'.$duration.'M'));
        $from_time = $curFromTime->format("Y-m-d H:i:s");
        $to_time = $curToTime->format("Y-m-d H:i:s");
        if ($this->isExistMyReserve($user_id, $organ_id, $from_time, $to_time)) return RESERVE_CONDITION_DISABLE;

        $shift_status = $this->getShiftStatus($organ_id, $staff_type, $staff_id, $from_time, $to_time, $multi_number);

        //        $shift_status = $this->getStaffShiftStatus($organ_id, $staff_type, $staff_id, $from_time, $to_time);
        return $shift_status['status'];
    }

   private function isExistMyReserve($user_id, $organ_id, $from_time, $to_time){
       $my_reserves = $this->order_model->getListByCond([
           'user_id' => $user_id,
           'organ_id' => $organ_id,
           'in_from_time' => $from_time,
           'in_to_time' => $to_time,
           'is_with_interval' => 1,
           'status_array' => [ORDER_STATUS_RESERVE_APPLY, ORDER_STATUS_RESERVE_REQUEST],
       ]);

       return !empty($my_reserves);
   }

//    private function getStaffShiftStatus($organ_id, $staff_type, $staff_id, $from_time, $to_time){
//
//        $cond = [];
//        if ($staff_type == 0) $cond['organ_id'] = $organ_id;
//        if ($staff_type == 1 || $staff_type == 2){
//            $cond['staff_sex'] = $staff_type;
//            $cond['organ_id'] = $organ_id;
//        }
//        if ($staff_type == 3) $cond['staff_id'] = $staff_id;
//
//        $staffs = $this->staff_organ_model->getStaffs($cond);
//
//        $status = RESERVE_CONDITION_DISABLE;
//        $sel_staff_id = '';
//        foreach ($staffs as $staff){
//            $reserves = $this->order_model->getListByCond([
//                'organ_id' => $organ_id,
//                'in_from_time' => $from_time,
//                'in_to_time' => $to_time,
//                'staff_id' => $staff['staff_id'],
//                'is_with_interval' => 1,
//                'status_array' => [ORDER_STATUS_RESERVE_APPLY, ORDER_STATUS_RESERVE_REQUEST],
//            ]);
//
//            if (!empty($reserves)) continue;
//
//            $shift_status = $this->getReserveShiftStatus($organ_id, $staff['staff_id'], $from_time, $to_time);
//
//            if ($shift_status == RESERVE_CONDITION_OK){
//                $sel_staff_id = $staff['staff_id'];
//                $status = RESERVE_CONDITION_OK;
//                break;
//            }
//
//            if ($shift_status == RESERVE_CONDITION_ENABLE){
//                $sel_staff_id = $staff['staff_id'];
//                $status = RESERVE_CONDITION_ENABLE;
//            }
//
//        }
//        return ['staff_id' => $sel_staff_id, 'status'=>$status];
//    }
//
//    private function getReserveShiftStatus($organ_id, $staff_id, $from_time, $to_time){
//        $shifts = $this->shift_model->getListByCond([
//            'organ_id' => $organ_id,
//            'staff_id' => $staff_id,
//            'in_from_time' => $from_time,
//            'in_to_time' => $to_time,
//            'reserve_flag' => 1
//        ]);
//        //ok Shifts.
//        $links = [];
//        $last = [];
//        foreach ($shifts as $shift){
//            if ($shift['shift_type'] == SHIFT_STATUS_OUT) continue;
//            if (empty($last)){
//                $last = $shift;
//            }else{
//                if ($last['to_time'] == $shift['from_time']){
//                    $last['to_time'] = $shift['to_time'];
//                }else{
//                    $links[] = $last;
//                    $last = $shift;
//                }
//            }
//        }
//        if (!empty($last)) $links[] = $last;
//        foreach ($links as $link){
//            if ($link['from_time']<=$from_time && $link['to_time']>=$to_time) return RESERVE_CONDITION_OK;
//        }
//
//        //enable Shifts.
//        $links = [];
//        $last = [];
//        foreach ($shifts as $shift){
//            if (empty($last)){
//                $last = $shift;
//            }else{
//                if ($last['to_time'] == $shift['from_time']){
//                    $last['to_time'] = $shift['to_time'];
//                }else{
//                    $links[] = $last;
//                    $last = $shift;
//                }
//            }
//        }
//        if (!empty($last)) $links[] = $last;
//
//        foreach ($links as $link){
//            if ($link['from_time']<=$from_time && $link['to_time']>=$to_time) return RESERVE_CONDITION_ENABLE;
//        }
//
//        return RESERVE_CONDITION_DISABLE;
//    }

    private function getShiftStatus($organ_id, $staff_type, $staff_id, $from_time, $to_time, $multi_number = 1){

        $shift_condition = [
            'organ_id' => $organ_id,
            'in_from_time' => $from_time,
            'in_to_time' => $to_time,
            'reserve_flag' => 1
        ];
        if (!empty($staff_id)) $shift_condition['staff_id'] = $staff_id;
        if ($staff_type == 1 || $staff_type == 2) $shift_condition['staff_sex'] = $staff_type;
        $shifts = $this->shift_model->getListByCond($shift_condition);
        $reserve_cond = RESERVE_CONDITION_DISABLE;
        $staffs = [];

        $ok_staffs = $this->getStaffsOkOrEnable($shifts, $from_time, $to_time, 1);
        if (!empty($ok_staffs)){
            $reserve_cond = RESERVE_CONDITION_OK;
            $staffs[] = $ok_staffs[0];
        }else{
            if ($staff_type == 1 || $staff_type == 2){
                
            }else{
                $enable_staffs = $this->getStaffsOkOrEnable($shifts, $from_time, $to_time, 2);
                if (!empty($enable_staffs)){
                    $reserve_cond = RESERVE_CONDITION_ENABLE;
                    $staffs[] = $enable_staffs[0];
                }
            }
        }
       
        if ($reserve_cond == RESERVE_CONDITION_DISABLE) return ['status' => RESERVE_CONDITION_DISABLE];
        if ($multi_number==1) return ['status' => $reserve_cond, 'staffs' => $staffs];

        // Multi ReserveCondition
        $shift_condition = [
            'organ_id' => $organ_id,
            'in_from_time' => $from_time,
            'in_to_time' => $to_time,
            'reserve_flag' => 1
        ];
        $shifts = $this->shift_model->getListByCond($shift_condition);

        if($reserve_cond == RESERVE_CONDITION_OK){
            $ok_staffs = $this->getStaffsOkOrEnable($shifts, $from_time, $to_time, 1);
            $ok_staffs = array_diff($ok_staffs, $staffs);

            if (count($ok_staffs)>=$multi_number-1){
                return ['status' => RESERVE_CONDITION_OK, 'staffs' => array_merge($staffs, array_slice($ok_staffs, 0, $multi_number-1))] ;
            }
        }

        // $enable_staffs = $this->getStaffsOkOrEnable($shifts, $from_time, $to_time, 2);

        // $enable_staffs=  array_diff($enable_staffs, $staffs);
        // if (count($enable_staffs)>=$multi_number-1){
        //     return ['status' => RESERVE_CONDITION_ENABLE, 'staffs' => array_merge($staffs, array_slice($enable_staffs, 0,$multi_number-1))] ;
        // }

        return ['status' => RESERVE_CONDITION_DISABLE];
    }

    private function getStaffsOkOrEnable($shifts, $from_time, $to_time, $flag = 1){
        $new_shifts = [];
        $last_staff_id = '';
        foreach ($shifts as $shift){
            $reserves = $this->order_model->getListByCond([
                'organ_id' => $shift['organ_id'],
                'in_from_time' => $from_time,
                'in_to_time' => $to_time,
                'staff_id' => $shift['staff_id'],
                'is_with_interval' => 1,
                'status_array' => [ORDER_STATUS_RESERVE_APPLY, ORDER_STATUS_RESERVE_REQUEST],
            ]);

            if (!empty($reserves)) continue;

            if ($flag==1 && $shift['shift_type'] == SHIFT_STATUS_OUT) continue;
            if ($last_staff_id == $shift['staff_id']){
                if ($last_shift['to_time'] == $shift['from_time']) {
                    $last_shift['to_time'] = $shift['to_time'];
                }else{
                    $staff_shifts[] = $last_shift;
                    $last_shift = $shift;
                }
            }else{
                if (!empty($last_staff_id)){
                    $staff_shifts[] = $last_shift;
                    $new_shifts[$last_staff_id] = $staff_shifts;
                }
                $staff_shifts = [];
                $last_staff_id = $shift['staff_id'];
                $last_shift = $shift;
            }
        }

        if (!empty($last_shift)){
            $staff_shifts[] = $last_shift;
            $new_shifts[$last_staff_id] = $staff_shifts;
        }
        $ok_staffs = [];
        foreach ($new_shifts as $key => $items){
            foreach ($items as $item){
                if ($item['from_time']<=$from_time && $item['to_time']>=$to_time){
                    $ok_staffs[] = $key;
                    break;
                }
            }
        }
        return $ok_staffs;
    }

    private function sendNotificationToStaffReserveRequest($order_id){
        $order = $this->order_model->getFromId($order_id);
        $order_menus = $this->order_menu_model->getListByCond(['order_id'=>$order_id]);
        $str_menus = '';
        foreach ($order_menus as $menu){
            if($str_menus!='') $str_menus = $str_menus . ', ';
            $str_menus = $str_menus . $menu['menu_title'];
        }

        $user = $this->user_model->getFromId($order['user_id']);
        $organ = $this->organ_model->getFromId($order['organ_id']);

        $reserve_time = new DateTime($order['from_time']);

        $this->load->model('notification_text_model');
        $text_data = $this->notification_text_model->getRecordByCond(['company_id'=>$user['company_id'], 'mail_type'=>'13']);
        $title = empty($text_data['title']) ? 'タイトルなし' : $text_data['title'];
        $content = empty($text_data['content']) ? '' : $text_data['content'];
        $content = str_replace('$organ_name', $organ['organ_name'], $content);
        $content = str_replace('$user_name', $user['user_first_name'].' '.$user['user_last_name'], $content);
        $content = str_replace('$reserve_time', $reserve_time->format('n月j日 H時i分'), $content);
        $content = str_replace('$menus', $str_menus, $content);
        $content = str_replace('$user_comment', '', $content);
        
        $is_fcm = $this->sendNotifications('13', $title, $content, $order['user_id'], $order['select_staff_id'], '1', $order_id);

        return true;

    }
}
?>
