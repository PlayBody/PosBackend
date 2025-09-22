<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/core/UpdateController.php';

class Scheduler extends UpdateController
{
    /**
     * This is default constructor of the class
     */
    public $company_id = 2;

    public function __construct()
    {
        parent::__construct(ROLE_STAFF);
        if ($this->staff['staff_auth'] < 4) {
            redirect('login');
        }

        $this->header['page'] = 'epark';
        $this->header['sub_page'] = 'receipt';
        $this->header['title'] = '予約受付'; //Reservation reception
//
        $this->load->model('organ_model');
        $this->load->model('organ_shift_time_model');
        $this->load->model('shift_model');

        $this->load->model('staff_shift_sort_model');
        $this->load->model('staff_organ_model');
        $this->load->model('table_name_model');
        $this->load->model('user_model');
        $this->load->model('menu_model');
        $this->load->model('category_model');
        $this->load->model('order_model');
        $this->load->model('order_menu_model');
        $this->load->model('shift_lock_model');

    }

    /**
     * This function used to load the first screen of the user
     */
    public function index()
    {
        $company_id = 2;

        $time_mode = $this->input->post('time_mode');
        $type_mode = $this->input->post('type_mode');


        // $this->load->helper('epark');
        // $epark_helper = new Epark_helper($company_id);
        // $r = $epark_helper->loadEparkReceipt(50, '2023-12-30');
        // die();


        $select_date = date('Y-m-d');

        $today = date('Y-m-d');

        $this->data['select_date'] = $select_date;
        $this->data['today_date'] = $today;

        $organ_list = $this->organ_model->getListByCond(['company_id' => $company_id]);
        if (empty($organ_id)) $organ_id = $organ_list[0]['organ_id'];

        $this->data['time_mode'] = empty($time_mode) ? 2 : $time_mode;
        $this->data['type_mode'] = empty($type_mode) ? 1 : $type_mode;

        $this->data['organs'] = $organ_list;
        $this->data['sel_organ_id'] = $organ_id;

        $this->load_view_with_menu("epark/scheduler");
    }

    public function ajaxMainLoad()
    {

        $company_id = 2;

        $time_mode = $this->input->post('time_mode');
        $type_mode = $this->input->post('type_mode');
        $sel_time = $this->input->post('sel_time');

        $organ_id = $this->input->post('organ_id');
        $select_date = $this->input->post('select_date');

        // $this->load->helper('epark');
        // $epark_helper = new Epark_helper($company_id);
        // $r = $epark_helper->loadEparkReceipt($organ_id, $select_date);

        $shifts = $this->shift_model->getDayShift($organ_id, $select_date);
        foreach ($shifts as $shift){
            $cond = [
                'staff_id' => $shift['staff_id'],
                'organ_id' => $shift['organ_id'],
                'to_time' => $shift['from_time'],
                'shift_type' => $shift['shift_type'],
            ];
            if ($shift['shift_type']==SHIFT_STATUS_REQUEST || $shift['shift_type']==SHIFT_STATUS_ME_REPLY){
                $cond['old_shift'] = $shift['old_shift'];
            }
            $prev_shift = $this->shift_model->getOneByParam($cond);
            if (!empty($prev_shift)){
                $prev_shift['to_time'] = $shift['to_time'];
                $this->shift_model->updateRecord($prev_shift, 'shift_id');
                $this->shift_model->delete_force($shift['shift_id'], 'shift_id');
            }
        }

        $organ_time = $this->getOrganAvailableTime($organ_id, $select_date);

        $staffs = $this->getAvailableStaffs($organ_id, $select_date);
        $staffs = $this->loadReserves($staffs, $organ_id, $select_date);
        $showStaffs = [];
        foreach ($staffs as $staff){
            if (empty($staff['shifts']) && empty($staff['orders'])) continue;
            $showStaffs[] = $staff;
        }

        $waiting_orders = $this->order_model->getListByCond(['organ_id' => $organ_id, 'is_waiting' => 1]);

        $waitings = [];
        foreach ($waiting_orders as $order){
            $order['interval'] = empty($order['interval']) ? 0 : $order['interval'];
            $order_menus = $this->order_menu_model->getFirstMenu($order['id']);
            if ($order['is_reset']){
                $order['color'] = '#999999';
            }else{
                $order['color'] = empty($order_menus['color']) ? "#cfcfcf" : $order_menus['color'];
            }
            $order['border_color'] = $this->getDarkColor($order['color'], 1.5);
            $order['interval_color'] = $this->getLightColor($order['color'], 0.5);
            $waitings[] = $order;
        }


        //var_dump($showStaffs);die();
        $table_orders = $this->loadTableReserves($showStaffs);
        $tables = $this->getTables($organ_id);

        $epark['staffs'] = $showStaffs;
        $epark['tables'] = $tables;
        $epark['waitings'] = $waitings;
        $epark['table_orders'] = $table_orders;


        $time_mode = empty($time_mode) ? 2 : $time_mode;
        $sel_time = empty($sel_time) ? $organ_time['open_time'] / 60 : $sel_time;

        $organ_time['show_open_time'] = $sel_time * 60;
        $organ_time['show_close_time'] = (($sel_time + 4) * 60) > $organ_time['close_time'] ? $organ_time['close_time'] : (($sel_time + 4) * 60);

        $epark['organ_time'] = $organ_time;


        $isLock  = $this->shift_lock_model->isLockSelectDate($select_date, $organ_id);

        $this->data = $epark;
        $this->data['isLock'] = $isLock;
        $this->data['time_mode'] = $time_mode;
        $this->data['type_mode'] = empty($type_mode) ? 1 : $type_mode;
        $this->data['sel_time'] = $sel_time;


        $this->load_view_empty("epark/scheduler_ajax_main");
    }

