<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/core/UpdateController.php';

class Shift extends UpdateController
{
    /**
     * This is default constructor of the class
     */
    private $company_id;
    private $organs;

    public function __construct()
    {
        parent::__construct(ROLE_STAFF);

        $auth = $this->staff['staff_auth'];
        if ($auth < 2) {
            redirect('login');
        }

        $this->header['page'] = 'epark';
        $this->header['sub_page'] = 'receipt';
        $this->header['title'] = '予約受付'; //Reservation reception

        $this->load->model('organ_model');
        $this->load->model('organ_shift_time_model');

        $organ_cond = [];
        $organ_cond['company_id'] = $this->staff['company_id'];
        $organ_cond['is_sync_epark'] = 1;
        
        if ($auth < 3) $organ_cond['staff_id'] = $this->staff['staff_id'];
        
        $this->data['organs'] = $this->organ_model->getListByCond($organ_cond);

        

//
        $this->load->model('shift_model');
        $this->load->model('shift_status_model');

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
        $organ_id = $this->input->post('organ_id');
        if (empty($organ_id) && !empty($this->data['organs'][0]['organ_id'])){
            $organ_id = $this->data['organs'][0]['organ_id'];
        }
        // $company_id = 2;

        // $time_mode = $this->input->post('time_mode');
        // $type_mode = $this->input->post('type_mode');


        // // $this->load->helper('epark');
        // // $epark_helper = new Epark_helper($company_id);
        // // $r = $epark_helper->loadEparkReceipt(50, '2023-12-30');
        // // die();


        // $today = date('Y-m-d');

        // $this->data['today_date'] = $today;

        // $organ_list = $this->organ_model->getListByCond(['company_id' => $company_id]);
        // if (empty($organ_id)) $organ_id = $organ_list[0]['organ_id'];


        //$this->data['organs'] = $organ_list;

        $this->data['shift_status'] = $this->shift_status_model->getDataByParam([]);

        $this->data['select_date'] = date('Y-m-d');
        $this->data['organ_id'] = $organ_id;

        $this->data['time_mode'] = empty($time_mode) ? 2 : $time_mode;
        $this->data['type_mode'] = empty($type_mode) ? 1 : $type_mode;

        $this->load_view_with_menu("shift/shift_base");
    }

    
    public function ajaxLoadMain()
    {

        $organ_id = $this->input->post('organ_id');
        $time_mode = $this->input->post('time_mode');
        $type_mode = $this->input->post('type_mode');
        $sel_time = $this->input->post('sel_time');
        $select_date = $this->input->post('select_date');

        if(empty($organ_id)){

        }
        $organ = $this->organ_model->getFromId($organ_id);
        $company_id = $organ['company_id'];
        

        // $this->load->helper('epark');
        // $epark_helper = new Epark_helper($company_id);
        // $r = $epark_helper->loadEparkReceipt($organ_id, $select_date);

        $this->getTimes($organ_id, $select_date);

        // $staffs = $this->getAvailableStaffs($organ_id, $select_date);
        // $staffs = $this->loadReserves($staffs, $organ_id, $select_date);
        // $showStaffs = [];
        // foreach ($staffs as $staff){
        //     if (empty($staff['shifts']) && empty($staff['orders'])) continue;
        //     $showStaffs[] = $staff;
        // }

        // $waiting_orders = $this->order_model->getListByCond(['organ_id' => $organ_id, 'is_waiting' => 1]);

        // $waitings = [];
        // foreach ($waiting_orders as $order){
        //     $order['interval'] = empty($order['interval']) ? 0 : $order['interval'];
        //     $order_menus = $this->order_menu_model->getFirstMenu($order['id']);
        //     if ($order['is_reset']){
        //         $order['color'] = '#999999';
        //     }else{
        //         $order['color'] = empty($order_menus['color']) ? "#cfcfcf" : $order_menus['color'];
        //     }
        //     $order['border_color'] = $this->getDarkColor($order['color'], 1.5);
        //     $order['interval_color'] = $this->getLightColor($order['color'], 0.5);
        //     $waitings[] = $order;
        // }


        //var_dump($showStaffs);die();
        // $table_orders = $this->loadTableReserves($showStaffs);

        // $epark['staffs'] = $showStaffs;
        // $epark['waitings'] = $waitings;
        // $epark['table_orders'] = $table_orders;


        // $time_mode = empty($time_mode) ? 2 : $time_mode;

        // $organ_time['show_open_time'] = $sel_time * 60;
        // $organ_time['show_close_time'] = (($sel_time + 4) * 60) > $organ_time['close_time'] ? $organ_time['close_time'] : (($sel_time + 4) * 60);

        // $epark['organ_time'] = $organ_time;


        // $isLock  = $this->shift_lock_model->isLockSelectDate($select_date, $organ_id);

        // $this->data = $epark;
        // $this->data['isLock'] = $isLock;
        $this->getStaffs($organ_id, $select_date);
        $this->data['tables'] = $this->getTables($organ);

        $this->data['time_mode'] = $time_mode;
        $this->data['type_mode'] = empty($type_mode) ? 1 : $type_mode;
        $this->data['sel_time'] = empty($sel_time) ? intVal($this->data['business_from'] / 60)  : $sel_time;
        
        $this->load_view_empty("shift/ajax_main_content");
    }


