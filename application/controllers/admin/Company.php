<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/core/AdminController.php';

class Company extends AdminController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct(ROLE_ADMIN);

        $this->load->model('setting_model');

        $this->header['page'] = 'company';
        $this->header['title'] = '企業管理';
    }
    
    /**
     * This function used to load the first screen of the user
     */
    public function index()
    {
        $mode = $this->input->post('mode');
        if($mode=='update'){
            $use_flag = $this->input->post('use_flag');
            $id = $this->input->post('company_id');
            $data = array(
                'use_flag'=>$use_flag,
                'company_id'=>$id,
            );
            $this->company_model->saveSetting($data);
        }
        $this->data['search'] =  $this->input->post('searchText');

        $this->load->library('pagination');

        $this->data['list_cnt'] = $this->company_model->getList('*',$this->data['search'],true);
        $returns = $this->_paginationCompress( "admin/company/index", $this->data['list_cnt'], 10 ,4);

        $this->data['start_page']  = $returns["segment"]+1;
        $this->data['end_page']  = $returns["segment"]+$returns["page"];
        if($this->data['end_page']>$this->data['list_cnt']) $this->data['end_page'] = $this->data['list_cnt'];
        if(!$this->data['start_page']) $this->data['start_page'] = 1;

        $this->data['list'] = $this->company_model->getList('*',$this->data['search'],false,$returns['page'],$returns['segment']);

        $this->_load_view_admin("admin/company/index");
    }

    public function edit($_id)
    {
        if($_id == null)
        {
            redirect('admin/company');
        }

        $company = $this->company_model->getFromId($_id);
        if(empty($company))
        {
            redirect('admin/company');
        }
        $mode = $this->input->post('mode');
        if($mode == 'save'){

            $work_start = $this->input->post('work_start');
            $work_end = $this->input->post('work_end');
            $company_wix_domain = $this->input->post('company_wix_domain');
            $company_wix_key = $this->input->post('company_wix_key');
            $company_wix_secret = $this->input->post('company_wix_secret');
            $company_wix_widget = $this->input->post('company_wix_widget');

            $this->data['company'] = array(
                'company_id'=>$_id,
                'company_name'=>$this->input->post('company_name'),
                'company_email'=>$this->input->post('company_email'),
                'company_password'=>$this->input->post('company_password'),
                'work_start' => $work_start,
                'work_end' => $work_end,
                'company_wix_domain' => $company_wix_domain,
                'company_wix_key' => $company_wix_key,
                'company_wix_secret' => $company_wix_secret,
                'company_wix_widget' => $company_wix_widget,
            );

            $this->form_validation->set_rules('company_name','企業名','trim|required|max_length[128]');
            $this->form_validation->set_rules('company_email','メールアドレス','trim|required|valid_email|max_length[128]');
            $this->form_validation->set_rules('company_password','パスワード','max_length[20]');
            $this->form_validation->set_rules('company_password_confirm','パスワード（確認）','trim|matches[company_password]|max_length[20]');
            $this->form_validation->set_rules('work_start','営業開始時間','trim|required|numeric');
            $this->form_validation->set_rules('work_end','営業終了時間','trim|required|numeric');
            $this->form_validation->set_rules('company_wix_domain','WixAnswerURL','trim|required');
            $this->form_validation->set_rules('company_wix_key','WixAnswerAPIキー','trim|required');
            $this->form_validation->set_rules('company_wix_secret','WixAnswerAPIシークレット','trim|required');
            $this->form_validation->set_rules('company_wix_widget','WixAnswerウィジェット','trim|required');

            if($this->form_validation->run() === TRUE)
            {
                if($this->_check_email($this->data['company']['company_email'],$_id)){
                    $this->data['company']['update_date'] = date('Y-m-d H:i:s');
                    if(!empty($this->data['company']['company_password'])){
                        $this->data['company']['company_password'] = sha1($this->data['company']['company_password']);
                    }else{
                        unset($this->data['company']['company_password'] );
                    }

                    $result = $this->company_model->edit($this->data['company'],'company_id');
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
            }else{
            }

        }else{
            $this->data['company'] = $company;
        }

        $this->data['bot_script'] = html_escape(  '<script src="'.base_url().'chat/js/'. $company['uuid'].'"></script>');

        $this->_load_view_admin("admin/company/edit");

    }
    /**
     * This function is used to load the user list
     */
    function add()
    {
        $mode = $this->input->post('mode');
        if($mode == 'save'){

            $this->data['company'] = array(
                'company_id'=>NULL,
                'company_name'=>$this->input->post('company_name'),
                'company_email'=>$this->input->post('company_email'),
                'company_password'=>$this->input->post('company_password'),
            );

            $this->form_validation->set_rules('company_name','企業名','trim|required|max_length[128]');
            $this->form_validation->set_rules('company_email','メールアドレス','trim|required|valid_email|max_length[128]');
            $this->form_validation->set_rules('company_password','パスワード','required|max_length[20]');
            $this->form_validation->set_rules('company_password_confirm','パスワード（確認）','trim|required|matches[company_password]|max_length[20]');

            if($this->form_validation->run() === TRUE)
            {

                if($this->_check_email($this->data['company']['company_email'])){
                    $this->data['company']['uuid'] = $this->_uuid();
                    $this->data['company']['company_password'] = sha1($this->data['company']['company_password']);
                    $this->data['company']['create_date'] = date('Y-m-d H:i:s');
                    $this->data['company']['update_date'] = date('Y-m-d H:i:s');
                    $result = $this->company_model->add($this->data['company']);
                    if($result){
                        $this->session->set_flashdata('success', '正常に登録されました。');
                        $this->session->set_flashdata('error', '');

                        $company = $this->company_model->getFromUUID($this->data['company']['uuid']);

                        $this->setting_model->registerSetting('anal_date', date('Y-m-d',time()-86400*30), $company['company_id']);
                        $this->setting_model->registerSetting('faq_date', date('Y-m-d',time()-86400*30), $company['company_id']);

                        redirect('admin/company');

                    }else{
                        $this->session->set_flashdata('success', '');
                        $this->session->set_flashdata('error', '更新に失敗しました。');
                    }
                }else{
                    $this->session->set_flashdata('error', '既に登録されているメールアドレスです。');
                }
            }else{
//                var_dump(validation_errors());;
            }

        }

        $this->_load_view_admin("admin/company/add");

    }

    public function _uuid() {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',

            // 32 bits for "time_low"
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),

            // 16 bits for "time_mid"
            mt_rand(0, 0xffff),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand(0, 0x0fff) | 0x4000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand(0, 0x3fff) | 0x8000,

            // 48 bits for "node"
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
    /**
     * This function is used to check whether email already exist or not
     */
    function _check_email($email,$company_id=0)
    {
        if(empty($company_id)){
            $result = $this->company_model->checkEmailExists($email);
        } else {
            $result = $this->company_model->checkEmailExists($email, $company_id);
        }

        return empty($result) ? true: false;
    }

    /**
     * This function is used to delete the user using userId
     * @return boolean $result : TRUE / FALSE
     */
    function delete()
    {
        $userId = $this->input->post('userId');

        $result = $this->company_model->delete($userId,'company_id');

        if ($result > 0) { echo(json_encode(array('status'=>TRUE))); }
        else { echo(json_encode(array('status'=>FALSE))); }
    }


    /**
     * This function used to show login history
     * @param number $userId : This is user id
     */
    function loginHistoy($userId = NULL)
    {
        if($this->isAdmin() == TRUE)
        {
            $this->loadThis();
        }
        else
        {
            $userId = ($userId == NULL ? 0 : $userId);

            $searchText = $this->input->post('searchText');
            $fromDate = $this->input->post('fromDate');
            $toDate = $this->input->post('toDate');

            $data["userInfo"] = $this->user_model->getUserInfoById($userId);

            $data['searchText'] = $searchText;
            $data['fromDate'] = $fromDate;
            $data['toDate'] = $toDate;
            
            $this->load->library('pagination');
            
            $count = $this->user_model->loginHistoryCount($userId, $searchText, $fromDate, $toDate);

            $returns = $this->paginationCompress ( "login-history/".$userId."/", $count, 10, 3);

            $data['userRecords'] = $this->user_model->loginHistory($userId, $searchText, $fromDate, $toDate, $returns["page"], $returns["segment"]);
            
            $this->global['pageTitle'] = 'ユーザー：ログイン履歴';
            
            $this->loadViews("loginHistory", $this->global, $data, NULL);
        }        
    }

    /**
     * This function is used to show users profile
     */
    function profile($active = "details")
    {
        $data["userInfo"] = $this->user_model->getUserInfoWithRole($this->vendorId);
        $data["active"] = $active;
        
        $this->global['pageTitle'] = $active == "詳細" ? 'パスワード変更' : 'プロフィール編集';
        $this->loadViews("profile", $this->global, $data, NULL);
    }

    /**
     * This function is used to update the user details
     * @param text $active : This is flag to set the active tab
     */
    function profileUpdate($active = "details")
    {
        $this->load->library('form_validation');
            
        $this->form_validation->set_rules('fname','Full Name','trim|required|max_length[128]');
        $this->form_validation->set_rules('mobile','Mobile Number','required|min_length[10]');
        $this->form_validation->set_rules('email','Email','trim|required|valid_email|max_length[128]|callback_emailExists');        
        
        if($this->form_validation->run() == FALSE)
        {
            $this->profile($active);
        }
        else
        {
            $name = strtolower($this->security->xss_clean($this->input->post('fname')));
            $mobile = $this->security->xss_clean($this->input->post('mobile'));
            $email = strtolower($this->security->xss_clean($this->input->post('email')));
            
            $userInfo = array('name'=>$name, 'email'=>$email, 'mobile'=>$mobile, 'updatedBy'=>$this->vendorId, 'updatedDtm'=>date('Y-m-d H:i:s'));
            
            $result = $this->user_model->editUser($userInfo, $this->vendorId);
            
            if($result == true)
            {
                $this->session->set_userdata('name', $name);
                $this->session->set_flashdata('success', 'パスワードの更新に成功しました。');
            }
            else
            {
                $this->session->set_flashdata('error', 'パスワードの更新に失敗しました。');
            }

            redirect('profile/'.$active);
        }
    }

    /**
     * This function is used to change the password of the user
     * @param text $active : This is flag to set the active tab
     */
    function changePassword($active = "changepass")
    {
        $this->load->library('form_validation');

        $this->form_validation->set_rules('oldPassword','Old password','required|max_length[20]');
        $this->form_validation->set_rules('newPassword','New password','required|max_length[20]');
        $this->form_validation->set_rules('cNewPassword','Confirm new password','required|matches[newPassword]|max_length[20]');

        if($this->form_validation->run() == FALSE)
        {
            $this->profile($active);
        }
        else
        {
            $oldPassword = $this->input->post('oldPassword');
            $newPassword = $this->input->post('newPassword');

            $resultPas = $this->user_model->matchOldPassword($this->vendorId, $oldPassword);

            if(empty($resultPas))
            {
                $this->session->set_flashdata('nomatch', 'Your old password is not correct');
                redirect('profile/'.$active);
            }
            else
            {
                $usersData = array('password'=>getHashedPassword($newPassword), 'updatedBy'=>$this->vendorId,
                                'updatedDtm'=>date('Y-m-d H:i:s'));

                $result = $this->user_model->changePassword($this->vendorId, $usersData);

                if($result > 0) { $this->session->set_flashdata('success', 'パスワードの更新に成功しました。'); }
                else { $this->session->set_flashdata('error', 'パスワードの更新に失敗しました。'); }

                redirect('profile/'.$active);
            }
        }
    }

    /**
     * This function is used to check whether email already exist or not
     * @param {string} $email : This is users email
     */
    function emailExists($email)
    {
        $userId = $this->vendorId;
        $return = false;

        if(empty($userId)){
            $result = $this->user_model->checkEmailExists($email);
        } else {
            $result = $this->user_model->checkEmailExists($email, $userId);
        }

        if(empty($result)){ $return = true; }
        else {
            $this->form_validation->set_message('emailExists', 'The {field} already taken');
            $return = false;
        }

        return $return;
    }
}

?>