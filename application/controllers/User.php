<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/core/UpdateController.php';

class User extends UpdateController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct(ROLE_STAFF);
        if ($this->staff['staff_auth'] < 4) {
            redirect('login');
        }

        $this->load->model('company_model');
        $this->load->model('user_model');
        $this->load->library('pagination');
        $this->load->helper('url');

        $this->header['page'] = 'application';
        $this->header['sub_page'] = 'user';
        $this->header['title'] = 'ユーザー管理';

//        $this->load->library('excel');
    }

    /**
     * This function used to load the first screen of the user
     */
    public function index()
    {
        $cond = $this->input->post('cond');
        if (empty($cond)) $cond = $this->session->userdata($cond);

        if ($this->staff['staff_auth'] < 5) {
            $cond['company_id'] = $this->staff['company_id'];
            $companies = $this->company_model->getListByCond($cond);
        } else {
            $companies = $this->company_model->getListByCond([]);
        }

        $company_id = empty($cond['company_id']) ? $companies[0]['company_id'] : $cond['company_id'];
        $company = $this->company_model->getFromId($company_id);

        $start_index = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
        $all_counts = $this->user_model->getUsersByCond($cond, true);
        $limit_per_page = 10;

        $this->_paginationCompress('user/index', $all_counts);

        $this->data['users'] = $this->user_model->getUsersByCond($cond, false, $limit_per_page, $start_index);

        $cond['company_id'] = $company_id;
        $cond['search'] = empty($cond['search']) ? '' : $cond['search'];

        $this->data['cond'] = $cond;
        $this->data['companies'] = $companies;
        $this->data['company'] = $company;


        $this->session->set_userdata($cond);

        $this->load_view_with_menu("user/index");
    }

    public function edit()
    {
        $user_id = $this->input->get('user_id');
        if (empty($user_id)) redirect('user/index');

        $mode = $this->input->post('mode');
        if ($mode == 'delete') {
            $this->user_model->delete_force($user_id, 'user_id');
            redirect('user/index');
        } else {
            $user = $this->user_model->getFromId($user_id);

            $reg_data = $this->input->post('user');

            if (!empty($reg_data)) {
                $user['user_first_name'] = $reg_data['user_first_name'];
                $user['user_last_name'] = $reg_data['user_last_name'];
                $user['user_nick'] = $reg_data['user_nick'];
                $user['user_email'] = $reg_data['user_email'];
                $user['user_tel'] = $reg_data['user_tel'];
                $user['user_sex'] = $reg_data['user_sex'];
                if (!empty($reg_data['user_password'])) {
                    $user['user_password'] = sha1($reg_data['user_password']);
                }

                $this->user_model->updateRecord($user, 'user_id');
            }


            $this->data['user'] = $user;

            $this->load_view_with_menu("user/edit");
        }
    }
}