    private function getOrganAvailableTime($organ_id, $select_date)
    {
        $open_time = 60 * 8;
        $close_time = 60 * 23;
        $real_open_time = $open_time;
        $real_close_time = $close_time;
        $timestamp = strtotime($select_date);
        $week_num = date('N', $timestamp);
        $organ_time_row = $this->organ_shift_time_model->getMinMaxTimeByCond([
            'organ_id' => $organ_id,
            'weekday' => $week_num
        ]);

        if (!empty($organ_time_row) && !empty($organ_time_row['from_time'])) {
            $organ_time_from = intval(mb_split(':', $organ_time_row['from_time'])[0]);
            $organ_time_from_minute = intval(mb_split(':', $organ_time_row['from_time'])[1]);
            $organ_time_to = intval(mb_split(':', $organ_time_row['to_time'])[0]);
            $organ_time_to_minute = intval(mb_split(':', $organ_time_row['to_time'])[1]);
//            if (intval($organ_time_row['to_time'].str_split(':')[0])>0) $organ_time_to++;

            $open_time = $organ_time_from * 60;
            $close_time = $organ_time_to_minute > 0 ? ($organ_time_to + 1) * 60 : $organ_time_to * 60;

            $real_open_time = $organ_time_from * 60 + $organ_time_from_minute;
            $real_close_time = $organ_time_to * 60 + $organ_time_to_minute;
        }

        return ['open_time' => $open_time, 'close_time' => $close_time, 'real_open_time' => $real_open_time, 'real_close_time' => $real_close_time];
    }

