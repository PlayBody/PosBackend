<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/core/AdminController.php';

class Member extends AdminController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct(ROLE_ADMIN);

        $this->load->model('member_model');

        $this->header['page'] = 'member';
        $this->header['title'] = '店舗管理';
    }
    
    /**
     * This function used to load the first screen of the user
     */
    public function index()
    {

        $mode = $this->input->post('mode');

        $this->data['search'] =  $this->input->post('searchText') ? $this->input->post('searchText') : '';

        $this->load->library('pagination');

        $this->data['list_cnt'] = $this->member_model->getList('*', $this->data['search'],true);
        $returns = $this->_paginationCompress( "admin/member/index", $this->data['list_cnt'], 10 ,4);

        $this->data['start_page']  = $returns["segment"]+1;
        $this->data['end_page']  = $returns["segment"]+$returns["page"];
        if($this->data['end_page']>$this->data['list_cnt']) $this->data['end_page'] = $this->data['list_cnt'];
        if(!$this->data['start_page']) $this->data['start_page'] = 1;

        $this->data['list'] = $this->member_model->getList('*',$this->data['search'],false,$returns['page'],$returns['segment']);

         $this->_load_view_admin("admin/member/index");
    }

    public function add()
    {
        $mode = $this->input->post('mode');
        if($mode == 'save'){

            $this->data['member'] = array(
                'title'=>$this->input->post('title'),
                'mail_address'=>$this->input->post('mail_address'),
                'set_time'=>$this->input->post('set_time'),
                'set_amount'=>$this->input->post('set_amount'),
                'password'=>$this->input->post('password'),
            );

            $this->form_validation->set_rules('title','店舗名','trim|required|max_length[128]');
            $this->form_validation->set_rules('mail_address','メールアドレス','trim|required|valid_email|max_length[128]');
            $this->form_validation->set_rules('set_time','セット時間','trim|required|max_length[128]');
            $this->form_validation->set_rules('set_amount','セット料金','trim|required|max_length[128]');
            $this->form_validation->set_rules('password','パスワード','required|max_length[20]');
            $this->form_validation->set_rules('password_confirm','パスワード（確認）','trim|required|matches[password]|max_length[20]');

            if($this->form_validation->run() === TRUE)
            {

                if($this->_check_email($this->data['member']['mail_address'])){
                    $this->data['member']['password'] = sha1($this->data['member']['password']);
                    $this->data['member']['del_flag'] = 0;
                    $this->data['member']['create_date'] = date('Y-m-d H:i:s');
                    $this->data['member']['update_date'] = date('Y-m-d H:i:s');
                    $result = $this->member_model->add($this->data['member']);
                    if($result){
                        $this->session->set_flashdata('success', '正常に登録されました。');
                        $this->session->set_flashdata('error', '');

                        redirect('admin/member');

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

        $this->_load_view_admin("admin/member/add");

    }

    public function edit($_id)
    {
        if($_id == null)
        {
            redirect('admin/member');
        }

        $member = $this->member_model->getFromId($_id);
        if(empty($member))
        {
            redirect('admin/member');
        }
        $mode = $this->input->post('mode');
        if($mode == 'save'){

           $this->data['member'] = array(
                'member_id'=>$_id,
                'title'=>$this->input->post('title'),
                'mail_address'=>$this->input->post('mail_address'),
                'set_time'=>$this->input->post('set_time'),
                'set_amount'=>$this->input->post('set_amount'),
                'password'=>$this->input->post('password'),
            );

            $this->form_validation->set_rules('title','店舗名','trim|required|max_length[128]');
            $this->form_validation->set_rules('mail_address','メールアドレス','trim|required|valid_email|max_length[128]');
            $this->form_validation->set_rules('set_time','セット時間','trim|required|max_length[128]');
            $this->form_validation->set_rules('set_amount','セット料金','trim|required|max_length[128]');
            $this->form_validation->set_rules('password','パスワード','required|max_length[20]');
            $this->form_validation->set_rules('password_confirm','パスワード（確認）','trim|required|matches[password]|max_length[20]');

            if($this->form_validation->run() === TRUE)
            {
                if($this->_check_email($this->data['member']['mail_address'],$_id)){
                    $this->data['member']['update_date'] = date('Y-m-d H:i:s');
                    if(!empty($this->data['member']['password'])){
                        $this->data['member']['password'] = sha1($this->data['member']['password']);
                    }else{
                        unset($this->data['member']['password'] );
                    }

                    $result = $this->member_model->edit($this->data['member'],'member_id');
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
            $this->data['member'] = $member;
        }

        $this->_load_view_admin("admin/member/edit");

    }

    public function delete()
    {
        $userId = $this->input->post('userId');

        $result = $this->member_model->delete($userId,'member_id');

        if ($result > 0) { echo(json_encode(array('status'=>TRUE))); }
        else { echo(json_encode(array('status'=>FALSE))); }
    }

    private function _check_email($email,$member_id=0)
    {
        if(empty($member_id)){
            $result = $this->member_model->checkEmailExists($email);
        } else {
            $result = $this->member_model->checkEmailExists($email, $member_id);
        }

        return empty($result) ? true: false;
    }    
}

?>