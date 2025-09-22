<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/core/UpdateController.php';

class Category extends UpdateController
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
        $this->header['sub_page'] = 'category';
        $this->header['title'] = 'カテゴリー';

        $this->load->model('category_model');
    }

    /**
     * This function used to load the first screen of the user
     */
    public function index()
    {
        $search_word = $this->input->post('search_word', '');

        $this->data['search_word'] = $search_word;

        $id = $this->input->get('id', '');
        if (empty($id)){
            $category = array(
                'id' => '',
                'code' => '',
                'name' => '',
                'alias' => '',
                'description' => '',
                'order_no' => '',
                'color' => '',
            );
        }else{
            $category = $this->category_model->getFromId($id);
        }
        $this->data['category'] = $category;
        $this->data['categories'] = $this->category_model->getCategoryList(['word'=>$search_word], 'order_no');

        $this->load_view_with_menu("menu/category.php");
//        $this->load_view_with_menu("menu/category.php");
    }
    
    public function save()
    {
        $id = $this->input->post('id');
        $code = $this->input->post('code');
        $name = $this->input->post('name');
        $alias = $this->input->post('alias');
        $description = $this->input->post('description');
        $order_no = $this->input->post('order_no');
        $color = $this->input->post('color');


        $this->load->library('form_validation');
        $this->form_validation->set_rules('name', 'name', 'required', array('required'=>'カテゴリ名を入力してください。')); 
        $this->form_validation->set_rules('alias', 'alias', 'required', array('required'=>'略名を入力してください。')); 
        $this->form_validation->set_rules('order_no', 'order_no', 'required', array('required'=>'表示順序を入力してください。')); 
        if ($this->form_validation->run() !== true){
            $this->data['category'] = array(
                'id' => $id,
                'code' => $code,
                'name' => $name,
                'alias' => $alias,
                'description' => $description,
                'order_no' => $order_no,
                'color' => $color
            );
            $this->data['search_word'] = $this->input->post('search_word', '');

            $this->data['categories'] = $this->category_model->getCategoryList(['word'=>$this->data['search_word']], 'order_no');
            
            $this->load_view_with_menu("menu/category.php");
            return;
        }
        if (empty($id)){
            $category = array(
                'code' => $code,
                'name' => $name,
                'alias' => $alias,
                'description' => $description,
                'order_no' => $order_no,
                'color' => $color
            );

            $id = $this->category_model->insertRecord($category);
        }else{
            $category = $this->category_model->getFromId($id);
            $category['code'] = $code;
            $category['name'] = $name;
            $category['alias'] = $alias;
            $category['description'] = $description;
            $category['order_no'] = $order_no;
            $category['color'] = $color;
            $this->category_model->updateRecord($category, 'id');
        }
        
        redirect('/menu/category/index?id='.$id);
    }

    public function delete()
    {
        $id = $this->input->post('id');

        $this->load->library('form_validation');
        $this->form_validation->set_rules('id', 'name', 'required', array('required'=>'エラーが発生しました。')); 
        if ($this->form_validation->run() !== true){
            $this->data['category'] = array(
                'id' => '',
                'code' => '',
                'name' => '',
                'alias' => '',
                'description' => '',
                'order_no' => '',
                'color' => ''
            );
            $this->data['search_word'] = $this->input->post('search_word', '');

            $this->data['categories'] = $this->category_model->getCategoryList(['word'=>$this->data['search_word']], 'order_no');
            
            $this->load_view_with_menu("menu/category.php");
            return;
        }

        $this->category_model->delete_force($id, 'id');
        
        redirect('/menu/category/index');
    }
}
?>
