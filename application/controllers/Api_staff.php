<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . 'core/WebController.php';

/*
 *
 */

class Api_staff extends WebController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('pos_staff_model');
    }

    public function login()
    {

        $login_data['mail_address'] = $this->input->post('email');
        $login_data['password'] = $this->input->post('password');

        $staff = $this->pos_staff_model->login($login_data);

        $results = [];
        $results['login_check'] = false;
        $results['info'] = array();
        
        if (!empty($staff)){
            $results['login_check'] = true;
            $results['info'] = $staff;
        }

        echo(json_encode($results));
   }

    public function load_attendance()
    {
        $this->load->model('pos_attendance_model');

        $staff_id = $this->input->post('staff_id');
        $results = [];
        if (empty($staff_id)){
            $results['isLoaded'] = false;
            echo json_encode($results);
            return;
        } 

        $staff = $this->pos_staff_model->getFromId($staff_id);

        if (empty($staff)){
            $results['isLoaded'] = false;
            echo json_encode($results);
            return;
        }

        $status = '2';
        if (!empty($staff['attendance_status'])){
            $status = $staff['attendance_status'];
        }

        $results['isLoaded'] = true;
        $results['status'] = $status;

        echo(json_encode($results));

    }


    public function update_attendance()
    {
        $this->load->model('pos_attendance_model');

        $staff_id = $this->input->post('staff_id');
        $update_status = $this->input->post('status');

        $results = [];
        if (empty($staff_id)){
            $results['isUpdated'] = false;
            echo json_encode($results);
            return;   
        }
        $staff = $this->pos_staff_model->getFromId($staff_id);

        if (empty($staff)){
            $results['isUpdated'] = false;
            echo json_encode($results);
            return;   
        }

        $staff_id = $staff['id'];

        $attendance = array(
            'staff_id' => $staff_id,
            'status' => $update_status,
            'attendance_time' => date('Y-m-d H:i:s'), 
            'del_flag' => 0, 
            'create_date' => date('Y-m-d H:i:s'), 
            'update_date' => date('Y-m-d H:i:s'), 
        );
        $insert = $this->pos_attendance_model->add($attendance);


        $results['isUpdated'] = false;
        if($insert){
            $staff['attendance_status'] = $update_status;
            $update = $this->pos_staff_model->edit($staff,'id');
            
            $results['isUpdated'] = true;
            $results['status'] = $update_status;
        }

        echo(json_encode($results));

    }

    public function get_avatar(){ 

        $staff_id = $this->input->get('staff_id');
        if($staff_id == null)
        {
            $file = 'noImage.jpg';
        }else{
            $staff = $this->pos_staff_model->getFromId($staff_id);

            if (empty($staff) || empty($staff['avatar'])){

                $file = 'noImage.jpg';
            }else{
                $file = $staff['avatar'];
            }

        }

        // header('Content-Type: image/jpeg');
        // header('Content-Length: ' . filesize(base_url().'assets/images/'.$file));
        // readfile(base_url().'assets/images/'.$file);

        // send the right headers
        $file = './assets/images/avatar/'.$file;
        // var_dump($file);die();
        header("Content-Type: image/png");
        header("Content-Length: " . filesize($file));
        // dump the picture and stop the script
        echo file_get_contents($file);
        exit;

   }


    public function load_staff_info()
    {

        $staff_id = $this->input->post('staff_id');
        if (empty($staff_id)){
            $results['isLoaded'] = false;
            echo json_encode($results);
            return;
        } 

        $staff = $this->pos_staff_model->getFromId($staff_id);

        if (empty($staff)){
            $results['isLoaded'] = false;
            echo json_encode($results);
            return;
        }

        $results['isLoaded'] = true;
        $results['staff'] = $staff;

        echo(json_encode($results));

    }


    public function update_staff_info()
    {

        $staff_id = $this->input->post('staff_id');
        $name = $this->input->post('name');
        $mail_address = $this->input->post('mail');
        $pass = $this->input->post('pass');
        $image_stream = $this->input->post('image_stream');
        $admin = $this->input->post('admin');

        if (empty($staff_id)){
            $results['isUpdate'] = false;
            echo json_encode($results);
            return;
        } 

        $staff = $this->pos_staff_model->getFromId($staff_id);

        if (empty($staff)){
            $results['isUpdate'] = false;
            echo json_encode($results);
            return;
        }

        $staff['name'] = $name;
        $staff['mail_address'] = $mail_address;
        if (!empty($pass)){
            $staff['password'] = sha1($pass);
        }

        if (!empty($image_stream)) {
            $data = base64_decode($image_stream);
            $im = imagecreatefromstring($data);
            if ($im !== false) {
                $file_name = 'avatar-'.date('YmdHis').'.jpg';
                $output = './assets/images/avatar/'.$file_name;
                imagejpeg($im, $output);
                // file_put_contents($output, file_get_contents($im));
                $staff['avatar'] = $file_name;
             }
        }

        if (!empty($admin) || $admin == '0') {
            $staff['admin'] = $admin;
        }

        $staff['update_date'] = date('Y-m-d H:i:s');

        $this->pos_staff_model->edit($staff, 'id');

        $results['isUpdate'] = true;

        echo(json_encode($results));

    }

    public function add_staff_info()
    {

        $staff_id = $this->input->post('staff_id');
        $name = $this->input->post('name');
        $mail_address = $this->input->post('mail');
        $admin = $this->input->post('admin');

        if (empty($staff_id)){
            $results['isAdded'] = false;
            echo json_encode($results);
            return;
        } 

        $staff = $this->pos_staff_model->getFromId($staff_id);

        if (empty($staff['member_id'])){
            $results['isAdded'] = false;
            echo json_encode($results);
            return;
        }

        $addStaff['member_id'] = $staff['member_id'];
        $addStaff['name'] = $name;
        $addStaff['mail_address'] = $mail_address;
        
        $this->load->model('pos_setting_model');
        $setting = $this->pos_setting_model->getFromId(1);

        if (empty($setting['format_password'])){
            $password = sha1('12345');
        }else{
            $password = sha1($setting['format_password']);
        }

        $addStaff['password'] = $password;
        $addStaff['attendance_status'] = 2;
        $addStaff['admin'] = $admin;
        $addStaff['del_flag'] = 0;
        $addStaff['create_date'] = date('Y-m-d H:i:s');
        $addStaff['update_date'] = date('Y-m-d H:i:s');

        $this->pos_staff_model->add($addStaff);

        $results['isAdded'] = true;

        echo(json_encode($results));

    }

    public function format_staff_password()
    {

        $staff_id = $this->input->post('staff_id');

        if (empty($staff_id)){
            $results['isUpdate'] = false;
            echo json_encode($results);
            return;
        } 

        $staff = $this->pos_staff_model->getFromId($staff_id);

        if (empty($staff)){
            $results['isUpdate'] = false;
            echo json_encode($results);
            return;
        }

        $this->load->model('pos_setting_model');
        $setting = $this->pos_setting_model->getFromId(1);

        if (empty($setting['format_password'])){
            $password = sha1('12345');
        }else{
            $password = sha1($setting['format_password']);
        }


        $staff['password'] = $password;
        $staff['update_date'] = date('Y-m-d H:i:s');

        $this->pos_staff_model->edit($staff, 'id');

        $results['isUpdate'] = true;

        echo(json_encode($results));

    }


}
?>