    private function getAvailableStaffs($organ_id, $select_date)
    {
        $sort_staffs = $this->staff_shift_sort_model->getSortList($this->staff['staff_id']);
        $all_staffs = $this->staff_organ_model->getStaffs(['organ_id' => $organ_id]);
        $free_staffs['staff_id'] = '0';
        $free_staffs['staff_first_name'] = 'フリー';
        $free_staffs['staff_last_name'] = '';
        $free_staffs['staff_sex'] = '0';
        $all_staffs[] = $free_staffs;

        $available_staffs = [];
        foreach ($all_staffs as $staff) {
            $isExist = false;
            foreach ($sort_staffs as $sort_staff) {
                if ($sort_staff['show_staff_id'] == $staff['staff_id']) {
                    $isExist = true;
                    $sort = $sort_staff['sort'];
                    break;
                }
            }
            if (!$isExist) {
                $sort = $this->staff_shift_sort_model->getSortMax($this->staff['staff_id']);
                $add_sort_staff = [
                    'staff_id' => $this->staff['staff_id'],
                    'show_staff_id' => $staff['staff_id'],
                    'sort' => $sort
                ];
                $this->staff_shift_sort_model->insertRecord($add_sort_staff);

            }
            $tmp = $staff;
            $tmp['sort'] = $sort;
            $available_staffs[] = $tmp;
        }
        function sort_count($a, $b)
        {
            if ($a['sort'] === $b['sort']) {
                return 0;
            } else {
                return ($a['sort'] > $b['sort'] ? 1 : -1);
            }
        }

        $array = uasort($available_staffs, 'sort_count');

        $staffs = [];
        foreach ($available_staffs as $staff) {
            if ($staff['staff_id']==0){
                $shifts=[];
            }else{
                $shifts = $this->shift_model->getListByCond([
                    'staff_id' => $staff['staff_id'],
                    'select_date' => $select_date,
                    'organ_id' => $organ_id
                ]);
            }

            $tmp = [];
            $tmp = $staff;
            $tmp['shifts'] = $shifts;

            if (!empty($tmp)) $staffs[] = $tmp;
        }

        return $staffs;
    }

    private function loadReserves($staffs, $organ_id, $select_date){

        $n_staffs = [];

        foreach ($staffs as $staff){
            $orders = $this->order_model->getListByCond([
                'staff_id' => $staff['staff_id'],
                'organ_id' => $organ_id,
                'from_time' => $select_date.' 00:00:00',
                'to_time' => $select_date.' 23:59:59',
            ]);

            $n_orders = [];
            foreach ($orders as $order){
                $order['interval'] = empty($order['interval']) ? 0 : $order['interval'];
                $order_menus = $this->order_menu_model->getFirstMenu($order['id']);
                if ($order['is_reset']){
                    $order['color'] = '#999999';
                }else{
                    $order['color'] = empty($order_menus['color']) ? "#cfcfcf" : $order_menus['color'];
                }
                $order['border_color'] = $this->getDarkColor($order['color'], 1.5);
                $order['interval_color'] = $this->getLightColor($order['color'], 0.5);
                $n_orders[] = $order;
            }

            $staff['orders'] = $n_orders;
            $n_staffs[] = $staff;
        }

        return $n_staffs;
    }

    private function loadTableReserves($staffs){


        $n_orders = [];
        foreach ($staffs as $staff){
            foreach ($staff['orders'] as $order){

                $n_orders[$order['table_position']][] = $order;
            }
        }
        return $n_orders;
    }

    private function getTables($organ_id)
    {

        $organ = $this->organ_model->getFromId($organ_id);
        $table_count = empty($organ['table_count']) ? 10 : $organ['table_count'];
        $tables = [];

        $tables = [];
        for ($i = 1; $i <= $table_count; $i++) {
            $table_name_record = $this->table_name_model->getOneByParam(['organ_id' => $organ_id, 'table_position' => $i]);
            $tables[$i]['table_name'] = empty($table_name_record['table_name']) ? ('席' . $i) : $table_name_record['table_name'];
            $tables[$i]['table_position'] = $i;
        }
        return $tables;
    }

    public function loadReserveAddResource()
    {
        $organ_id = $this->input->post('organ_id');
        $menus = $this->menu_model->getMenuList(['organ_id' => $organ_id, 'is_user_menu' => 1]);
        $categories = $this->category_model->getCategoryList([], 'order_no');
        $staffs = $this->staff_organ_model->getStaffs(['organ_id' => $organ_id]);

        $users = $this->user_model->getListByCond(['company_id' => $this->company_id]);

        $results['users'] = $users;
        $results['staffs'] = $staffs;
        $results['menus'] = $menus;
        $results['categories'] = $categories;

        echo json_encode($results);
    }

