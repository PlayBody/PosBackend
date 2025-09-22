<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

header('Access-Control-Allow-Origin: *');
Header('Access-Control-Allow-Headers: *'); //for allow any headers, insecure
Header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');

require_once APPPATH . 'core/WebController.php';

/*
 *
 */

class Order extends WebController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('organ_model');
        $this->load->model('organ_time_model');
        $this->load->model('menu_model');
        $this->load->model('order_model');
        $this->load->model('order_menu_model');
        $this->load->model('shift_model');
        $this->load->model('shift_frame_model');
        $this->load->model('shift_frame_group_model');
        $this->load->model('reserve_ticket_model');

        
        $this->load->model('staff_organ_model');
        $this->load->model('company_model');
        $this->load->model('staff_model');
        $this->load->model('user_model');
        $this->load->model('group_user_model');
        $this->load->model('group_model');


        $this->load->model('reserve_model');
        $this->load->model('reserve_menu_model');

        $this->load->model('organ_special_time_model');
    }

    public function getOrderEnableDays()
    {
        $organ_id = $this->input->post('organ_id');
        $staff_id = $this->input->post('staff_id');
        $menu_ids = $this->input->post('menu_ids');

        if (empty($menu_ids)) $menu_ids = [];

        $time = $this->getMenuTimeFromMenuIds($menu_ids);
        $order_time = $time['menu_time'] + $time['interval'];

        $now_date = new DateTime();
        $now_date->add(new DateInterval('PT' . $order_time . 'M'));

        $condDays = [];
        $condDays['organ_id'] = $organ_id;
        $condDays['now_start_time'] = $now_date->format('Y-m-d H:i:s');

        $shift_counts = $this->shift_frame_model->getEnableDays($condDays);

        $max_date = date('Y-m-d');
        $dates = [];
        foreach ($shift_counts as $count){
            $date = substr($count['to_time'], 0, 10);
            if ($date>$max_date) $max_date = $date;

            if (!in_array($date, $dates)){
                $dates[] = $date;
            }
        }

        $results['is_result'] = true;
        $data['max_date'] = $max_date;
        $data['dates'] = $dates;
        $results['data'] = $data;

        echo json_encode($results);
    }

    public function getOrderEnableHours()
    {
        $organ_id = $this->input->post('organ_id');
        $staff_type = $this->input->post('staff_type');
        $staff_id = $this->input->post('staff_id');
        $menu_ids = $this->input->post('menu_ids');
        $from_date = $this->input->post('from_date');
        $to_date = $this->input->post('to_date');
        $user_id = $this->input->post('user_id');

        if (empty($from_date)) $from_date = date('Y-m-d');
        if (empty($to_date)) $to_date = $this->shift_model->getMaxShiftDate(['organ_id'=>$organ_id, 'is_enable_reserve' => 1]);

        if (empty($menu_ids)){
            $menu_ids = [];
        } else {
            $menu_ids = explode(',', $menu_ids);
        }
        
        $menu_time = $this->getMenuTimeFromMenuIds($menu_ids);
        $menu_length = $menu_time['menu_time'] + $menu_time['interval'];

        $sel_date = $from_date;
        $sel_date_time = new DateTime($sel_date);

        $data_tmp = [];
        while ($sel_date<=$to_date){
            $organTime = $this->getOrganTimeByDate($organ_id, $sel_date);

            $hours = [];
            $mins = [];
            for ($i = $organTime['from']; $i<$organTime['to']; $i+=5){
                $tmpDateTime = new DateTime($sel_date);
                $select_time = $tmpDateTime->add(new DateInterval('PT'.$i.'M'))->format('Y-m-d H:i:s');

                $reserve_data = $this->getReserveType($organ_id, $staff_type, $staff_id, $select_time, $menu_length, $user_id);

                $hour = $tmpDateTime->format('H');
                $min = $tmpDateTime->format('i');

                if (empty($hours[$hour])){
                    $hours[$hour] = $reserve_data['type'];
                }else{
                    if ($hours[$hour]>$reserve_data['type']){
                        $hours[$hour] = $reserve_data['type'];
                    }
                }

                $tmp = [];
                $tmp['date'] = $sel_date;
                $tmp['hour'] = $hour;
                $tmp['min'] = $min;
                $tmp['type'] = $reserve_data['type'];
                $mins[] = $tmp;
            }

            $hours_tmp = [];
            $isDisable = true;
            foreach ($hours as $key=>$value){
                if ($value<3) $isDisable = false;
                $tmp = [];
                $tmp['hour'] = $key;
                $tmp['type'] = $value;
                $hours_tmp[] = $tmp;
            }

            if (!$isDisable){
                $tmp = [];
                $tmp['date'] = $sel_date;
                $tmp['hours'] = $hours_tmp;
                $tmp['mins'] = $mins;

                $data_tmp[] = $tmp;
            }

            $sel_date_time->add(new DateInterval('P1D'));
            $sel_date = $sel_date_time->format('Y-m-d');
        }

        $dates = [];
        $hours = [];
        $mins = [];
        foreach ($data_tmp as $item){
            $dates[] = $item['date'];
            foreach ($item['hours'] as $hItem){
                $tmp = $hItem;
                $tmp['date'] = $item['date'];
                $hours[] = $tmp;
            }
            foreach ($item['mins'] as $mItem){
                $mins[] = $mItem;
            }
        }

        $data['dates'] = $dates;
        $data['hours'] = $hours;
        $data['mins'] = $mins;

        $results['is_result'] = true;
        $results['data'] = $data;

        echo json_encode($results);
    }

    public function reserveOrder(){
        $organ_id = $this->input->post('organ_id');
        $user_id = $this->input->post('user_id');
        $staff_id = $this->input->post('staff_id');
        $staff_type = $this->input->post('staff_type');
        $from_time = $this->input->post('from_time');
        $to_time = $this->input->post('to_time');
        $shift_frame_id = $this->input->post('shift_frame_id');
        $menu_ids = $this->input->post('menu_ids');

        $reserve_ticket = $this->input->post('use_ticket');
        $coupon_id = $this->input->post('coupon_id');
        $coupon_use_amount = $this->input->post('coupon_use_amount');
        $ticket_amount = $this->input->post('ticket_amount');
        $pay_method = $this->input->post('pay_method');
        $user_count = $this->input->post('user_count');
        $sum_time = empty($this->input->post('sum_time')) ? 0 : $this->input->post('sum_time');
        $user_2 = empty($this->input->post('user_2')) ? null : $this->input->post('user_2');
        $user_3 = empty($this->input->post('user_3')) ? null : $this->input->post('user_3');
        $user_4 = empty($this->input->post('user_4')) ? null : $this->input->post('user_4');

        $is_reserve = $this->input->post('is_reserve');

        if (empty($menu_ids)){
            $menu_ids = [];
        } else {
            $menu_ids = explode(',', $menu_ids);
        }

        $menu_time = $this->getMenuTimeFromMenuIds($menu_ids);
        $from_datetime = new DateTime($from_time);
        if (empty($shift_frame_id)){
            $to_time = $from_datetime->add(new DateInterval('PT'.$menu_time['menu_time'].'M'))->format('Y-m-d H:i:s');

            $menu_length = $menu_time['menu_time'] + $menu_time['interval'];
            
            $reserve_data = $this->getReserveType($organ_id, $staff_type, $staff_id, $from_time, $menu_length, $user_id);

            if ($reserve_data['type']==RESERVE_CONDITION_DISABLE){
                $results['is_result'] = false;
                $results['err_message'] = "申し訳ありませんが、指摘した時間に予約することはできません。\n 予約内容を再確認してください。";
    
                echo json_encode($results);
                exit;
            }
            $status = '';
            if ($reserve_data['type'] == RESERVE_CONDITION_OK) $status = ORDER_STATUS_RESERVE_APPLY;
            if ($reserve_data['type'] == RESERVE_CONDITION_ENABLE) $status = ORDER_STATUS_RESERVE_REQUEST;
            
            $staff_id = $reserve_data['staff_id'];
            $table_position = $reserve_data['table'];
        }else{
            $status = ORDER_STATUS_RESERVE_APPLY;
            $table_position = 1;
        }

        $order = [
            'organ_id' => $organ_id,
            'table_position' => $table_position,
            'amount' => $menu_time['amount'],
            'user_id' => $user_id,
            'select_staff_type' => $staff_type,
            'select_staff_id' => $staff_id,
            'user_count'=>$user_count,
            'other_name_1' => $user_2,
            'other_name_2' => $user_3,
            'other_name_3' => $user_4,
            'shift_frame_id' => empty($shift_frame_id) ? null : $shift_frame_id,
            'from_time' => $from_time,
            'to_time' =>$to_time,
            'interval' => $menu_time['interval'],
            'coupon_id' => empty($coupon_id)?null:$coupon_id,
            'pay_method' => empty($pay_method)?null:($pay_method==1 ? 1: null),
            'coupon_use_amount' => empty($coupon_use_amount)?null:$coupon_use_amount,
            'ticket_amount' => empty($ticket_amount) ? null : $ticket_amount,
            'status' => $status,
            'is_reserve' => $is_reserve
        ];

        $order_id = $this->order_model->insertRecord($order);

        if (!empty($order_id)){
            foreach ($menu_ids as $menu_id){
                $menu = $this->menu_model->getMenuInfo($menu_id);
                if (empty($menu)) continue;
                $order_menus = [
                    'order_id' => $order_id,
                    'menu_id' => $menu_id,
                    'menu_title' => $menu['menu_title'],
                    'menu_price' => $menu['menu_price'],
                    'quantity' => 1
                ];
                $this->order_menu_model->insertRecord($order_menus);
            }

            // $tickets = empty($reserve_ticket) ? [] : json_decode($reserve_ticket);
            // foreach ($tickets as $record) {
            //     $insertData = array(
            //         'reserve_id' => $order_id,
            //         'ticket_id' => $record->ticket_id,
            //         'use_count' => $record->use_count,
            //     );
            //     $insert = $this->reserve_ticket_model->insertRecord($insertData);
            // }
        }

        $order = $this->order_model->getOrderRow(['id'=>$order_id]);
        $order['menu_time'] = $menu_time['menu_time'];
        $order_menus = $this->order_menu_model->getListByCond(['order_id'=>$order_id]);

        $data['order'] = $order;
        $data['order_menus'] = $order_menus;

        $results['is_result'] = true;
        $results['data'] = $data;

        echo json_encode($results);
    }
    
    private function getMenuTimeFromMenuIds($menu_ids){
        $menus = $this->menu_model->getMenuList(['menu_ids'=>implode(",", $menu_ids)]);

        $menu_time = 0;
        $interval = 0;
        $amount = 0;
        foreach ($menus as $menu){
            if ($menu['menu_interval']>$interval) $interval = $menu['menu_interval'];
            $menu_time += $menu['menu_time'];
            $amount += $menu['menu_price'];
        }

        return ['menu_time' => $menu_time, 'interval' => $interval, 'amount' => $menu['menu_price']];
    }

    private function getReserveType($organ_id, $staff_type, $staff_id, $select_time, $menu_length, $user_id){

        $organ = $this->organ_model->getFromId($organ_id);
        $table_count = $organ['table_count'] == null ? 10 : $organ['table_count'];


        $from_time = $select_time;
        $fromDateTime = new DateTime($select_time);
        $to_time = $fromDateTime->add(new DateInterval('PT'.$menu_length.'M'))->format('Y-m-d H:i:s');

        $weekday = date('N', strtotime($select_time));

        $isInOpenTime = $this->organ_time_model->isInOpenTime($organ_id, $weekday, substr($from_time, 11, 5), substr($to_time, 11, 5));
        if (!$isInOpenTime){
            $isInOpenTime = $this->organ_special_time_model->isInOpenTime($organ_id, $from_time, $to_time);
        }

        if(!$isInOpenTime)
            return ['type' => RESERVE_CONDITION_DISABLE, 'message' => 'out_open_time'];

        if (!empty($user_id)){
            $my_orders = $this->order_model->getListByCond([
                'user_id' => $user_id,
                'in_from_time' => $from_time,
                'in_to_time' => $to_time,
                'is_with_interval' => 1,
                'status_array' => [ORDER_STATUS_RESERVE_APPLY, ORDER_STATUS_RESERVE_REQUEST],
            ]);
            if (!empty($my_orders))
                return ['type' => RESERVE_CONDITION_DISABLE, 'message' => 'exist_order'];
        }

        $order_tables = $this->order_model->getOrderTables($organ_id, $from_time, $to_time);
        if (empty($order_tables)) $order_tables = [];
        if (count($order_tables) >= $table_count){
            return ['type' => RESERVE_CONDITION_DISABLE, 'message' => 'table_out'];
        }

        $table = 0;
        for($i=1; $i<=$table_count; $i++){
            if (in_array($i, $order_tables)) continue;
            $table = $i;
            break;
        }

        if ($table == 0) {
            return ['type' => RESERVE_CONDITION_DISABLE, 'message' => 'table_out'];
        }

        /*  enable Staff */
        $cond = [
            'organ_id' => $organ_id,
            'include_from_time' => $from_time,
            'include_to_time' => $to_time,
            'is_apply' => 1
        ];
        if ($staff_type==3 && !empty($staff_id)) $cond['staff_id'] = $staff_id;
        if ($staff_type==1) $cond['staff_sex'] = 1;
        if ($staff_type==2) $cond['staff_sex'] = 2;

        $enable_shift_staffs = $this->shift_model->getListByCond($cond);

        foreach ($enable_shift_staffs as $shift){
            $staff_orders = $this->order_model->getListByCond([
                'staff_id' => $shift['staff_id'],
                'in_from_time' => $from_time,
                'in_to_time' => $to_time,
                'is_with_interval' => 1,
                'status_array' => [ORDER_STATUS_RESERVE_APPLY, ORDER_STATUS_RESERVE_REQUEST],
            ]);
            if (empty($staff_orders)){
                return ['type' => RESERVE_CONDITION_OK, 'staff_id' => $shift['staff_id'], 'table' => $table];
            }
        }

        /* request staff */
        $cond = [
            'organ_id' => $organ_id,
            'include_from_time' => $from_time,
            'include_to_time' => $to_time,
            'reserve_flag' => 1
        ];

        if ($staff_type==3 && !empty($staff_id)) $cond['staff_id'] = $staff_id;
        if ($staff_type==1) $cond['staff_sex'] = 1;
        if ($staff_type==2) $cond['staff_sex'] = 2;

        $request_shift_staffs = $this->shift_model->getListByCond($cond);

        foreach ($request_shift_staffs as $shift){
            $staff_orders = $this->order_model->getListByCond([
                'staff_id' => $shift['staff_id'],
                'in_from_time' => $from_time,
                'in_to_time' => $to_time,
                'is_with_interval' => 1,
                'status_array' => [ORDER_STATUS_RESERVE_APPLY, ORDER_STATUS_RESERVE_REQUEST],
            ]);
            if (empty($staff_orders)){
                return ['type' => RESERVE_CONDITION_ENABLE, 'staff_id' => $shift['staff_id'], 'table' => $table];
            }
        }

        /*disable*/
        return ['type' => RESERVE_CONDITION_DISABLE, 'message' => 'no_staff'];
    }

    public function loadCheckFrameData(){
        $organ_id = $this->input->post('organ_id');
        $select_date = $this->input->post('select_date');

        $orders = $this->order_model->getListData([
            'organ_id' => $organ_id,
            'select_date' => $select_date,
            'is_shift_frame_mode' => 1,
        ]);

        $frames = $this->shift_frame_model->getListData(['organ_id'=>$organ_id, 'selected_date' => $select_date]);

        $users = [];
        $groups = [];

        foreach($frames as $frame){
            $tmp = [];
            $tmp['shift_frame_id'] = $frame['id'];
            $tmp['from_time'] = $frame['from_time'];
            $tmp['to_time'] = $frame['to_time'];
            
            $group = $this->shift_frame_group_model->getOneByParam([
                'shift_frame_id' => $frame['id']
            ]);

            if (empty($group)){
                $tmp['group_id'] = '';
                $group_users = [];
            }else{
                $tmp['group_id'] = $group['group_id'];
                $group_users = $this->group_user_model->getUsers(['group_id'=>$group['group_id']]);
            }
            $tmp['group_memo'] = $frame['comment']==null ? '' :  explode('<br />', nl2br($frame['comment']))[0];
            $groups[$frame['id']]['group'] = $tmp;
            foreach($group_users as $user){
                $tmp_user = [];
                $tmp_user['user_id'] = $user['user_id'];
                $tmp_user['user_name'] = $user['user_first_name']. ' ' .$user['user_last_name'];
                $tmp_user['is_group'] = true;
                $tmp_user['is_enter'] = false;
                $tmp_user['order_id'] = '';
    
                $groups[$frame['id']]['users'][$user['user_id']] = $tmp_user;
            }
        }

        foreach($orders as $order){

            $f_id = $order['shift_frame_id'];
            $u_id = $order['user_id'];
            if (!empty($groups[$f_id]) && array_key_exists($u_id, $groups[$f_id]['users'])){
                if ($order['status']>=ORDER_STATUS_TABLE_START){
                    $groups[$f_id]['users'][$u_id]['is_enter'] = true;
                }
                $groups[$f_id]['users'][$u_id]['order_id'] = $order['id'];
            }else{
                $user = $this->user_model->get($order['user_id']);

                $tmp_user = [];
                $tmp_user['user_id'] = $user['user_id'];
                $tmp_user['user_name'] = $user['user_first_name']. ' ' .$user['user_last_name'];
                $tmp_user['is_group'] = false;
                $tmp_user['is_enter'] = false;
                if ($order['status']>=ORDER_STATUS_TABLE_START){
                    $tmp_user['is_enter'] = true;
                }
                $tmp_user['order_id'] = $order['id'];
    
                $groups[$order['shift_frame_id']]['users'][$user['user_id']] = $tmp_user;
            }
        }
        $data = [];
        foreach($groups as $group){
            $tmp = [];
            $tmp['group'] = $group['group'];
            $users = [];
            if (!empty($group['users'])){
                foreach($group['users'] as $item){
                    $users[] = $item;
                }
            }else{
                // $users = null;
            }
            $tmp['users'] = $users;
            $data[] = $tmp;
        }

        $results['is_result'] = true;
        $results['data'] = $data;
        
        echo json_encode($results);
    }

    public function updateOrderStatus(){
        $order_id = $this->input->post('order_id');
        $status = $this->input->post('status');

        if (empty($order_id) || empty($status)){
            $results['is_result'] = false;
            $results['err_message'] = 'request parmeter error';

            echo json_encode($results);
            exit;
        }
        $order = $this->order_model->get($order_id);
        $order['status'] = $status;
        $this->order_model->updateRecord($order, 'id');

        $results['is_result'] = true;

        echo json_encode($results);
        exit;
    }

    public function insertOrder(){
        $organ_id = $this->input->post('organ_id');
        $user_id = $this->input->post('user_id');
        $from_time = $this->input->post('from_time');
        $to_time = $this->input->post('to_time');
        $shift_frame_id = $this->input->post('shift_frame_id');
        $status = $this->input->post('status');

        $table_position = 1;

        $order = [
            'organ_id' => $organ_id,
            'table_position' => $table_position,
            'user_id' => $user_id,
            'shift_frame_id' => empty($shift_frame_id) ? null : $shift_frame_id,
            'from_time' => $from_time,
            'to_time' =>$to_time,
            'status' => $status,
        ];

        $order_id = $this->order_model->insertRecord($order);

        $results['is_result'] = true;

        echo json_encode($results);
    }    

	public function updateEachElement(){
		$id = $this->input->post('id');
		$user_input_name = $this->input->post('user_input_name');

		if (empty($id)) {
			$results['is_result'] = false;

			echo json_encode($results);

			return;
		}

		$order= $this->order_model->getFromId($id);
		if (!empty($user_input_name)) $order['user_input_name'] = $user_input_name;

		$this->order_model->updateRecord($order);
		
        $results['is_result'] = true;

        echo json_encode($results);
	}
}
?>
