<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . 'core/WebController.php';

/*
 *
 */

class Api extends WebController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('app_version_model');
        $this->load->model('oauth_info_model');
        $this->load->model('device_token_model');
        $this->load->model('message_model');
        $this->load->model('site_model');
        $this->load->model('connect_home_menu_model');
        $this->load->model('staff_organ_model');
    }

    public function loadAppVersion(){
        $app_id = $this->input->post('app_id');
        $os_type= $this->input->post('os_type');
        $data = $this->app_version_model->getLastVersion($app_id, $os_type);

        $results['isLoad'] = true;
        $results['version'] = $data['version_no'];
        $results['build'] = $data['build_no'];
        $results['test_flag'] = $data['test_flag'];

        echo json_encode($results);
    }

    public function loadAdminHome(){
        $company_id = $this->input->post('company_id');
        $staff_id = $this->input->post('staff_id');

        $unread_message_cnt = $this->message_model->getUnreadMessageCount('1', $company_id);

        $results['isLoad'] = true;
        $results['unread_messages'] = $unread_message_cnt;
        echo json_encode($results);
    }

    public function registerDeviceToken(){
        $staff_id = $this->input->post('staff_id');
        $device_token = $this->input->post('device_token');

        $exist = $this->device_token_model->getRecordByCondition(['device_token' => $device_token]);
        if(!empty($exist)){
            $this->device_token_model->delete_force($exist['id'], 'id');
        }

        $data = $this->device_token_model->getRecordByCondition(['staff_id' => $staff_id]);
        if (empty($data)){
            $data = array(
                'staff_id' => $staff_id,
                'device_token' => $device_token
            );
            $this->device_token_model->insertRecord($data);
        }else{
            $data['device_token'] = $device_token;
            $this->device_token_model->updateRecord($data, 'id');
        }

        $results['isSave'] = true;
        echo json_encode($results);
    }

    public function loadSaleSite(){
        $company_id = $this->input->post('company_id');
        $data = $this->site_model->getSiteInfo($company_id);

        $results['site_url'] = empty($data['site_url']) ? '' : $data['site_url'];
        echo json_encode($results);
    }

    public function loadConnectHomeMenuSetting(){
        $company_id = $this->input->post('company_id');
        $is_admin = $this->input->post('is_admin');
        if (empty($company_id)){
            $results['isLoad'] = false;
            echo json_encode($results);
            return;
        }

        $list = $this->connect_home_menu_model->getHomeMenuList($company_id, $is_admin);

        $results['isLoad'] = true;
        $results['menus'] = $list;

        echo json_encode($results);
    }

    public function saveConnectHomeMenuSetting(){
        $setting_id = $this->input->post('setting_id');
        $setting_value = $this->input->post('value');
        if (empty($setting_id)){
            $results['isSave'] = false;
            echo json_encode($results);
            return;
        }

        $data = $this->connect_home_menu_model->getFromId($setting_id);
        $data['is_use']=$setting_value;
        $this->connect_home_menu_model->updateRecord($data, 'id');

        $results['isSave'] = true;

        echo json_encode($results);
    }


    public function updateOrderHomeMenu(){
        $company_id = $this->input->post('company_id');
        $menu_id = $this->input->post('menu_id');
        $mode = $this->input->post('mode');
        if (empty($company_id)){
            $results['isLoad'] = false;
            echo json_encode($results);
            return;
        }

        $menu = $this->connect_home_menu_model->getFromId($menu_id);

        $sort = $menu['sort'];
        if ($mode=='up'){
            $prev_menu = $this->connect_home_menu_model->getHomePrevMenu($company_id, $sort);
            if (!empty($prev_menu)){
                $prev_sort = $prev_menu['sort'];
                $prev_menu['sort'] = $sort;
                $this->connect_home_menu_model->updateRecord($prev_menu, 'id');

                $menu['sort'] = $prev_sort;
                $this->connect_home_menu_model->updateRecord($menu, 'id');
            }
        }
        if ($mode=='down'){
            $next_menu = $this->connect_home_menu_model->getHomeNextMenu($company_id, $sort);
            if (!empty($next_menu)){
                $next_sort = $next_menu['sort'];
                $next_menu['sort'] = $sort;
                $this->connect_home_menu_model->updateRecord($next_menu, 'id');

                $menu['sort'] = $next_sort;
                $this->connect_home_menu_model->updateRecord($menu, 'id');
            }
        }

        $results['isUpdate'] = true;

        echo json_encode($results);
    }

    public function loadOrganPrintMaxOrder(){
        $organ_id = $this->input->post('organ_id');
        $print_date = $this->input->post('print_date');

        $this->load->model('organ_print_order_model');

        $record = $this->organ_print_order_model->getMaxOrder($organ_id, $print_date);

        $max=1;
        if (empty($record)){
            $this->organ_print_order_model->insertRecord(['organ_id'=>$organ_id,'print_date'=>$print_date, 'order_number'=>1]);
        }else{
            $max = $record['order_number']+1;
            $record['order_number'] = $max;
            $this->organ_print_order_model->updateRecord($record, 'id');
        }

        $results['isLoad'] = true;
        $results['max_order'] = $max;

        echo json_encode($results);
    }

    public function isFileCheck(){
        $path = $this->input->post('path');

        $results['isFile'] = is_file($path);

        echo json_encode($results);
    }

    public function loadStaffSort(){
        $staff_id = $this->input->post('staff_id');

        $this->load->model('staff_shift_sort_model');

        $staff_sorts = $this->staff_shift_sort_model->getSortList($staff_id);

        if (empty($staff_sorts)){
            $results['isLoad'] = false;
            echo json_encode($results);
            return;
        }
        $results['isLoad'] = true;
        $results['sorts'] = $staff_sorts;
        echo json_encode($results);
    }

    public function saveStaffSort(){
        $staff_id = $this->input->post('staff_id');
        $json_sorts = $this->input->post('sorts');

        $this->load->model('staff_shift_sort_model');

        $sorts = json_decode($json_sorts);

        for ($i=0;$i<count($sorts);$i++){
            $sort = $sorts[$i];
            $record = $this->staff_shift_sort_model->getOneByParam(['staff_id'=>$staff_id, 'show_staff_id'=>$sort]);
            if (empty($record)){
                $record['staff_id'] = $staff_id;
                $record['show_staff_id'] = $sort;
                $record['sort'] = $i+1;
                $this->staff_shift_sort_model->insertRecord($record);
            }else{
                $record['sort'] = $i+1;
                $this->staff_shift_sort_model->updateRecord($record, 'id');
            }
        }

        $results['isSave'] = true;
        echo json_encode($results);
    }

    public function exchangeSort(){
        $staff_id = $this->input->post('staff_id');
        $move_staff_id = $this->input->post('move_staff');
        $target_staff_id = $this->input->post('target_staff');

        $this->load->model('staff_shift_sort_model');

        $move_record = $this->staff_shift_sort_model->getOneByParam(['staff_id'=>$staff_id, 'show_staff_id'=>$move_staff_id]);
        $target_record = $this->staff_shift_sort_model->getOneByParam(['staff_id'=>$staff_id, 'show_staff_id'=>$target_staff_id]);

        $target_sort = $target_record['sort'];
        $target_record['sort'] = $move_record['sort'];
        $move_record['sort'] = $target_sort;

        $this->staff_shift_sort_model->updateRecord($move_record, 'id');
        $this->staff_shift_sort_model->updateRecord($target_record, 'id');

        $results['isSave'] = true;
        echo json_encode($results);
    }

    public function loadBadgeCount(){
        $condition = $this->input->post('condition');
        $cond = json_decode($condition, true);
        $this->load->model('notification_model');
        $data = $this->notification_model->getDataByParam($cond);
        $count = 0;
        foreach ($data as $item) { $count += $item['badge_count'];}
        $results['badge_count'] = $count;
        echo json_encode($results);
    }

    public function clearBadgeCount(){
        $condition = $this->input->post('condition');
        $cond = json_decode($condition, true);
        $this->load->model('notification_model');
        $notification = $this->notification_model->getOneByParam($cond);
        if (empty($notification)){
            $results['isClear'] = false;
        }else{
            $notification['badge_count'] = 0;
            $this->notification_model->updateRecord($notification, 'id');
            $results['isClear'] = true;
        }
        echo json_encode($results);
    }
	
	public function testmail(){
		try {

            $this->sendMailMessage('title_test', 'test_body', 'devtoworld@gmail.com');

        }catch (Exception $e){
            return false;
        }
		echo 'ok';
	}

    public function loadStaffs(){
        $organ_id = $this->input->post('organ_id');

        $organ_staffs = $this->staff_organ_model->getStaffs(['organ_id'=>$organ_id]);
        $result['staffs'] = $organ_staffs;

        echo json_encode($result);

    }

}
?>