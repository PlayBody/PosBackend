<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

defined('BASEPATH') or exit('No direct script access allowed');
require_once APPPATH . 'core/AdminController.php';

class Dashboard extends AdminController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct(ROLE_ADMIN);

        $this->header['page'] = 'dashboard';
        $this->header['title'] = '管理画面|ダッシュボード';
    }

    /**
     * This function used to load the first screen of the user
     */
    public function index()
    {
        $this->load->model('scenario_model');
        $this->load->model('bot_model');
        $this->load->model('company_model');

        $this->data['all_scenario_count'] =$this->scenario_model->getList(0,'',true);
        $this->data['company_count'] =$this->company_model->getListCount(array('del_flag'=>0));

        $analysisData = $this->bot_model->getAnalysisData();
        if(!empty($analysisData)){
            //セッション数、
            $this->data['visit_count'] =$analysisData['visit'];
            //シナリオ選択数
            $this->data['scenario_count'] =$analysisData['scenario'];
            //FAQ数
            $this->data['faq_count'] =$analysisData['faq'];
            //チャット数
            $this->data['chat_count'] =$analysisData['chat'];
        }

        $this->_load_view_admin("/admin/dashboard");
    }

}
