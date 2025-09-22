<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . 'core/WebController.php';

class Apiorders extends WebController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('order_model');
        $this->load->model('order_menu_model');
        $this->load->model('staff_organ_model');
        $this->load->model('organ_model');
        $this->load->model('table_name_model');
        $this->load->model('user_model');
        $this->load->model('staff_model');
        $this->load->model('organ_set_table_model');
    }

    public function loadOrderUserIds() {
        $staff_id = $this->input->post('staff_id');

        if (empty($staff_id)) {
            $results['isLoad'] = false;
            echo json_encode($results);
            return;
        }
        
        $cond=[];
        $cond['staff_id'] = $staff_id;
        $cond['status_array'] = [ORDER_STATUS_RESERVE_REQUEST];
        $lists = $this->order_model->getListByCond($cond);

        $userIds = array();
        foreach($lists as $item) {
            $userIds[] = $item['user_id'];
        }

        $results['isLoad'] = true;
        $results['userIds'] = $userIds;
        echo json_encode($results);

    }

    public function loadOrderList(){
        $user_id = $this->input->post('user_id');
        $staff_id = $this->input->post('staff_id');
        $company_id = $this->input->post('company_id');
        $organ_id = $this->input->post('organ_id');
        $is_complete = $this->input->post('is_complete');
        $from_time = $this->input->post('from_time');
        $to_time = $this->input->post('to_time');
        $in_from_time = $this->input->post('in_from_time');
        $in_to_time = $this->input->post('in_to_time');
        $is_reserve_apply = $this->input->post('is_reserve_apply');
        $is_reserve_active = $this->input->post('is_reserve_active');
        $is_reserve_list = $this->input->post('is_reserve_list');
        $is_reserve_and_complete = $this->input->post('is_reserve_and_complete');

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

            $cond['staff_id'] = $staff_id;
        }

        if (!empty($user_id)) $cond['user_id'] = $user_id;
        if (!empty($organ_id)) $cond['organ_id'] = $organ_id;
        if (!empty($from_time)) $cond['from_time'] = $from_time;
        if (!empty($to_time)) $cond['to_time'] = $to_time;
        if (!empty($in_from_time)) $cond['in_from_time'] = $in_from_time;
        if (!empty($in_to_time)) $cond['in_to_time'] = $in_to_time;

        if($is_complete)
            $cond['status_array'] = [ORDER_STATUS_TABLE_COMPLETE];
        if($is_reserve_active)
            $cond['status_array'] = [ORDER_STATUS_RESERVE_REQUEST, ORDER_STATUS_RESERVE_APPLY];
        if($is_reserve_apply)
            $cond['status_array'] = [ORDER_STATUS_RESERVE_APPLY];
        if($is_reserve_and_complete)
            $cond['status_array'] = [ORDER_STATUS_RESERVE_APPLY, ORDER_STATUS_TABLE_COMPLETE];
        if(!empty($is_reserve_list)){
            if ($is_reserve_list==1){
                $cond['status_array'] = [ORDER_STATUS_RESERVE_REQUEST, ORDER_STATUS_RESERVE_REJECT, ORDER_STATUS_RESERVE_APPLY, ORDER_STATUS_TABLE_START];
            }
            if ($is_reserve_list==2){
                $cond['status_array'] = [ORDER_STATUS_RESERVE_CANCEL, ORDER_STATUS_TABLE_COMPLETE];
            }
        }
        $lists = $this->order_model->getListByCond($cond);

        $orders = [];
        foreach ($lists as $item){
            $menus = $this->order_menu_model->getDataByParam(['order_id'=>$item['id']]);

            $item['menus'] = $menus;
            $orders[] = $item;
        }

        $results['isLoad'] = true;
        $results['orders'] = $orders;

        echo json_encode($results);
    }

    public function acceptOrderRequest() {        
        $order_id = $this->input->get_post('order_id');
        $staff_id = $this->input->get_post('staff_id');
        if (empty(($order_id))) {
            $results['isLoad'] = false;
            echo json_encode($results); 
            return;
        }

        $order_record = $this->order_model->getFromId($order_id);
        if (empty($order_record)) {
            $results['isLoad'] = false;
            echo json_encode($results); 
            return;
        }

        $order_record['status'] = ORDER_STATUS_RESERVE_APPLY;
        $this->order_model->updateRecord($order_record);
        
        $this->load->model('notification_model');
        $notificationData = $this->notification_model->getRecordByCond([
            'receiver_type' => '1',
            'notification_type' => '13',
            'order_id' => $order_id,
            'receiver_id' => $staff_id,
        ]);
        if (!empty($notificationData) && $notificationData['badge_count'] > 0) {
            $newNotificationData['id'] = $notificationData['id'];
            $newNotificationData['badge_count'] = $notificationData['badge_count'] - 1;
            $this->notification_model->update($newNotificationData);
        }
        
        $results['isLoad'] = true;
        echo json_encode($results); 
        return;
    }

    public function loadCurrentOrganTables() {
        $staff_id = $this->input->get_post('staff_id');
        $organ_id = $this->input->get_post('organ_id');

        $organ = $this->organ_model->getFromId($organ_id);
        $table_count = empty($organ['table_count']) ? 4 : $organ['table_count'];

        $start_position = 1;
        $end_position = $table_count;
        $staff = $this->staff_model->getFromId($staff_id);
        if ($staff['staff_auth']==STAFF_AUTH_GUEST){
            $start_position = $staff['table_position'];
            $end_position = $staff['table_position'];
        }

        $tables = [];
        for($i=$start_position; $i<=$end_position;$i++){
            $tmp = [];
            $tmp['table_position'] = $i;

            $cond = ['organ_id'=>$organ_id, 'table_position'=>$i];
            $table_name_record = $this->table_name_model->getOneByParam($cond);

            $cond['status_array'] = [ORDER_STATUS_RESERVE_REQUEST];
            $cond['select_time'] = date('Y-m-d H:i:s');
            $order_record = $this->order_model->getOrderRecord($cond);
            
            $tmp['table_name'] = empty($table_name_record) ? '席'.$i : $table_name_record['table_name'];
            $tmp['status'] = empty($order_record)? ORDER_STATUS_NONE : $order_record['status'];
            $tmp['staff_name'] = empty($order_record['staff_name']) ? '' : $order_record['staff_name'];
            $tmp['user_name'] = empty($order_record['user_name']) ? '' : $order_record['user_name'];
            $tmp['user_id'] = empty($order_record) ? '' : $order_record['user_id'];
            $tmp['id'] = empty($order_record) ? '' : $order_record['id'];
            $tmp['from_time'] = empty($order_record['from_time']) ? '' : $order_record['from_time'];
            $tmp['to_time'] = empty($order_record['to_time']) ? '' : $order_record['to_time'];

            $tables[] = $tmp;
        }

        $results['isLoad'] = true;
        $results['tables'] = $tables;

        echo json_encode($results);
    }

    public function loadOrganTables(){
        $staff_id = $this->input->post('staff_id');
        $organ_id = $this->input->post('organ_id');

        $organ = $this->organ_model->getFromId($organ_id);
        $table_count = empty($organ['table_count']) ? 4 : $organ['table_count'];

        $start_position = 1;
        $end_position = $table_count;
        $staff = $this->staff_model->getFromId($staff_id);
        if ($staff['staff_auth']==STAFF_AUTH_GUEST){
            $start_position = $staff['table_position'];
            $end_position = $staff['table_position'];
        }

        $tables = [];
        for($i=$start_position; $i<=$end_position;$i++){
            $tmp = [];
            $tmp['table_position'] = $i;

            $cond = ['organ_id'=>$organ_id, 'table_position'=>$i];
            $table_name_record = $this->table_name_model->getOneByParam($cond);

            $cond['status_array'] = [ORDER_STATUS_TABLE_START, ORDER_STATUS_TABLE_END];
            $order_record = $this->order_model->getOrderRecord($cond);
            if(empty($order_record)){
                $cond['select_time'] = date('Y-m-d H:i:s');
                $cond['status_array'] = [ORDER_STATUS_RESERVE_APPLY];
                $order_record = $this->order_model->getOrderRecord($cond);
            }

            $tmp['table_name'] = empty($table_name_record) ? '席'.$i : $table_name_record['table_name'];
            $tmp['status'] = empty($order_record)? ORDER_STATUS_NONE : $order_record['status'];
            $tmp['staff_name'] = empty($order_record['staff_name']) ? '' : $order_record['staff_name'];
            $tmp['user_name'] = empty($order_record['user_name']) ? '' : $order_record['user_name'];
            $tmp['user_id'] = empty($order_record) ? '' : $order_record['user_id'];
            $tmp['id'] = empty($order_record) ? '' : $order_record['id'];
            $tmp['from_time'] = empty($order_record['from_time']) ? '' : $order_record['from_time'];
            $tmp['to_time'] = empty($order_record['to_time']) ? '' : $order_record['to_time'];

            $tables[] = $tmp;
        }

        $results['isLoad'] = true;
        $results['tables'] = $tables;

        echo json_encode($results);
    }

    public function loadOrderInfo(){
        $order_id = $this->input->post('order_id');
        if(empty($order_id)){
            $results['isLoad'] = false;
            echo json_encode($results);
            return;
        }

        $order = $this->order_model->getOrderRecord(['order_id'=>$order_id]);
        if(empty($order_id)){
            $results['isLoad'] = false;
            echo json_encode($results);
            return;
        }

        $table_name_record = $this->table_name_model->getOneByParam( ['organ_id'=>$order['organ_id'], 'table_position'=>$order['table_position']]);
        $order['table_name'] = empty($table_name_record) ? '席'.$order['table_position'] : $table_name_record['table_name'];
        $to_time = empty($order['to_time']) ? date('Y-m-d H:i:s') : $order['to_time'];
		if ($to_time<$order['from_time']) $to_time = $order['from_time'];
        $order['flow_time'] = $this->calcFlowTimeMinutes($order['from_time'], $to_time, empty($order['to_time']));

        $set_amount = $this->calcSetAmount($order['flow_time'], $order['set_start_time'], $order['set_time'], $order['set_amount']);
        $charge_amount = empty($order['charge_amount']) ? 0 : $order['charge_amount'];

        $order['menus'] = $this->order_menu_model->getDataByParam(['order_id'=>$order_id]);
        $menu_amount=0;
        foreach ($order['menus'] as $menu){
			$tax_rate = 1 + (empty($menu['menu_tax']) ? 0 : ($menu['menu_tax']/100));
			$menu_amount += $menu['menu_price'] * $tax_rate * $menu['quantity'];
		}
        $base_amount = $set_amount + $charge_amount;

        if (!empty($order['user_count'])){
            $base_amount = $base_amount * $order['user_count'] ;
        }

        $organ = $this->organ_model->getFromId($order['organ_id']);
		$service_rate = 1;
		if (!empty($organ['is_service_tax']) && $organ['is_service_tax'] == 1) {
			$service_rate += empty($organ['service_tax']) ? 0 : ($organ['service_tax']/100);
		}

        //if(empty($order['amobase_amountnt']))

		$order['amount'] = $this->calcRoundAmount($base_amount*$service_rate + $menu_amount, $organ['is_round_amount']);


        if($order['to_time']<date('Y-m-d H:i:s') && !$order['is_reset']){
            $order['is_reset_temp'] = 1;
        }

        $results['isLoad'] = true;
        $results['order'] = $order;

        echo json_encode($results);
    }

    public function addOrder(){
        $organ_id = $this->input->get_post('organ_id');
        $table_position = $this->input->get_post('table_position');
        $user_id = $this->input->post('user_id');
        $staff_id = $this->input->post('staff_id');
        $staff_sel_type = $this->input->post('staff_sel_type');
        $user_count = $this->input->post('user_count');
        $other_name1 = $this->input->post('user_name1');
        $other_name2 = $this->input->post('user_name2');
        $other_name3 = $this->input->post('user_name3');
        $amount = $this->input->post('amount');
        $discount = $this->input->post('discount');
        $from_time = $this->input->post('from_time');
        $to_time = $this->input->post('to_time');
        $interval = $this->input->post('interval');
        $pay_method = $this->input->post('pay_method');
        $set_number = $this->input->post('set_number');
        $status = $this->input->post('status');

		if ($this->order_model->isExistOrderByOrganAndPosition($organ_id, $table_position)) {
            $results['isAdd'] = false;
            $results['message'] = '申し訳ありません。現在の座席はご利用中です。';
            echo json_encode($results);
            return;
		}

        if(empty($from_time)){
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

        }
        if(!empty($set_number)){
            $set_table = $this->organ_set_table_model->getOneByParam(['organ_id'=>$organ_id, 'set_number' => $set_number]);
            if (!empty($set_table)){
                $set_start_time = empty($set_table['set_start_time']) ? '' : $set_table['set_start_time'];
                $set_time = empty($set_table['set_time']) ? '' : $set_table['set_time'];
                $set_amount = empty($set_table['set_amount']) ? '' : $set_table['set_amount'];
                $charge_amount = empty($set_table['table_amount']) ? '' : $set_table['table_amount'];
            }
        }

        $cond = ['organ_id'=>$organ_id, 'table_position'=>$table_position];
        $cond['status_array'] = [ORDER_STATUS_TABLE_START, ORDER_STATUS_TABLE_END];
        $order_record = $this->order_model->getOrderRecord($cond);
        if(empty($order_record)){
            $cond['select_time'] = date('Y-m-d H:i:s');
            $cond['status_array'] = [ORDER_STATUS_RESERVE_APPLY];
            $order_record = $this->order_model->getOrderRecord($cond);
        }

        if(!empty($order_record)){
            $results['isAdd'] = false;
            $results['message'] = '現在の座席には入店できません。';
            echo json_encode($results);
            return;
        }

        $user = $this->user_model->getFromId($user_id);
        $user_name = empty($user['user_first_name']) ? 'なし' : ($user['user_first_name']. ' '. $user['user_last_name']);

        $order = array(
            'organ_id' => $organ_id,
            'table_position' => $table_position,
            'user_id' => $user_id,
            'amount' => empty($amount) ? null : $amount,
            'discount_amount' => empty($discount) ? null : $discount,
            'select_staff_type'=> empty($staff_sel_type) ? null :$staff_sel_type,
            'select_staff_id' => $staff_id,
            'user_count' => $user_count,
            'user_input_name' => $user_name,
            'other_name_1' => $other_name1,
            'other_name_2' => $other_name2,
            'other_name_3' => $other_name3,
            'from_time' => $from_time,
            'to_time' => empty($to_time) ? null : $to_time,
            'interval' => empty($interval) ? null : $interval,
            'pay_method' => empty($pay_method) ? null : $pay_method,
            'set_start_time' => empty($set_start_time) ? null : $set_start_time,
            'set_time' => empty($set_time) ? null : $set_time,
            'set_amount' => empty($set_amount) ? null : $set_amount,
            'charge_amount' => empty($charge_amount) ? null : $charge_amount,
            'status' => empty($status) ? ORDER_STATUS_TABLE_START : $status,
        );

        $order_id = $this->order_model->insertRecord($order);

        if (empty($order_id)){
            $results['isAdd'] = false;
            $results['message'] = 'システムエラーが発生しました。';
            echo json_encode($results);
            return;
        }
        $results['isAdd'] = true;
        $results['order_id'] = $order_id;
        echo json_encode($results);
    }

    public function exitOrder(){
        $order_id = $this->input->post('order_id');
        $order = $this->order_model->getFromId($order_id);
		$organ = $this->organ_model->getFromId($order['organ_id']);

        if (empty($order)){
            $results['isUpdate'] = false;
            echo json_encode($results);
            return;
        }

        if($order['status']!=ORDER_STATUS_TABLE_START){
            $results['isUpdate'] = false;
            echo json_encode($results);
            return;
        }
        $now = new DateTime();
        $min = $now->format('i');
		
		$d = 5-($min%5);
		$now->add(new DateInterval('PT'.$d.'M'));

		$to_time = $now->format('Y-m-d H:i:00');

        $order['to_time'] = $to_time;

        $flow_time = $this->calcFlowTimeMinutes($order['from_time'], $to_time);
        $set_amount = $this->calcSetAmount($flow_time, $order['set_start_time'], $order['set_time'], $order['set_amount']);
        $charge_amount = empty($order['charge_amount']) ? 0 : $order['charge_amount'];

        $menus = $this->order_menu_model->getDataByParam(['order_id'=>$order_id]);
        $menu_amount=0;
        foreach ($menus as $menu){
			$tax_rate = 1 + (empty($menu['menu_tax']) ? 0 : ($menu['menu_tax']/100));
			$menu_amount += $menu['menu_price'] * $tax_rate * $menu['quantity'];
        }

		$service_rate = 0;
		if (!empty($organ['is_service_tax']) && $organ['is_service_tax'] == 1) {
			$service_rate = empty($organ['service_tax']) ? 0 : ($organ['service_tax']/100);
			$order['service_amount'] = ($set_amount + $charge_amount + $menu_amount)*$service_rate;
		}

		$base_amount = $set_amount + $charge_amount;
        if (!empty($order['user_count'])){
            $base_amount = $base_amount * $order['user_count'] ;
        }

		$order['amount'] = $this->calcRoundAmount($base_amount*(1+$service_rate) + $menu_amount, $organ['is_round_amount']);

		$order['status'] = ORDER_STATUS_TABLE_END;

        $this->order_model->updateRecord($order);
        $results['isUpdate'] = true;
        echo json_encode($results);
    }

    public function resetOrder(){
        $order_id = $this->input->post('order_id');
        $pay_method = $this->input->post('pay_method');
        $order = $this->order_model->getFromId($order_id);

        if (empty($order)){
            $results['isUpdate'] = false;
            echo json_encode($results);
            return;
        }

		$otherOrders = $this->order_model->getOrdersByPositionNoComplete($order['organ_id'], $order['table_position'], $order_id);
		foreach($otherOrders as $other){
			$this->order_model->delete_force($other['id'], 'id');
		}

        if($order['status']!=ORDER_STATUS_TABLE_END){
            $results['isUpdate'] = false;
            echo json_encode($results);
            return;
        }
        if(!empty($pay_method)) $order['pay_method'] = $pay_method;
        $order['status'] = ORDER_STATUS_TABLE_COMPLETE;

        $this->order_model->updateRecord($order);
        $results['isUpdate'] = true;
        echo json_encode($results);
    }    

    public function resetOrderTemp(){
        $order_id = $this->input->post('order_id');
        $order = $this->order_model->getFromId($order_id);

        if (empty($order)){
            $results['isUpdate'] = false;
            echo json_encode($results);
            return;
        }
        $order['is_reset'] = true;

        $this->order_model->updateRecord($order);
        $results['isUpdate'] = true;
        echo json_encode($results);
    }

    public function updateStatus(){
        $order_id = $this->input->post('order_id');
        $status = $this->input->post('status');

        $order = $this->order_model->getFromId($order_id);
        if (empty($order)){
            $results['isUpdate'] = false;
            echo json_encode($results);
            return;
        }

        $order['status'] = $status;

        $this->order_model->updateRecord($order);
        $results['isUpdate'] = true;
        echo json_encode($results);

    }

    public function updateOrder(){

        $reserve_id = $this->input->get_post('reserve_id');
        $status = $this->input->get_post('status');
        $staff_id = $this->input->get_post('staff_id');
        $from_time = $this->input->get_post('from_time');
        $to_time = $this->input->get_post('to_time');
        $user_input_name = $this->input->get_post('user_input_name');
        $user_count = $this->input->get_post('user_count');


        if (empty($reserve_id)) {
            $results['isUpdate'] = false;
            echo json_encode($results);
            return;
        }

        $order = $this->order_model->getFromId($reserve_id);
        if (empty($order)){
            $results['isUpdate'] = false;
            echo json_encode($results);
            return;
        }
        
        if (!empty($status)) {
            $order['status'] = $status;
        }
        
        if (!empty($from_time)) {
            $order['from_time'] = $from_time;
        }
        
        if (!empty($to_time)) {
            $order['to_time'] = $to_time;
        }
        
        if (!empty($user_input_name)) {
            $order['user_input_name'] = $user_input_name;
        }

        if (!empty($user_count)) {
            $order['user_count'] = $user_count;
        }

        $this->order_model->updateRecord($order);

        if (!empty($status)) {
            $this->load->model('notification_model');
            $notificationData = $this->notification_model->getRecordByCond([
                'receiver_type' => '1',
                'notification_type' => SHIFT_STATUS_REQUEST,
                'receiver_id' => $staff_id,
            ]);
            if (!empty($notificationData) && $notificationData['badge_count'] > 0) {
                $newNotificationData['id'] = $notificationData['id'];
                $newNotificationData['badge_count'] = $notificationData['badge_count'] - 1;
                $this->notification_model->update($newNotificationData);
            }
        }

        $results['isUpdate'] = true;
        echo json_encode($results);
    }

    public function applyReserveOrder(){
        $order_id = $this->input->get_post('order_id');
        $staff_id = $this->input->get_post('staff_id');

        $order = $this->order_model->getFromId($order_id);
        if (empty($order)){
            $results['isUpdate'] = false;
            echo json_encode($results);
            return;
        }

        $order['status'] = ORDER_STATUS_RESERVE_APPLY;
        $order['table_position'] = $this->order_model->emptyMaxPosition([
            'order_id' => $order['id'],
            'organ_id' => $order['organ_id'],
            'from_time' =>  $order['from_time'],
            'to_time' => $order['to_time'],
            'status_array' => [ORDER_STATUS_RESERVE_APPLY, ORDER_STATUS_TABLE_START, ORDER_STATUS_TABLE_END, ORDER_STATUS_TABLE_COMPLETE]
        ]);
        $this->order_model->updateRecord($order);

        $this->load->model('notification_model');
        $notificationData = $this->notification_model->getRecordByCond([
            'receiver_type' => '1',
            'notification_type' => SHIFT_STATUS_REQUEST,
            'receiver_id' => $staff_id
        ]);

        if (!empty($notificationData) && $notificationData['badge_count'] > 0) {
            $newNotificationData['id'] = $notificationData['id'];
            $newNotificationData['badge_count'] = $notificationData['badge_count'] - 1;
            $this->notification_model->update($newNotificationData);
        }

        $results['isUpdate'] = true;
        echo json_encode($results);

    }

    public function rejectOrder(){
        $organ_id = $this->input->post('organ_id');
        $user_id = $this->input->post('user_id');

        $reject = array(
            'organ_id' => $organ_id,
            'user_id' => $user_id,
            'from_time' => date('Y-m-d H:i:s'),
            'to_time' => date('Y-m-d H:i:s'),
            'status' => ORDER_STATUS_TABLE_REJECT,
        );

        $this->order_model->insertRecord($reject);

        $results['isSave'] = true;

        echo json_encode($results);

    }

    public function saveOrderMenus(){

        $order_id = $this->input->post('order_id');
        $data = $this->input->post('data');//'[{"title":"ボディケア基本的30分コース","price":"2390","quantity":"2","menu_id":"46","variation_id":null,"use_tickets":{"10":"2"}}]';

        $results = [];
        if (empty($order_id)){
            $results['isSave'] = false;
            echo(json_encode($results));
            return;
        }

//        $order_menus = $this->order_menu_model->getDataByParam(['order_id'=>$order_id]);
//        foreach ($order_menus as $item){
//            $this->table_menu_ticket_model->delete_force($item['table_menu_id'], 'table_menu_id');
//        }
        $this->order_menu_model->delete_force($order_id, 'order_id');

        $data = json_decode($data, true);

        foreach ($data as $record) {
            $menu = array(
                'menu_title' => $record['title'],
                'menu_price' => $record['price'],
				'menu_tax' => $record['menu_tax'],
                'quantity' => $record['quantity'],
                'order_id' => $order_id,
                'create_date' => date('Y-m-d'),
                'update_date' => date('Y-m-d'),
            );
            if (!empty($record['menu_id'])){
                $menu['menu_id'] = $record['menu_id'];
            }
            if (!empty($record['variation_id'])){
                $menu['variation_id'] = $record['variation_id'];
            }
            $order_menu_id = $insert = $this->order_menu_model->add($menu);
//
//            foreach ($record->use_tickets as $key=>$val) {
//                $insertTicket = [];
//                $insertTicket = array(
//                    'table_menu_id' => $talbe_menu_id,
//                    'ticket_id' => $key,
//                    'count' => $val,
//                );
//
//                $insert = $this->table_menu_ticket_model->insertRecord($insertTicket);
//
//            }
        }

        echo(json_encode(array('isSave'=>true)));
        exit(0);
    }

    public function deleteOrder(){
        $order_id = $this->input->post('order_id');

        $results = [];
        if (empty($order_id)){
            $results['isDelete'] = false;
            echo json_encode($results);
            return;
        }

        $this->order_model->delete_force($order_id);

        $this->order_menu_model->delete_force($order_id, 'order_id');

        $results['isDelete'] = true;
        echo json_encode($results);
    }

    public function deleteOrderMenu(){
        $order_menu_id = $this->input->post('order_menu_id');

        $results = [];
        if (empty($order_menu_id)){
            $results['isDelete'] = false;
            echo json_encode($results);
            return;
        }

        $this->order_menu_model->delete_force($order_menu_id);

        $results['isDelete'] = true;
        echo json_encode($results);
    }

    public function changeQuantityOrderMenu(){
        $order_menu_id = $this->input->post('order_menu_id');
        $quantity = $this->input->post('quantity');

        $results = [];
        if (empty($order_menu_id)){
            $results['isUpdate'] = false;
            echo json_encode($results);
            return;
        }
        $menu = $this->order_menu_model->getFromId($order_menu_id);
        if (empty($menu)){
            $results['isUpdate'] = false;
            echo json_encode($results);
            return;
        }
        $menu['quantity'] = $quantity;
        $this->order_menu_model->updateRecord($menu);

        $results['isUpdate'] = true;
        echo json_encode($results);
    }

    public function swapSeats() {
        $order_id1 = $this->input->post('order_id1');
        $order_id2 = $this->input->post('order_id2');
        $table_position1 = $this->input->post('table_position1');
        $table_position2 = $this->input->post('table_position2');

        $results = [];
        if (empty($order_id1) && empty($order_id2)) {
            $results['isUpdate'] = false;
            echo json_encode($results);
            return;
        }

        if(!empty($order_id1) && !empty($order_id2)) {
            $order1 = $this->order_model->getFromId($order_id1);
            $order2 = $this->order_model->getFromId($order_id2);
    
            if (empty($order1) || empty($order2)) {
                $results['isUpdate'] = false;
                echo json_encode($results);
                return;
            }
    
            // Swap table positions
            $temp_position = $order1['table_position'];
            $order1['table_position'] = $order2['table_position'];
            $order2['table_position'] = $temp_position;
    
            // Update both orders in database
            $this->order_model->updateRecord($order1);
            $this->order_model->updateRecord($order2);
    
            $results['isUpdate'] = true;
            echo json_encode($results);
        } else {
            $order = [];
            if(!empty($order_id1)) {
                $order = $this->order_model->getFromId($order_id1);
                if (empty($order) || empty($table_position2)) {
                    $results['isUpdate'] = false;
                    echo json_encode($results);
                    return;
                }
                $order['table_position'] = $table_position2;
            }
            
            if(!empty($order_id2)) {
                $order = $this->order_model->getFromId($order_id2);
                if (empty($order) || empty($table_position1)) {
                    $results['isUpdate'] = false;
                    echo json_encode($results);
                    return;
                }
                $order['table_position'] = $table_position1;
            }
            
            $this->order_model->updateRecord($order);
            $results['isUpdate'] = true;
            echo json_encode($results);
        }
    }

    public function combineSeats() {
        $order_id1 = $this->input->post('order_id1'); // Target seat
        $order_id2 = $this->input->post('order_id2'); // Source seat

        $results = [];
        if (empty($order_id1) || empty($order_id2)) {
            $results['isUpdate'] = false;
            echo json_encode($results);
            return;
        }

        // Get both orders
        $order1 = $this->order_model->getFromId($order_id1);
        $order2 = $this->order_model->getFromId($order_id2);

        if (empty($order1) || empty($order2)) {
            $results['isUpdate'] = false;
            echo json_encode($results);
            return;
        }

        // Get menu items from both orders
        $menus1 = $this->order_menu_model->getDataByParam(['order_id' => $order_id1]);
        $menus2 = $this->order_menu_model->getDataByParam(['order_id' => $order_id2]);

        // Combine menu items
        try {
            log_message('debug', 'Starting menu combination process');
            log_message('debug', 'Menus1 count: ' . count($menus1));
            log_message('debug', 'Menus2 count: ' . count($menus2));

            foreach ($menus2 as $menu2) {
                log_message('debug', 'Processing menu2: ' . json_encode($menu2));
                $found = false;
                
                foreach ($menus1 as $menu1) {
                    log_message('debug', 'Comparing with menu1: ' . json_encode($menu1));
                    
                    if ($menu1['menu_id'] == $menu2['menu_id']) {
                        if($menu1['menu_title'] != $menu2['menu_title'] || $menu1['menu_price'] != $menu2['menu_price']) {
                            continue;
                        }
                        log_message('debug', 'Found matching menu_id');
                        
                        // Variation check logging
                        log_message('debug', 'Menu1 variation_id: ' . (isset($menu1['variation_id']) ? $menu1['variation_id'] : 'null'));
                        log_message('debug', 'Menu2 variation_id: ' . (isset($menu2['variation_id']) ? $menu2['variation_id'] : 'null'));

                        if(!empty($menu1['variation_id'])){
                            if(!empty($menu2['variation_id'])){
                                if($menu1['variation_id'] != $menu2['variation_id']){
                                    log_message('debug', 'Variation IDs do not match, continuing...');
                                    continue;
                                }
                            }else{
                                log_message('debug', 'Menu2 has no variation_id, continuing...');
                                continue;
                            }
                        } else {
                            if(!empty($menu2['variation_id'])){
                                log_message('debug', 'Menu1 has no variation_id but Menu2 does, continuing...');
                                continue;
                            }
                        }

                        // Same menu item found, update quantity
                        log_message('debug', 'Updating quantity - Original quantities: Menu1=' . $menu1['quantity'] . ', Menu2=' . $menu2['quantity']);
                        $menu1['quantity'] = intval($menu1['quantity']) + intval($menu2['quantity']);
                        log_message('debug', 'New quantity after combination: ' . $menu1['quantity']);
                        
                        try {
                            $update_result = $this->order_menu_model->updateRecord($menu1);
                            log_message('debug', 'Update result: ' . json_encode($update_result));
                        } catch (Exception $e) {
                            log_message('error', 'Error updating menu quantity: ' . $e->getMessage());
                            log_message('error', 'Menu data: ' . json_encode($menu1));
                            throw $e;
                        }
                        
                        $found = true;
                        break;
                    }
                }

                if (!$found) {
                    log_message('debug', 'No matching menu found, updating menu2 -> menu1 item');
                    try {
                        $menu2['order_id'] = $order_id1;
                        $update_result = $this->order_menu_model->updateRecord($menu2);
                        log_message('debug', 'Update result: ' . json_encode($update_result));
                    } catch (Exception $e) {
                        log_message('error', 'Error updating menu quantity: ' . $e->getMessage());
                        log_message('error', 'Menu data: ' . json_encode($menu2));
                        throw $e;
                    }
                }
            }

        } catch (Exception $e) {
            log_message('error', 'Exception in combineSeats: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            $results['isUpdate'] = false;
            $results['error'] = $e->getMessage();
            echo json_encode($results);
            return;
        }

        // Update order1's user count
        $order1['user_count'] = intval($order1['user_count']) + intval($order2['user_count']);

        // Update order1's from_time if order2's from_time is earlier
        if (strtotime($order2['from_time']) < strtotime($order1['from_time'])) {
            $order1['from_time'] = $order2['from_time'];
        }

        // Update order1's to_time if order2's to_time is later
        if (strtotime($order2['to_time']) > strtotime($order1['to_time'])) {
            $order1['to_time'] = $order2['to_time'];
        }

        // Update order1
        $this->order_model->updateRecord($order1);

        $this->order_menu_model->delete_force($order_id2, 'order_id');
        $this->order_model->delete_force($order_id2);

        $results['isUpdate'] = true;
        echo json_encode($results);
    }

    public function loadTableTitle(){
        $organ_id = $this->input->post('organ_id');
        $table_position = $this->input->post('table_position');

        $table_name_record = $this->table_name_model->getOneByParam( ['organ_id'=>$organ_id, 'table_position'=>$table_position]);
        $table_name = empty($table_name_record) ? '席'.$table_position : $table_name_record['table_name'];

        $results['isLoad'] = true;
        $results['table_name'] = $table_name;
        echo json_encode($results);
    }

    public function updateTableTitle(){
        $organ_id = $this->input->post('organ_id');
        $table_position = $this->input->post('table_position');
        $title = $this->input->post('title');

        $table_name_record = $this->table_name_model->getOneByParam( ['organ_id'=>$organ_id, 'table_position'=>$table_position]);
        if (empty($table_name_record)){
            $table_name_record = array(
                'organ_id' => $organ_id,
                'table_position' => $table_position,
                'table_name' => $title
            );
            $this->table_name_model->insertRecord($table_name_record);
        }else{
            $table_name_record['table_name'] = $title;
            $this->table_name_model->updateRecord($table_name_record);
        }

        $results['isUpdate'] = true;
        echo json_encode($results);
    }


    private function calcFlowTimeMinutes($from, $to, $isRound = true){
        $now = new DateTime($to);

        $min = $now->format('i');
        /*if($min<5){
            $to_time = $now->format('Y-m-d H:00:00');
        }else{
            if ($min%5>0){
                $min = ($min) - $min%5;
                if ($min<10) $min = '0'.$min;
            }
        }*/
		if ($isRound) {
			$d = 5-($min%5);
			$now->add(new DateInterval('PT'.$d.'M'));
		}

		$to_time = $now->format('Y-m-d H:i:00');

        $start_time = new DateTime($from);
        $end_time =  new DateTime($to_time);

        $diff = $end_time->diff($start_time);

        $d = $diff->format('%d');
        $h = $diff->format('%h');
        $i = $diff->format('%i');

        $duration = $d*24*60+$h*60+$i;

        return $duration;
    }

    private function calcSetAmount($duration, $set_start_time, $set_time, $set_amount){
        if(empty($set_time) || empty($set_amount) || empty($duration)) return 0;
		
		$start = 0;
		if (!empty($set_start_time)){
	        $set_start_time_array =  explode(':', $set_start_time);
			$start = $set_start_time_array[0]*60 + $set_start_time_array[1];
		}
        $set_time_array =  explode(':', $set_time);

        if (count($set_time_array)!=3) return 0;
        $set_min = $set_time_array[0]*60 + $set_time_array[1];

        $amount = intval(($duration-$start)/$set_min) * $set_amount;

        return $amount;

    }

	private function calcRoundAmount($amount, $is_round)
	{
		if (empty($is_round)) return $amount;

		$mod = $amount % 100;
		return intval($amount/100) * 100 + ($mod>=50 ? 100 : 0);
	}

}

?>
