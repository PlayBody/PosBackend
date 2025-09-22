<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . 'core/WebController.php';

/*
 *
 */

class Apistaffs extends WebController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('staff_model');
        $this->load->model('staff_setting_model');
        $this->load->model('organ_model');
        $this->load->model('staff_organ_model');
        $this->load->model('attendance_model');
        $this->load->model('staff_point_setting_model');
        $this->load->model('staff_point_add_model');
        $this->load->model('reserve_model');
        $this->load->model('staff_enable_menu_model');

    }

    public function login()
    {
        $login_data = array();
        $login_data['login_id'] = $this->input->post('email');
        $login_data['password'] = $this->input->post('password');

        $staff = $this->staff_model->login($login_data);

        $results = [];
        $results['isLogin'] = false;
        $results['err_type'] = '2';
        $results['staff'] = array();

        $organ = [];
        if (!empty($staff)){
            $results['isLogin'] = true;
            $results['staff'] = $staff;

            if ($staff['staff_auth']>4)
                $company['visible']='1';
            else
                $company = $this->company_model->getFromId($staff['company_id']);

            if (empty($company) || $company['visible']!='1'){
                $results['isLogin'] = false;
                $results['err_type'] = '1';
                $results['staff'] = $staff;
            }else{
                $organs = $this->staff_organ_model->getOrgansByStaff($staff['staff_id']);
                if (!empty($organs)) $organ = $organs[0];
            }


        }
        $results['organ'] = $organ;

        echo(json_encode($results));
    }

    public function getStaffs(){
        $condition = $this->input->post('condition');
        $cond = json_decode($condition, true);

        $staffs = $this->staff_model->getStaffs($cond);

        $results['isLoad'] = true;
        $results['staffs'] = $staffs;

        echo json_encode($results);
    }

    public function renderAvatar(){

        $staff_id = $this->input->get('staff_id');
        if($staff_id == null)
        {
            $file = 'noImage.jpg';
        }else{
            $staff = $this->staff_model->getFromId($staff_id);
            if (empty($staff) || empty($staff['staff_image'])){
                $file = 'noImage.jpg';
            }else{
                $file = $staff['staff_image'];
            }
        }
        $file = './assets/images/staffs/'.$file;
        if (!is_file($file)) $file = './assets/images/staffs/noImage.jpg';

        header("Content-Type: image/png");
        header("Content-Length: " . filesize($file));
        echo file_get_contents($file);
        exit;
   }

    public function loadStaffAttendance(){
        $staff_id = $this->input->post('staff_id');

        $results = [];
        if(empty($staff_id)) {
            $results['isLoad'] = false;
            echo json_encode($results);
            return;
        }

        $organs = $this->staff_organ_model->getOrgansByStaff($staff_id);

        $results['organs'] = $organs;

        $attendance = $this->attendance_model->getLastAttendance($staff_id);

        if (empty($attendance)){
            $results['isLoad'] = false;
            echo json_encode($results);
            return;
        }

        $results['isLoad'] = true;
        $results['attendance'] = $attendance;

        echo json_encode($results);
    }

    public function updateStaffAttendance()
    {
        $staff_id = $this->input->post('staff_id');
        $organ_id = $this->input->post('organ_id');
        $update_status = $this->input->post('status');

        $results = [];
        if (empty($staff_id) || empty($organ_id)){
            $results['isUpdate'] = false;
            echo json_encode($results);
            return;
        }

        $attendance = array(
            'staff_id' => $staff_id,
            'organ_id' => $organ_id,
            'attendance_status' => $update_status,
            'attendance_time' => date('Y-m-d H:i:s'),
            'visible' => 1,
            'create_date' => date('Y-m-d H:i:s'),
            'update_date' => date('Y-m-d H:i:s'),
        );

        $insert = $this->attendance_model->add($attendance);


        $results['isUpdate'] = true;

        echo(json_encode($results));

    }

    public function loadStaffByGroupList(){
        $staff_id = $this->input->post('staff_id');

        $results = [];
        if (empty($staff_id)){
            $results['isLoad'] = false;
            echo json_encode($results);
            return;
        }

        $staff = $this->staff_model->getFromId($staff_id);
        $auth = empty($staff['staff_auth']) ? 1 : $staff['staff_auth'];

        $organs = $this->getOrgansByStaffPermission($staff_id);

        $data = [];
        foreach ($organs as $item){
            $tmp = [];
            $tmp['organ_id'] = $item['organ_id'];
            $tmp['organ_name'] = $item['organ_name'];
            $staffs = $this->staff_organ_model->getStaffsByOrgan($item['organ_id'], $auth);
			$first_staff_id = empty($staffs[0]['staff_id'])? '': $staffs[0]['staff_id'];
			if($staff['staff_auth']>4){
				$owner_staffs = $this->staff_model->getStaffList(['company_id'=>$item['company_id'], 'staff_auth'=>4]);
				foreach($owner_staffs as $owner){
					$key = array_search($owner['staff_id'], array_column($staffs, 'staff_id'));
					if($key==false && $owner['staff_id']!=$first_staff_id){
						array_push($staffs, $owner);
					}
				}
			}
            $tmp['staffs'] = $staffs;
            $data[] = $tmp;
        }

        $results['isLoad'] = true;
        $results['data'] = $data;

        echo json_encode($results);
    }

    public function loadStaffCompanyList(){
        $company_id = $this->input->post('company_id');

        $results = [];
        if (empty($company_id)){
            $results['isLoad'] = false;
            echo json_encode($results);
            return;
        }

        $cond['company_id'] = $company_id;
        $staffs = $this->staff_model->getStaffList($cond);

        $results['isLoad'] = true;
        $results['data'] = $staffs;

        echo json_encode($results);
    }

    public function loadStaffInfo(){
        $staff_id = $this->input->post('staff_id');
        $staff = $this->staff_model->getFromId($staff_id);

        echo json_encode($staff);
    }

    public function loadStaffDetail(){
        $staff_id = $this->input->post('staff_id');

        if (empty($staff_id)){
            $results['isLoad'] = false;
            echo json_encode($results);
            return;
        }

        $staff = $this->staff_model->getFromId($staff_id);

        $results['isLoad'] = true;
        $results['staff'] = $staff;

        echo json_encode($results);
    }


    public function saveStaffInfo()
    {
        $staff_organs = $this->input->post('staff_organs');
        $organs = json_decode($staff_organs);

        $company_id = '';
        $isOrganCheck = true;
        foreach ($organs as $organ_id){
            $cur_organ = $this->organ_model->getFromId($organ_id);
            if (empty($company_id)){
                $company_id = $cur_organ['company_id'];
            }elseif ($cur_organ['company_id']!=$company_id){
                $isOrganCheck = false;
            }
        }

        if (!$isOrganCheck){
            $results['isSave'] =false;
            $results['err_type'] = 'organ_input_err';
            echo json_encode($results);
            return;
        }

        if (!$this->staff_model->isMailCheck($this->input->post('staff_mail'), $this->input->post('staff_id'))){
            $results['isSave'] =false;
            $results['err_type'] = 'mail_input_err';
            echo json_encode($results);
            return;
        }


        $staff_id = $this->input->post('staff_id');
        $staff_auth = (empty($this->input->post('staff_auth')) && $this->input->post('staff_auth')!='0') ? 1 : $this->input->post('staff_auth');
        $staff_first_name = $this->input->post('staff_first_name');
        $staff_first_name = $this->input->post('staff_first_name');
        $staff_last_name = $this->input->post('staff_last_name');
        $staff_nick = $this->input->post('staff_nick');
        $staff_tel = $this->input->post('staff_tel');
        $staff_mail = $this->input->post('staff_mail');
        $staff_password = $this->input->post('staff_password');
        $staff_sex = $this->input->post('staff_sex');
        $staff_birthday = $this->input->post('staff_birthday');
        $staff_entering_date = $this->input->post('staff_entering_date');
        $staff_grade_level = empty($this->input->post('grade_level')) ? null : $this->input->post('grade_level');
        $staff_national_level = empty($this->input->post('national_level')) ? null : $this->input->post('national_level');
        $staff_organs = $this->input->post('staff_organs');
        $staff_salary_months = empty($this->input->post('staff_salary_months')) ? null : $this->input->post('staff_salary_months');
        $staff_salary_days =  empty($this->input->post('staff_salary_days')) ? null : $this->input->post('staff_salary_days');
        $staff_salary_minutes = empty($this->input->post('staff_salary_minutes')) ? null : $this->input->post('staff_salary_minutes');
        $staff_salary_times = empty($this->input->post('staff_salary_times')) ? null : $this->input->post('staff_salary_times');
        $staff_shift = $this->input->post('staff_shift');
        $table_position = empty($this->input->post('table_position')) ? null : $this->input->post('table_position');
        $staff_comment = $this->input->post('staff_comment');
        $menu_response = empty($this->input->post('menu_response')) ? null : $this->input->post('menu_response');
        $add_rate = empty($this->input->post('add_rate')) ? null : $this->input->post('add_rate');
        $test_rate = empty($this->input->post('test_rate')) ? null : $this->input->post('test_rate');
        $quality_rate = empty($this->input->post('quality_rate')) ? null : $this->input->post('quality_rate');

        $staff_avatar =empty($this->input->post('staff_avatar')) ? null : $this->input->post('staff_avatar');

//        if (!empty($image_stream)) {
//            $data = base64_decode($image_stream);
//            $im = imagecreatefromstring($data);
//            if ($im !== false) {
//                $file_name = 'avatar-'.date('YmdHis').'.jpg';
//                $output = './assets/images/avatar/'.$file_name;
//                imagejpeg($im, $output);
//                // file_put_contents($output, file_get_contents($im));
//            }
//        }

        if (empty($staff_id)){
            $staff = [];
            $staff['staff_auth'] = 1;
            $staff['company_id'] = $company_id;
            $staff['staff_auth'] = $staff_auth;
            $staff['staff_image'] = $staff_avatar;
            $staff['staff_first_name'] = $staff_first_name;
            $staff['staff_last_name'] = $staff_last_name;
            $staff['staff_nick'] = empty($staff_nick) ? null : $staff_nick;
            $staff['staff_tel'] = $staff_tel;
            $staff['staff_password'] = sha1('12345');
            $staff['staff_mail'] = $staff_mail;
            $staff['staff_shift'] = $staff_shift;
            $staff['table_position'] = $table_position;
            $staff['staff_sex'] = $staff_sex;
            $staff['staff_birthday'] = $staff_birthday;
            $staff['staff_entering_date'] = $staff_entering_date;
            $staff['staff_grade_level'] = $staff_grade_level;
            $staff['staff_national_level'] = $staff_national_level;
            $staff['staff_salary_months'] = empty($staff_salary_months) ? null : $staff_salary_months;
            $staff['staff_salary_days'] = empty($staff_salary_days) ? null : $staff_salary_days;
            $staff['staff_salary_minutes'] = empty($staff_salary_minutes) ? null : $staff_salary_minutes;
            $staff['staff_salary_times'] = empty($staff_salary_times) ? null : $staff_salary_times;
            $staff['staff_comment'] = $staff_comment;
            $staff['menu_response'] = $menu_response;
            $staff['add_rate'] = $add_rate;
            $staff['test_rate'] = $test_rate;
            $staff['quality_rate'] = $quality_rate;
            $staff['visible'] = 1;
            $staff['sort_no'] = $this->staff_model->getSortMax();
            $staff['create_date'] = date('Y-m-d H:i:s');
            $staff['update_date'] = date('Y-m-d H:i:s');

            $staff_id = $this->staff_model->add($staff);

        }else{
            $staff = $this->staff_model->getFromId($staff_id);
            if (!empty($staff_avatar))  $staff['staff_image'] = $staff_avatar;
            $staff['staff_auth'] = $staff_auth;
            $staff['staff_first_name'] = $staff_first_name;
            $staff['staff_last_name'] = $staff_last_name;
            $staff['staff_nick'] = empty($staff_nick) ? null : $staff_nick;
            $staff['staff_tel'] = $staff_tel;
            $staff['staff_mail'] = $staff_mail;
            $staff['staff_shift'] = $staff_shift;
            $staff['staff_sex'] = $staff_sex;
            $staff['staff_birthday'] = $staff_birthday;
            $staff['staff_entering_date'] = $staff_entering_date;
            $staff['staff_grade_level'] = $staff_grade_level;
            $staff['staff_national_level'] = $staff_national_level;
            $staff['table_position'] = $table_position;
            $staff['staff_salary_months'] = empty($staff_salary_months) ? null : $staff_salary_months;
            $staff['staff_salary_days'] = empty($staff_salary_days) ? null : $staff_salary_days;
            $staff['staff_salary_minutes'] = empty($staff_salary_minutes) ? null : $staff_salary_minutes;
            $staff['staff_salary_times'] = empty($staff_salary_times) ? null : $staff_salary_times;
            $staff['staff_comment'] = $staff_comment;
            $staff['menu_response'] = $menu_response;
            $staff['add_rate'] = $add_rate;
            $staff['test_rate'] = $test_rate;
            $staff['quality_rate'] = $quality_rate;

            if (!empty($staff_password))
                $staff['staff_password'] = sha1($staff_password);


            $staff['update_date'] = date('Y-m-d H:i:s');

            $this->staff_model->edit($staff, 'staff_id');
        }


        $old_organs = $this->staff_organ_model->getStaffOrganList(['staff_id'=>$staff_id]);
        foreach ($old_organs as $item){
            $is_exist = false;
            foreach ($organs as $organ_id){
                if ($organ_id==$item['organ_id']){
                    $is_exist = true;
                    break;
                }
            }
            if (!$is_exist){
                $this->staff_organ_model->delete_force($item['id']);
            }

        }

        foreach ($organs as $organ_id){
            $auth = $this->staff_organ_model->getAuthRecord($staff_id, $organ_id);
            if (empty($auth)){
                $auth = array(
                    'staff_id'=>$staff_id,
                    'organ_id' =>$organ_id,
                    'auth' => 1
                );
                $this->staff_organ_model->add($auth);
            }
        }

        $results['isSave'] = true;
        $results['staff_id'] = $staff_id;

        echo(json_encode($results));
    }


    public function deleteStaffInfo(){
        $staff_id = $this->input->post('staff_id');
        $login_staff_id = $this->input->post('login_staff_id');

        $results = [];
        if (empty($staff_id)||empty($login_staff_id)){
            $results['isDelete'] = false;

            echo json_encode($results);
            return;
        }

        $login_staff = $this->staff_model->getFromId($login_staff_id);
        if (empty($login_staff['staff_auth'])){
            $results['isDelete'] = false;

            echo json_encode($results);
            return;
        }
        $auth = $login_staff['staff_auth'];

        if ($auth==2){
            $staff_organs = $this->staff_organ_model->getOrgansByStaff($staff_id);
            $owner_organs = $this->staff_organ_model->getOrgansByStaff($login_staff_id);

            $ischeck=true;
            foreach ($staff_organs as $item){
                if (!in_array($item['organ_id'], array_column($owner_organs, 'organ_id'))){
                    $ischeck = false;
                    break;
                }
            }

            if (!$ischeck){
                $results['isDelete'] = false;
                $results['msg'] = 'organ_contain_err';

                echo json_encode($results);
                return;
            }
        }

        $this->staff_model->delete_force($staff_id, 'staff_id');
        $this->staff_organ_model->delete_force($staff_id, 'staff_id');

        $results['isDelete'] = true;

        echo json_encode($results);

    }

    public function loadStaffSetting(){
        $staff_id = $this->input->post('staff_id');

        $results = [];
        if (empty($staff_id)){
            $results['isLoad'] = false;

            echo json_encode($results);
            return;
        }

        $setting = $this->staff_setting_model->getStaffSetting($staff_id);
        if (empty($setting)){
            $setting = array(
                'staff_id' => $staff_id,
                'push' => 1,
                'face' => 1
            );

            $this->staff_setting_model->add($setting);
        }

        $results['isLoad'] = true;
        $results['setting'] = $setting;

        echo json_encode($results);

    }

    public function saveStaffSetting(){
        $staff_id = $this->input->post('staff_id');
        $option = $this->input->post('option');
        $value = $this->input->post('value');

        $results = [];
        if (empty($staff_id)){
            $results['isSave'] = false;

            echo json_encode($results);
            return;
        }

        $setting = $this->staff_setting_model->getStaffSetting($staff_id);

        if (empty($setting)){
            $results['isSave'] = false;

            echo json_encode($results);
            return;
        }

        $setting[$option] = $value;
        $this->staff_setting_model->edit($setting, 'setting_id');

        $results['isSave'] = true;

        echo json_encode($results);
    }

    public function loadStaffPoint(){
        $staff_id = $this->input->post('staff_id');
        $setting_year = $this->input->post('setting_year');
        $setting_month = $this->input->post('setting_month');
        $organ_id = $this->input->post('organ_id');

        $results = [];
        if (empty($staff_id) || empty($setting_year) || empty($setting_month)){
            $results['isLoad'] = false;
            echo json_encode($results);
            return;
        }

        $cond = [];
        $cond['staff_id'] = $staff_id;
        $cond['setting_year'] = $setting_year;
        $cond['setting_month'] = $setting_month;

        $point_setting = $this->staff_point_setting_model->getSettingData($cond);

        if (empty($point_setting)){
            $last_setting = $this->staff_point_setting_model->getLastSetting($staff_id, $setting_year.'-'.$setting_month);
            if (!empty($last_setting)){
                $point_setting = array(
                    'staff_id' => $staff_id,
                    'setting_year' => $setting_year,
                    'setting_month' => $setting_month,
                    'menu_response' => $last_setting['menu_response'],
                    'add_rate' => $last_setting['add_rate'],
                    'test_rate' => $last_setting['test_rate'],
                    'quality_rate' => $last_setting['quality_rate'],
                );

                $point_setting['id'] = $this->staff_point_setting_model->insertRecord($point_setting);
            }
        }

        $add_points = [];
        if (!empty($point_setting)){
            $add_points = $this->staff_point_add_model->getPointList(['point_setting_id' => $point_setting['id'], 'organ_id'=>$organ_id]);
        }

        $point_setting['add_rate'] = $this->clacPersonRate($staff_id, $setting_year, $setting_month);
        $results['isLoad'] = true;
        $results['point_setting'] = $point_setting;
        $results['point_add_list'] = $add_points;

        echo json_encode($results);
    }


    public function saveStaffPoint(){
        $staff_id = $this->input->post('staff_id');
        $setting_year = $this->input->post('setting_year');
        $setting_month = $this->input->post('setting_month');
        $setting_id = $this->input->post('setting_id');

        $results = [];
        if (empty($staff_id) || empty($setting_year) || empty($setting_month)){
            $results['isSave'] = false;
            echo json_encode($results);
            return;
        }

//        $staff = $this->staff_model->getFromId($staff_id);
//
//        $staff['menu_response'] = empty($this->input->post('menu_response')) ? null : $this->input->post('menu_response');
//        $staff['test_rate'] = empty($this->input->post('test_rate')) ? null : $this->input->post('test_rate');
//        $staff['quality_rate'] = empty($this->input->post('quality_rate')) ? null : $this->input->post('quality_rate');

        if (empty($setting_id)){
            $setting = array(
                'staff_id' => $staff_id,
                'setting_year' => $setting_year,
                'setting_month' => $setting_month,
                'menu_response' => empty($this->input->post('menu_response')) ? null : $this->input->post('menu_response'),
                'add_rate' => empty($this->input->post('add_rate')) ? null : $this->input->post('add_rate'),
                'test_rate' => empty($this->input->post('test_rate')) ? null : $this->input->post('test_rate'),
                'quality_rate' => empty($this->input->post('quality_rate')) ? null : $this->input->post('quality_rate')
            );

            $setting_id = $this->staff_point_setting_model->insertRecord($setting);
        }else{
            $setting = $this->staff_point_setting_model->getFromId($setting_id);
            $setting['menu_response'] = empty($this->input->post('menu_response')) ? null : $this->input->post('menu_response');
            $setting['add_rate'] = empty($this->input->post('add_rate')) ? null : $this->input->post('add_rate');
            $setting['test_rate'] = empty($this->input->post('test_rate')) ? null : $this->input->post('test_rate');
            $setting['quality_rate'] = empty($this->input->post('quality_rate')) ? null : $this->input->post('quality_rate');
            $this->staff_point_setting_model->updateRecord($setting, 'id');
        }

        $results['isSave'] = true;
        //$results['setting_id'] = true;

        echo json_encode($results);
    }


    public function savePointAdd(){
        $point_setting_id = $this->input->post('point_setting_id');
        $comment = $this->input->post('comment');
        $value = $this->input->post('value');
        $organ_id = $this->input->post('organ_id');

        $results = [];
        if (empty($point_setting_id)){
            $results['isSave'] = false;
            echo json_encode($results);
            return;
        }

        $point = array(
            'point_setting_id' => $point_setting_id,
            'comment' => $comment,
            'organ_id' => $organ_id,
            'value' => $value,
            'type' => '0',
            'status' => '2',
        );

        $this->staff_point_add_model->insertRecord($point);

        $results['isSave'] = true;

        echo json_encode($results);
    }
    public function deletePointAdd(){
        $point_add_id = $this->input->post('point_add_id');

        $results = [];
        if (empty($point_add_id)){
            $results['isDelete'] = false;
            echo json_encode($results);
            return;
        }

        $this->staff_point_add_model->delete_force($point_add_id, 'id');

        $results['isDelete'] = true;

        echo json_encode($results);
    }


    public function loadAddPoints(){
        $date_year = $this->input->post('date_year');
        $date_month = $this->input->post('date_month');
        $staff_id = $this->input->post('staff_id');
        $organ_id = $this->input->post('organ_id');
        $load_type = $this->input->post('load_type');

        $cond['staff_id'] = $staff_id;
        $cond['setting_year'] = $date_year;
        $cond['setting_month'] = $date_month;

        $point_setting = $this->staff_point_setting_model->getSettingData($cond);

        if (empty($point_setting)){
            $last_setting = $this->staff_point_setting_model->getLastSetting($staff_id, $date_year.'-'.$date_month);

            if (!empty($last_setting)){
                $point_setting = array(
                    'staff_id' => $staff_id,
                    'setting_year' => $date_year,
                    'setting_month' => $date_month,
                    'menu_response' => $last_setting['menu_response'],
                    'add_rate' => $last_setting['add_rate'],
                    'test_rate' => $last_setting['test_rate'],
                    'quality_rate' => $last_setting['quality_rate'],
                );
                $point_setting['id'] = $this->staff_point_setting_model->insertRecord($point_setting);
            }else{
                $results['isLoad'] = false;
                $results['msg'] = 'empty_setting';

                echo json_encode($results);
                return;
            }
        }

        $cond = array(
            'organ_id'=>$organ_id,
            'point_setting_id'=>$point_setting['id'],
            'type' => $load_type,
        );

        $points = $this->staff_point_add_model->getPointList($cond);

        $points_sum = $this->staff_point_add_model->getPointsSum($cond);

        $results['isLoad'] = true;
        $results['points'] = $points;
        $results['points_sum'] = $points_sum;

        echo json_encode($results);
    }

    public function submitAddPoint(){
        $date_year = $this->input->post('date_year');
        $date_month = $this->input->post('date_month');
        $staff_id = $this->input->post('staff_id');
        $point_type = $this->input->post('point_type');
        $organ_id = $this->input->post('organ_id');
        $time = $this->input->post('time');

        $cond['staff_id'] = $staff_id;
        $cond['setting_year'] = $date_year;
        $cond['setting_month'] = $date_month;

        $point_setting = $this->staff_point_setting_model->getSettingData($cond);

        if (empty($point_setting)){
            $last_setting = $this->staff_point_setting_model->getLastSetting($staff_id, $date_year.'-'.$date_month);

            if (!empty($last_setting)){
                $point_setting = array(
                    'staff_id' => $staff_id,
                    'setting_year' => $date_year,
                    'setting_month' => $date_month,
                    'menu_response' => $last_setting['menu_response'],
                    'add_rate' => $last_setting['add_rate'],
                    'test_rate' => $last_setting['test_rate'],
                    'quality_rate' => $last_setting['quality_rate'],
                );
                $point_setting['id'] = $this->staff_point_setting_model->insertRecord($point_setting);
            }else{
                $point_setting = array(
                    'staff_id' => $staff_id,
                    'setting_year' => $date_year,
                    'setting_month' => $date_month,
                    'menu_response' => 1,
                    'add_rate' => 0,
                    'test_rate' => 0,
                    'quality_rate' => 0,
                );
                $point_setting['id'] = $this->staff_point_setting_model->insertRecord($point_setting);
            }
        }


        $comment = '';
        $point_value = 0;

        $organ = $this->organ_model->getFromId($organ_id);
        if ($point_type=='1'){
            $comment = '飛び込み';
            $point_value = empty($organ['divide_point']) ? 0 : $organ['divide_point'];
        }
        if ($point_type=='2'){
            $comment = '販促 '. $time . '分';
            $point_value = empty($organ['promotional_point']) ? 0 : $organ['promotional_point'] * $time;
        }
        if ($point_type=='3'){
            $comment = '次回予約';
            $point_value = empty($organ['next_reservation_point']) ? 0 : $organ['next_reservation_point'];
        }
        if ($point_type=='4'){
            $comment = '延長';
            $point_value = empty($organ['extension_point']) ? 0 : $organ['extension_point'];
        }
        if ($point_type=='5'){
            $comment = 'オプション';
            $point_value = empty($organ['optional_acquisition_point']) ? 0 : $organ['optional_acquisition_point'];
        }
        if ($point_type=='6'){
            $comment = '指名 '. $time . '分';

            $staff = $this->staff_model->getFromId($staff_id);
            $month_reserves = $this->reserve_model->getMonthReserves($staff_id, $date_year."-".$date_month);
            $all_time = 0;
            foreach ($month_reserves as $item){
                $datetime1 = strtotime($item['reserve_time']);
                $datetime2 = strtotime($item['reserve_exit_time']);

                $all_time += floor(($datetime2 - $datetime1)/60);
            }
            $point = 0;
            if ($all_time<=500){
                $point = 1;
            }elseif ($all_time<=1000){
                $point = 1.2;
            }elseif($all_time<=2000){
                $point = 1.4;
            }elseif ($all_time<=2500){
                $point = 1.6;
            }else{
                $point = 1.8;
            }
            $weight = 1;
            $company = $staff['company_id'];
            if ($company=='2'){
                $weight = 2;
            }
            if ($company=='3'){
                $weight = 3;
            }

            $point = $point * $weight;
            $point_value = $point;
        }

        $point = array(
            'point_setting_id' =>$point_setting['id'],
            'organ_id' => $organ_id,
            'comment' => $comment,
            'type' => $point_type,
            'value' => $point_value * $time,
            'status' => 1,
        );

        $this->staff_point_add_model->insertRecord($point);

        if ($point_type<6){
            $staff = $this->staff_model->getFromId($staff_id);
            $title = ($staff['staff_nick'] == null ?
                    ($staff['staff_first_name'] . ' ' . $staff['staff_last_name'])
                    : $staff['staff_nick']) .  '様からリクエストが入ってきました。';
            $content = $comment . 'の追加ポイントのリクエストが入ってきました。';

            $pushStaffs = $this->staff_organ_model->getStaffsByOrgan($organ_id, 3, false, false);

            foreach ($pushStaffs as $pushStaff){
                if (!empty($pushStaff['staff_id'])){
                    if ($staff_id != $pushStaff['staff_id'])
                        $this->sendNotifications('add_point_request', $title, $content, $staff_id, $pushStaff['staff_id'], '1');
                }
            }
        }

        $results['isSave'] = true;

        echo json_encode($results);
    }

    public function deleteAddPoint(){
        $add_point_id = $this->input->post('point_id');

        $this->staff_point_add_model->delete_force($add_point_id, 'id');

        $results['isDelete'] = true;

        echo json_encode($results);
    }

    public function applyAddPoint(){
        $add_point_id = $this->input->post('point_id');

        $point = $this->staff_point_add_model->getFromId($add_point_id);

        $point['status'] = '2';

        $this->staff_point_add_model->updateRecord($point, 'id');

        $results['isUpdate'] = true;

        echo json_encode($results);
    }

    function uploadPicture() {

        $results = array();

        // user photo
        $image_path = "assets/images/staffs/";
        if(!is_dir($image_path)) {
            mkdir($image_path);
        }
        $image_url  = base_url().$image_path;
        $fileName = $_FILES['picture']['name'];
        $config = array(
            'upload_path'   => $image_path,
            'allowed_types' => '*',
            'overwrite'     => 1,
            'file_name' 	=> $fileName
        );
        $this->load->library('upload', $config);
        $this->upload->initialize($config);

        $results['isUpload'] = false;
        if (!empty($_FILES['picture']['name'])) {
            if ($this->upload->do_upload('picture')) {
                $file_url = $image_url.$this->upload->file_name;
                $results['isUpload'] = true;
                $results['picture'] = $file_url;
            }
        }

        echo json_encode($results);

    }

    public function getStaffsByOrgan(){
        $organ_id = $this->input->post('organ_id');

        if (empty($organ_id)){
            $staffs = [];
        }else{
            $staffs = $this->staff_organ_model->getStaffsByOrgan($organ_id, 3, false);
        }

        $results['staffs'] = $staffs;

        echo json_encode($results);
    }

    public function exchangeStaffSort(){
        $move_staff_id = $this->input->post('move_staff');
        $target_staff_id = $this->input->post('target_staff');

        $move_staff = $this->staff_model->getFromId($move_staff_id);
        $target_staff = $this->staff_model->getFromId($target_staff_id);

        $move_staff_sort = $move_staff['sort_no'];
        $move_staff['sort_no'] = $target_staff['sort_no'];
        $target_staff['sort_no'] = $move_staff_sort;

        $this->staff_model->updateRecord($move_staff, 'staff_id');
        $this->staff_model->updateRecord($target_staff, 'staff_id');

        $results['isUpdate'] = true;

        echo json_encode($results);
    }

    public function updateStaffPush(){
        $staff_id = $this->input->post('staff_id');
        $is_push = $this->input->post('is_push');

        $staff = $this->staff_model->getFromId($staff_id);

        $staff['is_push'] = $is_push;
        $this->staff_model->updateRecord($staff, 'staff_id');

        $results['isUpdate'] = true;
        echo json_encode($results);
    }

    public function getStaffEnableMenus(){
        $staff_id = $this->input->post('staff_id');
        $organ_id = $this->input->post('organ_id');

        $menus = $this->staff_enable_menu_model->getMenuList($staff_id, $organ_id);

        $results['isLoad'] = !empty($menus);
        $results['menus'] = $menus;

        echo json_encode($results);
    }

    public function updateStaffEnableMenu(){
        $staff_id = $this->input->post('staff_id');
        $menu_id = $this->input->post('menu_id');

        $menu = $this->staff_enable_menu_model->getEnableMenu($staff_id, $menu_id);

        if (empty($menu)){
            $this->staff_enable_menu_model->insertRecord([
                'staff_id' => $staff_id,
                'menu_id'=> $menu_id,
            ]);
        }else{
            $this->staff_enable_menu_model->delete_force($menu['staff_enable_menu_id'], 'staff_enable_menu_id');
        }

        $results['isUpdate'] = true;

        echo json_encode($results);
    }


}
?>