    function saveUserReserve()
    {
        $organ_id = $this->input->post('organ_id');
        $user_id = $this->input->post('user_id');
        $staff_id = $this->input->post('staff_id');
        $sel_staff_type = 3;
        $reserve_start_time = $this->input->post('reserve_start_time');
        $menus = $this->input->post('menus');
        $pay_method = 2;

        $sum_time = 0;
        $interval = 0;
        $amount = 0;
        foreach ($menus as $menu_id) {
            $menu = $this->menu_model->getFromId($menu_id);
            $sum_time += empty($menu['menu_time']) ? 0 : $menu['menu_time'];
            $amount += empty($menu['menu_price']) ? 0 : $menu['menu_price'];
            $menu_interval = empty($menu['menu_interval']) ? 0 : $menu['menu_interval'];
            if ($menu_interval > $interval) $interval = $menu_interval;
        }

        $reserve_time = $reserve_start_time;
        $date = new DateTime($reserve_start_time);
        $date->add(new DateInterval('PT' . $sum_time . 'M'));
        $reserve_exit_time = $date->format("Y-m-d H:i:s");

        if (empty($organ_id) || empty($user_id)) {
            $results['isSave'] = false;
            echo json_encode($results);
            return;
        }


        $pos = $this->order_model->emptyMaxPosition([
            'organ_id' => $organ_id,
            'from_time' => $reserve_start_time,
            'to_time' => $reserve_exit_time,
            'status_array' => [ORDER_STATUS_RESERVE_APPLY, ORDER_STATUS_TABLE_START, ORDER_STATUS_TABLE_END, ORDER_STATUS_TABLE_COMPLETE]
        ]);

        if (empty($staff_id)) {
            $staffs = $this->staff_organ_model->getStaffsByOrgan($organ_id, STAFF_AUTH_ADMIN, false);

            $reserve_status = RESERVE_CONDITION_DISABLE;
            foreach ($staffs as $staff) {
                $status = $this->shift_model->getReserveShiftStatus($organ_id, $staff['staff_id'], $reserve_start_time, $reserve_exit_time);
                if ($status == RESERVE_CONDITION_ENABLE && $reserve_status == RESERVE_CONDITION_DISABLE) {
                    $reserve_status = RESERVE_CONDITION_ENABLE;
                    $staff_id = $staff['staff_id'];
                }
                if ($status == RESERVE_CONDITION_OK) {
                    $reserve_status = RESERVE_CONDITION_OK;
                    $staff_id = $staff['staff_id'];
                    break;
                }
            }
            if ($reserve_status == RESERVE_CONDITION_DISABLE) {
                $results['isSave'] = false;
                echo json_encode($results);
                return;
            }
        } else {
            $reserve_status = $this->shift_model->getReserveShiftStatus($organ_id, $staff_id, $reserve_start_time, $reserve_exit_time);
        }

        $order = array(
            'user_id' => $user_id,
            'organ_id' => $organ_id,
            'table_position' => $pos,
            'select_staff_type' => empty($sel_staff_type) ? 0 : $sel_staff_type,
            'select_staff_id' => $staff_id,
            'from_time' => $reserve_time,
            'to_time' => $reserve_exit_time,
            'amount' => $amount,
            'interval' => $interval,
            'status' => $reserve_status == RESERVE_CONDITION_OK ? ORDER_STATUS_RESERVE_APPLY : ORDER_STATUS_RESERVE_REQUEST,
            'is_reserve' => 1,
        );

        $order_id = $this->order_model->insertRecord($order);

        $this->sendNotificationToStaffReserveRequest($order_id);

        if (empty($order_id)) {
            $results['isSave'] = false;
            echo json_encode($results);
            return;
        }

        foreach ($menus as $menu_id) {
            $menu = $this->menu_model->getFromId($menu_id);
            $insertData = array(
                'order_id' => $order_id,
                'menu_id' => $menu_id,
                'menu_title' => $menu['menu_title'],
                'menu_price' => $menu['menu_price'],
                'quantity' => 1,
            );

            $insert = $this->order_menu_model->insertRecord($insertData);
        }


        $results['isSave'] = true;
        echo json_encode($results);

    }

