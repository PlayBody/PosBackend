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
        parent::__construct();
        $this->load->model('scenario_model');
        $this->load->model('bot_model');
        $this->load->model('user_model');
        $this->isLoggedIn();
        $this->global['page'] = 'setting';
        $this->global['pageTitle'] = '設定';
    }

    /**
     * This function used to load the first screen of the user
     */
    public function index()
    {
        $this->load->library('form_validation');

        $mode = $this->input->post('mode');
        if($mode=='register'){
            $work_start = $this->input->post('work_start');
            $work_end = $this->input->post('work_end');
            $wix_url = $this->input->post('wix_url');
            $wix_key = $this->input->post('wix_key');
            $wix_secret = $this->input->post('wix_secret');
            $data =  array(
                'userId' => $this->vendorId,
                'work_start' => $work_start,
                'work_end' => $work_end,
                'wix_url' => $wix_url,
                'wix_key' => $wix_key,
                'wix_secret' => $wix_secret,
            );

            $this->form_validation->set_rules('work_start','営業開始時間','trim|required|numeric');
            $this->form_validation->set_rules('work_end','営業終了時間','trim|required|numeric');
            $this->form_validation->set_rules('wix_url','WixAnswer URL','trim|required');
            $this->form_validation->set_rules('wix_key','WixAnswerAPIキー','trim|required');
            $this->form_validation->set_rules('wix_secret','WixAnswerAPIシークレット','trim|required');

            if($this->form_validation->run() !== FALSE)
            {
                $res = $this->user_model->saveSetting($data);
                if($res){
                    $this->session->set_flashdata('success', '正常に更新されました。');
                    $this->session->set_flashdata('error', '');
                }else{
                    $this->session->set_flashdata('success', '');
                    $this->session->set_flashdata('error', '更新に失敗しました。');
                }
            }

        }else{
            $data = $this->user_model->getSetting($this->vendorId);
        }

        $data['bot_script'] = html_escape(  '<script></script>');
        $this->loadViews("setting/index", $this->global, $data , NULL);
    }

}

?>