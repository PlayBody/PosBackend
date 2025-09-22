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
        parent::__construct();

        $this->load->model('staff_organ_model');
        $this->load->model('organ_model');
        $this->load->model('company_model');
        $this->load->model('staff_model');
        $this->load->model('user_model');


        $this->load->model('reserve_model');
        $this->load->model('reserve_menu_model');

        $this->load->model('shift_model');
        $this->load->model('organ_time_model');
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

        $organ_time = $this->organ_time_model->getOrganFromToTime($organ_id);

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

            $diff1Day = new DateInterval('PT30M');
            $curDateTime->add($diff1Day);
            $cur_date = $curDateTime->format("Y-m-d H:i:s");
        }
        $results['isLoad'] = true;
        $results['regions'] = $regions;
        $results['staffs'] = $staff_list;
        $results['organ_from_time'] = $organ_from_time;
        $results['organ_to_time'] = $organ_to_time;

        $reserves = $this->reserve_model->getUserReserveData($from_date." 00:00:00", $to_date." 23:59:59", $user_id);
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

    function saveUserReserve(){
return;
        $organ_id = $this->input->post('organ_id');
        $user_id = $this->input->post('user_id');
        $staff_id = $this->input->post('staff_id');
        $reserve_start_time = $this->input->post('reserve_start_time');
        $reserve_end_time = $this->input->post('reserve_end_time');
//        $end_time = $this->input->post('end_time');
        $reserve_menu = $this->input->post('reserve_menu');
        $coupon_id = $this->input->post('coupon_id');
        $pay_method = $this->input->post('pay_method');
        $results = [];
        if (empty($organ_id) || empty($user_id)){
            $results['isSave'] = false;
            echo json_encode($results);
            return;
        }
        if (!$this->isPosibleReserve($organ_id, $user_id, $staff_id, $reserve_start_time, $reserve_end_time)){
            if (empty($reserve_id)){
                $results['isSave'] = false;
                echo json_encode($results);
                return;
            }

        }

        $reserve = array(
            'user_id' => $user_id,
            'organ_id' => $organ_id,
            'staff_id' => empty($staff_id) ? null : $staff_id,
            'reserve_time' => $reserve_start_time,
            'reserve_exit_time' => $reserve_end_time,
            'coupon_id' => empty($coupon_id)?null:$coupon_id,
            'pay_method' => 1,//empty($pay_method)?null:$pay_method,
//            'end_time' => $end_time,
            'reserve_status'=>1,
            'visible' => 1,
        );

        $reserve_id = $this->reserve_model->insertRecord($reserve);

        if (empty($reserve_id)){
            $results['isSave'] = false;
            echo json_encode($results);
            return;
        }

        $data = json_decode($reserve_menu);

        foreach ($data as $record) {
            $insertData = [];
            $insertData = array(
                'reserve_id' => $reserve_id,
                'menu_id' => $record->menu_id,
            );

            $insert = $this->reserve_menu_model->insertRecord($insertData);
        }


        if (!empty($staff_id)){
            $this->sendNotificationToStaffReserveRequest($user_id, $staff_id, $reserve_start_time, $reserve_end_time);
        }

        $results['isSave'] = true;
        echo json_encode($results);

    }

    private function sendNotificationToStaffReserveRequest($user_id, $staff_id, $from_time, $to_time){
        $user = $this->user_model->getFromId($user_id);
        $title = $user['user_first_name'] . ' ' . $user['user_last_name'] .  '様から施術が予約されました。';


        $fdate = new DateTime($from_time);
        $tdate = new DateTime($to_time);

        $content = $fdate->format('n月j日 H時i分').'から'. $tdate->format('H時i分') . 'まで施術が予約されました。';

        $is_fcm = $this->sendNotifications('reserve', $title, $content, $staff_id, $user_id, '1');

        return true;

    }

    public function loadReserveList(){
        $user_id = $this->input->post('user_id');
        $staff_id = $this->input->post('staff_id');
        $company_id = $this->input->post('company_id');

        $cond=[];

        if (!empty($staff_id)){
            $staff = $this->staff_model->getFromId($staff_id);
            if ($staff['staff_auth']==2){
                $organs = $this->staff_organ_model->getOrgansByStaff($staff_id);
                $cond['organ_ids'] = join(',' , array_column($organs,'organ_id'));
            }
            if ($staff['staff_auth']==3){
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

        $sum = 0;
        foreach ($menus as $menu){
            $price = $menu['menu_price']==null ? 0 : $menu['menu_price'];
            $sum = $sum + $price;
        }

        $reserve_year = substr($reserve['reserve_time'],0,4);
        $reserve['sum_amount'] = $sum;
        $reserve['menus'] = $menus;
        $reserve['reserve_year'] = $reserve_year;

        $organ = $this->organ_model->getFromId($reserve['organ_id']);

        $company = $this->company_model->getFromId($organ['company_id']);

        $user = $this->user_model->getFromId($reserve['user_id']);


        $results['isLoad'] = true;
        $results['company'] = $company;
        $results['organ'] = $organ;
        $results['user'] = $user;
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

    private function getReserveTimeStatus($organ_id, $staff_id, $sel_time){

        $organ = $this->organ_model->getFromId($organ_id);
        $table_count = $organ['table_count'] == null ? 10 : $organ['table_count'];

        if ($sel_time<=date('Y-m-d H:i:s')) return '0';

        $week = date('N', strtotime($sel_time));
        $time = date('H:i', strtotime($sel_time));
        $isActive = $this->organ_time_model->isActiveTime($organ_id, $week, $time);
        if (!$isActive) return '3';

        $reserve_count = $this->reserve_model->getReserveCount($organ_id, $sel_time);
        if ($reserve_count>=$table_count) return '3';

        $staff_reserve_count = $this->reserve_model->getReserveCount($organ_id, $sel_time, $staff_id);

       if (empty($staff_id)) {
           $activeStaffCount = $this->shift_model->getActiveStaffCount($organ_id, $sel_time);
            if ($reserve_count>=$activeStaffCount-$staff_reserve_count){
                return '2';
            }
       }else{
           if ($staff_reserve_count > 0) return '3';

           $isRejectActive = $this->shift_model->isStaffRejectReserve($organ_id, $staff_id, $sel_time);
           if ($isRejectActive) return '2';

           $isActive = $this->shift_model->isStaffActiveReserve($organ_id, $staff_id, $sel_time);
           if (!$isActive) return '2';
       }

        return '1';
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
        $staff_id = $this->input->post('staff_id');
        $reserve_time = $this->input->post('from_time');
        $reserve_exit_time = $this->input->post('to_time');

        $reserve = $this->reserve_model->getReserveRecord(['staff_id'=>$staff_id, 'reserve_time'=>$reserve_time, 'reserve_exit_time'=>$reserve_exit_time]);

        if (empty($reserve)){
            $results['isApply'] = false;
            echo json_encode($results);
            return;
        }

        $reserve['reserve_status'] = '2';

        $this->reserve_model->updateRecord($reserve);

        $results['isApply'] = true;
        echo json_encode($results);
    }

    public function rejectReserve(){
        $staff_id = $this->input->post('staff_id');
        $reserve_time = $this->input->post('from_time');
        $reserve_exit_time = $this->input->post('to_time');

        $reserve = $this->reserve_model->getReserveRecord(['staff_id'=>$staff_id, 'reserve_time'=>$reserve_time, 'reserve_exit_time'=>$reserve_exit_time]);

        if (empty($reserve)){
            $results['isReject'] = false;
            echo json_encode($results);
            return;
        }

        $reserve['reserve_status'] = '3';

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
        $user_id = $this->input->post('user_id');
        $organ_id = $this->input->post('organ_id');

        $cond = [];
        $cond['organ_id'] = $organ_id;
        $cond['user_id'] = $user_id;
        $cond['reserve_status'] = '1';
        $cond['from_time'] = date('Y-m-d H:i:s', strtotime(' -30 min'));
        $cond['to_time'] = date('Y-m-d H:i:s', strtotime(' +30 min'));
        $reserves = $this->reserve_model->getReserveNowData($cond);
        if (empty($reserves)){
            $results['isExistReserve'] = false;
        }else{
            $results['isExistReserve'] = true;
            foreach ($reserves as $reserve){
                $reserve['reserve_status']=2;
                $this->reserve_model->updateRecord($reserve, 'reserve_id');
            }
        }

        echo json_encode($results);

    }

}
?>