    private function sendNotificationToStaffReserveRequest($order_id)
    {
        $order = $this->order_model->getFromId($order_id);
        $order_menus = $this->order_menu_model->getListByCond(['order_id' => $order_id]);
        $str_menus = '';
        foreach ($order_menus as $menu) {
            if ($str_menus != '') $str_menus = $str_menus . ', ';
            $str_menus = $str_menus . $menu['menu_title'];
        }

        $user = $this->user_model->getFromId($order['user_id']);
        $organ = $this->organ_model->getFromId($order['organ_id']);

        $reserve_time = new DateTime($order['from_time']);

        $this->load->model('notification_text_model');
        $text_data = $this->notification_text_model->getRecordByCond(['company_id' => $user['company_id'], 'mail_type' => '13']);
        $title = empty($text_data['title']) ? 'タイトルなし' : $text_data['title'];
        $content = empty($text_data['content']) ? '' : $text_data['content'];
        $content = str_replace('$organ_name', $organ['organ_name'], $content);
        $content = str_replace('$user_name', $user['user_first_name'] . ' ' . $user['user_last_name'], $content);
        $content = str_replace('$reserve_time', $reserve_time->format('n月j日 H時i分'), $content);
        $content = str_replace('$menus', $str_menus, $content);
        $content = str_replace('$user_comment', '', $content);

        error_reporting(E_ERROR | E_PARSE);
        try {
            $is_fcm = $this->sendNotifications('13', $title, $content, $order['user_id'], $order['select_staff_id'], '1', $order_id);
        } catch (Throwable $e) {

        }
    }

    function ajaxUpdateReserveTime(){
        $order_id = $this->input->post('reserve_id');
        $staff_id = $this->input->post('staff_id');
        $position = $this->input->post('position');
        $reserve_start_time = $this->input->post('reserve_time');
        $time_length = $this->input->post('time_length');

        $order = $this->order_model->getFromId($order_id);

        $start = strtotime($order['from_time']);
        $end = strtotime($order['to_time']);
        $mins = ($end - $start) / 60;
        $duration = $mins + (empty($order['interval']) ? 0 : $order['interval']);

        if (!$this->isEnableReserve($order['organ_id'], $reserve_start_time, $staff_id, $duration, $order_id)){
            $results['isSave'] = false;

            echo json_encode($results);
            return;
        }


        $reserve_time = $reserve_start_time;
        $date = new DateTime($reserve_start_time);
        $date->add(new DateInterval('PT'.$time_length.'M'));
        $reserve_exit_time = $date->format("Y-m-d H:i:s");


        $order['from_time'] = $reserve_time;
        $order['to_time'] = $reserve_exit_time;
        if (!empty($staff_id)) $order['select_staff_id'] = $staff_id;
        if (!empty($position)) $order['table_position'] = $position;
        $order['is_waiting'] = 0;

        $this->order_model->updateRecord($order);

        $results['isSave'] = true;
        echo json_encode($results);
    }

    private function isEnableReserve($organ_id, $sel_time, $staff_id, $duration, $reserve_id = ''){

        $date = new DateTime($sel_time);
        $date->add(new DateInterval('PT'.$duration.'M'));
        $to_time = $date->format("Y-m-d H:i:s");

        $is_exist_other_reserve = $this->order_model->getListByCond([
            'organ_id' => $organ_id,
            'in_from_time' => $sel_time,
            'in_to_time' => $to_time,
            'is_with_interval' => 1,
            'staff_id' => $staff_id,
            'self_order_id' => $reserve_id,
            'status_array' => [ORDER_STATUS_RESERVE_APPLY, ORDER_STATUS_RESERVE_REQUEST],
        ]);

        if (!empty($is_exist_other_reserve)){
            return false;
        }

        $status = $this->shift_model->getReserveShiftStatus($organ_id, $staff_id, $sel_time, $to_time);
        return $status == RESERVE_CONDITION_OK;
    }
    function ajaxMoveReserveWaiting(){
        $order_id = $this->input->post('order_id');
        $is_wait = $this->input->post('is_wait');

        $order = $this->order_model->getFromId($order_id);

        $order['is_waiting'] = $is_wait;

        $this->order_model->updateRecord($order);

        $results['isUpdate'] = true;
        echo json_encode($results);
    }

    private function getDarkColor($rgb, $darker=2){
        $hash = (strpos($rgb, '#') !== false) ? '#' : '';
        $rgb = (strlen($rgb) == 7) ? str_replace('#', '', $rgb) : ((strlen($rgb) == 6) ? $rgb : false);
        if(strlen($rgb) != 6) return $hash.'000000';
        //$darker = ($darker > 1) ? $darker : 1;

        list($R16,$G16,$B16) = str_split($rgb,2);

        $R = sprintf("%02X", floor(hexdec($R16)/$darker));
        $G = sprintf("%02X", floor(hexdec($G16)/$darker));
        $B = sprintf("%02X", floor(hexdec($B16)/$darker));

        return $hash.$R.$G.$B;
    }

