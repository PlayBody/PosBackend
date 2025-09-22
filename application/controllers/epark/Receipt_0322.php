<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/core/AdminController.php';

class Receipt extends AdminController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct(ROLE_STAFF);
        if( $this->staff['staff_auth']<4){
            redirect('login');
        }

        $this->load->model('company_model');

        $this->header['page'] = 'epark';
        $this->header['sub_page'] = 'receipt';
        $this->header['title'] = '予約受付';

        $this->load->model('organ_model');
        $this->load->model('organ_shift_time_model');
        $this->load->model('shift_model');
        $this->load->model('reserve_model');
        $this->load->model('reserve_menu_model');
        $this->load->model('table_model');
        $this->load->model('user_model');
        $this->load->model('menu_model');
        $this->load->model('order_model');
        $this->load->model('order_menu_model');
        $this->load->model('staff_organ_model');
        $this->load->model('staff_shift_sort_model');
        $this->load->model('table_name_model');
        $this->load->model('shift_lock_model');

    }

    /**
     * This function used to load the first screen of the user
     */
    public function index()
    {
        $company_id = 2;

        $organ_id = $this->input->post('organ_id');
        $select_date = $this->input->post('select_date');
        $mod = $this->input->post('mod');

        $organ_list = $this->organ_model->getListByCond(['company_id'=>$company_id]);

        if(empty($organ_id)) $organ_id = $organ_list[0]['organ_id'];
        if(empty($select_date)) $select_date = date('Y-m-d');
        if(empty($mod)) $mod = 'shift';

        //---------------get shift_time _min _max ----------------
        $timestamp = strtotime($select_date);
        $week_num = date('N', $timestamp);
        $organ_time_row = $this->organ_shift_time_model->getMinMaxTimeByCond([
            'organ_id' => $organ_id,
            'weekday' => $week_num
        ]);

        $organ_time_from = 24;
        $organ_time_from_minute = 0;
        $organ_time_to = 0;
        $organ_time_to_minute = 0;

        if (!empty($organ_time_row) && !empty($organ_time_row['from_time'])){
            $organ_time_from = intval(mb_split(':', $organ_time_row['from_time'])[0]);
            $organ_time_from_minute = intval(mb_split(':', $organ_time_row['from_time'])[1]);
            $organ_time_to = intval(mb_split(':', $organ_time_row['to_time'])[0]);
            $organ_time_to_minute = intval(mb_split(':', $organ_time_row['to_time'])[1]);
//            if (intval($organ_time_row['to_time'].str_split(':')[0])>0) $organ_time_to++;

        }

        $reserve_counts = [];
        for($i=$organ_time_from;$i<=$organ_time_to;$i++){
            $reserves = $this->order_model->getListByCond([
                'organ_id'=>$organ_id,
                'in_from_time' => $select_date.' ' . ($i<10?'0':'') . $i .':00:00',
                'in_to_time' => $select_date.' ' . ($i==23 ? '23:59:59' : (($i+1)<10?'0':'') . ($i+1) .':00:00'),
                'status_array' => [ORDER_STATUS_RESERVE_APPLY, ORDER_STATUS_RESERVE_REQUEST],
            ]);

            $reserve_counts[$i] = empty($reserves) ? 0 : count($reserves);

        }

        $table_length = ($organ_time_to * 60 + $organ_time_to_minute)-($organ_time_from*60+$organ_time_from_minute);
        $table_start_time = $organ_time_from * 60 + $organ_time_from_minute;

        $total_length = ($organ_time_to - $organ_time_from + 1) * 60;
        //------------------------------------------------------------------

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

        $shifts = $this->shift_model->getDayShift($organ_id, $select_date);


        $staffs = [];
        foreach ($shifts as $shift){
            $datetime1 = date_create($shift['from_time']);
            $datetime2 = date_create($shift['to_time']);
            $diff = date_diff($datetime1, $datetime2);
            $shift_length_time = $diff->h * 60 + $diff->i;
            $shift_start_time = $datetime1->format('H') * 60 + $datetime1->format('i');

            if (!array_key_exists($shift['staff_id'], $staffs)) {
                $staffs[$shift['staff_id']] = ['name'=>$shift['staff_name'], 'sex'=>$shift['staff_sex']];
                $tmp_shift=[];
            }else{
                $tmp_shift=$staffs[$shift['staff_id']]['shifts'];
            }
            $shift['width'] = $shift_length_time;
            $shift['start'] = $shift_start_time;
            $tmp_shift[] = $shift;

            $staffs[$shift['staff_id']]['shifts'] = $tmp_shift;
        }

        /*table data load */
        $organ = $this->organ_model->getFromId($organ_id);
        $table_count = empty($organ['table_count']) ? 10 : $organ['table_count'];
        $tables = [];

        $tables = [];
        for ($i=1;$i<=$table_count;$i++){
            $table_name_record = $this->table_name_model->getOneByParam(['organ_id' => $organ_id, 'table_position' => $i]);
            $tables[$i]['table_name'] = empty($table_name_record['table_name']) ? ('席'.$i) : $table_name_record['table_name'];
        }

//        foreach($table_data as $table){
//            $tmp = [];
//            $tmp['table_id'] = $table['table_id'];
//            $tmp['table_title'] = $table['table_title'];
//            $tables[] = $tmp;
//        }

//        $reserves = $this->reserve_model->getReserveList(array(
//            'organ_id' => $organ_id,
//            'select_date' => $select_date,
//        ));

        $orders = $this->order_model->getListByCond([
            'organ_id' => $organ_id,
            'from_time' => $select_date.' 00:00:00',
            'to_time' => $select_date.' 23:59:59',
        ]);

        if(!empty($orders)){
            foreach ($orders as $order){
                $datetime1 = date_create($order['from_time']);
                $datetime2 = date_create($order['to_time']);
                $diff = date_diff($datetime1, $datetime2);
                $reserve_length_time = $diff->h * 60 + $diff->i;
                $reserve_start_time = $datetime1->format('H') * 60 + $datetime1->format('i');


                if(empty($order['select_staff_id'])){
                    $order['select_staff_id'] = 0;
                    $order['staff_name'] = 'フーリ';
                }
                if (!array_key_exists($order['select_staff_id'], $staffs)) {
                    $staffs[$order['select_staff_id']] = ['name'=>$order['staff_name'], 'sex'=>$order['user_sex'],];
                    $tmp_reserve=[];
                }else{
                    $tmp_reserve=empty($staffs[$order['select_staff_id']]['reserves']) ? [] : $staffs[$order['select_staff_id']]['reserves'];
                }

                $order['is_before'] = false;
                if($order['to_time']<date('Y-m-d H:i:s')){
                    $order['is_before'] = true;
                }
                $order['from'] = $datetime1->format('H:i');
                $order['to'] = $datetime2->format('H:i');
                $order['width'] = $reserve_length_time;
                $order['interval'] = empty($order['interval']) ? 0 : $order['interval'];
                $order['start'] = $reserve_start_time;
                $reserve_menus = $this->order_menu_model->getFirstMenu($order['id']);
                $order['color'] = $reserve_menus['color'];
                $tmp_reserve[] = $order;

                if (!empty($order['table_position'])){
                    $tables[$order['table_position']]['data'][] = $order;
                }

                $staffs[$order['select_staff_id']]['reserves'] = $tmp_reserve;

            }
        }


        $sort_staffs = $this->staff_shift_sort_model->getSortList($this->staff['staff_id']);
        $newstaffs = [];

        foreach ($sort_staffs as $staff){
            if(empty($staffs[$staff['show_staff_id']])) continue;
            $newstaffs[$staff['show_staff_id']] = $staffs[$staff['show_staff_id']];
        }

        foreach ($staffs as $key => $staff){
            if (empty($newstaffs[$key])){
                $shift_sort_staff = array(
                    'staff_id' => $this->staff['staff_id'],
                    'show_staff_id' => $key,
                    'sort'=>$this->staff_shift_sort_model->getSortMax($this->staff['staff_id'])
                );
                $this->staff_shift_sort_model->insertRecord($shift_sort_staff);
                $newstaffs[$key] = $staff;
            }
        }

        $isLock  = $this->shift_lock_model->isLockSelectDate($select_date, $organ_id);

        /* sum_amount */
        $cond_sum_amount=[];
        $cond_sum_amount['select_date'] = $select_date;
        $cond_sum_amount['status_array'] = [ORDER_STATUS_TABLE_COMPLETE];

        $cond_sum_amount['pay_method'] = PAY_METHOD_CASH;
        $cash_complete_amount = $this->order_model->getAmount($cond_sum_amount);
        $cond_sum_amount['pay_method'] = PAY_METHOD_CREDIT;
        $credit_complete_amount = $this->order_model->getAmount($cond_sum_amount);
        $cond_sum_amount['pay_method'] = PAY_METHOD_OTHER;
        $other_complete_amount = $this->order_model->getAmount($cond_sum_amount);

        $cond_menu_amount = [];
        $cond_menu_amount['select_date'] = $select_date;
        $cond_menu_amount['status_array'] = [ORDER_STATUS_TABLE_COMPLETE];

        $cond_menu_amount['is_goods'] = 1;
        $goods_complete_amount = $this->order_menu_model->getMenuAmount($cond_menu_amount);

        $cond_menu_amount['is_goods'] = 0;
        $cond_menu_amount['is_service'] = 1;
        $service_complete_amount = $this->order_menu_model->getMenuAmount($cond_menu_amount);

        //-------------- sum amount End ----------------------

        $users = $this->user_model->getListByCond(['company_id'=>$company_id]);
        $organ_staffs = $this->staff_organ_model->getStaffsByOrgan($organ_id, STAFF_AUTH_ADMIN, false, true);
        $organ_menus = $this->menu_model->getMenuList(['organ_id'=>$organ_id, 'is_user_menu'=>1]);

        $waiting_orders = $this->order_model->getListByCond(['organ_id' => $organ_id, 'is_waiting' => 1]);
        $waitings = [];
        foreach($waiting_orders as $order){
            $datetime1 = date_create($order['from_time']);
            $datetime2 = date_create($order['to_time']);
            $diff = date_diff($datetime1, $datetime2);
            $reserve_length_time = $diff->h * 60 + $diff->i;
            $reserve_start_time = $datetime1->format('H') * 60 + $datetime1->format('i');
            $tmp = $order;
            
            $tmp['is_before'] = false;
            if($order['to_time']<date('Y-m-d H:i:s')){
                $tmp['is_before'] = true;
            }
            $tmp['from'] = $datetime1->format('H:i');
            $tmp['to'] = $datetime2->format('H:i');
            $tmp['width'] = $reserve_length_time;
            $tmp['interval'] = empty($order['interval']) ? 0 : $order['interval'];
            $tmp['start'] = $reserve_start_time;
            $reserve_menus = $this->order_menu_model->getFirstMenu($order['id']);
            $tmp['color'] = $reserve_menus['color'];
            $waitings[] = $tmp;
        }
        $this->data['waitings'] = $waitings;

        $this->data['mod'] = $mod;

        $this->data['organ_id'] = $organ_id;
        $this->data['select_date'] = $select_date;
        $this->data['organs'] = $organ_list;
        $this->data['menus'] = $organ_menus;
        $this->data['is_lock'] = $isLock;

        $this->data['available_time_from'] = $organ_time_from;
        $this->data['available_time_to'] = $organ_time_to;
        $this->data['reserve_counts'] = $reserve_counts;
        $this->data['one_minute_length'] = 1 / $total_length * 100;
        $this->data['staffs'] = $newstaffs;
        $this->data['tables'] = $tables;
        $this->data['table_length'] = $table_length;
        $this->data['users'] = $users;
        $this->data['table_start_time'] = $table_start_time;

        $this->data['cash_complete_amount'] = $cash_complete_amount;
        $this->data['credit_complete_amount'] = $credit_complete_amount;
        $this->data['other_complete_amount'] = $other_complete_amount;

        $this->data['goods_complete_amount'] = $goods_complete_amount;
        $this->data['service_complete_amount'] = $service_complete_amount;


        $this->data['organ_staffs'] = $organ_staffs;
        $this->load_view_with_menu("epark/receipt");
    }

    public function getAjaxLeftReserveList()
    {
        $organ_id = $this->input->get_post('organ_id');
        $select_date = $this->input->get_post('select_date');
        $cond['organ_id'] = $organ_id;
        $cond['from_time'] = $select_date.' 00:00:00';
        $cond['to_time'] = $select_date.' 23:59:59';
        $lists = $this->order_model->getListByCond( $cond);

        $orders = [];
        foreach ($lists as $item){
            $menus = $this->order_menu_model->getDataByParam(['order_id' => $item['id']]);

            $item['menus'] = $menus;
            $orders[] = $item;
        }

        $this->load->view("epark/ajax_reserve_list", ['orders' => $orders]);
    }

    public function getAjaxLeftUserList()
    {
        $user_name = $this->input->post('user_name');
        $cond['user_name'] = $user_name;
        $company_id = 2;
        $cond['company_id'] = $company_id;

        if(empty($user_name))
            $users = [];
        else
            $users = $this->user_model->getListByCond($cond);

        $this->load->view("epark/ajax_user_list", ['users' => $users]);
    }
    public function getAjaxUserInfo()
    {
        $user_id = $this->input->post('user_id');

        $user = $this->user_model->getFromId($user_id);

        $this->load->view("epark/ajax_user_info", ['user' => $user]);
    }
    public function getAjaxUserReserveList()
    {
        $user_id = $this->input->post('user_id');
        $is_goods = $this->input->post('is_goods');
        $is_service = $this->input->post('is_service');
        $cond['user_id'] = $user_id;
        $cond['status_array'] = [ORDER_STATUS_RESERVE_APPLY, ORDER_STATUS_RESERVE_REQUEST, ORDER_STATUS_TABLE_COMPLETE];
        $lists = $this->order_model->getListByCond($cond);

        $orders = [];
        foreach ($lists as $item){
            $cond_menu = [];
            $cond_menu['order_id'] = $item['id'];
            if (!empty($is_goods)) $cond_menu['is_goods'] = 1;
            if (!empty($is_service)) $cond_menu['is_service'] = 1;
            $menus = $this->order_menu_model->getListByCond($cond_menu);

            if ((!empty($is_goods) || !empty($is_service)) && empty($menus)) continue;
            $item['menus'] = $menus;
            $orders[] = $item;
        }

        $this->load->view("epark/ajax_user_reserves", ['orders' => $orders]);
    }
    public function getAjaxReserveInfo()
    {
        $reserve_id = $this->input->post('reserve_id');
        $reserves = $this->order_model->getListByCond(["order_id" => $reserve_id]);
        $reserve = $reserves[0];
//        $reserves = [];
        $menus = $this->order_menu_model->getDataByParam(['order_id'=> $reserve_id]);
//
//            $item['menus'] = $menus;
//            $reserves[] = $item;
//        }

        $this->load->view("epark/ajax_reserve_info", ['reserve' => $reserve, 'menus' =>$menus]);
    }
    public function getAjaxShiftInfo()
    {
        $shift_id = $this->input->post('shift_id');
        $organ_from_time = $this->input->post('organ_from_time');
        $organ_to_time = $this->input->post('organ_to_time');
        $shift = $this->shift_model->getRecordByCond(['shift_id' => $shift_id]);


        $this->load->view("epark/ajax_shift_info", ['shift' => $shift, 'organ_from_hour' => $organ_from_time, 'organ_to_hour'=>$organ_to_time]);
    }

    public function ajaxDeleteShift()
    {
        $shift_id = $this->input->post('shift_id');
        $this->shift_model->delete_force($shift_id, 'shift_id');

        echo true;
        return;
    }
    
    function saveUserReserve(){
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
        foreach ($menus as $menu_id){
            $menu = $this->menu_model->getFromId($menu_id);
            $sum_time += empty($menu['menu_time']) ? 0 : $menu['menu_time'];
            $amount += empty($menu['menu_price']) ? 0 : $menu['menu_price'];
            $menu_interval = empty($menu['menu_interval']) ? 0 : $menu['menu_interval'];
            if($menu_interval>$interval) $interval = $menu_interval;
        }

        $reserve_time = $reserve_start_time;
        $date = new DateTime($reserve_start_time);
        $date->add(new DateInterval('PT'.$sum_time.'M'));
        $reserve_exit_time = $date->format("Y-m-d H:i:s");

        if (empty($organ_id) || empty($user_id)){
            $results['isSave'] = false;
            echo json_encode($results);
            return;
        }


        $pos = $this->order_model->emptyMaxPosition([
            'organ_id' => $organ_id,
            'from_time' =>  $reserve_start_time,
            'to_time' => $reserve_exit_time,
            'status_array' => [ORDER_STATUS_RESERVE_APPLY, ORDER_STATUS_TABLE_START, ORDER_STATUS_TABLE_END, ORDER_STATUS_TABLE_COMPLETE]
        ]);

        if (empty($staff_id)){
            $staffs = $this->staff_organ_model->getStaffsByOrgan($organ_id, STAFF_AUTH_ADMIN, false);

            $reserve_status = RESERVE_CONDITION_DISABLE;
            foreach($staffs as $staff){
                $status = $this->shift_model->getReserveShiftStatus($organ_id, $staff['staff_id'], $reserve_start_time, $reserve_exit_time);
                if ($status == RESERVE_CONDITION_ENABLE && $reserve_status == RESERVE_CONDITION_DISABLE){
                    $reserve_status = RESERVE_CONDITION_ENABLE;
                    $staff_id = $staff['staff_id'];
                }
                if ($status == RESERVE_CONDITION_OK){
                    $reserve_status = RESERVE_CONDITION_OK;
                    $staff_id = $staff['staff_id'];
                    break;
                }
            }
            if ($reserve_status == RESERVE_CONDITION_DISABLE){
                $results['isSave'] = false;
                echo json_encode($results);
                return;
            }
        }else{
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
            'interval' =>$interval,
            'status'=> $reserve_status == RESERVE_CONDITION_OK ? ORDER_STATUS_RESERVE_APPLY : ORDER_STATUS_RESERVE_REQUEST,
            'is_reserve'=>1,
        );

        $order_id = $this->order_model->insertRecord($order);

        if (empty($order_id)){
            $results['isSave'] = false;
            echo json_encode($results);
            return;
        }

        foreach ($menus as $menu_id){
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

    function ajaxUpdateReserveTime(){
        $order_id = $this->input->post('reserve_id');
        $staff_id = $this->input->post('staff_id');
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
        $order['select_staff_id'] = $staff_id;
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
}
