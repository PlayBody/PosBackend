<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

defined('BASEPATH') or exit('No direct script access allowed');
require_once APPPATH . 'core/UpdateController.php';

class Login extends UpdateController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Index Page for this controller.
     */
    public function index()
    {
        $this->data = array(
            'login_id'=>$this->input->post('email'),
            'password'=>$this->input->post('password'),
        );
        $this->form_validation->set_rules('email', 'メールアドレス', 'required|valid_email|max_length[255]|trim');
        $this->form_validation->set_rules('password', 'パスワード', 'required|max_length[255]');

        if($this->form_validation->run() === TRUE)
        {
            $staff = $this->staff_model->login($this->data);
            if(!empty($staff)){
                $this->session->set_userdata('staff', $staff);

                $loginInfo = array(
                    "staff_id"=>$staff['staff_id'],
                    "sessionData" => json_encode($staff)
                );

                redirect('/epark/scheduler');
            }else{
                $this->session->set_flashdata('error', 'メールアドレスまたはパスワードが正しくありません。');
            }
        }else{
            $this->session->set_flashdata('error', 'メールアドレスまたはパスワードが正しくありません。');
        }
        $this->load->view('/login');
    }

    public function forgotPassword()
    {
        $company = $this->session->userdata('company');

        if(empty($company))
        {
            $this->load->view('forgotPassword');
        }
        else
        {
            redirect('/epark/scheduler');
        }
    }
    /**
     * This function used to generate reset password request link
     */
    function resetPasswordUser()
    {
        $status = '';

        $this->load->library('form_validation');

        $this->form_validation->set_rules('login_email','メールアドレス','trim|required|valid_email');

        if($this->form_validation->run() == FALSE)
        {
            $this->forgotPassword();
        }
        else
        {
            $email = strtolower($this->security->xss_clean($this->input->post('login_email')));

            if($this->company_model->checkEmailExists($email))
            {
                $encoded_email = urlencode($email);

                $this->load->helper('string');
                $data['email'] = $email;
                $data['activation_id'] = random_string('alnum',15);
                $data['createdDtm'] = date('Y-m-d H:i:s');
                $data['agent'] = getBrowserAgent();
                $data['client_ip'] = $this->input->ip_address();

                $save = $this->company_model->resetPasswordUser($data);

                if($save)
                {
                    $data1['reset_link'] = base_url() . "resetPasswordConfirmUser/" . $data['activation_id'] . "/" . $encoded_email;
                    $userInfo = $this->company_model->getCustomerInfoByEmail($email);

                    if(!empty($userInfo)){
                        $data1["name"] = $userInfo->name;
                        $data1["email"] = $userInfo->email;
                        $data1["message"] = "パスワードをリセット";
                    }

                    $sendStatus = resetPasswordEmail($data1);

                    if($sendStatus){
                        $status = "send";
                        setFlashData($status, "パスワードリセット用リンクのメールを送信しました。");
                    } else {
                        $status = "notsend";
                        setFlashData($status, "メールの送信に失敗しました。");
                    }
                }
                else
                {
                    $status = 'unable';
                    setFlashData($status, "送信中にエラーが発生しました。もう一度お試しください。");
                }
            }
            else
            {
                $status = 'invalid';
                setFlashData($status, "このメールは登録されていません。");
            }
            //登録されていない、または仮登録のメールアドレスです
            //仮登録の場合は別途登録メールにお送りしている「本登録のお願い」より、本登録の完了をお願いいたします
            redirect('/forgotPassword');
        }
    }

    /**
     * This function used to reset the password
     * @param string $activation_id : This is unique id
     * @param string $email : This is user email
     */
    function resetPasswordConfirmUser($activation_id, $email)
    {
        // Get email and activation code from URL values at index 3-4
        $email = urldecode($email);

        // Check activation id in database
        $is_correct = $this->login_model->checkActivationDetails($email, $activation_id);

        $data['email'] = $email;
        $data['activation_code'] = $activation_id;

        if ($is_correct == 1)
        {
            $this->load->view('newPassword', $data);
        }
        else
        {
            redirect('/login');
        }
    }

    /**
     * This function used to create new password for user
     */
    function createPasswordUser()
    {
        $status = '';
        $message = '';
        $email = strtolower($this->input->post("email"));
        $activation_id = $this->input->post("activation_code");

        $this->load->library('form_validation');

        $this->form_validation->set_rules('password','パスワード','required|max_length[20]');
        $this->form_validation->set_rules('cpassword','パスワード（確認）','trim|required|matches[password]|max_length[20]');

        if($this->form_validation->run() == FALSE)
        {
            $this->resetPasswordConfirmUser($activation_id, urlencode($email));
        }
        else
        {
            $password = $this->input->post('password');
            $cpassword = $this->input->post('cpassword');

            // Check activation id in database
            $is_correct = $this->login_model->checkActivationDetails($email, $activation_id);

            if($is_correct == 1)
            {
                $this->login_model->createPasswordUser($email, $password);

                $status = 'success';
                $message = 'パスワードが正常にリセットされました';
            }
            else
            {
                $status = 'error';
                $message = 'パスワードのリセットに失敗しました';
            }

            setFlashData($status, $message);

            redirect("/login");
        }
    }

}
