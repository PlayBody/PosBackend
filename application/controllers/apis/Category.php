<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

header('Access-Control-Allow-Origin: *');
Header('Access-Control-Allow-Headers: *'); //for allow any headers, insecure
Header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');

require_once APPPATH . 'core/WebController.php';

/*
 *
 */

class Category extends WebController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('category_model');
        $this->load->model('menu_model');
    }

    public function getCategoryList()
    {

		$company_id = $this->input->post('company_id');
        $cond = [];
		
        if (!empty($company_id)) $cond['company_id'] = $company_id;

        $category_list = $this->category_model->getCategoryList($cond, 'order_no');

        $results['is_result'] = true;
        $results['data'] = $category_list;

        echo json_encode($results);
    }
	
	public function getCategory()
	{
		$catId = $this->input->post('category_id');
		if (empty($catId)) {
			echo json_encode(['is_result' => false]);
			exit;
		}
		$category = $this->category_model->getFromId($catId);
		
		if (empty($category)) {
			echo json_encode(['is_result' => false]);
		}else{
			echo json_encode(['is_result' => false, 'data' => $category]);
		}
	}
	
	public function saveCategory()
	{
		$category_id = $this->input->post('category_id');
		$company_id = $this->input->post('company_id');
		$name = $this->input->post('name');
		$code = $this->input->post('code');
		$alias = $this->input->post('alias');
		$description = $this->input->post('description');
		$order_no = $this->input->post('order_no');
		$color = $this->input->post('color');
		
		if (empty($category_id)) {
			$data = [
				'name' => $name,
				'company_id' => $company_id,
				'code' => $code,
				'alias' => $alias,
				'description' => $description,
				'order_no' => $order_no,
				'color' => empty($color) ? '#ffffff' : $color
			];
			$this->category_model->insertRecord($data);
		}else{
			$category = $this->category_model->getFromId($category_id);
			$category['name'] = $name;
			$category['code'] = $code;
			$category['alias'] = $alias;
			$category['description'] = $description;
			$category['order_no'] = $order_no;
			
			$this->category_model->updateRecord($category, 'id');
		}
		
		echo json_encode(['is_result' => true]);
		
	}
	
	public function deleteCategory()
	{
		$category_id = $this->input->post('category_id');
		
		if (empty($category_id)) {
			echo json_encode(['is_result' => false]);
			exit;
		}
		
        $this->category_model->delete_force($category_id, 'id');
		
		$menus = $this->menu_model->getDataByParam(['category_id' => $category_id]);
		foreach($menus as $menu){
			$menu['category_id'] = 0;
			$this->menu_model->updateRecord($menu, 'menu_id');
		}
		
		echo json_encode(['is_result' => true]);
		
	}

}
?>