    function getLightColor($hexcolor, $percent)
    {
        if ( strlen( $hexcolor ) < 6 ) {
            $hexcolor = $hexcolor[0] . $hexcolor[0] . $hexcolor[1] . $hexcolor[1] . $hexcolor[2] . $hexcolor[2];
        }
        $hexcolor = array_map('hexdec', str_split( str_pad( str_replace('#', '', $hexcolor), 6, '0' ), 2 ) );

        foreach ($hexcolor as $i => $color) {
            $from = $percent < 0 ? 0 : $color;
            $to = $percent < 0 ? $color : 255;
            $pvalue = ceil( ($to - $from) * $percent );
            $hexcolor[$i] = str_pad( dechex($color + $pvalue), 2, '0', STR_PAD_LEFT);
        }

        return '#' . implode($hexcolor);
    }

    public function loadReserveList(){
        $select_date = $this->input->post('select_date');
        $organ_id = $this->input->post('organ_id');

        $orders = $this->order_model->getListByCond([
            'organ_id' => $organ_id,
            'from_time' => $select_date.' 00:00:00',
            'to_time' => $select_date.' 23:59:59',
        ]);

        $reserves = [];
        foreach ($orders as $order){
            $menus = $this->order_menu_model->getListByCond(['order_id' => $order['id']]);

            $order['menus'] = $menus;
            $reserves[] = $order;
        }

        $this->data['orders'] = $reserves;

        $this->load_view_empty("epark/scheduler_ajax_reserve_list");
        //echo json_encode($reserves);

    }

    public function ajaxLoadUserReserves()
    {
        $user_id = $this->input->post('user_id');
        $service = $this->input->post('service');

        $cond['user_id'] = $user_id;
        $cond['status_array'] = [ORDER_STATUS_RESERVE_APPLY, ORDER_STATUS_RESERVE_REQUEST, ORDER_STATUS_TABLE_COMPLETE];
        $lists = $this->order_model->getListByCond($cond);

        $orders = [];
        foreach ($lists as $item){
            $cond_menu = [];
            $cond_menu['order_id'] = $item['id'];

            if (!empty($service)){
                if ($service == 1)
                    $cond_menu['is_service'] = 1;
                else
                    $cond_menu['is_goods'] = 1;
            }
            $menus = $this->order_menu_model->getListByCond($cond_menu);

            if (!empty($service) && empty($menus)) continue;
            $item['menus'] = $menus;
            $orders[] = $item;
        }
        $this->data['orders'] = $orders;

        $this->load_view_empty("epark/scheduler_ajax_user_reserves");
    }

    public function ajaxLoadShiftInfo()
    {
        $shift_id = $this->input->post('shift_id');
        $organ_from_time = $this->input->post('organ_from_time');
        $organ_to_time = $this->input->post('organ_to_time');
        $shift = $this->shift_model->getRecordByCond(['shift_id' => $shift_id]);

        $this->data['shift'] = $shift;
        $this->data['organ_from_time'] = $organ_from_time;
        $this->data['organ_to_time'] = $organ_to_time;
        $this->load_view_empty("epark/scheduler_ajax_shift_edit");
//        $this->load->view("epark/ajax_shift_info", ['shift' => $shift, 'organ_from_hour' => $organ_from_time, 'organ_to_hour'=>$organ_to_time]);
    }

    public function insertEparkShiftTest(){
        $shift = array(
            //'from_time' => '2023-12-29 09:00:00',
            //'to_time' => '2023-12-29 14:00:00',
            'staff_id' => 85,
            'organ_id' => 50,
            'shift_type' => SHIFT_STATUS_APPLY 
        );

        $this->load->helper('epark');
        $epark_helper = new Epark_helper(2);
        $r = $epark_helper->updateShiftToEpark(50, 75, '2024-01-17');
        
        print_r($r);
        die();
    }
}