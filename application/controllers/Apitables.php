<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . 'core/WebController.php';

class Apitables extends WebController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('table_model');
        $this->load->model('organ_model');
        $this->load->model('organ_set_table_model');
        $this->load->model('stamp_model');
        $this->load->model('staff_model');

        $this->load->model('table_menu_model');
        $this->load->model('table_menu_ticket_model');
        $this->load->model('history_table_model');
        $this->load->model('history_table_menu_model');
        $this->load->model('history_table_menu_ticket_model');
        $this->load->model('user_ticket_model');
        $this->load->model('user_model');
        $this->load->model('reserve_model');
        $this->load->model('reserve_menu_model');
    }

    public function loadTableRecord(){
        $table_id = $this->input->post('table_id');

        $results['table'] = $this->table_model->getFromId($table_id);

        echo json_encode($results);
    }

    public function loadTables(){
        $organ_id = $this->input->post('organ_id');
        $staff_id = $this->input->post('staff_id');
        $staff = $this->staff_model->getFromId($staff_id);

        $results = [];
        if(empty($organ_id)) {
            $results['isLoad'] = false;
            echo json_encode($results);
            return;
        }

        if ($staff['staff_auth']=='0'){
            $table_position = $staff['table_position'] == null ? 1 : $staff['table_position'];

            $table = $this->table_model->getTableRecord(['organ_id'=>$organ_id, 'position'=>$table_position]);
            if (empty($table)){
                $table = array(
                    'organ_id' => $organ_id,
                    'position' => $table_position,
                    'table_title' => '席'.$table_position,
                    'status' => '0',
                    'visible' => 1,
                );

                $this->table_model->insertRecord($table);

            }
            $table['seat_no'] = 'SEAT '.$table_position;

            if (!empty($table['staff_id'])){
                $table_staff = $this->staff_model->getFromId($table['staff_id']);
                $staff_name = $table_staff['staff_nick'];
                if (empty($staff_name)){
                    $staff_name = $table_staff['staff_first_name']. ' ' .$table_staff['staff_last_name'];
                }
                $table['staff_name'] = $staff_name;
            }

            $results['isLoad'] = true;
            $results['tables'] = [$table];
        }else{
            $setting = $this->organ_model->getFromId($organ_id);

            $table_count = empty($setting['table_count']) ? 4 : $setting['table_count'];

            $pos_amount = empty($setting['open_balance']) ? 0 : $setting['open_balance'];

            $table_list = $this->table_model->getTableList(['organ_id'=>$organ_id, 'count'=>$table_count]);

            $data = [];
            foreach ($table_list as $item) {
                $data[$item['position']] = $item;
            }
            $tables = [];
            for($i=1;$i<=$table_count; $i++){
                $tmp = [];
                if (empty($data[$i])){
                    $tmp = array(
                        'organ_id' => $organ_id,
                        'position' => $i,
                        'table_title' => '席'.$i,
                        'status' => '0',
                        'visible' => 1,
                    );
                    $tmp['table_id'] = $this->table_model->insertRecord($tmp);
                }else{
                    $tmp = $data[$i];
                }

                if (!empty($tmp['staff_id'])){
                    $table_staff = $this->staff_model->getFromId($tmp['staff_id']);
                    $staff_name = $table_staff['staff_nick'];
                    if (empty($staff_name)){
                        $staff_name = $table_staff['staff_first_name']. ' ' .$table_staff['staff_last_name'];
                    }
                    $tmp['staff_name'] = $staff_name;
                }
                $no = $i<10 ? '0'.$i: $i;
                $tmp['seat_no'] = 'SEAT '.$no;
                $tables[] = $tmp;
            }

            $today_amount = $this->history_table_model->getTodayHistoryAmount(date('Y-m-d'), $organ_id);

            $results['isLoad'] = true;
            $results['tables'] = $tables;
            $results['pos_amount'] = $pos_amount + $today_amount;
        }
        echo(json_encode($results));

    }

    public function loadTableDetail(){
        $table_id = $this->input->post('table_id');
        $organ_id = $this->input->post('organ_id');

        $results = [];
        if (empty($table_id) || empty($organ_id)){
            $results['isLoad'] = false;
            echo json_encode($results);
            return;
        }

        $table = $this->table_model->getFromId($table_id);

        if ($table['status']>0){
            $set_num = (empty($table) || empty($table['set_num'])) ? '1' : $table['set_num'];
            $setting = $this->organ_set_table_model->getRecordTable($organ_id, $set_num);

            $set_amount = 0;

            $start_time = $table['start_time'];
            $end_time = date('Y-m-d H:i:s');
            if ($table['status']>1){
                $end_time = $table['end_time'];
            }

            $from_time = new DateTime($start_time);
            $to_time =  new DateTime($end_time);

            $diff = $to_time->diff($from_time);

            $d = $diff->format('%d');
            $h = $diff->format('%h');
            $i = $diff->format('%i');

            $results['flow_time'] = $h.":".$i.":00";
            $duration = $d*24*60+$h*60+$i;

            if (!empty($setting['set_time'])){
                $tmp_time_array = explode(':', $setting['set_time']);
                $per_time = $tmp_time_array[0]*60+$tmp_time_array[1];

                if ($per_time>0){

                    $times = (int)($duration/$per_time);

                    if (empty($setting['set_amount'])) $setting['set_amount'] = 0;

                    $set_amount = $setting['set_amount']*$times;
                }
            }

            $table_amount = empty($setting['table_amount']) ? 0 : $setting['table_amount'];

            $table_menu_amount = $this->table_menu_model->getMenuAmountByCond(['table_id'=>$table_id]);

            $table_ticket_amount = $this->table_menu_ticket_model->getTicketAmountByCond(['table_id'=>$table_id]);

            $results['amount'] = $table_amount + $set_amount + $table_menu_amount - $table_ticket_amount;
        }

        $menus = $this->table_menu_model->getMenuListByCond(['table_id'=>$table_id]);

        $results['isLoad'] = true;
        $results['menus'] = $menus;
        $results['table'] = $table;
        $results['set_amount'] = empty($set_amount) ? 0 : $set_amount;
        $results['table_amount'] = empty($table_amount) ? 0 : $table_amount;

        echo json_encode($results);
    }

    public function updateTableStatus()
    {
        $table_id = $this->input->post('table_id');
        $organ_id = $this->input->post('organ_id');
        $update_value = $this->input->post('update_value');
        $user_id = $this->input->post('user_id');
        $staff_id = $this->input->post('staff_id');
        $pay_method = $this->input->post('pay_method');
        $person_count = $this->input->post('person_count');
        $set_number = $this->input->post('set_number');

        if (empty($table_id) || empty($organ_id) ||empty($update_value)){
            $results['isUpdate'] = false;
            echo(json_encode($results));
            exit(0);
        }

        if ($update_value=='1' && empty($user_id)){
            $results['isUpdate'] = false;
            echo(json_encode($results));
            exit(0);
        }

        $table = $this->table_model->getFromId($table_id);
        $organ_id = $table['organ_id'];

        if (empty($table)){
            $results['isUpdate'] = false;
            echo(json_encode($results));
            exit(0);
        }

        if (empty($table['status'])) $table['status'] = 0;

        $next_status = $table['status'] + 1;

        if ($update_value != $next_status){
            $results['isUpdate'] = false;
            echo(json_encode($results));
            exit(0);
        }

        $now = date('Y-m-d H:i:s');

        $results = [];

        $table['update_date'] = $now;
        $table['status'] = $update_value;

        if ($update_value == 1){
            $table['start_time'] = $now;
            $table['user_id'] = $user_id;

            $user = $this->user_model->getFromId($user_id);
            $user_name = $user['user_first_name'] .' '.$user['user_last_name'];
            $table['user_name'] = $user_name;
            $table['person_count'] = $person_count;
            $table['set_num'] = empty($set_number) ? '1' : $set_number;

            $reserve = $this->reserve_model->getReserveInputData(['organ_id' => $organ_id, 'user_id'=>$user_id, 'reserve_status'=>2, 'now_time'=>date('Y-m-d H:i:s')]);
            if (!empty($reserve)){
                $reserve_menus = $this->reserve_menu_model->getReserveMenuList($reserve['reserve_id']);
                foreach ($reserve_menus as $menu){
                    $table_menu = array(
                        'table_id'=>$table['table_id'],
                        'menu_id' => $menu['menu_id'],
                        'quantity' => '1',
                        'menu_title' => $menu['menu_title'],
                        'menu_price' => $menu['menu_price'],
                        'visible' => 1
                    );
                    $this->table_menu_model->insertRecord($table_menu);
                }

                $reserve['reserve_status'] = 5;
                $this->reserve_model->updateRecord($reserve, 'reserve_id');
            }
        }

        if ($update_value == 2){
            $table['end_time'] = $now;
        }

        if ($update_value == 3){

            $this->table_menu_model->delete_force($table_id, 'table_id');

            $table['user_id'] = null;
            $table['user_name'] = null;
            $table['start_time'] = null;
            $table['end_time'] = null;
            $table['status'] = '0';

            $reserve = $this->reserve_model->getReserveInputData(['organ_id' => $organ_id, 'user_id'=>$user_id, 'reserve_status'=>5, 'now_time'=>date('Y-m-d H:i:s')]);
            if (!empty($reserve)){
                $reserve['reserve_status'] = 10;
                $this->reserve_model->updateRecord($reserve, 'reserve_id');
            }
        }

        $table['staff_id'] = $staff_id;

        $result = $this->table_model->updateRecord($table,'table_id');

        if ($update_value == 2){

            $history_tables = array(
                'organ_id' => $organ_id,
                'table_title' => $table['table_title'],
                'table_position' => $table['position'],
                'user_id' => $table['user_id'],
                'table_charge_amount' => $this->getTableChargeAmount($organ_id, $table['table_id']),
                'set_amount' => $this->getSetAmount($organ_id, $table_id),
                'amount' =>$this->getTableAmount($organ_id, $table_id),
                'start_time' => $table['start_time'],
                'end_time' => $table['end_time'],
                'pay_method' => empty($pay_method) ? 1 : $pay_method,
                'person_count' => $table['person_count'],
                'visible' => 1,
            );

            $history_table_id = $this->history_table_model->insertRecord($history_tables);

            $menus = $this->table_menu_model->getMenuListByCond(['table_id'=>$table_id]);

            foreach ($menus as $item) {
                $histoty_table_menu = array(
                    'history_table_id' => $history_table_id,
                    'menu_title' =>$item['menu_title'],
                    'menu_id' =>$item['menu_id'],
                    'variation_id' =>$item['variation_id'],
                    'quantity' => $item['quantity'],
                    'menu_price' => $item['menu_price'],
                    'visible' => 1,
                );
                $histoty_table_menu_id = $this->history_table_menu_model->insertRecord($histoty_table_menu);

                $tickets = $this->table_menu_ticket_model->getListByCond(['table_menu_id'=>$item['table_menu_id']]);
                foreach ($tickets as $ticket_item){
                    $insert_ticket=array(
                        'history_table_menu_id' => $histoty_table_menu_id,
                        'ticket_id' => $ticket_item['ticket_id'],
                        'count'=> $ticket_item['count']
                    );

                    $this->history_table_menu_ticket_model->insertRecord($insert_ticket);

                    $user_ticket = $this->user_ticket_model->getUserTicket(['user_id'=>$user_id, 'ticket_id'=>$ticket_item['ticket_id']]);
                    if (!empty($user_ticket)){
                        $user_ticket['count'] = $user_ticket['count']-$ticket_item['count'];
                        $this->user_ticket_model->updateRecord($user_ticket, 'id');
                    }
                }
                $this->table_menu_ticket_model->delete_force($item['table_menu_id'], 'table_menu_id');
            }
        }
        // add stamp
        if ($update_value == 1 && $user_id != '1') {
            $organ = $this->organ_model->getFromId($organ_id);
            $stamp = array(
                'date' => date('Y-m-d'),
                'user_id'=>$user_id,
                'company_id' =>$organ['company_id'],
                'organ_id' => $organ_id,
                'staff_id' => $staff_id,
                'use_flag' => 1,
            );

            $this->stamp_model->insertRecord($stamp);

        }

        $results['isUpdate'] = true;

        echo(json_encode($results));

    }

    public function deleteTableMenu(){
        $table_menu_id = $this->input->post('table_menu_id');

        $results = [];
        if (empty($table_menu_id)){
            $results['isDelete'] = false;
            echo json_encode($results);
            return;
        }

        $this->table_menu_model->delete_force($table_menu_id, 'table_menu_id');

        $results['isDelete'] = true;
        echo json_encode($results);
    }

    private function getTableAmount($organ_id, $table_id){

        $table_menu_amount = $this->table_menu_model->getMenuAmountByCond(['table_id'=>$table_id]);
        $table_ticket_amount = $this->table_menu_ticket_model->getTicketAmountByCond(['table_id'=>$table_id]);

        return $this->getTableChargeAmount($organ_id, $table_id) + $this->getSetAmount($organ_id, $table_id) + $table_menu_amount-$table_ticket_amount;
    }

    private function getTableChargeAmount($organ_id, $table_id){
        $table = $this->table_model->getFromId($table_id);
        $set_num = (empty($table) || empty($table['set_num'])) ? '1' : $table['set_num'];
        $setRecord = $this->organ_set_table_model->getRecordTable($organ_id, $set_num);

        $table_amount = (empty($setRecord) || empty($setRecord['table_amount'])) ? 0 : $setRecord['table_amount'];

        return $table_amount;
    }

    private function getSetAmount($organ_id, $table_id){
        $table = $this->table_model->getFromId($table_id);
        $set_num = (empty($table) || empty($table['set_num'])) ? '1' : $table['set_num'];
        $setting = $this->organ_set_table_model->getRecordTable($organ_id, $set_num);
        if (empty($setting)) return 0;
        $set_amount = 0;

        $table = $this->table_model->getFromId($table_id);

        $start_time = $table['start_time'];
        $end_time = date('Y-m-d H:i:s');
        if ($table['status']>1){
            $end_time = $table['end_time'];
        }

        $from_time = new DateTime($start_time);
        $to_time =  new DateTime($end_time);

        $diff = $to_time->diff($from_time);

        $d = $diff->format('%d');
        $h = $diff->format('%h');
        $i = $diff->format('%i');

        $results['flow_time'] = $h.":".$i.":00";
        $duration = $d*24*60+$h*60+$i;

        if (!empty($setting['set_time'])){
            $tmp_time_array = explode(':', $setting['set_time']);
            $per_time = $tmp_time_array[0]*60+$tmp_time_array[1];

            if ($per_time>0){

                $times = (int)($duration/$per_time);

                if (empty($setting['set_amount'])) $setting['set_amount'] = 0;

                $set_amount = $setting['set_amount']*$times;
            }
        }

        return $set_amount;
    }

    public function updateTableTitle()
    {
        $table_id = $this->input->post('table_id');
        $update_title = $this->input->post('update_title');

        $results = [];
        if (empty($table_id)){
            $results['isUpdate'] = false;

            echo(json_encode($results));
            exit(0);
        }

        $table = $this->table_model->getFromId($table_id);

        if (empty($table)){
            $results['isUpdate'] = false;

            echo(json_encode($results));
            exit(0);
        }

        $table['update_date'] = date('Y-m-d H:i:s');
        $table['table_title'] = $update_title;

        $result = $this->table_model->edit($table,'table_id');

        $results['isUpdate'] = true;
        $results['title'] = $update_title;

        echo(json_encode($results));

    }

    public function updateTableStarTime()
    {

        $table_id = $this->input->post('table_id');
        $post_hour = $this->input->post('hour');
        $post_min = $this->input->post('min');

        if ($post_hour>23) $post_hour = 0;
        if ($post_min>59) $pos_min = 0;

        if ($post_hour<10)  $post_hour = "0".$post_hour;
        if ($post_min<10)  $post_min = "0".$post_min;

        $update_day = date('Y-m-d');
        $update_time = $update_day . " " . $post_hour . ":" . $post_min . ":00";

        $results = [];
        if ($update_time > date('Y-m-d H:i:s')){
            $results['isUpdate'] = false;
            $results['msg'] = '現在の時刻以前に入力してください。';
            echo(json_encode($results));
            exit(0);
        }

        $table = $this->table_model->getFromId($table_id);
        if (empty($table)){
            $results['isUpdate'] = false;

            $results['msg'] = '操作が失敗しました。';
            echo(json_encode($results));
            exit(0);
        }

        if ($table['status']==0){
            $results['isUpdate'] = false;

            $results['msg'] = '操作が失敗しました。';
            echo(json_encode($results));
            exit(0);
        }

        if ($table['status']==2){
            if ($update_time > $table['end_time']){
                $results['isUpdate'] = false;
                $results['msg'] = '完了前の時間に、入力してください。';
                echo(json_encode($results));
                exit(0);
            }
        }


        $table['update_date'] = date('Y-m-d H:i:s');
        $table['start_time'] = $update_time;

        $result = $this->table_model->edit($table,'table_id');

        $results['isUpdate'] = true;

        echo(json_encode($results));

    }

    public function saveRejectTable(){
        $organ_id = $this->input->post('organ_id');

        //$table = $this->table_model->getFromId($table_id);

        $reject = array(
            'organ_id' => $organ_id,
            'table_position' => null,
            'table_title' => null,
            'is_reject' => '1',
            'start_time' => date('Y-m-d H:i:s'),
            'end_time' => date('Y-m-d H:i:s'),
            'visible' => '1'
        );

        $this->history_table_model->insertRecord($reject);

        $results['isSave'] = true;

        echo json_encode($results);
    }
    public function updateTableData(){
        $json_data = $this->input->post('update_data');

        $update_data = json_decode($json_data, true);

        $table = $this->table_model->getFromId($update_data['table_id']);
        if (empty($table)){
            $results['isUpdate'] = false;
            echo json_encode($results);
            return;
        }

        foreach ($update_data as $key => $val){
            $table[$key] = $val;
        }
        $this->table_model->updateRecord($table, 'table_id');
        $results['isUpdate'] = true;
        echo json_encode($results);
    }
}
?>
