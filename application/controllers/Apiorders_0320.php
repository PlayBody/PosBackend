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
                $cond['status_array'] = [ORDER_STATUS_RESERVE_REQUEST, ORDER_STATUS_RESERVE_REJECT, ORDER_STATUS_RESERVE_APPLY];
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
        $order['flow_time'] = $this->calcFlowTimeMinutes($order['from_time'], $to_time);
        $set_amount = $this->calcSetAmount($order['flow_time'], $order['set_time'], $order['set_amount']);
        $charge_amount = empty($order['charge_amount']) ? 0 : $order['charge_amount'];

        $order['menus'] = $this->order_menu_model->getDataByParam(['order_id'=>$order_id]);
        $menu_amount=0;
        foreach ($order['menus'] as $menu) $menu_amount += $menu['menu_price'] * $menu['quantity'];

        if(empty($order['amount']))
            $order['amount'] = $set_amount + $charge_amount + $menu_amount;

        $results['isLoad'] = true;
        $results['order'] = $order;

        echo json_encode($results);
    }

    public function addOrder(){
        $organ_id = $this->input->post('organ_id');
        $table_position = $this->input->post('table_position');
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
        if($min<5){
            $to_time = $now->format('Y-m-d H:00:00');
        }else{
            if ($min%5>0){
                $min = ($min-5) - $min%5;
                if ($min<10) $min = '0'.$min;
            }
            $now->add(new DateInterval('PT5M'));
            $to_time = $now->format('Y-m-d H:'.$min.':00');
        }

        $order['to_time'] = $to_time;

        $flow_time = $this->calcFlowTimeMinutes($order['from_time'], $to_time);
        $set_amount = $this->calcSetAmount($flow_time, $order['set_time'], $order['set_amount']);
        $charge_amount = empty($order['charge_amount']) ? 0 : $order['charge_amount'];

        $menus = $this->order_menu_model->getDataByParam(['order_id'=>$order_id]);
        $menu_amount=0;
        foreach ($menus as $menu) $menu_amount += $menu['menu_price'] * $menu['quantity'];

        $order['amount'] = $set_amount + $charge_amount + $menu_amount;
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
        $json_data = $this->input->post('update_data');

        $update_data = json_decode($json_data, true);

        $order = $this->order_model->getFromId($update_data['id']);
        if (empty($order)){
            $results['isUpdate'] = false;
            echo json_encode($results);
            return;
        }

        foreach ($update_data as $key => $val){
            $order[$key] = $val;
        }
        $this->order_model->updateRecord($order);
        $results['isUpdate'] = true;
        echo json_encode($results);
    }

    public function applyReserveOrder(){
        $order_id = $this->input->post('order_id');

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

    private function calcFlowTimeMinutes($from, $to){
        $now = new DateTime($to);
        $min = $now->format('i');
        if($min<5){
            $to_time = $now->format('Y-m-d H:00:00');
        }else{
            if ($min%5>0){
                $min = ($min-5) - $min%5;
                if ($min<10) $min = '0'.$min;
            }
            $now->add(new DateInterval('PT5M'));
            $to_time = $now->format('Y-m-d H:'.$min.':00');
        }


        $start_time = new DateTime($from);
        $end_time =  new DateTime($to_time);

        $diff = $end_time->diff($start_time);

        $d = $diff->format('%d');
        $h = $diff->format('%h');
        $i = $diff->format('%i');

        $duration = $d*24*60+$h*60+$i;

        return $duration;
    }

    private function calcSetAmount($duration, $set_time, $set_amount){
        if(empty($set_time) || empty($set_amount) || empty($duration)) return 0;
        $set_time_array =  explode(':', $set_time);
        if (count($set_time_array)!=3) return 0;
        $set_min = $set_time_array[0]*60 + $set_time_array[1];

        $amount = intval($duration/$set_min) * $set_amount;

        return $amount;

    }

}

?>
