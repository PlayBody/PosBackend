<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

header('Access-Control-Allow-Origin: *');
Header('Access-Control-Allow-Headers: *'); //for allow any headers, insecure
Header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');

require_once APPPATH . 'core/WebController.php';

/*
 *
 */

class Menu extends WebController
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
        $is_add_all = $this->input->post('is_add_all');
        $search_word = $this->input->post('search_word');

        $cond = [];
        if (!empty($search_word)) $cond['word'] = $search_word;

        $category_list = $this->category_model->getCategoryList($cond, 'order_no');

        if (!empty($is_add_all)){
            $all_category = [
                'id' => 0,
                'name' => 'すべて',
                'alias' => 'すべて',
                'color' => '#ffffff'
            ];
            array_unshift($category_list, $all_category);
        }

        $results['is_result'] = true;
        $results['data'] = $category_list;

        echo json_encode($results);
    }

    public function getMenuList()
    {
		$company_id = $this->input->post('company_id');
        $organ_id = $this->input->post('organ_id');
        $is_user_menu = $this->input->post('is_user_menu');

        $cond = [];
        if (!empty($company_id)) $cond['company_id'] = $company_id;
        if (!empty($organ_id)) $cond['organ_id'] = $organ_id;
        if (!empty($is_user_menu)) $cond['is_user_menu'] = $is_user_menu;

        $menu_list = $this->menu_model->getMenuList($cond);
		$data = [];
		foreach($menu_list as $item){
			$tmp = $item;
			$tmp['fcolor'] = $this->darken_color($item['color']);
			$data[] = $tmp;
		}

        $results['is_result'] = true;
        $results['data'] = $data;

        echo json_encode($results);

    }

	function darken_color($rgb, $darker=2) {

        $hash = (strpos($rgb, '#') !== false) ? '#' : '';
        $rgb = (strlen($rgb) == 7) ? str_replace('#', '', $rgb) : ((strlen($rgb) == 6) ? $rgb : false);
        if(strlen($rgb) != 6) return $hash.'000000';
        //$darker = ($darker > 1) ? $darker : 1;

        list($R16,$G16,$B16) = str_split($rgb,2);

        $R = sprintf("%02X", floor(hexdec($R16)/$darker));
        $G = sprintf("%02X", floor(hexdec($G16)/$darker));
        $B = sprintf("%02X", floor(hexdec($B16)/$darker));

        return $hash.$R.$G.$B;

	}
}
?>
