<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/core/AdminController.php';
/**
 * Class : Setting
 */
class Setting extends AdminController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct(ROLE_COMPANY);
        $this->load->model('scenario_model');
        $this->load->model('bot_model');

        $this->data['page'] = 'setting';
        $this->header['title'] = '企業管理画面｜設定';
    }

    /**
     * This function used to load the first screen of the user
     */
    public function index()
    {
        $mode = $this->input->post('mode');
        if($mode=='register'){
            $work_start = $this->input->post('work_start');
            $work_end = $this->input->post('work_end');
            $company_wix_domain = $this->input->post('company_wix_domain');
            $company_wix_key = $this->input->post('company_wix_key');
            $company_wix_secret = $this->input->post('company_wix_secret');
            $company_wix_widget = $this->input->post('company_wix_widget');
            $this->data['setting'] =  array(
                'company_id' => $this->user['company_id'],
                'work_start' => $work_start,
                'work_end' => $work_end,
                'company_wix_domain' => $company_wix_domain,
                'company_wix_key' => $company_wix_key,
                'company_wix_secret' => $company_wix_secret,
                'company_wix_widget' => $company_wix_widget,
            );

            $this->form_validation->set_rules('work_start','営業開始時間','trim|required|numeric');
            $this->form_validation->set_rules('work_end','営業終了時間','trim|required|numeric');
            $this->form_validation->set_rules('company_wix_domain','WixAnswerURL','trim|required');
            $this->form_validation->set_rules('company_wix_key','WixAnswerAPIキー','trim|required');
            $this->form_validation->set_rules('company_wix_secret','WixAnswerAPIシークレット','trim|required');
            $this->form_validation->set_rules('company_wix_widget','WixAnswerウィジェット','trim|required');

            if($this->form_validation->run() !== FALSE)
            {
                $res = $this->company_model->saveSetting($this->data['setting']);
                if($res){
                    $this->session->set_flashdata('success', '正常に更新されました。');
                    $this->session->set_flashdata('error', '');
                }else{
                    $this->session->set_flashdata('success', '');
                    $this->session->set_flashdata('error', '更新に失敗しました。');
                }
            }

        }else{
            $this->data['setting'] = $this->company_model->getSetting($this->user['company_id']);
        }
        $this->data['bot_script'] = html_escape(  '<script src="'.base_url().'chat/js/'. $this->user['uuid'].'"></script>');
        $this->_load_view("setting/index");
    }

}