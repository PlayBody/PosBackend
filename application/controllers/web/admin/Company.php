<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/core/UpdateController.php';

class Company extends UpdateController
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

        $this->header['page'] = 'admin';
        $this->header['sub_page'] = 'company';
        $this->header['title'] = '企業管理';
        
        $this->load->model('company_model');
    }

    /**
     * This function used to load the first screen of the user
     */
    public function index()
    {
        $companies = $this->company_model->getDataByParam([]);

        $this->data['companies'] = $companies;
        $this->load_view_with_menu("admin/company.php");
    }

    public function edit()
    {
        $company_id = $this->input->get('id');

        $company = [];
        if (!empty($company_id)){
            $company = $this->company_model->getFromId($company_id);
        }

        $this->data['company'] = $company;

        $this->load_view_with_menu("admin/company_edit.php");
    }

    public function epark()
    {
        $company_id = $this->input->get('id');
        if (empty($company_id)) {
            redirect('/admin/company');
        }

        $company = $this->company_model->getFromId($company_id);

        $this->data['company'] = $company;
        
        $this->load_view_with_menu("admin/company_epark.php");
    }

    public function epark_update()
    {
        $company_id = $this->input->post('company_id');
        $is_sync_epark = $this->input->post('is_sync_epark');
        $epark_base_url = $this->input->post('epark_base_url');
        $epark_login_id = $this->input->post('epark_login_id');
        $epark_login_pwd = $this->input->post('epark_login_pwd');

        if (empty($company_id)) {
            redirect('/admin/company');
        }

        $company = $this->company_model->getFromId($company_id);
        $company['is_sync_epark'] = $is_sync_epark;
        if ($is_sync_epark==1){
            $company['epark_base_url'] = $epark_base_url;
            $company['epark_login_id'] = $epark_login_id;
            $company['epark_login_pwd'] = $epark_login_pwd;
        }
        
        $this->company_model->updateRecord($company, 'company_id');
        
        $this->session->set_flashdata('success', '保存しました。');

        redirect('/admin/company_epark?id='.$company_id);
        
    }
}
