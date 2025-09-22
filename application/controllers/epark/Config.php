<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/core/UpdateController.php';

class Config extends UpdateController
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

        $this->header['page'] = 'epark';
        $this->header['title'] = 'Epark同期設定'; //Reservation reception

        $this->load->model('epark_sync_config_model');
        $this->load->model('epark_sync_table_config_model');

    }

    /**
     * This function used to load the first screen of the user
     */
    public function index()
    {

        $config = $this->epark_sync_config_model->getOneByParam(['company_id'=>2]);
        $tables = $this->epark_sync_table_config_model->getDataByParam(['company_id'=>2]);

        if (empty($config)){
        }

        $this->data['config'] = $config;
        $this->data['tables'] = $tables;


        $this->load_view_with_menu("epark/config");
    }

    public function update(){
        $id = $this->input->post('config_id');
        $base_url = $this->input->post('base_url');
        $auth_id = $this->input->post('auth_id');
        $auth_pass = $this->input->post('auth_pass');
        $epark_company_id = $this->input->post('epark_company_id');
        $duration = $this->input->post('duration');

        if (empty($id)){
            $data['company_id'] = 2;
            $data['base_url'] = $base_url;
            $data['auth_id'] = $auth_id;
            $data['auth_pass'] = $auth_pass;
            $data['epark_company_id'] = $epark_company_id;
            $data['duration'] = $duration;
            $this->epark_sync_config_model->insertRecord($data);
        }else{
            $data = $this->epark_sync_config_model->get($id);
            $data['base_url'] = $base_url;
            $data['auth_id'] = $auth_id;
            $data['auth_pass'] = $auth_pass;
            $data['epark_company_id'] = $epark_company_id;
            $data['duration'] = $duration;
            $this->epark_sync_config_model->updateRecord($data);
        }


        redirect('/epark/config');
    }

    public function updateTable(){
        $tables = $this->input->post('table');

        foreach ($tables as $table){
            $id = $table['id'];
            if (empty($id)) continue;
            $data = $this->epark_sync_table_config_model->get($id);
            $data['is_real_update'] = $table['is_real_update'];
            $data['from_type'] = $table['from_type'];
            $data['from_date'] = $table['from_date'];

            $this->epark_sync_table_config_model->updateRecord($data);
        }


        redirect('/epark/config');
    }

}
