<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

header('Access-Control-Allow-Origin: *');
Header('Access-Control-Allow-Headers: *'); //for allow any headers, insecure
Header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');

require_once APPPATH . 'core/WebController.php';

/*
 *
 */

class Staff extends WebController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('staff_model');
        $this->load->model('organ_model');
        $this->load->model('staff_organ_model');
    }

    public function login()
    {
        $email = $this->input->post('email');
        $password = $this->input->post('password');
        
        $staff = $this->staff_model->getOneByParam(['staff_mail' => $email]);

        if (empty($staff)){
            $is_load = false;
            $err = 'メールアドリスかありません';
        }else{
            if (sha1($password) == $staff['staff_password'] ){
                $is_load = true;
            }else{
                $is_load = false;
                $err = 'パスウッドか正確ではありません。';
            }
        }

        $results = [];

        $result['is_load'] = $is_load;
        if ($is_load){
            $result['data'] = $staff;
        } else {
            $result['err'] = $err;
        }
        // $results['isLogin'] = false;
        // $results['err_type'] = '2';
        // $results['staff'] = array();

        // $organ = [];
        // if (!empty($staff)){
        //     $results['isLogin'] = true;
        //     $results['staff'] = $staff;

        //     if ($staff['staff_auth']>4)
        //         $company['visible']='1';
        //     else
        //         $company = $this->company_model->getFromId($staff['company_id']);

        //     if (empty($company) || $company['visible']!='1'){
        //         $results['isLogin'] = false;
        //         $results['err_type'] = '1';
        //         $results['staff'] = $staff;
        //     }else{
        //         $organs = $this->staff_organ_model->getOrgansByStaff($staff['staff_id']);
        //         if (!empty($organs)) $organ = $organs[0];
        //     }


        // }
        // $results['organ'] = $organ;

        echo(json_encode($result));
    }
    public function list()
    {
        $organ_id = $this->input->post('organ_id');
        
        $cond = [];
        $cond['organ_id'] = $organ_id;
        
        $staffs = $this->staff_model->getListData($cond);
        
        $result['is_load'] = true;
        $result['data'] = $staffs;
        
        echo json_encode($result);
    }
    
    public function detail(){
        $id = $this->input->post('staff_id');
        if(empty($id)){
            echo json_encode(['is_load' => false]);
            return;
        }
        
        $staff = $this->staff_model->getFromId($id);

        $results['is_load'] = true;
        $results['data'] = $staff;
        echo json_encode($results);
    }

    public function save()
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
                // $isOrganCheck = false;
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

        $epark_id = empty($this->input->post('epark_id')) ? null : $this->input->post('epark_id');

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
            $staff['epark_id'] = $epark_id;
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
            $staff['epark_id'] = $epark_id;

            if (!empty($staff_password)){
                $staff['staff_password'] = sha1($staff_password);
            }
            

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

        $data = $this->staff_model->getFromId($staff_id);

        $results['is_load'] = true;
        $results['data'] = $data;

        echo(json_encode($results));
    }

//---------------- old ---------------
    public function getStaffList()
    {
        $organ_id = $this->input->post('organ_id');

        $cond = [];
        if (!empty($organ_id)) $cond['organ_id'] = $organ_id;

        $staff_list = $this->staff_organ_model->getStaffs($cond);

        $results['is_result'] = true;
        $results['data'] = $staff_list;

        echo json_encode($results);
    }
}
?>
