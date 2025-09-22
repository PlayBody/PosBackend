<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

header('Access-Control-Allow-Origin: *');
Header('Access-Control-Allow-Headers: *'); //for allow any headers, insecure
Header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');

require_once APPPATH . 'core/WebController.php';

/*
 *
 */

class User extends WebController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('user_model');
    }

    public function loginUser()
    {
        $company_id = $this->input->post('company_id');
        $email = $this->input->post('email');
        $password = $this->input->post('password');

        $user = $this->user_model->getUserRow(['company_id'=> $company_id, 'user_email'=> $email]);
        $results = [];
        if (empty($user)){
            $results['is_result'] = false;
            $results['err_message'] = "有効なメールアドレスではありません。";
            echo json_encode($results);
            exit;
        }
        if (sha1($password) != $user['user_password']){
            $results['is_result'] = false;
            $results['err_message'] = "パスワードが正しくありません。";
            echo json_encode($results);
            exit;
        }
        if ($user['visible'] != 1){
            $results['is_result'] = false;
            $results['err_message'] = "ブロックされたユーザーです。";
            echo json_encode($results);
            exit;
        }

        $results['is_result'] = true;
        $results['user_id'] = $user['user_id'];

        echo json_encode($results);
    }

    public function registerUser(){
        $company_id = $this->input->post('company_id');
        $first_name = $this->input->post('first_name');
        $last_name = $this->input->post('last_name');
        $nick = $this->input->post('nick');
        $email = $this->input->post('email');
        $tel= $this->input->post('tel');
        $sex = $this->input->post('sex');
        $birthday = $this->input->post('birthday');
        $password = $this->input->post('password');
        $comment = $this->input->post('comment');

        $old_user = $this->user_model->getUserRow(['company_id'=> $company_id, 'user_email'=> $email]);

        if (!empty($old_user)){
            $result['is_result'] = false;
            $result['err_message'] = 'すでに使用されているメールアドレスです。';
        }else{
            $user_no = $this->generateUserCode();
            $user = [
                'company_id' => $company_id,
                'user_no' => $user_no,
                'user_qrcode' => $this->generateUserQRCode($user_no, $company_id),
                'user_grade' => 1,
                'user_first_name' => $first_name,
                'user_last_name' => $last_name,
                'user_nick' => $nick,
                'user_email' => $email,
                'user_tel' => $tel,
                'user_sex' => $sex,
                'user_birthday' => $birthday,
                'user_password' =>sha1($password),
                'user_comment' =>$comment,
                'visible' => 1,
            ];

            $user_id = $this->user_model->insertRecord($user);

            $result['is_result'] = true;
            $result['user_id'] = $user_id;
        }

        echo json_encode($result);
    }

    private function generateUserCode(){
        $user_code = 0;
        while($user_code==0){
            $tmpUserCode = rand(10000000000, 99999999999);
            $exit_code_user = $this->user_model->getRecordByCond(['user_no'=>$tmpUserCode]);
            if (empty($exit_code_user)){
                $user_code = $tmpUserCode;
            }
        }

        return $user_code;
    }

    public function generateUserQRCode($user_no, $company_id){
        $domain = '';
        if ($company_id==1) $domain = 'conceptbar.info';
        if ($company_id==2) $domain = 'riraku-kan.jp';
        if ($company_id==3) $domain = 'koritori.jp';
        if ($company_id==4) $domain = 'libero-school.com';

        $company_code = substr('000'.$company_id, strlen('000'.$company_id)-3);

        $sum_check = 0;
        foreach (str_split($user_no) as $each ){
            $sum_check = $sum_check + $each;
        }

        $qr_code = 'connect!'.$user_no.'!'.$domain.'!'.$company_code.'!'.$sum_check;

        return $qr_code;
    }

}
?>