    private function getTimes($organ_id, $select_date)
    {
        // init time set 8:00~23:00;
        $open_time = 60 * 8; 
        $close_time = 60 * 23;
        // ------------- init end ---------------- 
        
        $week_num = date('N', strtotime($select_date));
        $organ_time_row = $this->organ_shift_time_model->getMinMaxTimeByCond([
            'organ_id' => $organ_id,
            'weekday' => $week_num
        ]);

        if (!empty($organ_time_row) && !empty($organ_time_row['from_time'])) {
            $temp_froms = mb_split(':', $organ_time_row['from_time']);
            $temp_tos = mb_split(':', $organ_time_row['to_time']);

            $organ_time_from = intval($temp_froms[0]);
            $organ_time_from_minute = intval($temp_froms[1]);

            $organ_time_to = intval($temp_tos[0]);
            $organ_time_to_minute = intval($temp_tos[1]);

            // $open_time = $organ_time_from * 60;
            // $close_time = $organ_time_to_minute > 0 ? ($organ_time_to + 1) * 60 : $organ_time_to * 60;
            // $real_open_time = $organ_time_from * 60 + $organ_time_from_minute;
            // $real_close_time = $organ_time_to * 60 + $organ_time_to_minute;

            $open_time = $organ_time_from * 60 + $organ_time_from_minute;
            $close_time = $organ_time_to * 60 + $organ_time_to_minute;
        }
        

        $this->data['business_from'] = $open_time;
        $this->data['business_to'] = $close_time;

        //return ['open_time' => $open_time, 'close_time' => $close_time, 'real_open_time' => $real_open_time, 'real_close_time' => $real_close_time];
    }

    private function getStaffs($organ_id, $select_date){

        //$shifts = $this->shift_model->getShiftList(['organ_id' => $organ_id, 'select_date' => $select_date]);
//        foreach ($shifts as $shift){
        //     $cond = [
        //         'staff_id' => $shift['staff_id'],
        //         'organ_id' => $shift['organ_id'],
        //         'to_time' => $shift['from_time'],
        //         'shift_type' => $shift['shift_type'],
        //     ];
        //     if ($shift['shift_type']==SHIFT_STATUS_REQUEST || $shift['shift_type']==SHIFT_STATUS_ME_REPLY){
        //         $cond['old_shift'] = $shift['old_shift'];
        //     }
        //     $prev_shift = $this->shift_model->getOneByParam($cond);
        //     if (!empty($prev_shift)){
        //         $prev_shift['to_time'] = $shift['to_time'];
        //         $this->shift_model->updateRecord($prev_shift, 'shift_id');
        //         $this->shift_model->delete_force($shift['shift_id'], 'shift_id');
        //     }
  //      }

        //$staffs = $this->getAvailableStaffs($organ_id, $select_date);
        //$staffs = $this->loadReserves($staffs, $organ_id, $select_date);

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
                $shifts = $this->shift_model->getShiftList([
                    'staff_id' => $staff['staff_id'],
                    'select_date' => $select_date,
                    'organ_id' => $organ_id
                ]);
            }

            $tmp = [];
            $tmp = $staff;
            $tmp['shifts'] = $shifts;
            $tmp['orders'] = [];

            if (!empty($tmp)) $staffs[] = $tmp;
        }
    
        $showStaffs = [];
        foreach ($staffs as $staff){
            if (empty($staff['shifts']) && empty($staff['orders'])) continue;
            $showStaffs[] = $staff;
        }

        
        $this->data['staffs'] = $showStaffs;
    }

    private function getTables($organ)
    {
        $table_count = empty($organ['table_count']) ? 10 : $organ['table_count'];
        $tables = [];

        $tables = [];
        for ($i = 1; $i <= $table_count; $i++) {
            $table_name_record = $this->table_name_model->getOneByParam(['organ_id' => $organ['organ_id'], 'table_position' => $i]);
            $tables[$i]['table_name'] = empty($table_name_record['table_name']) ? ('席' . $i) : $table_name_record['table_name'];
            $tables[$i]['table_position'] = $i;
        }
        return $tables;
    }
}