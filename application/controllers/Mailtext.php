<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/core/UpdateController.php';

class Mailtext extends UpdateController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct(ROLE_STAFF);
        if( $this->staff['staff_auth']<4){
            redirect('login');
        }

        $this->load->model('company_model');
        $this->load->model('notification_text_model');

        $this->header['page'] = 'application';
        $this->header['sub_page'] = 'mail_text';
        $this->header['title'] = 'メール本文管理';

//        $this->load->library('excel');
    }

    /**
     * This function used to load the first screen of the user
     */
    public function index()
    {
        $notice_types = array(
            '11'=>'【スタッフ用】スケジュール枠決定時',
            '12'=>'【スタッフ用】出勤要請配信時',
            '13'=>'【スタッフ用】予約時',
            '14'=>'【スタッフ用】プロフィール変更時',
            '15'=>'【スタッフ用】店長が自分のシフトを変更した場合 ',
            '16'=>'【スタッフ用】お客様からメッセージが届いたとき',
            '17'=>'【スタッフ用】シフト承認通知',
            '18'=>'【スタッフ用】シフト拒否通知',
            '19'=>'【スタッフ用】シフト追加要求通知',
            '21'=>'【お客様用】予約受付（確定）',
            '22'=>'【お客様用】リクエスト受付',
            '23'=>'【お客様用】リクエスト承認時',
            '24'=>'【お客様用】リクエスト非承認時',
            '25'=>'【お客様用】予約の前日の１２時配信',
        );

        $cond = $this->input->post('cond');
        if(empty($cond)) $cond = $this->session->userdata($cond);

        if ($this->staff['staff_auth']<5){
            $cond['company_id'] = $this->staff['company_id'];
            $companies = $this->company_model->getListByCond($cond);
        }else{
            $companies = $this->company_model->getListByCond([]);
        }

        $company_id = empty($cond['company_id']) ? $companies[0]['company_id'] : $cond['company_id'];

        $cond['company_id'] = $company_id;
        $cond['mail_type'] = empty($cond['mail_type']) ? '11' : $cond['mail_type'];

        $result = $this->notification_text_model->getRecordByCond($cond);

        $mode = $this->input->post('mode');
        if($mode=='save'){
            $reg_data = $this->input->post('data');
            if (empty($result)){
                $result = array(
                    'company_id' => $company_id,
                    'mail_type' => $cond['mail_type'],
                    'title' => $reg_data['title'],
                    'content' => $reg_data['content']
                );
                $this->notification_text_model->insertRecord($result);
            }else{
                $result['title'] = $reg_data['title'];
                $result['content'] = $reg_data['content'];
                $this->notification_text_model->updateRecord($result, 'id');
            }
        }

        $this->data['cond'] = $cond;
        $this->data['companies'] = $companies;
        $this->data['result'] = $result;

        $this->data['notices'] = $notice_types;

        $this->session->set_userdata($cond);


        $this->load_view_with_menu("mailtext/index");
    }

}
