<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/core/UpdateController.php';

class Homemenu extends UpdateController
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

        $this->load->model('connect_home_menu_model');

        $this->header['page'] = 'application';
        $this->header['sub_page'] = 'home_menu';
        $this->header['title'] = 'お店アプリメニュー';

//        $this->load->library('excel');

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


        $mode = $this->input->post('mode');
        if($mode=='save'){
            $menu_id = $this->input->post('menu_id');
            $is_use = $this->input->post('is_use');

            $home_menu = $this->connect_home_menu_model->getFromId($menu_id);
            $home_menu['is_use'] = $is_use;

            $this->connect_home_menu_model->updateRecord($home_menu, 'id');
        }
        if($mode=='up'){
            $menu_id = $this->input->post('menu_id');
            $home_menu = $this->connect_home_menu_model->getFromId($menu_id);

            $prev_menu = $this->connect_home_menu_model->getHomePrevMenu($company_id, $home_menu['sort']);
            if (!empty($prev_menu)){
                $prev_sort = $prev_menu['sort'];
                $prev_menu['sort'] = $home_menu['sort'];
                $this->connect_home_menu_model->updateRecord($prev_menu, 'id');

                $home_menu['sort'] = $prev_sort;
                $this->connect_home_menu_model->updateRecord($home_menu, 'id');
            }
        }

        if($mode=='down'){
            $menu_id = $this->input->post('menu_id');
            $home_menu = $this->connect_home_menu_model->getFromId($menu_id);

            $next_menu = $this->connect_home_menu_model->getHomeNextMenu($company_id, $home_menu['sort']);
            if (!empty($next_menu)){
                $next_sort = $next_menu['sort'];
                $next_menu['sort'] = $home_menu['sort'];
                $this->connect_home_menu_model->updateRecord($next_menu, 'id');

                $home_menu['sort'] = $next_sort;
                $this->connect_home_menu_model->updateRecord($home_menu, 'id');
            }
        }


        $menus = $this->connect_home_menu_model->getHomeMenuList($company_id, true);

        $cond['company_id'] = $company_id;

        $this->data['cond'] = $cond;
        $this->data['menus'] = $menus;
        $this->data['companies'] = $companies;

        $this->session->set_userdata($cond);


        $this->load_view_with_menu("homemenu/index");
    }

}
