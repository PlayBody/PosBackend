<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . 'core/WebController.php';

/*
 *
 */

class Api_accounting extends WebController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('pos_accounting_model');
        $this->load->model('pos_accounting_menu_model');
        $this->load->model('pos_staff_model');
        $this->load->model('pos_setting_model');
        $this->load->model('pos_member_model');
    }

    public function load_accounting()
    {
        $this->load->model('pos_staff_model');

        $staff_id = $this->input->post('staff_id');

        $staff = $this->pos_staff_model->getSettings($staff_id);
        if (empty($staff['member_id'])){
            return;
        }

        $count = empty($staff['accounting_count']) ? 7 : $staff['accounting_count'];

        $list = $this->pos_accounting_model->getList('*', array('member_id' => $staff['member_id']));
        $data = [];
        foreach ($list as $item) {
            if ($item->position > $count) continue;
            $data[$item->position] = $item;
        }
        $results = [];
        for($i=1;$i<=$count; $i++){
            $results[$i]['ischeck'] = false;   
            if (empty($data[$i])){
                $results[$i]['data']['title'] = '席'.$i;       
                $results[$i]['position'] = $i;          
            }else{ 
                $results[$i]['data'] = $data[$i];                
                $results[$i]['position'] = $i;

                // $listData = $this->order_list_model->getOrderList($data[$i]->accounting_id);
                if ($data[$i]->status>0){
                    $results[$i]['ischeck'] = true;   
                }

            }
        }

        echo(json_encode($results));
    }

    public function load_accounting_detail()
    {

        $staff_id = $this->input->post('staff_id');
        $position = $this->input->post('position');
        $staff = $this->pos_staff_model->getFromId($staff_id);
        if (empty($staff['member_id'])){
            return;
        }

        $account = $this->pos_accounting_model->getRecord($staff['member_id'], $position);
        
        $results = [];

        if (empty($account)){
            $account = [
                'member_id' => $staff['member_id'],
                'position' => $position,
                'title' => '席'.$position,
                'del_flag' => 0,
                'status' => '0',
                'create_date' => date('Y-m-d H:i:s'),
                'update_date' => date('Y-m-d H:i:s'),
            ];
            $insert = $this->pos_accounting_model->add($account); 
            if (!$insert){
                return;
            }

            $account['id'] = $insert;
        }

        $staff = $this->pos_staff_model->getFromId($staff_id);

        $set_amount = $this->getAccountingSetAmount($account);

        $results['admin'] = $staff['admin'];

        $results['title'] = $account['title'];
        $results['status'] = $account['status'];
        $results['list'] = $this->pos_accounting_menu_model->getOrderList($account['id']);

        $results['flow_time'] = '';
        $results['accounting_id'] = $account['id'];
        $results['start_time'] = [
            'hour'=> 0,
            'minutue' => 0
        ];
        $results['amount'] = '';
            
        if ($account['status']>0){
        
            $charge_amount = $this->pos_accounting_menu_model->getOrderAmount($account['id']);
            if ($set_amount+$charge_amount > 0){
                $results['amount'] = ($set_amount + $charge_amount) . '円';
            }

            $results['start_time'] = $this->convertDateFormat($account['start_time']);

            $results['flow_time'] = $this->getFlowTime($account['start_time'], date('Y-m-d H:i:s'));
        }
        
        if ($account['status']>1){
            $results['end_time'] = $account['end_time'];
            $results['flow_time'] = $this->getFlowTime($account['start_time'], $account['end_time']);
        }

        echo(json_encode($results));
    }

    public function update_accounting_title()
    {
     
        $staff_id = $this->input->post('staff_id');
        $position = $this->input->post('position');
        
        if (empty($staff_id) || empty($position)){
             $results['isUpdate'] = false;

            echo(json_encode($results));
            exit(0);
        }


        $staff = $this->pos_staff_model->getFromId($staff_id);
        if (empty($staff['member_id'])){
            return;
        }

        $update_title = $this->input->post('update_title');
        if (empty($update_title)) $update_title = '席'.$position;

        $account = $this->pos_accounting_model->getRecord($staff['member_id'], $position);
        
        $results = [];
        if (empty($account)){
             $results['isUpdate'] = false;

            echo(json_encode($results));
            exit(0);  
        }

        $account['update_date'] = date('Y-m-d H:i:s');
        $account['title'] = $update_title;

        $result = $this->pos_accounting_model->edit($account,'id');

        $results['isUpdate'] = true;
        $results['title'] = $update_title;

        echo(json_encode($results));

    }

    public function update_accounting_start_time()
    {
     
        $accounting_id = $this->input->post('accounting_id');
        $post_hour = $this->input->post('hour');
        $post_min = $this->input->post('min');

        if ($post_hour>23) $post_hour = 0;
        if ($post_min>59) $pos_min = 0;

        if ($post_hour<10)  $post_hour = "0".$post_hour;
        if ($post_min<10)  $post_min = "0".$post_min;

        $update_day = date('Y-m-d');
        $update_time = $update_day . " " . $post_hour . ":" . $post_min . ":00";

        if ($update_time > date('Y-m-d H:i:s')){
            $results['isUpdate'] = false;
            $results['msg'] = '現在の時刻以前に入力してください。';
            echo(json_encode($results));
            exit(0);
        }

        $account = $this->pos_accounting_model->getWhere(['accounting_id' => $accounting_id]);
        $results = [];
        if (empty($account)){
            $results['isUpdate'] = false;

            $results['msg'] = '操作が失敗しました。';
            echo(json_encode($results));
            exit(0);  
        }

        if ($account['status']==0){
             $results['isUpdate'] = false;

            $results['msg'] = '操作が失敗しました。';
            echo(json_encode($results));
            exit(0);  
        }

        if ($account['status']==2){
            if ($update_time > $account['end_time']){
                $results['isUpdate'] = false;
                $results['msg'] = '完了前の時間に、入力してください。';
                echo(json_encode($results));
                exit(0);
            }
        }

        $account['update_date'] = date('Y-m-d H:i:s');
        $account['start_time'] = $update_time;

        $result = $this->pos_accounting_model->edit($account,'id');

        $results['isUpdate'] = true;

        echo(json_encode($results));

    }

    public function delete_accounting_menu()
    {
        
        $id = $this->input->post('id');

        if (empty($id)){
            echo(json_encode(array('isDeleted' => false)));
            exit();
        }

        $this->pos_accounting_menu_model->delete_force($id, 'id');

        echo(json_encode(array('isDeleted' => true)));
    }

    public function update_accounting_status()
    {
        $this->load->model('pos_order_history_model');
        $this->load->model('pos_order_menu_model');

        $staff_id = $this->input->post('staff_id');
        $position = $this->input->post('position');
        $update_value = $this->input->post('update_value');

        if (empty($staff_id) || empty($position) || empty($update_value)){
            $results['isUpdate'] = false;
            echo(json_encode($results));
            exit(0);
        }

        $staff = $this->pos_staff_model->getFromId($staff_id);
        if (empty($staff['member_id'])){
            $results['isUpdate'] = false;
            return;
        }

        $account = $this->pos_accounting_model->getRecord($staff['member_id'], $position);

        if (empty($account)){
            $results['isUpdate'] = false;
            echo(json_encode($results));
            exit(0);
        }

        if (empty($account['status'])) $account['status'] = 0;

        $set_amount = $this->getAccountingSetAmount($account);

        $charge_amount = $this->pos_accounting_menu_model->getOrderAmount($account['id']);
        

        $next_status = $account['status'] + 1;

        if ($update_value != $next_status){
            $results['isUpdate'] = false;
            echo(json_encode($results));
            exit(0);
        }

        $now = date('Y-m-d H:i:s');

        $results = [];

        $account['update_date'] = $now;
        $account['status'] = $update_value;

        if ($update_value == 1){
            $account['start_time'] = $now;
            $account['end_time'] = '';
        }

        if ($update_value == 2){
            $account['end_time'] = $now;
        }

        if ($update_value == 3){


            $order_history = array(
                'member_id' => $staff['member_id'],
                'position' => $position,
                'amount' =>$set_amount + $charge_amount,
                'start_time' => $account['start_time'],
                'end_time' => $account['end_time'],
                'del_flag' => 0,
                'create_date' => date('Y-m-d H:i:s'),
                'update_date' => date('Y-m-d H:i:s')
            );

            $order_id = $this->pos_order_history_model->add($order_history);

            $menus = $this->pos_accounting_menu_model->getOrderList($account['id']);

            foreach ($menus as $item) {
                $order_menu = array(
                    'order_id' => $order_id,
                    'menu_title' =>$item['title'],
                    'quantity' => $item['quantity'],
                    'amount' => $item['amount'],
                    'del_flag' => 0,
                    'create_date' => date('Y-m-d H:i:s'),
                    'update_date' => date('Y-m-d H:i:s')
                );
                $this->pos_order_menu_model->add($order_menu);
            }

            $account['start_time'] = '';
            $account['end_time'] = '';
            $account['status'] = '0';
    
            $this->pos_accounting_menu_model->delete_force($account['id'], 'accounting_id');
        }

        $result = $this->pos_accounting_model->edit($account,'id');

        
        $results['isUpdate'] = true;

        echo(json_encode($results));

    }

    private function convertDateFormat($date){

        if (empty($date)) return '';

        $month = date('n', strtotime($date));
        $day = date('j', strtotime($date));
        $hour = date('G', strtotime($date));
        $minutue = date('i', strtotime($date));

        $datetimeAry = [
            'month' => (int)$month,
            'day' => (int)$day,
            'hour' => (int)$hour,
            'minutue' => (int)$minutue
        ];

        return $datetimeAry;
        // return $month.'月'.$day.'日'.$hour.'時'.$minutue.'分';
    }

    private function getFlowTime($from, $to, $isFormat = true)
    {
        $startDateTime = new DateTime($from);
        $endDateTime =  new DateTime($to);

        $diff = $endDateTime->diff($startDateTime);

        $d = $diff->format('%d');
        $h = $diff->format('%h');
        $i = $diff->format('%i');

        if ($isFormat){
            return $d*24+$h."時間".$i."分";
        }
        return ($d*24+$h)*60+$i;

    }

    private function getAccountingSetAmount($accounting){


        $staff = $this->pos_member_model->getFromId($accounting['member_id']);

        $set_amount = $staff['set_amount'];
        $set_time = $staff['set_time'];

        $table_amount = $staff['table_amount'];

        $amount = 0;
        if ($accounting['status']>'0'){
            $set_time_array = explode(':', $set_time);
            $set_time_min = $set_time_array[0]*60+$set_time_array[1];

            if ($accounting['status']=='1'){
                $flow_time = $this->getFlowTime($accounting['start_time'], date('Y-m-d H:i:s'), false);
            }

            if ($accounting['status']=='2'){
                $flow_time = $this->getFlowTime($accounting['start_time'], $accounting['end_time'], false);
            }

            if (!empty($set_time_min)){
                $amount = ((int)($flow_time/$set_time_min)+1)*$set_amount;
            }
        }

        $amount = $amount + $table_amount;

        return $amount;

    }
}
?>