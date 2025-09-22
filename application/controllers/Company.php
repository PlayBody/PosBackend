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

        $this->load->model('company_model');

        $this->header['page'] = 'application';
        $this->header['sub_page'] = 'company';
        $this->header['title'] = '企業管理';
    }

    /**
     * This function used to load the first screen of the user
     */
    public function index()
    {
        $cond = $this->input->post('cond');
        if(empty($cond)) $cond = $this->session->userdata($cond);

        if ($this->staff['staff_auth']<5){
            $cond['company_id'] = $this->staff['company_id'];
            $companies = $this->company_model->getListByCond($cond);
        }else{
            $companies = $this->company_model->getListByCond([]);
        }

        $company_id = empty($cond['company_id']) ? $companies[0]['company_id'] : $cond['company_id'];
        $company = $this->company_model->getFromId($company_id);

        $mode = $this->input->post('mode');
        if($mode=='save'){
            $reg_data = $this->input->post('company');
            $company['company_name'] = $reg_data['company_name'];
            $company['company_domain'] = $reg_data['company_domain'];
            $company['company_receipt_number'] = $reg_data['company_receipt_number'];
            $company['is_push'] = $reg_data['is_push'];
            $company['is_mail'] = $reg_data['is_mail'];
            $company['license_text'] = $reg_data['license_text'];

            $this->company_model->updateRecord($company, 'company_id');
        }

        $cond['company_id'] = $company_id;

        $this->data['cond'] = $cond;
        $this->data['companies'] = $companies;
        $this->data['company'] = $company;

        $this->session->set_userdata($cond);


        $this->load_view_with_menu("company/index");
    }

}
