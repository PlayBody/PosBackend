<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/core/UpdateController.php';

class Menu extends UpdateController
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

        $this->header['page'] = 'menu';
        $this->header['sub_page'] = 'menu';
        $this->header['title'] = 'メニュー';

        $this->load->model('category_model');
        $this->load->model('organ_model');
        $this->load->model('menu_model');
        $this->load->model('organ_menu_model');
        
        $this->data['categories'] = $this->category_model->getCategoryList([]);
    }

    /**
     * This function used to load the first screen of the user
     */
    public function index()
    {
        $search_word = $this->input->post('search_word', '');

        $this->data['search_word'] = $search_word;
        $company_id = 2;

        $cond['company_id'] = 2;
        $cond['is_user_menu'] = 1;
        $cond['word'] = $search_word;
        $this->data['menus'] = $this->menu_model->getMenuList($cond);
        
        $menu_id = $this->input->get('id', '');

        if (empty($menu_id)){
            $menu = [
                'menu_id' => '',
                'menu_title' => '',
                'menu_price' => '',
                'menu_detail' => '',
                'sort_no' => '',
                'is_user_menu' => 0,
                'menu_time' => '',
                'category_id' => ''
            ];
        }else{
            $menu = $this->menu_model->getMenuInfo($menu_id);
        }

        $this->data['menu'] = $menu;
        
        $this->load_view_with_menu("menu/menu.php");
    }
    
    public function save()
    {
        $menu_id = $this->input->post('menu_id');
		$menu_title  = $this->input->post('menu_title');
		$menu_price  = $this->input->post('menu_price');
		$menu_time  = $this->input->post('menu_time');
		$menu_detail  = $this->input->post('menu_detail');
		$sort_no  = $this->input->post('sort_no');
        $category_id = $this->input->post('category_id');


        $this->load->library('form_validation');
        //$this->form_validation->set_rules('menu_id', 'menu_id', 'required', array('required'=>'メニューを選択してください。')); 
        $this->form_validation->set_rules('menu_title', 'menu_title', 'required', array('required'=>'メニュー名を入力してください。')); 
        $this->form_validation->set_rules('menu_price', 'menu_price', 'required', array('required'=>'価格を入力してください。')); 
       // $this->form_validation->set_rules('menu_detail', 'menu_detail', 'required', array('required'=>'説明を入力してください。')); 
        if ($this->form_validation->run() !== true){
            $this->data['menu'] = [
                'menu_id' => $menu_id,
                'menu_title' => $menu_title,
                'menu_price' => $menu_price,
                'menu_detail' => $menu_detail,
                'sort_no' => '',
                'is_user_menu' => 0,
                'menu_time' => $menu_time,
                'category_id' => $category_id
            ];;
            $this->data['search_word'] = $this->input->post('search_word', '');

            $cond['company_id'] = 2;
            $cond['is_user_menu'] = 1;
            $this->data['menus'] = $this->menu_model->getMenuList($cond);
            
            $this->load_view_with_menu("menu/menu.php");
            return;
        }

		if (empty($menu_id)) {
			$menuData = [
					'company_id' => 2,
					'menu_title' => $menu_title,
					'menu_price' => $menu_price,
					'menu_time' => $menu_time,
					'menu_detail' => $menu_detail,
					'sort_no' => $sort_no,
	                'is_user_menu' => 1,
	                'visible' => 1,
					'category_id' => $category_id,
				];

			$menu_id = $this->menu_model->insertRecord($menuData);

			$organs = $this->organ_model->getDataByParam(['company_id' => 2]);

			foreach($organs as $organ){
				$t_data = [
						'organ_id' => $organ['organ_id'],
						'menu_id' => $menu_id,
					];
				$this->organ_menu_model->insertRecord($t_data);
			}

		}else{
			$menu = $this->menu_model->getFromId($menu_id);
			$menu['category_id'] = $category_id;
			$menu['menu_title'] = $menu_title;
			$menu['menu_price'] = $menu_price;
			$menu['menu_time'] = $menu_time;
			$menu['menu_detail'] = $menu_detail;
			$menu['sort_no'] = $sort_no;

			$this->menu_model->updateRecord($menu, 'menu_id');
		}

        
        redirect('/menu/menu/index?id='.$menu_id);
    }

    public function delete()
    {
        $menu_id = $this->input->post('menu_id');

        $this->load->library('form_validation');
        $this->form_validation->set_rules('menu_id', 'menu_id', 'required', array('required'=>'エラーが発生しました。')); 
        if ($this->form_validation->run() !== true){
            $this->data['menu'] = [
                'menu_id' => '',
                'menu_title' => '',
                'menu_price' => '',
                'menu_detail' => '',
                'sort_no' => '',
                'is_user_menu' => 0,
                'menu_time' => '',
                'category_id' => ''
            ];;
            $this->data['search_word'] = $this->input->post('search_word', '');

            $cond['company_id'] = 2;
            $cond['is_user_menu'] = 1;
            $this->data['menus'] = $this->menu_model->getMenuList($cond);
            
            $this->load_view_with_menu("menu/menu.php");
            return;
        }

        $this->menu_model->delete_force($menu_id, 'menu_id');
        $this->organ_menu_model->delete_force($menu_id, 'menu_id');
        
        redirect('/menu/menu/index');
    }
}
?>
