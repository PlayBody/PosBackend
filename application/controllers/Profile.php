<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/core/AdminController.php';

class Profile extends AdminController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct(ROLE_COMPANY);
    }
    
    /**
     * This function used to load the first screen of the user
     */
    public function index($active='details')
    {
        $this->data["company"] = $this->company_model->get($this->user['company_id']);
        $this->data["active"] = $active;

//        $this->global['pageTitle'] = $active == "詳細" ? 'パスワード変更' : 'プロフィール編集';


        $this->load->library('form_validation');

        $mode = $this->input->post('mode');
        if($mode=='save'){
            $this->form_validation->set_rules('company_name','企業名','trim|required|max_length[128]');
            $this->form_validation->set_rules('company_email','メールアドレス','trim|required|valid_email|max_length[128]');
            $this->form_validation->set_rules('company_password','パスワード','required|max_length[20]');
            $this->form_validation->set_rules('company_password_confirm','パスワード（確認）','trim|required|matches[company_password]|max_length[20]');

            $this->data['company'] = array(
                'company_id'=>$this->user['company_id'],
                'company_name'=>$this->input->post('company_name'),
                'company_email'=>$this->input->post('company_email'),
                'company_password'=>$this->input->post('company_password'),
            );

            if($this->form_validation->run() === TRUE)
            {
                if(!$this->emailExists($this->data['company']['company_email'])){

                    if($this->data['company']['company_password']){
                        $this->data['company']['company_password'] = sha1($this->data['company']['company_password']);
                    }

                    $result = $this->company_model->update($this->data['company']);
                    if($result){
                        $this->session->set_flashdata('success', '正常に更新されました。');
                        $this->session->set_flashdata('error', '');
                    }else{
                        $this->session->set_flashdata('success', '');
                        $this->session->set_flashdata('error', '更新に失敗しました。');
                    }
                }else{
                    $this->session->set_flashdata('error', '既に登録されているメールアドレスです。');
                }
            }
        }

        $this->_load_view("profile");
    }


    function profileUpdate($active = "details")
    {
        $this->load->library('form_validation');

        $this->form_validation->set_rules('company_name','企業名','trim|required|max_length[128]');
        $this->form_validation->set_rules('company_email','メールアドレス','trim|required|valid_email|max_length[128]');

        if($this->form_validation->run() == FALSE)
        {
            $this->profile($active);
        }
        else
        {
            $name = $this->input->post('company_name');
            $email = $this->input->post('company_email');

            $userInfo = array(
                'company_name'=>$name,
                'company_email'=>$email,
                'company_id'=>$this->user['company_id'],
                );

            $result = $this->company_model->edit($userInfo,'company_id');

            if($result == true)
            {
                $this->session->set_userdata('name', $name);
                $this->session->set_flashdata('success', 'プロフィールの更新に成功しました。');
            }
            else
            {
                $this->session->set_flashdata('error', 'プロフィールの更新に失敗しました。');
            }

            redirect('profile/index/'.$active);
        }
    }
    /**
     * This function is used to change the password of the user
     * @param text $active : This is flag to set the active tab
     */
    function changePassword($active = "changepass")
    {
        $this->load->library('form_validation');

        $this->form_validation->set_rules('oldPassword','現在のパスワード','required|max_length[20]');
        $this->form_validation->set_rules('newPassword','新しい パスワード','required|max_length[20]');
        $this->form_validation->set_rules('cNewPassword','新しいパスワード（確認）','required|matches[newPassword]|max_length[20]');

        if($this->form_validation->run() == FALSE)
        {
            $this->index($active);
        }
        else
        {
            $oldPassword = $this->input->post('oldPassword');
            $newPassword = $this->input->post('newPassword');

            $resultPas = $this->company_model->matchOldPassword($this->user['company_id'], $oldPassword);

            if(empty($resultPas))
            {
                $this->session->set_flashdata('nomatch', '現在のパスワードが正しくありません。');
                redirect('profile/index/'.$active);
            }
            else
            {
                $usersData = array(
                    'company_password'=>sha1($newPassword),
                    'update_date'=>date('Y-m-d H:i:s')
                );

                $result = $this->company_model->changePassword($this->user['company_id'], $usersData);

                if($result > 0) { $this->session->set_flashdata('success', 'パスワードの更新に成功しました。'); }
                else { $this->session->set_flashdata('error', 'パスワードの更新に失敗しました。'); }

                redirect('profile/index/'.$active);
            }
        }
    }


    /**
     * This function is used to check whether email already exist or not
     * @param {string} $email : This is users email
     */
    function emailExists($email)
    {
        $company_id = $this->user['company_id'];
        $return = false;

        if(empty($company_id)){
            $result = $this->company_model->checkEmailExists($email);
        } else {
            $result = $this->company_model->checkEmailExists($email, $company_id);
        }

        if(empty($result)){ $return = true; }
        else {
            $this->form_validation->set_message('emailExists', '{field}はすでに存在します。');
            $return = false;
        }

        return $return;
    }
}

?>